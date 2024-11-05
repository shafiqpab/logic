<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


if($action=="pinumber_popup")
{
  	echo load_html_head_contents("PI Number Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
    ?>
        <script>
            function js_set_value(str)
            {
            	var splitData = str.split("_");
            	$("#pi_id").val(splitData[0]);
            	$("#pi_no").val(splitData[1]);
            	parent.emailwindow.hide();
            }
        </script>
    </head>
    <body>
        <div align="center" style="width:100%; margin-top:5px" >
            <form name="searchlcfrm_1" id="searchlcfrm_1" autocomplete="off">
            	<table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                        <thead>
                            <tr>
                                <th>Supplier</th>
                                <th id="search_by_td_up">Enter PI Number</th>
                                <th>Enter PI Date</th>
                                <th>
                                	<input type="reset" id="res" value="Reset" style="width:100px" class="formbutton" onClick="reset_form('searchlcfrm_1','search_div','','','','');" />
                                    <input type="hidden" id="pi_id" value="" />
                                    <input type="hidden" id="pi_no" value="" />
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr align="center">
                                <td>
                                    <?
                                        $sql_supplier = "select DISTINCT(c.id),c.supplier_name from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$companyID' and b.party_type in(1,9) order by id,supplier_name";
                                        //echo $sql;
            							echo create_drop_down( "cbo_supplier_id", 150,"$sql_supplier",'id,supplier_name', 1, '-- All Supplier --',0,'',0);
                                    ?>
                                </td>
                                <td align="center" id="search_by_td">
                                    <input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
                                </td>
                                <td align="center" id="search_by_td">
                                    <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px;" placeholder="From Date" readonly />
                                    To
                                    <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px;" placeholder="To Date" readonly />
                                </td>
                                 <td align="center">
                                    <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_supplier_id').value+'_'+document.getElementById('txt_search_common').value+'_'+<? echo $companyID; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_pi_search_list_view', 'search_div', 'lc_wise_knit_finish_febric_recv_summery_report_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                                </td>
                       	 	</tr>
                            <tr>
                            	<td colspan="4" align="center"><? echo load_month_buttons(1); ?></td>
                            </tr>
                        </tbody>
                    </table>
                    <div align="center" style="margin-top:10px" id="search_div"> </div>
                </form>
           </div>
        </body>
        <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?

}

$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
$store_arr=return_library_array( "select id, store_name from lib_store_location", "id", "store_name"  );
$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );

if($action=="create_pi_search_list_view")
{
	$ex_data = explode("_",$data);

	if($ex_data[0]==0) $cbo_supplier = "%%"; else $cbo_supplier = $ex_data[0];
	$txt_search_common = trim($ex_data[1]);
	$company = $ex_data[2];
	$from_date = $ex_data[3];
	$to_date = $ex_data[4];
	if( $from_date!="" && $to_date!="")
	{
		if($db_type==0)
		{
			$pi_date_cond= " and pi_date between '".change_date_format($from_date,"yyyy-mm-dd")."' and '".change_date_format($to_date,"yyyy-mm-dd")."'";
		}
		else
		{
			$pi_date_cond= " and pi_date between '".change_date_format($from_date,'','',1)."' and '".change_date_format($to_date,'','',1)."'";
		}
	}
	else $pi_date_cond="";

	$sql= "select id, pi_number, supplier_id, importer_id, pi_date, last_shipment_date, total_amount from com_pi_master_details where importer_id=$company and entry_form=166 and supplier_id like '$cbo_supplier' and pi_number like '%".$txt_search_common."%' and is_deleted=0 and status_active=1 $pi_date_cond";
    //echo $sql;
	$arr=array(1=>$company_arr,2=>$supplier_arr);
	echo create_list_view("list_view", "PI No, Importer, Supplier Name, PI Date, Last Shipment Date, PI Value","130,110,130,90,130","780","260",0, $sql , "js_set_value", "id,pi_number", "", 1, "0,importer_id,supplier_id,0,0,0,0", $arr, "pi_number,importer_id,supplier_id,pi_date,last_shipment_date,total_amount", "",'','0,0,0,3,3,2') ;
	exit();
}

if($action=="btbLc_popup")
{
  	echo load_html_head_contents("BTB LC Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
?>

        <script>
        	function js_set_value(str)
        	{
        		var splitData = str.split("_");
        		$("#btbLc_id").val(splitData[0]);
        		$("#btbLc_no").val(splitData[1]);
        		parent.emailwindow.hide();
        	}
        </script>
    </head>
    <body>
        <div align="center" style="width:100%; margin-top:5px" >
            <form name="searchlcfrm_1" id="searchlcfrm_1" autocomplete="off">
        	<table width="600" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                    <thead>
                        <tr>
                            <th>Supplier</th>
                            <th id="search_by_td_up">Enter BTB LC Number</th>
                            <th>
                            	<input type="reset" id="res" value="Reset" style="width:100px" class="formbutton" onClick="reset_form('searchlcfrm_1','search_div','','','','');" />
                                <input type="hidden" id="btbLc_id" value="" />
                                <input type="hidden" id="btbLc_no" value="" />
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr align="center">
                            <td>
                                <?
                                    $sql_supplier = "select DISTINCT(c.id),c.supplier_name from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$companyID' and b.party_type in(1,9)";
        							echo create_drop_down( "cbo_supplier_id", 160,"$sql_supplier",'id,supplier_name', 1, '-- All Supplier --',0,'',0);
                                ?>
                            </td>
                            <td align="center" id="search_by_td">
                                <input type="text" style="width:230px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
                            </td>
                             <td align="center">
                                <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_supplier_id').value+'_'+document.getElementById('txt_search_common').value+'_'+<? echo $companyID; ?>, 'create_lc_search_list_view', 'search_div', 'lc_wise_knit_finish_febric_recv_summery_report_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                            </td>
                   	 	</tr>
                    </tbody>
                </table>
                <div align="center" style="margin-top:10px" id="search_div"> </div>
                </form>
           </div>
        </body>
        <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
<?

}


if($action=="create_lc_search_list_view")
{
	$ex_data = explode("_",$data);

	if($ex_data[0]==0) $cbo_supplier = "%%"; else $cbo_supplier = $ex_data[0];
	$txt_search_common = trim($ex_data[1]);
	$company = $ex_data[2];

	$sql= "select id, lc_number, supplier_id, importer_id, lc_date, last_shipment_date, lc_value from com_btb_lc_master_details where importer_id=$company and pi_entry_form=166 and supplier_id like '$cbo_supplier' and lc_number like '%".$txt_search_common."%' and is_deleted=0 and status_active=1";
    //echo $sql;
	$arr=array(1=>$company_arr,2=>$supplier_arr);
	echo create_list_view("list_view", "LC No, Importer, Supplier Name, LC Date, Last Shipment Date, LC Value","130,110,130,90,130","780","260",0, $sql , "js_set_value", "id,lc_number", "", 1, "0,importer_id,supplier_id,0,0,0,0", $arr, "lc_number,importer_id,supplier_id,lc_date,last_shipment_date,lc_value", "",'','0,0,0,3,3,2') ;
	exit();

}

if($action=="report_generate")
{
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process ));

    //if(str_replace("'","",$cbo_store_name)!="") $store_cond=" and b.store_id in(".str_replace("'","",$cbo_store_name).")";
    $pi_no_cond=str_replace("'","",$txt_pi_no);
    $btbLc_id_str=str_replace("'","",$btbLc_id);
    //echo $btbLc_id_str; //die;
    if(str_replace("'","",$cbo_store_name)==0) $store="%%"; else $store=str_replace("'","",$cbo_store_name);
    if(str_replace("'","",$txt_pi_no)=='') $pi_cond=""; else $pi_cond="and b.pi_number='$pi_no_cond'";
    if(str_replace("'","",$btbLc_id)=='') $btbLc_id_cond=""; else $btbLc_id_cond=" and a.com_btb_lc_master_details_id='$btbLc_id_str'";
    if(str_replace("'","",$btbLc_id)=='') $btbLc_id_cond_mst=""; else $btbLc_id_cond_mst=" and a.id='$btbLc_id_str'";
    if(str_replace("'","",$txt_pi_no)=='') $pi_cond_btb=""; else $pi_cond_btb="and c.pi_number='$pi_no_cond'";

    if($db_type==0)
    {
        $conversion_date=date("Y-m-d");
    }
    else
    {
        $conversion_date=date("d-M-y");
    }
    $exchange_rate=set_conversion_rate( 2, $conversion_date );
    ob_start();
    ?>
    <!--<fieldset style="width:1120px">-->
        <div style="width:100%; margin-left:10px;" align="left">
            <table width="1100" cellpadding="0" cellspacing="0" style="visibility:hidden; border:none" id="caption">
                <tr>
                   <td align="center" width="100%" colspan="11" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
                </tr>
            </table>
            <table width="1100" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                <?
                    $sql_btb_lc = "SELECT a.id,a.lc_number, a.supplier_id, a.importer_id, a.lc_date, a.last_shipment_date, a.lc_value,c.id as pi_id, c.pi_number,c.pi_basis_id , d.work_order_id
                    from com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_master_details c, com_pi_item_details d
                    where a.id=b.com_btb_lc_master_details_id and c.id=b.pi_id and c.id=d.pi_id and c.item_category_id = 2  and a.pi_entry_form=166 and c.importer_id=$cbo_company_name and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 $btbLc_id_cond_mst $pi_cond_btb";
                    //echo $sql_btb_lc;//die;
                    $btbLcResult=sql_select($sql_btb_lc);
                    if(count($btbLcResult)<1) {echo "No Data Found;";die;}
                    $btbLcData=array();$pi_id_all_array=array(); $pi_name_array=array();
                    foreach($btbLcResult as $row)
                    {
                        $btbLcData[$row[csf("id")]]["id"]=$row[csf("id")];
                        $btbLcData[$row[csf("id")]]["lc_number"]=$row[csf("lc_number")];
                        $btbLcData[$row[csf("id")]]["supplier_id"]=$row[csf("supplier_id")];
                        $btbLcData[$row[csf("id")]]["importer_id"]=$row[csf("importer_id")];
                        $btbLcData[$row[csf("id")]]["lc_date"]=$row[csf("lc_date")];
                        $btbLcData[$row[csf("id")]]["last_shipment_date"]=$row[csf("last_shipment_date")];
                        $btbLcData[$row[csf("id")]]["lc_value"]=$row[csf("lc_value")];
                        if(!in_array($row[csf('pi_id')],$pi_id_all_array))
                        {
                            $pi_id_all_array[$row[csf('pi_id')]]=$row[csf('pi_id')];
                            $pi_name_array[$row[csf('pi_id')]]=$row[csf('pi_number')];
                        }

                        if($row[csf('pi_basis_id')]==1)
                        {
                            $pi_with_work_order_array[$row[csf('work_order_id')]]= $row[csf('work_order_id')];
                        }
                    }
                    //print_r($pi_id_all_array);
                    unset($btbLcResult);
                ?>
                <thead>
                     <tr>
                        <th colspan="9">BTB LC Details</th>
                    </tr>
                    <tr>
                        <th width="40">SL</th>
                        <th width="180">Importer</th>
                        <th width="200">Supplier</th>
                        <th width="180">BTB LC No</th>
                        <th width="120">LC Date</th>
                        <th width="140">Last Shipment Date</th>
                        <th>LC Value</th>
                    </tr>
                </thead>
            </table>
            <div id="report1" style="width:1120px; overflow-y:scroll; max-height:430px;"  align="left">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1100" class="rpt_table" id="tbl_finish_feb_btb_lc_dtls" >
                    <?
                    $i=1;$rate=0; $compos=''; $tot_pi_qnty=0; $tot_pi_amnt=0;
                    foreach ($btbLcData as  $value) 
                    {
                        if($i%2==0){
                            $bgcolor = '#FFFFFF';
                        }else{
                            $bgcolor = '#E9F3FF';
                        }
                        ?>
                        <tr bgcolor="<? echo $bgcolor;?>" id="tr_<? echo $value[('importer_id')];?>" onClick="change_color('tr_<? echo $value[('importer_id')];?>','<? echo $bgcolor;?>')" >
                            <td width="40" align="center"><p><? echo $i; ?></p></td>
                            <td width="180"><p><? echo $company_arr[$value[('importer_id')]]; ?></p></td>
                            <td width="200"><p><? echo $supplier_arr[$value[('supplier_id')]]; ?></p></td>
                            <td width="180"><p><? echo $value[('lc_number')]; ?></p></td>
                            <td width="120" align="center"><? echo change_date_format($value[('lc_date')]); ?></td>
                            <td width="140" align="center"><? echo change_date_format($value[('last_shipment_date')]); ?></td>
                            <td align="right"><? echo number_format($value[('lc_value')],2); ?></td>
                        </tr>
                        <?
                        $i++;
                        $tot_lc_value +=$value[('lc_value')];
                    }
                    ?>
                <tfoot>
                    <tr>
                        <th colspan="6" align="right">Total</th>
                        <th align="right"> <?php echo number_format($tot_lc_value,2,'.','');?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
            <br>
            <table width="1320" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                <thead>
                    <tr>
                        <th colspan="13">PI Details</th>
                    </tr>
                    <tr>
                        <th width="40">SL</th>
                        <th width="140">Buyer</th>
                        <th width="120">Style No</th>
                        <th width="100">Booking No </th>
                        <th width="90">color</th>
                        <th width="90">PI Number</th>
                        <th width="100">PI Date</th>
                        <th width="80">Item Group</th>
                        <th width="150">Item Description</th>
                        <th width="90">Qnty</th>
                        <th width="90">Rate</th>
                        <th width="120">Value</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
            </table>
                <div id="report2" style="width:1340px; overflow-y:scroll; max-height:430px;"  align="left">
                    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1320" class="rpt_table" id="tbl_finish_feb_pi_dtls" >
                    <?
                    $i=1;

                   /* $sql_pi_details="select c.id as pi_dtls_id, b.id as pi_id,b.pi_number, b.pi_date, b.currency_id,b.remarks,b.item_category_id,b.supplier_id,c.color_id,c.item_group, c.work_order_dtls_id, c.fabric_construction,c.fabric_composition, c.quantity, c.rate, c.net_pi_amount, d.booking_no,f.style_ref_no,f.buyer_name
                    from com_pi_master_details b, com_pi_item_details c, wo_booking_mst d, wo_booking_dtls h, wo_po_break_down e, wo_po_details_master f, com_btb_lc_pi a
                    where  b.id = c.pi_id and b.id=a.pi_id and  b.entry_form = 166 and b.importer_id = $cbo_company_name and b.item_category_id = 3 and c.work_order_id = d.id and d.booking_no = h.booking_no and h.po_break_down_id = e.id and e.job_no_mst = f.job_no and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 $pi_cond $btbLc_id_cond ";*/
                    $sql_pi_details="SELECT c.id as pi_dtls_id, b.id,b.pi_number, b.pi_date, b.currency_id,b.remarks, b.item_category_id, b.supplier_id, c.color_id, c.item_group, c.work_order_dtls_id, c.fabric_construction,c.fabric_composition, c.quantity, c.rate, c.net_pi_amount, c.work_order_no as booking_no,c.work_order_id as booking_id
                    from com_pi_master_details b, com_pi_item_details c
                    where b.id = c.pi_id  and b.entry_form = 166 and b.importer_id = $cbo_company_name and b.item_category_id = 2 and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 and b.id in(".implode(",",$pi_id_all_array).")";
                    //echo $sql_pi_details;die;
                    $result=sql_select($sql_pi_details); $booking_pi=array();
                    foreach ($result as  $value) 
                    {
                        if($booking_check[$value[csf('booking_no')]]=="")
                        {
                            $booking_check[$value[csf('booking_no')]]=$value[csf('booking_no')];
                            $booking_pi[$value[csf('booking_id')]]=$value[csf('id')];
                            $all_bookings.="'".$value[csf('booking_no')]."',";
                        }
                    }
                    //print_r($booking_pi);
                    $all_bookings=chop($all_bookings,",");
                    if($all_bookings!="")
                    {
                        $job_book_sql="SELECT b.job_no, b.style_ref_no, b.buyer_name, a.booking_no, c.id as po_id, c.po_number
                        from wo_booking_dtls a, wo_po_details_master b, wo_po_break_down c
                        where a.job_no=b.job_no and b.job_no=c.job_no_mst and a.po_break_down_id=c.id and a.booking_no in($all_bookings)";
                        //echo $job_book_sql;die;
                        $job_book_result=sql_select($job_book_sql);
                        $job_book_data=$po_data=array();
                        foreach ($job_book_result as  $row) 
                        {
                            $job_book_data[$row[csf("booking_no")]]["job_no"]=$row[csf("job_no")];
                            $job_book_data[$row[csf("booking_no")]]["style_ref_no"]=$row[csf("style_ref_no")];
                            $job_book_data[$row[csf("booking_no")]]["buyer_name"]=$row[csf("buyer_name")];
                            $po_data[$row[csf("po_id")]]["po_number"]=$row[csf("po_number")];
                            $po_data[$row[csf("po_id")]]["job_no"]=$row[csf("job_no")];
                            $po_data[$row[csf("po_id")]]["style_ref_no"]=$row[csf("style_ref_no")];
                            $po_data[$row[csf("po_id")]]["buyer_name"]=$row[csf("buyer_name")];
                            
                        }
                    }
                    
                    $pi_dtls_data = array();$pi_data_check=array();
                    foreach ($result as  $value) {
                        $pi_dtls_data[$value[csf('id')]][$value[csf('pi_dtls_id')]]['pi_id']=$value[csf('id')];
                        $pi_dtls_data[$value[csf('id')]][$value[csf('pi_dtls_id')]]['pi_number']=$value[csf('pi_number')];
                        $pi_dtls_data[$value[csf('id')]][$value[csf('pi_dtls_id')]]['pi_date']=$value[csf('pi_date')];
                        $pi_dtls_data[$value[csf('id')]][$value[csf('pi_dtls_id')]]['currency_id']=$value[csf('currency_id')];
                        $pi_dtls_data[$value[csf('id')]][$value[csf('pi_dtls_id')]]['remarks']=$value[csf('remarks')];
                        $pi_dtls_data[$value[csf('id')]][$value[csf('pi_dtls_id')]]['item_category_id']=$value[csf('item_category_id')];
                        $pi_dtls_data[$value[csf('id')]][$value[csf('pi_dtls_id')]]['supplier_id']=$value[csf('supplier_id')];
                        $pi_dtls_data[$value[csf('id')]][$value[csf('pi_dtls_id')]]['color_id']=$value[csf('color_id')];
                        $pi_dtls_data[$value[csf('id')]][$value[csf('pi_dtls_id')]]['item_group']=$value[csf('item_group')];
                        $pi_dtls_data[$value[csf('id')]][$value[csf('pi_dtls_id')]]['work_order_dtls_id']=$value[csf('work_order_dtls_id')];
                        $pi_dtls_data[$value[csf('id')]][$value[csf('pi_dtls_id')]]['fabric_construction']=$value[csf('fabric_construction')];
                        $pi_dtls_data[$value[csf('id')]][$value[csf('pi_dtls_id')]]['fabric_composition']=$value[csf('fabric_composition')];
                        if($pi_data_check[$value[csf('pi_dtls_id')]]=="")
                        {
                            $pi_data_check[$value[csf('pi_dtls_id')]]=$value[csf('pi_dtls_id')];
                            $pi_dtls_data[$value[csf('id')]][$value[csf('pi_dtls_id')]]['quantity']+=$value[csf('quantity')];
                            $pi_dtls_data[$value[csf('id')]][$value[csf('pi_dtls_id')]]['net_pi_amount']+=$value[csf('net_pi_amount')];
                        }

                        $pi_dtls_data[$value[csf('id')]][$value[csf('pi_dtls_id')]]['booking_no']=$value[csf('booking_no')];
                        $pi_dtls_data[$value[csf('id')]][$value[csf('pi_dtls_id')]]['style_ref_no']=$job_book_data[$value[csf('booking_no')]]["style_ref_no"];
                        $pi_dtls_data[$value[csf('id')]][$value[csf('pi_dtls_id')]]['buyer_name']=$job_book_data[$value[csf('booking_no')]]["buyer_name"];
                    }
                    //var_dump($pi_dtls_data);
                    $supplier_pi_arr=array();
                    $p=1;
                    foreach($pi_dtls_data as $pi_ids=>$row_color_id)
                    {
                        foreach ($row_color_id as  $row)
                        {
                            if ($p%2==0)
                                $bgcolor="#E9F3FF";
                            else
                                $bgcolor="#FFFFFF";

                            if($row['currency_id']!=1) $ex_rate=1; else $ex_rate=$exchange_rate;

                            $rate=($row['net_pi_amount']/$row['quantity']);

                            $item_description=$row['fabric_composition']." ".$row['fabric_construction'];
                            $supplier_pi_arr[$pi_ids]['supplier'] = $row['supplier_id'];

                            //if(!in_array($row[csf('id')],$pi_id_all_array))
                            //{
                                //$pi_id_all_array[$row[csf('id')]]=$row[csf('id')];
                                //$pi_name_array[$row[csf('id')]]=$row[csf('pi_number')];
                            //}
                            //var_dump($supplier_pi_arr);

                            ?>
                            <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trr_<? echo $p;?>','<? echo $bgcolor;?>')" id="trr_<? echo $p;?>">
                                <td width="40"><? echo $p; ?></td>
                                <td width="140"><p><? echo $buyer_arr[$row['buyer_name']]; ?></p></td>
                                <td width="120"><p><? echo $row['style_ref_no']; ?></p></td>
                                <td width="100"><p><? echo $row['booking_no']; ?></p></td>
                                <td width="90"><p><? echo $color_arr[$row['color_id']]; ?></p></td>
                                <td width="90"><p><? echo $row['pi_number']; ?></p></td>
                                <td width="100" align="center"><? echo change_date_format($row['pi_date']); ?></td>
                                <td width="80" align="center"><p>&nbsp;<? echo $row['item_group']; ?></p></td>
                                <td width="150"><p><? echo $item_description; ?></p></td>
                                <td width="90" align="right"><? echo number_format($row['quantity'],2); ?></td>
                                <td width="90" align="right"><? echo number_format($rate,4,'.',''); ?></td>
                                <td width="100" align="right"><? echo number_format($row['net_pi_amount'],2,'.',''); ?></td>
                                <td><p><? echo $row['remarks']; ?>&nbsp;</p></td>
                            </tr>
                            <?
                            $tot_pi_qnty+=$row['quantity'];
                            $tot_pi_amnt+=$row['net_pi_amount'];
                            $p++;
                        }

                    }
                    //print_r($supplier_pi_arr);
                ?>
                <tfoot>
                    <th colspan="9" align="right">Total</th>
                    <th align="right"> <?php echo number_format($tot_pi_qnty,2,'.','');?></th>
                    <th>&nbsp;</th>
                    <th align="right"> <?php echo number_format($tot_pi_amnt,2,'.','');?></th>
                    <th>&nbsp;</th>
                </tfoot>
            </table>
        </div>
            <br>
            <table width="1600" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                <thead>
                    <tr>
                      <th colspan="16">Knit Finish Febric Receive</th>
                    </tr>
                    <tr>
                        <th width="40">SL</th>
                        <th width="140">Buyer</th>
                        <th width="120">Style No</th>
                        <th width="100">Order No </th>
                        <th width="90">color</th>
                        <th width="140">Supplier Name</th>
                        <th width="90">PI Number</th>
                        <th width="100">MRR No</th>
                        <th width="90">Recv. Date</th>
                        <th width="80">Challan No</th>
                        <th width="120">Item Description</th>
                        <th width="70">Batch No</th>
                        <th width="80">Receive Qnty</th>
                        <th width="70">Rate</th>
                        <th width="100">Value</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
            </table>
                <div id="report5" style="width:1620px; overflow-y:scroll; max-height:300px;"  align="left">
                    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1600" class="rpt_table" id="tbl_finish_feb_rcv" >
                    <?
                    $pi_id_all=implode(",",$pi_id_all_array);
                    $pi_workd_order_id_all=implode(",",$pi_with_work_order_array);
                    //var_dump($pi_id_all);
                    $compos=''; $tot_recv_qnty=0; $tot_recv_amnt=0; $recv_id_array=array(); $recv_pi_array=array(); $pi_data_array=array();
                    //echo $pi_id_all.Jahhid;die;
                    if ($pi_id_all!='' || $pi_id_all!=0)
                    {
                        $batch_arr=return_library_array("select id, batch_no from pro_batch_create_mst where status_active=1 and company_id=$cbo_company_name","id","batch_no");

                        $sql_recv="SELECT b.id as trans_id, a.id as rec_id, a.recv_number, a.recv_number_prefix_num, a.receive_date, a.challan_no, a.booking_id as pi_id, a.booking_no as pi_number, b.remarks, b.prod_id, b.batch_id as batch_id, c.color_id, f.construction, f.copmposition, c.quantity, b.order_rate, b.order_amount, c.po_breakdown_id, c.id as propo_id
                        from inv_receive_master a, inv_transaction b, order_wise_pro_details c, wo_booking_dtls f
                        where a.id = b.mst_id and b.id=c.trans_id and c.po_breakdown_id=f.po_break_down_id and a.item_category = 2 and a.entry_form=37 and a.company_id = $cbo_company_name and a.receive_basis = 1 and b.item_category = 2 and b.transaction_type = 1 and a.booking_id in ($pi_id_all) and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0";
                    }

                    if($pi_workd_order_id_all!="")
                    {
                        if($sql_recv!="") $sql_recv.=" union all ";                      
                        $sql_recv.=" SELECT b.id as trans_id, a.id as rec_id, a.recv_number, a.recv_number_prefix_num, a.receive_date, a.challan_no, a.booking_id as pi_id, a.booking_no as pi_number, b.remarks, b.prod_id, b.batch_id as batch_id, c.color_id, f.construction, f.copmposition, c.quantity, b.order_rate, b.order_amount, c.po_breakdown_id, c.id as propo_id
                        from inv_receive_master a, inv_transaction b, order_wise_pro_details c, wo_booking_dtls f
                        where a.id = b.mst_id and b.id=c.trans_id and c.po_breakdown_id=f.po_break_down_id and a.item_category = 2 and a.entry_form=37 and a.company_id = $cbo_company_name and a.receive_basis = 2 and b.item_category = 2 and b.transaction_type = 1 and a.booking_id in ($pi_workd_order_id_all) and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0";

                    }
                     //echo $sql_recv;//die;
                    $pi_rec_data_arr= array();
                    $dataArray=sql_select($sql_recv);
                    foreach ($dataArray as $k => $value) {
                        if($rcv_check[$value[csf('rec_id')]][$value[csf('color_id')]]=="")
                        {
                            $rcv_check[$value[csf('rec_id')]][$value[csf('color_id')]]=$value[csf('rec_id')];
                            $pi_rec_data_arr[$value[csf('pi_id')]][$value[csf('color_id')]]['rec_id'].=$value[csf('rec_id')].",";
                            $pi_rec_data_arr[$value[csf('pi_id')]][$value[csf('color_id')]]['recv_number'].=$value[csf('recv_number_prefix_num')].",";
                            $pi_rec_data_arr[$value[csf('pi_id')]][$value[csf('color_id')]]['challan_no'].=$value[csf('challan_no')].",";
                        }
                        if($batch_check[$value[csf('batch_id')]][$value[csf('color_id')]]=="")
                        {
                            $batch_check[$value[csf('batch_id')]][$value[csf('color_id')]]=$value[csf('batch_id')];
                            $pi_rec_data_arr[$value[csf('pi_id')]][$value[csf('color_id')]]['batch_id'].=$value[csf('batch_id')].",";
                            $pi_rec_data_arr[$value[csf('pi_id')]][$value[csf('color_id')]]['batch_no'].=$batch_arr[$value[csf('batch_id')]].",";
                        }
                        
                        $pi_rec_data_arr[$value[csf('pi_id')]][$value[csf('color_id')]]['receive_date']=$value[csf('receive_date')];
                        $pi_rec_data_arr[$value[csf('pi_id')]][$value[csf('color_id')]]['pi_id']=$value[csf('pi_id')];
                        $pi_rec_data_arr[$value[csf('pi_id')]][$value[csf('color_id')]]['pi_number']=$value[csf('pi_number')];
                        $pi_rec_data_arr[$value[csf('pi_id')]][$value[csf('color_id')]]['color_id']=$value[csf('color_id')];
                        
                        
                        $pi_rec_data_arr[$value[csf('pi_id')]][$value[csf('color_id')]]['po_number']=$po_data[$value[csf("po_breakdown_id")]]["po_number"];
                        $pi_rec_data_arr[$value[csf('pi_id')]][$value[csf('color_id')]]['job_no']=$po_data[$value[csf("po_breakdown_id")]]["job_no"];
                        $pi_rec_data_arr[$value[csf('pi_id')]][$value[csf('color_id')]]['buyer_name']=$po_data[$value[csf("po_breakdown_id")]]["buyer_name"];
                        $pi_rec_data_arr[$value[csf('pi_id')]][$value[csf('color_id')]]['style_ref_no']=$po_data[$value[csf("po_breakdown_id")]]["style_ref_no"];
                        $pi_rec_data_arr[$value[csf('pi_id')]][$value[csf('color_id')]]['construction']=$value[csf('construction')];
                        $pi_rec_data_arr[$value[csf('pi_id')]][$value[csf('color_id')]]['copmposition']=$value[csf('copmposition')];
                        //echo "sumon";
                        if($propotion_check[$value[csf('propo_id')]]=="")
                        {
                            $propotion_check[$value[csf('propo_id')]]=$value[csf('propo_id')];
                            $pi_rec_data_arr[$value[csf('pi_id')]][$value[csf('color_id')]]['quantity']+=$value[csf('quantity')];
                            $pi_rec_data_arr[$value[csf('pi_id')]][$value[csf('color_id')]]['order_amount']+=$value[csf('quantity')]*$value[csf('order_rate')];
                        }
                        $pi_rec_data_arr[$value[csf('pi_id')]][$value[csf('color_id')]]['order_rate'][$value[csf('trans_id')]][$k]=$value[csf('order_rate')];
                        $pi_rec_data_arr[$value[csf('pi_id')]][$value[csf('color_id')]]['remarks']=$value[csf('remarks')];

                        $pi_rcv_rtn_rate_arr[$value[csf('rec_id')]][$value[csf('prod_id')]]['rate']=$value[csf('order_rate')];
                    }

                    $i=1;

                    foreach($pi_rec_data_arr as $pi_id=> $row_recv)
                    {
                        foreach ($row_recv as  $color_id=>$value) {

                            if ($i%2==0)
                                $bgcolor="#E9F3FF";
                            else
                                $bgcolor="#FFFFFF";

                            $item_description=$value['construction']." ".$value['copmposition'];

                            //echo "<pre>";print_r($value);
                            if(!in_array(chop($value['rec_id'],','),$recv_id_array))
                            {

                                $recv_id_array[chop($value['rec_id'],',')]=chop($value['rec_id'],',');
                                $rec_ids=array_unique(explode(",",$value['rec_id']));
                                foreach ($rec_ids as $key => $val) {
                                    $recv_pi_array[$val]=$value['pi_id'];//$booking_pi[$value['pi_id']];
                                }
                                //$recv_pi_array[chop($value['rec_id'],',')]=$booking_pi[$value['pi_id']] ;
                            }
                            $rate = array_sum(array_unique($value['order_rate']))/count(array_unique($value['order_rate']));
                            $rate = number_format($rate,4,'.','');
                            $amount= $value['order_amount'];
                            $pi_data_array[$pi_id]['rcv_value']+= $amount;
                            //$pi_data_array[$booking_pi[$value['pi_id']]]['rcv_value']+=$value['quantity']*$rate;
                            ?>
                            <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trp_<? echo $i;?>','<? echo $bgcolor;?>')" id="trp_<? echo $i;?>">
                                <td width="40" align="center"><? echo $i; ?></td>
                                <td width="140" align="center"><? echo $buyer_arr[$value['buyer_name']]; ?></td>
                                <td width="120" align="center"><? echo $value['style_ref_no']; ?></td>
                                <td width="100" align="center"><? echo $value['po_number']; ?></td>
                                <td width="90" align="center"><? echo $color_arr[$value['color_id']]; ?></td>
                                <td width="140" align="center" title="<?= $supplier_pi_arr[$value['pi_id']]['supplier']; ?>"><? echo $supplier_arr[$supplier_pi_arr[$value['pi_id']]['supplier']]; ?></td>
                                <td width="90" align="center"><? echo $value['pi_number']; ?></td>
                                <td width="100"><p><? echo chop($value['recv_number'],','); ?></p></td>
                                <td width="90" align="center"><? echo change_date_format($value['receive_date']); ?></td>
                                <td width="80"><p>&nbsp;<? echo chop($value['challan_no'],','); ?></p></td>
                                <td width="120"><p>&nbsp;<? echo $item_description; ?></p></td>
                                <td width="70" align="center" title="<?= chop($value['batch_id'],','); ?>"><p><? echo chop($value['batch_no'],','); ?></p></td>
                                <td width="80" align="right"><p><? echo $value['quantity']; ?></p></td>
                                <td width="70" align="right"><? echo $rate; ?></td>
                                <td width="100" align="right"><? echo number_format($amount,2,'.',''); ?></td>
                                <td><p>&nbsp;<? echo $value['remarks']; ?></p></td>
                            </tr>
                            <?
                            $tot_recv_qnty+=$value['quantity'];
                            $tot_recv_amnt+=$amount;
                            $i++;
                        }
                    }
                ?>
                <tfoot>
                    <th colspan="12" align="right">Total</th>
                    <th align="right"><?php echo number_format($tot_recv_qnty,2,'.','');?></th>
                    <th>&nbsp;</th>
                    <th align="right"><?php echo number_format($tot_recv_amnt,2,'.','');?></th>
                    <th>&nbsp;</th>
                </tfoot>
            </table>
        </div>
            <br>
             <table width="1100" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                <thead>
                    <tr>
                      <th colspan="8">Knit Finish Febric Receive Return</th>
                    </tr>
                    <tr>
                        <th width="40">SL</th>
                        <th width="100">Return Date</th>
                        <th width="130">Return No</th>
                        <th width="290">Item Description</th>
                        <th width="110">Qnty</th>
                        <th width="100">Rate</th>
                        <th width="120">Value</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
            </table>
                <div id="report3" style="width:1120px; overflow-y:scroll; max-height:430px;"  align="left">
                    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1100" class="rpt_table" id="tbl_finish_feb_rcv_rtrn" >
                 <?
                    $tot_retn_qnty=0; $tot_retn_amnt=0;

                    if(count($recv_id_array)>0)
                    {
                        //print_r($recv_id_array);
                        //$recv_id_all=chop(implode(",",$recv_id_array),',');
                        $recv_id_all=implode(",",array_unique($recv_id_array));

                        $sql_retn="SELECT a.id, a.received_id, a.issue_number, a.issue_date, a.challan_no,b.remarks,b.prod_id,b.order_rate as rate, b.rcv_rate, b.cons_quantity, b.cons_amount, c.product_name_details,d.receive_date, d.booking_id as pi_id
                        from inv_issue_master a, inv_transaction b, product_details_master c,inv_receive_master d
                        where a.id=b.mst_id and a.received_id=d.id and b.prod_id=c.id and a.item_category=2 and a.entry_form=46 and a.company_id=$cbo_company_name and a.received_id in($recv_id_all) and  b.item_category=2 and b.transaction_type=3 and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ";
                        //echo $sql_retn;
                        $dataRtArray=sql_select($sql_retn);

                        $i=1; $rate =$cons_amount='';
                        // print_r($pi_rcv_rtn_rate_arr);
                        foreach($dataRtArray as $row_retn)
                        {
                            $all_rtn_id[$row_retn[csf("id")]]=$row_retn[csf("id")];
                            if ($i%2==0)
                                $bgcolor="#E9F3FF";
                            else
                                $bgcolor="#FFFFFF";

                            if($db_type==0)
                            {
                                $conversion_date=date("Y-m-d",strtotime($row_retn[csf("receive_date")]));
                            }
                            else
                            {
                                $conversion_date=date("d-M-y",strtotime($row_retn[csf("receive_date")]));
                            }

                            if(is_nan($row_retn[csf('amount')]/$row_retn[csf('recv_qnty')])){
                                $rate= 0;
                            }else{
                                $rate = $row_retn[csf('amount')]/$row_retn[csf('recv_qnty')];
                            }
                            if($rate=='' || $rate==0)
                            {
                                $rate=$pi_rcv_rtn_rate_arr[$row_retn[csf('received_id')]][$row_retn[csf('prod_id')]]['rate'];
                            }
                            
                            $cons_amount = $row_retn[csf('cons_quantity')]*$rate; 

                            //$exchange_rate=set_conversion_rate( 2, $conversion_date );
                            //$rate=$row_retn[csf('rcv_rate')]/$exchange_rate;
                            //$amnt=$row_retn[csf('cons_quantity')]*$rate;

                            ?>
                            <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trrc_<? echo $i;?>','<? echo $bgcolor;?>')" id="trrc_<? echo $i;?>">
                                <td width="40" align="center"><? echo $i; ?></td>
                                <td width="100" align="center"><? echo change_date_format($row_retn[csf('issue_date')]); ?></td>
                                <td width="130"><p><? echo $row_retn[csf('issue_number')]; ?></p></td>
                                <td width="290"><p>&nbsp;<? echo $row_retn[csf('product_name_details')]; ?></p></td>
                                <td width="110" align="right"><? echo number_format($row_retn[csf('cons_quantity')],2,'.',''); ?></td>
                                <td width="100" align="right" title="<? echo 'Rate '.$row_retn[csf('rcv_rate')];?>"><? echo number_format($rate,2,'.',''); ?></td>
                                <td width="120" align="right"><? echo number_format($cons_amount,2,'.',''); ?></td>
                                <td><p>&nbsp;<? echo $row_retn[csf('remarks')]; ?></p></td>
                            </tr>
                            <?
                            //echo $row_retn[csf('rcv_rate')];
                            $pi_id=$recv_pi_array[$row_retn[csf('received_id')]];
                            $tot_retn_qnty+=$row_retn[csf('cons_quantity')];
                            $tot_retn_amnt+=$cons_amount;
                            //echo $pi_id;
                            $pi_data_array[$pi_id]['rtn']+=$row_retn[csf('cons_quantity')]*$rate;

                            $i++;
                        }
                        //var_dump($pi_data_array);
                    }

                    $rtn_cond="";
                    if(count($all_rtn_id)>0)
                    {
                        $all_rtn_id_arr=array_chunk($all_rtn_id,999);

                        $rtn_cond.=" and (";
                        foreach($all_rtn_id_arr as $rtn_id)
                        {
                            if($rtn_cond==" and (") $rtn_cond.=" a.id not in(".implode(",",$rtn_id).") "; else $rtn_cond.=" and a.id not in(".implode(",",$rtn_id).") ";
                        }
                        $rtn_cond.=")";
                    }


                    // show double return in group
                    if ($pi_id_all!='' || $pi_id_all!=0)
                    {
                        $sql_retn="SELECT a.received_id, a.pi_id, a.issue_number, a.issue_date, a.challan_no,a.remarks, (b.order_rate+b.order_ile_cost) as ord_rate, b.cons_rate as rate, b.rcv_rate, b.cons_quantity, b.cons_amount, c.product_name_details
                        from inv_issue_master a, inv_transaction b, product_details_master c
                        where a.item_category=1 and a.entry_form=8 and a.company_id=$cbo_company_name and a.pi_id in($pi_id_all) and a.id=b.mst_id and b.item_category=1 and b.transaction_type=3 and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $rtn_cond";
                        
                        $dataRtArray=sql_select($sql_retn);
                        foreach($dataRtArray as $row_retn)
                        {//echo '+++++++++++++++';
                            if ($i%2==0)
                                $bgcolor="#E9F3FF";
                            else
                                $bgcolor="#FFFFFF";

                            //$rate=$row_retn[csf('rate')]/$exchange_rate;
                            $rate=$row_retn[csf('rcv_rate')]/$exchange_rate;
                            $amnt=$row_retn[csf('cons_quantity')]*$rate;
                            ?>
                            <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                                <td width="100" align="center"><? echo change_date_format($row_retn[csf('issue_date')]); ?></td>
                                <td width="130"><p><? echo $row_retn[csf('issue_number')]; ?></p></td>
                                <td width="290"><p>&nbsp;<? echo $row_retn[csf('product_name_details')]; ?></p></td>
                                <td width="110" align="right"><? echo number_format($row_retn[csf('cons_quantity')],2,'.',''); ?></td>
                                <td width="100" align="right" title="<? echo 'Rate '.$row_retn[csf('rcv_rate')].' exchange_rate= '.$exchange_rate;?>"><? echo number_format($rate,2,'.',''); ?></td>
                                <td width="120" align="right"><? echo number_format($amnt,2,'.',''); ?></td>
                                <td><p>&nbsp;<? echo $row_retn[csf('remarks')]; ?></p></td>
                            </tr>
                        <?
                            $pi_id=$row_retn[csf('pi_id')];
                            //echo $row_retn[csf('pi_id')].'==';
                            $tot_retn_qnty+=$row_retn[csf('cons_quantity')];
                            $tot_retn_amnt+=$amnt;
                            $pi_data_array[$pi_id]['rtn']+=$amnt;

                            $i++;
                        }
                    }

                    $total_balance_qty = ($tot_pi_qnty+$tot_retn_qnty-$tot_recv_qnty);
                    $total_balance_value = ($tot_pi_amnt+$tot_retn_amnt-$tot_recv_amnt);
                ?>
                <tfoot>
                    <tr>
                        <th colspan="4" align="right">Total</th>
                        <th align="right"><?php echo number_format($tot_retn_qnty,2,'.','');?></th>
                        <th>&nbsp;</th>
                        <th align="right"><?php echo number_format($tot_retn_amnt,2,'.','');?></th>
                        <th>&nbsp;</th>
                    </tr>
                    <tr>
                        <th colspan="4" align="right">Balance</th>
                        <th align="right"><?php echo number_format($total_balance_qty,2,'.','');?></th>
                        <th>&nbsp;</th>
                        <th align="right"><?php echo number_format($total_balance_value,2,'.','');?></th>
                        <th>&nbsp;</th>
                    </tr>
                </tfoot>
            </table>
        </div>
            <br>
            <table width="850" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="acceptance_tble">
                <thead>
                    <tr>
                      <th colspan="7">Acceptance Details </th>
                    </tr>
                    <tr>
                        <th width="140">PI Number</th>
                        <th width="100">Receive Value</th>
                        <th width="100">Return Value</th>
                        <th width="100">Payable Value</th>
                        <th width="100">Acceptance Date</th>
                        <th width="100">Acceptance Given</th>
                        <th>Yet To Accept</th>
                    </tr>
                </thead>
            </table>
            <div id="report4" style="width:870px; overflow-y:scroll; max-height:430px;"  align="left">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="850" class="rpt_table" id="tbl_finish_feb_acceptance" >
                <?
                //var_dump($pi_id_all);
                    if ($pi_id_all!='' || $pi_id_all!=0)
                    {
                        /*$acceptance_arr=return_library_array( "select pi_id, sum(current_acceptance_value) as acceptance_value from com_import_invoice_dtls where pi_id in($pi_id_all) and is_lc=1 and status_active=1 and is_deleted=0 group by pi_id", "pi_id", "acceptance_value"  );
                        echo "select a.invoice_date, b.pi_id, sum(b.current_acceptance_value) as acceptance_value
                        from com_import_invoice_mst a, com_import_invoice_dtls b
                        where a.id=b.import_invoice_id and  b.pi_id in($pi_id_all) and b.is_lc=1 and b.status_active=1 and b.is_deleted=0 group by b.pi_id, a.invoice_date order by b.pi_id, a.invoice_date";

                        $accep_sql=sql_select("select a.company_acc_date, b.pi_id, sum(b.current_acceptance_value) as acceptance_value
                        from com_import_invoice_mst a, com_import_invoice_dtls b
                        where a.id=b.import_invoice_id and  b.pi_id in($pi_id_all) and b.is_lc=1 and b.status_active=1 and b.is_deleted=0 group by b.pi_id, a.company_acc_date order by b.pi_id, a.company_acc_date");*/
                        $acceptance_sql = "SELECT   m.id as pi_id, a.company_acc_date, b.current_acceptance_value as acceptance_value
                        from com_pi_master_details m
                        left  join com_import_invoice_dtls b on m.id=b.pi_id   and b.is_lc=1 and b.status_active=1 and b.is_deleted=0
                        left  join com_import_invoice_mst a on a.id=b.import_invoice_id
                        where  m.id in($pi_id_all)
                        order by m.id";
                        //echo $acceptance_sql;

                        $accep_result=sql_select($acceptance_sql);

                        //echo $count_row[1261].jahid;die;
                        $payble_value=0; $tot_payble_value=0; $yet_to_accept=0; $tot_accept_value=0; $tot_yet_to_accept=0; $total_receive_value=0; $total_return_value=0;

                        foreach($accep_result as $row)
                        {
                            $accep_breakdown_data[$row[csf("pi_id")]]++;
                            $acceptance_arr[$row[csf("pi_id")]]+=$row[csf("acceptance_value")];


                        if ($i%2==0)
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";
                        ?>
                        <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                            <?

                            //
                            if($temp_pi[$row[csf("pi_id")]]=="")
                            {
                                //print_r($pi_data_array);
                               // echo $row[csf("pi_id")];
                                $payble_value=$yet_to_accept=0;
                                $payble_value=$pi_data_array[$row[csf("pi_id")]]['rcv_value']-$pi_data_array[$row[csf("pi_id")]]['rtn'];
                                $yet_to_accept=$payble_value-$acceptance_arr[$row[csf("pi_id")]];

                                $total_receive_value+=$pi_data_array[$row[csf("pi_id")]]['rcv_value'];
                                $total_return_value+=$pi_data_array[$row[csf("pi_id")]]['rtn'];

                                $tot_payble_value+=$payble_value;
                                $tot_accept_value+=$acceptance_arr[$row[csf("pi_id")]];
                                $tot_yet_to_accept+=$yet_to_accept;
                                ?>
                                <td width="140" rowspan="<? echo $accep_breakdown_data[$row[csf("pi_id")]];?>" align="center"><p><? echo $pi_name_array[$row[csf("pi_id")]]; ?></p></td>
                                <td width="100"  align="right" rowspan="<? echo $accep_breakdown_data[$row[csf("pi_id")]];?>"><? echo number_format($pi_data_array[$row[csf("pi_id")]]['rcv_value'],2,'.',''); ?></td>
                                <td width="100" align="right" rowspan="<? echo $accep_breakdown_data[$row[csf("pi_id")]];?>"><? echo number_format($pi_data_array[$row[csf("pi_id")]]['rtn'],2,'.',''); ?></td>
                                <td width="100" align="right" rowspan="<? echo $accep_breakdown_data[$row[csf("pi_id")]];?>"><? echo number_format($payble_value,2,'.',''); ?></td>
                                <?
                            }
                            ?>
                            <td width="100" align="center"><? if($row[csf("company_acc_date")]!="" && $row[csf("company_acc_date")]!="0000-00-00") echo change_date_format($row[csf("company_acc_date")]);?>&nbsp;</td>
                            <td width="100" align="right"><? echo number_format($row[csf("acceptance_value")],2,'.',''); ?></td>
                            <?
                            if($temp_pi[$row[csf("pi_id")]]=="")
                            {
                                $temp_pi[$row[csf("pi_id")]]=$row[csf("pi_id")];
                                ?>
                                <td align="right" rowspan="<? echo $accep_breakdown_data[$row[csf("pi_id")]];?>"><? echo number_format($yet_to_accept,2,'.',''); ?></td>
                                <?
                            }
                            ?>
                        </tr>
                        <?
                        $i++;
                    }
                }
                ?>
                <tfoot>
                    <th align="right">Total</th>
                    <th align="right"><?php echo number_format($total_receive_value,2,'.',''); ?></th>
                    <th align="right"><?php echo number_format($total_return_value,2,'.',''); ?></th>
                    <th align="right"><?php echo number_format($tot_payble_value,2,'.',''); ?></th>
                    <th align="right">&nbsp;</th>
                     <th align="right"><?php echo number_format($tot_accept_value,2,'.',''); ?></th>
                    <th align="right"><?php echo number_format($tot_yet_to_accept,2,'.',''); ?></th>
                </tfoot>
            </table>
        </div>
             <?
                echo signature_table(3, str_replace("'","",$cbo_company_name), "1100px");
             ?>
        </div>
    <!--</fieldset>-->
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
    echo "$total_data####$filename";
    exit();
}
?>
