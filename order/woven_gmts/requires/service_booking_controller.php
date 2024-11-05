<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
require_once('../../../includes/class4/class.conditions.php');
require_once('../../../includes/class4/class.reports.php');
require_once('../../../includes/class4/class.conversions.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$permission=$_SESSION['page_permission'];
$user_id=$_SESSION['logic_erp']['user_id'];

//---------------------------------------------------- Start---------------------------------------------------------------------------
//$po_number=return_library_array( "select id,po_number from wo_po_break_down", "id", "po_number"  );
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name"  );
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
$trim_group= return_library_array( "select id, item_name from lib_item_group",'id','item_name');

$rate_type_mapping=array(1=>2,31=>3,32=>3,39=>3,60=>3,61=>3,62=>3,63=>3,137=>3,75=>3,86=>3,87=>3,25=>4,26=>4,34=>4,65=>4,66=>4,67=>4,68=>4,69=>4,70=>4,71=>4,73=>4,77=>4,78=>4,79=>4,80=>4,81=>4,82=>4,83=>4,84=>4,85=>4,88=>4,89=>4,90=>4,91=>4,92=>4,93=>4,100=>4,125=>4,127=>4,128=>4,135=>4,136=>4,138=>4,139=>4,35=>6,36=>6,37=>6,40=>6,129=>6,64=>7,104=>7);

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
	$currency_rate=set_conversion_rate( $data[0], $conversion_date ,$data[2] );
	echo "1"."_".$currency_rate;
	exit();
}
if($action=="check_process_rate")
{
	$data=explode("**",$data);
	$job_po_id=$data[0];
	$process_id=$data[1];
	$condition= new condition();
		if(str_replace("'","",$job_po_id) !=''){
			$condition->po_id("in($job_po_id)");
		}
		$condition->init();
		$conversion= new conversion($condition);
		//echo $conversion->getQuery(); die;
		 $conversion_fab_knit_qty_arr=$conversion->getQtyArray_by_orderAndProcess();
		 $conversion_fab_knit_amt_arr=$conversion->getAmountArray_by_orderAndProcess();
		  $po_ids=explode(",",$job_po_id);
		$tot_conv_amt=0;
		foreach($po_ids as $pid)
		{
		 $tot_conv_amt+=array_sum($conversion_fab_knit_amt_arr[$pid][$process_id]);
		// echo  $conv_am.', ';
		}
		 if($tot_conv_amt>0) echo $tot_conv_amt;else echo "0";die;
		 
		// if($conversion_fab_knit_amt_arr>0) echo array_sum($conversion_fab_knit_amt_arr[$job_po_id][$process_id]);else echo "0";die;
		// print_r($conversion_fab_knit_qty_arr);
		// $conversion_fab_knit_amt_arr=$conversion->getAmountArray_by_OrderFabricProcessAndColor();
}
if ($action == "check_fabric_process_data")
{
	$data=explode("**",$data);
	$process_id=$data[0];
	$conv_fabric_des_id=$data[1];
	$job_no=$data[2];
	$booking_no=$data[3];
	//if($booking_no!='') $booking_cond="and a.booking_no='$booking_no'";else $booking_cond='';
	if($booking_no!='')
	{
	$fab_conv_cost=sql_select("select c.id from wo_booking_dtls a,wo_pre_cost_fabric_cost_dtls b,wo_pre_cost_fab_conv_cost_dtls c  where  b.id=c.fabric_description and  a.job_no=c.job_no and  a.job_no=b.job_no  and  b.job_no=c.job_no and a.pre_cost_fabric_cost_dtls_id=c.id and c.id=$conv_fabric_des_id  and a.process=$process_id and a.booking_no='$booking_no'  and a.status_active=1 group by c.id");
	}
	if(count($fab_conv_cost)>0)
	{
		foreach($fab_conv_cost as $val)
		{
			$conv_id=$val[csf("id")];
			if($conv_id!="" || $conv_id!=0)
			{
				echo "1";
			}
			else
			{
				echo "0";
			}

		}
	}
}

if ($action == "supplier_company_action")
{
	$data=explode("_",$data);
	$company=$data[0];
	$pay_mode=$data[1];
	if($pay_mode==1 || $pay_mode==2 || $pay_mode==4)
	{
		$sql = "select c.id, c.supplier_name as label from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company=$company  and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name";
	}
	else
	{
		$sql = "select c.id, c.company_name as label from lib_company c where c.status_active=1 and c.is_deleted=0 group by c.id, c.company_name order by company_name";
	}
	$result = sql_select($sql);
	$supplierArr = array();
	foreach($result as $key => $val){
		$supplierArr[$key]["id"]=$val[csf("id")];
		$supplierArr[$key]["label"]=$val[csf("label")];
	}
	echo json_encode($supplierArr);
    exit();
}

if ($action=="order_search_popup")
{
  	echo load_html_head_contents("Order Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
?>
	<script>
	 var selected_id = new Array, selected_name = new Array();
	 function check_all_data() {
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
		}

		function toggle( x, origColor )
		{
			//alert(x)
			var newColor = 'yellow';
			//if ( x.style )
			//{
			document.getElementById(x).style.backgroundColor = ( newColor == document.getElementById(x).style.backgroundColor )? origColor : newColor;
			//}
		}

		function js_set_value( str_data,tr_id )
		{
			var str_all=str_data.split("_");
			var str_po=str_all[1];
			var str=str_all[0];
			//alert(str_all[2]+'='+tr_id);
			if ( document.getElementById('job_no').value!="" && document.getElementById('job_no').value!=str_all[2] )
			{
				alert('No Job Mix Allowed');return;
			}
			toggle( tr_id, '#FFFFCC');
			document.getElementById('job_no').value=str_all[2];

			if( jQuery.inArray( str , selected_id ) == -1 ) {
				selected_id.push( str );
				selected_name.push( str_po );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str ) break;
					//if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
				if(selected_id.length==0){
					document.getElementById('job_no').value="";
				}
			}
			var id = '' ; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			$('#po_number_id').val( id );
			$('#po_number').val( name );
		}
    </script>

</head>

<body>
<div align="center" style="width:100%;" >
<?
$booking_month=0;
 if(str_replace("'","",$cbo_booking_month)<10)
 {
	 $booking_month.=str_replace("'","",$cbo_booking_month);
 }
 else
 {
	$booking_month=str_replace("'","",$cbo_booking_month);
 }
$start_date="01"."-".$booking_month."-".str_replace("'","",$cbo_booking_year);
$end_date=cal_days_in_month(CAL_GREGORIAN, $booking_month, str_replace("'","",$cbo_booking_year))."-".$booking_month."-".str_replace("'","",$cbo_booking_year);
?>
	<form name="searchpofrm_1" id="searchpofrm_1">
        <table width="900" class="rpt_table" align="center" rules="all">
            <thead>
                <th width="150">Company Name</th>
                <th width="140">Buyer Name</th>
                <th width="100">Job No</th>
                <th width="60">Ref No</th>
                <th width="100">Order No</th>
                <th width="60">Style No</th>
                <th width="60">File No</th>
                <th width="130" colspan="2">Date Range</th>
                <th><input type="checkbox" value="0" onClick="set_checkvalue()" id="chk_job_wo_po">Job Without PO</th>
            </thead>
            <tr>
                <td>
                    <?
                        echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 $company_cond order by company_name","id,company_name",1, "-- Select Company --", str_replace("'","",$cbo_company_name), "load_drop_down( 'service_booking_multi_job_wise_dyeing_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );",1);
                    ?>
                </td>
                <td id="buyer_td">
    
                 <?
                 if(str_replace("'","",$cbo_company_name)!=0)
                 {
                    echo create_drop_down( "cbo_buyer_name", 150,"select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='".str_replace("'","",$cbo_company_name)."' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", str_replace("'","",$cbo_buyer_name), "" );
                 }
                 else
                 {
                   echo create_drop_down( "cbo_buyer_name", 150, $blank_array, 1, "-- Select Buyer --", str_replace("'","",$cbo_buyer_name), "" );
                 }
                ?>
                </td>
                 <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:88px"></td>
                 <td><input name="txt_ref_no" id="txt_ref_no" class="text_boxes" style="width:60px"></td>
                 <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:88px"></td>
                 <td><input name="txt_style_no" id="txt_style_no" class="text_boxes" style="width:60px"></td>
                 <td><input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:60px"></td>
                 <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px" value="<? //echo $start_date; ?>"/></td>
                 <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px" value="<? //echo $end_date; ?>"/></td>
                 <td align="center">
                 <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('chk_job_wo_po').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_ref_no').value+'_'+document.getElementById('txt_style_no').value+'_'+document.getElementById('txt_file_no').value+'_'+<? echo $txt_booking_date ?>, 'create_po_search_list_view', 'search_div', 'service_booking_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" /></td>
            </tr>
            <tr>
                <td align="center" valign="top" colspan="10">
                    <? //echo load_month_buttons();  ?>
                    <input type="hidden" id="po_number_id">
                    <input type="hidden" id="job_no"  style="width:100px" class="text_boxes">
                </td>
            </tr>
            <tr>
                <td colspan="10" align="center"><strong>Selected PO Number:</strong> &nbsp;<input type="text" class="text_boxes"  readonly style="width:550px" id="po_number"></td>
            </tr>
            <tr>
                <td colspan="10" align="center" >
                    <input type="button" name="close" onClick="parent.emailwindow.hide();"  class="formbutton" value="Close" style="width:100px" />
                </td>
            </tr>
     </table>
       <div id="search_div" align="center"></div>
	</form>
   </div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
  exit();
}

if($action=="create_po_search_list_view")
{
	$data=explode('_',$data);
	if ($data[0]!=0) $company=" and a.company_name='$data[0]'"; else { echo "Please Select Company First."; die; }
	$booking_date=$data[10];
	if ($data[1]!=0) $buyer=" and a.buyer_name='$data[1]'"; else $buyer="";//{ echo "Please Select Buyer First."; die; }
	if($db_type==0) $insert_year="SUBSTRING_INDEX(a.`insert_date`, '-', 1) as year";
	if($db_type==2) $insert_year="to_char(a.insert_date,'YYYY') as year";
	if (str_replace("'","",$data[5])!="") $job_cond=" and a.job_no_prefix_num='$data[5]' "; else  $job_cond="";
	if (str_replace("'","",$data[6])!="") $order_cond=" and b.po_number like '%$data[6]%'  "; else  $order_cond="";
	//new development
	if (str_replace("'","",$data[7])!="") $ref_cond=" and b.grouping='$data[7]' "; else  $ref_cond="";
	if (str_replace("'","",$data[8])!="") $style_ref_cond=" and a.style_ref_no='$data[8]' "; else  $style_ref_cond="";
	if (str_replace("'","",$data[9])!="") $file_no_cond=" and b.file_no='$data[9]' "; else  $file_no_cond="";
	if($db_type==0)
	{
		if ($data[3]!="" &&  $data[4]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'"; else $shipment_date ="";
	}
	else if($db_type==2)
	{
		if ($data[3]!="" &&  $data[4]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."'"; else $shipment_date ="";
	}

	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');

	if($db_type==0)
	{
		$approval_status="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$data[0]' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format($booking_date,'yyyy-mm-dd')."' and company_id='$data[0]')) and page_id=25 and status_active=1 and is_deleted=0";
	}
	else
	{
		$approval_status="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$data[0]' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format($booking_date, "", "",1)."' and company_id='$data[0]')) and page_id=25 and status_active=1 and is_deleted=0";
	}
	$approval_status=sql_select($approval_status);
	$approval_need=$approval_status[0][csf('approval_need')];

	 if($approval_need==2 || $approval_need==0 || $approval_need=="") $approval_need_id=0;else $approval_need_id=$approval_need;
	 if($approval_need_id==1) $approval_cond=" and c.approved=$approval_need_id";else $approval_cond="";
	 //echo $approval_cond;die;

	$arr=array (2=>$comp,3=>$buyer_arr);

	if ($data[2]==0)
	{
		 $sql= "select a.job_no_prefix_num,$insert_year, a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity,b.id, b.po_number,b.po_quantity,b.shipment_date,b.grouping,b.file_no from wo_po_details_master a, wo_po_break_down b, wo_pre_cost_mst c where a.job_no=b.job_no_mst and a.job_no=c.job_no and a.status_active=1 and b.status_active=1  and b.shiping_status not in(3) and a.garments_nature=3 $approval_cond $shipment_date $company $buyer $job_cond $order_cond $ref_cond $style_ref_cond $file_no_cond order by a.insert_date DESC";
		//echo $sql;
		echo create_list_view("list_view", "Job No,Year,Company,Buyer,Ref No,Style Ref. No,File No,Job Qty.,PO number,PO Qty,Shipment Date", "90,60,60,100,60,120,60,90,120,70,80","1020","320",0, $sql , "js_set_value", "id,po_number,job_no", "this.id", 1, "0,0,company_name,buyer_name,0,0,0,0,0,0", $arr , "job_no_prefix_num,year,company_name,buyer_name,grouping,style_ref_no,file_no,job_quantity,po_number,po_quantity,shipment_date", '','','0,0,0,0,0,0,0,1,0,1,3','','');
	}
	else
	{
		$sql= "select a.job_no_prefix_num, a.job_no,a.company_name,a.buyer_name,a.style_ref_no from wo_po_details_master a where a.status_active=1  and a.is_deleted=0 and a.garments_nature=3 $company $buyer order by a.insert_date DESC";

		echo  create_list_view("list_view", "Job No,Company,Buyer,Style Ref. No", "90,60,50,100,90","710","320",0, $sql , "js_set_value", "id", "", 1, "0,company_name,buyer_name,0,0,0,0", $arr , "job_no_prefix_num,company_name,buyer_name,style_ref_no", '','','0,0,0,0,1,0,2,3','','') ;
	}
	exit();
}

if ($action=="order_search_popup2")//Not Used
{
  	echo load_html_head_contents("Order Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	//echo $txt_booking_date;
	//$txt_booking_date=str_replace("'","",$txt_booking_date);
?>
	<script>
	 var selected_id = new Array, selected_name = new Array();
	 function check_all_data() {
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
		}

		function toggle( x, origColor )
		{

			var newColor = 'yellow';
			//if ( x.style )
			//{
			document.getElementById(x).style.backgroundColor = ( newColor == document.getElementById(x).style.backgroundColor )? origColor : newColor;
			//}
		}

		function js_set_value( str_data,tr_id ) {
			toggle( tr_id, '#FFFFCC');
			var str_all=str_data.split("_");
			var str_po=str_all[1];
			var str=str_all[0];
			//alert(str_all[2]);
			if ( document.getElementById('job_no').value!="" && document.getElementById('job_no').value!=str_all[2] )
			{
				alert('No Job Mix Allowed')
				return;
			}
				document.getElementById('job_no').value=str_all[2];

				if( jQuery.inArray( str , selected_id ) == -1 ) {
					selected_id.push( str );
					selected_name.push( str_po );
				}
				else {
					for( var i = 0; i < selected_id.length; i++ ) {
						if( selected_id[i] == str ) break;
					}
					selected_id.splice( i, 1 );
					selected_name.splice( i, 1 );
				}
				var id = '' ; var name = '';
				for( var i = 0; i < selected_id.length; i++ ) {
					id += selected_id[i] + ',';
					name += selected_name[i] + ',';
				}
				id = id.substr( 0, id.length - 1 );
				name = name.substr( 0, name.length - 1 );
				$('#po_number_id').val( id );
				$('#po_number').val( name );
		}
    </script>
</head>
<body>
<div align="center" style="width:100%;" >
<?
$booking_month=0;
 if(str_replace("'","",$cbo_booking_month)<10)
 {
	 $booking_month.=str_replace("'","",$cbo_booking_month);
 }
 else
 {
	$booking_month=str_replace("'","",$cbo_booking_month);
 }
$start_date="01"."-".$booking_month."-".str_replace("'","",$cbo_booking_year);
$end_date=cal_days_in_month(CAL_GREGORIAN, $booking_month, str_replace("'","",$cbo_booking_year))."-".$booking_month."-".str_replace("'","",$cbo_booking_year);
?>
	<form name="searchpofrm_1" id="searchpofrm_1">
        <table  width="880" class="rpt_table" align="center" rules="all">
            <thead>
                <tr>
                    <th colspan="10" align="center"><? echo create_drop_down( "cbo_string_search_type", 100, $string_search_type,'', 1, "--Searching Type--" ); ?></th>
                </tr>
                <tr>
                    <th width="130">Company Name</th>
                    <th width="172">Buyer Name</th>
                    <th width="70">Job No</th>
                    <th width="80">Style Ref </th>
                    <th width="80">Order No</th>
                    <th width="80">Ref. No</th>
                    <th width="80">File</th>
                    <th colspan="2">Date Range</th>
                    <th width="70"><input type="checkbox" value="0" onClick="set_checkvalue()" id="chk_job_wo_po">Job Without PO</th>
                </tr>
            </thead>
            <tr>
                <td><? echo create_drop_down( "cbo_company_mst", 130, "select id,company_name from lib_company comp where status_active=1 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "-- Select Company --", str_replace("'","",$cbo_company_name), "load_drop_down( 'service_booking_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );"); ?> </td>
            <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 172, $blank_array, 1, "-- Select Buyer --", str_replace("'","",$cbo_buyer_name), "" ); ?></td>
            <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:60px"></td>
            <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:70px"></td>
            <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:70px"></td>
            <td><input name="txt_ref_search" id="txt_ref_search" class="text_boxes" style="width:70px"></td>
            <td><input name="txt_file_search" id="txt_file_search" class="text_boxes" style="width:70px"></td>
            <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" value="<? echo $start_date; ?>"/></td>
            <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px" value="<? echo $end_date; ?>"/></td>
            <td align="center">
             <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('chk_job_wo_po').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+<? echo $txt_booking_date ?>+'_'+document.getElementById('txt_ref_search').value+'_'+document.getElementById('txt_file_search').value, 'create_po_search_list_view', 'search_div', 'service_booking_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px" /></td>
        </tr>
        <tr>
            <td colspan="10" align="center"><input type="hidden" id="po_number_id">
                <input type="hidden" id="job_no">
                <strong>Selected PO Number:</strong> &nbsp;<input type="text" class="text_boxes" readonly style="width:500px" id="po_number"></td>
        </tr>
        <tr>
            <td colspan="10" align="center"><input type="button" name="close" onClick="parent.emailwindow.hide();"  class="formbutton" value="Close" style="width:100px" /></td>
        </tr>
     </table>
     <div id="search_div" align="center"></div>
        </form>
       </div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
  exit();
}

if($action=="create_po_search_list_view2")
{
	$data=explode('_',$data);
	if ($data[0]!=0) $company=" and a.company_name='$data[0]'"; else { echo "Please Select Company First."; die; }

	if ($data[1]!=0) $buyer=" and a.buyer_name='$data[1]'"; else $buyer="";//{ echo "Please Select Buyer First."; die; }
	if($db_type==0)  $insert_year="SUBSTRING_INDEX(a.insert_date, '-', 1) as year";
	if($db_type==2)  $insert_year="to_char(a.insert_date,'YYYY') as year";
	$booking_date=$data[9];
	$company_id=$data[0];


	//if (str_replace("'","",$data[5])!="") $job_cond=" and a.job_no_prefix_num='$data[5]'   "; else  $job_cond="";
	//if (str_replace("'","",$data[6])!="") $order_cond=" and b.po_number like '%$data[6]%'  "; else  $order_cond="";
	$job_cond=""; $order_cond=""; $style_cond=""; $ref_cond=""; $file_cond="";
	if($data[8]==1)
	{
		if (str_replace("'","",$data[5])!="") $job_cond=" and a.job_no_prefix_num='$data[5]'"; //else  $job_cond="";
		if (str_replace("'","",$data[6])!="") $order_cond=" and b.po_number = '$data[6]'  "; //else  $order_cond="";
		if (trim($data[7])!="") $style_cond=" and a.style_ref_no ='$data[7]'"; //else  $style_cond="";
		if (trim($data[10])!="") $ref_cond=" and b.grouping ='$data[10]'"; //else  $style_cond="";
		if (trim($data[11])!="") $file_cond=" and b.file_no ='$data[11]'"; //else  $style_cond="";
	}
	else if($data[8]==2)
	{
		if (str_replace("'","",$data[5])!="") $job_cond=" and a.job_no_prefix_num like '$data[5]%'"; //else  $job_cond="";
		if (str_replace("'","",$data[6])!="") $order_cond=" and b.po_number like '$data[6]%'  "; //else  $order_cond="";
		if (trim($data[7])!="") $style_cond=" and a.style_ref_no like '$data[7]%'  "; //else  $style_cond="";
		if (trim($data[10])!="") $ref_cond=" and b.grouping like $data[10]%'"; //else  $style_cond="";
		if (trim($data[11])!="") $file_cond=" and b.file_no like '$data[11]%'"; //else  $style_cond="";
	}
	else if($data[8]==3)
	{
		if (str_replace("'","",$data[5])!="") $job_cond=" and a.job_no_prefix_num like '%$data[5]'"; //else  $job_cond="";
		if (str_replace("'","",$data[6])!="") $order_cond=" and b.po_number like '%$data[6]'  "; //else  $order_cond="";
		if (trim($data[7])!="") $style_cond=" and a.style_ref_no like '%$data[7]'"; //else  $style_cond="";
		if (trim($data[10])!="") $ref_cond=" and b.grouping like '%$data[10]'"; //else  $style_cond="";
		if (trim($data[11])!="") $file_cond=" and b.file_no like '%$data[11]'"; //else  $style_cond="";
	}
	else if($data[8]==4 || $data[8]==0)
	{
		if (str_replace("'","",$data[5])!="") $job_cond=" and a.job_no_prefix_num like '%$data[5]%'"; //else  $job_cond="";
		if (str_replace("'","",$data[6])!="") $order_cond=" and b.po_number like '%$data[6]%'  "; //else  $order_cond="";
		if (trim($data[7])!="") $style_cond=" and a.style_ref_no like '%$data[7]%'"; //else  $style_cond="";
		if (trim($data[10])!="") $ref_cond=" and b.grouping like '%$data[10]%'"; //else  $style_cond="";
		if (trim($data[11])!="") $file_cond=" and b.file_no like '%$data[11]%'"; //else  $style_cond="";
	}

	if($db_type==0)
	{
		if ($data[3]!="" &&  $data[4]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'"; else $shipment_date ="";

	//$setup_date= "and a.setup_date='".change_date_format($booking_date, "yyyy-mm-dd", "-")."' ";
	}
	else if($db_type==2)
	{
		if ($data[3]!="" &&  $data[4]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."'"; else $shipment_date ="";
	//$setup_date= "and a.setup_date='".change_date_format($booking_date, "yyyy-mm-dd", "-",1)."' ";
	}

	//$approval_need=return_field_value("approval_need as approval_need","approval_setup_mst a,approval_setup_dtls b","a.id=b.mst_id and a.company_id=".$company_id."  and b.page_id=25  and b.is_deleted=0 and b.status_active=1 $setup_date","approval_need");

	if($db_type==0)
	{
		$approval_status="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company_id' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format($booking_date,'yyyy-mm-dd')."' and company_id='$company_id')) and page_id=25 and status_active=1 and is_deleted=0";
	}
	else
	{
		$approval_status="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company_id' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format($booking_date, "", "",1)."' and company_id='$company_id')) and page_id=25 and status_active=1 and is_deleted=0";
	}
	$approval_status=sql_select($approval_status);
	$approval_need=$approval_status[0][csf('approval_need')];

	 if($approval_need==2 || $approval_need=="") $approval_need_id=0;else $approval_need_id=$approval_need;
	 if($approval_need_id==1) $approval_cond=" and c.approved=$approval_need_id";else $approval_cond="";
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');

	$arr=array (2=>$comp,3=>$buyer_arr);

	if ($data[2]==0)
	{
		  $sql= "select a.job_no_prefix_num,$insert_year, a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity,b.id, b.po_number,b.po_quantity,b.shipment_date from wo_po_details_master a, wo_po_break_down b, wo_pre_cost_mst c where a.job_no=b.job_no_mst and a.job_no=c.job_no and a.status_active=1 and b.status_active=1  and b.shiping_status not in(3) $shipment_date $company $buyer $job_cond $order_cond $ref_cond $file_cond $style_cond $approval_cond order by a.job_no";

		echo  create_list_view("list_view", "Job No,Year,Company,Buyer,Style Ref. No,Job Qty.,PO number,PO Qty,Shipment Date", "50,60,90,100,100,120,90,70,80","850","320",0, $sql , "js_set_value", "id,po_number,job_no", "this.id", 1, "0,0,company_name,buyer_name,0,0,0,0", $arr , "job_no_prefix_num,year,company_name,buyer_name,style_ref_no,job_quantity,po_number,po_quantity,shipment_date", '','','0,0,0,0,0,1,0,1,3','','');
	}
	else
	{
		$sql= "select a.job_no_prefix_num,$insert_year, a.job_no,a.company_name,a.buyer_name,a.style_ref_no from wo_po_details_master a where a.status_active=1  and a.is_deleted=0 $company $buyer $job_cond $shipment_date $style_cond order by a.job_no";

		echo  create_list_view("list_view", "Job No,Year,Company,Buyer,Style Ref. No", "60,60,120,120,200","610","320",0, $sql , "js_set_value", "id", "", 1, "0,0,company_name,buyer_name,0", $arr , "job_no_prefix_num,year,company_name,buyer_name,style_ref_no", '','','0,0,0,0,0','','') ;
	}

}
if ($action=="populate_report_setting")
{
		//echo "select format_id from lib_report_template where template_name ='".$data."' and module_id=2 and report_id=11 and is_deleted=0 and status_active=1";
		$print_report_format2=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=2 and report_id=11 and is_deleted=0 and status_active=1");
		//echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_name")]."';\n";
		//echo "document.getElementById('report_ids').value = '".$print_report_format2."';\n";
		echo "print_report_button_setting('".$print_report_format2."');\n";
		$fab_req_source=return_field_value("excut_source", "variable_order_tracking", "company_name=$data and variable_list=66 and status_active=1 and is_deleted=0");
		if($fab_req_source=="" || $fab_req_source==0) $fab_req_source=1;else $fab_req_source=$fab_req_source;
		echo "document.getElementById('vari_fab_source_id').value = '".$fab_req_source."';\n";
		exit();
}



if ($action=="send_mail_report_setting_first_select")
{
		$print_report_format2=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=2 and report_id=11 and is_deleted=0 and status_active=1");
		echo $print_report_format2;
		exit();
}



if ($action=="populate_order_data_from_search_popup")
{
$data=explode("_",$data);
$po_id=$data[0];
$fab_souce_id=$data[1];
if($fab_souce_id=="" || $fab_souce_id==0) $fab_souce_id=1;else $fab_souce_id=$fab_souce_id;
//echo $fab_souce_id."select a.job_no,a.company_name,a.buyer_name from wo_po_details_master a, wo_po_break_down b where b.id in (".$po_id.") and a.job_no=b.job_no_mst";
	$data_array=sql_select("select a.job_no,a.company_name,a.buyer_name from wo_po_details_master a, wo_po_break_down b where b.id in (".$po_id.") and a.job_no=b.job_no_mst");
	foreach ($data_array as $row)
	{

		//$print_report_format2=return_field_value("format_id","user_priviledge_report_setting","company_id ='".$row[csf("company_name")]."' and  user_id=$user_id and module_id=2 and report_id=11 and is_deleted=0 and status_active=1");

		$print_report_format2=return_field_value("format_id","lib_report_template","template_name ='".$row[csf("company_name")]."'   and module_id=2 and report_id=11 and is_deleted=0 and status_active=1");
		echo "print_report_button_setting('".$print_report_format2."');\n";
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_name")]."';\n";
		//echo "document.getElementById('report_ids').value = '".$print_report_format2."';\n";
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_name")]."';\n";
		//echo "document.getElementById('txt_job_no').value = '".$row[csf("job_no")]."';\n";
		//echo "check_exchange_rate();\n"; //vari_fab_source_id
		//$data_string=$row[csf("job_no")].'_'.$row[csf("job_no")];
		if($fab_souce_id==1 || $fab_souce_id==2)
		{
			echo "load_drop_down( 'requires/service_booking_controller', '".$row[csf("job_no")]."', 'load_drop_down_fabric_description', 'fabric_description_td' )\n";
		}
		else
		{
			//echo "load_drop_down( 'requires/service_booking_controller', '".$po_id."', 'load_drop_down_booking_fabric_description', 'fabric_description_td' )\n";
		}
		//echo "load_drop_down( 'requires/service_booking_controller', '".$row[csf("job_no")]."', 'load_drop_down_process', 'process_td' )\n";
	}
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
}

if ($action=="load_drop_down_fabric_description")
{

	//echo "select a.id as wo_pre_cost_fab_conv_cost_dtls,a.fabric_description,b.id, b.body_part_id, b.color_type_id, b.fabric_description from wo_pre_cost_fab_conv_cost_dtls a, wo_pre_cost_fabric_cost_dtls b where a.job_no =b.job_no and a.fabric_description=b.id and a.job_no='$data' ";

	$data=explode("_",$data);
	//print_r($data);
	$fabric_description_array=array();
	if($data[1] =="")// and cons_process not in (1,30,31,35)   ISD-22-17815
	{
		$wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select id,fabric_description,cons_process from wo_pre_cost_fab_conv_cost_dtls  where job_no='$data[0]' and status_active=1 and is_deleted=0 and cons_process not in (1,30,31,35)");
	}
	else
	{
		$wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select id,fabric_description,cons_process from wo_pre_cost_fab_conv_cost_dtls  where job_no='".$data[0]."' and status_active=1 and is_deleted=0  and cons_process not in (1,30,31,35) and id not in(select pre_cost_fabric_cost_dtls_id  from wo_booking_dtls where booking_no='".$data[1]."' and  booking_type=3 and status_active=1 and is_deleted=0)");
		//echo "select id,fabric_description,cons_process from wo_pre_cost_fab_conv_cost_dtls where job_no='FAL-13-00166' and id not in(select pre_cost_fabric_cost_dtls_id from wo_booking_dtls where booking_no='D n C-17-01690' and booking_type=3 and status_active=1 and is_deleted=0)";
	}

	foreach( $wo_pre_cost_fab_conv_cost_dtls_id as $row_wo_pre_cost_fab_conv_cost_dtls_id)
	{
		if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]!=0)
		{

			$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description from  wo_pre_cost_fabric_cost_dtls  where  id='".$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]."' and status_active=1");
			list($fabric_description_row)=$fabric_description;

			$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]=$body_part[$fabric_description_row[csf("body_part_id")]].', '.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")].'-'.$conversion_cost_head_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("cons_process")]];

		}

		if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]==0)
		{
			//echo "select id,body_part_id,color_type_id,fabric_description from  wo_pre_cost_fabric_cost_dtls  where  job_no='$data'";
			$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description,cons_process from  wo_pre_cost_fabric_cost_dtls  where  job_no='$data[0]' and status_active=1");
			//list($fabric_description_row)=$fabric_description;
			foreach( $fabric_description as $fabric_description_row)
	        {
			//$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]=$body_part[$fabric_description_row[csf("body_part_id")]].', '.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")];

			$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]="All Fabrics  ".$conversion_cost_head_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("cons_process")]];
			}
		}
	}
	echo create_drop_down( "cbo_fabric_description",650, $fabric_description_array,"", 1, "-- Select --", $selected, "set_process(this.value,'set_process')" );
}
if ($action=="load_drop_down_booking_fabric_description")
{

	//echo "select a.id as wo_pre_cost_fab_conv_cost_dtls,a.fabric_description,b.id, b.body_part_id, b.color_type_id, b.fabric_description from wo_pre_cost_fab_conv_cost_dtls a, wo_pre_cost_fabric_cost_dtls b where a.job_no =b.job_no and a.fabric_description=b.id and a.job_no='$data' ";

	//$data=explode("_",$data);
	//print_r($data);
	$fabric_description_array=array();
	$wo_booking=sql_select("select d.id as conv_dtls_id,b.pre_cost_fabric_cost_dtls_id as fab_dtls_id,c.body_part_id,c.color_type_id,c.fabric_description from wo_booking_dtls b,wo_pre_cost_fabric_cost_dtls c,wo_pre_cost_fab_conv_cost_dtls d  where  b.pre_cost_fabric_cost_dtls_id=c.id and d.fabric_description=c.id and b.po_break_down_id in(".$data.")  and b.booking_type=1  and b.status_active=1 and b.is_deleted=0 and d.cons_process not in (1,30,31,35) group by b.pre_cost_fabric_cost_dtls_id,d.id,c.body_part_id,c.color_type_id,c.fabric_description ");
	//echo "select d.id as conv_dtls_id,b.pre_cost_fabric_cost_dtls_id as fab_dtls_id,c.body_part_id,c.color_type_id,c.fabric_description from wo_booking_dtls b,wo_pre_cost_fabric_cost_dtls c,wo_pre_cost_fab_conv_cost_dtls d  where  b.pre_cost_fabric_cost_dtls_id=c.id and d.fabric_description=c.id and b.po_break_down_id in(".$data.")  and b.booking_type=1  and b.status_active=1 and b.is_deleted=0 group by b.pre_cost_fabric_cost_dtls_id,d.id,c.body_part_id,c.color_type_id,c.fabric_description ";
	//echo "select b.po_break_down_id,b.construction,b.copmposition,b.gsm_weight,b.dia_width,b.is_short,b.pre_cost_fabric_cost_dtls_id as fab_dtls_id,c.body_part_id,c.color_type_id,c.fabric_description from wo_booking_dtls b,wo_pre_cost_fabric_cost_dtls c  where  b.pre_cost_fabric_cost_dtls_id=c.id and b.po_break_down_id in(".$data.")  and b.booking_type=1  and b.status_active=1 and b.is_deleted=0";
		
	

	foreach( $wo_booking as $row)
	{
			$fabric_description_array[$row[csf("conv_dtls_id")]]=$body_part[$row[csf("body_part_id")]].', '.$color_type[$row[csf("color_type_id")]].', '.$row[csf("fabric_description")];
	}
	
	echo create_drop_down( "cbo_fabric_description", 650, $fabric_description_array,"", 1, "-- Select --", $selected, "set_process(this.value,'set_process')" );
	exit();
}
if ($action=="load_drop_down_fabric_description_new")
{

	//echo "select a.id as wo_pre_cost_fab_conv_cost_dtls,a.fabric_description,b.id, b.body_part_id, b.color_type_id, b.fabric_description from wo_pre_cost_fab_conv_cost_dtls a, wo_pre_cost_fabric_cost_dtls b where a.job_no =b.job_no and a.fabric_description=b.id and a.job_no='$data' ";

	$data=explode("_",$data);
	//print_r($data);
	$fabric_description_array=array();
	if($data[1] =="")
	{
		$wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select id,fabric_description,cons_process from wo_pre_cost_fab_conv_cost_dtls  where job_no='$data[0]' and status_active=1 and cons_process not in (1,30,31,35)");
	}
	else
	{
		$wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select id,fabric_description,cons_process from wo_pre_cost_fab_conv_cost_dtls  where job_no='".$data[0]."' and status_active=1 and cons_process not in (1,30,31,35)");
		//echo "select id,fabric_description,cons_process from wo_pre_cost_fab_conv_cost_dtls where job_no='FAL-13-00166' and id not in(select pre_cost_fabric_cost_dtls_id from wo_booking_dtls where booking_no='D n C-17-01690' and booking_type=3 and status_active=1 and is_deleted=0)";
	}

	foreach( $wo_pre_cost_fab_conv_cost_dtls_id as $row_wo_pre_cost_fab_conv_cost_dtls_id)
	{
		if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]!=0)
		{

			$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description from  wo_pre_cost_fabric_cost_dtls  where  id='".$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]."'");
			list($fabric_description_row)=$fabric_description;

			$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]=$body_part[$fabric_description_row[csf("body_part_id")]].', '.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")].'-'.$conversion_cost_head_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("cons_process")]];

		}

		if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]==0)
		{
			//echo "select id,body_part_id,color_type_id,fabric_description from  wo_pre_cost_fabric_cost_dtls  where  job_no='$data'";
			$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description,cons_process from  wo_pre_cost_fabric_cost_dtls  where  job_no='$data[0]' and status_active=1");
			//list($fabric_description_row)=$fabric_description;
			foreach( $fabric_description as $fabric_description_row)
	        {
			//$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]=$body_part[$fabric_description_row[csf("body_part_id")]].', '.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")];

			$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]="All Fabrics  ".$conversion_cost_head_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("cons_process")]];
			}
		}
	}
	echo create_drop_down( "cbo_fabric_description", 650, $fabric_description_array,"", 1, "-- Select --", $selected, "set_process2(this.value,'set_process')" );
}

if ($action=="load_drop_down_process")
{

	$cons_process="";
	$wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select distinct cons_process from wo_pre_cost_fab_conv_cost_dtls  where job_no='$data' and status_active=1 and cons_process not in (1,30,31,35)");
	foreach( $wo_pre_cost_fab_conv_cost_dtls_id as $row_wo_pre_cost_fab_conv_cost_dtls_id)
	{
		$cons_process.=$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("cons_process")].",";
	}
	$cons_process= rtrim($cons_process, ",");
    echo create_drop_down( "cbo_process", 172, $conversion_cost_head_array,"", 1, "-- Select --", "", "prev_booking(this.value);","","$cons_process" );
 }

 if($action=="set_process")
 {
	 $process=return_field_value("cons_process", "wo_pre_cost_fab_conv_cost_dtls", "id=$data and status_active=1");
	 // echo $process; die;

 }
 
 if($action=="wo_qty_po_fabric_wise")
 {
	 $data=explode("**",$data);
	 $po_id=$data[0];
	 $fab_dtls_id=$data[1];
	 $sql_data="select b.pre_cost_fabric_cost_dtls_id,b.po_break_down_id,sum(b.wo_qnty) as wo_qnty from  wo_booking_dtls a,wo_booking_dtls b where a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_break_down_id in($po_id) and b.pre_cost_fabric_cost_dtls_id in($fab_dtls_id) group by b.pre_cost_fabric_cost_dtls_id,b.po_break_down_id";
	 //$process=return_field_value("cons_process", "wo_pre_cost_fab_conv_cost_dtls", "id=$data");
	 // echo $process; die;
 }

if ($action=="save_update_delete")
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
		$response_booking_no="";
		if(str_replace("'","",$txt_booking_no)=="")
		{
			$new_booking_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'FSB', date("Y",time()), 5, "select id, booking_no_prefix, booking_no_prefix_num from wo_booking_mst where company_id=$cbo_company_name and booking_type=3 and entry_form=573 and to_char(insert_date,'YYYY')=".date('Y',time())." order by id desc ", "booking_no_prefix", "booking_no_prefix_num" ));

			$id=return_next_id( "id", "wo_booking_mst", 1 ) ;//vari_fab_source_id
			$field_array="id,booking_type,booking_month,booking_year,booking_no_prefix,booking_no_prefix_num,booking_no,company_id,buyer_id, item_category,entry_form,supplier_id,currency_id,exchange_rate,booking_date,delivery_date,pay_mode,source,quality_level,tenor,attention,ready_to_approved,inserted_by,insert_date";
			 $data_array ="(".$id.",3,".$cbo_booking_month.",".$cbo_booking_year.",'".$new_booking_no[1]."',".$new_booking_no[2].",'".$new_booking_no[0]."',".$cbo_company_name.",".$cbo_buyer_name.",12,573,".$hidden_supplier_id.",".$cbo_currency.",".$txt_exchange_rate.",".$txt_booking_date.",".$txt_delivery_date.",".$cbo_pay_mode.",".$cbo_source.",".$vari_fab_source_id.",".$txt_tenor.",".$txt_attention.",".$cbo_ready_to_approved.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			// $rID=sql_insert("wo_booking_mst",$field_array,$data_array,0);
			 $response_booking_no=$new_booking_no[0];
		}
		if(str_replace("'","",$txt_booking_no)!="")
		{
			 $field_array_up="booking_type*booking_month*booking_year*buyer_id* item_category*supplier_id*currency_id*exchange_rate*booking_date*delivery_date*pay_mode*source*tenor*attention*ready_to_approved*updated_by*update_date";
			 $data_array_up ="3*".$cbo_booking_month."*".$cbo_booking_year."*".$cbo_buyer_name."*12*".$hidden_supplier_id."*".$cbo_currency."*".$txt_exchange_rate."*".$txt_booking_date."*".$txt_delivery_date."*".$cbo_pay_mode."*".$cbo_source."*".$txt_tenor."*".$txt_attention."*".$cbo_ready_to_approved."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			 //$rID=sql_update("wo_booking_mst",$field_array_up,$data_array_up,"booking_no","".$txt_booking_no."",0);
			 $response_booking_no=str_replace("'","",$txt_booking_no);
		}

		 if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0";disconnect($con); die;}

		 //$rID=sql_insert("wo_booking_mst",$field_array,$data_array,0);
		 if(str_replace("'","",$txt_booking_no)=="")
		 {
			  $rID=sql_insert("wo_booking_mst",$field_array,$data_array,0);
		 }
		 if(str_replace("'","",$txt_booking_no)!="")
		 {
			  $rID=sql_update("wo_booking_mst",$field_array_up,$data_array_up,"booking_no","".$txt_booking_no."",0);
		 }
		// echo "10**insert into wo_booking_dtls (".$field_array1.") values ".$data_array1;die;

		// echo "10**". $rID.'=='. $rID1;die;
		check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID){
				mysql_query("COMMIT");
				echo "0**".$response_booking_no."**".$id;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$response_booking_no."**".$id;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID){
				oci_commit($con);
				echo "0**".$response_booking_no."**".$id;
			}
			else{
				oci_rollback($con);
				echo "10**".$response_booking_no."**".$id;
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here
	{
		 $con = connect();
		 if($db_type==0)
		 {
			mysql_query("BEGIN");
		 }

		$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id and b.work_order_no=$txt_booking_no and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
		if($pi_number){
			echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
			disconnect($con);die;
		}

		$issue_mrr=0;
		$sqlis=sql_select("select issue_number from inv_issue_master where service_booking_no=$txt_booking_no  and status_active=1 and is_deleted=0");
		foreach($sqlis as $rows){
			$issue_mrr=$rows[csf('issue_number')];
		}
		if($issue_mrr){
			echo "iss1**".str_replace("'","",$txt_booking_no)."**".$issue_mrr;
			disconnect($con);die;
		}
		
		$issueTofinProcess_mrr=0;
		$sqlissueFinProcess=sql_select("select a.recv_number from inv_receive_mas_batchroll a, pro_grey_batch_dtls b where a.id=b.mst_id and b.booking_no=$txt_booking_no and a.receive_basis=2 and a.entry_form=91 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		//echo "10**select a.recv_number from inv_receive_mas_batchroll a, pro_grey_batch_dtls b where a.id=b.mst_id and b.booking_no=$txt_booking_no and a.receive_basis=2 and a.entry_form=91 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0"; die;
		foreach($sqlissueFinProcess as $rows){
			$issueTofinProcess_mrr=$rows[csf('recv_number')];
		}
		if($issueTofinProcess_mrr){
			echo "issFinPrcess**".str_replace("'","",$txt_booking_no)."**".$issueTofinProcess_mrr;
			disconnect($con);die;
		}

		 $field_array_up="booking_type*booking_month*booking_year*buyer_id* item_category*supplier_id*currency_id*exchange_rate*booking_date*delivery_date*pay_mode*source*tenor*attention*ready_to_approved*updated_by*update_date";
		 $data_array_up ="3*".$cbo_booking_month."*".$cbo_booking_year."*".$cbo_buyer_name."*12*".$hidden_supplier_id."*".$cbo_currency."*".$txt_exchange_rate."*".$txt_booking_date."*".$txt_delivery_date."*".$cbo_pay_mode."*".$cbo_source."*".$txt_tenor."*".$txt_attention."*".$cbo_ready_to_approved."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		 //=======================================================================================================
		 if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**1"; disconnect($con);die; }

		 $rID=sql_update("wo_booking_mst",$field_array_up,$data_array_up,"booking_no","".$txt_booking_no."",0);
		// echo '10**'.bulk_update_sql_statement2( "wo_booking_dtls", "id", $field_array_up1, $data_array_up1, $id_arr );die;
     	 check_table_status( $_SESSION['menu_id'],0);
	  // echo "10**".$rID.'='.$rID1;die;
		if($db_type==0)
		{
			if($rID){
				mysql_query("COMMIT");
				echo "1**".str_replace("'","",$txt_booking_no);
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID){
				oci_commit($con);
				echo "1**".str_replace("'","",$txt_booking_no);
			}
			else{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)   // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id and b.work_order_no=$txt_booking_no and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
		if($pi_number){
			echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
			disconnect($con);die;
		}

		$issue_mrr=0;
		$sqlis=sql_select("select issue_number from inv_issue_master where service_booking_no=$txt_booking_no  and status_active=1 and is_deleted=0");
		foreach($sqlis as $rows){
			$issue_mrr=$rows[csf('issue_number')];
		}
		if($issue_mrr){
			echo "iss1**".str_replace("'","",$txt_booking_no)."**".$issue_mrr;
			disconnect($con);die;
		}
		
		$issueTofinProcess_mrr=0;
		$sqlissueFinProcess=sql_select("select a.recv_number from inv_receive_mas_batchroll a, pro_grey_batch_dtls b where a.id=b.mst_id and b.booking_no=$txt_booking_no and a.receive_basis=2 and a.entry_form=91 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		foreach($sqlissueFinProcess as $rows){
			$issueTofinProcess_mrr=$rows[csf('recv_number')];
		}
		if($issueTofinProcess_mrr){
			echo "issFinPrcess**".str_replace("'","",$txt_booking_no)."**".$issueTofinProcess_mrr;
			disconnect($con);die;
		}

		$field_array_del="status_active*is_deleted*updated_by*update_date";
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
	/*	$data_array_up="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		for ($i=1;$i<=$total_row;$i++)
		 {
			 $updateid="updateid_".$hide_fabric_description."_".$i;
			if(str_replace("'",'',$$updateid)!="")
			{
				$id_arr[]=str_replace("'",'',$$updateid);
				$data_array_up1[str_replace("'",'',$$updateid)] =explode("*",("0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			}

		 }*/

		//$rID=execute_query(bulk_update_sql_statement( "wo_booking_dtls", "id", $field_array_del, $data_array_up1, $id_arr ));
		$rID=sql_delete("wo_booking_mst",$field_array,$data_array,"booking_no","".$txt_booking_no."",1);
		$rID1=sql_delete("wo_booking_dtls",$field_array,$data_array,"booking_no","".$txt_booking_no."",1);
		if($db_type==0)
		{
			if($rID && $rID1){
				mysql_query("COMMIT");
				echo "2**".str_replace("'","",$txt_booking_no);
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID1){
				oci_commit($con);
				echo "2**".str_replace("'","",$txt_booking_no);
			}
			else{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		disconnect($con);
		die;
	}
}

if ($action=="save_update_delete_dtls")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$booking_mst_id=str_replace("'", "", $booking_mst_id);
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$response_booking_no=str_replace("'","",$txt_booking_no);
		$process=str_replace("'","",$cbo_process);
		//echo "select excut_source from variable_order_tracking where company_name=$cbo_company_name  and variable_list=66  and status_active=1 and is_deleted=0";
		 $fab_source=return_field_value("excut_source", "variable_order_tracking", "company_name=$cbo_company_name  and variable_list=66  and status_active=1 and is_deleted=0");
		 if($fab_source>0) $fab_source=$fab_source;else $fab_source=0;
		//echo "10**".$fab_source;die;
		if($fab_source==2 || $fab_source==1)
		{
			$poArr=array();$tot_woqnty=0;//For Validation Check
			for ($i=1;$i<=$row_num;$i++)
			{
				$po_id="po_id_".$hide_fabric_description."_".$i;
				$txt_woqnty="txt_woqnty_".$hide_fabric_description."_".$i;
				$tot_woqnty+=str_replace("'","",$$txt_woqnty);
				$poid=str_replace("'","",$$po_id);
			    $poArr[$poid]=$poid;
			}

			$prev_book_qty_po_arr=array();
			$po_fab_sql=sql_select("SELECT po_break_down_id as po_id,
				sum(case when booking_type=3 then wo_qnty else 0 end) as service_wo_qnty,
				sum(case when booking_type=1  then grey_fab_qnty else 0 end) as fab_wo_qnty
				from wo_booking_dtls where po_break_down_id in(".implode(",",$poArr).") and booking_type in(1,3)
				and status_active=1 and is_deleted=0 group by po_break_down_id");

			$tot_prev_service_wo_qnty=$fab_req_wo_qnty=0;
			foreach($po_fab_sql as $row)
			{
				$tot_prev_service_wo_qnty+=$row[csf('service_wo_qnty')];
				$fab_req_wo_qnty+=$row[csf('fab_wo_qnty')];
			}
			$tot_service_wo_qnty=($tot_prev_service_wo_qnty+$tot_woqnty)-$tot_prev_service_wo_qnty;
		}
		if($fab_source==2)
		{

			//echo "17**Fabric Req is Over**".$tot_service_wo_qnty."**".$fab_req_wo_qnty;die;
			if ($fab_req_wo_qnty > 0)
			{
				//if($tot_service_wo_qnty>$fab_req_wo_qnty)
				if(($tot_service_wo_qnty-$fab_req_wo_qnty)>1)
				{
					echo "17**Fabric Req is Over**".$tot_service_wo_qnty."**".$fab_req_wo_qnty;disconnect($con);die;
				}
		    }
		}
		else if($fab_source==1) //Budget
		{
			$condition= new condition();
			//$condition->company_name("=$com");
			$poid=implode(",",$poArr);
			if($poid!='' || $poid!=0)
			{
				$condition->po_id("in($poid)");
			}
			$condition->init();
			$conversion= new conversion($condition);
			//echo "10**".$conversion->getQuery();die;
			$conv_po_qty_arr=$conversion->getQtyArray_by_order();
			$tot_budget_conv_qty=0;
			foreach($poArr as $poID)
			{
				$tot_budget_conv_qty+=array_sum($conv_po_qty_arr[$poID]);
			}
			$tot_service_wo_qnty=$tot_prev_service_wo_qnty+$tot_woqnty;
			//echo "17**Budget Req is Over**".$tot_service_wo_qnty."**".$tot_budget_conv_qty;die;
			//if($tot_service_wo_qnty>$tot_budget_conv_qty)
			if(($tot_service_wo_qnty-$tot_budget_conv_qty)>1)

			{
				echo "17**Budget Req is Over**".$tot_service_wo_qnty."**".$tot_budget_conv_qty;disconnect($con);die;
			}
		}

		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0";disconnect($con); die;}
		 $id_dtls=return_next_id( "id", "wo_booking_dtls", 1) ;
		 $field_array1="id, booking_mst_id, pre_cost_fabric_cost_dtls_id, entry_form_id, artwork_no, po_break_down_id, color_size_table_id, job_no, booking_no, booking_type, fabric_color_id, gmts_color_id, item_size, gmts_size, description, uom, process, sensitivity, wo_qnty, rate, amount, delivery_date, delivery_end_date, dia_width, fin_dia, labdip_no, mc_dia, fin_gsm, slength, yarn_count, lot_no, brand, lib_composition, lib_supplier_rate_id, remark, inserted_by, insert_date, status_active, is_deleted";
		 $new_array_color=array();
		 for ($i=1;$i<=$row_num;$i++)
		 {
			 $po_id="po_id_".$hide_fabric_description."_".$i;
			 $txt_job_no="txt_job_no_".$hide_fabric_description."_".$i;
			 $fabric_description_id="fabric_description_id_".$hide_fabric_description."_".$i;
			 $artworkno="artworkno_".$hide_fabric_description."_".$i;
             $color_size_table_id="color_size_table_id_".$hide_fabric_description."_".$i;
			 $gmts_color_id="gmts_color_id_".$hide_fabric_description."_".$i;
			 $item_color_id="item_color_id_".$hide_fabric_description."_".$i;
			 $item_color="item_color_".$hide_fabric_description."_".$i;
			 $gmts_size_id="gmts_size_id_".$hide_fabric_description."_".$i;
			 $item_size="item_size_".$hide_fabric_description."_".$i;
			 $uom="uom_".$hide_fabric_description."_".$i;
			 $txt_woqnty="txt_woqnty_".$hide_fabric_description."_".$i;
			 $txt_rate="txt_rate_".$hide_fabric_description."_".$i;
			 $txt_amount="txt_amount_".$hide_fabric_description."_".$i;
			 $txt_paln_cut="txt_paln_cut".$hide_fabric_description."_".$i;
			 $updateid="updateid_".$hide_fabric_description."_".$i;

			 $startdate="startdate_".$hide_fabric_description."_".$i;
			 $enddate="enddate_".$hide_fabric_description."_".$i;
			 $findia="findia_".$hide_fabric_description."_".$i;
			 $labdipno="labdipno_".$hide_fabric_description."_".$i;
			 $mcdia="mcdia_".$hide_fabric_description."_".$i;
			 $fingsm="fingsm_".$hide_fabric_description."_".$i;
			 $slength="slength_".$hide_fabric_description."_".$i;
			 $yarncount="yarncount_".$hide_fabric_description."_".$i;
			 $lotno="lotno_".$hide_fabric_description."_".$i;
			 $brand="brand_".$hide_fabric_description."_".$i;
			 $lib_composition="subcon_supplier_compo_".$hide_fabric_description."_".$i;
			 $lib_supplier_rateId="subcon_supplier_rateid_".$hide_fabric_description."_".$i;
			 $txtremaks="txtremaks_".$hide_fabric_description."_".$i;

			 $new_array_color=return_library_array( "select a.fabric_color_id,b.id,b.color_name from wo_booking_dtls a, lib_color b where b.id=a.fabric_color_id and a.pre_cost_fabric_cost_dtls_id=".$$fabric_description_id."", "id", "color_name"  );
			 if(str_replace("'","",$$item_color)!="")
			 {
				 if (!in_array(str_replace("'","",$$item_color),$new_array_color))
				 {
					  $color_id = return_id( str_replace("'","",$$item_color), $color_library, "lib_color", "id,color_name","176");
					  $new_array_color[$color_id]=str_replace("'","",$$item_color);
				 }
				 else $color_id =  array_search(str_replace("'","",$$item_color), $new_array_color);
			 }
			 else $color_id =0; 
			 
			 if(str_replace("'","",$cbo_process)=='' || str_replace("'","",$cbo_process)==0) $cbo_process=0;else $cbo_process=$cbo_process;
			 if ($i!=1) $data_array1 .=",";
			 $data_array1 .="(".$id_dtls.",".$booking_mst_id.",".$$fabric_description_id.",573,".$$artworkno.",".$$po_id.",".$$color_size_table_id.",".$$txt_job_no.",'".$response_booking_no."',3,".$color_id.",".$$gmts_color_id.",".$$item_size.",".$$gmts_size_id.",".$$fabric_description_id.",".$$uom.",".$cbo_process.",".$cbo_colorsizesensitive.",".$$txt_woqnty.",".$$txt_rate.",".$$txt_amount.",".$$startdate.",".$$enddate.",".$$findia.",".$$findia.",".$$labdipno.",".$$mcdia.",".$$fingsm.",".$$slength.",".$$yarncount.",".$$lotno.",".$$brand.",".$$lib_composition.",".$$lib_supplier_rateId.",".$$txtremaks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
		   $id_dtls=$id_dtls+1;

		 }

		//echo "10**insert into wo_booking_dtls (".$field_array1.") values ".$data_array1;
		//check_table_status( $_SESSION['menu_id'],0);
		//die;
		 $rID1=sql_insert("wo_booking_dtls",$field_array1,$data_array1,0);
	
		check_table_status( $_SESSION['menu_id'],0);
			//echo "10**". $rID.'==';die;
		if($db_type==0)
		{
			if($rID1){
				mysql_query("COMMIT");
				echo "0**".$response_booking_no;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$response_booking_no;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID1){
				oci_commit($con);
				echo "0**".$response_booking_no;
			}
			else{
				oci_rollback($con);
				echo "10**".$response_booking_no;
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here
	{
		 $con = connect();
		 if($db_type==0) mysql_query("BEGIN");
		 $pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id and b.work_order_no=$txt_booking_no and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
		if($pi_number){
			echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
			disconnect($con);die;
		}

		$issue_mrr=0;
		$sqlis=sql_select("select issue_number from inv_issue_master where service_booking_no=$txt_booking_no  and status_active=1 and is_deleted=0");
		foreach($sqlis as $rows){
			$issue_mrr=$rows[csf('issue_number')];
		}
		if($issue_mrr){
			echo "iss1**".str_replace("'","",$txt_booking_no)."**".$issue_mrr;
			disconnect($con);die;
		}
		$updtlsid="";
		for ($i=1;$i<=$row_num;$i++)
		{
			$updateid="updateid_".$hide_fabric_description."_".$i;
			if( trim(str_replace("'",'',$$updateid))!="")
			{
				if($updtlsid=="") $updtlsid=str_replace("'",'',$$updateid); else $updtlsid.=','.str_replace("'",'',$$updateid);
			}
		}
		
		$updtlsid=implode(",",array_filter(array_unique(explode(",",$updtlsid))));
		$dtlsidCond="";
		if($updtlsid!="") $dtlsidCond="and b.booking_dtls_id in ($updtlsid)";
		$issueTofinProcess_mrr=0;
		$sqlissueFinProcess=sql_select("select a.recv_number,b.batch_issue_qty,b.booking_dtls_id from inv_receive_mas_batchroll a, pro_grey_batch_dtls b where a.id=b.mst_id and b.booking_no=$txt_booking_no and a.receive_basis=2 and a.entry_form=91 and a.status_active=1 $dtlsidCond and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		//echo "issFinPrcess**select a.recv_number from inv_receive_mas_batchroll a, pro_grey_batch_dtls b where a.id=b.mst_id and b.booking_no=$txt_booking_no and a.receive_basis=2 and a.entry_form=91 and a.status_active=1 $dtlsidCond and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";die;
		$tot_batch_issue_qty=0;$issueTofinProcess_mrr="";
		foreach($sqlissueFinProcess as $rows){
			$issueTofinProcess_mrr.=$rows[csf('recv_number')].',';
			$tot_batch_issue_qtyArr[$rows[csf('booking_dtls_id')]]+=$rows[csf('batch_issue_qty')];
		}
						//For Validation Check //-----issue Id=20829
			 for ($i=1;$i<=$row_num;$i++)
			 {
				 $po_id="po_id_".$hide_fabric_description."_".$i;
				
				 $tot_woqnty+=str_replace("'","",$$txt_woqnty);
				 $updateid="updateid_".$hide_fabric_description."_".$i;
				 $txt_woqnty="txt_woqnty_".$hide_fabric_description."_".$i;
				 $updateId=str_replace("'",'',$$updateid);
				 $gmts_color_id="gmts_color_id_".$hide_fabric_description."_".$i;
				 $woqnty_chk=str_replace("'",'',$$txt_woqnty);
				  $gmts_color_name=$color_library[str_replace("'",'',$$gmts_color_id)];
				if(trim($updateId)!="")
				{
					  $tot_batch_issue_qty=$tot_batch_issue_qtyArr[$updateId];
					  if($tot_batch_issue_qty>0 && $tot_batch_issue_qty>$woqnty_chk)
					  {
						 $issueTofinProcess_noAll=rtrim($issueTofinProcess_mrr,',');
						  $issue_mrr_no=implode(",",array_unique(explode(",", $issueTofinProcess_noAll)));
						// $msg=$gmts_color_name.", Issue Qty=".$tot_batch_issue_qty;
						  $msg="You can revised up to issue qty.";
						  echo "issFinPrcess**".str_replace("'","",$txt_booking_no)."**".$issue_mrr_no.'**'.$msg;
							disconnect($con);die;
					  }
				}
				// $poid=str_replace("'","",$$po_id);
				// $poArr[$poid]=$poid;
			 }
			 
		if($issueTofinProcess_mrr){
			//echo "issFinPrcess**".str_replace("'","",$txt_booking_no)."**".$issueTofinProcess_mrr;
			//disconnect($con);die;
		}
		 $process=str_replace("'","",$cbo_process);
		 $fab_source=return_field_value("excut_source", "variable_order_tracking", "company_name=$cbo_company_name  and variable_list=66  and status_active=1 and is_deleted=0");
		 if($fab_source>0) $fab_source=$fab_source; else $fab_source=0;
		//echo "10**".$fab_source;die;
		 $poArr=array();
		if($fab_source==2 || $fab_source==1)
		{
			$poArr=array();$tot_woqnty=0;//For Validation Check
			 for ($i=1;$i<=$row_num;$i++)
			 {
				 $po_id="po_id_".$hide_fabric_description."_".$i;
				 $txt_woqnty="txt_woqnty_".$hide_fabric_description."_".$i;
				 $tot_woqnty+=str_replace("'","",$$txt_woqnty);
				 $poid=str_replace("'","",$$po_id);
				 $poArr[$poid]=$poid;
			 }
			$prev_book_qty_po_arr=array();//and is_short=1 
			$po_fab_sql=sql_select("select  po_break_down_id as po_id,
			sum(case when booking_type=3 then wo_qnty else 0 end) as service_wo_qnty,
			sum(case when booking_type=1   then grey_fab_qnty else 0 end) as fab_wo_qnty
			from wo_booking_dtls where po_break_down_id in(".implode(",",$poArr).")  and booking_no!=$txt_booking_no and booking_type in(1,3)
			and status_active=1 and is_deleted=0 group by po_break_down_id");
			$tot_prev_service_wo_qnty=$fab_req_wo_qnty=0;
			foreach($po_fab_sql as $row){
				$tot_prev_service_wo_qnty+=$row[csf('service_wo_qnty')];
				$fab_req_wo_qnty+=$row[csf('fab_wo_qnty')];
			}
			$tot_service_wo_qnty=($tot_prev_service_wo_qnty+$tot_woqnty)-$tot_prev_service_wo_qnty;
		}
		if($fab_source==2)
		{
			if ($fab_req_wo_qnty > 0)
			{
				//if($tot_service_wo_qnty>$fab_req_wo_qnty)
				if(($tot_service_wo_qnty-$fab_req_wo_qnty)>1)
				{
					echo "17**Fabric Req is Over**".$tot_service_wo_qnty."**".$fab_req_wo_qnty;disconnect($con);die;
				}
			}
		}
		else if($fab_source==1) //Budget
		{
			$condition= new condition();
			//$condition->company_name("=$com");
			$poid=implode(",",$poArr);
			if($poid!='' || $poid!=0)
			{
				$condition->po_id("in($poid)");
			}
			$condition->init();
			$conversion= new conversion($condition);
			//echo "10**".$conversion->getQuery();die;
			$conv_po_qty_arr=$conversion->getQtyArray_by_order();
			$tot_budget_conv_qty=0;
			foreach($poArr as $poID)
			{
				$tot_budget_conv_qty+=array_sum($conv_po_qty_arr[$poID]);
			}
			$tot_service_wo_qnty=$tot_prev_service_wo_qnty+$tot_woqnty;
			//echo "17**Budget Req is Over**".$tot_service_wo_qnty."**".$tot_budget_conv_qty;die;
			if($tot_service_wo_qnty>$tot_budget_conv_qty)
			{
				echo "17**Budget Req is Over**".$tot_service_wo_qnty."**".$tot_budget_conv_qty;disconnect($con);die;
			}
		}

		 //=======================================================================================================

		// if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**1"; die;}
		$id_dtls=return_next_id( "id", "wo_booking_dtls", 1 ) ;
		 $field_array1="id, booking_mst_id, pre_cost_fabric_cost_dtls_id, entry_form_id, artwork_no, po_break_down_id, color_size_table_id, job_no, booking_no, booking_type, fabric_color_id, gmts_color_id, item_size, gmts_size, description, uom, process, sensitivity, wo_qnty, rate, amount, delivery_date, delivery_end_date, dia_width, fin_dia, labdip_no, mc_dia, fin_gsm, slength, yarn_count, lot_no, brand, lib_composition, lib_supplier_rate_id, remark, inserted_by, insert_date, status_active, is_deleted";

		 $field_array_up1="pre_cost_fabric_cost_dtls_id*artwork_no*po_break_down_id*color_size_table_id*job_no*booking_type*fabric_color_id*gmts_color_id*item_size*gmts_size*description*uom*process*sensitivity*wo_qnty*rate*amount*delivery_date*delivery_end_date*dia_width*fin_dia*labdip_no*mc_dia*fin_gsm*slength*yarn_count*lot_no*brand*lib_composition* lib_supplier_rate_id*remark*updated_by*update_date";
		 $new_array_color=array();
		 for ($i=1;$i<=$row_num;$i++)
		 {
			 $po_id="po_id_".$hide_fabric_description."_".$i;
			 $txt_job_no="txt_job_no_".$hide_fabric_description."_".$i;
			 $fabric_description_id="fabric_description_id_".$hide_fabric_description."_".$i;
			 $artworkno="artworkno_".$hide_fabric_description."_".$i;
             $color_size_table_id="color_size_table_id_".$hide_fabric_description."_".$i;
			 $gmts_color_id="gmts_color_id_".$hide_fabric_description."_".$i;
			 $item_color_id="item_color_id_".$hide_fabric_description."_".$i;
			 $item_color="item_color_".$hide_fabric_description."_".$i;
			 $gmts_size_id="gmts_size_id_".$hide_fabric_description."_".$i;
			 $item_size="item_size_".$hide_fabric_description."_".$i;
			 $uom="uom_".$hide_fabric_description."_".$i;
			 $txt_woqnty="txt_woqnty_".$hide_fabric_description."_".$i;
			 $txt_rate="txt_rate_".$hide_fabric_description."_".$i;
			 $txt_amount="txt_amount_".$hide_fabric_description."_".$i;
			 $txt_paln_cut="txt_paln_cut".$hide_fabric_description."_".$i;
			 $updateid="updateid_".$hide_fabric_description."_".$i;
			 $startdate="startdate_".$hide_fabric_description."_".$i;
			 $enddate="enddate_".$hide_fabric_description."_".$i;
			 $findia="findia_".$hide_fabric_description."_".$i;

			 $labdipno="labdipno_".$hide_fabric_description."_".$i;
			 $mcdia="mcdia_".$hide_fabric_description."_".$i;
			 $fingsm="fingsm_".$hide_fabric_description."_".$i;
			 $slength="slength_".$hide_fabric_description."_".$i;
			 $yarncount="yarncount_".$hide_fabric_description."_".$i;
			 $lotno="lotno_".$hide_fabric_description."_".$i;
			 $brand="brand_".$hide_fabric_description."_".$i;
			 $lib_composition="subcon_supplier_compo_".$hide_fabric_description."_".$i;
			 $lib_supplier_rateId="subcon_supplier_rateid_".$hide_fabric_description."_".$i;
			 $txtremaks="txtremaks_".$hide_fabric_description."_".$i;

		     $new_array_color=return_library_array( "select a.fabric_color_id,b.id,b.color_name from wo_booking_dtls a, lib_color b where b.id=a.fabric_color_id and a.pre_cost_fabric_cost_dtls_id=".$$fabric_description_id."", "id", "color_name"  );
			 if(str_replace("'","",$$item_color)!="")
			 {
				 if (!in_array(str_replace("'","",$$item_color),$new_array_color))
				 {
					  $color_id = return_id( str_replace("'","",$$item_color), $color_library, "lib_color", "id,color_name","176");
					  $new_array_color[$color_id]=str_replace("'","",$$item_color);
				 }
				 else $color_id =  array_search(str_replace("'","",$$item_color), $new_array_color);
			 }
			 else $color_id =0;

			  if(str_replace("'","",$cbo_process)=='' || str_replace("'","",$cbo_process)==0) $cbo_process=0;else $cbo_process=$cbo_process;

			if(str_replace("'",'',$$updateid)!="")
			{
				$id_arr[]=str_replace("'",'',$$updateid);
				$data_array_up1[str_replace("'",'',$$updateid)] =explode("*",("".$$fabric_description_id."*".$$artworkno."*".$$po_id."*".$$color_size_table_id."*".$$txt_job_no."*3*".$color_id."*".$$gmts_color_id."*".$$item_size."*".$$gmts_size_id."*".$$fabric_description_id."*".$$uom."*".$cbo_process."*".$cbo_colorsizesensitive."*".$$txt_woqnty."*".$$txt_rate."*".$$txt_amount."*".$$startdate."*".$$enddate."*".$$findia."*".$$findia."*".$$labdipno."*".$$mcdia."*".$$fingsm."*".$$slength."*".$$yarncount."*".$$lotno."*".$$brand."*".$$lib_composition."*".$$lib_supplier_rateId."*".$$txtremaks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			}
			else
			{
				if ($i!=1) $data_array1 .=",";
			 	$data_array1 .="(".$id_dtls.",".$booking_mst_id.",".$$fabric_description_id.",573,".$$artworkno.",".$$po_id.",".$$color_size_table_id.",".$$txt_job_no.",'".$response_booking_no."',3,".$color_id.",".$$gmts_color_id.",".$$item_size.",".$$gmts_size_id.",".$$fabric_description_id.",".$$uom.",".$cbo_process.",".$cbo_colorsizesensitive.",".$$txt_woqnty.",".$$txt_rate.",".$$txt_amount.",".$$startdate.",".$$enddate.",".$$findia.",".$$findia.",".$$labdipno.",".$$mcdia.",".$$fingsm.",".$$slength.",".$$yarncount.",".$$lotno.",".$$brand.",".$$lib_composition.",".$$lib_supplier_rateId.",".$$txtremaks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
		   $id_dtls=$id_dtls+1;
			}
		 }

		 $rID1=execute_query(bulk_update_sql_statement( "wo_booking_dtls", "id", $field_array_up1, $data_array_up1, $id_arr ));
		// echo "10**".bulk_update_sql_statement( "wo_booking_dtls", "id", $field_array_up1, $data_array_up1, $id_arr );die;

     // check_table_status( $_SESSION['menu_id'],0);
	//  echo "10**".$rID1.'='.$rID1;die;
		if($db_type==0)
		{
			if($rID1){
				mysql_query("COMMIT");
				echo "1**".str_replace("'","",$txt_booking_no);
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID1){
				oci_commit($con);
				echo "1**".str_replace("'","",$txt_booking_no);
			}
			else{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)   // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id and b.work_order_no=$txt_booking_no and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
		if($pi_number){
			echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
			disconnect($con);die;
		}

		$issue_mrr=0;
		$sqlis=sql_select("select issue_number from inv_issue_master where service_booking_no=$txt_booking_no  and status_active=1 and is_deleted=0");
		foreach($sqlis as $rows){
			$issue_mrr=$rows[csf('issue_number')];
		}
		if($issue_mrr){
			echo "iss1**".str_replace("'","",$txt_booking_no)."**".$issue_mrr;
			disconnect($con);die;
		}
		
		$updtlsid="";
		for ($i=1;$i<=$row_num;$i++)
		{
			$updateid="updateid_".$hide_fabric_description."_".$i;
			if( trim(str_replace("'",'',$$updateid))!="")
			{
				if($updtlsid=="") $updtlsid=str_replace("'",'',$$updateid); else $updtlsid.=','.str_replace("'",'',$$updateid);
			}
		}
		
		$updtlsid=implode(",",array_filter(array_unique(explode(",",$updtlsid))));
		$dtlsidCond="";
		if($updtlsid!="") $dtlsidCond="and b.booking_dtls_id in ($updtlsid)";
		$issueTofinProcess_mrr=0;
		$sqlissueFinProcess=sql_select("select a.recv_number from inv_receive_mas_batchroll a, pro_grey_batch_dtls b where a.id=b.mst_id and b.booking_no=$txt_booking_no and a.receive_basis=2 and a.entry_form=91 and a.status_active=1 $dtlsidCond and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		foreach($sqlissueFinProcess as $rows){
			$issueTofinProcess_mrr=$rows[csf('recv_number')];
		}
		if($issueTofinProcess_mrr){
			echo "issFinPrcess**".str_replace("'","",$txt_booking_no)."**".$issueTofinProcess_mrr;
			disconnect($con);die;
		}
		
		$process=str_replace("'","",$cbo_process);
		$field_array_del="status_active*is_deleted*updated_by*update_date";
		//$field_array="updated_by*update_date*status_active*is_deleted";
		//$data_array_up1="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";

		 for ($i=1;$i<=$row_num;$i++)
		 {
			 //$updateid="updateid_".$hide_fabric_description."_".$i;
			 $updateid="updateid_".$hide_fabric_description."_".$i;
			if(str_replace("'",'',$$updateid)!="")
			{
				$id_arr[]=str_replace("'",'',$$updateid);
				$data_array_up1[str_replace("'",'',$$updateid)] =explode("*",("0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			}
		 }
		$rID=execute_query(bulk_update_sql_statement( "wo_booking_dtls", "id", $field_array_del, $data_array_up1, $id_arr ));
		//$rID=sql_delete("wo_booking_mst",$field_array,$data_array,"booking_no","".$txt_booking_no."",1);
		//$rID1=sql_delete("wo_booking_dtls",$field_array,$data_array,"booking_no","".$txt_booking_no."",1);
		if($db_type==0)
		{
			if($rID){
				mysql_query("COMMIT");
				echo "2**".str_replace("'","",$txt_booking_no);
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID){
				oci_commit($con);
				echo "2**".str_replace("'","",$txt_booking_no);
			}
			else{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="show_detail_booking_list_view")
{
	$data=explode("**",$data);
	$job_po_id=$data[0];
	$type=$data[1];
	$fabric_description_id=$data[2];
	$process=$data[3];
	$sensitivity=$data[4];
	$txt_booking_no=$data[6];
	$dtls_id=implode(",",explode(",",$data[7]));
	
	$company=$data[7];
	$booking_date=$data[8];
	$currency_id=$data[9];
	$rate_from_library=$data[10];
	$fab_req_source=$data[11];
	$fabric_description_array_empty=array();
	$fabric_description_array=array();

	//$fab_req_source=return_field_value("excut_source", "variable_order_tracking", "company_name=$company and variable_list=66 and status_active=1 and is_deleted=0");
	//if($fab_req_source=="" || $fab_req_source==0) $fab_req_source=1;else $fab_req_source=$fab_req_source;
	
	
	if($currency_id==1)
	{
	 $exchange_rate=set_conversion_rate( 2, $booking_date,$company );
	}
	//echo $currency_id.'='.$booking_date.'='.$exchange_rate;
	
	$fabric_description_array=array();//
	$wo_pre_cost_fab_co_color_sql=sql_select("select b.gmts_color_id,b.contrast_color_id,c.id as fab_dtls_id from wo_pre_cos_fab_co_color_dtls b,wo_pre_cost_fab_conv_cost_dtls c  where  c.job_id=b.job_id and c.id=$fabric_description_id and b.pre_cost_fabric_cost_dtls_id=c.fabric_description and b.status_active=1 and c.status_active=1");
	foreach( $wo_pre_cost_fab_co_color_sql as $row)
	{
		$contrast_color_arr[$row[csf('fab_dtls_id')]][$row[csf('gmts_color_id')]]['contrast_color']=$row[csf('contrast_color_id')];
	}

	

	if($rate_from_library==1)
	{
		$rate_disable="disabled";
	}
	else
	{
		$fab_mapping_disable="disabled";
	}
$wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select c.job_no,c.id,c.fabric_description,c.cons_process from wo_pre_cost_fab_conv_cost_dtls c,wo_po_break_down b where  c.job_id=b.job_id and b.id in($job_po_id) and b.status_active=1 and c.status_active=1  group by c.job_no,c.id,c.fabric_description,c.cons_process");
		foreach( $wo_pre_cost_fab_conv_cost_dtls_id as $row_wo_pre_cost_fab_conv_cost_dtls_id)
		{
		if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]!=0)
		{
		
			$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description from  wo_pre_cost_fabric_cost_dtls
			where  id='".$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]."'");
			list($fabric_description_row)=$fabric_description;
			$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]=$body_part[$fabric_description_row[csf("body_part_id")]].', '.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")];
		}
		if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]==0)
		{
		
			$fabric_description_string="";
			$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description from  wo_pre_cost_fabric_cost_dtls
			where  job_no='".$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("job_no")]."'");
			foreach( $fabric_description as $fabric_description_row)
			{
			$fabric_description_string.=$body_part[$fabric_description_row[csf("body_part_id")]].', '.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")]." and ";
			}
			$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]].=rtrim($fabric_description_string,"and ");
		}
		}

		$condition= new condition();
		if(str_replace("'","",$job_po_id) !=''){
			$condition->po_id("in($job_po_id)");
		}

		$condition->init();

		$conversion= new conversion($condition);
		//echo $conversion->getQuery(); die;
		 // echo $job_no.'ddd';
		 $conversion_fab_knit_qty_arr=$conversion->getQtyArray_by_OrderFabricProcessAndColor();
		 $conversion_fab_knit_amt_arr=$conversion->getAmountArray_by_OrderFabricProcessAndColor();
		 
		$conversion_knit_qty_arr=$conversion->getQtyArray_by_ConversionidOrderColorAndUom();
		$conversion_knit_amt_arr=$conversion->getAmountArray_by_ConversionidOrderColorAndUom();
		//print_r($conversion_knit_qty_arr);
	
		$conversion_color_size_knit_qty_arr=$conversion->getQtyArray_by_ConversionidOrderColorSizeidAndUom();
		$conversion_color_size_knit_amt_arr=$conversion->getAmountArray_by_ConversionidOrderColorSizeidAndUom();
		$conversion_po_size_knit_qty_arr=$conversion->getQtyArray_by_ConversionidOrderSizeidAndUom();
		$conversion_po_size_knit_amt_arr=$conversion->getAmountArray_by_ConversionidOrderSizeidAndUom();
		//echo $fab_req_source.'D';
	if($fab_req_source==1) //Budget
	{
		$wo_pre_cost_fab_avg=sql_select("select b.color_number_id,d.id as fab_dtls_id,b.dia_width,c.gsm_weight,b.po_break_down_id as po_id from wo_pre_cos_fab_co_avg_con_dtls b,wo_pre_cost_fabric_cost_dtls c,wo_pre_cost_fab_conv_cost_dtls d  where  c.job_id=b.job_id and d.job_id=b.job_id and c.id=b.pre_cost_fabric_cost_dtls_id   and d.id in($fabric_description_id) and b.po_break_down_id in($job_po_id) and b.pre_cost_fabric_cost_dtls_id=d.fabric_description");
		foreach( $wo_pre_cost_fab_avg as $row)
		{
			if($row[csf('dia_width')]!='' || $row[csf('gsm_weight')]!='')
			{
				//echo $row[csf('dia_width')].', ';
				$fav_avg_color_arr[$row[csf('fab_dtls_id')]][$row[csf('po_id')]][$row[csf('color_number_id')]]['dia_width']=$row[csf('dia_width')];
				$fav_avg_color_arr[$row[csf('fab_dtls_id')]][$row[csf('po_id')]][$row[csf('color_number_id')]]['gsm_weight']=$row[csf('gsm_weight')];
			}
		}
		if($sensitivity==1 || $sensitivity==3)//AS Per Gmt Color and Contrast Color
		{
			 $sql_conv="SELECT a.job_no,b.id as po_id,b.po_number,min(c.id) as color_size_table_id,c.color_number_id,sum(c.plan_cut_qnty) as plan_cut_qnty, e.id as convid, e.fabric_description as fabric_desc,e.cons_process,e.charge_unit,f.body_part_id,f.uom,f.gsm_weight,f.id as pre_cost_fabric_cost_dtls_id from wo_po_details_master a, wo_po_break_down b ,wo_po_color_size_breakdown c,wo_pre_cost_mst d,wo_pre_cost_fab_conv_cost_dtls e, wo_pre_cost_fabric_cost_dtls f,wo_pre_cos_fab_co_avg_con_dtls g where a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and a.id=f.job_id and a.id=g.job_id and b.id=c.po_break_down_id and b.id=g.po_break_down_id and c.color_number_id=g.color_number_id and  c.size_number_id=g.gmts_sizes and c.item_number_id=f.item_number_id and f.id=g.pre_cost_fabric_cost_dtls_id and e.fabric_description=f.id and e.id in($fabric_description_id) and b.id in($job_po_id) and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 group by a.job_no,b.id,b.po_number,c.color_number_id,e.id,e.fabric_description,e.cons_process,e.charge_unit,f.gsm_weight, f.id,f.body_part_id ,f.uom order by b.id,c.color_number_id";

			$sql2="SELECT b.id as po_id, c.color_number_id,min(c.id)as color_size_table_id,sum(c.plan_cut_qnty) as plan_cut_qnty  from wo_po_break_down b, wo_po_color_size_breakdown c where 	b.job_id=c.job_id and b.id=c.po_break_down_id   and b.id in($job_po_id) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  group by b.id,c.color_number_id";
		}
		else if($sensitivity==2 || $sensitivity==0)//AS Per GMT Size
		{
			$sql_conv="SELECT a.job_no,b.id as po_id,b.po_number,min(c.id) as color_size_table_id,min(c.color_number_id) as color_number_id,c.size_number_id,sum(c.plan_cut_qnty) as plan_cut_qnty, e.fabric_description as fabric_desc,e.cons_process,e.charge_unit,f.body_part_id,f.uom,f.gsm_weight,f.id as pre_cost_fabric_cost_dtls_id from wo_po_details_master a, wo_po_break_down b ,wo_po_color_size_breakdown c,wo_pre_cost_mst d,wo_pre_cost_fab_conv_cost_dtls e, wo_pre_cost_fabric_cost_dtls f,wo_pre_cos_fab_co_avg_con_dtls g where a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and a.id=f.job_id and a.id=g.job_id and b.id=c.po_break_down_id and b.id=g.po_break_down_id and c.color_number_id=g.color_number_id and  c.size_number_id=g.gmts_sizes and c.item_number_id=f.item_number_id and f.id=g.pre_cost_fabric_cost_dtls_id and e.fabric_description=f.id and e.id in($fabric_description_id) and b.id in($job_po_id) and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 group by a.job_no,b.id,b.po_number,c.size_number_id,e.fabric_description,e.cons_process,e.charge_unit,f.gsm_weight, f.id,f.body_part_id ,f.uom order by b.id,c.size_number_id"; 
			$sql2="SELECT b.job_no_mst as job_no_mst, b.id as po_id,min(c.color_number_id) as color_number_id,c.size_number_id, min(c.id) as color_size_table_id,sum(c.plan_cut_qnty) as plan_cut_qnty  from wo_po_break_down b, wo_po_color_size_breakdown c where 	b.job_no_mst=c.job_no_mst and b.id=c.po_break_down_id   and b.id in($job_po_id) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  group by a.job_no,b.id,c.size_number_id";
		}
		else if($sensitivity==4)// AS Per Color and Size
		{
			$sql_conv="SELECT a.job_no,b.id as po_id,b.po_number,min(c.id)as color_size_table_id,c.color_number_id as color_number_id,c.size_number_id,sum(c.plan_cut_qnty) as plan_cut_qnty, e.fabric_description as fabric_desc,e.cons_process,e.charge_unit,f.body_part_id,f.uom,f.gsm_weight,f.id as pre_cost_fabric_cost_dtls_id from wo_po_details_master a, wo_po_break_down b ,wo_po_color_size_breakdown c,wo_pre_cost_mst d,wo_pre_cost_fab_conv_cost_dtls e, wo_pre_cost_fabric_cost_dtls f,wo_pre_cos_fab_co_avg_con_dtls g where a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and a.id=f.job_id and a.id=g.job_id and b.id=c.po_break_down_id and b.id=g.po_break_down_id and c.color_number_id=g.color_number_id and  c.size_number_id=g.gmts_sizes and c.item_number_id=f.item_number_id and f.id=g.pre_cost_fabric_cost_dtls_id and e.fabric_description=f.id and e.id in($fabric_description_id) and b.id in($job_po_id) and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 group by a.job_no, b.id, b.po_number, c.color_number_id, c.size_number_id, e.fabric_description, e.cons_process, e.charge_unit, f.gsm_weight, f.id,f.body_part_id ,f.uom order by b.id,c.color_number_id"; 
			$sql2="SELECT b.id as po_break_down_id, min(c.id)as color_size_table_id,sum(c.plan_cut_qnty) as plan_cut_qnty  from wo_po_break_down b, wo_po_color_size_breakdown c where 	b.job_no_mst=c.job_no_mst and b.id=c.po_break_down_id   and b.id in($job_po_id) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $groupby";
		}
		$dataArray=sql_select($sql_conv);
		if(count($dataArray)==0)
		{
			$dataArray=sql_select($sql2);
		}
		foreach($dataArray as $row)
		{

			$fabric_desc=$body_part[$row[csf('body_part_id')]].','.$row[csf('fabric_desc')].','.$row[csf('gsm_weight')];
			$fab_conv_detail_arr[$fabric_description_id][$row[csf('po_id')]][$row[csf('color_number_id')]]['color_size_table_id']=$row[csf('color_size_table_id')];
			$fab_conv_detail_arr[$fabric_description_id][$row[csf('po_id')]][$row[csf('color_number_id')]]['plan_cut_qnty']=$row[csf('plan_cut_qnty')];
			$fab_conv_detail_arr[$fabric_description_id][$row[csf('po_id')]][$row[csf('color_number_id')]]['charge_unit']=$row[csf('charge_unit')];

			$fab_conv_detail_arr[$fabric_description_id][$row[csf('po_id')]][$row[csf('color_number_id')]]['fabric_description']=$fabric_desc;
			$fab_conv_detail_arr[$fabric_description_id][$row[csf('po_id')]][$row[csf('color_number_id')]]['body_part_id']=$row[csf('body_part_id')];
			$fab_conv_detail_arr[$fabric_description_id][$row[csf('po_id')]][$row[csf('color_number_id')]]['job_no']=$row[csf('job_no')];
			$fab_conv_detail_arr[$fabric_description_id][$row[csf('po_id')]][$row[csf('color_number_id')]]['uom']=$row[csf('uom')];
			$fab_conv_detail_arr[$fabric_description_id][$row[csf('po_id')]][$row[csf('color_number_id')]]['fabric_dtl_id']=$row[csf('pre_cost_fabric_cost_dtls_id')];
			$po_number_arr[$row[csf('po_id')]]=$row[csf('po_number')];
		}		
	}
   else
   { 
		 $sql_data_fab="SELECT d.id as conv_dtls_id,b.pre_cost_fabric_cost_dtls_id as pre_cost_fabric_cost_dtls_id,b.job_no,b.po_break_down_id as po_id,b.sensitivity,b.gmts_color_id as color_number_id,b.fabric_color_id,sum(b.grey_fab_qnty) as grey_fab_qnty,sum(b.amount) as amount,c.po_number,sum(c.plan_cut) as plan_cut_qnty from  wo_pre_cost_fabric_cost_dtls a,wo_booking_dtls b,wo_po_break_down c,wo_pre_cost_fab_conv_cost_dtls d where  b.pre_cost_fabric_cost_dtls_id=a.id and d.fabric_description=a.id and b.job_no=a.job_no and c.id=b.po_break_down_id and  a.job_id=c.job_id and  b.job_no=d.job_no and b.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and b.booking_type=1 and b.po_break_down_id in($job_po_id) and d.id in($fabric_description_id) group by b.job_no, b.pre_cost_fabric_cost_dtls_id, b.po_break_down_id, c.po_number, b.sensitivity, b.gmts_color_id, b.fabric_color_id, d.id"; 
		 $dataArray=sql_select($sql_data_fab);
		foreach($dataArray as $row)
		{
			$fab_req_qty_booking_arr[$row[csf('po_id')]][$row[csf('conv_dtls_id')]][$row[csf('color_number_id')]]['grey_fab_qnty']=$row[csf('grey_fab_qnty')];
			$fab_req_qty_booking_arr[$row[csf('po_id')]][$row[csf('conv_dtls_id')]][$row[csf('color_number_id')]]['amount']=$row[csf('amount')];
			$fab_req_qty_booking_arr[$row[csf('po_id')]][$row[csf('conv_dtls_id')]][$row[csf('color_number_id')]]['rate']=$row[csf('grey_fab_qnty')]/$row[csf('amount')];
			$po_color_req_qty_booking_arr[$row[csf('po_id')]][$row[csf('color_number_id')]]['grey_fab_qnty']=$row[csf('grey_fab_qnty')];
			
			$po_number_arr[$row[csf('po_id')]]=$row[csf('po_number')];
		}
	}

	if($fab_req_source==1 || $fab_req_source==2)//1=Budget,2=Booking
	{
		$sql_data_Priv="SELECT c.id as conv_dtl_id,b.pre_cost_fabric_cost_dtls_id,b.job_no,b.po_break_down_id as po_id,b.sensitivity,b.gmts_color_id,b.fabric_color_id,sum(b.wo_qnty) as wo_qnty,sum(b.amount) as amount from  wo_pre_cost_fab_conv_cost_dtls c,wo_booking_dtls b where  b.pre_cost_fabric_cost_dtls_id=c.id and b.job_id=c.job_id and b.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and b.booking_type=3   and b.po_break_down_id in($job_po_id) and b.wo_qnty>0  group by b.job_no,b.pre_cost_fabric_cost_dtls_id,c.id,b.po_break_down_id,b.sensitivity,b.gmts_color_id,b.fabric_color_id order by b.po_break_down_id,b.gmts_color_id";
	}
	$dataResultPre=sql_select($sql_data_Priv);
	$po_fab_prev_booking_arr=array();
	foreach($dataResultPre as $row)
	{
		if($row[csf('sensitivity')]==1 || $row[csf('sensitivity')]==3)// AS Per Garments/Contrast Color
		{
			
			$po_fab_prev_color_booking_arr[$row[csf('po_id')]][$row[csf('conv_dtl_id')]][$row[csf('gmts_color_id')]]['wo_qnty']=$row[csf('wo_qnty')];
			$po_fab_prev_color_booking_arr[$row[csf('po_id')]][$row[csf('conv_dtl_id')]][$row[csf('gmts_color_id')]]['amount']=$row[csf('amount')];
			$po_fab_prev_booking_arr2[$row[csf('conv_dtl_id')]]['wo_qty']=$row[csf('wo_qnty')];			
		}
	}
	unset($dataResultPre);
	$condition= new condition();
	if(str_replace("'","",$job_po_id) !=''){
		$condition->po_id("in($job_po_id)");
	}

	$condition->init();
	$conversion= new conversion($condition);
	//echo $conversion->getQuery(); die;
	$conversion_fab_knit_qty_arr=$conversion->getQtyArray_by_OrderFabricProcessAndColor();
	$conversion_fab_knit_amt_arr=$conversion->getAmountArray_by_OrderFabricProcessAndColor();
	?>
    <div id="content_search_panel_<?=$fabric_description_id; ?>" class="accord_close">
    <b>Wo Qty:</b> <input type="text" name="txt_distru_woqnty_<?=$fabric_description_id; ?>" id="txt_distru_woqnty_<?=$fabric_description_id; ?>"  style="width:90px;" class="text_boxes_numeric" onChange="fnc_poportionate_qty(this.value,'<?=$fabric_description_id; ?>');"  placeholder="Distribute Wo Qty" />
     <input type="hidden" name="fab_req_source_<?=$fabric_description_id; ?>" id="fab_req_source_<?=$fabric_description_id; ?>" value="<?=$fab_req_source; ?>" style="width:30px;" class="text_boxes" disabled="disabled">
        <table class="rpt_table" border="1" width="1500" cellpadding="0" cellspacing="0" rules="all" id="table_<?=$fabric_description_id; ?>">
            <thead>
                <th>Job No</th>
                <th>Po Number</th>
                <th>Fabric Description</th>
                <th>Artwork No</th>
                <th>Y.Count</th>
                <th>Lot</th>
                <th>Brand</th>
                <th>Labdip No</th>
                <th>Gmts. Color</th>
                <th>Item Color</th>
                <th>Gmts.Size</th>
                <th>Item Size</th>
                <th>Fab. Mapping</th>
                <th>UOM</th>
                <th>M/C Dia</th>
                <th>Fin Dia</th>
                <th>Fin GSM</th>
                <th>S/Length</th>
                <th>Delivery Start Date</th>
                <th>Delivery End Date</th>
                <th>WO. Qty</th>
                <th>Rate</th>
                <th>Amount</th>
                <th>Plan Cut Qty</th>
                <th>Remarks</th>
            </thead>
            <tbody>
            <?

            $i=1;$total_woqnty=0;
            foreach($dataArray as $row)
            {


                if($sensitivity==1 || $sensitivity==3)
                {
                    $priv_wo_qty=$po_fab_prev_color_booking_arr[$row[csf('po_id')]][$fabric_description_id][$row[csf('color_number_id')]]['wo_qnty'];
                    $priv_wo_amt=$po_fab_prev_color_booking_arr[$row[csf('po_id')]][$fabric_description_id][$row[csf('color_number_id')]]['amount'];
                    
                    if($fab_req_source==1) //Budget
                    {
                        $pre_req_qnty=array_sum($conversion_knit_qty_arr[$fabric_description_id][$row[csf("po_id")]][$row[csf("color_number_id")]]);
                        
                        $pre_req_amt=array_sum($conversion_knit_amt_arr[$fabric_description_id][$row[csf("po_id")]][$row[csf('color_number_id')]]);
						//echo $pre_req_qnty.'DD';die;
                    }
                    else  //Booking
                    {
                        $pre_conv_req_qnty=array_sum($conversion_knit_qty_arr[$fabric_description_id][$row[csf("po_id")]][$row[csf("color_number_id")]]);
                        $pre_conv_amt_qnty=array_sum($conversion_knit_amt_arr[$fabric_description_id][$row[csf("po_id")]][$row[csf("color_number_id")]]);
                        $rate=$pre_conv_amt_qnty/$pre_conv_req_qnty;
                        $pre_req_qnty=$fab_req_qty_booking_arr[$row[csf("po_id")]][$fabric_description_id][$row[csf("color_number_id")]]['grey_fab_qnty'];
                        $pre_req_amt=$pre_req_qnty*$rate;
                    }						
                }
                else if($sensitivity==4)
                {
                    $pre_req_qnty=array_sum($conversion_color_size_knit_qty_arr[$fabric_description_id][$row[csf("po_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]);
                    $priv_wo_qty=$po_fab_prev_color_size_booking_arr[$row[csf('po_id')]][$fabric_description_id][$row[csf('color_number_id')]][$row[csf("size_number_id")]]['wo_qnty'];
                    $priv_wo_amt=$po_fab_prev_color_size_booking_arr[$row[csf('po_id')]][$fabric_description_id][$row[csf('color_number_id')]][$row[csf("size_number_id")]]['amount'];
                    $pre_req_amt=$fab_req_qty_booking_arr[$row[csf("po_id")]][$fabric_description_id][$row[csf('color_number_id')]]['amount'];
                }
                else if($sensitivity==2 || $sensitivity==0)
                {
                    $pre_req_qnty=array_sum($conversion_po_size_knit_qty_arr[$fabric_description_id][$row[csf("po_id")]][$row[csf("size_number_id")]]);
                    $priv_wo_qty=$po_fab_prev_size_booking_arr[$row[csf('po_id')]][$fabric_description_id][$row[csf("size_number_id")]]['wo_qnty'];
                    $priv_wo_amt=$po_fab_prev_size_booking_arr[$row[csf('po_id')]][$fabric_description_id][$row[csf("size_number_id")]]['amount'];
                    $pre_req_amt=array_sum($conversion_po_size_knit_amt_arr[$fabric_description_id][$row[csf("po_id")]][$row[csf("size_number_id")]]);
                }

             //echo $pre_req_qnty.'SS';

                $woqnty=0;
                if($row[csf("body_part_id")]==3)
                {
                    $woqnty=$pre_req_qnty*2;
                    $uom_item="1,2";
                    $selected_uom="1";
                }
                else if($row[csf("body_part_id")]==2)
                {
                    $woqnty=$pre_req_qnty*1;
                    $uom_item="1,2";
                    $selected_uom="1";
                }
                else if($row[csf("body_part_id")]!=2 || $row[csf("body_part_id")]!=3 )
                {
                    $woqnty=$pre_req_qnty*1;
                    $selected_uom="12";
                }

                if($row[csf("body_part_id")]==2 || $row[csf("body_part_id")]==3)
                {
                    $rate="";
                    $amount="";
                }
                else
                {

                    if($fab_req_source==1) //Budget
                    {
                        $rate=$row[csf("charge_unit")];
                        $amount=$rate*$woqnty;
                    }
                    else
                    {
                        $rate=$rate;
                        $amount=$rate*$woqnty;
                    }
                }
                //echo $row[csf("body_part_id")].'f';
                //if($sensitivity==1 || $sensitivity==3  || $sensitivity==4 ){ echo $color_library[$row[csf("color_number_id")] ];} else { echo "";}
                //$priv_wo_qty=$po_fab_qty_arr[$row[csf('po_break_down_id')]][$fabric_description_id][$row[csf('color_number_id')]]['wo_qty'];
                //echo $priv_wo_qty;
                //echo $woqnty-$priv_wo_qty.'<br>';
                //echo $woqnty.'-'.$priv_wo_qty.'<br>'


                if($sensitivity==3)
                {
                    $item_color=$color_library[$contrast_color_arr[$row[csf('convid')]][$row[csf('color_number_id')]]['contrast_color']];
                    $item_color_id=$contrast_color_arr[$row[csf('convid')]][$row[csf('color_number_id')]]['contrast_color'];
                }
                else if($sensitivity==1 || $sensitivity==4)
                {
                    $item_color=$color_library[$row[csf("color_number_id")]];
                    $item_color_id=$row[csf("color_number_id")];
                }
                else
                {
                    $item_color="";
                    $item_color_id="";
                }

                //$pre_cost_conv_amount=$conv_data_amount_arr[$row[csf("po_break_down_id")]][$row[csf("fabric_description")]][$row[csf('cons_process')]];
                //echo $pre_cost_conv_amount.'=='.$row[csf("po_break_down_id")]."=".$row[csf("fabric_description")]."=".$row[csf('cons_process')].', ';

            ?>
                <tr align="center">
                    <td>
                        <input type="text" name="txt_job_no_<? echo $fabric_description_id."_".$i; ?>" id="txt_job_no_<? echo $fabric_description_id."_".$i; ?>" value="<? echo $row[csf("job_no")]; ?>" style="width:90px;" class="text_boxes">
                       
                       
                    </td>
                    <td>
                        <?
                            echo create_drop_down("po_no_".$fabric_description_id."_".$i, 110, $po_number_arr,"", 1,'', $row[csf("po_id")],"",1);
                        ?>
                        <input type="hidden" name="po_id_<? echo $fabric_description_id.'_'.$i; ?>" id="po_id_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $row[csf("po_id")]; ?>" style="width:110px;" class="text_boxes" disabled="disabled">
                    </td>

                    <td>
                        <?
                            echo create_drop_down("fabric_description_".$fabric_description_id."_".$i, 250, $fabric_description_array,"", 1,'', $fabric_description_id,"",1);
                        ?>
                        <input type="hidden" name="fabric_description_id_<? echo $fabric_description_id.'_'.$i; ?>" id="fabric_description_id_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $fabric_description_id; ?>" style="width:30px;" class="text_boxes" disabled="disabled">
                    </td>

                    <td>
                        <input type="text" name="artworkno_<? echo $fabric_description_id.'_'.$i; ?>" id="artworkno_<? echo $fabric_description_id.'_'.$i; ?>" value="<? //echo $fabric_description_id; ?>" style="width:80px;" class="text_boxes">
                    </td>
                     <td>
                        <input type="text" name="yarncount_<? echo $fabric_description_id.'_'.$i; ?>" id="yarncount_<? echo $fabric_description_id.'_'.$i; ?>" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'yarncount')"; value="<? //echo $fabric_description_id; ?>" style="width:50px;" class="text_boxes">
                    </td>
                    <td>
                        <input type="text" name="lotno_<? echo $fabric_description_id.'_'.$i; ?>" id="lotno_<? echo $fabric_description_id.'_'.$i; ?>" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'lotno')"; value="<? //echo $fabric_description_id; ?>" style="width:30px;" class="text_boxes">
                    </td>
                    <td>
                        <input type="text" name="brand_<? echo $fabric_description_id.'_'.$i; ?>" id="brand_<? echo $fabric_description_id.'_'.$i; ?>" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'brand')";  value="<? //echo $fabric_description_id; ?>" style="width:40px;" class="text_boxes">
                    </td>
                    <td>
                        <input type="text" name="labdipno_<? echo $fabric_description_id.'_'.$i; ?>" id="labdipno_<? echo $fabric_description_id.'_'.$i; ?>" value="<? //echo $fabric_description_id; ?>" style="width:70px;" class="text_boxes">
                    </td>

                    <td title="<? if($sensitivity==1 || $sensitivity==3  || $sensitivity==4 ){ echo $color_library[$row[csf("color_number_id")] ];} else { echo "";}?>">
                    <input type="hidden" name="color_size_table_id_<? echo $fabric_description_id.'_'.$i; ?>" id="color_size_table_id_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<?  echo $row[csf("color_size_table_id")];?>" disabled="disabled"/>

                        <input type="text" name="gmts_color_<? echo $fabric_description_id.'_'.$i; ?>" id="gmts_color_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<? if($sensitivity==1 || $sensitivity==3  || $sensitivity==4 ){ echo $color_library[$row[csf("color_number_id")] ];} else { echo "";}?>" disabled="disabled"/>
                        <input type="hidden" name="gmts_color_id_<? echo $fabric_description_id.'_'.$i; ?>" id="gmts_color_id_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<? if($sensitivity==1 || $sensitivity==3  || $sensitivity==4 ){ echo $row[csf("color_number_id")];} else { echo "";}?>"disabled="disabled"/>
                    </td>
                    <td>
                        <input type="text" name="item_color_<? echo $fabric_description_id.'_'.$i; ?>" id="item_color_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes" onChange="copy_value()" value="<? echo $item_color; //if($sensitivity==1 || $sensitivity==3  || $sensitivity==4 ){ echo $color_library[$row[csf("color_number_id")]];} else { echo "";}?>"/>
                        <input type="hidden" name="item_color_id_<? echo $fabric_description_id.'_'.$i; ?>" id="item_color_id_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<? echo $item_color_id;//if($sensitivity==1 || $sensitivity==3  || $sensitivity==4 ){ echo $row[csf("color_number_id")];} else { echo "";}?>" disabled="disabled"/>
                    </td>
                    <td>
                        <input type="text" name="gmts_size_<? echo $fabric_description_id.'_'.$i; ?>" id="gmts_size_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<? if($sensitivity==2 || $sensitivity==4 ){echo $size_library[$row[csf("size_number_id")]];} else{ echo "";}?>" disabled="disabled" />
                        <input type="hidden" name="gmts_size_id_<? echo $fabric_description_id.'_'.$i; ?>" id="gmts_size_id_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<? if($sensitivity==2 || $sensitivity==4 ){echo $row[csf("size_number_id")];} else{ echo "";}?>" disabled="disabled"/>
                    </td>
                    <td>
                        <input type="text" name="item_size_<? echo $fabric_description_id.'_'.$i; ?>" id="item_size_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes" onChange="copy_value()" value="<? if($sensitivity==2 || $sensitivity==4 ){ echo $size_library[$row[csf("size_number_id")]];} else{ echo "";}?>">
                        <input type="hidden" name="item_size_id_<? echo $fabric_description_id.'_'.$i; ?>" id="item_size_id_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<? if($sensitivity==2 || $sensitivity==4 ){ echo $row[csf("size_number_id")];} else{ echo "";}?>" disabled="disabled"/>
                        <input type="hidden" name="updateid_<? echo $fabric_description_id.'_'.$i; ?>" id="updateid_<? echo $fabric_description_id.'_'.$i; ?>" value="">
                    </td>
                    <td>
                        <input type="text" name="subcon_supplier_compo_<? echo $fabric_description_id.'_'.$i; ?>" id="subcon_supplier_compo_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="" onDblClick="service_supplier_popup('<? echo $fabric_description_id.'_'.$i; ?>')" placeholder="Browse" <?php echo $fab_mapping_disable; ?>>

                        <input type="hidden" name="subcon_supplier_rateid_<? echo $fabric_description_id.'_'.$i; ?>" id="subcon_supplier_rateid_<? echo $fabric_description_id.'_'.$i; ?>" value="">
                    </td>
                    <td>
                        <?
                        echo create_drop_down("uom_".$fabric_description_id."_".$i, 50, $unit_of_measurement,"", 1, "--Select--",$selected_uom,"copy_value(".$fabric_description_id.",".$i.",'uom')","","$uom_item");
                        ?>
                    </td>
                     <td>
                        <input type="text" name="mcdia_<? echo $fabric_description_id.'_'.$i; ?>" id="mcdia_<? echo $fabric_description_id.'_'.$i; ?>" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'mcdia')" value="<? //echo $row[csf("start_date")]; ?>" style="width:60px;" class="text_boxes">
                    </td>
                     <td>
                        <input type="text" name="findia_<? echo $fabric_description_id.'_'.$i; ?>" id="findia_<? echo $fabric_description_id.'_'.$i; ?>"  onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'findia')"; value="<? //echo $row[csf("start_date")]; ?>" style="width:60px;" class="text_boxes">
                    </td>
                     <td>
                        <input type="text" name="fingsm_<? echo $fabric_description_id.'_'.$i; ?>" id="fingsm_<? echo $fabric_description_id.'_'.$i; ?>" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'fingsm')"; value="<? //echo $row[csf("start_date")]; ?>" style="width:60px;" class="text_boxes">
                    </td>
                     <td>
                        <input type="text" name="slength_<? echo $fabric_description_id.'_'.$i; ?>" id="slength_<? echo $fabric_description_id.'_'.$i; ?>" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'slength')"; value="<? //echo $row[csf("start_date")]; ?>" style="width:60px;" class="text_boxes">
                    </td>
                    <td>
                        <input type="text" name="startdate_<? echo $fabric_description_id.'_'.$i; ?>" id="startdate_<? echo $fabric_description_id.'_'.$i; ?>" value="<? //echo $row[csf("start_date")]; ?>" style="width:60px;" class="datepicker">
                    </td>
                    <td>
                        <input type="text" name="enddate_<? echo $fabric_description_id.'_'.$i; ?>" id="enddate_<? echo $fabric_description_id.'_'.$i; ?>" value="<? // echo $row[csf("end_date")]; ?>" style="width:60px;" class="datepicker">

                    </td>
                    <? //check_wo_qty_row( echo $fabric_description_id;,echo $i;
                    //	echo $woqnty.'='.$priv_wo_qty.'<br/>';
                    $exchange_rate =$exchange_rate ;
                    $wo_qty_bal=($woqnty*1)-($priv_wo_qty*1);
                        //echo $wo_qty_bal.'='.$priv_wo_qty.'<br/>';
                    $hidden_wo_amt=(($woqnty-$priv_wo_qty)*$rate);
                     ?>
                    <td title="<?='Wo Qty='.$woqnty.','.'Prev Wo Qty='.$priv_wo_qty.',Prev wo Amt bal='.$hidden_wo_amt; ?>">
                    	<input type="hidden" name="txt_hidden_woamt_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_hidden_woamt_<? echo $fabric_description_id.'_'.$i; ?>" style="width:30px;" class="text_boxes_numeric" value="<?  if($hidden_wo_amt<1) echo $hidden_wo_amt.'_'.$exchange_rate.'_'.$rate.'_'.$wo_qty_bal.'_'.($woqnty-$priv_wo_qty)*$row[csf("charge_unit")]; else echo number_format($hidden_wo_amt,4,'.','').'_'.$exchange_rate.'_'.$rate.'_'.$wo_qty_bal.'_'.($woqnty-$priv_wo_qty)*$row[csf("charge_unit")]; ?>"/>
                        <input type="text" name="txt_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'txt_woqnty');calculate_amount(<? echo $fabric_description_id; ?>,<? echo $i; ?>)" value="<? if($wo_qty_bal<1) echo $wo_qty_bal; else echo number_format($wo_qty_bal,4,'.',''); ?>"/>
                        <input type="hidden" name="txt_hidden_woqnty_bal_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_hidden_woqnty_bal_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric" value="<? if(($woqnty-$priv_wo_qty)<1) echo $woqnty-$priv_wo_qty;else  echo number_format($woqnty-$priv_wo_qty,4,'.',''); ?>"/>
                        <input type="hidden" name="txt_prev_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_prev_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric" value="<? if($priv_wo_qty<1) echo $priv_wo_qty;else echo number_format($priv_wo_qty,4,'.',''); ?>" />
                        <input type="hidden" name="txt_reqwoqnty_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_reqwoqnty_<? echo $fabric_description_id.'_'.$i; ?>" style="width:30px;" class="text_boxes_numeric" value="<? if($woqnty<1) echo $pre_req_qnty;else echo number_format($pre_req_qnty,4,'.',''); ?>"/>
                        <input type="hidden" name="txt_reqwoamt_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_reqwoamt_<? echo $fabric_description_id.'_'.$i; ?>" style="width:30px;" class="text_boxes_numeric" value="<? if($pre_req_amt<1) echo $pre_req_amt; else echo number_format($pre_req_amt,4,'.',''); ?>"/>
                        <input type="hidden" name="txt_prev_woamt_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_prev_woamt_<? echo $fabric_description_id.'_'.$i; ?>" style="width:30px;" class="text_boxes_numeric" value="<? if($priv_wo_amt<1) echo $priv_wo_amt;else echo number_format($priv_wo_amt,4,'.',''); ?>"/>
                    </td>
                    <td>
                        <input type="text" name="txt_rate_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_rate_<? echo $fabric_description_id.'_'.$i; ?>" style="width:50px;" class="text_boxes_numeric" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'txt_rate');calculate_amount(<? echo $fabric_description_id; ?>,<? echo $i; ?>)" value="<? echo $rate; ?>" <?php echo $rate_disable; ?>>
                         <input type="hidden" name="txt_hidden_rate_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_hidden_rate_<? echo $fabric_description_id.'_'.$i; ?>" style="width:50px;" class="text_boxes_numeric" value="<? echo $row[csf("charge_unit")]; ?>" <?php //echo $rate_disable; ?>>
                    </td>
                    <td>
                        <input type="text" name="txt_amount_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_amount_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric" value="<? echo number_format($amount,4,'.',''); ?>" disabled="disabled"/>
                    </td>
                    <td>
                        <input type="text" name="txt_paln_cut_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_paln_cut_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric" value="<? echo $row[csf("plan_cut_qnty")]; ?>" disabled>
                    </td>
                    <td><input type="text" name="txtremaks_<?=$fabric_description_id.'_'.$i; ?>" id="txtremaks_<?=$fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes" value="<? //=$row[csf("remarks")]; ?>" ></td>
                </tr>
				<?
                $i++;
                $total_woqnty+=$woqnty;
                //echo $priv_wo_qty.'='.$total_woqnty;
            }
            ?>
            </tbody>
        </table>
    </div>
	<?
    exit();
}

if ($action=="fabric_detls_list_view")
{
	$data=explode("**",$data);
	//echo "select b.id,b.po_number from wo_po_break_down b,wo_booking_dtls c where c.job_no=b.job_no_mst and c.booking_no='$data[1]' ";
	$po_number_arr=return_library_array( "select b.id,b.po_number from wo_po_break_down b,wo_booking_dtls c where c.job_no=b.job_no_mst and c.booking_no='$data[1]' ", "id", "po_number"  );
	$internal_ref_arr=return_library_array( "select b.id,b.grouping from wo_po_break_down b,wo_booking_dtls c where c.job_no=b.job_no_mst and c.booking_no='$data[1]' ", "id", "grouping"  );

	$wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select  c.job_no,c.id,c.fabric_description,c.cons_process from wo_booking_dtls b,wo_pre_cost_fab_conv_cost_dtls c  where c.id=b.pre_cost_fabric_cost_dtls_id and b.booking_no='$data[1]' group by c.job_no,c.id,c.fabric_description,c.cons_process");
	//echo "select id,fabric_description,cons_process from wo_pre_cost_fab_conv_cost_dtls  where job_no='$data[0]' ";
	foreach( $wo_pre_cost_fab_conv_cost_dtls_id as $row_wo_pre_cost_fab_conv_cost_dtls_id)
	{
		if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]!=0)
		{

			$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description from  wo_pre_cost_fabric_cost_dtls
			where  id='".$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]."'");
			list($fabric_description_row)=$fabric_description;
			$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]=$body_part[$fabric_description_row[csf("body_part_id")]].',
			'.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")];
		}
		if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]==0)
		{

			$fabric_description_string="";
			$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description from  wo_pre_cost_fabric_cost_dtls
			where  job_no=".$row_wo_pre_cost_fab_conv_cost_dtls_id[csf('job_no')]." ");
			foreach( $fabric_description as $fabric_description_row)
	        {
			$fabric_description_string.=$body_part[$fabric_description_row[csf("body_part_id")]].', '.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")]." and ";
			}
			$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]].=rtrim($fabric_description_string,"and ");
		}
	}

	if($db_type==0) { $group_concat="group_concat(b.po_break_down_id) as order_id"; $group_concat.=",group_concat(b.id) as dtls_id";}
	if($db_type==2)
	 { $group_concat="listagg(cast(b.po_break_down_id as varchar2(4000)),',') within group (order by b.po_break_down_id) as order_id";
	   $group_concat.=",listagg(cast(b.id as varchar2(4000)),',') within group (order by b.id) as dtls_id";
	}
	$sql="select a.id, b.job_no,b.booking_no,$group_concat,b.pre_cost_fabric_cost_dtls_id,sum(b.amount) as amount,b.process,b.sensitivity,
	sum(b.wo_qnty) as wo_qnty,b.insert_date from wo_booking_dtls b, wo_booking_mst a
  	where b.booking_no=a.booking_no and a.booking_no='$data[1]'and b.status_active=1 and a.entry_form=573 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0

  	group by b.job_no,a.id,b.pre_cost_fabric_cost_dtls_id,b.process,b.sensitivity,b.booking_no,b.insert_date";
	//echo $sql;
		?>
    <div id="" style="" class="accord_close">

        <table class="rpt_table" border="1" width="1200" cellpadding="0" cellspacing="0" rules="all" id="">
            <thead>
                <th width="50px">Sl</th>
                <th width="300px">Fabric Description</th>
                <th width="100px">Job No</th>
                <th width="100px">Booking No</th>
                <th width="200px">Po Number</th>
                <th width="100px">Internal Ref</th>
                <th width="100px">Process </th>
                <th width="120px">Sensitivity</th>
                <th width="80px">WO. Qnty</th>
                <th >Amount</th>
                
            </thead>
            <tbody>
            <?
            $dataArray=sql_select($sql);

            $i=1;
            foreach($dataArray as $row)
            {
				$allorder="";
				$all_internal_ref='';
				$all_po_number=explode(",",$row[csf('order_id')]);
				foreach($all_po_number as $po_id)
				{
					if($allorder!="") 	$allorder.=",".$po_number_arr[$po_id];
					else 	$allorder=$po_number_arr[$po_id];

					if($all_internal_ref!="") 	$all_internal_ref.="***".$internal_ref_arr[$po_id];
					else 	$all_internal_ref=$internal_ref_arr[$po_id];

				}
                $all_po_nos=implode(", ",array_unique(explode(",",$allorder)));
                $all_internal_ref=implode(", ",array_unique(explode("***",$all_internal_ref)));
            ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick='update_booking_data("<? echo $row[csf("dtls_id")]."_".$row[csf("job_no")]."_".$row[csf("pre_cost_fabric_cost_dtls_id")]."_".$row[csf("process")]."_".$row[csf("sensitivity")]."_".$row[csf("order_id")]."_".$row[csf("booking_no")]."_".$all_po_nos;?>")' style="cursor:pointer" >
                    <td> <? echo $i; ?>

                        <input type="hidden" name="po_id_<? echo $fabric_description_id.'_'.$i; ?>" id="po_id_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $row[csf("po_break_down_id")]; ?>" style="width:110px;" class="text_boxes" disabled="disabled">
                    </td>
                    <td><p><? echo  $fabric_description_array[$row[csf('pre_cost_fabric_cost_dtls_id')]]; ?></p> </td>

                    <td><? echo  $row[csf('job_no')]; ?></td>
                    <td><? echo  $row[csf('booking_no')]; ?></td>
                     <td width="200px" style="word-break:break-all">	<p><? echo  $all_po_nos; ?></p></td>
                     <td width="100px" style="word-break:break-all">	<p><? echo  $all_internal_ref; ?></p></td>
                    <td><? echo  $conversion_cost_head_array[$row[csf('process')]]; ?></td>
                    <td><? echo  $size_color_sensitive[$row[csf('sensitivity')]]; ?></td>
                    <td><? echo  number_format($row[csf('wo_qnty')],2); ?></td>
                    <td><? echo  number_format($row[csf('amount')],2); ?></td>
                   
                </tr>
            <?
            $i++;
            }
            ?>
            </tbody>
        </table>
    </div>
	<?
	exit();
}

if($action=="update_detail_booking_list_view")
{
	$data=explode("**",$data);

	$job_po=$data[0];
	//$type=$data[1];
	$process=$data[3];
	$sensitivity=$data[4];
	$txt_booking_no=$data[6];
	
	$dtls_id=implode(",",explode(",",$data[7]));
	$rate_from_library=$data[8];
    $job_po_id=$data[5];
	 $bookingNo=$data[6];
	$company=$data[10];
	$fabric_description_id=$data[2];
	$fabric_description_array_empty=array();
	$fabric_description_array=array();

	$fab_req_source=return_field_value("excut_source", "variable_order_tracking", "company_name=$company and variable_list=66 and status_active=1 and is_deleted=0");
	//$exchange_rate=return_field_value("exchange_rate", "wo_booking_mst", "booking_no='$bookingNo' and status_active=1 and is_deleted=0");
	$sql_book=sql_select("select exchange_rate,currency_id,booking_date from wo_booking_mst where  booking_no='$bookingNo' and status_active=1 and is_deleted=0");
	$exchange_rate=$sql_book[0][csf('exchange_rate')];
	$currency_id=$sql_book[0][csf('currency_id')];
	$booking_date=$sql_book[0][csf('booking_date')];
	if($currency_id==1)
	{
	 $exchange_rate=set_conversion_rate( 2, $booking_date,$company );
	} 
	
	

	//echo $exchange_rate.'='.$currency_id.'='.$booking_date;
	if($fab_req_source=="" || $fab_req_source==0) $fab_req_source=1;else $fab_req_source=$fab_req_source;
	/*if($fab_req_source==2)
	{
		$fabric_description_array=array();
		$wo_booking=sql_select("select b.pre_cost_fabric_cost_dtls_id as fab_dtls_id,c.body_part_id,c.color_type_id,c.fabric_description from wo_booking_dtls b,wo_pre_cost_fabric_cost_dtls c  where  b.pre_cost_fabric_cost_dtls_id=c.id and b.po_break_down_id in(".$job_po_id.")  and b.booking_type=1  and b.status_active=1 and b.is_deleted=0 group by b.pre_cost_fabric_cost_dtls_id,c.body_part_id,c.color_type_id,c.fabric_description ");
		//echo "select b.po_break_down_id,b.construction,b.copmposition,b.gsm_weight,b.dia_width,b.is_short,b.pre_cost_fabric_cost_dtls_id as fab_dtls_id,c.body_part_id,c.color_type_id,c.fabric_description from wo_booking_dtls b,wo_pre_cost_fabric_cost_dtls c  where  b.pre_cost_fabric_cost_dtls_id=c.id and b.po_break_down_id in(".$job_po_id.")  and b.booking_type=1  and b.status_active=1 and b.is_deleted=0";
			
		
	
		foreach( $wo_booking as $row)
		{
				$fabric_description_array[$row[csf("fab_dtls_id")]]=$body_part[$row[csf("body_part_id")]].', '.$color_type[$row[csf("color_type_id")]].', '.$row[csf("fabric_description")];
		}
	}
	else
	{*/
		$wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select b.id as po_id,b.po_number,c.job_no,c.id,c.fabric_description,c.cons_process from wo_pre_cost_fab_conv_cost_dtls c,wo_po_break_down b where  c.job_no=b.job_no_mst and b.id in($job_po_id) group by b.id, b.po_number,c.job_no,  c.id,c.fabric_description,c.cons_process");

	foreach( $wo_pre_cost_fab_conv_cost_dtls_id as $row_wo_pre_cost_fab_conv_cost_dtls_id)
	{
		$po_number_arr[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("po_id")]]=$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("po_number")];
		$fab_dtls_id_arr[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]=$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")];

		if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]!=0)
		{
			$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description from  wo_pre_cost_fabric_cost_dtls
			where  id='".$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]."'");
			list($fabric_description_row)=$fabric_description;
			$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]=$body_part[$fabric_description_row[csf("body_part_id")]].', '.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")];
		}
		if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]==0)
		{
			$fabric_description_string="";
			$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description from  wo_pre_cost_fabric_cost_dtls
			where  job_no='".$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("job_no")]."'");
			foreach( $fabric_description as $fabric_description_row)
	        {
			$fabric_description_string.=$body_part[$fabric_description_row[csf("body_part_id")]].', '.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")]." and ";
			}
			$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]].=rtrim($fabric_description_string,"and ");
		}
	}
	unset($wo_pre_cost_fab_conv_cost_dtls_id);
	//}
	

	
//	print_r($fabric_description_array);
	$wo_pre_cost_fab_co_color_sql=sql_select("select b.gmts_color_id,b.contrast_color_id,c.id as fab_dtls_id from wo_pre_cos_fab_co_color_dtls b,wo_pre_cost_fab_conv_cost_dtls c  where  c.job_no=b.job_no and c.id=$data[2] and b.pre_cost_fabric_cost_dtls_id=c.fabric_description");
	//echo "select b.gmts_color_id,b.contrast_color_id,c.id as fab_dtls_id from wo_pre_cos_fab_co_color_dtls b,wo_pre_cost_fab_conv_cost_dtls c  where  c.job_no=b.job_no and c.id=$data[2] and b.pre_cost_fabric_cost_dtls_id=c.fabric_description";
	foreach( $wo_pre_cost_fab_co_color_sql as $row)
	{
		$contrast_color_arr[$row[csf('fab_dtls_id')]][$row[csf('gmts_color_id')]]['contrast_color']=$row[csf('contrast_color_id')];
	}
	unset($wo_pre_cost_fab_co_color_sql);
	 $sql_data_Priv="select c.id as conv_dtl_id,b.job_no,b.gmts_size,b.po_break_down_id as po_id,b.sensitivity,b.uom,b.gmts_color_id,b.fabric_color_id,sum(b.wo_qnty) as wo_qnty,sum(b.amount) as amount from  wo_pre_cost_fab_conv_cost_dtls c,wo_booking_dtls b where  b.pre_cost_fabric_cost_dtls_id=c.id and b.job_no=c.job_no and b.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and b.booking_type=3 and b.booking_no!='$txt_booking_no' and b.wo_qnty>0 and  b.po_break_down_id in($job_po_id) and c.id in($fabric_description_id)   and c.is_deleted=0 and c.status_active=1 and b.is_deleted=0 and b.status_active=1 group by b.job_no,c.id,b.po_break_down_id,b.sensitivity,b.gmts_size,b.uom,b.gmts_color_id,b.fabric_color_id";

		$dataResultPre=sql_select($sql_data_Priv);
		$po_fab_prev_booking_arr=array();
		foreach($dataResultPre as $row)
		{
			if($row[csf('sensitivity')]=="") $row[csf('sensitivity')]=0;
			
			if($row[csf('sensitivity')]==1 || $row[csf('sensitivity')]==3)// AS Per Garments/Contrast Color
			{
				$po_fab_prev_color_booking_arr[$row[csf('po_id')]][$row[csf('conv_dtl_id')]][$row[csf('gmts_color_id')]]['wo_qnty']=$row[csf('wo_qnty')];
				$po_fab_prev_color_booking_arr[$row[csf('po_id')]][$row[csf('conv_dtl_id')]][$row[csf('gmts_color_id')]]['amount']=$row[csf('amount')];
				$po_fab_prev_booking_arr2[$row[csf('conv_dtl_id')]]['wo_qty']=$row[csf('wo_qnty')];
			}
			else if($row[csf('sensitivity')]==2 || $row[csf('sensitivity')]==0)// AS Per Size
			{
				$po_fab_prev_color_booking_arr[$row[csf('po_id')]][$row[csf('conv_dtl_id')]][$row[csf('gmts_size')]]['wo_qnty']=$row[csf('wo_qnty')];
				$po_fab_prev_color_booking_arr[$row[csf('po_id')]][$row[csf('conv_dtl_id')]][$row[csf('gmts_size')]]['amount']=$row[csf('amount')];
				$po_fab_prev_booking_arr2[$row[csf('conv_dtl_id')]]['wo_qty']=$row[csf('wo_qnty')];
			}
			else if($row[csf('sensitivity')]==4)// AS Per Color & Size
			{
				$po_fab_prev_color_booking_arr[$row[csf('po_id')]][$row[csf('conv_dtl_id')]][$row[csf('gmts_color_id')]][$row[csf('gmts_size')]]['wo_qnty']=$row[csf('wo_qnty')];
				$po_fab_prev_color_booking_arr[$row[csf('po_id')]][$row[csf('conv_dtl_id')]][$row[csf('gmts_color_id')]][$row[csf('gmts_size')]]['amount']=$row[csf('amount')];
				$po_fab_prev_booking_arr2[$row[csf('conv_dtl_id')]]['wo_qty']=$row[csf('wo_qnty')];
			}

		}
	unset($dataResultPre);
	//echo $fab_req_source.'xxxxxxxx';
		$condition= new condition();
		if(str_replace("'","",$job_po_id) !=''){
			$condition->po_id("in($job_po_id)");
		}

		$condition->init();

		$conversion= new conversion($condition);
		$conversion_knit_qty_arr=$conversion->getQtyArray_by_ConversionidOrderColorAndUom();
		$conversion_knit_amt_arr=$conversion->getAmountArray_by_ConversionidOrderColorAndUom();
		//echo "<pre>";
			//print_r($conversion_knit_amt_arr[3930]);// die;
			//echo $conversion->getQuery(); die;
	
		$conversion_color_size_knit_qty_arr=$conversion->getQtyArray_by_ConversionidOrderColorSizeidAndUom();
		$conversion_color_size_knit_amt_arr=$conversion->getAmountArray_by_ConversionidOrderColorSizeidAndUom();
		$conversion_po_size_knit_qty_arr=$conversion->getQtyArray_by_ConversionidOrderSizeidAndUom();
		$conversion_po_size_knit_amt_arr=$conversion->getAmountArray_by_ConversionidOrderSizeidAndUom();
		
		if($fab_req_source==1) //Budget
		{
			$booking_dtls_sql="select a.id as dtls_id, a.po_break_down_id as po_id, a.pre_cost_fabric_cost_dtls_id as fab_cost_dtls, a.artwork_no, a.labdip_no, a.slength, a.mc_dia, a.mc_gauge, a.fin_dia, a.fin_gsm, a.yarn_count, a.option_shade, a.lot_no, a.brand, a.po_break_down_id as po_id, a.color_size_table_id, a.fabric_color_id, a.item_size, a.process, a.dia_width, a.fabric_color_id,
		a.sensitivity, a.job_no, a.booking_no, a.booking_type, a.description, a.uom, a.delivery_date, a.delivery_end_date, a.sensitivity, a.wo_qnty, a.rate,
		a.amount, a.remark, b.size_number_id, b.color_number_id, a.lib_composition, a.lib_supplier_rate_id, b.color_number_id, b.plan_cut_qnty, a.delivery_end_date, a.delivery_date
		from wo_booking_dtls a, wo_po_color_size_breakdown b where a.job_no=b.job_no_mst and
		a.po_break_down_id=b.po_break_down_id and a.color_size_table_id=b.id  and a.booking_type=3  and
		a.booking_no='$txt_booking_no' and a.id in ($dtls_id) and a.status_active=1 and a.pre_cost_fabric_cost_dtls_id=$data[2] and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 ";
		}
		else
		{
			/* $sql_data_fab="select a.id as fab_dtl_id,b.job_no,b.po_break_down_id as po_id,b.sensitivity,b.gmts_color_id,b.fabric_color_id,sum(b.grey_fab_qnty) as grey_fab_qnty,sum(b.amount) as amount from  wo_pre_cost_fabric_cost_dtls a,wo_booking_dtls b where  b.pre_cost_fabric_cost_dtls_id=a.id and b.job_no=a.job_no and b.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and b.booking_type=1   and b.po_break_down_id in($job_po_id)  group by b.job_no,a.id,b.po_break_down_id,b.sensitivity,b.gmts_color_id,b.fabric_color_id";*/
			$sql_data_fab="select d.id as conv_dtls_id,b.pre_cost_fabric_cost_dtls_id as pre_cost_fabric_cost_dtls_id,b.job_no,b.po_break_down_id as po_id,b.sensitivity,b.gmts_color_id as gmts_color_id,b.fabric_color_id,sum(b.grey_fab_qnty) as grey_fab_qnty,sum(b.amount) as amount,c.po_number,sum(c.plan_cut) as plan_cut_qnty from  wo_pre_cost_fabric_cost_dtls a,wo_booking_dtls b,wo_po_break_down c,wo_pre_cost_fab_conv_cost_dtls d where  b.pre_cost_fabric_cost_dtls_id=a.id and d.fabric_description=a.id and b.job_no=a.job_no and c.id=b.po_break_down_id and  a.job_no=c.job_no_mst and  b.job_no=d.job_no and b.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0  and b.booking_type=1 and b.po_break_down_id in($job_po_id) and d.id in($fabric_description_id) group by b.job_no,b.pre_cost_fabric_cost_dtls_id,b.po_break_down_id,c.po_number,b.sensitivity,b.gmts_color_id,b.fabric_color_id,d.id"; 
		    $resultData_fab=sql_select($sql_data_fab);
		  	foreach($resultData_fab as $row)
			{
				$fab_req_qty_booking_arr[$row[csf('po_id')]][$row[csf('conv_dtls_id')]][$row[csf('gmts_color_id')]]['grey_fab_qnty']=$row[csf('grey_fab_qnty')];
				$fab_req_qty_booking_arr[$row[csf('po_id')]][$row[csf('conv_dtls_id')]][$row[csf('gmts_color_id')]]['amount']=$row[csf('amount')];
			}
		 $booking_dtls_sql="select a.id as dtls_id,a.po_break_down_id as po_id,a.pre_cost_fabric_cost_dtls_id as fab_cost_dtls,a.artwork_no,a.labdip_no,a.slength,a.mc_dia,a.mc_gauge,a.fin_dia,a.fin_gsm,a.yarn_count,a.option_shade,a.lot_no,a.brand,a.po_break_down_id as po_id,a.color_size_table_id,a.fabric_color_id,a.item_size,a.process,a.dia_width,a.fabric_color_id,
		a.sensitivity,a.job_no,booking_no,a.booking_type,a.description,a.uom,a.delivery_date,a.delivery_end_date,a.sensitivity,a.wo_qnty,a.rate,
		a.amount,null as size_number_id,a.fabric_color_id,a.gmts_color_id as color_number_id,a.lib_composition,a.lib_supplier_rate_id,b.plan_cut as plan_cut_qnty,a.delivery_end_date,a.delivery_date, a.remark, b.po_number
		from wo_booking_dtls a, wo_po_break_down b where a.job_no=b.job_no_mst and
		a.po_break_down_id=b.id  and a.booking_type=3  and
		a.booking_no='$txt_booking_no' and a.id in ($dtls_id) and a.status_active=1 and a.pre_cost_fabric_cost_dtls_id=$data[2] and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 ";
		}
		
		$dtls_dataArray=sql_select($booking_dtls_sql);
	//	print_r($fabric_description_array);

		if($rate_from_library==1) $rate_disable="disabled"; else $fab_mapping_disable="disabled";
		
		/*$condition= new condition();
		if(str_replace("'","",$job_po_id) !=''){
			$condition->po_id("in($job_po_id)");
		}

		$condition->init();
		$conversion= new conversion($condition);
		//echo $conversion->getQuery(); die;
		 $conversion_fab_knit_qty_arr=$conversion->getQtyArray_by_OrderFabricProcessAndColor();
		 $conversion_fab_knit_amt_arr=$conversion->getAmountArray_by_OrderFabricProcessAndColor();*/
		 
		?>

    <div id="content_search_panel_<?=$fabric_description_id; ?>" style="" class="accord_close">
    <input type="hidden" name="fab_req_source_<?=$fabric_description_id; ?>" id="fab_req_source_<?=$fabric_description_id; ?>" value="<?=$fab_req_source; ?>" style="width:30px;" class="text_boxes" disabled="disabled">
    <table class="rpt_table" border="1" width="1200" cellpadding="0" cellspacing="0" rules="all" id="table_<?=$fabric_description_id; ?>">
        <thead>
            <th>Job No</th>
            <th>Po Number</th>
            <th>Fabric Description</th>
            <th>Artwork No</th>
            <th>Y.Count</th>
            <th>Lot</th>
            <th>Brand</th>
            <th>Labdip No</th>
            <th>Gmts. Color</th>
            <th>Item Color</th>
            <th>Gmts.Size</th>
            <th>Item Size</th>
            <th>Fab. Mapping</th>
            <th>UOM</th>
            <th>M/C Dia</th>
            <th>Fin Dia</th>
            <th>Fin GSM</th>
            <th>S/Length</th>
            <th>Delivery Start Date</th>
            <th>Delivery End Date</th>
            <th>WO. Qty</th>
            <th>Rate</th>
            <th>Amount</th>
            <th>Plan Cut Qty</th>
            <th>Remarks</th>
        </thead>
        <tbody>
        <?
        $i=1;
        foreach($dtls_dataArray as $row)
        {
            $fab_dtls_id=$fab_dtls_id_arr[$fabric_description_id];
            $color_id=$row[csf("color_number_id")];
            $po_id=$row[csf("po_id")];
            //$po_number_arr[$po_id]=$row[csf("po_number")];
            if($sensitivity==1 || $sensitivity==3) // AS Per Garments/Contrast Color
            {
                if($fab_req_source==1) //Budget
                {
                    $pre_req_qnty=array_sum($conversion_knit_qty_arr[$fabric_description_id][$po_id][$color_id]);
                    $pre_req_amt=array_sum($conversion_knit_amt_arr[$fabric_description_id][$po_id][$color_id]);
                    $bom_amt=$pre_req_amt;
                }
                else 
                {
                    //$pre_cost_req_qnty=array_sum($conversion_fab_knit_qty_arr[$po_id][$fabric_description_id][$process][$color_id]);
                    //$pre_cost_req_amt=array_sum($conversion_knit_amt_arr[$po_id][$fabric_description_id][$process][$color_id]);
                    
                    $pre_req_qnty=$fab_req_qty_booking_arr[$po_id][$fabric_description_id][$color_id]['grey_fab_qnty'];
                    $pre_req_amt=$row[csf("amount")];//$fab_req_qty_booking_arr[$po_id][$fabric_description_id][$color_id]['amount'];
                    $bom_amt=$pre_req_amt;
                }
                $wo_prev_qnty=$po_fab_prev_color_booking_arr[$po_id][$fabric_description_id][$color_id]['wo_qnty'];
                $wo_prev_amt=$po_fab_prev_color_booking_arr[$po_id][$fabric_description_id][$color_id]['amount'];
            }
            else if($sensitivity==2 || $sensitivity==0) // AS Per Size
            {
                if($fab_req_source==1) //Budget
                {
                    $pre_req_qnty=array_sum($conversion_po_size_knit_qty_arr[$fabric_description_id][$po_id][$row[csf("size_number_id")]]);
                    $pre_req_amt=array_sum($conversion_po_size_knit_amt_arr[$fabric_description_id][$po_id][$row[csf("size_number_id")]]);
                    $bom_amt=$pre_req_amt;
                }
                else
                {
                    $pre_req_qnty=$fab_req_qty_booking_arr[$po_id][$fabric_description_id][$color_id]['grey_fab_qnty'];
                    $pre_req_amt=$fab_req_qty_booking_arr[$po_id][$fabric_description_id][$color_id]['amount'];
                    $bom_amt=$pre_req_amt;
                }
                
                $wo_prev_qnty=$po_fab_prev_color_booking_arr[$po_id][$fabric_description_id][$row[csf("size_number_id")]]['wo_qnty'];
                $wo_prev_amt=$po_fab_prev_color_booking_arr[$po_id][$fabric_description_id][$row[csf("size_number_id")]]['amount'];
            }
            else if($sensitivity==4) // AS Per Color & Size
            {
                if($fab_req_source==1) //Budget
                {
                    $pre_req_qnty=array_sum($conversion_color_size_knit_qty_arr[$fabric_description_id][$po_id][$color_id][$row[csf("size_number_id")]]);
                    $pre_req_amt=array_sum($conversion_color_size_knit_amt_arr[$fabric_description_id][$po_id][$color_id][$row[csf("size_number_id")]]);
                    $bom_amt=$pre_req_amt;
                }
                else
                {
                    $pre_req_qnty=$fab_req_qty_booking_arr[$po_id][$fab_dtls_id][$color_id]['grey_fab_qnty'];
                    $pre_req_amt=$fab_req_qty_booking_arr[$po_id][$fab_dtls_id][$color_id]['amount'];
                    $bom_amt=$pre_req_amt;
                }
                $wo_prev_qnty=$po_fab_prev_color_booking_arr[$po_id][$fabric_description_id][$color_id][$row[csf("size_number_id")]]['wo_qnty'];
                $wo_prev_amt=$po_fab_prev_color_booking_arr[$po_id][$fabric_description_id][$color_id][$row[csf("size_number_id")]]['amount'];
            }
            
            $woqnty=$row[csf("wo_qnty")];
            $amount=$row[csf("amount")];
            $rate=$row[csf("rate")];
            $bal_wo_qty=$pre_req_qnty-$woqnty;

            if($woqnty<=0) $td_color='#FF0000'; else $td_color='';
                        
            //	echo $pre_req_amt.'='.$woqnty.'='.$pre_req_qnty.'<BR/>';
        	?>
            <tr align="center">
                <td>
                    <input type="hidden" name="updateid_<?=$fabric_description_id.'_'.$i; ?>" id="updateid_<?=$fabric_description_id.'_'.$i; ?>" value="<?=$row[csf("dtls_id")]; ?>">
                    <input type="text" name="txt_job_no_<?=$fabric_description_id.'_'.$i; ?>" id="txt_job_no_<?=$fabric_description_id.'_'.$i; ?>" value="<?=$row[csf("job_no")]; ?>" style="width:90px;" class="text_boxes">
                </td>
                <td>
					<?=create_drop_down("po_no_".$fabric_description_id."_".$i, 110, $po_number_arr,"", 1,'', $po_id,"",1); ?>
                    <input type="hidden" name="po_id_<?=$fabric_description_id.'_'.$i; ?>" id="po_id_<?=$fabric_description_id.'_'.$i; ?>" value="<?=$po_id; ?>" style="width:110px;" class="text_boxes" disabled="disabled">
                </td>
                <td>
					<?=create_drop_down("fabric_description_".$fabric_description_id."_".$i, 250, $fabric_description_array,"", 1,'', $fabric_description_id,"",1); ?>
                    <input type="hidden" name="fabric_description_id_<?=$fabric_description_id.'_'.$i; ?>" id="fabric_description_id_<?=$fabric_description_id.'_'.$i; ?>" value="<?=$fabric_description_id; ?>" style="width:30px;" class="text_boxes" disabled="disabled">
                </td>
                <td><input type="text" name="artworkno_<?=$fabric_description_id.'_'.$i; ?>" id="artworkno_<?=$fabric_description_id.'_'.$i; ?>" value="<?=$row[csf("artwork_no")]; ?>" style="width:80px;" class="text_boxes"></td>
                <td><input type="text" name="yarncount_<?=$fabric_description_id.'_'.$i; ?>" id="yarncount_<?=$fabric_description_id.'_'.$i; ?>" onChange="copy_value(<?=$fabric_description_id; ?>,<?=$i; ?>,'yarncount');" value="<?=$row[csf("yarn_count")]; ?>" style="width:50px;" class="text_boxes"></td>
                <td><input type="text" name="lotno_<?=$fabric_description_id.'_'.$i; ?>" id="lotno_<?=$fabric_description_id.'_'.$i; ?>" onChange="copy_value(<?=$fabric_description_id; ?>,<?=$i; ?>,'lotno');" value="<?=$row[csf("lot_no")]; ?>" style="width:30px;" class="text_boxes"></td>
                <td><input type="text" name="brand_<?=$fabric_description_id.'_'.$i; ?>" id="brand_<?=$fabric_description_id.'_'.$i; ?>" onChange="copy_value(<?=$fabric_description_id; ?>,<?=$i; ?>,'brand');" value="<?=$row[csf("brand")]; ?>" style="width:40px;" class="text_boxes">
                </td>
                <td><input type="text" name="labdipno_<? echo $fabric_description_id.'_'.$i; ?>" id="labdipno_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $row[csf("labdip_no")]; ?>" style="width:70px;" class="text_boxes"></td>
                
                <td title="<? if($sensitivity==1 || $sensitivity==3  || $sensitivity==4 ){ echo $color_library[$color_id];} else { echo "";}?>">
                    <input type="hidden" name="color_size_table_id_<? echo $fabric_description_id.'_'.$i; ?>" id="color_size_table_id_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<?  echo $row[csf("color_size_table_id")];?>" disabled="disabled"/>
                    
                    <input type="text" name="gmts_color_<? echo $fabric_description_id.'_'.$i; ?>" id="gmts_color_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<? if($sensitivity==1 || $sensitivity==3  || $sensitivity==4 ){ echo $color_library[$color_id];} else { echo "";}?>" disabled="disabled"/>
                    <input type="hidden" name="gmts_color_id_<? echo $fabric_description_id.'_'.$i; ?>" id="gmts_color_id_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<? echo $color_id;?>" disabled="disabled"/>
                </td>
                <td>
                    <input type="text" name="item_color_<? echo $fabric_description_id.'_'.$i; ?>" id="item_color_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes" onChange="copy_value()" value="<? echo $color_library[$row[csf("fabric_color_id")]]; //if($sensitivity==1 || $sensitivity==3  || $sensitivity==4 ){ echo $color_library[$row[csf("color_number_id")]];} else { echo "";}?>"/>
                    <input type="hidden" name="item_color_id_<? echo $fabric_description_id.'_'.$i; ?>" id="item_color_id_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<? echo $row[csf("fabric_color_id")];//if($sensitivity==1 || $sensitivity==3  || $sensitivity==4 ){ echo $row[csf("color_number_id")];} else { echo "";}?>" disabled="disabled"/>
                </td>
                <td>
                    <input type="text" name="gmts_size_<? echo $fabric_description_id.'_'.$i; ?>" id="gmts_size_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<? if($sensitivity==2 || $sensitivity==4 ){echo $size_library[$row[csf("size_number_id")]];} else{ echo "";}?>" disabled="disabled" />
                    <input type="hidden" name="gmts_size_id_<? echo $fabric_description_id.'_'.$i; ?>" id="gmts_size_id_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<? if($sensitivity==2 || $sensitivity==4 ){echo $row[csf("size_number_id")];} else{ echo "";}?>" disabled="disabled"/>
                </td>
                <td>
                    <input type="text" name="item_size_<? echo $fabric_description_id.'_'.$i; ?>" id="item_size_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes" onChange="copy_value()" value="<?  echo $row[csf("item_size")];?>">
                    <input type="hidden" name="item_size_id_<? echo $fabric_description_id.'_'.$i; ?>" id="item_size_id_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<? if($sensitivity==2 || $sensitivity==4 ){ echo $row[csf("size_number_id")];} else{ echo "";}?>" disabled="disabled"/>
                </td>
                <td>
                    <input type="text" name="subcon_supplier_compo_<? echo $fabric_description_id.'_'.$i; ?>" id="subcon_supplier_compo_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="" onDblClick="service_supplier_popup('<? echo $fabric_description_id.'_'.$i; ?>')" placeholder="Browse" <?php echo $fab_mapping_disable; ?>>
                    
                    <input type="hidden" name="subcon_supplier_rateid_<? echo $fabric_description_id.'_'.$i; ?>" id="subcon_supplier_rateid_<? echo $fabric_description_id.'_'.$i; ?>" value="">
                </td>
                <td><?=create_drop_down("uom_".$fabric_description_id."_".$i, 50, $unit_of_measurement,"", 1, "--Select--",$row[csf("uom")],"copy_value(".$fabric_description_id.",".$i.",'uom')","","$uom_item"); ?></td>
                <td><input type="text" name="mcdia_<? echo $fabric_description_id.'_'.$i; ?>" id="mcdia_<? echo $fabric_description_id.'_'.$i; ?>" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'mcdia')" value="<? echo $row[csf("mc_dia")]; ?>" style="width:60px;" class="text_boxes"></td>
                <td><input type="text" name="findia_<? echo $fabric_description_id.'_'.$i; ?>" id="findia_<? echo $fabric_description_id.'_'.$i; ?>"  onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'findia')"; value="<? echo $row[csf("fin_dia")]; ?>" style="width:60px;" class="text_boxes"></td>
                <td><input type="text" name="fingsm_<? echo $fabric_description_id.'_'.$i; ?>" id="fingsm_<? echo $fabric_description_id.'_'.$i; ?>" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'fingsm')"; value="<? echo $row[csf("fin_gsm")]; ?>" style="width:60px;" class="text_boxes"></td>
                <td><input type="text" name="slength_<? echo $fabric_description_id.'_'.$i; ?>" id="slength_<? echo $fabric_description_id.'_'.$i; ?>" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'slength')"; value="<? echo $row[csf("slength")]; ?>" style="width:60px;" class="text_boxes"></td>
                <td><input type="text" name="startdate_<? echo $fabric_description_id.'_'.$i; ?>" id="startdate_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo change_date_format($row[csf("delivery_date")]); ?>" style="width:60px;" class="datepicker"></td>
                <td><input type="text" name="enddate_<? echo $fabric_description_id.'_'.$i; ?>" id="enddate_<? echo $fabric_description_id.'_'.$i; ?>" value="<?  echo change_date_format($row[csf("delivery_end_date")]); ?>" style="width:60px;" class="datepicker"></td>
                <? //check_wo_qty_row( echo $fabric_description_id;,echo $i;
                //	echo $woqnty.'='.$priv_wo_qty.'<br/>';
                $exchange_rate =$exchange_rate ;
                $wo_qty_bal=($woqnty*1)-($priv_wo_qty*1);
                //echo $wo_qty_bal.'='.$priv_wo_qty.'<br/>';
                $hidden_wo_amt=(($woqnty-$priv_wo_qty)*$rate);
                ?>
                <td title="<? echo 'Wo Qty='.$woqnty.','.'Prev Wo Qty='.$priv_wo_qty.',Prev Amt='.$wo_prev_amt.',pre wo Amt='.$hidden_wo_amt; ; ?>">
                    <input type="hidden" name="txt_hidden_woamt_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_hidden_woamt_<? echo $fabric_description_id.'_'.$i; ?>" style="width:30px;" class="text_boxes_numeric" value="<? if($hidden_wo_amt<1) echo $hidden_wo_amt.'_'.$exchange_rate.'_'.$rate.'_'.$wo_qty_bal.'_'.($woqnty-$priv_wo_qty)*$row[csf("rate")];else echo number_format($hidden_wo_amt,4,'.','').'_'.$exchange_rate.'_'.$rate.'_'.$wo_qty_bal.'_'.($woqnty-$priv_wo_qty)*$row[csf("rate")]; ?>"/>
                    
                    <input type="text" name="txt_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'txt_woqnty'); calculate_amount(<? echo $fabric_description_id; ?>,<? echo $i; ?>);" value="<?  if($woqnty<1) $woqnty;else echo number_format($woqnty,4,'.',''); ?>"/>
                    <input type="hidden" name="txt_hidden_woqnty_bal_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_hidden_woqnty_bal_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric"  value="<?  if(($woqnty-$priv_wo_qty)<1) echo $woqnty-$priv_wo_qty;else echo number_format($woqnty-$priv_wo_qty,4,'.',''); ?>"/>
                    <input type="hidden" name="txt_prev_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_prev_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric" value="<? if($priv_wo_qty<1) echo $priv_wo_qty;else echo number_format($priv_wo_qty,4,'.','');?>" />
                    <input type="hidden" name="txt_reqwoqnty_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_reqwoqnty_<? echo $fabric_description_id.'_'.$i; ?>" style="width:30px;" class="text_boxes_numeric" value="<? if($wo_qty_bal<1) echo $pre_req_qnty;else echo number_format($pre_req_qnty,4,'.',''); ?>"/>
                    <input type="hidden" name="txt_reqwoamt_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_reqwoamt_<? echo $fabric_description_id.'_'.$i; ?>" style="width:30px;" class="text_boxes_numeric" value="<? if($wo_qty_bal<1) echo $pre_req_amt; else echo number_format($bom_amt,4,'.',''); ?>"/>
                    <input type="hidden" name="txt_prev_woamt_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_prev_woamt_<? echo $fabric_description_id.'_'.$i; ?>" style="width:30px;" class="text_boxes_numeric" value="<? if($wo_prev_amt<1) echo $wo_prev_amt;else echo number_format($wo_prev_amt,4,'.',''); ?>"/>
                </td>
                <td>
                    <input type="text" name="txt_rate_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_rate_<? echo $fabric_description_id.'_'.$i; ?>" style="width:50px;" class="text_boxes_numeric" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'txt_rate'); calculate_amount(<? echo $fabric_description_id; ?>,<? echo $i; ?>);" value="<? echo $rate; ?>" <?php echo $rate_disable; ?>>
                    <input type="hidden" name="txt_hidden_rate_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_hidden_rate_<? echo $fabric_description_id.'_'.$i; ?>" style="width:50px;" class="text_boxes_numeric"  value="<? echo $row[csf("rate")]; ?>" />
                </td>
                <td><input type="text" name="txt_amount_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_amount_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric"  value="<? echo number_format($amount,4,'.',''); ?>" disabled="disabled"/></td>
                <td><input type="text" name="txt_paln_cut_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_paln_cut_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric"  value="<? echo  $row[csf("plan_cut_qnty")]; ?>" disabled> </td>
                <td><input type="text" name="txtremaks_<?=$fabric_description_id.'_'.$i; ?>" id="txtremaks_<?=$fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes" value="<?=$row[csf("remark")]; ?>" ></td>
            </tr>
        	<?
            $i++;
        }
        ?>
        </tbody>
    </table>
    </div>
    <?
    exit();
}

if ($action=="service_booking_popup")
{
  	echo load_html_head_contents("Booking Search","../../../", 1, 1, $unicode);
	?>
	<script>
	function set_checkvalue()
	{
		if(document.getElementById('chk_job_wo_po').value==0) document.getElementById('chk_job_wo_po').value=1;
		else document.getElementById('chk_job_wo_po').value=0;
	}
	function js_set_value(booking_no)
	{
		document.getElementById('selected_booking').value=booking_no;
		parent.emailwindow.hide();
	}
    </script>
</head>

<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
    <table width="1050" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
        <thead>
        	<th colspan="10"><?=create_drop_down( "cbo_search_category", 130, $string_search_type,'', 1, "-- Search Catagory --" ); ?></th>
        </thead>
        <thead>
            <th width="150" class="must_entry_caption">Company Name</th>
            <th width="170">Buyer Name</th>
            <th width="70">Booking No</th>
            <th width="70">Job No</th>
            <th width="70">Ref. No</th>
            <th width="70">File No</th>
            <th width="100">Style Ref</th>
            <th width="130" colspan="2">Date Range</th>
            <th><input type="checkbox" value="0" onClick="set_checkvalue()" id="chk_job_wo_po">WO without Dtls</th>
        </thead>
        <tr class="general">
            <td> <input type="hidden" id="selected_booking">
            <?=create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "-- Select Company --", '', "load_drop_down( 'service_booking_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );"); ?></td>
            <td id="buyer_td"><?=create_drop_down( "cbo_buyer_name", 170, $blank_array,"", 1, "-- Select Buyer --" ); ?></td>
            <td><input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:60px"></td>
            <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:60px"></td>
            <td><input name="txt_ref_no" id="txt_ref_no" class="text_boxes" style="width:60px"></td>
            <td><input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:60px"></td>
            <td><input name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:90px"></td>
            <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px"></td>
            <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px"></td>
            <td align="center">
            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('txt_style_ref').value+'_'+document.getElementById('chk_job_wo_po').value+'_'+document.getElementById('txt_ref_no').value+'_'+document.getElementById('txt_file_no').value, 'create_booking_search_list_view', 'search_div', 'service_booking_controller','setFilterGrid(\'list_view\',-1)');" style="width:80px;" /></td>
        </tr>
        <tr>
        	<td colspan="10" align="center" valign="middle"><?=load_month_buttons(1); ?></td>
        </tr>
    </table>
          <div id="search_div"></div>
    </form>
   	</div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if ($action=="create_booking_search_list_view")
{
	$data=explode('_',$data);
	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer=" and a.buyer_id='$data[1]'"; else  $buyer="";//{ echo "Please Select Buyer First."; die; }
	$style_ref=$data[8];
	$check_type=$data[9];
	$ref_no=$data[10];
	$file_no=$data[11];
	//echo $check_type.'DD';
	if($ref_no!="") $ref_no_cond="and c.grouping='$ref_no'";else $ref_no_cond="";
	if($file_no!="") $file_no_cond="and c.file_no=$file_no";else $file_no_cond="";
	if($db_type==0)
	{
		$booking_year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$data[4]";
		$year_cond=" and SUBSTRING_INDEX(b.insert_date, '-', 1)=$data[4]";
		if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
	}
	else if($db_type==2)
	{
		$booking_year_cond=" and to_char(a.insert_date,'YYYY')=$data[4]";
		$year_cond=" and to_char(b.insert_date,'YYYY')=$data[4]";
		if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
	}

	if($data[5]==4 || $data[5]==0)
	{
		if (str_replace("'","",$data[7])!="") $job_cond=" and b.job_no like '%$data[7]%' $year_cond "; else  $job_cond="";
		if (str_replace("'","",$data[8])!="") $style_cond=" and d.style_ref_no like '%$data[8]%' $year_cond "; else  $style_cond="";
		if (str_replace("'","",$data[6])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[6]%'  $booking_year_cond  "; else $booking_cond="";

	}
    else if($data[5]==1)
	{
		if (str_replace("'","",$data[7])!="") $job_cond=" and b.job_no ='$data[7]' "; else  $job_cond="";
		if (str_replace("'","",$data[6])!="") $booking_cond=" and a.booking_no_prefix_num ='$data[6]'   "; else $booking_cond="";
		if (str_replace("'","",$data[8])!="") $style_cond=" and d.style_ref_no like '$data[8]' $year_cond "; else  $style_cond="";
	}
    else if($data[5]==2)
	{
		if (str_replace("'","",$data[7])!="") $job_cond=" and b.job_no like '$data[7]%'  $year_cond"; else  $job_cond="";
		if (str_replace("'","",$data[6])!="") $booking_cond=" and a.booking_no_prefix_num like '$data[6]%'  $booking_year_cond  "; else $booking_cond="";
		if (str_replace("'","",$data[8])!="") $style_cond=" and d.style_ref_no like '$data[8]%' $year_cond "; else  $style_cond="";
	}
	else if($data[5]==3)
	{
		if (str_replace("'","",$data[7])!="") $job_cond=" and b.job_no like '%$data[7]'  $year_cond"; else  $job_cond="";
		if (str_replace("'","",$data[6])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[6]'  $booking_year_cond  "; else $booking_cond="";
		if (str_replace("'","",$data[8])!="") $style_cond=" and d.style_ref_no like '%$data[8]' $year_cond "; else  $style_cond="";
	}

	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$suplier=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	//$po_job_no_arr=return_library_array( "select id, job_no_prefix_num from wo_po_details_master",'id','job_no_prefix_num');
	if($db_type==0)  $group_concat="group_concat(b.po_break_down_id) as po_break_down_id";
	if($db_type==2) $group_concat="listagg(cast(b.po_break_down_id as varchar2(4000)),',') within group (order by b.po_break_down_id) as po_break_down_id";
	
	$po_number_with_booking=array();
	$sql_po_number=sql_select("select a.booking_no, b.po_break_down_id, d.id, d.job_no_prefix_num, d.job_no, c.grouping, c.po_number,a.pay_mode,a.supplier_id  from  wo_po_details_master d, wo_po_break_down c, wo_booking_mst a, wo_booking_dtls b where d.job_no=c.job_no_mst and d.job_no=b.job_no and b.po_break_down_id=c.id and a.booking_no=b.booking_no and  a.booking_type=3 and  a.entry_form=573 and  a.status_active=1 and a.is_deleted=0 $company $buyer $booking_date $booking_cond $job_cond $style_cond $file_no_cond $ref_no_cond ");
	//echo "select a.booking_no, b.po_break_down_id, d.id, d.job_no_prefix_num, d.job_no, c.grouping, c.po_number,a.pay_mode,a.supplier_id  from  wo_po_details_master d, wo_po_break_down c, wo_booking_mst a, wo_booking_dtls b where d.job_no=c.job_no_mst and d.job_no=b.job_no and b.po_break_down_id=c.id and a.booking_no=b.booking_no and  a.booking_type=3 and  a.entry_form=573 and  a.status_active=1 and a.is_deleted=0 $company $buyer $booking_date $booking_cond $job_cond $style_cond $file_no_cond $ref_no_cond ";die;
	
	//echo "select a.booking_no, b.po_break_down_id, d.id, d.job_no_prefix_num, d.job_no, c.grouping, c.po_number  from  wo_po_details_master d, wo_po_break_down c, wo_booking_mst a, wo_booking_dtls b where d.job_no=c.job_no_mst and d.job_no=b.job_no and b.po_break_down_id=c.id and a.booking_no=b.booking_no and  a.booking_type=3 and  a.status_active=1 and a.is_deleted=0 $company $buyer $booking_date $booking_cond $job_cond $style_cond $file_no_cond $ref_no_cond ";die;
	$poId="";
	foreach($sql_po_number as $row )
	{
		$poId.=$row[csf('po_break_down_id')].',';
		$po_number_string="";
		$po_id_arr=explode(",",$row[csf('po_number')]);
		foreach($po_id_arr as $key=> $value)
		{
			$po_number_string.=	$value.",";
		}
		$po_number_with_booking[$row[csf('booking_no')]]=rtrim($po_number_string,",");
		$po_job_no_arr[$row[csf('job_no')]]=$row[csf('job_no_prefix_num')];
		$refNo_arr[$row[csf('job_no')]]=$row[csf('grouping')];
		if($row[csf('pay_mode')]==3 || $row[csf('pay_mode')]==5)
		{
			$booking_arr_arr[$row[csf('booking_no')]]=$comp[$row[csf('supplier_id')]];
			
		}
		else $booking_arr_arr[$row[csf('booking_no')]]=$suplier[$row[csf('supplier_id')]];
	}
	unset($sql_po_number);
	
	/*$poIds=chop($poId,','); $poidsCond="";
	$poIds_count=count(array_unique(explode(",",$poIds)));
	if($db_type==2 && $poIds_count>1000)
	{
		$poidsCond=" and (";
		$poidsArr=array_chunk(explode(",",$poIds),999);
		foreach($poidsArr as $ids)
		{
			$ids=implode(",",$ids);
			$poidsCond.=" b.po_break_down_id in($ids) or"; 
		}
		$poidsCond=chop($poidsCond,'or ');
		$poidsCond.=")";
	}
	else $poidsCond=" and b.po_break_down_id in($poIds)";*/
	$jobIds=chop($poId,',');
	//echo $jobIds;die;
	$job_cond_for_in="";
	$job_ids=count(array_unique(explode(",",$jobIds)));
		if($db_type==2 && $job_ids>1000)
		{
			$job_cond_for_in=" and (";
			$jobIdsArr=array_chunk(explode(",",$jobIds),999);
			foreach($jobIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$job_cond_for_in.=" b.po_break_down_id in($ids) or"; 
			}
			$job_cond_for_in=chop($job_cond_for_in,'or ');
			$job_cond_for_in.=")";
		}
		else
		{
			$jobIds=implode(",",array_unique(explode(",",$jobIds)));
			$job_cond_for_in=" and b.po_break_down_id in($jobIds)";
		}
		
	if($check_type==0)
	{
		 $sql= "select a.id,a.booking_no_prefix_num, b.job_no as job_no_prefix_num, a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.item_category, a.fabric_source, a.supplier_id, a.pay_mode,a.source from wo_booking_mst a, wo_booking_dtls b where  a.booking_no=b.booking_no and  a.booking_type=3 and  a.status_active=1 and a.is_deleted=0 $company $buyer $booking_date $booking_cond $job_cond $job_cond_for_in
		group by  a.id, a.booking_no_prefix_num, b.job_no, a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.item_category, a.fabric_source,a.source, a.supplier_id, a.pay_mode order by a.id DESC";
	}
	else
	{
		$sql= "select a.id, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.job_no as job_no_prefix_num, a.item_category,a.source, a.fabric_source, a.supplier_id, a.pay_mode from wo_booking_mst a where a.booking_type=3 and a.status_active=1 and a.is_deleted=0 $company $buyer $booking_date $booking_cond $job_cond $style_cond  order by a.id DESC";
	}
//	echo $sql;
	$sql_query=sql_select($sql);
	foreach($sql_query as $row )
	{
		if($row[csf("pay_mode")]==3 || $row[csf("pay_mode")]==5)
		{
			
			$supplier_or_company[$row[csf("id")]]=$comp[$row[csf("supplier_id")]];
		}
		else
		{
			$supplier_or_company[$row[csf("id")]]=$suplier[$row[csf("supplier_id")]];
		}
	}
//source
	$arr=array (2=>$comp,3=>$buyer_arr,5=>$refNo_arr,6=>$po_number_with_booking,7=>$item_category,8=>$source,9=>$supplier_or_company);

	//echo $sql;
	echo  create_list_view("list_view", "Booking No,Booking Date,Company,Buyer,Job No., Ref No.,PO No,Fabric Nature,Source,Supplier", "50,60,120,100,80,70,170,80,70","1000","300",0, $sql , "js_set_value", "booking_no", "", 1, "0,0,company_id,buyer_id,0,job_no_prefix_num,booking_no,item_category,source,id", $arr , "booking_no_prefix_num,booking_date,company_id,buyer_id,job_no_prefix_num,job_no_prefix_num,booking_no,item_category,source,id", '','','0,3,0,0,0,0,0,0,0','','');
	exit();
}

if ($action=="previous_booking_list_view")
{
	$data=explode("_",$data);
	$po_ids=$data[0];
	$process=$data[1];
	$po_ids=implode(",",array_unique(explode(",",$po_ids)));

		if($db_type==2)
			{
				$group_con="listagg(cast(a.booking_no as varchar2(4000)),',') within group (order by a.booking_no) AS booking_no";
			}
			else
			{
				$group_con="group_concat(a.booking_no) AS booking_no";
			}

	 $booking_arr="select a.booking_no from wo_booking_mst a ,wo_booking_dtls b, wo_po_break_down c  where  a.booking_no=b.booking_no and b.po_break_down_id=c.id  and c.id in($po_ids) and b.process=$process and a.item_category=12 and b.job_no=c.job_no_mst  and a.booking_type=3  and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 group by a.booking_no";
	  $booking_previous=sql_select($booking_arr);

		?>

      <table width="150px" class="rpt_table" style="border:1px solid black;" align="center" border="0" cellpadding="0" cellspacing="0">
        <thead>

         <tr align="left" style="border:1px solid black;font-weight:bold;">
          <th>Booking No</th>
         </tr>
         </thead>
          <?
		  $i=1;
        foreach ($booking_previous as $row)
		{
			//$booking_nos=implode(",",array_unique(explode(",",$row[csf('booking_no')])));
			$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
		?>
     <tr style="border:1px solid black;" bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
         <td width="120" align="center"><p><? echo  $row[csf('booking_no')];?></p> </td>

        </tr>

	<?
	$i++;
	}
	?>
    </table>
    <?

}
if($action=="terms_condition_popup")
{
	echo load_html_head_contents("Order Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
?>
	<script>
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

			data_all=data_all+get_submitted_data_string('txt_booking_no*termscondition_'+i,"");
		}
		var data="action=save_update_delete_fabric_booking_terms_condition&operation="+operation+'&total_row='+row_num+data_all;
		//freeze_window(operation);
		http.open("POST","trims_booking_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_fabric_booking_terms_condition_reponse;
}

function fnc_fabric_booking_terms_condition_reponse()
{

	if(http.readyState == 4)
	{
	    var reponse=trim(http.responseText).split('**');
			if (reponse[0].length>2) reponse[0]=10;
			if(reponse[0]==0 || reponse[0]==1)
			{
				parent.emailwindow.hide();
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
           <input type="text" id="txt_booking_no" name="txt_booking_no" value="<? echo str_replace("'","",$txt_booking_no) ?>"/>


            <table width="650" cellspacing="0" class="rpt_table" border="0" id="tbl_termcondi_details" rules="all">
                	<thead>
                    	<tr>
                        	<th width="50">Sl</th><th width="530">Terms</th><th ></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
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
                                    <input type="text" id="termscondition_<? echo $i;?>"   name="termscondition_<? echo $i;?>" style="width:95%"  class="text_boxes"  value="<? echo $row[csf('terms')]; ?>"  />
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
					$data_array=sql_select("select id, terms from  lib_terms_condition where is_default=1 and page_id=176");// quotation_id='$data'
					foreach( $data_array as $row )
						{
							$i++;
					?>
                    <tr id="settr_1" align="center">
                                    <td>
                                    <? echo $i;?>
                                    </td>
                                    <td>
                                    <input type="text" id="termscondition_<? echo $i;?>"   name="termscondition_<? echo $i;?>" style="width:95%"  class="text_boxes"  value="<? echo $row[csf('terms')]; ?>"  />
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
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="show_trim_booking_report")
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$company_address_arr=return_library_array( "select id,city from   lib_company",'id','city');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	//$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	ob_start();
	?>
	<div style="width:1333px" align="center">
        <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black">
            <tr>
                <td width="100">
                	<img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
                </td>
                <td width="1000">
                    <table width="100%" cellpadding="0" cellspacing="0" >
                        <tr>
                            <td align="center" style="font-size:18px;"><?=$company_library[$cbo_company_name]; ?></td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:14px">
								<?
                                $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
                                foreach ($nameArray as $result)
                                {
                                    ?>
                                    Plot No: <? echo $result[csf('plot_no')]; ?>
                                    Level No: <? echo $result[csf('level_no')]?>
                                    Road No: <? echo $result[csf('road_no')]; ?>
                                    Block No: <? echo $result[csf('block_no')];?>
                                    City No: <? echo $result[csf('city')];?>
                                    Zip Code: <? echo $result[csf('zip_code')]; ?>
                                    Province No: <?php echo $result[csf('province')]; ?>
                                    Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br>
                                    Email Address: <? echo $result[csf('email')];?>
                                    Website No: <? echo $result[csf('website')];
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:20px">
                                <strong>Service Booking Sheet</strong>
                            </td>
                        </tr>
                    </table>
                </td>
                <td width="250" id="barcode_img_id"></td>
            </tr>
        </table>
		<?
		$booking_grand_total=0; $job_no=""; $currency_id=""; $po_no=""; $$style_ref="";
		$sqljobBooking="select c.job_no, c.style_ref_no, c.buyer_name as buyer_id, d.po_number from wo_booking_mst a, wo_booking_dtls b, wo_po_details_master c, wo_po_break_down d where a.booking_no=b.booking_no and b.po_break_down_id=d.id and c.job_no=d.job_no_mst and b.job_no=c.job_no and a.booking_no=$txt_booking_no";
		$nameArray_job=sql_select($sqljobBooking);
		$buyer_name=$nameArray_job[0][csf('buyer_id')];
		foreach ($nameArray_job as $result_job)
        {
			$job_no.=$result_job[csf('job_no')].",";
			$po_no.=$result_job[csf('po_number')].",";
			$style_ref.=$result_job[csf('style_ref_no')].",";
		}
		$job_no=implode(",",array_filter(array_unique(explode(',',$job_no))));
		$po_no=implode(",",array_filter(array_unique(explode(',',$po_no))));
		$style_ref=implode(",",array_filter(array_unique(explode(',',$style_ref))));
		
        $nameArray=sql_select( "select a.booking_no, a.booking_date, a.supplier_id, a.currency_id, a.exchange_rate, a.attention, a.delivery_date, a.source, a.pay_mode  from wo_booking_mst a where a.booking_no=$txt_booking_no and a.supplier_id=$hidden_supplier_id and a.id=$booking_mst_id");
        foreach ($nameArray as $result)
        {
			$varcode_booking_no=$result[csf('booking_no')];
			?>
			<table width="100%" style="border:1px solid black">
                <tr>
                	<td colspan="6" valign="top"></td>
                </tr>
                <tr>
                    <td width="100" style="font-size:12px"><b>Booking No </b>   </td>
                    <td width="110">:&nbsp;<? echo $result[csf('booking_no')];?> </td>
                    <td width="100" style="font-size:12px"><b>Booking Date</b></td>
                    <td width="110">:&nbsp;<? echo change_date_format($result[csf('booking_date')],'dd-mm-yyyy','-');?>&nbsp;&nbsp;&nbsp;</td>
                    <td width="100"><span style="font-size:12px"><b>Delivery Date</b></span></td>
                    <td width="110">:&nbsp;<? echo change_date_format($result[csf('delivery_date')],'dd-mm-yyyy','-');?></td>
                </tr>
                <tr>
                    <td style="font-size:12px"><b>Currency</b></td>
                    <td>:&nbsp;<? $currency_id=$result[csf('currency_id')];echo $currency[$result[csf('currency_id')]]; ?></td>
                    <td style="font-size:12px"><b>Conversion Rate</b></td>
                    <td>:&nbsp;<? echo $result[csf('exchange_rate')]; ?></td>
                    <td style="font-size:12px"><b>Source</b></td>
                    <td>:&nbsp;<? echo $source[$result[csf('source')]]; ?></td>
                </tr>
                <tr>
                    <td style="font-size:12px"><b>Supplier Name</b>   </td>
                    <td>:&nbsp;<?
                    if($result[csf("pay_mode")]!=3 && $result[csf("pay_mode")]!=5)
                    {
                    echo $supplier_name_arr[$result[csf('supplier_id')]];
                    }
                    else
                    {
                    echo $company_library[$result[csf('supplier_id')]];
                    }
                    ?>    </td>
                    <td style="font-size:12px"><b>Supplier Address</b></td>
                    <td>:&nbsp;<?
                    if($result[csf("pay_mode")]!=3 && $result[csf("pay_mode")]!=5)
                    {
                    echo $supplier_address_arr[$result[csf('supplier_id')]];
                    }
                    else
                    {
                    echo $company_address_arr[$result[csf('supplier_id')]];
                    }
                    ?></td>
                    <td style="font-size:12px"><b>Attention</b></td>
                    <td>:&nbsp;<? echo $result[csf('attention')]; ?></td>
                </tr>
                <tr>
                    <td style="font-size:12px"><b>Job No</b>   </td>
                    <td>:&nbsp;<?=$job_no; ?></td>
                    <td style="font-size:12px"><b>PO No</b> </td>
                    <td style="font-size:12px" >:&nbsp;<?=$po_no; ?> </td>
                    <td style="font-size:12px"><b>Buyer Name</b>   </td>
                    <td>:&nbsp;<?=$buyer_name_arr[$buyer_name]; ?></td>
                </tr>
                <tr>
                    <td style="font-size:12px"><b>Style Ref:</b></td>
                    <td>&nbsp;<?=$style_ref; ?></td>
                    <td style="font-size:12px">&nbsp;</td>
                    <td style="font-size:12px">&nbsp;</td>
                    <td style="font-size:12px"><b>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
			</table>
			<?
        }
        ?>
          <!--==============================================AS PER GMTS COLOR START=========================================  -->
		<?
		//========================================
		$fabric_description_array=array();
	    $wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select id,fabric_description,cons_process from wo_pre_cost_fab_conv_cost_dtls  where job_no='".rtrim($job_no,", ")."'");
        foreach( $wo_pre_cost_fab_conv_cost_dtls_id as $row_wo_pre_cost_fab_conv_cost_dtls_id)
        {
            if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]!=0)
            {
                $fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description,gsm_weight from  wo_pre_cost_fabric_cost_dtls  where  id='".$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]."'");//gsm_weight
                list($fabric_description_row)=$fabric_description;
                $fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]=$body_part[$fabric_description_row[csf("body_part_id")]].', '.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")].', '.$fabric_description_row[csf("gsm_weight")];//.', '.$fabric_description_row[csf("gsm_weight")]
            }
            if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]==0)
            {
                //echo "select id,body_part_id,color_type_id,fabric_description from  wo_pre_cost_fabric_cost_dtls  where  job_no='$data'";
                $fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description,gsm_weight from  wo_pre_cost_fabric_cost_dtls  where  job_no='".rtrim($job_no,", ")."'");//gsm_weight
                //list($fabric_description_row)=$fabric_description;
                foreach( $fabric_description as $fabric_description_row)
                {
                $fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]].=$body_part[$fabric_description_row[csf("body_part_id")]].', '.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")].", ".$fabric_description_row[csf("gsm_weight")];
                }
            }
        }
        //print_r($fabric_description_array);
        //=================================================
        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=1 and wo_qnty !=0 and status_active=1 ");
		//echo "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=1";
        $nameArray_color=sql_select( "select distinct fabric_color_id from wo_booking_dtls   where  booking_no=$txt_booking_no and sensitivity=1 and wo_qnty !=0 and status_active=1 " );
		if(count($nameArray_color)>0)
		{
        ?>
        <table border="0" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" >
            <tr>
                <td colspan="<? echo count($nameArray_color)+8; ?>" align="">
                <strong>As Per Garments Color</strong>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Service Type</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong>Item Color</strong> </td>
                <?
                foreach($nameArray_color  as $result_color)
                {	     ?>
                <td align="center" style="border:1px solid black"><strong><? echo $color_library[$result_color[csf('fabric_color_id')]];?></strong></td>
                <?	}    ?>
                <td style="border:1px solid black" align="center"><strong>Total</strong></td>
                <td style="border:1px solid black" align="center"><strong>Fin Dia</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
				$i++;
            $nameArray_item_description=sql_select("select distinct description,rate,uom,dia_width from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=1 and process=".$result_item[csf('process')]." and wo_qnty !=0 and status_active=1");
            //echo "select distinct description,rate,uom,dia_width from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=1 and process=".$result_item[csf('process')]." and wo_qnty !=0 and status_active=1"; die;
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $i; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $conversion_cost_head_array[$result_item[csf('process')]]; ?>
                </td>
                <?
                $color_tatal=array();
                $total_amount_as_per_gmts_color=0;
                $flag=0;
                foreach($nameArray_item_description as $result_itemdescription)
                {
                    if($flag==1){echo "<tr>";} $flag=1;
                   
                
                    $item_desctiption_total=0;
                ?>
                <td style="border:1px solid black"><? echo rtrim($fabric_description_array[$result_itemdescription[csf('description')]],", "); ?> </td>
                <td style="border:1px solid black">Booking Qty.</td>
                <?
                foreach($nameArray_color  as $result_color)
                {
                    if($result_itemdescription[csf('dia_width')] !=''){
                        $dia_con="and dia_width='".$result_itemdescription[csf('dia_width')]."'";
                    }
                    else{
                        $dia_con="and dia_width is null";
                    }
                $nameArray_color_size_qnty=sql_select("select sum(wo_qnty) as cons from wo_booking_dtls   where   booking_no=$txt_booking_no and sensitivity=1 and status_active=1  and process=". $result_item[csf('process')]." and description='". $result_itemdescription[csf('description')]."' and rate='". $result_itemdescription[csf('rate')]."' and uom='". $result_itemdescription[csf('uom')]."' $dia_con and fabric_color_id=".$result_color[csf('fabric_color_id')]."");
				 
                foreach($nameArray_color_size_qnty as $result_color_size_qnty)
                {
                ?>
                <td style="border:1px solid black; text-align:right">
                <?
                if($result_color_size_qnty[csf('cons')]!= "")
                {
                echo number_format($result_color_size_qnty[csf('cons')],2);
                $item_desctiption_total+=$result_color_size_qnty[csf('cons')] ;
                if (array_key_exists($result_color[csf('fabric_color_id')], $color_tatal))
                {
                $color_tatal[$result_color[csf('fabric_color_id')]]+=$result_color_size_qnty[csf('cons')];
                }
                else
                {
                $color_tatal[$result_color[csf('fabric_color_id')]]=$result_color_size_qnty[csf('cons')];
                }
                }
                else echo "";
                ?>
                </td>
                <?
                }
                }
                ?>

                <td style="border:1px solid black; text-align:right"><? echo number_format($item_desctiption_total,2); ?></td>
                 <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('dia_width')]; ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$result_itemdescription[csf('uom')]]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <?
                $amount_as_per_gmts_color = $item_desctiption_total*$result_itemdescription[csf('rate')];
                echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="2"><strong> Item Total</strong></td>
                <?
                foreach($nameArray_color  as $result_color)
                {

                ?>
                <td style="border:1px solid black;  text-align:right">
                <?
                if($color_tatal[$result_color[fabric_color_id]] !='')
                {
                echo number_format($color_tatal[$result_color[fabric_color_id]],2);
                }
                ?>
                </td>
            <?
            }
            ?>
                <td style="border:1px solid black;  text-align:right"><? echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black; text-align:right"><? //echo number_format(round($color_total),0); ?></td>
                <td style="border:1px solid black; text-align:right">
                <?
                echo number_format($total_amount_as_per_gmts_color,4);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="<? echo count($nameArray_color)+8; ?>"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
            </tr>
        </table>
        <br/>
        <?
		}
		?>
        <!--==============================================AS PER GMTS COLOR END=========================================  -->

        <!--==============================================AS PER GMTS SIZE START=========================================  -->
		<?
		 //$nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=1");
		//echo "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=1";
       // $nameArray_color=sql_select( "select distinct fabric_color_id from wo_booking_dtls   where  booking_no=$txt_booking_no and sensitivity=1");


        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=2 and wo_qnty !=0 and status_active=1 ");
        $nameArray_size=sql_select( "select distinct  item_size  as gmts_sizes from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=2 and wo_qnty !=0 and status_active=1 ");
		if(count($nameArray_size)>0)
		{
        ?>

        <table border="0" align="left" cellpadding="0" width="100%" cellspacing="0" >
            <tr>
                <td colspan="<? echo count($nameArray_size)+8; ?>" align="">
                <strong>As Per Garments Size </strong>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Service Type</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong>Item size</strong> </td>
                <?
                foreach($nameArray_size  as $result_size)
                {	     ?>
                <td align="center" style="border:1px solid black"><strong><? echo $result_size[csf('gmts_sizes')];?></strong></td>
                <?	}    ?>
                <td style="border:1px solid black" align="center"><strong>Total</strong></td>
                <td style="border:1px solid black" align="center"><strong>Fin Dia</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
			$i++;
            $nameArray_item_description=sql_select( "select distinct description,rate,uom,dia_width from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=2 and process=".$result_item[csf('process')]." and wo_qnty !=0 and status_active=1 ");
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $i ; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $conversion_cost_head_array[$result_item[csf('process')]]; ?>
                </td>
                <?
                $color_tatal=array();
                $total_amount_as_per_gmts_color=0;
                foreach($nameArray_item_description as $result_itemdescription)
                {
                $item_desctiption_total=0;
                ?>
                <td style="border:1px solid black"><? echo $fabric_description_array[$result_itemdescription[csf('description')]]; ?> </td>
                <td style="border:1px solid black"><? //echo $result_itemdescription['brand_supplier']; ?> Booking Qnty  </td>
                <?
					foreach($nameArray_size  as $result_size)
					{
					$nameArray_size_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls   where   booking_no=$txt_booking_no and sensitivity=2 and process=". $result_item[csf('process')]." and description='". $result_itemdescription[csf('description')]."' and rate='". $result_itemdescription[csf('rate')]."' and uom='". $result_itemdescription[csf('uom')]."' and dia_width='". $result_itemdescription[csf('dia_width')]."' and item_size='".$result_size[csf('gmts_sizes')]."' and status_active=1 ");

					foreach($nameArray_size_size_qnty as $result_size_size_qnty)
					{
					?>
					<td style="border:1px solid black; text-align:right">
					<?
					if($result_size_size_qnty[csf('cons')]!= "")
					{
					echo number_format($result_size_size_qnty[csf('cons')],2);
					$item_desctiption_total += $result_size_size_qnty[csf('cons')] ;
					if (array_key_exists($result_size[csf('gmts_sizes')], $color_tatal))
					{
					$color_tatal[$result_size[csf('gmts_sizes')]]+=$result_size_size_qnty[csf('cons')];
					}
					else
					{
					$color_tatal[$result_size[csf('gmts_sizes')]]=$result_size_size_qnty[csf('cons')];
					}
					}
					else echo "";
                ?>
                </td>
                <?
                }
                }
                ?>

                <td style="border:1px solid black; text-align:right"><? echo number_format($item_desctiption_total,2); ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('dia_width')]; ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$result_itemdescription[csf('uom')]]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <?
                $amount_as_per_gmts_color = $item_desctiption_total*$result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="2"><strong> Item Total</strong></td>
                <?
                foreach($nameArray_size  as $result_size)
                {

                ?>
                <td style="border:1px solid black;  text-align:right">
                <?
                if($color_tatal[$result_size[gmts_sizes]] !='')
                {
                echo number_format($color_tatal[$result_size[gmts_sizes]],2);
                }
                ?>
                </td>
            <?
            }
            ?>
                <td style="border:1px solid black;  text-align:right"><? echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
                 <td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black; text-align:right"><? //echo number_format(round($color_total),0); ?></td>
                <td style="border:1px solid black; text-align:right">
                <?
                echo number_format($total_amount_as_per_gmts_color,4);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="<? echo count($nameArray_size)+8; ?>"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,4); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
            </tr>
        </table>
        <br/>
        <?
		}
		?>
        <!--==============================================AS PER SIZE  END=========================================  -->

         <!--==============================================AS PER CONTRAST COLOR START=========================================  -->
		<?
		//$nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=2");
       // $nameArray_size=sql_select( "select distinct  item_size  as gmts_sizes from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=2");

	    $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=3 and wo_qnty !=0 and status_active=1 ");
        $nameArray_color=sql_select( "select distinct fabric_color_id as color_number_id from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=3 and wo_qnty !=0 and status_active=1 ");
		if(count($nameArray_color)>0)
		{
        ?>
        <table border="0" align="left" cellpadding="0" width="100%" cellspacing="0" >
            <tr>
                <td colspan="<? echo count($nameArray_color)+8; ?>" align="">
                <strong>Contrast Color</strong>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Service Type</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong>Item Color</strong> </td>
                <?
                foreach($nameArray_color  as $result_color)
                {	     ?>
                <td align="center" style="border:1px solid black"><strong><? echo $color_library[$result_color[csf('color_number_id')]];?></strong></td>
                <?	}    ?>
                <td style="border:1px solid black" align="center"><strong>Total</strong></td>
                <td style="border:1px solid black" align="center"><strong>Fin Dia</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
				$i++;
				//echo "select distinct description,rate,uom,dia_width from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=3 and process=".$result_item[csf('process')]." and wo_qnty !=0 ";
            $nameArray_item_description=sql_select( "select distinct description,rate,uom,dia_width from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=3 and process=".$result_item[csf('process')]." and wo_qnty !=0 and status_active=1 ");
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $i; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $conversion_cost_head_array[$result_item[csf('process')]]; ?>
                </td>
                <?
                $color_tatal=array();
                $total_amount_as_per_gmts_color=0;
                foreach($nameArray_item_description as $result_itemdescription)
                {
                $item_desctiption_total=0;
                ?>
                <td style="border:1px solid black"><? echo $fabric_description_array[$result_itemdescription[csf('description')]]; ?> </td>
                <td style="border:1px solid black"><? //echo $result_itemdescription['brand_supplier']; ?> Booking Qnty  </td>
                <?
                foreach($nameArray_color  as $result_color)
                {
                    // and dia_width='". $result_itemdescription[csf('dia_width')]."'
                $nameArray_color_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls    where   booking_no=$txt_booking_no and sensitivity=3 and process=". $result_item[csf('process')]." and description='". $result_itemdescription[csf('description')]."' and rate='". $result_itemdescription[csf('rate')]."' and uom='". $result_itemdescription[csf('uom')]."'  and fabric_color_id=".$result_color[csf('color_number_id')]." and status_active=1 ");
                foreach($nameArray_color_size_qnty as $result_color_size_qnty)
                {
                ?>
                <td style="border:1px solid black; text-align:right">
                <?
                if($result_color_size_qnty[csf('cons')]!= "")
                {
                echo number_format($result_color_size_qnty[csf('cons')],2);
                $item_desctiption_total += $result_color_size_qnty[csf('cons')] ;
                if (array_key_exists($result_color[csf('color_number_id')], $color_tatal))
                {
                $color_tatal[$result_color[csf('color_number_id')]]+=$result_color_size_qnty[csf('cons')];
                }
                else
                {
                $color_tatal[$result_color[csf('color_number_id')]]=$result_color_size_qnty[csf('cons')];
                }
                }
                else echo "";
                ?>
                </td>
                <?
                }
                }
                ?>
                <td style="border:1px solid black; text-align:right"><? echo number_format($item_desctiption_total,2); ?></td>
                <td style="border:1px solid black; text-align:center"><? echo $result_itemdescription[csf('dia_width')]; ?></td>
                <td style="border:1px solid black; text-align:center"><? echo $unit_of_measurement[$result_itemdescription[csf('uom')]]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <?
                $amount_as_per_gmts_color = $item_desctiption_total*  $result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="2"><strong> Item Total</strong></td>
                <?
                foreach($nameArray_color  as $result_color)
                {

                ?>
                <td style="border:1px solid black;  text-align:right">
                <?
                if($color_tatal[$result_color[csf('color_number_id')]] !='')
                {
                echo number_format($color_tatal[$result_color[csf('color_number_id')]],2);
                }
                ?>
                </td>
            <?
            }
            ?>
                <td style="border:1px solid black;  text-align:right"><? echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black;text-align:center"></td>
                <td style="border:1px solid black;text-align:center"></td>
                <td style="border:1px solid black; text-align:right"><? //echo number_format(round($color_total),0); ?></td>
                <td style="border:1px solid black; text-align:right">
                <?
                echo number_format($total_amount_as_per_gmts_color,4);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="<? echo count($nameArray_color)+8; ?>"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
            </tr>
        </table>
        <br/>
        <?
		}
		?>
        <!--==============================================AS PER CONTRAST COLOR END=========================================  -->

        <!--==============================================AS PER GMTS Color & SIZE START=========================================  -->
		<?
		//$nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=2");
       // $nameArray_size=sql_select( "select distinct  item_size  as gmts_sizes from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=2");
	   //$nameArray_color=sql_select( "select distinct fabric_color_id as color_number_id from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=3");

        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=4 and wo_qnty !=0 and status_active=1 ");
        $nameArray_size=sql_select( "select distinct item_size  as gmts_sizes from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=4 and wo_qnty !=0 and status_active=1 ");
	    $nameArray_color=sql_select( "select distinct fabric_color_id as color_number_id from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=4 and wo_qnty !=0 and status_active=1 ");

		if(count($nameArray_size)>0)
		{
        ?>

        <table border="0" align="left" cellpadding="0" width="100%" cellspacing="0" >
            <tr>
                <td colspan="<? echo count($nameArray_size)+8; ?>" align="">
                <strong>Color & size sensitive </strong>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Service Type</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong></strong> </td>
                <td style="border:1px solid black"><strong>Item Color</strong> </td>
                <?
                foreach($nameArray_size  as $result_size)
                {	     ?>
                <td align="center" style="border:1px solid black"><strong><? echo $result_size[csf('gmts_sizes')];?></strong></td>
                <?	}    ?>
                <td style="border:1px solid black" align="center"><strong>Total</strong></td>
                <td style="border:1px solid black" align="center"><strong>Fin Dia</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
			$i++;
            $nameArray_item_description=sql_select( "select distinct description,rate,uom,dia_width from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=4 and process=".$result_item[csf('process')]." and wo_qnty !=0 and status_active=1 ");
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo   (count($nameArray_item_description)*count($nameArray_color)); ?>">
                <? echo $i ; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo (count($nameArray_item_description)*count($nameArray_color)); ?>">
                <? echo $conversion_cost_head_array[$result_item[csf('process')]]; ?>
                </td>
                <?
                $color_tatal=array();
                $total_amount_as_per_gmts_color=0;
                foreach($nameArray_item_description as $result_itemdescription)
                {
					?>
                    <td style="border:1px solid black" rowspan="<? echo count($nameArray_color); ?>"><? echo $fabric_description_array[$result_itemdescription[csf('description')]]; ?> </td>
                    <td style="border:1px solid black" rowspan="<? echo count($nameArray_color); ?>"><? //echo $result_itemdescription['brand_supplier']; ?>Booking Qnty </td>
                    <?
                //$item_desctiption_total=0;
				foreach($nameArray_color as $result_color)
                {
					 $item_desctiption_total=0;
                ?>

                <td style="border:1px solid black"><? echo $color_library[$result_color[csf('color_number_id')]]; ?> </td>
                <?
                foreach($nameArray_size  as $result_size)
                {
                $nameArray_size_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=4 and process=". $result_item[csf('process')]." and  description='". $result_itemdescription[csf('description')]."' and  item_size='".$result_size[csf('gmts_sizes')]."' and rate='". $result_itemdescription[csf('rate')]."' and uom='". $result_itemdescription[csf('uom')]."' and dia_width='". $result_itemdescription[csf('dia_width')]."' and fabric_color_id=".$result_color[csf('color_number_id')]." and status_active=1 ");
                foreach($nameArray_size_size_qnty as $result_size_size_qnty)
                {
                ?>
                <td style="border:1px solid black; text-align:right">
                <?
                if($result_size_size_qnty[csf('cons')]!= "")
                {
                echo number_format($result_size_size_qnty[csf('cons')],2);
                $item_desctiption_total += $result_size_size_qnty[csf('cons')] ;
                if (array_key_exists($result_size[csf('color_number_id')], $color_tatal))
                {
                $color_tatal[$result_size[csf('color_number_id')]]+=$result_size_size_qnty[csf('cons')];
                }
                else
                {
                $color_tatal[$result_size[csf('color_number_id')]]=$result_size_size_qnty[csf('cons')];
                }
                }
                else echo "";
                ?>
                </td>
                <?
                }
                }
                ?>

                <td style="border:1px solid black; text-align:right"><? echo number_format($item_desctiption_total,2); ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('dia_width')]; ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$result_itemdescription[csf('uom')]]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <?
                $amount_as_per_gmts_color = $item_desctiption_total*  $result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
			}
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="5"><strong> Item Total</strong></td>
                <?
                foreach($nameArray_size  as $result_size)
                {

                ?>
                <td style="border:1px solid black;  text-align:right">
                <?
                if($color_tatal[$result_size[csf('gmts_sizes')]] !='')
                {
                echo number_format($color_tatal[$result_size[csf('gmts_sizes')]],2);
                }
                ?>
                </td>
            <?
            }
            ?>
                <td style="border:1px solid black;  text-align:right"><? echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black; text-align:right"><? //echo number_format(round($color_total),0); ?></td>
                <td style="border:1px solid black; text-align:right">
                <?
                echo number_format($total_amount_as_per_gmts_color,4);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="<? echo count($nameArray_size)+9; ?>"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
            </tr>
        </table>
        <br/>
        <?
		}
		?>
        <!--==============================================AS PER Color & SIZE  END=========================================  -->


         <!--==============================================NO NENSITIBITY START=========================================  -->
		<?
        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=0 and wo_qnty !=0 and status_active=1 ");
        //$nameArray_color=sql_select( "select distinct b.color_number_id from wo_trims_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=$txt_booking_no and a.sensitivity=1");
		$nameArray_color= array();
		if(count($nameArray_item)>0)
		{
        ?>
        <table border="0" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" >
            <tr>
                <td colspan="7" align="">
                <strong>No Sensitivity</strong>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Service Type</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong></strong> </td>
                <td align="center" style="border:1px solid black"><strong> Qnty</strong></td>
                <td style="border:1px solid black" align="center"><strong>Fin Dia</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
				$i++;
            $nameArray_item_description=sql_select( "select distinct description,rate,uom,dia_width from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=0 and process=".$result_item[csf('process')]." and wo_qnty !=0 and status_active=1 ");
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $i; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $conversion_cost_head_array[$result_item[csf('process')]]; ?>
                </td>
                <?
                $color_tatal=0;
                $total_amount_as_per_gmts_color=0;
                foreach($nameArray_item_description as $result_itemdescription)
                {
                $item_desctiption_total=0;
                ?>
                <td style="border:1px solid black"><? echo $fabric_description_array[$result_itemdescription[csf('description')]]; ?> </td>
                <td style="border:1px solid black"><? //echo $result_itemdescription['brand_supplier']; ?>Booking Qnty  </td>
                <?
				if($result_itemdescription[csf("dia_width")]=="") $dia_width_cond=" and dia_width is null";else $dia_width_cond=" and dia_width='".$result_itemdescription[csf("dia_width")]."'";
                $nameArray_color_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls where    booking_no=$txt_booking_no and sensitivity=0 and process=". $result_item[csf('process')]." and  description='". $result_itemdescription[csf('description')]."'and rate='". $result_itemdescription[csf('rate')]."' and uom='". $result_itemdescription[csf('uom')]."' $dia_width_cond and status_active=1 ");
                foreach($nameArray_color_size_qnty as $result_color_size_qnty)
                {
                ?>
                <td style="border:1px solid black; text-align:right">
                <?
                if($result_color_size_qnty[csf('cons')]!= "")
                {
                echo number_format($result_color_size_qnty[csf('cons')],2);
                $item_desctiption_total += $result_color_size_qnty[csf('cons')] ;
                $color_tatal+=$result_color_size_qnty[csf('cons')];
                }
                else echo "";
                ?>
                </td>
                <?
                }
                ?>
                <td style="border:1px solid black; text-align:center "><? echo $result_itemdescription[csf('dia_width')]; ?></td>
                <td style="border:1px solid black; text-align:center "><? echo $unit_of_measurement[$result_itemdescription[csf('uom')]]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <?
                $amount_as_per_gmts_color = $item_desctiption_total*  $result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="2"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right">
                <?
                if($color_tatal !='')
                {
                echo number_format($color_tatal,2);
                }
                ?>
                </td>
                <td style="border:1px solid black;"></td>
                <td style="border:1px solid black;"></td>
                <td style="border:1px solid black; text-align:right"></td>
                <td style="border:1px solid black; text-align:right">
                <?
                echo number_format($total_amount_as_per_gmts_color,4);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="8"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
            </tr>
        </table>
        <?
		//print_r($color_tatal);
		}
		?>
        <!--==============================================NO NENSITIBITY END=========================================  -->
       &nbsp;
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
       <table  width="100%" class="rpt_table" style="border:1px solid black;"   border="0" cellpadding="0" cellspacing="0">
       <tr style="border:1px solid black;">
                <th width="70%" style="border:1px solid black; text-align:right">Total Booking Amount</th><td width="30%" style="border:1px solid black; text-align:right"><? echo number_format($booking_grand_total,2);?></td>
            </tr>
            <tr style="border:1px solid black;">
                <th width="70%" style="border:1px solid black; text-align:right">Total Booking Amount (in word)</th><td width="30%" style="border:1px solid black;"><? echo number_to_words(number_format($booking_grand_total,2),$mcurrency, $dcurrency);?></td>
            </tr>
       </table>
          &nbsp;
        <table  width="100%" class="rpt_table" style="border:1px solid black;"   border="0" cellpadding="0" cellspacing="0">
        <thead>
            <tr style="border:1px solid black;">
                <th width="3%" style="border:1px solid black;">Sl</th><th width="97%" style="border:1px solid black;">Spacial Instruction</th>
            </tr>
        </thead>
        <tbody>
        <?
        $data_array=sql_select("select id, terms from  wo_booking_terms_condition where booking_no=$txt_booking_no");// quotation_id='$data'
        if ( count($data_array)>0)
        {
            $i=0;
            foreach( $data_array as $row )
            {
                $i++;
                ?>
                    <tr id="settr_1" align="" style="border:1px solid black;">
                        <td style="border:1px solid black;">
                        <? echo $i;?>
                        </td>
                        <td style="border:1px solid black;">
                        <? echo $row[csf('terms')]; ?>
                        </td>
                    </tr>
                <?
            }
        }
        else
        {
			$i=0;
        $data_array=sql_select("select id, terms from  lib_terms_condition where is_default=1 and page_id=176");// quotation_id='$data'
        foreach( $data_array as $row )
            {
                $i++;
        ?>
        <tr id="settr_1" align="" style="border:1px solid black;">
                        <td style="border:1px solid black;">
                        <? echo $i;?>
                        </td>
                        <td style="border:1px solid black;">
                        <? echo $row[csf('terms')]; ?>
                        </td>

                    </tr>
        <?
            }
        }
        ?>
    </tbody>
    </table>

         <br/>

		 <?
            echo signature_table(53, $cbo_company_name, "1313px");
			echo "****".custom_file_name($txt_booking_no,$style_sting,$job_no);
         ?>
    </div>
    <script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <script>
		fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');
	</script>
<?

	$html = ob_get_contents();
	ob_clean();
	list($is_mail_send,$mail)=explode('___',$mail_send_data);
	if($is_mail_send==1){
		
            //pdf att file..............................................
			$REAL_FILE_NAME = custom_file_name($txt_booking_no,$style_sting,$job_no);
			$user_id=$_SESSION['logic_erp']['user_id'];
			foreach (glob("../../../auto_mail/tmp/".$REAL_FILE_NAME) as $filename) {			
				@unlink($filename);
			}
			$att_file_arr=array();
			require('../../../ext_resource/mpdf60/mpdf.php');
			$mpdf = new mPDF('', 'A4-L', '', '', 10, 10, 10, 20, 3, 3);	
			$mpdf->WriteHTML($html,2);
			//$REAL_FILE_NAME = 'trims_booking_multy_job_'.$user_id.'.pdf';
			$file_path='../../../auto_mail/tmp/'.$REAL_FILE_NAME;
			$mpdf->Output($file_path, 'F');
			$att_file_arr[]='../../../auto_mail/tmp/'.$REAL_FILE_NAME.'**'.$REAL_FILE_NAME;
			//..............................................pdf att file;
            
           // echo $html;die;
        
        
        require_once('../../../mailer/class.phpmailer.php');
		require_once('../../../auto_mail/setting/mail_setting.php');
		$mailBody = preg_replace("/<img[^>]+\>/i", " ", $html); 
			
		$mailToArr=array();
		$mailSql = "select b.EMAIL  from wo_booking_mst a,LIB_SUPPLIER b where b.id=a.supplier_id and a.booking_no=$txt_booking_no";
		//echo $mailSql;die;
		$mailSqlRes=sql_select($mailSql);
		foreach($mailSqlRes as $rows){
			if($rows[EMAIL]){$mailToArr[]=$rows[EMAIL];}
		}
		
		
		$mailSql = "SELECT c.email_address as EMAIL FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=97 and b.mail_user_setup_id=c.id  and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
		//echo $mailSql;die;
		$mailSqlRes=sql_select($mailSql);
		foreach($mailSqlRes as $rows){
			if($rows[EMAIL]){$mailToArr[]=$rows[EMAIL];}
		}
		
		
		if($mail!=''){$mailToArr[]=$mail;}
		$to=implode(',',$mailToArr);
 		$subject=" Service Booking Sheet ";
		$header=mailHeader();
		echo sendMailMailer( $to, $subject, $mailBody, $from_mail,$att_file_arr );
		
	}
	else{
		echo $html;
	}
	exit();


}


if($action=="show_trim_booking_report1")
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','city');
	$company_address_arr=return_library_array( "select id,city from   lib_company",'id','city');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	//$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	ob_start();
	?>
	<div style="width:1333px" align="center">
       <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black">
           <tr>
               <td width="100">
               <img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
               </td>
               <td width="1250">
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
                                            Plot No: <? echo $result[csf('plot_no')]; ?>
                                            Level No: <? echo $result[csf('level_no')]?>
                                            Road No: <? echo $result[csf('road_no')]; ?>
                                            Block No: <? echo $result[csf('block_no')];?>
                                            City No: <? echo $result[csf('city')];?>
                                            Zip Code: <? echo $result[csf('zip_code')]; ?>
                                            Province No: <?php echo $result[csf('province')]; ?>
                                            Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br>
                                            Email Address: <? echo $result[csf('email')];?>
                                            Website No: <? echo $result[csf('website')];
                            }
                            ?>
                               </td>
                            </tr>
                            <tr>
                            <td align="center" style="font-size:20px">
                            <strong>Service Booking Sheet</strong>
                             </td>
                            </tr>
                      </table>
                </td>
            </tr>
       </table>
		<?
		$booking_grand_total=0;
		$job_no="";
		$currency_id="";
		$nameArray_job=sql_select( "select distinct b.job_no,a.buyer_id from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_no=$txt_booking_no");

	    $buyer_name=$nameArray_job[0][csf('buyer_id')];
        foreach ($nameArray_job as $result_job)
        {
			$job_no.=$result_job[csf('job_no')].",";
		}
		/*$po_no="";
		$nameArray_job=sql_select( "select distinct b.po_number from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no=$txt_booking_no");
        foreach ($nameArray_job as $result_job)
        {
			$po_no.=$result_job[csf('po_number')].",";
		}*/

		$po_no=""; $po_id='';

		$nameArray_job=sql_select( "select b.id, b.po_number from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no=$txt_booking_no group by b.id, b.po_number");

        foreach ($nameArray_job as $result_job)
        {
			$po_no.=$result_job[csf('po_number')].",";
			$po_id.=$result_job[csf('id')].",";
		}

		// PO ID Different But Po No Same Then Following Code (Added By Fuad)
		//$po_no=implode(",",array_unique(explode(",",substr($po_no,0,-1))));

        $nameArray=sql_select( "select a.booking_no,a.booking_date,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.source,a.pay_mode  from wo_booking_mst a where  a.booking_no=$txt_booking_no");
		//echo  "select po_break_down_id,article_number from wo_po_color_size_breakdown where po_break_down_id in(".rtrim($po_no,',').")";
		//$article_number_arr=return_library_array( "select po_break_down_id,article_number from wo_po_color_size_breakdown where po_break_down_id in(".rtrim($po_no,',').")", "po_break_down_id", "article_number"  );

		$po_id=substr($po_id,0,-1);//(Added By Fuad)
		$article_number_arr=return_library_array( "select po_break_down_id,article_number from wo_po_color_size_breakdown where po_break_down_id in(".$po_id.")", "po_break_down_id", "article_number");
		//print_r($article_number_arr);

        foreach ($nameArray as $result)
        {
        ?>
       <table width="100%" style="border:1px solid black">
            <tr>
                <td width="100" style="font-size:12px"><b>Booking No </b>   </td>
                <td width="110">:&nbsp;<? echo $result[csf('booking_no')];?> </td>
                <td width="100" style="font-size:12px"><b>Booking Date</b></td>
                <td width="110">:&nbsp;<? echo change_date_format($result[csf('booking_date')],'dd-mm-yyyy','-');?>&nbsp;&nbsp;&nbsp;</td>
                <td width="110" align="center"><b>IMAGE</b></td>

            </tr>
            <tr>
                <td width="100"><span style="font-size:12px"><b>Delivery Date</b></span></td>
                <td width="110">:&nbsp;<? echo change_date_format($result[csf('delivery_date')],'dd-mm-yyyy','-');?></td>
                <td  width="100" style="font-size:12px"><b>Attention</b></td>
                <td  width="110" >:&nbsp;<? echo $result[csf('attention')]; ?></td>
                <td  width="110" rowspan="6" align="center">

                <?
			$nameArray_imge =sql_select("SELECT image_location,real_file_name FROM common_photo_library where master_tble_id='".$result[csf('booking_no')]."' and file_type=1");
			?>

            	<table width="310">
                <tr>
                <?
				$img_counter = 0;
                foreach($nameArray_imge as $result_imge)
				{
				    if($path=="")
                    {
                    $path='../../';
                    }

					?>
					<td>
						<!--<img src="../../<? //echo $result_imge[csf('image_location')]; ?>" width="90" height="100" border="2" />-->
                        <img src="<? echo $path.$result_imge[csf('image_location')]; ?>" width="90" height="100" border="2" />
                       <?
					   $img=explode('.',$result_imge[csf('real_file_name')]);
					   echo $img[0];
					   ?>
					</td>
					<?

					$img_counter++;
				}
				?>
                </tr>
           </table>
                </td>
            </tr>
            <tr>
                <td width="100" style="font-size:12px"><b>Currency</b></td>
                <td width="110">:&nbsp;<? $currency_id =$result[csf('currency_id')]; echo $currency[$result[csf('currency_id')]]; ?></td>
                <td  width="100" style="font-size:12px"><b>Conversion Rate</b></td>
                <td  width="110" >:&nbsp;<? echo $result[csf('exchange_rate')]; ?></td>

            </tr>
             <tr>
                <td  width="100" style="font-size:12px"><b>Source</b></td>
                <td  width="110" >:&nbsp;<? echo $source[$result[csf('source')]]; ?></td>
                <td width="100" style="font-size:12px"><b>Supplier Name</b>   </td>
                <td width="110">:&nbsp;<?
                 if($result[csf("pay_mode")]!=3 && $result[csf("pay_mode")]!=5)
				{
					echo $supplier_name_arr[$result[csf('supplier_id')]];
				}
				else
				{
					echo $company_library[$result[csf('supplier_id')]];
				}
                ?>    </td>
            </tr>
             <tr>
                <td width="100" style="font-size:12px"><b>Supplier Address</b></td>
               	<td width="110" colspan="3">:&nbsp;<?
				if($result[csf("pay_mode")]!=3 && $result[csf("pay_mode")]!=5)
				{
					echo $supplier_address_arr[$result[csf('supplier_id')]];
				}
				else
				{
					echo $company_address_arr[$result[csf('supplier_id')]];
				}               	?></td>

            </tr>
            <tr>
                <td width="100" style="font-size:12px"><b>Job No</b>   </td>
                <td width="110" >:&nbsp;
				<?
				echo rtrim($job_no,',');
				?>
                </td>
                <td width="100" style="font-size:12px"><b>Buyer Name</b>   </td>
                <td width="110" >:&nbsp;
				<?
				echo $buyer_name_arr[$buyer_name];
				?>
                </td>
            </tr>
            <tr>
               	<td width="110" style="font-size:12px"><b>PO No</b> </td>
                <td  width="100" style="font-size:12px" colspan="3">:&nbsp;<? $po_nos= rtrim($po_no,',');
				$po_nos=implode(", ",array_unique(explode(",",$po_nos)));
				echo $po_nos;; ?> </td>
            </tr>
        </table>
        <br/>
		<?
        }
        ?>
          <!--==============================================AS PER GMTS COLOR START=========================================  -->
		<?
		//========================================
		$fabric_description_array=array();
	$wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select id,fabric_description,cons_process from wo_pre_cost_fab_conv_cost_dtls  where job_no='".rtrim($job_no,", ")."'");
	foreach( $wo_pre_cost_fab_conv_cost_dtls_id as $row_wo_pre_cost_fab_conv_cost_dtls_id)
	{
		if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]!=0)
		{
			$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description,gsm_weight from  wo_pre_cost_fabric_cost_dtls  where  id='".$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]."'");
			list($fabric_description_row)=$fabric_description;
			$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]=$body_part[$fabric_description_row[csf("body_part_id")]].', '.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")].', '.$fabric_description_row[csf("gsm_weight")];
		}
		if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]==0)
		{
			//echo "select id,body_part_id,color_type_id,fabric_description from  wo_pre_cost_fabric_cost_dtls  where  job_no='$data'";
			$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description,gsm_weight from  wo_pre_cost_fabric_cost_dtls  where  job_no='".rtrim($job_no,", ")."'");
			//list($fabric_description_row)=$fabric_description;
			foreach( $fabric_description as $fabric_description_row)
	        {
			$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]].=$body_part[$fabric_description_row[csf("body_part_id")]].', '.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")].', '.$fabric_description_row[csf("gsm_weight")];

			//$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]="All Fabrics  ".$conversion_cost_head_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("cons_process")]];
			}
		}


	}
	//print_r($fabric_description_array);
	//=================================================
        $nameArray_item=sql_select( "select distinct process,description from wo_booking_dtls  where booking_no=$txt_booking_no and wo_qnty !=0 and sensitivity=1 and status_active=1 ");//and sensitivity=1
        $nameArray_color=sql_select( "select distinct fabric_color_id from wo_booking_dtls   where  booking_no=$txt_booking_no and wo_qnty !=0 and sensitivity=1 and status_active=1 "); //and sensitivity=1

       if(count($nameArray_color)>0)
		{
		foreach($nameArray_item as $result_item)
        {
        ?>

        <table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="9" align="">
                <strong><? echo "Fabrication:".rtrim($fabric_description_array[$result_item[csf('description')]],", "); ?> </strong><br/>
                <strong><? echo "Process:".$conversion_cost_head_array[$result_item[csf('process')]]; ?> </strong>
                 <strong>As Per Gmts Color</strong>
                </td>
            </tr>
            <tr>

                <td style="border:1px solid black"><strong>Article No</strong> </td>
                <td style="border:1px solid black"><strong>Order No</strong> </td>
                <td style="border:1px solid black"><strong>GMT Color</strong> </td>
                <td style="border:1px solid black"><strong>Item Color</strong> </td>
                <td style="border:1px solid black" align="center"><strong>Wo Qty (Kg)</strong></td>
                <td style="border:1px solid black" align="center"><strong>Artwork No</strong></td>
                <td style="border:1px solid black" align="center"><strong>Fin Dia</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
            </tr>
            <?
			 $total_amount_as_per_gmts_color=0;
            $nameArray_item_description=sql_select( "select  po_break_down_id,fabric_color_id,gmts_color_id,description,rate,artwork_no,dia_width,sum(wo_qnty) as cons from wo_booking_dtls  where booking_no=$txt_booking_no  and process=".$result_item[csf('process')]." and description=".$result_item[csf('description')]." and wo_qnty !=0 and sensitivity=1 and status_active=1  group by po_break_down_id,fabric_color_id,gmts_color_id,description,rate,artwork_no,dia_width");//and sensitivity=1
                foreach($nameArray_item_description as $result_itemdescription)
                {

                ?>
            <tr>
                <td align="center" style="border:1px solid black">
                <? echo $article_number_arr[$result_itemdescription[csf('po_break_down_id')]]; ?>
                </td>
                <td style="border:1px solid black"><? echo rtrim($po_number[$result_itemdescription[csf('po_break_down_id')]],", "); ?> </td>
                <td style="border:1px solid black"><? echo$color_library[$result_itemdescription[csf('gmts_color_id')]]; ?>  </td>
                <td style="border:1px solid black"><? echo$color_library[$result_itemdescription[csf('fabric_color_id')]]; ?>  </td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('cons')],4); ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('artwork_no')]; ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('dia_width')]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <?
                $amount_as_per_gmts_color = $result_itemdescription[csf('cons')]*$result_itemdescription[csf('rate')];
				echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
				<?
                }
                ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="8"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right">
                <?
                echo number_format($total_amount_as_per_gmts_color,4);
                $booking_grand_total+=$total_amount_as_per_gmts_color;
                ?>
                </td>
            </tr>

        </table>
        &nbsp;
        <br/>
        <?
		}
		}
		?>
        <!--==============================================AS PER GMTS COLOR END=========================================  -->
        <?

        //=================================================
        $nameArray_item=sql_select( "select distinct process,description from wo_booking_dtls  where booking_no=$txt_booking_no and wo_qnty !=0 and sensitivity=3 and status_active=1 ");//and sensitivity=1
        $nameArray_color=sql_select( "select distinct fabric_color_id from wo_booking_dtls   where  booking_no=$txt_booking_no and wo_qnty !=0 and sensitivity=3 and status_active=1 "); //and sensitivity=1

       if(count($nameArray_color)>0)
		{
		foreach($nameArray_item as $result_item)
        {
        ?>

        <table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="9" align="">
                <strong><? echo "Fabrication:".rtrim($fabric_description_array[$result_item[csf('description')]],", "); ?> </strong><br/>
                <strong><? echo "Process:".$conversion_cost_head_array[$result_item[csf('process')]]; ?> </strong>
                 <strong>As Per Constrast Color</strong>
                </td>
            </tr>
            <tr>

                <td style="border:1px solid black"><strong>Article No</strong> </td>
                <td style="border:1px solid black"><strong>Order No</strong> </td>
                <td style="border:1px solid black"><strong>GMT Color</strong> </td>
                <td style="border:1px solid black"><strong>Item Color</strong> </td>
                <td style="border:1px solid black" align="center"><strong>Wo Qty (Kg)</strong></td>
                <td style="border:1px solid black" align="center"><strong>Artwork No</strong></td>
                <td style="border:1px solid black" align="center"><strong>Fin Dia</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
            </tr>
            <?
			 $total_constrast_color=0;
            $nameArray_item_description=sql_select( "select  po_break_down_id,fabric_color_id,gmts_color_id,description,rate,artwork_no,dia_width,sum(wo_qnty) as cons from wo_booking_dtls  where booking_no=$txt_booking_no and status_active=1  and process=".$result_item[csf('process')]." and description=".$result_item[csf('description')]." and wo_qnty !=0 and sensitivity=3  group by po_break_down_id,fabric_color_id,gmts_color_id,description,rate,artwork_no,dia_width");//and sensitivity=1
                foreach($nameArray_item_description as $result_itemdescription)
                {

                ?>
            <tr>
                <td align="center" style="border:1px solid black">
                <? echo $article_number_arr[$result_itemdescription[csf('po_break_down_id')]]; ?>
                </td>
                <td style="border:1px solid black"><? echo rtrim($po_number[$result_itemdescription[csf('po_break_down_id')]],", "); ?> </td>
                <td style="border:1px solid black"><? echo$color_library[$result_itemdescription[csf('gmts_color_id')]]; ?>  </td>
                <td style="border:1px solid black"><? echo$color_library[$result_itemdescription[csf('fabric_color_id')]]; ?>  </td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('cons')],4); ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('artwork_no')]; ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('dia_width')]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <?
                $amount_constrast_color = $result_itemdescription[csf('cons')]*$result_itemdescription[csf('rate')];
				echo number_format($amount_constrast_color,4);
                $total_constrast_color+=$amount_constrast_color;
                ?>
                </td>
            </tr>
				<?
                }
                ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="8"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right">
                <?
                echo number_format($total_constrast_color,4);
                $booking_grand_total+=$total_constrast_color;
                ?>
                </td>
            </tr>

        </table>
        &nbsp;
        <br/>
        <?
		}
		}
		?>
        <!--==============================================Constrast COLOR END=========================================  -->
        <?
        //=================================================
        $nameArray_item=sql_select( "select distinct process,description from wo_booking_dtls  where booking_no=$txt_booking_no and wo_qnty !=0 and sensitivity=2 and status_active=1 ");//and sensitivity=1
        $nameArray_color=sql_select( "select distinct item_size from wo_booking_dtls   where  booking_no=$txt_booking_no and wo_qnty !=0 and sensitivity=2 and status_active=1 "); //and sensitivity=1

       if(count($nameArray_color)>0)
		{
		foreach($nameArray_item as $result_item)
        {
        ?>

        <table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="9" align="">
                <strong><? echo "Fabrication:".rtrim($fabric_description_array[$result_item[csf('description')]],", "); ?> </strong><br/>
                <strong><? echo "Process:".$conversion_cost_head_array[$result_item[csf('process')]]; ?> </strong>
                 <strong> As Per Size Sensitive</strong>
                </td>
            </tr>
            <tr>

                <td style="border:1px solid black"><strong>Article No</strong> </td>
                <td style="border:1px solid black"><strong>Order No</strong> </td>
                <td style="border:1px solid black"><strong>GMT Size</strong> </td>
                <td style="border:1px solid black"><strong>Item Size</strong> </td>
                <td style="border:1px solid black" align="center"><strong>Wo Qty (Kg)</strong></td>
                <td style="border:1px solid black" align="center"><strong>Artwork No</strong></td>
                <td style="border:1px solid black" align="center"><strong>Fin Dia</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
            </tr>
            <?
			 $total_amount_size_sensitive=0;
            $nameArray_item_description=sql_select( "select  po_break_down_id,item_size,gmts_size,description,rate,artwork_no,dia_width,sum(wo_qnty) as cons from wo_booking_dtls  where booking_no=$txt_booking_no  and process=".$result_item[csf('process')]." and description=".$result_item[csf('description')]." and status_active=1  and wo_qnty !=0 and sensitivity=2  group by po_break_down_id,item_size,gmts_size,description,rate,artwork_no,dia_width");//and sensitivity=1
                foreach($nameArray_item_description as $result_itemdescription)
                {

                ?>
            <tr>
                <td align="center" style="border:1px solid black">
                <? echo $article_number_arr[$result_itemdescription[csf('po_break_down_id')]]; ?>
                </td>
                <td style="border:1px solid black"><? echo rtrim($po_number[$result_itemdescription[csf('po_break_down_id')]],", "); ?> </td>
                <td style="border:1px solid black"><? echo $size_library[$result_itemdescription[csf('gmts_size')]]; ?>  </td>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('item_size')]; ?>  </td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('cons')],4); ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('artwork_no')]; ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('dia_width')]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <?
                $amount_size_sensitive = $result_itemdescription[csf('cons')]*$result_itemdescription[csf('rate')];
				echo number_format($amount_size_sensitive,4);
                $total_amount_size_sensitive+=$amount_size_sensitive;
                ?>
                </td>
            </tr>
				<?
                }
                ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="8"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right">
                <?
                echo number_format($total_amount_size_sensitive,4);
                $booking_grand_total+=$total_amount_size_sensitive;
                ?>
                </td>
            </tr>

        </table>
        &nbsp;
        <br/>
        <?
		}
		}
		?>
        <!--==============================================Size Sensitive END=========================================  -->

         <?
        //=================================================
        $nameArray_item=sql_select( "select distinct process,description from wo_booking_dtls  where booking_no=$txt_booking_no and wo_qnty !=0 and sensitivity=4 and status_active=1 ");//and sensitivity=1
        $nameArray_color=sql_select( "select distinct item_size from wo_booking_dtls   where  booking_no=$txt_booking_no and wo_qnty !=0 and sensitivity=4 and status_active=1 "); //and sensitivity=1

       if(count($nameArray_color)>0)
		{
		foreach($nameArray_item as $result_item)
        {
        ?>

        <table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="11" align="">
                <strong><? echo "Fabrication:".rtrim($fabric_description_array[$result_item[csf('description')]],", "); ?> </strong><br/>
                <strong><? echo "Process:".$conversion_cost_head_array[$result_item[csf('process')]]; ?> </strong>
                 <strong> As Per Color & Size Sensitive</strong>
                </td>
            </tr>
            <tr>

                <td style="border:1px solid black"><strong>Article No</strong> </td>
                <td style="border:1px solid black"><strong>Order No</strong> </td>
                <td style="border:1px solid black"><strong>GMT Color</strong> </td>
                <td style="border:1px solid black"><strong>Item Color</strong> </td>
                <td style="border:1px solid black"><strong>GMT Size</strong> </td>
                <td style="border:1px solid black"><strong>Item Size</strong> </td>
                <td style="border:1px solid black" align="center"><strong>Wo Qty (Kg)</strong></td>
                <td style="border:1px solid black" align="center"><strong>Artwork No</strong></td>
                <td style="border:1px solid black" align="center"><strong>Fin Dia</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
            </tr>
            <?
			 $total_amount_color_and_size_sensitive=0;
            $nameArray_item_description=sql_select( "select  po_break_down_id,fabric_color_id,gmts_color_id,item_size,gmts_size,description,rate,artwork_no,dia_width,sum(wo_qnty) as cons from wo_booking_dtls  where booking_no=$txt_booking_no and status_active=1   and process=".$result_item[csf('process')]." and description=".$result_item[csf('description')]." and wo_qnty !=0 and sensitivity=4  group by po_break_down_id,fabric_color_id,gmts_color_id,item_size,gmts_size,description,rate,artwork_no,dia_width");//and sensitivity=1
                foreach($nameArray_item_description as $result_itemdescription)
                {

                ?>
            <tr>
                <td align="center" style="border:1px solid black">
                <? echo $article_number_arr[$result_itemdescription[csf('po_break_down_id')]]; ?>
                </td>
                <td style="border:1px solid black"><? echo rtrim($po_number[$result_itemdescription[csf('po_break_down_id')]],", "); ?> </td>
                <td style="border:1px solid black"><? echo $color_library[$result_itemdescription[csf('gmts_color_id')]]; ?>  </td>
                <td style="border:1px solid black"><? echo $color_library[$result_itemdescription[csf('fabric_color_id')]]; ?>  </td>
                <td style="border:1px solid black"><? echo $size_library[$result_itemdescription[csf('gmts_size')]]; ?>  </td>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('item_size')]; ?>  </td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('cons')],4); ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('artwork_no')]; ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('dia_width')]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <?
                $amount_color_and_size_sensitive = $result_itemdescription[csf('cons')]*$result_itemdescription[csf('rate')];
				echo number_format($amount_color_and_size_sensitive,4);
                $total_amount_color_and_size_sensitive+=$amount_size_sensitive;
        ?>
                </td>
            </tr>
				<?
                }
                ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="10"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right">
                <?
                echo number_format($total_amount_color_and_size_sensitive,4);
                $booking_grand_total+=$total_amount_color_and_size_sensitive;
                ?>
                </td>
            </tr>

        </table>
        &nbsp;
        <br/>
        <?
		}
		}
		?>
        <!--==============================================Size Sensitive END=========================================  -->

        <!--==============================================NO NENSITIBITY START=========================================  -->
		<?
        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=0 and wo_qnty !=0 and status_active=1 ");
        //$nameArray_color=sql_select( "select distinct b.color_number_id from wo_trims_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=$txt_booking_no and a.sensitivity=1");
		$nameArray_color= array();
		if(count($nameArray_item)>0)
		{
        ?>
        <table border="0" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" >
            <tr>
                <td colspan="7" align="">
                <strong> As Per No Sensitivity</strong>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Service Type</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong></strong> </td>
                <td align="center" style="border:1px solid black"><strong> Qnty</strong></td>
                <td style="border:1px solid black" align="center"><strong>Fin Dia</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
				$i++;
            $nameArray_item_description=sql_select( "select distinct description,rate,uom,dia_width from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=0 and process=".$result_item[csf('process')]." and wo_qnty !=0 and status_active=1 ");
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $i; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $conversion_cost_head_array[$result_item[csf('process')]]; ?>
                </td>
                <?
                $color_tatal=0;
                $total_amount_as_per_gmts_color=0;
                foreach($nameArray_item_description as $result_itemdescription)
                {
                $item_desctiption_total=0;
                ?>
                <td style="border:1px solid black"><? echo $fabric_description_array[$result_itemdescription[csf('description')]]; ?> </td>
                <td style="border:1px solid black"><? //echo $result_itemdescription['brand_supplier']; ?>Booking Qnty  </td>
                <?
                $nameArray_color_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls where    booking_no=$txt_booking_no and sensitivity=0 and process=". $result_item[csf('process')]." and  description='". $result_itemdescription[csf('description')]."'and rate='". $result_itemdescription[csf('rate')]."' and uom='". $result_itemdescription[csf('uom')]."' and dia_width='". $result_itemdescription[csf('dia_width')]."' and status_active=1 ");
                foreach($nameArray_color_size_qnty as $result_color_size_qnty)
                {
                ?>
                <td style="border:1px solid black; text-align:right">
                <?
                if($result_color_size_qnty[csf('cons')]!= "")
                {
                echo number_format($result_color_size_qnty[csf('cons')],2);
                $item_desctiption_total += $result_color_size_qnty[csf('cons')] ;
                $color_tatal+=$result_color_size_qnty[csf('cons')];
                }
                else echo "";
                ?>
                </td>
                <?
                }
                ?>
                <td style="border:1px solid black; text-align:center "><? echo $result_itemdescription[csf('dia_width')]; ?></td>
                <td style="border:1px solid black; text-align:center "><? echo $unit_of_measurement[$result_itemdescription[csf('uom')]]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <?
                $amount_as_per_gmts_color = $item_desctiption_total*  $result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="2"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right">
                <?
                if($color_tatal !='')
                {
                echo number_format($color_tatal,2);
                }
                ?>
                </td>
                <td style="border:1px solid black;"></td>
                <td style="border:1px solid black;"></td>
                <td style="border:1px solid black; text-align:right"></td>
                <td style="border:1px solid black; text-align:right">
                <?
                echo number_format($total_amount_as_per_gmts_color,4);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="8"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,4); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
            </tr>
        </table>
        <?
		//print_r($color_tatal);
		}
		?>
        <!--==============================================NO NENSITIBITY END=========================================  -->
       &nbsp;
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
       <table  width="100%" class="rpt_table" style="border:1px solid black;"   border="1" cellpadding="0" cellspacing="0" rules="all">
       <tr style="border:1px solid black;">
                <th width="70%" style="border:1px solid black; text-align:right">Total Booking Amount</th><td width="30%" style="border:1px solid black; text-align:right"><? echo number_format($booking_grand_total,2);?></td>
            </tr>
            <tr style="border:1px solid black;">
                <th width="70%" style="border:1px solid black; text-align:right">Total Booking Amount (in word)</th><td width="30%" style="border:1px solid black;"><? echo number_to_words($booking_grand_total,$mcurrency, $dcurrency);?></td>
            </tr>
       </table>
          &nbsp;
        <table  width="100%" class="rpt_table" style="border:1px solid black;"   border="1" cellpadding="0" cellspacing="0" rules="all">
        <thead>
            <tr style="border:1px solid black;">
                <th width="3%" style="border:1px solid black;">Sl</th><th width="97%" style="border:1px solid black;">Spacial Instruction</th>
            </tr>
        </thead>
        <tbody>
        <?
        $data_array=sql_select("select id, terms from  wo_booking_terms_condition where booking_no=$txt_booking_no");// quotation_id='$data'
        if ( count($data_array)>0)
        {
            $i=0;
            foreach( $data_array as $row )
            {
                $i++;
                ?>
                    <tr id="settr_1" align="" style="border:1px solid black;">
                        <td style="border:1px solid black;">
                        <? echo $i;?>
                        </td>
                        <td style="border:1px solid black;">
                        <? echo $row[csf('terms')]; ?>
                        </td>
                    </tr>
                <?
            }
        }
        else
        {
			$i=0;
        $data_array=sql_select("select id, terms from  lib_terms_condition where is_default=1 and page_id=176");// quotation_id='$data'
        foreach( $data_array as $row )
            {
                $i++;
        ?>
        <tr id="settr_1" align="" style="border:1px solid black;">
                        <td style="border:1px solid black;">
                        <? echo $i;?>
                        </td>
                        <td style="border:1px solid black;">
                        <? echo $row[csf('terms')]; ?>
                        </td>

                    </tr>
        <?
            }
        }
        ?>
    </tbody>
    </table>

         <br/>

		 <?
            echo signature_table(53, $cbo_company_name, "1313px");
			echo "****".custom_file_name($txt_booking_no,$style_sting,$job_no);
         ?>
    </div>
<?
	$html = ob_get_contents();
	ob_clean();
	list($is_mail_send,$mail)=explode('___',$mail_send_data);
	if($is_mail_send==1){
		require_once('../../../mailer/class.phpmailer.php');
		require_once('../../../auto_mail/setting/mail_setting.php');
		$mailBody = preg_replace("/<img[^>]+\>/i", " ", $html); 
			
		$mailToArr=array();
		$mailSql = "select b.EMAIL  from wo_booking_mst a,LIB_SUPPLIER b where b.id=a.supplier_id and a.booking_no=$txt_booking_no";
		//echo $mailSql;die;
		$mailSqlRes=sql_select($mailSql);
		foreach($mailSqlRes as $rows){
			if($rows[EMAIL]){$mailToArr[]=$rows[EMAIL];}
		}
		
		
		$mailSql = "SELECT c.email_address as EMAIL FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=97 and b.mail_user_setup_id=c.id  and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
		//echo $mailSql;die;
		$mailSqlRes=sql_select($mailSql);
		foreach($mailSqlRes as $rows){
			if($rows[EMAIL]){$mailToArr[]=$rows[EMAIL];}
		}
		
		
		if($mail!=''){$mailToArr[]=$mail;}
		$to=implode(',',$mailToArr);
 		$subject=" Service Booking Sheet ";
		$header=mailHeader();
		echo sendMailMailer( $to, $subject, $mailBody, $from_mail,$att_file_arr );
		
	}
	else{
		echo $html;
	}
	exit();


}

if($action=="show_trim_booking_report4")
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$company_address_arr=return_library_array( "select id,city from   lib_company",'id','city');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
    $style_ref=array();
    $job_no=array();
    $job_no_in=array();
    $nameArray_buyer=sql_select( "select  a.style_ref_no, a.job_no from wo_po_details_master a, wo_booking_dtls b where a.job_no=b.job_no and b.booking_no=$txt_booking_no and b.status_active =1 and b.is_deleted=0");
        foreach ($nameArray_buyer as $result_buy){
			$style_ref[$result_buy[csf('job_no')]]=$result_buy[csf('style_ref_no')];
            $job_no[$result_buy[csf('job_no')]]=$result_buy[csf('job_no')];
            $job_no_in[$result_buy[csf('job_no')]]="'".$result_buy[csf('job_no')]."'";
			
		}
       
    $po_no=array();
    $nameArray_job=sql_select( "select  b.job_no_mst,b.id, b.po_number from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no=$txt_booking_no and  a.status_active =1 and a.is_deleted=0 group by b.job_no_mst,b.id, b.po_number");
        foreach ($nameArray_job as $result_job){
			$po_no[$result_job[csf('job_no_mst')]][$result_job[csf('id')]]=$result_job[csf('po_number')];
		}
	ob_start();
	?>
	<div style="width:1333px" align="center">
       <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black">
           <tr>
               <td width="100">
               <img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
               </td>
               <td width="1250">
                    <table width="100%" cellpadding="0" cellspacing="0" >
                        <tr>
                            <td align="center" style="font-size:20px;">
                              <?php echo $company_library[$cbo_company_name]; ?>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:14px">
                            <?
                            $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
                            foreach ($nameArray as $result)
                            {
                            ?>
                                            Plot No: <? echo $result[csf('plot_no')]; ?>
                                            Level No: <? echo $result[csf('level_no')]?>
                                            Road No: <? echo $result[csf('road_no')]; ?>
                                            Block No: <? echo $result[csf('block_no')];?>
                                            City No: <? echo $result[csf('city')];?>
                                            Zip Code: <? echo $result[csf('zip_code')]; ?>
                                            Province No: <?php echo $result[csf('province')]; ?>
                                            Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br>
                                            Email Address: <? echo $result[csf('email')];?>
                                            Website No: <? echo $result[csf('website')];
                            }
                            ?>
                               </td>
                            </tr>
                            <tr>
                            <td align="center" style="font-size:20px">
                            <strong>Service Booking Sheet</strong>
                             </td>
                            </tr>
                      </table>
                </td>
            </tr>
       </table>
		<?
		$booking_grand_total=0;
		//$job_no="";
		$currency_id="";
		$nameArray_job=sql_select( "select distinct b.job_no,a.buyer_id from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_no=$txt_booking_no");

	    $buyer_name=$nameArray_job[0][csf('buyer_id')];
        // foreach ($nameArray_job as $result_job)
        // {
		// 	$job_no.=$result_job[csf('job_no')].",";
		// }

        
        $po_id='';

		$nameArray_job=sql_select( "select b.id, b.po_number from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no=$txt_booking_no group by b.id, b.po_number");

        foreach ($nameArray_job as $result_job)
        {
			$po_id.=$result_job[csf('id')].",";
		}


        $nameArray=sql_select( "select a.booking_no,a.booking_date,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.source,a.pay_mode  from wo_booking_mst a where  a.booking_no=$txt_booking_no and a.supplier_id=$hidden_supplier_id and a.id=$booking_mst_id");
		

		$po_id=substr($po_id,0,-1);
		$article_number_arr=return_library_array( "select po_break_down_id,article_number from wo_po_color_size_breakdown where po_break_down_id in(".$po_id.")", "po_break_down_id", "article_number");

        foreach ($nameArray as $result)
        {
        ?>
       <table width="100%" style="border:1px solid black">
            <tr>
                <td width="100" style="font-size:12px"><b>Booking No </b>   </td>
                <td width="110">:&nbsp;<? echo $result[csf('booking_no')];?> </td>
                <td width="100" style="font-size:12px"><b>Booking Date</b></td>
                <td width="110">:&nbsp;<? echo change_date_format($result[csf('booking_date')],'dd-mm-yyyy','-');?>&nbsp;&nbsp;&nbsp;</td>
                <td width="110" align="center"><b>IMAGE</b></td>

            </tr>
            <tr>
                <td width="100"><span style="font-size:12px"><b>Delivery Date</b></span></td>
                <td width="110">:&nbsp;<? echo change_date_format($result[csf('delivery_date')],'dd-mm-yyyy','-');?></td>
                <td  width="100" style="font-size:12px"><b>Attention</b></td>
                <td  width="110" >:&nbsp;<? echo $result[csf('attention')]; ?></td>
                <td  width="110" rowspan="6" align="center">

                <?
			$nameArray_imge =sql_select("SELECT image_location,real_file_name FROM common_photo_library where master_tble_id='".$result[csf('booking_no')]."' and file_type=1");
			?>

            	<table width="310">
                <tr>
                <?
				$img_counter = 0;
                foreach($nameArray_imge as $result_imge)
				{
				    if($path=="")
                    {
                    $path='../../';
                    }

					?>
					<td>
                        <img src="<? echo $path.$result_imge[csf('image_location')]; ?>" width="90" height="100" border="2" />
                       <?
					   $img=explode('.',$result_imge[csf('real_file_name')]);
					   echo $img[0];
					   ?>
					</td>
					<?

					$img_counter++;
				}
				?>
                </tr>
           </table>
                </td>
            </tr>
            <tr>
                <td width="100" style="font-size:12px"><b>Currency</b></td>
                <td width="110">:&nbsp;<? $currency_id =$result[csf('currency_id')]; echo $currency[$result[csf('currency_id')]]; ?></td>
                <td  width="100" style="font-size:12px"><b>Conversion Rate</b></td>
                <td  width="110" >:&nbsp;<? echo $result[csf('exchange_rate')]; ?></td>

            </tr>
             <tr>
                <td  width="100" style="font-size:12px"><b>Source</b></td>
                <td  width="110" >:&nbsp;<? echo $source[$result[csf('source')]]; ?></td>
                <td width="100" style="font-size:12px"><b>Supplier Name</b>   </td>
                <td width="110">:&nbsp;<?
                 if($result[csf("pay_mode")]!=3 && $result[csf("pay_mode")]!=5)
				{
					echo $supplier_name_arr[$result[csf('supplier_id')]];
				}
				else
				{
					echo $company_library[$result[csf('supplier_id')]];
				}
                ?>    </td>
            </tr>
             <tr>
                <td width="100" style="font-size:12px"><b>Supplier Address</b></td>
               	<td width="110" colspan="3">:&nbsp;<?
				if($result[csf("pay_mode")]!=3 && $result[csf("pay_mode")]!=5)
				{
					echo $supplier_address_arr[$result[csf('supplier_id')]];
				}
				else
				{
					echo $company_address_arr[$result[csf('supplier_id')]];
				}               	?></td>

            </tr>
            <tr>
                <td width="100" style="font-size:12px"><b>Buyer Name</b>   </td>
                <td width="110" >:&nbsp;
				<?
				echo $buyer_name_arr[$buyer_name];
				?>
                </td>
            </tr>
            
        </table>
        <br/>
		<?
        }
        ?>
          <!--==============================================AS PER GMTS COLOR START=========================================  -->
		<?

            $fabric_description_array=array();
            $wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select id,fabric_description,cons_process from wo_pre_cost_fab_conv_cost_dtls  where job_no in(".implode(",",$job_no_in).")");
            foreach( $wo_pre_cost_fab_conv_cost_dtls_id as $row_wo_pre_cost_fab_conv_cost_dtls_id){
                if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]!=0){
                    $fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description,gsm_weight,uom from  wo_pre_cost_fabric_cost_dtls  where  id='".$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]."'");
                    list($fabric_description_row)=$fabric_description;

                    $fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]=$body_part[$fabric_description_row[csf("body_part_id")]].', '.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")].', '.$fabric_description_row[csf("gsm_weight")];
                        $fabric_description_uom_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]=$unit_of_measurement[$fabric_description_row[csf("uom")]];
                }
            }

        $nameArray_item=sql_select( "select distinct process,description from wo_booking_dtls  where booking_no=$txt_booking_no and wo_qnty !=0 and sensitivity=1 and status_active=1 ");//and sensitivity=1
        $nameArray_color=sql_select( "select distinct fabric_color_id from wo_booking_dtls   where  booking_no=$txt_booking_no and wo_qnty !=0 and sensitivity=1 and status_active=1 "); //and sensitivity=1
        $nameArray_job_po=sql_select( "select job_no from wo_booking_dtls  where booking_no=$txt_booking_no and status_active =1 and is_deleted=0 group by job_no order by job_no ");
        foreach($nameArray_job_po as $nameArray_job_po_row){
            $job_no=$nameArray_job_po_row[csf('job_no')];
        } 
       

       if(count($nameArray_color)>0)
		{
		foreach($nameArray_item as $result_item)
        {
        ?>

        <table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="6" align="">
                <strong><? echo "Job NO:".$nameArray_job_po_row[csf('job_no')];?> <? echo "&nbsp;Style No:".$style_ref[$nameArray_job_po_row[csf('job_no')]];?> &nbsp; PO No:<?  echo implode(",",$po_no[$nameArray_job_po_row[csf('job_no')]]);?></strong><br>
                <strong><? echo "Fabrication:".rtrim($fabric_description_array[$result_item[csf('description')]],", "); ?></strong><br/>
                <strong><? echo "Process:".$conversion_cost_head_array[$result_item[csf('process')]]; ?> </strong>
                 <strong>As Per Gmts Color</strong>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>GMT Color</strong> </td>
                <td style="border:1px solid black"><strong>Item Color</strong> </td>
                <td style="border:1px solid black" align="center"><strong>Wo Qty (Kg)</strong></td>
                <td style="border:1px solid black" align="center"><strong>Fin Dia</strong></td>
                <? if($show_comment==1) {?>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
                <? }?>
            </tr>
            <?
			 $total_amount_as_per_gmts_color=0;
            $nameArray_item_description=sql_select( "select  po_break_down_id,fabric_color_id,gmts_color_id,description,rate,artwork_no,dia_width,sum(wo_qnty) as cons from wo_booking_dtls  where booking_no=$txt_booking_no  and process=".$result_item[csf('process')]." and description=".$result_item[csf('description')]." and wo_qnty !=0 and sensitivity=1 and status_active=1  group by po_break_down_id,fabric_color_id,gmts_color_id,description,rate,artwork_no,dia_width");//and sensitivity=1
                foreach($nameArray_item_description as $result_itemdescription)
                {

                ?>
            <tr>
                <td style="border:1px solid black"><? echo$color_library[$result_itemdescription[csf('gmts_color_id')]]; ?>  </td>
                <td style="border:1px solid black"><? echo$color_library[$result_itemdescription[csf('fabric_color_id')]]; ?>  </td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('cons')],4); ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('dia_width')]; ?></td>
                <? if($show_comment==1) {?>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <?
                $amount_as_per_gmts_color = $result_itemdescription[csf('cons')]*$result_itemdescription[csf('rate')];
				echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
                <? }?>
            </tr>
				<?
                }
                ?>
            <tr>
            <? if($show_comment==1) {?>
                <td style="border:1px solid black;  text-align:right" colspan="5"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right">
                <?
                echo number_format($total_amount_as_per_gmts_color,4);
                $booking_grand_total+=$total_amount_as_per_gmts_color;
                ?>
                </td>
                <? }?>
            </tr>

        </table>
        &nbsp;
        <br/>
        <?
		}
		}
		?>
        <!--==============================================AS PER GMTS COLOR END=========================================  -->
        <?

        //=================================================
        $nameArray_item=sql_select( "select distinct process,description from wo_booking_dtls  where booking_no=$txt_booking_no and wo_qnty !=0 and sensitivity=3 and status_active=1 ");//and sensitivity=1
        $nameArray_color=sql_select( "select distinct fabric_color_id from wo_booking_dtls   where  booking_no=$txt_booking_no and wo_qnty !=0 and sensitivity=3 and status_active=1 "); //and sensitivity=1

       if(count($nameArray_color)>0)
		{
		foreach($nameArray_item as $result_item)
        {
        ?>

        <table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="6" align="">
                <strong><? echo "Job NO:".$nameArray_job_po_row[csf('job_no')];?> <? echo "&nbsp;Style No:".$style_ref[$nameArray_job_po_row[csf('job_no')]];?> &nbsp; PO No:<?  echo implode(",",$po_no[$nameArray_job_po_row[csf('job_no')]]);?></strong><br>
                <strong><? echo "Fabrication:".rtrim($fabric_description_array[$result_item[csf('description')]],", "); ?> </strong><br/>
                <strong><? echo "Process:".$conversion_cost_head_array[$result_item[csf('process')]]; ?> </strong>
                 <strong>As Per Constrast Color</strong>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>GMT Color</strong> </td>
                <td style="border:1px solid black"><strong>Item Color</strong> </td>
                <td style="border:1px solid black" align="center"><strong>Wo Qty (Kg)</strong></td>
                <td style="border:1px solid black" align="center"><strong>Fin Dia</strong></td>
                <? if($show_comment==1) {?>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
                <? }?>
            </tr>
            <?
			 $total_constrast_color=0;
            $nameArray_item_description=sql_select( "select  po_break_down_id,fabric_color_id,gmts_color_id,description,rate,artwork_no,dia_width,sum(wo_qnty) as cons from wo_booking_dtls  where booking_no=$txt_booking_no and status_active=1  and process=".$result_item[csf('process')]." and description=".$result_item[csf('description')]." and wo_qnty !=0 and sensitivity=3  group by po_break_down_id,fabric_color_id,gmts_color_id,description,rate,artwork_no,dia_width");//and sensitivity=1
                foreach($nameArray_item_description as $result_itemdescription)
                {

                ?>
            <tr>
                <td style="border:1px solid black"><? echo$color_library[$result_itemdescription[csf('gmts_color_id')]]; ?>  </td>
                <td style="border:1px solid black"><? echo$color_library[$result_itemdescription[csf('fabric_color_id')]]; ?>  </td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('cons')],4); ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('dia_width')]; ?></td>
                <? if($show_comment==1) {?>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <?
                $amount_constrast_color = $result_itemdescription[csf('cons')]*$result_itemdescription[csf('rate')];
				echo number_format($amount_constrast_color,4);
                $total_constrast_color+=$amount_constrast_color;
                ?>
                </td>
                <? }?>
            </tr>
				<?
                }
                ?>
            <tr>
            <? if($show_comment==1) {?>
                <td style="border:1px solid black;  text-align:right" colspan="5"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right">
                <?
                echo number_format($total_constrast_color,4);
                $booking_grand_total+=$total_constrast_color;
                ?>
                </td>
                <? }?>
            </tr>

        </table>
        &nbsp;
        <br/>
        <?
		}
		}
		?>
        <!--==============================================Constrast COLOR END=========================================  -->
        <?
        //=================================================
        $nameArray_item=sql_select( "select distinct process,description from wo_booking_dtls  where booking_no=$txt_booking_no and wo_qnty !=0 and sensitivity=2 and status_active=1 ");//and sensitivity=1
        $nameArray_color=sql_select( "select distinct item_size from wo_booking_dtls   where  booking_no=$txt_booking_no and wo_qnty !=0 and sensitivity=2 and status_active=1 "); //and sensitivity=1

       if(count($nameArray_color)>0)
		{
		foreach($nameArray_item as $result_item)
        {
        ?>

        <table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="6" align="">
                <strong><? echo "Job NO:".$nameArray_job_po_row[csf('job_no')];?> <? echo "&nbsp;Style No:".$style_ref[$nameArray_job_po_row[csf('job_no')]];?> &nbsp; PO No:<?  echo implode(",",$po_no[$nameArray_job_po_row[csf('job_no')]]);?></strong><br>
                <strong><? echo "Fabrication:".rtrim($fabric_description_array[$result_item[csf('description')]],", "); ?> </strong><br/>
                <strong><? echo "Process:".$conversion_cost_head_array[$result_item[csf('process')]]; ?> </strong>
                 <strong> As Per Size Sensitive</strong>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>GMT Size</strong> </td>
                <td style="border:1px solid black"><strong>Item Size</strong> </td>
                <td style="border:1px solid black" align="center"><strong>Wo Qty (Kg)</strong></td>
                <td style="border:1px solid black" align="center"><strong>Fin Dia</strong></td>
                <? if($show_comment==1) {?>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
                <? }?>
            </tr>
            <?
			 $total_amount_size_sensitive=0;
            $nameArray_item_description=sql_select( "select  po_break_down_id,item_size,gmts_size,description,rate,artwork_no,dia_width,sum(wo_qnty) as cons from wo_booking_dtls  where booking_no=$txt_booking_no  and process=".$result_item[csf('process')]." and description=".$result_item[csf('description')]." and status_active=1  and wo_qnty !=0 and sensitivity=2  group by po_break_down_id,item_size,gmts_size,description,rate,artwork_no,dia_width");//and sensitivity=1
                foreach($nameArray_item_description as $result_itemdescription)
                {

                ?>
            <tr>         
                <td style="border:1px solid black"><? echo $size_library[$result_itemdescription[csf('gmts_size')]]; ?>  </td>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('item_size')]; ?>  </td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('cons')],4); ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('dia_width')]; ?></td>
                <? if($show_comment==1) {?>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <?
                $amount_size_sensitive = $result_itemdescription[csf('cons')]*$result_itemdescription[csf('rate')];
				echo number_format($amount_size_sensitive,4);
                $total_amount_size_sensitive+=$amount_size_sensitive;
                ?>
                </td>
                <? }?>
            </tr>
				<?
                }
                ?>
            <tr>
            <? if($show_comment==1) {?>
                <td style="border:1px solid black;  text-align:right" colspan="5"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right">
                <?
                echo number_format($total_amount_size_sensitive,4);
                $booking_grand_total+=$total_amount_size_sensitive;
                ?>
                </td>
                <? }?>
            </tr>

        </table>
        &nbsp;
        <br/>
        <?
		}
		}
		?>
        <!--==============================================Size Sensitive END=========================================  -->

         <?
        //=================================================
        $nameArray_item=sql_select( "select distinct process,description from wo_booking_dtls  where booking_no=$txt_booking_no and wo_qnty !=0 and sensitivity=4 and status_active=1 ");//and sensitivity=1
        $nameArray_color=sql_select( "select distinct item_size from wo_booking_dtls   where  booking_no=$txt_booking_no and wo_qnty !=0 and sensitivity=4 and status_active=1 "); //and sensitivity=1

       if(count($nameArray_color)>0)
		{
		foreach($nameArray_item as $result_item)
        {
        ?>

        <table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="8" align="">
                <strong><? echo "Job NO:".$nameArray_job_po_row[csf('job_no')];?> <? echo "&nbsp;Style No:".$style_ref[$nameArray_job_po_row[csf('job_no')]];?> &nbsp; PO No:<?  echo implode(",",$po_no[$nameArray_job_po_row[csf('job_no')]]);?></strong><br>
                <strong><? echo "Fabrication:".rtrim($fabric_description_array[$result_item[csf('description')]],", "); ?> </strong><br/>
                <strong><? echo "Process:".$conversion_cost_head_array[$result_item[csf('process')]]; ?> </strong>
                 <strong> As Per Color & Size Sensitive</strong>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>GMT Color</strong> </td>
                <td style="border:1px solid black"><strong>Item Color</strong> </td>
                <td style="border:1px solid black"><strong>GMT Size</strong> </td>
                <td style="border:1px solid black"><strong>Item Size</strong> </td>
                <td style="border:1px solid black" align="center"><strong>Wo Qty (Kg)</strong></td>
                <td style="border:1px solid black" align="center"><strong>Fin Dia</strong></td>
                <? if($show_comment==1) {?>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
                <? }?>
            </tr>
            <?
			 $total_amount_color_and_size_sensitive=0;
            $nameArray_item_description=sql_select( "select  po_break_down_id,fabric_color_id,gmts_color_id,item_size,gmts_size,description,rate,artwork_no,dia_width,sum(wo_qnty) as cons from wo_booking_dtls  where booking_no=$txt_booking_no and status_active=1   and process=".$result_item[csf('process')]." and description=".$result_item[csf('description')]." and wo_qnty !=0 and sensitivity=4  group by po_break_down_id,fabric_color_id,gmts_color_id,item_size,gmts_size,description,rate,artwork_no,dia_width");//and sensitivity=1
                foreach($nameArray_item_description as $result_itemdescription)
                {

                ?>
            <tr>
                <td style="border:1px solid black"><? echo $color_library[$result_itemdescription[csf('gmts_color_id')]]; ?>  </td>
                <td style="border:1px solid black"><? echo $color_library[$result_itemdescription[csf('fabric_color_id')]]; ?>  </td>
                <td style="border:1px solid black"><? echo $size_library[$result_itemdescription[csf('gmts_size')]]; ?>  </td>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('item_size')]; ?>  </td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('cons')],4); ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('dia_width')]; ?></td>
                <? if($show_comment==1) {?>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <?
                $amount_color_and_size_sensitive = $result_itemdescription[csf('cons')]*$result_itemdescription[csf('rate')];
				echo number_format($amount_color_and_size_sensitive,4);
                $total_amount_color_and_size_sensitive+=$amount_size_sensitive;
        ?>
                </td>
                <? }?>
            </tr>
				<?
                }
                ?>
            <tr>
            <? if($show_comment==1) {?>
                <td style="border:1px solid black;  text-align:right" colspan="7"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right">
                <?
                echo number_format($total_amount_color_and_size_sensitive,4);
                $booking_grand_total+=$total_amount_color_and_size_sensitive;
                ?>
                </td>
                <? }?>
            </tr>

        </table>
        &nbsp;
        <br/>
        <?
		}
		}
		?>
        <!--==============================================Size Sensitive END=========================================  -->

        <!--==============================================NO NENSITIBITY START=========================================  -->
		<?
        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=0 and wo_qnty !=0 and status_active=1 ");
		$nameArray_color= array();
		if(count($nameArray_item)>0)
		{
        ?>
        <table border="0" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" >
            <tr>
                <td colspan="7" align="">
                <strong><? echo "Job NO:".$nameArray_job_po_row[csf('job_no')];?> <? echo "&nbsp;Style No:".$style_ref[$nameArray_job_po_row[csf('job_no')]];?> &nbsp; PO No:<?  echo implode(",",$po_no[$nameArray_job_po_row[csf('job_no')]]);?></strong><br>
                <strong> As Per No Sensitivity</strong>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Service Type</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong></strong> </td>
                <td align="center" style="border:1px solid black"><strong> Qnty</strong></td>
                <td style="border:1px solid black" align="center"><strong>Fin Dia</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <? if($show_comment==1) {?>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
                <? }?>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
				$i++;
            $nameArray_item_description=sql_select( "select distinct description,rate,uom,dia_width from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=0 and process=".$result_item[csf('process')]." and wo_qnty !=0 and status_active=1 ");
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $i; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $conversion_cost_head_array[$result_item[csf('process')]]; ?>
                </td>
                <?
                $color_tatal=0;
                $total_amount_as_per_gmts_color=0;
                foreach($nameArray_item_description as $result_itemdescription)
                {
                $item_desctiption_total=0;
                ?>
                <td style="border:1px solid black"><? echo $fabric_description_array[$result_itemdescription[csf('description')]]; ?> </td>
                <td style="border:1px solid black"><? //echo $result_itemdescription['brand_supplier']; ?>Booking Qnty  </td>
                <?
                $nameArray_color_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls where    booking_no=$txt_booking_no and sensitivity=0 and process=". $result_item[csf('process')]." and  description='". $result_itemdescription[csf('description')]."'and rate='". $result_itemdescription[csf('rate')]."' and uom='". $result_itemdescription[csf('uom')]."' and dia_width='". $result_itemdescription[csf('dia_width')]."' and status_active=1 ");
                foreach($nameArray_color_size_qnty as $result_color_size_qnty)
                {
                ?>
                <td style="border:1px solid black; text-align:right">
                <?
                if($result_color_size_qnty[csf('cons')]!= "")
                {
                echo number_format($result_color_size_qnty[csf('cons')],2);
                $item_desctiption_total += $result_color_size_qnty[csf('cons')] ;
                $color_tatal+=$result_color_size_qnty[csf('cons')];
                }
                else echo "";
                ?>
                </td>
                <?
                }
                ?>
                <td style="border:1px solid black; text-align:center "><? echo $result_itemdescription[csf('dia_width')]; ?></td>
                <td style="border:1px solid black; text-align:center "><? echo $unit_of_measurement[$result_itemdescription[csf('uom')]]; ?></td>
                <? if($show_comment==1) {?>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <?
                $amount_as_per_gmts_color = $item_desctiption_total*  $result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
                <? }?>
            </tr>
            <?
            }
            ?>
            <tr>
            <? if($show_comment==1) {?>
                <td style="border:1px solid black;  text-align:right" colspan="2"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right">
                <?
                if($color_tatal !='')
                {
                echo number_format($color_tatal,2);
                }
                ?>
                </td>
                <td style="border:1px solid black;"></td>
                <td style="border:1px solid black;"></td>
                <td style="border:1px solid black; text-align:right"></td>
                <td style="border:1px solid black; text-align:right">
                <?
                echo number_format($total_amount_as_per_gmts_color,4);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
                <? }?>
            </tr>
            <?
            }
            ?>
            <tr>
            <? if($show_comment==1) {?>
                <td align="right" style="border:1px solid black"  colspan="8"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,4); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
                <? }?>
            </tr>
        </table>
        <?
		//print_r($color_tatal);
		}
		?>
        <!--==============================================NO NENSITIBITY END=========================================  -->
       &nbsp;
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
       <table  width="100%" class="rpt_table" style="border:1px solid black;"   border="1" cellpadding="0" cellspacing="0" rules="all">
       <? if($show_comment==1) {?>
       <tr style="border:1px solid black;">
                <th width="30%" style="border:1px solid black; text-align:left">Total Booking Amount</th>
                <td width="70%" style="border:1px solid black; text-align:left"><? echo number_format($booking_grand_total,2);?></td>
            </tr>
            <tr style="border:1px solid black;">
                <th width="30%" style="border:1px solid black; text-align:left">Total Booking Amount (in word)</th>
                <td width="70%" style="border:1px solid black; text-align:left""><? echo number_to_words($booking_grand_total,$mcurrency, $dcurrency);?></td>
            </tr>
            <? }?>
       </table>
          &nbsp;
        <table  width="100%" class="rpt_table" style="border:1px solid black;"   border="1" cellpadding="0" cellspacing="0" rules="all">
        <thead>
            <tr style="border:1px solid black;">
                <th width="3%" style="border:1px solid black;">Sl</th><th width="97%" style="border:1px solid black;">Spacial Instruction</th>
            </tr>
        </thead>
        <tbody>
        <?
        $data_array=sql_select("select id, terms from  wo_booking_terms_condition where booking_no=$txt_booking_no");// quotation_id='$data'
        if ( count($data_array)>0)
        {
            $i=0;
            foreach( $data_array as $row )
            {
                $i++;
                ?>
                    <tr id="settr_1" align="" style="border:1px solid black;">
                        <td style="border:1px solid black;">
                        <? echo $i;?>
                        </td>
                        <td style="border:1px solid black;">
                        <? echo $row[csf('terms')]; ?>
                        </td>
                    </tr>
                <?
            }
        }
        else
        {
			$i=0;
        $data_array=sql_select("select id, terms from  lib_terms_condition where is_default=1 and page_id=176");// quotation_id='$data'
        foreach( $data_array as $row )
            {
                $i++;
        ?>
        <tr id="settr_1" align="" style="border:1px solid black;">
                        <td style="border:1px solid black;">
                        <? echo $i;?>
                        </td>
                        <td style="border:1px solid black;">
                        <? echo $row[csf('terms')]; ?>
                        </td>

                    </tr>
        <?
            }
        }
        ?>
    </tbody>
    </table>

         <br/>

		 <?
            echo signature_table(53, $cbo_company_name, "1313px");
			echo "****".custom_file_name($txt_booking_no,$style_sting,$job_no);
         ?>
    </div>
<?
	$html = ob_get_contents();
	ob_clean();
	list($is_mail_send,$mail)=explode('___',$mail_send_data);
	if($is_mail_send==1){
		require_once('../../../mailer/class.phpmailer.php');
		require_once('../../../auto_mail/setting/mail_setting.php');
		$mailBody = preg_replace("/<img[^>]+\>/i", " ", $html); 
			
		$mailToArr=array();
		$mailSql = "select b.EMAIL  from wo_booking_mst a,LIB_SUPPLIER b where b.id=a.supplier_id and a.booking_no=$txt_booking_no";
		//echo $mailSql;die;
		$mailSqlRes=sql_select($mailSql);
		foreach($mailSqlRes as $rows){
			if($rows[EMAIL]){$mailToArr[]=$rows[EMAIL];}
		}
		
		
		$mailSql = "SELECT c.email_address as EMAIL FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=97 and b.mail_user_setup_id=c.id  and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
		//echo $mailSql;die;
		$mailSqlRes=sql_select($mailSql);
		foreach($mailSqlRes as $rows){
			if($rows[EMAIL]){$mailToArr[]=$rows[EMAIL];}
		}
		
		
		if($mail!=''){$mailToArr[]=$mail;}
		$to=implode(',',$mailToArr);
 		$subject=" Service Booking Sheet ";
		$header=mailHeader();
		echo sendMailMailer( $to, $subject, $mailBody, $from_mail,$att_file_arr );
		
	}
	else{
		echo $html;
	}
	exit();


}

if($action=="show_trim_booking_bpkw_report") //For BPKW
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library  where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$company_address_arr=return_library_array( "select id,city from   lib_company",'id','city');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	//$po_qnty_tot=return_field_value( "style_ref_no", "wo_po_details_master","job_no in(".str_replace("'","",$txt_order_no_id).")");
	$job_no="";$style_ref="";
	$nameArray_job=sql_select( "select distinct b.job_no,a.buyer_id,c.style_ref_no as style  from wo_booking_mst a, wo_booking_dtls b,wo_po_details_master c  where a.booking_no=b.booking_no and c.job_no=b.job_no and a.booking_no=$txt_booking_no and b.status_active=1");
		$buyer_name=$nameArray_job[0][csf('buyer_id')];
        foreach ($nameArray_job as $result_job)
        {
			$job_no.="'".$result_job[csf('job_no')]."'".",";
			$style_ref.=$result_job[csf('style')].",";
			$job_num=$result_job[csf('job_no')];

		}
	$po_no=""; $po_ids=""; $interRef="";
	$nameArray_job=sql_select( "select distinct b.id, b.po_number, b.grouping from wo_booking_dtls a, wo_po_break_down b  where a.po_break_down_id=b.id  and a.booking_no=$txt_booking_no and a.status_active=1 and b.status_active=1");
	foreach ($nameArray_job as $result_job)
	{
		$po_no.=$result_job[csf('po_number')].",";
		$interRef.=$result_job[csf('grouping')].",";
		if($po_ids!="") $po_ids.=",".$result_job[csf('id')];else $po_ids=$result_job[csf('id')];
	}

	$show_rate=str_replace("'","",$show_rate);
	//echo $show_rate;
	if($show_rate==1)
	{
		$col_span=2;
		$hide_show="display:none";
	}
	else
	{
		$col_span=0;
		$hide_show="";
	}
	$job_nos=str_replace("'","",$job_no);
	$copy_no=array(1,2); //for Dynamic Copy here
	ob_start();
	foreach($copy_no as $cid)
	{
	?>
	<div style="width:1333px" align="center">
        <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black">
            <tr>
                <td width="100">
                	<img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
                </td>
                <td width="1000">
                    <table width="100%" cellpadding="0" cellspacing="0" >
                        <tr>
                            <td align="center" style="font-size:18px;"><?=$company_library[$cbo_company_name]; ?>
                            <span style="font-size:x-large; margin-left:10px !important;"> <? echo $cid;?><sup><?php if ($cid == 1) {echo 'st';} elseif ($cid == 2) {echo 'nd';} else {echo 'rd';}?></sup> Copy</span>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:14px">
                            <?
                            $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
                            foreach ($nameArray as $result)
                            {
								?>
								Plot No: <? echo $result[csf('plot_no')]; ?>
								Level No: <? echo $result[csf('level_no')]?>
								Road No: <? echo $result[csf('road_no')]; ?>
								Block No: <? echo $result[csf('block_no')];?>
								City No: <? echo $result[csf('city')];?>
								Zip Code: <? echo $result[csf('zip_code')]; ?>
								Province No: <?php echo $result[csf('province')]; ?>
								Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br>
								Email Address: <? echo $result[csf('email')];?>
								Website No: <? echo $result[csf('website')];
                            }
                            ?>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:16px">
                            	<strong>Service Booking Sheet</strong>
                            </td>
                        </tr>
                    </table>
                </td>
                <td width="100" style="font-size:18px">
                	<strong> Style Ref: &nbsp; </strong>
                </td>
                <td width="250" style="font-size:18px">
                	<strong><?=$style_ref=rtrim($style_ref,','); ?> </strong>
                </td>
            </tr>
        </table>
		<?
		$booking_grand_total=0;
		//$job_no="";
		$currency_id="";

        $nameArray=sql_select( "select a.booking_no,a.booking_date,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.source,a.pay_mode  from wo_booking_mst a where  a.booking_no=$txt_booking_no");
        foreach ($nameArray as $result)
        {
			$varcode_booking_no=$result[csf('booking_no')];
			?>
			<table width="100%" style="border:1px solid black">
                <tr>
                	<td colspan="6" valign="top"></td>
                </tr>
                <tr>
                    <td width="100" style="font-size:12px"><b>Booking No </b>   </td>
                    <td width="110">:&nbsp;<? echo $result[csf('booking_no')];?> </td>
                    <td width="100" style="font-size:12px"><b>Booking Date</b></td>
                    <td width="110">:&nbsp;<? echo change_date_format($result[csf('booking_date')],'dd-mm-yyyy','-');?>&nbsp;&nbsp;&nbsp;</td>
                    <td width="100"><span style="font-size:12px"><b>Delivery Date</b></span></td>
                    <td width="110">:&nbsp;<? echo change_date_format($result[csf('delivery_date')],'dd-mm-yyyy','-');?></td>
                </tr>
                <tr>
                    <td width="100" style="font-size:12px"><b>Currency</b></td>
                    <td width="110">:&nbsp;<? $currency_id=$result[csf('currency_id')];echo $currency[$result[csf('currency_id')]]; ?></td>
                    <td  width="100" style="font-size:12px"><b>Conversion Rate</b></td>
                    <td  width="110" >:&nbsp;<? echo $result[csf('exchange_rate')]; ?></td>
                    <td  width="100" style="font-size:12px"><b>Source</b></td>
                    <td  width="110" >:&nbsp;<? echo $source[$result[csf('source')]]; ?></td>
                </tr>
                <tr>
                    <td width="100" style="font-size:12px"><b>Supplier Name</b>   </td>
                    <td width="110">:&nbsp;<?
                    if($result[csf("pay_mode")]!=3 && $result[csf("pay_mode")]!=5)
                    {
                    echo $supplier_name_arr[$result[csf('supplier_id')]];
                    }
                    else
                    {
                    echo $company_library[$result[csf('supplier_id')]];
                    }
                    ?>    </td>
                    <td width="100" style="font-size:12px"><b>Supplier Address</b></td>
                    <td width="110">:&nbsp;<?
                    if($result[csf("pay_mode")]!=3 && $result[csf("pay_mode")]!=5)
                    {
                    echo $supplier_address_arr[$result[csf('supplier_id')]];
                    }
                    else
                    {
                    echo $company_address_arr[$result[csf('supplier_id')]];
                    }
                    ?></td>
                    <td  width="100" style="font-size:12px"><b>Attention</b></td>
                    <td  width="110" >:&nbsp;<? echo $result[csf('attention')]; ?></td>
                </tr>
                <tr>
                    <td width="100" style="font-size:12px"><b>Job No</b>   </td>
                    <td width="110">:&nbsp;<?=$job_nos=rtrim($job_nos,','); ?></td>
                    <td width="110" style="font-size:12px"><b>PO No</b> </td>
                    <td  width="100" style="font-size:12px" >:&nbsp;<?
                    $po_nos= rtrim($po_no,',');
                    $po_nos=implode(", ",array_unique(explode(",",$po_nos)));
                    echo $po_nos;
                    ?> </td>
                    <td width="110" style="font-size:12px"><b>Buyer Name</b>   </td>
                    <td width="100" >:&nbsp;<?=$buyer_name_arr[$buyer_name]; ?></td>
                </tr>
                <tr>
                    <td width="100" style="font-size:12px"><b>Internal Ref.</b></td><!--ISD-21-01857-->
                    <td width="110" style="word-break:break-all">:&nbsp;<?=implode(",",array_filter(array_unique(explode(",",$interRef)))); ?></td>
                    <td width="110" style="font-size:12px"><b>&nbsp;</b> </td>
                    <td width="100" style="font-size:12px">&nbsp;</td>
                    <td width="110" style="font-size:12px"><b>&nbsp;</b></td>
                    <td width="100">&nbsp;</td>
                </tr>
			</table>
			<?
        }
        //-==============================================AS PER GMTS COLOR START=========================================  -->
		//========================================
		$fabric_description_array=array();
	    $wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select id,fabric_description,cons_process from wo_pre_cost_fab_conv_cost_dtls  where  job_no in(".rtrim($job_no,", ").")");
	foreach( $wo_pre_cost_fab_conv_cost_dtls_id as $row_wo_pre_cost_fab_conv_cost_dtls_id)
	{
		if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]!=0)
		{
			$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description,gsm_weight from  wo_pre_cost_fabric_cost_dtls  where  id='".$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]."'");//gsm_weight
			list($fabric_description_row)=$fabric_description;
			$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]=$body_part[$fabric_description_row[csf("body_part_id")]].', '.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")].', '.$fabric_description_row[csf("gsm_weight")];//.', '.$fabric_description_row[csf("gsm_weight")]
		}
		if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]==0)
		{
			//echo "select id,body_part_id,color_type_id,fabric_description from  wo_pre_cost_fabric_cost_dtls  where  job_no='$data'";
			$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description,gsm_weight from  wo_pre_cost_fabric_cost_dtls  where   job_no in(".rtrim($job_no,", ").")");//gsm_weight
			//list($fabric_description_row)=$fabric_description;
			foreach( $fabric_description as $fabric_description_row)
	        {
			$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]].=$body_part[$fabric_description_row[csf("body_part_id")]].', '.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")].", ".$fabric_description_row[csf("gsm_weight")];//.', '.$fabric_description_row[csf("gsm_weight")]

			//$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]="All Fabrics  ".$conversion_cost_head_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("cons_process")]];
			}
		}


	}
	//print_r($fabric_description_array);
	//=================================================
        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=1 and wo_qnty !=0 and is_deleted=0 and status_active=1" );

		 $nameArray_color2=sql_select( "select distinct fabric_color_id,gmts_color_id from wo_booking_dtls   where  booking_no=$txt_booking_no and sensitivity=1 and wo_qnty !=0 and is_deleted=0 and status_active=1");
		foreach( $nameArray_color2 as $row)
		{
			$fab_gmt_color_arr[$row[csf('fabric_color_id')]]=$row[csf('gmts_color_id')];
		}
		
		$only_color_size_qnty=sql_select( "select fabric_color_id, gmts_color_id, slength, dia_width, fin_gsm, rate, description, sum(wo_qnty) as cons from wo_booking_dtls where booking_no=$txt_booking_no and sensitivity=1 and status_active=1 and is_deleted =0 $process_cond group by fabric_color_id, gmts_color_id, slength, dia_width, fin_gsm, rate, description");
		/*echo "select fabric_color_id, gmts_color_id, slength, dia_width, fin_gsm, rate, description, dia_width, fin_gsm, sum(wo_qnty) as cons from wo_booking_dtls where booking_no=$txt_booking_no and sensitivity=1 and status_active=1 and is_deleted =0 $process_cond group by fabric_color_id, gmts_color_id, slength, dia_width, fin_gsm, rate, description, dia_width, fin_gsm";*/
					
		foreach($only_color_size_qnty as $OnlyColorSizeQnty)
		{
		  if($OnlyColorSizeQnty[csf('cons')]){
		 	$arrOnlyColQty[$OnlyColorSizeQnty[csf('fabric_color_id')]][$OnlyColorSizeQnty[csf('gmts_color_id')]][$OnlyColorSizeQnty[csf('description')]][$OnlyColorSizeQnty[csf('slength')]][$OnlyColorSizeQnty[csf('dia_width')]][$OnlyColorSizeQnty[csf('fin_gsm')]][$OnlyColorSizeQnty[csf('rate')]]['cons']=$OnlyColorSizeQnty[csf('cons')];
		  }
		}

        $nameArray_color=sql_select( "select distinct fabric_color_id,gmts_color_id from wo_booking_dtls   where  booking_no=$txt_booking_no and sensitivity=1 and wo_qnty !=0 and is_deleted=0 and status_active=1");
		$nameArray_color_labdib=sql_select( "select  labdip_no from wo_booking_dtls   where  booking_no=$txt_booking_no and sensitivity=1 and wo_qnty !=0 and is_deleted=0 and status_active=1");

		if(count($nameArray_color)>0)
		{
        ?>
        <table border="0" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" >
            <tr>
                <td colspan="<? echo count($nameArray_color)+10; ?>" align="">
                <strong>As Per Garments Color</strong>
                </td>

              <!-- <td colspan="7">
               <table class="rpt_table"  cellpadding="0" width="100%" cellspacing="0">
               <tr>
               <td width="200"> <strong> Yarn Count: </strong></td> <td></td>
                <td width="200">  <strong>Lot :  </strong></td> <td></td>
                <td width="200"> <strong>Brand:  </strong></td> <td></td>
               </tr>
               </table>
               </td> -->

            </tr>
            <tr>
                <td style="border:1px solid black" rowspan="3"><strong>Sl</strong> </td>
                <td style="border:1px solid black" rowspan="3"><strong>Service Type</strong> </td>
                <td style="border:1px solid black" rowspan="3"><strong>Item Description</strong> </td>
                <td style="border:1px solid black" width="80" rowspan="3"><strong>Y.Count</strong> </td>
                <td style="border:1px solid black" width="60" rowspan="3"><strong>Lot</strong> </td>
                <td style="border:1px solid black" width="80" rowspan="3"><strong>Brand</strong> </td>
               <!-- <td style="border:1px solid black" rowspan="3"><strong>Labdip No</strong> </td>-->
                <td style="border:1px solid black"><strong>Item Color</strong> </td>
                <?
                foreach($nameArray_color  as $result_color)
                {	     ?>
                <td align="center" style="border:1px solid black"><strong><? echo $color_library[$result_color[csf('fabric_color_id')]];?></strong></td>
                <?	}    ?>
                <td style="border:1px solid black" align="center" rowspan="3"><strong>Total</strong></td>
                <td style="border:1px solid black" align="center" rowspan="3"><strong>M/C Dia</strong></td>
                <td style="border:1px solid black" align="center" rowspan="3"><strong>Fin Dia</strong></td>
                <td style="border:1px solid black" align="center" rowspan="3"><strong>Fin GSM</strong></td>
                <td style="border:1px solid black" align="center" rowspan="3"><strong>S/Length</strong></td>
                <td style="border:1px solid black" align="center" rowspan="3"><strong>UOM</strong></td>
                <td style="border:1px solid black;<? echo $hide_show;?>" align="center" rowspan="3"><strong>Rate</strong></td>
                <td style="border:1px solid black;<? echo $hide_show;?>" align="center" rowspan="3"><strong>Amount</strong></td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Gmts Color </strong> </td>
                <?
                foreach($nameArray_color  as $result_color)
                {
				$gmt_color=$color_library[$fab_gmt_color_arr[$result_color[csf('fabric_color_id')]]];
				 ?>
                <td align="center" style="border:1px solid black"><strong><? echo $gmt_color;//$color_library[$result_color[csf('fabric_color_id')]];?></strong></td>
                <?	}    ?>

            </tr>
             <tr>
                <td style="border:1px solid black"><strong>Labdip No</strong> </td>
                <?
                foreach($nameArray_color_labdib  as $row)
                {	     ?>
                <td align="center" style="border:1px solid black"><strong><? echo $row[csf('labdip_no')];?></strong></td>
                <?	}    ?>

            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
				$i++;
				if($result_item[csf('process')]=='' || $result_item[csf('process')]==0)
				 {
				 $process_id="";
				 }
				 else
				 {
					 $process_id="and process=".$result_item[csf('process')]."";
				 }
			if($db_type==2)
			{
			$group_con="listagg(cast(yarn_count as varchar2(4000)),',') within group (order by yarn_count) AS yarn_count,
		listagg(cast(lot_no as varchar2(4000)),',') within group (order by lot_no) AS lot_no,
		listagg(cast(brand as varchar2(4000)),',') within group (order by brand) AS brand";
			}
			else
			{
				$group_con="group_concat(yarn_count) AS yarn_count,
		group_concat(lot_no) AS lot_no,
		group_concat(brand) AS brand";
			}
			$nameArray_item_description=sql_select( "select distinct description, rate, uom, dia_width, mc_dia, fin_gsm, slength,$group_con from wo_booking_dtls where booking_no=$txt_booking_no and sensitivity=1 and wo_qnty !=0  $process_id and status_active=1 and is_deleted=0 group by description, rate, uom, dia_width, mc_dia, fin_gsm, slength");
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $i; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $conversion_cost_head_array[$result_item[csf('process')]]; ?>
                </td>
                <?
                $color_tatal=array();
                $total_amount_as_per_gmts_color=0;
                foreach($nameArray_item_description as $result_itemdescription)
                {
                $item_desctiption_total=0;
				$yarn_count=implode(',',array_unique(explode(",",$result_itemdescription[csf('yarn_count')])));
				$lot_no=implode(',',array_unique(explode(",",$result_itemdescription[csf('lot_no')])));
				$brand=implode(',',array_unique(explode(",",$result_itemdescription[csf('brand')])));
                ?>
                <td style="border:1px solid black"><? echo rtrim($fabric_description_array[$result_itemdescription[csf('description')]],", "); ?> </td>
                   <td style="border:1px solid black" width="80"><? echo $yarn_count; ?> </td>
                    <td style="border:1px solid black" width="60"><? echo $lot_no; ?> </td>
                    <td style="border:1px solid black" width="80"><? echo $brand; ?> </td>
                 <!-- <td style="border:1px solid black"><? //echo rtrim($result_itemdescription[csf('labdip_no')],", "); ?> </td>-->
                <td style="border:1px solid black"><? //echo $result_itemdescription['brand_supplier']; ?> Booking Qnty </td>
                <?
                foreach($nameArray_color  as $result_color)
                {
					/*if($result_item[csf('process')]=='')
					{
						$process_cond="";
					}
					else if($result_item[csf('process')]==0)
					{
						$process_cond="and process=0";
					}
					else
					{
						$process_cond="and process=". $result_item[csf('process')]."";
					}
					if($db_type==0)
					{
						if($result_itemdescription[csf('slength')]=='') $slenth_con="";else $slenth_con="and slength='".$result_itemdescription[csf('slength')]."'";
						if($result_itemdescription[csf('description')]=='') $description_con="";else $description_con="and description='".$result_itemdescription[csf('description')]."'";
						if($result_itemdescription[csf('uom')]=='') $uom_con="";else $uom_con="and uom='".$result_itemdescription[csf('uom')]."'";
						if($result_itemdescription[csf('fabric_color_id')]!=0) $fcolor_con="and fabric_color_id='".$result_itemdescription[csf('fabric_color_id')]."'";else $fcolor_con="";
						if($result_itemdescription[csf('fin_gsm')]=='') $fin_gsmcon="";else $fin_gsmcon="and fin_gsm='".$result_itemdescription[csf('fin_gsm')]."'";
					}
					else
					{
						if($result_itemdescription[csf('slength')]=='') $slenth_con="and slength is null";else $slenth_con="and slength='".$result_itemdescription[csf('slength')]."'";
						if($result_itemdescription[csf('description')]=='') $description_con="and description is null";else $description_con="and description='".$result_itemdescription[csf('description')]."'";
						if($result_itemdescription[csf('uom')]=='') $uom_con="and uom is null";else $uom_con="and uom='".$result_itemdescription[csf('uom')]."'";
						if($result_itemdescription[csf('fabric_color_id')]=='') $fcolor_con="and fabric_color_id is null";else $fcolor_con="and fabric_color_id='".$result_itemdescription[csf('fabric_color_id')]."'";
						if($result_itemdescription[csf('fin_gsm')]=='') $fin_gsmcon="and fin_gsm is null";else $fin_gsmcon="and fin_gsm='".$result_itemdescription[csf('fin_gsm')]."'";
					}*/
					/*foreach($nameArray_color_size_qnty as $result_color_size_qnty)
					{
						*/?>
						<td style="border:1px solid black; text-align:right">
						<?
						/*if($result_color_size_qnty[csf('cons')]!="")
						{*/
							$color_cons=0;
							$color_cons=$arrOnlyColQty[$result_color[csf('fabric_color_id')]][$result_color[csf('gmts_color_id')]][$result_itemdescription[csf('description')]][$result_itemdescription[csf('slength')]][$result_itemdescription[csf('dia_width')]][$result_itemdescription[csf('fin_gsm')]][$result_itemdescription[csf('rate')]]['cons'];
							//echo $result_color[csf('fabric_color_id')].'--'.$result_color[csf('gmts_color_id')].'--'.$result_itemdescription[csf('description')].'--'.$result_itemdescription[csf('slength')].'--'.$result_itemdescription[csf('dia_width')].'--'.$result_itemdescription[csf('fin_gsm')].'--'.$result_itemdescription[csf('rate')].'<br>';

							echo number_format($color_cons,2);

							$item_desctiption_total+=$color_cons;
							if (array_key_exists($result_color[csf('fabric_color_id')], $color_tatal))
							{
								$color_tatal[$result_color[csf('fabric_color_id')]]+=$color_cons;
							}
							else
							{
								$color_tatal[$result_color[csf('fabric_color_id')]]=$color_cons;
							}
						/*}
						else echo "";*/
						?>
						</td>
						<?
					//}
                }
                ?>

				<td style="border:1px solid black; text-align:right"><? echo number_format($item_desctiption_total,2); ?></td>
				<td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('mc_dia')]; ?></td>
				<td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('dia_width')]; ?></td>
				<td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('fin_gsm')]; ?></td>
				<td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('slength')]; ?></td>
				<td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$result_itemdescription[csf('uom')]]; ?></td>
				<td style="border:1px solid black; text-align:right;<? echo $hide_show;?>"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
				<td style="border:1px solid black; text-align:right;<? echo $hide_show;?>">
				<?
                $amount_as_per_gmts_color = $item_desctiption_total*  $result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="5"><strong> Item Total</strong></td>
                <?
                foreach($nameArray_color  as $result_color)
                {

                ?>
                <td style="border:1px solid black;  text-align:right">
                <?
                if($color_tatal[$result_color[fabric_color_id]] !='')
                {
                echo number_format($color_tatal[$result_color[fabric_color_id]],2);
                }
                ?>
                </td>
            <?
            }
            ?>
                <td style="border:1px solid black;  text-align:right"><? echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>

                <td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black; text-align:right;<? echo $hide_show;?>"><? //echo number_format(round($color_total),0); ?></td>
                <td style="border:1px solid black; text-align:right;<? echo $hide_show;?>">
                <?
                echo number_format($total_amount_as_per_gmts_color,4);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="<? echo count($nameArray_color)+(14-$col_span); ?>"><strong><? if($show_rate==1) echo '';else echo 'Total'; ?></strong></td>
                <td  style="border:1px solid black;  text-align:right"><?
				if($show_rate==1) echo '';
				else echo number_format($grand_total_as_per_gmts_color,4); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
            </tr>
        </table>
        <br/>
        <?
		}
		?>
        <!--==============================================AS PER GMTS COLOR END=========================================  -->

        <!--==============================================AS PER GMTS SIZE START=========================================  -->
		<?
		 //$nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=1");
		//echo "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=1";
       // $nameArray_color=sql_select( "select distinct fabric_color_id from wo_booking_dtls   where  booking_no=$txt_booking_no and sensitivity=1");


        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=2 and wo_qnty !=0");
        $nameArray_size=sql_select( "select distinct  item_size  as gmts_sizes from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=2 and wo_qnty !=0");
		if(count($nameArray_size)>0)
		{
        ?>

        <table border="0" align="left" cellpadding="0" width="100%" cellspacing="0" >
            <tr>
                <td colspan="<? echo count($nameArray_size)+11; ?>" align="">
                <strong>As Per Garments Size </strong>
                </td>

              <!-- <td colspan="8">
                   <table class="rpt_table"  cellpadding="0" width="100%" cellspacing="0">
                   <tr>
                   <td width="200"> <strong> Yarn Count: </strong></td> <td></td>
                    <td width="200">  <strong>Lot :  </strong></td> <td></td>
                    <td width="200"> <strong>Brand:  </strong></td> <td></td>
                   </tr>
                   </table>
               </td> -->
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Service Type</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black" width="80" ><strong>Y.Count</strong> </td>
                <td style="border:1px solid black" width="60"><strong>Lot</strong> </td>
                <td style="border:1px solid black" width="80"><strong>Brand</strong> </td>
                 <td style="border:1px solid black"><strong>Labdip No</strong> </td>
                <td style="border:1px solid black"><strong>Item size</strong> </td>
                <?
                foreach($nameArray_size  as $result_size)
                {	     ?>
                <td align="center" style="border:1px solid black"><strong><? echo $result_size[csf('gmts_sizes')];?></strong></td>
               <? }    ?>
                <td style="border:1px solid black" align="center"><strong>Total</strong></td>
                <td style="border:1px solid black" align="center"><strong>M/C Dia</strong></td>
                <td style="border:1px solid black" align="center"><strong>Fin Dia</strong></td>
                <td style="border:1px solid black" align="center"><strong>Fin GSM</strong></td>
                <td style="border:1px solid black" align="center"><strong>S/Length</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black;<? echo $hide_show;?>" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black;<? echo $hide_show;?>" align="center"><strong>Amount</strong></td>
            </tr>
            <?
			if($db_type==2)
			{
			$group_con="listagg(cast(yarn_count as varchar2(4000)),',') within group (order by yarn_count) AS yarn_count,
	listagg(cast(lot_no as varchar2(4000)),',') within group (order by lot_no) AS lot_no,
	listagg(cast(brand as varchar2(4000)),',') within group (order by brand) AS brand";
			}
			else
			{
				$group_con="group_concat(yarn_count) AS yarn_count,
		group_concat(lot_no) AS lot_no,
		group_concat(brand) AS brand";
			}
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
			$i++;
            $nameArray_item_description=sql_select( "select distinct description,rate,uom,dia_width,labdip_no,mc_dia,fin_gsm,slength,$group_con from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=2 and process=".$result_item[csf('process')]." and wo_qnty !=0 group by description,rate,uom,dia_width,labdip_no,mc_dia,fin_gsm,slength ");
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $i ; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $conversion_cost_head_array[$result_item[csf('process')]]; ?>
                </td>
                <?
                $color_tatal=array();
                $total_amount_as_per_gmts_color=0;
                foreach($nameArray_item_description as $result_itemdescription)
                {
                $item_desctiption_total=0;
				$yarn_count=implode(',',array_unique(explode(",",$result_itemdescription[csf('yarn_count')])));
				$lot_no=implode(',',array_unique(explode(",",$result_itemdescription[csf('lot_no')])));
				$brand=implode(',',array_unique(explode(",",$result_itemdescription[csf('brand')])));
                ?>
                <td style="border:1px solid black"><? echo $fabric_description_array[$result_itemdescription[csf('description')]]; ?> </td>
                <td style="border:1px solid black" width="80"><? echo $yarn_count; ?> </td>
                <td style="border:1px solid black" width="60"><? echo $lot_no; ?> </td>
                <td style="border:1px solid black" width="80"><? echo $brand; ?> </td>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('labdip_no')]; ?> </td>
                <td style="border:1px solid black"><? //echo $result_itemdescription['brand_supplier']; ?> Booking Qnty  </td>
                <?
				if($db_type==0)
					{
					if($result_itemdescription[csf('dia_width')]=='') $dia_width_con="";else $dia_width_con="and dia_width='".$result_itemdescription[csf('dia_width')]."'";
					if($result_itemdescription[csf('description')]=='') $description_con="";else $description_con="and description='".$result_itemdescription[csf('description')]."'";
					if($result_itemdescription[csf('uom')]=='') $uom_con="";else $uom_con="and uom='".$result_itemdescription[csf('uom')]."'";
					if($result_itemdescription[csf('fabric_color_id')]!=0) $fcolor_con="and fabric_color_id='".$result_itemdescription[csf('fabric_color_id')]."'";else $fcolor_con="";
					//if($result_itemdescription[csf('fabric_color_id')]!=0) $fcolor_con="and fabric_color_id='".$result_itemdescription[csf('fabric_color_id')]."'";else $fcolor_con="";
					if($result_itemdescription[csf('item_size')]=='') $item_size_con="";else $item_size_con="and item_size='".$result_itemdescription[csf('gmts_sizes')]."'";
					}
					else
					{
					if($result_itemdescription[csf('dia_width')]=='') $dia_width_con="and slength is null";else $dia_width_con="and dia_width='".$result_itemdescription[csf('dia_width')]."'";
					if($result_itemdescription[csf('description')]=='') $description_con="and description is null";else $description_con="and description='".$result_itemdescription[csf('description')]."'";
					if($result_itemdescription[csf('uom')]=='') $uom_con="and uom is null";else $uom_con="and uom='".$result_itemdescription[csf('uom')]."'";
					//if($result_itemdescription[csf('fabric_color_id')]=='') $fcolor_con="and fabric_color_id is null";else $fcolor_con="and fabric_color_id='".$result_itemdescription[csf('fabric_color_id')]."'";
					if($result_size[csf('gmts_sizes')]=='' || $result_size[csf('gmts_sizes')]==0) $item_size_con="and item_size is null";else $item_size_con="and item_size='".$result_size[csf('gmts_sizes')]."'";
					}
					foreach($nameArray_size  as $result_size)
					{
					/*$nameArray_size_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls   where   booking_no=$txt_booking_no and sensitivity=2 and process=". $result_item[csf('process')]." and description='". $result_itemdescription[csf('description')]."' and rate='". $result_itemdescription[csf('rate')]."' and uom='". $result_itemdescription[csf('uom')]."' and dia_width='". $result_itemdescription[csf('dia_width')]."' and item_size='".$result_size[csf('gmts_sizes')]."'"); */
					$nameArray_size_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls   where   booking_no=$txt_booking_no and sensitivity=2 and process=". $result_item[csf('process')]."  and rate='". $result_itemdescription[csf('rate')]."'  $description_con $uom_con  $dia_width_con $item_size_con");

					foreach($nameArray_size_size_qnty as $result_size_size_qnty)
					{
					?>
					<td style="border:1px solid black; text-align:right">
					<?
					if($result_size_size_qnty[csf('cons')]!= "")
					{
					echo number_format($result_size_size_qnty[csf('cons')],2);
					$item_desctiption_total += $result_size_size_qnty[csf('cons')] ;
					if (array_key_exists($result_size[csf('gmts_sizes')], $color_tatal))
					{
					$color_tatal[$result_size[csf('gmts_sizes')]]+=$result_size_size_qnty[csf('cons')];
					}
					else
					{
					$color_tatal[$result_size[csf('gmts_sizes')]]=$result_size_size_qnty[csf('cons')];
					}
					}
					else echo "";
                ?>
                </td>
                <?
                }
                }
                ?>

                <td style="border:1px solid black; text-align:right"><? echo number_format($item_desctiption_total,2); ?></td>
                 <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('mc_dia')]; ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('dia_width')]; ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('fin_gsm')]; ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('slength')]; ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$result_itemdescription[csf('uom')]]; ?></td>
                <td style="border:1px solid black; text-align:right;<? echo $hide_show;?>"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right;<? echo $hide_show;?>">
                <?
                $amount_as_per_gmts_color = $item_desctiption_total*$result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="6"><strong> Item Total</strong></td>
                <?
                foreach($nameArray_size  as $result_size)
                {

                ?>
                <td style="border:1px solid black;  text-align:right">
                <?
                if($color_tatal[$result_size[gmts_sizes]] !='')
                {
                echo number_format($color_tatal[$result_size[gmts_sizes]],2);
                }
                ?>
                </td>
            <?
            }
            ?>
                <td style="border:1px solid black;  text-align:right"><? echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
                 <td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black; text-align:right"><? //echo number_format(round($color_total),0); ?></td>
                <td style="border:1px solid black; text-align:right">
                <?
                echo number_format($total_amount_as_per_gmts_color,4);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="<? echo count($nameArray_size)+(15-$col_span); ?>"><strong><? if($show_rate==1) echo '';else echo 'Total'; ?></strong></td>
                <td  style="border:1px solid black;  text-align:right"><?
				if($show_rate==1) echo ' ';
				else echo number_format($grand_total_as_per_gmts_color,4); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
            </tr>
        </table>
        <br/>
        <?
		}
		?>
        <!--==============================================AS PER SIZE  END=========================================  -->

         <!--==============================================AS PER CONTRAST COLOR START=========================================  -->
		<?
		//$nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=2");
       // $nameArray_size=sql_select( "select distinct  item_size  as gmts_sizes from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=2");
	//$gmtcolor_library=return_library_array( "select b.item_color,b.color_number_id from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no=$txt_booking_no and a.sensitivity=3 and a.status_active=1 and a.is_deleted=0", "item_color", "color_number_id"  );
	  // echo "select distinct process,fabric_color_id from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=3 and wo_qnty !=0 order by fabric_color_id";
	// echo $wo_pre_cost_fab_co_color_sql="select b.gmts_color_id,b.contrast_color_id,b.pre_cost_fabric_cost_dtls_id as fab_dtls_id from wo_pre_cos_fab_co_color_dtls b,wo_booking_dtls a  where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and b.job_no='$job_num'";
	 // echo $wo_pre_cost_fab_co_color_sql="select b.gmts_color_id,b.contrast_color_id,b.pre_cost_fabric_cost_dtls_id as fab_dtls_id from wo_pre_cos_fab_co_color_dtls b  where   b.job_no='$job_num'";
	/*$wo_pre_fab_co_color_result=sql_select($wo_pre_cost_fab_co_color_sql);
	foreach( $wo_pre_fab_co_color_result as $row)
	{
		$contrast_color_arr[$row[csf('fab_dtls_id')]][$row[csf('gmts_color_id')]]['contrast_color']=$row[csf('contrast_color_id')];
	}
	*/
       $only_color_size_qnty=sql_select( "select fabric_color_id, gmts_color_id, slength, dia_width, fin_gsm, rate, description, sum(wo_qnty) as cons from wo_booking_dtls where booking_no=$txt_booking_no and sensitivity=3 and status_active=1 and is_deleted =0 and wo_qnty>0 group by fabric_color_id, gmts_color_id, slength, dia_width, fin_gsm, rate, description");
		 
					
		foreach($only_color_size_qnty as $OnlyColorSizeQnty)
		{
		  if($OnlyColorSizeQnty[csf('cons')]){
		 	$arrOnlyColQty[$OnlyColorSizeQnty[csf('fabric_color_id')]][$OnlyColorSizeQnty[csf('gmts_color_id')]][$OnlyColorSizeQnty[csf('description')]][$OnlyColorSizeQnty[csf('slength')]][$OnlyColorSizeQnty[csf('dia_width')]][$OnlyColorSizeQnty[csf('fin_gsm')]][$OnlyColorSizeQnty[csf('rate')]]['cons']=$OnlyColorSizeQnty[csf('cons')];
		  }
		}
		
	    $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=3 and wo_qnty !=0 ");
        $nameArray_color=sql_select( "select distinct fabric_color_id as color_number_id,gmts_color_id from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=3 and wo_qnty !=0 order by fabric_color_id");
		 $nameArray_color_labdib=sql_select( "select distinct labdip_no as labdip_no from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=3 and wo_qnty !=0 ");

		if(count($nameArray_color)>0)
		{

        ?>
        <table border="0" align="left" cellpadding="0" width="100%" cellspacing="0" >
            <tr>
                <td colspan="<? echo count($nameArray_color)+10; ?>" align="">
                <strong>Contrast Color</strong>
                </td>

              <!-- <td colspan="7">
                   <table class="rpt_table"  cellpadding="0" width="100%" cellspacing="0">
                   <tr>
                   <td width="200"> <strong> Yarn Count: </strong></td> <td></td>
                    <td width="200">  <strong>Lot :  </strong></td> <td></td>
                    <td width="200"> <strong>Brand:  </strong></td> <td></td>
                   </tr>
                   </table>
               </td> -->
            </tr>
            <tr>
                <td style="border:1px solid black"  rowspan="3"><strong>Sl</strong> </td>
                <td style="border:1px solid black"  rowspan="3"><strong>Service Type</strong> </td>
                <td style="border:1px solid black"  rowspan="3"><strong>Item Description</strong> </td>
                <td style="border:1px solid black" width="80"  rowspan="3"><strong>Y.Count</strong> </td>
                <td style="border:1px solid black" width="60"  rowspan="3"><strong>Lot</strong> </td>
                <td style="border:1px solid black" width="80" rowspan="3"><strong>Brand</strong> </td>
               <!-- <td style="border:1px solid black"  rowspan="2"><strong>Labdip No</strong> </td>-->
                <td style="border:1px solid black"><strong>Item Color</strong> </td>
                <?
                foreach($nameArray_color  as $result_color)
                {
					//$color_id=$contrast_color_arr[$result_color[csf('pre_cost_fabric_cost_dtls_id')]][$result_color[csf('color_number_id')]]['contrast_color'];	     ?>
                <td align="center" style="border:1px solid black"><strong><? echo $color_library[$result_color[csf('color_number_id')]];?></strong></td>
                <?	}    ?>
                <td style="border:1px solid black" align="center"  rowspan="3"><strong>Total</strong></td>
                <td style="border:1px solid black" align="center"  rowspan="3"><strong>M/C Dia</strong></td>
                <td style="border:1px solid black" align="center"  rowspan="3"><strong>Fin Dia</strong></td>
                <td style="border:1px solid black" align="center"  rowspan="3"><strong>Fin GSM</strong></td>
                <td style="border:1px solid black" align="center"  rowspan="3"><strong>S/Length</strong></td>
                <td style="border:1px solid black" align="center"  rowspan="3"><strong>UOM</strong></td>
                <td style="border:1px solid black;<? echo $hide_show;?>" align="center"  rowspan="3"><strong>Rate</strong></td>
                <td style="border:1px solid black;<? echo $hide_show;?>" align="center"  rowspan="3"><strong>Amount</strong></td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Gmts Color</strong> </td>
                <?
                foreach($nameArray_color  as $result_color)
                {	     ?>
                <td align="center" style="border:1px solid black"><strong><? echo $color_library[$result_color[csf('gmts_color_id')]];?></strong></td>
                <?	}    ?>

            </tr>
             <tr>
                <td style="border:1px solid black"><strong>Labdip No</strong> </td>
                <?
                foreach($nameArray_color_labdib  as $row)
                {	     ?>
                <td align="center" style="border:1px solid black"><strong><? echo $row[csf('labdip_no')];?></strong></td>
                <?	}    ?>

            </tr>
            <?
			if($db_type==2)
			{
			$group_con="listagg(cast(yarn_count as varchar2(4000)),',') within group (order by yarn_count) AS yarn_count,
		listagg(cast(lot_no as varchar2(4000)),',') within group (order by lot_no) AS lot_no,
		listagg(cast(brand as varchar2(4000)),',') within group (order by brand) AS brand";
			}
			else
			{
				$group_con="group_concat(yarn_count) AS yarn_count,
		group_concat(lot_no) AS lot_no,
		group_concat(brand) AS brand";
			}
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
				$i++;
				//echo "select distinct description,rate,labdip_no,uom,mc_dia,fin_gsm,slength,fabric_color_id,dia_width from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=3 and process=".$result_item[csf('process')]." and wo_qnty !=0 order by fabric_color_id";

          /* echo "select distinct description,rate,uom,mc_dia,fin_gsm,slength,fabric_color_id,dia_width,$group_con from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=3 and process=".$result_item[csf('process')]." and wo_qnty !=0 group by description,rate,uom,mc_dia,fin_gsm,slength,fabric_color_id,dia_width order by fabric_color_id";*/
		    $nameArray_item_description=sql_select( "select distinct description,rate,uom,mc_dia,fin_gsm,slength,dia_width,$group_con from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=3 and process=".$result_item[csf('process')]." and wo_qnty !=0 group by description,rate,uom,mc_dia,fin_gsm,slength,dia_width order by description");
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $i; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $conversion_cost_head_array[$result_item[csf('process')]]; ?>
                </td>
                <?
                $color_tatal=array();
                $total_amount_as_per_gmts_color=0;
                foreach($nameArray_item_description as $result_itemdescription)
                {
                $item_desctiption_total=0;
				$yarn_count=implode(',',array_unique(explode(",",$result_itemdescription[csf('yarn_count')])));
				$lot_no=implode(',',array_unique(explode(",",$result_itemdescription[csf('lot_no')])));
				$brand=implode(',',array_unique(explode(",",$result_itemdescription[csf('brand')])));
				//echo 'BB';
                ?>
                <td style="border:1px solid black"><? echo $fabric_description_array[$result_itemdescription[csf('description')]]; ?> </td>
                 <td style="border:1px solid black" width="80"><? echo $yarn_count; ?> </td>
                 <td style="border:1px solid black" width="60"><? echo $lot_no; ?> </td>
                 <td style="border:1px solid black" width="80"><? echo $brand; ?> </td>
                <td style="border:1px solid black"><? //echo $result_itemdescription['brand_supplier']; ?> Booking Qnty  </td>
                <?
				if($db_type==0) $dia_con="dia_width";
				else $dia_con="nvl(dia_width,0)";
				if($db_type==0)
					{
					if($result_itemdescription[csf('dia_width')]=='') $dia_width_con="";else $dia_width_con="and dia_width='".$result_itemdescription[csf('dia_width')]."'";
					if($result_itemdescription[csf('description')]=='') $description_con="";else $description_con="and description='".$result_itemdescription[csf('description')]."'";
					if($result_itemdescription[csf('uom')]=='') $uom_con="";else $uom_con="and uom='".$result_itemdescription[csf('uom')]."'";

					if($result_color[csf('fabric_color_id')]!=0) $fcolor_con="and fabric_color_id='".$result_color[csf('color_number_id')]."'";else $fcolor_con="";
					if($result_itemdescription[csf('item_size')]=='') $item_size_con="";else $item_size_con="and item_size='".$result_itemdescription[csf('item_size')]."'";
					}
					else
					{
					if($result_itemdescription[csf('dia_width')]=='') $dia_width_con="and slength is null";else $dia_width_con="and dia_width='".$result_itemdescription[csf('dia_width')]."'";
					if($result_itemdescription[csf('description')]=='') $description_con="and description is null";else $description_con="and description='".$result_itemdescription[csf('description')]."'";
					if($result_itemdescription[csf('uom')]=='') $uom_con="and uom is null";else $uom_con="and uom='".$result_itemdescription[csf('uom')]."'";
					//if($result_color[csf('color_number_id')]=='') $fcolor_con="and fabric_color_id is null";else $fcolor_con="and fabric_color_id='".$result_color[csf('color_number_id')]."'";
					if($result_itemdescription[csf('item_size')]=='') $item_size_con="and item_size is null";else $item_size_con="and dia_width='".$result_itemdescription[csf('item_size')]."'";
					}
                foreach($nameArray_color  as $result_color)
                {
					if($result_itemdescription[csf('dia_width')]=='') $dia_width=0;else $dia_width=$result_itemdescription[csf('dia_width')];
                /*$nameArray_color_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls    where   booking_no=$txt_booking_no and sensitivity=3 and process=". $result_item[csf('process')]." and description='". $result_itemdescription[csf('description')]."' and rate='". $result_itemdescription[csf('rate')]."' and uom='". $result_itemdescription[csf('uom')]."' and $dia_con='".$dia_width."' and fabric_color_id=".$result_color[csf('color_number_id')]."  ");    */
			//	$nameArray_color_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls    where   booking_no=$txt_booking_no and sensitivity=3 and process=". $result_item[csf('process')]." and rate='". $result_itemdescription[csf('rate')]."'  and gmts_color_id=".$result_color[csf('gmts_color_id')]." and wo_qnty>0  $dia_width_con $description_con $uom_con $fcolor_con order by fabric_color_id ");
				$color_size_qnty=$arrOnlyColQty[$result_color[csf('color_number_id')]][$result_color[csf('gmts_color_id')]][$result_itemdescription[csf('description')]][$result_itemdescription[csf('slength')]][$result_itemdescription[csf('dia_width')]][$result_itemdescription[csf('fin_gsm')]][$result_itemdescription[csf('rate')]]['cons'];
				//echo  "select sum(wo_qnty) as cons from wo_booking_dtls    where   booking_no=$txt_booking_no and sensitivity=3 and process=". $result_item[csf('process')]." and rate='". $result_itemdescription[csf('rate')]."'  and gmts_color_id=".$result_color[csf('gmts_color_id')]." and wo_qnty>0  $dia_width_con $description_con $uom_con $fcolor_con  order by fabric_color_id";

             //   foreach($nameArray_color_size_qnty as $result_color_size_qnty)
               // {
                ?>
                <td style="border:1px solid black; text-align:right">

                <?
                if($color_size_qnty!= "")
                {
                echo number_format($color_size_qnty,2);
                $item_desctiption_total += $color_size_qnty ;
                if (array_key_exists($result_color[csf('gmts_color_id')], $color_tatal))
                {
                $color_tatal[$result_color[csf('gmts_color_id')]]+=$color_size_qnty;
                }
                else
                {
                $color_tatal[$result_color[csf('gmts_color_id')]]=$color_size_qnty;
                }
                }
                else echo "";
                ?>
                </td>
                <?
                }
              //  }
                ?>
                <td style="border:1px solid black; text-align:right"><? echo number_format($item_desctiption_total,2); ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('mc_dia')]; ?></td>
                <td style="border:1px solid black; text-align:center"><? echo $result_itemdescription[csf('dia_width')]; ?></td>
               <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('fin_gsm')]; ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('slength')]; ?></td>
                <td style="border:1px solid black; text-align:center"><? echo $unit_of_measurement[$result_itemdescription[csf('uom')]]; ?></td>
                <td style="border:1px solid black; text-align:right;<? echo $hide_show;?>"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right;<? echo $hide_show;?>">
                <?
                $amount_as_per_gmts_color = $item_desctiption_total*$result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="5"><strong> Item Total</strong></td>
                <?
                foreach($nameArray_color  as $result_color)
                {

                ?>
                <td style="border:1px solid black;  text-align:right">
                <?
                if($color_tatal[$result_color[csf('gmts_color_id')]] !='')
                {
                echo number_format($color_tatal[$result_color[csf('gmts_color_id')]],2);
                }
                ?>
                </td>
            <?
            }
            ?>
                <td style="border:1px solid black;  text-align:right"><? echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black;text-align:center"></td>
                <td style="border:1px solid black;text-align:center"></td>
                <td style="border:1px solid black;text-align:center"></td>
                <td style="border:1px solid black;text-align:center"></td>
                 <td style="border:1px solid black;text-align:center"></td>


                <td style="border:1px solid black; text-align:right;<? echo $hide_show;?>"><? //echo number_format(round($color_total),0); ?></td>
                <td style="border:1px solid black; text-align:right;<? echo $hide_show;?>">
                <?
                echo number_format($total_amount_as_per_gmts_color,4);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="<? echo count($nameArray_color)+(14-$col_span); ?>"><strong><? if($show_rate==1) echo '';else echo 'Total'; ?></strong></td>
                <td  style="border:1px solid black;  text-align:right"><?
				if($show_rate==1) echo '';
				else echo number_format($grand_total_as_per_gmts_color,4); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
            </tr>
        </table>
        <br/>
        <?
		}
		?>
        <!--==============================================AS PER CONTRAST COLOR END=========================================  -->

        <!--==============================================AS PER GMTS Color & SIZE START=========================================  -->
		<?
		//$nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=2");
       // $nameArray_size=sql_select( "select distinct  item_size  as gmts_sizes from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=2");
	   //$nameArray_color=sql_select( "select distinct fabric_color_id as color_number_id from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=3");

        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=4 and wo_qnty !=0 and is_deleted=0 and status_active=1");
        $nameArray_size=sql_select( "select distinct item_size  as gmts_sizes from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=4 and wo_qnty !=0 and is_deleted=0 and status_active=1");
	    $nameArray_color=sql_select( "select distinct fabric_color_id as color_number_id from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=4 and wo_qnty !=0 and is_deleted=0 and status_active=1");

		if(count($nameArray_size)>0)
		{
        ?>

        <table border="0" align="left" cellpadding="0" width="100%" cellspacing="0" >
            <tr>
                <td colspan="<? echo count($nameArray_size)+12; ?>" align="">
                <strong>Color & size sensitive </strong>
                </td>

              <!-- <td colspan="8">
                   <table class="rpt_table"  cellpadding="0" width="100%" cellspacing="0">
                   <tr>
                   <td width="200"> <strong> Yarn Count: </strong></td> <td></td>
                    <td width="200">  <strong>Lot :  </strong></td> <td></td>
                    <td width="200"> <strong>Brand:  </strong></td> <td></td>
                   </tr>
                   </table>
               </td> -->
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Service Type</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black" width="80"><strong>Y.Count</strong> </td>
                <td style="border:1px solid black" width="60"><strong>Lot</strong> </td>
                <td style="border:1px solid black" width="80"><strong>Brand</strong> </td>
                <td style="border:1px solid black"><strong>Labdip No</strong> </td>

                <td style="border:1px solid black"><strong>Item Color</strong> </td>
                <?
                foreach($nameArray_size  as $result_size)
                {	     ?>
                <td align="center" style="border:1px solid black"><strong><? echo $result_size[csf('gmts_sizes')];?></strong></td>
                <?	}    ?>
                <td style="border:1px solid black" align="center"><strong>Total</strong></td>
                <td style="border:1px solid black" align="center"><strong>M/C Dia</strong></td>
                <td style="border:1px solid black" align="center"><strong>Fin Dia</strong></td>
                <td style="border:1px solid black" align="center"><strong>Fin GSM</strong></td>
                <td style="border:1px solid black" align="center"><strong>S/Length</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black;<? echo $hide_show;?>" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black;<? echo $hide_show;?>" align="center"><strong>Amount</strong></td>
            </tr>

            <?
			if($db_type==2)
			{
			$group_con="listagg(cast(yarn_count as varchar2(4000)),',') within group (order by yarn_count) AS yarn_count,
		listagg(cast(lot_no as varchar2(4000)),',') within group (order by lot_no) AS lot_no,
		listagg(cast(brand as varchar2(4000)),',') within group (order by brand) AS brand";
			}
			else
			{
				$group_con="group_concat(yarn_count) AS yarn_count,
		group_concat(lot_no) AS lot_no,
		group_concat(brand) AS brand";
			}

			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {

			$i++;
            $nameArray_item_description=sql_select( "select distinct description,mc_dia,fin_gsm,slength,rate,uom,dia_width,$group_con from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=4 and process=".$result_item[csf('process')]." and wo_qnty !=0 group by description,labdip_no,mc_dia,fin_gsm,slength,rate,uom,dia_width order by description");
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo   count($nameArray_item_description)+1; ?>">
                <? echo $i ; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)*1+1; ?>">
                <? echo $conversion_cost_head_array[$result_item[csf('process')]]; ?>
                </td>
                <?
                $color_tatal=array();
                $total_amount_as_per_gmts_color=0;
                foreach($nameArray_item_description as $result_itemdescription)
                {
					$yarn_count=implode(',',array_unique(explode(",",$result_itemdescription[csf('yarn_count')])));
					$lot_no=implode(',',array_unique(explode(",",$result_itemdescription[csf('lot_no')])));
					$brand=implode(',',array_unique(explode(",",$result_itemdescription[csf('brand')])));
					?>
                    <td style="border:1px solid black" rowspan="<? echo count($nameArray_color); ?>"><? echo $fabric_description_array[$result_itemdescription[csf('description')]]; ?> </td>
                    <td style="border:1px solid black" width="80"><? echo $yarn_count; ?> </td>
                    <td style="border:1px solid black" width="60"><? echo $lot_no; ?> </td>
                    <td style="border:1px solid black" width="80"><? echo $brand; ?> </td>
                    <td style="border:1px solid black"><? echo $result_itemdescription[csf('labdip_no')]; ?> </td>
                    <td style="border:1px solid black" rowspan="<? //echo count($nameArray_color); ?>"><? //echo $result_itemdescription['brand_supplier']; ?>Booking Qnty </td>

                    <?
					if($db_type==0)
					{
					if($result_itemdescription[csf('dia_width')]=='') $dia_width_con="";else $dia_width_con="and dia_width='".$result_itemdescription[csf('dia_width')]."'";
					if($result_itemdescription[csf('description')]=='') $description_con="";else $description_con="and description='".$result_itemdescription[csf('description')]."'";
					if($result_itemdescription[csf('uom')]=='') $uom_con="";else $uom_con="and uom='".$result_itemdescription[csf('uom')]."'";

					if($result_color[csf('fabric_color_id')]!=0) $fcolor_con="and fabric_color_id='".$result_color[csf('fabric_color_id')]."'";else $fcolor_con="";
					if($result_itemdescription[csf('item_size')]=='') $item_size_con="";else $item_size_con="and item_size='".$result_itemdescription[csf('gmts_sizes')]."'";
					}
					else
					{
					if($result_itemdescription[csf('dia_width')]=='') $dia_width_con="and slength is null";else $dia_width_con="and dia_width='".$result_itemdescription[csf('dia_width')]."'";
					if($result_itemdescription[csf('description')]=='') $description_con="and description is null";else $description_con="and description='".$result_itemdescription[csf('description')]."'";
					if($result_itemdescription[csf('uom')]=='') $uom_con="and uom is null";else $uom_con="and uom='".$result_itemdescription[csf('uom')]."'";
					if($result_color[csf('color_number_id')]=='') $fcolor_con="and color_number_id is null";else $fcolor_con="and color_number_id='".$result_color[csf('color_number_id')]."'";
					if($result_size[csf('gmts_sizes')]=='') $item_size_con="and item_size is null";else $item_size_con="and item_size='".$result_size[csf('gmts_sizes')]."'";
					}
                //$item_desctiption_total=0;
				foreach($nameArray_color as $result_color)
                {
					 $item_desctiption_total=0;
                ?>

                <!--<td style="border:1px solid black">DFF<? //echo $color_library[$result_color[csf('color_number_id')]]; ?> </td>-->
                <?
                foreach($nameArray_size  as $result_size)
                {
					//echo 'ssddd';
               /* $nameArray_size_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=4 and process=". $result_item[csf('process')]." and  description='". $result_itemdescription[csf('description')]."' and  item_size='".$result_size[csf('gmts_sizes')]."' and rate='". $result_itemdescription[csf('rate')]."' and uom='". $result_itemdescription[csf('uom')]."' and dia_width='". $result_itemdescription[csf('dia_width')]."' and fabric_color_id=".$result_color[csf('color_number_id')].""); */
				 $nameArray_size_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=4 and process=". $result_item[csf('process')]." and rate='". $result_itemdescription[csf('rate')]."' $dia_width_con $description_con $uom_con $fcolor_con $item_size_con");
                foreach($nameArray_size_size_qnty as $result_size_size_qnty)
                {
                ?>
                <td style="border:1px solid black; text-align:right">
                <?
                if($result_size_size_qnty[csf('cons')]!= "")
                {
                echo number_format($result_size_size_qnty[csf('cons')],2);
                $item_desctiption_total += $result_size_size_qnty[csf('cons')] ;
                if (array_key_exists($result_size[csf('color_number_id')], $color_tatal))
                {
                $color_tatal[$result_size[csf('color_number_id')]]+=$result_size_size_qnty[csf('cons')];
                }
                else
                {
                $color_tatal[$result_size[csf('color_number_id')]]=$result_size_size_qnty[csf('cons')];
                }
                }
                else echo "";
                ?>
                </td>
                <?
                }
                }
                ?>

                <td style="border:1px solid black; text-align:right"><? echo number_format($item_desctiption_total,2); ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('mc_dia')]; ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('dia_width')]; ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('fin_gsm')]; ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('slength')]; ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$result_itemdescription[csf('uom')]]; ?></td>
                <td style="border:1px solid black; text-align:right;<? echo $hide_show;?>"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right;<? echo $hide_show;?>">
                <?
                $amount_as_per_gmts_color = $item_desctiption_total*$result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
			}
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="8"><strong> Item Total</strong></td>

                <?
                foreach($nameArray_size  as $result_size)
                {

                ?>
                <td style="border:1px solid black;  text-align:right">
                <?
                if($color_tatal[$result_size[csf('gmts_sizes')]] !='')
                {
                echo number_format($color_tatal[$result_size[csf('gmts_sizes')]],2);
                }
                ?>
                </td>
            <?
            }
            ?>
                <td style="border:1px solid black;  text-align:right"><? echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black; text-align:right;<? echo $hide_show;?>"><? //echo number_format(round($color_total),0); ?></td>
                <td style="border:1px solid black; text-align:right;<? echo $hide_show;?>" >
                <?
                echo number_format($total_amount_as_per_gmts_color,4);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="<? echo count($nameArray_size)+(15-$col_span); ?>"><strong><? if($show_rate==1) echo '';else echo 'Total'; ?></strong></td>
                <td  style="border:1px solid black;  text-align:right"><?
				if($show_rate==1) echo ' ';else echo number_format($grand_total_as_per_gmts_color,4); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
            </tr>
        </table>
        <br/>
        <?
		}
		?>
        <!--==============================================AS PER Color & SIZE  END=========================================  -->


         <!--==============================================NO NENSITIBITY START=========================================  -->
		<?
        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=0 and wo_qnty !=0 and is_deleted=0 and status_active=1");
        //$nameArray_color=sql_select( "select distinct b.color_number_id from wo_trims_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=$txt_booking_no and a.sensitivity=1");
		$nameArray_color= array();
		if(count($nameArray_item)>0)
		{
        ?>
        <table border="0" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" >
            <tr>
                <td align="" colspan="11" >
                <strong>No Sensitivity</strong>
                </td>

               <!--<td colspan="6">
                   <table class="rpt_table"  cellpadding="0" width="100%" cellspacing="0">
                   <tr>
                   <td width="200"> <strong> Yarn Count: </strong></td> <td></td>
                    <td width="200">  <strong>Lot :  </strong></td> <td></td>
                    <td width="200"> <strong>Brand:  </strong></td> <td></td>
                   </tr>
                   </table>
               </td> -->
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Service Type</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black" width="80"><strong>Y.Count</strong> </td>
                <td style="border:1px solid black" width="60"><strong>Lot</strong> </td>
                <td style="border:1px solid black" width="80"><strong>Brand</strong> </td>
                <td style="border:1px solid black"><strong>Labdip No</strong> </td>
                <td style="border:1px solid black"><strong></strong> </td>
                <td style="border:1px solid black" align="center"><strong> Qnty</strong></td>
                <td style="border:1px solid black" align="center"><strong>M/C Dia</strong></td>
                <td style="border:1px solid black" align="center"><strong>Fin Dia</strong></td>
                <td style="border:1px solid black" align="center"><strong>Fin GSM</strong></td>
                <td style="border:1px solid black" align="center"><strong>S/Length</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black;<? echo $hide_show;?>" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black;<? echo $hide_show;?>" align="center"><strong>Amount</strong></td>
            </tr>
            <?
			if($db_type==2)
			{
			$group_con="listagg(cast(yarn_count as varchar2(4000)),',') within group (order by yarn_count) AS yarn_count,
		listagg(cast(lot_no as varchar2(4000)),',') within group (order by lot_no) AS lot_no,
		listagg(cast(brand as varchar2(4000)),',') within group (order by brand) AS brand";
			}
			else
			{
				$group_con="group_concat(yarn_count) AS yarn_count,
		group_concat(lot_no) AS lot_no,
		group_concat(brand) AS brand";
			}
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
				$i++;
            $nameArray_item_description=sql_select( "select distinct description,labdip_no,rate,mc_dia,fin_gsm,slength,uom,dia_width,$group_con from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=0 and process=".$result_item[csf('process')]." and wo_qnty !=0 group by description,labdip_no,rate,mc_dia,fin_gsm,slength,uom,dia_width");
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $i; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $conversion_cost_head_array[$result_item[csf('process')]]; ?>
                </td>
                <?
                $color_tatal=0;
                $total_amount_as_per_gmts_color=0;
                foreach($nameArray_item_description as $result_itemdescription)
                {
                $item_desctiption_total=0;
				$yarn_count=implode(',',array_unique(explode(",",$result_itemdescription[csf('yarn_count')])));
				$lot_no=implode(',',array_unique(explode(",",$result_itemdescription[csf('lot_no')])));
				$brand=implode(',',array_unique(explode(",",$result_itemdescription[csf('brand')])));
                ?>
                <td style="border:1px solid black"><? echo $fabric_description_array[$result_itemdescription[csf('description')]]; ?> </td>
                <td style="border:1px solid black" width="80"><? echo $yarn_count; ?> </td>
                <td style="border:1px solid black" width="60"><?  echo $lot_no; ?> </td>
                <td style="border:1px solid black" width="80"><? echo $brand; ?> </td>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('labdip_no')]; ?> </td>
                <td style="border:1px solid black"><? //echo $result_itemdescription['brand_supplier']; ?>Booking Qnty  </td>
                <?
				if($db_type==0)
					{
					if($result_itemdescription[csf('dia_width')]=='') $dia_width_con="";else $dia_width_con="and dia_width='".$result_itemdescription[csf('dia_width')]."'";
					if($result_itemdescription[csf('description')]=='') $description_con="";else $description_con="and description='".$result_itemdescription[csf('description')]."'";
					if($result_itemdescription[csf('uom')]=='') $uom_con="";else $uom_con="and uom='".$result_itemdescription[csf('uom')]."'";


					}
					else
					{
					if($result_itemdescription[csf('dia_width')]=='') $dia_width_con="and slength is null";else $dia_width_con="and dia_width='".$result_itemdescription[csf('dia_width')]."'";
					if($result_itemdescription[csf('description')]=='') $description_con="and description is null";else $description_con="and description='".$result_itemdescription[csf('description')]."'";
					if($result_itemdescription[csf('uom')]=='') $uom_con="and uom is null";else $uom_con="and uom='".$result_itemdescription[csf('uom')]."'";

					}

            /*    $nameArray_color_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls where    booking_no=$txt_booking_no and sensitivity=0 and process=". $result_item[csf('process')]." and  description='". $result_itemdescription[csf('description')]."'and rate='". $result_itemdescription[csf('rate')]."' and uom='". $result_itemdescription[csf('uom')]."' and dia_width='". $result_itemdescription[csf('dia_width')]."'");  */
				 $nameArray_color_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls where    booking_no=$txt_booking_no and sensitivity=0 and process=". $result_item[csf('process')]."  and rate='". $result_itemdescription[csf('rate')]."'  $dia_width_con $description_con $uom_con");
                foreach($nameArray_color_size_qnty as $result_color_size_qnty)
                {
                ?>
                <td style="border:1px solid black; text-align:right">
                <?
                if($result_color_size_qnty[csf('cons')]!= "")
                {
                echo number_format($result_color_size_qnty[csf('cons')],2);
                $item_desctiption_total += $result_color_size_qnty[csf('cons')] ;
                $color_tatal+=$result_color_size_qnty[csf('cons')];
                }
                else echo "";
                ?>
                </td>
                <?
                }
                ?>
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('mc_dia')]; ?></td>
                <td style="border:1px solid black; text-align:center "><? echo $result_itemdescription[csf('dia_width')]; ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('fin_gsm')]; ?></td>
                 <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('slength')]; ?></td>
                <td style="border:1px solid black; text-align:center "><? echo $unit_of_measurement[$result_itemdescription[csf('uom')]]; ?></td>
                <td style="border:1px solid black; text-align:right;<? echo $hide_show;?>"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right;<? echo $hide_show;?>">
                <?
                $amount_as_per_gmts_color = $item_desctiption_total*$result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="6"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right">
                <?
                if($color_tatal !='')
                {
                echo number_format($color_tatal,2);
                }
                ?>
                </td>
                  <td style="border:1px solid black;"></td>
                <td style="border:1px solid black;"></td>
                <td style="border:1px solid black; text-align:right"></td>
                <td style="border:1px solid black;"></td>
                <td style="border:1px solid black;"></td>
                <td style="border:1px solid black; text-align:right<? echo $hide_show;?>"></td>
                <td style="border:1px solid black; text-align:right;<? echo $hide_show;?>">
                <?
			 if($show_rate==1) echo '';else echo number_format($total_amount_as_per_gmts_color,4);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="15-<? echo $col_span?>"><strong><? if($show_rate==1) echo '';else echo 'Total'; ?></strong></td>
                <td  style="border:1px solid black;  text-align:right"><?
				if($show_rate==1) echo '';else echo number_format($grand_total_as_per_gmts_color,4); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
            </tr>
        </table>
        <?
		//print_r($color_tatal);
		}
		?>
        <!--==============================================NO NENSITIBITY END=========================================  -->
       &nbsp;
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
       <table  width="100%" class="rpt_table" style="border:1px solid black;"   border="0" cellpadding="0" cellspacing="0">
       <tr style="border:1px solid black;">
                <th width="70%" style="border:1px solid black; text-align:right"><? if($show_rate==1) echo '';else echo 'Total Booking Amount'; ?></th><td width="30%" style="border:1px solid black; text-align:right"><?  if($show_rate==1) echo '';else echo number_format($booking_grand_total,4);?></td>
            </tr>
            <tr style="border:1px solid black;">
                <th width="70%" style="border:1px solid black; text-align:right"><? if($show_rate==1) echo '';else echo 'Total Booking Amount (in word)'; ?></th><td width="30%" style="border:1px solid black;"><?
				 if($show_rate==1) echo '';else echo number_to_words($booking_grand_total,$mcurrency, $dcurrency);?></td>
            </tr>
       </table>
          &nbsp;
        <table  width="100%" class="rpt_table" style="border:1px solid black;"   border="0" cellpadding="0" cellspacing="0">

        <?
        $data_array=sql_select("select id, terms from  wo_booking_terms_condition where booking_no=$txt_booking_no");// quotation_id='$data'
        if ( count($data_array)>0)
        {
		?>

         <thead>
            <tr style="border:1px solid black;">
                <th width="3%" style="border:1px solid black;">Sl</th><th width="97%" style="border:1px solid black;font-weight:bold;"><b>Spacial Instruction</b></th>
            </tr>
        </thead>
        <tbody>

        <?
            $i=0;
            foreach( $data_array as $row )
            {
                $i++;
                ?>
                    <tr id="settr_1" align="" style="border:1px solid black;">
                        <td style="border:1px solid black;">
                        <? echo $i;?>
                        </td>
                        <td style="border:1px solid black;"><strong style=" font-weight:bold; ">
                        <? echo $row[csf('terms')]; ?>
                        </strong>
                        </td>
                    </tr>
                <?
            }
        }
        else
        { 

		?>
         <thead>
            <tr style="border:1px solid black;">
                <th width="3%" style="border:1px solid black;">Sl</th><th width="97%" style="border:1px solid black;font-weight:bold;"><b>Spacial Instruction</b></th>
            </tr>
        </thead>
        <tbody>
        <?
			$i=0;
        $data_array=sql_select("select id, terms from  lib_terms_condition where is_default=1 and page_id=176");// quotation_id='$data'
        foreach( $data_array as $row )
            {
                $i++;
        ?>
        <tr id="settr_1" align="" style="border:1px solid black;">
                        <td style="border:1px solid black;">
                        <? echo $i;?>
                        </td>
                        <td style="border:1px solid black;">
                        <strong style="  font-weight:bold;">
                        <? echo $row[csf('terms')]; ?>
                        </strong>
                        </td>

                    </tr>
        <?
            }
        }
		//$po_no
		//$po_no=rtrim($po_no,',');//wo_booking_mst
		$po_ids=implode(",",array_unique(explode(",",$po_ids)));
		$booking_nos='';$booking_dates='';
	  $booking_arr="select a.booking_date,a.booking_no  from wo_booking_mst a ,wo_booking_dtls b, wo_po_break_down c  where  a.booking_no=b.booking_no and b.po_break_down_id=c.id  and a.booking_no!=$txt_booking_no and c.id in($po_ids) and a.company_id=$cbo_company_name and a.item_category=12 and b.job_no=c.job_no_mst  and a.booking_type=3  and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 group by a.booking_date,a.booking_no";
	  $booking_previous=sql_select($booking_arr);
	foreach ($booking_previous as $result_book)
	{
		if($booking_nos!='') $booking_nos.=",".$result_book[csf('booking_no')].'; '.change_date_format($result_book[csf('booking_date')]);else  $booking_nos=$result_book[csf('booking_no')].'; '.change_date_format($result_book[csf('booking_date')]);
	}
        ?>

    </tbody>
    </table>
    <? if($cid==2){?>
    <br/>
     <table  width="100%" class="rpt_table" style="border:1px solid black;"   border="0" cellpadding="0" cellspacing="0">
        <thead>
         <tr align="left" style="border:1px solid black;font-weight:bold;">
          <th> Booking No & Date</th>
         </tr>
         </thead>
     <tr style="border:1px solid black;">
         <td><? echo $booking_nos;?> </td>

        </tr>
     </table>

     <?
	}
	 ?>
         <br/>

		 <?
            echo signature_table(53, $cbo_company_name, "1313px");
			if($cid==2){
			echo "****".custom_file_name($txt_booking_no,$style_sting,$job_no);
			}
         ?>
    </div>
    <script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <script>
		fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id_<? echo $cid; ?>');
	</script>
	<p style="page-break-after:always;"></p>
    <?
	}
	
	$html = ob_get_contents();
	ob_clean();
	list($is_mail_send,$mail)=explode('___',$mail_send_data);
	if($is_mail_send==1){
		require_once('../../../mailer/class.phpmailer.php');
		require_once('../../../auto_mail/setting/mail_setting.php');
		$mailBody = preg_replace("/<img[^>]+\>/i", " ", $html); 
			
		$mailToArr=array();
		$mailSql = "select b.EMAIL  from wo_booking_mst a,LIB_SUPPLIER b where b.id=a.supplier_id and a.booking_no=$txt_booking_no";
		//echo $mailSql;die;
		$mailSqlRes=sql_select($mailSql);
		foreach($mailSqlRes as $rows){
			if($rows[EMAIL]){$mailToArr[]=$rows[EMAIL];}
		}
		
		
		$mailSql = "SELECT c.email_address as EMAIL FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=97 and b.mail_user_setup_id=c.id  and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
		//echo $mailSql;die;
		$mailSqlRes=sql_select($mailSql);
		foreach($mailSqlRes as $rows){
			if($rows[EMAIL]){$mailToArr[]=$rows[EMAIL];}
		}
		
		
		if($mail!=''){$mailToArr[]=$mail;}
		
		$to=implode(',',$mailToArr);
 		$subject=" Service Booking Sheet ";
		$header=mailHeader();
		echo sendMailMailer( $to, $subject, $mailBody, $from_mail,$att_file_arr );
		
	}
	else{
		echo $html;
	}
	exit();


}

if($action=="show_trim_booking_report2" || $action=="show_trim_booking_report3")
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library  where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$company_address_arr=return_library_array( "select id,city from   lib_company",'id','city');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	//$po_qnty_tot=return_field_value( "style_ref_no", "wo_po_details_master","job_no in(".str_replace("'","",$txt_order_no_id).")");
	$job_no="";$style_ref="";
	$nameArray_job=sql_select( "select distinct b.job_no,a.buyer_id,c.style_ref_no as style  from wo_booking_mst a, wo_booking_dtls b,wo_po_details_master c  where a.booking_no=b.booking_no and c.job_no=b.job_no and a.booking_no=$txt_booking_no and b.status_active=1");
		$buyer_name=$nameArray_job[0][csf('buyer_id')];
        foreach ($nameArray_job as $result_job)
        {
			$job_no.="'".$result_job[csf('job_no')]."'".",";
			$style_ref.=$result_job[csf('style')].",";
			$job_num=$result_job[csf('job_no')];

		}
	$po_no=""; $po_ids=""; $interRef="";
	$nameArray_job=sql_select( "select distinct b.id, b.po_number, b.grouping from wo_booking_dtls a, wo_po_break_down b  where a.po_break_down_id=b.id  and a.booking_no=$txt_booking_no and a.status_active=1 and b.status_active=1");
	foreach ($nameArray_job as $result_job)
	{
		$po_no.=$result_job[csf('po_number')].",";
		$interRef.=$result_job[csf('grouping')].",";
		if($po_ids!="") $po_ids.=",".$result_job[csf('id')];else $po_ids=$result_job[csf('id')];
	}

	$show_rate=str_replace("'","",$show_rate);
	//echo $show_rate;
	if($show_rate==1)
	{
		$col_span=2;
		$hide_show="display:none";
	}
	else
	{
		$col_span=0;
		$hide_show="";
	}
	if($action=="show_trim_booking_report2") $hdColSpan="4"; else if($action=="show_trim_booking_report3") $hdColSpan="3";
	$job_nos=str_replace("'","",$job_no);
	$copy_no=array(1,2); //for Dynamic Copy here
	ob_start();
	foreach($copy_no as $cid)
	{
	?>
	<div style="width:1333px" align="center">
        <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black">
            <tr>
                <td width="100">
                	<img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
                </td>
                <td width="1000">
                    <table width="100%" cellpadding="0" cellspacing="0" >
                        <tr>
                            <td align="center" style="font-size:18px;"><?=$company_library[$cbo_company_name]; ?>
                            <span style="font-size:x-large; margin-left:10px !important;"> <? echo $cid;?><sup><?php if ($cid == 1) {echo 'st';} elseif ($cid == 2) {echo 'nd';} else {echo 'rd';}?></sup> Copy</span>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:14px">
                            <?
                            $nameArray=sql_select( "select plot_no,level_no,road_no,bin_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
                            foreach ($nameArray as $result)
                            {
								?>
								Plot No: <? echo $result[csf('plot_no')]; ?>
								Level No: <? echo $result[csf('level_no')]?>
								Road No: <? echo $result[csf('road_no')]; ?>
								Block No: <? echo $result[csf('block_no')];?>
								City No: <? echo $result[csf('city')];?>
								Zip Code: <? echo $result[csf('zip_code')]; ?>
								Province No: <?php echo $result[csf('province')]; ?>
								Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br>
								Email Address: <? echo $result[csf('email')];?>
								Website No: <? echo $result[csf('website')];
								if($result[csf('bin_no')]!='') echo "<br> BIN:".$result[csf('bin_no')];
                            }
                            ?>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:16px">
                            	<strong>Service Booking Sheet</strong>
                            </td>
                        </tr>
                    </table>
                </td>
                <?php if($show_buyer==1){?>
                <td width="100" style="font-size:18px">
                	<strong> Style Ref: &nbsp; </strong>
                </td>
                <td width="250" style="font-size:18px">
                	
                	<strong><?=$style_ref=rtrim($style_ref,','); ?> </strong>
                	
                </td>
                <?}else{ ?>
                	<td width="100" ></td>
                	<td width="250" > </td>
              <?  } ?>
            </tr>
        </table>
		<?
		$booking_grand_total=0;
		//$job_no="";
		$currency_id="";

        $nameArray=sql_select( "select a.booking_no,a.booking_date,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.source,a.pay_mode  from wo_booking_mst a where  a.booking_no=$txt_booking_no and a.supplier_id=$hidden_supplier_id and a.id=$booking_mst_id");
      
        foreach ($nameArray as $result)
        {
			$varcode_booking_no=$result[csf('booking_no')];
			?>
			<table width="100%" style="border:1px solid black">
                <tr>
                	<td colspan="6" valign="top"></td>
                </tr>
                <tr>
                    <td width="100" style="font-size:12px"><b>Booking No </b>   </td>
                    <td width="110">:&nbsp;<? echo $result[csf('booking_no')];?> </td>
                    <td width="100" style="font-size:12px"><b>Booking Date</b></td>
                    <td width="110">:&nbsp;<? echo change_date_format($result[csf('booking_date')],'dd-mm-yyyy','-');?>&nbsp;&nbsp;&nbsp;</td>
                    <td width="100"><span style="font-size:12px"><b>Delivery Date</b></span></td>
                    <td width="110">:&nbsp;<? echo change_date_format($result[csf('delivery_date')],'dd-mm-yyyy','-');?></td>
                </tr>
                <tr>
                    <td width="100" style="font-size:12px"><b>Currency</b></td>
                    <td width="110">:&nbsp;<? $currency_id=$result[csf('currency_id')];echo $currency[$result[csf('currency_id')]]; ?></td>
                    <td  width="100" style="font-size:12px"><b>Conversion Rate</b></td>
                    <td  width="110" >:&nbsp;<? echo $result[csf('exchange_rate')]; ?></td>
                    <td  width="100" style="font-size:12px"><b>Source</b></td>
                    <td  width="110" >:&nbsp;<? echo $source[$result[csf('source')]]; ?></td>
                </tr>
                <tr>
                    <td width="100" style="font-size:12px"><b>Supplier Name</b>   </td>
                    <td width="110">:&nbsp;<?
                    if($result[csf("pay_mode")]!=3 && $result[csf("pay_mode")]!=5)
                    {
                    echo $supplier_name_arr[$result[csf('supplier_id')]];
                    }
                    else
                    {
                    echo $company_library[$result[csf('supplier_id')]];
                    }
                    ?>    </td>
                    <td width="100" style="font-size:12px"><b>Supplier Address</b></td>
                    <td width="110">:&nbsp;<?
                    if($result[csf("pay_mode")]!=3 && $result[csf("pay_mode")]!=5)
                    {
                    echo $supplier_address_arr[$result[csf('supplier_id')]];
                    }
                    else
                    {
                    echo $company_address_arr[$result[csf('supplier_id')]];
                    }
                    ?></td>
                    <td  width="100" style="font-size:12px"><b>Attention</b></td>
                    <td  width="110" >:&nbsp;<? echo $result[csf('attention')]; ?></td>
                </tr>
                <tr>
                    <td width="100" style="font-size:12px"><b>Job No</b>   </td>
                    <td width="110">:&nbsp;<?=$job_nos=rtrim($job_nos,','); ?></td>
                    <td width="110" style="font-size:12px"><b>PO No</b> </td>
                    <td  width="100" style="font-size:12px" >:&nbsp;<?
                    $po_nos= rtrim($po_no,',');
                    $po_nos=implode(", ",array_unique(explode(",",$po_nos)));
                    echo $po_nos;
                    ?> </td>
                    <?php if($show_buyer==1){?>
                    <td width="110" style="font-size:12px"><b>Buyer Name</b>   </td>
                    <td width="100" >:&nbsp;<?=$buyer_name_arr[$buyer_name]; ?></td>
                    <?}else{?>
                    	 <td width="110" style="font-size:12px">  </td>
                   		 <td width="100" ></td>
                    <?}?>
                </tr>
                <tr>
                    <td width="100" style="font-size:12px"><b>Internal Ref.</b></td><!--ISD-21-01857-->
                    <td width="110" style="word-break:break-all">:&nbsp;<?=implode(",",array_filter(array_unique(explode(",",$interRef)))); ?></td>
                    <td width="110" style="font-size:12px"><b>&nbsp;</b> </td>
                    <td width="100" style="font-size:12px">&nbsp;</td>
                    <td width="110" style="font-size:12px"><b>&nbsp;</td>
                    <td width="100">&nbsp;</td>
                </tr>
			</table>
			<?
        }
        //-==============================================AS PER GMTS COLOR START=========================================  -->
		//========================================
		$fabric_description_array=array();
	    $wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select id,fabric_description,cons_process from wo_pre_cost_fab_conv_cost_dtls  where  job_no in(".rtrim($job_no,", ").")");
	foreach( $wo_pre_cost_fab_conv_cost_dtls_id as $row_wo_pre_cost_fab_conv_cost_dtls_id)
	{
		if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]!=0)
		{
			$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description,gsm_weight from  wo_pre_cost_fabric_cost_dtls  where  id='".$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]."'");//gsm_weight
			list($fabric_description_row)=$fabric_description;
			$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]=$body_part[$fabric_description_row[csf("body_part_id")]].', '.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")].', '.$fabric_description_row[csf("gsm_weight")];//.', '.$fabric_description_row[csf("gsm_weight")]
		}
		if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]==0)
		{
			//echo "select id,body_part_id,color_type_id,fabric_description from  wo_pre_cost_fabric_cost_dtls  where  job_no='$data'";
			$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description,gsm_weight from  wo_pre_cost_fabric_cost_dtls  where   job_no in(".rtrim($job_no,", ").")");//gsm_weight
			//list($fabric_description_row)=$fabric_description;
			foreach( $fabric_description as $fabric_description_row)
	        {
			$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]].=$body_part[$fabric_description_row[csf("body_part_id")]].', '.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")].", ".$fabric_description_row[csf("gsm_weight")];//.', '.$fabric_description_row[csf("gsm_weight")]

			//$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]="All Fabrics  ".$conversion_cost_head_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("cons_process")]];
			}
		}


	}
	//print_r($fabric_description_array);
	//=================================================
        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=1 and wo_qnty !=0 and is_deleted=0 and status_active=1" );

		 $nameArray_color2=sql_select( "select distinct fabric_color_id,gmts_color_id from wo_booking_dtls   where  booking_no=$txt_booking_no and sensitivity=1 and wo_qnty !=0 and is_deleted=0 and status_active=1");
		foreach( $nameArray_color2 as $row)
		{
			$fab_gmt_color_arr[$row[csf('fabric_color_id')]]=$row[csf('gmts_color_id')];
		}
		
		$only_color_size_qnty=sql_select( "select fabric_color_id,remark, gmts_color_id, slength, dia_width, fin_gsm, rate, description, sum(wo_qnty) as cons from wo_booking_dtls where booking_no=$txt_booking_no and sensitivity=1 and status_active=1 and is_deleted =0 $process_cond group by fabric_color_id, gmts_color_id, slength, dia_width,remark, fin_gsm, rate, description");
		/*echo "select fabric_color_id, gmts_color_id, slength, dia_width, fin_gsm, rate, description, dia_width, fin_gsm, sum(wo_qnty) as cons from wo_booking_dtls where booking_no=$txt_booking_no and sensitivity=1 and status_active=1 and is_deleted =0 $process_cond group by fabric_color_id, gmts_color_id, slength, dia_width, fin_gsm, rate, description, dia_width, fin_gsm";*/
					
		foreach($only_color_size_qnty as $OnlyColorSizeQnty)
		{
		  if($OnlyColorSizeQnty[csf('cons')]){
		 	$arrOnlyColQty[$OnlyColorSizeQnty[csf('fabric_color_id')]][$OnlyColorSizeQnty[csf('gmts_color_id')]][$OnlyColorSizeQnty[csf('description')]][$OnlyColorSizeQnty[csf('slength')]][$OnlyColorSizeQnty[csf('dia_width')]][$OnlyColorSizeQnty[csf('fin_gsm')]][$OnlyColorSizeQnty[csf('rate')]][$OnlyColorSizeQnty[csf('remark')]]['cons']=$OnlyColorSizeQnty[csf('cons')];
			
			
		  }
		}

        $nameArray_color=sql_select( "select distinct fabric_color_id, gmts_color_id,remark from wo_booking_dtls  where  booking_no=$txt_booking_no and sensitivity=1 and wo_qnty !=0 and is_deleted=0 and status_active=1");
		$nameArray_color_labdib=sql_select( "select  labdip_no from wo_booking_dtls   where  booking_no=$txt_booking_no and sensitivity=1 and wo_qnty !=0 and is_deleted=0 and status_active=1");

		if(count($nameArray_color)>0)
		{
        ?>
        <table border="0" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" >
            <tr>
                <td colspan="<?=count($nameArray_color)+10; ?>" align="">
                <strong>As Per Garments Color</strong>
                </td>

              <!-- <td colspan="7">
               <table class="rpt_table"  cellpadding="0" width="100%" cellspacing="0">
               <tr>
               <td width="200"> <strong> Yarn Count: </strong></td> <td></td>
                <td width="200">  <strong>Lot :  </strong></td> <td></td>
                <td width="200"> <strong>Brand:  </strong></td> <td></td>
               </tr>
               </table>
               </td> -->

            </tr>
            <tr>
                <td style="border:1px solid black" rowspan="<?=$hdColSpan; ?>"><strong>Sl</strong> </td>
                <td style="border:1px solid black" rowspan="<?=$hdColSpan; ?>"><strong>Service Type</strong> </td>
                <td style="border:1px solid black" rowspan="<?=$hdColSpan; ?>"><strong>Item Description</strong> </td>
                <td style="border:1px solid black" width="80" rowspan="<?=$hdColSpan; ?>"><strong>Y.Count</strong> </td>
                <td style="border:1px solid black" width="60" rowspan="<?=$hdColSpan; ?>"><strong>Lot</strong> </td>
                <td style="border:1px solid black" width="80" rowspan="<?=$hdColSpan; ?>"><strong>Brand</strong> </td>
               <!-- <td style="border:1px solid black" rowspan="3"><strong>Labdip No</strong> </td>-->
                <td style="border:1px solid black"><strong>Item Color</strong> </td>
                <?
                foreach($nameArray_color  as $result_color)
                {	     ?>
                <td align="center" style="border:1px solid black"><strong><? echo $color_library[$result_color[csf('fabric_color_id')]];?></strong></td>
                <?	}    ?>
                <td style="border:1px solid black" align="center" rowspan="<?=$hdColSpan; ?>"><strong>Total</strong></td>
                <td style="border:1px solid black" align="center" rowspan="<?=$hdColSpan; ?>"><strong>M/C Dia</strong></td>
                <td style="border:1px solid black" align="center" rowspan="<?=$hdColSpan; ?>"><strong>Fin Dia</strong></td>
                <td style="border:1px solid black" align="center" rowspan="<?=$hdColSpan; ?>"><strong>Fin GSM</strong></td>
                <td style="border:1px solid black" align="center" rowspan="<?=$hdColSpan; ?>"><strong>S/Length</strong></td>
                <td style="border:1px solid black" align="center" rowspan="<?=$hdColSpan; ?>"><strong>UOM</strong></td>
                <td style="border:1px solid black;<? echo $hide_show;?>" align="center" rowspan="<?=$hdColSpan; ?>"><strong>Rate</strong></td>
                <td style="border:1px solid black;<? echo $hide_show;?>" align="center" rowspan="<?=$hdColSpan; ?>"><strong>Amount</strong></td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Gmts Color </strong> </td>
                <?
                foreach($nameArray_color  as $result_color)
                {
				$gmt_color=$color_library[$fab_gmt_color_arr[$result_color[csf('fabric_color_id')]]];
				 ?>
                <td align="center" style="border:1px solid black"><strong><? echo $gmt_color;//$color_library[$result_color[csf('fabric_color_id')]];?></strong></td>
                <?	}    ?>

            </tr>
             <tr>
                <td style="border:1px solid black"><strong>Labdip No</strong> </td>
                <?
                foreach($nameArray_color_labdib  as $row)
                {	     ?>
                <td align="center" style="border:1px solid black"><strong><? echo $row[csf('labdip_no')];?></strong></td>
                <?	}    ?>

            </tr>
            <? if($action=="show_trim_booking_report2") { ?>
            <tr>
                <td style="border:1px solid black"><strong>Remarks</strong> </td>
                <?
                foreach($nameArray_color  as $result_color)
                {
				 ?>
                <td align="center" style="border:1px solid black"><strong><? echo $result_color[csf('remark')];//$color_library[$result_color[csf('fabric_color_id')]];?></strong></td>
                <?	}     ?>

            </tr>
            <?
			}
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
				$i++;
				if($result_item[csf('process')]=='' || $result_item[csf('process')]==0)
				 {
				 $process_id="";
				 }
				 else
				 {
					 $process_id="and process=".$result_item[csf('process')]."";
				 }
			if($db_type==2)
			{
			$group_con="listagg(cast(yarn_count as varchar2(4000)),',') within group (order by yarn_count) AS yarn_count,
		listagg(cast(lot_no as varchar2(4000)),',') within group (order by lot_no) AS lot_no,
		listagg(cast(brand as varchar2(4000)),',') within group (order by brand) AS brand";
			}
			else
			{
				$group_con="group_concat(yarn_count) AS yarn_count,
		group_concat(lot_no) AS lot_no,
		group_concat(brand) AS brand";
			}
			$nameArray_item_description=sql_select( "select distinct description, rate, uom, dia_width, mc_dia, fin_gsm, slength,$group_con from wo_booking_dtls where booking_no=$txt_booking_no and sensitivity=1 and wo_qnty !=0  $process_id and status_active=1 and is_deleted=0 group by description, rate, uom, dia_width, mc_dia, fin_gsm, slength");
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $i; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $conversion_cost_head_array[$result_item[csf('process')]]; ?>
                </td>
                <?
                $color_tatal=array();
                $total_amount_as_per_gmts_color=0;
                foreach($nameArray_item_description as $result_itemdescription)
                {
                $item_desctiption_total=0;
				$yarn_count=implode(',',array_unique(explode(",",$result_itemdescription[csf('yarn_count')])));
				$lot_no=implode(',',array_unique(explode(",",$result_itemdescription[csf('lot_no')])));
				$brand=implode(',',array_unique(explode(",",$result_itemdescription[csf('brand')])));
                ?>
                <td style="border:1px solid black"><? echo rtrim($fabric_description_array[$result_itemdescription[csf('description')]],", "); ?> </td>
                   <td style="border:1px solid black" width="80"><? echo $yarn_count; ?> </td>
                    <td style="border:1px solid black" width="60"><? echo $lot_no; ?> </td>
                    <td style="border:1px solid black" width="80"><? echo $brand; ?> </td>
                 <!-- <td style="border:1px solid black"><? //echo rtrim($result_itemdescription[csf('labdip_no')],", "); ?> </td>-->
                <td style="border:1px solid black"><? //echo $result_itemdescription['brand_supplier']; ?> Booking Qnty </td>
                <?
                foreach($nameArray_color  as $result_color)
                {
					/*if($result_item[csf('process')]=='')
					{
						$process_cond="";
					}
					else if($result_item[csf('process')]==0)
					{
						$process_cond="and process=0";
					}
					else
					{
						$process_cond="and process=". $result_item[csf('process')]."";
					}
					if($db_type==0)
					{
						if($result_itemdescription[csf('slength')]=='') $slenth_con="";else $slenth_con="and slength='".$result_itemdescription[csf('slength')]."'";
						if($result_itemdescription[csf('description')]=='') $description_con="";else $description_con="and description='".$result_itemdescription[csf('description')]."'";
						if($result_itemdescription[csf('uom')]=='') $uom_con="";else $uom_con="and uom='".$result_itemdescription[csf('uom')]."'";
						if($result_itemdescription[csf('fabric_color_id')]!=0) $fcolor_con="and fabric_color_id='".$result_itemdescription[csf('fabric_color_id')]."'";else $fcolor_con="";
						if($result_itemdescription[csf('fin_gsm')]=='') $fin_gsmcon="";else $fin_gsmcon="and fin_gsm='".$result_itemdescription[csf('fin_gsm')]."'";
					}
					else
					{
						if($result_itemdescription[csf('slength')]=='') $slenth_con="and slength is null";else $slenth_con="and slength='".$result_itemdescription[csf('slength')]."'";
						if($result_itemdescription[csf('description')]=='') $description_con="and description is null";else $description_con="and description='".$result_itemdescription[csf('description')]."'";
						if($result_itemdescription[csf('uom')]=='') $uom_con="and uom is null";else $uom_con="and uom='".$result_itemdescription[csf('uom')]."'";
						if($result_itemdescription[csf('fabric_color_id')]=='') $fcolor_con="and fabric_color_id is null";else $fcolor_con="and fabric_color_id='".$result_itemdescription[csf('fabric_color_id')]."'";
						if($result_itemdescription[csf('fin_gsm')]=='') $fin_gsmcon="and fin_gsm is null";else $fin_gsmcon="and fin_gsm='".$result_itemdescription[csf('fin_gsm')]."'";
					}*/
					/*foreach($nameArray_color_size_qnty as $result_color_size_qnty)
					{
						*/?>
						<td style="border:1px solid black; text-align:right">
						<?
						/*if($result_color_size_qnty[csf('cons')]!="")
						{*/
							$color_cons=0;
							$color_cons=$arrOnlyColQty[$result_color[csf('fabric_color_id')]][$result_color[csf('gmts_color_id')]][$result_itemdescription[csf('description')]][$result_itemdescription[csf('slength')]][$result_itemdescription[csf('dia_width')]][$result_itemdescription[csf('fin_gsm')]][$result_itemdescription[csf('rate')]][$result_color[csf('remark')]]['cons'];
							//echo $result_color[csf('fabric_color_id')].'--'.$result_color[csf('gmts_color_id')].'--'.$result_itemdescription[csf('description')].'--'.$result_itemdescription[csf('slength')].'--'.$result_itemdescription[csf('dia_width')].'--'.$result_itemdescription[csf('fin_gsm')].'--'.$result_itemdescription[csf('rate')].'<br>';

							echo number_format($color_cons,2);

							$item_desctiption_total+=$color_cons;
							if (array_key_exists($result_color[csf('fabric_color_id')], $color_tatal))
							{
								$color_tatal[$result_color[csf('fabric_color_id')]]+=$color_cons;
							}
							else
							{
								$color_tatal[$result_color[csf('fabric_color_id')]]=$color_cons;
							}
						/*}
						else echo "";*/
						?>
						</td>
						<?
					//}
                }
                ?>

				<td style="border:1px solid black; text-align:right"><? echo number_format($item_desctiption_total,2); ?></td>
				<td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('mc_dia')]; ?></td>
				<td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('dia_width')]; ?></td>
				<td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('fin_gsm')]; ?></td>
				<td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('slength')]; ?></td>
				<td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$result_itemdescription[csf('uom')]]; ?></td>
				<td style="border:1px solid black; text-align:right;<? echo $hide_show;?>"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
				<td style="border:1px solid black; text-align:right;<? echo $hide_show;?>">
				<?
                $amount_as_per_gmts_color = $item_desctiption_total*  $result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="5"><strong> Item Total</strong></td>
                <?
                foreach($nameArray_color  as $result_color)
                {

                ?>
                <td style="border:1px solid black;  text-align:right">
                <?
                if($color_tatal[$result_color[fabric_color_id]] !='')
                {
                echo number_format($color_tatal[$result_color[fabric_color_id]],2);
                }
                ?>
                </td>
            <?
            }
            ?>
                <td style="border:1px solid black;  text-align:right"><? echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>

                <td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black; text-align:right;<? echo $hide_show;?>"><? //echo number_format(round($color_total),0); ?></td>
                <td style="border:1px solid black; text-align:right;<? echo $hide_show;?>">
                <?
                echo number_format($total_amount_as_per_gmts_color,4);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="<? echo count($nameArray_color)+(14-$col_span); ?>"><strong><? if($show_rate==1) echo '';else echo 'Total'; ?></strong></td>
                <td  style="border:1px solid black;  text-align:right"><?
				if($show_rate==1) echo '';
				else echo number_format($grand_total_as_per_gmts_color,4); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
            </tr>
        </table>
        <br/>
        <?
		}
		?>
        <!--==============================================AS PER GMTS COLOR END=========================================  -->

        <!--==============================================AS PER GMTS SIZE START=========================================  -->
		<?
		 //$nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=1");
		//echo "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=1";
       // $nameArray_color=sql_select( "select distinct fabric_color_id from wo_booking_dtls   where  booking_no=$txt_booking_no and sensitivity=1");


        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=2 and wo_qnty !=0");
        $nameArray_size=sql_select( "select distinct  item_size  as gmts_sizes from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=2 and wo_qnty !=0");
		if(count($nameArray_size)>0)
		{
        ?>

        <table border="0" align="left" cellpadding="0" width="100%" cellspacing="0" >
            <tr>
                <td colspan="<? echo count($nameArray_size)+11; ?>" align="">
                <strong>As Per Garments Size </strong>
                </td>

              <!-- <td colspan="8">
                   <table class="rpt_table"  cellpadding="0" width="100%" cellspacing="0">
                   <tr>
                   <td width="200"> <strong> Yarn Count: </strong></td> <td></td>
                    <td width="200">  <strong>Lot :  </strong></td> <td></td>
                    <td width="200"> <strong>Brand:  </strong></td> <td></td>
                   </tr>
                   </table>
               </td> -->
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Service Type</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black" width="80" ><strong>Y.Count</strong> </td>
                <td style="border:1px solid black" width="60"><strong>Lot</strong> </td>
                <td style="border:1px solid black" width="80"><strong>Brand</strong> </td>
                 <td style="border:1px solid black"><strong>Labdip No</strong> </td>
                <td style="border:1px solid black"><strong>Item size</strong> </td>
                <?
                foreach($nameArray_size  as $result_size)
                {	     ?>
                <td align="center" style="border:1px solid black"><strong><? echo $result_size[csf('gmts_sizes')];?></strong></td>
               <? }    ?>
                <td style="border:1px solid black" align="center"><strong>Total</strong></td>
                <td style="border:1px solid black" align="center"><strong>M/C Dia</strong></td>
                <td style="border:1px solid black" align="center"><strong>Fin Dia</strong></td>
                <td style="border:1px solid black" align="center"><strong>Fin GSM</strong></td>
                <td style="border:1px solid black" align="center"><strong>S/Length</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black;<? echo $hide_show;?>" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black;<? echo $hide_show;?>" align="center"><strong>Amount</strong></td>
            </tr>
            <?
			if($db_type==2)
			{
			$group_con="listagg(cast(yarn_count as varchar2(4000)),',') within group (order by yarn_count) AS yarn_count,
	listagg(cast(lot_no as varchar2(4000)),',') within group (order by lot_no) AS lot_no,
	listagg(cast(brand as varchar2(4000)),',') within group (order by brand) AS brand";
			}
			else
			{
				$group_con="group_concat(yarn_count) AS yarn_count,
		group_concat(lot_no) AS lot_no,
		group_concat(brand) AS brand";
			}
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
			$i++;
            $nameArray_item_description=sql_select( "select distinct description,rate,uom,dia_width,labdip_no,mc_dia,fin_gsm,slength,$group_con from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=2 and process=".$result_item[csf('process')]." and wo_qnty !=0 group by description,rate,uom,dia_width,labdip_no,mc_dia,fin_gsm,slength ");
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $i ; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $conversion_cost_head_array[$result_item[csf('process')]]; ?>
                </td>
                <?
                $color_tatal=array();
                $total_amount_as_per_gmts_color=0;
                foreach($nameArray_item_description as $result_itemdescription)
                {
                $item_desctiption_total=0;
				$yarn_count=implode(',',array_unique(explode(",",$result_itemdescription[csf('yarn_count')])));
				$lot_no=implode(',',array_unique(explode(",",$result_itemdescription[csf('lot_no')])));
				$brand=implode(',',array_unique(explode(",",$result_itemdescription[csf('brand')])));
                ?>
                <td style="border:1px solid black"><? echo $fabric_description_array[$result_itemdescription[csf('description')]]; ?> </td>
                <td style="border:1px solid black" width="80"><? echo $yarn_count; ?> </td>
                <td style="border:1px solid black" width="60"><? echo $lot_no; ?> </td>
                <td style="border:1px solid black" width="80"><? echo $brand; ?> </td>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('labdip_no')]; ?> </td>
                <td style="border:1px solid black"><? //echo $result_itemdescription['brand_supplier']; ?> Booking Qnty  </td>
                <?
				if($db_type==0)
					{
					if($result_itemdescription[csf('dia_width')]=='') $dia_width_con="";else $dia_width_con="and dia_width='".$result_itemdescription[csf('dia_width')]."'";
					if($result_itemdescription[csf('description')]=='') $description_con="";else $description_con="and description='".$result_itemdescription[csf('description')]."'";
					if($result_itemdescription[csf('uom')]=='') $uom_con="";else $uom_con="and uom='".$result_itemdescription[csf('uom')]."'";
					if($result_itemdescription[csf('fabric_color_id')]!=0) $fcolor_con="and fabric_color_id='".$result_itemdescription[csf('fabric_color_id')]."'";else $fcolor_con="";
					//if($result_itemdescription[csf('fabric_color_id')]!=0) $fcolor_con="and fabric_color_id='".$result_itemdescription[csf('fabric_color_id')]."'";else $fcolor_con="";
					if($result_itemdescription[csf('item_size')]=='') $item_size_con="";else $item_size_con="and item_size='".$result_itemdescription[csf('gmts_sizes')]."'";
					}
					else
					{
					if($result_itemdescription[csf('dia_width')]=='') $dia_width_con="and slength is null";else $dia_width_con="and dia_width='".$result_itemdescription[csf('dia_width')]."'";
					if($result_itemdescription[csf('description')]=='') $description_con="and description is null";else $description_con="and description='".$result_itemdescription[csf('description')]."'";
					if($result_itemdescription[csf('uom')]=='') $uom_con="and uom is null";else $uom_con="and uom='".$result_itemdescription[csf('uom')]."'";
					//if($result_itemdescription[csf('fabric_color_id')]=='') $fcolor_con="and fabric_color_id is null";else $fcolor_con="and fabric_color_id='".$result_itemdescription[csf('fabric_color_id')]."'";
					if($result_size[csf('gmts_sizes')]=='' || $result_size[csf('gmts_sizes')]==0) $item_size_con="and item_size is null";else $item_size_con="and item_size='".$result_size[csf('gmts_sizes')]."'";
					}
					foreach($nameArray_size  as $result_size)
					{
					/*$nameArray_size_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls   where   booking_no=$txt_booking_no and sensitivity=2 and process=". $result_item[csf('process')]." and description='". $result_itemdescription[csf('description')]."' and rate='". $result_itemdescription[csf('rate')]."' and uom='". $result_itemdescription[csf('uom')]."' and dia_width='". $result_itemdescription[csf('dia_width')]."' and item_size='".$result_size[csf('gmts_sizes')]."'"); */
					$nameArray_size_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls   where   booking_no=$txt_booking_no and sensitivity=2 and process=". $result_item[csf('process')]."  and rate='". $result_itemdescription[csf('rate')]."'  $description_con $uom_con  $dia_width_con $item_size_con");

					foreach($nameArray_size_size_qnty as $result_size_size_qnty)
					{
					?>
					<td style="border:1px solid black; text-align:right">
					<?
					if($result_size_size_qnty[csf('cons')]!= "")
					{
					echo number_format($result_size_size_qnty[csf('cons')],2);
					$item_desctiption_total += $result_size_size_qnty[csf('cons')] ;
					if (array_key_exists($result_size[csf('gmts_sizes')], $color_tatal))
					{
					$color_tatal[$result_size[csf('gmts_sizes')]]+=$result_size_size_qnty[csf('cons')];
					}
					else
					{
					$color_tatal[$result_size[csf('gmts_sizes')]]=$result_size_size_qnty[csf('cons')];
					}
					}
					else echo "";
                ?>
                </td>
                <?
                }
                }
                ?>

                <td style="border:1px solid black; text-align:right"><? echo number_format($item_desctiption_total,2); ?></td>
                 <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('mc_dia')]; ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('dia_width')]; ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('fin_gsm')]; ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('slength')]; ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$result_itemdescription[csf('uom')]]; ?></td>
                <td style="border:1px solid black; text-align:right;<? echo $hide_show;?>"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right;<? echo $hide_show;?>">
                <?
                $amount_as_per_gmts_color = $item_desctiption_total*$result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="6"><strong> Item Total</strong></td>
                <?
                foreach($nameArray_size  as $result_size)
                {

                ?>
                <td style="border:1px solid black;  text-align:right">
                <?
                if($color_tatal[$result_size[gmts_sizes]] !='')
                {
                echo number_format($color_tatal[$result_size[gmts_sizes]],2);
                }
                ?>
                </td>
            <?
            }
            ?>
                <td style="border:1px solid black;  text-align:right"><? echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
                 <td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black; text-align:right"><? //echo number_format(round($color_total),0); ?></td>
                <td style="border:1px solid black; text-align:right">
                <?
                echo number_format($total_amount_as_per_gmts_color,4);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="<? echo count($nameArray_size)+(15-$col_span); ?>"><strong><? if($show_rate==1) echo '';else echo 'Total'; ?></strong></td>
                <td  style="border:1px solid black;  text-align:right"><?
				if($show_rate==1) echo ' ';
				else echo number_format($grand_total_as_per_gmts_color,4); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
            </tr>
        </table>
        <br/>
        <?
		}
		?>
        <!--==============================================AS PER SIZE  END=========================================  -->

         <!--==============================================AS PER CONTRAST COLOR START=========================================  -->
		<?
		//$nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=2");
       // $nameArray_size=sql_select( "select distinct  item_size  as gmts_sizes from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=2");
	//$gmtcolor_library=return_library_array( "select b.item_color,b.color_number_id from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no=$txt_booking_no and a.sensitivity=3 and a.status_active=1 and a.is_deleted=0", "item_color", "color_number_id"  );
	  // echo "select distinct process,fabric_color_id from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=3 and wo_qnty !=0 order by fabric_color_id";
	// echo $wo_pre_cost_fab_co_color_sql="select b.gmts_color_id,b.contrast_color_id,b.pre_cost_fabric_cost_dtls_id as fab_dtls_id from wo_pre_cos_fab_co_color_dtls b,wo_booking_dtls a  where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and b.job_no='$job_num'";
	 // echo $wo_pre_cost_fab_co_color_sql="select b.gmts_color_id,b.contrast_color_id,b.pre_cost_fabric_cost_dtls_id as fab_dtls_id from wo_pre_cos_fab_co_color_dtls b  where   b.job_no='$job_num'";
	/*$wo_pre_fab_co_color_result=sql_select($wo_pre_cost_fab_co_color_sql);
	foreach( $wo_pre_fab_co_color_result as $row)
	{
		$contrast_color_arr[$row[csf('fab_dtls_id')]][$row[csf('gmts_color_id')]]['contrast_color']=$row[csf('contrast_color_id')];
	}
	*/
       $only_color_size_qnty=sql_select( "select fabric_color_id, gmts_color_id, slength, dia_width, fin_gsm, rate, description, sum(wo_qnty) as cons from wo_booking_dtls where booking_no=$txt_booking_no and sensitivity=3 and status_active=1 and is_deleted =0 and wo_qnty>0 group by fabric_color_id, gmts_color_id, slength, dia_width, fin_gsm, rate, description");
		foreach($only_color_size_qnty as $OnlyColorSizeQnty)
		{
		  if($OnlyColorSizeQnty[csf('cons')]){
		 	$arrOnlyColQty[$OnlyColorSizeQnty[csf('fabric_color_id')]][$OnlyColorSizeQnty[csf('gmts_color_id')]][$OnlyColorSizeQnty[csf('description')]][$OnlyColorSizeQnty[csf('slength')]][$OnlyColorSizeQnty[csf('dia_width')]][$OnlyColorSizeQnty[csf('fin_gsm')]][$OnlyColorSizeQnty[csf('rate')]]['cons']=$OnlyColorSizeQnty[csf('cons')];
		  }
		}
		
	    $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=3 and wo_qnty !=0 ");
        $nameArray_color=sql_select("select distinct fabric_color_id as color_number_id, gmts_color_id, remark from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=3 and wo_qnty !=0 order by fabric_color_id");
		$nameArray_color_labdib=sql_select( "select distinct labdip_no as labdip_no from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=3 and wo_qnty !=0 ");

		if(count($nameArray_color)>0)
		{

        ?>
        <table border="0" align="left" cellpadding="0" width="100%" cellspacing="0" >
            <tr>
                <td colspan="<? echo count($nameArray_color)+10; ?>" align="">
                <strong>Contrast Color</strong>
                </td>

              <!-- <td colspan="7">
                   <table class="rpt_table"  cellpadding="0" width="100%" cellspacing="0">
                   <tr>
                   <td width="200"> <strong> Yarn Count: </strong></td> <td></td>
                    <td width="200">  <strong>Lot :  </strong></td> <td></td>
                    <td width="200"> <strong>Brand:  </strong></td> <td></td>
                   </tr>
                   </table>
               </td> -->
            </tr>
            <tr>
                <td style="border:1px solid black"  rowspan="<?=$hdColSpan; ?>"><strong>Sl</strong> </td>
                <td style="border:1px solid black"  rowspan="<?=$hdColSpan; ?>"><strong>Service Type</strong> </td>
                <td style="border:1px solid black"  rowspan="<?=$hdColSpan; ?>"><strong>Item Description</strong> </td>
                <td style="border:1px solid black" width="80"  rowspan="<?=$hdColSpan; ?>"><strong>Y.Count</strong> </td>
                <td style="border:1px solid black" width="60"  rowspan="<?=$hdColSpan; ?>"><strong>Lot</strong> </td>
                <td style="border:1px solid black" width="80" rowspan="<?=$hdColSpan; ?>"><strong>Brand</strong> </td>
               <!-- <td style="border:1px solid black"  rowspan="<? //=$hdColSpan; ?>"><strong>Labdip No</strong> </td>-->
                <td style="border:1px solid black"><strong>Item Color</strong> </td>
                <?
                foreach($nameArray_color  as $result_color)
                {
					//$color_id=$contrast_color_arr[$result_color[csf('pre_cost_fabric_cost_dtls_id')]][$result_color[csf('color_number_id')]]['contrast_color'];	     ?>
                <td align="center" style="border:1px solid black"><strong><? echo $color_library[$result_color[csf('color_number_id')]];?></strong></td>
                <?	}    ?>
                <td style="border:1px solid black" align="center"  rowspan="<?=$hdColSpan; ?>"><strong>Total</strong></td>
                <td style="border:1px solid black" align="center"  rowspan="<?=$hdColSpan; ?>"><strong>M/C Dia</strong></td>
                <td style="border:1px solid black" align="center"  rowspan="<?=$hdColSpan; ?>"><strong>Fin Dia</strong></td>
                <td style="border:1px solid black" align="center"  rowspan="<?=$hdColSpan; ?>"><strong>Fin GSM</strong></td>
                <td style="border:1px solid black" align="center"  rowspan="<?=$hdColSpan; ?>"><strong>S/Length</strong></td>
                <td style="border:1px solid black" align="center"  rowspan="<?=$hdColSpan; ?>"><strong>UOM</strong></td>
                <td style="border:1px solid black;<? echo $hide_show;?>" align="center"  rowspan="<?=$hdColSpan; ?>"><strong>Rate</strong></td>
                <td style="border:1px solid black;<? echo $hide_show;?>" align="center"  rowspan="<?=$hdColSpan; ?>"><strong>Amount</strong></td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Gmts Color</strong> </td>
                <?
                foreach($nameArray_color  as $result_color)
                {	     ?>
                <td align="center" style="border:1px solid black"><strong><? echo $color_library[$result_color[csf('gmts_color_id')]];?></strong></td>
                <?	}    ?>

            </tr>
             <tr>
                <td style="border:1px solid black"><strong>Labdip No</strong> </td>
                <?
                foreach($nameArray_color_labdib  as $row)
                {	     ?>
                <td align="center" style="border:1px solid black"><strong><? echo $row[csf('labdip_no')];?></strong></td>
                <?	}    ?>

            </tr>
            <? if($action=="show_trim_booking_report2") { ?>
            <tr>
                <td style="border:1px solid black"><strong>Remarks</strong> </td>
                <?
                foreach($nameArray_color  as $result_color)
                {
				 ?>
                <td align="center" style="border:1px solid black"><strong><? echo $result_color[csf('remark')];//$color_library[$result_color[csf('fabric_color_id')]];?></strong></td>
                <?	}     ?>

            </tr>
            <?
			}
			if($db_type==2)
			{
			$group_con="listagg(cast(yarn_count as varchar2(4000)),',') within group (order by yarn_count) AS yarn_count,
		listagg(cast(lot_no as varchar2(4000)),',') within group (order by lot_no) AS lot_no,
		listagg(cast(brand as varchar2(4000)),',') within group (order by brand) AS brand";
			}
			else
			{
				$group_con="group_concat(yarn_count) AS yarn_count,
		group_concat(lot_no) AS lot_no,
		group_concat(brand) AS brand";
			}
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
				$i++;
				//echo "select distinct description,rate,labdip_no,uom,mc_dia,fin_gsm,slength,fabric_color_id,dia_width from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=3 and process=".$result_item[csf('process')]." and wo_qnty !=0 order by fabric_color_id";

          /* echo "select distinct description,rate,uom,mc_dia,fin_gsm,slength,fabric_color_id,dia_width,$group_con from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=3 and process=".$result_item[csf('process')]." and wo_qnty !=0 group by description,rate,uom,mc_dia,fin_gsm,slength,fabric_color_id,dia_width order by fabric_color_id";*/
		    $nameArray_item_description=sql_select( "select distinct description,rate,uom,mc_dia,fin_gsm,slength,dia_width,$group_con from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=3 and process=".$result_item[csf('process')]." and wo_qnty !=0 group by description,rate,uom,mc_dia,fin_gsm,slength,dia_width order by description");
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $i; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $conversion_cost_head_array[$result_item[csf('process')]]; ?>
                </td>
                <?
                $color_tatal=array();
                $total_amount_as_per_gmts_color=0;
                foreach($nameArray_item_description as $result_itemdescription)
                {
                $item_desctiption_total=0;
				$yarn_count=implode(',',array_unique(explode(",",$result_itemdescription[csf('yarn_count')])));
				$lot_no=implode(',',array_unique(explode(",",$result_itemdescription[csf('lot_no')])));
				$brand=implode(',',array_unique(explode(",",$result_itemdescription[csf('brand')])));
				//echo 'BB';
                ?>
                <td style="border:1px solid black"><? echo $fabric_description_array[$result_itemdescription[csf('description')]]; ?> </td>
                 <td style="border:1px solid black" width="80"><? echo $yarn_count; ?> </td>
                 <td style="border:1px solid black" width="60"><? echo $lot_no; ?> </td>
                 <td style="border:1px solid black" width="80"><? echo $brand; ?> </td>
                <td style="border:1px solid black"><? //echo $result_itemdescription['brand_supplier']; ?> Booking Qnty  </td>
                <?
				if($db_type==0) $dia_con="dia_width";
				else $dia_con="nvl(dia_width,0)";
				if($db_type==0)
					{
					if($result_itemdescription[csf('dia_width')]=='') $dia_width_con="";else $dia_width_con="and dia_width='".$result_itemdescription[csf('dia_width')]."'";
					if($result_itemdescription[csf('description')]=='') $description_con="";else $description_con="and description='".$result_itemdescription[csf('description')]."'";
					if($result_itemdescription[csf('uom')]=='') $uom_con="";else $uom_con="and uom='".$result_itemdescription[csf('uom')]."'";

					if($result_color[csf('fabric_color_id')]!=0) $fcolor_con="and fabric_color_id='".$result_color[csf('color_number_id')]."'";else $fcolor_con="";
					if($result_itemdescription[csf('item_size')]=='') $item_size_con="";else $item_size_con="and item_size='".$result_itemdescription[csf('item_size')]."'";
					}
					else
					{
					if($result_itemdescription[csf('dia_width')]=='') $dia_width_con="and slength is null";else $dia_width_con="and dia_width='".$result_itemdescription[csf('dia_width')]."'";
					if($result_itemdescription[csf('description')]=='') $description_con="and description is null";else $description_con="and description='".$result_itemdescription[csf('description')]."'";
					if($result_itemdescription[csf('uom')]=='') $uom_con="and uom is null";else $uom_con="and uom='".$result_itemdescription[csf('uom')]."'";
					//if($result_color[csf('color_number_id')]=='') $fcolor_con="and fabric_color_id is null";else $fcolor_con="and fabric_color_id='".$result_color[csf('color_number_id')]."'";
					if($result_itemdescription[csf('item_size')]=='') $item_size_con="and item_size is null";else $item_size_con="and dia_width='".$result_itemdescription[csf('item_size')]."'";
					}
                foreach($nameArray_color  as $result_color)
                {
					if($result_itemdescription[csf('dia_width')]=='') $dia_width=0;else $dia_width=$result_itemdescription[csf('dia_width')];
                /*$nameArray_color_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls    where   booking_no=$txt_booking_no and sensitivity=3 and process=". $result_item[csf('process')]." and description='". $result_itemdescription[csf('description')]."' and rate='". $result_itemdescription[csf('rate')]."' and uom='". $result_itemdescription[csf('uom')]."' and $dia_con='".$dia_width."' and fabric_color_id=".$result_color[csf('color_number_id')]."  ");    */
			//	$nameArray_color_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls    where   booking_no=$txt_booking_no and sensitivity=3 and process=". $result_item[csf('process')]." and rate='". $result_itemdescription[csf('rate')]."'  and gmts_color_id=".$result_color[csf('gmts_color_id')]." and wo_qnty>0  $dia_width_con $description_con $uom_con $fcolor_con order by fabric_color_id ");
				$color_size_qnty=$arrOnlyColQty[$result_color[csf('color_number_id')]][$result_color[csf('gmts_color_id')]][$result_itemdescription[csf('description')]][$result_itemdescription[csf('slength')]][$result_itemdescription[csf('dia_width')]][$result_itemdescription[csf('fin_gsm')]][$result_itemdescription[csf('rate')]]['cons'];
				//echo  "select sum(wo_qnty) as cons from wo_booking_dtls    where   booking_no=$txt_booking_no and sensitivity=3 and process=". $result_item[csf('process')]." and rate='". $result_itemdescription[csf('rate')]."'  and gmts_color_id=".$result_color[csf('gmts_color_id')]." and wo_qnty>0  $dia_width_con $description_con $uom_con $fcolor_con  order by fabric_color_id";

             //   foreach($nameArray_color_size_qnty as $result_color_size_qnty)
               // {
                ?>
                <td style="border:1px solid black; text-align:right">

                <?
                if($color_size_qnty!= "")
                {
                echo number_format($color_size_qnty,2);
                $item_desctiption_total += $color_size_qnty ;
                if (array_key_exists($result_color[csf('gmts_color_id')], $color_tatal))
                {
                $color_tatal[$result_color[csf('gmts_color_id')]]+=$color_size_qnty;
                }
                else
                {
                $color_tatal[$result_color[csf('gmts_color_id')]]=$color_size_qnty;
                }
                }
                else echo "";
                ?>
                </td>
                <?
                }
              //  }
                ?>
                <td style="border:1px solid black; text-align:right"><? echo number_format($item_desctiption_total,2); ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('mc_dia')]; ?></td>
                <td style="border:1px solid black; text-align:center"><? echo $result_itemdescription[csf('dia_width')]; ?></td>
               <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('fin_gsm')]; ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('slength')]; ?></td>
                <td style="border:1px solid black; text-align:center"><? echo $unit_of_measurement[$result_itemdescription[csf('uom')]]; ?></td>
                <td style="border:1px solid black; text-align:right;<? echo $hide_show;?>"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right;<? echo $hide_show;?>">
                <?
                $amount_as_per_gmts_color = $item_desctiption_total*$result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="5"><strong> Item Total</strong></td>
                <?
                foreach($nameArray_color  as $result_color)
                {

                ?>
                <td style="border:1px solid black;  text-align:right">
                <?
                if($color_tatal[$result_color[csf('gmts_color_id')]] !='')
                {
                echo number_format($color_tatal[$result_color[csf('gmts_color_id')]],2);
                }
                ?>
                </td>
            <?
            }
            ?>
                <td style="border:1px solid black;  text-align:right"><? echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black;text-align:center"></td>
                <td style="border:1px solid black;text-align:center"></td>
                <td style="border:1px solid black;text-align:center"></td>
                <td style="border:1px solid black;text-align:center"></td>
                 <td style="border:1px solid black;text-align:center"></td>


                <td style="border:1px solid black; text-align:right;<? echo $hide_show;?>"><? //echo number_format(round($color_total),0); ?></td>
                <td style="border:1px solid black; text-align:right;<? echo $hide_show;?>">
                <?
                echo number_format($total_amount_as_per_gmts_color,4);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="<? echo count($nameArray_color)+(14-$col_span); ?>"><strong><? if($show_rate==1) echo '';else echo 'Total'; ?></strong></td>
                <td  style="border:1px solid black;  text-align:right"><?
				if($show_rate==1) echo '';
				else echo number_format($grand_total_as_per_gmts_color,4); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
            </tr>
        </table>
        <br/>
        <?
		}
		?>
        <!--==============================================AS PER CONTRAST COLOR END=========================================  -->

        <!--==============================================AS PER GMTS Color & SIZE START=========================================  -->
		<?
		//$nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=2");
       // $nameArray_size=sql_select( "select distinct  item_size  as gmts_sizes from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=2");
	   //$nameArray_color=sql_select( "select distinct fabric_color_id as color_number_id from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=3");

        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=4 and wo_qnty !=0 and is_deleted=0 and status_active=1");
        $nameArray_size=sql_select( "select distinct item_size  as gmts_sizes from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=4 and wo_qnty !=0 and is_deleted=0 and status_active=1");
	    $nameArray_color=sql_select( "select distinct fabric_color_id as color_number_id from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=4 and wo_qnty !=0 and is_deleted=0 and status_active=1");

		if(count($nameArray_size)>0)
		{
        ?>

        <table border="0" align="left" cellpadding="0" width="100%" cellspacing="0" >
            <tr>
                <td colspan="<? echo count($nameArray_size)+12; ?>" align="">
                <strong>Color & size sensitive </strong>
                </td>

              <!-- <td colspan="8">
                   <table class="rpt_table"  cellpadding="0" width="100%" cellspacing="0">
                   <tr>
                   <td width="200"> <strong> Yarn Count: </strong></td> <td></td>
                    <td width="200">  <strong>Lot :  </strong></td> <td></td>
                    <td width="200"> <strong>Brand:  </strong></td> <td></td>
                   </tr>
                   </table>
               </td> -->
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Service Type</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black" width="80"><strong>Y.Count</strong> </td>
                <td style="border:1px solid black" width="60"><strong>Lot</strong> </td>
                <td style="border:1px solid black" width="80"><strong>Brand</strong> </td>
                <td style="border:1px solid black"><strong>Labdip No</strong> </td>

                <td style="border:1px solid black"><strong>Item Color</strong> </td>
                <?
                foreach($nameArray_size  as $result_size)
                {	     ?>
                <td align="center" style="border:1px solid black"><strong><? echo $result_size[csf('gmts_sizes')];?></strong></td>
                <?	}    ?>
                <td style="border:1px solid black" align="center"><strong>Total</strong></td>
                <td style="border:1px solid black" align="center"><strong>M/C Dia</strong></td>
                <td style="border:1px solid black" align="center"><strong>Fin Dia</strong></td>
                <td style="border:1px solid black" align="center"><strong>Fin GSM</strong></td>
                <td style="border:1px solid black" align="center"><strong>S/Length</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black;<? echo $hide_show;?>" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black;<? echo $hide_show;?>" align="center"><strong>Amount</strong></td>
            </tr>

            <?
			if($db_type==2)
			{
			$group_con="listagg(cast(yarn_count as varchar2(4000)),',') within group (order by yarn_count) AS yarn_count,
		listagg(cast(lot_no as varchar2(4000)),',') within group (order by lot_no) AS lot_no,
		listagg(cast(brand as varchar2(4000)),',') within group (order by brand) AS brand";
			}
			else
			{
				$group_con="group_concat(yarn_count) AS yarn_count,
		group_concat(lot_no) AS lot_no,
		group_concat(brand) AS brand";
			}

			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {

			$i++;
            $nameArray_item_description=sql_select( "select distinct description,mc_dia,fin_gsm,slength,rate,uom,dia_width,$group_con from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=4 and process=".$result_item[csf('process')]." and wo_qnty !=0 group by description,labdip_no,mc_dia,fin_gsm,slength,rate,uom,dia_width order by description");
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo   count($nameArray_item_description)+1; ?>">
                <? echo $i ; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)*1+1; ?>">
                <? echo $conversion_cost_head_array[$result_item[csf('process')]]; ?>
                </td>
                <?
                $color_tatal=array();
                $total_amount_as_per_gmts_color=0;
                foreach($nameArray_item_description as $result_itemdescription)
                {
					$yarn_count=implode(',',array_unique(explode(",",$result_itemdescription[csf('yarn_count')])));
					$lot_no=implode(',',array_unique(explode(",",$result_itemdescription[csf('lot_no')])));
					$brand=implode(',',array_unique(explode(",",$result_itemdescription[csf('brand')])));
					?>
                    <td style="border:1px solid black" rowspan="<? echo count($nameArray_color); ?>"><? echo $fabric_description_array[$result_itemdescription[csf('description')]]; ?> </td>
                    <td style="border:1px solid black" width="80"><? echo $yarn_count; ?> </td>
                    <td style="border:1px solid black" width="60"><? echo $lot_no; ?> </td>
                    <td style="border:1px solid black" width="80"><? echo $brand; ?> </td>
                    <td style="border:1px solid black"><? echo $result_itemdescription[csf('labdip_no')]; ?> </td>
                    <td style="border:1px solid black" rowspan="<? //echo count($nameArray_color); ?>"><? //echo $result_itemdescription['brand_supplier']; ?>Booking Qnty </td>

                    <?
					if($db_type==0)
					{
					if($result_itemdescription[csf('dia_width')]=='') $dia_width_con="";else $dia_width_con="and dia_width='".$result_itemdescription[csf('dia_width')]."'";
					if($result_itemdescription[csf('description')]=='') $description_con="";else $description_con="and description='".$result_itemdescription[csf('description')]."'";
					if($result_itemdescription[csf('uom')]=='') $uom_con="";else $uom_con="and uom='".$result_itemdescription[csf('uom')]."'";

					if($result_color[csf('fabric_color_id')]!=0) $fcolor_con="and fabric_color_id='".$result_color[csf('fabric_color_id')]."'";else $fcolor_con="";
					if($result_itemdescription[csf('item_size')]=='') $item_size_con="";else $item_size_con="and item_size='".$result_itemdescription[csf('gmts_sizes')]."'";
					}
					else
					{
					if($result_itemdescription[csf('dia_width')]=='') $dia_width_con="and slength is null";else $dia_width_con="and dia_width='".$result_itemdescription[csf('dia_width')]."'";
					if($result_itemdescription[csf('description')]=='') $description_con="and description is null";else $description_con="and description='".$result_itemdescription[csf('description')]."'";
					if($result_itemdescription[csf('uom')]=='') $uom_con="and uom is null";else $uom_con="and uom='".$result_itemdescription[csf('uom')]."'";
					if($result_color[csf('color_number_id')]=='') $fcolor_con="and color_number_id is null";else $fcolor_con="and color_number_id='".$result_color[csf('color_number_id')]."'";
					if($result_size[csf('gmts_sizes')]=='') $item_size_con="and item_size is null";else $item_size_con="and item_size='".$result_size[csf('gmts_sizes')]."'";
					}
                //$item_desctiption_total=0;
				foreach($nameArray_color as $result_color)
                {
					 $item_desctiption_total=0;
                ?>

                <!--<td style="border:1px solid black">DFF<? //echo $color_library[$result_color[csf('color_number_id')]]; ?> </td>-->
                <?
                foreach($nameArray_size  as $result_size)
                {
					//echo 'ssddd';
               /* $nameArray_size_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=4 and process=". $result_item[csf('process')]." and  description='". $result_itemdescription[csf('description')]."' and  item_size='".$result_size[csf('gmts_sizes')]."' and rate='". $result_itemdescription[csf('rate')]."' and uom='". $result_itemdescription[csf('uom')]."' and dia_width='". $result_itemdescription[csf('dia_width')]."' and fabric_color_id=".$result_color[csf('color_number_id')].""); */
				 $nameArray_size_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=4 and process=". $result_item[csf('process')]." and rate='". $result_itemdescription[csf('rate')]."' $dia_width_con $description_con $uom_con $fcolor_con $item_size_con");
                foreach($nameArray_size_size_qnty as $result_size_size_qnty)
                {
                ?>
                <td style="border:1px solid black; text-align:right">
                <?
                if($result_size_size_qnty[csf('cons')]!= "")
                {
                echo number_format($result_size_size_qnty[csf('cons')],2);
                $item_desctiption_total += $result_size_size_qnty[csf('cons')] ;
                if (array_key_exists($result_size[csf('color_number_id')], $color_tatal))
                {
                $color_tatal[$result_size[csf('color_number_id')]]+=$result_size_size_qnty[csf('cons')];
                }
                else
                {
                $color_tatal[$result_size[csf('color_number_id')]]=$result_size_size_qnty[csf('cons')];
                }
                }
                else echo "";
                ?>
                </td>
                <?
                }
                }
                ?>

                <td style="border:1px solid black; text-align:right"><? echo number_format($item_desctiption_total,2); ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('mc_dia')]; ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('dia_width')]; ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('fin_gsm')]; ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('slength')]; ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$result_itemdescription[csf('uom')]]; ?></td>
                <td style="border:1px solid black; text-align:right;<? echo $hide_show;?>"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right;<? echo $hide_show;?>">
                <?
                $amount_as_per_gmts_color = $item_desctiption_total*$result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
			}
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="8"><strong> Item Total</strong></td>

                <?
                foreach($nameArray_size  as $result_size)
                {

                ?>
                <td style="border:1px solid black;  text-align:right">
                <?
                if($color_tatal[$result_size[csf('gmts_sizes')]] !='')
                {
                echo number_format($color_tatal[$result_size[csf('gmts_sizes')]],2);
                }
                ?>
                </td>
            <?
            }
            ?>
                <td style="border:1px solid black;  text-align:right"><? echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black; text-align:right;<? echo $hide_show;?>"><? //echo number_format(round($color_total),0); ?></td>
                <td style="border:1px solid black; text-align:right;<? echo $hide_show;?>" >
                <?
                echo number_format($total_amount_as_per_gmts_color,4);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="<? echo count($nameArray_size)+(15-$col_span); ?>"><strong><? if($show_rate==1) echo '';else echo 'Total'; ?></strong></td>
                <td  style="border:1px solid black;  text-align:right"><?
				if($show_rate==1) echo ' ';else echo number_format($grand_total_as_per_gmts_color,4); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
            </tr>
        </table>
        <br/>
        <?
		}
		?>
        <!--==============================================AS PER Color & SIZE  END=========================================  -->


         <!--==============================================NO NENSITIBITY START=========================================  -->
		<?
        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=0 and wo_qnty !=0 and is_deleted=0 and status_active=1");
        //$nameArray_color=sql_select( "select distinct b.color_number_id from wo_trims_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=$txt_booking_no and a.sensitivity=1");
		$nameArray_color= array();
		if(count($nameArray_item)>0)
		{
        ?>
        <table border="0" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" >
            <tr>
                <td align="" colspan="11" >
                <strong>No Sensitivity</strong>
                </td>

               <!--<td colspan="6">
                   <table class="rpt_table"  cellpadding="0" width="100%" cellspacing="0">
                   <tr>
                   <td width="200"> <strong> Yarn Count: </strong></td> <td></td>
                    <td width="200">  <strong>Lot :  </strong></td> <td></td>
                    <td width="200"> <strong>Brand:  </strong></td> <td></td>
                   </tr>
                   </table>
               </td> -->
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Service Type</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black" width="80"><strong>Y.Count</strong> </td>
                <td style="border:1px solid black" width="60"><strong>Lot</strong> </td>
                <td style="border:1px solid black" width="80"><strong>Brand</strong> </td>
                <td style="border:1px solid black"><strong>Labdip No</strong> </td>
                <td style="border:1px solid black"><strong></strong> </td>
                <td style="border:1px solid black" align="center"><strong> Qnty</strong></td>
                <td style="border:1px solid black" align="center"><strong>M/C Dia</strong></td>
                <td style="border:1px solid black" align="center"><strong>Fin Dia</strong></td>
                <td style="border:1px solid black" align="center"><strong>Fin GSM</strong></td>
                <td style="border:1px solid black" align="center"><strong>S/Length</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black;<? echo $hide_show;?>" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black;<? echo $hide_show;?>" align="center"><strong>Amount</strong></td>
            </tr>
            <?
			if($db_type==2)
			{
			$group_con="listagg(cast(yarn_count as varchar2(4000)),',') within group (order by yarn_count) AS yarn_count,
		listagg(cast(lot_no as varchar2(4000)),',') within group (order by lot_no) AS lot_no,
		listagg(cast(brand as varchar2(4000)),',') within group (order by brand) AS brand";
			}
			else
			{
				$group_con="group_concat(yarn_count) AS yarn_count,
		group_concat(lot_no) AS lot_no,
		group_concat(brand) AS brand";
			}
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
				$i++;
            $nameArray_item_description=sql_select( "select distinct description,labdip_no,rate,mc_dia,fin_gsm,slength,uom,dia_width,$group_con from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=0 and process=".$result_item[csf('process')]." and wo_qnty !=0 group by description,labdip_no,rate,mc_dia,fin_gsm,slength,uom,dia_width");
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $i; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $conversion_cost_head_array[$result_item[csf('process')]]; ?>
                </td>
                <?
                $color_tatal=0;
                $total_amount_as_per_gmts_color=0;
                foreach($nameArray_item_description as $result_itemdescription)
                {
                $item_desctiption_total=0;
				$yarn_count=implode(',',array_unique(explode(",",$result_itemdescription[csf('yarn_count')])));
				$lot_no=implode(',',array_unique(explode(",",$result_itemdescription[csf('lot_no')])));
				$brand=implode(',',array_unique(explode(",",$result_itemdescription[csf('brand')])));
                ?>
                <td style="border:1px solid black"><? echo $fabric_description_array[$result_itemdescription[csf('description')]]; ?> </td>
                <td style="border:1px solid black" width="80"><? echo $yarn_count; ?> </td>
                <td style="border:1px solid black" width="60"><?  echo $lot_no; ?> </td>
                <td style="border:1px solid black" width="80"><? echo $brand; ?> </td>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('labdip_no')]; ?> </td>
                <td style="border:1px solid black"><? //echo $result_itemdescription['brand_supplier']; ?>Booking Qnty</td>
                <?
				if($db_type==0)
					{
					if($result_itemdescription[csf('dia_width')]=='') $dia_width_con="";else $dia_width_con="and dia_width='".$result_itemdescription[csf('dia_width')]."'";
					if($result_itemdescription[csf('description')]=='') $description_con="";else $description_con="and description='".$result_itemdescription[csf('description')]."'";
					if($result_itemdescription[csf('uom')]=='') $uom_con="";else $uom_con="and uom='".$result_itemdescription[csf('uom')]."'";


					}
					else
					{
					if($result_itemdescription[csf('dia_width')]=='') $dia_width_con="and slength is null";else $dia_width_con="and dia_width='".$result_itemdescription[csf('dia_width')]."'";
					if($result_itemdescription[csf('description')]=='') $description_con="and description is null";else $description_con="and description='".$result_itemdescription[csf('description')]."'";
					if($result_itemdescription[csf('uom')]=='') $uom_con="and uom is null";else $uom_con="and uom='".$result_itemdescription[csf('uom')]."'";

					}

            /*    $nameArray_color_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls where    booking_no=$txt_booking_no and sensitivity=0 and process=". $result_item[csf('process')]." and  description='". $result_itemdescription[csf('description')]."'and rate='". $result_itemdescription[csf('rate')]."' and uom='". $result_itemdescription[csf('uom')]."' and dia_width='". $result_itemdescription[csf('dia_width')]."'");  */
				 $nameArray_color_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls where    booking_no=$txt_booking_no and sensitivity=0 and process=". $result_item[csf('process')]."  and rate='". $result_itemdescription[csf('rate')]."'  $dia_width_con $description_con $uom_con");
                foreach($nameArray_color_size_qnty as $result_color_size_qnty)
                {
                ?>
                <td style="border:1px solid black; text-align:right">
                <?
                if($result_color_size_qnty[csf('cons')]!= "")
                {
                echo number_format($result_color_size_qnty[csf('cons')],2);
                $item_desctiption_total += $result_color_size_qnty[csf('cons')] ;
                $color_tatal+=$result_color_size_qnty[csf('cons')];
                }
                else echo "";
                ?>
                </td>
                <?
                }
                ?>
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('mc_dia')]; ?></td>
                <td style="border:1px solid black; text-align:center "><? echo $result_itemdescription[csf('dia_width')]; ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('fin_gsm')]; ?></td>
                 <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('slength')]; ?></td>
                <td style="border:1px solid black; text-align:center "><? echo $unit_of_measurement[$result_itemdescription[csf('uom')]]; ?></td>
                <td style="border:1px solid black; text-align:right;<? echo $hide_show;?>"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right;<? echo $hide_show;?>">
                <?
                $amount_as_per_gmts_color = $item_desctiption_total*$result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="6"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right">
                <?
                if($color_tatal !='')
                {
                echo number_format($color_tatal,2);
                }
                ?>
                </td>
                  <td style="border:1px solid black;"></td>
                <td style="border:1px solid black;"></td>
                <td style="border:1px solid black; text-align:right"></td>
                <td style="border:1px solid black;"></td>
                <td style="border:1px solid black;"></td>
                <td style="border:1px solid black; text-align:right<? echo $hide_show;?>"></td>
                <td style="border:1px solid black; text-align:right;<? echo $hide_show;?>">
                <?
			 if($show_rate==1) echo '';else echo number_format($total_amount_as_per_gmts_color,4);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="15-<? echo $col_span?>"><strong><? if($show_rate==1) echo '';else echo 'Total'; ?></strong></td>
                <td  style="border:1px solid black;  text-align:right"><?
				if($show_rate==1) echo '';else echo number_format($grand_total_as_per_gmts_color,4); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
            </tr>
        </table>
        <?
		//print_r($color_tatal);
		}
		?>
        <!--==============================================NO NENSITIBITY END=========================================  -->
       &nbsp;
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
       <table  width="100%" class="rpt_table" style="border:1px solid black;"   border="0" cellpadding="0" cellspacing="0">
       <tr style="border:1px solid black;">
                <th width="70%" style="border:1px solid black; text-align:right"><? if($show_rate==1) echo '';else echo 'Total Booking Amount'; ?></th><td width="30%" style="border:1px solid black; text-align:right"><?  if($show_rate==1) echo '';else echo number_format($booking_grand_total,4);?></td>
            </tr>
            <tr style="border:1px solid black;">
                <th width="70%" style="border:1px solid black; text-align:right"><? if($show_rate==1) echo '';else echo 'Total Booking Amount (in word)'; ?></th><td width="30%" style="border:1px solid black;"><?
				 if($show_rate==1) echo '';else echo number_to_words(number_format($booking_grand_total,4),$mcurrency, $dcurrency); ?></td>
            </tr>
       </table>
          &nbsp;
        <table  width="100%" class="rpt_table" style="border:1px solid black;"   border="0" cellpadding="0" cellspacing="0">

        <?
        $data_array=sql_select("select id, terms from  wo_booking_terms_condition where booking_no=$txt_booking_no");// quotation_id='$data'
        if ( count($data_array)>0)
        {
		?>

         <thead>
            <tr style="border:1px solid black;">
                <th width="3%" style="border:1px solid black;">Sl</th><th width="97%" style="border:1px solid black;font-weight:bold;"><b>Spacial Instruction</b></th>
            </tr>
        </thead>
        <tbody>

        <?
            $i=0;
            foreach( $data_array as $row )
            {
                $i++;
                ?>
                    <tr id="settr_1" align="" style="border:1px solid black;">
                        <td style="border:1px solid black;">
                        <? echo $i;?>
                        </td>
                        <td style="border:1px solid black;"><strong style=" font-weight:bold; ">
                        <? echo $row[csf('terms')]; ?>
                        </strong>
                        </td>
                    </tr>
                <?
            }
        }
        else
        {/*

		?>
         <thead>
            <tr style="border:1px solid black;">
                <th width="3%" style="border:1px solid black;">Sl</th><th width="97%" style="border:1px solid black;font-weight:bold;"><b>Spacial Instruction</b></th>
            </tr>
        </thead>
        <tbody>
        <?
			$i=0;
        $data_array=sql_select("select id, terms from  lib_terms_condition where is_default=1");// quotation_id='$data'
        foreach( $data_array as $row )
            {
                $i++;
        ?>
        <tr id="settr_1" align="" style="border:1px solid black;">
                        <td style="border:1px solid black;">
                        <? echo $i;?>
                        </td>
                        <td style="border:1px solid black;">
                        <strong style="  font-weight:bold;">
                        <? echo $row[csf('terms')]; ?>
                        </strong>
                        </td>

                    </tr>
        <?
            }
        */}
		//$po_no
		//$po_no=rtrim($po_no,',');//wo_booking_mst
		$po_ids=implode(",",array_unique(explode(",",$po_ids)));
		$booking_nos='';$booking_dates='';
	  $booking_arr="select a.booking_date,a.booking_no  from wo_booking_mst a ,wo_booking_dtls b, wo_po_break_down c  where  a.booking_no=b.booking_no and b.po_break_down_id=c.id  and a.booking_no!=$txt_booking_no and c.id in($po_ids) and a.company_id=$cbo_company_name and a.item_category=12 and b.job_no=c.job_no_mst  and a.booking_type=3  and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 group by a.booking_date,a.booking_no";
	  $booking_previous=sql_select($booking_arr);
	foreach ($booking_previous as $result_book)
	{
		if($booking_nos!='') $booking_nos.=",".$result_book[csf('booking_no')].'; '.change_date_format($result_book[csf('booking_date')]);else  $booking_nos=$result_book[csf('booking_no')].'; '.change_date_format($result_book[csf('booking_date')]);
	}
        ?>

    </tbody>
    </table>
    <? if($cid==2){?>
    <br/>
     <table  width="100%" class="rpt_table" style="border:1px solid black;"   border="0" cellpadding="0" cellspacing="0">
        <thead>
         <tr align="left" style="border:1px solid black;font-weight:bold;">
          <th> Booking No & Date</th>
         </tr>
         </thead>
     <tr style="border:1px solid black;">
         <td><? echo $booking_nos;?> </td>

        </tr>
     </table>
     <?
	}
	 ?>
         <br/>
		 <?
            echo signature_table(53, $cbo_company_name, "1313px");
			if($cid==2){
			echo "****".custom_file_name($txt_booking_no,$style_sting,$job_no);
			}
         ?>
    </div>
    <script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <script>
		fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id_<? echo $cid; ?>');
	</script>
	<p style="page-break-after:always;"></p>
    <?
	}
	
	$html = ob_get_contents();
	ob_clean();
	list($is_mail_send,$mail)=explode('___',$mail_send_data);
	if($is_mail_send==1){
		require_once('../../../mailer/class.phpmailer.php');
		require_once('../../../auto_mail/setting/mail_setting.php');
		$mailBody = preg_replace("/<img[^>]+\>/i", " ", $html); 
			
		$mailToArr=array();
		$mailSql = "select b.EMAIL  from wo_booking_mst a,LIB_SUPPLIER b where b.id=a.supplier_id and a.booking_no=$txt_booking_no";
		//echo $mailSql;die;
		$mailSqlRes=sql_select($mailSql);
		foreach($mailSqlRes as $rows){
			if($rows[EMAIL]){$mailToArr[]=$rows[EMAIL];}
		}
		
		
		$mailSql = "SELECT c.email_address as EMAIL FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=97 and b.mail_user_setup_id=c.id  and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
		//echo $mailSql;die;
		$mailSqlRes=sql_select($mailSql);
		foreach($mailSqlRes as $rows){
			if($rows[EMAIL]){$mailToArr[]=$rows[EMAIL];}
		}
		
		
		if($mail!=''){$mailToArr[]=$mail;}
		$to=implode(',',$mailToArr);
 		$subject=" Service Booking Sheet ";
		$header=mailHeader();
		echo sendMailMailer( $to, $subject, $mailBody, $from_mail,$att_file_arr );
		
	}
	else{
		echo $html;
	}
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

		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0";disconnect($con); die;}
		 $id=return_next_id( "id", "wo_booking_terms_condition", 1 ) ;
		 $field_array="id,booking_no,terms";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $termscondition="termscondition_".$i;
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$txt_booking_no.",".$$termscondition.")";
			$id=$id+1;
		 }
		// echo  $data_array;
		$rID_de3=execute_query( "delete from wo_booking_terms_condition where  booking_no =".$txt_booking_no."",0);

		 $rID=sql_insert("wo_booking_terms_condition",$field_array,$data_array,1);
		 check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");
				echo "0**".$new_booking_no[0];
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$new_booking_no[0];
			}
		}

		if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "0**".$new_booking_no[0];
			}
			else{
				oci_rollback($con);
				echo "10**".$new_booking_no[0];
			}
		}
		disconnect($con);
		die;
	}
}

if ($action=="populate_data_from_search_popup")
{
	 $sql= "select booking_no,booking_date,company_id,buyer_id,job_no,po_break_down_id,item_category,fabric_source,currency_id,exchange_rate,pay_mode,booking_month,supplier_id,attention,delivery_date,source,booking_year,quality_level,tenor,ready_to_approved,id from wo_booking_mst  where booking_no='$data'";

	  $supplier_library=return_library_array( "select id,supplier_name from lib_supplier", "id", "supplier_name"  );
	  
	  //$company_library
	 $data_array=sql_select($sql);
	 foreach ($data_array as $row)
	 {

			//$print_report_format2=return_field_value("format_id","user_priviledge_report_setting","company_id ='".$row[csf("company_id")]."' and  user_id=$user_id and module_id=2 and report_id=11 and is_deleted=0 and status_active=1");
			$print_report_format2=return_field_value("format_id","lib_report_template","template_name ='".$row[csf("company_id")]."'   and module_id=2 and report_id=11 and is_deleted=0 and status_active=1");
			$fab_req_source=return_field_value("excut_source", "variable_order_tracking", "company_name=".$row[csf("company_id")]." and variable_list=66 and status_active=1 and is_deleted=0");
		if($fab_req_source=="" || $fab_req_source==0) $fab_req_source=1;else $fab_req_source=$fab_req_source;
		echo "document.getElementById('vari_fab_source_id').value = '".$fab_req_source."';\n";
			

		echo "print_report_button_setting('".$print_report_format2."');\n";
		//echo "document.getElementById('txt_order_no_id').value = '".$row[csf("po_break_down_id")]."';\n";
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";
		//echo "document.getElementById('report_ids').value = '".$print_report_format2."';\n";
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_id")]."';\n";
		//echo "document.getElementById('vari_fab_source_id').value = '".$row[csf("quality_level")]."';\n";
		//echo "document.getElementById('txt_job_no').value = '".$row[csf("job_no")]."';\n";
		echo "document.getElementById('txt_booking_no').value = '".$row[csf("booking_no")]."';\n";
		echo "document.getElementById('booking_mst_id').value = '".$row[csf("id")]."';\n";
		//echo "document.getElementById('cbo_fabric_natu').value = '".$row[csf("item_category")]."';\n";
		//echo "document.getElementById('cbo_fabric_source').value = '".$row[csf("fabric_source")]."';\n";
		echo "document.getElementById('cbo_currency').value = '".$row[csf("currency_id")]."';\n";
		echo "document.getElementById('txt_exchange_rate').value = '".$row[csf("exchange_rate")]."';\n";
		echo "document.getElementById('cbo_pay_mode').value = '".$row[csf("pay_mode")]."';\n";
		echo "document.getElementById('txt_booking_date').value = '".change_date_format($row[csf("booking_date")],'dd-mm-yyyy','-')."';\n";
		echo "document.getElementById('cbo_booking_month').value = '".$row[csf("booking_month")]."';\n";
		echo "document.getElementById('hidden_supplier_id').value = '".$row[csf("supplier_id")]."';\n";
		if($row[csf("pay_mode")]!=3 && $row[csf("pay_mode")]!=5)
		{
			echo "document.getElementById('cbo_supplier_name').value = '".$supplier_library[$row[csf("supplier_id")]]."';\n";
			echo "document.getElementById('hidden_supplier_name').value = '".$supplier_library[$row[csf("supplier_id")]]."';\n";
		}
		else
		{
			echo "document.getElementById('cbo_supplier_name').value = '".$company_library[$row[csf("supplier_id")]]."';\n";
			echo "document.getElementById('hidden_supplier_name').value = '".$company_library[$row[csf("supplier_id")]]."';\n";
		}

		//echo "document.getElementById('cbo_supplier_name').value = '".$row[csf("supplier_id")]."';\n";
		echo "document.getElementById('txt_attention').value = '".$row[csf("attention")]."';\n";
		echo "document.getElementById('txt_delivery_date').value = '".change_date_format($row[csf("delivery_date")],'dd-mm-yyyy','-')."';\n";
	    echo "document.getElementById('cbo_source').value = '".$row[csf("source")]."';\n";
		echo "document.getElementById('cbo_booking_year').value = '".$row[csf("booking_year")]."';\n";
		echo "document.getElementById('txt_tenor').value = '".$row[csf("tenor")]."';\n";
        echo "document.getElementById('cbo_ready_to_approved').value = '".$row[csf("ready_to_approved")]."';\n";
		$po_no="";
		$sql_po= "select po_number from  wo_po_break_down  where id in(".$row[csf('po_break_down_id')].")";
		$data_array_po=sql_select($sql_po);
		foreach ($data_array_po as $row_po)
		{
			$po_no.=$row_po[csf('po_number')].",";
		}
	//	echo "document.getElementById('txt_order_no').value = '".substr($po_no, 0, -1)."';\n";
		//echo "load_drop_down( 'requires/service_booking_controller', '".$row[csf("job_no")]."_".$row[csf("booking_no")]."', 'load_drop_down_fabric_description', 'fabric_description_td' )\n";
		//echo "load_drop_down( 'requires/service_booking_controller', '".$row[csf("job_no")]."', 'load_drop_down_process', 'process_td' )\n";
		//echo "load_drop_down( 'requires/service_booking_controller', '".$row[csf("job_no")]."_".$row[csf("booking_no")]."', 'load_drop_down_fabric_description_new', 'fabric_description_td' )\n";


	 }
}

if($action=="load_drop_down_attention")
{
	$data=explode('_',$data);
	$supp_id=$data[0];
	$paymode_id=$data[1];
	if($paymode_id!=3 && $paymode_id!=5)
	{
		//echo "select contact_person from lib_supplier where id =".$supp_id." and is_deleted=0 and status_active=1";
		$supplier_com_att=return_field_value("contact_person","lib_supplier","id =".$supp_id." and is_deleted=0 and status_active=1");
	}
	else
	{
		$supplier_com_att=return_field_value("contract_person","lib_company","id =".$supp_id." and is_deleted=0 and status_active=1");
	}
	echo "document.getElementById('txt_attention').value = '".$supplier_com_att."';\n";
	exit();
}


if ($action=="populate_data_from_variable")
{
	$data=explode('_',$data);
	$process_id=$data[0];
	$rate_type_id=$rate_type_mapping[$process_id];
	if($rate_type_id=="") $rate_type_id=0;else $rate_type_id=$rate_type_id;
	$rate_from_library=0;
	$rate_from_library=return_field_value("is_serveice_rate_lib", "variable_settings_production", "service_process_id=$rate_type_id and company_name=".$data[1]." and status_active=1 and is_deleted=0 ");
	echo "document.getElementById('service_rate_from').value = '".$rate_from_library."';\n";
}


if ($action=="Supplier_workorder_popup")
{

	echo load_html_head_contents("Production Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
	//echo $cbo_process;die;
	$rate_type_id=$rate_type_mapping[$cbo_process];
	if($rate_type_id==2) $supplier_party_type=20;
	else if($rate_type_id==3) $supplier_party_type=21;
	else if($rate_type_id==4) $supplier_party_type=21;
	else if($rate_type_id==6) $supplier_party_type=25;
	else if($rate_type_id==7) $supplier_party_type=24;
	else $supplier_party_type=0;
?>
	<script>
		var permission='<? echo $permission; ?>';

		function js_set_value(id,rate,cons_compo)
		{
			document.getElementById('hide_charge_id').value=id;
			document.getElementById('hide_supplier_rate').value=rate;
			document.getElementById('hide_construction_compo').value=cons_compo;
			parent.emailwindow.hide();
		}

    </script>

</head>

<body>

<?php
if ($rate_type_id == 2) {
	?>

	<form name="searchdescfrm"  id="searchdescfrm">
            <input type="hidden" name="hide_supplier_rate" id="hide_supplier_rate" class="text_boxes" value="">
            <input type="hidden" name="hide_charge_id" id="hide_charge_id" class="text_boxes" value="">
            <input type="hidden" name="hide_construction_compo" id="hide_construction_compo" class="text_boxes" value="">
            <div style="width:720px;max-height:450px;" align="center">
                <table cellspacing="0" width="700" cellpadding="0" class="rpt_table" rules="all" border="1" id="tbl_list_search">
                	<thead>
                    	<th width="35">SL</th>
                        <th width="150">Body Part</th>
                        <th width="200">Construction & Composition </th>
                        <th width="50">GSM</th>
                        <th width="150">Yarn Description </th>
                        <th width="50">UOM</th>
                        <th width="">Rate</th>
                    </thead>
                    <tbody id="supplier_body">
						<?

						$supplier_sql=sql_select("select c.id as ID,c.mst_id as MST_ID,a.supplier_name as NAME,c.supplier_rate as RATE,d.body_part as BODY_PART,d.const_comp as CONST_COMP,d.gsm as GSM,d.yarn_description as YARN_DESCRIPTION,d.uom_id as UOM_ID from lib_supplier a, lib_supplier_party_type b,lib_subcon_supplier_rate c,lib_subcon_charge d where a.id=b.supplier_id and b.party_type=20 and b.supplier_id=c.supplier_id and c.mst_id=d.id and d.rate_type_id=2 and d.comapny_id=$cbo_company_name and a.id=$hidden_supplier_id");

						$i=1;
						foreach($supplier_sql as $row)
						{
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$rate=$row['RATE']/($txt_exchange_rate*1);
							if($hidden_supplier_rate_id==$row['ID'])  $bgcolor="#FFFF00";

							?>
							<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" align="center" valign="middle" height="25" onClick="js_set_value('<? echo $row['ID']; ?>','<? echo $rate; ?>','<? echo $row['CONST_COMP']; ?>')" style="cursor:pointer">
								<td><?php echo $i; ?></td>
								<td align="left"><? echo $body_part[$row['BODY_PART']]; ?>
								</td>
                                <td align="left"><? echo $row['CONST_COMP']; ?></td>
                                <td align="left"><? echo $row['GSM']; ?></td>
                                <td align="left"><? echo $row['YARN_DESCRIPTION']; ?></td>
                                <td align="left"><? echo $unit_of_measurement[$row['UOM_ID']]; ?></td>
								<td><? echo number_format($rate,4,".",""); ?>
                                    <input type="hidden"name="update_details_id[]" id="update_details_id_<? echo $i; ?>" value="<? echo $row['ID']; ?>">
								</td>
							</tr>
							<?
							$i++;
						}
                        ?>
                    </tbody>
                </table>

            </div>
	</form>
	<?php
} else {
	?>
	<form name="searchdescfrm"  id="searchdescfrm">
            <input type="hidden" name="hide_supplier_rate" id="hide_supplier_rate" class="text_boxes" value="">
            <input type="hidden" name="hide_charge_id" id="hide_charge_id" class="text_boxes" value="">
            <input type="hidden" name="hide_construction_compo" id="hide_construction_compo" class="text_boxes" value="">
            <div style="width:720px;max-height:450px;" align="center">
                <table cellspacing="0" width="700" cellpadding="0" class="rpt_table" rules="all" border="1" id="tbl_list_search">
                	<thead>
                    	<th width="35">SL</th>
                        <th width="200">Construction & Composition </th>
                        <th width="100">Process Type </th>
                        <th width="150">Process Name</th>
                        <th width="100">Color</th>
                        <th width="50">UOM</th>
                        <th width="">Rate</th>
                    </thead>
                    <tbody id="supplier_body">
						<?
						$color_library_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name");
						$supplier_sql=sql_select("select c.id as ID,c.mst_id as MST_ID,a.supplier_name as NAME,c.supplier_rate as RATE,d.process_type_id as PROCESS_TYPE_ID,d.const_comp as CONST_COMP,d.process_id AS PROCESS_ID,d.gsm as GSM,d.color_id as COLOR_ID,d.uom_id as UOM_ID from lib_supplier a, lib_supplier_party_type b,lib_subcon_supplier_rate c,lib_subcon_charge d where a.id=b.supplier_id and b.party_type=$supplier_party_type and b.supplier_id=c.supplier_id and c.mst_id=d.id and d.rate_type_id=$rate_type_id and d.comapny_id=$cbo_company_name and a.id=$hidden_supplier_id");

						$i=1;
						foreach($supplier_sql as $row)
						{
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$rate=$row['RATE']/($txt_exchange_rate*1);
							if($hidden_supplier_rate_id==$row['ID'])  $bgcolor="#FFFF00";

							?>
							<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" align="center" valign="middle" height="25" onClick="js_set_value('<? echo $row['ID']; ?>','<? echo $rate; ?>','<? echo $row['CONST_COMP']; ?>')" style="cursor:pointer">
								<td><?php echo $i; ?></td>
                                <td align="left"><? echo $row['CONST_COMP']; ?></td>
                                <td align="left"><? echo $process_type[$row['PROCESS_TYPE_ID']]; ?></td>
                                <td align="left"><? echo $conversion_cost_head_array[$row['PROCESS_ID']]; ?></td>
                                <td align="left"><? echo $color_library_arr[$row['COLOR_ID']]; ?></td>
                                <td align="left"><? echo $unit_of_measurement[$row['UOM_ID']]; ?></td>
								<td><? echo number_format($rate,4,".",""); ?>
                                    <input type="hidden"name="update_details_id[]" id="update_details_id_<? echo $i; ?>" value="<? echo $row['ID']; ?>">
								</td>
							</tr>
							<?
							$i++;
						}
                        ?>
                    </tbody>
                </table>

            </div>
	</form>
    <?php
}
?>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}


?>