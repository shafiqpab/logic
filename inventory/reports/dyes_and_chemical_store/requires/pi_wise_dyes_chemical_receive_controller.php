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
	echo create_drop_down( "cbo_store_name", 150, "select a.id, a.store_name from lib_store_location a,lib_store_location_category b  where a.id=b.store_location_id and a.company_id='$data' and b.category_type in(5,6,7,23) and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name","id,store_name", 1, "-- Select Store --", 0, "" );
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
							echo create_drop_down( "cbo_supplier_id", 150,"select DISTINCT(c.id),c.supplier_name from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$companyID' and b.party_type=3",'id,supplier_name', 1, '-- All Supplier --',0,'',0);
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
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_supplier_id').value+'_'+document.getElementById('txt_search_common').value+'_'+<? echo $companyID; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_pi_search_list_view', 'search_div', 'pi_wise_dyes_chemical_receive_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
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
exit();
}



$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );

if($action=="create_pi_search_list_view")
{
	$ex_data = explode("_",$data);
	
	if($ex_data[0]==0) $cbo_supplier = "%%"; else $cbo_supplier = $ex_data[0];
	$txt_search_common = trim($ex_data[1]);
	$company = $ex_data[2];
	$from_date = $ex_data[3];
	$to_date = $ex_data[4];
	if( $pi_date!="" && $to_date!="")
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
	//$sql= "select id, lc_number, supplier_id, importer_id, lc_date, last_shipment_date, lc_value from com_btb_lc_master_details where importer_id=$company and item_category_id=1 and supplier_id like '$cbo_supplier' and lc_number like '%".$txt_search_common."%' and is_deleted=0 and status_active=1";
	$sql= "select id, pi_number, supplier_id, importer_id, pi_date, last_shipment_date, total_amount from com_pi_master_details where importer_id=$company and item_category_id in(5,6,7,23) and supplier_id like '$cbo_supplier' and pi_number like '%".$txt_search_common."%' and is_deleted=0 and status_active=1 $pi_date_cond";//die;
	
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
							echo create_drop_down( "cbo_supplier_id", 160,"select DISTINCT(c.id),c.supplier_name from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$companyID' and b.party_type=3",'id,supplier_name', 1, '-- All Supplier --',0,'',0);
                        ?>
                    </td>
                    <td align="center" id="search_by_td">				
                        <input type="text" style="width:230px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                    </td> 
                     <td align="center">
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_supplier_id').value+'_'+document.getElementById('txt_search_common').value+'_'+<? echo $companyID; ?>, 'create_lc_search_list_view', 'search_div', 'pi_wise_dyes_chemical_receive_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
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
exit();
}


if($action=="create_lc_search_list_view")
{
	$ex_data = explode("_",$data);
	
	if($ex_data[0]==0) $cbo_supplier = "%%"; else $cbo_supplier = $ex_data[0];
	$txt_search_common = trim($ex_data[1]);
	$company = $ex_data[2];
	
	$sql= "select id, lc_number, supplier_id, importer_id, lc_date, last_shipment_date, lc_value from com_btb_lc_master_details where importer_id=$company and supplier_id like '$cbo_supplier' and lc_number like '%".$txt_search_common."%' and is_deleted=0 and status_active=1";
   // echo $sql;
	
	$arr=array(1=>$company_arr,2=>$supplier_arr);
	echo create_list_view("list_view", "LC No, Importer, Supplier Name, LC Date, Last Shipment Date, LC Value","130,110,130,90,130","780","260",0, $sql , "js_set_value", "id,lc_number", "", 1, "0,importer_id,supplier_id,0,0,0,0", $arr, "lc_number,importer_id,supplier_id,lc_date,last_shipment_date,lc_value", "",'','0,0,0,3,3,2') ;	
	exit();
	
}

$store_arr=return_library_array( "select id, store_name from lib_store_location", "id", "store_name"  );
$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	//if(str_replace("'","",$cbo_store_name)!="") $store_cond=" and b.store_id in(".str_replace("'","",$cbo_store_name).")";
	$pi_no_cond=str_replace("'","",$txt_pi_no);
	$btbLc_id_str=str_replace("'","",$btbLc_id);
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
	$item_group_arr=return_library_array( "select id, item_name from lib_item_group", "id", "item_name"  );
	$sub_group_arr=return_library_array( "select item_group_id, sub_group_name from product_details_master", "item_group_id", "sub_group_name"  );
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
				$btbLcData=sql_select("select a.id,a.lc_number, a.supplier_id, a.importer_id, a.lc_date, a.last_shipment_date, a.lc_value 
				from com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_master_details c 
				where a.id=b.com_btb_lc_master_details_id and c.id=b.pi_id and c.item_category_id in(5,6,7,23) and c.importer_id=$cbo_company_name and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $btbLc_id_cond_mst $pi_cond_btb group by a.id, a.lc_number, a.supplier_id, a.importer_id, a.lc_date, a.last_shipment_date, a.lc_value");
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
            </table>
            <div style="width:1118px; overflow-y:scroll; max-height:430px;"  align="left">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1100" class="rpt_table" id="tbl_dyes_chemical_btb_lc_dtls" >
                    <?
                    if(count($btbLcData)>0)
                    {
                        foreach($btbLcData as $row)
                        {
                            ?>
                            <tr bgcolor="#FFFFFF">
                                <td width="140"><p><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></p></td>
                                <td width="140"><p><? echo $company_arr[$row[csf('importer_id')]]; ?></p></td>
                                <td width="140"><p><? echo $store_arr[str_replace("'","",$cbo_store_name)]; ?>&nbsp;</p></td>
                                <td width="140"><p><? echo $supplier_arr[$row[csf('supplier_id')]]; ?></p></td>
                                <td width="140"><p><? echo $row[csf('lc_number')]; ?></p></td>
                                <td width="100" align="center"><? echo change_date_format($row[csf('lc_date')]); ?></td>
                                <td width="120" align="center"><? echo change_date_format($row[csf('last_shipment_date')]); ?></td>
                                <td align="right"><? echo number_format($row[csf('lc_value')],2); ?></td>
                            </tr>
                            <?
                        }
                    }
                    ?>
                </table>
            </div>
            <br>
            <table width="1200" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">            	
                <thead>
                    <tr>
                        <th colspan="12">PI Details</th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="100">PI Number</th>
                        <th width="80">PI Date</th>
                        <th width="100">Supplier Name</th>
                        <th width="110">Category</th>
                        <th width="110">Item Group</th>
                        <th width="110">Sub Group</th>
                        <th width="130">Item Description</th>
                        <th width="90">Qnty</th>
                        <th width="90">Rate</th>                            
                        <th width="90">Value</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
            </table>
            <div style="width:1220px; overflow-y:scroll; max-height:430px;"  align="left">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1200" class="rpt_table" id="tbl_dyes_chemical_pi_dtls" >
                    <tbody>
                    <? 	
                        $i=1; $work_order_id=array(); $pi_id_all_array=array(); $pi_name_array=array(); $rate=0; $compos=''; $tot_pi_qnty=0; $tot_pi_amnt=0;
                        $sql="select c.work_order_id, b.id, b.pi_number, b.pi_date, b.currency_id, b.remarks, b.item_category_id, b.supplier_id, c.item_prod_id, c.item_group, c.item_description, sum(c.quantity) as qnty, sum(c.net_pi_amount) as amnt 
                        from com_btb_lc_pi a, com_pi_master_details b, com_pi_item_details c 
                        where a.pi_id=b.id and b.id=c.pi_id and b.item_category_id in(5,6,7,23) and b.importer_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $pi_cond $btbLc_id_cond 
                        group by c.work_order_id, b.id, b.pi_number, b.pi_date, b.currency_id, b.remarks, b.item_category_id, b.supplier_id, c.item_prod_id, c.item_group, c.item_description";
                        // echo $sql;
                        $result=sql_select($sql);
                        foreach($result as $row)
                        {
                            if ($i%2==0)  
                                $bgcolor="#E9F3FF";
                            else
                                $bgcolor="#FFFFFF";
                            
                            if($row[csf('currency_id')]!=1) $ex_rate=1; else $ex_rate=$exchange_rate;  
                            
                            $rate=($row[csf('amnt')]/$row[csf('qnty')])/$ex_rate;
                            
                            if(!in_array($row[csf('id')],$pi_id_all_array))
                            {
                                $pi_id_all_array[$row[csf('id')]]=$row[csf('id')];
                                $pi_name_array[$row[csf('id')]]=$row[csf('pi_number')];
                            }
                          
                                $work_id_all_array[$row[csf('work_order_id')]]=$row[csf('work_order_id')];
                          
                            
                        ?>
                            <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                                <td width="30"><? echo $i; ?></td>
                                <td width="100"><p><? echo $row[csf('pi_number')]; ?></p></td>
                                <td width="80" align="center"><? echo change_date_format($row[csf('pi_date')]); ?></td>
                                <td width="100"><p><? echo $supplier_arr[$row[csf('supplier_id')]]; ?></p></td>
                                <td width="110"><p>&nbsp;<? echo $item_category[$row[csf('item_category_id')]]; ?></p></td>
                                <td width="110"><p>&nbsp;<? echo $item_group_arr[$row[csf('item_group')]]; ?></p></td>
                                <td width="110"><p>&nbsp;<? echo $sub_group_arr[$row[csf('item_group')]]; ?></p></td>
                                <td width="130"><p>&nbsp;<? echo $row[csf('item_description')]; ?></p></td>
                                <td width="90" align="right"><? echo number_format($row[csf('qnty')],2,'.',''); ?>&nbsp;</td>
                                <td width="90" align="right"><? echo number_format($rate,4,'.',''); ?>&nbsp;</td>
                                <td width="90" align="right"><? echo number_format($row[csf('amnt')],2,'.',''); ?>&nbsp;</td>
                                <td><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
                            </tr>
                        <?
                        
                            $tot_pi_qnty+=$row[csf('qnty')]; 
                            $tot_pi_amnt+=$row[csf('amnt')];
                            
                            $i++;
                        }
                    ?>
                    </tbody>
                    <tfoot>
                        <th colspan="8" align="right">Total</th>
                        <th align="right"> <?php echo number_format($tot_pi_qnty,2,'.','');?></th>
                        <th>&nbsp;</th>
                        <th align="right"> <?php echo number_format($tot_pi_amnt,2,'.','');?></th>
                        <th>&nbsp;</th>
                    </tfoot>
                </table>
            </div>
            <br>
            <table width="1200" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                <thead>
                    <tr>
                      <th colspan="13">Dyes & Chemical Receive</th>
                    </tr>
                    <tr>	
                        <th width="60">Recv. Date</th>
                        <th width="110">MRR No</th>
                        <th width="70">Accounting Posting</th>
                        <th width="100">Challan No</th>
                        <th width="100">Supplier Name</th>
                        <th width="120">Category</th>
                        <th width="120">Item Group</th>            
                        <th width="140">Item Description</th>
                        <th width="80">Qnty</th>
                        <th width="70">Rate</th>
                        <th width="80">Value</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
            </table>
            <div style="width:1220px; overflow-y:scroll; max-height:430px;"  align="left">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1200" class="rpt_table" id="tbl_dyes_chemical_rcv" >
                    <?
                        //$pi_id_all=implode(",",array_flip($pi_id_all_array));
                        $pi_id_all=implode(",",$pi_id_all_array); 
                        $wo_id_all=implode(",",$work_id_all_array); 
                        $compos=''; $tot_recv_qnty=0; $tot_recv_amnt=0; $recv_id_array=array(); $recv_pi_array=array(); $pi_data_array=array();
                        $sql_recv='';
                        if ($pi_id_all!='')
                        {
                            $sql_recv="SELECT a.id, a.recv_number, a.receive_date, a.supplier_id, a.is_posted_account, b.remarks, a.challan_no, a.currency_id, a.exchange_rate, b.pi_wo_batch_no,(b.order_rate+b.order_ile_cost) as rate, b.order_qnty, b.order_amount, c.item_category_id, c.item_group_id, c.item_description
                            from inv_receive_master a, inv_transaction b, product_details_master c 
                            where a.item_category in(5,6,7,23) and a.entry_form=4 and a.company_id=$cbo_company_name and a.receive_basis=1 and a.booking_id in($pi_id_all) and a.id=b.mst_id and b.item_category in(5,6,7,23) and b.transaction_type=1 and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.store_id like '$store' ";
                        }
                        if($wo_id_all!='')
                        {
                            if($sql_recv!=''){$sql_recv.=" union all ";}
                            $sql_recv.="SELECT a.id, a.recv_number, a.receive_date, a.supplier_id, a.is_posted_account, b.remarks, a.challan_no, a.currency_id, a.exchange_rate, b.pi_wo_batch_no,(b.order_rate+b.order_ile_cost) as rate, b.order_qnty, b.order_amount, c.item_category_id, c.item_group_id, c.item_description
                            from inv_receive_master a, inv_transaction b, product_details_master c 
                            where a.item_category in(5,6,7,23) and a.entry_form=4 and a.company_id=$cbo_company_name and a.receive_basis=2 and a.booking_id in($wo_id_all) and a.id=b.mst_id and b.item_category in(5,6,7,23) and b.transaction_type=1 and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.store_id like '$store' ";
                        }
                        if($sql_recv!=''){$sql_recv.=" order by id ";}
                        // echo $sql_recv;
                        
                        $dataArray=sql_select($sql_recv);

                        foreach($dataArray as $row_recv)
                        {
                            if ($i%2==0)  
                                $bgcolor="#E9F3FF";
                            else
                                $bgcolor="#FFFFFF";
                            
                            if(!in_array($row_recv[csf('id')],$recv_id_array))
                            {
                                $recv_id_array[$row_recv[csf('id')]]=$row_recv[csf('id')];
                                $recv_pi_array[$row_recv[csf('id')]]=$row_recv[csf('pi_wo_batch_no')];
                            }
                            
                            if($row_recv[csf('currency_id')]!=1) $ex_rate=1; else $ex_rate=$exchange_rate;  
                            
                            $order_rate=$row_recv[csf('order_amount')]/$row_recv[csf('order_qnty')];
                            
                            $pi_data_array[$row_recv[csf('pi_wo_batch_no')]]['rcv']+=$row_recv[csf('order_amount')]/$ex_rate;
                            
                        ?>
                            <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                                <td width="60" align="center"><? echo change_date_format($row_recv[csf('receive_date')]); ?></td>
                                <td width="110"><p><? echo $row_recv[csf('recv_number')]; ?></p></td>
                                <td width="70" align="center"><p><? echo ($row_recv[csf('is_posted_account')]==1)? "Yes":"No" ;?></p></td>
                                <td width="100"><p>&nbsp;<? echo $row_recv[csf('challan_no')]; ?></p></td>
                                <td width="100"><p>&nbsp;<? echo $supplier_arr[$row_recv[csf('supplier_id')]]; ?></p></td>
                                <td width="120"><p>&nbsp;<? echo $item_category[$row_recv[csf('item_category_id')]]; ?></p></td>
                                <td width="120" align="center"><p>&nbsp;<? echo $item_group_arr[$row_recv[csf('item_group_id')]]; ?></p></td>
                                <td width="140"><p><? echo $row_recv[csf('item_description')]; ?></p></td>
                                <td width="80" align="right"><? echo number_format($row_recv[csf('order_qnty')],2,'.',''); ?>&nbsp;</td>
                                <td width="70" align="right"><? echo number_format($order_rate,2,'.',''); ?>&nbsp;</td>
                                <td width="80" align="right"><? echo number_format($row_recv[csf('order_amount')],2,'.',''); ?>&nbsp;</td>
                                <td><p><? echo $row_recv[csf('remarks')]; ?></p></td>
                            </tr>
                        <?
                        
                            $tot_recv_qnty+=$row_recv[csf('order_qnty')]; 
                            $tot_recv_amnt+=$row_recv[csf('order_amount')];
                            
                            $i++;
                        }
                    ?>
                    <tfoot>
                        <th colspan="8" align="right">Total</th>
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
                      <th colspan="9">Dyes & Chemical Return</th>
                    </tr>
                    <tr>	
                        <th width="100">Return Date</th>
                        <th width="120">Return No</th>
                        <th width="110">Category</th>
                        <th width="110">Item Group</th>
                        <th width="150">Item Description</th>
                        <th width="110">Qnty</th>
                        <th width="100">Rate</th>
                        <th width="120">Value</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
            </table>
            <div style="width:1120px; overflow-y:scroll; max-height:430px;"  align="left">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1100" class="rpt_table" id="tbl_dyes_chemical_rcv_rtrn" >
                    <?  
                        $tot_retn_qnty=0; $tot_retn_amnt=0;
                        if(count($recv_id_array)>0)
                        {
                            $recv_id_all=implode(",",$recv_id_array);
                            $sql_retn="select a.received_id, a.issue_number, a.issue_date, a.challan_no,b.remarks, b.cons_rate as rate, b.cons_quantity, b.cons_amount, c.item_category_id, c.item_group_id, c.item_description
                            from inv_issue_master a, inv_transaction b, product_details_master c 
                            where a.item_category in(5,6,7,23) and a.entry_form=28 and a.company_id=$cbo_company_name and a.received_id in($recv_id_all) and a.id=b.mst_id and b.item_category in(5,6,7,23) and b.transaction_type=3 and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
                            $dataRtArray=sql_select($sql_retn);
                            foreach($dataRtArray as $row_retn)
                            {
                                if ($i%2==0)  
                                    $bgcolor="#E9F3FF";
                                else
                                    $bgcolor="#FFFFFF";
                                
                                $rate=$row_retn[csf('rate')]/$exchange_rate;
                                $amnt=$row_retn[csf('cons_quantity')]*$rate;
                                
                                ?>
                                <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                                    <td width="100" align="center"><? echo change_date_format($row_retn[csf('issue_date')]); ?></td>
                                    <td width="120"><p><? echo $row_retn[csf('issue_number')]; ?></p></td>
                                    <td width="110"><? echo $row_retn[csf('item_category_id')]; ?>&nbsp;</td>
                                    <td width="110"><? echo $row_retn[csf('item_group_id')]; ?>&nbsp;</td>
                                    <td width="150"><p>&nbsp;<? echo $row_retn[csf('item_description')]; ?></p></td>
                                    <td width="110" align="right"><? echo number_format($row_retn[csf('cons_quantity')],2,'.',''); ?>&nbsp;</td>
                                    <td width="100" align="right"><? echo number_format($rate,2,'.',''); ?>&nbsp;</td>
                                    <td width="120" align="right"><? echo number_format($amnt,2,'.',''); ?>&nbsp;</td>
                                    <td><p><? echo $row_retn[csf('remarks')]; ?></p></td>
                                </tr>
                                <?
                                $pi_id=$recv_pi_array[$row_retn[csf('received_id')]];
                                $tot_retn_qnty+=$row_retn[csf('cons_quantity')]; 
                                $tot_retn_amnt+=$amnt;
                                $pi_data_array[$pi_id]['rtn']+=$amnt;
                                $i++;
                            }
                        }
                        
                        if ($pi_id_all!='' || $pi_id_all!=0)
                        {
                            $sql_retn="select a.received_id, a.pi_id, a.issue_number, a.issue_date, a.challan_no, b.cons_rate as rate, b.cons_quantity, b.cons_amount, c.item_category_id, c.item_group_id, c.item_description
                            from inv_issue_master a, inv_transaction b, product_details_master c 
                            where a.item_category in(5,6,7,23) and a.entry_form=28 and a.company_id=$cbo_company_name and a.pi_id in($pi_id_all) and a.id=b.mst_id and b.item_category in(5,6,7,23) and b.transaction_type=3 and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
                            $dataRtArray=sql_select($sql_retn);
                            foreach($dataRtArray as $row_retn)
                            {
                                if ($i%2==0)  
                                    $bgcolor="#E9F3FF";
                                else
                                    $bgcolor="#FFFFFF";
                                
                                $rate=$row_retn[csf('rate')]/$exchange_rate;
                                $amnt=$row_retn[csf('cons_quantity')]*$rate;
                                ?>
                                <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                                    <td width="100" align="center"><? echo change_date_format($row_retn[csf('issue_date')]); ?></td>
                                    <td width="120"><p><? echo $row_retn[csf('issue_number')]; ?></p></td>
                                    <td width="110"><? echo $row_retn[csf('item_category_id')]; ?>&nbsp;</td>
                                    <td width="110"><? echo $row_retn[csf('item_group_id')]; ?>&nbsp;</td>
                                    <td width="150"><p>&nbsp;<? echo $row_retn[csf('item_description')]; ?></p></td>
                                    <td width="110" align="right"><? echo number_format($row_retn[csf('cons_quantity')],2,'.',''); ?>&nbsp;</td>
                                    <td width="100" align="right"><? echo number_format($rate,2,'.',''); ?>&nbsp;</td>
                                    <td width="120" align="right"><? echo number_format($amnt,2,'.',''); ?>&nbsp;</td>
                                    <td><p><? echo $row_retn[csf('remarks')]; ?></p></td>
                                </tr>
                                <?
                                $pi_id=$row_retn[csf('pi_id')];
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
                            <th colspan="5" align="right">Total</th>
                            <th align="right"><?php echo number_format($tot_retn_qnty,2,'.','');?></th>
                            <th>&nbsp;</th>
                            <th align="right"><?php echo number_format($tot_retn_amnt,2,'.','');?></th>
                            <th>&nbsp;</th>
                        </tr>
                        <tr>
                            <th colspan="5" align="right">Balance</th>
                            <th align="right"><?php echo number_format($total_balance_qty,2,'.','');?></th>
                            <th>&nbsp;</th>
                            <th align="right"><?php echo number_format($total_balance_value,2,'.','');?></th>
                            <th>&nbsp;</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <br>
            <table width="1000" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                <thead>
                    <tr>
                        <th colspan="8">Acceptance Details</th>
                    </tr>
                    <tr>
                        <th width="140">PI Number</th>
                        <th width="150">Supplier Name</th>
                        <th width="100">Receive Value</th>
                        <th width="100">Return Value</th>
                        <th width="100">Payable Value</th>
                        <th width="100">Acceptance Date</th>
                        <th width="150">Acceptance Given</th>
                        <th>Yet To Accept</th>
                    </tr>
                </thead>
                </table>
            <div style="width:1020px; overflow-y:scroll; max-height:430px;"  align="left">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1000" class="rpt_table" id="tbl_dyes_chemical_acceptance" >
                    <?
                        if ($pi_id_all!='' || $pi_id_all!=0)
                        {
                            /*$acceptance_arr=return_library_array( "select pi_id, sum(current_acceptance_value) as acceptance_value from com_import_invoice_dtls where pi_id in($pi_id_all) and is_lc=1 and status_active=1 and is_deleted=0 group by pi_id", "pi_id", "acceptance_value"  );
                            echo "select a.invoice_date, b.pi_id, sum(b.current_acceptance_value) as acceptance_value 
                            from com_import_invoice_mst a, com_import_invoice_dtls b
                            where a.id=b.import_invoice_id and  b.pi_id in($pi_id_all) and b.is_lc=1 and b.status_active=1 and b.is_deleted=0 group by b.pi_id, a.invoice_date order by b.pi_id, a.invoice_date";
                            
                            $accep_sql=sql_select("select a.company_acc_date, b.pi_id, sum(b.current_acceptance_value) as acceptance_value 
                            from com_import_invoice_mst a, com_import_invoice_dtls b
                            where a.id=b.import_invoice_id and  b.pi_id in($pi_id_all) and b.is_lc=1 and b.status_active=1 and b.is_deleted=0 group by b.pi_id, a.company_acc_date order by b.pi_id, a.company_acc_date");*/
                            
                            
                            
                            $accep_sql=sql_select("select m.id as pi_id, m.supplier_id, a.company_acc_date, b.current_acceptance_value as acceptance_value 
                            from com_pi_master_details m  
                            left  join com_import_invoice_dtls b on m.id=b.pi_id   and b.is_lc=1 and b.status_active=1 and b.is_deleted=0  
                            left  join com_import_invoice_mst a on a.id=b.import_invoice_id 
                            where  m.id in($pi_id_all) 
                            order by m.id");
                            
                            foreach($accep_sql as $row)
                            {
                                $accep_breakdown_data[$row[csf("pi_id")]]++;
                                $acceptance_arr[$row[csf("pi_id")]]+=$row[csf("acceptance_value")];
                            }
                        }
                        //echo $count_row[1261].jahid;die;
                        $payble_value=0; $tot_payble_value=0; $yet_to_accept=0; $tot_accept_value=0; $tot_yet_to_accept=0; $total_receive_value=0; $total_return_value=0;
                        
                        /*foreach($pi_name_array as $key=>$value)
                        {
                        }*/
                        
                        foreach($accep_sql as $row)
                        {
                            if ($i%2==0)  
                                $bgcolor="#E9F3FF";
                            else
                                $bgcolor="#FFFFFF";
                            ?>
                            <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
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
                                    <td width="140" rowspan="<? echo $accep_breakdown_data[$row[csf("pi_id")]];?>"><p><? echo $pi_name_array[$row[csf("pi_id")]]; ?></p></td>
                                    <td width="150" rowspan="<? echo $accep_breakdown_data[$row[csf("pi_id")]];?>"><p><? echo $supplier_arr[$row[csf("supplier_id")]]; ?></p></td>
                                    <td width="100"  align="right" rowspan="<? echo $accep_breakdown_data[$row[csf("pi_id")]];?>"><? echo number_format($pi_data_array[$row[csf("pi_id")]]['rcv'],2,'.',''); ?>&nbsp;</td>
                                    <td width="100" align="right" rowspan="<? echo $accep_breakdown_data[$row[csf("pi_id")]];?>"><? echo number_format($pi_data_array[$row[csf("pi_id")]]['rtn'],2,'.',''); ?>&nbsp;</td>
                                    <td width="100" align="right" rowspan="<? echo $accep_breakdown_data[$row[csf("pi_id")]];?>"><? echo number_format($payble_value,2,'.',''); ?>&nbsp;</td>
                                    <?
                                }
                                ?>
                                <td width="100" align="center"><? if($row[csf("company_acc_date")]!="" && $row[csf("company_acc_date")]!="0000-00-00") echo change_date_format($row[csf("company_acc_date")]);?>&nbsp;</td>
                                <td width="150" align="right"><? echo number_format($row[csf("acceptance_value")],2,'.',''); ?>&nbsp;</td>
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
                        <th align="right">&nbsp;</th>
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
