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
    $sql_store = "select a.id, a.store_name from lib_store_location a,lib_store_location_category b  where a.id=b.store_location_id and a.company_id='$data' and b.category_type in(11) and a.status_active=1 and a.is_deleted=0 order by a.store_name";
	echo create_drop_down( "cbo_store_name", 150, "$sql_store","id,store_name", 1, "-- Select Store --", 0, "" );
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
                                    <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_supplier_id').value+'_'+document.getElementById('txt_search_common').value+'_'+<? echo $companyID; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_pi_search_list_view', 'search_div', 'lc_wise_stationary_receive_report_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
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

	$sql= "select id, pi_number, supplier_id, importer_id, pi_date, last_shipment_date, total_amount from com_pi_master_details where importer_id=$company and entry_form=172 and supplier_id like '$cbo_supplier' and pi_number like '%".$txt_search_common."%' and is_deleted=0 and status_active=1 $pi_date_cond";
    //echo $sql;
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
                                <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_supplier_id').value+'_'+document.getElementById('txt_search_common').value+'_'+<? echo $companyID; ?>, 'create_lc_search_list_view', 'search_div', 'lc_wise_stationary_receive_report_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
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

	if($ex_data[0]==0) $cbo_supplier = "%%"; else $cbo_supplier = $ex_data[0];
	$txt_search_common = trim($ex_data[1]);
	$company = $ex_data[2];

	$sql= "select id, lc_number, supplier_id, importer_id, lc_date, last_shipment_date, lc_value from com_btb_lc_master_details where importer_id=$company and pi_entry_form=172 and supplier_id like '$cbo_supplier' and lc_number like '%".$txt_search_common."%' and is_deleted=0 and status_active=1";
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
	//if(str_replace("'","",$cbo_store_name)==0) $store="%%"; else $store=str_replace("'","",$cbo_store_name);
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
    
    $item_group_name = return_library_array("select id,item_name from lib_item_group","id","item_name");
    
	ob_start();
	?>
        <!--<fieldset style="width:1120px">-->
    	<div style="width:100%; margin-left:10px;" align="left">
            <table width="760" cellpadding="0" cellspacing="0" style="visibility:hidden; border:none" id="caption">
                <tr>
                   <td align="center" width="100%" colspan="7" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
                </tr>
            </table>
            <!-- BTB LC Details Start -->   
            <table width="760" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                <?
					//LISTAGG(CAST( mst_id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY mst_id)
                    $sql_btb_lc = "SELECT a.id, a.lc_number, a.supplier_id, a.importer_id, a.lc_date, a.last_shipment_date, a.lc_value, c.goods_rcv_status, LISTAGG(CAST(c.id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.id) as pi_id, LISTAGG(CAST(d.work_order_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY d.work_order_id) as work_order_id
					from com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_master_details c, com_pi_item_details d
					where a.id=b.com_btb_lc_master_details_id and c.id=b.pi_id and c.id = d.pi_id and c.item_category_id = 11  and a.pi_entry_form=172 and c.importer_id=$cbo_company_name and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $btbLc_id_cond_mst $pi_cond_btb 
					group by a.id, a.lc_number, a.supplier_id, a.importer_id, a.lc_date, a.last_shipment_date, a.lc_value, c.goods_rcv_status";
                    //echo $sql_btb_lc;
                    $btbLcData=sql_select($sql_btb_lc);
                ?>
                <thead>
                	<tr>
                        <th colspan="7">BTB LC Details</th>
                    </tr>
                	<tr>
                        <th width="40">SL</th>
                        <th width="150">Importer</th>
                        <th width="150">Supplier</th>
                        <th width="120">BTB LC No</th>
                        <th width="100">LC Date</th>
                        <th width="100">Last Shipment Date</th>
                        <th>LC Value</th>
                    </tr>
                </thead>
            </table>
            <div style="width:760px; overflow-y:scroll; max-height:430px;"  align="left">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="740" class="rpt_table" id="tbl_finish_feb_btb_lc_dtls" >
                <?
                $i=1;$pi_id_all_array=array();$pi_id_all_arr=array();$pi_wo_check=array(); $pi_name_array=array(); $rate=0; $compos=''; $tot_pi_qnty=0; $tot_pi_amnt=0;
				if(count($btbLcData)>0)
				{
                    foreach ($btbLcData as  $value) {
                        if ($i%2==0)
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";
				?>
                    <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trg<? echo $i;?>','<? echo $bgcolor;?>')" id="trg<? echo $i;?>">
                        <td width="40" align="center"><p><? echo $i; ?> &nbsp;</p></td>
                        <td width="150"><p><? echo $company_arr[$value[csf('importer_id')]]; ?></p></td>
                        <td width="150"><p><? echo $supplier_arr[$value[csf('supplier_id')]]; ?></p></td>
                        <td width="120"><p><? echo $value[csf('lc_number')]; ?></p></td>
                        <td width="100" align="center"><? echo change_date_format($value[csf('lc_date')]); ?></td>
                        <td width="100" align="center"><? echo change_date_format($value[csf('last_shipment_date')]); ?></td>
                        <td align="right"><? echo number_format($value[csf('lc_value')],4); ?></td>
                    </tr>
                <?
                        $i++;
                        //if(!in_array($value[csf('id')],$pi_id_all_array))
//                        {
//                             $pi_id_all_array[$value[csf('pi_id')]]=$value[csf('pi_id')];
//                            $goods_recv_status_array['goods_rcv_status']=$value[csf('goods_rcv_status')];
//                             $all_wo_id_array[$value[csf('work_order_id')]]=$value[csf('work_order_id')];
//                            $pi_id_all_array['work_order_no']=$value[csf('work_order_no')];
//							$pi_name_array[$value[csf('pi_id')]]=$value[csf('pi_number')];
//                        }

                        if($value[csf('goods_rcv_status')]==1)
                        {
							if($pi_wo_check[$value[csf('id')]][$value[csf('work_order_id')]]=="")
							{
								$pi_wo_check[$rvalueow[csf('id')]][$value[csf('work_order_id')]]=$value[csf('work_order_id')];
								$all_wo_id_array[$value[csf('id')]].= $value[csf('work_order_id')].",";
							}
                        }
						$pi_id_arr=array_unique(explode(",",$value[csf('pi_id')]));
						foreach($pi_id_arr as $pi_id)
						{
							$pi_id_all_arr[$pi_id]=$pi_id;  
						}
						
                        //if(!in_array($value[csf('pi_id')],$pi_id_all_arr))
//                        {                              
//                            $pi_id_all_arr[$value[csf('pi_id')]]=$value[csf('pi_id')];                           
//                        }
                        $tot_lc_value +=$value[csf('lc_value')];
                    }
				}
				?>
                <tfoot>
                    <tr>
                        <th colspan="6" align="right">Total</th>
                        <th align="right"> <?php echo number_format($tot_lc_value,4,'.','');?></th>
                    </tr>
                </tfoot>
            </table>
            </div>
            <!-- BTB LC Details End -->
            <br>
            <!-- PI Details Start -->
            <table width="860" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                <thead>
                    <tr>
                        <th colspan="9">PI Details</th>
                    </tr>
                    <tr>
                        <th width="40">SL</th>
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
            <div style="width:860px; overflow-y:scroll; max-height:430px;"  align="left">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="840" class="rpt_table" id="tbl_finish_feb_pi_dtls" >
                    <?
                    $i=1;
                   /* $sql_pi_details="select c.id as pi_dtls_id, b.id as pi_id, b.pi_number, b.pi_date, b.currency_id, b.remarks, b.item_category_id, b.supplier_id, c.item_description, c.color_id, c.item_group, c.work_order_dtls_id, c.quantity, c.rate, c.net_pi_amount
                    from com_pi_master_details b, com_pi_item_details c, wo_booking_mst d, wo_booking_dtls h, com_btb_lc_pi a
                    where b.id = c.pi_id and b.id = a.pi_id and b.entry_form = 172 and b.importer_id = $cbo_company_name and b.item_category_id = 11 and c.work_order_id = d.id and d.booking_no = h.booking_no and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 $pi_cond $btbLc_id_cond";*/

                     $sql_pi_details="select c.id as pi_dtls_id, b.id as pi_id, b.pi_number, b.pi_date, b.currency_id, b.remarks, b.item_category_id, b.supplier_id, c.item_description, c.color_id, c.item_group, c.work_order_dtls_id, c.quantity, c.rate, c.net_pi_amount
                    from com_pi_master_details b, com_pi_item_details c, com_btb_lc_pi a
                    where b.id = c.pi_id and b.id = a.pi_id and b.entry_form = 172 and b.importer_id = $cbo_company_name and b.item_category_id = 11 and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 $pi_cond $btbLc_id_cond";
                    //echo $sql_pi_details;
                    $result=sql_select($sql_pi_details);
                    $pi_dtls_data = array();$pi_data_check=array();

                    foreach ($result as  $value) 
                    {
                        $pi_dtls_data[$value[csf('pi_dtls_id')]]['pi_id']=$value[csf('id')];
                        $pi_dtls_data[$value[csf('pi_dtls_id')]]['pi_number']=$value[csf('pi_number')];
                        $pi_dtls_data[$value[csf('pi_dtls_id')]]['pi_date']=$value[csf('pi_date')];
                        $pi_dtls_data[$value[csf('pi_dtls_id')]]['currency_id']=$value[csf('currency_id')];
                        $pi_dtls_data[$value[csf('pi_dtls_id')]]['remarks']=$value[csf('remarks')];
                        $pi_dtls_data[$value[csf('pi_dtls_id')]]['item_group']=$value[csf('item_group')];
                        $pi_dtls_data[$value[csf('pi_dtls_id')]]['work_order_dtls_id']=$value[csf('work_order_dtls_id')];
                        $pi_dtls_data[$value[csf('pi_dtls_id')]]['item_description']=$value[csf('item_description')];
                        if($pi_data_check[$value[csf('pi_dtls_id')]]=="")
                        {
                            $pi_data_check[$value[csf('pi_dtls_id')]]=$value[csf('pi_dtls_id')];
                            $pi_dtls_data[$value[csf('pi_dtls_id')]]['quantity']=$value[csf('quantity')];
                            $pi_dtls_data[$value[csf('pi_dtls_id')]]['net_pi_amount']=$value[csf('net_pi_amount')];
                        }

                        $pi_dtls_data[$value[csf('pi_dtls_id')]]['booking_no']=$value[csf('booking_no')];
                    }
                    //var_dump($pi_dtls_data);
                    foreach($pi_dtls_data as $row)
                    {

                        if ($i%2==0)
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";

						if($row['currency_id']!=1) $ex_rate=1; else $ex_rate=$exchange_rate;

                        $rate=($row['net_pi_amount']/$row['quantity']);

                        $item_description=$row['item_description'];
                        $supplier_pi_arr[$row['pi_id']]['supplier'] = $row['supplier_id'];
                        
                        ?>
                        <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trb<? echo $i;?>','<? echo $bgcolor;?>')" id="trb<? echo $i;?>">
                            <td width="40"><? echo $i; ?>&nbsp;</td>
                            <td width="90"><p><? echo $row['pi_number']; ?></p></td>
                            <td width="100" align="center"><? echo change_date_format($row['pi_date']); ?></td>
                            <td width="80" align="center"><p>&nbsp;<? echo $item_group_name[$row['item_group']]; ?></p></td>
                            <td width="150"><p><? echo $item_description; ?></p></td>
                            <td width="90" align="right"><? echo number_format($row['quantity'],2,'.',''); ?>&nbsp;</td>
                            <td width="90" align="right"><? echo number_format($rate,4,'.',''); ?>&nbsp;</td>
                            <td width="120" align="right"><? echo number_format($row['net_pi_amount'],4,'.',''); ?>&nbsp;</td>
                            <td><p><? echo $row['remarks']; ?>&nbsp;</p></td>
                        </tr>
                        <?

                        $tot_pi_qnty+=$row['quantity'];
                        $tot_pi_amnt+=$row['net_pi_amount'];

                        $i++;
                    }
                    ?>
                    <tfoot>
                        <th colspan="5" align="right">Total</th>
                        <th align="right"> <?php echo number_format($tot_pi_qnty,2,'.','');?></th>
                        <th>&nbsp;</th>
                        <th align="right"> <?php echo number_format($tot_pi_amnt,4,'.','');?></th>
                        <th>&nbsp;</th>
                    </tfoot>
                </table>
            </div>
            <!-- PI Details End -->
            <br>
            <!-- Stationaries Receive Start -->
            <table width="970" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                <thead>
                    <tr>
                      <th colspan="11">Stationaries Receive</th>
                    </tr>
                    <tr>
                        <th width="40">SL</th>
                        <th width="100">MRR No</th>
                        <th width="90">Recv. Date</th>
                        <th width="80">Challan No</th>
                        <th width="70">Item Group</th>
                        <th width="150">Item Description</th>
                        <th width="80">Receive Qnty</th>
                        <th width="70">Rate</th>
                        <th width="100">Value</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
            </table>
            <div style="width:970px; overflow-y:scroll; max-height:300px;"  align="left">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="950" class="rpt_table" id="tbl_finish_feb_rcv" >
                    <?
                    /*$pi_id_all=implode(",",array_flip($pi_id_all_array));
                    $booking_id_all=implode(",",array_flip($all_wo_id_array));
                    if($goods_recv_status_array['goods_rcv_status'] == 1) //After goods received
                    { 
                        $booking_or_pi_id_cond = " and a.booking_id in ($booking_id_all)";
                        $pi_booking_relation_cond = " and a.booking_id = e.work_order_id";
                    }
                    else //Before goods received
                    { 
                        $booking_or_pi_id_cond = " and a.booking_id in ($pi_id_all)";
                        $pi_booking_relation_cond = " and a.booking_id = e.pi_id";
                    }

                    //var_dump($booking_id_all);
                    $compos=''; $tot_recv_qnty=0; $tot_recv_amnt=0; $recv_id_array=array(); $recv_pi_array=array(); $pi_data_array=array();
                    //echo $pi_id_all.Jahhid;die;
					if ($booking_id_all!='' || $booking_id_all!=0)
					{
                        // $sql_recv="SELECT a.id as rec_id, a.recv_number, a.receive_date, a.challan_no, b.remarks, b.prod_id, b.pi_wo_batch_no as wo_id, sum( case  when b.transaction_type =1 then b.order_qnty else 0 end) as receive_qnty,
                        // sum( case  when b.transaction_type =1 then b.order_amount else 0 end) as receive_amount, c.product_name_details, c.item_group_id, e.pi_id
                        // from inv_receive_master a, inv_transaction b, product_details_master c, com_pi_item_details e
                        // where a.id = b.mst_id and b.prod_id = c.id $pi_booking_relation_cond and a.entry_form = 20 and b.item_category = 11 and b.transaction_type = 1 and a.company_id = $cbo_company_name and a.receive_basis in(1,2) $booking_or_pi_id_cond and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0
                        // group by a.id, a.recv_number, a.receive_date, a.challan_no, a.booking_id, a.booking_no, b.remarks, b.prod_id, b.pi_wo_batch_no, c.product_name_details, c.item_group_id, e.pi_id
                        // order by receive_date desc";

                        //group by a.id, a.recv_number, a.receive_date, a.challan_no, a.booking_id, a.booking_no,a.remarks, b.prod_id, b.pi_wo_batch_no, c.color_id, d.po_number, d.id, e.job_no, e.buyer_name, e.style_ref_no,f.construction, f.copmposition

                        //echo $sql_recv;
                        $sql_recv="SELECT a.id as rec_id, a.recv_number, a.receive_date, a.challan_no, b.remarks, b.prod_id, b.pi_wo_batch_no as wo_id, b.order_qnty as receive_qnty, b.order_amount as receive_amount, c.product_name_details, c.item_group_id, e.pi_id
                        from inv_receive_master a, inv_transaction b, product_details_master c, com_pi_item_details e
                        where a.id = b.mst_id and b.prod_id = c.id $pi_booking_relation_cond and a.entry_form = 20 and b.item_category = 11 and b.transaction_type = 1 and a.company_id = $cbo_company_name and a.receive_basis in(1,2) $booking_or_pi_id_cond and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0
                        group by a.id, a.recv_number, a.receive_date, a.challan_no, a.booking_id, a.booking_no, b.remarks, b.prod_id, b.pi_wo_batch_no, b.order_qnty , b.order_amount, c.product_name_details, c.item_group_id, e.pi_id
                        order by receive_date desc";
					}*/
                    $pi_id_all=implode(",",$pi_id_all_arr);
					foreach($all_wo_id_array as $pi_ids)
					{
						$all_pi_ids.=chop($pi_ids,",").",";
					}
					$pi_workd_order_id_all=chop($all_pi_ids,",");					
					
                    $compos=''; $tot_recv_qnty=0; $tot_recv_amnt=0; $recv_id_array=array(); $recv_pi_array=array(); $pi_data_array=array();
                    //echo $pi_id_all.test;die;
					if ($pi_id_all!='' || $pi_id_all!=0)
					{
                        $sql_recv="SELECT a.id as rec_id, a.recv_number, a.receive_date, a.challan_no, b.remarks, b.prod_id, b.pi_wo_batch_no as wo_id, b.order_qnty as receive_qnty, b.order_amount as receive_amount, c.product_name_details, c.item_group_id, e.pi_id
                        from inv_receive_master a, inv_transaction b, product_details_master c, com_pi_item_details e
                        where a.id = b.mst_id and b.prod_id = c.id  and a.booking_id = e.pi_id and a.entry_form = 20 and b.item_category = 11 and b.transaction_type = 1 and a.company_id = $cbo_company_name and a.receive_basis=1 and a.booking_id in($pi_id_all) and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0";	

					}

					if($pi_workd_order_id_all!="")
					{
						if($sql_recv!="") $sql_recv.=" union all ";	
                        $sql_recv.="SELECT a.id as rec_id, a.recv_number, a.receive_date, a.challan_no, b.remarks, b.prod_id, b.pi_wo_batch_no as wo_id, b.order_qnty as receive_qnty, b.order_amount as receive_amount, c.product_name_details, c.item_group_id, e.pi_id
                        from inv_receive_master a, inv_transaction b, product_details_master c, com_pi_item_details e
                        where a.id = b.mst_id and b.prod_id = c.id  and a.booking_id = e.work_order_id and a.entry_form = 20 and b.item_category = 11 and b.transaction_type = 1 and a.company_id = $cbo_company_name and a.receive_basis=2 and a.booking_id in($pi_workd_order_id_all) and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0
                        group by a.id, a.recv_number, a.receive_date, a.challan_no, a.booking_id, a.booking_no, b.remarks, b.prod_id, b.pi_wo_batch_no, b.order_qnty , b.order_amount, c.product_name_details, c.item_group_id, e.pi_id";	
					}
					$sql_recv.=" order by receive_date desc";
                    // echo $sql_recv;
                    $dataArray=sql_select($sql_recv);
                    $pi_dup_data_check = array();
                    foreach ($dataArray as $value) 
                    {

                        $pi_rec_data_arr[$value[csf('pi_id')]][$value[csf('recv_number')]][$value[csf('prod_id')]]['rec_id']=$value[csf('rec_id')];
                        $pi_rec_data_arr[$value[csf('pi_id')]][$value[csf('recv_number')]][$value[csf('prod_id')]]['recv_number']=$value[csf('recv_number')];
                        $pi_rec_data_arr[$value[csf('pi_id')]][$value[csf('recv_number')]][$value[csf('prod_id')]]['receive_date']=$value[csf('receive_date')];
                        $pi_rec_data_arr[$value[csf('pi_id')]][$value[csf('recv_number')]][$value[csf('prod_id')]]['challan_no']=$value[csf('challan_no')];
                        $pi_rec_data_arr[$value[csf('pi_id')]][$value[csf('recv_number')]][$value[csf('prod_id')]]['pi_id']=$value[csf('pi_id')];
                        $pi_rec_data_arr[$value[csf('pi_id')]][$value[csf('recv_number')]][$value[csf('prod_id')]]['item_description']=$value[csf('product_name_details')];
                        $pi_rec_data_arr[$value[csf('pi_id')]][$value[csf('recv_number')]][$value[csf('prod_id')]]['receive_qnty']=$value[csf('receive_qnty')];
                        $pi_rec_data_arr[$value[csf('pi_id')]][$value[csf('recv_number')]][$value[csf('prod_id')]]['receive_amount']=$value[csf('receive_amount')];
                        $pi_rec_data_arr[$value[csf('pi_id')]][$value[csf('recv_number')]][$value[csf('prod_id')]]['item_group']=$value[csf('item_group_id')];
                        //$pi_rec_data_arr[$value[csf('wo_id')]]['work_order_id']=$value[csf('wo_id')];
                        //echo "sumon";
                        if($pi_dup_data_check[$value[csf('pi_id')]]=="")
                        {
                            $pi_dup_data_check[$value[csf('pi_id')]]=$value[csf('pi_id')];

                        }
                        $pi_rec_data_arr[$value[csf('pi_id')]][$value[csf('recv_number')]][$value[csf('prod_id')]]['remarks']=$value[csf('remarks')];
                    }
                    // echo "<pre>";
                    // print_r($pi_rec_data_arr);
                    //var_dump($pi_rec_data_arr);
                    $i=1;
                    foreach($pi_rec_data_arr as $pi_id_val)
                    {
                        foreach($pi_id_val as $prod_id_val)
                        {
                            foreach($prod_id_val as $row_recv)
                            {
                                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

                                $item_description=$row_recv['item_description'];
                                $item_group=$item_group_name[$row_recv['item_group']];

                                if(!in_array($row_recv['rec_id'],$recv_id_array))
                                {
                                    $recv_id_array[$row_recv['rec_id']]=$row_recv['rec_id'];
        							$recv_pi_array[$row_recv['pi_id']]=$row_recv['pi_id'];
                                }

                                $amount= $row_recv['receive_amount'];
                                $rate= $row_recv['receive_amount']/$row_recv['receive_qnty'];
                                $pi_data_array[$row_recv['pi_id']]['rcv']+=$row_recv['receive_qnty']*$rate;
                                
                                //print_r($pi_data_array);
                                ?>
                                <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trc_<? echo $i;?>','<? echo $bgcolor;?>')" id="trc_<? echo $i;?>">
                                    <td width="40" align="center"><? echo $i; ?>&nbsp;</td>
                                    <td width="100"><p><? echo $row_recv['recv_number']; ?></p></td>
                                    <td width="90" align="center"><? echo change_date_format($row_recv['receive_date']); ?></td>
                                    <td width="80"><p>&nbsp;<? echo $row_recv['challan_no']; ?></p></td>
                                    <td width="70" align="center"><p><? echo $item_group; ?></p></td>
                                    <td width="150"><p>&nbsp;<? echo $item_description; ?></p></td>
                                    <td width="80" align="right"><p>&nbsp;<? echo $row_recv['receive_qnty']; ?></p></td>
                                    <td width="70" align="right"><? echo number_format($rate,4); ?></td>
                                    <td width="100" align="right"><? echo number_format($amount,4,'.',''); ?>&nbsp;</td>
                                    <td><p>&nbsp;<? echo $row_recv['remarks']; ?></p></td>
                                </tr>
                                <?
                                $tot_recv_qnty+=$row_recv['receive_qnty'];
                                $tot_recv_amnt+=$amount;
                                $i++;
                            }
                        }
                    }
                    ?>
                    <tfoot>
                        <th colspan="6" align="right">Total</th>
                        <th align="right"><?php echo number_format($tot_recv_qnty,2,'.','');?></th>
                        <th>&nbsp;</th>
                        <th align="right"><?php echo number_format($tot_recv_amnt,4,'.','');?></th>
                        <th>&nbsp;</th>
                    </tfoot>
                </table>
            </div>
            <!-- Stationaries Receive End -->
            <br>
            <!-- General Stationaries Receive Return Start -->
            <table width="910" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                <thead>
                    <tr>
                      <th colspan="9">General Stationaries Receive Return</th>
                    </tr>
                    <tr>
                        <th width="40">SL</th>
                        <th width="130">Return No</th>
                        <th width="100">Return Date</th>
                        <th width="90">Item Group</th>
                        <th width="150">Item Description</th>
                        <th width="100">Qnty</th>
                        <th width="100">Rate</th>
                        <th width="100">Value</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
            </table>
            <div style="width:910px; overflow-y:scroll; max-height:430px;"  align="left">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="890" class="rpt_table" id="tbl_finish_feb_rcv_rtrn" >
                    <?
                    $tot_retn_qnty=0; $tot_retn_amnt=0;
                    if(count($recv_id_array)>0)
                    {
                        $recv_id_all=implode(",",$recv_id_array);
                        
                        if($$goods_recv_status_array['goods_rcv_status'] == 1){ //After goods received
                            $pi_booking_relation_rcv_rtn_cond = " and f.pi_wo_batch_no = g.work_order_id";
                        }else{ //Before goods received
                            $pi_booking_relation_rcv_rtn_cond = " and f.pi_wo_batch_no = g.pi_id";
                        }

                        $sql_retn="select distinct a.id, a.issue_number, a.issue_date, a.challan_no, b.remarks, b.cons_quantity as return_qnty, b.cons_amount as return_amount, c.product_name_details, f.pi_wo_batch_no AS wo_id, f.id as rcv_trans_id,g.pi_id
                        from inv_issue_master a, inv_transaction b, product_details_master c, inv_transaction f, com_pi_item_details g
                        where a.id = b.mst_id and b.prod_id = c.id $pi_booking_relation_rcv_rtn_cond and a.entry_form = 26 and a.received_id = f.mst_id and f.pi_wo_batch_no > 0 and a.company_id=$cbo_company_name and a.received_id in($recv_id_all) and  b.item_category=11 and b.transaction_type=3 and f.transaction_type = 1 and a.status_active = 1 and c.is_deleted = 0 and b.status_active = 1";
                        //echo $sql_retn;
                        $dataRtArray=sql_select($sql_retn);

                        $i=1;
                        foreach($dataRtArray as $row_retn)
                        {
							$all_rtn_id[$row_retn[csf("id")]]=$row_retn[csf("id")];
                            if ($i%2==0)
                                $bgcolor="#E9F3FF";
                            else
                                $bgcolor="#FFFFFF";

                            if($db_type==0)
							{
								$conversion_date=date("Y-m-d",strtotime($row_retn[csf("issue_date")]));
							}
							else
							{
								$conversion_date=date("d-M-y",strtotime($row_retn[csf("receive_date")]));
							}

                            if(is_nan($row_retn[csf('return_amount')]/$row_retn[csf('return_qnty')])){
                                $rate= 0;
                            }else{
                                $rate = $row_retn[csf('return_amount')]/$row_retn[csf('return_qnty')];
                            }
                        ?>
                            <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trf_<? echo $i;?>','<? echo $bgcolor;?>')" id="trf_<? echo $i;?>">
                                <td width="40" align="center"><? echo $i; ?>&nbsp;</td>
                                <td width="130"><p><? echo $row_retn[csf('issue_number')]; ?></p></td>
                                <td width="100" align="center"><? echo change_date_format($row_retn[csf('issue_date')]); ?></td>
                                <td width="90"><p>&nbsp;<? echo $row_retn[csf('item_name')]; ?></p></td>
                                <td width="150"><p>&nbsp;<? echo $row_retn[csf('product_name_details')]; ?></p></td>
                                <td width="100" align="right"><? echo number_format($row_retn[csf('return_qnty')],2,'.',''); ?>&nbsp;</td>
                                <td width="100" align="right" title="<? echo 'Rate '.$rate;?>"><? echo number_format($rate,4,'.',''); ?>&nbsp;</td>
                                <td width="100" align="right"><? echo number_format($row_retn[csf('return_amount')],4,'.',''); ?>&nbsp;</td>
                                <td><p>&nbsp;<? echo $row_retn[csf('remarks')]; ?></p></td>
                            </tr>
                        <?
                        
                            
                            $tot_retn_qnty+=$row_retn[csf('return_qnty')];
                            $tot_retn_amnt+=$row_retn[csf('return_amount')];
                            $pi_data_array[$row_retn[csf('pi_id')]]['rtn']+=$row_retn[csf('return_qnty')]*$rate;

                            $i++;
                        }
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
					/*if ($pi_id_all!='' || $pi_id_all!=0)
					{
						$sql_retn="select a.received_id, a.pi_id, a.issue_number, a.issue_date, a.challan_no, b.remarks, (b.order_rate+b.order_ile_cost) as ord_rate, b.cons_rate as rate, b.rcv_rate, b.cons_quantity, b.cons_amount, c.product_name_details
						from inv_issue_master a, inv_transaction b, product_details_master c
						where a.item_category=11 and a.entry_form=8 and a.company_id=$cbo_company_name and a.pi_id in($pi_id_all) and a.id=b.mst_id and b.item_category=11 and b.transaction_type=3 and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $rtn_cond";
						//echo $sql_retn;
						$dataRtArray=sql_select($sql_retn);
                        $i=1;
						foreach($dataRtArray as $row_retn)
						{
							if ($i%2==0)
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";

							//$rate=$row_retn[csf('rate')]/$exchange_rate;
							$rate=$row_retn[csf('rcv_rate')]/$exchange_rate;
							$amnt=$row_retn[csf('cons_quantity')]*$rate;
						?>
							<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trr_<? echo $i;?>','<? echo $bgcolor;?>')" id="trr_<? echo $i;?>">
								<td width="40" align="center"><? echo $i; ?>&nbsp;</td>
								<td width="100" align="center"><? echo change_date_format($row_retn[csf('issue_date')]); ?></td>
								<td width="130"><p><? echo $row_retn[csf('issue_number')]; ?></p></td>
								<td width="290"><p>&nbsp;<? echo $row_retn[csf('product_name_details')]; ?></p></td>
								<td width="110" align="right"><? echo number_format($row_retn[csf('cons_quantity')],2,'.',''); ?>&nbsp;</td>
								<td width="100" align="right" title="<? echo 'Rate '.$row_retn[csf('rcv_rate')].' exchange_rate= '.$exchange_rate;?>"><? echo number_format($rate,2,'.',''); ?>&nbsp;</td>
								<td width="120" align="right"><? echo number_format($amnt,2,'.',''); ?>&nbsp;</td>
								<td><p>&nbsp;<? echo $row_retn[csf('remarks')]; ?></p></td>
							</tr>
						<?
							$pi_id=$row_retn[csf('pi_id')];
							$tot_retn_qnty+=$row_retn[csf('cons_quantity')];
							$tot_retn_amnt+=$amnt;
							$pi_data_array[$pi_id]['rtn']+=$amnt;

							$i++;
						}
                    }*/

                    $total_balance_qty = ($tot_pi_qnty+$tot_retn_qnty-$tot_recv_qnty);
                    $total_balance_value = ($tot_pi_amnt+$tot_retn_amnt-$tot_recv_amnt);
                    ?>
                    <tfoot>
                        <tr>
                            <th colspan="5" align="right">Total</th>
                            <th align="right"><?php echo number_format($tot_retn_qnty,2,'.','');?></th>
                            <th>&nbsp;</th>
                            <th align="right"><?php echo number_format($tot_retn_amnt,4,'.','');?></th>
                            <th>&nbsp;</th>
                        </tr>
                        <tr>
                            <th colspan="5" align="right">Net PI Balance</th>
                            <th align="right"><?php echo number_format($total_balance_qty,2,'.','');?></th>
                            <th>&nbsp;</th>
                            <th align="right"><?php echo number_format($total_balance_value,4,'.','');?></th>
                            <th>&nbsp;</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <!-- General Stationaries Receive Return End -->
            <br>
            <!-- General Stationaries Acceptance Details Start -->
            <table width="850" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                <thead>
                    <tr>
                      <th colspan="8">General Stationaries Acceptance Details </th>
                    </tr>
                    <tr>
                        <th width="40">SL</th>
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
            <div style="width:870px; overflow-y:scroll; max-height:430px;"  align="left">
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
                        $acceptance_sql = "select   m.id as pi_id, a.company_acc_date, b.current_acceptance_value as acceptance_value
						from com_pi_master_details m
						left  join com_import_invoice_dtls b on m.id=b.pi_id   and b.is_lc=1 and b.status_active=1 and b.is_deleted=0
						left  join com_import_invoice_mst a on a.id=b.import_invoice_id
						where  m.id in($pi_id_all)
						order by m.id";
                        // echo $acceptance_sql;

						$accep_result=sql_select($acceptance_sql);

                        //echo $count_row[1261].jahid;die;
                        $payble_value=0; $tot_payble_value=0; $yet_to_accept=0; $tot_accept_value=0; $tot_yet_to_accept=0; $total_receive_value=0; $total_return_value=0;
                        $i=1;
						foreach($accep_result as $row)
						{
							$accep_breakdown_data[$row[csf("pi_id")]]++;
							$acceptance_arr[$row[csf("pi_id")]]+=$row[csf("acceptance_value")];


    						if ($i%2==0)
    							$bgcolor="#E9F3FF";
    						else
    							$bgcolor="#FFFFFF";
    						?>
    						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tra_<? echo $i;?>','<? echo $bgcolor;?>')" id="tra_<? echo $i;?>">
    							<?
    							if($temp_pi[$row[csf("pi_id")]]=="")
    							{
    								$payble_value=$yet_to_accept=0;
    								$payble_value=$pi_data_array[$row[csf("pi_id")]]['rcv']-$pi_data_array[$row[csf("pi_id")]]['rtn'];
    								$yet_to_accept=$payble_value-$acceptance_arr[$row[csf("pi_id")]];

    								$total_receive_value+=$pi_data_array[$row[csf("pi_id")]]['rcv'];
    								$total_return_value+=$pi_data_array[$row[csf("pi_id")]]['rtn'];

    								$tot_payble_value+=$payble_value;
    								$tot_accept_value+=$acceptance_arr[$row[csf("pi_id")]];
    								$tot_yet_to_accept+=$yet_to_accept;
    								?>
    								<td width="40"><? echo $i;?></p></td>
    								<td width="140" rowspan="<? echo $accep_breakdown_data[$row[csf("pi_id")]];?>"><p><? echo $pi_name_array[$row[csf("pi_id")]]; ?></p></td>
    								<td width="100"  align="right" rowspan="<? echo $accep_breakdown_data[$row[csf("pi_id")]];?>"><? echo number_format($pi_data_array[$row[csf("pi_id")]]['rcv'],4,'.',''); ?>&nbsp;</td>
    								<td width="100" align="right" rowspan="<? echo $accep_breakdown_data[$row[csf("pi_id")]];?>"><? echo number_format($pi_data_array[$row[csf("pi_id")]]['rtn'],4,'.',''); ?>&nbsp;</td>
    								<td width="100" align="right" rowspan="<? echo $accep_breakdown_data[$row[csf("pi_id")]];?>"><? echo number_format($payble_value,4,'.',''); ?>&nbsp;</td>
    								<?
    							}
    							?>
    							<td width="100" align="center"><? if($row[csf("company_acc_date")]!="" && $row[csf("company_acc_date")]!="0000-00-00") echo change_date_format($row[csf("company_acc_date")]);?>&nbsp;</td>
    							<td width="100" align="right"><? echo number_format($row[csf("acceptance_value")],4,'.',''); ?>&nbsp;</td>
    							<?
    							if($temp_pi[$row[csf("pi_id")]]=="")
    							{
    								$temp_pi[$row[csf("pi_id")]]=$row[csf("pi_id")];
    								?>
    								<td align="right" rowspan="<? echo $accep_breakdown_data[$row[csf("pi_id")]];?>"><? echo number_format($yet_to_accept,4,'.',''); ?></td>
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
                        <th colspan="2" align="right">Total</th>
                        <th align="right"><?php echo number_format($total_receive_value,4,'.',''); ?></th>
                        <th align="right"><?php echo number_format($total_return_value,4,'.',''); ?></th>
                        <th align="right"><?php echo number_format($tot_payble_value,4,'.',''); ?></th>
                        <th align="right">&nbsp;</th>
                         <th align="right"><?php echo number_format($tot_accept_value,4,'.',''); ?></th>
                        <th align="right"><?php echo number_format($tot_yet_to_accept,4,'.',''); ?></th>
                    </tfoot>
                </table>
            </div>
            <!-- General Stationaries Acceptance Details End -->
            <?
            //echo $cbo_company_id."sumon".$cbo_company_name;
              // echo signature_table(142, str_replace("'","",$cbo_company_name), "1100px");
            ?>
            <div width="100%">
                <?
                //echo $cbo_company_id."sumon".$cbo_company_name;
                   //echo signature_table(142, str_replace("'","",$cbo_company_name), 300);
                ?>
            </div>
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
