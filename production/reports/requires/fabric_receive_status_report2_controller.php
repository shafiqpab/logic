<?
header('Content-type:text/html; charset=utf-8');
session_start();
//ini_set('memory_limit','3072M');
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
require_once('../../../includes/class3/class.conditions.php');
require_once('../../../includes/class3/class.reports.php');
require_once('../../../includes/class3/class.yarns.php');
require_once('../../../includes/class3/class.fabrics.php');

$user_name=$_SESSION['logic_erp']['user_id'];
$user_id=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
// finish fab order to order transfer problem

//--------------------------------------------------------------------------------------------------------------------
if($action=="print_button_variable_setting")
{
    $print_report_format_arr = return_library_array("select format_id,format_id from lib_report_template where template_name =$data and module_id=7 and report_id=114 and is_deleted=0 and status_active=1","format_id","format_id");
    echo "print_report_button_setting('".implode(',',$print_report_format_arr)."');\n";
    exit();
}
if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );
	exit();
}

if ($action=="load_variable_settings")
{
	echo "$('#cbo_date_type').val(0);\n";
	$sql_result = sql_select("select report_date_catagory from variable_order_tracking where company_name='$data' and variable_list in (42) order by id");
 	foreach($sql_result as $result)
	{
		echo "$('#cbo_date_type').val(".$result[csf("report_date_catagory")].");\n";
		echo "search_by(".$result[csf("report_date_catagory")].",1);\n";
	}
 	exit();
}

$color_array=return_library_array( "select id, color_name from lib_color", "id", "color_name");
$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
$supplier_details=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name");
$buyer_short_name_library=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
$yarn_count_details=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count");

if($db_type==0)
{
	$fabric_desc_details=return_library_array( "select job_no, group_concat(distinct(fabric_description)) as fabric_description from wo_pre_cost_fabric_cost_dtls group by job_no", "job_no", "fabric_description");
}
else
{
	$fabric_desc_details=return_library_array( "select job_no, LISTAGG(cast(fabric_description as varchar2(4000)), ',') WITHIN GROUP (ORDER BY id) as fabric_description from wo_pre_cost_fabric_cost_dtls group by job_no", "job_no", "fabric_description");
}

$product_details=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
$batch_details=return_library_array( "select id, batch_no from pro_batch_create_mst", "id", "batch_no");

$costing_per_id_library=array(); $costing_date_library=array();
$costing_sql=sql_select("select job_no, costing_per, costing_date from wo_pre_cost_mst");
foreach($costing_sql as $row)
{
	$costing_per_id_library[$row[csf('job_no')]]=$row[csf('costing_per')];
	$costing_date_library[$row[csf('job_no')]]=$row[csf('costing_date')];
}

$receive_basis=array(0=>"Independent",1=>"Fabric Booking",2=>"Knitting Plan");

$report_format_arr=array(1=>"show_fabric_booking_report_gr",2=>"show_fabric_booking_report",3=>"show_fabric_booking_report3",4=>"show_fabric_booking_report1",5=>"show_fabric_booking_report2",6=>"show_fabric_booking_report4",7=>"show_fabric_booking_report5",8=>"show_fabric_booking_report",9=>"show_fabric_booking_report3",10=>"show_fabric_booking_report4",28=>"show_fabric_booking_report_akh",45=>"show_fabric_booking_report_urmi",53=>"show_fabric_booking_report_jk",73=>"show_fabric_booking_report_mf",93=>"show_fabric_booking_report_libas",719=>"show_fabric_booking_report16");//,78=>"Print",84=>"Print2",85=>"Print3"

$report_format_mainv2_arr=array(1=>"show_fabric_booking_report_gr",2=>"show_fabric_booking_report",849=>"show_fabric_booking_report_bl1",3=>"show_fabric_booking_report3",892=>"show_fabric_booking_report3_v1",4=>"show_fabric_booking_report1",5=>"show_fabric_booking_report2",6=>"show_fabric_booking_report4",7=>"show_fabric_booking_report5",13=>"show_fabric_booking_report13",28=>"show_fabric_booking_report_akh",45=>"show_fabric_booking_report_urmi",53=>"show_fabric_booking_report_jk",432=>"show_fabric_booking_report_fn",73=>"show_fabric_booking_report_mf",93=>"show_fabric_booking_report_libas",129=>"show_fabric_booking_report_print5",193=>"show_fabric_booking_report_print4",269=>"show_fabric_booking_report_knit",280=>"show_fabric_booking_report_print14",39=>"show_fabric_booking_report_print39",304=>"show_fabric_booking_report10",719=>"show_fabric_booking_report16",723=>"show_fabric_booking_report17",833=>"show_fabric_booking_report17_v1",339=>"show_fabric_booking_report18",370=>"show_fabric_booking_report_print19",383=>"show_fabric_booking_report_print20",404=>"show_fabric_booking_report21",419=>"show_fabric_booking_report22",426=>"show_fabric_booking_report_print23",452=>"show_fabric_booking_report_print24",786=>"show_fabric_booking_report25",502=>"show_fabric_booking_report26",437=>"show_fabric_booking_report27",865=>"show_fabric_booking_report28");

$report_format_short_arr = array(8=>"show_fabric_booking_report",9=>"show_fabric_booking_report3",10=>"show_fabric_booking_report4",46=>"show_fabric_booking_report_urmi",244=>"show_fabric_booking_report_ntg",45=>"print_booking_4",53=>"print_booking_5",72=>"print_booking_6",191=>"print_booking_7",220=>"print_booking_8");

$report_format_partial_arr=array(143=>"show_fabric_booking_report_urmi",84=>"show_fabric_booking_report_urmi_per_job",85=>"print_booking_3",151=>"show_fabric_booking_report_advance_attire_ltd",160=>"print_booking_5",175=>"print_booking_6",218=>"print_booking_7",220=>"print_booking_northern_new",235=>"print_booking_northern_9",274=>"print_booking_10",241=>"print_booking_11",269=>"print_booking_12",28=>"print_booking_13",280=>"print_booking_14",304=>"print_booking_15",719=>"print_booking_16",723=>"print_booking_17",339=>"print_booking_18",370=>"print_booking_19",768=>"print_booking_20");

$tmplte=explode("**",$data);

if ($tmplte[0]=="viewtemplate") $template=$tmplte[1]; else $template=$lib_report_template_array[$_SESSION['menu_id']]['0'];
if ($template=="") $template=1;


if($action=="booking_no_popup")
{
	echo load_html_head_contents("Order No Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		$(function(){
			load_drop_down( 'fabric_receive_status_report_controller',<? echo $companyID;?>, 'load_drop_down_buyer', 'buyer_td' );
		});

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

		function js_set_value( str )
		{
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

			$('#txt_booking_no').val( name );
			$('#txt_booking_id').val( id );
			//$('#txt_order_id').val( name );
		}
    </script>
	</head>

	<body>
	<div align="center">
		<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
             <table cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
                        <thead>
                        	<th width="150">Company Name</th>
                            <th width="140">Buyer Name</th>
                            <th width="80">Booking No</th>
                            <th>Booking Date</th>
                            <th><input type="reset" id="reset_btn" class="formbutton" style="width:100px" value="Reset" /></th>
                        </thead>
                        <tr>
                            <td>
                                <input type="hidden" id="txt_booking_no">
                                <input type="hidden" id="txt_booking_id">
                                <input type="hidden" id="txt_order_id">
                                <input type="hidden" id="job_no">
                                <input type="hidden" id="cbo_year" value="<? echo $cbo_year;?>">
                                <?
                                    echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $companyID, "load_drop_down( 'fabric_receive_status_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );");
                                ?>
                            </td>
                            <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 140, $blank_array,"", 1, "-- All Buyer --" ); ?></td>
                            <td>
                                <input type="text" id="booking_no_prefix_num" name="booking_no_prefix_num" class="text_boxes_numeric" style="width:75px" />
                            </td>
                            <td align="center">
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:100px">
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:100px">
                            </td>
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('job_no').value+'_'+document.getElementById('booking_no_prefix_num').value+'_'+document.getElementById('cbo_year').value, 'create_booking_search_list_view', 'search_div', 'fabric_receive_status_report2_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                             </td>
                        </tr>
                        <tr>
                            <td colspan="5"  align="center">
                                <? echo load_month_buttons(1);  ?>
                            </td>
                        </tr>
                    </table>
        	<div style="margin-top:5px" id="search_div"></div>
		</form>
	</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="create_booking_search_list_view")
{
	$data=explode('_',$data);
	if ($data[0]!=0) $company=" and  a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer=" and a.buyer_id='$data[1]'"; else $buyer="";
	if ($data[4]!=0) $job_no=" and a.job_no='$data[4]'"; else $job_no='';
	if ($data[5]!=0) $booking_no=" and a.booking_no_prefix_num='$data[5]'"; else $booking_no='';
	if ($data[6]!=0) $cbo_year_con=" and to_char(a.insert_date,'YYYY')=$data[6]"; else $cbo_year_con='';

	//$order_arr=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number"  );
	if($db_type==0)
	{
		if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
	}
	if($db_type==2)
	{
		if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
	}
	$po_array=array();
	$sql_po= sql_select("select b.booking_no,c.po_number from wo_booking_mst a,wo_booking_dtls b,wo_po_break_down c where a.booking_no=b.booking_no and b.po_break_down_id=c.id $company $buyer $booking_no $booking_date and a.booking_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");
	foreach($sql_po as $row)
	{
		$po_no_array[$row[csf("booking_no")]][$row[csf("po_number")]]=$row[csf("po_number")];
	}

	foreach($po_no_array as $booking_number=>$po_no_arr){
		$po_array[$booking_number]=implode(',',$po_no_arr);
	}

	$approved=array(0=>"No",1=>"Yes");
	$is_ready=array(0=>"No",1=>"Yes",2=>"No");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	$arr=array (2=>$comp,3=>$buyer_arr,5=>$po_array,6=>$item_category,7=>$fabric_source,8=>$suplier,9=>$approved,10=>$is_ready);

	$sql= "SELECT a.id,a.booking_no_prefix_num, a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.job_no, a.po_break_down_id, a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.ready_to_approved from wo_booking_mst a left join  wo_po_details_master b on a.job_no=b.job_no and b.status_active=1 and b.is_deleted=0 where a.booking_type=1 $company $buyer $booking_no $booking_date $cbo_year_con and a.status_active=1 and a.is_deleted=0  order by a.id Desc";
	//echo $sql;

	echo  create_list_view("tbl_list_search", "Booking No,Booking Date,Company,Buyer,Job No.,PO number,Fabric Nature,Fabric Source,Approved,Is-Ready", "100,80,70,100,80,220,110,60,60","1020","230",0, $sql , "js_set_value", "id,booking_no_prefix_num", "", 1, "0,0,company_id,buyer_id,0,booking_no,item_category,fabric_source,is_approved,ready_to_approved", $arr , "booking_no,booking_date,company_id,buyer_id,job_no,booking_no,item_category,fabric_source,is_approved,ready_to_approved", '','','0,3,0,0,0,0,0,0,0,0','',1);
   exit();
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$print_report_format=0;
	$print_report_format=return_field_value("format_id","lib_report_template","template_name =".$cbo_company_name." and module_id=7 and report_id=47 and is_deleted=0 and status_active=1");
	$imge_arr=return_library_array( "select master_tble_id, image_location from  common_photo_library where file_type=1",'master_tble_id','image_location');

	$print_report_format_budget_booking=return_field_value("format_id","lib_report_template","template_name =".$cbo_company_name." and module_id=2 and report_id=1 and is_deleted=0 and status_active=1");

	$print_report_format_short_booking=return_field_value("format_id","lib_report_template","template_name =".$cbo_company_name." and module_id=2 and report_id=2 and is_deleted=0 and status_active=1");

	$print_report_format_partial_booking=return_field_value("format_id","lib_report_template","template_name =".$cbo_company_name." and module_id=2 and report_id=35 and is_deleted=0 and status_active=1");

	$print_report_format_sample=return_field_value("format_id","lib_report_template","template_name =".$cbo_company_name." and module_id=2 and report_id=3 and is_deleted=0 and status_active=1");
	$reportFormatSample=explode(",",$print_report_format_sample);
	$reportFormatSample=$reportFormatSample[0];
	$report_format_sample_arr=array(16=>"show_fabric_booking_report_print_booking_3",38=>"show_fabric_booking_report",39=>"show_fabric_booking_report2",64=>"show_fabric_booking_report3");

 	if($template==1)
	{
		$company_name= str_replace("'","",$cbo_company_name);
		$bookingNo = str_replace("'", "", $txt_booking_no);
        $bookingId = str_replace("'", "", $txt_booking_id);
        $cbo_active_status=str_replace("'", "",$cbo_active_status);

        if($cbo_active_status!=4) $orderStatusCond = " and b.status_active in($cbo_active_status)";	else $orderStatusCond = " and b.status_active in(1,2,3)";

        if($bookingNo !="")
        {
        	if($bookingId !=""){$bookingIdCond = " and a.id in($bookingId)";}
        	$sql_booking = "SELECT b.po_break_down_id from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.booking_no_prefix_num in($bookingNo) $bookingIdCond";
			//echo $sql_booking;
        	$sql_res = sql_select($sql_booking);
        	$poIdArray = array();
        	foreach ($sql_res as $val)
        	{
        		$poIdArray[$val[csf('po_break_down_id')]] = $val[csf('po_break_down_id')];
        	}
        	$bookingPoIds = implode(",", $poIdArray);
        }

        if($bookingPoIds !="")	// check booking po
		{
			$po_style_cond=" and b.id in($bookingPoIds)";
		}
		/*else
		{
			if($type==1 || $type==0)
			{
				if ( $txt_search_string!="") $po_style_cond="and LOWER(b.po_number) like LOWER('$search_string')"; else $po_style_cond="";
			}
			else
			{
				if ( $txt_search_string!="") $po_style_cond="and LOWER(a.style_ref_no) like LOWER('$search_string')"; else $po_style_cond="";
			}
		}*/
		//echo $po_style_cond.'system';die;
		//if(str_replace("'","",$cbo_buyer_name)==0) $buyer_name="%%"; else $buyer_name=str_replace("'","",$cbo_buyer_name);
		if(str_replace("'","",$cbo_buyer_name)==0)
		{
			if ($_SESSION['logic_erp']["data_level_secured"]==1)
			{
				if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
			}
			else $buyer_id_cond="";
		}
		else $buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";

		$booking_print_arr=array();
		$booking_print_sql=sql_select("select report_id, format_id from lib_report_template where template_name='$company_name' and module_id=2 and report_id in (1,2,3) and is_deleted=0 and status_active=1");
		foreach($booking_print_sql as $print_id)
		{
			$booking_print_arr[$print_id[csf('report_id')]]=(int) $print_id[csf('format_id')];
		}
		unset($booking_print_sql);
		//print_r($booking_print_arr); die;

		$date_type = str_replace("'","",$cbo_date_type);
		$start_date=str_replace("'","",trim($txt_date_from));
		$end_date=str_replace("'","",trim($txt_date_to));
		if($date_type==1)
		{
			if($start_date!="" && $end_date!="") $date_search_cond_country="and country_ship_date between '$start_date' and '$end_date'"; else $date_search_cond_country="";
			if ($start_date=="" && $end_date=="") $date_search_cond=""; else $date_search_cond="and c.country_ship_date between '$start_date' and '$end_date'";
		}
		else if($date_type==2)
		{
			if ($start_date=="" && $end_date=="") $date_search_cond=""; else $date_search_cond="and b.pub_shipment_date between '$start_date' and '$end_date'";

		}
		else if($date_type==3)
		{
			if ($start_date=="" && $end_date=="") $date_search_cond=""; else $date_search_cond="and b.shipment_date between '$start_date' and '$end_date'";
		}
		else if($date_type==4)
		{
			if($db_type==0)
			{
				if ($start_date=="" && $end_date=="") $date_search_cond=""; else $date_search_cond="and b.insert_date between '".$start_date."' and '".$end_date." 23:59:59'";
			}
			else
			{
				if ($start_date=="" && $end_date=="") $date_search_cond=""; else $date_search_cond="and b.insert_date between '".$start_date."' and '".$end_date." 11:59:59 PM'";
			}
		}

		$cbo_year=str_replace("'","",$cbo_year);
		if(trim($cbo_year)!=0)
		{
			if($db_type==0) $year_cond=" and YEAR(a.insert_date)=$cbo_year";
			else if($db_type==2) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
			else $year_cond="";
		}
		else $year_cond="";

		$txt_job_no=str_replace("'","",$txt_job_no);
		$job_no_cond="";
		if(trim($txt_job_no)!="")
		{
			$job_no=trim($txt_job_no);
			$job_no_cond=" and a.job_no_prefix_num=$job_no";
		}

		$cbo_type=str_replace("'","",$cbo_type);
		$txt_search_string=str_replace("'","",$txt_search_string);
		if(trim($txt_search_string)!="") $search_string="%".trim($txt_search_string)."%"; else $search_string="%%";

		if($cbo_type==1)
		{
			if(trim($txt_search_string)!="") $po_style_src_cond=" and b.po_number like '$search_string'"; else $po_style_src_cond="";
		}
		else if($cbo_type==2)
		{
			if(trim($txt_search_string)!="") $po_style_src_cond=" and a.style_ref_no like '$search_string'"; else $po_style_src_cond="";
		}
		else if($cbo_type==3)
		{
			if(trim($txt_search_string)!="") $po_style_src_cond=" and b.file_no='$txt_search_string'"; else $po_style_src_cond="";
		}
		else if($cbo_type==4)
		{
			if(trim($txt_search_string)!="") $po_style_src_cond=" and b.grouping='$txt_search_string'"; else $po_style_src_cond="";
		}

		if (str_replace("'", "", trim($cbo_order_status))!= 0) $is_confirmed_cond = " and b.is_confirmed = '" . str_replace("'", "", trim($cbo_order_status)) . "'";
        else $is_confirmed_cond = "";

		$cbo_discrepancy=str_replace("'","",trim($cbo_discrepancy));
		if($cbo_discrepancy==0) $discrepancy_td_color=""; else $discrepancy_td_color="#FF4F4F";

		if ($start_date=="" && $end_date=="") $country_date_cond=""; else $country_date_cond="and c.country_ship_date between '$start_date' and '$end_date'";
		$cbo_shipping_status=str_replace("'","",trim($cbo_shipping_status));
		if(trim($cbo_shipping_status)!="") $shipping_status="%".trim($cbo_shipping_status)."%"; else $shipping_status="%%";
		if ($shipping_status=='%%') $siping_status_cond=""; else $siping_status_cond="and b.shiping_status like '$shipping_status'";
		if($date_type!=1) // check no booking $chk_no_boking == 1 &&
		{
			$sql="SELECT a.company_name, a.buyer_name, a.id as job_id,a.job_no_prefix_num, a.job_no, a.style_ref_no, a.gmts_item_id, a.order_uom, a.total_set_qnty as ratio, b.grouping, b.file_no, b.id as po_id, b.po_number, b.po_quantity as po_qnty, b.pub_shipment_date, b.shiping_status, b.insert_date, b.po_received_date, b.plan_cut, b.is_confirmed from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and a.company_name='$company_name' $siping_status_cond and a.is_deleted=0 and a.status_active=1  $orderStatusCond $buyer_id_cond $date_search_cond $year_cond $job_no_cond $po_style_src_cond $is_confirmed_cond $po_style_cond group by a.company_name, a.buyer_name, a.job_no_prefix_num, a.id,a.job_no, a.style_ref_no, a.gmts_item_id, a.order_uom, a.total_set_qnty, b.grouping, b.file_no, b.id, b.po_number, b.po_quantity, b.pub_shipment_date, b.shiping_status, b.insert_date, b.po_received_date, b.plan_cut, b.is_confirmed order by b.pub_shipment_date, b.id";
		}
		else
		{
			$sql="SELECT a.company_name, a.buyer_name, a.id as job_id, a.job_no_prefix_num, a.job_no, a.style_ref_no, a.gmts_item_id, a.order_uom, a.total_set_qnty as ratio, b.grouping, b.file_no, b.id as po_id, b.po_number, b.po_quantity as po_qnty, b.pub_shipment_date, b.shiping_status, b.insert_date, b.po_received_date, b.plan_cut, b.is_confirmed from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where  a.id=b.job_id and a.company_name='$company_name' and b.id=c.po_break_down_id $siping_status_cond and a.is_deleted=0 and a.status_active=1  and c.is_deleted=0 and c.status_active=1 $orderStatusCond $buyer_id_cond $date_search_cond $year_cond $job_no_cond $po_style_src_cond $is_confirmed_cond $po_style_cond group by a.company_name, a.buyer_name, a.job_no_prefix_num, a.job_no, a.style_ref_no, a.gmts_item_id, a.order_uom, a.total_set_qnty, b.grouping, b.file_no, b.id, b.po_number,a.id, b.po_quantity, b.pub_shipment_date, b.shiping_status, b.insert_date, b.po_received_date, b.plan_cut, b.is_confirmed order by b.pub_shipment_date, b.id";
		}

		//echo $sql;
		$nameArray=sql_select($sql); $po_data_arr=array(); $job_data_arr=array(); $job_allData_arr=array(); $poIdArray= array(); $jobIdArray= array(); $tot_rows=0; $poIds='';
		if(count($nameArray)>0)
		{
			foreach($nameArray as $row)
			{
				$tot_rows++;
				$poIds.=$row[csf("po_id")].","; $jobIds.=$row[csf("job_id")].",";
				$poIdArray[$row[csf('po_id')]] = $row[csf('po_id')];
				$jobIdArray[$row[csf('job_id')]] = $row[csf('job_id')];
				if($type==1  || $type==3)
				{
					$po_data_arr[$row[csf("po_id")]]=$row[csf("company_name")]."##".$row[csf("buyer_name")]."##".$row[csf("job_no_prefix_num")]."##".$row[csf("job_no")]."##".$row[csf("style_ref_no")]."##".$row[csf("gmts_item_id")]."##".$row[csf("order_uom")]."##".$row[csf("ratio")]."##".$row[csf("grouping")]."##".$row[csf("file_no")]."##".$row[csf("po_number")]."##".$row[csf("po_qnty")]."##".$row[csf("pub_shipment_date")]."##".$row[csf("shiping_status")]."##".$row[csf("insert_date")]."##".$row[csf("po_received_date")]."##".$row[csf("plan_cut")]."##".$row[csf("is_confirmed")];
				}
				if($type==2)
				{
					$job_data_arr[$row[csf("job_no")]]=$row[csf("company_name")]."##".$row[csf("buyer_name")]."##".$row[csf("job_no_prefix_num")]."##".$row[csf("style_ref_no")]."##".$row[csf("gmts_item_id")]."##".$row[csf("order_uom")]."##".$row[csf("ratio")];

					$job_allData_arr[$row[csf("job_no")]].=$row[csf("grouping")]."**".$row[csf("file_no")]."**".$row[csf("po_number")]."**".$row[csf("po_qnty")]."**".$row[csf("pub_shipment_date")]."**".$row[csf("shiping_status")]."**".$row[csf("insert_date")]."**".$row[csf("po_received_date")]."**".$row[csf("plan_cut")]."**".$row[csf("is_confirmed")]."**".$row[csf("po_id")]."___";
				}
			}
		}
		else
		{
			echo "3**".'Data Not Found'; die;
		}
		unset($nameArray);

		$con = connect();
		execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1,2) and ENTRY_FORM=30");
		oci_commit($con);

		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 30, 1, $poIdArray, $empty_arr);//Po ID
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 30, 2, $poIdArray, $empty_arr);//Job ID
		disconnect($con);

		$contry_ship_qty_arr=array();
		$country_ship_qty_sql="select a.po_break_down_id, a.country_ship_date, a.order_quantity as ship_qty from wo_po_color_size_breakdown a, gbl_temp_engine d where a.status_active=1 and a.is_deleted=0 and a.po_break_down_id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=30 and d.ref_from=1 $date_search_cond_country";
		$country_ship_qty_sql_result=sql_select($country_ship_qty_sql);
		foreach($country_ship_qty_sql_result as $row )
		{
			$contry_ship_qty_arr[$row[csf('po_break_down_id')]]['ship_qty']+=$row[csf('ship_qty')];
			$contry_ship_qty_arr[$row[csf('po_break_down_id')]]['ship_date'].=$row[csf('country_ship_date')].',';
		}
		unset($country_ship_qty_sql);
		//var_dump($contry_ship_qty_arr);
		//die;
		$txt_fab_color=str_replace("'","",$txt_fab_color);
		if(trim($txt_fab_color)!="") $fab_color="%".trim($txt_fab_color)."%"; else $fab_color="%%";

		if($txt_fab_color=="")
		{
			$color_cond="";	$color_cond_prop="";
		}
		else
		{
			if($db_type==0)
			{
				$color_id=return_field_value("group_concat(id) as color_id","lib_color","color_name like '$fab_color'","color_id");
			}
			else
			{
				$color_id=return_field_value("LISTAGG(id, ',') WITHIN GROUP (ORDER BY id) as color_id","lib_color","color_name like '$fab_color'","color_id");
			}
			if($color_id=="")
			{
				$color_cond_search="";
				$color_cond_prop="";
			}
			else
			{
				$color_cond_search=" and b.fabric_color_id in ($color_id)";
				$color_cond_prop=" and color_id in ($color_id)";
			}
		}

		$lapdipDataEc=sql_select("select a.job_no_mst, a.po_break_down_id, a.color_name_id, a.lapdip_no from wo_po_lapdip_approval_info a, gbl_temp_engine d where a.is_deleted=0 and a.status_active=1 and a.po_break_down_id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=30 and d.ref_from=1");
		foreach($lapdipDataEc as $row)
		{
			$key=$row[csf('job_no_mst')].$row[csf('po_break_down_id')].$row[csf('color_name_id')];
			$lapdip_arr[$key]= $row[csf('lapdip_no')];
		}
		unset($lapdipDataEc);

		$dataArrayYarn=array(); $dataArrayYarnIssue=array();
		$yarn_sql="select a.job_no, a.count_id, a.copm_one_id, a.percent_one, a.copm_two_id, a.percent_two, a.type_id, sum(a.avg_cons_qnty) as qnty from wo_pre_cost_fab_yarn_cost_dtls a, gbl_temp_engine d where a.status_active=1 and a.is_deleted=0 and a.job_id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=30 and d.ref_from=2 group by a.job_no, a.count_id, a.copm_one_id, a.percent_one, a.copm_two_id, a.percent_two, a.type_id";
		$resultYarn=sql_select($yarn_sql);
		foreach($resultYarn as $yarnRow)
		{
			$dataArrayYarn[$yarnRow[csf('job_no')]].=$yarnRow[csf('count_id')]."**".$yarnRow[csf('copm_one_id')]."**".$yarnRow[csf('percent_one')]."**".$yarnRow[csf('copm_two_id')]."**".$yarnRow[csf('percent_two')]."**".$yarnRow[csf('type_id')]."**".$yarnRow[csf('qnty')].",";
		}
		unset($resultYarn);
		//print_r($dataArrayYarn);

		$sql_yarn_iss="select a.po_breakdown_id, b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type,
				sum(CASE WHEN a.entry_form ='3' THEN a.quantity ELSE 0 END) AS issue_qnty,
				sum(CASE WHEN a.entry_form ='9' THEN a.quantity ELSE 0 END) AS return_qnty
				from order_wise_pro_details a, product_details_master b, gbl_temp_engine d where a.prod_id=b.id and b.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and a.issue_purpose in (1,4) and a.po_breakdown_id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=30 and d.ref_from=1 group by a.po_breakdown_id, b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type";
		$dataArrayIssue=sql_select($sql_yarn_iss);
		foreach($dataArrayIssue as $row_yarn_iss)
		{
			$dataArrayYarnIssue[$row_yarn_iss[csf('po_breakdown_id')]].=$row_yarn_iss[csf('yarn_count_id')]."**".$row_yarn_iss[csf('yarn_comp_type1st')]."**".$row_yarn_iss[csf('yarn_comp_percent1st')]."**".$row_yarn_iss[csf('yarn_comp_type2nd')]."**".$row_yarn_iss[csf('yarn_comp_percent2nd')]."**".$row_yarn_iss[csf('yarn_type')]."**".$row_yarn_iss[csf('issue_qnty')]."**".$row_yarn_iss[csf('return_qnty')].",";
		}
		unset($dataArrayIssue);
		$yarnAllocationArr=array(); //$yarnAllocationJobArr=array();

		$sql_yarn_allocation="select a.po_break_down_id, b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type,
				sum(a.qnty) AS allocation_qty,a.is_dyied_yarn
				from inv_material_allocation_dtls a, product_details_master b, gbl_temp_engine d where a.item_id=b.id and b.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and a.po_break_down_id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=30 and d.ref_from=1 group by a.po_break_down_id, b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type,a.is_dyied_yarn";
		//echo $sql_yarn_allocation;
		$dataArrayAllocation=sql_select($sql_yarn_allocation);
		foreach($dataArrayAllocation as $allocationRow)
		{
			$yarnAllocationArr[$allocationRow[csf('po_break_down_id')]].=$allocationRow[csf('yarn_count_id')]."**".$allocationRow[csf('yarn_comp_type1st')]."**".$allocationRow[csf('yarn_comp_percent1st')]."**".$allocationRow[csf('yarn_comp_type2nd')]."**".$allocationRow[csf('yarn_comp_percent2nd')]."**".$allocationRow[csf('yarn_type')]."**".$allocationRow[csf('allocation_qty')]."**".$allocationRow[csf('is_dyied_yarn')].",";
		}
		//var_dump($yarnAllocationArr);
		unset($dataArrayAllocation);

		$greyPurchaseQntyArray=array(); $greyrec_basis_arr=array();
		$sql_grey_purchase="select a.id, c.po_breakdown_id, a.receive_basis, c.quantity from inv_receive_master a,pro_grey_prod_entry_dtls b, order_wise_pro_details c, gbl_temp_engine d where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in (22,58) and c.entry_form in (22,58) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=30 and d.ref_from=1";
		$dataArrayGreyPurchase=sql_select($sql_grey_purchase);
		foreach($dataArrayGreyPurchase as $greyRow)
		{
			if($greyRow[csf('receive_basis')]==9 || $greyRow[csf('receive_basis')]==10)
			{
				$greyPurchaseQntyArray[$greyRow[csf('po_breakdown_id')]]['production']+=$greyRow[csf('quantity')];
			}
			else
			{
				$greyPurchaseQntyArray[$greyRow[csf('po_breakdown_id')]]['purchase']+=$greyRow[csf('quantity')];
			}
			$greyrec_basis_arr[$greyRow[csf('id')]]=$greyRow[csf('receive_basis')];
		}
		unset($dataArrayGreyPurchase);
		//print_r($greyrec_basis_arr); die;

		$finish_purchase_qnty_arr=array(); $finishrec_basis_arr=array();

		$sql_fin_purchase="select a.id, c.po_breakdown_id, c.color_id, a.receive_basis, c.quantity

		 from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c, gbl_temp_engine d where a.id=b.mst_id and b.id=c.dtls_id  and a.entry_form in(17,37,58,68) and c.entry_form in(17,37,58,68) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and c.po_breakdown_id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=30 and d.ref_from=1";
		 //echo $sql_fin_purchase;die;
		$dataArrayFinPurchase=sql_select($sql_fin_purchase);
		foreach($dataArrayFinPurchase as $finRow)
		{
			if($finRow[csf('receive_basis')]==9)
			{
				$finish_purchase_qnty_arr[$finRow[csf('po_breakdown_id')]][$finRow[csf('color_id')]]['production']+=$finRow[csf('quantity')];
			}
			else
			{
				$finish_purchase_qnty_arr[$finRow[csf('po_breakdown_id')]][$finRow[csf('color_id')]]['purchase']+=$finRow[csf('quantity')];
			}
			$finishrec_basis_arr[$finRow[csf('id')]]=$finRow[csf('receive_basis')];
		}
		unset($dataArrayFinPurchase);

		$grey_receive_return_qnty_arr=array(); $finish_recv_rtn_qnty_arr=array();
		$sql_return="select a.entry_form, a.received_id, c.trans_type, c.po_breakdown_id, c.color_id, c.quantity from inv_issue_master a, inv_transaction b, order_wise_pro_details c, gbl_temp_engine d where a.id=b.mst_id and b.id=c.trans_id and a.entry_form in (45,46,126,202) and c.entry_form in (45,46,126,202) and c.trans_type=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=30 and d.ref_from=1";
		$dataArrayReturn=sql_select($sql_return);
		//echo $sql_return; die;
		foreach($dataArrayReturn as $rtRow)
		{
			if($rtRow[csf('entry_form')]==45)
			{
				$grey_rec_bacis=$greyrec_basis_arr[$rtRow[csf('received_id')]];
				if($grey_rec_bacis==9)
				{
					$grey_receive_return_qnty_arr[$rtRow[csf('po_breakdown_id')]]['production']+= $rtRow[csf('quantity')];
				}
				else
				{
					$grey_receive_return_qnty_arr[$rtRow[csf('po_breakdown_id')]]['purchase']+= $rtRow[csf('quantity')];
				}
			}

			if($rtRow[csf('entry_form')]==46 || $rtRow[csf('entry_form')]==126 || $rtRow[csf('entry_form')]==202)
			{
				$finish_rec_bacis=$finishrec_basis_arr[$rtRow[csf('received_id')]];
				if($finish_rec_bacis==9)
				{
					$finish_recv_rtn_qnty_arr[$rtRow[csf('po_breakdown_id')]][$rtRow[csf('color_id')]]['production']+=$rtRow[csf('quantity')];
				}
				else
				{
					$finish_recv_rtn_qnty_arr[$rtRow[csf('po_breakdown_id')]][$rtRow[csf('color_id')]]['purchase']+=$rtRow[csf('quantity')];
				}
			}
		}
		unset($dataArrayReturn);
		//print_r($finish_recv_rtn_qnty_arr); die;

		$greyDeliveryArray=array();
		//$sql_grey_delivery="select order_id, sum(current_delivery) as grey_delivery_qty from pro_grey_prod_delivery_dtls where entry_form in(53,56) and status_active=1 and is_deleted=0 $grey_delivery_po_cond group by order_id";
		$sql_grey_delivery="select a.order_id, sum(a.current_delivery) as grey_delivery_qty from pro_grey_prod_delivery_dtls a,inv_receive_master b, gbl_temp_engine d where a.grey_sys_id=b.id and a.entry_form in(53,56) and  b.entry_form in(2) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.order_id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=30 and d.ref_from=1 and b.booking_without_order <>1 group by a.order_id";

		$data_grey_delivery=sql_select($sql_grey_delivery);
		foreach($data_grey_delivery as $greyDel)
		{
			$greyDeliveryArray[$greyDel[csf('order_id')]]=$greyDel[csf('grey_delivery_qty')];
		}
		unset($data_grey_delivery);
		//var_dump($greyDeliveryArray);
		$finDeliveryArray=array();
		/*$sql_fin_delivery="select a.order_id, b.color, sum(a.current_delivery) as fin_delivery_qty from pro_grey_prod_delivery_dtls a, product_details_master b, gbl_temp_engine d where a.product_id=b.id and a.entry_form in(54,67) and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and a.order_id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=30 and d.ref_from=1 group by a.order_id, b.color";*/
		$sql_fin_delivery="SELECT a.order_id, b.color, sum(a.current_delivery) as fin_delivery_qty
		from pro_grey_prod_delivery_dtls a, product_details_master b, PRO_BATCH_CREATE_MST e, gbl_temp_engine d
		where a.product_id=b.id and a.batch_id=e.id and a.entry_form in(54,67) and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and e.booking_without_order=0  and a.order_id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=30 and d.ref_from=1
		group by a.order_id, b.color";
		$data_fin_delivery=sql_select($sql_fin_delivery);
		foreach($data_fin_delivery as $finDel)
		{
			$finDeliveryArray[$finDel[csf('order_id')]][$finDel[csf('color')]]=$finDel[csf('fin_delivery_qty')];
		}
		unset($data_fin_delivery);

		$trans_qnty_arr=array(); $grey_receive_qnty_arr=array(); $grey_issue_qnty_arr=array(); $grey_issue_return_qnty_arr=array();
		$trans_qnty_fin_arr=array(); $finish_receive_qnty_arr=array(); $finish_issue_qnty_arr=array(); $finish_issue_rtn_qnty_arr=array(); $po_color_arr=array();


		$dataArrayTrans = sql_select("select a.trans_id, a.po_breakdown_id, a.color_id, a.entry_form, a.trans_type, a.quantity from order_wise_pro_details a, gbl_temp_engine d where a.status_active=1 and a.is_deleted=0 and a.entry_form in (2,7,11,14,13,15,16,18,19,37,51,52,61,66,68,71,80,81,82,83,84,126,134,110,183,209) and a.po_breakdown_id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=30 and d.ref_from=1");
		//echo "select trans_id, po_breakdown_id, color_id, entry_form, trans_type, quantity from order_wise_pro_details where status_active=1 and is_deleted=0 and entry_form in (2,7,11,13,14,15,16,18,37,51,52,61,66,68,71,80,81,82,83,84,126,134,110,183) $trans_po_cond";	die;
		foreach($dataArrayTrans as $row)
		{
			//knit
			if($row[csf('entry_form')]==2 || $row[csf('entry_form')]==11 || $row[csf('entry_form')]==13 || $row[csf('entry_form')]==16 || $row[csf('entry_form')]==51 || $row[csf('entry_form')]==58 || $row[csf('entry_form')]==61 || $row[csf('entry_form')]==80 || $row[csf('entry_form')]==81 || $row[csf('entry_form')]==82 || $row[csf('entry_form')]==83 || $row[csf('entry_form')]==84 || $row[csf('entry_form')]==110 || $row[csf('entry_form')]==183)
			{
				if($row[csf('entry_form')]==2  || $row[csf('entry_form')]==58) $grey_receive_qnty_arr[$row[csf('po_breakdown_id')]] += $row[csf('quantity')];

				//if($row[csf('entry_form')]==45 && $row[csf('trans_type')]==3) $grey_receive_return_qnty_arr[$row[csf('po_breakdown_id')]] += $row[csf('quantity')];
				if($row[csf('entry_form')]==51 || $row[csf('entry_form')]==84)
				{
					if($row[csf('trans_type')]==4) $grey_issue_return_qnty_arr[$row[csf('po_breakdown_id')]] += $row[csf('quantity')];
				}
				$grey_issue=0;
				if($row[csf('entry_form')]==16 || $row[csf('entry_form')]==61)
				{
					$grey_issue=$row[csf('quantity')];
					$grey_issue_qnty_arr[$row[csf('po_breakdown_id')]] += $row[csf('quantity')];
				}

				if($row[csf('entry_form')]==11)
				{
					if($row[csf('trans_type')]==5 || $row[csf('trans_type')]==6)
					{
						$trans_qnty_arr[$row[csf('po_breakdown_id')]]['yarn_trans'] += $row[csf('quantity')];
					}
				}
				$grey_trns_out=0; $grey_trns_in=0;
				if($row[csf('entry_form')]==13 || $row[csf('entry_form')]==80 || $row[csf('entry_form')]==81 || $row[csf('entry_form')]==82 || $row[csf('entry_form')]==83 || $row[csf('entry_form')]==110 || $row[csf('entry_form')]==183)
				{
					if($row[csf('trans_type')]==5)
					{
						$trans_qnty_arr[$row[csf('po_breakdown_id')]]['knit_trans'] += $row[csf('quantity')];
						if($row[csf('trans_id')]!=0) $grey_trns_in=$row[csf('quantity')];
					}
					if($row[csf('trans_type')]==6)
					{
						$trans_qnty_arr[$row[csf('po_breakdown_id')]]['knit_trans'] -= $row[csf('quantity')];
						if($row[csf('trans_id')]!=0) $grey_trns_out=$row[csf('quantity')];
					}
				}

				$knit_avail=0; $grey_rec=0; $grey_purchase=0; $grey_rec_return=0; $net_grey_trns=0;
				if($row[csf('trans_id')]!=0)
				{
					if($row[csf('entry_form')]==2) $grey_rec= $row[csf('quantity')];
					if($row[csf('entry_form')]==45 && $row[csf('trans_type')]==3) $grey_rec_return= $row[csf('quantity')];
					$grey_purchase=($greyPurchaseQntyArray[$row[csf('po_breakdown_id')]]['purchase']+$greyPurchaseQntyArray[$row[csf('po_breakdown_id')]]['production'])-$grey_rec_return;
				}
				$net_grey_trns=$grey_trns_in-$grey_trns_out;
				$knit_avail=($grey_rec+$grey_purchase+$net_grey_trns)-$grey_issue;
				$grey_available_arr[$row[csf('po_breakdown_id')]]+=$knit_avail;
			}
		    //finish
			if($row[csf('entry_form')]==7 || $row[csf('entry_form')]==14 || $row[csf('entry_form')]==15 || $row[csf('entry_form')]==18 || $row[csf('entry_form')]==19 || $row[csf('entry_form')]==37 || $row[csf('entry_form')]==52 || $row[csf('entry_form')]==66 || $row[csf('entry_form')]==71 || $row[csf('entry_form')]==126 || $row[csf('entry_form')]==134 || $row[csf('entry_form')]==209)
			{
				$finish_trns_out=0; $finish_trns_in=0;
				if($row[csf('entry_form')]==14 || $row[csf('entry_form')]==15 || $row[csf('entry_form')]==134)
				{
					if($row[csf('trans_type')]==5)
					{
						$trans_qnty_fin_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]['trans']+= $row[csf('quantity')];
						if($row[csf('trans_id')]!=0) $finish_trns_in=$row[csf('quantity')];
					}
					if($row[csf('trans_type')]==6)
					{
						$trans_qnty_fin_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]['trans']-= $row[csf('quantity')];
						if($row[csf('trans_id')]!=0) $finish_trns_out=$row[csf('quantity')];
					}
				}

				if($row[csf('entry_form')]==7 || $row[csf('entry_form')]==66) $finish_receive_qnty_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]] +=$row[csf('quantity')];
				if($row[csf('entry_form')]==7 && $row[csf('trans_id')]!=0) $finish_purchase_qnty_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]['production']+= $row[csf('quantity')];
				$finish_issue=0;
				if($row[csf('entry_form')]==18 || $row[csf('entry_form')]==19 || $row[csf('entry_form')]==71)
				{
					$finish_issue=$row[csf('quantity')];
					$finish_issue_qnty_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]] +=$row[csf('quantity')];
				}
				//if($row[csf('entry_form')]==46 && $row[csf('trans_type')]==3) $finish_recv_rtn_qnty_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]] +=$row[csf('quantity')];
				if(($row[csf('entry_form')]==52 || $row[csf('entry_form')]==126 || $row[csf('entry_form')]==209) && $row[csf('trans_type')]==4) $finish_issue_rtn_qnty_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]] +=$row[csf('quantity')];
				$finish_avail=0; $finish_rec=0; $finish_purchase=0; $finish_rec_return=0; $net_finish_trns=0;
				if($row[csf('trans_id')]!=0)
				{
					if(($row[csf('entry_form')]==7 || $row[csf('entry_form')]==37) && $row[csf('trans_id')]==0)
					{
						$finish_rec= $row[csf('quantity')];
						if($row[csf('entry_form')]==7)
						{
							$finish_receive_qnty_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]] +=$row[csf('quantity')];
							//$finish_purchase_qnty_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]['production']+= $row[csf('quantity')];
						}
					}
					if($row[csf('entry_form')]==46 && $row[csf('trans_type')]==3) $finish_rec_return= $row[csf('quantity')];
				}

				$net_finish_trns=$finish_trns_in-$finish_trns_out;

				$finish_avail=$finish_rec+($net_finish_trns - $finish_rec_return);
				$finish_available_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]+=$finish_avail;
				//finish color arr

				if($row[csf('entry_form')]==7 || $row[csf('entry_form')]==18 || $row[csf('entry_form')]==37 || $row[csf('entry_form')]==66 || $row[csf('entry_form')]==68 || $row[csf('entry_form')]==71)
				{
					$po_color_arr[$row[csf('po_breakdown_id')]].=$row[csf('color_id')].',';
				}
			}
		}
		unset($dataArrayTrans);
		// echo $trans_qnty_arr[37073]['knit_trans'];die();
		//print_r($finish_purchase_qnty_arr[34164][89]['production']); die;

		$batch_qnty_arr=array();
		$sql_batch="select a.color_id, b.po_id, sum(b.batch_qnty) as batch_qnty from pro_batch_create_mst a, pro_batch_create_dtls b, gbl_temp_engine d where a.id=b.mst_id and a.entry_form not in (36,74,17,7,37,14,134) and  a.batch_against not in(2) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=30 and d.ref_from=1 group by a.color_id, b.po_id";
		//and a.entry_form!=36

		$resultBatch=sql_select($sql_batch);
		foreach($resultBatch as $batchRow)
		{
			$batch_qnty_arr[$batchRow[csf('po_id')]][$batchRow[csf('color_id')]]=$batchRow[csf('batch_qnty')];
		}
		unset($resultBatch);

		$grey_receive_by_batch=sql_select("select a.po_breakdown_id, a.qnty from pro_roll_details a, gbl_temp_engine d where a.entry_form=62 and a.status_active=1 and a.is_deleted=0 and a.po_breakdown_id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=30 and d.ref_from=1");

		$receive_by_batch_data=array();
		foreach($grey_receive_by_batch as $row)
		{
			$receive_by_batch_data[$row[csf("po_breakdown_id")]] +=$row[csf("qnty")];
		}
		unset($grey_receive_by_batch);

		$grey_receive_by_batch_withoutroll=sql_select("select b.po_id, b.batch_qnty as qnty from pro_batch_create_mst a, pro_batch_create_dtls b, gbl_temp_engine d where a.id=b.mst_id and a.page_without_roll=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=30 and d.ref_from=1");

		foreach($grey_receive_by_batch_withoutroll as $row)
		{
			$receive_by_batch_data[$row[csf("po_id")]]+=$row[csf("qnty")];
		}
		unset($grey_receive_by_batch_withoutroll);

		$dye_qnty_arr=array();
		$sql_dye="select b.po_id, a.color_id, b.batch_qnty as dye_qnty from pro_batch_create_mst a,pro_batch_create_dtls b, pro_fab_subprocess c, gbl_temp_engine d where a.id=b.mst_id and a.id=c.batch_id and c.load_unload_id=2 and c.entry_form=35 and a.batch_against<>2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.po_id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=30 and d.ref_from=1";
		$resultDye=sql_select($sql_dye);
		foreach($resultDye as $dyeRow)
		{
			$dye_qnty_arr[$dyeRow[csf('po_id')]][$dyeRow[csf('color_id')]]+=$dyeRow[csf('dye_qnty')];
		}
		unset($resultDye);
		$dataArrayWo=array(); $fab_source_arr=array();
		$sql_wo="select b.po_break_down_id, a.id, a.booking_no, a.insert_date, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.booking_no_prefix_num, a.job_no, a.is_short, a.is_approved, a.po_break_down_id as wo_po_id,a.entry_form, b.fabric_color_id, sum(b.fin_fab_qnty) as req_qnty, sum(b.grey_fab_qnty) as grey_req_qnty from wo_booking_mst a, wo_booking_dtls b, gbl_temp_engine d where a.booking_no=b.booking_no and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.po_break_down_id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=30 and d.ref_from=1 $color_cond_search group by b.po_break_down_id, a.id, a.booking_no, a.insert_date, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.booking_no_prefix_num, a.job_no, a.is_short, a.is_approved, a.po_break_down_id,a.entry_form, b.fabric_color_id";
		//echo $sql_wo;die;
		$resultWo=sql_select($sql_wo);
		foreach($resultWo as $woRow)
		{
			$dataArrayWo[$woRow[csf('po_break_down_id')]].=$woRow[csf('id')]."**".$woRow[csf('booking_no')]."**".$woRow[csf('insert_date')]."**".$woRow[csf('item_category')]."**".$woRow[csf('fabric_source')]."**".$woRow[csf('company_id')]."**".$woRow[csf('booking_type')]."**".$woRow[csf('booking_no_prefix_num')]."**".$woRow[csf('job_no')]."**".$woRow[csf('is_short')]."**".$woRow[csf('is_approved')]."**".$woRow[csf('fabric_color_id')]."**".$woRow[csf('req_qnty')]."**".$woRow[csf('grey_req_qnty')]."**".$woRow[csf('wo_po_id')]."__";
			 $fab_source_arr[$woRow[csf('po_break_down_id')]].=$woRow[csf('fabric_source')].',';
			 $bookingEntryFromArr[$woRow[csf('booking_no')]]=$woRow[csf('entry_form')];

		}
		unset($resultWo);
		$tna_tsk_arr=array(50,60,61,73);
		$tna_plan_actual_arr=array();
		$tna_sql="select a.po_number_id";
		$i=1;
		if($db_type==0)
		{
			foreach( $tna_tsk_arr as $dval)
			{
				$tna_sql .=", max(CASE WHEN CONCAT(a.task_number) = '".$dval."' THEN concat(a.task_finish_date,'_',a.actual_finish_date)  END ) as status_$dval ";
			}
		}
		else if ($db_type==2)
		{
			foreach( $tna_tsk_arr as $dval)
			{
				$tna_sql .=", max(CASE WHEN a.task_number = '".$dval."' THEN a.task_finish_date || '_' || a.actual_finish_date END ) as status_$dval ";
			}
		}
		$tna_sql .=" from tna_process_mst a, gbl_temp_engine d where a.is_deleted=0 and a.status_active=1 and a.po_number_id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=30 and d.ref_from=1 group by a.po_number_id";
		//echo $tna_sql;
		$tna_sql_result = sql_select($tna_sql);
		foreach($tna_sql_result as $tnaVal)
		{
			foreach( $tna_tsk_arr as $dval)
			{
				$tna_date=explode('_',$tnaVal[csf('status_'.$dval)]);
				$plan_fin_date=""; $actual_fin_date="";
				if($tna_date[0]=="" || $tna_date[0]=='0000-00-00') $plan_fin_date=""; else $plan_fin_date=date("Y-m-d",strtotime($tna_date[0]));
				if($tna_date[1]=="" || $tna_date[1]=='0000-00-00') $actual_fin_date=""; else $actual_fin_date=date("Y-m-d",strtotime($tna_date[1]));
				$tna_plan_actual_arr[$tnaVal[csf('po_number_id')]][$dval]['plan']=$plan_fin_date;
				$tna_plan_actual_arr[$tnaVal[csf('po_number_id')]][$dval]['actual']=$actual_fin_date;
			}
		}
		unset($tna_sql_result);
		//var_dump($tna_plan_actual_arr);

		/*$reqSQL = "select job_no, sum(avg_cons) as grey_cons, sum(avg_finish_cons) as fin_cons from  wo_pre_cost_fabric_cost_dtls where status_active=1 and is_deleted=0 group by job_no";
		$reqSQLresult = sql_select($reqSQL);
		$reqArr = array();
		foreach($reqSQLresult as $val)
		{
			$reqArr[$val[csf('job_no')]]['grey']+=$val[csf('grey_cons')];
			$reqArr[$val[csf('job_no')]]['finish']+=$val[csf('fin_cons')];
		}*/
		//var_dump($reqArr);

		//var_dump($contry_ship_qty_arr);
		$tot_order_qnty=0; $tot_mkt_required=0; $tot_yarn_issue_qnty=0; $tot_balance=0; $tot_fabric_req=0; $tot_grey_recv_qnty=0; $tot_grey_balance=0; $tot_grey_available=0;
		$tot_grey_issue=0; $tot_batch_qnty=0; $tot_color_wise_req=0; $tot_dye_qnty=0; $tot_fabric_recv=0; $tot_fabric_purchase=0; $tot_fabric_balance=0; $tot_issue_to_cut_qnty=0;
		$tot_fabric_available=0; $tot_fabric_left_over=0; $tot_fabric_left_over_excel=0; $tot_fabric_recv_excel=0;$tot_batch_qnty_excel=0;$tot_grey_prod_balance=0;$total_grey_del_store=0; $tot_net_trans_knit_qnty=0; $tot_country_ship_qty=0;

		$buyer_name_array= array(); $order_qty_array= array(); $country_order_qty_array= array(); $grey_required_array= array(); $yarn_issue_array= array(); $grey_issue_array= array();
		$fin_fab_Requi_array= array(); $finFabProductionArr= array(); $fin_fab_recei_array= array(); $issue_to_cut_array= array(); $yarn_balance_array= array();
		$grey_balance_array= array(); $fin_balance_array= array(); $knitted_array=array(); $dye_qnty_array=array(); $batch_qnty_array=array(); $issue_toCut_array=array();

		$template_id_arr=return_library_array("select a.po_number_id, a.template_id from tna_process_mst a, gbl_temp_engine d where a.po_number_id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=30 and d.ref_from=1 group by a.po_number_id, a.template_id","po_number_id","template_id");

		$con = connect();
		execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1,2) and ENTRY_FORM=30");
		oci_commit($con);
		disconnect($con);
		//echo $sql;die;
		if($type==1 )
		{
			$table_width="6850"; $colspan="21";
		}
		else if($type==3)
		{
			$table_width="6750"; $colspan="21";
		}
		else
		{
			$table_width="6490"; $colspan="18";
		}
		if($type==3) $table_display="display:none"; else $table_display="";
		ob_start();
		?>
        <fieldset style="width:100%">
            <table cellpadding="0" cellspacing="0" width="<? echo $table_width; ?>" style=" <?php echo $table_display; ?> ">
                <tr>
                   <td align="center" width="100%" colspan="<? echo $colspan+44; ?>" style="font-size:16px"><strong><?php echo $company_library[$company_name]; ?></strong></td>
                </tr>
                <tr>
                   <td align="center" width="100%" colspan="<? echo $colspan+44; ?>" style="font-size:16px"><strong><? if($start_date!="" && $end_date!="") echo "From ". change_date_format($start_date). " To ". change_date_format($end_date);?></strong></td>
                </tr>
            </table>
            <table class="rpt_table" border="1" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="table_header_1" style=" <?php echo $table_display; ?> ">
                <thead>
                    <tr>
                        <th rowspan="2" width="40">SL</th>
                        <th colspan="<? echo $colspan; ?>">Order Details</th>
						<?
						if($type==1 )
						{
						?>
                        <th colspan="10" style="background:#FFFF66">Yarn Status</th>
						<? }else{ ?>
							<th colspan="9" style="background:#FFFF66">Yarn Status</th>
						<? }?>
                        <th colspan="5">Knitting Production</th>
                        <th colspan="8" style="background:#00FFFF">Grey Fabric Status</th>
                        <th colspan="7">Deying Production</th>
                        <th colspan="5" style="background:#00FF00">Finish Fabric Production</th>
                        <th colspan="9">Finish Fabric Store</th>
                        <th rowspan="2" style="background:#1E90FF" width="100">Remarks</th>
                        <th rowspan="2">Fabric Description</th>
                    </tr>
                    <tr>
                        <th width="125">Main Fabric Booking No</th>
                        <th width="125">Sample Fabric Booking No</th>
                        <th width="75">Booking No</th>
                        <th width="100">Job Number</th>
                        <th width="40">Img</th>
                        <th width="120">Order Number</th>
                        <th width="90">Order Status</th>
                        <th width="80">Buyer Name</th>
                        <th width="130">Style Ref.</th>
                        <th width="100">File No.</th>
                        <th width="100">Internal Ref.</th>
                        <th width="140">Item Name</th>
                        <th width="100">Order Qnty</th>
                        <th width="80">Shipment Date</th>
                        <?
						if($type==1  || $type==3)
						{
						?>
                            <th width="80">PO Received Date</th>
                            <th width="80">PO Entry Date</th>
                            <th width="100">Shipping Status
                                <select name="cbo_shipping_status" id="cbo_shipping_status" class="combo_boxes" style="width:85%" onChange="fn_report_generated(2);">
                                    <?
                                    foreach($shipment_status as $key=>$value)
                                    {
                                    ?>

                                        <option value=<? echo $key; if ($key==$cbo_shipping_status){?> selected <?php }?>><? echo "$value" ?> </option>
                                    <?
                                    }
                                    ?>
                                </select>
                            </th>
                        <?
						}
						?>
                        <th width="80">Country Ship Date</th>
                        <th width="100">Country Ship Qty.</th>
                        <th width="100" title="Total Grey Req. Qty/ Plancut Qty. (Pcs.)">Avg Grey Cons./Pcs</th>
                        <th width="100" title="Total Fin. Req. Qty/ Plancut Qty. (Pcs.)">Avg. Finish Cons./Pcs</th>
                        <th width="70">Count</th>
                        <th width="110">Composition</th>
                        <th width="80">Type</th>
                        <th width="100">Required<br/><font style="font-size:9px; font-weight:100">(As Per Pre-Cost)</font></th>
                        <th width="100">Allocated</th>
						<?
						if($type==1)
						{
						?>
						 <th width="100">Auto Allocated</th>
						<? } ?>
                        <th width="100">Yet to Allocate</th>
                        <th width="100">Issued</th>
                        <th width="100">Net Transfer</th>
                        <th width="100">Balance<br/><font style="font-size:9px; font-weight:100">(Grey Req-(Yarn Issue+Net Transfer))</font></th>

                        <th width="100">Required<br/><font style="font-size:9px; font-weight:100">(As Per Booking)</font></th>
                        <th width="100">Knitting Production</th>
                        <th width="100">Knitting Balance<br/><font style="font-size:9px; font-weight:100">(Grey Req-Prod)</font></th>
                        <th width="100">Grey Fab Delv. To Store</th>
                        <th width="100">Grey in Knit Floor</th>

                        <th width="100">Net Grey Recv.-Production</th>
                        <th width="100">Net Grey Recv.-Purchase</th>
                        <th width="100">Net Return</th>
                        <th width="100">Net Transfer</th>
                        <th width="100" title="G. Prod. Qty + G. Purchase Qty + Net Trans Knit">Grey Actual Recv.</th>
                        <th width="100" title="Fabric Req. - Fabric Available">Receive Balance</th>
                        <th width="100">Net Grey Issue</th>
                        <th width="100" title="Grey Actual Recv.-Net Grey Issue">Grey In Hand</th>

                        <th width="100">Receive By Batch</th>
                        <th width="100">Fabric Color
                        	<input type="text" name="txt_fab_color" onKeyUp="show_inner_filter(event);" value="<? echo str_replace("'","",$txt_fab_color); ?>" id="txt_fab_color" class="text_boxes" style="width:85px" />
                        </th>
                        <th width="100">Grey Req.<br/><font style="font-size:9px; font-weight:100">(As Per Booking)</font></th>
                        <th width="100">Batch Qty</th>
                        <th width="100">Dye Qnty</th>
                        <th width="100">Grey Balance</th>
                        <th width="100">Balance Qty</th>

                        <th width="100">Req. Qty (As Per Booking)</th>
                        <th width="100">Production Qty</th>
                        <th width="100">Balance Qty</th>
                        <th width="100">Finish Fab. Delv. To Store</th>
                        <th width="100">Fabric in Prod. Floor</th>

                        <th width="100">Net Received - Prod.</th>
                        <th width="100">Net Received - Purchase</th>
                        <th width="100">Net Return</th>
                        <th width="100">Net Transfer</th>
                        <th width="100" title="F. Prod. Qty + F. Purchase Qty + F. Net Transt">Fin. Fab. Actual Recv.</th>
                        <th width="100">Receive Balance</th>
                        <th width="100">Net Issue to Cutting</th>
                        <th width="100">Yet to Issue</th>
                        <th width="100" title="Fin. Fab. Actual Recv.-Net Issue to Cutting">Fabric Stock/ Left Over (In Hand)</th>
                    </tr>
                </thead>
            </table>
            <?
			$html="";
			$colspan_excel=$colspan+40;
			$html="<table>
						 <tr>
							<th colspan='$colspan_excel' align='center'>".$company_library[$company_name]."</th>
						 </tr>";
			if($start_date!="" && $end_date!="")
			{
				$html.="<tr>
							<th colspan='$colspan_excel' align='center'>From ". change_date_format($start_date). " To ". change_date_format($end_date)."</th>
						</tr>";
			}
			$html.="</table><table border='1' rules='all'>
					<thead>
						<tr>
							<th rowspan='2' width='40'>SL</th>
							<th colspan='$colspan'>Order Details</th>";

						if($type==1)
						{
							$html.="<th colspan='10'>Yarn Status</th>";
						}
						else
						{
							$html.="<th colspan='9'>Yarn Status</th>";
						}


						$html.="<th colspan='5'>Knitting Production</th>
							<th colspan='8'>Grey Fabric Store</th>
							<th colspan='7'>Deying Production</th>
							<th colspan='6'>Finish Fabric Production</th>
							<th colspan='9'>Finish Fabric Store</th>
							<th rowspan='2'>Remarks</th>
							<th rowspan='2'>Fabric Description</th>
						</tr>
						<tr>
							<th>Main Fabric Booking No</th>
							<th>Sample Fabric Booking No</th>
							<th>Booking No</th>
							<th>Job Number</th>
							<th>Img</th>
							<th>Order Number</th>
							<th>Order Status</th>
							<th>Buyer Name</th>
							<th>Style Ref.</th>
							<th>File No.</th>
							<th>Internal Ref.</th>
							<th>Item Name</th>
							<th>Order Qnty</th>
							<th>Shipment Date</th>";

			if($type==1  || $type==3)
			{
				$html.="<th>PO Received Date</th>
						<th>Po Entry Date</th>
						<th>Shipping Status</th>";
			}

			$html.="
					<th>Country Ship Date</th>
					<th>Country Ship Qty.</th>
					<th>Avg Grey Cons./Pcs</th>
					<th>Avg. Finish Cons./Pcs</th>
					<th>Count</th>
					<th>Composition</th>
					<th>Type</th>
					<th>Required<br/><font style='font-size:9px; font-weight:100'>(As Per Pre-Cost)</font></th>
					<th>Allocated</th>";
				if($type==1)
				{
					$html.="<th>Auto Allocated</th>";
				}
			$html.="<th>Yet to Allocate</th>
					<th>Issued</th>
					<th>Net Transfer</th>
					<th>Balance<br/><font style='font-size:9px; font-weight:100'>(Grey Req-(Yarn Issue+Net Transfer))</font></th>

					<th>Required<br/><font style='font-size:9px; font-weight:100'>(As Per Booking)</font></th>
					<th>Knitting Production</th>
					<th>Knitting Balance<br/><font style='font-size:9px; font-weight:100'>(Grey Req-Prod)</font></th>
					<th>Grey Fab Delv. To Store</th>
					<th>Grey in Knit Floor</th>

					<th>Net Grey Recv.-Production</th>
					<th>Net Grey Recv.-Purchase</th>
					<th>Net Return</th>
					<th>Net Transfer</th>
					<th>Grey Actual Recv.</th>
					<th>Receive Balance</th>
					<th>Grey Issue</th>
					<th>Grey In Hand</th>

					<th>Receive By Batch</th>
					<th>Fabric Color</th>
					<th>Grey Req.</th>
					<th>Batch Qty</th>
					<th>Dye Qty</th>
					<th>Grey Balance</th>
					<th>Balance Qty</th>

					<th>Req. Qty (As Per Booking)</th>
					<th>Production Qty</th>
					<th>Balance Qty</th>
					<th>Finish Fab. Delv. To Store</th>
					<th>Fabric in Prod. Floor</th>

					<th>Net Received - Prod.</th>
					<th>Net Received - Purchase</th>
					<th>Net Return</th>
					<th>Net Transfer</th>
					<th>Fin. Fab. Actual Recv.</th>
					<th>Receive Balance</th>
					<th>Issue to Cutting </th>
					<th>Yet to Issue</th>
					<th>Fabric Stock/ Left Over (In Hand)</th>
				</tr>
			</thead>";

			$html_short="<table width='1620'>
							 <tr>
								<th colspan='17' align='center'>".$company_library[$company_name]."</th>
							 </tr>
							 <tr>
								<th colspan='17' align='center'>From ". change_date_format($start_date). " To ". change_date_format($end_date)."</th>
							 </tr>
						</table>
						<table class='rpt_table' border='1' rules='all' width='100%'>
							<thead>
								<th>SL</th>
								<th>Main Fabric<br/> Booking No</th>
								<th>Sample Fabric<br/> Booking No</th>
								<th>Booking No</th>
								<th>Order Number</th>
								<th>Buyer Name</th>
								<th>File No</th>
								<th>Ref No</th>
								<th>Order Qnty.</th>
								<th>Shipment Date</th>
								<th>Yarn Issue</th>
								<th>Grey Req<br/> (As per Booking)</th>
								<th>Grey Knitted</th>
								<th>Fabric Color</th>
								<th>Dyeing Qnty</th>
								<th>Finish Fabric Qnty</th>
								<th>Issue to Cutting</th>
							</thead>";


			$html_medium="<table width='1780'>
							 <tr>
								<th colspan='18' align='center'>".$company_library[$company_name]."</th>
							 </tr>
							 <tr>
								<th colspan='18' align='center'>From ". change_date_format($start_date). " To ". change_date_format($end_date)."</th>
							 </tr>
						</table>
						<table class='rpt_table' border='1' rules='all' width='100%'>
							<thead>
								<th>SL</th>
								<th>Buyer Name</th>
								<th>Main Fabric<br/> Booking No</th>
								<th>Sample Fabric<br/> Booking No</th>
								<th>Booking No</th>
								<th>Order Number</th>
								<th>Ref No</th>
								<th>File No</th>
								<th>Style Ref No</th>
								<th>Order Qnty.</th>
								<th>Shipment Date</th>
								<th>Order Lead Time</th>
								<th>Yarn Issue</th>
								<th>Yarn Issue<br/> Balance</th>
								<th>Grey Req<br/> (As per Booking)</th>
								<th>Knitting<br/> Complete</th>
								<th>Knitting Balance</th>
								<th>Fabric Color</th>
								<th>Dyeing Complete</th>
								<th>Dyeing Balance</th>
								<th>Finish Fabric<br/>Delivery</th>
								<th>Finish Delivery<br/>Balance</th>
								<th>Issue to Cutting</th>
							</thead>";
			?>
            <div style="width:<? echo $table_width+20; ?>px; overflow-y:scroll; max-height:400px; <?php echo $table_display; ?>" id="scroll_body">
                <table width="<? echo $table_width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body" >
                <?
				//$nameArray=sql_select($sql);

				$condition= new condition();
				$condition->company_name("=$company_name");
				if(str_replace("'","",$cbo_buyer_name)>0){
					$condition->buyer_name("=$cbo_buyer_name");
				}

				if(trim($txt_search_string)!="")
				{
					if($cbo_type==1) $condition->po_number("='$txt_search_string'");
					else if($cbo_type==2) $condition->style_ref_no("='$txt_search_string'");
					else if($cbo_type==3) $condition->file_no("='$txt_search_string'");
					else if($cbo_type==4) $condition->grouping("='$txt_search_string'");
				}

				if(str_replace("'","",$txt_job_no) !=''){
					$condition->job_no_prefix_num("=$txt_job_no");
				}
			if(trim($txt_search_string)=="" && $start_date!="")
				{
					if($date_type==1)
					{
						if($start_date!="" && $end_date!="") $condition->country_ship_date(" between '$start_date' and '$end_date'");
					}
					else if($date_type==2)
					{
							if ($start_date!="" && $end_date!="") $condition->pub_shipment_date(" between '$start_date' and '$end_date'");
						//if ($start_date=="" && $end_date=="") $condition->po_received_date(" between '$start_date' and '$end_date'");
					}
					else if($date_type==3)
					{
						if ($start_date=="" && $end_date=="") $condition->po_received_date(" between '$start_date' and '$end_date'");
					}
					else if($date_type==4)
					{
						if ($start_date=="" && $end_date=="") $condition->pub_shipment_date(" between '$start_date' and '$end_date'");
					}
				}

				$condition->init();
				$yarn= new yarn($condition);
				$yarn_qty_arr=$yarn->getOrderCountCompositionColorAndTypeWiseYarnQtyArray();
				//$yarn_qty_arr_job=$yarn->getJobWiseYarnQtyArray();

				if($type==1 || $type==3)
				{
					$yarn= new yarn($condition);
					//echo $yarn->getQuery(); die;
					$yarn_des_data=$yarn->getOrderCountCompositionPercentAndTypeWiseYarnQtyArray();
				}
				else if ($type==2)
				{
					$yarn= new yarn($condition);
					//echo $yarn->getQuery(); die;
					$yarn_des_data_job=$yarn->getJobCountCompositionPercentAndTypeWiseYarnQtyArray();
				}

				$fabric= new fabric($condition);
				//echo $fabric->getQuery(); die;
				$fabric_costing_arr=$fabric->getQtyArray_by_order_knitAndwoven_greyAndfinish();

				//die;
				$k=1; $i=1;
				if($type==1 || $type==3)
				{
					if ($chk_no_boking == 1) // check no booking
					{

						foreach($po_data_arr as $po_id=>$other_data)
						{

							$nobooking_check = array_filter(explode(",", substr($dataArrayWo[$po_id], 0, -1)));
                            if (count($nobooking_check)< 1)
							{

								$ex_data=explode('##',$other_data);
								$company_id=''; $buyer_name='';  $job_no_prefix_num=''; $job_no=''; $style_ref_no=''; $gmts_item_id=''; $order_uom=''; $ratio=''; $grouping=''; $file_no=''; $po_number=''; $po_qnty=''; $pub_shipment_date=''; $shiping_status=''; $insert_date=''; $po_received_date=''; $plan_cut=''; $is_confirmed='';// $po_id
								$company_id=$ex_data[0];
								$buyer_name=$ex_data[1];
								$job_no_prefix_num=$ex_data[2];
								$job_no=$ex_data[3];
								$style_ref_no=$ex_data[4];
								$gmts_item_id=$ex_data[5];
								$order_uom=$ex_data[6];
								$ratio=$ex_data[7];
								$grouping=$ex_data[8];
								$file_no=$ex_data[9];
								$po_number=$ex_data[10];
								$po_qnty=$ex_data[11];
								$pub_shipment_date=$ex_data[12];
								$shiping_status=$ex_data[13];
								$insert_date=$ex_data[14];
								$po_received_date=$ex_data[15];
								$plan_cut=$ex_data[16];
								$is_confirmed=$ex_data[17];

								$template_id=$template_id_arr[$po_id];

								$order_qnty_in_pcs=$po_qnty*$ratio;
								$plan_cut_qnty=$plan_cut*$ratio;
								$order_qty_array[$buyer_name]+=$order_qnty_in_pcs;
								$gmts_item='';
								$gmts_item_id=explode(",",$gmts_item_id);
								foreach($gmts_item_id as $item_id)
								{
									if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
								}

								$dzn_qnty=0; $balance=0; $job_mkt_required=0; $yarn_issued=0;
								if($costing_per_id_library[$job_no]==1) $dzn_qnty=12;
								else if($costing_per_id_library[$job_no]==3) $dzn_qnty=12*2;
								else if($costing_per_id_library[$job_no]==4) $dzn_qnty=12*3;
								else if($costing_per_id_library[$job_no]==5) $dzn_qnty=12*4;
								else $dzn_qnty=1;

								$dzn_qnty=$dzn_qnty*$ratio;

								$yarn_data_array=array(); $mkt_required_array=array(); $yarn_allocation_arr=array(); $yetTo_allocate_arr=array(); $req_for_allocate_arr=array(); $yarn_desc_array_for_popup=array(); $yarn_desc_array=array(); $yarn_iss_qnty_array=array();
								$s=1;

								$yarn_descrip_data=$yarn_des_data[$po_id];
								//print_r($yarn_des_data[$po_id]);
								$qnty=0;
								foreach($yarn_descrip_data as $count=>$count_value)
								{
									foreach($count_value as $Composition=>$composition_value)
									{
										foreach($composition_value as $percent=>$percent_value)
										{
											foreach($percent_value as $type_ref=>$type_value)
											{

												$count_id=$count;//$yarnRow[0];
												$copm_one_id=$Composition;//$yarnRow[1];
												$percent_one=$percent;//$yarnRow[2];
												$type_id=$type_ref;//$yarnRow[5];
												$qnty=$type_value;//$yarnRow[6];

												$mkt_required=$qnty;//$plan_cut_qnty*($qnty/$dzn_qnty);
												$mkt_required_array[$s]=$mkt_required;

												$job_mkt_required+=$mkt_required;

												$yarn_data_array['count'][$s]=$yarn_count_details[$count_id];
												$yarn_data_array['type'][$s]=$yarn_type[$type_id];

												//$compos=$composition[$copm_one_id]." ".$percent_one."%".$composition[$copm_two_id];
												$compos=$composition[$copm_one_id]." ".$percent_one." %"." ".$composition[$copm_two_id];

												$yarn_data_array['comp'][]=$compos;

												$yarn_desc_array[$s]=$yarn_count_details[$count_id]." ".$compos." ".$yarn_type[$type_id];
												$des_for_allocation=$yarn_count_details[$count_id]." ".$compos." ".$yarn_type[$type_id];

												$req_for_allocate_arr[$des_for_allocation]=$mkt_required;

												$yarn_desc_for_popup=$count_id."__".$copm_one_id."__".$percent_one."__".$copm_two_id."__".$percent_two."__".$type_id;
												$yarn_desc_array_for_popup[$s]=$yarn_desc_for_popup;

												$s++;
											}
										}
									}
								}

								$dataYarnIssue=explode(",",substr($dataArrayYarnIssue[$po_id],0,-1));
								foreach($dataYarnIssue as $yarnIssueRow)
								{
									$yarnIssueRow=explode("**",$yarnIssueRow);
									$yarn_count_id=$yarnIssueRow[0];
									$yarn_comp_type1st=$yarnIssueRow[1];
									$yarn_comp_percent1st=$yarnIssueRow[2];
									$yarn_comp_type2nd=$yarnIssueRow[3];
									$yarn_comp_percent2nd=$yarnIssueRow[4];
									$yarn_type_id=$yarnIssueRow[5];
									$issue_qnty=$yarnIssueRow[6];
									$return_qnty=$yarnIssueRow[7];

									if($yarn_comp_percent2nd!=0)
									{
										$compostion_not_req=$composition[$yarn_comp_type1st]." ".$yarn_comp_percent1st." % ".$composition[$yarn_comp_type2nd]." ".$yarn_comp_percent2nd." %";
									}
									else
									{
										$compostion_not_req=$composition[$yarn_comp_type1st]." ".$yarn_comp_percent1st." % ".$composition[$yarn_comp_type2nd];
									}

									$desc=$yarn_count_details[$yarn_count_id]." ".$compostion_not_req." ".$yarn_type[$yarn_type_id];

									$net_issue_qnty=$issue_qnty-$return_qnty;

									$fab_source=rtrim($fab_source_arr[$po_id],',');
									$fab_source_id=array_unique(explode(",",$fab_source));
									foreach($fab_source_id as $fsid)
									{
										if($fsid==1)
										{
											 $net_issue_qnty=$net_issue_qnty;
										}
									}

									$yarn_issued+=$net_issue_qnty;
									if(!in_array($desc,$yarn_desc_array))
									{
										$yarn_iss_qnty_array['not_req']+=$net_issue_qnty;
									}
									else
									{
										$yarn_iss_qnty_array[$desc]+=$net_issue_qnty;
									}
								}

								$grey_rec_rtn_purchase=$grey_receive_return_qnty_arr[$po_id]['purchase'];
								$grey_rec_rtn_production=$grey_receive_return_qnty_arr[$po_id]['production'];

								$grey_purchase_qnty=$greyPurchaseQntyArray[$po_id]['purchase']-$grey_rec_rtn_purchase;
								$grey_production_qnty=$greyPurchaseQntyArray[$po_id]['production']-$grey_rec_rtn_production;

								$grey_issue_rtn=$grey_issue_return_qnty_arr[$po_id];
								$grey_rec_rtn=$grey_rec_rtn_purchase+$grey_rec_rtn_production;

								$grey_net_return=$grey_issue_rtn-$grey_rec_rtn;
								$grey_recv_qnty=$grey_receive_qnty_arr[$po_id];

								$grey_fabric_issue=$grey_issue_qnty_arr[$po_id]-$grey_issue_rtn;
								$receive_by_batch_qnt=$receive_by_batch_data[$po_id];

								$grey_available=0;
								$grey_available=$grey_production_qnty+$grey_purchase_qnty+$net_trans_knit;

								$grey_in_hand=$grey_available-$grey_fabric_issue;

								$fab_source=rtrim($fab_source_arr[$po_id],',');
								$fab_source_id=array_unique(explode(",",$fab_source));
								rsort($fab_source_id);
								foreach($fab_source_id as $fsid)
								{
									if($fsid==1)
									{
										$grey_recv_qnty=$grey_recv_qnty;$grey_production_qnty=$grey_production_qnty;$grey_purchase_qnty=$grey_purchase_qnty;
										$grey_net_return=$grey_net_return;$grey_fabric_issue=$grey_fabric_issue;$receive_by_batch_qnt=$receive_by_batch_qnt;
									}
									else
									{
										//$grey_recv_qnty=0;$grey_production_qnty=0;$grey_purchase_qnty=0;$grey_net_return=$receive_by_batch_qnt=$grey_fabric_issue=0;
									}
								}

								$contry_ship_date=""; $country_ship_qty=0;
								//$country_date_all=$contry_ship_qty_arr[$po_id]['ship_date'];
								//print_r();
								$country_date_all=array_filter(array_unique(explode(',',$contry_ship_qty_arr[$po_id]['ship_date'])));
								//print_r($country_date_all);
								foreach($country_date_all as $date_all)
								{
									//echo $date_all.'<br>';
									if($date_all!='')
									{
										if($contry_ship_date=="") $contry_ship_date=change_date_format($date_all); else $contry_ship_date.=',<br>'.change_date_format($date_all);
										//if($contry_ship_date=="") $contry_ship_date=$date_all; else $contry_ship_date.=',<br>'.$date_all;
									}
								}
								//die;
								$country_ship_qty=$contry_ship_qty_arr[$po_id]['ship_qty'];
								$tot_country_ship_qty+=$country_ship_qty;
								$country_order_qty_array[$buyer_name]+=$country_ship_qty;

								if(($cbo_discrepancy==1 && $grey_recv_qnty>$yarn_issued) || ($cbo_discrepancy==0))
								{
									if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									$buyer_name_array[$buyer_name]=$buyer_short_name_library[$buyer_name];

									$booking_array=array(); $color_data_array=array(); $grey_req_color_arr=array(); $fabric_source_arr=array();
									$required_qnty=0; $main_booking=''; $sample_booking=''; $main_booking_excel=''; $sample_booking_excel='';  $all_book_prefix_no = '';
									$dataArray=array_filter(explode("__",$dataArrayWo[$po_id]));
									if(count($dataArray)>0)
									{
										foreach($dataArray as $woRow)
										{
											$woRow=explode("**",$woRow);
											$id=$woRow[0];
											$booking_no=$woRow[1];
											$insert_date=$woRow[2];
											$item_category=$woRow[3];
											$fabric_source=$woRow[4];
											$company_id=$woRow[5];
											$booking_type=$woRow[6];
											$booking_no_prefix_num=$woRow[7];
											$job_no=$woRow[8];
											$is_short=$woRow[9];
											$is_approved=$woRow[10];
											$fabric_color_id=$woRow[11];
											$req_qnty=$woRow[12];
											$grey_req_qnty=$woRow[13];
											$wo_po_id=$woRow[14];
											$book_prefix_no=$woRow[7];
											if($fabric_source==1)
											{
												$grey_req_qnty=$grey_req_qnty; $req_qnty=$req_qnty;
											}
											else
											{
												//$grey_req_qnty=$req_qnty=0;
											}
											$required_qnty+=$grey_req_qnty;

											if(!in_array($id,$booking_array))
											{
												if($bookingEntryFromArr[$booking_no]==86)
												{
													$entryForm=$bookingEntryFromArr[$booking_no];
													$reportFormat=explode(",",$print_report_format_budget_booking[0]);
													$reportFormat=$reportFormat[0];
													$action_name=$report_format_arr[$reportFormat];
												}
												$system_date=date('d-M-Y', strtotime($insert_date));
												if ($fabric_source == 2) $wo_color = "color='color:#000'"; else $wo_color = "";

												if($booking_type==4)
												{
													if($entryForm==86)
													{
														$action_name=$action_namez;
													}
													else{
														//$action_name='show_fabric_booking_report';
														$action_name=$report_format_sample_arr[$reportFormatSample];
													}
													$sample_booking.="<a href='##' style='color:#000' onclick=\"generate_worder_report('3','".$booking_no."','".$company_id."','".$wo_po_id."','".$item_category."','".$fabric_source."','".$job_no."','".$is_approved."','".$action_name."','".$print_report_format."','".$entryForm."')\"><font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")"."</font></a><br>";
													$sample_booking_excel.="<font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")"."</font><br>";
												}
												else
												{
													 $all_book_prefix_no .= $book_prefix_no . ",";
													if($is_short==1)
													{
														$pre="S";
														$action_name=$report_format_arr[$booking_print_arr[2]];
													}
													else
													{
														$pre="M";
														$action_name=$report_format_arr[$booking_print_arr[1]];
													}
													if($entryForm==86)
													{
														$action_name=$action_namez;
													}
													else if ($action_name=='') {
														$action_name='show_fabric_booking_report';
													}
													//if($is_short==1) $pre="S"; else $pre="M";

													$main_booking.="<a href='##' style='color:#000' onclick=\"generate_worder_report('".$is_short."','".$booking_no."','".$company_id."','".$wo_po_id."','".$item_category."','".$fabric_source."','".$job_no."','".$is_approved."','".$action_name."','".$print_report_format."','".$entryForm."')\"><font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")".$pre."</font></a><br>";
													$main_booking_excel.="<font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")".$pre."</font><br>";
												}

												$booking_array[]=$id;
											}
											$color_data_array[$fabric_color_id]+=$req_qnty;
											$grey_req_color_arr[$fabric_color_id]+=$grey_req_qnty;
										}

									}
									else
									{
										$main_booking.="No Booking";
										$main_booking_excel.="No Booking";
										$sample_booking.="No Booking";
										$sample_booking_excel.="No Booking";
										$all_book_prefix_no = "&nbsp;";
									}

									if($main_booking=="")
									{
										$main_booking.="No Booking";
										$main_booking_excel.="No Booking";
									}

									if($sample_booking=="")
									{
										$sample_booking.="No Booking";
										$sample_booking_excel.="No Booking";
									}

									$all_book_prefix_no = implode(",", array_unique(explode(",", chop($all_book_prefix_no, ","))));

									$finish_color=array_unique(explode(",",$po_color_arr[$po_id]));
									foreach($finish_color as $color_id)
									{
										if($color_id>0)
										{
											$color_data_array[$color_id]+=0;
										}
									}
									//var_dump($color_data_array);
									$yarn_issue_array[$buyer_name]+=$yarn_issued;

									$grey_required_array[$buyer_name]+=$required_qnty;

									$net_trans_yarn=$trans_qnty_arr[$po_id]['yarn_trans'];

									$yarn_issue_array[$buyer_name]+=$net_trans_yarn;

									//$balance=$mkt_required_value-($yarn_issued+$net_trans_yarn);
									$fab_source=rtrim($fab_source_arr[$po_id],',');
									$fab_source_id=array_unique(explode(",",$fab_source));
									foreach($fab_source_id as $fsid)
									{
										if($fsid==1)
										{
											 $balance=$required_qnty-($yarn_issued+$net_trans_yarn);
										}
									}
									//$yetTo_allocate=$balance-$yarnAllocationQty;

									$dataYarnAllocation=explode(",",substr($yarnAllocationArr[$po_id],0,-1));
									$job_yetTo_allocate=0; $job_yarnAllocationQty=0; $yetTo_allocate=0; $yarnAllocationQty=0;

									foreach($dataYarnAllocation as $yarnAllRow)
									{
										$yarnAlloRow=explode("**",$yarnAllRow);
										$yarn_count_id=$yarnAlloRow[0];
										$yarn_comp_type1st=$yarnAlloRow[1];
										$yarn_comp_percent1st=$yarnAlloRow[2];
										$yarn_comp_type2nd=$yarnAlloRow[3];
										$yarn_comp_percent2nd=$yarnAlloRow[4];
										$yarn_type_id=$yarnAlloRow[5];
										$yarnAllocationQty=$yarnAlloRow[6];
										$fab_source=rtrim($fab_source_arr[$po_id],',');
										$fab_source_id=array_unique(explode(",",$fab_source));

										foreach($fab_source_id as $fsid)
										{
											if($fsid==1)
											{
											if($yarn_comp_percent2nd!=0)
											{
												$compostion_not_req=$composition[$yarn_comp_type1st]." ".$yarn_comp_percent1st." % ".$composition[$yarn_comp_type2nd]." ".$yarn_comp_percent2nd." %";
											}
											else
											{
												$compostion_not_req=$composition[$yarn_comp_type1st]." ".$yarn_comp_percent1st." % ".$composition[$yarn_comp_type2nd];
											}

											$desc=$yarn_count_details[$yarn_count_id]." ".$compostion_not_req." ".$yarn_type[$yarn_type_id];
											$req_allocation=$req_for_allocate_arr[$desc];

											$job_yarnAllocationQty+=$yarnAllocationQty;
											//$yetTo_allocate=$required_qnty-$job_yarnAllocationQty;
											//$job_yetTo_allocate+=$yetTo_allocate;

											if(!in_array($desc,$yarn_desc_array))
											{
												$yarn_allocation_arr['not_req']+=$yarnAllocationQty;

												//$yetTo_allocate+=$req_for_allocate_arr[$desc]-$yarnAllocationQty;
											}
											else
											{
												$yarn_allocation_arr[$desc]+=$yarnAllocationQty;
												//$yetTo_allocate+=$req_for_allocate_arr[$desc]-$yarnAllocationQty;
											}
										  }
										}
									}

									//$yarnAllocationArr

									$yarn_balance_array[$buyer_name]+=$balance;


									$net_trans_knit=$trans_qnty_arr[$po_id]['knit_trans'];
									//$knitted_array[$buyer_name]+=$net_trans_knit;


									$grey_balance=$required_qnty-$grey_available;//-($grey_recv_qnty+$net_trans_knit+$grey_purchase_qnty);
									$grey_prod_balance=$required_qnty-$grey_recv_qnty;
									$grey_del_store=$greyDeliveryArray[$po_id];
									$fab_source=rtrim($fab_source_arr[$po_id],',');
									$fab_source_id=array_unique(explode(",",$fab_source));
									rsort($fab_source_id);
									foreach($fab_source_id as $fsid)
									{
										if($fsid==1)
										{
											$grey_del_store=$grey_del_store;$net_trans_knit=$net_trans_knit;
										}
										else
										{ //$grey_del_store=$net_trans_knit=0;
										}
									}
									$total_grey_del_store+=$grey_del_store;

									$grey_balance_array[$buyer_name]+=$grey_balance;

									$grey_issue_array[$buyer_name]+=$grey_fabric_issue;
									$receive_by_batch_array[$buyer_name]+=$receive_by_batch_qnt;

									$tot_order_qnty+=$order_qnty_in_pcs;
									$tot_mkt_required+=$job_mkt_required;

									$tot_yarnAllocationQty+=$job_yarnAllocationQty;


									$tot_yarn_issue_qnty+=$yarn_issued;
									$tot_fabric_req+=$required_qnty;
									$tot_balance+=$balance;
									$tot_grey_recv_qnty+=$grey_recv_qnty;
									$tot_grey_production_qnty+=$grey_production_qnty;
									$tot_grey_purchase_qnty+=$grey_purchase_qnty;
									$tot_grey_balance+=$grey_balance;
									$tot_grey_prod_balance+=$grey_prod_balance;
									$tot_grey_issue+=$grey_fabric_issue;
									$tot_receive_by_batch+=$receive_by_batch_qnt;

									$tot_grey_available+=$grey_available;
									//$required_qnty;
									$yarn_iss_plan_date_fin=""; $yarn_iss_actual_date_fin="";
									$yarn_iss_plan_date_fin=$tna_plan_actual_arr[$po_id][50]['plan'];
									$yarn_iss_actual_date_fin=$tna_plan_actual_arr[$po_id][50]['actual'];

									$gray_prod_plan_date_fin=""; $gray_prod_actual_date_fin="";
									$gray_prod_plan_date_fin=$tna_plan_actual_arr[$po_id][60]['plan'];
									$gray_prod_actual_date_fin=$tna_plan_actual_arr[$po_id][60]['actual'];

									$dye_prod_plan_date_fin=""; $dye_prod_actual_date_fin="";
									$dye_prod_plan_date_fin=$tna_plan_actual_arr[$po_id][61]['plan'];
									$dye_prod_actual_date_fin=$tna_plan_actual_arr[$po_id][61]['actual'];

									$fin_fab_plan_date_fin=""; $fin_fab_actual_date_fin="";
									$fin_fab_plan_date_fin=$tna_plan_actual_arr[$po_id][73]['plan'];
									$fin_fab_actual_date_fin=$tna_plan_actual_arr[$po_id][73]['actual'];

									$yarn_color_td="";
									if($yarn_iss_plan_date_fin<$yarn_iss_actual_date_fin) $yarn_color_td='#FF0000';


									$current_date=date("Y-m-d");
									if($gray_prod_plan_date_fin=="" || $gray_prod_plan_date_fin=="0000-00-00") $gray_prod_color_td="";
									else if($current_date>$gray_prod_plan_date_fin && ($gray_prod_actual_date_fin=="" || $gray_prod_actual_date_fin=="0000-00-00")) $gray_prod_color_td="#FF0000";
									else if(!($gray_prod_actual_date_fin=="" || $gray_prod_actual_date_fin=="0000-00-00") && $gray_prod_actual_date_fin>$gray_prod_plan_date_fin) $gray_prod_color_td="#33CCFF";
									else if(($gray_prod_actual_date_fin<=$gray_prod_plan_date_fin) && ($gray_prod_plan_date_fin!="" || $gray_prod_plan_date_fin!="0000-00-00")) $gray_prod_color_td="#008000";
									else $gray_prod_color_td="";


									if($dye_prod_plan_date_fin=="" || $dye_prod_plan_date_fin=="0000-00-00") $dye_prod_color_td="";
									else if($current_date>$dye_prod_plan_date_fin && ($dye_prod_actual_date_fin=="" || $dye_prod_actual_date_fin=="0000-00-00")) $dye_prod_color_td="#FF0000";
									else if(!($dye_prod_actual_date_fin=="" || $dye_prod_actual_date_fin=="0000-00-00") && $dye_prod_actual_date_fin>$dye_prod_plan_date_fin) $dye_prod_color_td="#33CCFF";
									else if(($dye_prod_actual_date_fin<=$dye_prod_plan_date_fin) && ($dye_prod_plan_date_fin!="" || $dye_prod_plan_date_fin!="0000-00-00")) $dye_prod_color_td="#008000";
									else $dye_prod_color_td="";
									//echo $dye_prod_color_td;
									if($current_date>$fin_fab_plan_date_fin && ($fin_fab_actual_date_fin=="" || $fin_fab_actual_date_fin=="0000-00-00")) $fin_prod_color_td="#FF0000";
									else if(!($fin_fab_actual_date_fin=="" || $fin_fab_actual_date_fin=="0000-00-00") && $fin_fab_actual_date_fin>$fin_fab_plan_date_fin) $fin_prod_color_td="#33CCFF";
									else if($fin_fab_actual_date_fin<=$fin_fab_plan_date_fin) $fin_prod_color_td="#008000";
									else $fin_prod_color_td="";


									if($required_qnty>$job_mkt_required) $bgcolor_grey_td='#FF0000'; $bgcolor_grey_td='';

									$po_entry_date=date('d-m-Y', strtotime($insert_date));
									$costing_date=$costing_date_library[$job_no];

									$tot_color=count($color_data_array);
									//echo $tot_color.'kkk';
									$grey_cons=0; $fin_cons=0;
									//echo $country_ship_qty.'=='.$po_id;
									$grey_cons=$fabric_costing_arr['knit']['grey'][$po_id]/$plan_cut_qnty;//$reqArr[$job_no]['grey']/$dzn_qnty;
									$fin_cons=$fabric_costing_arr['knit']['finish'][$po_id]/$plan_cut_qnty;//$reqArr[$job_no]['finish']/$dzn_qnty;
									if ($date_type==1 && ($start_date!='' || $end_date!=''))
									{ // Confirm by Rasel bhai
										//Ex-Cut=(Plan Cut Qty-Order Qnty)/Order Qnty*100
										$ex_cut=($plan_cut_qnty-$order_qnty_in_pcs)/$order_qnty_in_pcs*100;

										//Ex-Cut2 =Ex-Cut / 100*Country Ship Qty.
										$ex_cut2 = $ex_cut/100*$country_ship_qty;

										//Ex-Cut3 = Country Ship Qty + Ex-Cut2
										$ex_cut3 = $country_ship_qty+$ex_cut2;

										//$Avg_Grey_Cons/Pcs=Country Ship Qty / Ex-Cut3
										/*$grey_cons = $country_ship_qty/$ex_cut3;
										$title=$country_ship_qty."/".$ex_cut3;*/

										$grey_cons = $fabric_costing_arr['knit']['grey'][$po_id]/$ex_cut3;
										$title=$fabric_costing_arr['knit']['grey'][$po_id]."/".$ex_cut3;

										$fin_cons=$fabric_costing_arr['knit']['finish'][$po_id]/$ex_cut3;
										$titleFin=$fabric_costing_arr['knit']['finish'][$po_id]."/".$ex_cut3;
									}

									if($tot_color>0)
									{
										$z=1;
										foreach($color_data_array as $key=>$value)
										{
											if($z==1)
											{
												$display_font_color="";
												$font_end="";
											}
											else
											{
												$display_font_color="<font style='display:none' color='$bgcolor'>";
												$font_end="</font>";
											}
											$batch_qnty=$batch_qnty_arr[$po_id][$key];
											$fin_delivery_qty=$finDeliveryArray[$po_id][$key];
											$fab_source=rtrim($fab_source_arr[$po_id],',');
											$fab_source_id=array_unique(explode(",",$fab_source));
											foreach($fab_source_id as $fsid)
											{
												if($fsid==1)
												{
													$batch_qnty=$batch_qnty;$fin_delivery_qty=$fin_delivery_qty;
												}
												else
												{
													//$batch_qnty=$fin_delivery_qty=0;
												}
											}
											$batch_qnty_array[$buyer_name]+=$batch_qnty;
											//$tot_batch_qnty+=$batch_qnty;



											if($z==1)
											{

												$html.="<tr bgcolor='".$bgcolor."'>
														<td align='left'>".$i."</td>
														<td align='left'>".$main_booking_excel."</td>
														<td align='left'>".$sample_booking_excel."</td>
														<td align='left'>".$all_book_prefix_no."</td>
														<td align='center'>".$job_no."</td>
														<td align='center'><img  src='../../".$imge_arr[$job_no]."' height='25' width='30' /></td>
														<td align='left'>".$po_number."</td>
														<td align='left'>".$order_status[$is_confirmed]."</td>
														<td>".$buyer_short_name_library[$buyer_name]."</td>
														<td align='left'>".$style_ref_no."</td>
														<td align='left'>".$file_no."</td>
														<td align='left'>".$grouping."</td>
														<td align='left'>".$gmts_item."</td>
														<td align='right'>".$order_qnty_in_pcs."</td>
														<td align='left'>".change_date_format($pub_shipment_date)."</td>
														<td align='center'>".change_date_format($po_received_date)."</td>
														<td align='center'>".$po_entry_date."</td>
														<td>".$shipment_status[$shiping_status]."</td>
														<td align='center'>".$contry_ship_date."</td>
														<td align='right'>".$country_ship_qty."</td>
														<td align='right'>".$grey_cons."</td>
														<td align='right'>".$fin_cons."</td>";
												$lead_time=0;
												$lead_time=datediff('d',$po_received_date,$pub_shipment_date);
												if($lead_time>0) $lead_time=$lead_time-1;

												$html_short.="<tr bgcolor='".$bgcolor."'>
															<td align='left'>".$i."</td>
															<td align='left'>".$main_booking_excel."</td>
															<td align='left'>".$sample_booking_excel."</td>
															<td align='left'>".$all_book_prefix_no."</td>
															<td align='left'>".$po_number."</td>
															<td>".$buyer_short_name_library[$buyer_name]."</td>
															<td>".$file_no."</td>
															<td>".$grouping."</td>
															<td align='right'>".$order_qnty_in_pcs."</td>
															<td align='left'>".change_date_format($pub_shipment_date)."</td>";
												$html_medium.="<tr bgcolor='".$bgcolor."'>
															<td align='left'>".$i."</td>
															<td>".$buyer_short_name_library[$buyer_name]."</td>
															<td align='left'>".$main_booking_excel."</td>
															<td align='left'>".$sample_booking_excel."</td>
															<td align='left'>".$all_book_prefix_no."</td>
															<td align='left'>".$po_number."</td>
															<td align='left'>".$grouping."</td>
															<td align='left'>".$file_no."</td>
															<td align='left'>".$style_ref_no."</td>

															<td align='right'>".$order_qnty_in_pcs."</td>
															<td align='left'>".change_date_format($pub_shipment_date)."</td>
															<td align='left'>".$lead_time."</td>";
											}
											else
											{
												$html.="<tr bgcolor='".$bgcolor."'>
														<td></td>
														<td></td>
														<td></td>
														<td></td>
														<td></td>
														<td></td>
														<td></td>
														<td></td>
														<td></td>
														<td></td>
														<td></td>
														<td></td>
														<td></td>
														<td></td>
														<td></td>
														<td></td>
														<td></td>
														<td></td>
														<td></td>
														<td></td>
														<td></td>
														<td></td>";

												$html_short.="<tr bgcolor='".$bgcolor."'>
																<td></td>
																<td></td>
																<td></td>
																<td></td>
																<td></td>
																<td></td>
																<td></td>
																<td></td>
																<td></td>
																<td></td>";
												$html_medium.="<tr bgcolor='".$bgcolor."'>
																<td></td>
																<td></td>
																<td></td>
																<td></td>
																<td></td>
																<td></td>
																<td></td>
																<td></td>
																<td></td>
																<td></td>
																<td></td>
																<td></td>";
											}
										?>
											<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
												<td width="40"><? echo $display_font_color.$i.$font_end; ?></td>
												<td width="125"><? echo $display_font_color.$main_booking.$font_end; ?></td>
												<td width="125"><? echo $display_font_color.$sample_booking.$font_end; ?></td>
                                                <td width="75" style="word-break:break-all"><? echo $display_font_color.$all_book_prefix_no.$font_end; ?></td>
												<td width="100" align="center"><? echo $display_font_color.$job_no.$font_end; ?></td>
                                                <td width="40" onClick="openmypage_image('requires/fabric_receive_status_report2_controller.php?action=show_image&job_no=<?=$job_no; ?>','Image View')"><?=$display_font_color; ?><img  src="../../<?=$imge_arr[$job_no]; ?>" height='25' width='30' /><?=$font_end; ?></td>
												<td width="120">
													<p>
														<a href='#report_details' onClick="progress_comment_popup('<? echo $job_no; ?>','<? echo $po_id; ?>','<? echo $template_id; ?>','<? echo $tna_process_type; ?>');"><? echo $display_font_color.$po_number.$font_end;  ?></a>
													</p>
												</td>
												<td width="90" align="center"><? echo $display_font_color.$order_status[$is_confirmed].$font_end; ?></td>
												<td width="80"><p><? echo $display_font_color.$buyer_short_name_library[$buyer_name].$font_end; ?></p></td>
												<td width="130"><p><? echo $display_font_color.$style_ref_no.$font_end; ?></p></td>
												<td width="100"><p><? echo $file_no; ?></p></td>
												<td width="100"><p><? echo $grouping; ?></p></td>
												<td width="140"><p><? echo $display_font_color.$gmts_item.$font_end; ?></p></td>
												<td width="100" align="right"><? if($z==1) echo number_format($order_qnty_in_pcs,0,'.',''); ?></td>
												<td width="80" align="center"><? echo $display_font_color.change_date_format($pub_shipment_date).$font_end; ?></td>
												<td width="80" align="center"><? echo $display_font_color.change_date_format($po_received_date).$font_end; ?></td>
												<td width="80" align="center"><? echo $display_font_color.$po_entry_date.$font_end; ?></td>
												<td width="100" align="center"><? echo $display_font_color.$shipment_status[$shiping_status].$font_end; ?></td>
												<td width="80"><p><? echo $display_font_color.$contry_ship_date.$font_end; ?></p></td>

												<? if($country_ship_qty>0)
											   {
												   ?>
												<td width="100" align="right"><a href="##" onClick="country_order_dtls('<? echo $po_id; ?>','<? echo $start_date; ?>','<? echo $end_date; ?>','<? echo $buyer_name; ?>','<? echo $job_no; ?>','country_order_dtls_popup')"><? if($z==1) echo number_format($country_ship_qty); ?></a></td>
												<? }
												else
												{
												?>
												<td width="100" align="right"> <? if($z==1) echo number_format($country_ship_qty); ?></td>
												<? } ?>
												<td width="100" align="right" title="<? echo $title=($date_type==1 && ($start_date!='' || $end_date!='')) ? $title : $fabric_costing_arr['knit']['grey'][$po_id]."/".$plan_cut_qnty; ?>"><? if($z==1) echo number_format($grey_cons,5,'.',''); ?></td>
												<td width="100" align="right" title="<? echo $titleFin=($date_type==1 && ($start_date!='' || $end_date!='')) ? $titleFin : $fabric_costing_arr['knit']['finish'][$po_id]."/".$plan_cut_qnty; ?>"><? if($z==1) echo number_format($fin_cons,5,'.',''); ?></td>

												<td width="70">
													<?
														 $html.="<td>"; $d=1;
														 foreach($yarn_data_array['count'] as $yarn_count_value)
														 {
															if($d!=1)
															{
																echo $display_font_color."<hr/>".$font_end;
																if($z==1) $html.="<hr/>";
															}
															//else $html.="kausar";
															echo $display_font_color.$yarn_count_value.$font_end;
															if($z==1) $html.=$yarn_count_value;
														 $d++;
														 }
														 $html.="</td><td>";
													?>
												</td>
												<td width="110">
													<div style="word-wrap:break-word; width:110px">
														<?
															 $d=1;
															 foreach($yarn_data_array['comp'] as $yarn_composition_value)
															 {
																if($d!=1)
																{
																	echo $display_font_color."<hr/>".$font_end;
																	if($z==1) $html.="<hr/>";
																}
																echo $display_font_color.$yarn_composition_value.$font_end;
																if($z==1) $html.=$yarn_composition_value;
															 $d++;
															 }
															 $html.="</td><td>";
														?>
													</div>
												</td>
												<td width="80">
													<p>
														<?
															 $d=1;
															 foreach($yarn_data_array['type'] as $yarn_type_value)
															 {
																if($d!=1)
																{
																	echo $display_font_color."<hr/>".$font_end;
																	if($z==1) $html.="<hr/>";
																}

																echo $display_font_color.$yarn_type_value.$font_end;
																if($z==1) $html.=$yarn_type_value;
															 $d++;
															 }
															 $html.="</td><td>";
														?>
													</p>
												</td>
												<td width="100" align="right">
													<?
														if($z==1)
														{
															echo "<font color='$bgcolor' style='display:none'>".number_format(array_sum($mkt_required_array),2,'.','')."</font>\n";
															$d=1;
															foreach($mkt_required_array as $mkt_required_value)
															{
																if($d!=1)
																{
																	echo "<hr/>";
																	$html.="<hr/>";
																}
																$yarn_desc_for_popup_req=explode("__",$yarn_desc_array_for_popup[$d]);
																?>
																 <? echo number_format($mkt_required_value,2,'.','');?>
															<?
															$html.=number_format($mkt_required_value,2);
															$d++;
															}
														}

														$html.="</td><td bgcolor='$discrepancy_td_color'>"; $html_short.="<td>"; $html_medium.="<td>";
													?>
												</td>
												<td width="100" align="right">

													<?

														if($z==1)
														{
															echo "<font color='$bgcolor' style='display:none'>".number_format($job_yarnAllocationQty,2,'.','')."</font>\n";
															$d=1;
															foreach($yarn_desc_array as $yarn_desc)
															{
																if($d!=1)
																{
																	echo "<hr/>";
																	$html.="<hr/>";
																}

																$yarn_allo_qnty=$yarn_allocation_arr[$yarn_desc];
																$yarn_desc_for_popup=explode("__",$yarn_desc_array_for_popup[$d]);
																//echo $fab_source_arr[$po_id];
																?>

																<a href="##" onClick="openmypage('<? echo $po_id; ?>','yarn_allocation_pop','<? echo $yarn_desc_for_popup[0]; ?>','<? echo $yarn_desc_for_popup[1]; ?>','<? echo $yarn_desc_for_popup[2]; ?>','<? echo $yarn_desc_for_popup[3]; ?>','<? echo $yarn_desc_for_popup[4]; ?>','<? echo $yarn_desc_for_popup[5]; ?>')"><? echo number_format($yarn_allo_qnty,2,'.','');?></a>
																<?
																$html.=number_format($yarn_allo_qnty,2);
																$d++;
															}

															if($d!=1)
															{
																echo "<hr/>";
																$html.="<hr/>";
															}

															$yarn_desc=join(",",$yarn_desc_array);

															$allo_qnty_not_req=$yarn_allocation_arr['not_req'];

															$html.=number_format($allo_qnty_not_req,2);
															//$html_short.=number_format($iss_qnty_not_req+$yarn_issued,2);
															?>
															<a href="##" onClick="openmypage('<? echo $po_id; ?>','yarn_allocation_not','<? echo $yarn_desc; ?>','','','','','')"><? echo number_format($allo_qnty_not_req,2);?></a>
														<?
														}
														$html.="</td><td>";
													?>
												</td>
												 <td width="100" align="right">
													<?
														if($z==1)
														{
															$job_yetTo_allocate=0;
															$fab_source=rtrim($fab_source_arr[$po_id],',');
															$fab_source_id=array_unique(explode(",",$fab_source));
															foreach($fab_source_id as $fsid)
															{
																if($fsid==1) $job_yetTo_allocate=$required_qnty-$job_yarnAllocationQty;
															}
															echo "<font color='$bgcolor' style='display:none'>".number_format($job_yetTo_allocate,2,'.','')."</font>\n";
															$tot_yetTo_allocate+=$job_yetTo_allocate;
															echo number_format($job_yetTo_allocate,2,'.','');
															$html.=number_format($job_yetTo_allocate,2);
														}
														$html.="</td><td>";
													?>
												</td>
												<td width="100" align="right" bgcolor="<? echo $yarn_color_td; ?>">
													<?
														if($z==1)
														{
															echo "<font color='$bgcolor' style='display:none'>".number_format($yarn_issued,2,'.','')."</font>\n";
															$d=1;
															foreach($yarn_desc_array as $yarn_desc)
															{
																if($d!=1)
																{
																	echo "<hr/>";
																	$html.="<hr/>";
																}

																$yarn_iss_qnty=$yarn_iss_qnty_array[$yarn_desc];
																$yarn_desc_for_popup=explode("__",$yarn_desc_array_for_popup[$d]);

																?>
																<a href="##" onClick="openmypage('<? echo $po_id; ?>','yarn_issue','<? echo $yarn_desc_for_popup[0]; ?>','<? echo $yarn_desc_for_popup[1]; ?>','<? echo $yarn_desc_for_popup[2]; ?>','<? echo $yarn_desc_for_popup[3]; ?>','<? echo $yarn_desc_for_popup[4]; ?>','<? echo $yarn_desc_for_popup[5]; ?>')"><? echo number_format($yarn_iss_qnty,2,'.','');?></a>
																<?
																$html.=number_format($yarn_iss_qnty,2);
																$d++;
															}

															if($d!=1)
															{
																echo "<hr/>";
																$html.="<hr/>";
															}

															$yarn_desc=join(",",$yarn_desc_array);

															$iss_qnty_not_req=$yarn_iss_qnty_array['not_req'];

															$html.=number_format($iss_qnty_not_req,2);
															$html_medium.=number_format($yarn_issued,2);
															$html_short.=number_format(($iss_qnty_not_req+$yarn_iss_qnty),2);//$yarn_issued

															?>
															<a href="##" onClick="openmypage('<? echo $po_id; ?>','yarn_issue_not','<? echo $yarn_desc; ?>','','','','','')"><? echo number_format($iss_qnty_not_req,2);?></a>
														<?
														}
														?>
												</td>
												<? $html.="</td><td>"; ?>
												<td width="100" align="right">

												<?
													if($z==1)
													{
													?>
														<a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $po_id; ?>','yarn_trans','')"><? echo number_format($net_trans_yarn,2,'.','');  ?></a>
													<?
														$html.=number_format($net_trans_yarn,2);
														$tot_net_trans_yarn_qnty+=$net_trans_yarn;
													}
												?>
												</td>
												<? $html.="</td><td>"; $html_medium.="</td><td>"; ?>
												<td width="100" align="right" title="Grey Req.-(Yarn Issue+Net Transfer)">
												<?
													if($z==1)
													{
														echo number_format($balance,2,'.','');
														$html.=number_format($balance,2);
														$html_medium.=number_format($balance,2);
													}
												?>
												</td>
												<? $html.="</td><td bgcolor='$bgcolor_grey_td'>"; $html_short.="</td><td bgcolor='$bgcolor_grey_td'>"; $html_medium.="</td><td bgcolor='$bgcolor_grey_td'>"; ?>
												<td width="100" align="right" bgcolor="<? echo $bgcolor_grey_td; ?>">
												<?
													if($z==1)
													{
														echo number_format($required_qnty,2,'.','');
														$html.=number_format($required_qnty,2);
														$html_short.=number_format($required_qnty,2);
														$html_medium.=number_format($required_qnty,2);
													}
												?>
												</td>
												<? $html.="</td><td bgcolor='$discrepancy_td_color'>"; $html_short.="</td><td>";   $html_medium.="</td><td>"; ?>
												<td width="100" align="right" bgcolor="<? echo $gray_prod_color_td; ?>">
													<?
														if($z==1)
														{
														?>
															<a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $po_id; ?>','grey_receive','')"><? echo number_format($grey_recv_qnty,2,'.',''); ?></a>
														<?
															$html.=number_format($grey_recv_qnty,2);
															$html_short.=number_format($grey_recv_qnty,2);
															$html_medium.=number_format($grey_recv_qnty,2);
														}
													?>
												</td>
												<? $html.="</td><td bgcolor='$bgcolor_grey_td'>"; $html_medium.="</td><td bgcolor='$bgcolor_grey_td'>"; ?>
												<td width="100" align="right" title="(Grey Req-Prod)">
												<?
													if($z==1)
													{
														echo number_format($grey_prod_balance,2,'.','');
														$html.=number_format($grey_prod_balance,2);
														$html_medium.=number_format($grey_prod_balance,2);
													}
												?>
												</td>
												<? $html.="</td><td bgcolor='$bgcolor_grey_td'>"; ?>
												<td width="100" align="right">

												<?
													if($z==1)
													{
														?>
															<a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $po_id; ?>','grey_delivery_to_store','')"><? echo number_format($grey_del_store,2,'.',''); ?></a>
														<?
														$html.=number_format($grey_del_store,2);
													}
												?>
												</td>
												<? $html.="</td><td bgcolor='$bgcolor_grey_td'>"; ?>
												<td width="100" align="right">
												<?
													$greyKnitFloor=0;
													if($z==1)
													{
														$greyKnitFloor=$grey_recv_qnty-$grey_del_store;
														echo number_format($greyKnitFloor,2,'.','');
														$tot_greyKnitFloor+=$greyKnitFloor;
														$html.=number_format($greyKnitFloor,2);
													}
												?>
												</td>
												<? $html.="</td><td bgcolor='$bgcolor_grey_td'>"; ?>
												<td width="100" align="right">

													<?
														if($z==1)
														{
														?>
															<a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $po_id.'_'.'9'; ?>','grey_purchase','')"><? echo number_format($grey_production_qnty,2,'.',''); ?></a>
														<?
															$html.=number_format($grey_production_qnty,2);
														}
													?>
												</td>
												<? $html.="</td><td bgcolor='$bgcolor_grey_td'>"; ?>
												<td width="100" align="right">
													<?
														if($z==1)
														{
														?>
															<a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $po_id.'_'.'0'; ?>','grey_purchase','')"><? echo number_format($grey_purchase_qnty,2,'.',''); ?></a>
														<?
															$html.=number_format($grey_purchase_qnty,2);
														}
													?>
												</td>
												<? $html.="</td><td>"; ?>
												<td width="100" align="right">

													<?
														if($z==1)
														{
														?>
                                                        <a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $po_id; ?>','grey_return','')"><? echo number_format($grey_net_return,2,'.',''); ?></a>
														<?
															$html.=number_format($grey_net_return,2);
															$tot_net_gray_return+=$grey_net_return;
														}
													?>
												</td>
												<? $html.="</td><td>"; ?>
												<td width="100" align="right">
												<?
													if($z==1)
													{
													?>
														<a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $po_id; ?>','knit_trans','')"><? echo number_format($net_trans_knit,2,'.','');  ?></a>
													<?
														$html.=number_format($net_trans_knit,2);
														$tot_net_trans_knit_qnty+=$net_trans_knit;
													}
												?>
												</td>
												<? $html.="</td><td>"; ?>
												<td width="100" align="right" title="Total Receive=( G. Prod. Qty + G. Purchase Qty + Net Trans Knit)">
												<?
													//$grey_available=0;
													//$grey_available=$grey_production_qnty+$grey_purchase_qnty+$net_trans_knit;
													if($z==1)
													{
														echo number_format($grey_available,2,'.','');
														$html.=number_format($grey_available,2);
														$knitted_array[$buyer_name]+=$grey_available;
														//$tot_net_trans_knit_qnty+=$net_trans_knit;
													}
												?>
												</td>
												<? $html.="</td><td>"; ?>
												<td width="100" align="right" title="Required (As per Booking) - Grey Actual Recv.">
													<?
														if($z==1)
														{
															echo number_format($grey_balance,2,'.','');
															$html.=number_format($grey_balance,2);
														}
													?>
												</td>
												<? $html.="</td><td>"; ?>
												<td width="100" align="right">

													<?
														if($z==1)
														{
														?>
															<a  href="##" onClick="open_febric_receive_status_order_wise_popup('<?
															$grey_fabric_issue=$grey_fabric_issue-$grey_net_return;
															 echo $po_id; ?>','grey_issue','')"><? echo number_format($grey_fabric_issue,2,'.',''); ?></a>
														<?
															$html.=number_format($grey_fabric_issue,2);
														};
													?>
												</td>
												<? $html.="</td><td>"; ?>
                                                <td width="100" align="right" title="Grey Actual Recv.-Net Grey Issue">
												<?
													if($z==1)
													{
														echo number_format($grey_in_hand,2,'.','');
														$html.=number_format($grey_in_hand,2);
														$tot_grey_in_hand+=$grey_in_hand;
													};
												?>
											</td>
											<? $html.="</td><td>"; ?>
												<td width="100" align="right">
													<?
														if($z==1)
														{
															?>
                                                            <a href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $po_id; ?>','receive_by_batch','')"><? echo number_format($receive_by_batch_qnt,2,'.',''); ?></a>
                                                            <?
															$html.=number_format($receive_by_batch_qnt,2);
														};
													?>
												</td>
												<? $html.="</td><td bgcolor='#FF9BFF'>"; $html_short.="</td><td bgcolor='#FF9BFF'>"; $html_medium.="</td><td bgcolor='#FF9BFF'>"; ?>
												<td width="100" align="center" bgcolor="#FF9BFF">
													<p>
														<?
															if($key==0)
															{
																echo "-";
																$html.="-"; $html_short.="-"; $html_medium.="-";
															}
															else
															{
																echo $color_array[$key];
																echo "<span style='font-size:10px;'>LD No. ".$lapdip_arr[$job_no.$po_id.$key].'</span>';
																$html.=$color_array[$key]; $html_short.=$color_array[$key]; $html_medium.=$color_array[$key];
															}
														?>
													</p>
												</td>
												<? $html.="</td><td>"; $html_short.="</td>"; $html_medium.="</td>"; ?>
												<td width="100" align="right">

													<?
														$grey_req_color_qty=0;
														$grey_req_color_qty=$grey_req_color_arr[$key];
														$html.=number_format($grey_req_color_qty,2);
														$tot_grey_req_color_qty+=$grey_req_color_qty;

													echo number_format($grey_req_color_qty,2,'.',''); ?>
												</td>
											   <? $html.="</td><td>"; ?>
												<td width="100" align="right">
													<?
														$batch_color_qnty=0;
														$batch_color_qnty=$batch_qnty_arr[$po_id][$key];
														$html.=number_format($batch_color_qnty,2);
														$tot_batch_qnty_excel+=$batch_color_qnty;
														$tot_batch_qnty+=$batch_color_qnty;

													?>
														<a href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $po_id.'_'.$key; ?>','batch_qnty','')"><? echo number_format($batch_color_qnty,2,'.',''); ?></a>
												</td>
											   <?
													$html.="</td><td>"; $html_short.="<td>"; $html_medium.="<td>";

													$fab_rec_return_production=$finish_recv_rtn_qnty_arr[$po_id][$key]['production'];
													$fab_rec_return_purchase=$finish_recv_rtn_qnty_arr[$po_id][$key]['purchase'];

													$fab_rec_return=$fab_rec_return_production+$fab_rec_return_purchase;
													$fab_issue_return=$finish_issue_rtn_qnty_arr[$po_id][$key];
													$fab_net_return=$fab_issue_return-$fab_rec_return;

													$fab_recv_qnty=$finish_receive_qnty_arr[$po_id][$key];
													$fab_production_qnty=$finish_purchase_qnty_arr[$po_id][$key]['production']-$fab_rec_return_production;
													$fab_purchase_qnty=$finish_purchase_qnty_arr[$po_id][$key]['purchase']-$fab_rec_return_purchase;
													$issue_to_cut_qnty=$finish_issue_qnty_arr[$po_id][$key]-$fab_issue_return;

													$dye_qnty=$dye_qnty_arr[$po_id][$key];
													$fab_source=rtrim($fab_source_arr[$po_id],',');
													$fab_source_id=array_unique(explode(",",$fab_source));
													rsort($fab_source_id);
													foreach($fab_source_id as $fsid)
													{
														if($fsid==1)
														{
															$dye_qnty=$dye_qnty;$fab_recv_qnty=$fab_recv_qnty;$fab_production_qnty=$fab_production_qnty;
														}
														else
														{
															//$dye_qnty=$fab_recv_qnty=$fab_production_qnty=0;
														}
													}
												?>
												<td width="100" align="right" bgcolor="<? echo $dye_prod_color_td; ?>">
													<a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $po_id; ?>','dye_qnty','<? echo $key; ?>')"><? echo number_format($dye_qnty,2,'.',''); ?></a>
													<?
														$html.=number_format($dye_qnty,2);
														$html_short.=number_format($dye_qnty,2);
														$html_medium.=number_format($dye_qnty,2);

														$dye_qnty_array[$buyer_name]+=$dye_qnty;
														$tot_dye_qnty+=$dye_qnty;
													?>
												</td>
												<? $html.="</td><td>"; $html_short.="</td><td>"; $html_medium.="</td><td>"; ?>

												<td width="100" align="right" title="Grey Req. Color - Dye Qty">
													<?
														$grey_balance_color_qty=0;
														$grey_balance_color_qty=$grey_req_color_qty-$dye_qnty;
														$html.=number_format($grey_balance_color_qty,2);
														$tot_grey_balance_color_qty+=$grey_balance_color_qty;

													echo number_format($grey_balance_color_qty,2,'.',''); ?>
												</td>
											   <? $html.="</td><td>"; ?>
												<td width="100" align="right">
													<?
														$dyeing_balance=$batch_color_qnty-$dye_qnty;
														echo number_format($dyeing_balance,2,'.','');
														$html.=number_format($dyeing_balance,2);
														$html_medium.=number_format($dyeing_balance,2);
														//$tot_dye_qnty+=$dyeing_balance;
														$tot_dye_qnty_balance+=$dyeing_balance;
													?>
												</td>
											   <td width="100" align="right">

													<?
														$html.="</td><td>";

														echo number_format($value,2,'.','');
														$html.=number_format($value,2);

														$fin_fab_Requi_array[$buyer_name]+=$value;
														$tot_color_wise_req+=$value;
													?>
												</td>
												<? $html.="</td><td>"; $html_medium.="</td><td>"; ?>
												<td width="100" align="right" bgcolor="<? echo $fin_prod_color_td; ?>">
													<a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $po_id; ?>','fabric_receive','<? echo $key; ?>')"><? echo number_format($fab_recv_qnty,2,'.',''); ?></a>
													<?
														$html.=number_format($fab_recv_qnty,2);
														$html_short.=number_format($fab_recv_qnty,2);
														$html_medium.=number_format($fab_recv_qnty,2);

														$finFabProductionArr[$buyer_name]+=$fab_recv_qnty;
														$tot_fabric_recv+=$fab_recv_qnty;
														$tot_fabric_recv_excel+=$fab_recv_qnty;
													?>
												</td>
												<? $html.="</td><td>"; $html_medium.="</td><td>"; ?>
												<td width="100" align="right">

													<?

														$finish_balance=$value-$fab_recv_qnty;

														echo number_format($finish_balance,2,'.','');
														$html.=number_format($finish_balance,2);
														$html_medium.=number_format($finish_balance,2);
														//$fin_fab_recei_array[$buyer_name]+=$finish_balance;
														$tot_fabric_recv_balance+=$finish_balance;
													?>
												</td>
												<? $html.="</td><td>"; ?>
												<td width="100" align="right">
														<a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $po_id.'_'.$key; ?>','finish_delivery_to_store','')"><? echo number_format($fin_delivery_qty,2,'.',''); ?></a>
														<?
														$html.=number_format($fin_delivery_qty,2);
														//$fin_fab_recei_array[$buyer_name]+=$fin_delivery_qty;
														$tot_fin_delivery_qty+=$fin_delivery_qty;
													?>
												</td>
												<? $html.="</td><td>"; ?>
												<td width="100" align="right">
													<?
														$finProdFloor=$fab_recv_qnty-$fin_delivery_qty;
														echo number_format($finProdFloor,2,'.','');
														$html.=number_format($finProdFloor,2);
														//$fin_fab_recei_array[$buyer_name]+=$fin_delivery_qty;
														$tot_finProdFloor+=$finProdFloor;
													?>
												</td>
												<? $html.="</td><td>"; ?>
												<td width="100" align="right">
													<a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $po_id.'_'.'9'; ?>','fabric_purchase','<? echo $key; ?>')"><? echo number_format($fab_production_qnty,2,'.',''); ?></a>
													<?
														$html.=number_format($fab_production_qnty,2);

														//$fin_fab_recei_array[$buyer_name]+=$fab_purchase_qnty;
														$tot_fabric_production+=$fab_production_qnty;
													?>
												</td>
												<? $html.="</td><td>"; ?>
												<td width="100" align="right" title="Data comes purchase booking from here">

													<a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $po_id.'_'.'0'; ?>','fabric_purchase','<? echo $key; ?>')"><? echo number_format($fab_purchase_qnty,2,'.',''); ?></a>
													<?
														$html.=number_format($fab_purchase_qnty,2);

														//$fin_fab_recei_array[$buyer_name]+=$fab_purchase_qnty;
														$tot_fabric_purchase+=$fab_purchase_qnty;
													?>
												</td>
												<? $html.="</td><td>"; ?>
												<td width="100" align="right">
                                                	<a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $po_id; ?>','finish_return','<? echo $key; ?>')"><? echo number_format($fab_net_return,2,'.',''); ?></a>
													<?
														$html.=number_format($fab_net_return,2);

														//$fin_fab_recei_array[$buyer_name]+=$fab_purchase_qnty;
														$tot_fab_net_return+=$fab_net_return;
													?>
												</td>
												<? $html.="</td><td>"; ?>
												<td width="100" align="right" >
													<?
														$net_trans_finish=$trans_qnty_fin_arr[$po_id][$key]['trans'];
														//$fin_fab_recei_array[$buyer_name]+=$net_trans_finish;
													?>
														<a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $po_id; ?>','finish_trans','<? echo $key; ?>')"><? echo number_format($net_trans_finish,2,'.','');  ?></a>
													<?
														$html.=number_format($net_trans_finish,2);
														$tot_net_trans_finish_qnty+=$net_trans_finish;
														$fabric_balance=$value-($fab_recv_qnty+$fab_purchase_qnty+$net_trans_finish+$fab_net_return);
													?>
												</td>
												<? $html.="</td><td>"; ?>
												<td width="100" align="right" title="Total Receive=( Received (Prod.) + Received (Purchase) + Net Transfer)">
													<?
														$fabric_available=$fab_production_qnty+$fab_purchase_qnty+$net_trans_finish;
														$fin_fab_recei_array[$buyer_name]+=$fabric_available;
														echo number_format($fabric_available,2,'.','');
														$html.=number_format($fabric_available,2);
														$tot_fabric_available+=$fabric_available;
													?>
												</td>
												<? $html.="</td><td>"; ?>
												<td width="100" align="right" title="Req. Qty (As Per Booking)-Fin. Fab. Actual Recv.">
													<?
														$fabric_receive_bal=$value-$fabric_available;
														echo number_format($fabric_receive_bal,2,'.','');
														$fin_balance_array[$buyer_name]+=$fabric_receive_bal;
														$html.=number_format($fabric_receive_bal,2);
														$tot_fabric_rec_bal+=$fabric_receive_bal;
													?>
												</td>
												<? $html.="</td><td>"; $html_short.="</td><td>"; $html_medium.="</td><td>"; ?>
												<td width="100" align="right">
													<a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $po_id; ?>','issue_to_cut','<? echo $key; ?>')"><? echo number_format($issue_to_cut_qnty,2,'.',''); ?></a>
													<?
														$html.=number_format($issue_to_cut_qnty,2);
														$html_short.=number_format($issue_to_cut_qnty,2);
														$html_medium.=number_format($issue_to_cut_qnty,2);
														$issue_toCut_array[$buyer_name]+=$issue_to_cut_qnty;
														$tot_issue_to_cut_qnty+=$issue_to_cut_qnty;
													?>
												</td>
												<? $html.="</td><td>"; ?>
												<td width="100" align="right"><? $yet_to_cut_qty=$value-$issue_to_cut_qnty; echo number_format($yet_to_cut_qty,2,'.',''); $tot_yet_to_cut+=$yet_to_cut_qty; $html.=number_format($yet_to_cut_qty,2); ?></a>
												</td>
												<? $html.="</td><td>"; ?>
												<td width="100" align="right">
													<?
														$fabric_left_over=$fabric_available-$issue_to_cut_qnty;
														echo number_format($fabric_left_over,2,'.','');
														$html.=number_format($fabric_left_over,2);
														$tot_fabric_left_over+=$fabric_left_over;
														$tot_fabric_left_over_excel+=$fabric_left_over;
													?>
												</td>
												<? $html.="</td><td>"; ?>
												<td width="100" align="right">
													<?
														//$fabric_left_over=($fab_recv_qnty+$fabric_available)-$issue_to_cut_qnty;
														//echo number_format($fabric_left_over,2,'.','');
														//$html.=number_format($fabric_left_over,2);
														//$tot_fabric_left_over+=$fabric_left_over;
													?>
												</td>
												<td>
													<p>
														<? $fabric_desc=explode(",",$fabric_desc_details[$job_no]); echo $display_font_color.join(",<br>",array_unique($fabric_desc)).$font_end; ?>
													</p>
												</td>
											</tr>
										<?
											if($z==1) $html.="</td><td>".join(",<br>",array_unique($fabric_desc))."</td></tr>"; else $html.="</td><td>&nbsp;</td></tr>";
											$html_short.="</td></tr>";
											$html_medium.="</td></tr>";
										$z++;
										$k++;
										}
									}
									else
									{
										$html.="<tr bgcolor='".$bgcolor."'>
														<td align='left'>".$i."</td>
														<td align='left'>".$main_booking_excel."</td>
														<td align='left'>".$sample_booking_excel."</td>
														<td align='left'>".$all_book_prefix_no."</td>
														<td align='center'>".$job_no."</td>
														<td align='center'><img  src='../../".$imge_arr[$job_no]."' height='25' width='30' /></td>
														<td align='left'>".$po_number."</td>
														<td align='left'>".$order_status[$is_confirmed]."</td>
														<td>".$buyer_short_name_library[$buyer_name]."</td>
														<td align='left'>".$style_ref_no."</td>
														<td align='left'>".$file_no."</td>
														<td align='left'>".$grouping."</td>

														<td align='left'>".$gmts_item."</td>
														<td align='right'>".$order_qnty_in_pcs."</td>
														<td align='left'>".change_date_format($pub_shipment_date)."</td>
														<td align='center'>".change_date_format($po_received_date)."</td>
														<td align='center'>".$po_entry_date."</td>
														<td>".$shipment_status[$shiping_status]."</td>
														<td align='center'>".$contry_ship_date."</td>
														<td align='right'>".$country_ship_qty."</td>
														<td align='right'>".$grey_cons."</td>
														<td align='right'>".$fin_cons."</td>";

										$lead_time=0;
										$lead_time=datediff('d',$po_received_date,$pub_shipment_date);
										if($lead_time>0) $lead_time=$lead_time-1;

										$html_short.="<tr bgcolor='".$bgcolor."'>
													<td align='left'>".$i."</td>
													<td align='left'>".$main_booking_excel."</td>
													<td align='left'>".$sample_booking_excel."</td>
													<td align='left'>".$all_book_prefix_no."</td>
													<td align='left'>".$po_number."</td>
													<td>".$buyer_short_name_library[$buyer_name]."</td>
													<td align='left'>".$file_no."</td>
													<td align='left'>".$grouping."</td>
													<td align='right'>".$order_qnty_in_pcs."</td>
													<td align='left'>".change_date_format($pub_shipment_date)."</td>";

										$html_medium.="<tr bgcolor='".$bgcolor."'>
													<td align='left'>".$i."</td>
													<td>".$buyer_short_name_library[$buyer_name]."</td>
													<td align='left'>".$main_booking_excel."</td>
													<td align='left'>".$sample_booking_excel."</td>
													<td align='left'>".$all_book_prefix_no."</td>
													<td align='left'>".$po_number."</td>
													<td align='left'>".$grouping."</td>
													<td align='left'>".$file_no."</td>
													<td align='left'>".$style_ref_no."</td>

													<td align='right'>".$order_qnty_in_pcs."</td>
													<td align='left'>".change_date_format($pub_shipment_date)."</td>
													<td align='left'>".$lead_time."</td>";

									?>
										<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
											<td width="40"><? echo $i; ?></td>
											<td width="125"><? echo $main_booking; ?></td>
											<td width="125"><? echo $sample_booking; ?></td>
                                            <td width="75" style="word-break:break-all"><? echo $all_book_prefix_no; ?></td>
											<td width="100" align="center"><? echo $job_no; ?></td>
                                            <td width="40" onClick="openmypage_image('requires/fabric_receive_status_report2_controller.php?action=show_image&job_no=<?=$job_no; ?>','Image View')"><img  src="../../<?=$imge_arr[$job_no]; ?>" height='25' width='30' /></td>
											<td width="120">
												<p>
													<a href='#report_details' onClick="progress_comment_popup('<? echo $job_no; ?>','<? echo $po_id; ?>','<? echo $template_id; ?>');"><? echo $po_number;  ?></a>
												</p>
											</td>
											<td width="90" align="center"><? echo $order_status[$is_confirmed]; ?></td>
											<td width="80"><p><? echo $buyer_short_name_library[$buyer_name]; ?></p></td>
											<td width="130"><p><? echo $style_ref_no; ?></p></td>
											<td width="100"><p><? echo $file_no; ?></p></td>
											<td width="100"><p><? echo $grouping; ?></p></td>
											<td width="140"><p><? echo $gmts_item; ?></p></td>
											<td width="100" align="right"><? echo number_format($order_qnty_in_pcs,0,'.',''); ?></td>
											<td width="80" align="center"><? echo change_date_format($pub_shipment_date); ?></td>
											<td width="80" align="center"><? echo change_date_format($po_received_date); ?></td>
											<td width="80" align="center"><? echo $po_entry_date; ?></td>
											<td width="100" align="center"><? echo $shipment_status[$shiping_status]; ?></td>
											<td width="80"><p><? echo $display_font_color.$contry_ship_date.$font_end; ?></p></td>
											<td width="100" align="right"><a href="##" onClick="country_order_dtls('<? echo $po_id; ?>','<? echo $start_date; ?>','<? echo $end_date; ?>','<? echo $buyer_name; ?>','<? echo $job_no; ?>','country_order_dtls_popup')"><? echo number_format($country_ship_qty); ?></a></td>
											<td width="100" align="right" title="<? echo $fabric_costing_arr['knit']['grey'][$po_id]."/".$plan_cut_qnty; ?>"><? echo number_format($grey_cons,5,'.',''); ?></td>
											<td width="100" align="right" title="<? echo $fabric_costing_arr['knit']['finish'][$po_id]."/".$plan_cut_qnty; ?>"><? echo number_format($fin_cons,5,'.',''); ?></td>
											<td width="70">
												<?
													 $html.="<td>"; $d=1;
													 foreach($yarn_data_array['count'] as $yarn_count_value)
													 {
														if($d!=1)
														{
															echo "<hr/>";
															$html.="<hr/>";
														}

														echo $yarn_count_value;
														$html.=$yarn_count_value;

													 $d++;
													 }

													 $html.="</td><td>";
												?>
											</td>
											<td width="110">
												<div style="word-wrap:break-word; width:110px">
													<?
														 $d=1;
														 foreach($yarn_data_array['comp'] as $yarn_composition_value)
														 {
															if($d!=1)
															{
																echo "<hr/>";
																$html.="<hr/>";
															}

															echo $yarn_composition_value;
															$html.=$yarn_composition_value;

														 $d++;
														 }

														 $html.="</td><td>";
													?>
												</div>
											</td>
											<td width="80">
												<p>
													<?
														 $d=1;
														 foreach($yarn_data_array['type'] as $yarn_type_value)
														 {
															if($d!=1)
															{
																echo "<hr/>";
																$html.="<hr/>";
															}

															echo $yarn_type_value;
															$html.=$yarn_type_value;

														 $d++;
														 }
														 $html.="</td><td>";
													?>
												</p>
											</td>
											<td width="100" align="right">
												<?
													echo "<font color='$bgcolor' style='display:none'>".number_format(array_sum($mkt_required_array),2,'.','')."</font>\n";
													$d=1;
													foreach($mkt_required_array as $mkt_required_value)
													{
														if($d!=1)
														{
															echo "<hr/>";
															$html.="<hr/>";
														}

														$yarn_desc_for_popup_req=explode("__",$yarn_desc_array_for_popup[$d]);

														?>
														<a href="##" onClick="openmypage('<? echo $po_id; ?>','yarn_req','<? echo $yarn_desc_for_popup_req[0]; ?>','<? echo $yarn_desc_for_popup_req[1]; ?>','<? echo $yarn_desc_for_popup_req[2]; ?>','<? echo $yarn_desc_for_popup_req[3]; ?>','<? echo $yarn_desc_for_popup_req[4]; ?>','<? echo $yarn_desc_for_popup_req[5]; ?>')"><? echo number_format($mkt_required_value,2,'.','');?></a>
													<?
													$html.=number_format($mkt_required_value,2);
													$d++;
													}

													$html.="</td><td bgcolor='$discrepancy_td_color'>"; $html_short.="<td>"; $html_medium.="<td>";
												?>
											</td>
											<td width="100" align="right">
												<?
													if($z==1)
													{
														$d=1;
														foreach($yarn_allocation_arr as $yarn_allocation_value)
														{
															if($d!=1)
															{
																echo "<hr/>";
																$html.="<hr/>";
															}
															$yarn_desc_for_popup_req=explode("__",$yarn_desc_array_for_popup[$d]);
															?>
															<a href="##" onClick="openmypage('<? echo $po_id; ?>','yarn_allocation_pop','<? echo $yarn_desc_for_popup_req[0]; ?>','<? echo $yarn_desc_for_popup_req[1]; ?>','<? echo $yarn_desc_for_popup_req[2]; ?>','<? echo $yarn_desc_for_popup_req[3]; ?>','<? echo $yarn_desc_for_popup_req[4]; ?>','<? echo $yarn_desc_for_popup_req[5]; ?>')"><? echo number_format($yarn_allocation_value,2,'.','');?></a>
														<?
														$html.=number_format($yarn_allocation_value,2);
														$d++;
														}
													}
													$html.="</td><td bgcolor='$discrepancy_td_color'>";
												?>
											</td>
											<td width="100" align="right">
												<?
													if($z==1)
													{
														$job_yetTo_allocate=$required_qnty-$job_yarnAllocationQty;
														echo number_format($job_yetTo_allocate,2,'.','');
														$tot_yetTo_allocate+=$job_yetTo_allocate;
														$html.=number_format($job_yetTo_allocate,2);
													}
													$html.="</td><td bgcolor='$discrepancy_td_color'>";
												?>
											</td>
											<td width="100" align="right" bgcolor="<? echo $yarn_color_td; ?>">
												<?
													echo "<font color='$bgcolor' style='display:none'>".number_format($yarn_issued,2,'.','')."</font>\n";
													$d=1;
													foreach($yarn_desc_array as $yarn_desc)
													{
														if($d!=1)
														{
															echo "<hr/>";
															$html.="<hr/>";
														}

														$yarn_iss_qnty=$yarn_iss_qnty_array[$yarn_desc];
														$yarn_desc_for_popup=explode("__",$yarn_desc_array_for_popup[$d]);

														?>
														<a href="##" onClick="openmypage('<? echo $po_id; ?>','yarn_issue','<? echo $yarn_desc_for_popup[0]; ?>','<? echo $yarn_desc_for_popup[1]; ?>','<? echo $yarn_desc_for_popup[2]; ?>','<? echo $yarn_desc_for_popup[3]; ?>','<? echo $yarn_desc_for_popup[4]; ?>','<? echo $yarn_desc_for_popup[5]; ?>')"><? echo number_format($yarn_iss_qnty,2,'.','');?></a>
														<?
														$html.=number_format($yarn_iss_qnty,2);
														$d++;
													}

													if($d!=1)
													{
														echo "<hr/>";
														$html.="<hr/>";
													}

													$yarn_desc=join(",",$yarn_desc_array);

													$iss_qnty_not_req=$yarn_iss_qnty_array['not_req'];

													$html.=number_format($iss_qnty_not_req,2);
													$html_short.=number_format($iss_qnty_not_req+$yarn_issued,2);
													$html_medium.=number_format($yarn_issued,2);
													?>
													<a href="##" onClick="openmypage('<? echo $po_id; ?>','yarn_issue_not','<? echo $yarn_desc; ?>','','','','','')"><? echo number_format($iss_qnty_not_req,2);?></a>
											</td>
											<? $html.="</td><td>"; $html_short.="</td>"; $html_medium.="</td>"; ?>
											<td width="100" align="right">
												 <a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $po_id; ?>','yarn_trans','')"><? echo number_format($net_trans_yarn,2,'.','');  ?></a>
												<?
													$html.=number_format($net_trans_yarn,2);
													$tot_net_trans_yarn_qnty+=$net_trans_yarn;
												?>
											</td>
											<? $html.="</td><td>"; $html_medium.="</td><td>"; ?>
											<td width="100" align="right" title="(Grey Req-(Yarn Issue+Net Transfer))">
												<?
													echo number_format($balance,2,'.','');
													$html.=number_format($balance,2);
													$html_medium.=number_format($balance,2);
												?>
											</td>
											<? $html.="</td><td>"; ?>
											<td width="100" align="right" bgcolor="<? echo $bgcolor_grey_td; ?>"> <? echo number_format($required_qnty,2,'.',''); $html.=number_format($required_qnty,2); ?></td>
											<? $html.="</td><td>"; ?>
											<td width="100" align="right" bgcolor="<? echo $gray_prod_color_td; ?>"><a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $po_id; ?>','grey_receive','')"><? echo number_format($grey_recv_qnty,2,'.',''); $html.=number_format($grey_recv_qnty,2);?></a></td>
											<? $html.="</td><td>"; ?>
											<td width="100" align="right">
												<?
													echo number_format($grey_prod_balance,2,'.','');
													$html.=number_format($grey_prod_balance,2);
												?>
											</td>
											<? $html.="</td><td>"; ?>
											<td width="100" align="right"><a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $po_id; ?>','grey_purchase','')"><? echo number_format($grey_del_store,2,'.',''); $html.=number_format($grey_del_store,2);?></a></td>
											<? $html.="</td><td>"; ?>
											<td width="100">&nbsp; <? $html.="&nbsp;"; ?></td>
											<? $html.="</td><td>"; ?>
											<td width="100" align="right"><a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $po_id.'_'.'9'; ?>','grey_purchase','')"><? echo number_format($grey_production_qnty,2,'.',''); $html.=number_format($grey_production_qnty,2); ?></a></td>
											<? $html.="</td><td>"; ?>
											<td width="100" align="right"><a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $po_id.'_'.'9'; ?>','grey_purchase','')"><? echo number_format($grey_purchase_qnty,2,'.',''); $html.=number_format($grey_purchase_qnty,2); ?></a></td>
											<? $html.="</td><td>"; ?>
											<td width="100" align="right"></td>
											<? $html.="</td><td>"; ?>
											<td width="100" align="right">
												<a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $po_id; ?>','knit_trans','')"><? echo number_format($net_trans_knit,2,'.','');  ?></a><? $tot_net_trans_knit_qnty+=$net_trans_knit; $html.=number_format($net_trans_knit,2); ?>
											</td>
											<? $html.="</td><td>"; ?>
											<td width="100" align="right">
												<? $grey_available=0; $grey_available=$grey_production_qnty+$grey_purchase_qnty+$net_trans_knit;
												echo number_format($grey_available,2,'.',''); $html.=number_format($grey_available,2); ?>
											</td>
											<? $html.="</td><td>"; ?>
											<td width="100" align="right"><? echo number_format($grey_balance,2,'.',''); $html.=number_format($grey_balance,2); ?></td>
											<? $html.="</td><td>"; ?>
											<td width="100" align="right"><a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $po_id; ?>','grey_issue','')"><?
											$grey_fabric_issue=$grey_fabric_issue-$grey_net_return;
											 echo number_format($grey_fabric_issue,2,'.',''); $html.=number_format($grey_fabric_issue,2); ?></a>
											</td>
											<? $html.="</td><td>"; ?>
                                            <td width="100" align="right"><? echo number_format($grey_in_hand,2,'.',''); $html.=number_format($grey_in_hand,2); ?></td>
											<? $html.="</td><td>"; ?>
											<td width="100" align="right">
											<a href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $po_id; ?>','receive_by_batch','')"><? echo number_format($receive_by_batch_qnt,2,'.',''); ?></a>
											<? $html.=number_format($receive_by_batch_qnt,2); ?>
											</td>
											<? $html.="</td><td>"; ?>
											<td width="100">&nbsp;<? $html.="&nbsp;"; ?></td>
											<? $html.="</td><td>"; ?>
											<td width="100">&nbsp;<? $html.="&nbsp;"; ?></td>
											<? $html.="</td><td>"; ?>
											<td width="100" align="right"><a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $po_id; ?>','batch_qnty','')"><? /*echo number_format($batch_color_qnty,2,'.','');*/ $html.="&nbsp;"; ?></a></td>
											<? $html.="</td><td>"; ?>
											<td width="100" bgcolor="<? echo $dye_prod_color_td; ?>">&nbsp;</td>
											<td width="100">&nbsp;</td>
											<td width="100">&nbsp;</td>
											<td width="100">&nbsp;</td>
											<td width="100">&nbsp;</td>
											<td width="100">&nbsp;</td>
											<td width="100">&nbsp;</td>
											<td width="100">&nbsp;</td>
											<td width="100">&nbsp;</td>
											<td width="100">&nbsp;</td>
											<td width="100">&nbsp;</td>
											<td width="100">&nbsp;</td>
											<td width="100">&nbsp;</td>
											<td width="100">&nbsp;</td>
											<td width="100">&nbsp;</td>
											<td width="100">&nbsp;</td>
											<td width="100">&nbsp;</td>
											<td width="100">&nbsp;</td>
											<td>
												<p>
													<? $fabric_desc=explode(",",$fabric_desc_details[$job_no]); echo join(",<br>",array_unique($fabric_desc)); ?>
												</p>
											</td>
										</tr>
										<?
											$tot_batch_qnty_excel+=$batch_qnty;
											$html.="</td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td>".join(",<br>",array_unique($fabric_desc))."</td>
											</tr>
											";

											$html_short.="</td><td bgcolor='$bgcolor_grey_td'>".number_format($required_qnty,2)."</td>
											<td>".number_format($grey_recv_qnty,2)."</td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											</tr>
											";

											$html_medium.="</td><td bgcolor='$bgcolor_grey_td'>".number_format($required_qnty,2)."</td>
											<td>".number_format($grey_recv_qnty,2)."</td>
											<td>".number_format($grey_prod_balance,2)."</td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											</tr>
											";
										$k++;
									}
									$i++;
								}
							}// end main query
						}
					}
					else
					{

						foreach($po_data_arr as $po_id=>$other_data)
						{
							$ex_data=explode('##',$other_data);
							$company_id=''; $buyer_name='';  $job_no_prefix_num=''; $job_no=''; $style_ref_no=''; $gmts_item_id=''; $order_uom=''; $ratio=''; $grouping=''; $file_no=''; $po_number=''; $po_qnty=''; $pub_shipment_date=''; $shiping_status=''; $insert_date=''; $po_received_date=''; $plan_cut=''; $is_confirmed='';// $po_id
							$company_id=$ex_data[0];
							$buyer_name=$ex_data[1];
							$job_no_prefix_num=$ex_data[2];
							$job_no=$ex_data[3];
							$style_ref_no=$ex_data[4];
							$gmts_item_id=$ex_data[5];
							$order_uom=$ex_data[6];
							$ratio=$ex_data[7];
							$grouping=$ex_data[8];
							$file_no=$ex_data[9];
							$po_number=$ex_data[10];
							$po_qnty=$ex_data[11];
							$pub_shipment_date=$ex_data[12];
							$shiping_status=$ex_data[13];
							$insert_date=$ex_data[14];
							$po_received_date=$ex_data[15];
							$plan_cut=$ex_data[16];
							$is_confirmed=$ex_data[17];

							$template_id=$template_id_arr[$po_id];

							$order_qnty_in_pcs=$po_qnty*$ratio;
							$plan_cut_qnty=$plan_cut*$ratio;
							$order_qty_array[$buyer_name]+=$order_qnty_in_pcs;
							$gmts_item='';
							$gmts_item_id=explode(",",$gmts_item_id);
							foreach($gmts_item_id as $item_id)
							{
								if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
							}

							$dzn_qnty=0; $balance=0; $job_mkt_required=0; $yarn_issued=0;
							if($costing_per_id_library[$job_no]==1) $dzn_qnty=12;
							else if($costing_per_id_library[$job_no]==3) $dzn_qnty=12*2;
							else if($costing_per_id_library[$job_no]==4) $dzn_qnty=12*3;
							else if($costing_per_id_library[$job_no]==5) $dzn_qnty=12*4;
							else $dzn_qnty=1;

							$dzn_qnty=$dzn_qnty*$ratio;

							$yarn_data_array=array(); $mkt_required_array=array(); $yarn_allocation_arr=array(); $yetTo_allocate_arr=array(); $req_for_allocate_arr=array(); $yarn_desc_array_for_popup=array(); $yarn_desc_array=array(); $yarn_iss_qnty_array=array();
							$yarn_allocated_arr=array();$yarn_autoallocated_arr=array();
							$job_yarnAllocationQty=0;
							$job_yarnAutoAllocationQty=0;
							$s=1;

							$yarn_descrip_data=$yarn_des_data[$po_id];
							//echo "kausar";
							//print_r($yarn_des_data[$po_id]);
							$qnty=0;
							foreach($yarn_descrip_data as $count=>$count_value)
							{
								foreach($count_value as $Composition=>$composition_value)
								{
									foreach($composition_value as $percent=>$percent_value)
									{
										foreach($percent_value as $type_ref=>$type_value)
										{
											$count_id=$count;//$yarnRow[0];
											$copm_one_id=$Composition;//$yarnRow[1];
											$percent_one=$percent;//$yarnRow[2];
											$type_id=$type_ref;//$yarnRow[5];
											$qnty=$type_value;//$yarnRow[6];

											$mkt_required=$qnty;//$plan_cut_qnty*($qnty/$dzn_qnty);
											$mkt_required_array[$s]=$mkt_required;

											$job_mkt_required+=$mkt_required;

											$yarn_data_array['count'][$s]=$yarn_count_details[$count_id];
											$yarn_data_array['type'][$s]=$yarn_type[$type_id];

											//$compos=$composition[$copm_one_id]." ".$percent_one."%".$composition[$copm_two_id];
											$compos=$composition[$copm_one_id]." ".$percent_one." %"." ".$composition[$copm_two_id];

											$yarn_data_array['comp'][]=$compos;

											$yarn_desc_array[$s]=$yarn_count_details[$count_id]." ".$compos." ".$yarn_type[$type_id];
											$des_for_allocation=$yarn_count_details[$count_id]." ".$compos." ".$yarn_type[$type_id];

											$req_for_allocate_arr[$des_for_allocation]=$mkt_required;

											$yarn_desc_for_popup=$count_id."__".$copm_one_id."__".$percent_one."__".$copm_two_id."__".$percent_two."__".$type_id;
											$yarn_desc_array_for_popup[$s]=$yarn_desc_for_popup;

											$s++;
										}
									}
								}
							}

							$dataYarnIssue=explode(",",substr($dataArrayYarnIssue[$po_id],0,-1));
							foreach($dataYarnIssue as $yarnIssueRow)
							{
								$yarnIssueRow=explode("**",$yarnIssueRow);
								$yarn_count_id=$yarnIssueRow[0];
								$yarn_comp_type1st=$yarnIssueRow[1];
								$yarn_comp_percent1st=$yarnIssueRow[2];
								$yarn_comp_type2nd=$yarnIssueRow[3];
								$yarn_comp_percent2nd=$yarnIssueRow[4];
								$yarn_type_id=$yarnIssueRow[5];
								$issue_qnty=$yarnIssueRow[6];
								$return_qnty=$yarnIssueRow[7];

								if($yarn_comp_percent2nd!=0)
								{
									$compostion_not_req=$composition[$yarn_comp_type1st]." ".$yarn_comp_percent1st." % ".$composition[$yarn_comp_type2nd]." ".$yarn_comp_percent2nd." %";
								}
								else
								{
									$compostion_not_req=$composition[$yarn_comp_type1st]." ".$yarn_comp_percent1st." % ".$composition[$yarn_comp_type2nd];
								}

								$desc=$yarn_count_details[$yarn_count_id]." ".$compostion_not_req." ".$yarn_type[$yarn_type_id];

								$net_issue_qnty=$issue_qnty-$return_qnty;

								$fab_source=rtrim($fab_source_arr[$po_id],',');
								$fab_source_id=array_unique(explode(",",$fab_source));
								foreach($fab_source_id as $fsid)
								{
									if($fsid==1)
									{
										 $net_issue_qnty=$net_issue_qnty;
									}
								}


								$yarn_issued+=$net_issue_qnty;
								if(!in_array($desc,$yarn_desc_array))
								{
									$yarn_iss_qnty_array['not_req']+=$net_issue_qnty;
								}
								else
								{
									$yarn_iss_qnty_array[$desc]+=$net_issue_qnty;
								}
							}

							$grey_issue_rtn=$grey_issue_return_qnty_arr[$po_id];
							$grey_rec_rtn_purchase=$grey_receive_return_qnty_arr[$po_id]['purchase'];
							$grey_rec_rtn_production=$grey_receive_return_qnty_arr[$po_id]['production'];

							$grey_purchase_qnty=$greyPurchaseQntyArray[$po_id]['purchase']-$grey_rec_rtn_purchase;
							$grey_production_qnty=$greyPurchaseQntyArray[$po_id]['production']-$grey_rec_rtn_production;

							$grey_net_return=$grey_issue_rtn-($grey_rec_rtn_purchase+$grey_rec_rtn_production);
							$grey_recv_qnty=$grey_receive_qnty_arr[$po_id];

							$grey_fabric_issue=$grey_issue_qnty_arr[$po_id]-$grey_issue_rtn;



							$receive_by_batch_qnt=$receive_by_batch_data[$po_id];
							$fab_source=rtrim($fab_source_arr[$po_id],',');
							$fab_source_id=array_unique(explode(",",$fab_source));
							foreach($fab_source_id as $fsid)
							{
								if($fsid==1)
								{
									$grey_recv_qnty=$grey_recv_qnty; $grey_production_qnty=$grey_production_qnty; $grey_purchase_qnty=$grey_purchase_qnty;
									$grey_net_return=$grey_net_return; $grey_fabric_issue=$grey_fabric_issue; $receive_by_batch_qnt=$receive_by_batch_qnt;
								}
								else
								{
									//$grey_recv_qnty=0;$grey_production_qnty=0;$grey_purchase_qnty=0;$grey_net_return=$receive_by_batch_qnt=$grey_fabric_issue=0;
								}
							}

							$contry_ship_date=""; $country_ship_qty=0;
							//$country_date_all=$contry_ship_qty_arr[$po_id]['ship_date'];
							//print_r();
							$country_date_all=array_filter(array_unique(explode(',',$contry_ship_qty_arr[$po_id]['ship_date'])));
							//print_r($country_date_all);
							foreach($country_date_all as $date_all)
							{
								//echo $date_all.'<br>';
								if($date_all!='')
								{
									if($contry_ship_date=="") $contry_ship_date=change_date_format($date_all); else $contry_ship_date.=',<br>'.change_date_format($date_all);
									//if($contry_ship_date=="") $contry_ship_date=$date_all; else $contry_ship_date.=',<br>'.$date_all;
								}
							}
							//die;
							$country_ship_qty=$contry_ship_qty_arr[$po_id]['ship_qty'];
							$tot_country_ship_qty+=$country_ship_qty;
							$country_order_qty_array[$buyer_name]+=$country_ship_qty;

							if(($cbo_discrepancy==1 && $grey_recv_qnty>$yarn_issued) || ($cbo_discrepancy==0))
							{
								if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								$buyer_name_array[$buyer_name]=$buyer_short_name_library[$buyer_name];

								$booking_array=array(); $color_data_array=array(); $grey_req_color_arr=array(); $fabric_source_arr=array();
								$required_qnty=0; $main_booking=''; $sample_booking=''; $main_booking_excel=''; $sample_booking_excel=''; $all_book_prefix_no = '';
								$dataArray=array_filter(explode("__",$dataArrayWo[$po_id]));
								if(count($dataArray)>0)
								{
									foreach($dataArray as $woRow)
									{

										$woRow=explode("**",$woRow);
										$id=$woRow[0];
										$booking_no=$woRow[1];
										$insert_date=$woRow[2];
										$item_category=$woRow[3];
										$fabric_source=$woRow[4];
										$company_id=$woRow[5];
										$booking_type=$woRow[6];
										$booking_no_prefix_num=$woRow[7];
										$job_no=$woRow[8];
										$is_short=$woRow[9];
										$is_approved=$woRow[10];
										$fabric_color_id=$woRow[11];
										$req_qnty=$woRow[12];
										$grey_req_qnty=$woRow[13];
										$wo_po_id=$woRow[14];
										$book_prefix_no = $woRow[7];
										if($fabric_source==1)
										{
											$grey_req_qnty=$grey_req_qnty;$req_qnty=$req_qnty;
										}
										else
										{
											//$grey_req_qnty=$req_qnty=0;
										}
										$required_qnty+=$grey_req_qnty;

										if(!in_array($id,$booking_array))
										{
											if($bookingEntryFromArr[$booking_no]==86)
											{
												$entryForm=$bookingEntryFromArr[$booking_no];
												$reportFormat=explode(",",$print_report_format_budget_booking);
												$reportFormat=$reportFormat[0];
												$action_namez=$report_format_arr[$reportFormat];
											}
											if($bookingEntryFromArr[$booking_no]==88)
											{
												$entryForm=$bookingEntryFromArr[$booking_no];
												$reportFormatshort=explode(",",$print_report_format_short_booking);
												$reportFormatshort=$reportFormatshort[0];
												$action_nameshort=$report_format_short_arr[$reportFormatshort];
											}
											if($bookingEntryFromArr[$booking_no]==118)
											{
												$entryForm=$bookingEntryFromArr[$booking_no];
												$reportFormatv2=explode(",",$print_report_format_budget_booking);
												$reportFormatv2=$reportFormatv2[0];
												$action_namemv2=$report_format_mainv2_arr[$reportFormatv2];
											}

											if($bookingEntryFromArr[$booking_no]==108)
											{
												$entryForm=$bookingEntryFromArr[$booking_no];
												$reportFormatPartial=explode(",",$print_report_format_partial_booking);
												$reportFormatPartial=$reportFormatPartial[0];
												$action_namep=$report_format_partial_arr[$reportFormatPartial];
											}
											$system_date=date('d-M-Y', strtotime($insert_date));
											$wo_color = "";
											if ($fabric_source == 2) $wo_color = "color:#000"; else $wo_color = "";

											if($booking_type==4)
											{
												if($entryForm==86)
												{
													$action_name=$action_namez;
												}
												else{
													//$action_name='show_fabric_booking_report';
													$action_name=$report_format_sample_arr[$reportFormatSample];
												}

												$sample_booking.="<a href='##' style='color:#000' onclick=\"generate_worder_report('3','".$booking_no."','".$company_id."','".$wo_po_id."','".$item_category."','".$fabric_source."','".$job_no."','".$is_approved."','".$action_name."','".$print_report_format."','".$entryForm."')\"><font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")"."</font></a><br>";
												$sample_booking_excel.="<font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")"."</font><br>";
											}
											else
											{
												$all_book_prefix_no .= $book_prefix_no . ",";
												if($is_short==1)
												{
													$pre="S";
													$action_name=$report_format_arr[$booking_print_arr[2]];
												}
												else
												{
													$pre="M";
													$action_name=$report_format_arr[$booking_print_arr[1]];
												}
												if($entryForm==86)
												{
													//$action_name=$action_namez;
												}
												else if ($action_name=='') {
														$action_name='show_fabric_booking_report';
												}
												//if($is_short==1) $pre="S"; else $pre="M";

												if($entryForm==108)
												{
													$action_name=$action_namep;
													$main_booking.="<a href='##' style='color:#000' onclick=\"generate_worder_report('".$is_short."','".$booking_no."','".$company_id."','".$wo_po_id."','".$item_category."','".$fabric_source."','".$job_no."','".$is_approved."','".$action_name."','".$reportFormatPartial[0]."','".$entryForm."')\"><font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")".$pre."</font></a><br>";
													$main_booking_excel.="<font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")".$pre."</font><br>";
												}
												else if($entryForm==118)
												{
													$action_name=$action_namemv2;
													$main_booking.="<a href='##' style='color:#000' onclick=\"generate_worder_report('".$is_short."','".$booking_no."','".$company_id."','".$wo_po_id."','".$item_category."','".$fabric_source."','".$job_no."','".$is_approved."','".$action_name."','".$reportFormatv2[0]."','".$entryForm."')\"><font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")".$pre."</font></a><br>";
													$main_booking_excel.="<font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")".$pre."</font><br>";
												}
												else if($entryForm==88)
												{
													$action_name=$action_nameshort;
													$main_booking.="<a href='##' style='color:#000' onclick=\"generate_worder_report('".$is_short."','".$booking_no."','".$company_id."','".$wo_po_id."','".$item_category."','".$fabric_source."','".$job_no."','".$is_approved."','".$action_name."','".$reportFormatshort[0]."','".$entryForm."')\"><font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")".$pre."</font></a><br>";
													$main_booking_excel.="<font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")".$pre."</font><br>";
												}
												else
												{
													$main_booking.="<a href='##' style='color:#000' onclick=\"generate_worder_report('".$is_short."','".$booking_no."','".$company_id."','".$wo_po_id."','".$item_category."','".$fabric_source."','".$job_no."','".$is_approved."','".'show_fabric_booking_report_jk'."','".'777'."')\"><font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")".$pre."</font></a><br>";
													$main_booking_excel.="<font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")".$pre."</font><br>";
												}

											}

											$booking_array[]=$id;
										}
										$color_data_array[$fabric_color_id]+=$req_qnty;
										$grey_req_color_arr[$fabric_color_id]+=$grey_req_qnty;
									}
								}
								else
								{
									$main_booking.="No Booking";
									$main_booking_excel.="No Booking";
									$sample_booking.="No Booking";
									$sample_booking_excel.="No Booking";
									$all_book_prefix_no = "&nbsp;";
								}

								if($main_booking=="")
								{
									$main_booking.="No Booking";
									$main_booking_excel.="No Booking";
								}

								if($sample_booking=="")
								{
									$sample_booking.="No Booking";
									$sample_booking_excel.="No Booking";
								}

								$all_book_prefix_no = implode(",", array_unique(explode(",", chop($all_book_prefix_no, ","))));
								$finish_color=array_unique(explode(",",$po_color_arr[$po_id]));
								foreach($finish_color as $color_id)
								{
									if($color_id>0)
									{
										$color_data_array[$color_id]+=0;
									}
								}
								//var_dump($color_data_array);
								$yarn_issue_array[$buyer_name]+=$yarn_issued;

								$grey_required_array[$buyer_name]+=$required_qnty;

								$net_trans_yarn=$trans_qnty_arr[$po_id]['yarn_trans'];

								$yarn_issue_array[$buyer_name]+=$net_trans_yarn;

								//$balance=$mkt_required_value-($yarn_issued+$net_trans_yarn);
								$fab_source=rtrim($fab_source_arr[$po_id],',');
								$fab_source_id=array_unique(explode(",",$fab_source));
								foreach($fab_source_id as $fsid)
								{
									if($fsid==1)
									{
										 $balance=$required_qnty-($yarn_issued+$net_trans_yarn);
									}
								}
								//$yetTo_allocate=$balance-$yarnAllocationQty;

								$dataYarnAllocation=explode(",",substr($yarnAllocationArr[$po_id],0,-1));

								//var_dump($dataYarnAllocation);
								$job_yetTo_allocate=0; $yetTo_allocate=0; $yarnAllocationQty=0;

								foreach($dataYarnAllocation as $yarnAllRow)
								{
									$yarnAlloRow=explode("**",$yarnAllRow);
									$yarn_count_id=$yarnAlloRow[0];
									$yarn_comp_type1st=$yarnAlloRow[1];
									$yarn_comp_percent1st=$yarnAlloRow[2];
									$yarn_comp_type2nd=$yarnAlloRow[3];
									$yarn_comp_percent2nd=$yarnAlloRow[4];
									$yarn_type_id=$yarnAlloRow[5];
									$yarnAllocationQty=$yarnAlloRow[6];
									$yarnDyedType=$yarnAlloRow[7];
									$fab_source=rtrim($fab_source_arr[$po_id],',');
									$fab_source_id=array_unique(explode(",",$fab_source));

									foreach($fab_source_id as $fsid)
									{
										if($fsid==1)
										{
										if($yarn_comp_percent2nd!=0)
										{
											$compostion_not_req=$composition[$yarn_comp_type1st]." ".$yarn_comp_percent1st." % ".$composition[$yarn_comp_type2nd]." ".$yarn_comp_percent2nd." %";
										}
										else
										{
											$compostion_not_req=$composition[$yarn_comp_type1st]." ".$yarn_comp_percent1st." % ".$composition[$yarn_comp_type2nd];
										}

										$desc=$yarn_count_details[$yarn_count_id]." ".$compostion_not_req." ".$yarn_type[$yarn_type_id];
										$req_allocation=$req_for_allocate_arr[$desc];

										if($yarnDyedType==1)
										{
											$job_yarnAutoAllocationQty+=$yarnAllocationQty;

										}
										else
										{
											$job_yarnAllocationQty+=$yarnAllocationQty;
										}


										//$yetTo_allocate=$required_qnty-$job_yarnAllocationQty;
										//$job_yetTo_allocate+=$yetTo_allocate;

										if(!in_array($desc,$yarn_desc_array))
										{
											if($yarnDyedType==1)
											{

												$yarn_autoallocated_arr['not_req']+=$yarnAllocationQty;
											}
											else
											{
												$yarn_allocated_arr['not_req']+=$yarnAllocationQty;
											}

											$yarn_allocation_arr['not_req']+=$yarnAllocationQty;
										}
										else
										{
											if($yarnDyedType==1)
											{
												//$yarn_autoallocated_arr[$desc]+=$yarnAllocationQty;
											}
											else
											{
												$yarn_allocated_arr[$desc]+=$yarnAllocationQty;
											}
											$yarn_allocation_arr[$desc]+=$yarnAllocationQty;
										}
									  }
									}
								}
								//var_dump($yarn_autoallocated_arr);

								//$yarnAllocationArr

								$yarn_balance_array[$buyer_name]+=$balance;
								$net_trans_knit=$trans_qnty_arr[$po_id]['knit_trans'];
								//$knitted_array[$buyer_name]+=$net_trans_knit;
								$grey_available=0; $grey_in_hand=0;
								$grey_available=($grey_production_qnty+$grey_purchase_qnty+$net_trans_knit);
								//$grey_available_arr[$po_id];
								$grey_in_hand=$grey_available-$grey_fabric_issue;

								$grey_balance=$required_qnty-$grey_available;//-($grey_recv_qnty+$net_trans_knit+$grey_purchase_qnty);
								$grey_prod_balance=$required_qnty-$grey_recv_qnty;
								$grey_del_store=$greyDeliveryArray[$po_id];
								$fab_source=rtrim($fab_source_arr[$po_id],',');
								$fab_source_id=array_unique(explode(",",$fab_source));
								foreach($fab_source_id as $fsid)
								{
									if($fsid==1)
									{
										$grey_del_store=$grey_del_store;$net_trans_knit=$net_trans_knit;
									}
									else
									{ //$grey_del_store=$net_trans_knit=0;
									}
								}
								$total_grey_del_store+=$grey_del_store;

								$grey_balance_array[$buyer_name]+=$grey_balance;

								$grey_issue_array[$buyer_name]+=$grey_fabric_issue;
								$receive_by_batch_array[$buyer_name]+=$receive_by_batch_qnt;

								$tot_order_qnty+=$order_qnty_in_pcs;
								$tot_mkt_required+=$job_mkt_required;

								// $tot_yarnAllocationQty+=$job_yarnAllocationQty;
								// $tot_yarnAutoAllocationQty+=$job_yarnAutoAllocationQty;


								$tot_yarn_issue_qnty+=$yarn_issued;
								$tot_fabric_req+=$required_qnty;
								$tot_balance+=$balance;
								$tot_grey_recv_qnty+=$grey_recv_qnty;
								$tot_grey_production_qnty+=$grey_production_qnty;
								$tot_grey_purchase_qnty+=$grey_purchase_qnty;
								$tot_grey_balance+=$grey_balance;
								$tot_grey_prod_balance+=$grey_prod_balance;
								$tot_grey_issue+=$grey_fabric_issue;
								$tot_receive_by_batch+=$receive_by_batch_qnt;

								$tot_grey_available+=$grey_available;
								//$required_qnty;
								$yarn_iss_plan_date_fin=""; $yarn_iss_actual_date_fin="";
								$yarn_iss_plan_date_fin=$tna_plan_actual_arr[$po_id][50]['plan'];
								$yarn_iss_actual_date_fin=$tna_plan_actual_arr[$po_id][50]['actual'];

								$gray_prod_plan_date_fin=""; $gray_prod_actual_date_fin="";
								$gray_prod_plan_date_fin=$tna_plan_actual_arr[$po_id][60]['plan'];
								$gray_prod_actual_date_fin=$tna_plan_actual_arr[$po_id][60]['actual'];

								$dye_prod_plan_date_fin=""; $dye_prod_actual_date_fin="";
								$dye_prod_plan_date_fin=$tna_plan_actual_arr[$po_id][61]['plan'];
								$dye_prod_actual_date_fin=$tna_plan_actual_arr[$po_id][61]['actual'];

								$fin_fab_plan_date_fin=""; $fin_fab_actual_date_fin="";
								$fin_fab_plan_date_fin=$tna_plan_actual_arr[$po_id][73]['plan'];
								$fin_fab_actual_date_fin=$tna_plan_actual_arr[$po_id][73]['actual'];

								$yarn_color_td="";
								if($yarn_iss_plan_date_fin<$yarn_iss_actual_date_fin) $yarn_color_td='#FF0000';


								$current_date=date("Y-m-d");
								if($gray_prod_plan_date_fin=="" || $gray_prod_plan_date_fin=="0000-00-00") $gray_prod_color_td="";
								else if($current_date>$gray_prod_plan_date_fin && ($gray_prod_actual_date_fin=="" || $gray_prod_actual_date_fin=="0000-00-00")) $gray_prod_color_td="#FF0000";
								else if(!($gray_prod_actual_date_fin=="" || $gray_prod_actual_date_fin=="0000-00-00") && $gray_prod_actual_date_fin>$gray_prod_plan_date_fin) $gray_prod_color_td="#33CCFF";
								else if(($gray_prod_actual_date_fin<=$gray_prod_plan_date_fin) && ($gray_prod_plan_date_fin!="" || $gray_prod_plan_date_fin!="0000-00-00")) $gray_prod_color_td="#008000";
								else $gray_prod_color_td="";


								if($dye_prod_plan_date_fin=="" || $dye_prod_plan_date_fin=="0000-00-00") $dye_prod_color_td="";
								else if($current_date>$dye_prod_plan_date_fin && ($dye_prod_actual_date_fin=="" || $dye_prod_actual_date_fin=="0000-00-00")) $dye_prod_color_td="#FF0000";
								else if(!($dye_prod_actual_date_fin=="" || $dye_prod_actual_date_fin=="0000-00-00") && $dye_prod_actual_date_fin>$dye_prod_plan_date_fin) $dye_prod_color_td="#33CCFF";
								else if(($dye_prod_actual_date_fin<=$dye_prod_plan_date_fin) && ($dye_prod_plan_date_fin!="" || $dye_prod_plan_date_fin!="0000-00-00")) $dye_prod_color_td="#008000";
								else $dye_prod_color_td="";
								//echo $dye_prod_color_td;
								if($current_date>$fin_fab_plan_date_fin && ($fin_fab_actual_date_fin=="" || $fin_fab_actual_date_fin=="0000-00-00")) $fin_prod_color_td="#FF0000";
								else if(!($fin_fab_actual_date_fin=="" || $fin_fab_actual_date_fin=="0000-00-00") && $fin_fab_actual_date_fin>$fin_fab_plan_date_fin) $fin_prod_color_td="#33CCFF";
								else if($fin_fab_actual_date_fin<=$fin_fab_plan_date_fin) $fin_prod_color_td="#008000";
								else $fin_prod_color_td="";



								if($required_qnty>$job_mkt_required) $bgcolor_grey_td='#FF0000'; $bgcolor_grey_td='';

								$po_entry_date=date('d-m-Y', strtotime($insert_date));
								$costing_date=$costing_date_library[$job_no];

								$tot_color=count($color_data_array);
								//echo $tot_color.'kkk';
								$grey_cons=0; $fin_cons=0;
								//echo $country_ship_qty.'=='.$po_id;
								$grey_cons=$fabric_costing_arr['knit']['grey'][$po_id]/$plan_cut_qnty;//$reqArr[$job_no]['grey']/$dzn_qnty;
								$fin_cons=$fabric_costing_arr['knit']['finish'][$po_id]/$plan_cut_qnty;//$reqArr[$job_no]['finish']/$dzn_qnty;
								if($tot_color>0)
								{
									$z=1;
									foreach($color_data_array as $key=>$value)
									{
										if($z==1)
										{
											$display_font_color="";
											$font_end="";
										}
										else
										{
											$display_font_color="<font style='display:none' color='$bgcolor'>";
											$font_end="</font>";
										}
										$batch_qnty=$batch_qnty_arr[$po_id][$key];
										$fin_delivery_qty=$finDeliveryArray[$po_id][$key];
										$fab_source=rtrim($fab_source_arr[$po_id],',');
										$fab_source_id=array_unique(explode(",",$fab_source));
										foreach($fab_source_id as $fsid)
										{
											if($fsid==1)
											{
												$batch_qnty=$batch_qnty;$fin_delivery_qty=$fin_delivery_qty;
											}
											else
											{
												//$batch_qnty=$fin_delivery_qty=0;
											}
										}
										$batch_qnty_array[$buyer_name]+=$batch_qnty;
										//$tot_batch_qnty+=$batch_qnty;


										if($z==1)
										{
											$html.="<tr bgcolor='".$bgcolor."'>
													<td align='left'>".$i."</td>
													<td align='left'>".$main_booking_excel."</td>
													<td align='left'>".$sample_booking_excel."</td>
													<td align='left'>".$all_book_prefix_no."</td>
													<td align='center'>".$job_no."</td>
													<td align='center'><img  src='../../".$imge_arr[$job_no]."' height='25' width='30' /></td>
													<td align='left'>".$po_number."</td>
													<td align='left'>".$order_status[$is_confirmed]."</td>
													<td>".$buyer_short_name_library[$buyer_name]."</td>
													<td align='left'>".$style_ref_no."</td>
													<td align='left'>".$file_no."</td>
													<td align='left'>".$grouping."</td>
													<td align='left'>".$gmts_item."</td>
													<td align='right'>".$order_qnty_in_pcs."</td>
													<td align='left'>".change_date_format($pub_shipment_date)."</td>
													<td align='center'>".change_date_format($po_received_date)."</td>
													<td align='center'>".$po_entry_date."</td>
													<td>".$shipment_status[$shiping_status]."</td>
													<td align='center'>".$contry_ship_date."</td>
													<td align='right'>".$country_ship_qty."</td>
													<td align='right'>".$grey_cons."</td>
													<td align='right'>".$fin_cons."</td>";
											$lead_time=0;
											$lead_time=datediff('d',$po_received_date,$pub_shipment_date);
											if($lead_time>0) $lead_time=$lead_time-1;

											$html_short.="<tr bgcolor='".$bgcolor."'>
														<td align='left'>".$i."</td>
														<td align='left'>".$main_booking_excel."</td>
														<td align='left'>".$sample_booking_excel."</td>
														<td align='left'>".$all_book_prefix_no."</td>
														<td align='left'>".$po_number."</td>
														<td>".$buyer_short_name_library[$buyer_name]."</td>
														<td>".$file_no."</td>
														<td>".$grouping."</td>
														<td align='right'>".$order_qnty_in_pcs."</td>
														<td align='left'>".change_date_format($pub_shipment_date)."</td>";
											$html_medium.="<tr bgcolor='".$bgcolor."'>
														<td align='left'>".$i."</td>
														<td>".$buyer_short_name_library[$buyer_name]."</td>
														<td align='left'>".$main_booking_excel."</td>
														<td align='left'>".$sample_booking_excel."</td>
														<td align='left'>".$all_book_prefix_no."</td>
														<td align='left'>".$po_number."</td>
														<td align='left'>".$grouping."</td>
														<td align='left'>".$file_no."</td>
														<td align='left'>".$style_ref_no."</td>

														<td align='right'>".$order_qnty_in_pcs."</td>
														<td align='left'>".change_date_format($pub_shipment_date)."</td>
														<td align='left'>".$lead_time."</td>";
										}
										else
										{
											$html.="<tr bgcolor='".$bgcolor."'>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>";

											$html_short.="<tr bgcolor='".$bgcolor."'>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>";
											$html_medium.="<tr bgcolor='".$bgcolor."'>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>";
										}
									?>
										<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
											<td width="40"><? echo $display_font_color.$i.$font_end; ?></td>
											<td width="125"><? echo $display_font_color.$main_booking.$font_end; ?></td>
											<td width="125"><? echo $display_font_color.$sample_booking.$font_end; ?></td>
                                            <td width="75" style="word-break:break-all"><? echo $display_font_color.$all_book_prefix_no.$font_end; ?></td>
											<td width="100" align="center"><? echo $display_font_color.$job_no.$font_end; ?></td>
                                            <td width="40" onClick="openmypage_image('requires/fabric_receive_status_report2_controller.php?action=show_image&job_no=<?=$job_no; ?>','Image View')"><?=$display_font_color; ?><img  src="../../<?=$imge_arr[$job_no]; ?>" height='25' width='30' /><?=$font_end; ?></td>
											<td width="120">
												<p>
													<a href='#report_details' onClick="progress_comment_popup('<? echo $job_no; ?>','<? echo $po_id; ?>','<? echo $template_id; ?>','<? echo $tna_process_type; ?>');"><? echo $display_font_color.$po_number.$font_end;  ?></a>
												</p>
											</td>
											<td width="90" align="center"><? echo $display_font_color.$order_status[$is_confirmed].$font_end; ?></td>
											<td width="80"><p><? echo $display_font_color.$buyer_short_name_library[$buyer_name].$font_end; ?></p></td>
											<td width="130"><p><? echo $display_font_color.$style_ref_no.$font_end; ?></p></td>
											<td width="100"><p><? echo $file_no; ?></p></td>
											<td width="100"><p><? echo $grouping; ?></p></td>
											<td width="140"><p><? echo $display_font_color.$gmts_item.$font_end; ?></p></td>
											<td width="100" align="right"><? if($z==1) echo number_format($order_qnty_in_pcs,0,'.',''); ?></td>
											<td width="80" align="center"><? echo $display_font_color.change_date_format($pub_shipment_date).$font_end; ?></td>
											<td width="80" align="center"><? echo $display_font_color.change_date_format($po_received_date).$font_end; ?></td>
											<td width="80" align="center"><? echo $display_font_color.$po_entry_date.$font_end; ?></td>
											<td width="100" align="center"><? echo $display_font_color.$shipment_status[$shiping_status].$font_end; ?></td>
											<td width="80"><p><? echo $display_font_color.$contry_ship_date.$font_end; ?></p></td>

											<? if($country_ship_qty>0)
										   {
											   ?>
											<td width="100" align="right"><a href="##" onClick="country_order_dtls('<? echo $po_id; ?>','<? echo $start_date; ?>','<? echo $end_date; ?>','<? echo $buyer_name; ?>','<? echo $job_no; ?>','country_order_dtls_popup')"><? if($z==1) echo number_format($country_ship_qty); ?></a></td>
											<? }
											else
											{
											?>
											<td width="100" align="right"> <? if($z==1) echo number_format($country_ship_qty); ?></td>
											<? } ?>
											<td width="100" align="right" title="<? echo $fabric_costing_arr['knit']['grey'][$po_id]."/".$plan_cut_qnty; ?>"><? if($z==1) echo number_format($grey_cons,5,'.',''); ?></td>
											<td width="100" align="right" title="<? echo $fabric_costing_arr['knit']['finish'][$po_id]."/".$plan_cut_qnty; ?>"><? if($z==1) echo number_format($fin_cons,5,'.',''); ?></td>

											<td width="70">
												<?
													 $html.="<td>"; $d=1;
													 foreach($yarn_data_array['count'] as $yarn_count_value)
													 {
														if($d!=1)
														{
															echo $display_font_color."<hr/>".$font_end;
															if($z==1) $html.="<hr/>";
														}
														//else $html.="kausar";
														echo $display_font_color.$yarn_count_value.$font_end;
														if($z==1) $html.=$yarn_count_value;
													 $d++;
													 }
													 $html.="</td><td>";
												?>
											</td>
											<td width="110">
												<div style="word-wrap:break-word; width:110px">
													<?
														 $d=1;
														 foreach($yarn_data_array['comp'] as $yarn_composition_value)
														 {
															if($d!=1)
															{
																echo $display_font_color."<hr/>".$font_end;
																if($z==1) $html.="<hr/>";
															}
															echo $display_font_color.$yarn_composition_value.$font_end;
															if($z==1) $html.=$yarn_composition_value;
														 $d++;
														 }
														 $html.="</td><td>";
													?>
												</div>
											</td>
											<td width="80">
												<p>
													<?
														 $d=1;
														 foreach($yarn_data_array['type'] as $yarn_type_value)
														 {
															if($d!=1)
															{
																echo $display_font_color."<hr/>".$font_end;
																if($z==1) $html.="<hr/>";
															}

															echo $display_font_color.$yarn_type_value.$font_end;
															if($z==1) $html.=$yarn_type_value;
														 $d++;
														 }
														 $html.="</td><td>";
													?>
												</p>
											</td>
											<td width="100" align="right">
												<?
													if($z==1)
													{
														echo "<font color='$bgcolor' style='display:none'>".number_format(array_sum($mkt_required_array),2,'.','')."</font>\n";
														$d=1;
														$tot_mkt_required_value=0;
														foreach($mkt_required_array as $mkt_required_value)
														{
															if($d!=1)
															{
																echo "<hr/>";
																$html.="<hr/>";
															}
															$yarn_desc_for_popup_req=explode("__",$yarn_desc_array_for_popup[$d]);
															?>
															 <? echo number_format($mkt_required_value,2,'.','');

															 $tot_mkt_required_value+=$mkt_required_value;?>
														<?
														$html.=number_format($mkt_required_value,2);
														$d++;
														}
													}
													$html.="</td><td bgcolor='$discrepancy_td_color'>"; $html_short.="<td>"; $html_medium.="<td>";
												?>
											</td>
											<td width="100" align="right">
												<?
													if($z==1)
													{
														echo "<font color='$bgcolor' style='display:none'>".number_format($job_yarnAllocationQty,2,'.','')."</font>\n";
														$d=1;
														foreach($yarn_desc_array as $yarn_desc)
														{
															if($d!=1)
															{
																echo "<hr/>";
																$html.="<hr/>";
															}

															//$yarn_allo_qnty=$yarn_allocation_arr[$yarn_desc];
															$yarn_allo_qnty=$yarn_allocated_arr[$yarn_desc];
															$tot_yarnAllocationQty+=$yarn_allocated_arr[$yarn_desc];

															$yarn_desc_for_popup=explode("__",$yarn_desc_array_for_popup[$d]);
															//echo $fab_source_arr[$po_id];
															?>

															<a href="##" onClick="openmypage('<? echo $po_id; ?>','yarn_allocation_pop','<? echo $yarn_desc_for_popup[0]; ?>','<? echo $yarn_desc_for_popup[1]; ?>','<? echo $yarn_desc_for_popup[2]; ?>','<? echo $yarn_desc_for_popup[3]; ?>','<? echo $yarn_desc_for_popup[4]; ?>','<? echo $yarn_desc_for_popup[5]; ?>',2)"><? echo number_format($yarn_allo_qnty,2,'.','');?></a>
															<?
															$html.=number_format($yarn_allo_qnty,2);
															$d++;
														}

														if($d!=1)
														{
															echo "<hr/>";
															$html.="<hr/>";
														}

														$yarn_desc=join(",",$yarn_desc_array);

														//$allo_qnty_not_req=$yarn_allocation_arr['not_req'];
														$allo_qnty_not_req=$yarn_allocated_arr['not_req'];
														$tot_yarnAllocationQty+=$yarn_allocated_arr['not_req'];
														$html.=number_format($allo_qnty_not_req,2);
														//$html_short.=number_format($iss_qnty_not_req+$yarn_issued,2);
														?>
														<a href="##" onClick="openmypage('<? echo $po_id; ?>','yarn_allocation_not','<? echo $yarn_desc; ?>','','','','','',2)"><? echo number_format($allo_qnty_not_req,2);?></a>
													<?
													}
													$html.="</td><td>";
												?>
											</td>
											<?
											if($type==1){?>
											<td width="100" align="right">
												<?
													if($z==1)
													{
														echo "<font color='$bgcolor' style='display:none'>".number_format($job_yarnAutoAllocationQty,2,'.','')."</font>\n";
														$d=1;
														foreach($yarn_desc_array as $yarn_desc)
														{
															if($d!=1)
															{
																echo "<hr/>";
																$html.="<hr/>";
															}

															//$yarn_allo_qnty=$yarn_allocation_arr[$yarn_desc];
															$yarn_allo_qnty=$yarn_autoallocated_arr[$yarn_desc];
															$tot_yarnAutoAllocationQty+=$yarn_autoallocated_arr[$yarn_desc];
															$yarn_desc_for_popup=explode("__",$yarn_desc_array_for_popup[$d]);
															//echo $fab_source_arr[$po_id];
															?>

															<a href="##" onClick="openmypage('<? echo $po_id; ?>','yarn_allocation_pop','<? echo $yarn_desc_for_popup[0]; ?>','<? echo $yarn_desc_for_popup[1]; ?>','<? echo $yarn_desc_for_popup[2]; ?>','<? echo $yarn_desc_for_popup[3]; ?>','<? echo $yarn_desc_for_popup[4]; ?>','<? echo $yarn_desc_for_popup[5]; ?>',1)"><? echo number_format($yarn_allo_qnty,2,'.','');?></a>
															<?
															$html.=number_format($yarn_allo_qnty,2);
															$d++;
														}

														if($d!=1)
														{
															echo "<hr/>";
															$html.="<hr/>";
														}

														$yarn_desc=join(",",$yarn_desc_array);

														//$allo_qnty_not_req=$yarn_allocation_arr['not_req'];
														$auto_allo_qnty_not_req=$yarn_autoallocated_arr['not_req'];
														$tot_yarnAutoAllocationQty+=$yarn_autoallocated_arr['not_req'];

														$html.=number_format($auto_allo_qnty_not_req,2);
														//$html_short.=number_format($iss_qnty_not_req+$yarn_issued,2);
														?>
														<a href="##" onClick="openmypage('<? echo $po_id; ?>','yarn_allocation_not','<? echo $yarn_desc; ?>','','','','','',1)"><? echo number_format($auto_allo_qnty_not_req,2);?></a>
													<?
													}
													$html.="</td><td>";
												?>
											</td>
											<? } ?>
											 <td width="100" align="right">
												<?
													if($z==1)
													{
														$job_yetTo_allocate=0;
														$fab_source=rtrim($fab_source_arr[$po_id],',');
														$fab_source_id=array_unique(explode(",",$fab_source));
														foreach($fab_source_id as $fsid)
														{
															if($fsid==1)
															//$job_yetTo_allocate=$required_qnty-$job_yarnAllocationQty;
															$job_yetTo_allocate=$tot_mkt_required_value-$job_yarnAllocationQty;
															//$job_yetTo_allocate=$tot_mkt_required_value;
														}
														echo "<font color='$bgcolor' style='display:none'>".number_format($job_yetTo_allocate,2,'.','')."</font>\n";
														$tot_yetTo_allocate+=$job_yetTo_allocate;
														echo number_format($job_yetTo_allocate,2,'.','');
														$html.=number_format($job_yetTo_allocate,2);
													}
													$html.="</td><td>";
												?>
											</td>
											<td width="100" align="right" bgcolor="<? echo $yarn_color_td; ?>">
												<?
													if($z==1)
													{
														echo "<font color='$bgcolor' style='display:none'>".number_format($yarn_issued,2,'.','')."</font>\n";
														$d=1;
														foreach($yarn_desc_array as $yarn_desc)
														{
															if($d!=1)
															{
																echo "<hr/>";
																$html.="<hr/>";
															}

															$yarn_iss_qnty=$yarn_iss_qnty_array[$yarn_desc];
															$yarn_desc_for_popup=explode("__",$yarn_desc_array_for_popup[$d]);

															?>
															<a href="##" onClick="openmypage('<? echo $po_id; ?>','yarn_issue','<? echo $yarn_desc_for_popup[0]; ?>','<? echo $yarn_desc_for_popup[1]; ?>','<? echo $yarn_desc_for_popup[2]; ?>','<? echo $yarn_desc_for_popup[3]; ?>','<? echo $yarn_desc_for_popup[4]; ?>','<? echo $yarn_desc_for_popup[5]; ?>')"><? echo number_format($yarn_iss_qnty,2,'.','');?></a>
															<?
															$html.=number_format($yarn_iss_qnty,2);
															$d++;
														}

														if($d!=1)
														{
															echo "<hr/>";
															$html.="<hr/>";
														}

														$yarn_desc=join(",",$yarn_desc_array);

														$iss_qnty_not_req=$yarn_iss_qnty_array['not_req'];

														$html.=number_format($iss_qnty_not_req,2);
														$html_medium.=number_format($yarn_issued,2);
														$html_short.=number_format(($iss_qnty_not_req+$yarn_iss_qnty),2);//$yarn_issued

														?>
														<a href="##" onClick="openmypage('<? echo $po_id; ?>','yarn_issue_not','<? echo $yarn_desc; ?>','','','','','')"><? echo number_format($iss_qnty_not_req,2);?></a>
													<?
													}
													?>
											</td>
											<? $html.="</td><td>"; ?>
											<td width="100" align="right">

											<?
												if($z==1)
												{
												?>
													<a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $po_id; ?>','yarn_trans','')"><? echo number_format($net_trans_yarn,2,'.','');  ?></a>
												<?
													$html.=number_format($net_trans_yarn,2);
													$tot_net_trans_yarn_qnty+=$net_trans_yarn;
												}
											?>
											</td>
											<? $html.="</td><td>"; $html_medium.="</td><td>"; ?>
											<td width="100" align="right" title="Grey Req.-(Yarn Issue+Net Transfer)">
											<?
												if($z==1)
												{
													echo number_format($balance,2,'.','');
													$html.=number_format($balance,2);
													$html_medium.=number_format($balance,2);
												}
											?>
											</td>
											<? $html.="</td><td bgcolor='$bgcolor_grey_td'>"; $html_short.="</td><td bgcolor='$bgcolor_grey_td'>"; $html_medium.="</td><td bgcolor='$bgcolor_grey_td'>"; ?>
											<td width="100" align="right" bgcolor="<? echo $bgcolor_grey_td; ?>">
											<?
												if($z==1)
												{
													echo number_format($required_qnty,2,'.','');
													$html.=number_format($required_qnty,2);
													$html_short.=number_format($required_qnty,2);
													$html_medium.=number_format($required_qnty,2);
												}
											?>
											</td>
											<? $html.="</td><td bgcolor='$discrepancy_td_color'>"; $html_short.="</td><td>";   $html_medium.="</td><td>"; ?>
											<td width="100" align="right" bgcolor="<? echo $gray_prod_color_td; ?>">
												<?
													if($z==1)
													{
													?>
														<a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $po_id; ?>','grey_receive','')"><? echo number_format($grey_recv_qnty,2,'.',''); ?></a>
													<?
														$html.=number_format($grey_recv_qnty,2);
														$html_short.=number_format($grey_recv_qnty,2);
														$html_medium.=number_format($grey_recv_qnty,2);
													}
												?>
											</td>
											<? $html.="</td><td bgcolor='$bgcolor_grey_td'>"; $html_medium.="</td><td bgcolor='$bgcolor_grey_td'>"; ?>
											<td width="100" align="right" title="(Grey Req-Prod)">
											<?
												if($z==1)
												{
													echo number_format($grey_prod_balance,2,'.','');
													$html.=number_format($grey_prod_balance,2);
													$html_medium.=number_format($grey_prod_balance,2);
												}
											?>
											</td>
											<? $html.="</td><td bgcolor='$bgcolor_grey_td'>"; ?>
											<td width="100" align="right">

											<?
												if($z==1)
												{
													?>
														<a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $po_id; ?>','grey_delivery_to_store','')"><? echo number_format($grey_del_store,2,'.',''); ?></a>
													<?
													$html.=number_format($grey_del_store,2);
												}
											?>
											</td>
											<? $html.="</td><td bgcolor='$bgcolor_grey_td'>"; ?>
											<td width="100" align="right">
											<?
												$greyKnitFloor=0;
												if($z==1)
												{
													$greyKnitFloor=$grey_recv_qnty-$grey_del_store;
													echo number_format($greyKnitFloor,2,'.','');
													$tot_greyKnitFloor+=$greyKnitFloor;
													$html.=number_format($greyKnitFloor,2);
												}
											?>
											</td>
											<? $html.="</td><td bgcolor='$bgcolor_grey_td'>"; ?>
											<td width="100" align="right">

												<?
													if($z==1)
													{
													?>
														<a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $po_id.'_9'; ?>','grey_purchase','')"><? echo number_format($grey_production_qnty,2,'.',''); ?></a>
													<?
														$html.=number_format($grey_production_qnty,2);
													}
												?>
											</td>
											<? $html.="</td><td bgcolor='$bgcolor_grey_td'>"; ?>
											<td width="100" align="right">
												<?
													if($z==1)
													{
													?>
														<a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $po_id.'_0'; ?>','grey_purchase','')"><? echo number_format($grey_purchase_qnty,2,'.',''); ?></a>
													<?
														$html.=number_format($grey_purchase_qnty,2);
													}
												?>
											</td>
											<? $html.="</td><td>"; ?>
											<td width="100" align="right">

												<?
													if($z==1)
													{
													?>
														<a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $po_id; ?>','grey_return','')"><? echo number_format($grey_net_return,2,'.',''); ?></a>
													<?
														$html.=number_format($grey_net_return,2);
														$tot_net_gray_return+=$grey_net_return;
													}
												?>
											</td>
											<? $html.="</td><td>"; ?>
											<td width="100" align="right" title="Item Transfer Receive - Item Transfer Issue">
											<?
												if($z==1)
												{
												?>
													<a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $po_id; ?>','knit_trans','')"><? echo number_format($net_trans_knit,2,'.','');  ?></a>
												<?
													$html.=number_format($net_trans_knit,2);
													$tot_net_trans_knit_qnty+=$net_trans_knit;
												}
											?>
											</td>
											<? $html.="</td><td>"; ?>
											<td width="100" align="right" title="Total Receive=( Grey Rcvd (Prod.) + Grey Rcvd (Purchase) + Net Transfer)">
											<?
												//$grey_available=0;
												//$grey_available=$grey_production_qnty+$grey_purchase_qnty+$net_trans_knit;
												if($z==1)
												{
													echo number_format($grey_available,2,'.','');
													$html.=number_format($grey_available,2);
													$knitted_array[$buyer_name]+=$grey_available;
													//$tot_net_trans_knit_qnty+=$net_trans_knit;
												}
											?>
											</td>
											<? $html.="</td><td>"; ?>
											<td width="100" align="right" title="Required (As per Booking) - Grey Actual Recv.">
												<?
													if($z==1)
													{
														echo number_format($grey_balance,2,'.','');
														$html.=number_format($grey_balance,2);
													}
												?>
											</td>
											<? $html.="</td><td>"; ?>
											<td width="100" align="right">

												<?
													if($z==1)
													{
													?>
														<a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? 
														$grey_fabric_issue=$grey_fabric_issue-$grey_net_return;
														echo $po_id; ?>','grey_issue','')"><? echo number_format($grey_fabric_issue,2,'.',''); ?></a>
													<?
														$html.=number_format($grey_fabric_issue,2);
													};
												?>
											</td>
											<? $html.="</td><td>"; ?>
                                            <td width="100" align="right" title="Grey Actual Recv.-Net Grey Issue">
												<?
													if($z==1)
													{
														echo number_format($grey_in_hand,2,'.','');
														$html.=number_format($grey_in_hand,2);
														$tot_grey_in_hand+=$grey_in_hand;
													};
												?>
											</td>
											<? $html.="</td><td>"; ?>
											<td width="100" align="right">
												<?
													if($z==1)
													{
														?>
                                                        	<a href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $po_id; ?>','receive_by_batch','')"><? echo number_format($receive_by_batch_qnt,2,'.',''); ?></a>
                                                        <?
														$html.=number_format($receive_by_batch_qnt,2);
													};
												?>
											</td>
											<? $html.="</td><td bgcolor='#FF9BFF'>"; $html_short.="</td><td bgcolor='#FF9BFF'>"; $html_medium.="</td><td bgcolor='#FF9BFF'>"; ?>
											<td width="100" align="center" bgcolor="#FF9BFF">
												<p>
													<?
														if($key==0)
														{
															echo "-";
															$html.="-"; $html_short.="-"; $html_medium.="-";
														}
														else
														{
															echo $color_array[$key].'<br>';
														   echo "<span style='font-size:10px;'>LD No. ".$lapdip_arr[$job_no.$po_id.$key].'</span>';
															$html.=$color_array[$key]; $html_short.=$color_array[$key]; $html_medium.=$color_array[$key];
														}
													?>
												</p>
											</td>
											<? $html.="</td><td>"; $html_short.="</td>"; $html_medium.="</td>"; ?>
											<td width="100" align="right">

												<?
													$grey_req_color_qty=0;
													$grey_req_color_qty=$grey_req_color_arr[$key];
													$html.=number_format($grey_req_color_qty,2);
													$tot_grey_req_color_qty+=$grey_req_color_qty;

												echo number_format($grey_req_color_qty,2,'.',''); ?>
											</td>
										   <? $html.="</td><td>"; ?>
											<td width="100" align="right">
												<?
													$batch_color_qnty=0;
													$batch_color_qnty=$batch_qnty_arr[$po_id][$key];
													$html.=number_format($batch_color_qnty,2);
													$tot_batch_qnty_excel+=$batch_color_qnty;
													$tot_batch_qnty+=$batch_color_qnty;

												?>
													<a href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $po_id.'_'.$key; ?>','batch_qnty','')"><? echo number_format($batch_color_qnty,2,'.',''); ?></a>
											</td>
										   <?
												$html.="</td><td>"; $html_short.="<td>"; $html_medium.="<td>";

												$fab_rec_return_production=$finish_recv_rtn_qnty_arr[$po_id][$key]['production'];
												$fab_rec_return_purchase=$finish_recv_rtn_qnty_arr[$po_id][$key]['purchase'];

												//$finish_recv_rtn_qnty_arr[$po_id][$key];
												$fab_issue_return=$finish_issue_rtn_qnty_arr[$po_id][$key];
												$fab_net_return=$fab_issue_return-($fab_rec_return_production+$fab_rec_return_purchase);

												$fab_recv_qnty=$finish_receive_qnty_arr[$po_id][$key];
												$fab_production_qnty=$finish_purchase_qnty_arr[$po_id][$key]['production']-$fab_rec_return_production;
												$fab_purchase_qnty=$finish_purchase_qnty_arr[$po_id][$key]['purchase']-$fab_rec_return_purchase;
												$issue_to_cut_qnty=$finish_issue_qnty_arr[$po_id][$key]-$fab_issue_return;

												$dye_qnty=$dye_qnty_arr[$po_id][$key];
												$fab_source=rtrim($fab_source_arr[$po_id],',');
												$fab_source_id=array_unique(explode(",",$fab_source));
												rsort($fab_source_id);
												foreach($fab_source_id as $fsid)
												{
													if($fsid==1)
													{
														$dye_qnty=$dye_qnty;$fab_recv_qnty=$fab_recv_qnty;$fab_production_qnty=$fab_production_qnty;
													}
													else
													{
														//$dye_qnty=$fab_recv_qnty=$fab_production_qnty=0;
													}
												}
											?>
											<td width="100" align="right" bgcolor="<? echo $dye_prod_color_td; ?>">
												<a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $po_id; ?>','dye_qnty','<? echo $key; ?>')"><? echo number_format($dye_qnty,2,'.',''); ?></a>
												<?
													$html.=number_format($dye_qnty,2);
													$html_short.=number_format($dye_qnty,2);
													$html_medium.=number_format($dye_qnty,2);

													$dye_qnty_array[$buyer_name]+=$dye_qnty;
													$tot_dye_qnty+=$dye_qnty;
												?>
											</td>
											<? $html.="</td><td>"; $html_short.="</td><td>"; $html_medium.="</td><td>"; ?>

											<td width="100" align="right" title="Grey Req. Color - Dye Qty">
												<?
													$grey_balance_color_qty=0;
													$grey_balance_color_qty=$grey_req_color_qty-$dye_qnty;
													$html.=number_format($grey_balance_color_qty,2);
													$tot_grey_balance_color_qty+=$grey_balance_color_qty;

												echo number_format($grey_balance_color_qty,2,'.',''); ?>
											</td>
										   <? $html.="</td><td>"; ?>
											<td width="100" align="right">
												<?
													$dyeing_balance=$batch_color_qnty-$dye_qnty;
													echo number_format($dyeing_balance,2,'.','');
													$html.=number_format($dyeing_balance,2);
													$html_medium.=number_format($dyeing_balance,2);
													//$tot_dye_qnty+=$dyeing_balance;
													$tot_dye_qnty_balance+=$dyeing_balance;
												?>
											</td>
										   <td width="100" align="right">
												<?
													$html.="</td><td>";

													echo number_format($value,2,'.','');
													$html.=number_format($value,2);

													$fin_fab_Requi_array[$buyer_name]+=$value;
													$tot_color_wise_req+=$value;
												?>
											</td>
											<? $html.="</td><td>"; $html_medium.="</td><td>"; ?>
											<td width="100" align="right" bgcolor="<? echo $fin_prod_color_td; ?>">
												<a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $po_id; ?>','fabric_receive','<? echo $key; ?>')"><? echo number_format($fab_recv_qnty,2,'.',''); ?></a>
												<?
													$html.=number_format($fab_recv_qnty,2);
													$html_short.=number_format($fab_recv_qnty,2);
													$html_medium.=number_format($fab_recv_qnty,2);

													$finFabProductionArr[$buyer_name]+=$fab_recv_qnty;
													$tot_fabric_recv+=$fab_recv_qnty;
													$tot_fabric_recv_excel+=$fab_recv_qnty;
												?>
											</td>
											<? $html.="</td><td>"; $html_medium.="</td><td>"; ?>
											<td width="100" align="right">

												<?

													$finish_balance=$value-$fab_recv_qnty;

													echo number_format($finish_balance,2,'.','');
													$html.=number_format($finish_balance,2);
													$html_medium.=number_format($finish_balance,2);
													//$fin_fab_recei_array[$buyer_name]+=$finish_balance;
													$tot_fabric_recv_balance+=$finish_balance;
												?>
											</td>
											<? $html.="</td><td>"; ?>
											<td width="100" align="right">
													<a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $po_id.'_'.$key; ?>','finish_delivery_to_store','')"><? echo number_format($fin_delivery_qty,2,'.',''); ?></a>
													<?
													$html.=number_format($fin_delivery_qty,2);
													//$fin_fab_recei_array[$buyer_name]+=$fin_delivery_qty;
													$tot_fin_delivery_qty+=$fin_delivery_qty;
												?>
											</td>
											<? $html.="</td><td>"; ?>
											<td width="100" align="right">
												<?
													$finProdFloor=$fab_recv_qnty-$fin_delivery_qty;
													echo number_format($finProdFloor,2,'.','');
													$html.=number_format($finProdFloor,2);
													//$fin_fab_recei_array[$buyer_name]+=$fin_delivery_qty;
													$tot_finProdFloor+=$finProdFloor;
												?>
											</td>
											<? $html.="</td><td>"; ?>
											<td width="100" align="right">
												<a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $po_id.'_'.'9'; ?>','fabric_purchase','<? echo $key; ?>')"><? echo number_format($fab_production_qnty,2,'.',''); ?></a>
												<?
													$html.=number_format($fab_production_qnty,2);

													//$fin_fab_recei_array[$buyer_name]+=$fab_purchase_qnty;
													$tot_fabric_production+=$fab_production_qnty;
												?>
											</td>
											<? $html.="</td><td>"; ?>
											<td width="100" align="right" title="Data comes purchase booking from here">

												<a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $po_id.'_'.'0'; ?>','fabric_purchase','<? echo $key; ?>')"><? echo number_format($fab_purchase_qnty,2,'.',''); ?></a>
												<?
													$html.=number_format($fab_purchase_qnty,2);

													//$fin_fab_recei_array[$buyer_name]+=$fab_purchase_qnty;
													$tot_fabric_purchase+=$fab_purchase_qnty;
												?>
											</td>
											<? $html.="</td><td>"; ?>
											<td width="100" align="right">
												<a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $po_id; ?>','finish_return','<? echo $key; ?>')"><? echo number_format($fab_net_return,2,'.',''); ?></a>
												<?
													$html.=number_format($fab_net_return,2);

													//$fin_fab_recei_array[$buyer_name]+=$fab_purchase_qnty;
													$tot_fab_net_return+=$fab_net_return;
												?>
											</td>
											<? $html.="</td><td>"; ?>
											<td width="100" align="right" >
												<?
													$net_trans_finish=$trans_qnty_fin_arr[$po_id][$key]['trans'];
													//$fin_fab_recei_array[$buyer_name]+=$net_trans_finish;
												?>
													<a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $po_id; ?>','finish_trans','<? echo $key; ?>')"><? echo number_format($net_trans_finish,2,'.','');  ?></a>
												<?
													$html.=number_format($net_trans_finish,2);
													$tot_net_trans_finish_qnty+=$net_trans_finish;
													$fabric_balance=$value-($fab_recv_qnty+$fab_purchase_qnty+$net_trans_finish);
												?>
											</td>
											<? $html.="</td><td>"; ?>
											<td width="100" align="right" title="Total Receive=(Received Prod. + Received Purchase + Net Transfer)">
												<?
													//$fabric_available=$finish_available_arr[$po_id][$key];
													$fabric_available=$fab_production_qnty+$fab_purchase_qnty+$net_trans_finish;
													//$value-($fab_production_qnty+$fab_purchase_qnty+$net_trans_finish+$fab_net_return);
													$fin_fab_recei_array[$buyer_name]+=$fabric_available;
													echo number_format($fabric_available,2,'.','');
													$html.=number_format($fabric_available,2);
													$tot_fabric_available+=$fabric_available;
												?>
											</td>
											<? $html.="</td><td>"; ?>
											<td width="100" align="right" title="Req. Qty (As Per Booking)-Fin. Fab. Actual Recv.">
												<?
													$fabric_receive_bal=$value-$fabric_available;
													echo number_format($fabric_receive_bal,2,'.','');
													$fin_balance_array[$buyer_name]+=$fabric_receive_bal;
													$html.=number_format($fabric_receive_bal,2);
													$tot_fabric_rec_bal+=$fabric_receive_bal;
												?>
											</td>
											<? $html.="</td><td>"; $html_short.="</td><td>"; $html_medium.="</td><td>"; ?>
											<td width="100" align="right">
												<a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $po_id; ?>','issue_to_cut','<? echo $key; ?>')"><? echo number_format($issue_to_cut_qnty,2,'.',''); ?></a>
												<?
													$html.=number_format($issue_to_cut_qnty,2);
													$html_short.=number_format($issue_to_cut_qnty,2);
													$html_medium.=number_format($issue_to_cut_qnty,2);
													$issue_toCut_array[$buyer_name]+=$issue_to_cut_qnty;
													$tot_issue_to_cut_qnty+=$issue_to_cut_qnty;
												?>
											</td>
											<? $html.="</td><td>"; ?>
											<td width="100" align="right" title="Req. Qty (As Per Booking)-Net Issue to Cutting"><? $yet_to_cut_qty=$value-$issue_to_cut_qnty; echo number_format($yet_to_cut_qty,2,'.',''); $tot_yet_to_cut+=$yet_to_cut_qty; $html.=number_format($yet_to_cut_qty,2); ?></a>
											</td>
											<? $html.="</td><td>"; ?>
											<td width="100" align="right" title="Fin. Fab. Actual Recv.-Net Issue to Cutting">
												<?
													$fabric_left_over=$fabric_available-$issue_to_cut_qnty;
													echo number_format($fabric_left_over,2,'.','');
													$html.=number_format($fabric_left_over,2);
													$tot_fabric_left_over+=$fabric_left_over;
													$tot_fabric_left_over_excel+=$fabric_left_over;
												?>
											</td>
											<? $html.="</td><td>"; ?>
											<td width="100" align="right">
												<?
													//$fabric_left_over=($fab_recv_qnty+$fabric_available)-$issue_to_cut_qnty;
													//echo number_format($fabric_left_over,2,'.','');
													//$html.=number_format($fabric_left_over,2);
													//$tot_fabric_left_over+=$fabric_left_over;
												?>
											</td>
											<td>

												<p>
													<? $fabric_desc=explode(",",$fabric_desc_details[$job_no]); echo $display_font_color.join(",<br>",array_unique($fabric_desc)).$font_end; ?>
												</p>
											</td>
										</tr>
									<?
										if($z==1) $html.="</td><td>".join(",<br>",array_unique($fabric_desc))."</td></tr>"; else $html.="</td><td>&nbsp;</td></tr>";
										$html_short.="</td></tr>";
										$html_medium.="</td></tr>";
									$z++;
									$k++;
									}
								}
								else
								{
									$html.="<tr bgcolor='".$bgcolor."'>
													<td align='left'>".$i."</td>
													<td align='left'>".$main_booking_excel."</td>
													<td align='left'>".$sample_booking_excel."</td>
													<td align='left'>".$all_book_prefix_no."</td>
													<td align='center'>".$job_no."</td>
													<td align='center'><img  src='../../".$imge_arr[$job_no]."' height='25' width='30' /></td>
													<td align='left'>".$po_number."</td>
													<td align='left'>".$order_status[$is_confirmed]."</td>
													<td>".$buyer_short_name_library[$buyer_name]."</td>
													<td align='left'>".$style_ref_no."</td>
													<td align='left'>".$file_no."</td>
													<td align='left'>".$grouping."</td>

													<td align='left'>".$gmts_item."</td>
													<td align='right'>".$order_qnty_in_pcs."</td>
													<td align='left'>".change_date_format($pub_shipment_date)."</td>
													<td align='center'>".change_date_format($po_received_date)."</td>
													<td align='center'>".$po_entry_date."</td>
													<td>".$shipment_status[$shiping_status]."</td>
													<td align='center'>".$contry_ship_date."</td>
													<td align='right'>".$country_ship_qty."</td>
													<td align='right'>".$grey_cons."</td>
													<td align='right'>".$fin_cons."</td>";

									$lead_time=0;
									$lead_time=datediff('d',$po_received_date,$pub_shipment_date);
									if($lead_time>0) $lead_time=$lead_time-1;

									$html_short.="<tr bgcolor='".$bgcolor."'>
												<td align='left'>".$i."</td>
												<td align='left'>".$main_booking_excel."</td>
												<td align='left'>".$sample_booking_excel."</td>
												<td align='left'>".$all_book_prefix_no."</td>
												<td align='left'>".$po_number."</td>
												<td>".$buyer_short_name_library[$buyer_name]."</td>
												<td align='left'>".$file_no."</td>
												<td align='left'>".$grouping."</td>
												<td align='right'>".$order_qnty_in_pcs."</td>
												<td align='left'>".change_date_format($pub_shipment_date)."</td>";

									$html_medium.="<tr bgcolor='".$bgcolor."'>
												<td align='left'>".$i."</td>
												<td>".$buyer_short_name_library[$buyer_name]."</td>
												<td align='left'>".$main_booking_excel."</td>
												<td align='left'>".$sample_booking_excel."</td>
												<td align='left'>".$all_book_prefix_no."</td>
												<td align='left'>".$po_number."</td>
												<td align='left'>".$grouping."</td>
												<td align='left'>".$file_no."</td>
												<td align='left'>".$style_ref_no."</td>

												<td align='right'>".$order_qnty_in_pcs."</td>
												<td align='left'>".change_date_format($pub_shipment_date)."</td>
												<td align='left'>".$lead_time."</td>";

									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
										<td width="40"><? echo $i; ?></td>
										<td width="125"><? echo $main_booking; ?></td>
										<td width="125"><? echo $sample_booking; ?></td>
                                        <td width="75" style="word-break:break-all"><? echo $all_book_prefix_no; ?></td>
										<td width="100" align="center"><? echo $job_no; ?></td>
                                        <td width="40" onClick="openmypage_image('requires/fabric_receive_status_report2_controller.php?action=show_image&job_no=<?=$job_no; ?>','Image View')"><img  src="../../<?=$imge_arr[$job_no]; ?>" height='25' width='30' /></td>
										<td width="120">
											<p>
												<a href='#report_details' onClick="progress_comment_popup('<? echo $job_no; ?>','<? echo $po_id; ?>','<? echo $template_id; ?>');"><? echo $po_number;  ?></a>
											</p>
										</td>
										<td width="90" align="center"><? echo $order_status[$is_confirmed]; ?></td>
										<td width="80"><p><? echo $buyer_short_name_library[$buyer_name]; ?></p></td>
										<td width="130"><p><? echo $style_ref_no; ?></p></td>
										<td width="100"><p><? echo $file_no; ?></p></td>
										<td width="100"><p><? echo $grouping; ?></p></td>
										<td width="140"><p><? echo $gmts_item; ?></p></td>
										<td width="100" align="right"><? echo number_format($order_qnty_in_pcs,0,'.',''); ?></td>
										<td width="80" align="center"><? echo change_date_format($pub_shipment_date); ?></td>
										<td width="80" align="center"><? echo change_date_format($po_received_date); ?></td>
										<td width="80" align="center"><? echo $po_entry_date; ?></td>
										<td width="100" align="center"><? echo $shipment_status[$shiping_status]; ?></td>
										<td width="80"><p><? echo $display_font_color.$contry_ship_date.$font_end; ?></p></td>
										<td width="100" align="right"><a href="##" onClick="country_order_dtls('<? echo $po_id; ?>','<? echo $start_date; ?>','<? echo $end_date; ?>','<? echo $buyer_name; ?>','<? echo $job_no; ?>','country_order_dtls_popup')"><? echo number_format($country_ship_qty); ?></a></td>
										<td width="100" align="right" title="<? echo $fabric_costing_arr['knit']['grey'][$po_id]."/".$plan_cut_qnty; ?>"><? echo number_format($grey_cons,5,'.',''); ?></td>
										<td width="100" align="right" title="<? echo $fabric_costing_arr['knit']['finish'][$po_id]."/".$plan_cut_qnty; ?>"><? echo number_format($fin_cons,5,'.',''); ?></td>
										<td width="70">
											<?
												 $html.="<td>"; $d=1;
												 foreach($yarn_data_array['count'] as $yarn_count_value)
												 {
													if($d!=1)
													{
														echo "<hr/>";
														$html.="<hr/>";
													}

													echo $yarn_count_value;
													$html.=$yarn_count_value;

												 $d++;
												 }
												 $html.="</td><td>";
											?>
										</td>
										<td width="110">
											<div style="word-wrap:break-word; width:110px">
												<?
													 $d=1;
													 foreach($yarn_data_array['comp'] as $yarn_composition_value)
													 {
														if($d!=1)
														{
															echo "<hr/>";
															$html.="<hr/>";
														}

														echo $yarn_composition_value;
														$html.=$yarn_composition_value;

													 $d++;
													 }

													 $html.="</td><td>";
												?>
											</div>
										</td>
										<td width="80">
											<p>
												<?
													 $d=1;
													 foreach($yarn_data_array['type'] as $yarn_type_value)
													 {
														if($d!=1)
														{
															echo "<hr/>";
															$html.="<hr/>";
														}

														echo $yarn_type_value;
														$html.=$yarn_type_value;

													 	$d++;
													 }

													 $html.="</td><td>";
												?>
											</p>
										</td>
										<td width="100" align="right">
											<?
												echo "<font color='$bgcolor' style='display:none'>".number_format(array_sum($mkt_required_array),2,'.','')."</font>\n";
												$d=1;
												foreach($mkt_required_array as $mkt_required_value)
												{
													if($d!=1)
													{
														echo "<hr/>";
														$html.="<hr/>";
													}

													$yarn_desc_for_popup_req=explode("__",$yarn_desc_array_for_popup[$d]);

													?>
													<a href="##" onClick="openmypage('<? echo $po_id; ?>','yarn_req','<? echo $yarn_desc_for_popup_req[0]; ?>','<? echo $yarn_desc_for_popup_req[1]; ?>','<? echo $yarn_desc_for_popup_req[2]; ?>','<? echo $yarn_desc_for_popup_req[3]; ?>','<? echo $yarn_desc_for_popup_req[4]; ?>','<? echo $yarn_desc_for_popup_req[5]; ?>')"><? echo number_format($mkt_required_value,2,'.','');?></a>
												<?
												$html.=number_format($mkt_required_value,2);
												$d++;
												}

												$html.="</td><td bgcolor='$discrepancy_td_color'>"; $html_short.="<td>"; $html_medium.="<td>";
											?>
										</td>
										<td width="100" align="right">
											<?
												if($z==1)
												{
													$d=1;
													foreach($yarn_allocation_arr as $yarn_allocation_value)
													{
														if($d!=1)
														{
															echo "<hr/>";
															$html.="<hr/>";
														}
														$yarn_desc_for_popup_req=explode("__",$yarn_desc_array_for_popup[$d]);
														?>
														<a href="##" onClick="openmypage('<? echo $po_id; ?>','yarn_allocation_pop','<? echo $yarn_desc_for_popup_req[0]; ?>','<? echo $yarn_desc_for_popup_req[1]; ?>','<? echo $yarn_desc_for_popup_req[2]; ?>','<? echo $yarn_desc_for_popup_req[3]; ?>','<? echo $yarn_desc_for_popup_req[4]; ?>','<? echo $yarn_desc_for_popup_req[5]; ?>',2)"><? echo number_format($yarn_allocation_value,2,'.','');?></a>
													<?
													$html.=number_format($yarn_allocation_value,2);
													$d++;
													}
												}
												$html.="</td><td bgcolor='$discrepancy_td_color'>";
											?>
										</td>
										<?
										if($type==1)
										{?>
											<td width="100" align="right">
												<?
													if($z==1)
													{
														$d=1;
														foreach($yarn_autoallocated_arr as $yarn_autoallocation_value)
														{
															if($d!=1)
															{
																echo "<hr/>";
																$html.="<hr/>";
															}
															$yarn_desc_for_popup_req=explode("__",$yarn_desc_array_for_popup[$d]);
															?>
															<a href="##" onClick="openmypage('<? echo $po_id; ?>','yarn_allocation_pop','<? echo $yarn_desc_for_popup_req[0]; ?>','<? echo $yarn_desc_for_popup_req[1]; ?>','<? echo $yarn_desc_for_popup_req[2]; ?>','<? echo $yarn_desc_for_popup_req[3]; ?>','<? echo $yarn_desc_for_popup_req[4]; ?>','<? echo $yarn_desc_for_popup_req[5]; ?>',1)"><? echo number_format($yarn_autoallocation_value,2,'.','');?></a>
														<?
														$html.=number_format($yarn_autoallocation_value,2);
														$d++;
														}
													}
													$html.="</td><td bgcolor='$discrepancy_td_color'>";
												?>
											</td>
										<?}
										?>
										<td width="100" align="right" >
											<?
												if($z==1)
												{
													$job_yetTo_allocate=$required_qnty-$job_yarnAllocationQty;
													echo number_format($job_yetTo_allocate,2,'.','');
													$tot_yetTo_allocate+=$job_yetTo_allocate;
													$html.=number_format($job_yetTo_allocate,2);
												}
												$html.="</td><td bgcolor='$discrepancy_td_color'>";
											?>
										</td>
										<td width="100" align="right" bgcolor="<? echo $yarn_color_td; ?>">
											<?
												echo "<font color='$bgcolor' style='display:none'>".number_format($yarn_issued,2,'.','')."</font>\n";
												$d=1;
												foreach($yarn_desc_array as $yarn_desc)
												{
													if($d!=1)
													{
														echo "<hr/>";
														$html.="<hr/>";
													}

													$yarn_iss_qnty=$yarn_iss_qnty_array[$yarn_desc];
													$yarn_desc_for_popup=explode("__",$yarn_desc_array_for_popup[$d]);

													?>
													<a href="##" onClick="openmypage('<? echo $po_id; ?>','yarn_issue','<? echo $yarn_desc_for_popup[0]; ?>','<? echo $yarn_desc_for_popup[1]; ?>','<? echo $yarn_desc_for_popup[2]; ?>','<? echo $yarn_desc_for_popup[3]; ?>','<? echo $yarn_desc_for_popup[4]; ?>','<? echo $yarn_desc_for_popup[5]; ?>')"><? echo number_format($yarn_iss_qnty,2,'.','');?></a>
													<?
													$html.=number_format($yarn_iss_qnty,2);
													$d++;
												}

												if($d!=1)
												{
													echo "<hr/>";
													$html.="<hr/>";
												}

												$yarn_desc=join(",",$yarn_desc_array);

												$iss_qnty_not_req=$yarn_iss_qnty_array['not_req'];

												$html.=number_format($iss_qnty_not_req,2);
												$html_short.=number_format($iss_qnty_not_req+$yarn_issued,2);
												$html_medium.=number_format($yarn_issued,2);
												?>
												<a href="##" onClick="openmypage('<? echo $po_id; ?>','yarn_issue_not','<? echo $yarn_desc; ?>','','','','','')"><? echo number_format($iss_qnty_not_req,2);?></a>
										</td>
										<? $html.="</td><td>"; $html_short.="</td>"; $html_medium.="</td>"; ?>
										<td width="100" align="right">
											 <a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $po_id; ?>','yarn_trans','')"><? echo number_format($net_trans_yarn,2,'.','');  ?></a>
											<?
												$html.=number_format($net_trans_yarn,2);
												$tot_net_trans_yarn_qnty+=$net_trans_yarn;
											?>
										</td>
										<? $html.="</td><td>"; $html_medium.="</td><td>"; ?>
										<td width="100" align="right" title="(Grey Req-(Yarn Issue+Net Transfer))">
											<?
												echo number_format($balance,2,'.','');
												$html.=number_format($balance,2);
												$html_medium.=number_format($balance,2);
											?>
										</td>
										<? $html.="</td><td>"; ?>
										<td width="100" align="right" bgcolor="<? echo $bgcolor_grey_td; ?>"> <? echo number_format($required_qnty,2,'.',''); $html.=number_format($required_qnty,2); ?></td>
										<? $html.="</td><td>"; ?>
										<td width="100" align="right" bgcolor="<? echo $gray_prod_color_td; ?>"><a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $po_id; ?>','grey_receive','')"><? echo number_format($grey_recv_qnty,2,'.',''); $html.=number_format($grey_recv_qnty,2);?></a></td>
										<? $html.="</td><td>"; ?>
										<td width="100" align="right">
											<?
												echo number_format($grey_prod_balance,2,'.','');
												$html.=number_format($grey_prod_balance,2);
											?>
										</td>
										<? $html.="</td><td>"; ?>
										<td width="100" align="right"><a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $po_id; ?>','grey_purchase','')"><? echo number_format($grey_del_store,2,'.',''); $html.=number_format($grey_del_store,2);?></a></td>
										<? $html.="</td><td>"; ?>
										<td width="100">&nbsp; <? $html.="&nbsp;"; ?></td>
										<? $html.="</td><td>"; ?>
										<td width="100" align="right"><a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $po_id.'_'.'9'; ?>','grey_purchase','')"><? echo number_format($grey_production_qnty,2,'.',''); $html.=number_format($grey_production_qnty,2); ?></a></td>
										<? $html.="</td><td>"; ?>
										<td width="100" align="right"><a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $po_id.'_'.'9'; ?>','grey_purchase','')"><? echo number_format($grey_purchase_qnty,2,'.',''); $html.=number_format($grey_purchase_qnty,2); ?></a></td>
										<? $html.="</td><td>"; ?>
										<td width="100" align="right"></td>
										<? $html.="</td><td>"; ?>
										<td width="100" align="right">
											<a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $po_id; ?>','knit_trans','')"><? echo number_format($net_trans_knit,2,'.','');  ?></a><? $tot_net_trans_knit_qnty+=$net_trans_knit; $html.=number_format($net_trans_knit,2); ?>
										</td>
										<? $html.="</td><td>"; ?>
										<td width="100" align="right">
											<? $grey_available=0; $grey_available=$grey_production_qnty+$grey_purchase_qnty+$net_trans_knit;
											echo number_format($grey_available,2,'.',''); $html.=number_format($grey_available,2); ?>
										</td>
										<? $html.="</td><td>"; ?>
										<td width="100" align="right"><? echo number_format($grey_balance,2,'.',''); $html.=number_format($grey_balance,2); ?></td>
										<? $html.="</td><td>"; ?>
										<td width="100" align="right"><a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $po_id; ?>','grey_issue','')"><?
										$grey_fabric_issue=$grey_fabric_issue;
										 echo number_format($grey_fabric_issue,2,'.',''); $html.=number_format($grey_fabric_issue,2); ?></a>
										</td>
										<? $html.="</td><td>"; ?>
                                        <td width="100" align="right"><? echo number_format($grey_in_hand,2,'.',''); $html.=number_format($grey_in_hand,2); ?></td>
										<? $html.="</td><td>"; ?>
										<td width="100" align="right"><a href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $po_id; ?>','receive_by_batch','')"><? echo number_format($receive_by_batch_qnt,2,'.',''); ?></a><? $html.=number_format($receive_by_batch_qnt,2); ?>
										</td>
										<? $html.="</td><td>"; ?>
										<td width="100">&nbsp;<? $html.="&nbsp;"; ?></td>
										<? $html.="</td><td>"; ?>
										<td width="100">&nbsp;<? $html.="&nbsp;"; ?></td>
										<? $html.="</td><td>"; ?>
										<td width="100" align="right"><a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $po_id; ?>','batch_qnty','')"><? /*echo number_format($batch_color_qnty,2,'.','');*/ $html.="&nbsp;"; ?></a></td>
										<? $html.="</td><td>"; ?>
										<td width="100" bgcolor="<? echo $dye_prod_color_td; ?>">&nbsp;</td>
										<td width="100">&nbsp;</td>
										<td width="100">&nbsp;</td>
										<td width="100">&nbsp;</td>
										<td width="100">&nbsp;</td>
										<td width="100">&nbsp;</td>
										<td width="100">&nbsp;</td>
										<td width="100">&nbsp;</td>
										<td width="100">&nbsp;</td>
										<td width="100">&nbsp;</td>
										<td width="100">&nbsp;</td>
										<td width="100">&nbsp;</td>
										<td width="100">&nbsp;</td>
										<td width="100">&nbsp;</td>
										<td width="100">&nbsp;</td>
										<td width="100">&nbsp;</td>
										<td width="100">&nbsp;</td>
										<td width="100">&nbsp;</td>
										<td>
											<p>
												<? $fabric_desc=explode(",",$fabric_desc_details[$job_no]); echo join(",<br>",array_unique($fabric_desc)); ?>
											</p>
										</td>
									</tr>
									<?
										$tot_batch_qnty_excel+=$batch_qnty;
										$html.="</td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td>".join(",<br>",array_unique($fabric_desc))."</td>
										</tr>
										";

										$html_short.="</td><td bgcolor='$bgcolor_grey_td'>".number_format($required_qnty,2)."</td>
										<td>".number_format($grey_recv_qnty,2)."</td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										</tr>
										";

										$html_medium.="</td><td bgcolor='$bgcolor_grey_td'>".number_format($required_qnty,2)."</td>
										<td>".number_format($grey_recv_qnty,2)."</td>
										<td>".number_format($grey_prod_balance,2)."</td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										</tr>
										";
									$k++;
								}
								$i++;
							}
						}// end main query
					}
				}
				else if($type==2)
				{
					//echo $type;die;
					if ($chk_no_boking == 1) // check no booking
					{
						foreach($job_data_arr as $job_no=>$other_data)
						{
							$ex_data_job=explode('##',$other_data);
							$company_id=''; $buyer_name='';  $job_no_prefix_num=''; $style_ref_no=''; $gmts_item_id=''; $order_uom=''; $ratio=''; $poId_id=''; $grouping=''; $file_no=''; $po_number=''; $po_qnty=''; $pub_shipment_date=''; $shiping_status=''; $insert_date=''; $po_received_date=''; $plan_cut=''; $is_confirmed='';// $po_id
							$company_id=$ex_data_job[0];
							$buyer_name=$ex_data_job[1];
							$job_no_prefix_num=$ex_data_job[2];
							$style_ref_no=$ex_data_job[3];
							$gmts_item_id=$ex_data_job[4];
							$order_uom=$ex_data_job[5];
							$ratio=$ex_data_job[6];

							//$job_all_data=explode(',',$job_allData_arr[$job_no]);
							$job_all_data=array();
							$job_all_data[]=$job_allData_arr[$job_no];
							//echo $job_all_data;
							$grouping_all=''; $file_no_all=''; $po_number_all=''; $pub_shipment_date_all=''; $insert_date_all=''; $po_received_date_all=''; $po_id_all='';
							$bk=0;
							foreach ( $job_all_data as $poall_data )
							{
								//$bk++;
								//echo $bk;
								$po_data_arr=array_filter(explode('___',$poall_data));
								foreach($po_data_arr as $data_po)
								{
									$ex_data=explode('**',$data_po);

									if($grouping_all=="") $grouping_all=$ex_data[0]; else $grouping_all.=','.$ex_data[0];
									if($file_no_all=="") $file_no_all=$ex_data[1]; else $file_no_all.=','.$ex_data[1];
									if($po_number_all=="") $po_number_all=$ex_data[2]; else $po_number_all.=','.$ex_data[2];
									$po_qnty+=$ex_data[3];
									if($pub_shipment_date_all=="") $pub_shipment_date_all=$ex_data[4]; else $pub_shipment_date_all.=','.$ex_data[4];
									//$shiping_status=$ex_data[5];
									if($insert_date_all=="") $insert_date_all=$ex_data[6]; else $insert_date_all.=','.$ex_data[6];
									if($po_received_date_all=="") $po_received_date_all=$ex_data[7]; else $po_received_date_all.=','.$ex_data[7];
									$plan_cut+=$ex_data[8];
									//$is_confirmed=$ex_data[9];
									if($po_id_all=="") $po_id_all=$ex_data[10]; else $po_id_all.=','.$ex_data[10];
								}
							}
							//echo $po_number_all;
							$poId_id=implode(',',array_filter(array_unique(explode(',',$po_id_all))));
							$grouping=implode(',',array_filter(array_unique(explode(',',$grouping_all))));
							$file_no=implode(',',array_filter(array_unique(explode(',',$file_no_all))));
							$po_number=implode(', ',array_filter(array_unique(explode(',',$po_number_all))));
							$po_qnty=$po_qnty;
							$pub_shipment_date=implode(',',array_filter(array_unique(explode(',',$pub_shipment_date_all))));
							//$shiping_status='';
							$insert_date=implode(',',array_filter(array_unique(explode(',',$insert_date_all))));
							$po_received_date=implode(',',array_filter(array_unique(explode(',',$po_received_date_all))));
							$plan_cut=$plan_cut;
							//$is_confirmed='';
							//echo $po_number;



							$no_book_check="";
							$check_job_po_id = explode(",", $poId_id);
							foreach ($check_job_po_id as $po_id) {
								$no_book_check .= implode(",", array_filter(explode(",", substr($dataArrayWo[$po_id], 0, -1)))) . ",";
								//$nobooking_check = array_filter(explode(",", substr($dataArrayWo[$po_id], 0, -1)));
							}

							$no_book_check_arr = array_filter(explode(",", substr($no_book_check, 0, -1)));
							//print_r($no_book_check_arr);
							//if (count($no_book_check_arr) < 1) echo count($no_book_check_arr).'==<br>';
							//echo "13=".count($no_book_check_arr);
							if (count($no_book_check_arr) < 1)
							{
								$order_qnty_in_pcs=$po_qnty*$ratio;
								$plan_cut_qnty=$plan_cut*$ratio;
								$order_qty_array[$buyer_name]+=$order_qnty_in_pcs;

								$gmts_item='';
								$gmts_item_id=explode(",",$gmts_item_id);
								foreach($gmts_item_id as $item_id)
								{
									if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
								}

								$dzn_qnty=0; $balance=0; $job_mkt_required=0; $yarn_issued=0;
								if($costing_per_id_library[$job_no]==1) $dzn_qnty=12;
								else if($costing_per_id_library[$job_no]==3) $dzn_qnty=12*2;
								else if($costing_per_id_library[$job_no]==4) $dzn_qnty=12*3;
								else if($costing_per_id_library[$job_no]==5) $dzn_qnty=12*4;
								else $dzn_qnty=1;

								$dzn_qnty=$dzn_qnty*$ratio;

								$yarn_data_array=array(); $mkt_required_array=array(); $yarn_allocation_arr=array(); $yetTo_allocate_arr=array(); $req_for_allocate_arr=array();  $yarn_desc_array_for_popup=array(); $yarn_desc_array=array(); $yarn_iss_qnty_array=array(); $s=1;

								$yarn_descrip_data=$yarn_des_data_job[$job_no];
								$qnty=0;
								foreach($yarn_descrip_data as $count=>$count_value)
								{
									foreach($count_value as $Composition=>$composition_value)
									{
										foreach($composition_value as $percent=>$percent_value)
										{
											foreach($percent_value as $type_ref=>$type_value)
											{
												$count_id=$count;
												$copm_one_id=$Composition;
												$percent_one=$percent;
												$type_id=$type_ref;
												$qnty=$type_value;

												$mkt_required=$qnty;
												$mkt_required_array[$s]=$mkt_required;
												$job_mkt_required+=$mkt_required;

												$yarn_data_array['count'][$s]=$yarn_count_details[$count_id];
												$yarn_data_array['type'][$s]=$yarn_type[$type_id];

												$compos=$composition[$copm_one_id]." ".$percent_one." %"." ".$composition[$copm_two_id];
												$yarn_data_array['comp'][]=$compos;

												$yarn_desc_array[$s]=$yarn_count_details[$count_id]." ".$compos." ".$yarn_type[$type_id];
												$des_for_allocation=$yarn_count_details[$count_id]." ".$compos." ".$yarn_type[$type_id];

												$req_for_allocate_arr[$des_for_allocation]=$mkt_required;

												$yarn_desc_for_popup=$count_id."__".$copm_one_id."__".$percent_one."__".$copm_two_id."__".$percent_two."__".$type_id;
												$yarn_desc_array_for_popup[$s]=$yarn_desc_for_popup;
												$s++;
											}
										}
									}
								}

								$grey_production_qnty=0; $grey_purchase_qnty=0; $grey_net_return=0; $grey_recv_qnty=0; $grey_fabric_issue=0; $booking_data=''; $job_yarnAllocationQty=0; $grey_del_store=0; $n=1; $grey_in_hand=0;

								$job_po_id=explode(",",$poId_id); //$job_yetTo_allocate=0;
								foreach($job_po_id as $po_id)
								{
									$dataYarnIssue=explode(",",substr($dataArrayYarnIssue[$po_id],0,-1));
									foreach($dataYarnIssue as $yarnIssueRow)
									{
										$yarnIssueRow=explode("**",$yarnIssueRow);
										$yarn_count_id=$yarnIssueRow[0];
										$yarn_comp_type1st=$yarnIssueRow[1];
										$yarn_comp_percent1st=$yarnIssueRow[2];
										$yarn_comp_type2nd=$yarnIssueRow[3];
										$yarn_comp_percent2nd=$yarnIssueRow[4];
										$yarn_type_id=$yarnIssueRow[5];
										$issue_qnty=$yarnIssueRow[6];
										$return_qnty=$yarnIssueRow[7];

										if($yarn_comp_percent2nd!=0)
										{
											$compostion_not_req=$composition[$yarn_comp_type1st]." ".$yarn_comp_percent1st." %"." ".$composition[$yarn_comp_type2nd]." ".$yarn_comp_percent2nd." %";
										}
										else
										{
											$compostion_not_req=$composition[$yarn_comp_type1st]." ".$yarn_comp_percent1st." %"." ".$composition[$yarn_comp_type2nd];
										}

										$desc=$yarn_count_details[$yarn_count_id]." ".$compostion_not_req." ".$yarn_type[$yarn_type_id];

										$net_issue_qnty=$issue_qnty-$return_qnty;
										$yarn_issued+=$net_issue_qnty;
										if(!in_array($desc,$yarn_desc_array))
										{
											$yarn_iss_qnty_array['not_req']+=$net_issue_qnty;
										}
										else
										{
											$yarn_iss_qnty_array[$desc]+=$net_issue_qnty;
										}
									}

									$dataYarnAllocation=explode(",",substr($yarnAllocationArr[$po_id],0,-1));
									foreach($dataYarnAllocation as $yarnAllRow)
									{
										$yarnAlloRow=explode("**",$yarnAllRow);
										$yarn_count_id=$yarnAlloRow[0];
										$yarn_comp_type1st=$yarnAlloRow[1];
										$yarn_comp_percent1st=$yarnAlloRow[2];
										$yarn_comp_type2nd=$yarnAlloRow[3];
										$yarn_comp_percent2nd=$yarnAlloRow[4];
										$yarn_type_id=$yarnAlloRow[5];
										$yarnAllocationQty=$yarnAlloRow[6];

										if($yarn_comp_percent2nd!=0)
										{
											$compostion_not_req=$composition[$yarn_comp_type1st]." ".$yarn_comp_percent1st." % ".$composition[$yarn_comp_type2nd]." ".$yarn_comp_percent2nd." %";
										}
										else
										{
											$compostion_not_req=$composition[$yarn_comp_type1st]." ".$yarn_comp_percent1st." % ".$composition[$yarn_comp_type2nd];
										}

										$desc=$yarn_count_details[$yarn_count_id]." ".$compostion_not_req." ".$yarn_type[$yarn_type_id];
										$req_allocation=$req_for_allocate_arr[$desc];
										//$yetTo_allocate=$req_for_allocate_arr[$desc]-$yarnAllocationQty;
										//$job_yetTo_allocate+=$yetTo_allocate;
										$job_yarnAllocationQty+=$yarnAllocationQty;
										if(!in_array($desc,$yarn_desc_array))
										{
											$yarn_allocation_arr['not_req']+=$yarnAllocationQty;
											//$yetTo_allocate_arr['not_req']+=$req_for_allocate_arr[$desc]-$yarnAllocationQty;
										}
										else
										{
											$yarn_allocation_arr[$desc]+=$yarnAllocationQty;
											//$yetTo_allocate_arr[$desc]+=$req_for_allocate_arr[$desc]-$yarnAllocationQty;
										}
									}
									$grey_rec_rtn_purchase=$grey_rec_rtn_production=0;
									$grey_rec_rtn_purchase=$grey_receive_return_qnty_arr[$po_id]['purchase'];
									$grey_rec_rtn_production=$grey_receive_return_qnty_arr[$po_id]['production'];

									$grey_production_qnty+=$greyPurchaseQntyArray[$po_id]['production']-$grey_rec_rtn_production;
									$grey_purchase_qnty+=$greyPurchaseQntyArray[$po_id]['purchase']-$grey_rec_rtn_purchase;

									$grey_issue_rtn+=$grey_issue_return_qnty_arr[$po_id];
									$grey_rec_rtn+=$grey_rec_rtn_purchase+$grey_rec_rtn_production;
									$grey_net_return+=$grey_issue_rtn-$grey_rec_rtn;

									$grey_recv_qnty+=$grey_receive_qnty_arr[$po_id];

									$grey_fabric_issue+=$grey_issue_qnty_arr[$po_id]-$grey_issue_rtn;
									$receive_by_batch_qnt=$receive_by_batch_data[$po_id];

									$grey_del_store+=$greyDeliveryArray[$po_id];

									$booking_data.=implode("__",array_filter(explode("__",$dataArrayWo[$po_id]))).",";
									$n++;
								}
								$grey_in_hand=($grey_production_qnty+$grey_purchase_qnty)-$grey_fabric_issue;

								$total_grey_del_store+=$grey_del_store;

								if(($cbo_discrepancy==1 && $grey_recv_qnty>$yarn_issued) || ($cbo_discrepancy==0))
								{
									if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									$buyer_name_array[$buyer_name]=$buyer_short_name_library[$buyer_name];

									$booking_array=array(); $color_data_array=array(); $grey_req_color_arr=array();
									$required_qnty=0; $main_booking=''; $sample_booking=''; $main_booking_excel=''; $sample_booking_excel='';  $all_book_prefix_no = '';
									$dataArray=explode("__",substr($booking_data,0,-1));
									if(count($dataArray)>0)
									{
										foreach($dataArray as $woRow)
										{
											$woRow=explode("**",$woRow);
											$id=$woRow[0];
											$booking_no=$woRow[1];
											$insert_date=$woRow[2];
											$item_category=$woRow[3];
											$fabric_source=$woRow[4];
											$company_id=$woRow[5];
											$booking_type=$woRow[6];
											$booking_no_prefix_num=$woRow[7];
											$job_no_book=$woRow[8];
											$is_short=$woRow[9];
											$is_approved=$woRow[10];
											$fabric_color_id=$woRow[11];
											$req_qnty=$woRow[12];
											$grey_req_qnty=$woRow[13];
											$wo_po_id=$woRow[14];
											$book_prefix_no = $woRow[7];

											$required_qnty+=$grey_req_qnty;

											if(!in_array($id,$booking_array))
											{
												$system_date=date('d-M-Y', strtotime($insert_date));
												if ($fabric_source == 2) $wo_color = "color='color:#000'"; else $wo_color = "";

												if($booking_type==4)
												{
													//$action_name='show_fabric_booking_report';
													$action_name=$report_format_sample_arr[$reportFormatSample];
													$sample_booking.="<a href='##' style='color:#000' onclick=\"generate_worder_report('3','".$booking_no."','".$company_id."','".$wo_po_id."','".$item_category."','".$fabric_source."','".$job_no_book."','".$is_approved."','".$action_name."','".$print_report_format."')\"><font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")"."</font></a><br>";
													$sample_booking_excel.="<font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")"."</font><br>";
												}
												else
												{
													$all_book_prefix_no .= $book_prefix_no . ",";
													if($is_short==1)
													{
														$pre="S";
														$action_name=$report_format_arr[$booking_print_arr[2]];
													}
													else
													{
														$pre="M";
														$action_name=$report_format_arr[$booking_print_arr[1]];
													}
													if($action_name=='') $action_name='show_fabric_booking_report';
													//if($is_short==1) $pre="S"; else $pre="M";
													//if($is_short==1) $pre="S"; else $pre="M";
													if($booking_no!="")
													{
														$main_booking.="<a href='##' style='color:#000' onclick=\"generate_worder_report('".$is_short."','".$booking_no."','".$company_id."','".$wo_po_id."','".$item_category."','".$fabric_source."','".$job_no_book."','".$is_approved."','".$action_name."','".$print_report_format."')\"><font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")".$pre."</font></a><br>";
													}
													else $main_booking.="No Booking";
													$main_booking_excel.="<font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")".$pre."</font><br>";
												}

												$booking_array[]=$id;
											}
											$color_data_array[$fabric_color_id]+=$req_qnty;
											$grey_req_color_arr[$fabric_color_id]+=$grey_req_qnty;
										}
									}
									else
									{
										$main_booking.="No Booking";
										$main_booking_excel.="No Booking";
										$sample_booking.="No Booking";
										$sample_booking_excel.="No Booking";
										$all_book_prefix_no = "&nbsp;";
									}

									if($main_booking=="")
									{
										$main_booking.="No Booking";
										$main_booking_excel.="No Booking";
									}

									if($sample_booking=="")
									{
										$sample_booking.="No Booking";
										$sample_booking_excel.="No Booking";
									}
									$all_book_prefix_no = chop($all_book_prefix_no, ",");

									$yarn_issue_array[$buyer_name]+=$yarn_issued;
									$grey_required_array[$buyer_name]+=$required_qnty;

									$net_trans_yarn=0; $net_trans_knit=0; $batch_qnty=0;
									foreach($job_po_id as $val)
									{
										$finish_color=array_unique(explode(",",$po_color_arr[$val]));
										foreach($finish_color as $color_id)
										{
											if($color_id>0)
											{
												$color_data_array[$color_id]+=0;
											}
										}

										$net_trans_yarn+=$trans_qnty_arr[$val]['yarn_trans'];
										$net_trans_knit+=$trans_qnty_arr[$val]['knit_trans'];
									}

									$yarn_issue_array[$buyer_name]+=$net_trans_yarn;
									$balance=$required_qnty-($yarn_issued+$net_trans_yarn);
									//$yetTo_allocate=$balance-$job_yarnAllocationQty;
									//$job_yetTo_allocate+=$yetTo_allocate;
									$yarn_balance_array[$buyer_name]+=$balance;
									$grey_available=$grey_production_qnty+$grey_purchase_qnty+$net_trans_knit;
									$knitted_array[$buyer_name]+=$grey_available;

									//$knitted_array[$buyer_name]+=$net_trans_knit;
									$grey_prod_balance=$required_qnty-$grey_recv_qnty;
									$grey_balance=$required_qnty-$grey_available;
									$tot_grey_prod_balance+=$grey_prod_balance;

									$grey_balance_array[$buyer_name]+=$grey_balance;

									$grey_issue_array[$buyer_name]+=$grey_fabric_issue;
									$receive_by_batch_array[$buyer_name]+=$receive_by_batch_qnt;

									//$batch_qnty_array[$buyer_name]+=$batch_qnty;

									$tot_order_qnty+=$order_qnty_in_pcs;
									$tot_mkt_required+=$job_mkt_required;
									$tot_yarnAllocationQty+=$job_yarnAllocationQty;

									$tot_yarn_issue_qnty+=$yarn_issued;
									$tot_fabric_req+=$required_qnty;
									$tot_balance+=$balance;
									$tot_grey_recv_qnty+=$grey_recv_qnty;
									$tot_grey_production_qnty+=$grey_production_qnty;
									$tot_grey_purchase_qnty+=$grey_purchase_qnty;
									$tot_grey_balance+=$grey_balance;
									$tot_grey_issue+=$grey_fabric_issue;
									$tot_receive_by_batch+=$receive_by_batch_qnt;
									//$tot_batch_qnty+=$batch_qnty;


									$tot_grey_available+=$grey_available;

									if($required_qnty>$job_mkt_required) $bgcolor_grey_td='#FF0000'; $bgcolor_grey_td='';

									$po_entry_date=date('d-m-Y', strtotime($insert_date));
									$costing_date=$costing_date_library[$job_no];

									$contry_ship_date=""; $country_ship_qty=0; $grey_cons=0; $fin_cons=0;
									$job_po_id=explode(",",$poId_id); //$job_yetTo_allocate=0;
									foreach($job_po_id as $po_id)
									{
										$country_date_all=array_filter(explode(",",$contry_ship_qty_arr[$po_id]['ship_date']));
										foreach($country_date_all as $date_all)
										{
											if($contry_ship_date=="") $contry_ship_date=change_date_format($date_all); else $contry_ship_date.=','.change_date_format($date_all);
										}
										$country_ship_qty+=$contry_ship_qty_arr[$po_id]['ship_qty'];

										$grey_cons+=$fabric_costing_arr['knit']['grey'][$po_id]/$plan_cut_qnty;//$reqArr[$job_no]['grey']/$dzn_qnty;
										$fin_cons+=$fabric_costing_arr['knit']['finish'][$po_id]/$plan_cut_qnty;//$reqArr[$job_no]['finish']/$dzn_qnty;
									}
									$tot_country_ship_qty+=$country_ship_qty;
									$country_order_qty_array[$buyer_name]+=$country_ship_qty;
									$contry_ship_date=implode(',<br>',array_unique(explode(',',$contry_ship_date)));
									//echo $country_ship_qty.'=='.$poId_id;
									//$grey_cons=$reqArr[$job_no]['grey']/$dzn_qnty;
									//$fin_cons=$reqArr[$job_no]['finish']/$dzn_qnty;

									$tot_color=count($color_data_array);
									//echo $tot_color.'===';
									if($tot_color>0)
									{
										$z=1;
										foreach($color_data_array as $key=>$value)
										{
											if($z==1)
											{
												$display_font_color="";
												$font_end="";
											}
											else
											{
												$display_font_color="<font style='display:none' color='$bgcolor'>";
												$font_end="</font>";
											}


											if($z==1)
											{
												$html.="<tr bgcolor='".$bgcolor."'>
														<td align='left'>".$i."</td>
														<td align='left'>".$main_booking_excel."</td>
														<td align='left'>".$sample_booking_excel."</td>
														<td align='left'>" . $all_book_prefix_no . "</td>
														<td align='center'>".$job_no."</td>
														<td align='center'><img  src='../../".$imge_arr[$job_no]."' height='25' width='30' /></td>
														<td align='left'>".implode(",",array_unique(explode(",",$po_number)))."</td>
														<td align='left'></td>
														<td>".$buyer_short_name_library[$buyer_name]."</td>
														<td align='left'>".$style_ref_no."</td>
														<td align='left'>".implode(",",array_unique(explode(",",$file_no)))."</td>
														<td align='left'>".implode(",",array_unique(explode(",",$grouping)))."</td>
														<td align='left'>".$gmts_item."</td>
														<td align='right'>".$order_qnty_in_pcs."</td>
														<td align='left'>View</td>
														<td align='center'>".$contry_ship_date."</td>
														<td align='right'>".$country_ship_qty."</td>
														<td align='right'>".$grey_cons."</td>
														<td align='right'>".$fin_cons."</td>";


												$lead_time=0;
												$lead_time=datediff('d',$po_received_date,$pub_shipment_date);
												if($lead_time>0) $lead_time=$lead_time-1;

												$html_short.="<tr bgcolor='".$bgcolor."'>
															<td align='left'>".$i."</td>
															<td align='left'>".$main_booking_excel."</td>
															<td align='left'>".$sample_booking_excel."</td>
															<td align='left'>" . $all_book_prefix_no . "</td>
															<td align='left'>".$po_number."</td>
															<td>".$buyer_short_name_library[$buyer_name]."</td>
															<td>".$file_no."</td>
															<td>".$grouping."</td>
															<td align='right'>".$order_qnty_in_pcs."</td>
															<td align='left'>View</td>";
												$html_medium.="<tr bgcolor='".$bgcolor."'>
															<td align='left'>".$i."</td>
															<td>".$buyer_short_name_library[$buyer_name]."</td>
															<td align='left'>".$main_booking_excel."</td>
															<td align='left'>".$sample_booking_excel."</td>
															<td align='left'>" . $all_book_prefix_no . "</td>
															<td align='left'>".$po_number."</td>
															<td align='left'>".$grouping."</td>
															<td align='left'>".$file_no."</td>
															<td align='left'>".$style_ref_no."</td>

															<td align='right'>".$order_qnty_in_pcs."</td>
															<td align='left'>View</td>
															<td align='left'>View</td>";

											}
											else
											{
												$html.="<tr bgcolor='".$bgcolor."'>
														<td></td>
														<td></td>
														<td></td>
														<td></td>
														<td></td>
														<td></td>
														<td></td>
														<td></td>
														<td></td>
														<td></td>
														<td></td>
														<td></td>
														<td></td>
														<td></td>
														<td></td>
														<td></td>
														<td></td>
														<td></td>
														<td></td>";

												$html_short.="<tr bgcolor='".$bgcolor."'>
																<td></td>
																<td></td>
																<td></td>
																<td></td>
																<td></td>

																<td></td>
																<td></td>
																<td></td>
																<td></td>
																<td></td>";
												$html_medium.="<tr bgcolor='".$bgcolor."'>
																<td></td>
																<td></td>
																<td></td>
																<td></td>
																<td></td>
																<td></td>
																<td></td>
																<td></td>
																<td></td>
																<td></td>
																<td></td>
																<td></td>";
											}
										?>
											<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
												<td width="40"><? echo $display_font_color.$i.$font_end; ?></td>
												<td width="125"><? echo $display_font_color.$main_booking.$font_end; ?></td>
												<td width="125"><? echo $display_font_color.$sample_booking.$font_end; ?></td>
												<td width="75" style="word-break:break-all"><? echo $display_font_color.$all_book_prefix_no.$font_end; ?></td>
												<td width="100" align="center"><? echo $display_font_color.$job_no.$font_end; ?></td>
                                                <td width="40" onClick="openmypage_image('requires/fabric_receive_status_report2_controller.php?action=show_image&job_no=<?=$job_no; ?>','Image View')"><?=$display_font_color; ?><img  src="../../<?=$imge_arr[$job_no]; ?>" height='25' width='30' /><?=$font_end; ?></td>
												<td width="120"><p><? echo $display_font_color.implode(",",array_unique(explode(",",$po_number))). $font_end; ?></p></td>
												<td width="90" align="center"><? //echo $order_status[$row[csf('is_confirmed')]]; ?></td>
												<td width="80"><p><? echo $display_font_color.$buyer_short_name_library[$buyer_name].$font_end; ?></p></td>
												<td width="130"><p><? echo $display_font_color.$style_ref_no.$font_end; ?></p></td>
												<td width="100"><p><? echo $display_font_color.implode(",",array_unique(explode(",",$file_no))).$font_end; ?></p></td>
												<td width="100"><p><? echo $display_font_color.implode(",",array_unique(explode(",",$grouping))).$font_end; ?></p></td>
												<td width="140"><p><? echo $display_font_color.$gmts_item; ?></p></td>
												<td width="100" align="right"><? if($z==1) echo number_format($order_qnty_in_pcs,0,'.',''); ?></td>
												<td width="80" align="center"><? echo $display_font_color; ?><a href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $poId_id; ?>','Shipment_date','')"><? echo "View"; ?></a><? echo $font_end; ?></td>
												<td width="80"><p><? echo $display_font_color.$contry_ship_date.$font_end; ?></p></td>
												<td width="100" align="right"><a href="##" onClick="country_order_dtls('<? echo $poId_id; ?>','<? echo $start_date; ?>','<? echo $end_date; ?>','<? echo $buyer_name; ?>','<? echo $job_no; ?>','country_order_dtls_popup')"><? if($z==1) echo number_format($country_ship_qty,0,'.',''); ?></a></td>
												<td width="100" align="right"><? if($z==1) echo number_format($grey_cons,5,'.',''); ?></td>
												<td width="100" align="right"><? if($z==1) echo number_format($fin_cons,5,'.',''); ?></td>
												<td width="70">
													<?
														 $html.="<td>"; $d=1;
														 foreach($yarn_data_array['count'] as $yarn_count_value)
														 {
															if($d!=1)
															{
																echo $display_font_color."<hr/>".$font_end;
																if($z==1) $html.="<hr/>";
															}

															echo $display_font_color.$yarn_count_value.$font_end;
															if($z==1) $html.=$yarn_count_value;
														 $d++;
														 }

														 $html.="</td><td>";
													?>
												</td>
												<td width="110">
													<div style="word-wrap:break-word; width:110px">
														<?
															 $d=1;
															 foreach($yarn_data_array['comp'] as $yarn_composition_value)
															 {
																if($d!=1)
																{
																	echo $display_font_color."<hr/>".$font_end;
																	if($z==1) $html.="<hr/>";
																}
																echo $display_font_color.$yarn_composition_value.$font_end;
																if($z==1) $html.=$yarn_composition_value;
															 $d++;
															 }

															 $html.="</td><td>";
														?>
													</div>
												</td>
												<td width="80">
													<p>
														<?
															 $d=1;
															 foreach($yarn_data_array['type'] as $yarn_type_value)
															 {
																if($d!=1)
																{
																	echo $display_font_color."<hr/>".$font_end;
																	if($z==1) $html.="<hr/>";
																}

																echo $display_font_color.$yarn_type_value.$font_end;
																if($z==1) $html.=$yarn_type_value;
															 $d++;
															 }

															 $html.="</td><td>";
														?>
													</p>
												</td>
												<td width="100" align="right">
													<?
														if($z==1)
														{
															echo "<font color='$bgcolor' style='display:none'>".number_format(array_sum($mkt_required_array),2,'.','')."</font>\n";
															$d=1;
															foreach($mkt_required_array as $mkt_required_value)
															{
																if($d!=1)
																{
																	echo "<hr/>";
																	$html.="<hr/>";
																}

																$yarn_desc_for_popup_req=explode("__",$yarn_desc_array_for_popup[$d]);

																?>
																<a href="##" onClick="openmypage('<? echo $poId_id; ?>','yarn_req','<? echo $yarn_desc_for_popup_req[0]; ?>','<? echo $yarn_desc_for_popup_req[1]; ?>','<? echo $yarn_desc_for_popup_req[2]; ?>','<? echo $yarn_desc_for_popup_req[3]; ?>','<? echo $yarn_desc_for_popup_req[4]; ?>','<? echo $yarn_desc_for_popup_req[5]; ?>')"><? echo number_format($mkt_required_value,2,'.','');?></a>
															<?
															$html.=number_format($mkt_required_value,2);
															$d++;
															}
														}
														$html.="</td><td bgcolor='$discrepancy_td_color'>"; $html_short.="<td>"; $html_medium.="<td>";
													?>
												</td>
												<td width="100" align="right">
													<?
														if($z==1)
														{
															echo "<font color='$bgcolor' style='display:none'>".number_format($job_yarnAllocationQty,2,'.','')."</font>\n";
															$d=1;
															foreach($yarn_desc_array as $yarn_desc)
															{
																if($d!=1)
																{
																	echo "<hr/>";
																	$html.="<hr/>";
																}

																$yarn_allo_qnty=$yarn_allocation_arr[$yarn_desc];
																$yarn_desc_for_popup=explode("__",$yarn_desc_array_for_popup[$d]);

																?>
																<a href="##" onClick="openmypage('<? echo $poId_id; ?>','yarn_allocation_pop','<? echo $yarn_desc_for_popup[0]; ?>','<? echo $yarn_desc_for_popup[1]; ?>','<? echo $yarn_desc_for_popup[2]; ?>','<? echo $yarn_desc_for_popup[3]; ?>','<? echo $yarn_desc_for_popup[4]; ?>','<? echo $yarn_desc_for_popup[5]; ?>')"><? echo number_format($yarn_allo_qnty,2,'.','');?></a>
																<?
																$html.=number_format($yarn_allo_qnty,2);
																$d++;
															}

															if($d!=1)
															{
																echo "<hr/>";
																$html.="<hr/>";
															}

															$yarn_desc=join(",",$yarn_desc_array);

															$allo_qnty_not_req=$yarn_allocation_arr['not_req'];

															$html.=number_format($allo_qnty_not_req,2);
															//$html_short.=number_format($iss_qnty_not_req+$yarn_issued,2);
															?>
															<a href="##" onClick="openmypage('<? echo $poId_id; ?>','yarn_allocation_not','<? echo $yarn_desc; ?>','','','','','')"><? echo number_format($allo_qnty_not_req,2);?></a>
														<?
														}
														$html.="</td><td>";
													?>
												</td>
												 <td width="100" align="right">
													<?
														if($z==1)
														{
															//echo $job_yarnAllocationQty;
															$job_yetTo_allocate=$required_qnty-$job_yarnAllocationQty;
															echo "<font color='$bgcolor' style='display:none'>".number_format($job_yetTo_allocate,2,'.','')."</font>\n";
															$tot_yetTo_allocate+=$job_yetTo_allocate;
															echo number_format($job_yetTo_allocate,2,'.','');
															$html.=number_format($job_yetTo_allocate,2);
														}
														$html.="</td><td>";
													?>
												</td>
												<td width="100" align="right" bgcolor="<? echo $discrepancy_td_color; ?>">
													<?
														if($z==1)
														{
															echo "<font color='$bgcolor' style='display:none'>".number_format($yarn_issued,2,'.','')."</font>\n";
															$d=1;
															foreach($yarn_desc_array as $yarn_desc)
															{
																if($d!=1)
																{
																	echo "<hr/>";
																	$html.="<hr/>";
																	$html_short.="<hr/>";
																	$html_medium.="<hr/>";
																}

																$yarn_iss_qnty=$yarn_iss_qnty_array[$yarn_desc];
																$yarn_desc_for_popup=explode("__",$yarn_desc_array_for_popup[$d]);

																?>
																<a href="##" onClick="openmypage('<? echo $poId_id; ?>','yarn_issue','<? echo $yarn_desc_for_popup[0]; ?>','<? echo $yarn_desc_for_popup[1]; ?>','<? echo $yarn_desc_for_popup[2]; ?>','<? echo $yarn_desc_for_popup[3]; ?>','<? echo $yarn_desc_for_popup[4]; ?>','<? echo $yarn_desc_for_popup[5]; ?>')"><? echo number_format($yarn_iss_qnty,2,'.','');?></a>
																<?
																$html.=number_format($yarn_iss_qnty,2);
																$html_short.=number_format($yarn_iss_qnty,2);
																$html_medium.=number_format($yarn_iss_qnty,2);
																$d++;
															}

															if($d!=1)
															{
																echo "<hr/>";
																$html.="<hr/>";
																$html_short.="<hr/>";
																$html_medium.="<hr/>";
															}

															$yarn_desc=join(",",$yarn_desc_array);

															$iss_qnty_not_req=$yarn_iss_qnty_array['not_req'];

															$html.=number_format($iss_qnty_not_req,2);
															$html_short.=number_format($iss_qnty_not_req,2);
															$html_medium.=number_format($yarn_issued,2);
															?>
															<a href="##" onClick="openmypage('<? echo $poId_id; ?>','yarn_issue_not','<? echo $yarn_desc; ?>','','','','','')"><? echo number_format($iss_qnty_not_req,2);?></a>
														<?
														}
														?>
												</td>
												<? $html.="</td><td>"; ?>
												<td width="100" align="right">
													<?
														if($z==1)
														{
														?>
															<a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $poId_id; ?>','yarn_trans','')"><? echo number_format($net_trans_yarn,2,'.','');  ?></a>
														<?
															$html.=number_format($net_trans_yarn,2);
															$tot_net_trans_yarn_qnty+=$net_trans_yarn;
														}
													?>
												</td>
												<? $html.="</td><td>"; $html_medium.="</td><td>"; ?>
												<td width="100" align="right">
												<?
													if($z==1)
													{
														echo number_format($balance,2,'.','');
														$html.=number_format($balance,2);
														$html_medium.=number_format($balance,2);
													}
												?>
												</td>
												<? $html.="</td><td bgcolor='$bgcolor_grey_td'>"; $html_short.="</td><td bgcolor='$bgcolor_grey_td'>"; $html_medium.="</td><td bgcolor='$bgcolor_grey_td'>"; ?>
												<td width="100" align="right" bgcolor="<? echo $bgcolor_grey_td; ?>">
												<?
													if($z==1)
													{
														echo number_format($required_qnty,2,'.','');
														$html.=number_format($required_qnty,2);
														$html_short.=number_format($required_qnty,2);
														$html_medium.=number_format($required_qnty,2);
													}
												?>
												</td>
												<? $html.="</td><td bgcolor='$discrepancy_td_color'>"; $html_short.="</td><td>"; $html_medium.="</td><td>"; ?>
												<td width="100" align="right" bgcolor="<? echo $discrepancy_td_color; ?>">
													<?
														if($z==1)
														{
														?>
															<a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $poId_id; ?>','grey_receive','')"><? echo number_format($grey_recv_qnty,2,'.',''); ?></a>
														<?
															$html.=number_format($grey_recv_qnty,2);
															$html_short.=number_format($grey_recv_qnty,2);
															$html_medium.=number_format($grey_recv_qnty,2);
														}
													?>
												</td>
												<? $html.="</td><td bgcolor='$discrepancy_td_color'>"; $html_medium.="</td><td bgcolor='$discrepancy_td_color'>"; ?>
												<td width="100" align="right">
													<?
														if($z==1)
														{
															echo number_format($grey_prod_balance,2,'.','');
															$html.=number_format($grey_prod_balance,2);
															$html_medium.=number_format($grey_prod_balance,2);
														}
													?>
												</td>
												<? $html.="</td><td bgcolor='$discrepancy_td_color'>"; ?>
												<td width="100" align="right">
												<?
													if($z==1)
													{
														?>
															<a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $poId_id; ?>','grey_delivery_to_store','')"><? echo number_format($grey_del_store,2,'.',''); ?></a>
														<?
														$html.=number_format($grey_del_store,2);
													}
												?>
												</td>
												<? $html.="</td><td bgcolor='$discrepancy_td_color'>"; ?>
												<td width="100" align="right">
												<?
													$greyKnitFloor=0;
													if($z==1)
													{
														$greyKnitFloor=$grey_recv_qnty-$grey_del_store;
														echo number_format($greyKnitFloor,2,'.','');
														$html.=number_format($greyKnitFloor,2);
														$tot_greyKnitFloor+=$greyKnitFloor;
													}
												?>
												</td>
												<? $html.="</td><td>"; ?>
												<td width="100" align="right">
													<?
														if($z==1)
														{
														?>
															<a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $poId_id.'_'.'9'; ?>','grey_purchase','')"><? echo number_format($grey_production_qnty,2,'.',''); ?></a>
														<?
															$html.=number_format($grey_production_qnty,2);
														}
													?>
												</td>
												<? $html.="</td><td bgcolor='$bgcolor_grey_td'>"; ?>
												<td width="100" align="right">
													<?
														if($z==1)
														{
														?>
															<a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $poId_id.'_'.'0'; ?>','grey_purchase','')"><? echo number_format($grey_purchase_qnty,2,'.',''); ?></a>
														<?
															$html.=number_format($grey_purchase_qnty,2);
														}
													?>
												</td>
												<? $html.="</td><td>"; ?>
												<td width="100" align="right">
													<?
														if($z==1)
														{
														?>
                                                        	<a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $poId_id; ?>','grey_return','')"><? echo number_format($grey_net_return,2,'.',''); ?></a>
														<?
															$html.=number_format($grey_net_return,2);
															$tot_net_gray_return+=$grey_net_return;
														}
													?>
												</td>
												<? $html.="</td><td>"; ?>
												<td width="100" align="right">
													<?
														if($z==1)
														{
														?>
															<a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $poId_id; ?>','knit_trans','')"><? echo number_format($net_trans_knit,2,'.','');  ?></a>
														<?
															$html.=number_format($net_trans_knit,2);
															$tot_net_trans_knit_qnty+=$net_trans_knit;
														}
													?>
												</td>
												<? $html.="</td><td>"; ?>
												<td width="100" align="right" title="Total Receive=( Grey Rcvd (Prod.) + Grey Rcvd (Purchase) + Net Transfer)">
												<?
													//
													//$grey_available=$grey_production_qnty+$grey_purchase_qnty+$net_trans_knit;
													if($z==1)
													{
														echo number_format($grey_available,2,'.','');
														$html.=number_format($grey_available,2);
													}
												?>
												</td>
												<? $html.="</td><td>"; ?>
												<td width="100" align="right" title="Required (As per Booking) - Grey Actual Recv.">
													<?
														if($z==1)
														{
															echo number_format($grey_balance,2,'.','');
															$html.=number_format($grey_balance,2);
														}
													?>
												</td>
												<? $html.="</td><td>"; ?>

												<td width="100" align="right">
													<?
														if($z==1)
														{
														?>
															<a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $poId_id; ?>','grey_issue','')"><?
															$grey_fabric_issue=$grey_fabric_issue-$grey_net_return;
															 echo number_format($grey_fabric_issue,2,'.',''); ?></a>
														<?
															$html.=number_format($grey_fabric_issue,2);
														}
													?>
												</td>
												<? $html.="</td><td>"; ?>
                                                <td width="100" align="right" title="Grey Actual Recv.-Net Grey Issue">
												<?
													if($z==1)
													{
														echo number_format($grey_in_hand,2,'.','');
														$html.=number_format($grey_in_hand,2);
														$tot_grey_in_hand+=$grey_in_hand;
													};
												?>
											</td>
											<? $html.="</td><td>"; ?>

												<td width="100" align="right">
													<?
														if($z==1)
														{
															?>
                                                            <a href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $poId_id; ?>','receive_by_batch','')"><? echo number_format($receive_by_batch_qnt,2,'.',''); ?></a>
                                                            <?
															$html.=number_format($receive_by_batch_qnt,2);
														}
													?>
												</td>
												<? $html.="</td><td bgcolor='#FF9BFF'>"; $html_short.="</td><td bgcolor='#FF9BFF'>"; $html_medium.="</td><td bgcolor='#FF9BFF'>"; ?>
												<td width="100" align="center" bgcolor="#FF9BFF">
													<p>
														<?
															if($key==0)
															{
																echo "-";
																$html.="-"; $html_short.="-"; $html_medium.="-";
															}
															else
															{
																echo $color_array[$key];
																echo "<span style='font-size:10px;'>LD No. ".$lapdip_arr[$job_no.$po_id.$key].'</span>';
																$html.=$color_array[$key]; $html_short.=$color_array[$key]; $html_medium.=$color_array[$key];
															}

														?>
													</p>
												</td>
												<? $html.="</td><td>";

												$batch_color_qnty=0; $fab_recv_qnty=0; $fab_production_qnty=0; $fab_purchase_qnty=0; $issue_to_cut_qnty=0; $dye_qnty=0; $fin_delivery_qty=0; $fab_net_return=0; $fab_rec_return=0; $fab_issue_return=0;
												$job_po_id_batch=array_unique(explode(",",$poId_id));
												foreach($job_po_id_batch as $val)
												{

													$batch_color_qnty+=$batch_qnty_arr[$val][$key];
													//$tot_batch_qnty+=$batch_color_qnty;
													$fab_rec_return_production=$finish_recv_rtn_qnty_arr[$val][$key]['production'];
													$fab_rec_return_purchase=$finish_recv_rtn_qnty_arr[$val][$key]['purchase'];

													$fab_recv_qnty+=$finish_receive_qnty_arr[$val][$key];
													$fab_production_qnty+=$finish_purchase_qnty_arr[$val][$key]['production']-$fab_rec_return_production;
													$fab_purchase_qnty+=$finish_purchase_qnty_arr[$val][$key]['purchase']-$fab_rec_return_purchase;
													$issue_to_cut_qnty+=$finish_issue_qnty_arr[$val][$key];

													$fab_rec_return+=$fab_rec_return_production+$fab_rec_return_purchase;
													$fab_issue_return+=$finish_issue_rtn_qnty_arr[$val][$key];
													$fab_net_return+=$fab_issue_return-$fab_rec_return;
													//$dye_qnty+=$dye_qnty_arr[$val][$key];
													$dye_qnty+=$dye_qnty_arr[$val][$key];
													$fin_delivery_qty+=$finDeliveryArray[$val][$key];
												}
												?>
												<td width="100" align="right">
													<?
														$grey_req_color_qty=0;
														$grey_req_color_qty=$grey_req_color_arr[$key];
														$html.=number_format($grey_req_color_qty,2);
														$tot_grey_req_color_qty+=$grey_req_color_qty;

													echo number_format($grey_req_color_qty,2,'.',''); ?>
												</td>
											   <? $html.="</td><td>"; ?>

												<td width="100" align="right">
													<a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $poId_id.'_'.$key; ?>','batch_qnty','')"><? echo number_format($batch_color_qnty,2,'.','');
													$html.=number_format($batch_color_qnty,2);
													$batch_qnty_array[$buyer_name]+=$batch_color_qnty;
													$tot_batch_qnty+=$batch_color_qnty;
													$tot_batch_qnty_excel+=$batch_color_qnty;
												?></a>
												</td>
												<? $html.="</td><td>"; $html_short.="</td><td bgcolor='#FF9BFF'>"; $html_medium.="</td><td bgcolor='#FF9BFF'>"; ?>
												<td width="100" align="right">
													<a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $poId_id; ?>','dye_qnty','<? echo $key; ?>')"><? echo number_format($dye_qnty,2,'.',''); ?></a>
													<?
														$html.=number_format($dye_qnty,2);
														$html_short.=number_format($dye_qnty,2);
														$html_medium.=number_format($dye_qnty,2);

														$dye_qnty_array[$buyer_name]+=$dye_qnty;
														$tot_dye_qnty+=$dye_qnty;
													?>
												</td>
												<? $html.="</td><td>"; $html_short.="</td><td>"; $html_medium.="</td><td>"; ?>
												<td width="100" align="right" title="Grey Req. Color - Dye Qty">
													<?
														$grey_balance_color_qty=0;
														$grey_balance_color_qty=$grey_req_color_qty-$dye_qnty;
														$html.=number_format($grey_balance_color_qty,2);
														$tot_grey_balance_color_qty+=$grey_balance_color_qty;

													echo number_format($grey_balance_color_qty,2,'.',''); ?>
												</td>
											   <? $html.="</td><td>"; ?>
												<td width="100" align="right">
													<?
														$dyeing_balance=$batch_color_qnty-$dye_qnty;
														echo number_format($dyeing_balance,2);
														$html.=number_format($dyeing_balance,2);
														$html_short.=number_format($dyeing_balance,2);
														$html_medium.=number_format($dyeing_balance,2);

														//$dye_qnty_array[$buyer_name]+=$dyeing_balance;
														//$tot_dye_qnty+=$dyeing_balance;
														$tot_dye_qnty_balance+=$dyeing_balance;
													?>
												</td>
												<? $html.="</td><td>"; ?>
												<td width="100" align="right">
													<?
														echo number_format($value,2,'.','');
														$html.=number_format($value,2);

														$fin_fab_Requi_array[$buyer_name]+=$value;
														$tot_color_wise_req+=$value;
													?>
												</td>
												<? $html.="</td><td>"; $html_medium.="</td><td>"; ?>
												<td width="100" align="right">
													<a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $poId_id; ?>','fabric_receive','<? echo $key; ?>')"><? echo number_format($fab_recv_qnty,2,'.',''); ?></a>
													<?
														$html.=number_format($fab_recv_qnty,2);
														$html_short.=number_format($fab_recv_qnty,2);
														$html_medium.=number_format($fab_recv_qnty,2);

														$finFabProductionArr[$buyer_name]+=$fab_recv_qnty;
														$tot_fabric_recv+=$fab_recv_qnty;
														$tot_fabric_recv_excel+=$fab_recv_qnty;
													?>
												</td>
												<? $html.="</td><td>"; $html_short.="</td><td>"; $html_medium.="</td><td>"; ?>
												<td width="100" align="right">
													<?
														$finish_balance=$value-$fab_recv_qnty;
														echo number_format($finish_balance,2,'.','');
														$html.=number_format($finish_balance,2);
														$html_short.=number_format($finish_balance,2);
														$html_medium.=number_format($finish_balance,2);

														//$fin_fab_recei_array[$buyer_name]+=$finish_balance;
														$tot_fabric_recv_balance+=$finish_balance;
													?>
												</td>
												<? $html.="</td><td>"; ?>
												<td width="100" align="right">
															<a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $poId_id.'_'.$key; ?>','finish_delivery_to_store','')"><? echo number_format($fin_delivery_qty,2,'.',''); ?></a>
														<?
														$html.=number_format($fin_delivery_qty,2);

														//$fin_fab_recei_array[$buyer_name]+=$fin_delivery_qty;
														$tot_fin_delivery_qty+=$fin_delivery_qty;
													?>
												</td>
												<? $html.="</td><td>"; ?>
												<td width="100" align="right">
													<?
														$finProdFloor=$fab_recv_qnty-$fin_delivery_qty;
														echo number_format($finProdFloor,2,'.','');
														$html.=number_format($finProdFloor,2);
														//$fin_fab_recei_array[$buyer_name]+=$finProdFloor;
														$tot_finProdFloor+=$finProdFloor;
													?>
												</td>
												<? $html.="</td><td>"; ?>
												<td width="100" align="right">
													<a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $poId_id.'_'.'9'; ?>','fabric_purchase','<? echo $key; ?>')"><? echo number_format($fab_production_qnty,2,'.',''); ?></a>
													<?
														$html.=number_format($fab_production_qnty,2);

														//$fin_fab_recei_array[$buyer_name]+=$fab_purchase_qnty;
														$tot_fabric_production+=$fab_production_qnty;
													?>
												</td>
												<? $html.="</td><td>"; $html_short.="</td><td>"; //$html_medium.="</td><td>"; ?>
												<td width="100" align="right">
													<a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $poId_id.'_'.'0'; ?>','fabric_purchase','<? echo $key; ?>')"><? echo number_format($fab_purchase_qnty,2,'.',''); ?></a>
													<?
														$html.=number_format($fab_purchase_qnty,2);
														$html_short.=number_format($fab_purchase_qnty,2);
														//$html_medium.=number_format($fab_purchase_qnty,2);

														//$fin_fab_recei_array[$buyer_name]+=$fab_purchase_qnty;
														$tot_fabric_purchase+=$fab_purchase_qnty;
													?>
												</td>
												<? $html.="</td><td>"; ?>
												<td width="100" align="right">
                                                <a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $poId_id; ?>','finish_return','<? echo $key; ?>')"><? echo number_format($fab_net_return,2,'.',''); ?></a>
													<?
														$html.=number_format($fab_net_return,2);
														//$html_short.=number_format($fab_purchase_qnty,2);

														//$fin_fab_recei_array[$buyer_name]+=$fab_purchase_qnty;
														$tot_fab_net_return+=$fab_net_return;
													?>
												</td>
												<? $html.="</td><td>"; ?>
												<td width="100" align="right">
													<?
														$net_trans_finish=0;
														$job_po_id=explode(",",$poId_id);
														foreach($job_po_id as $val)
														{
															$net_trans_finish+=$trans_qnty_fin_arr[$val][$key]['trans'];
														}
													?>
														<a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $poId_id; ?>','finish_trans','<? echo $key; ?>')"><? echo number_format($net_trans_finish,2,'.','');  ?></a>
													<?
														$html.=number_format($net_trans_finish,2);
														//$fin_fab_recei_array[$buyer_name]+=$net_trans_finish;
														$tot_net_trans_finish_qnty+=$net_trans_finish;
													?>
												</td>
												<? $html.="</td><td>"; ?>
												<td width="100" align="right" title="Total Receive=(Received Prod. + Received Purchase + Net Transfer)">
													<?
														$fabric_available=$fab_production_qnty+$fab_purchase_qnty+$net_trans_finish;
														$fin_fab_recei_array[$buyer_name]+=$fabric_available;
														echo number_format($fabric_available,2,'.','');
														$html.=number_format($fabric_available,2);
														$tot_fabric_available+=$fabric_available;
													?>
												</td>
												<? $html.="</td><td>"; ?>
												<td width="100" align="right">
													<?
														$fabric_receive_bal=$value-$fabric_available;
														echo number_format($fabric_receive_bal,2,'.','');
														$fin_balance_array[$buyer_name]+=$fabric_receive_bal;
														$html.=number_format($fabric_receive_bal,2);
														$tot_fabric_rec_bal+=$fabric_receive_bal;
													?>
												</td>
												<? $html.="</td><td>"; $html_short.="</td><td>"; $html_medium.="</td><td>"; ?>
												<td width="100" align="right">
													<a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $poId_id; ?>','issue_to_cut','<? echo $key; ?>')"><? echo number_format($issue_to_cut_qnty,2,'.',''); ?></a>
													<?
														$html.=number_format($issue_to_cut_qnty,2);
														$html_short.=number_format($issue_to_cut_qnty,2);
														$html_medium.=number_format($issue_to_cut_qnty,2);

														$issue_toCut_array[$buyer_name]+=$issue_to_cut_qnty;
														$tot_issue_to_cut_qnty+=$issue_to_cut_qnty;
													?>
												</td>
												<? $html.="</td><td>"; ?>
												<td width="100" align="right"><? $yet_to_cut_qty=$value-$issue_to_cut_qnty; echo number_format($yet_to_cut_qty,2,'.',''); $tot_yet_to_cut+=$yet_to_cut_qty; $html.=number_format($yet_to_cut_qty,2); ?></a>
												</td>
												<? $html.="</td><td>"; ?>
												<td width="100" align="right" title="Rec-Issue">
													<?
														$fabric_left_over=$fabric_available-$issue_to_cut_qnty;
														echo number_format($fabric_left_over,2,'.','');
														$html.=number_format($fabric_left_over,2);
														$tot_fabric_left_over+=$fabric_left_over;
														$tot_fabric_left_over_excel+=$fabric_left_over;
													?>
												</td>
												<? $html.="</td><td>"; ?>
												<td width="100">&nbsp; <? $html.="&nbsp;"; ?></td>
												<td>
													<p>
														<? $fabric_desc=explode(",",$fabric_desc_details[$job_no]); echo $display_font_color.join(",<br>",array_unique($fabric_desc)).$font_end; ?>
													</p>
												</td>
											</tr>
										 <?
											if($z==1) $html.="</td><td>".join(",<br>",array_unique($fabric_desc))."</td></tr>"; else $html.="</td>&nbsp;<td></td></tr>";
											$html_short.="</td></tr>"; $html_medium.="</td></tr>";
										$z++;
										$k++;
										}
									}
									else
									{
										$html.="<tr bgcolor='".$bgcolor."'>
														<td align='left'>".$i."</td>
														<td align='left'>".$main_booking_excel."</td>
														<td align='left'>".$sample_booking_excel."</td>
														<td align='left'>".$all_book_prefix_no."</td>
														<td align='center'>".$job_no."</td>
														<td align='center'><img  src='../../".$imge_arr[$job_no]."' height='25' width='30' /></td>
														<td align='left'>".$po_number."</td>
														<td align='left'></td>
														<td>".$buyer_short_name_library[$buyer_name]."</td>
														<td align='left'>".$style_ref_no."</td>
														<td align='left'>".$file_no."</td>
														<td align='left'>".$grouping."</td>
														<td align='left'>".$gmts_item."</td>
														<td align='right'>".$order_qnty_in_pcs."</td>
														<td align='left'>View</td>
														<td align='center'>".$contry_ship_date."</td>
														<td align='right'>".$country_ship_qty."</td>
														<td align='right'>".$grey_cons."</td>
														<td align='right'>".$fin_cons."</td>";

										$html_short.="<tr bgcolor='".$bgcolor."'>
													<td align='left'>".$i."</td>
													<td align='left'>".$main_booking_excel."</td>
													<td align='left'>".$sample_booking_excel."</td>
													<td align='left'>".$all_book_prefix_no."</td>
													<td align='left'>".$po_number."</td>
													<td>".$buyer_short_name_library[$buyer_name]."</td>
													<td>".$file_no."</td>
													<td>".$grouping."</td>
													<td align='right'>".$order_qnty_in_pcs."</td>
													<td align='left'>View</td>";
										$html_medium.="<tr bgcolor='".$bgcolor."'>
													<td align='left'>".$i."</td>
													<td>".$buyer_short_name_library[$buyer_name]."</td>
													<td align='left'>".$main_booking_excel."</td>
													<td align='left'>".$sample_booking_excel."</td>
													<td align='left'>".$all_book_prefix_no."</td>
													<td align='left'>".$po_number."</td>
													<td align='left'>".$grouping."</td>
													<td align='left'>".$file_no."</td>
													<td align='left'>".$style_ref_no."</td>
													<td align='right'>".$order_qnty_in_pcs."</td>
													<td align='left'>View</td>
													<td align='left'>View</td>";

										?>
										<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
											<td width="40"><? echo $i; ?></td>
											<td width="125"><? echo $main_booking; ?></td>
											<td width="125"><? echo $sample_booking; ?></td>
											<td width="75" style="word-break:break-all"><? echo $all_book_prefix_no; ?></td>
											<td width="100" align="center"><? echo $job_no; ?></td>
                                            <td width="40" onClick="openmypage_image('requires/fabric_receive_status_report2_controller.php?action=show_image&job_no=<?=$job_no; ?>','Image View')"><img  src="../../<?=$imge_arr[$job_no]; ?>" height='25' width='30' /></td>
											<td width="120"><p><? echo $po_number; ?></p></td>
											<td width="90" align="center"><? //echo $order_status[$row[csf('is_confirmed')]]; ?></td>
											<td width="80"><p><? echo $buyer_short_name_library[$buyer_name]; ?></p></td>
											<td width="130"><p><? echo $style_ref_no; ?></p></td>
											<td width="100"><p><? echo $file_no; ?></p></td>
											<td width="100"><p><? echo $grouping; ?></p></td>
											<td width="140"><p><? echo $gmts_item; ?></p></td>
											<td width="100" align="right"><? echo number_format($order_qnty_in_pcs,0,'.',''); ?></td>
											<td width="80" align="center"><a href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $poId_id; ?>','Shipment_date','')"><? echo "View"; ?></a></td>
											<td width="80"><p><? echo $contry_ship_date; ?></p></td>
											<td width="100" align="right"><a href="##" onClick="country_order_dtls('<? echo $poId_id; ?>','<? echo $start_date; ?>','<? echo $end_date; ?>','<? echo $buyer_name; ?>','<? echo $job_no; ?>','country_order_dtls_popup')"><? echo number_format($country_ship_qty,0,'.',''); ?></a></td>
											<td width="100" align="right"><? echo number_format($grey_cons,5,'.',''); ?></td>
											<td width="100" align="right"><? echo number_format($fin_cons,5,'.',''); ?></td>
											<td width="70">
												<?
													 $html.="<td>"; $d=1;
													 foreach($yarn_data_array['count'] as $yarn_count_value)
													 {
														if($d!=1)
														{
															echo "<hr/>";
															$html.="<hr/>";
														}

														echo $yarn_count_value;
														$html.=$yarn_count_value;

													 $d++;
													 }

													 $html.="</td><td>";
												?>
											</td>
											<td width="110">
												<div style="word-wrap:break-word; width:110px">
													<?
														 $d=1;
														 foreach($yarn_data_array['comp'] as $yarn_composition_value)
														 {
															if($d!=1)
															{
																echo "<hr/>";
																$html.="<hr/>";
															}

															echo $yarn_composition_value;
															$html.=$yarn_composition_value;

														 $d++;
														 }

														 $html.="</td><td>";
													?>
												</div>
											</td>
											<td width="80">
												<p>
													<?
														 $d=1;
														 foreach($yarn_data_array['type'] as $yarn_type_value)
														 {
															if($d!=1)
															{
																echo "<hr/>";
																$html.="<hr/>";
															}

															echo $yarn_type_value;
															$html.=$yarn_type_value;

														 $d++;
														 }

														 $html.="</td><td>";
													?>
												</p>
											</td>
											<td width="100" align="right">
												<?
													echo "<font color='$bgcolor' style='display:none'>".number_format(array_sum($mkt_required_array),2,'.','')."</font>\n";
													$d=1;
													foreach($mkt_required_array as $mkt_required_value)
													{
														if($d!=1)
														{
															echo "<hr/>";
															$html.="<hr/>";
														}

														$yarn_desc_for_popup_req=explode("__",$yarn_desc_array_for_popup[$d]);

														?>
														<a href="##" onClick="openmypage('<? echo $poId_id; ?>','yarn_req','<? echo $yarn_desc_for_popup_req[0]; ?>','<? echo $yarn_desc_for_popup_req[1]; ?>','<? echo $yarn_desc_for_popup_req[2]; ?>','<? echo $yarn_desc_for_popup_req[3]; ?>','<? echo $yarn_desc_for_popup_req[4]; ?>','<? echo $yarn_desc_for_popup_req[5]; ?>')"><? echo number_format($mkt_required_value,2,'.','');?></a>
													<?
													$html.=number_format($mkt_required_value,2);
													$d++;
													}

													$html.="</td><td bgcolor='$discrepancy_td_color'>"; $html_short.="<td>"; $html_medium.="<td>";
												?>
											</td>
											<td width="100" align="right">
												<?
													if($z==1)
													{
														$d=1;
														foreach($yarn_desc_array as $yarn_desc)
														{
															if($d!=1)
															{
																echo "<hr/>";
																$html.="<hr/>";
															}
															$yarn_allocation_value=$yarn_allocation_arr[$yarn_desc];
															$yarn_desc_for_popup_req=explode("__",$yarn_desc_array_for_popup[$d]);
															?>
															<a href="##" onClick="openmypage('<? echo $poId_id; ?>','yarn_allocation_pop','<? echo $yarn_desc_for_popup_req[0]; ?>','<? echo $yarn_desc_for_popup_req[1]; ?>','<? echo $yarn_desc_for_popup_req[2]; ?>','<? echo $yarn_desc_for_popup_req[3]; ?>','<? echo $yarn_desc_for_popup_req[4]; ?>','<? echo $yarn_desc_for_popup_req[5]; ?>')"><? echo number_format($yarn_allocation_value,2,'.','');?></a>
														<?
														$html.=number_format($yarn_allocation_value,2);
														$d++;
														}
													}
													$html.="</td><td bgcolor='$discrepancy_td_color'>";
												?>
											</td>
											<td width="100" align="right">
												<?
													if($z==1)
													{
														$d=1;
														foreach($yetTo_allocate_arr as $yetTo_allocate_value)
														{
															if($d!=1)
															{
																echo "<hr/>";
																$html.="<hr/>";
															}
															$yarn_desc_for_popup_req=explode("__",$yarn_desc_array_for_popup[$d]);
															echo number_format($yetTo_allocate_value,2,'.','');
															$html.=number_format($yetTo_allocate_value,2);
															$d++;
														}
													}
													$html.="</td><td bgcolor='$discrepancy_td_color'>";
												?>
											</td>
											<td width="100" align="right" bgcolor="<? echo $discrepancy_td_color; ?>">
												<?
													echo "<font color='$bgcolor' style='display:none'>".number_format($yarn_issued,2,'.','')."</font>\n";
													$d=1;
													foreach($yarn_desc_array as $yarn_desc)
													{
														if($d!=1)
														{
															echo "<hr/>";
															$html.="<hr/>";
															$html_short.="<hr/>";$html_medium.="<hr/>";
														}

														$yarn_iss_qnty=$yarn_iss_qnty_array[$yarn_desc];
														$yarn_desc_for_popup=explode("__",$yarn_desc_array_for_popup[$d]);

														?>
														<a href="##" onClick="openmypage('<? echo $poId_id; ?>','yarn_issue','<? echo $yarn_desc_for_popup[0]; ?>','<? echo $yarn_desc_for_popup[1]; ?>','<? echo $yarn_desc_for_popup[2]; ?>','<? echo $yarn_desc_for_popup[3]; ?>','<? echo $yarn_desc_for_popup[4]; ?>','<? echo $yarn_desc_for_popup[5]; ?>')"><? echo number_format($yarn_iss_qnty,2,'.','');?></a>
														<?
														$html.=number_format($yarn_iss_qnty,2);
														$html_short.=number_format($yarn_iss_qnty,2);
														$html_medium.=number_format($yarn_iss_qnty,2);
														$d++;
													}

													if($d!=1)
													{
														echo "<hr/>";
														$html.="<hr/>";
														$html_short.="<hr/>";
														$html_medium.="<hr/>";
													}

													$yarn_desc=join(",",$yarn_desc_array);

													$iss_qnty_not_req=$yarn_iss_qnty_array['not_req'];

													$html.=number_format($iss_qnty_not_req,2);
													$html_short.=number_format($iss_qnty_not_req,2);
													$html_medium.=number_format($yarn_issued,2);
													?>
													<a href="##" onClick="openmypage('<? echo $poId_id; ?>','yarn_issue_not','<? echo $yarn_desc; ?>','','','','','')"><? echo number_format($iss_qnty_not_req,2);?></a>

											</td>
											<? $html.="</td><td>"; ?>
											<td width="100" align="right">
												<a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $poId_id; ?>','yarn_trans','')"><? echo number_format($net_trans_yarn,2,'.','');  ?></a>
												<?
													$html.=number_format($net_trans_yarn,2);
													$tot_net_trans_yarn_qnty+=$net_trans_yarn;
												?>
											</td>
											<? $html.="</td><td>";  $html_medium.="</td><td>"; ?>
											<td width="100" align="right">
												<?
													echo number_format($balance,2,'.','');
													$html.=number_format($balance,2);
													$html_medium.=number_format($balance,2);
												?>
											</td>
											<? $html.="</td><td>"; ?>
											<td width="100" align="right" bgcolor="<? echo $bgcolor_grey_td; ?>"> <? echo number_format($required_qnty,2,'.',''); $html.=number_format($required_qnty,2); ?></td>
											<? $html.="</td><td>"; ?>
											<td width="100" align="right" bgcolor="<? echo $discrepancy_td_color; ?>"><a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $poId_id; ?>','grey_receive','')"><? echo number_format($grey_recv_qnty,2,'.',''); $html.=number_format($grey_recv_qnty,2); ?></a></td>
											<? $html.="</td><td>"; ?>
											<td width="100" align="right"><? echo number_format($grey_balance,2,'.',''); $html.=number_format($grey_balance,2); ?></td>
											<? $html.="</td><td>"; ?>
											<td width="100" align="right"><a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $poId_id; ?>','grey_purchase','')"><? echo number_format($grey_del_store,2,'.',''); $html.=number_format($grey_del_store,2); ?></a></td>
											<? $html.="</td><td>"; ?>
											<td width="100">&nbsp; <? $html.="&nbsp;"; ?></td>
											<? $html.="</td><td>"; ?>
											<td width="100" align="right"><a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $poId_id; ?>','grey_purchase','')"><? echo number_format($grey_production_qnty,2,'.',''); $html.=number_format($grey_production_qnty,2); ?></a></td>
											<? $html.="</td><td>"; ?>
											<td width="100" align="right"><a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $poId_id; ?>','grey_purchase','')"><? echo number_format($grey_purchase_qnty,2,'.',''); $html.=number_format($grey_purchase_qnty,2); ?></a></td>
											<? $html.="</td><td>"; ?>
											<td width="100" align="right"><a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $poId_id; ?>','grey_net_return','')"><? echo number_format($grey_net_return,2,'.',''); $html.=number_format($grey_net_return,2); ?></a></td>
											<? $html.="</td><td>"; ?>
											<td width="100" align="right">
												 <a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $poId_id; ?>','knit_trans','')"><? echo number_format($net_trans_knit,2,'.','');  ?></a>
												<?
													$html.=number_format($net_trans_knit,2);
													$tot_net_trans_knit_qnty+=$net_trans_knit;
												?>
											</td>
											<? $html.="</td><td>"; ?>
											<td width="100" align="right">
												<? $grey_available=0; $grey_available=$grey_production_qnty+$grey_purchase_qnty+$net_trans_knit;
												echo number_format($grey_available,2,'.',''); $html.=number_format($grey_available,2); ?>
											</td>
											<? $html.="</td><td>"; ?>
											<td width="100" align="right">
												<?
													echo number_format($grey_balance,2,'.','');
													$html.=number_format($grey_balance,2);
												?>
											</td>
											<? $html.="</td><td>"; ?>
											<td width="100" align="right"><a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $poId_id; ?>','grey_issue','')"><?
											$grey_fabric_issue=$grey_fabric_issue-$grey_net_return;
											 echo number_format($grey_fabric_issue,2,'.',''); $html.=number_format($grey_fabric_issue,2); ?></a>
											</td>
											<? $html.="</td><td>"; ?>

                                            <td width="100" align="right"><? echo number_format($grey_in_hand,2,'.',''); $html.=number_format($grey_in_hand,2); ?></td>
											<? $html.="</td><td>"; ?>
											<td width="100" align="right"><? echo number_format($receive_by_batch_qnt,2,'.',''); $html.=number_format($receive_by_batch_qnt,2); ?>
											</td>
											<? $html.="</td><td>"; ?>
											<td width="100">&nbsp;<? $html.="&nbsp;"; ?></td>
											<? $html.="</td><td>"; ?>
											<td width="100" align="right"><a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $poId_id; ?>','batch_qnty','')"><? $html.="&nbsp;";//echo number_format($batch_color_qnty,2,'.',''); ?></a></td>
											<? $html.="</td><td>"; ?>
											<td width="100">&nbsp;</td>
											<td width="100">&nbsp;</td>
											<td width="100">&nbsp;</td>
											<td width="100">&nbsp;</td>
											<td width="100">&nbsp;</td>
											<td width="100">&nbsp;</td>
											<td width="100">&nbsp;</td>
											<td width="100">&nbsp;</td>
											<td width="100">&nbsp;</td>
											<td width="100">&nbsp;</td>
											<td width="100">&nbsp;</td>
											<td width="100">&nbsp;</td>
											<td width="100">&nbsp;</td>
											<td width="100">&nbsp;</td>
											<td width="100">&nbsp;</td>
											<td width="100">&nbsp;</td>
											<td>
												<p>
													<? $fabric_desc=explode(",",$fabric_desc_details[$job_no]); echo join(",<br>",array_unique($fabric_desc)); ?>
												</p>
											</td>
										</tr>
										<?	$tot_batch_qnty_excel+=$batch_qnty;
											$html.="</td>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
												<td>".join(",<br>",array_unique($fabric_desc))."</td>
											</tr>
											";

											$html_short.="</td><td bgcolor='$bgcolor_grey_td'>".number_format($required_qnty,2)."</td>
												<td>".number_format($grey_recv_qnty,2)."</td>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
											</tr>";

											//number_format($grey_recv_qnty,2)
											$html_medium.="</td><td bgcolor='$bgcolor_grey_td'>".number_format($required_qnty,2)."</td>
											<td>".number_format($grey_recv_qnty,2)."</td>
											<td>".number_format($grey_prod_balance,2)."</td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											</tr>";
									$k++;
									}
								$i++;
								}
							}
						}// end main query
					}
					else
					{
						foreach($job_data_arr as $job_no=>$other_data)
						{
							$ex_data_job=explode('##',$other_data);
							$company_id=''; $buyer_name='';  $job_no_prefix_num=''; $style_ref_no=''; $gmts_item_id=''; $order_uom=''; $ratio=''; $poId_id=''; $grouping=''; $file_no=''; $po_number=''; $po_qnty=''; $pub_shipment_date=''; $shiping_status=''; $insert_date=''; $po_received_date=''; $plan_cut=''; $is_confirmed='';// $po_id
							$company_id=$ex_data_job[0];
							$buyer_name=$ex_data_job[1];
							$job_no_prefix_num=$ex_data_job[2];
							$style_ref_no=$ex_data_job[3];
							$gmts_item_id=$ex_data_job[4];
							$order_uom=$ex_data_job[5];
							$ratio=$ex_data_job[6];

							//$job_all_data=explode(',',$job_allData_arr[$job_no]);
							$job_all_data=array();
							$job_all_data[]=$job_allData_arr[$job_no];
							//echo $job_all_data;
							$grouping_all=''; $file_no_all=''; $po_number_all=''; $pub_shipment_date_all=''; $insert_date_all=''; $po_received_date_all=''; $po_id_all='';
							$bk=0;
							foreach ( $job_all_data as $poall_data )
							{
								//$bk++;
								//echo $bk;
								$po_data_arr=array_filter(explode('___',$poall_data));
								foreach($po_data_arr as $data_po)
								{
									$ex_data=explode('**',$data_po);

									if($grouping_all=="") $grouping_all=$ex_data[0]; else $grouping_all.=','.$ex_data[0];
									if($file_no_all=="") $file_no_all=$ex_data[1]; else $file_no_all.=','.$ex_data[1];
									if($po_number_all=="") $po_number_all=$ex_data[2]; else $po_number_all.=','.$ex_data[2];
									$po_qnty+=$ex_data[3];
									if($pub_shipment_date_all=="") $pub_shipment_date_all=$ex_data[4]; else $pub_shipment_date_all.=','.$ex_data[4];
									//$shiping_status=$ex_data[5];
									if($insert_date_all=="") $insert_date_all=$ex_data[6]; else $insert_date_all.=','.$ex_data[6];
									if($po_received_date_all=="") $po_received_date_all=$ex_data[7]; else $po_received_date_all.=','.$ex_data[7];
									$plan_cut+=$ex_data[8];
									//$is_confirmed=$ex_data[9];
									if($po_id_all=="") $po_id_all=$ex_data[10]; else $po_id_all.=','.$ex_data[10];
								}
							}
							//echo $po_number_all;
							$poId_id=implode(',',array_filter(array_unique(explode(',',$po_id_all))));
							$grouping=implode(',',array_filter(array_unique(explode(',',$grouping_all))));
							$file_no=implode(',',array_filter(array_unique(explode(',',$file_no_all))));
							$po_number=implode(', ',array_filter(array_unique(explode(',',$po_number_all))));
							$po_qnty=$po_qnty;
							$pub_shipment_date=implode(',',array_filter(array_unique(explode(',',$pub_shipment_date_all))));
							//$shiping_status='';
							$insert_date=implode(',',array_filter(array_unique(explode(',',$insert_date_all))));
							$po_received_date=implode(',',array_filter(array_unique(explode(',',$po_received_date_all))));
							$plan_cut=$plan_cut;
							//$is_confirmed='';
							//echo $po_number;
							$order_qnty_in_pcs=$po_qnty*$ratio;
							$plan_cut_qnty=$plan_cut*$ratio;
							$order_qty_array[$buyer_name]+=$order_qnty_in_pcs;

							$gmts_item='';
							$gmts_item_id=explode(",",$gmts_item_id);
							foreach($gmts_item_id as $item_id)
							{
								if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
							}

							$dzn_qnty=0; $balance=0; $job_mkt_required=0; $yarn_issued=0;
							if($costing_per_id_library[$job_no]==1) $dzn_qnty=12;
							else if($costing_per_id_library[$job_no]==3) $dzn_qnty=12*2;
							else if($costing_per_id_library[$job_no]==4) $dzn_qnty=12*3;
							else if($costing_per_id_library[$job_no]==5) $dzn_qnty=12*4;
							else $dzn_qnty=1;

							$dzn_qnty=$dzn_qnty*$ratio;

							$yarn_data_array=array(); $mkt_required_array=array(); $yarn_allocation_arr=array(); $yetTo_allocate_arr=array(); $req_for_allocate_arr=array();  $yarn_desc_array_for_popup=array(); $yarn_desc_array=array(); $yarn_iss_qnty_array=array(); $s=1;

							$yarn_descrip_data=$yarn_des_data_job[$job_no];
							$qnty=0;
							foreach($yarn_descrip_data as $count=>$count_value)
							{
								foreach($count_value as $Composition=>$composition_value)
								{
									foreach($composition_value as $percent=>$percent_value)
									{
										foreach($percent_value as $type_ref=>$type_value)
										{
											$count_id=$count;
											$copm_one_id=$Composition;
											$percent_one=$percent;
											$type_id=$type_ref;
											$qnty=$type_value;

											$mkt_required=$qnty;
											$mkt_required_array[$s]=$mkt_required;
											$job_mkt_required+=$mkt_required;

											$yarn_data_array['count'][$s]=$yarn_count_details[$count_id];
											$yarn_data_array['type'][$s]=$yarn_type[$type_id];

											$compos=$composition[$copm_one_id]." ".$percent_one." %"." ".$composition[$copm_two_id];
											$yarn_data_array['comp'][]=$compos;

											$yarn_desc_array[$s]=$yarn_count_details[$count_id]." ".$compos." ".$yarn_type[$type_id];
											$des_for_allocation=$yarn_count_details[$count_id]." ".$compos." ".$yarn_type[$type_id];

											$req_for_allocate_arr[$des_for_allocation]=$mkt_required;

											$yarn_desc_for_popup=$count_id."__".$copm_one_id."__".$percent_one."__".$copm_two_id."__".$percent_two."__".$type_id;
											$yarn_desc_array_for_popup[$s]=$yarn_desc_for_popup;
											$s++;
										}
									}
								}
							}

							$grey_production_qnty=0; $grey_purchase_qnty=0; $grey_net_return=0; $grey_recv_qnty=0; $grey_fabric_issue=0; $booking_data=''; $job_yarnAllocationQty=0; $grey_del_store=0; $receive_by_batch_qnt=0; $n=1;

							$job_po_id=explode(",",$poId_id); //$job_yetTo_allocate=0;
							foreach($job_po_id as $po_id)
							{
								$dataYarnIssue=explode(",",substr($dataArrayYarnIssue[$po_id],0,-1));
								foreach($dataYarnIssue as $yarnIssueRow)
								{
									$yarnIssueRow=explode("**",$yarnIssueRow);
									$yarn_count_id=$yarnIssueRow[0];
									$yarn_comp_type1st=$yarnIssueRow[1];
									$yarn_comp_percent1st=$yarnIssueRow[2];
									$yarn_comp_type2nd=$yarnIssueRow[3];
									$yarn_comp_percent2nd=$yarnIssueRow[4];
									$yarn_type_id=$yarnIssueRow[5];
									$issue_qnty=$yarnIssueRow[6];
									$return_qnty=$yarnIssueRow[7];

									if($yarn_comp_percent2nd!=0)
									{
										$compostion_not_req=$composition[$yarn_comp_type1st]." ".$yarn_comp_percent1st." %"." ".$composition[$yarn_comp_type2nd]." ".$yarn_comp_percent2nd." %";
									}
									else
									{
										$compostion_not_req=$composition[$yarn_comp_type1st]." ".$yarn_comp_percent1st." %"." ".$composition[$yarn_comp_type2nd];
									}

									$desc=$yarn_count_details[$yarn_count_id]." ".$compostion_not_req." ".$yarn_type[$yarn_type_id];

									$net_issue_qnty=$issue_qnty-$return_qnty;
									$yarn_issued+=$net_issue_qnty;
									if(!in_array($desc,$yarn_desc_array))
									{
										$yarn_iss_qnty_array['not_req']+=$net_issue_qnty;
									}
									else
									{
										$yarn_iss_qnty_array[$desc]+=$net_issue_qnty;
									}
								}

								$dataYarnAllocation=explode(",",substr($yarnAllocationArr[$po_id],0,-1));
								foreach($dataYarnAllocation as $yarnAllRow)
								{
									$yarnAlloRow=explode("**",$yarnAllRow);
									$yarn_count_id=$yarnAlloRow[0];
									$yarn_comp_type1st=$yarnAlloRow[1];
									$yarn_comp_percent1st=$yarnAlloRow[2];
									$yarn_comp_type2nd=$yarnAlloRow[3];
									$yarn_comp_percent2nd=$yarnAlloRow[4];
									$yarn_type_id=$yarnAlloRow[5];
									$yarnAllocationQty=$yarnAlloRow[6];

									if($yarn_comp_percent2nd!=0)
									{
										$compostion_not_req=$composition[$yarn_comp_type1st]." ".$yarn_comp_percent1st." % ".$composition[$yarn_comp_type2nd]." ".$yarn_comp_percent2nd." %";
									}
									else
									{
										$compostion_not_req=$composition[$yarn_comp_type1st]." ".$yarn_comp_percent1st." % ".$composition[$yarn_comp_type2nd];
									}

									$desc=$yarn_count_details[$yarn_count_id]." ".$compostion_not_req." ".$yarn_type[$yarn_type_id];
									$req_allocation=$req_for_allocate_arr[$desc];
									//$yetTo_allocate=$req_for_allocate_arr[$desc]-$yarnAllocationQty;
									//$job_yetTo_allocate+=$yetTo_allocate;
									$job_yarnAllocationQty+=$yarnAllocationQty;
									if(!in_array($desc,$yarn_desc_array))
									{
										$yarn_allocation_arr['not_req']+=$yarnAllocationQty;
										//$yetTo_allocate_arr['not_req']+=$req_for_allocate_arr[$desc]-$yarnAllocationQty;
									}
									else
									{
										$yarn_allocation_arr[$desc]+=$yarnAllocationQty;
										//$yetTo_allocate_arr[$desc]+=$req_for_allocate_arr[$desc]-$yarnAllocationQty;
									}
								}
								$grey_rec_rtn_purchase=$grey_receive_return_qnty_arr[$po_id]['purchase'];
								$grey_rec_rtn_production=$grey_receive_return_qnty_arr[$po_id]['production'];


								$grey_production_qnty+=$greyPurchaseQntyArray[$po_id]['production']-$grey_rec_rtn_production;
								$grey_purchase_qnty+=$greyPurchaseQntyArray[$po_id]['purchase']-$grey_rec_rtn_purchase;

								$grey_issue_rtn=$grey_issue_return_qnty_arr[$po_id];
								$grey_rec_rtn=$grey_rec_rtn_purchase+$grey_rec_rtn_production;
								$grey_net_return+=$grey_issue_rtn-$grey_rec_rtn;

								$grey_recv_qnty+=$grey_receive_qnty_arr[$po_id];


								$grey_fabric_issue+=$grey_issue_qnty_arr[$po_id];
								$receive_by_batch_qnt+=$receive_by_batch_data[$po_id];

								$grey_del_store+=$greyDeliveryArray[$po_id];

								$booking_data.=implode("__",explode("__",$dataArrayWo[$po_id])).",";
								$n++;
							}

							$total_grey_del_store+=$grey_del_store;

							if(($cbo_discrepancy==1 && $grey_recv_qnty>$yarn_issued) || ($cbo_discrepancy==0))
							{
								if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								$buyer_name_array[$buyer_name]=$buyer_short_name_library[$buyer_name];

								$booking_array=array(); $color_data_array=array(); $grey_req_color_arr=array();
								$required_qnty=0; $main_booking=''; $sample_booking=''; $main_booking_excel=''; $sample_booking_excel='';  $all_book_prefix_no = '';
								$dataArray=array_filter(explode("__",substr($booking_data,0,-1)));
								if(count($dataArray)>0)
								{
									foreach($dataArray as $woRow)
									{
										$woRow=explode("**",$woRow);
										$id=$woRow[0];
										$booking_no=$woRow[1];
										$insert_date=$woRow[2];
										$item_category=$woRow[3];
										$fabric_source=$woRow[4];
										$company_id=$woRow[5];
										$booking_type=$woRow[6];
										$booking_no_prefix_num=$woRow[7];
										$job_no_book=$woRow[8];
										$is_short=$woRow[9];
										$is_approved=$woRow[10];
										$fabric_color_id=$woRow[11];
										$req_qnty=$woRow[12];
										$grey_req_qnty=$woRow[13];
										$wo_po_id=$woRow[14];
										$book_prefix_no = $woRow[7];

										$required_qnty+=$grey_req_qnty;

										if(!in_array($id,$booking_array))
										{
											if($bookingEntryFromArr[$booking_no]==86)
											{
												$entryForm=$bookingEntryFromArr[$booking_no];
												$reportFormat=explode(",",$print_report_format_budget_booking);
												$reportFormat=$reportFormat[0];
												$action_namez=$report_format_arr[$reportFormat];
											}
											if($bookingEntryFromArr[$booking_no]==88)
											{
												$entryForm=$bookingEntryFromArr[$booking_no];
												$reportFormatshort=explode(",",$print_report_format_short_booking);
												$reportFormatshort=$reportFormatshort[0];
												$action_nameshort=$report_format_short_arr[$reportFormatshort];
											}
											if($bookingEntryFromArr[$booking_no]==118)
											{
												$entryForm=$bookingEntryFromArr[$booking_no];
												$reportFormatv2=explode(",",$print_report_format_budget_booking);
												$reportFormatv2=$reportFormatv2[0];
												$action_namemv2=$report_format_mainv2_arr[$reportFormatv2];
											}

											if($bookingEntryFromArr[$booking_no]==108)
											{
												$entryForm=$bookingEntryFromArr[$booking_no];
												$reportFormatPartial=explode(",",$print_report_format_partial_booking);
												$reportFormatPartial=$reportFormatPartial[0];
												$action_namep=$report_format_partial_arr[$reportFormatPartial];
											}

											$system_date=date('d-M-Y', strtotime($insert_date));
											$wo_color = "";
											if ($fabric_source == 2) $wo_color = "color:#000"; else $wo_color = "";

											if($booking_type==4)
											{
												//$action_name='show_fabric_booking_report';
												$action_name=$report_format_sample_arr[$reportFormatSample];
												$sample_booking.="<a href='##' style='color:#000' onclick=\"generate_worder_report('3','".$booking_no."','".$company_id."','".$wo_po_id."','".$item_category."','".$fabric_source."','".$job_no_book."','".$is_approved."','".$action_name."','".$print_report_format."')\"><font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")"."</font></a><br>";
												$sample_booking_excel.="<font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")"."</font><br>";
											}
											else
											{
												$all_book_prefix_no .= $book_prefix_no . ", ";
												if($is_short==1)
												{
													$pre="S";
													$action_name=$report_format_arr[$booking_print_arr[2]];
												}
												else
												{
													$pre="M";
													$action_name=$report_format_arr[$booking_print_arr[1]];
												}
												if($action_name=='') $action_name='show_fabric_booking_report';
												//if($is_short==1) $pre="S"; else $pre="M";
												//if($is_short==1) $pre="S"; else $pre="M";
												if($booking_no!="")
												{
													if($entryForm==118)
													{
														$action_name=$action_namemv2;
														$main_booking.="<a href='##' style='color:#000' onclick=\"generate_worder_report('".$is_short."','".$booking_no."','".$company_id."','".$wo_po_id."','".$item_category."','".$fabric_source."','".$job_no_book."','".$is_approved."','".$action_name."','".$reportFormatv2[0]."','".$entryForm."')\"><font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")".$pre."</font></a><br>";
														$main_booking_excel.="<font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")".$pre."</font><br>";
													}
													else if($entryForm==88)
													{
														$action_name=$action_nameshort;
														$main_booking.="<a href='##' style='color:#000' onclick=\"generate_worder_report('".$is_short."','".$booking_no."','".$company_id."','".$wo_po_id."','".$item_category."','".$fabric_source."','".$job_no_book."','".$is_approved."','".$action_name."','".$reportFormatshort[0]."','".$entryForm."')\"><font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")".$pre."</font></a><br>";
														$main_booking_excel.="<font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")".$pre."</font><br>";
													}
													else
													{
														$main_booking.="<a href='##' style='color:#000' onclick=\"generate_worder_report('".$is_short."','".$booking_no."','".$company_id."','".$wo_po_id."','".$item_category."','".$fabric_source."','".$job_no_book."','".$is_approved."','".'show_fabric_booking_report_jk'."','".'777'."')\"><font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")".$pre."</font></a><br>";
													}


												}
												else $main_booking.="No Booking";
												$main_booking_excel.="<font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")".$pre."</font><br>";
											}

											$booking_array[]=$id;
										}
										$color_data_array[$fabric_color_id]+=$req_qnty;
										$grey_req_color_arr[$fabric_color_id]+=$grey_req_qnty;
									}
								}
								else
								{
									$main_booking.="No Booking";
									$main_booking_excel.="No Booking";
									$sample_booking.="No Booking";
									$sample_booking_excel.="No Booking";
									$all_book_prefix_no = "&nbsp;";
								}
								$main_booking=implode("<br>",array_unique(explode("<br>",$main_booking)));
								if($main_booking=="")
								{
									$main_booking.="No Booking";
									$main_booking_excel.="No Booking";
								}

								if($sample_booking=="")
								{
									$sample_booking.="No Booking";
									$sample_booking_excel.="No Booking";
								}
								$all_book_prefix_no = chop($all_book_prefix_no, ",");

								$yarn_issue_array[$buyer_name]+=$yarn_issued;
								$grey_required_array[$buyer_name]+=$required_qnty;

								$net_trans_yarn=0; $net_trans_knit=0; $batch_qnty=0;
								foreach($job_po_id as $val)
								{
									$finish_color=array_unique(explode(",",$po_color_arr[$val]));
									foreach($finish_color as $color_id)
									{
										if($color_id>0)
										{
											$color_data_array[$color_id]+=0;
										}
									}

									$net_trans_yarn+=$trans_qnty_arr[$val]['yarn_trans'];
									$net_trans_knit+=$trans_qnty_arr[$val]['knit_trans'];
								}

								$yarn_issue_array[$buyer_name]+=$net_trans_yarn;
								$balance=$required_qnty-($yarn_issued+$net_trans_yarn);
								//$yetTo_allocate=$balance-$job_yarnAllocationQty;
								//$job_yetTo_allocate+=$yetTo_allocate;
								$yarn_balance_array[$buyer_name]+=$balance;
								$grey_available=$grey_production_qnty+$grey_purchase_qnty+$net_trans_knit;
								$knitted_array[$buyer_name]+=$grey_available;
								$grey_in_hand=$grey_available-$grey_fabric_issue;
								//$knitted_array[$buyer_name]+=$net_trans_knit;
								$grey_prod_balance=$required_qnty-$grey_recv_qnty;
								$grey_balance=$required_qnty-$grey_available;
								$tot_grey_prod_balance+=$grey_prod_balance;

								$grey_balance_array[$buyer_name]+=$grey_balance;

								$grey_issue_array[$buyer_name]+=$grey_fabric_issue;
								$receive_by_batch_array[$buyer_name]+=$receive_by_batch_qnt;

								//$batch_qnty_array[$buyer_name]+=$batch_qnty;

								$tot_order_qnty+=$order_qnty_in_pcs;
								$tot_mkt_required+=$job_mkt_required;
								$tot_yarnAllocationQty+=$job_yarnAllocationQty;

								$tot_yarn_issue_qnty+=$yarn_issued;
								$tot_fabric_req+=$required_qnty;
								$tot_balance+=$balance;
								$tot_grey_recv_qnty+=$grey_recv_qnty;
								$tot_grey_production_qnty+=$grey_production_qnty;
								$tot_grey_purchase_qnty+=$grey_purchase_qnty;
								$tot_grey_balance+=$grey_balance;
								$tot_grey_issue+=$grey_fabric_issue;
								$tot_receive_by_batch+=$receive_by_batch_qnt;
								//$tot_batch_qnty+=$batch_qnty;


								$tot_grey_available+=$grey_available;

								if($required_qnty>$job_mkt_required) $bgcolor_grey_td='#FF0000'; $bgcolor_grey_td='';

								$po_entry_date=date('d-m-Y', strtotime($insert_date));
								$costing_date=$costing_date_library[$job_no];

								$contry_ship_date=""; $country_ship_qty=0; $grey_cons=0; $fin_cons=0;
								$job_po_id=explode(",",$poId_id); //$job_yetTo_allocate=0;
								foreach($job_po_id as $po_id)
								{
									$country_date_all=array_filter(explode(",",$contry_ship_qty_arr[$po_id]['ship_date']));
									foreach($country_date_all as $date_all)
									{
										if($contry_ship_date=="") $contry_ship_date=change_date_format($date_all); else $contry_ship_date.=','.change_date_format($date_all);
									}
									$country_ship_qty+=$contry_ship_qty_arr[$po_id]['ship_qty'];

									$grey_cons+=$fabric_costing_arr['knit']['grey'][$po_id]/$plan_cut_qnty;//$reqArr[$job_no]['grey']/$dzn_qnty;
									$fin_cons+=$fabric_costing_arr['knit']['finish'][$po_id]/$plan_cut_qnty;//$reqArr[$job_no]['finish']/$dzn_qnty;
								}
								$tot_country_ship_qty+=$country_ship_qty;
								$country_order_qty_array[$buyer_name]+=$country_ship_qty;
								$contry_ship_date=implode(',<br>',array_unique(explode(',',$contry_ship_date)));
								//echo $country_ship_qty.'=='.$poId_id;
								//$grey_cons=$reqArr[$job_no]['grey']/$dzn_qnty;
								//$fin_cons=$reqArr[$job_no]['finish']/$dzn_qnty;

								$tot_color=count($color_data_array);
								//echo $tot_color;
								if($tot_color>0)
								{
									$z=1;
									foreach($color_data_array as $key=>$value)
									{
										if($z==1)
										{
											$display_font_color="";
											$font_end="";
										}
										else
										{
											$display_font_color="<font style='display:none' color='$bgcolor'>";
											$font_end="</font>";
										}


										if($z==1)
										{
											$html.="<tr bgcolor='".$bgcolor."'>
													<td align='left'>".$i."</td>
													<td align='left'>".$main_booking_excel."</td>
													<td align='left'>".$sample_booking_excel."</td>
													<td align='left'>" . $all_book_prefix_no . "</td>
													<td align='center'>".$job_no."</td>
													<td align='center'><img  src='../../".$imge_arr[$job_no]."' height='25' width='30' /></td>
													<td align='left'>".implode(",",array_unique(explode(",",$po_number)))."</td>
													<td align='left'></td>
													<td>".$buyer_short_name_library[$buyer_name]."</td>
													<td align='left'>".$style_ref_no."</td>
													<td align='left'>".implode(",",array_unique(explode(",",$file_no)))."</td>
													<td align='left'>".implode(",",array_unique(explode(",",$grouping)))."</td>
													<td align='left'>".$gmts_item."</td>
													<td align='right'>".$order_qnty_in_pcs."</td>
													<td align='left'>View</td>
													<td align='center'>".$contry_ship_date."</td>
													<td align='right'>".$country_ship_qty."</td>
													<td align='right'>".$grey_cons."</td>
													<td align='right'>".$fin_cons."</td>";


											$lead_time=0;
											$lead_time=datediff('d',$po_received_date,$pub_shipment_date);
											if($lead_time>0) $lead_time=$lead_time-1;

											$html_short.="<tr bgcolor='".$bgcolor."'>
														<td align='left'>".$i."</td>
														<td align='left'>".$main_booking_excel."</td>
														<td align='left'>".$sample_booking_excel."</td>
														<td align='left'>" . $all_book_prefix_no . "</td>
														<td align='left'>".$po_number."</td>
														<td>".$buyer_short_name_library[$buyer_name]."</td>
														<td>".$file_no."</td>
														<td>".$grouping."</td>
														<td align='right'>".$order_qnty_in_pcs."</td>
														<td align='left'>View</td>";
											$html_medium.="<tr bgcolor='".$bgcolor."'>
														<td align='left'>".$i."</td>
														<td>".$buyer_short_name_library[$buyer_name]."</td>
														<td align='left'>".$main_booking_excel."</td>
														<td align='left'>".$sample_booking_excel."</td>
														<td align='left'>" . $all_book_prefix_no . "</td>
														<td align='left'>".$po_number."</td>
														<td align='left'>".$grouping."</td>
														<td align='left'>".$file_no."</td>
														<td align='left'>".$style_ref_no."</td>

														<td align='right'>".$order_qnty_in_pcs."</td>
														<td align='left'>View</td>
														<td align='left'>View</td>";

										}
										else
										{
											$html.="<tr bgcolor='".$bgcolor."'>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>";

											$html_short.="<tr bgcolor='".$bgcolor."'>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>

															<td></td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>";
											$html_medium.="<tr bgcolor='".$bgcolor."'>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>";
										}
									?>
										<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
											<td width="40"><? echo $display_font_color.$i.$font_end; ?></td>
											<td width="125"><? echo $display_font_color.$main_booking.$font_end; ?></td>
											<td width="125"><? echo $display_font_color.$sample_booking.$font_end; ?></td>
											<td width="75" style="word-break:break-all; word-wrap: break-word;"><? echo rtrim($display_font_color.$all_book_prefix_no.$font_end,', '); ?></td>
											<td width="100" align="center"><? echo $display_font_color.$job_no.$font_end; ?></td>
                                            <td width="40" onClick="openmypage_image('requires/fabric_receive_status_report2_controller.php?action=show_image&job_no=<?=$job_no; ?>','Image View')"><?=$display_font_color; ?><img  src="../../<?=$imge_arr[$job_no]; ?>" height='25' width='30' /><?=$font_end; ?></td>
											<td width="120"><p><? echo $display_font_color.implode(",",array_unique(explode(",",$po_number))). $font_end; ?></p></td>
											<td width="90" align="center"><? //echo $order_status[$row[csf('is_confirmed')]]; ?></td>
											<td width="80"><p><? echo $display_font_color.$buyer_short_name_library[$buyer_name].$font_end; ?></p></td>
											<td width="130"><p><? echo $display_font_color.$style_ref_no.$font_end; ?></p></td>
											<td width="100"><p><? echo $display_font_color.implode(",",array_unique(explode(",",$file_no))).$font_end; ?></p></td>
											<td width="100"><p><? echo $display_font_color.implode(",",array_unique(explode(",",$grouping))).$font_end; ?></p></td>
											<td width="140"><p><? echo $display_font_color.$gmts_item; ?></p></td>
											<td width="100" align="right"><? if($z==1) echo number_format($order_qnty_in_pcs,0,'.',''); ?></td>
											<td width="80" align="center"><? echo $display_font_color; ?><a href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $poId_id; ?>','Shipment_date','')"><? echo "View"; ?></a><? echo $font_end; ?></td>
											<td width="80"><p><? echo $display_font_color.$contry_ship_date.$font_end; ?></p></td>
											<td width="100" align="right"><a href="##" onClick="country_order_dtls('<? echo $poId_id; ?>','<? echo $start_date; ?>','<? echo $end_date; ?>','<? echo $buyer_name; ?>','<? echo $job_no; ?>','country_order_dtls_popup')"><? if($z==1) echo number_format($country_ship_qty,0,'.',''); ?></a></td>
											<td width="100" align="right"><? if($z==1) echo number_format($grey_cons,5,'.',''); ?></td>
											<td width="100" align="right"><? if($z==1) echo number_format($fin_cons,5,'.',''); ?></td>
											<td width="70">
												<?
													 $html.="<td>"; $d=1;
													 foreach($yarn_data_array['count'] as $yarn_count_value)
													 {
														if($d!=1)
														{
															echo $display_font_color."<hr/>".$font_end;
															if($z==1) $html.="<hr/>";
														}

														echo $display_font_color.$yarn_count_value.$font_end;
														if($z==1) $html.=$yarn_count_value;
													 $d++;
													 }

													 $html.="</td><td>";
												?>
											</td>
											<td width="110">
												<div style="word-wrap:break-word; width:110px">
													<?
														 $d=1;
														 foreach($yarn_data_array['comp'] as $yarn_composition_value)
														 {
															if($d!=1)
															{
																echo $display_font_color."<hr/>".$font_end;
																if($z==1) $html.="<hr/>";
															}
															echo $display_font_color.$yarn_composition_value.$font_end;
															if($z==1) $html.=$yarn_composition_value;
														 $d++;
														 }

														 $html.="</td><td>";
													?>
												</div>
											</td>
											<td width="80">
												<p>
													<?
														 $d=1;
														 foreach($yarn_data_array['type'] as $yarn_type_value)
														 {
															if($d!=1)
															{
																echo $display_font_color."<hr/>".$font_end;
																if($z==1) $html.="<hr/>";
															}

															echo $display_font_color.$yarn_type_value.$font_end;
															if($z==1) $html.=$yarn_type_value;
														 $d++;
														 }

														 $html.="</td><td>";
													?>
												</p>
											</td>
											<td width="100" align="right">
												<?
													if($z==1)
													{
														echo "<font color='$bgcolor' style='display:none'>".number_format(array_sum($mkt_required_array),2,'.','')."</font>\n";
														$d=1;
														foreach($mkt_required_array as $mkt_required_value)
														{
															if($d!=1)
															{
																echo "<hr/>";
																$html.="<hr/>";
															}

															$yarn_desc_for_popup_req=explode("__",$yarn_desc_array_for_popup[$d]);

															?>
															<a href="##" onClick="openmypage('<? echo $poId_id; ?>','yarn_req','<? echo $yarn_desc_for_popup_req[0]; ?>','<? echo $yarn_desc_for_popup_req[1]; ?>','<? echo $yarn_desc_for_popup_req[2]; ?>','<? echo $yarn_desc_for_popup_req[3]; ?>','<? echo $yarn_desc_for_popup_req[4]; ?>','<? echo $yarn_desc_for_popup_req[5]; ?>')"><? echo number_format($mkt_required_value,2,'.','');?></a>
														<?
														$html.=number_format($mkt_required_value,2);
														$d++;
														}
													}
													$html.="</td><td bgcolor='$discrepancy_td_color'>"; $html_short.="<td>"; $html_medium.="<td>";
												?>
											</td>
											<td width="100" align="right">
												<?
													if($z==1)
													{
														echo "<font color='$bgcolor' style='display:none'>".number_format($job_yarnAllocationQty,2,'.','')."</font>\n";
														$d=1;
														foreach($yarn_desc_array as $yarn_desc)
														{
															if($d!=1)
															{
																echo "<hr/>";
																$html.="<hr/>";
															}

															$yarn_allo_qnty=$yarn_allocation_arr[$yarn_desc];
															$yarn_desc_for_popup=explode("__",$yarn_desc_array_for_popup[$d]);

															?>
															<a href="##" onClick="openmypage('<? echo $poId_id; ?>','yarn_allocation_pop','<? echo $yarn_desc_for_popup[0]; ?>','<? echo $yarn_desc_for_popup[1]; ?>','<? echo $yarn_desc_for_popup[2]; ?>','<? echo $yarn_desc_for_popup[3]; ?>','<? echo $yarn_desc_for_popup[4]; ?>','<? echo $yarn_desc_for_popup[5]; ?>')"><? echo number_format($yarn_allo_qnty,2,'.','');?></a>
															<?
															$html.=number_format($yarn_allo_qnty,2);
															$d++;
														}

														if($d!=1)
														{
															echo "<hr/>";
															$html.="<hr/>";
														}

														$yarn_desc=join(",",$yarn_desc_array);

														$allo_qnty_not_req=$yarn_allocation_arr['not_req'];

														$html.=number_format($allo_qnty_not_req,2);
														//$html_short.=number_format($iss_qnty_not_req+$yarn_issued,2);
														?>
														<a href="##" onClick="openmypage('<? echo $poId_id; ?>','yarn_allocation_not','<? echo $yarn_desc; ?>','','','','','')"><? echo number_format($allo_qnty_not_req,2);?></a>
													<?
													}
													$html.="</td><td>";
												?>
											</td>
											 <td width="100" align="right">
												<?
													if($z==1)
													{
														//echo $job_yarnAllocationQty;
														$job_yetTo_allocate=$required_qnty-$job_yarnAllocationQty;
														echo "<font color='$bgcolor' style='display:none'>".number_format($job_yetTo_allocate,2,'.','')."</font>\n";
														$tot_yetTo_allocate+=$job_yetTo_allocate;
														echo number_format($job_yetTo_allocate,2,'.','');
														$html.=number_format($job_yetTo_allocate,2);
													}
													$html.="</td><td>";
												?>
											</td>
											<td width="100" align="right" bgcolor="<? echo $discrepancy_td_color; ?>">
												<?
													if($z==1)
													{
														echo "<font color='$bgcolor' style='display:none'>".number_format($yarn_issued,2,'.','')."</font>\n";
														$d=1;
														foreach($yarn_desc_array as $yarn_desc)
														{
															if($d!=1)
															{
																echo "<hr/>";
																$html.="<hr/>";
																$html_short.="<hr/>";
																$html_medium.="<hr/>";
															}

															$yarn_iss_qnty=$yarn_iss_qnty_array[$yarn_desc];
															$yarn_desc_for_popup=explode("__",$yarn_desc_array_for_popup[$d]);

															?>
															<a href="##" onClick="openmypage('<? echo $poId_id; ?>','yarn_issue','<? echo $yarn_desc_for_popup[0]; ?>','<? echo $yarn_desc_for_popup[1]; ?>','<? echo $yarn_desc_for_popup[2]; ?>','<? echo $yarn_desc_for_popup[3]; ?>','<? echo $yarn_desc_for_popup[4]; ?>','<? echo $yarn_desc_for_popup[5]; ?>')"><? echo number_format($yarn_iss_qnty,2,'.','');?></a>
															<?
															$html.=number_format($yarn_iss_qnty,2);
															$html_short.=number_format($yarn_iss_qnty,2);
															$html_medium.=number_format($yarn_iss_qnty,2);
															$d++;
														}

														if($d!=1)
														{
															echo "<hr/>";
															$html.="<hr/>";
															$html_short.="<hr/>";
															$html_medium.="<hr/>";
														}

														$yarn_desc=join(",",$yarn_desc_array);

														$iss_qnty_not_req=$yarn_iss_qnty_array['not_req'];

														$html.=number_format($iss_qnty_not_req,2);
														$html_short.=number_format($iss_qnty_not_req,2);
														$html_medium.=number_format($yarn_issued,2);
														?>
														<a href="##" onClick="openmypage('<? echo $poId_id; ?>','yarn_issue_not','<? echo $yarn_desc; ?>','','','','','')"><? echo number_format($iss_qnty_not_req,2);?></a>
													<?
													}
													?>
											</td>
											<? $html.="</td><td>"; ?>
											<td width="100" align="right">
												<?
													if($z==1)
													{
													?>
														<a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $poId_id; ?>','yarn_trans','')"><? echo number_format($net_trans_yarn,2,'.','');  ?></a>
													<?
														$html.=number_format($net_trans_yarn,2);
														$tot_net_trans_yarn_qnty+=$net_trans_yarn;
													}
												?>
											</td>
											<? $html.="</td><td>"; $html_medium.="</td><td>"; ?>
											<td width="100" align="right">
											<?
												if($z==1)
												{
													echo number_format($balance,2,'.','');
													$html.=number_format($balance,2);
													$html_medium.=number_format($balance,2);
												}
											?>
											</td>
											<? $html.="</td><td bgcolor='$bgcolor_grey_td'>"; $html_short.="</td><td bgcolor='$bgcolor_grey_td'>"; $html_medium.="</td><td bgcolor='$bgcolor_grey_td'>"; ?>
											<td width="100" align="right" bgcolor="<? echo $bgcolor_grey_td; ?>">
											<?
												if($z==1)
												{
													echo number_format($required_qnty,2,'.','');
													$html.=number_format($required_qnty,2);
													$html_short.=number_format($required_qnty,2);
													$html_medium.=number_format($required_qnty,2);
												}
											?>
											</td>
											<? $html.="</td><td bgcolor='$discrepancy_td_color'>"; $html_short.="</td><td>"; $html_medium.="</td><td>"; ?>
											<td width="100" align="right" bgcolor="<? echo $discrepancy_td_color; ?>">
												<?
													if($z==1)
													{
													?>
														<a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $poId_id; ?>','grey_receive','')"><? echo number_format($grey_recv_qnty,2,'.',''); ?></a>
													<?
														$html.=number_format($grey_recv_qnty,2);
														$html_short.=number_format($grey_recv_qnty,2);
														$html_medium.=number_format($grey_recv_qnty,2);
													}
												?>
											</td>
											<? $html.="</td><td bgcolor='$discrepancy_td_color'>"; $html_medium.="</td><td bgcolor='$discrepancy_td_color'>"; ?>
											<td width="100" align="right">
												<?
													if($z==1)
													{
														echo number_format($grey_prod_balance,2,'.','');
														$html.=number_format($grey_prod_balance,2);
														$html_medium.=number_format($grey_prod_balance,2);
													}
												?>
											</td>
											<? $html.="</td><td bgcolor='$discrepancy_td_color'>"; ?>
											<td width="100" align="right">
											<?
												if($z==1)
												{
													?>
														<a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $poId_id; ?>','grey_delivery_to_store','')"><? echo number_format($grey_del_store,2,'.',''); ?></a>
													<?
													$html.=number_format($grey_del_store,2);
												}
											?>
											</td>
											<? $html.="</td><td bgcolor='$discrepancy_td_color'>"; ?>
											<td width="100" align="right">
											<?
												$greyKnitFloor=0;
												if($z==1)
												{
													$greyKnitFloor=$grey_recv_qnty-$grey_del_store;
													echo number_format($greyKnitFloor,2,'.','');
													$html.=number_format($greyKnitFloor,2);
													$tot_greyKnitFloor+=$greyKnitFloor;

												}
											?>
											</td>
											<? $html.="</td><td>"; ?>
											<td width="100" align="right">
												<?
													if($z==1)
													{
													?>
														<a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $poId_id.'_'.'9'; ?>','grey_purchase','')"><? echo number_format($grey_production_qnty,2,'.',''); ?></a>
													<?
														$html.=number_format($grey_production_qnty,2);
													}
												?>
											</td>
											<? $html.="</td><td bgcolor='$bgcolor_grey_td'>"; ?>
											<td width="100" align="right">
												<?
													if($z==1)
													{
													?>
														<a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $poId_id.'_'.'0'; ?>','grey_purchase','')"><? echo number_format($grey_purchase_qnty,2,'.',''); ?></a>
													<?
														$html.=number_format($grey_purchase_qnty,2);
													}
												?>
											</td>
											<? $html.="</td><td>"; ?>
											<td width="100" align="right">
												<?
													if($z==1)
													{
													?>
														<? echo number_format($grey_net_return,2,'.',''); ?>
													<?
														$html.=number_format($grey_net_return,2);
														$tot_net_gray_return+=$grey_net_return;
													}
												?>
											</td>
											<? $html.="</td><td>"; ?>
											<td width="100" align="right">
												<?
													if($z==1)
													{
													?>
														<a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $poId_id; ?>','knit_trans','')"><? echo number_format($net_trans_knit,2,'.','');  ?></a>
													<?
														$html.=number_format($net_trans_knit,2);
														$tot_net_trans_knit_qnty+=$net_trans_knit;
													}
												?>
											</td>
											<? $html.="</td><td>"; ?>
											<td width="100" align="right" title="Total Receive=( Grey Rcvd (Prod.) + Grey Rcvd (Purchase) + Net Transfer)">
											<?
												//
												//$grey_available=$grey_production_qnty+$grey_purchase_qnty+$net_trans_knit;
												if($z==1)
												{
													echo number_format($grey_available,2,'.','');
													$html.=number_format($grey_available,2);
												}
											?>
											</td>
											<? $html.="</td><td>"; ?>
											<td width="100" align="right" title="Required (As per Booking) - Grey Actual Recv.">
												<?
													if($z==1)
													{
														echo number_format($grey_balance,2,'.','');
														$html.=number_format($grey_balance,2);
													}
												?>
											</td>
											<? $html.="</td><td>"; ?>

											<td width="100" align="right">
												<?
													if($z==1)
													{
													?>
														<a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $poId_id; ?>','grey_issue','')"><?
														$grey_fabric_issue=$grey_fabric_issue-$grey_net_return;
														 echo number_format($grey_fabric_issue,2,'.',''); ?></a>
													<?
														$html.=number_format($grey_fabric_issue,2);
													}
												?>
											</td>
											<? $html.="</td><td>"; ?>
											<td width="100" align="right" title="Grey Actual Recv.-Net Grey Issue">
												<?
													if($z==1)
													{
														echo number_format($grey_in_hand,2,'.','');
														$html.=number_format($grey_in_hand,2);
														$tot_grey_in_hand+=$grey_in_hand;
													};
												?>
											</td>
											<? $html.="</td><td>"; ?>
											<td width="100" align="right">
												<?
													if($z==1)
													{
														?>
                                                        <a href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $poId_id; ?>','receive_by_batch','')"><? echo number_format($receive_by_batch_qnt,2,'.',''); ?></a>
                                                        <?
														$html.=number_format($receive_by_batch_qnt,2);
													}
												?>
											</td>
											<? $html.="</td><td bgcolor='#FF9BFF'>"; $html_short.="</td><td bgcolor='#FF9BFF'>"; $html_medium.="</td><td bgcolor='#FF9BFF'>"; ?>
											<td width="100" align="center" bgcolor="#FF9BFF">
												<p>
													<?
														if($key==0)
														{
															echo "-";
															$html.="-"; $html_short.="-"; $html_medium.="-";
														}
														else
														{
															echo $color_array[$key];
															$html.=$color_array[$key]; $html_short.=$color_array[$key]; $html_medium.=$color_array[$key];
														}

													?>
												</p>
											</td>
											<? $html.="</td><td>";

											$batch_color_qnty=0; $fab_recv_qnty=0; $fab_production_qnty=0; $fab_purchase_qnty=0; $issue_to_cut_qnty=0; $dye_qnty=0; $fin_delivery_qty=0; $fab_net_return=0; $fab_rec_return=0; $fab_issue_return=0;
											$job_po_id_batch=array_unique(explode(",",$poId_id));
											foreach($job_po_id_batch as $val)
											{
												$batch_color_qnty+=$batch_qnty_arr[$val][$key];
												//$tot_batch_qnty+=$batch_color_qnty;
												$fab_rec_return_production=$finish_recv_rtn_qnty_arr[$val][$key]['production'];
												$fab_rec_return_purchase=$finish_recv_rtn_qnty_arr[$val][$key]['purchase'];

												$fab_recv_qnty+=$finish_receive_qnty_arr[$val][$key];
												$fab_production_qnty+=$finish_purchase_qnty_arr[$val][$key]['production']-$fab_rec_return_production;
												$fab_purchase_qnty+=$finish_purchase_qnty_arr[$val][$key]['purchase']-$fab_rec_return_purchase;


												$fab_rec_return=$fab_rec_return_production+$fab_rec_return_purchase;
												$fab_issue_return=$finish_issue_rtn_qnty_arr[$val][$key];

												$fab_net_return+=$fab_issue_return-$fab_rec_return;
												//$dye_qnty+=$dye_qnty_arr[$val][$key];
												$issue_to_cut_qnty+=$finish_issue_qnty_arr[$val][$key]-$fab_issue_return;
												$dye_qnty+=$dye_qnty_arr[$val][$key];
												$fin_delivery_qty+=$finDeliveryArray[$val][$key];
											}
											?>
											<td width="100" align="right">
												<?
													$grey_req_color_qty=0;
													$grey_req_color_qty=$grey_req_color_arr[$key];
													$html.=number_format($grey_req_color_qty,2);
													$tot_grey_req_color_qty+=$grey_req_color_qty;

												echo number_format($grey_req_color_qty,2,'.',''); ?>
											</td>
										   <? $html.="</td><td>"; ?>

											<td width="100" align="right">
												<a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $poId_id.'_'.$key; ?>','batch_qnty','')"><? echo number_format($batch_color_qnty,2,'.','');
												$html.=number_format($batch_color_qnty,2);
												$batch_qnty_array[$buyer_name]+=$batch_color_qnty;
												$tot_batch_qnty+=$batch_color_qnty;
												$tot_batch_qnty_excel+=$batch_color_qnty;
											?></a>
											</td>
											<? $html.="</td><td>"; $html_short.="</td><td bgcolor='#FF9BFF'>"; $html_medium.="</td><td bgcolor='#FF9BFF'>"; ?>
											<td width="100" align="right">
												<a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $poId_id; ?>','dye_qnty','<? echo $key; ?>')"><? echo number_format($dye_qnty,2,'.',''); ?></a>
												<?
													$html.=number_format($dye_qnty,2);
													$html_short.=number_format($dye_qnty,2);
													$html_medium.=number_format($dye_qnty,2);

													$dye_qnty_array[$buyer_name]+=$dye_qnty;
													$tot_dye_qnty+=$dye_qnty;
												?>
											</td>
											<? $html.="</td><td>"; $html_short.="</td><td>"; $html_medium.="</td><td>"; ?>
											<td width="100" align="right" title="Grey Req. Color - Dye Qty">
												<?
													$grey_balance_color_qty=0;
													$grey_balance_color_qty=$grey_req_color_qty-$dye_qnty;
													$html.=number_format($grey_balance_color_qty,2);
													$tot_grey_balance_color_qty+=$grey_balance_color_qty;

												echo number_format($grey_balance_color_qty,2,'.',''); ?>
											</td>
										   <? $html.="</td><td>"; ?>
											<td width="100" align="right">
												<?
													$dyeing_balance=$batch_color_qnty-$dye_qnty;
													echo number_format($dyeing_balance,2);
													$html.=number_format($dyeing_balance,2);
												//	$html_short.=number_format($dyeing_balance,2);
													$html_medium.=number_format($dyeing_balance,2);

													//$dye_qnty_array[$buyer_name]+=$dyeing_balance;
													//$tot_dye_qnty+=$dyeing_balance;
													$tot_dye_qnty_balance+=$dyeing_balance;
												?>
											</td>
											<? $html.="</td><td>"; ?>
											<td width="100" align="right">
												<?
													echo number_format($value,2,'.','');
													$html.=number_format($value,2);

													$fin_fab_Requi_array[$buyer_name]+=$value;
													$tot_color_wise_req+=$value;
												?>
											</td>
											<? $html.="</td><td>"; $html_medium.="</td><td>"; ?>
											<td width="100" align="right">
												<a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $poId_id; ?>','fabric_receive','<? echo $key; ?>')"><? echo number_format($fab_recv_qnty,2,'.',''); ?></a>
												<?
													$html.=number_format($fab_recv_qnty,2);
													$html_short.=number_format($fab_recv_qnty,2);
													$html_medium.=number_format($fab_recv_qnty,2);

													$finFabProductionArr[$buyer_name]+=$fab_recv_qnty;
													$tot_fabric_recv+=$fab_recv_qnty;
													$tot_fabric_recv_excel+=$fab_recv_qnty;
												?>
											</td>
											<? $html.="</td><td>"; $html_short.="</td>"; $html_medium.="</td><td>"; ?>
											<td width="100" align="right" >
												<?
													$finish_balance=$value-$fab_recv_qnty;
													echo number_format($finish_balance,2,'.','');
													$html.=number_format($finish_balance,2);
													//$html_short.=number_format($finish_balance,2);
													$html_medium.=number_format($finish_balance,2);

													//$fin_fab_recei_array[$buyer_name]+=$finish_balance;
													$tot_fabric_recv_balance+=$finish_balance;
												?>
											</td>
											<? $html.="</td><td>"; ?>
											<td width="100" align="right">
														<a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $poId_id.'_'.$key; ?>','finish_delivery_to_store','')"><? echo number_format($fin_delivery_qty,2,'.',''); ?></a>
													<?
													$html.=number_format($fin_delivery_qty,2);

													//$fin_fab_recei_array[$buyer_name]+=$fin_delivery_qty;
													$tot_fin_delivery_qty+=$fin_delivery_qty;
												?>
											</td>
											<? $html.="</td><td>"; ?>
											<td width="100" align="right">
												<?
													$finProdFloor=$fab_recv_qnty-$fin_delivery_qty;
													echo number_format($finProdFloor,2,'.','');
													$html.=number_format($finProdFloor,2);
													//$fin_fab_recei_array[$buyer_name]+=$finProdFloor;
													$tot_finProdFloor+=$finProdFloor;
												?>
											</td>
											<? $html.="</td><td>"; ?>
											<td width="100" align="right">
												<a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $poId_id.'_'.'9'; ?>','fabric_purchase','<? echo $key; ?>')"><? echo number_format($fab_production_qnty,2,'.',''); ?></a>
												<?
													$html.=number_format($fab_production_qnty,2);

													//$fin_fab_recei_array[$buyer_name]+=$fab_purchase_qnty;
													$tot_fabric_production+=$fab_production_qnty;
												?>
											</td>
											<? $html.="</td><td>"; $html_short.="<td>"; //$html_medium.="</td><td>"; ?>
											<td width="100" align="right">
												<a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $poId_id.'_'.'0'; ?>','fabric_purchase','<? echo $key; ?>')"><? echo number_format($fab_purchase_qnty,2,'.',''); ?></a>
												<?
													$html.=number_format($fab_purchase_qnty,2);
													//$html_short.=number_format($fab_purchase_qnty,2);
													//$html_medium.=number_format($fab_purchase_qnty,2);

													//$fin_fab_recei_array[$buyer_name]+=$fab_purchase_qnty;
													$tot_fabric_purchase+=$fab_purchase_qnty;
												?>
											</td>
											<? $html.="</td><td>"; ?>
											<td width="100" align="right" title="<? echo "fab_issue_return:".$fab_issue_return.", fab_rec_return_production:".$fab_rec_return_production.",fab_rec_return_purchase:".$fab_rec_return_purchase?>">
												<? echo number_format($fab_net_return,2,'.',''); ?>
												<?
													$html.=number_format($fab_net_return,2);
													//$html_short.=number_format($fab_purchase_qnty,2);

													//$fin_fab_recei_array[$buyer_name]+=$fab_purchase_qnty;
													$tot_fab_net_return+=$fab_net_return;
												?>
											</td>
											<? $html.="</td><td>"; ?>
											<td width="100" align="right">
												<?
													$net_trans_finish=0;
													$job_po_id=explode(",",$poId_id);
													foreach($job_po_id as $val)
													{
														$net_trans_finish+=$trans_qnty_fin_arr[$val][$key]['trans'];
													}
												?>
													<a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $poId_id; ?>','finish_trans','<? echo $key; ?>')"><? echo number_format($net_trans_finish,2,'.','');  ?></a>
												<?
													$html.=number_format($net_trans_finish,2);
													//$fin_fab_recei_array[$buyer_name]+=$net_trans_finish;
													$tot_net_trans_finish_qnty+=$net_trans_finish;
												?>
											</td>
											<? $html.="</td><td>"; ?>
											<td width="100" align="right" title="Total Receive=(Received Prod. + Received Purchase + Net Transfer)">
												<?
													$fabric_available=$fab_production_qnty+$fab_purchase_qnty+$net_trans_finish;
													$fin_fab_recei_array[$buyer_name]+=$fabric_available;
													echo number_format($fabric_available,2,'.','');
													$html.=number_format($fabric_available,2);
													$tot_fabric_available+=$fabric_available;
												?>
											</td>
											<? $html.="</td><td>"; ?>
											<td width="100" align="right">
												<?
													$fabric_receive_bal=$value-$fabric_available;
													echo number_format($fabric_receive_bal,2,'.','');
													$fin_balance_array[$buyer_name]+=$fabric_receive_bal;
													$html.=number_format($fabric_receive_bal,2);
													$tot_fabric_rec_bal+=$fabric_receive_bal;
												?>
											</td>
											<? $html.="</td><td>";  $html_medium.="</td><td>"; //$html_short.="</td><td>";?>
											<td width="100" align="right">
												<a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $poId_id; ?>','issue_to_cut','<? echo $key; ?>')"><? echo number_format($issue_to_cut_qnty,2,'.',''); ?></a>
												<?
													$html.=number_format($issue_to_cut_qnty,2);
													$html_short.=number_format($issue_to_cut_qnty,2);
													$html_medium.=number_format($issue_to_cut_qnty,2);

													$issue_toCut_array[$buyer_name]+=$issue_to_cut_qnty;
													$tot_issue_to_cut_qnty+=$issue_to_cut_qnty;
												?>
											</td>
											<? $html.="</td><td>"; ?>
											<td width="100" align="right"><? $yet_to_cut_qty=$value-$issue_to_cut_qnty; echo number_format($yet_to_cut_qty,2,'.',''); $tot_yet_to_cut+=$yet_to_cut_qty; $html.=number_format($yet_to_cut_qty,2); ?></a>
											</td>
											<? $html.="</td><td>"; ?>
											<td width="100" align="right" title="Rec-Issue">
												<?
													$fabric_left_over=$fabric_available-$issue_to_cut_qnty;
													echo number_format($fabric_left_over,2,'.','');
													$html.=number_format($fabric_left_over,2);
													$tot_fabric_left_over+=$fabric_left_over;
													$tot_fabric_left_over_excel+=$fabric_left_over;
												?>
											</td>
											<? $html.="</td><td>"; ?>
											<td width="100">&nbsp; <? $html.="&nbsp;"; ?></td>
											<td>
												<p>
													<? $fabric_desc=explode(",",$fabric_desc_details[$job_no]); echo $display_font_color.join(",<br>",array_unique($fabric_desc)).$font_end; ?>
												</p>
											</td>
										</tr>
									 <?
										if($z==1) $html.="</td><td>".join(",<br>",array_unique($fabric_desc))."</td></tr>"; else $html.="</td><td>&nbsp;</td></tr>";
										$html_short.="</td></tr>"; $html_medium.="</td></tr>";
									$z++;
									$k++;
									}
								}
								else
								{
									$html.="<tr bgcolor='".$bgcolor."'>
													<td align='left'>".$i."</td>
													<td align='left'>".$main_booking_excel."</td>
													<td align='left'>".$sample_booking_excel."</td>
													<td align='left'>".$all_book_prefix_no."</td>
													<td align='center'>".$job_no."</td>
													<td align='center'><img  src='../../".$imge_arr[$job_no]."' height='25' width='30' /></td>
													<td align='left'>".$po_number."</td>
													<td align='left'></td>
													<td>".$buyer_short_name_library[$buyer_name]."</td>
													<td align='left'>".$style_ref_no."</td>
													<td align='left'>".$file_no."</td>
													<td align='left'>".$grouping."</td>
													<td align='left'>".$gmts_item."</td>
													<td align='right'>".$order_qnty_in_pcs."</td>
													<td align='left'>View</td>
													<td align='center'>".$contry_ship_date."</td>
													<td align='right'>".$country_ship_qty."</td>
													<td align='right'>".$grey_cons."</td>
													<td align='right'>".$fin_cons."</td>";

									$html_short.="<tr bgcolor='".$bgcolor."'>
												<td align='left'>".$i."</td>
												<td align='left'>".$main_booking_excel."</td>
												<td align='left'>".$sample_booking_excel."</td>
												<td align='left'>".$all_book_prefix_no."</td>
												<td align='left'>".$po_number."</td>
												<td>".$buyer_short_name_library[$buyer_name]."</td>
												<td>".$file_no."</td>
												<td>".$grouping."</td>
												<td align='right'>".$order_qnty_in_pcs."</td>
												<td align='left'>View</td>";
									$html_medium.="<tr bgcolor='".$bgcolor."'>
												<td align='left'>".$i."</td>
												<td>".$buyer_short_name_library[$buyer_name]."</td>
												<td align='left'>".$main_booking_excel."</td>
												<td align='left'>".$sample_booking_excel."</td>
												<td align='left'>".$all_book_prefix_no."</td>
												<td align='left'>".$po_number."</td>
												<td align='left'>".$grouping."</td>
												<td align='left'>".$file_no."</td>
												<td align='left'>".$style_ref_no."</td>

												<td align='right'>".$order_qnty_in_pcs."</td>
												<td align='left'>View</td>
												<td align='left'>View</td>";

									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
										<td width="40"><? echo $i; ?></td>
										<td width="125"><? echo $main_booking; ?></td>
										<td width="125"><? echo $sample_booking; ?></td>
										<td width="75" style="word-break:break-all"><? echo $all_book_prefix_no; ?></td>
										<td width="100" align="center"><? echo $job_no; ?></td>
                                        <td width="40" onClick="openmypage_image('requires/fabric_receive_status_report2_controller.php?action=show_image&job_no=<?=$job_no; ?>','Image View')"><img  src="../../<?=$imge_arr[$job_no]; ?>" height='25' width='30' /></td>
										<td width="120"><p><? echo $po_number; ?></p></td>
										<td width="90" align="center"><? //echo $order_status[$row[csf('is_confirmed')]]; ?></td>
										<td width="80"><p><? echo $buyer_short_name_library[$buyer_name]; ?></p></td>
										<td width="130"><p><? echo $style_ref_no; ?></p></td>
										<td width="100"><p><? echo $file_no; ?></p></td>
										<td width="100"><p><? echo $grouping; ?></p></td>
										<td width="140"><p><? echo $gmts_item; ?></p></td>
										<td width="100" align="right"><? echo number_format($order_qnty_in_pcs,0,'.',''); ?></td>
										<td width="80" align="center"><a href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $poId_id; ?>','Shipment_date','')"><? echo "View"; ?></a></td>
										<td width="80"><p><? echo $contry_ship_date; ?></p></td>
										<td width="100" align="right"><a href="##" onClick="country_order_dtls('<? echo $poId_id; ?>','<? echo $start_date; ?>','<? echo $end_date; ?>','<? echo $buyer_name; ?>','<? echo $job_no; ?>','country_order_dtls_popup')"><? echo number_format($country_ship_qty,0,'.',''); ?></a></td>
										<td width="100" align="right"><? echo number_format($grey_cons,5,'.',''); ?></td>
										<td width="100" align="right"><? echo number_format($fin_cons,5,'.',''); ?></td>
										<td width="70">
											<?
												 $html.="<td>"; $d=1;
												 foreach($yarn_data_array['count'] as $yarn_count_value)
												 {
													if($d!=1)
													{
														echo "<hr/>";
														$html.="<hr/>";
													}

													echo $yarn_count_value;
													$html.=$yarn_count_value;

												 $d++;
												 }

												 $html.="</td><td>";
											?>
										</td>
										<td width="110">
											<div style="word-wrap:break-word; width:110px">
												<?
													 $d=1;
													 foreach($yarn_data_array['comp'] as $yarn_composition_value)
													 {
														if($d!=1)
														{
															echo "<hr/>";
															$html.="<hr/>";
														}

														echo $yarn_composition_value;
														$html.=$yarn_composition_value;

													 $d++;
													 }

													 $html.="</td><td>";
												?>
											</div>
										</td>
										<td width="80">
											<p>
												<?
													 $d=1;
													 foreach($yarn_data_array['type'] as $yarn_type_value)
													 {
														if($d!=1)
														{
															echo "<hr/>";
															$html.="<hr/>";
														}

														echo $yarn_type_value;
														$html.=$yarn_type_value;

													 $d++;
													 }

													 $html.="</td><td>";
												?>
											</p>
										</td>
										<td width="100" align="right">
											<?
												echo "<font color='$bgcolor' style='display:none'>".number_format(array_sum($mkt_required_array),2,'.','')."</font>\n";
												$d=1;
												foreach($mkt_required_array as $mkt_required_value)
												{
													if($d!=1)
													{
														echo "<hr/>";
														$html.="<hr/>";
													}

													$yarn_desc_for_popup_req=explode("__",$yarn_desc_array_for_popup[$d]);

													?>
													<a href="##" onClick="openmypage('<? echo $poId_id; ?>','yarn_req','<? echo $yarn_desc_for_popup_req[0]; ?>','<? echo $yarn_desc_for_popup_req[1]; ?>','<? echo $yarn_desc_for_popup_req[2]; ?>','<? echo $yarn_desc_for_popup_req[3]; ?>','<? echo $yarn_desc_for_popup_req[4]; ?>','<? echo $yarn_desc_for_popup_req[5]; ?>')"><? echo number_format($mkt_required_value,2,'.','');?></a>
												<?
												$html.=number_format($mkt_required_value,2);
												$d++;
												}

												$html.="</td><td bgcolor='$discrepancy_td_color'>"; $html_short.="<td>"; $html_medium.="<td>";
											?>
										</td>
										<td width="100" align="right">
											<?
												if($z==1)
												{
													$d=1;
													foreach($yarn_desc_array as $yarn_desc)
													{
														if($d!=1)
														{
															echo "<hr/>";
															$html.="<hr/>";
														}
														$yarn_allocation_value=$yarn_allocation_arr[$yarn_desc];
														$yarn_desc_for_popup_req=explode("__",$yarn_desc_array_for_popup[$d]);
														?>
														<a href="##" onClick="openmypage('<? echo $poId_id; ?>','yarn_allocation_pop','<? echo $yarn_desc_for_popup_req[0]; ?>','<? echo $yarn_desc_for_popup_req[1]; ?>','<? echo $yarn_desc_for_popup_req[2]; ?>','<? echo $yarn_desc_for_popup_req[3]; ?>','<? echo $yarn_desc_for_popup_req[4]; ?>','<? echo $yarn_desc_for_popup_req[5]; ?>')"><? echo number_format($yarn_allocation_value,2,'.','');?></a>
													<?
													$html.=number_format($yarn_allocation_value,2);
													$d++;
													}
												}
												$html.="</td><td bgcolor='$discrepancy_td_color'>";
											?>
										</td>
										<td width="100" align="right">
											<?
												if($z==1)
												{
													$d=1;
													foreach($yetTo_allocate_arr as $yetTo_allocate_value)
													{
														if($d!=1)
														{
															echo "<hr/>";
															$html.="<hr/>";
														}
														$yarn_desc_for_popup_req=explode("__",$yarn_desc_array_for_popup[$d]);
														echo number_format($yetTo_allocate_value,2,'.','');
														$html.=number_format($yetTo_allocate_value,2);
														$d++;
													}
												}
												$html.="</td><td bgcolor='$discrepancy_td_color'>";
											?>
										</td>
										<td width="100" align="right" bgcolor="<? echo $discrepancy_td_color; ?>">
											<?
												echo "<font color='$bgcolor' style='display:none'>".number_format($yarn_issued,2,'.','')."</font>\n";
												$d=1;
												foreach($yarn_desc_array as $yarn_desc)
												{
													if($d!=1)
													{
														echo "<hr/>";
														$html.="<hr/>";
														$html_short.="<hr/>";$html_medium.="<hr/>";
													}

													$yarn_iss_qnty=$yarn_iss_qnty_array[$yarn_desc];
													$yarn_desc_for_popup=explode("__",$yarn_desc_array_for_popup[$d]);

													?>
													<a href="##" onClick="openmypage('<? echo $poId_id; ?>','yarn_issue','<? echo $yarn_desc_for_popup[0]; ?>','<? echo $yarn_desc_for_popup[1]; ?>','<? echo $yarn_desc_for_popup[2]; ?>','<? echo $yarn_desc_for_popup[3]; ?>','<? echo $yarn_desc_for_popup[4]; ?>','<? echo $yarn_desc_for_popup[5]; ?>')"><? echo number_format($yarn_iss_qnty,2,'.','');?></a>
													<?
													$html.=number_format($yarn_iss_qnty,2);
													$html_short.=number_format($yarn_iss_qnty,2);
													$html_medium.=number_format($yarn_iss_qnty,2);
													$d++;
												}

												if($d!=1)
												{
													echo "<hr/>";
													$html.="<hr/>";
													$html_short.="<hr/>";
													$html_medium.="<hr/>";
												}

												$yarn_desc=join(",",$yarn_desc_array);

												$iss_qnty_not_req=$yarn_iss_qnty_array['not_req'];

												$html.=number_format($iss_qnty_not_req,2);
												$html_short.=number_format($iss_qnty_not_req,2);
												$html_medium.=number_format($yarn_issued,2);
												?>
												<a href="##" onClick="openmypage('<? echo $poId_id; ?>','yarn_issue_not','<? echo $yarn_desc; ?>','','','','','')"><? echo number_format($iss_qnty_not_req,2);?></a>

										</td>
										<? $html.="</td><td>"; ?>
										<td width="100" align="right">
											<a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $poId_id; ?>','yarn_trans','')"><? echo number_format($net_trans_yarn,2,'.','');  ?></a>
											<?
												$html.=number_format($net_trans_yarn,2);
												$tot_net_trans_yarn_qnty+=$net_trans_yarn;
											?>
										</td>
										<? $html.="</td><td>";  $html_medium.="</td><td>"; ?>
										<td width="100" align="right">
											<?
												echo number_format($balance,2,'.','');
												$html.=number_format($balance,2);
												$html_medium.=number_format($balance,2);
											?>
										</td>
										<? $html.="</td><td>"; ?>
										<td width="100" align="right" bgcolor="<? echo $bgcolor_grey_td; ?>"> <? echo number_format($required_qnty,2,'.',''); $html.=number_format($required_qnty,2); ?></td>
										<? $html.="</td><td>"; ?>
										<td width="100" align="right" bgcolor="<? echo $discrepancy_td_color; ?>"><a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $poId_id; ?>','grey_receive','')"><? echo number_format($grey_recv_qnty,2,'.',''); $html.=number_format($grey_recv_qnty,2); ?></a></td>
										<? $html.="</td><td>"; ?>
										<td width="100" align="right"><? echo number_format($grey_balance,2,'.',''); $html.=number_format($grey_balance,2); ?></td>
										<? $html.="</td><td>"; ?>
										<td width="100" align="right"><a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $poId_id; ?>','grey_purchase','')"><? echo number_format($grey_del_store,2,'.',''); $html.=number_format($grey_del_store,2); ?></a></td>
										<? $html.="</td><td>"; ?>
										<td width="100">&nbsp; <? $html.="&nbsp;"; ?></td>
										<? $html.="</td><td>"; ?>
										<td width="100" align="right"><a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $poId_id; ?>','grey_purchase','')"><? echo number_format($grey_production_qnty,2,'.',''); $html.=number_format($grey_production_qnty,2); ?></a></td>
										<? $html.="</td><td>"; ?>
										<td width="100" align="right"><a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $poId_id; ?>','grey_purchase','')"><? echo number_format($grey_purchase_qnty,2,'.',''); $html.=number_format($grey_purchase_qnty,2); ?></a></td>
										<? $html.="</td><td>"; ?>
										<td width="100" align="right"><a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $poId_id; ?>','grey_net_return','')"><? echo number_format($grey_net_return,2,'.',''); $html.=number_format($grey_net_return,2); ?></a></td>
										<? $html.="</td><td>"; ?>
										<td width="100" align="right">
											 <a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $poId_id; ?>','knit_trans','')"><? echo number_format($net_trans_knit,2,'.','');  ?></a>
											<?
												$html.=number_format($net_trans_knit,2);
												$tot_net_trans_knit_qnty+=$net_trans_knit;
											?>
										</td>
										<? $html.="</td><td>"; ?>
										<td width="100" align="right">
											<? $grey_available=0; $grey_available=$grey_production_qnty+$grey_purchase_qnty+$net_trans_knit;
											echo number_format($grey_available,2,'.',''); $html.=number_format($grey_available,2); ?>
										</td>
										<? $html.="</td><td>"; ?>
										<td width="100" align="right">
											<?
												echo number_format($grey_balance,2,'.','');
												$html.=number_format($grey_balance,2);
											?>
										</td>
										<? $html.="</td><td>"; ?>
										<td width="100" align="right"><a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $poId_id; ?>','grey_issue','')"><?
										$grey_fabric_issue=$grey_fabric_issue-$grey_net_return;
										 echo number_format($grey_fabric_issue,2,'.',''); $html.=number_format($grey_fabric_issue,2); ?></a>
										</td>
										<? $html.="</td><td>"; ?>
                                        <td width="100" align="right"><? echo number_format($grey_in_hand,2,'.',''); $html.=number_format($grey_in_hand,2); ?></td>
										<? $html.="</td><td>"; ?>
										<td width="100" align="right"><? echo number_format($receive_by_batch_qnt,2,'.',''); $html.=number_format($receive_by_batch_qnt,2); ?>
										</td>
										<? $html.="</td><td>"; ?>
										<td width="100">&nbsp;<? $html.="&nbsp;"; ?></td>
										<? $html.="</td><td>"; ?>
										<td width="100" align="right"><a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $poId_id; ?>','batch_qnty','')"><? $html.="&nbsp;";//echo number_format($batch_color_qnty,2,'.',''); ?></a></td>
										<? $html.="</td><td>"; ?>
										<td width="100">&nbsp;</td>
										<td width="100">&nbsp;</td>
										<td width="100">&nbsp;</td>
										<td width="100">&nbsp;</td>
										<td width="100">&nbsp;</td>
										<td width="100">&nbsp;</td>
										<td width="100">&nbsp;</td>
										<td width="100">&nbsp;</td>
										<td width="100">&nbsp;</td>
										<td width="100">&nbsp;</td>
										<td width="100">&nbsp;</td>
										<td width="100">&nbsp;</td>
										<td width="100">&nbsp;</td>
										<td width="100">&nbsp;</td>
										<td width="100">&nbsp;</td>
										<td width="100">&nbsp;</td>
										<td>
											<p>
												<? $fabric_desc=explode(",",$fabric_desc_details[$job_no]); echo join(",<br>",array_unique($fabric_desc)); ?>
											</p>
										</td>
									</tr>
									<?	$tot_batch_qnty_excel+=$batch_qnty;
										$html.="</td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td>".join(",<br>",array_unique($fabric_desc))."</td>
										</tr>
										";

										$html_short.="</td><td bgcolor='$bgcolor_grey_td'>".number_format($required_qnty,2)."</td>
											<td>".number_format($grey_recv_qnty,2)."</td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
										</tr>";

										//number_format($grey_recv_qnty,2)
										$html_medium.="</td><td bgcolor='$bgcolor_grey_td'>".number_format($required_qnty,2)."</td>
										<td>".number_format($grey_recv_qnty,2)."</td>
										<td>".number_format($grey_prod_balance,2)."</td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										</tr>";
								$k++;
								}
								$i++;
							}
						}// end main query
					}
				}
				?>
                </table>
            </div>
            <?
				$html.="<tfoot>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th>Total</th>
							<th align='right'>".number_format($tot_order_qnty)."</th>
							<th></th>";

				if($type==1)
				{
					$html.="<th></th>
							<th></th>
							<th></th>";
				}

				$html.="<th>&nbsp;</th>
						<th align='right'>".number_format($tot_country_ship_qty)."</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th align='right'>".number_format($tot_mkt_required,2)."</th>
						<th align='right'>".number_format($tot_yarnAllocationQty,2)."</th>
						<th align='right'>".number_format($tot_yetTo_allocate,2)."</th>
						<th align='right'>".number_format($tot_yarn_issue_qnty,2)."</th>
						<th align='right'>".number_format($tot_net_trans_yarn_qnty,2)."</th>
						<th align='right'>".number_format($tot_balance,2)."</th>


						<th align='right'>".number_format($tot_fabric_req,2)."</th>
						<th align='right'>".number_format($tot_grey_recv_qnty,2)."</th>
						<th align='right'>".number_format($tot_grey_prod_balance,2)."</th>
						<th align='right'>".number_format($total_grey_del_store,2)."</th>
						<th align='right'>".number_format($tot_greyKnitFloor,2)."</th>

						<th align='right'>".number_format($tot_grey_production_qnty,2)."</th>
						<th align='right'>".number_format($tot_grey_purchase_qnty,2)."</th>
						<th align='right'>".number_format($tot_net_gray_return,2)."</th>
						<th align='right'>".number_format($tot_net_trans_knit_qnty,2)."</th>
						<th align='right'>".number_format($tot_grey_available,2)."</th>
						<th align='right'>".number_format($tot_grey_balance,2)."</th>
						<th align='right'>".number_format($tot_grey_issue-$tot_net_gray_return,2)."</th>
						<th align='right'>".number_format($tot_grey_in_hand,2)."</th>
						<th align='right'>".number_format($tot_receive_by_batch,2)."</th>
						<th>&nbsp;</th>

						<th align='right'>".number_format($tot_grey_req_color_qty,2)."</th>
						<th align='right'>".number_format($tot_batch_qnty,2)."</th>
						<th align='right'>".number_format($tot_dye_qnty,2)."</th>
						<th align='right'>".number_format($tot_grey_balance_color_qty,2)."</th>
						<th align='right'>".number_format($tot_dye_qnty_balance,2)."</th>
						<th align='right'>".number_format($tot_color_wise_req,2)."</th>
						<th align='right'>".number_format($tot_fabric_recv,2)."</th>
						<th align='right'>".number_format($tot_fabric_recv_balance,2)."</th>
						<th align='right'>".number_format($tot_fin_delivery_qty,2)."</th>
						<th align='right'>".number_format($tot_finProdFloor,2)."</th>

						<th align='right'>".number_format($tot_fabric_production,2)."</th>
						<th align='right'>".number_format($tot_fabric_purchase,2)."</th>
						<th align='right'>".number_format($tot_fab_net_return,2)."</th>
						<th align='right'>".number_format($tot_net_trans_finish_qnty,2)."</th>
						<th align='right'>".number_format($tot_fabric_available,2)."</th>
						<th align='right'>".number_format($tot_fabric_rec_bal,2)."</th>
						<th align='right'>".number_format($tot_issue_to_cut_qnty,2)."</th>
						<th align='right'>".number_format($tot_yet_to_cut,2)."</th>
						<th align='right'>".number_format($tot_fabric_left_over,2)."</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
					</tfoot>
				</table>
				<br />
				";

				$html_short.="<tfoot>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th>Total</th>

								<th></th>
								<th align='right'>".number_format($tot_order_qnty)."</th>
								<th></th>
								<th align='right'>".number_format($tot_yarn_issue_qnty,2)."</th>
								<th align='right'>".number_format($tot_fabric_req,2)."</th>
								<th align='right'>".number_format($tot_grey_recv_qnty,2)."</th>
								<th></th
								<th align='right'>".number_format($tot_dye_qnty,2)."</th>
								<th align='right'>".number_format($tot_fabric_recv,2)."</th>
								<th align='right'>".number_format($tot_issue_to_cut_qnty,2)."</th>
							</tfoot>
						</table>
						<br /> ";

				$html_medium.="<tfoot>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th>Total</th>
								<th align='right'>".number_format($tot_order_qnty)."</th>
								<th></th>
								<th></th>
								<th align='right'>".number_format($tot_yarn_issue_qnty,2)."</th>
								<th align='right'>".number_format($tot_balance,2)."</th>

								<th align='right'>".number_format($tot_fabric_req,2)."</th>
								<th align='right'>".number_format($tot_grey_recv_qnty,2)."</th>
								<th align='right'>".number_format($tot_grey_prod_balance,2)."</th>
								<th></th>
								<th align='right'>".number_format($tot_dye_qnty,2)."</th>
								<th align='right'>".number_format($tot_dye_qnty_balance,2)."</th>

								<th align='right'>".number_format($tot_fabric_recv,2)."</th>
								<th align='right'>".number_format($tot_fabric_recv_balance,2)."</th>
								<th align='right'>".number_format($tot_issue_to_cut_qnty,2)."</th>
							</tfoot>
						</table>
						<br /> ";

			?>
            <table width="<? echo $table_width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" style=" <?php echo $table_display; ?> ">
                <tfoot>
                    <tr>
                        <th width="40">&nbsp;</th>
                        <th width="125">&nbsp;</th>
                        <th width="125">&nbsp;</th>
                        <th width="75">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="40">&nbsp;</th>
                        <th width="120">&nbsp;</th>
                        <th width="90">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="130">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="140">Total</th>
                        <th width="100" id="total_tot_order_qnty"><? echo number_format($tot_order_qnty); ?></th>
                        <th width="80">&nbsp;</th>
                        <?
						if($type!=2)
						{
						?>
                            <th width="80">&nbsp;</th>
                            <th width="80">&nbsp;</th>
                            <th width="100">&nbsp;</th>
                        <?
						}
						?>
                        <th width="80">&nbsp;</th>
                        <th width="100" id="total_tot_country_ship_qty"><? echo number_format($tot_country_ship_qty); ?></th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="110">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="100" id="value_tot_mkt_required"><? echo number_format($tot_mkt_required,2); ?></th>
                        <th width="100" id="value_tot_yarnAllocationQty"><? echo number_format($tot_yarnAllocationQty,2); ?></th>
						<? if($type==1){?>
							<th width="100" id="value_tot_yarnAutoAllocationQty"><? //echo number_format($tot_yarnAutoAllocationQty,2); ?></th>
						<? }?>
                        <th width="100" id="value_tot_yetTo_allocate"><? echo number_format($tot_yetTo_allocate,2); ?></th>
                        <th width="100" id="value_tot_yarn_issue"><? echo number_format($tot_yarn_issue_qnty,2); ?></th>
                        <th width="100" id="value_tot_net_trans_yarn"><? echo number_format($tot_net_trans_yarn_qnty,2); ?></th>
                        <th width="100" id="value_tot_yarn_balance"><? echo number_format($tot_balance,2); ?></th>

                        <th width="100" id="value_tot_fabric_req"><? echo number_format($tot_fabric_req,2); ?></th>
                        <th width="100" id="value_tot_grey_recv_qnty"><? echo number_format($tot_grey_recv_qnty,2); ?></th>
                        <th width="100" id="value_tot_grey_prod_balance"><? echo number_format($tot_grey_prod_balance,2); ?></th>
                        <th width="100" id="value_tot_net_del_store"><? echo number_format($total_grey_del_store,2); ?></th>
                        <th width="100" id="value_tot_greyKnitFloor"><? echo number_format($tot_greyKnitFloor,2); ?></th>

                        <th width="100" id="value_tot_grey_production_qnty"><? echo number_format($tot_grey_production_qnty,2); ?></th>
                        <th width="100" id="value_tot_grey_purchase_qnty"><? echo number_format($tot_grey_purchase_qnty,2); ?></th>
                        <th width="100" id="value_tot_net_gray_return"><? echo number_format($tot_net_gray_return,2); ?></th>
                        <th width="100" id="value_tot_net_trans_knit_qnty"><? echo number_format($tot_net_trans_knit_qnty,2); ?></th>
                        <th width="100" id="value_tot_grey_available"><? echo number_format($tot_grey_available,2); ?></th>
                        <th width="100" id="value_tot_grey_balance"><? echo number_format($tot_grey_balance,2); ?></th>
                        <th width="100" id="value_tot_grey_issue"><? echo number_format($tot_grey_issue-$tot_net_gray_return,2); ?></th>

                        <th width="100" id="value_tot_grey_inhand"><? echo number_format($tot_grey_in_hand,2); ?></th>
                        <th width="100" id="value_tot_receive_by_batch"><? echo number_format($tot_receive_by_batch,2); ?></th>
                        <th width="100">&nbsp;</th>

                        <th width="100" id="value_tot_grey_req_color"><? echo number_format($tot_grey_req_color_qty,2); ?></th>
                        <th width="100" id="value_tot_batch"><? echo number_format($tot_batch_qnty,2); ?></th>
                        <th width="100" id="value_tot_dye_qnty"><? echo number_format($tot_dye_qnty,2); ?></th>
                        <th width="100" id="value_tot_grey_balance_color"><? echo number_format($tot_grey_balance_color_qty,2); ?></th>

                        <th width="100" id="value_tot_dye_qnty_balance"><? echo number_format($tot_dye_qnty_balance,2); ?></th>
                        <th width="100" id="value_tot_fini_req"><? echo number_format($tot_color_wise_req,2); ?></th>
                        <th width="100" id="value_tot_fini_receive"><? echo number_format($tot_fabric_recv,2); ?></th>
                        <th width="100" id="value_tot_fabric_recv_balance"><? echo number_format($tot_fabric_recv_balance,2); ?></th>
                        <th width="100" id="value_tot_fin_delivery_qty"><? echo number_format($tot_fin_delivery_qty,2); ?></th>
                        <th width="100" id="value_tot_finProdFloor"><? echo number_format($tot_finProdFloor,2); ?></th>

                        <th width="100" id="value_tot_fabric_production"><? echo number_format($tot_fabric_production,2); ?></th>
                        <th width="100" id="value_tot_fabric_purchase"><? echo number_format($tot_fabric_purchase,2); ?></th>
                        <th width="100" id="value_tot_fab_net_return"><? echo number_format($tot_fab_net_return,2); ?></th>
                        <th width="100" id="value_tot_trans_finish_qnty"><? echo number_format($tot_net_trans_finish_qnty,2); ?></th>
                        <th width="100" id="value_tot_fabric_available"><? echo number_format($tot_fabric_available,2); ?></th>
                        <th width="100" id="value_tot_fabric_rec_bal"><? echo number_format($tot_fabric_rec_bal,2); ?></th>
                        <th width="100" id="value_tot_issue_to_cut_qnty"><? echo number_format($tot_issue_to_cut_qnty,2); ?></th>

                        <th width="100" id="value_tot_yet_to_cut"><? echo number_format($tot_yet_to_cut,2); ?></th>
                        <th width="100" id="value_tot_fabric_left_over"><? echo number_format($tot_fabric_left_over,2); ?></th>
                        <th width="100">&nbsp;</th>
                        <th>&nbsp;</th>
                    </tr>
                </tfoot>
            </table>
            <br />
			<?
                $html.="<table align='center'>
                        <tr valign='top'>
                        <td>
                            <table border=1 rules='all' align='center'>
                            <thead>
                            <tr align='center'>
                                <th colspan='17'>Buyer Level Summary</th>
                            </tr>
                            <tr>
                                <th>SL</th>
                                <th>Buyer Name</th>";
                                 if($type==1){
                                	$html.="<th>Order Qty.</th>";
                                }
								$html.="<th>Country Order Qty.</th>
                                <th>Grey Req</th>
                                <th>Yarn Issue + Net Transfer</th>
                                <th>Yarn Balance</th>
                                <th>Grey Fabric Available</th>
                                <th>Grey Receive Balance</th>
								<th>Grey To Dye</th>
                                <th>Grey Issue Balance</th>
                                <th>Receive By Batch</th>
								<th>Batch Qnty</th>
								<th>Yet To Batch</th>
                                <th>Batch Balance</th>
                                <th>Total Dye Qnty</th>
                                <th>Dyeing Balance</th>
                                <th>Yet to Dyeing</th>
                                <th>Finish Fabric Req</th>
								<th>Finish Fabric Production</th>
                                <th>Finish Fabric Available</th>
                                <th>Finish Fabric Balance</th>
                                <th>Issue To Cut</th>
                            </tr>
                        </thead>
                        <tbody>

                ";
            ?>
            <table align="center">
                <tr>
                    <td valign="top">
                        <table width="600" class="rpt_table" border="1" rules="all">
                            <thead>
                                <tr>
                                    <th colspan="3">Summary</th>
                                </tr>
                                <tr bgcolor="<? echo "#FFFFFF"; ?>">
                                    <th width="300">Particulars</th>
                                    <th width="170">Total Qnty</th>
                                    <th>% On Required</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr bgcolor="<? echo "#FFFFFF"; ?>">
                                   <td>Total Yarn Required</td>
                                   <td align="right"><? echo number_format($tot_fabric_req,2); ?></td>
                                   <td>&nbsp;</td>
                                </tr>
                                <tr bgcolor="<? echo "#FFFFFF"; ?>">
                                   <td>Total Yarn Issued To Knitting</td>
                                   <td align="right"> <? echo number_format($tot_yarn_issue_qnty,2); ?></td>
                                   <td align="right"><? $per_yarn_issued=$tot_yarn_issue_qnty/$tot_fabric_req*100; echo number_format($per_yarn_issued,2)."%"; ?></td>
                                </tr>
                                <tr bgcolor="<? echo "#FFFFFF"; ?>">
                                   <td>Total Yarn Net Transfer</td>
                                   <td align="right"> <? echo number_format($tot_net_trans_yarn_qnty,2); ?></td>
                                   <td align="right"><? $per_yarn_transfer=$tot_net_trans_yarn_qnty/$tot_fabric_req*100; echo number_format($per_yarn_transfer,2)."%"; ?></td>
                                </tr>
                                <tr style="background-color:#CFF; font-weight:bold">
                                   <td>Total Yarn Balance</td>
                                   <td align="right"><? echo number_format($tot_fabric_req-($tot_yarn_issue_qnty+$tot_net_trans_yarn_qnty),2); ?></td>
                                   <td align="right"><? $per_yarn_balance=($per_yarn_issued+ $per_yarn_transfer); echo number_format($per_yarn_balance,2)."%"; ?></td>
                                </tr>
                                <tr bgcolor="<? echo "#FFFFFF"; ?>">
                                    <td>Total Grey Fabric Required</td>
                                   <td align="right"><? echo number_format($tot_fabric_req,2); ?></td>
                                   <td>&nbsp;</td>
                                </tr>
                                <tr bgcolor="<? echo "#FFFFFF"; ?>">
                                   <td>Total Grey Fabric Available</td>
                                   <td align="right"> <? echo number_format($tot_grey_available,2); ?></td>
                                   <td align="right"><? echo number_format(($tot_grey_available)/$tot_fabric_req*100,2)."%";?></td>
                                </tr>
                                <tr bgcolor="<? echo "#FFFFFF"; ?>">
                                   <td>Total Grey Fabric Issued To Dye</td>
                                   <td align="right"> <? echo number_format($tot_grey_issue,2); ?></td>
                                   <td align="right"><? echo number_format($tot_grey_issue/$tot_fabric_req*100,2)."%"; ?></td>
                                </tr>
                                <tr style="background-color:#CFF; font-weight:bold">
                                   <td>Total Grey Fabric Issue Balance</td>
                                   <td align="right"><? echo number_format($tot_fabric_req-$tot_grey_issue,2); ?></td>
                                   <td align="right"><? echo number_format(((($tot_fabric_req-$tot_grey_issue)/$tot_fabric_req)*100),2)."%"; ?></td>
                                </tr>
                                <tr bgcolor="#FFFFFF">
                                    <td>Total Receive By Batch</td>
                                    <td align="right"> <? echo number_format($tot_receive_by_batch,2);  ?></td>
                                    <td align="right">&nbsp;</td>
                                </tr>
                                <tr bgcolor="#FFFFFF">
                                    <td>Total Batch Qnty</td>
                                    <td align="right"> <? echo number_format($tot_batch_qnty,2); ?></td>
                                    <td align="right">&nbsp;</td>
                                </tr>
                                <tr style="background-color:#CFF; font-weight:bold">
                                    <td>Total Batch Balance To Receive By Batch</td>
                                    <td align="right"> <? $tot_batch_bal_to_rcv_batch=$tot_receive_by_batch-$tot_batch_qnty; echo number_format($tot_batch_bal_to_rcv_batch,2); ?></td>
                                    <td align="right">&nbsp;</td>
                                </tr>
                                <tr style="background-color:#CFF; font-weight:bold">
                                    <td>Total Batch Balance To Grey Required</td>
                                    <td align="right"> <? $tot_batch_balance=$tot_fabric_req-$tot_batch_qnty; echo number_format($tot_batch_balance,2); ?></td>
                                    <td align="right">&nbsp;</td>
                                </tr>
                                <tr bgcolor="#FFFFFF">
                                    <td>Total Dye Qnty</td>
                                    <td align="right"> <? echo number_format($tot_dye_qnty,2); ?></td>
                                    <td align="right"><? echo number_format($tot_dye_qnty/$tot_fabric_req*100,2)."%"; ?></td>
                                </tr>
                                <tr style="background-color:#CFF; font-weight:bold">
                                    <td>Total Dye Balance To Grey Required</td>
                                    <td align="right"> <? $tot_dye_balance=$tot_fabric_req-$tot_dye_qnty; echo number_format($tot_dye_balance,2); ?></td>
                                    <td align="right"><? echo number_format($tot_dye_balance/$tot_fabric_req*100,2)."%"; ?></td>
                                </tr>
                                <tr bgcolor="<? echo "#FFFFFF"; ?>">
                                   <td>Total Finish Fabric Required</td>
                                   <td align="right"><? echo number_format($tot_color_wise_req,2); ?></td>
                                   <td>&nbsp;</td>
                                </tr>
                                <tr bgcolor="<? echo "#FFFFFF"; ?>">
                                   <td>Total Finish Fabric Available</td>
                                   <td align="right"><? echo number_format($tot_fabric_available,2); ?></td>
                                   <td align="right"><? echo number_format(($tot_fabric_available)/$tot_color_wise_req*100,2)."%";?></td>
                                </tr>
                                <tr style="background-color:#CFF; font-weight:bold">
                                   <td>Total Finish Fabric Balance</td>
                                   <td align="right"><? echo number_format($tot_color_wise_req-($tot_fabric_available),2); ?></td>
                                   <td align="right"><? echo number_format(((($tot_color_wise_req-($tot_fabric_available))/$tot_color_wise_req)*100),2)."%"; ?></td>
                                </tr>
                                <tr bgcolor="<? echo "#FFFFFF"; ?>">
                                   <td>Total Finish Fabric Issued To Cut</td>
                                   <td align="right"><? echo number_format($tot_issue_to_cut_qnty,2); ?></td>
                                   <td align="right"><? echo number_format($tot_issue_to_cut_qnty/$tot_color_wise_req*100,2)."%"; ?></td>
                                </tr>
                            </tbody>
                       </table>
                    </td>
                    <td width="90"></td>
                    <td valign="top">
                        <div id="data_panel1" align="center" style="width:100%">
                            <input type="button" value="Print" class="formbutton" name="print" id="print" onClick="new_window()" style="width:100px" />
                        </div>
                        <div id="buyer_summary" style="border:none">
                            <table width="2220" class="rpt_table" border="0" rules="all" align="center">
                                <thead>
                                	<?php $colspan=22;
                                		if($type==1){
                                			$colspan=$colspan+1;
                                		}
                                	?>
                                    <tr align="center" id="company_id_td" style="visibility:hidden; border:none">
                                        <th colspan="<?php echo $colspan;?>" style="border:none">
                                            <font size="3"><strong>Company Name: <?=$company_library[$company_name]; ?></strong></font>
                                        </th>
                                    </tr>
                                    <tr align="center" id="date_td" style="visibility:hidden;border:none">
                                         <th colspan="<?php echo $colspan;?>" style="border:none"><font size="3"><?="From ". change_date_format($start_date). " To ". change_date_format($end_date);?></font></th>
                                    </tr>
                                    <tr align="center">
                                        <th colspan="<?php echo $colspan;?>">Buyer Level Summary</th>
                                    </tr>
                                    <tr>
                                        <th width="40">SL</th>
                                        <th width="130">Buyer Name</th>
                                         <?php if($type==1){?>
                                        <th width="100">Order Qty.</th>
                                   		 <?php }?>
                                        <th width="100">Country Order Qty.</th>
                                        <th width="100">Grey Req</th>
                                        <th width="100">Yarn Issue + Net Transfer</th>
                                        <th width="100">Yarn Balance</th>
                                        <th width="100">Grey Fabric Available</th>
                                        <th width="100">Grey Receive Balance</th>
                                        <th width="100">Grey To Dye</th>
                                        <th width="100">Grey Issue Balance</th>
                                        <th width="100">Receive By Batch</th>
                                        <th width="100">Batch Qnty</th>
                                        <th width="100">Yet To Batch</th>
                                        <th width="100">Batch Balance</th>
                                        <th width="100">Total Dye Qnty</th>
                                        <th width="100">Dyeing Balance</th>
                                        <th width="100">Yet to Dyeing</th>

                                        <th width="100">Finish Fabric Req</th>
                                        <th width="100">Finish Fabric Production</th>
                                        <th width="100">Finish Fabric Available</th>
                                        <th width="100">Finish Fabric Balance</th>
                                        <th>Issue To Cut</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?
                                $b_sl=1;
                                $buyer_number=asort($buyer_name_array);
                                 $order_qty_array_sum_without_country=0;
                                foreach($buyer_name_array as $key=>$value)
                                {
                                    if ($b_sl%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

                                    $batch_bl=$grey_issue_array[$key]-$batch_qnty_array[$key];
                                    $yetToDye=$batch_qnty_array[$key]-$dye_qnty_array[$key];//$order_qty_array[$key]
									$dyeBalance=$grey_required_array[$key]-$dye_qnty_array[$key];
									$yet_to_batch=$greyIssueBalance=0;
                                    $yet_to_batch=$receive_by_batch_array[$key]-$batch_qnty_array[$key];
									$greyIssueBalance=$grey_required_array[$key]-$grey_issue_array[$key];
                                    $html.="<tr bgcolor='$bgcolor'>
                                            <td align='right'>".$b_sl."</td>
                                            <td align='right'>".$value."</td>";
                                             if($type==1){
                                            	$html.="<td align='right'>".number_format($order_qty_array[$key],2)."</td>";
                                            }

											$html.="<td align='right'>".number_format($country_order_qty_array[$key],2)."</td>
                                            <td align='right'>".number_format($grey_required_array[$key],2)."</td>
                                            <td align='right'>".number_format($yarn_issue_array[$key],2)."</td>
                                            <td align='right'>".number_format($yarn_balance_array[$key],2)."</td>
                                            <td align='right'>".number_format($knitted_array[$key],2)."</td>
                                            <td align='right'>".number_format($grey_balance_array[$key],2)."</td>
                                            <td align='right'>".number_format($grey_issue_array[$key],2)."</td>
											<td align='right'>".number_format($greyIssueBalance,2)."</td>
                                            <td align='right'>".number_format($receive_by_batch_array[$key],2)."</td>
											<td align='right'>".number_format($batch_qnty_array[$key],2)."</td>
											<td align='right'>".number_format($yet_to_batch,2)."</td>
                                            <td align='right'>".number_format($batch_bl,2)."</td>
                                            <td align='right'>".number_format($dye_qnty_array[$key],2)."</td>
											<td align='right'>".number_format($dyeBalance,2)."</td>
                                            <td align='right'>".number_format($yetToDye,2)."</td>
                                            <td align='right'>".number_format($fin_fab_Requi_array[$key],2)."</td>
											<td align='right'>".number_format($finFabProductionArr[$key],2)."</td>
                                            <td align='right'>".number_format($fin_fab_recei_array[$key],2)."</td>
                                            <td align='right'>".number_format($fin_balance_array[$key],2)."</td>
                                            <td align='right'>".number_format($issue_toCut_array[$key],2)."</td>
                                            </tr>
                                        ";

                                    ?>
                                    <tr bgcolor="<?=$bgcolor; ?>">
                                        <td width="40"><?=$b_sl ;?></td>
                                        <td width="130"><?=$value;?></td>
                                         <?php if($type==1){?>
                                        <td width="100" align="right"><?php echo number_format($order_qty_array[$key],2);
                                        	$order_qty_array_sum_without_country+=$order_qty_array[$key];
                                        ?></td>
                                   		 <?php  }?>
                                        <td width="100" align="right"><?=number_format($country_order_qty_array[$key],2); $order_qty_array_tot+=$country_order_qty_array[$key]; ?></td>
                                        <td width="100" align="right"><?=number_format($grey_required_array[$key],2); $grey_required_array_tot+=$grey_required_array[$key]; ?></td>
                                        <td width="100" align="right"><?=number_format($yarn_issue_array[$key],2); $yarn_issue_array_tot+=$yarn_issue_array[$key]; ?></td>
                                        <td width="100" align="right"><?=number_format($yarn_balance_array[$key],2); $yarn_balance_array_tot+=$yarn_balance_array[$key];?></td>
                                        <td width="100" align="right"><?=number_format($knitted_array[$key],2); $knitted_array_tot+=$knitted_array[$key];?></td>
                                        <td width="100" align="right"><?=number_format($grey_balance_array[$key],2); $grey_balance_array_tot+=$grey_balance_array[$key];?></td>
                                        <td width="100" align="right"><?=number_format($grey_issue_array[$key],2);$grey_issue_array_tot+=$grey_issue_array[$key]; ?></td>
                                        <td width="100" align="right"><?=number_format($greyIssueBalance,2); $greyIssueBalance_array_tot+=$greyIssueBalance; ?></td>
                                        <td width="100" align="right"><?=number_format($receive_by_batch_array[$key],2);$receive_by_batch_array_tot+=$receive_by_batch_array[$key]; ?></td>
                                        <td width="100" align="right"><?=number_format($batch_qnty_array[$key],2);$batch_qnty_array_tot+=$batch_qnty_array[$key]; ?></td>
                                        <td width="100" align="right" title="Receive By Batch-Batch Quantity"><?=number_format($yet_to_batch,2);$yet_to_batch_tot+=$yet_to_batch; ?></td>
                                        <td width="100" align="right"><?=number_format($batch_bl,2); $batch_bl_tot+=$batch_bl; ?></td>
                                        <td width="100" align="right"><?=number_format($dye_qnty_array[$key],2); $dye_qnty_array_tot+=$dye_qnty_array[$key];?></td>
                                        <td width="100" align="right"><?=number_format($dyeBalance,2);$dyeBalance_tot+=$dyeBalance;?></td>
                                        <td width="100" align="right"><?=number_format($yetToDye,2);$yetToDye_tot+=$yetToDye;?></td>
                                        <td width="100" align="right"><?=number_format($fin_fab_Requi_array[$key],2); $fin_fab_Requi_array_tot+=$fin_fab_Requi_array[$key]; ?></td>
                                        <td width="100" align="right"><?=number_format($finFabProductionArr[$key],2); $finFabProductionTot+=$finFabProductionArr[$key]; ?></td>
                                        <td width="100" align="right"><?=number_format($fin_fab_recei_array[$key],2); $fin_fab_recei_array_tot+=$fin_fab_recei_array[$key]; ?></td>
                                        <td width="100" align="right"><?=number_format($fin_balance_array[$key],2); $fin_balance_array_tot+=$fin_balance_array[$key];?></td>
                                        <td align="right"><?=number_format($issue_toCut_array[$key],2); $issue_toCut_array_tot+=$issue_toCut_array[$key]; ?></td>
                                    </tr>
                                <?
                                $b_sl++;
                                }
                                ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th width="40" colspan="2" align="right">Total</th>
                                         <?php if($type==1){?>
                                        <th width="100" align="right"><?php echo $order_qty_array_sum_without_country;?></th>
                                   		 <?php }?>
                                        <th width="100" align="right"><?=number_format($order_qty_array_tot,2);?></th>
                                        <th width="100" align="right"><?=number_format($grey_required_array_tot,2);?></th>
                                        <th width="100" align="right"><?=number_format($yarn_issue_array_tot,2) ;?></th>
                                        <th width="100" align="right"><?=number_format($yarn_balance_array_tot,2) ;?></th>
                                        <th width="100" align="right"><?=number_format($knitted_array_tot,2) ;?></th>
                                        <th width="100" align="right"><?=number_format($grey_balance_array_tot,2) ;?></th>
                                        <th width="100" align="right"><?=number_format($grey_issue_array_tot,2) ;?></th>
                                        <th width="100" align="right"><?=number_format($greyIssueBalance_array_tot,2) ;?></th>
                                        <th width="100" align="right"><?=number_format($receive_by_batch_array_tot,2) ;?></th>
                                        <th width="100" align="right"><?=number_format($batch_qnty_array_tot,2) ;?></th>
                                        <th width="100" align="right"><?=number_format($yet_to_batch_tot,2) ;?></th>
                                        <th width="100" align="right"><?=number_format($batch_bl_tot,2) ;?></th>
                                        <th width="100" align="right"><?=number_format($dye_qnty_array_tot,2) ;?></th>
                                        <th width="100" align="right"><?=number_format($dyeBalance_tot,2) ;?></th>
                                        <th width="100" align="right"><?=number_format($yetToDye_tot,2) ;?></th>
                                        <th width="100" align="right"><?=number_format($fin_fab_Requi_array_tot,2) ;?></th>
                                        <th width="100" align="right"><?=number_format($finFabProductionTot,2) ;?></th>
                                        <th width="100" align="right"><?=number_format($fin_fab_recei_array_tot,2) ;?></th>
                                        <th width="100" align="right"><?=number_format($fin_balance_array_tot,2) ;?></th>
                                        <th align="right"><?=number_format($issue_toCut_array_tot,2) ;?></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </td>
                    <?
                        $html.="</tbody>
                                <tfoot>
									<tr>
										<th colspan='2' align='right'>Total</th>";
										  if($type==1){
                                        $html.=" <th align='right'>".number_format($order_qty_array_sum_without_country,2)."</td>";
                                   		 }

										 $html.="<th align='right'>".number_format($order_qty_array_tot,2)."</th>
										<th align='right'>".number_format($grey_required_array_tot,2)."</th>
										<th align='right'>".number_format($yarn_issue_array_tot,2)."</th>
										<th align='right'>".number_format($yarn_balance_array_tot,2)."</th>
										<th align='right'>".number_format($knitted_array_tot,2)."</th>
										<th align='right'>".number_format($grey_balance_array_tot,2)."</th>
										<th align='right'>".number_format($grey_issue_array_tot,2)."</th>
										<th align='right'>".number_format($greyIssueBalance_array_tot,2)."</th>
										<th align='right'>".number_format($receive_by_batch_array_tot,2)."</th>
										<th align='right'>".number_format($batch_qnty_array_tot,2)."</th>
										<th align='right'>".number_format($yet_to_batch_tot,2)."</th>
										<th align='right'>".number_format($batch_bl_tot,2)."</th>
										<th align='right'>".number_format($dye_qnty_array_tot,2)."</th>
										<th align='right'>".number_format($dyeBalance_tot,2)."</th>
										<th align='right'>".number_format($yetToDye_tot,2)."</th>
										<th align='right'>".number_format($fin_fab_Requi_array_tot,2)."</th>
										<th align='right'>".number_format($finFabProductionTot,2)."</th>
										<th align='right'>".number_format($fin_fab_recei_array_tot,2)."</th>
										<th align='right'>".number_format($fin_balance_array_tot,2)."</th>
										<th align='right'>".number_format($issue_toCut_array_tot,2)."</th>
									</tr>
                                </tfoot>
                            </table>
                            </td>
                            <td width='90'></td>
                          <td>
                                <table border=1 rules='all'>
                                    <thead>
                                    <tr>
                                        <th colspan='3'>Summary</th>
                                    </tr>
                                    <tr bgcolor='#FFFFFF'>
                                        <th>Particulars</th>
                                        <th>Total Qnty</th>
                                        <th>% On Required</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr bgcolor='#FFFFFF'>
                                        <td>Total Yarn Required</td>
                                        <td align='right'>".number_format($tot_fabric_req,2)."</td>
                                        <td align='right'></td>
                                    </tr>
                                    <tr bgcolor='#FFFFFF'>
                                        <td>Total Yarn Issued To Knitting</td>
                                        <td align='right'>".number_format($tot_yarn_issue_qnty,2)."</td>
                                        <td align='right'>".number_format($tot_yarn_issue_qnty/$tot_fabric_req*100,2)."%</td>
                                    </tr>
                                    <tr style='background-color:#CFF; font-weight:bold'>
                                        <td>Total Yarn Balance</td>
                                        <td align='right'>".number_format($tot_fabric_req-$tot_yarn_issue_qnty,2)."</td>
                                        <td align='right'>".number_format(((($tot_fabric_req-$tot_yarn_issue_qnty)/$tot_fabric_req)*100),2)."%</td>
                                    </tr>
                                    <tr bgcolor='#FFFFFF'>
                                        <td>Total Grey Fabric Required</td>
                                        <td align='right'>".number_format($tot_fabric_req,2)."</td>
                                        <td align='right'></td>
                                    </tr>
									<tr bgcolor='#FFFFFF'>
                                       <td>Total Grey Fabric Available</td>
                                       <td align='right'>".number_format($tot_grey_available,2)."</td>
                                       <td align='right'>".number_format(($tot_grey_available)/$tot_fabric_req*100,2)."%</td>
                                    </tr>
                                    <tr bgcolor='#FFFFFF'>
                                        <td>Total Grey Fabric Issued To Dye</td>
                                        <td align='right'>".number_format($tot_grey_issue,2)."</td>
                                        <td align='right'>".number_format($tot_grey_issue/$tot_fabric_req*100,2)."%</td>
                                    </tr>
                                    <tr style='background-color:#CFF; font-weight:bold'>
                                        <td>Total Grey Fabric Issue Balance</td>
                                        <td align='right'>".number_format($tot_fabric_req-$tot_grey_issue,2)."</td>
                                        <td align='right'>".number_format(((($tot_fabric_req-$tot_grey_issue)/$tot_fabric_req)*100),2)."%</td>
                                    </tr>
                                    <tr bgcolor='#FFFFFF'>
                                        <td>Total Batch Qnty</td>
                                        <td align='right'>".number_format($tot_batch_qnty,2)."</td>
                                        <td align='right'>&nbsp;</td>
                                    </tr>
                                    <tr style='background-color:#CFF; font-weight:bold'>
                                        <td>Total Batch Balance To Grey Required</td>
                                        <td align='right'>".number_format($tot_fabric_req-$tot_batch_qnty,2)."</td>
                                        <td align='right'>&nbsp;</td>
                                    </tr>
                                    <tr bgcolor='#FFFFFF'>
                                        <td>Total Dye Qnty</td>
                                        <td align='right'>".number_format($tot_dye_qnty,2)."</td>
                                        <td align='right'>".number_format($tot_dye_qnty/$tot_fabric_req*100,2)."%</td>
                                    </tr>
                                    <tr style='background-color:#CFF; font-weight:bold'>
                                        <td>Total Dye Balance To Grey Required</td>
                                        <td align='right'>".number_format($tot_fabric_req-$tot_dye_qnty,2)."</td>
                                        <td align='right'>".number_format(($tot_fabric_req-$tot_dye_qnty)/$tot_fabric_req*100,2)."%</td>
                                    </tr>
                                    <tr bgcolor='#FFFFFF'>
                                        <td>Total Finish Fabric Required</td>
                                        <td align='right'>".number_format($tot_color_wise_req,2)."</td>
                                        <td align='right'></td>
                                    </tr>
                                    <tr bgcolor='#FFFFFF'>
                                        <td>Total Finish Fabric Receive</td>
                                        <td align='right'>".number_format($tot_fabric_available,2)."</td>
                                        <td align='right'>".number_format($tot_fabric_available/$tot_color_wise_req*100,2)."%</td>
                                    </tr>
                                    <tr style='background-color:#CFF; font-weight:bold'>
                                        <td>Total Finish Fabric Balance</td>
                                        <td align='right'>".number_format($tot_color_wise_req-($tot_fabric_available),2)."</td>
                                        <td align='right'>".number_format(((($tot_color_wise_req-($tot_fabric_available))/$tot_color_wise_req)*100),2)."%</td>
                                    </tr>
                                    <tr bgcolor='#FFFFFF'>
                                        <td>Total Finish Fabric Issued To Cut</td>
                                        <td align='right'>".number_format($tot_issue_to_cut_qnty,2)."</td>
                                        <td align='right'>".number_format($tot_issue_to_cut_qnty/$tot_color_wise_req*100,2)."%</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                            </tr>
                            </table>
                            ";
                        ?>
                </tr>
            </table>
        </fieldset>
	<?
	}

	foreach (glob("../../../ext_resource/tmp_report/$user_name*.xls") as $filename)
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename="../../../ext_resource/tmp_report/".$user_name."_".$name.".xls";
	$filename_short="../../../ext_resource/tmp_report/".$user_name."_".$name."short.xls";
	$filename_medium="../../../ext_resource/tmp_report/".$user_name."_".$name."medium.xls";
	$create_new_doc = fopen($filename, 'w');
	$create_new_doc_short = fopen($filename_short, 'w');
	$create_new_doc_medium = fopen($filename_medium, 'w');
	$is_created = fwrite($create_new_doc,$html);
	$is_created_short = fwrite($create_new_doc_short,$html_short);
	$is_created_medium = fwrite($create_new_doc_medium,$html_medium);
	$filename="../../ext_resource/tmp_report/".$user_name."_".$name.".xls";
	$filename_short="../../ext_resource/tmp_report/".$user_name."_".$name."short.xls";
	$filename_medium="../../ext_resource/tmp_report/".$user_name."_".$name."medium.xls";
	echo "$total_data####$filename####$filename_short####$html####$filename_medium####$type";
	exit();
}

if($action=="report_generate_fabric_wise")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$company_name= str_replace("'","",$cbo_company_name);
	$bookingNo = str_replace("'", "", $txt_booking_no);
    $bookingId = str_replace("'", "", $txt_booking_id);
    $cbo_active_status=str_replace("'", "",$cbo_active_status);

    if($cbo_active_status!=4)
	{
		$orderStatusCond = " and b.status_active in($cbo_active_status)";
	}
	else
	{
		$orderStatusCond = " and b.status_active in(1,2,3)";
	}

	if($bookingNo != '')
    {
    	if($bookingId != ''){$bookingIdCond = " and a.id in($bookingId)";}
    	$sql_booking = "SELECT a.booking_no_prefix_num, b.po_break_down_id from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.booking_no_prefix_num in($bookingNo) $bookingIdCond";
    	$sql_res = sql_select($sql_booking);
    	$poIdArray = array();
    	foreach ($sql_res as $val)
    	{
    		$poIdArray[$val[csf('po_break_down_id')]] = $val[csf('po_break_down_id')];
    	}
    	$bookingPoIds = implode(",", $poIdArray);
    }
    //echo '<pre>';print_r();

    if($bookingPoIds !="")	//check booking po
	{
		$po_style_cond=" and b.id in($bookingPoIds)";
	}

	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else $buyer_id_cond="";
	}
	else $buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";


	$date_type = str_replace("'","",$cbo_date_type);
	$start_date=str_replace("'","",trim($txt_date_from));
	$end_date=str_replace("'","",trim($txt_date_to));
	if($date_type==1)
	{
		if($start_date!="" && $end_date!="") $date_search_cond_country="and country_ship_date between '$start_date' and '$end_date'"; else $date_search_cond_country="";
		if ($start_date=="" && $end_date=="") $date_search_cond=""; else $date_search_cond="and c.country_ship_date between '$start_date' and '$end_date'";
	}
	else if($date_type==2)
	{
		if ($start_date=="" && $end_date=="") $date_search_cond=""; else $date_search_cond="and b.pub_shipment_date between '$start_date' and '$end_date'";
	}
	else if($date_type==3)
	{
		if ($start_date=="" && $end_date=="") $date_search_cond=""; else $date_search_cond="and b.shipment_date between '$start_date' and '$end_date'";
	}
	else if($date_type==4)
	{
		if($db_type==0)
		{
			if ($start_date=="" && $end_date=="") $date_search_cond=""; else $date_search_cond="and b.insert_date between '".$start_date."' and '".$end_date." 23:59:59'";
		}
		else
		{
			if ($start_date=="" && $end_date=="") $date_search_cond=""; else $date_search_cond="and b.insert_date between '".$start_date."' and '".$end_date." 11:59:59 PM'";
		}
	}

	$cbo_year=str_replace("'","",$cbo_year);
	$year_cond="";
	if(trim($cbo_year)!=0)
	{
		if ($db_type==0) $year_cond=" and YEAR(a.insert_date)=$cbo_year";
		else if ($db_type==2) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
	}

	if ($db_type==0) $date_diff_cond="DATEDIFF(b.pub_shipment_date,b.po_received_date)";
	else if ($db_type==2) $date_diff_cond="(b.pub_shipment_date - b.po_received_date)";

	$txt_job_no=str_replace("'","",$txt_job_no);
	$job_no_cond="";
	if(trim($txt_job_no)!="")
	{
		$job_no=trim($txt_job_no);
		$job_no_cond=" and a.job_no_prefix_num=$job_no";
	}

	$cbo_type=str_replace("'","",$cbo_type);
	$txt_search_string=str_replace("'","",$txt_search_string);
	if(trim($txt_search_string)!="") $search_string="%".trim($txt_search_string)."%"; else $search_string="%%";

	if($cbo_type==1)
	{
		if(trim($txt_search_string)!="") $po_style_src_cond=" and b.po_number like '$search_string'"; else $po_style_src_cond="";
	}
	else if($cbo_type==2)
	{
		if(trim($txt_search_string)!="") $po_style_src_cond=" and a.style_ref_no like '$search_string'"; else $po_style_src_cond="";
	}
	else if($cbo_type==3)
	{
		if(trim($txt_search_string)!="") $po_style_src_cond=" and b.file_no='$txt_search_string'"; else $po_style_src_cond="";
	}
	else if($cbo_type==4)
	{
		if(trim($txt_search_string)!="") $po_style_src_cond=" and b.grouping='$txt_search_string'"; else $po_style_src_cond="";
	}

	if (str_replace("'", "", trim($cbo_order_status))!= 0) $is_confirmed_cond = " and b.is_confirmed = '" . str_replace("'", "", trim($cbo_order_status)) . "'";
    else $is_confirmed_cond = "";

	$cbo_discrepancy=str_replace("'","",trim($cbo_discrepancy));
	if($cbo_discrepancy==0) $discrepancy_td_color=""; else $discrepancy_td_color="#FF4F4F";

	if ($start_date=="" && $end_date=="") $country_date_cond="";
	else $country_date_cond="and c.country_ship_date between '$start_date' and '$end_date'";
	$cbo_shipping_status=str_replace("'","",trim($cbo_shipping_status));
	if(trim($cbo_shipping_status)!="") $shipping_status="%".trim($cbo_shipping_status)."%"; else $shipping_status="%%";
	if ($shipping_status=='%%') $siping_status_cond=""; else $siping_status_cond="and b.shiping_status like '$shipping_status'";

	$company_arr=return_library_array("select id, company_short_name from lib_company where status_active=1", 'id','company_short_name');
	$supplier_arr=return_library_array("select id, supplier_name from lib_supplier where status_active=1", 'id','supplier_name');
	$buyer_arr=return_library_array("select id, short_name from lib_buyer where status_active=1", 'id','short_name');
	$color_arr=return_library_array("select id, color_name from lib_color where status_active=1", 'id','color_name');

	/* $sql="SELECT a.id as JOB_ID, a.COMPANY_NAME, a.BUYER_NAME, a.STYLE_REF_NO, a.JOB_NO, a.JOB_NO_PREFIX_NUM, b.id as PO_ID, b.PO_NUMBER, b.pub_shipment_date as SHIP_DATE, b.SHIPING_STATUS, b.insert_date as PO_INSERT_DATE, b.PO_RECEIVED_DATE, $date_diff_cond as DATE_DIFF, b.PO_QUANTITY, c.PLAN_CUT_QNTY, c.ITEM_NUMBER_ID, d.lib_yarn_count_deter_id as DETERMINATION_ID, d.composition as FAB_COMPOSITION, d.construction as FAB_TYPE, d.color_type_id as COLOR_TYPE, d.gsm_weight as GSM, e.color_number_id as COLOR_ID, e.REQUIRMENT
	from wo_po_details_master a
	join wo_po_break_down b on a.id=b.job_id and b.is_deleted=0
	join wo_po_color_size_breakdown c on a.id=c.job_id and b.id=c.po_break_down_id and c.is_deleted=0
	join wo_pre_cost_fabric_cost_dtls d on a.id=d.job_id and c.item_number_id=d.item_number_id and d.status_active=1 and d.is_deleted=0
	join wo_pre_cos_fab_co_avg_con_dtls e on a.id=e.job_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and e.status_active=1 and e.is_deleted=0 and e.cons !=0
	where a.company_name=$company_name $siping_status_cond $orderStatusCond $buyer_id_cond $date_search_cond $year_cond $job_no_cond $po_style_src_cond $is_confirmed_cond $po_style_cond and a.status_active=1 and a.is_deleted=0"; */

	$sql="SELECT a.id as JOB_ID, a.COMPANY_NAME, a.BUYER_NAME, a.STYLE_REF_NO, a.JOB_NO, a.JOB_NO_PREFIX_NUM, b.id as PO_ID, b.PO_NUMBER, b.pub_shipment_date as SHIP_DATE, b.SHIPING_STATUS, b.insert_date as PO_INSERT_DATE, b.PO_RECEIVED_DATE, $date_diff_cond as DATE_DIFF, b.PO_QUANTITY, c.PLAN_CUT_QNTY, c.ITEM_NUMBER_ID, d.lib_yarn_count_deter_id as DETERMINATION_ID, d.composition as FAB_COMPOSITION, d.construction as FAB_TYPE, d.color_type_id as COLOR_TYPE, d.gsm_weight as GSM, e.color_number_id as COLOR_ID, e.REQUIRMENT, h.stripe_color as STRIPE_COLOR, g.contrast_color_id AS CONTRAST_COLOR_ID
	from wo_po_details_master a
	join wo_po_break_down b on a.id=b.job_id and b.is_deleted=0
	join wo_po_color_size_breakdown c on a.id=c.job_id and b.id=c.po_break_down_id and c.is_deleted=0
	join wo_pre_cost_fabric_cost_dtls d on a.id=d.job_id and c.item_number_id=d.item_number_id and d.status_active=1 and d.is_deleted=0
	join wo_pre_cos_fab_co_avg_con_dtls e on a.id=e.job_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and e.status_active=1 and e.is_deleted=0 and e.cons !=0
	left join wo_pre_cos_fab_co_color_dtls g on a.id=g.job_id and d.id=g.pre_cost_fabric_cost_dtls_id and c.color_number_id=g.gmts_color_id
	and e.color_number_id =g.gmts_color_id
	left join wo_pre_stripe_color h on a.id=h.job_id and  c.item_number_id= h.item_number_id and d.id=h.pre_cost_fabric_cost_dtls_id
	and e.color_number_id =h.color_number_id and e.po_break_down_id=h.po_break_down_id and e.gmts_sizes=h.size_number_id
	where a.company_name=$company_name $siping_status_cond $orderStatusCond $buyer_id_cond $date_search_cond $year_cond $job_no_cond $po_style_src_cond $is_confirmed_cond $po_style_cond and a.status_active=1 and a.is_deleted=0";

	$sql_res=sql_select($sql);
	$main_data_arr=array(); $poIdArray= array();
	foreach ($sql_res as $row)
	{
		$po_ids.=$row['PO_ID'].',';
		$job_nos.="'".$row['JOB_NO']."',";

		$poIdArray[$row['PO_ID']] = $row['PO_ID'];

		$fabricColorId=$row['STRIPE_COLOR'];
		if(!$fabricColorId){
			$fabricColorId=$row['CONTRAST_COLOR_ID'];
		}
		if(!$fabricColorId){
			$fabricColorId=$row['COLOR_ID'];
		}

		$main_data_arr[$row['PO_ID']][$row['COLOR_ID']][$row['DETERMINATION_ID']][$row['COLOR_TYPE']]['COMPANY_NAME']=$row['COMPANY_NAME'];
		$main_data_arr[$row['PO_ID']][$row['COLOR_ID']][$row['DETERMINATION_ID']][$row['COLOR_TYPE']]['BUYER_NAME']=$row['BUYER_NAME'];
		$main_data_arr[$row['PO_ID']][$row['COLOR_ID']][$row['DETERMINATION_ID']][$row['COLOR_TYPE']]['STYLE_REF_NO']=$row['STYLE_REF_NO'];
		$main_data_arr[$row['PO_ID']][$row['COLOR_ID']][$row['DETERMINATION_ID']][$row['COLOR_TYPE']]['JOB_NO']=$row['JOB_NO'];
		$main_data_arr[$row['PO_ID']][$row['COLOR_ID']][$row['DETERMINATION_ID']][$row['COLOR_TYPE']]['JOB_NO_PREFIX_NUM']=$row['JOB_NO_PREFIX_NUM'];
		$main_data_arr[$row['PO_ID']][$row['COLOR_ID']][$row['DETERMINATION_ID']][$row['COLOR_TYPE']]['PO_ID']=$row['PO_ID'];
		$main_data_arr[$row['PO_ID']][$row['COLOR_ID']][$row['DETERMINATION_ID']][$row['COLOR_TYPE']]['PO_NUMBER']=$row['PO_NUMBER'];
		$main_data_arr[$row['PO_ID']][$row['COLOR_ID']][$row['DETERMINATION_ID']][$row['COLOR_TYPE']]['FAB_COMPOSITION']=$row['FAB_COMPOSITION'];
		$main_data_arr[$row['PO_ID']][$row['COLOR_ID']][$row['DETERMINATION_ID']][$row['COLOR_TYPE']]['FAB_TYPE']=$row['FAB_TYPE'];
		$main_data_arr[$row['PO_ID']][$row['COLOR_ID']][$row['DETERMINATION_ID']][$row['COLOR_TYPE']]['SHIP_DATE']=$row['SHIP_DATE'];
		$main_data_arr[$row['PO_ID']][$row['COLOR_ID']][$row['DETERMINATION_ID']][$row['COLOR_TYPE']]['PO_INSERT_DATE']=$row['PO_INSERT_DATE'];
		$main_data_arr[$row['PO_ID']][$row['COLOR_ID']][$row['DETERMINATION_ID']][$row['COLOR_TYPE']]['PO_RECEIVED_DATE']=$row['PO_RECEIVED_DATE'];
		$main_data_arr[$row['PO_ID']][$row['COLOR_ID']][$row['DETERMINATION_ID']][$row['COLOR_TYPE']]['PO_QUANTITY']=$row['PO_QUANTITY'];
		$main_data_arr[$row['PO_ID']][$row['COLOR_ID']][$row['DETERMINATION_ID']][$row['COLOR_TYPE']]['DATE_DIFF']=$row['DATE_DIFF'];
		$main_data_arr[$row['PO_ID']][$row['COLOR_ID']][$row['DETERMINATION_ID']][$row['COLOR_TYPE']]['GSM']=$row['GSM'];
		$main_data_arr[$row['PO_ID']][$row['COLOR_ID']][$row['DETERMINATION_ID']][$row['COLOR_TYPE']]['ITEM_NUMBER_ID']=$row['ITEM_NUMBER_ID'];
		$main_data_arr[$row['PO_ID']][$row['COLOR_ID']][$row['DETERMINATION_ID']][$row['COLOR_TYPE']]['FABRIC_COLOR'] .=$fabricColorId.",";
	}

	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (3,4) and ENTRY_FORM=30");
	oci_commit($con);

	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 30, 3, $poIdArray, $empty_arr);//Po ID
	disconnect($con);

	$sql_booking_res=sql_select("SELECT a.booking_no_prefix_num, b.po_break_down_id from wo_booking_mst a, wo_booking_dtls b, gbl_temp_engine d where a.booking_no=b.booking_no and b.booking_type in(1,4) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.po_break_down_id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=30 and d.ref_from=3");
	$bookingArray=array();
	foreach ($sql_booking_res as $val) {
		$bookingArray[$val[csf('po_break_down_id')]] .= $val[csf('booking_no_prefix_num')].',';
	}

	$condition= new condition();
    $condition->po_id_in("$po_ids");
    $condition->init();

    $fabric= new fabric($condition);
    $fabric2= new fabric($condition);
    //$fabric2= new fabric($condition);
    //echo $fabric->getQuery(); die;
    $fabric_qty_arr=$fabric->getQtyArray_by_OrderLibYarnCountDeterIdAndGmtscolor_knitAndwoven_greyAndfinish();
    $fabric_qty_arr2=$fabric2->getQtyArray_by_order_knitAndwoven_greyAndfinish();
	//echo '<pre>';print_r($fabric_qty_arr2);


	$finish_purchase_qnty_arr=array(); $finishrec_basis_arr=array();
	$sql_fin_purchase="select a.id, b.fabric_description_id, c.po_breakdown_id, c.color_id, a.receive_basis, c.quantity from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c, gbl_temp_engine d where a.id=b.mst_id and b.id=c.dtls_id  and a.entry_form in(17,37,58,68) and c.entry_form in(17,37,58,68) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=30 and d.ref_from=3";//and a.receive_basis<>9 sum(c.quantity) as finish_purchase
	$dataArrayFinPurchase=sql_select($sql_fin_purchase);
	foreach($dataArrayFinPurchase as $finRow)
	{
		if($finRow[csf('receive_basis')]==9)
		{
			$finish_purchase_qnty_arr[$finRow[csf('po_breakdown_id')]][$finRow[csf('color_id')]][$finRow[csf('fabric_description_id')]]['production']+=$finRow[csf('quantity')];
		}
		else
		{
			$finish_purchase_qnty_arr[$finRow[csf('po_breakdown_id')]][$finRow[csf('color_id')]][$finRow[csf('fabric_description_id')]]['purchase']+=$finRow[csf('quantity')];
		}
		$finishrec_basis_arr[$finRow[csf('id')]]=$finRow[csf('receive_basis')];
	}
	unset($dataArrayFinPurchase);
	//echo '<pre>';print_r($finish_purchase_qnty_arr);

	$grey_receive_return_qnty_arr=array();
	$finish_recv_rtn_qnty_arr=array();
    $sql_return="select a.entry_form, a.received_id, c.trans_type, c.po_breakdown_id, c.color_id, c.quantity from inv_issue_master a, inv_transaction b, order_wise_pro_details c, gbl_temp_engine d where a.id=b.mst_id and b.id=c.trans_id and a.entry_form in (45,46,126) and c.entry_form in (45,46,126) and c.trans_type=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=30 and d.ref_from=3";//45,84 --- grey 46----- finish
	$dataArrayReturn=sql_select($sql_return);
	foreach($dataArrayReturn as $rtRow)
	{
		if($rtRow[csf('entry_form')]==46 || $rtRow[csf('entry_form')]==126)
		{
			$finish_rec_bacis=$finishrec_basis_arr[$rtRow[csf('received_id')]];
			if($finish_rec_bacis==9)
			{
				$finish_recv_rtn_qnty_arr[$rtRow[csf('po_breakdown_id')]][$rtRow[csf('color_id')]]['production']+=$rtRow[csf('quantity')];
			}
			else
			{
				$finish_recv_rtn_qnty_arr[$rtRow[csf('po_breakdown_id')]][$rtRow[csf('color_id')]]['purchase']+=$rtRow[csf('quantity')];
			}
		}
	}


	$dataArrayTrans = sql_select("select a.trans_id, a.po_breakdown_id, a.color_id, a.entry_form, a.trans_type, a.quantity from order_wise_pro_details a, gbl_temp_engine d where status_active=1 and is_deleted=0 and entry_form in (2,7,14,15,16,18,37,51,52,58,61,66,71,84,126,134) and a.po_breakdown_id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=30 and d.ref_from=3");
	$grey_receive_qnty_arr=array();
	$grey_issue_qnty_arr=array();
	$grey_issue_return_qnty_arr=array();
	foreach($dataArrayTrans as $row)
	{
		if ($row[csf('entry_form')]==2  || $row[csf('entry_form')]==58) {
			$grey_receive_qnty_arr[$row[csf('po_breakdown_id')]] += $row[csf('quantity')];
		}

		$grey_issue=0;
		if($row[csf('entry_form')]==16 || $row[csf('entry_form')]==61) {
			$grey_issue=$row[csf('quantity')];
			$grey_issue_qnty_arr[$row[csf('po_breakdown_id')]] += $row[csf('quantity')];
		}

		if($row[csf('entry_form')]==51 || $row[csf('entry_form')]==84) {
			if($row[csf('trans_type')]==4) {
				$grey_issue_return_qnty_arr[$row[csf('po_breakdown_id')]] += $row[csf('quantity')];
			}
		}

		if($row[csf('entry_form')]==7 || $row[csf('entry_form')]==14 || $row[csf('entry_form')]==15 || $row[csf('entry_form')]==18 || $row[csf('entry_form')]==37 || $row[csf('entry_form')]==52 || $row[csf('entry_form')]==66 || $row[csf('entry_form')]==71 || $row[csf('entry_form')]==126 || $row[csf('entry_form')]==134)
		{
			if($row[csf('entry_form')]==7 && $row[csf('trans_id')]!=0) {
				$finish_purchase_qnty_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]['production']+= $row[csf('quantity')];
			}
		}
	}

	$batch_qnty_arr=array();
	$sql_batch="select a.color_id, b.po_id, b.item_description, sum(b.batch_qnty) as batch_qnty from pro_batch_create_mst a, pro_batch_create_dtls b, gbl_temp_engine d where a.id=b.mst_id and a.entry_form not in (36,74,17) and  a.batch_against not in(2) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=30 and d.ref_from=3 group by a.color_id, b.po_id, b.item_description";
	//and a.entry_form!=36

	$resultBatch=sql_select($sql_batch);
	foreach($resultBatch as $batchRow)
	{
		$item_description_batch = explode(',', $batchRow[csf('item_description')]);
		$fab_composition_batch=trim($item_description_batch[1]);
		$fab_type_batch=trim($item_description_batch[0]);
		$batch_qnty_arr[$batchRow[csf('po_id')]][$batchRow[csf('color_id')]][$fab_composition_batch][$fab_type_batch]=$batchRow[csf('batch_qnty')];
	}

	$dye_qnty_arr=array();
	$sql_dye="select b.po_id, a.color_id, b.item_description, sum(b.batch_qnty) as dye_qnty from pro_batch_create_mst a,pro_batch_create_dtls b, pro_fab_subprocess c, gbl_temp_engine d where a.id=b.mst_id and a.id=c.batch_id and c.load_unload_id=2 and c.entry_form=35 and a.batch_against<>2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.po_id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=30 and d.ref_from=3 group by b.po_id, a.color_id, b.item_description";
	//echo $sql_dye;
	$resultDye=sql_select($sql_dye);
	foreach($resultDye as $dyeRow)
	{
		$item_description = explode(',', $dyeRow[csf('item_description')]);
		$fab_composition=trim($item_description[1]);
		$fab_type=trim($item_description[0]);
		$dye_qnty_arr[$dyeRow[csf('po_id')]][$dyeRow[csf('color_id')]][$fab_composition][$fab_type]=$dyeRow[csf('dye_qnty')];
	}

	// tna data
	$sql_tna="select a.id, a.po_number_id,
		(case when a.task_number=50 then a.task_start_date else null end) as yarn_issue_start_date,
		(case when a.task_number=50 then a.task_finish_date else null end) as yarn_issue_end_date,
		(case when a.task_number=72 then a.task_start_date else null end) as grey_rec_start_date,
		(case when a.task_number=72 then a.task_finish_date else null end) as grey_rec_end_date,
		(case when a.task_number=73 then a.task_start_date else null end) as finish_fab_rcv_start_date,
		(case when a.task_number=73 then a.task_finish_date else null end) as finish_fab_rcv_end_date,
		(case when a.task_number=84 then a.task_start_date else null end) as cutting_start_date
		from tna_process_mst a, gbl_temp_engine d
		where a.status_active=1 and a.po_number_id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=30 and d.ref_from=3";
	$sql_tna_res=sql_select($sql_tna);
	$yarn_issue_start_date=$yarn_issue_end_date=$grey_rec_start_date=$grey_rec_end_date='';
	$finish_fab_rcv_start_date=$finish_fab_rcv_end_date=$cutting_start_date='';
	$tna_date_task_arr=array();
	foreach($sql_tna_res as $row)
	{
		if($row[csf("yarn_issue_start_date")]!="" && $row[csf("yarn_issue_start_date")]!="0000-00-00")
		{
			$tna_date_task_arr[$row[csf("po_number_id")]]['yarn_issue_start_date']=$row[csf("yarn_issue_start_date")];
			$tna_date_task_arr[$row[csf("po_number_id")]]['yarn_issue_end_date']=$row[csf("yarn_issue_end_date")];
		}

		if($row[csf("grey_rec_start_date")]!="" && $row[csf("grey_rec_start_date")]!="0000-00-00")
		{
			$tna_date_task_arr[$row[csf("po_number_id")]]['grey_rec_start_date']=$row[csf("grey_rec_start_date")];
			$tna_date_task_arr[$row[csf("po_number_id")]]['grey_rec_end_date']=$row[csf("grey_rec_end_date")];
		}

		if($row[csf("finish_fab_rcv_start_date")]!="" && $row[csf("finish_fab_rcv_start_date")]!="0000-00-00")
		{
			$tna_date_task_arr[$row[csf("po_number_id")]]['finish_fab_rcv_start_date']=$row[csf("finish_fab_rcv_start_date")];
			$tna_date_task_arr[$row[csf("po_number_id")]]['finish_fab_rcv_end_date']=$row[csf("finish_fab_rcv_end_date")];
		}

		if($row[csf("cutting_start_date")]!="" && $row[csf("cutting_start_date")]!="0000-00-00")
		{
			$tna_date_task_arr[$row[csf("po_number_id")]]['cutting_start_date']=$row[csf("cutting_start_date")];
		}
	}
	//echo '<pre>';print_r($main_data_arr);

	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (3) and ENTRY_FORM=30");
	oci_commit($con);
	disconnect($con);

	$table_width = 2630+80;
	ob_start();

	?>
	<style type="text/css">
		.wrd_brk{word-break: break-all;}
		.left{text-align: left}
		.center{text-align: center}
		.right{text-align: right}
	</style>
	<div width="<? echo $table_width; ?>" style="margin-left: 5px;">
		<table cellpadding="0" cellspacing="0" width="<? echo $table_width; ?>">
			<tr>
			   <td align="center" width="100%"><strong style="font-size:16px"><? echo $company_library[$cbo_company_name]; ?></strong></td>
			</tr>
		</table>

		<table class="rpt_table" border="1" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
					<th width="50">SL</th>
					<th width="80">Company</th>
	                <th width="80">Job No</th>
					<th width="120">Order</th>
	                <th width="120">Style</th>
	                <th width="80">Buyer</th>
	                <th width="80">Order Qty</th>
	                <th width="80">PO Insert Date</th>
	                <th width="80">Ship date</th>
	                <th width="60">Lead Time</th>
	                <th width="80">Booking No</th>
	                <th width="80">Gmts.Color</th>
	                <th width="80">Fab.Color</th>
	                <th width="80">Color Type</th>
	                <th width="120">Fabric Composition</th>
	                <th width="100">Fab type</th>
	                <th width="60">GSM</th>
	                <th width="80">Grey Req</th>
	                <th width="80">Grey received</th>
	                <th width="80">Grey Balance</th>
	                <th width="80">Grey To Dye</th>
	                <th width="80">Batch Qty</th>
	                <th width="80">Dyeing Qty</th>
	                <th width="80">Finish Req Qty</th>
	                <th width="80">Finish Rec Qty</th>
	                <th width="80">Balance</th>
	                <th width="80">Yarn Issue Start Date</th>
	                <th width="80">Yarn Issue Close Date</th>
	                <th width="80">Grey Start Date</th>
	                <th width="80">Grey Close Date</th>
	                <th width="80">Fin Fab Rec Start Date</th>
	                <th width="80">Fin Fab Rec Close Date</th>
	                <th width="80">Cutting Start Date</th>
				</tr>
			</thead>
		</table>
		<div style="width:<? echo $table_width+20; ?>px; overflow-y:scroll; max-height:300px" id="scroll_body">
		    <table class="rpt_table" border="1" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="table_body">
		        <tbody>
		        	<?
		        	$i=1;
		        	$check_order_qty=array(); $check_grey_qty=array(); $check_grey_recv_qnty=array(); $check_grey_balance=array(); $check_grey_fabric_issue=array(); $check_dye_qnty=array(); $check_finishcons_qnty=array(); $check_finish_rece_qnty=array(); $check_finish_balance=array(); $check_batch_qnty=array();
		        	$tot_po_quantity=$tot_graycons=0; $tot_grey_recv_qnty=$tot_grey_balance=0; $tot_grey_fabric_issue=$tot_dye_qnty=0; $tot_finishcons=$tot_finish_rec_qty=0; $tot_finish_balance=$tot_batch_qnty=0; $tot_grey_qty_order=0;
		        	foreach ($main_data_arr as $po_id => $po_data)
		        	{
		        		foreach ($po_data as $color_id => $color_data)
		        		{
	        				foreach ($color_data as $determination_id => $determination_data)
	        				{
        						foreach ($determination_data as $color_type_id => $row)
        						{
        							$tot_grey_qty_order = $fabric_qty_arr2['knit']['grey'][$po_id]+$fabric_qty_arr2['woven']['grey'][$po_id];
	        						$graycons=$fabric_qty_arr['knit']['grey'][$po_id][$determination_id][$color_id]+$fabric_qty_arr['woven']['grey'][$po_id][$determination_id][$color_id];
	        						$finishcons=$fabric_qty_arr['knit']['finish'][$po_id][$determination_id][$color_id]+$fabric_qty_arr['woven']['finish'][$po_id][$determination_id][$color_id];
	        						//echo $po_id.'**'.$determination_id.'**'.$color_id.'**'.$finishcons.'system';
	        						$grey_recv_qnty=$grey_receive_qnty_arr[$po_id];
	        						//$grey_balance = $graycons - $grey_recv_qnty;
	        						$grey_balance =$tot_grey_qty_order - $grey_recv_qnty;

	        						$grey_fabric_issue=$grey_issue_qnty_arr[$po_id]-$grey_issue_return_qnty_arr[$po_id];
	        						$fab_composition=trim($row['FAB_COMPOSITION']);
	        						$fab_type=trim($row['FAB_TYPE']);
	        						$dye_qnty=$dye_qnty_arr[$po_id][$color_id][$fab_composition][$fab_type];
	        						$batch_qnty=$batch_qnty_arr[$po_id][$color_id][$fab_composition][$fab_type];

	        						$fab_rec_return_production=$finish_recv_rtn_qnty_arr[$po_id][$color_id]['production'];
									$fab_rec_return_purchase=$finish_recv_rtn_qnty_arr[$po_id][$color_id]['purchase'];
									$fab_rec_return=$fab_rec_return_production+$fab_rec_return_purchase;
									$fab_production_qnty=$finish_purchase_qnty_arr[$po_id][$color_id][$determination_id]['production']-$fab_rec_return_production;
									$fab_purchase_qnty=$finish_purchase_qnty_arr[$po_id][$color_id][$determination_id]['purchase']-$fab_rec_return_purchase;
									$finish_rec_qty=$fab_production_qnty+$fab_purchase_qnty;

						        	if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					        		?>
						        	<tr bgcolor="<? echo $bgcolor;  ?>" onClick="change_color('trds_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trds_<? echo $i; ?>">
										<td width="50" class="wrd_brk"><? echo $i; ?></td>
										<td width="80" class="wrd_brk"><? echo $company_arr[$row['COMPANY_NAME']]; ?></td>
						                <td width="80" class="wrd_brk"><? echo $row['JOB_NO_PREFIX_NUM']; ?></td>
										<td width="120" class="wrd_brk"><? echo $row['PO_NUMBER']; ?></td>
						                <td width="120" class="wrd_brk"><? echo $row['STYLE_REF_NO']; ?></td>
						                <td width="80" class="wrd_brk"><? echo $buyer_arr[$row['BUYER_NAME']]; ?></td>
						                <td width="80" class="wrd_brk right" title="<? echo $row['PO_QUANTITY']; ?>">
						                	<?
						                	if ($check_order_qty[$po_id]=='') {
						                		echo $row['PO_QUANTITY'];
						                		$tot_po_quantity+=$row['PO_QUANTITY'];
						                	} else '';
						                	$check_order_qty[$po_id]=$po_id;
						                	?>
						                </td>
						                <td width="80" class="wrd_brk center"><? $po_insert_date= explode(' ', $row['PO_INSERT_DATE']); echo change_date_format($po_insert_date[0]); ?></td>
						                <td width="80" class="wrd_brk center"><? echo change_date_format($row['SHIP_DATE']); ?></td>
						                <td width="60" class="wrd_brk"><? echo $row['DATE_DIFF']; ?></td>
						                <td width="80" class="wrd_brk"><? echo implode(',',array_flip(array_flip(explode(',', rtrim($bookingArray[$po_id],','))))); ?></td>
						                <td width="80" class="wrd_brk"><? echo $color_arr[$color_id]; ?></td>
						                <td width="80" class="wrd_brk" title="<? echo $row['FABRIC_COLOR'];?>">
											<?
											$FABRIC_COLOR_NAMES="";
											$FABRIC_COLOR_IDS = chop($row['FABRIC_COLOR'],",");
											$FABRIC_COLOR_ARR = array_unique(explode(",",$FABRIC_COLOR_IDS));
											foreach($FABRIC_COLOR_ARR as $crow)
											{
												$FABRIC_COLOR_NAMES .=$color_arr[$crow].",";
											}
											echo chop($FABRIC_COLOR_NAMES,",");
											?>
										</td>
						                <td width="80" class="wrd_brk"><? echo $color_type[$color_type_id]; ?></td>
						                <td width="120" class="wrd_brk"><? echo $row['FAB_COMPOSITION']; ?></td>
						                <td width="100" class="wrd_brk"><? echo $row['FAB_TYPE']; ?></td>
						                <td width="60" class="wrd_brk center"><? echo $row['GSM']; ?></td>
						                <td width="80" class="wrd_brk right" title="<? echo $graycons; ?>">
						                	<?
						                	$grey_qty_key=$po_id.'**'.$color_id.'**'.$fab_composition.'**'.$fab_type;
						                	if ($check_grey_qty[$grey_qty_key]=='') {
						                		echo number_format($graycons,2,'.','');
						                		$tot_graycons+=$graycons;
						                	} else echo '';
						                	$check_grey_qty[$grey_qty_key]=$grey_qty_key;
						                	?>
						                </td>
						                <td width="80" class="wrd_brk right" title="<? echo $grey_recv_qnty; ?>">
						                	<?
						                	if ($check_grey_recv_qnty[$po_id]=='') {
						                		echo number_format($grey_recv_qnty,2,'.','');
						                		$tot_grey_recv_qnty+=$grey_recv_qnty;
						                	} else echo '';
						                	$check_grey_recv_qnty[$po_id]=$po_id;
						                	?>
						                </td>
						                <td width="80" class="wrd_brk right" title="<? echo $tot_grey_qty_order.' - '.$grey_recv_qnty; ?>">
						                	<?
						                	if ($check_grey_balance[$po_id]=='') {
						                		echo number_format($grey_balance,2,'.','');
						                		$tot_grey_balance+=$grey_balance;
						                	} else echo '';
						                	$check_grey_balance[$po_id]=$po_id;
						                	?>
						                </td>
						                <td width="80" class="wrd_brk right" title="<? echo $grey_fabric_issue; ?>">
						                	<?
						                	if ($check_grey_fabric_issue[$po_id]=='') {
						                		echo number_format($grey_fabric_issue,2,'.','');
						                		$tot_grey_fabric_issue+=$grey_fabric_issue;
						                	} else echo '';
						                	$check_grey_fabric_issue[$po_id]=$po_id;
						                	?>
						                </td>
						                <td width="80" class="wrd_brk right" title="<? echo $batch_qnty; ?>">
						                	<?
						                	$batch_qty_key=$po_id.'**'.$color_id.'**'.$fab_composition.'**'.$fab_type;
						                	if ($batch_qnty !=0){
							                	if ($check_batch_qnty[$batch_qty_key]=='') {
							                		echo number_format($batch_qnty,2,'.','');
							                		$tot_batch_qnty+=$batch_qnty;
							                	} else echo '';
							                }
						                	$check_batch_qnty[$batch_qty_key]=$batch_qty_key;
						                	?>
						                </td>
						                <td width="80" class="wrd_brk right">
						                	<?
						                	$arr_key=$po_id.'**'.$color_id.'**'.$fab_composition.'**'.$fab_type;
						                	if ($dye_qnty !=0){
							                	if ($check_dye_qnty[$arr_key]=='') {
							                		echo number_format($dye_qnty,2,'.','');
							                		$tot_dye_qnty+=$dye_qnty;
							                	} else echo '';
						                	}
						                	$check_dye_qnty[$arr_key]=$arr_key;
						                	?>
						                </td>
						                <td width="80" class="wrd_brk right">
						                	<?
						                	$arr_key2=$po_id.'**'.$color_id.'**'.$determination_id;
						                	if ($finishcons !=0){
							                	if ($check_finishcons_qnty[$arr_key2]=='') {
							                		echo number_format($finishcons,2,'.','');
							                		$tot_finishcons+=$finishcons;
							                	} else echo '';
						                	}
						                	$check_finishcons_qnty[$arr_key2]=$arr_key2;
						                	?>
						                </td>
						                <td width="80" class="wrd_brk right">
						                	<?
						                	$arr_key3=$po_id.'**'.$color_id.'**'.$determination_id;
						                	if ($finish_rec_qty !=0){
							                	if ($check_finish_rece_qnty[$arr_key3]=='') {
							                		echo number_format($finish_rec_qty,2,'.','');
							                		$tot_finish_rec_qty+=$finish_rec_qty;
							                	} else echo '';
						                	}
						                	$check_finish_rece_qnty[$arr_key3]=$arr_key3;
						                	?>
						                </td>
						                <td width="80" class="wrd_brk right" title="<? echo $finishcons.' - '.$finish_rec_qty; ?>">
						                	<?
						                	$arr_key4=$po_id.'**'.$color_id.'**'.$determination_id;
						                	if ($finishcons !=0){
							                	if ($check_finish_balance[$arr_key4]=='') {
							                		$finish_balance = $finishcons - $finish_rec_qty;
							                		echo number_format($finish_balance,2,'.','');
							                		$tot_finish_balance+=$finish_balance;
							                	} else echo '';
						                	}
						                	$check_finish_balance[$arr_key4]=$arr_key4;
						                	?>
						                </td>
						                <td width="80" class="wrd_brk center"><? echo change_date_format($tna_date_task_arr[$po_id]['yarn_issue_start_date']); ?></td>
						                <td width="80" class="wrd_brk center"><? echo change_date_format($tna_date_task_arr[$po_id]['yarn_issue_end_date']); ?></td>
						                <td width="80" class="wrd_brk center"><? echo change_date_format($tna_date_task_arr[$po_id]['grey_rec_start_date']); ?></td>
						                <td width="80" class="wrd_brk center"><? echo change_date_format($tna_date_task_arr[$po_id]['grey_rec_end_date']); ?></td>
						                <td width="80" class="wrd_brk center"><? echo change_date_format($tna_date_task_arr[$po_id]['finish_fab_rcv_start_date']); ?></td>
						                <td width="80" class="wrd_brk center"><? echo change_date_format($tna_date_task_arr[$po_id]['finish_fab_rcv_end_date']); ?></td>
						                <td width="80" class="wrd_brk center"><? echo change_date_format($tna_date_task_arr[$po_id]['cutting_start_date']); ?></td>
									</tr>
									<?
									$i++;
								}
							}
						}
					}
					?>
				</tbody>
		    </table>
	    </div>
	    <table class="rpt_table" border="1" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0">
			<tfoot>
				<tr>
					<th width="50"></th>
					<th width="80"></th>
	                <th width="80"></th>
					<th width="120"></th>
	                <th width="120"></th>
	                <th width="80">Total</th>
	                <th width="80"><? echo $tot_po_quantity; ?></th>
	                <th width="80"></th>
	                <th width="80"></th>
	                <th width="60"></th>
	                <th width="80"></th>
	                <th width="80"></th>
	                <th width="80"></th>
	                <th width="80"></th>
	                <th width="120"></th>
	                <th width="100"></th>
	                <th width="60"></th>
	                <th width="80"><? echo number_format($tot_graycons,2,'.',''); ?></th>
	                <th width="80"><? echo number_format($tot_grey_recv_qnty,2,'.',''); ?></th>
	                <th width="80"><? echo number_format($tot_grey_balance,2,'.',''); ?></th>
	                <th width="80"><? echo number_format($tot_grey_fabric_issue,2,'.',''); ?></th>
	                <th width="80"><? echo number_format($tot_batch_qnty,2,'.',''); ?></th>
	                <th width="80"><? echo number_format($tot_dye_qnty,2,'.',''); ?></th>
	                <th width="80"><? echo number_format($tot_finishcons,2,'.',''); ?></th>
	                <th width="80"><? echo number_format($tot_finish_rec_qty,2,'.',''); ?></th>
	                <th width="80"><? echo number_format($tot_finish_balance,2,'.',''); ?></th>
	                <th width="80"></th>
	                <th width="80"></th>
	                <th width="80"></th>
	                <th width="80"></th>
	                <th width="80"></th>
	                <th width="80"></th>
	                <th width="80"></th>
				</tr>
			</tfoot>
		</table>
	</div>
	<?
	foreach (glob("../../../ext_resource/tmp_report/$user_name*.xls") as $filename)
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename="../../../ext_resource/tmp_report/".$user_name."_".$name.".xls";
	$filename_short="../../../ext_resource/tmp_report/".$user_name."_".$name."short.xls";
	$filename_medium="../../../ext_resource/tmp_report/".$user_name."_".$name."medium.xls";
	$create_new_doc = fopen($filename, 'w');
	$create_new_doc_short = fopen($filename_short, 'w');
	$create_new_doc_medium = fopen($filename_medium, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$is_created_short = fwrite($create_new_doc_short,ob_get_contents());
	$is_created_medium = fwrite($create_new_doc_medium,ob_get_contents());
	$filename="../../ext_resource/tmp_report/".$user_name."_".$name.".xls";
	$filename_short="../../ext_resource/tmp_report/".$user_name."_".$name."short.xls";
	$filename_medium="../../ext_resource/tmp_report/".$user_name."_".$name."medium.xls";
	echo "$total_data####$filename####$filename_short####$html####$filename_medium####$type";
	exit();
}

if($action=="Shipment_date")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?>
<div align="center">
<fieldset style="width:670px">
	<table border="1" class="rpt_table" cellpadding="0" cellspacing="0" rules="all" width="640">
		<thead>
        	<tr>
            	<th colspan="6">Order Details</th>
            </tr>
            <tr>
                <th width="130">PO No</th>
                <th width="120">PO Qnty</th>
                <th width="90">Shipment Date</th>
                <th width="90">PO Receive Date</th>
                <th width="90">PO Entry Date</th>
                <th>Shipping Status</th>
        	</tr>
        </thead>
		<?
        $i=1; $total_order_qnty=0;
        $sql="select a.job_no, a.total_set_qnty as ratio, b.id as po_id, b.po_number, b.po_quantity as po_qnty, b.pub_shipment_date, b.shiping_status, b.insert_date, b.po_received_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in(".str_replace("'","",$order_id).") order by b.pub_shipment_date, b.id";
        $result=sql_select($sql);
        foreach($result as $row)
        {
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

			$order_qnty=$row[csf('po_qnty')]*$row[csf('ratio')];
			$total_order_qnty+=$order_qnty;
			?>
			<tr bgcolor="<? echo $bgcolor; ?>">
                <td width="130"><p><? echo $row[csf('po_number')]; ?></p> </td>
                <td width="120" align="right"><? echo number_format($order_qnty,0);; ?></td>
                <td width="90" align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
                <td width="90" align="center"><? echo change_date_format($row[csf('po_received_date')]); ?></td>
                <td width="90" align="center"><? echo date('d-m-Y', strtotime($row[csf('insert_date')])); ?></td>
				<td><? echo $shipment_status[$row[csf('shiping_status')]]; ?></td>
            </tr>
		<?
        $i++;
        }
        ?>
        <tfoot>
            <th>Total</th>
        	<th><? echo number_format($total_order_qnty,2);?></th>
            <th></th>
         	<th></th>
          	<th></th>
            <th></th>
        </tfoot>
    </table>
</fieldset>
</div>
<?
exit();
}

if($action=="yarn_req")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?>
<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');

		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
	}

</script>
	<div style="width:850px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:845px; margin-left:10px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="810" cellpadding="0" cellspacing="0">
            	<thead>
					<th colspan="8"><b>Required Qnty Info</b></th>
				</thead>
				<thead>
                    <th width="40">SL</th>
                    <th width="120">Order No.</th>
                    <th width="120">Buyer Name</th>
                    <th width="90">Cons/Dzn</th>
                    <th width="110">Order Qnty</th>
                    <th width="110">Plan Cut Qnty</th>
                    <th width="110">Required Qnty</th>
                    <th>Shipment Date</th>
                </thead>
             </table>
             <div style="width:830px; max-height:320px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="810" cellpadding="0" cellspacing="0">
                    <?

					if($yarn_count!=0) $yarn_count_cond="and c.count_id='$yarn_count'";else $yarn_count_cond="";
					if($yarn_comp_type1st!=0) $yarn_comp_type1st_cond="and c.copm_one_id='$yarn_comp_type1st'";else $yarn_comp_type1st_cond="";
					if($yarn_comp_percent1st!=0 || $yarn_comp_percent1st!='') $yarn_comp_percent1st_cond="and c.percent_one='$yarn_comp_percent1st'";else $yarn_comp_percent1st_cond="";
					if($yarn_comp_type2nd!=0 || $yarn_comp_type2nd!="") $yarn_comp_type2nd_cond="and c.copm_two_id='$yarn_comp_type2nd'";else $yarn_comp_type2nd_cond="";
					if($yarn_type_id!=0 ) $yarn_type_id_cond="and c.type_id='$yarn_type_id'";else $yarn_type_id_cond="";

                    $i=1; $tot_req_qnty=0;
					 $sql="select a.buyer_name, a.job_no, a.total_set_qnty as ratio, b.po_number, b.po_quantity, b.pub_shipment_date, b.plan_cut, sum(c.cons_qnty) as qnty from wo_po_details_master a, wo_po_break_down b, wo_pre_cost_fab_yarn_cost_dtls c where a.job_no=b.job_no_mst and a.job_no=c.job_no and b.id in($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $yarn_count_cond $yarn_comp_type1st_cond $yarn_comp_percent1st_cond $yarn_comp_type2nd_cond  $yarn_comp_type2nd_cond group by b.id, a.buyer_name, a.job_no, a.total_set_qnty, b.po_number, b.po_quantity, b.pub_shipment_date, b.plan_cut";
                    $result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$dzn_qnty=0; $required_qnty=0; $order_qnty=0;
						if($costing_per_id_library[$row[csf('job_no')]]==1) $dzn_qnty=12;
						else if($costing_per_id_library[$row[csf('job_no')]]==3) $dzn_qnty=12*2;
						else if($costing_per_id_library[$row[csf('job_no')]]==4) $dzn_qnty=12*3;
						else if($costing_per_id_library[$row[csf('job_no')]]==5) $dzn_qnty=12*4;
						else $dzn_qnty=1;

						$order_qnty=$row[csf('po_quantity')]*$row[csf('ratio')];
						$plan_cut_qnty=$row[csf('plan_cut')];
						$required_qnty=$plan_cut_qnty*($row[csf('qnty')]/$dzn_qnty);
                        $tot_req_qnty+=$required_qnty;
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="40"><? echo $i; ?></td>
                            <td width="120"><p><? echo $row[csf('po_number')]; ?></p></td>
                            <td width="120"><p><? echo $buyer_short_name_library[$row[csf('buyer_name')]]; ?></p></td>
                            <td width="90" align="right"><p><? echo number_format($row[csf('qnty')],2); ?></p></td>
                            <td width="110" align="right"><p><? echo number_format($order_qnty,0); ?></p></td>
                            <td width="110" align="right"><p><? echo number_format($plan_cut_qnty,0); ?></p></td>
                            <td width="110" align="right"><p><? echo number_format($required_qnty,2); ?></p></td>
                            <td align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tfoot>
                        <th align="right" colspan="6">Total</th>
                        <th align="right"><? echo number_format($tot_req_qnty,2); ?></th>
                        <th>&nbsp;</th>
                    </tfoot>
                </table>
            </div>
        </div>
	</fieldset>
<?
exit();
}

if($action=="yarn_issue")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$brand_array=return_library_array( "select id, brand_name from lib_brand", "id", "brand_name"  );

	$sqlWO="select a.fabric_source,b.po_break_down_id,a.booking_no from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.po_break_down_id in ($order_id)";
	 $resultWo=sql_select($sqlWO);
	 $fab_source_ids="";
	foreach($resultWo as $woRow)
	{
		$fab_source_ids.=$woRow[csf('fabric_source')].',';
	}
	$fab_source=rtrim($fab_source_ids,',');
	$fab_source_id=array_unique(explode(",",$fab_source));
	?>
	<script>

		function print_window()
		{
			//document.getElementById('scroll_body').style.overflow="auto";
			//document.getElementById('scroll_body').style.maxHeight="none";

			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');

			d.close();
			//document.getElementById('scroll_body').style.overflowY="scroll";
			//document.getElementById('scroll_body').style.maxHeight="230px";
		}
	</script>
	<div style="width:870px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:865px; margin-left:3px">
		<div id="report_container">
        	<table border="1" class="rpt_table" rules="all" width="300" cellpadding="0" cellspacing="0" align="center">
            	<thead>
                	<tr>
						<th colspan="3"><b>TNA Details</b></th>
                    </tr>
                    <tr>
                    	<th>Date Type</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                    </tr>
				</thead>
                <tbody>
                <? $yarn_tna="select task_start_date, task_finish_date, actual_start_date, actual_finish_date from tna_process_mst where task_number=50 and po_number_id in ($order_id) and is_deleted=0 and status_active=1";

				$tna_sql=sql_select($yarn_tna);
				?>
                    <tr bgcolor="#E9F3FF">
                    	<td>Plan</td>
                        <td><? if($tna_sql[0][csf('task_start_date')]=="" || $tna_sql[0][csf('task_start_date')]=='0000-00-00') echo ""; else echo change_date_format($tna_sql[0][csf('task_start_date')]); ?></td>
                        <td><? if($tna_sql[0][csf('task_finish_date')]=="" || $tna_sql[0][csf('task_finish_date')]=='0000-00-00') echo ""; else echo change_date_format($tna_sql[0][csf('task_finish_date')]); ?></td>
					</tr>
                	<tr bgcolor="#FFFFFF">
                    	<td>Actual</td>
                        <td><? if($tna_sql[0][csf('actual_start_date')]=="" || $tna_sql[0][csf('actual_start_date')]=='0000-00-00') echo ""; else echo change_date_format($tna_sql[0][csf('actual_start_date')]); ?></td>
                        <td><? if($tna_sql[0][csf('actual_finish_date')]=="" || $tna_sql[0][csf('actual_finish_date')]=='0000-00-00') echo ""; else echo change_date_format($tna_sql[0][csf('actual_finish_date')]); ?></td>
					</tr>
                </tbody>
            </table>
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
				if($yarn_comp_type2nd!="") $yarn_comp_type2nd_cond="and c.yarn_comp_type2nd='$yarn_comp_type2nd'"; else $yarn_comp_type2nd_cond="";
				if($yarn_comp_percent2nd!="") $yarn_comp_percent2nd_cond="and c.yarn_comp_percent2nd='$yarn_comp_percent2nd'"; else $yarn_comp_percent2nd_cond="";
				$sql="SELECT a.id as issue_id, a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company, a.booking_no, sum(b.quantity) as issue_qnty, c.lot, c.yarn_type, c.id as prod_id, c.product_name_details, d.brand_id, a.issue_basis, d.requisition_no from inv_issue_master a, order_wise_pro_details b, product_details_master c, inv_transaction d
				where a.id=d.mst_id and d.transaction_type=2 and d.item_category=1 and c.item_category_id=1 and d.id=b.trans_id and b.trans_type=2 and b.entry_form=3 and b.po_breakdown_id in ($order_id) and b.prod_id=c.id and c.yarn_count_id='$yarn_count' and c.yarn_comp_type1st='$yarn_comp_type1st' and c.yarn_comp_percent1st='$yarn_comp_percent1st' $yarn_comp_type2nd_cond $yarn_comp_percent2nd_cond and c.yarn_type='$yarn_type_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose  in (1,4)
				group by a.id, c.id, a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company, a.booking_no, c.lot, c.yarn_type, c.product_name_details, d.brand_id, a.issue_basis, d.requisition_no order by a.issue_date ASC";
                $result=sql_select($sql);
                foreach($result as $row)
				{
					if($row[csf('issue_basis')] == 3){
						$requisition_no_arr[$row[csf("requisition_no")]] = $row[csf("requisition_no")];
					}
					$issue_id_arr[$row[csf("issue_id")]] = $row[csf("issue_id")];
				}
				$requisition_no_arr = array_filter($requisition_no_arr);

				if(!empty($requisition_no_arr))
				{
					$requ_booking_no_arr = return_library_array("select a.requisition_no, c.booking_no from ppl_yarn_requisition_entry a, ppl_planning_info_entry_dtls b, ppl_planning_info_entry_mst c where a.knit_id = b.id and b.mst_id = c.id and a.status_active=1 and a.requisition_no in (".implode(",", $requisition_no_arr).") group by a.requisition_no, c.booking_no","requisition_no","booking_no");
				}

				foreach($result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

					$issue_to="";
					if($row[csf('knit_dye_source')]==1) $issue_to=$company_library[$row[csf('knit_dye_company')]];
					else $issue_to=$supplier_details[$row[csf('knit_dye_company')]];

                   foreach($fab_source_id as $fsid)
					{
						if($fsid==1)
						{
						 	$yarn_issued=$row[csf('issue_qnty')];
						}

					}
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="105"><p><? echo $row[csf('issue_number')]; ?></p></td>
                        <td width="90"><p><? echo $issue_to; ?></p></td>
                        <td width="105">
                        	<p>
                        	<?
                        		if($row[csf('issue_basis')] == 3){
									echo $requ_booking_no_arr[$row[csf("requisition_no")]];
								}
								else if($row[csf('issue_basis')] == 1)
								{
									echo $row[csf('booking_no')];
								}

                        	?>
                        	&nbsp;
                        	</p>
                        </td>
                        <td width="80"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
                        <td width="70"><p><? echo $brand_array[$row[csf('brand_id')]]; ?>&nbsp;</p></td>
                        <td width="60"><p><? echo $row[csf('lot')]; ?></p></td>
                        <td width="75" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
                        <td width="80"><p><? echo $yarn_type[$row[csf('yarn_type')]]; ?></p></td>
                        <td align="right" width="90">
							<?
								if($row[csf('knit_dye_source')]!=3)
								{
									echo number_format($yarn_issued,2);
									$total_yarn_issue_qnty+=$yarn_issued;
								}
								else echo "&nbsp;";
                            ?>
                        </td>
                        <td align="right">
							<?
								if($row[csf('knit_dye_source')]==3)
								{
									echo number_format($yarn_issued,2);
									$total_yarn_issue_qnty_out+=$yarn_issued;
								}
								else echo "&nbsp;";
                            ?>
                        </td>
                    </tr>
                <?
                $total_issue = $total_yarn_issue_qnty+$total_yarn_issue_qnty_out;
                $i++;
                }
				unset($result);
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
                    <td align="right"><? echo number_format($total_yarn_issue_qnty,2);?></td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty_out,2);?></td>
                </tr>
                <tr style="font-weight:bold">
                    <td align="right" colspan="9">Issue Total</td>
                    <td align="right"><? echo number_format($total_issue,2);?></td>
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
				if($yarn_comp_type2nd!="") $yarn_comp_type2nd_cond="and c.yarn_comp_type2nd='$yarn_comp_type2nd'"; else $yarn_comp_type2nd_cond="";
				if($yarn_comp_percent2nd!="") $yarn_comp_percent2nd_cond="and c.yarn_comp_percent2nd='$yarn_comp_percent2nd'"; else $yarn_comp_percent2nd_cond="";

				if(!empty($issue_id_arr))
				{
					$issue_id_cond = " and a.issue_id in (".implode(',', $issue_id_arr).") ";
				}
                $sql="SELECT a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no, sum(b.quantity) as returned_qnty, sum(b.reject_qty) as reject_qty, c.lot, c.yarn_type, c.id as prod_id, c.product_name_details, d.brand_id, a.receive_basis
                from inv_receive_master a, order_wise_pro_details b, product_details_master c, inv_transaction d
                where a.id=d.mst_id and d.transaction_type=4 and c.item_category_id=1 and d.item_category=1 and d.id=b.trans_id and b.trans_type=4 and b.entry_form=9 and b.po_breakdown_id in ($order_id) and b.prod_id=c.id and c.yarn_count_id='$yarn_count' and c.yarn_comp_type1st='$yarn_comp_type1st' and c.yarn_comp_percent1st='$yarn_comp_percent1st' $yarn_comp_type2nd_cond $yarn_comp_percent2nd_cond and c.yarn_type='$yarn_type_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose  in (1,4) $issue_id_cond
                group by a.id, c.id, a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no, c.lot, c.yarn_type, c.product_name_details, d.brand_id, a.receive_basis order by a.receive_date ASC";
                // echo $sql;
                $result=sql_select($sql);
				foreach($result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

					$return_from="";
					if($row[csf('knitting_source')]==1) $return_from=$company_library[$row[csf('knitting_company')]];
					else $return_from=$supplier_details[$row[csf('knitting_company')]];

                    $yarn_returned=$row[csf('returned_qnty')];
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="105"><p><? echo $row[csf('recv_number')]; ?></p></td>
                        <td width="90"><p><? echo $return_from; ?></p></td>
                        <td width="105">
                        	<p>
                        	<?
                        		if($row[csf('receive_basis')] == 3)
                        		{
									echo $requ_booking_no_arr[$row[csf("booking_no")]];
								}
								else if($row[csf('receive_basis')] == 1)
								{
									echo $row[csf('booking_no')];
								}
                        	?>
                        	&nbsp;
                        	</p>
                        </td>
                        <td width="80"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
                        <td width="70"><p><? echo $brand_array[$row[csf('brand_id')]]; ?>&nbsp;</p></td>
                        <td width="60"><p><? echo $row[csf('lot')]; ?></p></td>
                        <td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                        <td width="80"><p><? echo $yarn_type[$row[csf('yarn_type')]]; ?></p></td>
                        <td align="right" width="90">
							<?
								if($row[csf('knitting_source')]!=3)
								{
									echo number_format($yarn_returned,2);
									$total_yarn_return_qnty+=$yarn_returned;
								}
								else echo "&nbsp;";
                            ?>
                        </td>
                        <td align="right">
							<?
								if($row[csf('knitting_source')]==3)
								{
									echo number_format($yarn_returned,2);
									$total_yarn_return_qnty_out+=$yarn_returned;
								}
								else echo "&nbsp;";
                            ?>
                        </td>
                    </tr>
	                <?
	                $return_qnty = $total_yarn_return_qnty+$total_yarn_return_qnty_out;
	                $i++;
                }
				unset($result);
                $total_balence = $total_issue-$return_qnty;
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
                    <td align="right"><? echo number_format($total_yarn_return_qnty,2);?></td>
                    <td align="right"><? echo number_format($total_yarn_return_qnty_out,2);?></td>
                </tr>
                <tr style="font-weight:bold">
                    <td align="right" colspan="9">Return Total</td>
                    <td align="right"><? echo number_format($return_qnty,2);?></td>
                </tr>

                <thead>
                    <th colspan="10"><b>Yarn Reject Return</b></th>
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
                $total_yarn_reject_return_qnty=0; $total_yarn_reject_return_qnty_out=0;
                // echo $sql;
                $result=sql_select($sql);
				foreach($result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

					$return_from="";
					if($row[csf('knitting_source')]==1) $return_from=$company_library[$row[csf('knitting_company')]];
					else $return_from=$supplier_details[$row[csf('knitting_company')]];

                    $yarn_reject_returned=$row[csf('reject_qty')];
                    if ($yarn_reject_returned>0)
                    {
	                    ?>
	                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
	                    	<td width="105"><p><? echo $row[csf('recv_number')]; ?></p></td>
	                        <td width="90"><p><? echo $return_from; ?></p></td>
	                        <td width="105">
	                        	<p>
	                        	<?
	                        		if($row[csf('receive_basis')] == 3)
	                        		{
										echo $requ_booking_no_arr[$row[csf("booking_no")]];
									}
									else if($row[csf('receive_basis')] == 1)
									{
										echo $row[csf('booking_no')];
									}
	                        	?>
	                        	&nbsp;
	                        	</p>
	                        </td>
	                        <td width="80"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
	                        <td width="70"><p><? echo $brand_array[$row[csf('brand_id')]]; ?>&nbsp;</p></td>
	                        <td width="60"><p><? echo $row[csf('lot')]; ?></p></td>
	                        <td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
	                        <td width="80"><p><? echo $yarn_type[$row[csf('yarn_type')]]; ?></p></td>
	                        <td align="right" width="90">
								<?
									if($row[csf('knitting_source')]!=3)
									{
										echo number_format($yarn_reject_returned,2);
										$total_yarn_reject_return_qnty+=$yarn_reject_returned;
									}
									else echo "&nbsp;";
	                            ?>
	                        </td>
	                        <td align="right">
								<?
									if($row[csf('knitting_source')]==3)
									{
										echo number_format($yarn_reject_returned,2);
										$total_yarn_reject_return_qnty_out+=$yarn_reject_returned;
									}
									else echo "&nbsp;";
	                            ?>
	                        </td>
	                    </tr>
		                <?
		                $reject_return_qnty = $total_yarn_reject_return_qnty+$total_yarn_reject_return_qnty_out;
		                $i++;
	            	}
                }
				unset($result);
				$net_yarn_issue_in=$total_yarn_issue_qnty-$total_yarn_return_qnty;
				$net_yarn_issue_out=$total_yarn_issue_qnty_out-$total_yarn_return_qnty_out;
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
                    <td align="right"><? echo number_format($total_yarn_reject_return_qnty,2);?></td>
                    <td align="right"><? echo number_format($total_yarn_reject_return_qnty_out,2);?></td>
                </tr>
                <tr style="font-weight:bold">
                    <td align="right" colspan="9">Reject Return Total :</td>
                    <td align="right"><? echo number_format($reject_return_qnty,2);?></td>
                </tr>
                <tr style="font-weight:bold">
                    <td align="right" colspan="8">Net Yarn Issue (Without Reject Qty.) :</td>
                    <th align="right"><? echo number_format($net_yarn_issue_in,2);?></td>
                    <td align="right"><? echo number_format($net_yarn_issue_out,2);?></td>
                </tr>

                <tfoot>
                    <tr>
                        <th align="right" colspan="9">Total Balance</th>
                        <th align="right"><? echo number_format($total_balence,2);?></th>
                    </tr>
                </tfoot>
            </table>
		</div>
	</fieldset>
<?
exit();
}


if ($action=="yarn_issue_not")
{
    echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
    extract($_REQUEST);
    $brand_array = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
    $yarn_desc_array = explode(",", $yarn_count);
  //  print_r($yarn_desc_array);die;
    ?>
    <script>

        function print_window()
        {

            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
                    '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

            d.close();
        }
    </script>
    <div style="width:970px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
    <fieldset style="width:970px; margin-left:3px">
        <div id="report_container">
        <table border="1" class="rpt_table" rules="all" width="300" cellpadding="0" cellspacing="0" align="center">
            	<thead>
                	<tr>
						<th colspan="3"><b>TNA Details</b></th>
                    </tr>
                    <tr>
                    	<th>Date Type</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                    </tr>
				</thead>
                <tbody>
                <? $yarn_tna="select task_start_date, task_finish_date, actual_start_date, actual_finish_date from tna_process_mst where task_number=50 and po_number_id in ($order_id) and is_deleted=0 and status_active=1";

				$tna_sql=sql_select($yarn_tna);
				?>
                    <tr bgcolor="#E9F3FF">
                    	<td>Plan</td>
                        <td><? if($tna_sql[0][csf('task_start_date')]=="" || $tna_sql[0][csf('task_start_date')]=='0000-00-00') echo ""; else echo change_date_format($tna_sql[0][csf('task_start_date')]); ?></td>
                        <td><? if($tna_sql[0][csf('task_finish_date')]=="" || $tna_sql[0][csf('task_finish_date')]=='0000-00-00') echo ""; else echo change_date_format($tna_sql[0][csf('task_finish_date')]); ?></td>
					</tr>
                	<tr bgcolor="#FFFFFF">
                    	<td>Actual</td>
                        <td><? if($tna_sql[0][csf('actual_start_date')]=="" || $tna_sql[0][csf('actual_start_date')]=='0000-00-00') echo ""; else echo change_date_format($tna_sql[0][csf('actual_start_date')]); ?></td>
                        <td><? if($tna_sql[0][csf('actual_finish_date')]=="" || $tna_sql[0][csf('actual_finish_date')]=='0000-00-00') echo ""; else echo change_date_format($tna_sql[0][csf('actual_finish_date')]); ?></td>
					</tr>
                </tbody>
            </table>

            <table border="1" class="rpt_table" rules="all" width="960" cellpadding="0" cellspacing="0">
                <thead>
                	<th colspan="10"><b>Yarn Issue</b></th>
                </thead>
                <thead>
	                <th width="105">Issue Id</th>
	                <th width="90">Issue To</th>
	                <th width="105">Booking No</th>
	                <th width="70">Challan No</th>
	                <th width="75">Issue Date</th>
	                <th width="70">Brand</th>
	                <th width="60">Lot No</th>
	                <th width="180">Yarn Description</th>
	                <th width="90">Issue Qnty (In)</th>
	                <th>Issue Qnty (Out)</th>
                </thead>
                <?
                $i = 1;
                $total_yarn_issue_qnty = 0;
                $total_yarn_issue_qnty_out = 0;
                $yarn_desc_array_for_return = array();
				 $sql = "SELECT a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company, a.booking_no, (b.quantity) as issue_qnty, c.lot, c.yarn_type, c.id as prod_id,c.yarn_count_id,c.yarn_comp_type1st,c.yarn_comp_percent1st,c.yarn_comp_type2nd,c.yarn_comp_percent2nd,c.yarn_type, c.product_name_details, c.brand as brand_id, d.requisition_no
                        from inv_issue_master a, order_wise_pro_details b, product_details_master c, inv_transaction d
                        where a.id=d.mst_id and d.transaction_type=2 and d.item_category=1 and d.id=b.trans_id and b.trans_type=2 and b.entry_form=3 and b.po_breakdown_id in ($order_id) and b.prod_id=c.id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose in (1,4)  and b.po_breakdown_id in ($order_id) order by a.issue_date ASC  ";//group by a.id, c.id, a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company, a.booking_no, c.lot, c.yarn_type, c.product_name_details, c.brand, d.requisition_no,c.yarn_count_id,c.yarn_comp_type1st,c.yarn_comp_percent1st,c.yarn_comp_type2nd,c.yarn_comp_percent2nd,c.yarn_type
                        $result = sql_select($sql);

						foreach( $result as $row)
						{
							   if ($row[csf('yarn_comp_percent2nd')] != 0) {
									$compostion_not_req = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row_yarown_iss[csf('yarn_comp_percent1st')] . " %" . " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')] . " %";
								} else {
									$compostion_not_req = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . " %" . " " . $composition[$row[csf('yarn_comp_type2nd')]];
								}
							   $desc = $yarn_count_details[$row[csf('yarn_count_id')]] . " " . $compostion_not_req . " " . $yarn_type[$row[csf('yarn_type')]];

							 if (!in_array($desc, $yarn_desc_array))
							 {
							$yarn_issue_arr[$row[csf('issue_number')]][$row[csf('yarn_count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_comp_percent1st')]][$row[csf('yarn_comp_type2nd')]][$row[csf('yarn_comp_percent2nd')]][$row[csf('yarn_type')]]['issue_number']=$row[csf('issue_number')];
							$yarn_issue_arr[$row[csf('issue_number')]][$row[csf('yarn_count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_comp_percent1st')]][$row[csf('yarn_comp_type2nd')]][$row[csf('yarn_comp_percent2nd')]][$row[csf('yarn_type')]]['issue_date']=$row[csf('issue_date')];
							$yarn_issue_arr[$row[csf('issue_number')]][$row[csf('yarn_count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_comp_percent1st')]][$row[csf('yarn_comp_type2nd')]][$row[csf('yarn_comp_percent2nd')]][$row[csf('yarn_type')]]['challan_no']=$row[csf('challan_no')];
							$yarn_issue_arr[$row[csf('issue_number')]][$row[csf('yarn_count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_comp_percent1st')]][$row[csf('yarn_comp_type2nd')]][$row[csf('yarn_comp_percent2nd')]][$row[csf('yarn_type')]]['knit_dye_source']=$row[csf('knit_dye_source')];
							$yarn_issue_arr[$row[csf('issue_number')]][$row[csf('yarn_count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_comp_percent1st')]][$row[csf('yarn_comp_type2nd')]][$row[csf('yarn_comp_percent2nd')]][$row[csf('yarn_type')]]['knit_dye_company']=$row[csf('knit_dye_company')];
							$yarn_issue_arr[$row[csf('issue_number')]][$row[csf('yarn_count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_comp_percent1st')]][$row[csf('yarn_comp_type2nd')]][$row[csf('yarn_comp_percent2nd')]][$row[csf('yarn_type')]]['booking_no']=$row[csf('booking_no')];
							$yarn_issue_arr[$row[csf('issue_number')]][$row[csf('yarn_count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_comp_percent1st')]][$row[csf('yarn_comp_type2nd')]][$row[csf('yarn_comp_percent2nd')]][$row[csf('yarn_type')]]['knit_dye_company']=$row[csf('knit_dye_company')];
							$yarn_issue_arr[$row[csf('issue_number')]][$row[csf('yarn_count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_comp_percent1st')]][$row[csf('yarn_comp_type2nd')]][$row[csf('yarn_comp_percent2nd')]][$row[csf('yarn_type')]]['issue_qnty']+=$row[csf('issue_qnty')];
							$yarn_issue_arr[$row[csf('issue_number')]][$row[csf('yarn_count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_comp_percent1st')]][$row[csf('yarn_comp_type2nd')]][$row[csf('yarn_comp_percent2nd')]][$row[csf('yarn_type')]]['product_name_details']=$row[csf('product_name_details')];
							$yarn_issue_arr[$row[csf('issue_number')]][$row[csf('yarn_count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_comp_percent1st')]][$row[csf('yarn_comp_type2nd')]][$row[csf('yarn_comp_percent2nd')]][$row[csf('yarn_type')]]['brand_id']=$row[csf('brand_id')];
							$yarn_issue_arr[$row[csf('issue_number')]][$row[csf('yarn_count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_comp_percent1st')]][$row[csf('yarn_comp_type2nd')]][$row[csf('yarn_comp_percent2nd')]][$row[csf('yarn_type')]]['lot']=$row[csf('lot')];
							$yarn_issue_arr[$row[csf('issue_number')]][$row[csf('yarn_count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_comp_percent1st')]][$row[csf('yarn_comp_type2nd')]][$row[csf('yarn_comp_percent2nd')]][$row[csf('yarn_type')]]['requisition_no']=$row[csf('requisition_no')];
							 }
						//	$yarn_issue_arr[$row[csf('yarn_count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_comp_percent1st')]][$row[csf('yarn_comp_type2nd')]][$row[csf('yarn_comp_percent2nd')]][$row[csf('yarn_type')]]['booking_no']=$row[csf('booking_no')];
						}

               /*  $sql_yarn_iss = "select b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type from order_wise_pro_details a, product_details_master b where a.prod_id=b.id and a.po_breakdown_id in ($order_id) and a.entry_form in(3,9) and b.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and a.issue_purpose in (1,4) group by b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type";


                $dataArrayIssue = sql_select($sql_yarn_iss);*/
               foreach ($yarn_issue_arr as $issue_no=>$issue_data)
                {


                    //if (!in_array($desc, $yarn_desc_array))
                   // {
                        /*$sql = "SELECT a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company, a.booking_no, sum(b.quantity) as issue_qnty, c.lot, c.yarn_type, c.id as prod_id, c.product_name_details, c.brand as brand_id, d.requisition_no
                        from inv_issue_master a, order_wise_pro_details b, product_details_master c, inv_transaction d
                        where a.id=d.mst_id and d.transaction_type=2 and d.item_category=1 and d.id=b.trans_id and b.trans_type=2 and b.entry_form=3 and b.po_breakdown_id in ($order_id) and b.prod_id=c.id and c.yarn_count_id='" . $row_yarn_iss[csf('yarn_count_id')] . "' and c.yarn_comp_type1st='" . $row_yarn_iss[csf('yarn_comp_type1st')] . "' and c.yarn_comp_percent1st='" . $row_yarn_iss[csf('yarn_comp_percent1st')] . "' and c.yarn_comp_type2nd='" . $row_yarn_iss[csf('yarn_comp_type2nd')] . "' and c.yarn_comp_percent2nd='" . $row_yarn_iss[csf('yarn_comp_percent2nd')] . "' and c.yarn_type='" . $row_yarn_iss[csf('yarn_type')] . "' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose in (1,4) group by a.id, c.id, a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company, a.booking_no, c.lot, c.yarn_type, c.product_name_details, c.brand, d.requisition_no";
                        $result = sql_select($sql);*/



					    foreach ($issue_data as $yarn_count=>$yarn_count_data)
						{
							 foreach ($yarn_count_data as $yarn_comptype1st=>$yarn_comptype1st_data)
							{
								foreach ($yarn_comptype1st_data as $yarn_comp_percent1st=>$yarn_comp_percent1st_data)
								{
								 foreach ($yarn_comp_percent1st_data as $yarn_comp_type2nd=>$yarn_comp_type2nd_data)
								{
								 foreach ($yarn_comp_type2nd_data as $yarn_comp_percent2nd=>$yarn_comp_percent2nd_data)
								{
								foreach ($yarn_comp_percent2nd_data as $yarn_typeId=>$row)
								{
                            	if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
								 if ($yarn_comp_percent2nd != 0) {

								$compostion_not_req = $composition[$yarn_comptype1st] . " " . $yarn_comp_percent1st . " %" . " " . $composition[$yarn_comp_type2nd] . " " . $yarn_comp_percent2nd . " %";
							} else {
								$compostion_not_req = $composition[$yarn_comptype1st] . " " . $yarn_comp_percent1st . " %" . " " . $composition[$yarn_comp_type2nd];
							}

							$desc = $yarn_count_details[$yarn_count] . " " . $compostion_not_req . " " . $yarn_type[$yarn_typeId];

							$yarn_desc_for_return = $yarn_count . "__" . $yarn_comptype1st . "__" . $yarn_comp_percent1st . "__" . $yarn_comp_type2nd . "__" . $yarn_comp_percent2nd . "__" . $yarn_typeId;

							$yarn_desc_array_for_return[$desc] = $yarn_desc_for_return;


                            if ($row[('knit_dye_source')] == 1) $issue_to = $company_library[$row[('knit_dye_company')]]; else if ($row[('knit_dye_source')] == 3)  $issue_to = $supplier_details[$row[('knit_dye_company')]]; else  $issue_to = "&nbsp;";

							if($row[('booking_no')]=="") $row[('booking_no')]=$row[('requisition_no')];

                            $yarn_issued = $row[('issue_qnty')];
							 $issue_number =rtrim($row[('issue_number')],',');
							$issue_numbers=implode(",",array_unique(explode(",",$issue_number)));
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                <td width="105"><p><? echo $issue_numbers; ?></p></td>
                                <td width="90"><p><? echo $issue_to; ?></p></td>
                                <td width="105"><p><? echo $row[('booking_no')]; ?>&nbsp;</p></td>
                                <td width="70"><p><? echo $row[('challan_no')]; ?>&nbsp;</p></td>
                                <td width="75" align="center"><? echo change_date_format($row[('issue_date')]); ?></td>
                                <td width="70"><p><? echo $brand_array[$row[('brand_id')]]; ?>&nbsp;</p></td>
                                <td width="60"><p><? echo $row[('lot')]; ?></p></td>
                                <td width="180"><p><? echo $row[('product_name_details')]; ?></p></td>
                                <td align="right" width="90">
                                    <?
                                    if ($row[('knit_dye_source')] != 3) {
                                        echo number_format($yarn_issued, 2, '.', '');
                                        $total_yarn_issue_qnty += $yarn_issued;
                                    } else
                                        echo "&nbsp;";
                                    ?>
                                </td>
                                <td align="right">
                                    <?
                                    if ($row[('knit_dye_source')] == 3) {
                                        echo number_format($yarn_issued, 2, '.', '');
                                        $total_yarn_issue_qnty_out += $yarn_issued;
                                    } else
                                        echo "&nbsp;";
                                    ?>
                                </td>
                            </tr>
                            <?
                            $total_issue = $total_yarn_issue_qnty+$total_yarn_issue_qnty_out;
                            $i++;
									}
								  }
								 }
								}
							}
                        } //chk end
                    }
               // }
			//print_r($yarn_desc_array_for_return);
                ?>
                <tr style="font-weight:bold">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right">Total</td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty, 2, '.', ''); ?></td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty_out, 2, '.', ''); ?></td>
                </tr>
                <tr style="font-weight:bold">
                    <td align="right" colspan="9">Issue Total</td>
                    <td align="right"><? echo number_format($total_issue, 2, '.', ''); ?></td>
                </tr>
                <?
                	//die;
				?>
                <thead>
                	<th colspan="10"><b>Yarn Return</b></th>
                </thead>
                <thead>
	                <th width="105">Return Id</th>
	                <th width="90">Return From</th>
	                <th width="105">Booking No</th>
	                <th width="70">Challan No</th>
	                <th width="75">Return Date</th>
	                <th width="70">Brand</th>
	                <th width="60">Lot No</th>
	                <th width="180">Yarn Description</th>
	                <th width="90">Return Qnty (In)</th>
	                <th>Return Qnty (Out)</th>
                </thead>
                <?
				 $sql_ret = "SELECT a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no, (b.quantity) as returned_qnty, c.lot, c.yarn_type, c.id as prod_id, c.product_name_details, c.brand as brand_id ,c.yarn_count_id,c.yarn_comp_type1st,c.yarn_comp_percent1st,c.yarn_comp_type2nd,c.yarn_comp_percent2nd
                        from inv_receive_master a, order_wise_pro_details b, product_details_master c, inv_transaction d
                        where a.id=d.mst_id and d.transaction_type=4 and d.item_category=1 and d.id=b.trans_id and b.trans_type=4 and b.entry_form=9 and b.po_breakdown_id in ($order_id) and b.prod_id=c.id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose in (1,4) order by a.receive_date, a.recv_number ASC"; // group by a.id, c.id, a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no, c.lot, c.yarn_type, c.product_name_details, c.brand
						 $result_ret = sql_select($sql_ret);
                        foreach ($result_ret as $row)
						{
							if ($row[csf('yarn_comp_percent2nd')] != 0) {
									$compostion_not_req = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row_yarown_iss[csf('yarn_comp_percent1st')] . " %" . " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')] . " %";
								} else {
									$compostion_not_req = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . " %" . " " . $composition[$row[csf('yarn_comp_type2nd')]];
								}
							   $desc = $yarn_count_details[$row[csf('yarn_count_id')]] . " " . $compostion_not_req . " " . $yarn_type[$row[csf('yarn_type')]];

							 if (!in_array($desc, $yarn_desc_array))
							 {
							$yarn_issue_reurn_arr[$row[csf('recv_number')]][$row[csf('yarn_count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_comp_percent1st')]][$row[csf('yarn_comp_type2nd')]][$row[csf('yarn_comp_percent2nd')]][$row[csf('yarn_type')]]['recv_number']=$row[csf('recv_number')];
							$yarn_issue_reurn_arr[$row[csf('recv_number')]][$row[csf('yarn_count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_comp_percent1st')]][$row[csf('yarn_comp_type2nd')]][$row[csf('yarn_comp_percent2nd')]][$row[csf('yarn_type')]]['knitting_company']=$row[csf('knitting_company')];
							$yarn_issue_reurn_arr[$row[csf('recv_number')]][$row[csf('yarn_count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_comp_percent1st')]][$row[csf('yarn_comp_type2nd')]][$row[csf('yarn_comp_percent2nd')]][$row[csf('yarn_type')]]['knitting_source']=$row[csf('knitting_source')];
							$yarn_issue_reurn_arr[$row[csf('recv_number')]][$row[csf('yarn_count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_comp_percent1st')]][$row[csf('yarn_comp_type2nd')]][$row[csf('yarn_comp_percent2nd')]][$row[csf('yarn_type')]]['challan_no']=$row[csf('challan_no')];

							$yarn_issue_reurn_arr[$row[csf('recv_number')]][$row[csf('yarn_count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_comp_percent1st')]][$row[csf('yarn_comp_type2nd')]][$row[csf('yarn_comp_percent2nd')]][$row[csf('yarn_type')]]['booking_no']=$row[csf('booking_no')];
							$yarn_issue_reurn_arr[$row[csf('recv_number')]][$row[csf('yarn_count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_comp_percent1st')]][$row[csf('yarn_comp_type2nd')]][$row[csf('yarn_comp_percent2nd')]][$row[csf('yarn_type')]]['receive_date']=$row[csf('receive_date')];
							$yarn_issue_reurn_arr[$row[csf('recv_number')]][$row[csf('yarn_count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_comp_percent1st')]][$row[csf('yarn_comp_type2nd')]][$row[csf('yarn_comp_percent2nd')]][$row[csf('yarn_type')]]['brand_id']=$row[csf('brand_id')];
							$yarn_issue_reurn_arr[$row[csf('recv_number')]][$row[csf('yarn_count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_comp_percent1st')]][$row[csf('yarn_comp_type2nd')]][$row[csf('yarn_comp_percent2nd')]][$row[csf('yarn_type')]]['lot']=$row[csf('lot')];
							$yarn_issue_reurn_arr[$row[csf('recv_number')]][$row[csf('yarn_count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_comp_percent1st')]][$row[csf('yarn_comp_type2nd')]][$row[csf('yarn_comp_percent2nd')]][$row[csf('yarn_type')]]['product_name_details']=$row[csf('product_name_details')];
							$yarn_issue_reurn_arr[$row[csf('recv_number')]][$row[csf('yarn_count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_comp_percent1st')]][$row[csf('yarn_comp_type2nd')]][$row[csf('yarn_comp_percent2nd')]][$row[csf('yarn_type')]]['returned_qnty']+=$row[csf('returned_qnty')];
						//	$yarn_issue_reurn_arr[$row[csf('recv_number')]][$row[csf('yarn_count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_comp_percent1st')]][$row[csf('yarn_comp_type2nd')]][$row[csf('yarn_comp_percent2nd')]][$row[csf('yarn_type')]]['product_name_details']=$row[csf('product_name_details')];



							 }
						}

                $total_yarn_return_qnty = 0;
                $total_yarn_return_qnty_out = 0;
                foreach ($yarn_issue_reurn_arr as $recv_no => $recv_no_data)
                {
				 foreach ($recv_no_data as $yarn_count_id => $yarn_count_id_data)
              	 {
				 foreach ($yarn_count_id_data as $yarn_comp_type1st => $yarn_comp_type1st_data)
              	 {
				 foreach ($yarn_comp_type1st_data as $yarn_comp_percent1st => $yarn_comp_percent1st_data)
              	 {
				 foreach ($yarn_comp_percent1st_data as $yarn_comp_type2nd => $yarn_comp_type2nd_data)
              	 {
				 foreach ($yarn_comp_type2nd_data as $yarn_comp_percent2nd => $yarn_comp_percent2nd_data)
              	 {
				 foreach ($yarn_comp_percent2nd_data as $yarn_type => $row)
              	 {

                    //if (!in_array($key, $yarn_desc_array))
                   //{
                       /* $desc = explode("__", $value);
                        $yarn_count = $desc[0];
                        $yarn_comp_type1st = $desc[1];
                        $yarn_comp_percent1st = $desc[2];
                        $yarn_comp_type2nd = $desc[3];
                        $yarn_comp_percent2nd = $desc[4];
                        $yarn_type_id = $desc[5];*/

                       /* $sql = "SELECT a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no, sum(b.quantity) as returned_qnty, c.lot, c.yarn_type, c.id as prod_id, c.product_name_details, c.brand as brand_id
                        from inv_receive_master a, order_wise_pro_details b, product_details_master c, inv_transaction d
                        where a.id=d.mst_id and d.transaction_type=4 and d.item_category=1 and d.id=b.trans_id and b.trans_type=4 and b.entry_form=9 and b.po_breakdown_id in ($order_id) and b.prod_id=c.id and c.yarn_count_id='$yarn_count' and c.yarn_comp_type1st='$yarn_comp_type1st' and c.yarn_comp_percent1st='$yarn_comp_percent1st' and c.yarn_comp_type2nd='$yarn_comp_type2nd' and c.yarn_comp_percent2nd='$yarn_comp_percent2nd' and c.yarn_type='$yarn_type_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose in (1,4) group by a.id, c.id, a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no, c.lot, c.yarn_type, c.product_name_details, c.brand";
                        $result = sql_select($sql);*/

                            if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

                            if ($row[('knitting_source')] == 1) $return_from = $company_library[$row[('knitting_company')]]; else if ($row[('knitting_source')] == 3) $return_from = $supplier_details[$row[('knitting_company')]]; else $return_from = "&nbsp;";

                            $yarn_returned = $row[('returned_qnty')];
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                <td width="105"><p><? echo $recv_no; ?></p></td>
                                <td width="90"><p><? echo $return_from; ?></p></td>
                                <td width="105"><p><? echo $row[('booking_no')]; ?>&nbsp;</p></td>
                                <td width="70"><p><? echo $row[('challan_no')]; ?>&nbsp;</p></td>
                                <td width="75" align="center"><? echo change_date_format($row[('receive_date')]); ?></td>
                                <td width="70"><p><? echo $brand_array[$row[('brand_id')]]; ?>&nbsp;</p></td>
                                <td width="60"><p><? echo $row[('lot')]; ?></p></td>
                                <td width="180"><p><? echo $row[('product_name_details')]; ?></p></td>
                                <td align="right" width="90">
                                    <?
                                    if ($row[('knitting_source')] != 3) {
                                        echo number_format($yarn_returned, 2, '.', '');
                                        $total_yarn_return_qnty += $yarn_returned;
                                    } else
                                        echo "&nbsp;";
                                    ?>
                                </td>
                                <td align="right">
                                    <?
                                    if ($row[('knitting_source')] == 3) {
                                        echo number_format($yarn_returned, 2, '.', '');
                                        $total_yarn_return_qnty_out += $yarn_returned;
                                    } else
                                        echo "&nbsp;";
                                    ?>
                                </td>
                            </tr>
                            <?
                            $return_qnty = $total_yarn_return_qnty+$total_yarn_return_qnty_out;
                            $i++;
                        }
                        $total_balence = $total_issue-$return_qnty;
                        $net_yarn_issue_in=$total_yarn_issue_qnty-$total_yarn_return_qnty;
						$net_yarn_issue_out=$total_yarn_issue_qnty_out-$total_yarn_return_qnty_out;
                  	   }
               		   }
				 	  }
					 }
					 }

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
                    <td align="right"><? echo number_format($total_yarn_return_qnty,2);?></td>
                    <td align="right"><? echo number_format($total_yarn_return_qnty_out,2);?></td>
                </tr>
                <tr style="font-weight:bold">
                    <td align="right" colspan="9">Return Total</td>
                    <td align="right"><? echo number_format($return_qnty,2);?></td>
                </tr>

                <thead>
                	<th colspan="10"><b>Yarn Reject Return</b></th>
                </thead>
                <thead>
	                <th width="105">Return Id</th>
	                <th width="90">Return From</th>
	                <th width="105">Booking No</th>
	                <th width="70">Challan No</th>
	                <th width="75">Return Date</th>
	                <th width="70">Brand</th>
	                <th width="60">Lot No</th>
	                <th width="180">Yarn Description</th>
	                <th width="90">Return Qnty (In)</th>
	                <th>Return Qnty (Out)</th>
                </thead>
                <?
				 $reject_sql = "select a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no, (b.reject_qty) as reject_qty, c.lot, c.yarn_type, c.id as prod_id, c.product_name_details, c.brand as brand_id,c.yarn_count_id,c.yarn_comp_type1st,c.yarn_comp_percent1st,c.yarn_comp_type2nd,c.yarn_comp_percent2nd
                        from inv_receive_master a, order_wise_pro_details b, product_details_master c, inv_transaction d
                        where a.id=d.mst_id and d.transaction_type=4 and d.item_category=1 and d.id=b.trans_id and b.trans_type=4 and b.entry_form=9 and b.po_breakdown_id in ($order_id) and b.prod_id=c.id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose in (1,4) order by a.receive_date ASC";
						// group by a.id, c.id, a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no, c.lot, c.yarn_type, c.product_name_details, c.brand
                        $result_data = sql_select($reject_sql);
						foreach ($result_data as $row)
                		{
						if ($row[csf('yarn_comp_percent2nd')] != 0) {
									$compostion_not_req = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row_yarown_iss[csf('yarn_comp_percent1st')] . " %" . " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')] . " %";
								} else {
									$compostion_not_req = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . " %" . " " . $composition[$row[csf('yarn_comp_type2nd')]];
								}
							   $desc = $yarn_count_details[$row[csf('yarn_count_id')]] . " " . $compostion_not_req . " " . $yarn_type[$row[csf('yarn_type')]];

							 if (!in_array($desc, $yarn_desc_array))
							 {
							 $yarn_reject_arr[$row[csf('recv_number')]][$row[csf('yarn_count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_comp_percent1st')]][$row[csf('yarn_comp_type2nd')]][$row[csf('yarn_comp_percent2nd')]][$row[csf('yarn_type')]]['recv_number']=$row[csf('recv_number')];
							$yarn_reject_arr[$row[csf('recv_number')]][$row[csf('yarn_count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_comp_percent1st')]][$row[csf('yarn_comp_type2nd')]][$row[csf('yarn_comp_percent2nd')]][$row[csf('yarn_type')]]['knitting_company']=$row[csf('knitting_company')];
							$yarn_reject_arr[$row[csf('recv_number')]][$row[csf('yarn_count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_comp_percent1st')]][$row[csf('yarn_comp_type2nd')]][$row[csf('yarn_comp_percent2nd')]][$row[csf('yarn_type')]]['knitting_source']=$row[csf('knitting_source')];
							$yarn_reject_arr[$row[csf('recv_number')]][$row[csf('yarn_count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_comp_percent1st')]][$row[csf('yarn_comp_type2nd')]][$row[csf('yarn_comp_percent2nd')]][$row[csf('yarn_type')]]['challan_no']=$row[csf('challan_no')];

							$yarn_reject_arr[$row[csf('recv_number')]][$row[csf('yarn_count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_comp_percent1st')]][$row[csf('yarn_comp_type2nd')]][$row[csf('yarn_comp_percent2nd')]][$row[csf('yarn_type')]]['booking_no']=$row[csf('booking_no')];
							$yarn_reject_arr[$row[csf('recv_number')]][$row[csf('yarn_count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_comp_percent1st')]][$row[csf('yarn_comp_type2nd')]][$row[csf('yarn_comp_percent2nd')]][$row[csf('yarn_type')]]['receive_date']=$row[csf('receive_date')];
							$yarn_reject_arr[$row[csf('recv_number')]][$row[csf('yarn_count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_comp_percent1st')]][$row[csf('yarn_comp_type2nd')]][$row[csf('yarn_comp_percent2nd')]][$row[csf('yarn_type')]]['brand_id']=$row[csf('brand_id')];
							$yarn_reject_arr[$row[csf('recv_number')]][$row[csf('yarn_count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_comp_percent1st')]][$row[csf('yarn_comp_type2nd')]][$row[csf('yarn_comp_percent2nd')]][$row[csf('yarn_type')]]['lot']=$row[csf('lot')];
							$yarn_reject_arr[$row[csf('recv_number')]][$row[csf('yarn_count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_comp_percent1st')]][$row[csf('yarn_comp_type2nd')]][$row[csf('yarn_comp_percent2nd')]][$row[csf('yarn_type')]]['product_name_details']=$row[csf('product_name_details')];
							$yarn_reject_arr[$row[csf('recv_number')]][$row[csf('yarn_count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_comp_percent1st')]][$row[csf('yarn_comp_type2nd')]][$row[csf('yarn_comp_percent2nd')]][$row[csf('yarn_type')]]['reject_qty']+=$row[csf('reject_qty')];

							 }
						}

                $total_yarn_reject_return_qnty = 0;
                $total_yarn_reject_return_qnty_out = 0;
                foreach ($yarn_reject_arr as $recv_no => $recv_no_data)
                {
				 foreach ($recv_no_data as $yarn_count_id => $yarn_count_id_data)
              	 {
				 foreach ($yarn_count_id_data as $yarn_comp_type1st => $yarn_comp_type1st_data)
              	 {
				 foreach ($yarn_comp_type1st_data as $yarn_comp_percent1st => $yarn_comp_percent1st_data)
              	 {
				 foreach ($yarn_comp_percent1st_data as $yarn_comp_type2nd => $yarn_comp_type2nd_data)
              	 {
				 foreach ($yarn_comp_type2nd_data as $yarn_comp_percent2nd => $yarn_comp_percent2nd_data)
              	 {
				 foreach ($yarn_comp_percent2nd_data as $yarn_type => $row)
              	 {
                       /* $desc = explode("__", $value);
                        $yarn_count = $desc[0];
                        $yarn_comp_type1st = $desc[1];
                        $yarn_comp_percent1st = $desc[2];
                        $yarn_comp_type2nd = $desc[3];
                        $yarn_comp_percent2nd = $desc[4];
                        $yarn_type_id = $desc[5];*/

                       /* $reject_sql = "SELECT a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no, sum(b.reject_qty) as reject_qty, c.lot, c.yarn_type, c.id as prod_id, c.product_name_details, c.brand as brand_id
                        from inv_receive_master a, order_wise_pro_details b, product_details_master c, inv_transaction d
                        where a.id=d.mst_id and d.transaction_type=4 and d.item_category=1 and d.id=b.trans_id and b.trans_type=4 and b.entry_form=9 and b.po_breakdown_id in ($order_id) and b.prod_id=c.id and c.yarn_count_id='$yarn_count' and c.yarn_comp_type1st='$yarn_comp_type1st' and c.yarn_comp_percent1st='$yarn_comp_percent1st' and c.yarn_comp_type2nd='$yarn_comp_type2nd' and c.yarn_comp_percent2nd='$yarn_comp_percent2nd' and c.yarn_type='$yarn_type_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose in (1,4)
                        group by a.id, c.id, a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no, c.lot, c.yarn_type, c.product_name_details, c.brand";
                        $result_data = sql_select($reject_sql);*/

                            if ($i % 2 == 0)
                                $bgcolor = "#E9F3FF";
                            else
                                $bgcolor = "#FFFFFF";

                            if ($row[('knitting_source')] == 1) {
                                $reject_return_from = $company_library[$row[('knitting_company')]];
                            } else if ($row[('knitting_source')] == 3) {
                                $reject_return_from = $supplier_details[$row[('knitting_company')]];
                            } else
                                $reject_return_from = "&nbsp;";

                            $yarn_reject_returned = $row[('reject_qty')];
                            if ($yarn_reject_returned>0)
                    		{
	                            ?>
	                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
	                                <td width="105"><p><? echo $recv_no; ?></p></td>
	                                <td width="90"><p><? echo $reject_return_from; ?></p></td>
	                                <td width="105"><p><? echo $row[('booking_no')]; ?>&nbsp;</p></td>
	                                <td width="70"><p><? echo $row[('challan_no')]; ?>&nbsp;</p></td>
	                                <td width="75" align="center"><? echo change_date_format($row[('receive_date')]); ?></td>
	                                <td width="70"><p><? echo $brand_array[$row[('brand_id')]]; ?>&nbsp;</p></td>
	                                <td width="60"><p><? echo $row[('lot')]; ?></p></td>
	                                <td width="180"><p><? echo $row[('product_name_details')]; ?></p></td>
	                                <td align="right" width="90">
	                                    <?
	                                    if ($row[('knitting_source')] != 3) {
	                                        echo number_format($yarn_reject_returned, 2, '.', '');
	                                        $total_yarn_reject_return_qnty += $yarn_reject_returned;
	                                    } else
	                                        echo "&nbsp;";
	                                    ?>
	                                </td>
	                                <td align="right">
	                                    <?
	                                    if ($row[('knitting_source')] == 3) {
	                                        echo number_format($yarn_reject_returned, 2, '.', '');
	                                        $total_yarn_reject_return_qnty_out += $yarn_reject_returned;
	                                    } else
	                                        echo "&nbsp;";
	                                    ?>
	                                </td>
	                            </tr>
	                            <?
	                            $reject_return_qnty = $total_yarn_reject_return_qnty+$total_yarn_reject_return_qnty_out;
	                            $i++;
                        	 }
				 			}
                        }
                    }
                   }
				  }
				  }
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
                    <td align="right"><? echo number_format($total_yarn_reject_return_qnty,2);?></td>
                    <td align="right"><? echo number_format($total_yarn_reject_return_qnty_out,2);?></td>
                </tr>
                <tr style="font-weight:bold">
                    <td align="right" colspan="9">Reject Total</td>
                    <td align="right"><? echo number_format($reject_return_qnty,2);?></td>
                </tr>

                <tr style="font-weight:bold">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right">Net Yarn Issue (Without Reject Qty.) :</td>
                    <td align="right"><? echo number_format($net_yarn_issue_in, 2, '.', ''); ?></td>
                    <td align="right"><? echo number_format($net_yarn_issue_out, 2, '.', ''); ?></td>
                </tr>
                <tfoot>
                    <tr>
                        <th align="right" colspan="9">Net Yarn Issue (Without Reject Qty.) :</th>
                        <th align="right"><? echo number_format($total_balence, 2); ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?
    exit();
}

if($action=="grey_receive")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$machine_arr = return_library_array("select id, machine_no from lib_machine_name","id","machine_no");

	$sqlWO="select a.fabric_source,b.po_break_down_id,a.booking_no from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.po_break_down_id in ($order_id)";
	//echo $sqlWO;
	$resultWo=sql_select($sqlWO);
	$fab_source_ids=array();
	foreach($resultWo as $woRow)
	{
		$fab_source_ids[$woRow[csf('booking_no')]][$woRow[csf('fabric_source')]]=$woRow[csf('fabric_source')];
	}

	$sqlWO="select a.fabric_source,b.po_break_down_id, d.id as program_no
	from wo_booking_mst a, wo_booking_dtls b, ppl_planning_info_entry_mst c, ppl_planning_info_entry_dtls d where a.booking_no=b.booking_no and b.booking_no=c.booking_no and c.id=d.mst_id
	and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.po_break_down_id in ($order_id) ";
	//echo $sqlWO;
	$resultWo=sql_select($sqlWO);
	//$fab_source_ids=array();
	foreach($resultWo as $woRow)
	{
		$fab_source_ids[$woRow[csf('program_no')]][$woRow[csf('fabric_source')]]=$woRow[csf('fabric_source')];
	}
	?>
	<script>

		var tableFilters = {
							   col_operation: {
							   id: ["value_receive_qnty_in","value_receive_qnty_out","value_receive_qnty_tot"],
							   col: [7,8,9],
							   operation: ["sum","sum","sum"],
							   write_method: ["innerHTML","innerHTML","innerHTML"]
							}
						}
		$(document).ready(function(e) {
			setFilterGrid('tbl_list_search',-1,tableFilters);
		});

		function print_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";

			$('#tbl_list_search tr:first').hide();

			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');

			d.close();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="230px";

			$('#tbl_list_search tr:first').show();
		}
	</script>
	<div style="width:1037px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:1037px;">
		<div id="report_container">
        	<table border="1" class="rpt_table" rules="all" width="300" cellpadding="0" cellspacing="0" align="center">
            	<thead>
                	<tr>
						<th colspan="3"><b>TNA Details</b></th>
                    </tr>
                    <tr>
                    	<th>Date Type</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                    </tr>
				</thead>
                <tbody>
                <? $gray_tna="select task_start_date, task_finish_date, actual_start_date, actual_finish_date from tna_process_mst where task_number=60 and po_number_id in ($order_id) and is_deleted=0 and status_active=1";

				$tna_sql=sql_select($gray_tna);
				?>
                    <tr bgcolor="#E9F3FF">
                    	<td>Plan</td>
                        <td><? if($tna_sql[0][csf('task_start_date')]=="" || $tna_sql[0][csf('task_start_date')]=='0000-00-00') echo ""; else echo change_date_format($tna_sql[0][csf('task_start_date')]); ?></td>
                        <td><? if($tna_sql[0][csf('task_finish_date')]=="" || $tna_sql[0][csf('task_finish_date')]=='0000-00-00') echo ""; else echo change_date_format($tna_sql[0][csf('task_finish_date')]); ?></td>
					</tr>
                	<tr bgcolor="#FFFFFF">
                    	<td>Actual</td>
                        <td><? if($tna_sql[0][csf('actual_start_date')]=="" || $tna_sql[0][csf('actual_start_date')]=='0000-00-00') echo ""; else echo change_date_format($tna_sql[0][csf('actual_start_date')]); ?></td>
                        <td><? if($tna_sql[0][csf('actual_finish_date')]=="" || $tna_sql[0][csf('actual_finish_date')]=='0000-00-00') echo ""; else echo change_date_format($tna_sql[0][csf('actual_finish_date')]); ?></td>
					</tr>
                </tbody>
            </table>
			<table border="1" class="rpt_table" rules="all" width="1020" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="12"><b>Grey Receive Info</b></th>
				</thead>
				<thead>
                	<th width="30">SL</th>
                    <th width="115">Receive Id</th>
                    <th width="95">Receive Basis</th>
                    <th width="110">Product Details</th>
                    <th width="100">Booking / Program No</th>
                    <th width="60">Machine No</th>
                    <th width="75">Production Date</th>
                    <th width="80">Inhouse Production</th>
                    <th width="80">Outside Production</th>
                    <th width="80">Production Qnty</th>
                    <th width="70">Challan No</th>
                    <th>Kniting Com.</th>
				</thead>
            </table>
            <div style="width:1038px; max-height:330px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="1020" cellpadding="0" cellspacing="0" id="tbl_list_search">
                    <?
                    $i=1; $total_receive_qnty=0;
					$product_arr=return_library_array( "select id,product_name_details from product_details_master where item_category_id=13",'id','product_name_details');

                    $sql="select a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.machine_no_id, b.prod_id, sum(c.quantity) as quantity from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.po_breakdown_id in($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.id, a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.machine_no_id, b.prod_id, a.id order by a.id";
                    $result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						/*foreach($fab_source_id as $fsid)
						{
							if($fsid==1)
							{
								$row[csf('quantity')]=$row[csf('quantity')];
							}
							else
							{
								$row[csf('quantity')]=0;
							}
						}*/
						// check when fabric source pursase then qnty 0
                        if ($fab_source_ids[$row[csf('booking_no')]][$row[csf('knitting_source')]]==2)
                        {
							$row[csf('quantity')]=0;
                        }
                        $total_receive_qnty+=$row[csf('quantity')];
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="115"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="95"><p><? echo $receive_basis[$row[csf('receive_basis')]]; ?></p></td>
                            <td width="110"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
                            <td width="100"><p><? echo $row[csf('booking_no')]; ?>&nbsp;</p></td>
                            <td width="60"><p>&nbsp;<? echo $machine_arr[$row[csf('machine_no_id')]]; ?></p></td>
                            <td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                            <td align="right" width="80">
								<?
                                	if($row[csf('knitting_source')]!=3)
									{
										echo number_format($row[csf('quantity')],2,'.','');
										$total_receive_qnty_in+=$row[csf('quantity')];
									}
									else echo "&nbsp;";
                                ?>
                            </td>
                            <td align="right" width="80">
								<?
                                	if($row[csf('knitting_source')]==3)
									{
										echo number_format($row[csf('quantity')],2,'.','');
										$total_receive_qnty_out+=$row[csf('quantity')];
									}
									else echo "&nbsp;";
                                ?>
                            </td>
                            <td align="right" width="80"><? echo number_format($row[csf('quantity')],2,'.',''); ?></td>
                            <td width="70"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
                            <td><p><? if ($row[csf('knitting_source')]==1) echo $company_library[$row[csf('knitting_company')]]; else if ($row[csf('knitting_source')]==3) echo $supplier_details[$row[csf('knitting_company')]]; ?></p></td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                 </table>
            </div>
            <table border="1" class="rpt_table" rules="all" width="1020" cellpadding="0" cellspacing="0">
                <tfoot>
                    <th width="30">&nbsp;</th>
                    <th width="115">&nbsp;</th>
                    <th width="95">&nbsp;</th>
                    <th width="110">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="60">&nbsp;</th>
                    <th width="75" align="right">Total</th>
                    <th width="80" align="right" id="value_receive_qnty_in"><? echo number_format($total_receive_qnty_in,2,'.',''); ?></th>
                    <th width="80" align="right" id="value_receive_qnty_out"><? echo number_format($total_receive_qnty_out,2,'.',''); ?></th>
                    <th width="80" align="right" id="value_receive_qnty_tot"><? echo number_format($total_receive_qnty,2,'.',''); ?></th>
                    <th width="70">&nbsp;</th>
                    <th>&nbsp;</th>
                </tfoot>
            </table>
        </div>
	</fieldset>
<?
exit();
}

if($action=="grey_return")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$po_arr=return_library_array( "select id, po_number from wo_po_break_down where id in ($order_id)", "id", "po_number");
	$issue_arr=return_library_array( "select id, issue_number from inv_issue_master", "id", "issue_number");
	$store_arr=return_library_array( "select id, store_name from lib_store_location", "id", "store_name");
	$batch_arr=return_library_array( "select id, batch_no from pro_batch_create_mst", "id", "batch_no");
?>
<script>

	function print_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');

		d.close();
	}

</script>
	<div style="width:965px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:960px; margin-left:7px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="950" cellpadding="0" cellspacing="0">
				<thead>
                	<tr>
                    	<th colspan="9">Issue Return</th>
                    </tr>
                    <tr>
                    	<th width="20">SL</th>
                        <th width="115">Issue Rtn No</th>
                        <th width="60">Return Date</th>
                        <th width="115">Issue No</th>
                        <th width="100">Store</th>
                        <th width="100">Batch No</th>
                        <th width="170">Item Description</th>
                        <th width="100">Po No.</th>
                        <th>Return Qty</th>
                    </tr>
				</thead>
                <?

				/*if($row[csf('entry_form')]==45 && $row[csf('trans_type')]==3) $grey_receive_return_qnty_arr[$row[csf('po_breakdown_id')]] += $row[csf('quantity')];
				if($row[csf('entry_form')]==51 || $row[csf('entry_form')]==84)
				{
					if($row[csf('trans_type')]==4) $grey_issue_return_qnty_arr[$row[csf('po_breakdown_id')]] += $row[csf('quantity')];
				}*/

                $i=1; $total_issue_return_qnty=0;
				$sql="select a.recv_number, a.receive_date, a.issue_id, b.store_id, b.pi_wo_batch_no, b.prod_id, c.po_breakdown_id, sum(c.quantity) as quantity, d.product_name_details from inv_receive_master a, inv_transaction b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.trans_id and b.prod_id=d.id and a.entry_form in (51,84) and c.entry_form in (51,84) and c.po_breakdown_id in($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.recv_number, a.receive_date, a.issue_id, b.store_id, b.pi_wo_batch_no, b.prod_id, c.po_breakdown_id, d.product_name_details";
				//echo $sql;
                $result=sql_select($sql);
				foreach($result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="20"><? echo $i; ?></td>
                    	<td width="115"><? echo $row[csf('recv_number')]; ?></td>
                        <td width="60" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                        <td width="115"><? echo $issue_arr[$row[csf('issue_id')]]; ?></td>
                        <td width="100" style="word-break:break-all"><? echo $store_arr[$row[csf('store_id')]]; ?></td>
                        <td width="100" style="word-break:break-all"><? echo $batch_arr[$row[csf('pi_wo_batch_no')]]; ?></td>
                        <td width="170" style="word-break:break-all"><? echo $row[csf('product_name_details')]; ?></td>
                        <td width="100" style="word-break:break-all"><? echo $po_arr[$row[csf('po_breakdown_id')]]; ?></td>
                        <td align="right"><? echo number_format($row[csf('quantity')],2); ?> </td>
                    </tr>
                <?
					$total_issue_return_qnty+=$row[csf('quantity')];
                $i++;
                }
                ?>
                <tr style="font-weight:bold">
                    <td colspan="8" align="right">Total</td>
                    <td align="right"><? echo number_format($total_issue_return_qnty,2);?></td>
                </tr>
                </table>
                <table border="1" class="rpt_table" rules="all" width="950" cellpadding="0" cellspacing="0">
				<thead>
                	<tr>
                    	<th colspan="9">Receive Return</th>
                    </tr>
                    <tr>
                    	<th width="20">SL</th>
                        <th width="115">Recieve Rtn No</th>
                        <th width="60">Return Date</th>
                        <th width="115">Recieve No</th>
                        <th width="100">Store</th>
                        <th width="100">Batch No</th>
                        <th width="170">Item Description</th>
                        <th width="100">Po No.</th>
                        <th>Return Qty</th>
                    </tr>
				</thead>
                <?
                $total_receive_return_qnty=0;
				//$sql="select a.transfer_system_id, a.transfer_date, a.challan_no, a.to_order_id, b.from_prod_id, d.product_name_details, sum(c.quantity) as transfer_qnty from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.item_category=2 and a.transfer_criteria=4 and c.trans_type=6 and c.entry_form=15 and c.po_breakdown_id in ($order_id) and c.color_id='$color' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, c.prod_id, a.transfer_system_id, a.transfer_date, a.challan_no, a.to_order_id, b.from_prod_id, d.product_name_details";
				$sql="select a.issue_number, a.issue_date, a.received_mrr_no, b.store_id, b.pi_wo_batch_no, b.prod_id, c.po_breakdown_id, sum(c.quantity) as quantity, d.product_name_details from inv_issue_master a, inv_transaction b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.trans_id and b.prod_id=d.id and a.entry_form in (45) and c.entry_form in (45) and c.po_breakdown_id in($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.issue_number, a.issue_date, a.received_mrr_no, b.store_id, b.pi_wo_batch_no, b.prod_id, c.po_breakdown_id, d.product_name_details";
                $result=sql_select($sql);
				foreach($result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                     <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="20"><? echo $i; ?></td>
                    	<td width="115"><? echo $row[csf('issue_number')]; ?></td>
                        <td width="60" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
                        <td width="115"><? echo $row[csf('received_mrr_no')]; ?></td>
                        <td width="100" style="word-break:break-all"><? echo $store_arr[$row[csf('store_id')]]; ?></td>
                        <td width="100" style="word-break:break-all"><? echo $batch_arr[$row[csf('pi_wo_batch_no')]]; ?></td>
                        <td width="170" style="word-break:break-all"><? echo $row[csf('product_name_details')]; ?></td>
                        <td width="100" style="word-break:break-all"><? echo $po_arr[$row[csf('po_breakdown_id')]]; ?></td>
                        <td align="right"><? echo number_format($row[csf('quantity')],2); ?> </td>
                    </tr>
                <?
					$total_receive_return_qnty+=$row[csf('quantity')];
                	$i++;
                }
                ?>
                <tr style="font-weight:bold">
                    <td colspan="8" align="right">Total</td>
                    <td align="right"><? echo number_format($total_receive_return_qnty,2); ?></td>
                </tr>
                <tfoot>
                    <th colspan="8" align="right">Net Return</th>
                    <th><? echo number_format($total_issue_return_qnty-$total_receive_return_qnty,2);?></th>
                </tfoot>
            </table>
		</div>
	</fieldset>
<?
exit();
}

if($action=="grey_delivery_to_store")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$machine_arr = return_library_array("select id, machine_no from lib_machine_name","id","machine_no");

	$sqlWO="SELECT a.fabric_source,b.po_break_down_id,a.booking_no from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.po_break_down_id in ($order_id)";
	// echo $sqlWO;
	$resultWo=sql_select($sqlWO);
	$fab_source_ids=array();
	foreach($resultWo as $woRow)
	{
		$fab_source_ids[$woRow[csf('booking_no')]][$woRow[csf('fabric_source')]]=$woRow[csf('fabric_source')];
	}

	$sqlWO="SELECT a.fabric_source,b.po_break_down_id, d.id as program_no
	from wo_booking_mst a, wo_booking_dtls b, ppl_planning_info_entry_mst c, ppl_planning_info_entry_dtls d where a.booking_no=b.booking_no and b.booking_no=c.booking_no and c.id=d.mst_id
	and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.po_break_down_id in ($order_id) ";
	//echo $sqlWO;
	$resultWo=sql_select($sqlWO);
	//$fab_source_ids=array();
	foreach($resultWo as $woRow)
	{
		$fab_source_ids[$woRow[csf('program_no')]][$woRow[csf('fabric_source')]]=$woRow[csf('fabric_source')];
	}
	/*$fab_source_ids="";
	foreach($resultWo as $woRow)
	{
		 $fab_source_ids.=$woRow[csf('fabric_source')].',';
	}
	$fab_source=rtrim($fab_source_ids,',');
	$fab_source_id=array_unique(explode(",",$fab_source));*/

	?>
	<script>

		var tableFilters = {
							   col_operation: {
							   id: ["value_delivery_qnty"],
							   col: [7],
							   operation: ["sum"],
							   write_method: ["innerHTML"]
							}
						}
		$(document).ready(function(e) {
			setFilterGrid('tbl_list_search',-1,tableFilters);
		});

		function print_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";

			$('#tbl_list_search tr:first').hide();

			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');

			d.close();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="230px";

			$('#tbl_list_search tr:first').show();
		}
	</script>
	<div style="width:720px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:720px;">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="720" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="8"><b>Grey Delivery To Store Info</b></th>
				</thead>
				<thead>
                	<th width="30">SL</th>
                    <th width="115">Challan No</th>
                    <th width="75">Delivery Date</th>
                    <th width="115">Production ID</th>
                    <th width="180">Product Details</th>
                    <th width="50">GSM</th>
                    <th width="50">Dia</th>
                    <th>Delivery Qnty</th>
				</thead>
            </table>
            <div style="width:740px; max-height:330px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="720" cellpadding="0" cellspacing="0" id="tbl_list_search">
                    <?
                    $i=1; $total_receive_qnty=0;
					$product_arr=return_library_array( "select id,product_name_details from product_details_master where item_category_id=13",'id','product_name_details');

                   // $sql="select a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.machine_no_id, b.prod_id, sum(c.quantity) as quantity from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.po_breakdown_id in($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.id, a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.machine_no_id, b.prod_id";

				   //select order_id, sum(current_delivery) as grey_delivery_qty from pro_grey_prod_delivery_dtls where entry_form in(53,56) and status_active=1 and is_deleted=0 $grey_delivery_po_cond group by order_id
				    //$sql="select a.sys_number_prefix_num, a.sys_number, a.delevery_date, b.grey_sys_number, b.product_id, b.construction, b.composition, b.gsm, b.dia, sum(b.current_delivery) as delivery_qty from pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b where a.id=b.mst_id and a.entry_form in (53,56) and b.order_id in ($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.sys_number_prefix_num, a.sys_number, a.delevery_date, b.grey_sys_number, b.product_id, b.construction, b.composition, b.gsm, b.dia, b.id order by b.id";
				    $sql="SELECT a.sys_number_prefix_num, a.sys_number, a.delevery_date, b.grey_sys_number, b.product_id, b.construction, b.composition, b.gsm, b.dia, sum(b.current_delivery) as delivery_qty from pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b, inv_receive_master c where a.id=b.mst_id and b.grey_sys_id=c.id and a.entry_form in (53,56)  and c.entry_form in (2) and b.order_id in ($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.booking_without_order <> 1 and c.is_deleted=0 group by a.sys_number_prefix_num, a.sys_number, a.delevery_date, b.grey_sys_number, b.product_id, b.construction, b.composition, b.gsm, b.dia, b.id order by b.id";
                    $result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    	/*foreach($fab_source_id as $fsid)
						{
							if($fsid==1)
							{
								$row[csf('delivery_qty')]=$row[csf('delivery_qty')];
							}
							else
							{
								$row[csf('delivery_qty')]=0;
							}
						}*/
						// check when fabric source pursase then qnty 0
						if ($fab_source_ids[$row[csf('booking_no')]][$row[csf('knitting_source')]]==2)
                        {
							$row[csf('quantity')]=0;
                        }
                        $total_delivery_qnty+=$row[csf('delivery_qty')];
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="115"><p><? echo $row[csf('sys_number')]; ?></p></td>
                            <td width="75"><p><? echo change_date_format($row[csf('delevery_date')]); ?></p></td>
                            <td width="115"><p><? echo $row[csf('grey_sys_number')]; ?></p></td>
                            <td width="180"><p><? echo $product_arr[$row[csf('product_id')]]; ?>&nbsp;</p></td>
                            <td width="50"><? echo $row[csf('gsm')]; ?></td>
                            <td width="50"><? echo $row[csf('dia')]; ?></td>
                            <td align="right"><? echo number_format($row[csf('delivery_qty')],2,'.',''); ?></td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                 </table>
            </div>
            <table border="1" class="rpt_table" rules="all" width="720" cellpadding="0" cellspacing="0">
                <tfoot>
                    <th width="30">&nbsp;</th>
                    <th width="115">&nbsp;</th>
                    <th width="75">&nbsp;</th>
                    <th width="115">&nbsp;</th>
                    <th width="180">&nbsp;</th>
                    <th width="50">&nbsp;</th>
                    <th width="50" align="right">Total</th>
                    <th align="right" id="value_delivery_qnty"><? echo number_format($total_delivery_qnty,2,'.',''); ?></th>
                </tfoot>
            </table>
        </div>
	</fieldset>
<?
exit();
}

if($action=="grey_purchase")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$order_id=explode('_',$order_id);
	$po_arr=return_library_array( "select id, po_number from wo_po_break_down where id in ($order_id[0])", "id", "po_number");
	$issue_arr=return_library_array( "select id, issue_number from inv_issue_master", "id", "issue_number");
	$store_arr=return_library_array( "select id, store_name from lib_store_location", "id", "store_name");
	$batch_arr=return_library_array( "select id, batch_no from pro_batch_create_mst", "id", "batch_no");

	/*$sqlWO="select a.fabric_source,b.po_break_down_id,a.booking_no from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.po_break_down_id in ($order_id[0])";
	$resultWo=sql_select($sqlWO);
	$fab_source_ids="";
	foreach($resultWo as $woRow)
	{
		 $fab_source_ids.=$woRow[csf('fabric_source')].',';
	}
	$fab_source=rtrim($fab_source_ids,',');
	$fab_source_id=array_unique(explode(",",$fab_source));*/
	$sqlWO="SELECT a.fabric_source,b.po_break_down_id,a.booking_no from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.po_break_down_id in ($order_id[0])";
	// echo $sqlWO;
	$resultWo=sql_select($sqlWO);
	$fab_source_ids=array();
	foreach($resultWo as $woRow)
	{
		$fab_source_ids[$woRow[csf('booking_no')]][$woRow[csf('fabric_source')]]=$woRow[csf('fabric_source')];
	}

	$sqlWO="SELECT a.fabric_source,b.po_break_down_id, d.id as program_no
	from wo_booking_mst a, wo_booking_dtls b, ppl_planning_info_entry_mst c, ppl_planning_info_entry_dtls d where a.booking_no=b.booking_no and b.booking_no=c.booking_no and c.id=d.mst_id
	and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.po_break_down_id in ($order_id[0]) ";
	//echo $sqlWO;
	$resultWo=sql_select($sqlWO);
	//$fab_source_ids=array();
	foreach($resultWo as $woRow)
	{
		$fab_source_ids[$woRow[csf('program_no')]][$woRow[csf('fabric_source')]]=$woRow[csf('fabric_source')];
	}

	$head_cap=""; $ret_rec_basis_cond="";
	if($order_id[1]==9)
	{
		$receive_basis_cond=" and a.receive_basis in (9,10)";
		$head_cap="Production";
		$ret_rec_basis_cond=" and e.receive_basis in (9,10)";
	}
	else if($order_id[1]==0)
	{
		$receive_basis_cond=" and a.receive_basis not in (9,10)";
		$head_cap="Purchase";
		$ret_rec_basis_cond=" and e.receive_basis not in (9,10)";
	}

	?>
	<script>
		function print_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";

			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');

			d.close();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="230px";
		}
	</script>
	<div style="width:1037px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:1037px; margin-left:2px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="1020" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="11"><b>Grey Receive / <? echo $head_cap; ?> Info</b></th>
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

                   $sql="SELECT a.recv_number, a.receive_date, a.receive_basis, a.booking_id, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.prod_id, sum(c.quantity) as quantity from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id $receive_basis_cond and a.entry_form in (22,58) and c.entry_form in (22,58) and c.po_breakdown_id in($order_id[0]) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.id, a.recv_number, a.receive_date, a.receive_basis, a.booking_id, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.prod_id order by b.id";
                    $result=sql_select($sql);
                    $booking_id_arr=array();
                    foreach($result as $row)
                    {
                    	$booking_id_arr[$row[csf('booking_id')]]=$row[csf('booking_id')];
                    }
                    if(count($booking_id_arr)>0)
					{
						$all_delivery_id = implode(",", array_unique($booking_id_arr));
						$delivery_ids=" and a.id in($all_delivery_id)";
					}
					//echo $prog_cond_for_rcv;
					$delivery_sql="SELECT a.sys_number, b.product_id, c.booking_no
					from pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b, inv_receive_master c
					where a.id=b.mst_id and b.grey_sys_id=c.id and a.entry_form in (53,56) and c.entry_form in (2) and b.order_id in($order_id[0]) $delivery_ids and a.status_active=1 and a.is_deleted=0 and b.status_active=1
					and b.is_deleted=0 and c.status_active=1 and c.booking_without_order <> 1 and c.is_deleted=0
					group by a.sys_number, b.product_id, c.booking_no ";
					//echo $delivery_sql;
					$delivery_result=sql_select($delivery_sql);
					$booking_program_arr=array();
					foreach($result as $rows)
                    {
                    	$booking_program_arr[$rows[csf('booking_no')]]=$rows[csf('booking_no')];
                    }
        			foreach($result as $row)
                    {
                        if ($i%2==0) $bgcolor="#E9F3FF";else $bgcolor="#FFFFFF";
                        // check when fabric source pursase then qnty 0
						if ($fab_source_ids[$booking_program_arr[$row[csf('booking_no')]]][$row[csf('knitting_source')]]==2)
                        {
							$row[csf('quantity')]=0;
                        }
                    	/*foreach($fab_source_id as $fsid)
						{
							if($fsid==1) $row[csf('quantity')]=$row[csf('quantity')];
							else $row[csf('quantity')]=0;
						}*/
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
										echo number_format($row[csf('quantity')],2,'.','');
										$total_receive_qnty_in+=$row[csf('quantity')];
									}
									else echo "&nbsp;";
                                ?>
                            </td>
                            <td align="right" width="80">
								<?
                                	if($row[csf('knitting_source')]==3)
									{
										echo number_format($row[csf('quantity')],2,'.','');
										$total_receive_qnty_out+=$row[csf('quantity')];
									}
									else echo "&nbsp;";
                                ?>
                            </td>
                            <td align="right" width="80"><? echo number_format($row[csf('quantity')],2,'.',''); ?></td>
                            <td width="65"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
                            <td><p><? if ($row[csf('knitting_source')]==1) echo $company_library[$row[csf('knitting_company')]]; else if ($row[csf('knitting_source')]==3) echo $supplier_details[$row[csf('knitting_company')]]; ?>&nbsp;</p></td>
                        </tr>
                    	<?
                    	$i++;
                    }
                    ?>
                    <tfoot>
                        <th colspan="6" align="right">Total <? echo $head_cap; ?></th>
                        <th align="right"><? echo number_format($total_receive_qnty_in,2,'.',''); ?></th>
                        <th align="right"><? echo number_format($total_receive_qnty_out,2,'.',''); ?></th>
                        <th align="right"><? echo number_format($total_receive_qnty,2,'.',''); ?></th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                    </tfoot>
                </table>

                <table border="1" class="rpt_table" rules="all" width="950" cellpadding="0" cellspacing="0">
				<thead>
                	<tr>
                    	<th colspan="8">Receive Return <? echo $head_cap; ?></th>
                    </tr>
                    <tr>
                    	<th width="20">SL</th>
                        <th width="115">Recieve Rtn No</th>
                        <th width="60">Return Date</th>
                        <th width="115">Recieve No</th>
                        <th width="100">Store</th>
                        <th width="100">Batch No</th>
                        <th width="270">Item Description</th>
                        <th>Return Qty</th>
                    </tr>
				</thead>
                <?
                $total_receive_return_qnty=0;
				//$sql="select a.transfer_system_id, a.transfer_date, a.challan_no, a.to_order_id, b.from_prod_id, d.product_name_details, sum(c.quantity) as transfer_qnty from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.item_category=2 and a.transfer_criteria=4 and c.trans_type=6 and c.entry_form=15 and c.po_breakdown_id in ($order_id) and c.color_id='$color' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, c.prod_id, a.transfer_system_id, a.transfer_date, a.challan_no, a.to_order_id, b.from_prod_id, d.product_name_details";
				$sql="select a.issue_number, a.issue_date, a.received_mrr_no, b.store_id, b.pi_wo_batch_no, b.prod_id, c.po_breakdown_id, sum(c.quantity) as quantity, d.product_name_details from inv_issue_master a, inv_transaction b, order_wise_pro_details c, product_details_master d, inv_receive_master e where a.id=b.mst_id and b.id=c.trans_id and b.prod_id=d.id and e.id=a.received_id and a.entry_form in (45) and c.entry_form in (45) and c.po_breakdown_id in($order_id[0]) $ret_rec_basis_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.issue_number, a.issue_date, a.received_mrr_no, b.store_id, b.pi_wo_batch_no, b.prod_id, c.po_breakdown_id, d.product_name_details";
                $result=sql_select($sql);
				foreach($result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                     <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="20"><? echo $i; ?></td>
                    	<td width="115"><? echo $row[csf('issue_number')]; ?></td>
                        <td width="60" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
                        <td width="115"><? echo $row[csf('received_mrr_no')]; ?></td>
                        <td width="100" style="word-break:break-all"><? echo $store_arr[$row[csf('store_id')]]; ?></td>
                        <td width="100" style="word-break:break-all"><? echo $batch_arr[$row[csf('pi_wo_batch_no')]]; ?></td>
                        <td width="270" style="word-break:break-all"><? echo $row[csf('product_name_details')]; ?></td>
                        <td align="right"><? echo number_format($row[csf('quantity')],2); ?> </td>
                    </tr>
                <?
					$total_receive_return_qnty+=$row[csf('quantity')];
                	$i++;
                }
                ?>
                <tr style="font-weight:bold">
                    <td colspan="7" align="right">Total Receive Return <? echo $head_cap; ?></td>
                    <td align="right"><? echo number_format($total_receive_return_qnty,2); ?></td>
                </tr>
                <tfoot>
                    <th colspan="7" align="right">Net Receive <? echo $head_cap; ?></th>
                    <th><? echo number_format($total_receive_qnty-$total_receive_return_qnty,2);?></th>
                </tfoot>
            </table>
            </div>
        </div>
	</fieldset>
	<?
    exit();
}

if($action=="batch_qnty")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$ex_data=explode('_',$order_id);
	/*$sqlWO="select a.fabric_source,b.po_break_down_id,a.booking_no from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.po_break_down_id in ($ex_data[0])";
	 $resultWo=sql_select($sqlWO);
	 $fab_source_ids="";
		foreach($resultWo as $woRow)
		{
			 $fab_source_ids.=$woRow[csf('fabric_source')].',';
		}
		$fab_source=rtrim($fab_source_ids,',');
		$fab_source_id=array_unique(explode(",",$fab_source));*/
?>
<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');

		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
	}

</script>
	<div style="width:675px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:670px; margin-left:7px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="5"><b>Batch Info</b></th>
				</thead>
				<thead>
                	<th width="50">SL</th>
                    <th width="100">Batch Date</th>
                    <th width="170">Batch No</th>
                    <th width="150">Batch Color</th>
                    <th>Batch Qnty </th>
				</thead>
             </table>
             <div style="width:667px; max-height:320px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0">
                    <?
                    $i=1; $total_batch_qnty=0;
                   $sql="select a.batch_no, a.extention_no as ext_no, a.batch_date, a.color_id, sum(b.batch_qnty) as quantity from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and b.po_id in($ex_data[0]) and a.color_id='$ex_data[1]' and a.status_active=1 and a.entry_form not in (36,74,17,7,37,14,134)  and a.batch_against not in(2) and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.batch_no, a.extention_no, a.batch_date, a.color_id";
                   //and a.entry_form!=36
                    $result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                        $total_batch_qnty+=$row[csf('quantity')];
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="50"><? echo $i; ?></td>
                            <td width="100" align="center"><? echo change_date_format($row[csf('batch_date')]); ?></td>
                            <td width="170"><p><? echo $row[csf('batch_no')]; if($row[csf('ext_no')]!=0) echo $row[csf('ext_no')]; ?></p></td>
                            <td width="150"><p><? echo $color_array[$row[csf('color_id')]]; ?></p></td>
                            <td align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tfoot>
                        <th colspan="4" align="right">Total</th>
                        <th align="right"><? echo number_format($total_batch_qnty,2); ?></th>
                    </tfoot>
                </table>
            </div>
        </div>
	</fieldset>
<?
exit();
}

if($action=="grey_issue")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$sqlWO="select a.fabric_source,b.po_break_down_id,a.booking_no from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.po_break_down_id in ($order_id)";
	 $resultWo=sql_select($sqlWO);
	 $fab_source_ids="";
		foreach($resultWo as $woRow)
		{
			 $fab_source_ids.=$woRow[csf('fabric_source')].',';
		}
		$fab_source=rtrim($fab_source_ids,',');
		$fab_source_id=array_unique(explode(",",$fab_source));
		$po_arr=return_library_array( "select id, po_number from wo_po_break_down where id in ($order_id)", "id", "po_number");
		$issue_arr=return_library_array( "select id, issue_number from inv_issue_master", "id", "issue_number");
		$store_arr=return_library_array( "select id, store_name from lib_store_location", "id", "store_name");
		$batch_arr=return_library_array( "select id, batch_no from pro_batch_create_mst", "id", "batch_no");
?>

<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');

		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
	}

</script>
	<div style="width:955px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
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
                    $sql="select a.issue_number, a.issue_date, a.issue_purpose, a.knit_dye_source, a.knit_dye_company, a.booking_no, a.batch_no, sum(c.quantity) as quantity from inv_issue_master a, inv_grey_fabric_issue_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(16,61) and c.entry_form in(16,61) and c.po_breakdown_id in($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id,  a.issue_number, a.issue_date, a.issue_purpose, a.knit_dye_source, a.knit_dye_company, a.booking_no, a.batch_no order by a.id";
                    $result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

                        if($row[csf('knit_dye_source')]==1) $issue_to=$company_library[$row[csf('knit_dye_company')]];
                        else if($row[csf('knit_dye_source')]==3) $issue_to=$supplier_details[$row[csf('knit_dye_company')]];
                        else $issue_to="&nbsp;";

						foreach($fab_source_id as $fsid)
						{
							if($fsid==1) $row[csf('quantity')]=$row[csf('quantity')];
						}

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
                                        echo number_format($row[csf('quantity')],2);
                                        $total_issue_qnty+=$row[csf('quantity')];
                                    }
                                    else echo "&nbsp;";
                                ?>
                            </td>
                            <td align="right">
                                <?
                                    if($row[csf('knit_dye_source')]==3)
                                    {
                                        echo number_format($row[csf('quantity')],2);
                                        $total_issue_qnty_out+=$row[csf('quantity')];
                                    }
                                    else echo "&nbsp;";
                                ?>
                            </td>
                        </tr>
                    <?
                    $i++;
                    }
                    unset($result);
                    ?>
                    <tfoot>
                    	<tr>
                            <th colspan="8" align="right">Total Grey Issue:</th>
                            <th align="right"><? echo number_format($total_issue_qnty,2); ?></th>
                            <th align="right"><? echo number_format($total_issue_qnty_out,2); ?></th>
                        </tr>
                     </tfoot>
                </table>

                <table border="1" class="rpt_table" rules="all" width="950" cellpadding="0" cellspacing="0">
				<thead>
                	<tr>
                    	<th colspan="9">Issue Return</th>
                    </tr>
                    <tr>
                    	<th width="20">SL</th>
                        <th width="115">Issue Rtn No</th>
                        <th width="60">Return Date</th>
                        <th width="115">Issue No</th>
                        <th width="100">Store</th>
                        <th width="100">Batch No</th>
                        <th width="170">Item Description</th>
                        <th width="100">Po No.</th>
                        <th>Return Qty</th>
                    </tr>
				</thead>
                <?

                $i=1; $total_issue_return_qnty=0;
				$sql="select a.recv_number, a.receive_date, a.issue_id, b.store_id, b.pi_wo_batch_no, b.prod_id, c.po_breakdown_id, sum(c.quantity) as quantity, d.product_name_details from inv_receive_master a, inv_transaction b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.trans_id and b.prod_id=d.id and a.entry_form in (51,84) and c.entry_form in (51,84) and c.po_breakdown_id in($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.recv_number, a.receive_date, a.issue_id, b.store_id, b.pi_wo_batch_no, b.prod_id, c.po_breakdown_id, d.product_name_details";
				//echo $sql;
                $result=sql_select($sql);
				foreach($result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="20"><? echo $i; ?></td>
                    	<td width="115"><? echo $row[csf('recv_number')]; ?></td>
                        <td width="60" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                        <td width="115"><? echo $issue_arr[$row[csf('issue_id')]]; ?></td>
                        <td width="100" style="word-break:break-all"><? echo $store_arr[$row[csf('store_id')]]; ?></td>
                        <td width="100" style="word-break:break-all"><? echo $batch_arr[$row[csf('pi_wo_batch_no')]]; ?></td>
                        <td width="170" style="word-break:break-all"><? echo $row[csf('product_name_details')]; ?></td>
                        <td width="100" style="word-break:break-all"><? echo $po_arr[$row[csf('po_breakdown_id')]]; ?></td>
                        <td align="right"><? echo number_format($row[csf('quantity')],2); ?> </td>
                    </tr>
                <?
					$total_issue_return_qnty+=$row[csf('quantity')];
                	$i++;
                	$greyIssReturnArr[$row[csf('prod_id')]] += $row[csf('quantity')];
                	$prod_ref[$row[csf('prod_id')]] = $row[csf('product_name_details')];
                }
                unset($result);
                ?>
                <tr style="font-weight:bold">
                    <td colspan="8" align="right">Total Issue Return:</td>
                    <td align="right"><? echo number_format($total_issue_return_qnty,2);?></td>
                </tr>
                <tr>
                    <th colspan="7" align="right">Grand Total:</th>
                    <th align="right" colspan="2"><? echo number_format((($total_issue_qnty+$total_issue_qnty_out)-$total_issue_return_qnty),2); ?></th>
                </tr>
            </table>
            </div>
        </div>
        <br>
        <table border="1" class="rpt_table" rules="all" width="550" cellpadding="0" cellspacing="0">
            <thead>
            	<tr>
                    <th colspan="5"><b>Grey Issue and Batch Balance Info</b></th>
                </tr>
                <tr>
                    <th width="40">SL</th>
                    <th width="170">Fabric Description</th>
                    <th width="100">Issue Qnty</th>
                    <th width="100">Batch Qnty</th>
                    <th width="100">Balanced</th>
                </tr>
			</thead>
			<tbody>
				<?
				$batch_result = return_library_array("select b.prod_id, sum(b.batch_qnty) as quantity from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and b.po_id in($order_id) and a.status_active=1 and a.entry_form!=36  and a.batch_against not in(2) and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.prod_id","prod_id","quantity");


				$result= sql_select("select b.prod_id, d.product_name_details, sum(c.quantity) as quantity from inv_issue_master a, inv_grey_fabric_issue_dtls b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.dtls_id and b.prod_id=d.id and a.entry_form in(16,61) and c.entry_form in(16,61) and c.po_breakdown_id in($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.prod_id, d.product_name_details");
				$i=1;$total_issue_quantity=$total_batch_quantity=$total_balance_quantity=0;
				foreach($result as $row)
				{
					$issue_quantity = $row[csf('quantity')] - $greyIssReturnArr[$row[csf('prod_id')]];
					if($issue_quantity>0)
					{
						$balance_qnty = $row[csf('quantity')] - $batch_result[$row[csf('prod_id')]];
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('trb_<? echo $i; ?>','<? echo $bgcolor;?>')" id="trb_<? echo $i;?>">
		                	<td width="20"><? echo $i; ?></td>
		                    <td width="170" style="word-break:break-all"><? echo $row[csf('product_name_details')]; ?></td>
		                    <td width="100" align="right"><? echo number_format($issue_quantity,2); ?></td>
		                    <td width="100" align="right"><? echo number_format($batch_result[$row[csf('prod_id')]],2); ?></td>
		                    <td width="100" align="right"><? echo number_format($balance_qnty,2); ?></td>
		                </tr>
	                <?
	                	$total_issue_quantity += $issue_quantity;
	                	$total_batch_quantity += $batch_result[$row[csf('prod_id')]];
	                	$total_balance_quantity += $balance_qnty;
	            	}
	            	$i++;
            	}
            	?>
			</tbody>
			<tfoot>
            	<tr>
                    <th colspan="2" align="right">Total:</th>
                    <th align="right"><? echo number_format($total_issue_quantity,2); ?></th>
                    <th align="right"><? echo number_format($total_batch_quantity,2); ?></th>
                    <th align="right"><? echo number_format($total_balance_quantity,2); ?></th>
                </tr>
             </tfoot>
         </table>
         <br>
	</fieldset>
<?
exit();
}

if($action=="receive_by_batch")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function print_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";

			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');

			d.close();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="230px";
		}
	</script>
	<div style="width:955px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:970px; margin-left:3px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="950" cellpadding="0" cellspacing="0">
                <thead>
                	<tr>
                        <th colspan="11"><b>Grey Receive For Batch</b></th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="110">Receive Id</th>
                        <th width="100">Purpose</th>
                        <th width="110">Issue Challan</th>
                        <th width="80">Dyeing Source</th>
                        <th width="100">Dyeing Company</th>
                        <th width="60">Receive Date</th>
                        <th width="170">Item Description</th>
                        <th width="80">Bodypart</th>
                        <th width="100">Qty</th>
						<th width="100">Insert User</th>
                    </tr>
				</thead>
             </table>
             <div style="width:967px; max-height:320px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="950" cellpadding="0" cellspacing="0">
                    <?
					$product_arr = return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
					$user_name   = return_library_array( "select id, user_name from user_passwd", "id", "user_name");

                    $i=1; $issue_to='';
                    $sql="SELECT a.id, a.recv_number, a.dyeing_source, a.dyeing_company, a.challan_no, a.receive_basis, a.receive_date, b.prod_id, b.body_part_id, a.inserted_by, sum(c.qnty) as qnty
					 from inv_receive_mas_batchroll a, pro_grey_batch_dtls b, pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(62) and c.entry_form in(62) and b.order_id in($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, a.recv_number, a.dyeing_source, a.dyeing_company, a.challan_no, a.receive_basis, a.receive_date, b.prod_id, b.body_part_id, a.inserted_by order by a.id";
                    $result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

                        if($row[csf('dyeing_source')]==1) $issue_to=$company_library[$row[csf('dyeing_company')]];
                        else if($row['dyeing_source']==3) $issue_to=$supplier_details[$row[csf('dyeing_company')]];
                        else $issue_to="&nbsp;";
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="110" style="word-break:break-all"><? echo $row[csf('recv_number')]; ?></td>
                            <td width="100" style="word-break:break-all"><? echo $yarn_issue_purpose[$row[csf('receive_basis')]]; ?></td>
                            <td width="110" style="word-break:break-all"><? echo $row[csf('challan_no')]; ?>&nbsp;</td>
                            <td width="80" style="word-break:break-all"><? echo $knitting_source[$row[csf('dyeing_source')]]; ?></td>
                            <td width="100" style="word-break:break-all"><? echo $issue_to; ?></td>

                            <td width="60" style="word-break:break-all"><? echo change_date_format($row[csf('receive_date')]); ?>&nbsp;</td>
                            <td width="170" style="word-break:break-all"><? echo $product_arr[$row[csf('prod_id')]]; ?>&nbsp;</td>
                            <td width="80" style="word-break:break-all"><? echo $body_part[$row[csf('body_part_id')]]; ?></td>
                            <td width="100" align="right"><? echo number_format($row[csf('qnty')],2); $total_rec_qnty+=$row[csf('qnty')]; ?></td>
							<td width="100" style="word-break:break-all"><? echo $user_name[$row[csf('inserted_by')]]; ?></td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tfoot>
                    	<tr>
                            <th colspan="9" align="right">Total Grey Receive For Batch:</th>
                            <th align="right"><? echo number_format($total_rec_qnty,2); ?></th>
                        </tr>
                     </tfoot>
                </table>
            </div>
        </div>
	</fieldset>
	<?
    exit();
}

if($action=="dye_qnty")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);

	$machine_arr = return_library_array("select id, machine_no as machine_name from lib_machine_name","id","machine_name");
	$sqlWO="select a.fabric_source,b.po_break_down_id,a.booking_no from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.po_break_down_id in ($order_id)";
	 $resultWo=sql_select($sqlWO);
	 $fab_source_ids="";
		foreach($resultWo as $woRow)
		{
			 $fab_source_ids.=$woRow[csf('fabric_source')].',';
		}
		$fab_source=rtrim($fab_source_ids,',');
		$fab_source_id=array_unique(explode(",",$fab_source));

?>
<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');

		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
	}

</script>
	<div style="width:885px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:880px; margin-left:3px">
		<div id="report_container">
        <table border="1" class="rpt_table" rules="all" width="300" cellpadding="0" cellspacing="0" align="center">
            	<thead>
                	<tr>
						<th colspan="3"><b>TNA Details</b></th>
                    </tr>
                    <tr>
                    	<th>Date Type</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                    </tr>
				</thead>
                <tbody>
                <? $dye_tna="select task_start_date, task_finish_date, actual_start_date, actual_finish_date from tna_process_mst where task_number=61 and po_number_id in ($order_id) and is_deleted=0 and status_active=1";

				$tna_sql=sql_select($dye_tna);
				?>
                    <tr bgcolor="#E9F3FF">
                    	<td>Plan</td>
                        <td><? if($tna_sql[0][csf('task_start_date')]=="" || $tna_sql[0][csf('task_start_date')]=='0000-00-00') echo ""; else echo change_date_format($tna_sql[0][csf('task_start_date')]); ?></td>
                        <td><? if($tna_sql[0][csf('task_finish_date')]=="" || $tna_sql[0][csf('task_finish_date')]=='0000-00-00') echo ""; else echo change_date_format($tna_sql[0][csf('task_finish_date')]); ?></td>
					</tr>
                	<tr bgcolor="#FFFFFF">
                    	<td>Actual</td>
                        <td><? if($tna_sql[0][csf('actual_start_date')]=="" || $tna_sql[0][csf('actual_start_date')]=='0000-00-00') echo ""; else echo change_date_format($tna_sql[0][csf('actual_start_date')]); ?></td>
                        <td><? if($tna_sql[0][csf('actual_finish_date')]=="" || $tna_sql[0][csf('actual_finish_date')]=='0000-00-00') echo ""; else echo change_date_format($tna_sql[0][csf('actual_finish_date')]); ?></td>
					</tr>
                </tbody>
            </table>
			<table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">

				<thead>
					<th colspan="10"><b>Dyeing Info</b></th>
				</thead>
				<thead>
                	<th width="30">SL</th>
                    <th width="70">System Id</th>
                    <th width="80">Process End Date</th>
                    <th width="100">Batch No</th>
                    <th width="70">Dyeing Source</th>
                    <th width="120">Dyeing Company</th>
                    <th width="90">Receive Qnty</th>
                    <th width="190">Fabric Description</th>
                    <th>Machine Name</th>
				</thead>
             </table>
             <div style="width:877px; max-height:320px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
                    <?
					$i=1; $total_dye_qnty=0; $dye_company='';
					$sql="select a.batch_no, b.item_description as febric_description, sum(b.batch_qnty) as quantity, c.id, c.company_id,c.service_source,c.service_company, c.process_end_date, c.machine_id from pro_batch_create_mst a,pro_batch_create_dtls b, pro_fab_subprocess c where a.id=b.mst_id and a.id=c.batch_id and a.color_id='$color' and c.load_unload_id=2 and c.entry_form=35 and b.po_id in($order_id) and a.batch_against<>2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, a.batch_no, b.item_description, c.id,c.service_source,c.service_company,c.company_id, c.process_end_date, c.machine_id";
                    $result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0)
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";
                    	if($row[csf('service_source')]==1)
						{
							$dye_company=$company_library[$row[csf('service_company')]];
						}
						else
						{
							$dye_company=$supplier_details[$row[csf('service_company')]];
						}
						foreach($fab_source_id as $fsid)
						{
							if($fsid==1)
							{
								$row[csf('quantity')]=$row[csf('quantity')];
							}

						}
                        $total_dye_qnty+=$row[csf('quantity')];
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="70"><p><? echo $row[csf('id')]; ?></p></td>
                            <td width="80" align="center"><? echo change_date_format($row[csf('process_end_date')]); ?>&nbsp;</td>
                            <td width="100"><p><? echo $row[csf('batch_no')];//$batch_details[$row[csf('batch_id')]]; ?></p></td>
                            <td width="70"><? echo "Inhouse";//echo $knitting_source[$row[csf('dyeing_source')]]; ?></td>
                            <td width="120"><p><? echo $dye_company; ?></p></td>
                            <td width="90" align="right"><? echo number_format($row[csf('quantity')],2); ?></td>
                            <td width="190"><p><? echo $row[csf('febric_description')]; ?></p></td>
                            <td><p>&nbsp;<? echo $machine_arr[$row[csf('machine_id')]]; ?></p></td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tfoot>
                        <th colspan="6" align="right">Total</th>
                        <th align="right"><? echo number_format($total_dye_qnty,2); ?></th>
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

if($action=="fabric_receive")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?>
<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');

		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
	}

</script>
	<div style="width:885px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:880px; margin-left:3px">
		<div id="report_container">
        	<table border="1" class="rpt_table" rules="all" width="300" cellpadding="0" cellspacing="0" align="center">
            	<thead>
                	<tr>
						<th colspan="3"><b>TNA Details</b></th>
                    </tr>
                    <tr>
                    	<th>Date Type</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                    </tr>
				</thead>
                <tbody>
                <? $fin_tna="select task_start_date, task_finish_date, actual_start_date, actual_finish_date from tna_process_mst where task_number=73 and po_number_id in ($order_id) and is_deleted=0 and status_active=1";

				$tna_sql=sql_select($fin_tna);
				?>
                    <tr bgcolor="#E9F3FF">
                    	<td>Plan</td>
                        <td><? if($tna_sql[0][csf('task_start_date')]=="" || $tna_sql[0][csf('task_start_date')]=='0000-00-00') echo ""; else echo change_date_format($tna_sql[0][csf('task_start_date')]); ?></td>
                        <td><? if($tna_sql[0][csf('task_finish_date')]=="" || $tna_sql[0][csf('task_finish_date')]=='0000-00-00') echo ""; else echo change_date_format($tna_sql[0][csf('task_finish_date')]); ?></td>
					</tr>
                	<tr bgcolor="#FFFFFF">
                    	<td>Actual</td>
                        <td><? if($tna_sql[0][csf('actual_start_date')]=="" || $tna_sql[0][csf('actual_start_date')]=='0000-00-00') echo ""; else echo change_date_format($tna_sql[0][csf('actual_start_date')]); ?></td>
                        <td><? if($tna_sql[0][csf('actual_finish_date')]=="" || $tna_sql[0][csf('actual_finish_date')]=='0000-00-00') echo ""; else echo change_date_format($tna_sql[0][csf('actual_finish_date')]); ?></td>
					</tr>
                </tbody>
            </table>
			<table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="9"><b>Fabric Receive Info</b></th>
				</thead>
				<thead>
                	<th width="30">SL</th>
                    <th width="120">System Id</th>
                    <th width="75">Rec. Date</th>
                    <th width="80">Rec. Basis</th>
                    <th width="90">Batch No</th>
                    <th width="90">Dyeing Source</th>
                    <th width="100">Dyeing Company</th>
                    <th width="90">Receive Qnty</th>
                    <th>Fabric Description</th>
				</thead>
             </table>
             <div style="width:877px; max-height:320px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
                    <?
                    $i=1;
                    $total_fabric_recv_qnty=0; $dye_company='';
                    $sql="select a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, b.batch_id, b.prod_id, sum(c.quantity) as quantity from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in (7,66) and c.entry_form in (7,66) and c.po_breakdown_id in($order_id) and c.color_id='$color' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.id, a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, b.batch_id, b.prod_id";
                    $result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

                        if($row[csf('knitting_source')]==1)
                        {
                            $dye_company=$company_library[$row[csf('knitting_company')]];
                        }
                        else if($row['knitting_source']==3)
                        {
                            $dye_company=$supplier_details[$row[csf('knitting_company')]];
                        }
                        else
                            $dye_company="&nbsp;";

                        $total_fabric_recv_qnty+=$row[csf('quantity')];
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="120"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                            <td width="80"><? echo $receive_basis_arr[$row[csf('receive_basis')]]; ?></td>
                            <td width="90"><p><? echo $batch_details[$row[csf('batch_id')]]; ?></p></td>
                            <td width="90"><? echo $knitting_source[$row[csf('knitting_source')]]; ?></td>
                            <td width="100"><p><? echo $dye_company; ?></p></td>
                            <td width="90" align="right"><? echo number_format($row[csf('quantity')],2); ?></td>
                            <td><p><? echo $product_details[$row[csf('prod_id')]]; ?></p></td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tfoot>
                        <th colspan="7" align="right">Total</th>
                        <th align="right"><? echo number_format($total_fabric_recv_qnty,2); ?></th>
                        <th>&nbsp;</th>
                    </tfoot>
                </table>
            </div>
        </div>
	</fieldset>
<?
exit();
}

if($action=="fabric_purchase")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$order_id=explode('_',$order_id);
	$head_cap="";
	$po_arr=return_library_array( "select id, po_number from wo_po_break_down where id in($order_id[0])", "id", "po_number");
	$issue_arr=return_library_array( "select id, issue_number from inv_issue_master", "id", "issue_number");
	$store_arr=return_library_array( "select id, store_name from lib_store_location", "id", "store_name");
	$batch_arr=return_library_array( "select id, batch_no from pro_batch_create_mst", "id", "batch_no");
	if($order_id[1]==9)
	{
		$receive_basis_cond=" and a.receive_basis in (9)";
		$head_cap="Production";
		$ret_rec_basis_cond=" and e.receive_basis in (9)";
	}
	else if($order_id[1]==0)
	{
		$receive_basis_cond=" and a.receive_basis<>9";
		$head_cap="Purchase";
		$ret_rec_basis_cond=" and e.receive_basis<>9";
	}
?>
	<script>
        function print_window()
        {
            document.getElementById('scroll_body').style.overflow="auto";
            document.getElementById('scroll_body').style.maxHeight="none";

            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
        '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');

            d.close();
            document.getElementById('scroll_body').style.overflowY="scroll";
            document.getElementById('scroll_body').style.maxHeight="230px";
        }
    </script>
	<div style="width:920px" align="center"><input type="button" value="Print Preview" onClick="print_window();" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:920px; margin-left:3px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="920" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="9"><b>Fabric Receive/ <? echo $head_cap; ?> Info</b></th>
				</thead>
				<thead>
                	<th width="30">SL</th>
                    <th width="120">System Id</th>
                    <th width="75">Rec. Date</th>
                    <th width="80">Rec. Basis</th>
                    <th width="90">Batch No</th>
                    <th width="90">Dyeing Source</th>
                    <th width="100">Dyeing Company</th>
                    <th width="90">Receive Qnty</th>
                    <th>Fabric Description</th>
				</thead>
             </table>
             <div style="width:920px; max-height:320px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="900" cellpadding="0" cellspacing="0">
                    <?
					//echo $order_id[1].'SS';
                    $i=1;
                    $total_fabric_recv_qnty=0; $dye_company='';
					if($order_id[1]==9)
					{
					   $sql="(SELECT a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, b.batch_id, b.prod_id, sum(c.quantity) as quantity,a.entry_form from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(17,37,68) and c.entry_form in(17,37,68) and c.po_breakdown_id in($order_id[0]) and c.color_id='$color' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $receive_basis_cond group by b.id, a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, b.batch_id, b.prod_id,a.entry_form)
					   union all
					   (
						SELECT a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, b.batch_id, b.prod_id, sum(c.quantity) as quantity,a.entry_form from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(7) and c.entry_form in(7) and c.trans_id!=0 and c.po_breakdown_id in($order_id[0]) and c.color_id='$color' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.id, a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, b.batch_id, b.prod_id,a.entry_form
					   )";
					}
					else
					{
						$sql="(select a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, b.batch_id, b.prod_id, sum(c.quantity) as quantity, a.entry_form from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(17,37,68) and c.entry_form in(17,37,68) and c.po_breakdown_id in($order_id[0]) and c.color_id='$color' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $receive_basis_cond group by b.id, a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, b.batch_id, b.prod_id, a.entry_form)";
					}
				    //echo $sql;
                    $result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

                        if($row[csf('knitting_source')]==1) $dye_company=$company_library[$row[csf('knitting_company')]];
                        else if($row[csf('knitting_source')]==3) $dye_company=$supplier_details[$row[csf('knitting_company')]];
                        else $dye_company="&nbsp;";

                        $total_fabric_recv_qnty+=$row[csf('quantity')];
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="120"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                            <td width="80"><? echo $receive_basis_arr[$row[csf('receive_basis')]]; ?></td>
                            <td width="90"><p><? if($row[csf('entry_form')] !=17) echo $batch_details[$row[csf('batch_id')]]; ?></p></td>
                            <td width="90"><? echo $knitting_source[$row[csf('knitting_source')]]; ?></td>
                            <td width="100"><p><? echo $dye_company; ?></p></td>
                            <td width="90" align="right"><? echo number_format($row[csf('quantity')],2); ?></td>
                            <td><p><? echo $product_details[$row[csf('prod_id')]]; ?></p></td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tfoot>
                        <th colspan="7" align="right">Total Receive/ <? echo $head_cap; ?>:</th>
                        <th align="right"><? echo number_format($total_fabric_recv_qnty,2); ?></th>
                        <th>&nbsp;</th>
                    </tfoot>
                </table>
                <table border="1" class="rpt_table" rules="all" width="900" cellpadding="0" cellspacing="0">
				<thead>
                	<tr>
                    	<th colspan="9">Receive Return (<? echo $head_cap; ?>)</th>
                    </tr>
                    <tr>
                    	<th width="20">SL</th>
                        <th width="115">Recieve Rtn No</th>
                        <th width="60">Return Date</th>
                        <th width="115">Recieve No</th>
                        <th width="100">Store</th>
                        <th width="100">Batch No</th>
                        <th width="170">Item Description</th>
                        <th width="100">Po No.</th>
                        <th>Return Qty</th>
                    </tr>
				</thead>
                <?
                $total_receive_return_qnty=0;
				//$sql="select a.transfer_system_id, a.transfer_date, a.challan_no, a.to_order_id, b.from_prod_id, d.product_name_details, sum(c.quantity) as transfer_qnty from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.item_category=2 and a.transfer_criteria=4 and c.trans_type=6 and c.entry_form=15 and c.po_breakdown_id in ($order_id) and c.color_id='$color' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, c.prod_id, a.transfer_system_id, a.transfer_date, a.challan_no, a.to_order_id, b.from_prod_id, d.product_name_details";

				$sql="SELECT a.issue_number, a.issue_date, a.received_mrr_no, b.store_id, b.pi_wo_batch_no, b.prod_id, c.po_breakdown_id, sum(c.quantity) as quantity, d.product_name_details,a.entry_form from inv_issue_master a, inv_transaction b, order_wise_pro_details c, product_details_master d, inv_receive_master e where a.id=b.mst_id and b.id=c.trans_id and b.prod_id=d.id and e.id=a.received_id and a.entry_form in (46,202) and c.entry_form in (46,202) and c.po_breakdown_id in($order_id[0]) and c.color_id='$color' $ret_rec_basis_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.issue_number, a.issue_date, a.received_mrr_no, b.store_id, b.pi_wo_batch_no, b.prod_id, c.po_breakdown_id, d.product_name_details,a.entry_form";
				//echo $sql;
                $result=sql_select($sql);
				foreach($result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                     <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="20"><? echo $i; ?></td>
                    	<td width="115"><? echo $row[csf('issue_number')]; ?></td>
                        <td width="60" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
                        <td width="115"><? echo $row[csf('received_mrr_no')]; ?></td>
                        <td width="100" style="word-break:break-all"><? echo $store_arr[$row[csf('store_id')]]; ?></td>
                        <td width="100" style="word-break:break-all"><? if($row[csf('entry_form')] !=202) echo $batch_arr[$row[csf('pi_wo_batch_no')]]; ?></td>
                        <td width="170" style="word-break:break-all"><? echo $row[csf('product_name_details')]; ?></td>
                        <td width="100" style="word-break:break-all"><? echo $po_arr[$row[csf('po_breakdown_id')]]; ?></td>
                        <td align="right"><? echo number_format($row[csf('quantity')],2); ?> </td>
                    </tr>
                <?
					$total_receive_return_qnty+=$row[csf('quantity')];
                	$i++;
                }
                ?>
                <tr style="font-weight:bold">
                    <td colspan="8" align="right">Total (<? echo $head_cap; ?>):</td>
                    <td align="right"><? echo number_format($total_receive_return_qnty,2); ?></td>
                </tr>
                <tfoot>
                    <th colspan="8" align="right">Net Receive (<? echo $head_cap; ?>):</th>
                    <th><? echo number_format(($total_fabric_recv_qnty-$total_receive_return_qnty),2);?></th>
                </tfoot>
            </table>
            </div>
        </div>
	</fieldset>
	<?
    exit();
}

if($action=="finish_delivery_to_store")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$ex_data=explode('_',$order_id);
	$color_arr = return_library_array("select id, color_name from lib_color","id","color_name");
?>
<script>

	var tableFilters = {
						   col_operation: {
						   id: ["value_grey_used_qnty","value_delivery_qnty"],
						   col: [9,10],
						   operation: ["sum","sum"],
						   write_method: ["innerHTML","innerHTML"]
						}
					}
	$(document).ready(function(e) {
		//setFilterGrid('tbl_list_search',-1,tableFilters);
	});

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";

		//$('#tbl_list_search tr:first').hide();

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');

		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";

		//$('#tbl_list_search tr:first').show();
	}

</script>
	<div style="width:920px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:920px;">
		<div id="report_container">
			<?
				$sql_roll="SELECT a.company_id, b.sys_dtls_id, a.sys_number_prefix_num, a.sys_number, a.delevery_date, b.grey_sys_number, b.product_id, d.batch_no, b.construction, b.composition, b.gsm, b.dia, sum(b.current_delivery) as delivery_qty, c.product_name_details, c.color,b.order_id from pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b, product_details_master c, pro_batch_create_mst d where a.id=b.mst_id and b.product_id=c.id and b.batch_id=d.id and a.entry_form in (67) and b.order_id in ($ex_data[0]) and c.color='$ex_data[1]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.company_id, b.sys_dtls_id, a.sys_number_prefix_num, a.sys_number, a.delevery_date, b.grey_sys_number, b.product_id, d.batch_no, b.construction, b.composition, b.gsm, b.dia, c.product_name_details, c.color,b.order_id";
				$result_roll=sql_select($sql_roll);

				$sql_gross="SELECT a.company_id, b.sys_dtls_id, a.sys_number_prefix_num, a.sys_number, a.delevery_date, b.grey_sys_number, b.product_id, d.batch_no, b.construction, b.composition, b.gsm, b.dia, sum(b.current_delivery) as delivery_qty, c.product_name_details, c.color,b.order_id from pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b, product_details_master c, pro_batch_create_mst d where a.id=b.mst_id and b.product_id=c.id and b.batch_id=d.id and a.entry_form in (54) and b.order_id in ($ex_data[0]) and c.color='$ex_data[1]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.company_id, b.sys_dtls_id, a.sys_number_prefix_num, a.sys_number, a.delevery_date, b.grey_sys_number, b.product_id, d.batch_no, b.construction, b.composition, b.gsm, b.dia, c.product_name_details, c.color,b.order_id";
				$result_gross=sql_select($sql_gross);

				$company_id = ($result_roll[0][csf("company_id")]) ? $result_roll[0][csf("company_id")]:$result_gross[0][csf("company_id")];
				$process_loss_method_variable	=sql_select("select process_loss_method from variable_order_tracking where company_name=$company_id and variable_list=18 and item_category_id=2 and status_active =1");
				$process_loss_method = ($process_loss_method_variable[0][csf("process_loss_method")] ==2) ? 2: 1;

				$fin_production=sql_select("SELECT b.id as dtls_id, c.po_breakdown_id, c.process_loss_perc from pro_finish_fabric_rcv_dtls b, order_wise_pro_details c where b.id=c.dtls_id and c.entry_form=7 and c.po_breakdown_id in ($ex_data[0]) and b.color_id='$ex_data[1]' and c.trans_type=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.id, b.color_id, c.process_loss_perc, c.po_breakdown_id");

				foreach ($fin_production as $val) {
					$production_process_loss_arr[$val[csf('dtls_id')]][$val[csf('po_breakdown_id')]]= $val[csf('process_loss_perc')];
				}

				if(!empty($result_roll))
				{
					$result = $result_roll;
				}
				else
				{
					$result = $result_gross;
				}
			?>
			<table border="1" class="rpt_table" rules="all" width="920" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="11"><b>Finish Delivery To Store Info</b></th>
				</thead>
				<thead>
                	<th width="30">SL</th>
                    <th width="115">Challan No</th>
                    <th width="70">Delivery Date</th>
                    <th width="115">Production ID</th>
                    <th width="100">Batch No</th>
                    <th width="160">Product Details</th>
                    <th width="50">GSM</th>
                    <th width="50">Dia</th>
                    <th width="70">Color</th>
                    <th width="80">Grey Used Qnty</th>
                    <th width="70">Delivery Qnty</th>
				</thead>
             </table>
             <div style="width:940px; max-height:330px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="920" cellpadding="0" cellspacing="0" id="tbl_list_search">
                    <?
                    $i=1; $total_delivery_fin_qnty=0;

        			foreach($result as $row)
                    {
						$process_loss_perc = $production_process_loss_arr[$row[csf('sys_dtls_id')]][$row[csf('order_id')]];
						if($process_loss_perc=="")
						{
							$process_loss_perc=0;
						}
						if($process_loss_method==1)
						{
							$grey_used_qty = $row[csf('delivery_qty')] + ($process_loss_perc*$row[csf('delivery_qty')])/100;
						}
						else
						{
							$grey_used_qty = $row[csf('delivery_qty')] / ( 1- $process_loss_perc/100);
						}

                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="115"><p><? echo $row[csf('sys_number')]; ?></p></td>
                            <td width="70"><p><? echo change_date_format($row[csf('delevery_date')]); ?></p></td>
                            <td width="115"><p><? echo $row[csf('grey_sys_number')]; ?></p></td>
                            <td width="100"><p><? echo $row[csf('batch_no')]; ?></p></td>
                            <td width="160"><p><? echo $row[csf('product_name_details')]; ?>&nbsp;</p></td>
                            <td width="50"><? echo $row[csf('gsm')]; ?></td>
                            <td width="50"><? echo $row[csf('dia')]; ?></td>
                            <td width="70"><p><? echo $color_arr[$row[csf('color')]]; ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($grey_used_qty,2); ?></p></td>
                            <td width="70" align="right"><p><? echo number_format($row[csf('delivery_qty')],2,'.',''); ?></p></td>
                        </tr>
                    <?
					$total_grey_used_qty+=$grey_used_qty;
					$total_delivery_fin_qnty+=$row[csf('delivery_qty')];
                    $i++;
                    }
                    ?>
					<tr bgcolor="#8DAFDA">
					<th colspan="11" style="color: #423d39;font-size: 13px">Finish Delivery To Store Info-Reprocess</th>
					</tr>

				<?
			if(!empty($result_roll) && !empty($result_gross))
			{
				foreach($result_gross as $row)
				{
					$process_loss_perc = $production_process_loss_arr[$row[csf('sys_dtls_id')]][$row[csf('order_id')]];
					if($process_loss_perc=="")
					{
						$process_loss_perc=0;
					}
					if($process_loss_method==1)
					{
						$grey_used_qty = $row[csf('delivery_qty')] + ($process_loss_perc*$row[csf('delivery_qty')])/100;
					}
					else
					{
						$grey_used_qty = $row[csf('delivery_qty')] / ( 1- $process_loss_perc/100);
					}

					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
						<td width="30"><? echo $i; ?></td>
						<td width="115"><p><? echo $row[csf('sys_number')]; ?></p></td>
						<td width="70"><p><? echo change_date_format($row[csf('delevery_date')]); ?></p></td>
						<td width="115"><p><? echo $row[csf('grey_sys_number')]; ?></p></td>
						<td width="100"><p><? echo $row[csf('batch_no')]; ?></p></td>
						<td width="160"><p><? echo $row[csf('product_name_details')]; ?>&nbsp;</p></td>
						<td width="50"><? echo $row[csf('gsm')]; ?></td>
						<td width="50"><? echo $row[csf('dia')]; ?></td>
						<td width="70"><? echo $color_arr[$row[csf('color')]]; ?></td>
						<td width="80" align="right"><? echo number_format($grey_used_qty,2); ?></td>
						<td width="70" align="right"><? echo number_format($row[csf('delivery_qty')],2,'.',''); ?></td>
					</tr>
				<?
				$total_grey_used_qty+=$grey_used_qty;
				$total_delivery_fin_qnty+=$row[csf('delivery_qty')];
				$i++;
				}
			}
			?>
			</table>
            </div>
            <table border="1" class="rpt_table" rules="all" width="920" cellpadding="0" cellspacing="0">
                <tfoot>
                    <th width="30">&nbsp;</th>
                    <th width="115">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="115">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="160">&nbsp;</th>
                    <th width="50">&nbsp;</th>
                    <th width="50">&nbsp;</th>
                    <th width="70" align="right">Total</th>
                    <th width="80" align="right" id="value_grey_used_qnty"><? echo number_format($total_grey_used_qty,2,'.','');?></th>
                    <th width="70" align="right" id="value_delivery_qnty"><? echo number_format($total_delivery_fin_qnty,2,'.',''); ?></th>
                </tfoot>
            </table>
        </div>
	</fieldset>
<?
exit();
}

if($action=="issue_to_cut")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?>
<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');

		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
	}

</script>
	<div style="width:740px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
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
                    $sql_fin="SELECT a.id as issue_id,a.issue_number, a.issue_date, b.batch_id, b.prod_id, sum(c.quantity) as quantity, a.challan_no, a.entry_form
                    from inv_issue_master a, inv_finish_fabric_issue_dtls b, order_wise_pro_details c
                    where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in (18,71) and c.entry_form in (18,71) and c.po_breakdown_id in($order_id) and c.color_id='$color' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
                    group by a.id, b.id, a.issue_number, a.issue_date, a.challan_no, b.batch_id, b.prod_id, a.entry_form";

					$sql_woven="SELECT a.id as issue_id,a.issue_number, a.issue_date, b.batch_id, b.prod_id, sum(c.quantity) as quantity, a.challan_no, a.entry_form
					from inv_issue_master a, inv_wvn_finish_fab_iss_dtls b, order_wise_pro_details c
					where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in (19) and c.entry_form in (19) and c.po_breakdown_id in($order_id) and c.color_id='$color' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
					group by a.id, b.id, a.issue_number, a.issue_date, a.challan_no, b.batch_id, b.prod_id, a.entry_form";

					//echo $sql_woven;
                    $result_fin=sql_select($sql_fin);
                    $result_woven=sql_select($sql_woven);
					$result  = array_merge( $result_fin,$result_woven);

                    $issue_number_id="";
					foreach($result as $rows)
				    {
				    	if($issue_number_id=="") $issue_number_id=$rows[csf('issue_id')]; else $issue_number_id.=",".$rows[csf('issue_id')];
				    }
				    $issueIds=implode(",",array_unique(explode(",",chop($issue_number_id,','))));
				    $issue_id_cond="";
					$issue_ids=count(array_unique(explode(",",$issue_number_id)));
					if($db_type==2 && $issue_ids>1000)
					{
						$issue_id_cond=" and (";
						$issueIdsArr=array_chunk(array_unique(explode(",",$issueIds)),999);
						foreach($issueIdsArr as $ids)
						{
							$ids=implode(",",$ids);
							$issue_id_cond.=" a.id in($ids) or";
						}
						$issue_id_cond=chop($issue_id_cond,'or ');
						$issue_id_cond.=")";
					}
					else
					{
						$issue_id_cond=" and a.id in($issueIds)";
					}
					$batch="SELECT a.id, b.pi_wo_batch_no, a.entry_form from inv_issue_master a, inv_transaction b where a.id=b.mst_id and a.entry_form=71 and b.transaction_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $issue_id_cond";
					$batch_result=sql_select($batch);
					$batch_no_Arr = array();
					foreach($batch_result as $rows)
                    {
                    	$batch_no_Arr[$rows[csf('id')]]['pi_wo_batch_no']=$rows[csf('pi_wo_batch_no')];
                    }

        			foreach($result as $row)
                    {
                        if ($i%2==0)
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";
                    	if ($row[csf('entry_form')] == 71)
                    	{
                    		$batch_id=$batch_details[$batch_no_Arr[$row[csf('issue_id')]]['pi_wo_batch_no']];
                    	}
                    	else{
                    		$batch_id=$batch_details[$row[csf('batch_id')]];
                    	}
                        $total_issue_to_cut_qnty+=$row[csf('quantity')];
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="50"><? echo $i; ?></td>
                            <td width="120"><p><? echo $row[csf('issue_number')]; ?></p></td>
                            <td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td width="80" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
                            <td width="120"><p><? if($row[csf('entry_form')] !=19) echo $batch_id; ?></p></td>
                            <td width="110" align="right"><? echo number_format($row[csf('quantity')],2); ?></td>
                            <td><p><? echo $product_details[$row[csf('prod_id')]]; ?></p></td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tfoot>
                        <th colspan="5" align="right">Total Issue</th>
                        <th align="right"><? echo number_format($total_issue_to_cut_qnty,2); ?></th>
                        <th>&nbsp;</th>
                    </tfoot>
                </table>
            </div>
            <table border="1" class="rpt_table" rules="all" width="730" cellpadding="0" cellspacing="0">
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
                 <table border="1" class="rpt_table" rules="all" width="720" cellpadding="0" cellspacing="0">
                    <?
                    $j=1; $total_ret_qnty=0;
                    $sql_ret="SELECT a.recv_number, a.receive_date, b.batch_id_from_fissuertn, b.prod_id, sum(c.quantity) as quantity, a.challan_no, a.entry_form from inv_receive_master a, inv_transaction b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.trans_id and a.entry_form in (46,52,126,209) and c.entry_form in (46,52,126,209) and b.transaction_type in (3,4) and c.po_breakdown_id in($order_id) and c.color_id='$color' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.id, a.recv_number, a.receive_date, a.challan_no, b.batch_id_from_fissuertn, b.prod_id, a.entry_form";
					//echo $sql_ret;
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
                            <td width="120"><p><? if($row[csf('entry_form')] !=209) echo $batch_details[$row[csf('batch_id_from_fissuertn')]]; ?></p></td>
                            <td width="110" align="right"><? echo number_format($row[csf('quantity')],2); ?></td>
                            <td><p><? echo $product_details[$row[csf('prod_id')]]; ?></p></td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tfoot>
                        <tr>

                            <th colspan="5" align="right">Total Return</th>
                            <th align="right"><? echo number_format($total_ret_qnty,2); ?></th>
                            <th>&nbsp;</th>
                        </tr>
                        <tr>
                            <th colspan="5" align="right">Total Issue to Cut</th>
                            <th align="right"><? $tot_iss_to_cut=$total_issue_to_cut_qnty-$total_ret_qnty; echo number_format($tot_iss_to_cut,2); ?></th>
                            <th>&nbsp;</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
	</fieldset>
<?
exit();
}

if($action=="yarn_trans")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$po_arr=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number"  );
	$sqlWO="select a.fabric_source,b.po_break_down_id,a.booking_no from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.po_break_down_id in ($order_id)";
	 $resultWo=sql_select($sqlWO);
	 $fab_source_ids="";
		foreach($resultWo as $woRow)
		{
			 $fab_source_ids.=$woRow[csf('fabric_source')].',';
		}
		$fab_source=rtrim($fab_source_ids,',');
		$fab_source_id=array_unique(explode(",",$fab_source));
?>
<script>

	function print_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');

		d.close();
	}

</script>
	<div style="width:675px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:670px; margin-left:7px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0">
				<thead>
                	<tr>
                    	<th colspan="6">Transfer In</th>
                    </tr>
                    <tr>
                    	<th width="40">SL</th>
                        <th width="115">Transfer Id</th>
                        <th width="80">Transfer Date</th>
                        <th width="100">From Order</th>
                        <th width="170">Item Description</th>
                        <th>Transfer Qnty</th>
                    </tr>
				</thead>
                <?
                $i=1; $total_trans_in_qnty=0;
				$sql="select a.transfer_system_id, a.transfer_date, a.challan_no, a.from_order_id, b.from_prod_id, sum(c.quantity) as transfer_qnty, d.product_name_details from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.item_category=1 and a.transfer_criteria=4 and c.trans_type=5 and c.entry_form=11 and c.po_breakdown_id in ($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, c.prod_id, a.transfer_system_id, a.transfer_date, a.challan_no, a.from_order_id, b.from_prod_id, d.product_name_details";
                $result=sql_select($sql);
				foreach($result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					foreach($fab_source_id as $fsid)
					{
						if($fsid==1)
						{
							$row[csf('transfer_qnty')]=$row[csf('transfer_qnty')];
						}

					}
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="40"><? echo $i; ?></td>
                    	<td width="115"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('transfer_date')]); ?></td>
                        <td width="100"><p><? echo $po_arr[$row[csf('from_order_id')]]; ?></p></td>
                        <td width="170"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                        <td align="right"><? echo number_format($row[csf('transfer_qnty')],2); ?> </td>
                    </tr>
                <?
					$total_trans_in_qnty+=$row[csf('transfer_qnty')];
                $i++;
                }
                ?>
                <tr style="font-weight:bold">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right">Total</td>
                    <td align="right"><? echo number_format($total_trans_in_qnty,2);?></td>
                </tr>
                <thead>
                	<tr>
                    	<th colspan="6">Transfer Out</th>
                    </tr>
                    <tr>
                    	<th width="40">SL</th>
                        <th width="115">Transfer Id</th>
                        <th width="80">Transfer Date</th>
                        <th width="100">To Order</th>
                        <th width="170">Item Description</th>
                        <th>Transfer Qnty</th>
                    </tr>
				</thead>
                <?
                $total_trans_out_qnty=0;
				$sql="select a.transfer_system_id, a.transfer_date, a.challan_no, a.to_order_id, b.from_prod_id, sum(c.quantity) as transfer_qnty, d.product_name_details from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.item_category=1 and a.transfer_criteria=4 and c.trans_type=6 and c.entry_form=11 and c.po_breakdown_id in ($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, c.prod_id, a.transfer_system_id, a.transfer_date, a.challan_no, a.to_order_id, b.from_prod_id, d.product_name_details";
                $result=sql_select($sql);
				foreach($result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					foreach($fab_source_id as $fsid)
					{
						if($fsid==1)
						{
							$row[csf('transfer_qnty')]=$row[csf('transfer_qnty')];
						}
					}
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="40"><? echo $i; ?></td>
                    	<td width="115"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('transfer_date')]); ?></td>
                        <td width="100"><p><? echo $po_arr[$row[csf('to_order_id')]]; ?></p></td>
                        <td width="170"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                        <td align="right"><? echo number_format($row[csf('transfer_qnty')],2); ?> </td>
                    </tr>
                <?
					$total_trans_out_qnty+=$row[csf('transfer_qnty')];
                $i++;
                }
                ?>
                <tr style="font-weight:bold">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right">Total</td>
                    <td align="right"><? echo number_format($total_trans_out_qnty,2); ?></td>
                </tr>
                <tfoot>
                	<th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>Net Transfer</th>
                    <th><? echo number_format($total_trans_in_qnty-$total_trans_out_qnty,2);?></th>
                </tfoot>
            </table>
		</div>
	</fieldset>
	<?
    exit();
}

if($action=="knit_trans")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);

	$sqlWO="select a.fabric_source,b.po_break_down_id,a.booking_no from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.po_break_down_id in ($order_id)";
	 $resultWo=sql_select($sqlWO);
	 $fab_source_ids="";
		foreach($resultWo as $woRow)
		{
			 $fab_source_ids.=$woRow[csf('fabric_source')].',';
		}
		$fab_source=rtrim($fab_source_ids,',');
		$fab_source_id=array_unique(explode(",",$fab_source));
?>

<script>

	function print_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');

		d.close();
	}

</script>
	<div style="width:675px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:770px; margin-left:7px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="750" cellpadding="0" cellspacing="0">
				<thead>
                	<tr>
                    	<th colspan="7">Transfer In</th>
                    </tr>
                    <tr>
                    	<th width="40">SL</th>
                        <th width="115">Transfer Id</th>
                        <th width="80">Transfer Date</th>
                        <th width="100">From Order</th>
                        <th width="100">Internal ref</th>
                        <th width="170">Item Description</th>
                        <th>Transfer Qnty</th>
                    </tr>
				</thead>
                <?
                $i=1; $total_trans_in_qnty=0;
				$sql="SELECT a.transfer_system_id, a.transfer_date, a.challan_no, a.from_order_id, b.from_prod_id, sum(c.quantity) as transfer_qnty, d.product_name_details,a.from_samp_dtls_id
				from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d
				where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.item_category=13 and c.trans_type=5 and c.entry_form in (13,81,83,183) and c.po_breakdown_id in ($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
				group by a.id, c.prod_id, a.transfer_system_id, a.transfer_date, a.challan_no, a.from_order_id, b.from_prod_id, d.product_name_details,a.from_samp_dtls_id
				union all
				SELECT a.transfer_system_id, a.transfer_date, a.challan_no, b.from_order_id, b.from_prod_id, sum(c.quantity) as transfer_qnty, d.product_name_details,a.from_samp_dtls_id
				from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d
				where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.item_category=13 and c.trans_type=5 and c.entry_form in (82) and c.po_breakdown_id in ($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
				group by a.id, c.prod_id, a.transfer_system_id, a.transfer_date, a.challan_no, b.from_order_id, b.from_prod_id, d.product_name_details,a.from_samp_dtls_id

				";
				 //echo $sql;
                $result=sql_select($sql);
                $trans_out_from_booking_id = array();
                $fromOrders="";
                foreach($result as $row)
				{
					if(isset($row[csf('from_samp_dtls_id')]))
					{
						$trans_out_from_booking_id[$row[csf('from_samp_dtls_id')]] = $row[csf('from_samp_dtls_id')];
					}
					$fromOrders.=$row[csf('from_order_id')].",";
				}
				$fromOrders=chop($fromOrders,",");

				$po_sql="SELECT id, po_number,grouping from wo_po_break_down where id in($fromOrders)";
				$po_data=sql_select($po_sql);
			    foreach($po_data as $row)
				{
					$po_arr[$row[csf('id')]]['po_number']=$row[csf('po_number')];
					$po_arr[$row[csf('id')]]['grouping']=$row[csf('grouping')];

				}

				$non_ord_booking_id_from = implode(",", $trans_out_from_booking_id);
				$wo_non_ord_bookin_no_from = return_library_array( "SELECT id, booking_no from wo_non_ord_samp_booking_dtls where id in($non_ord_booking_id_from)", "id", "booking_no"  );

				foreach($result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

					foreach($fab_source_id as $fsid)
					{
						if($fsid==1)
						{
							$row[csf('transfer_qnty')]=$row[csf('transfer_qnty')];
						}
					}
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="40"><? echo $i; ?></td>
                    	<td width="115"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('transfer_date')]); ?></td>
                        <td width="100"><p>
                        <?
                        if($row[csf('from_samp_dtls_id')])
                        {
                         	echo $wo_non_ord_bookin_no_from[$row[csf('from_samp_dtls_id')]];
                        }
                        else
                        {
                        	echo $po_arr[$row[csf('from_order_id')]]['po_number'];
                        }
                        ?>
                        </p></td>
                        <td width="100"><p><? echo $po_arr[$row[csf('from_order_id')]]['grouping']; ?></p></td>
                        <td width="170"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                        <td align="right"><? echo number_format($row[csf('transfer_qnty')],2); ?> </td>
                    </tr>
                <?
					$total_trans_in_qnty+=$row[csf('transfer_qnty')];
                $i++;
                }
                ?>
                <tr style="font-weight:bold">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right">Total</td>
                    <td align="right"><? echo number_format($total_trans_in_qnty,2);?></td>
                </tr>
                <thead>
                	<tr>
                    	<th colspan="8">Transfer Out</th>
                    </tr>
                    <tr>
                    	<th width="40">SL</th>
                        <th width="115">Transfer Id</th>
                        <th width="80">Transfer Date</th>
                        <th width="100">To Order</th>
                        <th width="100">To Job</th>
                        <th width="100">Internal Ref</th>
                        <th width="170">Item Description</th>
                        <th>Transfer Qnty</th>
                    </tr>
				</thead>
                <?
                $total_trans_out_qnty=0;

				$sql="SELECT a.transfer_system_id, a.transfer_date, a.challan_no, a.to_order_id, b.from_prod_id,b.to_prod_id, sum(c.quantity) as transfer_qnty,a.to_samp_dtls_id
				from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c
				where a.id=b.mst_id and b.id=c.dtls_id and a.item_category=13 and c.trans_type=6 and c.entry_form in (13,80,83,110) and c.po_breakdown_id in ($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
				group by a.id, c.prod_id, a.transfer_system_id, a.transfer_date, a.challan_no, a.to_order_id, b.from_prod_id, b.to_prod_id,a.to_samp_dtls_id
				union all
				SELECT a.transfer_system_id, a.transfer_date, a.challan_no, b.to_order_id, b.from_prod_id,b.to_prod_id, sum(c.quantity) as transfer_qnty,a.to_samp_dtls_id
				from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c
				where a.id=b.mst_id and b.id=c.dtls_id and a.item_category=13 and c.trans_type=6 and c.entry_form in (82) and c.po_breakdown_id in ($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
				group by a.id, c.prod_id, a.transfer_system_id, a.transfer_date, a.challan_no, b.to_order_id, b.from_prod_id, b.to_prod_id,a.to_samp_dtls_id
				";
				 //echo $sql;
                $result=sql_select($sql);
                // ================= getting non order booking id ===================
                $trans_out_to_booking_id = array();
                $toOrders="";$toProds="";
                foreach($result as $row)
				{
					if(isset($row[csf('to_samp_dtls_id')]))
					{
						$trans_out_to_booking_id[$row[csf('to_samp_dtls_id')]] = $row[csf('to_samp_dtls_id')];
					}
					$toOrders.=$row[csf('to_order_id')].",";
					$toProds.=$row[csf('to_prod_id')].",";
				}
				$toOrders=chop($toOrders,",");
				$toProds=chop($toProds,",");


				$prod_inf_sql_out=sql_select("SELECT id, product_name_details from product_details_master where id in($toProds)");
				foreach($prod_inf_sql_out as $row)
				{
					$prod_arrs[$row[csf('id')]]['product_name_details']=$row[csf('product_name_details')];
				}

				$po_sql_out="SELECT id, po_number,grouping,job_no_mst from wo_po_break_down where id in($toOrders)";
				$po_data_out=sql_select($po_sql_out);
			    foreach($po_data_out as $row)
				{
					$po_arrs[$row[csf('id')]]['po_number']=$row[csf('po_number')];
					$po_arrs[$row[csf('id')]]['grouping']=$row[csf('grouping')];
					$po_arrs[$row[csf('id')]]['job_no']=$row[csf('job_no_mst')];

				}


				$non_ord_booking_id_to = implode(",", $trans_out_to_booking_id);
				$wo_non_ord_bookin_no_to = return_library_array( "SELECT id, booking_no from wo_non_ord_samp_booking_dtls where id in($non_ord_booking_id_to)", "id", "booking_no"  );

				foreach($result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					foreach($fab_source_id as $fsid)
					{
						if($fsid==1)
						{
							$row[csf('transfer_qnty')]=$row[csf('transfer_qnty')];
						}

					}
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="40"><? echo $i; ?></td>
                    	<td width="115"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('transfer_date')]); ?></td>
                        <td width="100"><p>
                        <?
                        if($row[csf('to_samp_dtls_id')])
                        {
                        	echo $wo_non_ord_bookin_no_to[$row[csf('to_samp_dtls_id')]];
                        }
                        else
                        {
                        	echo $po_arrs[$row[csf('to_order_id')]]['po_number'];
                        }
                        ?>
                        <td width="100"><p><? echo $po_arrs[$row[csf('to_order_id')]]['job_no'];; ?></p></td>
                        </p></td>
                        <td width="100"><p><? echo $po_arrs[$row[csf('to_order_id')]]['grouping'];; ?></p></td>
                        <td width="170"><p><? echo $prod_arrs[$row[csf('to_prod_id')]]['product_name_details']; //$row[csf('product_name_details')]; ?></p></td>
                        <td align="right"><? echo number_format($row[csf('transfer_qnty')],2); ?> </td>
                    </tr>
                <?
					$total_trans_out_qnty+=$row[csf('transfer_qnty')];
                $i++;
                }
                ?>
                <tr style="font-weight:bold">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right">Total</td>
                    <td align="right"><? echo number_format($total_trans_out_qnty,2); ?></td>
                </tr>
                <tfoot>
                	<th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>Net Transfer</th>
                    <th><? echo number_format($total_trans_in_qnty-$total_trans_out_qnty,2);?></th>
                </tfoot>
            </table>
		</div>
	</fieldset>
<?
exit();
}

if($action=="finish_return")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$po_arr=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number");
	$issue_arr=return_library_array( "select id, issue_number from inv_issue_master", "id", "issue_number");
	$store_arr=return_library_array( "select id, store_name from lib_store_location", "id", "store_name");
	$batch_arr=return_library_array( "select id, batch_no from pro_batch_create_mst", "id", "batch_no");
?>
<script>

	function print_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');

		d.close();
	}

</script>
	<div style="width:965px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:960px; margin-left:7px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="950" cellpadding="0" cellspacing="0">
				<thead>
                	<tr>
                    	<th colspan="9">Issue Return</th>
                    </tr>
                    <tr>
                    	<th width="20">SL</th>
                        <th width="115">Issue Rtn No</th>
                        <th width="60">Return Date</th>
                        <th width="115">Issue No</th>
                        <th width="100">Store</th>
                        <th width="100">Batch No</th>
                        <th width="170">Item Description</th>
                        <th width="100">Po No.</th>
                        <th>Return Qty</th>
                    </tr>
				</thead>
                <?
                $i=1; $total_issue_return_qnty=0;
				$sql="select a.recv_number, a.receive_date, a.issue_id, b.store_id, b.pi_wo_batch_no, b.prod_id, c.po_breakdown_id, sum(c.quantity) as quantity, d.product_name_details from inv_receive_master a, inv_transaction b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.trans_id and b.prod_id=d.id and a.entry_form in (52,126) and c.entry_form in (52,126) and c.po_breakdown_id in($order_id) and c.color_id='$color' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.recv_number, a.receive_date, a.issue_id, b.store_id, b.pi_wo_batch_no, b.prod_id, c.po_breakdown_id, d.product_name_details";
				//echo $sql;
                $result=sql_select($sql);
				foreach($result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="20"><? echo $i; ?></td>
                    	<td width="115"><? echo $row[csf('recv_number')]; ?></td>
                        <td width="60" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                        <td width="115"><? echo $issue_arr[$row[csf('issue_id')]]; ?></td>
                        <td width="100" style="word-break:break-all"><? echo $store_arr[$row[csf('store_id')]]; ?></td>
                        <td width="100" style="word-break:break-all"><? echo $batch_arr[$row[csf('pi_wo_batch_no')]]; ?></td>
                        <td width="170" style="word-break:break-all"><? echo $row[csf('product_name_details')]; ?></td>
                        <td width="100" style="word-break:break-all"><? echo $po_arr[$row[csf('po_breakdown_id')]]; ?></td>
                        <td align="right"><? echo number_format($row[csf('quantity')],2); ?> </td>
                    </tr>
                <?
					$total_issue_return_qnty+=$row[csf('quantity')];
                $i++;
                }
                ?>
                <tr style="font-weight:bold">
                    <td colspan="8" align="right">Total</td>
                    <td align="right"><? echo number_format($total_issue_return_qnty,2);?></td>
                </tr>
                </table>
                <table border="1" class="rpt_table" rules="all" width="950" cellpadding="0" cellspacing="0">
				<thead>
                	<tr>
                    	<th colspan="9">Receive Return</th>
                    </tr>
                    <tr>
                    	<th width="20">SL</th>
                        <th width="115">Recieve Rtn No</th>
                        <th width="60">Return Date</th>
                        <th width="115">Recieve No</th>
                        <th width="100">Store</th>
                        <th width="100">Batch No</th>
                        <th width="170">Item Description</th>
                        <th width="100">Po No.</th>
                        <th>Return Qty</th>
                    </tr>
				</thead>
                <?
                $total_receive_return_qnty=0;
				//$sql="select a.transfer_system_id, a.transfer_date, a.challan_no, a.to_order_id, b.from_prod_id, d.product_name_details, sum(c.quantity) as transfer_qnty from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.item_category=2 and a.transfer_criteria=4 and c.trans_type=6 and c.entry_form=15 and c.po_breakdown_id in ($order_id) and c.color_id='$color' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, c.prod_id, a.transfer_system_id, a.transfer_date, a.challan_no, a.to_order_id, b.from_prod_id, d.product_name_details";
				$sql="select a.issue_number, a.issue_date, a.received_mrr_no, b.store_id, b.pi_wo_batch_no, b.prod_id, c.po_breakdown_id, sum(c.quantity) as quantity, d.product_name_details from inv_issue_master a, inv_transaction b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.trans_id and b.prod_id=d.id and a.entry_form in (46) and c.entry_form in (46) and c.po_breakdown_id in($order_id) and c.color_id='$color' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.issue_number, a.issue_date, a.received_mrr_no, b.store_id, b.pi_wo_batch_no, b.prod_id, c.po_breakdown_id, d.product_name_details";
                $result=sql_select($sql);
				foreach($result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                     <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="20"><? echo $i; ?></td>
                    	<td width="115"><? echo $row[csf('issue_number')]; ?></td>
                        <td width="60" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
                        <td width="115"><? echo $row[csf('received_mrr_no')]; ?></td>
                        <td width="100" style="word-break:break-all"><? echo $store_arr[$row[csf('store_id')]]; ?></td>
                        <td width="100" style="word-break:break-all"><? echo $batch_arr[$row[csf('pi_wo_batch_no')]]; ?></td>
                        <td width="170" style="word-break:break-all"><? echo $row[csf('product_name_details')]; ?></td>
                        <td width="100" style="word-break:break-all"><? echo $po_arr[$row[csf('po_breakdown_id')]]; ?></td>
                        <td align="right"><? echo number_format($row[csf('quantity')],2); ?> </td>
                    </tr>
                <?
					$total_receive_return_qnty+=$row[csf('quantity')];
                	$i++;
                }
                ?>
                <tr style="font-weight:bold">
                    <td colspan="8" align="right">Total</td>
                    <td align="right"><? echo number_format($total_receive_return_qnty,2); ?></td>
                </tr>
                <tfoot>
                    <th colspan="8" align="right">Net Transfer</th>
                    <th><? echo number_format($total_issue_return_qnty-$total_receive_return_qnty,2);?></th>
                </tfoot>
            </table>
		</div>
	</fieldset>
<?
exit();
}

if($action=="finish_trans")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$po_arr=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number"  );
?>
<script>

	function print_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');

		d.close();
	}

</script>
	<div style="width:675px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:670px; margin-left:7px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0">
				<thead>
                	<tr>
                    	<th colspan="7">Transfer In</th>
                    </tr>
                    <tr>
                    	<th width="40">SL</th>
                        <th width="115">Transfer Id</th>
                        <th width="80">Transfer Date</th>
                        <th width="100">From Order</th>
                        <th width="100">Internal Ref.</th>
                        <th width="170">Item Description</th>
                        <th>Transfer Qnty</th>
                    </tr>
				</thead>
                <?
                $i=1; $total_trans_in_qnty=0;
				$sql="SELECT a.transfer_system_id, a.transfer_date, a.challan_no, a.from_order_id, b.from_prod_id, d.product_name_details, sum(c.quantity) as transfer_qnty from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.item_category=2 and a.transfer_criteria=4 and c.trans_type=5 and c.entry_form in(14,15,134) and c.po_breakdown_id in ($order_id) and c.color_id='$color' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, c.prod_id, a.transfer_system_id, a.transfer_date, a.challan_no, a.from_order_id, b.from_prod_id, d.product_name_details";
                $result=sql_select($sql);
                $fromOrders="";
				foreach($result as $row)
				{
					$fromOrders.=$row[csf('from_order_id')].",";
				}
				$fromOrders=chop($fromOrders,",");
                $po_sql="SELECT id, po_number,grouping from wo_po_break_down where id in($fromOrders)";
				$po_data=sql_select($po_sql);
			    foreach($po_data as $row)
				{
					$po_arr[$row[csf('id')]]['po_number']=$row[csf('po_number')];
					$po_arr[$row[csf('id')]]['grouping']=$row[csf('grouping')];

				}
				foreach($result as $row)
				{
					if ($i%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="40"><? echo $i; ?></td>
                    	<td width="115"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('transfer_date')]); ?></td>
                        <td width="100"><p><? echo $po_arr[$row[csf('from_order_id')]]['po_number']; ?></p></td>
                        <td width="100"><p><? echo $po_arr[$row[csf('from_order_id')]]['grouping']; ?></p></td>
                        <td width="170"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                        <td align="right"><? echo number_format($row[csf('transfer_qnty')],2); ?> </td>
                    </tr>
                <?
					$total_trans_in_qnty+=$row[csf('transfer_qnty')];
                $i++;
                }
                ?>
                <tr style="font-weight:bold">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right">Total</td>
                    <td align="right"><? echo number_format($total_trans_in_qnty,2);?></td>
                </tr>
                <thead>
                	<tr>
                    	<th colspan="7">Transfer Out</th>
                    </tr>
                    <tr>
                    	<th width="40">SL</th>
                        <th width="115">Transfer Id</th>
                        <th width="80">Transfer Date</th>
                        <th width="100">To Order</th>
                        <th width="100">Internal Ref.</th>
                        <th width="170">Item Description</th>
                        <th>Transfer Qnty</th>
                    </tr>
				</thead>
                <?
                $total_trans_out_qnty=0;
				$sql="SELECT a.transfer_system_id, a.transfer_date, a.challan_no, a.to_order_id, b.from_prod_id, d.product_name_details, sum(c.quantity) as transfer_qnty from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.item_category=2 and a.transfer_criteria=4 and c.trans_type=6 and c.entry_form in(14,15,134) and c.po_breakdown_id in ($order_id) and c.color_id='$color' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, c.prod_id, a.transfer_system_id, a.transfer_date, a.challan_no, a.to_order_id, b.from_prod_id, d.product_name_details";
				// echo $sql;
                $result=sql_select($sql);
                $toOrders="";
                foreach($result as $row)
				{
					$toOrders.=$row[csf('id')].",";
				}
				$toOrders=chop($toOrders,",");
                $po_sql_out="SELECT id, po_number,grouping from wo_po_break_down where id in($toOrders)";
				$po_data_out=sql_select($po_sql_out);
			    foreach($po_data_out as $row)
				{
					$po_arr_out[$row[csf('id')]]['po_number']=$row[csf('po_number')];
					$po_arr_out[$row[csf('id')]]['grouping']=$row[csf('grouping')];

				}


				foreach($result as $row)
				{
					if ($i%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="40"><? echo $i; ?></td>
                    	<td width="115"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('transfer_date')]); ?></td>
                        <td width="100"><p><? echo $po_arr_out[$row[csf('to_order_id')]]['po_number']; ?></p></td>
                        <td width="100"><p><? echo $po_arr_out[$row[csf('to_order_id')]]['grouping']; ?></p></td>
                        <td width="170"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                        <td align="right"><? echo number_format($row[csf('transfer_qnty')],2); ?> </td>
                    </tr>
                <?
					$total_trans_out_qnty+=$row[csf('transfer_qnty')];
                $i++;
                }
                ?>
                <tr style="font-weight:bold">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right">Total</td>
                    <td align="right"><? echo number_format($total_trans_out_qnty,2); ?></td>
                </tr>
                <tfoot>
                	<th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>Net Transfer</th>
                    <th><? echo number_format($total_trans_in_qnty-$total_trans_out_qnty,2);?></th>
                </tfoot>
            </table>
		</div>
	</fieldset>
	<?
    exit();
}

if($action=="yarn_allocation_pop")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);

	$sqlWO="select a.fabric_source,b.po_break_down_id,a.booking_no from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.po_break_down_id in ($order_id)";
	 $resultWo=sql_select($sqlWO);
	 $fab_source_ids="";
		foreach($resultWo as $woRow)
		{
			 $fab_source_ids.=$woRow[csf('fabric_source')].',';
		}
		$fab_source=rtrim($fab_source_ids,',');
		$fab_source_id=array_unique(explode(",",$fab_source));
?>
<script>

	function print_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');

		d.close();
	}

</script>
	<div style="width:675px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:670px; margin-left:7px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                    	<th width="40">SL</th>
                        <th width="100">Lot</th>
                        <th width="60">Allocation Date</th>
                        <th width="70">Count</th>
                        <th width="200">Composition</th>
                        <th width="130">Supplier</th>
                        <th>Allocated Qty</th>
                    </tr>
				</thead>
                <?
				if($yarn_comp_type2nd!='') $yarn_comp_type2nd_cond="and c.yarn_comp_type2nd='$yarn_comp_type2nd'"; else $yarn_comp_type2nd_cond="";
				if($yarn_comp_percent2nd!='') $yarn_comp_percent2nd_cond="and c.yarn_comp_percent2nd='$yarn_comp_percent2nd'"; else $yarn_comp_percent2nd_cond="";
				if($alocate_type==1)
				{
					$alocate_cond = " and a.is_dyied_yarn in(1)";
				}
				else if($alocate_type==2)
				{
					$alocate_cond = " and a.is_dyied_yarn in(0,2)";
				}
				$sql="select a.po_break_down_id,a.allocation_date, sum(a.qnty) as allocation_qty, c.lot, c.yarn_type, c.yarn_count_id, c.product_name_details, c.supplier_id from inv_material_allocation_dtls a, product_details_master c where a.item_id=c.id and a.po_break_down_id in ($order_id) $alocate_cond and c.yarn_count_id='$yarn_count' and c.yarn_comp_type1st='$yarn_comp_type1st' and c.yarn_comp_percent1st='$yarn_comp_percent1st' $yarn_comp_type2nd_cond $yarn_comp_percent2nd_cond and c.yarn_type='$yarn_type_id' and a.status_active=1 and a.is_deleted=0 and a.item_category=1 group by a.po_break_down_id,a.allocation_date, c.lot, c.yarn_type, c.yarn_count_id, c.product_name_details, c.supplier_id";

                $total_allocation_qty=0; $i=1;
                $result=sql_select($sql);
				foreach($result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

					foreach($fab_source_id as $fsid)
					{
						if($fsid==1)
						{
							$allocation_qty=$row[csf('allocation_qty')];
						}

					}
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="40"><? echo $i; ?></td>
                    	<td width="100"><p><? echo $row[csf('lot')]; ?></p></td>
                    	<td align="center" width="60"><p><? echo change_date_format($row[csf('allocation_date')]); ?></p></td>
                        <td width="70"><? echo $yarn_count_details[$row[csf('yarn_count_id')]]; ?></td>
                        <td width="200"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                        <td width="130"><p><? echo $supplier_details[$row[csf('supplier_id')]]; ?></p></td>
                        <td align="right"><? echo number_format($allocation_qty,2); ?> </td>
                    </tr>
                <?
					$total_allocation_qty+=$allocation_qty;
					$i++;
                }
				unset($result);
                ?>
                <tfoot>
                	<th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>Total Allocation</th>
                    <th><? echo number_format($total_allocation_qty,2);?></th>
                </tfoot>
            </table>
		</div>
	</fieldset>
<?
exit();
}

if($action=="yarn_allocation_not")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$brand_array=return_library_array( "select id, brand_name from lib_brand", "id", "brand_name"  );
	$yarn_desc_array=explode(",",$yarn_count);
	//print_r($yarn_desc_array);

	$sqlWO="select a.fabric_source,b.po_break_down_id,a.booking_no from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.po_break_down_id in ($order_id)";
	 $resultWo=sql_select($sqlWO);
	 $fab_source_ids="";
		foreach($resultWo as $woRow)
		{
			 $fab_source_ids.=$woRow[csf('fabric_source')].',';
		}
		$fab_source=rtrim($fab_source_ids,',');
		$fab_source_id=array_unique(explode(",",$fab_source));

?>
<script>

	function print_window()
	{

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');

		d.close();
	}

</script>
	<div style="width:970px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:970px; margin-left:3px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                    	<th width="40">SL</th>
                        <th width="100">Lot</th>
                        <th width="60">Allocation Date</th>
                        <th width="70">Count</th>
                        <th width="200">Composition</th>
                        <th width="130">Supplier</th>
                        <th>Allocated Qty</th>
                    </tr>
				</thead>
                <?
				$i=1; $total_yarn_issue_qnty=0; $total_yarn_issue_qnty_out=0; $yarn_desc_array_for_return=array();

				$sql_yarn_iss="select b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type from inv_material_allocation_dtls a, product_details_master b where a.item_id=b.id and a.po_break_down_id in ($order_id) and b.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 group by b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type";

				$dataArrayIssue=sql_select($sql_yarn_iss);
				foreach($dataArrayIssue as $row_yarn_iss)
				{
					if($row_yarn_iss[csf('yarn_comp_percent2nd')]!=0)
					{
						$compostion_not_req=$composition[$row_yarn_iss[csf('yarn_comp_type1st')]]." ".$row_yarn_iss[csf('yarn_comp_percent1st')]." %"." ".$composition[$row_yarn_iss[csf('yarn_comp_type2nd')]]." ".$row_yarn_iss[csf('yarn_comp_percent2nd')]." %";
					}
					else
					{
						$compostion_not_req=$composition[$row_yarn_iss[csf('yarn_comp_type1st')]]." ".$row_yarn_iss[csf('yarn_comp_percent1st')]." %"." ".$composition[$row_yarn_iss[csf('yarn_comp_type2nd')]];
					}

					$desc=$yarn_count_details[$row_yarn_iss[csf('yarn_count_id')]]." ".$compostion_not_req." ".$yarn_type[$row_yarn_iss[csf('yarn_type')]];

					$yarn_desc_for_return=$row_yarn_iss[csf('yarn_count_id')]."__".$row_yarn_iss[csf('yarn_comp_type1st')]."__".$row_yarn_iss[csf('yarn_comp_percent1st')]."__".$row_yarn_iss[csf('yarn_comp_type2nd')]."__".$row_yarn_iss[csf('yarn_comp_percent2nd')]."__".$row_yarn_iss[csf('yarn_type')];

					$yarn_desc_array_for_return[$desc]=$yarn_desc_for_return;

					if(!in_array($desc,$yarn_desc_array))
					{
						if($alocate_type==1)
						{
							$alocate_cond = " and a.is_dyied_yarn in(1)";
						}
						else if($alocate_type==2)
						{
							$alocate_cond = " and a.is_dyied_yarn in(0,2)";
						}
						$sql="select a.po_break_down_id,a.allocation_date, sum(a.qnty) as allocation_qty, c.lot, c.yarn_type, c.yarn_count_id, c.product_name_details, c.supplier_id from inv_material_allocation_dtls a, product_details_master c where a.item_id=c.id and a.po_break_down_id in ($order_id) $alocate_cond and c.yarn_count_id='".$row_yarn_iss[csf('yarn_count_id')]."' and c.yarn_comp_type1st='".$row_yarn_iss[csf('yarn_comp_type1st')]."' and c.yarn_comp_percent1st='".$row_yarn_iss[csf('yarn_comp_percent1st')]."' and c.yarn_comp_type2nd='".$row_yarn_iss[csf('yarn_comp_type2nd')]."' and c.yarn_comp_percent2nd='".$row_yarn_iss[csf('yarn_comp_percent2nd')]."' and c.yarn_type='".$row_yarn_iss[csf('yarn_type')]."' and a.status_active=1 and a.is_deleted=0 and a.item_category=1 group by a.po_break_down_id,a.allocation_date, c.lot, c.yarn_type, c.yarn_count_id, c.product_name_details, c.supplier_id";

						//echo $sql;
						$result=sql_select($sql);
						foreach($result as $row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							foreach($fab_source_id as $fsid)
							{
								if($fsid==1)
								{
									$allocation_qty=$row[csf('allocation_qty')];
								}

							}

							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="40"><? echo $i; ?></td>
								<td width="100"><p><? echo $row[csf('lot')]; ?></p></td>
								<td width="60" align="center"><p><? echo change_date_format($row[csf('allocation_date')]); ?></p></td>
								<td width="70"><? echo $yarn_count_details[$row[csf('yarn_count_id')]]; ?></td>
								<td width="200"><p><? echo $row[csf('product_name_details')]; ?></p></td>
								<td width="130"><p><? echo $supplier_details[$row[csf('supplier_id')]]; ?></p></td>
								<td align="right"><? echo number_format($allocation_qty,2); ?> </td>
							</tr>
						<?
							$total_allocation_qty+=$allocation_qty;
							$i++;
						}
					}
				}
				?>
                <tfoot>
                	<th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>Total Allocation</th>
                    <th><? echo number_format($total_allocation_qty,2);?></th>
                </tfoot>
            </table>
		</div>
	</fieldset>
<?
exit();
}

if($action=="country_order_dtls_popup")
{
	echo load_html_head_contents("Country Order Dtls Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);

	$order_arr=return_library_array( "select id, po_number from wo_po_break_down where id in ($po_id)", "id", "po_number");
	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	?>
	<script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_div').innerHTML+'</body</html>');
			d.close();
		}
	</script>
	<fieldset style="width:670px; margin-left:3px">
        <div style="width:670px;" align="center">
        	<input  type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
        </div>
        <div id="report_div" align="center">
            <table rules="all" width="650" cellpadding="0" cellspacing="0" align="center">
                <tr>
                	<td colspan="4" align="center"><strong> Country Wise Order Details </strong></td>
                </tr>
                <tr>
                    <td width="130"><strong>Job No. :&nbsp; <? echo $job_no; ?>;</strong></td>
                    <td width="150"><strong> Order:&nbsp;<? echo $order_arr[$po_id]; ?>;</strong></td>
                    <td width="150"><strong> Buyer:&nbsp;<? echo $buyer_library[$buyer_id]; ?>;</strong></td>
                    <td><!--<strong> Country Ship Date:&nbsp;<? //echo change_date_format($country_date); ?></strong>--></td>
                </tr>
            </table>
            <table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0" align="center">
                <thead>
                    <th width="30">SL</th>
                    <th width="100">Country</th>
                    <th width="100">Cut Off</th>
                    <th width="90">Order Qty</th>
                    <th width="60">Avg Exc. %</th>
                    <th width="90">Plan Cut Qty.</th>
                    <th width="60">Avg Rate</th>
                    <th>Order Value</th>
                </thead>
                <tbody>
                <?
//print_r($cut_up_array);
$cut_up_array[0]="No Cutoff";
				if ($start_date=="" && $end_date=="") $country_ship_date_cond=""; else $country_ship_date_cond="and country_ship_date between '$start_date' and '$end_date'";
				$contry_sql="select country_id, cutup, sum(order_quantity) as po_qty, sum(plan_cut_qnty) as plan_cut_qty, sum(order_total) as order_value from wo_po_color_size_breakdown where po_break_down_id in ($po_id) and status_active=1 and is_deleted=0 $country_ship_date_cond group by country_id, cutup";
				$contry_sql_result=sql_select($contry_sql); $i=1;
				foreach($contry_sql_result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

					$avg_ex_per=0; $avg_rate=0;
					$avg_ex_per=(($row[csf('plan_cut_qty')]-$row[csf('po_qty')])/$row[csf('po_qty')])*100;
					$avg_rate=($row[csf('order_value')]/$row[csf('po_qty')]);
					?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
						<td width="30"><p><? echo $i; ?></p></td>
						<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $country_arr[$row[csf('country_id')]]; ?></div></td>
						<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $cut_up_array[$row[csf('cutup')]]; ?></div></td>
						<td width="90" align="right"><p><? echo number_format($row[csf('po_qty')]); ?></p></td>
						<td width="60" align="right"><p><? echo number_format($avg_ex_per,3).' %'; ?></p></td>
						<td width="90" align="right"><p><? echo number_format($row[csf('plan_cut_qty')]); ?></p></td>
						<td width="60" align="right"><p><? echo number_format($avg_rate,4); ?></p></td>
                        <td align="right"><p><? echo number_format($row[csf('order_value')],2); ?></p></td>
					</tr>
					<?
					$tot_po_qty+=$row[csf('po_qty')];
					$tot_plan_cut_qty+=$row[csf('plan_cut_qty')];
					$tot_order_value+=$row[csf('order_value')];
					$i++;
				}
				?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="3" align="right">Total</td>
						<td align="right"><? echo number_format($tot_po_qty,2); ?></td>
                        <td align="right">&nbsp;</td>
                        <td align="right"><? echo number_format($tot_plan_cut_qty,2); ?></td>
                        <td align="right">&nbsp;</td>
                        <td align="right"><? echo number_format($tot_order_value,2); ?></td>
					</tr>
				</tfoot>
			</table>
		</div>
	</fieldset>
	<?
	exit();
}

if($action=="show_image")
{
	echo load_html_head_contents("Image View","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	//echo "select image_location  from common_photo_library  where master_tble_id='$job_no' and form_name='knit_order_entry' and is_deleted=0 and file_type=1";
	$data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$job_no' and form_name='knit_order_entry' and is_deleted=0 and file_type=1");
	?>
    <table>
        <tr>
        <?
        foreach ($data_array as $row)
        {
			?>
			<td><img src='../../../<? echo $row[csf('image_location')]; ?>' height='250' width='300' /></td>
			<?
        }
        ?>
        </tr>
    </table>
    <?
	exit();
}
?>