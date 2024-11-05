<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_store")
{
	echo create_drop_down( "cbo_store_name", 150, "select a.id, a.store_name from lib_store_location a,lib_store_location_category b  where a.id=b.store_location_id and a.company_id='$data' and b.category_type in(1) and a.status_active=1 and a.is_deleted=0 order by a.store_name","id,store_name", 1, "-- Select Store --", 0, "" );
	exit();
	//select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$data[0] and b.category_type=$data[1] order by a.store_name
}

if($action=="pinumber_popup")
{
  	echo load_html_head_contents("PI Number Popup Info","../../../../", 1, 1, $unicode);
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
                                echo create_drop_down( "cbo_supplier_id", 150,"select DISTINCT(c.id),c.supplier_name from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$companyID' and b.party_type=2",'id,supplier_name', 1, '-- All Supplier --',0,'',0);
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
                            <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_supplier_id').value+'_'+document.getElementById('txt_search_common').value+'_'+<? echo $companyID; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_pi_search_list_view', 'search_div', 'pi_wise_yarn_receive_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
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
    <script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?

}

$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
$store_arr=return_library_array( "select id, store_name from lib_store_location", "id", "store_name"  );
$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
$yarn_count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');



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

	$sql= "select id, pi_number, supplier_id, importer_id, pi_date, last_shipment_date, total_amount from com_pi_master_details where importer_id=$company and entry_form=165 and supplier_id like '$cbo_supplier' and pi_number like '%".$txt_search_common."%' and is_deleted=0 and status_active=1 $pi_date_cond";

	$arr=array(1=>$company_arr,2=>$supplier_arr);
	echo create_list_view("list_view", "PI No, Importer, Supplier Name, PI Date, Last Shipment Date, PI Value","130,110,130,90,130","780","260",0, $sql , "js_set_value", "id,pi_number", "", 1, "0,importer_id,supplier_id,0,0,0,0", $arr, "pi_number,importer_id,supplier_id,pi_date,last_shipment_date,total_amount", "",'','0,0,0,3,3,2') ;
	exit();
}

if($action=="btbLc_popup")
{
  	echo load_html_head_contents("BTB LC Popup Info","../../../../", 1, 1, $unicode);
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
        <table width="700" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                <thead>
                    <tr>
                        <th>Supplier</th>
                        <th id="search_by_td_up">Enter BTB LC Number</th>
                        <th>LC Date</th>
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
                                echo create_drop_down( "cbo_supplier_id", 160,"select DISTINCT(c.id),c.supplier_name from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$companyID' and b.party_type=2",'id,supplier_name', 1, '-- All Supplier --',0,'',0);
                            ?>
                        </td>
                        <td align="center" id="search_by_td">
                            <input type="text" style="width:180px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
                        </td>
                        <td align="center">
                            <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" placeholder="From Date" style="width:80px"/>
                            To
                            <input type="text" name="txt_date_to" placeholder="To Date" id="txt_date_to" value="" class="datepicker" style="width:80px"/>
                        </td>
                        <td align="center">
                            <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_supplier_id').value+'_'+document.getElementById('txt_search_common').value+'_'+<? echo $companyID; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_lc_search_list_view', 'search_div', 'pi_wise_yarn_receive_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                        </td>
                    </tr>
                </tbody>
            </table>
            <div align="center" style="margin-top:10px" id="search_div"> </div>
            </form>
    </div>
    </body>
    <script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?

}


if($action=="create_lc_search_list_view")
{
	$ex_data = explode("_",$data);
    $cbo_supplier = "";
	if($ex_data[0]>0)  $cbo_supplier = " and supplier_id = ".$ex_data[0];
	$txt_search_common = trim($ex_data[1]);
	$company = $ex_data[2];
    $date_cond = "";
    if ($db_type == 0) {
        if ($ex_data[3] != "" && $ex_data[4] != "")
            $date_cond = " and lc_date  between '" . change_date_format($ex_data[3], 'yyyy-mm-dd') . "' and '" . change_date_format($ex_data[4], 'yyyy-mm-dd') . "'";
    }
    else {
        if ($ex_data[3] != "" && $ex_data[4] != "")
            $date_cond = " and lc_date between '" . date("j-M-Y", strtotime($ex_data[3])) . "' and '" . date("j-M-Y", strtotime($ex_data[4]. ' +1 day')) . "'";
    }

	$sql= "select id, lc_number, supplier_id, importer_id, lc_date, last_shipment_date, lc_value from com_btb_lc_master_details where importer_id=$company and pi_entry_form=165  $cbo_supplier and lc_number like '%".$txt_search_common."%' and is_deleted=0 and status_active=1 $date_cond";
	$arr=array(1=>$company_arr,2=>$supplier_arr);
	echo create_list_view("list_view", "LC No, Importer, Supplier Name, LC Date, Last Shipment Date, LC Value","130,110,130,90,130","780","260",0, $sql , "js_set_value", "id,lc_number", "", 1, "0,importer_id,supplier_id,0,0,0,0", $arr, "lc_number,importer_id,supplier_id,lc_date,last_shipment_date,lc_value", "",'','0,0,0,3,3,2') ;
	exit();

}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	//if(str_replace("'","",$cbo_store_name)!="") $store_cond=" and b.store_id in(".str_replace("'","",$cbo_store_name).")";
    $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$pi_no_cond=str_replace("'","",$txt_pi_no);
	$btbLc_id_str=str_replace("'","",$btbLc_id);
	if(str_replace("'","",$cbo_store_name)==0) $store="%%"; else $store=str_replace("'","",$cbo_store_name);
	if(str_replace("'","",$txt_pi_no)=='') $pi_cond=""; else $pi_cond="and b.pi_number='$pi_no_cond'";
	if(str_replace("'","",$btbLc_id)=='') $btbLc_id_cond=""; else $btbLc_id_cond=" and a.com_btb_lc_master_details_id='$btbLc_id_str'";
	if(str_replace("'","",$btbLc_id)=='') $btbLc_id_cond_mst=""; else $btbLc_id_cond_mst=" and a.id='$btbLc_id_str'";
	if(str_replace("'","",$txt_pi_no)=='') $pi_cond_btb=""; else $pi_cond_btb="and c.pi_number='$pi_no_cond'";
	$from_date=str_replace("'","",$txt_date_from);
    $to_date=str_replace("'","",$txt_date_to);
    $search_cond = "";
	if ($db_type == 0)
	{
		if ($from_date != "" && $to_date != "")
			$search_cond .= " and a.lc_date  between '" . change_date_format($from_date, 'yyyy-mm-dd') . "' and '" . change_date_format($to_date, 'yyyy-mm-dd') . "'";
	}
	else
	{
		if ($from_date != "" && $to_date != "")
			$search_cond .= " and a.lc_date between '" . date("j-M-Y", strtotime($from_date)) . "' and '" . date("j-M-Y", strtotime($to_date)) . "'";
	}

	if($db_type==0)
	{
		$conversion_date=date("Y-m-d");
	}
	else
	{
		$conversion_date=date("d-M-y");
	}
	$exchange_rate=set_conversion_rate( 2, $conversion_date );
	//echo $exchange_rate;die;
	ob_start();
    if ($type == 1) // Show Button
    {
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
                    $sql_btb_lc_data = "select a.id,a.lc_number, a.supplier_id, a.importer_id, a.lc_date, a.last_shipment_date, a.lc_value
					from com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_master_details c
					where a.id=b.com_btb_lc_master_details_id and c.id=b.pi_id and c.entry_form=165 and c.importer_id=$cbo_company_name and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $btbLc_id_cond_mst $pi_cond_btb $search_cond
					group by a.id, a.lc_number, a.supplier_id, a.importer_id, a.lc_date, a.last_shipment_date, a.lc_value";
					//echo $sql_btb_lc_data;die;
                    $btbLcData=sql_select($sql_btb_lc_data);
                ?>
                <thead>
                	 <tr>
                        <th colspan="8">BTB LC Details</th>
                    </tr>
                	<tr>
                        <th width="140">Company</th>
                        <th width="140">Importer</th>
                        <th width="140">Store</th>
                        <th width="140">Supplier</th>
                        <th width="140">BTB LC No</th>
                        <th width="100">LC Date</th>
                        <th width="120">Last Shipment Date</th>
                        <th>LC Value</th>
                    </tr>
                </thead>
                <?
				$btb_id_arr=array();
				foreach($btbLcData as $val)
				{
					?>
                    <tr bgcolor="#FFFFFF">
                        <td><p><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></p></td>
                        <td><p><? echo $company_arr[$val[csf('importer_id')]]; ?></p></td>
                        <td><p><? echo $store_arr[str_replace("'","",$cbo_store_name)]; ?>&nbsp;</p></td>
                        <td><p><? echo $supplier_arr[$val[csf('supplier_id')]]; ?></p></td>
                        <td><p><? echo $val[csf('lc_number')]; ?>&nbsp;</p></td>
                        <td align="center"><? echo change_date_format($val[csf('lc_date')]); ?></td>
                        <td align="center"><? echo change_date_format($val[csf('last_shipment_date')]); ?></td>
                        <td align="right"><? echo number_format($val[csf('lc_value')],2); ?></td>
                    </tr>
                	<?
					if ($from_date != "" && $to_date != "")
					{
						$btb_id_arr[$val[csf('id')]]=$val[csf('id')];
					}
				}
				?>
            </table>
            <br>
            <table width="1100" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                <thead>
                    <tr>
                        <th colspan="11">PI Details</th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="120">PI Number</th>
                        <th width="80">PI Date</th>
                        <th width="70">Count</th>
                        <th width="150">Composition</th>
                        <th width="80">Type</th>
                        <th width="90">Color</th>
                        <th width="110">Qnty</th>
                        <th width="90">Rate</th>
                        <th width="120">Value</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <?
                   $i=1; $pi_id_all_array=array(); $pi_with_work_order_array=array(); $yarn_composition_item=array();  $pi_name_array=array();  $rate=0; $compos=''; $tot_pi_qnty=0; $tot_pi_amnt=0;
				   if(count($btb_id_arr)>0) $btbLc_id_cond.=" and a.COM_BTB_LC_MASTER_DETAILS_ID in(".implode(",",$btb_id_arr).")";
                   $sql="select b.id, b.pi_number,b.pi_basis_id, b.goods_rcv_status, b.pi_date, b.currency_id, b.remarks, c.color_id, c.count_name, c.yarn_composition_item1, c.yarn_composition_percentage1, c.yarn_composition_item2, c.yarn_composition_percentage2, c.yarn_type, c.work_order_id, sum(c.quantity) as qnty, sum(c.net_pi_amount) as amnt
					from com_btb_lc_pi a, com_pi_master_details b, com_pi_item_details c
					where a.pi_id=b.id and b.id=c.pi_id and b.entry_form=165 and b.importer_id=$cbo_company_name and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $pi_cond $btbLc_id_cond
					group by b.id, c.color_id, c.count_name, c.yarn_composition_item1, c.yarn_composition_percentage1, c.yarn_composition_item2, c.yarn_composition_percentage2, c.yarn_type,c.work_order_id, b.pi_number,b.pi_basis_id, b.goods_rcv_status, b.pi_date, b.currency_id, b.remarks";

                    //echo $sql;
                    $result=sql_select($sql);$pi_mrr_summery=array();
                    foreach($result as $row)
                    {
                        if ($i%2==0)
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";

						if($row[csf('currency_id')]!=1) $ex_rate=1; else $ex_rate=$exchange_rate;

                        $rate=($row[csf('amnt')]/$row[csf('qnty')])/$ex_rate;

                        $compos=$composition[$row[csf('yarn_composition_item1')]]." ".$row[csf('yarn_composition_percentage1')]."%";

                        if($row[csf('yarn_composition_percentage2')]>0)
                        {
                            $compos.=" ".$composition[$row[csf('yarn_composition_item2')]]." ".$row[csf('yarn_composition_percentage2')]."%";
                        }


                        if(!in_array($row[csf('id')],$pi_id_all_array))
                        {
                            $pi_id_all_array[$row[csf('id')]]=$row[csf('id')];
							$pi_name_array[$row[csf('id')]]=$row[csf('pi_number')];
                        }

                        if($row[csf('pi_basis_id')]==1)
                        {
							if($pi_wo_check[$row[csf('id')]][$row[csf('work_order_id')]]=="" && $row[csf('work_order_id')]>0)
							{
								$pi_wo_check[$row[csf('id')]][$row[csf('work_order_id')]]=$row[csf('work_order_id')];
								$pi_with_work_order_array[$row[csf('id')]].= $row[csf('work_order_id')].",";
							}
                            if($pi_wo_check2[$row[csf('id')]][$row[csf('work_order_id')]][$row[csf('yarn_composition_item1')]]=="" && $row[csf('work_order_id')]>0)
							{
                                $pi_wo_check2[$row[csf('id')]][$row[csf('work_order_id')]][$row[csf('yarn_composition_item1')]]=$row[csf('yarn_composition_item1')];
								$wo_comp_data[$row[csf('work_order_id')]][$row[csf('yarn_composition_item1')]]=$row[csf('yarn_composition_item1')];
                                $yarn_composition_item[$row[csf('id')]].= $row[csf('yarn_composition_item1')].",";
							}
                        }

						$pi_mrr_summery[$row[csf('id')]][$row[csf('count_name')]][$row[csf('yarn_composition_item1')]][$row[csf('yarn_type')]][$row[csf('color_id')]]["pi_qnty"]+=$row[csf('qnty')];
						$pi_mrr_summery[$row[csf('id')]][$row[csf('count_name')]][$row[csf('yarn_composition_item1')]][$row[csf('yarn_type')]][$row[csf('color_id')]]["pi_amnt"]+=$row[csf('amnt')];
                        $pi_mrr_summery[$row[csf('id')]][$row[csf('count_name')]][$row[csf('yarn_composition_item1')]][$row[csf('yarn_type')]][$row[csf('color_id')]]["pi_basis_id"] = $row[csf('pi_basis_id')];
                        $pi_mrr_summery[$row[csf('id')]][$row[csf('count_name')]][$row[csf('yarn_composition_item1')]][$row[csf('yarn_type')]][$row[csf('color_id')]]["goods_rcv_status"] = $row[csf('goods_rcv_status')];
						 $pi_mrr_summery[$row[csf('id')]][$row[csf('count_name')]][$row[csf('yarn_composition_item1')]][$row[csf('yarn_type')]][$row[csf('color_id')]]["pi_rate"] = $rate;

                        ?>

                        <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="120"><p><? echo $row[csf('pi_number')]; ?></p></td>
                            <td width="80" align="center"><? echo change_date_format($row[csf('pi_date')]); ?></td>
                            <td width="70" align="center"><p>&nbsp;<? echo $count_arr[$row[csf('count_name')]]; ?></p></td>
                            <td width="150"><p><? echo $compos; ?></p></td>
                            <td width="80"><p>&nbsp;<? echo $yarn_type[$row[csf('yarn_type')]]; ?></p></td>
                            <td width="90"><p>&nbsp;<? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
                            <td width="110" align="right"><? echo number_format($row[csf('qnty')],2,'.',''); ?></td>
                            <td width="90" align="right"><? if(number_format($row[csf('qnty')],2,'.','')>0) echo number_format($rate,4,'.',''); else echo "0.00"; ?></td>
                            <td width="120" align="right"><? if(number_format($row[csf('qnty')],2,'.','')>0) echo number_format($row[csf('amnt')],2,'.',''); else echo "0.00"; ?></td>
                            <td><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
                        </tr>
                    <?

                        if(number_format($row[csf('qnty')],2,'.','')>0)
						{
							$tot_pi_qnty+=$row[csf('qnty')];
							$tot_pi_amnt+=$row[csf('amnt')];
						}

                        $i++;
                    }
                ?>
                <tfoot>
                    <th colspan="7" align="right">Total</th>
                    <th align="right"> <? echo number_format($tot_pi_qnty,2,'.','');?></th>
                    <th>&nbsp;</th>
                    <th align="right"> <? echo number_format($tot_pi_amnt,2,'.','');?></th>
                    <th>&nbsp;</th>
                </tfoot>
            </table>
            <br>
            <table width="1280" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                <thead>
                    <tr>
                      <th colspan="14">Yarn Receive</th>
                    </tr>
                    <tr>
                        <th width="80">Recv. Date</th>
                        <th width="110">MRR No</th>
                        <th width="110"> Already posted in account</th>
                        <th width="110">Store Name</th>
                        <th width="80">Challan No</th>
                        <th width="80">Lot No</th>
                        <th width="70">Count</th>
                        <th width="130">Composition</th>
                        <th width="80">Type</th>
                        <th width="80">Color</th>
                        <th width="90">Qnty</th>
                        <th width="70">Rate</th>
                        <th width="100">Value</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                 <?
                    //$pi_id_all=implode(",",array_flip($pi_id_all_array));
					$pi_id_all=implode(",",$pi_id_all_array);
					//echo "<pre>";print_r($pi_with_work_order_array);die;
					foreach($pi_with_work_order_array as $pi_ids)
					{
						$all_pi_ids.=chop($pi_ids,",").",";
					}
					$pi_workd_order_id_all=chop($all_pi_ids,",");
					//$pi_workd_order_id_all=implode(",",$pi_with_work_order_array);

                    foreach($yarn_composition_item as $comps_ids)
					{
						$all_comps_ids.=chop($comps_ids,",").",";
					}
					$pi_workd_order_comps_ids_all=chop($all_comps_ids,",");

                    $compos=''; $tot_recv_qnty=0; $tot_recv_amnt=0; $recv_id_array=array(); $recv_pi_array=array(); $pi_data_array=array();
                    //echo $pi_id_all.test;die;
					if ($pi_id_all!='' || $pi_id_all!=0)
					{
                        $sql_recv="select a.id, a.recv_number, a.receive_date,a.receive_basis, a.challan_no, a.currency_id, a.exchange_rate, a.remarks,a.store_id ,a.is_posted_account , b.pi_wo_batch_no, (b.order_rate+b.order_ile_cost) as rate, b.order_qnty, b.order_amount, c.yarn_count_id, c.yarn_type, c.lot, c.color, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_comp_type2nd, c.yarn_comp_percent2nd
                        from inv_receive_master a, inv_transaction b, product_details_master c
                        where a.item_category=1 and a.entry_form in(1,248) and a.company_id=$cbo_company_name and a.receive_basis in(1) and a.booking_id in($pi_id_all) and a.id=b.mst_id and b.item_category=1 and b.transaction_type=1 and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.store_id like '$store' ";
					}

					if($pi_workd_order_id_all!="")
					{
                        if($pi_workd_order_comps_ids_all=='') $y_comp_cond=""; else $y_comp_cond="and c.yarn_comp_type1st in($pi_workd_order_comps_ids_all) ";

						if($sql_recv!="") $sql_recv.="union all ";
                        $sql_recv.="select a.id, a.recv_number, a.receive_date,a.receive_basis,a.challan_no, a.currency_id, a.exchange_rate, a.remarks,a.store_id,a.is_posted_account, b.pi_wo_batch_no, (b.order_rate+b.order_ile_cost) as rate, b.order_qnty, b.order_amount, c.yarn_count_id,c.yarn_type,c.lot,c.color,c.yarn_comp_type1st,c.yarn_comp_percent1st,c.yarn_comp_type2nd, c.yarn_comp_percent2nd from inv_receive_master a, inv_transaction b, product_details_master c, wo_non_order_info_mst d where a.item_category=1 and a.entry_form in(1,248) and a.company_id=$cbo_company_name and a.receive_basis=2 and a.booking_id in($pi_workd_order_id_all) $y_comp_cond and a.booking_id=d.id and b.pi_wo_batch_no=d.id and a.receive_purpose in(16,43) and a.id=b.mst_id and b.item_category=1 and b.transaction_type=1 and b.prod_id=c.id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and b.store_id like '$store'";
					}
					$sql_recv.=" order by receive_date";
					//echo $sql_recv;

					$dataArray=sql_select($sql_recv);

                    foreach($dataArray as $row_recv)
                    {
                        if ($i%2==0)
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";

                        $display_data=1;
						if($row_recv[csf('receive_basis')]==2)
						{
							if($wo_comp_data[$row_recv[csf('pi_wo_batch_no')]][$row_recv[csf('yarn_comp_type1st')]]=="")
							{
								$display_data=0;
							}
						}

						$compos=$composition[$row_recv[csf('yarn_comp_type1st')]]." ".$row_recv[csf('yarn_comp_percent1st')]."%";

                        if($row_recv[csf('yarn_comp_percent2nd')]>0)
                        {
                            $compos.=" ".$composition[$row_recv[csf('yarn_comp_type2nd')]]." ".$row_recv[csf('yarn_comp_percent2nd')]."%";
                        }

                        if(!in_array($row_recv[csf('id')],$recv_id_array))
                        {
                            $recv_id_array[$row_recv[csf('id')]]=$row_recv[csf('id')];
							$recv_pi_array[$row_recv[csf('id')]]=$row_recv[csf('pi_wo_batch_no')];
                        }

                        if($row_recv[csf('currency_id')]!=1) $ex_rate=1; else $ex_rate=$exchange_rate;

                        $pi_data_array[$row_recv[csf('pi_wo_batch_no')]]['rcv_basis'] = $row_recv[csf('receive_basis')];
                        $pi_data_array[$row_recv[csf('pi_wo_batch_no')]]['rcv']+=($row_recv[csf('rate')]*$row_recv[csf('order_qnty')]);
						$pi_data_array[$row_recv[csf('pi_wo_batch_no')]]['rcvQnt']+=$row_recv[csf('order_qnty')];

                        $pi_rcv_mrr_summery[$row_recv[csf('pi_wo_batch_no')]][$row_recv[csf('yarn_count_id')]][$row_recv[csf('yarn_comp_type1st')]][$row_recv[csf('yarn_type')]][$row_recv[csf('color')]]["mrr_qnty"]+=$row_recv[csf('order_qnty')];

						$pi_rcv_mrr_summery[$row_recv[csf('pi_wo_batch_no')]][$row_recv[csf('yarn_count_id')]][$row_recv[csf('yarn_comp_type1st')]][$row_recv[csf('yarn_type')]][$row_recv[csf('color')]]["mrr_amnt"]+=$row_recv[csf('order_amount')];
						$pi_rcv_mrr_summery[$row_recv[csf('pi_wo_batch_no')]][$row_recv[csf('yarn_count_id')]][$row_recv[csf('yarn_comp_type1st')]][$row_recv[csf('yarn_type')]][$row_recv[csf('color')]]["remarks"]=$row_recv[csf('remarks')];
                    ?>
                        <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                            <td width="80" align="center"><? echo change_date_format($row_recv[csf('receive_date')]); ?></td>
                            <td width="110"><p><? echo $row_recv[csf('recv_number')]; ?></p></td>
                            <td width="110"><p>&nbsp;<? echo ($row_recv[csf('is_posted_account')]==1)? "Yes":"No" ; ?></p></td>
                            <td width="110"><p>&nbsp;<? echo $store_arr[$row_recv[csf('store_id')]]; ?></p></td>
                            <td width="80"><p>&nbsp;<? echo $row_recv[csf('challan_no')]; ?></p></td>
                            <td width="80"><p>&nbsp;<? echo $row_recv[csf('lot')]; ?></p></td>
                            <td width="70" align="center"><p>&nbsp;<? echo $count_arr[$row_recv[csf('yarn_count_id')]]; ?></p></td>
                            <td width="130"><p><? echo $compos; ?></p></td>
                            <td width="80"><p>&nbsp;<? echo $yarn_type[$row_recv[csf('yarn_type')]]; ?></p></td>
                            <td width="80"><p>&nbsp;<? echo $color_arr[$row_recv[csf('color')]]; ?></p></td>
                            <td width="90" align="right"><? echo number_format($row_recv[csf('order_qnty')],2,'.',''); ?></td>
                            <td width="70" align="right"><? echo number_format($row_recv[csf('rate')],2,'.',''); ?></td>
                            <td width="100" align="right"><? echo number_format(($row_recv[csf('rate')]*$row_recv[csf('order_qnty')]),2,'.',''); ?></td>
                            <td><p><? echo $row_recv[csf('remarks')];?> </p></td>
                        </tr>
                    <?

                        $tot_recv_qnty+=$row_recv[csf('order_qnty')];
                        $tot_recv_amnt+=($row_recv[csf('rate')]*$row_recv[csf('order_qnty')]);//$row_recv[csf('order_amount')];

                        $i++;
                    }
                ?>
                <tfoot>
                    <th colspan="10" align="right">Total</th>
                    <th align="right"><? echo number_format($tot_recv_qnty,2,'.','');?></th>
                    <th>&nbsp;</th>
                    <th align="right"><? echo number_format($tot_recv_amnt,2,'.','');?></th>
                    <th>&nbsp;</th>
                </tfoot>
            </table>
            <br>
             <table width="1210" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                <thead>
                    <tr>
                      <th colspan="8">Yarn Return</th>
                    </tr>
                    <tr>
                        <th width="100">Return Date</th>
                        <th width="130">Return No</th>
                        <th width="110">Store Name</th>
                        <th width="290">Item Description</th>
                        <th width="110">Qnty</th>
                        <th width="100">Rate</th>
                        <th width="120">Value</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                 <?
                    $tot_retn_qnty=0; $tot_retn_amnt=0;$return_data_arr=array();
                    if(count($recv_id_array)>0)
                    {
						$recv_id_all=implode(",",$recv_id_array);
						/*$sql_retn="select a.id, a.received_id, a.issue_number, a.issue_date, a.challan_no,b.store_id, cons_rate as rate, b.rcv_rate, b.cons_quantity, b.cons_amount, c.product_name_details,d.receive_date,a.remarks, a.pi_id, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_type, c.color
						from inv_issue_master a, inv_transaction b, product_details_master c,inv_receive_master d
						where a.item_category=1 and a.entry_form=8 and a.company_id=$cbo_company_name and a.received_id in($recv_id_all) and a.id=b.mst_id and a.received_id=d.id and b.item_category=1 and b.transaction_type=3 and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";*/
                        $sql_retn="select a.id, a.received_id, a.issue_number, a.issue_date, a.challan_no,b.store_id, cons_rate as rate, b.rcv_rate, b.cons_quantity, b.cons_amount, c.product_name_details,d.receive_date,a.remarks, a.pi_id, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_type, c.color
						from inv_issue_master a, inv_transaction b, product_details_master c,inv_receive_master d
						where a.item_category=1 and a.company_id=$cbo_company_name and a.received_id in($recv_id_all) and a.id=b.mst_id and a.received_id=d.id and b.item_category=1 and b.transaction_type=3 and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
						//echo $sql_retn;
                        $dataRtArray=sql_select($sql_retn);
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

							$exchange_rate=set_conversion_rate( 2, $conversion_date );
							if($row_retn[csf('rcv_rate')]>0)
							{
								$pi_string=$row_retn[csf('pi_id')]."*".$row_retn[csf('yarn_count_id')]."*".$row_retn[csf('yarn_comp_type1st')]."*".$row_retn[csf('yarn_type')]."*".$row_retn[csf('color')];
								$pi_rate=$pi_mrr_summery[$row_retn[csf('pi_id')]][$row_retn[csf('yarn_count_id')]][$row_retn[csf('yarn_comp_type1st')]][$row_retn[csf('yarn_type')]][$row_retn[csf('color')]]["pi_rate"];
								if($pi_rate>0)
								{
									$rate=$pi_rate;
								}
								else
								{
									$rate=$row_retn[csf('rcv_rate')]/$exchange_rate;
								}
							}
							else
							{
								$rate=$row_retn[csf('rate')]/$exchange_rate;
							}

							$amnt=$row_retn[csf('cons_quantity')]*$rate;

                            $rcv_return_qty_arr[$row_retn[csf('received_id')]] = $row_retn[csf('cons_quantity')];

                        ?>
                            <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                                <td width="100" align="center"><? echo change_date_format($row_retn[csf('issue_date')]); ?></td>
                                <td width="130" title="<?= "pi string = ".$pi_string?>"><p><? echo $row_retn[csf('issue_number')]; ?></p></td>
                                <td width="110" ><p>&nbsp;<? echo $store_arr[$row_retn[csf('store_id')]]; ?></p></td>
                                <td width="290"><p>&nbsp;<? echo $row_retn[csf('product_name_details')]; ?></p></td>
                                <td width="110" align="right"><? echo number_format($row_retn[csf('cons_quantity')],2,'.',''); ?>&nbsp;</td>
                                <td width="100" align="right" title="<? echo 'exchange rate = '.$exchange_rate;?>"><? echo number_format($rate,2,'.',''); ?>&nbsp;</td>
                                <td width="120" align="right"><? echo number_format($amnt,2,'.',''); ?>&nbsp;</td>
                                <td><p><? echo $row_retn[csf('remarks')];?> &nbsp;</p></td>
                            </tr>
                        <?
                            $pi_id=$recv_pi_array[$row_retn[csf('received_id')]];
                            $tot_retn_qnty+=$row_retn[csf('cons_quantity')];
                            $tot_retn_amnt+=$amnt;
                            $pi_data_array[$pi_id]['rtn']+=$amnt;
                            $pi_data_array[$pi_id]['rtnQnt']+=$row_retn[csf('cons_quantity')];
							$return_data_arr[$pi_id][$row_retn[csf('yarn_count_id')]][$row_retn[csf('yarn_comp_type1st')]][$row_retn[csf('yarn_type')]][$row_retn[csf('color')]]["rtn_qnty"]+=$row_retn[csf('cons_quantity')];
							$return_data_arr[$pi_id][$row_retn[csf('yarn_count_id')]][$row_retn[csf('yarn_comp_type1st')]][$row_retn[csf('yarn_type')]][$row_retn[csf('color')]]["rtn_amnt"]+=$amnt;
                            $i++;
                        }
                    }
                    //print_r($pi_data_array);
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
						$sql_retn="select a.received_id, a.pi_id, a.issue_number, a.issue_date, a.challan_no,a.remarks,b.store_id, (b.order_rate+b.order_ile_cost) as ord_rate, b.cons_rate as rate, b.rcv_rate, b.cons_quantity, b.cons_amount, c.product_name_details, a.pi_id, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_type, c.color
						from inv_issue_master a, inv_transaction b, product_details_master c
						where a.item_category=1 and a.entry_form=8 and a.company_id=$cbo_company_name and a.pi_id in($pi_id_all) and a.id=b.mst_id and b.item_category=1 and b.transaction_type=3 and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $rtn_cond";
						//echo $sql_retn;
						$dataRtArray=sql_select($sql_retn);
						foreach($dataRtArray as $row_retn)
						{
							if ($i%2==0)
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";

							//$rate=$row_retn[csf('rate')]/$exchange_rate;
							$pi_string=$row_retn[csf('pi_id')]."*".$row_retn[csf('yarn_count_id')]."*".$row_retn[csf('yarn_comp_type1st')]."*".$row_retn[csf('yarn_type')]."*".$row_retn[csf('color')];
							$pi_rate=$pi_mrr_summery[$row_retn[csf('pi_id')]][$row_retn[csf('yarn_count_id')]][$row_retn[csf('yarn_comp_type1st')]][$row_retn[csf('yarn_type')]][$row_retn[csf('color')]]["pi_rate"];
							if($row_retn[csf('rcv_rate')]>0)
							{
								if($pi_rate>0)
								{
									$rate=$pi_rate;
								}
								else
								{
									$rate=$row_retn[csf('rcv_rate')]/$exchange_rate;
								}

							}
							else
							{
								$rate=$row_retn[csf('rate')]/$exchange_rate;
							}

							$amnt=$row_retn[csf('cons_quantity')]*$rate;


                            $rcv_return_qty_arr[$row_retn[csf('received_id')]] = $row_retn[csf('cons_quantity')];
						?>
							<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
								<td width="100" align="center"><? echo change_date_format($row_retn[csf('issue_date')]); ?></td>
								<td width="130" title="<?= "pi string = ".$pi_string?>"><p><? echo $row_retn[csf('issue_number')]; ?></p></td>
								<td width="110" ><p>&nbsp;<? echo $store_arr[$row_retn[csf('store_id')]]; ?></p></td>
								<td width="290"><p>&nbsp;<? echo $row_retn[csf('product_name_details')]; ?></p></td>
								<td width="110" align="right"><? echo number_format($row_retn[csf('cons_quantity')],2,'.',''); ?>&nbsp;</td>
								<td width="100" align="right" title="<? echo 'exchange_rate='.$exchange_rate;?>"><? echo number_format($rate,2,'.',''); ?>&nbsp;</td>
								<td width="120" align="right"><? echo number_format($amnt,2,'.',''); ?>&nbsp;</td>
								<td><p><? echo $row_retn[csf('remarks')];?> &nbsp;</p></td>
							</tr>
						<?
							$pi_id=$row_retn[csf('pi_id')];
							$tot_retn_qnty+=$row_retn[csf('cons_quantity')];
							$tot_retn_amnt+=$amnt;
							$pi_data_array[$pi_id]['rtn']+=$amnt;
							$pi_data_array[$pi_id]['rtnQnt']+=$row_retn[csf('cons_quantity')];

							$return_data_arr[$pi_id][$row_retn[csf('yarn_count_id')]][$row_retn[csf('yarn_comp_type1st')]][$row_retn[csf('yarn_type')]][$row_retn[csf('color')]]["rtn_qnty"]+=$row_retn[csf('cons_quantity')];
							$return_data_arr[$pi_id][$row_retn[csf('yarn_count_id')]][$row_retn[csf('yarn_comp_type1st')]][$row_retn[csf('yarn_type')]][$row_retn[csf('color')]]["rtn_amnt"]+=$amnt;

							$i++;
						}
					}

                    $total_balance_qty = ($tot_pi_qnty+$tot_retn_qnty-$tot_recv_qnty);
                    $total_balance_value = ($tot_pi_amnt+$tot_retn_amnt-$tot_recv_amnt);
                ?>
                <tfoot>
                    <tr>
                        <th colspan="4" align="right">Total</th>
                        <th align="right"><? echo number_format($tot_retn_qnty,2,'.','');?></th>
                        <th>&nbsp;</th>
                        <th align="right"><? echo number_format($tot_retn_amnt,2,'.','');?></th>
                        <th>&nbsp;</th>
                    </tr>
                    <tr>
                        <th colspan="4" align="right">Balance</th>
                        <th align="right" title="<? echo $tot_pi_qnty.test; ?>"><? echo number_format($total_balance_qty,2,'.','');?></th>
                        <th>&nbsp;</th>
                        <th align="right"><? echo number_format($total_balance_value,2,'.','');?></th>
                        <th>&nbsp;</th>
                    </tr>
                </tfoot>
            </table>
            <br>
            <table width="850" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                <thead>
                        <tr>

							<th width="140" rowspan="2">PI Number</th>
							<th width="" colspan="2">Receive</th>
							<th width="" colspan="2">Return</th>
							<th width="" colspan="2">Payable</th>
                            <th width="" colspan="4">Acceptance Details</th>
						</tr>
						<tr>
							<th width="100">Qnty</th>
							<th width="100">Value</th>
							<th width="100">Qnty</th>
							<th width="100">Value</th>
							<th width="100">Qnty</th>
							<th width="100">Value</th>
                            <th width="100">Date</th>
							<th width="100">Invoice No.</th>
							<th width="100">Value</th>
							<th width="100">Bal. Value</th>
						</tr>
                </thead>
                <?

					if ($pi_id_all!='' || $pi_id_all!=0)
					{
                        if($db_type==0){
                            $accep_query = "select m.id as pi_id, m.pi_basis_id, m.goods_rcv_status, group_concat(n.work_order_id) as work_order_id, a.company_acc_date, b.current_acceptance_value as acceptance_value, a.invoice_no
                            from  com_pi_master_details m
                            left join com_pi_item_details n on m.id=n.pi_id and n.status_active=1 and n.is_deleted=0
                            left join com_import_invoice_dtls b on m.id=b.pi_id and b.status_active=1 and b.is_deleted=0
                            left join com_import_invoice_mst a on a.id=b.import_invoice_id where m.id in($pi_id_all)
                            group by m.id, m.pi_basis_id, m.goods_rcv_status, a.company_acc_date, b.current_acceptance_value, a.invoice_no
                            order by m.id";
                        }else if($db_type ==2){
                            $accep_query = "select m.id as pi_id, m.pi_basis_id, m.goods_rcv_status, listagg(n.work_order_id, ',') within group (order by n.work_order_id) work_order_id, a.company_acc_date, b.current_acceptance_value as acceptance_value, a.invoice_no
                            from  com_pi_master_details m
                            left join com_pi_item_details n on m.id=n.pi_id and n.status_active=1 and n.is_deleted=0
                            left join com_import_invoice_dtls b on m.id=b.pi_id and b.status_active=1 and b.is_deleted=0
                            left join com_import_invoice_mst a on a.id=b.import_invoice_id where m.id in($pi_id_all)
                            group by m.id, m.pi_basis_id, m.goods_rcv_status, a.company_acc_date, b.current_acceptance_value, a.invoice_no
                            order by m.id";
                        }
                        //echo $accep_query;//die;
                        $accep_sql=sql_select($accep_query);
						foreach($accep_sql as $row)
						{
							$accep_breakdown_data[$row[csf("pi_id")]]++;
							$acceptance_arr[$row[csf("pi_id")]]["acceptance_value"]+=$row[csf("acceptance_value")];
							if($row[csf("goods_rcv_status")]==1){ //After Goods Received
                                if($wo_check[$row[csf("pi_id")]][$row[csf("work_order_id")]]=="")
                                {
                                    $acceptance_arr[$row[csf("pi_id")]]["work_order_id"] =$row[csf("work_order_id")];
                                }
                            }else{ //Before Goods Received
                                if($wo_check[$row[csf("pi_id")]][$row[csf("pi_id")]]=="")
                                {
                                    $acceptance_arr[$row[csf("pi_id")]]["pi_id"].=$row[csf("pi_id")].",";
                                }
                            }
						}
					}
                    //echo $count_row[1261].jahid;die;
						$payble_value=0;
						$tot_payble_value=0;
						$yet_to_accept=0;
						$tot_accept_value=0;
						$tot_yet_to_accept=0;
						$total_receive_value=0;
						$total_receive_rcvQnt=0;
						$total_return_value=0;
						$total_return_rtnQnt=0;
						$tot_payble_Qnty=0;
					/*foreach($pi_name_array as $key=>$value)
					{
					}*/
					//echo "<pre>";print_r($pi_data_array);die;
                    foreach($accep_sql as $row)
                    {
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
							<?
                           	//echo "<pre>";print_r($pi_data_array);
                            //echo $row[csf("pi_basis_id")];
							if($temp_pi[$row[csf("pi_id")]]=="")
							{
								$payble_value=$yet_to_accept=$recv_qnty=$recv_value=$recv_rtn_qnty=$recv_rtn_value=0;$piwo_id ="";
                                if($row[csf("goods_rcv_status")]==1)
                                {
									$piwo_id =chop($acceptance_arr[$row[csf("pi_id")]]["work_order_id"],",");
									$piwo_id_arr = array_unique(explode(",",chop($acceptance_arr[$row[csf("pi_id")]]["work_order_id"],",")));
									//echo "<pre>";print_r($piwo_id_arr);die;
									foreach($piwo_id_arr as $wo_id)
									{
										$recv_qnty+=$pi_data_array[$wo_id*1]['rcvQnt'];
										$recv_value+=$pi_data_array[$wo_id*1]['rcv'];
									}
									//echo $recv_qnty;die;
                                }
								else
								{
									$recv_qnty = $pi_data_array[$row[csf("pi_id")]]['rcvQnt'];
									$recv_value = $pi_data_array[$row[csf("pi_id")]]['rcv'];
									$recv_rtn_qnty=$pi_data_array[$row[csf("pi_id")]]['rtnQnt'];
									$recv_rtn_value=$pi_data_array[$row[csf("pi_id")]]['rtn'];
                                    $piwo_id =  $row[csf("pi_id")];
                                }


								$payble_Qnty=$recv_qnty-$recv_rtn_qnty;
								$payble_value=$recv_value-$recv_rtn_value;
								$yet_to_accept=$payble_value-$acceptance_arr[$row[csf("pi_id")]]["acceptance_value"];

								$total_receive_value+=$recv_value;
								$total_receive_rcvQnt+=$recv_qnty;
								$total_return_value+=$recv_rtn_value;
								$total_return_rtnQnt+=$recv_rtn_qnty;
								$tot_payble_value+=$payble_value;
								$tot_payble_Qnty+=$payble_Qnty;
								$tot_accept_value+=$acceptance_arr[$row[csf("pi_id")]]["acceptance_value"];
								$tot_yet_to_accept+=$yet_to_accept;
								?>
								<td width="140" rowspan="<? echo $accep_breakdown_data[$row[csf("pi_id")]];?>" title="<?= $row[csf("pi_id")]; ?>"><p><? echo $pi_name_array[$row[csf("pi_id")]]; ?></p></td>
								<td width="100"  align="right" rowspan="<? echo $accep_breakdown_data[$row[csf("pi_id")]];?>" title="<?= print_r($piwo_id_arr); ?>"><? echo number_format($recv_qnty,2,'.',''); ?>&nbsp;</td>
                                <td width="100"  align="right" rowspan="<? echo $accep_breakdown_data[$row[csf("pi_id")]];?>" title="<? echo $piwo_id." PI ".$row[csf("pi_id")]; ?>"><? echo number_format($recv_value,2,'.',''); ?>&nbsp;</td>
                                <td width="100" align="right" rowspan="<? echo $accep_breakdown_data[$row[csf("pi_id")]];?>"><? echo number_format($recv_rtn_qnty,2,'.',''); ?>&nbsp;</td>
								<td width="100" align="right" rowspan="<? echo $accep_breakdown_data[$row[csf("pi_id")]];?>"><? echo number_format($recv_rtn_value,2,'.',''); ?>&nbsp;</td>
								<td width="100" align="right" rowspan="<? echo $accep_breakdown_data[$row[csf("pi_id")]];?>"><? echo number_format($payble_Qnty,2,'.',''); ?>&nbsp;</td>
                                <td width="100" align="right" rowspan="<? echo $accep_breakdown_data[$row[csf("pi_id")]];?>"><? echo number_format($payble_value,2,'.',''); ?>&nbsp;</td>
								<?
							}
							?>
							<td width="100" align="center"><? if($row[csf("company_acc_date")]!="" && $row[csf("company_acc_date")]!="0000-00-00") echo change_date_format($row[csf("company_acc_date")]);?>&nbsp;</td>
                            <td width="100" align="center"><? echo $row[csf("invoice_no")];?>&nbsp;</td>
							<td width="100" align="right"><? echo number_format($row[csf("acceptance_value")],2,'.',''); ?>&nbsp;</td>
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
                ?>
                <tfoot>
                    <th align="right">Total</th>
                    <th align="right"><? echo number_format($total_receive_rcvQnt,2,'.',''); ?></th>
                    <th align="right"><? echo number_format($total_receive_value,2,'.',''); ?></th>
                    <th align="right"><? echo number_format($total_return_rtnQnt,2,'.',''); ?></th>
                    <th align="right"><? echo number_format($total_return_value,2,'.',''); ?></th>
                    <th align="right"><? echo number_format($tot_payble_Qnty,2,'.',''); ?></th>
                    <th align="right"><? echo number_format($tot_payble_value,2,'.',''); ?></th>
                    <th align="right">&nbsp;</th>
                    <th align="right">&nbsp;</th>
                    <th align="right"><? echo number_format($tot_accept_value,2,'.',''); ?></th>
                    <th align="right"><? echo number_format($tot_yet_to_accept,2,'.',''); ?></th>
                </tfoot>
            </table>

            <br>
            <table width="1150" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                <thead>
                    <tr>
                      <th colspan="13">Summary Of PI and Receive</th>
                    </tr>
                    <tr>
                    	<th colspan="8">PI Summary</th>
                        <th colspan="5">Receive Summary</th>
                    </tr>
                    <tr>
                        <th width="100">PI Number</th>
                        <th width="70">Count</th>
                        <th width="130">Composition</th>
                        <th width="80">Yarn Type</th>
                        <th width="70">Color</th>
                        <th width="80">Require Qnty</th>
                        <th width="80">Rate</th>
                        <th width="90">Value</th>
                        <th width="80">Receive Qnty</th>
                        <th width="80">Receive Blance</th>
                        <th width="80">Rate</th>
                        <th width="90">Value</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                <?

				foreach( $pi_mrr_summery as $pi_wo_id=>$pi_data )
				{
					foreach($pi_data as $y_count_id=>$y_count_data)
					{
						foreach($y_count_data as $y_comp_id=>$y_comp_data)
						{
							foreach($y_comp_data as $y_type_id=>$y_type_data)
							{
								foreach($y_type_data as $y_color_id=>$color_data)
								{
									if ($i%2==0)
										$bgcolor="#E9F3FF";
									else
										$bgcolor="#FFFFFF";

									//echo $color_data["pi_qnty"]."==".$color_data["mrr_qnty"];

                                    $pi_basis_id = $pi_mrr_summery[$pi_wo_id][$y_count_id][$y_comp_id][$y_type_id][$y_color_id]["pi_basis_id"];
                                    $goods_rcv_status = $pi_mrr_summery[$pi_wo_id][$y_count_id][$y_comp_id][$y_type_id][$y_color_id]["goods_rcv_status"];
									$mrr_qnty = $mrr_amnt =0;
									$work_order_id="";
                                    if($goods_rcv_status==1)
                                    {
                                        $work_order_id_arr = explode(",",chop($pi_with_work_order_array[$pi_wo_id],","));
										foreach($work_order_id_arr as $wo_id)
										{
											$mrr_qnty += ($pi_rcv_mrr_summery[$wo_id][$y_count_id][$y_comp_id][$y_type_id][$y_color_id]["mrr_qnty"]-$return_data_arr[$work_order_id][$y_count_id][$y_comp_id][$y_type_id][$y_color_id]["rtn_qnty"]);
											$mrr_amnt += ( $pi_rcv_mrr_summery[$wo_id][$y_count_id][$y_comp_id][$y_type_id][$y_color_id]["mrr_amnt"]-$return_data_arr[$work_order_id][$y_count_id][$y_comp_id][$y_type_id][$y_color_id]["rtn_amnt"] );
											$work_order_id .= $wo_id.",";
										}
                                    }else{
                                        $work_order_id = $pi_wo_id;
										$mrr_qnty = ($pi_rcv_mrr_summery[$work_order_id][$y_count_id][$y_comp_id][$y_type_id][$y_color_id]["mrr_qnty"]-$return_data_arr[$work_order_id][$y_count_id][$y_comp_id][$y_type_id][$y_color_id]["rtn_qnty"]);
										$mrr_amnt = ( $pi_rcv_mrr_summery[$work_order_id][$y_count_id][$y_comp_id][$y_type_id][$y_color_id]["mrr_amnt"]-$return_data_arr[$work_order_id][$y_count_id][$y_comp_id][$y_type_id][$y_color_id]["rtn_amnt"] );
                                    }
									$work_order_id=chop($work_order_id,",");
									//echo $work_order_id."=".$goods_rcv_status."<br>";
									//$mrr_qnty = ($pi_rcv_mrr_summery[$work_order_id][$y_count_id][$y_comp_id][$y_type_id][$y_color_id]["mrr_qnty"]-$pi_data_array[$pi_wo_id]['rtnQnt']);
									//$mrr_amnt = ( $pi_rcv_mrr_summery[$work_order_id][$y_count_id][$y_comp_id][$y_type_id][$y_color_id]["mrr_amnt"]-$pi_data_array[$pi_wo_id]['rtn'] );

									?>
                                    <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                                        <td title="<? echo $pi_wo_id; ?>"><? echo $pi_name_array[$pi_wo_id]; ?></td>
                                        <td title="<? echo $y_count_id; ?>"><? echo $count_arr[$y_count_id]; ?></td>
                                        <td title="<? echo $y_comp_id; ?>"><? echo $composition[$y_comp_id]; ?></td>
                                        <td title="<? echo $y_type_id; ?>"><? echo $yarn_type[$y_type_id]; ?></td>
                                        <td title="<? echo $y_color_id; ?>"><? echo $color_arr[$y_color_id]; ?></td>
                                        <td align="right"><? echo number_format($color_data["pi_qnty"],2); ?></td>
                                        <td align="right"><? if($color_data["pi_qnty"]>0 && $color_data["pi_amnt"]>0) echo number_format(($color_data["pi_amnt"]/$color_data["pi_qnty"]),2); else echo "0.00"; ?></td>
                                        <td align="right"><? echo number_format($color_data["pi_amnt"],2); ?></td>
                                        <td align="right"><? echo number_format($mrr_qnty,2); ?></td>
                                        <td align="right">
                                        <?
                                            $mrr_balance=($color_data["pi_qnty"]*1)-$mrr_qnty;
                                            echo number_format($mrr_balance,2);
                                        ?>
                                        </td>
                                        <td align="right"><? if($mrr_qnty>0 && $mrr_amnt>0) echo number_format(($mrr_amnt/$mrr_qnty),2); else echo "0.00"; ?></td>
                                        <td align="right"><? echo number_format($mrr_amnt,2); ?></td>
                                        <td><? echo $color_data["remarks"]; ?></td>
                                    </tr>
                                    <?
									$total_pi_qnty+=$color_data["pi_qnty"];
									$total_pi_amnt+=$color_data["pi_amnt"];
									$total_mrr_qnty+=$mrr_qnty;
									$total_mrr_amnt+=$mrr_amnt;
									$total_mrr_balance+=$mrr_balance;
									$i++;
								}
							}
						}
					}
				}
				?>
                </tbody>
                <tfoot>
                    <th align="right" colspan="5">Total:</th>
                    <th align="right"><? echo number_format($total_pi_qnty,2,'.',''); ?></th>
                    <th>&nbsp;</th>
                    <th align="right"><? echo number_format($total_pi_amnt,2,'.',''); ?></th>
                    <th align="right"><? echo number_format($total_mrr_qnty,2); ?></th>
                    <th align="right"><? echo number_format($total_mrr_balance,2); ?></th>
                    <th>&nbsp;</th>
                    <th align="right"><? echo number_format($total_mrr_amnt,2,'.',''); ?></th>
                    <th align="right">&nbsp;</th>
                </tfoot>
        	 </table>
             <br>
            <table width="980" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                <thead>
                    <tr>
                      <th colspan="8">Style Wise Yarn Receive Summery</th>
                    </tr>
                    <tr>
                        <th width="80">Buyer</th>
                        <th width="110">Job No</th>
                        <th width="110"> Style No</th>
                        <th width="110">Yarn Color</th>
                        <th width="200">Yarn Description</th>
                        <th width="80">MRR Qty.</th>
                        <th width="70">MRR Rcvd Return Qty.</th>
                        <th width="130">Net MRR Qty.</th>
                    </tr>
                </thead>
                 <?
					$pi_id_all=implode(",",$pi_id_all_array);			
					foreach($pi_with_work_order_array as $pi_ids)
					{
						$all_pi_ids.=chop($pi_ids,",").",";
					}
					$pi_workd_order_id_all=chop($all_pi_ids,",");			
                    foreach($yarn_composition_item as $comps_ids)
					{
						$all_comps_ids.=chop($comps_ids,",").",";
					}
					$pi_workd_order_comps_ids_all=chop($all_comps_ids,",");

                    $compos=''; $tot_recv_qnty=0; $tot_recv_amnt=0; $recv_id_array=array(); $recv_pi_array=array(); $pi_data_array=array();
                    // echo $pi_id_all.test;die;
                    if($store>0){ $store_id="and a.store_id=$store";}
					if ($pi_id_all!='' || $pi_id_all!=0)
					{
                        $sql_recv_summary="SELECT b.buyer_id, b.job_no,a.receive_date, b.style_ref_no,c.color, c.product_name_details, b.order_qnty,b.prod_id
                        from inv_receive_master a, inv_transaction b, product_details_master c
                        where a.item_category=1 and a.entry_form in(1,248) and a.company_id=$cbo_company_name and a.receive_basis in(1) and a.booking_id in($pi_id_all) and a.id=b.mst_id and b.item_category=1 and b.transaction_type=1 and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $store_id";
					}

					if($pi_workd_order_id_all!="")
					{
                        if($pi_workd_order_comps_ids_all=='') $y_comp_cond=""; else $y_comp_cond="and c.yarn_comp_type1st in($pi_workd_order_comps_ids_all) ";

						if($sql_recv_summary!="") $sql_recv_summary.="union all ";
                        $sql_recv_summary.="SELECT b.buyer_id, b.job_no, a.receive_date, b.style_ref_no,c.color, c.product_name_details, b.order_qnty,b.prod_id
                         from inv_receive_master a, inv_transaction b, product_details_master c, wo_non_order_info_mst d where a.item_category=1 and a.entry_form in(1,248) and a.company_id=$cbo_company_name and a.receive_basis=2 and a.booking_id in($pi_workd_order_id_all) $y_comp_cond and a.booking_id=d.id and b.pi_wo_batch_no=d.id and a.receive_purpose in(16,43) and a.id=b.mst_id and b.item_category=1 and b.transaction_type=1 and b.prod_id=c.id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $store_id";
					}
					$sql_recv_summary.=" order by receive_date";
					// echo $sql_recv_summary;

					$dataArray=sql_select($sql_recv_summary);

                    $sql_return=sql_select("SELECT b.JOB_NO,b.PROD_ID, b.STYLE_REF_NO, b.CONS_QUANTITY FROM inv_issue_master a, inv_transaction b WHERE a.id=b.mst_id and  a.COMPANY_ID=$cbo_company_name and b.TRANSACTION_TYPE=3");

                    $return_arr=array();
                    foreach($sql_return as $row){
                        $return_arr[$row["JOB_NO"]][$row["PROD_ID"]][$row["STYLE_REF_NO"]]["CONS_QUANTITY"]=$row["CONS_QUANTITY"];
                    }

                    foreach($dataArray as $row_recv)
                    {
                        if ($i%2==0)
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";
                        $return_qty=$return_arr[$row_recv["JOB_NO"]][$row_recv["PROD_ID"]][$row_recv["STYLE_REF_NO"]]["CONS_QUANTITY"];
                    ?>
                        <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                            <td width="80" align="center"><? echo $buyer_arr[$row_recv[csf('buyer_id')]]; ?></td>
                            <td width="110"><p><? echo $row_recv[csf('job_no')]; ?></p></td>
                            <td width="110"><p><? echo $row_recv[csf('style_ref_no')]; ?></p></td>
                            <td width="110"><p>&nbsp;<? echo  $color_arr[$row_recv[csf('color')]]; ?></p></td>
                            <td width="200"><p>&nbsp;<? echo $row_recv[csf('product_name_details')]; ?></p></td>
                            <td width="80"  align="right"><p>&nbsp;<? echo $row_recv[csf('order_qnty')]; ?></p></td>
                            <td width="70" align="right"><p>&nbsp;<? echo $return_qty; ?></p></td>
                            <td width="130" align="right"><p><? echo $row_recv[csf('order_qnty')]-$return_qty; ?></p></td>
                        </tr>
                        <?
                        $order_qnty+=$row_recv[csf('order_qnty')];
                        $tot_ret_qnty+=$return_qty;
                        $tot_balance_amnt+=$row_recv[csf('order_qnty')]-$return_qty;
                        $i++;
                    }
                ?>
                 <tfoot>
                    <th colspan="5" align="right">Total</th>
                    <th align="right"><? echo number_format($order_qnty,2,'.','');?></th>
                    <th align="right"><? echo number_format($tot_ret_qnty,2,'.','');?></th>
                    <th align="right"><? echo number_format($tot_balance_amnt,2,'.','');?></th>
                </tfoot>
            </table>
			 <?
				echo signature_table(297, str_replace("'","",$cbo_company_name), "1100px");
			 ?>
        </div>
        <!--</fieldset>-->
        <?

    }
    else if($type == 2) // Show Button 2
    {
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
                    $sql_btb_lc_data = "select a.id,a.lc_number, a.supplier_id, a.importer_id, a.lc_date, a.last_shipment_date, a.lc_value
					from com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_master_details c
					where a.id=b.com_btb_lc_master_details_id and c.id=b.pi_id and c.entry_form=165 and c.importer_id=$cbo_company_name and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $btbLc_id_cond_mst $pi_cond_btb $search_cond
					group by a.id, a.lc_number, a.supplier_id, a.importer_id, a.lc_date, a.last_shipment_date, a.lc_value";
					//echo $sql_btb_lc_data;
                    $btbLcData=sql_select($sql_btb_lc_data);
                ?>
                <thead>
                	 <tr>
                        <th colspan="8">BTB LC Details</th>
                    </tr>
                	<tr>
                        <th width="140">Company</th>
                        <th width="140">Importer</th>
                        <th width="140">Store</th>
                        <th width="140">Supplier</th>
                        <th width="140">BTB LC No</th>
                        <th width="100">LC Date</th>
                        <th width="120">Last Shipment Date</th>
                        <th>LC Value</th>
                    </tr>
                </thead>
                <?
				$btb_id_arr=array();
				foreach($btbLcData as $val)
				{
					?>
                    <tr bgcolor="#FFFFFF">
                        <td><p><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></p></td>
                        <td><p><? echo $company_arr[$val[csf('importer_id')]]; ?></p></td>
                        <td><p><? echo $store_arr[str_replace("'","",$cbo_store_name)]; ?>&nbsp;</p></td>
                        <td><p><? echo $supplier_arr[$val[csf('supplier_id')]]; ?></p></td>
                        <td><p><? echo $val[csf('lc_number')]; ?>&nbsp;</p></td>
                        <td align="center"><? echo change_date_format($val[csf('lc_date')]); ?></td>
                        <td align="center"><? echo change_date_format($val[csf('last_shipment_date')]); ?></td>
                        <td align="right"><? echo number_format($val[csf('lc_value')],2); ?></td>
                    </tr>
                	<?
					if ($from_date != "" && $to_date != "")
					{
						$btb_id_arr[$val[csf('id')]]=$val[csf('id')];
					}
				}
				?>
            </table>
            <br>
            <table width="1100" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                <thead>
                    <tr>
                        <th colspan="11">PI Details</th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="120">PI Number</th>
                        <th width="80">PI Date</th>
                        <th width="70">Count</th>
                        <th width="150">Composition</th>
                        <th width="80">Type</th>
                        <th width="90">Color</th>
                        <th width="110">Qnty</th>
                        <th width="90">Rate</th>
                        <th width="120">Value</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <?
                   $i=1; $pi_id_all_array=array(); $pi_with_work_order_array=array(); $pi_name_array=array();  $rate=0; $compos=''; $tot_pi_qnty=0; $tot_pi_amnt=0;
				   if(count($btb_id_arr)>0) $btbLc_id_cond.=" and a.COM_BTB_LC_MASTER_DETAILS_ID in(".implode(",",$btb_id_arr).")";
                   $sql="select b.id, b.pi_number,b.pi_basis_id, b.goods_rcv_status, b.pi_date, b.currency_id, b.remarks, c.color_id, c.count_name, c.yarn_composition_item1, c.yarn_composition_percentage1, c.yarn_composition_item2, c.yarn_composition_percentage2, c.yarn_type, c.work_order_id, sum(c.quantity) as qnty, sum(c.net_pi_amount) as amnt
					from com_btb_lc_pi a, com_pi_master_details b, com_pi_item_details c
					where a.pi_id=b.id and b.id=c.pi_id and b.entry_form=165 and b.importer_id=$cbo_company_name and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $pi_cond $btbLc_id_cond
					group by b.id, c.color_id, c.count_name, c.yarn_composition_item1, c.yarn_composition_percentage1, c.yarn_composition_item2, c.yarn_composition_percentage2, c.yarn_type,c.work_order_id, b.pi_number,b.pi_basis_id, b.goods_rcv_status, b.pi_date, b.currency_id, b.remarks";

                    //echo $sql;
                    $result=sql_select($sql);$pi_mrr_summery=array();
                    foreach($result as $row)
                    {
                        if ($i%2==0)
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";

						if($row[csf('currency_id')]!=1) $ex_rate=1; else $ex_rate=$exchange_rate;

                        $rate=($row[csf('amnt')]/$row[csf('qnty')])/$ex_rate;

                        $compos=$composition[$row[csf('yarn_composition_item1')]]." ".$row[csf('yarn_composition_percentage1')]."%";

                        if($row[csf('yarn_composition_percentage2')]>0)
                        {
                            $compos.=" ".$composition[$row[csf('yarn_composition_item2')]]." ".$row[csf('yarn_composition_percentage2')]."%";
                        }


                        if(!in_array($row[csf('id')],$pi_id_all_array))
                        {
                            $pi_id_all_array[$row[csf('id')]]=$row[csf('id')];
							$pi_name_array[$row[csf('id')]]=$row[csf('pi_number')];
                        }

                        if($row[csf('pi_basis_id')]==1)
                        {
							if($pi_wo_check[$row[csf('id')]][$row[csf('work_order_id')]]=="" && $row[csf('work_order_id')]>0)
							{
								$pi_wo_check[$row[csf('id')]][$row[csf('work_order_id')]]=$row[csf('work_order_id')];
								$pi_with_work_order_array[$row[csf('id')]].= $row[csf('work_order_id')].",";
							}
                        }

						$pi_mrr_summery[$row[csf('id')]][$row[csf('count_name')]][$row[csf('yarn_composition_item1')]][$row[csf('yarn_type')]][$row[csf('color_id')]]["pi_qnty"]+=$row[csf('qnty')];
						$pi_mrr_summery[$row[csf('id')]][$row[csf('count_name')]][$row[csf('yarn_composition_item1')]][$row[csf('yarn_type')]][$row[csf('color_id')]]["pi_amnt"]+=$row[csf('amnt')];
                        $pi_mrr_summery[$row[csf('id')]][$row[csf('count_name')]][$row[csf('yarn_composition_item1')]][$row[csf('yarn_type')]][$row[csf('color_id')]]["pi_basis_id"] = $row[csf('pi_basis_id')];
                        $pi_mrr_summery[$row[csf('id')]][$row[csf('count_name')]][$row[csf('yarn_composition_item1')]][$row[csf('yarn_type')]][$row[csf('color_id')]]["goods_rcv_status"] = $row[csf('goods_rcv_status')];
						 $pi_mrr_summery[$row[csf('id')]][$row[csf('count_name')]][$row[csf('yarn_composition_item1')]][$row[csf('yarn_type')]][$row[csf('color_id')]]["pi_rate"] = $rate;

                        ?>

                        <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="120"><p><? echo $row[csf('pi_number')]; ?></p></td>
                            <td width="80" align="center"><? echo change_date_format($row[csf('pi_date')]); ?></td>
                            <td width="70" align="center"><p>&nbsp;<? echo $count_arr[$row[csf('count_name')]]; ?></p></td>
                            <td width="150"><p><? echo $compos; ?></p></td>
                            <td width="80"><p>&nbsp;<? echo $yarn_type[$row[csf('yarn_type')]]; ?></p></td>
                            <td width="90"><p>&nbsp;<? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
                            <td width="110" align="right"><? echo number_format($row[csf('qnty')],2,'.',''); ?>&nbsp;</td>
                            <td width="90" align="right"><? echo number_format($rate,4,'.',''); ?>&nbsp;</td>
                            <td width="120" align="right"><? echo number_format($row[csf('amnt')],2,'.',''); ?>&nbsp;</td>
                            <td><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
                        </tr>
                    <?

                        $tot_pi_qnty+=$row[csf('qnty')];
                        $tot_pi_amnt+=$row[csf('amnt')];

                        $i++;
                    }
                ?>
                <tfoot>
                    <th colspan="7" align="right">Total</th>
                    <th align="right"> <? echo number_format($tot_pi_qnty,2,'.','');?></th>
                    <th>&nbsp;</th>
                    <th align="right"> <? echo number_format($tot_pi_amnt,2,'.','');?></th>
                    <th>&nbsp;</th>
                </tfoot>
            </table>
            <br>
            <table width="1100" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                <thead>
                   <!-- <tr>
                        <th colspan="11">PI Details Approved History</th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="120">PI Number</th>
                        <th width="80">PI Date</th>
                        <th width="70">Count</th>
                        <th width="150">Composition</th>
                        <th width="80">Type</th>
                        <th width="90">Color</th>
                        <th width="110">Qnty</th>
                        <th width="90">Rate</th>
                        <th width="120">Value</th>
                        <th>Remarks</th>
                    </tr>-->
                </thead>
                <?
                   $i=1; //$pi_id_all_array=array();  $pi_with_work_order_array=array(); $pi_name_array=array();
				   $rate=0; $compos='';  $tot_pi_approve_qnty=$tot_pi_approve_amnt=0;
                   $sql="select b.mst_id, b.pi_number,b.pi_basis_id, b.pi_date, b.currency_id, b.remarks, c.color_id, c.count_name, c.yarn_composition_item1, c.yarn_composition_percentage1, c.yarn_composition_item2, c.yarn_composition_percentage2, c.yarn_type, c.work_order_id, sum(c.quantity) as qnty, sum(c.net_pi_amount) as amnt
					from com_btb_lc_pi a, com_pi_master_details_history b, com_pi_item_details_history c
					where a.pi_id=b.mst_id and b.mst_id=c.pi_id and b.entry_form=165 and b.importer_id=$cbo_company_name and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $pi_cond $btbLc_id_cond group by b.mst_id, c.color_id, c.count_name, c.yarn_composition_item1, c.yarn_composition_percentage1, c.yarn_composition_item2, c.yarn_composition_percentage2, c.yarn_type,c.work_order_id, b.pi_number,b.pi_basis_id, b.pi_date, b.currency_id, b.remarks";

                    //echo $sql;
                    $result=sql_select($sql);//$pi_mrr_summery=array();
                    foreach($result as $row)
                    {
                        if ($i%2==0)
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";

						if($row[csf('currency_id')]!=1) $ex_rate=1; else $ex_rate=$exchange_rate;

                        $rate=($row[csf('amnt')]/$row[csf('qnty')])/$ex_rate;

                        $compos=$composition[$row[csf('yarn_composition_item1')]]." ".$row[csf('yarn_composition_percentage1')]."%";

                        if($row[csf('yarn_composition_percentage2')]>0)
                        {
                            $compos.=" ".$composition[$row[csf('yarn_composition_item2')]]." ".$row[csf('yarn_composition_percentage2')]."%";
                        }


                       /* if(!in_array($row[csf('id')],$pi_id_all_array))
                        {
                            $pi_id_all_array[$row[csf('id')]]=$row[csf('id')];
							$pi_name_array[$row[csf('id')]]=$row[csf('pi_number')];
                        }

                        if($row[csf('pi_basis_id')]==1)
                        {
                            if(!in_array($row[csf('work_order_id')],$pi_with_work_order_array))
                            {
                                if($row[csf('work_order_id')]!="")
                                {
                                    $pi_with_work_order_array[$row[csf('id')]]= $row[csf('work_order_id')];
                                }
                            }
                        }

						$pi_mrr_summery[$row[csf('id')]][$row[csf('count_name')]][$row[csf('yarn_composition_item1')]][$row[csf('yarn_type')]][$row[csf('color_id')]]["pi_qnty"]+=$row[csf('qnty')];
						$pi_mrr_summery[$row[csf('id')]][$row[csf('count_name')]][$row[csf('yarn_composition_item1')]][$row[csf('yarn_type')]][$row[csf('color_id')]]["pi_amnt"]+=$row[csf('amnt')];
                        $pi_mrr_summery[$row[csf('id')]][$row[csf('count_name')]][$row[csf('yarn_composition_item1')]][$row[csf('yarn_type')]][$row[csf('color_id')]]["pi_basis_id"] = $row[csf('pi_basis_id')];*/

                        ?>

                        <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="120"><p><? echo $row[csf('pi_number')]; ?></p></td>
                            <td width="80" align="center"><? echo change_date_format($row[csf('pi_date')]); ?></td>
                            <td width="70" align="center"><p>&nbsp;<? echo $count_arr[$row[csf('count_name')]]; ?></p></td>
                            <td width="150"><p><? echo $compos; ?></p></td>
                            <td width="80"><p>&nbsp;<? echo $yarn_type[$row[csf('yarn_type')]]; ?></p></td>
                            <td width="90"><p>&nbsp;<? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
                            <td width="110" align="right"><? echo number_format($row[csf('qnty')],2,'.',''); ?>&nbsp;</td>
                            <td width="90" align="right"><? echo number_format($rate,4,'.',''); ?>&nbsp;</td>
                            <td width="120" align="right"><? echo number_format($row[csf('amnt')],2,'.',''); ?>&nbsp;</td>
                            <td><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
                        </tr>
                    <?

                        $tot_pi_approve_qnty+=$row[csf('qnty')];
                        $tot_pi_approve_amnt+=$row[csf('amnt')];

                        $i++;
                    }
                ?>
                <tfoot>
                    <?php /*?><th colspan="7" align="right">Total</th>
                    <th align="right"> <? echo number_format($tot_pi_approve_qnty,2,'.','');?></th>
                    <th>&nbsp;</th>
                    <th align="right"> <? echo number_format($tot_pi_approve_amnt,2,'.','');?></th>
                    <th>&nbsp;</th><?php */?>
                </tfoot>
            </table>
            <br>
            <table width="1210" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                <thead>
                    <tr>
                      <th colspan="13">Yarn Receive</th>
                    </tr>
                    <tr>
                        <th width="80">Recv. Date</th>
                        <th width="110">MRR No</th>
                        <th width="110">Store Name</th>
                        <th width="80">Challan No</th>
                        <th width="80">Lot No</th>
                        <th width="70">Count</th>
                        <th width="130">Composition</th>
                        <th width="80">Type</th>
                        <th width="80">Color</th>
                        <th width="90">Qnty</th>
                        <th width="70">Rate</th>
                        <th width="100">Value</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                 <?
                    //$pi_id_all=implode(",",array_flip($pi_id_all_array));
					$pi_id_all=implode(",",$pi_id_all_array);
					//echo "<pre>";print_r($pi_with_work_order_array);die;
					foreach($pi_with_work_order_array as $pi_ids)
					{
						$all_pi_ids.=chop($pi_ids,",").",";
					}
					$pi_workd_order_id_all=chop($all_pi_ids,",");
					//$pi_workd_order_id_all=implode(",",$pi_with_work_order_array);

                    $compos=''; $tot_recv_qnty=0; $tot_recv_amnt=0; $recv_id_array=array(); $recv_pi_array=array(); $pi_data_array=array();
                    //echo $pi_id_all.test;die;
					if ($pi_id_all!='' || $pi_id_all!=0)
					{
                        $sql_recv="select a.id, a.recv_number, a.receive_date,a.receive_basis, a.challan_no, a.currency_id, a.exchange_rate, a.remarks,a.store_id, b.id as rcv_trans_id,b.pi_wo_batch_no,lc_no, (b.order_rate+b.order_ile_cost) as rate, b.order_qnty, b.order_amount,b.prod_id, c.yarn_count_id, c.yarn_type, c.lot, c.color, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_comp_type2nd, c.yarn_comp_percent2nd,c.id as product_id
                        from inv_receive_master a, inv_transaction b, product_details_master c
                        where a.item_category=1 and a.entry_form in(1,248) and a.company_id=$cbo_company_name and a.receive_basis in(1) and a.booking_id in($pi_id_all) and a.id=b.mst_id and b.item_category=1 and b.transaction_type=1 and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.store_id like '$store' ";
					}

					if($pi_workd_order_id_all!="")
					{
						if($sql_recv!="") $sql_recv.="union all ";
                        $sql_recv.="select a.id, a.recv_number, a.receive_date,a.receive_basis,a.challan_no, a.currency_id, a.exchange_rate, a.remarks,a.store_id,b.id as rcv_trans_id, b.pi_wo_batch_no,lc_no, (b.order_rate+b.order_ile_cost) as rate, b.order_qnty, b.order_amount,b.prod_id, c.yarn_count_id,c.yarn_type,c.lot,c.color,c.yarn_comp_type1st,c.yarn_comp_percent1st,c.yarn_comp_type2nd, c.yarn_comp_percent2nd,c.id as product_id from inv_receive_master a, inv_transaction b, product_details_master c, wo_non_order_info_mst d where a.item_category=1 and a.entry_form in(1,248) and a.company_id=$cbo_company_name and a.receive_basis=2 and a.booking_id in($pi_workd_order_id_all) and a.booking_id=d.id and b.pi_wo_batch_no=d.id and a.receive_purpose in(16,43) and a.id=b.mst_id and b.item_category=1 and b.transaction_type=1 and b.prod_id=c.id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and b.store_id like '$store'";
					}
					$sql_recv.=" order by receive_date";
					//echo $sql_recv;

					$dataArray=sql_select($sql_recv);
                    $con = connect();
                    $prod_id_array= array();
                    foreach($dataArray as $row_recv)
                    {
                        if ($i%2==0)
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";

                        $rcv_prod_rate[$row_recv[csf('prod_id')]]['rate']=$row_recv[csf('rate')];

                        if(!$rcv_lc_id_check[$row_recv[csf('lc_no')]])
                        {
                            $rcv_lc_id_check[$row_recv[csf('lc_no')]]=$row_recv[csf('lc_no')];
                            $rcv_lc_id = $row_recv[csf('lc_no')];
                            $rtmpBtblcId=execute_query("insert into tmp_btb_lc_id (userid, btb_lc_id) values ($user_id,$rcv_lc_id)");
                        }

                        if(!$prod_id_check[$row_recv[csf('prod_id')]])
                        {
                            $prod_id_check[$row_recv[csf('prod_id')]]=$row_recv[csf('prod_id')];
                            $prodId = $row_recv[csf('prod_id')];
                            $rtmprodId=execute_query("insert into tmp_prod_id (userid, prod_id) values ($user_id,$prodId)");
                        }

                        if($rtmprodId && $rtmpBtblcId)
                        {
                            oci_commit($con);
                        }

                        $compos=$composition[$row_recv[csf('yarn_comp_type1st')]]." ".$row_recv[csf('yarn_comp_percent1st')]."%";

                        if($row_recv[csf('yarn_comp_percent2nd')]>0)
                        {
                            $compos.=" ".$composition[$row_recv[csf('yarn_comp_type2nd')]]." ".$row_recv[csf('yarn_comp_percent2nd')]."%";
                        }

                        if(!in_array($row_recv[csf('id')],$recv_id_array))
                        {
                            $recv_id_array[$row_recv[csf('id')]]=$row_recv[csf('id')];
							$recv_pi_array[$row_recv[csf('id')]]=$row_recv[csf('pi_wo_batch_no')];
                        }

                       // $prod_id_array1[$row_recv[csf("prod_id")]]=$row_recv[csf("prod_id")];
                        $prod_id_array[$row_recv[csf('prod_id')]]=$row_recv[csf('prod_id')];

                        if($row_recv[csf('currency_id')]!=1) $ex_rate=1; else $ex_rate=$exchange_rate;

                        $pi_data_array[$row_recv[csf('pi_wo_batch_no')]]['rcv_basis'] = $row_recv[csf('receive_basis')];
                        $pi_data_array[$row_recv[csf('pi_wo_batch_no')]]['rcv']+=($row_recv[csf('rate')]*$row_recv[csf('order_qnty')]);
						$pi_data_array[$row_recv[csf('pi_wo_batch_no')]]['rcvQnt']+=$row_recv[csf('order_qnty')];

						//$pi_rcv_mrr_summery[$row_recv[csf('pi_wo_batch_no')]][$row_recv[csf('yarn_count_id')]][$row_recv[csf('yarn_comp_type1st')]][$row_recv[csf('yarn_type')]][$row_recv[csf('color')]]["mrr_id"]+=$row_recv[csf('id')];
						//$pi_data_array[$row_recv[csf('pi_wo_batch_no')]]['rcv']+=$row_recv[csf('order_amount')]/$ex_rate;

                        $pi_rcv_mrr_summery[$row_recv[csf('pi_wo_batch_no')]][$row_recv[csf('yarn_count_id')]][$row_recv[csf('yarn_comp_type1st')]][$row_recv[csf('yarn_type')]][$row_recv[csf('color')]]["mrr_qnty"]+=$row_recv[csf('order_qnty')];

						$pi_rcv_mrr_summery[$row_recv[csf('pi_wo_batch_no')]][$row_recv[csf('yarn_count_id')]][$row_recv[csf('yarn_comp_type1st')]][$row_recv[csf('yarn_type')]][$row_recv[csf('color')]]["mrr_amnt"]+=$row_recv[csf('order_amount')];
						$pi_rcv_mrr_summery[$row_recv[csf('pi_wo_batch_no')]][$row_recv[csf('yarn_count_id')]][$row_recv[csf('yarn_comp_type1st')]][$row_recv[csf('yarn_type')]][$row_recv[csf('color')]]["remarks"]=$row_recv[csf('remarks')];
                        ?>
                        <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                            <td width="80" align="center"><? echo change_date_format($row_recv[csf('receive_date')]); ?></td>
                            <td width="110"><p><? echo $row_recv[csf('recv_number')]; ?></p></td>
                            <td width="110"><p>&nbsp;<? echo $store_arr[$row_recv[csf('store_id')]]; ?></p></td>
                            <td width="80"><p>&nbsp;<? echo $row_recv[csf('challan_no')]; ?></p></td>
                            <td width="80"><p>&nbsp;<? echo $row_recv[csf('lot')]; ?></p></td>
                            <td width="70" align="center"><p>&nbsp;<? echo $count_arr[$row_recv[csf('yarn_count_id')]]; ?></p></td>
                            <td width="130"><p><? echo $compos; ?></p></td>
                            <td width="80"><p>&nbsp;<? echo $yarn_type[$row_recv[csf('yarn_type')]]; ?></p></td>
                            <td width="80"><p>&nbsp;<? echo $color_arr[$row_recv[csf('color')]]; ?></p></td>
                            <td width="90" align="right"><? echo number_format($row_recv[csf('order_qnty')],2,'.',''); ?>&nbsp;</td>
                            <td width="70" align="right"><? echo number_format($row_recv[csf('rate')],2,'.',''); ?>&nbsp;</td>
                            <td width="100" align="right"><? echo number_format(($row_recv[csf('rate')]*$row_recv[csf('order_qnty')]),2,'.',''); ?>&nbsp;</td>
                            <td><p><? echo $row_recv[csf('remarks')];?> </p></td>
                        </tr>
                        <?

                        $tot_recv_qnty+=$row_recv[csf('order_qnty')];
                        $tot_recv_amnt+=($row_recv[csf('rate')]*$row_recv[csf('order_qnty')]);//$row_recv[csf('order_amount')];

                        $i++;
                    }
                ?>
                <tfoot>
                    <th colspan="9" align="right">Total</th>
                    <th align="right"><? echo number_format($tot_recv_qnty,2,'.','');?></th>
                    <th>&nbsp;</th>
                    <th align="right"><? echo number_format($tot_recv_amnt,2,'.','');?></th>
                    <th>&nbsp;</th>
                </tfoot>
            </table>
            <br>
             <table width="1210" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                <thead>
                    <tr>
                      <th colspan="8">Yarn Return</th>
                    </tr>
                    <tr>
                        <th width="100">Return Date</th>
                        <th width="130">Return No</th>
                        <th width="110">Store Name</th>
                        <th width="290">Item Description</th>
                        <th width="110">Qnty</th>
                        <th width="100">Rate</th>
                        <th width="120">Value</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                 <?
                    $tot_retn_qnty=0; $tot_retn_amnt=0;$return_data_arr=array();
                    if(count($recv_id_array)>0)
                    {
						$recv_id_all=implode(",",$recv_id_array);
						$sql_retn="select a.id, a.received_id, a.issue_number, a.issue_date, a.challan_no,b.store_id, cons_rate as rate, b.rcv_rate, b.cons_quantity, b.cons_amount, c.product_name_details,d.receive_date,a.remarks, a.pi_id, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_type, c.color
						from inv_issue_master a, inv_transaction b, product_details_master c,inv_receive_master d
						where a.item_category=1 and a.entry_form=8 and a.company_id=$cbo_company_name and a.received_id in($recv_id_all) and a.id=b.mst_id and a.received_id=d.id and b.item_category=1 and b.transaction_type=3 and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
						//echo $sql_retn;die;
                        $dataRtArray=sql_select($sql_retn);
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

							$exchange_rate=set_conversion_rate( 2, $conversion_date );
							if($row_retn[csf('rcv_rate')]>0)
							{
								$pi_string=$row_retn[csf('pi_id')]."*".$row_retn[csf('yarn_count_id')]."*".$row_retn[csf('yarn_comp_type1st')]."*".$row_retn[csf('yarn_type')]."*".$row_retn[csf('color')];
								$pi_rate=$pi_mrr_summery[$row_retn[csf('pi_id')]][$row_retn[csf('yarn_count_id')]][$row_retn[csf('yarn_comp_type1st')]][$row_retn[csf('yarn_type')]][$row_retn[csf('color')]]["pi_rate"];
								if($pi_rate>0)
								{
									$rate=$pi_rate;
								}
								else
								{
									$rate=$row_retn[csf('rcv_rate')]/$exchange_rate;
								}
							}
							else
							{
								$rate=$row_retn[csf('rate')]/$exchange_rate;
							}

							$amnt=$row_retn[csf('cons_quantity')]*$rate;

                            $rcv_return_qty_arr[$row_retn[csf('received_id')]] = $row_retn[csf('cons_quantity')];

                        ?>
                            <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                                <td width="100" align="center"><? echo change_date_format($row_retn[csf('issue_date')]); ?></td>
                                <td width="130" title="<?= "pi string = ".$pi_string?>"><p><? echo $row_retn[csf('issue_number')]; ?></p></td>
                                <td width="110" ><p>&nbsp;<? echo $store_arr[$row_retn[csf('store_id')]]; ?></p></td>
                                <td width="290"><p>&nbsp;<? echo $row_retn[csf('product_name_details')]; ?></p></td>
                                <td width="110" align="right"><? echo number_format($row_retn[csf('cons_quantity')],2,'.',''); ?>&nbsp;</td>
                                <td width="100" align="right" title="<? echo 'exchange rate = '.$exchange_rate;?>"><? echo number_format($rate,2,'.',''); ?>&nbsp;</td>
                                <td width="120" align="right"><? echo number_format($amnt,2,'.',''); ?>&nbsp;</td>
                                <td><p><? echo $row_retn[csf('remarks')];?> &nbsp;</p></td>
                            </tr>
                         <?
                            $pi_id=$recv_pi_array[$row_retn[csf('received_id')]];
                            $tot_retn_qnty+=$row_retn[csf('cons_quantity')];
                            $tot_retn_amnt+=$amnt;
                            $pi_data_array[$pi_id]['rtn']+=$amnt;
                            $pi_data_array[$pi_id]['rtnQnt']+=$row_retn[csf('cons_quantity')];
							$return_data_arr[$pi_id][$row_retn[csf('yarn_count_id')]][$row_retn[csf('yarn_comp_type1st')]][$row_retn[csf('yarn_type')]][$row_retn[csf('color')]]["rtn_qnty"]+=$row_retn[csf('cons_quantity')];
							$return_data_arr[$pi_id][$row_retn[csf('yarn_count_id')]][$row_retn[csf('yarn_comp_type1st')]][$row_retn[csf('yarn_type')]][$row_retn[csf('color')]]["rtn_amnt"]+=$amnt;
                            $i++;
                        }
                    }
                    //print_r($pi_data_array);
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
						$sql_retn="select a.received_id, a.pi_id, a.issue_number, a.issue_date, a.challan_no,a.remarks,b.store_id, (b.order_rate+b.order_ile_cost) as ord_rate, b.cons_rate as rate, b.rcv_rate, b.cons_quantity, b.cons_amount, c.product_name_details, a.pi_id, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_type, c.color
						from inv_issue_master a, inv_transaction b, product_details_master c
						where a.item_category=1 and a.entry_form=8 and a.company_id=$cbo_company_name and a.pi_id in($pi_id_all) and a.id=b.mst_id and b.item_category=1 and b.transaction_type=3 and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $rtn_cond";
						//echo $sql_retn;
						$dataRtArray=sql_select($sql_retn);
						foreach($dataRtArray as $row_retn)
						{
							if ($i%2==0)
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";

							//$rate=$row_retn[csf('rate')]/$exchange_rate;
							$pi_string=$row_retn[csf('pi_id')]."*".$row_retn[csf('yarn_count_id')]."*".$row_retn[csf('yarn_comp_type1st')]."*".$row_retn[csf('yarn_type')]."*".$row_retn[csf('color')];
							$pi_rate=$pi_mrr_summery[$row_retn[csf('pi_id')]][$row_retn[csf('yarn_count_id')]][$row_retn[csf('yarn_comp_type1st')]][$row_retn[csf('yarn_type')]][$row_retn[csf('color')]]["pi_rate"];
							if($row_retn[csf('rcv_rate')]>0)
							{
								if($pi_rate>0)
								{
									$rate=$pi_rate;
								}
								else
								{
									$rate=$row_retn[csf('rcv_rate')]/$exchange_rate;
								}

							}
							else
							{
								$rate=$row_retn[csf('rate')]/$exchange_rate;
							}

							$amnt=$row_retn[csf('cons_quantity')]*$rate;


                            $rcv_return_qty_arr[$row_retn[csf('received_id')]] = $row_retn[csf('cons_quantity')];
						?>
							<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
								<td width="100" align="center"><? echo change_date_format($row_retn[csf('issue_date')]); ?></td>
								<td width="130" title="<?= "pi string = ".$pi_string?>"><p><? echo $row_retn[csf('issue_number')]; ?></p></td>
								<td width="110" ><p>&nbsp;<? echo $store_arr[$row_retn[csf('store_id')]]; ?></p></td>
								<td width="290"><p>&nbsp;<? echo $row_retn[csf('product_name_details')]; ?></p></td>
								<td width="110" align="right"><? echo number_format($row_retn[csf('cons_quantity')],2,'.',''); ?>&nbsp;</td>
								<td width="100" align="right" title="<? echo 'exchange_rate='.$exchange_rate;?>"><? echo number_format($rate,2,'.',''); ?>&nbsp;</td>
								<td width="120" align="right"><? echo number_format($amnt,2,'.',''); ?>&nbsp;</td>
								<td><p><? echo $row_retn[csf('remarks')];?> &nbsp;</p></td>
							</tr>
						<?
							$pi_id=$row_retn[csf('pi_id')];
							$tot_retn_qnty+=$row_retn[csf('cons_quantity')];
							$tot_retn_amnt+=$amnt;
							$pi_data_array[$pi_id]['rtn']+=$amnt;
							$pi_data_array[$pi_id]['rtnQnt']+=$row_retn[csf('cons_quantity')];

							$return_data_arr[$pi_id][$row_retn[csf('yarn_count_id')]][$row_retn[csf('yarn_comp_type1st')]][$row_retn[csf('yarn_type')]][$row_retn[csf('color')]]["rtn_qnty"]+=$row_retn[csf('cons_quantity')];
							$return_data_arr[$pi_id][$row_retn[csf('yarn_count_id')]][$row_retn[csf('yarn_comp_type1st')]][$row_retn[csf('yarn_type')]][$row_retn[csf('color')]]["rtn_amnt"]+=$amnt;

							$i++;
						}
					}

                    $total_balance_qty = ($tot_pi_qnty+$tot_retn_qnty-$tot_recv_qnty);
                    $total_balance_value = ($tot_pi_amnt+$tot_retn_amnt-$tot_recv_amnt);
                ?>
                <tfoot>
                    <tr>
                        <th colspan="4" align="right">Total</th>
                        <th align="right"><? echo number_format($tot_retn_qnty,2,'.','');?></th>
                        <th>&nbsp;</th>
                        <th align="right"><? echo number_format($tot_retn_amnt,2,'.','');?></th>
                        <th>&nbsp;</th>
                    </tr>
                    <tr>
                        <th colspan="4" align="right">Balance</th>
                        <th align="right" title="<? echo $tot_pi_qnty.test; ?>"><? echo number_format($total_balance_qty,2,'.','');?></th>
                        <th>&nbsp;</th>
                        <th align="right"><? echo number_format($total_balance_value,2,'.','');?></th>
                        <th>&nbsp;</th>
                    </tr>
                </tfoot>
            </table>
            <br>
            <table width="850" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                <thead>
                        <tr>

							<th width="140" rowspan="2">PI Number</th>
							<th width="" colspan="2">Receive</th>
							<th width="" colspan="2">Return</th>
							<th width="" colspan="2">Payable</th>
                            <th width="" colspan="4">Acceptance Details</th>
						</tr>
						<tr>
							<th width="100">Qnty</th>
							<th width="100">Value</th>
							<th width="100">Qnty</th>
							<th width="100">Value</th>
							<th width="100">Qnty</th>
							<th width="100">Value</th>
                            <th width="100">Date</th>
							<th width="100">Invoice No.</th>
							<th width="100">Value</th>
							<th width="100">Bal. Value</th>
						</tr>
                </thead>
                <?

					if ($pi_id_all!='' || $pi_id_all!=0)
					{
						/*echo "select m.id as pi_id, m.pi_basis_id, m.goods_rcv_status, listagg(n.work_order_id, ', ') within group (order by n.work_order_id) work_order_id, a.company_acc_date, b.current_acceptance_value as acceptance_value, a.invoice_no
						from  com_pi_master_details m
                        left join com_pi_item_details n on m.id=n.pi_id and n.status_active=1 and n.is_deleted=0
                        left join com_import_invoice_dtls b on m.id=b.pi_id and b.status_active=1 and b.is_deleted=0
                        left join com_import_invoice_mst a on a.id=b.import_invoice_id where m.id in($pi_id_all)
						group by m.id, m.pi_basis_id, m.goods_rcv_status, a.company_acc_date, b.current_acceptance_value, a.invoice_no
						order by m.id";*/
                        if($db_type==0){
                            $accep_query = "select m.id as pi_id, m.pi_basis_id, m.goods_rcv_status, group_concat(n.work_order_id) as work_order_id, a.company_acc_date, b.current_acceptance_value as acceptance_value, a.invoice_no
                            from  com_pi_master_details m
                            left join com_pi_item_details n on m.id=n.pi_id and n.status_active=1 and n.is_deleted=0
                            left join com_import_invoice_dtls b on m.id=b.pi_id and b.status_active=1 and b.is_deleted=0
                            left join com_import_invoice_mst a on a.id=b.import_invoice_id where m.id in($pi_id_all)
                            group by m.id, m.pi_basis_id, m.goods_rcv_status, a.company_acc_date, b.current_acceptance_value, a.invoice_no
                            order by m.id";
                        }else if($db_type ==2){
                            $accep_query = "select m.id as pi_id, m.pi_basis_id, m.goods_rcv_status, listagg(n.work_order_id, ',') within group (order by n.work_order_id) work_order_id, a.company_acc_date, b.current_acceptance_value as acceptance_value, a.invoice_no
                            from  com_pi_master_details m
                            left join com_pi_item_details n on m.id=n.pi_id and n.status_active=1 and n.is_deleted=0
                            left join com_import_invoice_dtls b on m.id=b.pi_id and b.status_active=1 and b.is_deleted=0
                            left join com_import_invoice_mst a on a.id=b.import_invoice_id where m.id in($pi_id_all)
                            group by m.id, m.pi_basis_id, m.goods_rcv_status, a.company_acc_date, b.current_acceptance_value, a.invoice_no
                            order by m.id";
                        }
                        //echo $accep_query;//die;
                        $accep_sql=sql_select($accep_query);
						foreach($accep_sql as $row)
						{
							$accep_breakdown_data[$row[csf("pi_id")]]++;
							$acceptance_arr[$row[csf("pi_id")]]["acceptance_value"]+=$row[csf("acceptance_value")];
							if($row[csf("goods_rcv_status")]==1){ //After Goods Received
                                if($wo_check[$row[csf("pi_id")]][$row[csf("work_order_id")]]=="")
                                {
                                    $acceptance_arr[$row[csf("pi_id")]]["work_order_id"] =$row[csf("work_order_id")];
                                }
                            }else{ //Before Goods Received
                                if($wo_check[$row[csf("pi_id")]][$row[csf("pi_id")]]=="")
                                {
                                    $acceptance_arr[$row[csf("pi_id")]]["pi_id"].=$row[csf("pi_id")].",";
                                }
                            }
						}
					}
                    //echo $count_row[1261].jahid;die;
						$payble_value=0;
						$tot_payble_value=0;
						$yet_to_accept=0;
						$tot_accept_value=0;
						$tot_yet_to_accept=0;
						$total_receive_value=0;
						$total_receive_rcvQnt=0;
						$total_return_value=0;
						$total_return_rtnQnt=0;
						$tot_payble_Qnty=0;
					/*foreach($pi_name_array as $key=>$value)
					{
					}*/
					//echo "<pre>";print_r($pi_data_array);die;
                    foreach($accep_sql as $row)
                    {
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
							<?
                           // print_r($pi_data_array);
                            //echo $row[csf("pi_basis_id")];
							if($temp_pi[$row[csf("pi_id")]]=="")
							{
								$payble_value=$yet_to_accept=$recv_qnty=$recv_value=$recv_rtn_qnty=$recv_rtn_value=0;$piwo_id ="";
                                if($row[csf("goods_rcv_status")]==1)
                                {
									$piwo_id =chop($acceptance_arr[$row[csf("pi_id")]]["work_order_id"],",");
									$piwo_id_arr = explode(",",chop($acceptance_arr[$row[csf("pi_id")]]["work_order_id"],","));
									//echo "<pre>";print_r($piwo_id_arr);die;
									foreach($piwo_id_arr as $wo_id)
									{
										$recv_qnty+=$pi_data_array[$wo_id*1]['rcvQnt'];
										$recv_value+=$pi_data_array[$wo_id*1]['rcv'];
									}
									//echo $recv_qnty;die;
                                }
								else
								{
									$recv_qnty = $pi_data_array[$row[csf("pi_id")]]['rcvQnt'];
									$recv_value = $pi_data_array[$row[csf("pi_id")]]['rcv'];
									$recv_rtn_qnty=$pi_data_array[$row[csf("pi_id")]]['rtnQnt'];
									$recv_rtn_value=$pi_data_array[$row[csf("pi_id")]]['rtn'];
                                    $piwo_id =  $row[csf("pi_id")];
                                }


								$payble_Qnty=$recv_qnty-$recv_rtn_qnty;
								$payble_value=$recv_value-$recv_rtn_value;
								$yet_to_accept=$payble_value-$acceptance_arr[$row[csf("pi_id")]]["acceptance_value"];

								$total_receive_value+=$recv_value;
								$total_receive_rcvQnt+=$recv_qnty;
								$total_return_value+=$recv_rtn_value;
								$total_return_rtnQnt+=$recv_rtn_qnty;
								$tot_payble_value+=$payble_value;
								$tot_payble_Qnty+=$payble_Qnty;
								$tot_accept_value+=$acceptance_arr[$row[csf("pi_id")]]["acceptance_value"];
								$tot_yet_to_accept+=$yet_to_accept;
								?>
								<td width="140" rowspan="<? echo $accep_breakdown_data[$row[csf("pi_id")]];?>"><p><? echo $pi_name_array[$row[csf("pi_id")]]; ?></p></td>
								<td width="100"  align="right" rowspan="<? echo $accep_breakdown_data[$row[csf("pi_id")]];?>"><? echo number_format($recv_qnty,2,'.',''); ?>&nbsp;</td>
                                <td width="100"  align="right" rowspan="<? echo $accep_breakdown_data[$row[csf("pi_id")]];?>" title="<? echo $piwo_id." PI ".$row[csf("pi_id")]; ?>"><? echo number_format($recv_value,2,'.',''); ?>&nbsp;</td>
                                <td width="100" align="right" rowspan="<? echo $accep_breakdown_data[$row[csf("pi_id")]];?>"><? echo number_format($recv_rtn_qnty,2,'.',''); ?>&nbsp;</td>
								<td width="100" align="right" rowspan="<? echo $accep_breakdown_data[$row[csf("pi_id")]];?>"><? echo number_format($recv_rtn_value,2,'.',''); ?>&nbsp;</td>
								<td width="100" align="right" rowspan="<? echo $accep_breakdown_data[$row[csf("pi_id")]];?>"><? echo number_format($payble_Qnty,2,'.',''); ?>&nbsp;</td>
                                <td width="100" align="right" rowspan="<? echo $accep_breakdown_data[$row[csf("pi_id")]];?>"><? echo number_format($payble_value,2,'.',''); ?>&nbsp;</td>
								<?
							}
							?>
							<td width="100" align="center"><? if($row[csf("company_acc_date")]!="" && $row[csf("company_acc_date")]!="0000-00-00") echo change_date_format($row[csf("company_acc_date")]);?>&nbsp;</td>
                            <td width="100" align="center"><? echo $row[csf("invoice_no")];?>&nbsp;</td>
							<td width="100" align="right"><? echo number_format($row[csf("acceptance_value")],2,'.',''); ?>&nbsp;</td>
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
                ?>
                <tfoot>
                    <th align="right">Total</th>
                    <th align="right"><? echo number_format($total_receive_rcvQnt,2,'.',''); ?></th>
                    <th align="right"><? echo number_format($total_receive_value,2,'.',''); ?></th>
                    <th align="right"><? echo number_format($total_return_rtnQnt,2,'.',''); ?></th>
                    <th align="right"><? echo number_format($total_return_value,2,'.',''); ?></th>
                    <th align="right"><? echo number_format($tot_payble_Qnty,2,'.',''); ?></th>
                    <th align="right"><? echo number_format($tot_payble_value,2,'.',''); ?></th>
                    <th align="right">&nbsp;</th>
                    <th align="right">&nbsp;</th>
                    <th align="right"><? echo number_format($tot_accept_value,2,'.',''); ?></th>
                    <th align="right"><? echo number_format($tot_yet_to_accept,2,'.',''); ?></th>
                </tfoot>
            </table>

            <br>
            <table width="1150" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                <thead>
                    <tr>
                      <th colspan="13">Summary Of PI and Receive</th>
                    </tr>
                    <tr>
                    	<th colspan="8">PI Summary</th>
                        <th colspan="5">Receive Summary</th>
                    </tr>
                    <tr>
                        <th width="100">PI Number</th>
                        <th width="70">Count</th>
                        <th width="130">Composition</th>
                        <th width="80">Yarn Type</th>
                        <th width="70">Color</th>
                        <th width="80">Require Qnty</th>
                        <th width="80">Rate</th>
                        <th width="90">Value</th>
                        <th width="80">Receive Qnty</th>
                        <th width="80">Receive Blance</th>
                        <th width="80">Rate</th>
                        <th width="90">Value</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                <?

				foreach( $pi_mrr_summery as $pi_wo_id=>$pi_data )
				{
					foreach($pi_data as $y_count_id=>$y_count_data)
					{
						foreach($y_count_data as $y_comp_id=>$y_comp_data)
						{
							foreach($y_comp_data as $y_type_id=>$y_type_data)
							{
								foreach($y_type_data as $y_color_id=>$color_data)
								{
									if ($i%2==0)
										$bgcolor="#E9F3FF";
									else
										$bgcolor="#FFFFFF";

									//echo $color_data["pi_qnty"]."==".$color_data["mrr_qnty"];

                                    $pi_basis_id = $pi_mrr_summery[$pi_wo_id][$y_count_id][$y_comp_id][$y_type_id][$y_color_id]["pi_basis_id"];
                                    $goods_rcv_status = $pi_mrr_summery[$pi_wo_id][$y_count_id][$y_comp_id][$y_type_id][$y_color_id]["goods_rcv_status"];
									$mrr_qnty = $mrr_amnt =0;
									$work_order_id="";
                                    if($goods_rcv_status==1)
                                    {
                                        $work_order_id_arr = explode(",",chop($pi_with_work_order_array[$pi_wo_id],","));
										foreach($work_order_id_arr as $wo_id)
										{
											$mrr_qnty += ($pi_rcv_mrr_summery[$wo_id][$y_count_id][$y_comp_id][$y_type_id][$y_color_id]["mrr_qnty"]-$return_data_arr[$work_order_id][$y_count_id][$y_comp_id][$y_type_id][$y_color_id]["rtn_qnty"]);
											$mrr_amnt += ( $pi_rcv_mrr_summery[$wo_id][$y_count_id][$y_comp_id][$y_type_id][$y_color_id]["mrr_amnt"]-$return_data_arr[$work_order_id][$y_count_id][$y_comp_id][$y_type_id][$y_color_id]["rtn_amnt"] );
											$work_order_id .= $wo_id.",";
										}
                                    }else{
                                        $work_order_id = $pi_wo_id;
										$mrr_qnty = ($pi_rcv_mrr_summery[$work_order_id][$y_count_id][$y_comp_id][$y_type_id][$y_color_id]["mrr_qnty"]-$return_data_arr[$work_order_id][$y_count_id][$y_comp_id][$y_type_id][$y_color_id]["rtn_qnty"]);
										$mrr_amnt = ( $pi_rcv_mrr_summery[$work_order_id][$y_count_id][$y_comp_id][$y_type_id][$y_color_id]["mrr_amnt"]-$return_data_arr[$work_order_id][$y_count_id][$y_comp_id][$y_type_id][$y_color_id]["rtn_amnt"] );
                                    }
									$work_order_id=chop($work_order_id,",");
									//echo $work_order_id."=".$goods_rcv_status."<br>";
									//$mrr_qnty = ($pi_rcv_mrr_summery[$work_order_id][$y_count_id][$y_comp_id][$y_type_id][$y_color_id]["mrr_qnty"]-$pi_data_array[$pi_wo_id]['rtnQnt']);
									//$mrr_amnt = ( $pi_rcv_mrr_summery[$work_order_id][$y_count_id][$y_comp_id][$y_type_id][$y_color_id]["mrr_amnt"]-$pi_data_array[$pi_wo_id]['rtn'] );

									?>
                                    <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                                        <td title="<? echo $pi_wo_id; ?>"><? echo $pi_name_array[$pi_wo_id]; ?></td>
                                        <td title="<? echo $y_count_id; ?>"><? echo $count_arr[$y_count_id]; ?></td>
                                        <td title="<? echo $y_comp_id; ?>"><? echo $composition[$y_comp_id]; ?></td>
                                        <td title="<? echo $y_type_id; ?>"><? echo $yarn_type[$y_type_id]; ?></td>
                                        <td title="<? echo $y_color_id; ?>"><? echo $color_arr[$y_color_id]; ?></td>
                                        <td align="right"><? echo number_format($color_data["pi_qnty"],2); ?></td>
                                        <td align="right"><? if($color_data["pi_qnty"]>0 && $color_data["pi_amnt"]>0) echo number_format(($color_data["pi_amnt"]/$color_data["pi_qnty"]),2); else echo "0.00"; ?></td>
                                        <td align="right"><? echo number_format($color_data["pi_amnt"],2); ?></td>
                                        <td align="right"><? echo number_format($mrr_qnty,2); ?></td>
                                        <td align="right">
                                        <?
                                            $mrr_balance=($color_data["pi_qnty"]*1)-$mrr_qnty;
                                            echo number_format($mrr_balance,2);
                                        ?>
                                        </td>
                                        <td align="right"><? if($mrr_qnty>0 && $mrr_amnt>0) echo number_format(($mrr_amnt/$mrr_qnty),2); else echo "0.00"; ?></td>
                                        <td align="right"><? echo number_format($mrr_amnt,2); ?></td>
                                        <td><? echo $color_data["remarks"]; ?></td>
                                    </tr>
                                    <?
									$total_pi_qnty+=$color_data["pi_qnty"];
									$total_pi_amnt+=$color_data["pi_amnt"];
									$total_mrr_qnty+=$mrr_qnty;
									$total_mrr_amnt+=$mrr_amnt;
									$total_mrr_balance+=$mrr_balance;
									$i++;
								}
							}
						}
					}
				}
				?>
                </tbody>
                <tfoot>
                    <th align="right" colspan="5">Total:</th>
                    <th align="right"><? echo number_format($total_pi_qnty,2,'.',''); ?></th>
                    <th>&nbsp;</th>
                    <th align="right"><? echo number_format($total_pi_amnt,2,'.',''); ?></th>
                    <th align="right"><? echo number_format($total_mrr_qnty,2); ?></th>
                    <th align="right"><? echo number_format($total_mrr_balance,2); ?></th>
                    <th>&nbsp;</th>
                    <th align="right"><? echo number_format($total_mrr_amnt,2,'.',''); ?></th>
                    <th align="right">&nbsp;</th>
                </tfoot>
        	</table>

            <br>
            <table width="1260" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                <thead>
                    <tr>
                      <th colspan="16">Yarn Issued Details</th>
                    </tr>
                    <tr>
                        <th width="80">Trans. Date</th>
                        <th width="110">Trans. No</th>
                        <th width="110">Trans. Type</th>
                        <th width="80">Issue Purpose</th>
                        <th width="120">Trans. To</th>
                        <th width="120">Store Name</th>
                        <th width="130">Challan No</th>
                        <th width="80">Lot No</th>
                        <th width="80">Count</th>
                        <th width="90">Composition</th>
                        <th width="70">Type</th>
                        <th width="100">Color</th>
                        <th width="80">Qnty</th>
                        <th width="80">Rate</th>
                        <th width="100">Value</th>
                        <th width="100">Remarks</th>
                    </tr>
                </thead>
                <tbody>

                <?
                   $prod_sql = "select a.id,a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot from product_details_master a, tmp_prod_id b where a.id=b.prod_id and a.company_id=$cbo_company_name and a.item_category_id=1 and a.status_active=1 and a.is_deleted=0";
                   //echo $prod_sql;die;
                   $prod_result = sql_select($prod_sql);

                   $prod_data = array();
                   foreach ($prod_result as $row) {
                       $prod_data[$row[csf('id')]]['lot']=$row[csf('lot')];
                       $prod_data[$row[csf('id')]]['yarn_count_id']=$row[csf('yarn_count_id')];
                       $prod_data[$row[csf('id')]]['yarn_comp_type1st']=$row[csf('yarn_comp_type1st')];
                       $prod_data[$row[csf('id')]]['yarn_comp_percent1st']=$row[csf('yarn_comp_percent1st')];
                       $prod_data[$row[csf('id')]]['yarn_comp_type2nd']=$row[csf('yarn_comp_type2nd')];
                       $prod_data[$row[csf('id')]]['yarn_comp_percent2nd']=$row[csf('yarn_comp_percent2nd')];
                       $prod_data[$row[csf('id')]]['yarn_type']=$row[csf('yarn_type')];
                       $prod_data[$row[csf('id')]]['color']=$row[csf('color')];
                   }

                    $yarnIssuesql= "select a.issue_number as trans_no,0 as transfer_criteria,a.knit_dye_company as company_name,a.issue_date,a.issue_purpose as purpose,a.challan_no,a.knit_dye_source,a.loan_party,b.store_id,b.prod_id,b.transaction_type,a.remarks,sum(b.cons_quantity) as issue_qnty from inv_issue_master a, inv_transaction b,tmp_prod_id c,tmp_btb_lc_id d where a.id=b.mst_id and b.prod_id=c.prod_id and b.btb_lc_id=d.btb_lc_id and b.transaction_type=2 and b.item_category=1 and a.entry_form=3 and a.company_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.issue_number ,a.knit_dye_company,a.issue_date,a.issue_purpose,a.challan_no,a.knit_dye_source,a.loan_party,b.store_id,b.prod_id,b.transaction_type,a.remarks
                    union all
                    select a.transfer_system_id as trans_no,a.transfer_criteria,a.to_company as company_name,a.transfer_date as issue_date,a.purpose,a.challan_no,0 as knit_dye_source, 0 as loan_party, c.store_id,c.prod_id,c.transaction_type,a.remarks,sum(b.transfer_qnty) as issue_qnty from inv_item_transfer_mst a,inv_item_transfer_dtls b, inv_transaction c,tmp_prod_id d, tmp_btb_lc_id e where a.id=b.mst_id and b.mst_id=c.mst_id and b.from_prod_id=c.prod_id and c.prod_id=d.prod_id and c.btb_lc_id=e.btb_lc_id and a.company_id=$cbo_company_name and a.transfer_criteria=1 and c.transaction_type in (6) and c.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and b.is_deleted=0  and c.status_active=1  and c.is_deleted=0 group by a.transfer_system_id ,a.transfer_criteria,a.to_company ,a.transfer_date,a.purpose,a.challan_no, c.store_id,c.prod_id,c.transaction_type,a.remarks
                    ";

                    //echo $yarnIssuesql;die;
                    $issue_result=sql_select($yarnIssuesql);
                    foreach ($issue_result as $row)
                    {
                        if ($i%2==0)
                        $bgcolor="#E9F3FF";
                        else
                        $bgcolor="#FFFFFF";

                        //$trans_no = $trsns_data[$row[csf('id')]]['trans_no'];

                        $composition_string = $composition[$prod_data[$row[csf('prod_id')]]['yarn_comp_type1st']] . " " . $prod_data[$row[csf('prod_id')]]['yarn_comp_percent1st'] . " ";

                        if($prod_data[$row[csf('prod_id')]]['yarn_comp_type2nd'])
                        {
                            $composition_string.= $composition[$prod_data[$row[csf('prod_id')]]['yarn_comp_type2nd']]. " ";
                        }
                        if($prod_data[$row[csf('prod_id')]]['yarn_comp_percent2nd'])
                        {
                            $composition_string.= $prod_data[$row[csf('prod_id')]]['yarn_comp_percent2nd'];
                        }

                        $com_name = '';
                        if($row[csf('knit_dye_source')] == 1)
                        {
                            $com_name = $company_arr[$row[csf('company_name')]];
                        }
                        elseif($row[csf('knit_dye_source')] == 3)
                        {
                            $com_name = $supplier_arr[$row[csf('company_name')]];
                        }
                        elseif($row[csf('purpose')]==5 )
                        {
                            $com_name = $supplier_arr[$row[csf('loan_party')]];
                        }
                        elseif( $row[csf('transfer_criteria')] == 1)
                        {
                            $com_name = $company_arr[$row[csf('company_name')]];
                        }

                        $purpose_name = '';
                        if($row[csf('purpose')]){
                            $purpose_name = $yarn_issue_purpose[$row[csf('purpose')]];
                        }else{
                            $purpose_name = $item_transfer_criteria[$row[csf('transfer_criteria')]];
                        }
                        $rate = $rcv_prod_rate[$row[csf('prod_id')]]['rate'];
                        $value = $rate*$row[csf('issue_qnty')];
                        ?>

                        <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                            <td width="80" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
                            <td width="110" align="center"><p><? echo $row[csf('trans_no')]; ?></p></td>
                            <td width="110" align="center"><p><? echo $transaction_type[$row[csf('transaction_type')]]; ?></p></td>
                            <td width="80" align="center"><p><? echo $purpose_name;?></p></td>
                            <td width="120"><p><? echo $com_name; ?></p></td>
                            <td width="120" align="center"><p><? echo $store_arr[$row[csf('store_id')]]; ?></p></td>
                            <td width="130" align="center"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td width="80"><p><? echo $prod_data[$row[csf('prod_id')]]['lot']; ?></p></td>
                            <td width="80" align="center"><p><? echo $yarn_count_arr[$prod_data[$row[csf('prod_id')]]['yarn_count_id']]; ?></p></td>
                            <td width="90" align="center"><? echo $composition_string; ?></td>
                            <td width="70" align="center"><? echo $yarn_type[$prod_data[$row[csf('prod_id')]]['yarn_type']]; ?></td>
                            <td width="100" align="center"><? echo $color_arr[$prod_data[$row[csf('prod_id')]]['color']]; ?></td>
                            <td width="80" align="right"><? echo $row[csf('issue_qnty')]; ?></td>
                            <td width="80" align="right"><? echo number_format($rate,2,'.',''); ?></td>
                            <td width="100" align="right"><? echo number_format($value,2); ?></td>
                            <td width="100"><p><? echo substr($row[csf('remarks')],0,10); ?></p></td>
                        </tr>

                        <?
                        $total_issue_quantity+= $row[csf('issue_qnty')];
                        $total_value +=$value;
                        $i++;
                    }
                    ?>
                </tbody>
                <tfoot>
                    <th align="right" colspan="12">Total:</th>
                    <th align="right"><? echo number_format($total_issue_quantity,2,'.',''); ?></th>
                    <th>&nbsp;</th>
                    <th align="right"><? echo number_format($total_value,2,'.',''); ?></th>

                    <th align="right">&nbsp;</th>
                </tfoot>
            </table>

            <?
				echo signature_table(297, str_replace("'","",$cbo_company_name), "1100px");
			 ?>
        </div>
        <!--</fieldset>-->
        <?
    }
    else  if ($type == 3) // Show 3 Button
    {
        $com_dtls = fnc_company_location_address($cbo_company_name, 0, 2);
	    ?>
        <fieldset style="width:1420px">
    	<div style="width:100%; margin:5px;" align="left">

            <table width="1430" cellpadding="0" cellspacing="0" id="caption">
                <thead>
                     <tr  class="" style="border:none;">
                        <td colspan="16" align="center" style="border:none; font-size:xx-large;">
                           <? echo $com_dtls[0]; ?>
                        </td>
                    </tr>
                    <tr class="" style="border:none;">
                        <td colspan="16" align="center" style="border:none;font-size:16px; font-weight:bold" ><? echo $com_dtls[1];?></td>
                    </tr>
                    <tr class="" style="border:none;">
                        <td colspan="16" align="center" style="border:none;font-size:16px; font-weight:bold" >LC Wise Due Yarn Summary </td>
                    </tr>

                </thead>
            </table>

            <table width="1430" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                <thead>
                    <tr>
                        <th width="30">SL.</th>
                        <th width="160">Party Name</th>
                        <th width="110">LC / No</th>
                        <th width="80">PI / No</th>
                        <th width="80">LC Date</th>
                        <th width="70">Count</th>
                        <th width="130">Composition</th>
                        <th width="100">Type</th>
                        <th width="100">Color</th>
                        <th width="100">Req. Qty.</th>
                        <th width="10">Qnty</th>
                        <th width="100">Due Balance Qty</th>
                        <th width="80">Rate</th>
                        <th width="80">Rcv. Value</th>
                        <th width="80">Status </th>
                        <th width="80">LC Details Link</th>
                    </tr>
                </thead>
                 <?

                    $sql_btb_lc_data = "select a.id, a.lc_number, a.supplier_id, a.importer_id, a.lc_date, a.last_shipment_date, a.lc_value
                    from com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_master_details c
                    where a.id=b.com_btb_lc_master_details_id and c.id=b.pi_id and c.entry_form=165 and c.importer_id=$cbo_company_name and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $btbLc_id_cond_mst $pi_cond_btb $search_cond group by a.id, a.lc_number, a.supplier_id, a.importer_id, a.lc_date, a.last_shipment_date, a.lc_value";
					//echo $sql_btb_lc_data;
                    $btbLcData=sql_select($sql_btb_lc_data);
                    $lc_info_arr = array();
                    $lc_ids_arr = array();
                    foreach ($btbLcData as $row)
                    {
                        $lc_info_arr[$row[csf('id')]]['lc_number']=$row[csf('lc_number')];
                        $lc_info_arr[$row[csf('id')]]['lc_date']=$row[csf('lc_date')];
                        $lc_info_arr[$row[csf('id')]]['supplier_id']=$row[csf('supplier_id')];
                        $lc_info_arr[$row[csf('id')]]['lc_value']+=$row[csf('lc_value')];
                        //array_push($lc_ids_arr,$row[csf('id')]);
						$lc_ids_arr[$row[csf('id')]]=$row[csf('id')];
                    }
					//echo count($lc_ids_arr);;print_r($lc_ids_arr);die;
					$con = connect();
					execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ENTRY_FORM=2256",1);
					oci_commit($con);
					fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 2256, 1, $lc_ids_arr, $empty_arr);
					//echo count($lc_ids_arr);;print_r($lc_ids_arr);die;

                   	$pi_id_all_array=array(); $pi_with_work_order_array=array(); $pi_name_array=array();  $rate=0; $compos=''; $tot_pi_qnty=0; $tot_pi_amnt=0;
                   	$sql="SELECT b.id, b.pi_number,b.pi_basis_id, b.goods_rcv_status, b.pi_date, b.currency_id, b.remarks, c.color_id, c.count_name, c.yarn_composition_item1, c.yarn_composition_percentage1, c.yarn_composition_item2, c.yarn_composition_percentage2, c.yarn_type, c.work_order_id, sum(c.quantity) as qnty, sum(c.net_pi_amount) as amnt, a.com_btb_lc_master_details_id, b.net_total_amount
					from GBL_TEMP_ENGINE p, com_btb_lc_pi a, com_pi_master_details b, com_pi_item_details c
					where p.REF_VAL=a.COM_BTB_LC_MASTER_DETAILS_ID and p.REF_FROM=1 and p.ENTRY_FORM=2256 and a.pi_id=b.id and b.id=c.pi_id and b.entry_form=165 and b.importer_id=$cbo_company_name and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $pi_cond $btbLc_id_cond
					group by b.id, c.color_id, c.count_name, c.yarn_composition_item1, c.yarn_composition_percentage1, c.yarn_composition_item2, c.yarn_composition_percentage2, c.yarn_type,c.work_order_id, b.pi_number,b.pi_basis_id, b.goods_rcv_status, b.pi_date, b.currency_id, b.remarks, a.com_btb_lc_master_details_id,b.net_total_amount";
					//echo $sql;die;
                    $result=sql_select($sql);$pi_mrr_summery=array();
                    $pi_total=array();
                    $pi_total_new=array();
                    $pi_amount_arr=array();
                    foreach($result as $row)
                    {

						if($row[csf('currency_id')]!=1) $ex_rate=1; else $ex_rate=$exchange_rate;

                        $rate=($row[csf('amnt')]/$row[csf('qnty')])/$ex_rate;

                        $compos=$composition[$row[csf('yarn_composition_item1')]]." ".$row[csf('yarn_composition_percentage1')]."%";

                        if($row[csf('yarn_composition_percentage2')]>0)
                        {
                            $compos.=" ".$composition[$row[csf('yarn_composition_item2')]]." ".$row[csf('yarn_composition_percentage2')]."%";
                        }


                        if(!in_array($row[csf('id')],$pi_id_all_array))
                        {
                            $pi_id_all_array[$row[csf('id')]]=$row[csf('id')];
							$pi_name_array[$row[csf('id')]]=$row[csf('pi_number')];
                        }

                        if($row[csf('pi_basis_id')]==1)
                        {
							if($pi_wo_check[$row[csf('id')]][$row[csf('work_order_id')]]=="" && $row[csf('work_order_id')]>0)
							{
								$pi_wo_check[$row[csf('id')]][$row[csf('work_order_id')]]=$row[csf('work_order_id')];
								$pi_with_work_order_array[$row[csf('id')]].= $row[csf('work_order_id')].",";
							}
                        }

                        $pi_mrr_summery[$row[csf('id')]][$row[csf('count_name')]][$row[csf('yarn_composition_item1')]][$row[csf('yarn_type')]][$row[csf('color_id')]]["lc_master_details_id"] =$row[csf('com_btb_lc_master_details_id')];
                        $pi_mrr_summery[$row[csf('id')]][$row[csf('count_name')]][$row[csf('yarn_composition_item1')]][$row[csf('yarn_type')]][$row[csf('color_id')]]["pi_number"] =$row[csf('pi_number')];
                        $pi_mrr_summery[$row[csf('id')]][$row[csf('count_name')]][$row[csf('yarn_composition_item1')]][$row[csf('yarn_type')]][$row[csf('color_id')]]["req_qnty"] +=$row[csf('qnty')];
                        $pi_mrr_summery[$row[csf('id')]][$row[csf('count_name')]][$row[csf('yarn_composition_item1')]][$row[csf('yarn_type')]][$row[csf('color_id')]]["amnt"] +=$row[csf('amnt')];

                        $pi_amount_arr[$row[csf('id')]]["amnt"] +=$row[csf('amnt')];

                        $wo_mrr_summery[$row[csf('work_order_id')]][$row[csf('count_name')]][$row[csf('yarn_composition_item1')]][$row[csf('yarn_type')]][$row[csf('color_id')]]["lc_master_details_id"] =$row[csf('com_btb_lc_master_details_id')];
                        $wo_mrr_summery[$row[csf('work_order_id')]][$row[csf('count_name')]][$row[csf('yarn_composition_item1')]][$row[csf('yarn_type')]][$row[csf('color_id')]]["pi_number"] =$row[csf('pi_number')];
                        $wo_mrr_summery[$row[csf('work_order_id')]][$row[csf('count_name')]][$row[csf('yarn_composition_item1')]][$row[csf('yarn_type')]][$row[csf('color_id')]]["req_qnty"] +=$row[csf('qnty')];
                    }


                    //$pi_id_all=implode(",",array_flip($pi_id_all_array));
					//$pi_id_all=implode(",",$pi_id_all_array);
					$all_pi_wo_ids=$all_pi_wo_id_arr=array();
					foreach($pi_with_work_order_array as $pi_id=>$wo_ids)
					{
						$all_pi_wo_ids=array_unique(explode(",",chop($wo_ids,",")));
						foreach($all_pi_wo_ids as $pi_wo_id)
						{
							$all_pi_wo_id_arr[$pi_wo_id]=$pi_wo_id;
						}
					}
					//$pi_workd_order_id_all=chop($all_pi_ids,",");
					//$pi_workd_order_id_all=implode(",",$pi_with_work_order_array);

                    $compos=''; $tot_recv_qnty=0; $tot_recv_amnt=0; $recv_id_array=array(); $recv_pi_array=array(); $pi_data_array=array();
                    //echo $pi_id_all.test;die;
					if(count($pi_id_all_array)>0)
					{
						fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 2256, 2, $pi_id_all_array, $empty_arr);
                        $sql_recv="SELECT a.receive_basis,a.company_id,
                        b.pi_wo_batch_no, (b.order_rate+b.order_ile_cost) as rate, sum(b.order_qnty) as order_qnty, sum(b.order_amount) as order_amount, c.yarn_count_id, c.yarn_type,  c.color,
                        c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_comp_type2nd, c.yarn_comp_percent2nd
                        from GBL_TEMP_ENGINE p, inv_receive_master a, inv_transaction b, product_details_master c
						where p.REF_VAL=a.booking_id and p.REF_FROM=2 and p.ENTRY_FORM=2256 and a.item_category=1 and a.entry_form in(1,248) and
                        a.company_id=$cbo_company_name and a.receive_basis in(1) and a.id=b.mst_id and b.item_category=1 and b.transaction_type=1
                        and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.store_id like '$store'
                        group by a.receive_basis,a.company_id, b.order_rate,b.order_ile_cost,
                        b.pi_wo_batch_no,   c.yarn_count_id, c.yarn_type,  c.color,
                        c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_comp_type2nd, c.yarn_comp_percent2nd ";
					}

					if(count($all_pi_wo_id_arr)>0)
					{
						fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 2256, 3, $all_pi_wo_id_arr, $empty_arr);
                        if($sql_recv!="") $sql_recv.="union all ";
                        $sql_recv.="SELECT a.receive_basis,a.company_id, b.pi_wo_batch_no, (b.order_rate+b.order_ile_cost) as rate, sum(b.order_qnty) as order_qnty, sum(b.order_amount) as order_amount, c.yarn_count_id,c.yarn_type, c.color,c.yarn_comp_type1st,c.yarn_comp_percent1st,c.yarn_comp_type2nd, c.yarn_comp_percent2nd
						from GBL_TEMP_ENGINE p, inv_receive_master a, inv_transaction b, product_details_master c, wo_non_order_info_mst d
						where p.REF_VAL=a.booking_id and p.REF_FROM=3 and p.ENTRY_FORM=2256 and a.item_category=1 and a.entry_form in(1,248) and a.company_id=$cbo_company_name and a.receive_basis=2 and a.booking_id=d.id and b.pi_wo_batch_no=d.id and a.receive_purpose in(16,43) and a.id=b.mst_id and b.item_category=1 and b.transaction_type=1 and b.prod_id=c.id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and b.store_id like '$store'
						group by a.receive_basis,a.company_id, b.pi_wo_batch_no, b.order_rate,b.order_ile_cost, c.yarn_count_id,c.yarn_type, c.color,c.yarn_comp_type1st,c.yarn_comp_percent1st,c.yarn_comp_type2nd, c.yarn_comp_percent2nd ";
					}
					$sql_recv.=" order by pi_wo_batch_no";
					//echo $sql_recv;die;
                    $dataArray=sql_select($sql_recv);
                    $i=1; $receive_qty=0;
                    $req_total_arr=array();
                    $pi_receive_amount_arr=array();
                    foreach($dataArray as $row){
                        $req_total_arr[$row[csf('booking_id')]]['total_val']=$row[csf('rate')]*$row[csf('order_qnty')];
                        $pi_receive_amount_arr[$row[csf('pi_wo_batch_no')]]['amt']+=$row[csf('order_amount')];

                    }
                    $pi_amount=$pi_receive_amount=0;

					execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id."  and ENTRY_FORM=2256",1);
					oci_commit($con);
					disconnect($con);

                    foreach($dataArray as $row_recv)
                    {
                        //var_dump($row_recv);
                        if ($i%2==0)
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";

                        $compos=$composition[$row_recv[csf('yarn_comp_type1st')]]." ".$row_recv[csf('yarn_comp_percent1st')]."%";

                        if($row_recv[csf('yarn_comp_percent2nd')]>0)
                        {
                            $compos.=" ".$composition[$row_recv[csf('yarn_comp_type2nd')]]." ".$row_recv[csf('yarn_comp_percent2nd')]."%";
                        }

                        if(!in_array($row_recv[csf('id')],$recv_id_array))
                        {
                            $recv_id_array[$row_recv[csf('id')]]=$row_recv[csf('id')];
							$recv_pi_array[$row_recv[csf('id')]]=$row_recv[csf('pi_wo_batch_no')];
                        }

                        //if($row_recv[csf('currency_id')]!=1) $ex_rate=1; else $ex_rate=$exchange_rate;

                        $pi_data_array[$row_recv[csf('pi_wo_batch_no')]]['rcv_basis'] = $row_recv[csf('receive_basis')];
                        $pi_data_array[$row_recv[csf('pi_wo_batch_no')]]['rcv']+=($row_recv[csf('rate')]*$row_recv[csf('order_qnty')]);
						$pi_data_array[$row_recv[csf('pi_wo_batch_no')]]['rcvQnt']+=$row_recv[csf('order_qnty')];

                        if($row_recv[csf('receive_basis')]==1)
                        {
                            $arr = $pi_mrr_summery;
                        }else  if($row_recv[csf('receive_basis')]==2)
                        {
                            $arr = $wo_mrr_summery;
                        }

                        $lc_ammount_val=$lc_info_arr[$arr[$row_recv[csf('pi_wo_batch_no')]][$row_recv[csf('yarn_count_id')]][$row_recv[csf('yarn_comp_type1st')]][$row_recv[csf('yarn_type')]][$row_recv[csf('color')]]["lc_master_details_id"]]['lc_value'];

                        $pi_amount=$pi_amount_arr[$row_recv[csf('pi_wo_batch_no')]]["amnt"];
                        $pi_receive_amount=$pi_receive_amount_arr[$row_recv[csf('pi_wo_batch_no')]]['amt'];

                        //echo  $pi_amount."__".$pi_receive_amount.'##';


                        if( $pi_receive_amount<$pi_amount){
                            $status="Running";
                        }else{
                            $status="Close";
                        }
                    ?>
                        <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                            <td width="30" align="center"><? echo $i; ?></td>
                            <td width="160" align="center" title="<? echo $arr[$row_recv[csf('pi_wo_batch_no')]][$row_recv[csf('yarn_count_id')]][$row_recv[csf('yarn_comp_type1st')]][$row_recv[csf('yarn_type')]][$row_recv[csf('color')]]["lc_master_details_id"];?>"><p><? echo $supplier_arr[$lc_info_arr[$arr[$row_recv[csf('pi_wo_batch_no')]][$row_recv[csf('yarn_count_id')]][$row_recv[csf('yarn_comp_type1st')]][$row_recv[csf('yarn_type')]][$row_recv[csf('color')]]["lc_master_details_id"]]['supplier_id']]; ?></p></td>
                            <td width="110" align="center"><p><? echo $lc_info_arr[$arr[$row_recv[csf('pi_wo_batch_no')]][$row_recv[csf('yarn_count_id')]][$row_recv[csf('yarn_comp_type1st')]][$row_recv[csf('yarn_type')]][$row_recv[csf('color')]]["lc_master_details_id"]]['lc_number']; ?></p></td>
                            <td width="80" align="center"><p><? echo $arr[$row_recv[csf('pi_wo_batch_no')]][$row_recv[csf('yarn_count_id')]][$row_recv[csf('yarn_comp_type1st')]][$row_recv[csf('yarn_type')]][$row_recv[csf('color')]]["pi_number"]; ?></p></td>
                            <td width="80" align="center"><p><? echo change_date_format($lc_info_arr[$arr[$row_recv[csf('pi_wo_batch_no')]][$row_recv[csf('yarn_count_id')]][$row_recv[csf('yarn_comp_type1st')]][$row_recv[csf('yarn_type')]][$row_recv[csf('color')]]["lc_master_details_id"]]['lc_date']); ?></p></td>
                            <td width="70" align="center"><p><? echo $count_arr[$row_recv[csf('yarn_count_id')]]; ?></p></td>
                            <td width="130" align="center"><p><? echo $compos; ?></p></td>
                            <td width="100" align="center"><p><? echo $yarn_type[$row_recv[csf('yarn_type')]]; ?></p></td>
                            <td width="100" align="center"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
                            <td width="80" align="right"><? $req_qnty = $arr[$row_recv[csf('pi_wo_batch_no')]][$row_recv[csf('yarn_count_id')]][$row_recv[csf('yarn_comp_type1st')]][$row_recv[csf('yarn_type')]][$row_recv[csf('color')]]["req_qnty"]; echo number_format($req_qnty,2,'.',''); ?></td>
                            <td width="90" align="right"><? echo number_format($row_recv[csf('order_qnty')],2,'.',''); ?></td>
                            <td width="70" align="right"><? $due_balance = $req_qnty-$row_recv[csf('order_qnty')]; echo number_format( $due_balance,2,'.',''); ?></td>
                            <td width="80" align="right"><? echo number_format($row_recv[csf('rate')],2,'.',''); ?></td>
                            <td width="80"  align="right"><? echo number_format($row_recv[csf('rate')]*$row_recv[csf('order_qnty')],2,'.',''); ?></td>
                            <td width="80"  align="center"><? echo $status;?></td>
                            <td width="80"   align="right"> <a href='##' onClick="fnc_btb_lc_details('<? echo $row_recv[csf('company_id')];?>','<? echo $lc_info_arr[$arr[$row_recv[csf('pi_wo_batch_no')]][$row_recv[csf('yarn_count_id')]][$row_recv[csf('yarn_comp_type1st')]][$row_recv[csf('yarn_type')]][$row_recv[csf('color')]]["lc_master_details_id"]]['lc_number']; ?>','Loan Issue','issue_popup_details')"> <? echo "LC Details Link" ?> </a> </td>
                        </tr>
                    <?
                        $tot_req_qnty+= $req_qnty;
                        $tot_recv_qnty+=$row_recv[csf('order_qnty')];
                        $rec_val_total+=$row_recv[csf('rate')]*$row_recv[csf('order_qnty')];

                        $i++;
                    }
                ?>
                <tfoot>
                    <th colspan="9" align="right"><b>Total :</b></th>
                    <th align="right"><? echo number_format($tot_req_qnty,2,'.','');?></th>
                    <th align="right"><? echo number_format($tot_recv_qnty,2,'.','');?></th>
                    <th align="right"><? echo number_format($tot_req_qnty-$tot_recv_qnty,2,'.','');?></th>
                    <th>&nbsp;</th>
                    <th align="right"> <?= number_format($rec_val_total,2)?></th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                </tfoot>
            </table>
            <br>
            <?
				echo signature_table(297, str_replace("'","",$cbo_company_name), "1100px");
			 ?>
        </div>
        </fieldset>
        <?

    }
    ?>

    <?

    $r_id1=execute_query("delete from tmp_prod_id where userid=$user_id");
    $r_id2=execute_query("delete from tmp_btb_lc_id where userid=$user_id");
    if($r_id1 && $r_id2) $flag=1; else $flag=0;
    if($flag==1)
    {
        oci_commit($con);
    }

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

if ($action=="issue_popup_details")
{
	echo load_html_head_contents("Receive Details", "../../../../", 1, 1, $unicode, '', '');
	extract($_REQUEST);
	$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$item_group_arr = return_library_array("select id, item_name from lib_item_group","id","item_name");
     $company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );


	?>
	<fieldset style="width:110px">
        <table width="1100" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
            <?
                $sql_btb_lc_data = "SELECT a.id,a.lc_number, a.supplier_id, a.importer_id, a.lc_date, a.last_shipment_date, a.lc_value
                from com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_master_details c
                where a.id=b.com_btb_lc_master_details_id and c.id=b.pi_id and c.entry_form=165 and c.importer_id=$company and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.lc_number='$lc_number'
                group by a.id, a.lc_number, a.supplier_id, a.importer_id, a.lc_date, a.last_shipment_date, a.lc_value";
                //echo $sql_btb_lc_data;die;
                $btbLcData=sql_select($sql_btb_lc_data);
            ?>
                <thead>
                	 <tr>
                        <th colspan="8">BTB LC Details</th>
                    </tr>
                	<tr>
                        <th width="140">Company</th>
                        <th width="140">Importer</th>
                        <th width="140">Store</th>
                        <th width="140">Supplier</th>
                        <th width="140">BTB LC No</th>
                        <th width="100">LC Date</th>
                        <th width="120">Last Shipment Date</th>
                        <th>LC Value</th>
                    </tr>
                </thead>
                <?
				$btb_id_arr=array();
				foreach($btbLcData as $val)
				{
					?>
                    <tr bgcolor="#FFFFFF">
                        <td><p><? echo $company_arr[str_replace("'","",$company)]; ?></p></td>
                        <td><p><? echo $company_arr[$val[csf('importer_id')]]; ?></p></td>
                        <td><p><? echo $store_arr[str_replace("'","",$cbo_store_name)]; ?>&nbsp;</p></td>
                        <td><p><? echo $supplier_arr[$val[csf('supplier_id')]]; ?></p></td>
                        <td><p><? echo $val[csf('lc_number')]; ?>&nbsp;</p></td>
                        <td align="center"><? echo change_date_format($val[csf('lc_date')]); ?></td>
                        <td align="center"><? echo change_date_format($val[csf('last_shipment_date')]); ?></td>
                        <td align="right"><? echo number_format($val[csf('lc_value')],2); ?></td>
                    </tr>
                	<?
					// if ($from_date != "" && $to_date != "")
					// {
						$btb_id_arr[$val[csf('id')]]=$val[csf('id')];
					//}
				}
				?>
        </table>
        <br>
            <table width="1100" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                <thead>
                    <tr>
                        <th colspan="11">PI Details</th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="120">PI Number</th>
                        <th width="80">PI Date</th>
                        <th width="70">Count</th>
                        <th width="150">Composition</th>
                        <th width="80">Type</th>
                        <th width="90">Color</th>
                        <th width="110">Qnty</th>
                        <th width="90">Rate</th>
                        <th width="120">Value</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <?
                   $i=1; $pi_id_all_array=array(); $pi_with_work_order_array=array(); $yarn_composition_item=array();  $pi_name_array=array();  $rate=0; $compos=''; $tot_pi_qnty=0; $tot_pi_amnt=0;
				   if(count($btb_id_arr)>0) $btbLc_id_cond.=" and a.COM_BTB_LC_MASTER_DETAILS_ID in(".implode(",",$btb_id_arr).")";
                   $sql="select b.id, b.pi_number,b.pi_basis_id, b.goods_rcv_status, b.pi_date, b.currency_id, b.remarks, c.color_id, c.count_name, c.yarn_composition_item1, c.yarn_composition_percentage1, c.yarn_composition_item2, c.yarn_composition_percentage2, c.yarn_type, c.work_order_id, sum(c.quantity) as qnty, sum(c.net_pi_amount) as amnt
					from com_btb_lc_pi a, com_pi_master_details b, com_pi_item_details c
					where a.pi_id=b.id and b.id=c.pi_id and b.entry_form=165 and b.importer_id=$company and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $pi_cond $btbLc_id_cond
					group by b.id, c.color_id, c.count_name, c.yarn_composition_item1, c.yarn_composition_percentage1, c.yarn_composition_item2, c.yarn_composition_percentage2, c.yarn_type,c.work_order_id, b.pi_number,b.pi_basis_id, b.goods_rcv_status, b.pi_date, b.currency_id, b.remarks";

                    //echo $sql;
                    $result=sql_select($sql);$pi_mrr_summery=array();
                    foreach($result as $row)
                    {
                        if ($i%2==0)
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";

						if($row[csf('currency_id')]!=1) $ex_rate=1; else $ex_rate=$exchange_rate;

                        $rate=($row[csf('amnt')]/$row[csf('qnty')])/$ex_rate;

                        $compos=$composition[$row[csf('yarn_composition_item1')]]." ".$row[csf('yarn_composition_percentage1')]."%";

                        if($row[csf('yarn_composition_percentage2')]>0)
                        {
                            $compos.=" ".$composition[$row[csf('yarn_composition_item2')]]." ".$row[csf('yarn_composition_percentage2')]."%";
                        }


                        if(!in_array($row[csf('id')],$pi_id_all_array))
                        {
                            $pi_id_all_array[$row[csf('id')]]=$row[csf('id')];
							$pi_name_array[$row[csf('id')]]=$row[csf('pi_number')];
                        }

                        if($row[csf('pi_basis_id')]==1)
                        {
							if($pi_wo_check[$row[csf('id')]][$row[csf('work_order_id')]]=="" && $row[csf('work_order_id')]>0)
							{
								$pi_wo_check[$row[csf('id')]][$row[csf('work_order_id')]]=$row[csf('work_order_id')];
								$pi_with_work_order_array[$row[csf('id')]].= $row[csf('work_order_id')].",";
							}
                            if($pi_wo_check2[$row[csf('id')]][$row[csf('work_order_id')]][$row[csf('yarn_composition_item1')]]=="" && $row[csf('work_order_id')]>0)
							{
                                $pi_wo_check2[$row[csf('id')]][$row[csf('work_order_id')]][$row[csf('yarn_composition_item1')]]=$row[csf('yarn_composition_item1')];
								$wo_comp_data[$row[csf('work_order_id')]][$row[csf('yarn_composition_item1')]]=$row[csf('yarn_composition_item1')];
                                $yarn_composition_item[$row[csf('id')]].= $row[csf('yarn_composition_item1')].",";
							}
                        }

						$pi_mrr_summery[$row[csf('id')]][$row[csf('count_name')]][$row[csf('yarn_composition_item1')]][$row[csf('yarn_type')]][$row[csf('color_id')]]["pi_qnty"]+=$row[csf('qnty')];
						$pi_mrr_summery[$row[csf('id')]][$row[csf('count_name')]][$row[csf('yarn_composition_item1')]][$row[csf('yarn_type')]][$row[csf('color_id')]]["pi_amnt"]+=$row[csf('amnt')];
                        $pi_mrr_summery[$row[csf('id')]][$row[csf('count_name')]][$row[csf('yarn_composition_item1')]][$row[csf('yarn_type')]][$row[csf('color_id')]]["pi_basis_id"] = $row[csf('pi_basis_id')];
                        $pi_mrr_summery[$row[csf('id')]][$row[csf('count_name')]][$row[csf('yarn_composition_item1')]][$row[csf('yarn_type')]][$row[csf('color_id')]]["goods_rcv_status"] = $row[csf('goods_rcv_status')];
						 $pi_mrr_summery[$row[csf('id')]][$row[csf('count_name')]][$row[csf('yarn_composition_item1')]][$row[csf('yarn_type')]][$row[csf('color_id')]]["pi_rate"] = $rate;

                        ?>

                        <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="120"><p><? echo $row[csf('pi_number')]; ?></p></td>
                            <td width="80" align="center"><? echo change_date_format($row[csf('pi_date')]); ?></td>
                            <td width="70" align="center"><p>&nbsp;<? echo $count_arr[$row[csf('count_name')]]; ?></p></td>
                            <td width="150"><p><? echo $compos; ?></p></td>
                            <td width="80"><p>&nbsp;<? echo $yarn_type[$row[csf('yarn_type')]]; ?></p></td>
                            <td width="90"><p>&nbsp;<? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
                            <td width="110" align="right"><? echo number_format($row[csf('qnty')],2,'.',''); ?></td>
                            <td width="90" align="right"><? if(number_format($row[csf('qnty')],2,'.','')>0) echo number_format($rate,4,'.',''); else echo "0.00"; ?></td>
                            <td width="120" align="right"><? if(number_format($row[csf('qnty')],2,'.','')>0) echo number_format($row[csf('amnt')],2,'.',''); else echo "0.00"; ?></td>
                            <td><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
                        </tr>
                    <?

                        if(number_format($row[csf('qnty')],2,'.','')>0)
						{
							$tot_pi_qnty+=$row[csf('qnty')];
							$tot_pi_amnt+=$row[csf('amnt')];
						}

                        $i++;
                    }
                ?>
                <tfoot>
                    <th colspan="7" align="right">Total</th>
                    <th align="right"> <? echo number_format($tot_pi_qnty,2,'.','');?></th>
                    <th>&nbsp;</th>
                    <th align="right"> <? echo number_format($tot_pi_amnt,2,'.','');?></th>
                    <th>&nbsp;</th>
                </tfoot>
            </table>
            <br>

            <table width="1280" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                <thead>
                    <tr>
                      <th colspan="14">Yarn Receive</th>
                    </tr>
                    <tr>
                        <th width="80">Recv. Date</th>
                        <th width="110">MRR No</th>
                        <th width="110"> Already posted in account</th>
                        <th width="110">Store Name</th>
                        <th width="80">Challan No</th>
                        <th width="80">Lot No</th>
                        <th width="70">Count</th>
                        <th width="130">Composition</th>
                        <th width="80">Type</th>
                        <th width="80">Color</th>
                        <th width="90">Qnty</th>
                        <th width="70">Rate</th>
                        <th width="100">Value</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                 <?
                    //$pi_id_all=implode(",",array_flip($pi_id_all_array));
					$pi_id_all=implode(",",$pi_id_all_array);
					//echo "<pre>";print_r($pi_with_work_order_array);die;
					foreach($pi_with_work_order_array as $pi_ids)
					{
						$all_pi_ids.=chop($pi_ids,",").",";
					}
					$pi_workd_order_id_all=chop($all_pi_ids,",");
					//$pi_workd_order_id_all=implode(",",$pi_with_work_order_array);

                    foreach($yarn_composition_item as $comps_ids)
					{
						$all_comps_ids.=chop($comps_ids,",").",";
					}
					$pi_workd_order_comps_ids_all=chop($all_comps_ids,",");

                    $compos=''; $tot_recv_qnty=0; $tot_recv_amnt=0; $recv_id_array=array(); $recv_pi_array=array(); $pi_data_array=array();
                    //echo $pi_id_all.test;die;
					if ($pi_id_all!='' || $pi_id_all!=0)
					{
                        $sql_recv="select a.id, a.recv_number, a.receive_date,a.receive_basis, a.challan_no, a.currency_id, a.exchange_rate, a.remarks,a.store_id ,a.is_posted_account , b.pi_wo_batch_no, (b.order_rate+b.order_ile_cost) as rate, b.order_qnty, b.order_amount, c.yarn_count_id, c.yarn_type, c.lot, c.color, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_comp_type2nd, c.yarn_comp_percent2nd
                        from inv_receive_master a, inv_transaction b, product_details_master c
                        where a.item_category=1 and a.entry_form in(1,248) and a.company_id=$company and a.receive_basis in(1) and a.booking_id in($pi_id_all) and a.id=b.mst_id and b.item_category=1 and b.transaction_type=1 and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ";
					}

					if($pi_workd_order_id_all!="")
					{
                        if($pi_workd_order_comps_ids_all=='') $y_comp_cond=""; else $y_comp_cond="and c.yarn_comp_type1st in($pi_workd_order_comps_ids_all) ";

						if($sql_recv!="") $sql_recv.="union all ";
                        $sql_recv.="select a.id, a.recv_number, a.receive_date,a.receive_basis,a.challan_no, a.currency_id, a.exchange_rate, a.remarks,a.store_id,a.is_posted_account, b.pi_wo_batch_no, (b.order_rate+b.order_ile_cost) as rate, b.order_qnty, b.order_amount, c.yarn_count_id,c.yarn_type,c.lot,c.color,c.yarn_comp_type1st,c.yarn_comp_percent1st,c.yarn_comp_type2nd, c.yarn_comp_percent2nd from inv_receive_master a, inv_transaction b, product_details_master c, wo_non_order_info_mst d where a.item_category=1 and a.entry_form in(1,248) and a.company_id=$company and a.receive_basis=2 and a.booking_id in($pi_workd_order_id_all) $y_comp_cond and a.booking_id=d.id and b.pi_wo_batch_no=d.id and a.receive_purpose in(16,43) and a.id=b.mst_id and b.item_category=1 and b.transaction_type=1 and b.prod_id=c.id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 ";
					}
					$sql_recv.=" order by receive_date";
					//echo $sql_recv;

					$dataArray=sql_select($sql_recv);

                    foreach($dataArray as $row_recv)
                    {
                        if ($i%2==0)
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";

                        $display_data=1;
						if($row_recv[csf('receive_basis')]==2)
						{
							if($wo_comp_data[$row_recv[csf('pi_wo_batch_no')]][$row_recv[csf('yarn_comp_type1st')]]=="")
							{
								$display_data=0;
							}
						}

						$compos=$composition[$row_recv[csf('yarn_comp_type1st')]]." ".$row_recv[csf('yarn_comp_percent1st')]."%";

                        if($row_recv[csf('yarn_comp_percent2nd')]>0)
                        {
                            $compos.=" ".$composition[$row_recv[csf('yarn_comp_type2nd')]]." ".$row_recv[csf('yarn_comp_percent2nd')]."%";
                        }

                        if(!in_array($row_recv[csf('id')],$recv_id_array))
                        {
                            $recv_id_array[$row_recv[csf('id')]]=$row_recv[csf('id')];
							$recv_pi_array[$row_recv[csf('id')]]=$row_recv[csf('pi_wo_batch_no')];
                        }

                        if($row_recv[csf('currency_id')]!=1) $ex_rate=1; else $ex_rate=$exchange_rate;

                        $pi_data_array[$row_recv[csf('pi_wo_batch_no')]]['rcv_basis'] = $row_recv[csf('receive_basis')];
                        $pi_data_array[$row_recv[csf('pi_wo_batch_no')]]['rcv']+=($row_recv[csf('rate')]*$row_recv[csf('order_qnty')]);
						$pi_data_array[$row_recv[csf('pi_wo_batch_no')]]['rcvQnt']+=$row_recv[csf('order_qnty')];

						//$pi_rcv_mrr_summery[$row_recv[csf('pi_wo_batch_no')]][$row_recv[csf('yarn_count_id')]][$row_recv[csf('yarn_comp_type1st')]][$row_recv[csf('yarn_type')]][$row_recv[csf('color')]]["mrr_id"]+=$row_recv[csf('id')];
						//$pi_data_array[$row_recv[csf('pi_wo_batch_no')]]['rcv']+=$row_recv[csf('order_amount')]/$ex_rate;

                        $pi_rcv_mrr_summery[$row_recv[csf('pi_wo_batch_no')]][$row_recv[csf('yarn_count_id')]][$row_recv[csf('yarn_comp_type1st')]][$row_recv[csf('yarn_type')]][$row_recv[csf('color')]]["mrr_qnty"]+=$row_recv[csf('order_qnty')];

						$pi_rcv_mrr_summery[$row_recv[csf('pi_wo_batch_no')]][$row_recv[csf('yarn_count_id')]][$row_recv[csf('yarn_comp_type1st')]][$row_recv[csf('yarn_type')]][$row_recv[csf('color')]]["mrr_amnt"]+=$row_recv[csf('order_amount')];
						$pi_rcv_mrr_summery[$row_recv[csf('pi_wo_batch_no')]][$row_recv[csf('yarn_count_id')]][$row_recv[csf('yarn_comp_type1st')]][$row_recv[csf('yarn_type')]][$row_recv[csf('color')]]["remarks"]=$row_recv[csf('remarks')];
                    ?>
                        <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                            <td width="80" align="center"><? echo change_date_format($row_recv[csf('receive_date')]); ?></td>
                            <td width="110"><p><? echo $row_recv[csf('recv_number')]; ?></p></td>
                            <td width="110"><p>&nbsp;<? echo ($row_recv[csf('is_posted_account')]==1)? "Yes":"No" ; ?></p></td>
                            <td width="110"><p>&nbsp;<? echo $store_arr[$row_recv[csf('store_id')]]; ?></p></td>
                            <td width="80"><p>&nbsp;<? echo $row_recv[csf('challan_no')]; ?></p></td>
                            <td width="80"><p>&nbsp;<? echo $row_recv[csf('lot')]; ?></p></td>
                            <td width="70" align="center"><p>&nbsp;<? echo $count_arr[$row_recv[csf('yarn_count_id')]]; ?></p></td>
                            <td width="130"><p><? echo $compos; ?></p></td>
                            <td width="80"><p>&nbsp;<? echo $yarn_type[$row_recv[csf('yarn_type')]]; ?></p></td>
                            <td width="80"><p>&nbsp;<? echo $color_arr[$row_recv[csf('color')]]; ?></p></td>
                            <td width="90" align="right"><? echo number_format($row_recv[csf('order_qnty')],2,'.',''); ?></td>
                            <td width="70" align="right"><? echo number_format($row_recv[csf('rate')],2,'.',''); ?></td>
                            <td width="100" align="right"><? echo number_format(($row_recv[csf('rate')]*$row_recv[csf('order_qnty')]),2,'.',''); ?></td>
                            <td><p><? echo $row_recv[csf('remarks')];?> </p></td>
                        </tr>
                    <?

                        $tot_recv_qnty+=$row_recv[csf('order_qnty')];
                        $tot_recv_amnt+=($row_recv[csf('rate')]*$row_recv[csf('order_qnty')]);//$row_recv[csf('order_amount')];

                        $i++;
                    }
                ?>
                <tfoot>
                    <th colspan="10" align="right">Total</th>
                    <th align="right"><? echo number_format($tot_recv_qnty,2,'.','');?></th>
                    <th>&nbsp;</th>
                    <th align="right"><? echo number_format($tot_recv_amnt,2,'.','');?></th>
                    <th>&nbsp;</th>
                </tfoot>
            </table>
            <br>
             <table width="1210" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                <thead>
                    <tr>
                      <th colspan="8">Yarn Return</th>
                    </tr>
                    <tr>
                        <th width="100">Return Date</th>
                        <th width="130">Return No</th>
                        <th width="110">Store Name</th>
                        <th width="290">Item Description</th>
                        <th width="110">Qnty</th>
                        <th width="100">Rate</th>
                        <th width="120">Value</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                 <?
                    $tot_retn_qnty=0; $tot_retn_amnt=0;$return_data_arr=array();
                    if(count($recv_id_array)>0)
                    {
						$recv_id_all=implode(",",$recv_id_array);
						$sql_retn="select a.id, a.received_id, a.issue_number, a.issue_date, a.challan_no,b.store_id, cons_rate as rate, b.rcv_rate, b.cons_quantity, b.cons_amount, c.product_name_details,d.receive_date,a.remarks, a.pi_id, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_type, c.color
						from inv_issue_master a, inv_transaction b, product_details_master c,inv_receive_master d
						where a.item_category=1 and a.entry_form=8 and a.company_id=$company and a.received_id in($recv_id_all) and a.id=b.mst_id and a.received_id=d.id and b.item_category=1 and b.transaction_type=3 and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
						//echo $sql_retn;
                        $dataRtArray=sql_select($sql_retn);
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

							$exchange_rate=set_conversion_rate( 2, $conversion_date );
							if($row_retn[csf('rcv_rate')]>0)
							{
								$pi_string=$row_retn[csf('pi_id')]."*".$row_retn[csf('yarn_count_id')]."*".$row_retn[csf('yarn_comp_type1st')]."*".$row_retn[csf('yarn_type')]."*".$row_retn[csf('color')];
								$pi_rate=$pi_mrr_summery[$row_retn[csf('pi_id')]][$row_retn[csf('yarn_count_id')]][$row_retn[csf('yarn_comp_type1st')]][$row_retn[csf('yarn_type')]][$row_retn[csf('color')]]["pi_rate"];
								if($pi_rate>0)
								{
									$rate=$pi_rate;
								}
								else
								{
									$rate=$row_retn[csf('rcv_rate')]/$exchange_rate;
								}
							}
							else
							{
								$rate=$row_retn[csf('rate')]/$exchange_rate;
							}

							$amnt=$row_retn[csf('cons_quantity')]*$rate;

                            $rcv_return_qty_arr[$row_retn[csf('received_id')]] = $row_retn[csf('cons_quantity')];

                        ?>
                            <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                                <td width="100" align="center"><? echo change_date_format($row_retn[csf('issue_date')]); ?></td>
                                <td width="130" title="<?= "pi string = ".$pi_string?>"><p><? echo $row_retn[csf('issue_number')]; ?></p></td>
                                <td width="110" ><p>&nbsp;<? echo $store_arr[$row_retn[csf('store_id')]]; ?></p></td>
                                <td width="290"><p>&nbsp;<? echo $row_retn[csf('product_name_details')]; ?></p></td>
                                <td width="110" align="right"><? echo number_format($row_retn[csf('cons_quantity')],2,'.',''); ?>&nbsp;</td>
                                <td width="100" align="right" title="<? echo 'exchange rate = '.$exchange_rate;?>"><? echo number_format($rate,2,'.',''); ?>&nbsp;</td>
                                <td width="120" align="right"><? echo number_format($amnt,2,'.',''); ?>&nbsp;</td>
                                <td><p><? echo $row_retn[csf('remarks')];?> &nbsp;</p></td>
                            </tr>
                        <?
                            $pi_id=$recv_pi_array[$row_retn[csf('received_id')]];
                            $tot_retn_qnty+=$row_retn[csf('cons_quantity')];
                            $tot_retn_amnt+=$amnt;
                            $pi_data_array[$pi_id]['rtn']+=$amnt;
                            $pi_data_array[$pi_id]['rtnQnt']+=$row_retn[csf('cons_quantity')];
							$return_data_arr[$pi_id][$row_retn[csf('yarn_count_id')]][$row_retn[csf('yarn_comp_type1st')]][$row_retn[csf('yarn_type')]][$row_retn[csf('color')]]["rtn_qnty"]+=$row_retn[csf('cons_quantity')];
							$return_data_arr[$pi_id][$row_retn[csf('yarn_count_id')]][$row_retn[csf('yarn_comp_type1st')]][$row_retn[csf('yarn_type')]][$row_retn[csf('color')]]["rtn_amnt"]+=$amnt;
                            $i++;
                        }
                    }
                    //print_r($pi_data_array);
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
						$sql_retn="select a.received_id, a.pi_id, a.issue_number, a.issue_date, a.challan_no,a.remarks,b.store_id, (b.order_rate+b.order_ile_cost) as ord_rate, b.cons_rate as rate, b.rcv_rate, b.cons_quantity, b.cons_amount, c.product_name_details, a.pi_id, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_type, c.color
						from inv_issue_master a, inv_transaction b, product_details_master c
						where a.item_category=1 and a.entry_form=8 and a.company_id=$company and a.pi_id in($pi_id_all) and a.id=b.mst_id and b.item_category=1 and b.transaction_type=3 and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $rtn_cond";
						//echo $sql_retn;
						$dataRtArray=sql_select($sql_retn);
						foreach($dataRtArray as $row_retn)
						{
							if ($i%2==0)
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";

							//$rate=$row_retn[csf('rate')]/$exchange_rate;
							$pi_string=$row_retn[csf('pi_id')]."*".$row_retn[csf('yarn_count_id')]."*".$row_retn[csf('yarn_comp_type1st')]."*".$row_retn[csf('yarn_type')]."*".$row_retn[csf('color')];
							$pi_rate=$pi_mrr_summery[$row_retn[csf('pi_id')]][$row_retn[csf('yarn_count_id')]][$row_retn[csf('yarn_comp_type1st')]][$row_retn[csf('yarn_type')]][$row_retn[csf('color')]]["pi_rate"];
							if($row_retn[csf('rcv_rate')]>0)
							{
								if($pi_rate>0)
								{
									$rate=$pi_rate;
								}
								else
								{
									$rate=$row_retn[csf('rcv_rate')]/$exchange_rate;
								}

							}
							else
							{
								$rate=$row_retn[csf('rate')]/$exchange_rate;
							}

							$amnt=$row_retn[csf('cons_quantity')]*$rate;


                            $rcv_return_qty_arr[$row_retn[csf('received_id')]] = $row_retn[csf('cons_quantity')];
						?>
							<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
								<td width="100" align="center"><? echo change_date_format($row_retn[csf('issue_date')]); ?></td>
								<td width="130" title="<?= "pi string = ".$pi_string?>"><p><? echo $row_retn[csf('issue_number')]; ?></p></td>
								<td width="110" ><p>&nbsp;<? echo $store_arr[$row_retn[csf('store_id')]]; ?></p></td>
								<td width="290"><p>&nbsp;<? echo $row_retn[csf('product_name_details')]; ?></p></td>
								<td width="110" align="right"><? echo number_format($row_retn[csf('cons_quantity')],2,'.',''); ?>&nbsp;</td>
								<td width="100" align="right" title="<? echo 'exchange_rate='.$exchange_rate;?>"><? echo number_format($rate,2,'.',''); ?>&nbsp;</td>
								<td width="120" align="right"><? echo number_format($amnt,2,'.',''); ?>&nbsp;</td>
								<td><p><? echo $row_retn[csf('remarks')];?> &nbsp;</p></td>
							</tr>
						<?
							$pi_id=$row_retn[csf('pi_id')];
							$tot_retn_qnty+=$row_retn[csf('cons_quantity')];
							$tot_retn_amnt+=$amnt;
							$pi_data_array[$pi_id]['rtn']+=$amnt;
							$pi_data_array[$pi_id]['rtnQnt']+=$row_retn[csf('cons_quantity')];

							$return_data_arr[$pi_id][$row_retn[csf('yarn_count_id')]][$row_retn[csf('yarn_comp_type1st')]][$row_retn[csf('yarn_type')]][$row_retn[csf('color')]]["rtn_qnty"]+=$row_retn[csf('cons_quantity')];
							$return_data_arr[$pi_id][$row_retn[csf('yarn_count_id')]][$row_retn[csf('yarn_comp_type1st')]][$row_retn[csf('yarn_type')]][$row_retn[csf('color')]]["rtn_amnt"]+=$amnt;

							$i++;
						}
					}

                    $total_balance_qty = ($tot_pi_qnty+$tot_retn_qnty-$tot_recv_qnty);
                    $total_balance_value = ($tot_pi_amnt+$tot_retn_amnt-$tot_recv_amnt);
                ?>
                <tfoot>
                    <tr>
                        <th colspan="4" align="right">Total</th>
                        <th align="right"><? echo number_format($tot_retn_qnty,2,'.','');?></th>
                        <th>&nbsp;</th>
                        <th align="right"><? echo number_format($tot_retn_amnt,2,'.','');?></th>
                        <th>&nbsp;</th>
                    </tr>
                    <tr>
                        <th colspan="4" align="right">Balance</th>
                        <th align="right" title="<? echo $tot_pi_qnty.test; ?>"><? echo number_format($total_balance_qty,2,'.','');?></th>
                        <th>&nbsp;</th>
                        <th align="right"><? echo number_format($total_balance_value,2,'.','');?></th>
                        <th>&nbsp;</th>
                    </tr>
                </tfoot>
            </table>
            <br>
            <table width="850" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                <thead>
                        <tr>

							<th width="140" rowspan="2">PI Number</th>
							<th width="" colspan="2">Receive</th>
							<th width="" colspan="2">Return</th>
							<th width="" colspan="2">Payable</th>
                            <th width="" colspan="4">Acceptance Details</th>
						</tr>
						<tr>
							<th width="100">Qnty</th>
							<th width="100">Value</th>
							<th width="100">Qnty</th>
							<th width="100">Value</th>
							<th width="100">Qnty</th>
							<th width="100">Value</th>
                            <th width="100">Date</th>
							<th width="100">Invoice No.</th>
							<th width="100">Value</th>
							<th width="100">Bal. Value</th>
						</tr>
                </thead>
                <?

					if ($pi_id_all!='' || $pi_id_all!=0)
					{
						/*echo "select m.id as pi_id, m.pi_basis_id, m.goods_rcv_status, listagg(n.work_order_id, ', ') within group (order by n.work_order_id) work_order_id, a.company_acc_date, b.current_acceptance_value as acceptance_value, a.invoice_no
						from  com_pi_master_details m
                        left join com_pi_item_details n on m.id=n.pi_id and n.status_active=1 and n.is_deleted=0
                        left join com_import_invoice_dtls b on m.id=b.pi_id and b.status_active=1 and b.is_deleted=0
                        left join com_import_invoice_mst a on a.id=b.import_invoice_id where m.id in($pi_id_all)
						group by m.id, m.pi_basis_id, m.goods_rcv_status, a.company_acc_date, b.current_acceptance_value, a.invoice_no
						order by m.id";*/
                        if($db_type==0){
                            $accep_query = "select m.id as pi_id, m.pi_basis_id, m.goods_rcv_status, group_concat(n.work_order_id) as work_order_id, a.company_acc_date, b.current_acceptance_value as acceptance_value, a.invoice_no
                            from  com_pi_master_details m
                            left join com_pi_item_details n on m.id=n.pi_id and n.status_active=1 and n.is_deleted=0
                            left join com_import_invoice_dtls b on m.id=b.pi_id and b.status_active=1 and b.is_deleted=0
                            left join com_import_invoice_mst a on a.id=b.import_invoice_id where m.id in($pi_id_all)
                            group by m.id, m.pi_basis_id, m.goods_rcv_status, a.company_acc_date, b.current_acceptance_value, a.invoice_no
                            order by m.id";
                        }else if($db_type ==2){
                            $accep_query = "select m.id as pi_id, m.pi_basis_id, m.goods_rcv_status, listagg(n.work_order_id, ',') within group (order by n.work_order_id) work_order_id, a.company_acc_date, b.current_acceptance_value as acceptance_value, a.invoice_no
                            from  com_pi_master_details m
                            left join com_pi_item_details n on m.id=n.pi_id and n.status_active=1 and n.is_deleted=0
                            left join com_import_invoice_dtls b on m.id=b.pi_id and b.status_active=1 and b.is_deleted=0
                            left join com_import_invoice_mst a on a.id=b.import_invoice_id where m.id in($pi_id_all)
                            group by m.id, m.pi_basis_id, m.goods_rcv_status, a.company_acc_date, b.current_acceptance_value, a.invoice_no
                            order by m.id";
                        }
                        //echo $accep_query;//die;
                        $accep_sql=sql_select($accep_query);
						foreach($accep_sql as $row)
						{
							$accep_breakdown_data[$row[csf("pi_id")]]++;
							$acceptance_arr[$row[csf("pi_id")]]["acceptance_value"]+=$row[csf("acceptance_value")];
							if($row[csf("goods_rcv_status")]==1){ //After Goods Received
                                if($wo_check[$row[csf("pi_id")]][$row[csf("work_order_id")]]=="")
                                {
                                    $acceptance_arr[$row[csf("pi_id")]]["work_order_id"] =$row[csf("work_order_id")];
                                }
                            }else{ //Before Goods Received
                                if($wo_check[$row[csf("pi_id")]][$row[csf("pi_id")]]=="")
                                {
                                    $acceptance_arr[$row[csf("pi_id")]]["pi_id"].=$row[csf("pi_id")].",";
                                }
                            }
						}
					}
                    //echo $count_row[1261].jahid;die;
						$payble_value=0;
						$tot_payble_value=0;
						$yet_to_accept=0;
						$tot_accept_value=0;
						$tot_yet_to_accept=0;
						$total_receive_value=0;
						$total_receive_rcvQnt=0;
						$total_return_value=0;
						$total_return_rtnQnt=0;
						$tot_payble_Qnty=0;
					/*foreach($pi_name_array as $key=>$value)
					{
					}*/
					//echo "<pre>";print_r($pi_data_array);die;
                    foreach($accep_sql as $row)
                    {
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
							<?
                           	//echo "<pre>";print_r($pi_data_array);
                            //echo $row[csf("pi_basis_id")];
							if($temp_pi[$row[csf("pi_id")]]=="")
							{
								$payble_value=$yet_to_accept=$recv_qnty=$recv_value=$recv_rtn_qnty=$recv_rtn_value=0;$piwo_id ="";
                                if($row[csf("goods_rcv_status")]==1)
                                {
									$piwo_id =chop($acceptance_arr[$row[csf("pi_id")]]["work_order_id"],",");
									$piwo_id_arr = array_unique(explode(",",chop($acceptance_arr[$row[csf("pi_id")]]["work_order_id"],",")));
									//echo "<pre>";print_r($piwo_id_arr);die;
									foreach($piwo_id_arr as $wo_id)
									{
										$recv_qnty+=$pi_data_array[$wo_id*1]['rcvQnt'];
										$recv_value+=$pi_data_array[$wo_id*1]['rcv'];
									}
									//echo $recv_qnty;die;
                                }
								else
								{
									$recv_qnty = $pi_data_array[$row[csf("pi_id")]]['rcvQnt'];
									$recv_value = $pi_data_array[$row[csf("pi_id")]]['rcv'];
									$recv_rtn_qnty=$pi_data_array[$row[csf("pi_id")]]['rtnQnt'];
									$recv_rtn_value=$pi_data_array[$row[csf("pi_id")]]['rtn'];
                                    $piwo_id =  $row[csf("pi_id")];
                                }


								$payble_Qnty=$recv_qnty-$recv_rtn_qnty;
								$payble_value=$recv_value-$recv_rtn_value;
								$yet_to_accept=$payble_value-$acceptance_arr[$row[csf("pi_id")]]["acceptance_value"];

								$total_receive_value+=$recv_value;
								$total_receive_rcvQnt+=$recv_qnty;
								$total_return_value+=$recv_rtn_value;
								$total_return_rtnQnt+=$recv_rtn_qnty;
								$tot_payble_value+=$payble_value;
								$tot_payble_Qnty+=$payble_Qnty;
								$tot_accept_value+=$acceptance_arr[$row[csf("pi_id")]]["acceptance_value"];
								$tot_yet_to_accept+=$yet_to_accept;
								?>
								<td width="140" rowspan="<? echo $accep_breakdown_data[$row[csf("pi_id")]];?>" title="<?= $row[csf("pi_id")]; ?>"><p><? echo $pi_name_array[$row[csf("pi_id")]]; ?></p></td>
								<td width="100"  align="right" rowspan="<? echo $accep_breakdown_data[$row[csf("pi_id")]];?>" title="<?= print_r($piwo_id_arr); ?>"><? echo number_format($recv_qnty,2,'.',''); ?>&nbsp;</td>
                                <td width="100"  align="right" rowspan="<? echo $accep_breakdown_data[$row[csf("pi_id")]];?>" title="<? echo $piwo_id." PI ".$row[csf("pi_id")]; ?>"><? echo number_format($recv_value,2,'.',''); ?>&nbsp;</td>
                                <td width="100" align="right" rowspan="<? echo $accep_breakdown_data[$row[csf("pi_id")]];?>"><? echo number_format($recv_rtn_qnty,2,'.',''); ?>&nbsp;</td>
								<td width="100" align="right" rowspan="<? echo $accep_breakdown_data[$row[csf("pi_id")]];?>"><? echo number_format($recv_rtn_value,2,'.',''); ?>&nbsp;</td>
								<td width="100" align="right" rowspan="<? echo $accep_breakdown_data[$row[csf("pi_id")]];?>"><? echo number_format($payble_Qnty,2,'.',''); ?>&nbsp;</td>
                                <td width="100" align="right" rowspan="<? echo $accep_breakdown_data[$row[csf("pi_id")]];?>"><? echo number_format($payble_value,2,'.',''); ?>&nbsp;</td>
								<?
							}
							?>
							<td width="100" align="center"><? if($row[csf("company_acc_date")]!="" && $row[csf("company_acc_date")]!="0000-00-00") echo change_date_format($row[csf("company_acc_date")]);?>&nbsp;</td>
                            <td width="100" align="center"><? echo $row[csf("invoice_no")];?>&nbsp;</td>
							<td width="100" align="right"><? echo number_format($row[csf("acceptance_value")],2,'.',''); ?>&nbsp;</td>
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
                ?>
                <tfoot>
                    <th align="right">Total</th>
                    <th align="right"><? echo number_format($total_receive_rcvQnt,2,'.',''); ?></th>
                    <th align="right"><? echo number_format($total_receive_value,2,'.',''); ?></th>
                    <th align="right"><? echo number_format($total_return_rtnQnt,2,'.',''); ?></th>
                    <th align="right"><? echo number_format($total_return_value,2,'.',''); ?></th>
                    <th align="right"><? echo number_format($tot_payble_Qnty,2,'.',''); ?></th>
                    <th align="right"><? echo number_format($tot_payble_value,2,'.',''); ?></th>
                    <th align="right">&nbsp;</th>
                    <th align="right">&nbsp;</th>
                    <th align="right"><? echo number_format($tot_accept_value,2,'.',''); ?></th>
                    <th align="right"><? echo number_format($tot_yet_to_accept,2,'.',''); ?></th>
                </tfoot>
            </table>

            <br>
            <table width="1150" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                <thead>
                    <tr>
                      <th colspan="13">Summary Of PI and Receive</th>
                    </tr>
                    <tr>
                    	<th colspan="8">PI Summary</th>
                        <th colspan="5">Receive Summary</th>
                    </tr>
                    <tr>
                        <th width="100">PI Number</th>
                        <th width="70">Count</th>
                        <th width="130">Composition</th>
                        <th width="80">Yarn Type</th>
                        <th width="70">Color</th>
                        <th width="80">Require Qnty</th>
                        <th width="80">Rate</th>
                        <th width="90">Value</th>
                        <th width="80">Receive Qnty</th>
                        <th width="80">Receive Blance</th>
                        <th width="80">Rate</th>
                        <th width="90">Value</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                <?

				foreach( $pi_mrr_summery as $pi_wo_id=>$pi_data )
				{
					foreach($pi_data as $y_count_id=>$y_count_data)
					{
						foreach($y_count_data as $y_comp_id=>$y_comp_data)
						{
							foreach($y_comp_data as $y_type_id=>$y_type_data)
							{
								foreach($y_type_data as $y_color_id=>$color_data)
								{
									if ($i%2==0)
										$bgcolor="#E9F3FF";
									else
										$bgcolor="#FFFFFF";

									//echo $color_data["pi_qnty"]."==".$color_data["mrr_qnty"];

                                    $pi_basis_id = $pi_mrr_summery[$pi_wo_id][$y_count_id][$y_comp_id][$y_type_id][$y_color_id]["pi_basis_id"];
                                    $goods_rcv_status = $pi_mrr_summery[$pi_wo_id][$y_count_id][$y_comp_id][$y_type_id][$y_color_id]["goods_rcv_status"];
									$mrr_qnty = $mrr_amnt =0;
									$work_order_id="";
                                    if($goods_rcv_status==1)
                                    {
                                        $work_order_id_arr = explode(",",chop($pi_with_work_order_array[$pi_wo_id],","));
										foreach($work_order_id_arr as $wo_id)
										{
											$mrr_qnty += ($pi_rcv_mrr_summery[$wo_id][$y_count_id][$y_comp_id][$y_type_id][$y_color_id]["mrr_qnty"]-$return_data_arr[$work_order_id][$y_count_id][$y_comp_id][$y_type_id][$y_color_id]["rtn_qnty"]);
											$mrr_amnt += ( $pi_rcv_mrr_summery[$wo_id][$y_count_id][$y_comp_id][$y_type_id][$y_color_id]["mrr_amnt"]-$return_data_arr[$work_order_id][$y_count_id][$y_comp_id][$y_type_id][$y_color_id]["rtn_amnt"] );
											$work_order_id .= $wo_id.",";
										}
                                    }else{
                                        $work_order_id = $pi_wo_id;
										$mrr_qnty = ($pi_rcv_mrr_summery[$work_order_id][$y_count_id][$y_comp_id][$y_type_id][$y_color_id]["mrr_qnty"]-$return_data_arr[$work_order_id][$y_count_id][$y_comp_id][$y_type_id][$y_color_id]["rtn_qnty"]);
										$mrr_amnt = ( $pi_rcv_mrr_summery[$work_order_id][$y_count_id][$y_comp_id][$y_type_id][$y_color_id]["mrr_amnt"]-$return_data_arr[$work_order_id][$y_count_id][$y_comp_id][$y_type_id][$y_color_id]["rtn_amnt"] );
                                    }
									$work_order_id=chop($work_order_id,",");
									//echo $work_order_id."=".$goods_rcv_status."<br>";
									//$mrr_qnty = ($pi_rcv_mrr_summery[$work_order_id][$y_count_id][$y_comp_id][$y_type_id][$y_color_id]["mrr_qnty"]-$pi_data_array[$pi_wo_id]['rtnQnt']);
									//$mrr_amnt = ( $pi_rcv_mrr_summery[$work_order_id][$y_count_id][$y_comp_id][$y_type_id][$y_color_id]["mrr_amnt"]-$pi_data_array[$pi_wo_id]['rtn'] );

									?>
                                    <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                                        <td title="<? echo $pi_wo_id; ?>"><? echo $pi_name_array[$pi_wo_id]; ?></td>
                                        <td title="<? echo $y_count_id; ?>"><? echo $count_arr[$y_count_id]; ?></td>
                                        <td title="<? echo $y_comp_id; ?>"><? echo $composition[$y_comp_id]; ?></td>
                                        <td title="<? echo $y_type_id; ?>"><? echo $yarn_type[$y_type_id]; ?></td>
                                        <td title="<? echo $y_color_id; ?>"><? echo $color_arr[$y_color_id]; ?></td>
                                        <td align="right"><? echo number_format($color_data["pi_qnty"],2); ?></td>
                                        <td align="right"><? if($color_data["pi_qnty"]>0 && $color_data["pi_amnt"]>0) echo number_format(($color_data["pi_amnt"]/$color_data["pi_qnty"]),2); else echo "0.00"; ?></td>
                                        <td align="right"><? echo number_format($color_data["pi_amnt"],2); ?></td>
                                        <td align="right"><? echo number_format($mrr_qnty,2); ?></td>
                                        <td align="right">
                                        <?
                                            $mrr_balance=($color_data["pi_qnty"]*1)-$mrr_qnty;
                                            echo number_format($mrr_balance,2);
                                        ?>
                                        </td>
                                        <td align="right"><? if($mrr_qnty>0 && $mrr_amnt>0) echo number_format(($mrr_amnt/$mrr_qnty),2); else echo "0.00"; ?></td>
                                        <td align="right"><? echo number_format($mrr_amnt,2); ?></td>
                                        <td><? echo $color_data["remarks"]; ?></td>
                                    </tr>
                                    <?
									$total_pi_qnty+=$color_data["pi_qnty"];
									$total_pi_amnt+=$color_data["pi_amnt"];
									$total_mrr_qnty+=$mrr_qnty;
									$total_mrr_amnt+=$mrr_amnt;
									$total_mrr_balance+=$mrr_balance;
									$i++;
								}
							}
						}
					}
				}
				?>
                </tbody>
                <tfoot>
                    <th align="right" colspan="5">Total:</th>
                    <th align="right"><? echo number_format($total_pi_qnty,2,'.',''); ?></th>
                    <th>&nbsp;</th>
                    <th align="right"><? echo number_format($total_pi_amnt,2,'.',''); ?></th>
                    <th align="right"><? echo number_format($total_mrr_qnty,2); ?></th>
                    <th align="right"><? echo number_format($total_mrr_balance,2); ?></th>
                    <th>&nbsp;</th>
                    <th align="right"><? echo number_format($total_mrr_amnt,2,'.',''); ?></th>
                    <th align="right">&nbsp;</th>
                </tfoot>
        	</table>
	</fieldset>
	<?
	exit();
}



?>
