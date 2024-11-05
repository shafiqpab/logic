<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']['user_id'];
if ($_SESSION['logic_erp']['user_id'] == "") {
    header("location:login.php");
    die;
}
$permission = $_SESSION['page_permission'];
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];


if($action=="load_drop_down_buyer")
{
    echo create_drop_down( "cbo_buyer_id", 110, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' order by buy.buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "","" );
    exit();
}

if ($action == "load_drop_down_company_store")
{
    extract($_REQUEST);

    $sql= "select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and  a.status_active=1 and a.is_deleted=0 and a.company_id=$choosenCompany and b.category_type in(1)  order by a.store_name";

    echo create_drop_down("cbo_store_name", 110, $sql, "id,store_name", 0, "-- Select Store --", $selected, "", "");

    exit();
}

if($action=="booking_no_popup")
{
    echo load_html_head_contents("Order No Info", "../../../../", 1, 1,'','','');
    extract($_REQUEST);
    $dataEx = explode("_", $data);
    $companyID = $dataEx[0];
    $buyer_name = $dataEx[1];
    ?>

    <script>
        $(function(){
            load_drop_down( 'sample_booking_wise_daily_yarn_issue_report_controller',<? echo $companyID;?>, 'load_drop_down_buyer', 'buyer_td' );
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
                                    echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $companyID, "load_drop_down( 'sample_booking_wise_daily_yarn_issue_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );");
                                ?>
                            </td>
                            <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_id", 140, $blank_array,"", 1, "-- All Buyer --" ); ?></td>
                            <td>
                                <input type="text" id="booking_no_prefix_num" name="booking_no_prefix_num" class="text_boxes_numeric" style="width:75px" />
                            </td>
                            <td align="center">
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:100px">
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:100px">
                            </td>
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('job_no').value+'_'+document.getElementById('booking_no_prefix_num').value+'_'+document.getElementById('cbo_year_selection').value, 'create_booking_search_list_view', 'search_div', 'sample_booking_wise_daily_yarn_issue_report_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
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
    <script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if($action=="create_booking_search_list_view")
{
    $data=explode('_',$data);
    //var_dump($data[5]);
    if ($data[0]!=0) $company=" and  a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
    if ($data[1]!=0) $buyer=" and a.buyer_id='$data[1]'"; else $buyer="";
    if ($data[4]!=0) $job_no=" and a.job_no='$data[4]'"; else $job_no='';
    if ($data[5]!=0) $booking_no=" and a.booking_no_prefix_num='$data[5]'"; else $booking_no='';
    if ($data[6]!=0) $cbo_year_con=" and to_char(a.insert_date,'YYYY')=$data[6]"; else $cbo_year_con='';


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

    //print_r($po_array);die;


    $approved=array(0=>"No",1=>"Yes");
    $is_ready=array(0=>"No",1=>"Yes",2=>"No");
    $buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
    $comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
    $suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
    $arr=array (2=>$comp,3=>$buyer_arr,5=>$po_array,6=>$item_category,7=>$fabric_source,8=>$suplier,9=>$approved,10=>$is_ready);

    $sql= "SELECT a.id,a.booking_no_prefix_num, a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.job_no, a.po_break_down_id, a.item_category, a.fabric_source, a.supplier_id from wo_booking_mst a, wo_po_details_master b,inv_issue_master c where a.job_no=b.job_no and a.id=c.booking_id $company $buyer $booking_no $booking_date $cbo_year_con and a.booking_type=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and c.item_category=1 and c.issue_basis=1 and c.issue_purpose in(4,8) and c.entry_form=3 group by a.id,a.booking_no_prefix_num, a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.job_no, a.po_break_down_id, a.item_category,
    a.fabric_source, a.supplier_id
    UNION ALL
    SELECT a.id, a.booking_no_prefix_num, a.booking_no,a.booking_date,a.company_id,a.buyer_id,null as job_no, CAST(a.po_break_down_id AS nvarchar2(2000)) AS po_break_down_id, a.item_category, a.fabric_source,a.supplier_id from  wo_non_ord_samp_booking_mst  a left join wo_non_ord_samp_booking_dtls b on a.id=b.booking_mst_id and b.status_active=1 and b.is_deleted=0, inv_issue_master c  where a.id=c.booking_id  $company $buyer $booking_no $booking_date $cbo_year_con and a.booking_type=4 and  a.status_active=1 and a.is_deleted=0 and c.item_category=1 and c.issue_basis=1 and c.issue_purpose in(4,8) and c.entry_form=3 group by a.id, a.booking_no_prefix_num, a.booking_no,a.booking_date,a.company_id,a.buyer_id, a.po_break_down_id, a.item_category, a.fabric_source,a.supplier_id ";
    //echo $sql;//and  a.entry_form_id=140 and  b.entry_form_id=140

    echo  create_list_view("tbl_list_search", "Booking No,Booking Date,Company,Buyer,Job No.,PO number", "120,80,70,100,120,220","700","230",0, $sql , "js_set_value", "id,booking_no", "", 1, "0,0,company_id,buyer_id,0,booking_no", $arr , "booking_no,booking_date,company_id,buyer_id,job_no,booking_no", '','','0,3,0,0,0,0','',1);
   exit();
}

if ($action == "report_generate")
{
    $process = array(&$_POST);
    extract(check_magic_quote_gpc($process));

    if (str_replace("'", "", $cbo_store_name) != 0)
        $store_cond = " and b.store_id in(" . str_replace("'", "", $cbo_store_name) . ")";

    if (str_replace("'", "", $cbo_buyer_id) != 0)
        $buyerCond = " and a.buyer_id in(" . str_replace("'", "", $cbo_buyer_id) . ")";

    if (str_replace("'", "", $txt_booking_id) != "")
    {
        $booking_id = str_replace(",", "','", $txt_booking_id);
        $booking_cond = " and a.booking_id in($booking_id)";
	}
    else
    {
        $txt_booking_no = "%".trim(str_replace("'", "", $txt_booking_no))."%";
		$booking_cond = " and a.booking_no LIKE '".$txt_booking_no."'";
        //$txt_booking_no = str_replace(",", "','", $txt_booking_no);
        //$booking_cond = " and a.booking_no like '%$txt_booking_no%'";
        //'%$txt_search_common%'
    }


    if (str_replace("'", "", $cbo_yarn_type) != "")
        $yarn_type_cond = " and c.yarn_type in(" . str_replace("'", "", $cbo_yarn_type) . ")";
    if (str_replace("'", "", $cbo_yarn_count) != "")
        $yarn_count_cond = " and c.yarn_count_id in(" . str_replace("'", "", $cbo_yarn_count) . ")";

	$txt_lot_no = str_replace("'", "", trim($txt_lot_no));
	$lot_no = '';
	if ($txt_lot_no != "")
	{
		if($lot_search_type == 1)
		{
			$lot_no = " and c.lot like '%".$txt_lot_no."%'";
		}
		else
		{
			$lot_no = " and c.lot='".$txt_lot_no."'";
		}
	}

    if (str_replace("'", "", $cbo_issue_purpose) != "" && str_replace("'", "", $cbo_issue_purpose) != 0){
        $issue_purpose_cond = " and a.issue_purpose in (".str_replace("'", "", $cbo_issue_purpose).")";
    }

    //for date
	if (str_replace("'", "", trim($txt_date_from)) != "" && str_replace("'", "", trim($txt_date_to)) != "")
	{
		$date_cond = " and a.issue_date between " . trim($txt_date_from) . " AND " . trim($txt_date_to) . "";
	}
	else
	{
		$date_cond = "";
	}
    $year_id=str_replace("'","",$cbo_year_selection);
    if($year_id!=0) $year_cond=" and to_char(a.insert_date,'yyyy')=$year_id"; else $year_cond="";

    if ($type==1) // Show Button
    {
       $sql="SELECT a.id as issue_id,a.issue_number, a.issue_basis, a.issue_purpose, a.booking_id, a.booking_no, a.buyer_id, a.issue_date, a.knit_dye_source, a.knit_dye_company, a.location_id, a.challan_no,b.id as trans_id, b.store_id, b.brand_id, b.cons_quantity as issue_qnty, b.cons_rate,b.cons_amount, c.brand, c.yarn_type, c.yarn_count_id, c.lot, c.color, c.avg_rate_per_unit,c.yarn_comp_type1st,c.yarn_comp_percent1st ,c.id as product_id from inv_issue_master a, inv_transaction b,  product_details_master c where a.item_category=1 and a.entry_form=3 and a.company_id=$cbo_company_name and a.issue_basis=1 and a.issue_purpose in (4,8) and a.id=b.mst_id and b.prod_id=c.id and b.item_category=1 and b.transaction_type=2  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $lot_no $store_cond $yarn_type_cond $yarn_count_cond $issue_purpose_cond $buyerCond $booking_cond $date_cond $year_cond";

        //echo $sql;die;
        $result = sql_select($sql);

        if(count($result)==0)
        {
            ?>
            <div class="alert alert-danger">Data not found! Please try again.</div>
            <?
            die();
        }

        $con = connect();
        $r_id1=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (1990,1991)");
        if($r_id1)
        {
            oci_commit($con);
        }

        $issue_arr = array();
        $all_issue_id_arr = array();
        $all_booking_id_arr = array();
        foreach ($result as $row)
        {
            $comp = $composition[$row[csf('yarn_comp_type1st')]].$row[csf('yarn_comp_percent1st')].'%';

            $issue_arr[$row[csf('booking_no')]][$row[csf('issue_number')]][$row[csf('product_id')]]['buyer_id']= $row[csf('buyer_id')];
            $issue_arr[$row[csf('booking_no')]][$row[csf('issue_number')]][$row[csf('product_id')]]['issue_date']= $row[csf('issue_date')];
            $issue_arr[$row[csf('booking_no')]][$row[csf('issue_number')]][$row[csf('product_id')]]['issue_basis']= $row[csf('issue_basis')];
            $issue_arr[$row[csf('booking_no')]][$row[csf('issue_number')]][$row[csf('product_id')]]['challan_no']= $row[csf('challan_no')];
            $issue_arr[$row[csf('booking_no')]][$row[csf('issue_number')]][$row[csf('product_id')]]['issue_qnty'] += $row[csf('issue_qnty')];
            $issue_arr[$row[csf('booking_no')]][$row[csf('issue_number')]][$row[csf('product_id')]]['issue_purpose'] = $row[csf('issue_purpose')];
            $issue_arr[$row[csf('booking_no')]][$row[csf('issue_number')]][$row[csf('product_id')]]['yarn_count_id'] = $row[csf('yarn_count_id')];
            $issue_arr[$row[csf('booking_no')]][$row[csf('issue_number')]][$row[csf('product_id')]]['brand'] = $row[csf('brand')];
            $issue_arr[$row[csf('booking_no')]][$row[csf('issue_number')]][$row[csf('product_id')]]['composition'] = $comp;
            $issue_arr[$row[csf('booking_no')]][$row[csf('issue_number')]][$row[csf('product_id')]]['yarn_type'] = $row[csf('yarn_type')];
            $issue_arr[$row[csf('booking_no')]][$row[csf('issue_number')]][$row[csf('product_id')]]['color'] = $row[csf('color')];
            $issue_arr[$row[csf('booking_no')]][$row[csf('issue_number')]][$row[csf('product_id')]]['lot'] = $row[csf('lot')];
            $issue_arr[$row[csf('booking_no')]][$row[csf('issue_number')]][$row[csf('product_id')]]['location_id'] = $row[csf('location_id')];
            $issue_arr[$row[csf('booking_no')]][$row[csf('issue_number')]][$row[csf('product_id')]]['store_id'] = $row[csf('store_id')];
            $issue_arr[$row[csf('booking_no')]][$row[csf('issue_number')]][$row[csf('product_id')]]['knit_dye_source'] = $row[csf('knit_dye_source')];
            $issue_arr[$row[csf('booking_no')]][$row[csf('issue_number')]][$row[csf('product_id')]]['knit_dye_company'] = $row[csf('knit_dye_company')];
            $issue_arr[$row[csf('booking_no')]][$row[csf('issue_number')]][$row[csf('product_id')]]['cons_rate'] = $row[csf('cons_rate')];

            $color_ids.=$row[csf("color")].",";
            $product_ids.=$row[csf("product_id")].",";
            $yarn_count_ids.=$row[csf("yarn_count_id")].",";
            $brand_ids.=$row[csf("brand")].",";
            $supplier_ids.=$row[csf("knit_dye_company")].",";
            $location_ids.=$row[csf("location_id")].",";
            $store_ids.=$row[csf("store_id")].",";

            if($issueIdChk[$row[csf('issue_id')]] == "")
            {
                $issueIdChk[$row[csf('issue_id')]] = $row[csf('issue_id')];
                $all_issue_id_arr[$row[csf("issue_id")]] = $row[csf("issue_id")];
            }
            if($bookingIdChk[$row[csf('booking_id')]] == "")
            {
                $bookingIdChk[$row[csf('booking_id')]] = $row[csf('booking_id')];
                $all_booking_id_arr[$row[csf("booking_id")]] = $row[csf("booking_id")];
            }
        }
        // echo "<pre>";
        // print_r($issue_arr);
        unset($result);


        //echo $location_ids; die;
        $supplier_ids =chop($supplier_ids,",");
        $location_ids =chop($location_ids,",");
        $store_ids =chop($store_ids,",");
        $color_ids =chop($color_ids,",");
        $product_ids =chop($product_ids,",");
        $yarn_count_ids =chop($yarn_count_ids,",");
        $brand_ids =chop($brand_ids,",");

        $supplier_ids=implode(",",array_filter(array_unique(explode(",",$supplier_ids))));
        $location_ids=implode(",",array_filter(array_unique(explode(",",$location_ids))));
        $store_ids=implode(",",array_filter(array_unique(explode(",",$store_ids))));
        $color_ids=implode(",",array_filter(array_unique(explode(",",$color_ids))));
        $product_ids=implode(",",array_filter(array_unique(explode(",",$product_ids))));
        $yarn_count_ids=implode(",",array_filter(array_unique(explode(",",$yarn_count_ids))));
        $brand_ids=implode(",",array_filter(array_unique(explode(",",$brand_ids))));


        if ($supplier_ids!="") {$supplier_ids_cond="and id in ($supplier_ids)";}else{$supplier_ids_cond="";}
        if ($location_ids!="") {$location_ids_cond="and id in ($location_ids)";}else{$location_ids_cond="";}
        if ($store_ids!="") {$store_ids_cond="and id in ($store_ids)";}else{$store_ids_cond="";}
        if ($color_ids!="") {$color_ids_cond="and id in ($color_ids)";}else{$color_ids_cond="";}
        if ($yarn_count_ids!="") {$yarn_count_ids_cond="and id in ($yarn_count_ids)";}else{$yarn_count_ids_cond="";}
        if ($brand_ids!="") {$brand_ids_cond="and id in ($brand_ids)";}else{$brand_ids_cond="";}

        $company_arr = return_library_array("select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
        $supplier_arr = return_library_array("select id, supplier_name from lib_supplier where status_active=1 $supplier_ids_cond and is_deleted=0", "id", "supplier_name");
        $locat_arr = return_library_array("select id, store_location from lib_store_location where status_active=1  $location_ids_cond and is_deleted=0", "id", "store_location");
        $location_arr = return_library_array("select id,location_name from lib_location where status_active =1 and is_deleted=0 $location_ids_cond ", "id", "location_name");
        $store_arr = return_library_array("select id, store_name from lib_store_location where  status_active=1 $store_ids_cond and is_deleted=0", "id", "store_name");
        $buyer_arr = return_library_array("select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0", "id", "buyer_name");
        $color_arr = return_library_array("select id,color_name from lib_color where status_active=1 $color_ids_cond and is_deleted=0", "id", "color_name");
        $count_arr = return_library_array("select id, yarn_count from lib_yarn_count where status_active=1 $yarn_count_ids_cond and is_deleted=0", 'id', 'yarn_count');
        $brand_arr = return_library_array("select id, brand_name from lib_brand where status_active=1 $brand_ids_cond and is_deleted=0", 'id', 'brand_name');

        $all_issue_id_arr = array_filter($all_issue_id_arr);
        //var_dump($all_issue_id_arr);die;
        if(!empty($all_issue_id_arr))
        {
            fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 1990, 1,$all_issue_id_arr, $empty_arr);
            //die;

            /* echo "SELECT a.recv_number,a.booking_no, b.cons_quantity, c.issue_number,b.prod_id, b.id as trans_id from inv_receive_master a, inv_transaction b, inv_issue_master c, GBL_TEMP_ENGINE d where a.id = b.mst_id and b.issue_id = c.id and b.transaction_type = 4 and b.item_category = 1 and a.entry_form = 9 and b.status_active = 1 and b.is_deleted = 0 and a.status_active = 1 and c.id=d.ref_val and d.user_id=$user_id and d.entry_form=1990";die;  */
            $issue_return_res = sql_select("SELECT a.recv_number,a.booking_no, b.cons_quantity, c.issue_number,b.prod_id, b.id as trans_id from inv_receive_master a, inv_transaction b, inv_issue_master c, GBL_TEMP_ENGINE d where a.id = b.mst_id and b.issue_id = c.id and b.transaction_type = 4 and b.item_category = 1 and a.entry_form = 9 and b.status_active = 1 and b.is_deleted = 0 and a.status_active = 1 and c.id=d.ref_val and d.user_id=$user_id and d.entry_form=1990");

            $transIdChk = array();
            foreach ($issue_return_res as $val)
            {
                if($transIdChk[$val[csf("trans_id")]]=="")
                {
                    $transIdChk[$val[csf("trans_id")]] = $val[csf("trans_id")];
                    $issue_return_qnty_arr[$val[csf("issue_number")]][$val[csf("booking_no")]][$val[csf("prod_id")]] += $val[csf("cons_quantity")];
                }
            }
            unset($issue_return_res);
        }


        $all_booking_id_arr = array_filter($all_booking_id_arr);
        //var_dump()
        if(!empty($all_booking_id_arr))
        {
            fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 1991, 1,$all_booking_id_arr, $empty_arr);
            //die;

            $sql_smn_info =  "SELECT a.booking_no, b.grey_fabric as grey_fab_qnty, c.requisition_number from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b, sample_development_mst c, GBL_TEMP_ENGINE d
            where a.booking_no=b.booking_no and b.style_id=c.id and  c.company_id=$cbo_company_name and a.booking_type=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.id=d.ref_val and d.user_id=$user_id and d.entry_form=1991"; // and c.entry_form_id=203 and a.entry_form_id=140

            //echo $sql_smn_info;die;

            $smn_info_arr = array();
            foreach (sql_select($sql_smn_info) as $row)
            {
                $smn_info_arr[$row[csf('booking_no')]]['requisition_number'] = $row[csf('requisition_number')];
                $smn_info_arr[$row[csf('booking_no')]]['grey_fab_qnty'] += $row[csf('grey_fab_qnty')];
            }
            // var_dump($smn_info_arr);



            $sql_smn_inf1="SELECT a.booking_no, b.grey_fabric as grey_fab_qnty, null as requisition_number from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b, GBL_TEMP_ENGINE d
            where a.booking_no=b.booking_no and  a.company_id=$cbo_company_name and a.booking_type=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=d.ref_val and d.user_id=$user_id and d.entry_form=1991";
             $smn_info_arr1 = array();
             foreach (sql_select($sql_smn_inf1) as $row)
             {
                 $smn_info_arr1[$row[csf('booking_no')]]['grey_fab_qnty'] += $row[csf('grey_fab_qnty')];
             }
             // var_dump($smn_info_arr);


        }

        $usd_arr = array();
        $sqlSelectData = sql_select("select con_date,conversion_rate from currency_conversion_rate where currency=2 and is_deleted=0 and company_id=$cbo_company_name order by con_date desc");
        foreach ($sqlSelectData as $row)
        {
            $usd_arr[date('d-m-Y',strtotime($row[csf('con_date')]))] = $row[csf('conversion_rate')];
        }


        $value_width = 2570;
        $span = 24;


        $booking_count=array();
        foreach ($issue_arr as $k_booking_no=>$v_booking_no)
        {
            foreach ($v_booking_no as $k_issue_no => $v_issue_no)
            {
                foreach ($v_issue_no as $k_prod_id => $row)
                {
                    $booking_count[$k_booking_no]++;
                }
            }
        }

        $con = connect();
        $r_id111=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (1990,1991)");
        if($r_id111)
        {
            oci_commit($con);
        }

        ob_start();


            ?>
            <style>
                .wrd_brk{word-break: break-all;word-wrap: break-word;}
            </style>

            <fieldset style="width:<? echo $value_width + 18; ?>px;">
                <table cellpadding="0" cellspacing="0" width="<? echo $value_width; ?>" style="float: left;">
                    <tr>
                        <td align="center" width="100%" colspan="<? echo $span; ?>" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
                    </tr>
                    <tr>
                        <td align="center" width="100%" colspan="<? echo $span; ?>" style="font-size:16px"><strong><? echo $company_arr[str_replace("'", "", $cbo_company_name)]; ?></strong></td>
                    </tr>
                    <tr>
                        <td align="center" width="100%" colspan="<? echo $span; ?>" style="font-size:16px"><strong>From Date: <? echo change_date_format(str_replace("'", "", $txt_date_from)); ?> To Date: <? echo change_date_format(str_replace("'", "", $txt_date_to)); ?></strong></td>
                    </tr>
                </table>

                <table width="<? echo $value_width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" style="float: left;">
                    <thead>
                        <th width="30">SL</th>
                        <th width="100">Buyer Name</th>
                        <th width="100">Sample Booking</th>
                        <th width="100">Sample-Req. No</th>
                        <th width="100">Booking/<br>Requ. Qty</th>
                        <th width="100">Issue Date</th>
                        <th width="100">Issue Basis</th>
                        <th width="130">Issue No</th>
                        <th width="130">Challan No</th>
                        <th width="100">Issue Qty</th>
                        <th width="100">Issue Return Qty.</th>
                        <th width="100">Total Issue Qty.</th>
                        <th width="100">Rate/Kg</th>
                        <th width="100">Net Issue Amount</th>
                        <th width="120">Issue Purpose</th>
                        <th width="100">Count</th>
                        <th width="100">Yarn Brand</th>
                        <th width="200">Composition</th>
                        <th width="100">Type</th>
                        <th width="100">Color</th>
                        <th width="100">Lot No</th>
                        <th width="130">Issue To</th>
                        <th width="100">Location</th>
                        <th>Store</th>
                    </thead>
                </table>
                <div style="width:<? echo $value_width + 18; ?>px; overflow-y: scroll; max-height:380px; float: left;" id="scroll_body">
                    <table width="<? echo $value_width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" style="float: left;" id="table_body">
                        <tbody>
                            <?
                            $i = 1;

                            $g_iss_tot_qnty=$g_iss_rtn_tot_qnty=$g_total_iss_tot_qnty=$g_iss_tot_rate=$g_iss_tot_amount=0;

                            foreach ($issue_arr as $k_booking_no=>$v_booking_no)
                            {
                                $iss_tot_qnty=$iss_rtn_tot_qnty=$total_iss_tot_qnty=$iss_tot_rate=$iss_tot_amount=0;
                                foreach ($v_booking_no as $k_issue_no => $v_issue_no)
                                {
                                    foreach ($v_issue_no as $k_prod_id => $row)
                                    {

                                    $booking_span = $booking_count[$k_booking_no];

                                        $exchangeRate=$usd_arr[date('d-m-Y',strtotime($row['issue_date']))];
                                        if($exchangeRate =="")
                                        {
                                            foreach ($usd_arr as $rate_date => $rat)
                                            {
                                                if(strtotime($rate_date) <= strtotime($row['issue_date']))
                                                {
                                                    $rate_date = date('d-m-Y',strtotime($rate_date));
                                                    $exchangeRate=$rat;
                                                    break;
                                                }
                                            }
                                        }


                                        //-----------------------------------------------------------------------

                                        if ($i % 2 == 0)
                                            $bgcolor = "#E9F3FF";
                                        else
                                            $bgcolor = "#FFFFFF";

                                        if ($row['knit_dye_source'] == 1)
                                        {
                                            $knitting_party = $company_arr[$row['knit_dye_company']];
                                            $knitting_location = $location_arr[$row['location_id']];
                                        }
                                        else if ($row['knit_dye_source'] == 3)
                                        {
                                            $knitting_party = $supplier_arr[$row['knit_dye_company']];
                                            $knitting_location = $locat_arr[$row['location_id']];
                                        }
                                        else
                                        {
                                            $knitting_party = "";
                                            $knitting_location = '';
                                        }

                                        ?>

                                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr<? echo $i; ?>">

                                            <?
                                            if(!in_array($k_booking_no,$booking_chk))
                                            {
                                                $booking_chk[]=$k_booking_no;
                                            ?>

                                            <td width="30" class="wrd_brk" rowspan="<? echo $booking_span;?>" valign="middle" align="center"><? echo $i; ?></td>
                                            <td width="100" class="wrd_brk" rowspan="<? echo $booking_span;?>" valign="middle" align="center"><? echo $buyer_arr[$row['buyer_id']]; ?></td>
                                            <td width="100" class="wrd_brk" rowspan="<? echo $booking_span;?>" valign="middle" align="center"><? echo $k_booking_no; ?></td>
                                            <td width="100" class="wrd_brk" rowspan="<? echo $booking_span;?>" valign="middle" align="center">
                                                <? echo $smn_info_arr[$k_booking_no]['requisition_number']; ?>
                                            </td>
                                            <td width="100" class="wrd_brk" rowspan="<? echo $booking_span;?>" valign="middle" align="center">
                                                <?
                                                if($smn_info_arr[$k_booking_no]['grey_fab_qnty'] !='')
                                                {
                                                    echo number_format($smn_info_arr[$k_booking_no]['grey_fab_qnty'],2);
                                                }
                                                else
                                                {
                                                    echo number_format($smn_info_arr1[$k_booking_no]['grey_fab_qnty'],2);
                                                }

                                                 ?>
                                            </td>
                                            <? } ?>
                                            <td width="100" class="wrd_brk" align="center"><? echo change_date_format($row['issue_date']); ?></td>
                                            <td width="100" class="wrd_brk" align="center"><? echo $issue_basis[$row['issue_basis']]; ?></td>
                                            <td width="130" class="wrd_brk" align="center"><? echo $k_issue_no; ?></td>
                                            <td width="130" class="wrd_brk" align="center"><? echo $row['challan_no']; ?></td>
                                            <td width="100" class="wrd_brk" align="right"><? echo number_format($row['issue_qnty'],2); ?></td>
                                            <td width="100" class="wrd_brk" align="right">
                                                <?
                                                echo number_format($issue_return_qnty_arr[$k_issue_no][$k_booking_no][$k_prod_id],2);
                                                ?>
                                            </td>
                                            <td width="100" class="wrd_brk" align="right">
                                            <?
                                            $net_issue = $row['issue_qnty']-$issue_return_qnty_arr[$k_issue_no][$k_booking_no][$k_prod_id];
                                            echo number_format($net_issue,4);
                                            ?>
                                            </td>
                                            <td width="100" class="wrd_brk" align="right">
                                                <?
                                            $rate =  $row['cons_rate']/$exchangeRate;
                                                echo number_format($rate, 4);?>
                                            </td>
                                            <td width="100" class="wrd_brk" align="right">
                                                <?
                                                if( number_format($rate,2) > 0.00 )
                                                {
                                                    echo number_format($net_issue*$rate,4);
                                                }
                                                else
                                                {
                                                    echo '0.0000';
                                                }

                                                ?>
                                            </td>
                                            <td width="120" class="wrd_brk" align="center"><? echo $yarn_issue_purpose[$row['issue_purpose']]; ?></td>
                                            <td width="100" class="wrd_brk" align="center"><? echo  $count_arr[$row['yarn_count_id']]; ?>&nbsp;</td>
                                            <td width="100" class="wrd_brk" align="center"><? echo $brand_arr[$row['brand']]; ?></td>
                                            <td width="200" class="wrd_brk" align="center"><? echo $row['composition']; ?></td>
                                            <td width="100" class="wrd_brk" align="center"><? echo $yarn_type[$row['yarn_type']]; ?></td>
                                            <td width="100" class="wrd_brk" align="center"><? echo $color_arr[$row['color']]; ?></td>
                                            <td width="100" class="wrd_brk" align="center"><? echo $row['lot']; ?>&nbsp;</td>
                                            <td width="130" class="wrd_brk" align="center"><? echo $knitting_party; ?></td>
                                            <td width="100" class="wrd_brk" align="center"><? echo $knitting_location; ?></td>
                                            <td class="wrd_brk" align="center"><? echo $store_arr[$row['store_id']]; ?></td>
                                        </tr>
                                        <?

                                            $iss_tot_qnty +=$row['issue_qnty'];
                                            $iss_rtn_tot_qnty +=$issue_return_qnty_arr[$k_issue_no][$k_booking_no][$k_prod_id];
                                            $total_iss_tot_qnty +=$row['issue_qnty']-$issue_return_qnty_arr[$k_issue_no][$k_booking_no][$k_prod_id];
                                            $iss_tot_rate += $rate;
                                            $iss_tot_amount +=$net_issue*$rate;

                                            $g_iss_tot_qnty +=$row['issue_qnty'];
                                            $g_iss_rtn_tot_qnty +=$issue_return_qnty_arr[$k_issue_no][$k_booking_no][$k_prod_id];
                                            $g_total_iss_tot_qnty +=$row['issue_qnty']-$issue_return_qnty_arr[$k_issue_no][$k_booking_no][$k_prod_id];
                                            $g_iss_tot_rate += $rate;
                                            $g_iss_tot_amount +=$net_issue*$rate;

                                    }
                                }
                                    ?>

                                    <tr bgcolor="#e5e7e9">
                                        <td width="30" colspan="9" class="wrd_brk" align="right"><b>Total :</b></td>
                                        <td width="100" class="wrd_brk" align="right"><b><? echo number_format($iss_tot_qnty,2);?></b></td>
                                        <td width="100" class="wrd_brk" align="right"><b><? echo number_format($iss_rtn_tot_qnty,2);?></b></td>
                                        <td width="100" class="wrd_brk" align="right"><b><? echo number_format($total_iss_tot_qnty,4);?></b></td>
                                        <td width="100" class="wrd_brk" align="right"><b><? echo number_format($iss_tot_rate,4);?></b></td>
                                        <td width="100" class="wrd_brk" align="right"><b><? echo number_format($iss_tot_amount,4);?></b></td>
                                        <td width="120" class="wrd_brk">&nbsp;</td>
                                        <td width="100" class="wrd_brk">&nbsp;</td>
                                        <td width="100" class="wrd_brk">&nbsp;</td>
                                        <td width="200" class="wrd_brk">&nbsp;</td>
                                        <td width="100" class="wrd_brk">&nbsp;</td>
                                        <td width="100" class="wrd_brk">&nbsp;</td>
                                        <td width="100" class="wrd_brk">&nbsp;</td>
                                        <td width="130" class="wrd_brk">&nbsp;</td>
                                        <td width="100" class="wrd_brk">&nbsp;</td>
                                        <td class="wrd_brk">&nbsp;</td>
                                    </tr>

                                    <?
                                    $i++;
                            }
                            ?>

                            <tr bgcolor="#e5e7e9">
                                <td width="30" colspan="9" class="wrd_brk" align="right"><b>Grand Total :</b></td>
                                <td width="100" class="wrd_brk" align="right"><b><? echo number_format($g_iss_tot_qnty,2);?></b></td>
                                <td width="100" class="wrd_brk" align="right"><b><? echo number_format($g_iss_rtn_tot_qnty,2);?></b></td>
                                <td width="100" class="wrd_brk" align="right"><b><? echo number_format($g_total_iss_tot_qnty,2);?></b></td>
                                <td width="100" class="wrd_brk" align="right"><b><? echo number_format($g_iss_tot_rate,2);?></b></td>
                                <td width="100" class="wrd_brk" align="right"><b><? echo number_format($g_iss_tot_amount,2);?></b></td>
                                <td width="120" class="wrd_brk">&nbsp;</td>
                                <td width="100" class="wrd_brk">&nbsp;</td>
                                <td width="100" class="wrd_brk">&nbsp;</td>
                                <td width="200" class="wrd_brk">&nbsp;</td>
                                <td width="100" class="wrd_brk">&nbsp;</td>
                                <td width="100" class="wrd_brk">&nbsp;</td>
                                <td width="100" class="wrd_brk">&nbsp;</td>
                                <td width="130" class="wrd_brk">&nbsp;</td>
                                <td width="100" class="wrd_brk">&nbsp;</td>
                                <td class="wrd_brk">&nbsp;</td>
                            </tr>

                        </tbody>

                    </table>
                </div>
            </fieldset>
            <?

    }

    foreach (glob("$user_id*.xls") as $filename) {
        if (@filemtime($filename) < (time() - $seconds_old))
            @unlink($filename);
    }
    //---------end------------//
    $name = time();
    $filename = $user_id . "_" . $name . ".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, ob_get_contents());
    $filename = $user_id . "_" . $name . ".xls";
    echo "$total_data####$filename";
    exit();
}

?>
