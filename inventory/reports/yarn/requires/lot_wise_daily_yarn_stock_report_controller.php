<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');



$user_id = $_SESSION['logic_erp']["user_id"];
if ($_SESSION['logic_erp']['user_id'] == "")
{
	header("location:login.php");
	die;
}
$permission = $_SESSION['page_permission'];
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

//--------------------------------------------------------------------------------------------

if ($action == "generate_report")
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	// echo $txt_excange_rate;die;

	$companyArr = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$companyArr[0] = "All Company";
	$supplierArr = return_library_array("select id, short_name from lib_supplier", "id", "short_name");
	$brandArr = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
	$yarn_count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$color_name_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$buyer_dtls = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
	$buy_short_name_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');

    $curent_date = date("d-m-Y");
    
   
	$txt_lot_no = trim($txt_lot_no);
	$txt_composition = trim($txt_composition);

    if ($cbo_company_name == 0)
    {
        $company_cond = "";
    }
    else
    {
        $company_cond = " and a.company_id=$cbo_company_name";
    }


	if ($db_type == 0)
	{
		$from_date = change_date_format($from_date, 'yyyy-mm-dd');
		$to_date = change_date_format($to_date, 'yyyy-mm-dd');
		$curent_date = change_date_format($curent_date, 'yyyy-mm-dd');
	}
	else if ($db_type == 2)
	{   
		$from_date = change_date_format($from_date, '', '', 1);
		$to_date = change_date_format($to_date, '', '', 1);
		$curent_date = change_date_format($curent_date, '', '', 1);
	}
	else
	{
		$from_date = "";
		$to_date = "";
		$curent_date = "";
	}

    if ($to_date != "")
		$date_cond = " and b.transaction_date<='$to_date'";
    if ($curent_date != "")
		$curent_date_cond = " and a.transaction_date = '$curent_date'";

       
    $search_cond = "";

    if ($txt_count != "")
    {
        $search_cond .= " and a.yarn_count_id in($txt_count)";
    }


    if ($txt_lot_no != "")
    {
        if($lot_search_type == 1)
        {
            if($db_type == 2)
            {
                $search_cond .= " and regexp_like (a.lot, '^".trim($txt_lot_no)."')";
            }
            else
            {
                $search_cond .= " and a.lot like '".trim($txt_lot_no)."%'";
            }

        }
        else
        {
            $search_cond .= " and a.lot='" . trim($txt_lot_no) . "'";
        }
    }

    if ($txt_composition != "")
    {
        $search_cond .= " and a.yarn_comp_type1st in (" .$txt_composition_id .")";
    }

    if ($txt_yarn_brand != "")
    {
        $search_cond .= " and a.brand in (" .$txt_yarn_brand_id .")";
    }

    if ($type == 1)
    {
        $con = connect();
        $r_id1=execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1) and ENTRY_FORM = 10");
        if($r_id1)
        {
            oci_commit($con);
        }

       
        $sql = "SELECT a.id, a.company_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.lot, a.brand,
        SUM(case when transaction_type in (1,4,5) then cons_quantity else 0 end) as total_recieved_quantity,
        SUM(case when transaction_type in (2,3,6) then cons_quantity else 0 end) as total_issue_quantity,
        MIN(case when transaction_type in (1) then transaction_date else null end) as all_rcv_mrr_min_date
        from product_details_master a, inv_transaction b
        where a.id=b.prod_id and a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and transaction_type in(1,2,3,4,5,6)  $company_cond $search_cond $date_cond
        group by a.id, a.company_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.lot, a.allocated_qnty, a.brand order by a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.id";
        //echo $sql;die;
       
        $result = sql_select($sql);
    

        $prod_ids_arr = array();
        $prodIdChk = array();
        $allProdId = array();
        foreach($result as $row)
        {
            if($prodIdChk[$row[csf('id')]] == "")
            {
                $prodIdChk[$row[csf('id')]] = $row[csf('id')];
                $allProdId[$row[csf("id")]] = $row[csf("id")];
            }
            //array_push($prod_ids_arr, $row[csf("id")]);
        }
        $allProdId = array_filter($allProdId);

        if(!empty($allProdId))
        {
            fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 10, 1,$allProdId, $empty_arr);
            //die;
                
            $mrr_info_sql = "SELECT a.prod_id,a.transaction_date,
            SUM(case when a.transaction_type in (1,4,5) then a.cons_quantity else 0 end) as mrr_total_recieved_quantity,
            SUM(case when a.transaction_type in (2,3,6) then a.cons_quantity else 0 end) as mrr_total_issue_quantity
            FROM inv_transaction a, GBL_TEMP_ENGINE b
            WHERE a.status_active = 1
            AND a.is_deleted = 0
            AND a.item_category = 1
            AND a.transaction_type IN (1,2,3,4,5,6) $company_cond $curent_date_cond  AND a.prod_id=b.ref_val AND b.user_id=$user_id AND b.ref_from in (1) AND b.entry_form=10
            GROUP BY a.prod_id,a.transaction_date";
            //echo $mrr_info_sql;die;
            $result_mrr_info = sql_select($mrr_info_sql);
            $mrr_info_arr = array();
            foreach ($result_mrr_info as $row)
            {
                $mrr_info_arr[$row[csf("prod_id")]]['mrr_total_recieved_quantity']=$row[csf("mrr_total_recieved_quantity")];
                $mrr_info_arr[$row[csf("prod_id")]]['mrr_total_issue_quantity']=$row[csf("mrr_total_issue_quantity")];
            }

            unset($result_mrr_info);
            //echo "<pre>";print_r($mrr_info_arr);
        }
       

        $con = connect();
        execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1) and ENTRY_FORM=10");
        oci_commit($con);
        disconnect($con);
        
        ob_start();

        if($type==1)
        {
            $tblWidth = "1220";
            $divWidth = "1250";
            $colspan = "11";
        }
        ?>
       
        <style type="text/css">
            table tr th, table tr td{word-wrap: break-word;word-break: break-all;}
        </style>

        <div style="width:<?php echo $divWidth; ?>">

            <fieldset style="width:<? echo $div_width; ?>;">
                <table width="<? echo $tblWidth;?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" >
                    <thead>
                        <tr class="form_caption" style="border:none;">
                            <td colspan="18" align="center" style="border:none;font-size:16px; font-weight:bold" > Daily Yarn Stock Report</td>
                        </tr>
                        <tr style="border:none;">
                            <td colspan="18" align="center" style="border:none; font-size:14px;">
                                Company Name : <? echo $companyArr[str_replace("'", "", $cbo_company_name)]; ?>
                            </td>
                        </tr>
                        <tr style="border:none;">
                            <td colspan="18" align="center" style="border:none;font-size:12px; font-weight:bold">
                                <? if ($from_date != "" && $to_date != "") echo "From " . change_date_format($from_date, 'dd-mm-yyyy') . " To " . change_date_format($to_date, 'dd-mm-yyyy') . ""; ?>
                            </td>
                        </tr>
                        <tr  style="word-break:normal;">
                            <th width="40">SL</th>
                            <th width="80">Product Id</th>
                            <th width="80">Count</th>
                            <th width="200">Yarn Composition</th>
                            <th width="80">Brand</th>
                            <th width="80">Lot - No</th>
                            <th width="100">First Receiving<br> Date</th>
                            <th width="80">Day Rcvd</th>
                            <th width="100">Total Rcvd</th>
                            <th width="80">Day Issue</th>
                            <th width="100">Total Issue</th>
                            <th width="100">Stock Qty.</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                </table>
                <div style="width:<?php echo $divWidth; ?>px; overflow-y:scroll; max-height:350px" id="scroll_body" >
                    <table width="<?php echo $tblWidth; ?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
                        <?
                        $i = 1;
                        $grand_total_stock_in_hand=0;

                        foreach ($result as $row)
                        {
                            $compositionDetails = $composition[$row[csf("yarn_comp_type1st")]] . " " . $row[csf("yarn_comp_percent1st")] . "%\n";
                            if ($row[csf("yarn_comp_type2nd")] != 0)
                                $compositionDetails .= $composition[$row[csf("yarn_comp_type2nd")]] . " " . $row[csf("yarn_comp_percent2nd")] . "%";

                            //subtotal and group-----------------------
                            $check_string = $row[csf("yarn_count_id")] . $compositionDetails;
                            //$curent_date="'".$curent_date."'";
                            $stockInHand = $row[csf("total_recieved_quantity")]-$row[csf("total_issue_quantity")];

                            if ($i % 2 == 0)
                                $bgcolor = "#E9F3FF";
                            else
                                $bgcolor = "#FFFFFF";
                        
                            if (number_format($stockInHand, 2) > 0.00)
                            {
                                if (!in_array($check_string, $checkArr))
                                {
                                    $checkArr[$i] = $check_string;
                                    if ($i > 1) {
                                        ?>
                                        <tr bgcolor="#CCCCCC" style="font-weight:bold">
                                            <td colspan="<? echo $colspan;?>" align="right">Sub Total:</td>
                                            <td align="right"><? echo number_format($total_stock_in_hand, 2); ?></td>
                                            <td align="right">&nbsp;</td>
                                        </tr>
                                        <?
                                        $total_stock_in_hand = 0;
                                    }
                                }
                            
                                ?>
                                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                <td width="40"><? echo $i; ?></td>
                                <td width="80"><? echo $row[csf("id")]; ?></td>
                                <td width="80"><p style="mso-number-format:'\@';"><? echo $yarn_count_arr[$row[csf("yarn_count_id")]]; ?>&nbsp;</p></td>
                                <td width="200"><p><? echo $compositionDetails; ?></p></td>
                                <td width="80"><p><? echo $brandArr[$row[csf("brand")]]; ?></p></td>
                                <td width="80"><p><? echo $row[csf("lot")]; ?></p></td>
                                <td width="100"><p><? echo change_date_format($row[csf("all_rcv_mrr_min_date")]); ?></p</td>
                                <td width="80" align="right"><p><? echo number_format($mrr_info_arr[$row[csf("id")]]['mrr_total_recieved_quantity'],2,".",""); ?></p></td>
                                <td width="100" align="right"><p><? echo number_format($row[csf("total_recieved_quantity")],2,".",""); ?></p></td>
                                <td width="80" align="right"><p><? echo number_format($mrr_info_arr[$row[csf("id")]]['mrr_total_issue_quantity'],2,".",""); ?></p></td>
                                <td width="100" align="right"><p><? echo number_format($row[csf("total_issue_quantity")],2,".",""); ?></p> </td>
                                <td width="100" align="right" title="(Total Rcv-Total Issue)">
                                    <p>
                                    <? 
                                    echo number_format($stockInHand,2,".",""); ?>
                                    </p>
                                </td>
                            
                                <td align="right"><p>&nbsp;</p></td>
                                </tr>
                                <?
                                $i++;

                                $total_stock_in_hand += $stockInHand;
                                $grand_total_stock_in_hand += $stockInHand;
                            }       
                            
                        }
                            ?>
                            <tr bgcolor="#CCCCCC" style="font-weight:bold">
                                <td colspan="<? echo $colspan;?>>" align="right">Sub Total:</td>
                                <td align="right" style="word-wrap:break-word; word-break: break-all;" ><? echo number_format($total_stock_in_hand, 2); ?></td>
                                <td align="right">&nbsp;</td>
                            </tr>
                    </table>
                </div>
                <table width="<?php echo $tblWidth; ?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_footer">
                    <tr class="tbl_bottom">
                        <td width="40"></td>
                        <td width="80"></td>
                        <td width="80"></td>
                        <td width="200"></td>
                        <td width="80"></td>
                        <td width="80"></td>
                        <td width="100"></td>
                        <td width="80"></td>
                        <td width="100"></td>
                        <td width="80"></td>
                        <td  width="100" align="right">Grand Total:</td>
                        <td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"  id="value_total_stock_in_hand"><? echo number_format($grand_total_stock_in_hand, 2); ?></td>
                        <td align="right">&nbsp;</td>
                    </tr>
                </table>
            </fieldset>
        </div>
        <?
    }
	
	?>
	
	<?
	$html = ob_get_contents();
	ob_clean();
	foreach (glob("*.xls") as $filename)
	{
		@unlink($filename);
	}
	//---------end------------//
	$name = time();
	$filename = $user_id . "_" . $name . ".xls";
	$create_new_doc = fopen($filename, 'w+');
	$is_created = fwrite($create_new_doc, $html);
	echo "$html**$filename";
	exit();
}

if($action == "composition_popup")
{
    echo load_html_head_contents("Composition Info","../../../../", 1, 1, '','1','');
    extract($_REQUEST);
    ?>
    <script>
    var selected_id = new Array(); var selected_name = new Array();

    function check_all_data()
    {
        var tbl_row_count = document.getElementById( 'table_body' ).rows.length;

        tbl_row_count = tbl_row_count-1;
        for( var i = 1; i <= tbl_row_count; i++ ) {
            js_set_value( i );
        }
    }

    function toggle( x, origColor )
    {
        var newColor = 'yellow';
        if ( x.style ) {
            x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
        }
    }

    function set_all()
    {
        var old=document.getElementById('txt_pre_composition_row_id').value;
        if(old!="")
        {
            old=old.split(",");
            for(var k=0; k<old.length; k++)
            {
                js_set_value( old[k] )
            }
        }
    }

    function js_set_value( str )
    {

        toggle( document.getElementById( 'search' + str ), '#FFFFCC' );

        if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
            selected_id.push( $('#txt_individual_id' + str).val() );
            selected_name.push( $('#txt_individual' + str).val() );

        }
        else {
            for( var i = 0; i < selected_id.length; i++ ) {
                if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
            }
            selected_id.splice( i, 1 );
            selected_name.splice( i, 1 );
        }

        var id = ''; var name = '';
        for( var i = 0; i < selected_id.length; i++ ) {
            id += selected_id[i] + ',';
            name += selected_name[i] + ',';
        }

        id = id.substr( 0, id.length - 1 );
        name = name.substr( 0, name.length - 1 );

        $('#hidden_composition_id').val(id);
        $('#hidden_composition').val(name);
    }
    </script>
    </head>
    <fieldset style="width:390px">
        <legend>Yarn Receive Details</legend>
        <input type="hidden" name="hidden_composition" id="hidden_composition" value="">
        <input type="hidden" name="hidden_composition_id" id="hidden_composition_id" value="">
        <table width="370" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
            <thead>
                <tr>
                    <th colspan="2">
                        <? echo create_drop_down( "cbo_string_search_type", 150, $string_search_type,'', 1, "-- Searching Type --" ); ?>
                    </th>
                </tr>
                <tr>
                    <th width="50">SL</th>
                    <th width="">Composition Name</th>
                </tr>
            </thead>
        </table>
        <div style="width:390px; overflow-y:scroll; max-height:300px" id="scroll_body">
            <table width="370" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
        <?
        $i = 1;

        $result=sql_select("select id,composition_name from  lib_composition_array where status_active=1 and is_deleted=0 order by composition_name");
        $pre_composition_id_arr=explode(",",$pre_composition_id);
        foreach ($result as $row)
        {
            if ($i % 2 == 0)
                $bgcolor = "#E9F3FF";
            else
                $bgcolor = "#FFFFFF";


            if(in_array($row[csf("id")],$pre_composition_id_arr))
            {
                if($pre_composition_ids=="") $pre_composition_ids=$i; else $pre_composition_ids.=",".$i;
            }
            ?>
            <tr bgcolor="<? echo $bgcolor; ?>" onclick="js_set_value(<? echo $i; ?>)" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>">
                <td width="50">
                    <? echo $i; ?>
                    <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $row[csf("id")]; ?>"/>
                    <input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $row[csf("composition_name")]; ?>"/>
                </td>
                <td width=""><p><? echo $row[csf("composition_name")]; ?></p></td>
            </tr>
            <?
            $i++;
        }
        ?>
        <input type="hidden" name="txt_pre_composition_row_id" id="txt_pre_composition_row_id" value="<?php echo $pre_composition_ids; ?>"/>
        </table>
        </div>
        <table width="390" cellspacing="0" cellpadding="0" style="border:none" align="center">
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
    </fieldset>
    <script type="text/javascript">
        setFilterGrid('table_body',-1);
        set_all();
    </script>
    <?
}

if($action == "supplier_popup")
{
    echo load_html_head_contents("Supplier Info","../../../../", 1, 1, '','1','');
    extract($_REQUEST);
    ?>
    <script>
    var selected_id = new Array(); var selected_name = new Array();

    function check_all_data()
    {
        var tbl_row_count = document.getElementById( 'table_body' ).rows.length;

        tbl_row_count = tbl_row_count-1;
        for( var i = 1; i <= tbl_row_count; i++ ) {
            js_set_value( i );
        }
    }

    function toggle( x, origColor )
    {
        var newColor = 'yellow';
        if ( x.style ) {
            x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
        }
    }

    function js_set_value( str )
    {

        toggle( document.getElementById( 'search' + str ), '#FFFFCC' );

        if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
            selected_id.push( $('#txt_individual_id' + str).val() );
            selected_name.push( $('#txt_individual' + str).val() );

        }
        else {
            for( var i = 0; i < selected_id.length; i++ ) {
                if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
            }
            selected_id.splice( i, 1 );
            selected_name.splice( i, 1 );
        }

        var id = ''; var name = '';
        for( var i = 0; i < selected_id.length; i++ ) {
            id += selected_id[i] + ',';
            name += selected_name[i] + ',';
        }

        id = id.substr( 0, id.length - 1 );
        name = name.substr( 0, name.length - 1 );

        $('#hidden_supplier_id').val(id);
        $('#hidden_supplier').val(name);
    }
    </script>
    </head>
    <fieldset style="width:390px">

        <input type="hidden" name="hidden_supplier" id="hidden_supplier" value="">
        <input type="hidden" name="hidden_supplier_id" id="hidden_supplier_id" value="">
        <table width="370" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
            <thead>
                <tr>
                    <th width="50">SL</th>
                    <th width="">Supplier Name</th>
                </tr>
            </thead>
        </table>
        <div style="width:390px; overflow-y:scroll; max-height:300px" id="scroll_body">
            <table width="370" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
        <?

        if($companyID){$companyCon=" and a.tag_company='$companyID'";}
        else{$companyCon="";}

        $result=sql_select("select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id $companyCon and b.party_type =2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name");
        $i = 1;
        foreach ($result as $row)
        {
            if ($i % 2 == 0)
                $bgcolor = "#E9F3FF";
            else
                $bgcolor = "#FFFFFF";
            ?>
            <tr bgcolor="<? echo $bgcolor; ?>" onclick="js_set_value(<? echo $i; ?>)" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>">
                <td width="50">
                    <? echo $i; ?>
                    <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $row[csf("id")]; ?>"/>
                    <input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $row[csf("supplier_name")]; ?>"/>
                </td>
                <td width=""><p><? echo $row[csf("supplier_name")]; ?></p></td>
            </tr>
            <?
            $i++;
        }
        ?>

        </table>
        </div>
        <table width="390" cellspacing="0" cellpadding="0" style="border:none" align="center">
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
    </fieldset>
    <script type="text/javascript">
        setFilterGrid('table_body',-1);
    </script>
    <?
}

if($action == "yarn_type_popup")
{
    echo load_html_head_contents("Yarn Type Info","../../../../", 1, 1, '','1','');
    extract($_REQUEST);
    ?>
    <script>
    var selected_id = new Array(); var selected_name = new Array();

    function check_all_data()
    {
        var tbl_row_count = document.getElementById( 'table_body' ).rows.length;

        tbl_row_count = tbl_row_count-1;
        for( var i = 1; i <= tbl_row_count; i++ ) {
            js_set_value( i );
        }
    }

    function toggle( x, origColor )
    {
        var newColor = 'yellow';
        if ( x.style ) {
            x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
        }
    }

    function js_set_value( str )
    {

        toggle( document.getElementById( 'search' + str ), '#FFFFCC' );

        if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
            selected_id.push( $('#txt_individual_id' + str).val() );
            selected_name.push( $('#txt_individual' + str).val() );

        }
        else {
            for( var i = 0; i < selected_id.length; i++ ) {
                if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
            }
            selected_id.splice( i, 1 );
            selected_name.splice( i, 1 );
        }

        var id = ''; var name = '';
        for( var i = 0; i < selected_id.length; i++ ) {
            id += selected_id[i] + ',';
            name += selected_name[i] + ',';
        }

        id = id.substr( 0, id.length - 1 );
        name = name.substr( 0, name.length - 1 );

        $('#hidden_yarn_type_id').val(id);
        $('#hidden_yarn_type').val(name);
    }
    </script>
    </head>
    <fieldset style="width:390px">
        <input type="hidden" name="hidden_yarn_type" id="hidden_yarn_type" value="">
        <input type="hidden" name="hidden_yarn_type_id" id="hidden_yarn_type_id" value="">
        <table width="370" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
            <thead>
                <tr>
                    <th width="50">SL</th>
                    <th width="">Yarn Type Name</th>
                </tr>
            </thead>
        </table>
        <div style="width:390px; overflow-y:scroll; max-height:300px" id="scroll_body">
            <table width="370" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
        <?
        $i = 1;
        foreach ($yarn_type as $key=> $val)
        {
            //var_dump($val);
            if ($i % 2 == 0)
                $bgcolor = "#E9F3FF";
            else
                $bgcolor = "#FFFFFF";
            ?>
            <tr bgcolor="<? echo $bgcolor; ?>" onclick="js_set_value(<? echo $i; ?>)" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>">
                <td width="50">
                    <? echo $i; ?>
                    <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $key; ?>"/>
                    <input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $val; ?>"/>
                </td>
                <td width=""><p><? echo $val; ?></p></td>
            </tr>
            <?
            $i++;
        }
        ?>

        </table>
        </div>
        <table width="390" cellspacing="0" cellpadding="0" style="border:none" align="center">
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
    </fieldset>
    <script type="text/javascript">
        setFilterGrid('table_body',-1);
    </script>
    <?
}

if($action == "yarn_count_popup")
{
    echo load_html_head_contents("Yarn Count Info","../../../../", 1, 1, '','1','');
    extract($_REQUEST);
    ?>
    <script>
    var selected_id = new Array(); var selected_name = new Array();

    function check_all_data()
    {
        var tbl_row_count = document.getElementById( 'table_body' ).rows.length;

        tbl_row_count = tbl_row_count-1;
        for( var i = 1; i <= tbl_row_count; i++ ) {
            js_set_value( i );
        }
    }

    function toggle( x, origColor )
    {
        var newColor = 'yellow';
        if ( x.style ) {
            x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
        }
    }

    function js_set_value( str )
    {

        toggle( document.getElementById( 'search' + str ), '#FFFFCC' );

        if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
            selected_id.push( $('#txt_individual_id' + str).val() );
            selected_name.push( $('#txt_individual' + str).val() );

        }
        else {
            for( var i = 0; i < selected_id.length; i++ ) {
                if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
            }
            selected_id.splice( i, 1 );
            selected_name.splice( i, 1 );
        }

        var id = ''; var name = '';
        for( var i = 0; i < selected_id.length; i++ ) {
            id += selected_id[i] + ',';
            name += selected_name[i] + ',';
        }

        id = id.substr( 0, id.length - 1 );
        name = name.substr( 0, name.length - 1 );

        $('#hidden_yarn_count_id').val(id);
        $('#hidden_yarn_count').val(name);
    }
    </script>
    </head>
    <fieldset style="width:390px">

        <input type="hidden" name="hidden_yarn_count" id="hidden_yarn_count" value="">
        <input type="hidden" name="hidden_yarn_count_id" id="hidden_yarn_count_id" value="">
        <table width="370" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
            <thead>
                <tr>
                    <th width="50">SL</th>
                    <th width="">Yarn Count Name</th>
                </tr>
            </thead>
        </table>
        <div style="width:390px; overflow-y:scroll; max-height:300px" id="scroll_body">
            <table width="370" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
        <?
        $result=sql_select("select id,yarn_count from lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count");
        $i = 1;
        foreach ($result as $row)
        {
            if ($i % 2 == 0)
                $bgcolor = "#E9F3FF";
            else
                $bgcolor = "#FFFFFF";
            ?>
            <tr bgcolor="<? echo $bgcolor; ?>" onclick="js_set_value(<? echo $i; ?>)" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>">
                <td width="50">
                    <? echo $i; ?>
                    <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $row[csf("id")]; ?>"/>
                    <input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $row[csf("yarn_count")]; ?>"/>
                </td>
                <td width=""><p><? echo $row[csf("yarn_count")]; ?></p></td>
            </tr>
            <?
            $i++;
        }
        ?>

        </table>
        </div>
        <table width="390" cellspacing="0" cellpadding="0" style="border:none" align="center">
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
    </fieldset>
    <script type="text/javascript">
        setFilterGrid('table_body',-1);
    </script>
    <?
}

if($action == "yarn_brand_popup")
{
    echo load_html_head_contents("Yarn Brand Info","../../../../", 1, 1, '','1','');
    extract($_REQUEST);
    ?>
    <script>
    var selected_id = new Array(); var selected_name = new Array();

    function check_all_data()
    {
        var tbl_row_count = document.getElementById( 'table_body' ).rows.length;

        tbl_row_count = tbl_row_count-1;
        for( var i = 1; i <= tbl_row_count; i++ ) {
            js_set_value( i );
        }
    }

    function toggle( x, origColor )
    {
        var newColor = 'yellow';
        if ( x.style ) {
            x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
        }
    }

    function js_set_value( str )
    {

        toggle( document.getElementById( 'search' + str ), '#FFFFCC' );

        if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
            selected_id.push( $('#txt_individual_id' + str).val() );
            selected_name.push( $('#txt_individual' + str).val() );

        }
        else {
            for( var i = 0; i < selected_id.length; i++ ) {
                if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
            }
            selected_id.splice( i, 1 );
            selected_name.splice( i, 1 );
        }

        var id = ''; var name = '';
        for( var i = 0; i < selected_id.length; i++ ) {
            id += selected_id[i] + ',';
            name += selected_name[i] + ',';
        }

        id = id.substr( 0, id.length - 1 );
        name = name.substr( 0, name.length - 1 );

        $('#hidden_yarn_brand_id').val(id);
        $('#hidden_yarn_brand').val(name);
    }
    </script>
    </head>
    <fieldset style="width:390px">

        <input type="hidden" name="hidden_yarn_brand" id="hidden_yarn_brand" value="">
        <input type="hidden" name="hidden_yarn_brand_id" id="hidden_yarn_brand_id" value="">
        <table width="370" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
            <thead>
                <tr>
                    <th width="50">SL</th>
                    <th width="">Yarn Brand Name</th>
                </tr>
            </thead>
        </table>
        <div style="width:390px; overflow-y:scroll; max-height:300px" id="scroll_body">
            <table width="370" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
        <?
        $result=sql_select("select id,brand_name from lib_brand where is_deleted=0 and status_active=1 order by brand_name");
        $i = 1;
        foreach ($result as $row)
        {
            if ($i % 2 == 0)
                $bgcolor = "#E9F3FF";
            else
                $bgcolor = "#FFFFFF";
            ?>
            <tr bgcolor="<? echo $bgcolor; ?>" onclick="js_set_value(<? echo $i; ?>)" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>">
                <td width="50">
                    <? echo $i; ?>
                    <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $row[csf("id")]; ?>"/>
                    <input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $row[csf("brand_name")]; ?>"/>
                </td>
                <td width=""><p><? echo $row[csf("brand_name")]; ?></p></td>
            </tr>
            <?
            $i++;
        }
        ?>

        </table>
        </div>
        <table width="390" cellspacing="0" cellpadding="0" style="border:none" align="center">
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
    </fieldset>
    <script type="text/javascript">
        setFilterGrid('table_body',-1);
    </script>
    <?
}    
	
	?>
