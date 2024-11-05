<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($db_type==0)
{
	$select_year="year";
	$year_con="";
}
else
{
	$select_year="to_char";
	$year_con=",'YYYY'";
}


//--------------------------------------------------------------------------------------------

$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
$store_arr=return_library_array( "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name",'id','store_name');

$supplier_arr=return_library_array( "select id, supplier_name from  lib_supplier",'id','supplier_name');
$buyer_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
$yarn_count_arr=return_library_array( "select id,yarn_count from lib_yarn_count where is_deleted = 0 AND status_active = 1",'id','yarn_count');


if($action=="load_drop_down_from_company")
{
    $data=explode("**",$data);
    if($data[0]==1)
    {
        echo create_drop_down( "cbo_company_from", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
    }
    if($data[0]==2)
    {
        if($data[1]!=0){ $com_cond=" and a.company_id=$data[1]";}else{$com_cond="";}
        echo create_drop_down( "cbo_store_from", 120, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id $com_cond and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name","id,store_name", 1, "--Select store--", 0, "" );
    }
    if($data[0]==4)
    {
        echo '<input type="text" name="txt_from_order_no" id="txt_from_order_no" class="text_boxes" style="width:100px;" placeholder="Double click to search" onDblClick="openmypage_orderNo('."'from'".')" readonly/><input type="hidden" name="txt_from_order_id" id="txt_from_order_id" readonly/>';
    }
}

if($action=="load_drop_down_to_company")
{
    $data=explode("**",$data);
    if($data[0]==1)
    {
        echo create_drop_down( "cbo_company_to", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
    }
    if($data[0]==2)
    {
        if($data[1]!=0){ $com_cond=" and a.company_id=$data[1]";}else{$com_cond="";}
        echo create_drop_down( "cbo_store_to", 120, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id $com_cond and a.status_active=1 and a.is_deleted=0  group by a.id, a.store_name order by a.store_name","id,store_name", 1, "--Select store--", 0, "" );

        die;
    }
    if($data[0]==4)
    {
        echo '<input type="text" name="txt_to_order_no" id="txt_to_order_no" class="text_boxes" style="width:100px;" placeholder="Double click to search" onDblClick="openmypage_orderNo('."'to'".')" readonly/><input type="hidden" name="txt_to_order_id" id="txt_to_order_id" readonly/>';
    }
}

if ($action=="order_popup")
{
	echo load_html_head_contents("Order Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?> 

	<script>

        var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();
        
        function toggle( x, origColor ) 
        {
            var newColor = 'yellow';
            if ( x.style ) {
                x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
            }
        }
        
        function check_all_data()
        {
            var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
            tbl_row_count = tbl_row_count - 1;

            for( var i = 1; i <= tbl_row_count; i++ )
            {
                $('#tr_'+i).trigger('click'); 
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
            //alert(ddd);
            $('#order_id').val( id );
            $('#order_no').val( ddd );
        }
		
		/*function js_set_value(data)
		{
			$('#order_id').val(data);
			parent.emailwindow.hide();
		}*/
		
	</script>

    </head>

    <body>
        <div align="center" style="width:880px;">
            <form name="searchdescfrm"  id="searchdescfrm">
                <fieldset style="width:870px;margin-left:10px">
                    <legend>Enter search words</legend>
                    <table cellpadding="0" cellspacing="0" width="800" class="rpt_table">
                        <thead>
                            <th>Buyer Name</th>
                            <th>Order No</th>
                            <th width="230">Shipment Date Range</th>
                            <th>
                                <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
                                <input type="hidden" name="order_id" id="order_id" class="text_boxes" value="">
                                <input type="hidden" name="order_no" id="order_no" value="" />
                            </th>
                        </thead>
                        <tr class="general">
                            <td>
                                <?
                                echo create_drop_down( "cbo_buyer_name", 170, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "",$data[0] );
                                ?>
                            </td>
                            <td>
                                <input type="text" style="width:130px;" class="text_boxes" name="txt_order_no" id="txt_order_no" />
                            </td>
                            <td>
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" readonly>
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px" placeholder="To Date" readonly>
                            </td>
                            <td>
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_order_no').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+'<? echo $type; ?>', 'create_po_search_list_view', 'search_div', 'item_transfer_report_all_category_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
                        </tr>
                    </table>
                    <div style="margin-top:10px" id="search_div"></div> 
                </fieldset>
            </form>
        </div>    
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if($action=='create_po_search_list_view')
{
	$data=explode('_',$data);
	
	if ($data[0]==0) $buyer="%%"; else $buyer=$data[0];
	$search_string="%".trim($data[1])."%";
	$company_id=$data[2];
	
	if ($data[3]!="" &&  $data[4]!="") 
	{
		if($db_type==0)
		{
			$shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3],'','',1)."' and '".change_date_format($data[4],'','',1)."'";
		}
	}
	else 
		$shipment_date ="";
	
	$type=$data[5];
	$arr=array (2=>$company_arr,3=>$buyer_arr);
	
	if($db_type==0) $year_field="YEAR(a.insert_date) as year,"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year,";
	else $year_field="";//defined Later
	
	if($type=="from") $status_cond=" and b.status_active in(1,3)"; else $status_cond=" and b.status_active=1";
	$sql= "select a.job_no_prefix_num, $year_field a.job_no,a.company_name, a.buyer_name, a.style_ref_no, a.job_quantity, b.id, b.po_number, b.po_quantity, b.pub_shipment_date as shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$company_id and a.buyer_name like '$buyer' and b.po_number like '$search_string' and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 $status_cond $shipment_date order by b.id, b.pub_shipment_date";  
	
	echo create_list_view("tbl_list_search", "Job No,Year,Company,Buyer Name,Style Ref. No,Job Qty.,PO number,PO Quantity,Shipment Date", "70,60,70,80,120,90,110,90,80","850","200",0, $sql , "js_set_value", "id,po_number", "", 1, "0,0,company_name,buyer_name,0,0,0,0", $arr , "job_no_prefix_num,year,company_name,buyer_name,style_ref_no,job_quantity,po_number,po_quantity,shipment_date", "","",'0,0,0,0,0,1,0,1,3','',1);
	
	exit();
}
//report generated here--------------------//
if($action=="generate_report")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
    $txt_from_order_id=str_replace("'","",$txt_from_order_id);
    $txt_to_order_id=str_replace("'","",$txt_to_order_id);

    $userArr = return_library_array("select id,user_name from user_passwd","id","user_name");

    $from_company_cond=$to_company_cond=$from_store_cond=$to_store_cond=$from_order_cond=$to_order_cond =$cbo_item_cat_cond= $category_name = $transfer_criteria_cond="";
	if($cbo_transfer_type==1)
	{
        if($cbo_company_from!=0){$from_company_cond = " and a.company_id=$cbo_company_from";}
        if($cbo_company_to!=0){$to_company_cond = " and a.to_company=$cbo_company_to"; }   
    }
	if($cbo_transfer_type==2)
	{
        if($cbo_store_from!=0){$from_store_cond = " and b.from_store=$cbo_store_from";}
        if($cbo_store_to!=0){$to_store_cond = " and b.to_store=$cbo_store_to"; }  
    }
	if($cbo_transfer_type==4)
	{
        if($txt_from_order_id!=''){$from_order_cond = " and (a.from_order_id in($txt_from_order_id) or  b.from_order_id in($txt_from_order_id))";}
        if($txt_to_order_id!=''){$to_order_cond = " and ( a.to_order_id in($txt_to_order_id) or  b.to_order_id in($txt_to_order_id))"; } 
    }
    
    if( $cbo_item_cat==3)
    {
        if($cbo_transfer_type!=0){$transfer_criteria_cond = " and a.transfer_criteria = $cbo_transfer_type and b.active_dtls_id_in_transfer=1";}
    }
    else
    {
        if($cbo_transfer_type!=0){$transfer_criteria_cond = " and a.transfer_criteria = $cbo_transfer_type";}
    }
    
    if($cbo_item_cat!=0)
    {
        if($cbo_item_cat==1) { $cbo_item_cat_cond= "and b.item_category=1"; $category_name ="Yarn";}
        if($cbo_item_cat==2) { $cbo_item_cat_cond= "and b.item_category=13"; $category_name ="Grey Fabric";}
        if($cbo_item_cat==3) { $cbo_item_cat_cond= "and b.item_category=2"; $category_name ="Finish Fabric";}
        if($cbo_item_cat==4) { $cbo_item_cat_cond= "and b.item_category=4"; $category_name ="Accessories";}
        if($cbo_item_cat==5 ) { $cbo_item_cat_cond= "and b.item_category in (".implode(',',array_keys($general_item_category)).")"; $category_name ="General";}
		if($cbo_item_cat==6 ) { $cbo_item_cat_cond= "and b.item_category in (5,6,7,23)"; $category_name ="Chemical";}

    }
	if($db_type==0)
	{
		if($txt_date_from=="" || $txt_date_to=="")
		{
			$txt_date_con="";
		}
		else
		{
			$txt_date_con=" and a.transfer_date between '$txt_date_from' and '$txt_date_to'";
		}
	}
	if($db_type==2)
	{
		if($txt_date_from=="" || $txt_date_to=="")
		{
			$txt_date_con="";
		}
		else
		{
			$txt_date_con=" and a.transfer_date between '".change_date_format($txt_date_from,'dd-mm-yyyy','-',1)."' and '".change_date_format($txt_date_to,'dd-mm-yyyy','-',1)."'";
		}
	}
    if($cbo_item_cat==1)
    {
        $tbl_width=1880;
        $div_width="1900px";
    }
    if($cbo_item_cat==2)
    {
        $tbl_width=1680;
        $div_width="1700px";
    }
    if($cbo_item_cat==3)
    {
        $tbl_width=1580;
        $div_width="1600px";
    }
    if($cbo_item_cat==4)
    {
        $tbl_width=1680;
        $div_width="1700px";
    }
    if($cbo_item_cat==5 || $cbo_item_cat==6)
    {
        $tbl_width=1600;
        $div_width="1620px";
    }
    $composition_arr=array(); $construction_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$construction_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	$sql="SELECT a.id as ID, a.ENTRY_FORM,a.transfer_date as TRANSFER_DATE,a.transfer_system_id as TRANSFER_SYSTEM_ID,a.transfer_criteria as TRANSFER_CRITERIA,a.company_id as COMPANY_ID,a.to_company as TO_COMPANY, a.inserted_by as TRANSFER_USER,b.from_store as FROM_STORE, b.to_store as TO_STORE,a.from_order_id as FROM_ORDER_ID,a.to_order_id as TO_ORDER_ID,b.from_order_id as DTLS_FROM_ORDER_ID,b.to_order_id as DTLS_TO_ORDER_ID, b.item_category as ITEM_CATEGORY, b.feb_description_id as FEB_DESCRIPTION_ID, b.yarn_lot as YARN_LOT,sum(b.transfer_qnty) as TRANSFER_QNTY,b.rate as RATE,sum(b.transfer_value) as TRANSFER_VALUE,b.from_prod_id as FROM_PROD_ID, c.supplier_id as SUPPLIER_ID,c.yarn_count_id as YARN_COUNT_ID,c.yarn_type as YARN_TYPE,c.yarn_comp_type1st as YARN_COMP_TYPE1ST,c.yarn_comp_type2nd as YARN_COMP_TYPE2ND,c.item_description as ITEM_DESCRIPTION,c.product_name_details as PRODUCT_NAME_DETAILS, c.item_code as ITEM_CODE, c.unit_of_measure as UNIT_OF_MEASURE, d.transfer_system_id as REQ_SYS_ID, d.transfer_date as REQ_DATE, d.inserted_by as REQ_USER, e.remarks as REMARKS, d.company_id as COMPANY, d.id as IDS
    from inv_item_transfer_mst a,product_details_master c,inv_item_transfer_dtls b
    left join inv_item_transfer_requ_mst d on b.requisition_mst_id=d.id and d.entry_form in(494,516) and d.status_active=1
    left join inv_item_transfer_requ_dtls e on d.id=e.mst_id and b.requisition_dtls_id=e.id and e.status_active=1
    where a.id=b.mst_id and b.from_prod_id=c.id $from_company_cond $to_company_cond $from_store_cond $to_store_cond $from_order_cond $to_order_cond $transfer_criteria_cond $cbo_item_cat_cond
    and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
    $txt_date_con
    group by a.id, a.entry_form,a.transfer_date,a.transfer_system_id,a.transfer_criteria,a.company_id,a.to_company,a.inserted_by,b.from_store,b.to_store,a.from_order_id,a.to_order_id,b.from_order_id,b.to_order_id, b.item_category,b.feb_description_id,b.yarn_lot,b.rate,b.from_prod_id, c.supplier_id,c.yarn_count_id,c.yarn_type,c.yarn_comp_type1st,c.yarn_comp_type2nd,c.item_description,c.product_name_details, c.item_code,c.unit_of_measure,d.transfer_system_id,d.transfer_date,d.inserted_by, e.remarks, d.company_id, d.id
    order by a.id ";
	
	//  echo $sql;
	
	$result_array=sql_select($sql);

    $po_order_id='';
	foreach ($result_array as $rows)
	{ 
        if($rows['FROM_ORDER_ID']!='' && $rows['TO_ORDER_ID']!='')
        {
            $po_order_id.=$rows['FROM_ORDER_ID'].','.$rows['TO_ORDER_ID'].',';
        }
        if($rows['DTLS_FROM_ORDER_ID']!='' && $rows['DTLS_TO_ORDER_ID']!='')
        {
            $po_order_id.=$rows['DTLS_FROM_ORDER_ID'].','.$rows['DTLS_TO_ORDER_ID'].',';
        }

	}

    if (!empty($po_order_id))
    {     
        $po_order_cond = '';
        if($db_type==2)
        {
            $p=1;
            $poIdArr=array_chunk(array_unique(explode(",",chop($po_order_id,','))),999);
            foreach($poIdArr as $ids)
            {
                if($p==1) $po_order_cond .=" and ( b.id in(".implode(",",$ids).")"; else $po_order_cond .=" or  b.id in(".implode(",",$ids).")";
                $p++;
            }
            $po_order_cond .=" )";
        }
        else
        {
            $poIds = implode(',',array_unique(explode(",",chop($po_order_id,','))));
            $po_order_cond = " and b.id in ($poIds) ";
        }
    }
    $order_sql= sql_select("SELECT a.job_no as JOB_NO,a.style_ref_no as STYLE_REF_NO,a.buyer_name as BUYER_NAME, b.id as ID, b.po_number as PO_NUMBER from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $po_order_cond");  

    $order_arr=array();
	if(count($order_sql)>0)
    {
        foreach($order_sql as $val)
        {
            $order_arr[$val['ID']]['STYLE_REF_NO']=$val['STYLE_REF_NO'];
            $order_arr[$val['ID']]['PO_NUMBER']=$val['PO_NUMBER'];
            $order_arr[$val['ID']]['JOB_NO']=$val['JOB_NO'];
            $order_arr[$val['ID']]['BUYER_NAME']=$val['BUYER_NAME'];
        }
    }

    ob_start();
    ?>
    <div style="width:<?=$div_width;?>">
    <br>
	</table>
    <table width="<?=$tbl_width;?>" border="1" rules="all" >
    <tr><th width="100%" style="font-size:15px;"><?=$category_name;?></th></tr>
    <tr>
			<th align="canter" ><?php echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --",4); ?></th>
		</tr>
    </table>
    <br>
    <table width="<?=$div_width;?>" border="1" rules="all" class="rpt_table">
        <thead>
            <tr>
                <th width="35">SL</th>
                <th width="80">Transfer Date</th>
                <th width="120">Transfer ID.</th>
                    <?
                    if($cbo_item_cat==5 || $cbo_item_cat==6)
                    {
                        ?>
                            <th width="120">Transfer Req ID</th>
                            <th width="80">Transfer Req Date</th>
                        <?
                    }
                    ?>
                <th width="100">From</th>
                    <?
                    if($cbo_item_cat==1 || $cbo_item_cat==2 || $cbo_item_cat==3 || $cbo_item_cat==4 ){
                    ?>
                        <th width="120">Buyer</th>
                        <th width="120">Job</th>
                        <th width="120">Style</th>
                    <?
                    }
                    ?>
                <th width="100">To</th>
                    <?
                    if($cbo_item_cat==1 || $cbo_item_cat==2 || $cbo_item_cat==3 || $cbo_item_cat==4 ){
                    ?>
                        <th width="120">Buyer</th>
                        <th width="120">Job</th>
                        <th width="120">Style</th>
                    <?
                    }
                    if($cbo_item_cat==1){
                    ?>
                        <th width="80">Count</th>
                    <?
                    }
                    if($cbo_item_cat==1 || $cbo_item_cat==2 ){
                    ?>
                        <th width="120">Composition</th>
                    <?
                    }
                    if($cbo_item_cat==1){
                    ?>
                        <th width="60">Type </th>
                    <?
                    }
                    if($cbo_item_cat==1 || $cbo_item_cat==2 ){
                    ?>
                        <th width="60">Lot No. </th>
                    <?
                    }
                    if($cbo_item_cat==1 || $cbo_item_cat==4 ){
                    ?>
                        <th width="100">Supplier</th>
                    <?
                    }
                    if($cbo_item_cat==5 || $cbo_item_cat==6){
                    ?>
                        <th width="100">Item Category</th>
                    <?
                    }
                    if($cbo_item_cat==3 || $cbo_item_cat==4 || $cbo_item_cat==5 || $cbo_item_cat==6){
                    ?>
                        <th width="80">Item Code</th>
                        <th width="150">Item Description</th>
                        <th width="60">UOM</th>
                    <?
                    if($cbo_item_cat==5 || $cbo_item_cat==6)
                    {
                        ?>
                            <th width="100">Remarks</th>
                        <?
                        }
                    }
                    ?>
                <th width="80">Quantity</th>
                <th width="80">Rate</th>
                <th width="80">Total Value</th>
                    <?
                        if($cbo_item_cat==5 || $cbo_item_cat==6){
                        ?>
                            <th width="80">Tran. Req. User ID</th>
                        <?
                        }
                    ?>
                <th >Trans. User ID</th>
            </tr>
    </thead>
    </table>
            
    <div style=" max-height:360px; width:<?=$div_width;?>; overflow-y:scroll;" id="scroll_body">
        <table class="rpt_table" id="table_body" width="<?=$tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
        <? 
        $i=1;
        foreach($result_array as $rows)
        {
            $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
            if($rows['TRANSFER_CRITERIA']==1)
            {
                $from_transfer=$company_arr[$rows['COMPANY_ID']];
                $to_transfer=$company_arr[$rows['TO_COMPANY']];
            }
            if($rows['TRANSFER_CRITERIA']==2)
            {
                $from_transfer=$store_arr[$rows['FROM_STORE']];
                $to_transfer=$store_arr[$rows['TO_STORE']];
            }
            if($rows['TRANSFER_CRITERIA']==4 && $rows['ENTRY_FORM']!=82)
            {
                $from_transfer= $order_arr[$rows['FROM_ORDER_ID']]['PO_NUMBER'];
                $to_transfer= $order_arr[$rows['TO_ORDER_ID']]['PO_NUMBER'];
                $from_style= $order_arr[$rows['FROM_ORDER_ID']]['STYLE_REF_NO'];
                $to_style= $order_arr[$rows['TO_ORDER_ID']]['STYLE_REF_NO'];
                $from_job= $order_arr[$rows['FROM_ORDER_ID']]['JOB_NO'];
                $to_job= $order_arr[$rows['TO_ORDER_ID']]['JOB_NO'];
                $from_buyer= $buyer_arr[$order_arr[$rows['FROM_ORDER_ID']]['BUYER_NAME']];
                $to_buyer= $buyer_arr[$order_arr[$rows['TO_ORDER_ID']]['BUYER_NAME']];
            }
            if($rows['TRANSFER_CRITERIA']==4 && $rows['ENTRY_FORM']==82)
            {
                $from_transfer= $order_arr[$rows['DTLS_FROM_ORDER_ID']]['PO_NUMBER'];
                $to_transfer= $order_arr[$rows['DTLS_TO_ORDER_ID']]['PO_NUMBER'];
                $from_style= $order_arr[$rows['DTLS_FROM_ORDER_ID']]['STYLE_REF_NO'];
                $to_style= $order_arr[$rows['DTLS_TO_ORDER_ID']]['STYLE_REF_NO'];
                $from_job= $order_arr[$rows['DTLS_FROM_ORDER_ID']]['JOB_NO'];
                $to_job= $order_arr[$rows['DTLS_TO_ORDER_ID']]['JOB_NO'];
                $from_buyer= $buyer_arr[$order_arr[$rows['DTLS_FROM_ORDER_ID']]['BUYER_NAME']];
                $to_buyer= $buyer_arr[$order_arr[$rows['DTLS_TO_ORDER_ID']]['BUYER_NAME']];
            }
            if($rows['TRANSFER_CRITERIA']!=4 && $cbo_item_cat!=5)
            {
                $from_style= $order_arr[$rows['DTLS_FROM_ORDER_ID']]['STYLE_REF_NO'];
                $to_style= $order_arr[$rows['DTLS_TO_ORDER_ID']]['STYLE_REF_NO'];
                $from_job= $order_arr[$rows['DTLS_FROM_ORDER_ID']]['JOB_NO'];
                $to_job= $order_arr[$rows['DTLS_TO_ORDER_ID']]['JOB_NO'];
                $from_buyer= $buyer_arr[$order_arr[$rows['DTLS_FROM_ORDER_ID']]['BUYER_NAME']];
                $to_buyer= $buyer_arr[$order_arr[$rows['DTLS_TO_ORDER_ID']]['BUYER_NAME']];
            }
            if($cbo_item_cat==1) { $composition_name=$composition[$rows['YARN_COMP_TYPE1ST']] ;}
            if($cbo_item_cat==2) { $composition_name=$construction_arr[$rows['FEB_DESCRIPTION_ID']].' '.$composition_arr[$rows['FEB_DESCRIPTION_ID']] ;}

            ?> 
            <tr bgcolor="<? echo $bgcolor;?>" id="tr_<? echo $i;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')">
                <td align="center" width="35"><? echo $i;?></td>
                <td width="80" align="center"><? echo change_date_format($rows['TRANSFER_DATE']);?></td>
                <td width="120" align="center"> <a href="##" onclick="set_item_transfer_report('<? echo $rows['COMPANY_ID'];?>','<? echo $rows['ID'];?>')" > <? echo $rows['TRANSFER_SYSTEM_ID'];?></a> </td>
                <?
                    if($cbo_item_cat==5 || $cbo_item_cat==6)
                    {
                        ?>
                            <td width="120"> <a href="##" onclick="set_transfer_req_report('<? echo $rows['COMPANY'];?>','<? echo $rows['IDS'];?>')"> <? echo $rows['REQ_SYS_ID'];?></a> </td>
                            <td width="80"><? echo change_date_format($rows['REQ_DATE']);?></td>
                        <?
                    }
                ?>
                <td width="100"><? echo $from_transfer;?></td>
                    <?
                    if($cbo_item_cat==1 || $cbo_item_cat==2 || $cbo_item_cat==3 || $cbo_item_cat==4 ){
                    ?>
                <td width="120"><? echo $from_buyer;?></td>
                <td width="120"><? echo $from_job;?></td>
                <td width="120"><? echo $from_style;?></td>
                    <?
                    }
                    ?>
                <td width="100"><? echo $to_transfer;?></td>
                    <?
                    if($cbo_item_cat==1 || $cbo_item_cat==2 || $cbo_item_cat==3 || $cbo_item_cat==4 ){
                    ?>
                <td width="120"><p><? echo $to_buyer; ?></p></td>
                <td width="120"><p><? echo $to_job; ?></p></td>
                <td width="120"><p><? echo $to_style; ?></p></td>
                    <?
                    }
                    if($cbo_item_cat==1){
                    ?>
                <td width="80"><p><? echo $yarn_count_arr[$rows['YARN_COUNT_ID']]?></p></td>
                    <?
                    }
                    if($cbo_item_cat==1 || $cbo_item_cat==2 ){
                    ?>
                <td width="120"><p><? echo $composition_name;?></p></td>
                    <?
                    }
                    if($cbo_item_cat==1){
                    ?>
                <td width="60" ><? echo $yarn_type[$rows['YARN_TYPE']];?></td>
                    <?
                    }
                    if($cbo_item_cat==1 || $cbo_item_cat==2 ){
                    ?>
                <td width="60" ><? echo $rows['YARN_LOT'];;?></td>
                    <?
                    }
                    if($cbo_item_cat==1 || $cbo_item_cat==4 ){
                    ?>
                <td width="100" ><? echo $supplier_arr[$rows['SUPPLIER_ID']];?></td>
                    <?
                    }
                    if($cbo_item_cat==5 || $cbo_item_cat==6){
                    ?>
                <td width="100" ><? echo $item_category[$rows['ITEM_CATEGORY']];?></td>
                    <?
                    }
                    if($cbo_item_cat==3 || $cbo_item_cat==4 ||$cbo_item_cat==5 || $cbo_item_cat==6){
                    ?>
                        <td width="80" style=" word-break: break-all;" ><? echo $rows['ITEM_CODE'];?></td>
                        <td width="150"><? echo $rows['PRODUCT_NAME_DETAILS'];?></td>
                        <td width="60" ><? echo $unit_of_measurement[$rows['UNIT_OF_MEASURE']];?></td>
                    <?
                    }
                    if($cbo_item_cat==5 || $cbo_item_cat==6){
                    ?>
                        <td width="100" style=" word-break: break-all;"><? echo $rows['REMARKS'];?></td>
                    <?
                    }
                    ?>
                <td width="80" align="right"><? echo number_format($rows['TRANSFER_QNTY'],4);$total_qnty+=$rows['TRANSFER_QNTY'];?></td>
                <td width="80" align="right"><? echo number_format(  $rows['RATE'],6);?></td>
                <td width="80" align="right"><? echo number_format($rows['TRANSFER_VALUE'],4);$total_value+=$rows['TRANSFER_VALUE'];?></td>
                    <?
                        if($cbo_item_cat==5 || $cbo_item_cat==6){
                        ?>
                            <td width="80"><? echo $userArr[$rows['REQ_USER']];?></td>
                        <?
                        }
                    ?>
                <td ><? echo $userArr[$rows['TRANSFER_USER']];?></td>
            </tr>
        <? 
        $i++;
        }
        ?>
        </table>
        <table class="rpt_table" width="<?=$tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
        <tfoot>
            <th width="35">&nbsp;</th>
            <th width="80">&nbsp;</th>
            <th width="120">&nbsp;</th>
                <?
                if($cbo_item_cat==5 || $cbo_item_cat==6)
                {
                    ?>
                        <th width="120">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                    <?
                }
                ?>
            <th width="100">&nbsp;</th>
                <?
                if($cbo_item_cat==1 || $cbo_item_cat==2 || $cbo_item_cat==3 || $cbo_item_cat==4 ){
                ?>
            <th width="120">&nbsp;</th>
            <th width="120">&nbsp;</th>
            <th width="120">&nbsp;</th>
                <?
                }
                ?>
            <th width="100">&nbsp;</th>
                <?
                if($cbo_item_cat==1 || $cbo_item_cat==2 || $cbo_item_cat==3 || $cbo_item_cat==4 ){
                ?>
            <th width="120">&nbsp;</th>
            <th width="120">&nbsp;</th>
            <th width="120">&nbsp;</th>
                <?
                }
                if($cbo_item_cat==1){
                ?>
            <th width="80">&nbsp;</th>
                <?
                }
                if($cbo_item_cat==1 || $cbo_item_cat==2 ){
                ?>
            <th width="120">&nbsp;</th>
                <?
                }
                if($cbo_item_cat==1){
                ?>
            <th width="60">&nbsp; </th>
                <?
                }
                if($cbo_item_cat==1 || $cbo_item_cat==2 ){
                ?>
            <th width="60">&nbsp; </th>
                <?
                }
                if($cbo_item_cat==1 || $cbo_item_cat==4 ){
                ?>
            <th width="100">&nbsp;</th>
                <?
                }
                if($cbo_item_cat==5 || $cbo_item_cat==6){
                ?>
            <th width="100">&nbsp;</th>
                <?
                }
                if($cbo_item_cat==3 || $cbo_item_cat==4 || $cbo_item_cat==5 || $cbo_item_cat==6){
                ?>
                    <th width="80">&nbsp;</th>
                    <th width="150">&nbsp;</th>
                    <th width="60">&nbsp;</th>
                <?
                }
                if($cbo_item_cat==5 || $cbo_item_cat==6){
                ?>
                    <th width="100">&nbsp;</th>
                <?
                }
                ?>
            <th width="80" id="grand_total_qnty"><? echo $total_qnty;?></th>
            <th width="80">&nbsp;</th>
            <th width="80" id="grand_total_value"><? echo number_format($total_value,2);?></th>
                <?
                    if($cbo_item_cat==5 ){
                    ?>
                        <th width="80">&nbsp;</th>
                    <?
                    }
                ?>
            <th >&nbsp;</th>
        </tfoot>
    </table>
    </div>

    </div>
    <?
    $html=ob_get_contents();	
    ob_end_clean();	
  
    foreach (glob("*.xls") as $filename) {
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc,$html);
    echo "$html**$filename**$cbo_item_cat"; 
    disconnect($con);
    exit(); 
}

?>

