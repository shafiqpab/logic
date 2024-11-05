<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_store")
{
	$data=explode('_',$data);
	echo create_drop_down( "cbo_store_name", 120, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$data[0]' and b.category_type=$data[1] and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name","id,store_name", 1, "--Select Store--", 0, "" );
	exit();
}

if ($action=="item_account_popup")
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
		$('#item_account_id').val( id );
		$('#item_account_val').val( ddd );
	}

	</script>
     <input type="hidden" id="item_account_id" />
     <input type="hidden" id="item_account_val" />
 <?

	$sql="SELECT id, item_category_id, product_name_details from product_details_master where item_category_id=$data[1] and status_active=1 and is_deleted=0";
	$arr=array(0=>$item_category);
	echo  create_list_view("list_view", "Item Category,Fabric Description,Product ID", "120,250","490","300",0, $sql , "js_set_value", "id,product_name_details", "", 1, "item_category_id,0,0", $arr , "item_category_id,product_name_details,id", "",'setFilterGrid("list_view",-1);','0,0,0','',1) ;
	exit();
}

if($action=="generate_report")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_store_name=str_replace("'","",$cbo_store_name);
	$cbo_item_category_id=str_replace("'","",$cbo_item_category_id);
	$item_account_id=str_replace("'","",$item_account_id);

    if ($cbo_company_name==0) $company_id =""; else $company_id =" and a.company_id='$cbo_company_name'";
	if ($cbo_company_name==0) $prod_company_id =""; else $prod_company_id =" and b.company_id='$cbo_company_name'";
    if ($cbo_company_name==0) $prod_company_id_trnsf_prod =""; else $prod_company_id_trnsf_prod =" and c.company_id='$cbo_company_name'";
	if ($cbo_item_category_id==0) $item_category_id=""; else $item_category_id=" and a.item_category=$cbo_item_category_id";
	$item_account=""; $item_account_trnsf="";
	$prod_cond="";
	if($item_account_id !="")
	{
		$item_account=" and a.prod_id in ($item_account_id)";
		$prod_cond=" and id in ($item_account_id)";
        $item_account_trnsf=" and b.to_prod_id in ($item_account_id)";
	}


	if($db_type==0)
	{
		$from_date=change_date_format($from_date,'yyyy-mm-dd');
		$to_date=change_date_format($to_date,'yyyy-mm-dd');
	}
	else if($db_type==2)
	{
		$from_date=change_date_format($from_date,'','',1);
		$to_date=change_date_format($to_date,'','',1);
	}

	//echo $from_date."====".$to_date;die;
    if ($cbo_store_name==0){ $store_id="";}else{$store_id=" and a.store_id='$cbo_store_name'";}
    if ($cbo_store_name==0){ $store_id_trnsf="";}else{$store_id_trnsf=" and b.to_store='$cbo_store_name'";}
    if ($cbo_store_name==0){ $from_store_id_trnsf="";}else{$from_store_id_trnsf=" and b.from_store='$cbo_store_name'";}

    $store_cond1="";
        if($cbo_store_name>0) $store_cond1=" and a.store_id=$cbo_store_name";


    /*$sql_check_store_trnsf=sql_select("select b.id from inv_transaction a, product_details_master b,inv_item_transfer_mst c
            where a.prod_id=b.id and a.mst_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.order_id=0 $company_id $prod_company_id $item_category_id $store_id $item_account");

    if(count($sql_check_store_trnsf)>0)
    {
        if ($cbo_store_name==0){ $store_id="";}else{$store_id=" and a.store_id='$cbo_store_name'";}
    }
    else
    {
        $store_id="";
    }*/

    $companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$buyer_Arr = return_library_array("select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0","id","buyer_name");
	$determinaArr = return_library_array("select id,construction from lib_yarn_count_determina_mst where status_active=1 and is_deleted=0","id","construction");


    if($report_type ==3){
        $colspan = 17;
        $tableWidth = 1541;
    }else if($report_type ==4){
        $colspan = 23;
        $tableWidth = 2141;
    }

	ob_start();
    if($report_type ==3 || $report_type ==4)
    {       
        $data_array=array();

        $sql_order="Select p.id as prod_id, p.item_description, p.item_group_id, p.current_stock, p.avg_rate_per_unit, p.company_id, d.buyer_name, b.id as prop_id, b.is_sales,b.po_breakdown_id,p.detarmination_id, p.gsm, p.dia_width,
        (case when b.trans_type in(1,4,5) and a.transaction_date<'".$from_date."' then b.quantity else 0 end) as opening_rcv_qnty,
        (case when b.trans_type in(2,3,6) and a.transaction_date<'".$from_date."' then b.quantity else 0 end) as opening_issue_qtny,
        (case when b.trans_type in(1,4,5) and a.transaction_date between '".$from_date."' and '".$to_date."' then b.quantity else 0 end) as purchase_quantity,
        (case when b.trans_type in(2,3,6) and a.transaction_date between '".$from_date."' and '".$to_date."' then b.quantity else 0 end) as issue_quantity,
        (case when b.trans_type in(1) and a.transaction_date between '".$from_date."' and '".$to_date."' then b.quantity else 0 end) as receive,
        (case when b.trans_type in(4) and a.transaction_date between '".$from_date."' and '".$to_date."' then b.quantity else 0 end) as issue_return,
        (case when b.trans_type in(2) and a.transaction_date between '".$from_date."' and '".$to_date."' then b.quantity else 0 end) as issue,
        (case when b.trans_type in(3) and a.transaction_date between '".$from_date."' and '".$to_date."' then b.quantity else 0 end) as rec_return,
        (case when b.trans_type in(5) and a.transaction_date between '".$from_date."' and '".$to_date."' then b.quantity else 0 end) as transfer_in,
        (case when b.trans_type in(6) and a.transaction_date between '".$from_date."' and '".$to_date."' then b.quantity else 0 end) as transfer_out,

        (case when b.trans_type in (1,4,5) and a.transaction_date<'".$from_date."' then a.cons_amount else 0 end) as total_rcv_value_opening,
        (case when b.trans_type in (2,3,6) and a.transaction_date<'".$from_date."' then a.cons_amount else 0 end) as total_issue_value_opening,
        (case when b.trans_type in (1,4,5) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as total_rcv_value,
        (case when b.trans_type in (2,3,6) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as total_issue_value


        from product_details_master p, inv_transaction a, order_wise_pro_details b, wo_po_break_down c, wo_po_details_master d
        where p.id=a.prod_id and a.id=b.trans_id and b.po_breakdown_id=c.id and c.job_id=d.id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and p.item_category_id in ($cbo_item_category_id) and a.item_category in ($cbo_item_category_id) and b.entry_form in (2,12,13,16,22,23,45,50,51,52,53,56,58,61,62,81,82,83,84,110,180,183) and p.company_id=$cbo_company_name $store_cond1 $item_account";



       /* $sql_qury="SELECT b.id as id,b.detarmination_id, b.gsm, b.dia_width,
        sum(case when a.transaction_type=1 and a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as rcv_total_opening,
        sum(case when a.transaction_type=2 and a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as iss_total_opening,
        sum(case when a.transaction_type=3 and a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as rcv_return_opening,
        sum(case when a.transaction_type=4 and a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as iss_return_opening,
        sum(case when a.transaction_type=5 and a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as transfer_in_opening,
        sum(case when a.transaction_type=6 and a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as transfer_out_opening,
        sum(case when a.transaction_type in (1,4,5) and a.transaction_date<'".$from_date."' then a.cons_amount else 0 end) as total_rcv_value_opening,
        sum(case when a.transaction_type in (2,3,6) and a.transaction_date<'".$from_date."' then a.cons_amount else 0 end) as total_issue_value_opening,
        sum(case when a.transaction_type=1 and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as receive,
        sum(case when a.transaction_type=2 and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as issue,
        sum(case when a.transaction_type=3 and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as rec_return,
        sum(case when a.transaction_type=4 and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as issue_return,
        sum(case when a.transaction_type=5 and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as transfer_in,
        sum(case when a.transaction_type=6 and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as transfer_out,
        sum(case when a.transaction_type in (1,4,5) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as total_rcv_value,
        sum(case when a.transaction_type in (2,3,6) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as total_issue_value
        from inv_transaction a, product_details_master b
        where a.prod_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $company_id $prod_company_id $item_category_id $store_id $item_account 
        group by b.id, b.detarmination_id, b.gsm, b.dia_width order by id ASC

        ";*/

        /*union all

        SELECT   c.id as id,c.detarmination_id, c.gsm, c.dia_width,
        sum(case when a.transfer_criteria=2 and a.transfer_date<'".$from_date."' then b.transfer_qnty else 0 end) as rcv_total_opening,
        sum(case when a.transfer_criteria=2 and a.transfer_date<'".$from_date."' then b.transfer_qnty else 0 end) as iss_total_opening,
        sum(case when a.transfer_criteria=2 and a.transfer_date<'".$from_date."' then b.transfer_qnty else 0 end) as rcv_return_opening,
        sum(case when a.transfer_criteria=2 and a.transfer_date<'".$from_date."' then b.transfer_qnty else 0 end) as iss_return_opening,
        sum(case when a.transfer_criteria=2 and a.transfer_date<'".$from_date."' then b.transfer_qnty else 0 end) as transfer_in_opening,
        sum(case when a.transfer_criteria=2 and a.transfer_date<'".$from_date."' then b.transfer_qnty else 0 end) as transfer_out_opening,
        sum(case when a.transfer_criteria in (2) and a.transfer_date<'".$from_date."' then b.transfer_qnty else 0 end) as total_rcv_value_opening,
        sum(case when a.transfer_criteria in (2) and a.transfer_date<'".$from_date."' then b.transfer_qnty else 0 end) as total_issue_value_opening,
        sum(case when a.transfer_criteria=2 and a.transfer_date between '".$from_date."' and '".$to_date."' then b.transfer_qnty else 0 end) as receive,
        sum(case when a.transfer_criteria=2 and a.transfer_date between '".$from_date."' and '".$to_date."' then b.transfer_qnty else 0 end) as issue,
        sum(case when a.transfer_criteria=2 and a.transfer_date between '".$from_date."' and '".$to_date."' then b.transfer_qnty else 0 end) as rec_return,
        sum(case when a.transfer_criteria=2 and a.transfer_date between '".$from_date."' and '".$to_date."' then b.transfer_qnty else 0 end) as issue_return,
        sum(case when a.transfer_criteria=2 and a.transfer_date between '".$from_date."' and '".$to_date."' then b.transfer_qnty else 0 end) as transfer_in,
        sum(case when a.transfer_criteria=2 and a.transfer_date between '".$from_date."' and '".$to_date."' then b.transfer_qnty else 0 end) as transfer_out,
        sum(case when a.transfer_criteria in (2) and a.transfer_date between '".$from_date."' and '".$to_date."' then b.transfer_qnty else 0 end) as total_rcv_value,
        sum(case when a.transfer_criteria in (2) and a.transfer_date between '".$from_date."' and '".$to_date."' then b.transfer_qnty else 0 end) as total_issue_value
        from inv_item_transfer_mst a,inv_item_transfer_dtls b, product_details_master c
        where a.id=b.mst_id and b.to_prod_id=c.id $prod_company_id_trnsf_prod $item_category_id $item_account_trnsf $store_id_trnsf and a.transfer_criteria=2 and a.transfer_date  between '".$from_date."' and '".$to_date."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
        group by c.id,c.detarmination_id, c.gsm, c.dia_width  order by id ASC*/

        // echo $sql_qury;die;

       /* $sql_trans_out=sql_select("select c.id as id,c.detarmination_id, c.gsm, c.dia_width, b.from_store,b.to_store,
        sum(case when a.transfer_criteria=2 and a.transfer_date between '".$from_date."' and '".$to_date."' then b.transfer_qnty else 0 end) as transfer_out
        from inv_item_transfer_mst a,inv_item_transfer_dtls b, product_details_master c
        where a.id=b.mst_id and b.to_prod_id=c.id $prod_company_id_trnsf_prod $item_category_id $item_account_trnsf $from_store_id_trnsf and a.transfer_criteria=2 and a.transfer_date  between '".$from_date."' and '".$to_date."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
        group by c.id,c.detarmination_id, c.gsm, c.dia_width,b.from_store,b.to_store  order by id ASC");
        
        foreach ($sql_trans_out as $row) 
        {
           $transfer_outQnty[$row[csf("id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]]['tranfOutQnty']=$row[csf("transfer_out")];
        }*/
           
        $trnasactionData=sql_select($sql_order);

        $prod_ids="";
        foreach($trnasactionData as $row)
        {
            $prod_ids.=$row[csf("prod_id")].',';
            //$data_array[$row[csf("prod_id")]]['rcv_total_opening']=$row[csf("rcv_total_opening")];
            //$data_array[$row[csf("prod_id")]]['iss_total_opening']=$row[csf("iss_total_opening")];
            //$data_array[$row[csf("prod_id")]]['rcv_return_opening']=$row[csf("rcv_return_opening")];
            //$data_array[$row[csf("prod_id")]]['iss_return_opening']=$row[csf("iss_return_opening")];

            $data_array[$row[csf("prod_id")]]['opening_rcv_qnty']+=$row[csf("opening_rcv_qnty")];
            $data_array[$row[csf("prod_id")]]['opening_issue_qtny']+=$row[csf("opening_issue_qtny")];

            
                //$opening_stock=$row[csf("opening_rcv_qnty")]-$row[csf("opening_issue_qtny")];
               // $closingStock = $opening_stock+$purchase_quantity-$issue_quantity;

            $data_array[$row[csf("prod_id")]]['receive']+=$row[csf("receive")];
            $data_array[$row[csf("prod_id")]]['issue']+=$row[csf("issue")];
            $data_array[$row[csf("prod_id")]]['rec_return']+=$row[csf("rec_return")];
            $data_array[$row[csf("prod_id")]]['issue_return']+=$row[csf("issue_return")];
            $data_array[$row[csf("prod_id")]]['transfer_in']+=$row[csf("transfer_in")];
            $data_array[$row[csf("prod_id")]]['transfer_out']+=$row[csf("transfer_out")];
            //$data_array[$row[csf("prod_id")]]['transfer_in_opening']=$row[csf("transfer_in_opening")];
            //$data_array[$row[csf("prod_id")]]['transfer_out_opening']=$row[csf("transfer_out_opening")];
            $data_array[$row[csf("prod_id")]]['total_rcv_value_opening']+=$row[csf("total_rcv_value_opening")];
            $data_array[$row[csf("prod_id")]]['total_issue_value_opening']+=$row[csf("total_issue_value_opening")];
            $data_array[$row[csf("prod_id")]]['total_rcv_value']+=$row[csf("total_rcv_value")];
            $data_array[$row[csf("prod_id")]]['total_issue_value']+=$row[csf("total_issue_value")];

            $data_array[$row[csf("prod_id")]]['detarmination_id']=$row[csf("detarmination_id")];
            $data_array[$row[csf("prod_id")]]['gsm']=$row[csf("gsm")];
            $data_array[$row[csf("prod_id")]]['dia_width']=$row[csf("dia_width")];
            //$transfer_outQnty[$row[csf("prod_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]]['tranfOutQnty']=$row[csf("transfer_out")];
        }
        $prod_ids =chop($prod_ids,",");
        $date_array=array();
        $dateRes_date="select prod_id, min(transaction_date) as min_date, max(transaction_date) as max_date from inv_transaction where is_deleted=0 and status_active=1 and item_category=13 group by prod_id";
        $result_dateRes_date = sql_select($dateRes_date);
        foreach($result_dateRes_date as $row)
        {
            $date_array[$row[csf("prod_id")]]['min_date']=$row[csf("min_date")];
            $date_array[$row[csf("prod_id")]]['max_date']=$row[csf("max_date")];
        }

        // ===========================Non order query start =============================
        $sql_non_order_roll="Select p.id as prod_id, p.item_description, p.item_group_id, p.current_stock, p.avg_rate_per_unit, p.company_id, d.buyer_id as buyer_name,p.detarmination_id, p.gsm, p.dia_width,
        sum(case when a.transaction_date<'".$from_date."' then c.qnty else 0 end) as rcv_total_opening,
        0 as opening_issue_qtny,
        0 as opening_transfer_rcv,
        0 as opening_transfer_issue,
        sum(case when a.transaction_date between '".$from_date."' and '".$to_date."' then c.qnty else 0 end) as receive,
        0 as issue,
        0 as transfer_rcv,
        0 as transfer_issue,
        1 as type
        from product_details_master p, inv_transaction a, pro_grey_prod_entry_dtls b, pro_roll_details c, wo_non_ord_samp_booking_mst d
        where p.id=a.prod_id and a.id=b.trans_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and c.entry_form in(58) and c.booking_without_order=1 and a.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and p.item_category_id=$cbo_item_category_id and a.item_category=$cbo_item_category_id and p.company_id=$cbo_company_name $store_cond1 $item_account
        group by p.id, p.item_description, p.item_group_id, p.current_stock, p.avg_rate_per_unit, p.company_id, d.buyer_id,p.detarmination_id, p.gsm, p.dia_width

        union all

        Select p.id as prod_id, p.item_description, p.item_group_id, p.current_stock, p.avg_rate_per_unit, p.company_id, d.buyer_id as buyer_name,p.detarmination_id, p.gsm, p.dia_width,
        0 as rcv_total_opening,
        sum(case when a.transaction_date<'".$from_date."' then c.qnty else 0 end) as opening_issue_qtny,
        0 as opening_transfer_rcv,
        0 as opening_transfer_issue,
        0 as receive,
        sum(case when a.transaction_date between '".$from_date."' and '".$to_date."' then c.qnty else 0 end) as issue,
        0 as transfer_rcv,
        0 as transfer_issue,
        2 as type
        from product_details_master p, inv_transaction a, inv_grey_fabric_issue_dtls b, pro_roll_details c, wo_non_ord_samp_booking_mst d
        where p.id=a.prod_id and a.id=b.trans_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and c.entry_form=61 and c.booking_without_order=1 and a.transaction_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and p.item_category_id=$cbo_item_category_id and a.item_category=$cbo_item_category_id and p.company_id=$cbo_company_name $store_cond1 $item_account
        group by p.id, p.item_description, p.item_group_id, p.current_stock, p.avg_rate_per_unit, p.company_id, d.buyer_id,p.detarmination_id, p.gsm, p.dia_width

        union all

        Select p.id as prod_id, p.item_description, p.item_group_id, p.current_stock, p.avg_rate_per_unit, p.company_id, c.buyer_id as buyer_name,p.detarmination_id, p.gsm, p.dia_width,
        0 as rcv_total_opening,
        0 as opening_issue_qtny,
        sum(case when a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as opening_transfer_rcv,
        0 as opening_transfer_issue,
        0 as receive,
        0 as issue,
        sum(case when a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as transfer_rcv,
        0 as transfer_issue,
        3 as type
        from product_details_master p, inv_transaction a, inv_item_transfer_mst b, wo_non_ord_samp_booking_mst c
        where p.id=a.prod_id and a.mst_id=b.id and b.to_order_id=c.id and b.entry_form in(110,180) and a.transaction_type=5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and p.item_category_id=$cbo_item_category_id and a.item_category=$cbo_item_category_id and p.company_id=$cbo_company_name $store_cond1 $item_account
        group by p.id, p.item_description, p.item_group_id, p.current_stock, p.avg_rate_per_unit, p.company_id, c.buyer_id,p.detarmination_id, p.gsm, p.dia_width

        union all

        Select p.id as prod_id, p.item_description, p.item_group_id, p.current_stock, p.avg_rate_per_unit, p.company_id, c.buyer_id as buyer_name,p.detarmination_id, p.gsm, p.dia_width,
        0 as rcv_total_opening,
        0 as opening_issue_qtny,
        0 as opening_transfer_rcv,
        sum(case when a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as opening_transfer_issue,
        0 as receive,
        0 as issue,
        0 as transfer_rcv,
        sum(case when a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as transfer_issue,
        4 as type
        from product_details_master p, inv_transaction a, inv_item_transfer_mst b, wo_non_ord_samp_booking_mst c
        where p.id=a.prod_id and a.mst_id=b.id and b.from_order_id=c.id and b.entry_form in(183,180) and a.transaction_type=6 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and p.item_category_id=$cbo_item_category_id and a.item_category=$cbo_item_category_id and p.company_id=$cbo_company_name $store_cond1 $item_account
        group by p.id, p.item_description, p.item_group_id, p.current_stock, p.avg_rate_per_unit, p.company_id, c.buyer_id,p.detarmination_id, p.gsm, p.dia_width";
        
        //echo $sql_non_order_roll;die;
        
        $result_non_order_roll = sql_select($sql_non_order_roll);
        //var_dump($result_non_order);
        foreach($result_non_order_roll as $row)
        {

             $prod_ids.=$row[csf("prod_id")].',';
            //$data_array[$row[csf("prod_id")]]['rcv_total_opening']=$row[csf("rcv_total_opening")];
            //$data_array[$row[csf("prod_id")]]['iss_total_opening']=$row[csf("iss_total_opening")];
            //$data_array[$row[csf("prod_id")]]['rcv_return_opening']=$row[csf("rcv_return_opening")];
            //$data_array[$row[csf("prod_id")]]['iss_return_opening']=$row[csf("iss_return_opening")];

            $data_array[$row[csf("prod_id")]]['opening_rcv_qnty']+=$row[csf("rcv_total_opening")];
            $data_array[$row[csf("prod_id")]]['opening_issue_qtny']+=$row[csf("opening_issue_qtny")];

            


            $data_array[$row[csf("prod_id")]]['receive']+=$row[csf("receive")];
            $data_array[$row[csf("prod_id")]]['issue']+=$row[csf("issue")];
           // $data_array[$row[csf("prod_id")]]['rec_return']+=$row[csf("rec_return")];
           // $data_array[$row[csf("prod_id")]]['issue_return']+=$row[csf("issue_return")];
            $data_array[$row[csf("prod_id")]]['transfer_in']+=$row[csf("transfer_rcv")];
            $data_array[$row[csf("prod_id")]]['transfer_out']+=$row[csf("transfer_issue")];
            //$data_array[$row[csf("prod_id")]]['transfer_in_opening']=$row[csf("transfer_in_opening")];
            //$data_array[$row[csf("prod_id")]]['transfer_out_opening']=$row[csf("transfer_out_opening")];
            //$data_array[$row[csf("prod_id")]]['total_rcv_value_opening']+=$row[csf("total_rcv_value_opening")];
            //$data_array[$row[csf("prod_id")]]['total_issue_value_opening']+=$row[csf("total_issue_value_opening")];
            //$data_array[$row[csf("prod_id")]]['total_rcv_value']+=$row[csf("total_rcv_value")];
            //$data_array[$row[csf("prod_id")]]['total_issue_value']+=$row[csf("total_issue_value")];

            $data_array[$row[csf("prod_id")]]['detarmination_id']=$row[csf("detarmination_id")];
            $data_array[$row[csf("prod_id")]]['gsm']=$row[csf("gsm")];
            $data_array[$row[csf("prod_id")]]['dia_width']=$row[csf("dia_width")];


        }




        ?>
        <div>
            <table style="width:<? echo $tableWidth;?>px" border="1" cellpadding="2" cellspacing="0"  id="caption" rules="all">
                <thead>
                    <tr class="form_caption" style="border:none;">
                        <td colspan="<? echo $colspan;?>" align="center" style="border:none;font-size:16px; font-weight:bold" >Grey Fabric <? echo $report_title; ?></td>
                    </tr>
                    <tr class="form_caption" style="border:none;">
                        <td colspan="<? echo $colspan;?>" align="center" style="border:none; font-size:14px;">
                           <b>Company Name : <? echo $companyArr[$cbo_company_name]; ?></b>
                        </td>
                    </tr>
                    <tr class="form_caption" style="border:none;">
                        <td colspan="<? echo $colspan;?>" align="center" style="border:none;font-size:12px; font-weight:bold">
                            <? if($from_date!="" || $to_date!="") echo "From : ".change_date_format($from_date)." To : ".change_date_format($to_date)."" ;?>
                        </td>
                    </tr>
                </thead>
            </table>
            <table width="<? echo $tableWidth;?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
            	<thead>
                    <tr>
                        <th rowspan="2" width="40">SL</th>
                        <th rowspan="2" width="60">Prod.ID</th>
                        <th colspan="4">Description<? echo $report_type;?></th>
                        <? if($report_type==4){?>
                        <th rowspan="2" width="100" style="">Opening Rate</th>
                        <? }?>
                        <th rowspan="2" width="110">Opening Stock</th>
                        <?  if($report_type==4){ ?>
                        <th rowspan="2" width="100" style="">Opening Value</th>
                            <? }?>
                        <th colspan="4">Receive</th>
                        <? if($report_type==4){?>
                        <th rowspan="2" width="100" style="">Total Rcv Value</th>
                            <?}?>
                        <th colspan="4">Issue</th>
                        <? if($report_type==4){?>
                        <th rowspan="2" width="100" style="">Total Issue Value</th>
                        <th rowspan="2" width="100" style="">Avg. Rate</th>
                            <?}?>
                        <th rowspan="2" width="100">Closing Stock</th>
                        <? if($report_type==4){?><th rowspan="2" width="100" style="" >Closing Value</th><?}?>
                        <th rowspan="2">DOH</th>
                    </tr>
                    <tr>
                        <th width="120">Construction</th>
                        <th width="180">Composition</th>
                        <th width="70">GSM</th>
                        <th width="100">Dia/Width</th>

                        <th width="80">Receive</th>
                        <th width="80">Issue Return</th>
                        <th width="80">Transfer In</th>
                        <th width="100">Total Receive</th>

                        <th width="80">Issue</th>
                        <th width="80">Receive Return</th>
                        <th width="80">Transfer Out</th>
                        <th width="100">Total Issue</th>
                    </tr>
                </thead>
            </table>
            <div style="width:<? echo $tableWidth + 19?>px; max-height:350px; overflow-y:scroll" id="scroll_body" >
                <table width="<? echo $tableWidth;?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
                <?
                    $composition_arr=array(); $i=1;
                    $sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
                    $deterdata_array=sql_select($sql_deter);
                    if(count($deterdata_array)>0)
                    {
                        foreach( $deterdata_array as $row )
                        {
                            if(array_key_exists($row[csf('id')],$composition_arr))
                            {
                                $composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
                            }
                            else
                            {
                                $composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
                            }
                        }
                    }
                    //$trans = return_library_array("select  prod_id, sum(quantity) as qnty from order_wise_pro_details where entry_form in(2,22,58) and trans_id!=0 and trans_type=1 and status_active=1 and is_deleted=0 group by prod_id order by prod_id","prod_id","qnty");

                    foreach($data_array as $prod_id=> $row)
                    {
                        if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";

                        //$total_value_opening=$data_array[$row[csf("id")]]['total_rcv_value_opening'] - $data_array[$row[csf("id")]]['total_issue_value_opening'];
                        //$opening_rcv= ($row['rcv_total_opening']+$row['iss_return_opening']+$row['transfer_in_opening']);
    					//$opening_issue = $row['iss_total_opening']+$row['rcv_return_opening']+$row['transfer_out_opening'];
                        
                        $opening_rcv= $row['opening_rcv_qnty'];
                        $opening_issue = $row['opening_issue_qtny'];

                        $opening= $opening_rcv - $opening_issue;

                        $opening_rate = $total_value_opening = 0;
                        if($opening_rcv > 0){
                        $opening_rate = $row['total_rcv_value_opening']/ $opening_rcv;
    					}
    					$total_value_opening = $opening *$opening_rate;
                        //$opening_rate = $total_value_opening/$opening;

                        $receive = $row['receive'];
    					$issue_return = $row['issue_return'];
                        $transfer_in = $row['transfer_in'];
                        $totalReceive=$receive+$issue_return+$transfer_in;

                        $total_rcv_value = $row['total_rcv_value'];

                        $issue = $row['issue'];
    					$rec_return = $row['rec_return'];
                        $transfer_out = $row['transfer_out'];
                        $totalIssue=$issue+$rec_return+$transfer_out;
                        $total_issue_value=$row['total_issue_value'];

                        $closingStock=$opening+$totalReceive-$totalIssue;
                        //echo $get_upto_qnty.'='.$txt_qnty;
                        //$get_upto=array(1=>"Greater Than",2=>"Less Than",3=>"Greater/Equal",4=>"Less/Equal",5=>"Equal");
                        if (
                            ($get_upto_qnty==1 && $txt_qnty<$closingStock) ||
                            ($get_upto_qnty==2 && $txt_qnty>$closingStock) ||
                            ($get_upto_qnty==3 && $txt_qnty<=$closingStock) ||
                            ($get_upto_qnty==4 && $txt_qnty>=$closingStock) ||
                            ($get_upto_qnty==5 && $txt_qnty==$closingStock) ||
                            ($get_upto_qnty==0)
                            )  
                        {
                            $closingValue= $total_value_opening+ $total_rcv_value - $total_issue_value;
                            $closingRate = $closingValue/$closingStock;

                            $totalStockValue+=$closingStock;
                            $tot_closingValue+=$closingValue;
                            $tot_total_value_opening+=$total_value_opening;
                            $tot_total_rcv_value+=$total_rcv_value;
                            $tot_total_issue_value+=$total_issue_value;

                            $tot_opening+=$opening;
                            $tot_transfer_in+=$transfer_in;
                            $tot_transfer_out+=$transfer_out;
                            $total_receive+=$totalReceive;
                            $tot_issue+=$totalIssue;
        					$tot_issue_return+=$issue_return;
        					$tot_rec_return+=$rec_return;


                            //$opening==$total_value_opening==$receive==$issue_return==$transfer_in==$totalReceive==$total_rcv_value==$issue==$rec_return==$transfer_out==$totalIssue
                            if(($cbo_value_with ==1 && (number_format($opening,2,'.','')!= 0 || number_format($total_value_opening,2,'.','')!=0 || number_format($receive,2,'.','')!= 0 ||number_format($issue_return,2,'.','')!=0 || number_format($transfer_in,2,'.','')!= 0 || number_format($totalReceive,2,'.','')!=0 || number_format($total_rcv_value,2,'.','')!= 0 || number_format($issue,2,'.','')!=0 || number_format($rec_return,2,'.','')!=0 || number_format($transfer_out,2,'.','')!= 0 || number_format($totalIssue,2,'.','')!=0) ) || ($cbo_value_with ==0 && (number_format($opening,2,'.','')== 0 || number_format($total_value_opening,2,'.','')==0 || number_format($receive,2,'.','')== 0 ||number_format($issue_return,2,'.','')==0 || number_format($transfer_in,2,'.','')== 0 || number_format($totalReceive,2,'.','')==0 || number_format($total_rcv_value,2,'.','')== 0 || number_format($issue,2,'.','')==0 || number_format($rec_return,2,'.','')==0 || number_format($transfer_out,2,'.','')== 0 || number_format($totalIssue,2,'.','')==0)))
                            {
                                /*if($opening>0 || $totalReceive>0 || $totalIssue>0)
                                { 
                                    if($opening>=0 || $totalReceive>=0 || $totalIssue>=0)
                                    {*/
                                        ?>
                                        <tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                            <td width="40"><? echo $i; ?></td>
                                            <td width="60" align="center"><p><? echo $prod_id; ?></p></td>
                                            <td width="120"><? echo $determinaArr[$row["detarmination_id"]]; ?></td>
                                            <td width="180"><p><? echo $composition_arr[$row["detarmination_id"]]; ?></p></td>
                                            <td width="70"><p><? echo $row["gsm"]; ?></p></td>
                                            <td width="100"><p><? echo $row["dia_width"]; ?></p></td>
                                            <? if($report_type==4){?>
                                            <td width="100" align="right"><p><? echo number_format($opening_rate,2);?></p></td>
                                            <?}?>
                                            <td width="110" align="right"><p><? echo number_format($opening,2); ?></p></td>
                                            <? if($report_type==4){?>
                                            <td width="100" align="right"><p><? echo number_format($total_value_opening,2);?></p></td>
                                            <?}?>
                                            <td width="80" align="right"><p><? echo number_format($receive,2); ?></p></td>
                                            <td width="80" align="right"><p><? echo number_format($issue_return,2); ?></p></td>
                                            <td width="80" align="right"><p>
                                                <?
                                               // if ($transfer_in>0) {
                                                    echo number_format($transfer_in,2);
                                                //}
                                                 //else{echo number_format($transfer_qnty_store_arr[$prod_id]['transfer_qnty'],2);  }
                                                ?>
                                                </p></td>
                                            <td width="100" align="right"><p><? echo number_format($totalReceive,2); ?></p></td>
                                            <? if($report_type==4){?>
                                            <td width="100" align="right"><p><? echo number_format($total_rcv_value,2);?></p></td>
                                            <?}?>
                                            <td width="80" align="right"><p><? echo number_format($issue,2); ?></p></td>
                                            <td width="80" align="right"><p><? echo number_format($rec_return,2); ?></p></td>
                                            <td width="80" align="right"><p>

                                            <?
                                              echo number_format($transfer_out,2);
                                              // echo number_format($transfer_outQnty[$prod_id][$row["detarmination_id"]][$row["gsm"]][$row["dia_width"]]['tranfOutQnty'],2);
                                                //if ($transfer_in>0) {echo number_format($transfer_in,2); }
                                                // else{echo number_format($transfer_qnty_store_arr[$prod_id]['transfer_qnty'],2);  }
                                            ?>


                                                </p></td>
                                            <td width="100" align="right"><p><? echo number_format($totalIssue,2); ?></p></td>
                                            <? if($report_type==4){?>
                                            <td width="100" align="right"><p><? echo number_format($total_issue_value,2);?></p></td>
                                            <td width="100" align="right"><p><? echo number_format($closingRate,2);?></p></td>
                                            <?}?>
                                            <td width="100" align="right"><p><? echo number_format($closingStock,2); ?></p></td>
                                            <? if($report_type==4){?>
                                            <td width="100" align="right"><p><? echo number_format($closingValue,2);?></p></td>
                                            <?}?>
                                            <? $daysOnHand = datediff("d",$date_array[$prod_id]['max_date'],date("Y-m-d")); ?>
                                            <td align="center"><? echo $daysOnHand; ?></td>
                                        </tr>
                                        <?
                                        $i++;
                                   // }
                               // }
                            }   
    					}
                    }
    				?>
                </table>
    		</div>
            <table width="<? echo $tableWidth;?>" border="1" cellpadding="0" cellspacing="0" class="tbl_bottom" rules="all" >
               <tr>
                    <td width="40" align="right">&nbsp;</td>
                    <td width="60" align="right">&nbsp;</td>
                    <td width="120" align="right">&nbsp;</td>
                    <td width="180" align="right">&nbsp;</td>
                    <td width="70" align="right">&nbsp;</td>
                        <? if($report_type==4){?>
                    <td width="100"><p>&nbsp;</p></td>
                        <?}?>
                    <td width="100" align="right">Total</td>

                    <td width="110" align="right" id="value_tot_opening"><? echo number_format($tot_opening,2);  ?></td>
                    <? if($report_type==4){?>
                    <td width="100" align="right" id="value_tot_opening_amount"><p><? echo $tot_total_value_opening;?></p></td>
                    <?}?>
                    <td width="80" align="right" id="value_tot_receive"><? echo number_format($tot_receive,2);  ?></td>
                    <td width="80" align="right" id="value_tot_issue_return"><? echo number_format($tot_issue_return,2);  ?></td>
                    <td width="80" align="right" id="value_tot_trans_in"><? echo number_format($tot_transfer_in,2);  ?></td>
                    <td width="100" align="right" id="value_total_receive"><? echo number_format($total_receive,2);  ?></td>
                    <? if($report_type==4){?>
                    <td width="100" align="right" id="value_total_rcv_amount"><p><? echo number_format($tot_total_rcv_value,2);?></p></td>
                    <?}?>
                    <td width="80" align="right" id="value_tot_issue"><? echo number_format($tot_issue,2);  ?></td>
                    <td width="80" align="right" id="value_tot_rec_return"><? echo number_format($tot_rec_return,2);  ?></td>
                    <td width="80" align="right" id="value_tot_transfer_out"><? echo number_format($tot_transfer_out,2);  ?></td>
                    <td width="100" align="right" id="value_total_issue"><? echo number_format($total_issue,2);  ?></td>
                    <? if($report_type==4){?>
                    <td width="100" align="right" id="value_tot_issue_amount"><p><? echo number_format($tot_total_issue_value,2);?></p></td>
                    <td width="100"><p>&nbsp;</p></td>
                    <?}?>
                    <td width="100" align="right" id="value_totalStock"><? echo number_format($totalStockValue,2); ?></td>
                     <? if($report_type==4){?>
                    <td width="100" align="right" id="value_totalStock_amount"><p><? echo number_format($tot_closingValue,2);?></p></td>
                     <?}?>


                    <td align="right">&nbsp;</td>
                </tr>
            </table>
    	</div>
        <?
    }
	elseif($report_type == 5)
	{
		$buyer_arr = return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
		$store_cond="";
		if($cbo_store_name>0) $store_cond=" and a.store_id=$cbo_store_name";
		//$order_basis_data_array = array();
		//sum(case when b.trans_type in(1,4,5) and a.transaction_date between '".$from_date."' and '".$to_date."' then b.quantity else 0 end) as purchase_quantity,
		//sum(case when b.trans_type in(2,3,6) and a.transaction_date between '".$pre_from_date."' and '".$pre_to_date."' then b.quantity else 0 end) as pre_month_issue
		$sql_order="Select p.id as prod_id, p.item_description, p.item_group_id, p.current_stock, p.avg_rate_per_unit, p.company_id, d.buyer_name, b.id as prop_id, b.is_sales,b.po_breakdown_id,
		(case when b.trans_type in(1,4,5) and a.transaction_date<'".$from_date."' then b.quantity else 0 end) as opening_rcv_qnty,
		(case when b.trans_type in(2,3,6) and a.transaction_date<'".$from_date."' then b.quantity else 0 end) as opening_issue_qtny,
		(case when b.trans_type in(1,4,5) and a.transaction_date between '".$from_date."' and '".$to_date."' then b.quantity else 0 end) as purchase_quantity,
		(case when b.trans_type in(2,3,6) and a.transaction_date between '".$from_date."' and '".$to_date."' then b.quantity else 0 end) as issue_quantity,
		(case when b.trans_type in(1) and a.transaction_date between '".$from_date."' and '".$to_date."' then b.quantity else 0 end) as receive,
		(case when b.trans_type in(4) and a.transaction_date between '".$from_date."' and '".$to_date."' then b.quantity else 0 end) as issue_return,
		(case when b.trans_type in(2) and a.transaction_date between '".$from_date."' and '".$to_date."' then b.quantity else 0 end) as issue,
		(case when b.trans_type in(3) and a.transaction_date between '".$from_date."' and '".$to_date."' then b.quantity else 0 end) as receive_return,
		(case when b.trans_type in(5) and a.transaction_date between '".$from_date."' and '".$to_date."' then b.quantity else 0 end) as receive_transfer,
		(case when b.trans_type in(6) and a.transaction_date between '".$from_date."' and '".$to_date."' then b.quantity else 0 end) as issue_transfer
		from product_details_master p, inv_transaction a, order_wise_pro_details b, wo_po_break_down c, wo_po_details_master d
		where p.id=a.prod_id and a.id=b.trans_id and b.po_breakdown_id=c.id and c.job_id=d.id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and p.item_category_id in ($cbo_item_category_id) and a.item_category in ($cbo_item_category_id) and b.entry_form in (2,12,13,16,22,23,45,50,51,52,53,56,58,61,62,81,82,83,84,110,180,183) and p.company_id=$cbo_company_name $store_cond $item_account";
		//and c.job_no_mst=d.job_no

		//echo $sql_order; die;
		$result = sql_select($sql_order);
		$report_arr = array();$count=0;
		$sl=1;$prodIDSS="";
		foreach($result as $row)
		{
            /*if($row[csf("receive_transfer")]>0)
            {
                $prodIDSS.=$row[csf("prod_id")].",";
            }*/
			if($propotionate_check[$row[csf("prop_id")]]=="")
			{
				$propotionate_check[$row[csf("prop_id")]]=$row[csf("prop_id")];
				$tot_receive = $row[csf("receive")]+$row[csf("issue_return")]+$row[csf("receive_transfer")];
				$tot_issue = $row[csf("issue")]+$row[csf("receive_return")]+$row[csf("issue_transfer")];
				$opening_stock=$row[csf("opening_rcv_qnty")]-$row[csf("opening_issue_qtny")];
				$closingStock = $opening_stock+$purchase_quantity-$issue_quantity;
	
				$report_arr[$row[csf('buyer_name')]]['opening'] += $opening_stock;
				$report_arr[$row[csf('buyer_name')]]['issue_quantity'] += $issue_quantity;
				$report_arr[$row[csf('buyer_name')]]['receive'] += $row[csf("receive")];
				$report_arr[$row[csf('buyer_name')]]['iss_return'] += $row[csf("issue_return")];
				$report_arr[$row[csf('buyer_name')]]['trans_in'] += $row[csf("receive_transfer")];
				$report_arr[$row[csf('buyer_name')]]['total_rcv'] += $tot_receive;
	
				$report_arr[$row[csf('buyer_name')]]['issue'] += $row[csf("issue")];
				$report_arr[$row[csf('buyer_name')]]['trans_out'] += $row[csf("issue_transfer")];
				$report_arr[$row[csf('buyer_name')]]['rcv_return'] += $row[csf("receive_return")];
				$report_arr[$row[csf('buyer_name')]]['total_issue'] += $tot_issue;
				$report_arr[$row[csf('buyer_name')]]['closingStock'] += $closingStock;
                $report_arr[$row[csf('buyer_name')]]['order_id'] = $row[csf("po_breakdown_id")];
                $report_arr[$row[csf('buyer_name')]]['is_sales'] = $row[csf("is_sales")];

                if($row[csf("is_sales")]==1)
                {
                    //$sales_ids_arr[] = $rows[csf("order_id")];
                    $order_ids.=$row[csf("po_breakdown_id")].',';
                }
			}
		}
        //echo $prodIDSS;


      /*  $prodIDSS=chop($prodIDSS,",");
        $pz=1;
        if($prodIDSS!="")
        {
            $prod_ids_arr=array_chunk(array_unique(explode(",",$prodIDSS)),999);
            foreach($prod_ids_arr as $pro_id)
            {
                if($pz==1) $sales_order_id_condz .=" and (p.id in(".implode(',',$pro_id).")"; else $sales_order_id_condz .=" or p.id in(".implode(',',$pro_id).")";
                $pz++;
            }
            $sales_order_id_condz .=" )";
        }


        echo  $sales_order_id_condz;

        die;*/

        // print_r($order_ids);
		//echo "<pre>";print_r($report_arr);die;
        $order_ids=chop($order_ids,",");
        $p=1;
        if($order_ids!="")
        {
            $order_ids_arr=array_chunk(array_unique(explode(",",$order_ids)),999);
            foreach($order_ids_arr as $po_id)
            {
                if($p==1) $sales_order_id_cond .=" and (id in(".implode(',',$po_id).")"; else $sales_order_id_cond .=" or id in(".implode(',',$po_id).")";
                $p++;
            }
            $sales_order_id_cond .=" )";

            if($db_type==0) $selected_year="year(insert_date) as job_year"; else $selected_year="to_char(insert_date,'YYYY') as job_year";

            $sql_sales_query = "select id,job_no,sales_booking_no, within_group, buyer_id, style_ref_no, booking_without_order, $selected_year from fabric_sales_order_mst where status_active=1 and is_deleted=0  $sales_order_id_cond";
            // echo $sql_sales_query;

            $sql_sales_data=sql_select($sql_sales_query);
            foreach($sql_sales_data as $row)
            {
                $sales_data_arr[$row[csf("id")]]['job_no']=$row[csf("job_no")];
                $sales_data_arr[$row[csf("id")]]['sales_booking_no']=$row[csf("sales_booking_no")];
                $sales_data_arr[$row[csf("id")]]['within_group']=$row[csf("within_group")];
                $sales_data_arr[$row[csf("id")]]['buyer_id']=$row[csf("buyer_id")];
                $sales_data_arr[$row[csf("id")]]['style_ref_no']=$row[csf("style_ref_no")];
                $sales_data_arr[$row[csf("id")]]['job_year']=$row[csf("job_year")];
            }
            /*echo "<pre>";
            print_r($sales_data_arr);*/
        }
		unset($result);
		//echo "select  company_name, variable_list, item_category_id, fabric_roll_level from variable_settings_production where variable_list = 3 and company_name = $cbo_company_name and item_category_id in( 13,14) and status_active = 1 and is_deleted = 0";
		$variable_settings_result = sql_select("select  company_name, variable_list, item_category_id, fabric_roll_level from variable_settings_production where variable_list = 3 and company_name = $cbo_company_name and item_category_id in( 13,14) and status_active = 1 and is_deleted = 0");

		foreach ($variable_settings_result as $row) {
			$variable_settings_arr = $row[csf("fabric_roll_level")];
		}
		//var_dump($variable_settings_result);
		if($variable_settings_arr = 1){
			$receive_basis_cond = " and a.receive_basis=9";
		}
		else{
			$receive_basis_cond = " and a.receive_basis=10";
		}
		// ===========================Non order query start =============================
		$sql_non_order_roll="Select p.id as prod_id, p.item_description, p.item_group_id, p.current_stock, p.avg_rate_per_unit, p.company_id, d.buyer_id as buyer_name,
		sum(case when a.transaction_date<'".$from_date."' then c.qnty else 0 end) as rcv_total_opening,
		0 as opening_issue_qtny,
		0 as opening_transfer_rcv,
		0 as opening_transfer_issue,
		sum(case when a.transaction_date between '".$from_date."' and '".$to_date."' then c.qnty else 0 end) as receive,
		0 as issue,
		0 as transfer_rcv,
		0 as transfer_issue,
		1 as type
		from product_details_master p, inv_transaction a, pro_grey_prod_entry_dtls b, pro_roll_details c, wo_non_ord_samp_booking_mst d
		where p.id=a.prod_id and a.id=b.trans_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and c.entry_form in(58) and c.booking_without_order=1 and a.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and p.item_category_id=$cbo_item_category_id and a.item_category=$cbo_item_category_id and p.company_id=$cbo_company_name $store_cond $item_account
		group by p.id, p.item_description, p.item_group_id, p.current_stock, p.avg_rate_per_unit, p.company_id, d.buyer_id

		union all

		Select p.id as prod_id, p.item_description, p.item_group_id, p.current_stock, p.avg_rate_per_unit, p.company_id, d.buyer_id as buyer_name,
		0 as rcv_total_opening,
		sum(case when a.transaction_date<'".$from_date."' then c.qnty else 0 end) as opening_issue_qtny,
		0 as opening_transfer_rcv,
		0 as opening_transfer_issue,
		0 as receive,
		sum(case when a.transaction_date between '".$from_date."' and '".$to_date."' then c.qnty else 0 end) as issue,
		0 as transfer_rcv,
		0 as transfer_issue,
		2 as type
		from product_details_master p, inv_transaction a, inv_grey_fabric_issue_dtls b, pro_roll_details c, wo_non_ord_samp_booking_mst d
		where p.id=a.prod_id and a.id=b.trans_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and c.entry_form=61 and c.booking_without_order=1 and a.transaction_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and p.item_category_id=$cbo_item_category_id and a.item_category=$cbo_item_category_id and p.company_id=$cbo_company_name $store_cond $item_account
		group by p.id, p.item_description, p.item_group_id, p.current_stock, p.avg_rate_per_unit, p.company_id, d.buyer_id

		union all

		Select p.id as prod_id, p.item_description, p.item_group_id, p.current_stock, p.avg_rate_per_unit, p.company_id, c.buyer_id as buyer_name,
		0 as rcv_total_opening,
		0 as opening_issue_qtny,
		sum(case when a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as opening_transfer_rcv,
		0 as opening_transfer_issue,
		0 as receive,
		0 as issue,
		sum(case when a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as transfer_rcv,
		0 as transfer_issue,
		3 as type
		from product_details_master p, inv_transaction a, inv_item_transfer_mst b, wo_non_ord_samp_booking_mst c
		where p.id=a.prod_id and a.mst_id=b.id and b.to_order_id=c.id and b.entry_form in(110,180) and a.transaction_type=5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and p.item_category_id=$cbo_item_category_id and a.item_category=$cbo_item_category_id and p.company_id=$cbo_company_name $store_cond $item_account
		group by p.id, p.item_description, p.item_group_id, p.current_stock, p.avg_rate_per_unit, p.company_id, c.buyer_id

		union all

		Select p.id as prod_id, p.item_description, p.item_group_id, p.current_stock, p.avg_rate_per_unit, p.company_id, c.buyer_id as buyer_name,
		0 as rcv_total_opening,
		0 as opening_issue_qtny,
		0 as opening_transfer_rcv,
		sum(case when a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as opening_transfer_issue,
		0 as receive,
		0 as issue,
		0 as transfer_rcv,
		sum(case when a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as transfer_issue,
		4 as type
		from product_details_master p, inv_transaction a, inv_item_transfer_mst b, wo_non_ord_samp_booking_mst c
		where p.id=a.prod_id and a.mst_id=b.id and b.from_order_id=c.id and b.entry_form in(183,180) and a.transaction_type=6 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and p.item_category_id=$cbo_item_category_id and a.item_category=$cbo_item_category_id and p.company_id=$cbo_company_name $store_cond $item_account
		group by p.id, p.item_description, p.item_group_id, p.current_stock, p.avg_rate_per_unit, p.company_id, c.buyer_id";
		
		//echo $sql_non_order_roll;die;
		
		$result_non_order_roll = sql_select($sql_non_order_roll);
		//var_dump($result_non_order);
		$count=0;
		foreach($result_non_order_roll as $row)
		{
			$non_order_opening_bal = $row[csf("rcv_total_opening")]-$row[csf("opening_issue_qtny")];
			//$openingBalanceValue = $opening_bal*$recv_rate_arr[$row[csf("prod_id")]];

			//$receive_rtn = $row[csf("receive_rtn")];
			//$issue_rtn = $row[csf("issue_rtn")];

			//$transfer_in = $row[csf("transfer_in")];
			//$transfer_out = $row[csf("transfer_out")];

			$tot_receive = $row[csf("receive")]+$row[csf("transfer_rcv")]+$issue_rtn;
			$tot_issue += $row[csf("issue")]+$row[csf("transfer_issue")]+$receive_rtn;
			
			//$tot_receive += $row[csf("receive")]+$row[csf("transfer_rcv")]+$row[csf("issue_rtn")];
			//$tot_issue += $row[csf("issue")]+$row[csf("transfer_issue")]+$receive_rtn;

			//$t_receive=$row[csf("receive")];//($row[csf("receive")]+$row[csf("issue_return")]+$row[csf("receive_transfer")]);
			//$t_issue=$row[csf("issue")];//($row[csf("issue")];+$row[csf("receive_return")]+$row[csf("issue_transfer")]);
			//$c_stock=($opening_bal+($row[csf("receive")])-($row[csf("issue")]));
			$closingStock = $non_order_opening_bal+$row[csf("receive")]-$row[csf("issue")];
			//$closingAmout = $openingBalanceValue+$receive-$issue;

			//$report_arr_nonOrder[$row[csf('buyer_name')]]['opening'] += $openingBalanceValue;
			$report_arr_nonOrder[$row[csf('buyer_name')]]['opening_qnty'] += $non_order_opening_bal;
			$report_arr_nonOrder[$row[csf('buyer_name')]]['receive'] += $row[csf("receive")];
			$report_arr_nonOrder[$row[csf('buyer_name')]]['issue_return'] += $row[csf("issue_rtn")];
			$report_arr_nonOrder[$row[csf('buyer_name')]]['trans_in'] += $row[csf("transfer_rcv")];
			$report_arr_nonOrder[$row[csf('buyer_name')]]['trans_out'] += $row[csf("transfer_issue")];
			//$report_arr_nonOrder[$row[csf('buyer_name')]]['total_rcv'] += $tot_receive;
            $report_arr_nonOrder[$row[csf('buyer_name')]]['total_rcv'] += $row[csf("receive")]+$row[csf("transfer_rcv")]+$row[csf("issue_rtn")];
			$report_arr_nonOrder[$row[csf('buyer_name')]]['issue'] += $row[csf("issue")];
			$report_arr_nonOrder[$row[csf('buyer_name')]]['rcv_return'] += $row[csf("rcv_rtn")];//$transfer_issue;
			//$report_arr_nonOrder[$row[csf('buyer_name')]]['total_issue'] = $tot_issue;
            $report_arr_nonOrder[$row[csf('buyer_name')]]['total_issue'] += $row[csf("issue")]+$row[csf("transfer_issue")]+ $row[csf("rcv_rtn")];
			$report_arr_nonOrder[$row[csf('buyer_name')]]['closingStock'] += $closingStock;

			/*$buyers_id="";
			if($buyerTrans[$row[csf('trans_id')]]>0)
			{
				$buyers_id=$buyerTrans[$row[csf('trans_id')]];
			}
			else
			{
				$buyers_id=$buyerTransNonorder[$row[csf('trans_id')]];
			}
			if($buyers_id)
			{

			}
			else{
				echo $row[csf('trans_id')]."=".$buyerTrans[$row[csf('trans_id')]]."=".$buyers_id;die;
				//$trans_non_buyer.=$row[csf('trans_id')].",";
			}*/
		}

		$sql_non_order="Select p.id as prod_id, p.item_description, p.item_group_id, p.current_stock, p.avg_rate_per_unit, p.company_id, b.buyer_id as buyer_name,
		sum(case when a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as rcv_total_opening,
		0 as opening_issue_qtny,
		0 as opening_transfer_rcv,
		0 as opening_transfer_issue,
		0 as opening_issue_rtn,
		0 as opening_rcv_rtn,
		sum(case when a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as receive,
		0 as issue,
		0 as transfer_rcv,
		0 as transfer_issue,
		0 as issue_rtn,
		0 as rcv_rtn,
		1 as type
		from product_details_master p, inv_transaction a, inv_receive_master b
		where p.id=a.prod_id and a.mst_id=b.id and b.entry_form in(2,22) and b.booking_without_order=1 and a.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and p.item_category_id=$cbo_item_category_id and a.item_category=$cbo_item_category_id and p.company_id=$cbo_company_name $store_cond $item_account
		group by p.id, p.item_description, p.item_group_id, p.current_stock, p.avg_rate_per_unit, p.company_id, b.buyer_id

		union all

		Select p.id as prod_id, p.item_description, p.item_group_id, p.current_stock, p.avg_rate_per_unit, p.company_id, c.buyer_id as buyer_name,
		0 as rcv_total_opening,
		sum(case when a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as opening_issue_qtny,
		0 as opening_transfer_rcv,
		0 as opening_transfer_issue,
		0 as opening_issue_rtn,
		0 as opening_rcv_rtn,
		0 as receive,
		sum(case when a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as issue,
		0 as transfer_rcv,
		0 as transfer_issue,
		0 as issue_rtn,
		0 as rcv_rtn,
		2 as type
		from product_details_master p, inv_transaction a, inv_issue_master b, wo_non_ord_samp_booking_mst c
		where p.id=a.prod_id and a.mst_id=b.id and b.booking_id=c.id and b.entry_form=16 and b.issue_basis=1 and b.issue_purpose=8 and a.transaction_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and p.item_category_id=$cbo_item_category_id and a.item_category=$cbo_item_category_id and p.company_id=$cbo_company_name $store_cond $item_account
		group by p.id, p.item_description, p.item_group_id, p.current_stock, p.avg_rate_per_unit, p.company_id, c.buyer_id

		union all

		Select p.id as prod_id, p.item_description, p.item_group_id, p.current_stock, p.avg_rate_per_unit, p.company_id, c.buyer_id as buyer_name,
		0 as rcv_total_opening,
		0 as opening_issue_qtny,
		sum(case when a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as opening_transfer_rcv,
		0 as opening_transfer_issue,
		0 as opening_issue_rtn,
		0 as opening_rcv_rtn,
		0 as receive,
		0 as issue,
		sum(case when a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as transfer_rcv,
		0 as transfer_issue,
		0 as issue_rtn,
		0 as rcv_rtn,
		3 as type
		from product_details_master p, inv_transaction a, inv_item_transfer_mst b, wo_non_ord_samp_booking_mst c
		where p.id=a.prod_id and a.mst_id=b.id and b.to_order_id=c.id and b.entry_form=80 and a.transaction_type=5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and p.item_category_id=$cbo_item_category_id and a.item_category=$cbo_item_category_id and p.company_id=$cbo_company_name $store_cond $item_account
		group by p.id, p.item_description, p.item_group_id, p.current_stock, p.avg_rate_per_unit, p.company_id, c.buyer_id

		union all

		Select p.id as prod_id, p.item_description, p.item_group_id, p.current_stock, p.avg_rate_per_unit, p.company_id, c.buyer_id as buyer_name,
		0 as rcv_total_opening,
		0 as opening_issue_qtny,
		0 as opening_transfer_rcv,
		sum(case when a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as opening_transfer_issue,
		0 as opening_issue_rtn,
		0 as opening_rcv_rtn,
		0 as receive,
		0 as issue,
		0 as transfer_rcv,
		sum(case when a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as transfer_issue,
		0 as issue_rtn,
		0 as rcv_rtn,
		4 as type
		from product_details_master p, inv_transaction a, inv_item_transfer_mst b, wo_non_ord_samp_booking_mst c
		where p.id=a.prod_id and a.mst_id=b.id and b.from_order_id=c.id and b.entry_form=81 and a.transaction_type=6 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and p.item_category_id=$cbo_item_category_id and a.item_category=$cbo_item_category_id and p.company_id=$cbo_company_name $store_cond $item_account
		group by p.id, p.item_description, p.item_group_id, p.current_stock, p.avg_rate_per_unit, p.company_id, c.buyer_id

		union all

		Select p.id as prod_id, p.item_description, p.item_group_id, p.current_stock, p.avg_rate_per_unit, p.company_id, c.buyer_id as buyer_name,
		0 as rcv_total_opening,
		0 as opening_issue_qtny,
		0 as opening_transfer_rcv,
		0 as opening_transfer_issue,
		sum(case when a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as opening_issue_rtn,
		0 as opening_rcv_rtn,
		0 as receive,
		0 as issue,
		0 as transfer_rcv,
		0 as transfer_issue,
		sum(case when a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as issue_rtn,
		0 as rcv_rtn,
		5 as type
		from product_details_master p, inv_transaction a, inv_receive_master b, wo_non_ord_samp_booking_mst c
		where p.id=a.prod_id and a.mst_id=b.id and b.booking_id=c.id and b.entry_form in(51) and b.receive_basis=1 and b.receive_purpose=8 and a.transaction_type=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and p.item_category_id=$cbo_item_category_id and a.item_category=$cbo_item_category_id and p.company_id=$cbo_company_name $store_cond $item_account
		group by p.id, p.item_description, p.item_group_id, p.current_stock, p.avg_rate_per_unit, p.company_id, c.buyer_id

		union all

		Select p.id as prod_id, p.item_description, p.item_group_id, p.current_stock, p.avg_rate_per_unit, p.company_id, c.buyer_id as buyer_name,
		0 as rcv_total_opening,
		0 as opening_issue_qtny,
		0 as opening_transfer_rcv,
		0 as opening_transfer_issue,
		0 as opening_issue_rtn,
		sum(case when a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as opening_rcv_rtn,
		0 as receive,
		0 as issue,
		0 as transfer_rcv,
		0 as transfer_issue,
		0 as issue_rtn,
		sum(case when a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as rcv_rtn,
		6 as type
		from product_details_master p, inv_transaction a, inv_issue_master b, inv_receive_master c
		where p.id=a.prod_id and a.mst_id=b.id and b.received_id=c.id and b.entry_form=45 and c.booking_without_order=1 and a.transaction_type=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and p.item_category_id=$cbo_item_category_id and a.item_category=$cbo_item_category_id and p.company_id=$cbo_company_name $store_cond $item_account
		group by p.id, p.item_description, p.item_group_id, p.current_stock, p.avg_rate_per_unit, p.company_id, c.buyer_id";	

		//echo $sql_non_order;die;


		$result_non_order = sql_select($sql_non_order);
		//var_dump($result_non_order);
		$count=0;
		foreach($result_non_order as $row)
		{
			$non_order_opening_bal = $row[csf("rcv_total_opening")]-$row[csf("opening_issue_qtny")];
			//$openingBalanceValue = $opening_bal*$recv_rate_arr[$row[csf("prod_id")]];

			//$receive_rtn = $row[csf("receive_rtn")];
			//$issue_rtn = $row[csf("issue_rtn")];

			//$transfer_in = $row[csf("transfer_in")];
			//$transfer_out = $row[csf("transfer_out")];

			$tot_receive += $row[csf("receive")]+$row[csf("transfer_rcv")]+$issue_rtn;
			$tot_issue += $row[csf("issue")]+$row[csf("transfer_issue")]+$receive_rtn;
			//$t_receive=$row[csf("receive")];//($row[csf("receive")]+$row[csf("issue_return")]+$row[csf("receive_transfer")]);
			//$t_issue=$row[csf("issue")];//($row[csf("issue")];+$row[csf("receive_return")]+$row[csf("issue_transfer")]);
			//$c_stock=($opening_bal+($row[csf("receive")])-($row[csf("issue")]));
			$closingStock = $non_order_opening_bal+$row[csf("receive")]-$row[csf("issue")];
			//$closingAmout = $openingBalanceValue+$receive-$issue;

			//$report_arr_nonOrder[$row[csf('buyer_name')]]['opening'] += $openingBalanceValue;
			$report_arr_nonOrder[$row[csf('buyer_name')]]['opening_qnty'] += $non_order_opening_bal;
			$report_arr_nonOrder[$row[csf('buyer_name')]]['receive'] += $row[csf("receive")];
			$report_arr_nonOrder[$row[csf('buyer_name')]]['issue_return'] += $row[csf("issue_rtn")];
			$report_arr_nonOrder[$row[csf('buyer_name')]]['trans_in'] += $row[csf("transfer_rcv")];
			$report_arr_nonOrder[$row[csf('buyer_name')]]['trans_out'] += $row[csf("transfer_issue")];
			//$report_arr_nonOrder[$row[csf('buyer_name')]]['total_rcv'] += $tot_receive;
            $report_arr_nonOrder[$row[csf('buyer_name')]]['total_rcv'] += $row[csf("receive")];
			$report_arr_nonOrder[$row[csf('buyer_name')]]['issue'] += $row[csf("issue")];
			$report_arr_nonOrder[$row[csf('buyer_name')]]['rcv_return'] += $row[csf("rcv_rtn")];//$transfer_issue;
			$report_arr_nonOrder[$row[csf('buyer_name')]]['total_issue'] = $tot_issue;
			$report_arr_nonOrder[$row[csf('buyer_name')]]['closingStock'] += $closingStock;

			/*$buyers_id="";
			if($buyerTrans[$row[csf('trans_id')]]>0)
			{
				$buyers_id=$buyerTrans[$row[csf('trans_id')]];
			}
			else
			{
				$buyers_id=$buyerTransNonorder[$row[csf('trans_id')]];
			}
			if($buyers_id)
			{

			}
			else{
				echo $row[csf('trans_id')]."=".$buyerTrans[$row[csf('trans_id')]]."=".$buyers_id;die;
				//$trans_non_buyer.=$row[csf('trans_id')].",";
			}*/
		}
        // ===========================Non order query end =============================

		//print_r($trans_id_arr) ;
		$div_width=1720;
		$table_width=1700;
		?>
        <style type="text/css">
            .wrd_brk{word-break: break-all;}
            .center{text-align: center;}
            .right{text-align: right;}
            .left{text-align: left;}
        </style>

        <div id="scroll_body">
    		<div id="order_wise_stock_container" style="width: <? echo $div_width; ?>px; padding: 10px 0;" class="order_basis">
    			<table cellspacing=0 width="<? echo $table_width; ?>" border="1" cellpadding="0" rules="all" id="caption">
    				<thead>
    					<tr class="form_caption">
    						<th colspan="13" align="center"><h1 style="font-size: 18px;">Order wise <? echo $report_title; ?> Report</h1></th>
    					</tr>
    					<tr class="form_caption" style="border:none;">
    						<th colspan="13" align="center"><h1 style="font-size: 16px;">Comapny Name: <? echo $companyArr[$cbo_company_name]; ?></h4></th>
    					</tr>
    					<tr class="form_caption" style="border:none;">
    						<th colspan="13" align="center"><strong>From : <? echo $from_date; ?> To <? echo $to_date; ?></strong></th>
    					</tr>
    				</thead>
    			</table>
    			<table cellspacing=0 width="<? echo $table_width; ?>" border="1" cellpadding="0" rules="all"  class="rpt_table">
    				<thead>
    					<tr class="form_caption">
    						<th width="40" rowspan="2" class="wrd_brk center">SL</th>
    						<th width="150" rowspan="2" class="wrd_brk center">Buyer Name</th>
    						<th width="180" rowspan="2" class="wrd_brk center">Opening Stock</th>
    						<th colspan="4" width="480" class="wrd_brk center">Received</th>
    						<th colspan="4" width="480" class="wrd_brk center">Issue</th>
    						<th width="150" rowspan="2" align="center" class="wrd_brk center">Closing Stock</th>
    						<th rowspan="2" width="200" class="wrd_brk center">Remarks</th>
    					</tr>
    					<tr>
    						<th width="120" class="wrd_brk center">Receive</th>
    						<th width="120" class="wrd_brk center">Issue Return</th>
    						<th width="120" class="wrd_brk center">Transfer In</th>
    						<th width="120" class="wrd_brk center">Total Receive</th>
    						<th width="120" class="wrd_brk center">Issue</th>
    						<th width="120" class="wrd_brk center">Receive Return</th>
    						<th width="120" class="wrd_brk center">Transfer Out</th>
    						<th width="120" class="wrd_brk center">Total Issue</th>
    					</tr>
    				</thead>
    			</table>
    			<table id="table_body" width="<? echo $table_width; ?>" border="1" cellspacing="0" cellpadding="0" class="rpt_table" rules="all">
                    <tbody>
    				<?
                    $tot_opening_value_order = 0; $tot_trans_type_receive_order = 0; $tot_trans_type_iss_return_order = 0; $tot_trans_in_order = 0; $tot_rcv_total_order= 0; $tot_trans_type_issue_order = 0;$tot_trans_type_rcv_return_order = 0; $tot_trans_type_trans_out_order = 0;
                    $tot_issue_total_order = 0; $tot_closing_value_order = 0;
    				$sl = 1;
    				foreach($report_arr as $buyer_id => $trans_type)
    				{
    					$opening_value  = $trans_type["opening"];
    					if($cbo_store_name > 0)
    					{
    						$trans_in = $trans_type["trans_in"] -  $trans_store_arr[$buyer_id]["tr_in"];
    						$trans_out = $trans_type["trans_out"] - $trans_store_arr[$buyer_id]["tr_out"];
    						$store_trans_in = $trans_store_arr[$buyer_id]["tr_in"];
    						$store_trans_out = $trans_store_arr[$buyer_id]["tr_out"];
    					}else{
    						$trans_in = $trans_type["trans_in"];
    						$trans_out = $trans_type["trans_out"];
    						$store_trans_in = 0;
    						$store_trans_out = 0;
    					}
    					$rcv_total =  $trans_type["receive"] + $trans_type["iss_return"] + $trans_in + $store_trans_in;
    					$issue_total = $trans_type["issue"] + $trans_type["rcv_return"] + $trans_out + $store_trans_out;
    					$closing_value = $opening_value + $rcv_total - $issue_total;
    					if($sl%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
    					/*if($closing_value>0 || $opening_value>0 || $rcv_total>0 || $issue_total>0)
    					{*/
	    					if($cbo_value_with==1)
	    					{
	    						if($closing_value>=1 || $opening_value>=1 || $rcv_total>=1 || $issue_total>=1)
	    						{
	                                ?>
	                                <tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onclick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
	                                    <td width="40" class="wrd_brk center"><? echo $sl; ?></td>
	                                    <td width="150" class="wrd_brk left" title="<? echo 'Order ID='.$trans_type['order_id'].', is_sales='.$trans_type["is_sales"]; ?>"><? 
	                                    if ($trans_type["is_sales"]==1) 
	                                    {
	                                        if($sales_data_arr[$trans_type["order_id"]]['within_group']==2)
	                                        {
	                                            echo $buyer_arr[$sales_data_arr[$trans_type["order_id"]]['buyer_id']];
	                                        }
	                                        else
	                                        {
	                                            echo $companyArr[$sales_data_arr[$trans_type["order_id"]]['buyer_id']];
	                                        }                                        
	                                    }
	                                    else
	                                    {
	                                        echo $buyer_arr[$buyer_id];
	                                    }                                    
	                                    ?></td>
	                                    <td width="180" class="wrd_brk right">
	                                    <? echo ($opening_value < 0) ? '0.00' : number_format($opening_value,2); ?>
	                                    </td>
	                                    <td width="120" class="wrd_brk right"><? echo number_format($trans_type['receive'],2); ?></td>
	                                    <td width="120" class="wrd_brk right"><? echo number_format($trans_type['iss_return'],2); ?></td>
	                                    <td width="120" class="wrd_brk right"><? echo number_format($trans_in,2); ?></td>
	                                    <td width="120" class="wrd_brk right"><? echo number_format($rcv_total,2); ?></td>
	                                    <td width="120" class="wrd_brk right"><? echo number_format($trans_type['issue'],2); ?></td>
	                                    <td width="120" class="wrd_brk right"><? echo number_format($trans_type['rcv_return'],2); ?></td>
	                                    <td width="120" class="wrd_brk right"><? echo number_format($trans_type['trans_out'],2); ?></td>
	                                    <td width="120" class="wrd_brk right"><? echo number_format($issue_total,2); ?></td>
	                                    <td width="150" class="wrd_brk right">
	                                    <? echo ($closing_value <0) ? '0.00': number_format($closing_value,2); ?>
	                                    </td>
	                                    <td width="200">&nbsp;</td>
	                                </tr>
	                                <?
	                                $tot_opening_value_order += $opening_value;
	                                $tot_trans_type_receive_order += $trans_type['receive'];
	                                $tot_trans_type_iss_return_order += $trans_type['iss_return']; 
	                                $tot_trans_in_order += $trans_in;
	                                $tot_rcv_total_order += $rcv_total;
	                                $tot_trans_type_issue_order += $trans_type['issue'];
	                                $tot_trans_type_rcv_return_order += $trans_type['rcv_return'];
	                                $tot_trans_type_trans_out_order += $trans_type['trans_out'];
	                                $tot_issue_total_order += $issue_total;
	                                $tot_closing_value_order += $closing_value;
	                                $sl++;
	    						}
	    					}
	    					else
	    					{
	    						/*if($closing_value>=0 || $opening_value>=0 || $rcv_total>=0 || $issue_total>=0)
	    						{*/
	    							?>
	    							<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onclick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
	    								<td width="40" class="wrd_brk center"><? echo $sl; ?></td>
	    								<td width="150" class="wrd_brk left" title="<? echo 'Order ID='.$trans_type['order_id'].', is_sales='.$trans_type["is_sales"]; ?>"><? 
	                                    if ($trans_type["is_sales"]==1) 
	                                    {
	                                        if($sales_data_arr[$trans_type["order_id"]]['within_group']==2)
	                                        {
	                                            echo $buyer_arr[$sales_data_arr[$trans_type["order_id"]]['buyer_id']];
	                                        }
	                                        else
	                                        {
	                                            echo $companyArr[$sales_data_arr[$trans_type["order_id"]]['buyer_id']];
	                                        }
	                                        
	                                    }
	                                    else
	                                    {
	                                        echo $buyer_arr[$buyer_id];
	                                    }
	                                     

	                                    ?></td>
	                                    <td width="180" class="wrd_brk right">
	                                    <? echo ($opening_value < 0)  ? '0.00' : number_format($opening_value,2); ?>
	                                    </td>
	                                    <td width="120" class="wrd_brk right"><? echo number_format($trans_type['receive'],2); ?></td>
	                                    <td width="120" class="wrd_brk right"><? echo number_format($trans_type['iss_return'],2); ?></td>
	                                    <td width="120" class="wrd_brk right"><? echo number_format($trans_in,2); ?></td>
	                                    <td width="120" class="wrd_brk right"><? echo number_format($rcv_total,2); ?></td>
	                                    <td width="120" class="wrd_brk right"><? echo number_format($trans_type['issue'],2); ?></td>
	                                    <td width="120" class="wrd_brk right"><? echo number_format($trans_type['rcv_return'],2); ?></td>
	                                    <td width="120" class="wrd_brk right"><? echo number_format($trans_type['trans_out'],2); ?></td>
	                                    <td width="120" class="wrd_brk right"><? echo number_format($issue_total,2); ?></td>
	                                    <td width="150" class="wrd_brk right">
	                                    <? echo ($closing_value < 0) ? '0.00': number_format($closing_value,2); ?></td>
	    								<td width="200">&nbsp;</td>
	    							</tr>
	    							<?
	                                $tot_opening_value_order += $opening_value;
	                                $tot_trans_type_receive_order += $trans_type['receive'];
	                                $tot_trans_type_iss_return_order += $trans_type['iss_return']; 
	                                $tot_trans_in_order += $trans_in;
	                                $tot_rcv_total_order += $rcv_total;
	                                $tot_trans_type_issue_order += $trans_type['issue'];
	                                $tot_trans_type_rcv_return_order += $trans_type['rcv_return'];
	                                $tot_trans_type_trans_out_order += $trans_type['trans_out'];
	                                $tot_issue_total_order += $issue_total;
	                                $tot_closing_value_order += $closing_value;
	    							$sl++;
	    						//}
	    				    }
	    				//}
    				}
    				?>
                    </tbody>
                    <tfoot>
                        <th width="190" colspan="2" class="wrd_brk right">Total:</th>
                        <th width="180" class="wrd_brk"><? echo number_format($tot_opening_value_order, 2); ?></th>
                        <th width="120" class="wrd_brk"><? echo number_format($tot_trans_type_receive_order, 2); ?></th>
                        <th width="120" class="wrd_brk"><? echo number_format($tot_trans_type_iss_return_order, 2); ?></th>
                        <th width="120" class="wrd_brk"><? echo number_format($tot_trans_in_order, 2); ?></th>
                        <th width="120" class="wrd_brk"><? echo number_format($tot_rcv_total_order, 2); ?></th>
                        <th width="120" class="wrd_brk"><? echo number_format($tot_trans_type_issue_order, 2); ?></th>
                        <th width="120" class="wrd_brk"><? echo number_format($tot_trans_type_rcv_return_order, 2); ?></th>
                        <th width="120" class="wrd_brk"><? echo number_format($tot_trans_type_trans_out_order, 2); ?></th>
                        <th width="120" class="wrd_brk"><? echo number_format($tot_issue_total_order, 2); ?></th>
                        <th width="120" class="wrd_brk"><? echo number_format($tot_closing_value_order, 2); ?></th>
                        <th width="200" class="wrd_brk">&nbsp;</th>                    
                    </tfoot>
    			</table>            
    		</div>
    		<div id="non_order_wise_stock_container" style="width: <? echo $div_width; ?>px; padding: 10px 0; margin:15px 0;" class="order_basis" >
    			<table cellspacing=0 width="<? echo $table_width; ?>" border="1" cellpadding="0" rules="all" id="caption">
    				<thead>
    					<tr class="form_caption">
    						<th colspan="13" align="center"><h1 style="font-size: 18px;">Non Order Wise <? echo $report_title; ?> Report</h1></th>
    					</tr>
    					<tr class="form_caption" style="border:none;">
    						<th colspan="13" align="center"><h1 style="font-size: 16px;">Comapny Name: <? echo $companyArr[$cbo_company_name]; ?></h4></th>
    					</tr>
    					<tr class="form_caption" style="border:none;">
    						<th colspan="13" align="center"><strong>From : <? echo $from_date; ?> To <? echo $to_date; ?></strong></th>
    					</tr>
    				</thead>
    			</table>
    			<table cellspacing=0 width="<? echo $table_width; ?>" border="1" cellpadding="0" rules="all"  class="rpt_table">
    				<thead>
    					<tr class="form_caption">
    						<th width="40" rowspan="2" class="wrd_brk center">SL</th>
    						<th width="150" rowspan="2" class="wrd_brk center">Buyer Name</th>
    						<th width="180" rowspan="2" class="wrd_brk center">Opening Stock</th>
    						<th colspan="4" class="wrd_brk center">Received</th>
    						<th colspan="4" class="wrd_brk center">Issue</th>
    						<th width="150" rowspan="2" class="wrd_brk center">Closing Stock</th>
    						<th width="200" rowspan="2" class="wrd_brk center">Remarks</th>
    					</tr>
    					<tr>
    						<th width="120" class="wrd_brk center">Receive</th>
    						<th width="120" class="wrd_brk center">Issue Return</th>
    						<th width="120" class="wrd_brk center">Transfer In</th>
    						<th width="120" class="wrd_brk center">Total Receive</th>
    						<th width="120" class="wrd_brk center">Issue</th>
    						<th width="120" class="wrd_brk center">Receive Return</th>
    						<th width="120" class="wrd_brk center">Transfer Out</th>
    						<th width="120" class="wrd_brk center">Total Issue</th>
    					</tr>
    				</thead>
    			</table>
    			<table id="table_body_non_ord" width="<? echo $table_width; ?>" border="1" cellspacing="0" cellpadding="0" class="rpt_table" rules="all">
                    <tbody>
    				<?
    				//var_dump($report_arr_nonOrder);
                    $tot_trans_type_opening_qnty = 0; $tot_trans_type_receive = 0; $tot_trans_type_issue_return = 0;
                    $tot_trans_type_trans_in = 0; $tot_trans_type_total_rcv = 0; $tot_trans_type_issue = 0;
                    $tot_trans_type_rcv_return = 0; $tot_trans_type_trans_out = 0; $tot_trans_type_total_issue = 0;
                    $tot_trans_type_closingStock = 0;
    				$sl = 1;

    				foreach($report_arr_nonOrder as $buyer_id => $trans_type)
    				{
                       if($bg%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
    					if($cbo_value_with==1)
    					{
    						if($trans_type["opening_qnty"] > 0 || $trans_type['closingStock'] > 0 || $trans_type['total_rcv']>0 || $trans_type['total_issue']>0)
    						{
    						?>
    						<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onclick="change_color('tr_non_ord<? echo $bg; ?>','<? echo $bgcolor; ?>')" id="tr_non_ord<? echo $bg; ?>">
    							<td width="40" class="wrd_brk center"><? echo $bg; ?></td>
    							<td width="150" class="wrd_brk right"><? echo $buyer_arr[$buyer_id]; ?></td>
    							<td width="180" class="wrd_brk right">
                                <? echo ($trans_type["opening_qnty"]<0) ? '0.00' : number_format($trans_type["opening_qnty"],2); ?>
                                </td>
    							<td width="120" class="wrd_brk right"><? echo number_format($trans_type['receive'],2); ?></td>
    							<td width="120" class="wrd_brk right"><? echo number_format($trans_type['issue_return'],2); ?></td>
    							<td width="120" class="wrd_brk right"><? echo number_format($trans_type['trans_in'],2); ?></td>
    							<td width="120" class="wrd_brk right"><? echo number_format($trans_type['total_rcv'],2); ?></td>
    							<td width="120" class="wrd_brk right"><? echo number_format($trans_type['issue'],2); ?></td>
    							<td width="120" class="wrd_brk right"><? echo number_format($trans_type['rcv_return'],2); ?></td>
    							<td width="120" class="wrd_brk right"><? echo number_format($trans_type['trans_out'],2); ?></td>
    							<td width="120" class="wrd_brk right"><? echo number_format($trans_type['total_issue'],2); ?></td>
    							<td width="150" class="wrd_brk right">
                                <? echo ($trans_type['closingStock'] < 0) ? '0.00': number_format($trans_type['closingStock'],2); ?>
                                </td>
    							<td width="200">&nbsp;</td>
    						</tr>
    						<?
    						}
    					}else{
    						?>
    						<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onclick="change_color('tr_non_ord<? echo $bg; ?>','<? echo $bgcolor; ?>')" id="tr_non_ord<? echo $bg; ?>">
    							<td width="40" class=" wrd_brk center"><? echo $bg; ?></td>
    							<td width="150" class=" wrd_brk right"><? echo $buyer_arr[$buyer_id]; ?></td>
    							<td width="180" class=" wrd_brk right">
                                <? echo ($trans_type["opening_qnty"] < 0) ? '0.00': number_format($trans_type["opening_qnty"],2); ?></td>
    							<td width="120" class=" wrd_brk right"><? echo number_format($trans_type['receive'],2); ?></td>
    							<td width="120" class=" wrd_brk right"><? echo number_format($trans_type['issue_return'],2); ?></td>
    							<td width="120" class=" wrd_brk right"><? echo number_format($trans_type['trans_in'],2); ?></td>
    							<td width="120" class=" wrd_brk right"><? echo number_format($trans_type['total_rcv'],2); ?></td>
    							<td width="120" class=" wrd_brk right"><? echo number_format($trans_type['issue'],2); ?></td>
    							<td width="120" class=" wrd_brk right"><? echo number_format($trans_type['rcv_return'],2); ?></td>
    							<td width="120" class=" wrd_brk right"><? echo number_format($trans_type['trans_out'],2); ?></td>
    							<td width="120" class=" wrd_brk right"><? echo number_format($trans_type['total_issue'],2); ?></td>
    							<td width="150" class=" wrd_brk right">
                                <? echo ($trans_type['closingStock'] < 0) ? '0.00' : number_format($trans_type['closingStock'],2); ?>
                                </td>
    							<td width="200">&nbsp;</td>
    						</tr>
    						<?
    					}
                        $tot_trans_type_opening_qnty += $trans_type["opening_qnty"];
                        $tot_trans_type_receive += $trans_type["receive"];
                        $tot_trans_type_issue_return += $trans_type["issue_return"];
                        $tot_trans_type_trans_in += $trans_type["trans_in"];
                        $tot_trans_type_total_rcv += $trans_type["total_rcv"];
                        $tot_trans_type_issue += $trans_type["issue"];
                        $tot_trans_type_rcv_return += $trans_type["rcv_return"];
                        $tot_trans_type_trans_out += $trans_type["trans_out"];
                        $tot_trans_type_total_issue += $trans_type["total_issue"];
                        $tot_trans_type_closingStock += $trans_type["closingStock"];
        				$bg++;
    				}

    					?>
                    </tbody>
                    <tfoot>
                        <th colspan="2" width="190" class="wrd_brk right">Total:</th>
                        <th width="180" class="wrd_brk"><? echo number_format($tot_trans_type_opening_qnty, 2); ?></th>
                        <th width="120" class="wrd_brk"><? echo number_format($tot_trans_type_receive, 2); ?></th>
                        <th width="120" class="wrd_brk"><? echo number_format($tot_trans_type_issue_return, 2); ?></th>
                        <th width="120" class="wrd_brk"><? echo number_format($tot_trans_type_trans_in, 2); ?></th>
                        <th width="120" class="wrd_brk"><? echo number_format($tot_trans_type_total_rcv, 2); ?></th>
                        <th width="120" class="wrd_brk"><? echo number_format($tot_trans_type_issue, 2); ?></th>
                        <th width="120" class="wrd_brk"><? echo number_format($tot_trans_type_rcv_return, 2); ?></th>
                        <th width="120" class="wrd_brk"><? echo number_format($tot_trans_type_trans_out, 2); ?></th>
                        <th width="120" class="wrd_brk"><? echo number_format($tot_trans_type_total_issue, 2); ?></th>
                        <th width="150" class="wrd_brk"><? echo number_format($tot_trans_type_closingStock, 2); ?></th>
                        <th width="200" class="wrd_brk">&nbsp;</th>                    
                    </tfoot>    
    			</table>            
    		</div>
            <div style="width: <? echo $div_width; ?>px;">
                <table cellspacing=0 width="<? echo $table_width; ?>" cellpadding="0" border="1" rules="all"  class="rpt_table" >
                    <tfoot>
                        <th colspan="2" width="190" class="right">Grand Total:</th>
                        <th width="180" class="wrd_brk"><? echo number_format(($tot_opening_value_order+$tot_trans_type_opening_qnty), 2); ?></th>
                        <th width="120" class="wrd_brk"><? echo number_format(($tot_trans_type_receive_order+$tot_trans_type_receive), 2); ?></th>
                        <th width="120" class="wrd_brk"><? echo number_format(($tot_trans_type_iss_return_order+$tot_trans_type_issue_return), 2); ?></th>
                        <th width="120" class="wrd_brk"><? echo number_format(($tot_trans_in_order+$tot_trans_type_trans_in), 2); ?></th>
                        <th width="120" class="wrd_brk"><? echo number_format(($tot_rcv_total_order+$tot_trans_type_total_rcv), 2); ?></th>
                        <th width="120" class="wrd_brk"><? echo number_format(($tot_trans_type_issue_order+$tot_trans_type_issue), 2); ?></th>
                        <th width="120" class="wrd_brk"><? echo number_format(($tot_trans_type_rcv_return_order+$tot_trans_type_rcv_return), 2); ?></th>
                        <th width="120" class="wrd_brk"><? echo number_format(($tot_trans_type_trans_out_order+$tot_trans_type_trans_out), 2); ?></th>
                        <th width="120" class="wrd_brk"><? echo number_format(($tot_issue_total_order+$tot_trans_type_total_issue), 2); ?></th>
                        <th width="150" class="wrd_brk"><? echo number_format(($tot_closing_value_order+$tot_trans_type_closingStock), 2); ?></th>
                        <th width="200" class="wrd_brk">&nbsp;</th>                    
                    </tfoot>
                </table>
            </div>
        </div>
        
		<?
	}
    else if($report_type == 6)
    {
        $data_array=array();
        $colspan = 17;
        $tableWidth = 1541;

        $sql_order="SELECT p.id as prod_id, p.item_description, p.item_group_id, p.current_stock, p.avg_rate_per_unit, p.company_id, d.buyer_id, b.id as prop_id, b.is_sales,b.po_breakdown_id,p.detarmination_id, p.gsm, p.dia_width,
        (case when b.trans_type in(1,4,5) and a.transaction_date<'".$from_date."' then b.quantity else 0 end) as opening_rcv_qnty,
        (case when b.trans_type in(2,3,6) and a.transaction_date<'".$from_date."' then b.quantity else 0 end) as opening_issue_qtny,
        (case when b.trans_type in(1,4,5) and a.transaction_date between '".$from_date."' and '".$to_date."' then b.quantity else 0 end) as purchase_quantity,
        (case when b.trans_type in(2,3,6) and a.transaction_date between '".$from_date."' and '".$to_date."' then b.quantity else 0 end) as issue_quantity,
        (case when b.trans_type in(1) and a.transaction_date between '".$from_date."' and '".$to_date."' then b.quantity else 0 end) as receive,
        (case when b.trans_type in(4) and a.transaction_date between '".$from_date."' and '".$to_date."' then b.quantity else 0 end) as issue_return,
        (case when b.trans_type in(2) and a.transaction_date between '".$from_date."' and '".$to_date."' then b.quantity else 0 end) as issue,
        (case when b.trans_type in(3) and a.transaction_date between '".$from_date."' and '".$to_date."' then b.quantity else 0 end) as rec_return,
        (case when b.trans_type in(5) and a.transaction_date between '".$from_date."' and '".$to_date."' then b.quantity else 0 end) as transfer_in,
        (case when b.trans_type in(6) and a.transaction_date between '".$from_date."' and '".$to_date."' then b.quantity else 0 end) as transfer_out,

        (case when b.trans_type in (1,4,5) and a.transaction_date<'".$from_date."' then a.cons_amount else 0 end) as total_rcv_value_opening,
        (case when b.trans_type in (2,3,6) and a.transaction_date<'".$from_date."' then a.cons_amount else 0 end) as total_issue_value_opening,
        (case when b.trans_type in (1,4,5) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as total_rcv_value,
        (case when b.trans_type in (2,3,6) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as total_issue_value


        from product_details_master p, inv_transaction a, order_wise_pro_details b, fabric_sales_order_dtls c, fabric_sales_order_mst d
        where p.id=a.prod_id and a.id=b.trans_id and b.po_breakdown_id=c.id and c.mst_id=d.id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and p.item_category_id in ($cbo_item_category_id) and a.item_category in ($cbo_item_category_id) and b.entry_form in (550) and p.company_id=$cbo_company_name $store_cond1 $item_account";


        //echo $sql_order;die;

           
        $trnasactionData=sql_select($sql_order);

        $prod_ids="";
        foreach($trnasactionData as $row)
        {
            $prod_ids.=$row[csf("prod_id")].',';
            //$data_array[$row[csf("prod_id")]]['rcv_total_opening']=$row[csf("rcv_total_opening")];
            //$data_array[$row[csf("prod_id")]]['iss_total_opening']=$row[csf("iss_total_opening")];
            //$data_array[$row[csf("prod_id")]]['rcv_return_opening']=$row[csf("rcv_return_opening")];
            //$data_array[$row[csf("prod_id")]]['iss_return_opening']=$row[csf("iss_return_opening")];

            $data_array[$row[csf("prod_id")]]['opening_rcv_qnty']+=$row[csf("opening_rcv_qnty")];
            $data_array[$row[csf("prod_id")]]['opening_issue_qtny']+=$row[csf("opening_issue_qtny")];

            
                //$opening_stock=$row[csf("opening_rcv_qnty")]-$row[csf("opening_issue_qtny")];
               // $closingStock = $opening_stock+$purchase_quantity-$issue_quantity;

            $data_array[$row[csf("prod_id")]]['receive']+=$row[csf("receive")];
            $data_array[$row[csf("prod_id")]]['issue']+=$row[csf("issue")];
            $data_array[$row[csf("prod_id")]]['rec_return']+=$row[csf("rec_return")];
            $data_array[$row[csf("prod_id")]]['issue_return']+=$row[csf("issue_return")];
            $data_array[$row[csf("prod_id")]]['transfer_in']+=$row[csf("transfer_in")];
            $data_array[$row[csf("prod_id")]]['transfer_out']+=$row[csf("transfer_out")];
            //$data_array[$row[csf("prod_id")]]['transfer_in_opening']=$row[csf("transfer_in_opening")];
            //$data_array[$row[csf("prod_id")]]['transfer_out_opening']=$row[csf("transfer_out_opening")];
            $data_array[$row[csf("prod_id")]]['total_rcv_value_opening']+=$row[csf("total_rcv_value_opening")];
            $data_array[$row[csf("prod_id")]]['total_issue_value_opening']+=$row[csf("total_issue_value_opening")];
            $data_array[$row[csf("prod_id")]]['total_rcv_value']+=$row[csf("total_rcv_value")];
            $data_array[$row[csf("prod_id")]]['total_issue_value']+=$row[csf("total_issue_value")];

            $data_array[$row[csf("prod_id")]]['detarmination_id']=$row[csf("detarmination_id")];
            $data_array[$row[csf("prod_id")]]['gsm']=$row[csf("gsm")];
            $data_array[$row[csf("prod_id")]]['dia_width']=$row[csf("dia_width")];
            //$transfer_outQnty[$row[csf("prod_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]]['tranfOutQnty']=$row[csf("transfer_out")];
        }
        $prod_ids =chop($prod_ids,",");
        $date_array=array();
        $dateRes_date="select prod_id, min(transaction_date) as min_date, max(transaction_date) as max_date from inv_transaction where is_deleted=0 and status_active=1 and item_category=13 group by prod_id";
        $result_dateRes_date = sql_select($dateRes_date);
        foreach($result_dateRes_date as $row)
        {
            $date_array[$row[csf("prod_id")]]['min_date']=$row[csf("min_date")];
            $date_array[$row[csf("prod_id")]]['max_date']=$row[csf("max_date")];
        }

       


        ?>
        <div>
            <table style="width:<? echo $tableWidth;?>px" border="1" cellpadding="2" cellspacing="0"  id="caption" rules="all">
                <thead>
                    <tr class="form_caption" style="border:none;">
                        <td colspan="<? echo $colspan;?>" align="center" style="border:none;font-size:16px; font-weight:bold" >Grey Fabric <? echo $report_title; ?></td>
                    </tr>
                    <tr class="form_caption" style="border:none;">
                        <td colspan="<? echo $colspan;?>" align="center" style="border:none; font-size:14px;">
                           <b>Company Name : <? echo $companyArr[$cbo_company_name]; ?></b>
                        </td>
                    </tr>
                    <tr class="form_caption" style="border:none;">
                        <td colspan="<? echo $colspan;?>" align="center" style="border:none;font-size:12px; font-weight:bold">
                            <? if($from_date!="" || $to_date!="") echo "From : ".change_date_format($from_date)." To : ".change_date_format($to_date)."" ;?>
                        </td>
                    </tr>
                </thead>
            </table>
            <table width="<? echo $tableWidth;?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                <thead>
                    <tr>
                        <th style="word-break: break-all;" rowspan="2" width="40">SL</th>
                        <th style="word-break: break-all;" rowspan="2" width="60">Prod.ID</th>
                        <th style="word-break: break-all;" colspan="4">Description</th>
                        
                        <th style="word-break: break-all;" rowspan="2" width="110">Opening Stock</th>
                       
                        <th style="word-break: break-all;" colspan="4">Receive</th>
                       
                        <th style="word-break: break-all;" colspan="4">Issue</th>

                        <th style="word-break: break-all;" rowspan="2" width="100">Closing Stock</th>
                       
                        <th style="word-break: break-all;" rowspan="2">DOH</th>
                    </tr>
                    <tr>
                        <th style="word-break: break-all;"  width="120">Construction</th>
                        <th style="word-break: break-all;"  width="180">Composition</th>
                        <th style="word-break: break-all;"  width="70">GSM</th>
                        <th style="word-break: break-all;"  width="100">Dia/Width</th>

                        <th style="word-break: break-all;"  width="80">Receive</th>
                        <th style="word-break: break-all;"   width ="80">Issue Return</th>
                        <th style="word-break: break-all;" width="80">Transfer In</th>
                        <th style="word-break: break-all;" width="100">Total Receive</th>

                        <th style="word-break: break-all;" width="80">Issue</th>
                        <th style="word-break: break-all;" width="80">Receive Return</th>
                        <th style="word-break: break-all;" width="80">Transfer Out</th>
                        <th style="word-break: break-all;" width="100">Total Issue</th>
                    </tr>
                </thead>
            </table>
            <div style="width:<? echo $tableWidth + 19?>px; max-height:350px; overflow-y:scroll" id="scroll_body" >
                <table width="<? echo $tableWidth;?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
                <?
                    $composition_arr=array(); $i=1;
                    $sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
                    $deterdata_array=sql_select($sql_deter);
                    if(count($deterdata_array)>0)
                    {
                        foreach( $deterdata_array as $row )
                        {
                            if(array_key_exists($row[csf('id')],$composition_arr))
                            {
                                $composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
                            }
                            else
                            {
                                $composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
                            }
                        }
                    }
                    //$trans = return_library_array("select  prod_id, sum(quantity) as qnty from order_wise_pro_details where entry_form in(2,22,58) and trans_id!=0 and trans_type=1 and status_active=1 and is_deleted=0 group by prod_id order by prod_id","prod_id","qnty");
                    //print_r($data_array);
                    foreach($data_array as $prod_id=> $row)
                    {
                        if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";

                        //$total_value_opening=$data_array[$row[csf("id")]]['total_rcv_value_opening'] - $data_array[$row[csf("id")]]['total_issue_value_opening'];
                        //$opening_rcv= ($row['rcv_total_opening']+$row['iss_return_opening']+$row['transfer_in_opening']);
                        //$opening_issue = $row['iss_total_opening']+$row['rcv_return_opening']+$row['transfer_out_opening'];
                        
                        $opening_rcv= $row['opening_rcv_qnty'];
                        $opening_issue = $row['opening_issue_qtny'];

                        $opening= $opening_rcv - $opening_issue;

                        $opening_rate = $total_value_opening = 0;
                        if($opening_rcv > 0){
                        $opening_rate = $row['total_rcv_value_opening']/ $opening_rcv;
                        }
                        $total_value_opening = $opening *$opening_rate;
                        //$opening_rate = $total_value_opening/$opening;

                        $receive = $row['receive'];
                        $issue_return = $row['issue_return'];
                        $transfer_in = $row['transfer_in'];
                        $totalReceive=$receive+$issue_return+$transfer_in;

                        $total_rcv_value = $row['total_rcv_value'];

                        $issue = $row['issue'];
                        $rec_return = $row['rec_return'];
                        $transfer_out = $row['transfer_out'];
                        $totalIssue=$issue+$rec_return+$transfer_out;
                        $total_issue_value=$row['total_issue_value'];

                        $closingStock=$opening+$totalReceive-$totalIssue;
                        //echo "<pre>".$get_upto_qnty.'='.$txt_qnty." = ".$closingStock."</pre>";
                        //$get_upto=array(1=>"Greater Than",2=>"Less Than",3=>"Greater/Equal",4=>"Less/Equal",5=>"Equal");
                        if (
                            ($get_upto_qnty==1 && $txt_qnty<$closingStock) ||
                            ($get_upto_qnty==2 && $txt_qnty>$closingStock) ||
                            ($get_upto_qnty==3 && $txt_qnty<=$closingStock) ||
                            ($get_upto_qnty==4 && $txt_qnty>=$closingStock) ||
                            ($get_upto_qnty==5 && $txt_qnty==$closingStock) ||
                            ($get_upto_qnty==0)
                            )  
                        {
                            $closingValue= $total_value_opening+ $total_rcv_value - $total_issue_value;
                            $closingRate = $closingValue/$closingStock;

                            $totalStockValue+=$closingStock;
                            $tot_closingValue+=$closingValue;
                            $tot_total_value_opening+=$total_value_opening;
                            $tot_total_rcv_value+=$total_rcv_value;
                            $tot_total_issue_value+=$total_issue_value;

                            $tot_opening+=$opening;
                            $tot_transfer_in+=$transfer_in;
                            $tot_transfer_out+=$transfer_out;
                            $total_receive+=$totalReceive;
                            $tot_issue+=$totalIssue;
                            $tot_issue_return+=$issue_return;
                            $tot_rec_return+=$rec_return;


                            //$opening==$total_value_opening==$receive==$issue_return==$transfer_in==$totalReceive==$total_rcv_value==$issue==$rec_return==$transfer_out==$totalIssue
                            if(($cbo_value_with ==1 && (number_format($opening,2,'.','')!= 0 || number_format($total_value_opening,2,'.','')!=0 || number_format($receive,2,'.','')!= 0 ||number_format($issue_return,2,'.','')!=0 || number_format($transfer_in,2,'.','')!= 0 || number_format($totalReceive,2,'.','')!=0 || number_format($total_rcv_value,2,'.','')!= 0 || number_format($issue,2,'.','')!=0 || number_format($rec_return,2,'.','')!=0 || number_format($transfer_out,2,'.','')!= 0 || number_format($totalIssue,2,'.','')!=0) ) || ($cbo_value_with ==0 && (number_format($opening,2,'.','')== 0 || number_format($total_value_opening,2,'.','')==0 || number_format($receive,2,'.','')== 0 ||number_format($issue_return,2,'.','')==0 || number_format($transfer_in,2,'.','')== 0 || number_format($totalReceive,2,'.','')==0 || number_format($total_rcv_value,2,'.','')== 0 || number_format($issue,2,'.','')==0 || number_format($rec_return,2,'.','')==0 || number_format($transfer_out,2,'.','')== 0 || number_format($totalIssue,2,'.','')==0)))
                            {
                                /*if($opening>0 || $totalReceive>0 || $totalIssue>0)
                                { 
                                    if($opening>=0 || $totalReceive>=0 || $totalIssue>=0)
                                    {*/
                                        ?>
                                        <tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                            <td width="40"><? echo $i; ?></td>
                                            <td width="60" align="center" style="word-break: break-all;"><p><? echo $prod_id; ?></p></td>
                                            <td width="120" style="word-break: break-all;"><? echo $determinaArr[$row["detarmination_id"]]; ?></td>
                                            <td width="180" style="word-break: break-all;"><p><? echo $composition_arr[$row["detarmination_id"]]; ?></p></td>
                                            <td width="70" style="word-break: break-all;"><p><? echo $row["gsm"]; ?></p></td>
                                            <td width="100" style="word-break: break-all;"><p><? echo $row["dia_width"]; ?></p></td>
                                           
                                            <td width="110" align="right" style="word-break: break-all;"><p><? echo number_format($opening,2); ?></p></td>
                                           
                                            <td width="80" align="right" style="word-break: break-all;"><p><? echo number_format($receive,2); ?></p></td>
                                            <td width="80" align="right" style="word-break: break-all;"><p><? echo number_format($issue_return,2); ?></p></td>
                                            <td width="80" align="right" style="word-break: break-all;"><p>
                                                <?
                                               // if ($transfer_in>0) {
                                                    echo number_format($transfer_in,2);
                                                //}
                                                 //else{echo number_format($transfer_qnty_store_arr[$prod_id]['transfer_qnty'],2);  }
                                                ?>
                                                </p></td>
                                            <td width="100" align="right" style="word-break: break-all;"><p><? echo number_format($totalReceive,2); ?></p></td>
                                           
                                            <td width="80" align="right" style="word-break: break-all;"><p><? echo number_format($issue,2); ?></p></td>
                                            <td width="80" align="right" style="word-break: break-all;"><p><? echo number_format($rec_return,2); ?></p></td>
                                            <td width="80" align="right" style="word-break: break-all;"><p>

                                            <?
                                              echo number_format($transfer_out,2);
                                              // echo number_format($transfer_outQnty[$prod_id][$row["detarmination_id"]][$row["gsm"]][$row["dia_width"]]['tranfOutQnty'],2);
                                                //if ($transfer_in>0) {echo number_format($transfer_in,2); }
                                                // else{echo number_format($transfer_qnty_store_arr[$prod_id]['transfer_qnty'],2);  }
                                            ?>


                                                </p></td>
                                            <td width="100" align="right" style="word-break: break-all;"><p><? echo number_format($totalIssue,2); ?></p></td>
                                           
                                            <td width="100" align="right" style="word-break: break-all;"><p><? echo number_format($closingStock,2); ?></p></td>
                                            
                                            <? $daysOnHand = datediff("d",$date_array[$prod_id]['max_date'],date("Y-m-d")); ?>
                                            <td align="center" style="word-break: break-all;"><? echo $daysOnHand; ?></td>
                                        </tr>
                                        <?
                                        $i++;
                                   // }
                               // }
                            }   
                        }
                    }
                    ?>
                </table>
            </div>
            <table width="<? echo $tableWidth;?>" border="1" cellpadding="0" cellspacing="0" class="tbl_bottom" rules="all" >
               <tr>
                    <td width="40" align="right">&nbsp;</td>
                    <td width="60" align="right">&nbsp;</td>
                    <td width="120" align="right">&nbsp;</td>
                    <td width="180" align="right">&nbsp;</td>
                    <td width="70" align="right">&nbsp;</td>
                       
                    <td width="100" align="right">Total</td>

                    <td  style="word-break: break-all;" width="110" align="right" id="value_tot_opening"><? echo number_format($tot_opening,2);  ?></td>
                    
                    <td style="word-break: break-all;" width="80" align="right" id="value_tot_receive"><? echo number_format($tot_receive,2);  ?></td>
                    <td style="word-break: break-all;" width="80" align="right" id="value_tot_issue_return"><? echo number_format($tot_issue_return,2);  ?></td>
                    <td style="word-break: break-all;" width="80" align="right" id="value_tot_trans_in"><? echo number_format($tot_transfer_in,2);  ?></td>
                    <td style="word-break: break-all;" width="100" align="right" id="value_total_receive"><? echo number_format($total_receive,2);  ?></td>
                   
                    <td style="word-break: break-all;" width="80" align="right" id="value_tot_issue"><? echo number_format($tot_issue,2);  ?></td>
                    <td  style="word-break: break-all;" width="80" align="right" id="value_tot_rec_return"><? echo number_format($tot_rec_return,2);  ?></td>
                    <td  style="word-break: break-all;" width="80" align="right" id="value_tot_transfer_out"><? echo number_format($tot_transfer_out,2);  ?></td>
                    <td  style="word-break: break-all;" width="100" align="right" id="value_total_issue"><? echo number_format($total_issue,2);  ?></td>
                    
                    <td  style="word-break: break-all;" width="100" align="right" id="value_totalStock"><? echo number_format($totalStockValue,2); ?></td>
                    
                    <td align="right">&nbsp;</td>
                </tr>
            </table>
        </div>
        <?
    }
	else
    {

        $data_array=array();

        $trnasactionData=sql_select("Select a.id as trans_id,
            (case when a.transaction_type=1 and a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as rcv_total_opening,
            (case when a.transaction_type=2 and a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as iss_total_opening,
            (case when a.transaction_type=3 and a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as rcv_return_opening,
            (case when a.transaction_type=4 and a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as iss_return_opening,
            (case when a.transaction_type=5 and a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as transfer_in_opening,
            (case when a.transaction_type=6 and a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as transfer_out_opening,
            (case when a.transaction_type in (1,4,5) and a.transaction_date<'".$from_date."' then a.cons_amount else 0 end) as total_rcv_value_opening,
            (case when a.transaction_type in (2,3,6) and a.transaction_date<'".$from_date."' then a.cons_amount else 0 end) as total_issue_value_opening,
            (case when a.transaction_type=1 and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as receive,
            (case when a.transaction_type=2 and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as issue,
            (case when a.transaction_type=3 and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as rec_return,
            (case when a.transaction_type=4 and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as issue_return,
            (case when a.transaction_type=5 and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as transfer_in,
            (case when a.transaction_type=6 and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as transfer_out,
            (case when a.transaction_type in (1,4,5) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as total_rcv_value,
            (case when a.transaction_type in (2,3,6) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as total_issue_value
            from inv_transaction a, product_details_master b
            where a.prod_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_id $prod_company_id $item_category_id   $store_id $item_account order by b.id ASC");

        foreach($trnasactionData as $row)
        {

            $trans_id_arr[$row[csf("trans_id")]] = $row[csf("trans_id")];

        }


        $trans_ids = implode(",", array_filter($trans_id_arr));
        if($trans_ids=="") $trans_ids=0;
        $transIdCond = $trans_id_cond = $For_nonOrderTransIdCond= $for_non_order="";
        $transIdArr=explode(",",$trans_ids);
        if($db_type==2 && count($transIdArr)>999)
        {
            $transIdChunkArr=array_chunk($transIdArr,999) ;
            foreach($transIdChunkArr as $chunk_arr)
            {
                $transIdCond.=" a.trans_id in(".implode(",",$chunk_arr).") or ";
                $For_nonOrderTransIdCond.=" b.id in(".implode(",",$chunk_arr).") or ";
            }

            $trans_id_cond.=" and (".chop($transIdCond,'or ').")";
            $for_non_order.=" and (".chop($For_nonOrderTransIdCond,'or ').")";

        }
        else
        {
            $trans_id_cond=" and a.trans_id in($trans_ids)";
            $for_non_order=" and b.id in($trans_ids)";
        }


        $buyer_from_trans = sql_select("select b.buyer_name, a.trans_id, a.is_sales,a.po_breakdown_id
        from order_wise_pro_details a, wo_po_details_master b, wo_po_break_down c
        where a.po_breakdown_id = c.id and c.job_id = b.id
        and a.trans_id > 0 $trans_id_cond and a.status_active = 1 and a.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0");
        foreach($buyer_from_trans as $row)
        {
            if($row[csf('is_sales')] == 1)
            {
                $sales_order[$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];
            }
            else
            {
                $buyerTrans[$row[csf('trans_id')]] = $row[csf('buyer_name')];
            }

        }

        /*echo "<pre>";
        print_r($buyerTrans);die;*/


        $sales_order_ids = implode(",", array_filter($sales_order));
        if($sales_order_ids=="") $sales_order_ids=0;
        $sales_cond = $sales_order_id_cond = "";
        $sales_order_arr=explode(",",$sales_order_ids);
        if($db_type==2 && count($sales_order_arr)>999)
        {
            $sales_order_chunk=array_chunk($sales_order_arr,999) ;
            foreach($sales_order_chunk as $chunk_arr)
            {
                $sales_cond.=" a.id in(".implode(",",$chunk_arr).") or ";
            }

            $sales_order_id_cond.=" and (".chop($sales_cond,'or ').")";

        }
        else
        {
            $sales_order_id_cond=" and a.id in($sales_order_ids)";
        }

        $buyer_from_sales= return_library_array("select a.id , (case when a.within_group = 2 then a.buyer_id when a.within_group = 1 and b.id is not null then b.buyer_id when a.within_group = 1 and c.id is not null then c.buyer_id end) buyer_id
                    from fabric_sales_order_mst a left join wo_booking_mst b on a.sales_booking_no = b.booking_no and b.status_active = 1 left join   wo_non_ord_samp_booking_mst c on a.sales_booking_no = c.booking_no
                    and c.status_active = 1 where a.status_active = 1 and a.company_id= $cbo_company_name $sales_order_id_cond",'id','buyer_id');

        foreach($buyer_from_trans as $row)
        {
            if($row[csf('is_sales')] == 1)
            {
                $buyerTrans[$row[csf('trans_id')]] = $buyer_from_sales[$row[csf('po_breakdown_id')]];
            }

        }


        $nonOrderRcvBuyer_1= sql_select("select c.buyer_id, b.id as trans_id
        from wo_non_ord_samp_booking_mst c ,inv_receive_master a, inv_transaction b
        where a.id = b.mst_id and c.booking_no = a.booking_no and a.entry_form in (22,23) and a.receive_basis = 2
        and b.status_active = 1 and a.status_active = 1 and c.status_active = 1 and b.transaction_type =1 $for_non_order $company_id
        union all
        select  c.buyer_id, b.id as trans_id
        from wo_non_ord_samp_booking_mst c ,inv_receive_master a, inv_transaction b, inv_receive_master d
        where a.id = b.mst_id  and a.entry_form in (22,23) and a.receive_basis = 9 and b.item_category = 13 and a.booking_no = d.recv_number and d.booking_no = c.booking_no
        and b.status_active = 1 and a.status_active = 1 and c.status_active = 1 and  b.transaction_type =1 $for_non_order $company_id
        union all
        select  c.buyer_id, b.id as trans_id
        from wo_non_ord_samp_booking_mst c ,inv_issue_master a, inv_transaction b
        where c.booking_no = a.booking_no and a.id = b.mst_id and a.item_category = 13 and b.is_deleted = 0 and a.is_deleted = 0 and b.transaction_type in (2,3,6) $for_non_order $company_id
        ");



        foreach ($nonOrderRcvBuyer_1 as $row)
        {
            $buyerTrans[$row[csf('trans_id')]] = $row[csf('buyer_id')];
        }


        $nonOrderRcvBuyer_2= sql_select("select  b.id as trans_id, d.buyer_id
        from inv_receive_master a, inv_transaction b, pro_roll_details c, wo_non_ord_samp_booking_mst d
        where a.id = b.mst_id and a.id = c.mst_id and c.booking_no = d.booking_no and a.entry_form in (58)  and c.entry_form in (58) and b.item_category = 13
        and b.status_active = 1 and a.status_active = 1  and  b.transaction_type =1 $for_non_order
        union all
        select  b.id as trans_id, d.buyer_id
        from inv_issue_master a, inv_transaction b, pro_roll_details c, wo_non_ord_samp_booking_mst d
        where a.id = b.mst_id and a.id = c.mst_id and c.booking_no = d.booking_no and a.entry_form in (61)  and c.entry_form in (61) and b.item_category = 13
        and b.status_active = 1 and a.status_active = 1  and  b.transaction_type=2 $for_non_order
        ");

        foreach ($nonOrderRcvBuyer_2 as $row)
        {
            $buyerTrans[$row[csf('trans_id')]] = $row[csf('buyer_id')];
        }

        foreach($trnasactionData as $row)
        {

            $data_array[$buyerTrans[$row[csf("trans_id")]]]['rcv_total_opening']+=$row[csf("rcv_total_opening")];
            $data_array[$buyerTrans[$row[csf("trans_id")]]]['iss_total_opening']+=$row[csf("iss_total_opening")];
            $data_array[$buyerTrans[$row[csf("trans_id")]]]['rcv_return_opening']+=$row[csf("rcv_return_opening")];
            $data_array[$buyerTrans[$row[csf("trans_id")]]]['iss_return_opening']+=$row[csf("iss_return_opening")];
            $data_array[$buyerTrans[$row[csf("trans_id")]]]['receive']+=$row[csf("receive")];
            $data_array[$buyerTrans[$row[csf("trans_id")]]]['issue']+=$row[csf("issue")];
            $data_array[$buyerTrans[$row[csf("trans_id")]]]['rec_return']+=$row[csf("rec_return")];
            $data_array[$buyerTrans[$row[csf("trans_id")]]]['issue_return']+=$row[csf("issue_return")];
            $data_array[$buyerTrans[$row[csf("trans_id")]]]['transfer_in']+=$row[csf("transfer_in")];
            $data_array[$buyerTrans[$row[csf("trans_id")]]]['transfer_out']+=$row[csf("transfer_out")];
            $data_array[$buyerTrans[$row[csf("trans_id")]]]['transfer_in_opening']+=$row[csf("transfer_in_opening")];
            $data_array[$buyerTrans[$row[csf("trans_id")]]]['transfer_out_opening']+=$row[csf("transfer_out_opening")];


            $data_array[$buyerTrans[$row[csf("trans_id")]]]['total_rcv_value_opening']+=$row[csf("total_rcv_value_opening")];
            $data_array[$buyerTrans[$row[csf("trans_id")]]]['total_issue_value_opening']+=$row[csf("total_issue_value_opening")];
            $data_array[$buyerTrans[$row[csf("trans_id")]]]['total_rcv_value']+=$row[csf("total_rcv_value")];
            $data_array[$buyerTrans[$row[csf("trans_id")]]]['total_issue_value']+=$row[csf("total_issue_value")];

        }

        ?>
        <div>
            <table style="width:1178px" border="1" cellpadding="2" cellspacing="0"  id="caption" rules="all">
                <thead>
                    <tr class="form_caption" style="border:none;">
                        <td colspan="<? echo $colspan;?>" align="center" style="border:none;font-size:16px; font-weight:bold" >Buyer Wise Grey Fabric <? echo $report_title; ?></td>
                    </tr>
                    <tr class="form_caption" style="border:none;">
                        <td colspan="<? echo $colspan;?>" align="center" style="border:none; font-size:14px;">
                           <b>Company Name : <? echo $companyArr[$cbo_company_name]; ?></b>
                        </td>
                    </tr>
                    <tr class="form_caption" style="border:none;">
                        <td colspan="<? echo $colspan;?>" align="center" style="border:none;font-size:12px; font-weight:bold">
                            <? if($from_date!="" || $to_date!="") echo "From : ".change_date_format($from_date)." To : ".change_date_format($to_date)."" ;?>
                        </td>
                    </tr>
                </thead>
            </table>
            <table width="1160" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" align="left">
                <thead>
                    <tr>
                        <th rowspan="2" width="40">SL</th>
                        <th rowspan="2" width="120">Buyer Name</th>
                        <th rowspan="2" width="150">Opening Stock</th>
                        <th colspan="4">Received</th>
                        <th colspan="4">Issue</th>
                        <th rowspan="2" width="100">Closing Stock</th>
                        <th rowspan="2" >Remarks</th>
                    </tr>
                    <tr>

                        <th width="80">Receive</th>
                        <th width="80">Issue Return</th>
                        <th width="80">Transfer In</th>
                        <th width="100">Total Receive</th>

                        <th width="80">Issue</th>
                        <th width="80">Receive Return</th>
                        <th width="80">Transfer Out</th>
                        <th width="100">Total Issue</th>
                    </tr>
                </thead>
            </table>
            <div style="width:1178px; max-height:350px; overflow-y:scroll; float: left;" id="scroll_body" >
                <table width="1160" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body" align="left">
                <?
                    $i=1;
                    foreach($data_array as $buyer_id=>$row)
                    {
                        if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";

                        $opening_rcv= $row['rcv_total_opening']+$row['iss_return_opening']+$row['transfer_in_opening'];
                        $opening_issue = $row['iss_total_opening']+$row['rcv_return_opening']+$row['transfer_out_opening'];
                        $opening= $opening_rcv - $opening_issue;

                        $opening_rate = $total_value_opening = 0;
                        if($opening_rcv > 0)
                        {
                            $opening_rate = $row['total_rcv_value_opening']/ $opening_rcv;
                        }
                        $total_value_opening = $opening *$opening_rate;

                        $receive = $row['receive'];
                        $issue_return = $row['issue_return'];
                        $transfer_in = $row['transfer_in'];
                        $totalReceive=$receive+$issue_return+$transfer_in;

                        $total_rcv_value = $row['total_rcv_value'];

                        $issue = $row['issue'];
                        $rec_return = $row['rec_return'];
                        $transfer_out = $row['transfer_out'];
                        $totalIssue=$issue+$rec_return+$transfer_out;
                        $total_issue_value=$row['total_issue_value'];

                        $closingStock=$opening+$totalReceive-$totalIssue;
                         //echo $get_upto_qnty.'='.$txt_qnty;
                        //$get_upto=array(1=>"Greater Than",2=>"Less Than",3=>"Greater/Equal",4=>"Less/Equal",5=>"Equal");
                        if (
                            ($get_upto_qnty==1 && $txt_qnty<$closingStock) ||
                            ($get_upto_qnty==2 && $txt_qnty>$closingStock) ||
                            ($get_upto_qnty==3 && $txt_qnty<=$closingStock) ||
                            ($get_upto_qnty==4 && $txt_qnty>=$closingStock) ||
                            ($get_upto_qnty==5 && $txt_qnty==$closingStock) ||
                            ($get_upto_qnty==0)
                        )  
                        { 
                            $closingValue= $total_value_opening+ $total_rcv_value - $total_issue_value;
                            $closingRate = $closingValue/$closingStock;

                            $totalStock+=$closingStock;
                            $tot_closingValue+=$closingValue;
                            $tot_total_value_opening+=$total_value_opening;
                            $tot_total_rcv_value+=$total_rcv_value;
                            $tot_total_issue_value+=$total_issue_value;

                            $tot_opening+=$opening;
                            $tot_only_issue+= $issue;
                            $tot_only_receive+=$receive;
                            $tot_transfer_in+=$transfer_in;
                            $tot_transfer_out+=$transfer_out;
                            $total_receive+=$totalReceive;
                            $tot_issue+=$totalIssue;
                            $tot_issue_return+=$issue_return;
                            $tot_rec_return+=$rec_return;

                             if(($cbo_value_with ==1 && (number_format($opening,2,'.','')!= 0 ||  number_format($receive,2,'.','')!= 0 || number_format($issue_return,2,'.','')!=0 || number_format($transfer_in,2,'.','')!= 0 || number_format($totalReceive,2,'.','')!=0 || number_format($issue,2,'.','')!=0 || number_format($rec_return,2,'.','')!=0 || number_format($transfer_out,2,'.','')!= 0 || number_format($totalIssue,2,'.','')!=0) ) || ($cbo_value_with ==0 && (number_format($opening,2,'.','')== 0 ||  number_format($receive,2,'.','')== 0 || number_format($issue_return,2,'.','')==0 || number_format($transfer_in,2,'.','')== 0 || number_format($totalReceive,2,'.','')==0 || number_format($issue,2,'.','')==0 || number_format($rec_return,2,'.','')==0 || number_format($transfer_out,2,'.','')== 0 || number_format($totalIssue,2,'.','')==0)))
                             {

                           
                                ?>
                                    <tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                        <td width="40"><? echo $i; ?></td>
                                        <td width="120" align="center"><p><? echo $buyer_Arr[$buyer_id].$buyer_id; ?></p></td>
                                        <td width="150" align="right"><p><? echo number_format($opening,2); ?></p></td>
                                        <td width="80" align="right"><p><? echo number_format($receive,2); ?></p></td>
                                        <td width="80" align="right"><p><? echo number_format($issue_return,2); ?></p></td>
                                        <td width="80" align="right"><p><? echo number_format($transfer_in,2); ?></p></td>
                                        <td width="100" align="right"><p><? echo number_format($totalReceive,2); ?></p></td>
                                        <td width="80" align="right"><p><? echo number_format($issue,2); ?></p></td>
                                        <td width="80" align="right"><p><? echo number_format($rec_return,2); ?></p></td>
                                        <td width="80" align="right"><p><? echo number_format($transfer_out,2); ?></p></td>
                                        <td width="100" align="right"><p><? echo number_format($totalIssue,2); ?></p></td>
                                        <td width="100" align="right"><p><? echo number_format($closingStock,2); ?></p></td>

                                        <td align="center"><?  ?></td>
                                    </tr>
                                <?
                                 $i++;
                            }
                        }

                    }
                    ?>
                </table>
            </div>
            <table width="1160" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body" align="left">
                <tfoot>
                    <tr>
                        <th width="40" align="right"><p><? //echo number_format($tot_opening,2); ?></p></th>
                        <th width="120" align="right"><p>Total </p></th>
                        <th width="150" align="right"><p><? echo number_format($tot_opening,2); ?></p></th>
                        <th width="80" align="right"><p><? echo number_format($tot_only_receive,2); ?></p></th>
                        <th width="80" align="right"><p><? echo number_format($tot_issue_return,2); ?></p></th>
                        <th width="80" align="right"><p><? echo number_format($tot_transfer_in,2); ?></p></th>
                        <th width="100" align="right"><p><? echo number_format($total_receive,2); ?></p></th>
                        <th width="80" align="right"><p><? echo number_format($tot_only_issue,2); ?></p></th>
                        <th width="80" align="right"><p><? echo number_format($tot_rec_return,2); ?></p></th>
                        <th width="80" align="right"><p><? echo number_format($tot_transfer_out,2); ?></p></th>
                        <th width="100" align="right"><p><? echo number_format($tot_issue,2); ?></p></th>
                        <th width="100" align="right"><p><? echo number_format($totalStock,2); ?></p></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>

        </div>
        <?
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
    echo "$html**$filename**$report_type";
    exit();
}
?>
