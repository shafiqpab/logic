<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );

if($action=="load_drop_down_buyer")
{
    echo create_drop_down( "cbo_buyer_id", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' order by buy.buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "","" );
    exit();
}

if($action=="load_drop_down_file_year")
{
	//$sql="select a.lc_year from com_export_lc a where a.status_active=1 and a.is_deleted=0 and a.beneficiary_name=$data group by a.lc_year";
    $sql="select a.lc_year from com_export_lc a where a.status_active=1 and a.is_deleted=0 and a.beneficiary_name=$data group by a.lc_year
    union
    select a.sc_year as lc_year from com_sales_contract a where a.status_active=1 and a.is_deleted=0 and a.beneficiary_name=$data group by  a.sc_year
    order by lc_year";

	echo create_drop_down( "cbo_file_year", 150, $sql,"lc_year,lc_year", 1, "---- Year ----", 0, "" );
	exit();
}

if($action=="file_search")
{
	echo load_html_head_contents("Export LC Form", "../../../../", 1, 1,'','1','');
	extract($_REQUEST);
	$sql="select a.internal_file_no, a.lc_year, 1 as type from com_export_lc a where a.status_active=1 and a.is_deleted=0 and a.beneficiary_name=$company_id and a.lc_year='$file_year' group by a.internal_file_no, a.lc_year
	union
	select a.internal_file_no, a.sc_year as lc_year, 2 as type from com_sales_contract a where a.status_active=1 and a.is_deleted=0 and a.beneficiary_name=$company_id and a.sc_year='$file_year' group by a.internal_file_no, a.sc_year
	order by internal_file_no";

	?>

	<script>

        var selected_id = new Array, selected_name = new Array();
        function check_all_data() 
        {
			var tbl_row_count = document.getElementById( 'table_body' ).rows.length;

			tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ ) 
            {
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
			toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );
			var file=$("#td_"+str).html();
			if( jQuery.inArray(file, selected_id ) == -1 ) {
				selected_id.push(file);

			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == file) break;
				}
				selected_id.splice( i, 1 );
			}
			var id = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );

			$('#txt_selected_id').val( id );
		}
    </script>

    </head>
    <body>
        <div align="center" style="width:520px;">
            <form name="searchexportlcfrm" id="searchexportlcfrm">
                <fieldset style="width:500px; margin-left:3px">
                    <input type="hidden" id="txt_selected_id" >
                    <table cellpadding="0" cellspacing="0" width="100%" class="rpt_table"  border="1" rules="all">
                        <thead>
                            <th width="50">Sl</th>
                            <th width="80">Year</th>
                            <th>File No</th>
                        </thead>
                    </table>
                    <div style="width:500px; max-height:290px; overflow:auto;" >
                        <table cellpadding="0" cellspacing="0" width="480" class="rpt_table" id="table_body" border="1" rules="all">
                            <tbody>
                            <?
                            $sql_result=sql_select($sql);$i=1;
                            foreach($sql_result as $row)
                            {	if(!in_array($row[csf("internal_file_no")],$temp_file_arr))
                                {
                                    $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
                                    ?>
                                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $i; ?>');" style="cursor:pointer;" id="tr_<? echo $i;?>">
                                        <td width="50" align="center"><? echo $i; ?></td>
                                        <td width="80"><? echo $row[csf("lc_year")]; ?></td>
                                        <td id="td_<? echo $i;?>"><? echo $row[csf("internal_file_no")]; ?></td>
                                    </tr>
                                    <?
                                    $i++;
                                    $temp_file_arr[]=$row[csf("internal_file_no")];
                                }
                            }
                            ?>
                            </tbody>
                        </table>
                        <script>setFilterGrid('table_body',-1);</script>
                    </div>
                </fieldset>
                <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
                <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
            </form>
        </div>
    </body>
    <script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();

}


if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_buyer=str_replace("'","",$cbo_buyer_id);
	$cbo_file_year=str_replace("'","",$cbo_file_year);
	$txt_internal_file_no_arr=explode(",",str_replace("'","",$txt_internal_file_no));
	$txt_internal_file_no="";
	foreach($txt_internal_file_no_arr as $file_no)
	{
        if(!empty($file_no))
        {
            $txt_internal_file_no.="'".$file_no."',";
        }
	}
	$txt_internal_file_no=chop($txt_internal_file_no,",");
	//echo $txt_internal_file_no.test;die;

    if ($cbo_buyer != 0)
    {
        $buyer_con = " and a.buyer_name in($cbo_buyer)";
    }

    if ($txt_internal_file_no != '')
    {
        $file_con = " and a.internal_file_no in($txt_internal_file_no)";
    }
	//echo $file_con.test;die;
   

    // for lc---------------
    $sql="SELECT a.internal_file_no, a.export_lc_no, a.buyer_name, e.pi_number, e.supplier_id, d.pi_id, d.color_id, d.count_name, d.yarn_type, d.net_pi_rate, d.quantity as pi_qty, d.net_pi_amount, d.yarn_composition_item1 as yarn_composition_id, d.yarn_composition_percentage2, f.bank_code, f.lc_year, f.lc_category, f.lc_serial, e.remarks,f.lc_date,f.id as btb_id
    FROM com_export_lc a, com_btb_export_lc_attachment b, com_btb_lc_pi c, com_pi_item_details d, com_pi_master_details e, com_btb_lc_master_details f
    WHERE a.id=b.lc_sc_id and b.import_mst_id=c.com_btb_lc_master_details_id and b.is_lc_sc=0 and c.pi_id=d.pi_id and c.pi_id=e.id and b.import_mst_id=f.id and a.status_active=1 and  a.is_deleted=0 and b.status_active=1 and  b.is_deleted=0 and c.status_active=1 and  c.is_deleted=0 and d.status_active=1 and  d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and  f.is_deleted=0 and e.item_category_id=1 and
    f.item_category_id=1 and a.beneficiary_name=$cbo_company_name  $buyer_con and a.lc_year='$cbo_file_year' $file_con";
    //echo $sql;
    $lcresult=sql_select($sql);
    $piIdChk = array();
    $piIdArr = array();
    $btbIdArr = array();
    foreach($lcresult as $row)
    {
        $yarn_composition=$count_arr[$row[csf('count_name')]].' '.$composition[$row[csf('yarn_composition_id')]].' 100 '.$yarn_type[$row[csf('yarn_type')]].' '.$color_arr[$row[csf('color_id')]];

        $buyer_data_arr[$row[csf('buyer_name')]][$row[csf('internal_file_no')]][$row[csf('btb_id')]][$row[csf('pi_id')]][$yarn_composition]=array(
            'buyer_name'=>$row[csf('buyer_name')],
            'export_lc_no'=>$row[csf('export_lc_no')],
            'btb_lc'=>$row[csf('bank_code')].$row[csf('lc_year')].$row[csf('lc_category')].$row[csf('lc_serial')],
            'pi_number'=>$row[csf('pi_number')],
            'color_id'=>$row[csf('color_id')],
            'supplier_id'=>$row[csf('supplier_id')],
            'count_name'=>$row[csf('count_name')],
            'yarn_composition'=>$yarn_composition,
            'yarn_type'=>$row[csf('yarn_type')],
            'pi_qty'=>$row[csf('pi_qty')],
            'net_pi_rate'=>$row[csf('net_pi_rate')],
            'net_pi_amount'=>$row[csf('net_pi_amount')],
            'remarks'=>$row[csf('remarks')],
            'lc_date'=>$row[csf('lc_date')],
            'btb_id'=>$row[csf('btb_id')]
        );

        $qtyArr[$row[csf('buyer_name')]][$row[csf('internal_file_no')]][$row[csf('export_lc_no')]][$row[csf('pi_id')]][$yarn_composition]+=$row[csf('pi_qty')];

        if($piIdChk[$row[csf('pi_id')]] == "")
        {
            $piIdChk[$row[csf('pi_id')]] = $row[csf('pi_id')];
            array_push($piIdArr,$row[csf('pi_id')]);
        }

        if($btbIdChk[$row[csf('btb_id')]] == "")
        {
            $btbIdChk[$row[csf('btb_id')]] = $row[csf('btb_id')];
            array_push($btbIdArr,$row[csf('btb_id')]);
        }
    
    }
    unset($lcresult);
    /* echo "<pre>";
    print_r($buyer_data_arr);  */


    // for sc---------------
    $sc_sql="SELECT a.internal_file_no, a.contract_no as export_lc_no, a.buyer_name, e.pi_number, e.supplier_id, d.pi_id, d.color_id, d.count_name, d.yarn_type, d.net_pi_rate, d.quantity as pi_qty, d.net_pi_amount, d.yarn_composition_item1 as yarn_composition_id, d.yarn_composition_percentage2, f.bank_code, f.lc_year, f.lc_category, f.lc_serial, f.remarks, f.lc_date,f.id as btb_id
    FROM com_sales_contract a, com_btb_export_lc_attachment b, com_btb_lc_pi c, com_pi_item_details	 d, com_pi_master_details e, com_btb_lc_master_details f
    WHERE a.id=b.lc_sc_id and b.import_mst_id=c.com_btb_lc_master_details_id and b.is_lc_sc=1 and c.pi_id=d.pi_id and c.pi_id=e.id and b.import_mst_id=f.id and a.status_active=1 and  a.is_deleted=0 and b.status_active=1 and  b.is_deleted=0 and c.status_active=1 and  c.is_deleted=0 and d.status_active=1 and  d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and  f.is_deleted=0 and e.item_category_id=1 and f.item_category_id=1 and  a.beneficiary_name=$cbo_company_name $buyer_con and a.sc_year='$cbo_file_year' $file_con";
    //echo $sc_sql;
    $scresult=sql_select($sc_sql);
    foreach($scresult as $row)
    {
        $yarn_composition=$count_arr[$row[csf('count_name')]].' '.$composition[$row[csf('yarn_composition_id')]].' 100 '.$yarn_type[$row[csf('yarn_type')]].' '.$color_arr[$row[csf('color_id')]];
        
        $buyer_data_arr[$row[csf('buyer_name')]][$row[csf('internal_file_no')]][$row[csf('btb_id')]][$row[csf('pi_id')]][$yarn_composition]=array(
            'buyer_name'=>$row[csf('buyer_name')],
            'export_lc_no'=>$row[csf('export_lc_no')],
            'btb_lc'=>$row[csf('bank_code')].$row[csf('lc_year')].$row[csf('lc_category')].$row[csf('lc_serial')],
            'pi_number'=>$row[csf('pi_number')],
            'color_id'=>$row[csf('color_id')],
            'supplier_id'=>$row[csf('supplier_id')],
            'count_name'=>$row[csf('count_name')],
            'yarn_composition'=>$yarn_composition,
            'yarn_type'=>$row[csf('yarn_type')],
            'pi_qty'=>$row[csf('pi_qty')],
            'net_pi_rate'=>$row[csf('net_pi_rate')],
            'net_pi_amount'=>$row[csf('net_pi_amount')],
            'remarks'=>$row[csf('remarks')],
            'lc_date'=>$row[csf('lc_date')],
            'btb_id'=>$row[csf('btb_id')]
        );
     
        $qtyArr[$row[csf('buyer_name')]][$row[csf('internal_file_no')]][$row[csf('export_lc_no')]][$row[csf('pi_id')]][$yarn_composition]+=$row[csf('pi_qty')];

        if($piIdChk[$row[csf('pi_id')]] == "")
        {
            $piIdChk[$row[csf('pi_id')]] = $row[csf('pi_id')];
            array_push($piIdArr,$row[csf('pi_id')]);
        }

        if($btbIdChk[$row[csf('btb_id')]] == "")
        {
            $btbIdChk[$row[csf('btb_id')]] = $row[csf('btb_id')];
            array_push($btbIdArr,$row[csf('btb_id')]);
        }
        
    }
    unset($scresult);


    $Rcvsql="SELECT
                a.booking_id as pi_id,
                b.remarks as rec_remarks,
                a.recv_number as mrr_number,
                a.receive_date,
                a.supplier_id,
                b.order_qnty as rec_qty,
                b.order_rate as rec_rate,
                b.order_amount as rec_amount,
                c.product_name_details,
                c.lot
            FROM
                inv_receive_master a,
                inv_transaction b,
                product_details_master c
            WHERE
                a.id=b.mst_id and
                a.entry_form=1 and
                a.receive_basis=1 and
                b.prod_id=c.id and b.item_category=1 and c.item_category_id=1 and
                a.company_id=$cbo_company_name and
                a.status_active=1 and
                a.is_deleted=0 and
                b.status_active=1 and
                b.is_deleted=0 and
                c.status_active=1 and
                c.is_deleted=0
                ".where_con_using_array($piIdArr,0,'a.booking_id')."
            ";
    //echo $Rcvsql;
    $RcRresult=sql_select($Rcvsql);
    $mrrArr = array();
    foreach($RcRresult as $row)
    {
        $rec_data_arr[$row[csf('pi_id')]][$row[csf('product_name_details')]]['rec_qty'] +=$row[csf('rec_qty')]; 
        $rec_data_arr[$row[csf('pi_id')]][$row[csf('product_name_details')]]['lot'] =$row[csf('lot')];  
        $rec_data_arr1[$row[csf('pi_id')]][$row[csf('product_name_details')]]['lot'] .=$row[csf('lot')].',';  
        
        $rec_data_arr[$row[csf('pi_id')]][$row[csf('product_name_details')]]['mrr_number'] .=$row[csf('mrr_number')].'**'.$row[csf('lot')].','; 

        if($mrrChk[$row[csf('mrr_number')]] == "")
        {
            $mrrChk[$row[csf('mrr_number')]] = $row[csf('mrr_number')];
            array_push($mrrArr,$row[csf('mrr_number')]);
        }
    }
    unset($RcRresult);

    $sqlRcvRtn = "SELECT b.id, b.issue_number, a.order_rate,a.cons_quantity,a.cons_rate,a.order_qnty,b.received_mrr_no,c.lot,a.id as trans_id,a.transaction_type,a.btb_lc_id,c.product_name_details, c.id as prod_id
    from inv_transaction a, inv_issue_master b, product_details_master c
    where a.mst_id = b.id and b.company_id=$cbo_company_name and a.transaction_type in(3) and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 and a.prod_id = c.id  and a.item_category=1 and c.item_category_id=1  ".where_con_using_array($mrrArr,1,'b.received_mrr_no')."";
    //echo $sqlRcvRtn;
    $resultRcvRtn=sql_select($sqlRcvRtn); $receive_return_arr = array(); $transRcvIdChk =array(); 
    foreach($resultRcvRtn as $row)
    {
        if($transRcvIdChk[$row[csf('trans_id')]] == "")
        {
            $transRcvIdChk[$row[csf('trans_id')]] = $row[csf('trans_id')];
            $receive_return_arr[$row[csf('received_mrr_no')]][$row[csf('lot')]]["qnty"] += $row[csf('cons_quantity')];
        }
    }
    unset($resultRcvRtn);


    $sqlIssue = "SELECT b.id, b.issue_number, a.order_rate,a.cons_quantity,a.cons_rate,a.order_qnty,b.received_mrr_no,c.lot,a.id as trans_id,a.transaction_type,a.btb_lc_id,c.product_name_details, c.id as prod_id
    from inv_transaction a, inv_issue_master b, product_details_master c
    where a.mst_id = b.id and b.company_id=$cbo_company_name and a.transaction_type in(2) and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 and a.prod_id = c.id  and a.item_category=1 and c.item_category_id=1 ".where_con_using_array($btbIdArr,0,'a.btb_lc_id')." order by c.id desc";
    //echo $sqlIssue;
    $resultIssue=sql_select($sqlIssue); $transIssIdChk =array(); $issue_no_arr = array();
    foreach($resultIssue as $row)
    {
        if($transIssIdChk[$row[csf('trans_id')]] == "")
        {
            $transIssIdChk[$row[csf('trans_id')]] = $row[csf('trans_id')];
            $issue_data_arr[$row[csf('btb_lc_id')]][$row[csf('product_name_details')]][$row[csf('lot')]]["qnty"] += $row[csf('cons_quantity')];

            $issue_data_arr[$row[csf('btb_lc_id')]][$row[csf('product_name_details')]][$row[csf('lot')]]["issue_number"] = $row[csf('issue_number')];
            $issue_data_arr[$row[csf('btb_lc_id')]][$row[csf('product_name_details')]][$row[csf('lot')]]["prod_id"] = $row[csf('prod_id')];

            $issue_data_arr1[$row[csf('btb_lc_id')]][$row[csf('product_name_details')]][$row[csf('lot')]]["qnty"] += $row[csf('cons_quantity')];
            $issue_data_arr1[$row[csf('btb_lc_id')]][$row[csf('product_name_details')]][$row[csf('lot')]]["issue_number"] .= $row[csf('issue_number')].',';
            $issue_number_arr[$row[csf('issue_number')]]["prod_id"] .= $row[csf('prod_id')].',';
        
            array_push($issue_no_arr, $row[csf('issue_number')]);
        }
    }
    unset($resultIssue);
    //var_dump($issue_number_arr);

    // echo "SELECT a.recv_number,a.booking_no, b.cons_quantity, c.issue_number,b.prod_id, b.id as trans_id from inv_receive_master a, inv_transaction b, inv_issue_master c where a.id = b.mst_id and a.issue_id = c.id and b.transaction_type = 4 and b.item_category = 1 and a.entry_form = 9 and b.status_active = 1 and b.is_deleted = 0 and a.status_active = 1 ".where_con_using_array($issue_no_arr,1,'c.issue_number')." ";

    // echo "SELECT a.recv_number,a.booking_no, b.cons_quantity, c.issue_number,b.prod_id, b.id as trans_id from inv_receive_master a, inv_transaction b, inv_issue_master c where a.id = b.mst_id and a.issue_id = c.id and b.transaction_type = 4 and b.item_category = 1 and a.entry_form = 9 and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 ".where_con_using_array($issue_no_arr,1,'c.issue_number')." ";

    // $issue_return_res = sql_select("SELECT a.recv_number,a.booking_no, b.cons_quantity, c.issue_number,b.prod_id, b.id as trans_id from inv_receive_master a, inv_transaction b, inv_issue_master c where a.id = b.mst_id and a.issue_id = c.id and b.transaction_type = 4 and b.item_category = 1 and a.entry_form = 9 and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 ".where_con_using_array($issue_no_arr,1,'c.issue_number')." order by b.prod_id desc");

    // $issue_return_res = sql_select("SELECT a.recv_number,a.booking_no, b.cons_quantity, c.issue_number,b.prod_id, b.id as trans_id 
    // from inv_receive_master a, inv_transaction b, inv_issue_master c where a.id = b.mst_id and a.issue_id = c.id and b.transaction_type = 4 and b.item_category = 1 and a.entry_form = 9 and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 ".where_con_using_array($issue_no_arr,1,'c.issue_number')." order by b.prod_id desc");

    $issue_return_res = sql_select("SELECT a.recv_number,a.booking_no, b.cons_quantity, c.issue_number,b.prod_id, b.id as trans_id ,
    d.product_name_details,d.lot
    from inv_receive_master a, inv_transaction b, inv_issue_master c ,product_details_master d
    where a.id = b.mst_id and a.issue_id = c.id and b.prod_id=d.id and d.item_category_id=1 and b.transaction_type = 4 and b.item_category = 1 and a.entry_form = 9 and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 ".where_con_using_array($issue_no_arr,1,'c.issue_number')." order by b.prod_id desc");

    $transRtnIdChk = array();
    foreach ($issue_return_res as $val) 
    {
        if($transRtnIdChk[$val[csf("trans_id")]]=="")
        {
            $transRtnIdChk[$val[csf("trans_id")]] = $val[csf("trans_id")];
            //$issue_return_qnty_arr[$val[csf("issue_number")]][$val[csf("prod_id")]] += $val[csf("cons_quantity")];
            $issue_return_qnty_arr[$val[csf("issue_number")]][$val[csf("prod_id")]][$val[csf("product_name_details")]][$val[csf("lot")]]+= $val[csf("cons_quantity")];
        }          
    }
    unset($issue_return_res);

    $buyer_count=array();
    $file_count=array();
    $lc_count=array();
    foreach($buyer_data_arr as $buyer_key=>$buyer_data)
    {   
        foreach ($buyer_data as $file_no_key => $file_data) 
        {
            foreach($file_data as $lc_key=>$lc_data_arr)
            {
                foreach($lc_data_arr as $pi_key=>$pidata_arr)
                {
                    foreach($pidata_arr as $composition=> $row)
                    {
                        $buyer_count[$buyer_key]++;
                        $file_count[$buyer_key][$file_no_key]++;
                        $lc_count[$buyer_key][$file_no_key][$lc_key]++;
                    }
                }
            }
        }
    }

    $b_data_count=array();
    foreach ($lc_count as $buyer_id => $buyer_data) 
    {
        foreach ($buyer_data as $file_key => $file_data) 
        {
            foreach ($file_data as $lc_key => $lc_data) 
            {
                $b_data_count[$buyer_id]++;
            }
        }
    }

    ob_start();
    ?>
    <style>
        .wrd_brk{word-break: break-all;word-wrap: break-word;}          
    </style>

    <fieldset style="width:1530px">
        <div style="width:100%; margin-left:10px;" align="left">
            <table width="1510" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                <thead>
                    <tr>
                        <th width="35">SL</th>
                        <th width="100">Buyer</th>
                        <th width="80">File No</th>
                        <th width="100">L/C No</th>
                        <th width="70">L/C Date</th>
                        <th width="60">Yarn Count</th>
                        <th width="140">Composition</th>
                        <th width="90">Yarn Type</th>
                        <th width="90">L/C Qty</th>
                        <th width="90">Receive Qty</th>
                        <th width="90">Yarn Issue<br> Return</th>
                        <th width="90">Total  Receive</th>
                        <th width="90">Yarn Issue</th>
                        <th width="90">Receive Return</th>
                        <th width="90">Total Issue</th>
                        <th width="90">Balance</th>
                        <th width="">Pipe Line</th>
                    </tr>
                </thead>
            </table>
            <!-- style="max-height:350px; overflow-y:auto; width:1667px;" -->
            <div>
            <table width="1510" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                <? 	$i=1;

                $glc_tot_qnty=0;$g_tot_rcv=0; $g_tot_iss_rtn=0;$gttot_rcv=0;$gtissueQty=0;$gtrcvRtnQnty=0;$gttot_issue=0;$gtbalance=0;$gtpipe_line=0;
                
                foreach($buyer_data_arr as $buyer_key=>$buyer_data)
                {   
                    $blc_tot_qnty=0;$buyer_tot_rcv =0; $btot_iss_rtn=0;$bttot_rcv=0;$btissueQty=0;$btrcvRtnQnty=0; $bttot_issue-0;$btbalance=0; $btpipe_line=0;
                    foreach ($buyer_data as $file_no_key => $file_data) 
                    {
                        $flc_tot_qnty=0; $file_tot_rcv=0;$ftot_iss_rtn=0;$fttot_rcv=0; $ftissueQty=0; $ftrcvRtnQnty=0; $fttot_issue=0;$ftbalance=0;$ftpipe_line=0;
                        foreach($file_data as $lc_key=>$lc_data_arr)
                        {
                            $lc_tot_qnty=0;$lc_tot_rcv=0;$lc_tot_iss_rtn=0; $lc_ttot_rcv=0;$lc_tissueQty=0;$lc_trcvRtnQnty=0;$lc_ttot_issue=0;$lc_tbalance=0;$lc_tpipe_line=0;
                            foreach($lc_data_arr as $pi_key=>$pidata_arr)
                            {
                                foreach($pidata_arr as $composition=> $row)
                                {
                                    $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";

                                    $buyer_span = $buyer_count[$buyer_key]+$b_data_count[$buyer_key]+count($file_count[$buyer_key]);
                                    $file_span  = $file_count[$buyer_key][$file_no_key]+count($lc_count[$buyer_key][$file_no_key]);
                                    $lc_span    = $lc_count[$buyer_key][$file_no_key][$lc_key];

                                    ?>
                                    <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                                       
                                        
                                            <?
                                            if(!in_array($buyer_key,$buyer_chk))
                                            {
                                                $buyer_chk[]=$buyer_key;
                                                ?>
                                                 <td width="35" class="wrd_brk" align="center" rowspan="<? echo $buyer_span ;?>"  valign="middle" ><? echo $i; ?>&nbsp;</td>
                                                    <td width="100" class="wrd_brk" rowspan="<? echo $buyer_span ;?>" valign="middle" align="center"><p><? echo $buyer_arr[$buyer_key]; ?>&nbsp;</p></td>
                                                <?
                                            }

                                            if(!in_array($buyer_key."**".$file_no_key,$file_chk))
                                            {
                                                $file_chk[]=$buyer_key."**".$file_no_key;
                                                ?>
                                                    <td width="80" rowspan="<? echo $file_span;?>" valign="middle" class="wrd_brk" align="center"><? echo $file_no_key; ?>&nbsp;</td>
                                                <?
                                            }

                                            if(!in_array($buyer_key."**".$file_no_key."**".$lc_key,$lc_chk))
                                            {
                                                $lc_chk[]=$buyer_key."**".$file_no_key."**".$lc_key;
                                                ?>
                                                    <td width="100" rowspan="<? echo $lc_span;?>" valign="middle" class="wrd_brk" align="center"><? echo $row['btb_lc']; ?>&nbsp;</td>
                                                    <td width="70" rowspan="<? echo $lc_span;?>" valign="middle" class="wrd_brk" align="center"><? echo change_date_format($row['lc_date']); ?>&nbsp;</td>
                                                <?
                                            }
                                        
                                        ?>
                                        
                                        <td width="60" class="wrd_brk" align="center"><? echo $count_arr[$row['count_name']]; ?>&nbsp;</td>
                                        <td width="140" class="wrd_brk" align="center"><? echo $row['yarn_composition']; ?>&nbsp;</td>
                                        <td width="90" class="wrd_brk" align="center"><? echo $yarn_type[$row['yarn_type']]; ?>&nbsp;</td>
                                        <td width="90" class="wrd_brk"  align="right">
                                        <?
                                        $lc_qnty = $qtyArr[$buyer_key][$file_no_key][$row['export_lc_no']][$pi_key][$row['yarn_composition']];
                                        echo number_format($lc_qnty,2,'.','');   
                                        ?>&nbsp;</td>
                                        
                                        <td width="90" class="wrd_brk" align="right" ><? $rcvQnty = $rec_data_arr[$pi_key][$row['yarn_composition']]['rec_qty']; echo number_format($rcvQnty,2,'.','');
                                        ?>&nbsp;</td>

                                        <td width="90" class="wrd_brk" align="right" >
                                            <?
                                                $lot = $rec_data_arr1[$pi_key][$row['yarn_composition']]['lot'];
                                                $lots = array_unique(explode(",",chop($lot ,",")));
                                                //var_dump($lots);
                                                $issue_rtn_qnt = 0;
                                                foreach ($lots as $lot)
                                                {
                                                   
                                                    $issue_numberr = $issue_data_arr1[$row['btb_id']][$row['yarn_composition']][$lot]["issue_number"];
                                                    //$issue_numbers = array_unique(explode(",",chop($issue_number ,",")));
                                                    $issue_numbers = explode(",",chop($issue_numberr ,","));
                                                    //var_dump($issue_number);
                                                    foreach ($issue_numbers as $issue_num)
                                                    {
                                                        //var_dump($issue_num);
                                                        $prod_id = $issue_number_arr[$issue_num]["prod_id"];
                                                        $prod_ids = explode(",",chop($prod_id ,","));
                                                        //var_dump($prod_ids);
                                                        foreach ($prod_ids as $v_prod_id)
                                                        {
                                                            //var_dump($v_prod_id);
                                                            //$issue_rtn_qnt +=  $issue_return_qnty_arr[$issue_num][$v_prod_id];
                                                            $issue_rtn_qnt +=  $issue_return_qnty_arr[$issue_num][$v_prod_id][$row['yarn_composition']][$lot];
                                                        }
                                                    }
                                                }

                                                // $issue_number = $issue_data_arr[$row['btb_id']][$row['yarn_composition']][$rec_data_arr1[$pi_key][$row['yarn_composition']]['lot']]["issue_number"];

                                                // $prod_id = $issue_data_arr[$row['btb_id']][$row['yarn_composition']][$rec_data_arr1[$pi_key][$row['yarn_composition']]['lot']]["prod_id"];

                                                // $issue_rtn_qnt =  $issue_return_qnty_arr[$issue_number][$prod_id];

                                                echo  number_format($issue_rtn_qnt,2,'.','');
                                            ?>&nbsp;
                                        </td>
                                        <td width="90" class="wrd_brk" align="right" title="( Receive Qty+Yarn Issue Return )">
                                            <?
                                            $tot_rcv = ($rcvQnty+$issue_rtn_qnt);
                                            echo  number_format($tot_rcv,2,'.','');
                                            ?>&nbsp;
                                        </td>
                                    
                                        <td width="90" class="wrd_brk" align="right">
                                            <?
                                            
                                            $lot = $rec_data_arr1[$pi_key][$row['yarn_composition']]['lot'];
                                            $lots = array_unique(explode(",",chop($lot ,",")));
                                            //var_dump($lots);
                                            $issueQty = 0;
                                            foreach ($lots as $lot)
                                            {
                                                $issueQty += $issue_data_arr1[$row['btb_id']][$row['yarn_composition']][$lot]["qnty"]; 
                                            }
                                                //echo $row['yarn_composition'];
                                                // $issueQty = $issue_data_arr[$row['btb_id']][$row['yarn_composition']][$rec_data_arr[$pi_key][$row['yarn_composition']]['lot']]["qnty"]; 
                                                //$issueQty = $issue_data_arr[13441 ]['26s CMIA Cotton 100 Combed GREY']['R91-0350']["qnty"]; 
                                            echo number_format($issueQty,2,'.',''); 
                                            ?>&nbsp;
                                        </td>
                                        <td width="90" class="wrd_brk" align="right">
                                            <?
                                            $rcv_mrr = $rec_data_arr[$pi_key][$row['yarn_composition']]['mrr_number'];
                                            $rcv_mrrs = array_unique(explode(",",chop($rcv_mrr ,",")));
                                           // var_dump( $rcv_mrrs);
                                           $rcvRtnQnty=0;
                                            foreach ($rcv_mrrs as $row)
                                            {
                                                $data = explode('**',$row);
                                                //echo $data[0].'<br>';
                                                $rcvRtnQnty += $receive_return_arr[$data[0]][$data[1]]["qnty"]; 
                                            }
                                            echo number_format($rcvRtnQnty,2,'.','');
                                            ?>&nbsp;
                                        </td>
                                        <td width="90" class="wrd_brk" align="right" title="( Yarn Issue+Receive Return )">
                                            <?
                                            $tot_issue = ($issueQty+$rcvRtnQnty);
                                            echo number_format( $tot_issue,2,'.','');
                                            ?>&nbsp;
                                        </td>
                                        <td width="90" class="wrd_brk" align="right" title="( Total Receive-Total Issue )">
                                            <?
                                                $balance = ($tot_rcv-$tot_issue);
                                                echo number_format( $balance,2,'.','');
                                            ?>&nbsp;
                                        </td>
                                        <td width="" class="wrd_brk" align="right" title="( L/C Qty-Receive Qty+Receive Return )">
                                            <?
                                           
                                            $pipe_line = ($lc_qnty-$rcvQnty)+$rcvRtnQnty;
                                          
                                          
                                            if(number_format($pipe_line,2,'.','')>0.00 )
                                            {
                                                echo number_format($pipe_line,2,'.','');
                                            } 
                                            else
                                            {
                                                echo '0.00';
                                            }
                                            ?>&nbsp;
                                        </td>
                                    </tr>

                                    <?
                                  

                                    $lc_tot_qnty += $lc_qnty;
                                    $flc_tot_qnty += $lc_qnty;
                                    $blc_tot_qnty += $lc_qnty;
                                    $glc_tot_qnty += $lc_qnty;

                                    $lc_tot_rcv += $rcvQnty;
                                    $file_tot_rcv += $rcvQnty;
                                    $buyer_tot_rcv += $rcvQnty;
                                    $g_tot_rcv += $rcvQnty;

                                    $lc_tot_iss_rtn += $issue_rtn_qnt;
                                    $ftot_iss_rtn += $issue_rtn_qnt;
                                    $btot_iss_rtn += $issue_rtn_qnt;
                                    $g_tot_iss_rtn += $issue_rtn_qnt;

                                    $lc_ttot_rcv += $tot_rcv;
                                    $fttot_rcv += $tot_rcv;
                                    $bttot_rcv += $tot_rcv;
                                    $gttot_rcv += $tot_rcv;

                                    $lc_tissueQty += $issueQty;
                                    $ftissueQty += $issueQty;
                                    $btissueQty += $issueQty;
                                    $gtissueQty += $issueQty;

                                    $lc_trcvRtnQnty += $rcvRtnQnty;
                                    $ftrcvRtnQnty += $rcvRtnQnty;
                                    $btrcvRtnQnty += $rcvRtnQnty;
                                    $gtrcvRtnQnty += $rcvRtnQnty;

                                    $lc_ttot_issue += $tot_issue;
                                    $fttot_issue += $tot_issue;
                                    $bttot_issue += $tot_issue;
                                    $gttot_issue += $tot_issue;

                                    $lc_tbalance += $balance;
                                    $ftbalance += $balance;
                                    $btbalance += $balance;
                                    $gtbalance += $balance;

                                    if(number_format($pipe_line,2,'.','')>0.00 )
                                    {
                                        $lc_tpipe_line += $pipe_line;
                                        $ftpipe_line += $pipe_line;
                                        $btpipe_line += $pipe_line;
                                        $gtpipe_line += $pipe_line;
                                    } 
                                   
                                   
                                }

                            }

                            ?>
                            <tr bgcolor="#e5e7e9">
                                <td align="right" >&nbsp;</td>
                                <td align="right" colspan="3">&nbsp;</td>
                                <td align="center"><b>LC Total : </b>&nbsp;</td>
                                <td align="right"><? echo number_format($lc_tot_qnty,2,'.','');?>&nbsp;</td>
                                <td align="right"><? echo  number_format($lc_tot_rcv,2,'.','');?>&nbsp;</td>
                                <td align="right"><? echo number_format($lc_tot_iss_rtn,2,'.','');?>&nbsp;</td>
                                <td align="right"><? echo number_format($lc_ttot_rcv,2,'.','');?>&nbsp;</td>
                                <td align="right"><? echo number_format($lc_tissueQty,2,'.','');?>&nbsp;</td>
                                <td align="right"><? echo number_format($lc_trcvRtnQnty,2,'.','');?>&nbsp;</td>
                                <td align="right"><? echo number_format($lc_ttot_issue,2,'.','');?>&nbsp;</td>
                                <td align="right"><? echo number_format($lc_tbalance,2,'.','');?>&nbsp;</td>
                                <td align="right">
                                    <? 
                                    if(number_format($lc_tpipe_line,2,'.','')>0.00 )
                                    {
                                        echo number_format($lc_tpipe_line,2,'.','');
                                    } 
                                    else
                                    {
                                        echo '0.00';
                                    }
                                    ?>
                                    &nbsp;</td>
                            </tr>

                            <?

                        }
                        
                            ?>
                            <tr bgcolor="#cacfd2">
                                <td align="right" >&nbsp;</td>
                                <td align="right" colspan="4">&nbsp;</td>
                                <td align="center" ><b>File Total : </b>&nbsp;</td>
                                <td align="right"><?php echo number_format($flc_tot_qnty,2,'.','');?>&nbsp;</td>
                                <td align="right"><? echo  number_format($file_tot_rcv,2,'.','');?>&nbsp;</td>
                                <td align="right"><? echo number_format($ftot_iss_rtn,2,'.','');?>&nbsp;</td>
                                <td align="right"><? echo number_format($fttot_rcv,2,'.','');?>&nbsp;</td>
                                <td align="right"><? echo number_format($ftissueQty,2,'.','');?>&nbsp;</td>
                                <td align="right"><? echo number_format($ftrcvRtnQnty,2,'.','');?>&nbsp;</td>
                                <td align="right"><? echo number_format($fttot_issue,2,'.','');?>&nbsp;</td>
                                <td align="right"><? echo number_format($ftbalance,2,'.','');?>&nbsp;</td>
                                <td align="right">
                                    <? 
                                    
                                    if(number_format($ftpipe_line,2,'.','')>0.00 )
                                    {
                                        echo number_format($ftpipe_line,2,'.','');
                                    } 
                                    else
                                    {
                                        echo '0.00';
                                    }?>
                                    &nbsp;</td>
                            </tr>

                            <?
                            
                    }
                    
                    ?>
                    <tr bgcolor="#bdc3c7">
                        <td align="right" colspan="7">&nbsp;</td>
                        <td align="center" ><b>Buyer Total :</b>&nbsp; </td>
                        <td align="right"><?php echo number_format($blc_tot_qnty,2,'.','');?>&nbsp;</td>
                        <td align="right"><? echo  number_format($buyer_tot_rcv,2,'.','');?>&nbsp;</td>
                        <td align="right"><? echo number_format($btot_iss_rtn,2,'.','');?>&nbsp;</td>
                        <td align="right"><? echo number_format($bttot_rcv,2,'.','');?>&nbsp;</td>
                        <td align="right"><? echo number_format($btissueQty,2,'.','');?>&nbsp;</td>
                        <td align="right"><? echo number_format($btrcvRtnQnty,2,'.','');?>&nbsp;</td>
                        <td align="right"><? echo number_format($bttot_issue,2,'.','');?>&nbsp;</td>
                        <td align="right"><? echo number_format($btbalance,2,'.','');?>&nbsp;</td>
                        <td align="right"><?
                         if(number_format($btpipe_line,2,'.','')>0.00 )
                         {
                             echo number_format($btpipe_line,2,'.','');
                         } 
                         else
                         {
                             echo '0.00';
                         }
                        
                         ?>&nbsp;</td>
                    </tr>

                    <?
                      $i++;
                }

                ?>

                <tfoot>
                    <tr bgcolor="#a6acaf">
                        <td colspan="7" align="right">&nbsp;</td>
                        <td style="font-size:16px;text-align:center;font-weight:bold">Grand Total : </td>
                        <td width="90" align="right"><?php echo number_format($glc_tot_qnty,2,'.','');?>&nbsp;</td>
                        <td width="90" align="right"><? echo number_format($g_tot_rcv,2,'.','');?>&nbsp;</td>
                        <td width="90" align="right"><? echo number_format($g_tot_iss_rtn,2,'.','');?>&nbsp;</td>
                        <td width="90" align="right"><? echo number_format($gttot_rcv,2,'.','');?>&nbsp;</td>
                        <td width="90" align="right"><? echo number_format($gtissueQty,2,'.','');?>&nbsp;</td>
                        <td width="90" align="right"><? echo number_format($gtrcvRtnQnty,2,'.','');?>&nbsp;</td>
                        <td width="90" align="right"><? echo number_format($gttot_issue,2,'.','');?>&nbsp;</td>
                        <td width="90" align="right"><? echo number_format($gtbalance,2,'.','');?>&nbsp;</td>
                        <td  align="right">
                            <?
                            if(number_format($gtpipe_line,2,'.','')>0.00 )
                            {
                                echo number_format($gtpipe_line,2,'.','');
                            } 
                            else
                            {
                                echo '0.00';
                            }
                             ?>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
            </div>
        </div>
    </fieldset>
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

if($action=="report_generate2")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_buyer=str_replace("'","",$cbo_buyer_id);
	$cbo_file_year=str_replace("'","",$cbo_file_year);
	$txt_internal_file_no_arr=explode(",",str_replace("'","",$txt_internal_file_no));
	$txt_internal_file_no="";
	foreach($txt_internal_file_no_arr as $file_no)
	{
        if(!empty($file_no))
        {
            $txt_internal_file_no.="'".$file_no."',";
        }
	}
	$txt_internal_file_no=chop($txt_internal_file_no,",");
	//echo $txt_internal_file_no.test;die;

    if ($cbo_buyer != 0)
    {
        $buyer_con = " and a.buyer_name in($cbo_buyer)";
    }

    if ($txt_internal_file_no != '')
    {
        $file_con = " and a.internal_file_no in($txt_internal_file_no)";
    }
	//echo $file_con.test;die;
   

    // for lc---------------
    $sql="SELECT a.internal_file_no, a.export_lc_no, a.buyer_name, e.pi_number, e.supplier_id, d.id as pi_dtls_id, d.pi_id, d.color_id, d.count_name, d.yarn_type, d.net_pi_rate, d.quantity as pi_qty, d.net_pi_amount, d.yarn_composition_item1 as yarn_composition_id, d.yarn_composition_percentage2, f.bank_code, f.lc_year, f.lc_category, f.lc_serial, e.remarks,f.lc_date,f.id as btb_id
    FROM com_export_lc a, com_btb_export_lc_attachment b, com_btb_lc_pi c, com_pi_item_details d, com_pi_master_details e, com_btb_lc_master_details f
    WHERE a.id=b.lc_sc_id and b.import_mst_id=c.com_btb_lc_master_details_id and b.is_lc_sc=0 and c.pi_id=d.pi_id and c.pi_id=e.id and b.import_mst_id=f.id and a.status_active=1 and  a.is_deleted=0 and b.status_active=1 and  b.is_deleted=0 and c.status_active=1 and  c.is_deleted=0 and d.status_active=1 and  d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and  f.is_deleted=0 and e.item_category_id=1 and
    f.item_category_id=1 and a.beneficiary_name=$cbo_company_name  $buyer_con and a.lc_year='$cbo_file_year' $file_con";
    //echo $sql;
    $lcresult=sql_select($sql);

    $buyer_data_arr = array();
    $piIdChk = array();
    $piIdChk1 = array();
    $btbIdChk = array();
    $btbIdChk1 = array();
    $piIdArr = array();
    $btbIdArr = array();
     
    foreach($lcresult as $row)
    {
        $yarn_composition=$count_arr[$row[csf('count_name')]].' '.$composition[$row[csf('yarn_composition_id')]].' 100 '.$yarn_type[$row[csf('yarn_type')]].' '.$color_arr[$row[csf('color_id')]];

        $buyer_data_arr[$row[csf('buyer_name')]][$row[csf('internal_file_no')]][$row[csf('btb_id')]][$yarn_composition]['buyer_name']=$row[csf('buyer_name')];
        $buyer_data_arr[$row[csf('buyer_name')]][$row[csf('internal_file_no')]][$row[csf('btb_id')]][$yarn_composition]['internal_file_no']=$row[csf('internal_file_no')];
        $buyer_data_arr[$row[csf('buyer_name')]][$row[csf('internal_file_no')]][$row[csf('btb_id')]][$yarn_composition]['btb_id']=$row[csf('btb_id')];
        $buyer_data_arr[$row[csf('buyer_name')]][$row[csf('internal_file_no')]][$row[csf('btb_id')]][$yarn_composition]['yarn_composition']=$yarn_composition;
        $buyer_data_arr[$row[csf('buyer_name')]][$row[csf('internal_file_no')]][$row[csf('btb_id')]][$yarn_composition]['pi_id'].=$row[csf('pi_id')].',';
        $buyer_data_arr[$row[csf('buyer_name')]][$row[csf('internal_file_no')]][$row[csf('btb_id')]][$yarn_composition]['btb_lc']=$row[csf('bank_code')].$row[csf('lc_year')].$row[csf('lc_category')].$row[csf('lc_serial')];
        $buyer_data_arr[$row[csf('buyer_name')]][$row[csf('internal_file_no')]][$row[csf('btb_id')]][$yarn_composition]['lc_date']=$row[csf('lc_date')];
        $buyer_data_arr[$row[csf('buyer_name')]][$row[csf('internal_file_no')]][$row[csf('btb_id')]][$yarn_composition]['count_name']=$row[csf('count_name')];
        $buyer_data_arr[$row[csf('buyer_name')]][$row[csf('internal_file_no')]][$row[csf('btb_id')]][$yarn_composition]['yarn_type']=$row[csf('yarn_type')];
        $buyer_data_arr[$row[csf('buyer_name')]][$row[csf('internal_file_no')]][$row[csf('btb_id')]][$yarn_composition]['export_lc_no']=$row[csf('export_lc_no')];



		if($pi_dtls_check[$row[csf('pi_dtls_id')]]=="")
		{
			$pi_dtls_check[$row[csf('pi_dtls_id')]]=$row[csf('pi_dtls_id')];
			$qtyArr[$row[csf('buyer_name')]][$row[csf('internal_file_no')]][$row[csf('btb_id')]][$yarn_composition]+=$row[csf('pi_qty')];
		}
       

        if($piIdChk[$row[csf('pi_id')]] == "")
        {
            $piIdChk[$row[csf('pi_id')]] = $row[csf('pi_id')];
            array_push($piIdArr,$row[csf('pi_id')]);
        }

        if($btbIdChk[$row[csf('btb_id')]] == "")
        {
            $btbIdChk[$row[csf('btb_id')]] = $row[csf('btb_id')];
            array_push($btbIdArr,$row[csf('btb_id')]);
        }
    
    }
    unset($lcresult);
   /*  echo "<pre>";
    print_r($piIdArr); */


    // for sc---------------
    $sc_sql="SELECT a.internal_file_no, a.contract_no as export_lc_no, a.buyer_name, e.pi_number, e.supplier_id, d.id as pi_dtls_id, d.pi_id, d.color_id, d.count_name, d.yarn_type, d.net_pi_rate, d.quantity as pi_qty, d.net_pi_amount, d.yarn_composition_item1 as yarn_composition_id, d.yarn_composition_percentage2, f.bank_code, f.lc_year, f.lc_category, f.lc_serial, f.remarks, f.lc_date,f.id as btb_id
    FROM com_sales_contract a, com_btb_export_lc_attachment b, com_btb_lc_pi c, com_pi_item_details	 d, com_pi_master_details e, com_btb_lc_master_details f
    WHERE a.id=b.lc_sc_id and b.import_mst_id=c.com_btb_lc_master_details_id and b.is_lc_sc=1 and c.pi_id=d.pi_id and c.pi_id=e.id and b.import_mst_id=f.id and a.status_active=1 and  a.is_deleted=0 and b.status_active=1 and  b.is_deleted=0 and c.status_active=1 and  c.is_deleted=0 and d.status_active=1 and  d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and  f.is_deleted=0 and e.item_category_id=1 and f.item_category_id=1 and  a.beneficiary_name=$cbo_company_name $buyer_con and a.sc_year='$cbo_file_year' $file_con";
    //echo $sc_sql;
    $scresult=sql_select($sc_sql);

    foreach($scresult as $row)
    {
        $yarn_composition=$count_arr[$row[csf('count_name')]].' '.$composition[$row[csf('yarn_composition_id')]].' 100 '.$yarn_type[$row[csf('yarn_type')]].' '.$color_arr[$row[csf('color_id')]];
        
        $buyer_data_arr[$row[csf('buyer_name')]][$row[csf('internal_file_no')]][$row[csf('btb_id')]][$yarn_composition]['buyer_name']=$row[csf('buyer_name')];
        $buyer_data_arr[$row[csf('buyer_name')]][$row[csf('internal_file_no')]][$row[csf('btb_id')]][$yarn_composition]['internal_file_no']=$row[csf('internal_file_no')];
        $buyer_data_arr[$row[csf('buyer_name')]][$row[csf('internal_file_no')]][$row[csf('btb_id')]][$yarn_composition]['btb_id']=$row[csf('btb_id')];
        $buyer_data_arr[$row[csf('buyer_name')]][$row[csf('internal_file_no')]][$row[csf('btb_id')]][$yarn_composition]['yarn_composition']=$yarn_composition;
        $buyer_data_arr[$row[csf('buyer_name')]][$row[csf('internal_file_no')]][$row[csf('btb_id')]][$yarn_composition]['pi_id'].=$row[csf('pi_id')].',';
        $buyer_data_arr[$row[csf('buyer_name')]][$row[csf('internal_file_no')]][$row[csf('btb_id')]][$yarn_composition]['btb_lc']=$row[csf('bank_code')].$row[csf('lc_year')].$row[csf('lc_category')].$row[csf('lc_serial')];
        $buyer_data_arr[$row[csf('buyer_name')]][$row[csf('internal_file_no')]][$row[csf('btb_id')]][$yarn_composition]['lc_date']=$row[csf('lc_date')];
        $buyer_data_arr[$row[csf('buyer_name')]][$row[csf('internal_file_no')]][$row[csf('btb_id')]][$yarn_composition]['count_name']=$row[csf('count_name')];
        $buyer_data_arr[$row[csf('buyer_name')]][$row[csf('internal_file_no')]][$row[csf('btb_id')]][$yarn_composition]['yarn_type']=$row[csf('yarn_type')];
        $buyer_data_arr[$row[csf('buyer_name')]][$row[csf('internal_file_no')]][$row[csf('btb_id')]][$yarn_composition]['export_lc_no']=$row[csf('export_lc_no')];
     	
		if($pi_dtls_check[$row[csf('pi_dtls_id')]]=="")
		{
			$pi_dtls_check[$row[csf('pi_dtls_id')]]=$row[csf('pi_dtls_id')];
			$qtyArr[$row[csf('buyer_name')]][$row[csf('internal_file_no')]][$row[csf('btb_id')]][$yarn_composition]+=$row[csf('pi_qty')];
		}
        

        if($piIdChk1[$row[csf('pi_id')]] == "")
        {
            $piIdChk1[$row[csf('pi_id')]] = $row[csf('pi_id')];
            array_push($piIdArr,$row[csf('pi_id')]);
        }

        if($btbIdChk1[$row[csf('btb_id')]] == "")
        {
            $btbIdChk1[$row[csf('btb_id')]] = $row[csf('btb_id')];
            array_push($btbIdArr,$row[csf('btb_id')]);
        }
        
    }
    unset($scresult);
	//echo "<pre>";print_r($buyer_data_arr);die;

    $Rcvsql="SELECT
                a.booking_id as pi_id,
                b.remarks as rec_remarks,
                a.recv_number as mrr_number,
                a.receive_date,
                a.supplier_id,
                b.order_qnty as rec_qty,
                b.order_rate as rec_rate,
                b.order_amount as rec_amount,
                c.product_name_details,
                c.lot
            FROM
                inv_receive_master a,
                inv_transaction b,
                product_details_master c
            WHERE
                a.id=b.mst_id and
                a.entry_form=1 and
                a.receive_basis=1 and
                b.prod_id=c.id and b.item_category=1 and c.item_category_id=1 and
                a.company_id=$cbo_company_name and
                a.status_active=1 and
                a.is_deleted=0 and
                b.status_active=1 and
                b.is_deleted=0 and
                c.status_active=1 and
                c.is_deleted=0
                ".where_con_using_array($piIdArr,0,'a.booking_id')."
            ";
    //echo $Rcvsql;
    $RcRresult=sql_select($Rcvsql);
    $mrrArr = array();
    $rec_data_arr = array();
    foreach($RcRresult as $row)
    {
        $rec_data_arr[$row[csf('pi_id')]][$row[csf('product_name_details')]]['rec_qty'] +=$row[csf('rec_qty')]; 
        $rec_data_arr[$row[csf('pi_id')]][$row[csf('product_name_details')]]['lot'] =$row[csf('lot')];  
        $rec_data_arr1[$row[csf('pi_id')]][$row[csf('product_name_details')]]['lot'] .=$row[csf('lot')].',';  
        
        $rec_data_arr[$row[csf('pi_id')]][$row[csf('product_name_details')]]['mrr_number'] .=$row[csf('mrr_number')].'**'.$row[csf('lot')].','; 

        if($mrrChk[$row[csf('mrr_number')]] == "")
        {
            $mrrChk[$row[csf('mrr_number')]] = $row[csf('mrr_number')];
            array_push($mrrArr,$row[csf('mrr_number')]);
        }
    }
    //var_dump( $rec_data_arr);
    unset($RcRresult);

    $sqlRcvRtn = "SELECT b.id, b.issue_number, a.order_rate,a.cons_quantity,a.cons_rate,a.order_qnty,b.received_mrr_no,c.lot,a.id as trans_id,a.transaction_type,a.btb_lc_id,c.product_name_details, c.id as prod_id
    from inv_transaction a, inv_issue_master b, product_details_master c
    where a.mst_id = b.id and b.company_id=$cbo_company_name and a.transaction_type in(3) and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 and a.prod_id = c.id  and a.item_category=1 and c.item_category_id=1  ".where_con_using_array($mrrArr,1,'b.received_mrr_no')."";
    //echo $sqlRcvRtn;
    $resultRcvRtn=sql_select($sqlRcvRtn); $receive_return_arr = array(); $transRcvIdChk =array(); 
    foreach($resultRcvRtn as $row)
    {
        if($transRcvIdChk[$row[csf('trans_id')]] == "")
        {
            $transRcvIdChk[$row[csf('trans_id')]] = $row[csf('trans_id')];
            $receive_return_arr[$row[csf('received_mrr_no')]][$row[csf('lot')]]["qnty"] += $row[csf('cons_quantity')];
        }
    }
    unset($resultRcvRtn);


    $sqlIssue = "SELECT b.id, b.issue_number, a.order_rate,a.cons_quantity,a.cons_rate,a.order_qnty,b.received_mrr_no,c.lot,a.id as trans_id,a.transaction_type,a.btb_lc_id,c.product_name_details, c.id as prod_id
    from inv_transaction a, inv_issue_master b, product_details_master c
    where a.mst_id = b.id and b.company_id=$cbo_company_name and a.transaction_type in(2) and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 and a.prod_id = c.id  and a.item_category=1 and c.item_category_id=1 ".where_con_using_array($btbIdArr,0,'a.btb_lc_id')." order by c.id desc";
    //echo $sqlIssue;
    $resultIssue=sql_select($sqlIssue); $transIssIdChk =array(); $issue_no_arr = array();
    foreach($resultIssue as $row)
    {
        if($transIssIdChk[$row[csf('trans_id')]] == "")
        {
            $transIssIdChk[$row[csf('trans_id')]] = $row[csf('trans_id')];
            $issue_data_arr[$row[csf('btb_lc_id')]][$row[csf('product_name_details')]][$row[csf('lot')]]["qnty"] += $row[csf('cons_quantity')];

            $issue_data_arr[$row[csf('btb_lc_id')]][$row[csf('product_name_details')]][$row[csf('lot')]]["issue_number"] = $row[csf('issue_number')];
            $issue_data_arr[$row[csf('btb_lc_id')]][$row[csf('product_name_details')]][$row[csf('lot')]]["prod_id"] = $row[csf('prod_id')];

            $issue_data_arr1[$row[csf('btb_lc_id')]][$row[csf('product_name_details')]][$row[csf('lot')]]["qnty"] += $row[csf('cons_quantity')];
            $issue_data_arr1[$row[csf('btb_lc_id')]][$row[csf('product_name_details')]][$row[csf('lot')]]["issue_number"] .= $row[csf('issue_number')].',';
            $issue_number_arr[$row[csf('issue_number')]]["prod_id"] .= $row[csf('prod_id')].',';
        
            array_push($issue_no_arr, $row[csf('issue_number')]);
        }
    }
    unset($resultIssue);
    //var_dump($issue_number_arr);

    $issue_return_res = sql_select("SELECT a.recv_number,a.booking_no, b.cons_quantity, c.issue_number,b.prod_id, b.id as trans_id,d.product_name_details,d.lot
    from inv_receive_master a, inv_transaction b, inv_issue_master c,product_details_master d where a.id = b.mst_id and a.issue_id = c.id and b.transaction_type = 4 and b.item_category = 1 and b.prod_id=d.id and d.item_category_id=1 and a.entry_form = 9 and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 ".where_con_using_array($issue_no_arr,1,'c.issue_number')." order by b.prod_id desc");
    $transRtnIdChk = array();
    foreach ($issue_return_res as $val) 
    {
        if($transRtnIdChk[$val[csf("trans_id")]]=="")
        {
            $transRtnIdChk[$val[csf("trans_id")]] = $val[csf("trans_id")];
            // $issue_return_qnty_arr[$val[csf("issue_number")]][$val[csf("prod_id")]][$val[csf("product_name_details")]]+= $val[csf("cons_quantity")];
            $issue_return_qnty_arr[$val[csf("issue_number")]][$val[csf("prod_id")]][$val[csf("product_name_details")]][$val[csf("lot")]]+= $val[csf("cons_quantity")];
        }          
    }
    unset($issue_return_res);
	//echo $issue_return_qnty_arr['AKDL-YIS-22-09839'][330236]['24s Organic Cotton 100 Combed GREY'].testee;
    $buyer_count=array();
    $file_count=array();
    $lc_count=array();
    foreach($buyer_data_arr as $buyer_key=>$buyer_data)
    {   
        foreach ($buyer_data as $file_no_key => $file_data) 
        {
            foreach($file_data as $lc_key=>$lc_data_arr)
            {
                foreach($lc_data_arr as $composition=> $row)
                {
                    $buyer_count[$buyer_key]++;
                    $file_count[$buyer_key][$file_no_key]++;
                    $lc_count[$buyer_key][$file_no_key][$lc_key]++;
                }
            }
        }
    }

    $b_data_count=array();
    foreach ($lc_count as $buyer_id => $buyer_data) 
    {
        foreach ($buyer_data as $file_key => $file_data) 
        {
            foreach ($file_data as $lc_key => $lc_data) 
            {
                $b_data_count[$buyer_id]++;
            }
        }
    }

    ob_start();
    ?>
    <style>
        .wrd_brk{word-break: break-all;word-wrap: break-word;}          
    </style>

    <fieldset style="width:1530px">
        <div style="width:100%; margin-left:10px;" align="left">
            <table width="1510" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                <thead>
                    <tr>
                        <th width="35">SL</th>
                        <th width="100">Buyer</th>
                        <th width="80">File No</th>
                        <th width="100">L/C No</th>
                        <th width="70">L/C Date</th>
                        <th width="60">Yarn Count</th>
                        <th width="140">Composition</th>
                        <th width="90">Yarn Type</th>
                        <th width="90">L/C Qty</th>
                        <th width="90">Receive Qty</th>
                        <th width="90">Yarn Issue<br> Return</th>
                        <th width="90">Total  Receive</th>
                        <th width="90">Yarn Issue</th>
                        <th width="90">Receive Return</th>
                        <th width="90">Total Issue</th>
                        <th width="90">Balance</th>
                        <th width="">Pipe Line</th>
                    </tr>
                </thead>
            </table>
            <!-- style="max-height:350px; overflow-y:auto; width:1667px;" -->
            <div>
            <table width="1510" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                <? 	$i=1;

                $glc_tot_qnty=0;$g_tot_rcv=0; $g_tot_iss_rtn=0;$gttot_rcv=0;$gtissueQty=0;$gtrcvRtnQnty=0;$gttot_issue=0;$gtbalance=0;$gtpipe_line=0;
                
                foreach($buyer_data_arr as $buyer_key=>$buyer_data)
                {   
                    $blc_tot_qnty=0;$buyer_tot_rcv =0; $btot_iss_rtn=0;$bttot_rcv=0;$btissueQty=0;$btrcvRtnQnty=0; $bttot_issue-0;$btbalance=0; $btpipe_line=0;
                    foreach ($buyer_data as $file_no_key => $file_data) 
                    {
                        $flc_tot_qnty=0; $file_tot_rcv=0;$ftot_iss_rtn=0;$fttot_rcv=0; $ftissueQty=0; $ftrcvRtnQnty=0; $fttot_issue=0;$ftbalance=0;$ftpipe_line=0;
                        foreach($file_data as $lc_key=>$lc_data_arr)
                        {
                            $lc_tot_qnty=0;$lc_tot_rcv=0;$lc_tot_iss_rtn=0; $lc_ttot_rcv=0;$lc_tissueQty=0;$lc_trcvRtnQnty=0;$lc_ttot_issue=0;$lc_tbalance=0;$lc_tpipe_line=0;
                            
                            foreach($lc_data_arr as $composition=> $row)
                            {
                                $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";

                                $buyer_span = $buyer_count[$buyer_key]+$b_data_count[$buyer_key]+count($file_count[$buyer_key]);
                                $file_span  = $file_count[$buyer_key][$file_no_key]+count($lc_count[$buyer_key][$file_no_key]);
                                $lc_span    = $lc_count[$buyer_key][$file_no_key][$lc_key];

                                ?>
                                <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                                    
                                <?
                                if(!in_array($buyer_key,$buyer_chk))
                                {
                                    $buyer_chk[]=$buyer_key;
                                    ?>
                                        <td width="35" class="wrd_brk" align="center" rowspan="<? echo $buyer_span ;?>"  valign="middle" ><? echo $i; ?>&nbsp;</td>
                                        <td width="100" class="wrd_brk" rowspan="<? echo $buyer_span ;?>" valign="middle" align="center"><p><? echo $buyer_arr[$buyer_key]; ?>&nbsp;</p></td>
                                    <?
                                }

                                if(!in_array($buyer_key."**".$file_no_key,$file_chk))
                                {
                                    $file_chk[]=$buyer_key."**".$file_no_key;
                                    ?>
                                        <td width="80" rowspan="<? echo $file_span;?>" valign="middle" class="wrd_brk" align="center"><? echo $file_no_key; ?>&nbsp;</td>
                                    <?
                                }

                                if(!in_array($buyer_key."**".$file_no_key."**".$lc_key,$lc_chk))
                                {
                                    $lc_chk[]=$buyer_key."**".$file_no_key."**".$lc_key;
                                    ?>
                                        <td width="100" rowspan="<? echo $lc_span;?>" valign="middle" class="wrd_brk" align="center"><? echo $row['btb_lc']; ?>&nbsp;</td>
                                        <td width="70" rowspan="<? echo $lc_span;?>" valign="middle" class="wrd_brk" align="center"><? echo change_date_format($row['lc_date']); ?>&nbsp;</td>
                                    <?
                                }
                            
                            ?>
                                    <td width="60" class="wrd_brk" align="center"><? echo $count_arr[$row['count_name']]; ?>&nbsp;</td>
                                    <td width="140" class="wrd_brk" align="center"><? echo $row['yarn_composition']; ?>&nbsp;</td>
                                    <td width="90" class="wrd_brk" align="center"><? echo $yarn_type[$row['yarn_type']]; ?>&nbsp;</td>
                                    <td width="90" class="wrd_brk"  align="right">
                                        <?
                                        $lc_qnty = $qtyArr[$buyer_key][$file_no_key][$lc_key][$row['yarn_composition']];
                                        echo number_format($lc_qnty,2,'.','');   
                                        ?>&nbsp;
                                    </td>
                                    <td width="90" class="wrd_brk" align="right" >
                                        <? 
                                        $pi_ids = array_unique(explode(",",chop($row['pi_id'] ,",")));
                                       
                                        $rcvQnty = 0;
                                        foreach ($pi_ids as $pi_id) 
                                        {
                                            $rcvQnty += $rec_data_arr[$pi_id][$row['yarn_composition']]['rec_qty'];
                                        }
                                        echo number_format($rcvQnty,2,'.','');
                                        ?>&nbsp;
                                    </td>
                                    <td width="90" class="wrd_brk" align="right" >
                                        <?
											
                                            $pi_ids = array_unique(explode(",",chop($row['pi_id'] ,",")));
                                            $lot ='';
                                            foreach ($pi_ids as $pi_id) 
                                            {
                                                $lot .= $rec_data_arr1[$pi_id][$row['yarn_composition']]['lot'];
                                            }
                                            $issue_rtn_qnt = 0;
                                              
                                            $lots = array_unique(explode(",",chop($lot ,",")));
                                            //var_dump($lots);
                                        	
											$issue_rtn_qnt = 0;
                                            foreach ($lots as $lot)
                                            {
                                                $issue_numberr = $issue_data_arr1[$row['btb_id']][$row['yarn_composition']][$lot]["issue_number"];
                                                $issue_numbers = explode(",",chop($issue_numberr ,","));
                                                //var_dump($issue_number);
                                                foreach ($issue_numbers as $issue_num)
                                                {
                                                    $prod_id = $issue_number_arr[$issue_num]["prod_id"];
                                                    $prod_ids = explode(",",chop($prod_id ,","));
                                                	
                                                    foreach ($prod_ids as $v_prod_id)
                                                    {
                                                       // $issue_rtn_qnt +=  $issue_return_qnty_arr[$issue_num][$v_prod_id][$row['yarn_composition']];
                                                       $issue_rtn_qnt +=  $issue_return_qnty_arr[$issue_num][$v_prod_id][$row['yarn_composition']][$lot];
                                                    }
                                                }
                                            }
                                           
                                            echo  number_format($issue_rtn_qnt,2,'.','');
                                        ?>&nbsp;
                                    </td>
                                    <td width="90" class="wrd_brk" align="right" title="( Receive Qty+Yarn Issue Return )">
                                        <?
                                        $tot_rcv = ($rcvQnty+$issue_rtn_qnt);
                                        echo  number_format($tot_rcv,2,'.','');
                                        ?>&nbsp;
                                    </td>
                                    <td width="90" class="wrd_brk" align="right">
                                        <?
                                        $pi_ids = array_unique(explode(",",chop($row['pi_id'] ,",")));
                                        $lot = '';
                                        foreach ($pi_ids as $pi_id) 
                                        {
                                            $lot .= $rec_data_arr1[$pi_id][$row['yarn_composition']]['lot'];
                                        }
                                      
                                        $lots = array_unique(explode(",",chop($lot ,",")));
                                        //var_dump($lots);
                                        $issueQty = 0;
                                        foreach ($lots as $lot)
                                        {
                                            $issueQty += $issue_data_arr1[$row['btb_id']][$row['yarn_composition']][$lot]["qnty"]; 
                                        }
                                        
                                        echo number_format($issueQty,2,'.',''); 
                                        ?>&nbsp;
                                    </td>
                                    <td width="90" class="wrd_brk" align="right">
                                        <?
                                        $pi_ids = array_unique(explode(",",chop($row['pi_id'] ,",")));
                                       
                                        $rcv_mrr = '';
                                        foreach ($pi_ids as $pi_id) 
                                        {
                                            $rcv_mrr .= $rec_data_arr[$pi_id][$row['yarn_composition']]['mrr_number'];
                                        }
                                      
                                        $rcv_mrrs = array_unique(explode(",",chop($rcv_mrr ,",")));
                                        $rcvRtnQnty=0;
                                        foreach ($rcv_mrrs as $row)
                                        {
                                            $data = explode('**',$row);
                                            //echo $data[0].'<br>';
                                            $rcvRtnQnty += $receive_return_arr[$data[0]][$data[1]]["qnty"]; 
                                        }
                                       
                                        echo number_format($rcvRtnQnty,2,'.','');
                                        ?>&nbsp;
                                    </td>
                                    <td width="90" class="wrd_brk" align="right" title="( Yarn Issue+Receive Return )">
                                        <?
                                        $tot_issue = ($issueQty+$rcvRtnQnty);
                                        echo number_format( $tot_issue,2,'.','');
                                        ?>&nbsp;
                                    </td>
                                    <td width="90" class="wrd_brk" align="right" title="( Total Receive-Total Issue )">
                                        <?
                                            $balance = ($tot_rcv-$tot_issue);
                                            echo number_format( $balance,2,'.','');
                                        ?>&nbsp;
                                    </td>
                                    <td width="" class="wrd_brk" align="right" title="( L/C Qty-Receive Qty+Receive Return )">
                                        <?
                                        
                                        $pipe_line = ($lc_qnty-$rcvQnty)+$rcvRtnQnty;
                                        
                                        
                                        if(number_format($pipe_line,2,'.','')>0.00 )
                                        {
                                            echo number_format($pipe_line,2,'.','');
                                        } 
                                        else
                                        {
                                            echo '0.00';
                                        }
                                        ?>&nbsp;
                                    </td>

                                            
                                </tr>

                                <?
                                $lc_tot_qnty += $lc_qnty;
                                $flc_tot_qnty += $lc_qnty;
                                $blc_tot_qnty += $lc_qnty;
                                $glc_tot_qnty += $lc_qnty;

                                $lc_tot_rcv += $rcvQnty;
                                $file_tot_rcv += $rcvQnty;
                                $buyer_tot_rcv += $rcvQnty;
                                $g_tot_rcv += $rcvQnty;

                                $lc_tot_iss_rtn += $issue_rtn_qnt;
                                $ftot_iss_rtn += $issue_rtn_qnt;
                                $btot_iss_rtn += $issue_rtn_qnt;
                                $g_tot_iss_rtn += $issue_rtn_qnt;

                                $lc_ttot_rcv += $tot_rcv;
                                $fttot_rcv += $tot_rcv;
                                $bttot_rcv += $tot_rcv;
                                $gttot_rcv += $tot_rcv;

                                $lc_tissueQty += $issueQty;
                                $ftissueQty += $issueQty;
                                $btissueQty += $issueQty;
                                $gtissueQty += $issueQty;

                                $lc_trcvRtnQnty += $rcvRtnQnty;
                                $ftrcvRtnQnty += $rcvRtnQnty;
                                $btrcvRtnQnty += $rcvRtnQnty;
                                $gtrcvRtnQnty += $rcvRtnQnty;

                                $lc_ttot_issue += $tot_issue;
                                $fttot_issue += $tot_issue;
                                $bttot_issue += $tot_issue;
                                $gttot_issue += $tot_issue;

                                $lc_tbalance += $balance;
                                $ftbalance += $balance;
                                $btbalance += $balance;
                                $gtbalance += $balance;

                                if(number_format($pipe_line,2,'.','')>0.00 )
                                {
                                    $lc_tpipe_line += $pipe_line;
                                    $ftpipe_line += $pipe_line;
                                    $btpipe_line += $pipe_line;
                                    $gtpipe_line += $pipe_line;
                                }
                            }
                            ?>
                            <tr bgcolor="#e5e7e9">
                                <td align="right" >&nbsp;</td>
                                <td align="right" colspan="3">&nbsp;</td>
                                <td align="center"><b>LC Total : </b>&nbsp;</td>
                                <td align="right"><? echo number_format($lc_tot_qnty,2,'.','');?>&nbsp;</td>
                                <td align="right"><? echo  number_format($lc_tot_rcv,2,'.','');?>&nbsp;</td>
                                <td align="right"><? echo number_format($lc_tot_iss_rtn,2,'.','');?>&nbsp;</td>
                                <td align="right"><? echo number_format($lc_ttot_rcv,2,'.','');?>&nbsp;</td>
                                <td align="right"><? echo number_format($lc_tissueQty,2,'.','');?>&nbsp;</td>
                                <td align="right"><? echo number_format($lc_trcvRtnQnty,2,'.','');?>&nbsp;</td>
                                <td align="right"><? echo number_format($lc_ttot_issue,2,'.','');?>&nbsp;</td>
                                <td align="right"><? echo number_format($lc_tbalance,2,'.','');?>&nbsp;</td>
                                <td align="right">
                                    <? 
                                    if(number_format($lc_tpipe_line,2,'.','')>0.00 )
                                    {
                                        echo number_format($lc_tpipe_line,2,'.','');
                                    } 
                                    else
                                    {
                                        echo '0.00';
                                    }
                                    ?>
                                    &nbsp;</td>
                            </tr>

                            <?
                        }
                        ?>
                        <tr bgcolor="#cacfd2">
                            <td align="right" >&nbsp;</td>
                            <td align="right" colspan="4">&nbsp;</td>
                            <td align="center" ><b>File Total : </b>&nbsp;</td>
                            <td align="right"><?php echo number_format($flc_tot_qnty,2,'.','');?>&nbsp;</td>
                            <td align="right"><? echo  number_format($file_tot_rcv,2,'.','');?>&nbsp;</td>
                            <td align="right"><? echo number_format($ftot_iss_rtn,2,'.','');?>&nbsp;</td>
                            <td align="right"><? echo number_format($fttot_rcv,2,'.','');?>&nbsp;</td>
                            <td align="right"><? echo number_format($ftissueQty,2,'.','');?>&nbsp;</td>
                            <td align="right"><? echo number_format($ftrcvRtnQnty,2,'.','');?>&nbsp;</td>
                            <td align="right"><? echo number_format($fttot_issue,2,'.','');?>&nbsp;</td>
                            <td align="right"><? echo number_format($ftbalance,2,'.','');?>&nbsp;</td>
                            <td align="right">
                                <? 
                                
                                if(number_format($ftpipe_line,2,'.','')>0.00 )
                                {
                                    echo number_format($ftpipe_line,2,'.','');
                                } 
                                else
                                {
                                    echo '0.00';
                                }?>
                                &nbsp;</td>
                        </tr>

                        <?    
                    }
                    ?>
                    <tr bgcolor="#bdc3c7">
                        <td align="right" colspan="7">&nbsp;</td>
                        <td align="center" ><b>Buyer Total :</b>&nbsp; </td>
                        <td align="right"><?php echo number_format($blc_tot_qnty,2,'.','');?>&nbsp;</td>
                        <td align="right"><? echo  number_format($buyer_tot_rcv,2,'.','');?>&nbsp;</td>
                        <td align="right"><? echo number_format($btot_iss_rtn,2,'.','');?>&nbsp;</td>
                        <td align="right"><? echo number_format($bttot_rcv,2,'.','');?>&nbsp;</td>
                        <td align="right"><? echo number_format($btissueQty,2,'.','');?>&nbsp;</td>
                        <td align="right"><? echo number_format($btrcvRtnQnty,2,'.','');?>&nbsp;</td>
                        <td align="right"><? echo number_format($bttot_issue,2,'.','');?>&nbsp;</td>
                        <td align="right"><? echo number_format($btbalance,2,'.','');?>&nbsp;</td>
                        <td align="right"><?
                         if(number_format($btpipe_line,2,'.','')>0.00 )
                         {
                             echo number_format($btpipe_line,2,'.','');
                         } 
                         else
                         {
                             echo '0.00';
                         }
                        
                         ?>&nbsp;</td>
                    </tr>

                    <?
                    $i++;
                }

                ?>

                <tfoot>
                    <tr bgcolor="#a6acaf">
                        <td colspan="7" align="right">&nbsp;</td>
                        <td style="font-size:16px;text-align:center;font-weight:bold">Grand Total : </td>
                        <td width="90" align="right"><?php echo number_format($glc_tot_qnty,2,'.','');?>&nbsp;</td>
                        <td width="90" align="right"><? echo number_format($g_tot_rcv,2,'.','');?>&nbsp;</td>
                        <td width="90" align="right"><? echo number_format($g_tot_iss_rtn,2,'.','');?>&nbsp;</td>
                        <td width="90" align="right"><? echo number_format($gttot_rcv,2,'.','');?>&nbsp;</td>
                        <td width="90" align="right"><? echo number_format($gtissueQty,2,'.','');?>&nbsp;</td>
                        <td width="90" align="right"><? echo number_format($gtrcvRtnQnty,2,'.','');?>&nbsp;</td>
                        <td width="90" align="right"><? echo number_format($gttot_issue,2,'.','');?>&nbsp;</td>
                        <td width="90" align="right"><? echo number_format($gtbalance,2,'.','');?>&nbsp;</td>
                        <td  align="right">
                            <?
                            if(number_format($gtpipe_line,2,'.','')>0.00 )
                            {
                                echo number_format($gtpipe_line,2,'.','');
                            } 
                            else
                            {
                                echo '0.00';
                            }
                             ?>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
            </div>
        </div>
    </fieldset>
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
