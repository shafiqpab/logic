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


if ($action == "load_drop_down_company_store") 
{
    extract($_REQUEST);

    $sql= "select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and  a.status_active=1 and a.is_deleted=0 and a.company_id in($choosenCompany) and b.category_type in(1)  order by a.store_name";

    echo create_drop_down("cbo_store_name", 110, $sql, "id,store_name", 0, "-- Select Store --", $selected, "", "");

    exit();
}


if($action=="load_drop_down_buyer")
{
     //$data=explode("_",$data);  
    //if($data==1) $party="1,3,21,90"; else $party="80";
    echo create_drop_down( "cbo_buyer_id", 110, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($data) order by buy.buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "","" );
    //and buy.id in (select buyer_id from lib_buyer_party_type where party_type in ($party)
    exit();
}

/* if($action=="print_button_variable_setting")
{
    $print_report_format_arr = return_library_array("select format_id,format_id from lib_report_template where template_name =$data and module_id=6 and report_id=116 and is_deleted=0 and status_active=1","format_id","format_id");
    echo "print_report_button_setting('".implode(',',$print_report_format_arr)."');\n";
    exit();
} */

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
            load_drop_down( 'multi_company_wise_daily_yarn_issue_report_controller',<? echo $companyID;?>, 'load_drop_down_buyer', 'buyer_td' );
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
                                    echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $companyID, "load_drop_down( 'multi_company_wise_daily_yarn_issue_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );");
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
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('job_no').value+'_'+document.getElementById('booking_no_prefix_num').value+'_'+document.getElementById('cbo_year').value, 'create_booking_search_list_view', 'search_div', 'multi_company_wise_daily_yarn_issue_report_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
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
    if ($data[0]!=0) $company=" and  a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
    if ($data[1]!=0) $buyer=" and a.buyer_id='$data[1]'"; else $buyer="";
    if ($data[4]!=0) $job_no=" and a.job_no='$data[4]'"; else $job_no='';
    if ($data[5]!=0) $booking_no=" and a.booking_no_prefix_num='$data[5]'"; else $booking_no='';
    if ($data[6]!=0) $cbo_year_con=" and to_char(b.insert_date,'YYYY')=$data[6]"; else $cbo_year_con='';

    
    
    
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
    
    //print_r($po_array);die; 
     
     
    $approved=array(0=>"No",1=>"Yes");
    $is_ready=array(0=>"No",1=>"Yes",2=>"No");
    $buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
    $comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
    $suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
    $arr=array (2=>$comp,3=>$buyer_arr,5=>$po_array,6=>$item_category,7=>$fabric_source,8=>$suplier,9=>$approved,10=>$is_ready);
    
    $sql= "SELECT a.id,a.booking_no_prefix_num, a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.job_no, a.po_break_down_id, a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.ready_to_approved from wo_booking_mst a, wo_po_details_master b where a.job_no=b.job_no $company $buyer $booking_no $booking_date $cbo_year_con and a.booking_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 group by a.id,a.booking_no_prefix_num, a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.job_no, a.po_break_down_id, a.item_category, 
    a.fabric_source, a.supplier_id, a.is_approved, a.ready_to_approved
    UNION ALL 
    SELECT a.id, a.booking_no_prefix_num, a.booking_no,a.booking_date,a.company_id,a.buyer_id,null as job_no, CAST(a.po_break_down_id AS nvarchar2(2000)) AS po_break_down_id, a.item_category, a.fabric_source,a.supplier_id,a.is_approved,a.ready_to_approved from wo_non_ord_samp_booking_mst  a left join wo_non_ord_samp_booking_dtls b on a.booking_no=b.booking_no  and b.status_active=1 and b.is_deleted=0  where  a.entry_form_id=140 and  b.entry_form_id=140 $company $buyer $booking_no $booking_date $cbo_year_con and a.booking_type=4 and  a.status_active=1 and a.is_deleted=0 group by a.id, a.booking_no_prefix_num, a.booking_no,a.booking_date,a.company_id,a.buyer_id, a.po_break_down_id, a.item_category, a.fabric_source,a.supplier_id,a.is_approved,a.ready_to_approved ";
   
    
    echo  create_list_view("tbl_list_search", "Booking No,Booking Date,Company,Buyer,Job No.,PO number,Fabric Nature,Fabric Source,Approved,Is-Ready", "100,80,70,100,80,220,110,60,60","1020","230",0, $sql , "js_set_value", "id,booking_no", "", 1, "0,0,company_id,buyer_id,0,booking_no,item_category,fabric_source,is_approved,ready_to_approved", $arr , "booking_no,booking_date,company_id,buyer_id,job_no,booking_no,item_category,fabric_source,is_approved,ready_to_approved", '','','0,3,0,0,0,0,0,0,0,0','',1);
   exit(); 
}

if ($action == "report_generate") 
{
    $process = array(&$_POST);
    extract(check_magic_quote_gpc($process));
   

    $cbo_company_name = str_replace(",", "','", $cbo_company_name);

    if (str_replace("'", "", $cbo_store_name) != 0)
        $store_cond = " and b.store_id in(" . str_replace("'", "", $cbo_store_name) . ")";

    if (str_replace("'", "", $cbo_buyer_id) != 0)
        $buyerCond = " and a.buyer_id in(" . str_replace("'", "", $cbo_buyer_id) . ")";

    if (str_replace("'", "", $txt_booking_no) != "")
    {
        $booking_no = str_replace(",", "','", $txt_booking_no);
        $booking_cond = " and a.booking_no in($booking_no)";

       $sqlProgram = "select b.requisition_no from ppl_planning_entry_plan_dtls a ,ppl_yarn_requisition_entry b where a.dtls_id=b.knit_id and a.status_active = 1 and a.is_deleted = 0 $booking_cond group by b.requisition_no";
		$sqlProgramRslt = sql_select($sqlProgram);
		$bookingNoArr = array();
		foreach($sqlProgramRslt as $row)
		{
			$bookingNoArr[$row[csf('requisition_no')]] = $row[csf('requisition_no')];
		}
		
		$booking_req_cond = " and b.requisition_no in('".implode("','",$bookingNoArr)."')";
	        	
	}
    
    if (str_replace("'", "", $cbo_display_type) != 0)
        $display_cond = " and a.knit_dye_source in(" . str_replace("'", "", $cbo_display_type) . ")";

    if (str_replace("'", "", $cbo_yarn_type) != "")
        $yarn_type_cond = " and c.yarn_type in(" . str_replace("'", "", $cbo_yarn_type) . ")";
    if (str_replace("'", "", $cbo_yarn_count) != "")
        $yarn_count_cond = " and c.yarn_count_id in(" . str_replace("'", "", $cbo_yarn_count) . ")";
	
	//for lot
	/*	
    if (str_replace("'", "", trim($txt_lot_no)) == "")
        $lot_no = "%%";
    else
        $lot_no = "%" . str_replace("'", "", trim($txt_lot_no)) . "%";
	*/
	$txt_lot_no = str_replace("'", "", trim($txt_lot_no));	
	$lot_no = '';		
	if ($txt_lot_no != "")
	{
		if($lot_search_type == 1)
		{
			/*if($db_type == 2)
			{
				$lot_no = " and regexp_like (c.lot, '^".$txt_lot_no."')";
			}
			else
			{
				$lot_no = " and c.lot like '".$txt_lot_no."%'";
			}*/
			$lot_no = " and c.lot like '%".$txt_lot_no."%'";
		}
		else
		{
			$lot_no = " and c.lot='".$txt_lot_no."'";
		}
	}

    if (str_replace("'", "", $cbo_issue_purpose) != "" && str_replace("'", "", $cbo_issue_purpose) != 0){
        $issue_purpose_cond = " and a.issue_purpose in (".str_replace("'", "", $cbo_issue_purpose).")";
    }else{$issue_purpose_cond = " and a.issue_purpose in (1,2,4,7,8,15,16,38,46,3,5,6,12,26,29,30,39,40,45,50,51,54)";}

    if (str_replace("'", "", $cbo_using_item) != "" && str_replace("'", "", $cbo_using_item) != 0)
        $using_item_cond = " and b.using_item in (".str_replace("'", "", $cbo_using_item).")";

    if ($db_type == 0) 
    {
        $from_date = change_date_format(str_replace("'", "", $txt_date_from), 'yyyy-mm-dd');
        $to_date = change_date_format(str_replace("'", "", $txt_date_to), 'yyyy-mm-dd');
    }
    if ($db_type == 2) 
    {
        $from_date = change_date_format(str_replace("'", "", $txt_date_from), '', '', 1);
        $to_date = change_date_format(str_replace("'", "", $txt_date_to), '', '', 1);
    }
    $con = connect();
    $r_id555=execute_query("DELETE FROM TMP_BOOKING_ID WHERE USERID=$user_id ");
    $r_id666=execute_query("DELETE FROM TMP_BOOKING_NO WHERE USERID=$user_id ");
    $r_id111=execute_query("DELETE FROM TMP_JOB_NO WHERE USERID=$user_id ");
    $r_id222=execute_query("DELETE FROM TMP_ISSUE_ID WHERE USER_ID=$user_id and TYPE = 1");
    $r_id333=execute_query("DELETE FROM TMP_REQS_NO WHERE USERID=$user_id ");
    $r_id444=execute_query("DELETE FROM TMP_PROG_NO WHERE USERID=$user_id ");
    oci_commit($con);
    disconnect($con);

    if ($type==1) // Show Button
    {
       $sql="SELECT a.id as issue_id,a.company_id,a.issue_number, a.issue_basis, a.issue_purpose, a.booking_id, a.booking_no, a.buyer_id, a.issue_date, a.knit_dye_source, a.knit_dye_company, a.location_id, a.challan_no,b.id as trans_id, b.store_id, b.brand_id, sum(b.cons_quantity) as issue_qnty,b.requisition_no, sum(b.return_qnty) as return_qnty,b.cons_rate,b.cons_amount, b.using_item, c.brand, c.yarn_type, c.yarn_count_id, c.lot, c.color, c.avg_rate_per_unit,c.yarn_comp_type1st,c.yarn_comp_percent1st ,c.id as product_id from inv_issue_master a, inv_transaction b,  product_details_master c where a.id=b.mst_id and b.prod_id=c.id and a.item_category=1 and a.entry_form=3 and a.company_id in($cbo_company_name) and a.issue_purpose not in (7,8,12,15,38,46,50,51) and a.issue_date between '$from_date' and '$to_date' and  b.item_category=1 and b.transaction_type=2  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $lot_no $store_cond $yarn_type_cond $yarn_count_cond $issue_purpose_cond $using_item_cond $buyerCond $booking_req_cond $display_cond 
        group by a.id,a.company_id,a.issue_number, a.issue_basis, a.issue_purpose, a.booking_id, a.booking_no, a.buyer_id, a.issue_date, a.knit_dye_source, a.knit_dye_company, a.location_id, a.challan_no,b.id,b.store_id, b.brand_id,b.requisition_no,b.cons_rate,b.cons_amount, b.using_item, c.brand, c.yarn_type, c.yarn_count_id, c.lot, c.color, c.avg_rate_per_unit,c.yarn_comp_type1st,c.yarn_comp_percent1st ,c.id 
        order by a.knit_dye_source,a.issue_number, a.issue_date,a.company_id";

        $sql_summary="SELECT a.knit_dye_source, sum(b.cons_quantity) as issue_qnty from inv_issue_master a, inv_transaction b, product_details_master c where a.id=b.mst_id and b.prod_id=c.id and a.item_category=1 and a.entry_form=3 and a.company_id in($cbo_company_name) and a.issue_date between '$from_date' and '$to_date' and  b.item_category=1 and b.transaction_type=2 and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $lot_no $store_cond $yarn_type_cond $yarn_count_cond $issue_purpose_cond $using_item_cond $buyerCond $booking_req_cond $display_cond group by a.knit_dye_source order by a.knit_dye_source";

        //echo $sql;die;
        $result = sql_select($sql);
        $result_summery = sql_select($sql_summary);
        $all_issue_id_arr = array();
        $all_req_no_arr = array();
        $all_booking_id_arr = array();
        $all_booking_no_arr = array();
        foreach ($result as $row) 
        {

            if($row[csf("booking_id")]!="")
            {
                $all_booking_id_arr[$row[csf('booking_id')]] = $row[csf('booking_id')];
            }

            if($row[csf("booking_no")]!="")
            {
                $all_booking_no_arr[$row[csf('booking_no')]] = $row[csf('booking_no')];
            }

            if($row[csf("requisition_no")]!="")
            {
                //$requisition_nos.=$row[csf("requisition_no")].",";
                $all_req_no_arr[$row[csf('requisition_no')]] = $row[csf('requisition_no')];
            }
            
            $buyer_ids.=$row[csf("buyer_id")].",";
            $color_ids.=$row[csf("color")].",";
            $product_ids.=$row[csf("product_id")].",";
            $yarn_count_ids.=$row[csf("yarn_count_id")].",";
            $brand_ids.=$row[csf("brand")].",";
            $supplier_ids.=$row[csf("knit_dye_company")].",";
            $location_ids.=$row[csf("location_id")].",";
            $store_ids.=$row[csf("store_id")].",";
            
           
            $trans_ids.=$row[csf("trans_id")].",";
            //$issue_numbers.="'".$row[csf("issue_number")]."',";
            $all_issue_id_arr[$row[csf('issue_id')]] = $row[csf('issue_id')];
            

            if ( $row[csf("issue_purpose")]!=1 &&  $row[csf("issue_purpose")]!=2 &&  $row[csf("issue_purpose")]!=8 &&  $row[csf("issue_purpose")]!=4) 
            {
                $issue_purpose_wise_issue_qnty_arr[$row[csf("issue_purpose")]]+=$row[csf("issue_qnty")];
            }

            if ($row[csf("issue_purpose")]==8) 
            {
                $sample_without_order_issue_qnty_arr[$row[csf("issue_purpose")]]+=$row[csf("issue_qnty")];
            }
        }
        //var_dump( $all_booking_id_arr);die;

        //echo $location_ids; die;
        $booking_nos =chop($booking_nos,",");
        $buyer_ids =chop($buyer_ids,",");
        $supplier_ids =chop($supplier_ids,",");
        $location_ids =chop($location_ids,",");
        $store_ids =chop($store_ids,",");
        $color_ids =chop($color_ids,",");
        $product_ids =chop($product_ids,",");
        $yarn_count_ids =chop($yarn_count_ids,",");
        $brand_ids =chop($brand_ids,",");
        //$requisition_nos =chop($requisition_nos,",");
        $trans_ids =chop($trans_ids,",");
        //$issue_numbers =chop($issue_numbers,",");

        $buyer_ids=implode(",",array_filter(array_unique(explode(",",$buyer_ids))));
        $supplier_ids=implode(",",array_filter(array_unique(explode(",",$supplier_ids))));
        $location_ids=implode(",",array_filter(array_unique(explode(",",$location_ids))));
        $store_ids=implode(",",array_filter(array_unique(explode(",",$store_ids))));
        $color_ids=implode(",",array_filter(array_unique(explode(",",$color_ids))));
        $product_ids=implode(",",array_filter(array_unique(explode(",",$product_ids))));
        $yarn_count_ids=implode(",",array_filter(array_unique(explode(",",$yarn_count_ids))));
        $brand_ids=implode(",",array_filter(array_unique(explode(",",$brand_ids))));
        //$issue_numbers=implode(",",array_filter(array_unique(explode(",",$issue_numbers))));
    }

    $all_booking_id_arr = array_filter($all_booking_id_arr);
    if(!empty($all_booking_id_arr))
    {   
        $con = connect();
        foreach($all_booking_id_arr as $bookingId)
        {
            execute_query("INSERT INTO TMP_BOOKING_ID(BOOKING_ID,USERID) VALUES(".$bookingId.", ".$user_id.")");
            oci_commit($con);
        }
    }
    //die;

    $all_booking_no_arr = array_filter($all_booking_no_arr);
    if(!empty($all_booking_no_arr))
    { 
        $con = connect();
        foreach($all_booking_no_arr as $bookingNo)
        {
            execute_query("INSERT INTO TMP_BOOKING_NO(BOOKING_NO,USERID) VALUES('".$bookingNo."', ".$user_id.")");
            oci_commit($con);
        }
    }
    //die;
    
    if ($supplier_ids!="") {$supplier_ids_cond="and id in ($supplier_ids)";}else{$supplier_ids_cond="";}
    if ($location_ids!="") {$location_ids_cond="and id in ($location_ids)";}else{$location_ids_cond="";}
    if ($store_ids!="") {$store_ids_cond="and id in ($store_ids)";}else{$store_ids_cond="";}
    //if ($buyer_ids!="") {$buyer_ids_cond="and id in ($buyer_ids)";}else{$buyer_ids_cond="";}
    if ($color_ids!="") {$color_ids_cond="and id in ($color_ids)";}else{$color_ids_cond="";}
    if ($yarn_count_ids!="") {$yarn_count_ids_cond="and id in ($yarn_count_ids)";}else{$yarn_count_ids_cond="";}
    if ($brand_ids!="") {$brand_ids_cond="and id in ($brand_ids)";}else{$brand_ids_cond="";}

    $company_arr = return_library_array("select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name"); 
    $supplier_arr = return_library_array("select id, supplier_name from lib_supplier where status_active=1 $supplier_ids_cond and is_deleted=0", "id", "supplier_name");
    $locat_arr = return_library_array("select id, store_location from lib_store_location where status_active=1  $location_ids_cond and is_deleted=0", "id", "store_location");
    
    $location_arr = return_library_array("select id,location_name from lib_location where status_active =1 and is_deleted=0 $location_ids_cond ", "id", "location_name");
    //echo "nnnnnnnn";
    //print_r($location_arr);

    $store_arr = return_library_array("select id, store_name from lib_store_location where  status_active=1 $store_ids_cond and is_deleted=0", "id", "store_name");
    $buyer_arr = return_library_array("select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0", "id", "buyer_name");
    $color_arr = return_library_array("select id,color_name from lib_color where status_active=1 $color_ids_cond and is_deleted=0", "id", "color_name");
    $count_arr = return_library_array("select id, yarn_count from lib_yarn_count where status_active=1 $yarn_count_ids_cond and is_deleted=0", 'id', 'yarn_count');
    $brand_arr = return_library_array("select id, brand_name from lib_brand where status_active=1 $brand_ids_cond and is_deleted=0", 'id', 'brand_name');
    $other_party_arr = return_library_array("select id,other_party_name from lib_other_party", "id", "other_party_name");
    
   

    $booking_arr = array();
   
    // echo "SELECT a.id,a.buyer_id, a.booking_no, b.job_no, b.grey_fab_qnty as qnty from wo_booking_mst a, wo_booking_dtls b, tmp_booking_id c where a.id=b.booking_mst_id and a.id=c.booking_id and c.userid=$user_id and a.item_category=2 and b.status_active=1 and b.is_deleted=0 and a.company_id in($cbo_company_name) ";die;
    $dataArray = sql_select("SELECT a.id,a.buyer_id, a.booking_no, b.job_no, b.grey_fab_qnty as qnty from wo_booking_mst a, wo_booking_dtls b, tmp_booking_id c where a.id=b.booking_mst_id and a.id=c.booking_id and c.userid=$user_id and a.item_category=2 and b.status_active=1 and b.is_deleted=0 and a.company_id in($cbo_company_name) "); // and a.company_id=$cbo_company_name    $job_no_cond
 
   
    foreach ($dataArray as $row) 
    {
        $booking_arr[$row[csf('booking_no')]]['qnty'] += $row[csf('qnty')];
        $booking_arr[$row[csf('booking_no')]]['buyer_id'] = $row[csf('buyer_id')];
        $booking_arr[$row[csf('booking_no')]]['job'] = $row[csf('job_no')];
        $booking_arr[$row[csf('booking_no')]]['booking_no'] = $row[csf('booking_no')];
        $all_job_arr[$row[csf('job_no')]] = $row[csf('job_no')];
    } 
    //var_dump($booking_arr); //and a.booking_no in($booking_nos)

    $all_job_arr = array_filter($all_job_arr);
    if(!empty($all_job_arr))
    {   
        $con = connect();
        foreach($all_job_arr as $jobNo)
        {
            execute_query("INSERT INTO TMP_JOB_NO(JOB_NO,USERID) VALUES('".$jobNo."', ".$user_id.")");
            oci_commit($con);
        }
    }
    //die;

    // Requ wise Booking show
    $reqibooking_sql="SELECT a.requisition_no, b.id, c.booking_no,c.buyer_id, b.is_sales 
    from ppl_yarn_requisition_entry a, ppl_planning_info_entry_dtls b, ppl_planning_info_entry_mst c, tmp_booking_no d 
    where a.knit_id=b.id and b.mst_id=c.id and c.booking_no=d.booking_no and d.userid=$user_id and c.company_id in($cbo_company_name) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0"; 
    // and a.company_id=$cbo_company_name    $job_no_cond //  and a.requisition_no='5678'
    //echo $reqibooking_sql;die;
    $reqibookingarray = sql_select($reqibooking_sql);
    $reqibooking_arr = array();
    foreach ($reqibookingarray as $row) 
    {
        $reqibooking_arr[$row[csf('requisition_no')]]['booking_no'] = $row[csf('booking_no')];
        $reqibooking_arr[$row[csf('requisition_no')]]['buyer_id'] = $row[csf('buyer_id')];
        $reqibooking_arr[$row[csf('requisition_no')]]['program_no'] = $row[csf('id')];
        $reqibooking_arr[$row[csf('requisition_no')]]['is_sales'] = $row[csf('is_sales')];
    }
    /*echo "<pre>";
    print_r($reqibooking_arr);die;*/

    // echo "SELECT b.id, b.ydw_no, a.job_no, sum(a.yarn_wo_qty) as qnty from  wo_yarn_dyeing_dtls a,wo_yarn_dyeing_mst b, tmp_job_no c where b.id=a.mst_id and a.job_no=c.job_no and c.userid=$user_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 group by b.id, b.ydw_no, a.job_no ";die; 
    $data_yarn = sql_select("SELECT b.id, b.ydw_no, a.job_no, sum(a.yarn_wo_qty) as qnty from  wo_yarn_dyeing_dtls a,wo_yarn_dyeing_mst b, tmp_job_no c where b.id=a.mst_id and a.job_no=c.job_no and c.userid=$user_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 group by b.id, b.ydw_no, a.job_no ");

    $yarn_booking_array = array();
    foreach ($data_yarn as $row) 
    {
        $yarn_booking_array[$row[csf('ydw_no')]]['qnty'] = $row[csf('qnty')];
        $yarn_booking_array[$row[csf('ydw_no')]]['job'] = $row[csf('job_no')];
        //$all_job_arr[$row[csf('job_no')]] = $row[csf('job_no')];
    }
    

    $job_array = array();
    $job_array_order_wise = array();
    $po_array = array();

    $sql_job = "SELECT a.job_no, a.buyer_name, a.style_ref_no, b.id, b.po_number, b.file_no, b.grouping as ref_no, c.booking_no, c.copmposition 
    from wo_po_details_master a, wo_po_break_down b, wo_booking_dtls c, tmp_job_no d 
    where a.id=b.job_id and b.id=c.po_break_down_id and a.company_name in($cbo_company_name) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.job_no=d.job_no and d.userid=$user_id 
    group by a.job_no, a.buyer_name, a.style_ref_no,b.id, b.po_number,b.file_no,b.grouping,c.booking_no,c.copmposition";
    //echo $sql_job;die;
    $sql_job_result = sql_select($sql_job);
    foreach ($sql_job_result as $row) 
    {
        $job_array[$row[csf('job_no')]]['job_no'] = $row[csf('job_no')];
        $job_array[$row[csf('job_no')]]['buyer_name'] = $row[csf('buyer_name')];
        $job_array[$row[csf('job_no')]]['style_ref_no'] = $row[csf('style_ref_no')];
        $job_array[$row[csf('job_no')]]['copmposition'] = $row[csf('copmposition')];
        $job_array[$row[csf('job_no')]]['po_number'].= $row[csf('po_number')].',';

        $po_array[$row[csf('id')]]['po_number'] = $row[csf('po_number')];

        $job_array[$row[csf('booking_no')]]['po_number'][$row[csf('po_number')]] = $row[csf('po_number')];
        $job_array[$row[csf('booking_no')]]['ref'][$row[csf('ref_no')]] = $row[csf('ref_no')];
        $job_array[$row[csf('booking_no')]]['file'][$row[csf('file_no')]] = $row[csf('file_no')];
        $job_array_order_wise[$row[csf('id')]]['style_ref_no'] = $row[csf('style_ref_no')];
        $job_array_order_wise[$row[csf('booking_no')]][$row[csf('id')]]['ref'][$row[csf('ref_no')]] = $row[csf('ref_no')];

    }
    

    $booking_without_array = array();
   
    
    $data_without = sql_select("SELECT a.id, a.booking_no, a.buyer_id, b.grey_fabric as qnty from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b, tmp_booking_id c where a.id=b.booking_mst_id and a.id=c.booking_id and c.userid=$user_id and a.status_active=1 and a.is_deleted=0 and a.company_id in($cbo_company_name) and b.status_active=1 and b.is_deleted=0");
    foreach ($data_without as $row) 
    {
        $booking_without_array[$row[csf('booking_no')]]['qnty'] += $row[csf('qnty')];
        $booking_without_array[$row[csf('booking_no')]]['booking_no'] = $row[csf('booking_no')];
        $booking_without_array[$row[csf('booking_no')]]['buyer_id'] = $row[csf('buyer_id')];
    }

    $all_issue_id_arr = array_filter($all_issue_id_arr);
    if(!empty($all_issue_id_arr))
    {    
        $con = connect();
        foreach($all_issue_id_arr as $issueId)
        {
            execute_query("INSERT INTO TMP_ISSUE_ID(ISSUE_ID,USER_ID,TYPE) VALUES(".$issueId.", ".$user_id.",1)");
            oci_commit($con);
        }
    }
    //die;


       
    $issue_return_res = sql_select("SELECT a.recv_number,a.booking_no, b.cons_quantity, c.issue_number,b.prod_id, b.id as trans_id from inv_receive_master a, inv_transaction b, inv_issue_master c, tmp_issue_id d where a.id = b.mst_id and c.id=d.issue_id and d.user_id=$user_id and d.type = 1 and a.issue_id = c.id and b.transaction_type = 4 and b.item_category = 1 and a.entry_form = 9 and b.status_active = 1 and b.is_deleted = 0 and a.status_active = 1");
    

    $transIdChk = array();
    foreach ($issue_return_res as $val) 
    {
        if($transIdChk[$val[csf("trans_id")]]=="")
        {
            $transIdChk[$val[csf("trans_id")]] = $val[csf("trans_id")];
            $issue_return_qnty_arr[$val[csf("issue_number")]][$val[csf("booking_no")]][$val[csf("prod_id")]] += $val[csf("cons_quantity")];
        }          
    }

    $all_req_no_arr = array_filter($all_req_no_arr);
    if(!empty($all_req_no_arr))
    {
        $con = connect();
        foreach($all_req_no_arr as $reqNo)
        {
            execute_query("INSERT INTO TMP_REQS_NO(REQS_NO,USERID) VALUES(".$reqNo.", ".$user_id.")");
            oci_commit($con);
        }
    }
    //die;
    
    
    $requisition_arr = array();
    $datareqsnArray = sql_select("select a.requisition_no, a.knit_id, a.yarn_qnty as qnty from ppl_yarn_requisition_entry a, tmp_reqs_no b where a.requisition_no=b.reqs_no and b.userid=$user_id and a.status_active=1 and a.is_deleted=0");
    $all_knit_id_arr = array();
    foreach ($datareqsnArray as $row) 
    {
        $requisition_arr[$row[csf('requisition_no')]]['qnty'] += $row[csf('qnty')];
        $requisition_arr[$row[csf('requisition_no')]]['knit_id'] = $row[csf('knit_id')];

        //$knit_ids.=$row[csf("knit_id")].",";
        $all_knit_id_arr[$row[csf('knit_id')]] = $row[csf('knit_id')];
    }

    $all_knit_id_arr = array_filter($all_knit_id_arr);
    if(!empty($all_knit_id_arr))
    {  
        $con = connect();
        foreach($all_knit_id_arr as $progNo)
        {
            execute_query("INSERT INTO TMP_PROG_NO(PROG_NO,USERID) VALUES(".$progNo.", ".$user_id.")");
            oci_commit($con);
        }
    }
    //die;
    
    // $knit_ids =chop($knit_ids,",");
    // $knit_ids=implode(",",array_filter(array_unique(explode(",",$knit_ids))));

    if(!empty($all_knit_id_arr))
    {
        $planning_arr = return_library_array("SELECT b.id, a.booking_no from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, tmp_prog_no c where a.id=b.mst_id  and b.id=c.prog_no and c.userid=$user_id", "id", "booking_no"); 
        //var_dump($planning_arr);

        $sql_planning_info_arr =  "SELECT b.id, a.booking_no from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, tmp_prog_no c where a.id=b.mst_id and b.id=c.prog_no and c.userid=$user_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
        $booking_no_arr = array();
        foreach (sql_select($sql_planning_info_arr) as $rows) 
        {
            array_push($booking_no_arr,$rows[csf('booking_no')]);
        }

        $booking_no_cond= where_con_using_array($booking_no_arr,1,"a.booking_no");

        $sql_smn_info =  "SELECT c.id, c.requisition_number, c.company_id, c.buyer_name, c.style_ref_no, a.booking_no, c.buyer_ref from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b, sample_development_mst c
        where a.id=b.booking_mst_id and b.style_id=c.id and  c.company_id in($cbo_company_name) $booking_no_cond and c.entry_form_id=203 and a.entry_form_id=140 and a.status_active=1 and a.is_deleted=0 and b.status_active=1
        and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by c.id DESC";

        //echo $sql_smn_info;die;

      $smn_info_arr = array();
      foreach (sql_select($sql_smn_info) as $row) 
      {
        $smn_info_arr[$row[csf('booking_no')]]['requisition_number'] = $row[csf('requisition_number')];
        $smn_info_arr[$row[csf('booking_no')]]['buyer_name'] = $row[csf('buyer_name')];
        $smn_info_arr[$row[csf('booking_no')]]['style_ref_no'] = $row[csf('style_ref_no')];
        $smn_info_arr[$row[csf('booking_no')]]['buyer_ref'] = $row[csf('buyer_ref')];
      }
     // var_dump($smn_info_arr);
        
    }
   
    $usd_arr = array();
    $sqlSelectData = sql_select("select con_date,conversion_rate from currency_conversion_rate where currency=2 and is_deleted=0 order by con_date desc");
    foreach ($sqlSelectData as $row) 
    {
        $usd_arr[date('d-m-Y',strtotime($row[csf('con_date')]))] = $row[csf('conversion_rate')];
    }

    $r_id555=execute_query("DELETE FROM TMP_BOOKING_ID WHERE USERID=$user_id ");
    $r_id666=execute_query("DELETE FROM TMP_BOOKING_NO WHERE USERID=$user_id ");
    $r_id111=execute_query("DELETE FROM TMP_JOB_NO WHERE USERID=$user_id ");
    $r_id222=execute_query("DELETE FROM TMP_ISSUE_ID WHERE USER_ID=$user_id and TYPE = 1");
    $r_id333=execute_query("DELETE FROM TMP_REQS_NO WHERE USERID=$user_id ");
    $r_id444=execute_query("DELETE FROM TMP_PROG_NO WHERE USERID=$user_id ");
    
    oci_commit($con);
    disconnect($con);
    

    ?>
    <style>
        .wrd_brk{word-break: break-all;word-wrap: break-word;}          
    </style>
    <?
    
    if ($zero_val == 1) 
    {
        $value_width = 3030;
        $span = 32;
        $column = '';
    } 
    else 
    {
        $value_width = 3280;
        $span = 34;
        $column = '<th rowspan="2" width="90" class="wrd_brk">Avg. Rate (Tk)</th><th rowspan="2" width="110">Stock Value</th>';
    }

    ob_start();

    if ($type == 1) // Show Button
    {
        ?>
        <fieldset style="width:<? echo $value_width + 18; ?>px;">
            <table cellpadding="0" cellspacing="0" width="<? echo $value_width; ?>" style="float: left;">
                <tr>
                    <td align="center" width="100%" colspan="<? echo $span; ?>" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
                </tr>
                <tr>
                    <td align="center" width="100%" colspan="<? echo $span; ?>" style="font-size:16px"><strong>From Date: <? echo change_date_format(str_replace("'", "", $txt_date_from)); ?> To Date: <? echo change_date_format(str_replace("'", "", $txt_date_to)); ?></strong></td>
                </tr>
            </table>
            <table style="float: left; margin-bottom: 10px;" width="250" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                <thead>
                    <th colspan="2"><b>Summary</b></th>
                </thead>
                <?
                //summary issue qnty
                $inside_outside_array_summary = array();
                $kk = 1;$caption_summary=''; $summary_issueQnty=0;
                $grand_total=0; 
                foreach ($result_summery as $rows) 
                {
                    if ($rows[csf('knit_dye_source')] == 1) 
                    {
                        $caption_summary = 'Inside';
                    } 
                    else if ($rows[csf('knit_dye_source')] == 3) 
                    {
                        $caption_summary = 'Outside';
                    } 
                    else
                    {

                        $caption_summary = '';
                    }
                 
                    if (in_array($rows[csf('knit_dye_source')], $inside_outside_array_summary)) 
                    {
                        $print_caption = 0;
                    } 
                    else 
                    {
                        $print_caption = 1;
                        $inside_outside_array_summary[$kk] = $rows[csf('knit_dye_source')];
                    }
                   
                    if ($print_caption == 1) 
                    {
                         $summary_issueQnty=$rows[csf('issue_qnty')];
                    }
                    if ($print_caption == 1 && $rows[csf('knit_dye_source')] !=0 ) 
                    {
                        ?>
                        <tr>
                            <td><b><?php echo $caption_summary; ?></b></td>

                            <td align="right"><b><? echo number_format($summary_issueQnty, 2); ?></b></td>

                        </tr>    
                        <?
                       $grand_total+=$summary_issueQnty; 
                    }
                    
                    $kk++;
                }

                foreach ($issue_purpose_wise_issue_qnty_arr as $key => $values) 
                {
                    ?>
                    <tr>
                        <td><b><? echo  $yarn_issue_purpose[$key]; ?></b></td>
                        <td align="right"><b><? echo  number_format($values,2); ?></b></td>
                    </tr>
                    <?
                    $grand_total+=$values; 
                }
                //end summary issue qnty
                $g_total=0;
                $sample=0;
                ?>
                <tr style="background-color: #f9f9f9;">
                    <td><b>Total</b></td>
                    <td align="right"><b><? echo  number_format($grand_total,2); ?></b></td>
                </tr>
                <?
                    foreach ($sample_without_order_issue_qnty_arr as $key => $values) 
                    {
                        ?>
                        <tr>
                            <td><b><? echo  $yarn_issue_purpose[$key]; ?></b></td>
                            <td align="right"><b><? echo  number_format($values,2);$sample+=$values; ?></b></td>
                        </tr>
                        <?
                    }
                ?>
                <tr>
                    <td><b>Grand Total</b></td>
                    <td align="right"><b><?php echo number_format($grand_total+ $sample,2);?></b></td>
                </tr>
            </table>

            <table width="<? echo $value_width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" style="float: left;">              
                <thead>
                    <th width="30" class="wrd_brk">SL</th>
                    <th width="100" class="wrd_brk">Company Name</th>
                    <th width="90" class="wrd_brk">Job No</th>
                    <th width="100" class="wrd_brk">Buyer Name</th>
                    <th width="80" class="wrd_brk">File No.</th>
                    <th width="80" class="wrd_brk">Ref. No</th>
                    <th width="135" class="wrd_brk">Style No</th>
                    <th width="150" class="wrd_brk">Order No</th>
                    <th width="100" class="wrd_brk">Issue Basis</th>
                    <th width="110" class="wrd_brk">Issue No</th>
                    <th width="70" class="wrd_brk">Issue Date</th>
                    <th width="80" class="wrd_brk">Challan No</th>
                    <th width="60" class="wrd_brk">Count</th>
                    <th width="70" class="wrd_brk">Yarn Brand</th>
                    <th width="100" class="wrd_brk">Composition</th> 
                    <th width="80" class="wrd_brk">Type</th>
                    <th width="90" class="wrd_brk">Color</th>
                    <th width="70" class="wrd_brk">Lot No</th>
                    <th width="90" class="wrd_brk">Issue Qty</th>
                    <? echo $column; ?>
                    <th width="90" class="wrd_brk">Returnable Qty.</th>
                    <th width="100" class="wrd_brk">Return Qty.</th>
                    <th width="100" class="wrd_brk">Net Issue Qty.</th>
                    <th width="60" class="wrd_brk">Rate/Kg</th>
                    <th width="100" class="wrd_brk">Net Issue Amount</th>
                    <th width="100" class="wrd_brk">Issue Purpose</th> 
                    <th width="100" class="wrd_brk">Using Item</th>
                    <th width="110" class="wrd_brk">Booking</th>
                    <th width="100" class="wrd_brk">Reqn. No</th>
                    <th width="100" class="wrd_brk">Booking/ Reqn. Qty</th>
                    <th width="130" class="wrd_brk">Issue To</th>
                    <th width="130" class="wrd_brk">Location</th>  
                    <th class="wrd_brk">Store</th>
                </thead>
            </table>
            <div style="width:<? echo $value_width + 18; ?>px; overflow-y: scroll; max-height:380px; float: left;" id="scroll_body">
                <table width="<? echo $value_width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" style="float: left;">    
                    <tbody>
                        <?
                        $i = 1;
                        $total_iss_qnty = 0;
                        $issue_qnty = 0;
                        $issue_amount = 0;
                        $return_qnty=0;
                        $issue_amount_return=0;
                        $issue_balance_qnty=0;
                        $issue_amount_qnty=0;
                        $caption = '';
                        $knitting_party = '';
                        $total_amount = 0;
                        $grand_total_amount = 0;

                        $issue_amount_grand=0;
                        $issue_qnty_grand = 0;
                        $return_qnty_grand = 0;
                        $total_amount_grand=0;
                        $issue_amount_return_grand=0;
                        $issue_balance_qnty_grand=0;
                        $issue_amount_qnty_grand=0;

                        $inside_outside_array = array();
                        $issue_purpose_array = array();
                        //$reqi_wise_booking=0;
                        foreach ($result as $row) 
                        {
                            $exchangeRate=$usd_arr[date('d-m-Y',strtotime($row[csf('issue_date')]))];
                            if($exchangeRate =="")
                            {
                                foreach ($usd_arr as $rate_date => $rat) 
                                {
                                    if(strtotime($rate_date) <= strtotime($row[csf('issue_date')]))
                                    {
                                        $rate_date = date('d-m-Y',strtotime($rate_date));
                                        $exchangeRate=$rat;
                                        break;
                                    }
                                }
                            }

                            //----------------------------------------------------------------------
                            /*$exchangeRate=$usd_arr[date('d-m-Y',strtotime($row[csf('issue_date')]))];
                            if(is_null($exchangeRate)){
                                for($day=1;$day<366;$day++){
                                    $previousDate=date('d-m-Y', strtotime("-$day day",strtotime($row[csf('issue_date')])));
                                    $exchangeRate=$usd_arr[$previousDate];
                                    if(!is_null($exchangeRate)){break;}
                                }
                            }*/
                            //-----------------------------------------------------------------------                   
                            
                            if ($i % 2 == 0)
                                $bgcolor = "#E9F3FF";
                            else
                                $bgcolor = "#FFFFFF";

                            if ($row[csf('knit_dye_source')] == 1) 
                            {
                                $knitting_party = $company_arr[$row[csf('knit_dye_company')]];
                                $knitting_location = $location_arr[$row[csf('location_id')]];
                                $caption = 'Inside';
                            } 
                            else if ($row[csf('knit_dye_source')] == 3) 
                            {
                                $knitting_party = $supplier_arr[$row[csf('knit_dye_company')]];
                                $knitting_location = $locat_arr[$row[csf('location_id')]];
                                $caption = 'Outside';
                            } 
                            else 
                            {
                                $knitting_party = "";
                                $knitting_location = '';
                                $caption = '';
                            }

                            if (in_array($row[csf('knit_dye_source')], $inside_outside_array)) {
                                $print_caption = 0;
                            } else {
                                $print_caption = 1;
                                $inside_outside_array[$i] = $row[csf('knit_dye_source')];
                            }

                            if ($print_caption == 1 && $i != 1) 
                            {
                                ?>
                                <tr class="tbl_bottom"> <!-- inhouse total -->
                                    <td colspan="18" align="right"><b>Total</b></td>
                                    <td align="right" class="wrd_brk" ><b><?php echo number_format($issue_qnty, 2); ?></b></td>                                   
                                    <?
                                    if ($zero_val == 0) 
                                    {
                                        ?><td align="right" class="wrd_brk" ></td><td align="right"><b><?php echo number_format($total_amount, 2); ?></b></td><? 
                                    }
                                    ?>
                                    <td align="right" class="wrd_brk" ><b><?php echo number_format($return_qnty, 2); ?></b></td>
                                    <td align="right" class="wrd_brk" ><b><?php echo number_format($issue_amount_return, 2);$total_issue_return=0; ?></b></td>

                                    <td align="right" class="wrd_brk" ><b><?php echo number_format($issue_balance_qnty, 2);//$total_issue_return=0; ?></b></td>
                                    <td>&nbsp;</td>
                                       <td align="right" class="wrd_brk" ><b><?php echo number_format($issue_amount_qnty, 2);//$total_issue_return=0; ?></b></td>
                                    <td colspan="8"></td>
                                </tr>   
                                <?
                                $issue_amount_grand+=$issue_amount;
                                $issue_qnty_grand+=$issue_qnty;
                                $return_qnty_grand+=$return_qnty;
                                $total_amount_grand+=$total_amount;
                                $issue_amount_return_grand+=$issue_amount_return;
                                $issue_balance_qnty_grand+=$issue_balance_qnty;
                                $issue_amount_qnty_grand+=$issue_amount_qnty;
                                $issue_amount=0;
                                $issue_qnty = 0;
                                $total_amount = 0;
                                $return_qnty = 0;
                                $issue_amount_return=0;
                                $issue_balance_qnty=0;
                                $issue_amount_qnty=0;
                            }

                            if ($print_caption == 1) 
                            {
                                ?>
                                <tr><td colspan="<? echo $span; ?>" class="wrd_brk" bgcolor="#CCCCCC"><b><?php echo $caption; ?></b></td></tr>    
                                <?
                            }

                            // =====start oooooo
                            ?>

                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr<? echo $i; ?>">
                                
                                <?
                                $buyer = '';
                                $job_no = '';
                                $styRef = '';
                                $booking_reqsn_qty = 0;
                                $knit_id = '';
                                $order_no = '';
                                $order_ref = '';
                                $order_file = '';

                                if ($row[csf('issue_basis')] == 1) 
                                {
                                    //$booking_req_no = $row[csf('booking_no')];
                                    $reqi_wise_booking = $row[csf('booking_no')];
                                    if ($row[csf('issue_purpose')] == 1 || $row[csf('issue_purpose')] == 4) 
                                    {
                                        $job_no = $job_array[$booking_arr[$row[csf('booking_no')]]['job']]['job_no'];
                                        $buyer = $job_array[$booking_arr[$row[csf('booking_no')]]['job']]['buyer_name'];
                                        $styRef = $job_array[$booking_arr[$row[csf('booking_no')]]['job']]['style_ref_no'];

                                        $order_no = $job_array[$booking_req_no]['po_number'];
                                        $order_ref = $job_array[$booking_req_no]['ref'];
                                        $order_file = $job_array[$booking_req_no]['file'];
                                        $booking_reqsn_qty = $booking_arr[$row[csf('booking_no')]]['qnty'];
                                    } 
                                    else if ($row[csf('issue_purpose')] == 2) 
                                    {
                                        $job_no = $job_array[$yarn_booking_array[$row[csf('booking_no')]]['job']]['job_no'];
                                        //$buyer = $job_array[$yarn_booking_array[$row[csf('booking_no')]]['job']]['buyer_name'];
                                        $buyer = $row[csf('buyer_id')];
                                        $styRef = $job_array[$yarn_booking_array[$row[csf('booking_no')]]['job']]['style_ref_no'];
                                        //$order_no = $job_array[$booking_req_no]['po_number'];
                                         $order = chop($job_array[$yarn_booking_array[$row[csf('booking_no')]]['job']]['po_number'],',');
                                        //   $poIds=chop($order_n,',');
                                        $order_no=array_unique(explode(",",$order));
                                        $order_ref = $job_array[$booking_req_no]['ref'];
                                        $order_file = $job_array[$booking_req_no]['file'];
                                    } 
                                    else 
                                    {
                                        $job_no = '';
                                        //$buyer = '';
                                        $buyer = $booking_without_array[$row[csf('booking_no')]]['buyer_id'];
                                        $styRef = '';
                                        $order_no = '';
                                        $order_ref = '';
                                        $order_file = '';
                                        $booking_reqsn_qty = '';
                                    }
                                } 
                                else if ($row[csf('issue_basis')] == 3) 
                                {
                                    $booking_req_no = $row[csf('requisition_no')];

                                    //$reqibooking_arr[$row[csf('requisition_no')]] = $row[csf('booking_no')];
                                    $reqi_wise_booking = $reqibooking_arr[$row[csf('requisition_no')]]['booking_no'];
                                    //echo $row[csf('requisition_no')].'='.$reqi_wise_booking.'<br>';
                                   
                                    $knit_id = $requisition_arr[$row[csf('requisition_no')]]['knit_id'];
                                    $job_no = $job_array[$booking_arr[$planning_arr[$knit_id]]['job']]['job_no'];
                                    $buyer = $booking_arr[$planning_arr[$knit_id]]['buyer_id'];
                                    //$buyer = $reqibooking_arr[$row[csf('requisition_no')]]['buyer_id'];
                                    $styRef = $job_array[$booking_arr[$planning_arr[$knit_id]]['job']]['style_ref_no'];

                                    //$order_no = $job_array[$booking_req_no]['po_number'];
                                    $order_no = $job_array[$booking_arr[$planning_arr[$knit_id]]['booking_no']]['po_number'];

                                    //$order_no=$job_array[$booking_arr[$planning_arr[$knit_id]]['job']]['po_number'];
                                    $order_ref = $job_array[$reqi_wise_booking]['ref'];
                                     $order_file = $job_array[$booking_arr[$planning_arr[$knit_id]]['booking_no']]['file'];

                                    $booking_reqsn_qty = $requisition_arr[$row[csf('requisition_no')]]['qnty'];
                                } 
                                else 
                                {
                                    $booking_req_no = "";
                                    $buyer = $row[csf('buyer_id')];
                                    $job_no = '';
                                    $styRef = '';
                                    $booking_reqsn_qty = 0;
                                    $order_no = '';
                                    $order_ref = '';
                                    $order_file = '';
                                }

                                ?>

                                <td width="30"><? echo $i; ?></td>
                                <td width="100" align="center" class="wrd_brk" ><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
                                <td width="90" align="center" class="wrd_brk" ><p><? echo $job_no; ?></p></td>
                                <td width="100" class="wrd_brk" title="<? echo $buyer;?>"><p><? echo $buyer_arr[$buyer]; ?></p></td>
                                <td width="80" class="wrd_brk" ><p><?
                                        $file_no = implode(",", $order_file);
                                        echo $file_no;
                                        ?></p></td>
                                <td width="80" class="wrd_brk" ><p><?
                                        $ref_no = implode(",", $order_ref);
                                        echo $ref_no;
                                        ?></p></td>   
                                <td width="135" class="wrd_brk" ><p><? echo $styRef; ?> &nbsp;</p></td>
                                <td width="150" class="wrd_brk" ><p><?
                                        $order_n = implode(", ", $order_no);
                                        echo $order_n;
                                        ?></p></td>
                                <td width="100" class="wrd_brk" ><p><? echo $issue_basis[$row[csf('issue_basis')]]; ?></p></td>
                                <td width="110" class="wrd_brk" ><p><? echo $row[csf('issue_number')]; ?></p></td>
                                <td width="70" class="wrd_brk"  align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
                                <td width="80" class="wrd_brk" ><p><? echo $row[csf('challan_no')]; ?></p></td>
                                <td width="60" class="wrd_brk"  align="center"><p><? $yarn_count = $count_arr[$row[csf('yarn_count_id')]]; echo $yarn_count ; ?>  &nbsp;</p></td>
                                <td width="70" class="wrd_brk" ><p><? echo $brand_arr[$row[csf('brand')]]; ?></p></td>
                                <td width="100" class="wrd_brk" ><p><?  echo $composition[$row[csf('yarn_comp_type1st')]].$row[csf('yarn_comp_percent1st')].'%'; ?></p></td>
                                <td width="80" class="wrd_brk" ><p><? echo $yarn_type[$row[csf('yarn_type')]]; ?></p></td>
                                <td width="90" class="wrd_brk" ><p><? echo $color_arr[$row[csf('color')]]; ?></p></td>
                                <td width="70" class="wrd_brk" ><p><? echo $row[csf('lot')]; ?></p></td>
                                <td width="90" class="wrd_brk"  align="right">
                                    <?
                                    echo number_format($row[csf('issue_qnty')], 2);
                                    $total_iss_qnty += $row[csf('issue_qnty')];
                                    $issue_qnty += $row[csf('issue_qnty')];
                                    ?>
                                </td>
                                <?
                                if ($zero_val == 0) 
                                {
                                    ?>
                                    <td width="90" align="right" class="wrd_brk" >
                                        <?
                                        $avgg = $row[csf('avg_rate_per_unit')];
                                        $avg_rate = $avgg / 78;
                                        echo number_format($avg_rate, 4);
                                        ?>
                                    </td> 
                                    <td width="110" align="right" class="wrd_brk" >
                                        <?
                                        $amount = $row[csf('issue_qnty')] * $avg_rate;
                                        echo number_format($amount, 2);
                                        $total_amount += $amount;
                                        $grand_total_amount += $amount;
                                        ?>
                                    </td> 
                                    <?
                                }
                                ?>
                                <td width="90" align="right" class="wrd_brk" >
                                    <?
                                    echo number_format($row[csf('return_qnty')], 2);
                                    $total_ret_qnty += $row[csf('return_qnty')];
                                    $return_qnty += $row[csf('return_qnty')];
                                    ?>
                                </td> <!-- report one================================ --> 
                                <td width="100" class="wrd_brk" align="right">
                                    <p>
                                        <? 
                                            echo number_format($issue_return_qnty_arr[$row[csf("issue_number")]][$row[csf("requisition_no")]][$row[csf("product_id")]],2); 
                                            $total_issue_return += $issue_return_qnty_arr[$row[csf("issue_number")]][$row[csf("requisition_no")]][$row[csf("product_id")]];
                                            $grand_issue_return += $issue_return_qnty_arr[$row[csf("issue_number")]][$row[csf("requisition_no")]][$row[csf("product_id")]];

                                                $total_iss_amount_return += $issue_return_qnty_arr[$row[csf("issue_number")]][$row[csf("requisition_no")]][$row[csf("product_id")]];
                                                $issue_amount_return += $issue_return_qnty_arr[$row[csf("issue_number")]][$row[csf("requisition_no")]][$row[csf("product_id")]];
                                        ?>
                                    </p>
                                </td>
                                <td width="100" class="wrd_brk" align="right">
                                    <p><? echo number_format($row[csf('issue_qnty')]-$issue_return_qnty_arr[$row[csf("issue_number")]][$row[csf("requisition_no")]][$row[csf("product_id")]],2); 
                                    $issue_balance_qnty+=$row[csf('issue_qnty')]-$issue_return_qnty_arr[$row[csf("issue_number")]][$row[csf("requisition_no")]][$row[csf("product_id")]];
                                        //$total_issue_balance_qnty +=  $issue_balance_qnty;
                                    ?></p>
                                </td>
                                <td width="60" align="right" class="wrd_brk" ><? echo number_format($row[csf('cons_rate')]/$exchangeRate, 2); ?></td>

                                 <td width="100" class="wrd_brk" align="right">
                                    <p><? echo number_format(($row[csf('cons_rate')]/$exchangeRate)*($row[csf('issue_qnty')]-$issue_return_qnty_arr[$row[csf("issue_number")]][$row[csf("requisition_no")]][$row[csf("product_id")]]),2); 
                                    $issue_amount_qnty+=($row[csf('cons_rate')]/$exchangeRate)*($row[csf('issue_qnty')]-$issue_return_qnty_arr[$row[csf("issue_number")]][$row[csf("requisition_no")]][$row[csf("product_id")]]);
                                        //$total_issue_balance_qnty +=  $issue_balance_qnty;
                                    ?></p>
                                </td>
                                <td width="100" class="wrd_brk" ><p><? echo $yarn_issue_purpose[$row[csf('issue_purpose')]]; ?></p></td> 
                                <td width="100" class="wrd_brk"><p><? echo $using_item_arr[$row[csf("using_item")]]; ?></p></td>
                                <td width="100" class="wrd_brk" align="center"><? echo $reqi_wise_booking; unset($reqi_wise_booking); ?></td>
                                <td width="110" class="wrd_brk" align="center" title="<? echo $row[csf('requisition_no')]; ?>"><? echo $booking_req_no;?> </td>
                                
                                <td width="100" class="wrd_brk"  align="right"><? echo number_format($booking_reqsn_qty, 2); ?></td>
                                <td width="130" class="wrd_brk" ><p><? echo $knitting_party; ?></p></td>
                                <td width="130" class="wrd_brk" ><p><? echo $knitting_location; ?></p></td>
                                <td class="wrd_brk" ><p><? echo $store_arr[$row[csf('store_id')]]; ?></td>
                            </tr>   
                            <?
                            $i++;
                        }
                        //===============end

                        if (count($result) > 0) 
                        {
                            ?>
                            <tr class="tbl_bottom">
                                <td colspan="18" align="right"><b>Total</b></td>
                                <td align="right" class="wrd_brk" ><b><?php echo number_format($issue_qnty, 2); ?></b></td>
                                
                                    <?
                                    if ($zero_val == 0) {
                                        ?>
                                        <td align="right" class="wrd_brk" ></td><td align="right"><b><?php echo number_format($total_amount, 2); ?></b></td>
                                        <?
                                    }
                                    ?>
                                    <td align="right" class="wrd_brk" ><b><?php echo number_format($return_qnty, 2);//$return_qnty=0; ?></b></td>
                                    <td align="right" class="wrd_brk" ><b><?php echo number_format($issue_amount_return, 2);$total_issue_return=0; ?></b></td>

                                    <td align="right" class="wrd_brk" ><b><?php echo number_format($issue_balance_qnty, 2);
                                    //$total_issue_return=0; 

                                    ?></b></td>
                                    <td>&nbsp;</td>
                                     <td align="right" class="wrd_brk" ><b><?php echo number_format($issue_amount_qnty, 2);
                                    //$total_issue_return=0; 

                                    ?></b></td>
                                     
                                    <td colspan="8"></td>
                            </tr>
                            <?
                            
                            $issue_qnty_grand+=$issue_qnty;
                            $issue_amount_grand+=$issue_amount;
                            $return_qnty_grand+=$return_qnty;
                            $total_amount_grand+=$total_amount;
                            $issue_amount_return_grand+=$issue_amount_return;
                            $issue_balance_qnty_grand+=$issue_balance_qnty;
                            $issue_amount_qnty_grand+=$issue_amount_qnty;
                        }
                        ?>
                        <tr style="background-color: grey;" >
                            <td colspan="18" align="right"><b>Inside + Outside Grand Total</b></td>
                            <td align="right" class="wrd_brk"" ><b><?php echo number_format($issue_qnty_grand, 2); ?></b></td>
                           
                                <?
                                if ($zero_val == 0) 
                                {
                                    ?>
                                    <td align="right" class="wrd_brk" ></td><td align="right"><b><?php echo number_format($total_amount_grand, 2); ?></b></td>
                                    <?
                                }
                                ?>
                                <td align="right" class="wrd_brk"" ><b><?php echo number_format($return_qnty_grand, 2); ?></b></td>
                                <td align="right" class="wrd_brk"" ><b><?php echo number_format($issue_amount_return_grand, 2);$total_issue_return=0; ?></b></td>
                                 <td align="right" class="wrd_brk" ><b><?php echo number_format($issue_balance_qnty_grand, 2);//$total_issue_return=0; ?></b></td>
                                 <td></td>
                                  <td align="right" class="wrd_brk" ><b><?php echo number_format($issue_amount_qnty_grand, 2);//$total_issue_return=0; ?></b></td>
                                 
                                <td colspan="8"></td>
                        </tr>       
                        
						<?
                        $k = 1;
                        $issue_amount = 0;
                        $issue_qnty = 0;
                        $knitting_party = '';
                        $total_amount = 0;
                        $return_qnty=0;
						$query="SELECT a.company_id, a.id as issue_id, a.issue_number, a.issue_basis, a.issue_purpose, a.booking_id, a.booking_no, a.buyer_id, a.issue_date, a.knit_dye_source, a.knit_dye_company, a.challan_no, a.other_party, b.requisition_no, b.store_id, b.brand_id, b.cons_quantity as issue_qnty, b.return_qnty, b.cons_rate, b.cons_amount, c.brand, c.yarn_count_id, c.yarn_type, c.lot, c.color, c.avg_rate_per_unit, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.id as product_id from inv_issue_master a, inv_transaction b, product_details_master c where a.id=b.mst_id and b.prod_id=c.id and a.item_category=1 and a.entry_form=3 and a.company_id in($cbo_company_name) and a.issue_basis in (1,2) and a.issue_purpose in (7,8,12,15,38,46,50,51) and a.issue_date between '$from_date' and '$to_date' and  b.item_category=1 and b.transaction_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $lot_no $yarn_type_cond $yarn_count_cond $issue_purpose_cond $using_item_cond $booking_req_cond order by a.issue_purpose, a.issue_date";
                        //}
                        //echo $query;
                        $nameArray = sql_select($query);
						
						//for library dtls
						$lib_arr = array();
						foreach($nameArray as $row)
						{
							$lib_arr['brand'][$row[csf('brand')]] = $row[csf('brand')];
							$lib_arr['yarn_count'][$row[csf('yarn_count_id')]] = $row[csf('yarn_count_id')];
							$lib_arr['color'][$row[csf('color')]] = $row[csf('color')];

                            //$yt_issue_numbers.="'".$row[csf("issue_number")]."',";
                            $all_yt_issue_id_arr[$row[csf('issue_id')]] = $row[csf('issue_id')];
						}

                        $all_yt_issue_id_arr = array_filter($all_yt_issue_id_arr);
                        if(!empty($all_yt_issue_id_arr))
                        {
                            $con = connect();
                            execute_query("DELETE FROM TMP_ISSUE_ID WHERE USER_ID = ".$user_id." and TYPE = 2 ");
                            oci_commit($con);
                            
                            $con = connect();
                            foreach($all_yt_issue_id_arr as $issueId)
                            {
                                execute_query("INSERT INTO TMP_ISSUE_ID(ISSUE_ID,USER_ID,TYPE) VALUES(".$issueId.", ".$user_id.",2)");
                                oci_commit($con);
                            }
                        }
                        //die;
						
                        // $yt_issue_numbers =chop($yt_issue_numbers,",");

                        // $yt_issue_numbers=implode(",",array_filter(array_unique(explode(",",$yt_issue_numbers))));

                        // if(!empty($all_yt_issue_id_arr))
                        // {
                           /*  $yt_issue_numbers=explode(",",$yt_issue_numbers);  
                            $issue_numbers_chnk=array_chunk($yt_issue_numbers,999);
                            $yt_issue_no_cond=" and";
                            foreach($issue_numbers_chnk as $dtls_id)
                            {
                                if($yt_issue_no_cond==" and")  $yt_issue_no_cond.="(c.issue_number in(".implode(',',$dtls_id).")"; else $yt_issue_no_cond.=" or c.issue_number in(".implode(',',$dtls_id).")";
                            }
                            $yt_issue_no_cond.=")"; */
                            //echo $issue_no_cond;die;
                        
                            
                            $yt_issue_return_res = sql_select("SELECT a.recv_number,a.booking_no, b.cons_quantity, c.issue_number,b.prod_id, b.id as trans_id from inv_receive_master a, inv_transaction b, inv_issue_master c, tmp_issue_id d where a.id = b.mst_id and a.issue_id = c.id and c.id=d.issue_id and d.user_id=$user_id and d.type=2 and b.transaction_type = 4 and b.item_category = 1 and a.entry_form = 9 and b.status_active = 1 and b.is_deleted = 0 and a.status_active = 1 $yt_issue_no_cond");
                        //}

                        $yt_transIdChk = array();
                        foreach ($yt_issue_return_res as $val) 
                        {
                            if($yt_transIdChk[$val[csf("trans_id")]]=="")
                            {
                                $yt_transIdChk[$val[csf("trans_id")]] = $val[csf("trans_id")];
                                $yt_issue_return_qnty_arr[$val[csf("issue_number")]][$val[csf("prod_id")]] += $val[csf("cons_quantity")];
                            }          
                        }
                        //var_dump($yt_issue_return_qnty_arr);

						$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0 and id in(".implode(',', $lib_arr['color']).")", "id", "color_name");
						$count_arr = return_library_array("select id, yarn_count from lib_yarn_count where status_active=1 and is_deleted=0 and id in(".implode(',', $lib_arr['yarn_count']).")", 'id', 'yarn_count');
						$brand_arr = return_library_array("select id, brand_name from lib_brand where status_active=1 and is_deleted=0 and id in(".implode(',', $lib_arr['brand']).")", 'id', 'brand_name');
						//end for library dtls

                        $r_id777=execute_query("DELETE FROM TMP_ISSUE_ID WHERE USER_ID=$user_id and TYPE = 2");
                        if($r_id777)
                        {
                            oci_commit($con);
                        }
						
                        $grnd_purpose_issue_qnty=0;
                        $grnd_purpose_issue_amount=0;
                        $grnd_purpose_total_amount=0;
                        $grnd_purpose_return_qnty=0;
                        $grnd_purpose_issue_return_qnty=0;
                        $grnd_purpose_issue_balance_qnty=0;
                        $grnd_purpose_issue_amount_qnty=0;

                        foreach ($nameArray as $row) 
                        {
                            $exchangeRate=$usd_arr[date('d-m-Y',strtotime($row[csf('issue_date')]))];
                            if($exchangeRate =="")
                            {
                                foreach ($usd_arr as $rate_date => $rat) 
                                {
                                    if(strtotime($rate_date) <= strtotime($row[csf('issue_date')]))
                                    {
                                        $rate_date = date('d-m-Y',strtotime($rate_date));
                                        $exchangeRate=$rat;
                                        break;
                                    }
                                }
                            }
                            
                            //----------------------------------------------------------------------
                            /*$exchangeRate=$usd_arr[date('d-m-Y',strtotime($row[csf('issue_date')]))];
                            if(is_null($exchangeRate)){
                                for($day=1;$day<366;$day++){
                                    $previousDate=date('d-m-Y', strtotime("-$day day",strtotime($row[csf('issue_date')])));
                                    $exchangeRate=$usd_arr[$previousDate];
                                    if(!is_null($exchangeRate)){break;}
                                }
                            }*/
                            //-----------------------------------------------------------------------                   
                           
                            if ($i % 2 == 0)
                                $bgcolor = "#E9F3FF";
                            else
                                $bgcolor = "#FFFFFF";

                            if ($row[csf('knit_dye_source')] == 1) 
                            {
                                $knitting_party = $company_arr[$row[csf('knit_dye_company')]];
                                $knitting_location = $location_arr[$row[csf('location_id')]];
                                
                            } else if ($row[csf('knit_dye_source')] == 3) 
                            {
                                $knitting_party = $supplier_arr[$row[csf('knit_dye_company')]];
                                $knitting_location = $locat_arr[$row[csf('location_id')]];
                                
                            } 
                            else 
                            {
                                $knitting_party = "";
                                $knitting_location = '';
                               
                            }
                            if (in_array($row[csf('issue_purpose')], $issue_purpose_array)) 
                            {
                                $print_caption = 0;
                            } 
                            else 
                            {
                                $print_caption = 1;
                                $issue_purpose_array[$i] = $row[csf('issue_purpose')];
                            }
                            

                            if ($print_caption == 1 && $k != 1) 
                            {
                                ?>
                                <tr class="tbl_bottom">
                                <td colspan="18" align="right"><b>Total</b></td>
                                <td align="right" class="wrd_brk" ><b><?php echo number_format($issue_qnty, 2); ?></b></td>
                                
                                    <?
                                    if ($zero_val == 0) 
                                    {
                                        ?>
                                        <td align="right" class="wrd_brk" ></td><td align="right"><b><?php echo number_format($total_amount, 2); ?></b></td>
                                        <?
                                    }
                                    ?>
                                    <td align="right" class="wrd_brk" ><b><?php echo number_format($return_qnty, 2); ?></b></td>
                                    <td align="right" class="wrd_brk">
                                        <p>
                                            <? 
                                            echo number_format($yt_issue_return_qnty_arr[$row[csf("issue_number")]][$row[csf("product_id")]],2); $returnQntypurpose=$yt_issue_return_qnty_arr[$row[csf("issue_number")]][$row[csf("product_id")]];
                                            ?>
                                        </p>
                                    </td>
                                    <td>&nbsp;</td>
                                    <td align="right" class="wrd_brk">
                                    	&nbsp;
                                        <p>
                                            <? 
                                            //echo number_format($issue_return_qnty_arr[$row[csf("issue_number")]][$row[csf("product_id")]],2); $returnQntypurpose=$issue_return_qnty_arr[$row[csf("issue_number")]][$row[csf("product_id")]];
                                            ?>
                                        </p>
                                    </td>
                                    <td>&nbsp;</td>
                                    <td colspan="8"></td></tr>    
                                <?
                                $issue_qnty = 0;
                                $issue_amount=0;
                                $total_amount = 0;
                                $return_qnty = 0;
                                $total_issue_return=0;                    
                                $total_issue_balance_qnty=0;                    
                            }
                            if ($print_caption == 1) 
                            {
                                ?>
                                <tr><td colspan="<? echo $span; ?>" style="font-size:14px;" bgcolor="#CCCCCC"><b><?php echo $yarn_issue_purpose[$row[csf('issue_purpose')]]; ?></b></td></tr> 
                                <?
                            }
                            ?>

                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr<? echo $i; ?>">
                                
                                <?
                                $buyer = '';
                                $job_no = '';
                                $styRef = '';
                                $booking_reqsn_qty = 0;
                                $knit_id = '';
                                $order_no = '';
                                $order_ref = '';
                                $order_file = '';
                                if ($row[csf('issue_basis')] == 1) 
                                {
                                    $booking_req_no = $row[csf('booking_no')];

                                    if ($row[csf('issue_purpose')] == 8) 
                                    {
                                        $buyer = $booking_without_array[$row[csf('booking_no')]]['buyer_id'];
                                        $booking_reqsn_qty = $booking_without_array[$row[csf('booking_no')]]['qnty'];
                                        $job_no = '';
                                        $styRef = '';
                                        $order_no = "";
                                        $order_ref = "";
                                        $order_file = "";
                                    } 
                                    else 
                                    {
                                        $job_no = $job_array[$booking_arr[$row[csf('booking_no')]]['job']]['job_no'];
                                        $buyer = $job_array[$booking_arr[$row[csf('booking_no')]]['job']]['buyer_name'];
                                        $styRef = $job_array[$booking_arr[$row[csf('booking_no')]]['job']]['style_ref_no'];
                                        $order_no = $job_array[$booking_arr[$row[csf('booking_no')]]['job']]['po_number'];
                                        $order_ref = $job_array[$booking_arr[$row[csf('booking_no')]]['job']]['ref'];
                                        $order_file = $job_array[$booking_arr[$row[csf('booking_no')]]['job']]['file'];
                                        $booking_reqsn_qty = $booking_arr[$row[csf('booking_no')]]['qnty'];
                                    }
                                } 
                                else if ($row[csf('issue_basis')] == 3) 
                                {
                                    $booking_req_no = $row[csf('requisition_no')]; 
                                    $reqi_wise_booking = $reqibooking_arr[$row[csf('requisition_no')]]['booking_no'];
                                    $knit_id = $requisition_arr[$row[csf('requisition_no')]]['knit_id'];
                                    $job_no = $job_array[$booking_arr[$planning_arr[$knit_id]]['job']]['job_no'];
                                    $buyer = $job_array[$booking_arr[$planning_arr[$knit_id]]['job']]['buyer_name'];
                                    $styRef = $job_array[$booking_arr[$planning_arr[$knit_id]]['job']]['style_ref_no'];
                                    $order_no = $job_array[$booking_arr[$planning_arr[$knit_id]]['job']]['po_number'];
                                    $order_ref = $job_array[$booking_arr[$planning_arr[$knit_id]]['job']]['ref'];
                                    $order_file = $job_array[$booking_arr[$planning_arr[$knit_id]]['job']]['file'];
                                    $booking_reqsn_qty = $requisition_arr[$row[csf('requisition_no')]]['qnty'];
                                } 
                                else 
                                {
                                    $booking_req_no = "";
                                    $buyer = $row[csf('buyer_id')];
                                    $job_no = '';
                                    $styRef = '';
                                    $order_no = '';
                                    $order_ref = '';
                                    $order_file = '';
                                    $booking_reqsn_qty = 0;
                                }

                                if ($row[csf('issue_purpose')] == 5) 
                                {
                                    $knitting_party = $other_party_arr[$row[csf('other_party')]];
                                } 
                                else if ($row[csf('issue_purpose')] == 3) 
                                {
                                    $knitting_party = $buyer_arr[$row[csf('buyer_id')]];
                                    $buyer = '';
                                }
                                ?> 

                                <td width="30"><? echo $i; ?></td>                           
                                <td width="100" align="center" class="wrd_brk" ><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
                                <td width="90" align="center" class="wrd_brk" ><p><? echo $job_no; ?></p></td>
                                <td width="100" class="wrd_brk" ><p><? echo $buyer_arr[$buyer]; ?></p></td>
                                <td width="80" class="wrd_brk" ><p><?
                                        echo $file_no = implode(",", $order_file);
                                        echo $file_no;
                                        ?></p></td>   
                                <td width="80" class="wrd_brk" ><p><?
                                        echo $ref_no = implode(",", $order_ref);
                                        echo $ref_no;
                                        ?></p></td>       
                                <td width="135" class="wrd_brk" ><p><? echo $styRef; ?></p></td>
                                <td width="150" class="wrd_brk" ><p><?
                                        $order_n = implode(",", $order_no);
                                        echo $order_n;
                                        ?></p></td>
                                <td width="100" class="wrd_brk" ><p><? echo $issue_basis[$row[csf('issue_basis')]]; ?></p></td>
                                <td width="110" class="wrd_brk" ><p><? echo $row[csf('issue_number')]; ?></p></td>
                                <td width="70" class="wrd_brk"  align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
                                <td width="80" class="wrd_brk" ><p><? echo $row[csf('challan_no')]; ?></p></td>
                                <td width="60" class="wrd_brk"  align="center"><p><? echo $count_arr[$row[csf('yarn_count_id')]]; ?></p></td>
                                <td width="70" class="wrd_brk" ><p><? echo $brand_arr[$row[csf('brand')]]; ?></p></td>
                                <td width="100" class="wrd_brk" ><p><? echo $composition[$row[csf('yarn_comp_type1st')]].$row[csf('yarn_comp_percent1st')].'%'; ?></p></td>
                                <td width="80" class="wrd_brk" ><p><? echo $yarn_type[$row[csf('yarn_type')]]; ?></p></td>
                                <td width="90" class="wrd_brk" ><p><? echo $color_arr[$row[csf('color')]]; ?></p></td>
                                <td width="70" class="wrd_brk" ><p><? echo $row[csf('lot')]; ?></p></td>
                                <td width="90" class="wrd_brk" align="right">
                                    <?
                                    echo number_format($row[csf('issue_qnty')], 2);
                                    $total_iss_qnty += $row[csf('issue_qnty')];
                                    $issue_qnty += $row[csf('issue_qnty')];
                                    ?>
                                </td>
                                <?
                                if ($zero_val == 0) {
                                    ?>
                                    <td width="90" align="right" class="wrd_brk" >
                                        <?
                                        $avgg = $row[csf('avg_rate_per_unit')];
                                        $avg_rate = $avgg / 78;
                                        echo number_format($avg_rate, 4);
                                        ?>
                                    </td> 
                                    <td width="110" align="right" class="wrd_brk" >
                                        <?
                                        $amount = $row[csf('issue_qnty')] * $avg_rate;
                                        echo number_format($amount, 2);
                                        $total_amount += $amount;
                                        $grand_total_amount += $amount;
                                        
                                        ?>
                                    </td> 
                                    <?
                                }
                                ?>
                                <td width="90" align="right" class="wrd_brk">
                                    <?
                                    echo number_format($row[csf('return_qnty')], 2);
                                    $total_ret_qnty += $row[csf('return_qnty')];
                                    $return_qnty += $row[csf('return_qnty')];
                                    ?>
                                </td>

                                <td width="100" class="wrd_brk" align="right">
                                    <p title="<? echo 'issue_number : '.$row[csf("issue_number")].'**product_id :'.$row[csf("product_id")]?>">
                                        <? 
                                        echo number_format($yt_issue_return_qnty_arr[$row[csf("issue_number")]][$row[csf("product_id")]],2); 
                                        $total_issue_return += $yt_issue_return_qnty_arr[$row[csf("issue_number")]][$row[csf("product_id")]];
                                        $grand_issue_return += $yt_issue_return_qnty_arr[$row[csf("issue_number")]][$row[csf("product_id")]];
                                       
                                        ?>
                                    </p>
                                </td>

                                <td width="100" class="wrd_brk" align="right">
                                    <p>
                                        <?
                                          echo number_format($row[csf('issue_qnty')]-$yt_issue_return_qnty_arr[$row[csf("issue_number")]][$row[csf("product_id")]], 2);
                                           
                                            $total_issue_balance_qnty += $row[csf('issue_qnty')]-$yt_issue_return_qnty_arr[$row[csf("issue_number")]][$row[csf("product_id")]];
                                        ?>
                                    </p>
                                </td>
                                 <td width="60" align="right" class="wrd_brk" ><? echo number_format($row[csf('cons_rate')]/$exchangeRate, 2);?></td>
                                <td width="100" class="wrd_brk" align="right">
                                    <p>
                                        <?
                                          echo number_format(($row[csf('cons_rate')]/$exchangeRate)*($row[csf('issue_qnty')]-$yt_issue_return_qnty_arr[$row[csf("issue_number")]][$row[csf("product_id")]]), 2);
                                           
                                            $total_issue_amount_qnty += ($row[csf('cons_rate')]/$exchangeRate)*($row[csf('issue_qnty')]-$yt_issue_return_qnty_arr[$row[csf("issue_number")]][$row[csf("product_id")]]);
                                        ?>
                                    </p>
                                </td>
                                
                                <td width="100" class="wrd_brk" ><p><? echo $yarn_issue_purpose[$row[csf('issue_purpose')]]; ?></p></td>
                                <td width="100" class="wrd_brk"><p><? echo $using_item_arr[$row[csf("using_item")]]; ?></p></td> 

                                <td width="110" class="wrd_brk"  align="center"><? echo $booking_req_no; ?></td>
                                <td width="100"  class="wrd_brk" align="right"><? echo $reqi_wise_booking; unset($reqi_wise_booking); ?></td>
                                <td width="100"  class="wrd_brk" align="right"><? echo number_format($booking_reqsn_qty, 2); ?></td>
                                <td width="130" class="wrd_brk" ><p><? echo $knitting_party; ?></p></td>
                                <td width="130" class="wrd_brk" ><p><? echo $knitting_location; ?></p></td>
                                <td class="wrd_brk" ><p><? echo $store_arr[$row[csf('store_id')]]; ?></td>
                            </tr>   

                            <?
                            $k++;
                            $i++;
                            $grnd_purpose_issue_qnty+=$row[csf('issue_qnty')];
                            $grnd_purpose_issue_amount+=$row[csf('cons_amount')]/$exchangeRate;
                            $grnd_purpose_total_amount+=$amount;
                            $grnd_purpose_return_qnty+=$row[csf('return_qnty')];
                            $grnd_purpose_issue_return_qnty+=$yt_issue_return_qnty_arr[$row[csf("issue_number")]][$row[csf("product_id")]];
                            $grnd_purpose_issue_balance_qnty+=$row[csf('issue_qnty')]-$yt_issue_return_qnty_arr[$row[csf("issue_number")]][$row[csf("product_id")]];
                            $grnd_purpose_issue_amount_qnty+=($row[csf('cons_rate')]/$exchangeRate)*($row[csf('issue_qnty')]-$yt_issue_return_qnty_arr[$row[csf("issue_number")]][$row[csf("product_id")]]);
                        }

                        if (count($nameArray) > 0) 
                        {
                            ?>
                            <tr class="tbl_bottom">
                                <td colspan="18" align="right"><b>Total</b></td>
                                <td align="right" class="wrd_brk" ><b><?php echo number_format($issue_qnty, 2); ?></b></td>
                            
                                <?
                                if ($zero_val == 0) 
                                {
                                    ?>
                                    <td align="right" class="wrd_brk" ></td><td align="right"><b><?php echo number_format($total_amount, 2); ?></b></td>
                                    <?
                                }
                                ?>
                                <td align="right" class="wrd_brk" ><b><?php echo number_format($return_qnty, 2); $return_qnty=0;?></b></td>
                                <td align="right" class="wrd_brk" ><b><?php echo number_format($total_issue_return, 2); $total_issue_return=0;?></b></td>

                                <td align="right" class="wrd_brk" ><b><?php echo number_format($total_issue_balance_qnty, 2); $total_issue_balance_qnty=0;?></b></td>
                                <td>&nbsp;</td>
                                <td align="right" class="wrd_brk" ><b><?php echo number_format($total_issue_amount_qnty, 2); $total_issue_amount_qnty=0;?></b></td>
                                <td colspan="8"></td>
                            </tr>   
                            <?
                        }
                        ?>
                         <tr>
                            <th colspan="18" align="right">Issue Purpose Wise Grand Total</th>
                            <th align="right" class="wrd_brk" ><?php echo number_format($grnd_purpose_issue_qnty, 2); ?></th>
                          
                            <?
                            if ($zero_val == 0) 
                            {
                                ?>
                                <th></th>
                                <th align="right" class="wrd_brk" ><?php echo number_format($grnd_purpose_total_amount, 2); ?></th>
                                <?
                            }
                            ?>
                            <th align="right" class="wrd_brk" ><?php echo number_format($grnd_purpose_return_qnty, 2); ?></th>
                            <th align="right" class="wrd_brk" ><?php echo number_format($grnd_purpose_issue_return_qnty, 2); ?></th>
                            <th align="right" class="wrd_brk" ><?php echo number_format($grnd_purpose_issue_balance_qnty, 2); ?></th>
                            <th></th>
                             <th align="right" class="wrd_brk" ><?php echo number_format($grnd_purpose_issue_amount_qnty, 2); ?></th>
                             
                            <th colspan="8" align="right"></th>
                         </tr>
                    </tbody> 
                   
                    <tfoot style="background-color: grey;">
                         <tr>
                            <th colspan="18" align="right"> Grand Total</th>
                            <th align="right" class="wrd_brk" ><?php echo number_format($grnd_purpose_issue_qnty+$issue_qnty_grand, 2); ?></th>
                          
                            <?
                            if ($zero_val == 0) 
                            {
                                ?>
                                <th></th>
                                <th align="right" class="wrd_brk" ><?php echo number_format($grnd_purpose_total_amount+$total_amount_grand, 2); ?></th>
                                <?
                            }
                            ?>
                            <th align="right" class="wrd_brk" ><?php echo number_format($grnd_purpose_return_qnty+$return_qnty_grand, 2); ?></th>
                            <th align="right" class="wrd_brk" ><?php echo number_format($grnd_purpose_issue_return_qnty+$issue_amount_return_grand, 2); ?></th>
                            <th align="right" class="wrd_brk" ><?php echo number_format($grnd_purpose_issue_balance_qnty+$issue_balance_qnty_grand, 2); ?></th>
                            <th></th>
                             <th align="right" class="wrd_brk" ><?php echo number_format($grnd_purpose_issue_amount_qnty+$issue_amount_qnty_grand, 2); ?></th>
                             
                            <th colspan="8" align="right"></th>
                         </tr>
                    </tfoot>
                    
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
