<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');


if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];
//---------------------------------------------------- Start---------------------------------------------------------------------------

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_id", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",0);
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
	<input type="hidden" id="txt_po_id" />
    <input type="hidden" id="txt_po_val" />
    <?
    $sql_cond = "";
	if ($data[0] !=0 )
        $sql_cond .= " and company_name=$data[0]";

	if ($data[1] !=0)
        $sql_cond .=" and buyer_name=$data[1]";

    if ($data[2] != "")
        $sql_cond .=" and id in ($data[2])";

	$sql = "select id, style_ref_no, job_no_prefix_num as job_prefix, to_char(insert_date,'YYYY') as year from wo_po_details_master where status_active = 1 and is_deleted = 0 $sql_cond order by id desc";
    echo create_list_view("list_view", "Style Ref. No.,Job No,Year","200,100,100","450","310",0, $sql , "js_set_value", "id,style_ref_no", "", 1, "0", $arr, "style_ref_no,job_prefix,year", "","setFilterGrid('list_view',-1)","0","",1) ;
	exit();	 
}

if ($action=="order_no_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	//print_r($data);die;
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
	$sql_cond="";
	if ($data[0]>0) $sql_cond .=" and b.company_name = $data[0]";
	if ($data[1]>0) $sql_cond .=" and b.buyer_name = $data[1]";
	if ($data[2]!="") $sql_cond .=" and b.id in ($data[2])";
	if ($data[3] != "") $sql_cond .=" and b.id in ($data[3])";


	$sql ="select distinct a.id,a.po_number,b.job_no_prefix_num as job_prefix, to_char(b.insert_date,'YYYY') as year from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.status_active in(1,3) $sql_cond order by a.id desc";
    echo create_list_view("list_view", "Order Number,Job No, Year","250,80","460","300",0, $sql , "js_set_value", "id,po_number", "", 1, "0", $arr, "po_number,job_prefix,year", "","setFilterGrid('list_view',-1)","0","",1) ;
	exit();
}

if ($action=="job_no_popup")
{
    echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
    extract($_REQUEST);
    $data=explode('_',$data);
    //print_r($data);die;
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
    $sql_cond="";
    if ($data[0]>0) $sql_cond .=" and company_name = $data[0]";
    if ($data[1]>0) $sql_cond .=" and buyer_name = $data[1]";

    $sql ="select id, job_no, job_no_prefix_num as job_prefix, to_char(insert_date,'YYYY') as year from wo_po_details_master where status_active = 1 and is_deleted = 0 $sql_cond order by id desc";
    echo create_list_view("list_view", "Job No.,Job SL., Year","250,80","460","300",0, $sql , "js_set_value", "id,job_no", "", 1, "0", $arr, "job_no,job_prefix,year", "","setFilterGrid('list_view',-1)","0","",1) ;
    exit();
}

if ($action=="receive_popup")
{
    echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
    extract($_REQUEST);
    $data=explode('*',$data);
    if(count($data) < 5){
        echo "Receive Data Not Found!";
        die();
    }
    $sql_cond="";
    $sql_cond .=" and d.company_name=$data[0]";
    $sql_cond .=" and d.style_ref_no ='$data[1]'";
    $sql_cond .=" and c.id = $data[2]";
    if(count($data) == 6){
        $sql_cond .=" and e.gmts_color_id = $data[4]";
        $sql_cond .=" and a.item_size = '$data[5]'";
    }else{
        $sql_cond .=" and a.item_size = '$data[4]'";
    }
    $sql_cond .=" and a.item_group_id = $data[3]";
	

    $sql_total_rcv = sql_select("select a.id as product_id, f.id, f.recv_number, f.booking_no, f.challan_no, f.receive_date,
       sum(case when b.trans_type in(1) then b.reject_qty else 0 end) as reject_qty,
       sum(case when b.entry_form in(24) and b.trans_type in(1) then b.quantity else 0 end) as rcv_qty,
       sum(case when b.entry_form in(73) and b.trans_type in(4) then b.quantity else 0 end) as issue_return_qty,
       sum(case when b.entry_form in(78,112)  and b.trans_type in(5) then b.quantity else 0 end) as item_transfer_receive	
       from product_details_master a, inv_transaction t, order_wise_pro_details b, wo_po_break_down c, wo_po_details_master d, inv_trims_entry_dtls e, inv_receive_master f
       where a.id=b.prod_id and a.id=t.prod_id and t.id=b.trans_id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and a.id=b.prod_id and t.id = e.trans_id and t.mst_id = f.id and a.item_category_id=4 and b.entry_form in(24,73,78,112) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $sql_cond
       group by a.id, f.id, f.recv_number, f.booking_no, f.challan_no, f.receive_date order by f.id desc");

    $total_recv = array();
    foreach ($sql_total_rcv as $k => $v){
        $rcv_qnty=$v[csf('rcv_qty')]+$v[csf('issue_return_qty')]+$v[csf('item_transfer_receive')];
        $total_recv[$v[csf('product_id')]."*".$v[csf('recv_number')]."*".$v[csf('booking_no')]."*".$v[csf('challan_no')]."*".$v[csf('receive_date')]]['rcv_qty'] += $rcv_qnty;
        $total_recv[$v[csf('product_id')]."*".$v[csf('recv_number')]."*".$v[csf('booking_no')]."*".$v[csf('challan_no')]."*".$v[csf('receive_date')]]['reject_qty'] += $v[csf('reject_qty')];
        $total_recv[$v[csf('product_id')]."*".$v[csf('recv_number')]."*".$v[csf('booking_no')]."*".$v[csf('challan_no')]."*".$v[csf('receive_date')]]['product_id'] = $v[csf('product_id')];
        $total_recv[$v[csf('product_id')]."*".$v[csf('recv_number')]."*".$v[csf('booking_no')]."*".$v[csf('challan_no')]."*".$v[csf('receive_date')]]['receive_number'] = $v[csf('recv_number')];
        $total_recv[$v[csf('product_id')]."*".$v[csf('recv_number')]."*".$v[csf('booking_no')]."*".$v[csf('challan_no')]."*".$v[csf('receive_date')]]['booking'] = $v[csf('booking_no')];
        $total_recv[$v[csf('product_id')]."*".$v[csf('recv_number')]."*".$v[csf('booking_no')]."*".$v[csf('challan_no')]."*".$v[csf('receive_date')]]['challan'] = $v[csf('challan_no')];
        $total_recv[$v[csf('product_id')]."*".$v[csf('recv_number')]."*".$v[csf('booking_no')]."*".$v[csf('challan_no')]."*".$v[csf('receive_date')]]['date'] = $v[csf('receive_date')];
    }
    ?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="755" class="rpt_table" align="left" >
        <thead>
            <tr>
                <th width="25">SL No.</th>
                <th width="100">Prod ID</th>
                <th width="110">Recv. No.</th>
                <th width="110">WO/PI No.</th>
                <th width="100">Challan No.</th>
                <th width="100">Receive Date</th>
                <th width="110">Receive Qty.</th>
                <th width="100">Reject Qty.</th>
            </tr>
        </thead>
    </table>
    <div style="width:775px; overflow-y: scroll; max-height:290px;" id="scroll_body">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="755" class="rpt_table" align="left" >
            <tbody>
            <?
            $total_qty = 0; $total_reject_qty = 0;
            $i = 1;
            foreach ($total_recv as $key => $val){
                if ($i%2==0)
                    $bgcolor="#E9F3FF";
                else
                    $bgcolor="#FFFFFF";
            ?>
                <tr bgcolor="<?=$bgcolor?>">
                    <td width="25" align="center"><?=$i++?></td>
                    <td width="100" align="center"><?=$val['product_id']?></td>
                    <td width="110" align="center"><?=$val['receive_number']?></td>
                    <td width="110" align="center"><?=$val['booking']?></td>
                    <td width="100" align="center"><?=$val['challan']?></td>
                    <td width="100" align="center"><?=$val['date']?></td>
                    <td width="110" align="right"><?=number_format($val['rcv_qty'], 2)?></td>
                    <td width="100" align="right"><?=number_format($val['reject_qty'], 2)?></td>
                </tr>
            <?
                $total_qty += $val['rcv_qty']; $total_reject_qty += $val['reject_qty'];
            }
            ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="6" align="right">Total</th>
                    <th align="right"><?=number_format($total_qty, 2)?></th>
                    <th align="right"><?=number_format($total_reject_qty, 2)?></th>
                </tr>
            </tfoot>
        </table>
    </div>
    <?
    exit();
}

if ($action=="report_generate")// Item Group Wise Search.
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$cbo_company        =str_replace("'","",$cbo_company_id);
	$cbo_buyer          =str_replace("'","",$cbo_buyer_id);
    $txt_job_no      =str_replace("'","",$txt_job_no_id);
	$txt_job_no         =str_replace("'","",$txt_job_no);
	$txt_style_id       =str_replace("'","",$txt_style_id);
	$txt_style          =str_replace("'","",$txt_style);
	$txt_order_no       =str_replace("'","",$txt_order_no);
	$txt_order_id       =str_replace("'","",$txt_order_no_id);
	$date_from          =str_replace("'","",$txt_date_from);
	$date_to            =str_replace("'","",$txt_date_to);
	$cbo_store_name     =str_replace("'","",$cbo_store_name);
	$cbo_item_group     =str_replace("'","",$cbo_item_group);
	
	$color_library = return_library_array( "select id,color_name from lib_color where status_active = 1 and is_deleted = 0", "id", "color_name");
	$group_library = return_library_array( "select id,item_name from lib_item_group where status_active = 1 and is_deleted = 0", "id", "item_name");
	$line_library = return_library_array( "select id, line_name from lib_sewing_line where status_active = 1 and is_deleted = 0", "id", "line_name");
	
	$con = connect();
    execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form=24)");
    oci_commit($con);

    $sql_cond="";
    if($cbo_company>0) $sql_cond .=" and d.company_name=$cbo_company";
    if($cbo_buyer>0) $sql_cond .=" and d.buyer_name=$cbo_buyer";
    if($txt_job_no !="" ) $sql_cond .=" and d.id in($txt_job_no_id)";
    if($txt_style !="" ) $sql_cond .=" and d.id in($txt_style_id)";
    if($txt_order_id !="" ) $sql_cond .=" and c.id in($txt_order_id)";
    if($cbo_item_group !="" ) $sql_cond .=" and a.item_group_id in($cbo_item_group)";
    if($date_from!="" && $date_to!="")  $sql_cond .=" and t.transaction_date  between '$date_from' and '$date_to' ";

    $sql_trim_issue = "select a.item_group_id, c.id, c.po_number, d.style_ref_no, a.item_size, e.gmts_color_id as item_color, b.trans_type, e.sewing_line, case when b.trans_type = 2 then t.mst_id when b.trans_type = 4 then t.issue_id else 0 end as issue_id, sum(case when b.entry_form in(25) and b.trans_type in(2)  then b.quantity else 0 end) as issue_qty, sum(case when b.entry_form in(73) and b.trans_type in(4)  then b.quantity else 0 end) as issue_return_qty
    from inv_transaction t, product_details_master a, order_wise_pro_details b, wo_po_break_down c, wo_po_details_master d, inv_trims_issue_dtls e
    where a.id=b.prod_id and a.id=t.prod_id and t.id=b.trans_id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and t.id = e.trans_id and a.item_category_id=4 and b.entry_form in (25,73) and b.trans_type in (2, 4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_cond
    group by t.mst_id, t.issue_id ,a.item_group_id, c.id, c.po_number, d.style_ref_no, a.item_size, e.gmts_color_id, e.sewing_line, b.trans_type, t.transaction_date order by t.transaction_date desc";

    $sql_trim_issue_arr = sql_select($sql_trim_issue);

    if(count($sql_trim_issue_arr) == 0){
        echo "<h3 style='text-align: center; margin-top: 10px;'>Issue not found!</h3>";
        die();
    }
    $data_issue_main_arr = array(); $po_id_arr = array(); $issue_id_arr = array();
    foreach ($sql_trim_issue_arr as $k => $v){
		
		$po_id_arr[$v[csf('id')]]=$v[csf('id')];
		$issue_id_arr[$v[csf('issue_id')]]=$v[csf('issue_id')];
		
        $data_issue_main_arr[$v[csf('style_ref_no')]."*".$v[csf('id')]]['style'] = $v[csf('style_ref_no')];
        $data_issue_main_arr[$v[csf('style_ref_no')]."*".$v[csf('id')]]['po_number'] = $v[csf('po_number')];
        $data_issue_main_arr[$v[csf('style_ref_no')]."*".$v[csf('id')]]['data'][$v[csf('item_group_id')]."*".$v[csf('item_color')]]['color'] = $color_library[$v[csf('item_color')]];
        $data_issue_main_arr[$v[csf('style_ref_no')]."*".$v[csf('id')]]['data'][$v[csf('item_group_id')]."*".$v[csf('item_color')]]['group'] = $group_library[$v[csf('item_group_id')]];
        $data_issue_main_arr[$v[csf('style_ref_no')]."*".$v[csf('id')]]['data'][$v[csf('item_group_id')]."*".$v[csf('item_color')]]['data'][$v[csf('issue_id')]]['line'][$v[csf('sewing_line')]] = $v[csf('sewing_line')];
        $data_issue_main_arr[$v[csf('style_ref_no')]."*".$v[csf('id')]]['data'][$v[csf('item_group_id')]."*".$v[csf('item_color')]]['data'][$v[csf('issue_id')]]['size'][$v[csf('item_size')]] += $v[csf('issue_qty')];
        $data_issue_main_arr[$v[csf('style_ref_no')]."*".$v[csf('id')]]['data'][$v[csf('item_group_id')]."*".$v[csf('item_color')]]['data'][$v[csf('issue_id')]]['size'][$v[csf('item_size')]] -= $v[csf('issue_return_qty')];
    }

    /*$po_id_con = '';
    $po_id_con1 = '';
    $po_id_con2 = '';
    $issue_id_con = '';
    $po_id = array_chunk(array_unique($po_id), 999);

    foreach ($po_id as $k => $v){
        if($k == 0){
            $po_id_cond = " and a.po_break_down_id in (".implode(',', $v).")";
            $po_id_cond1 = " and c.id in (".implode(',', $v).")";
            $po_id_cond2 = " and c.id in (".implode(',', $v).")";
        }else{
            $po_id_cond = " or a.po_break_down_id in (".implode(',', $v).")";
            $po_id_cond1 = " or c.id in (".implode(',', $v).")";
            $po_id_cond2 = " or c.id in (".implode(',', $v).")";
        }
    }*/
	
	if(count($po_id_arr)>0)
	{
		$rid=fnc_tempengine("gbl_temp_engine", $user_id, 24, 1, $po_id_arr, $empty_arr);
		if($rid) oci_commit($con);
	}
	
	if(count($issue_id_arr)>0)
	{
		$rid=fnc_tempengine("gbl_temp_engine", $user_id, 24, 2, $issue_id_arr, $empty_arr);
		if($rid) oci_commit($con);
	}
	

    if($date_from!="")
        $po_id_cond1 .=" and t.transaction_date  < to_date('$date_from', 'DD-MM-YYYY')'";


    $sql_get_order_size = sql_select("select d.style_ref_no, a.po_break_down_id, b.size_name, a.size_order 
	from wo_po_color_size_breakdown a, lib_size b, wo_po_break_down c, wo_po_details_master d, gbl_temp_engine g  
	where a.size_number_id = b.id and a.po_break_down_id = c.id and c.job_no_mst=d.job_no and c.id=g.ref_val and g.user_id=$user_id and g.entry_form=24 and g.ref_from=1 and a.status_active = 1 
	group by d.style_ref_no, a.po_break_down_id, b.size_name, a.size_order order by a.size_order");
    $size_arr = array();
    foreach ($sql_get_order_size as $k => $v){
        $size_arr[$v[csf('style_ref_no')]."*".$v[csf('po_break_down_id')]][$v[csf('size_order')]] = $v[csf('size_name')];
    }

    $sql_total_rcv = sql_select("select a.item_group_id, d.style_ref_no, c.id as po_id, c.po_number, a.item_size, e.gmts_color_id as item_color,
	sum(case when b.entry_form in(24) and b.trans_type in(1) then b.quantity else 0 end) as rcv_qty,
	sum(case when b.entry_form in(73) and b.trans_type in(4) then b.quantity else 0 end) as issue_return_qty,
	sum(case when b.entry_form in(78,112)  and b.trans_type in(5) then b.quantity else 0 end) as item_transfer_receive	
	from product_details_master a, inv_transaction t, order_wise_pro_details b, wo_po_break_down c, wo_po_details_master d, inv_trims_entry_dtls e, gbl_temp_engine g 
	where a.id=b.prod_id and a.id=t.prod_id and t.id=b.trans_id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and a.id=b.prod_id and t.id = e.trans_id and c.id=g.ref_val and g.user_id=$user_id and g.entry_form=24 and g.ref_from=1 and a.item_category_id=4 and b.entry_form in(24,73,78,112) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
	group by a.item_group_id, d.style_ref_no, c.id, c.po_number, a.item_size, e.gmts_color_id order by c.id, a.item_group_id");
    $total_recv = array();
    foreach ($sql_total_rcv as $k => $v){
        $rcv_qnty=$v[csf('rcv_qty')]+$v[csf('issue_return_qty')]+$v[csf('item_transfer_receive')];
        $total_recv[$v[csf('style_ref_no')]."*".$v[csf('po_id')]][$v[csf('item_group_id')]."*".$v[csf('item_color')]][$v[csf('item_size')]] += $rcv_qnty;
    }

    $sql_trim_balance_stock_rcv = sql_select("select a.item_group_id, d.style_ref_no, c.id as po_id, c.po_number, a.item_size, e.gmts_color_id as item_color,
	sum(case when b.entry_form in(24) and b.trans_type in(1)  then b.quantity else 0 end) as rcv_qty,
	sum(case when b.entry_form in(73) and b.trans_type in(4)  then b.quantity else 0 end) as issue_return_qty,
	sum(case when b.entry_form in(78,112)  and b.trans_type in(5) then b.quantity else 0 end) as item_transfer_receive	
	from product_details_master a, inv_transaction t, order_wise_pro_details b, wo_po_break_down c, wo_po_details_master d, inv_trims_entry_dtls e, gbl_temp_engine g
	where a.id=b.prod_id and a.id=t.prod_id and t.id=b.trans_id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and a.id=b.prod_id and t.id = e.trans_id and c.id=g.ref_val and g.user_id=$user_id and g.entry_form=24 and g.ref_from=1 and a.item_category_id=4 and b.entry_form in(24,73,78,112) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
	group by a.item_group_id, d.style_ref_no, c.id, c.po_number, a.item_size, e.gmts_color_id order by c.id, a.item_group_id");

    $stock_balance = array();
    foreach ($sql_trim_balance_stock_rcv as $k => $v){
        $rcv_qnty=$v[csf('rcv_qty')]+$v[csf('issue_return_qty')]+$v[csf('item_transfer_receive')];
        $stock_balance[$v[csf('style_ref_no')]."*".$v[csf('po_id')]][$v[csf('item_group_id')]."*".$v[csf('item_color')]][$v[csf('item_size')]] += $rcv_qnty;
    }
    $sql_trim_balance_stock_iss = sql_select("select a.item_group_id, d.style_ref_no, c.id as po_id, c.po_number, a.item_size, e.gmts_color_id as item_color,
	sum(case when b.entry_form in(25) and b.trans_type in(2)  then b.quantity else 0 end) as issue_qty,
	sum(case when b.entry_form in(49)  and b.trans_type in(3) then b.quantity else 0 end) as recv_return_qty,
	sum(case when b.entry_form in(78,112) and b.trans_type in(6) then b.quantity else 0 end) as item_transfer_issue,
	from product_details_master a, inv_transaction t, order_wise_pro_details b, wo_po_break_down c, wo_po_details_master d, inv_trims_issue_dtls e, gbl_temp_engine g
	where a.id=b.prod_id and a.id=t.prod_id and t.id=b.trans_id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and a.id=b.prod_id and c.id=g.ref_val and g.user_id=$user_id and g.entry_form=24 and g.ref_from=1 and a.item_category_id=4 and b.entry_form in(25,49,78,112) and t.id = e.trans_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
	group by a.item_group_id, d.style_ref_no, c.id, c.po_number, a.item_size, e.gmts_color_id order by c.id, a.item_group_id");

    foreach ($sql_trim_balance_stock_iss as $k => $v){
        $issue_qnty=$v[csf('issue_qty')]+$v[csf('recv_return_qty')]+$v[csf('item_transfer_issue')];
        $stock_balance[$v[csf('style_ref_no')]."*".$v[csf('po_id')]][$v[csf('item_group_id')]."*".$v[csf('item_color')]][$v[csf('item_size')]] -= $issue_qnty;
    }

    $issue_id = array_chunk(array_unique($issue_id), 999);
    foreach ($issue_id as $k => $v){
        if($k == 0){
            $issue_id_con = " and a.id in (".implode(',', $v).")";
        }else{
            $issue_id_con = " or a.id in (".implode(',', $v).")";
        }
    }
    
    $sql_select_issue_id = sql_select("select a.id, a.issue_number, a.challan_no, to_char(b.transaction_date, 'dd-mm-YYYY') as tdate 
	from inv_issue_master a, inv_transaction b, gbl_temp_engine g 
	where a.id = b.mst_id and a.id=g.ref_val and g.user_id=$user_id and g.entry_form=24 and g.ref_from=2 and a.status_active = 1 and a.is_deleted = 0 $issue_id_con 
	group by a.id, a.issue_number, b.transaction_date, a.challan_no");
    $issue_arr = array();
    foreach ($sql_select_issue_id as $k => $v){
        $issue_arr[$v[csf('id')]]['issue_number'] = $v[csf('issue_number')];
        $issue_arr[$v[csf('id')]]['date'] = $v[csf('tdate')];
        $issue_arr[$v[csf('id')]]['challan'] = $v[csf('challan_no')];
    }
	execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form=24");
    oci_commit($con); disconnect($con);
    ob_start();
	?>
    <div style="width:100%;">
        <?
        foreach ($data_issue_main_arr as $k => $v){
            $table_width_calc = 400 + count($size_arr[$k])*90 ;
        ?>
            <table cellspacing="0" cellpadding="0" border="0" rules="all"  width="<?=$table_width_calc?>" class="rpt_table" style="margin: 0 auto; margin-top: 15px; margin-bottom: 10px;">
                 <tr style="border: none">
                     <td width="<?=$table_width_calc?>" colspan="<?=count($size_arr[$k])+4?>" valign="middle" align="center" style="border: none;"><strong style="border: none; font-size: 18px;">Style : <?=$v['style']?></strong></td>
                 </tr>
                 <tr style="border: none">
                     <td width="<?=$table_width_calc?>" valign="middle" colspan="<?=count($size_arr[$k])+4?>" align="center" style="border: none;"><strong style="font-size: 16px;">Order No. : <?=$v['po_number']?></strong></td>
                 </tr>
             </table>
            <?
            foreach ($v['data'] as $group_color => $issue){
            ?>
                <table cellspacing="0" cellpadding="0" border="1" rules="all"  width="<?=$table_width_calc?>" class="rpt_table" style="margin: 0 auto;">
                    <tr>
                        <td width="<?=$table_width_calc?>" valign="middle" colspan="<?=count($size_arr[$k])+4?>" align="left" style="padding: 3px 2px;"><strong style="font-size:14px;">Item : <?=$issue['group']?></strong></td>
                    </tr>
                    <tr>
                        <td width="<?=$table_width_calc?>" valign="middle" align="left" colspan="<?=count($size_arr[$k])+4?>" style="padding: 3px 2px;"><strong style="font-size:14px;">Color : <?=$issue['color']?></strong></td>
                    </tr>
                </table>

                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?=$table_width_calc?>" class="rpt_table"  style="margin: 0 auto; margin-bottom: 20px;">
                    <thead>
                        <tr>
                            <th width="80" rowspan="2">DATE</th>
                            <th width="115" rowspan="2">SR-NO</th>
                            <th width="<?=count($size_arr[$k])*90?>" colspan="<?=count($size_arr[$k])?>">Size</th>
                            <th rowspan="2" width="100">Total</th>
                            <th rowspan="2">Section</th>
                        </tr>
                        <tr>
                            <?
                            foreach ($size_arr[$k] as $k2 => $v2){
                                ?>
                                <th width="90"><?=$v2?></th>
                                <?
                            }
                            ?>
                        </tr>
                    </thead>
                     <tbody>
                     <tr bgcolor="#ffede0">
                         <td align="center" style="padding: 3px 2px;font-size: 14px;" colspan="2"><strong>Total Receive Quantity</strong></td>
                         <?
                         $rowTotal3 = 0;
                         if(count($total_recv[$k][$group_color]) == 1 && isset($total_recv[$k][$group_color][0])){
                             ?>
                             <td width="<?=count($size_arr[$k])*90?>" align="right" colspan="<?=count($size_arr[$k])?>" style="padding: 3px 2px;font-size: 14px;"><strong><a href="javascript:void(0)" onclick="open_recv_popup('<?=$k.'*'.$group_color.'*'.$v2?>')"><?=number_format(isset($total_recv[$k][$group_color][0]) ? $total_recv[$k][$group_color][0] : 0, 2) ?></a></strong></td>
                             <?
                             $rowTotal1 = isset($total_recv[$k][$group_color][0]) ? $total_recv[$k][$group_color][0] : 0;
                         }else{
                             foreach ($size_arr[$k] as $k2 => $v2){
                                 ?>
                                 <td width="90" align="right" style="padding: 3px 2px;font-size: 14px;"><strong><a href="javascript:void(0)" onclick="open_recv_popup('<?=$cbo_company.'*'.$k.'*'.$group_color.'*'.$v2?>')"><?=number_format(isset($total_recv[$k][$group_color][$v2]) ? $total_recv[$k][$group_color][$v2] : 0, 2) ?></a></strong></td>
                                 <?
                                 $rowTotal3 += isset($total_recv[$k][$group_color][$v2]) ? $total_recv[$k][$group_color][$v2] : 0;
                             }
                         }
                         ?>
                         <td align="right" style="padding: 3px 2px;font-size: 14px;"><strong><?=number_format($rowTotal3, 2)?></strong></td>
                         <td></td>
                     </tr>
                     <tr bgcolor="#eaeaea">
                         <td align="center" style="padding: 3px 2px;font-size: 14px;" colspan="2"><strong>Opening Stock Balance</strong></td>
                         <?
                         $rowTotal1 = 0;
                         if(count($stock_balance[$k][$group_color]) == 1 && isset($stock_balance[$k][$group_color][0])){
                             ?>
                             <td width="<?=count($size_arr[$k])*90?>" align="right" colspan="<?=count($size_arr[$k])?>" style="padding: 3px 2px;font-size: 14px;"><strong><?=number_format(isset($stock_balance[$k][$group_color][0]) ? $stock_balance[$k][$group_color][0] : 0, 2) ?></strong></td>
                             <?
                             $rowTotal1 = isset($stock_balance[$k][$group_color][0]) ? $stock_balance[$k][$group_color][0] : 0;
                         }else{
                             foreach ($size_arr[$k] as $k2 => $v2){
                                 ?>
                                 <td width="90" align="right" style="padding: 3px 2px;font-size: 14px;"><strong><?=number_format(isset($stock_balance[$k][$group_color][$v2]) ? $stock_balance[$k][$group_color][$v2] : 0, 2) ?></strong></td>
                                 <?
                                 $rowTotal1 += isset($stock_balance[$k][$group_color][$v2]) ? $stock_balance[$k][$group_color][$v2] : 0;
                             }
                         }
                         ?>
                         <td align="right" style="padding: 3px 2px;font-size: 14px;"><strong><?=number_format($rowTotal1, 2)?></strong></td>
                         <td></td>
                     </tr>
                     <?
                     $i = 0;
                     $grand_total = 0; $grand_total_arr = array();
                     foreach ($issue['data'] as $issue_key => $iss_val){
                         $rowTotal = 0;
                         ?>
                         <tr>
                             <td align="center" style="padding: 3px 2px;font-size: 14px;"><?=$issue_arr[$issue_key]['date']?></td>
                             <td align="center" style="padding: 3px 2px;font-size: 14px;"><?=$issue_arr[$issue_key]['challan']?></td>
                             <?
                             if(count($iss_val['size']) == 1 && isset($iss_val['size'][0])){
                             ?>
                                 <td width="<?=count($size_arr[$k])*90?>" align="right" colspan="<?=count($size_arr[$k])?>" style="padding: 3px 2px;font-size: 14px;"><?=number_format(isset($iss_val['size'][0]) ? $iss_val['size'][0] : 0, 2) ?></td>
                             <?
                                 $rowTotal = isset($iss_val['size'][0]) ? $iss_val['size'][0] : 0;
                                 $grand_total_arr[0] = isset($grand_total_arr[0]) ? $grand_total_arr[0] + (isset($iss_val['size'][0]) ? $iss_val['size'][0] : 0) : (isset($iss_val['size'][0]) ? $iss_val['size'][0] : 0);
                             }else{
                                foreach ($size_arr[$k] as $k2 => $v2){
                             ?>
                                     <td width="90" align="right" style="padding: 3px 2px;font-size: 14px;"><?=number_format(isset($iss_val['size'][$v2]) ? $iss_val['size'][$v2] : 0, 2) ?></td>
                             <?
                                    $rowTotal += isset($iss_val['size'][$v2]) ? $iss_val['size'][$v2] : 0;
                                    $grand_total_arr[$v2] = isset($grand_total_arr[$v2]) ? $grand_total_arr[$v2] + (isset($iss_val['size'][$v2]) ? $iss_val['size'][$v2] : 0) : (isset($iss_val['size'][$v2]) ? $iss_val['size'][$v2] : 0);
                                }
                             }
                             $grand_total += $rowTotal;
                             ?>
                             <td align="right" style="padding: 3px 2px;font-size: 14px;"><?=number_format($rowTotal, 2)?></td>
                             <td align="center" style="padding: 3px 2px;font-size: 14px;">
                                 <?
                                 $line_unique_arr = array_unique($iss_val['line']);
                                 $line_appender = "";
                                 $line = 0;
                                 foreach ($line_unique_arr as $line_key => $line_val){
                                     if($line == 0){
                                         $line_appender .= $line_library[$line_val];
                                     }else{
                                         $line_appender .= ', '.$line_library[$line_val];
                                     }
                                     $line++;
                                 }
                                 echo $line_appender;
                                ?>
                             </td>
                         </tr>
                         <?
                     }
                     ?>
                         <tr bgcolor="#cfffef">
                             <td colspan="2" align="center"><strong>Issue Total</strong></td>
                             <?
                             if(count($grand_total_arr) == 1 && isset($grand_total_arr[0])){
                                 ?>
                                 <td width="<?=count($size_arr[$k])*90?>" align="right" colspan="<?=count($size_arr[$k])?>" style="padding: 3px 2px;font-size: 14px;"><strong><?=number_format($grand_total_arr[0], 2)?></strong></td>
                                 <?
                             }else{
                                 foreach ($size_arr[$k] as $k2 => $v2){
                                     ?>
                                     <td width="90" align="right"  style="padding: 3px 2px;font-size: 14px;"><strong><?=number_format($grand_total_arr[$v2], 2)?></strong></td>
                                     <?
                                 }
                             }
                             ?>
                             <td align="right" style="padding: 3px 2px;font-size: 14px;"><strong><?=number_format($grand_total, 2)?></strong></td>
                             <td></td>
                         </tr>
                         <tr bgcolor="#bde6ff">
                             <td align="center" style="padding: 3px 2px;font-size: 14px;" colspan="2"><strong>Current Stock Balance</strong></td>
                             <?
                             $rowTotal44 = 0;
                             if(count($stock_balance[$k][$group_color]) == 1 && isset($stock_balance[$k][$group_color][0])){
                                 ?>
                                 <td width="<?=count($size_arr[$k])*90?>" align="right" colspan="<?=count($size_arr[$k])?>" style="padding: 3px 2px;font-size: 14px;"><strong><?=number_format(isset($stock_balance[$k][$group_color][0]) ? ($stock_balance[$k][$group_color][0]-$grand_total_arr[0]) : (0-$grand_total_arr[0]), 2) ?></strong></td>
                                 <?
                                 $rowTotal1 = isset($stock_balance[$k][$group_color][0]) ? $stock_balance[$k][$group_color][0] : 0;
                             }else{
                                 foreach ($size_arr[$k] as $k2 => $v2){
                                     ?>
                                     <td width="90" align="right" style="padding: 3px 2px;font-size: 14px;"><strong><?=number_format(isset($stock_balance[$k][$group_color][$v2]) ? $stock_balance[$k][$group_color][$v2]-$grand_total_arr[$v2] : 0-$grand_total_arr[$v2], 2) ?></strong></td>
                                     <?
                                     $rowTotal44 += isset($stock_balance[$k][$group_color][$v2]) ? $stock_balance[$k][$group_color][$v2]-$grand_total_arr[$v2] : 0-$grand_total_arr[$v2];
                                 }
                             }
                             ?>
                             <td align="right" style="padding: 3px 2px;font-size: 14px;"><strong><?=number_format($rowTotal44, 2)?></strong></td>
                             <td></td>
                         </tr>
                     </tbody>
                </table>
        <?
            }
        }
        ?>

  </div>
<?
    $html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
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

if ($action=="report_generate_2")// Item Group Wise Search.
{
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process ));

    $cbo_company        =str_replace("'","",$cbo_company_id);
    $cbo_buyer          =str_replace("'","",$cbo_buyer_id);
    $txt_job_no      =str_replace("'","",$txt_job_no_id);
    $txt_job_no         =str_replace("'","",$txt_job_no);
    $txt_style_id       =str_replace("'","",$txt_style_id);
    $txt_style          =str_replace("'","",$txt_style);
    $txt_order_no       =str_replace("'","",$txt_order_no);
    $txt_order_id       =str_replace("'","",$txt_order_no_id);
    $date_from          =str_replace("'","",$txt_date_from);
    $date_to            =str_replace("'","",$txt_date_to);
    $cbo_store_name     =str_replace("'","",$cbo_store_name);
    $cbo_item_group     =str_replace("'","",$cbo_item_group);

    $group_library = return_library_array( "select id,item_name from lib_item_group where status_active = 1 and is_deleted = 0", "id", "item_name");
    $line_library = return_library_array( "select id, line_name from lib_sewing_line where status_active = 1 and is_deleted = 0", "id", "line_name");

    $sql_cond="";
    if($cbo_company>0) $sql_cond .=" and d.company_name=$cbo_company";
    if($cbo_buyer>0) $sql_cond .=" and d.buyer_name=$cbo_buyer";
    if($txt_job_no !="" ) $sql_cond .=" and d.id in($txt_job_no_id)";
    if($txt_style !="" ) $sql_cond .=" and d.id in($txt_style_id)";
    if($txt_order_id !="" ) $sql_cond .=" and c.id in($txt_order_id)";
    if($cbo_item_group !="" ) $sql_cond .=" and a.item_group_id in($cbo_item_group)";
    if($date_from!="" && $date_to!="")  $sql_cond .=" and t.transaction_date  between '$date_from' and '$date_to' ";

    $sql_trim_issue = "select a.item_group_id, c.id, c.po_number, d.style_ref_no, a.item_size, b.trans_type, e.sewing_line,
       case when b.trans_type = 2 then t.mst_id when b.trans_type = 4 then t.issue_id else 0 end as issue_id, 
       sum(case when b.entry_form in(25) and b.trans_type in(2)  then b.quantity else 0 end) as issue_qty,
       sum(case when b.entry_form in(73) and b.trans_type in(4)  then b.quantity else 0 end) as issue_return_qty
    from 
         inv_transaction t, product_details_master a, order_wise_pro_details b, wo_po_break_down c, wo_po_details_master d, inv_trims_issue_dtls e
    where 
          a.id=b.prod_id and a.id=t.prod_id and t.id=b.trans_id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and t.id = e.trans_id and a.item_category_id=4
          and b.entry_form in (25,73) and b.trans_type in (2, 4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_cond
    group by t.mst_id, t.issue_id ,a.item_group_id, c.id, c.po_number, d.style_ref_no, a.item_size, e.sewing_line, b.trans_type, t.transaction_date order by t.transaction_date desc";

    $sql_trim_issue_arr = sql_select($sql_trim_issue);

    if(count($sql_trim_issue_arr) == 0){
        echo "<h3 style='text-align: center; margin-top: 10px;'>Issue not found!</h3>";
        die();
    }
    $data_issue_main_arr = array(); $po_id = array(); $issue_id = array();
    foreach ($sql_trim_issue_arr as $k => $v){
        array_push($po_id, $v[csf('id')]);
        array_push($issue_id, $v[csf('issue_id')]);
        $data_issue_main_arr[$v[csf('style_ref_no')]."*".$v[csf('id')]]['style'] = $v[csf('style_ref_no')];
        $data_issue_main_arr[$v[csf('style_ref_no')]."*".$v[csf('id')]]['po_number'] = $v[csf('po_number')];
        $data_issue_main_arr[$v[csf('style_ref_no')]."*".$v[csf('id')]]['data'][$v[csf('item_group_id')]]['group'] = $group_library[$v[csf('item_group_id')]];
        $data_issue_main_arr[$v[csf('style_ref_no')]."*".$v[csf('id')]]['data'][$v[csf('item_group_id')]]['data'][$v[csf('issue_id')]]['line'][$v[csf('sewing_line')]] = $v[csf('sewing_line')];
        $data_issue_main_arr[$v[csf('style_ref_no')]."*".$v[csf('id')]]['data'][$v[csf('item_group_id')]]['data'][$v[csf('issue_id')]]['size'][$v[csf('item_size')]] += $v[csf('issue_qty')];
        $data_issue_main_arr[$v[csf('style_ref_no')]."*".$v[csf('id')]]['data'][$v[csf('item_group_id')]]['data'][$v[csf('issue_id')]]['size'][$v[csf('item_size')]] -= $v[csf('issue_return_qty')];
    }

    $po_id_con = '';
    $po_id_con1 = '';
    $po_id_con2 = '';
    $issue_id_con = '';
    $po_id = array_chunk(array_unique($po_id), 999);

    foreach ($po_id as $k => $v){
        if($k == 0){
            $po_id_cond = " and a.po_break_down_id in (".implode(',', $v).")";
            $po_id_cond1 = " and c.id in (".implode(',', $v).")";
            $po_id_cond2 = " and c.id in (".implode(',', $v).")";
        }else{
            $po_id_cond = " or a.po_break_down_id in (".implode(',', $v).")";
            $po_id_cond1 = " or c.id in (".implode(',', $v).")";
            $po_id_cond2 = " or c.id in (".implode(',', $v).")";
        }
    }

    if($date_from!="")
        $po_id_cond1 .=" and t.transaction_date  < to_date('$date_from', 'DD-MM-YYYY')'";


    $sql_get_order_size = sql_select("select d.style_ref_no, a.po_break_down_id, b.size_name, a.size_order from wo_po_color_size_breakdown a, lib_size b, wo_po_break_down c, wo_po_details_master d  where a.size_number_id = b.id and a.po_break_down_id = c.id and c.job_no_mst=d.job_no and a.status_active = 1 $po_id_cond group by d.style_ref_no, a.po_break_down_id, b.size_name, a.size_order order by a.size_order");
    $size_arr = array();
    foreach ($sql_get_order_size as $k => $v){
        $size_arr[$v[csf('style_ref_no')]."*".$v[csf('po_break_down_id')]][$v[csf('size_order')]] = $v[csf('size_name')];
    }

    $sql_total_rcv = sql_select("select a.item_group_id, d.style_ref_no, c.id as po_id, c.po_number, a.item_size,
       sum(case when b.entry_form in(24) and b.trans_type in(1) then b.quantity else 0 end) as rcv_qty,
       sum(case when b.entry_form in(73) and b.trans_type in(4) then b.quantity else 0 end) as issue_return_qty,
       sum(case when b.entry_form in(78,112)  and b.trans_type in(5) then b.quantity else 0 end) as item_transfer_receive	
       from product_details_master a, inv_transaction t, order_wise_pro_details b, wo_po_break_down c, wo_po_details_master d
       where a.id=b.prod_id and a.id=t.prod_id and t.id=b.trans_id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and a.id=b.prod_id and a.item_category_id=4 and b.entry_form in(24,73,78,112) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $po_id_con2
       group by a.item_group_id, d.style_ref_no, c.id, c.po_number, a.item_size order by c.id, a.item_group_id");
    $total_recv = array();
    foreach ($sql_total_rcv as $k => $v){
        $rcv_qnty=$v[csf('rcv_qty')]+$v[csf('issue_return_qty')]+$v[csf('item_transfer_receive')];
        $total_recv[$v[csf('style_ref_no')]."*".$v[csf('po_id')]][$v[csf('item_group_id')]][$v[csf('item_size')]] += $rcv_qnty;
    }

    $sql_trim_balance_stock_rcv = sql_select("select a.item_group_id, d.style_ref_no, c.id as po_id, c.po_number, a.item_size,
       sum(case when b.entry_form in(24) and b.trans_type in(1)  then b.quantity else 0 end) as rcv_qty,
       sum(case when b.entry_form in(73) and b.trans_type in(4)  then b.quantity else 0 end) as issue_return_qty,
       sum(case when b.entry_form in(78,112)  and b.trans_type in(5) then b.quantity else 0 end) as item_transfer_receive	
       from product_details_master a, inv_transaction t, order_wise_pro_details b, wo_po_break_down c, wo_po_details_master d
       where a.id=b.prod_id and a.id=t.prod_id and t.id=b.trans_id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and a.id=b.prod_id and a.item_category_id=4 and b.entry_form in(24,73,78,112) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $po_id_con1
       group by a.item_group_id, d.style_ref_no, c.id, c.po_number, a.item_size order by c.id, a.item_group_id");

    $stock_balance = array();
    foreach ($sql_trim_balance_stock_rcv as $k => $v){
        $rcv_qnty=$v[csf('rcv_qty')]+$v[csf('issue_return_qty')]+$v[csf('item_transfer_receive')];
        $stock_balance[$v[csf('style_ref_no')]."*".$v[csf('po_id')]][$v[csf('item_group_id')]][$v[csf('item_size')]] += $rcv_qnty;
    }
    $sql_trim_balance_stock_iss = sql_select("select a.item_group_id, d.style_ref_no, c.id as po_id, c.po_number, a.item_size,
       sum(case when b.entry_form in(25) and b.trans_type in(2)  then b.quantity else 0 end) as issue_qty,
       sum(case when b.entry_form in(49)  and b.trans_type in(3) then b.quantity else 0 end) as recv_return_qty,
       sum(case when b.entry_form in(78,112) and b.trans_type in(6)  then b.quantity else 0 end) as item_transfer_issue,
       from product_details_master a, inv_transaction t, order_wise_pro_details b, wo_po_break_down c, wo_po_details_master d
       where a.id=b.prod_id and a.id=t.prod_id and t.id=b.trans_id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and a.id=b.prod_id and a.item_category_id=4 and b.entry_form in(25,49,78,112) and t.id = e.trans_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $po_id_con1
       group by a.item_group_id, d.style_ref_no, c.id, c.po_number, a.item_size order by c.id, a.item_group_id");

    foreach ($sql_trim_balance_stock_iss as $k => $v){
        $issue_qnty=$v[csf('issue_qty')]+$v[csf('recv_return_qty')]+$v[csf('item_transfer_issue')];
        $stock_balance[$v[csf('style_ref_no')]."*".$v[csf('po_id')]][$v[csf('item_group_id')]][$v[csf('item_size')]] -= $issue_qnty;
    }

    $issue_id = array_chunk(array_unique($issue_id), 999);
    foreach ($issue_id as $k => $v){
        if($k == 0){
            $issue_id_con = " and a.id in (".implode(',', $v).")";
        }else{
            $issue_id_con = " or a.id in (".implode(',', $v).")";
        }
    }

    $sql_select_issue_id = sql_select("select a.id, a.issue_number, a.challan_no, to_char(b.transaction_date, 'dd-mm-YYYY') as tdate from inv_issue_master a, inv_transaction b where a.id = b.mst_id and a.status_active = 1 and a.is_deleted = 0 $issue_id_con group  by a.id, a.issue_number, b.transaction_date, a.challan_no");
    $issue_arr = array();
    foreach ($sql_select_issue_id as $k => $v){
        $issue_arr[$v[csf('id')]]['issue_number'] = $v[csf('issue_number')];
        $issue_arr[$v[csf('id')]]['date'] = $v[csf('tdate')];
        $issue_arr[$v[csf('id')]]['challan'] = $v[csf('challan_no')];
    }
    ob_start();
    ?>
    <div style="width:100%;">
        <?
        foreach ($data_issue_main_arr as $k => $v){
            $table_width_calc = 400 + count($size_arr[$k])*90 ;
            ?>
            <table cellspacing="0" cellpadding="0" border="0" rules="all"  width="<?=$table_width_calc?>" class="rpt_table" style="margin: 0 auto; margin-top: 15px; margin-bottom: 10px;">
                <tr style="border: none">
                    <td width="<?=$table_width_calc?>" colspan="<?=count($size_arr[$k])+4?>" valign="middle" align="center" style="border: none;"><strong style="border: none; font-size: 18px;">Style : <?=$v['style']?></strong></td>
                </tr>
            </table>
            <?
            foreach ($v['data'] as $group_color => $issue){
                ?>
                <table cellspacing="0" cellpadding="0" border="1" rules="all"  width="<?=$table_width_calc?>" class="rpt_table" style="margin: 0 auto;">
                    <tr>
                        <td width="<?=$table_width_calc?>" valign="middle" colspan="<?=count($size_arr[$k])+4?>" align="left" style="padding: 3px 2px;"><strong style="font-size:14px;">Item : <?=$issue['group']?></strong></td>
                    </tr>
                    <tr>
                        <td width="<?=$table_width_calc?>" valign="middle" align="left" colspan="<?=count($size_arr[$k])+4?>" style="padding: 3px 2px;"><strong style="font-size:14px;">Order No. : <?=$v['po_number']?></strong></td>
                    </tr>
                </table>

                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?=$table_width_calc?>" class="rpt_table"  style="margin: 0 auto; margin-bottom: 20px;">
                    <thead>
                    <tr>
                        <th width="80" rowspan="2">DATE</th>
                        <th width="115" rowspan="2">SR-NO</th>
                        <th width="<?=count($size_arr[$k])*90?>" colspan="<?=count($size_arr[$k])?>">Size</th>
                        <th rowspan="2" width="100">Total</th>
                        <th rowspan="2">Section</th>
                    </tr>
                    <tr>
                        <?
                        foreach ($size_arr[$k] as $k2 => $v2){
                            ?>
                            <th width="90"><?=$v2?></th>
                            <?
                        }
                        ?>
                    </tr>
                    </thead>
                    <tbody>
                    <tr bgcolor="#ffede0">
                        <td align="center" style="padding: 3px 2px;font-size: 14px;" colspan="2"><strong>Total Receive Quantity</strong></td>
                        <?
                        $rowTotal3 = 0;
                        if(count($total_recv[$k][$group_color]) == 1 && isset($total_recv[$k][$group_color][0])){
                            ?>
                            <td width="<?=count($size_arr[$k])*90?>" align="right" colspan="<?=count($size_arr[$k])?>" style="padding: 3px 2px;font-size: 14px;"><strong><a href="javascript:void(0)" onclick="open_recv_popup('<?=$k.'*'.$group_color.'*'.$v2?>')"><?=number_format(isset($total_recv[$k][$group_color][0]) ? $total_recv[$k][$group_color][0] : 0, 2) ?></a></strong></td>
                            <?
                            $rowTotal1 = isset($total_recv[$k][$group_color][0]) ? $total_recv[$k][$group_color][0] : 0;
                        }else{
                            foreach ($size_arr[$k] as $k2 => $v2){
                                ?>
                                <td width="90" align="right" style="padding: 3px 2px;font-size: 14px;"><strong><a href="javascript:void(0)" onclick="open_recv_popup('<?=$cbo_company.'*'.$k.'*'.$group_color.'*'.$v2?>')"><?=number_format(isset($total_recv[$k][$group_color][$v2]) ? $total_recv[$k][$group_color][$v2] : 0, 2) ?></a></strong></td>
                                <?
                                $rowTotal3 += isset($total_recv[$k][$group_color][$v2]) ? $total_recv[$k][$group_color][$v2] : 0;
                            }
                        }
                        ?>
                        <td align="right" style="padding: 3px 2px;font-size: 14px;"><strong><?=number_format($rowTotal3, 2)?></strong></td>
                        <td></td>
                    </tr>
                    <tr bgcolor="#eaeaea">
                        <td align="center" style="padding: 3px 2px;font-size: 14px;" colspan="2"><strong>Opening Stock Balance</strong></td>
                        <?
                        $rowTotal1 = 0;
                        if(count($stock_balance[$k][$group_color]) == 1 && isset($stock_balance[$k][$group_color][0])){
                            ?>
                            <td width="<?=count($size_arr[$k])*90?>" align="right" colspan="<?=count($size_arr[$k])?>" style="padding: 3px 2px;font-size: 14px;"><strong><?=number_format(isset($stock_balance[$k][$group_color][0]) ? $stock_balance[$k][$group_color][0] : 0, 2) ?></strong></td>
                            <?
                            $rowTotal1 = isset($stock_balance[$k][$group_color][0]) ? $stock_balance[$k][$group_color][0] : 0;
                        }else{
                            foreach ($size_arr[$k] as $k2 => $v2){
                                ?>
                                <td width="90" align="right" style="padding: 3px 2px;font-size: 14px;"><strong><?=number_format(isset($stock_balance[$k][$group_color][$v2]) ? $stock_balance[$k][$group_color][$v2] : 0, 2) ?></strong></td>
                                <?
                                $rowTotal1 += isset($stock_balance[$k][$group_color][$v2]) ? $stock_balance[$k][$group_color][$v2] : 0;
                            }
                        }
                        ?>
                        <td align="right" style="padding: 3px 2px;font-size: 14px;"><strong><?=number_format($rowTotal1, 2)?></strong></td>
                        <td></td>
                    </tr>
                    <?
                    $i = 0;
                    $grand_total = 0; $grand_total_arr = array();
                    foreach ($issue['data'] as $issue_key => $iss_val){
                        $rowTotal = 0;
                        ?>
                        <tr>
                            <td align="center" style="padding: 3px 2px;font-size: 14px;"><?=$issue_arr[$issue_key]['date']?></td>
                            <td align="center" style="padding: 3px 2px;font-size: 14px;"><?=$issue_arr[$issue_key]['challan']?></td>
                            <?
                            if(count($iss_val['size']) == 1 && isset($iss_val['size'][0])){
                                ?>
                                <td width="<?=count($size_arr[$k])*90?>" align="right" colspan="<?=count($size_arr[$k])?>" style="padding: 3px 2px;font-size: 14px;"><?=number_format(isset($iss_val['size'][0]) ? $iss_val['size'][0] : 0, 2) ?></td>
                                <?
                                $rowTotal = isset($iss_val['size'][0]) ? $iss_val['size'][0] : 0;
                                $grand_total_arr[0] = isset($grand_total_arr[0]) ? $grand_total_arr[0] + (isset($iss_val['size'][0]) ? $iss_val['size'][0] : 0) : (isset($iss_val['size'][0]) ? $iss_val['size'][0] : 0);
                            }else{
                                foreach ($size_arr[$k] as $k2 => $v2){
                                    ?>
                                    <td width="90" align="right" style="padding: 3px 2px;font-size: 14px;"><?=number_format(isset($iss_val['size'][$v2]) ? $iss_val['size'][$v2] : 0, 2) ?></td>
                                    <?
                                    $rowTotal += isset($iss_val['size'][$v2]) ? $iss_val['size'][$v2] : 0;
                                    $grand_total_arr[$v2] = isset($grand_total_arr[$v2]) ? $grand_total_arr[$v2] + (isset($iss_val['size'][$v2]) ? $iss_val['size'][$v2] : 0) : (isset($iss_val['size'][$v2]) ? $iss_val['size'][$v2] : 0);
                                }
                            }
                            $grand_total += $rowTotal;
                            ?>
                            <td align="right" style="padding: 3px 2px;font-size: 14px;"><?=number_format($rowTotal, 2)?></td>
                            <td align="center" style="padding: 3px 2px;font-size: 14px;">
                                <?
                                $line_unique_arr = array_unique($iss_val['line']);
                                $line_appender = "";
                                $line = 0;
                                foreach ($line_unique_arr as $line_key => $line_val){
                                    if($line == 0){
                                        $line_appender .= $line_library[$line_val];
                                    }else{
                                        $line_appender .= ', '.$line_library[$line_val];
                                    }
                                    $line++;
                                }
                                echo $line_appender;
                                ?>
                            </td>
                        </tr>
                        <?
                    }
                    ?>
                    <tr bgcolor="#cfffef">
                        <td colspan="2" align="center"><strong>Issue Total</strong></td>
                        <?
                        if(count($grand_total_arr) == 1 && isset($grand_total_arr[0])){
                            ?>
                            <td width="<?=count($size_arr[$k])*90?>" align="right" colspan="<?=count($size_arr[$k])?>" style="padding: 3px 2px;font-size: 14px;"><strong><?=number_format($grand_total_arr[0], 2)?></strong></td>
                            <?
                        }else{
                            foreach ($size_arr[$k] as $k2 => $v2){
                                ?>
                                <td width="90" align="right"  style="padding: 3px 2px;font-size: 14px;"><strong><?=number_format($grand_total_arr[$v2], 2)?></strong></td>
                                <?
                            }
                        }
                        ?>
                        <td align="right" style="padding: 3px 2px;font-size: 14px;"><strong><?=number_format($grand_total, 2)?></strong></td>
                        <td></td>
                    </tr>
                    <tr bgcolor="#bde6ff">
                        <td align="center" style="padding: 3px 2px;font-size: 14px;" colspan="2"><strong>Current Stock Balance</strong></td>
                        <?
                        $rowTotal44 = 0;
                        if(count($stock_balance[$k][$group_color]) == 1 && isset($stock_balance[$k][$group_color][0])){
                            ?>
                            <td width="<?=count($size_arr[$k])*90?>" align="right" colspan="<?=count($size_arr[$k])?>" style="padding: 3px 2px;font-size: 14px;"><strong><?=number_format(isset($stock_balance[$k][$group_color][0]) ? ($stock_balance[$k][$group_color][0]-$grand_total_arr[0]) : (0-$grand_total_arr[0]), 2) ?></strong></td>
                            <?
                            $rowTotal1 = isset($stock_balance[$k][$group_color][0]) ? $stock_balance[$k][$group_color][0] : 0;
                        }else{
                            foreach ($size_arr[$k] as $k2 => $v2){
                                ?>
                                <td width="90" align="right" style="padding: 3px 2px;font-size: 14px;"><strong><?=number_format(isset($stock_balance[$k][$group_color][$v2]) ? $stock_balance[$k][$group_color][$v2]-$grand_total_arr[$v2] : 0-$grand_total_arr[$v2], 2) ?></strong></td>
                                <?
                                $rowTotal44 += isset($stock_balance[$k][$group_color][$v2]) ? $stock_balance[$k][$group_color][$v2]-$grand_total_arr[$v2] : 0-$grand_total_arr[$v2];
                            }
                        }
                        ?>
                        <td align="right" style="padding: 3px 2px;font-size: 14px;"><strong><?=number_format($rowTotal44, 2)?></strong></td>
                        <td></td>
                    </tr>
                    </tbody>
                </table>
                <?
            }
        }
        ?>

    </div>
    <?
    $html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
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

?>