<?
/*-------------------------------------------- Comments
Version          : V1
Purpose			 : This form will create Service Booking Multi Job Wise
Functionality	 :
JS Functions	 :
Created by		 : Aziz
Creation date 	 : 09-09-2018
Requirment Client:
Requirment By    :
Requirment type  :
Requirment       :
Affected page    :
Affected Code    :
DB Script        :
Updated by 		 :
Update date		 :
QC Performed BY	 :
QC Date			 :
Comments		 : From this version oracle conversion is start
*/
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


//---------------------------------------------------- Start---------------------------------------------------------------------------
$color_library=return_library_array( "select id,color_name from lib_color where status_active=1", "id", "color_name"  );
$size_library=return_library_array( "select id,size_name from lib_size where status_active=1", "id", "size_name"  );
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
$trim_group= return_library_array( "select id, item_name from lib_item_group where status_active=1",'id','item_name');

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
	$currency_rate=set_conversion_rate( $data[0], $conversion_date, $data[2] );
	echo "1"."_".$currency_rate;
	exit();
}
if ($action=="load_drop_down_supplier")
{
	if($data==5 || $data==3){
	   echo create_drop_down( "cbo_supplier_name", 130, "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", "id,company_name",1, "-- Select Company --", "", "get_php_form_data( this.value+'_'+document.getElementById('cbo_pay_mode').value, 'load_drop_down_attention', 'requires/service_booking_multi_job_wise_dyeing_controller');",0,"" );
	}
	else{
	   echo create_drop_down( "cbo_supplier_name", 130, "select c.supplier_name,c.id from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and b.party_type=21  and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name","id,supplier_name", 1, "--Select Supplier--",$selected,"get_php_form_data( this.value+'_'+document.getElementById('cbo_pay_mode').value, 'load_drop_down_attention', 'requires/service_booking_multi_job_wise_dyeing_controller');","");
	}
	exit();
}

if($action=="load_drop_down_attention")
{
	$data=explode("_",$data);
	if($data[1]==5 || $data[1]==3 )
	{
			$supplier_name=return_field_value("contract_person","lib_company","id ='".$data[0]."' and is_deleted=0 and status_active=1");
	}
	else
	{
			$supplier_name=return_field_value("contact_person","lib_supplier","id ='".$data[0]."' and is_deleted=0 and status_active=1");
	}
	echo "document.getElementById('txt_attention').value = '".$supplier_name."';\n";
	exit();
}

if ($action=="order_search_popup")
{
  	echo load_html_head_contents("Order Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$cbo_short_type=str_replace("'","",$cbo_short_type);
	?>
	<script>
	 var selected_id = new Array, selected_name = new Array();
	 function check_all_data() {
			//var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			var tbl_row_count=$('#tbl_list_search tbody tr').length;
			tbl_row_count = tbl_row_count;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				var str_data=$('#strdata'+i).val();
				js_set_value(str_data, i );
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

if($cbo_short_type==10) $th_show_hide="";
	else $th_show_hide="display:none";
	//echo $cbo_short_type.'d';
?>
	<form name="searchpofrm_1" id="searchpofrm_1">
    <table  width="1180" class="rpt_table" align="center" rules="all">
        <thead>
            <th width="150">Company Name</th>
            <th width="140">Buyer Name</th>
            <th width="100">Job No</th>
            <th width="60">Ref No</th>
            <th width="80" style="<?=$th_show_hide;?>">Short Booking No</th>
            <th width="130">Order No</th>
            <th width="60">Style No</th>
            <th width="60">File No</th>
            <th width="150">Date Range</th>
            <th><input type="checkbox" value="0" onClick="set_checkvalue();" id="chk_job_wo_po">Job Without PO</th>
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
         <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:100px"></td>
         <td><input name="txt_ref_no" id="txt_ref_no" class="text_boxes" style="width:60px"></td>
          <td style="<?=$th_show_hide;?>"><input name="txt_booking_search"  id="txt_booking_search" class="text_boxes" style="width:80px"></td>
         <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:130px"></td>
         <td><input name="txt_style_no" id="txt_style_no" class="text_boxes" style="width:60px"></td>
         <td><input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:60px"></td>
        <td>
          <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" value="<? //echo $start_date; ?>"/>
          <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" value="<? //echo $end_date; ?>"/>
         </td>
         <td align="center">
         <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('chk_job_wo_po').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_ref_no').value+'_'+document.getElementById('txt_style_no').value+'_'+document.getElementById('txt_file_no').value+'_'+<? echo $txt_booking_date ?>+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_booking_search').value+'_'+'<?=$cbo_short_type;?>', 'create_po_search_list_view', 'search_div', 'service_booking_multi_job_wise_dyeing_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100%;" /></td>
    </tr>
    <tr>
        <td  align="center"  valign="top" colspan="9">
            <? echo load_month_buttons(1);  ?>
            <input type="hidden" id="po_number_id">
        </td>
    </tr>
    <tr>
        <td colspan="6" align="center"><strong>Selected PO Number:</strong> &nbsp;<input type="text" class="text_boxes"  readonly style="width:550px" id="po_number"></td>
        <td colspan="3" align="left"><strong>Selected JOB Number:</strong> &nbsp;
            <input type="text" id="job_no"  style="width:150px" class="text_boxes"></td>

    </tr>
    <tr>
        <td align="center" colspan="9">
            <div style="width:50%; float:left" align="right">
                <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data();" /> Check / Uncheck All
            </div>
            <div style="width:50%; float:left" align="left">
                <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
            </div>
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
		$year_cond=" and SUBSTRING_INDEX(b.insert_date, '-', 1)=$data[11]";
	if ($data[3]!="" &&  $data[4]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'"; else $shipment_date ="";
	}
	if($db_type==2)
	{
		$year_cond=" and to_char(b.insert_date,'YYYY')=$data[11]";
	if ($data[3]!="" &&  $data[4]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."'"; else $shipment_date ="";
	}
	if($data[1]==0 && str_replace("'","",$data[5])=="" && str_replace("'","",$data[7]) == '' && str_replace("'","",$data[6]) == '' && str_replace("'","",$data[8]) =='' && str_replace("'","",$data[9])=='' && $data[3] == '' && $data[4] =='')
	{
		echo "<span style='color:red; font-weight:bold; font-size:20px; text-align:center'>Please select any search data.";
		die;
	}
	$short_booking=$data[12];
	$short_booking_type=$data[13];
	if($short_booking!="") $short_booking_cond=" and e.booking_no like '%$short_booking%' ";
	else  $short_booking_cond="";
	
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	
	$approval_allow = sql_select("select b.id, b.page_id, b.approval_need, b.allow_partial, b.validate_page,a.setup_date from approval_setup_mst a,approval_setup_dtls b where a.id=b.mst_id and a.company_id='$data[0]' and a.status_active=1 and b.page_id=25 and b.status_active=1 and b.is_deleted=0 order by b.id desc ");

	if ($approval_allow[0][csf("approval_need")] == 1 && $approval_allow[0][csf("allow_partial")] == 1)
		$approval_cond = "and c.approved in (1,3)";
	else if ($approval_allow[0][csf("approval_need")] == 1 && $approval_allow[0][csf("allow_partial")] == 2)
		$approval_cond = "and c.approved in (1)";
	else if ($approval_allow[0][csf("approval_need")] == 1 && $approval_allow[0][csf("allow_partial")] == 0)
		$approval_cond = "and c.approved in (1,3)";
	else $approval_cond = "";
	
	if($short_booking_type==0)
	{
	$sql_job= sql_select("SELECT a.job_no_prefix_num,$insert_year, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.job_quantity, b.id, b.po_number, b.po_quantity, b.shipment_date, b.grouping, b.file_no from wo_po_details_master a, wo_po_break_down b, wo_pre_cost_mst c where a.id=b.job_id and a.id=c.job_id and a.status_active=1 and b.status_active=1 and b.shiping_status not in(3) $approval_cond $shipment_date $company $buyer $job_cond $order_cond $ref_cond $style_ref_cond $file_no_cond $year_cond order by a.job_no");
	}
	else
	{
			$sql_job= sql_select("SELECT a.job_no_prefix_num,$insert_year, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.job_quantity, b.id,
			 b.po_number, b.po_quantity, b.shipment_date, b.grouping, b.file_no from wo_po_details_master a, wo_po_break_down b, wo_pre_cost_mst c,wo_booking_dtls d where
			  a.id=b.job_id and a.id=c.job_id and a.job_no=d.job_no and d.po_break_down_id=b.id 
			 and d.is_short=1 and d.booking_type=1 and  a.status_active=1 and b.status_active=1 and  d.status_active=1 and d.status_active=1 and b.shiping_status not in(3) 
			  $approval_cond $shipment_date $company $buyer $job_cond $order_cond $ref_cond $style_ref_cond $file_no_cond $year_cond order by a.job_no");

	}
	foreach($sql_job as $row){
		$po_id_arr[$row[csf('id')]]=$row[csf('id')];
	}
	$po_id_cond=where_con_using_array($po_id_arr,0,"b.po_break_down_id");

	$condition= new condition();
	$po_id_str=implode(", ",$po_id_arr);
	if(count($po_id_arr)>0){
		$condition->po_id("in($po_id_str)");
	}
	$condition->init();
	$conversion= new conversion($condition);
	//echo $conversion->getQuery(); die;
	$conv_qty_arr=$conversion->getQtyArray_by_order();
	
	//  echo '<pre>';
	//print_r($conv_qty_arr);die;

	$cu_booking_arr=array();
	//$sql_cu_booking=sql_select("SELECT b.pre_cost_fabric_cost_dtls_id, b.po_break_down_id, b.wo_qnty as cu_wo_qnty, b.amount as cu_amount from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_type=3 and a.booking_type=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $po_id_cond ");
	$sql_cu_booking=sql_select("SELECT b.pre_cost_fabric_cost_dtls_id, b.po_break_down_id, b.wo_qnty as cu_wo_qnty, b.amount as cu_amount from wo_booking_mst a, wo_booking_dtls b where  a.id=b.booking_mst_id and a.booking_type=3 and a.booking_type=3 and a.entry_form=229 and b.entry_form_id=229 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $po_id_cond ");
	foreach($sql_cu_booking as $rowcu){
		$cu_booking_arr[$rowcu[csf('po_break_down_id')]]['cu_wo_qnty']+=$rowcu[csf('cu_wo_qnty')];
		$cu_booking_arr[$rowcu[csf('po_break_down_id')]]['cu_amount']+=$rowcu[csf('cu_amount')];
	}
	unset($sql_cu_booking);
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1100" class="rpt_table" id="tbl_list_search" >
		<thead>
            <th width="30">SL</th>
            <th width="90">Job No</th>
            <th width="50">Year</th>
            <th width="100">Company</th>
            <th width="100">Buyer</th>
            <th width="100">Ref. No</th>
            <th width="100">Style Ref. No</th>
            <th width="60">File No</th>
            <th width="80">Job Qty.</th>
            <th width="120">PO NO</th>
            <th width="70">PO Qty</th>
            <th>Shipment Date</th>
        </thead>
		<tbody>
			<?
			if($short_booking_type==0)
			{
			$sql_job= sql_select("SELECT a.job_no_prefix_num,$insert_year, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.job_quantity, b.id, b.po_number,
			 b.po_quantity, b.shipment_date, b.grouping, b.file_no from wo_po_details_master a, wo_po_break_down b, wo_pre_cost_mst c where a.job_no=b.job_no_mst 
			 and a.job_no=c.job_no and a.status_active=1 and b.status_active=1 and b.shiping_status not in(3) $approval_cond $shipment_date $company $buyer $job_cond 
			 $order_cond $ref_cond $style_ref_cond $file_no_cond $year_cond order by a.job_no");
			}
			else
			{
			 $sql_job= sql_select("SELECT a.job_no_prefix_num,$insert_year, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.job_quantity, b.id, b.po_number,
			 b.po_quantity, b.shipment_date, b.grouping, b.file_no from wo_po_details_master a, wo_po_break_down b, wo_pre_cost_mst c,wo_booking_dtls e,wo_pre_cost_fabric_cost_dtls d  where a.job_no=b.job_no_mst 
			 and a.job_no=c.job_no and c.job_no=d.job_no and a.job_no=d.job_no and e.po_break_down_id=b.id and e.pre_cost_fabric_cost_dtls_id=d.id 
			 and e.is_short=1 and e.booking_type=1 
		and  d.id=e.pre_cost_fabric_cost_dtls_id and a.status_active=1  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.shiping_status not in(3) $approval_cond $shipment_date $company $buyer $job_cond 
			 $order_cond $ref_cond $style_ref_cond $file_no_cond $year_cond $short_booking_cond order by a.job_no");
			  
			}
			 
			
			$i=1;
				foreach($sql_job as $row){ 
					$booking_woqty=$cu_booking_arr[$row[csf('id')]]['cu_wo_qnty'];
					$req_qty=array_sum($conv_qty_arr[$row[csf('id')]]);
					//  echo $req_qty.'--'.$booking_woqty.'<br>';
					if($req_qty>$booking_woqty){
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="<?=$i;?>" onClick="js_set_value('<?=$row[csf('id')].'_'.$row[csf('po_number')].'_'.$row[csf('job_no')];?>',<?=$i ?>)">
						<td><?=$i;?>
                        	<input type="hidden" id="strdata<?=$i; ?>" style="width:60px" value="<?=$row[csf('id')].'_'.$row[csf('po_number')].'_'.$row[csf('job_no')];?>" >
                        </td>
						<td style="word-break:break-all"><?= $row[csf('job_no_prefix_num')]?></td>
						<td style="word-break:break-all"><?= $row[csf('year')]?></td>
						<td style="word-break:break-all"><?= $comp[$row[csf('company_name')]]?></td>
						<td style="word-break:break-all"><?= $buyer_arr[$row[csf('buyer_name')]]?></td>
						<td style="word-break:break-all"><?= $row[csf('grouping')]?></td>
						<td style="word-break:break-all"><?= $row[csf('style_ref_no')]?></td>
						<td style="word-break:break-all"><?= $row[csf('file_no')]?></td>
						<td style="word-break:break-all"><?= $row[csf('job_quantity')]?></td>
						<td style="word-break:break-all"><?= $row[csf('po_number')]?></td>
						<td style="word-break:break-all"><?= $row[csf('po_quantity')]?></td>
						<td style="word-break:break-all"><?= change_date_format($row[csf('shipment_date')]) ?></td>
					</tr>
					<?
					$i++;
					}
				}
			?>
		</tbody>
	</table>
	<?
	exit();
}

if ($action=="populate_order_data_from_search_popup")
{
	$dataArr=explode("_",$data);
	$poid=$dataArr[0];
	$bookingType=$dataArr[1];
	$data_array=sql_select("select a.job_no,a.company_name,a.buyer_name from wo_po_details_master a, wo_po_break_down b where b.id in (".$poid.") and a.job_no=b.job_no_mst");
	foreach ($data_array as $row)
	{
		$job_no=$row[csf("job_no")];
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_name")]."';\n";
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_name")]."';\n";
		//echo "document.getElementById('txt_job_no').value = '".$row[csf("job_no")]."';\n";
		$job_no=$row[csf("job_no")].'__'.$bookingType;
		echo "load_drop_down( 'requires/service_booking_multi_job_wise_dyeing_controller', '".$job_no."', 'load_drop_down_fabric_description', 'fabric_description_td' )\n";
		$rate_from_library=0;
		$rate_from_library=return_field_value("is_serveice_rate_lib", "variable_settings_production", "service_process_id=3 and company_name=".$row[csf("company_name")]." and status_active=1 and is_deleted=0 ");
		echo "document.getElementById('service_rate_from').value = '".$rate_from_library."';\n";
		//echo "load_drop_down( 'requires/service_booking_multi_job_wise_dyeing_controller', '".$row[csf("job_no")]."', 'load_drop_down_process', 'process_td' )\n";
	}
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
}

if ($action=="load_drop_down_fabric_description")
{

	$data=explode("_",$data);
	$bookingType=$data[2];
	$fabric_description_array=array();
	//echo $bookingType.'D';
	if($bookingType==0) 
	{
		if($data[1] =="")
		{
			$wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select id,fabric_description,cons_process from wo_pre_cost_fab_conv_cost_dtls  where  job_no in('$data[0]') and cons_process=31 and status_active=1");
			//echo "select id,fabric_description,cons_process from wo_pre_cost_fab_conv_cost_dtls  where  job_no in('$data[0]') and cons_process=31 ";
		}
		else
		{
			$wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select id,fabric_description,cons_process from wo_pre_cost_fab_conv_cost_dtls
			where job_no in('$data[0]') and status_active=1 and is_deleted=0 and cons_process=31 and status_active=1 ");
			//echo "select id,fabric_description,cons_process from wo_pre_cost_fab_conv_cost_dtls  where in($data[0]) and status_active=1 and is_deleted=0 and cons_process=31";
		}
	}
	else
	{
		$wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select distinct a.id,c.id as fabric_description from wo_pre_cost_fabric_cost_dtls c,WO_BOOKING_DTLS b,
		wo_pre_cost_fab_conv_cost_dtls a where 
		 b.pre_cost_fabric_cost_dtls_id=c.id and b.pre_cost_fabric_cost_dtls_id=a.fabric_description and c.id=a.fabric_description and c.job_no in('$data[0]')  and b.is_short=1
		 and a.cons_process=31 and b.booking_type=1
		and c.status_active=1 and c.is_deleted=0  and b.status_active=1 and b.is_deleted=0   and a.status_active=1 and a.is_deleted=0  ");
		if(count($wo_pre_cost_fab_conv_cost_dtls_id)<=0)
		{
			echo "<div><b> No Conversation Description Found.</b> </div>";
		}
	}

	foreach( $wo_pre_cost_fab_conv_cost_dtls_id as $row_wo_pre_cost_fab_conv_cost_dtls_id)
	{
		if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]!=0)
		{

			$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description from  wo_pre_cost_fabric_cost_dtls
			where  id='".$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]."' and status_active=1");
			list($fabric_description_row)=$fabric_description;

			$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]=$body_part[$fabric_description_row[csf("body_part_id")]].', '.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")];

		}

		if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]==0)
		{
			$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description from  wo_pre_cost_fabric_cost_dtls
			where  job_no in($data[0]) and status_active=1");
			foreach( $fabric_description as $fabric_description_row)
	        {
			$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]="All Fabrics  ".$conversion_cost_head_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("cons_process")]];
			}
		}


	}
	echo create_drop_down( "cbo_fabric_description", 650, $fabric_description_array,"", 1, "-- Select --", $selected,
	"set_process(this.value,'set_process')" );
}



 if($action=="set_process")
 {
	 $process=return_field_value("cons_process", "wo_pre_cost_fab_conv_cost_dtls", "id=$data");
	 echo $process; die;

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
	$rate_from_library=$data[8];
	$company=$data[9];
	$short_type=$data[10];
	$fabric_description_array_empty=array();
	$fabric_description_array=array();

	$fab_req_source=return_field_value("excut_source", "variable_order_tracking", "company_name=$company and variable_list=66 and status_active=1 and is_deleted=0");
	if($fab_req_source=="" || $fab_req_source==0) $fab_req_source=1;else $fab_req_source=$fab_req_source;
	$fabric_description_array=array();//
	$wo_pre_cost_fab_co_color_sql=sql_select("select b.gmts_color_id,b.contrast_color_id,c.id as fab_dtls_id from wo_pre_cos_fab_co_color_dtls b,wo_pre_cost_fab_conv_cost_dtls c  where  c.job_no=b.job_no and c.id=$fabric_description_id and b.pre_cost_fabric_cost_dtls_id=c.fabric_description");
	foreach( $wo_pre_cost_fab_co_color_sql as $row)
	{
		$contrast_color_arr[$row[csf('fab_dtls_id')]][$row[csf('gmts_color_id')]]['contrast_color']=$row[csf('contrast_color_id')];
	}
	unset($wo_pre_cost_fab_co_color_sql);
	if($short_type==0)
	{
	$wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select c.job_no,c.id,c.fabric_description,c.cons_process from wo_pre_cost_fab_conv_cost_dtls c,wo_po_break_down b where  c.job_id=b.job_id and b.id in($job_po_id) group by c.job_no,c.id,c.fabric_description,c.cons_process");
	}
	else
	{
		$short_cond=" and b.is_short=1";
	$wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select c.job_no,a.id,a.fabric_description from wo_pre_cost_fabric_cost_dtls c,wo_po_break_down b,wo_booking_dtls d,
	wo_pre_cost_fab_conv_cost_dtls a where 
	 c.job_id=b.job_id and d.pre_cost_fabric_cost_dtls_id=c.id and d.pre_cost_fabric_cost_dtls_id=a.fabric_description and c.id=a.fabric_description and d.is_short=1 and d.booking_type=1  and b.id in($job_po_id) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.is_deleted=0 
	 and d.status_active=1 and d.is_deleted=0 group by c.job_no,a.id,a.fabric_description");
	}
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
	unset($wo_pre_cost_fab_conv_cost_dtls_id);

	if($rate_from_library==1)
	{
		$rate_disable="disabled";
	}
	else
	{
		$fab_mapping_disable="disabled";
	}
	//echo $fab_req_source.'D';

	if($fab_req_source==1) //Budget
	{
		$condition= new condition();
		if(str_replace("'","",$job_po_id) !=''){
			$condition->po_id("in($job_po_id)");
		}

		$condition->init();
		$conversion= new conversion($condition);
		//echo $conversion->getQuery(); die;
		 // echo $job_no.'ddd';
		$conversion_knit_qty_arr=$conversion->getQtyArray_by_ConversionidOrderColorAndUom();
		$conversion_color_size_knit_qty_arr=$conversion->getQtyArray_by_ConversionidOrderColorSizeidAndUom();
		$conversion_po_size_knit_qty_arr=$conversion->getQtyArray_by_ConversionidOrderSizeidAndUom();
		//print_r($conversion_color_size_knit_qty_arr);
   }
   else
   {
   		   $sql_data_fab="select a.id as fab_dtl_id,b.job_no,b.po_break_down_id as po_id,b.sensitivity,b.gmts_color_id,b.fabric_color_id,b.gmts_size,
		   sum(b.grey_fab_qnty) as grey_fab_qnty,sum(b.amount) as amount from  wo_pre_cost_fabric_cost_dtls a,wo_booking_dtls b where  b.pre_cost_fabric_cost_dtls_id=a.id 
		   and b.job_no=a.job_no and b.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and b.booking_type=1 and b.po_break_down_id in($job_po_id) $short_cond
		     group by b.job_no,a.id,b.po_break_down_id,b.sensitivity,b.gmts_color_id,b.fabric_color_id,b.gmts_size";
		  $resultData_fab=sql_select($sql_data_fab);
		  	foreach($resultData_fab as $row)
			{
				if($sensitivity==1 || $sensitivity==3 ) //As Per Gmt or Contrast color
				{
					$fab_req_qty_booking_arr[$row[csf('po_id')]][$row[csf('fab_dtl_id')]][$row[csf('gmts_color_id')]]['grey_fab_qnty']+=$row[csf('grey_fab_qnty')];
				}
				else if($sensitivity==4) //Color & Size
				{
					$fab_req_qty_booking_arr[$row[csf('po_id')]][$row[csf('fab_dtl_id')]][$row[csf('gmts_color_id')]][$row[csf('gmts_size')]]['grey_fab_qnty']=$row[csf('grey_fab_qnty')];
				}
				else if($sensitivity==2) //Size
				{
					$fab_req_qty_booking_arr[$row[csf('po_id')]][$row[csf('fab_dtl_id')]][$row[csf('gmts_size')]]['grey_fab_qnty']=$row[csf('grey_fab_qnty')];
				}
			}
   }
		//print_r($fab_req_qty_booking_arr);
	/* $booking_no=str_replace("'","",$txt_booking_no);
	 if($booking_no!='') $booking_cond="and b.booking_no!='$booking_no'";
	 else $booking_cond="";*/

	  $sql_data_Priv="select c.id as conv_dtl_id,b.job_no,b.po_break_down_id as po_id,b.sensitivity,b.gmts_color_id,b.fabric_color_id,b.gmts_size,sum(b.wo_qnty) as wo_qnty,sum(b.amount) as amount from  wo_pre_cost_fab_conv_cost_dtls c,wo_booking_dtls b where  b.pre_cost_fabric_cost_dtls_id=c.id and b.job_no=c.job_no and b.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and b.booking_type=3 and b.entry_form_id=229 and b.po_break_down_id in($job_po_id)  and b.process=31 group by b.job_no,c.id,b.po_break_down_id,b.sensitivity,b.gmts_color_id,b.fabric_color_id,b.gmts_size order by b.po_break_down_id,b.gmts_color_id";

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
			else if($row[csf('sensitivity')]==4)//  Color & Size
			{
				$po_fab_prev_color_booking_arr[$row[csf('po_id')]][$row[csf('conv_dtl_id')]][$row[csf('gmts_color_id')]][$row[csf('gmts_size')]]['wo_qnty']=$row[csf('wo_qnty')];
				$po_fab_prev_color_booking_arr[$row[csf('po_id')]][$row[csf('conv_dtl_id')]][$row[csf('gmts_color_id')]][$row[csf('gmts_size')]]['amount']=$row[csf('amount')];
				$po_fab_prev_booking_arr2[$row[csf('conv_dtl_id')]]['wo_qty']=$row[csf('wo_qnty')];
			}
			else if($row[csf('sensitivity')]==2)//  Size
			{
				$po_fab_prev_color_booking_arr[$row[csf('po_id')]][$row[csf('conv_dtl_id')]][$row[csf('gmts_size')]]['wo_qnty']=$row[csf('wo_qnty')];
				$po_fab_prev_color_booking_arr[$row[csf('po_id')]][$row[csf('conv_dtl_id')]][$row[csf('gmts_size')]]['amount']=$row[csf('amount')];
				$po_fab_prev_booking_arr2[$row[csf('conv_dtl_id')]]['wo_qty']=$row[csf('wo_qnty')];
			}

		}
		unset($dataResultPre);
	//$tot_prev_wo_qty=0;

	$wo_pre_cost_fab_avg=sql_select("select b.color_number_id,d.id as fab_dtls_id,b.dia_width,c.gsm_weight,b.po_break_down_id as po_id from wo_pre_cos_fab_co_avg_con_dtls b,wo_pre_cost_fabric_cost_dtls c,wo_pre_cost_fab_conv_cost_dtls d  where  c.job_no=b.job_no and d.job_no=b.job_no and c.id=b.pre_cost_fabric_cost_dtls_id   and d.id in($fabric_description_id) and b.po_break_down_id in($job_po_id) and b.pre_cost_fabric_cost_dtls_id=d.fabric_description");
	//echo "select b.color_number_id,d.id as fab_dtls_id,b.dia_width,c.gsm_weight,b.po_break_down_id as po_id from wo_pre_cos_fab_co_avg_con_dtls b,wo_pre_cost_fabric_cost_dtls c,wo_pre_cost_fab_conv_cost_dtls d  where  c.job_no=b.job_no and d.job_no=b.job_no and c.id=b.pre_cost_fabric_cost_dtls_id   and d.id in($fabric_description_id) and b.po_break_down_id in($job_po_id) and b.pre_cost_fabric_cost_dtls_id=d.fabric_description";
	//echo "select b.gmts_color_id,b.contrast_color_id,b.pre_cost_fabric_cost_dtls_id as fab_dtls_id from wo_pre_cos_fab_co_color_dtls b,wo_pre_cost_fab_conv_cost_dtls c  where  c.job_no=b.job_no and c.job_no='$job_no' and b.pre_cost_fabric_cost_dtls_id=c.id";
	foreach( $wo_pre_cost_fab_avg as $row)
	{
		if($row[csf('dia_width')]!='' || $row[csf('gsm_weight')]!='')
		{
			//echo $row[csf('dia_width')].', ';
			$fav_avg_color_arr[$row[csf('fab_dtls_id')]][$row[csf('po_id')]][$row[csf('color_number_id')]]['dia_width']=$row[csf('dia_width')];
			$fav_avg_color_arr[$row[csf('fab_dtls_id')]][$row[csf('po_id')]][$row[csf('color_number_id')]]['gsm_weight']=$row[csf('gsm_weight')];
			$fav_avg_color_arr2[$row[csf('fab_dtls_id')]][$row[csf('po_id')]]['dia_width']=$row[csf('dia_width')];
			$fav_avg_color_arr2[$row[csf('fab_dtls_id')]][$row[csf('po_id')]]['gsm_weight']=$row[csf('gsm_weight')];
		}
	}
	unset($wo_pre_cost_fab_avg);

		//echo $fabric_description_id.'ss';
		//if($sensitivity==1 || $sensitivity==3 )
		//{


			// $sql_conv="select a.job_no,b.id as po_id,b.po_number,min(c.id)as color_size_table_id,c.color_number_id,c.size_number_id as size_id,sum(c.plan_cut_qnty) as plan_cut_qnty,
			//  e.fabric_description as fabric_desc,e.cons_process,e.charge_unit,f.body_part_id,f.uom,f.gsm_weight,f.id as pre_cost_fabric_cost_dtls_id
			//  from wo_po_details_master a, wo_po_break_down b ,wo_po_color_size_breakdown c,wo_pre_cost_mst d,wo_pre_cost_fab_conv_cost_dtls e,
			//  wo_pre_cost_fabric_cost_dtls f,wo_pre_cos_fab_co_avg_con_dtls g

			//  where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and a.job_no=f.job_no and a.job_no=g.job_no
			//  and b.id=c.po_break_down_id and b.id=g.po_break_down_id and c.color_number_id=g.color_number_id and  c.size_number_id=g.gmts_sizes
		 	//  and c.item_number_id=f.item_number_id and f.id=g.pre_cost_fabric_cost_dtls_id and e.fabric_description=f.id
			//  and e.id in($fabric_description_id) and b.id in($job_po_id) and a.status_active=1 and a.is_deleted=0  and b.status_active=1
		    //  and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and d.status_active=1 and d.is_deleted=0 and e.status_active=1
			//  and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0
		    //  group by a.job_no,b.id,b.po_number,c.color_number_id,c.size_number_id,e.fabric_description,e.cons_process,e.charge_unit,f.gsm_weight, f.id,f.body_part_id ,f.uom order by b.id,c.color_number_id";
			  $sql_conv="select a.job_no,b.id as po_id,b.po_number,min(c.id)as color_size_table_id,c.color_number_id,c.size_number_id as size_id,sum(c.plan_cut_qnty) as plan_cut_qnty,e.fabric_description as fabric_desc,e.cons_process,h.unit_charge,f.body_part_id,f.uom,f.gsm_weight,f.id as pre_cost_fabric_cost_dtls_id from wo_po_details_master a, wo_po_break_down b ,wo_po_color_size_breakdown c,wo_pre_cost_mst d,wo_pre_cost_fab_conv_cost_dtls e,
			  wo_pre_cost_fabric_cost_dtls f,wo_pre_cos_fab_co_avg_con_dtls g,wo_pre_cos_conv_color_dtls h where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and a.job_no=f.job_no and a.job_no=g.job_no and e.job_no=h.job_no and b.id=c.po_break_down_id and b.id=g.po_break_down_id and c.color_number_id=g.color_number_id and  c.size_number_id=g.gmts_sizes and c.item_number_id=f.item_number_id and f.id=g.pre_cost_fabric_cost_dtls_id and e.fabric_description=f.id and e.id=h.conv_cost_dtls_id and  c.color_number_id=h.gmts_color_id and e.id in($fabric_description_id) and b.id in($job_po_id) and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0
		     group by a.job_no,b.id,b.po_number,c.color_number_id,c.size_number_id,e.fabric_description,e.cons_process,h.unit_charge,f.gsm_weight, f.id,f.body_part_id ,f.uom order by b.id,c.color_number_id";
			 //echo $sql_conv;

		//}
		
		$dataArray=sql_select($sql_conv);

		foreach($dataArray as $row)
		{

			if($sensitivity==1 || $sensitivity==3 )
			{
				$fabric_desc=$body_part[$row[csf('body_part_id')]].','.$row[csf('fabric_desc')].','.$row[csf('gsm_weight')];
				$fab_conv_detail_arr[$fabric_description_id][$row[csf('po_id')]][$row[csf('color_number_id')]]['color_size_table_id']=$row[csf('color_size_table_id')];
				$fab_conv_detail_arr[$fabric_description_id][$row[csf('po_id')]][$row[csf('color_number_id')]]['plan_cut_qnty']=$row[csf('plan_cut_qnty')];
				$fab_conv_detail_arr[$fabric_description_id][$row[csf('po_id')]][$row[csf('color_number_id')]]['unit_charge']=$row[csf('unit_charge')];
	
				$fab_conv_detail_arr[$fabric_description_id][$row[csf('po_id')]][$row[csf('color_number_id')]]['fabric_description']=$fabric_desc;
				$fab_conv_detail_arr[$fabric_description_id][$row[csf('po_id')]][$row[csf('color_number_id')]]['body_part_id']=$row[csf('body_part_id')];
				$fab_conv_detail_arr[$fabric_description_id][$row[csf('po_id')]][$row[csf('color_number_id')]]['job_no']=$row[csf('job_no')];
				$fab_conv_detail_arr[$fabric_description_id][$row[csf('po_id')]][$row[csf('color_number_id')]]['uom']=$row[csf('uom')];
				$fab_conv_detail_arr[$fabric_description_id][$row[csf('po_id')]][$row[csf('color_number_id')]]['fabric_dtl_id']=$row[csf('pre_cost_fabric_cost_dtls_id')];
				$po_number_arr[$row[csf('po_id')]]=$row[csf('po_number')];
			}
			else if($sensitivity==4 ) //Color & Size
			{
				$fabric_desc=$body_part[$row[csf('body_part_id')]].','.$row[csf('fabric_desc')].','.$row[csf('gsm_weight')];
				$fab_conv_detail_arr[$fabric_description_id][$row[csf('po_id')]][$row[csf('color_number_id')]][$row[csf('size_id')]]['color_size_table_id']=$row[csf('color_size_table_id')];
				$fab_conv_detail_arr[$fabric_description_id][$row[csf('po_id')]][$row[csf('color_number_id')]][$row[csf('size_id')]]['plan_cut_qnty']=$row[csf('plan_cut_qnty')];
				$fab_conv_detail_arr[$fabric_description_id][$row[csf('po_id')]][$row[csf('color_number_id')]][$row[csf('size_id')]]['unit_charge']=$row[csf('unit_charge')];
	
				$fab_conv_detail_arr[$fabric_description_id][$row[csf('po_id')]][$row[csf('color_number_id')]][$row[csf('size_id')]]['fabric_description']=$fabric_desc;
				$fab_conv_detail_arr[$fabric_description_id][$row[csf('po_id')]][$row[csf('color_number_id')]][$row[csf('size_id')]]['body_part_id']=$row[csf('body_part_id')];
				$fab_conv_detail_arr[$fabric_description_id][$row[csf('po_id')]][$row[csf('color_number_id')]][$row[csf('size_id')]]['job_no']=$row[csf('job_no')];
				$fab_conv_detail_arr[$fabric_description_id][$row[csf('po_id')]][$row[csf('color_number_id')]][$row[csf('size_id')]]['uom']=$row[csf('uom')];
				$fab_conv_detail_arr[$fabric_description_id][$row[csf('po_id')]][$row[csf('color_number_id')]][$row[csf('size_id')]]['fabric_dtl_id']=$row[csf('pre_cost_fabric_cost_dtls_id')];
				$po_number_arr[$row[csf('po_id')]]=$row[csf('po_number')];
			}
			else if($sensitivity==2 ) //Size
			{
				$fabric_desc=$body_part[$row[csf('body_part_id')]].','.$row[csf('fabric_desc')].','.$row[csf('gsm_weight')];
				$fab_conv_detail_arr[$fabric_description_id][$row[csf('po_id')]][$row[csf('size_id')]]['color_size_table_id']=$row[csf('color_size_table_id')];
				$fab_conv_detail_arr[$fabric_description_id][$row[csf('po_id')]][$row[csf('size_id')]]['plan_cut_qnty']=$row[csf('plan_cut_qnty')];
				$fab_conv_detail_arr[$fabric_description_id][$row[csf('po_id')]][$row[csf('size_id')]]['unit_charge']=$row[csf('unit_charge')];
	
				$fab_conv_detail_arr[$fabric_description_id][$row[csf('po_id')]][$row[csf('size_id')]]['fabric_description']=$fabric_desc;
				$fab_conv_detail_arr[$fabric_description_id][$row[csf('po_id')]][$row[csf('size_id')]]['body_part_id']=$row[csf('body_part_id')];
				$fab_conv_detail_arr[$fabric_description_id][$row[csf('po_id')]][$row[csf('size_id')]]['job_no']=$row[csf('job_no')];
				$fab_conv_detail_arr[$fabric_description_id][$row[csf('po_id')]][$row[csf('size_id')]]['uom']=$row[csf('uom')];
				$fab_conv_detail_arr[$fabric_description_id][$row[csf('po_id')]][$row[csf('size_id')]]['fabric_dtl_id']=$row[csf('pre_cost_fabric_cost_dtls_id')];
				$po_number_arr[$row[csf('po_id')]]=$row[csf('po_number')];
			}
		}
		unset($dataArray);
		//print_r($fab_conv_detail_arr);


		//echo $sql2;
		$tot_prev_wo_qty=$po_fab_prev_booking_arr2[$fabric_description_id]['wo_qty'];
		?>


			<div id="content_search_panel_<? echo $fabric_description_id; ?>" style="" class="accord_close">

				<table class="rpt_table" border="1" width="1590" cellpadding="0" cellspacing="0" rules="all" id="table_<? echo $fabric_description_id; ?>">
					<thead>
						<th>Job No</th>
						<th>PO No</th>
						<th title="Fab. Req. Source=<? echo $fab_req_source;?>">Fabric Description</th>
                        <th>Artwork No</th>
						<th>Y.Count</th>
						<th>LabDip No</th>
						<th>Option/Shade</th>

						<th>Lot</th>
						<th>Brand</th>
						<th>M/C DiaXGG</th>
						<th>Gmts. Color</th>
						<th>Item Color</th>
                        <th>Gmts Size</th>
                        <th>Item Size</th>
                        <th>Fab. Mapping</th>
						<th>WO. Balance</th>
						<th>WO. Qnty</th>
                        <th>Rate</th>

						<th>Fin Dia</th>
						<th>Fin GSM</th>
						<th>S.length</th>
						<th>Delivery Start Date</th>
                        <th>Delivery End Date</th>
                        <th>UOM</th>
                       
                        <th>Amount</th>
                        <th>Process %</th>
                        <th>Finish Qty</th>
                        <th>PCS</th>
                        <th title="<? echo $tot_prev_wo_qty;?>">Plan Cut Qnty</th>
						<th></th>
					</thead>
					<tbody id="table_search">
					<?
					$i=1;
					if($sensitivity==1 || $sensitivity==3) // AS Per Garments/Contrast Color
					{
						foreach($fab_conv_detail_arr as $fab_id=>$fab_data)
                        {
							foreach($fab_data as $po_id=>$po_data)
							{
								foreach($po_data as $color_id=>$row)
								{

										if($sensitivity==1 || $sensitivity==3) // AS Per Garments/Contrast Color
										{
												if($fab_req_source==1) //Budget
												{
													$pre_req_qnty=array_sum($conversion_knit_qty_arr[$fabric_description_id][$po_id][$color_id]);
												}
												else //Booking
												{
													$pre_req_qnty=$fab_req_qty_booking_arr[$po_id][$row[('fabric_dtl_id')]][$color_id]['grey_fab_qnty'];
												}
												$wo_prev_qnty=$po_fab_prev_color_booking_arr[$po_id][$fabric_description_id][$color_id]['wo_qnty'];
												$wo_prev_amount=$po_fab_prev_color_booking_arr[$po_id][$fabric_description_id][$color_id]['amount'];
										}
										//echo $po_id.'='.$row[('fabric_dtl_id')].'='.$color_id.'='.$pre_req_qnty.'<br>';


											if($row[("body_part_id")]==3)
											{
												$woqnty=$pre_req_qnty*2;
												$bal_woqnty=$woqnty-$wo_prev_qnty;
												$rate=$row[("unit_charge")];
												$amount=$rate*number_format($bal_woqnty,4,'.','');
												$req_amount=$rate*$woqnty;
											}
											else if($row[("body_part_id")]==2)
											{
												$woqnty=$pre_req_qnty*1;
												$bal_woqnty=$woqnty-$wo_prev_qnty;
												$rate=$row[("unit_charge")];
												$amount=$rate*number_format($bal_woqnty,4,'.','');
												$req_amount=$rate*$woqnty;
												//echo $row[csf('body_part_id')].'=='.$selected_uom.'=='.$uom_item;
											}
											else
											{
												$woqnty=$pre_req_qnty*1;
												$bal_woqnty=$woqnty-$wo_prev_qnty;
												$rate=$row[("unit_charge")];
												$amount=$rate*number_format($bal_woqnty,4,'.','');
												$req_amount=$rate*$woqnty;
												//echo $woqnty.'C';
											}
											// echo $woqnty.'='.$rate.'='.$amount.'<br>';


											if($sensitivity==3)
											{
												$item_colorID=$contrast_color_arr[$fabric_description_id][$color_id]['contrast_color'];
												if($item_colorID!='')
												{
													$itemColor=$color_library[$contrast_color_arr[$fabric_description_id][$color_id]['contrast_color']];
													$item_color_id=$contrast_color_arr[$fabric_description_id][$color_id]['contrast_color'];
												}
												else
												{
													$itemColor=$color_library[$color_id];
													$item_color_id=$color_id;
												}
											}
											else
											{
												$itemColor=$color_library[$color_id];
												$item_color_id=$color_id;
											}


											$woqnty=$woqnty;
											$amount=$amount;
											if($woqnty<=0)
											{
												$td_color='#FF0000';
											}
											else
											{
												$td_color='';
											}
										//echo $woqnty.'-'.$bal_woqnty.'<BR/>';

								$dia_width=$fav_avg_color_arr[$fabric_description_id][$po_id][$color_id]['dia_width'];
								$gsm_weight=$fav_avg_color_arr[$fabric_description_id][$po_id][$color_id]['gsm_weight'];

						if($bal_woqnty>0)
						 {
							  $bal_amount=$req_amount-$wo_prev_amount;
							// if($bal_amount >0 ){$amount=$bal_amount;$rate=number_format($bal_amount/$bal_woqnty,2, ".", "");}
								
						?>
						<tr align="center">
							<td>
								<input type="text" name="txt_job_no_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_job_no_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $row[("job_no")]; ?>" style="width:90px;" class="text_boxes">
							</td>
							<td>
								<?
									echo create_drop_down("po_no_".$fabric_description_id."_".$i, 100, $po_number_arr,"", 1,'', $po_id,"",1);
								?>
								<input type="hidden" name="po_id_<? echo $fabric_description_id.'_'.$i; ?>" id="po_id_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $po_id; ?>" style="width:110px;" class="text_boxes" disabled="disabled">
							</td>
							<td>
								<?
									echo create_drop_down("fabric_description_".$fabric_description_id."_".$i, 250, $fabric_description_array,"", 1,'', $fabric_description_id,"",1);
								?>
								<input type="hidden" name="fabric_description_id_<? echo $fabric_description_id.'_'.$i; ?>" id="fabric_description_id_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $fabric_description_id; ?>" style="width:110px;" class="text_boxes" disabled="disabled">
							</td>

							<td>
								<input type="text" name="artworkno_<? echo $fabric_description_id.'_'.$i; ?>" id="artworkno_<? echo $fabric_description_id.'_'.$i; ?>" value="" style="width:80px;" onBlur="copy_value('<? echo $fabric_description_id; ?>','<? echo $i; ?>','artworkno_')" class="text_boxes">
							</td>
							<td>
                                    <input type="text" name="txt_ycount_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_ycount_<? echo $fabric_description_id.'_'.$i; ?>" value="" style="width:80px;" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'ycount');" class="text_boxes">
                            </td>
							<td>
								<input type="text" name="txt_labdip_no_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_labdip_no_<? echo $fabric_description_id.'_'.$i; ?>" value="" style="width:80px;" onBlur="copy_value('<? echo $fabric_description_id; ?>','<? echo $i; ?>','labdip_no')" class="text_boxes">
							</td>
							<td>
								<input type="text" name="txt_option_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_option_<? echo $fabric_description_id.'_'.$i; ?>" value="" style="width:80px;" onBlur="copy_value('<? echo $fabric_description_id; ?>','<? echo $i; ?>','option_shade')" class="text_boxes">
							</td>

								<td>
                                    <input type="text" name="txt_lot_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_lot_<? echo $fabric_description_id.'_'.$i; ?>" value="" style="width:80px;" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'lotno');" class="text_boxes">
                                </td>
								<td>
                                    <input type="text" name="txt_brand_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_brand_<? echo $fabric_description_id.'_'.$i; ?>" value="" style="width:80px;" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'brand');" class="text_boxes">
                                </td>

								<td>
                                    <input type="text" name="txt_mcdia_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_mcdia_<? echo $fabric_description_id.'_'.$i; ?>" value="" style="width:80px;" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'mcdia');" class="text_boxes">
                                </td>



							<td>
                            <input type="hidden" name="color_size_table_id_<? echo $fabric_description_id.'_'.$i; ?>" id="color_size_table_id_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<?  echo $row[("color_size_table_id")];?>" disabled="disabled"/>

								 <input type="hidden" name="gmts_color_<? echo $fabric_description_id.'_'.$i; ?>" id="gmts_color_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<? if($sensitivity==1 || $sensitivity==3  ){ echo $color_library[$color_id];} else { echo "";}?>" disabled="disabled"/> 
								<? if($sensitivity==1 || $sensitivity==3  ){ echo $color_library[$color_id];} else { echo "";}?>
                                <input type="hidden" name="gmts_color_id_<? echo $fabric_description_id.'_'.$i; ?>" id="gmts_color_id_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<? if($sensitivity==1 || $sensitivity==3 ){ echo $color_id;} else { echo "";}?>"disabled="disabled"/>
							</td>
							<td>
								 <input type="text" name="item_color_<? echo $fabric_description_id.'_'.$i; ?>" id="item_color_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes" onChange="copy_value()" value="<? echo $itemColor;//if($sensitivity==1 || $sensitivity==3  || $sensitivity==4 ){ echo $color_library[$itemColor];} else { echo "";}?>"/>
                                <input type="hidden" name="item_color_id_<? echo $fabric_description_id.'_'.$i; ?>" id="item_color_id_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<? echo $item_color_id;//if($sensitivity==1 || $sensitivity==3  || $sensitivity==4 ){ echo $itemColor;} else { echo "";}?>" disabled="disabled"/>
								<input type="hidden" name="updateid_<? echo $fabric_description_id.'_'.$i; ?>" id="updateid_<? echo $fabric_description_id.'_'.$i; ?>" value="">
							</td>
                            <td>
								<input type="text" name="gmts_size_<? echo $fabric_description_id.'_'.$i; ?>" id="gmts_size_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<? if($sensitivity==2 || $sensitivity==4 ){echo $size_library[$row[("size_number_id")]];} else{ echo "";}?>" disabled="disabled"/>
                                <input type="hidden" name="gmts_size_id_<? echo $fabric_description_id.'_'.$i; ?>" id="gmts_size_id_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<? if($sensitivity==2 || $sensitivity==4 ){echo $row[("size_number_id")];} else{ echo "";}?>" disabled="disabled"/>
							</td>
                            <td>
								<input type="text" name="item_size_<? echo $fabric_description_id.'_'.$i; ?>" id="item_size_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes" onBlur="copy_value('<? echo $fabric_description_id; ?>','<? echo $i; ?>','item_size_')" value="<? if($sensitivity==2 || $sensitivity==4 ){ echo $size_library[$row[("size_number_id")]];} else{ echo "";}?>">
                                <input type="hidden" name="item_size_id_<? echo $fabric_description_id.'_'.$i; ?>" id="item_size_id_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<? if($sensitivity==2 || $sensitivity==4 ){ echo $row[("size_number_id")];} else{ echo "";}?>" disabled="disabled"/>
								
							</td>

                            <td>
								<input type="text" name="subcon_supplier_compo_<? echo $fabric_description_id.'_'.$i; ?>" id="subcon_supplier_compo_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="" onDblClick="service_supplier_popup('<? echo $fabric_description_id.'_'.$i; ?>')" placeholder="Browse" <?php echo $fab_mapping_disable; ?>>

								<input type="hidden" name="subcon_supplier_rateid_<? echo $fabric_description_id.'_'.$i; ?>" id="subcon_supplier_rateid_<? echo $fabric_description_id.'_'.$i; ?>" value="">
							</td>
							<td  style="width:70px;">
							<? echo number_format($bal_woqnty,2, ".", ""); ?>
							</td>
							<td   title="<? echo 'Req. Qty='.$woqnty.' Prev Wo Qty='.$wo_prev_qnty.' Balance Wo Qty='.$bal_woqnty;?>">
								
								<input type="text" name="txt_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" style="width:60px; background:<? echo $td_color;?>" class="text_boxes_numeric" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'txt_woqnty');calculate_amount(<? echo $fabric_description_id; ?>,<? echo $i; ?>)" value="<? echo number_format($bal_woqnty,2, ".", ""); ?>"/>
	
									<input type="hidden" name="txt_hidden_bal_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_hidden_bal_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" style="width:60px;" class="text_boxes_numeric"  value="<? echo number_format($bal_woqnty,2, ".", ""); ?>"/>
									<input type="hidden" name="txt_reqqty_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_reqqty_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric" value="<? echo number_format($woqnty,2, ".", ""); ?>"  />
									<input type="hidden" name="txt_prev_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_prev_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric" value="<? echo number_format($wo_prev_qnty,2, ".", "")?>" />
								</td>
								<td title="pre-cost-rate=<?=$req_amount/$woqnty;?>;Bal.Rate=<?=$rate;?>">								
									<input type="text" name="txt_rate_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_rate_<? echo $fabric_description_id.'_'.$i; ?>" style="width:60px;" class="text_boxes_numeric" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'txt_rate');calculate_amount(<? echo $fabric_description_id; ?>,<? echo $i; ?>)" value="<? echo $rate; ?>" pre-cost-rate="<? echo $rate; ?>" <?php echo $rate_disable; ?>>
									
								</td>
							<td>
								<input type="text" name="findia_<? echo $fabric_description_id.'_'.$i; ?>" id="findia_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $dia_width; ?>" style="width:100px;" onBlur="copy_value('<? echo $fabric_description_id; ?>','<? echo $i; ?>','findia_')" class="text_boxes">
							</td>
							<td>
                                    <input type="text" name="txt_fingsm_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_fingsm_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $gsm_weight; ?>" style="width:80px;" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'fingsm');" class="text_boxes">
                            </td>
							<td>
                                    <input type="text" name="txt_slength_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_slength_<? echo $fabric_description_id.'_'.$i; ?>" value="" style="width:80px;" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'slength');" class="text_boxes">
                           </td>
							 <td>
								<input type="text" name="startdate_<? echo $fabric_description_id.'_'.$i; ?>" id="startdate_<? echo $fabric_description_id.'_'.$i; ?>" value="<? //echo $row[csf("start_date")]; ?>" style="width:70px;" onChange="copy_value('<? echo $fabric_description_id; ?>','<? echo $i; ?>','startdate_')" class="datepicker">
							</td>
                            <td>
								<input type="text" name="enddate_<? echo $fabric_description_id.'_'.$i; ?>" id="enddate_<? echo $fabric_description_id.'_'.$i; ?>" value="<? // echo $row[csf("end_date")]; ?>" style="width:70px;" onChange="copy_value('<? echo $fabric_description_id; ?>','<? echo $i; ?>','enddate_')" class="datepicker">
							</td>
                            <td>
								<?
								echo create_drop_down("uom_".$fabric_description_id."_".$i, 50, $unit_of_measurement,"", 1, "--Select--",  $row[("uom")],"copy_value(".$fabric_description_id.",".$i.",'uom')","", $row[("uom")]);
								?>
							</td>
                           
                            <td title="pre-cost-amount=<?=$amount;?>">
							
								<input type="text" name="txt_amount_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_amount_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric"  value="<? echo $amount; ?>" disabled="disabled"/>
								<input type="hidden" name="txt_pre_amount_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_pre_amount_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric"  value="<? echo number_format($req_amount,4,'.',''); ?>" disabled="disabled"/>
                                <input type="hidden" name="txt_priv_amount_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_priv_amount_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric"  value="<? echo number_format($wo_prev_amount,4,'.',''); ?>" disabled="disabled"/>					
								<input type="hidden" name="hidd_bal_amount_<? echo $fabric_description_id.'_'.$i; ?>" id="hidd_bal_amount_<? echo $fabric_description_id.'_'.$i; ?>" value="<?=$bal_amount; ?>" />
							</td>
                            <td>
								<input type="text" name="txt_process_loss_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_process_loss_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'txt_process_loss');calculate_process_per(<? echo $fabric_description_id; ?>,<? echo $i; ?>)"  value="<? echo $process_loss; ?>" />
								 
							</td>
                            <td>
								<input type="text" name="txt_fin_qty_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_fin_qty_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric"  value="<? echo $woqnty; ?>" readonly/>
							</td>
                            
                            <td>
								<input type="text" name="txt_pcs_qty_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_pcs_qty_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric"  value="<? //echo  $row[("plan_cut_qnty")]; ?>" disabled>
							</td>
                            <td>
								<input type="text" name="txt_paln_cut_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_paln_cut_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric"  value="<? echo  $row[("plan_cut_qnty")]; ?>" disabled>
							</td>
							<td></td>
						</tr>
					 <?
						$i++;

							 	}
							}
						}
					}
					} //As Per Gmt & Contrast Color End
					else if($sensitivity==4)
					{
						
						foreach($fab_conv_detail_arr as $fab_id=>$fab_data)
                        {
							foreach($fab_data as $po_id=>$po_data)
							{
								foreach($po_data as $color_id=>$color_data)
								{
									foreach($color_data as $size_id=>$row)
									{

										if($sensitivity==4) // AS Per Garments/Contrast Color
										{
												if($fab_req_source==1) //Budget
												{
													//$pre_req_qnty=array_sum($conversion_knit_qty_arr[$fabric_description_id][$po_id][$color_id]);
													$pre_req_qnty=array_sum($conversion_color_size_knit_qty_arr[$fabric_description_id][$po_id][$color_id][$size_id]);
												}
												else //Booking
												{
													$pre_req_qnty=$fab_req_qty_booking_arr[$po_id][$row[('fabric_dtl_id')]][$color_id][$size_id]['grey_fab_qnty'];
												}
												$wo_prev_qnty=$po_fab_prev_color_booking_arr[$po_id][$fabric_description_id][$color_id][$size_id]['wo_qnty'];
												$wo_prev_amount=$po_fab_prev_color_booking_arr[$po_id][$fabric_description_id][$color_id][$size_id]['amount'];
										}
										//echo $pre_req_qnty.'DD';


											if($row[("body_part_id")]==3)
											{
												$woqnty=$pre_req_qnty*2;
												$bal_woqnty=$woqnty-$wo_prev_qnty;
												$rate=$row[("unit_charge")];
												$amount=$rate*$bal_woqnty;
											}
											else if($row[("body_part_id")]==2)
											{
												$woqnty=$pre_req_qnty*1;
												$bal_woqnty=$woqnty-$wo_prev_qnty;
												$rate=$row[("unit_charge")];
												$amount=$rate*$bal_woqnty;
												//echo $row[csf('body_part_id')].'=='.$selected_uom.'=='.$uom_item;
											}
											else
											{
												$woqnty=$pre_req_qnty*1;
												$bal_woqnty=$woqnty-$wo_prev_qnty;
												$rate=$row[("unit_charge")];
												$amount=$rate*$bal_woqnty;
												//echo $woqnty.'C';
											}


											if($sensitivity==3)
											{
												$item_colorID=$contrast_color_arr[$fabric_description_id][$color_id]['contrast_color'];
												if($item_colorID!='')
												{
													$itemColor=$color_library[$contrast_color_arr[$fabric_description_id][$color_id]['contrast_color']];
													$item_color_id=$contrast_color_arr[$fabric_description_id][$color_id]['contrast_color'];
												}
												else
												{
													$itemColor=$color_library[$color_id];
													$item_color_id=$color_id;
												}
											}
											else
											{
												$itemColor=$color_library[$color_id];
												$item_color_id=$color_id;
											}


											$woqnty=$woqnty;
											$amount=$amount;
											if($woqnty<=0)
											{
												$td_color='#FF0000';
											}
											else
											{
												$td_color='';
											}
										//echo $woqnty.'-'.$bal_woqnty.'<BR/>';

								$dia_width=$fav_avg_color_arr[$fabric_description_id][$po_id][$color_id]['dia_width'];
								$gsm_weight=$fav_avg_color_arr[$fabric_description_id][$po_id][$color_id]['gsm_weight'];

						if($bal_woqnty>0)
						{
					 ?>
						<tr align="center">
							<td>
								<input type="text" name="txt_job_no_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_job_no_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $row[("job_no")]; ?>" style="width:90px;" class="text_boxes">
							</td>
							<td>
								<?
									echo create_drop_down("po_no_".$fabric_description_id."_".$i, 100, $po_number_arr,"", 1,'', $po_id,"",1);
								?>
								<input type="hidden" name="po_id_<? echo $fabric_description_id.'_'.$i; ?>" id="po_id_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $po_id; ?>" style="width:110px;" class="text_boxes" disabled="disabled">
							</td>
							<td>
								<?
									echo create_drop_down("fabric_description_".$fabric_description_id."_".$i, 250, $fabric_description_array,"", 1,'', $fabric_description_id,"",1);
								?>
								<input type="hidden" name="fabric_description_id_<? echo $fabric_description_id.'_'.$i; ?>" id="fabric_description_id_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $fabric_description_id; ?>" style="width:110px;" class="text_boxes" disabled="disabled">
							</td>

							<td>
								<input type="text" name="artworkno_<? echo $fabric_description_id.'_'.$i; ?>" id="artworkno_<? echo $fabric_description_id.'_'.$i; ?>" value="" style="width:80px;" onBlur="copy_value('<? echo $fabric_description_id; ?>','<? echo $i; ?>','artworkno_')" class="text_boxes">
							</td>
							<td>
                                    <input type="text" name="txt_ycount_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_ycount_<? echo $fabric_description_id.'_'.$i; ?>" value="" style="width:80px;" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'ycount');" class="text_boxes">
                            </td>
							<td>
								<input type="text" name="txt_labdip_no_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_labdip_no_<? echo $fabric_description_id.'_'.$i; ?>" value="" style="width:80px;" onBlur="copy_value('<? echo $fabric_description_id; ?>','<? echo $i; ?>','labdip_no')" class="text_boxes">
							</td>
							<td>
								<input type="text" name="txt_option_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_option_<? echo $fabric_description_id.'_'.$i; ?>" value="" style="width:80px;" onBlur="copy_value('<? echo $fabric_description_id; ?>','<? echo $i; ?>','option_shade')" class="text_boxes">
							</td>

								<td>
                                    <input type="text" name="txt_lot_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_lot_<? echo $fabric_description_id.'_'.$i; ?>" value="" style="width:80px;" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'lotno');" class="text_boxes">
                                </td>
								<td>
                                    <input type="text" name="txt_brand_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_brand_<? echo $fabric_description_id.'_'.$i; ?>" value="" style="width:80px;" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'brand');" class="text_boxes">
                                </td>

								<td>
                                    <input type="text" name="txt_mcdia_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_mcdia_<? echo $fabric_description_id.'_'.$i; ?>" value="" style="width:80px;" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'mcdia');" class="text_boxes">
                                </td>



							<td>
                            <input type="hidden" name="color_size_table_id_<? echo $fabric_description_id.'_'.$i; ?>" id="color_size_table_id_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<?  echo $row[("color_size_table_id")];?>" disabled="disabled"/>

								<input type="hidden" name="gmts_color_<? echo $fabric_description_id.'_'.$i; ?>" id="gmts_color_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<? if($sensitivity==4  ){ echo $color_library[$color_id];} else { echo "";}?>" disabled="disabled"/> 
								<? if($sensitivity==4  ){ echo $color_library[$color_id];} else { echo "";}?>
                                <input type="hidden" name="gmts_color_id_<? echo $fabric_description_id.'_'.$i; ?>" id="gmts_color_id_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<? if($sensitivity==4 ){ echo $color_id;} else { echo "";}?>"disabled="disabled"/>
							</td>
							<td>
								 <input type="text" name="item_color_<? echo $fabric_description_id.'_'.$i; ?>" id="item_color_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes" onChange="copy_value()" value="<? echo $itemColor;//if($sensitivity==1 || $sensitivity==3  || $sensitivity==4 ){ echo $color_library[$itemColor];} else { echo "";}?>"/>
                                <input type="hidden" name="item_color_id_<? echo $fabric_description_id.'_'.$i; ?>" id="item_color_id_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<? echo $item_color_id;//if($sensitivity==1 || $sensitivity==3  || $sensitivity==4 ){ echo $itemColor;} else { echo "";}?>" disabled="disabled"/>
								<input type="hidden" name="updateid_<? echo $fabric_description_id.'_'.$i; ?>" id="updateid_<? echo $fabric_description_id.'_'.$i; ?>" value="">
							</td>
                            <td>
								<input type="text" name="gmts_size_<? echo $fabric_description_id.'_'.$i; ?>" id="gmts_size_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<? if($sensitivity==2 || $sensitivity==4 ){echo $size_library[$size_id];} else{ echo "";}?>" disabled="disabled"/>
                                <input type="hidden" name="gmts_size_id_<? echo $fabric_description_id.'_'.$i; ?>" id="gmts_size_id_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<? if($sensitivity==2 || $sensitivity==4 ){echo $size_id;} else{ echo "";}?>" disabled="disabled"/>
							</td>
                             <td>
								<input type="text" name="item_size_<? echo $fabric_description_id.'_'.$i; ?>" id="item_size_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes" onBlur="copy_value('<? echo $fabric_description_id; ?>','<? echo $i; ?>','item_size_')" value="<? if($sensitivity==2 || $sensitivity==4 ){ echo $size_library[$row[("size_number_id")]];} else{ echo "";}?>">
                                <input type="hidden" name="item_size_id_<? echo $fabric_description_id.'_'.$i; ?>" id="item_size_id_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<? if($sensitivity==2 || $sensitivity==4 ){ echo $row[("size_number_id")];} else{ echo "";}?>" disabled="disabled"/>
								
							</td>

                            <td>
								<input type="text" name="subcon_supplier_compo_<? echo $fabric_description_id.'_'.$i; ?>" id="subcon_supplier_compo_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="" onDblClick="service_supplier_popup('<? echo $fabric_description_id.'_'.$i; ?>')" placeholder="Browse" <?php echo $fab_mapping_disable; ?>>

								<input type="hidden" name="subcon_supplier_rateid_<? echo $fabric_description_id.'_'.$i; ?>" id="subcon_supplier_rateid_<? echo $fabric_description_id.'_'.$i; ?>" value="">
							</td>
							<td  style="width:70px;">
							<? echo number_format($bal_woqnty,2, ".", ""); ?>
							</td>

							<td   title="<? echo 'Req. Qty='.$woqnty.' Prev Wo Qty='.$wo_prev_qnty.' Balance Wo Qty='.$bal_woqnty;?>">
								<input type="text" name="txt_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" style="width:60px; background:<? echo $td_color;?>" class="text_boxes_numeric" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'txt_woqnty');calculate_amount(<? echo $fabric_description_id; ?>,<? echo $i; ?>)" value="<? echo number_format($bal_woqnty,2, ".", ""); ?>"/>

								<input type="hidden" name="txt_hidden_bal_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_hidden_bal_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" style="width:60px;" class="text_boxes_numeric" value="<? echo number_format($bal_woqnty,2, ".", ""); ?>"/>
									 <input type="hidden" name="txt_reqqty_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_reqqty_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric" value="<? echo number_format($woqnty,2, ".", ""); ?>"  />
									  <input type="hidden" name="txt_prev_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_prev_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric" value="<? echo number_format($wo_prev_qnty,2, ".", "")?>" />
							</td>
                            <td>
								<input type="text" name="txt_rate_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_rate_<? echo $fabric_description_id.'_'.$i; ?>" style="width:60px;" class="text_boxes_numeric" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'txt_rate');calculate_amount(<? echo $fabric_description_id; ?>,<? echo $i; ?>)" value="<? echo $rate; ?>" pre-cost-rate="<? echo $rate; ?>" <?php echo $rate_disable; ?>>
							</td>
							<td>
								<input type="text" name="findia_<? echo $fabric_description_id.'_'.$i; ?>" id="findia_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $dia_width; ?>" style="width:100px;" onBlur="copy_value('<? echo $fabric_description_id; ?>','<? echo $i; ?>','findia_')" class="text_boxes">
							</td>
							<td>
                                    <input type="text" name="txt_fingsm_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_fingsm_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $gsm_weight; ?>" style="width:80px;" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'fingsm');" class="text_boxes">
                            </td>
							<td>
                                    <input type="text" name="txt_slength_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_slength_<? echo $fabric_description_id.'_'.$i; ?>" value="" style="width:80px;" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'slength');" class="text_boxes">
                           </td>
							 <td>
								<input type="text" name="startdate_<? echo $fabric_description_id.'_'.$i; ?>" id="startdate_<? echo $fabric_description_id.'_'.$i; ?>" value="<? //echo $row[csf("start_date")]; ?>" style="width:70px;" onChange="copy_value('<? echo $fabric_description_id; ?>','<? echo $i; ?>','startdate_')" class="datepicker">
							</td>
                            <td>
								<input type="text" name="enddate_<? echo $fabric_description_id.'_'.$i; ?>" id="enddate_<? echo $fabric_description_id.'_'.$i; ?>" value="<? // echo $row[csf("end_date")]; ?>" style="width:70px;" onChange="copy_value('<? echo $fabric_description_id; ?>','<? echo $i; ?>','enddate_')" class="datepicker">
							</td>
                            <td>
								<?
								echo create_drop_down("uom_".$fabric_description_id."_".$i, 50, $unit_of_measurement,"", 1, "--Select--",  $row[("uom")],"copy_value(".$fabric_description_id.",".$i.",'uom')","", $row[("uom")]);
								?>
							</td>
                           
                            <td>
								<input type="text" name="txt_amount_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_amount_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric"  value="<? echo $amount; ?>" disabled="disabled"/>
								<input type="hidden" name="txt_pre_amount_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_pre_amount_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric"  value="<? echo $amount; ?>" disabled="disabled"/>
                                 <input type="hidden" name="txt_priv_amount_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_priv_amount_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric"  value="<? echo number_format($wo_prev_amount,4,'.',''); ?>" disabled="disabled"/>
							</td>
                             <td>
								<input type="text" name="txt_process_loss_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_process_loss_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'txt_process_loss');calculate_process_per(<? echo $fabric_description_id; ?>,<? echo $i; ?>)"  value="<? echo $process_loss; ?>" />
								 
							</td>
                              <td>
								<input type="text" name="txt_fin_qty_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_fin_qty_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric"  value="<? echo $bal_woqnty; ?>" readonly/>
							</td>
                            
                              <td>
								<input type="text" name="txt_pcs_qty_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_pcs_qty_<? echo $fabric_description_id.'_'.$i; ?>" style="width:60px;" class="text_boxes_numeric"  value="<? //echo  $row[("plan_cut_qnty")]; ?>" >
							</td>
                            <td>
								<input type="text" name="txt_paln_cut_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_paln_cut_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric"  value="<? echo  $row[("plan_cut_qnty")]; ?>" disabled>
							</td>
							<td></td>
						</tr>
								<?
										$i++;
							 			}
									}
							    }
						     }
					     }
					
					
					} //Color Size End
					else if($sensitivity==2)
					{
						
						foreach($fab_conv_detail_arr as $fab_id=>$fab_data)
                        {
							foreach($fab_data as $po_id=>$po_data)
							{
								
									foreach($po_data as $size_id=>$row)
									{

										if($sensitivity==2) //  Size level
										{
												if($fab_req_source==1) //Budget
												{
													//$pre_req_qnty=array_sum($conversion_knit_qty_arr[$fabric_description_id][$po_id][$color_id]);
													$pre_req_qnty=array_sum($conversion_po_size_knit_qty_arr[$fabric_description_id][$po_id][$size_id]);
												}
												else //Booking
												{
													$pre_req_qnty=$fab_req_qty_booking_arr[$po_id][$row[('fabric_dtl_id')]][$size_id]['grey_fab_qnty'];
												}
												$wo_prev_qnty=$po_fab_prev_color_booking_arr[$po_id][$fabric_description_id][$size_id]['wo_qnty'];
												$wo_prev_amount=$po_fab_prev_color_booking_arr[$po_id][$fabric_description_id][$size_id]['amount'];
										}
										//echo $pre_req_qnty.'DD';


											if($row[("body_part_id")]==3)
											{
												$woqnty=$pre_req_qnty*2;
												$bal_woqnty=$woqnty-$wo_prev_qnty;
												$rate=$row[("unit_charge")];
												$amount=$rate*$bal_woqnty;
											}
											else if($row[("body_part_id")]==2)
											{
												$woqnty=$pre_req_qnty*1;
												$bal_woqnty=$woqnty-$wo_prev_qnty;
												$rate=$row[("unit_charge")];
												$amount=$rate*$bal_woqnty;
												//echo $row[csf('body_part_id')].'=='.$selected_uom.'=='.$uom_item;
											}
											else
											{
												$woqnty=$pre_req_qnty*1;
												$bal_woqnty=$woqnty-$wo_prev_qnty;
												$rate=$row[("unit_charge")];
												$amount=$rate*$bal_woqnty;
												//echo $woqnty.'C';
											}


											if($sensitivity==3)
											{
												$item_colorID=$contrast_color_arr[$fabric_description_id][$color_id]['contrast_color'];
												if($item_colorID!='')
												{
													$itemColor=$color_library[$contrast_color_arr[$fabric_description_id][$color_id]['contrast_color']];
													$item_color_id=$contrast_color_arr[$fabric_description_id][$color_id]['contrast_color'];
												}
												else
												{
													$itemColor=$color_library[$color_id];
													$item_color_id=$color_id;
												}
											}
											else
											{
												$itemColor=$color_library[$color_id];
												$item_color_id=$color_id;
											}


											$woqnty=$woqnty;
											$amount=$amount;
											if($woqnty<=0)
											{
												$td_color='#FF0000';
											}
											else
											{
												$td_color='';
											}
										//echo $woqnty.'-'.$bal_woqnty.'<BR/>';

								$dia_width=$fav_avg_color_arr2[$fabric_description_id][$po_id]['dia_width'];
								$gsm_weight=$fav_avg_color_arr2[$fabric_description_id][$po_id]['gsm_weight'];

						if($bal_woqnty>0)
						{
					 ?>
						<tr align="center">
							<td>
								<input type="text" name="txt_job_no_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_job_no_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $row[("job_no")]; ?>" style="width:90px;" class="text_boxes">
							</td>
							<td>
								<?
									echo create_drop_down("po_no_".$fabric_description_id."_".$i, 100, $po_number_arr,"", 1,'', $po_id,"",1);
								?>
								<input type="hidden" name="po_id_<? echo $fabric_description_id.'_'.$i; ?>" id="po_id_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $po_id; ?>" style="width:110px;" class="text_boxes" disabled="disabled">
							</td>
							<td>
								<?
									echo create_drop_down("fabric_description_".$fabric_description_id."_".$i, 250, $fabric_description_array,"", 1,'', $fabric_description_id,"",1);
								?>
								<input type="hidden" name="fabric_description_id_<? echo $fabric_description_id.'_'.$i; ?>" id="fabric_description_id_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $fabric_description_id; ?>" style="width:110px;" class="text_boxes" disabled="disabled">
							</td>

							<td>
								<input type="text" name="artworkno_<? echo $fabric_description_id.'_'.$i; ?>" id="artworkno_<? echo $fabric_description_id.'_'.$i; ?>" value="" style="width:80px;" onBlur="copy_value('<? echo $fabric_description_id; ?>','<? echo $i; ?>','artworkno_')" class="text_boxes">
							</td>
							<td>
                                    <input type="text" name="txt_ycount_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_ycount_<? echo $fabric_description_id.'_'.$i; ?>" value="" style="width:80px;" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'ycount');" class="text_boxes">
                            </td>
							<td>
								<input type="text" name="txt_labdip_no_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_labdip_no_<? echo $fabric_description_id.'_'.$i; ?>" value="" style="width:80px;" onBlur="copy_value('<? echo $fabric_description_id; ?>','<? echo $i; ?>','labdip_no')" class="text_boxes">
							</td>
							<td>
								<input type="text" name="txt_option_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_option_<? echo $fabric_description_id.'_'.$i; ?>" value="" style="width:80px;" onBlur="copy_value('<? echo $fabric_description_id; ?>','<? echo $i; ?>','option_shade')" class="text_boxes">
							</td>

								<td>
                                    <input type="text" name="txt_lot_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_lot_<? echo $fabric_description_id.'_'.$i; ?>" value="" style="width:80px;" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'lotno');" class="text_boxes">
                                </td>
								<td>
                                    <input type="text" name="txt_brand_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_brand_<? echo $fabric_description_id.'_'.$i; ?>" value="" style="width:80px;" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'brand');" class="text_boxes">
                                </td>

								<td>
                                    <input type="text" name="txt_mcdia_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_mcdia_<? echo $fabric_description_id.'_'.$i; ?>" value="" style="width:80px;" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'mcdia');" class="text_boxes">
                                </td>



							<td>
                            <input type="hidden" name="color_size_table_id_<? echo $fabric_description_id.'_'.$i; ?>" id="color_size_table_id_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<?  echo $row[("color_size_table_id")];?>" disabled="disabled"/>

								 <input type="hidden" name="gmts_color_<? echo $fabric_description_id.'_'.$i; ?>" id="gmts_color_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<? if($sensitivity==4  ){ echo $color_library[$color_id];} else { echo "";}?>" disabled="disabled"/> 
								<? if($sensitivity==4  ){ echo $color_library[$color_id];} else { echo "";}?>
                                <input type="hidden" name="gmts_color_id_<? echo $fabric_description_id.'_'.$i; ?>" id="gmts_color_id_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<? if($sensitivity==4 ){ echo $color_id;} else { echo "";}?>"disabled="disabled"/>
							</td>
							<td>
								 <input type="text" name="item_color_<? echo $fabric_description_id.'_'.$i; ?>" id="item_color_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes" onChange="copy_value()" value="<? echo $itemColor;//if($sensitivity==1 || $sensitivity==3  || $sensitivity==4 ){ echo $color_library[$itemColor];} else { echo "";}?>"/>
                                <input type="hidden" name="item_color_id_<? echo $fabric_description_id.'_'.$i; ?>" id="item_color_id_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<? echo $item_color_id;//if($sensitivity==1 || $sensitivity==3  || $sensitivity==4 ){ echo $itemColor;} else { echo "";}?>" disabled="disabled"/>
								<input type="hidden" name="updateid_<? echo $fabric_description_id.'_'.$i; ?>" id="updateid_<? echo $fabric_description_id.'_'.$i; ?>" value="">
							</td>
                            <td>
								<input type="text" name="gmts_size_<? echo $fabric_description_id.'_'.$i; ?>" id="gmts_size_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<? if($sensitivity==2 || $sensitivity==4 ){echo $size_library[$size_id];} else{ echo "";}?>" disabled="disabled"/>
                                <input type="hidden" name="gmts_size_id_<? echo $fabric_description_id.'_'.$i; ?>" id="gmts_size_id_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<? if($sensitivity==2 || $sensitivity==4 ){echo $size_id;} else{ echo "";}?>" disabled="disabled"/>
							</td>
                             <td>
								<input type="text" name="item_size_<? echo $fabric_description_id.'_'.$i; ?>" id="item_size_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes" onBlur="copy_value('<? echo $fabric_description_id; ?>','<? echo $i; ?>','item_size_')" value="<? if($sensitivity==2 || $sensitivity==4 ){ echo $size_library[$row[("size_number_id")]];} else{ echo "";}?>">
                                <input type="hidden" name="item_size_id_<? echo $fabric_description_id.'_'.$i; ?>" id="item_size_id_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<? if($sensitivity==2 || $sensitivity==4 ){ echo $row[("size_number_id")];} else{ echo "";}?>" disabled="disabled"/>
								
							</td>
							
                            <td>
								<input type="text" name="subcon_supplier_compo_<? echo $fabric_description_id.'_'.$i; ?>" id="subcon_supplier_compo_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="" onDblClick="service_supplier_popup('<? echo $fabric_description_id.'_'.$i; ?>')" placeholder="Browse" <?php echo $fab_mapping_disable; ?>>

								<input type="hidden" name="subcon_supplier_rateid_<? echo $fabric_description_id.'_'.$i; ?>" id="subcon_supplier_rateid_<? echo $fabric_description_id.'_'.$i; ?>" value="">
							</td>
							<td style="width:70px;">
							<? echo number_format($bal_woqnty,2, ".", ""); ?>
							</td>
							<td   title="<? echo 'Req. Qty='.$woqnty.' Prev Wo Qty='.$wo_prev_qnty.' Balance Wo Qty='.$bal_woqnty;?>">
								<input type="text" name="txt_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" style="width:60px; background:<? echo $td_color;?>" class="text_boxes_numeric" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'txt_woqnty');calculate_amount(<? echo $fabric_description_id; ?>,<? echo $i; ?>)" value="<? echo number_format($bal_woqnty,2, ".", ""); ?>"/>

								<input type="hidden" name="txt_hidden_bal_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_hidden_bal_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" style="width:60px;" class="text_boxes_numeric" value="<? echo number_format($bal_woqnty,2, ".", ""); ?>"/>
									 <input type="hidden" name="txt_reqqty_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_reqqty_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric" value="<? echo number_format($woqnty,2, ".", ""); ?>"  />
									  <input type="hidden" name="txt_prev_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_prev_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric" value="<? echo number_format($wo_prev_qnty,2, ".", "")?>" />
							</td>
                            <td>
								<input type="text" name="txt_rate_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_rate_<? echo $fabric_description_id.'_'.$i; ?>" style="width:60px;" class="text_boxes_numeric" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'txt_rate');calculate_amount(<? echo $fabric_description_id; ?>,<? echo $i; ?>)" value="<? echo $rate; ?>" pre-cost-rate="<? echo $rate; ?>" <?php echo $rate_disable; ?>>
								
							</td>
							<td>
								<input type="text" name="findia_<? echo $fabric_description_id.'_'.$i; ?>" id="findia_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $dia_width; ?>" style="width:100px;" onBlur="copy_value('<? echo $fabric_description_id; ?>','<? echo $i; ?>','findia_')" class="text_boxes">
							</td>
							<td>
                                    <input type="text" name="txt_fingsm_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_fingsm_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $gsm_weight; ?>" style="width:80px;" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'fingsm');" class="text_boxes">
                            </td>
							<td>
                                    <input type="text" name="txt_slength_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_slength_<? echo $fabric_description_id.'_'.$i; ?>" value="" style="width:80px;" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'slength');" class="text_boxes">
                           </td>
							 <td>
								<input type="text" name="startdate_<? echo $fabric_description_id.'_'.$i; ?>" id="startdate_<? echo $fabric_description_id.'_'.$i; ?>" value="<? //echo $row[csf("start_date")]; ?>" style="width:70px;" onChange="copy_value('<? echo $fabric_description_id; ?>','<? echo $i; ?>','startdate_')" class="datepicker">
							</td>
                            <td>
								<input type="text" name="enddate_<? echo $fabric_description_id.'_'.$i; ?>" id="enddate_<? echo $fabric_description_id.'_'.$i; ?>" value="<? // echo $row[csf("end_date")]; ?>" style="width:70px;" onChange="copy_value('<? echo $fabric_description_id; ?>','<? echo $i; ?>','enddate_')" class="datepicker">
							</td>
                            <td>
								<?
								echo create_drop_down("uom_".$fabric_description_id."_".$i, 50, $unit_of_measurement,"", 1, "--Select--",  $row[("uom")],"copy_value(".$fabric_description_id.",".$i.",'uom')","", $row[("uom")]);
								?>
							</td>
                           
                            <td>
								<input type="text" name="txt_amount_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_amount_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric"  value="<? echo $amount; ?>" disabled="disabled"/>
								<input type="hidden" name="txt_pre_amount_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_pre_amount_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric"  value="<? echo $amount; ?>" disabled="disabled"/>
                                 <input type="hidden" name="txt_priv_amount_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_priv_amount_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric"  value="<? echo number_format($wo_prev_amount,4,'.',''); ?>" disabled="disabled"/>
							</td>
                            <td>
								<input type="text" name="txt_process_loss_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_process_loss_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'txt_process_loss');calculate_process_per(<? echo $fabric_description_id; ?>,<? echo $i; ?>)"  value="<? echo $process_loss; ?>" />
								 
							</td>
                              <td>
								<input type="text" name="txt_fin_qty_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_fin_qty_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric"  value="<? echo $bal_woqnty; ?>" readonly/>
							</td>
                            
                              <td>
								<input type="text" name="txt_pcs_qty_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_pcs_qty_<? echo $fabric_description_id.'_'.$i; ?>" style="width:60px;" class="text_boxes_numeric"  value="<? //echo  $row[("plan_cut_qnty")]; ?>" >
							</td>
                            <td>
								<input type="text" name="txt_paln_cut_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_paln_cut_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric"  value="<? echo  $row[("plan_cut_qnty")]; ?>" disabled>
							</td>
							<td></td>
						</tr>
								<?
										$i++;
							 		}
							    }
						     }
					     }
					} // Size level End
					?>
					
					</tbody>
				</table>
			</div>
		<?



}
if($action=="update_detail_booking_list_view")
{
	$data=explode("**",$data);

	$job_po=$data[0];
	//$type=$data[1];
	$fabric_description_id=$data[2];
	$process=$data[3];
	$sensitivity=$data[4];
	$txt_booking_no=$data[6];
	$dtls_id=implode(",",explode(",",$data[7]));
	$rate_from_library=$data[8];
    $programNo=$data[9];
	$company=$data[10];
	$fabric_conv_id=$data[2];
	$short_type=$data[11];
	
	$fabric_description_array_empty=array();
	$fabric_description_array=array();

	$fab_req_source=return_field_value("excut_source", "variable_order_tracking", "company_name=$company and variable_list=66 and status_active=1 and is_deleted=0");
	if($fab_req_source=="" || $fab_req_source==0) $fab_req_source=1;else $fab_req_source=$fab_req_source;
//	echo $fab_req_source.'DD';
	$short_cond="";
	if($short_type==0)
	{
	$wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select b.id as po_id,b.po_number,c.job_no,c.id,c.fabric_description,c.cons_process from wo_pre_cost_fab_conv_cost_dtls c,wo_po_break_down b where  c.job_no=b.job_no_mst and c.job_no in('$job_po') group by b.id, b.po_number,c.job_no,  c.id,c.fabric_description,c.cons_process");
	//echo "select b.id as po_id,b.po_number,c.job_no,c.id,c.fabric_description,c.cons_process from wo_pre_cost_fab_conv_cost_dtls c,wo_po_break_down b where  c.job_no=b.job_no_mst and c.job_no in('$job_po') group by b.id, b.po_number,c.job_no,  c.id,c.fabric_description,c.cons_process";
	}
	else
	{
		$short_cond=" and b.is_short=1";
	$wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select c.job_no,a.id,a.fabric_description from wo_pre_cost_fabric_cost_dtls c,wo_po_break_down b,wo_booking_dtls d,
	wo_pre_cost_fab_conv_cost_dtls a where 
	 c.job_id=b.job_id and d.pre_cost_fabric_cost_dtls_id=c.id and d.pre_cost_fabric_cost_dtls_id=a.fabric_description and c.id=a.fabric_description and d.is_short=1 and d.booking_type=1  and b.id in($job_po_id) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.is_deleted=0 
	 and d.status_active=1 and d.is_deleted=0 group by c.job_no,a.id,a.fabric_description");
	}
	foreach( $wo_pre_cost_fab_conv_cost_dtls_id as $row_wo_pre_cost_fab_conv_cost_dtls_id)
	{
		//$fab_conv_id_arr_arr[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf('fabric_description')]]['fab_dtls_id']=$row_wo_pre_cost_fab_conv_cost_dtls_id[csf('id')];
		
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
//	print_r($fabric_description_array);
	$wo_pre_cost_fab_co_color_sql=sql_select("select c.fabric_description as fab_dtls_id, b.gmts_color_id,b.contrast_color_id,c.id as conv_dtls_id from wo_pre_cos_fab_co_color_dtls b,wo_pre_cost_fab_conv_cost_dtls c  where  c.job_no=b.job_no and c.id=$data[2] and b.pre_cost_fabric_cost_dtls_id=c.fabric_description");
 
	//echo "select b.gmts_color_id,b.contrast_color_id,c.id as fab_dtls_id from wo_pre_cos_fab_co_color_dtls b,wo_pre_cost_fab_conv_cost_dtls c  where  c.job_no=b.job_no and c.id=$data[2] and b.pre_cost_fabric_cost_dtls_id=c.fabric_description";
	foreach( $wo_pre_cost_fab_co_color_sql as $row)
	{
		$contrast_color_arr[$row[csf('conv_dtls_id')]][$row[csf('gmts_color_id')]]['contrast_color']=$row[csf('contrast_color_id')];
		
	}
	$sql_data_Priv="select c.id as conv_dtl_id,b.job_no,b.po_break_down_id as po_id,b.sensitivity,b.uom,b.gmts_color_id,b.fabric_color_id,b.gmts_size,sum(b.wo_qnty) as wo_qnty,sum(b.amount) as amount,d.unit_charge from wo_pre_cost_fab_conv_cost_dtls c,wo_booking_dtls b, wo_pre_cos_conv_color_dtls d where b.pre_cost_fabric_cost_dtls_id=c.id and b.job_no=c.job_no and c.id=d.conv_cost_dtls_id and b.gmts_color_id=d.gmts_color_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and b.booking_type=3 and b.entry_form_id=229 and b.job_no in('$job_po') and c.id in($fabric_conv_id) and b.process=31 and c.is_deleted=0 and c.status_active=1 and b.is_deleted=0 and b.status_active=1 group by b.job_no,c.id,b.po_break_down_id,b.sensitivity,b.uom,b.gmts_color_id,b.gmts_size,b.fabric_color_id,d.unit_charge";
	 //echo  $sql_data_Priv;

		$dataResultPre=sql_select($sql_data_Priv);
		$po_fab_prev_booking_arr=array();
		foreach($dataResultPre as $row)
		{
			if($row[csf('sensitivity')]==1 || $row[csf('sensitivity')]==3)// AS Per Garments/Contrast Color
			{
				$po_fab_prev_color_booking_arr[$row[csf('po_id')]][$row[csf('conv_dtl_id')]][$row[csf('gmts_color_id')]]['wo_qnty']=$row[csf('wo_qnty')];			
				$po_fab_prev_color_booking_arr[$row[csf('po_id')]][$row[csf('conv_dtl_id')]][$row[csf('gmts_color_id')]]['amount']=$row[csf('amount')];
				$po_fab_prev_color_booking_arr[$row[csf('po_id')]][$row[csf('conv_dtl_id')]][$row[csf('gmts_color_id')]]['unit_charge']=$row[csf('unit_charge')];
				$po_fab_prev_booking_arr2[$row[csf('conv_dtl_id')]]['wo_qty']=$row[csf('wo_qnty')];
			}
			else if($row[csf('sensitivity')]==4)//  Color & Size
			{
				$po_fab_prev_color_booking_arr[$row[csf('po_id')]][$row[csf('conv_dtl_id')]][$row[csf('gmts_color_id')]][$row[csf('gmts_size')]]['wo_qnty']=$row[csf('wo_qnty')];			
				$po_fab_prev_color_booking_arr[$row[csf('po_id')]][$row[csf('conv_dtl_id')]][$row[csf('gmts_color_id')]][$row[csf('gmts_size')]]['amount']=$row[csf('amount')];
				$po_fab_prev_booking_arr2[$row[csf('conv_dtl_id')]]['wo_qty']=$row[csf('wo_qnty')];
			}
			else if($row[csf('sensitivity')]==2)//  Color & Size
			{
				$po_fab_prev_color_booking_arr[$row[csf('po_id')]][$row[csf('conv_dtl_id')]][$row[csf('gmts_size')]]['wo_qnty']=$row[csf('wo_qnty')];
				$po_fab_prev_color_booking_arr[$row[csf('po_id')]][$row[csf('conv_dtl_id')]][$row[csf('gmts_size')]]['amount']=$row[csf('amount')];
				$po_fab_prev_booking_arr2[$row[csf('conv_dtl_id')]]['wo_qty']=$row[csf('wo_qnty')];
			}
		}
		//echo $fab_req_source.'DD';
		if($fab_req_source==1) //Budget
		{
			$condition= new condition();
			if(str_replace("'","",$job_po) !=''){
				$condition->job_no("in('$job_po')");
			}
			$condition->init();
			$conversion= new conversion($condition);
			$conversion_knit_qty_arr=$conversion->getQtyArray_by_ConversionidOrderColorAndUom();
			
			
			//print_r($conversion_knit_qty_arr);
			$conversion_color_size_knit_qty_arr=$conversion->getQtyArray_by_ConversionidOrderColorSizeidAndUom();
			$conversion_po_size_knit_qty_arr=$conversion->getQtyArray_by_ConversionidOrderSizeidAndUom();
		}
		else
		{
			  $sql_data_fab="select a.id as fab_dtl_id,b.job_no,b.po_break_down_id as po_id,b.sensitivity,b.gmts_color_id,b.fabric_color_id,sum(b.grey_fab_qnty) as grey_fab_qnty,b.gmts_size,sum(b.amount) as amount from  wo_pre_cost_fabric_cost_dtls a,wo_booking_dtls b where  b.pre_cost_fabric_cost_dtls_id=a.id and b.job_no=a.job_no and b.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and b.booking_type=1   and b.job_no in('$job_po') $short_cond group by b.job_no,a.id,b.po_break_down_id,b.sensitivity,b.gmts_color_id,b.fabric_color_id,b.gmts_size";
		    $resultData_fab=sql_select($sql_data_fab);
		  	foreach($resultData_fab as $row)
			{
				if($sensitivity==1 || $sensitivity==3) // AS Per Garments/Contrast Color
				{
				$fab_req_qty_booking_arr[$row[csf('po_id')]][$row[csf('fab_dtl_id')]][$row[csf('gmts_color_id')]]['grey_fab_qnty']=$row[csf('grey_fab_qnty')];			
				$fab_req_qty_booking_arr[$row[csf('po_id')]][$row[csf('fab_dtl_id')]][$row[csf('gmts_color_id')]]['amount']=$row[csf('amount')];
				}
				else if($sensitivity==4) //  Color & Size
				{
				$fab_req_qty_booking_arr[$row[csf('po_id')]][$row[csf('fab_dtl_id')]][$row[csf('gmts_color_id')]][$row[csf('gmts_size')]]['grey_fab_qnty']=$row[csf('grey_fab_qnty')];$fab_req_qty_booking_arr[$row[csf('po_id')]][$row[csf('fab_dtl_id')]][$row[csf('gmts_color_id')]][$row[csf('gmts_size')]]['amount']=$row[csf('amount')];
				}
				else if($sensitivity==2) // Size
				{
				$fab_req_qty_booking_arr[$row[csf('po_id')]][$row[csf('fab_dtl_id')]][$row[csf('gmts_size')]]['grey_fab_qnty']=$row[csf('grey_fab_qnty')];
				$fab_req_qty_booking_arr[$row[csf('po_id')]][$row[csf('fab_dtl_id')]][$row[csf('gmts_size')]]['amount']=$row[csf('amount')];
				}
			}
		}//sakib
		$booking_dtls_sql="SELECT a.id as dtls_id,a.pre_cost_fabric_cost_dtls_id as fab_dtls_id,a.artwork_no,a.labdip_no,a.slength,a.mc_dia,a.mc_gauge,a.fin_dia,a.fin_gsm,a.yarn_count,a.option_shade,a.lot_no,a.brand,a.po_break_down_id as po_id,a.color_size_table_id,a.fabric_color_id,a.item_size,a.gmts_size,a.item_size,a.process,a.dia_width,a.req_qty,a.process_loss_percent,a.fin_fab_qnty, a.sensitivity,a.job_no,booking_no,a.booking_type,a.description,a.uom,a.delivery_date,a.delivery_end_date,a.sensitivity,a.wo_qnty,a.rate, a.amount,b.size_number_id,b.color_number_id,a.lib_composition,a.lib_supplier_rate_id,b.color_number_id,b.plan_cut_qnty,a.delivery_end_date,a.delivery_date from wo_booking_dtls a, wo_po_color_size_breakdown b where a.job_no=b.job_no_mst and a.po_break_down_id=b.po_break_down_id and a.color_size_table_id=b.id  and a.booking_type=3 and a.process=31 and a.booking_no='$txt_booking_no' and a.id in ($dtls_id) and a.status_active=1 and a.pre_cost_fabric_cost_dtls_id=$data[2] and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
		//echo $booking_dtls_sql; die;
		$dtls_dataArray=sql_select($booking_dtls_sql);		
		?>

		<div id="content_search_panel_<? echo $data[2]; ?>" style="" class="accord_close">

				<table class="rpt_table" border="1" width="1590" cellpadding="0" cellspacing="0" rules="all" id="table_<? echo $data[2]; ?>">
					<thead>
						<th>Job No</th>
						<th>PO No</th>
						<th title="Fab. Req. Source=<? echo $fab_req_source;?>">Fabric Description</th>
                        <th>Artwork No</th>
						<th>Y.Count</th>
						<th>LabDip No</th>
						<th>Option/Shade</th>
						<th>Lot</th>
						<th>Brand</th>
						<th>M/C DiaXGG</th>
						<th>Gmts. Color</th>
						<th>Item Color</th>
                        <th>Gmts Size</th>
                        <th>Item Size</th>
                        <th>Fab. Mapping</th>
						<th>WO. Balance</th>
						<th>WO. Qnty</th>
                        <th>Rate</th>
						<th>Fin Dia</th>
						<th>Fin GSM</th>
						<th>S.length</th>
						<th>Delivery Start Date</th>
                        <th>Delivery End Date</th>
                        <th>UOM</th>

                       
                        <th>Amount</th>
                         <th>Process Loss%</th>
                         <th>Finish Qty</th>
                        <th>PCS</th>
                        <th title="<? echo $tot_prev_wo_qty;?>">Plan Cut Qnty</th>
						<th></th>
					</thead>
					<tbody id="table_search">
					<?

					$i=1;$budgetRate=0;
					 foreach($dtls_dataArray as $row)
                        {
							//foreach($fab_data as $po_id=>$po_data)
							//{
								//foreach($po_data as $color_id=>$row)
								//{
									$fab_dtls_id=$fab_dtls_id_arr[$fabric_description_id];
									$po_id=$row[csf("po_id")];
									$color_id=$row[csf("color_number_id")];
									$gmts_size_id=$row[csf("gmts_size")];
									$fab_dtls_id=$row[csf("fab_dtls_id")];
									
									if($sensitivity==1 || $sensitivity==3) // AS Per Garments/Contrast Color
									{
										if($fab_req_source==1) //Budget
										{
											$pre_req_qnty=array_sum($conversion_knit_qty_arr[$fabric_description_id][$po_id][$color_id]);
											$budgetRate=$po_fab_prev_color_booking_arr[$po_id][$fabric_description_id][$color_id]['unit_charge'];
										}
										else
										{
											$fab_dtls_id=$fab_dtls_id_arr[$row[csf("fab_dtls_id")]];
											$pre_req_qnty=$fab_req_qty_booking_arr[$po_id][$fab_dtls_id][$color_id]['grey_fab_qnty'];
											$req_amount=$fab_req_qty_booking_arr[$po_id][$fab_dtls_id][$color_id]['amount'];
											$fab_rate=$req_amount/$req_amount;
										}
										//echo $fab_rate.'='.$req_amount.'='.$req_amount.'='.$color_id.'<br>';
										$wo_prev_qnty=$po_fab_prev_color_booking_arr[$po_id][$fabric_description_id][$color_id]['wo_qnty'];
										$wo_prev_amount=$po_fab_prev_color_booking_arr[$po_id][$fabric_description_id][$color_id]['amount'];
									  
										
									}
									else if($sensitivity==4) // Color & Size
									{
										if($fab_req_source==1) //Budget
										{
											$pre_req_qnty=array_sum($conversion_color_size_knit_qty_arr[$fabric_description_id][$po_id][$color_id][$gmts_size_id]);
										}
										else
										{
											$pre_req_qnty=$fab_req_qty_booking_arr[$po_id][$fab_dtls_id][$color_id][$gmts_size_id]['grey_fab_qnty'];
											$req_amount=$fab_req_qty_booking_arr[$po_id][$fab_dtls_id][$color_id][$gmts_size_id]['amount'];
											$fab_rate=$req_amount/$pre_req_qnty;
										}
										$wo_prev_qnty=$po_fab_prev_color_booking_arr[$po_id][$fabric_description_id][$color_id][$gmts_size_id]['wo_qnty'];
										$wo_prev_amount=$po_fab_prev_color_booking_arr[$po_id][$fabric_description_id][$color_id][$gmts_size_id]['amount'];
									}
									else if($sensitivity==2) //  Size 
									{
										if($fab_req_source==1) //Budget
										{
											$pre_req_qnty=array_sum($conversion_po_size_knit_qty_arr[$fabric_description_id][$po_id][$gmts_size_id]);
										}
										else
										{
											$pre_req_qnty=$fab_req_qty_booking_arr[$po_id][$fab_dtls_id][$gmts_size_id]['grey_fab_qnty'];
											$req_amount=$fab_req_qty_booking_arr[$po_id][$fab_dtls_id][$gmts_size_id]['amount'];
											$fab_rate=$req_amount/$pre_req_qnty;
										}
										$wo_prev_qnty=$po_fab_prev_color_booking_arr[$po_id][$fabric_description_id][$gmts_size_id]['wo_qnty'];
										$wo_prev_amount=$po_fab_prev_color_booking_arr[$po_id][$fabric_description_id][$gmts_size_id]['amount'];
									}

									if($sensitivity==3)
									{
										$item_colorID=$contrast_color_arr[$fabric_description_id][$color_id]['contrast_color'];
										if($item_colorID!='')
										{
											$itemColor=$color_library[$contrast_color_arr[$fabric_description_id][$color_id]['contrast_color']];
											$item_color_id=$contrast_color_arr[$fabric_description_id][$color_id]['contrast_color'];
										}
										else
										{
											$itemColor=$color_library[$color_id];
											$item_color_id=$color_id;
										}
									}
									else
									{
										$itemColor=$color_library[$color_id];
										$item_color_id=$color_id;
									}

									$woqnty=$row[csf("wo_qnty")];
									$fin_fab_qnty=$row[csf("fin_fab_qnty")];
									if($fin_fab_qnty=="" || $fin_fab_qnty==0)
									{
									$fin_fab_qnty=$woqnty;	
									}
									$process_loss=$row[csf("process_loss_percent")];
									$amount=$row[csf("amount")];
									$rate=$row[csf("rate")];
									$bal_wo_qty=$pre_req_qnty-$woqnty;
									if($fab_req_source==2) //Fabric
									{
										$req_amount=$fab_rate*$woqnty;
									}
										
									
									//echo $fab_rate.'='.$woqnty.'<br>';


									if($woqnty<=0)
									{
										$td_color='#FF0000';
									}
									else
									{
										$td_color='';
									}
								//echo $woqnty.'-'.$bal_woqnty.'<BR/>';
					?>
						<tr align="center">
							<td>
								<input type="text" name="txt_job_no_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_job_no_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $row[csf("job_no")]; ?>" style="width:90px;" class="text_boxes">
							</td>
							<td>
								<?
									echo create_drop_down("po_no_".$fabric_description_id."_".$i, 100, $po_number_arr,"", 1,'', $po_id,"",1);
								?>
								<input type="hidden" name="po_id_<? echo $fabric_description_id.'_'.$i; ?>" id="po_id_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $po_id; ?>" style="width:110px;" class="text_boxes" disabled="disabled">
							</td>
							<td>
								<?
									echo create_drop_down("fabric_description_".$fabric_description_id."_".$i, 250, $fabric_description_array,"", 1,'', $fabric_description_id,"",1);
								?>
								<input type="hidden" name="fabric_description_id_<? echo $fabric_description_id.'_'.$i; ?>" id="fabric_description_id_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $fabric_description_id; ?>" style="width:110px;" class="text_boxes" disabled="disabled">
							</td>

							<td>
								<input type="text" name="artworkno_<? echo $fabric_description_id.'_'.$i; ?>" id="artworkno_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $row[csf("artwork_no")]; ?>" style="width:80px;" onBlur="copy_value('<? echo $fabric_description_id; ?>','<? echo $i; ?>','artworkno_')" class="text_boxes">
							</td>
							<td>
                                    <input type="text" name="txt_ycount_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_ycount_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $row[csf("yarn_count")]; ?>" style="width:80px;" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'ycount');" class="text_boxes">
                            </td>
							<td>
								<input type="text" name="txt_labdip_no_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_labdip_no_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $row[csf("labdip_no")]; ?>" style="width:80px;" onBlur="copy_value('<? echo $fabric_description_id; ?>','<? echo $i; ?>','labdip_no')" class="text_boxes">
							</td>
							<td>
								<input type="text" name="txt_option_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_option_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $row[csf("option_shade")]; ?>" style="width:80px;" onBlur="copy_value('<? echo $fabric_description_id; ?>','<? echo $i; ?>','option_shade')" class="text_boxes">
							</td>


								<td>
                                    <input type="text" name="txt_lot_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_lot_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $row[csf("lot_no")]; ?>" style="width:80px;" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'lotno');" class="text_boxes">
                                </td>
								<td>
                                    <input type="text" name="txt_brand_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_brand_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $row[csf("brand")]; ?>" style="width:80px;" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'brand');" class="text_boxes">
                                </td>

								<td>
                                    <input type="text" name="txt_mcdia_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_mcdia_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $row[csf("mc_dia")]; ?>" style="width:80px;" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'mcdia');" class="text_boxes">
                                </td>


							<td>
                            <input type="hidden" name="color_size_table_id_<? echo $fabric_description_id.'_'.$i; ?>" id="color_size_table_id_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<?  echo $row[csf("color_size_table_id")];?>" disabled="disabled"/>

								 <input type="hidden" name="gmts_color_<? echo $fabric_description_id.'_'.$i; ?>" id="gmts_color_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<? if($sensitivity==1 || $sensitivity==3 || $sensitivity==4  ){ echo $color_library[$color_id];} else { echo "";}?>" disabled="disabled"/> 
								<? if($sensitivity==1 || $sensitivity==3 || $sensitivity==4  ){ echo $color_library[$color_id];} else { echo "";} ?>
                                <input type="hidden" name="gmts_color_id_<? echo $fabric_description_id.'_'.$i; ?>" id="gmts_color_id_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<? if($sensitivity==1 || $sensitivity==3 || $sensitivity==4 ){ echo $color_id;} else { echo "";}?>"disabled="disabled"/>
							</td>
							<td>
								 <input type="text" name="item_color_<? echo $fabric_description_id.'_'.$i; ?>" id="item_color_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes" onChange="copy_value()" value="<? echo $itemColor;//if($sensitivity==1 || $sensitivity==3  || $sensitivity==4 ){ echo $color_library[$itemColor];} else { echo "";}?>"/>
                                <input type="hidden" name="item_color_id_<? echo $fabric_description_id.'_'.$i; ?>" id="item_color_id_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<? echo $item_color_id;//if($sensitivity==1 || $sensitivity==3  || $sensitivity==4 ){ echo $itemColor;} else { echo "";}?>" disabled="disabled"/>
								 <input type="hidden" name="updateid_<? echo $fabric_description_id.'_'.$i; ?>" id="updateid_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $row[csf('dtls_id')]?>">
							</td>
                             <td>
								<input type="text" name="gmts_size_<? echo $fabric_description_id.'_'.$i; ?>" id="gmts_size_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<? echo $size_library[$gmts_size_id];?>" disabled="disabled"/>
                                <input type="hidden" name="gmts_size_id_<? echo $fabric_description_id.'_'.$i; ?>" id="gmts_size_id_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<?  echo $gmts_size_id;?>" disabled="disabled"/>
							</td>
                            <td>
								<input type="text" name="item_size_<? echo $fabric_description_id.'_'.$i; ?>" id="item_size_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes" onBlur="copy_value('<? echo $fabric_description_id; ?>','<? echo $i; ?>','item_size_')" value="<? echo $row[csf("item_size")];?>">
                                <input type="hidden" name="item_size_id_<? echo $fabric_description_id.'_'.$i; ?>" id="item_size_id_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<? i//f($sensitivity==2 || $sensitivity==4 ){ echo $row[csf("item_size")];} else{ echo "";}?>" disabled="disabled"/>
								
							</td>

                            <td>
								<input type="text" name="subcon_supplier_compo_<? echo $fabric_description_id.'_'.$i; ?>" id="subcon_supplier_compo_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="" onDblClick="service_supplier_popup('<? echo $fabric_description_id.'_'.$i; ?>')" placeholder="Browse" <?php echo $fab_mapping_disable; ?>>

								<input type="hidden" name="subcon_supplier_rateid_<? echo $fabric_description_id.'_'.$i; ?>" id="subcon_supplier_rateid_<? echo $fabric_description_id.'_'.$i; ?>" value="">
							</td>
							<td  width="80">
							 	<? echo number_format($bal_wo_qty,2);?>
							</td>
							<td   title="<? echo 'Req. Qty='.$pre_req_qnty.' Prev Wo Qty='.$wo_prev_qnty.' Balance Wo Qty='.$bal_wo_qty;?>">
								<input type="text" name="txt_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" style="width:60px; background:<? echo $td_color;?>" class="text_boxes_numeric" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'txt_woqnty');calculate_amount(<? echo $fabric_description_id; ?>,<? echo $i; ?>)" value="<? echo  number_format($woqnty,2, ".", ""); ?>"/>

								<input type="hidden" name="txt_hidden_bal_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_hidden_bal_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" style="width:60px;" class="text_boxes_numeric"  value="<? echo number_format($bal_wo_qty,2, ".", ""); ?>"/>
									 <input type="hidden" name="txt_reqqty_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_reqqty_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric" value="<? echo number_format($pre_req_qnty,2, ".", ""); ?>"  />
									  <input type="hidden" name="txt_prev_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_prev_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric" value="<? echo number_format($wo_prev_qnty,2, ".", "")?>" />

							</td>
                            <td title="<?=$budgetRate;?>">
								<input type="text" name="txt_rate_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_rate_<? echo $fabric_description_id.'_'.$i; ?>" style="width:60px;" class="text_boxes_numeric" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'txt_rate');calculate_amount(<? echo $fabric_description_id; ?>,<? echo $i; ?>)" pre-cost-rate="<? echo $budgetRate; ?>" value="<? echo $rate; ?>" <?//php echo $rate_disable; ?>>
								 
							</td> 

							   <td>
								<input type="text" name="findia_<? echo $fabric_description_id.'_'.$i; ?>" id="findia_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $row[csf("dia_width")]; ?>"  style="width:100px;" onBlur="copy_value('<? echo $fabric_description_id; ?>','<? echo $i; ?>','findia_')" class="text_boxes">
							</td>
							 <td>
								<input type="text" name="txt_fingsm_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_fingsm_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $row[csf("fin_gsm")]; ?>"  style="width:100px;" onBlur="copy_value('<? echo $fabric_description_id; ?>','<? echo $i; ?>','fingsm')" class="text_boxes">
							</td>
								<td>
                                    <input type="text" name="txt_slength_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_slength_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $row[csf("slength")]; ?>" style="width:80px;" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'slength');" class="text_boxes">
                                </td>
							  <td>
								<input type="text" name="startdate_<? echo $fabric_description_id.'_'.$i; ?>" id="startdate_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo change_date_format($row[csf("delivery_date")],"dd-mm-yyyy","-"); ?>" style="width:70px;" onChange="copy_value('<? echo $fabric_description_id; ?>','<? echo $i; ?>','startdate_')" class="datepicker">
							</td>
                            <td>
								<input type="text" name="enddate_<? echo $fabric_description_id.'_'.$i; ?>" id="enddate_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo change_date_format($row[csf("delivery_end_date")],"dd-mm-yyyy","-"); ?>" style="width:70px;" onChange="copy_value('<? echo $fabric_description_id; ?>','<? echo $i; ?>','enddate_')" class="datepicker">
							</td>
                            <td>
								<?
								echo create_drop_down("uom_".$fabric_description_id."_".$i, 50, $unit_of_measurement,"", 1, "--Select--",  $row[csf("uom")],"copy_value(".$fabric_description_id.",".$i.",'uom')","", $row[("uom")]);
								?>
							</td>


                          
                            <td>
								<input type="text" name="txt_amount_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_amount_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric"  value="<? echo $amount; ?>" disabled="disabled"/>

								<input type="hidden" name="hidd_amount_<? echo $fabric_description_id.'_'.$i; ?>" id="hidd_amount_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric"  value="<? echo $amount; ?>" disabled="disabled"/>
								<input type="hidden" name="txt_pre_amount_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_pre_amount_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric"  value="<? echo $req_amount; ?>" disabled="disabled"/>
                                 <input type="hidden" name="txt_priv_amount_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_priv_amount_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric"  value="<? echo number_format($wo_prev_amount,4,'.',''); ?>" disabled="disabled"/>
								
							</td>
                              <td>
								<input type="text" name="txt_process_loss_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_process_loss_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'txt_process_loss');calculate_process_per(<? echo $fabric_description_id; ?>,<? echo $i; ?>)"  value="<? echo $process_loss; ?>" />
								 
							</td>
                              <td>
								<input type="text" name="txt_fin_qty_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_fin_qty_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric"  value="<? echo $fin_fab_qnty; ?>" readonly/>
							</td>
                            
                            <td>
								<input type="text" name="txt_pcs_qty_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_pcs_qty_<? echo $fabric_description_id.'_'.$i; ?>" style="width:60px;" class="text_boxes_numeric"  value="<?  echo  $row[csf("req_qty")]; ?>" >
							</td>
                            <td>
								<input type="text" name="txt_paln_cut_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_paln_cut_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric"  value="<? echo  $row[csf("plan_cut_qnty")]; ?>" disabled>
							</td>
							<td></td>
						</tr>
					<?
						$i++;

							}
						//}
					//}
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
	$po_number_arr=return_library_array( "select b.id,b.po_number from wo_po_break_down b,wo_booking_dtls c where c.job_no=b.job_no_mst and c.booking_no='$data[1]' and b.status_active=1  and c.status_active=1  ", "id", "po_number"  );

	$wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select  c.job_no,c.id,c.fabric_description,c.cons_process from wo_booking_dtls b,wo_pre_cost_fab_conv_cost_dtls c  where c.id=b.pre_cost_fabric_cost_dtls_id and b.booking_no='$data[1]'  and b.status_active=1  and c.status_active=1 group by c.job_no,c.id,c.fabric_description,c.cons_process");
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
  	where b.booking_mst_id=a.id and a.booking_no='$data[1]'and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0
	and b.process=31 and a.entry_form=229
  	group by b.job_no,a.id,b.pre_cost_fabric_cost_dtls_id,b.process,b.sensitivity,b.booking_no,b.insert_date";
	//echo $sql;
		?>
    <div id="" style="" class="accord_close">

        <table class="rpt_table" border="1" width="1100" cellpadding="0" cellspacing="0" rules="all" id="">
            <thead>
                <th width="50px">Sl</th>
                <th width="300px">Fabric Description</th>
                <th width="100px">Job No</th>
                <th width="100px">Booking No</th>
                <th width="200px">Po Number</th>
                <th width="100px">Process </th>
                <th width="120px">Sensitivity</th>
                <th width="80px">WO. Qnty</th>
                <th width="80px">Amount</th>
                <th></th>
            </thead>
            <tbody>
            <?
            $dataArray=sql_select($sql);

            $i=1;
            foreach($dataArray as $row)
            {
				$allorder="";
				$all_po_number=explode(",",$row[csf('order_id')]);
				foreach($all_po_number as $po_id)
				{
					if($allorder!="") 	$allorder.=",".$po_number_arr[$po_id];
					else 	$allorder=$po_number_arr[$po_id];

				}
                $all_po_nos=implode(",",array_unique(explode(",",$allorder)));
            ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick='update_booking_data("<? echo $row[csf("dtls_id")]."_".$row[csf("job_no")]."_".$row[csf("pre_cost_fabric_cost_dtls_id")]."_".$row[csf("process")]."_".$row[csf("sensitivity")]."_".$row[csf("order_id")]."_".$row[csf("booking_no")]."_".$all_po_nos;?>")' style="cursor:pointer" >
                    <td> <? echo $i; ?>

                        <input type="hidden" name="po_id_<? echo $fabric_description_id.'_'.$i; ?>" id="po_id_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $row[csf("po_break_down_id")]; ?>" style="width:110px;" class="text_boxes" disabled="disabled">
                    </td>
                    <td><p><? echo  $fabric_description_array[$row[csf('pre_cost_fabric_cost_dtls_id')]]; ?></p> </td>

                    <td>	<? echo  $row[csf('job_no')]; ?></td>
                    <td>	<? echo  $row[csf('booking_no')]; ?></td>
                    <td>	<p><? echo  $all_po_nos; ?></p></td>
                    <td>	<? echo  $conversion_cost_head_array[$row[csf('process')]]; ?></td>
                    <td>	<? echo  $size_color_sensitive[$row[csf('sensitivity')]]; ?></td>
                    <td>	<? echo  number_format($row[csf('wo_qnty')],2); ?></td>
                    <td>	<? echo  number_format($row[csf('amount')],2); ?></td>
                    <td></td>
                </tr>
            <?
            $i++;
            }
            ?>
            </tbody>
        </table>
    </div>
		<?
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
		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; disconnect($con); die;}
		$response_booking_no="";
		$new_booking_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'DSB', date("Y",time()), 5,"select id, booking_no_prefix, booking_no_prefix_num from wo_booking_mst where company_id=$cbo_company_name and booking_type=3 and entry_form=229 and to_char(insert_date,'YYYY')=".date('Y',time())." order by id desc ", "booking_no_prefix", "booking_no_prefix_num" ));
		
		//txt_remark
		$id=return_next_id( "id", "wo_booking_mst", 1 ) ;
		$field_array="id, booking_type, entry_form,is_short, booking_month, booking_year, booking_no_prefix, booking_no_prefix_num, booking_no, company_id, buyer_id, item_category, supplier_id, currency_id, exchange_rate, booking_date, delivery_date, pay_mode, source, attention, tenor, remarks, process, delivery_to, ready_to_approved, inserted_by, insert_date, status_active, is_deleted";
		$data_array ="(".$id.",3,229,".$cbo_short_type.",".$cbo_booking_month.",".$cbo_booking_year.",'".$new_booking_no[1]."',".$new_booking_no[2].",'".$new_booking_no[0]."',".$cbo_company_name.",".$cbo_buyer_name.",12,".$cbo_supplier_name.",".$cbo_currency.",".$txt_exchange_rate.",".$txt_booking_date.",".$txt_delivery_date.",".$cbo_pay_mode.",".$cbo_source.",".$txt_attention.",".$txt_tenor.",".$txt_remark.",".$cbo_process.",".$txt_delivery_to.",".$cbo_ready_to_approved.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
		$response_booking_no=$new_booking_no[0];
		// echo "insert into wo_booking_mst($field_array)values".$data_array;die;
		$rID=sql_insert("wo_booking_mst",$field_array,$data_array,0);
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

		 $dyeing_booking_no=return_field_value("booking_no","inv_receive_master","booking_no=$txt_booking_no and entry_form=37 and status_active=1 and is_deleted=0","booking_no");
		if($dyeing_booking_no!="") {
			echo "14**Booking Found.Update Not Allowed..";
			die;
		}
		 $field_array_up="booking_type*is_short*booking_month*booking_year*buyer_id*item_category*supplier_id*currency_id*exchange_rate*booking_date*delivery_date*pay_mode*source*attention*tenor*remarks*delivery_to*ready_to_approved*updated_by*update_date";
		 $data_array_up ="3*".$cbo_short_type."*".$cbo_booking_month."*".$cbo_booking_year."*".$cbo_buyer_name."*12*".$cbo_supplier_name."*".$cbo_currency."*".$txt_exchange_rate."*".$txt_booking_date."*".$txt_delivery_date."*".$cbo_pay_mode."*".$cbo_source."*".$txt_attention."*".$txt_tenor."*".$txt_remark."*".$txt_delivery_to."*".$cbo_ready_to_approved."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		 //=======================================================================================================
		 $rID=sql_update("wo_booking_mst",$field_array_up,$data_array_up,"booking_no","".$txt_booking_no."",0);
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

		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		$rID=sql_delete("wo_booking_mst",$field_array,$data_array,"booking_no","".$txt_booking_no."",1);
		$rID1=execute_query( "update wo_booking_dtls set status_active=0,is_deleted=1,updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."'   where  booking_no=$txt_booking_no",0);

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

if ($action=="save_update_delete_dtls")
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

		 if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0";  disconnect($con);die;}
		 $id_dtls=return_next_id( "id", "wo_booking_dtls", 1 ) ;
		 //process_loss_percent,fin_fab_qnty SUB_PROCESS 

		 $field_array1="id,entry_form_id,booking_mst_id,pre_cost_fabric_cost_dtls_id,artwork_no,labdip_no,option_shade,slength,mc_dia,fin_gsm,yarn_count,lot_no,brand,po_break_down_id,color_size_table_id,job_no,booking_no,booking_type,fabric_color_id, gmts_color_id,gmts_size,item_size,description,uom,process,sensitivity,wo_qnty,req_qty,rate,amount,delivery_date,delivery_end_date, dia_width,lib_composition, lib_supplier_rate_id,process_loss_percent,fin_fab_qnty,sub_process_id, inserted_by, insert_date";
		 $new_array_color=array();
		 for ($i=1;$i<=$row_num;$i++)
		 {
			 $po_id="po_id_".$hide_fabric_description."_".$i;
			  $txt_job_no="txt_job_no_".$hide_fabric_description."_".$i;
			 $fabric_description_id="fabric_description_id_".$hide_fabric_description."_".$i;
			 $artworkno="artworkno_".$hide_fabric_description."_".$i;

			 $txt_labdip_no="txt_labdip_no_".$hide_fabric_description."_".$i;
			 $txt_option="txt_option_".$hide_fabric_description."_".$i;
			 $txt_mcdia="txt_mcdia_".$hide_fabric_description."_".$i;
			// $txt_gg="txt_gg_".$hide_fabric_description."_".$i;
			 $txt_slength="txt_slength_".$hide_fabric_description."_".$i;
			 $txt_fingsm="txt_fingsm_".$hide_fabric_description."_".$i;
			 $txt_ycount="txt_ycount_".$hide_fabric_description."_".$i;
			 $txt_lot="txt_lot_".$hide_fabric_description."_".$i;
			 $txt_brand="txt_brand_".$hide_fabric_description."_".$i;
			$txt_fin_qty="txt_fin_qty_".$hide_fabric_description."_".$i;
			$txt_process_loss="txt_process_loss_".$hide_fabric_description."_".$i;


             $color_size_table_id="color_size_table_id_".$hide_fabric_description."_".$i;
			 $gmts_color_id="gmts_color_id_".$hide_fabric_description."_".$i;
			 $item_color_id="item_color_id_".$hide_fabric_description."_".$i;
			 $item_color="item_color_".$hide_fabric_description."_".$i;
			 $gmts_size_id="gmts_size_id_".$hide_fabric_description."_".$i;
			 $item_size="item_size_".$hide_fabric_description."_".$i;
			 $txt_pcs_qty="txt_pcs_qty_".$hide_fabric_description."_".$i;
			 $uom="uom_".$hide_fabric_description."_".$i;
			 $txt_woqnty="txt_woqnty_".$hide_fabric_description."_".$i;
			 $txt_rate="txt_rate_".$hide_fabric_description."_".$i;
			 $txt_amount="txt_amount_".$hide_fabric_description."_".$i;
			 $txt_paln_cut="txt_paln_cut".$hide_fabric_description."_".$i;
			 $updateid="updateid_".$hide_fabric_description."_".$i;
			 $startdate="startdate_".$hide_fabric_description."_".$i;
			 $enddate="enddate_".$hide_fabric_description."_".$i;
			 $findia="findia_".$hide_fabric_description."_".$i;
			 $lib_composition="subcon_supplier_compo_".$hide_fabric_description."_".$i;
			 $lib_supplier_rateId="subcon_supplier_rateid_".$hide_fabric_description."_".$i;

			 $new_array_color=return_library_array( "select a.fabric_color_id,b.id,b.color_name from wo_booking_dtls a, lib_color b where b.id=a.fabric_color_id and a.pre_cost_fabric_cost_dtls_id=".$$fabric_description_id."", "id", "color_name"  );
			 if(str_replace("'","",$$item_color)!="")
			 {
				 if (!in_array(str_replace("'","",$$item_color),$new_array_color))
				 {
					  $color_id = return_id( str_replace("'","",$$item_color), $color_library, "lib_color", "id,color_name","229");
					  $new_array_color[$color_id]=str_replace("'","",$$item_color);
				 }
				 else $color_id =  array_search(str_replace("'","",$$item_color), $new_array_color);
			 }
			 else $color_id =0;
			 
			 if($color_id=='' || $color_id==0) $color_id=0;else $color_id=$color_id;

			 if ($i!=1) $data_array1 .=",";
			 $data_array1 .="(".$id_dtls.",229,".$update_id.",".$$fabric_description_id.",".$$artworkno.",".$$txt_labdip_no.",".$$txt_option.",".$$txt_slength.",".$$txt_mcdia.",".$$txt_fingsm.",".$$txt_ycount.",".$$txt_lot.",".$$txt_brand.",".$$po_id.",".$$color_size_table_id.",".$$txt_job_no.",".$txt_booking_no.",3,".$color_id.",".$$gmts_color_id.",".$$gmts_size_id.",".$$item_size.",".$$fabric_description_id.",".$$uom.",".$cbo_process.",".$cbo_colorsizesensitive.",".$$txt_woqnty.",".$$txt_pcs_qty.",".$$txt_rate.",".$$txt_amount.",".$$startdate.",".$$enddate.",".$$findia.",".$$lib_composition.",".$$lib_supplier_rateId.",".$$txt_process_loss.",".$$txt_fin_qty.",".$txt_sub_process_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		     $id_dtls=$id_dtls+1;
		 }
		// echo "10**";die;
		// echo "10**insert into wo_booking_dtls($field_array1)values".$data_array1;check_table_status( $_SESSION['menu_id'],0);die;
		 $rID=sql_insert("wo_booking_dtls",$field_array1,$data_array1,0);
		 check_table_status( $_SESSION['menu_id'],0);

		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");
				echo "0**".str_replace("'","",$$txt_job_no)."**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$cbo_colorsizesensitive);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$$txt_job_no)."**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$cbo_colorsizesensitive);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);
				echo "0**".str_replace("'","",$$txt_job_no)."**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$cbo_colorsizesensitive);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$$txt_job_no)."**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$cbo_colorsizesensitive);
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
		$receive_mrr=0;
		$sqlre=sql_select("select recv_number from inv_receive_master where booking_no=$txt_booking_no and status_active=1 and is_deleted=0");
		foreach($sqlre as $rows){
			$receive_mrr=$rows[csf('recv_number')];
		}
		if($receive_mrr){
			echo "rec1**".str_replace("'","",$txt_booking_no)."**".$receive_mrr;
			disconnect($con);die;
		}

		

		 $field_array_up1="entry_form_id*pre_cost_fabric_cost_dtls_id*artwork_no*labdip_no*option_shade*slength*mc_dia*fin_gsm*yarn_count*lot_no*brand*po_break_down_id*color_size_table_id*job_no*booking_no*booking_type*fabric_color_id *gmts_color_id*gmts_size*item_size*description*uom*process*sensitivity*wo_qnty*req_qty*rate*amount*delivery_date*delivery_end_date*dia_width*lib_composition*lib_supplier_rate_id*process_loss_percent*fin_fab_qnty*sub_process_id*updated_by*update_date";
		 $new_array_color=array();
		 for ($i=1;$i<=$row_num;$i++)
		 {
			 $po_id="po_id_".$hide_fabric_description."_".$i;
			 $txt_job_no="txt_job_no_".$hide_fabric_description."_".$i;
			 $fabric_description_id="fabric_description_id_".$hide_fabric_description."_".$i;
			 $artworkno="artworkno_".$hide_fabric_description."_".$i;

			 $txt_labdip_no="txt_labdip_no_".$hide_fabric_description."_".$i;
			 $txt_option="txt_option_".$hide_fabric_description."_".$i;
			 $txt_slength="txt_slength_".$hide_fabric_description."_".$i;
			 $txt_mcdia="txt_mcdia_".$hide_fabric_description."_".$i;


			 $txt_fingsm="txt_fingsm_".$hide_fabric_description."_".$i;
			 $txt_ycount="txt_ycount_".$hide_fabric_description."_".$i;
			 $txt_lot="txt_lot_".$hide_fabric_description."_".$i;
			 $txt_brand="txt_brand_".$hide_fabric_description."_".$i;
			 $txt_fin_qty="txt_fin_qty_".$hide_fabric_description."_".$i;
			$txt_process_loss="txt_process_loss_".$hide_fabric_description."_".$i;

             $color_size_table_id="color_size_table_id_".$hide_fabric_description."_".$i;
			 $gmts_color_id="gmts_color_id_".$hide_fabric_description."_".$i;
			 $item_color_id="item_color_id_".$hide_fabric_description."_".$i;
			 $item_color="item_color_".$hide_fabric_description."_".$i;
			 $gmts_size_id="gmts_size_id_".$hide_fabric_description."_".$i;
		     $txt_pcs_qty="txt_pcs_qty_".$hide_fabric_description."_".$i;
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
			 $lib_composition="subcon_supplier_compo_".$hide_fabric_description."_".$i;
			 $lib_supplier_rateId="subcon_supplier_rateid_".$hide_fabric_description."_".$i;

		     $new_array_color=return_library_array( "select a.fabric_color_id,b.id,b.color_name from wo_booking_dtls a, lib_color b
			 where b.id=a.fabric_color_id and a.pre_cost_fabric_cost_dtls_id=".$$fabric_description_id."", "id", "color_name"  );
			 if(str_replace("'","",$$item_color)!="")
			 {
				 if (!in_array(str_replace("'","",$$item_color),$new_array_color))
				 {
					  $color_id = return_id( str_replace("'","",$$item_color), $color_library, "lib_color", "id,color_name","229");
					  $new_array_color[$color_id]=str_replace("'","",$$item_color);
				 }
				 else $color_id =  array_search(str_replace("'","",$$item_color), $new_array_color);
			 }
			  else $color_id =0;

			if(str_replace("'",'',$$updateid)!="")
			{
				$id_arr[]=str_replace("'",'',$$updateid);
				$data_array_up1[str_replace("'",'',$$updateid)] =explode("*",("229*".$$fabric_description_id."*".$$artworkno."*".$$txt_labdip_no."*".$$txt_option."*".$$txt_slength."*".$$txt_mcdia."*".$$txt_fingsm."*".$$txt_ycount."*".$$txt_lot."*".$$txt_brand."*".$$po_id."*".$$color_size_table_id."*".$$txt_job_no."*".$txt_booking_no."*3*".$color_id."*".$$gmts_color_id."*".$$gmts_size_id."*".$$item_size."*".$$fabric_description_id."*".$$uom."*".$cbo_process."*".$cbo_colorsizesensitive."*".$$txt_woqnty."*".$$txt_pcs_qty."*".$$txt_rate."*".$$txt_amount."*".$$startdate."*".$$enddate."*".$$findia."*".$$lib_composition."*".$$lib_supplier_rateId."*".$$txt_process_loss."*".$$txt_fin_qty."*".$txt_sub_process_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			}
		 }

		 $rID=execute_query(bulk_update_sql_statement( "wo_booking_dtls", "id", $field_array_up1, $data_array_up1, $id_arr ),1);
		// echo "10**". bulk_update_sql_statement( "wo_booking_dtls", "id", $field_array_up1, $data_array_up1, $id_arr );die;
         check_table_status( $_SESSION['menu_id'],0);

		if($db_type==0)
		{
			if($rID==1){
				mysql_query("COMMIT");
				echo "1**".str_replace("'","",$$txt_job_no)."**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$cbo_colorsizesensitive);
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$$txt_job_no)."**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$cbo_colorsizesensitive);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{

			if($rID==1)
			{
				oci_commit($con);
				echo "1**".str_replace("'","",$$txt_job_no)."**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$cbo_colorsizesensitive);
			}
			else{
				oci_rollback($con);
				echo "10**".str_replace("'","",$$txt_job_no)."**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$cbo_colorsizesensitive);
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
		$field_array="status_active*is_deleted";
		$data_array="'0'*'1'";
		$txt_all_update_id=str_replace("*",",",str_replace("'","",$txt_all_update_id));
		$rID=sql_multirow_update("wo_booking_dtls",$field_array,$data_array,"id","".$txt_all_update_id."",1);
		//$rID1=sql_delete("wo_booking_dtls",$field_array,$data_array,"booking_no","".$txt_booking_no."",1);
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");
				echo "2**".str_replace("'","",$$txt_job_no)."**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$cbo_colorsizesensitive);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$$txt_job_no)."**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$cbo_colorsizesensitive);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID){
				oci_commit($con);
				echo "2**".str_replace("'","",$$txt_job_no)."**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$cbo_colorsizesensitive);
			}
			else{
				oci_rollback($con);
				echo "10**".str_replace("'","",$$txt_job_no)."**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$cbo_colorsizesensitive);
			}
		}
		disconnect($con);
		die;
	}
}

if ($action=="service_booking_popup")
{
  	echo load_html_head_contents("Booking Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		var permission="<? echo $_SESSION['page_permission']; ?>";
		function js_set_value(booking_no)
		{
			document.getElementById('selected_booking').value=booking_no;
			parent.emailwindow.hide();
		}
		function set_checkvalue()
		{
			if(document.getElementById('chk_job_wo_po').value==0) document.getElementById('chk_job_wo_po').value=1;
			else document.getElementById('chk_job_wo_po').value=0;
		}
    </script>
    </head>
    <body>
        <div align="center" style="width:100%;" >
        <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            <table  cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all" width="980">
                <thead>
                    <tr>
                        <th colspan="6"><?=create_drop_down( "cbo_search_category", 130, $string_search_type,'', 1, "-- Search Catagory --" ); ?></th>
                        <th colspan="2"><input type="checkbox" value="0" onClick="set_checkvalue()" id="chk_job_wo_po">Booking Without Dtls &nbsp;</th>
                    </tr>
                    <tr>
                        <th width="160">Company Name</th>
                        <th width="172">Buyer Name</th>
                        <th width="120">Booking No</th>
                        <th width="120">Job No</th>
						<th width="120">Order No</th>
                        <th width="130" colspan="2">Date Range</th>
                        <th><input type="reset" id="rst" class="formbutton" style="width:100px" onClick="reset_form('searchorderfrm_1','search_div','','','')" ></th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="general">
                        <td> <input type="hidden" id="selected_booking">
                        <?
                        echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "-- Select Company --",  $company, "load_drop_down( 'service_booking_multi_job_wise_dyeing_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );");
                        ?>
                        </td>
                        <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 172, $blank_array,"", 1, "-- Select Buyer --" ); ?>
                        </td>
                        <td>
                            <input type="text" id="txt_booking_no" name="txt_booking_no" class="text_boxes" style="width:100px"  placeholder="Write Booking No">
                        </td>
                        <td>
                            <input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes" style="width:100px" placeholder="Write Job No">
                        </td>
						<td>
							<input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:100px" placeholder="Write Order No">
						</td>
                        <td>
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date">
                        </td>
                        <td>
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date">
                        </td>
                        <td>
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_booking_no').value+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('chk_job_wo_po').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_order_search').value, 'create_booking_search_list_view', 'search_div', 'service_booking_multi_job_wise_dyeing_controller', 'setFilterGrid(\'table_body\',-1)')" style="width:100px;" />
                        </td>
                    </tr>
                    <tr>
                        <td align="center" valign="middle" colspan="8"><?=load_month_buttons(1);  ?></td>
                    </tr>
                </tbody>
            </table>
            <div id="search_div"></div>
        </form>
        </div>
    </body>
    <script>
        load_drop_down( 'service_booking_multi_job_wise_dyeing_controller', <? echo $company;?>, 'load_drop_down_buyer', 'buyer_td' );
    </script>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if ($action=="create_booking_search_list_view")
{
	$data=explode('_',$data);
	
	$company_id=$data[0];
	$buyer_id=$data[1];
	$date_form=$data[2];
	$date_to=$data[3];
	$search_catgory=$data[4];
	$booking_no=$data[5];
	$job_no=$data[6];
	$rpt_type_id=$data[7];
	$bookingyear=$data[8];
	$orderno=$data[9];
	//echo $rpt_type_id.'DD';
	$sql_cond="";
	if ($company_id!=0) $sql_cond =" and a.company_id='$company_id'"; else { echo "Please Select Company First.";  disconnect($con);die; }
	if ($buyer_id!=0) $sql_cond .=" and a.buyer_id='$buyer_id'";

	if($db_type==0)
	{
		$year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$data[8]";
		if ($date_form!="" &&  $date_to!="")  $sql_cond .= "and a.booking_date  between '".change_date_format($date_form, "yyyy-mm-dd", "-")."' and '".change_date_format($date_to, "yyyy-mm-dd", "-")."'";
	}
	if($db_type==2)
	{
		$year_cond=" and to_char(a.insert_date,'YYYY')=$data[8]";
		if ($date_form!="" &&  $date_to!="") $sql_cond .= "and a.booking_date  between '".change_date_format($date_form, "yyyy-mm-dd", "-",1)."' and '".change_date_format($date_to, "yyyy-mm-dd", "-",1)."'";
	}
	if($job_no!="")
	{
		if($search_catgory==1)
		{
			$sql_cond .=" and b.job_no_prefix_num='$job_no'";
		}
		else if($search_catgory==2)
		{
			$sql_cond .=" and b.job_no like '$job_no%'";
		}
		else if($search_catgory==3)
		{
			$sql_cond .=" and b.job_no like '%$job_no'";
		}
		else
		{
			$sql_cond .=" and b.job_no like '%$job_no%'";
		}
	}
	
	if($orderno!="")
	{
		if($search_catgory==1)
		{
			$sql_cond .=" and c.po_number='$orderno'";
		}
		else if($search_catgory==2)
		{
			$sql_cond .=" and c.po_number like '$orderno%'";
		}
		else if($search_catgory==3)
		{
			$sql_cond .=" and c.po_number like '%$orderno'";
		}
		else
		{
			$sql_cond .=" and c.po_number like '%$orderno%'";
		}
	}

	if($booking_no!="") $sql_cond .=" and a.booking_no_prefix_num=$booking_no";

	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier_arr=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
//	$job_no_arr=return_library_array( "select b.id, a.job_no_prefix_num from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst",'id','job_no_prefix_num');
	//$po_no_arr=return_library_array( "select id, po_number from wo_po_break_down",'id','po_number');
	//
	//echo $rpt_type_id.'D';
	//$arr=array (2=>$comp_arr,3=>$buyer_arr,4=>$job_no_arr,5=>$po_no_arr,6=>$item_category,7=>$fabric_source,8=>$suplier_arr);
	if($rpt_type_id==0)
	{
		$sql= "SELECT a.id, a.process, a.pay_mode, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.delivery_date, a.company_id, a.buyer_id, a.job_no, a.po_break_down_id, a.item_category, a.fabric_source, a.supplier_id from wo_booking_mst a,wo_booking_dtls b,wo_po_break_down c where  a.booking_no=b.booking_no and b.po_break_down_id=c.id and a.booking_type=3 and a.entry_form=229 and a.status_active=1 and a.is_deleted=0 and a.process=31 $sql_cond $year_cond group by a.id, a.process, a.pay_mode, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.delivery_date, a.company_id, a.buyer_id, a.job_no, a.po_break_down_id, a.item_category, a.fabric_source, a.supplier_id order by a.id DESC"; 
	}
	else
	{
		$sql_data=sql_select("SELECT a.id from wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c where a.booking_no=b.booking_no and b.po_break_down_id=c.id and a.booking_type=3 and a.entry_form=229 and a.status_active=1 and a.is_deleted=0 and a.process=31 $sql_cond $year_cond group by a.id"); 
			foreach($sql_data as $val){
				$bookingArr[$val[csf('id')]]=$val[csf('id')];
			}
			$booking_ids=implode(",",$bookingArr);
			$booking_ids_cond="";
			if($booking_ids!="")
			{
				$booking_ids_cond="and  a.id not in ($booking_ids)";	
			}
		
		$sql= "SELECT a.id, a.process, a.pay_mode, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.delivery_date, a.company_id, a.buyer_id, a.job_no, a.po_break_down_id, a.item_category, a.fabric_source, a.supplier_id from wo_booking_mst a where a.booking_type=3 and a.entry_form=229 and a.status_active=1 and a.is_deleted=0 and a.process=31  $sql_cond $year_cond order by a.id DESC"; 
	}

	//echo $sql;
	?>
    <table  cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all" width="920" align="center">
    	<thead>
        	<tr>
            	<th width="40">SL</th>
            	<th width="50">Booking No</th>
                <th width="70">Booking Date</th>
                <th width="100">Company</th>
                <th width="100">Buyer</th>
                <th width="70">Delivery Date</th>
                <th width="120">Item Category</th>
                <th width="110">Fabric Source</th>
                <th>Supplier</th>
            </tr>
        </thead>
    </table>
    <div id="scroll_body" style="width:920px; max-height:350; overflow-y:scroll" align="center">
    <table  cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all" width="900" id="table_body">
        <tbody>
        <?
		
		$sql_result=sql_select($sql);
		$i=1;
		foreach($sql_result as $row)
		{
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			if($row[csf("pay_mode")]==3 || $row[csf("pay_mode")]==5)
			{
				$lib_com_sup=$comp_arr[$row[csf("supplier_id")]];
			}
			else
			{
				$lib_com_sup=$suplier_arr[$row[csf("supplier_id")]];
			}
			?>
            <tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $row[csf("booking_no")]; ?>')" style="cursor:pointer;">
                <td width="40" align="center"><? echo $i; ?></td>
                <td width="50" align="center"><p><? echo $row[csf("booking_no_prefix_num")]; ?>&nbsp;</p></td>
                <td width="70" align="center"><p><? if($row[csf("booking_date")]!="" && $row[csf("booking_date")]!="0000-00-00") echo change_date_format($row[csf("booking_date")]); ?>&nbsp;</p></td>
                <td width="100"><p><? echo $comp_arr[$row[csf("company_id")]]; ?>&nbsp;</p></td>
                <td width="100"><p><? echo $buyer_arr[$row[csf("buyer_id")]]; ?>&nbsp;</p></td>
                <td width="70" align="center"><p><? echo change_date_format($row[csf("delivery_date")]); ?>&nbsp;</p></td>

                <td width="120"><p><? echo $item_category[$row[csf("item_category")]]; ?>&nbsp;</p></td>
                <td width="110"><p><? echo $fabric_source[$row[csf("fabric_source")]]; ?>&nbsp;</p></td>
                <td><p><? echo $lib_com_sup; ?>&nbsp;</p></td>
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

			data_all=data_all+get_submitted_data_string('txt_booking_no*termscondition_'+i,"../../../",i);
		}
		var data="action=save_update_delete_fabric_booking_terms_condition&operation="+operation+'&total_row='+row_num+data_all;
		//freeze_window(operation);
		http.open("POST","service_booking_multi_job_wise_dyeing_controller.php",true);
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
					$data_array=sql_select("select id, terms from  lib_terms_condition where is_default=1 and page_id=229");// quotation_id='$data'
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
}

if($action=="show_trim_booking_report")
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	//$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library",'master_tble_id','image_location');
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	//$comp_address_arr=return_library_array( "select id,address_1 from   lib_company",'id','address_1');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
    $user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");

	?>
	<div style="width:1150px" align="left">
       <table width="90%" cellpadding="0" cellspacing="0" style="border:1px solid black">
           <tr>
               <td width="100">
               <img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
               </td>
               <td width="">
                    <table width="90%" cellpadding="0" cellspacing="0" >
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
                            <strong>Service Booking Sheet For Dyeing</strong>
                             </td>
                            </tr>
                      </table>
                </td>
                <td width="250" id="barcode_img_id" >

               </td>
            </tr>
       </table>
		<?
		$booking_grand_total=0;
		$job_no="";
		$nameArray_job=sql_select( "select distinct b.job_no  from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and b.entry_form_id=229");
        foreach ($nameArray_job as $result_job)
        {
			$job_no.=$result_job[csf('job_no')].",";
		}

		$po_no=""; $file=''; $ref='';
		$nameArray_job=sql_select( "select distinct b.po_number,b.id, b.grouping, b.file_no  from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no=$txt_booking_no and a.entry_form_id=229");
        foreach ($nameArray_job as $result_job)
        {
			$po_no.=$result_job[csf('po_number')].",";
			$file.=$result_job[csf('file_no')].",";
			$ref.=$result_job[csf('grouping')].",";
			$po_number[$result_job[csf('id')]]=$result_job[csf('po_number')];
		}
		$file=implode(",",array_unique(explode(",",$file)));
		$ref=implode(",",array_unique(explode(",",$ref)));
        $nameArray=sql_select( "select a.booking_no,a.booking_date,a.pay_mode,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.source,a.inserted_by  from wo_booking_mst a where  a.booking_no=$txt_booking_no and a.entry_form=229");

        foreach ($nameArray as $result)
        {
			$varcode_booking_no=$result[csf('booking_no')];
			$inserted_by=$user_lib_name[$inserted_by=$result[csf('inserted_by')]];
			
			if($result[csf("pay_mode")]==3 || $result[csf("pay_mode")]==5)
			{
				$lib_com_sup=$company_library[$result[csf("supplier_id")]];
				$supplier_address="";
			}
			else
			{
				$lib_com_sup=$supplier_name_arr[$result[csf("supplier_id")]];
				$supplier_address=$supplier_address_arr[$result[csf('supplier_id')]];
			}
        ?>
       <table width="90%" style="border:1px solid black">
            <tr>
                <td colspan="6" valign="top"></td>
            </tr>
            <tr>
                <td width="90" style="font-size:12px"><b>Booking No </b>   </td>
                <td width="150">:&nbsp;<? echo $result[csf('booking_no')];?> </td>
                <td width="90" style="font-size:12px"><b>Booking Date</b></td>
                <td width="150">:&nbsp;<? echo change_date_format($result[csf('booking_date')],'dd-mm-yyyy','-');?>&nbsp;&nbsp;&nbsp;</td>
                <td width="90"><span style="font-size:12px"><b>Delivery Date</b></span></td>
                <td width="150">:&nbsp;<? echo change_date_format($result[csf('delivery_date')],'dd-mm-yyyy','-');?></td>
            </tr>
            <tr>
                <td width="90" style="font-size:12px"><b>Currency</b></td>
                <td width="150">:&nbsp;<? echo $currency[$result[csf('currency_id')]]; ?></td>
                <td  width="90" style="font-size:12px"><b>Conversion Rate</b></td>
                <td  width="150" >:&nbsp;<? echo $result[csf('exchange_rate')]; ?></td>
                <td  width="90" style="font-size:12px"><b>Source</b></td>
                <td  width="150" >:&nbsp;<? echo $source[$result[csf('source')]]; ?></td>
            </tr>
             <tr>
                <td width="90" style="font-size:12px"><b>Supplier Name</b>   </td>
                <td width="150">:&nbsp;<? echo $lib_com_sup;?>    </td>
                 <td width="90" style="font-size:12px"><b>Supplier Address</b></td>
               	<td width="150">:&nbsp;<? echo $supplier_address;?></td>
                <td  width="90" style="font-size:12px"><b>Attention</b></td>
                <td  width="150" >:&nbsp;<? echo $result[csf('attention')]; ?></td>
            </tr>
            <tr>
                <td width="90" style="font-size:12px"><b>Job No</b>   </td>
                <td width="150">:&nbsp;
				<?
				echo rtrim($job_no,',');
				?>
                </td>

               	<td width="150" style="font-size:12px"><b>PO No</b> </td>
                <td width="90" style="font-size:12px" colspan="3">:&nbsp;<? echo rtrim($po_no,','); ?> </td>
            </tr>
            <tr>
                <td width="90" style="font-size:12px"><b>File No</b>   </td>
                <td width="150">:&nbsp;<? echo rtrim($file,','); ?></td>
                <td width="90" style="font-size:12px"><b>Ref. No</b>   </td>
                <td width="150">:&nbsp;<? echo rtrim($ref,','); ?></td>
               	<td width="90" style="font-size:12px"><b>&nbsp;</b> </td>
                <td style="font-size:12px">&nbsp;</td>
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
			$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description from  wo_pre_cost_fabric_cost_dtls  where  id='".$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]."'");
			list($fabric_description_row)=$fabric_description;
			$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]=$body_part[$fabric_description_row[csf("body_part_id")]].', '.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")];
		}
		if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]==0)
		{
			//echo "select id,body_part_id,color_type_id,fabric_description from  wo_pre_cost_fabric_cost_dtls  where  job_no='$data'";
			$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description from  wo_pre_cost_fabric_cost_dtls  where  job_no='".rtrim($job_no,", ")."'");
			//list($fabric_description_row)=$fabric_description;
			foreach( $fabric_description as $fabric_description_row)
	        {
			$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]].=$body_part[$fabric_description_row[csf("body_part_id")]].', '.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")].", ";

			//$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]="All Fabrics  ".$conversion_cost_head_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("cons_process")]];
			}
		}


	}
	if($show_comments==1){$show_colspan="0";}else{$show_colspan="2";}
	//print_r($fabric_description_array);
	//=================================================
        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and entry_form_id=229 and sensitivity=1 and status_active=1 and is_deleted=0 and wo_qnty!=0");
		//echo "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=1";  gmts_color_id
        $nameArray_color=sql_select( "select distinct  fabric_color_id as fabric_color_id from wo_booking_dtls   where  booking_no=$txt_booking_no and entry_form_id=229 and sensitivity=1 and status_active=1 and is_deleted=0 and wo_qnty!=0");
		if(count($nameArray_color)>0)
		{
        ?>
        <table border="0" align="left" class="rpt_table"  cellpadding="0" width="90%" cellspacing="0" >
            <tr>
                <td colspan="<? echo count($nameArray_color)+8-$show_colspan; ?>" align="">
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
				<td style="border:1px solid black" align="center"><strong>FIN GSM</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
				<? if($show_comments==1){?>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
				<?}?>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
				$i++;
            $nameArray_item_description=sql_select( "select distinct description,rate,uom,fin_gsm from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=1 and process=".$result_item[csf('process')]." and status_active=1 and is_deleted=0 and  wo_qnty!=0");
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
                <td style="border:1px solid black"><? echo rtrim($fabric_description_array[$result_itemdescription[csf('description')]],", "); ?> </td>
                <td style="border:1px solid black"><? //echo $result_itemdescription['brand_supplier']; ?> Booking Qnty </td>
                <?
                foreach($nameArray_color  as $result_color)
                {
                $nameArray_color_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls   where   booking_no=$txt_booking_no and sensitivity=1 and process=". $result_item[csf('process')]." and description='". $result_itemdescription[csf('description')]."' and fabric_color_id=".$result_color[csf('fabric_color_id')]." and status_active=1 and is_deleted=0 and wo_qnty!=0");
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
				<td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('fin_gsm')]; ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$result_itemdescription[csf('uom')]]; ?></td>
				<? if($show_comments==1){?>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <?
                $amount_as_per_gmts_color = $item_desctiption_total*  $result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
					<?}?>
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
                <td style="border:1px solid black; text-align:right"><? //echo number_format(round($color_total),0); ?></td>
				<? if($show_comments==1){?>
				<td style="border:1px solid black; text-align:right"></td>
                <td style="border:1px solid black; text-align:right">
                <?
                echo number_format($total_amount_as_per_gmts_color,4);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
					<?}?>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="<? echo count($nameArray_color)+8-$show_colspan; ?>"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,4); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
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


        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and entry_form_id=229 and sensitivity=2 and status_active=1 and is_deleted=0 and wo_qnty!=0");
        $nameArray_size=sql_select( "select distinct  item_size  as gmts_sizes from wo_booking_dtls   where booking_no=$txt_booking_no and entry_form_id=229 and sensitivity=2 and status_active=1 and is_deleted=0 and wo_qnty!=0");
		if(count($nameArray_size)>0)
		{
        ?>

        <table border="0" align="left" cellpadding="0" width="90%" cellspacing="0" >
            <tr>
                <td colspan="<? echo count($nameArray_size)+8-$show_colspan; ?>" align="">
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
				<td style="border:1px solid black" align="center"><strong>FIN GSM</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
				<? if($show_comments==1){?>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
				<?}?>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
			$i++;
            $nameArray_item_description=sql_select( "select distinct description,rate,uom,fin_gsm from wo_booking_dtls  where booking_no=$txt_booking_no and entry_form_id=229 and sensitivity=2 and process=".$result_item[csf('process')]." and status_active=1 and is_deleted=0 and wo_qnty!=0");
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
					$nameArray_size_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls   where   booking_no=$txt_booking_no and sensitivity=2 and process=". $result_item[csf('process')]." and description='". $result_itemdescription[csf('description')]."' and item_size='".$result_size[csf('gmts_sizes')]."' and status_active=1 and is_deleted=0 and wo_qnty!=0");

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
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('fin_gsm')]; ?></td>
				<td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$result_itemdescription[csf('uom')]]; ?></td>
				<? if($show_comments==1){?>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <?
                $amount_as_per_gmts_color = $item_desctiption_total*$result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
				<?}?>
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
                <td style="border:1px solid black; text-align:right"><? //echo number_format(round($color_total),0); ?></td>
				<? if($show_comments==1){?>
				<td style="border:1px solid black; text-align:right"></td>
                <td style="border:1px solid black; text-align:right">
                <?
                echo number_format($total_amount_as_per_gmts_color,4);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
				<?}?>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="<? echo count($nameArray_size)+8-$show_colspan; ?>"><strong>Total</strong></td>
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
        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and entry_form_id=229 and sensitivity=3 and status_active=1 and is_deleted=0 and wo_qnty!=0");
        $nameArray_color=sql_select( "select distinct fabric_color_id as color_number_id from wo_booking_dtls   where booking_no=$txt_booking_no and entry_form_id=229 and sensitivity=3 and status_active=1 and is_deleted=0 and wo_qnty!=0");
		if(count($nameArray_color)>0)
		{
        ?>
        <table border="0" align="left" cellpadding="0" width="90%" cellspacing="0" >
            <tr>
                <td colspan="<? echo count($nameArray_color)+8-$show_colspan; ?>" align="">
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
				<td style="border:1px solid black" align="center"><strong>FIN GSM</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
				<? if($show_comments==1){?>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
				<?}?>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
				$i++;
            $nameArray_item_description=sql_select( "select distinct description,brand_supplier,rate,uom,fin_gsm from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=3 and process=".$result_item[csf('process')]." and status_active=1 and is_deleted=0 and wo_qnty!=0");
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
                $nameArray_color_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls    where   booking_no=$txt_booking_no and sensitivity=3 and process=". $result_item[csf('process')]." and description='". $result_itemdescription[csf('description')]."' and fabric_color_id=".$result_color[csf('color_number_id')]." and status_active=1 and is_deleted=0 and wo_qnty!=0");
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
                <td style="border:1px solid black; text-align:center"><? echo $result_itemdescription[csf('fin_gsm')]; ?></td>
				<td style="border:1px solid black; text-align:center"><? echo $unit_of_measurement[$result_itemdescription[csf('uom')]]; ?></td>
				<? if($show_comments==1){?>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <?
                $amount_as_per_gmts_color = $item_desctiption_total*  $result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
				<?}?>
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
				<td style="border:1px solid black; text-align:right"></td>
				<? if($show_comments==1){?>
                <td style="border:1px solid black; text-align:right"><? //echo number_format(round($color_total),0); ?></td>
                <td style="border:1px solid black; text-align:right">
                <?
                echo number_format($total_amount_as_per_gmts_color,4);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
				<?}?>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="<? echo count($nameArray_color)+8-$show_colspan; ?>"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,4); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
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

        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and entry_form_id=229 and sensitivity=4 and status_active=1 and is_deleted=0 and wo_qnty!=0");
        $nameArray_size=sql_select( "select distinct item_size  as gmts_sizes from wo_booking_dtls   where booking_no=$txt_booking_no and entry_form_id=229 and sensitivity=4 and status_active=1 and is_deleted=0 and wo_qnty!=0");
	    $nameArray_color=sql_select( "select distinct fabric_color_id as color_number_id from wo_booking_dtls   where booking_no=$txt_booking_no and entry_form_id=229 and sensitivity=4 and status_active=1 and is_deleted=0 and wo_qnty!=0");

		if(count($nameArray_size)>0)
		{
        ?>

        <table border="0" align="left" cellpadding="0" width="90%" cellspacing="0" >
            <tr>
                <td colspan="<? echo count($nameArray_size)+8-$show_colspan; ?>" align="">
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
				<td style="border:1px solid black" align="center"><strong>FIN GSM</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
				<? if($show_comments==1){?>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
				<?}?>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
			$i++;
            $nameArray_item_description=sql_select( "select distinct description,brand_supplier,rate,uom,fin_gsm from wo_booking_dtls  where booking_no=$txt_booking_no and entry_form_id=229 and sensitivity=4 and process=".$result_item[csf('process')]." and status_active=1 and is_deleted=0 and wo_qnty!=0");
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
                $nameArray_size_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls   where booking_no=$txt_booking_no and entry_form_id=229 and sensitivity=4 and process=". $result_item[csf('process')]." and  description='". $result_itemdescription[csf('description')]."' and  item_size='".$result_size[csf('gmts_sizes')]."' and fabric_color_id=".$result_color[csf('color_number_id')]." and status_active=1 and is_deleted=0 and wo_qnty!=0");
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
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('fin_gsm')]; ?></td>
				<td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$result_itemdescription[csf('uom')]]; ?></td>
				<? if($show_comments==1){?>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <?
                $amount_as_per_gmts_color = $item_desctiption_total*  $result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
				<?}?>
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
                <td style="border:1px solid black; text-align:right"><? //echo number_format(round($color_total),0); ?></td>
				<? if($show_comments==1){?>
				<td style="border:1px solid black; text-align:right"></td>
                <td style="border:1px solid black; text-align:right">
                <?
                echo number_format($total_amount_as_per_gmts_color,4);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
				<?}?>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="<? echo count($nameArray_size)+9-$show_colspan; ?>"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,4); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
            </tr>
        </table>
        <br/>
        <?
		}
		?>
        <!--==============================================AS PER Color & SIZE  END=========================================  -->


         <!--==============================================NO NENSITIBITY START=========================================  -->
		<?
        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and entry_form_id=229 and sensitivity=0 and status_active=1 and is_deleted=0 and wo_qnty!=0");
        //$nameArray_color=sql_select( "select distinct b.color_number_id from wo_trims_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=$txt_booking_no and a.sensitivity=1");
		$nameArray_color= array();
		if(count($nameArray_item)>0)
		{
        ?>
        <table border="0" align="left" class="rpt_table"  cellpadding="0" width="90%" cellspacing="0" >
            <tr>
                <td colspan="7-<?=$show_colspan;?>" align="">
                <strong>No Sensitivity</strong>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Service Type</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong></strong> </td>
                <td align="center" style="border:1px solid black"><strong> Qnty</strong></td>
                <td style="border:1px solid black" align="center"><strong>Fin GSM</strong></td>
				<td style="border:1px solid black" align="center"><strong>UOM</strong></td>
				<? if($show_comments==1){?>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
				<?}?>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
				$i++;
            $nameArray_item_description=sql_select( "select distinct description,brand_supplier,rate,uom,fin_gsm from wo_booking_dtls  where booking_no=$txt_booking_no and entry_form_id=229 and sensitivity=0 and process=".$result_item['process']." and status_active=1 and is_deleted=0 and wo_qnty!=0");
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
                $nameArray_color_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls where    booking_no=$txt_booking_no and entry_form_id=229 and sensitivity=0 and process=". $result_item[csf('process')]." and  description='". $result_itemdescription[csf('description')]."' and status_active=1 and is_deleted=0 and wo_qnty!=0");
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

                <td style="border:1px solid black; text-align:center "><? echo $unit_of_measurement[$result_itemdescription[csf('uom')]]; ?></td>
				<td style="border:1px solid black; text-align:center "><? echo $result_itemdescription[csf('fin_gsm')]; ?></td>
				<? if($show_comments==1){?>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <?
                $amount_as_per_gmts_color = $item_desctiption_total*  $result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
				<?}?>
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
                <td style="border:1px solid black; text-align:right"></td>
				<? if($show_comments==1){?>
				<td style="border:1px solid black; text-align:right"></td>
                <td style="border:1px solid black; text-align:right">
                <?
                echo number_format($total_amount_as_per_gmts_color,4);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
				<?}?>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="8-<?=$show_colspan;?>"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,4); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
            </tr>
        </table>
        <?
		//print_r($color_tatal);
		}
		?>
        <!--==============================================NO NENSITIBITY END=========================================  -->
       &nbsp;
       <table  width="90%" class="rpt_table" style="border:1px solid black;"   border="0" cellpadding="0" cellspacing="0">
       <tr style="border:1px solid black;">
                <th width="70%" style="border:1px solid black; text-align:right">Total Booking Amount</th><td width="30%" style="border:1px solid black; text-align:right"><? echo number_format($booking_grand_total,4);$booking_grand_total=number_format($booking_grand_total,4);?></td>
            </tr>
            <tr style="border:1px solid black;">
                <th width="70%" style="border:1px solid black; text-align:right">Total Booking Amount (in word)</th><td width="30%" style="border:1px solid black;">
				<?
				$mcurrency="";
				$dcurrency="";
				$currency_id=$result[csf('currency_id')];
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
				$currency_name=$currency[$result[csf('currency_id')]];
				echo number_to_words($booking_grand_total,$mcurrency, $dcurrency);
				?></td>
            </tr>
       </table>
          &nbsp;
        <table  width="90%" class="rpt_table" style="border:1px solid black;"   border="0" cellpadding="0" cellspacing="0">
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
        $data_array=sql_select("select id, terms from  lib_terms_condition where is_default=1 and page_id=229");// quotation_id='$data'
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
    <br><? if ($show_comments!=1) { ?>
    <table border="0" cellpadding="0" cellspacing="0"  width="90%" class="rpt_table"  style="border:1px solid black;" >
                <tr> <td style="border:1px solid black;" colspan="9" align="center"><b> Comments</b> </td></tr>
                <tr style="border:1px solid black;" align="center">
                    <th style="border:1px solid black;" width="40">SL</th>
                    <th style="border:1px solid black;" width="200">Job No</th>
                    <th style="border:1px solid black;" width="200">PO No</th>
                    <th style="border:1px solid black;" width="80">Ship Date</th>
                    <th style="border:1px solid black;" width="80">Pre-Cost/Budget Value</th>
                    <th style="border:1px solid black;" width="80">WO Value</th>

                    <th style="border:1px solid black;" width="80">Balance</th>
                    <th style="border:1px solid black;" width="">Comments </th>
                </tr>
       <tbody>
       <?
					$job_no=rtrim($job_no,',');
					$job_nos=implode(",",array_unique(explode(",",$job_no)));
					$condition= new condition();
					if(str_replace("'","",$job_nos) !=''){
					$condition->job_no("in('$job_nos')");
					}
					$condition->init();
					$conversion= new conversion($condition);
					//echo $conversion->getQuery();
					$convAmt=$conversion->getAmountArray_by_orderAndProcess();
					//print_r($convAmt);
					$po_qty_arr=array();$aop_data_arr=array();
					$sql_po_qty=sql_select("select b.id as po_id,b.pub_shipment_date,sum(b.po_quantity) as order_quantity,(sum(b.po_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst   and a.is_deleted=0  and a.status_active=1 group by b.id,a.total_set_qnty,b.pub_shipment_date");
					foreach( $sql_po_qty as $row)
					{
						$po_qty_arr[$row[csf("po_id")]]['order_quantity']=$row[csf("order_quantity_set")];
						$po_qty_arr[$row[csf("po_id")]]['pub_shipment_date']=$row[csf("pub_shipment_date")];
					}
					$pre_cost=sql_select("select job_no,sum(amount) AS aop_cost from wo_pre_cost_fab_conv_cost_dtls where cons_process=31 and status_active=1 and is_deleted=0 group by job_no");
					foreach($pre_cost as $row)
					{
						$aop_data_arr[$row[csf('job_no')]]['aop']=$row[csf('aop_cost')];
					}

					$i=1; $total_balance_aop=0;$tot_aop_cost=0;$tot_pre_cost=0;

					$sql_aop=( "select b.po_break_down_id as po_id,a.job_no,sum(b.amount) as amount from wo_booking_mst a, wo_booking_dtls b    where a.job_no=b.job_no and a.booking_no=b.booking_no  and a.booking_no=$txt_booking_no and b.entry_form_id=229 and a.booking_type=3  and a.item_category=12 and  a.status_active=1  and a.is_deleted=0  group by b.po_break_down_id,a.job_no  order by b.po_break_down_id");

                    $nameArray=sql_select( $sql_aop );
                    foreach ($nameArray as $selectResult)
                    {
						$costing_per=return_field_value("costing_per", "wo_pre_cost_mst", "job_no='".$selectResult[csf('job_no')]."'");
						//echo $costing_per;
						//echo $selectResult[csf('job_no')];
						if($costing_per==1)
						{
							$costing_per_qty=12;
						}
						else if($costing_per==2)
						{
							$costing_per_qty=1;
						}
						else if($costing_per==3)
						{
							$costing_per_qty=24;
						}
						else if($costing_per==4)
						{
							$costing_per_qty=36;
						}
						else if($costing_per==5)
						{
							$costing_per_qty=48;
						}
						$po_qty=$po_qty_arr[$selectResult[csf('po_id')]]['order_quantity'];
						$pre_cost_aop=$pre_cost_dyeing=array_sum($convAmt[$selectResult[csf("po_id")]][31]);//($aop_data_arr[$selectResult[csf('job_no')]]['aop']/$costing_per_qty)*$po_qty;


						$wo_aop_charge=$selectResult[csf("amount")];
						$ship_date=$po_qty_arr[$selectResult[csf("po_id")]]['pub_shipment_date'];

						if($db_type==0)
						{
						$conversion_date=change_date_format($result[csf('booking_date')], "Y-m-d", "-",1);
						}
						else
						{
						$conversion_date=change_date_format($result[csf('booking_date')], "d-M-y", "-",1);
						}

						//echo $currency_rate;
						if($currency_id==1)
						{
							$currency_rate=set_conversion_rate( 2, $conversion_date );
							$aop_charge=$wo_aop_charge/$currency_rate;
						}
						else
						{
							$aop_charge=$wo_aop_charge;
						}
	   ?>
                    <tr>
                    <td style="border:1px solid black;" width="40"><? echo $i;?></td>
                    <td style="border:1px solid black;" width="200">
					<? echo $selectResult[csf('job_no')];?>
                    </td>
                    <td style="border:1px solid black;" width="200">
					<? echo $po_number[$selectResult[csf('po_id')]];?>
                    </td>
                    <td style="border:1px solid black;" width="80" align="right">
					<? echo change_date_format($ship_date);?>

                    </td>
                     <td style="border:1px solid black;" width="80" align="right">
                     <? echo number_format($pre_cost_aop,2); ?>
                    </td>
                     <td style="border:1px solid black;" width="80" align="right">
                    <? echo number_format($aop_charge,2); ?>
                    </td>

                    <td style="border:1px solid black;" width="80" align="right">
                       <? $tot_balance=$pre_cost_aop-$aop_charge; echo number_format($tot_balance,2); ?>
                    </td>
                    <td style="border:1px solid black;" width="">
                    <?
					if( $pre_cost_aop>$aop_charge)
						{
						echo "Less Booking";
						}
					else if ($pre_cost_aop<$aop_charge)
						{
						echo "Over Booking";
						}
					else if ($pre_cost_aop==$aop_charge)
						{
							echo "As Per";
						}
					else
						{
						echo "";
						}
						?>
                    </td>
                    </tr>
	   <?
	  	 $tot_pre_cost+=$pre_cost_aop;
	  	 $tot_aop_cost+=$aop_charge;
		 $total_balance_aop+=$tot_balance;
	   $i++;
					}
       ?>
	</tbody>
        <tfoot>
            <tr>
                <td style="border:1px solid black;" colspan="4" align="right">  <b>Total</b></td>
                <td style="border:1px solid black;" align="right"> <b><? echo number_format($tot_pre_cost,2); ?></b></td>
                <td style="border:1px solid black;"  align="right"><b> <? echo number_format($tot_aop_cost,2); ?> </b></td>
                <td style="border:1px solid black;"  align="right"><b> <? echo number_format($total_balance_aop,2); ?></b> </td>
                <td style="border:1px solid black;">&nbsp;  </td>
             </tr>
        </tfoot>
    </table>
        <? } ?>
         <br/>
		 

		 <?
		 $hh="";
		 
		echo signature_table(290, $cbo_company_name, "1113px","",40,$inserted_by);

         ?>
    </div>
    <script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <script>
    fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');
    </script>
<?
exit();
}


if($action=="show_trim_booking_report1")
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	//$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library",'master_tble_id','image_location');
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
    $user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");

	//$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	?>
	<div style="width:1150px" align="left">
       <table width="90%" cellpadding="0" cellspacing="0" style="border:1px solid black">
           <tr>
               <td width="100">
               <img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
               </td>
               <td width="1050">
                    <table width="100%" cellpadding="0" cellspacing="0" >
                        <tr>
                            <td align="center" style="font-size:20px;">
                              <?php echo $company_library[$cbo_company_name];?>
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
                            <strong>Service Booking Sheet For Dyeing</strong>
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
		$nameArray_job=sql_select( "select distinct b.job_no  from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_no=$txt_booking_no  and b.entry_form_id=229");
        foreach ($nameArray_job as $result_job)
        {
			$job_no.=$result_job[csf('job_no')].",";
		}
		$po_no="";$po_id="";
		$nameArray_job=sql_select( "select distinct b.id as po_id,b.po_number  from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no=$txt_booking_no  and a.entry_form_id=229");
        foreach ($nameArray_job as $result_job)
        {
			$po_no.=$result_job[csf('po_number')].",";
			$po_id.=$result_job[csf('po_id')].",";
			$po_number[$result_job[csf('po_id')]]=$result_job[csf('po_number')];
		}
        $nameArray=sql_select( "select a.booking_no,a.pay_mode,a.booking_date,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.source,a.inserted_by  from wo_booking_mst a where  a.booking_no=$txt_booking_no  and a.entry_form=229");
		//echo  "select po_break_down_id,article_number from wo_po_color_size_breakdown where po_break_down_id in(".rtrim($po_id,',').")";

		$article_number_arr=return_library_array( "select po_break_down_id,article_number from wo_po_color_size_breakdown where po_break_down_id in(".rtrim($po_id,',').")", "po_break_down_id", "article_number"  );
		//print_r($article_number_arr);
		$booking_date=$nameArray[0][csf('booking_date')];
		$inserted_by=$user_lib_name[$nameArray[0][csf('inserted_by')]];

        foreach ($nameArray as $result)
        {
			if($result[csf("pay_mode")]==3 || $result[csf("pay_mode")]==5)
			{
				$lib_com_sup=$company_library[$result[csf("supplier_id")]];
				$supplier_address="";
			}
			else
			{
				$lib_com_sup=$supplier_name_arr[$result[csf("supplier_id")]];
				$supplier_address=$supplier_address_arr[$result[csf('supplier_id')]];
			}
        ?>
       <table width="90%" style="border:1px solid black">
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
                <td width="110">:&nbsp;<? echo $lib_com_sup;?>    </td>
            </tr>
             <tr>
                <td width="100" style="font-size:12px"><b>Supplier Address</b></td>
               	<td width="110" colspan="3">:&nbsp;<? echo $supplier_address;//$supplier_address_arr[$result[csf('supplier_id')]];?></td>

            </tr>
            <tr>
                <td width="100" style="font-size:12px"><b>Job No</b>   </td>
                <td width="110" colspan="3">:&nbsp;
				<?
				echo rtrim($job_no,',');
				?>
                </td>
            </tr>
            <tr>
               	<td width="110" style="font-size:12px"><b>PO No</b> </td>
                <td  width="100" style="font-size:12px" colspan="3">:&nbsp;<? echo rtrim($po_no,','); ?> </td>
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
			$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description from  wo_pre_cost_fabric_cost_dtls  where  id='".$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]."'");
			list($fabric_description_row)=$fabric_description;
			$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]=$body_part[$fabric_description_row[csf("body_part_id")]].', '.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")];
		}
		if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]==0)
		{
			//echo "select id,body_part_id,color_type_id,fabric_description from  wo_pre_cost_fabric_cost_dtls  where  job_no='$data'";
			$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description from  wo_pre_cost_fabric_cost_dtls  where  job_no='".rtrim($job_no,", ")."'");
			//list($fabric_description_row)=$fabric_description;
			foreach( $fabric_description as $fabric_description_row)
	        {
			$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]].=$body_part[$fabric_description_row[csf("body_part_id")]].', '.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")].", ";

			//$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]="All Fabrics  ".$conversion_cost_head_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("cons_process")]];
			}
		}


	}
	//print_r($fabric_description_array);
	//=================================================
        $nameArray_item=sql_select( "select distinct process,description from wo_booking_dtls  where booking_no=$txt_booking_no  and entry_form_id=229 and sensitivity=1 and status_active=1 and is_deleted=0 and wo_qnty!=0");//and sensitivity=1
        $nameArray_color=sql_select( "select distinct fabric_color_id from wo_booking_dtls   where  booking_no=$txt_booking_no  and entry_form_id=229 and sensitivity=1 and status_active=1 and is_deleted=0 and wo_qnty!=0"); //and sensitivity=1

		if($show_comments==1){$show_colspan="0";}else{$show_colspan="2";}
		foreach($nameArray_item as $result_item)
        {
        ?>

        <table border="1" align="left" class="rpt_table"  cellpadding="0" width="90%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="<? echo count($nameArray_color)+8-$show_colspan; ?>" align="">
                <strong title="<? echo $fabric_description_array[$result_item[csf('description')]];?>">As Per Garments Color |&nbsp; <? echo $fabric_description_array[$result_item[csf('description')]]; ?></strong>
                </td>
            </tr>
            <tr>

                <td style="border:1px solid black"><strong>Article No</strong> </td>
                <td style="border:1px solid black"><strong>Order No</strong> </td>
                <td style="border:1px solid black"><strong>GMT Color</strong> </td>
                <td style="border:1px solid black" align="center"><strong>Wo Qty (Kg)</strong></td>
                <td style="border:1px solid black" align="center"><strong>Artwork No</strong></td>
				<? if($show_comments==1){?>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
				<?}?>
            </tr>
            <?
			 $total_amount_as_per_gmts_color=0;
            $nameArray_item_description=sql_select( "select  po_break_down_id,fabric_color_id,description,rate,artwork_no,sum(wo_qnty) as cons from wo_booking_dtls  where booking_no=$txt_booking_no  and entry_form_id=229  and process=".$result_item[csf('process')]." and description='".$result_item[csf('description')]."' and sensitivity=1 and status_active=1 and is_deleted=0 and wo_qnty!=0  group by po_break_down_id,fabric_color_id,description,rate,artwork_no ");//and sensitivity=1
			//echo  "select  po_break_down_id,fabric_color_id,description,rate,artwork_no,sum(wo_qnty) as cons from wo_booking_dtls  where booking_no=$txt_booking_no  and process=".$result_item[csf('process')]." and sensitivity=1 and status_active=1 and is_deleted=0 and wo_qnty!=0  group by po_break_down_id,fabric_color_id,description,rate,artwork_no ";
                foreach($nameArray_item_description as $result_itemdescription)
                {

                ?>
            <tr>
                <td align="center" style="border:1px solid black">
                <? echo $article_number_arr[$result_item[csf('po_break_down_id')]]; ?>
                </td>
                <td style="border:1px solid black"><? echo rtrim($po_number[$result_itemdescription[csf('po_break_down_id')]],", "); ?> </td>
                <td style="border:1px solid black"><? echo$color_library[$result_itemdescription[csf('fabric_color_id')]]; ?>  </td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('cons')],2); ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('artwork_no')]; ?></td>
				<? if($show_comments==1){?>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <?
                $amount_as_per_gmts_color = $result_itemdescription[csf('cons')]*$result_itemdescription[csf('rate')];
				echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
				<?}?>
            </tr>
				<?
                }
                ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="<?=6-$show_colspan;?>"><strong> Item Total</strong></td>
				<? if($show_comments==1){?>
                <td style="border:1px solid black;  text-align:right">
                <?
                echo number_format($total_amount_as_per_gmts_color,4);
                $booking_grand_total+=$total_amount_as_per_gmts_color;
                ?>
                </td>
				<?}?>
            </tr>

        </table>
        &nbsp;
        <br/>
        <?
		}

        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no   and entry_form_id=229 and sensitivity=2 and status_active=1 and is_deleted=0 and wo_qnty!=0");
        $nameArray_size=sql_select( "select distinct  item_size  as gmts_sizes from wo_booking_dtls   where booking_no=$txt_booking_no  and entry_form_id=229  and sensitivity=2 and status_active=1 and is_deleted=0 and wo_qnty!=0");
		if(count($nameArray_size)>0)
		{
        ?>

        <table border="0" align="left" cellpadding="0" width="90%" cellspacing="0" >
            <tr>
                <td colspan="<? echo count($nameArray_size)+8-$show_colspan; ?>" align="">
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
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
				<? if($show_comments==1){?>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
				<?}?>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
			$i++;
            $nameArray_item_description=sql_select( "select distinct description,rate,uom from wo_booking_dtls  where booking_no=$txt_booking_no  and entry_form_id=229 and sensitivity=2 and process=".$result_item[csf('process')]." and status_active=1 and is_deleted=0 and wo_qnty!=0");
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
					$nameArray_size_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls   where   booking_no=$txt_booking_no  and entry_form_id=229 and sensitivity=2 and process=". $result_item[csf('process')]." and description='". $result_itemdescription[csf('description')]."' and item_size='".$result_size[csf('gmts_sizes')]."' and status_active=1 and is_deleted=0 and wo_qnty!=0");

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
                <td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$result_itemdescription[csf('uom')]]; ?></td>
				<? if($show_comments==1){?>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <?
                $amount_as_per_gmts_color = $item_desctiption_total*$result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
				<?}?>
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
				 <? if($show_comments==1){?>
                <td style="border:1px solid black; text-align:right"><? //echo number_format(round($color_total),0); ?></td>
                <td style="border:1px solid black; text-align:right">
                <?
                echo number_format($total_amount_as_per_gmts_color,4);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
				<?}?>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="<? echo count($nameArray_size)+7-$show_colspan; ?>"><strong>Total</strong></td>
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

        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no  and entry_form_id=229  and sensitivity=3 and status_active=1 and is_deleted=0 and wo_qnty!=0");
        $nameArray_color=sql_select( "select distinct fabric_color_id as color_number_id from wo_booking_dtls   where booking_no=$txt_booking_no   and entry_form_id=229 and sensitivity=3 and status_active=1 and is_deleted=0 and wo_qnty!=0");
		if(count($nameArray_color)>0)
		{
        ?>
        <table border="0" align="left" cellpadding="0" width="90%" cellspacing="0" >
            <tr>
                <td colspan="<? echo count($nameArray_color)+8-$show_colspan; ?>" align="">
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
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
				 <? if($show_comments==1){?>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
				<?}?>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
				$i++;
            $nameArray_item_description=sql_select( "select distinct description,brand_supplier,rate,uom from wo_booking_dtls  where booking_no=$txt_booking_no  and entry_form_id=229 and sensitivity=3 and process=".$result_item[csf('process')]." and status_active=1 and is_deleted=0 and wo_qnty!=0");
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
                $nameArray_color_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls    where   booking_no=$txt_booking_no  and entry_form_id=229 and sensitivity=3 and process=". $result_item[csf('process')]." and description='". $result_itemdescription[csf('description')]."' and fabric_color_id=".$result_color[csf('color_number_id')]." and wo_qnty!=0");
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
                <td style="border:1px solid black; text-align:center"><? echo $unit_of_measurement[$result_itemdescription[csf('uom')]]; ?></td>
				 <? if($show_comments==1){?>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <?
                $amount_as_per_gmts_color = $item_desctiption_total*  $result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
				<?}?>
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
				 <? if($show_comments==1){?>
                <td style="border:1px solid black; text-align:right"><? //echo number_format(round($color_total),0); ?></td>
                <td style="border:1px solid black; text-align:right">
                <?
                echo number_format($total_amount_as_per_gmts_color,4);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
				<?}?>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="<? echo count($nameArray_color)+7-$show_colspan; ?>"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,4); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
            </tr>
        </table>
        <br/>
        <?
		}
		?>
        <!--==============================================AS PER CONTRAST COLOR END=========================================  -->

        <!--==============================================AS PER GMTS Color & SIZE START=========================================  -->
		<?
        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no  and entry_form_id=229 and sensitivity=4 and status_active=1 and is_deleted=0 and wo_qnty!=0");
        $nameArray_size=sql_select( "select distinct item_size  as gmts_sizes from wo_booking_dtls   where booking_no=$txt_booking_no  and entry_form_id=229 and sensitivity=4 and status_active=1 and is_deleted=0 and wo_qnty!=0");
	    $nameArray_color=sql_select( "select distinct fabric_color_id as color_number_id from wo_booking_dtls   where booking_no=$txt_booking_no  and entry_form_id=229 and sensitivity=4 and status_active=1 and is_deleted=0 and wo_qnty!=0");

		if(count($nameArray_size)>0)
		{
        ?>

        <table border="0" align="left" cellpadding="0" width="90%" cellspacing="0" >
            <tr>
                <td colspan="<? echo count($nameArray_size)+8-$show_colspan; ?>" align="">
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
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
				<? if($show_comments==1){?>
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
            $nameArray_item_description=sql_select( "select distinct description,brand_supplier,rate,uom from wo_booking_dtls  where booking_no=$txt_booking_no  and entry_form_id=229 and sensitivity=4 and process=".$result_item[csf('process')]." and status_active=1 and is_deleted=0 and wo_qnty!=0");
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
                $nameArray_size_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls   where booking_no=$txt_booking_no  and entry_form_id=229 and sensitivity=4 and process=". $result_item[csf('process')]." and  description='". $result_itemdescription[csf('description')]."' and  item_size='".$result_size[csf('gmts_sizes')]."' and fabric_color_id=".$result_color[csf('color_number_id')]." and status_active=1 and is_deleted=0 and wo_qnty!=0");
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
                <td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$result_itemdescription[csf('uom')]]; ?></td>
				<? if($show_comments==1){?>
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
				<? if($show_comments==1){?>
                <td style="border:1px solid black; text-align:right"><? //echo number_format(round($color_total),0); ?></td>
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
                <td align="right" style="border:1px solid black"  colspan="<? echo count($nameArray_size)+8-$show_colspan; ?>"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,4); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
            </tr>
        </table>
        <br/>
        <?
		}
		?>
        <!--==============================================AS PER Color & SIZE  END=========================================  -->


         <!--==============================================NO NENSITIBITY START=========================================  -->
		<?
        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no  and entry_form_id=229 and sensitivity=0 and status_active=1 and is_deleted=0 and wo_qnty!=0");
        //$nameArray_color=sql_select( "select distinct b.color_number_id from wo_trims_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=$txt_booking_no and a.sensitivity=1");
		$nameArray_color= array();
		if(count($nameArray_item)>0)
		{
        ?>
        <table border="0" align="left" class="rpt_table"  cellpadding="0" width="90%" cellspacing="0" >
            <tr>
                <td colspan="<?=7-$show_colspan;?>" align="">
                <strong>No Sensitivity</strong>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Service Type</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong></strong> </td>
                <td align="center" style="border:1px solid black"><strong> Qnty</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
				<? if($show_comments==1){?>
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
            $nameArray_item_description=sql_select( "select distinct description,brand_supplier,rate,uom from wo_booking_dtls  where booking_no=$txt_booking_no  and entry_form_id=229 and sensitivity=0 and process=".$result_item['process']." and status_active=1 and is_deleted=0 and wo_qnty!=0");
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
                $nameArray_color_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls where    booking_no=$txt_booking_no  and entry_form_id=229 and sensitivity=0 and process=". $result_item[csf('process')]." and  description='". $result_itemdescription[csf('description')]."' and status_active=1 and is_deleted=0 and wo_qnty!=0");
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

                <td style="border:1px solid black; text-align:center "><? echo $unit_of_measurement[$result_itemdescription[csf('uom')]]; ?></td>
				<? if($show_comments==1){?>
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
				<? if($show_comments==1){?>
                <td style="border:1px solid black; text-align:right"></td>
                <td style="border:1px solid black; text-align:right">
                <?
                echo number_format($total_amount_as_per_gmts_color,4);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
				<?}?>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="<?=7-$show_colspan;?>"><strong>Total</strong></td>
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
       <table  width="90%" class="rpt_table" style="border:1px solid black;"   border="0" cellpadding="0" cellspacing="0">
       <tr style="border:1px solid black;">
                <th width="70%" style="border:1px solid black; text-align:right">Total Booking Amount</th><td width="30%" style="border:1px solid black; text-align:right"><? echo number_format($booking_grand_total,2);$booking_grand_total=number_format($booking_grand_total,2);?></td>
            </tr>
            <tr style="border:1px solid black;">
                <th width="70%" style="border:1px solid black; text-align:right">Total Booking Amount (in word)</th><td width="30%" style="border:1px solid black;"><? echo number_to_words($booking_grand_total,$mcurrency, $dcurrency);?></td>
            </tr>
       </table>
          &nbsp;
        <table  width="90%" class="rpt_table" style="border:1px solid black;"   border="0" cellpadding="0" cellspacing="0">
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
        $data_array=sql_select("select id, terms from  lib_terms_condition where is_default=1 and page_id=229");// quotation_id='$data'
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

     <br><br>
    <table border="0" cellpadding="0" cellspacing="0"  width="90%" class="rpt_table"  style="border:1px solid black;" >
                <tr> <td style="border:1px solid black;" colspan="9" align="center"><b> Comments</b> </td></tr>
                <tr style="border:1px solid black;" align="center">
                    <th style="border:1px solid black;" width="40">SL</th>
                    <th style="border:1px solid black;" width="200">Job No</th>
                    <th style="border:1px solid black;" width="200">PO No</th>
                    <th style="border:1px solid black;" width="80">Ship Date</th>
                    <th style="border:1px solid black;" width="80">Pre-Cost/Budget Value</th>
                    <th style="border:1px solid black;" width="80">WO Value</th>

                    <th style="border:1px solid black;" width="80">Balance</th>
                    <th style="border:1px solid black;" width="">Comments </th>
                </tr>
       <tbody>
       <?

					$job_no=rtrim($job_no,',');
					$job_nos=implode(",",array_unique(explode(",",$job_no)));
					$condition= new condition();
					if(str_replace("'","",$job_nos) !=''){
					$condition->job_no("in('$job_nos')");
					}
					$condition->init();
					$conversion= new conversion($condition);
					//echo $conversion->getQuery();
					$convAmt=$conversion->getAmountArray_by_orderAndProcess();
					//print_r($convAmt);
					$po_qty_arr=array();$dyeing_data_arr=array();
					$sql_po_qty=sql_select("select b.id as po_id,b.pub_shipment_date,sum(b.po_quantity) as order_quantity,(sum(b.po_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst   and a.is_deleted=0  and a.status_active=1 group by b.id,a.total_set_qnty,b.pub_shipment_date");
					foreach( $sql_po_qty as $row)
					{
						$po_qty_arr[$row[csf("po_id")]]['order_quantity']=$row[csf("order_quantity_set")];
						$po_qty_arr[$row[csf("po_id")]]['pub_shipment_date']=$row[csf("pub_shipment_date")];
					}
					$pre_cost=sql_select("select job_no,sum(amount) AS dyeing_cost from wo_pre_cost_fab_conv_cost_dtls where cons_process=31 and status_active=1 and is_deleted=0 group by job_no");
					foreach($pre_cost as $row)
					{
						$dyeing_data_arr[$row[csf('job_no')]]['dyeing']=$row[csf('dyeing_cost')];
					}

					$i=1; $total_balance_dyeing=0;$tot_dyeing_cost=0;$tot_pre_cost=0;

					$sql_aop=("select b.po_break_down_id as po_id,a.job_no,sum(b.amount) as amount from wo_booking_mst a, wo_booking_dtls b    where a.job_no=b.job_no and a.booking_no=b.booking_no  and a.booking_no=$txt_booking_no  and b.entry_form_id=229 and a.booking_type=3 and a.item_category=12 and  a.status_active=1  and a.is_deleted=0  and b.status_active=1  and b.is_deleted=0 group by b.po_break_down_id,a.job_no  order by b.po_break_down_id");

                    $nameArray=sql_select( $sql_aop );
                    foreach ($nameArray as $selectResult)
                    {
						$costing_per=return_field_value("costing_per", "wo_pre_cost_mst", "job_no='".$selectResult[csf('job_no')]."'");
						//echo $costing_per;
						//echo $selectResult[csf('job_no')];
						if($costing_per==1)
						{
							$costing_per_qty=12;
						}
						else if($costing_per==2)
						{
							$costing_per_qty=1;
						}
						else if($costing_per==3)
						{
							$costing_per_qty=24;
						}
						else if($costing_per==4)
						{
							$costing_per_qty=36;
						}
						else if($costing_per==5)
						{
							$costing_per_qty=48;
						}
						$po_qty=$po_qty_arr[$selectResult[csf('po_id')]]['order_quantity'];
						$pre_cost_dyeing=$pre_cost_dyeing=array_sum($convAmt[$selectResult[csf("po_id")]][31]);;//($dyeing_data_arr[$selectResult[csf('job_no')]]['dyeing']/$costing_per_qty)*$po_qty;
						$wo_dyeing_charge=$selectResult[csf("amount")];
						$ship_date=$po_qty_arr[$selectResult[csf("po_id")]]['pub_shipment_date'];
						if($db_type==0)
						{
						$conversion_date=change_date_format($booking_date, "Y-m-d", "-",1);
						}
						else
						{
						$conversion_date=change_date_format($booking_date, "d-M-y", "-",1);
						}

						//echo $currency_rate;
						if($currency_id==1)
						{
							$currency_rate=set_conversion_rate( 2, $conversion_date );
							$dyeing_charge=$wo_dyeing_charge/$currency_rate;
						}
						else
						{
							$dyeing_charge=$wo_dyeing_charge;
						}
	   ?>
                    <tr>
                    <td style="border:1px solid black;" width="40"><? echo $i;?></td>
                    <td style="border:1px solid black;" width="200">
					<? echo $selectResult[csf('job_no')];?>
                    </td>
                    <td style="border:1px solid black;" width="200">
					<? echo $po_number[$selectResult[csf('po_id')]];?>
                    </td>
                    <td style="border:1px solid black;" width="80" align="right">
					<? echo change_date_format($ship_date);?>

                    </td>
                     <td style="border:1px solid black;" width="80" align="right">
                     <? echo number_format($pre_cost_dyeing,2); ?>
                    </td>
                     <td style="border:1px solid black;" width="80" align="right">
                    <? echo number_format($dyeing_charge,2); ?>
                    </td>

                    <td style="border:1px solid black;" width="80" align="right">
                       <? $tot_balance=$pre_cost_dyeing-$dyeing_charge; echo number_format($tot_balance,2); ?>
                    </td>
                    <td style="border:1px solid black;" width="">
                    <?
					if( $pre_cost_dyeing>$dyeing_charge)
						{
						echo "Less Booking";
						}
					else if ($pre_cost_dyeing<$dyeing_charge)
						{
						echo "Over Booking";
						}
					else if ($pre_cost_dyeing==$dyeing_charge)
						{
							echo "As Per";
						}
					else
						{
						echo "";
						}
						?>
                    </td>
                    </tr>
	   <?
	  	 $tot_pre_cost+=$pre_cost_dyeing;
	  	 $tot_dyeing_cost+=$dyeing_charge;
		 $total_balance_dyeing+=$tot_balance;
	   $i++;
					}
       ?>
	</tbody>
        <tfoot>
            <tr>
                <td style="border:1px solid black;" colspan="4" align="right">  <b>Total</b></td>
                <td style="border:1px solid black;" align="right"> <b><? echo number_format($tot_pre_cost,2); ?></b></td>
                <td style="border:1px solid black;"  align="right"><b> <? echo number_format($tot_dyeing_cost,2); ?> </b></td>
                <td style="border:1px solid black;"  align="right"><b> <? echo number_format($total_balance_dyeing,2); ?></b> </td>
                <td style="border:1px solid black;">&nbsp;  </td>
             </tr>
        </tfoot>
    </table>

         <br/>

		 <?
            echo signature_table(290, $cbo_company_name, "1150px","",40,$inserted_by);
         ?>
    </div>
<?
}
if($action=="show_trim_booking_report2")
{  

	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	//$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library",'master_tble_id','image_location');
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");

	$path=($path=='')?'../../':$path;
	?>
	<div style="width:1150px" align="left">
       <table width="100%" cellpadding="0" cellspacing="0" style="border:0px solid black;margin:5px; font-size:16px; font-family:'Arial Narrow';" >
           <tr>
               <td width="100">
               <img  src='<? echo $path.$imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
               </td>
               <td width="">
                    <table width="100%" cellpadding="0" cellspacing="0" >
                        <tr>
                            <td align="center" style="font-size:20px;">
                              <?php	echo $company_library[$cbo_company_name];	?>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:14px;">
                            <?
                            $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
                            foreach ($nameArray as $result)
                            {
                            ?>
                                             <? echo $result[csf('plot_no')]; ?>
                                            <? echo $result[csf('level_no')]?>
                                            <? echo $result[csf('road_no')]; ?>
                                            <? echo $result[csf('block_no')];?>
                                            <? echo $result[csf('city')];?>
                                            <? echo $result[csf('zip_code')]; ?>
                                            <?php echo $result[csf('province')]; ?>
                                            <? echo $country_arr[$result[csf('country_id')]]; ?><br>
                                            Email Address: <? echo $result[csf('email')];?>
                                            Website No: <? echo $result[csf('website')];

									$email=$result[csf('email')];
									$city=$result[csf('city')];
									$road_no=$result[csf('road_no')];
									$block_no=$result[csf('block_no')];
                            }
                            ?>
                               </td>
                            </tr>
                            <tr>
                            <td align="center" style="font-size:20px">
                            <strong>Service Booking Sheet For Dyeing</strong>
                             </td>
                            </tr>
                      </table>
                </td>
                <td width="250" id="barcode_img_id">

               </td>
            </tr>
       </table>
		<?

		$booking_grand_total=0;
		$job_no="";
		$po_no="";$pre_cost_fabric_dtls_id="";
		$nameArray_job=sql_select( "SELECT  b.id as po_id,b.po_number,a.slength,a.mc_dia,a.mc_gauge,a.artwork_no,a.dia_width as fin_dia,a.fabric_color_id,a.fin_gsm,a.option_shade,a.yarn_count,a.lot_no,a.labdip_no,a.brand,a.gmts_color_id,a.fabric_color_id,a.job_no,a.description,a.program_no,a.lib_composition,a.delivery_date,a.delivery_end_date,a.pre_cost_fabric_cost_dtls_id as fab_dtls_id,a.sensitivity,a.rate,a.uom,a.amount,a.wo_qnty,b.job_no_mst,c.job_no,c.style_ref_no,a.sub_process_id from wo_booking_dtls a, wo_po_break_down b, wo_po_details_master c where a.po_break_down_id=b.id and a.booking_no=$txt_booking_no and c.job_no=b.job_no_mst and a.entry_form_id=229 and a.wo_qnty>0 and a.status_active=1");
        foreach ($nameArray_job as $row)
        {
			$po_no.=$row[csf('po_number')].",";
			$job_no.=$row[csf('job_no')].",";
			$description=$row[csf('description')];
			$pre_cost_fabric_dtls_id.=$row[csf('fab_dtls_id')].",";
			$sensitivity_arr[$row[csf('sensitivity')]]=$row[csf('sensitivity')];
			$sensitivity_prog_arr[$row[csf('sensitivity')]][$row[csf('po_id')]][$description][$row[csf('gmts_color_id')]]['po_number']=$row[csf('po_number')];

			$sensitivity_prog_arr[$row[csf('sensitivity')]][$row[csf('po_id')]][$description][$row[csf('gmts_color_id')]]['fabric_color_id']=$row[csf('fabric_color_id')];
			$sensitivity_prog_arr[$row[csf('sensitivity')]][$row[csf('po_id')]][$description][$row[csf('gmts_color_id')]]['sub_process_id']=$row[csf('sub_process_id')];
			$sensitivity_prog_arr[$row[csf('sensitivity')]][$row[csf('po_id')]][$description][$row[csf('gmts_color_id')]]['lib_composition']=$row[csf('lib_composition')];
			$sensitivity_prog_arr[$row[csf('sensitivity')]][$row[csf('po_id')]][$description][$row[csf('gmts_color_id')]]['rate']=$row[csf('rate')];
			$sensitivity_prog_arr[$row[csf('sensitivity')]][$row[csf('po_id')]][$description][$row[csf('gmts_color_id')]]['wo_qnty']+=$row[csf('wo_qnty')];
			$sensitivity_prog_arr[$row[csf('sensitivity')]][$row[csf('po_id')]][$description][$row[csf('gmts_color_id')]]['amount']+=$row[csf('amount')];
			$sensitivity_prog_arr[$row[csf('sensitivity')]][$row[csf('po_id')]][$description][$row[csf('gmts_color_id')]]['remarks']=$row[csf('remarks')];
			$sensitivity_prog_arr[$row[csf('sensitivity')]][$row[csf('po_id')]][$description][$row[csf('gmts_color_id')]]['style_id']=$row[csf('style_ref_no')];
			$sensitivity_prog_arr[$row[csf('sensitivity')]][$row[csf('po_id')]][$description][$row[csf('gmts_color_id')]]['uom']=$row[csf('uom')];
			$sensitivity_prog_arr[$row[csf('sensitivity')]][$row[csf('po_id')]][$description][$row[csf('gmts_color_id')]]['program_no']=$row[csf('program_no')];

			if($row[csf('mc_dia')]!="" || $row[csf('mc_gauge')]!="" || $row[csf('artwork_no')]!="" || $row[csf('fin_dia')]!=""  || $row[csf('fin_gsm')]!="")
			{
			$sensitivity_prog_arr[$row[csf('sensitivity')]][$row[csf('po_id')]][$description][$row[csf('gmts_color_id')]]['mc_dia'].=$row[csf('mc_dia')].',';
			$sensitivity_prog_arr[$row[csf('sensitivity')]][$row[csf('po_id')]][$description][$row[csf('gmts_color_id')]]['mc_gauge'].=$row[csf('mc_gauge')].',';
			$sensitivity_prog_arr[$row[csf('sensitivity')]][$row[csf('po_id')]][$description][$row[csf('gmts_color_id')]]['artwork_no'].=$row[csf('artwork_no')].',';
			$sensitivity_prog_arr[$row[csf('sensitivity')]][$row[csf('po_id')]][$description][$row[csf('gmts_color_id')]]['fin_dia'].=$row[csf('fin_dia')].',';
			$sensitivity_prog_arr[$row[csf('sensitivity')]][$row[csf('po_id')]][$description][$row[csf('gmts_color_id')]]['fin_gsm'].=$row[csf('fin_gsm')].',';

			}
			if($row[csf('yarn_count')]!="" )
			{
				$sensitivity_prog_arr2[$row[csf('sensitivity')]][$row[csf('po_id')]][$description]['yarn_count'].=$row[csf('yarn_count')].',';
			}
			if($row[csf('option_shade')]!="" )
			{
				$sensitivity_prog_arr[$row[csf('sensitivity')]][$row[csf('po_id')]][$description][$row[csf('gmts_color_id')]]['option_shade']=$row[csf('option_shade')];
			}
			if($row[csf('labdip_no')]!="" )
			{
				$sensitivity_prog_arr[$row[csf('sensitivity')]][$row[csf('po_id')]][$description][$row[csf('gmts_color_id')]]['labdip_no']=$row[csf('labdip_no')];
			}
			// if($row[csf('lot_no')]!="" )
			// {
			// 	$sensitivity_prog_arr2[$row[csf('sensitivity')]][$row[csf('po_id')]][$description][$row[csf('gmts_color_id')]]['lot_no'].=$row[csf('lot_no')].',';
			// }
			if($row[csf('slength')]!="" )
			{
				$sensitivity_prog_arr[$row[csf('sensitivity')]][$row[csf('po_id')]][$description][$row[csf('gmts_color_id')]]['slength'].=$row[csf('slength')].',';
			}
			$sensitivity_prog_arr2[$row[csf('sensitivity')]][$row[csf('po_id')]][$description]['lot_no'].=$row[csf('lot_no')].',';
			$sensitivity_prog_arr2[$row[csf('sensitivity')]][$row[csf('po_id')]][$description]['brand'].=$row[csf('brand')].',';
			$sensitivity_prog_arr[$row[csf('sensitivity')]][$row[csf('po_id')]][$description][$row[csf('gmts_color_id')]]['delivery_date'].=$row[csf('delivery_date')].',';
			$sensitivity_prog_arr[$row[csf('sensitivity')]][$row[csf('po_id')]][$description][$row[csf('gmts_color_id')]]['delivery_end_date'].=$row[csf('delivery_end_date')].',';
			//$sensitivity_prog_wise_color_arr[$row[csf('sensitivity')]][$description][$row[csf('gmts_color_id')]]['wo_qnty']+=$row[csf('wo_qnty')];

			//$nameArray_color[$result_job[csf('fabric_color_id')]]=$result_job[csf('fabric_color_id')];
		}
		/* echo '<pre>';
		print_r($sensitivity_prog_arr); die; */
		unset($nameArray_job);
		$pre_cost_fabric_dtls_id=rtrim($pre_cost_fabric_dtls_id,',');
		$job_no=rtrim($job_no,',');

        $nameArray=sql_select( "select a.booking_no,a.booking_date,a.is_approved,a.pay_mode,a.remarks,a.buyer_id,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.source,a.inserted_by  from wo_booking_mst a where  a.booking_no=$txt_booking_no  and a.entry_form=229 ");

		
		$inserted_by=$user_lib_name[$nameArray[0][csf("inserted_by")]];
		$currencyId="";
        foreach ($nameArray as $result)
        {
			$varcode_booking_no=$result[csf('booking_no')];
			$currencyId=$result[csf('currency_id')];
			if($result[csf('is_approved')]==1 ){
				$approved_msg= "Fully approved";
			}
			else if( $result[csf('is_approved')]==3){
				$approved_msg= "Partial approved";
			}
			else{
				$approved_msg='';
			}
			$pay_mode=$result[csf('pay_mode')];$supplier_id=$result[csf('supplier_id')];
			if($pay_mode==5 || $pay_mode==3){
						$com_supp=$company_library[$supplier_id];
						$suplier_address=$road_no.', '.$block_no.', '.$city.', '.$email;
					}
					else{
						$com_supp=$supplier_name_arr[$supplier_id];
						$suplier_address=$supplier_address_arr[$supplier_id];
					}

        ?>
		<div style="width:1150px;">
       <table width="100%" style="border:0px solid black;margin:5px;font-size:16px; font-family:'Arial Narrow'; ">
            <tr>
                <td colspan="6" valign="top"></td>
            </tr>
			<tr>
                <td width="100" style="font-size:16px" colspan="6" align="left"><b>To</b></td>

			</tr>
			<tr>
				<td width="100" style="font-size:16px" ><b>Supplier Name</b>   </td>
                <td width="160" colspan="2">:&nbsp;<? echo $com_supp;?> </td>
				 <td width="350" style="font-size:16px">&nbsp;   </td>

				 <td width="120" style="font-size:16px"><b>Work order No</b>   </td>
                <td width="120" colspan="2">:&nbsp;<? echo $result[csf('booking_no')];?> </td>

				
                <td width="120" style="font-size:23px; color:#F00"  colspan="2">&nbsp;<? echo $approved_msg;?></td>
			</tr>
			<tr>
				<td width="100" valign="top" style="font-size:16px" ><b>Address</b>   </td>
                <td width="160" colspan="2"   valign="top">:&nbsp;<? echo $suplier_address;?> </td>
				 <td width="350" style="font-size:16px">&nbsp; </td>
				 <td width="120" style="font-size:16px"><b>Booking Date</b>   </td>
                <td width="120" colspan="2">:&nbsp;<? echo change_date_format($result[csf('booking_date')]);?> </td>
			</tr>
			<tr>
				<td width="100"   style="font-size:16px" ><b>Attention</b>   </td>
                <td width="160" colspan="2"  >:&nbsp;<? echo $result[csf('attention')];?> </td>
				 <td width="350" style="font-size:16px">&nbsp; </td>
				 <td width="120" style="font-size:16px"><b>Delivery  Date</b>   </td>
                <td width="120" colspan="2">:&nbsp;<? echo change_date_format($result[csf('delivery_date')]);?> </td>
			</tr>
			<tr>
				<td width="100"   style="font-size:16px" ><b>Currency</b>   </td>
                <td width="160" colspan="2"  >:&nbsp;<? echo $currency[$result[csf('currency_id')]];?> </td>
				 <td width="350" style="font-size:16px">&nbsp; </td>
				 <td width="120" style="font-size:16px"><b>Buyer</b>   </td>
                <td width="120" colspan="2">:&nbsp;<? echo $buyer_name_arr[$result[csf('buyer_id')]];?> </td>
			</tr>
			<tr>
				<td width="100"   style="font-size:16px" ><b>Job </b>   </td>
                <td width="160" colspan="2"  >:&nbsp;<? echo $txt_job_no=implode(",",array_unique(explode(",",$job_no)));?> </td>
				<td width="350" style="font-size:16px">&nbsp; </td>
				<td width="100"   style="font-size:16px" ><b>Style</b>   </td>
                <td width="160" colspan="2"  >:&nbsp;<? echo $row[csf('style_ref_no')];?> </td>


			</tr>
			<tr>


				 <td width="550" style="font-size:16px" colspan="6"><b>Remark : <? echo $result[csf('remarks')];?></b>   </td>

			</tr>
        </table>
		<?
        }

        ?>
          <!--==============================================AS PER GMTS COLOR START=========================================  -->
		<?
		//========================================
		$fabric_description_array=array();
		$pre_cost_fabric_dtls_id=implode(",",array_unique(explode(",",$pre_cost_fabric_dtls_id)));
	$wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select id,fabric_description,cons_process from wo_pre_cost_fab_conv_cost_dtls  where id in($pre_cost_fabric_dtls_id)");
	foreach( $wo_pre_cost_fab_conv_cost_dtls_id as $row_wo_pre_cost_fab_conv_cost_dtls_id)
	{
		if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]!=0)
		{
			$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description from  wo_pre_cost_fabric_cost_dtls  where  id='".$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]."'");
			list($fabric_description_row)=$fabric_description;
			$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]=$body_part[$fabric_description_row[csf("body_part_id")]].', '.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")];
		}
		if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]==0)
		{
			//echo "select id,body_part_id,color_type_id,fabric_description from  wo_pre_cost_fabric_cost_dtls  where  job_no='$data'";
			$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description from  wo_pre_cost_fabric_cost_dtls  where  id in($pre_cost_fabric_dtls_id)");
			//list($fabric_description_row)=$fabric_description;
			foreach( $fabric_description as $fabric_description_row)
	        {
			$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]].=$body_part[$fabric_description_row[csf("body_part_id")]].', '.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")].", ";

			//$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]="All Fabrics  ".$conversion_cost_head_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("cons_process")]];
			}
		}

	}
	unset($wo_pre_cost_fab_conv_cost_dtls_id);

	//print_r($fabric_description_array); //listagg(program_no ,',') within group (order by program_no) AS program_no
	//=================================================
	$color_rowspan_arr=array();$po_rowspan_arr=array();$sensity_rowspan_arr=array();
	foreach($sensitivity_prog_arr as $sensitivity_id=>$sensitivity_data)
	{
		  $sensity_rowspan=0;
		foreach($sensitivity_data as $po_id=>$po_data)
        {
		    $po_rowspan=0;
			foreach($po_data as $desc_id=>$desc_data)
            {
				$color_rowspan=0;
				foreach($desc_data as $color_id=>$row)
           		 {
				 	$color_rowspan++;
					$po_rowspan++;
					$sensity_rowspan++;
				 }
				 $sensity_rowspan_arr[$sensitivity_id]=$sensity_rowspan;
				 $po_rowspan_arr[$sensitivity_id][$po_id]=$po_rowspan;
				 $color_rowspan_arr[$sensitivity_id][$po_id][$desc_id]=$color_rowspan;
			}
		}
	}
	//print_r($po_rowspan_arr);

	$grand_total_wo_qty=$grand_total_wo_amount=$total_wo_qty=$total_wo_amount=0;
	foreach($sensitivity_prog_arr as $sensitivity_id=>$sensitivity_data)
	{
		if(count($sensitivity_prog_arr)>0)
		{
		if($sensitivity_id==1) $sensitivity_txt="As Per Garments Color";else $sensitivity_txt="As Per Contrast Color";
        ?>
        <table border="1" align="left" class="rpt_table" style="margin:5px;font-size:15px;font-family:'Arial Narrow';"  cellpadding="0" width="100%"  cellspacing="0" >
            <caption> <strong><? echo $sensitivity_txt;?></strong></caption>
            <tr>
                <th><strong>Sl</strong> </th>
                <th><strong>Po No</strong> </th>
                <th><strong>Program No</strong> </th>
                <th><strong>Fabric Description</strong> </th>
				<th><strong>Sub Process</strong> </th>
				<th  align="center"><strong>Y.Count</strong></th>
				<th  align="center"><strong>Lot No</strong></th>
				<th  align="center"><strong>Brand</strong></th>
				<th  align="center"><strong>Labdip No</strong></th>
				<th align="center"><strong>Option</strong></th>
				<th  align="center"><strong>Gmts Color</strong></th>
				<th  align="center"><strong>Fabric Color</strong></th>
				<th   align="center"><strong> &nbsp;M/C Dia X GG  &nbsp;</strong></th>
				<th  align="center"><strong>Fin Dia</strong></th>
				<th  align="center"><strong>Fin GSM </strong></th>
				<th  align="center"><strong>S/L</strong></th>
				<th   align="center"><strong>Delivery Start Date</strong></th>
				<th align="center"><strong>Delivery End Date</strong></th>
                <th  align="center"><strong>UOM</strong></th>
				<th  align="center"><strong>Wo .Qnty</strong></th>
				<? if ($show_comments!=0) { ?>
                <th  align="center"><strong>Rate</strong></th>
                <th   align="center"><strong>Amount</strong></th>
				<? }?>

            </tr>
			<tbody>
            <?
			$i=1;
            $total_amount_as_per_gmts_color=0;  $sub_total_wo_qty=$sub_total_wo_amount=0;
			$grand_sensitivity_total_wo_qty=array();
           foreach($sensitivity_data as $po_id=>$po_data)
           {
		    	$x=1; 
				
			foreach($po_data as $desc_id=>$desc_data)
            {
				$y=1; $processId=array();
				foreach($desc_data as $color_id=>$row)
           		 {

				$row[('mc_dia')]=rtrim($row[('mc_dia')],',');
				$row[('mc_gauge')]=rtrim($row[('mc_gauge')],',');
				$row[('artwork_no')]=rtrim($row[('artwork_no')],',');
				$row[('fin_dia')]=rtrim($row[('fin_dia')],',');
				$row[('fin_gsm')]=rtrim($row[('fin_gsm')],',');
				$row[('slength')]=rtrim($row[('slength')],',');
				$row[('delivery_date')]=rtrim($row[('delivery_date')],',');
				$row[('delivery_end_date')]=rtrim($row[('delivery_end_date')],',');

				$yarn_count=$sensitivity_prog_arr2[$sensitivity_id][$po_id][$desc_id]['yarn_count'];
				$lot_no=$sensitivity_prog_arr2[$sensitivity_id][$po_id][$desc_id]['lot_no'];
				$brand=$sensitivity_prog_arr2[$sensitivity_id][$po_id][$desc_id]['brand'];

				$yarn_count=rtrim($yarn_count,',');
				$lot_no=rtrim($lot_no,',');
				$brand=rtrim($brand,',');
				$labdip_no=rtrim($labdip_no,',');
				$option_shade=rtrim($option_shade,',');
				$sub_process_arr=array();
				if($row[('sub_process_id')]!=''){
					$sub_process_arr=explode(",",$row[('sub_process_id')]);
				}				
				if(count($sub_process_arr)>0){
					foreach($sub_process_arr as $sub_processId){
						$processId[$sub_processId]=$conversion_cost_head_array[$sub_processId];
					}
				}
				
				$yarn_count=implode(",",array_unique(explode(",",$yarn_count)));
				$lot_no=implode(",",array_unique(explode(",",$lot_no)));
				$brand=implode(",",array_unique(explode(",",$brand)));
				$labdip_no=implode(",",array_unique(explode(",",$labdip_no)));
				$option_shade=implode(",",array_unique(explode(",",$option_shade)));


				$color_rowspan=$color_rowspan_arr[$sensitivity_id][$po_id][$desc_id];
            ?>
            <tr>
                <?
				if($x==1)
				{
				?>
				<td style="" rowspan="<? echo $po_rowspan_arr[$sensitivity_id][$po_id]; ?>">  <? echo $i; ?> </td>
                <td align="center" style="" rowspan="<? echo $po_rowspan_arr[$sensitivity_id][$po_id]; ?>" > <p><? echo $row[('po_number')]; ?> </p> </td>
                <?
				}
				if($y==1)
				{

                ?>

                <td  rowspan="<? echo $color_rowspan; ?>"><? echo $row[('program_no')]; ?></td>
				 <td  rowspan="<? echo $color_rowspan; ?>"><? echo rtrim($fabric_description_array[$desc_id],", "); ?> </td>
				 <td  rowspan="<? echo $color_rowspan; ?>"><? echo implode(",", $processId); ?> </td>


                <td style="text-align:center" rowspan="<? echo $color_rowspan; ?>"><?  echo $yarn_count; ?>   </td>
				<td style="text-align:center" rowspan="<? echo $color_rowspan; ?>"><? echo $lot_no; ?></td>
				<td style="text-align:center" rowspan="<? echo $color_rowspan; ?>"><? echo $brand; ?></td>

					<?
				}
					?>
					<td style="text-align:center"><? echo $row[('labdip_no')]; ?></td>
					<td style="text-align:center"><? echo $row[('option_shade')]; ?></td>
					<td style="text-align:center"><? echo $color_library[$color_id]; ?></td>
					<td style="text-align:center"><? echo $color_library[$row[('fabric_color_id')]]; ?></td>

					<td style="text-align:center"><?
					if($row[('mc_dia')]!='' && $row[('mc_gauge')]!='') $mc_dia_gg=$row[('mc_dia')].'X'.$row[('mc_gauge')];
					else if($row[('mc_dia')]!='' && $row[('mc_gauge')]=='') $mc_dia_gg=$row[('mc_dia')];
					else if($row[('mc_dia')]=='' && $row[('mc_gauge')]!='') $mc_dia_gg=$row[('mc_gauge')];
					else $mc_dia_gg=""; echo $mc_dia_gg; ?></td>
					<td style="text-align:center"><? echo $row[('fin_dia')]; ?></td>
					<td style="text-align:center"><? echo $row[('fin_gsm')]; ?></td>
					<td style="text-align:center"><? echo $row[('slength')]; ?></td>

					<td style="text-align:center"><? echo change_date_format($row[('delivery_date')]); ?></td>
					<td style="text-align:center"><? echo change_date_format($row[('delivery_end_date')]); ?></td>
					<td style="text-align:right"><? echo $unit_of_measurement[$row[('uom')]]; ?></td>
					<td style="text-align:right"><? echo number_format($row[('wo_qnty')],2); ?></td>
					<? if ($show_comments!=0) { ?>
					<td style="text-align:right"><? echo number_format(($row[('amount')]/$row[('wo_qnty')]),2); ?></td>
					<td style="text-align:right"><? echo number_format($row[('amount')],2); ?></td>
					<?  }  ?>

            </tr>

            <?
				$x++;$y++;
				$sub_total_wo_qty+=$row[('wo_qnty')];$sub_total_wo_amount+=$row[('amount')];
				$grand_sensitivity_total_wo_qty[$sensitivity_id]+=$row[('wo_qnty')];
				$grand_sensitivity_total_wo_amount[$sensitivity_id]+=$row[('amount')];
				$total_wo_qty+=$row[('wo_qnty')];
				$total_wo_amount+=$row[('amount')];
				 } //Color End
          	 } //Prog End
			 	?>

				<tr  style="">
                <td align="right" style="" colspan="19"><strong>Sub Total</strong></td>
                <td align="right" style=""><strong><? echo number_format($sub_total_wo_qty,4);unset($sub_total_wo_qty); ?></strong></td>
				<? if ($show_comments!=0) { ?>
				<td style="" align="right"> </td>
				<td  style=" text-align:right"><?  echo number_format($sub_total_wo_amount,4);unset($sub_total_wo_amount); ?></td>
				<?  }  ?>
               </tr>


				<?
				$i++;
		   } //Desc End
            ?>
		</tbody>
           <tr style="">
                <td colspan="19" style="border:1px solid black; text-align:right"><strong> Total </strong></td>
				<td style="text-align:right"><? echo number_format($total_wo_qty,2);$total_wo_qty=0;?></td>
				<? if ($show_comments!=0) { ?>
				<td style="text-align:right" ></td>
				<td style="text-align:right" ><? echo number_format($total_wo_amount,2);$total_wo_amount=0;?></td>
				<? }  ?>

      		 </tr>

       		</table>
       		 <br/>
        <?
			}
			?>

      	  <br/>
			<?
			 //$grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
			 $grand_total_wo_qty+=$grand_sensitivity_total_wo_qty[$sensitivity_id];
			 $grand_total_wo_amount+=$grand_sensitivity_total_wo_amount[$sensitivity_id];
		} //Sensitivity End
		?>
        <!--==============================================AS PER  COLOR END=========================================  -->

	 <br/>
     <table border="1" align="left" class="rpt_table" style="margin:5px;font-family:'Arial Narrow'; font-size:15px;"  cellpadding="0" width="100%" cellspacing="0" >
      <tr style="">
                <td colspan="19" width="86%" style="border:1px solid black; text-align:right"><strong> Grand Total </strong></td>
				<td style="text-align:right" width="" ><? echo number_format($grand_total_wo_qty,2);?></td>
				<td style="text-align:right" width="" ></td>
				<? if ($show_comments!=0) { ?>
				<td style="text-align:right" width="" ><? echo number_format($grand_total_wo_amount,2);
				$booking_grand_total=$grand_total_wo_amount;
				?></td>
				<? }  ?>

      </tr>
	</table>

	&nbsp;
<table  width="90%" class="rpt_table" style="border:1px solid black;"   border="0" cellpadding="0" cellspacing="0">
<tr style="border:1px solid black;">
         <th width="70%" style="border:1px solid black; text-align:right">Total Booking Amount</th><td width="30%" style="border:1px solid black; text-align:right"><? echo number_format($booking_grand_total,2);?></td>
     </tr>
     <tr style="border:1px solid black;">
         <th width="70%" style="border:1px solid black; text-align:right">Total Booking Amount (in word)</th><td width="30%" style="border:1px solid black;">
         <?
         $mcurrency="";
         $dcurrency="";
         $currency_id=$currencyId;
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
         $currency_name=$currency[$currencyId];
         echo number_to_words($booking_grand_total,$mcurrency, $dcurrency);
         ?></td>
     </tr>
</table>
   &nbsp;
	</br>
	</br>


<?
$lib_designation_arr = return_library_array(" select id,custom_designation from lib_designation", "id", "custom_designation");
$user_lib_designation_arr = return_library_array("SELECT id,designation from user_passwd", "id", "designation");
$user_lib_name_arr = return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");

$mst_id = return_field_value("id as mst_id", "wo_booking_mst", "booking_no=$txt_booking_no", "mst_id");
//echo $mst_id.'ssD';
//and b.un_approved_date is null
$approve_data_array = sql_select("select b.approved_by,min(b.approved_date) as approved_date from   approval_history b where b.mst_id=$mst_id and b.entry_form=28  GROUP BY b.approved_by,b.mst_id,b.sequence_no order by b.sequence_no asc");



$unapprove_data_array = sql_select("select b.approved_by,b.approved_date,b.un_approved_reason,b.un_approved_date,b.approved_no from   approval_history b where b.mst_id=$mst_id and b.entry_form=65  order by b.approved_date,b.approved_by");

// echo "select b.approved_by,b.approved_date,b.un_approved_reason,b.un_approved_date,b.approved_no from   approval_history b where b.mst_id=$mst_id and b.entry_form=65  order by b.approved_date,b.approved_by";die;


?>
<?
if (count($unapprove_data_array) > 0) {
	//echo 111;
	$sql_unapproved = sql_select("select booking_id,approval_cause from fabric_booking_approval_cause where  entry_form=65 and approval_type=2 and is_deleted=0 and status_active=1 and booking_id=$mst_id");
	
	$unapproved_request_arr = array();
	foreach ($sql_unapproved as $rowu) {
		$unapproved_request_arr[$rowu[csf('booking_id')]] = $rowu[csf('approval_cause')];
	}
?>
	<table width="850" class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all">
		<thead>
			<tr style="border:1px solid black;">
				<th colspan="6" style="border:1px solid black;">Approval/Un Approval History</th>
			</tr>
			<tr style="border:1px solid black;">
				<th width="3%" style="border:1px solid black;">Sl</th>
				<th width="30%" style="border:1px solid black;">Name</th>
				<th width="20%" style="border:1px solid black;">Designation</th>
				<th width="5%" style="border:1px solid black;">Approval Status</th>
				<th width="20%" style="border:1px solid black;">Reason For Un Approval</th>
				<th width="22%" style="border:1px solid black;"> Date</th>

			</tr>
		</thead>
		<tbody>
			<?
			$i = 1;
			foreach ($unapprove_data_array as $row) {

			?>
				<tr style="border:1px solid black;">
					<td width="3%" style="border:1px solid black;"><? echo $i; ?></td>
					<td width="30%" style="border:1px solid black;text-align:center"><? echo $user_lib_name_arr[$row[csf('approved_by')]]; ?></td>
					<td width="20%" style="border:1px solid black;text-align:center"><? echo $lib_designation_arr[$user_lib_designation_arr[$row[csf('approved_by')]]]; ?></td>
					<td width="5%" style="border:1px solid black; text-align:center"><? echo 'Yes'; ?></td>
					<td width="20%" style="border:1px solid black;"><? echo ''; ?></td>
					<td width="22%" style="border:1px solid black;text-align:center"><? if ($row[csf('approved_date')] != "") echo date("d-m-Y h:i:s", strtotime($row[csf('approved_date')]));
																						else echo ""; ?></td>
				</tr>
				<?
				$i++;
				$un_approved_date = explode(" ", $row[csf('un_approved_date')]);
				$un_approved_date = $un_approved_date[0];
				if ($db_type == 0) //Mysql
				{
					if ($un_approved_date == "" || $un_approved_date == "0000-00-00") $un_approved_date = "";
					else $un_approved_date = $un_approved_date;
				} else {
					if ($un_approved_date == "") $un_approved_date = "";
					else $un_approved_date = $un_approved_date;
				}

				if ($un_approved_date != "") {
				?>
					<tr style="border:1px solid black;">
						<td width="3%" style="border:1px solid black;"><? echo $i; ?></td>
						<td width="30%" style="border:1px solid black;text-align:center"><? echo $user_lib_name_arr[$row[csf('approved_by')]]; ?></td>
						<td width="20%" style="border:1px solid black;text-align:center"><? echo $lib_designation_arr[$user_lib_designation_arr[$row[csf('approved_by')]]]; ?></td>
						<td width="5%" style="border:1px solid black;text-align:center;"><? echo 'No'; ?></td>
						<td width="20%" style="border:1px solid black;text-align:center"><? echo $unapproved_request_arr[$mst_id]; ?></td>
						<td width="22%" style="border:1px solid black;text-align:center"><? if ($row[csf('un_approved_date')] != "") echo date("d-m-Y h:i:s", strtotime($row[csf('un_approved_date')]));
																							else echo ""; ?></td>
					</tr>

			<?
					$i++;
				}
			}
			?>
		</tbody>
	</table>
<?
}

?>
<br />

      <br/>
        <table  width="100%" class="rpt_table" style="margin:5px;font-size:16px;font-family:'Arial Narrow';"   border="1" cellpadding="0" cellspacing="0">
          <tr>
          <td>
           <?
		  	 echo get_spacial_instruction($txt_booking_no);
		  ?>
          </td>
          </tr>
           </table>
         <br/>
		 <?
            echo signature_table(290, $cbo_company_name, "1113px","",40,$inserted_by);
			//echo "****".custom_file_name($txt_booking_no,$style_sting,$txt_job_no);
         ?>
    </div>
	<script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <script>
    fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');
    </script>

<?
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

		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0";  disconnect($con);die;}
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
	 $sql= "select id,booking_no,booking_date,is_short,company_id,buyer_id,item_category,fabric_source,currency_id,exchange_rate,remarks,pay_mode,booking_month,supplier_id,attention,tenor,delivery_date,source,booking_year,delivery_to,ready_to_approved from wo_booking_mst  where booking_no='$data' and  entry_form=229";
	 $data_array=sql_select($sql);
	 foreach ($data_array as $row)
	 {
		$booking_id=$row[csf("id")];
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";
		$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$row[csf("company_id")]."' and module_id=2 and report_id=195 and is_deleted=0 and status_active=1");
		echo "print_report_button_setting('$print_report_format');\n";
		
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_id")]."';\n";
		echo "load_drop_down( 'requires/service_booking_multi_job_wise_dyeing_controller', '".$row[csf("pay_mode")]."', 'load_drop_down_supplier', 'supplier_td' );\n";

		echo "document.getElementById('txt_booking_no').value = '".$row[csf("booking_no")]."';\n";
		echo "document.getElementById('cbo_process').value = '31';\n";
		//echo "document.getElementById('cbo_fabric_source').value = '".$row[csf("fabric_source")]."';\n";
		echo "document.getElementById('cbo_currency').value = '".$row[csf("currency_id")]."';\n";
		echo "document.getElementById('txt_exchange_rate').value = '".$row[csf("exchange_rate")]."';\n";
		echo "document.getElementById('cbo_pay_mode').value = '".$row[csf("pay_mode")]."';\n";
		echo "document.getElementById('txt_booking_date').value = '".change_date_format($row[csf("booking_date")],'dd-mm-yyyy','-')."';\n";
		echo "document.getElementById('cbo_booking_month').value = '".$row[csf("booking_month")]."';\n";
		echo "document.getElementById('cbo_supplier_name').value = '".$row[csf("supplier_id")]."';\n";
		echo "document.getElementById('txt_attention').value = '".$row[csf("attention")]."';\n";
		echo "document.getElementById('cbo_short_type').value = '".$row[csf("is_short")]."';\n";
		echo "document.getElementById('txt_tenor').value = '".$row[csf("tenor")]."';\n";
		echo "document.getElementById('txt_remark').value = '".$row[csf("remarks")]."';\n";
		echo "document.getElementById('txt_delivery_to').value = '".$row[csf("delivery_to")]."';\n";
		echo "document.getElementById('update_id').value = '".$row[csf("id")]."';\n";
		echo "document.getElementById('txt_delivery_date').value = '".change_date_format($row[csf("delivery_date")],'dd-mm-yyyy','-')."';\n";
	    echo "document.getElementById('cbo_source').value = '".$row[csf("source")]."';\n";
		echo "document.getElementById('cbo_booking_year').value = '".$row[csf("booking_year")]."';\n";
		echo "document.getElementById('cbo_ready_to_approved').value = '".$row[csf("ready_to_approved")]."';\n";

		//echo "load_drop_down( 'requires/service_booking_multi_job_wise_dyeing_controller', '".$row[csf("job_no")]."_".$row[csf("booking_no")]."', 'load_drop_down_fabric_description', 'fabric_description_td' )\n";
		$rate_from_library=0;
		$rate_from_library=return_field_value("is_serveice_rate_lib", "variable_settings_production", "service_process_id=3 and company_name=".$row[csf("company_id")]." and status_active=1 and is_deleted=0 ");
		echo "document.getElementById('service_rate_from').value = '".$rate_from_library."';\n";

		$is_editable=return_field_value("editable", "variable_order_tracking ", "company_name=".$row[csf("company_id")]." and variable_list=91");
		echo "document.getElementById('txt_amount_vali_id').value = '".$is_editable."';\n";
		//echo "load_drop_down( 'requires/service_booking_multi_job_wise_dyeing_controller', '".$row[csf("job_no")]."', 'load_drop_down_process', 'process_td' )\n";		 
	 }	
}
if($action=="populate_sub_process"){
	$booking_sub_p_data=sql_select("SELECT distinct sub_process_id from wo_booking_dtls where id in($data) group by sub_process_id");
	$subProcessArr=explode(",",$booking_sub_p_data[0][csf('sub_process_id')]);
	echo "document.getElementById('txt_sub_process_id').value = '".$booking_sub_p_data[0][csf('sub_process_id')]."';\n";
	foreach($subProcessArr as $proId){
		$subProcessName .=$conversion_cost_head_array[$proId].",";
	}
	echo "document.getElementById('txt_sub_process').value = '".rtrim($subProcessName,",")."';\n";
}


if ($action=="Supplier_workorder_popup")
{
	echo load_html_head_contents("Production Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
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
						$supplier_sql=sql_select("select c.id as ID,c.mst_id as MST_ID,a.supplier_name as NAME,c.supplier_rate as RATE,d.process_type_id as PROCESS_TYPE_ID,d.const_comp as CONST_COMP,d.process_id AS PROCESS_ID,d.gsm as GSM,d.color_id as COLOR_ID,d.uom_id as UOM_ID from lib_supplier a, lib_supplier_party_type b,lib_subcon_supplier_rate c,lib_subcon_charge d where a.id=b.supplier_id and b.party_type=21 and b.supplier_id=c.supplier_id and c.mst_id=d.id and d.rate_type_id=3 and d.comapny_id=$cbo_company_name and a.id=$cbo_supplier_name");

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
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="report_formate_setting")
{
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=2 and report_id=195 and is_deleted=0 and status_active=1");
	echo "print_report_button_setting('$print_report_format');\n";
	exit();
}

if($action=="show_trim_booking_report3") // zakaria joy (16.01.22)
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	//$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library",'master_tble_id','image_location');
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	//$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$color_library=return_library_array( "select id,color_name from lib_color  where status_active=1 and is_deleted=0", "id", "color_name");
	$lib_body_part=return_library_array( "select id,body_part_full_name from lib_body_part  where status_active=1 and is_deleted=0", "id", "body_part_full_name");
	$user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
	//$fabric_color_sql=sql_select("SELECT master_tble_id, image_location from common_photo_library where is_deleted=0 and file_type=1 ");
	$fabric_ima_arr=return_library_array( "select master_tble_id,image_location from common_photo_library  where is_deleted=0 and file_type=1", "master_tble_id", "image_location");
	$path=($path=='')?'../../':$path;
	?>
	<div style="width:1150px" align="left">
       <table width="100%" cellpadding="0" cellspacing="0" style="border:0px solid black;margin:5px; font-size:16px; font-family:'Arial Narrow';" >
           <tr>
               <td width="100">
               <img  src='<? echo $path.$imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
               </td>
               <td width="">
                    <table width="100%" cellpadding="0" cellspacing="0" >
                        <tr>
                            <td align="center" style="font-size:20px;"><?=$company_library[$cbo_company_name]; ?></td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:14px;">
                            <?
                            $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
                            foreach ($nameArray as $result)
                            {
                            ?>
									<? echo $result[csf('plot_no')]; ?>
                                    <? echo $result[csf('level_no')]?>
                                    <? echo $result[csf('road_no')]; ?>
                                    <? echo $result[csf('block_no')];?>
                                    <? echo $result[csf('city')];?>
                                    <? echo $result[csf('zip_code')]; ?>
                                    <?php echo $result[csf('province')]; ?>
                                    <? echo $country_arr[$result[csf('country_id')]]; ?><br>
                                    Email Address: <? echo $result[csf('email')];?>
                                    Website No: <? echo $result[csf('website')];

									$email=$result[csf('email')];
									$website=$result[csf('website')];
									$city=$result[csf('city')];
									$road_no=$result[csf('road_no')];
									$block_no=$result[csf('block_no')];
									$com_add=$result[csf('plot_no')].' '.$result[csf('level_no')].' '.$result[csf('road_no')].' '.$result[csf('block_no')].' '.$city.' '.$result[csf('zip_code')];
                            }
                            ?>
                               </td>
                            </tr>
                            <tr>
                                <td align="center" style="font-size:20px">
                                	<strong>Service Order for Dyeing Finishing:<? echo str_replace("'","",$txt_booking_no); ?></strong>
                                </td>
                            </tr>
                      </table>
                </td>
            </tr>
       </table>
		<?
		$nameArray_job=sql_select(" SELECT b.id as po_id,b.grouping, b.po_number, a.dia_width, a.fabric_color_id, a.fin_gsm, a.gmts_color_id, a.fabric_color_id, a.job_no, a.description, a.program_no, a.lib_composition, a.delivery_date, a.delivery_end_date, a.pre_cost_fabric_cost_dtls_id as fab_dtls_id, a.sensitivity, a.rate, a.uom, a.amount, a.wo_qnty, d.body_part_id, e.style_ref_no, e.id as job_id, d.fabric_description, d.color_type_id, d.id as fabric_id, d.lib_yarn_count_deter_id from wo_booking_dtls a join wo_po_break_down b on a.po_break_down_id=b.id join wo_pre_cost_fab_conv_cost_dtls c on c.id=a.pre_cost_fabric_cost_dtls_id join 
		wo_pre_cost_fabric_cost_dtls d on c.fabric_description=d.id join wo_po_details_master e on e.id=b.job_id where 
		a.booking_no=$txt_booking_no and a.entry_form_id=229 and a.status_active=1 and a.wo_qnty>0  group by b.id, b.po_number, a.dia_width, a.fabric_color_id, a.fin_gsm, a.gmts_color_id, a.fabric_color_id, a.job_no, a.description, a.program_no, a.lib_composition, a.delivery_date, a.delivery_end_date, a.pre_cost_fabric_cost_dtls_id, a.sensitivity, a.rate, a.uom, a.amount, a.wo_qnty, d.body_part_id,b.grouping, e.style_ref_no, e.id, d.fabric_description, d.color_type_id, d.id, d.lib_yarn_count_deter_id");
		$fabric_atribute_arr=array('body_part_id','fabric_description','fin_gsm','color_type_id');
		$fabric_color_attr=array('fabric_color_id','uom','rate','fabric_id');
		$fabric_color_summary_attr=array('fabric_description');
		
		foreach($nameArray_job as $row){
			$sensitivity_prog_arr[$row[csf('job_id')]]['job_no']=$row[csf('job_no')];
			$sensitivity_prog_arr[$row[csf('job_id')]]['style_ref']=$row[csf('style_ref_no')];
			$sensitivity_prog_arr[$row[csf('job_id')]]['po_no'][$row[csf('po_id')]]=$row[csf('po_number')];
			if($row[csf('grouping')])
			{
				$sensitivity_prog_arr[$row[csf('job_id')]]['ref_no'][$row[csf('po_id')]]=$row[csf('grouping')];
			}
			
			foreach($fabric_atribute_arr as $fabattr){
				$sensitivity_prog_arr[$row[csf('job_id')]]['fabric_data'][$row[csf('fab_dtls_id')]][$fabattr]=$row[csf($fabattr)];
				$sensitivity_prog_arr[$row[csf('job_id')]]['fabric_data'][$row[csf('fab_dtls_id')]]['dia'].=$row[csf('dia_width')].',';
			}
			foreach($fabric_color_attr as $fcolorattr){
				$sensitivity_prog_arr[$row[csf('job_id')]]['fabric_data'][$row[csf('fab_dtls_id')]]['color_dtls'][$row[csf('fabric_color_id')]][$fcolorattr]=$row[csf($fcolorattr)];				
			}
			 
				$sensitivity_prog_arr[$row[csf('job_id')]]['fabric_data'][$row[csf('fab_dtls_id')]]['color_dtls'][$row[csf('fabric_color_id')]]['wo_qnty']+=$row[csf('wo_qnty')];
				$sensitivity_prog_arr[$row[csf('job_id')]]['fabric_data'][$row[csf('fab_dtls_id')]]['color_dtls'][$row[csf('fabric_color_id')]]['amount']+=$row[csf('amount')];
			 
		
			$sensitivity_prog_arr[$row[csf('job_id')]]['fabric_data'][$row[csf('fab_dtls_id')]]['color_dtls'][$row[csf('fabric_color_id')]]['dia'].=$row[csf('dia_width')].',';
			$fabric_color_summary_key=$row[csf('lib_yarn_count_deter_id')].'*'.$row[csf('fin_gsm')].'*'.$row[csf('dia_width')].'*'.$row[csf('color_type_id')];
			$fabric_color_summary[$fabric_color_summary_key]['fabric_description']=$row[csf('fabric_description')];
			$fabric_color_summary[$fabric_color_summary_key]['fin_gsm']=$row[csf('fin_gsm')];
			$fabric_color_summary[$fabric_color_summary_key]['dia_width']=$row[csf('dia_width')];
			$fabric_color_summary[$fabric_color_summary_key]['color_type_id']=$row[csf('color_type_id')];
			 
			$fabric_color_summary[$fabric_color_summary_key]['fabric_color'][$row[csf('fabric_color_id')]]['fabric_color_id']=$row[csf('fabric_color_id')];
			$fabric_color_summary[$fabric_color_summary_key]['fabric_color'][$row[csf('fabric_color_id')]]['wo_qnty']+=$row[csf('wo_qnty')];	
			 		
		}
		foreach($sensitivity_prog_arr as $jobid=>$job_data){
			foreach($job_data['fabric_data'] as $fabric_data){
				foreach($fabric_data['color_dtls'] as $fabric_color){
					$job_wise_rowspan[$jobid]+=1;
				}				
			}					
		}
		
        $nameArray=sql_select( "select a.booking_no, a.booking_date, a.pay_mode, a.remarks, a.buyer_id, a.supplier_id, a.currency_id, a.exchange_rate, a.attention, a.delivery_date, a.source, a.delivery_to, a.attention, a.tenor, a.inserted_by from wo_booking_mst a where a.booking_no=$txt_booking_no  and a.entry_form=229");
		$inserted_by=$user_lib_name[$nameArray[0][csf("inserted_by")]];
        foreach ($nameArray as $result)
        {
			$varcode_booking_no=$result[csf('booking_no')];
			$pay_mode=$result[csf('pay_mode')];
			$supplier_id=$result[csf('supplier_id')];
			if($pay_mode==5 || $pay_mode==3){
				$compsuparr=sql_select( "select company_name, plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website from lib_company where id='$supplier_id'");
				$com_supp=$compsuparr[0][csf('company_name')];
				$suplier_address=$compsuparr[0][csf('plot_no')].' '.$compsuparr[0][csf('level_no')].' '.$compsuparr[0][csf('road_no')].' '.$compsuparr[0][csf('block_no')].' '.$compsuparr[0][csf('city')].' '.$compsuparr[0][csf('zip_code')].'<br> '.$compsuparr[0][csf('email')].'<br> '.$compsuparr[0][csf('website')];
			}
			else{
				
				$suppliar_data=sql_select("SELECT id, supplier_name, contact_no, email,web_site, address_1, address_2, address_3, address_4 from lib_supplier where status_active=1 and is_deleted=0 and id='$supplier_id'");
				
				$com_supp=$suppliar_data[0][csf('supplier_name')];
				$suplier_address=$suppliar_data[0][csf('address_1')].' '.$suppliar_data[0][csf('address_2')].' '.$suppliar_data[0][csf('address_3')].' '.$suppliar_data[0][csf('address_4')].'<br>'.$suppliar_data[0][csf('contact_no')].'<br>'.$suppliar_data[0][csf('email')].'<br>'.$suppliar_data[0][csf('web_site')];
			}
			$currency_id=$result[csf('currency_id')];

        ?>
		<div style="width:1150px;">
       	<table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all">
            <tr>
                <th colspan="4" valign="top" align="center">Beneficiary</th>
				<th colspan="6" valign="top" align="center">Consignee</th>
            </tr>
			<tr>
                <td width="100" style="font-size:16px" colspan="4" align="left">
				<strong><?=$com_supp; ?></strong><br>
				<?=$suplier_address; ?>
				</td>
				<td width="100" style="font-size:16px" colspan="6" align="left">
				<strong><?=$company_library[$cbo_company_name];?></strong><br>
				<?=$com_add;?><br>
				<?=$email;  ?><br>
				<?=$website;  ?><br>
				</td>

			</tr>
			<tr>
				<th align="left">Issue Date</th>
				<td><? echo change_date_format($result[csf('booking_date')]);?></td>
				<th align="left">Delivery Date</th>
				<td><? echo change_date_format($result[csf('delivery_date')]);?></td>
				<th align="left">Contact Person</th>
				<td><? echo $result[csf('attention')];?></td>
				<th align="left">Buyer</th>
				<td><? echo $buyer_name_arr[$result[csf('buyer_id')]];?></td>
				<th align="left">Tenor</th>
				<td><? echo $result[csf('tenor')];?></td>
			</tr>
			<tr>
				<th align="left">Delivery Address</th>
				<td colspan="9"><? echo $result[csf('delivery_to')];?></td>
			</tr>
			<tr>
				<th align="left">Remarks</th>
				<td colspan="9"><? echo $result[csf('remarks')];?></td>
			</tr>			
        </table>
		<?
        }
        ?>
		<br>
		<br>
		<table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all">
			<tr>
				<th width="100">Job No</th>
				<th width="70">Int. Ref No</th>
				<th width="100">Style Ref</th>
				<th width="100">PO NO</th>
				<th width="80">Body Part</th>
				<th width="150">Fab Description</th>
                <th width="60">Color Type</th>
				<th width="80">GSM</th>
				<th width="60">Fab Dia</th>
				<th width="60">Fab Color</th>
				<th width="60">Fab Design Image</th>
				<th width="60">Finish Fab Qty</th>
				<th width="60">UOM</th>
				<?
				if($show_comments==1){?>
				<th width="60">Rate</th>
				<th width="60">Amount</th>
				<?}?>
			</tr>
			<?  $color_wise_grand_qty=0; $color_wise_grand_amount=0;
				foreach($sensitivity_prog_arr as $jobid=>$job_data){
					$ref_no=implode(",",array_unique($job_data['ref_no']));
					 ?>
					<tr>
						<td rowspan="<?= $job_wise_rowspan[$jobid]+count($job_data['fabric_data']); ?>"><?= $job_data['job_no'];  ?></td>
						<td rowspan="<?= $job_wise_rowspan[$jobid]+count($job_data['fabric_data']); ?>"><?= $ref_no;  ?></td>
						<td rowspan="<?= $job_wise_rowspan[$jobid]+count($job_data['fabric_data']); ?>"><?= $job_data['style_ref'];  ?></td>
						<td rowspan="<?= $job_wise_rowspan[$jobid]+count($job_data['fabric_data']); ?>"><?= implode(", ", $job_data['po_no']);  ?></td>
						<? 
						$fabrictr=1;
						foreach($job_data['fabric_data'] as $fabric_id=>$fabric_data){
							if($fabrictr!=1) echo '<tr>';
							$dia=implode(",",array_filter(array_unique(explode(",",$fabric_data['dia']))));
							 ?>
							<td rowspan="<?= count($fabric_data['color_dtls'])+1;  ?>"><?= $lib_body_part[$fabric_data['body_part_id']];  ?></td>
							<td rowspan="<?= count($fabric_data['color_dtls'])+1;  ?>"><?= $fabric_data['fabric_description'];  ?></td>
                            <td rowspan="<?= count($fabric_data['color_dtls'])+1;  ?>"><?= $color_type[$fabric_data['color_type_id']];  ?></td>
							<td rowspan="<?= count($fabric_data['color_dtls'])+1;  ?>"><?= $fabric_data['fin_gsm'];  ?></td>
							<td rowspan="<?= count($fabric_data['color_dtls'])+1;  ?>"><?= $dia;  ?></td>
							<? 
							$colortr=1;
							$color_wise_qty=0;
							$color_wise_amount=0;
							$fabric_img_mst='';
							foreach($fabric_data['color_dtls'] as $color_data){ 
								if($colortr!=1) echo '<tr>';
								$fabric_img_mst=$color_data['fabric_id'].'_'.$color_data['fabric_color_id'];
							?>
							<td><?= $color_library[$color_data['fabric_color_id']]  ?></td>
							<td title="<?= $fabric_img_mst?>"><? if($fabric_ima_arr[$fabric_img_mst]!=''){ ?><img  src='<? echo $path.$fabric_ima_arr[$fabric_img_mst]; ?>' height='50' width='110' /><? } ?></td>
							<td align="right"><?= number_format($color_data['wo_qnty'],2);  ?></td>
							<td><?= $unit_of_measurement[$color_data['uom']];  ?></td>

								<?
							if($show_comments==1){?>
							<td align="right"><?= $color_data['rate']  ?></td>
							<td align="right"><?= number_format($color_data['amount'],2)  ?></td>
							<?}?>
							<? 
							$colortr++;
							$color_wise_qty+=$color_data['wo_qnty'];
							$color_wise_amount+=$color_data['amount'];
							$color_wise_grand_qty+=$color_data['wo_qnty'];
							$color_wise_grand_amount+=$color_data['amount'];
							} ?>
							<tr>
							<th colspan="2" align="right">Fabric Total</th>
							<th align="right"><?= number_format($color_wise_qty,2) ?></th>
							<th></th>
							<?
							if($show_comments==1){?>
							<th></th>
							<th align="right"><?= number_format($color_wise_amount,2) ?></th>
							<?}?>
							</tr>
							
						<? 
							$fabrictr++;
						} ?>						
					</tr>
				<? } 

				if($currency_id==1){$paysa_sent="Paisa";} else if($currency_id==2){$paysa_sent="CENTS";}
			?>
			<tr>
				<th colspan="11" align="right">Grand Total</th>
				<th align="right"><?= number_format($color_wise_grand_qty,2) ?></th>
				<th></th>
					<?
				if($show_comments==1){?>
				<th></th>
				<th align="right"><?= number_format($color_wise_grand_amount,2) ?></th>
				<?}?>
			</tr>
			<tr>
				<th colspan="11" align="right">Total Booking Amount (in word)</th>
				<?
				if($show_comments==1){?>
				<th colspan="4" align="left"><? echo number_to_words(def_number_format($color_wise_grand_amount,2,""),$currency[$currency_id],$paysa_sent); ?></th>
				<?}?>
			</tr>
		</table>
		<br><br>
		<table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all">
			<tr><th colspan="6" align="center">Fab Description & Color Wise Summary</th></tr>
			<tr>
				<th>Fabrication</th>
				<th>GSM</th>
				<th>Dia</th>
				<th>Color Type</th>
				<th>Fab. Color</th>
				<th>Fin. Fab. Qty</th>
			</tr>
			<? 
			foreach($fabric_color_summary as $summaray){
				?>
				<tr>
					<td rowspan="<?= count($summaray['fabric_color']) ?>"><?= $summaray['fabric_description']  ?></td>
					<td rowspan="<?= count($summaray['fabric_color']) ?>"><?= $summaray['fin_gsm']  ?></td>
					<td rowspan="<?= count($summaray['fabric_color']) ?>"><?= $summaray['dia_width']  ?></td>
					<td rowspan="<?= count($summaray['fabric_color']) ?>"><?= $color_type[$summaray['color_type_id']]  ?></td>
					<? 
					$colorsummtr=1;
					$color_wise_qty_summ=0;
					foreach($summaray['fabric_color'] as $color_summ){ 
						if($colorsummtr!=1) echo '<tr>'	
					?>
						<td align="right"><?= $color_library[$color_summ['fabric_color_id']]  ?></td>
						<td align="right"><?= number_format($color_summ['wo_qnty'],2)  ?></td>
					<? 
					$colorsummtr++;
					$color_wise_qty_summ+=$color_summ['wo_qnty'];
					} 
				?>
				</tr>
				<tr>
					<th colspan="5" align="right">Fabric Total :</th>
					<th align="right"><?= number_format($color_wise_qty_summ,2)  ?></th>
				</tr>
				<?
			}
			?>
		</table>
         

       	<table  width="100%" class="rpt_table" style="margin:5px;font-size:16px;font-family:'Arial Narrow';"   border="1" cellpadding="0" cellspacing="0">
          <tr>
          <td><? echo get_spacial_instruction($txt_booking_no); ?></td>
          </tr>
        </table>
         <br/>
		 <?
            echo signature_table(290, $cbo_company_name, "1113px","",40,$inserted_by);
         ?>
    </div>

<?
}

if($action=="show_trim_booking_report4")//Shariar
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_fabric_natu=str_replace("'","",$cbo_fabric_natu);
	$cbo_fabric_source=str_replace("'","",$cbo_fabric_source);
	$txt_job_no=str_replace("'","",$txt_job_no);
	$show_yarn_rate=str_replace("'","",$show_yarn_rate);
	$booking_id=str_replace("'","",$booking_mst_id);
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	//booking_no=$txt_booking_no
	
	$path=str_replace("'","",$path);
	if($path==1) $path="../../";
	
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1 and master_tble_id='$cbo_company_name'",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$brand_name_arr=return_library_array( "select id, brand_name from lib_buyer_brand ",'id','brand_name');
	$user_name_arr=return_library_array( "select id,user_name from user_passwd  where status_active=1 ",'id','user_name');
	$team_leader_arr=return_library_array( "select id,team_leader_name from lib_marketing_team",'id','team_leader_name');
	$designation_arr=return_library_array( "select id,designation from   lib_supplier  where status_active=1 and is_deleted=0",'id','designation');
	$contact_no_arr=return_library_array( "select id,contact_no from   lib_supplier  where status_active=1 and is_deleted=0",'id','contact_no');
 
	//$location_name_arr=return_library_array( "select id,location_name from lib_location",'id','location_name');
	
	//$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	//$po_qnty_tot1=return_field_value( "sum(po_quantity)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	//wo_pre_cost_fabric_cost_dtls
	$pro_sub_dept_array=return_library_array( "select id,sub_department_name from lib_pro_sub_deparatment",'id','sub_department_name');
	?>
	<style type="text/css">
		@media print {
		    .pagebreak { page-break-before: always; } /* page-break-after works, as well */
		}
	</style>
	<div style="width:1330px" align="center">
    <?php
    	$lip_yarn_count=return_library_array( "select id,fabric_composition_id from lib_yarn_count_determina_mst where  status_active=1", "id", "fabric_composition_id");
		$fabric_composition=return_library_array( "select id,fabric_composition_name from lib_fabric_composition where  status_active=1", "id", "fabric_composition_name");
		
		$nameArray_approved = sql_select("select max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and  a.booking_no='$txt_booking_no' and b.entry_form=7");
		list($nameArray_approved_row) = $nameArray_approved;
		$nameArray_approved_date = sql_select("select b.approved_date as approved_date from wo_booking_mst a, approval_history b where a.id=b.mst_id and  a.booking_no='$txt_booking_no' and b.entry_form=7 and b.approved_no='" . $nameArray_approved_row[csf('approved_no')] . "'");
		list($nameArray_approved_date_row) = $nameArray_approved_date;
		$nameArray_approved_comments = sql_select("select b.comments as comments from wo_booking_mst a, approval_history b where a.id=b.mst_id and  a.booking_no='$txt_booking_no' and b.entry_form=7 and b.approved_no='" . $nameArray_approved_row[csf('approved_no')] . "'");
		list($nameArray_approved_comments_row) = $nameArray_approved_comments;

		$max_approve_date_data = sql_select("select min(b.approved_date) as approved_date,max(b.approved_date) as last_approve_date,max(b.un_approved_date) as un_approved_date from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7");
		$first_approve_date='';
		$last_approve_date='';
		$un_approved_date='';
		if(count($max_approve_date_data))
		{
			$last_approve_date=$max_approve_date_data[0][csf('last_approve_date')];
			$first_approve_date=$max_approve_date_data[0][csf('approved_date')];
			$un_approved_date=$max_approve_date_data[0][csf('un_approved_date')];
		}
		
		
	
		
		$nameArray=sql_select( "select a.booking_date, a.supplier_id, a.currency_id, a.exchange_rate, a.attention, a.delivery_date, a.po_break_down_id,a.currency_id, a.delivery_date,  a.fabric_source, a.inserted_by, a.insert_date, a.update_date, a.tagged_booking_no, a.uom, a.pay_mode, a.booking_percent, b.buyer_name,b.id as job_id, b.style_ref_no, b.gmts_item_id, b.order_uom, b.total_set_qnty, sum(b.job_quantity*b.total_set_qnty) as jobqtypcs, b.style_description, b.season_buyer_wise as season, b.product_dept, b.product_code, b.pro_sub_dep, b.dealing_marchant,b.factory_marchant, b.order_repeat_no, b.repeat_job_no, a.fabric_composition, a.remarks, a.sustainability_standard, b.brand_id, a.quality_level, a.fab_material, a.requisition_no, b.qlty_label, b.packing,  a.proceed_knitting, a.proceed_dyeing,c.process,b.team_leader,c.job_no from wo_booking_mst a, wo_po_details_master b ,wo_booking_dtls c  where a.id=c.booking_mst_id and c.job_no=b.job_no  and  c.process=31 and c.entry_form_id=229  and a.entry_form=229 and c.booking_type=3 and c.booking_no='$txt_booking_no' group by  a.booking_date, a.supplier_id, a.currency_id, a.exchange_rate, a.attention, a.delivery_date, a.po_break_down_id,a.currency_id, a.delivery_date,  a.fabric_source, a.inserted_by, a.insert_date, a.update_date, a.tagged_booking_no, a.uom, a.pay_mode, a.booking_percent, b.buyer_name,b.id , b.style_ref_no, b.gmts_item_id, b.order_uom, b.total_set_qnty, b.style_description, b.season_buyer_wise , b.product_dept, b.product_code, b.pro_sub_dep, b.dealing_marchant,b.factory_marchant, b.order_repeat_no, b.repeat_job_no, a.fabric_composition, a.remarks, a.sustainability_standard, b.brand_id, a.quality_level, a.fab_material, a.requisition_no, b.qlty_label, b.packing,  a.proceed_knitting, a.proceed_dyeing,c.process,b.team_leader,c.job_no");
		 
		 
		$jobqtypcs=0;
		foreach($nameArray as $row)
		{
			$po_id_allArr[$row[csf('po_break_down_id')]]=$row[csf('po_break_down_id')];
			$job_no_strArr[$row[csf('job_id')]]=$row[csf('job_id')];
			$job_no_Arr[$row[csf('job_no')]]=$row[csf('job_no')];
			$tagged_booking_no=$row[csf('tagged_booking_no')];
			$booking_uom=$row[csf('uom')];$pay_modeId=$row[csf('pay_mode')];
			$bookingup_date=$row[csf('update_date')];
			$bookingins_date=$row[csf('insert_date')];
			$delivery_date=$row[csf('delivery_date')];
			$supplier_id=$row[csf('supplier_id')];
			$gmts_item_id=$row[csf('gmts_item_id')];
			$buyer_name=$row[csf('buyer_name')];
			
			$product_code=$row[csf('product_code')];
			$requisition_no=$row[csf('requisition_no')];
			$jobqtypcs+=$row[csf('jobqtypcs')];
			$inserted_by2=$user_name_arr[$row[csf('inserted_by')]];
			$supplier_id=$row[csf('supplier_id')];
			$pay_mode=$row[csf('pay_mode')];
			$style_ref_no=$row[csf('style_ref_no')];
			$team_leader=$team_leader_arr[$row[csf('team_leader')]];
			$style_description=$row[csf('style_description')];
			if($row[csf('style_description')]!="")
			{
			$job_desc_strArr[$row[csf('style_description')]]=$row[csf('style_description')];
			}
			if($row[csf('style_ref_no')]!="")
			{
			$job_style_ref_noArr[$row[csf('style_ref_no')]]=$row[csf('style_ref_no')];
			}
			$currency_id=$row[csf('currency_id')];
			
			if($row[csf('remarks')]!="")
			{
			$remarks=$row[csf('remarks')];
			//echo $remarks.'DD';
			}
			$attention=$row[csf('attention')];
			$process=$conversion_cost_head_array[$row[csf('process')]];
			if($currency_id==1){$paysa_sent="Paisa";} else if($currency_id==2){$paysa_sent="CENTS";}
			
			$total_set_qnty=$row[csf('total_set_qnty')];
          //  $colar_excess_percent=$row[csf('colar_excess_percent')];
           // $cuff_excess_percent=$row[csf('cuff_excess_percent')];
           // $rmg_process_breakdown=$row[csf('rmg_process_breakdown')];
           // $booking_percent=$row[csf('booking_percent')];
			//$booking_po_id=$row[csf('po_break_down_id')];
			

		}
		
		
		
		 $po_id_all=implode(",",$po_id_allArr);
		 $job_no_str=implode(",",$job_no_strArr);
		 $job_no_Arr_all=implode(",",$job_no_Arr);
		 
		 $fab_book=sql_select("select c.booking_no,a.job_no from wo_booking_dtls c,wo_po_details_master a  where  a.job_no=c.job_no and a.id in($job_no_str) and c.status_active=1 and c.is_deleted=0 and c.booking_type=1  group by c.booking_no,a.job_no order by c.booking_no");
		//echo "select c.booking_no,a.job_no from wo_booking_dtls c,wo_po_details_master a  where  a.job_no=c.job_no and a.id in($job_no_str) and c.status_active=1 and c.is_deleted=0 and c.booking_type=1  group by c.booking_no,a.job_no order by c.booking_no";
		foreach($fab_book as $row)
		{
		  $fab_booking_noArr[$row[csf('booking_no')]]= $row[csf('booking_no')];
		}	
		 $fab_booking_noArr_all=implode(",",$fab_booking_noArr);
		 
		 if($job_no_str!="") $location=return_field_value( "location_name", "wo_po_details_master","id in($job_no_str)"); else $location="";
		$sql_loc=sql_select("select id,location_name,address from lib_location where company_id=$cbo_company_name");
		foreach($sql_loc as $row)
		{
			$location_name_arr[$row[csf('id')]]= $row[csf('location_name')];
			$location_address_arr[$row[csf('id')]]= $row[csf('address')];
		}		
		$yes_no_sql=sql_select("select job_no,cons_process from  wo_pre_cost_fab_conv_cost_dtls where job_id  in($job_no_str)  and status_active=1 and is_deleted=0  order by id");
		
		$peach=''; $brush=''; $fab_wash='';

		$emb_print=sql_select("select id, job_no, emb_name, emb_type from wo_pre_cost_embe_cost_dtls where  job_id  in($job_no_str) and status_active=1 and is_deleted=0 and cons_dzn_gmts>0 and emb_name in (1,2,3) order by id");
		
		$emb_print_data=array();
		$type_array=array(0=>$blank_array,1=>$emblishment_print_type,2=>$emblishment_embroy_type,3=>$emblishment_wash_type,4=>$emblishment_spwork_type,5=>$emblishment_gmts_type,99=>$blank_array);
		
		foreach ($emb_print as $row) 
		{
			$emb_print_data[$row[csf('job_no')]][$row[csf('emb_name')]].=$type_array[$row[csf("emb_name")]][$row[csf('emb_type')]].",";
		}
		

	

		 $cancel_po_arr=return_library_array( "select po_number,po_number from wo_po_break_down where job_no_mst='$job_no_str' and status_active=3", "po_number", "po_number");
	

		$po_shipment_date=sql_select("select  MIN(pub_shipment_date) as min_shipment_date,max(pub_shipment_date) as max_shipment_date from wo_po_break_down where id in(".$po_id_all.") order by shipment_date asc ");
         $min_shipment_date='';
         $max_shipment_date='';
         foreach ($po_shipment_date as $row) {
         	 $min_shipment_date=$row[csf('min_shipment_date')];
         	 $max_shipment_date=$row[csf('max_shipment_date')];
         	 break;
         }

        
        
       
  		ob_start();     
		?>	
											<!--    Header Company Information         -->
        <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black; font-family:Arial Narrow;" >
            <tr>
                <td width="200" style="font-size:28px"><img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' /></td>
                <td width="1250">
                    <table width="100%" cellpadding="0" cellspacing="0"  style="position: relative;">
                        <tr>
                            <td align="center" style="font-size:28px;"> <?php echo $company_library[$cbo_company_name]; ?></td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:18px;position: relative;"><?=$location_address_arr[$location]; ?></td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:24px">
                            	<span style="float:center;"><b><strong> <font style="color:black">Finish Fabric Booking</font></strong></b></span> 
                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:20px">
							<!-- <?
							if(str_replace("'","",$id_approved_id) ==1){ ?>
                            <span style="font-size:20px; float:center;"><strong> <font style="color:green"> <? echo "[Approved]"; ?> </font></strong></span> 
                               <? }else{ ?>
								<span style="font-size:20px; float:center;"><strong> <font style="color:red"><? echo "[Not Approved]"; ?> </font></strong></span> 
							   <? } ?> -->
							  
                            </td>
							<td><strong style="background-color:yellow;padding:5%;font-size: 30px;"><?=str_replace("'","",$txt_booking_no);;?></strong><br><strong style="margin-left:20%;"><?=str_replace("'","",$txt_booking_date);;?></strong></td> 
							
                        </tr>
						
						
                    </table>
					
                </td>
            </tr>
        </table>
		<?
        $job_no=trim($job_no_str,"'"); $total_set_qnty=0; $colar_excess_percent=0; $cuff_excess_percent=0; $rmg_process_breakdown=0; $booking_percent=0; $booking_po_id='';
		if($db_type==0)
        {
            $date_dif_cond="DATEDIFF(pub_shipment_date,po_received_date)";
            $group_concat_all="group_concat(grouping) as grouping, group_concat(file_no) as file_no";
        }
        else
        {
            $date_dif_cond="(pub_shipment_date-po_received_date)";
            $group_concat_all=" listagg(cast(grouping as varchar2(4000)),',') within group (order by grouping) as grouping,
                                listagg(cast(file_no as varchar2(4000)),',') within group (order by file_no) as file_no  ";
        }
        $po_number_arr=array(); $po_ship_date_arr=array(); $shipment_date=""; $po_no=""; $po_received_date=""; $shiping_status="";
        $po_sql=sql_select("select id, po_number, pub_shipment_date, MIN(pub_shipment_date) as mpub_shipment_date, MIN(po_received_date) as po_received_date, MIN(insert_date) as insert_date, plan_cut, po_quantity, shiping_status, $date_dif_cond as date_diff,min(factory_received_date) as factory_received_date, $group_concat_all,status_active from wo_po_break_down where id in(".$po_id_all.") group by id, po_number, pub_shipment_date, plan_cut, po_quantity, shiping_status, po_received_date,status_active ");
      
		
        $to_ship=0; $fp_ship=0; $f_ship=0;

        foreach($po_sql as $row)
        {
            $po_qnty_tot+=$row[csf('plan_cut')];
            $po_qnty_tot1+=$row[csf('po_quantity')];
            $po_number_arr[$row[csf('id')]]=$row[csf('po_number')];
            $po_ship_date_arr[$row[csf('id')]]=$row[csf('pub_shipment_date')];
            $po_num_arr[$row[csf('id')]]=$row[csf('po_number')];
            $po_no.=$row[csf('po_number')].", ";
            $shipment_date.=change_date_format($row[csf('mpub_shipment_date')],'dd-mm-yyyy','-').", ";
            $lead_time.=$row[csf('date_diff')].",";
            $po_received_date=change_date_format($row[csf('po_received_date')],'dd-mm-yyyy','-');
            $factory_received_date=change_date_format($row[csf('factory_received_date')],'dd-mm-yyyy','-');
            $grouping.=$row[csf('grouping')].",";
            $file_no.=$row[csf('file_no')].",";
			if($row[csf('status_active')]==3){
				$cancel_po_no[$row[csf('po_number')]]=$row[csf('po_number')];
			}

			
			$daysInHand.=(datediff('d',date('d-m-Y',time()),$row[csf('mpub_shipment_date')])-1).",";
			
			if($bookingup_date=="" || $bookingup_date=="0000-00-00 00:00:00")
			{
				$booking_date=$bookingins_date;
			}
			$WOPreparedAfter.=(datediff('d',$row[csf('insert_date')],$booking_date)-1).",";

			if($row[csf('shiping_status')]==1) {
				$shiping_status.= "FP".",";
				$to_ship++;
				$fp_ship++;
			}
			else if($row[csf('shiping_status')]==2){
				$shiping_status.= "PD".",";
				$to_ship++;
			} 
			else if($row[csf('shiping_status')]==3){
				$shiping_status.= "FS".",";
				$to_ship++;
				$f_ship++;
			} 
        }

        if($to_ship==$f_ship) $shiping_status= "<b style='color:green'>Full shipped</b>";
        else if($to_ship==$fp_ship) $shiping_status= "<b style='color:red'>Full Pending</b>";
        else $shiping_status= "<b style='color:red'>Partial Delivery</b>";
		
		$po_no=implode(",",array_filter(array_unique(explode(",",$po_no))));
		$shipment_date=implode(",",array_filter(array_unique(explode(",",$shipment_date))));
		$lead_time=implode(",",array_filter(array_unique(explode(",",$lead_time))));
		$po_received_date=implode(",",array_filter(array_unique(explode(",",$po_received_date))));
		$factory_received_date=implode(",",array_filter(array_unique(explode(",",$factory_received_date))));
		$grouping=implode(",",array_filter(array_unique(explode(",",$grouping))));
		$file_no=implode(",",array_filter(array_unique(explode(",",$file_no))));
		
		$daysInHand=implode(",",array_filter(array_unique(explode(",",$daysInHand))));
		$WOPreparedAfter=implode(",",array_filter(array_unique(explode(",",$WOPreparedAfter))));
		$shiping_status=implode(",",array_filter(array_unique(explode(",",$shiping_status))));
		
       // foreach ($nameArray as $result)
       // {
            $total_set_qnty=$result[csf('total_set_qnty')];
            $colar_excess_percent=$result[csf('colar_excess_percent')];
            $cuff_excess_percent=$result[csf('cuff_excess_percent')];
            $rmg_process_breakdown=$result[csf('rmg_process_breakdown')];
            
            $booking_percent=$result[csf('booking_percent')];
			$booking_po_id=$result[csf('po_break_down_id')];
			?>
			<table width="100%" class="rpt_table"  border="1" align="left" cellpadding="0"  cellspacing="0" rules="all"  style="font-size:18px; font-family:Arial Narrow;" >
				<tr>
					<td width="100"><b>Service Provider </b></td>		 
					<td width="140"> <span style="font-size:18px"><?
					//echo $supplier_name_arr[$supplier_id];
					 if($pay_modeId==5 || $pay_modeId==3){
					 	echo $company_library[$supplier_id];
					 	}
					 	else{
					 	echo $supplier_name_arr[$supplier_id];
					 	}
					?></span> </td>
					<td width="100"><span style="font-size:18px"><b>Address</b></span></td>
					<td width="110"><span style="font-size:18px"><?
					echo $supplier_address_arr[$supplier_id];

					
					?> </span> </td>
					<td width="110"><b>Attention</b></td>
					<td width="100"><? echo $attention; ?></td>
				
				</tr>
				<tr>
					<td width="100"><b>Job No</b></td>		 
					<td width="140"> <span style="font-size:18px"><?=str_replace("'","",$job_no_Arr_all);?></span></span> </td>
					<td width="100"><span style="font-size:18px"><b>Fabric Booking No</b></span></td>
					<td width="110"><span style="font-size:18px"><?=str_replace("'","",$fab_booking_noArr_all);?> </span> </td>
					<td width="100"><span style="font-size:18px"><b>Designation</b></span></td>
					<td width="110">&nbsp;<span style="font-size:18px"> <? echo $designation_arr[$supplier_id]; ?></span></td>	
				</tr>
				<tr>		
					<td width="100"><span style="font-size:18px"><b>Buyer/Agent Name</b></span></td>
					<td width="110">&nbsp;<span style="font-size:18px"><? echo $buyer_name_arr[$buyer_name]; ?></span></td>
					<td width="100"><span style="font-size:18px"><b>Currency</b></span></td>
					<td width="110">&nbsp;<span style="font-size:18px"><? echo $currency[$currency_id]; ?></span></td>
					<td width="100"><b>Contact No</b></td>
					<td width="140"><? echo $contact_no_arr[$supplier_id]; ?> </td>
				</tr>
				<tr>
					<td width="100" style="font-size:16px;"><b>Style</b></td>
					<td width="110"style="font-size:16px;" >&nbsp;<? echo implode(",",$job_style_ref_noArr); ?></td>				
					<td width="100"><span style="font-size:18px"><b>Garments Item</b></span></td>
					<td width="110">&nbsp;<span style="font-size:18px"> <?
                        $gmts_item_name="";
                        $gmts_item=explode(',',$gmts_item_id);
                        for($g=0;$g<=count($gmts_item); $g++)
                        {
                            $gmts_item_name.= $garments_item[$gmts_item[$g]].",";
                        }
                        echo rtrim($gmts_item_name,',');
                        ?></span></td>	
					
					<td width="110"><b>Delivery Date</b></td>
					<td width="100"><? echo change_date_format($delivery_date); ?></td>
				</tr>
				<tr>
					<td width="100"><span style="font-size:18px"><b>Remarks</b></span></td>
					<td width="350" ><span style="font-size:18px"><? echo $remarks;?></span></td>			
					<td width="100"><span style="font-size:18px"><b>GMT/ Style Description</b></span></td>
					<td width="350"><span style="font-size:18px"><? echo implode(",",$job_desc_strArr); ?></span></td>
					<td width="110"><b>Responsible Merchandiser</b></td>
					<td width="100"><? echo $inserted_by2; ?></td>
				</tr>
			</table>
			<br>
			
			<?
		//}	
			
	  	?>
		
		<br>
		<h5 style="color:red;"></h5>
		<br>


		<?php
		$fabric_desc_arr=array();

		$wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select id,fabric_description,cons_process from wo_pre_cost_fab_conv_cost_dtls  where job_id in($job_no_str)");
		foreach( $wo_pre_cost_fab_conv_cost_dtls_id as $row_wo_pre_cost_fab_conv_cost_dtls_id)
		{
			if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]!=0)
			{
				$fabric_description=sql_select("select id,body_part_id,body_part_type,color_type_id,fabric_description,construction,composition,gsm_weight,width_dia_type from  wo_pre_cost_fabric_cost_dtls 
				where  id='".$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]."'");
				list($fabric_description_row)=$fabric_description;
				


				$fabric_desc_arr[$fabric_description_row[csf("id")]]['body_part_id']=$fabric_description_row[csf("body_part_id")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['construction']=$fabric_description_row[csf("construction")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['composition']=$fabric_description_row[csf("composition")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['color_type_id']=$fabric_description_row[csf("color_type_id")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['gsm_weight']=$fabric_description_row[csf("gsm_weight")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['width_dia_type']=$fabric_description_row[csf("width_dia_type")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['body_part_type']=$fabric_description_row[csf("body_part_type")];
				
			}
			if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]==0)
			{
				
			
				$fabric_description=sql_select("select id,body_part_id,body_part_type,color_type_id,fabric_description,construction,composition,gsm_weight,width_dia_type from  wo_pre_cost_fabric_cost_dtls 
				where  job_id in($job_no_str)");

				foreach( $fabric_description as $fabric_description_row)
				{
				

				$fabric_desc_arr[$fabric_description_row[csf("id")]]['body_part_id']=$fabric_description_row[csf("body_part_id")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['construction']=$fabric_description_row[csf("construction")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['composition']=$fabric_description_row[csf("composition")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['color_type_id']=$fabric_description_row[csf("color_type_id")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['gsm_weight']=$fabric_description_row[csf("gsm_weight")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['width_dia_type']=$fabric_description_row[csf("width_dia_type")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['body_part_type']=$fabric_description_row[csf("body_part_type")];
		
				}
				
			}


		}

			// echo "<pre>";
			// print_r($fabric_desc_arr);



		$pre_cons_data=sql_select("select  id, po_break_down_id, color_number_id, gmts_sizes, dia_width, item_size, cons, process_loss_percent, requirment, pcs, color_size_table_id, rate, amount, remarks ,pre_cost_fabric_cost_dtls_id  as fab_desc_id from wo_pre_cos_fab_co_avg_con_dtls where job_id in($job_no_str)  ");


		foreach($pre_cons_data as $row){

			$color_wise_data[$row[csf("po_break_down_id")]][$row[csf("fab_desc_id")]][$row[csf("color_number_id")]]['finsh_cons']=$row[csf("cons")];
			$color_wise_data[$row[csf("po_break_down_id")]][$row[csf("fab_desc_id")]][$row[csf("color_number_id")]]['gray_cons']=$row[csf("requirment")];
			$color_wise_data[$row[csf("po_break_down_id")]][$row[csf("fab_desc_id")]][$row[csf("color_number_id")]]['gray_cons']=$row[csf("requirment")];
			if($row[csf("dia_width")]!="")
			{
			$color_wise_data[$row[csf("po_break_down_id")]][$row[csf("fab_desc_id")]][$row[csf("color_number_id")]]['dia_width']=$row[csf("dia_width")];
			}
			$color_wise_data[$row[csf("po_break_down_id")]][$row[csf("fab_desc_id")]][$row[csf("color_number_id")]]['process_loss_percent']=$row[csf("process_loss_percent")];

		}

		$nameArray_fabric_description= sql_select("select c.id as conv_dtl_id,b.job_no,b.po_break_down_id as po_id,b.sensitivity,b.uom,b.gmts_color_id,b.fabric_color_id,b.gmts_size,b.option_shade,sum(b.wo_qnty) as wo_qnty,a.style_ref_no,d.po_number,d.id,
		sum(b.amount) as amount,c.charge_unit,c.fabric_description  as fab_desc_id,b.delivery_date,b.delivery_end_date,b.labdip_no,b.rate	from wo_pre_cost_fab_conv_cost_dtls c,wo_booking_dtls b,wo_po_details_master a,wo_po_break_down d where b.pre_cost_fabric_cost_dtls_id=c.id and b.job_no=c.job_no and b.job_no=a.job_no and b.po_break_down_id=d.id and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 	and d.job_id in($job_no_str) and b.wo_qnty>0	and b.booking_type=3 and b.process=31 and  b.booking_no='$txt_booking_no'  	group by b.job_no,c.id,c.charge_unit,b.sensitivity,b.uom,b.gmts_color_id,b.fabric_color_id,b.gmts_size,c.fabric_description,b.delivery_date,b.delivery_end_date,a.style_ref_no, b.po_break_down_id,d.po_number,d.id,b.labdip_no ,b.rate,b.option_shade");
	 
		

	
		$body_part_type_arr=array();
		foreach ($nameArray_fabric_description as $row) {	

			$body_part_id=$fabric_desc_arr[$row[csf("fab_desc_id")]]['body_part_id'];
			$body_part_type=$fabric_desc_arr[$row[csf("fab_desc_id")]]['body_part_type'];
			$construction=$fabric_desc_arr[$row[csf("fab_desc_id")]]['construction'];
			$composition=$fabric_desc_arr[$row[csf("fab_desc_id")]]['composition'];
			$color_type_id=$fabric_desc_arr[$row[csf("fab_desc_id")]]['color_type_id'];
			$gsm_weight=$fabric_desc_arr[$row[csf("fab_desc_id")]]['gsm_weight'];
			$width_dia_type=$fabric_desc_arr[$row[csf("fab_desc_id")]]['width_dia_type'];
			if($body_part_type==40 || $body_part_type==50){
				$body_part_type_arr[$body_part_type]=$body_part_type;
			}


			$finsh_cons=$color_wise_data[$row[csf("po_id")]][$row[csf("fab_desc_id")]][$row[csf("gmts_color_id")]]['finsh_cons'];
			$gray_cons=$color_wise_data[$row[csf("po_id")]][$row[csf("fab_desc_id")]][$row[csf("gmts_color_id")]]['gray_cons'];
			$dia_width=$color_wise_data[$row[csf("po_id")]][$row[csf("fab_desc_id")]][$row[csf("gmts_color_id")]]['dia_width'];
			$process_loss_percent=$color_wise_data[$row[csf("po_id")]][$row[csf("fab_desc_id")]][$row[csf("gmts_color_id")]]['process_loss_percent'];
		
	
			$grouping_item=$row[csf('fabric_color_id')].'*'.$color_type_id.'*'.$row[csf('po_id')].'*'.$body_part_id.'*'.$construction.'*'.$composition.'*'.$gsm_weight.'*'.$width_dia_type.'*'.$dia_width;	
				$pp=100+$process_loss_percent;
			$fabric_data_arr[$row[csf('labdip_no')]][$grouping_item]['gmts_color_id'] = $row[csf('gmts_color_id')];
			$fabric_data_arr[$row[csf('labdip_no')]][$grouping_item]['style_ref_no'] = $row[csf('style_ref_no')];
			$fabric_data_arr[$row[csf('labdip_no')]][$grouping_item]['po_id'] = $row[csf('po_id')];
			$fabric_data_arr[$row[csf('labdip_no')]][$grouping_item]['po_number'] = $row[csf('po_number')];
			$fabric_data_arr[$row[csf('labdip_no')]][$grouping_item]['fabric_color_id'] = $row[csf('fabric_color_id')];
			$fabric_data_arr[$row[csf('labdip_no')]][$grouping_item]['labdip_no'] = $row[csf('labdip_no')];
			$fabric_data_arr[$row[csf('labdip_no')]][$grouping_item]['body_part_id'] = $body_part_id;
			$fabric_data_arr[$row[csf('labdip_no')]][$grouping_item]['fabric_des'] = $construction.','.$composition;
			$fabric_data_arr[$row[csf('labdip_no')]][$grouping_item]['gsm'] = $gsm_weight;
			$fabric_data_arr[$row[csf('labdip_no')]][$grouping_item]['fabric_dia'] = $dia_width.",".$fabric_typee[$width_dia_type];
			$fabric_data_arr[$row[csf('labdip_no')]][$grouping_item]['color_type_id'] = $color_type_id;
			$fabric_data_arr[$row[csf('labdip_no')]][$grouping_item]['finsh_cons'] = $finsh_cons;
			$fabric_data_arr[$row[csf('labdip_no')]][$grouping_item]['gray_cons'] = $gray_cons;
			$fabric_data_arr[$row[csf('labdip_no')]][$grouping_item]['fin_fab_qnty'] =($row[csf('wo_qnty')]/$pp)*100;
			$fabric_data_arr[$row[csf('labdip_no')]][$grouping_item]['grey_fab_qnty'] = $row[csf('wo_qnty')];
			$fabric_data_arr[$row[csf('labdip_no')]][$grouping_item]['amount'] = $row[csf('amount')];
			$fabric_data_arr[$row[csf('labdip_no')]][$grouping_item]['rate'] = $row[csf('rate')];
			$fabric_data_arr[$row[csf('labdip_no')]][$grouping_item]['option_shade'] = $row[csf('option_shade')];
			$fabric_data_arr[$row[csf('labdip_no')]][$grouping_item]['process_loss_percent'] = $process_loss_percent;

			if($row[csf('delivery_date')]){
				$fabric_data_arr[$row[csf('labdip_no')]][$grouping_item]['delivery_start_date'] = $row[csf('delivery_date')];
			}
			if($row[csf('delivery_end_date')]){			
				$fabric_data_arr[$row[csf('labdip_no')]][$grouping_item]['delivery_end_date'] = $row[csf('delivery_end_date')];
			}
			
	
		}

		$body_part_type_ids=implode(",",$body_part_type_arr);
	
		?>
		 <table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all"  style="font-size: 18px;">
			 <tr>	
			 	 <th>Lab Dip No</th>
				 <th>Pantone</th>
				 <th>Style Ref</th>
				 <th>Order No</th>	
				 <th>Fabric Color</th>	
				 <th>Gmts Colors</th>
				 <th>Fabrication</th>				 						 
				 <th>Body Part</th>				 
				 <th>GSM</th>
				 <th>Dia Type with </br> Fabric Dia</th>			
				 <th>Color Type</th>
				 <th>Finish  Qty</th>				
				 <th>Rate</th>
				 <th>Amount</th>

			 </tr>
			 <? 
			 foreach ($fabric_data_arr as $lab_id=>$fabric_data_arr) {  
			 $i=1;     		 	
			 $sub_fin_fab_qnty=0;   		 	
			 $sub_grey_fab_qnty=0;
			 $sub_amount=0;
				 foreach ($fabric_data_arr as $fabric_id => $value) {	
					$sub_fin_fab_qnty+=$value['fin_fab_qnty'];   		 	
					$sub_grey_fab_qnty+=$value['grey_fab_qnty']; 
					$rate=$value['rate'];
					$sub_amount+=$rate*$value['fin_fab_qnty'];
					
							 	
						  if($i==1){
							
						   ?>
						  <tr>
							 <td style="font-size: 16px;" align="center" rowspan="<? echo count($fabric_data_arr) ?>"><? echo  $lab_id;?></td>
							 <td style="font-size: 16px;" align="center"><? echo $value['option_shade'] ?></td>	
							 <td style="font-size: 16px;" align="center"><? echo $value['style_ref_no'] ?></td>
							 <td style="font-size: 16px;" align="center"><? echo $value['po_number'] ?></td>	
							 <td style="font-size: 16px;" align="center"><? echo $color_library[$value['fabric_color_id']] ?></td>		
							 <td style="font-size: 16px;" align="center"><? echo $color_library[$value['gmts_color_id']] ?></td>	
							 <td style="font-size: 16px;" align="center"><? echo $value['fabric_des'] ?></td>
							 <td style="font-size: 16px;" align="center"><? echo $body_part[$value['body_part_id']] ?></td>
							 <td style="font-size: 16px;" align="center"><? echo $value['gsm'] ?></td>
							 <td style="font-size: 16px;" align="center"><? echo $value['fabric_dia'] ?></td>
							 <td style="font-size: 16px;" align="center"><? echo $color_type[$value['color_type_id']] ?></td>
							 <td style="font-size: 16px;" align="right"><? echo fn_number_format($value['fin_fab_qnty'],2) ; ?></td>					
							 <td style="font-size: 16px;" align="right"><? echo fn_number_format($rate,2); ?></td>
							 <td style="font-size: 16px;" align="right"><? echo fn_number_format($rate*$value['fin_fab_qnty'],4) ; ?></td>

						 </tr>
						  <? } 
						  else {
							?>
							  <tr>
							 <td style="font-size: 16px;" align="center"><? echo $value['option_shade'] ?></td>	
							 <td style="font-size: 16px;" align="center"><? echo $value['style_ref_no'] ?></td>		
							 <td style="font-size: 16px;" align="center"><? echo $value['po_number'] ?></td>
							 <td style="font-size: 16px;" align="center"><? echo $color_library[$value['fabric_color_id']] ?></td>	
							 <td style="font-size: 16px;" align="center"><? echo $color_library[$value['gmts_color_id']] ?></td>	
							 <td style="font-size: 16px;" align="center"><? echo $value['fabric_des'] ?></td>							 			
							 <td style="font-size: 16px;" align="center"><? echo $body_part[$value['body_part_id']] ?></td>							 
							 <td style="font-size: 16px;" align="center"><? echo $value['gsm'] ?></td>
							 <td style="font-size: 16px;" align="center"><? echo $value['fabric_dia'] ?></td>
							 <td style="font-size: 16px;" align="center"><? echo $color_type[$value['color_type_id']] ?></td>
							 <td style="font-size: 16px;" align="right"><? echo fn_number_format($value['fin_fab_qnty'],2) ; ?></td>						
							 <td style="font-size: 16px;" align="right"><? echo fn_number_format($rate,2); ?></td>
							 <td style="font-size: 16px;" align="right"><? echo fn_number_format($rate*$value['fin_fab_qnty'],4) ; ?></td>
							 </tr>
						  <? }
						  $i++;
				 }
				 ?>
				 <tr>
				 <th align="right" colspan="11"> Sub Total</th>
				 <th align="right"><?echo number_format($sub_fin_fab_qnty,2);  ?></th>		
				 <th></th>
				 <th align="right"><?echo number_format($sub_amount,4);  ?></th>
				 </tr>
				 <?
				 	$fin_fab_qnty+=	$sub_fin_fab_qnty;   		 	
					$grey_fab_qnty+=$sub_grey_fab_qnty;
					$amount+=$sub_amount;
					?>
				 
			 <?
			 } 
			 ?>
			 <tr>
				 <th align="right" colspan="11"> Grand Total</th>
				 <th align="right"><?echo number_format($fin_fab_qnty,2);  ?></th>			
				 <th></th>
				 <th align="right"><?echo number_format($amount,4);  ?></th>
			</tr>
			<tr>
				<th colspan="13" align="left">Grand Total Booking Amount (in word):<? echo str_replace("-"," ",number_to_words(def_number_format($amount,2,""),$currency[$currency_id],$paysa_sent)); ?></th>
				
			</tr>
		</table>
		  <br/>



      	<!--  Here will be the main portion  -->
		<?
		//$job_no_strArr[$row[csf('job_id')]]=$row[csf('job_id')];
		//	$job_no_Arr[$row[csf('job_no')]]=$row[csf('job_no')];
			
        $costing_per=""; $costing_per_qnty=0; //
        $costing_per_id=return_field_value( "costing_per", "wo_pre_cost_mst","job_id in(".implode(",",$job_no_strArr).")");
        if($costing_per_id==1)
        {
			$costing_per="1 Dzn";
			$costing_per_qnty=12;
        }
        if($costing_per_id==2)
        {
			$costing_per="1 Pcs";
			$costing_per_qnty=1;
        }
        if($costing_per_id==3)
        {
			$costing_per="2 Dzn";
			$costing_per_qnty=24;
        }
        if($costing_per_id==4)
        {
			$costing_per="3 Dzn";
			$costing_per_qnty=36;
        }
        if($costing_per_id==5)
        {
			$costing_per="4 Dzn";
			$costing_per_qnty=48;
        }




      
		
		?>
        <br/>
        

       		
        <br/>
        
		<fieldset>
                   
			<table  width="100%" class="rpt_table" style="margin:5px;font-size:16px;font-family:'Arial Narrow';"   border="1" cellpadding="0" cellspacing="0">
				<tr>
				<td><? echo get_spacial_instruction($txt_booking_no); ?></td>
				</tr>
			</table>
			</fieldset>       
        <div ><? echo signature_table(290, $cbo_company_name, "1400px",'',40,$inserted_by2);  //signature_table(1, $cbo_company_name, "1400px"); //$user_name_arr[$user_id] ?></div>
		<br>
		    
       </div>
       <?
	$emailBody=ob_get_contents();
	//ob_clean();
	if($is_mail_send==1){
		/*$req_approved=return_field_value("id", "ELECTRONIC_APPROVAL_SETUP", "PAGE_ID = 336 and is_deleted=0");
		$is_approved=return_field_value("IS_APPROVED", "WO_BOOKING_MST", "STATUS_ACTIVE = 1 and is_deleted=0 and booking_no='$txt_booking_no'");
		if($req_approved && $is_approved==1){
			$emailBody.="<h1 style='border:1px sloid #0ff;'>Approved</h1>";
		}
		elseif($req_approved && $is_approved==0){
			$emailBody.="<h1 style='border:1px sloid #0ff;'>Draft</h1>";
		}
	*/		
		
		$sql = "SELECT c.email_address as EMAIL FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=87 and b.mail_user_setup_id=c.id and a.company_id =$cbo_company_name";
		$mail_sql_res=sql_select($sql);
		
		$mailArr=array();
		foreach($mail_sql_res as $row)
		{
			$mailArr[$row[EMAIL]]=$row[EMAIL]; 
		}
		
		$supplier_id=$nameArray[0][csf('supplier_id')];
		$supplier_mail=return_field_value("email", "lib_supplier", "status_active=1 and is_deleted=0 and id=$supplier_id ");

		
		$mailArr=array();
		if($mail_id!=''){$mailArr[]=$mail_id;}
		if($supplier_mail_arr[$supplier_id]!=''){$mailArr[]=$supplier_mail;}
		
		$to=implode(',',$mailArr);
		$subject="Fabric Booking Auto Mail";
		
		if($to!=""){
			require '../../../vendor/phpmailer/phpmailer/PHPMailerAutoload.php';
			require_once('../../../auto_mail/setting/mail_setting.php');
			$header=mailHeader();
			sendMailMailer( $to, $subject, $emailBody );
		}
	}
	exit();
}

if($action=="show_trim_booking_report5")//Shariar
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_fabric_natu=str_replace("'","",$cbo_fabric_natu);
	$cbo_fabric_source=str_replace("'","",$cbo_fabric_source);
	$txt_job_no=str_replace("'","",$txt_job_no);
	$show_yarn_rate=str_replace("'","",$show_yarn_rate);
	$booking_id=str_replace("'","",$booking_mst_id);
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	//booking_no=$txt_booking_no
	
	$path=str_replace("'","",$path);
	if($path==1) $path="../../";
	
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1 and master_tble_id='$cbo_company_name'",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$brand_name_arr=return_library_array( "select id, brand_name from lib_buyer_brand ",'id','brand_name');
	$user_name_arr=return_library_array( "select id,user_name from user_passwd  where status_active=1 ",'id','user_name');
	$team_leader_arr=return_library_array( "select id,team_leader_name from lib_marketing_team",'id','team_leader_name');
	$designation_arr=return_library_array( "select id,designation from   lib_supplier  where status_active=1 and is_deleted=0",'id','designation');
	$contact_no_arr=return_library_array( "select id,contact_no from   lib_supplier  where status_active=1 and is_deleted=0",'id','contact_no');
 
	//$location_name_arr=return_library_array( "select id,location_name from lib_location",'id','location_name');
	
	//$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	//$po_qnty_tot1=return_field_value( "sum(po_quantity)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	//wo_pre_cost_fabric_cost_dtls
	$pro_sub_dept_array=return_library_array( "select id,sub_department_name from lib_pro_sub_deparatment",'id','sub_department_name');
	?>
	<style type="text/css">
		@media print {
		    .pagebreak { page-break-before: always; } /* page-break-after works, as well */
		}
	</style>
	<div style="width:1330px" align="center">
    <?php
    	$lip_yarn_count=return_library_array( "select id,fabric_composition_id from lib_yarn_count_determina_mst where  status_active=1", "id", "fabric_composition_id");
		$fabric_composition=return_library_array( "select id,fabric_composition_name from lib_fabric_composition where  status_active=1", "id", "fabric_composition_name");
		
		$nameArray_approved = sql_select("select max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and  a.booking_no='$txt_booking_no' and b.entry_form=7");
		list($nameArray_approved_row) = $nameArray_approved;
		$nameArray_approved_date = sql_select("select b.approved_date as approved_date from wo_booking_mst a, approval_history b where a.id=b.mst_id and  a.booking_no='$txt_booking_no' and b.entry_form=7 and b.approved_no='" . $nameArray_approved_row[csf('approved_no')] . "'");
		list($nameArray_approved_date_row) = $nameArray_approved_date;
		$nameArray_approved_comments = sql_select("select b.comments as comments from wo_booking_mst a, approval_history b where a.id=b.mst_id and  a.booking_no='$txt_booking_no' and b.entry_form=7 and b.approved_no='" . $nameArray_approved_row[csf('approved_no')] . "'");
		list($nameArray_approved_comments_row) = $nameArray_approved_comments;

		$max_approve_date_data = sql_select("select min(b.approved_date) as approved_date,max(b.approved_date) as last_approve_date,max(b.un_approved_date) as un_approved_date from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7");
		$first_approve_date='';
		$last_approve_date='';
		$un_approved_date='';
		if(count($max_approve_date_data))
		{
			$last_approve_date=$max_approve_date_data[0][csf('last_approve_date')];
			$first_approve_date=$max_approve_date_data[0][csf('approved_date')];
			$un_approved_date=$max_approve_date_data[0][csf('un_approved_date')];
		}
		
		
	
		
		$nameArray=sql_select( "select a.booking_date, a.supplier_id, a.currency_id, a.exchange_rate, a.attention, a.booking_date, a.po_break_down_id,a.currency_id, a.delivery_date,a.source,  a.fabric_source, a.inserted_by, a.insert_date, a.update_date, a.tagged_booking_no, a.uom, a.pay_mode, a.booking_percent, b.buyer_name,b.id as job_id, b.style_ref_no, b.gmts_item_id, b.order_uom, b.total_set_qnty, sum(b.job_quantity*b.total_set_qnty) as jobqtypcs, b.style_description, b.season_buyer_wise as season, b.product_dept, b.product_code, b.pro_sub_dep, b.dealing_marchant,b.factory_marchant, b.order_repeat_no, b.repeat_job_no, a.fabric_composition, a.remarks, a.sustainability_standard, b.brand_id, a.quality_level, a.fab_material, a.requisition_no, b.qlty_label, b.packing,  a.proceed_knitting, a.proceed_dyeing,c.process,b.team_leader,c.job_no from wo_booking_mst a, wo_po_details_master b ,wo_booking_dtls c  where a.id=c.booking_mst_id and c.job_no=b.job_no  and  c.process=31 and c.entry_form_id=229  and a.entry_form=229 and c.booking_type=3 and c.booking_no='$txt_booking_no' group by  a.booking_date, a.supplier_id, a.currency_id, a.exchange_rate, a.attention, a.booking_date, a.po_break_down_id,a.currency_id, a.delivery_date,a.source,  a.fabric_source, a.inserted_by, a.insert_date, a.update_date, a.tagged_booking_no, a.uom, a.pay_mode, a.booking_percent, b.buyer_name,b.id , b.style_ref_no, b.gmts_item_id, b.order_uom, b.total_set_qnty, b.style_description, b.season_buyer_wise , b.product_dept, b.product_code, b.pro_sub_dep, b.dealing_marchant,b.factory_marchant, b.order_repeat_no, b.repeat_job_no, a.fabric_composition, a.remarks, a.sustainability_standard, b.brand_id, a.quality_level, a.fab_material, a.requisition_no, b.qlty_label, b.packing,  a.proceed_knitting, a.proceed_dyeing,c.process,b.team_leader,c.job_no");
		 
		 
		$jobqtypcs=0;
		foreach($nameArray as $row)
		{
			$po_id_allArr[$row[csf('po_break_down_id')]]=$row[csf('po_break_down_id')];
			$job_no_strArr[$row[csf('job_id')]]=$row[csf('job_id')];
			$job_no_Arr[$row[csf('job_no')]]=$row[csf('job_no')];
			$tagged_booking_no=$row[csf('tagged_booking_no')];
			$booking_uom=$row[csf('uom')];$pay_modeId=$row[csf('pay_mode')];
			$bookingup_date=$row[csf('update_date')];
			$bookingins_date=$row[csf('insert_date')];
			$delivery_date=$row[csf('delivery_date')];
			$booking_date=$row[csf('booking_date')];
			$supplier_id=$row[csf('supplier_id')];
			$gmts_item_id=$row[csf('gmts_item_id')];
			$buyer_name=$row[csf('buyer_name')];
			$source_id=$row[csf('source')];
			$exchange_rate=$row[csf('exchange_rate')];
			$product_code=$row[csf('product_code')];
			$requisition_no=$row[csf('requisition_no')];
			$jobqtypcs+=$row[csf('jobqtypcs')];
			$inserted_by2=$user_name_arr[$row[csf('inserted_by')]];
			$supplier_id=$row[csf('supplier_id')];
			$pay_mode=$row[csf('pay_mode')];
			$style_ref_no=$row[csf('style_ref_no')];
			$team_leader=$team_leader_arr[$row[csf('team_leader')]];
			$style_description=$row[csf('style_description')];
			if($row[csf('style_description')]!="")
			{
			$job_desc_strArr[$row[csf('style_description')]]=$row[csf('style_description')];
			}
			if($row[csf('style_ref_no')]!="")
			{
			$job_style_ref_noArr[$row[csf('style_ref_no')]]=$row[csf('style_ref_no')];
			}
			$currency_id=$row[csf('currency_id')];
			
			if($row[csf('remarks')]!="")
			{
			$remarks=$row[csf('remarks')];
			//echo $remarks.'DD';
			}
			$attention=$row[csf('attention')];
			$process=$conversion_cost_head_array[$row[csf('process')]];
			if($currency_id==1){$paysa_sent="Paisa";} else if($currency_id==2){$paysa_sent="CENTS";}
			
			$total_set_qnty=$row[csf('total_set_qnty')];
          //  $colar_excess_percent=$row[csf('colar_excess_percent')];
           // $cuff_excess_percent=$row[csf('cuff_excess_percent')];
           // $rmg_process_breakdown=$row[csf('rmg_process_breakdown')];
           // $booking_percent=$row[csf('booking_percent')];
			//$booking_po_id=$row[csf('po_break_down_id')];
			

		}
		
		
		
		 $po_id_all=implode(",",$po_id_allArr);
		 $job_no_str=implode(",",$job_no_strArr);
		 $job_no_Arr_all=implode(",",$job_no_Arr);
		 
		 $fab_book=sql_select("select c.booking_no,a.job_no from wo_booking_dtls c,wo_po_details_master a  where  a.job_no=c.job_no and a.id in($job_no_str) and c.status_active=1 and c.is_deleted=0 and c.booking_type=1  group by c.booking_no,a.job_no order by c.booking_no");
		//echo "select c.booking_no,a.job_no from wo_booking_dtls c,wo_po_details_master a  where  a.job_no=c.job_no and a.id in($job_no_str) and c.status_active=1 and c.is_deleted=0 and c.booking_type=1  group by c.booking_no,a.job_no order by c.booking_no";
		foreach($fab_book as $row)
		{
		  $fab_booking_noArr[$row[csf('booking_no')]]= $row[csf('booking_no')];
		}	
		 $fab_booking_noArr_all=implode(",",$fab_booking_noArr);
		 
		 if($job_no_str!="") $location=return_field_value( "location_name", "wo_po_details_master","id in($job_no_str)"); else $location="";
		$sql_loc=sql_select("select id,location_name,address from lib_location where company_id=$cbo_company_name");
		foreach($sql_loc as $row)
		{
			$location_name_arr[$row[csf('id')]]= $row[csf('location_name')];
			$location_address_arr[$row[csf('id')]]= $row[csf('address')];
		}		
		$yes_no_sql=sql_select("select job_no,cons_process from  wo_pre_cost_fab_conv_cost_dtls where job_id  in($job_no_str)  and status_active=1 and is_deleted=0  order by id");
		
		$peach=''; $brush=''; $fab_wash='';

		$emb_print=sql_select("select id, job_no, emb_name, emb_type from wo_pre_cost_embe_cost_dtls where  job_id  in($job_no_str) and status_active=1 and is_deleted=0 and cons_dzn_gmts>0 and emb_name in (1,2,3) order by id");
		
		$emb_print_data=array();
		$type_array=array(0=>$blank_array,1=>$emblishment_print_type,2=>$emblishment_embroy_type,3=>$emblishment_wash_type,4=>$emblishment_spwork_type,5=>$emblishment_gmts_type,99=>$blank_array);
		
		foreach ($emb_print as $row) 
		{
			$emb_print_data[$row[csf('job_no')]][$row[csf('emb_name')]].=$type_array[$row[csf("emb_name")]][$row[csf('emb_type')]].",";
		}
		

	

		 $cancel_po_arr=return_library_array( "select po_number,po_number from wo_po_break_down where job_no_mst='$job_no_str' and status_active=3", "po_number", "po_number");
	

		$po_shipment_date=sql_select("select  MIN(pub_shipment_date) as min_shipment_date,max(pub_shipment_date) as max_shipment_date from wo_po_break_down where id in(".$po_id_all.") order by shipment_date asc ");
         $min_shipment_date='';
         $max_shipment_date='';
         foreach ($po_shipment_date as $row) {
         	 $min_shipment_date=$row[csf('min_shipment_date')];
         	 $max_shipment_date=$row[csf('max_shipment_date')];
         	 break;
         }

        
        
       
  		ob_start();     
		?>	
											<!--    Header Company Information         -->
        <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black; font-family:Arial Narrow;" >
            <tr>
                <td width="200" style="font-size:28px"><img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' /></td>
                <td width="1250">
                    <table width="100%" cellpadding="0" cellspacing="0"  style="position: relative;">
                        <tr>
                            <td align="center" style="font-size:28px;"> <?php echo $company_library[$cbo_company_name]; ?></td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:18px;position: relative;"><?=$location_address_arr[$location]; ?></td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:24px">
                            	<span style="float:center;"><b><strong> <font style="color:black">Service Booking For Dyeing</font></strong></b></span> 
                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:20px">
							<!-- <?
							if(str_replace("'","",$id_approved_id) ==1){ ?>
                            <span style="font-size:20px; float:center;"><strong> <font style="color:green"> <? echo "[Approved]"; ?> </font></strong></span> 
                               <? }else{ ?>
								<span style="font-size:20px; float:center;"><strong> <font style="color:red"><? echo "[Not Approved]"; ?> </font></strong></span> 
							   <? } ?> -->
							  
                            </td>
							<!-- <td><strong style="background-color:yellow;padding:5%;font-size: 30px;"><?=str_replace("'","",$txt_booking_no);;?></strong><br><strong style="margin-left:20%;"><?=str_replace("'","",$txt_booking_date);;?></strong></td>  -->
							
                        </tr>
						
						
                    </table>
					
                </td>
            </tr>
        </table>

		<?
        $job_no=trim($job_no_str,"'"); $total_set_qnty=0; $colar_excess_percent=0; $cuff_excess_percent=0; $rmg_process_breakdown=0; $booking_percent=0; $booking_po_id='';
		if($db_type==0)
        {
            $date_dif_cond="DATEDIFF(pub_shipment_date,po_received_date)";
            $group_concat_all="group_concat(grouping) as grouping, group_concat(file_no) as file_no";
        }
        else
        {
            $date_dif_cond="(pub_shipment_date-po_received_date)";
            $group_concat_all=" listagg(cast(grouping as varchar2(4000)),',') within group (order by grouping) as grouping,
                                listagg(cast(file_no as varchar2(4000)),',') within group (order by file_no) as file_no  ";
        }
        $po_number_arr=array(); $po_ship_date_arr=array(); $shipment_date=""; $po_no=""; $po_received_date=""; $shiping_status="";
        $po_sql=sql_select("select id, po_number, pub_shipment_date, MIN(pub_shipment_date) as mpub_shipment_date, MIN(po_received_date) as po_received_date, MIN(insert_date) as insert_date, plan_cut, po_quantity, shiping_status, $date_dif_cond as date_diff,min(factory_received_date) as factory_received_date, $group_concat_all,status_active from wo_po_break_down where id in(".$po_id_all.") group by id, po_number, pub_shipment_date, plan_cut, po_quantity, shiping_status, po_received_date,status_active ");
      
		
        $to_ship=0; $fp_ship=0; $f_ship=0;

        foreach($po_sql as $row)
        {
            $po_qnty_tot+=$row[csf('plan_cut')];
            $po_qnty_tot1+=$row[csf('po_quantity')];
            $po_number_arr[$row[csf('id')]]=$row[csf('po_number')];
            $po_ship_date_arr[$row[csf('id')]]=$row[csf('pub_shipment_date')];
            $po_num_arr[$row[csf('id')]]=$row[csf('po_number')];
            $po_no.=$row[csf('po_number')].", ";
            $shipment_date.=change_date_format($row[csf('mpub_shipment_date')],'dd-mm-yyyy','-').", ";
            $lead_time.=$row[csf('date_diff')].",";
            $po_received_date=change_date_format($row[csf('po_received_date')],'dd-mm-yyyy','-');
            $factory_received_date=change_date_format($row[csf('factory_received_date')],'dd-mm-yyyy','-');
            $grouping.=$row[csf('grouping')].",";
            $file_no.=$row[csf('file_no')].",";
			if($row[csf('status_active')]==3){
				$cancel_po_no[$row[csf('po_number')]]=$row[csf('po_number')];
			}

			
			$daysInHand.=(datediff('d',date('d-m-Y',time()),$row[csf('mpub_shipment_date')])-1).",";
			
			if($bookingup_date=="" || $bookingup_date=="0000-00-00 00:00:00")
			{
				$booking_date=$bookingins_date;
			}
			$WOPreparedAfter.=(datediff('d',$row[csf('insert_date')],$booking_date)-1).",";

			if($row[csf('shiping_status')]==1) {
				$shiping_status.= "FP".",";
				$to_ship++;
				$fp_ship++;
			}
			else if($row[csf('shiping_status')]==2){
				$shiping_status.= "PD".",";
				$to_ship++;
			} 
			else if($row[csf('shiping_status')]==3){
				$shiping_status.= "FS".",";
				$to_ship++;
				$f_ship++;
			} 
        }

        if($to_ship==$f_ship) $shiping_status= "<b style='color:green'>Full shipped</b>";
        else if($to_ship==$fp_ship) $shiping_status= "<b style='color:red'>Full Pending</b>";
        else $shiping_status= "<b style='color:red'>Partial Delivery</b>";
		
		$po_no=implode(",",array_filter(array_unique(explode(",",$po_no))));
		$shipment_date=implode(",",array_filter(array_unique(explode(",",$shipment_date))));
		$lead_time=implode(",",array_filter(array_unique(explode(",",$lead_time))));
		$po_received_date=implode(",",array_filter(array_unique(explode(",",$po_received_date))));
		$factory_received_date=implode(",",array_filter(array_unique(explode(",",$factory_received_date))));
		$grouping=implode(",",array_filter(array_unique(explode(",",$grouping))));
		$file_no=implode(",",array_filter(array_unique(explode(",",$file_no))));
		
		$daysInHand=implode(",",array_filter(array_unique(explode(",",$daysInHand))));
		$WOPreparedAfter=implode(",",array_filter(array_unique(explode(",",$WOPreparedAfter))));
		$shiping_status=implode(",",array_filter(array_unique(explode(",",$shiping_status))));
		
       // foreach ($nameArray as $result)
       // {
            $total_set_qnty=$result[csf('total_set_qnty')];
            $colar_excess_percent=$result[csf('colar_excess_percent')];
            $cuff_excess_percent=$result[csf('cuff_excess_percent')];
            $rmg_process_breakdown=$result[csf('rmg_process_breakdown')];
            
            $booking_percent=$result[csf('booking_percent')];
			$booking_po_id=$result[csf('po_break_down_id')];
			?>
			<table width="100%" border="0" align="left" cellpadding="0"  cellspacing="0" rules="all"  style="border:0px solid black;font-size:18px; font-family:Arial Narrow;" >
				<tr>
					<td width="200"><b>Booking No: </b></td>		 
					<td width="250"> <span style="font-size:18px"><?=str_replace("'","",$txt_booking_no);?></span></td>
					<td width="200"><b>Booking Date:</b></td>		 
					<td width="250"> <span style="font-size:18px"><?echo change_date_format($booking_date);?></span></td>
					<td width="200"><b>Delivery Date:</b></td>		 
					<td width="250"> <span style="font-size:18px"><? echo change_date_format($delivery_date); ?></span></td>

				</tr>
				<tr>
					<td><b>Supplier Name:</b></td>		 
					<td> <span style="font-size:18px"><?
					if($pay_modeId==5 || $pay_modeId==3){
						echo $company_library[$supplier_id];
						}
						else{
						echo $supplier_name_arr[$supplier_id];
						}
				   ?></span> </td>
				   <td><span style="font-size:18px"><b>Supplier Address:</b></span></td>
				   <td><span style="font-size:18px"><? echo $supplier_address_arr[$supplier_id]; ?> </span> </td>
				   <td><b>Attention:</b></td>
				   <td><? echo $attention; ?></td>
			   
			   </tr>
				<tr>		
					<td><span style="font-size:18px"><b>Currency:</b></span></td>
					<td>&nbsp;<span style="font-size:18px"><? echo $currency[$currency_id]; ?></span></td>
					<td><span style="font-size:18px"><b>Conversion Rate:</b></span></td>
					<td>&nbsp;<span style="font-size:18px"><? echo $exchange_rate; ?></span></td>
					<td><span style="font-size:18px"><b>Source:</b></span></td>
					<td>&nbsp;<span style="font-size:18px"><? echo $source[$source_id]; ?></span></td>
				</tr>
				<tr>
					<td><span style="font-size:18px"><b>Remarks</b></span></td>
					<td><span style="font-size:18px"><? echo $remarks;?></span></td>			
					<td><b>Insert User:</b></td>
					<td><? echo $inserted_by2; ?></td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
			</table>
			
			
						
		<?php
		$fabric_desc_arr=array();

		$wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select id,fabric_description,cons_process from wo_pre_cost_fab_conv_cost_dtls  where job_id in($job_no_str)");
		foreach( $wo_pre_cost_fab_conv_cost_dtls_id as $row_wo_pre_cost_fab_conv_cost_dtls_id)
		{
			if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]!=0)
			{
				$fabric_description=sql_select("select id,body_part_id,body_part_type,color_type_id,fabric_description,construction,composition,gsm_weight,width_dia_type from  wo_pre_cost_fabric_cost_dtls 
				where  id='".$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]."' order by body_part_id");
				list($fabric_description_row)=$fabric_description; 
				


				$fabric_desc_arr[$fabric_description_row[csf("id")]]['body_part_id']=$fabric_description_row[csf("body_part_id")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['construction']=$fabric_description_row[csf("construction")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['composition']=$fabric_description_row[csf("composition")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['color_type_id']=$fabric_description_row[csf("color_type_id")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['gsm_weight']=$fabric_description_row[csf("gsm_weight")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['width_dia_type']=$fabric_description_row[csf("width_dia_type")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['body_part_type']=$fabric_description_row[csf("body_part_type")];
				
			}
			if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]==0)
			{
				
			
				$fabric_description=sql_select("select id,body_part_id,body_part_type,color_type_id,fabric_description,construction,composition,gsm_weight,width_dia_type from  wo_pre_cost_fabric_cost_dtls 
				where  job_id in($job_no_str)");

				foreach( $fabric_description as $fabric_description_row)
				{
				

				$fabric_desc_arr[$fabric_description_row[csf("id")]]['body_part_id']=$fabric_description_row[csf("body_part_id")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['construction']=$fabric_description_row[csf("construction")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['composition']=$fabric_description_row[csf("composition")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['color_type_id']=$fabric_description_row[csf("color_type_id")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['gsm_weight']=$fabric_description_row[csf("gsm_weight")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['width_dia_type']=$fabric_description_row[csf("width_dia_type")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['body_part_type']=$fabric_description_row[csf("body_part_type")];
		
				}
				
			}


		}

			// echo "<pre>";
			// print_r($fabric_desc_arr);



		$pre_cons_data=sql_select("select  id, po_break_down_id, color_number_id, gmts_sizes, dia_width, item_size, cons, process_loss_percent, requirment, pcs, color_size_table_id, rate, amount, remarks ,pre_cost_fabric_cost_dtls_id  as fab_desc_id from wo_pre_cos_fab_co_avg_con_dtls where job_id in($job_no_str)  ");


		foreach($pre_cons_data as $row){

			$color_wise_data[$row[csf("po_break_down_id")]][$row[csf("fab_desc_id")]][$row[csf("color_number_id")]]['finsh_cons']=$row[csf("cons")];
			$color_wise_data[$row[csf("po_break_down_id")]][$row[csf("fab_desc_id")]][$row[csf("color_number_id")]]['gray_cons']=$row[csf("requirment")];
			$color_wise_data[$row[csf("po_break_down_id")]][$row[csf("fab_desc_id")]][$row[csf("color_number_id")]]['gray_cons']=$row[csf("requirment")];
			if($row[csf("dia_width")]!="")
			{
			$color_wise_data[$row[csf("po_break_down_id")]][$row[csf("fab_desc_id")]][$row[csf("color_number_id")]]['dia_width']=$row[csf("dia_width")];
			}
			$color_wise_data[$row[csf("po_break_down_id")]][$row[csf("fab_desc_id")]][$row[csf("color_number_id")]]['process_loss_percent']=$row[csf("process_loss_percent")];

		}

		$nameArray_fabric_description= sql_select("SELECT c.id as conv_dtl_id,b.job_no,b.po_break_down_id as po_id,b.sensitivity,b.uom,b.gmts_color_id,b.fabric_color_id,b.gmts_size,b.remark,sum(b.wo_qnty) as wo_qnty,a.style_ref_no,a.buyer_name,d.po_number,d.id,b.option_shade,
		sum(b.amount) as amount,c.charge_unit,c.fabric_description  as fab_desc_id,b.delivery_date,b.delivery_end_date,b.labdip_no,b.rate,b.process_loss_percent	from wo_pre_cost_fab_conv_cost_dtls c,wo_booking_dtls b,wo_po_details_master a,wo_po_break_down d where b.pre_cost_fabric_cost_dtls_id=c.id and b.job_no=c.job_no and b.job_no=a.job_no and b.po_break_down_id=d.id and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 	and d.job_id in($job_no_str) and b.wo_qnty>0	and b.booking_type=3 and b.process=31 and  b.booking_no='$txt_booking_no'  	group by b.job_no,c.id,c.charge_unit,b.sensitivity,b.uom,b.gmts_color_id,b.fabric_color_id,b.gmts_size,c.fabric_description,b.delivery_date,b.delivery_end_date,a.style_ref_no,a.buyer_name, b.po_break_down_id,d.po_number,d.id,b.labdip_no ,b.rate,b.remark,b.process_loss_percent,b.option_shade order by c.fabric_description");

		//echo "<pre>";
		//print_r($nameArray_fabric_description);

	
		$body_part_type_arr=array();
		foreach ($nameArray_fabric_description as $row) {	

			$body_part_id=$fabric_desc_arr[$row[csf("fab_desc_id")]]['body_part_id'];
			$body_part_type=$fabric_desc_arr[$row[csf("fab_desc_id")]]['body_part_type'];
			$construction=$fabric_desc_arr[$row[csf("fab_desc_id")]]['construction'];
			$composition=$fabric_desc_arr[$row[csf("fab_desc_id")]]['composition'];
			$color_type_id=$fabric_desc_arr[$row[csf("fab_desc_id")]]['color_type_id'];
			$gsm_weight=$fabric_desc_arr[$row[csf("fab_desc_id")]]['gsm_weight'];
			$width_dia_type=$fabric_desc_arr[$row[csf("fab_desc_id")]]['width_dia_type'];
			if($body_part_type==40 || $body_part_type==50){
				$body_part_type_arr[$body_part_type]=$body_part_type;
			}


			$finsh_cons=$color_wise_data[$row[csf("po_id")]][$row[csf("fab_desc_id")]][$row[csf("gmts_color_id")]]['finsh_cons'];
			$gray_cons=$color_wise_data[$row[csf("po_id")]][$row[csf("fab_desc_id")]][$row[csf("gmts_color_id")]]['gray_cons'];
			$dia_width=$color_wise_data[$row[csf("po_id")]][$row[csf("fab_desc_id")]][$row[csf("gmts_color_id")]]['dia_width'];
			// $process_loss_percent=$color_wise_data[$row[csf("po_id")]][$row[csf("fab_desc_id")]][$row[csf("gmts_color_id")]]['process_loss_percent'];
		
	
			// $grouping_item=$row[csf('buyer_name')].'*'.$row[csf('job_no')].'*'.$row[csf('po_id')].'*'.$row[csf('style_ref_no')].'*'.$color_type_id.'*'.$body_part_id.'*'.$construction.'*'.$composition.'*'.$row[csf('fabric_color_id')];	

			$grouping_item=$row[csf('buyer_name')].'*'.$row[csf('job_no')].'*'.$row[csf('po_id')].'*'.$row[csf('style_ref_no')];
			$grouping_item2=$color_type_id.'*'.$body_part_id.'*'.$construction.'*'.$composition.'*'.$row[csf('fabric_color_id')];

				$pp=100+$process_loss_percent;
			$fabric_data_arr[$grouping_item][$grouping_item2]['gmts_color_id'] = $row[csf('gmts_color_id')];
			$fabric_data_arr[$grouping_item][$grouping_item2]['style_ref_no'] = $row[csf('style_ref_no')];
			$fabric_data_arr[$grouping_item][$grouping_item2]['po_id'] = $row[csf('po_id')];
			$fabric_data_arr[$grouping_item][$grouping_item2]['job_no'] = $row[csf('job_no')];
			$fabric_data_arr[$grouping_item][$grouping_item2]['po_number'] = $row[csf('po_number')];
			$fabric_data_arr[$grouping_item][$grouping_item2]['fabric_color_id'] = $row[csf('fabric_color_id')];
			$fabric_data_arr[$grouping_item][$grouping_item2]['labdip_no'][$row[csf('labdip_no')]] = $row[csf('labdip_no')];
			$fabric_data_arr[$grouping_item][$grouping_item2]['option_shade'][$row[csf('option_shade')]] = $row[csf('option_shade')];
			$fabric_data_arr[$grouping_item][$grouping_item2]['body_part_id'] = $body_part_id;
			$fabric_data_arr[$grouping_item][$grouping_item2]['composition'] = $composition;
			$fabric_data_arr[$grouping_item][$grouping_item2]['construction'] = $construction;
			$fabric_data_arr[$grouping_item][$grouping_item2]['gsm'] = $gsm_weight;
			$fabric_data_arr[$grouping_item][$grouping_item2]['dia_width'] = $dia_width;
			$fabric_data_arr[$grouping_item][$grouping_item2]['dia'] = $fabric_typee[$width_dia_type];
			$fabric_data_arr[$grouping_item][$grouping_item2]['color_type_id'] = $color_type_id;
			$fabric_data_arr[$grouping_item][$grouping_item2]['finsh_cons'] = $finsh_cons;
			$fabric_data_arr[$grouping_item][$grouping_item2]['gray_cons'] = $gray_cons;
			$fabric_data_arr[$grouping_item][$grouping_item2]['fin_fab_qnty'] +=$row[csf('wo_qnty')]-(($row[csf('wo_qnty')]*$row[csf('process_loss_percent')])/100);
			$fabric_data_arr[$grouping_item][$grouping_item2]['grey_fab_qnty'] += $row[csf('wo_qnty')];
			$fabric_data_arr[$grouping_item][$grouping_item2]['amount'] = $row[csf('amount')];
			$fabric_data_arr[$grouping_item][$grouping_item2]['rate'] = $row[csf('rate')];
			$fabric_data_arr[$grouping_item][$grouping_item2]['remark'] = $row[csf('remark')];
			$fabric_data_arr[$grouping_item][$grouping_item2]['buyer_name'] = $row[csf('buyer_name')];
			$fabric_data_arr[$grouping_item][$grouping_item2]['process_loss_percent'] = $row[csf('process_loss_percent')];

			if($row[csf('delivery_date')]){
				$fabric_data_arr[$row[csf('labdip_no')]][$grouping_item]['delivery_start_date'] = $row[csf('delivery_date')];
			}
			if($row[csf('delivery_end_date')]){			
				$fabric_data_arr[$row[csf('labdip_no')]][$grouping_item]['delivery_end_date'] = $row[csf('delivery_end_date')];
			}
			
	
		}

		$body_part_type_ids=implode(",",$body_part_type_arr);
	
		?>
		<p>&nbsp;</p>
		 <table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all"  style="font-size: 18px;">
			 <tr>	
			 	 <th>Buyer Name</th>
				 <th>Job No</th>
				 <th>Style Ref</th>
				 <th>Order No</th>
				 <th>Body Part</th>			
				 
				 <th>Construction</th>
				 <th>Composition</th>	
				 <th>LapDip</th>	
				 <th>Opt/Shade</th>				 						 
				 <th>Colour</th>	
				 <th>GSM</th>
				 <th>Dia Type </th>
				 <th>Dia</th>			
				 <th>Grey Kg</th>	
				 <th>P.Loss%</th>
				 <th>Finish Kg</th>			
				 <th>Rate</th>
				 <th>Amount</th>
				 <th>Remark</th>

			 </tr>
			 <? 
			 foreach ($fabric_data_arr as $jobPoData=>$poData) { 
				foreach ($poData as $fabric_id => $value) { 

					$po_wise_rowspan[$jobPoData]+=1;
				}
			 }

			
					 	
			 $sub_fin_fab_qnty=0;   		 	
			 $sub_grey_fab_qnty=0;
			 $sub_amount=0;
				 foreach ($fabric_data_arr as $jobPoData => $poData) {	
					$i=1;
					foreach ($poData as $fabric_id => $value) {	
					$sub_fin_fab_qnty+=$value['fin_fab_qnty'];   		 	
					$sub_grey_fab_qnty+=$value['grey_fab_qnty']; 
					$rate=$value['rate'];
					$sub_amount+=$rate*$value['grey_fab_qnty'];

						   ?>
						  <tr>
							<?
						  if($i==1){?>
							 <td style="font-size: 16px;" align="center" rowspan="<?=$po_wise_rowspan[$jobPoData]; ?>"><? echo  $buyer_name_arr[$value['buyer_name']]?></td>
							 <td style="font-size: 16px;" align="center" rowspan="<?=$po_wise_rowspan[$jobPoData]; ?>"><? echo $value['job_no'] ?></td>
							 <td style="font-size: 16px;" align="center" rowspan="<?=$po_wise_rowspan[$jobPoData]; ?>"><? echo $value['style_ref_no'] ?></td>
							 <td style="font-size: 16px;" align="center" rowspan="<?=$po_wise_rowspan[$jobPoData]; ?>"><? echo $value['po_number'] ?></td>	
							 <?$i++;}?>
							 <td style="font-size: 16px;" align="center"><? echo $body_part[$value['body_part_id']] ?></td>
							 	
							 <td style="font-size: 16px;" align="center"><? echo $value['construction'] ?></td>
							 <td style="font-size: 16px;" align="center"><? echo $value['composition'] ?></td>
							 <td style="font-size: 16px;" align="center"><? echo implode(",",$value[('labdip_no')]); ?></td>
							 <td style="font-size: 16px;" align="center"><? echo implode(",",$value[('option_shade')]); ?></td>
							 <td style="font-size: 16px;" align="center"><? echo $color_library[$value['fabric_color_id']] ?></td>		
							 <td style="font-size: 16px;" align="center"><? echo $value['gsm'] ?></td>							 
							 <td style="font-size: 16px;" align="center"><? echo $value['dia'] ?></td>
							 <td style="font-size: 16px;" align="center"><? echo $value['dia_width'] ?></td>
							 <td style="font-size: 16px;" align="right"><? echo fn_number_format($value['grey_fab_qnty'],2) ; ?></td>
							 <td style="font-size: 16px;" align="right"><? echo $value['process_loss_percent'] ; ?></td>	
							 <td style="font-size: 16px;" align="right"><? echo fn_number_format($value['fin_fab_qnty'],2) ; ?></td>						
							 <td style="font-size: 16px;" align="right"><? echo fn_number_format($rate,2); ?></td>
							 <td style="font-size: 16px;" align="right"><? echo fn_number_format($rate*$value['grey_fab_qnty'],2) ; ?></td>
							 <td style="font-size: 16px;" align="center"><? echo $value['remark'] ?></td>	

						 </tr>
						  <?// } 
						 // else {
							?>
							  <!-- <tr>
							 <td style="font-size: 16px;" align="center"><? echo $body_part[$value['body_part_id']] ?></td>	
							 
							 <td style="font-size: 16px;" align="center"><? echo $value['construction'] ?></td>
							 <td style="font-size: 16px;" align="center"><? echo $value['composition'] ?></td>					 			
							 <td style="font-size: 16px;" align="center"><? echo $color_library[$value['fabric_color_id']] ?></td>					 
							 <td style="font-size: 16px;" align="center"><? echo $value['gsm'] ?></td>
							 <td style="font-size: 16px;" align="center"><? echo $value['dia'] ?></td>
							 <td style="font-size: 16px;" align="center"><? echo $value['dia_width'] ?></td>
							 <td style="font-size: 16px;" align="right"><? echo fn_number_format($value['grey_fab_qnty'],2) ; ?></td>
							 <td style="font-size: 16px;" align="right"><? echo $value['process_loss_percent'] ; ?></td>	
							 <td style="font-size: 16px;" align="right"><? echo fn_number_format($value['fin_fab_qnty'],2) ; ?></td>							
							 <td style="font-size: 16px;" align="right"><? echo fn_number_format($rate,2); ?></td>
							 <td style="font-size: 16px;" align="right"><? echo fn_number_format($rate*$value['grey_fab_qnty'],2) ; ?></td>
							 <td style="font-size: 16px;" align="center"><? echo $value['remark'] ?></td>	
							 </tr> -->
							
						  <?// }
						  $i++;
				 }}
				 ?>
				 <tr>
				 <th align="right" colspan="13"> Sub Total</th>
				 <th align="right"><?echo number_format($sub_grey_fab_qnty,2);  ?></th>	
				 <th></th>
				 <th align="right"><?echo number_format($sub_fin_fab_qnty,2);  ?></th>					 
				 <th></th>
				 <th align="right"><?echo number_format($sub_amount,2);  ?></th>
				 <th></th>
				 </tr>
				 <?
				 	$fin_fab_qnty+=	$sub_fin_fab_qnty;   		 	
					$grey_fab_qnty+=$sub_grey_fab_qnty;
					$amount+=$sub_amount;
					?>
				 
			 <?
			 //} 
			 ?>
			 <tr>
				 <th align="right" colspan="13"> Grand Total</th>
				 <th align="right"><?echo number_format($grey_fab_qnty,2);  ?></th>			
				 <th></th>				 
				 <th align="right"><?echo number_format($fin_fab_qnty,2);  ?></th>		
				 <th></th>
				 <th align="right"><?echo number_format($amount,2);  ?></th>
				 <th></th>
			</tr>
			<tr>
			<th colspan="19" align="left">Total Qty (in word):<? echo str_replace("-"," ",number_to_words(def_number_format($fin_fab_qnty,2,""))); ?></th>
			</tr>
			<tr>
				<th colspan="19" align="left">Grand Total Booking Amount (in word):<? echo str_replace("-"," ",number_to_words(def_number_format($amount,2,""),$currency[$currency_id],$paysa_sent)); ?></th>
				
			</tr>
		</table>
		  <br/>



      	<!--  Here will be the main portion  -->
		<?
		//$job_no_strArr[$row[csf('job_id')]]=$row[csf('job_id')];
		//	$job_no_Arr[$row[csf('job_no')]]=$row[csf('job_no')];
			
        $costing_per=""; $costing_per_qnty=0; //
        $costing_per_id=return_field_value( "costing_per", "wo_pre_cost_mst","job_id in(".implode(",",$job_no_strArr).")");
        if($costing_per_id==1)
        {
			$costing_per="1 Dzn";
			$costing_per_qnty=12;
        }
        if($costing_per_id==2)
        {
			$costing_per="1 Pcs";
			$costing_per_qnty=1;
        }
        if($costing_per_id==3)
        {
			$costing_per="2 Dzn";
			$costing_per_qnty=24;
        }
        if($costing_per_id==4)
        {
			$costing_per="3 Dzn";
			$costing_per_qnty=36;
        }
        if($costing_per_id==5)
        {
			$costing_per="4 Dzn";
			$costing_per_qnty=48;
        }




      
		
		?>
        <br/>
        

       		
        <br/>
        
		<fieldset>
                   
			<table  width="100%" class="rpt_table" style="margin:5px;font-size:16px;font-family:'Arial Narrow';"   border="1" cellpadding="0" cellspacing="0">
				<tr>
				<td><? echo get_spacial_instruction($txt_booking_no); ?></td>
				</tr>
			</table>
			</fieldset>       
        <div ><? echo signature_table(290, $cbo_company_name, "1400px",'',40,$inserted_by2);  //signature_table(1, $cbo_company_name, "1400px"); //$user_name_arr[$user_id] ?></div>
		<br>
		    
       </div>
       <?
	$emailBody=ob_get_contents();
	//ob_clean();
	if($is_mail_send==1){
		/*$req_approved=return_field_value("id", "ELECTRONIC_APPROVAL_SETUP", "PAGE_ID = 336 and is_deleted=0");
		$is_approved=return_field_value("IS_APPROVED", "WO_BOOKING_MST", "STATUS_ACTIVE = 1 and is_deleted=0 and booking_no='$txt_booking_no'");
		if($req_approved && $is_approved==1){
			$emailBody.="<h1 style='border:1px sloid #0ff;'>Approved</h1>";
		}
		elseif($req_approved && $is_approved==0){
			$emailBody.="<h1 style='border:1px sloid #0ff;'>Draft</h1>";
		}
	*/		
		
		$sql = "SELECT c.email_address as EMAIL FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=87 and b.mail_user_setup_id=c.id and a.company_id =$cbo_company_name";
		$mail_sql_res=sql_select($sql);
		
		$mailArr=array();
		foreach($mail_sql_res as $row)
		{
			$mailArr[$row[EMAIL]]=$row[EMAIL]; 
		}
		
		$supplier_id=$nameArray[0][csf('supplier_id')];
		$supplier_mail=return_field_value("email", "lib_supplier", "status_active=1 and is_deleted=0 and id=$supplier_id ");

		
		$mailArr=array();
		if($mail_id!=''){$mailArr[]=$mail_id;}
		if($supplier_mail_arr[$supplier_id]!=''){$mailArr[]=$supplier_mail;}
		
		$to=implode(',',$mailArr);
		$subject="Fabric Booking Auto Mail";
		
		if($to!=""){
			require '../../../vendor/phpmailer/phpmailer/PHPMailerAutoload.php';
			require_once('../../../auto_mail/setting/mail_setting.php');
			$header=mailHeader();
			sendMailMailer( $to, $subject, $emailBody );
		}
	}
	exit();
}
if($action=="show_trim_booking_report6") //MD. SAKIBUL ISLAM 7639 08 May 2023
{

	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	//$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library",'master_tble_id','image_location');
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	//$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$color_library=return_library_array( "select id,color_name from lib_color  where status_active=1 and is_deleted=0", "id", "color_name");
	$lib_body_part=return_library_array( "select id,body_part_full_name from lib_body_part  where status_active=1 and is_deleted=0", "id", "body_part_full_name");
	//$fabric_color_sql=sql_select("SELECT master_tble_id, image_location from common_photo_library where is_deleted=0 and file_type=1 ");
	// $fabric_ima_arr=return_library_array( "select master_tble_id,image_location from common_photo_library  where is_deleted=0 and file_type=1 and form_name='aop_v2' ", "master_tble_id", "image_location");
	$fabric_ima_lib=return_library_array( "select master_tble_id,image_location from common_photo_library  where is_deleted=0 and file_type=1 and form_name='aop_v2' ", "master_tble_id", "image_location");
	$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name"  );
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$trim_group= return_library_array( "select id, item_name from lib_item_group",'id','item_name');
	
	$path=($path=='')?'../../':$path;

	// echo "<pre>";
	// print_r($fabric_ima_lib);
	foreach($fabric_ima_lib as $img_id=>$row){

			$img_id_arr=explode(",",$img_id);
			foreach($img_id_arr as $val){
				$fabric_ima_arr[$val]=$row;

			}
	}

	// echo "<pre>";
	// print_r($fabric_ima_arr);
	?>
	<div style="width:1150px" align="left">
       <table width="100%" cellpadding="0" cellspacing="0" style="border:0px solid black;margin:5px; font-size:16px; font-family:'Arial Narrow';" >
           <tr>
               <td width="100">
               <img  src='<? echo $path.$imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
               </td>
               <td width="">
                    <table width="100%" cellpadding="0" cellspacing="0" >
                        <tr>
                            <td align="center" style="font-size:20px;">
                              <?php
								echo $company_library[$cbo_company_name];
								?>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:14px;">
                            <?
                            $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website,contact_no from lib_company where id=$cbo_company_name");
                            foreach ($nameArray as $result)
                            {
                            ?>
                                             <? echo $result[csf('plot_no')]; ?>
                                            <? echo $result[csf('level_no')]?>
                                            <? echo $result[csf('road_no')]; ?>
                                            <? echo $result[csf('block_no')];?>
                                            <? echo $result[csf('city')];?>
                                            <? echo $result[csf('zip_code')]; ?>
                                            <?php echo $result[csf('province')]; ?>
                                            <? echo $country_arr[$result[csf('country_id')]]; ?><br>
                                            Email Address: <? echo $result[csf('email')];?>
                                            Website No: <? echo $result[csf('website')];

									$email=$result[csf('email')];
									$contact_no=$result[csf('contact_no')];
									$website=$result[csf('website')];
									$city=$result[csf('city')];
									$road_no=$result[csf('road_no')];
									$block_no=$result[csf('block_no')];
									$com_add=$result[csf('plot_no')].' '.$result[csf('level_no')].' '.$result[csf('road_no')].' '.$result[csf('block_no')].' '.$city.' '.$result[csf('zip_code')];
                            }
                            ?>
                               </td>
                            </tr>
                            <tr>
                            <td align="center" style="font-size:20px">
                            <strong> Service Booking For Dyeing:<? echo str_replace("'","",$txt_booking_no); ?></strong>
                             </td>
                            </tr>
                      </table>
                </td>
            </tr>
       </table>
		<?
		$yarn_count_arr=return_library_array( "select id,yarn_count from lib_yarn_count",'id','yarn_count');
		$nameArray_count=sql_select(" SELECT a.job_no, d.fabric_description,a.fin_dia,a.fabric_color_id,f.count_id from wo_booking_dtls a join wo_po_break_down b on a.po_break_down_id=b.id join wo_pre_cost_fab_conv_cost_dtls c on c.id=a.pre_cost_fabric_cost_dtls_id join 
		wo_pre_cost_fabric_cost_dtls d on c.fabric_description=d.id join wo_po_details_master e on e.id=b.job_id   JOIN wo_pre_cost_fab_yarn_cost_dtls f on e.id = f.job_id where 
		a.booking_no=$txt_booking_no and a.entry_form_id=229 and a.status_active=1 group by a.job_no, d.fabric_description,a.fin_dia,a.fabric_color_id,f.count_id");
		$fab_arr=array('fabric_description','dia_width','fin_dia','fin_gsm','color_type_id','gsm_weight');
		foreach($nameArray_count as $row){
			$main_data_arr[$row[csf('job_no')]][$row[csf('fabric_description')]][$row[csf('fin_dia')]][$row[csf('fabric_color_id')]]['count_id'].=$yarn_count_arr[$row[csf('count_id')]].",";	
			$fabric_color_summary[$row[csf('fabric_color_id')]][$row[csf('fabric_description')]][$row[csf('fin_dia')]]['count_id'].=$yarn_count_arr[$row[csf('count_id')]].",";
		}
		$nameArray_job=sql_select(" SELECT b.id as po_id,b.po_number,a.dia_width,a.fin_dia,a.fabric_color_id,a.fin_gsm,a.gmts_color_id,a.fabric_color_id,a.job_no,a.description,a.program_no,a.lib_composition,a.delivery_date, a.delivery_end_date,a.pre_cost_fabric_cost_dtls_id as fab_dtls_id,a.sensitivity,a.rate,a.uom,a.amount,	a.wo_qnty,a.grey_fab_qnty, d.body_part_id, e.style_ref_no , e.id as job_id, d.fabric_description, d.color_type_id, d.id as fabric_id, d.lib_yarn_count_deter_id ,d.gsm_weight ,a.artwork_no,a.printing_color_id,a.id from wo_booking_dtls a join wo_po_break_down b on a.po_break_down_id=b.id join wo_pre_cost_fab_conv_cost_dtls c on c.id=a.pre_cost_fabric_cost_dtls_id join 
		wo_pre_cost_fabric_cost_dtls d on c.fabric_description=d.id join wo_po_details_master e on e.id=b.job_id  where 
		a.booking_no=$txt_booking_no and a.entry_form_id=229 and a.status_active=1 group by b.id ,b.po_number, a.dia_width,a.fin_dia, a.fabric_color_id, a.fin_gsm, a.gmts_color_id, a.fabric_color_id, a.job_no, a.description, a.program_no, a.lib_composition, a.delivery_date, a.delivery_end_date, a.pre_cost_fabric_cost_dtls_id , a.sensitivity, a.rate, a.uom, a.amount, a.wo_qnty,a.grey_fab_qnty, d.body_part_id, e.style_ref_no,e.id,d.fabric_description, d.color_type_id, d.id, d.lib_yarn_count_deter_id,d.gsm_weight,a.artwork_no,a.printing_color_id,a.id");
		
		$fabric_atribute_arr=array('fabric_description','dia_width','fin_dia','fin_gsm','color_type_id','gsm_weight');
		$fabric_color_attr=array('fabric_color_id','uom','rate','fabric_id','artwork_no','printing_color_id','id');
		$fabric_color_summary_attr=array('fabric_description');
			
		foreach($nameArray_job as $row){
			
			$sensitivity_prog_arr[$row[csf('job_id')]]['job_no']=$row[csf('job_no')];
			$sensitivity_prog_arr[$row[csf('job_id')]]['style_ref']=$row[csf('style_ref_no')];
			$sensitivity_prog_arr[$row[csf('job_id')]]['po_no'][$row[csf('po_id')]]=$row[csf('po_number')];
			foreach($fabric_atribute_arr as $fabattr){
				$sensitivity_prog_arr[$row[csf('job_id')]]['fabric_data'][$row[csf('fab_dtls_id')]][$row[csf('fin_dia')]][$fabattr]=$row[csf($fabattr)];
			}
			foreach($fabric_color_attr as $fcolorattr){
				$sensitivity_prog_arr[$row[csf('job_id')]]['fabric_data'][$row[csf('fab_dtls_id')]][$row[csf('fin_dia')]]['color_dtls'][$row[csf('fabric_color_id')]][$fcolorattr]=$row[csf($fcolorattr)];				
			}
			$sensitivity_prog_arr[$row[csf('job_id')]]['fabric_data'][$row[csf('fab_dtls_id')]][$row[csf('fin_dia')]]['color_dtls'][$row[csf('fabric_color_id')]]['wo_qnty']+=$row[csf('wo_qnty')];
			$sensitivity_prog_arr[$row[csf('job_id')]]['fabric_data'][$row[csf('fab_dtls_id')]][$row[csf('fin_dia')]]['color_dtls'][$row[csf('fabric_color_id')]]['grey_fab_qnty']+=$row[csf('grey_fab_qnty')];
			$sensitivity_prog_arr[$row[csf('job_id')]]['fabric_data'][$row[csf('fab_dtls_id')]][$row[csf('fin_dia')]]['color_dtls'][$row[csf('fabric_color_id')]]['amount']+=$row[csf('amount')];
		


			$job_po_arr[$row[csf('job_no')]][$row[csf('po_number')]]=$row[csf('po_number')];

			$main_data_arr[$row[csf('job_no')]][$row[csf('fabric_description')]][$row[csf('fin_dia')]][$row[csf('fabric_color_id')]]['wo_qnty']+=$row[csf('wo_qnty')];
			$main_data_arr[$row[csf('job_no')]][$row[csf('fabric_description')]][$row[csf('fin_dia')]][$row[csf('fabric_color_id')]]['grey_fab_qnty']+=$row[csf('grey_fab_qnty')];
			$main_data_arr[$row[csf('job_no')]][$row[csf('fabric_description')]][$row[csf('fin_dia')]][$row[csf('fabric_color_id')]]['artwork_no']=$row[csf('artwork_no')];		
			$main_data_arr[$row[csf('job_no')]][$row[csf('fabric_description')]][$row[csf('fin_dia')]][$row[csf('fabric_color_id')]]['printing_color_id']=$row[csf('printing_color_id')];
			$main_data_arr[$row[csf('job_no')]][$row[csf('fabric_description')]][$row[csf('fin_dia')]][$row[csf('fabric_color_id')]]['uom']=$row[csf('uom')];	
			$main_data_arr[$row[csf('job_no')]][$row[csf('fabric_description')]][$row[csf('fin_dia')]][$row[csf('fabric_color_id')]]['dia_width']=$row[csf('dia_width')];
			$main_data_arr[$row[csf('job_no')]][$row[csf('fabric_description')]][$row[csf('fin_dia')]][$row[csf('fabric_color_id')]]['rate']=$row[csf('rate')];	
			$main_data_arr[$row[csf('job_no')]][$row[csf('fabric_description')]][$row[csf('fin_dia')]][$row[csf('fabric_color_id')]]['id']=$row[csf('id')];	
			//$main_data_arr[$row[csf('job_no')]][$row[csf('fabric_description')]][$row[csf('fin_dia')]][$row[csf('fabric_color_id')]]['count_id'].=$yarn_count_arr[$row[csf('count_id')]].",";	
			$main_data_arr[$row[csf('job_no')]][$row[csf('fabric_description')]][$row[csf('fin_dia')]][$row[csf('fabric_color_id')]]['color_type_id']=$row[csf('color_type_id')];	
			$main_data_arr[$row[csf('job_no')]][$row[csf('fabric_description')]][$row[csf('fin_dia')]][$row[csf('fabric_color_id')]]['gsm_weight']=$row[csf('gsm_weight')];
			$main_data_arr[$row[csf('job_no')]][$row[csf('fabric_description')]][$row[csf('fin_dia')]][$row[csf('fabric_color_id')]]['job_no']=$row[csf('job_no')];	
			$main_data_arr[$row[csf('job_no')]][$row[csf('fabric_description')]][$row[csf('fin_dia')]][$row[csf('fabric_color_id')]]['style_ref']=$row[csf('style_ref_no')];	
			$main_data_arr[$row[csf('job_no')]][$row[csf('fabric_description')]][$row[csf('fin_dia')]][$row[csf('fabric_color_id')]]['amount']+=$row[csf('amount')];

			$fabric_color_summary[$row[csf('fabric_color_id')]][$row[csf('fabric_description')]][$row[csf('fin_dia')]]['dia_width']=$row[csf('dia_width')];
			$fabric_color_summary[$row[csf('fabric_color_id')]][$row[csf('fabric_description')]][$row[csf('fin_dia')]]['fabric_color_id']=$row[csf('fabric_color_id')];
			$fabric_color_summary[$row[csf('fabric_color_id')]][$row[csf('fabric_description')]][$row[csf('fin_dia')]]['fabric_description']=$row[csf('fabric_description')];
			//$fabric_color_summary[$row[csf('fabric_color_id')]][$row[csf('fabric_description')]][$row[csf('fin_dia')]]['count_id']=$yarn_count_arr[$row[csf('count_id')]].",";
			$fabric_color_summary[$row[csf('fabric_color_id')]][$row[csf('fabric_description')]][$row[csf('fin_dia')]]['gsm_weight']=$row[csf('gsm_weight')];
			$fabric_color_summary[$row[csf('fabric_color_id')]][$row[csf('fabric_description')]][$row[csf('fin_dia')]]['fin_dia']=$row[csf('fin_dia')];
			$fabric_color_summary[$row[csf('fabric_color_id')]][$row[csf('fabric_description')]][$row[csf('fin_dia')]]['wo_qnty']+=$row[csf('wo_qnty')];
			$fabric_color_summary2[$row[csf('fabric_color_id')]]['tot_qnty']+=$row[csf('wo_qnty')];			
		}

		   /* echo "<pre>";
		 print_r($fabric_color_summary);  die;  */
		
	
		// 	echo "<pre>";
		// print_r($job_wise_rowspan);
		$suppliar_data=sql_select("SELECT id, contact_no, email,web_site, address_1, address_2, address_3, address_4 from lib_supplier where status_active=1 and is_deleted=0");
		foreach($suppliar_data as $row){
			$supplier_address_arr[$row[csf('id')]]['address']=$row[csf('address_1')].' '.$row[csf('address_2')].' '.$row[csf('address_3')].' '.$row[csf('address_4')];
			$supplier_address_arr[$row[csf('id')]]['contact']=$row[csf('contact_no')];
			$supplier_address_arr[$row[csf('id')]]['email']=$row[csf('email')];
			$supplier_address_arr[$row[csf('id')]]['website']=$row[csf('web_site')];
		}
        $nameArray=sql_select( "select a.booking_no,a.booking_date,a.pay_mode,a.remarks,a.buyer_id,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.source, a.delivery_to, a.attention, a.tenor from wo_booking_mst a where  a.booking_no=$txt_booking_no and a.entry_form=229");

        foreach ($nameArray as $result)
        {
			$varcode_booking_no=$result[csf('booking_no')];
			$pay_mode=$result[csf('pay_mode')];$supplier_id=$result[csf('supplier_id')];
			$supp_address=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website,contact_no from lib_company where id=$supplier_id");
			if($pay_mode==5 || $pay_mode==3){

				$com_supp=$company_library[$supplier_id];
				// $suplier_address=$com_add.'<br> '.$email.'<br> '.$website;
				$suplier_address=$supp_address[0][csf('plot_no')]."-".$supp_address[0][csf('level_no')].",".$supp_address[0][csf('road_no')].",".$supp_address[0][csf('block_no')]."<br>".$supp_address[0][csf('city')].",".$supp_address[0][csf('zip_code')].",".$country_arr[$supp_address[0][csf('country_id')]]."<br>Email Address:".$supp_address[0][csf('email')]."<br>".$supp_address[0][csf('website')];
				
			}
			else{

				$com_supp=$supplier_name_arr[$supplier_id];
				$suplier_address=$supplier_address_arr[$supplier_id]['address'].'<br>TEL:'.$supplier_address_arr[$supplier_id]['contact'].'<br>Email:'.$supplier_address_arr[$supplier_id]['email'].'<br>'.$supplier_address_arr[$supplier_id]['website'];
			}
			$currency_id=$result[csf('currency_id')];

        ?>
		<div style="width:1150px;">
       	<table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all">
            <tr>
                <th colspan="6" valign="top" align="center">Beneficiary</th>
				<th colspan="6" valign="top" align="center">Consignee</th>
            </tr>
			<tr>
                <td width="50%" style="font-size:16px" colspan="6" align="left">
				<strong>&nbsp;<? echo $com_supp;?></strong><br>
				<? echo $suplier_address;?>
				</td>
				<td width="50%" style="font-size:16px" colspan="6" align="left">
				<strong>&nbsp;<? echo $company_library[$cbo_company_name];?></strong><br>
				<? echo $com_add;?><br>			
				<? echo "TEL:".$contact_no;  ?><br>
				<? echo "Email:".$email;  ?><br>
				<? echo $website;  ?><br>
				</td>

			</tr>
			<tr>
				<th align="left">Issue Date</th>
				<td>&nbsp;<? echo change_date_format($result[csf('booking_date')]);?></td>
				<th align="left">Delivery Date</th>
				<td>&nbsp;<? echo change_date_format($result[csf('delivery_date')]);?></td>
				<th align="left">Contact Person</th>
				<td>&nbsp;<? echo $result[csf('attention')];?></td>
				<th align="left">Buyer</th>
				<td>&nbsp;<? echo $buyer_name_arr[$result[csf('buyer_id')]];?></td>
				<th align="left">Tenor</th>
				<td>&nbsp;<? echo $result[csf('tenor')];?></td>
			</tr>
			<tr>
				<th align="left">Delivery Address</th>
				<td colspan="7">&nbsp;<? echo $result[csf('delivery_to')];?></td>
				<th align="left">Currency</th>
				<td>&nbsp;<? echo $currency[$currency_id];?></td>
			</tr>
			<tr>
				<th align="left">Remarks</th>
				<td colspan="9">&nbsp;<? echo $result[csf('remarks')];?></td>
			</tr>			
        </table>
		<?
        }
        ?>
		<br>
		<br>
		<table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all">
			<tr>
				<th width="100">Job No</th>
				<th width="100">Style Ref</th>
				<th width="100">PO NO</th>
				<th width="100">Yarn Count</th>
				<th width="250">Fab Description</th>
				<th width="100">Fab Color</th>
				<th width="80">GSM</th>
				<th width="60">Fab Dia</th>
				<th width="160">Color Type</th>
				<th width="60">Grey Fab Qty</th>
				<? if($show_comments==1){?>
				<th width="60">Rate</th>
				<th width="100">Amount</th>
				<?}?>
			</tr>
			<? 
			foreach($main_data_arr as $job_id=>$job_data){
				$job_rowspan=0;
					foreach($job_data as $desc_id=>$gsm_data){
						$desc_rowspan=0;
						foreach($gsm_data as $dia_id=>$dia_data){
							$dia_rowspan=0;
							foreach($dia_data as $color_id=>$row){

								$job_rowspan++;
								$desc_rowspan++;
								$dia_rowspan++;

							}
							$job_id_arr[$job_id]=$job_rowspan;
							$desc_id_arr[$job_id][$desc_id]=$desc_rowspan;
							$dia_id_arr[$job_id][$desc_id][$dia_id]=$dia_rowspan;
						}

					}
				
			}

					// echo "<pre>";
					// print_r($rowc);

		foreach($main_data_arr as $job_id=>$job_data){
			$j=1;
				foreach($job_data as $desc_id=>$gsm_data){
					$fab=1;
					foreach($gsm_data as $dia_id=>$dia_data){
						$d=1;
						foreach($dia_data as $color_id=>$row){
						
						
					 	?>
					<tr>
					<?
							if($j==1){?>
							<td rowspan="<?=$job_id_arr[$job_id];?>"><?= $job_id  ?></td>
							<td rowspan="<?=$job_id_arr[$job_id];?>"><?= $row['style_ref']  ?></td>
							<td rowspan="<?=$job_id_arr[$job_id];?>"><?= implode(", ", $job_po_arr[$job_id])  ?></td>
							<td rowspan="<?=$job_id_arr[$job_id];?>"><?= rtrim($row['count_id'],",")  ?></td>
							<? 

							}
							
							if($fab==1){
							?>
							<td rowspan="<?=$desc_id_arr[$job_id][$desc_id];?>"><?= $desc_id;  ?></td>
							<?}?>								
							<td><?= $color_library[$color_id];  ?></td>
							<td><?= $row['gsm_weight'];  ?></td>	
												
							<td><?= $row['dia_width'];  ?></td>								
							<td><?= $color_type[$row['color_type_id']];  ?></td>
							<td align="right"><?= number_format($row['wo_qnty'],2)  ?></td>
							<? if($show_comments==1){?>							
							<td align="right"><?= $row['rate']  ?></td>
							<td align="right"><?= number_format($row['amount'],2)  ?></td>
							<?
								$color_wise_amount+=$row['amount'];
								$color_wise_grand_amount+=$row['amount'];
							}?>
							<? 
							$colortr++;
							$color_wise_qty+=$row['wo_qnty'];
							
							$color_wise_grand_qty+=$row['wo_qnty'];
						
							 ?>
							
							
						<? 
							$fab++;
							$f2++;$f++;$j++;
						
						?>						
					</tr>
				<? } }}}

				if($currency_id==1){$paysa_sent="Paisa";} else if($currency_id==2){$paysa_sent="CENTS";}
			?>
				<tr>
				<th colspan="9" align="right">Grand Total</th>
				<th align="right"><?= number_format($color_wise_grand_qty,2) ?></th>
				<? if($show_comments==1){?>
				<th></th>
				<th align="right"><?= number_format($color_wise_grand_amount,2) ?></th>
				<?}?>
			</tr>
			<tr>
				<th colspan="3" align="left">Total Booking Amount (in word)</th>
				<th colspan="8" align="left"><? echo number_to_words(def_number_format($color_wise_grand_amount,2,""),$currency[$currency_id],$paysa_sent); ?></th>
			</tr>
		</table>
		<br><br>
		<table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all">
			<tr><th colspan="7" align="center">Color Wise Summary</th></tr>
			<tr>
				<th>Fab. Color</th>
				<th>Yarn Count</th>
				<th>Fabrication</th>
				<th>GSM</th>
				<th>Dia</th>
				<th>Quantity</th>
				<th>Color Wise Total</th>
			</tr>
			<? 

		foreach($fabric_color_summary as $color_id=>$color_data){
			$color_rowspan=0;
			foreach($color_data as $desc_id=>$gsm_data){
				$desc_rowspan=0;
				foreach($gsm_data as $dia_id=>$row){
			
					$color_rowspan++;
					$desc_rowspan++;

				}
				$color_id_arr[$color_id]=$color_rowspan;
				$desc_id_arr[$color_id][$desc_id]=$desc_rowspan;
			}}

			foreach($fabric_color_summary as $color_id=>$color_data){
				$j=1;
				$color_wise_qty_summ=0;	
					foreach($color_data as $desc_id=>$gsm_data){
						$fab=1;
						foreach($gsm_data as $dia_id=>$row){
	
							$total_qty=	$fabric_color_summary2[$color_id]['tot_qnty'];
							 ?>
						<tr>
						<?
								if($j==1){?>
								<td rowspan="<?=$color_id_arr[$color_id];?>"><?= $color_library[$color_id]  ?></td>
								<td rowspan="<?=$color_id_arr[$color_id];?>"><?= rtrim($row['count_id'],",")  ?></td>
								<? 
	
								}
								
								if($fab==1){
								?>
								<td rowspan="<?=$desc_id_arr[$color_id][$desc_id];?>"><?= $desc_id;  ?></td>
								<?}?>								
								<td><?= $row['gsm_weight'];  ?></td>	
													
								<td><?= $row['dia_width'];  ?></td>								
								<td align="right"><?= number_format($row['wo_qnty'],2)  ?></td>
								
								<?
								if($j==1){?>
								<td align="right" rowspan="<?=$color_id_arr[$color_id];?>"><?= number_format($total_qty,2);$grand_total+=$total_qty  ?></td>
								<?}?>	

							
								
								
							<? 
								$fab++;
								$j++;
								
							?>						
						</tr>
					<? } }?>

				<?
			}
			?>
			<tr>
					<th colspan="5" align="right">Fabric Grand Total :</th>
					<th align="right"><?= number_format($grand_total,2)  ?></th>
				</tr>
		</table>
			
         

       	<table  width="100%" class="rpt_table" style="margin:5px;font-size:16px;font-family:'Arial Narrow';"   border="1" cellpadding="0" cellspacing="0">
          <tr>
          <td><? echo get_spacial_instruction($txt_booking_no); ?></td>
          </tr>
        </table>
         <br/>
		 <?
            echo signature_table(82, $cbo_company_name, "1113px");
         ?>
    </div>

	<?
}
if($action=="show_trim_booking_report7")//MD. SAKIBUL ISLAM 16592 22 Aug 2023
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_fabric_natu=str_replace("'","",$cbo_fabric_natu);
	$cbo_fabric_source=str_replace("'","",$cbo_fabric_source);
	$txt_job_no=str_replace("'","",$txt_job_no);
	$show_yarn_rate=str_replace("'","",$show_yarn_rate);
	$booking_id=str_replace("'","",$booking_mst_id);
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	//booking_no=$txt_booking_no
	
	$path=str_replace("'","",$path);
	if($path==1) $path="../../";
	
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1 and master_tble_id='$cbo_company_name'",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$brand_name_arr=return_library_array( "select id, brand_name from lib_buyer_brand ",'id','brand_name');
	$user_name_arr=return_library_array( "select id,user_name from user_passwd  where status_active=1 ",'id','user_name');
	$team_leader_arr=return_library_array( "select id,team_leader_name from lib_marketing_team",'id','team_leader_name');
	$designation_arr=return_library_array( "select id,designation from   lib_supplier  where status_active=1 and is_deleted=0",'id','designation');
	$contact_no_arr=return_library_array( "select id,contact_no from   lib_supplier  where status_active=1 and is_deleted=0",'id','contact_no');
 
	$pro_sub_dept_array=return_library_array( "select id,sub_department_name from lib_pro_sub_deparatment",'id','sub_department_name');
	?>
	<style type="text/css">
		@media print {
		    .pagebreak { page-break-before: always; } /* page-break-after works, as well */
		}
	</style>
	<div style="width:1330px" align="center">
    <?php
    	$lip_yarn_count=return_library_array( "select id,fabric_composition_id from lib_yarn_count_determina_mst where  status_active=1", "id", "fabric_composition_id");
		$fabric_composition=return_library_array( "select id,fabric_composition_name from lib_fabric_composition where  status_active=1", "id", "fabric_composition_name");
		
		$nameArray_approved = sql_select("select max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and  a.booking_no='$txt_booking_no' and b.entry_form=7");
		list($nameArray_approved_row) = $nameArray_approved;
		$nameArray_approved_date = sql_select("select b.approved_date as approved_date from wo_booking_mst a, approval_history b where a.id=b.mst_id and  a.booking_no='$txt_booking_no' and b.entry_form=7 and b.approved_no='" . $nameArray_approved_row[csf('approved_no')] . "'");
		list($nameArray_approved_date_row) = $nameArray_approved_date;
		$nameArray_approved_comments = sql_select("select b.comments as comments from wo_booking_mst a, approval_history b where a.id=b.mst_id and  a.booking_no='$txt_booking_no' and b.entry_form=7 and b.approved_no='" . $nameArray_approved_row[csf('approved_no')] . "'");
		list($nameArray_approved_comments_row) = $nameArray_approved_comments;

		$max_approve_date_data = sql_select("select min(b.approved_date) as approved_date,max(b.approved_date) as last_approve_date,max(b.un_approved_date) as un_approved_date from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7");
		$first_approve_date='';
		$last_approve_date='';
		$un_approved_date='';
		if(count($max_approve_date_data))
		{
			$last_approve_date=$max_approve_date_data[0][csf('last_approve_date')];
			$first_approve_date=$max_approve_date_data[0][csf('approved_date')];
			$un_approved_date=$max_approve_date_data[0][csf('un_approved_date')];
		}
		
		$nameArray=sql_select( "SELECT a.booking_date,a.is_approved, a.supplier_id, a.currency_id, a.exchange_rate, a.attention, a.booking_date, a.po_break_down_id,a.currency_id, a.delivery_date,a.source,  a.fabric_source, a.inserted_by, a.insert_date, a.update_date, a.tagged_booking_no, a.uom, a.pay_mode, a.booking_percent, b.buyer_name,b.id as job_id, b.style_ref_no, b.gmts_item_id, b.order_uom, b.total_set_qnty, sum(b.job_quantity*b.total_set_qnty) as jobqtypcs, b.style_description, b.season_buyer_wise as season, b.product_dept, b.product_code, b.pro_sub_dep, b.dealing_marchant,b.factory_marchant, b.order_repeat_no, b.repeat_job_no, a.fabric_composition, a.remarks, a.sustainability_standard, b.brand_id, a.quality_level, a.fab_material, a.requisition_no, b.qlty_label, b.packing,  a.proceed_knitting, a.proceed_dyeing,c.process,b.team_leader,c.job_no,
		c. sub_process_id from wo_booking_mst a, wo_po_details_master b ,wo_booking_dtls c  where a.id=c.booking_mst_id and c.job_no=b.job_no  and  c.process=31 and c.entry_form_id=229  and a.entry_form=229 and c.booking_type=3 and c.booking_no='$txt_booking_no' group by  a.booking_date,a.is_approved, a.supplier_id, a.currency_id, a.exchange_rate, a.attention, a.booking_date, a.po_break_down_id,a.currency_id, a.delivery_date,a.source,  a.fabric_source, a.inserted_by, a.insert_date, a.update_date, a.tagged_booking_no, a.uom, a.pay_mode, a.booking_percent, b.buyer_name,b.id , b.style_ref_no, b.gmts_item_id, b.order_uom, b.total_set_qnty, b.style_description, b.season_buyer_wise , b.product_dept, b.product_code, b.pro_sub_dep, b.dealing_marchant,b.factory_marchant, b.order_repeat_no, b.repeat_job_no, a.fabric_composition, a.remarks, a.sustainability_standard, b.brand_id, a.quality_level, a.fab_material, a.requisition_no, b.qlty_label, b.packing,  a.proceed_knitting, a.proceed_dyeing,c.process,b.team_leader,c.job_no,
         c. sub_process_id");

		 
		 
		$jobqtypcs=0;
		foreach($nameArray as $row)
		{
			$po_id_allArr[$row[csf('po_break_down_id')]]=$row[csf('po_break_down_id')];
			$job_no_strArr[$row[csf('job_id')]]=$row[csf('job_id')];
			$job_no_Arr[$row[csf('job_no')]]=$row[csf('job_no')];
			$tagged_booking_no=$row[csf('tagged_booking_no')];
			$booking_uom=$row[csf('uom')];$pay_modeId=$row[csf('pay_mode')];
			$bookingup_date=$row[csf('update_date')];
			$bookingins_date=$row[csf('insert_date')];
			$delivery_date=$row[csf('delivery_date')];
			$booking_date=$row[csf('booking_date')];
			$supplier_id=$row[csf('supplier_id')];
			$gmts_item_id=$row[csf('gmts_item_id')];
			$buyer_name=$row[csf('buyer_name')];
			$source_id=$row[csf('source')];
			$exchange_rate=$row[csf('exchange_rate')];
			$product_code=$row[csf('product_code')];
			$requisition_no=$row[csf('requisition_no')];
			$jobqtypcs+=$row[csf('jobqtypcs')];
			$inserted_by2=$user_name_arr[$row[csf('inserted_by')]];
			$supplier_id=$row[csf('supplier_id')];
			$pay_mode=$row[csf('pay_mode')];
			$style_ref_no=$row[csf('style_ref_no')];
			$team_leader=$team_leader_arr[$row[csf('team_leader')]];
			$style_description=$row[csf('style_description')];
			$sub_process=$dyeing_sub_process[$row[csf('sub_process_id')]];
			if($row[csf('style_description')]!="")
			{
			$job_desc_strArr[$row[csf('style_description')]]=$row[csf('style_description')];
			}
			if($row[csf('style_ref_no')]!="")
			{
			$job_style_ref_noArr[$row[csf('style_ref_no')]]=$row[csf('style_ref_no')];
			}
			$currency_id=$row[csf('currency_id')];
			
			if($row[csf('remarks')]!="")
			{
			$remarks=$row[csf('remarks')];
			//echo $remarks.'DD';
			}
			$attention=$row[csf('attention')];
			$process=$conversion_cost_head_array[$row[csf('process')]];
			if($currency_id==1){$paysa_sent="Paisa";} else if($currency_id==2){$paysa_sent="CENTS";}
			
			$total_set_qnty=$row[csf('total_set_qnty')];

			if($row[csf('is_approved')]==1){
				$approved_msg= "Fully approved";
			}
			elseif ($row[csf('is_approved')]==3) {
				$approved_msg= "Partial approved";
			}
			else{
				$approved_msg='';
			}
			

		}
		
		 $po_id_all=implode(",",$po_id_allArr);
		 $job_no_str=implode(",",$job_no_strArr);
		 $job_no_Arr_all=implode(",",$job_no_Arr);
		 
		 $fab_book=sql_select("select c.booking_no,a.job_no from wo_booking_dtls c,wo_po_details_master a  where  a.job_no=c.job_no and a.id in($job_no_str) and c.status_active=1 and c.is_deleted=0 and c.booking_type=1  group by c.booking_no,a.job_no order by c.booking_no");

		foreach($fab_book as $row)
		{
		  $fab_booking_noArr[$row[csf('booking_no')]]= $row[csf('booking_no')];
		}	
		 $fab_booking_noArr_all=implode(",",$fab_booking_noArr);
		 
		 if($job_no_str!="") $location=return_field_value( "location_name", "wo_po_details_master","id in($job_no_str)"); else $location="";
		$sql_loc=sql_select("select id,location_name,address from lib_location where company_id=$cbo_company_name");
		foreach($sql_loc as $row)
		{
			$location_name_arr[$row[csf('id')]]= $row[csf('location_name')];
			$location_address_arr[$row[csf('id')]]= $row[csf('address')];
		}		
		$yes_no_sql=sql_select("select job_no,cons_process from  wo_pre_cost_fab_conv_cost_dtls where job_id  in($job_no_str)  and status_active=1 and is_deleted=0  order by id");
		
		$peach=''; $brush=''; $fab_wash='';

		$emb_print=sql_select("select id, job_no, emb_name, emb_type from wo_pre_cost_embe_cost_dtls where  job_id  in($job_no_str) and status_active=1 and is_deleted=0 and cons_dzn_gmts>0 and emb_name in (1,2,3) order by id");
		
		$emb_print_data=array();
		$type_array=array(0=>$blank_array,1=>$emblishment_print_type,2=>$emblishment_embroy_type,3=>$emblishment_wash_type,4=>$emblishment_spwork_type,5=>$emblishment_gmts_type,99=>$blank_array);
		
		foreach ($emb_print as $row) 
		{
			$emb_print_data[$row[csf('job_no')]][$row[csf('emb_name')]].=$type_array[$row[csf("emb_name")]][$row[csf('emb_type')]].",";
		}
		 $cancel_po_arr=return_library_array( "select po_number,po_number from wo_po_break_down where job_no_mst='$job_no_str' and status_active=3", "po_number", "po_number");
		 $po_shipment_date=sql_select("select  MIN(pub_shipment_date) as min_shipment_date,max(pub_shipment_date) as max_shipment_date from wo_po_break_down where id in(".$po_id_all.") order by shipment_date asc ");
         $min_shipment_date='';
         $max_shipment_date='';
         foreach ($po_shipment_date as $row) {
         	 $min_shipment_date=$row[csf('min_shipment_date')];
         	 $max_shipment_date=$row[csf('max_shipment_date')];
         	 break;
         }

        
        
       
  		ob_start();     
		?>	
											<!--    Header Company Information         -->
        <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black; font-family:Arial Narrow;" >
            <tr>
                <td width="200" style="font-size:28px"><img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' /></td>
                <td width="1250">
                    <table width="100%" cellpadding="0" cellspacing="0"  style="position: relative;">
                        <tr>
                            <td align="center" style="font-size:28px;"> <?php echo $company_library[$cbo_company_name]; ?></td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:18px;position: relative;"><?=$location_address_arr[$location]; ?></td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:24px">
                            	<span style="float:center;"><b><strong> <font style="color:black">Service Booking For Dyeing.</font></strong></b></span> 
                            </td>

							<td align="center" style="font-size:24px">
                            	<span style="float:center;"><b><strong> <font style="color:red">&nbsp;<? echo $approved_msg;?></font></strong></b></span> 
                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:20px">
							<!-- <?
							if(str_replace("'","",$id_approved_id) ==1){ ?>
                            <span style="font-size:20px; float:center;"><strong> <font style="color:green"> <? echo "[Approved]"; ?> </font></strong></span> 
                               <? }else{ ?>
								<span style="font-size:20px; float:center;"><strong> <font style="color:red"><? echo "[Not Approved]"; ?> </font></strong></span> 
							   <? } ?> -->
							  
                            </td>
							<!-- <td><strong style="background-color:yellow;padding:5%;font-size: 30px;"><?=str_replace("'","",$txt_booking_no);;?></strong><br><strong style="margin-left:20%;"><?=str_replace("'","",$txt_booking_date);;?></strong></td>  -->
							
                        </tr>
						
						
                    </table>
					
                </td>
            </tr>
        </table>

		<?
        $job_no=trim($job_no_str,"'"); $total_set_qnty=0; $colar_excess_percent=0; $cuff_excess_percent=0; $rmg_process_breakdown=0; $booking_percent=0; $booking_po_id='';
		if($db_type==0)
        {
            $date_dif_cond="DATEDIFF(pub_shipment_date,po_received_date)";
            $group_concat_all="group_concat(grouping) as grouping, group_concat(file_no) as file_no";
        }
        else
        {
            $date_dif_cond="(pub_shipment_date-po_received_date)";
            $group_concat_all=" listagg(cast(grouping as varchar2(4000)),',') within group (order by grouping) as grouping,
                                listagg(cast(file_no as varchar2(4000)),',') within group (order by file_no) as file_no  ";
        }
        $po_number_arr=array(); $po_ship_date_arr=array(); $shipment_date=""; $po_no=""; $po_received_date=""; $shiping_status="";
        $po_sql=sql_select("select id, po_number, pub_shipment_date, MIN(pub_shipment_date) as mpub_shipment_date, MIN(po_received_date) as po_received_date, MIN(insert_date) as insert_date, plan_cut, po_quantity, shiping_status, $date_dif_cond as date_diff,min(factory_received_date) as factory_received_date, $group_concat_all,status_active from wo_po_break_down where id in(".$po_id_all.") group by id, po_number, pub_shipment_date, plan_cut, po_quantity, shiping_status, po_received_date,status_active ");
      
		
        $to_ship=0; $fp_ship=0; $f_ship=0;

        foreach($po_sql as $row)
        {
            $po_qnty_tot+=$row[csf('plan_cut')];
            $po_qnty_tot1+=$row[csf('po_quantity')];
            $po_number_arr[$row[csf('id')]]=$row[csf('po_number')];
            $po_ship_date_arr[$row[csf('id')]]=$row[csf('pub_shipment_date')];
            $po_num_arr[$row[csf('id')]]=$row[csf('po_number')];
            $po_no.=$row[csf('po_number')].", ";
            $shipment_date.=change_date_format($row[csf('mpub_shipment_date')],'dd-mm-yyyy','-').", ";
            $lead_time.=$row[csf('date_diff')].",";
            $po_received_date=change_date_format($row[csf('po_received_date')],'dd-mm-yyyy','-');
            $factory_received_date=change_date_format($row[csf('factory_received_date')],'dd-mm-yyyy','-');
            $grouping.=$row[csf('grouping')].",";
            $file_no.=$row[csf('file_no')].",";
			if($row[csf('status_active')]==3){
				$cancel_po_no[$row[csf('po_number')]]=$row[csf('po_number')];
			}

			
			$daysInHand.=(datediff('d',date('d-m-Y',time()),$row[csf('mpub_shipment_date')])-1).",";
			
			if($bookingup_date=="" || $bookingup_date=="0000-00-00 00:00:00")
			{
				$booking_date=$bookingins_date;
			}
			$WOPreparedAfter.=(datediff('d',$row[csf('insert_date')],$booking_date)-1).",";

			if($row[csf('shiping_status')]==1) {
				$shiping_status.= "FP".",";
				$to_ship++;
				$fp_ship++;
			}
			else if($row[csf('shiping_status')]==2){
				$shiping_status.= "PD".",";
				$to_ship++;
			} 
			else if($row[csf('shiping_status')]==3){
				$shiping_status.= "FS".",";
				$to_ship++;
				$f_ship++;
			} 
        }

        if($to_ship==$f_ship) $shiping_status= "<b style='color:green'>Full shipped</b>";
        else if($to_ship==$fp_ship) $shiping_status= "<b style='color:red'>Full Pending</b>";
        else $shiping_status= "<b style='color:red'>Partial Delivery</b>";
		
		$po_no=implode(",",array_filter(array_unique(explode(",",$po_no))));
		$shipment_date=implode(",",array_filter(array_unique(explode(",",$shipment_date))));
		$lead_time=implode(",",array_filter(array_unique(explode(",",$lead_time))));
		$po_received_date=implode(",",array_filter(array_unique(explode(",",$po_received_date))));
		$factory_received_date=implode(",",array_filter(array_unique(explode(",",$factory_received_date))));
		$grouping=implode(",",array_filter(array_unique(explode(",",$grouping))));
		$file_no=implode(",",array_filter(array_unique(explode(",",$file_no))));
		
		$daysInHand=implode(",",array_filter(array_unique(explode(",",$daysInHand))));
		$WOPreparedAfter=implode(",",array_filter(array_unique(explode(",",$WOPreparedAfter))));
		$shiping_status=implode(",",array_filter(array_unique(explode(",",$shiping_status))));
		
       // foreach ($nameArray as $result)
       // {
            $total_set_qnty=$result[csf('total_set_qnty')];
            $colar_excess_percent=$result[csf('colar_excess_percent')];
            $cuff_excess_percent=$result[csf('cuff_excess_percent')];
            $rmg_process_breakdown=$result[csf('rmg_process_breakdown')];
            
            $booking_percent=$result[csf('booking_percent')];
			$booking_po_id=$result[csf('po_break_down_id')];
			?>
			<table width="100%" border="0" align="left" cellpadding="0"  cellspacing="0" rules="all"  style="border:0px solid black;font-size:18px; font-family:Arial Narrow;" >
				<tr>
					<td width="200"><b>Booking No: </b></td>		 
					<td width="250"> <span style="font-size:18px"><?=str_replace("'","",$txt_booking_no);?></span></td>
					<td width="200"><b>Booking Date:</b></td>		 
					<td width="250"> <span style="font-size:18px"><?echo change_date_format($booking_date);?></span></td>
					<td width="200"><b>Delivery Date:</b></td>		 
					<td width="250"> <span style="font-size:18px"><? echo change_date_format($delivery_date); ?></span></td>

				</tr>
				<tr>
					<td><b>Supplier Name:</b></td>		 
					<td> <span style="font-size:18px"><?
					if($pay_modeId==5 || $pay_modeId==3){
						echo $company_library[$supplier_id];
						}
						else{
						echo $supplier_name_arr[$supplier_id];
						}
				   ?></span> </td>
				   <td><span style="font-size:18px"><b>Supplier Address:</b></span></td>
				   <td><span style="font-size:18px"><? echo $supplier_address_arr[$supplier_id]; ?> </span> </td>
				   <td><b>Attention:</b></td>
				   <td><? echo $attention; ?></td>
			   
			   </tr>
				<tr>		
					<td><span style="font-size:18px"><b>Currency:</b></span></td>
					<td>&nbsp;<span style="font-size:18px"><? echo $currency[$currency_id]; ?></span></td>
					<td><span style="font-size:18px"><b>Conversion Rate:</b></span></td>
					<td>&nbsp;<span style="font-size:18px"><? echo $exchange_rate; ?></span></td>
					<td><span style="font-size:18px"><b>Source:</b></span></td>
					<td>&nbsp;<span style="font-size:18px"><? echo $source[$source_id]; ?></span></td>
				</tr>
				<tr>
					<td><span style="font-size:18px"><b>Remarks</b></span></td>
					<td><span style="font-size:18px"><? echo $remarks;?></span></td>			
					<td><b>Insert User:</b></td>
					<td><? echo $inserted_by2; ?></td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
			</table>		
		<?php
		$fabric_desc_arr=array();

		$wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select id,fabric_description,cons_process from wo_pre_cost_fab_conv_cost_dtls  where job_id in($job_no_str)");
		foreach( $wo_pre_cost_fab_conv_cost_dtls_id as $row_wo_pre_cost_fab_conv_cost_dtls_id)
		{
			if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]!=0)
			{
				$fabric_description=sql_select("select id,body_part_id,body_part_type,color_type_id,fabric_description,construction,composition,gsm_weight,width_dia_type from  wo_pre_cost_fabric_cost_dtls 
				where  id='".$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]."' order by body_part_id");
				list($fabric_description_row)=$fabric_description; 
				


				$fabric_desc_arr[$fabric_description_row[csf("id")]]['body_part_id']=$fabric_description_row[csf("body_part_id")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['construction']=$fabric_description_row[csf("construction")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['composition']=$fabric_description_row[csf("composition")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['color_type_id']=$fabric_description_row[csf("color_type_id")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['gsm_weight']=$fabric_description_row[csf("gsm_weight")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['width_dia_type']=$fabric_description_row[csf("width_dia_type")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['body_part_type']=$fabric_description_row[csf("body_part_type")];
				
			}
			if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]==0)
			{
				
			
				$fabric_description=sql_select("select id,body_part_id,body_part_type,color_type_id,fabric_description,construction,composition,gsm_weight,width_dia_type from  wo_pre_cost_fabric_cost_dtls 
				where  job_id in($job_no_str)");

				foreach( $fabric_description as $fabric_description_row)
				{
				

				$fabric_desc_arr[$fabric_description_row[csf("id")]]['body_part_id']=$fabric_description_row[csf("body_part_id")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['construction']=$fabric_description_row[csf("construction")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['composition']=$fabric_description_row[csf("composition")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['color_type_id']=$fabric_description_row[csf("color_type_id")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['gsm_weight']=$fabric_description_row[csf("gsm_weight")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['width_dia_type']=$fabric_description_row[csf("width_dia_type")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['body_part_type']=$fabric_description_row[csf("body_part_type")];
		
				}
				
			}


		}
			// echo "<pre>";
			// print_r($fabric_desc_arr);
		$pre_cons_data=sql_select("select  id, po_break_down_id, color_number_id, gmts_sizes, dia_width, item_size, cons, process_loss_percent, requirment, pcs, color_size_table_id, rate, amount, remarks ,pre_cost_fabric_cost_dtls_id  as fab_desc_id from wo_pre_cos_fab_co_avg_con_dtls where job_id in($job_no_str)  ");


		foreach($pre_cons_data as $row){

			$color_wise_data[$row[csf("po_break_down_id")]][$row[csf("fab_desc_id")]][$row[csf("color_number_id")]]['finsh_cons']=$row[csf("cons")];
			$color_wise_data[$row[csf("po_break_down_id")]][$row[csf("fab_desc_id")]][$row[csf("color_number_id")]]['gray_cons']=$row[csf("requirment")];
			$color_wise_data[$row[csf("po_break_down_id")]][$row[csf("fab_desc_id")]][$row[csf("color_number_id")]]['gray_cons']=$row[csf("requirment")];
			if($row[csf("dia_width")]!="")
			{
			$color_wise_data[$row[csf("po_break_down_id")]][$row[csf("fab_desc_id")]][$row[csf("color_number_id")]]['dia_width']=$row[csf("dia_width")];
			}
			$color_wise_data[$row[csf("po_break_down_id")]][$row[csf("fab_desc_id")]][$row[csf("color_number_id")]]['process_loss_percent']=$row[csf("process_loss_percent")];

		}

		$nameArray_fabric_description= sql_select("SELECT c.id as conv_dtl_id,b.job_no,b.po_break_down_id as po_id,b.sensitivity,b.uom,b.gmts_color_id,b.fabric_color_id,b.gmts_size,b.remark,sum(b.wo_qnty) as wo_qnty,a.style_ref_no,a.buyer_name,d.po_number,d.id,b.option_shade,sum(b.amount) as amount,c.charge_unit,c.fabric_description  as fab_desc_id,b.delivery_date,b.delivery_end_date,b.labdip_no,b.rate,b.process_loss_percent,b.sub_process_id	from wo_pre_cost_fab_conv_cost_dtls c,wo_booking_dtls b, wo_po_details_master a,wo_po_break_down d where b.pre_cost_fabric_cost_dtls_id=c.id and b.job_no=c.job_no and b.job_no=a.job_no and b.po_break_down_id=d.id and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 	and d.job_id in($job_no_str) and b.wo_qnty>0 and b.booking_type=3 and b.process=31 and  b.booking_no='$txt_booking_no' group by b.job_no,c.id,c.charge_unit,b.sensitivity,b.uom,b.gmts_color_id,b.fabric_color_id,b.gmts_size,c.fabric_description,b.delivery_date,b.delivery_end_date,a.style_ref_no,a.buyer_name, b.po_break_down_id,d.po_number,d.id,b.labdip_no ,b.rate,b.remark,b.process_loss_percent,b.option_shade,b.sub_process_id order by c.fabric_description");
		//echo "<pre>";
		//print_r($nameArray_fabric_description);
		$body_part_type_arr=array();
		foreach ($nameArray_fabric_description as $row) {	

			$body_part_id=$fabric_desc_arr[$row[csf("fab_desc_id")]]['body_part_id'];
			$body_part_type=$fabric_desc_arr[$row[csf("fab_desc_id")]]['body_part_type'];
			$construction=$fabric_desc_arr[$row[csf("fab_desc_id")]]['construction'];
			$composition=$fabric_desc_arr[$row[csf("fab_desc_id")]]['composition'];
			$color_type_id=$fabric_desc_arr[$row[csf("fab_desc_id")]]['color_type_id'];
			$gsm_weight=$fabric_desc_arr[$row[csf("fab_desc_id")]]['gsm_weight'];
			$width_dia_type=$fabric_desc_arr[$row[csf("fab_desc_id")]]['width_dia_type'];
			if($body_part_type==40 || $body_part_type==50){
				$body_part_type_arr[$body_part_type]=$body_part_type;
			}


			$finsh_cons=$color_wise_data[$row[csf("po_id")]][$row[csf("fab_desc_id")]][$row[csf("gmts_color_id")]]['finsh_cons'];
			$gray_cons=$color_wise_data[$row[csf("po_id")]][$row[csf("fab_desc_id")]][$row[csf("gmts_color_id")]]['gray_cons'];
			$dia_width=$color_wise_data[$row[csf("po_id")]][$row[csf("fab_desc_id")]][$row[csf("gmts_color_id")]]['dia_width'];
			$grouping_item=$row[csf('buyer_name')].'*'.$row[csf('job_no')].'*'.$row[csf('po_id')].'*'.$row[csf('style_ref_no')];
			$grouping_item2=$color_type_id.'*'.$body_part_id.'*'.$construction.'*'.$composition.'*'.$row[csf('fabric_color_id')];
			$pp=100+$process_loss_percent;
			$fabric_data_arr[$grouping_item][$grouping_item2]['gmts_color_id'] = $row[csf('gmts_color_id')];
			$fabric_data_arr[$grouping_item][$grouping_item2]['style_ref_no'] = $row[csf('style_ref_no')];
			$fabric_data_arr[$grouping_item][$grouping_item2]['po_id'] = $row[csf('po_id')];
			$fabric_data_arr[$grouping_item][$grouping_item2]['job_no'] = $row[csf('job_no')];
			$fabric_data_arr[$grouping_item][$grouping_item2]['po_number'] = $row[csf('po_number')];
			$fabric_data_arr[$grouping_item][$grouping_item2]['fabric_color_id'] = $row[csf('fabric_color_id')];
			$fabric_data_arr[$grouping_item][$grouping_item2]['labdip_no'][$row[csf('labdip_no')]] = $row[csf('labdip_no')];
			$fabric_data_arr[$grouping_item][$grouping_item2]['option_shade'][$row[csf('option_shade')]] = $row[csf('option_shade')];
			$fabric_data_arr[$grouping_item][$grouping_item2]['body_part_id'] = $body_part_id;
			$fabric_data_arr[$grouping_item][$grouping_item2]['composition'] = $composition;
			$fabric_data_arr[$grouping_item][$grouping_item2]['construction'] = $construction;
			$fabric_data_arr[$grouping_item][$grouping_item2]['gsm'] = $gsm_weight;
			$fabric_data_arr[$grouping_item][$grouping_item2]['dia_width'] = $dia_width;
			$fabric_data_arr[$grouping_item][$grouping_item2]['dia'] = $fabric_typee[$width_dia_type];
			$fabric_data_arr[$grouping_item][$grouping_item2]['color_type_id'] = $color_type_id;
			$fabric_data_arr[$grouping_item][$grouping_item2]['finsh_cons'] = $finsh_cons;
			$fabric_data_arr[$grouping_item][$grouping_item2]['gray_cons'] = $gray_cons;
			$fabric_data_arr[$grouping_item][$grouping_item2]['fin_fab_qnty'] +=$row[csf('wo_qnty')]-(($row[csf('wo_qnty')]*$row[csf('process_loss_percent')])/100);
			$fabric_data_arr[$grouping_item][$grouping_item2]['grey_fab_qnty'] += $row[csf('wo_qnty')];
			$fabric_data_arr[$grouping_item][$grouping_item2]['amount'] = $row[csf('amount')];
			$fabric_data_arr[$grouping_item][$grouping_item2]['rate'] = $row[csf('rate')];
			$fabric_data_arr[$grouping_item][$grouping_item2]['remark'] = $row[csf('remark')];
			$fabric_data_arr[$grouping_item][$grouping_item2]['buyer_name'] = $row[csf('buyer_name')];
			$fabric_data_arr[$grouping_item][$grouping_item2]['process_loss_percent'] = $row[csf('process_loss_percent')];
			$fabric_data_arr[$grouping_item][$grouping_item2]['sub_process'] = $row[csf('sub_process_id')];
			//dyeing_sub_process[

			if($row[csf('delivery_date')]){
				$fabric_data_arr[$row[csf('labdip_no')]][$grouping_item]['delivery_start_date'] = $row[csf('delivery_date')];
			}
			if($row[csf('delivery_end_date')]){			
				$fabric_data_arr[$row[csf('labdip_no')]][$grouping_item]['delivery_end_date'] = $row[csf('delivery_end_date')];
			}
			
		}
		/* echo "<pre>";
		print_r($fabric_data_arr);die; */

		$body_part_type_ids=implode(",",$body_part_type_arr);
	
		?>
		<p>&nbsp;</p>
		 <table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all"  style="font-size: 18px;">
			 <tr>	
			 	 <th>Buyer Name</th>
				 <th>Job No</th>
				 <th>Style Ref</th>
				 <th>Order No</th>
				 <th>Sub Process</th>
				 <th>Body Part</th>			
				 <th>Construction</th>
				 <th>Composition</th>	
				 <th>LapDip</th>	
				 <th>Opt/Shade</th>				 						 
				 <th>Colour</th>	
				 <th>GSM</th>
				 <th>Dia Type </th>
				 <th>Dia</th>			
				 <th>Grey Kg</th>	
				 <th>P.Loss%</th>
				 <th>Finish Kg</th>	
				 <? if($show_comments==1){?>
				 <th>Rate</th>
				 <th>Amount</th>
				<?}?>		
				 <th>Remark</th>

			 </tr>
			 <? 
			 foreach ($fabric_data_arr as $jobPoData=>$poData) { 
				foreach ($poData as $fabric_id => $value) { 

					$po_wise_rowspan[$jobPoData]+=1;
				}
			 }

			
					 	
			 $sub_fin_fab_qnty=0;   		 	
			 $sub_grey_fab_qnty=0;
			 $sub_amount=0;
				 foreach ($fabric_data_arr as $jobPoData => $poData) {	
					$i=1;
					foreach ($poData as $fabric_id => $value) {	
					$sub_fin_fab_qnty+=$value['fin_fab_qnty'];   		 	
					$sub_grey_fab_qnty+=$value['grey_fab_qnty']; 
					$rate=$value['rate'];
					$sub_amount+=$rate*$value['grey_fab_qnty'];
					$sub_process_arr=array();
						if($value['sub_process']!=''){
							$sub_process_arr=explode(",",$value['sub_process']);
						}				
						if(count($sub_process_arr)>0){
							foreach($sub_process_arr as $sub_processId){
								$processId[$sub_processId]=$conversion_cost_head_array[$sub_processId];
							}
						}

						   ?>
						  <tr>
							<?
						  if($i==1){?>
							 <td style="font-size: 16px;" align="center" rowspan="<?=$po_wise_rowspan[$jobPoData]; ?>"><? echo  $buyer_name_arr[$value['buyer_name']]?></td>
							 <td style="font-size: 16px;" align="center" rowspan="<?=$po_wise_rowspan[$jobPoData]; ?>"><? echo $value['job_no'] ?></td>
							 <td style="font-size: 16px;" align="center" rowspan="<?=$po_wise_rowspan[$jobPoData]; ?>"><? echo $value['style_ref_no'] ?></td>
							 <td style="font-size: 16px;" align="center" rowspan="<?=$po_wise_rowspan[$jobPoData]; ?>"><? echo $value['po_number'] ?></td>	
							 <?$i++;}?>
							 <td style="font-size: 16px;" align="center"><? echo implode(",", $processId); ?></td>
							 <td style="font-size: 16px;" align="center"><? echo $body_part[$value['body_part_id']] ?></td>
							 	
							 <td style="font-size: 16px;" align="center"><? echo $value['construction'] ?></td>
							 <td style="font-size: 16px;" align="center"><? echo $value['composition'] ?></td>
							 <td style="font-size: 16px;" align="center"><? echo implode(",",$value[('labdip_no')]); ?></td>
							 <td style="font-size: 16px;" align="center"><? echo implode(",",$value[('option_shade')]); ?></td>
							 <td style="font-size: 16px;" align="center"><? echo $color_library[$value['fabric_color_id']] ?></td>		
							 <td style="font-size: 16px;" align="center"><? echo $value['gsm'] ?></td>							 
							 <td style="font-size: 16px;" align="center"><? echo $value['dia'] ?></td>
							 <td style="font-size: 16px;" align="center"><? echo $value['dia_width'] ?></td>
							 <td style="font-size: 16px;" align="right"><? echo fn_number_format($value['grey_fab_qnty'],2) ; ?></td>
							 <td style="font-size: 16px;" align="right"><? echo $value['process_loss_percent'] ; ?></td>	
							 <td style="font-size: 16px;" align="right"><? echo fn_number_format($value['fin_fab_qnty'],2) ; ?></td>
							 <? if($show_comments==1){?>
							 <td style="font-size: 16px;" align="right"><? echo fn_number_format($rate,2); ?></td>
							 <td style="font-size: 16px;" align="right"><? echo fn_number_format($rate*$value['grey_fab_qnty'],2) ; ?></td>
							 <?}?>							
							 
							 <td style="font-size: 16px;" align="center"><? echo $value['remark'] ?></td>	

						 </tr>
						  <?// } 
						 // else {
							?>
							  <!-- <tr>
							 <td style="font-size: 16px;" align="center"><? echo $body_part[$value['body_part_id']] ?></td>	
							 
							 <td style="font-size: 16px;" align="center"><? echo $value['construction'] ?></td>
							 <td style="font-size: 16px;" align="center"><? echo $value['composition'] ?></td>					 			
							 <td style="font-size: 16px;" align="center"><? echo $color_library[$value['fabric_color_id']] ?></td>					 
							 <td style="font-size: 16px;" align="center"><? echo $value['gsm'] ?></td>
							 <td style="font-size: 16px;" align="center"><? echo $value['dia'] ?></td>
							 <td style="font-size: 16px;" align="center"><? echo $value['dia_width'] ?></td>
							 <td style="font-size: 16px;" align="right"><? echo fn_number_format($value['grey_fab_qnty'],2) ; ?></td>
							 <td style="font-size: 16px;" align="right"><? echo $value['process_loss_percent'] ; ?></td>	
							 <td style="font-size: 16px;" align="right"><? echo fn_number_format($value['fin_fab_qnty'],2) ; ?></td>
							 <? if($show_comments==1){?>				
							 <td style="font-size: 16px;" align="right"><? echo fn_number_format($rate,2); ?></td>
							 <td style="font-size: 16px;" align="right"><? echo fn_number_format($rate*$value['grey_fab_qnty'],2) ; ?></td> <?}?>	
							 <td style="font-size: 16px;" align="center"><? echo $value['remark'] ?></td>	
							 </tr> -->
							
						  <?// }
						  $i++;
				 }}
				 ?>
				 <tr>
				 <th align="right" colspan="14"> Sub Total</th>
				 <th align="right"><?echo number_format($sub_grey_fab_qnty,2);  ?></th>	
				 <th></th>
				 <th align="right"><?echo number_format($sub_fin_fab_qnty,2);  ?></th>					 
				 <th></th><? if($show_comments==1){?>
				 <th align="right"><?echo number_format($sub_amount,2);  ?></th>
				 <th></th> <?}?>	
				 </tr>
				 <?
				 	$fin_fab_qnty+=	$sub_fin_fab_qnty;   		 	
					$grey_fab_qnty+=$sub_grey_fab_qnty;
					$amount+=$sub_amount;
					?>
				 
			 <?
			 //} 
			 ?>
			 <tr>
				 <th align="right" colspan="14"> Grand Total</th>
				 <th align="right"><?echo number_format($grey_fab_qnty,2);  ?></th>			
				 <th></th>				 
				 <th align="right"><?echo number_format($fin_fab_qnty,2);  ?></th>		
				 <th></th><? if($show_comments==1){?>
				 <th align="right"><?echo number_format($amount,2);  ?></th>
				 <th></th>
				 <?}?>	
			</tr>
			<tr>
			<th colspan="20" align="left">Total Qty (in word):<? echo str_replace("-"," ",number_to_words(def_number_format($fin_fab_qnty,2,""))); ?></th>
			</tr>
			<tr>
				<th colspan="20" align="left">Grand Total Booking Amount (in word):<? echo str_replace("-"," ",number_to_words(def_number_format($amount,2,""),$currency[$currency_id],$paysa_sent)); ?></th>
				
			</tr>
		</table>
		  <br/>

      	<!--  Here will be the main portion  -->
		<?
		//$job_no_strArr[$row[csf('job_id')]]=$row[csf('job_id')];
		//	$job_no_Arr[$row[csf('job_no')]]=$row[csf('job_no')];
			
        $costing_per=""; $costing_per_qnty=0; //
        $costing_per_id=return_field_value( "costing_per", "wo_pre_cost_mst","job_id in(".implode(",",$job_no_strArr).")");
        if($costing_per_id==1)
        {
			$costing_per="1 Dzn";
			$costing_per_qnty=12;
        }
        if($costing_per_id==2)
        {
			$costing_per="1 Pcs";
			$costing_per_qnty=1;
        }
        if($costing_per_id==3)
        {
			$costing_per="2 Dzn";
			$costing_per_qnty=24;
        }
        if($costing_per_id==4)
        {
			$costing_per="3 Dzn";
			$costing_per_qnty=36;
        }
        if($costing_per_id==5)
        {
			$costing_per="4 Dzn";
			$costing_per_qnty=48;
        }
		
		?>
		&nbsp;
	</br>
	</br>


  <?
   $lib_designation_arr = return_library_array(" select id,custom_designation from lib_designation", "id", "custom_designation");
 
  $user_lib_designation_arr = return_library_array("SELECT id,designation from user_passwd", "id", "designation");
  $user_lib_name_arr = return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");

  $mst_id = return_field_value("id as mst_id", "wo_booking_mst", "booking_no='$txt_booking_no'", "mst_id");
   //echo $mst_id.'ssD';   
//and b.un_approved_date is null
//  $approve_data_array = sql_select("select b.approved_by,min(b.approved_date) as approved_date from   approval_history b where b.mst_id=$mst_id and b.entry_form=28  GROUP BY b.approved_by,b.mst_id,b.sequence_no order by b.sequence_no asc");




 $unapprove_data_array = sql_select("select b.approved_by,b.approved_date,b.un_approved_reason,b.un_approved_date,b.approved_no from   approval_history b where b.mst_id=$mst_id and b.entry_form=65  order by b.approved_date,b.approved_by");




?>
<?
if (count($unapprove_data_array) > 0) {
	
	$sql_unapproved = sql_select("select booking_id,approval_cause from fabric_booking_approval_cause where  entry_form=65 and approval_type=2 and is_deleted=0 and status_active=1 and booking_id=$mst_id");
	
	$unapproved_request_arr = array();
	foreach ($sql_unapproved as $rowu) {
		$unapproved_request_arr[$rowu[csf('booking_id')]] = $rowu[csf('approval_cause')];
	}
?>
	<table width="850" class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all">
		<thead>
			<tr style="border:1px solid black;">
				<th colspan="6" style="border:1px solid black;">Approval/Un Approval History</th>
			</tr>
			<tr style="border:1px solid black;">
				<th width="3%" style="border:1px solid black;">Sl</th>
				<th width="30%" style="border:1px solid black;">Name</th>
				<th width="20%" style="border:1px solid black;">Designation</th>
				<th width="5%" style="border:1px solid black;">Approval Status</th>
				<th width="20%" style="border:1px solid black;">Reason For Un Approval</th>
				<th width="22%" style="border:1px solid black;"> Date</th>

			</tr>
		</thead>
		<tbody>
			<?
			$i = 1;
			foreach ($unapprove_data_array as $row) {

			?>
				<tr style="border:1px solid black;">
					<td width="3%" style="border:1px solid black;"><? echo $i; ?></td>
					<td width="30%" style="border:1px solid black;text-align:center"><? echo $user_lib_name_arr[$row[csf('approved_by')]]; ?></td>
					<td width="20%" style="border:1px solid black;text-align:center"><? echo $lib_designation_arr[$user_lib_designation_arr[$row[csf('approved_by')]]]; ?></td>
					<td width="5%" style="border:1px solid black; text-align:center"><? echo 'Yes'; ?></td>
					<td width="20%" style="border:1px solid black;"><? echo ''; ?></td>
					<td width="22%" style="border:1px solid black;text-align:center"><? if ($row[csf('approved_date')] != "") echo date("d-m-Y h:i:s", strtotime($row[csf('approved_date')]));
																						else echo ""; ?></td>
				</tr>
				<?
				$i++;
				$un_approved_date = explode(" ", $row[csf('un_approved_date')]);
				$un_approved_date = $un_approved_date[0];
				if ($db_type == 0) //Mysql
				{
					if ($un_approved_date == "" || $un_approved_date == "0000-00-00") $un_approved_date = "";
					else $un_approved_date = $un_approved_date;
				} else {
					if ($un_approved_date == "") $un_approved_date = "";
					else $un_approved_date = $un_approved_date;
				}

				if ($un_approved_date != "") {
				?>
					<tr style="border:1px solid black;">
						<td width="3%" style="border:1px solid black;"><? echo $i; ?></td>
						<td width="30%" style="border:1px solid black;text-align:center"><? echo $user_lib_name_arr[$row[csf('approved_by')]]; ?></td>
						<td width="20%" style="border:1px solid black;text-align:center"><? echo $lib_designation_arr[$user_lib_designation_arr[$row[csf('approved_by')]]]; ?></td>
						<td width="5%" style="border:1px solid black;text-align:center;"><? echo 'No'; ?></td>
						<td width="20%" style="border:1px solid black;text-align:center"><? echo $unapproved_request_arr[$mst_id]; ?></td>
						<td width="22%" style="border:1px solid black;text-align:center"><? if ($row[csf('un_approved_date')] != "") echo date("d-m-Y h:i:s", strtotime($row[csf('un_approved_date')]));
																							else echo ""; ?></td>
					</tr>

			<?
					$i++;
				}
			}
			?>
		</tbody>
	</table>
 <?
 }

 ?>
<br />

 <br/>
        
		<fieldset>
                   
			<table  width="100%" class="rpt_table" style="margin:5px;font-size:16px;font-family:'Arial Narrow';"   border="1" cellpadding="0" cellspacing="0">
				<tr>
				<td><? echo get_spacial_instruction($txt_booking_no); ?></td>
				</tr>
			</table>
			</fieldset>       
        <div ><? echo signature_table(290, $cbo_company_name, "1400px",'',40,$inserted_by2); ?></div>
		<br>
		    
       </div>
       <?
	$emailBody=ob_get_contents();
	if($is_mail_send==1){
		
		$sql = "SELECT c.email_address as EMAIL FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=87 and b.mail_user_setup_id=c.id and a.company_id =$cbo_company_name";
		$mail_sql_res=sql_select($sql);
		
		$mailArr=array();
		foreach($mail_sql_res as $row)
		{
			$mailArr[$row[EMAIL]]=$row[EMAIL]; 
		}
		
		$supplier_id=$nameArray[0][csf('supplier_id')];
		$supplier_mail=return_field_value("email", "lib_supplier", "status_active=1 and is_deleted=0 and id=$supplier_id ");

		
		$mailArr=array();
		if($mail_id!=''){$mailArr[]=$mail_id;}
		if($supplier_mail_arr[$supplier_id]!=''){$mailArr[]=$supplier_mail;}
		
		$to=implode(',',$mailArr);
		$subject="Fabric Booking Auto Mail";
		
		if($to!=""){
			require '../../../vendor/phpmailer/phpmailer/PHPMailerAutoload.php';
			require_once('../../../auto_mail/setting/mail_setting.php');
			$header=mailHeader();
			sendMailMailer( $to, $subject, $emailBody );
		}
	}
	exit();
}
if($action=="show_trim_booking_report8") //MD. SAKIBUL ISLAM 14788 31 Aug 2023
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	//$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$color_library=return_library_array( "select id,color_name from lib_color  where status_active=1 and is_deleted=0", "id", "color_name");
	$lib_body_part=return_library_array( "select id,body_part_full_name from lib_body_part  where status_active=1 and is_deleted=0", "id", "body_part_full_name");
	$user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
	$fabric_ima_arr=return_library_array( "select master_tble_id,image_location from common_photo_library  where is_deleted=0 and file_type=1", "master_tble_id", "image_location");
	$path=($path=='')?'../../':$path;
	?>
	<div style="width:1150px" align="left">
       <table width="100%" cellpadding="0" cellspacing="0" style="border:0px solid black;margin:5px; font-size:16px; font-family:'Arial Narrow';" >
           <tr>
               <td width="100">
               <img  src='<? echo $path.$imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
               </td>
               <td width="">
                    <table width="100%" cellpadding="0" cellspacing="0" >
                        <tr>
                            <td align="center" style="font-size:20px;"><?=$company_library[$cbo_company_name]; ?></td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:14px;">
                            <?
                            $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
                            foreach ($nameArray as $result)
                            {
                            ?>
									<? echo $result[csf('plot_no')]; ?>
                                    <? echo $result[csf('level_no')]?>
                                    <? echo $result[csf('road_no')]; ?>
                                    <? echo $result[csf('block_no')];?>
                                    <? echo $result[csf('city')];?>
                                    <? echo $result[csf('zip_code')]; ?>
                                    <?php echo $result[csf('province')]; ?>
                                    <? echo $country_arr[$result[csf('country_id')]]; ?><br>
                                    Email Address: <? echo $result[csf('email')];?>
                                    Website No: <? echo $result[csf('website')];

									$email=$result[csf('email')];
									$website=$result[csf('website')];
									$city=$result[csf('city')];
									$road_no=$result[csf('road_no')];
									$block_no=$result[csf('block_no')];
									$com_add=$result[csf('plot_no')].' '.$result[csf('level_no')].' '.$result[csf('road_no')].' '.$result[csf('block_no')].' '.$city.' '.$result[csf('zip_code')];
                            }
                            ?>
                               </td>
                            </tr>
                            <tr>
                                <td align="center" style="font-size:20px">
                                	<strong>Service Order for Dyeing Finishing</strong>
                                </td>
                            </tr>
                      </table>
                </td>
            </tr>
       </table>
		<?
		$nameArray_job=sql_select(" SELECT b.id as po_id, b.po_number, a.dia_width, a.fabric_color_id, a.fin_gsm, a.gmts_color_id, a.fabric_color_id, a.job_no, a.description, a.program_no, a.lib_composition, a.delivery_date, a.delivery_end_date, a.pre_cost_fabric_cost_dtls_id as fab_dtls_id, a.sensitivity, a.rate, a.uom, a.amount, a.wo_qnty,	a.process_loss_percent,a.labdip_no, d.body_part_id, e.style_ref_no, e.id as job_id, d.fabric_description, d.color_type_id, d.id as fabric_id, d.lib_yarn_count_deter_id, d.construction from wo_booking_dtls a join wo_po_break_down b on a.po_break_down_id=b.id join wo_pre_cost_fab_conv_cost_dtls c on c.id=a.pre_cost_fabric_cost_dtls_id join 
		wo_pre_cost_fabric_cost_dtls d on c.fabric_description=d.id join wo_po_details_master e on e.id=b.job_id where 
		a.booking_no=$txt_booking_no and a.entry_form_id=229 and a.status_active=1 group by b.id, b.po_number, a.dia_width, a.fabric_color_id, a.fin_gsm, a.gmts_color_id, a.fabric_color_id, a.job_no, a.description, a.program_no, a.lib_composition, a.delivery_date, a.delivery_end_date, a.pre_cost_fabric_cost_dtls_id, a.sensitivity, a.rate, a.uom, a.amount, a.wo_qnty,a.process_loss_percent,a.labdip_no, d.body_part_id, e.style_ref_no, e.id, d.fabric_description, d.color_type_id, d.id, d.lib_yarn_count_deter_id, d.construction");
		$fabric_atribute_arr=array('body_part_id','fabric_description','fin_gsm','color_type_id','construction');
		$fabric_color_attr=array('fabric_color_id','uom','rate','fabric_id');
		$fabric_color_summary_attr=array('fabric_description');
		
		foreach($nameArray_job as $row){
			$sensitivity_prog_arr[$row[csf('po_id')]]['job_no']=$row[csf('job_no')];
			$sensitivity_prog_arr[$row[csf('po_id')]]['style_ref']=$row[csf('style_ref_no')];
			$sensitivity_prog_arr[$row[csf('po_id')]]['po_no'][$row[csf('po_id')]]=$row[csf('po_number')];
			foreach($fabric_atribute_arr as $fabattr){
				$sensitivity_prog_arr[$row[csf('po_id')]]['fabric_data'][$row[csf('fab_dtls_id')]][$fabattr]=$row[csf($fabattr)];
				$sensitivity_prog_arr[$row[csf('po_id')]]['fabric_data'][$row[csf('fab_dtls_id')]]['dia'].=$row[csf('dia_width')].',';
			}
			foreach($fabric_color_attr as $fcolorattr){
				$sensitivity_prog_arr[$row[csf('po_id')]]['fabric_data'][$row[csf('fab_dtls_id')]]['color_dtls'][$row[csf('fabric_color_id')]][$fcolorattr]=$row[csf($fcolorattr)];				
			}
			$sensitivity_prog_arr[$row[csf('po_id')]]['fabric_data'][$row[csf('fab_dtls_id')]]['color_dtls'][$row[csf('fabric_color_id')]]['labdip_no']=$row[csf('labdip_no')];
			$sensitivity_prog_arr[$row[csf('po_id')]]['fabric_data'][$row[csf('fab_dtls_id')]]['color_dtls'][$row[csf('fabric_color_id')]]['gmts_color_id']=$row[csf('gmts_color_id')];
			$sensitivity_prog_arr[$row[csf('po_id')]]['fabric_data'][$row[csf('fab_dtls_id')]]['color_dtls'][$row[csf('fabric_color_id')]]['wo_qnty']+=$row[csf('wo_qnty')];
			$sensitivity_prog_arr[$row[csf('po_id')]]['fabric_data'][$row[csf('fab_dtls_id')]]['color_dtls'][$row[csf('fabric_color_id')]]['process_loss_percent']+=$row[csf('process_loss_percent')];
			$sensitivity_prog_arr[$row[csf('po_id')]]['fabric_data'][$row[csf('fab_dtls_id')]]['color_dtls'][$row[csf('fabric_color_id')]]['amount']+=$row[csf('amount')];
			$sensitivity_prog_arr[$row[csf('po_id')]]['fabric_data'][$row[csf('fab_dtls_id')]]['color_dtls'][$row[csf('fabric_color_id')]]['dia'].=$row[csf('dia_width')].',';
			$fabric_color_summary_key=$row[csf('lib_yarn_count_deter_id')].'*'.$row[csf('fin_gsm')].'*'.$row[csf('dia_width')].'*'.$row[csf('color_type_id')].'*'.$row[csf('gmts_color_id')];
			$fabric_color_summary[$fabric_color_summary_key]['fabric_description']=$row[csf('fabric_description')];
			$fabric_color_summary[$fabric_color_summary_key]['fin_gsm']=$row[csf('fin_gsm')];
			$fabric_color_summary[$fabric_color_summary_key]['dia_width']=$row[csf('dia_width')];
			$fabric_color_summary[$fabric_color_summary_key]['color_type_id']=$row[csf('color_type_id')];

			$fabric_color_summary[$fabric_color_summary_key]['fabric_color'][$row[csf('fabric_color_id')]]['fabric_color_id']=$row[csf('fabric_color_id')];
			$fabric_color_summary[$fabric_color_summary_key]['gmts_color'][$row[csf('gmts_color_id')]]['gmts_color_id']=$row[csf('gmts_color_id')];
			$fabric_color_summary[$fabric_color_summary_key]['fabric_color'][$row[csf('fabric_color_id')]]['wo_qnty']+=$row[csf('wo_qnty')];			
		}
		
		foreach($sensitivity_prog_arr as $jobid=>$job_data){
			foreach($job_data['fabric_data'] as $fabric_data){
				foreach($fabric_data['color_dtls'] as $fabric_color){
					$job_wise_rowspan[$jobid]+=1;
				}				
			}					
		}

		
        $nameArray=sql_select( "select a.booking_no, a.booking_date, a.pay_mode, a.remarks, a.buyer_id, a.supplier_id, a.currency_id, a.exchange_rate, a.attention, a.delivery_date, a.source, a.delivery_to, a.attention, a.tenor, a.inserted_by from wo_booking_mst a where a.booking_no=$txt_booking_no  and a.entry_form=229");
		$inserted_by=$user_lib_name[$nameArray[0][csf("inserted_by")]];
        foreach ($nameArray as $result)
        {
			$varcode_booking_no=$result[csf('booking_no')];
			$pay_mode=$result[csf('pay_mode')];
			$supplier_id=$result[csf('supplier_id')];
			if($pay_mode==5 || $pay_mode==3){
				$compsuparr=sql_select( "select company_name, plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website from lib_company where id='$supplier_id'");
				$com_supp=$compsuparr[0][csf('company_name')];
				$suplier_address=$compsuparr[0][csf('plot_no')].' '.$compsuparr[0][csf('level_no')].' '.$compsuparr[0][csf('road_no')].' '.$compsuparr[0][csf('block_no')].' '.$compsuparr[0][csf('city')].' '.$compsuparr[0][csf('zip_code')].'<br> '.$compsuparr[0][csf('email')].'<br> '.$compsuparr[0][csf('website')];
			}
			else{
				
				$suppliar_data=sql_select("SELECT id, supplier_name, contact_no, email,web_site, address_1, address_2, address_3, address_4 from lib_supplier where status_active=1 and is_deleted=0 and id='$supplier_id'");
				
				$com_supp=$suppliar_data[0][csf('supplier_name')];
				$suplier_address=$suppliar_data[0][csf('address_1')].' '.$suppliar_data[0][csf('address_2')].' '.$suppliar_data[0][csf('address_3')].' '.$suppliar_data[0][csf('address_4')].'<br>'.$suppliar_data[0][csf('contact_no')].'<br>'.$suppliar_data[0][csf('email')].'<br>'.$suppliar_data[0][csf('web_site')];
			}
			$currency_id=$result[csf('currency_id')];

        ?>
		<div style="width:1150px;">
       	<table class="rpt_table" width="100%">
			<tr>
				<th width="100" style="font-size:16px" align="left">Booking Ref </th>
				<td>&nbsp;:<? echo $result[csf('booking_no')];?></td>
			</tr>
			<tr>
				<th width="100" style="font-size:16px" align="left">Booking Date </th>
				<td>&nbsp;:<? echo change_date_format($result[csf('booking_date')]); ?></td>
				<td colspan="2" rowspan="5" width="210">
					<? $nameArray_imge =sql_select("SELECT image_location FROM common_photo_library where master_tble_id=$txt_booking_no and file_type=1"); 
					?>
                        <div id="div_size_color_matrix" style="float:right;">
                            <fieldset id="" width="170">
                                <legend>Image </legend>
                                <table width="170">
                                    <tr>
                    				<?
                                        $img_counter = 0;
                                        foreach($nameArray_imge as $result_imge)
                                        {
                      						if($path=="") $path='../../';
                     			 	?>
                      						<td><img src="<? echo $path.$result_imge[csf('image_location')]; ?>" width="150" height="150" /></td>
                      				<?
                     						 $img_counter++;
                                        }
                                        ?>
                                    </tr>
                                </table>
                            </fieldset>
                        </div>
          			</td>
			</tr>
			<tr>
				<th width="100" style="font-size:16px" align="left">Delivery Date </th>
				<td>&nbsp;:<? echo change_date_format($result[csf('booking_date')]); ?></td>
			</tr>
			<tr>
				<th width="100" style="font-size:16px" align="left">Delivery To </th>
				<td>&nbsp;:<?echo $result[csf('delivery_to')]; ?></td>
			</tr>
			<tr>
				<th width="100" style="font-size:16px" align="left">Buyer</th>
				<td>&nbsp;:<?echo $buyer_name_arr[$result[csf('buyer_id')]]; ?></td>
			</tr>
			<tr>
				<th align="left">Atten</th>
				<td>&nbsp;:<? echo $result[csf('attention')];?></td>
			</tr>			
        </table>
		<?
        }
        ?>
		<br>
		<br>
		<table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all">
			<tr>
				<th width="100">PO NO</th>
				<th width="100">STYLE</th>
				<th width="150">DESCRIPTION</th>
                <th width="60">TYPES</th>
				<th width="60">CUTTABLE WIDTH-INCH</th>
				<th width="80">GSM</th>
				<th width="100">GROUND COLOR</th>
				<th width="100">COLOR</th>
				<th width="60" title="Finish Qty after process loss">QTY</th> 
				<th width="60" title="With Process Loss">QUANTITY (With Process Loss)</th>
				<?
				if($show_comments==1){?>
				<th width="60">UNIT PRICE</th>
				<th width="60">AMOUNT USD</th>
				<?}?>
				
				<th width="140">COLOR CODE REF</th>
			</tr>
			<?  $color_wise_grand_qty=0; $color_wise_grand_amount=0;
		
				foreach($sensitivity_prog_arr as $jobid=>$job_data){
					 ?>
					<tr>
						<td rowspan="<?= $job_wise_rowspan[$jobid]+count($job_data['fabric_data']); ?>"><?= implode(", ", $job_data['po_no'])  ?></td>
						<td rowspan="<?= $job_wise_rowspan[$jobid]+count($job_data['fabric_data']); ?>"><?= $job_data['style_ref']  ?></td>
						
						<? 
						$fabrictr=1;
						foreach($job_data['fabric_data'] as $fabric_id=>$fabric_data){
							if($fabrictr!=1) echo '<tr>';
							$dia=implode(",",array_filter(array_unique(explode(",",$fabric_data['dia']))));
							 ?>
							 <td rowspan="<?= count($fabric_data['color_dtls'])+1;  ?>"><?= $fabric_data['fabric_description'];  ?></td>
							 <td rowspan="<?= count($fabric_data['color_dtls'])+1;  ?>"><?= $fabric_data['construction'];  ?></td>
							<td rowspan="<?= count($fabric_data['color_dtls'])+1;  ?>"><?= $dia; ?></td>
							<td rowspan="<?= count($fabric_data['color_dtls'])+1;  ?>"><?= $fabric_data['fin_gsm'];  ?></td>
							 <!-- <td rowspan="<?//= count($fabric_data['color_dtls'])+1;  ?>"><?//= $color_type[$fabric_data['gmts_color_id']];  ?></td> -->
							 <!-- <td rowspan="<?//= count($fabric_data['color_dtls'])+1;  ?>"><?//= $color_type[$fabric_data['color_type_id']];  ?></td> -->
							<? 
							$colortr=1;
							$color_wise_qty=0;
							$color_wise_amount=0;
							$fabric_img_mst='';

							foreach($fabric_data['color_dtls'] as $color_data){ 
								if($colortr!=1) echo '<tr>';
								$fabric_img_mst=$color_data['fabric_id'].'_'.$color_data['fabric_color_id'];
							?>
							<td><?= $color_library[$color_data['gmts_color_id']] ?></td>
							<td><?= $color_library[$color_data['fabric_color_id']]  ?></td>
							<td align="right"><?= number_format($color_data['wo_qnty'],2)  ?></td>
						 
							<td align="right">
							<? 
								$wo_qry_after_p_loss=$color_data['wo_qnty']-(($color_data['wo_qnty']*$color_data['process_loss_percent'])/100); 
								echo number_format($wo_qry_after_p_loss,2);
							
							//$process_loss=number_format($color_data['process_loss_percent']); 
							//$with_process_loss_quantity= number_format($color_data['wo_qnty'])-$process_loss;
							
							 ?></td> 
							 <?
							 if($show_comments==1){?>
							<td align="right"><?= $color_data['rate']  ?></td>
							<td align="right"><?= number_format($color_data['amount'],2)  ?></td>
							<?}?> 
							<td align="left"><?= $color_data['labdip_no']  ?></td>
							<? 
							$colortr++;
							$color_wise_qty+=$wo_qry_after_p_loss;
							$color_wise_amount+=$color_data['amount'];
							$color_wise_grand_qty+=$color_data['wo_qnty'];
							$color_wise_grand_amount+=$color_data['amount'];
							} ?>
							<tr>
							<th colspan="3" align="right">Fabric Total</th>
							<th align="right"><?= number_format($color_wise_qty,2) ?></th>
							
							<?
							if($show_comments==1){?>
							<th></th>
							<th align="right"><?= number_format($color_wise_amount,2) ?></th>
							<?}?>
							<th></th>
							</tr>
							
						<? 
							$fabrictr++;
						} ?>						
					</tr>
				<? } 

				if($currency_id==1){$paysa_sent="Paisa";} else if($currency_id==2){$paysa_sent="CENTS";}
			?>
			<tr>
				<th colspan="9" align="right">Grand Total</th>
				<th align="right"><?= number_format($color_wise_grand_qty,2) ?></th>
				<th></th>
					<?
				if($show_comments==1){?>
				<th align="right"><?= number_format($color_wise_grand_amount,2) ?></th>
				<?}?>
				<th></th>
			</tr>
			<tr>
				<th colspan="2" align="left">Total Amount (in word)</th>
				<?
				if($show_comments==1){?>
				<th colspan="11" align="left"><? echo number_to_words(def_number_format($color_wise_grand_amount,2,""),$currency[$currency_id],$paysa_sent); ?></th>
			
				<?}?>
			</tr>
		</table>
		<br><br>
       	<table  width="100%" class="rpt_table" style="margin:5px;font-size:16px;font-family:'Arial Narrow';"   border="1" cellpadding="0" cellspacing="0">
          <tr>
          <td><? echo get_spacial_instruction($txt_booking_no); ?></td>
          </tr>
        </table>
         <br/>
		 <?
            echo signature_table(290, $cbo_company_name, "1113px","",40,$inserted_by);
         ?>
    </div>

<?
}
if($action=="show_trim_booking_report9") //19868
{
	
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_fabric_natu=str_replace("'","",$cbo_fabric_natu);
	$cbo_fabric_source=str_replace("'","",$cbo_fabric_source);
	//$txt_job_no=str_replace("'","",$txt_job_no);
	$cbo_template_id=str_replace("'","",$cbo_template_id);
	$show_yarn_rate=str_replace("'","",$show_yarn_rate);
	$path=str_replace("'","",$path);
	if($path==1) $path="../../";

	// print_r($cbo_template_id);die;
	
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1 and master_tble_id='$cbo_company_name'",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');

	$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$brand_name_arr=return_library_array( "select id, brand_name from lib_buyer_brand ",'id','brand_name');
	//$user_name_arr=return_library_array( "select id, user_full_name from user_passwd ",'id','user_full_name');
	$user_name_arr=return_library_array( "select id, user_name from user_passwd ",'id','user_name');
	$team_leader_arr=return_library_array( "select id,team_leader_name from lib_marketing_team",'id','team_leader_name');
 
	//$location_name_arr=return_library_array( "select id,location_name from lib_location",'id','location_name');
	
	//$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	//$po_qnty_tot1=return_field_value( "sum(po_quantity)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	//wo_pre_cost_fabric_cost_dtls
	$pro_sub_dept_array=return_library_array( "select id,sub_department_name from lib_pro_sub_deparatment",'id','sub_department_name');
	?>
	<style type="text/css">
		@media print {
		    .pagebreak { page-break-before: always; } /* page-break-after works, as well */
		}
	</style>
	<div style="width:1000px" align="left">
    <?php
    	$lip_yarn_count=return_library_array( "select id,fabric_composition_id from lib_yarn_count_determina_mst where  status_active=1", "id", "fabric_composition_id");
		$fabric_composition=return_library_array( "select id,fabric_composition_name from lib_fabric_composition where  status_active=1", "id", "fabric_composition_name");
		
		$nameArray_approved = sql_select("select max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7");
		list($nameArray_approved_row) = $nameArray_approved;
		$nameArray_approved_date = sql_select("select b.approved_date as approved_date from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7 and b.approved_no='" . $nameArray_approved_row[csf('approved_no')] . "'");
		list($nameArray_approved_date_row) = $nameArray_approved_date;
		$nameArray_approved_comments = sql_select("select b.comments as comments from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7 and b.approved_no='" . $nameArray_approved_row[csf('approved_no')] . "'");
		list($nameArray_approved_comments_row) = $nameArray_approved_comments;

		$max_approve_date_data = sql_select("select min(b.approved_date) as approved_date,max(b.approved_date) as last_approve_date,max(b.un_approved_date) as un_approved_date from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7");
		$first_approve_date='';
		$last_approve_date='';
		$un_approved_date='';
		if(count($max_approve_date_data))
		{
			$last_approve_date=$max_approve_date_data[0][csf('last_approve_date')];
			$first_approve_date=$max_approve_date_data[0][csf('approved_date')];
			$un_approved_date=$max_approve_date_data[0][csf('un_approved_date')];
		}
		
		//if($txt_job_no!="") $location=return_field_value( "location_name", "wo_po_details_master","job_no='$txt_job_no'"); else $location="";
		$sql_loc=sql_select("select id,location_name,address from lib_location where company_id=$cbo_company_name");
		foreach($sql_loc as $row)
		{
			$location_name_arr[$row[csf('id')]]= $row[csf('location_name')];
			$location_address_arr[$row[csf('id')]]= $row[csf('address')];
		}		
		//$yes_no_sql=sql_select("select job_no,cons_process from  wo_pre_cost_fab_conv_cost_dtls where job_no='$txt_job_no'  and status_active=1 and is_deleted=0  order by id");
		$nameArray_sql=sql_select( "select a.booking_no, a.booking_date, a.supplier_id, a.currency_id, a.exchange_rate, a.attention, a.delivery_date, c.po_break_down_id, a.colar_excess_percent, a.cuff_excess_percent, a.delivery_date, a.is_apply_last_update, a.fabric_source, a.inserted_by,a.rmg_process_breakdown, a.insert_date, a.update_date, a.tagged_booking_no, a.uom, a.pay_mode, a.booking_percent,b.id as job_id, b.job_no, b.buyer_name, b.style_ref_no, b.gmts_item_id, b.order_uom, b.total_set_qnty, (b.job_quantity*b.total_set_qnty) as jobqtypcs, b.style_description, b.season_buyer_wise as season, b.product_dept, b.product_code, b.pro_sub_dep, b.dealing_marchant,b.factory_marchant, b.order_repeat_no, b.repeat_job_no, a.fabric_composition, a.remarks, a.sustainability_standard, b.brand_id, a.quality_level, a.fab_material, a.requisition_no, b.qlty_label, b.packing, b.job_no, a.proceed_knitting, a.proceed_dyeing,a.process,b.team_leader from wo_booking_mst a,wo_booking_dtls c, wo_po_details_master b where   c.booking_no=a.booking_no and c.job_no=b.job_no and a.booking_no=$txt_booking_no and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0");
		foreach($nameArray_sql as $row)
        {
			$job_id_allArr[$row[csf('job_id')]]=$row[csf('job_id')];
			$job_no_allArr[$row[csf('job_no')]]=$row[csf('job_no')];
			$po_id_allArr[$row[csf('po_break_down_id')]]=$row[csf('po_break_down_id')];
			
		}
		$job_id_all=implode(",",$job_id_allArr);
		$job_no_all=implode(",",$job_no_allArr);
		
		$peach=''; $brush=''; $fab_wash='';

		$emb_print=sql_select("select id, job_no, emb_name, emb_type from wo_pre_cost_embe_cost_dtls where  job_id in($job_id_all) and status_active=1 and is_deleted=0 and cons_dzn_gmts>0 and emb_name in (1,2,3) order by id");
		
		$emb_print_data=array();
		$type_array=array(0=>$blank_array,1=>$emblishment_print_type,2=>$emblishment_embroy_type,3=>$emblishment_wash_type,4=>$emblishment_spwork_type,5=>$emblishment_gmts_type,99=>$blank_array);
		
		foreach ($emb_print as $row) 
		{
			$emb_print_data[$row[csf('job_no')]][$row[csf('emb_name')]].=$type_array[$row[csf("emb_name")]][$row[csf('emb_type')]].",";
		}
		
		// echo "<pre>";
		// print_r();
		$service_process=sql_select("SELECT b.process from wo_booking_mst a join wo_booking_dtls b on a.id=b.booking_mst_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_no=$txt_booking_no and b.booking_type=3");
		foreach($service_process as $row){
			$process_arr[$row[csf('process')]]=$conversion_cost_head_array[$row[csf('process')]];
		}
		$process=implode(", ",$process_arr);
		//$process=$conversion_cost_head_array[$nameArray[0][csf('process')]];
		

		$nameArray=sql_select( "select a.booking_no, a.booking_date, a.supplier_id, a.currency_id, a.exchange_rate, a.attention, a.delivery_date, a.po_break_down_id, a.colar_excess_percent, a.cuff_excess_percent, a.delivery_date, a.is_apply_last_update, a.fabric_source, a.inserted_by,a.rmg_process_breakdown, a.insert_date, a.update_date, a.tagged_booking_no, a.uom, a.pay_mode, a.booking_percent, b.job_no,b.location_name, b.buyer_name, b.style_ref_no, b.gmts_item_id, b.order_uom, b.total_set_qnty, (b.job_quantity*b.total_set_qnty) as jobqtypcs, b.style_description, b.season_buyer_wise as season, b.product_dept, b.product_code, b.pro_sub_dep, b.dealing_marchant,b.factory_marchant, b.order_repeat_no, b.repeat_job_no, a.fabric_composition, a.remarks, a.sustainability_standard, b.brand_id, a.quality_level, a.fab_material, a.requisition_no, b.qlty_label, b.packing, b.job_no, a.proceed_knitting, a.proceed_dyeing,a.process,b.team_leader from wo_booking_mst a ,wo_po_details_master b where   a.job_no=b.job_no and b.id in(".implode(",",$job_id_allArr).") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	 
		 
		 

		$po_id_all=implode(",",$po_id_allArr);
		$job_no_str=$nameArray[0][csf('job_no')];
		$location=$nameArray[0][csf('location_name')];
		$tagged_booking_no=$nameArray[0][csf('tagged_booking_no')];
		$booking_uom=$nameArray[0][csf('uom')];
		$bookingup_date=$nameArray[0][csf('update_date')];
		$bookingins_date=$nameArray[0][csf('insert_date')];
		$delivery_date=$nameArray[0][csf('delivery_date')];
		$product_code=$nameArray[0][csf('product_code')];
		$requisition_no=$nameArray[0][csf('requisition_no')];
		$jobqtypcs=$nameArray[0][csf('jobqtypcs')];
		$inserted_by2=$user_name_arr[$nameArray[0][csf('inserted_by')]];
		$supplier_id=$nameArray[0][csf('supplier_id')];
		$pay_mode=$nameArray[0][csf('pay_mode')];
		$style_ref_no=$nameArray[0][csf('style_ref_no')];
		$team_leader=$team_leader_arr[$nameArray[0][csf('team_leader')]];
		$style_description=$nameArray[0][csf('style_description')];
		//$process=$conversion_cost_head_array[$nameArray[0][csf('process')]];

		$job_no_str=$nameArray[0][csf('job_no')];
	

		$po_shipment_date=sql_select("select  MIN(pub_shipment_date) as min_shipment_date,max(pub_shipment_date) as max_shipment_date from wo_po_break_down where id in(".$po_id_all.") order by shipment_date asc ");
         $min_shipment_date='';
         $max_shipment_date='';
         foreach ($po_shipment_date as $row) {
         	 $min_shipment_date=$row[csf('min_shipment_date')];
         	 $max_shipment_date=$row[csf('max_shipment_date')];
         	 break;
         }
       
  		ob_start();     
		?>	
											<!--    Header Company Information         -->
										
        <table width="1200" align="left" cellpadding="0" cellspacing="0" style="border:1px solid black; font-family:Arial Narrow;" >
            <tr>
                <td width="200" style="font-size:28px"><img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='50%' width='50%' /></td>
                <td width="1000">
                    <table width="100%" cellpadding="0" cellspacing="0"  style="position: relative;">
                        <tr>
                            <td align="center" style="font-size:28px;"> <?php echo $company_library[$cbo_company_name]; ?></td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:18px;position: relative;"><?=$location_address_arr[$location]; ?></td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:24px">
                            	<span style="float:center;"><b><strong> <font style="color:black">Service Booking For Dyeing </font></strong></b></span> 
                            </td>
                        </tr>
                        <?php /*?><tr>
                            <td align="center" style="font-size:20px">
							<?
							if(str_replace("'","",$id_approved_id) ==1){ ?>
                            <span style="font-size:20px; float:center;"><strong> <font style="color:green"> <? echo "[Approved]"; ?> </font></strong></span> 
                               <? }else{ ?>
								<span style="font-size:20px; float:center;"><strong> <font style="color:red"><? echo "[Not Approved]"; ?> </font></strong></span> 
							   <? } ?>
							  
                            </td>
							<td><strong style="background-color:yellow;padding:2%;font-size: 30px;"><?=str_replace("'","",$tagged_booking_no);;?></strong></td>
							
                        </tr><?php */?>
						
						
                    </table>
					
                </td>
            </tr>
        </table>
		<?
        $job_no=trim($txt_job_no,"'"); $total_set_qnty=0; $colar_excess_percent=0; $cuff_excess_percent=0; $rmg_process_breakdown=0; $booking_percent=0; $booking_po_id='';
		if($db_type==0)
        {
            $date_dif_cond="DATEDIFF(pub_shipment_date,po_received_date)";
            $group_concat_all="group_concat(grouping) as grouping, group_concat(file_no) as file_no";
        }
        else
        {
            $date_dif_cond="(pub_shipment_date-po_received_date)";
            $group_concat_all=" listagg(cast(grouping as varchar2(4000)),',') within group (order by grouping) as grouping,
                                listagg(cast(file_no as varchar2(4000)),',') within group (order by file_no) as file_no  ";
        }
        $po_number_arr=array(); $po_ship_date_arr=array(); $shipment_date=""; $po_no=""; $po_received_date=""; $shiping_status="";
        $po_sql=sql_select("select id, po_number,grouping, pub_shipment_date, MIN(pub_shipment_date) as mpub_shipment_date, MIN(po_received_date) as po_received_date, MIN(insert_date) as insert_date, plan_cut, po_quantity, shiping_status, $date_dif_cond as date_diff,min(factory_received_date) as factory_received_date, $group_concat_all,status_active from wo_po_break_down where id in(".$po_id_all.") group by id, po_number, grouping,pub_shipment_date, plan_cut, po_quantity, shiping_status, po_received_date,status_active ");
		 
        $to_ship=0; $fp_ship=0; $f_ship=0;
        foreach($po_sql as $row)
        {
            $po_qnty_tot+=$row[csf('plan_cut')];
            $po_qnty_tot1+=$row[csf('po_quantity')];
            $po_number_arr[$row[csf('id')]]=$row[csf('po_number')];
            $po_ship_date_arr[$row[csf('id')]]=$row[csf('pub_shipment_date')];
            $po_num_arr[$row[csf('id')]]=$row[csf('po_number')];
            $po_no.=$row[csf('po_number')].", ";
            $shipment_date.=change_date_format($row[csf('mpub_shipment_date')],'dd-mm-yyyy','-').", ";
            $lead_time.=$row[csf('date_diff')].",";
            $po_received_date=change_date_format($row[csf('po_received_date')],'dd-mm-yyyy','-');
            $factory_received_date=change_date_format($row[csf('factory_received_date')],'dd-mm-yyyy','-');
            $grouping.=$row[csf('grouping')].",";
            $file_no.=$row[csf('file_no')].",";
			if($row[csf('status_active')]==3){
				$cancel_po_no[$row[csf('po_number')]]=$row[csf('po_number')];
			}
			 
			
			$daysInHand.=(datediff('d',date('d-m-Y',time()),$row[csf('mpub_shipment_date')])-1).",";
			
			if($bookingup_date=="" || $bookingup_date=="0000-00-00 00:00:00")
			{
				$booking_date=$bookingins_date;
			}
			$WOPreparedAfter.=(datediff('d',$row[csf('insert_date')],$booking_date)-1).",";

			if($row[csf('shiping_status')]==1) {
				$shiping_status.= "FP".",";
				$to_ship++;
				$fp_ship++;
			}
			else if($row[csf('shiping_status')]==2){
				$shiping_status.= "PD".",";
				$to_ship++;
			} 
			else if($row[csf('shiping_status')]==3){
				$shiping_status.= "FS".",";
				$to_ship++;
				$f_ship++;
			} 
        }
		$grouping=rtrim(",",$grouping);
		$ref=implode(",",array_filter(array_unique(explode(',',$grouping))));
        if($to_ship==$f_ship) $shiping_status= "<b style='color:green'>Full shipped</b>";
        else if($to_ship==$fp_ship) $shiping_status= "<b style='color:red'>Full Pending</b>";
        else $shiping_status= "<b style='color:red'>Partial Delivery</b>";
		
		$po_no=implode(",",array_filter(array_unique(explode(",",$po_no))));
		$shipment_date=implode(",",array_filter(array_unique(explode(",",$shipment_date))));
		$lead_time=implode(",",array_filter(array_unique(explode(",",$lead_time))));
		$po_received_date=implode(",",array_filter(array_unique(explode(",",$po_received_date))));
		$factory_received_date=implode(",",array_filter(array_unique(explode(",",$factory_received_date))));
		$grouping=implode(",",array_filter(array_unique(explode(",",$grouping))));
		$file_no=implode(",",array_filter(array_unique(explode(",",$file_no))));
		
		$daysInHand=implode(",",array_filter(array_unique(explode(",",$daysInHand))));
		$WOPreparedAfter=implode(",",array_filter(array_unique(explode(",",$WOPreparedAfter))));
		$shiping_status=implode(",",array_filter(array_unique(explode(",",$shiping_status))));
		
            $total_set_qnty=$nameArray[0][csf('total_set_qnty')];
            $colar_excess_percent=$nameArray[0][csf('colar_excess_percent')];
            $cuff_excess_percent=$nameArray[0][csf('cuff_excess_percent')];
            $rmg_process_breakdown=$nameArray[0][csf('rmg_process_breakdown')];
            
            $booking_percent=$nameArray[0][csf('booking_percent')];
			$booking_po_id=$nameArray[0][csf('po_break_down_id')];
			?>
			<table width="1200" class="rpt_table"  border="1" align="left" cellpadding="0"  cellspacing="0" rules="all"  style="font-size:18px; font-family:Arial Narrow;" >
				<tr>
					<td width="150"><b>Service Provider </b></td>		 
					<td width="250"> <span style="font-size:18px"><?
					if($pay_mode==5 || $pay_mode==3){
						echo $company_library[$nameArray[0][csf('supplier_id')]];
						}
						else{
						echo $supplier_name_arr[$nameArray[0][csf('supplier_id')]];
						}
					?></span> </td>
					<td width="150"><span style="font-size:18px"><b>Supplier Address</b></span></td>
					<td width="250"><span style="font-size:18px"><?

					$supplier_id=$nameArray[0][csf('supplier_id')];
				
					if($pay_mode==5 || $pay_mode==3){
						$nameArray_addres=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$supplier_id");
						foreach ($nameArray_addres as $result)
						{
							$company_address= "Plot No:".$result[csf('plot_no')].",Level No:".$result[csf('level_no')].",Road No:".$result[csf('road_no')].",Block No:".$result[csf('block_no')].",City No:".$result[csf('city')].",Zip Code:".$result[csf('zip_code')].",Province No:".$result[csf('province')].",Country:".$country_arr[$result[csf('country_id')]]; 
						}
						echo $company_address;
						}
						else{
						echo $supplier_address_arr[$nameArray[0][csf('supplier_id')]];
						}
					
					?> </span> </td>
					<td width="150"><span style="font-size:18px"><b>Work Order No</b></span></td>
					<td width="250"><span style="font-size:18px"><?=$nameArray[0][csf('booking_no')];?> </span> </td>
					
				
				</tr>
				<tr>
					<td width="150"><b>Job No/IR</b></td>
					
					<?
					$job=trim($job_no_all,"'");
					$ir=rtrim($ref,',');
					?>
					<td width="250"> <span style="font-size:18px"><? echo $job.'  /  '.$ir; if(!empty($revised_no)){ ?>&nbsp;<span style="color: red;">/&nbsp;<? echo $revised_no; }?></span></span> </td>
					<td width="150"><b>Dealing Merchandiser</b></td>
					<td width="250"><? echo $marchentrArr[$nameArray[0][csf('dealing_marchant')]]; ?></td>
					<td width="150"><span style="font-size:18px"><b>Team Leader</b></span></td>
					<td width="250">&nbsp;<span style="font-size:18px"> <?=$team_leader;    ?></span></td>	
				</tr>
				<tr>		
					<td width="150"><span style="font-size:18px"><b>Buyer</b></span></td>
					<td width="250">&nbsp;<span style="font-size:18px"><? echo $buyer_name_arr[$nameArray[0][csf('buyer_name')]]; ?></span></td>
					<td width="100"><b>Brand</b></td>
					<td width="140"><?php echo $brand_name_arr[$nameArray[0][csf('brand_id')]]; ?></td>
					<td width="100"><b></b></td>
					<td width="140"><?php //echo str_replace("'","",$tagged_booking_no); ?></td>
				</tr>
				<tr>
					<td width="150" style="font-size:16px;"><b>Style</b></td>
					<td width="250"style="font-size:16px;" >&nbsp;<? echo $style_ref_no; ?></td>				
					<td width="150"><span style="font-size:18px"><b>Garments Item</b></span></td>
					<td width="250">&nbsp;<span style="font-size:18px"> <?
                        $gmts_item_name="";
                        $gmts_item=explode(',',$nameArray[0][csf('gmts_item_id')]);
                        for($g=0;$g<=count($gmts_item); $g++)
                        {
                            $gmts_item_name.= $garments_item[$gmts_item[$g]].",";
                        }
                        echo rtrim($gmts_item_name,',');
                        ?></span></td>	
					
					<td width="150"><b>Process</b></td>
					<td width="250"><? echo $process; ?></td>
				</tr>

						<? 
							$sql_fab="SELECT a.lib_yarn_count_deter_id AS determin_id, a.construction
							    FROM wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d
							   WHERE a.job_id = b.job_id AND a.id = b.pre_cost_fabric_cost_dtls_id AND a.id = d.pre_cost_fabric_cost_dtls_id AND b.po_break_down_id = d.po_break_down_id AND b.color_size_table_id = d.color_size_table_id AND b.pre_cost_fabric_cost_dtls_id = d.pre_cost_fabric_cost_dtls_id AND d.booking_no = $txt_booking_no AND a.status_active = 1 AND d.status_active = 1 AND d.is_deleted = 0 and a.body_part_id in (1,20) group by a.lib_yarn_count_deter_id , a.construction";
							//echo $sql_fab;
							$res_fab=sql_select($sql_fab);
							$des='';
							foreach ($res_fab as $row) 
							{
								if(!empty($des))
								{
									$des."***";
								}
								$des.=$row[csf('construction')] . " ". $fabric_composition[$lip_yarn_count[$row[csf('determin_id')]]].",";
							}
							//echo implode(",", array_unique(explode("***", $des)));
						?>
			</table>
			<br>
			<br>
			<br>
			
			<?
		
			
	  	?>
		<br>
		<br>
	
		<?php
		$fabric_desc_arr=array();

		$wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select id,fabric_description,cons_process from wo_pre_cost_fab_conv_cost_dtls  where  job_id in($job_id_all) ");
		foreach( $wo_pre_cost_fab_conv_cost_dtls_id as $row_wo_pre_cost_fab_conv_cost_dtls_id)
		{
			if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]!=0)
			{
				$fabric_description=sql_select("select id,body_part_id,body_part_type,color_type_id,fabric_description,construction,composition,gsm_weight,width_dia_type from  wo_pre_cost_fabric_cost_dtls 
				where  id='".$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]."'");
				list($fabric_description_row)=$fabric_description;

				$fabric_desc_arr[$fabric_description_row[csf("id")]]['body_part_id']=$fabric_description_row[csf("body_part_id")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['construction']=$fabric_description_row[csf("construction")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['composition']=$fabric_description_row[csf("composition")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['color_type_id']=$fabric_description_row[csf("color_type_id")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['gsm_weight']=$fabric_description_row[csf("gsm_weight")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['width_dia_type']=$fabric_description_row[csf("width_dia_type")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['body_part_type']=$fabric_description_row[csf("body_part_type")];
				
			}
			if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]==0)
			{
				
				$fabric_description=sql_select("select id,body_part_id,body_part_type,color_type_id,fabric_description,construction,composition,gsm_weight,width_dia_type from  wo_pre_cost_fabric_cost_dtls 
				where  job_id in($job_id_all)");

				foreach( $fabric_description as $fabric_description_row)
				{
				
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['body_part_id']=$fabric_description_row[csf("body_part_id")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['construction']=$fabric_description_row[csf("construction")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['composition']=$fabric_description_row[csf("composition")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['color_type_id']=$fabric_description_row[csf("color_type_id")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['gsm_weight']=$fabric_description_row[csf("gsm_weight")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['width_dia_type']=$fabric_description_row[csf("width_dia_type")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['body_part_type']=$fabric_description_row[csf("body_part_type")];
		
				}
				
			}


		}


		$pre_cons_data=sql_select("select  id, po_break_down_id, color_number_id, gmts_sizes, dia_width, item_size, cons, process_loss_percent, requirment, pcs, color_size_table_id, rate, amount, remarks ,pre_cost_fabric_cost_dtls_id  as fab_desc_id from wo_pre_cos_fab_co_avg_con_dtls where job_id in($job_id_all)  and po_break_down_id in (".$po_id_all.") order by id");


		foreach($pre_cons_data as $row){

			$color_wise_data[$row[csf("po_break_down_id")]][$row[csf("fab_desc_id")]][$row[csf("color_number_id")]]['finsh_cons']=$row[csf("cons")];
			$color_wise_data[$row[csf("po_break_down_id")]][$row[csf("fab_desc_id")]][$row[csf("color_number_id")]]['gray_cons']=$row[csf("requirment")];
			$color_wise_data[$row[csf("po_break_down_id")]][$row[csf("fab_desc_id")]][$row[csf("color_number_id")]]['gray_cons']=$row[csf("requirment")];
			$color_wise_data[$row[csf("po_break_down_id")]][$row[csf("fab_desc_id")]][$row[csf("color_number_id")]]['dia_width']=$row[csf("dia_width")];
			$color_wise_data[$row[csf("po_break_down_id")]][$row[csf("fab_desc_id")]][$row[csf("color_number_id")]]['process_loss_percent']=$row[csf("process_loss_percent")];

		}


		$nameArray_fabric_description= sql_select("select c.id as conv_dtl_id,b.job_no,b.po_break_down_id as po_id,b.sensitivity,b.uom,b.gmts_color_id,b.fabric_color_id,b.gmts_size,(b.wo_qnty) as wo_qnty,b.rate,b.fin_fab_qnty,b.process_loss_percent,b.labdip_no,b.delivery_date,b.delivery_end_date,
		(b.amount) as amount,c.charge_unit,c.fabric_description  as fab_desc_id	from wo_pre_cost_fab_conv_cost_dtls c,wo_booking_dtls b where b.pre_cost_fabric_cost_dtls_id=c.id and b.job_no=c.job_no and b.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.booking_type=3 and c.job_id in($job_id_all)  and b.booking_no =$txt_booking_no order by c.fabric_description");
	
		$body_part_type_arr=array();
		foreach ($nameArray_fabric_description as $row) {	

			if($row[csf('wo_qnty')]>0){

				$body_part_id=$fabric_desc_arr[$row[csf("fab_desc_id")]]['body_part_id'];
				$body_part_type=$fabric_desc_arr[$row[csf("fab_desc_id")]]['body_part_type'];
				$construction=$fabric_desc_arr[$row[csf("fab_desc_id")]]['construction'];
				$composition=$fabric_desc_arr[$row[csf("fab_desc_id")]]['composition'];
				$color_type_id=$fabric_desc_arr[$row[csf("fab_desc_id")]]['color_type_id'];
				$gsm_weight=$fabric_desc_arr[$row[csf("fab_desc_id")]]['gsm_weight'];
				$width_dia_type=$fabric_desc_arr[$row[csf("fab_desc_id")]]['width_dia_type'];
				if($body_part_type==40 || $body_part_type==50){
					$body_part_type_arr[$body_part_type]=$body_part_type;
				}


				$finsh_cons=$color_wise_data[$row[csf("po_id")]][$row[csf("fab_desc_id")]][$row[csf("gmts_color_id")]]['finsh_cons'];
				$gray_cons=$color_wise_data[$row[csf("po_id")]][$row[csf("fab_desc_id")]][$row[csf("gmts_color_id")]]['gray_cons'];
				$dia_width=$color_wise_data[$row[csf("po_id")]][$row[csf("fab_desc_id")]][$row[csf("gmts_color_id")]]['dia_width'];
				$process_loss_percent=$color_wise_data[$row[csf("po_id")]][$row[csf("fab_desc_id")]][$row[csf("gmts_color_id")]]['process_loss_percent'];
			
		
				$grouping_item=$row[csf('fabric_color_id')].'*'.$body_part_id.'*'.$construction.'*'.$composition.'*'.$gsm_weight.'*'.$width_dia_type.'*'.$color_type_id;	
					$pp=100+$process_loss_percent;
				$fabric_data_arr[$row[csf('gmts_color_id')]][$grouping_item]['gmts_color_id'] = $row[csf('gmts_color_id')];
				$fabric_data_arr[$row[csf('gmts_color_id')]][$grouping_item]['fabric_color_id'] = $row[csf('fabric_color_id')];
				$fabric_data_arr[$row[csf('gmts_color_id')]][$grouping_item]['labdip_no'] =  $row[csf('labdip_no')];
				$fabric_data_arr[$row[csf('gmts_color_id')]][$grouping_item]['delivery_date'] =  $row[csf('delivery_date')];
				$fabric_data_arr[$row[csf('gmts_color_id')]][$grouping_item]['delivery_end_date'] =  $row[csf('delivery_end_date')];
				$fabric_data_arr[$row[csf('gmts_color_id')]][$grouping_item]['body_part_id'] = $body_part_id;
				$fabric_data_arr[$row[csf('gmts_color_id')]][$grouping_item]['fabric_des'] = $construction.','.$composition;
				$fabric_data_arr[$row[csf('gmts_color_id')]][$grouping_item]['gsm'] = $gsm_weight;
				$fabric_data_arr[$row[csf('gmts_color_id')]][$grouping_item]['fabric_dia'] = $dia_width.",".$fabric_typee[$width_dia_type];
				$fabric_data_arr[$row[csf('gmts_color_id')]][$grouping_item]['color_type_id'] = $color_type_id;
				$fabric_data_arr[$row[csf('gmts_color_id')]][$grouping_item]['finsh_cons'] = $finsh_cons;
				$fabric_data_arr[$row[csf('gmts_color_id')]][$grouping_item]['gray_cons'] = $gray_cons;
				$fabric_data_arr[$row[csf('gmts_color_id')]][$grouping_item]['fin_fab_qnty'] +=$row[csf('fin_fab_qnty')];
				$fabric_data_arr[$row[csf('gmts_color_id')]][$grouping_item]['process_loss'] =$row[csf('process_loss_percent')];
				$fabric_data_arr[$row[csf('gmts_color_id')]][$grouping_item]['grey_fab_qnty'] += $row[csf('wo_qnty')];
				$fabric_data_arr[$row[csf('gmts_color_id')]][$grouping_item]['amount'] += $row[csf('amount')];
				$fabric_data_arr[$row[csf('gmts_color_id')]][$grouping_item]['rate']= $row[csf('rate')];
				$fabric_data_arr[$row[csf('gmts_color_id')]][$grouping_item]['process_loss_percent'] = $process_loss_percent;
		   }
	
		}
		$body_part_type_ids=implode(",",$body_part_type_arr);
		// echo $body_part_type_ids;
		$gmts_colorWiseArr=array();
		 foreach ($fabric_data_arr as $gmts_id=>$gmts_data_arr) 
		 {  
			   $gmt_color_span=0;   		  		
			 foreach ($gmts_data_arr as $fabric_id => $value) 
			 {
				 $gmt_color_span++;
			 }
			 $gmts_colorSpanArr[$gmts_id]=$gmt_color_span;
				 
		 }
	
		?>
		 <table class="rpt_table" width="1200"  align="left" border="1" cellpadding="0" cellspacing="0" rules="all" style="font-size: 18px;">
			 <tr>
				 <th>Gmts Colors</th>
				 <th>Fabric Color</th>	
				 <th>LD No</th>			
				 <th>Body Part</th>
				 <th>Fabrication</th>
				 <th>GSM</th>
				 <th>Dia Type with </br> Fabric Dia</th>			
				 <th>Color Type</th>
				 <th>Work Order Qty</th>
                  <th>Process Loss%</th>
                  <th>Req. Fin Qty</th>
                   
				 <? if ($show_comments==1) { ?>
				 <th>Rate</th>
				 <th>Amount</th>
				 <? } ?>
				 <th>TNA Start Date</th>
				 <th>TNA End Date</th>
			 </tr>
			 <? $grey_fab_qnty=$tot_fin_fab_qnty=0;
			 foreach ($fabric_data_arr as $gmts_id=>$gmts_data_arr) {  
			 $jj=1;     		  		
				 foreach ($gmts_data_arr as $fabric_id => $value) {
						 $amount+=$value['amount'];   		 	
						 $grey_fab_qnty+=$value['grey_fab_qnty'];  
						  $tot_fin_fab_qnty+=$value['fin_fab_qnty'];   
						   $gmts_colorRowspan=$gmts_colorSpanArr[$gmts_id];	
						  // echo $gmts_colorRowspan.'d,';	 	
						
						   ?>
						  <tr>
						   <? if($jj==1){
                               ?>
                             <td rowspan="<? echo $gmts_colorRowspan; ?>"><? echo $color_library[$gmts_id] ?></td>
                             <?
							   }
							 ?>
							 <td><? echo $color_library[$value['fabric_color_id']] ?></td>
							 <td><? echo $value['labdip_no'] ?></td>
							 <td><? echo $body_part[$value['body_part_id']] ?></td>
							 <td><? echo $value['fabric_des'] ?></td>
							 <td align="center"><? echo $value['gsm'] ?></td>
							 <td><? echo $value['fabric_dia'] ?></td>
							 <td align="center"><? echo $color_type[$value['color_type_id']] ?></td>		     			
							 <td align="center"><? echo fn_number_format($value['grey_fab_qnty'],4) ; ?></td>
                             <td align="center"><? echo fn_number_format($value['process_loss'],4) ; ?></td>
                             <td align="center"><? echo fn_number_format($value['fin_fab_qnty'],4) ; ?></td>
							 <? if ($show_comments==1) { ?>
							 <td align="center"><? echo $value['rate'] ; ?></td>
							 <td align="center"><? echo fn_number_format($value['amount'],4) ; ?></td>
							 <? } ?>
							 <td  align="center"><? echo change_date_format($value['delivery_date']) ?></td>
							 <td  align="center"><? echo change_date_format($value['delivery_end_date']) ?></td>
						 </tr>
						  <?  
						 
						  $jj++;
					  //}
				 }
			 } 
			 ?>
			 <tr>
				 <th align="right" colspan="8">Total</th>
				 <th align="right" ><? echo number_format($grey_fab_qnty);  ?></th>
                  <th align="right" ><? //echo number_format($grey_fab_qnty);  ?></th>
                   <th align="right" ><? echo number_format($tot_fin_fab_qnty);  ?></th>
                   
				 <? if ($show_comments==1) { ?>
				 <th>&nbsp;</th>
				 <th align="right" ><?echo number_format($amount);  ?></th>
				 <? } ?>
				 <th>&nbsp;</th>
				 <th>&nbsp;</th>

			 </tr>
		 </table>
		  <br/>
		  <br>
		  <br>
		  <br> 
		<? 
       	echo get_spacial_instruction($txt_booking_no,'1200px',229);
        ?>
        
        <div ><?
		    echo signature_table(290, $cbo_company_name, "1200px",$cbo_template_id,"3px");
            
         ?></div>
		<br> 
		    

	   </div>
       <?
	$emailBody=ob_get_contents();

	if($is_mail_send==1){		
		$sql = "SELECT c.email_address as EMAIL FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=87 and b.mail_user_setup_id=c.id and a.company_id =$cbo_company_name";
		$mail_sql_res=sql_select($sql);
		
		$mailArr=array();
		foreach($mail_sql_res as $row)
		{
			$mailArr[$row[EMAIL]]=$row[EMAIL]; 
		}
		
		$supplier_id=$nameArray[0][csf('supplier_id')];
		$supplier_mail=return_field_value("email", "lib_supplier", "status_active=1 and is_deleted=0 and id=$supplier_id ");

		
		$mailArr=array();
		if($mail_id!=''){$mailArr[]=$mail_id;}
		if($supplier_mail_arr[$supplier_id]!=''){$mailArr[]=$supplier_mail;}
		
		$to=implode(',',$mailArr);
		$subject="Fabric Booking Auto Mail";
		
		if($to!=""){
			require '../../../vendor/phpmailer/phpmailer/PHPMailerAutoload.php';
			require_once('../../../auto_mail/setting/mail_setting.php');
			$header=mailHeader();
			sendMailMailer( $to, $subject, $emailBody );
		}
	}
	exit();
}
if($action=="show_trim_booking_report10") //MD. SAKIBUL ISLAM
{
	
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_fabric_natu=str_replace("'","",$cbo_fabric_natu);
	$cbo_fabric_source=str_replace("'","",$cbo_fabric_source);
	//$txt_job_no=str_replace("'","",$txt_job_no);
	$cbo_template_id=str_replace("'","",$cbo_template_id);
	$show_yarn_rate=str_replace("'","",$show_yarn_rate);
	$path=str_replace("'","",$path);
	if($path==1) $path="../../";

	// print_r($cbo_template_id);die;
	
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1 and master_tble_id='$cbo_company_name'",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');

	$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$brand_name_arr=return_library_array( "select id, brand_name from lib_buyer_brand ",'id','brand_name');
	//$user_name_arr=return_library_array( "select id, user_full_name from user_passwd ",'id','user_full_name');
	$user_name_arr=return_library_array( "select id, user_name from user_passwd ",'id','user_name');
	$team_leader_arr=return_library_array( "select id,team_leader_name from lib_marketing_team",'id','team_leader_name');
 
	//$location_name_arr=return_library_array( "select id,location_name from lib_location",'id','location_name');
	
	//$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	//$po_qnty_tot1=return_field_value( "sum(po_quantity)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	//wo_pre_cost_fabric_cost_dtls
	$pro_sub_dept_array=return_library_array( "select id,sub_department_name from lib_pro_sub_deparatment",'id','sub_department_name');
	?>
	<style type="text/css">
		@media print {
		    .pagebreak { page-break-before: always; } /* page-break-after works, as well */
		}
	</style>
	<div style="width:1000px" align="left">
    <?php
    	$lip_yarn_count=return_library_array( "select id,fabric_composition_id from lib_yarn_count_determina_mst where  status_active=1", "id", "fabric_composition_id");
		$fabric_composition=return_library_array( "select id,fabric_composition_name from lib_fabric_composition where  status_active=1", "id", "fabric_composition_name");
		
		$nameArray_approved = sql_select("select max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7  and a.entry_form=229 ");
		list($nameArray_approved_row) = $nameArray_approved;
		$nameArray_approved_date = sql_select("select b.approved_date as approved_date from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7  and a.entry_form=229  and b.approved_no='" . $nameArray_approved_row[csf('approved_no')] . "'");
		list($nameArray_approved_date_row) = $nameArray_approved_date;
		$nameArray_approved_comments = sql_select("select b.comments as comments from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7  and a.entry_form=229  and b.approved_no='" . $nameArray_approved_row[csf('approved_no')] . "'");
		list($nameArray_approved_comments_row) = $nameArray_approved_comments;

		$max_approve_date_data = sql_select("select min(b.approved_date) as approved_date,max(b.approved_date) as last_approve_date,max(b.un_approved_date) as un_approved_date from wo_booking_mst a, approval_history b where a.id=b.mst_id  and a.entry_form=229  and a.booking_no=$txt_booking_no and b.entry_form=7");
		$first_approve_date='';
		$last_approve_date='';
		$un_approved_date='';
		if(count($max_approve_date_data))
		{
			$last_approve_date=$max_approve_date_data[0][csf('last_approve_date')];
			$first_approve_date=$max_approve_date_data[0][csf('approved_date')];
			$un_approved_date=$max_approve_date_data[0][csf('un_approved_date')];
		}
		
		//if($txt_job_no!="") $location=return_field_value( "location_name", "wo_po_details_master","job_no='$txt_job_no'"); else $location="";
		$sql_loc=sql_select("select id,location_name,address from lib_location where company_id=$cbo_company_name");
		foreach($sql_loc as $row)
		{
			$location_name_arr[$row[csf('id')]]= $row[csf('location_name')];
			$location_address_arr[$row[csf('id')]]= $row[csf('address')];
		}		
		//$yes_no_sql=sql_select("select job_no,cons_process from  wo_pre_cost_fab_conv_cost_dtls where job_no='$txt_job_no'  and status_active=1 and is_deleted=0  order by id");
		$nameArray_sql=sql_select( "select a.booking_no, a.booking_date, a.supplier_id, a.currency_id, a.exchange_rate, a.attention, a.delivery_date, c.po_break_down_id, a.colar_excess_percent, a.cuff_excess_percent, a.delivery_date, a.is_apply_last_update, a.fabric_source, a.inserted_by,a.rmg_process_breakdown, a.insert_date, a.update_date, a.tagged_booking_no, a.uom, a.pay_mode, a.booking_percent,b.id as job_id, b.job_no, b.buyer_name, b.style_ref_no, b.gmts_item_id, b.order_uom, b.total_set_qnty, (b.job_quantity*b.total_set_qnty) as jobqtypcs, b.style_description, b.season_buyer_wise as season, b.product_dept, b.product_code, b.pro_sub_dep, b.dealing_marchant,b.factory_marchant, b.order_repeat_no, b.repeat_job_no, a.fabric_composition, a.remarks, a.sustainability_standard, b.brand_id, a.quality_level, a.fab_material, a.requisition_no, b.qlty_label, b.packing, b.job_no, a.proceed_knitting, a.proceed_dyeing,a.process,b.team_leader from wo_booking_mst a,wo_booking_dtls c, wo_po_details_master b where   c.booking_no=a.booking_no and c.job_no=b.job_no and c.booking_mst_id=a.id and a.entry_form=229 and a.booking_no=$txt_booking_no and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0");
		foreach($nameArray_sql as $row)
        {
			$job_id_allArr[$row[csf('job_id')]]=$row[csf('job_id')];
			$job_no_allArr[$row[csf('job_no')]]=$row[csf('job_no')];
			$po_id_allArr[$row[csf('po_break_down_id')]]=$row[csf('po_break_down_id')];
			
		}
		$job_id_all=implode(",",$job_id_allArr);
		$job_no_all=implode(",",$job_no_allArr);
		
		$peach=''; $brush=''; $fab_wash='';

		$emb_print=sql_select("select id, job_no, emb_name, emb_type from wo_pre_cost_embe_cost_dtls where  job_id in($job_id_all) and status_active=1 and is_deleted=0 and cons_dzn_gmts>0 and emb_name in (1,2,3) order by id");
		
		$emb_print_data=array();
		$type_array=array(0=>$blank_array,1=>$emblishment_print_type,2=>$emblishment_embroy_type,3=>$emblishment_wash_type,4=>$emblishment_spwork_type,5=>$emblishment_gmts_type,99=>$blank_array);
		
		foreach ($emb_print as $row) 
		{
			$emb_print_data[$row[csf('job_no')]][$row[csf('emb_name')]].=$type_array[$row[csf("emb_name")]][$row[csf('emb_type')]].",";
		}
		
		// echo "<pre>";
		// print_r();
		$service_process=sql_select("SELECT b.process from wo_booking_mst a join wo_booking_dtls b on a.id=b.booking_mst_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_no=$txt_booking_no and b.booking_type=3");
		foreach($service_process as $row){
			$process_arr[$row[csf('process')]]=$conversion_cost_head_array[$row[csf('process')]];
		}
		$process=implode(", ",$process_arr);
		//$process=$conversion_cost_head_array[$nameArray[0][csf('process')]];
		

		$nameArray=sql_select( "select a.booking_no, a.booking_date, a.supplier_id, a.currency_id, a.exchange_rate, a.attention, a.delivery_date, a.po_break_down_id, a.colar_excess_percent, a.cuff_excess_percent, a.delivery_date, a.is_apply_last_update, a.fabric_source, a.inserted_by,a.rmg_process_breakdown, a.insert_date, a.update_date, a.tagged_booking_no, a.uom, a.pay_mode, a.booking_percent, b.job_no,b.location_name, b.buyer_name, b.style_ref_no, b.gmts_item_id, b.order_uom, b.total_set_qnty, (b.job_quantity*b.total_set_qnty) as jobqtypcs, b.style_description, b.season_buyer_wise as season, b.product_dept, b.product_code, b.pro_sub_dep, b.dealing_marchant,b.factory_marchant, b.order_repeat_no, b.repeat_job_no, a.fabric_composition, a.remarks, a.sustainability_standard, b.brand_id, a.quality_level, a.fab_material, a.requisition_no, b.qlty_label, b.packing, b.job_no, a.proceed_knitting, a.proceed_dyeing,a.process,b.team_leader from wo_booking_mst a ,wo_po_details_master b where   a.job_no=b.job_no and b.id in(".implode(",",$job_id_allArr).") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	 
		 
		 

		$po_id_all=implode(",",$po_id_allArr);
		$job_no_str=$nameArray[0][csf('job_no')];
		$location=$nameArray[0][csf('location_name')];
		$tagged_booking_no=$nameArray[0][csf('tagged_booking_no')];
		$booking_uom=$nameArray[0][csf('uom')];
		$bookingup_date=$nameArray[0][csf('update_date')];
		$bookingins_date=$nameArray[0][csf('insert_date')];
		$delivery_date=$nameArray[0][csf('delivery_date')];
		$product_code=$nameArray[0][csf('product_code')];
		$requisition_no=$nameArray[0][csf('requisition_no')];
		$jobqtypcs=$nameArray[0][csf('jobqtypcs')];
		$inserted_by2=$user_name_arr[$nameArray[0][csf('inserted_by')]];
		$supplier_id=$nameArray[0][csf('supplier_id')];
		$pay_mode=$nameArray[0][csf('pay_mode')];
		$style_ref_no=$nameArray[0][csf('style_ref_no')];
		$team_leader=$team_leader_arr[$nameArray[0][csf('team_leader')]];
		$style_description=$nameArray[0][csf('style_description')];
		//$process=$conversion_cost_head_array[$nameArray[0][csf('process')]];

		$job_no_str=$nameArray[0][csf('job_no')];
	

		$po_shipment_date=sql_select("select  MIN(pub_shipment_date) as min_shipment_date,max(pub_shipment_date) as max_shipment_date from wo_po_break_down where id in(".$po_id_all.") order by shipment_date asc ");
         $min_shipment_date='';
         $max_shipment_date='';
         foreach ($po_shipment_date as $row) {
         	 $min_shipment_date=$row[csf('min_shipment_date')];
         	 $max_shipment_date=$row[csf('max_shipment_date')];
         	 break;
         }
       
  		ob_start();     
		?>	
											<!--    Header Company Information         -->
										
        <table width="1200" align="left" cellpadding="0" cellspacing="0" style="border:1px solid black; font-family:Arial Narrow;" >
            <tr>
                <td width="200" style="font-size:28px"><img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='50%' width='50%' /></td>
                <td width="1000">
                    <table width="100%" cellpadding="0" cellspacing="0"  style="position: relative;">
                        <tr>
                            <td align="center" style="font-size:28px;"> <?php echo $company_library[$cbo_company_name]; ?></td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:18px;position: relative;"><?=$location_address_arr[$location]; ?></td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:24px">
                            	<span style="float:center;"><b><strong> <font style="color:black">Service Booking For Dyeing </font></strong></b></span> 
                            </td>
                        </tr>
                        <?php /*?><tr>
                            <td align="center" style="font-size:20px">
							<?
							if(str_replace("'","",$id_approved_id) ==1){ ?>
                            <span style="font-size:20px; float:center;"><strong> <font style="color:green"> <? echo "[Approved]"; ?> </font></strong></span> 
                               <? }else{ ?>
								<span style="font-size:20px; float:center;"><strong> <font style="color:red"><? echo "[Not Approved]"; ?> </font></strong></span> 
							   <? } ?>
							  
                            </td>
							<td><strong style="background-color:yellow;padding:2%;font-size: 30px;"><?=str_replace("'","",$tagged_booking_no);;?></strong></td>
							
                        </tr><?php */?>
						
						
                    </table>
					
                </td>
            </tr>
        </table>
		<?
        $job_no=trim($txt_job_no,"'"); $total_set_qnty=0; $colar_excess_percent=0; $cuff_excess_percent=0; $rmg_process_breakdown=0; $booking_percent=0; $booking_po_id='';
		if($db_type==0)
        {
            $date_dif_cond="DATEDIFF(pub_shipment_date,po_received_date)";
            $group_concat_all="group_concat(grouping) as grouping, group_concat(file_no) as file_no";
        }
        else
        {
            $date_dif_cond="(pub_shipment_date-po_received_date)";
            $group_concat_all=" listagg(cast(grouping as varchar2(4000)),',') within group (order by grouping) as grouping,
                                listagg(cast(file_no as varchar2(4000)),',') within group (order by file_no) as file_no  ";
        }
        $po_number_arr=array(); $po_ship_date_arr=array(); $shipment_date=""; $po_no=""; $po_received_date=""; $shiping_status="";
        $po_sql=sql_select("select id, po_number,grouping, pub_shipment_date, MIN(pub_shipment_date) as mpub_shipment_date, MIN(po_received_date) as po_received_date, MIN(insert_date) as insert_date, plan_cut, po_quantity, shiping_status, $date_dif_cond as date_diff,min(factory_received_date) as factory_received_date, $group_concat_all,status_active from wo_po_break_down where id in(".$po_id_all.") group by id, po_number, grouping,pub_shipment_date, plan_cut, po_quantity, shiping_status, po_received_date,status_active ");
		 
        $to_ship=0; $fp_ship=0; $f_ship=0;
        foreach($po_sql as $row)
        {
            $po_qnty_tot+=$row[csf('plan_cut')];
            $po_qnty_tot1+=$row[csf('po_quantity')];
            $po_number_arr[$row[csf('id')]]=$row[csf('po_number')];
            $po_ship_date_arr[$row[csf('id')]]=$row[csf('pub_shipment_date')];
            $po_num_arr[$row[csf('id')]]=$row[csf('po_number')];
            $po_no.=$row[csf('po_number')].", ";
            $shipment_date.=change_date_format($row[csf('mpub_shipment_date')],'dd-mm-yyyy','-').", ";
            $lead_time.=$row[csf('date_diff')].",";
            $po_received_date=change_date_format($row[csf('po_received_date')],'dd-mm-yyyy','-');
            $factory_received_date=change_date_format($row[csf('factory_received_date')],'dd-mm-yyyy','-');
            $grouping.=$row[csf('grouping')].",";
            $file_no.=$row[csf('file_no')].",";
			if($row[csf('status_active')]==3){
				$cancel_po_no[$row[csf('po_number')]]=$row[csf('po_number')];
			}
			 
			
			$daysInHand.=(datediff('d',date('d-m-Y',time()),$row[csf('mpub_shipment_date')])-1).",";
			
			if($bookingup_date=="" || $bookingup_date=="0000-00-00 00:00:00")
			{
				$booking_date=$bookingins_date;
			}
			$WOPreparedAfter.=(datediff('d',$row[csf('insert_date')],$booking_date)-1).",";

			if($row[csf('shiping_status')]==1) {
				$shiping_status.= "FP".",";
				$to_ship++;
				$fp_ship++;
			}
			else if($row[csf('shiping_status')]==2){
				$shiping_status.= "PD".",";
				$to_ship++;
			} 
			else if($row[csf('shiping_status')]==3){
				$shiping_status.= "FS".",";
				$to_ship++;
				$f_ship++;
			} 
        }
		$grouping=rtrim(",",$grouping);
		$ref=implode(",",array_filter(array_unique(explode(',',$grouping))));
        if($to_ship==$f_ship) $shiping_status= "<b style='color:green'>Full shipped</b>";
        else if($to_ship==$fp_ship) $shiping_status= "<b style='color:red'>Full Pending</b>";
        else $shiping_status= "<b style='color:red'>Partial Delivery</b>";
		
		$po_no=implode(",",array_filter(array_unique(explode(",",$po_no))));
		$shipment_date=implode(",",array_filter(array_unique(explode(",",$shipment_date))));
		$lead_time=implode(",",array_filter(array_unique(explode(",",$lead_time))));
		$po_received_date=implode(",",array_filter(array_unique(explode(",",$po_received_date))));
		$factory_received_date=implode(",",array_filter(array_unique(explode(",",$factory_received_date))));
		$grouping=implode(",",array_filter(array_unique(explode(",",$grouping))));
		$file_no=implode(",",array_filter(array_unique(explode(",",$file_no))));
		
		$daysInHand=implode(",",array_filter(array_unique(explode(",",$daysInHand))));
		$WOPreparedAfter=implode(",",array_filter(array_unique(explode(",",$WOPreparedAfter))));
		$shiping_status=implode(",",array_filter(array_unique(explode(",",$shiping_status))));
		
            $total_set_qnty=$nameArray[0][csf('total_set_qnty')];
            $colar_excess_percent=$nameArray[0][csf('colar_excess_percent')];
            $cuff_excess_percent=$nameArray[0][csf('cuff_excess_percent')];
            $rmg_process_breakdown=$nameArray[0][csf('rmg_process_breakdown')];
            
            $booking_percent=$nameArray[0][csf('booking_percent')];
			$booking_po_id=$nameArray[0][csf('po_break_down_id')];
			?>
			<table width="1200" class="rpt_table"  border="1" align="left" cellpadding="0"  cellspacing="0" rules="all"  style="font-size:18px; font-family:Arial Narrow;" >
				<tr>
					<td width="150"><b>Service Provider </b></td>		 
					<td width="250"> <span style="font-size:18px"><?
					if($pay_mode==5 || $pay_mode==3){
						echo $company_library[$nameArray_sql[0][csf('supplier_id')]];
						}
						else{
						echo $supplier_name_arr[$nameArray_sql[0][csf('supplier_id')]];
						}
					?></span> </td>
					<td width="150"><span style="font-size:18px"><b>Supplier Address</b></span></td>
					<td width="250"><span style="font-size:18px"><?

					$supplier_id=$nameArray_sql[0][csf('supplier_id')];
				
					if($pay_mode==5 || $pay_mode==3){
						$nameArray_addres=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$supplier_id");
						foreach ($nameArray_addres as $result)
						{
							$company_address= "Plot No:".$result[csf('plot_no')].",Level No:".$result[csf('level_no')].",Road No:".$result[csf('road_no')].",Block No:".$result[csf('block_no')].",City No:".$result[csf('city')].",Zip Code:".$result[csf('zip_code')].",Province No:".$result[csf('province')].",Country:".$country_arr[$result[csf('country_id')]]; 
						}
						echo $company_address;
						}
						else{
						echo $supplier_address_arr[$nameArray_sql[0][csf('supplier_id')]];
						}
					
					?> </span> </td>
					<td width="150"><span style="font-size:18px"><b>Work Order No</b></span></td>
					<td width="250"><span style="font-size:18px"><?=$nameArray_sql[0][csf('booking_no')];?> </span> </td>
					
				
				</tr>
				<tr>
					<td width="150"><b>Job No/IR</b></td>
					
					<?
					$job=trim($job_no_all,"'");
					$ir=rtrim($ref,',');
					?>
					<td width="250"> <span style="font-size:18px"><? echo $job.'  /  '.$ir; if(!empty($revised_no)){ ?>&nbsp;<span style="color: red;">/&nbsp;<? echo $revised_no; }?></span></span> </td>
					<td width="150"><b>Dealing Merchandiser</b></td>
					<td width="250"><? echo $marchentrArr[$nameArray[0][csf('dealing_marchant')]]; ?></td>
					<td width="150"><span style="font-size:18px"><b>Team Leader</b></span></td>
					<td width="250">&nbsp;<span style="font-size:18px"> <?=$team_leader;    ?></span></td>	
				</tr>
				<tr>		
					<td width="150"><span style="font-size:18px"><b>Buyer</b></span></td>
					<td width="250">&nbsp;<span style="font-size:18px"><? echo $buyer_name_arr[$nameArray_sql[0][csf('buyer_id')]]; ?></span></td>
					<td width="100"><b>Brand</b></td>
					<td width="140"><?php echo $brand_name_arr[$nameArray[0][csf('brand_id')]]; ?></td>
					<td width="100"><b></b></td>
					<td width="140"><?php //echo str_replace("'","",$tagged_booking_no); ?></td>
				</tr>
				<tr>
					<td width="150" style="font-size:16px;"><b>Style</b></td>
					<td width="250"style="font-size:16px;" >&nbsp;<? echo $style_ref_no; ?></td>				
					<td width="150"><span style="font-size:18px"><b>Garments Item</b></span></td>
					<td width="250">&nbsp;<span style="font-size:18px"> <?
                        $gmts_item_name="";
                        $gmts_item=explode(',',$nameArray[0][csf('gmts_item_id')]);
                        for($g=0;$g<=count($gmts_item); $g++)
                        {
                            $gmts_item_name.= $garments_item[$gmts_item[$g]].",";
                        }
                        echo rtrim($gmts_item_name,',');
                        ?></span></td>	
					
					<td width="150"><b>Process</b></td>
					<td width="250"><? echo $process; ?></td>
				</tr>

						<? 
							$sql_fab="SELECT a.lib_yarn_count_deter_id AS determin_id, a.construction
							    FROM wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d
							   WHERE a.job_id = b.job_id AND a.id = b.pre_cost_fabric_cost_dtls_id AND a.id = d.pre_cost_fabric_cost_dtls_id AND b.po_break_down_id = d.po_break_down_id AND b.color_size_table_id = d.color_size_table_id AND b.pre_cost_fabric_cost_dtls_id = d.pre_cost_fabric_cost_dtls_id AND d.booking_no = $txt_booking_no AND a.status_active = 1 AND d.status_active = 1 AND d.is_deleted = 0 and a.body_part_id in (1,20) group by a.lib_yarn_count_deter_id , a.construction";
							//echo $sql_fab;
							$res_fab=sql_select($sql_fab);
							$des='';
							foreach ($res_fab as $row) 
							{
								if(!empty($des))
								{
									$des."***";
								}
								$des.=$row[csf('construction')] . " ". $fabric_composition[$lip_yarn_count[$row[csf('determin_id')]]].",";
							}
							//echo implode(",", array_unique(explode("***", $des)));
						?>
			</table>
			<br>
			<br>
			<br>
			
			<?
		
			
	  	?>
		<br>
		<br>
	
		<?php
		$fabric_desc_arr=array();

		$wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select id,fabric_description,cons_process from wo_pre_cost_fab_conv_cost_dtls  where  job_id in($job_id_all) ");
		foreach( $wo_pre_cost_fab_conv_cost_dtls_id as $row_wo_pre_cost_fab_conv_cost_dtls_id)
		{
			if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]!=0)
			{
				$fabric_description=sql_select("select id,body_part_id,body_part_type,color_type_id,fabric_description,construction,composition,gsm_weight,width_dia_type from  wo_pre_cost_fabric_cost_dtls 
				where  id='".$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]."'");
				list($fabric_description_row)=$fabric_description;

				$fabric_desc_arr[$fabric_description_row[csf("id")]]['body_part_id']=$fabric_description_row[csf("body_part_id")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['construction']=$fabric_description_row[csf("construction")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['composition']=$fabric_description_row[csf("composition")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['color_type_id']=$fabric_description_row[csf("color_type_id")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['gsm_weight']=$fabric_description_row[csf("gsm_weight")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['width_dia_type']=$fabric_description_row[csf("width_dia_type")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['body_part_type']=$fabric_description_row[csf("body_part_type")];
				
			}
			if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]==0)
			{
				
				$fabric_description=sql_select("select id,body_part_id,body_part_type,color_type_id,fabric_description,construction,composition,gsm_weight,width_dia_type from  wo_pre_cost_fabric_cost_dtls 
				where  job_id in($job_id_all)");

				foreach( $fabric_description as $fabric_description_row)
				{
				
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['body_part_id']=$fabric_description_row[csf("body_part_id")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['construction']=$fabric_description_row[csf("construction")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['composition']=$fabric_description_row[csf("composition")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['color_type_id']=$fabric_description_row[csf("color_type_id")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['gsm_weight']=$fabric_description_row[csf("gsm_weight")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['width_dia_type']=$fabric_description_row[csf("width_dia_type")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['body_part_type']=$fabric_description_row[csf("body_part_type")];
		
				}
				
			}


		}


		$pre_cons_data=sql_select("select  id, po_break_down_id, color_number_id, gmts_sizes, dia_width, item_size, cons, process_loss_percent, requirment, pcs, color_size_table_id, rate, amount, remarks ,pre_cost_fabric_cost_dtls_id  as fab_desc_id from wo_pre_cos_fab_co_avg_con_dtls where job_id in($job_id_all)  and po_break_down_id in (".$po_id_all.") order by id");


		foreach($pre_cons_data as $row){

			$color_wise_data[$row[csf("po_break_down_id")]][$row[csf("fab_desc_id")]][$row[csf("color_number_id")]]['finsh_cons']=$row[csf("cons")];
			$color_wise_data[$row[csf("po_break_down_id")]][$row[csf("fab_desc_id")]][$row[csf("color_number_id")]]['gray_cons']=$row[csf("requirment")];
			$color_wise_data[$row[csf("po_break_down_id")]][$row[csf("fab_desc_id")]][$row[csf("color_number_id")]]['gray_cons']=$row[csf("requirment")];
			$color_wise_data[$row[csf("po_break_down_id")]][$row[csf("fab_desc_id")]][$row[csf("color_number_id")]]['dia_width']=$row[csf("dia_width")];
			$color_wise_data[$row[csf("po_break_down_id")]][$row[csf("fab_desc_id")]][$row[csf("color_number_id")]]['process_loss_percent']=$row[csf("process_loss_percent")];

		}


		$nameArray_fabric_description= sql_select("select c.id as conv_dtl_id,b.job_no,b.po_break_down_id as po_id,b.sensitivity,b.uom,b.gmts_color_id,b.fabric_color_id,b.gmts_size,(b.wo_qnty) as wo_qnty,b.rate,b.fin_fab_qnty,b.process_loss_percent,b.labdip_no,b.delivery_date,b.delivery_end_date,
		(b.amount) as amount,c.charge_unit,c.fabric_description  as fab_desc_id, a.grouping	from wo_po_break_down a, wo_pre_cost_fab_conv_cost_dtls c,wo_booking_dtls b where a.id=b.po_break_down_id and b.pre_cost_fabric_cost_dtls_id=c.id and b.job_no=c.job_no and b.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.booking_type=3 and c.job_id in($job_id_all)  and b.booking_no =$txt_booking_no order by c.fabric_description");
	
		$body_part_type_arr=array();
		foreach ($nameArray_fabric_description as $row) {	

			if($row[csf('wo_qnty')]>0){

				$body_part_id=$fabric_desc_arr[$row[csf("fab_desc_id")]]['body_part_id'];
				$body_part_type=$fabric_desc_arr[$row[csf("fab_desc_id")]]['body_part_type'];
				$construction=$fabric_desc_arr[$row[csf("fab_desc_id")]]['construction'];
				$composition=$fabric_desc_arr[$row[csf("fab_desc_id")]]['composition'];
				$color_type_id=$fabric_desc_arr[$row[csf("fab_desc_id")]]['color_type_id'];
				$gsm_weight=$fabric_desc_arr[$row[csf("fab_desc_id")]]['gsm_weight'];
				$width_dia_type=$fabric_desc_arr[$row[csf("fab_desc_id")]]['width_dia_type'];
				if($body_part_type==40 || $body_part_type==50){
					$body_part_type_arr[$body_part_type]=$body_part_type;
				}


				$finsh_cons=$color_wise_data[$row[csf("po_id")]][$row[csf("fab_desc_id")]][$row[csf("gmts_color_id")]]['finsh_cons'];
				$gray_cons=$color_wise_data[$row[csf("po_id")]][$row[csf("fab_desc_id")]][$row[csf("gmts_color_id")]]['gray_cons'];
				$dia_width=$color_wise_data[$row[csf("po_id")]][$row[csf("fab_desc_id")]][$row[csf("gmts_color_id")]]['dia_width'];
				$process_loss_percent=$color_wise_data[$row[csf("po_id")]][$row[csf("fab_desc_id")]][$row[csf("gmts_color_id")]]['process_loss_percent'];
			
		
				$grouping_item=$body_part_id.'*'.$construction.'*'.$composition.'*'.$gsm_weight.'*'.$width_dia_type.'*'.$color_type_id;	
					$pp=100+$process_loss_percent;
				$fabric_data_arr[$row[csf('gmts_color_id')]][$row[csf('fabric_color_id')]][$grouping_item]['gmts_color_id'] = $row[csf('gmts_color_id')];
				$fabric_data_arr[$row[csf('gmts_color_id')]][$row[csf('fabric_color_id')]][$grouping_item]['fabric_color_id'] = $row[csf('fabric_color_id')];
				$fabric_data_arr[$row[csf('gmts_color_id')]][$row[csf('fabric_color_id')]][$grouping_item]['labdip_no'] =  $row[csf('labdip_no')];
				$fabric_data_arr[$row[csf('gmts_color_id')]][$row[csf('fabric_color_id')]][$grouping_item]['delivery_date'] =  $row[csf('delivery_date')];
				$fabric_data_arr[$row[csf('gmts_color_id')]][$row[csf('fabric_color_id')]][$grouping_item]['delivery_end_date'] =  $row[csf('delivery_end_date')];
				$fabric_data_arr[$row[csf('gmts_color_id')]][$row[csf('fabric_color_id')]][$grouping_item]['body_part_id'] = $body_part_id;
				$fabric_data_arr[$row[csf('gmts_color_id')]][$row[csf('fabric_color_id')]][$grouping_item]['fabric_des'] = $construction.','.$composition;
				$fabric_data_arr[$row[csf('gmts_color_id')]][$row[csf('fabric_color_id')]][$grouping_item]['gsm'] = $gsm_weight;
				$fabric_data_arr[$row[csf('gmts_color_id')]][$row[csf('fabric_color_id')]][$grouping_item]['fabric_dia'] = $dia_width.",".$fabric_typee[$width_dia_type];
				$fabric_data_arr[$row[csf('gmts_color_id')]][$row[csf('fabric_color_id')]][$grouping_item]['color_type_id'] = $color_type_id;
				$fabric_data_arr[$row[csf('gmts_color_id')]][$row[csf('fabric_color_id')]][$grouping_item]['finsh_cons'] = $finsh_cons;
				$fabric_data_arr[$row[csf('gmts_color_id')]][$row[csf('fabric_color_id')]][$grouping_item]['gray_cons'] = $gray_cons;
				$fabric_data_arr[$row[csf('gmts_color_id')]][$row[csf('fabric_color_id')]][$grouping_item]['fin_fab_qnty'] +=$row[csf('fin_fab_qnty')];
				$fabric_data_arr[$row[csf('gmts_color_id')]][$row[csf('fabric_color_id')]][$grouping_item]['process_loss'] =$row[csf('process_loss_percent')];
				$fabric_data_arr[$row[csf('gmts_color_id')]][$row[csf('fabric_color_id')]][$grouping_item]['grey_fab_qnty'] += $row[csf('wo_qnty')];
				$fabric_data_arr[$row[csf('gmts_color_id')]][$row[csf('fabric_color_id')]][$grouping_item]['amount'] += $row[csf('amount')];
				$fabric_data_arr[$row[csf('gmts_color_id')]][$row[csf('fabric_color_id')]][$grouping_item]['rate']= $row[csf('rate')];
				$fabric_data_arr[$row[csf('gmts_color_id')]][$row[csf('fabric_color_id')]][$grouping_item]['grouping']= $row[csf('grouping')];
				$fabric_data_arr[$row[csf('gmts_color_id')]][$row[csf('fabric_color_id')]][$grouping_item]['process_loss_percent'] = $process_loss_percent;
		   }
	
		}
		$body_part_type_ids=implode(",",$body_part_type_arr);
		// echo $body_part_type_ids;
		
		 foreach ($fabric_data_arr as $gmts_id=>$gmts_data_arr) 
		 {    	 $gmt_color_span=0;   		
			 foreach ($gmts_data_arr as $fabric_id => $body_part_arr) 
			 {
				$fab_color_span=0; 
				foreach ($body_part_arr as $body_part_id => $value) 
				{
					$fab_color_span++;
					$gmt_color_span++;
				}
				$gmts_colorSpanArr[$gmts_id]=$gmt_color_span;
				$fab_colorSpanArr[$gmts_id][$fabric_id]=$fab_color_span;
				 
			 }
			 
				 
		 }
		//echo "<pre>";print_r($body_part_arr);
	
		?>
		 <table class="rpt_table" width="1200"  align="left" border="1" cellpadding="0" cellspacing="0" rules="all" style="font-size: 18px;">
			 <tr>
				 <th>Gmts Colors</th>
				 <th>Fabric Color</th>	
				 <th>IR</th>			
				 <th>Body Part</th>
				 <th>Fabrication</th>
				 <th>GSM</th>
				 <th>Dia Type with </br> Fabric Dia</th>			
				 <th>Color Type</th>
				 <th>Work Order Qty</th>
                  <th>Process Loss%</th>
                  <th>Req. Fin Qty</th>
                   
				 <? if ($show_comments==1) { ?>
				 <th>Rate</th>
				 <th>Amount</th>
				 <? } ?>
			 </tr>
			 <? $grey_fab_qnty=$tot_fin_fab_qnty=0;
			 foreach ($fabric_data_arr as $gmts_id=>$gmts_data_arr) {  
			 $jj=1;     		  		
				 foreach ($gmts_data_arr as $fabric_id => $fabric_data_arr) {
					$kk=1;
					foreach ($fabric_data_arr as $body_part_id => $value) {
						 	$amount+=$value['amount'];   		 	
						 	$grey_fab_qnty+=$value['grey_fab_qnty'];  
						  	$tot_fin_fab_qnty+=$value['fin_fab_qnty'];   
						    $gmts_colorRowspan=$gmts_colorSpanArr[$gmts_id];	
						    $fab_colorRowspan=$fab_colorSpanArr[$gmts_id][$fabric_id];	
						  // echo $fab_colorRowspan.'d,';	 	
						
						   ?>
						  <tr>
						   <? if($jj==1){
                               ?>
                             <td rowspan="<? echo $gmts_colorRowspan; ?>"><? echo $color_library[$gmts_id] ?></td>
                             <?
							   }if($kk==1){
							 ?>
							 <td rowspan="<? echo $fab_colorRowspan; ?>"><? echo $color_library[$value['fabric_color_id']] ?></td>
							 <?
							   }
							 ?>
							 <td><? echo $value['grouping'] ?></td>
							 <td><? echo $body_part[$value['body_part_id']] ?></td>
							 <td><? echo $value['fabric_des'] ?></td>
							 <td align="center"><? echo $value['gsm'] ?></td>
							 <td><? echo $value['fabric_dia'] ?></td>
							 <td align="center"><? echo $color_type[$value['color_type_id']] ?></td>		     			
							 <td align="center"><? echo fn_number_format($value['grey_fab_qnty'],4) ; ?></td>
                             <td align="center"><? echo fn_number_format($value['process_loss'],4) ; ?></td>
                             <td align="center"><? echo fn_number_format($value['fin_fab_qnty'],4) ; ?></td>
							 <? if ($show_comments==1) { ?>
							 <td align="center"><? echo $value['rate'] ; ?></td>
							 <td align="center"><? echo fn_number_format($value['amount'],4) ; ?></td>
							 <? } ?>
						 </tr>
						  <?  
						 
						 $kk++; $jj++;

					  }
					 
				 }
			 } 
			 ?>
			 <tr>
				 <th align="right" colspan="8">Total</th>
				 <th align="right" ><? echo number_format($grey_fab_qnty);  ?></th>
                  <th align="right" ><? //echo number_format($grey_fab_qnty);  ?></th>
                   <th align="right" ><? echo number_format($tot_fin_fab_qnty);  ?></th>
                   
				 <? if ($show_comments==1) { ?>
				 <th>&nbsp;</th>
				 <th align="right" ><?echo number_format($amount);  ?></th>
				 <? } ?>

			 </tr>
		 </table>
		  <br/>
		  <br>
		  <br>
		  <br> 
		<? 
       	echo get_spacial_instruction($txt_booking_no,'1200px',229);
        ?>
        
        <div ><?
		    echo signature_table(290, $cbo_company_name, "1200px",$cbo_template_id,"3px");
            
         ?></div>
		<br> 
		    

	   </div>
       <?
	$emailBody=ob_get_contents();

	if($is_mail_send==1){		
		$sql = "SELECT c.email_address as EMAIL FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=87 and b.mail_user_setup_id=c.id and a.company_id =$cbo_company_name";
		$mail_sql_res=sql_select($sql);
		
		$mailArr=array();
		foreach($mail_sql_res as $row)
		{
			$mailArr[$row[EMAIL]]=$row[EMAIL]; 
		}
		
		$supplier_id=$nameArray[0][csf('supplier_id')];
		$supplier_mail=return_field_value("email", "lib_supplier", "status_active=1 and is_deleted=0 and id=$supplier_id ");

		
		$mailArr=array();
		if($mail_id!=''){$mailArr[]=$mail_id;}
		if($supplier_mail_arr[$supplier_id]!=''){$mailArr[]=$supplier_mail;}
		
		$to=implode(',',$mailArr);
		$subject="Fabric Booking Auto Mail";
		
		if($to!=""){
			require '../../../vendor/phpmailer/phpmailer/PHPMailerAutoload.php';
			require_once('../../../auto_mail/setting/mail_setting.php');
			$header=mailHeader();
			sendMailMailer( $to, $subject, $emailBody );
		}
	}
	exit();
}
if ($action == "process_name_popup") {
	echo load_html_head_contents("Process Name Info", "../../../", 1, 1, '', '1', '');
	extract($_REQUEST);
	?>
	<script>

		$(document).ready(function (e) {
			setFilterGrid('tbl_list_search', -1);
		});

		var selected_id = new Array();
		var selected_name = new Array();

		function check_all_data() {
			var tbl_row_count = document.getElementById('tbl_list_search').rows.length;

			tbl_row_count = tbl_row_count - 1;
			for (var i = 1; i <= tbl_row_count; i++) {
				js_set_value(i);
			}
		}

		function toggle(x, origColor) {
			var newColor = 'yellow';
			if (x.style) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor ) ? origColor : newColor;
			}
		}
		function set_all()
		{
			var old=document.getElementById('txt_process_row_id').value;
			if(old!="")
			{
				old=old.split(",");
				for(var k=0; k<old.length; k++)
				{
					js_set_value( old[k] )
				}
			}
		}
		
		function js_set_value(str) {
            /*var currentRowColor=document.getElementById( 'search' + str ).style.backgroundColor;
             if(currentRowColor=='yellow')
             {
             var mandatory=$('#txt_mandatory' + str).val();
             var process_name=$('#txt_individual' + str).val();
             if(mandatory==1)
             {
             alert(process_name+" Subprocess is Mandatory. So You can't De-select");
             return;
             }
         }*/

         toggle(document.getElementById('search' + str), '#FFFFCC');

         if (jQuery.inArray($('#txt_individual_id' + str).val(), selected_id) == -1) {
         	selected_id.push($('#txt_individual_id' + str).val());
         	selected_name.push($('#txt_individual' + str).val());
         }
         else {
         	for (var i = 0; i < selected_id.length; i++) {
         		if (selected_id[i] == $('#txt_individual_id' + str).val()) break;
         	}
         	selected_id.splice(i, 1);
         	selected_name.splice(i, 1);
         }

         var id = '';
         var name = '';
         for (var i = 0; i < selected_id.length; i++) {
         	id += selected_id[i] + ',';
         	name += selected_name[i] + ',';
         }

         id = id.substr(0, id.length - 1);
         name = name.substr(0, name.length - 1);

         $('#hidden_process_id').val(id);
         $('#hidden_process_name').val(name);
     }

     function window_close(){

     	var old = document.getElementById('hidden_process_id').value;
     	if (old != "") {
     		old = old.split(",");
     		var seq='';
     		for (var k = 0; k < old.length; k++) {
     			if(seq==''){seq=old[k]+'_'+$('#txt_sequence'+old[k]).val();}
     			else{seq+=','+old[k]+'_'+$('#txt_sequence'+old[k]).val();}
     		}
     	}
     	$('#hidden_process_seq').val(seq);
			//var oldArr = old_seq.split(",");


			parent.emailwindow.hide();
		}

	</script>

 </head>

<body>
	<div align="center">
		<fieldset style="width:370px;margin-left:10px">
			<input type="hidden" name="hidden_process_id" id="hidden_process_id" class="text_boxes" value="">		
			<input type="hidden" name="hidden_process_name" id="hidden_process_name" class="text_boxes"
			value="">
			<form name="searchprocessfrm_1" id="searchprocessfrm_1" autocomplete="off">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="350" class="rpt_table">
					<thead>
						<th width="50">SL</th>
						<th>Process Name</th>
						
					</thead>
				</table>
				<div style="width:350px; overflow-y:scroll; max-height:280px;" id="buyer_list_view"
				align="center">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="332" class="rpt_table"
				id="tbl_list_search">
				<?
				$i = 1;
				$process_row_id = '';
						$not_process_id_print_array = array(1, 2, 3, 4, 101, 120, 121, 122, 123, 124); //$mandatory_subprocess_array=array(33,63,65,94);
						$hidden_process_id = explode(",", $txt_process_id);
						
					
						foreach ($conversion_cost_head_array as $id => $name) {
							if (!in_array($id, $not_process_id_print_array)) {
								if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

								if (in_array($id, $hidden_process_id)) {
									// $bgcolor = "Yellow";
									if ($process_row_id == "") $process_row_id = $i; else $process_row_id .= "," . $i;
							 
								}
								 
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
								id="search<? echo $i; ?>" onClick="js_set_value(<? echo $i; ?>)">
								<td width="50" align="center"><?php echo "$i"; ?>
								<input type="hidden" name="txt_individual_id"
								id="txt_individual_id<?php echo $i ?>" value="<? echo $id; ?>"/>
								<input type="hidden" name="txt_individual"
								id="txt_individual<?php echo $i ?>"
								value="<? echo $name; ?>"/>
								<input type="hidden" name="txt_mandatory"
								id="txt_mandatory<?php echo $i ?>"
								value="<? echo $mandatory; ?>"/>
							</td>
							<td title="<? echo $id; ?>"><p><? echo $name; ?></p></td>
							
						</tr>
						<?
						$i++;
					}
				}
				?>
				<input type="hidden" name="txt_process_row_id" id="txt_process_row_id" value="<?php echo $process_row_id; ?>"/>

			



			</table>
		</div>
		<table width="350" cellspacing="0" cellpadding="0" style="border:none" align="center">
			<tr>
				<td align="center" height="30" valign="bottom">
					<div style="width:100%">
						<div style="width:50%; float:left" align="left">
							<input type="checkbox" name="check_all" id="check_all"
							onClick="check_all_data()"/>
							Check / Uncheck All
						</div>
						<div style="width:50%; float:left" align="left">
							<input type="button" name="close" onClick="window_close()"
							class="formbutton" value="Close" style="width:100px"/>
						</div>
					</div>
				</td>
			</tr>
		</table>
	</form>
</fieldset>
</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
<script>set_all();</script>
</html>
<?
exit();
}

?>