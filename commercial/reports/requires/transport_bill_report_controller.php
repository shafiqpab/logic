<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
$_SESSION['page_permission']=$permission;

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

if ($action=="load_drop_down_transport_com")
{
	echo create_drop_down( "cbo_transport_company_name", 140, "select a.id,a.supplier_name from  lib_supplier a, lib_supplier_tag_company b where a.status_active =1 and a.is_deleted=0 and b.supplier_id=a.id and b.tag_company='$data'  and a.id in (select  supplier_id  from  lib_supplier_party_type where party_type in (35)) order by supplier_name","id,supplier_name", 1, "-- Select Transport --", $selected, "" );
	exit();
}

if($action=="report_generate")
{
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process ));
    $cbo_company_name   = str_replace("'","",$cbo_company_name);
    $cbo_type_name      = str_replace("'","",$cbo_type_name);   
    $cbo_transport_company_name = str_replace("'","",$cbo_transport_company_name);           
    $cbo_year_selection = str_replace("'","",$cbo_year_selection);    
    $txt_date_from      = str_replace("'","",$txt_date_from);    
    $txt_date_to        = str_replace("'","",$txt_date_to); 
    $cbo_date_type        = str_replace("'","",$cbo_date_type); 

    $company_arr=return_library_array( "select id, company_name from lib_company ",'id','company_name');
    $supplier_arr = return_library_array("select id, supplier_name from lib_supplier where is_deleted=0","id","supplier_name");
    // $bank_arr = return_library_array("select id,bank_name from lib_bank where is_deleted=0","id","bank_name");
    $sql_cond="";
    if($rpt_type==1)
    {
        if ($cbo_company_name) {$sql_cond.=" and a.company_id=$cbo_company_name";}
        if ($cbo_type_name) {$sql_cond.=" and a.type_id=$cbo_type_name";}
        if ($cbo_transport_company_name) {$sql_cond.=" and a.trans_com_id=$cbo_transport_company_name";}
        if ($txt_date_from != '' && $txt_date_to != '') 
        {
            if($cbo_date_type==1){
                if ($db_type == 0) {
                    $sql_cond.= " and a.bill_date between '" . change_date_format($txt_date_from, 'yyyy-mm-dd') . "' and '" . change_date_format($txt_date_to, 'yyyy-mm-dd') . "'";
                } else if ($db_type == 2) {
                    $sql_cond.= " and a.bill_date between '" . change_date_format($txt_date_from, '', '', 1) . "' and '" . change_date_format($txt_date_to, '', '', 1) . "'";
                }	
            }else{

                if ($db_type == 0) {
                    $sql_cond.= " and a.payable_date between '" . change_date_format($txt_date_from, 'yyyy-mm-dd') . "' and '" . change_date_format($txt_date_to, 'yyyy-mm-dd') . "'";
                } else if ($db_type == 2) {
                    $sql_cond.= " and a.payable_date between '" . change_date_format($txt_date_from, '', '', 1) . "' and '" . change_date_format($txt_date_to, '', '', 1) . "'";
                }
            }
        } 
        else 
        {
            if($db_type==0){$sql_cond.=" and year(a.bill_date)=".$cbo_year_selection."";}
            else{$sql_cond.=" and to_char(a.bill_date,'YYYY')=".$cbo_year_selection."";}
        }
        $mst_sql="SELECT a.id as ID, a.sys_number as SYS_NUMBER, a.company_id as COMPANY_ID,a.type_id as TRANS_TYPE, a.bill_no as BILL_NO, a.bill_date as BILL_DATE, a.trans_com_id as TRANS_COM_ID, a.ship_mode as SHIP_MODE, b.id as DTLS_ID, b.vechicale_no as VECHICALE_NO, b.amount as AMOUNT, a.payable_date
        from transport_bill_mst a, transport_bill_dtls b	
        where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_cond";
        // echo $mst_sql;
        $mst_sql_result=sql_select($mst_sql);
        unset($mst_sql);
        ob_start();
        ?>
            <div style="width:820px;">
                <table width="800" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr>
                            <th width="30">Sl No</th>
                            <th width="50">Type</th>
                            <th width="140">Transport. Company</th>
                            <th width="80">Bill Date</th>
                            <th width="100">System ID</th>
                            <th width="100">Bill NO</th>
                            <th width="70">Ship Mode</th>
                            <th width="100">Vechicle No</th>
                            <th >Total Amount TK</th>
                        </tr>
                    </thead>
                    <tbody id="table_body" style="max-height: 50px; overflow-y:scroll;width:800px">
                        <?
                            $trans_type=array(1=>"Export",2=>"Import");
                            $i=1;
                            foreach($mst_sql_result as $row)
                            {
                                if($i%2==0){ $bgcolor="#E9F3FF"; }else{ $bgcolor="#FFFFFF"; }
                                ?>
                                    <tr bgcolor="<?=$bgcolor;?>" onclick="change_color('tr_<?=$i;?>','<?=$bgcolor;?>')" id="tr_<?=$i;?>">
                                        <td align="center"><?=$i;?></td>
                                        <td align="center"><?=$trans_type[$row['TRANS_TYPE']];?></td>
                                        <td><?=$supplier_arr[$row['TRANS_COM_ID']];?></td>
                                        <td align="center"><?=change_date_format($row['BILL_DATE']);?></td>
                                        <td><?=$row['SYS_NUMBER'];?></td>
                                        <td><?=$row['BILL_NO'];?></td>
                                        <td align="center"><?=$shipment_mode[$row['SHIP_MODE']];?></td>
                                        <td><?=$row['VECHICALE_NO'];?></td>
                                        <td align="right">
                                            <a href="##" onClick="openmypage_popup('<?=$row['TRANS_TYPE']; ?>','<?=$row['DTLS_ID']; ?>','<?=$row['SYS_NUMBER']; ?>','Transport Bill Info','transport_bill_popup');" ><?=number_format($row['AMOUNT'],2); $total_amount+=$row['AMOUNT']; ?></a>    
                                        </td>
                                    </tr>
                                <?
                                $i++;
                            }
                        ?>
                    </tbody>
                    <tfoot>
                        <th colspan="7"></th>
                        <th><strong>Total</strong></th>
                        <th><strong><?echo number_format($total_amount,2);?></strong></th>
                    </tfoot>
                </table>
            </div>
        <?
    }

    if($rpt_type==2)
    {
        if ($cbo_company_name) {$sql_cond.=" and a.company_id=$cbo_company_name";}
        if ($cbo_type_name) {$sql_cond.=" and a.type_id=$cbo_type_name";}
        if ($cbo_transport_company_name) {$sql_cond.=" and a.trans_com_id=$cbo_transport_company_name";}
        if ($txt_date_from != '' && $txt_date_to != '') 
        {
            // if ($db_type == 0) {
            //     $sql_cond.= " and a.bill_date between '" . change_date_format($txt_date_from, 'yyyy-mm-dd') . "' and '" . change_date_format($txt_date_to, 'yyyy-mm-dd') . "'";
            // } else if ($db_type == 2) {
            //     $sql_cond.= " and a.bill_date between '" . change_date_format($txt_date_from, '', '', 1) . "' and '" . change_date_format($txt_date_to, '', '', 1) . "'";
            // }	
            if($cbo_date_type==1){
                if ($db_type == 0) {
                    $sql_cond.= " and a.bill_date between '" . change_date_format($txt_date_from, 'yyyy-mm-dd') . "' and '" . change_date_format($txt_date_to, 'yyyy-mm-dd') . "'";
                } else if ($db_type == 2) {
                    $sql_cond.= " and a.bill_date between '" . change_date_format($txt_date_from, '', '', 1) . "' and '" . change_date_format($txt_date_to, '', '', 1) . "'";
                }	
            }else{

                if ($db_type == 0) {
                    $sql_cond.= " and a.payable_date between '" . change_date_format($txt_date_from, 'yyyy-mm-dd') . "' and '" . change_date_format($txt_date_to, 'yyyy-mm-dd') . "'";
                } else if ($db_type == 2) {
                    $sql_cond.= " and a.payable_date between '" . change_date_format($txt_date_from, '', '', 1) . "' and '" . change_date_format($txt_date_to, '', '', 1) . "'";
                }
            }
        } 
        else 
        {
            if($db_type==0){$sql_cond.=" and year(a.bill_date)=".$cbo_year_selection."";}
            else{$sql_cond.=" and to_char(a.bill_date,'YYYY')=".$cbo_year_selection."";}
        }
        $mst_sql="SELECT a.id, a.sys_number, a.company_id, a.type_id, a.bill_no, a.bill_date, a.ship_mode, a.depo, a.port_name, a.remarks, b.id as DTLS_ID, b.challan_no, b.challan_btb_id, b.invoice_id, b.qty, b.vechicale_no, b.no_vechicale, b.cbm_amt, b.amount, b.deduction, b.payable, a.payable_date 
        from transport_bill_mst a, transport_bill_dtls b
        where a.id=b.mst_id and a.status_active=1 and b.status_active=1 $sql_cond";
        // echo $mst_sql;
        $mst_sql_result=sql_select($mst_sql);
        $calan_btb_arr=$empty_arr=array();
        foreach($mst_sql_result as $row)
        {
            $calan_btb_arr[$row["CHALLAN_BTB_ID"]]=$row["CHALLAN_BTB_ID"];
        }

        $con = connect();
        fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 999, 1,$calan_btb_arr, $empty_arr);
	    oci_commit($con);
        $add_info_arr=array();
        if($cbo_type_name==1)
        {
            if($db_type==0){$sql_clm=" group_concat(distinct a.invoice_no) as INVOICE_NO";}
            else{ $sql_clm="listagg(cast( a.invoice_no as varchar(4000)),',') within group(order by a.id) as INVOICE_NO";}

            $dtls_sql = "SELECT b.delivery_mst_id, $sql_clm, sum(b.total_carton_qnty) as TOTAL_CARTON_QNTY, sum(b.ex_factory_qnty) as EX_FACTORY_QNTY from com_export_invoice_ship_mst a, pro_ex_factory_mst b, GBL_TEMP_ENGINE c where a.id=b.invoice_no and b.delivery_mst_id=c.ref_val and c.user_id=$user_id and c.entry_form=999 and a.status_active=1 and b.status_active=1 group by b.delivery_mst_id";
            // echo $dtls_sql;
            $dtls_sql_result=sql_select($dtls_sql);
            foreach($dtls_sql_result as $row)
            {
                $add_info_arr[$row["DELIVERY_MST_ID"]]['invoice_no']=$row["INVOICE_NO"];
                $add_info_arr[$row["DELIVERY_MST_ID"]]['tol_crtn_qty']=$row["TOTAL_CARTON_QNTY"];
                $add_info_arr[$row["DELIVERY_MST_ID"]]['ex_fcty_qty']=$row["EX_FACTORY_QNTY"];
            }

            $div_width=1650;
            $tbl_width=1600;
            $tbl_clpn=13;
            
        }
        else
        {
            $dtls_sql = "SELECT a.id, a.lc_number from com_btb_lc_master_details a, GBL_TEMP_ENGINE b where  a.id=b.ref_val and b.user_id=$user_id and b.entry_form=999 and a.status_active=1 ";
            // echo $dtls_sql;
            $dtls_sql_result=sql_select($dtls_sql);
            foreach($dtls_sql_result as $row)
            {
                $add_info_arr[$row["ID"]]["lc_number"]=$row["LC_NUMBER"];
            }

            $div_width=1550;
            $tbl_width=1500;
        }
        execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form=999");
        oci_commit($con);

        ob_start();
        ?>
            <div style="width:<?=$div_width;?>px;">
                <table width="<?=$tbl_width;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr>
                            <th width="120">Company</th>
                            <th width="80">Bill Date</th>
                            <th width="100">Bill No.</th>
                            <th width="100"> Payable Date.</th>
                            <th width="80">Ship Mode</th>
                            <th width="80"><?=($cbo_type_name==1)?'Depo':'CBM'?></th>
                            <th width="100">Port Name</th>
                            <th width="100">Challan No</th>
                            <th width="100"><?=($cbo_type_name==1)?'Inv. No.':'BTB LC Number'?></th>
                            <th width="80"><?=($cbo_type_name==1)?'Shipment Qty.':'Qty.'?></th>
                            <? if($cbo_type_name==1)
                                {
                                    ?>
                                        <th width="80">CTN Qty.</th>
                                        <th width="80">CBM</th>
                                    <?
                                } 
                            ?> 
                            <th width="100">Vechicle No</th>
                            <th width="80">No Of Vehicle</th>
                            <th width="80">Total Amount Taka</th>
                            <th width="80">Total Deduction</th>
                            <th width="80">Payable Amount</th>
                            <th >Remarks</th>
                        </tr>
                    </thead>
                    <tbody id="table_body" style="max-height: 50px; overflow-y:scroll;width:<?=$div_width;?>px">
                        <?
                            $i=1;
                            foreach($mst_sql_result as $row)
                            {
                                if($i%2==0){ $bgcolor="#E9F3FF"; }else{ $bgcolor="#FFFFFF"; }
                                ?>
                                    <tr bgcolor="<?=$bgcolor;?>" onclick="change_color('tr_<?=$i;?>','<?=$bgcolor;?>')" id="tr_<?=$i;?>">
                                        <td><?=$company_arr[$row['COMPANY_ID']];?></td>
                                        <td align="center"><?=change_date_format($row['BILL_DATE']);?></td>
                                        <td align="center"><?=$row['BILL_NO'];?></td>
                                        <td align="center"><?=change_date_format($row[csf('payable_date')]);?></td>
                                        <td align="center"><?=$shipment_mode[$row['SHIP_MODE']];?></td>
                                        <td align="center"><?=($cbo_type_name==1)?$row['DEPO']:$row['CBM_AMT'];?></td>
                                        <td align="center"><?=$row['PORT_NAME'];?></td>
                                        <td align="center"><?=$row['CHALLAN_NO'];?></td>
                                        <td align="center"><?
                                        if($cbo_type_name==1){
                                            echo $add_info_arr[$row["CHALLAN_BTB_ID"]]["invoice_no"];
                                        }else
                                        {
                                            echo $add_info_arr[$row["CHALLAN_BTB_ID"]]["lc_number"];
                                        }?></td>
                                        <td align="right"><?=($cbo_type_name==1)?$add_info_arr[$row["CHALLAN_BTB_ID"]]["ex_fcty_qty"]:$row['QTY']?></td>
                                        <? if($cbo_type_name==1)
                                            {
                                                ?>
                                                    <td align="right"><?=$add_info_arr[$row["CHALLAN_BTB_ID"]]["tol_crtn_qty"];?></td>
                                                    <td align="right"><?=$row['CBM_AMT'];?></td>
                                                <?
                                            } 
                                        ?> 
                                        <td>&nbsp;<?=$row['VECHICALE_NO'];?></td>
                                        <td align="right"><?=$row['NO_VECHICALE'];?></td>
                                        <td align="right"><?=number_format($row['AMOUNT'],2);?></td>
                                        <td align="right"><?=number_format($row['DEDUCTION'],2);?></td>
                                        <td align="right"><?=number_format($row['AMOUNT']-$row['DEDUCTION'],2);?></td>
                                        <td ><?=$row['REMARKS'];?></td>
                                    </tr>
                                <?
                                $i++;
                                $total_amount+=$row['AMOUNT'];
                                $total_deduction+=$row['DEDUCTION'];
                            }
                        ?>
                    </tbody>
                    <tfoot>
                        <th colspan="14"><strong>Total</strong></th>
                        <th><strong><?echo number_format($total_amount,2);?></strong></th>
                        <th><strong><?echo number_format($total_deduction,2);?></strong></th>
                        <th><strong><?echo number_format($total_amount-$total_deduction,2);?></strong></th>
                        <th></th>
                    </tfoot>
                </table>
            </div>
        <?
    }
    foreach (glob("*.xls") as $filename) {
    @unlink($filename);
    }
    $name=time();
    $filename=$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc,ob_get_contents());
    echo "$html****$filename";
    exit();	
}

if($action=="transport_bill_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);

    $sql_result=sql_select("SELECT vechicale_rent as VECHICALE_RENT, point_unloading as POINT_UNLOADING, load_extra_unload as LOAD_EXTRA_UNLOAD,local_vechicle_rent as LOCAL_VECHICLE_RENT, demurrage_other as DEMURRAGE_OTHER, amount as AMOUNT  from transport_bill_dtls where status_active=1 and is_deleted=0 and id=$id");
    
    if($type==1)
    {
        ?>
            <div id="report_container" align="center" style="width:750px">
                <fieldset style="width:750px; margin-left:10px">
                    <table class="rpt_table" border="1" rules="all" width="750" cellpadding="0" cellspacing="0">
                        <thead>
                            <th width="100">System Id</th>
                            <th width="80" >Vehicle Rent (Tk)</th>
                            <th width="140">Two Point Unloading Charge</th>
                            <th width="100">Loading Unloading Charge</th>
                            <th width="100">Local Vechicle Rent</th>
                            <th width="100">Demurrage Charge</th>
                            <th>Total Amount</th>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?echo $sys_number;?></td>
                                <td align="center"><?echo $sql_result[0]['VECHICALE_RENT'];?></td>
                                <td align="right"><?echo $sql_result[0]['POINT_UNLOADING'];?></td>
                                <td align="right"><?echo $sql_result[0]['LOAD_EXTRA_UNLOAD'];?></td>
                                <td align="right"><?echo $sql_result[0]['LOCAL_VECHICLE_RENT'];?></td>
                                <td align="right"><?echo $sql_result[0]['DEMURRAGE_OTHER'];?></td>
                                <td align="right"><?echo $sql_result[0]['AMOUNT'];?></td>
                            </tr>
                        </tbody>   
                    </table>
                </fieldset>
            </div>
        <?
    }
    if($type==2)
    {
        ?>
            <div id="report_container" align="center" style="width:750px">
                <fieldset style="width:750px; margin-left:10px">
                    <table class="rpt_table" border="1" rules="all" width="750" cellpadding="0" cellspacing="0">
                        <thead>
                            <th width="100">System Id</th>
                            <th width="80" >Vehicle Rent (Tk)</th>
                            <th width="140">Extra Loading Bill TK</th>
                            <th width="100">CTG Port to Godown Local TK</th>
                            <th width="100">Others Amount TK</th>
                            <th>Total Amount</th>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?echo $sys_number;?></td>
                                <td align="center"><?echo $sql_result[0]['VECHICALE_RENT'];?></td>
                                <td align="right"><?echo $sql_result[0]['LOAD_EXTRA_UNLOAD'];?></td>
                                <td align="right"><?echo $sql_result[0]['LOCAL_VECHICLE_RENT'];?></td>
                                <td align="right"><?echo $sql_result[0]['DEMURRAGE_OTHER'];?></td>
                                <td align="right"><?echo $sql_result[0]['AMOUNT'];?></td>
                            </tr>
                        </tbody>   
                    </table>
                </fieldset>
            </div>
        <?
    }
	?>
    
	<?
    exit();
}