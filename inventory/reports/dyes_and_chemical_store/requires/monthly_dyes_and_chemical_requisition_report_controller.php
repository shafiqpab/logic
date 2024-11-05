<?php
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


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
		$itemgroupArr = return_library_array("select id,item_name from  lib_item_group where status_active=1 and is_deleted=0","id","item_name");
		$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
		if ($data[0]=='') $company_name =""; else $company_name =" and company_id in($data[0])";
		if ($data[1]=='') $item_category_name =" and item_category_id in(5,6,7,23)"; else $item_category_name =" and item_category_id in($data[1])";
		if ($data[2]=='') $item_name =""; else $item_name =" and item_group_id in($data[2])";

	$sql="SELECT id, item_account, item_category_id, item_group_id, item_description, supplier_id, sub_group_name from product_details_master where status_active=1 and is_deleted=0 $company_name $item_category_name $item_name";
		$arr=array(1=>$item_category,2=>$itemgroupArr,5=>$supplierArr);
		echo  create_list_view("list_view", "Item Account,Item Category,Item Group,Item Sub Group,Item Description,Supplier,Product ID", "70,120,120,100,170,100","780","400",0, $sql , "js_set_value", "id,item_description", "", 0, "0,item_category_id,item_group_id,0,0,supplier_id,0", $arr , "item_account,item_category_id,item_group_id,sub_group_name,item_description,supplier_id,id", "",'setFilterGrid("list_view",-1);','0,0,0,0,0,0,0','',1) ;
		exit();
}


if ($action=="item_group_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	// print_r ($data);
    ?>
    <script>
	var selected_id = new Array, selected_name = new Array(); 
	selected_attach_id = new Array();

	function check_all_data() {
		var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
		tbl_row_count = tbl_row_count - 1;
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
		$('#item_name_id').val( id );
		$('#item_name_val').val( ddd );
	}
	</script>
    <input type="hidden" id="item_name_id"/>
    <input type="hidden" id="item_name_val"/>
    <?
	if ($data[1]=='') $item_category =""; else $item_category =" and item_category in($data[1])";
	
	$sql="SELECT id,item_name from  lib_item_group where status_active=1 and is_deleted=0 $item_category"; 

	echo  create_list_view("list_view", "Item Name", "350","480","330",0, $sql , "js_set_value", "id,item_name", "", 1, "0", $arr , "item_name", "",'setFilterGrid("list_view",-1);','0','',1);
	exit();
}


if($action=="generate_report")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$report_type=str_replace("'","",$report_type);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_item_category=str_replace("'","",$cbo_item_category);
	$item_account_id=str_replace("'","",$item_account_id);
	$item_group_id=str_replace("'","",$item_group_id);
	$cbo_store_name=str_replace("'","",$cbo_store_name);
	
    if ($cbo_company_name=='') $company_id =""; else $company_id =" and a.company_id in ($cbo_company_name)";
    if ($cbo_item_category=='') $item_category_id=" and b.item_category_id in(5,6,7,23)"; else $item_category_id=" and b.item_category_id in ($cbo_item_category)";
    if ($cbo_item_category=='') $item_category_cond=" and a.item_category in(5,6,7,23)"; else $item_category_cond=" and a.item_category in ($cbo_item_category)";
    if ($item_group_id=='') $group_id=""; else $group_id=" and b.item_group_id in($item_group_id)";
    if ($item_account_id=='') $item_account=""; else $item_account=" and a.prod_id in ($item_account_id)";
    if ($item_account_id=='') $item_account_cond=""; else $item_account_cond=" and b.id in ($item_account_id)";
    if ($cbo_store_name=='')  $store_id="";  else  $store_id=" and a.store_id in ($cbo_store_name)";

    if($db_type==0)
    {
        $txt_date_cond=change_date_format($txt_date,'yyyy-mm-dd');
    }
    else if($db_type==2)
    {
        $txt_date_cond=change_date_format($txt_date,'','',1);
    }
    else
    {
        $txt_date_cond="";
    }
    $companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
    $companyShortArr = return_library_array("select id,company_short_name from lib_company where status_active=1 and is_deleted=0","id","company_short_name");
    // $itemnameArr = return_library_array("select id,item_name from lib_item_creation where status_active=1 and is_deleted=0","id","item_name");
    $itemgroupArr = return_library_array("select id,item_name from  lib_item_group where status_active=1 and is_deleted=0","id","item_name");

    if($txt_date_cond!="")
    {
        if($txt_date_cond!="") $trans_date_cond=" and a.transaction_date<='$txt_date_cond'";
    }

    $all_company=explode(',',$cbo_company_name);
    $com_count=count($all_company);
    foreach($all_company as $row)
    {
        $company_name.= $companyArr[$row].", ";
    }
    $width_td=1330+($com_count*100);
    $width_div=1350+($com_count*100);

    $trans_sql="SELECT b.id as PROD_ID, a.transaction_date as TRANSACTION_DATE,a.item_category as ITEM_CATEGORY, a.transaction_type as TRANSACTION_TYPE, a.cons_quantity as CONS_QUANTITY, a.company_id as COMPANY_ID, b.unit_of_measure as UNIT_OF_MEASURE, b.item_group_id as ITEM_GROUP_ID, b.item_description as ITEM_DESCRIPTION 
    from inv_transaction a, product_details_master b
    where a.prod_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_id $group_id $prod_cond $store_id $item_account $item_account_cond $trans_date_cond $item_category_id $item_category_cond order by b.id desc";
    
    // echo $trans_sql;die;
    $trnasactionData = sql_select($trans_sql);
    // $lastmonth=date('d-m-Y', strtotime('-1 month', strtotime($txt_date)));
    $lastmonth=month_add(date("Y-m-d",strtotime($txt_date)),-1);

    $lastmonthArr=explode('-',$lastmonth);
    if($db_type==0)
    {
        $startMonthDate = date("Y-m-d",strtotime($lastmonthArr[0].'-'.$lastmonthArr[1]));
        $endMonthDate = date("Y-m-t",strtotime($lastmonthArr[0].'-'.$lastmonthArr[1]));
    }
    else
    {
        $startMonthDate = date("d-M-Y", strtotime($lastmonthArr[0].'-'.$lastmonthArr[1]));
        $endMonthDate = date("t-M-Y", strtotime($lastmonthArr[0].'-'.$lastmonthArr[1]));
    }
    // $lastmonth=date_format(date_sub(date_create($txt_date),date_interval_create_from_date_string("29 days")),"d-m-Y");
    // echo $startMonthDate."*".$endMonthDate;
    $data_array=array();$stock_data_array=array();$last_month_consumption=array();
    foreach($trnasactionData as $row)
    {
        $data_array[$row["UNIT_OF_MEASURE"]][$row["PROD_ID"]]['PROD_ID']=$row["PROD_ID"];
        $data_array[$row["UNIT_OF_MEASURE"]][$row["PROD_ID"]]['ITEM_CATEGORY']=$row["ITEM_CATEGORY"];
        $data_array[$row["UNIT_OF_MEASURE"]][$row["PROD_ID"]]['ITEM_GROUP_ID']=$row["ITEM_GROUP_ID"];
        $data_array[$row["UNIT_OF_MEASURE"]][$row["PROD_ID"]]['ITEM_DESCRIPTION']=$row["ITEM_DESCRIPTION"];
        $data_array[$row["UNIT_OF_MEASURE"]][$row["PROD_ID"]]['UOM']=$row["UNIT_OF_MEASURE"];
        if($row["TRANSACTION_TYPE"]==1 || $row["TRANSACTION_TYPE"]==4 || $row["TRANSACTION_TYPE"]==5)
        {
            $stock_data_array[$row["UNIT_OF_MEASURE"]][$row["PROD_ID"]][$row["COMPANY_ID"]]['rcv_total_qnty']+=$row["CONS_QUANTITY"];
        }
        if($row["TRANSACTION_TYPE"]==2 || $row["TRANSACTION_TYPE"]==3 || $row["TRANSACTION_TYPE"]==6)
        {
            $stock_data_array[$row["UNIT_OF_MEASURE"]][$row["PROD_ID"]][$row["COMPANY_ID"]]['iss_total_qnty']+=$row["CONS_QUANTITY"];
        }
        if($row["TRANSACTION_TYPE"]==2 && strtotime($row["TRANSACTION_DATE"])>=strtotime($startMonthDate) && strtotime($row["TRANSACTION_DATE"])<=strtotime($endMonthDate))
        {
            $last_month_consumption[$row["UNIT_OF_MEASURE"]][$row["PROD_ID"]][$row["COMPANY_ID"]]['last_month']+=$row["CONS_QUANTITY"];
        }
    }
    unset($trnasactionData);
    // var_dump($data_array);die;
    //print_r($data_array);die;
    $this_month=explode('-',$txt_date);
    if($db_type==0)
    {
        $startDate = date("Y-m-d",strtotime($this_month[2].'-'.$this_month[1]));
        $endDate = date("Y-m-t",strtotime($this_month[2].'-'.$this_month[1]));
    }
    else
    {
        $startDate = date("d-M-Y", strtotime($this_month[2].'-'.$this_month[1]));
        $endDate = date("t-M-Y", strtotime($this_month[2].'-'.$this_month[1]));
    }

    if ($cbo_item_category=='') $category_cond =""; else $category_cond=" and b.item_category in(".$cbo_item_category.")";
    if ($cbo_company_name=='') $company_cond =""; else $company_cond =" and a.company_id in ($cbo_company_name)";
    $requisition_sql="SELECT a.company_id as COMPANY_ID, b.id as DTLS_ID, b.product_id as PROD_ID, b.quantity as QUANTITY, b.cons_uom as CONS_UOM  from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b where a.id=b.mst_id and a.requisition_date >='$startDate' and a.requisition_date <='$endDate' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $company_cond $category_cond ";
    // echo  $requisition_sql;

    $requisition_data = sql_select($requisition_sql);
    $requisition_date_array=array();
    foreach($requisition_data as $row)
    {
        $requisition_date_array[$row["CONS_UOM"]][$row["PROD_ID"]]['QUANTITY']+=$row["QUANTITY"];
        $requisition_date_array[$row["CONS_UOM"]][$row["PROD_ID"]]['DTLS_ID'].=$row["DTLS_ID"].",";
    }

    $i=1;
    ob_start();
    ?>
    <style>
        table tr td{
            word-wrap:break-word;
            word-break: break-all;
        }
    </style>
    <div align="center" style="height:auto; width:auto; margin:0 auto; padding:0; width:<?=$width_div;?>px">
        <table width="<?=$width_td;?>" cellpadding="0" cellspacing="0" id="caption" align="center">
            <thead>
                <tr style="border:none;">
                    <td colspan="20" class="form_caption" align="center" style="border:none; font-size:14px;">
                        <b>Company Name : <?  echo chop($company_name,', '); ?></b>
                    </td>
                </tr>
            </thead>
        </table>
        <table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="<?=$width_td;?>" rules="all" id="rpt_table_header">
            <thead>
                <tr>
                    <th rowspan="2" width="50">Sl No</th>
                    <th rowspan="2" width="70">Prod. ID</th>
                    <th rowspan="2" width="100">Item Category</th>
                    <th rowspan="2" width="120">Item Group</th>
                    <th rowspan="2" width="150">Item Description</th>
                    <th rowspan="2" width="60">UOM</th>
                    <th colspan="<?=$com_count;?>">Stock Unit Wise<br>Up to <?=$txt_date;?></th>
                    <th rowspan="2" width="100">Total Stock  QTY.</th>
                    <th colspan="<?=$com_count;?>">Last Month Conssumption<br><?=$startMonthDate;?> To <?=$endMonthDate;?></th>
                    <th rowspan="2" width="100">Total Con. QTY.</th>
                    <th colspan="<?=$com_count;?>">Per Day  Ave. Con.</th>
                    <th rowspan="2" width="100">Total Con. QTY.</th>
                    <th colspan="2" width="80">Approximate Required qty </th>
                    <th rowspan="2" width="80">Total</th>
                    <th rowspan="2" width="80">Actual Requirement</th>
                    <th rowspan="2" width="80">Requisition QTY.</th>
                </tr>
                <tr>
                    <?
                        foreach($all_company as $row)
                        {
                            ?>
                                <th width="100"><?=$companyShortArr[$row];?></th>
                            <?
                        }
                        foreach($all_company as $row)
                        {
                            ?>
                                <th width="100"><?=$companyShortArr[$row];?></th>
                            <?
                        }
                        foreach($all_company as $row)
                        {
                            ?>
                                <th width="100"><?=$companyShortArr[$row];?></th>
                            <?
                        }
                    ?>
                    <th width="80">for 30 days</th>
                    <th width="80">30% Extra</th>
                </tr>
            </thead>
        </table>
        <div style="width:<?=$width_div;?>px; max-height:250px; overflow-y:scroll; overflow-x:hidden;" id="scroll_body">
        <table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body_id" width="<?=$width_td;?>" rules="all" align="left">
        <?
        $grand_total_stock=$grand_total_consumption=$grand_total_consumption_daily=$grand_total_requisition=0;
        $com_prodStockBalance=$com_prodConssumptionLastMonth=$com_prodConssumptionDaily=array();
        $i=1;
        foreach($data_array as $key=>$rowUom)
        {
            $total_stock=$total_consumption=$total_consumption_daily=$total_requisition=0;
            foreach($rowUom as $row)
            {
                if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" style=" word-wrap:break-word;"  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="50" align="center"><? echo $i; ?></td>
                    <td width="70" ><? echo $row['PROD_ID']; ?></td>
                    <td width="100" ><? echo $item_category[$row['ITEM_CATEGORY']]; ?></td>
                    <td width="120" ><? echo $itemgroupArr[$row['ITEM_GROUP_ID']]; ?></td>
                    <td width="150" ><? echo $row['ITEM_DESCRIPTION']; ?></td>
                    <td width="60" align="center"><? echo $unit_of_measurement[$row['UOM']]; ?></td>
                    <?php
                        $prodStockBalance=0;
                        foreach($all_company as $val)
                        {
                            $stockBalance=$stock_data_array[$row['UOM']][$row["PROD_ID"]][$val]['rcv_total_qnty']-$stock_data_array[$row['UOM']][$row["PROD_ID"]][$val]['iss_total_qnty'];
                            $prodStockBalance+=$stockBalance;$total_stock+=$stockBalance;$grand_total_stock+=$stockBalance;
                            $com_prodStockBalance[$row['UOM']][$val]['sub_total']+=$stockBalance;
                            $com_prodStockBalance[$val]['grand_total']+=$stockBalance;
                            ?>
                                <td width="80" align="right"><? echo number_format($stockBalance,2); ?></td>
                            <?
                        }
                    ?>
                    <td width="100" align="right"><? echo number_format($prodStockBalance,2); ?></td>
                    <?php
                        $prodConssumptionLastMonth=0;
                        foreach($all_company as $val)
                        {
                            $conssumptionLastMonth=$last_month_consumption[$row['UOM']][$row["PROD_ID"]][$val]['last_month'];
                            $prodConssumptionLastMonth+=$conssumptionLastMonth;$total_consumption+=$conssumptionLastMonth;$grand_total_consumption+=$conssumptionLastMonth;
                            $com_prodConssumptionLastMonth[$row['UOM']][$val]['sub_total']+=$conssumptionLastMonth;
                            $com_prodConssumptionLastMonth[$val]['grand_total']+=$conssumptionLastMonth;
                            ?>
                                <td width="100" align="right"><? echo number_format($conssumptionLastMonth,2); ?></td>
                            <?
                        }
                    ?>
                    <td width="100" align="right"><? echo number_format($prodConssumptionLastMonth,2); ?></td>
                    <?php
                        $prodConssumptionDaily=0;
                        foreach($all_company as $val)
                        {
                            $conssumptionDaily=($last_month_consumption[$row['UOM']][$row["PROD_ID"]][$val]['last_month'])/30;
                            $prodConssumptionDaily+=$conssumptionDaily;$total_consumption_daily+=$conssumptionDaily;$grand_total_consumption_daily+=$conssumptionDaily;
                            $com_prodConssumptionDaily[$row['UOM']][$val]['sub_total']+=$conssumptionDaily;
                            $com_prodConssumptionDaily[$val]['grand_total']+=$conssumptionDaily;
                            ?>
                                <td width="100" align="right"><? echo number_format($conssumptionDaily,2); ?></td>
                            <?
                        }
                    ?>
                    <td width="100" align="right"><? echo number_format($prodConssumptionDaily,2); ?></td>
                    <td width="80" align="right"><? echo number_format($prodConssumptionDaily*30,2); ?></td>
                    <td width="80" align="right"><? echo number_format((($prodConssumptionDaily*30)*.30),2); ?></td>
                    <td width="80" align="right"><? echo number_format(($prodConssumptionDaily*30)+(($prodConssumptionDaily*30)*.30),2); ?></td>
                    <td width="80" align="right"><? echo number_format((($prodConssumptionDaily*30)+(($prodConssumptionDaily*30)*.30))-$prodStockBalance,2); ?></td>
                    <td width="80" align="right">
                    <a href="##" onclick="fnc_purchase_requisition_details('<? echo chop($requisition_date_array[$row['UOM']][$row['PROD_ID']]['DTLS_ID'],',');?>','load_requisition_info')"><p><? echo number_format($requisition_date_array[$row['UOM']][$row["PROD_ID"]]['QUANTITY'],2); $total_requisition+=$requisition_date_array[$row['UOM']][$row["PROD_ID"]]['QUANTITY'];
                    $grand_total_requisition+=$requisition_date_array[$row['UOM']][$row["PROD_ID"]]['QUANTITY'];?></p></a>
                    </td>
                </tr>
                <?
                $i++;
            }
            ?>
                <tr >
                    <td colspan="6" align="right"><strong>UOM WISE TOTAL&nbsp;</strong></td>
                    <?php
                        foreach($all_company as $val)
                        {
                            ?>
                                <td width="100" align="right"><strong><?echo number_format($com_prodStockBalance[$key][$val]['sub_total'],2);?></strong></td>
                            <?
                        }
                    ?>
                    <td width="100" align="right"><strong><?echo number_format($total_stock,2);?></strong></td>
                    <?php
                        foreach($all_company as $val)
                        {
                            ?>
                                <td width="100" align="right"><strong><?echo number_format($com_prodConssumptionLastMonth[$key][$val]['sub_total'],2);?></strong></td>
                            <?
                        }
                    ?>
                    <td width="100" align="right"><strong><?echo number_format($total_consumption,2);?></strong></td>
                    <?php
                        foreach($all_company as $val)
                        {
                            ?>
                                <td width="100" align="right"><strong><?echo number_format($com_prodConssumptionDaily[$key][$val]['sub_total'],2);?></strong></td>
                            <?
                        }
                    ?>
                    <td width="100" align="right"><strong><?echo number_format($total_consumption_daily,2);?></strong></td>
                    <td width="80" align="right"><strong><?echo number_format($total_consumption_daily*30,2);?></strong></td>
                    <td width="80" align="right"><strong><?echo number_format((($total_consumption_daily*30)*.30),2);?></strong></td>
                    <td width="80" align="right"><strong><?echo number_format(($total_consumption_daily*30)+(($total_consumption_daily*30)*.30),2);?></strong></td>
                    <td width="80" align="right"><strong><?echo number_format((($total_consumption_daily*30)+(($total_consumption_daily*30)*.30))-$total_stock,2);?></strong></td>
                    <td width="80" align="right"><strong><?echo number_format($total_requisition,2);?></strong></td>
                </tr>
            <?
        }
        ?>
        <!-- <tfoot>
        <tr >
                <td colspan="6"  align="right"><strong>Grand TOTAL&nbsp;</strong></td>
                <?php
                    foreach($all_company as $val)
                    {
                        ?>
                            <td align="right"><?echo number_format($com_prodStockBalance[$val]['grand_total'],2);?></td>
                        <?
                    }
                ?>
                <td align="right"><?echo number_format($grand_total_stock,2);?></td>
                <?php
                    foreach($all_company as $val)
                    {
                        ?>
                            <td align="right"><?echo number_format($com_prodConssumptionLastMonth[$val]['grand_total'],2);?></td>
                        <?
                    }
                ?>
                <td align="right"><?echo number_format($grand_total_consumption,2);?></td>
                <?php
                    foreach($all_company as $val)
                    {
                        ?>
                            <td  align="right"><?echo number_format($com_prodConssumptionDaily[$val]['grand_total'],2);?></td>
                        <?
                    }
                ?>
                <td  align="right"><?echo number_format($grand_total_consumption_daily,2);?></td>
                <td align="right"><?echo number_format($grand_total_consumption_daily*30,2);?></td>
                <td align="right"><?echo number_format((($grand_total_consumption_daily*30)*.30),2);?></td>
                <td align="right"><?echo number_format(($grand_total_consumption_daily*30)+(($grand_total_consumption_daily*30)*.30),2);?></td>
                <td align="right"><?echo number_format((($grand_total_consumption_daily*30)+(($grand_total_consumption_daily*30)*.30))-$grand_total_stock,2);?></td>
                <td align="right"><?echo number_format($grand_total_requisition,2);?></td>
            </tr>
        </tfoot> -->
        </table>
        </div>
       
    </div>
    <?
	

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

if($action=="load_requisition_info")
{
    echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
    $companyArr = return_library_array("select id,company_name from lib_company","id","company_name");
    $itemgroupArr = return_library_array("select id,item_name from lib_item_group","id","item_name");
    $userArr = return_library_array("select id,user_name from user_passwd ","id","user_name");
    $storeArr = return_library_array("select id,store_name from lib_store_location","id","store_name");

    $product_sql=sql_select("SELECT a.company_id as COMPANY_ID, a.requ_no as REQU_NO,a.requisition_date as REQUISITION_DATE,a.store_name as STORE_NAME,a.inserted_by as INSERTED_BY,b.item_category as ITEM_CATEGORY,b.quantity as QUANTITY, b.cons_uom as CONS_UOM, c.item_group_id as ITEM_GROUP_ID, c.item_description as ITEM_DESCRIPTION  from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, product_details_master c  where a.id=b.mst_id and b.id in($dtls_id) and b.product_id=c.id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 ");
    ?>
        <table width="890" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all">
            <thead>
                <tr>
                    <th width="100">Company Name</th>
                    <th width="120">Req. No</th>
                    <th width="100">Req. Date</th>
                    <th width="100">Category</th>
                    <th width="150">Item Group</th>
                    <th width="100">Item Description</th>
                    <th width="60">UOM</th>
                    <th width="80">Req. Qty</th>
                    <th width="100">Store Name</th>
                    <th width="100">Insert BY</th>
                </tr>
            </thead>
            <tbody>
        <?
            foreach($product_sql as $row)
            {
                ?>
                    <tr>
                        <td><?echo $companyArr[$row['COMPANY_ID']];?></td>
                        <td><?echo $row['REQU_NO'];?></td>
                        <td><?echo $row['REQUISITION_DATE'];?></td>
                        <td><?echo $item_category[$row['ITEM_CATEGORY']];?></td>
                        <td><?echo $itemgroupArr[$row['ITEM_GROUP_ID']];?></td>
                        <td><?echo $row['ITEM_DESCRIPTION'];?></td>
                        <td><?echo $unit_of_measurement[$row['CONS_UOM']];?></td>
                        <td align="right"><?echo $row['QUANTITY'];$totalReqQnty+=$row['QUANTITY'];?></td>
                        <td><?echo $storeArr[$row['STORE_NAME']];?></td>
                        <td><?echo $userArr[$row['INSERTED_BY']];?></td>
                    </tr>
                <?
            }
        ?>
            </tbody>
            <tfoot>
                <tr class="general">
                    <td colspan="7" align="right"><strong>Grand Total</strong></td>
                    <td align="right"><?echo $totalReqQnty;?></td>
                    <td></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    <?
}