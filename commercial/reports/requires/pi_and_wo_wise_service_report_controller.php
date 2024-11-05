<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];


if ($action=="load_drop_down_location")
{    	 
	echo create_drop_down( "cbo_location", 130, "select id,location_name from lib_location where company_id='$data' $company_location_credential_cond and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0,"" );
	// , "load_drop_down('requires/pi_and_wo_wise_service_report_controller', this.value+'_'+$data, 'load_drop_down_store','store_td');"
	exit();
}

/*if ($action=="load_drop_down_store")
{
	$data=explode("_",$data);
	echo create_drop_down( "cbo_store_name", 130, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$data[1]' and a.location_id = $data[0] and a.status_active=1 and a.is_deleted=0 $store_location_credential_cond group by a.id, a.store_name order by a.store_name","id,store_name", 1,"--Select store--",0,"");
	exit();
}*/

if ($action=="load_drop_down_supplier")
{
	$ex_data = explode('_',$data);
	$company=$ex_data[0];
	$item_category=$ex_data[1];
	$supplier=$ex_data[2];

	if($item_category==0)
	{
		echo create_drop_down( "cbo_supplier", 160, $blank_array,'', 1, '-- Select Supplier --',0,'',0);
	}
	else
	{
		echo create_drop_down( "cbo_supplier", 160,"select DISTINCT(c.supplier_name),c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$company' and b.party_type in(1,7,9,10,11,13,15,19,20,25,31,33) and c.status_active=1 and c.is_deleted=0 order by c.supplier_name",'id,supplier_name', 1, '-- Select Supplier --',$supplier,"",0);
	}
	exit();
}


if($action=="wo_no_popup")
{
	echo load_html_head_contents("WO Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
    <script>
		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;
    	function check_all_data() {
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value( functionParam );
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) { 
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( strCon ) 
		{
			
			var splitSTR = strCon.split("_");
			var str = splitSTR[0];
			var selectID = splitSTR[1];
			var selectDESC = splitSTR[2];
			
			toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( selectID, selected_id ) == -1 ) {
				selected_id.push( selectID );
				selected_name.push( selectDESC );			
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == selectID ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}

			var id = ''; var name = ''; var job = ''; var num='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			id 		= id.substr( 0, id.length - 1 );
			name 	= name.substr( 0, name.length - 1 ); 
			//alert(name);
			$('#hide_wo_id').val( id );
			$('#hide_wo_no').val( name ); 
		}
    </script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="100">Please Enter WO No</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:80px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 					
                    <input type="hidden" name="hide_wo_id" id="hide_wo_id" value="" />
                    <input type="hidden" name="hide_wo_no" id="hide_wo_no" value="" />
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <? 
								echo create_drop_down( "cbo_buyer_name", 100, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($cbo_company_name) $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
							?>
                        </td>                 
                        <td align="center">	
                    	<?
                       		//$search_by_arr=array(1=>"Job No",2=>"Style Ref");
                       		$search_by_arr=array(3=>"WO No");
							//$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $cbo_company_name; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_supplier; ?>'+'**'+'<? echo $item_category_id; ?>', 'create_wo_no_search_list_view', 'search_div', 'pi_and_wo_wise_service_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:80px;" />
                    </td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:15px" id="search_div"></div>
		</fieldset>
	</form>
    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit(); 
}

if($action=="create_wo_no_search_list_view")
{
	extract($_REQUEST);
	//echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	list($company,$buyer,$search_type,$search_value,$cbo_supplier,$item_category_id)=explode('**',$data);

	$buyer=str_replace("'","",$buyer);
	$company=str_replace("'","",$company);
	$cbo_supplier=str_replace("'","",$cbo_supplier);

	$sql_cond =" and a.company_id=$company";	
	if($item_category_id==24){	$sql_cond .=" and a.item_category_id=$item_category_id";}
	else{$sql_cond .=" and a.item_category=$item_category_id";}

    if($cbo_supplier!=0) $sql_cond .=" and a.supplier_id=$cbo_supplier";	
	if($search_type==3 && $search_value!=''){
		$sql_cond .=" and a.booking_no_prefix_num='$search_value'";	
	}

    if($buyer!=0) $buyer_cond="and a.buyer_id=$buyer"; else $buyer_cond="";
	
	if($item_category_id==24){
		$sql_wo="SELECT a.id, a.ydw_no as wo_number, a.yarn_dyeing_prefix_num as wo_number_prefix_num, a.booking_date as wo_date
		from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b
		where a.id=b.mst_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_cond order by id desc";
	}else{
		$sql_wo="SELECT a.id, a.booking_no as wo_number, a.booking_no_prefix_num as wo_number_prefix_num, a.booking_date as wo_date, a.buyer_id
		from wo_booking_mst a, wo_booking_dtls b
		where a.booking_no=b.booking_no and a.booking_type in(3,6) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $buyer_cond $sql_cond group by a.id, a.booking_no, a.booking_no_prefix_num, a.booking_date, a.buyer_id order by id desc";
	}

	// echo $sql_wo;
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
	$arr=array(2=>$buyer_arr);
	echo create_list_view("list_view", "WO No,WO Date,Buyer","100,90,160","400","200",0, $sql_wo , "js_set_value", "id,wo_number_prefix_num", "", 1, "0,0,buyer_id", $arr, "wo_number,wo_date,buyer_id", "","setFilterGrid('list_view',-1)","0","",1) ;

	exit();
}

if($action=="pi_no_popup")
{
	echo load_html_head_contents("PI Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
    <script>
		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;
    	function check_all_data() {
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value( functionParam );
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) { 
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( strCon ) 
		{
			
			var splitSTR = strCon.split("_");
			var str = splitSTR[0];
			var selectID = splitSTR[1];
			var selectDESC = splitSTR[2];
			
			toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( selectID, selected_id ) == -1 ) {
				selected_id.push( selectID );
				selected_name.push( selectDESC );			
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					//alert(selected_id);
					if( selected_id[i] == selectID ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}

			var id = ''; var name = ''; var job = ''; var num='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			id 		= id.substr( 0, id.length - 1 );
			name 	= name.substr( 0, name.length - 1 ); 
			//alert(name);
			$('#hide_pi_id').val( id );
			$('#hide_pi_no').val( name ); 
		}
    </script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="100">Please Enter PI No</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:80px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th>
                    <input type="hidden" name="hide_pi_id" id="hide_pi_id" value="" />
                    <input type="hidden" name="hide_pi_no" id="hide_pi_no" value="" />
                </thead>
                <tbody>
                	<tr>                                      
                        <td align="center">	
                    	<?
                       		//$search_by_arr=array(1=>"Job No",2=>"Style Ref");
                       		$search_by_arr=array(3=>"PI No");
							//$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $cbo_company_name; ?>'+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_supplier; ?>'+'**'+'<? echo $item_category_id; ?>', 'create_pi_no_search_list_view', 'search_div', 'pi_and_wo_wise_service_report_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:80px;" />
                    </td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:15px" id="search_div"></div>
		</fieldset>
	</form>
    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit(); 
}

if($action=="create_pi_no_search_list_view")
{
	extract($_REQUEST);
	//echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	list($company,$search_type,$search_value,$cbo_supplier,$item_category_id)=explode('**',$data);

	$company=str_replace("'","",$company);
	$cbo_supplier=str_replace("'","",$cbo_supplier);

	$sql_cond =" and a.importer_id=$company";	
	$sql_cond .=" and a.item_category_id=$item_category_id";
    if($cbo_supplier!=0) $sql_cond .=" and a.supplier_id=$cbo_supplier";	
	if($search_type==3 && $search_value!=''){
		$sql_cond .=" and a.pi_number like '%$search_value%'";	
	}

	$sql_pi="SELECT a.id, a.pi_number from com_pi_master_details a where a.status_active=1 and a.is_deleted=0  $sql_cond";

	echo create_list_view("list_view", "PI No, System ID","190,160","400","200",0, $sql_pi , "js_set_value", "id,pi_number", "", 1, "0,0", $arr, "pi_number,id", "","setFilterGrid('list_view',-1)","0","",1) ;

	exit();
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_item_category_id=str_replace("'","",$cbo_item_category_id);
	$cbo_location=str_replace("'","",$cbo_location);
	// $cbo_store_name=str_replace("'","",$cbo_store_name);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$txt_pi_no=trim(str_replace("'","",$txt_pi_no));
	$txt_pi_id=trim(str_replace("'","",$txt_pi_id));
	$cbo_supplier=str_replace("'","",$cbo_supplier);
	$cbo_date_type=str_replace("'","",$cbo_date_type);
	$txt_wo_po_no=trim(str_replace("'","",$txt_wo_po_no));
	$txt_wo_id=trim(str_replace("'","",$txt_wo_id));

	if($db_type==0)
	{
		$txt_date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
		$txt_date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
	}
	else if($db_type==2)
	{
		$txt_date_from=change_date_format($txt_date_from,'','',-1);
		$txt_date_to=change_date_format($txt_date_to,'','',-1);
	}
	
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$bank_arr=return_library_array( "select id, bank_name from lib_bank",'id','bank_name');
	$suplier_array = return_library_array("select id,supplier_name from  lib_supplier ","id","supplier_name");
	$container_status = array(1=>"FCL", 2=>"LCL");
	$container_size = array(1=>"20 ft GP", 2=>"20 ft HQ", 3=>"40 ft GP", 4=>"40 ft HQ");

	$inv_id_array=array();
	$all_data_arr=array();
	$sql_cond="";
	if(($cbo_date_type==1 &&  ($txt_date_from && $txt_date_to)) ||  ($txt_wo_po_no!=""))
	{
		if($cbo_company_name) $sql_cond.=" and a.company_id='$cbo_company_name' ";
		if($cbo_item_category_id) $pi_item_category=" and c.item_category_id='$cbo_item_category_id' ";
		if($txt_date_from !="" && $txt_date_to !="") $sql_cond.=" and a.booking_date between  '$txt_date_from' and '$txt_date_to'";
		if($cbo_item_category_id==24)
		{
			if($cbo_item_category_id) $sql_cond.=" and a.item_category_id='$cbo_item_category_id' ";
			if($db_type==2) $sql_cond.=" and a.item_category_id is not null"; else $sql_cond.=" and a.item_category_id !=''";
		}
		else{
			if($cbo_item_category_id) $sql_cond.=" and a.item_category='$cbo_item_category_id' ";
			if($db_type==2) $sql_cond.=" and a.item_category is not null"; else $sql_cond.=" and a.item_category !=''";
		}

		if($txt_wo_po_no !="") 
		{
			$sql_wo_id=" and a.id in ($txt_wo_id)";
			if($cbo_item_category_id==24)
			{			
				$sql_pi="SELECT d.id as PI_ID from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b, com_pi_item_details c, com_pi_master_details d  
				where a.id=b.mst_id and c.work_order_dtls_id=b.id and c.after_goods_source=1 and c.status_active=1 and d.id=c.pi_id and d.pi_basis_id=1 and d.after_goods_source=1 and d.status_active=1 and d.is_deleted=0 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category_id in (24) $sql_cond $sql_wo_id";
			}
			else{
				$sql_pi="SELECT d.id as PI_ID from wo_booking_mst a, wo_booking_dtls b, com_pi_item_details c, com_pi_master_details d  
				where a.booking_no=b.booking_no and a.booking_type in(3,6) and c.work_order_dtls_id=b.id and c.after_goods_source=1 and c.status_active=1 and d.id=c.pi_id and d.pi_basis_id=1 and d.after_goods_source=1 and d.status_active=1 and d.is_deleted=0 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category in (12,25,31,74,102,103,104) $sql_cond $supplier_cond $sql_wo_id";
			}
			// echo $sql_pi;
			$sql_pi_data=sql_select($sql_pi);
			if(count($sql_pi_data)>0)
			{
				$pi_id_all='';
				foreach($sql_pi_data as $row)
				{
					$pi_id_all.=$row['PI_ID'].',';
				}
				$sql_cond.=" and d.id in (".implode(",",array_unique(explode(",",chop($pi_id_all,',')))).") ";
			}
			else
			{
				$sql_cond.=" and a.id in ($txt_wo_id)";
			}
		}
	}
	if(($cbo_date_type==2 &&  ($txt_date_from && $txt_date_to)) ||  ($txt_pi_no!=""))
	{
		if($cbo_company_name) $sql_cond.=" and d.importer_id='$cbo_company_name' ";
		if($cbo_item_category_id) $pi_item_category=" and c.item_category_id='$cbo_item_category_id' ";
		if($cbo_item_category_id==24)
		{
			if($cbo_item_category_id) $sql_cond.=" and a.item_category_id='$cbo_item_category_id' ";
			if($db_type==2) $sql_cond.=" and a.item_category_id is not null"; else $sql_cond.=" and a.item_category_id !=''";
		}
		else{
			if($cbo_item_category_id) $sql_cond.=" and a.item_category='$cbo_item_category_id' ";
			if($db_type==2) $sql_cond.=" and a.item_category is not null"; else $sql_cond.=" and a.item_category !=''";
		}
		if($cbo_item_category_id) $sql_cond.=" and c.item_category_id='$cbo_item_category_id' ";
		if($cbo_location!=0) $sql_cond.=" and d.location_id='$cbo_location' ";
		if($cbo_supplier!=0) $sql_cond.=" and d.supplier_id='$cbo_supplier' ";;
		if($txt_pi_no !="") 
		{
			$sql_cond.=" and d.id in ($txt_pi_id) ";
		}
		if($txt_date_from !="" && $txt_date_to !="") $sql_cond.=" and d.pi_date between  '$txt_date_from' and '$txt_date_to'";
		if($db_type==2) $sql_cond.=" and c.item_category_id is not null"; else $sql_cond.=" and c.item_category_id !=''";
	}
	if($cbo_item_category_id==24)
	{
		$sql_wo="SELECT a.id as MST_ID, a.ydw_no as BOOKING_NO, a.yarn_dyeing_prefix_num as BOOKING_NO_PREFIX_NUM, a.booking_date as BOOKING_DATE, a.item_category_id as ITEM_CATEGORY,a.supplier_id as SUPPLIER_ID, b.id as DTLS_ID, b.YARN_WO_QTY as  WO_QNTY, b.amount as WO_AMOUNT,
		d.id as PI_ID, d.pi_number as PI_NUMBER, d.pi_date as PI_DATE, d.last_shipment_date as LAST_SHIPMENT_DATE, d.currency_id as CURRENCY_ID, c.id as PI_DTLS_ID, c.uom as UOM, c.quantity as PI_QUANTITY, c.amount as PI_AMOUNT,
		f.id as LC_ID, f.lc_number as LC_NUMBER, f.lc_date as LC_DATE, f.payterm_id as PAYTERM_ID, f.tenor as TENOR, f.lc_value as LC_VALUE, f.last_shipment_date as LAST_SHIPMENT_DATE, f.lc_expiry_date as LC_EXPIRY_DATE, f.issuing_bank_id as ISSUING_BANK_ID, f.etd_date as ETD_DATE 
		from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b
		left join com_pi_item_details c on c.work_order_dtls_id=b.id and c.after_goods_source=1 $pi_item_category and c.status_active=1 and c.is_deleted=0
		left join com_pi_master_details d on d.id=c.pi_id and d.pi_basis_id=1 and d.after_goods_source=1 and d.status_active=1 and d.is_deleted=0
		left join com_btb_lc_pi e on d.id=e.pi_id and e.status_active=1 and e.is_deleted=0
		left join com_btb_lc_master_details f on f.id=e.com_btb_lc_master_details_id and f.status_active=1 and f.is_deleted=0
		where a.id=b.mst_id and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category_id=24 $sql_cond $supplier_cond order by f.id,d.id,a.id";
	}
	else
	{
		$sql_wo="SELECT a.id as MST_ID, a.booking_no as BOOKING_NO, a.booking_no_prefix_num as BOOKING_NO_PREFIX_NUM, a.booking_date as BOOKING_DATE, a.item_category as ITEM_CATEGORY,a.supplier_id as SUPPLIER_ID, b.id as DTLS_ID, b.wo_qnty as  WO_QNTY, b.amount as WO_AMOUNT,
		d.id as PI_ID, d.pi_number as PI_NUMBER, d.pi_date as PI_DATE, d.last_shipment_date as LAST_SHIPMENT_DATE, d.currency_id as CURRENCY_ID, c.id as PI_DTLS_ID, c.uom as UOM, c.quantity as PI_QUANTITY, c.amount as PI_AMOUNT,
		f.id as LC_ID, f.lc_number as LC_NUMBER, f.lc_date as LC_DATE, f.payterm_id as PAYTERM_ID, f.tenor as TENOR, f.lc_value as LC_VALUE, f.last_shipment_date as LAST_SHIPMENT_DATE, f.lc_expiry_date as LC_EXPIRY_DATE, f.issuing_bank_id as ISSUING_BANK_ID, f.etd_date as ETD_DATE 
		from wo_booking_mst a, wo_booking_dtls b
		left join com_pi_item_details c on c.work_order_dtls_id=b.id and c.after_goods_source=1 $pi_item_category and c.status_active=1 and c.is_deleted=0
		left join com_pi_master_details d on d.id=c.pi_id and d.pi_basis_id=1 and d.after_goods_source=1 and d.status_active=1 and d.is_deleted=0
		left join com_btb_lc_pi e on d.id=e.pi_id and e.status_active=1 and e.is_deleted=0
		left join com_btb_lc_master_details f on f.id=e.com_btb_lc_master_details_id and f.status_active=1 and f.is_deleted=0
		where a.booking_no=b.booking_no and a.booking_type in(3,6) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category in (12,25,31,74,102,103,104) $sql_cond $supplier_cond order by f.id,d.id,a.id";
	}	
		// echo $sql_wo;die;
		
		$req_result=sql_select($sql_wo);
        $wo_dtls_id_arr=array();$wo_lc_pi=array();$pi_mst_id_check=array();$lc_id_check=array();
		foreach($req_result as $row)
		{
            // $key=$row["DTLS_ID"];
			$wo_lc_pi[$row["LC_ID"]]=$row["LC_ID"];
            $key=$row["MST_ID"].'__'.$row["PI_ID"].'__'.$row["MST_ID"];
            $wo_dtls_id_arr[$row["DTLS_ID"]]=$row["DTLS_ID"];
			$all_data_arr[$row["ITEM_CATEGORY"]][$key]["booking_no"]=$row["BOOKING_NO"];
			$all_data_arr[$row["ITEM_CATEGORY"]][$key]["dtls_id"].=$row["DTLS_ID"].",";
			$all_data_arr[$row["ITEM_CATEGORY"]][$key]["booking_no_prefix_num"]=$row["BOOKING_NO_PREFIX_NUM"];
			$all_data_arr[$row["ITEM_CATEGORY"]][$key]["booking_date"]=$row["BOOKING_DATE"];
			$all_data_arr[$row["ITEM_CATEGORY"]][$key]["wo_qnty"]+=$row["WO_QNTY"];
			$all_data_arr[$row["ITEM_CATEGORY"]][$key]["wo_amount"]+=$row["WO_AMOUNT"];
			$all_data_arr[$row["ITEM_CATEGORY"]][$key]["supplier"]=$row["SUPPLIER_ID"];

			$all_data_arr[$row["ITEM_CATEGORY"]][$key]["pi_id"]=$row["PI_ID"];
			$all_data_arr[$row["ITEM_CATEGORY"]][$key]["pi_number"]=$row["PI_NUMBER"];
			$all_data_arr[$row["ITEM_CATEGORY"]][$key]["pi_date"]=$row["PI_DATE"];
			$all_data_arr[$row["ITEM_CATEGORY"]][$key]["pi_last_shipment_date"]=$row["LAST_SHIPMENT_DATE"];
			$all_data_arr[$row["ITEM_CATEGORY"]][$key]["pi_currency_id"]=$row["CURRENCY_ID"];
			$all_data_arr[$row["ITEM_CATEGORY"]][$key]["pi_uom"]=$row["UOM"];
			$all_data_arr[$row["ITEM_CATEGORY"]][$key]["pi_quantity"]+=$row["PI_QUANTITY"];
			$all_data_arr[$row["ITEM_CATEGORY"]][$key]["pi_amount"]+=$row["PI_AMOUNT"];

			$all_data_arr[$row["ITEM_CATEGORY"]][$key]["lc_id"]=$row["LC_ID"];
			$all_data_arr[$row["ITEM_CATEGORY"]][$key]["lc_number"]=$row["LC_NUMBER"];
			$all_data_arr[$row["ITEM_CATEGORY"]][$key]["lc_date"]=$row["LC_DATE"];
			$all_data_arr[$row["ITEM_CATEGORY"]][$key]["lc_payterm_id"]=$row["PAYTERM_ID"];
			$all_data_arr[$row["ITEM_CATEGORY"]][$key]["lc_tenor"]=$row["TENOR"];
			$all_data_arr[$row["ITEM_CATEGORY"]][$key]["lc_last_shipment_date"]=$row["LAST_SHIPMENT_DATE"];
			$all_data_arr[$row["ITEM_CATEGORY"]][$key]["lc_expiry_date"]=$row["LC_EXPIRY_DATE"];
			$all_data_arr[$row["ITEM_CATEGORY"]][$key]["lc_issuing_bank_id"]=$row["ISSUING_BANK_ID"];
			$all_data_arr[$row["ITEM_CATEGORY"]][$key]["lc_etd_date"]=$row["ETD_DATE"];
			
			if($pi_dtls_id_check[$row["PI_DTLS_ID"]]=="")
			{
				$pi_dtls_id_check[$row["PI_DTLS_ID"]]=$row["PI_DTLS_ID"];
				$all_data_arr[$row["ITEM_CATEGORY"]][$key]["lc_value"]+=$row["LC_VALUE"];
			}
			if($pi_mst_id_check[$row["PI_ID"]][$row["MST_ID"]]=="")
			{
				$pi_mst_id_check[$row["PI_ID"]][$row["MST_ID"]]=$row["PI_ID"];
				$pi_count[$row["PI_ID"]]++;
			}
			if($lc_id_check[$row["LC_ID"]][$row["MST_ID"]]=="")
			{
				$lc_id_check[$row["LC_ID"]][$row["MST_ID"]]=$row["LC_ID"];
				$lc_count[$row["LC_ID"]]++;
			}
		}
		// var_dump($pi_count);die;
		// var_dump($all_data_arr);die;

		if(count($wo_lc_pi)>0)
		{
			$wo_lc_piArr = array_flip(array_flip($wo_lc_pi));
			$wo_lc_pi_cond = '';

			if($db_type==2 && count($wo_lc_piArr>1000))
			{
				$wo_lc_pi_cond = ' and (';
				$woLcPiArr = array_chunk($wo_lc_piArr,999);
				foreach($woLcPiArr as $ids)
				{
					$ids = implode(',',$ids);
					$wo_lc_pi_cond .= " b.btb_lc_id in($ids) or ";
				}
				$wo_lc_pi_cond = rtrim($wo_lc_pi_cond,'or ');
				$wo_lc_pi_cond .= ')';
			}
			else
			{
				$wo_lc_pi_ids = implode(',', $wo_lc_piArr);
				$wo_lc_pi_cond=" and b.btb_lc_id in ($wo_lc_pi_ids)";
			}
			$sql_invoice=" SELECT a.id as inv_id, b.pi_id, a.invoice_no, a.invoice_date, a.inco_term, a.inco_term_place, a.bill_no,  a.bill_date, a.mother_vessel, a.feeder_vessel, a.container_no, a.pkg_quantity, a.doc_to_cnf, a.document_status, a.copy_doc_receive_date, a.original_doc_receive_date, a.edf_paid_date, a.maturity_date, a.retire_source,a.bill_of_entry_no as BILL_OF_ENTRY_NO, a.container_status as CONTAINER_STATUS, a.copy_doc_receive_date as COPY_DOC_RECEIVE_DATE,a.original_doc_receive_date as ORIGINAL_DOC_RECEIVE_DATE,a.bill_of_entry_date as BILL_OF_ENTRY_DATE, a.container_size as CONTAINER_SIZE, a.release_date as RELEASE_DATE, b.btb_lc_id as BTB_LC_ID
			from com_import_invoice_mst a, com_import_invoice_dtls b
			where a.id = b.import_invoice_id and a.status_active =1 and b.status_active=1 and b.current_acceptance_value>0 $wo_lc_pi_cond";
			// echo $sql_invoice;die;
			$invoice_result=sql_select($sql_invoice);
			$invoice_data_arr=array();
			foreach($invoice_result as $row)
			{
				$inv_id_array[$row[csf("inv_id")]]=$row[csf("inv_id")];
				$key=$row["BTB_LC_ID"];
				
				$invoice_data_arr[$key]["invoice_id"]=$row[csf("inv_id")];
				$invoice_data_arr[$key]["invoice_no"]=$row[csf("invoice_no")];
				$invoice_data_arr[$key]["invoice_date"]=$row[csf("invoice_date")];
				$invoice_data_arr[$key]["invoice_inco_term"]=$row[csf("inco_term")];
				$invoice_data_arr[$key]["invoice_inco_term_place"]=$row[csf("inco_term_place")];
				$invoice_data_arr[$key]["invoice_bill_no"]=$row[csf("bill_no")];
				$invoice_data_arr[$key]["invoice_bill_date"]=$row[csf("bill_date")];
				$invoice_data_arr[$key]["invoice_mother_vessel"]=$row[csf("mother_vessel")];
				$invoice_data_arr[$key]["invoice_feeder_vessel"]=$row[csf("feeder_vessel")];
				$invoice_data_arr[$key]["invoice_container_no"]=$row[csf("container_no")];
				$invoice_data_arr[$key]["invoice_doc_to_cnf"]=$row[csf("doc_to_cnf")];
				$invoice_data_arr[$key]["invoice_maturity_date"]=$row[csf("maturity_date")];
				$invoice_data_arr[$key]["invoice_pkg_quantity"]=$row[csf("pkg_quantity")];

				$invoice_data_arr[$key]["invoice_bill_of_entry_no"]=$row["BILL_OF_ENTRY_NO"];
				$invoice_data_arr[$key]["invoice_container_status"]=$row["CONTAINER_STATUS"];
				$invoice_data_arr[$key]["invoice_container_size"]=$row["CONTAINER_SIZE"];
				$invoice_data_arr[$key]["invoice_release_date"]=$row["RELEASE_DATE"];
				$invoice_data_arr[$key]["invoice_copy_doc_receive_date"]=$row["COPY_DOC_RECEIVE_DATE"];
				$invoice_data_arr[$key]["invoice_bill_of_entry_date"]=$row["BILL_OF_ENTRY_DATE"];
				$invoice_data_arr[$key]["invoice_original_doc_receive_date"]=$row["ORIGINAL_DOC_RECEIVE_DATE"];

			}
		}
		
		// var_dump($invoice_data_arr);die;
		if(!empty($inv_id_array))
		{
			$inv_idsArr = array_flip(array_flip($inv_id_array));
	        $inv_ids_cond = '';

	        if($db_type==2 && count($inv_idsArr>1000))
	        {
	            $inv_ids_cond = ' and (';
	            $invIdsArr = array_chunk($inv_idsArr,999);
	            foreach($invIdsArr as $ids)
	            {
	                $ids = implode(',',$ids);
	                $inv_ids_cond .= " invoice_id in($ids) or ";
	            }
	            $inv_ids_cond = rtrim($inv_ids_cond,'or ');
	            $inv_ids_cond .= ')';
	        }
	        else
	        {
	            $inv_ids = implode(',', $inv_idsArr);
	            $inv_ids_cond=" and invoice_id in ($inv_ids)";
	        }
			$sql_pay="select id, invoice_id, payment_date, accepted_ammount, domistic_currency from com_import_payment where status_active=1 $inv_ids_cond";
			$pay_result=sql_select($sql_pay);
			$payment_data_arr=array();
			foreach($pay_result as $row)
			{
				$payment_data_arr[$row[csf("invoice_id")]]["payment_date"]=$row[csf("payment_date")];
				$payment_data_arr[$row[csf("invoice_id")]]["accepted_ammount"]+=$row[csf("accepted_ammount")];
				$payment_data_arr[$row[csf("invoice_id")]]["domistic_currency"]+=$row[csf("domistic_currency")];
			}
			$sql_pay_atsite="select id, invoice_id, payment_date, accepted_ammount, domistic_currency from com_import_payment_com where status_active=1 $inv_ids_cond";
			$pay_result_atsite=sql_select($sql_pay_atsite);
			foreach($pay_result_atsite as $row)
			{
				$payment_data_arr[$row[csf("invoice_id")]]["payment_date"]=$row[csf("payment_date")];
				$payment_data_arr[$row[csf("invoice_id")]]["accepted_ammount"]+=$row[csf("accepted_ammount")];
				$payment_data_arr[$row[csf("invoice_id")]]["domistic_currency"]+=$row[csf("domistic_currency")];
			}
		}

	ksort($all_data_arr);
	//var_dump($payment_data_arr);die;
	ob_start();
	?>
	<div style="width:3490px; margin-left:10px">
		<fieldset style="width:100%;">	 
			<table width="3480" cellpadding="0" cellspacing="0" id="caption">
				<tr>
					<td align="center" width="100%" colspan="20" class="form_caption" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
				</tr> 
				<tr>  
					<td align="center" width="100%" colspan="20" class="form_caption" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
				</tr>  
			</table>
			<br />
			
				<?
				// var_dump($all_data_arr);die;
				$arr_chk=array();$arr_chk1=array();$arr_chk2=array();$arr_chk4=array();
				foreach($all_data_arr as $category_id=>$category_val)
				{
					$i=1;
					$total_wo_qnty=$total_wo_amt=$total_pi_qnty=$total_pi_amt=$total_lc_amt=$total_pkg_qnty=$total_pay_amt=0;
					?>
						<table width="3480" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
						<thead>
							<tr>
								<th colspan="6">Work Order Details</th>
								<th colspan="7">PI Details</th>
								<th colspan="8">L/C Details</th>
								<th colspan="18">Invoice Details</th>
								<th colspan="3">Payment Details</th>
							</tr>
							<tr>
								<!--Work Order details-->
								<th width="30">SL</th>
								<th width="60">WO Date</th>
								<th width="50">WO No</th>
								<!-- <th width="150">Item Description</th> -->
								<th width="80">WO Qnty</th>
								<th width="80">WO Amount</th>
								<th width="120">Supplier</th>

								<!--PI details-->
								<th width="80">PI No</th>
								<th width="70">PI Date</th>
								<th width="100">Item Category</th>
								<th width="50">UOM</th>
								<th width="80">PI Quantity</th>
								<th width="80">PI Value</th>
								<th width="70">Currency</th>

								<!--L/C details-->
								<th width="70">LC Date</th>
								<th width="120">LC No</th>
								<th width="120">Issuing Bank</th>
								<th width="80">Pay Term</th>
								<th width="50">Tenor</th>
								<th width="80">LC Amount</th>
								<th width="70">Shipment Date</th>
								<th width="80">Expiry Date</th>

								<!--Invoice details-->
								<th width="150">Invoice No</th>
								<th width="70">Invoice Date</th>
								<th width="80">Incoterm</th>
								<th width="100">Incoterm Place</th>
								<th width="80">B/L No</th>
								<th width="70">BL Date</th>
								<th width="100">Mother Vassel</th>
								<th width="100">Feedar Vassel</th>
								<th width="100">Continer No</th>
								<th width="100">Continer Status</th>
								<th width="100">Continer Size</th>
								<th width="80">Pkg Qty</th>
								<th width="70">NN Doc Received Date</th>
								<th width="80">Original Doc Received Date</th>
								<th width="80">Doc Send to CNF</th>
								<th width="80">Bill Of Entry No</th>
								<th width="80">Bill Of Entry Date</th>
								<th width="80">Release Date</th>
								
								<!--Payment details-->
								<th width="70">Maturity Date</th>
								<th width="70">Payment Date</th>
								<th >Paid Amount</th>
							</tr>
						</thead>
						<tbody>
					<tr>
						<th colspan="52" style="text-align: left !important; color: black" bgcolor="#FFFFCC"><? echo  $item_category[$category_id]; ?> :</th>
					</tr>
					<?
					foreach($category_val as $key=>$val)
					{
						if ($i%2==0)
						$bgcolor="#E9F3FF";
						else
						$bgcolor="#FFFFFF";

						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">

							<!--Work Order details-->   
							<td align="center"><? echo $i; ?></td>
							<td align="center"><?php echo change_date_format($val['booking_date']); ?></td>
							<td align="center"><?php echo $val['booking_no_prefix_num']; ?></td>
							<!-- <td align="center">
								<a href='##' onClick="openmypage('<? echo rtrim($val['dtls_id'],',') ;?>')"><b>View</b></a>	
								<? echo $wo_pre_cost_fab_cost_dtls_array[$key]; ?>
							</td> -->
							<td align="right"><p><? echo number_format($val["wo_qnty"],2);$total_wo_qnty+=$val["wo_qnty"]; ?>&nbsp;</p></td>
							<td align="right"><p><? echo number_format($val["wo_amount"],2);$total_wo_amt+=$val["wo_amount"]; ?>&nbsp;</p></td>
							<td ><? echo $suplier_array[$val['supplier']];?></td>

							<!--PI details-->
							<?
								if(!empty($val['pi_id']))
								{
									if(!in_array($val['pi_id'],$arr_chk))
									{
										$arr_chk[]=$val['pi_id'];
										$rowspan_pi_count= $pi_count[$val['pi_id']];
										?>
											<td rowspan="<?= $rowspan_pi_count;?>"><p><? echo rtrim($val['pi_number'],',');?>&nbsp;</p></td> 
											<td align="center" rowspan="<?= $rowspan_pi_count;?>"><p><? echo change_date_format($val['pi_date']); ?>&nbsp;</p></td>
											<td rowspan="<?= $rowspan_pi_count;?>"><p><? echo $item_category[$category_id] ?>&nbsp;</p></td>
											<td align="center" rowspan="<?= $rowspan_pi_count;?>"><p><? echo $unit_of_measurement[$val["pi_uom"]]; ?>&nbsp;</p></td>
											<td align="right" rowspan="<?= $rowspan_pi_count;?>"><p><? echo number_format($val["pi_quantity"],2);$total_pi_qnty+=$val["pi_quantity"]; ?>&nbsp;</p></td>
											<td align="right" rowspan="<?= $rowspan_pi_count;?>"><? echo number_format($val["pi_amount"],2);$total_pi_amt+=$val["pi_amount"]; ?></td>
											<td align="center" rowspan="<?= $rowspan_pi_count;?>"><? echo $currency[$val["pi_currency_id"]]; ?></td>
										
										<?

									}
								}
								else
								{
									?>
										<td></td> 
										<td ></td>
										<td></td>
										<td></td>
										<td ></td>
										<td ></td>
										<td></td>
									<?
								}
							?>
							<!--L/C details-->
							<?
								if(!empty($val['lc_id']))
								{
									if(!in_array($val['lc_id'],$arr_chk1))
									{
										$arr_chk1[]=$val['lc_id'];
										$rowspan_lc_count= $lc_count[$val['lc_id']];
										?>
											<td align="center" rowspan="<?= $rowspan_lc_count;?>" ><p><? echo change_date_format($val["lc_date"]); ?>&nbsp;</p></td>
											<td rowspan="<?= $rowspan_lc_count;?>" ><p><? echo $val["lc_number"]; ?>&nbsp;</p></td>
											<td rowspan="<?= $rowspan_lc_count;?>"  ><p><? echo $bank_arr[$val["lc_issuing_bank_id"]]; ?>&nbsp;</p></td>
											<td align="center" rowspan="<?= $rowspan_lc_count;?>" ><p><? echo $pay_term[$val["lc_payterm_id"]]; ?>&nbsp;</p></td>
											<td align="center" rowspan="<?= $rowspan_lc_count;?>" ><? echo $val["lc_tenor"]; ?></td>
											<td align="right" rowspan="<?= $rowspan_lc_count;?>" ><? echo number_format($val["lc_value"],2);$total_lc_amt+=$val["lc_value"]; ?></td>
											<td align="center" rowspan="<?= $rowspan_lc_count;?>" ><? echo change_date_format($val["lc_last_shipment_date"]); ?></td>
											<td align="center" rowspan="<?= $rowspan_lc_count;?>" ><? echo change_date_format($val["lc_expiry_date"]); ?></td>
										<?

									}
								}
								else
								{
									?>
										<td></td> 
										<td ></td>
										<td></td>
										<td></td>
										<td ></td>
										<td ></td>
										<td></td>
										<td></td>
									<?
								}
							?>
							<!--Invoice details-->
							<?
								if(!empty($invoice_data_arr[$val["lc_id"]]["invoice_id"]))
								{
									if(!in_array($invoice_data_arr[$val["lc_id"]]["invoice_id"],$arr_chk2))
									{
										$arr_chk2[]=$invoice_data_arr[$val["lc_id"]]["invoice_id"];
										$rowspan_lc_count= $lc_count[$val['lc_id']];
										?>
											<td rowspan="<?= $rowspan_lc_count;?>"><p><? echo $invoice_data_arr[$val["lc_id"]]["invoice_no"]; ?> &nbsp;</p></td>
											<td align="center" rowspan="<?= $rowspan_lc_count;?>" ><? echo change_date_format($invoice_data_arr[$val["lc_id"]]["invoice_date"]); ?></td>
											<td align="center" rowspan="<?= $rowspan_lc_count;?>"><p><? echo $incoterm[$invoice_data_arr[$val["lc_id"]]["invoice_inco_term"]]; ?> &nbsp;</p></td>
											<td rowspan="<?= $rowspan_lc_count;?>"><p><? echo $invoice_data_arr[$val["lc_id"]]["invoice_inco_term_place"]; ?> &nbsp;</p></td>
											<td rowspan="<?= $rowspan_lc_count;?>"><p><? echo $invoice_data_arr[$val["lc_id"]]["invoice_bill_no"]; ?> &nbsp;</p></td>
											<td rowspan="<?= $rowspan_lc_count;?>" align="center"><? echo change_date_format($invoice_data_arr[$val["lc_id"]]["invoice_bill_date"]); ?></td>
											<td rowspan="<?= $rowspan_lc_count;?>"><p><? echo $invoice_data_arr[$val["lc_id"]]["invoice_mother_vessel"]; ?> &nbsp;</p></td>
											<td rowspan="<?= $rowspan_lc_count;?>"><p><? echo $invoice_data_arr[$val["lc_id"]]["invoice_feeder_vessel"]; ?> &nbsp;</p></td>
											<td rowspan="<?= $rowspan_lc_count;?>"><p><? echo $invoice_data_arr[$val["lc_id"]]["invoice_container_no"]; ?> &nbsp;</p></td>
											<td rowspan="<?= $rowspan_lc_count;?>" align="center"><p><? echo $container_status[$invoice_data_arr[$val["lc_id"]]["invoice_container_status"]]; ?> &nbsp;</p></td>
											<td rowspan="<?= $rowspan_lc_count;?>" align="center"><p><? echo $container_size[$invoice_data_arr[$val["lc_id"]]["invoice_container_size"]]; ?> &nbsp;</p></td>
											<td rowspan="<?= $rowspan_lc_count;?>" align="right"><? echo number_format($invoice_data_arr[$val["lc_id"]]["invoice_pkg_quantity"],2);$total_pkg_qnty+=$invoice_data_arr[$val["lc_id"]]["invoice_pkg_quantity"]; ?></td>
											<td rowspan="<?= $rowspan_lc_count;?>" align="center"><? echo change_date_format($invoice_data_arr[$val["lc_id"]]["invoice_copy_doc_receive_date"]); ?></td>
											<td rowspan="<?= $rowspan_lc_count;?>" align="center"><? echo change_date_format($invoice_data_arr[$val["lc_id"]]["invoice_original_doc_receive_date"]); ?></td>
											<td rowspan="<?= $rowspan_lc_count;?>" align="center"><? echo change_date_format($invoice_data_arr[$val["lc_id"]]["invoice_doc_to_cnf"]); ?></td>
											<td rowspan="<?= $rowspan_lc_count;?>"><p><? echo $invoice_data_arr[$val["lc_id"]]["invoice_bill_of_entry_no"]; ?> &nbsp;</p></td>
											<td rowspan="<?= $rowspan_lc_count;?>" align="center"><? echo change_date_format($invoice_data_arr[$val["lc_id"]]["invoice_bill_of_entry_date"]); ?></td>
											<td rowspan="<?= $rowspan_lc_count;?>" align="center"><? echo change_date_format($invoice_data_arr[$val["lc_id"]]["invoice_release_date"]); ?></td>
										<?

									}
								}
								else
								{
									?>
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
									<?
								}
							?>
							<!--Payment details-->
							<?
								$pay_date='';
								$pay_amt=0;
								$pay_date=$payment_data_arr[$invoice_data_arr[$val["lc_id"]]["invoice_id"]]["payment_date"];
								$pay_amt+=$payment_data_arr[$invoice_data_arr[$val["lc_id"]]["invoice_id"]]["accepted_ammount"];
								if(!empty($pay_date))
								{
									if(!in_array($val['lc_id'],$arr_chk3))
									{
										$arr_chk3[]=$val['lc_id'];
										$rowspan_lc_count= $lc_count[$val['lc_id']];
										
										?>
											<td rowspan="<?= $rowspan_lc_count;?>" align="center"><? echo change_date_format($invoice_data_arr[$val["lc_id"]]["invoice_maturity_date"]); ?></td>
											<td rowspan="<?= $rowspan_lc_count;?>" align="center"><? if($pay_date!="" &&  $pay_date!="0000-00-00")echo change_date_format($pay_date); ?></td>
											<td rowspan="<?= $rowspan_lc_count;?>" align="right"><? echo number_format($pay_amt,2);$total_pay_amt+=$pay_amt; ?></td>
										<?

									}
								}
								else
								{
									?>
										<td></td> 
										<td ></td>
										<td></td>
									<?
								}
							?>
						</tr>
						<?
						$i++;
					}					
					?>
					<tr bgcolor="#CCCCCC">
						<!--Work Order details-->
						<td ></td>
						<td ></td>
						<!-- <td ></td> -->
						<td align="right"><strong>Total: </strong></td>
						<td align="right"> <strong><?echo number_format($total_wo_qnty,2);?></strong> </td>
						<td align="right"><strong><?echo number_format($total_wo_amt,2);?></strong></td>
						<td ></td>

						<!--PI details-->
						<td ></td>
						<td ></td>
						<td ></td>
						<td ></td>
						<td align="right"><strong><?echo number_format($total_pi_qnty,2);?></strong></td>
						<td align="right"><strong><?echo number_format($total_pi_amt,2);?></strong></td>
						<td ></td>

						<!--L/C details-->
						<td ></td>
						<td ></td>
						<td ></td>
						<td ></td>
						<td ></td>
						<td align="right"><strong><?echo number_format($total_lc_amt,2);?></strong></td>
						<td ></td>
						<td ></td>

						<!--Invoice details-->
						<td ></td>
						<td ></td>
						<td ></td>
						<td ></td>
						<td ></td>
						<td ></td>
						<td ></td>
						<td ></td>
						<td ></td>
						<td ></td>
						<td ></td>
						<td align="right"><strong><?echo number_format($total_pkg_qnty,2);?></strong></td>
						<td ></td>
						<td ></td>
						<td ></td>
						<td ></td>
						<td ></td>
						<td ></td>
						
						<!--Payment details-->
						<td ></td>
						<td ></td>
						<td align="right"><strong><?echo number_format($total_pay_amt,2);?></strong></td>
					</tr>
					<?
				}
				?>
				</tbody>	
			</table>
		</fieldset>
	</div>
	<?
		
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "$total_data****$filename";
	exit();
}

/*if($action=="item_details")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$wo_dtls_id=str_replace("'","",$wo_dtls_id);
	// print_r($wo_dtls_id);die;
	$wo_pre_cost_fab_cost_dtls="SELECT a.booking_no as BOOKING_NO, b.id as ID, d.body_part_id as BODY_PART_ID, d.color_type_id as COLOR_TYPE_ID, d.fabric_description as FABRIC_DESCRIPTION from wo_booking_mst a,wo_booking_dtls b,wo_pre_cost_fab_conv_cost_dtls c, wo_pre_cost_fabric_cost_dtls d where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and c.fabric_description=d.id and a.booking_type in(3,6) and a.company_id='$cbo_company_name' and a.item_category in (12,24,25,31,74,102,103,104) and a.is_deleted=0 and b.is_deleted=0";
    $wo_pre_cost_fab_cost_dtls_sql=sql_select($wo_pre_cost_fab_cost_dtls);
	$wo_pre_cost_fab_cost_dtls_array=array();
    foreach($wo_pre_cost_fab_cost_dtls_sql as $row)
    {
        $wo_pre_cost_fab_cost_dtls_array[$row["ID"]]=$body_part[$row["BODY_PART_ID"]].','.$color_type[$row["COLOR_TYPE_ID"]].', '.$row["FABRIC_DESCRIPTION"];
    }
	$wo_dtls_arr=explode(",",$wo_dtls_id);
	?>

	<div style="width:230px">
	<fieldset style="width:100%"  >
	    <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="230">
	        <thead>
	            <th width="30">SL NO</th>
	            <th width="200">Item Description</th>

	        </thead>
	        <tbody>
			<?
			$p=1;
	        foreach($wo_dtls_arr as $row)
	        {
	        ?>
	            <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
	                <td><? echo $p; ?>&nbsp;</td>
	                <td><? echo $wo_pre_cost_fab_cost_dtls_array[$row]; ?>&nbsp;</td>     
	            </tr>
			<?
			$p++;
	        }
	        ?>
	        </tbody>

	    </table>
	</fieldset>
	</div>
	<?
	exit();
}*/
disconnect($con);
?>
