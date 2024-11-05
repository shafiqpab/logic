<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
	$permission=$_SESSION['page_permission'];

	$data=$_REQUEST['data'];
	$action=$_REQUEST['action'];
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
$supplier_arr = return_library_array("select id, supplier_name from lib_supplier",'id','supplier_name');
//--------------------------------------------------------------------------------------------
//load drop down knitting company
	if ($action=="load_drop_down_knit_com")
	{
		$exDataArr = explode("**",$data);	
		$knit_source=$exDataArr[0];
		$company=$exDataArr[1];
		$issuePurpose=$exDataArr[2];

		if($company=="" || $company==0) $company_cond2 = ""; else $company_cond2 = "and c.tag_company=$company";

		if($knit_source==0 || $knit_source=="")
		{
			echo create_drop_down( "cbo_dyeing_company", 170, $blank_array,"", 1, "-- Select --", 0, "",0 );	
		}
		else if($knit_source==1)
		{
			echo create_drop_down( "cbo_dyeing_company", 170, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select --", $company, "" );
		}
		else if($knit_source==3)
		{
			echo create_drop_down( "cbo_dyeing_company", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c where a.id=b.supplier_id and b.supplier_id=c.supplier_id and b.party_type in(95) and a.status_active=1 $company_cond2 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "",0 );
		}
		else
		{
			echo create_drop_down( "cbo_dyeing_company", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c where a.id=b.supplier_id and b.supplier_id=c.supplier_id and a.status_active=1 $company_cond2 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "",0 );
		}
		exit();	
	}
	
	
if($action=="load_drop_down_buyer")
{
	$data = explode("_",$data);
	$company_id=$data[0];
	$selected_buyer=$data[2];
	if($selected_buyer!=0)
	{
		$disabled=1;
	}
	else
	{
		$disabled=0;
	}
	if($data[1]==0)
	{
		echo create_drop_down( "cbo_buyer_name", 151, $blank_array,"",1, "-- Select Buyer --", 0, "" );
	}
	else if($data[1]==1)
	{
		echo create_drop_down( "cbo_buyer_name", 151, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name",1, "-- Select Buyer --", "$selected_buyer", "",$disabled,"","","" );
	}
	else if($data[1]==2)
	{
		echo create_drop_down( "cbo_buyer_name", 151, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected_buyer, "",$disabled,"","","" ); 
	}
	
	exit();
}


if($action=="check_conversion_rate")
{
	$data=explode("**",$data);
	if($db_type==0) $conversion_date=change_date_format($data[1], "Y-m-d", "-",1);
	else $conversion_date=change_date_format($data[1], "d-M-y", "-",1);

	$currency_rate=set_conversion_rate( $data[0], $conversion_date );
	echo "1"."_".$currency_rate;
	exit();
}


if ($action=="sales_no_popup")
{
	echo load_html_head_contents("Sales/Booking No. Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $exporter_id."**".$within_group."**".$buyer_name; die;
	//$previous_wo_ids=$prev_wo_nums=$prev_deters=$prev_colors=$prev_constructs=$prev_compositions=$prev_gsms=$prev_widths=$prev_uoms="";
	
	?> 		
	<script>
	
		function load_buyer(cbo_company_id,within_group)
		{
		//alert(within_group);
			
			load_drop_down( 'grey_fabric_service_wo_order_controller',cbo_company_id+'_'+within_group, 'load_drop_down_buyer', 'buyer_td' );
		}

		var selected_id = new Array;
		
	 	function check_all_data() {
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length; 

			tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str ) {
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( $('#txt_wo_id_dtls' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_wo_id_dtls' + str).val() );
				selected_mst_id.push( $('#txt_wo_id' + str).val() );
				//based_on.push( $('#txt_wo_id' + str).val() );
				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_wo_id_dtls' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
			}
			var id = ''; var mst_id = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				mst_id += selected_mst_id[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			mst_id = mst_id.substr( 0, mst_id.length - 1 );
			
			$('#txt_selected_wo_id').val( id );
			$('#txt_selected_wo_mst_id').val( mst_id );
		}
	
		function reset_hide_field()
		{
			$('#txt_selected_wo_id').val( '' );
			$('#txt_selected_wo_mst_id').val( '' );
			selected_id = new Array();
			selected_mst_id = new Array();
		}
	
    </script>

</head>

<body onLoad="">
<div align="center" style="width:900px;">
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:100%; margin-left:2px">
		<legend>Enter search words</legend>           
            <table cellpadding="0" cellspacing="0" width="900" border="1" rules="all" class="rpt_table">
                <thead>
                	<th>Within Group</th>
                    <th>Buyer</th>
                    <th>Booking No.</th>
                    <th>Sales Order</th>
                    <th>Receive Date Range</th>
                 
                    <th>
                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" onClick="reset_hide_field();" />
                    	<input type="hidden" name="txt_selected_wo_id" style="width:60px"  id="txt_selected_wo_id" class="text_boxes" value=""> 
                    	<input type="hidden" name="txt_selected_wo_mst_id" id="txt_selected_wo_mst_id" class="text_boxes" value="">
                    	<input type="hidden" name="txt_item_category" id="txt_item_category" class="text_boxes" style="width:70px" value="<? echo $item_category_id; ?>">
                    </th> 
                </thead>
                <tr class="general">
                	<td>
						<?php echo create_drop_down( "cbo_within_group", 85, $yes_no,"", 0, "-- Select --",$within_group, "load_drop_down( 'grey_fabric_service_wo_order_controller',$cbo_company_id+'_'+this.value, 'load_drop_down_buyer', 'buyer_td' );",0 ); ?>
                    </td>
                    <td id="buyer_td"> 
						<?php echo create_drop_down( "cbo_buyer_name", 151, $blank_array,"", 1, "-- Select Buyer --", 0, "",0 ); ?>
                    </td>
                    <td> 
                        <input type="text" name="txt_wo_no" id="txt_wo_no" class="text_boxes" style="width:120px">
                    </td>						
                    <td> 
                        <input type="text" name="txt_sales_no" id="txt_sales_no" class="text_boxes" style="width:120px">
                    </td>						
                    <td align="center">
                      <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">To
					  <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
					</td> 
					
            		<td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_wo_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('txt_sales_no').value+'_'+'<? echo $prev_wo_ids; ?>'+'_'+'<? echo $previous_wo_ids; ?>', 'create_fso_search_list_view', 'search_div', 'grey_fabric_service_wo_order_controller', 'setFilterGrid(\'tbl_list_search\',-1);reset_hide_field();')" style="width:100px;" />
                     	
                    </td>
                </tr>
                <tr>
                	<td colspan="6" align="center" valign="middle"><? echo load_month_buttons(1); ?></td>
                </tr>
           </table>
           <div style="width:100%; margin-top:5px; margin-left:5px" id="search_div" align="left"></div> 
		</fieldset>
	</form>
</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
load_buyer(<? echo $cbo_company_id;?>,document.getElementById('cbo_within_group').value)
</script>
</html>
<?
exit();
}

if($action=="create_fso_search_list_view")
{
	$data = explode("_",$data);
	
	$within_group =$data[3];
	$buyer_id =$data[4];
	$company_id =$data[5];

	//$selected_based_on=$data[7];	
	$prev_wo_ids=$data[8];
	//echo $data[10];die;
	if($item_category_id==9)
	{
		$prev_wo_mst_ids=$data[9];
		$prev_dtls_data_arr=array();
	}
	//print_r($prev_dtls_data_arr);die;
	
	if($company_id==0) { echo "Please Select Company First."; die; }
	if($within_group==0) $within_group_cond=""; else $within_group_cond=" and a.within_group=$within_group";
	if($buyer_id==0) $buyer_id_cond=""; else $buyer_id_cond=" and a.buyer_id=$buyer_id";
	if (trim($data[0])!="") $wo_number=" and a.sales_booking_no like '%".trim($data[0])."'"; else { $wo_number = ''; }
	if (trim($data[6])!="") $sales_order=" and a.job_no like '%".trim($data[6])."'"; else { $sales_order = ''; }
	
	
	if ($data[1]!="" &&  $data[2]!="")
	{
		if($db_type==0)
		{
			$wo_date_cond = "and a.booking_date between '".change_date_format($data[1], "yyyy-mm-dd", "-")."' and '".change_date_format($data[2], "yyyy-mm-dd", "-")."'"; 
		}
		else
		{
			$wo_date_cond = "and a.booking_date between '".change_date_format($data[1],'','',1)."' and '".change_date_format($data[2],'','',1)."'"; 
		}
	}
	else
	{
		$wo_date_cond ="";
	}
	
	
	$company_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	
	
	/*$sql = "select a.id as mst_id, a.job_no, a.within_group, a.sales_booking_no, a.booking_date, a.buyer_id, b.id as dtls_id, b.fabric_desc, b.gsm_weight, b.dia, b.color_id, b.finish_qty, b.order_uom from fabric_sales_order_mst a, fabric_sales_order_dtls b where a.id=b.mst_id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $wo_number $wo_date_cond $within_group_cond $buyer_id_cond $sales_order order by a.id";*/

	$sql = "select a.id as mst_id, a.job_no, a.within_group, a.sales_booking_no,a.booking_type, a.booking_date, a.buyer_id, b.id as dtls_id, b.fabric_desc, b.gsm_weight, b.dia, b.color_id, b.grey_qnty_by_uom, b.order_uom as cons_uom ,b.grey_qty as grey_qty from fabric_sales_order_mst a, fabric_sales_order_dtls b where a.id=b.mst_id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $wo_number $wo_date_cond $within_group_cond $buyer_id_cond $sales_order order by a.id";

		
	//echo "<pre>";
	?>
	 <table cellspacing="0" cellpadding="0" border="1" rules="all" width="890" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="120">Sales No</th>
            <th width="105">Booking No</th>
			<th width="65">Receive Date</th>
			<th width="50">Buyer</th>
			<th width="80">Color</th>
			<th width="200">Fabric Description</th>
			<th width="40">GSM</th>
			<th width="40">Dia/ Width</th>
			<th width="70">Grey Qty</th>
			<th>UOM</th>
		</thead>
	 </table>
	 <div style="width:890px; max-height:250px; overflow-y:scroll">
		 <table cellspacing="0" cellpadding="0" border="1" rules="all" width="870" class="rpt_table" id="tbl_list_search">
		 <? 
		 $i=1; $job_id_arr =array(); $without_order_arr =array();
		 $nameArray=sql_select( $sql );
		 foreach ($nameArray as $row)
		 {
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			
			if($row[csf('within_group')]==2) $buyer=$buyer_arr[$row[csf('buyer_id')]];
			else $buyer=$company_arr[$row[csf('buyer_id')]];
			//$bal_qtny=0; $is_loop=1;
			//echo $bal_qtny."**";

			
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)">
	                <td width="40" align="center"><?php echo $i; ?>
	                    <input type="hidden" name="txt_wo_id_dtls" id="txt_wo_id_dtls<?php echo $i ?>" value="<? echo $row[csf('dtls_id')]; ?>"/>
	                    <input type="hidden" name="txt_wo_id" id="txt_wo_id<?php echo $i ?>" value="<? echo $row[csf('mst_id')]; ?>"/>	
	                </td>	
					<td width="120"><p><? echo $row[csf('job_no')];?></p></td>
	                <td width="105"><p><? echo $row[csf('sales_booking_no')];?></p></td>
					<td width="65" align="center"><p><? if($row[csf('booking_date')]!="" && $row[csf('booking_date')]!="0000-00-00") echo change_date_format($row[csf('booking_date')]);?></p></td>
					<td width="50"><p><? echo $buyer; ?>&nbsp;</p></td> 
					<td width="80"><p><? echo $color_library[$row[csf('color_id')]]; ?>&nbsp;</p></td>
					<td width="200"><p><? echo $row[csf('fabric_desc')]; ?>&nbsp;</p></td>
					<td width="40"><p><? echo $row[csf('gsm_weight')]; ?></p></td>
					<td width="40"><p><? echo $row[csf('dia')]; ?>&nbsp;</p></td>
					<td width="70" align="right"><p><? echo number_format($row[csf('grey_qty')],2,".",""); ?>&nbsp;</p></td>
					<td align="center"><p><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?>&nbsp;</p></td>
				</tr>
		 		<?
		 		$i++;
			
		 			 
		 }
		 ?>
		</table>
	</div>
	<table width="890" cellspacing="0" cellpadding="0" style="border:none" align="center">
		<tr>
			<td align="center" height="30" valign="bottom">
				<div style="width:100%"> 
					<div style="width:50%; float:left" align="left">
						<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
					</div>
					<div style="width:50%; float:left" align="left">
					<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
					</div>
				</div>
			</td>
		</tr>
	</table>
	<?
	exit();	
} 

if( $action == 'populate_data_wo_form' ) 
{
	$data=explode('**',$data);
	$wo_dtls_id =$data[0];
	$tblRow 	=$data[1];
	$wo_mst_id 	=$data[2];
	//$wo_dtls_id 	=$data[3];
	//echo $tblRow.'ddd';
	//$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$composition_arr=array(); $construction_arr=array();
	$sql_deter="select a.id,a.construction,a.color_range_id,b.copmposition_id,b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
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
			
			$construction_arr[$row[csf('id')]]=$row[csf('construction')];
		}
	}
	if($wo_mst_id!="")  $cond_mst_dls="and b.mst_id in($wo_mst_id)";else $cond_mst_dls="";
	if($wo_dtls_id!="") $cond_dtls_ids=" and b.id in($wo_dtls_id)";else $cond_dtls_ids="and b.id in(0)";
	
	//,b.grey_qnty_by_uom,b.cons_uom
	//$sql = "select a.id, a.job_no, b.order_uom, b.id as dtls_id, b.determination_id, b.gsm_weight, b.dia, b.color_id, b.finish_qty as qty, b.avg_rate, b.amount from fabric_sales_order_mst a, fabric_sales_order_dtls b where a.id=b.mst_id $cond_mst_dls";  b.fabric_desc, b.gsm_weight, b.dia,
	$sql = "select a.id, a.job_no,a.company_id,a.sales_booking_no,a.within_group,a.booking_type,a.booking_without_order,a.buyer_id,a.po_buyer, b.order_uom as cons_uom, b.id as dtls_id, b.determination_id as deter_id,b.fabric_desc, b.gsm_weight, b.dia, b.color_id, b.grey_qnty_by_uom as qty,b.grey_qty as finish_qty, b.avg_rate, b.amount,b.width_dia_type,b.color_range_id,b.gsm_weight,b.pre_cost_fabric_cost_dtls_id from fabric_sales_order_mst a, fabric_sales_order_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $cond_mst_dls $cond_dtls_ids  order by a.id";

	//$prev_pi_qnty_arr_dtls=return_library_array("select work_order_dtls_id, sum(quantity) as quantity from com_export_pi_dtls where status_active=1 and is_deleted=0 group by work_order_dtls_id",'work_order_dtls_id','quantity');
	$data_array=sql_select($sql);
	foreach($data_array as $row)
	{
		
		
			if($tblRow%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		//$bal_qtny=$row[csf('qty')]-$prev_pi_qnty_arr_dtls[$row[csf('dtls_id')]];	
		//if($row[csf('booking_type')]==1 && $row[csf('bookingg_without_order')]==1)		
		
			$within_group=$row[csf('within_group')];
			$amount= $bal_qtny*$row[csf('avg_rate')];
			//echo $po_buyer.'DDD';
			if($within_group==1) $po_buyer=$row[csf('po_buyer')];
			else $po_buyer=$row[csf('buyer_id')];
			$fab_desc=$row[csf('fabric_desc')].','.$row[csf('gsm_weight')].','.$row[csf('dia')];
			
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" id="slTd_<? echo $tblRow; ?>"  align="center">
	            <td width="20">
					<? echo $tblRow; ?>
				</td>
				<td title="<? echo $row[csf('job_no')]; ?>">
					<input type="text" name="txtsalesno_<? echo $tblRow; ?>" id="txtsalesno_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('job_no')]; ?>" style="width:110px;"  readonly />			
					<input type="hidden" name="hideWoId_<? echo $tblRow; ?>" id="hideWoId_<? echo $tblRow; ?>" value="<? echo $row[csf('id')]; ?>" readonly />
					<input type="hidden" name="hideWoDtlsId_<? echo $tblRow; ?>" id="hideWoDtlsId_<? echo $tblRow; ?>" value="<? echo $row[csf('dtls_id')]; ?>" readonly />
				</td>
	            <td title="<? echo $po_buyer;?>"> 
					 <?
					
					 echo create_drop_down( "cbobuyer_".$tblRow, 70, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=".$row[csf('company_id')]." and buy.id=$po_buyer $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $po_buyer, "", 1, "", "", "", "","","","");
					
					?>
				</td>
				<td title="<? echo $row[csf('sales_booking_no')]; ?>">
					<input type="text" name="txtfabricbookingno[]" id="txtfabricbookingno_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('sales_booking_no')]; ?>" style="width:100px" disabled="disabled"/>
				</td> 
	             <td id="itemDescTd_1">
				<input type="text" name="txtFabricDesc[]" id="txtFabricDesc_<? echo $tblRow; ?>" class="text_boxes" style="width:100px" value="<? echo $fab_desc;//$composition_arr[$row[csf('deter_id')]]; ?>" readonly/>
				<input type="hidden" name="fabricDescId[]" id="fabricDescId_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('deter_id')]; ?>">
	            </td>
	            <td>
	            <?   echo create_drop_down( "cboDiaWidthType_".$tblRow, 70, $fabric_typee,"",1, "-- Select --", $row[csf('width_dia_type')], "", "", "", "", "", "", "", "", "cboDiaWidthType[]" ); ?>
	            </td>
	            <td>
	            <?   echo create_drop_down( "cbouom_".$tblRow, 60, $unit_of_measurement,"", 1, "-Uom-",  $row[csf('cons_uom')], "", "", "", "", "", "","","","cbouom[]"); ?>
	            </td>
	             <td>
					<input type="text" name="txtcolor[]" id="txtcolor_<? echo $tblRow; ?>" class="text_boxes"
					style="width:60px" value="<? echo $color_library[$row[csf('color_id')]]; ?>" title="<? echo $color_library[$row[csf('color_id')]]; ?>"
					placeholder="Display" readonly/> 
					<input type="hidden" name="colorId[]" id="colorId_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('color_id')]; ?>">
	            </td>
	            <td>
					 <? 
						 echo create_drop_down( "cbocolorrange_".$tblRow, 70, $color_range,"", 1, "-- ColorRange --", $row[csf('color_range_id')], "", "", "", "", "", "","","","cbocolor_range[]");
					 ?>
				</td>
				<td>
					<input type="text" name="txtwoqty[]" id="txtwoqty_<? echo $tblRow; ?>" class="text_boxes_numeric"  style="width:60px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" placeholder="<? echo $row[csf('finish_qty')];?>" />
				</td>
				<td>
					<input type="text" name="txtrate[]" id="txtrate_<? echo $tblRow; ?>" class="text_boxes_numeric"  style="width:60px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" />
				</td>
				<td>
					<input type="text" name="txtamount[]" id="txtamount_<? echo $tblRow; ?>" class="text_boxes_numeric"  style="width:75px;" readonly/>
				</td>
				<td>
					<input type="text" name="txtnoofroll[]" id="txtnoofroll_<? echo $tblRow; ?>" class="text_boxes_numeric" value=""  style="width:60px;" />
				</td>
				<td>
				<input type="text" name="txtremark[]" id="txtremark_<? echo $tblRow; ?>" class="text_boxes" value="" style="width:75px;" />
                   <input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $tblRow; ?>" readonly/>
				</td>
				<td width="90">
					<input type="hidden" id="increase_<? echo $tblRow; ?>" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(<? echo $tblRow; ?>)" />
					<input type="button" id="decrease_<? echo $tblRow; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $tblRow; ?>);" />
				</td>					
			</tr>
		<?
		$tblRow++;		
	}
	?>
	<tr bgcolor="<? echo $bgcolor; ?>" id="slTd_<? echo $tblRow; ?>"  align="center">
	            <td width="20">
					<? echo $tblRow; ?>
				</td>
				<td>
					<input type="text" name="txtsalesno_<? echo $tblRow; ?>" id="txtsalesno_<? echo $tblRow; ?>" class="text_boxes" style="width:110px;" placeholder="Dbl click" onDblClick="openmypage_wo(<? echo $tblRow; ?>);" readonly />			
					<input type="hidden" name="hideWoId_<? echo $tblRow; ?>" id="hideWoId_<? echo $tblRow; ?>"  readonly />
					<input type="hidden" name="hideWoDtlsId_<? echo $tblRow; ?>" id="hideWoDtlsId_<? echo $tblRow; ?>" readonly />
				</td>
	            <td> 
					 <?
					 echo create_drop_down( "cbobuyer_".$tblRow, 70, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=".$row[csf('company_id')]." $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $po_buyer, "", 1, "", "", "", "","","","");
					?>
				</td>
				<td>
					<input type="text" name="txtfabricbookingno[]" id="txtfabricbookingno_<? echo $tblRow; ?>" class="text_boxes"  style="width:100px" disabled="disabled"/>
				</td> 
	             <td id="itemDescTd_1">
				<input type="text" name="txtFabricDesc[]" id="txtFabricDesc_<? echo $tblRow; ?>" class="text_boxes" style="width:100px"  readonly/>
				<input type="hidden" name="fabricDescId[]" id="fabricDescId_<? echo $tblRow; ?>" class="text_boxes" >
	            </td>
	            <td>
	            <?   echo create_drop_down( "cboDiaWidthType_".$tblRow, 70, $fabric_typee,"",1, "-- Select --", $row[csf('width_dia_type')], "", "", "", "", "", "", "", "", "cboDiaWidthType[]" ); ?>
	            </td>
	            <td>
	            <?   echo create_drop_down( "cbouom_".$tblRow, 60, $unit_of_measurement,"", 1, "-Uom-",  $row[csf('cons_uom')], "", "", "", "", "", "","","","cbouom[]"); ?>
	            </td>
	             <td>
					<input type="text" name="txtcolor[]" id="txtcolor_<? echo $tblRow; ?>" class="text_boxes" style="width:60px" 
					placeholder="Display" readonly/> 
					<input type="hidden" name="colorId[]" id="colorId_<? echo $tblRow; ?>" class="text_boxes">
	            </td>
	            <td>
					 <? 
						 echo create_drop_down( "cbocolorrange_".$tblRow, 70, $color_range,"", 1, "-- ColorRange --", $row[csf('color_range_id')], "", "", "", "", "", "","","","cbocolor_range[]");
					 ?>
				</td>
				<td>
					<input type="text" name="txtwoqty[]" id="txtwoqty_<? echo $tblRow; ?>" class="text_boxes_numeric"  style="width:60px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)"/>
				</td>
				<td>
					<input type="text" name="txtrate[]" id="txtrate_<? echo $tblRow; ?>" class="text_boxes_numeric"  style="width:60px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" />
				</td>
				<td>
					<input type="text" name="txtamount[]" id="txtamount_<? echo $tblRow; ?>" class="text_boxes_numeric"  style="width:75px;" readonly/>
				</td>
				<td>
					<input type="text" name="txtnoofroll[]" id="txtnoofroll_<? echo $tblRow; ?>" class="text_boxes_numeric" value="" style="width:60px;" readonly/>
				</td>
				<td>
				<input type="text" name="txtremark[]" id="txtremark_<? echo $tblRow; ?>" class="text_boxes" value="" style="width:75px;" readonly/>
                   <input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $tblRow; ?>" readonly/>
				</td>
				<td width="90">
					<input type="hidden" id="increase_<? echo $tblRow; ?>" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(<? echo $tblRow; ?>)" />
					<input type="button" id="decrease_<? echo $tblRow; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $tblRow; ?>);" />
				</td>					
			</tr>
	<?
	exit();
} 


//data save update delete here------------------------------//
if($action=="save_update_delete")
{	 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 

	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{

		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }

	
		if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		
		
		$mrr_no='';
		//issue master table entry here Start---------------------------------------//
		if( str_replace("'","",$txt_system_no) == "" ) //new insert
		{	
			$id=return_next_id("id", "inv_grey_fab_service_mst", 1);
			//$id = return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master", $con);
			//if($db_type==0) $year_cond="YEAR(insert_date)"; 
			//else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			//else $year_cond="";//defined Later
			//echo "10**select issue_number_prefix,issue_number_prefix_num from  inv_grey_fab_service_mst where company_id=$cbo_company_id and entry_form=309 and TO_CHAR(insert_date,'YYYY')=".date('Y',time())." order by id desc ";die;
 				if($db_type==2)
				{
				$new_mrr_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'GFSW', date("Y",time()), 5, "select sys_number_prefix,sys_number_prefix_num from  inv_grey_fab_service_mst where company_id=$cbo_company_id and entry_form=309 and TO_CHAR(insert_date,'YYYY')=".date('Y',time())." order by id desc ", "sys_number_prefix", "sys_number_prefix_num" ));
				}
				 if($db_type==0)
				{
				$new_mrr_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'GFSW', date("Y",time()), 5, "select sys_number_prefix,sys_number_prefix_num from  inv_grey_fab_service_mst where company_id=$cbo_company_id and entry_form=309 and YEAR(insert_date)=".date('Y',time())." order by id desc ", "sys_number_prefix", "sys_number_prefix_num" ));
				}
			//$new_mrr_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'GFSW', date("Y",time()), 5, "select sys_number_prefix,sys_number_prefix_num from inv_grey_fab_service_mst where company_id=$cbo_company_id and entry_form=309 and $year_cond=".date('Y',time())." order by id DESC ", "sys_number_prefix", "sys_number_prefix_num" ));
			    
			//$new_mrr_number = explode("*", return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_grey_fab_service_mst",$con,1,$cbo_company_id,"KGI",16,date("Y",time()),13 ));
			
			$field_array="id,sys_number_prefix, sys_number_prefix_num, sys_number, entry_form, company_id, service_type, pay_mode_id, booking_date, attention, currency_id, exchange_rate, dyeing_source, knit_dye_company,delivery_date, challan_no, vehical_no,  driver_name, dl_no,transport_no,mobile_no,remarks, inserted_by, insert_date";
			$data_array="(".$id.",'".$new_mrr_number[1]."','".$new_mrr_number[2]."','".$new_mrr_number[0]."',309,".$cbo_company_id.",".$cbo_service_type.",".$cbo_pay_mode.",".$txt_booking_date.",".$txt_attention.",".$cbo_currency.",".$txt_exchange_rate.",".$cbo_dyeing_source.",".$cbo_dyeing_company.",".$txt_delivery_date.",".$txt_challan_no.",".$txt_vehical_no.",".$txt_driver.",".$txt_dl_no.",".$txt_transport_no.",".$txt_mobile_no.",".$txt_remarks.",'".$user_id."','".$pc_date_time."')";		
			//$rID=sql_insert("inv_issue_master",$field_array,$data_array,1);  txt_dl_no*txt_transport_no*txt_mobile_no*txt_remarks
			$mrr_no=$new_mrr_number[0];
		}
		else
		{
			$id = str_replace("'","",$hidden_system_id);
			$field_array="company_id*service_type*pay_mode_id*booking_date*attention*currency_id* exchange_rate*dyeing_source*knit_dye_company*delivery_date*challan_no*vehical_no*driver_name*dl_no*transport_no*mobile_no*remarks*updated_by*update_date";
			$data_array="".$cbo_company_id."*".$cbo_service_type."*".$cbo_pay_mode."*".$txt_booking_date."*".$txt_attention."*".$cbo_currency."*".$txt_exchange_rate."*".$cbo_dyeing_source."*".$cbo_dyeing_company."*".$txt_delivery_date."*".$txt_challan_no."*".$txt_challan_no."*".$txt_vehical_no."*".$txt_driver."*".$txt_dl_no."*".$txt_transport_no."*".$txt_mobile_no."*".$txt_remarks."*'".$user_id."'*'".$pc_date_time."'";
			//echo $field_array."<br>".$data_array;."-".;
			//$rID=sql_update("inv_issue_master",$field_array,$data_array,"id",$id,0);
			$mrr_no=str_replace("'","",$txt_system_no);
		}
		$dtlsID = return_next_id("id", "inv_grey_fab_service_mst_dtls", 1);
		//this is for Dtls table insert-------------
	

		//####################################################------------------------
		//inv_grey_fabric_issue_dtls table insert start------------------------------//
		//$dtls_id=return_next_id("id", "inv_grey_fabric_issue_dtls", 1);
		//$dtls_id = return_next_id_by_sequence("INV_GREY_FAB_ISS_DTLS_PK_SEQ", "inv_grey_fabric_issue_dtls", $con);
			$field_array_dtls = "id,mst_id,fso_mst_id,fso_mst_no,fso_dtls_id ,determin_id,buyer_id,color_id,color_range,fabric_desc,dia_type_id,uom_id, wo_qnty,rate,amount,remarks,no_of_roll,inserted_by,insert_date";		
				
		for ($i = 1; $i <= $total_row; $i++)
		{
			$txtsalesno = "txtsalesno_" . $i;
			$hideWoId = "hideWoId_" . $i;
			$hideWoDtlsId = "hideWoDtlsId_" . $i;
			$cbobuyer = "cbobuyer_" . $i;
			$txtfabricbookingno = "txtfabricbookingno_" . $i;
			$txtFabricDesc = "txtFabricDesc_" . $i;
			$fabricDescId = "fabricDescId_" . $i;
			$cboDiaWidthType = "cboDiaWidthType_" . $i;
			$cbouom = "cbouom_" . $i;
			$colorId = "colorId_" . $i;
			$cbocolorrange = "cbocolorrange_" . $i;
			$txtwoqty = "txtwoqty_" . $i;
			$txtrate = "txtrate_" . $i;
			$txtamount = "txtamount_" . $i;
			$txtnoofroll = "txtnoofroll_" . $i;
			$txtremark = "txtremark_" . $i;
			$updateIdDtls = "updateIdDtls_" . $i;
		if(str_replace("'","",$$txtwoqty)>0)
        {
			if ($data_array_dtls != "") $data_array_dtls .= ",";
			$data_array_dtls .= "(" . $dtlsID . "," . $id . "," . $$hideWoId . "," . $$txtsalesno . "," . $$hideWoDtlsId . "," . $$fabricDescId . "," . $$cbobuyer . "," . $$colorId . "," . $$cbocolorrange . "," . $$txtFabricDesc . "," . $$cboDiaWidthType . "," . $$cbouom . ",".$$txtwoqty."," . $$txtrate . "," . $$txtamount . "," . $$txtremark . "," . $$txtnoofroll . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
			$dtlsID = $dtlsID + 1;
			}
		}
		//if(str_replace("'","",$$reqqnty)>0)
        //{
		
		
		// table entry end------------------------------------//
	//echo "10** insert into inv_grey_fab_service_mst ($field_array) values $data_array";die;
		

		if( str_replace("'","",$txt_system_no) == "" )
		{
			$rID=sql_insert("inv_grey_fab_service_mst",$field_array,$data_array,1); 
			if ($rID) $flag = 1; else $flag = 0;
		}
		else
		{
			$rID=sql_update("inv_grey_fab_service_mst",$field_array,$data_array,"id",$id,0);
			if ($rID) $flag = 1; else $flag = 0;
		}

		$rID2 = sql_insert("inv_grey_fab_service_mst_dtls", $field_array_dtls, $data_array_dtls, 1);
		if ($flag == 1) {
			if ($rID2) $flag = 1; else $flag = 0;
		}
		//echo "10**".$rID." && ".$rID2; die;
		//release lock table
		check_table_status( $_SESSION['menu_id'],0);
		//oci_rollback($con); 
		//echo "10**".$rID; die;
		
		if($db_type==0)
		{
			if($rID && $rID2)
			{
				mysql_query("COMMIT");  
				echo "0**".$mrr_no."**".$id;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$mrr_no."**".$id;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2)
			{
				oci_commit($con); 
				echo "0**".$mrr_no."**".$id;
			}
			else
			{
				oci_rollback($con); 
				echo "10**".$mrr_no."**".$id;
			}
		}
		disconnect($con);
		die;
	}	
	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		$con = connect();		
		if($db_type==0)	{ mysql_query("BEGIN"); }
		//table lock here  
		
		
		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		
		
		

  		//################################################################
		//issue master update START--------------------------------------//
		$id = str_replace("'","",$hidden_system_id);

	

		$field_array="company_id*service_type*pay_mode_id*booking_date*attention*currency_id* exchange_rate*dyeing_source*knit_dye_company*delivery_date*challan_no*vehical_no*driver_name*dl_no*transport_no*mobile_no*remarks*updated_by*update_date";
			$data_array="".$cbo_company_id."*".$cbo_service_type."*".$cbo_pay_mode."*".$txt_booking_date."*".$txt_attention."*".$cbo_currency."*".$txt_exchange_rate."*".$cbo_dyeing_source."*".$cbo_dyeing_company."*".$txt_delivery_date."*".$txt_challan_no."*".$txt_vehical_no."*".$txt_driver."*".$txt_dl_no."*".$txt_transport_no."*".$txt_mobile_no."*".$txt_remarks."*'".$user_id."'*'".$pc_date_time."'";
		//echo "20**".$field_array."<br>".$data_array;
		//$rID=sql_update("inv_issue_master",$field_array,$data_array,"id",$id,0);
		// master update END---------------------------------------// 

		$field_array_dtls = "id,mst_id,fso_mst_id,fso_mst_no,fso_dtls_id ,determin_id,buyer_id,color_id,color_range,fabric_desc,dia_type_id,uom_id, wo_qnty,rate,amount,remarks,no_of_roll,inserted_by,insert_date";
		$field_array_dtls_update = "fso_mst_id*fso_mst_no*fso_dtls_id*determin_id*buyer_id*color_id*color_range*fabric_desc*dia_type_id*uom_id* wo_qnty*rate*amount*remarks*no_of_roll*updated_by*update_date";		
		$dtlsID = return_next_id("id", "inv_grey_fab_service_mst_dtls", 1);		
		for ($i = 1; $i <= $total_row; $i++)
		{
			$txtsalesno = "txtsalesno_" . $i;
			$hideWoId = "hideWoId_" . $i;
			$hideWoDtlsId = "hideWoDtlsId_" . $i;
			$cbobuyer = "cbobuyer_" . $i;
			$txtfabricbookingno = "txtfabricbookingno_" . $i;
			$txtFabricDesc = "txtFabricDesc_" . $i;
			$fabricDescId = "fabricDescId_" . $i;
			$cboDiaWidthType = "cboDiaWidthType_" . $i;
			$cbouom = "cbouom_" . $i;
			$colorId = "colorId_" . $i;
			$cbocolorrange = "cbocolorrange_" . $i;
			$txtwoqty = "txtwoqty_" . $i;
			$txtrate = "txtrate_" . $i;
			$txtamount = "txtamount_" . $i;
			$txtnoofroll = "txtnoofroll_" . $i;
			$txtremark = "txtremark_" . $i;
			$updateIdDtls = "updateIdDtls_" . $i;
		 if(str_replace("'","",$$txtwoqty)>0)
          {
			if(str_replace("'","",$$updateIdDtls)!="")
			{
				
				$id_arr[] = str_replace("'", '', $$updateIdDtls);
				$data_array_dtls_update[str_replace("'", '', $$updateIdDtls)] = explode("*", ($$hideWoId . "*" . $$txtsalesno . "*" . $$hideWoDtlsId . "*" . $$fabricDescId . "*" . $$cbobuyer . "*" . $$colorId . "*" . $$cbocolorrange . "*" . $$txtFabricDesc . "*" . $$cboDiaWidthType . "*" . $$cbouom . "*" . $$txtwoqty . "*" . $$txtrate . "*" . $$txtamount . "*" . $$txtremark . "*" . $$txtnoofroll . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time."'"));
				$id_dtls = str_replace("'", '', $$updateIdDtls);
			
			}
			else
			{
					
				 if ($data_array_dtls != "") $data_array_dtls .= ",";
				$data_array_dtls .= "(" . $dtlsID . "," . $id . "," . $$hideWoId . "," . $$txtsalesno . "," . $$hideWoDtlsId . "," . $$fabricDescId . "," . $$cbobuyer . "," . $$colorId . "," . $$cbocolorrange . "," . $$txtFabricDesc . "," . $$cboDiaWidthType . "," . $$cbouom . ",".$$txtwoqty."," . $$txtrate . "," . $$txtamount . "," . $$txtremark . "," . $$txtnoofroll . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
				$dtlsID = $dtlsID + 1;
			}
		 }
		}
		

		//echo "10**".print_r($data_array_dtls_update);die;
		$rID=sql_update("inv_grey_fab_service_mst",$field_array,$data_array,"id",$id,0);
		if ($rID) $flag = 1; else $flag = 0;
		
		
		if ($data_array_dtls_update != "") {
				$rID2 = execute_query(bulk_update_sql_statement("inv_grey_fab_service_mst_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $id_arr));
				//echo "10**".bulk_update_sql_statement("inv_grey_fab_service_mst_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $id_arr);die;
				if ($flag == 1) {
					if ($rID2) $flag = 1; else $flag = 0;
				}
			}
			
			
		//$dtlsrID=sql_update("inv_grey_fab_service_mst_dtls",$field_array_dtls,$data_array_dtls,"id",$dtls_id,0);			
		//echo "10** insert into inv_grey_fab_service_mst_dtls ($field_array_dtls) values $data_array_dtls";die;
		
		if($data_array_dtls!="")
		{		
			$rID3 = sql_insert("inv_grey_fab_service_mst_dtls", $field_array_dtls, $data_array_dtls, 1);
			if ($flag == 1) {
				if ($rID3) $flag = 1; else $flag = 0;
			}
			//echo "10** insert into inv_grey_fab_service_mst_dtls ($field_array_dtls) values $data_array_dtls";die;
		}
		if ($txt_deleted_id != "") {
				$field_array_status = "updated_by*update_date*status_active*is_deleted";
				$data_array_status = $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*0*1";

				$rID4 = sql_multirow_update("inv_grey_fab_service_mst_dtls", $field_array_status, $data_array_status, "id", $txt_deleted_id, 1);
				if ($flag == 1) {
					if ($rID4) $flag = 1; else $flag = 0;
				}
			}
			
		//echo "10**".$rID2."==".$flag; die; 
		//oci_rollback($con);
		
		//mysql_query("ROLLBACK");
		
		//release lock table
		//check_table_status( $_SESSION['menu_id'],0);
		
		if($db_type==0)
		{
			if ($flag == 1) 
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$txt_system_no)."**".str_replace("'","",$hidden_system_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_system_no)."**".str_replace("'","",$hidden_system_id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if ($flag == 1) 
			{
				oci_commit($con); 
				echo "1**".str_replace("'","",$txt_system_no)."**".str_replace("'","",$hidden_system_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_system_no)."**".str_replace("'","",$hidden_system_id);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2) // Delete Here----------------------------------------------------------
	{
		 //-------
	}		
}



if($action=="mrr_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);  
	?>

	<script>
		function js_set_value(sys_number,posted_account,id)
		{
 		$("#hidden_sys_number").val(sys_number); // mrr number
		$("#hidden_posted_account").val(posted_account); // check Posted account
		$("#hidden_id").val(id); // check Posted account
		
		parent.emailwindow.hide();
	}
</script>

</head>

<body>
	<div align="center" style="width:100%;" >
		<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
			<table width="880" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
				<thead>
					<tr>                	 
						<th>Service Type</th>
						<th>Search By</th>
						<th align="center" id="search_by_td_up" width="170">Please Enter Sys No</th>
						<th>Booking Date Range</th>
						<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>           
					</tr>
				</thead>
				<tbody>
					<tr>
						<td align="center">
							<?  
							echo create_drop_down( "cbo_service_type", 170, $fabric_service_type,"", 1, "-- Select --", 0, "",0 );
							?>
						</td>
						<td align="center"> 
							<?  
							$search_by = array(1=>'SYS No',2=>'Challan No');
							$dd="change_search_event(this.value, '0*0*1*1*0*0*1', '0*0*select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name*select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name*0*0*select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name', '../../../') ";
							echo create_drop_down( "cbo_search_by", 150, $search_by,"",0, "--Select--", "",$dd,0 );
							?>
						</td>
						<td width="" align="center" id="search_by_td">				
							<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
						</td>    
						<td align="center">
							<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" />
							<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px" placeholder="To Date" />
						</td> 
						<td align="center">
							<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_service_type').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>, 'create_mrr_search_view', 'search_div', 'grey_fabric_service_wo_order_controller', 'setFilterGrid(\'view\',-1)')" style="width:100px;" />				
						</td>
					</tr>
					<tr>                  
						<td align="center" height="40" valign="middle" colspan="5">
							<? echo load_month_buttons(1);  ?>
							<!-- Hidden field here-->
							<input type="hidden" id="hidden_sys_number" value="hidden_sys_number" />
							<input type="hidden" id="hidden_posted_account" value="hidden_posted_account" />
							<input type="hidden" id="hidden_id" value="hidden_id" />
                        <!-- END -->
                    </td>
                </tr>    
            </tbody>
         </tr>         
        </table>    
        <div align="center" valign="top" id="search_div" style="margin-top:10px"> </div> 
        </form>
   </div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_mrr_search_view")
{
	$ex_data = explode("_",$data);
	$cbo_service_type = $ex_data[0];
	$txt_search_by = $ex_data[1];
	$txt_search_common = trim($ex_data[2]);
	$fromDate = $ex_data[3];
	$toDate = $ex_data[4];
	$company = $ex_data[5];
 	$sql_cond="";
	if( str_replace("'","",$fromDate)!="" && str_replace("'","",$toDate)!="" )
	{
		if($db_type==0)
		{
			$sql_cond .= " and booking_date between '".change_date_format($fromDate,'yyyy-mm-dd')."' and '".change_date_format($toDate,'yyyy-mm-dd')."'";
		}
		else
		{
			$sql_cond .= " and booking_date between '".change_date_format($fromDate,'','',1)."' and '".change_date_format($toDate,'','',1)."'";
		}
	}
	
	if($cbo_service_type>0) $sql_cond .= " and service_type=$cbo_service_type";
	if($company>0) $sql_cond .= " and company_id=$company";
	
 	
	$company_arr = return_library_array("select id, company_name from lib_company",'id','company_name');
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer",'id','buyer_name');
	$issuQnty_arr=array();
	
 	if($txt_search_common!="")
	{
		

			if($txt_search_by==1)
			{	
				$sql_cond .= " and sys_number like '%$txt_search_common'";			
			}
			else if($txt_search_by==2)
			{		
				$sql_cond .= " and challan_no like '%$txt_search_common%'";	
			}
			else if($txt_search_by==3)
			{
				$sql_cond .= " and knit_dye_source=1 and knit_dye_company='$txt_search_common'";
			}
			else if($txt_search_by==4)
			{
				$sql_cond .= " and knit_dye_source=3 and knit_dye_company='$txt_search_common'";
			}
			
		
	}
	
	
	if($db_type==0) $year_field="YEAR(insert_date)"; 
	else if($db_type==2) $year_field="to_char(insert_date,'YYYY')";
	else $year_field="";//defined Later
	
	$sql = "select id, sys_number_prefix_num, sys_number,booking_date,delivery_date,pay_mode_id, $year_field as year,booking_date,pay_mode_id,service_type, dyeing_source, knit_dye_company, challan_no  from inv_grey_fab_service_mst where status_active=1 and entry_form=309 $sql_cond order by id";
	//echo $sql;//die;
	$result = sql_select( $sql );
	?>
    	<div>
            <div style="width:945px;">
                <table cellspacing="0" cellpadding="0" width="100%" class="rpt_table" border="1" rules="all">
                    <thead>
                        <th width="30">SL</th>
                        <th width="70">Sys No</th>
                        <th width="60">Year</th>
                        <th width="80">Booking Date</th>     
                        <th width="100">Service Type</th>        
                        <th width="80">Challan No</th>
                        <th width="100">Pay Mode</th>
                        <th width="120">Delivery Date</th>
                        <th width="120">Dyeing Company</th>
                        <th>Pay Mode</th>
                     </thead>
                </table>
             </div>
            <div style="width:945px;overflow-y:scroll;max-height:230px;" id="search_div" >
                <table cellspacing="0" cellpadding="0" width="927" class="rpt_table" id="view" border="1" rules="all">
				<?php	
				 
                $i=1;   
                foreach( $result as $row )
                {
                    if ($i%2==0)  
                        $bgcolor="#E9F3FF";
                    else
                        $bgcolor="#FFFFFF";	
                
       			?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"  onclick="js_set_value('<? echo $row[csf("sys_number")];?>','<? echo $row[csf("sys_number")];?>','<? echo $row[csf("id")];?>');"> 
                        <td width="30"><?php echo $i; ?></td>	
                        <td width="70"><p>&nbsp;&nbsp;<?php echo $row[csf("sys_number_prefix_num")];?></p></td>  
                        <td width="60" align="center"><p><?php echo $row[csf("year")];?></p></td>             	            			
                        <td width="80" align="center"><p><?php echo change_date_format($row[csf("booking_date")]); ?></p></td>								
                        <td width="100"><p><?php echo $fabric_service_type[$row[csf("service_type")]]; ?></p></td>					
                        <td width="80"><p><?php echo $row[csf("challan_no")]; ?></p></td>
                        <td width="100" align="right"><p><?php echo $pay_mode[$row[csf("pay_mode_id")]]; ?>&nbsp;</p></td>
                        <td width="120"><p><?php echo $row[csf("delivery_date")]; ?></p></td>
                        <td width="120"><p><?php 
                            if($row[csf("dyeing_source")]==1) $knit_com=$company_arr[$row[csf("knit_dye_company")]]; else $knit_com=$supplier_arr[$row[csf("knit_dye_company")]];
                         	echo $knit_com; 
                         ?></p>
                        </td>
                        <td width=""><p><?php echo $pay_mode[$row[csf("pay_mode_id")]]; ?></p></td>
                     </tr>
                    <?php
                    $i++;
                    }
                    ?>
                </table>
            </div>
        </div>
    <?
	 
	exit();
	
}

if($action=="populate_data_from_data")
{
	$sql = "select id,sys_number,sys_number_prefix, sys_number_prefix_num, sys_number, entry_form, company_id, service_type, pay_mode_id, booking_date, attention, currency_id, exchange_rate, dyeing_source, knit_dye_company,delivery_date, challan_no, vehical_no,  driver_name, dl_no,transport_no,mobile_no,remarks
			from inv_grey_fab_service_mst 
			where id='$data' and entry_form=309";
	//echo $sql;
	$res = sql_select($sql);	
	foreach($res as $row)
	{		
		echo "$('#hidden_system_id').val(".$row[csf("id")].");\n";
		echo "$('#cbo_company_id').val(".$row[csf("company_id")].");\n";
		
		echo "$('#cbo_service_type').val(".$row[csf("service_type")].");\n";
		echo "$('#cbo_pay_mode').val(".$row[csf("pay_mode_id")].");\n";
		echo "$('#txt_attention').val('".$row[csf("attention")]."');\n";
		//echo "$('#txt_attention').attr('disabled','true')".";\n";
		//echo "enable_disable();\n";
		echo "$('#txt_booking_date').val('".change_date_format($row[csf("booking_date")])."');\n";
		echo "$('#cbo_currency').val(".$row[csf("currency_id")].");\n";
		echo "$('#txt_exchange_rate').val('".$row[csf("exchange_rate")]."');\n"; 
		echo "load_drop_down( 'requires/grey_fabric_service_wo_order_controller', ".$row[csf("dyeing_source")]."+'**'+".$row[csf("company_id")].", 'load_drop_down_knit_com', 'dyeing_company_td' );\n";
		echo "$('#cbo_dyeing_source').val(".$row[csf("dyeing_source")].");\n";
		echo "$('#cbo_dyeing_company').val(".$row[csf("knit_dye_company")].");\n";
		
		echo "$('#txt_delivery_date').val('".change_date_format($row[csf("delivery_date")])."');\n";
		 	
		echo "$('#txt_challan_no').val('".$row[csf("challan_no")]."');\n";
		echo "$('#txt_vehical_no').val('".$row[csf("vehical_no")]."');\n";
		echo "$('#txt_dl_no').val('".$row[csf("dl_no")]."');\n";
		echo "$('#txt_driver').val('".$row[csf("driver_name")]."');\n";
		echo "$('#txt_transport_no').val('".$row[csf("transport_no")]."');\n";
		echo "$('#txt_mobile_no').val('".$row[csf("mobile_no")]."');\n";
		echo "$('#txt_remarks').val('".$row[csf("remarks")]."');\n";
		//echo "enable_disable();\n";
				
  	}
		
	exit();	
}

if($action=="show_dtls_list_view")
{
 	$sql = "select a.company_id,b.id as wo_dtls_id,b.fso_mst_id,b.fso_mst_no,b.fso_dtls_id ,b.determin_id,b.buyer_id,b.color_id,b.color_range,b.fabric_desc,b.dia_type_id,b.uom_id, b.wo_qnty,b.rate,b.amount,b.remarks,b.no_of_roll
			from inv_grey_fab_service_mst a,inv_grey_fab_service_mst_dtls b 
			where a.id=b.mst_id and  a.entry_form=309  and a.id=$data and b.status_active=1 order by b.id";
	//echo $sql;
	$result = sql_select($sql);	
	//fabric_sales_order_mst
	$sql_fso="select c.job_no,c.sales_booking_no,c.within_group from fabric_sales_order_mst c,inv_grey_fab_service_mst a,inv_grey_fab_service_mst_dtls b where  a.id=b.mst_id and b.fso_mst_id=c.id and a.id=$data";
	$result_fso = sql_select($sql_fso);	
	foreach($result_fso as $row)
	{
		$fso_arr[$row[csf('job_no')]]['booking']=$row[csf('sales_booking_no')];
		$fso_arr[$row[csf('job_no')]]['within_group']=$row[csf('within_group')];
		//$fso_arr[$row[csf('job_no')]]['booking']=$row[csf('sales_booking_no')];
	}

	
	?>
   
        <?php	
            $tblRow=1;   
            foreach( $result as $row )
			{
                if ($$tblRow%2==0)  
                    $bgcolor="#E9F3FF";
                else
                    $bgcolor="#FFFFFF";	
		
			if($tblRow%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			//$bal_qtny=$row[csf('qty')]-$prev_pi_qnty_arr_dtls[$row[csf('dtls_id')]];			
			$booking_no=$fso_arr[$row[csf('fso_mst_no')]]['booking'];
			$within_group=$fso_arr[$row[csf('fso_mst_no')]]['within_group'];
		
			$buyer_id=$row[csf('buyer_id')];
			$amount= $bal_qtny*$row[csf('avg_rate')];
			
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" id="slTd_<? echo $tblRow; ?>"  align="center">
	            <td width="20">
					<? echo $tblRow; ?>
				</td>
				<td title="<? echo $row[csf('fso_mst_no')]; ?>">
					<input type="text" name="txtsalesno_<? echo $tblRow; ?>" id="txtsalesno_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('fso_mst_no')]; ?>" style="width:110px;"  readonly />			
					<input type="hidden" name="hideWoId_<? echo $tblRow; ?>" id="hideWoId_<? echo $tblRow; ?>" value="<? echo $row[csf('fso_mst_id')]; ?>" readonly />
					<input type="hidden" name="hideWoDtlsId_<? echo $tblRow; ?>" id="hideWoDtlsId_<? echo $tblRow; ?>" value="<? echo $row[csf('fso_dtls_id')]; ?>" readonly />
				</td>
	            <td> 
					 <?
				
					  echo create_drop_down( "cbobuyer_".$tblRow, 70, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=".$row[csf('company_id')]."  and buy.id=$buyer_id $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $buyer_id, "", 1, "", "", "", "","","","");
					 
					?>
				</td>
				<td title="<? echo $booking_no; ?>">
					<input type="text" name="txtfabricbookingno[]" id="txtfabricbookingno_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $booking_no; ?>" style="width:100px" disabled="disabled"/>
				</td> 
	             <td id="itemDescTd_1">
				<input type="text" name="txtFabricDesc[]" id="txtFabricDesc_<? echo $tblRow; ?>" class="text_boxes" style="width:100px" value="<? echo $row[csf('fabric_desc')]; ?>" readonly/>
				<input type="hidden" name="fabricDescId[]" id="fabricDescId_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('determin_id')]; ?>">
	            </td>
	            <td>
	            <?   echo create_drop_down( "cboDiaWidthType_".$tblRow, 70, $fabric_typee,"",1, "-- Select --", $row[csf('dia_type_id')], "", "", "", "", "", "", "", "", "cboDiaWidthType[]" ); ?>
	            </td>
	            <td>
	            <?   echo create_drop_down( "cbouom_".$tblRow, 60, $unit_of_measurement,"", 1, "-Uom-",  $row[csf('uom_id')], "", "", "", "", "", "","","","cbouom[]"); ?>
	            </td>
	             <td>
					<input type="text" name="txtcolor[]" id="txtcolor_<? echo $tblRow; ?>" class="text_boxes"
					style="width:60px" value="<? echo $color_library[$row[csf('color_id')]]; ?>" title="<? echo $color_library[$row[csf('color_id')]]; ?>"
					placeholder="Display" readonly/> 
					<input type="hidden" name="colorId[]" id="colorId_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('color_id')]; ?>">
	            </td>
	            <td>
					 <? 
						 echo create_drop_down( "cbocolorrange_".$tblRow, 70, $color_range,"", 1, "-- ColorRange --", $row[csf('color_range')], "", "", "", "", "", "","","","cbocolor_range[]");
					 ?>
				</td>
				<td>
					<input type="text" name="txtwoqty[]" id="txtwoqty_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('wo_qnty')]; ?>" style="width:60px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)"/>
				</td>
				<td>
					<input type="text" name="txtrate[]" id="txtrate_<? echo $tblRow; ?>" class="text_boxes_numeric"  style="width:60px;" value="<? echo $row[csf('rate')]; ?>" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" />
				</td>
				<td>
					<input type="text" name="txtamount[]" id="txtamount_<? echo $tblRow; ?>" class="text_boxes_numeric"  style="width:75px;" value="<? echo $row[csf('amount')]; ?>"  readonly/>
				</td>
				<td>
					<input type="text" name="txtnoofroll[]" id="txtnoofroll_<? echo $tblRow; ?>" class="text_boxes"  value="<? echo $row[csf('no_of_roll')]; ?>"   style="width:60px;"  />
				</td>
				<td>
				<input type="text" name="txtremark[]" id="txtremark_<? echo $tblRow; ?>" class="text_boxes" style="width:75px;" value="<? echo $row[csf('remarks')]; ?>" />
                   <input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $tblRow; ?>" value="<? echo $row[csf('wo_dtls_id')]; ?>" readonly/>
				</td>
				<td width="90">
					<input type="hidden" id="increase_<? echo $tblRow; ?>" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(<? echo $tblRow; ?>)" />
					<input type="button" id="decrease_<? echo $tblRow; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $tblRow; ?>);" />
				</td>					
			</tr>
		<?
		$tblRow++;		
	
			
            
            }
            ?>
       <tr bgcolor="<? echo $bgcolor; ?>" id="slTd_<? echo $tblRow; ?>"  align="center">
	            <td width="20">
					<? echo $tblRow; ?>
				</td>
				<td>
					<input type="text" name="txtsalesno_<? echo $tblRow; ?>" id="txtsalesno_<? echo $tblRow; ?>" class="text_boxes"  style="width:110px;" onDblClick="openmypage_wo(<? echo $tblRow; ?>);"  readonly />			
					<input type="hidden" name="hideWoId_<? echo $tblRow; ?>" id="hideWoId_<? echo $tblRow; ?>"  readonly />
					<input type="hidden" name="hideWoDtlsId_<? echo $tblRow; ?>" id="hideWoDtlsId_<? echo $tblRow; ?>"  readonly />
				</td>
	            <td> 
					 <?
					 
					 	 echo create_drop_down( "cbobuyer_".$tblRow, 70, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=".$row[csf('company_id')]." $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $buyer_id, "", 1, "", "", "", "","","","");
					 
					?>
				</td>
				<td>
					<input type="text" name="txtfabricbookingno[]" id="txtfabricbookingno_<? echo $tblRow; ?>" class="text_boxes"  style="width:100px" disabled="disabled"/>
				</td> 
	             <td id="itemDescTd_1">
				<input type="text" name="txtFabricDesc[]" id="txtFabricDesc_<? echo $tblRow; ?>" class="text_boxes" style="width:100px"  readonly/>
				<input type="hidden" name="fabricDescId[]" id="fabricDescId_<? echo $tblRow; ?>" class="text_boxes">
	            </td>
	            <td>
	            <?   echo create_drop_down( "cboDiaWidthType_".$tblRow, 70, $fabric_typee,"",1, "-- Select --", $row[csf('dia_type_id')], "", "", "", "", "", "", "", "", "cboDiaWidthType[]" ); ?>
	            </td>
	            <td>
	            <?   echo create_drop_down( "cbouom_".$tblRow, 60, $unit_of_measurement,"", 1, "-Uom-",  $row[csf('uom_id')], "", "", "", "", "", "","","","cbouom[]"); ?>
	            </td>
	             <td>
					<input type="text" name="txtcolor[]" id="txtcolor_<? echo $tblRow; ?>" class="text_boxes"
					style="width:60px"  title="<? echo $color_library[$row[csf('color_id')]]; ?>"
					placeholder="Display" readonly/> 
					<input type="hidden" name="colorId[]" id="colorId_<? echo $tblRow; ?>" class="text_boxes" >
	            </td>
	            <td>
					 <? 
						 echo create_drop_down( "cbocolorrange_".$tblRow, 70, $color_range,"", 1, "-- ColorRange --", $row[csf('color_range')], "", "", "", "", "", "","","","cbocolor_range[]");
					 ?>
				</td>
				<td>
					<input type="text" name="txtwoqty[]" id="txtwoqty_<? echo $tblRow; ?>" class="text_boxes_numeric"  style="width:60px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)"/>
				</td>
				<td>
					<input type="text" name="txtrate[]" id="txtrate_<? echo $tblRow; ?>" class="text_boxes_numeric"  style="width:60px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" />
				</td>
				<td>
					<input type="text" name="txtamount[]" id="txtamount_<? echo $tblRow; ?>" class="text_boxes_numeric"  style="width:75px;" readonly/>
				</td>
				<td>
					<input type="text" name="txtnoofroll[]" id="txtnoofroll_<? echo $tblRow; ?>" class="text_boxes"    style="width:60px;"  />
				</td>
				<td>
				<input type="text" name="txtremark[]" id="txtremark_<? echo $tblRow; ?>" class="text_boxes"  style="width:75px;" />
                   <input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $tblRow; ?>"  />
				</td>
				<td width="90">
					<input type="hidden" id="increase_<? echo $tblRow; ?>" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(<? echo $tblRow; ?>)" />
					<input type="button" id="decrease_<? echo $tblRow; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $tblRow; ?>);" />
				</td>					
			</tr>
    <?
	
	exit();
}



if ($action=="grey_fab_service_wo_order_print")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	$company_id=$data[0];
	$mst_id=$data[1];
	$report_title=$data[2];
	//$print_with_vat=$data[3];
	$show_item=$data[4];
	//print_r ($data);
	
	$sql="select id, sys_number,booking_date,delivery_date,driver_name,transport_no,attention,pay_mode_id, to_char(insert_date,'YYYY') as year,booking_date,pay_mode_id,vehical_no,service_type,exchange_rate,currency_id, dyeing_source,dl_no,mobile_no,remarks, knit_dye_company, challan_no from inv_grey_fab_service_mst where id=$mst_id and status_active=1 and entry_form=309  order by id ";
	//echo $sql.'k'.$show_item;die;
	$supplier_library=array(); $supplier_short_library=array();
	$supplier_data=sql_select( "select id,supplier_name,short_name,address_1,address_2 from lib_supplier");
	foreach ($supplier_data as $row)
	{
		$supplier_library[$row[csf('id')]]=$row[csf('supplier_name')];
		$supplier_address[$row[csf('id')]]=$row[csf('address_1')];
		$supplier_short_library[$row[csf('id')]]=$row[csf('short_name')];
	}
	$sql_fso="select c.job_no,c.sales_booking_no,c.within_group from fabric_sales_order_mst c,inv_grey_fab_service_mst a,inv_grey_fab_service_mst_dtls b where  a.id=b.mst_id and b.fso_mst_id=c.id and a.id=$mst_id";
	$result_fso = sql_select($sql_fso);	
	foreach($result_fso as $row)
	{
		$fso_arr[$row[csf('job_no')]]['booking']=$row[csf('sales_booking_no')];
		$fso_arr[$row[csf('job_no')]]['within_group']=$row[csf('within_group')];
		//$fso_arr[$row[csf('job_no')]]['booking']=$row[csf('sales_booking_no')];
	}
	$show_item=str_replace("'","",$show_item);
	//echo $show_item.'XXXXXXXXX';
	$dataArray=sql_select($sql);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	
	
	?>
    <div style="width:980px;">
    <table width="980" cellspacing="0" align="right" style="margin-bottom:10px">
       <tr>
            <td colspan="6" align="center" style="font-size:22px"><strong><? echo $company_library[$company_id]; ?></strong></td>
        </tr>
        <tr class="form_caption">
        
        	<?
            $data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$company_id' and form_name='company_details' and is_deleted=0 and file_type=1");
                ?>
            <td  align="left">
                <?
                foreach($data_array as $img_row)
                {
					?>
                    <img src='../../<? echo $img_row[csf('image_location')]; ?>' height='50' width='50' align="middle" />	
                    <? 
                }
                ?>
           </td>
                
        	<td colspan="4" align="center" style="font-size:14px">  
				<?
					
					echo show_company($company_id,'','');//Aziz
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]"); 
					foreach ($nameArray as $result)
					{ 
					$address=$country_arr[$result[csf('country_id')]].','.$result[csf('province')];
					}
					 $wo_no=$dataArray[0][csf('sys_number')];
                ?> 
            </td>  
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:18px"><strong><u><? echo "Grey Fabric Service/Delivery Challan";//$report_title;?></u></strong></td>
        </tr>
		
        <tr>
             <td><strong>WO No:</strong> </td>
			<td width="175px"><? echo $wo_no; ?></td>
			<td><strong>Service Type:</strong></td><td width="175px"><? echo $fabric_service_type[$dataArray[0][csf('service_type')]]; ?></td>
            <td><strong>Source:</strong></td><td width="175px"><?  if($dataArray[0][csf('dyeing_source')]==1) $dye_company=$company_library[$dataArray[0][csf('knit_dye_company')]];else $dye_company=$supplier_library[$dataArray[0][csf('knit_dye_company')]];
			echo $knitting_source[$dataArray[0][csf('dyeing_source')]]; ?></td>
        </tr>
        <tr>
            <td  width="175px"><strong>WO Date:</strong></td> <td width="175px"><? echo change_date_format($dataArray[0][csf('booking_date')]); ?></td>
            <td><strong>Issue To:</strong></td><td width="175px"><? echo $dye_company; ?></td>
			<td><strong>Attention:</strong></td><td width="175px"><? echo $dataArray[0][csf('attention')];; ?></td>
        </tr>
		 <tr>
            <td  width="175px"><strong>Delivery Date:</strong></td> <td width="175px"><? echo change_date_format($dataArray[0][csf('delivery_date')]);//$dataArray[0][csf('exchange_rate')]; ?></td>
            <td><strong>Address:</strong></td><td width="175px"><?  if($dataArray[0][csf('dyeing_source')]==3) echo $supplier_address[$dataArray[0][csf('knit_dye_company')]];
			 ?></td>
			<td><strong>Currency:</strong></td><td width="175px"><? echo $currency[$dataArray[0][csf('currency_id')]];//$dye_company; ?></td>
        </tr>
		
        <tr>
            <td><strong>Challan No:</strong></td><td width="175px"><? echo $dataArray[0][csf('challan_no')]; ?></td>
            <td><strong>Vehical No:</strong></td><td width="175px">
				<? 
					echo 	$dataArray[0][csf('vehical_no')];
				?>
        	</td>
			<td><strong>Driver Name:</strong></td><td width="175px">
				<? 
					echo 	$dataArray[0][csf('driver_name')];
				?>
        	</td>
        </tr>
        <tr>
             <td><strong>DL No:</strong></td><td width="175px"><? echo $dataArray[0][csf('dl_no')]; ?></td>
			 <td><strong>Transport No:</strong></td><td width="175px"><? echo $dataArray[0][csf('transport_no')]; ?></td>
			<td><strong>Mobile No:</strong></td><td width="175px"><? echo $dataArray[0][csf('mobile_no')]; ?></td>
           
            
        </tr>
       
        <tr>
        	 <td><strong>Remarks:</strong></td><td colspan="5"><? echo $dataArray[0][csf('remarks')];//$po_arr[$dataArray[0][csf('order_id')]]; ?></td>
        </tr>
       
    </table>
    <div style="width:100%;">
    <table align="left" cellspacing="0" width="980"  border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" align="center">
            <th width="20">SL</th>
			 <th width="120">Buyer Name</th>
            <th width="160">Sales Order No</th>
           
            <th width="140">Booking NO</th>
            <th width="120">Item Description</th>
            <th width="60">Dia Type</th>
            <th width="40">UOM</th>
            <th width="70">WO Qnty</th>
			<?
			if($show_item==0)
			{
			
			?>
            <th width="40">Rate</th>
			 <th width="70">Amount</th> 
			<?
			
			}
			?>
           
            <th width="50">No of Roll</th>
            <th>Remarks</th> 
        </thead>
        <tbody> 
		<?
		//$color_arr = return_library_array( "select id, color_name from lib_color where status_active =1 and is_deleted=0",'id','color_name');
		$sql_dtls = "select b.fso_mst_no,b.determin_id,b.buyer_id,b.color_id,b.color_range,b.fabric_desc,b.dia_type_id,b.uom_id,b.wo_qnty,b.rate,b.amount,b.remarks,b.no_of_roll from inv_grey_fab_service_mst_dtls b where   b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0";
		//echo $sql_dtls;
		$sql_result= sql_select($sql_dtls);
		$sql_count=count($sql_result);
		$i=1; $all_program_no='';$wo_qnty_sum=$wo_amount_sum=0;
		foreach($sql_result as $row)
		{
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$roll_qty_sum+=$row[csf('no_of_roll')];
			$wo_qnty_sum+=$row[csf('wo_qnty')];$wo_amount_sum+=$row[csf('amount')];
			$booking_no=$fso_arr[$row[csf('fso_mst_no')]]['booking'];
			$within_group=$fso_arr[$row[csf('fso_mst_no')]]['within_group'];
			$buyer_name=$buyer_library[$row[csf("buyer_id")]];
			?>
			<tr bgcolor="<? echo $bgcolor; ?>">
				<td align="center"><? echo $i; ?></td>
				<td><p><? echo $buyer_name; ?></p></td>
				<td><p><?  echo $row[csf("fso_mst_no")]; ?></p></td>
				
				<td><p><? echo $booking_no; ?></p></td>
				<td align="center"><p><? echo $row[csf("fabric_desc")]; ?></p></td>
				<td align="center"><p><? echo $fabric_typee[$row[csf("dia_type_id")]]; ?></p></td>
				<td align="center"><p><? echo $unit_of_measurement[$row[csf("uom_id")]]; ?></p></td>
				<td style="word-break:break-all;" align="right"><p><? echo number_format($row[csf("wo_qnty")],2); ?></p></td>
				<?
				if($show_item==0)
				{
				?>
				<td align="right"><? echo number_format($row[csf("rate")],2); ?></td>
				<td align="right"><? echo number_format($row[csf("amount")],2) ?></td>
				<?
				}
				?>
				
				<td align="center"><p><? echo $row[csf("no_of_roll")]; ?></p></td>
				<td><div style="word-break:break-all;"><? echo $row[csf("remarks")]; ?></div></td>
			</tr>
			<? 
				//if ($sql_count>1) {$inWordTxt="Quantity";}else{$inWordTxt=$unit_of_measurement[$product_array[$row[csf("prod_id")]]['uom']];}
			//$inWordTxt=$unit_of_measurement[$product_array[$row[csf("prod_id")]]['uom']];

			$i++; 
			} ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="7" align="right"><strong>Total :</strong></td>
				<td align="right"><?php echo number_format($wo_qnty_sum,2); ?></td>
				<?
				if($show_item==0)
				{
				?>
				<td align="right"><?php //echo $wo_qnty_sum; ?></td>
				<td align="right"><?php echo number_format($wo_amount_sum,2); ?></td>
				<?
				}
				?>
				
				<td align="right"><?php echo number_format($roll_qty_sum,0); ?></td>
				<td align="right"><?php //echo number_format($issue_qnty_sum,2); ?></td>
				
			</tr>
			                           
		</tfoot>
	</table>
    <br> <br>
	<table  width="450"   cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
		<thead>
			<tr>
				<th width="3%"></th><th width="97%" align="left"><u>Terms & Conditions</u></th>
			</tr>
		</thead>
		<tbody>
			<?
			$data_array=sql_select("select id, terms from  wo_booking_terms_condition where booking_no='$wo_no'");// quotation_id='$data'
			if ( count($data_array)>0)
			{
				$i=0;
				foreach( $data_array as $row )
				{
					$i++;
					?>
						<tr id="settr_1" valign="top">
							<td style="vertical-align:top">
							<? echo $i;?>
							</td>
							<td>
						   <strong style="font-size:20px"> <? echo $row[csf('terms')]; ?></strong>
							</td>
						</tr>
					<?
				}
			}
			
			?>
         </tbody>
         </table>
    <br>&nbsp;
    <!--================================================================-->
    <? 
    
   
    echo signature_table(166, $company_id, "970px");
    ?>
	</div>
	</div>          
	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>

		function generateBarcode( valuess )
		{
			var value = valuess;//$("#barcodeValue").val();
		 // alert(value)
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer ='bmp';// $("input[name=renderer]:checked").val();

			var settings = {
				output:renderer,
				bgColor: '#FFFFFF',
				color: '#000000',
				barWidth: 1,
				barHeight: 30,
				moduleSize:5,
				posX: 10,
				posY: 20,
				addQuietZone: 1
			};
			$("#barcode_img_id").html('11');
			value = {code:value, rect: false};
			
			$("#barcode_img_id").show().barcode(value, btype, settings);
		} 
		generateBarcode('<? echo $data[2]; ?>');
	</script>
<?
exit();
}



if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 172, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
} 
?>