<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header('location:login.php');
include('../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];
$user_level=$_SESSION['logic_erp']["user_level"];

if ($action=="load_drop_down_location")
{
	$data=explode("_",$data);
	if($data[1]==1) $dropdown_name="cbo_location_name";
	else $dropdown_name="cbo_party_location";

	echo create_drop_down( $dropdown_name, 150, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "" );
	exit();
}

if ($action=="load_drop_down_supplier")
{
	$sql="SELECT a.id, a.supplier_name 
    from lib_supplier a, lib_supplier_tag_company b
    where a.id=b.supplier_id and b.tag_company='$data' and a.status_active =1 and a.is_deleted=0 order by a.supplier_name";
    
	$sql_res=sql_select($sql);
	$supplier_select="";
	if (count($sql_res)==1){
		$supplier_select=$sql_res[0][csf('id')];
	}
	
	echo create_drop_down( "cbo_supplier_name", 150, $sql,"id,supplier_name", 1, "-- Select Supplier --", $supplier_select, "" );
	exit();
}


if($action=="check_conversion_rate")
{
	$data=explode("**",$data);

	$conversion_date=date("Y/m/d");
	$exchange_rate=set_conversion_rate( $data[0], $conversion_date,$data[1] );
	echo $exchange_rate;
	exit();	
}

if ($action=="load_drop_down_buyer")
{
	$data=explode("_",$data);
    //company+'_'+1
	if($data[1]==1) $load_function="fnc_load_party(2,document.getElementById('cbo_within_group').value)";
	else $load_function="";
	//$company_cond
	if($data[1]==1)
	{
		//echo  "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name";
		echo create_drop_down( "cbo_party_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 order by comp.company_name","id,company_name", 1, "-- Select Company --", $data[2], "$load_function");
	}
	else
	{
		echo create_drop_down( "cbo_party_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $data[2], "" );
	}
	exit();
}


if($action == 'work_order_popup') {
	echo load_html_head_contents('Yarn Dyeing Sales Order Info', '../../', 1, 0, $unicode);
	extract($_REQUEST);
?>
    <style>
        table.rpt_table tbody td input {
            width: 90%;
        }
    </style>
	<script>
		permission="<?php echo $permission; ?>";

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
			if($("#tr_"+i).css("display") != "none")
			{
				$("#tr_"+i).click();
			}
		}
	}

	function js_set_value(id)
	{
		var strs=id.split("__");
        // alert(str[0]+'=='+str)
		toggle( document.getElementById( 'tr_' + strs[0] ), '#FFFFFF' );
		str=strs[1];

		if( jQuery.inArray(  str , selected_id ) == -1 ) {
			selected_id.push( str );
		}
		else {
			for( var i = 0; i < selected_id.length; i++ ) {
				if( selected_id[i] == str  ) break;
			}
			selected_id.splice( i, 1 );
		
		}
		var id = '';
		var ddd='';
		for( var i = 0; i < selected_id.length; i++ ) {
			id += selected_id[i] + ',';
			
		}
		id = id.substr( 0, id.length - 1 );
		
		$('#selected_prod_id').val( id );	
	}

	</script>
</head>
<body>
<div align="center" style="width:920px;" >
    <form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center" style="width: 920px;">
            <thead>
                <tr>
                    <th colspan="8"><?php echo create_drop_down('cbo_string_search_type', 163, $string_search_type, '', 1, '-- Searching Type --'); ?></th>
                </tr>
                <tr>
                    <th style="width:163px">Company Name</th>
                    <th style="width:100px;">Item Category</th>
                    <th style="width:100px">Job No</th>
                    <th style="width:100px">Style Name</th>
                    <th style="width:100px">WO/Booking Number</th>
                    <th style="width:200px">WO Date Range</th>
                    <th style="width:100px">
                    	<input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width: 90%;" />
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr class="general"><? $data=explode("_",$data); ?>
                    <td>
                        <?php echo create_drop_down('cbo_company_name', 163, "select id, company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", 'id,company_name', 1, '-- Select Company --', $data[0], '', 1); ?>
                    </td>
                    <td>
                        <!-- <?php echo create_drop_down('cbo_catagory_name', 100, $item_category, '', 1, '-- Select Catagory --',"", '', 0); ?> -->

                        <? echo create_drop_down( "cbo_catagory_name", 150, "select category_id, short_name from  lib_item_category_list where status_active=1 and category_id in(1,3,2,4,25) and is_deleted=0 order by short_name","category_id,short_name", 1, "-- Select Debit Note --",$data[1],"",1); ?>
                    </td>
                    <td>
                        <input class="text_boxes" type="text" name="txt_job_no" id="txt_job_no" />
                    </td>
                    <td>
                        <input class="text_boxes" type="text" name="txt_style_name" id="txt_style_name" />
                    </td>              
                    <td>
                        <input class="text_boxes" type="text" name="txt_workorder_no" id="txt_workorder_no" />
                    </td>
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px"> To
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
                    </td>
                    <td align="center">
                        <input type="hidden" id="selected_prod_id">
                        <input type="button" name="btnSearchJob" class="formbutton" value="Show" onClick="show_list_view(document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_style_name').value+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_workorder_no').value+'_'+document.getElementById('cbo_catagory_name').value+'_'+document.getElementById('cbo_year_selection').value+'_'+<?=$data[2];?>, 'create_work_order_list_view', 'search_div', 'supplier_debit_note_entry_controller','setFilterGrid(\'list_view\',-1)')" />
                    </td>
                </tr>
                <tr>
                    <td  align="center" colspan="8" height="40" valign="middle">
                        <? echo load_month_buttons(1);  ?>
                        <input type="hidden" id="hidden_wo_number" name="hidden_wo_number" value="" />
                    </td>
               </tr>
                <tr>
                    <td colspan="8" align="center" valign="top" id=""><div id="search_div"></div></td>
                </tr>
            </tbody>
        </table>
    </form>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?php
exit();
}

if($action == 'create_work_order_list_view') {
    $data=explode('_', $data);
//    print_r($data);
    $search_type = $data[0];
    $company_id = $data[1];
    $style_ref = $data[2];
    $job_no = $data[3];
    $wo_no = $data[4];
    $item_catagory = $data[5];
    $suplier_id = $data[7];
    $condition = '';  $yo_wo_condition = ''; 
    $wo_condition = '';$suplier_con='';
    $party = '';$style_ref_condition="";
    $sql_cond="";


    if($company_id) {
        $company.=" and a.company_id=$data[1]";
        $company_name.=" and company_name=$data[1]";
    } else {
        echo "<h3 style='margin-top: 10px;'>Please Select Company First.</h3>"; die;
    }

    if($suplier_id) {
        $suplier_con.=" and a.supplier_id=$suplier_id";
    } 

    // if($party_id) {
    //     $condition.=" and a.party_id=$party_id";  
    // }

   
    if($item_catagory==1){
        if($search_type==0 || $search_type==4) { // no searching type or contents
            if ($job_no!="") $condition.=" and b.job_no like '%$job_no%'";
            if ($wo_no!="") $wo_condition.=" and a.booking_no like '%$wo_no%'";
            if ($wo_no!="") $yo_wo_condition.=" and a.wo_number like '%$wo_no%'";
            if ($style_ref!="") $style_ref_condition.=" and b.style_no like '%$style_ref%'";
        
        } else if($search_type==1) { // exact
            if ($job_no!="") $condition.=" and b.job_no = '$job_no'";
            if ($wo_no!="") $wo_condition.=" and a.booking_no = '$wo_no'";
            if ($wo_no!="") $yo_wo_condition.=" and a.wo_number = '$wo_no'";
            if ($style_ref!="") $style_ref_condition.=" and b.style_no = '$style_ref'";
    
        } else if($search_type==2) { // Starts with
            if ($job_no!="") $condition.=" and b.job_no like '$job_no%'";
            if ($wo_no!="") $wo_condition.=" and a.booking_no like '$wo_no%'";
            if ($wo_no!="") $yo_wo_condition.=" and a.wo_number like '$wo_no%'";
            if ($style_ref!="") $style_ref_condition.=" and b.style_no like = '$style_ref%'";

    
        } else if($search_type==3) { // Ends with
            if ($job_no!="") $condition.=" and b.job_no like '%$job_no'";
            if ($wo_no!="") $wo_condition.=" and b.booking_no like '%$wo_no'";
            if ($wo_no!="") $yo_wo_condition.=" and b.wo_number like '%$wo_no'";
            if ($style_ref!="") $style_ref_condition.=" and b.style_no like '%$style_ref'";
    
        }

    }elseif($item_catagory==2)
    {
        if($search_type==0 || $search_type==4) { // no searching type or contents
            if ($job_no!="") $condition.=" and b.job_no like '%$job_no%'";
            if ($wo_no!="") $wo_condition.=" and a.booking_no like '%$wo_no%'";
            if ($style_ref!="") $style_ref_condition.=" and b.style_ref_no like '%$style_ref%'";
        
        } else if($search_type==1) { // exact
            if ($job_no!="") $condition.=" and b.job_no = '$job_no'";
            if ($wo_no!="") $wo_condition.=" and a.booking_no = '$wo_no'";
            if ($style_ref!="") $style_ref_condition.=" and b.style_ref_no = '$style_ref'";
        
    
        } else if($search_type==2) { // Starts with
            if ($job_no!="") $condition.=" and b.job_no like '$job_no%'";
            if ($wo_no!="") $wo_condition.=" and a.booking_no like '$wo_no%'";
            if ($style_ref!="") $style_ref_condition.=" and b.style_ref_no like '$style_ref%'";
            
    
        } else if($search_type==3) { // Ends with
            if ($job_no!="") $condition.=" and b.job_no like '%$job_no'";
            if ($wo_no!="") $wo_condition.=" and b.booking_no like '%$wo_no'";
            if ($style_ref!="") $style_ref_condition.=" and b.style_ref_no like '%$style_ref'";
    
        }

    }elseif($item_catagory==3)
    {
        if($search_type==0 || $search_type==4) { // no searching type or contents
            if ($job_no!="") $condition.=" and c.job_no like '%$job_no%'";
            if ($wo_no!="") $wo_condition.=" and a.booking_no like '%$wo_no%'";
            if ($style_ref!="") $style_ref_condition.=" and c.style_ref_no like '%$style_ref%'";

        
        } else if($search_type==1) { // exact
            if ($job_no!="") $condition.=" and c.job_no = '$job_no'";
            if ($wo_no!="") $wo_condition.=" and a.booking_no = '$wo_no'";
            if ($style_ref!="") $style_ref_condition.=" and c.style_ref_no = '$wo_no'";

    
        } else if($search_type==2) { // Starts with
            if ($job_no!="") $condition.=" and c.job_no like '$job_no%'";
            if ($wo_no!="") $wo_condition.=" and a.booking_no like '$wo_no%'";
            if ($style_ref!="") $style_ref_condition.=" and c.style_ref_no like '$style_ref%'";
            
    
        } else if($search_type==3) { // Ends with
            if ($job_no!="") $condition.=" and c.job_no like '%$job_no'";
            if ($wo_no!="") $wo_condition.=" and b.booking_no like '%$wo_no'";   
            if ($style_ref!="") $style_ref_condition.=" and c.style_ref_no like '%$style_ref'";   
        }
        
    }elseif($item_catagory==4)
    {
        if($search_type==0 || $search_type==4) { // no searching type or contents
            if ($job_no!="") $condition.=" and b.job_no like '%$job_no%'";
            if ($wo_no!="") $wo_condition.=" and a.booking_no like '%$wo_no%'";
            if ($style_ref!="") $style_ref_condition.=" and c.style_ref_no like '%$style_ref%'";
        
        } else if($search_type==1) { // exact
            if ($job_no!="") $condition.=" and b.job_no = '$job_no'";
            if ($wo_no!="") $wo_condition.=" and a.booking_no = '$wo_no'";
            if ($style_ref!="") $style_ref_condition.=" and c.style_ref_no = '$style_ref'";
    
        } else if($search_type==2) { // Starts with
            if ($job_no!="") $condition.=" and b.job_no like '$job_no%'";
            if ($wo_no!="") $wo_condition.=" and a.booking_no like '$wo_no%'";
            if ($style_ref!="") $style_ref_condition.=" and c.style_ref_no like '$style_ref%'";
            
    
        } else if($search_type==3) { // Ends with
            if ($job_no!="") $condition.=" and b.job_no like '%$job_no'";
            if ($wo_no!="") $wo_condition.=" and b.booking_no like '%$wo_no'";
            if ($style_ref!="") $style_ref_condition.=" and c.style_ref_no like '%$style_ref'";
    
        }
        
    }elseif($item_catagory==25)
    {
        if($search_type==0 || $search_type==4) { // no searching type or contents
            if ($job_no!="") $condition.=" and b.job_no like '%$job_no%'";
            if ($wo_no!="") $wo_condition.=" and a.booking_no like '%$wo_no%'";
            if ($style_ref!="") $style_ref_condition.=" and c.style_ref_no like '%$style_ref%'";
        
        } else if($search_type==1) { // exact
            if ($job_no!="") $condition.=" and b.job_no = '$job_no'";
            if ($wo_no!="") $wo_condition.=" and a.booking_no = '$wo_no'";
            if ($style_ref!="") $style_ref_condition.=" and c.style_ref_no = '$style_ref'";
    
        } else if($search_type==2) { // Starts with
            if ($job_no!="") $condition.=" and b.job_no like '$job_no%'";
            if ($wo_no!="") $wo_condition.=" and a.booking_no like '$wo_no%'";
            if ($style_ref!="") $style_ref_condition.=" and c.style_ref_no like '$style_ref%'";
            
    
        } else if($search_type==3) { // Ends with
            if ($job_no!="") $condition.=" and b.job_no like '%$job_no'";
            if ($wo_no!="") $wo_condition.=" and b.booking_no like '%$wo_no'";
            if ($style_ref!="") $style_ref_condition.=" and c.style_ref_no like '%$style_ref'";
    
        }
        
    }

    if($db_type==0){
		if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
	}
	else if($db_type==2){
		if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
	}

    if($db_type==0){ $sql_cond .=" and year(a.insert_date)=".$data[6].""; }
	else{ $sql_cond .=" and to_char(a.insert_date,'YYYY')=".$data[6].""; }


    $color_arr = return_library_array('select id, color_name from lib_color where status_active=1 and is_deleted=0', 'id', 'color_name');
    $buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
    $suplier=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');


    $pi_data=sql_select("SELECT a.pi_number, SUM (b.amount)AS amount,b.work_order_id,c.lc_number,d.invoice_no,d.document_value FROM com_pi_master_details a, com_pi_item_details b LEFT JOIN com_btb_lc_master_details c ON c.pi_id = TO_CHAR(b.pi_id) LEFT JOIN com_import_invoice_mst d ON TO_CHAR(c.id) = d.BTB_LC_ID WHERE a.id = b.pi_id GROUP BY a.pi_number,
    b.work_order_id,c.lc_number,d.invoice_no, d.document_value");
    
    $pi_data_arr=array();
    foreach( $pi_data as $row){
        $pi_data_arr[$row[csf('work_order_id')]]['pi_number']=$row[csf('pi_number')];
        $pi_data_arr[$row[csf('work_order_id')]]['lc_number']=$row[csf('lc_number')];
        $pi_data_arr[$row[csf('work_order_id')]]['amount']=$row[csf('amount')];     
        $pi_data_arr[$row[csf('work_order_id')]]['invoice_no']=$row[csf('invoice_no')];     
        $pi_data_arr[$row[csf('work_order_id')]]['document_value']=$row[csf('document_value')];     
    }

    if($item_catagory==2){

        $sql= "SELECT a.id, a.booking_date, a.company_id, a.buyer_id, a.job_no, a.item_category, a.supplier_id, a.booking_no 
        from wo_booking_mst a, wo_po_details_master b, wo_po_break_down c, wo_po_details_mas_set_details d where 
        a.job_no=b.job_no and a.job_no=c.job_no_mst and b.job_no=c.job_no_mst and b.job_no=d.job_no and a.booking_type=1 and a.is_short=2 and a.status_active=1 and a.entry_form=118 and  a.item_category=2 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $sql_cond $company $condition $booking_date $wo_condition $suplier_con  $style_ref_condition group by a.id, a.booking_date, a.company_id, a.buyer_id, a.job_no, a.item_category, a.supplier_id, a.booking_no order by a.id DESC";
    }
    elseif($item_catagory==4){
        $sql="SELECT a.id, a.booking_no, a.company_id, a.supplier_id,a.buyer_id, a.booking_date from wo_booking_mst a, wo_booking_dtls b, wo_po_details_master c, wo_po_break_down d where a.booking_no=b.booking_no and b.job_no=c.job_no and b.job_no=d.job_no_mst and c.id=d.job_id and b.po_break_down_id=d.id and a.booking_type=2 and  a.item_category=4 and a.entry_form=87 and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 $sql_cond $company $condition $wo_condition $suplier_con  $style_ref_condition  group by a.id, a.booking_no, a.company_id, a.supplier_id,a.buyer_id, a.booking_date  order by a.id DESC";
    }
    elseif($item_catagory==1){

        $sql = "SELECT a.id, a.wo_number as booking_no, a.buyer_name as buyer_id, a.wo_date as booking_date, a.wo_date,a.supplier_id,a.item_category , a.currency_id
        from wo_non_order_info_mst a,wo_non_order_info_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and a.entry_form=144 and a.item_category=1 $company_name $sql_cond $condition $yo_wo_condition $suplier_con $style_ref_condition group by a.id, a.wo_number, a.buyer_name, a.wo_date, a.wo_date,a.supplier_id,a.item_category , a.currency_id order by a.id desc";

    }elseif($item_catagory==25){

        $sql = "SELECT a.id, a.booking_date,a.booking_no, a.company_id, a.buyer_id, a.supplier_id, a.item_category from wo_booking_mst a, wo_booking_dtls b, wo_po_details_master c where 
         a.booking_no=b.booking_no and b.job_no=c.job_no and a.booking_type=6 and a.entry_form=201 and  a.status_active=1  and a.is_deleted=0 and  b.status_active=1 and a.item_category=25  and b.is_deleted=0 $company $condition $sql_cond $wo_condition $suplier_con  $style_ref_condition group by a.id, a.booking_date,a.booking_no, a.company_id, a.buyer_id, a.supplier_id, a.item_category order by a.id DESC";
    }elseif($item_catagory==3){

        $sql= "SELECT a.id, a.booking_date, a.company_id, a.buyer_id, a.item_category, a.supplier_id, a.booking_no from wo_booking_mst a, wo_booking_dtls b, wo_po_details_master c where a.id=b.BOOKING_MST_ID and b.job_no=c.job_no   and a.booking_type=1  and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and a.entry_form=271 and a.item_category=3 $company $wo_condition $condition $suplier_con $style_ref_condition group by a.id, a.booking_date, a.company_id, a.buyer_id, a.item_category, a.supplier_id, a.booking_no order by a.id DESC";
    }

//    echo $sql;

        $result = sql_select($sql);
    ?>
     <div style="width:920px; max-height:200px;">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="900" class="rpt_table" align="left">
        <thead>
                <th width="40">SL No</th>
                <th width="100">Item Category</th>
                <th width="100">Buyer</th>
                <th width="80">WO No</th>
                <th width="100">WO Date</th>
                <th width="80">Supplier</th>
                <th width="100">Wo value</th>
                <th width="100">PI No</th>
                <th width="100">BTB LC</th>
                <th width="100">Invoice No</th>
                <th width="100">Invoice Value</th>
            </thead>
        </table>
        <div style="width:920px; max-height:200px; overflow-y:scroll">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="900" class="rpt_table" id="list_view">
            <?
                $i=1;
                $pi_ammount="";
                foreach ($result as $row)
                {
                    $pi_number=$pi_data_arr[$row[csf('id')]]['pi_number'];
                    $lc_number=$pi_data_arr[$row[csf('id')]]['lc_number'];
                    $pi_ammount=$pi_data_arr[$row[csf('id')]]['amount'];
                    $invoice_no=$pi_data_arr[$row[csf('id')]]['invoice_no'];
                    $document_value=$pi_data_arr[$row[csf('id')]]['document_value'];

                //  echo $pi_data_arr[$row[csf('id')]]['amount'].'-------------';
                    if($i%2==0) $bgcolor="#E9F3FF"; 
                    else $bgcolor="#FFFFFF";        
                    $string=$row[csf('id')].'_'.$item_catagory.'_'.$row[csf('job_no')];          
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="tr_<? echo $i;?>" onClick="js_set_value('<? echo $i.'__'.$string;?>')">
                        <td width="40"  align="center"><? echo $i; ?></td>
                        <td width="100"  align="center"><p><? echo $item_category[$item_catagory];?></p></td>
                        <td width="100"  align="center"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></p></td>
                        <td width="80"  align="center"><p><? echo  $row[csf('booking_no')]; ?></p></td>
                        <td width="100" align="center"><p><? echo change_date_format($row[csf('booking_date')]); ?>&nbsp;</p></td>
                        <td width="80"  align="center"><p><? echo $suplier[$row[csf('supplier_id')]]; ?>&nbsp;</p></td>
                        <td width="100"  align="center"><p><? echo $pi_ammount;  ?>&nbsp;</p></td>
                        <td width="100"><p><? echo  $pi_number; ?></p></td>                    
                        <td width="100"><p><? echo $lc_number;  ?></p></td>                    
                        <td width="100"><p><? echo $invoice_no;  ?></p></td>                    
                        <td width="100"><p><? echo $document_value;  ?></p></td>                    
                    </tr>
                    <?
                    $i++;		
                } 
                ?>    
            </table>
        </div>
        <table>
              <div class="check_all_container">
		         <div style="width:20%; float:left" align="left">
		           <input type="checkbox" name="check_all" id="check_all" onclick="check_all_data()"> Check / Uncheck All
		         </div>
                <div style="width:70%; float:left" align="center">
                        
                     <input type="button" name="close" onClick="parent.salesPopup.hide();" class="formbutton" value="Close" style="width:100px" />
                    
                </div>
		   </div>    
        </table>
        </div>
  
    <?
    exit();  

}

if($action == 'system_id_popup') {
    echo load_html_head_contents('Search Yarn Dyeing Sales Order', '../../', 1, 0, $unicode);
    extract($_REQUEST);
?>
    <style>
        table.rpt_table tbody td input {
            width: 90%;
        }
    </style>
    <script>
        permission="<?php echo $permission; ?>";

        function js_set_value(id) {
            document.getElementById('selected_mst_id').value = id;
            parent.deliveryPopup.hide();
        }
    </script>
</head>
<body>
<div align="center" style="width:100%;" >
    <form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center" style="width: 100%;">
            <thead>
                <tr>
                    <th colspan="8"><?php echo create_drop_down('cbo_string_search_type', 163, $string_search_type, '', 1, '-- Searching Type --'); ?></th>
                </tr>
                <tr>
                    <th style="width:20%;">Company Name</th>
                    <th style="width:20%;">Work Order No</th>
                    <th style="width:25%;">System ID</th>
                    <th style="width:25%;">Debit Date</th>
                    <th style="width:20%;">
                        <input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width: 90%;" />
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr class="general">
                    <td>
                        <?php echo create_drop_down('cbo_company_name', 163, "select id, company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", 'id,company_name', 1, '-- Select Company --', $data, '', 1); ?>
                    </td>
                    <td>
                        <input class="text_boxes" type="text" name="txt_wo_no" id="txt_wo_no" />
                    </td>
                    <td style="display: none;">
                        <input class="text_boxes" type="text" name="txt_job_no" id="txt_job_no" />
                    </td>
                    <td>
                        <input class="text_boxes" type="text" name="txt_system_id" id="txt_system_id" />
                    </td>
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px"> To
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
                    </td>
                    <td align="center">
                        <input type="hidden" id="selected_mst_id">
                        <input type="button" name="btnSearchJob" class="formbutton" value="Show" onClick="show_list_view(document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_wo_no').value+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_system_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value, 'create_system_popup_list_view', 'search_div', 'supplier_debit_note_entry_controller', '')" />
                    </td>
                </tr>
                <tr>
                    <td  align="center" colspan="8" height="40" valign="middle">
                        <? echo load_month_buttons(1);  ?>
                    </td>
               </tr>
                <tr>
                    <td colspan="8" align="center" valign="top" id=""><div id="search_div"></div></td>
                </tr>
               
            </tbody>
        </table>
    </form>  
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?php
exit();
}

if($action == 'create_system_popup_list_view') {
    // echo $data;die;
    $data=explode('_', $data);
    // print_r( $data);
    $search_type = $data[0];
    $company_id = $data[1];
    $ord_no = $data[2];
    $yd_job = $data[3];
    $system_id = $data[4];
    $condition = '';

    if($company_id) {
        $condition.=" and a.company_id=$data[1]";
    } else {
        echo "<h3 style='margin-top: 10px;'>Please Select Company First.</h3>"; die;
    }

    if($search_type==0 || $search_type==4) { // no searching type or contents
        if ($ord_no!="") $condition.=" and b.work_order_no like '%$ord_no%'";
        if ($system_id!="") $condition.=" and a.sys_number like '%$system_id%'";
    } else if($search_type==1) { // exact
        if ($ord_no!="") $condition.=" and b.work_order_no ='$ord_no'";
        if ($system_id!="") $condition.=" and a.sys_number = '$system_id'";
    } else if($search_type==2) { // Starts with
        if ($ord_no!="") $condition.=" and b.work_order_no like '$ord_no%'";
        if ($system_id!="") $condition.=" and a.sys_number like '$system_id%'";
    } else if($search_type==3) { // Ends with
        if ($ord_no!="") $condition.=" and b.work_order_no like '%$ord_no'";
        if ($system_id!="") $condition.=" and a.sys_number like '%$system_id'";
    }

    if ($data[5]!="" &&  $data[6]!="") $date_con  = "and a.insert_date  between '".change_date_format($data[5], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[6], "yyyy-mm-dd", "-",1)."'"; else $date_con ="";

    if($db_type==0){ $sql_cond .=" and year(a.insert_date)=".$data[6].""; }
	else{ $sql_cond .=" and to_char(a.insert_date,'YYYY')=".$data[6].""; }

    $color_arr = return_library_array('select id, color_name from lib_color where status_active=1 and is_deleted=0', 'id', 'color_name');

   $sql = "SELECT a.id, a.sys_number,a.debit_note_entry_date, a.remarks, b.work_order_no from supplier_debit_note_entry_mst a, supplier_debit_note_entry_dtls b where a.id=b.mst_id and a.status_active=1 and a.company_id=$company_id $condition $date_con";

   // $arr=array(2=>$color_arr);

    echo create_list_view('list_view', 'System ID,Work Order No,Inspection date', '150,150', 500, 300, 0, $sql, 'js_set_value', 'id', '', 1, '0,0,0', '', 'sys_number,work_order_no,debit_note_entry_date', '', '', '0,0,0');

    exit();
}

if($action == 'populate_mst_data_from_search_popup') {
   // $data = explode('**', $data);
   $sql = "SELECT a.id, a.company_id, a.debit_note_entry_date, a.supplier_id, a.sys_number, a.debit_note_for,a.exchange_rate, a.remarks,a.issuing_bank from supplier_debit_note_entry_mst a where a.status_active=1 and a.id=$data";

    $result = sql_select($sql);
    echo "document.getElementById('cbo_company_name').value = '".$result[0][csf('company_id')]."';\n";
    echo "document.getElementById('txt_debat_note_date').value = '".$result[0][csf('debit_note_entry_date')]."';\n";
	echo "document.getElementById('cbo_supplier_name').value = '".$result[0][csf('supplier_id')]."';\n"; 
    echo "document.getElementById('cbo_debit_note_for').value = '".$result[0][csf('debit_note_for')]."';\n";
    echo "document.getElementById('txt_system_id').value = '".$result[0][csf('sys_number')]."';\n";
    echo "document.getElementById('txt_exchange_rate').value = '".$result[0][csf('exchange_rate')]."';\n";
    echo "document.getElementById('txt_remarks').value = '".$result[0][csf('remarks')]."';\n";
    echo "document.getElementById('hdnupdateid').value = '".$result[0][csf('id')]."';\n";
    echo "document.getElementById('txt_issuing_bank').value = '".$result[0][csf('issuing_bank')]."';\n";
}

if($action == 'populate_dtls_data_from_search_popup')
 {
	$data = explode('**', $data);
//    print_r($data );
    $all_data="";
    $all_data=$data[0];
    $all_datas=explode(",",$all_data);
    foreach($all_datas as $row){
        list($id,$catagory_id)=explode("_",$row);
        $dtlsIdArrMst[$id]=$id;
        $dtlsIdArrDtls[$ord_dtls]=$ord_dtls;
    }

   $issue_ban_name=return_library_array( "select id, branch_name from lib_bank",'id','branch_name');

    $pi_data=sql_select("SELECT a.pi_number, SUM (b.amount)AS amount,b.work_order_id,c.lc_number,d.invoice_no,d.document_value FROM com_pi_master_details a, com_pi_item_details b LEFT JOIN com_btb_lc_master_details c ON c.pi_id = TO_CHAR(b.pi_id) LEFT JOIN com_import_invoice_mst d ON TO_CHAR(c.id) = d.BTB_LC_ID WHERE a.id = b.pi_id GROUP BY a.pi_number, b.work_order_id,c.lc_number,d.invoice_no, d.document_value");
    
    $pi_data_arr=array();
    foreach( $pi_data as $row){
        $pi_data_arr[$row[csf('work_order_id')]]['pi_number']=$row[csf('pi_number')];
        $pi_data_arr[$row[csf('work_order_id')]]['lc_number']=$row[csf('lc_number')];
        $pi_data_arr[$row[csf('work_order_id')]]['amount']=$row[csf('amount')];        
        $pi_data_arr[$row[csf('work_order_id')]]['invoice_no']=$row[csf('invoice_no')];        
    }

    if($catagory_id==2){

        $sql= "SELECT a.id, a.company_id, a.buyer_id, a.item_category, a.supplier_id, a.booking_no
        from wo_booking_mst a, wo_po_details_master b, wo_po_break_down c, wo_po_details_mas_set_details d where 
        a.job_no=b.job_no and a.job_no=c.job_no_mst and b.job_no=c.job_no_mst and b.job_no=d.job_no and a.booking_type=1 and a.is_short=2 and a.status_active=1 and a.entry_form=118 and a.item_category=2 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.id in(".implode(',',$dtlsIdArrMst).") group by a.id, a.company_id, a.buyer_id, a.item_category, a.supplier_id, a.booking_no  order by a.id DESC";
    }
    elseif($catagory_id==4)
    {
        $sql="SELECT a.id, a.booking_no, a.company_id, a.supplier_id,a.buyer_id, a.currency_id from wo_booking_mst a, wo_booking_dtls b, wo_po_details_master c, wo_po_break_down d where a.booking_no=b.booking_no and b.job_no=c.job_no and b.job_no=d.job_no_mst and c.id=d.job_id and b.po_break_down_id=d.id and a.booking_type=2 and a.item_category=4 and a.entry_form=87 and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and a.id in(".implode(',',$dtlsIdArrMst).") $sql_cond $company $condition   group by a.id,  a.booking_no, a.company_id, a.supplier_id,a.buyer_id,a.currency_id order by a.id DESC";
    }
    elseif($catagory_id==1){
        $sql = "SELECT a.id, a.wo_number as booking_no, a.supplier_id, a.item_category
        from wo_non_order_info_mst a, wo_non_order_info_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and a.entry_form=144 and a.item_category=1 and a.id in(".implode(',',$dtlsIdArrMst).") $company_name $sql_cond $condition group by a.id, a.wo_number, a.supplier_id,a.item_category order by a.id desc";

    }elseif($catagory_id==25){
        $sql = "SELECT a.id, company_id, a.buyer_id, a.supplier_id, a.booking_no from wo_booking_mst a, wo_booking_dtls b where  a.id=b.booking_mst_id and a.booking_type=6 and a.entry_form=201 and a.item_category=25 and  a.status_active=1  and a.is_deleted=0 and  b.status_active=1  and b.is_deleted=0 and a.id in(".implode(',',$dtlsIdArrMst).") group by a.id, company_id,a.buyer_id, a.supplier_id,a.booking_no order by a.id DESC";
    }elseif($catagory_id==3){ 
        $sql= "SELECT a.id, a.company_id, a.buyer_id, a.item_category, a.booking_no from wo_booking_mst a, wo_booking_dtls b where a.id=b.booking_mst_id and a.item_category=3  and a.booking_type=1 and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and a.entry_form=271 and a.id in(".implode(',',$dtlsIdArrMst).") group by a.id, a.company_id, a.buyer_id, a.item_category, a.booking_no order by a.id DESC";
    }
    //   echo  $sql;//die;
	$result = sql_select($sql);
    ?>
        <?php
        foreach($result as $row) {

            $pi_number=$pi_data_arr[$row[csf('id')]]['pi_number'];
            $lc_number=$pi_data_arr[$row[csf('id')]]['lc_number'];
            $pi_ammount=$pi_data_arr[$row[csf('id')]]['amount'];
            $invoice_no=$pi_data_arr[$row[csf('id')]]['invoice_no'];
            ?>
            <tr>
               <td align="center">
                    <input name="txtdebatNoteDetails[]" id="txtdebatNoteDetails_<?php echo $sl; ?>" type="text" class="text_boxes" placeholder="write"   style="width:100px"/>
                </td>
                <td align="center">
                    <input name="txtWoOrder[]" id="txtWoOrder_<?php echo $sl; ?>" type="text" class="text_boxes" placeholder="Double Click" value="<?php echo $row[csf('booking_no')]; ?>" readonly style="width:130px" />
                </td>
                <td align="center">
                    <input name="txtLcNumber[]" id="txtLcNumber_<?php echo $sl; ?>" type="text" class="text_boxes" placeholder="Display" value="<?php echo  $lc_number; ?>" readonly style="width:100px"/>
                </td>
                <td align="center">
                    <input name="txtInvoiceNo[]" id="txtInvoiceNo_<?php echo $sl; ?>" type="text" class="text_boxes" readonly placeholder="Display" value="<?php echo  $invoice_no ?>" style="width:100px" />                   
                </td>

                <td align="center">
                    <input name="txtwoAmmount[]" id="txtwoAmmount_<?php echo $sl; ?>" type="text" class="text_boxes" placeholder="Display" value="<?php echo  $pi_ammount; ?>" readonly style="width:100px"/>
                </td>
                <td align="center">
                    <input name="noteAmmount[]" id="noteAmmount_<?php echo $sl; ?>" oninput="calculateTotal()" type="number" class="text_boxes" placeholder="write"   style="width:100px"/>
                    <input name="txtBookingId[]" id="txtBookingId_<?php echo $sl; ?>" type="hidden" value="<?php echo $row[csf('id')]; ?>" />
                </td>            
            </tr>
            <?php
            $sl++;
        }
        ?>
           <tr>
            <td align="right" colspan="5"><b>Total Amount:</b></td>
            <td align="center"> <input id="total_note_ammount" name="total_note_ammount" type="text" class="text_boxes" placeholder="Display"  style="width:100px"/></td>
          </tr>
        <?

	exit();
}

if($action == 'populate_dtls_data_from_search_popup_update')
 {
	$data = explode('**', $data);
   //print_r($data );
    $mst_id=$data[0];
    $issue_ban_name=return_library_array( "select id, branch_name from lib_bank",'id','branch_name');
  
        $sql = "SELECT id, work_order_no, lc_no, issuing_bank_id, invoice_no,wo_currency,work_order_value,debit_note_details, dr_note_amount,wo_currency,booking_id  from supplier_debit_note_entry_dtls where mst_id=$mst_id and STATUS_ACTIVE=1 order by id desc";
      //echo  $sql;
	$result = sql_select($sql);
    ?>
        <?php
        foreach($result as $row) {

            ?>
            <tr>
                <td align="center">
                    <input name="txtdebatNoteDetails[]" id="txtdebatNoteDetails_<?php echo $sl; ?>" type="text" class="text_boxes" placeholder="write" value="<?= $row[csf("debit_note_details")]?>"   style="width:100px"/>
                </td>
                <td align="center">
                    <input name="txtWoOrder[]" id="txtWoOrder_<?php echo $sl; ?>" type="text" class="text_boxes" placeholder="Double Click" value="<?php echo $row[csf('work_order_no')]; ?>" readonly style="width:130px" />
                </td>
                <td align="center">
                    <input name="txtLcNumber[]" id="txtLcNumber_<?php echo $sl; ?>" type="text" class="text_boxes" placeholder="Display" value="<?php echo  $row[csf('lc_no')]; ?>" readonly style="width:100px"/>
                </td>
                <td align="center">
                    <input name="txtInvoiceNo[]" id="txtInvoiceNo_<?php echo $sl; ?>" type="text" class="text_boxes" readonly placeholder="Display" value="<?php echo  $row[csf('invoice_no')] ?>" style="width:100px" />                   
                </td>
                <td align="center">
                    <input name="txtwoAmmount[]" id="txtwoAmmount_<?php echo $sl; ?>" type="text" class="text_boxes" placeholder="Display" value="<?php echo  $row[csf('work_order_value')]; ?>" readonly style="width:100px"/>
                </td>
                <td align="center"> 
                    <input name="noteAmmount[]" id="noteAmmount_<?php echo $sl; ?>" type="number" value="<?= $row[csf("dr_note_amount")]?>" class="text_boxes" placeholder="write"   style="width:100px"/>
                    <input name="txtBookingId[]" id="txtBookingId_<?php echo $sl; ?>" type="hidden" value="<?php echo $row[csf('booking_id')]; ?>" />
                    <input name="txtHiddenDtlsId[]" id="txtHiddenDtlsId_<?php echo $sl; ?>" type="hidden" value="<?php echo $row[csf('id')]; ?>" />
                </td>            
            </tr>
            <?php
            $sl++;
            $total+=$row[csf("dr_note_amount")];
        }
        ?>
           <tr>
            <td align="right" colspan="5"><b>Total Amount:</b></td>
            <td align="center">  <input  style="width:100px"   class="text_boxes" type="text" value="<?php echo  $total; ?>" /></td>
          </tr>
        <?

	exit();
}

if($action=='save_update_delete') 
{
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process ));

    if ($operation==0) {
        // save here
        $con = connect();
        $flag = 1;
        $add_comma = false;
        $field_array_mst = '';
        $data_array_mst = '';
        $field_array_dtls = '';
        $data_array_dtls = '';
        $entryForm = 620;
        $con = connect();
        $mstId = return_next_id('id', 'supplier_debit_note_entry_mst', 1);
        $dtls_is_first = return_next_id('id', 'supplier_debit_note_entry_dtls', 1);
       
		 if($db_type==0) $insert_date_con="and YEAR(insert_date)=".date('Y',time())."";
        else if($db_type==2) $insert_date_con="and TO_CHAR(insert_date,'YYYY')=".date('Y',time())."";
        //$hdnOrderId = str_replace("'", '', $hdnOrderId);
      $new_system_id=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name),'', 'SDNE' , date("Y",time()), 5, "select id,sys_number_prefix,sys_number_prefix_num from supplier_debit_note_entry_mst where company_id=$cbo_company_name and entry_form=620 $insert_date_con order by id desc ", "sys_number_prefix", "sys_number_prefix_num" ));
  
        //$txt_debat_note_date=change_date_format($txt_debat_note_date, "", "",1);
        $txt_debat_note_date=change_date_format(str_replace("'",'',$txt_debat_note_date), "", "",1);

        if($db_type==0) {
            mysql_query("BEGIN");
        }
        $field_array_mst = 'id,entry_form, sys_number, sys_number_prefix, sys_number_prefix_num,  company_id, debit_note_entry_date, supplier_id, Debit_Note_For, debit_note_currency, Exchange_Rate,pay_mode, remarks,issuing_bank, inserted_by, insert_date,is_deleted, status_active';
        $data_array_mst="(".$mstId.", ".$entryForm.", '".$new_system_id[0]."', '".$new_system_id[1]."', '".$new_system_id[2]."', ".$cbo_company_name.", '".$txt_debat_note_date."', ".$cbo_supplier_name.", ".$cbo_debit_note_for.", ".$cbo_currency.", '".$txt_exchange_rate."', ".$cbo_pay_mode.", '".$txt_remarks."', '".$txt_issuing_bank."', ".$_SESSION['logic_erp']['user_id'].", '".$pc_date_time."','0',1)";

        $field_array_dtls = 'id, mst_id, Work_Order_No, Booking_id, lc_no,invoice_no, Debit_Note_Details,work_order_value,dr_note_amount,inserted_by, insert_date,is_deleted, status_active';
        for($i=1; $i<=$total_row; $i++) {
            $txtWoOrder      = 'txtWoOrder_'.$i;
            $txtLcNumber      = 'txtLcNumber_'.$i;
            $txtBookingId     = 'txtBookingId_'.$i;
            $txtInvoiceNo      = 'txtInvoiceNo_'.$i;
            $txtwoAmmount           = 'txtwoAmmount_'.$i;
            $txtdebatNoteDetails     = 'txtdebatNoteDetails_'.$i;
            $noteAmmount     = 'noteAmmount_'.$i;

            $data_array_dtls .= $add_comma ? ',' : ''; // if $add_comma is true, add a comma in the end of $data_array_dtls
           
            if($noteAmmount!=""){
            $data_array_dtls .= "(".$dtls_is_first.",".$mstId.",".$$txtWoOrder.",".$$txtBookingId.",".$$txtLcNumber.",".$$txtInvoiceNo.",".$$txtdebatNoteDetails.",".$$txtwoAmmount.",".$$noteAmmount.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','0',1)";
            }
            
            $add_comma = true; // first entry is done. add a comma for next entries
            $dtls_is_first++; // increment details id by 1
        }

        // echo "10**insert into supplier_debit_note_entry_mst(".$field_array_mst.") values ".$data_array_mst; die;
        $rID = sql_insert('supplier_debit_note_entry_mst', $field_array_mst, $data_array_mst, 0);

        $flag = ($flag && $rID);    // return true if $flag is true and mst table insert is successful

        // echo $flag, $rID;die;
        // echo "10**insert into supplier_debit_note_entry_dtls(".$field_array_dtls.") values ".$data_array_dtls; die;
        $rID2 = sql_insert('supplier_debit_note_entry_dtls', $field_array_dtls, $data_array_dtls, 0);

        // echo '10**'.$rID.'**'.$rID2;die;

        $flag = ($flag && $rID2);   // return true if $flag is true and dtls table insert is successful

        if($db_type==0) {
            if($flag) {
                mysql_query("COMMIT");
                echo '0**'.$new_system_id[0].'**'.$mstId;
            } else {
                mysql_query("ROLLBACK");
                echo '10**'.$hdnOrderId;
            }
        }
        else if($db_type==2) {
            if($flag) {
                oci_commit($con);
                echo '0**'.$new_system_id[0].'**'.$mstId;
            } else {
                oci_rollback($con);
                echo '10**'.$hdnOrderId;
            }
        }

        disconnect($con);
        die;
    }

    else if($operation == 1) {
        // update here
        $flag = 1;
        $id_arr = array();
        $con = connect();
        $hdn_update_id = str_replace("'", '', $hdn_update_id);
        $txt_system_id = str_replace("'", '', $txt_system_id);

        if($db_type==0) mysql_query("BEGIN");
        $$txt_debat_note=change_date_format($txt_debat_note_date, "", "",1);

    
        $field_array_mst = 'debit_note_entry_date*supplier_id*Debit_Note_For*debit_note_currency*Exchange_Rate*pay_mode*remarks*issuing_bank*updated_by*update_date';
        $data_array_mst="'".$$txt_debat_note."'*".$cbo_supplier_name."*".$cbo_debit_note_for."*".$cbo_currency."*'".$txt_exchange_rate."'*".$cbo_pay_mode."*'".$txt_remarks."'*'".$txt_issuing_bank."'*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'";

        // txt_issuing_bank
       
        $field_array_dtls = 'Work_Order_No*Booking_id*lc_no*invoice_no*Debit_Note_Details*work_order_value*dr_note_amount*updated_by*update_date';

        // echo "10**".$pc_date_time;oci_rollback($con); disconnect($con);die;

        $txt_update_id=str_replace("'",'',$hdnupdateid);
        for($i = 1; $i <= $total_row; $i++) {
            $txtWoOrder            = 'txtWoOrder_'.$i;
            $txtLcNumber           = 'txtLcNumber_'.$i;
            $txtBookingId          = 'txtBookingId_'.$i;
            $txtInvoiceNo          = 'txtInvoiceNo_'.$i;
            $txtwoAmmount           = 'txtwoAmmount_'.$i;
            $txtdebatNoteDetails     = 'txtdebatNoteDetails_'.$i;
            $noteAmmount             = 'noteAmmount_'.$i;
            $txtHiddenDtlsId     = 'txtHiddenDtlsId_'.$i;

            $data_array_dtls[str_replace("'", '', $$txtHiddenDtlsId)] =explode("*",("".$$txtWoOrder."*".$$txtBookingId."*".$$txtLcNumber."*".$$txtInvoiceNo."*".$$txtdebatNoteDetails."*".$$txtwoAmmount."*".$$noteAmmount."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));

            $id_arr[]=str_replace("'", '', $$txtHiddenDtlsId);
        }

        // echo sql_update('supplier_debit_note_entry_mst', $field_array_mst, $data_array_mst, 'id', $txt_update_id, 0);
        $rID = sql_update('supplier_debit_note_entry_mst', $field_array_mst, $data_array_mst, 'id', $txt_update_id, 0);

        //echo $rID;die;

        $flag = ($flag && $rID);    // return true if $flag is true and mst table update is successful

     // echo '10**' . bulk_update_sql_statement('supplier_debit_note_entry_dtls', 'id', $field_array_dtls, $data_array_dtls, $id_arr); disconnect($con);die;

        $rID2 = execute_query(bulk_update_sql_statement('supplier_debit_note_entry_dtls', 'id', $field_array_dtls, $data_array_dtls, $id_arr), 1);

        $flag = ($flag && $rID2);   // return true if $flag is true and dtls table update is successful
    //    echo '10**'.$rID.'**'.$rID2;die;
        if($db_type==0) {
            if($flag) {
                mysql_query('COMMIT');
                echo '1**'.$txt_system_id.'**'.$txt_update_id;
            } else {
                mysql_query('ROLLBACK');
                echo '6**'.$txt_system_id.'**'.$txt_update_id;
            }
        }
        else if($db_type==2) {
            if($flag) {
                oci_commit($con);
                echo '1**'.$txt_system_id.'**'.$txt_update_id;
            } else {
                oci_rollback($con);
                echo '6**'.$txt_system_id.'**'.$txt_update_id;
            }
        }

        disconnect($con);
        die;
    }
    else if($operation == 2) // Delete here
    { 
        $con = connect();
        $txt_update_id=str_replace("'",'',$hdnupdateid);

        $field_array_mst="updated_by*update_date*status_active*is_deleted";
        $data_array_mst="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";

        $field_array_dtls="updated_by*update_date*status_active*is_deleted";
        $data_array_dtls="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
        
       
        $rID2=sql_update("supplier_debit_note_entry_dtls",$field_array_dtls,$data_array_dtls,"mst_id",$txt_update_id,0);
        $rID1=sql_update("supplier_debit_note_entry_mst",$field_array_mst,$data_array_mst,"id",$txt_update_id,0);
        // echo "67**".$rID1; disconnect($con); die;
        // echo "10**".$rID1."_".$rID2; disconnect($con); die;

        if($db_type==2) {
            if($rID2 && $rID1) {
                oci_commit($con);
                echo '2**'.$txt_system_id.'**'.$txt_update_id;
            } else {
                oci_rollback($con);
                echo '10**'.$txt_system_id.'**'.$txt_update_id;
            }
        }
        disconnect($con);
        die;
    }

    exit();
}

if($action=="issuing_bank_popup")
{
	echo load_html_head_contents("Invoice Additional Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name");
	//$data=explode('*',$data);

	?>
	<script>
		var issue_bank_info='<?  echo $data; ?>';
		if(issue_bank_info != "")
		{
			issue_bank_info=issue_bank_info.split('_');

			$(document).ready(function(e) {
				$('#txt_bank_name').val( issue_bank_info[0]);
				$('#txt_to').val( issue_bank_info[1]);
				$('#txt_attention').val( issue_bank_info[2]);
				$('#txt_address').val( issue_bank_info[3]);
				$('#txt_accounts_number').val( issue_bank_info[4]);
				$('#txt_swift').val( issue_bank_info[5]);
			});
		}
		function submit_additional_info()
		{
			var issue_bank =   $('#txt_bank_name').val()+ '_'+$('#txt_to').val()+ '_'+$('#txt_attention').val()+ '_'+$('#txt_address').val()+ '_'+$('#txt_accounts_number').val()+ '_'+$('#txt_swift').val();
			var issue_bank_arr=issue_bank_data="";

			issue_bank_arr=issue_bank.split("_");
			for(var i=0;i<issue_bank_arr.length;i++)
			{
				issue_bank_data+=issue_bank_arr[i];
			}
			if(issue_bank_data!="") issue_bank=issue_bank; else issue_bank=issue_bank_data;
			$('#txt_hidden_issue_bank').val( issue_bank );
			parent.emailwindow.hide();
		}
    </script>
	</head>
	<body>
		<div align="center" style="width:100%;" >
		<form name="invoiceadditionalinfo_1"  id="invoiceadditionalinfo_1" autocomplete="off">
			<table width="690" cellspacing="0" cellpadding="0" class="rpt_table" align="center" border="1" rules="all">
				<input type="hidden" name="txt_hidden_issue_bank" id="txt_hidden_issue_bank" value="">

				<tr>
					<td width="150" align="right">Bank Name: &nbsp;</td>
					<td width="200">
						<input type="text" name="txt_bank_name" id="txt_bank_name" value="" class="text_boxes" style="width:190px;"/>
					</td>
					<td width="150" align="right">To: &nbsp;</td>
					<td>
						<input type="text" name="txt_to" id="txt_to" class="text_boxes" style="width:190px;"/>
					</td>
				</tr>
				<tr>
					<td align="right">Attention: &nbsp;</td>
					<td><input type="text" name="txt_attention" id="txt_attention" value="" class="text_boxes" style="width:190px;"/></td>
					<td align="right">Address: &nbsp;</td>
					<td><input type="text" name="txt_address" id="txt_address" value="" class="text_boxes" style="width:190px;"/></td>
				</tr>
				<tr>
					<td align="right">Accounts Number: &nbsp;</td>
					<td><input type="text" name="txt_accounts_number" id="txt_accounts_number" value="" class="text_boxes_numeric" style="width:190px;"/></td>
					<td align="right">SWIFT: &nbsp;</td>
					<td><input type="text" name="txt_swift" id="txt_swift" value="" class="text_boxes_numeric" style="width:190px;"/></td>
				</tr>
				<tr>
					<td align="center" colspan="4" class="button_container">
						<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="submit_additional_info();" style="width:100px" />
					</td>
				</tr>
			</table>
		</form>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="supplier_debit_note_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
        // print_r($data);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$trim_group_arr=return_library_array( "select id, item_name from lib_item_group where item_category=4",'id','item_name');
	$leader_name_arr=return_library_array( "select id, team_leader_name from lib_marketing_team",'id','team_leader_name');
	$member_name_arr=return_library_array( "select id, team_member_name from lib_mkt_team_member_info",'id','team_member_name');
	$supplier_name_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');

	$source_for_order = array(1 => 'In-House', 2 => 'Sub-Contract');
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
    $buyerArr = return_library_array("select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0 ","id","buyer_name");

	$sql_mst="SELECT company_id,debit_note_entry_date,supplier_id,debit_note_for,pay_mode,debit_note_currency,exchange_rate,sys_number,issuing_bank from supplier_debit_note_entry_mst where id=$data[1] and entry_form=620 and status_active=1";
	$dataArray=sql_select($sql_mst);
	$inserted_by=$dataArray[0]['INSERTED_BY'];
	$com_dtls = fnc_company_location_address($data[0], 0, 2);
	//echo "<pre>";

    $issuebank = explode("_", $dataArray[0]['ISSUING_BANK']);

    $sql = "SELECT WORK_ORDER_NO, INVOICE_NO, DEBIT_NOTE_DETAILS,WORK_ORDER_VALUE,DR_NOTE_AMOUNT,BOOKING_ID  from supplier_debit_note_entry_dtls where mst_id =$data[1] and status_active=1 and is_deleted=0 order by id ASC";
    $qry_result=sql_select($sql);

   

    if($data[2]==4){
       $sql_booking_dat="SELECT a.id , a.booking_date, a.booking_no, a.company_id, a.buyer_id ,b.job_no, c.style_ref_no,a.item_category  from wo_booking_mst a, wo_booking_dtls b, wo_po_details_master c, wo_po_break_down d where a.booking_no=b.booking_no and b.job_no=c.job_no and b.job_no=d.job_no_mst and c.id=d.job_id and b.po_break_down_id=d.id and a.booking_type=2 and a.entry_form=87 and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and a.company_id=$data[0] and a.item_category=$data[2]";
    } else if($data[2]==2){
       $sql_booking_dat="SELECT a.id, a.booking_date,a.booking_no, a.company_id, a.buyer_id, a.job_no , b.style_ref_no, a.item_category from wo_booking_mst a, wo_po_details_master b, wo_po_break_down c, wo_po_details_mas_set_details d where 
       a.job_no=b.job_no and a.job_no=c.job_no_mst and b.job_no=c.job_no_mst and b.job_no=d.job_no and a.booking_type=1 and a.is_short=2 and a.status_active=1 and a.entry_form=118 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_id=$data[0] and a.item_category=$data[2]";
    }else if($data[2]==1){
        $sql_booking_dat="SELECT a.id, a.wo_date as booking_date, a.wo_number as booking_no ,a.company_name as company_id, b.buyer_id as buyer_id , b.job_no, b.style_no as style_ref_no, b.ITEM_CATEGORY_ID as item_category
        from wo_non_order_info_mst a,wo_non_order_info_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and a.entry_form=144 and a.company_name=$data[0] and b.ITEM_CATEGORY_ID=$data[2]";
    }else if($data[2]==25){
        $sql_booking_dat=" SELECT a.id, a.booking_date,a.booking_no, a.company_id, a.buyer_id, b.job_no , c.style_ref_no, a.item_category from wo_booking_mst a, wo_booking_dtls b, wo_po_details_master c where 
       a.booking_no=b.booking_no and b.job_no=c.job_no  and a.entry_form=201 and a.status_active=1  and a.is_deleted=0  and  b.status_active=1  and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_id=$data[0] and a.item_category=$data[2]";
     } else if($data[2]==3){
        $sql_booking_dat="SELECT a.id, a.booking_date,a.booking_no, a.company_id, a.buyer_id, a.job_no , b.style_ref_no, a.item_category from wo_booking_mst a, wo_po_details_master b, wo_po_break_down c, wo_po_details_mas_set_details d where a.job_no=b.job_no and a.job_no=c.job_no_mst and b.job_no=c.job_no_mst and b.job_no=d.job_no and a.booking_type=1  and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form=118 and a.company_id=$data[0] and a.item_category=$data[2]";
    }
    // echo $sql_booking_dat;

    $sql_booking_arr=sql_select($sql_booking_dat);
    
    $booking_arr=array();
    foreach($sql_booking_arr as $row){
        $booking_arr[$row[csf('id')]]['booking_date']=$row[csf("booking_date")];
        $booking_arr[$row[csf('id')]]['buyer_id']=$row[csf("buyer_id")];
        $booking_arr[$row[csf('id')]]['booking_no']=$row[csf("booking_no")];
        $booking_arr[$row[csf('id')]]['item_category']=$row[csf("item_category")];
        $booking_arr[$row[csf('id')]]['job_no']=$row[csf("job_no")];
        $booking_arr[$row[csf('id')]]['style_ref_no']=$row[csf("style_ref_no")];
    }
    // print_r($booking_arr);die;

    $pi_data=sql_select("SELECT a.pi_number, SUM (b.amount)AS amount,b.work_order_id,c.lc_number,d.invoice_no,d.document_value FROM com_pi_master_details a, com_pi_item_details b LEFT JOIN com_btb_lc_master_details c ON c.pi_id = TO_CHAR(b.pi_id) LEFT JOIN com_import_invoice_mst d ON TO_CHAR(c.id) = d.BTB_LC_ID WHERE a.id = b.pi_id and a.importer_id=$data[0] GROUP BY a.pi_number,
    b.work_order_id,c.lc_number,d.invoice_no, d.document_value");
    
    $pi_data_array=array();
    foreach( $pi_data as $row){
        $pi_data_array[$row[csf('work_order_id')]]['pi_number']=$row[csf('pi_number')];
        $pi_data_array[$row[csf('work_order_id')]]['lc_number']=$row[csf('lc_number')];
        $pi_data_array[$row[csf('work_order_id')]]['amount']=$row[csf('amount')];     
        $pi_data_array[$row[csf('work_order_id')]]['invoice_no']=$row[csf('invoice_no')];     
        $pi_data_array[$row[csf('work_order_id')]]['document_value']=$row[csf('document_value')];     
    }
	?>
	<style type="text/css">
		td.make_bold {
	  		font-weight: 900;
		}
	</style>
	<div style="width:1100px;">
		<table width="1100" cellspacing="0" align="center" border="0">
			<!-- <tr>
            	<td colspan="6" align="center"  style="font-size:xx-large; text-align:center;"><strong >Pad Print</strong>
        	</tr> -->
	        <tr>
	            <td colspan="6" style="font-size: 40px;" align="center"><? echo "Debit Note"; ?> </td>
	        </tr>
		</table>
		<br>
		<table width="1100" cellspacing="0" align="center" border="0">
			
			<tr>			
				<td width="120" class="make_bold">Debit Note TO: </td> <td width="275" class="make_bold"></td>
				<td width="120" class="make_bold">Debit Note No:</td> <td width="275" class="make_bold"><? echo $dataArray[0][csf('sys_number')];?></td>
				
			</tr>
			<tr>
				<td width="120">Supplier Name : </td> <td width="275"><? echo $supplier_name_arr[$dataArray[0][csf('supplier_id')]]; ?></td>
                <td width="120" > Currency : </td> <td width="275"><? echo $currency[$dataArray[0][csf('debit_note_currency')]]; ?></td>
			</tr>
            <tr>
            <td width="120">Address : </td> <td width="275"><? echo $com_dtls[1]; ?></td>
				<td width="120">Debit Note Date:  </td> <td width="275"><? echo change_date_format($dataArray[0][csf('debit_note_entry_date')]); ?></td>
            </tr>
                <td width="120" class="make_bold">Payment to</td> <td width="275"></td>
				<td width="120"> Pay Mode : </td> <td width="275"><? echo $pay_mode[$dataArray[0][csf('pay_mode')]]; ?></td>
            </tr>
            </tr>
                <td width="120">Bank Name:</td> <td width="275"><?=$issuebank[0]?></td>
				<td width="120">Exchange Rate :</td> <td width="275"> <? echo $dataArray[0][csf('exchange_rate')];?></td>
            </tr>
            </tr>
                <td width="120">Addres:</td> <td width="275"><? echo $issuebank[3];?></td>
				<td width="120"> </td> <td width="275"></td>
            </tr> </tr>
                <td width="120">Account No:</td> <td width="275"><? echo $issuebank[4];?></td>
				<td width="120"> </td> <td width="275"></td>
            </tr> </tr>
                <td width="120">SWIFT:</td> <td width="275"> <? echo $issuebank[5];?></td>
				<td width="120"> </td> <td width="275"></td>
            </tr>
		</table>
        <br>
        <table width="1100" cellspacing="0" align="center" border="1">
            <tr bgcolor="#98AFC7">
                <td  align="center" class="make_bold" colspan="2"> Reason for Debit Note</td>
            </tr>
            <? 
            foreach ($qry_result as $row) {
            ?>
            <tr>
                <td align="center"><?= $row['DEBIT_NOTE_DETAILS']?></td>
            </tr>
            <?
            $i++;
            }
            ?>
        </table>
		<br>
		<div style="width:100%;">
			<table align="left" cellspacing="0" width="1100"  border="1" rules="all" class="rpt_table"  >
				<thead>
					<tr bgcolor="#98AFC7">
		        		<th width="34">Sl No.</th>
		        		<th width="110">Buye & Style.</th>
		                <th width="110" >DESCRIPTION.</th>
		                <th width="110">WO Value</th>
		                <th width="110">DAMOUNT</th>
		        	</tr>
				</thead>
				<tbody>
				<?
				$tblRow=1; $i=1;
					foreach($qry_result as $row)
					{
                        if($i%2==0) $bgcolor="#E9F3FF"; 
                        else $bgcolor="#FFFFFF"; 
                        // echo $buyerArr[$booking_arr[$row['BOOKING_ID']]['buyer_id']]."__".$row['BOOKING_ID'];
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
							<td style="word-break:break-all" width="35"><?  echo $i; ?></td>
							<td style="word-break:break-all" width="250"><?  echo "Buyer: ".$buyerArr[$booking_arr[$row['BOOKING_ID']]['buyer_id']]."<br>"."Job: ".$booking_arr[$row['BOOKING_ID']]['job_no']."<br>"."Style: ".$booking_arr[$row['BOOKING_ID']]['style_ref_no']; ?></td>

			                <td style="word-break:break-all" width="200"><? echo "Work Worder: ".$booking_arr[$row['BOOKING_ID']]['booking_no']."<br>"."Catagory: ".$item_category[$booking_arr[$row['BOOKING_ID']]['item_category']]."<br>"."Pi No: ".$pi_data_array[$row['BOOKING_ID']]['pi_number']."<br>"."Lc No: ".$pi_data_array[$row['BOOKING_ID']]['lc_number']."<br>"."Invoice No: ".$pi_data_array[$row['BOOKING_ID']]['invoice_no']; ?></td>
			                <td style="word-break:break-all" width="110"><?  echo $row['WORK_ORDER_VALUE'] ; ?></td>
			                <td style="word-break:break-all" width="110" align="right"><?  echo $row['DR_NOTE_AMOUNT'] ; ?></td>
						</tr>
						<?
						$tblRow++; 
                        $total+=$row['DR_NOTE_AMOUNT'];
					}
					?> 
					<tr>
						<td colspan="4" align="right"><strong>Total:</strong></td>
						<td align="right" class="make_bold"><p><?  echo number_format($total,4) ; ?></p></td>
					</tr><? 
				
				?>
				</table>
			</div>
		<br>
	</div>

     <br>
     <br>
    <table align="left" cellspacing="0" width="1100" class="rpt_table" >
        <tr>
            <td colspan="2"><b>IN WORD: <? echo number_to_words(number_format($total,2,'.',''),$inWordTxt);?></b></td>
        </tr>
    </table>
    <br>
    <table align="center" cellspacing="0" width="1100"  height='400' border="0"  class="rpt_table" >
        <tr>
            <td width="400">For and on behalf of</td>
            <td width="400">Please Sign with chop and return</td>
        </tr>
        <tr>
            <td width="400"></td>
            <td width="400"></td>
        </tr>
        <tr>
            <td width="400">Company Name</td>
            <td width="400">Supplier Name</td>
        </tr>
        <tr>
            <td width="400">Authorised Signature</td>
            <td width="400">Authorised Signature</td>
        </tr>
    </table>
    <br>
    <br>
</div>
<?
exit();
}
?>