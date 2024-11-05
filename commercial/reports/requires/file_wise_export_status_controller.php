<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
require_once('../../../includes/class4/class.conditions.php');
require_once('../../../includes/class4/class.reports.php');
require_once('../../../includes/class4/class.yarns.php');
require_once('../../../includes/class4/class.trims.php');
require_once('../../../includes/class4/class.emblishments.php');
require_once('../../../includes/class4/class.washes.php');
require_once('../../../includes/class4/class.fabrics.php');
require_once('../../../includes/class4/class.conversions.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];
$permission=$_SESSION['page_permission'];


if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 170, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "",0 );
	exit();
}

if ($action=="load_drop_down_lc_year")
{
	$sql="select distinct(lc_year) as lc_sc_year from com_export_lc where beneficiary_name=$data and status_active=1 and is_deleted=0  
	union select distinct(sc_year) as lc_sc_year from com_sales_contract where beneficiary_name=$data and status_active=1 and is_deleted=0";
	echo create_drop_down( "hide_year", 100,$sql,"lc_sc_year,lc_sc_year", 1, "-- Select --", 0,"");
	exit();
}

if($action=="check_report_button")
{
	$sql="select format_id from lib_report_template where template_name='".trim($data)."' and report_id=208 and is_deleted=0 and status_active=1";
	$data_array=sql_select($sql);
	if(count($data_array)>0)
	{
		echo $data_array[0][csf('format_id')];
	}
	else
	{
		echo "";
	}
	exit();
}

$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
$lein_bank_arr=return_library_array( "select bank_name,id from lib_bank where is_deleted=0  and status_active=1 and lien_bank=1 order by bank_name",'id','bank_name');
$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer where is_deleted=0  and status_active=1 order by buyer_name",'id','buyer_name');
$suplier_name_arr=return_library_array( "select id,supplier_name from  lib_supplier where is_deleted=0  and status_active=1 order by supplier_name",'id','supplier_name');
$pi_no_arr=return_library_array( "select id,pi_number from  com_pi_master_details where status_active=1",'id','pi_number');

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_based_on=str_replace("'","",$cbo_based_on);
	$cbo_company_name=str_replace("'","",$cbo_company_name); $cbo_buyer_name=str_replace("'","",$cbo_buyer_name); 
    $cbo_lein_bank=str_replace("'","",$cbo_lein_bank); $txt_file_no=str_replace("'","",$txt_file_no);$hide_year=str_replace("'","",$hide_year);
	//echo $hide_year;die;
	//echo $cbo_company_name.'____'.$cbo_buyer_name.'____'.$cbo_lein_bank.'____'.$txt_file_no; die;
	if($cbo_company_name!=0) $cbo_company_name = $cbo_company_name; else $cbo_company_name="%%";
	if($cbo_buyer_name == 0) $cbo_buyer_name="%%"; else $cbo_buyer_name = $cbo_buyer_name;
	if($cbo_lein_bank == 0) $cbo_lein_bank="%%"; else $cbo_lein_bank = $cbo_lein_bank;
	if(trim($txt_file_no)!="") $txt_file_no =$txt_file_no; else $txt_file_no="%%";
	if(trim($hide_year)!="") $hide_year =$hide_year; else $hide_year="%%";	

    ob_start();
    if($rpt_type==1)
    {
        ?>
        <div style="width:1900px;" id="scroll_body">
            <fieldset style="width:100%">
                <table width="1200" cellpadding="0" cellspacing="0" id="caption" align="left">
                    <tr>
                        <td align="center" width="100%" colspan="9" class="form_caption" ><strong style="font-size:18px">Company Name:<? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
                    </tr>
                    <tr>
                        <td align="center" width="100%" colspan="9" class="form_caption" ><strong style="font-size:18px"><? echo $report_title; ?></strong></td>
                    </tr>
                    <tr>
                        <td width="100%" style="font-size:16px; font-weight:bold">File No:&nbsp;<? echo $txt_file_no; ?></td>
                    </tr>
                </table>
                <table width="1500" align="left">
                    <tr>
                        <td width="500">
                            <table width="450">
                                <tr>
                                    <td width="250"><b>Sales Contact (Finance/Lc-Sc):</b></td>
                                    <td align="center" width="100"><b>Value</b></td>
                                    <td>&nbsp;</td>
                                </tr>
                                <?
                                $sales_ref_finance= sql_select("select id, buyer_name, contract_no, contract_value from com_sales_contract where beneficiary_name like '$cbo_company_name' and  buyer_name like '$cbo_buyer_name' and  internal_file_no like '$txt_file_no' and lien_bank like '$cbo_lein_bank' and sc_year like '$hide_year' and  ( converted_from is null or converted_from=0)  and  convertible_to_lc!=2 and is_deleted='0' and status_active='1' order by id");
                                $sc_id_arr=array();$lc_id_arr=array();
                                foreach($sales_ref_finance as $sal_ref) // Master Job  table queery ends here
                                {
                                    $sc_id_arr[]=$sal_ref[csf('id')];
                                    ?>
                                    <tr>
                                        <td>
                                        <?
                                        $sales_contct_ref= $sal_ref[csf('contract_no')];
                                        echo $sales_contct_ref;
                                        ?>
                                        &nbsp;Buyer: <?php echo $buyer_name_arr[$sal_ref[csf('buyer_name')]]; $buyer_ref_id=$sal_ref[csf('buyer_name')];?>
                                        </td>
                                        <td align="right">
                                        <?
                                        $sales_contct_value_finance= $sal_ref[csf('contract_value')];
                                        echo number_format($sales_contct_value_finance,2);

                                        ?>
                                        </td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <?
                                    $total_sales_contct_value_finance += $sales_contct_value_finance;
                                }
                                ?>
                                <tr align="right">
                                    <td><b>Total</b></td>
                                    <td style="border-top-style: solid;border-top-width: 1px;"><b><?php  echo number_format($total_sales_contct_value_finance,2);?></b></td>
                                    <td>&nbsp;</td>
                                </tr>
                            </table>

                            <table width="450">
                                <tr>
                                    <td width="250"><b>Replacement(Lc/Sc)</b></td>
                                    <td align="center" width="100"><b>Value</b></td>
                                    <td width="100"></td>
                                </tr>
                                    <? 
                                    $sales_ref3= sql_select("select id, buyer_name, contract_no as lc_sc_no, contract_value as lc_sc_val, 1 as type
                                    from com_sales_contract
                                    where beneficiary_name like '$cbo_company_name' and  buyer_name like '$cbo_buyer_name' and  internal_file_no like '$txt_file_no' and lien_bank like '$cbo_lein_bank' and sc_year like '$hide_year' and converted_from!=0 and is_deleted='0' and status_active='1'
                                    union all
                                    select id, buyer_name, export_lc_no as lc_sc_no, lc_value as lc_sc_val, 2 as type
                                    from com_export_lc
                                    where  beneficiary_name like '$cbo_company_name' and  buyer_name like '$cbo_buyer_name' and  internal_file_no like '$txt_file_no' and lien_bank like '$cbo_lein_bank' and lc_year like '$hide_year' and is_deleted='0' and status_active='1' and replacement_lc='1'
                                    order by id");                                                              

                                    $sales_contract_id="";$sales_contract_number="";
                                    foreach($sales_ref3 as $sales_ref)  // Master Job  table queery ends here
                                    {
                                        if($sales_ref[csf('type')]==1)
                                        {
                                            $sc_id_arr[]=$sales_ref[csf('id')];          
                                        }
                                        else
                                        {
                                            $lc_id_arr[]=$sales_ref[csf('id')];                                 
                                        } 
                                        //print_r($sc_id_attachQty_arr);                                    

                                        ?>
                                        <tr>
                                            <td>
                                            <?
                                            $sales_contct_ref= $sales_ref[csf('lc_sc_no')];
                                            echo $sales_contct_ref;
                                            ?>
                                            &nbsp;Buyer: <?php echo $buyer_name_arr[$sales_ref[csf('buyer_name')]]?>
                                            </td>
                                            <td align="right">
                                            <?
                                            $replace_lc_sc_val= $sales_ref[csf('lc_sc_val')];
                                            echo number_format($replace_lc_sc_val,2);
                                            ?>
                                            </td>
                                            <td>&nbsp;</td>
                                        </tr>
                                        <?
                                        $total_sales_contct_value_top += $replace_lc_sc_val;
                                    }
                                    ?>
                                <tr align="right">
                                    <td>&nbsp;</td>
                                    <td style="border-top-style: solid;border-top-width: 1px;"><b><? echo number_format($total_sales_contct_value_top,2)?></b></td>
                                    <td></td>
                                </tr>
                            </table>

                            <table width="450">
                                <tr>
                                    <td width="250"><b>Balance:</b></td>
                                    <td align="center" width="100"><b>Amount</b></td>
                                    <td width="100"></td>
                                </tr>
                                <tr>
                                    <td>
                                    </td>
                                    <td align="right" style="border-top-style: solid;border-top-width: 1px;"><b>
                                    <?
                                    //balance show here
                                    $balance=$total_sales_contct_value_finance-$total_sales_contct_value_top;
                                    if($balance<0) $balance=0;
                                    echo number_format($balance,2);
                                    ?></b>
                                    </td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr align="right">
                                    <td>&nbsp;</td>
                                    <td ><? //echo number_format($total_sales_contct_value_top,2)?></td>
                                    <td></td>
                                </tr>
                            </table>

                            <table width="450">
                                <tr>
                                    <td width="250"><b>Salse Contact(Direct)</b></td>
                                    <td align="center" width="100"><b>Value</b></td>
                                    <td width="100"></td>
                                </tr>

                                <?
                                $sales_direct= sql_select("select id, buyer_name, contract_no, contract_value ,pay_term
                                from com_sales_contract
                                where beneficiary_name like '$cbo_company_name' and  buyer_name like '$cbo_buyer_name' and  internal_file_no like '$txt_file_no' and lien_bank like '$cbo_lein_bank' and sc_year like '$hide_year' and convertible_to_lc=2 and is_deleted='0'  and  ( converted_from is null or converted_from=0)  and status_active='1' order by id");

                                foreach($sales_direct as $exp_ref)  // Master Job  table queery ends here
                                {

                                    $sc_id_arr[]=$exp_ref[csf('id')];
                                    //if($exp_ref[csf('pay_term')] == 3)
                                    //{
                                        $cashInAdv_sc_id_arr[] = $exp_ref[csf('id')];
                                    //}
                                    ?>
                                    <tr>
                                        <td><?
                                        $export_lc= $exp_ref[csf('contract_no')];
                                        echo $export_lc;
                                        ?>
                                        &nbsp;Buyer: <? echo  $buyer_name_arr[$exp_ref[csf('buyer_name')]]; ?>
                                        </td>
                                        <td align="right">
                                        <?
                                            $sc_direct_val= $exp_ref[ csf('contract_value')];
                                            echo number_format($sc_direct_val,2);
                                        ?>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <?
                                    $total_direct_sc_val+= $sc_direct_val;
                                    //$total_max_btb_sale_limit_value += $max_btb_sale_limit_value;
                                }
                                //var_dump( $sc_id_arr);die;
                                ?>
                                <tr align="right">
                                    <td></td>
                                    <td style="border-top-style: solid;border-top-width: 1px;"><b><? echo number_format($total_direct_sc_val,2)?></b></td>
                                </tr>
                            </table>

                            <table width="450">
                                <tr>
                                    <td width="250"><b>Lc(Direct)</b></td>
                                    <td align="center" width="100"><b>Value</b></td>
                                    <td width="100"></td>
                                </tr>
                                <?
                                $exp_ref3= sql_select("select id, buyer_name, export_lc_no as lc_sc_no, lc_value as lc_sc_val, 2 as type
                                from com_export_lc
                                where  beneficiary_name like '$cbo_company_name' and  buyer_name like '$cbo_buyer_name' and  internal_file_no like '$txt_file_no' and lien_bank like '$cbo_lein_bank' and lc_year like '$hide_year'  and is_deleted='0' and status_active='1' and replacement_lc=2");
                                //echo 	"select id, buyer_name, export_lc_no as lc_sc_no, lc_value as lc_sc_val, 2 as type from	com_export_lc where	beneficiary_name like '$cbo_company_name' and  buyer_name like '$cbo_buyer_name' and  internal_file_no like '$txt_file_no' and lien_bank like '$cbo_lein_bank' and lc_year like '$hide_year'  and is_deleted='0' and status_active='1' and replacement_lc=2";die;

                                foreach($exp_ref3 as $exp_ref)  // Master Job  table queery ends here
                                {
                                    $lc_id_arr[]=$exp_ref[csf('id')];
                                    ?>
                                    <tr>
                                        <td><?
                                        $export_lc= $exp_ref[csf('lc_sc_no')];
                                        echo $export_lc;
                                        ?>
                                        &nbsp;Buyer: <? echo  $buyer_name_arr[$exp_ref[csf('buyer_name')]]; ?>
                                        </td>

                                        <td align="right"><?

                                        $direct_lc_val= $exp_ref[ csf('lc_sc_val')];
                                        echo number_format($direct_lc_val,2);
                                        ?>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <?
                                    $total_direct_lc_val+= $direct_lc_val;
                                }
                                ?>
                                <tr align="right">
                                    <td></td>
                                    <td style="border-top-style: solid;border-top-width: 1px;"><b><? echo number_format($total_direct_lc_val,2)?></b></td>
                                </tr>
                            </table>
                        </td>

                        <?
                        //$file_value=$total_sales_contct_value_top+$balance+$total_direct_sc_val+$total_direct_lc_val;
                        $i = 1;$realize_lc_arr=array();$submit_id_lc=0;//$relize_arr=array();$distributed_arr=array();$submit_inv_arr=array();
                        $lc_id_arr=array_unique($lc_id_arr);$sc_id_arr=array_unique($sc_id_arr);
                        
                        $sub_inv_id_lc=0;$sub_inv_id_sc=0;$payment_realized=0;$payment_realized_deduction=0;
                        //var_dump($lc_id_arr);
                        if(!empty($lc_id_arr))
                        {
                            /*if($db_type==0)
                            {
                                $submision_id_lc_rlz=return_field_value("group_concat(distinct doc_submission_mst_id) as id","com_export_doc_submission_invo","is_lc=1 and lc_sc_id in(".implode(',',$lc_id_arr).")","id");

                            }
                            else if($db_type==2)
                            {
                                $submision_id_lc_rlz=return_field_value("LISTAGG(CAST(doc_submission_mst_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY doc_submission_mst_id) as id","com_export_doc_submission_invo","is_lc=1 and lc_sc_id in(".implode(',',$lc_id_arr).")","id");
                                $submision_id_lc_rlz=implode(",",array_unique(explode(",",$submision_id_lc_rlz)));
                            }*/


                            $sub_rlz_sql=sql_select("select doc_submission_mst_id as sub_id from com_export_doc_submission_invo where is_lc=1 and lc_sc_id in(".implode(',',$lc_id_arr).")");
                            $submision_id_lc_rlz="";
                            foreach($sub_rlz_sql as $row)
                            {
                                if($submision_id_lc_rlz=="") $submision_id_lc_rlz=$row[csf("sub_id")]; 
                                else $submision_id_lc_rlz .=",".$row[csf("sub_id")];
                            }

                            //$invoice_id_lc_arr=array_chunk(array_unique(explode(",",$invoice_id_lc)),999);
                            if($submision_id_lc_rlz!="")
                            {
                                $submision_id_lc_rlz=array_chunk(array_unique(explode(",",$submision_id_lc_rlz)),999);
                                if($db_type==0)
                                {
                                    $sql_sub_lc_rlz="select a.id as relz_id, a.invoice_bill_id as sub_id, a.received_date, b.type, b.account_head, b.document_currency, c.lc_sc_id from com_export_proceed_realization a, com_export_proceed_rlzn_dtls b, com_export_doc_submission_invo c where a.id=b.mst_id and c.doc_submission_mst_id=a.invoice_bill_id";
                                    $p=1;
                                    foreach($submision_id_lc_rlz as $sub_id_lc_rlz)
                                    {
                                        if($p==1) $sql_sub_lc_rlz .=" and (a.invoice_bill_id in(".implode(',',$sub_id_lc_rlz).")"; else  $sql_sub_lc_rlz .=" or a.invoice_bill_id  in(".implode(',',$sub_id_lc_rlz).")";
                                        $p++;
                                    }

                                    $sql_sub_lc_rlz .=") and a.is_invoice_bill=1 and a.status_active=1 and a.is_deleted=0 group by b.id order by c.lc_sc_id,a.id";

                                    // AND a.invoice_bill_id in($submision_id_lc_rlz) and a.is_invoice_bill=1 and a.status_active=1 and a.is_deleted=0 group by b.id order by c.lc_sc_id,a.id";
                                }
                                else if($db_type==2)
                                {
                                    $sql_sub_lc_rlz="select a.id as relz_id, a.invoice_bill_id as sub_id, a.received_date, b.type, b.account_head, b.document_currency, c.lc_sc_id
                                    from com_export_proceed_realization a, com_export_proceed_rlzn_dtls b, com_export_doc_submission_invo c
                                    where a.id=b.mst_id and c.doc_submission_mst_id=a.invoice_bill_id";
                                    $p=1;
                                    foreach($submision_id_lc_rlz as $sub_id_lc_rlz)
                                    {
                                        if($p==1) $sql_sub_lc_rlz .=" and (a.invoice_bill_id in(".implode(',',$sub_id_lc_rlz).")"; else  $sql_sub_lc_rlz .=" or a.invoice_bill_id  in(".implode(',',$sub_id_lc_rlz).")";
                                        $p++;
                                    }

                                    $sql_sub_lc_rlz .=") and a.is_invoice_bill=1 and a.status_active=1 and a.is_deleted=0
                                    group by b.id , b.type, b.account_head, b.document_currency,a.id, a.invoice_bill_id, a.received_date, c.lc_sc_id
                                    order by c.lc_sc_id,a.id";
                                }

                                //echo $sql_sub_lc_rlz."<br>jahid**********<br>";
                                $sql_result_sub_lc_rlz=sql_select($sql_sub_lc_rlz);

                                foreach($sql_result_sub_lc_rlz as $row)
                                {
                                    if($submit_id_lc==0) $submit_id_lc=$row[csf("sub_id")]; 
                                    else $submit_id_lc= $submit_id_lc.",".$row[csf("sub_id")];
                                    $realize_lc_arr[$row[csf('relz_id')]]['sub_id']=$row[csf('sub_id')];
                                    $realize_lc_arr[$row[csf('relz_id')]]['rlz_received_date']=$row[csf('received_date')];

                                    if($row[csf('type')]==0)
                                    {
                                        $realize_lc_arr[$row[csf('relz_id')]]['deduct_realize']+=$row[csf('document_currency')];
                                        $payment_realized_deduction+=$row[csf('document_currency')];
                                    }
                                    else
                                    {
                                        $realize_lc_arr[$row[csf('relz_id')]]['distribute_realize']+=$row[csf('document_currency')];
                                        $payment_realized+=$row[csf('document_currency')];
                                    }

                                    if(!in_array($row[csf('relz_id')],$temp_arr))
                                    {
                                        //$payment_realized+=$row[csf('distribute_realize')];
                                        //$payment_realized_deduction+=$row[csf('deduct_realize')];
                                        $temp_arr[]=$row[csf('relz_id')];
                                    }

                                    if($row[csf("account_head")]==6)
                                    {
                                        $realize_lc_arr[$row[csf("relz_id")]]["erq"]+=$row[csf("document_currency")];
                                    }
                                    else if($row[csf("account_head")]==5)
                                    {
                                        $realize_lc_arr[$row[csf("relz_id")]]["btb"]+=$row[csf("document_currency")];
                                        $total_btb_margine+=$row[csf("document_currency")];
                                    }
                                    else if($row[csf("account_head")]==10)
                                    {
                                        $realize_lc_arr[$row[csf("relz_id")]]["cd"]+=$row[csf("document_currency")];
                                    }
                                    else if($row[csf("account_head")]==15)
                                    {
                                        $realize_lc_arr[$row[csf("relz_id")]]["cc"]+=$row[csf("document_currency")];
                                    }
                                    else if($row[csf("account_head")]==65)
                                    {
                                        $realize_lc_arr[$row[csf("relz_id")]]["fdbc"]+=$row[csf("document_currency")];
                                    }
                                    else if($row[csf("account_head")]==81)
                                    {
                                        $realize_lc_arr[$row[csf("relz_id")]]["sun_ac"]+=$row[csf("document_currency")];
                                    }
                                    else if($row[csf("account_head")]==11)
                                    {
                                        $realize_lc_arr[$row[csf("relz_id")]]["std"]+=$row[csf("document_currency")];
                                    }

                                    /*$payment_realized+=$realize_lc_arr[$row[csf('relz_id')]]['distribute_realize']=$row[csf('distribute_realize')];
                                    $payment_realized_deduction+=$realize_lc_arr[$row[csf('relz_id')]]['deduct_realize']=$row[csf('deduct_realize')];*/
                                }
                            }
                        }
                        //var_dump($realize_lc_arr);die;
                        // LISTAGG(CAST( b.id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.id) as tr_id
                        $submit_id_lc=implode(",",array_unique(explode(",",$submit_id_lc)));

                        if($db_type==0)
                        {
                            $sql_re=sql_select("SELECT d.id as rlz_id, group_concat(distinct b.invoice_id) as inv_id,group_concat(distinct b.doc_submission_mst_id) as sub_id, group_concat(distinct c.invoice_no) as invoice_no, sum(b.net_invo_value) as net_invo_value, group_concat(distinct a.bank_ref_no) as bank_ref_no,group_concat(distinct a.submit_date) as bank_ref_date, sum(case when a.submit_type=1 then b.net_invo_value else 0 end) as sub_collection, group_concat(distinct a.negotiation_date) as negotiation_date, sum(a.total_negotiated_amount) as total_negotiated_amount, group_concat(distinct c.lc_sc_id) as lc_sc_id, c.is_lc,sum(c.invoice_quantity) as invoice_quantity
                            FROM com_export_doc_submission_mst a, com_export_doc_submission_invo b,  com_export_invoice_ship_mst c,com_export_proceed_realization d
                            WHERE b.doc_submission_mst_id=a.id and b.invoice_id=c.id and d.invoice_bill_id=a.id and d.is_invoice_bill='1'  and c.is_lc=1 AND a.id in($submit_id_lc) and  a.status_active=1 and a.is_deleted=0
                            group by d.id , c.is_lc
                            order by  c.lc_sc_id,d.id");
                        }
                        else
                        {
                            $sql_re=sql_select("SELECT d.id as rlz_id, LISTAGG(CAST( b.invoice_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.invoice_id) as inv_id, b.doc_submission_mst_id as sub_id, LISTAGG(CAST( c.invoice_no  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.invoice_no) as invoice_no, sum(b.net_invo_value) as net_invo_value, a.bank_ref_no , a.submit_date  as bank_ref_date, sum(case when a.submit_type=1 then b.net_invo_value else 0 end) as sub_collection, a.negotiation_date , sum(a.total_negotiated_amount) as total_negotiated_amount, b.lc_sc_id as lc_sc_id, c.is_lc,sum(c.invoice_quantity) as invoice_quantity
                            FROM com_export_doc_submission_mst a, com_export_doc_submission_invo b,  com_export_invoice_ship_mst c,com_export_proceed_realization d
                            WHERE b.doc_submission_mst_id=a.id and b.invoice_id=c.id and d.invoice_bill_id=a.id and d.is_invoice_bill='1' and c.is_lc=1 AND a.id in($submit_id_lc) and  a.status_active=1 and a.is_deleted=0
                            group by d.id , b.doc_submission_mst_id,a.bank_ref_no ,a.submit_date,a.negotiation_date,b.lc_sc_id,c.is_lc
                            order by b.lc_sc_id,d.id ");
                        }

                        foreach($sql_re as $result)
                        {
                            if($sub_inv_id_lc==0) $sub_inv_id_lc=$result[csf("inv_id")]; else $sub_inv_id_lc=$sub_inv_id_lc.",".$result[csf("inv_id")];
                            $submit_inv_arr[]=$result[csf("inv_id")];
                            $realize_lc_arr[$result[csf("rlz_id")]]['inv_id']=$result[csf("inv_id")];
                            $realize_lc_arr[$result[csf("rlz_id")]]['sub_id']=$result[csf("sub_id")];
                            $realize_lc_arr[$result[csf("rlz_id")]]['invoice_no']=$result[csf("invoice_no")];
                            $realize_lc_arr[$result[csf("rlz_id")]]['net_invo_value']=$result[csf("net_invo_value")];
                            $realize_lc_arr[$result[csf("rlz_id")]]['bank_ref_no']=$result[csf("bank_ref_no")];
                            $realize_lc_arr[$result[csf("rlz_id")]]['invoice_quantity']=$result[csf("invoice_quantity")];
                            $realize_lc_arr[$result[csf("rlz_id")]]['sub_collection']=$result[csf("sub_collection")];
                            $realize_lc_arr[$result[csf("rlz_id")]]['bank_ref_date']=$result[csf("bank_ref_date")];
                            //$realize_lc_arr[$result[csf("rlz_id")]]['purchase_amount']=$result[csf("purchase_amount")];
                            $realize_lc_arr[$result[csf("rlz_id")]]['negotiation_date']=$result[csf("negotiation_date")];
                            $realize_lc_arr[$result[csf("rlz_id")]]['total_negotiated_amount']=$result[csf("total_negotiated_amount")];
                            $realize_lc_arr[$result[csf("rlz_id")]]['lc_sc_id']=$result[csf("lc_sc_id")];
                        }

                        if($sub_inv_id_lc!=0) $sub_inv_id_lc=implode(",",array_unique(explode(",",$sub_inv_id_lc)));
                        //var_dump($realize_lc_arr);die;
                        //var_dump($relize_arr);
                        $submit_id_lc=implode(",",array_unique(explode(",",$submit_id_lc)));
                        $k = 1;$realize_sc_arr=array();$submit_id_sc=0;
                        //$sc_relize_arr=array();$sc_distributed_arr=array();

                        if(!empty($sc_id_arr))
                        {
                            $submision_id_sc_rlz="";
                            //echo "select doc_submission_mst_id as sub_id from com_export_doc_submission_invo where is_lc=2 and lc_sc_id in(".implode(',',$sc_id_arr).")";die;
                            $sub_rlz_sql=sql_select("select doc_submission_mst_id as sub_id from com_export_doc_submission_invo where is_lc=2 and lc_sc_id in(".implode(',',$sc_id_arr).")");
                            //echo "<pre>";print_r($sub_rlz_sql);die;
                            foreach($sub_rlz_sql as $row)
                            {
                                $sub_lc_ids[$row[csf("sub_id")]]=$row[csf("sub_id")];
                            }
                            $submision_id_sc_rlz=implode(",",$sub_lc_ids);
                            //echo $submision_id_sc_rlz.test;die;
                            if($submision_id_sc_rlz!="" || !empty($cashInAdv_sc_id_arr))
                            {
                                $submision_id_sc_rlz=array_chunk(array_unique(explode(",",$submision_id_sc_rlz)),999);
                                if($db_type==0)
                                {
                                    $sql_rlz_query="select a.id as relz_id, a.invoice_bill_id as sub_id, a.received_date, b.type, b.account_head, b.document_currency, c.lc_sc_id , null as invoice_no, 1 as invoice_type from com_export_proceed_realization a, com_export_proceed_rlzn_dtls b, com_export_doc_submission_invo c where a.id=b.mst_id and c.doc_submission_mst_id=a.invoice_bill_id";
                                    
                                    $p=1;
                                    foreach($submision_id_sc_rlz as $sub_id_sc_rlz)
                                    {
                                        if($p==1) $sql_rlz_query .=" and (a.invoice_bill_id in(".implode(',',$sub_id_sc_rlz).")"; else  $sql_rlz_query .=" or a.invoice_bill_id  in(".implode(',',$sub_id_sc_rlz).")";
                                        $p++;
                                    }
                                    $sql_rlz_query .=") and a.is_invoice_bill=1 and a.status_active=1 and a.is_deleted=0 group by b.id order by c.lc_sc_id,a.id";
                                }
                                else if($db_type==2)
                                {
                                    $sql_rlz_query="select a.id as relz_id, a.invoice_bill_id as sub_id, a.received_date, b.type, b.account_head, b.document_currency, c.lc_sc_id, null as invoice_no, 1 as invoice_type from com_export_proceed_realization a, com_export_proceed_rlzn_dtls b, com_export_doc_submission_invo c where a.id=b.mst_id and c.doc_submission_mst_id=a.invoice_bill_id ";
                                    //echo $sql_rlz_query;die;
                                    $p=1;
                                    foreach($submision_id_sc_rlz as $sub_id_sc_rlz)
                                    {
                                        if($p==1) $sql_rlz_query .=" and (a.invoice_bill_id in(".implode(',',$sub_id_sc_rlz).")"; else  $sql_rlz_query .=" or a.invoice_bill_id  in(".implode(',',$sub_id_sc_rlz).")";
                                        $p++;
                                    }
                                    $sql_rlz_query .=") and a.is_invoice_bill=1 and a.status_active=1 and a.is_deleted=0 and b.status_active = 1 and b.is_deleted=0
                                    group by b.id , b.type, b.account_head, b.document_currency,a.id, a.invoice_bill_id, a.received_date, c.lc_sc_id";
                                }

                                //print_r($submision_id_sc_rlz);die;
                                if($sql_rlz_query) $sql_rlz_query .= " union all ";
                                //echo $sql_rlz_query;die;
                                //implode(",",array_filter(array_unique($cashInAdv_sc_id_arr)));


                                $cashInAdv_sc_id = implode("','",array_filter(array_unique($cashInAdv_sc_id_arr)));
                                $cashScCond=""; $cashInAdv_sc_id_arr=explode(",",$cashInAdv_sc_id);
                                if($cashInAdv_sc_id =="") {$cashInAdv_sc_id=0;}
                                $cashINAdvScIdCond = "";
                                if($db_type==2 && count($cashInAdv_sc_id_arr)>999)
                                {
                                    $job_no_chunk_arr=array_chunk($cashInAdv_sc_id_arr,999) ;
                                    foreach($job_no_chunk_arr as $chunk_arr)
                                    {
                                        $chunk_arr_value=implode(",",$chunk_arr);
                                        $cashScCond.=" c.lc_sc_id in($chunk_arr_value) or ";
                                    }

                                    $cashINAdvScIdCond.=" and (".chop($cashScCond,'or ').")";
                                }
                                else
                                {
                                    $cashINAdvScIdCond=" and c.lc_sc_id in($cashInAdv_sc_id)";
                                }

                                $sql_rlz_query .= " select a.id as relz_id, null as sub_id, a.received_date, b.type, b.account_head, b.document_currency, a.invoice_bill_id as sub_id, c.invoice_no, 2 as invoice_type 
                                from com_export_proceed_realization a, com_export_proceed_rlzn_dtls b , com_export_invoice_ship_mst c 
                                where a.id=b.mst_id and A.INVOICE_BILL_ID = c.id  and a.is_invoice_bill=2 and a.status_active=1 and a.is_deleted=0 and b.status_active = 1 and b.is_deleted=0 $cashINAdvScIdCond  and c.is_lc= 2 
                                group by b.id , b.type, b.account_head, b.document_currency, a.id, a.invoice_bill_id, a.received_date,c.invoice_no order by sub_id, relz_id";


                                //echo $sql_rlz_query."<br>nahid**************<br>";die;
                                $sql_rlz=sql_select($sql_rlz_query);
                                //print_r($sql_rlz);die;
                                foreach($sql_rlz as $row)
                                {
                                    if($submit_id_sc==0) $submit_id_sc=$row[csf("sub_id")]; else $submit_id_sc= $submit_id_sc.",".$row[csf("sub_id")];
                                    $realize_sc_arr[$row[csf('relz_id')]]['sub_id']=$row[csf('sub_id')];
                                    $realize_sc_arr[$row[csf('relz_id')]]['rlz_received_date']=$row[csf('received_date')];
                                    if($row[csf('invoice_type')] == 2){
                                        $realize_sc_arr[$row[csf("relz_id")]]['invoice_no']=$row[csf('invoice_no')];
                                    }


                                    if($row[csf('type')]==0)
                                    {
                                        $realize_sc_arr[$row[csf('relz_id')]]['deduct_realize']+=$row[csf('document_currency')];
                                        $payment_realized_deduction+=$row[csf('document_currency')];
                                    }
                                    else
                                    {
                                        $realize_sc_arr[$row[csf('relz_id')]]['distribute_realize']+=$row[csf('document_currency')];
                                        $payment_realized+=$row[csf('document_currency')];
                                    }

                                    if(!in_array($row[csf('relz_id')],$temp_arr))
                                    {
                                        //$payment_realized+=$row[csf('distribute_realize')];
                                        //$payment_realized_deduction+=$row[csf('deduct_realize')];
                                        $temp_arr[]=$row[csf('relz_id')];
                                    }

                                    if($row[csf("account_head")]==6)
                                    {
                                        $realize_sc_arr[$row[csf("relz_id")]]["erq"]+=$row[csf("document_currency")];
                                    }
                                    else if($row[csf("account_head")]==5)
                                    {
                                        $realize_sc_arr[$row[csf("relz_id")]]["btb"]+=$row[csf("document_currency")];
                                        $total_btb_margine+=$row[csf("document_currency")];
                                    }
                                    else if($row[csf("account_head")]==10)
                                    {
                                        $realize_sc_arr[$row[csf("relz_id")]]["cd"]+=$row[csf("document_currency")];
                                    }
                                    else if($row[csf("account_head")]==15)
                                    {
                                        $realize_sc_arr[$row[csf("relz_id")]]["cc"]+=$row[csf("document_currency")];
                                    }
                                    else if($row[csf("account_head")]==65)
                                    {
                                        $realize_sc_arr[$row[csf("relz_id")]]["fdbc"]+=$row[csf("document_currency")];
                                    }
                                    else if($row[csf("account_head")]==81)
                                    {
                                        $realize_sc_arr[$row[csf("relz_id")]]["sun_ac"]+=$row[csf("document_currency")];
                                    }
                                    else if($row[csf("account_head")]==11)
                                    {
                                        $realize_sc_arr[$row[csf("relz_id")]]["std"]+=$row[csf("document_currency")];
                                    }
                                }
                            }
                        }
                        //echo "<pre>";print_r($realize_sc_arr);die;
                        $submit_id_sc=implode(",",array_unique(explode(",",$submit_id_sc)));
                        //echo $submit_id_sc.tes;die;
                        if($submit_id_sc == "") $submit_id_sc = 0;

                        if($db_type==0)
                        {
                            $sql_sc=sql_select("SELECT d.id as rlz_id, group_concat(distinct b.invoice_id) as inv_id,group_concat(distinct a.id) as sub_id,  group_concat(distinct c.invoice_no  ) as invoice_no, sum(b.net_invo_value) as net_invo_value, group_concat(distinct a.bank_ref_no) as bank_ref_no, group_concat(distinct a.submit_date) as bank_ref_date, sum(case when a.submit_type=1 then b.net_invo_value else 0 end) as sub_collection, group_concat(distinct a.negotiation_date) as negotiation_date, sum(a.total_negotiated_amount) as total_negotiated_amount, group_concat(distinct c.lc_sc_id) as lc_sc_id, c.is_lc,sum(c.invoice_quantity) as invoice_quantity
                            FROM com_export_doc_submission_mst a, com_export_doc_submission_invo b,  com_export_invoice_ship_mst c,com_export_proceed_realization d
                            WHERE b.doc_submission_mst_id=a.id and b.invoice_id=c.id and d.invoice_bill_id=a.id and d.is_invoice_bill='1'  and c.is_lc=2 AND a.id in($submit_id_sc) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0
                            group by d.id, c.is_lc
                            order by b.lc_sc_id,d.id");
                        }
                        else if($db_type==2)
                        {
                            /*$sql_sc=sql_select("SELECT d.id as rlz_id, LISTAGG(CAST( b.invoice_id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.invoice_id) as inv_id,a.id as sub_id,  LISTAGG(CAST( c.invoice_no  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.invoice_no) as invoice_no, sum(b.net_invo_value) as net_invo_value,a.bank_ref_no,a.submit_date as bank_ref_date, sum(case when a.submit_type=1 then b.net_invo_value else 0 end) as sub_collection,a.negotiation_date, sum(a.total_negotiated_amount) as total_negotiated_amount,b.lc_sc_id as lc_sc_id, c.is_lc,sum(c.invoice_quantity) as invoice_quantity
                            FROM com_export_doc_submission_mst a, com_export_doc_submission_invo b,  com_export_invoice_ship_mst c, com_export_proceed_realization d
                            WHERE b.doc_submission_mst_id=a.id and b.invoice_id=c.id and d.invoice_bill_id=a.id and d.is_invoice_bill='1'  and c.is_lc=2 AND a.id in($submit_id_sc) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0
                            group by d.id,a.id,a.bank_ref_no,a.submit_date,a.negotiation_date,b.lc_sc_id,c.is_lc
                            order by b.lc_sc_id,d.id");*/
                            $sql_sc=sql_select("SELECT d.id as rlz_id, rtrim(xmlagg(xmlelement(e,b.invoice_id,',').extract('//text()') order by b.invoice_id).GetClobVal(),',') as inv_id, a.id as sub_id, rtrim(xmlagg(xmlelement(e,c.invoice_no,',').extract('//text()') order by c.invoice_no).GetClobVal(),',') as invoice_no, sum(b.net_invo_value) as net_invo_value,a.bank_ref_no,a.submit_date as bank_ref_date, sum(case when a.submit_type=1 then b.net_invo_value else 0 end) as sub_collection,a.negotiation_date, sum(a.total_negotiated_amount) as total_negotiated_amount,b.lc_sc_id as lc_sc_id, c.is_lc,sum(c.invoice_quantity) as invoice_quantity
                            FROM com_export_doc_submission_mst a, com_export_doc_submission_invo b,  com_export_invoice_ship_mst c, com_export_proceed_realization d
                            WHERE b.doc_submission_mst_id=a.id and b.invoice_id=c.id and d.invoice_bill_id=a.id and d.is_invoice_bill='1'  and c.is_lc=2 AND a.id in($submit_id_sc) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0
                            group by d.id,a.id,a.bank_ref_no,a.submit_date,a.negotiation_date,b.lc_sc_id,c.is_lc
                            order by b.lc_sc_id,d.id");
                            
                        }
                        
                    
                        foreach($sql_sc as $result)
                        {
                            if($sub_inv_id_sc==0) $sub_inv_id_sc=$result[csf("inv_id")]->load(); else $sub_inv_id_sc=$sub_inv_id_sc.",".$result[csf("inv_id")]->load();
                            $submit_inv_arr[]=$result[csf("inv_id")]->load();
                            $realize_sc_arr[$result[csf("rlz_id")]]['inv_id']=$result[csf("inv_id")]->load();
                            $realize_sc_arr[$result[csf("rlz_id")]]['sub_id']=$result[csf("sub_id")];
                            $realize_sc_arr[$result[csf("rlz_id")]]['invoice_no']=$result[csf("invoice_no")]->load();
                            $realize_sc_arr[$result[csf("rlz_id")]]['invoice_quantity']=$result[csf("invoice_quantity")];
                            $realize_sc_arr[$result[csf("rlz_id")]]['net_invo_value']=$result[csf("net_invo_value")];
                            $realize_sc_arr[$result[csf("rlz_id")]]['bank_ref_no']=$result[csf("bank_ref_no")];
                            $realize_sc_arr[$result[csf("rlz_id")]]['sub_collection']=$result[csf("sub_collection")];
                            $realize_sc_arr[$result[csf("rlz_id")]]['bank_ref_date']=$result[csf("bank_ref_date")];
                            //$realize_sc_arr[$result[csf("rlz_id")]]['purchase_amount']=$result[csf("purchase_amount")];
                            $realize_sc_arr[$result[csf("rlz_id")]]['negotiation_date']=$result[csf("negotiation_date")];
                            $realize_sc_arr[$result[csf("rlz_id")]]['total_negotiated_amount']=$result[csf("total_negotiated_amount")];
                            $realize_sc_arr[$result[csf("rlz_id")]]['lc_sc_id']=$result[csf("lc_sc_id")];
                            $realize_sc_arr[$result[csf("rlz_id")]]['is_lc']=$result[csf("is_lc")];
                        }
                        if($sub_inv_id_sc!=0) $sub_inv_id_sc=implode(",",array_unique(explode(",",$sub_inv_id_sc)));
                        //var_dump($sub_inv_id_sc);die;
                        //echo $sub_inv_id_sc; die;

                        $submit_id_lc=implode(",",array_unique(explode(",",$submit_id_lc)));
                        $submit_id_sc=implode(",",array_unique(explode(",",$submit_id_sc)));
                        $submission_id_lc=0;$submission_id_sc=0;$sub_as_collection=0;
                        $sub_lc_arr=array();
                        //echo "ttt<pre>";print_r($lc_id_arr);die;
                        if(!empty($lc_id_arr))
                        {
                            if($db_type==0)
                            {
                                $sql_re=sql_select("SELECT group_concat(distinct b.invoice_id) as inv_id,a.id as sub_id, group_concat(distinct c.invoice_no ) as invoice_no, sum(b.net_invo_value) as net_invo_value, a.bank_ref_no, a.submit_date, a.bank_ref_date, sum(case when a.submit_type=1 then b.net_invo_value else 0 end) as sub_collection, a.negotiation_date, sum(a.total_negotiated_amount) as total_negotiated_amount, c.lc_sc_id as lc_sc_id, c.is_lc, sum(c.invoice_quantity) as invoice_quantity
                                FROM com_export_doc_submission_mst a, com_export_doc_submission_invo b,  com_export_invoice_ship_mst c
                                WHERE b.doc_submission_mst_id=a.id and b.invoice_id=c.id and b.is_lc='1' and a.id not in($submit_id_lc) AND b.lc_sc_id in(".implode(',',$lc_id_arr).")  AND a.status_active='1' and a.is_deleted=0 and a.entry_form=40
                                group by a.id, a.bank_ref_no, a.submit_date, a.bank_ref_date, a.negotiation_date, c.lc_sc_id, c.is_lc
                                order by c.lc_sc_id,a.id");
                            }
                            else if($db_type==2)
                            {
                                $sql_re=sql_select("SELECT LISTAGG(CAST(  b.invoice_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.invoice_id) as inv_id,a.id as sub_id, LISTAGG(CAST(   c.invoice_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.invoice_no) as invoice_no, sum(b.net_invo_value) as net_invo_value, a.bank_ref_no, a.submit_date, a.bank_ref_date, sum(case when a.submit_type=1 then b.net_invo_value else 0 end) as sub_collection, a.negotiation_date, sum(a.total_negotiated_amount) as total_negotiated_amount, c.lc_sc_id as lc_sc_id, c.is_lc,sum(c.invoice_quantity) as invoice_quantity
                                FROM com_export_doc_submission_mst a, com_export_doc_submission_invo b,  com_export_invoice_ship_mst c
                                WHERE b.doc_submission_mst_id=a.id and b.invoice_id=c.id and b.is_lc='1' and a.id not in($submit_id_lc) AND b.lc_sc_id in(".implode(',',$lc_id_arr).")  AND a.status_active='1' and a.is_deleted=0  and a.entry_form=40
                                group by a.id, a.bank_ref_no, a.submit_date, a.bank_ref_date, a.negotiation_date, c.lc_sc_id, c.is_lc
                                order by c.lc_sc_id, a.id");
                            }
                        }

                        /*echo "SELECT LISTAGG(CAST(  b.invoice_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.invoice_id) as inv_id,a.id as sub_id, LISTAGG(CAST(   c.invoice_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.invoice_no) as invoice_no, sum(b.net_invo_value) as net_invo_value, a.bank_ref_no, a.bank_ref_date, sum(case when a.submit_type=1 then b.net_invo_value else 0 end) as sub_collection, a.negotiation_date, sum(a.total_negotiated_amount) as total_negotiated_amount, LISTAGG(CAST(c.lc_sc_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.lc_sc_id) as lc_sc_id, c.is_lc,sum(c.invoice_quantity) as invoice_quantity
                            FROM
                            com_export_doc_submission_mst a, com_export_doc_submission_invo b,  com_export_invoice_ship_mst c
                            WHERE
                            b.doc_submission_mst_id=a.id and b.invoice_id=c.id and b.is_lc='1' and a.id not in($submit_id_lc) AND b.lc_sc_id in(".implode(',',$lc_id_arr).")  AND a.status_active='1' and a.is_deleted=0
                            group by a.id, a.bank_ref_no, a.bank_ref_date, a.negotiation_date, c.is_lc
                            order by a.id";*/


                        foreach($sql_re as $result)
                        {
                            if($submission_id_lc==0) $submission_id_lc=$result[csf("sub_id")]; else $submission_id_lc=$submission_id_lc.",".$result[csf("sub_id")];
                            if($sub_inv_id_lc==0) $sub_inv_id_lc=$result[csf("inv_id")]; else $sub_inv_id_lc=$sub_inv_id_lc.",".$result[csf("inv_id")];
                            $submit_inv_arr[]=$result[csf("inv_id")];
                            $sub_lc_arr[$result[csf("sub_id")]]['sub_id']=$result[csf("sub_id")];
                            $sub_lc_arr[$result[csf("sub_id")]]['inv_id']=$result[csf("inv_id")];
                            $sub_lc_arr[$result[csf("sub_id")]]['invoice_no']=$result[csf("invoice_no")];
                            $sub_lc_arr[$result[csf("sub_id")]]['invoice_quantity']=$result[csf("invoice_quantity")];
                            $sub_lc_arr[$result[csf("sub_id")]]['net_invo_value']=$result[csf("net_invo_value")];
                            $sub_lc_arr[$result[csf("sub_id")]]['bank_ref_no']=$result[csf("bank_ref_no")];
                            $sub_lc_arr[$result[csf("sub_id")]]['sub_collection']=$result[csf("sub_collection")];
                            $sub_lc_arr[$result[csf("sub_id")]]['submit_date']=$result[csf("submit_date")];
                            $sub_lc_arr[$result[csf("sub_id")]]['bank_ref_date']=$result[csf("bank_ref_date")];
                            $sub_lc_arr[$result[csf("sub_id")]]['purchase_amount']=$result[csf("purchase_amount")];
                            $sub_lc_arr[$result[csf("sub_id")]]['negotiation_date']=$result[csf("negotiation_date")];
                            $sub_lc_arr[$result[csf("sub_id")]]['total_negotiated_amount']=$result[csf("total_negotiated_amount")];
                            $sub_lc_arr[$result[csf("sub_idsub_lc_arr")]]['lc_sc_id']=$result[csf("lc_sc_id")];
                            $sub_lc_arr[$result[csf("sub_id")]]['is_lc']=$result[csf("is_lc")];
                            $sub_as_collection+=($result[csf("net_invo_value")]);
                        }

                        $sub_sc_arr=array();
                        if(!empty($sc_id_arr))
                        {
                            // LISTAGG(CAST( b.id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.id) as tr_id
                            if($db_type==0)
                            {
                                $sql_sc=sql_select("SELECT group_concat(distinct b.invoice_id) as inv_id,a.id as sub_id, group_concat(distinct c.invoice_no) as invoice_no, sum(b.net_invo_value) as net_invo_value, a.bank_ref_no, a.submit_date, a.bank_ref_date, sum(case when a.submit_type=1 then b.net_invo_value else 0 end) as sub_collection, a.negotiation_date, sum(a.total_negotiated_amount) as total_negotiated_amount,  group_concat(distinct c.lc_sc_id) as lc_sc_id, c.is_lc,sum(c.invoice_quantity) as invoice_quantity, group_concat(distinct c.id) as sub_invoice_id
                                FROM com_export_doc_submission_mst a, com_export_doc_submission_invo b,  com_export_invoice_ship_mst c
                                WHERE b.doc_submission_mst_id=a.id and b.invoice_id=c.id  and b.is_lc='2' and a.id not in($submit_id_sc) AND b.lc_sc_id in(".implode(',',$sc_id_arr).") and a.company_id='$cbo_company_name' AND a.status_active='1' and a.is_deleted=0 and a.entry_form=40
                                group by a.id, a.bank_ref_no, a.submit_date, a.bank_ref_date, a.negotiation_date, c.is_lc
                                order by c.lc_sc_id,a.id");
                            }
                            else if($db_type==2)
                            {
                                $sql_sc=sql_select("SELECT LISTAGG(CAST( b.invoice_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.invoice_id) as inv_id,a.id as sub_id, LISTAGG(CAST(c.invoice_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.invoice_no)  as invoice_no, sum(b.net_invo_value) as net_invo_value, a.bank_ref_no, a.submit_date, a.bank_ref_date, sum(case when a.submit_type=1 then b.net_invo_value else 0 end) as sub_collection, a.negotiation_date, sum(a.total_negotiated_amount) as total_negotiated_amount,  LISTAGG(CAST( c.lc_sc_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.lc_sc_id) as lc_sc_id, c.is_lc,sum(c.invoice_quantity) as invoice_quantity, LISTAGG(CAST( c.id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.id) as sub_invoice_id
                                FROM com_export_doc_submission_mst a, com_export_doc_submission_invo b,  com_export_invoice_ship_mst c
                                WHERE b.doc_submission_mst_id=a.id and b.invoice_id=c.id  and b.is_lc='2' and a.id not in($submit_id_sc) AND b.lc_sc_id in(".implode(',',$sc_id_arr).") and a.company_id='$cbo_company_name' AND a.status_active='1' and a.is_deleted=0 and a.entry_form=40
                                group by a.id, a.bank_ref_no, a.submit_date, a.bank_ref_date, a.negotiation_date, c.is_lc
                                order by a.id");
                            }
                        }

                        foreach($sql_sc as $result)
                        {
                            if($submission_id_sc=0) $submission_id_sc=$result[csf("sub_id")]; else $submission_id_sc=$submission_id_sc.",".$result[csf("sub_id")];
                            //if($sc_submit_id==0) $sc_submit_id=$result[csf("sub_id")]; else $sc_submit_id=$sc_submit_id.",".$result[csf("sub_id")];
                            if($sub_inv_id_sc==0) $sub_inv_id_sc=$result[csf("sub_invoice_id")]; else $sub_inv_id_sc=$sub_inv_id_sc.",".$result[csf("sub_invoice_id")];

                            $submit_inv_arr[]=$result[csf("inv_id")];
                            $sub_sc_arr[$result[csf("sub_id")]]['sub_id']=$result[csf("sub_id")];
                            $sub_sc_arr[$result[csf("sub_id")]]['inv_id']=$result[csf("inv_id")];
                            $sub_sc_arr[$result[csf("sub_id")]]['invoice_no']=$result[csf("invoice_no")];
                            $sub_sc_arr[$result[csf("sub_id")]]['invoice_quantity']=$result[csf("invoice_quantity")];
                            $sub_sc_arr[$result[csf("sub_id")]]['net_invo_value']=$result[csf("net_invo_value")];
                            $sub_sc_arr[$result[csf("sub_id")]]['bank_ref_no']=$result[csf("bank_ref_no")];
                            $sub_sc_arr[$result[csf("sub_id")]]['sub_collection']=$result[csf("sub_collection")];
                            $sub_sc_arr[$result[csf("sub_id")]]['submit_date']=$result[csf("submit_date")];
                            $sub_sc_arr[$result[csf("sub_id")]]['bank_ref_date']=$result[csf("bank_ref_date")];
                            $sub_sc_arr[$result[csf("sub_id")]]['purchase_amount']=$result[csf("purchase_amount")];
                            $sub_sc_arr[$result[csf("sub_id")]]['negotiation_date']=$result[csf("negotiation_date")];
                            $sub_sc_arr[$result[csf("sub_id")]]['total_negotiated_amount']=$result[csf("total_negotiated_amount")];
                            $sub_sc_arr[$result[csf("sub_id")]]['lc_sc_id']=$result[csf("lc_sc_id")];
                            $sub_sc_arr[$result[csf("sub_id")]]['is_lc']=$result[csf("is_lc")];

                            $sub_as_collection+=($result[csf("net_invo_value")]);
                        }
                        //var_dump($sub_sc_arr);die;



                        /*$sql=sql_select("select a.invoice_bill_id , a.received_date,sum(case when b.type=1 then b.document_currency else 0 end) as distribute_realize, sum(case when b.type=0 then b.document_currency else 0 end) as deduct_realize from com_export_proceed_realization a, com_export_proceed_rlzn_dtls b where   a.id=b.mst_id AND a.is_invoice_bill=1 and a.invoice_bill_id in($submit_id) group by  a.invoice_bill_id ");

                        foreach($sql as $row)
                        {
                        $relize_arr[$row[csf('invoice_bill_id')]]['sub_id']=$row[csf('invoice_bill_id')];
                        $relize_arr[$row[csf('invoice_bill_id')]]['received_date']=$row[csf('received_date')];
                        $relize_arr[$row[csf('invoice_bill_id')]]['distribute_realize']=$row[csf('distribute_realize')];
                        $relize_arr[$row[csf('invoice_bill_id')]]['deduct_realize']=$row[csf('deduct_realize')];
                        }

                        $document_sub= sql_select("SELECT
                        b.invoice_bill_id as sub_id,
                        sum(CASE WHEN account_head =6 THEN document_currency END) AS 'erq',
                        sum(CASE WHEN account_head =5 THEN document_currency END) AS 'btb',
                        sum(CASE WHEN account_head =10 THEN document_currency END) AS 'cd',
                        sum(CASE WHEN account_head =15 THEN document_currency END) AS 'cc',
                        sum(CASE WHEN account_head =65 THEN document_currency END) AS 'fdbc',
                        sum(CASE WHEN account_head =81 THEN document_currency END) AS 'sun_ac',
                        sum(CASE WHEN account_head =82 THEN document_currency END) AS 'mda'

                        FROM
                        com_export_proceed_rlzn_dtls a, com_export_proceed_realization b
                        WHERE
                        a.mst_id = b.id AND a.type = '1' AND b.is_invoice_bill = 1 AND b.invoice_bill_id  in($submit_id) group by b.invoice_bill_id ");


                        foreach($document_sub as $row)
                        {
                        $distributed_arr[$row[csf('sub_id')]]['sub_id']=$row[csf("sub_id")];
                        $distributed_arr[$row[csf("sub_id")]]["erq"]=$row[csf("erq")];
                        $distributed_arr[$row[csf("sub_id")]]["btb"]=$row[csf("btb")];
                        $distributed_arr[$row[csf("sub_id")]]["cd"]=$row[csf("cd")];
                        $distributed_arr[$row[csf("sub_id")]]["cc"]=$row[csf("cc")];
                        $distributed_arr[$row[csf("sub_id")]]["fdbc"]=$row[csf("fdbc")];
                        $distributed_arr[$row[csf("sub_id")]]["sun_ac"]=$row[csf("sun_ac")];
                        $distributed_arr[$row[csf("sub_id")]]["mda"]=$row[csf("mda")];

                        }*/


                        $sub_buyer_arr=array();
                        /*if(!empty($lc_id_arr))
                        {

                            if($db_type==0)
                            {
                            $sql_buyer_sub_lc=sql_select("SELECT group_concat(distinct b.invoice_id) as inv_id,a.id as sub_id, group_concat(distinct c.invoice_no ) as invoice_no, sum(b.net_invo_value) as net_invo_value, a.bank_ref_no, a.bank_ref_date, sum(case when a.submit_type=1 then b.net_invo_value else 0 end) as sub_collection, a.negotiation_date, sum(a.total_negotiated_amount) as total_negotiated_amount, group_concat(distinct c.lc_sc_id) as lc_sc_id, c.is_lc,sum(c.invoice_quantity) as invoice_quantity
                            FROM
                            com_export_doc_submission_mst a, com_export_doc_submission_invo b,  com_export_invoice_ship_mst c
                            WHERE
                            b.doc_submission_mst_id=a.id and b.invoice_id=c.id and b.is_converted=0 and b.is_lc='1' AND b.lc_sc_id in(".implode(',',$lc_id_arr).")  AND a.status_active='1' and a.is_deleted=0 AND b.status_active='1' and b.is_deleted=0 and a.entry_form=39
                            group by a.id, a.bank_ref_no, a.bank_ref_date, a.negotiation_date, c.is_lc
                            order by c.lc_sc_id,a.id");
                            }
                            else if($db_type==2)
                            {
                            $sql_buyer_sub_lc=sql_select("SELECT LISTAGG(CAST(  b.invoice_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.invoice_id) as inv_id,a.id as sub_id, LISTAGG(CAST(   c.invoice_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.invoice_no) as invoice_no, sum(b.net_invo_value) as net_invo_value, a.bank_ref_no, a.bank_ref_date, sum(case when a.submit_type=1 then b.net_invo_value else 0 end) as sub_collection, a.negotiation_date, sum(a.total_negotiated_amount) as total_negotiated_amount, LISTAGG(CAST(c.lc_sc_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.lc_sc_id) as lc_sc_id, c.is_lc,sum(c.invoice_quantity) as invoice_quantity
                            FROM
                            com_export_doc_submission_mst a, com_export_doc_submission_invo b,  com_export_invoice_ship_mst c
                            WHERE
                            b.doc_submission_mst_id=a.id and b.invoice_id=c.id and b.is_converted=0 and b.is_lc='1' AND b.lc_sc_id in(".implode(',',$lc_id_arr).")  AND a.status_active='1' and a.is_deleted=0  AND b.status_active='1' and b.is_deleted=0  and a.entry_form=39
                            group by a.id, a.bank_ref_no, a.bank_ref_date, a.negotiation_date, c.is_lc
                            order by a.id");
                            }
                        }

                        foreach($sql_buyer_sub_lc as $result)
                        {
                            if($submission_id_lc==0) $submission_id_lc=$result[csf("sub_id")]; else $submission_id_lc=$submission_id_lc.",".$result[csf("sub_id")];
                            if($sub_inv_id_lc==0) $sub_inv_id_lc=$result[csf("inv_id")]; else $sub_inv_id_lc=$sub_inv_id_lc.",".$result[csf("inv_id")];
                            $submit_inv_arr[]=$result[csf("inv_id")];
                            $sub_buyer_arr[$result[csf("sub_id")]]['sub_id']=$result[csf("sub_id")];
                            $sub_buyer_arr[$result[csf("sub_id")]]['inv_id']=$result[csf("inv_id")];
                            $sub_buyer_arr[$result[csf("sub_id")]]['invoice_no']=$result[csf("invoice_no")];
                            $sub_buyer_arr[$result[csf("sub_id")]]['invoice_quantity']=$result[csf("invoice_quantity")];
                            $sub_buyer_arr[$result[csf("sub_id")]]['net_invo_value']=$result[csf("net_invo_value")];
                            $sub_buyer_arr[$result[csf("sub_id")]]['bank_ref_no']=$result[csf("bank_ref_no")];
                            $sub_buyer_arr[$result[csf("sub_id")]]['sub_collection']=$result[csf("sub_collection")];
                            $sub_buyer_arr[$result[csf("sub_id")]]['bank_ref_date']=$result[csf("bank_ref_date")];
                            $sub_buyer_arr[$result[csf("sub_id")]]['purchase_amount']=$result[csf("purchase_amount")];
                            $sub_buyer_arr[$result[csf("sub_id")]]['negotiation_date']=$result[csf("negotiation_date")];
                            $sub_buyer_arr[$result[csf("sub_id")]]['total_negotiated_amount']=$result[csf("total_negotiated_amount")];
                            $sub_buyer_arr[$result[csf("sub_id")]]['lc_sc_id']=$result[csf("lc_sc_id")];
                            $sub_buyer_arr[$result[csf("sub_id")]]['is_lc']=$result[csf("is_lc")];

                            $sub_as_collection+=($result[csf("net_invo_value")]);
                        }*/

                        if(!empty($sc_id_arr))
                        {
                            // LISTAGG(CAST( b.id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.id) as tr_id
                            if($db_type==0)
                            {
                            $buyer_sub_sc="SELECT a.id as sub_id, a.buyer_id, a.submit_date, group_concat(distinct b.invoice_id) as inv_id, sum(b.net_invo_value) as net_invo_value, group_concat(distinct c.invoice_no) as invoice_no, c.lc_sc_id as lc_sc_id, sum(c.invoice_quantity) as invoice_quantity
                                FROM com_export_doc_submission_mst a, com_export_doc_submission_invo b,  com_export_invoice_ship_mst c
                                WHERE b.doc_submission_mst_id=a.id and b.invoice_id=c.id and b.is_converted=0   and b.is_lc='2' AND b.lc_sc_id in(".implode(',',$sc_id_arr).") and a.company_id='$cbo_company_name' AND a.status_active='1' and a.is_deleted=0 AND b.status_active='1' and b.is_deleted=0 and a.entry_form=39 and b.invoice_id not in(select q.invoice_id from com_export_doc_submission_mst p, com_export_doc_submission_invo q where p.id=q.doc_submission_mst_id and p.entry_form=40 and p.status_active=1 and p.is_deleted=0 and q.status_active=1 and q.is_deleted=0)
                                group by a.id, a.buyer_id, a.submit_date, c.lc_sc_id order by a.id";
                            }
                            else if($db_type==2)
                            {
                                $buyer_sub_sc="SELECT a.id as sub_id, a.buyer_id, a.submit_date, LISTAGG(CAST( b.invoice_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.invoice_id) as inv_id, sum(b.net_invo_value) as net_invo_value, LISTAGG(CAST(c.invoice_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.invoice_no)  as invoice_no, c.lc_sc_id as lc_sc_id, sum(c.invoice_quantity) as invoice_quantity
                                FROM com_export_doc_submission_mst a, com_export_doc_submission_invo b,  com_export_invoice_ship_mst c
                                WHERE b.doc_submission_mst_id=a.id and b.invoice_id=c.id and b.is_converted=0 and b.is_lc='2' AND b.lc_sc_id in(".implode(',',$sc_id_arr).") and a.company_id='$cbo_company_name' AND a.status_active='1' and a.is_deleted=0 AND b.status_active='1' and b.is_deleted=0 and a.entry_form=39 and b.invoice_id not in(select q.invoice_id from com_export_doc_submission_mst p, com_export_doc_submission_invo q where p.id=q.doc_submission_mst_id and p.entry_form=40 and p.status_active=1 and p.is_deleted=0 and q.status_active=1 and q.is_deleted=0)
                                group by a.id, a.buyer_id, a.submit_date, c.lc_sc_id order by a.id";
                                
                            }
                            //echo $buyer_sub_sc;
                            $sql_buyer_sub_sc=sql_select($buyer_sub_sc);
                        }

                        foreach($sql_buyer_sub_sc as $result)
                        {
                            if($submission_id_sc=0) $submission_id_sc=$result[csf("sub_id")]; else $submission_id_sc=$submission_id_sc.",".$result[csf("sub_id")];
                            //if($sc_submit_id==0) $sc_submit_id=$result[csf("sub_id")]; else $sc_submit_id=$sc_submit_id.",".$result[csf("sub_id")];
                            if($sub_inv_id_sc==0) $sub_inv_id_sc=$result[csf("inv_id")]; else $sub_inv_id_sc=$sub_inv_id_sc.",".$result[csf("inv_id")];

                            $submit_inv_arr[]=$result[csf("inv_id")];
                            $sub_buyer_arr[$result[csf("sub_id")]]['sub_id']=$result[csf("sub_id")];
                            $sub_buyer_arr[$result[csf("sub_id")]]['buyer_id']=$result[csf("buyer_id")];
                            $sub_buyer_arr[$result[csf("sub_id")]]['bank_ref_date']=$result[csf("submit_date")];
                            $sub_buyer_arr[$result[csf("sub_id")]]['inv_id']=$result[csf("inv_id")];
                            $sub_buyer_arr[$result[csf("sub_id")]]['invoice_no']=$result[csf("invoice_no")];
                            $sub_buyer_arr[$result[csf("sub_id")]]['invoice_quantity']=$result[csf("invoice_quantity")];
                            $sub_buyer_arr[$result[csf("sub_id")]]['net_invo_value']=$result[csf("net_invo_value")];
                            $sub_buyer_arr[$result[csf("sub_id")]]['lc_sc_id']=$result[csf("lc_sc_id")];
                            $sub_as_collection+=($result[csf("net_invo_value")]);
                        }
                        
                        $inv_arr=array();$in_hand=0;
                        if(!empty($lc_id_arr))
                        {
                            if($db_type==2)
                            {
                                $sql_lc=sql_select("select id, invoice_no, invoice_date, invoice_quantity, net_invo_value as invoice_value, lc_sc_id, is_lc from com_export_invoice_ship_mst  where is_lc=1 and lc_sc_id in(".implode(',',$lc_id_arr).") and id not in($sub_inv_id_lc) and  status_active=1 and is_deleted=0" );
                            }
                            else if($db_type==0)
                            {
                                $sql_lc=sql_select("select id, invoice_no, invoice_date, invoice_quantity, net_invo_value as invoice_value, lc_sc_id, is_lc from com_export_invoice_ship_mst  where is_lc=1 and lc_sc_id in(".implode(',',$lc_id_arr).") and id not in($sub_inv_id_lc) and  status_active=1 and is_deleted=0" );
                            }
                            foreach($sql_lc as $row)
                            {
                                $inv_arr[$row[csf("id")]]["id_lc"]=$row[csf("id")];
                                $inv_arr[$row[csf("id")]]["invoice_no"]=$row[csf("invoice_no")];
                                $inv_arr[$row[csf("id")]]["invoice_date"]=$row[csf("invoice_date")];
                                $inv_arr[$row[csf("id")]]["invoice_quantity"]=$row[csf("invoice_quantity")];
                                $inv_arr[$row[csf("id")]]["invoice_value"]=$row[csf("invoice_value")];
                                $inv_arr[$row[csf("id")]]["lc_sc_id"]=$row[csf("lc_sc_id")];
                                $inv_arr[$row[csf("id")]]["is_lc"]=$row[csf("is_lc")];
                                $in_hand +=$row[csf("invoice_value")];
                            }
                        }

                        if(!empty($sc_id_arr))
                        {
                            //echo $cashInAdv_sc_id."=========================================================";die;
                            if($cashInAdv_sc_id) $without_cashInAdvSc = " and lc_sc_id not in($cashInAdv_sc_id) "; else $without_cashInAdvSc = "";
                            if($db_type==2)
                            {
                                $sql_lc=sql_select("select id, invoice_no, invoice_date, invoice_quantity, net_invo_value as invoice_value, lc_sc_id, is_lc from com_export_invoice_ship_mst  where is_lc=2 and lc_sc_id in(".implode(',',$sc_id_arr).") $without_cashInAdvSc and id not in($sub_inv_id_sc)  and  status_active=1 and is_deleted=0");
                            }
                            else if($db_type==0)
                            {
                                $sql_lc=sql_select("select id, invoice_no, invoice_date, invoice_quantity, net_invo_value as invoice_value, lc_sc_id, is_lc from com_export_invoice_ship_mst  where is_lc=2 and lc_sc_id in(".implode(',',$sc_id_arr).") $without_cashInAdvSc and id not in($sub_inv_id_sc)  and  status_active=1 and is_deleted=0");
                            }
                            //echo "select id, invoice_no, invoice_date, invoice_quantity, net_invo_value as invoice_value, lc_sc_id, is_lc from com_export_invoice_ship_mst  where is_lc=2 and lc_sc_id in(".implode(',',$sc_id_arr).") $without_cashInAdvSc and id not in($sub_inv_id_sc)  and  status_active=1 and is_deleted=0";die;
                            foreach($sql_lc as $row)
                            {
                                $inv_arr[$row[csf("id")]]["id_sc"]=$row[csf("id")];
                                $inv_arr[$row[csf("id")]]["invoice_no"]=$row[csf("invoice_no")];
                                $inv_arr[$row[csf("id")]]["invoice_date"]=$row[csf("invoice_date")];
                                $inv_arr[$row[csf("id")]]["invoice_quantity"]=$row[csf("invoice_quantity")];

                                $inv_arr[$row[csf("id")]]["invoice_value"]=$row[csf("invoice_value")];
                                $inv_arr[$row[csf("id")]]["lc_sc_id"]=$row[csf("lc_sc_id")];
                                $inv_arr[$row[csf("id")]]["is_lc"]=$row[csf("is_lc")];
                                $in_hand +=$row[csf("invoice_value")];
                            }
                        }
                        //var_dump($inv_arr);die;
                        ?>
                        <td style="vertical-align:top" width="550">
                            <table width="450" style="vertical-align:top">
                                <tr>
                                    <td width="220">Total File Value</td>
                                    <td width="115" align="right"></td>
                                    <td width="115" style="font-weight:bold;" align="right">&nbsp;&nbsp;
                                        <?
                                        $file_value=$total_sales_contct_value_top+$balance+$total_direct_sc_val+$total_direct_lc_val;
                                        echo number_format($file_value,2);//."_____".$lc_id."_____".$sc_id
                                            
                                        $attach_order_id=""; $powiseJobNoArr=array();
                                        $attach_value_sales=$attach_value_lc=0;
                                        if (!empty($sc_id_arr))
                                        {
                                            $attach_sales_sql="select b.id as po_id, c.job_no, (c.job_quantity*c.total_set_qnty) as job_quantity, (a.attached_qnty*c.total_set_qnty) as attached_qnty, a.attached_value
                                            from com_sales_contract_order_info a, wo_po_break_down b, wo_po_details_master c 
                                            where a.wo_po_break_down_id=b.id and b.job_no_mst=c.job_no and a.com_sales_contract_id in(". implode(',',$sc_id_arr).") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
                                            //echo $attach_sales_sql;die;
                                            $attach_sales_sql_result=sql_select($attach_sales_sql);
                                            foreach($attach_sales_sql_result as $row)
                                            {
                                                $attachQty_sales+=$row[csf("attached_qnty")];
                                                $attach_value_sales+=$row[csf("attached_value")];
                                                $attach_order_id.=$row[csf("po_id")].",";
                                                $powiseJobNoArr[$row[csf("po_id")]]=$row[csf("job_no")];
                                                if ($jobwisejobQntySC[$row[csf("job_no")]]=="")
                                                {
                                                    $total_jobQntySC+=$row[csf("job_quantity")];
                                                    $jobwisejobQntySC[$row[csf("job_no")]]=$row[csf("job_no")];
                                                }
                                            }
                                        }
                                        //echo $total_jobQntySC;
                                        if (!empty($lc_id_arr))
                                        {
                                            $attach_lc_sql="select b.id as po_id, c.job_no, (c.job_quantity*c.total_set_qnty) as job_quantity, (a.attached_qnty*c.total_set_qnty) as attached_qnty, a.attached_value
                                            from com_export_lc_order_info a, wo_po_break_down b, wo_po_details_master c 
                                            where a.wo_po_break_down_id=b.id and b.job_no_mst=c.job_no and a.com_export_lc_id in(". implode(',',$lc_id_arr).") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
                                            $attach_lc_sql_result=sql_select($attach_lc_sql);
                                            foreach($attach_lc_sql_result as $row)
                                            {
                                                $attachQty_lc+=$row[csf("attached_qnty")];
                                                $attach_value_lc+=$row[csf("attached_value")];
                                                $attach_order_id.=$row[csf("po_id")].",";
                                                $powiseJobNoArr[$row[csf("po_id")]]=$row[csf("job_no")];
                                                if ($jobwisejobQntyLC[$row[csf("job_no")]]=="")
                                                {
                                                    $total_jobQntyLC+=$row[csf("job_quantity")];
                                                    $jobwisejobQntyLC[$row[csf("job_no")]]=$row[csf("job_no")];
                                                }
                                            }
                                        }
                                        $attach_order_id=implode(",",array_unique(explode(",",chop($attach_order_id,","))));
                                        $total_attach_value=$attach_value_sales+$attach_value_lc;
                                        $total_lcsc_attach_quantity=$attachQty_sales+$attachQty_lc;
                                        $total_job_quantity=$total_jobQntySC+$total_jobQntyLC;
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Total Attach Value</td>
                                    <td align="right"></td>
                                    <td align="right" style="font-weight:bold">&nbsp;&nbsp;
                                    <a href="##" onClick="fnc_attach_order_details('<? echo $cbo_company_name;?>','<? echo $txt_file_no;?>','<? echo $attach_order_id;?>','attach_order_details')"><? echo  number_format($total_attach_value,2); ?></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Total Shipment</td>
                                    <td align="right"></td>
                                    <td align="right" style="font-weight:bold">&nbsp;&nbsp;
                                    <?
                                    //var_dump($lc_id_arr);var_dump($sc_id_arr);
                                    $adjustment_arr=array();
                                    if(!empty($lc_id_arr))
                                    {
                                        if($db_type==0)
                                        {
                                            $sql_lc=sql_select("SELECT b.export_lc_no, a.id as id, a.invoice_no, a.invoice_quantity as current_invoice_qnty, a.invoice_value as current_invoice_value, a.net_invo_value
                                            from com_export_invoice_ship_mst a, com_export_lc b
                                            where a.lc_sc_id=b.id and a.lc_sc_id in(". implode(',',$lc_id_arr).") and a.is_lc=1 and a.status_active=1 and benificiary_id like '$cbo_company_name' and  buyer_id like '$cbo_buyer_name'");
                                        }
                                        else if($db_type==2)
                                        {
                                            $sql_lc=sql_select("SELECT b.export_lc_no, a.id as id, a.invoice_no, a.invoice_quantity as current_invoice_qnty, a.invoice_value as current_invoice_value, a.net_invo_value
                                            from com_export_invoice_ship_mst a, com_export_lc b
                                            where a.lc_sc_id=b.id and a.lc_sc_id in(". implode(',',$lc_id_arr).") and a.is_lc=1 and a.status_active=1 and benificiary_id like '$cbo_company_name' and  buyer_id like '$cbo_buyer_name'");
                                        }
                                        $total_shipment_val=0;$shp_inv_id=array();$total_discount=0;
                                        foreach($sql_lc as $row_lc_result)
                                        {
                                            $shp_inv_id[]=$row_lc_result[csf("id")];
                                            $total_shipment_val += $row_lc_result[csf("current_invoice_value")];
                                            $total_discount +=($row_lc_result[csf("current_invoice_value")]- $row_lc_result[csf("net_invo_value")]);

                                            $adjustment_arr[$row_lc_result[csf("id")]]["id"]=$row_lc_result[csf("id")];
                                            $adjustment_arr[$row_lc_result[csf("id")]]["lc_sc_no"]=$row_lc_result[csf("export_lc_no")];
                                            $adjustment_arr[$row_lc_result[csf("id")]]["invoice_no"]=$row_lc_result[csf("invoice_no")];
                                            $adjustment_arr[$row_lc_result[csf("id")]]["current_invoice_qnty"]=$row_lc_result[csf("current_invoice_qnty")];
                                            $adjustment_arr[$row_lc_result[csf("id")]]["current_invoice_value"]=$row_lc_result[csf("current_invoice_value")];
                                            $adjustment_arr[$row_lc_result[csf("id")]]["net_invo_value"]=$row_lc_result[csf("net_invo_value")];
                                        }
                                    }

                                    if(!empty($sc_id_arr))
                                    {
                                        if($db_type==0)
                                        {
                                            $sql_cur_ship ="SELECT contract_no, a.id as id, a.invoice_no, a.invoice_quantity as current_invoice_qnty, a.invoice_value as current_invoice_value, a.net_invo_value
                                            from com_export_invoice_ship_mst a, com_sales_contract b
                                            where a.lc_sc_id=b.id and a.lc_sc_id in(".implode(',',$sc_id_arr).") and a.is_lc=2 and a.status_active=1 and benificiary_id like '$cbo_company_name' and  buyer_id like '$cbo_buyer_name'";
                                            $sql_sc_q=sql_select($sql_cur_ship);
                                        }
                                        else if($db_type==2)
                                        {
                                            $sql_cur_ship = "SELECT contract_no, a.id as id, a.invoice_no, a.invoice_quantity as current_invoice_qnty, a.invoice_value as current_invoice_value, a.net_invo_value
                                            from com_export_invoice_ship_mst a, com_sales_contract b
                                            where a.lc_sc_id=b.id and a.lc_sc_id in(".implode(',',$sc_id_arr).") and a.is_lc=2 and a.status_active=1 and benificiary_id like '$cbo_company_name' and  buyer_id like '$cbo_buyer_name'";
                                            $sql_sc_q=sql_select($sql_cur_ship);
                                        }
                                        foreach($sql_sc_q as $row_sc_result)
                                        {
                                            $shp_inv_id[]=$row_sc_result[csf("id")];
                                            $total_shipment_val += $row_sc_result[csf("current_invoice_value")];
                                            $total_discount += ($row_sc_result[csf("current_invoice_value")]-$row_sc_result[csf("net_invo_value")]);

                                            $adjustment_arr[$row_sc_result[csf("id")]]["id"]=$row_sc_result[csf("id")];
                                            $adjustment_arr[$row_sc_result[csf("id")]]["lc_sc_no"]=$row_sc_result[csf("contract_no")];//$row_sc_result[csf("lc_sc_no")];
                                            $adjustment_arr[$row_sc_result[csf("id")]]["invoice_no"]=$row_sc_result[csf("invoice_no")];
                                            $adjustment_arr[$row_sc_result[csf("id")]]["current_invoice_qnty"]=$row_sc_result[csf("current_invoice_qnty")];
                                            $adjustment_arr[$row_sc_result[csf("id")]]["current_invoice_value"]=$row_sc_result[csf("current_invoice_value")];
                                            $adjustment_arr[$row_sc_result[csf("id")]]["net_invo_value"]=$row_sc_result[csf("net_invo_value")];
                                        }

                                    }
                                    $lc_id=implode(',',$lc_id_arr); $sc_id=implode(',',$sc_id_arr); //hidden_lc_sc_id
                                    ?>
                                    <a href="##" onClick="fnc_amount_detail('<? echo $cbo_company_name;?>','<? echo $lc_id;?>','<? echo $sc_id;?>','invoice_details','<? echo $cbo_buyer_name; ?>')"><? echo  number_format($total_shipment_val,2); ?></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Payment Realized</td>
                                    <td align="right">
                                    <?
                                    $total_break_ship_val=0;
                                    $total_break_ship_val=$total_break_ship_val+$payment_realized;
                                    echo number_format(($payment_realized),2);
                                    ?>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Document Under Collection/Rlz.</td>
                                    <td align="right">
                                        <?
                                        //var_dump($submission_id_sc);die;
                                        if($submission_id_lc)
                                        {
                                            $sub_as_purchase_amt_lc=return_field_value("sum(b.lc_sc_curr) as net_invo_value","com_export_doc_submission_mst a, com_export_doc_sub_trans b"," a.id=b.doc_submission_mst_id AND a.submit_type=2 and b.doc_submission_mst_id in($submission_id_lc)","net_invo_value");
                                        }
                                        if($submission_id_sc)
                                        {
                                            $sub_as_purchase_amt_sc=return_field_value("sum(b.lc_sc_curr) as net_invo_value","com_export_doc_submission_mst a, com_export_doc_sub_trans b"," a.id=b.doc_submission_mst_id AND a.submit_type=2 and b.doc_submission_mst_id in($submission_id_sc) ","net_invo_value");
                                        }

                                        $sub_as_purchase_amt=$sub_as_purchase_amt_lc+$sub_as_purchase_amt_sc;
                                        $sub_as_collection_rlz=$sub_as_collection-$sub_as_purchase_amt;
                                        $total_break_ship_val=$total_break_ship_val+$sub_as_collection_rlz;
                                        echo number_format(($sub_as_collection_rlz),2);
                                        ?>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Document Purchased</td>
                                    <td align="right">
                                        <?
                                            $total_break_ship_val=$total_break_ship_val+$sub_as_purchase_amt;
                                            echo number_format(($sub_as_purchase_amt),2);
                                        ?>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Document In Hand</td>
                                    <td align="right">
                                        <?
                                            $total_break_ship_val=$total_break_ship_val+$in_hand;
                                            echo number_format($in_hand,2);
                                        ?>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Short Realization</td>
                                    <td align="right">
                                    <?
                                        $total_break_ship_val=$total_break_ship_val+$payment_realized_deduction;
                                        echo number_format($payment_realized_deduction,2) ;
                                    ?>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Discount(<span style="font-size:9px; font-family:'Times New Roman', Times, serif">Gross Invoice value-Net Invoice Value</span>)</td>
                                    <td align="right">
                                        <?
                                            $total_break_ship_val=$total_break_ship_val+$total_discount;
                                            //$st_ex_ship_perc = ($payment_realized_deduction*100)/$payment_realized;
                                            echo number_format($total_discount,2);
                                        ?>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="3">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td align="right" style="font-weight:bold;">Total:</td>
                                    <td align="right" style="border-top-style:solid;border-top-width:1px;font-weight:bold;">
                                        <?
                                            echo number_format($total_break_ship_val,2);
                                        ?>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>% of Short/Excess Shipment</td>
                                    <td align="right">
                                        <?
                                            $st_ex_ship_perc = ($payment_realized_deduction*100)/$payment_realized;
                                            echo number_format($st_ex_ship_perc,2);
                                        ?>%
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Balance Shipment</td>
                                    <td align="right">
                                    </td>
                                    <td align="right" style="font-weight:bold;">&nbsp;&nbsp;
                                        <?
                                            $shipment_balance =  $file_value-$total_shipment_val;
                                            echo number_format($shipment_balance,2);
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Attached Order Qty</td>
                                    <td align="right">
                                    </td>
                                    <td align="right" style="font-weight:bold;">&nbsp;&nbsp;
                                        <?
                                        //print_r($lc_id_arr);echo "cks";die;
                                        
                                        
                                        //echo $attach_order_id.pk;die;

                                        $attachQty=$attachQty_sales+$attachQty_lc;                               
                                        echo number_format($attachQty,2);
                                        //echo $attach_order_id.test;die;
                                        $budge_btb_open_amt=0;
                                        
                                        if($attach_order_id!="")
                                        {
                                            $condition= new condition();
                                            $condition->po_id(" in( $attach_order_id ) ");
                                            
                                            $condition->init();
                                            $fabric= new fabric($condition);
                                            $fabric_costing_arr=$fabric->getAmountArray_by_order_knitAndwoven_greyAndfinish();

                                            $conversion= new conversion($condition);
                                            $conversion_costing_arr=$conversion->getAmountArray_by_order();
                                            //echo '<pre>';print_r($conversion_costing_arr);
                                            //echo $conversion->getQuery();die;
                                            
                                            $yarn= new yarn($condition);
                                            $yarn_costing_arr=$yarn->getOrderWiseYarnAmountArray();
                                            
                                            $trims= new trims($condition);
                                            $trims_costing_arr=$trims->getAmountArray_by_order();
                                            
                                            $emblishment= new emblishment($condition);
                                            $emblishment_costing_arr=$emblishment->getAmountArray_by_orderAndEmbname();
                                            //print_r($emblishment_costing_arr);
                                            
                                            $wash= new wash($condition);
                                            $emblishment_costing_arr_name_wash=$wash->getAmountArray_by_orderAndEmbname();
                                            
                                            /*$budge_btb_open_sql="select a.job_no, (d.job_quantity*d.total_set_qnty) as job_quantity, (d.job_quantity*d.total_set_qnty)+((d.job_quantity*(c.excess_cut/100))*d.total_set_qnty) as job_quantity_plan, (c.po_quantity*d.total_set_qnty) as po_quantity, (c.plan_cut*d.total_set_qnty) as plan_cut_quantity, e.budget_on, a.costing_per, e.amount, e.id as dtls_id, 0 as type
                                            from wo_po_break_down c, wo_po_details_master d, wo_pre_cost_mst a, wo_pre_cost_fabric_cost_dtls e  
                                            where c.job_no_mst=d.job_no and d.job_no=a.job_no and e.job_no=d.job_no and e.job_no=c.job_no_mst and e.job_no=a.job_no and e.fabric_source=2 and a.status_active=1 and c.status_active=1 and e.status_active=1 and e.amount > 0 and c.id in($attach_order_id) 	
                                            union all
                                            select a.job_no, (d.job_quantity*d.total_set_qnty) as job_quantity, (d.job_quantity*d.total_set_qnty)+((d.job_quantity*(c.excess_cut/100))*d.total_set_qnty) as job_quantity_plan, (c.po_quantity*d.total_set_qnty) as po_quantity, (c.plan_cut*d.total_set_qnty) as plan_cut_quantity, e.budget_on, a.costing_per, b.amount, b.id as dtls_id, 1 as type
                                            from wo_po_break_down c, wo_po_details_master d, wo_pre_cost_mst a, wo_pre_cost_fab_yarn_cost_dtls b, wo_pre_cost_fabric_cost_dtls e  
                                            where c.job_no_mst=d.job_no and d.job_no=a.job_no and a.job_no=b.job_no and b.fabric_cost_dtls_id=e.id and e.job_no=d.job_no and e.job_no=c.job_no_mst and a.status_active=1 and b.status_active=1 and c.status_active=1 and e.status_active=1 and b.amount > 0 and c.id in($attach_order_id) 	
                                            union all 
                                            select a.job_no, (d.job_quantity*d.total_set_qnty) as job_quantity, (d.job_quantity*d.total_set_qnty)+((d.job_quantity*(c.excess_cut/100))*d.total_set_qnty) as job_quantity_plan, (c.po_quantity*d.total_set_qnty) as po_quantity, (c.plan_cut*d.total_set_qnty) as plan_cut_quantity, 0 as budget_on, a.costing_per, b.amount, b.id as dtls_id, 2 as type
                                            from wo_po_break_down c, wo_po_details_master d, wo_pre_cost_mst a, wo_pre_cost_trim_cost_dtls b 
                                            where c.job_no_mst=d.job_no and d.job_no=a.job_no and a.job_no=b.job_no and a.status_active=1 and b.status_active=1 and c.status_active=1 and b.amount > 0 and c.id in($attach_order_id)
                                            union all 
                                            select a.job_no, (d.job_quantity*d.total_set_qnty) as job_quantity, (d.job_quantity*d.total_set_qnty)+((d.job_quantity*(c.excess_cut/100))*d.total_set_qnty) as job_quantity_plan, (c.po_quantity*d.total_set_qnty) as po_quantity, (c.plan_cut*d.total_set_qnty) as plan_cut_quantity, b.budget_on, a.costing_per, b.amount, b.id as dtls_id, 3 as type
                                            from wo_po_break_down c, wo_po_details_master d, wo_pre_cost_mst a, wo_pre_cost_embe_cost_dtls b 
                                            where c.job_no_mst=d.job_no and d.job_no=a.job_no and a.job_no=b.job_no and a.status_active=1 and b.status_active=1 and c.status_active=1 and b.amount > 0 and b.emb_name <>2 and c.id in($attach_order_id)";
                                            echo $budge_btb_open_sql;die;
                                            $budge_btb_open_result=sql_select($budge_btb_open_sql);*/
                                            $budge_btb_open_amt=0;
                                            $attach_order_id_arr=explode(",",$attach_order_id);
                                            //$attach_order_id_arr=array(0=>"51627",1=>"51628");
                                            foreach($attach_order_id_arr as $bompoid)
                                            {
                                                $job_wise_budge_amt[$powiseJobNoArr[$bompoid]]['0']+=array_sum($fabric_costing_arr['sweater']['grey'][$bompoid])+array_sum($fabric_costing_arr['knit']['grey'][$bompoid])+array_sum($fabric_costing_arr['woven']['grey'][$bompoid]);
                                                $job_wise_budge_amt[$powiseJobNoArr[$bompoid]]['1']+=$yarn_costing_arr[$bompoid];
                                                $job_wise_budge_amt[$powiseJobNoArr[$bompoid]]['2']+=$trims_costing_arr[$bompoid];
                                                $job_wise_budge_amt[$powiseJobNoArr[$bompoid]]['3']+=$emblishment_costing_arr[$bompoid][1]+$emblishment_costing_arr_name_wash[$bompoid][3]+$emblishment_costing_arr[$bompoid][4]+$emblishment_costing_arr[$bompoid][5];
                                               $job_wise_budge_amt[$powiseJobNoArr[$bompoid]]['4']+=array_sum($conversion_costing_arr[$bompoid]);
                                            }
                                            
                                            $budge_btb_open_amt=$fab_budge_cost=$yarn_budge_cost=0;
                                            $trims_budge_cost=$embel_budge_cost=$conversion_budge_cost=0;
                                            foreach($job_wise_budge_amt as $job_no_ref=>$job_data)
                                            {
                                                $budge_btb_open_amt+=array_sum($job_data);
                                                $fab_budge_cost+=$job_data[0];
                                                $yarn_budge_cost+=$job_data[1];
                                                $trims_budge_cost+=$job_data[2];
                                                $embel_budge_cost+=$job_data[3];
                                                $conversion_budge_cost+=$job_data[4];
                                            }
                                            
                                            //echo "<pre>";print_r($job_wise_budge_amt2);die;
                                            /*foreach($budge_btb_open_result as $row)
                                            {
                                                $dzn_qnty=0;
                                                $costing_per_id=$row[csf('costing_per')];//$fabriccostArray[$row[csf('job_no')]]['costing_per_id'];
                                                if($costing_per_id==1) $dzn_qnty=12;
                                                else if($costing_per_id==3) $dzn_qnty=12*2;
                                                else if($costing_per_id==4) $dzn_qnty=12*3;
                                                else if($costing_per_id==5) $dzn_qnty=12*4;
                                                else $dzn_qnty=1;
                                                $amount=0;
                                                if($row[csf('budget_on')]==2) 
                                                {
                                                    $amount=($row[csf("amount")]/$dzn_qnty)*$row[csf("plan_cut_quantity")]; 
                                                }
                                                else 
                                                {
                                                    $amount=($row[csf("amount")]/$dzn_qnty)*$row[csf("po_quantity")];
                                                }
                                                
                                                $job_wise_budge_amt[$row[csf('job_no')]][$row[csf('type')]]+=$amount;
                                                $budge_btb_open_amt+=$amount;
                                            }*/
                                        }
                                        if(empty($lc_id_arr)) $lc_Ids_arr=0;  else $lc_Ids_arr=implode(',',$lc_id_arr);
                                        if(empty($sc_id_arr)) $sc_Ids_arr=0;  else $sc_Ids_arr=implode(',',$sc_id_arr);

                                        if($db_type==0)
                                        {
                                            $btb_mst_lc_id=return_field_value("group_concat(distinct import_mst_id) as import_mst_id"," com_btb_export_lc_attachment","lc_sc_id in($lc_Ids_arr) and is_lc_sc=0 and status_active=1 and is_deleted=0","import_mst_id");
                                            //echo "select group_concat(distinct import_mst_id) as import_mst_id from com_btb_export_lc_attachment where lc_sc_id in($sc_Ids_arr) and is_lc_sc=1  and status_active=1 and is_deleted=0";
                                            $btb_mst_sc_id=return_field_value("group_concat(distinct import_mst_id) as import_mst_id","com_btb_export_lc_attachment","lc_sc_id in($sc_Ids_arr) and is_lc_sc=1  and status_active=1 and is_deleted=0","import_mst_id");
                                        }
                                        else if($db_type==2)
                                        {
                                            $btb_mst_lc_id=return_field_value("LISTAGG(CAST(import_mst_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY import_mst_id) as import_mst_id"," com_btb_export_lc_attachment","lc_sc_id in($lc_Ids_arr) and is_lc_sc=0  and status_active=1 and is_deleted=0","import_mst_id");
                                            $btb_mst_sc_id=return_field_value("LISTAGG(CAST(import_mst_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY import_mst_id) as import_mst_id","com_btb_export_lc_attachment","lc_sc_id in($sc_Ids_arr) and is_lc_sc=1  and status_active=1 and is_deleted=0","import_mst_id");
                                        }
                                        if($btb_mst_lc_id=="") $btb_mst_lc_id=0;
                                        if($btb_mst_sc_id=="") $btb_mst_sc_id=0;

                                        $mst_id=$btb_mst_lc_id.",".$btb_mst_sc_id;
                                        $sort_val=(explode(",",$mst_id));
                                        asort($sort_val);
                                        $mst_id=implode(",",$sort_val);

                                        $sql_btb=sql_select("select
                                        a.id, sum(a.lc_value) as lc_value, a.currency_id, a.lc_date, a.importer_id, 
                                        max(case when a.payterm_id=1 THEN a.id else 0 end) as at_sight_btb_lc_id,
                                        max(case when a.payterm_id=2 THEN a.id else 0 end) as usance_btb_lc_id,
                                        max(case when a.payterm_id=3 THEN a.id else 0 end) as cash_btb_lc_id,
                                        sum(case when a.payterm_id=3 THEN a.lc_value else 0 end) as cash_in_advance
                                        from com_btb_lc_master_details a
                                        where a.id in($mst_id)  and a.is_deleted=0 and a.status_active=1 group by a.id, a.currency_id, a.lc_date, a.importer_id");
                                        $btb_id="";
                                        foreach($sql_btb as $row)
                                        {
                                            $btb_id.=$row[csf("id")].",";
											if($row[csf("currency_id")]==1)
											{
												$conversion_rate=set_conversion_rate(2, $row[csf("lc_date")],$row[csf("importer_id")]);
												$row[csf("lc_value")]=$row[csf("lc_value")]/$conversion_rate;
											}
											
                                            $btb_open_value +=$row[csf("lc_value")];
                                            $at_sight_lc_id .=$row[csf("at_sight_btb_lc_id")].",";
                                            $usance_lc_id .=$row[csf("usance_btb_lc_id")].",";
                                            $cash_lc_id .=$row[csf("cash_btb_lc_id")].",";
                                            $cash_in_advance +=$row[csf("cash_in_advance")];
                                        }
                                        $btb_id=chop($btb_id,",");
                                        $atsite_accep_id=substr($at_sight_lc_id, 0, -1);
                                        $usance_paid_lc_id=substr($usance_lc_id, 0, -1);
                                        $cash_lc_id=substr($cash_lc_id, 0, -1);
                                        //$btb_tobe_open_value=$budge_btb_open_amt-$btb_open_value;
                                        $btb_tobe_open_value=((($fab_budge_cost+$yarn_budge_cost+$conversion_budge_cost+$trims_budge_cost+$embel_budge_cost)/$total_job_quantity)*$total_lcsc_attach_quantity)-$btb_open_value;
                                        //echo 'system'.$conversion_budge_cost;
                                        ?>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td style="vertical-align:top" width="450">
                            <table width="400" style="vertical-align:top">
                                <tr>
                                    <td width="220" title="<? //print_r($job_wise_budge_amt); ?>">BTB to be Open</td>
                                    <td width="100" align="right" style="font-weight:bold;" title="(((fab_budge_cost+yarn_budge_cost+conversion_budge_cost+trims_budge_cost+embel_budge_cost)/total_job_quantity)*total_lcsc_attach_quantity)-btb_open_value">
                                    <?
                                    echo number_format($btb_tobe_open_value,2);
                                    ?>
                                </td>
                                <td>&nbsp; </td>
                                </tr>
                                <tr>
                                    <td width="220">Total BTB Opened</td>
                                    <td width="100" align="right" style="font-weight:bold;">
                                        <input type="hidden" id="hidden_btb_id" value="<? echo $btb_id; ?>">
                                        <a href="##" onClick="btb_open('btb_open','<? echo $btb_id; ?>','<? echo $txt_file_no.'*'.$buyer_ref_id; ?>')"><? echo number_format($btb_open_value,2); ?> </a>
                                    </td>
                                    <td>&nbsp; </td>
                                </tr>
                                <tr>
                                    <td>BTB Percentage</td>
                                    <td align="right" style="font-weight:bold;"><? $btb_percent=($btb_open_value/$file_value)*100; echo number_format($btb_percent,2); ?>%</td>
                                    <td>&nbsp; </td>
                                </tr>
                                <tr>
                                    <td>Total BTB Accepted</td>
                                    <td align="right" style="font-weight:bold;" title="<? echo "all btb id=".$mst_id ?>">
                                        <?
                                        if($btb_id=="") $btb_id=0;
                                        if($atsite_accep_id=="") $atsite_accep_id=0;
                                        //echo $btb_id;die;
                                        //echo "select sum(current_acceptance_value) as current_acceptance_value from com_import_invoice_dtls where btb_lc_id in($btb_id) group by btb_lc_id";die;
                                        $sql_accep="select a.id as btb_id, a.currency_id, a.lc_date, a.importer_id, sum(b.current_acceptance_value) as current_acceptance_value from com_btb_lc_master_details a, com_import_invoice_dtls b where a.id=b.btb_lc_id and b.btb_lc_id in($btb_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.currency_id, a.lc_date, a.importer_id";
                                        $sql_accep_res=sql_select($sql_accep);
                                        $paid_lc_conversion_arr=array();
                                        foreach($sql_accep_res as $row)
                                        {
                                            if($row[csf("currency_id")]==1)
                                            {
                                                $conversion_rate=set_conversion_rate(2, $row[csf("lc_date")],$row[csf("importer_id")]);
                                                $bill_accepted +=$row[csf("current_acceptance_value")]/$conversion_rate;
                                                $paid_lc_conversion_arr[$row[csf("btb_id")]]=$conversion_rate;
                                            }
                                            else
                                            {
                                                $bill_accepted +=$row[csf("current_acceptance_value")];
                                            }                                            
                                        }
                                        //$bill_accepted=return_field_value("sum(current_acceptance_value) as current_acceptance_value","com_import_invoice_dtls","btb_lc_id in($btb_id) and status_active=1 and is_deleted=0","current_acceptance_value");

                                        //$atsite_accepted=return_field_value("sum(current_acceptance_value) as current_acceptance_value","com_import_invoice_dtls","btb_lc_id in($atsite_accep_id) and status_active=1 and is_deleted=0","current_acceptance_value");
										//echo "select a.BTB_LC_ID, a.CURRENT_ACCEPTANCE_VALUE from com_import_invoice_dtls a, com_import_payment_com b where a.IMPORT_INVOICE_ID=b.INVOICE_ID and a.status_active=1 and b.status_active=1 and a.btb_lc_id in($atsite_accep_id)";
										$atsite_accep_sql=sql_select("select a.BTB_LC_ID, a.CURRENT_ACCEPTANCE_VALUE from com_import_invoice_dtls a, com_import_payment_com b where a.IMPORT_INVOICE_ID=b.INVOICE_ID and a.status_active=1 and b.status_active=1 and a.btb_lc_id in($atsite_accep_id)");
										$atsite_accep_id=array();
										foreach($atsite_accep_sql as $val)
										{
											$atsite_accepted+=$val["CURRENT_ACCEPTANCE_VALUE"];
											$atsite_accep_id[$val["BTB_LC_ID"]]=$val["BTB_LC_ID"];
										}
										//echo "kkk";print_r($atsite_accep_id);die;

                                        //echo $btb_inv_id;
                                        ?>

                                        <input type="hidden" id="hidden_acept_id" value="<? echo $btb_id; ?>">
                                        <a href="##" onClick="btb_open('btb_accep','<? echo $btb_id; ?>','<? echo $txt_file_no.'*'.$buyer_ref_id; ?>')">
                                        <?
                                        echo number_format($bill_accepted,2);
                                        //echo $btb_inv_id;
                                        ?></a>
                                    </td>
                                    <td>&nbsp; </td>
                                </tr>
                                <tr>
                                    <td>Total Paid</td>
                                    <?
                                    if($cash_lc_id=="") $cash_lc_id=0;
                                    if($usance_paid_lc_id=="") $usance_paid_lc_id=0;
                                    $paid2_sql=sql_select("select a.id, b.btb_lc_id, a.accepted_ammount as paid from com_import_payment a, com_import_invoice_dtls b where b.import_invoice_id=a.INVOICE_ID and b.btb_lc_id in($usance_paid_lc_id) and b.status_active=1 and a.status_active=1");//die;
                                    //echo "select a.id, a.accepted_ammount as paid from com_import_payment a, com_import_invoice_dtls b where b.import_invoice_id=a.INVOICE_ID and b.btb_lc_id in($usance_paid_lc_id) and b.status_active=1 and a.status_active=1";
                                    foreach($paid2_sql as $row)
                                    {
                                        $all_usence_paid_id.=$row[csf("id")].",";
                                        if($paid_id_tes[$row[csf("id")]]=="")
                                        {
                                            $paid_id_tes[$row[csf("id")]]=$row[csf("id")];
                                            if ($paid_lc_conversion_arr[$row[csf("btb_lc_id")]]){
                                                $paid2+=$row[csf("paid")]/$paid_lc_conversion_arr[$row[csf("btb_lc_id")]];
                                            } else {
                                                $paid2+=$row[csf("paid")];
                                            }
                                            
                                        }
                                    }
                                    //echo $paid2;
                                    $all_usence_paid_id=chop($all_usence_paid_id,",");
                                    //$paid2=return_field_value("sum(accepted_ammount) as paid"," com_import_payment","lc_id in($usance_paid_lc_id) and status_active='1'","paid");

                                    $paid=($paid2+$atsite_accepted+$cash_in_advance);
                                    //$paid=($atsite_accepted+$cash_in_advance);
                                    $cash_lc_id=implode(",",array_unique(explode(",",$cash_lc_id)));
                                    $atsite_accep_id=implode(",",array_unique($atsite_accep_id));
									
                                    $usance_paid_lc_id=implode(",",array_unique(explode(",",$usance_paid_lc_id)));
                                    $all_usence_paid_id=implode(",",array_unique(explode(",",$all_usence_paid_id)));
                                    //$paid_all_id=$cash_lc_id."_".$atsite_accep_id."_".$all_usence_paid_id;
                                    $paid_all_id=implode(",",array_unique(explode(",",$btb_id)))."_".$cash_lc_id."_".$atsite_accep_id."_".$all_usence_paid_id;
                                    //echo $usance_paid_lc_id;

                                    ?>
                                    <td align="right" style="font-weight:bold;" title="<? echo $paid2."==".$atsite_accepted."==".$cash_in_advance;?>">

                                    <a href="##" onClick="btb_open('btb_paid','<? echo $paid_all_id; ?>','<? echo $txt_file_no.'*'.$buyer_ref_id; ?>')">
                                    <?
                                    echo number_format($paid,2);
                                    ?>
                                    </a>
                                    </td>
                                    <td>&nbsp; </td>
                                </tr>
                                <tr>
                                    <td>Balance</td>
                                    <td align="right" style="font-weight:bold;">
                                        <?
                                            $bal= $bill_accepted-$paid;
                                            echo number_format($bal,2);

                                        ?>
                                    </td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>Yet To Acceptance</td>
                                    <td align="right" style="font-weight:bold;">
                                        <a href="##" onClick="btb_open('yet_to_accept_pop_up','<? echo $btb_id; ?>','<? echo $txt_file_no.'*'.$buyer_ref_id; ?>')">
                                        <?
                                            $yet_accept= $btb_open_value-$bill_accepted;
                                            echo number_format($yet_accept,2);

                                        ?>
                                        </a>
                                    </td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>BTB Margin A/C Balance</td>
                                    <td align="right" style="font-weight:bold;" title="<? echo "BTB Margine=".$total_btb_margine." ; Paid = ".$paid; ?>">
                                        <?
                                            $total_btb_margine_balance=$total_btb_margine-$paid;//$total_btb_margine
                                            echo number_format($total_btb_margine_balance,2);
                                        ?>
                                    </td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>PC Amount</td>
                                    <td align="right" style="font-weight:bold;">
                                        <?
                                        //print_r($sc_id_arr);
                                        if (!empty($lc_id_arr))
                                        {
                                            $pc_amount_lc= return_field_value("sum(b.loan_amount) as loan_amount", "com_pre_export_finance_dtls b, com_pre_export_lc_wise_dtls c","b.id = c.pre_export_dtls_id and c.export_type=1 and b.status_active=1 and b.is_deleted=0 and c.lc_sc_id in(". implode(',',$lc_id_arr).") and b.loan_type=20","loan_amount");
                                        }

                                        if (!empty($sc_id_arr))
                                        {
                                            $pc_amount_sc= return_field_value("sum(b.loan_amount) as loan_amount", "com_pre_export_finance_dtls b, com_pre_export_lc_wise_dtls c","b.id = c.pre_export_dtls_id and c.export_type=2 and b.status_active=1 and b.is_deleted=0 and c.lc_sc_id in(". implode(',',$sc_id_arr).") and b.loan_type=20","loan_amount");
                                        } 

                                        $pc_amount = $pc_amount_sc + $pc_amount_lc;
                                        echo number_format($pc_amount,2);
                                        ?>
                                    </td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td colspan="3">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td colspan="3">BTB Accepted : &nbsp; &nbsp;
                                    <input type="button" class="formbutton" id="btn_trims" style="width:70px;" value="Trims" onClick="btb_open_category('btb_accep_category','<? echo $btb_id; ?>','<? echo $txt_file_no.'*'.$buyer_ref_id; ?>','<? echo str_replace("'","",$cbo_buyer_name); ?>','<? echo $trims_budge_cost; ?>','4')" /> &nbsp;
                                    <input type="button" class="formbutton" id="btn_yarn" style="width:70px;" value="Yarn" onClick="btb_open_category('btb_accep_category','<? echo $btb_id; ?>','<? echo $txt_file_no.'*'.$buyer_ref_id; ?>','<? echo str_replace("'","",$cbo_buyer_name); ?>','<? echo $yarn_budge_cost; ?>','1')" /> &nbsp;
                                    <input type="button" class="formbutton" id="btn_fabric" style="width:70px;" value="Fabric" onClick="btb_open_category('btb_accep_category','<? echo $btb_id; ?>','<? echo $txt_file_no.'*'.$buyer_ref_id; ?>','<? echo str_replace("'","",$cbo_buyer_name); ?>','<? echo $fab_budge_cost; ?>','2,3')" /> &nbsp;
                                    <input type="button" class="formbutton" id="btn_service" style="width:70px; display:none;" value="Service" onClick="btb_open_category('btb_accep_category','<? echo $btb_id; ?>','<? echo $txt_file_no.'*'.$buyer_ref_id; ?>','<? echo str_replace("'","",$cbo_buyer_name); ?>','<? echo $service_budge_cost; ?>','12,74,25,104,102,103,24,31')" /> &nbsp;
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                <table width="1900" cellpadding="0" cellspacing="0" border="0" rules="all" id="" align="left">
                    <tr>
                        <td><strong style="font:Arial, Helvetica, sans-serif; font-size:16px;">Document Realized:</strong></td>
                    </tr>
                </table>
                <table width="1900" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all" id="" align="left">
                    <thead>
                        <tr>
                            <th  rowspan="2">Sl</th>
                            <th width="120" rowspan="2">Invoice No.</th>
                            <th width="140" rowspan="2">Export Bill No.</th>
                            <th width="80" rowspan="2">Bill Date</th>
                            <th width="70" rowspan="2">Inv/Bill Qty/Pcs</th>
                            <th width="85" rowspan="2">Bill Amount</th>
                            <th width="85">Sub Under Collection</th>
                            <th colspan="4">Sub. Under Purchase</th>
                            <th colspan="3">Realized</th>
                            <th colspan="9">Proceeds Distribution A/C</th>
                        </tr>
                        <tr>
                            <th>Amount</th>
                            <th width="90">Bill Amount</th>
                            <th width="90">Purchase Amount</th>
                            <th width="50">(%)</th>
                            <th width="80">Purchase Date</th>
                            <th width="85">Amount</th>
                            <th width="80">Date</th>
                            <th width="85">Short Realization</th>
                            <th width="75">ERQ A/C</th>
                            <th width="75">BTB Margin A/C</th>
                            <th width="75">Sundry A/C</th>
                            <th width="75">STD A/C</th>
                            <th width="75">CD A/C</th>
                            <th width="75">CC A/C</th>
                            <th width="75">FDBC</th>
                            <th width="90">Exp & Adj.</th>
                            <th width="80">Balance</th>
                        </tr>
                    </thead>
                </table>
                <table width="1900" rules="all" class="rpt_table" align="left" id="" border="1">
                    <tbody>
                        <?
                        $lc_num_chack=array();$i=1;$c=1;
                        foreach ($realize_lc_arr as $key=>$val)
                        {
                            $submision_id=$val[('sub_id')];
                            $purchase_amt=return_field_value("sum(b.lc_sc_curr) as dom_curr"," com_export_doc_sub_trans b","b.doc_submission_mst_id in(".$val[("sub_id")].") group by b.doc_submission_mst_id ","dom_curr");
                            if(!in_array($val["lc_sc_id"],$lc_num_chack))
                            {
                                $lc_num_chack[]=$val["lc_sc_id"];
                                if($c!=1)
                                {
                                    ?>
                                    <tr align="right" bgcolor="#CCCCCC">
                                        <td><p><strong></strong></p>&nbsp;</td>
                                        <td><p><strong></strong></p>&nbsp;</td>
                                        <td><p><strong></strong></p>&nbsp;</td>
                                        <td><strong>Sub-Total</strong>&nbsp;</td>
                                        <td><p><strong><? echo number_format($lc_bill_qty,0); $gt_lc_bill_qty+=$lc_bill_qty; ?></strong></p>&nbsp;</td>
                                        <td><p><strong><? echo number_format($lc_bill_value,2); $gt_lc_bill_value+=$lc_bill_value; ?></strong></p>&nbsp;</td>
                                        <td><p><strong><? echo number_format($lc_doc_col_value,2); $gt_lc_doc_col_value+=$lc_doc_col_value; ?></strong></p>&nbsp;</td>
                                        <td><p><strong><? echo number_format($lc_bill_value_sub,2); $gt_sub_lc_bill_value+=$lc_bill_value_sub; ?></strong></p>&nbsp;</td>
                                        <td><p><strong><? echo number_format($lc_doc_pur_value,2); $gt_lc_doc_pur_value+=$lc_doc_pur_value; ?></strong></p>&nbsp;</td>
                                        <td><p><strong></strong></p></td>
                                        <td><p><strong></strong></td>
                                        <td><p><strong><? echo number_format($lc_doc_distribute_value,2); $gt_lc_doc_distribute_value+=$lc_doc_distribute_value;?></strong></p>&nbsp;</td>
                                        <td></td>
                                        <td><p><strong><? echo number_format($lc_realized_deduct,2); $gt_lc_realized_deduct+=$lc_realized_deduct;?></strong></p>&nbsp;</td>
                                        <td><p><strong><? echo number_format($lc_erq,2); $gt_lc_erq+=$lc_erq; ?></strong></p>&nbsp;</td>
                                        <td><p><strong><? echo number_format($lc_btb,2); $gt_lc_btb+=$lc_btb; ?> </strong></p>&nbsp;</td>
                                        <td><p><strong><? echo number_format($lc_sundry_ac,2); $gt_lc_sundry_ac+=$lc_sundry_ac; ?></strong></p>&nbsp;</td>
                                        <td><p><strong><? echo number_format($lc_std_spaecial,2); $gt_lc_std_spaecial+=$lc_std_spaecial; ?></strong></p>&nbsp;</td>
                                        <td><p><strong><? echo number_format($lc_cd,2); $gt_lc_cd+=$lc_cd; ?></strong></p>&nbsp;</td>
                                        <td><p><strong><? echo number_format($lc_cc,2); $gt_lc_cc+=$lc_cc; ?></strong></p>&nbsp;</td>
                                        <td><p><strong><? echo number_format($lc_fdbc,2); $gt_lc_fdbc+=$lc_fdbc; ?></strong></p>&nbsp;</td>
                                        <td><p><strong><? echo number_format($lc_exp_adj,2); $gt_lc_exp_adj+=$lc_exp_adj;?></strong></p>&nbsp;</td>
                                        <td><p><strong><? //echo number_format($sc_exp_adj,2); $gt_sc_exp_adj+=$sc_exp_adj; ?></strong></p>&nbsp;</td>
                                    </tr>
                                    <?
                                }
                                $c++;
                                $lc_bill_qty =0;
                                $lc_bill_value =0;
                                $lc_doc_col_value =0;
                                $lc_doc_pur_value =0;
                                $lc_doc_distribute_value =0;
                                $lc_realized_deduct = 0;
                                $lc_bill_value_sub=0;
                                $lc_erq = 0;
                                $lc_btb = 0;
                                $lc_cd = 0;
                                $lc_cc =0;
                                $lc_sundry_ac =0;
                                $lc_std_spaecial =0;
                                $lc_fdbc =0;
                                $lc_exp_adj =0;
                                $lc_short_ship_export =0;
                                ?>
                                <tr>
                                    <td colspan="23" style="background-color:#FDF4EF"><b>
                                    <?/*
                                    $export_number_rlz=return_field_value("export_lc_no","com_export_lc","id='".$val[('lc_sc_id')]."' and status_active=1","export_lc_no");
                                    echo "Export L/C No."." - ".$export_number_rlz; */

                                    $lc_sc_ids=$val[('lc_sc_id')];
                                    $export_number_rlz='';
                                    $lc_sc_id=explode(",",$lc_sc_ids);
                                    $lc_sc_id=array_unique($lc_sc_id);
                                    foreach($lc_sc_id as $lcsc_id)
                                    {
                                        $export_number_rlz.=return_field_value("export_lc_no","com_export_lc","id=$lcsc_id and status_active=1","export_lc_no").",";
                                    }
                                    echo "Export L/C No."." - ".chop($export_number_rlz,",");
                                    ?>
                                    </b>
                                    </td>
                                </tr>
                                <?
                            }
                            if ($i%2==0)
                            $bgcolor="#E9F3FF";
                            else
                            $bgcolor="#FFFFFF";
                            ?>

                            <tr  bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                <td ><? echo $i;?></td>
                                <td width="120"><p><? echo $val[('invoice_no')];?></p></td>
                                <td width="140" ><? echo $val[('bank_ref_no')];?></td>
                                <td width="80" align="center"><? if($val[('bank_ref_date')]!='0000-00-00') echo change_date_format($val[('bank_ref_date')]); else echo "00-00-0000"; ?></td>
                                <td width="70" align="right">
                                <?
                                /*$id=$val[csf('inv_id')];
                                $inv_qty=return_field_value("sum(a.current_invoice_qnty) as current_invoice_qnty"," com_export_invoice_ship_dtls a, com_export_doc_submission_invo b ","a.mst_id=b.invoice_id and b.invoice_id in($id) and a.status_active=1","current_invoice_qnty");
                                echo $inv_qty;*/
                                $inv_qty=$val[('invoice_quantity')]; echo  $inv_qty;
                                ?>
                                </td>
                                <td width="85" align="right"><? echo number_format($val[('net_invo_value')],2); ?></td>
                                <td width="85" align="right"><? echo number_format($val[('sub_collection')],2); ?></td>
                                <td width="90" align="right"><? if($purchase_amt) echo number_format($val[('net_invo_value')],2); else echo "0.00";?></td>
                                <td width="90" align="right"><? echo number_format($purchase_amt,2);
                                /*$purchase_amt_sub_rlz=return_field_value("sum(b.lc_sc_curr) as dom_curr"," com_export_doc_sub_trans b","b.doc_submission_mst_id='".$val[csf('sub_id')]."'","dom_curr");
                                echo number_format($purchase_amt_sub_rlz,2);*/
                                //echo $submision_id;
                                ?>
                                </td>
                                <td width="50" align="right"><? echo number_format((($purchase_amt/$val[('net_invo_value')])*100),2)."%"; ?> </td>
                                <td width="80" align="center"><?  if($val[('negotiation_date')]!='0000-00-00') echo change_date_format($val[('negotiation_date')]); else echo "00-00-0000"; ?></td>
                                <td width="85" align="right"><? echo number_format($val[('distribute_realize')],2);?></td>
                                <td width="80" align="center"><? if($val[('rlz_received_date')]!='0000-00-00')  echo change_date_format($val[('rlz_received_date')]);  ?></td>
                                <td width="85" align="right"><? echo number_format($val[('deduct_realize')],2);?></td>
                                <td width='75' align='right'><?  echo number_format($val[('erq')],2);?></td>
                                <td width='75' align='right'><?  echo number_format($val[('btb')],2);?></td>
                                <td width='75' align='right'><?  echo number_format($val[('sun_ac')],2);?></td>
                                <td width='75' align='right'><?  echo number_format($val[('std')],2);?></td>
                                <td width='75' align='right'><?  echo number_format($val[('cd')],2);?></td>
                                <td width='75' align='right'><?  echo number_format($val[('cc')],2);?></td>
                                <td width='75' align='right'><?  echo number_format($val[('fdbc')],2);?></td>
                                <td  align="right" width="85" >
                                <?
                                    $document_adj = return_field_value("sum(document_currency)","com_export_proceed_rlzn_dtls a, com_export_proceed_realization b"," a.mst_id = b.id AND a.type = '1' AND b.is_invoice_bill = 1 AND b.invoice_bill_id='".$val[('sub_id')]."' AND a.account_head not in (5,6,10,15,65,81,11) ");
                                    echo "<a href='#report_detals' onclick= \"openmypage('$submision_id');\">".number_format($document_adj,2)."</a>";
                                ?></td>
                                <td  align="right" width="85" ><? $balen=($val[('net_invo_value')]-($val[('distribute_realize')]+$val[('deduct_realize')])); echo number_format($balen,2);  ?></td>
                            </tr>
                            <?
                            $lc_bill_qty+=$inv_qty;
                            $lc_bill_value +=$val[('net_invo_value')];
                            $lc_doc_col_value +=$val[('sub_collection')];
                            if($purchase_amt) $lc_bill_value_sub +=$val[('net_invo_value')];
                            $lc_doc_pur_value +=$purchase_amt;
                            $lc_doc_distribute_value +=$val[('distribute_realize')];
                            $lc_realized_deduct += $val[('deduct_realize')];
                            $lc_erq += $val[('erq')];
                            $lc_btb += $val[('btb')];
                            $lc_cd+= $val[('cd')];
                            $lc_cc += $val[('cc')];
                            $lc_sundry_ac+= $val[('sun_ac')];
                            $lc_std_spaecial += $val[('std')];
                            $lc_fdbc += $val[('fdbc')];
                            $lc_exp_adj += $document_adj;
                            $lc_short_ship_export += $document_adj;
                            $i++;
                        }
                        if(!empty($realize_lc_arr))
                        {
                            ?>
                            <tr align="right" bgcolor="#CCCCCC">
                                <td><p><strong></strong></p>&nbsp;</td>
                                <td><p><strong></strong></p>&nbsp;</td>
                                <td><p><strong></strong></p>&nbsp;</td>
                                <td><strong>Sub-Total</strong>&nbsp;</td>
                                <td><p><strong><? echo number_format($lc_bill_qty,0); $gt_lc_bill_qty+=$lc_bill_qty; ?></strong></p>&nbsp;</td>
                                <td><p><strong><? echo number_format($lc_bill_value,2); $gt_lc_bill_value+=$lc_bill_value; ?></strong></p>&nbsp;</td>
                                <td><p><strong><? echo number_format($lc_doc_col_value,2); $gt_lc_doc_col_value+=$lc_doc_col_value; ?></strong></p>&nbsp;</td>
                                <td><p><strong><? echo number_format($lc_bill_value_sub,2); $gt_sub_lc_bill_value+=$lc_bill_value_sub; ?></strong></p>&nbsp;</td>
                                <td><p><strong><? echo number_format($lc_doc_pur_value,2); $gt_lc_doc_pur_value+=$lc_doc_pur_value; ?></strong></p>&nbsp;</td>
                                <td><p><strong></strong></p></td>
                                <td><p><strong></strong></td>
                                <td><p><strong><? echo number_format($lc_doc_distribute_value,2); $gt_lc_doc_distribute_value+=$lc_doc_distribute_value;?></strong></p>&nbsp;</td>
                                <td></td>
                                <td><p><strong><? echo number_format($lc_realized_deduct,2); $gt_lc_realized_deduct+=$lc_realized_deduct;?></strong></p>&nbsp;</td>
                                <td><p><strong><? echo number_format($lc_erq,2); $gt_lc_erq+=$lc_erq; ?></strong></p>&nbsp;</td>
                                <td><p><strong><? echo number_format($lc_btb,2); $gt_lc_btb+=$lc_btb; ?> </strong></p>&nbsp;</td>
                                <td><p><strong><? echo number_format($lc_sundry_ac,2); $gt_lc_sundry_ac+=$lc_sundry_ac; ?></strong></p>&nbsp;</td>
                                <td><p><strong><? echo number_format($lc_std_spaecial,2); $gt_lc_std_spaecial+=$lc_std_spaecial; ?></strong></p>&nbsp;</td>
                                <td><p><strong><? echo number_format($lc_cd,2); $gt_lc_cd+=$lc_cd; ?></strong></p>&nbsp;</td>
                                <td><p><strong><? echo number_format($lc_cc,2); $gt_lc_cc+=$lc_cc; ?></strong></p>&nbsp;</td>
                                <td><p><strong><? echo number_format($lc_fdbc,2); $gt_lc_fdbc+=$lc_fdbc; ?></strong></p>&nbsp;</td>
                                <td><p><strong><? echo number_format($lc_exp_adj,2); $gt_lc_exp_adj+=$lc_exp_adj;?></strong></p>&nbsp;</td>
                                <td><p><strong><? //echo number_format($sc_exp_adj,2); $gt_sc_exp_adj+=$sc_exp_adj; ?></strong></p>&nbsp;</td>
                            </tr>
                            <?
                        }

                        $sc_num_chack=array();$j=1;$d=1;
                        //echo "<pre>";
                        //print_r($realize_sc_arr);echo "test";die;
                        foreach ($realize_sc_arr as $key=>$val)
                        {
                            $submision_id=$val[('sub_id')];
                            $purchase_amt_sc=return_field_value("sum(b.lc_sc_curr) as dom_curr"," com_export_doc_sub_trans b","b.doc_submission_mst_id in(".$val[("sub_id")].") ","dom_curr");

                            if(!in_array($val["lc_sc_id"],$sc_num_chack))
                            {
                                $sc_num_chack[]=$val["lc_sc_id"];
                                if($d!=1)
                                {
                                    ?>
                                    <tr align="right" bgcolor="#CCCCCC">
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td><b>Sub-Total</b>&nbsp;</td>
                                        <td><b><? echo number_format($sc_bill_qty,0); $gt_sc_bill_qty+=$sc_bill_qty; ?></b>&nbsp;</td>
                                        <td><b><? echo number_format($sc_bill_value,2); $gt_sc_bill_value+=$sc_bill_value; ?></b>&nbsp;</td>
                                        <td><b><? echo number_format($sc_doc_col_value,2); $gt_sc_doc_col_value+=$sc_doc_col_value; ?></b></td>
                                        <td><b><? echo number_format($sc_bill_value_sub,2); $gt_sub_sc_bill_value+=$sc_bill_value_sub; ?></b></td>
                                        <td><b><? echo number_format($sc_doc_pur_value,2); $gt_sc_doc_pur_value+=$sc_doc_pur_value;  ?></b></td>
                                        <td></td>
                                        <td></td>
                                        <td><b><? echo number_format($sc_doc_distribute_value,2); $gt_sc_doc_distribute_value+= $sc_doc_distribute_value; ?></b></td>
                                        <td></td>
                                        <td><b><? echo number_format($sc_realized_deduct,2); $gt_sc_realized_deduct+= $sc_realized_deduct; ?></b></td>
                                        <td><b><? echo number_format($sc_erq,2); $gt_sc_erq+=$sc_erq; ?></b></td>
                                        <td><b><? echo number_format($sc_btb,2); $gt_sc_btb+=$sc_btb; ?> </b></td>
                                        <td><b><? echo number_format($sc_sundry_ac,2); $gt_sc_sundry_ac+= $sc_sundry_ac; ?></b></td>
                                        <td><b><? echo number_format($sc_std_spaecial,2); $gt_sc_std_spaecial+= $sc_std_spaecial; ?></b></td>
                                        <td><b><? echo number_format($sc_cd,2); $gt_sc_cd+=$sc_cd; ?></b></td>
                                        <td><b><? echo number_format($sc_cc,2); $gt_sc_cc+= $sc_cc; ?></b></td>
                                        <td><b><? echo number_format($sc_fdbc,2); $gt_sc_fdbc+= $sc_fdbc; ?></b></td>
                                        <td><b><? echo number_format($sc_exp_adj,2); $gt_sc_exp_adj+=$sc_exp_adj; ?></b></td>
                                        <td><b><? //echo number_format($sc_exp_adj,2); $gt_sc_exp_adj+=$sc_exp_adj; ?></b></td>
                                    </tr>
                                    <?
                                }
                                $d++;
                                $sc_bill_qty =0;
                                $sc_bill_value =0;
                                $sc_doc_col_value =0;
                                $sc_doc_pur_value =0;
                                $sc_doc_distribute_value =0;
                                $sc_realized_deduct =0;
                                $sc_erq =0;
                                $sc_btb =0;
                                $sc_cd =0;
                                $sc_cc =0;
                                $sc_sundry_ac =0;
                                $sc_std_spaecial =0;
                                $sc_fdbc =0;
                                $sc_exp_adj =0;
                                $sc_short_ship_export =0;
                                ?>
                                <tr>
                                    <td colspan="23" style="background-color:#FDF4EF"><b>
                                    <?
                                    $salse_nubmer_rlz=return_field_value("contract_no","com_sales_contract","id='".$val[('lc_sc_id')]."' and status_active=1");
                                    echo "Export Salse Contact No."." - ".$salse_nubmer_rlz;
                                    ?></b></td>
                                </tr>
                                <?
                                //if($val[csf('bank_ref_no')]!= '0000-00-00') echo change_date_format($val[csf('bank_ref_no')]); else echo  '00-00-0000';
                            }

                            if ($i%2==0) $bgcolor="#E9F3FF";
                            else $bgcolor="#FFFFFF";
                            ?>
                            <tr  bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                <td ><? echo $j;?></td>
                                <td width="120" ><p><? echo $val[('invoice_no')];?></p></td>
                                <td width="140" ><? echo $val[('bank_ref_no')];?></td>
                                <td width="80" align="center"><? if($val[ 'bank_ref_date']!='0000-00-00') echo change_date_format($val[ 'bank_ref_date']); else echo "00-00-0000"; ?></td>
                                <td width="70" align="right">
                                <?
                                /*$id=$val[csf('inv_id')];
                                $inv_qty=return_field_value("sum(a.current_invoice_qnty) as current_invoice_qnty"," com_export_invoice_ship_dtls a, com_export_doc_submission_invo b ","a.mst_id=b.invoice_id and b.invoice_id in($id) and a.status_active=1","current_invoice_qnty");*/
                                $inv_qty=$val[('invoice_quantity')];
                                echo $inv_qty;
                                ?>
                                </td>
                                <td width="85" align="right"><? echo number_format($val[('net_invo_value')],2); ?></td>
                                <td width="85" align="right">
                                <?
                                    echo number_format($val[('sub_collection')],2);
                                ?>
                                </td>
                                <td width="90" align="right"><? if($purchase_amt_sc) echo number_format($val[('net_invo_value')],2); else echo "0.00";?></td>
                                <td width="90" align="right">
                                <?
                                echo number_format($purchase_amt_sc,2);
                                ?>
                                </td>
                                <td width="50" align="right"><? echo number_format((($purchase_amt_sc/$val[('net_invo_value')])*100),2)."%"; ?></td>
                                <td width="80" align="center"><? if($val["negotiation_date"]!='0000-00-00') echo change_date_format($val[('negotiation_date')]); else echo "00-00-0000"; ?></td>
                                <td width="85" align="right"><? echo number_format($val[('distribute_realize')],2);?></td>
                                <td width="80" align="center"><? if($val[('rlz_received_date')]!='0000-00-00')  echo change_date_format($val[('rlz_received_date')]); else echo "00-00-0000"; ?></td>
                                <td width="85" align="right"><? echo number_format($val[('deduct_realize')],2);?></td>
                                <td width='75' align='right'><?  echo number_format($val[('erq')],2);?></td>
                                <td width='75' align='right'><?  echo number_format($val[('btb')],2);?></td>
                                <td width='75' align='right'><?  echo number_format($val[('sun_ac')],2);?></td>
                                <td width='75' align='right'><?  echo number_format($val[('std')],2);?></td>
                                <td width='75' align='right'><?  echo number_format($val[('cd')],2);?></td>
                                <td width='75' align='right'><?  echo number_format($val[('cc')],2);?></td>
                                <td width='75' align='right'><?  echo number_format($val[('fdbc')],2);?></td>

                                <td  align="right"  width="85">
                                <?
                                    $document_adj = return_field_value("sum(document_currency)","com_export_proceed_rlzn_dtls a, com_export_proceed_realization b"," a.mst_id = b.id AND a.type = '1' AND b.is_invoice_bill = 1 AND b.invoice_bill_id='".$val[('sub_id')]."' AND a.account_head not in (5,6,10,15,65,81,11)");
                                    echo "<a href='#report_detals' onclick= \"openmypage('$submision_id');\">".number_format($document_adj,2)."</a>";
                                ?>
                                </td>
                                <td  align="right" width="85" ><? $balen_sc=($val[('net_invo_value')]-($val[('distribute_realize')]+$val[('deduct_realize')])); echo number_format($balen_sc,2);  ?></td>
                            </tr>
                            <?
                            $sc_bill_qty+=$inv_qty;
                            $sc_bill_value +=$val[('net_invo_value')];
                            $sc_doc_col_value +=$val[('sub_collection')];
                            if($purchase_amt_sc_sc) $sc_bill_value_sub +=$val[('net_invo_value')];
                            $sc_doc_pur_value +=$purchase_amt_sc;
                            $sc_doc_distribute_value +=$val[('distribute_realize')];
                            $sc_realized_deduct += $val[('deduct_realize')];
                            $sc_erq += $val[('erq')];
                            $sc_btb += $val[('btb')];
                            $sc_cd += $val[('cd')];
                            $sc_cc += $val[('cc')];
                            $sc_sundry_ac+= $val[('sun_ac')];
                            $sc_std_spaecial += $val[('std')];
                            $sc_fdbc += $val[('fdbc')];
                            $sc_exp_adj += $document_adj;
                            $sc_short_ship_export += $document_adj;
                            $i++;$j++;
                        }
                        if(!empty($realize_sc_arr))
                        {
                            ?>
                            <tr align="right" bgcolor="#CCCCCC">
                                <td></td>
                                <td></td>
                                <td></td>
                                <td><b>Sub-Total</b></td>
                                <td><b><? echo number_format($sc_bill_qty,0); $gt_sc_bill_qty+=$sc_bill_qty; ?></b></td>
                                <td><b><? echo number_format($sc_bill_value,2); $gt_sc_bill_value+=$sc_bill_value; ?></b></td>
                                <td><b><? echo number_format($sc_doc_col_value,2); $gt_sc_doc_col_value+=$sc_doc_col_value; ?></b></td>
                                <td><b><? echo number_format($sc_bill_value_sub,2); $gt_sub_sc_bill_value+=$sc_bill_value_sub; ?></b></td>
                                <td><b><? echo number_format($sc_doc_pur_value,2); $gt_sc_doc_pur_value+=$sc_doc_pur_value;  ?></b></td>
                                <td></td>
                                <td></td>
                                <td><b><? echo number_format($sc_doc_distribute_value,2); $gt_sc_doc_distribute_value+= $sc_doc_distribute_value; ?></b></td>
                                <td></td>
                                <td><b><? echo number_format($sc_realized_deduct,2); $gt_sc_realized_deduct+= $sc_realized_deduct; ?></b></td>
                                <td><b><? echo number_format($sc_erq,2); $gt_sc_erq+=$sc_erq; ?></b></td>
                                <td><b><? echo number_format($sc_btb,2); $gt_sc_btb+=$sc_btb; ?> </b></td>
                                <td><b><? echo number_format($sc_sundry_ac,2); $gt_sc_sundry_ac+= $sc_sundry_ac; ?></b></td>
                                <td><b><? echo number_format($sc_std_spaecial,2); $gt_sc_std_spaecial+= $sc_std_spaecial; ?></b></td>
                                <td><b><? echo number_format($sc_cd,2); $gt_sc_cd+=$sc_cd; ?></b></td>
                                <td><b><? echo number_format($sc_cc,2); $gt_sc_cc+= $sc_cc; ?></b></td>
                                <td><b><? echo number_format($sc_fdbc,2); $gt_sc_fdbc+= $sc_fdbc; ?></b></td>
                                <td><b><? echo number_format($sc_exp_adj,2); $gt_sc_exp_adj+=$sc_exp_adj; ?></b></td>
                                <td><b><? //echo number_format($sc_exp_adj,2); $gt_sc_exp_adj+=$sc_exp_adj; ?></b></td>
                            </tr>
                            <?
                        }
                        ?>
                    </tbody>
                </table>

                <table width="1900" rules="all" border="1" class="rpt_table" align="left" style="margin-bottom:20px;">

                    <tfoot>
                        <tr align="right">
                            <th colspan="4"><b>Grand Total</b></th>
                            <th width="70"><? echo number_format($gt_lc_bill_qty+$gt_sc_bill_qty,0);?></th>
                            <th width="85"><b><? echo number_format($gt_lc_bill_value+$gt_sc_bill_value,2);?></b></th>
                            <th width="85"><b><? echo number_format($gt_lc_doc_col_value+$gt_sc_doc_col_value,2);?></b></th>
                            <th width="90"><b><? echo number_format($gt_sub_lc_bill_value+$gt_sub_sc_bill_value,2);?></b></th>
                            <th width="90"><b><? echo number_format($gt_lc_doc_pur_value+$gt_sc_doc_pur_value,2);?></b></th>
                            <th width="50"></th>
                            <th width="80"></th>
                            <th width="85"><b><? echo number_format($gt_lc_doc_distribute_value+$gt_sc_doc_distribute_value,2);?></b></th>
                            <th width="80"></th>
                            <th width="85"><b><? echo number_format($gt_lc_realized_deduct+$gt_sc_realized_deduct,2);?></b></th>
                            <th width="75"><b><? echo number_format($gt_lc_erq+$gt_sc_erq,2);?></b></th>
                            <th width="75"><b><? echo number_format($gt_lc_btb+$gt_sc_btb,2);?> </b></th>
                            <th width="75"><b><? echo number_format($gt_lc_sundry_ac+$gt_sc_sundry_ac,2);?></b></th>
                            <th width="75"><b><? echo number_format($gt_lc_std_spaecial+$gt_sc_std_spaecial,2);?></b></th>
                            <th width="75"><b><? echo number_format($gt_lc_cd+$gt_sc_cd,2);?></b></th>
                            <th width="75"><b><? echo number_format($gt_lc_cc+$gt_sc_cc,2);?></b></th>
                            <th width="75"><b><? echo number_format($gt_lc_fdbc+$gt_sc_fdbc,2);?></b></th>
                            <th width="85"><b><? echo number_format($gt_lc_exp_adj+$gt_sc_exp_adj,2);?></b></th>
                            <th width="85"><b><? //echo number_format($gt_lc_exp_adj+$gt_sc_exp_adj,2);?></b></th>
                        </tr>
                    </tfoot>
                </table>

                <table width="1900" cellpadding="0" cellspacing="0" border="0" rules="all" id="" align="left">
                    <tr>
                    <td><strong style="font:Arial, Helvetica, sans-serif; font-size:16px;">Document Submitted Bank:</strong></td>
                    </tr>
                </table>

                <table width="1140" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all" id="" align="left">
                    <thead>
                        <tr>
                            <th  rowspan="2">Sl</th>
                            <th width="130" rowspan="2">Invoice No.</th>
                            <th width="150" rowspan="2">Export Bill No.</th>
                            <th width="80" rowspan="2">Bill Date</th>
                            <th width="80" rowspan="2">Submission Date</th>
                            <th width="100" rowspan="2">Inv/Bill Qty/Pcs</th>
                            <th width="100" rowspan="2">Bill Value</th>
                            <th width="100">Sub Under Collection</th>
                            <th colspan="4">Sub. Under Purchase</th>
                        </tr>
                        <tr>
                            <th>Amount</th>
                            <th width="105">Bill Amount</th>
                            <th width="105">Purchase Amount</th>
                            <th width="50">(%)</th>
                            <th width="90">Purchase Date</th>
                        </tr>
                    </thead>
                </table>

                <table width="1140" rules="all" class="rpt_table" align="left" id="" border="1">
                    <tbody>
                        <?
                        $sub_invoice=return_library_array("select id, invoice_no from com_export_invoice_ship_mst where is_lc=1 and lc_sc_id in($lc_Ids_arr) and status_active=1 and is_deleted=0 union all select id, invoice_no from com_export_invoice_ship_mst where is_lc=2 and lc_sc_id in($sc_Ids_arr) and status_active=1 and is_deleted=0","id","invoice_no");					
                        $sub_lc_num_chack=array();$k=1;$o=1;
                        foreach ($sub_lc_arr as $key=>$val)
                        {
                            if(!in_array($val["lc_sc_id"],$sub_lc_num_chack))
                            {
                                $sub_lc_num_chack[]=$val["lc_sc_id"];
                                if($o!=1)
                                {
                                    ?>
                                    <tr align="right" bgcolor="#CCCCCC">
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td><b>Sub-Total</b></td>
                                        <td><b><? echo number_format($sub_lc_bill_qty,0); $gt_sub_lc_bill_qty+=$sub_lc_bill_qty; ?></b></td>
                                        <td><b><? echo number_format($sub_lc_bill_value,2); $gt_submiss_lc_bill_value+=$sub_lc_bill_value; ?></b></td>
                                        <td><b><? echo number_format($sub_lc_doc_col_value,2); $gt_sub_lc_doc_col_value+=$sub_lc_doc_col_value; ?></b></td>
                                        <td><b><? echo number_format($sub_lc_bill_value_sub,2); $gtsub_sub_lc_bill_value+=$sub_lc_bill_value_sub; ?></b></td>
                                        <td><b><? echo number_format($sub_lc_doc_pur_value,2); $gt_sub_lc_doc_pur_value+=$sub_lc_doc_pur_value; ?></b></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <?
                                }
                                $o++;
                                $sub_lc_bill_qty =0;
                                $sub_lc_bill_value =0;
                                $sub_lc_doc_col_value =0;
                                $sub_lc_doc_pur_value =0;

                                ?>
                                <tr>
                                    <td colspan="12" style="background-color:#FDF4EF"><b>
                                    <?
                                    /*if($val[csf('is_lc')]==1) $export_number=return_field_value("export_lc_no","com_export_lc","id='".$val[('lc_sc_id')]."' and status_active=1");
                                    echo "Export L/C No."." - ".$export_number; */
                                    if($val[('is_lc')]==1)
                                    {
                                        $export_number='';
                                        $lc_sc_ids=$val[('lc_sc_id')];
                                        $export_number.=return_field_value("export_lc_no","com_export_lc","id=$lc_sc_ids and status_active=1","export_lc_no");
                                    }
                                    echo "Export L/C No."." - ".$export_number;
                                    ?>
                                    </b>
                                    </td>
                                </tr>
                                <?
                            }
                            if ($i%2==0) $bgcolor="#E9F3FF";
                            else $bgcolor="#FFFFFF";
                            ?>
                            <tr  bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                <td ><? echo $k;?></td>
                                <td width="130" ><p>
                                <? 
                                $inv_id_arr=array_unique(explode(",",$val[('inv_id')]));
                                $all_invoice_no="";
                                foreach($inv_id_arr as $inv_ids)
                                {
                                    $all_invoice_no.='<a href="##" onClick="fnc_invoice_show('.$inv_ids.')" >'.$sub_invoice[$inv_ids].'</a>,';
                                }
                                $all_invoice_no=chop($all_invoice_no,",");
                                echo $all_invoice_no;
                                ?></p></td>
                                <td width="150" ><? echo $val[('bank_ref_no')];?></td>
                                <td width="80" align="center"><? echo change_date_format($val[('bank_ref_date')]);?></td>
                                <td width="80" align="center"><? echo change_date_format($val[('submit_date')]);?></td>
                                <td width="100" align="right">
                                <?
                                /*$id=$val[csf('inv_id')];
                                $inv_qty=return_field_value("sum(a.current_invoice_qnty) as current_invoice_qnty"," com_export_invoice_ship_dtls a, com_export_doc_submission_invo b ","a.mst_id=b.invoice_id and b.invoice_id in($id) and a.status_active=1","current_invoice_qnty")*/
                                $inv_qty=$val[('invoice_quantity')];
                                echo $inv_qty;
                                $submision_id=$val[('sub_id')];
                                $purchase_amt_sub=return_field_value("sum(b.lc_sc_curr) as dom_curr"," com_export_doc_sub_trans b","b.doc_submission_mst_id='$submision_id' group by b.doc_submission_mst_id ","dom_curr");

                                ?>
                                </td>
                                <td width="100" align="right"><? echo number_format($val[('net_invo_value')],2); ?></td>
                                <td width="100" align="right"><? echo number_format($val[('sub_collection')],2);?></td>
                                <td width="105" align="right"><? if($purchase_amt_sub) echo number_format($val[('net_invo_value')],2); else echo "0.00";?></td>
                                <td width="105" align="right">
                                <?
                                echo number_format($purchase_amt_sub,2);
                                //echo $submision_id;
                                ?>
                                </td>
                                <td width="50" align="right"><? echo number_format((($purchase_amt_sub/$val[('net_invo_value')])*100),2)."%"; ?> </td>
                                <td width="90" align="center"><? if($val[('negotiation_date')]!='0000-00-00') echo change_date_format($val[('negotiation_date')]);?></td>
                            </tr>
                            <?
                            $sub_lc_bill_qty +=$inv_qty;
                            $sub_lc_bill_value +=$val[('net_invo_value')];
                            if($purchase_amt_sub) $sub_lc_bill_value_sub +=$val[('net_invo_value')];
                            $sub_lc_doc_col_value +=$val[('sub_collection')];
                            $sub_lc_doc_pur_value +=$purchase_amt_sub;
                            $i++;$k++;
                        }
                        if(!empty($sub_lc_arr))
                        {
                        ?>
                            <tr align="right" bgcolor="#CCCCCC">
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td><b>Sub-Total</b></td>
                                <td><b><? echo number_format($sub_lc_bill_qty,0); $gt_sub_lc_bill_qty+=$sub_lc_bill_qty; ?></b></td>
                                <td><b><? echo number_format($sub_lc_bill_value,2); $gt_submiss_lc_bill_value+=$sub_lc_bill_value; ?></b></td>
                                <td><b><? echo number_format($sub_lc_doc_col_value,2); $gt_sub_lc_doc_col_value+=$sub_lc_doc_col_value; ?></b></td>
                                <td><b><? echo number_format($sub_lc_bill_value_sub,2); $gtsub_sub_lc_bill_value+=$sub_lc_bill_value_sub; ?></b></td>
                                <td><b><? echo number_format($sub_lc_doc_pur_value,2); $gt_sub_lc_doc_pur_value+=$sub_lc_doc_pur_value; ?></b></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <?
                            //$submision_id=0;
                        }

                        //echo "press <pre>";print_r($sub_sc_arr);
                        $sub_sc_num_chack=array();$m=1;$p=1;
                        foreach ($sub_sc_arr as $key=>$val)
                        {
                            if(!in_array($val["lc_sc_id"],$sub_sc_num_chack))
                            {
                                $sub_sc_num_chack[]=$val["lc_sc_id"];
                                if($p!=1)
                                {
                                    ?>
                                    <tr align="right" bgcolor="#CCCCCC">
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td><b>Sub-Total</b></td>
                                        <td><b><? echo number_format($sub_sc_bill_qty,0); $gt_sub_sc_bill_qty+=$sub_sc_bill_qty; ?></b></td>
                                        <td><b><? echo number_format($sub_sc_bill_value,2); $gt_submiss_sc_bill_value+=$sub_sc_bill_value; ?></b></td>
                                        <td><b><? echo number_format($sub_sc_doc_col_value,2); $gt_sub_sc_doc_col_value+=$sub_sc_doc_col_value; ?></b></td>
                                        <td><b><? echo number_format($sub_sc_bill_value,2); $gtsub_sub_sc_bill_value+=$sub_sc_bill_value; ?></b></td>
                                        <td><b><? echo number_format($sub_sc_doc_pur_value,2); $gt_sub_sc_doc_pur_value+=$sub_sc_doc_pur_value; ?></b></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <?
                                }
                                $p++;
                                $sub_sc_bill_qty =0;
                                $sub_sc_bill_value =0;
                                $sub_sc_doc_col_value =0;
                                $sub_sc_doc_pur_value =0;
                                ?>
                                <tr>
                                    <td colspan="12" style="background-color:#FDF4EF"><b>
                                    <?
                                    /*if($val[('is_lc')]==2) $export_number=return_field_value("contract_no","com_sales_contract","id='".$val[('lc_sc_id')]."' and status_active=1");
                                    echo "Export Salse Contact No."." - ".$export_number;*/
                                    $lc_sc_ids=$val[('lc_sc_id')];
                                    if($val[('is_lc')]==2)
                                    {
                                        $export_number='';
                                        $lc_sc_id=explode(",",$lc_sc_ids);
                                        $lc_sc_id=array_unique($lc_sc_id);
                                        foreach($lc_sc_id as $lcsc_id)
                                        {
                                            //$export_number.=return_field_value("export_lc_no","com_export_lc","id=$lcsc_id and status_active=1","export_lc_no").",";
                                            $export_number=return_field_value("contract_no","com_sales_contract","id=$lcsc_id and status_active=1","contract_no");
                                        }
                                    }
                                    echo "Export Salse Contact No."." - ".chop($export_number,",");
                                    ?></b></td>
                                </tr>
                                <?
                            }
                            if ($i%2==0) $bgcolor="#E9F3FF";
                            else $bgcolor="#FFFFFF";
                            ?>
                                <tr  bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                <td ><? echo $m;?></td>
                                <td width="130" align="center"><p>
                                <? 
                                $inv_id_arr=array_unique(explode(",",$val[('inv_id')]));
                                $all_invoice_no="";
                                foreach($inv_id_arr as $inv_ids)
                                {
                                    $all_invoice_no.='<a href="##" onClick="fnc_invoice_show('.$inv_ids.')" >'.$sub_invoice[$inv_ids].'</a>,';
                                }
                                $all_invoice_no=chop($all_invoice_no,",");
                                echo $all_invoice_no;
                                //echo $val[('invoice_no')];
                                ?></p></td>
                                <td width="150" align="center"><? echo $val[('bank_ref_no')];?></td>
                                <td width="80" align="center"><? echo change_date_format($val[('bank_ref_date')]);?></td>
                                <td width="80" align="center"><? echo change_date_format($val[('submit_date')]);?></td>
                                <td width="100" align="right">
                                <?
                                /*$id=$val[csf('inv_id')];
                                $inv_qty=return_field_value("sum(a.current_invoice_qnty) as current_invoice_qnty"," com_export_invoice_ship_dtls a, com_export_doc_submission_invo b ","a.mst_id=b.invoice_id and b.invoice_id in($id) and a.status_active=1","current_invoice_qnty");*/
                                $inv_qty=$val[('invoice_quantity')];
                                echo $inv_qty;
                                $submision_id=$val[('sub_id')];
                                $purchase_amt=return_field_value("sum(b.lc_sc_curr) as dom_curr"," com_export_doc_sub_trans b","b.doc_submission_mst_id='$submision_id' ","dom_curr");
                                ?>
                                </td>
                                <td width="100" align="right"><? echo number_format($val[('net_invo_value')],2); ?></td>
                                <td width="100" align="right"><? echo number_format($val[('sub_collection')],2);?></td>
                                <td width="105" align="right"><? if($purchase_amt) echo number_format($val[('net_invo_value')],2); else echo "0.00";?></td>
                                <td width="105" align="right">
                                <?
                                echo number_format($purchase_amt,2);
                                ?>
                                </td>
                                <td width="50" align="right"><? echo number_format((($purchase_amt/$val[('net_invo_value')])*100),2)."%"; ?></td>
                                <td width="90" align="center"><? echo change_date_format($val[('negotiation_date')]);?></td>
                            </tr>
                            <?
                            $sub_sc_bill_qty +=$inv_qty;
                            $sub_sc_bill_value +=$val[('net_invo_value')];
                            $sub_sc_doc_col_value +=$val[('sub_collection')];
                            $sub_sc_doc_pur_value +$val[('purchase_amount')];
                            $i++;$m++;
                        }
                        if(!empty($sub_sc_arr))
                        {
                            ?>
                            <tr align="right" bgcolor="#CCCCCC">
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td><b>Sub-Total</b></td>
                                <td><b><? echo number_format($sub_sc_bill_qty,0); $gt_sub_sc_bill_qty+=$sub_sc_bill_qty; ?></b></td>
                                <td><b><? echo number_format($sub_sc_bill_value,2); $gt_submiss_sc_bill_value+=$sub_sc_bill_value; ?></b></td>
                                <td><b><? echo number_format($sub_sc_doc_col_value,2); $gt_sub_sc_doc_col_value+=$sub_sc_doc_col_value; ?></b></td>
                                <td><b><? echo number_format($sub_sc_bill_value,2); $gtsub_sub_sc_bill_value+=$sub_sc_bill_value; ?></b></td>
                                <td><b><? echo number_format($sub_sc_doc_pur_value,2); $gt_sub_sc_doc_pur_value+=$sub_sc_doc_pur_value; ?></b></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <?
                        }
                        ?>
                    </tbody>
                </table>

                <table width="1140" rules="all" border="1" class="rpt_table" align="left" style="margin-bottom:20px;">
                    <tfoot>
                        <tr align="right">
                            <th colspan="5"><b>Grand Total</b></th>
                            <th width="100"><? echo number_format($gt_sub_lc_bill_qty+$gt_sub_sc_bill_qty,0);?></th>
                            <th width="100"><b><? echo number_format($gt_submiss_lc_bill_value+$gt_submiss_sc_bill_value,2);?></b></th>
                            <th width="100"><b><? echo number_format($gt_sub_lc_doc_col_value+$gt_sub_sc_doc_col_value,2);?></b></th>
                            <th width="105"><b><? echo number_format($gtsub_sub_lc_bill_value+$gtsub_sub_sc_bill_value,2);?></b></th>
                            <th width="105"><b><? echo number_format($gt_sub_lc_doc_pur_value+$gt_sub_sc_doc_pur_value,2);?></b></th>
                            <th width="50"></th>
                            <th width="90"></th>
                        </tr>
                    </tfoot>
                </table>

                <table width="1900" cellpadding="0" cellspacing="0" border="0" rules="all" id="" align="left">
                    <tr>
                    <td><strong style="font:Arial, Helvetica, sans-serif; font-size:16px;">Document Submitted Buyer:</strong></td>
                    </tr>
                </table>

                <table width="1070" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all" id="" align="left">
                    <thead>
                        <tr>
                            <th width="50">Sl</th>
                            <th width="150">Buyer</th>
                            <th width="400">Invoice No.</th>
                            <th width="100">System Id.</th>
                            <th width="100">Submit Date</th>
                            <th width="100">Submit Qty</th>
                            <th >Submit Value</th>
                        </tr>
                    </thead>
                </table>

                <table width="1070" rules="all" class="rpt_table" align="left" id="" border="1">
                    <tbody>
                        <?
                        /*$sub_lc_num_chack=array();$k=1;$o=1;
                        foreach ($sub_buyer_arr as $key=>$val)
                        {
                            if(!in_array($val["lc_sc_id"],$sub_lc_num_chack))
                            {
                                $sub_lc_num_chack[]=$val["lc_sc_id"];
                                if($o!=1)
                                {
                                    ?>
                                    <tr align="right" bgcolor="#CCCCCC">
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td><b>Sub-Total</b></td>
                                        <td><b><? echo number_format($sub_lc_bill_qty,0); $gt_sub_lc_bill_qty+=$sub_lc_bill_qty; ?></b></td>
                                        <td><b><? echo number_format($sub_lc_bill_value,2); $gt_submiss_lc_bill_value+=$sub_lc_bill_value; ?></b></td>
                                        <td><b><? echo number_format($sub_lc_doc_col_value,2); $gt_sub_lc_doc_col_value+=$sub_lc_doc_col_value; ?></b></td>
                                        <td><b><? echo number_format($sub_lc_bill_value_sub,2); $gtsub_sub_lc_bill_value+=$sub_lc_bill_value_sub; ?></b></td>
                                        <td><b><? echo number_format($sub_lc_doc_pur_value,2); $gt_sub_lc_doc_pur_value+=$sub_lc_doc_pur_value; ?></b></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <?
                                }
                                $o++;
                                $sub_lc_bill_qty =0;
                                $sub_lc_bill_value =0;
                                $sub_lc_doc_col_value =0;
                                $sub_lc_doc_pur_value =0;

                                ?>
                                <tr>
                                    <td colspan="11" style="background-color:#FDF4EF"><b>
                                    <?
                                    if($val[csf('is_lc')]==1) $export_number=return_field_value("export_lc_no","com_export_lc","id='".$val[('lc_sc_id')]."' and status_active=1");
                                    echo "Export L/C No."." - ".$export_number;
                                    ?>
                                    </b>
                                    </td>
                                </tr>
                                <?

                            }
                            if ($i%2==0)
                            $bgcolor="#E9F3FF";
                            else
                            $bgcolor="#FFFFFF";
                            ?>

                            <tr  bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                <td ><? echo $k;?></td>
                                <td width="130" ><p><? echo $val[('invoice_no')];?></p></td>
                                <td width="150" ><? echo $val[('bank_ref_no')];?></td>
                                <td width="90" align="center"><? echo change_date_format($val[('bank_ref_date')]);?></td>
                                <td width="100" align="right">
                                <?
                                /*$id=$val[csf('inv_id')];
                                $inv_qty=return_field_value("sum(a.current_invoice_qnty) as current_invoice_qnty"," com_export_invoice_ship_dtls a, com_export_doc_submission_invo b ","a.mst_id=b.invoice_id and b.invoice_id in($id) and a.status_active=1","current_invoice_qnty")
                                $inv_qty=$val[('invoice_quantity')];
                                echo $inv_qty;
                                $submision_id=$val[('sub_id')];
                                $purchase_amt_sub=return_field_value("sum(b.lc_sc_curr) as dom_curr"," com_export_doc_sub_trans b","b.doc_submission_mst_id='$submision_id' group by b.doc_submission_mst_id ","dom_curr");

                                ?>
                                </td>
                                <td width="100" align="right"><? echo number_format($val[('net_invo_value')],2); ?></td>
                                <td width="100" align="right"><? echo number_format($val[('sub_collection')],2);?></td>
                                <td width="105" align="right"><? if($purchase_amt_sub) echo number_format($val[('net_invo_value')],2); else echo "0.00";?></td>
                                <td width="105" align="right">
                                <?
                                echo number_format($purchase_amt_sub,2);
                                //echo $submision_id;
                                ?>
                                </td>
                                <td width="50" align="right"><? echo number_format((($purchase_amt_sub/$val[('net_invo_value')])*100),2)."%"; ?> </td>
                                <td width="90" align="center"><? if($val[('negotiation_date')]!='0000-00-00') echo change_date_format($val[('negotiation_date')]);?></td>

                            </tr>
                            <?
                            $sub_lc_bill_qty +=$inv_qty;
                            $sub_lc_bill_value +=$val[('net_invo_value')];
                            if($purchase_amt_sub) $sub_lc_bill_value_sub +=$val[('net_invo_value')];
                            $sub_lc_doc_col_value +=$val[('sub_collection')];
                            $sub_lc_doc_pur_value +=$purchase_amt_sub;
                            $i++;$k++;
                        }
                        if(!empty($sub_lc_arr))
                        {
                            ?>
                            <tr align="right" bgcolor="#CCCCCC">
                                <td></td>
                                <td></td>
                                <td></td>
                                <td><b>Sub-Total</b></td>
                                <td><b><? echo number_format($sub_lc_bill_qty,0); $gt_sub_lc_bill_qty+=$sub_lc_bill_qty; ?></b></td>
                                <td><b><? echo number_format($sub_lc_bill_value,2); $gt_submiss_lc_bill_value+=$sub_lc_bill_value; ?></b></td>
                                <td><b><? echo number_format($sub_lc_doc_col_value,2); $gt_sub_lc_doc_col_value+=$sub_lc_doc_col_value; ?></b></td>
                                <td><b><? echo number_format($sub_lc_bill_value_sub,2); $gtsub_sub_lc_bill_value+=$sub_lc_bill_value_sub; ?></b></td>
                                <td><b><? echo number_format($sub_lc_doc_pur_value,2); $gt_sub_lc_doc_pur_value+=$sub_lc_doc_pur_value; ?></b></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <?
                            //$submision_id=0;
                        }*/

                        $sub_sc_num_chack=array();$m=1;$p=1;
                        foreach ($sub_buyer_arr as $key=>$val)
                        {
                            if(!in_array($val["lc_sc_id"],$sub_sc_num_chack))
                            {
                                $sub_sc_num_chack[]=$val["lc_sc_id"];
                                if($p!=1)
                                {
                                    ?>
                                    <tr align="right" bgcolor="#CCCCCC">
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td><b>Sub-Total</b></td>
                                        <td><b><? echo number_format($submit_qty,0); $gt_submit_qty+=$submit_qty; ?></b></td>
                                        <td><b><? echo number_format($submit_value,2); $gt_submit_value+=$submit_value; ?></b></td>
                                    </tr>
                                    <?
                                }
                                $p++;
                                $submit_qty =$submit_value =0;
                                ?>
                                <tr>
                                    <td colspan="7" style="background-color:#FDF4EF"><b>
                                    <?
                                    if($val[('is_lc')]==2) $export_number=return_field_value("contract_no","com_sales_contract","id in(".$val[('lc_sc_id')].") and status_active=1");
                                    echo "Export Salse Contact No."." - ".$export_number;
                                    ?></b></td>
                                </tr>
                                <?
                            }
                            if ($i%2==0)
                            $bgcolor="#E9F3FF";
                            else
                            $bgcolor="#FFFFFF";
                            ?>                            
                            <tr  bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                <td width="50" align="center"><? echo $m;?></td>
                                <td width="150" ><? echo $buyer_name_arr[$val[('buyer_id')]];?></td>
                                <td width="400" ><p>
                                <? 
                                $inv_id_arr=array_unique(explode(",",$val[('inv_id')]));
                                $all_invoice_no="";
                                foreach($inv_id_arr as $inv_ids)
                                {
                                    $all_invoice_no.='<a href="##" onClick="fnc_invoice_show('.$inv_ids.')" >'.$sub_invoice[$inv_ids].'</a>,';
                                }
                                $all_invoice_no=chop($all_invoice_no,",");
                                echo $all_invoice_no;
                                //echo $val[('invoice_no')];
                                ?></p></td>
                                <td width="100" align="center"><? echo $val[('sub_id')];?></td>
                                <td width="100" align="center"><? echo change_date_format($val[('bank_ref_date')]);?></td>
                                <td width="100" align="right"><? echo  number_format($val[('invoice_quantity')],0);?> </td>
                                <td align="right"><? echo number_format($val[('net_invo_value')],2); ?></td>
                            </tr>
                            <?
                            $submit_qty +=$val[('invoice_quantity')];
                            $submit_value +=$val[('net_invo_value')];
                            $i++;$m++;
                        }
                        if(!empty($sub_buyer_arr))
                        {
                            ?>
                            <tr align="right" bgcolor="#CCCCCC">
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td><b>Sub-Total</b></td>
                                <td><b><? echo number_format($submit_qty,0); $gt_submit_qty+=$submit_qty; ?></b></td>
                                <td><b><? echo number_format($submit_value,2); $gt_submit_value+=$submit_value; ?></b></td>
                            </tr>
                            <?
                        }
                        ?>
                    </tbody>
                </table>

                <table width="1070" rules="all" border="1" class="rpt_table" align="left" style="margin-bottom:20px;">
                    <tfoot>
                        <tr align="right">
                            <th width="50"></th>
                            <th width="150"></th>
                            <th width="400"></th>
                            <th width="100"></th>
                            <th width="100"><b>Grand Total</b></th>
                            <th width="100"><? echo number_format($gt_submit_qty,0);?></th>
                            <th ><b><? echo number_format($gt_submit_value,2);?></b></th>
                        </tr>
                    </tfoot>
                </table>

                <table border="0" width="1900"><tr>&nbsp;</tr></table><br>

                <table width="1900" cellpadding="0" cellspacing="0" border="0" rules="all" id="" align="left">
                    <tr>
                    <td><strong style="font:Arial, Helvetica, sans-serif; font-size:16px;">Un-Submitted Invoice:</strong></td>
                    </tr>
                </table>

                <table width="750" cellpadding="0" cellspacing="0" border="1" rules="all" align="left" class="rpt_table">
                    <thead>
                        <tr>
                            <th width="50">SL</td>
                            <th width="170">LC/SC No</td>
                            <th width="180">Invoice No</td>
                            <th width="90">Invoice Date</td>
                            <th width="100">Invoice Quantity</td>
                            <th>Invoice Value</td>
                        </tr>
                    </thead>
                    <tbody>
                    <?
                    $lc_no_arr=return_library_array( "select id,export_lc_no from com_export_lc where status_active=1",'id','export_lc_no');
                    $sc_no_arr=return_library_array( "select id,contract_no from com_sales_contract where status_active=1",'id','contract_no');

                    //var_dump($sql);
                    $k=1;
                    foreach($inv_arr as $inv_id=>$row)
                    {
                        if ($i%2==0) $bgcolor="#E9F3FF";
                        else $bgcolor="#FFFFFF";
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td align="center"><? echo $k;?></td>
                            <td><? if($row[('is_lc')]==1) echo $lc_no_arr[$row[('lc_sc_id')]]; else echo  $sc_no_arr[$row[('lc_sc_id')]]?></td>
                            <td><a href="##" onClick="fnc_invoice_show('<? echo $inv_id;?>')"><? echo $row[('invoice_no')]?></a></td>
                            <td align="center"><? echo change_date_format($row[('invoice_date')]);?></td>
                            <td align="right"><? echo number_format($row[('invoice_quantity')],2);?></td>
                            <td align="right"><? echo number_format($row[('invoice_value')],2);?></td>
                        </tr>
                        <?
                        $total_unsubmit+=$row[('invoice_value')];
                        $total_invoice_quantity+=$row[('invoice_quantity')];
                        $k++;$i++;
                    }
                    ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4" align="right">Total</td>
                            <th  align="right"><? echo number_format($total_invoice_quantity,2);?></td>
                            <th  align="right"><? echo number_format($total_unsubmit,2);?></td>
                        </tr>
                    </tfoot>
                </table>

                <table border="0" width="1900"><tr>&nbsp;</tr></table><br>

                <table width="1900" cellpadding="0" cellspacing="0" border="0" rules="all" id="" align="left">
                    <tr>
                    <td><strong style="font:Arial, Helvetica, sans-serif; font-size:16px;">Discount/Adjustment Details:</strong></td>
                    </tr>
                </table>

                <table width="750" cellpadding="0" cellspacing="0" border="1" rules="all" align="left" class="rpt_table">
                    <thead>
                        <tr>
                            <th width="30">Sl</td>
                            <th width="140">LC/SC No</td>
                            <th width="140">Invoice No</td>
                            <th width="110">Invoice Quantity</td>
                            <th width="110">Gross Value</td>
                            <th width="110">Adjustment</td>
                            <th >Net Value</td>
                        </tr>
                    </thead>
                    <tbody>
                    <?
                    //$lc_no_arr=return_library_array( "select id,export_lc_no from com_export_lc where status_active=1",'id','export_lc_no');
                    //$sc_no_arr=return_library_array( "select id,contract_no from com_sales_contract where status_active=1",'id','contract_no');

                    //var_dump($sql);
                    $p=1;
                    foreach($adjustment_arr as $inv_id=>$row)
                    {
                        if ($i%2==0)
                        $bgcolor="#E9F3FF";
                        else
                        $bgcolor="#FFFFFF";
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td align="center"><? echo $p;?></td>
                            <td><? echo $row['lc_sc_no']?></td>
                            <td><a href="##" onClick="fnc_invoice_show('<? echo $inv_id;?>')"><? echo $row['invoice_no']?></a></td>
                            <td align="right"><?  echo number_format($row['current_invoice_qnty'],2); $to_current_invoice_qnty+=$row['current_invoice_qnty'];?></td>
                            <td align="right"><? echo number_format($row['current_invoice_value'],2); $to_adjst_gross+=$row['current_invoice_value'];?></td>
                            <td align="right"><?  $adjust_amt=$row['current_invoice_value']-$row['net_invo_value']; echo number_format($adjust_amt,2); $to_adjust+=$adjust_amt;?></td>
                            <td align="right"><? echo number_format($row['net_invo_value'],2); $to_adjust_net+=$row['net_invo_value'];?></td>
                        </tr>
                        <?
                        $i++;$p++;
                    }
                    ?>
                    </tbody>
                    <tfoot>
                        <tr >
                            <th colspan="3"><strong>Total</strong></td>
                            <td align="right"><? echo number_format($to_current_invoice_qnty,2)?></td>
                            <td align="right"><? echo number_format($to_adjst_gross,2)?></td>
                            <td align="right"><? echo number_format($to_adjust,2)?></td>
                            <td align="right"><? echo number_format($to_adjust_net,2)?></td>
                        </tr>
                    </tfoot>
                </table>

                <table border="0" width="1900"><tr>&nbsp;</tr></table><br>

                <table width="1900" cellpadding="0" cellspacing="0" border="0" rules="all" id="" align="left">
                    <tr>
                    <td><strong style="font:Arial, Helvetica, sans-serif; font-size:16px;">Import Status :</strong></td>
                    </tr>
                </table>
                <table width="1450" cellpadding="0" cellspacing="0" border="1" rules="all" align="left" class="rpt_table">
                    <thead>
                        <tr>
                            <th width="50">Sl</td>
                            <th width="120">BTB LC NO</td>
                            <th width="80">LC Date</td>
                            <th width="100">LC Value</td>
                            <th width="80">Ship Date</td>
                            <th width="100">Pay Term</td>
                            <th width="120">Invoice NO</td>
                            <th width="80">Invoice Date</td>
                            <th width="100">Invoice Value</td>
                            <th width="80">Maturaty Date</td>
                            <th width="100">Paid Amount</td>
                            <th width="80">Paid Date</td>
                            <th width="120">Supplier Name</td>
                            <th width="120">Item Category</td>
                            <th >PI Qty. (Kg)</td>
                        </tr>
                    </thead>

                    <tbody>

                    <?
                    //$lc_no_arr=return_library_array( "select id,export_lc_no from com_export_lc where status_active=1",'id','export_lc_no');
                    //$sc_no_arr=return_library_array( "select id,contract_no from com_sales_contract where status_active=1",'id','contract_no');

                    //var_dump($sql);
                    $lc_wise_value=array();
                    $all_btb_id=$btb_mst_lc_id.",".$btb_mst_sc_id;
                    $all_btb_id=implode(",",array_unique(explode(",",$all_btb_id)));
                    //echo $all_btb_id;
                    $btb_inv_sql="select a.id as btb_id, a.lc_number, a.lc_date, a.lc_value, a.last_shipment_date, a.payterm_id, a.supplier_id, a.item_category_id, sum(c.current_acceptance_value) as invoice_value, d.id as invoice_id, d.invoice_no, d.invoice_date, d.maturity_date, 1 as type
                    from com_btb_lc_master_details a, com_import_invoice_dtls c, com_import_invoice_mst d
                    where a.id=c.btb_lc_id and c.import_invoice_id=d.id and c.current_acceptance_value>0 and a.payterm_id<>3 and a.id in($all_btb_id) and a.is_deleted=0 and a.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1
                    group by a.id, a.lc_number, a.lc_date, a.lc_value, a.last_shipment_date, a.payterm_id, a.supplier_id, a.item_category_id, d.id, d.invoice_no, d.invoice_date, d.maturity_date

                    union all
                    select a.id as btb_id, a.lc_number, a.lc_date, a.lc_value, a.last_shipment_date, a.payterm_id, a.supplier_id, a.item_category_id, null as invoice_value, null as invoice_id, null as invoice_no, null as invoice_date, null as maturity_date, 2 as type
                    from com_btb_lc_master_details a, com_btb_export_lc_attachment b
                    where a.id=b.import_mst_id and a.payterm_id=3 and a.id in($all_btb_id) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";

                    // echo $btb_inv_sql;

                    $btb_inv_result=sql_select($btb_inv_sql);
                    $all_import_invoice_id=array();
                    $all_pi_id="";
                    $btb_id=$pi_category=array();
                    foreach($btb_inv_result as $row)
                    {
                        if($row[csf("invoice_id")]!="") $all_import_invoice_id[$row[csf("invoice_id")]]=$row[csf("invoice_id")];
                        $btb_id[$row[csf("btb_id")]]=$row[csf("btb_id")];
                    }
                    $btb_id=where_con_using_array($btb_id,0,'a.com_btb_lc_master_details_id');
                    $pi_sql=sql_select("SELECT a.com_btb_lc_master_details_id as BTB_ID, b.item_category_id as ITEM_CATEGORY_ID from com_btb_lc_pi a, com_pi_master_details b where a.pi_id=b.id and a.status_active=1 and b.status_active=1");
                    foreach($pi_sql as $row)
                    {
                        $pi_category[$row["BTB_ID"]].=$item_category[$row["ITEM_CATEGORY_ID"]].',';
                    }


                    if(count($all_import_invoice_id)>0)
                    {
                        $pi_sql=sql_select("select a.import_invoice_id, b.quantity from com_import_invoice_dtls a, com_pi_item_details b where a.pi_id=b.pi_id and b.status_active=1 and b.is_deleted=0 and a.import_invoice_id in(".implode(",",$all_import_invoice_id).")");
                        $pi_data=array();
                        foreach($pi_sql as $row)
                        {
                            $pi_data[$row[csf("import_invoice_id")]]=$row[csf("quantity")];
                        }
                        $paid2=return_field_value("sum(accepted_ammount) as paid"," com_import_payment","lc_id in($usance_paid_lc_id) and status_active='1'","paid");

                        /*echo "select id, invoice_id, accepted_ammount from com_import_payment where lc_id in($usance_paid_lc_id) and status_active='1' <br>
                        select id, invoice_id, payment_date, accepted_ammount from com_import_payment where invoice_id in(".implode(",",$all_import_invoice_id).") and status_active=1 and is_deleted=0";*/

                        $payment_sql=sql_select("select id, invoice_id, payment_date, accepted_ammount from com_import_payment where invoice_id in(".implode(",",$all_import_invoice_id).") and status_active=1 and is_deleted=0");
                        $payment_data=array();
                        foreach($payment_sql as $row)
                        {
                            $payment_data[$row[csf("invoice_id")]]["payment_date"]=$row[csf("payment_date")];
                            $payment_data[$row[csf("invoice_id")]]["accepted_ammount"]+=$row[csf("accepted_ammount")];
                        }
                    }

                    $p=1;
                    foreach($btb_inv_result as $row)
                    {
                        if ($i%2==0)
                        $bgcolor="#E9F3FF";
                        else
                        $bgcolor="#FFFFFF";
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td align="center"><? echo $p;?></td>
                            <td><p><? echo $row[csf('lc_number')];?>&nbsp;</p></td>
                            <td align="center"><? if($row[csf('lc_date')]!="" && $row[csf('lc_date')]!="0000-00-00") echo change_date_format($row[csf('lc_date')]);?></td>
                            <td align="right">
                                <?
                                    echo number_format($row[csf('lc_value')],2);
                                    //$tot_lc_value+=$row[csf('lc_value')];
                                    $lc_wise_value[$row[csf('lc_number')]]=$row[csf('lc_value')];
                                ?>
                            </td>
                            <td align="center"><?  if($row[csf('last_shipment_date')]!="" && $row[csf('last_shipment_date')]!="0000-00-00") echo change_date_format($row[csf('last_shipment_date')]); ?></td>
                            <td><p><? echo $pay_term[$row[csf('payterm_id')]];?>&nbsp;</p></td>
                            <td><p><? echo $row[csf('invoice_no')];?>&nbsp;</p></td>
                            <td align="center"><? if($row[csf('invoice_date')]!="" && $row[csf('invoice_date')]!="0000-00-00") echo change_date_format($row[csf('invoice_date')]);?></td>
                            <td align="right"><? echo number_format($row[csf('invoice_value')],2); $tot_invoice_value+=$row[csf('invoice_value')];?></td>
                            <td align="center"><? if($row[csf('maturity_date')]!="" && $row[csf('maturity_date')]!="0000-00-00") echo change_date_format($row[csf('maturity_date')]);?></td>
                            <td align="right" title="<? echo $row[csf("type")]; ?>">
                            <?
                            if($row[csf("type")]==1)
                            {
                                echo  number_format($payment_data[$row[csf("invoice_id")]]["accepted_ammount"],2);
                                if($inv_check[$row[csf("invoice_id")]]=="")
                                {
                                    $inv_check[$row[csf("invoice_id")]]=$row[csf("invoice_id")];
                                    $tot_payment_amt+=$payment_data[$row[csf("invoice_id")]]["accepted_ammount"];
                                }
                            }
                            else
                            {
                                echo  number_format($row[csf("lc_value")],2);
                                $tot_payment_amt+=$row[csf("lc_value")];
                            }

                            ?></td>
                            <td align="center"><? if($payment_data[$row[csf("invoice_id")]]["payment_date"]!="" && $payment_data[$row[csf("invoice_id")]]["payment_date"]!="0000-00-00") echo change_date_format($payment_data[$row[csf("invoice_id")]]["payment_date"]);?></td>
                            <td><p><? echo $suplier_name_arr[$row[csf('supplier_id')]];?>&nbsp;</p></td>
                            <td><p><? echo implode(", ",array_unique(explode(",",chop($pi_category[$row["BTB_ID"]],','))));?>&nbsp;</p></td>
                            <td align="right">
                            <?
                            if($row[csf('item_category_id')]==1)
                            {
                                echo number_format($pi_data[$row[csf("invoice_id")]],2);
                                $tot_pi_qnty+=$pi_data[$row[csf("invoice_id")]];
                            }
                            ?></td>
                        </tr>
                        <?
                        $i++;$p++;
                    }
                    ?>
                    </tbody>
                    <tfoot>
                        <tr >
                            <!-- <th  align="right"><? //echo number_format($tot_lc_value,2);?></td> -->
                            <!-- <th colspan="8" align="right">Total:</td> -->
                            <th colspan="3" align="right">Total:</td>
                            <th align="right"><? echo number_format(array_sum($lc_wise_value),2); //number_format($tot_lc_value,2);?></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <th align="right"><? echo number_format($tot_invoice_value,2);?></td>
                            <td>&nbsp;</td>
                            <th align="right"><? echo number_format($tot_payment_amt,2);?></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <th align="right"><? if($row[csf('item_category_id')]==1) echo number_format($tot_pi_qnty,2);?></td>
                        </tr>
                    </tfoot>
                </table>
            </fieldset>
        </div>
        <?
    }
    else if($rpt_type==2)
    {
        ?>
        <div style="width:1300px;" id="scroll_body">
            <fieldset style="width:100%">
                <table width="1200" cellpadding="0" cellspacing="0" id="caption" align="left">
                    <tr>
                        <td align="center" width="100%" colspan="9" class="form_caption" ><strong style="font-size:18px">Company Name:<? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
                    </tr>
                    <tr>
                        <td align="center" width="100%" colspan="9" class="form_caption" ><strong style="font-size:18px"><? echo $report_title; ?></strong></td>
                    </tr>
                    <tr>
                        <td width="100%" style="font-size:16px; font-weight:bold">File No:&nbsp;<? echo $txt_file_no; ?></td>
                    </tr>
                </table>
                <table width="1200" align="left">
                    <tr>
                        <td width="400">
                            <table width="400">
                                <tr>
                                    <td width="250"><b>Sales Contact (Finance/Lc-Sc):</b></td>
                                    <td align="center" width="100"><b>Value</b></td>
                                    <td>&nbsp;</td>
                                </tr>
                                <?
                                $sales_ref_finance= sql_select("SELECT id, buyer_name, contract_no, contract_value, estimated_qnty from com_sales_contract where beneficiary_name like '$cbo_company_name' and  buyer_name like '$cbo_buyer_name' and  internal_file_no like '$txt_file_no' and lien_bank like '$cbo_lein_bank' and sc_year like '$hide_year' and  ( converted_from is null or converted_from=0)  and  convertible_to_lc!=2 and is_deleted='0' and status_active='1' order by id");
                                $sc_id_arr=array();$lc_id_arr=array();
                                foreach($sales_ref_finance as $sal_ref) // Master Job  table queery ends here
                                {
                                    $sc_id_arr[]=$sal_ref[csf('id')];
                                    ?>
                                    <tr>
                                        <td>
                                        <?
                                        $sales_contct_ref= $sal_ref[csf('contract_no')];
                                        echo $sales_contct_ref;
                                        ?>
                                        &nbsp;Buyer: <?php echo $buyer_name_arr[$sal_ref[csf('buyer_name')]]; $buyer_ref_id=$sal_ref[csf('buyer_name')];?>&nbsp;EST. Qty: <?=$sal_ref[csf('estimated_qnty')];?> Pcs
                                        </td>
                                        <td align="right">
                                        <?
                                        $sales_contct_value_finance= $sal_ref[csf('contract_value')];
                                        echo number_format($sales_contct_value_finance,2);

                                        ?>
                                        </td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <?
                                    $total_sales_contct_value_finance += $sales_contct_value_finance;
                                }
                                ?>
                                <tr align="right">
                                    <td><b>Total</b></td>
                                    <td style="border-top-style: solid;border-top-width: 1px;"><b><?php  echo number_format($total_sales_contct_value_finance,2);?></b></td>
                                    <td>&nbsp;</td>
                                </tr>
                            </table>

                            <table width="400">
                                <tr>
                                    <td width="250"><b>Replacement(Lc/Sc)</b></td>
                                    <td align="center" width="100"><b>Value</b></td>
                                    <td ></td>
                                </tr>
                                    <? 
                                    $sales_ref3= sql_select("SELECT id, buyer_name, contract_no as lc_sc_no, contract_value as lc_sc_val, estimated_qnty, 1 as type
                                    from com_sales_contract
                                    where beneficiary_name like '$cbo_company_name' and  buyer_name like '$cbo_buyer_name' and  internal_file_no like '$txt_file_no' and lien_bank like '$cbo_lein_bank' and sc_year like '$hide_year' and converted_from!=0 and is_deleted='0' and status_active='1'
                                    union all
                                    select id, buyer_name, export_lc_no as lc_sc_no, lc_value as lc_sc_val, estimated_qnty, 2 as type
                                    from com_export_lc
                                    where  beneficiary_name like '$cbo_company_name' and  buyer_name like '$cbo_buyer_name' and  internal_file_no like '$txt_file_no' and lien_bank like '$cbo_lein_bank' and lc_year like '$hide_year' and is_deleted='0' and status_active='1' and replacement_lc='1'
                                    order by id");                                                              

                                    $sales_contract_id="";$sales_contract_number="";
                                    foreach($sales_ref3 as $sales_ref)  // Master Job  table queery ends here
                                    {
                                        if($sales_ref[csf('type')]==1)
                                        {
                                            $sc_id_arr[]=$sales_ref[csf('id')];          
                                        }
                                        else
                                        {
                                            $lc_id_arr[]=$sales_ref[csf('id')];                                 
                                        } 
                                        //print_r($sc_id_attachQty_arr);                                    

                                        ?>
                                        <tr>
                                            <td>
                                            <?
                                            $sales_contct_ref= $sales_ref[csf('lc_sc_no')];
                                            echo $sales_contct_ref;
                                            ?>
                                            &nbsp;Buyer: <?php echo $buyer_name_arr[$sales_ref[csf('buyer_name')]]?>&nbsp;EST. Qty: <?=$sales_ref[csf('estimated_qnty')];?> Pcs
                                            </td>
                                            <td align="right">
                                            <?
                                            $replace_lc_sc_val= $sales_ref[csf('lc_sc_val')];
                                            echo number_format($replace_lc_sc_val,2);
                                            ?>
                                            </td>
                                            <td>&nbsp;</td>
                                        </tr>
                                        <?
                                        $total_sales_contct_value_top += $replace_lc_sc_val;
                                    }
                                    ?>
                                <tr align="right">
                                    <td>&nbsp;</td>
                                    <td style="border-top-style: solid;border-top-width: 1px;"><b><? echo number_format($total_sales_contct_value_top,2)?></b></td>
                                    <td></td>
                                </tr>
                            </table>

                            <table width="400">
                                <tr>
                                    <td width="250"><b>Balance:</b></td>
                                    <td align="center" width="100"><b>Amount</b></td>
                                    <td ></td>
                                </tr>
                                <tr>
                                    <td>
                                    </td>
                                    <td align="right" style="border-top-style: solid;border-top-width: 1px;"><b>
                                    <?
                                    //balance show here
                                    $balance=$total_sales_contct_value_finance-$total_sales_contct_value_top;
                                    if($balance<0) $balance=0;
                                    echo number_format($balance,2);
                                    ?></b>
                                    </td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr align="right">
                                    <td>&nbsp;</td>
                                    <td ><? //echo number_format($total_sales_contct_value_top,2)?></td>
                                    <td></td>
                                </tr>
                            </table>

                            <table width="400">
                                <tr>
                                    <td width="250"><b>Salse Contact(Direct)</b></td>
                                    <td align="center" width="100"><b>Value</b></td>
                                    <td ></td>
                                </tr>

                                <?
                                $sales_direct= sql_select("SELECT id, buyer_name, contract_no, contract_value ,pay_term, estimated_qnty
                                from com_sales_contract
                                where beneficiary_name like '$cbo_company_name' and  buyer_name like '$cbo_buyer_name' and  internal_file_no like '$txt_file_no' and lien_bank like '$cbo_lein_bank' and sc_year like '$hide_year' and convertible_to_lc=2 and is_deleted='0'  and  ( converted_from is null or converted_from=0)  and status_active='1' order by id");

                                foreach($sales_direct as $exp_ref)  // Master Job  table queery ends here
                                {

                                    $sc_id_arr[]=$exp_ref[csf('id')];
                                    //if($exp_ref[csf('pay_term')] == 3)
                                    //{
                                        $cashInAdv_sc_id_arr[] = $exp_ref[csf('id')];
                                    //}
                                    ?>
                                    <tr>
                                        <td><?
                                        $export_lc= $exp_ref[csf('contract_no')];
                                        echo $export_lc;
                                        ?>
                                        &nbsp;Buyer: <? echo  $buyer_name_arr[$exp_ref[csf('buyer_name')]]; ?>&nbsp;EST. Qty: <?=$exp_ref[csf('estimated_qnty')];?> Pcs
                                        </td>
                                        <td align="right">
                                        <?
                                            $sc_direct_val= $exp_ref[ csf('contract_value')];
                                            echo number_format($sc_direct_val,2);
                                        ?>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <?
                                    $total_direct_sc_val+= $sc_direct_val;
                                    //$total_max_btb_sale_limit_value += $max_btb_sale_limit_value;
                                }
                                //var_dump( $sc_id_arr);die;
                                ?>
                                <tr align="right">
                                    <td></td>
                                    <td style="border-top-style: solid;border-top-width: 1px;"><b><? echo number_format($total_direct_sc_val,2)?></b></td>
                                </tr>
                            </table>

                            <table width="400">
                                <tr>
                                    <td width="250"><b>Lc(Direct)</b></td>
                                    <td align="center" width="100"><b>Value</b></td>
                                    <td ></td>
                                </tr>
                                <?
                                $exp_ref3= sql_select("SELECT id, buyer_name, export_lc_no as lc_sc_no, lc_value as lc_sc_val, estimated_qnty, 2 as type
                                from com_export_lc
                                where  beneficiary_name like '$cbo_company_name' and  buyer_name like '$cbo_buyer_name' and  internal_file_no like '$txt_file_no' and lien_bank like '$cbo_lein_bank' and lc_year like '$hide_year'  and is_deleted='0' and status_active='1' and replacement_lc=2");
    
                                foreach($exp_ref3 as $exp_ref)  // Master Job  table queery ends here
                                {
                                    $lc_id_arr[]=$exp_ref[csf('id')];
                                    ?>
                                    <tr>
                                        <td><?
                                        $export_lc= $exp_ref[csf('lc_sc_no')];
                                        echo $export_lc;
                                        ?>
                                        &nbsp;Buyer: <? echo  $buyer_name_arr[$exp_ref[csf('buyer_name')]]; ?>&nbsp;EST. Qty: <?=$exp_ref[csf('estimated_qnty')];?> Pcs
                                        </td>

                                        <td align="right"><?

                                        $direct_lc_val= $exp_ref[ csf('lc_sc_val')];
                                        echo number_format($direct_lc_val,2);
                                        ?>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <?
                                    $total_direct_lc_val+= $direct_lc_val;
                                }
                                ?>
                                <tr align="right">
                                    <td></td>
                                    <td style="border-top-style: solid;border-top-width: 1px;"><b><? echo number_format($total_direct_lc_val,2)?></b></td>
                                </tr>
                            </table>
                        </td>

                        <?
                        $i = 1;$realize_lc_arr=array();$submit_id_lc=0;//$relize_arr=array();$distributed_arr=array();$submit_inv_arr=array();
                        $lc_id_arr=array_unique($lc_id_arr);$sc_id_arr=array_unique($sc_id_arr);
                        
                        $sub_inv_id_lc=0;$sub_inv_id_sc=0;$payment_realized=0;$payment_realized_deduction=0;
                        //var_dump($lc_id_arr);
                        if(!empty($lc_id_arr))
                        {

                            $sub_rlz_sql=sql_select("select doc_submission_mst_id as sub_id from com_export_doc_submission_invo where is_lc=1 and lc_sc_id in(".implode(',',$lc_id_arr).")");
                            $submision_id_lc_rlz="";
                            foreach($sub_rlz_sql as $row)
                            {
                                if($submision_id_lc_rlz=="") $submision_id_lc_rlz=$row[csf("sub_id")]; 
                                else $submision_id_lc_rlz .=",".$row[csf("sub_id")];
                            }

                            if($submision_id_lc_rlz!="")
                            {
                                $submision_id_lc_rlz=array_chunk(array_unique(explode(",",$submision_id_lc_rlz)),999);
                                if($db_type==0)
                                {
                                    $sql_sub_lc_rlz="select a.id as relz_id, a.invoice_bill_id as sub_id, a.received_date, b.type, b.account_head, b.document_currency, c.lc_sc_id from com_export_proceed_realization a, com_export_proceed_rlzn_dtls b, com_export_doc_submission_invo c where a.id=b.mst_id and c.doc_submission_mst_id=a.invoice_bill_id";
                                    $p=1;
                                    foreach($submision_id_lc_rlz as $sub_id_lc_rlz)
                                    {
                                        if($p==1) $sql_sub_lc_rlz .=" and (a.invoice_bill_id in(".implode(',',$sub_id_lc_rlz).")"; else  $sql_sub_lc_rlz .=" or a.invoice_bill_id  in(".implode(',',$sub_id_lc_rlz).")";
                                        $p++;
                                    }

                                    $sql_sub_lc_rlz .=") and a.is_invoice_bill=1 and a.status_active=1 and a.is_deleted=0 group by b.id order by c.lc_sc_id,a.id";

                                }
                                else if($db_type==2)
                                {
                                    $sql_sub_lc_rlz="select a.id as relz_id, a.invoice_bill_id as sub_id, a.received_date, b.type, b.account_head, b.document_currency, c.lc_sc_id
                                    from com_export_proceed_realization a, com_export_proceed_rlzn_dtls b, com_export_doc_submission_invo c
                                    where a.id=b.mst_id and c.doc_submission_mst_id=a.invoice_bill_id";
                                    $p=1;
                                    foreach($submision_id_lc_rlz as $sub_id_lc_rlz)
                                    {
                                        if($p==1) $sql_sub_lc_rlz .=" and (a.invoice_bill_id in(".implode(',',$sub_id_lc_rlz).")"; else  $sql_sub_lc_rlz .=" or a.invoice_bill_id  in(".implode(',',$sub_id_lc_rlz).")";
                                        $p++;
                                    }

                                    $sql_sub_lc_rlz .=") and a.is_invoice_bill=1 and a.status_active=1 and a.is_deleted=0
                                    group by b.id , b.type, b.account_head, b.document_currency,a.id, a.invoice_bill_id, a.received_date, c.lc_sc_id
                                    order by c.lc_sc_id,a.id";
                                }

                                $sql_result_sub_lc_rlz=sql_select($sql_sub_lc_rlz);

                                foreach($sql_result_sub_lc_rlz as $row)
                                {
                                    if($submit_id_lc==0) $submit_id_lc=$row[csf("sub_id")]; 
                                    else $submit_id_lc= $submit_id_lc.",".$row[csf("sub_id")];
                                    $realize_lc_arr[$row[csf('relz_id')]]['sub_id']=$row[csf('sub_id')];
                                    $realize_lc_arr[$row[csf('relz_id')]]['rlz_received_date']=$row[csf('received_date')];

                                    if($row[csf('type')]==0)
                                    {
                                        $realize_lc_arr[$row[csf('relz_id')]]['deduct_realize']+=$row[csf('document_currency')];
                                        $payment_realized_deduction+=$row[csf('document_currency')];
                                    }
                                    else
                                    {
                                        $realize_lc_arr[$row[csf('relz_id')]]['distribute_realize']+=$row[csf('document_currency')];
                                        $payment_realized+=$row[csf('document_currency')];
                                    }

                                    if(!in_array($row[csf('relz_id')],$temp_arr))
                                    {
                                        //$payment_realized+=$row[csf('distribute_realize')];
                                        //$payment_realized_deduction+=$row[csf('deduct_realize')];
                                        $temp_arr[]=$row[csf('relz_id')];
                                    }

                                    if($row[csf("account_head")]==6)
                                    {
                                        $realize_lc_arr[$row[csf("relz_id")]]["erq"]+=$row[csf("document_currency")];
                                    }
                                    else if($row[csf("account_head")]==5)
                                    {
                                        $realize_lc_arr[$row[csf("relz_id")]]["btb"]+=$row[csf("document_currency")];
                                        $total_btb_margine+=$row[csf("document_currency")];
                                    }
                                    else if($row[csf("account_head")]==10)
                                    {
                                        $realize_lc_arr[$row[csf("relz_id")]]["cd"]+=$row[csf("document_currency")];
                                    }
                                    else if($row[csf("account_head")]==15)
                                    {
                                        $realize_lc_arr[$row[csf("relz_id")]]["cc"]+=$row[csf("document_currency")];
                                    }
                                    else if($row[csf("account_head")]==65)
                                    {
                                        $realize_lc_arr[$row[csf("relz_id")]]["fdbc"]+=$row[csf("document_currency")];
                                    }
                                    else if($row[csf("account_head")]==81)
                                    {
                                        $realize_lc_arr[$row[csf("relz_id")]]["sun_ac"]+=$row[csf("document_currency")];
                                    }
                                    else if($row[csf("account_head")]==11)
                                    {
                                        $realize_lc_arr[$row[csf("relz_id")]]["std"]+=$row[csf("document_currency")];
                                    }
                                }
                            }
                        }

                        $submit_id_lc=implode(",",array_unique(explode(",",$submit_id_lc)));

                        if($db_type==0)
                        {
                            $sql_re=sql_select("SELECT d.id as rlz_id, group_concat(distinct b.invoice_id) as inv_id,group_concat(distinct b.doc_submission_mst_id) as sub_id, group_concat(distinct c.invoice_no) as invoice_no, sum(b.net_invo_value) as net_invo_value, group_concat(distinct a.bank_ref_no) as bank_ref_no,group_concat(distinct a.submit_date) as bank_ref_date, sum(case when a.submit_type=1 then b.net_invo_value else 0 end) as sub_collection, group_concat(distinct a.negotiation_date) as negotiation_date, sum(a.total_negotiated_amount) as total_negotiated_amount, group_concat(distinct c.lc_sc_id) as lc_sc_id, c.is_lc,sum(c.invoice_quantity) as invoice_quantity
                            FROM com_export_doc_submission_mst a, com_export_doc_submission_invo b,  com_export_invoice_ship_mst c,com_export_proceed_realization d
                            WHERE b.doc_submission_mst_id=a.id and b.invoice_id=c.id and d.invoice_bill_id=a.id and d.is_invoice_bill='1'  and c.is_lc=1 AND a.id in($submit_id_lc) and  a.status_active=1 and a.is_deleted=0
                            group by d.id , c.is_lc
                            order by  c.lc_sc_id,d.id");
                        }
                        else
                        {
                            $sql_re=sql_select("SELECT d.id as rlz_id, LISTAGG(CAST( b.invoice_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.invoice_id) as inv_id, b.doc_submission_mst_id as sub_id, LISTAGG(CAST( c.invoice_no  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.invoice_no) as invoice_no, sum(b.net_invo_value) as net_invo_value, a.bank_ref_no , a.submit_date  as bank_ref_date, sum(case when a.submit_type=1 then b.net_invo_value else 0 end) as sub_collection, a.negotiation_date , sum(a.total_negotiated_amount) as total_negotiated_amount, b.lc_sc_id as lc_sc_id, c.is_lc,sum(c.invoice_quantity) as invoice_quantity
                            FROM com_export_doc_submission_mst a, com_export_doc_submission_invo b,  com_export_invoice_ship_mst c,com_export_proceed_realization d
                            WHERE b.doc_submission_mst_id=a.id and b.invoice_id=c.id and d.invoice_bill_id=a.id and d.is_invoice_bill='1' and c.is_lc=1 AND a.id in($submit_id_lc) and  a.status_active=1 and a.is_deleted=0
                            group by d.id , b.doc_submission_mst_id,a.bank_ref_no ,a.submit_date,a.negotiation_date,b.lc_sc_id,c.is_lc
                            order by b.lc_sc_id,d.id ");
                        }

                        foreach($sql_re as $result)
                        {
                            if($sub_inv_id_lc==0) $sub_inv_id_lc=$result[csf("inv_id")]; else $sub_inv_id_lc=$sub_inv_id_lc.",".$result[csf("inv_id")];
                            $submit_inv_arr[]=$result[csf("inv_id")];
                            $realize_lc_arr[$result[csf("rlz_id")]]['inv_id']=$result[csf("inv_id")];
                            $realize_lc_arr[$result[csf("rlz_id")]]['sub_id']=$result[csf("sub_id")];
                            $realize_lc_arr[$result[csf("rlz_id")]]['invoice_no']=$result[csf("invoice_no")];
                            $realize_lc_arr[$result[csf("rlz_id")]]['net_invo_value']=$result[csf("net_invo_value")];
                            $realize_lc_arr[$result[csf("rlz_id")]]['bank_ref_no']=$result[csf("bank_ref_no")];
                            $realize_lc_arr[$result[csf("rlz_id")]]['invoice_quantity']=$result[csf("invoice_quantity")];
                            $realize_lc_arr[$result[csf("rlz_id")]]['sub_collection']=$result[csf("sub_collection")];
                            $realize_lc_arr[$result[csf("rlz_id")]]['bank_ref_date']=$result[csf("bank_ref_date")];
                            //$realize_lc_arr[$result[csf("rlz_id")]]['purchase_amount']=$result[csf("purchase_amount")];
                            $realize_lc_arr[$result[csf("rlz_id")]]['negotiation_date']=$result[csf("negotiation_date")];
                            $realize_lc_arr[$result[csf("rlz_id")]]['total_negotiated_amount']=$result[csf("total_negotiated_amount")];
                            $realize_lc_arr[$result[csf("rlz_id")]]['lc_sc_id']=$result[csf("lc_sc_id")];
                        }

                        if($sub_inv_id_lc!=0) $sub_inv_id_lc=implode(",",array_unique(explode(",",$sub_inv_id_lc)));
                        //var_dump($realize_lc_arr);die;
                        //var_dump($relize_arr);
                        $submit_id_lc=implode(",",array_unique(explode(",",$submit_id_lc)));
                        $k = 1;$realize_sc_arr=array();$submit_id_sc=0;
                        //$sc_relize_arr=array();$sc_distributed_arr=array();

                        if(!empty($sc_id_arr))
                        {
                            $submision_id_sc_rlz="";
                            $sub_rlz_sql=sql_select("select doc_submission_mst_id as sub_id from com_export_doc_submission_invo where is_lc=2 and lc_sc_id in(".implode(',',$sc_id_arr).")");
                            //echo "<pre>";print_r($sub_rlz_sql);die;
                            foreach($sub_rlz_sql as $row)
                            {
                                $sub_lc_ids[$row[csf("sub_id")]]=$row[csf("sub_id")];
                            }
                            $submision_id_sc_rlz=implode(",",$sub_lc_ids);
                            //echo $submision_id_sc_rlz.test;die;
                            if($submision_id_sc_rlz!="" || !empty($cashInAdv_sc_id_arr))
                            {
                                $submision_id_sc_rlz=array_chunk(array_unique(explode(",",$submision_id_sc_rlz)),999);
                                if($db_type==0)
                                {
                                    $sql_rlz_query="select a.id as relz_id, a.invoice_bill_id as sub_id, a.received_date, b.type, b.account_head, b.document_currency, c.lc_sc_id , null as invoice_no, 1 as invoice_type from com_export_proceed_realization a, com_export_proceed_rlzn_dtls b, com_export_doc_submission_invo c where a.id=b.mst_id and c.doc_submission_mst_id=a.invoice_bill_id";
                                    
                                    $p=1;
                                    foreach($submision_id_sc_rlz as $sub_id_sc_rlz)
                                    {
                                        if($p==1) $sql_rlz_query .=" and (a.invoice_bill_id in(".implode(',',$sub_id_sc_rlz).")"; else  $sql_rlz_query .=" or a.invoice_bill_id  in(".implode(',',$sub_id_sc_rlz).")";
                                        $p++;
                                    }
                                    $sql_rlz_query .=") and a.is_invoice_bill=1 and a.status_active=1 and a.is_deleted=0 group by b.id order by c.lc_sc_id,a.id";
                                }
                                else if($db_type==2)
                                {
                                    $sql_rlz_query="select a.id as relz_id, a.invoice_bill_id as sub_id, a.received_date, b.type, b.account_head, b.document_currency, c.lc_sc_id, null as invoice_no, 1 as invoice_type from com_export_proceed_realization a, com_export_proceed_rlzn_dtls b, com_export_doc_submission_invo c where a.id=b.mst_id and c.doc_submission_mst_id=a.invoice_bill_id ";
                                    //echo $sql_rlz_query;die;
                                    $p=1;
                                    foreach($submision_id_sc_rlz as $sub_id_sc_rlz)
                                    {
                                        if($p==1) $sql_rlz_query .=" and (a.invoice_bill_id in(".implode(',',$sub_id_sc_rlz).")"; else  $sql_rlz_query .=" or a.invoice_bill_id  in(".implode(',',$sub_id_sc_rlz).")";
                                        $p++;
                                    }
                                    $sql_rlz_query .=") and a.is_invoice_bill=1 and a.status_active=1 and a.is_deleted=0 and b.status_active = 1 and b.is_deleted=0
                                    group by b.id , b.type, b.account_head, b.document_currency,a.id, a.invoice_bill_id, a.received_date, c.lc_sc_id";
                                }

                                //print_r($submision_id_sc_rlz);die;
                                if($sql_rlz_query) $sql_rlz_query .= " union all ";
                                //echo $sql_rlz_query;die;
                                //implode(",",array_filter(array_unique($cashInAdv_sc_id_arr)));


                                $cashInAdv_sc_id = implode("','",array_filter(array_unique($cashInAdv_sc_id_arr)));
                                $cashScCond=""; $cashInAdv_sc_id_arr=explode(",",$cashInAdv_sc_id);
                                if($cashInAdv_sc_id =="") {$cashInAdv_sc_id=0;}
                                $cashINAdvScIdCond = "";
                                if($db_type==2 && count($cashInAdv_sc_id_arr)>999)
                                {
                                    $job_no_chunk_arr=array_chunk($cashInAdv_sc_id_arr,999) ;
                                    foreach($job_no_chunk_arr as $chunk_arr)
                                    {
                                        $chunk_arr_value=implode(",",$chunk_arr);
                                        $cashScCond.=" c.lc_sc_id in($chunk_arr_value) or ";
                                    }

                                    $cashINAdvScIdCond.=" and (".chop($cashScCond,'or ').")";
                                }
                                else
                                {
                                    $cashINAdvScIdCond=" and c.lc_sc_id in($cashInAdv_sc_id)";
                                }

                                $sql_rlz_query .= " select a.id as relz_id, null as sub_id, a.received_date, b.type, b.account_head, b.document_currency, a.invoice_bill_id as sub_id, c.invoice_no, 2 as invoice_type 
                                from com_export_proceed_realization a, com_export_proceed_rlzn_dtls b , com_export_invoice_ship_mst c 
                                where a.id=b.mst_id and A.INVOICE_BILL_ID = c.id  and a.is_invoice_bill=2 and a.status_active=1 and a.is_deleted=0 and b.status_active = 1 and b.is_deleted=0 $cashINAdvScIdCond  and c.is_lc= 2 
                                group by b.id , b.type, b.account_head, b.document_currency, a.id, a.invoice_bill_id, a.received_date,c.invoice_no order by sub_id, relz_id";

                                //echo $sql_rlz_query."<br>";die;
                                $sql_rlz=sql_select($sql_rlz_query);
                                //print_r($sql_rlz);die;
                                foreach($sql_rlz as $row)
                                {
                                    if($submit_id_sc==0) $submit_id_sc=$row[csf("sub_id")]; else $submit_id_sc= $submit_id_sc.",".$row[csf("sub_id")];
                                    $realize_sc_arr[$row[csf('relz_id')]]['sub_id']=$row[csf('sub_id')];
                                    $realize_sc_arr[$row[csf('relz_id')]]['rlz_received_date']=$row[csf('received_date')];
                                    if($row[csf('invoice_type')] == 2){
                                        $realize_sc_arr[$row[csf("relz_id")]]['invoice_no']=$row[csf('invoice_no')];
                                    }

                                    if($row[csf('type')]==0)
                                    {
                                        $realize_sc_arr[$row[csf('relz_id')]]['deduct_realize']+=$row[csf('document_currency')];
                                        $payment_realized_deduction+=$row[csf('document_currency')];
                                    }
                                    else
                                    {
                                        $realize_sc_arr[$row[csf('relz_id')]]['distribute_realize']+=$row[csf('document_currency')];
                                        $payment_realized+=$row[csf('document_currency')];
                                    }

                                    if(!in_array($row[csf('relz_id')],$temp_arr))
                                    {
                                        //$payment_realized+=$row[csf('distribute_realize')];
                                        //$payment_realized_deduction+=$row[csf('deduct_realize')];
                                        $temp_arr[]=$row[csf('relz_id')];
                                    }

                                    if($row[csf("account_head")]==6)
                                    {
                                        $realize_sc_arr[$row[csf("relz_id")]]["erq"]+=$row[csf("document_currency")];
                                    }
                                    else if($row[csf("account_head")]==5)
                                    {
                                        $realize_sc_arr[$row[csf("relz_id")]]["btb"]+=$row[csf("document_currency")];
                                        $total_btb_margine+=$row[csf("document_currency")];
                                    }
                                    else if($row[csf("account_head")]==10)
                                    {
                                        $realize_sc_arr[$row[csf("relz_id")]]["cd"]+=$row[csf("document_currency")];
                                    }
                                    else if($row[csf("account_head")]==15)
                                    {
                                        $realize_sc_arr[$row[csf("relz_id")]]["cc"]+=$row[csf("document_currency")];
                                    }
                                    else if($row[csf("account_head")]==65)
                                    {
                                        $realize_sc_arr[$row[csf("relz_id")]]["fdbc"]+=$row[csf("document_currency")];
                                    }
                                    else if($row[csf("account_head")]==81)
                                    {
                                        $realize_sc_arr[$row[csf("relz_id")]]["sun_ac"]+=$row[csf("document_currency")];
                                    }
                                    else if($row[csf("account_head")]==11)
                                    {
                                        $realize_sc_arr[$row[csf("relz_id")]]["std"]+=$row[csf("document_currency")];
                                    }
                                }
                            }
                        }
                        //echo "<pre>";print_r($realize_sc_arr);die;
                        $submit_id_sc=implode(",",array_unique(explode(",",$submit_id_sc)));
                        //echo $submit_id_sc.tes;die;
                        if($submit_id_sc == "") $submit_id_sc = 0;

                        if($db_type==0)
                        {
                            $sql_sc=sql_select("SELECT d.id as rlz_id, group_concat(distinct b.invoice_id) as inv_id,group_concat(distinct a.id) as sub_id,  group_concat(distinct c.invoice_no  ) as invoice_no, sum(b.net_invo_value) as net_invo_value, group_concat(distinct a.bank_ref_no) as bank_ref_no, group_concat(distinct a.submit_date) as bank_ref_date, sum(case when a.submit_type=1 then b.net_invo_value else 0 end) as sub_collection, group_concat(distinct a.negotiation_date) as negotiation_date, sum(a.total_negotiated_amount) as total_negotiated_amount, group_concat(distinct c.lc_sc_id) as lc_sc_id, c.is_lc,sum(c.invoice_quantity) as invoice_quantity
                            FROM com_export_doc_submission_mst a, com_export_doc_submission_invo b,  com_export_invoice_ship_mst c,com_export_proceed_realization d
                            WHERE b.doc_submission_mst_id=a.id and b.invoice_id=c.id and d.invoice_bill_id=a.id and d.is_invoice_bill='1'  and c.is_lc=2 AND a.id in($submit_id_sc) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0
                            group by d.id, c.is_lc
                            order by b.lc_sc_id,d.id");
                        }
                        else if($db_type==2)
                        {
                            $sql_sc=sql_select("SELECT d.id as rlz_id, rtrim(xmlagg(xmlelement(e,b.invoice_id,',').extract('//text()') order by b.invoice_id).GetClobVal(),',') as inv_id, a.id as sub_id, rtrim(xmlagg(xmlelement(e,c.invoice_no,',').extract('//text()') order by c.invoice_no).GetClobVal(),',') as invoice_no, sum(b.net_invo_value) as net_invo_value,a.bank_ref_no,a.submit_date as bank_ref_date, sum(case when a.submit_type=1 then b.net_invo_value else 0 end) as sub_collection,a.negotiation_date, sum(a.total_negotiated_amount) as total_negotiated_amount,b.lc_sc_id as lc_sc_id, c.is_lc,sum(c.invoice_quantity) as invoice_quantity
                            FROM com_export_doc_submission_mst a, com_export_doc_submission_invo b,  com_export_invoice_ship_mst c, com_export_proceed_realization d
                            WHERE b.doc_submission_mst_id=a.id and b.invoice_id=c.id and d.invoice_bill_id=a.id and d.is_invoice_bill='1'  and c.is_lc=2 AND a.id in($submit_id_sc) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0
                            group by d.id,a.id,a.bank_ref_no,a.submit_date,a.negotiation_date,b.lc_sc_id,c.is_lc
                            order by b.lc_sc_id,d.id");
                        }
                        
                        foreach($sql_sc as $result)
                        {
                            if($sub_inv_id_sc==0) $sub_inv_id_sc=$result[csf("inv_id")]->load(); else $sub_inv_id_sc=$sub_inv_id_sc.",".$result[csf("inv_id")]->load();
                            $submit_inv_arr[]=$result[csf("inv_id")]->load();
                            $realize_sc_arr[$result[csf("rlz_id")]]['inv_id']=$result[csf("inv_id")]->load();
                            $realize_sc_arr[$result[csf("rlz_id")]]['sub_id']=$result[csf("sub_id")];
                            $realize_sc_arr[$result[csf("rlz_id")]]['invoice_no']=$result[csf("invoice_no")]->load();
                            $realize_sc_arr[$result[csf("rlz_id")]]['invoice_quantity']=$result[csf("invoice_quantity")];
                            $realize_sc_arr[$result[csf("rlz_id")]]['net_invo_value']=$result[csf("net_invo_value")];
                            $realize_sc_arr[$result[csf("rlz_id")]]['bank_ref_no']=$result[csf("bank_ref_no")];
                            $realize_sc_arr[$result[csf("rlz_id")]]['sub_collection']=$result[csf("sub_collection")];
                            $realize_sc_arr[$result[csf("rlz_id")]]['bank_ref_date']=$result[csf("bank_ref_date")];
                            //$realize_sc_arr[$result[csf("rlz_id")]]['purchase_amount']=$result[csf("purchase_amount")];
                            $realize_sc_arr[$result[csf("rlz_id")]]['negotiation_date']=$result[csf("negotiation_date")];
                            $realize_sc_arr[$result[csf("rlz_id")]]['total_negotiated_amount']=$result[csf("total_negotiated_amount")];
                            $realize_sc_arr[$result[csf("rlz_id")]]['lc_sc_id']=$result[csf("lc_sc_id")];
                            $realize_sc_arr[$result[csf("rlz_id")]]['is_lc']=$result[csf("is_lc")];
                        }
                        if($sub_inv_id_sc!=0) $sub_inv_id_sc=implode(",",array_unique(explode(",",$sub_inv_id_sc)));
                        //var_dump($sub_inv_id_sc);die;
                        //echo $sub_inv_id_sc; die;

                        $submit_id_lc=implode(",",array_unique(explode(",",$submit_id_lc)));
                        $submit_id_sc=implode(",",array_unique(explode(",",$submit_id_sc)));
                        $submission_id_lc=0;$submission_id_sc=0;$sub_as_collection=0;
                        $sub_lc_arr=array();
                        //echo "ttt<pre>";print_r($lc_id_arr);die;
                        if(!empty($lc_id_arr))
                        {
                            if($db_type==0)
                            {
                                $sql_re=sql_select("SELECT group_concat(distinct b.invoice_id) as inv_id,a.id as sub_id, group_concat(distinct c.invoice_no ) as invoice_no, sum(b.net_invo_value) as net_invo_value, a.bank_ref_no, a.submit_date, a.bank_ref_date, sum(case when a.submit_type=1 then b.net_invo_value else 0 end) as sub_collection, a.negotiation_date, sum(a.total_negotiated_amount) as total_negotiated_amount, c.lc_sc_id as lc_sc_id, c.is_lc, sum(c.invoice_quantity) as invoice_quantity
                                FROM com_export_doc_submission_mst a, com_export_doc_submission_invo b,  com_export_invoice_ship_mst c
                                WHERE b.doc_submission_mst_id=a.id and b.invoice_id=c.id and b.is_lc='1' and a.id not in($submit_id_lc) AND b.lc_sc_id in(".implode(',',$lc_id_arr).")  AND a.status_active='1' and a.is_deleted=0 and a.entry_form=40
                                group by a.id, a.bank_ref_no, a.submit_date, a.bank_ref_date, a.negotiation_date, c.lc_sc_id, c.is_lc
                                order by c.lc_sc_id,a.id");
                            }
                            else if($db_type==2)
                            {
                                $sql_re=sql_select("SELECT LISTAGG(CAST(  b.invoice_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.invoice_id) as inv_id,a.id as sub_id, LISTAGG(CAST(   c.invoice_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.invoice_no) as invoice_no, sum(b.net_invo_value) as net_invo_value, a.bank_ref_no, a.submit_date, a.bank_ref_date, sum(case when a.submit_type=1 then b.net_invo_value else 0 end) as sub_collection, a.negotiation_date, sum(a.total_negotiated_amount) as total_negotiated_amount, c.lc_sc_id as lc_sc_id, c.is_lc,sum(c.invoice_quantity) as invoice_quantity
                                FROM com_export_doc_submission_mst a, com_export_doc_submission_invo b,  com_export_invoice_ship_mst c
                                WHERE b.doc_submission_mst_id=a.id and b.invoice_id=c.id and b.is_lc='1' and a.id not in($submit_id_lc) AND b.lc_sc_id in(".implode(',',$lc_id_arr).")  AND a.status_active='1' and a.is_deleted=0  and a.entry_form=40
                                group by a.id, a.bank_ref_no, a.submit_date, a.bank_ref_date, a.negotiation_date, c.lc_sc_id, c.is_lc
                                order by c.lc_sc_id, a.id");
                            }
                        }

                        foreach($sql_re as $result)
                        {
                            if($submission_id_lc==0) $submission_id_lc=$result[csf("sub_id")]; else $submission_id_lc=$submission_id_lc.",".$result[csf("sub_id")];
                            if($sub_inv_id_lc==0) $sub_inv_id_lc=$result[csf("inv_id")]; else $sub_inv_id_lc=$sub_inv_id_lc.",".$result[csf("inv_id")];
                            $submit_inv_arr[]=$result[csf("inv_id")];
                            $sub_lc_arr[$result[csf("sub_id")]]['sub_id']=$result[csf("sub_id")];
                            $sub_lc_arr[$result[csf("sub_id")]]['inv_id']=$result[csf("inv_id")];
                            $sub_lc_arr[$result[csf("sub_id")]]['invoice_no']=$result[csf("invoice_no")];
                            $sub_lc_arr[$result[csf("sub_id")]]['invoice_quantity']=$result[csf("invoice_quantity")];
                            $sub_lc_arr[$result[csf("sub_id")]]['net_invo_value']=$result[csf("net_invo_value")];
                            $sub_lc_arr[$result[csf("sub_id")]]['bank_ref_no']=$result[csf("bank_ref_no")];
                            $sub_lc_arr[$result[csf("sub_id")]]['sub_collection']=$result[csf("sub_collection")];
                            $sub_lc_arr[$result[csf("sub_id")]]['submit_date']=$result[csf("submit_date")];
                            $sub_lc_arr[$result[csf("sub_id")]]['bank_ref_date']=$result[csf("bank_ref_date")];
                            $sub_lc_arr[$result[csf("sub_id")]]['purchase_amount']=$result[csf("purchase_amount")];
                            $sub_lc_arr[$result[csf("sub_id")]]['negotiation_date']=$result[csf("negotiation_date")];
                            $sub_lc_arr[$result[csf("sub_id")]]['total_negotiated_amount']=$result[csf("total_negotiated_amount")];
                            $sub_lc_arr[$result[csf("sub_idsub_lc_arr")]]['lc_sc_id']=$result[csf("lc_sc_id")];
                            $sub_lc_arr[$result[csf("sub_id")]]['is_lc']=$result[csf("is_lc")];
                            $sub_as_collection+=($result[csf("net_invo_value")]);
                        }

                        $sub_sc_arr=array();
                        if(!empty($sc_id_arr))
                        {
                            if($db_type==0)
                            {
                                $sql_sc=sql_select("SELECT group_concat(distinct b.invoice_id) as inv_id,a.id as sub_id, group_concat(distinct c.invoice_no) as invoice_no, sum(b.net_invo_value) as net_invo_value, a.bank_ref_no, a.submit_date, a.bank_ref_date, sum(case when a.submit_type=1 then b.net_invo_value else 0 end) as sub_collection, a.negotiation_date, sum(a.total_negotiated_amount) as total_negotiated_amount,  group_concat(distinct c.lc_sc_id) as lc_sc_id, c.is_lc,sum(c.invoice_quantity) as invoice_quantity, group_concat(distinct c.id) as sub_invoice_id
                                FROM com_export_doc_submission_mst a, com_export_doc_submission_invo b,  com_export_invoice_ship_mst c
                                WHERE b.doc_submission_mst_id=a.id and b.invoice_id=c.id  and b.is_lc='2' and a.id not in($submit_id_sc) AND b.lc_sc_id in(".implode(',',$sc_id_arr).") and a.company_id='$cbo_company_name' AND a.status_active='1' and a.is_deleted=0 and a.entry_form=40
                                group by a.id, a.bank_ref_no, a.submit_date, a.bank_ref_date, a.negotiation_date, c.is_lc
                                order by c.lc_sc_id,a.id");
                            }
                            else if($db_type==2)
                            {
                                $sql_sc=sql_select("SELECT LISTAGG(CAST( b.invoice_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.invoice_id) as inv_id,a.id as sub_id, LISTAGG(CAST(c.invoice_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.invoice_no)  as invoice_no, sum(b.net_invo_value) as net_invo_value, a.bank_ref_no, a.submit_date, a.bank_ref_date, sum(case when a.submit_type=1 then b.net_invo_value else 0 end) as sub_collection, a.negotiation_date, sum(a.total_negotiated_amount) as total_negotiated_amount,  LISTAGG(CAST( c.lc_sc_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.lc_sc_id) as lc_sc_id, c.is_lc,sum(c.invoice_quantity) as invoice_quantity, LISTAGG(CAST( c.id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.id) as sub_invoice_id
                                FROM com_export_doc_submission_mst a, com_export_doc_submission_invo b,  com_export_invoice_ship_mst c
                                WHERE b.doc_submission_mst_id=a.id and b.invoice_id=c.id  and b.is_lc='2' and a.id not in($submit_id_sc) AND b.lc_sc_id in(".implode(',',$sc_id_arr).") and a.company_id='$cbo_company_name' AND a.status_active='1' and a.is_deleted=0 and a.entry_form=40
                                group by a.id, a.bank_ref_no, a.submit_date, a.bank_ref_date, a.negotiation_date, c.is_lc
                                order by a.id");
                            }
                        }

                        foreach($sql_sc as $result)
                        {
                            if($submission_id_sc=0) $submission_id_sc=$result[csf("sub_id")]; else $submission_id_sc=$submission_id_sc.",".$result[csf("sub_id")];
                            //if($sc_submit_id==0) $sc_submit_id=$result[csf("sub_id")]; else $sc_submit_id=$sc_submit_id.",".$result[csf("sub_id")];
                            if($sub_inv_id_sc==0) $sub_inv_id_sc=$result[csf("sub_invoice_id")]; else $sub_inv_id_sc=$sub_inv_id_sc.",".$result[csf("sub_invoice_id")];

                            $submit_inv_arr[]=$result[csf("inv_id")];
                            $sub_sc_arr[$result[csf("sub_id")]]['sub_id']=$result[csf("sub_id")];
                            $sub_sc_arr[$result[csf("sub_id")]]['inv_id']=$result[csf("inv_id")];
                            $sub_sc_arr[$result[csf("sub_id")]]['invoice_no']=$result[csf("invoice_no")];
                            $sub_sc_arr[$result[csf("sub_id")]]['invoice_quantity']=$result[csf("invoice_quantity")];
                            $sub_sc_arr[$result[csf("sub_id")]]['net_invo_value']=$result[csf("net_invo_value")];
                            $sub_sc_arr[$result[csf("sub_id")]]['bank_ref_no']=$result[csf("bank_ref_no")];
                            $sub_sc_arr[$result[csf("sub_id")]]['sub_collection']=$result[csf("sub_collection")];
                            $sub_sc_arr[$result[csf("sub_id")]]['submit_date']=$result[csf("submit_date")];
                            $sub_sc_arr[$result[csf("sub_id")]]['bank_ref_date']=$result[csf("bank_ref_date")];
                            $sub_sc_arr[$result[csf("sub_id")]]['purchase_amount']=$result[csf("purchase_amount")];
                            $sub_sc_arr[$result[csf("sub_id")]]['negotiation_date']=$result[csf("negotiation_date")];
                            $sub_sc_arr[$result[csf("sub_id")]]['total_negotiated_amount']=$result[csf("total_negotiated_amount")];
                            $sub_sc_arr[$result[csf("sub_id")]]['lc_sc_id']=$result[csf("lc_sc_id")];
                            $sub_sc_arr[$result[csf("sub_id")]]['is_lc']=$result[csf("is_lc")];

                            $sub_as_collection+=($result[csf("net_invo_value")]);
                        }
                        //var_dump($sub_sc_arr);die;

                        $sub_buyer_arr=array();

                        if(!empty($sc_id_arr))
                        {
                            if($db_type==0)
                            {
                            $buyer_sub_sc="SELECT a.id as sub_id, a.buyer_id, a.submit_date, group_concat(distinct b.invoice_id) as inv_id, sum(b.net_invo_value) as net_invo_value, group_concat(distinct c.invoice_no) as invoice_no, c.lc_sc_id as lc_sc_id, sum(c.invoice_quantity) as invoice_quantity
                                FROM com_export_doc_submission_mst a, com_export_doc_submission_invo b,  com_export_invoice_ship_mst c
                                WHERE b.doc_submission_mst_id=a.id and b.invoice_id=c.id and b.is_converted=0   and b.is_lc='2' AND b.lc_sc_id in(".implode(',',$sc_id_arr).") and a.company_id='$cbo_company_name' AND a.status_active='1' and a.is_deleted=0 AND b.status_active='1' and b.is_deleted=0 and a.entry_form=39 and b.invoice_id not in(select q.invoice_id from com_export_doc_submission_mst p, com_export_doc_submission_invo q where p.id=q.doc_submission_mst_id and p.entry_form=40 and p.status_active=1 and p.is_deleted=0 and q.status_active=1 and q.is_deleted=0)
                                group by a.id, a.buyer_id, a.submit_date, c.lc_sc_id order by a.id";
                            }
                            else if($db_type==2)
                            {
                                $buyer_sub_sc="SELECT a.id as sub_id, a.buyer_id, a.submit_date, LISTAGG(CAST( b.invoice_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.invoice_id) as inv_id, sum(b.net_invo_value) as net_invo_value, LISTAGG(CAST(c.invoice_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.invoice_no)  as invoice_no, c.lc_sc_id as lc_sc_id, sum(c.invoice_quantity) as invoice_quantity
                                FROM com_export_doc_submission_mst a, com_export_doc_submission_invo b,  com_export_invoice_ship_mst c
                                WHERE b.doc_submission_mst_id=a.id and b.invoice_id=c.id and b.is_converted=0 and b.is_lc='2' AND b.lc_sc_id in(".implode(',',$sc_id_arr).") and a.company_id='$cbo_company_name' AND a.status_active='1' and a.is_deleted=0 AND b.status_active='1' and b.is_deleted=0 and a.entry_form=39 and b.invoice_id not in(select q.invoice_id from com_export_doc_submission_mst p, com_export_doc_submission_invo q where p.id=q.doc_submission_mst_id and p.entry_form=40 and p.status_active=1 and p.is_deleted=0 and q.status_active=1 and q.is_deleted=0)
                                group by a.id, a.buyer_id, a.submit_date, c.lc_sc_id order by a.id";
                                
                            }
                            //echo $buyer_sub_sc;
                            $sql_buyer_sub_sc=sql_select($buyer_sub_sc);
                        }

                        foreach($sql_buyer_sub_sc as $result)
                        {
                            if($submission_id_sc=0) $submission_id_sc=$result[csf("sub_id")]; else $submission_id_sc=$submission_id_sc.",".$result[csf("sub_id")];
                            //if($sc_submit_id==0) $sc_submit_id=$result[csf("sub_id")]; else $sc_submit_id=$sc_submit_id.",".$result[csf("sub_id")];
                            if($sub_inv_id_sc==0) $sub_inv_id_sc=$result[csf("inv_id")]; else $sub_inv_id_sc=$sub_inv_id_sc.",".$result[csf("inv_id")];

                            $submit_inv_arr[]=$result[csf("inv_id")];
                            $sub_buyer_arr[$result[csf("sub_id")]]['sub_id']=$result[csf("sub_id")];
                            $sub_buyer_arr[$result[csf("sub_id")]]['buyer_id']=$result[csf("buyer_id")];
                            $sub_buyer_arr[$result[csf("sub_id")]]['bank_ref_date']=$result[csf("submit_date")];
                            $sub_buyer_arr[$result[csf("sub_id")]]['inv_id']=$result[csf("inv_id")];
                            $sub_buyer_arr[$result[csf("sub_id")]]['invoice_no']=$result[csf("invoice_no")];
                            $sub_buyer_arr[$result[csf("sub_id")]]['invoice_quantity']=$result[csf("invoice_quantity")];
                            $sub_buyer_arr[$result[csf("sub_id")]]['net_invo_value']=$result[csf("net_invo_value")];
                            $sub_buyer_arr[$result[csf("sub_id")]]['lc_sc_id']=$result[csf("lc_sc_id")];
                            $sub_as_collection+=($result[csf("net_invo_value")]);
                        }
                        
                        $inv_arr=array();$in_hand=0;
                        if(!empty($lc_id_arr))
                        {
                            if($db_type==2)
                            {
                                $sql_lc=sql_select("select id, invoice_no, invoice_date, invoice_quantity, net_invo_value as invoice_value, lc_sc_id, is_lc from com_export_invoice_ship_mst  where is_lc=1 and lc_sc_id in(".implode(',',$lc_id_arr).") and id not in($sub_inv_id_lc) and  status_active=1 and is_deleted=0" );
                            }
                            else if($db_type==0)
                            {
                                $sql_lc=sql_select("select id, invoice_no, invoice_date, invoice_quantity, net_invo_value as invoice_value, lc_sc_id, is_lc from com_export_invoice_ship_mst  where is_lc=1 and lc_sc_id in(".implode(',',$lc_id_arr).") and id not in($sub_inv_id_lc) and  status_active=1 and is_deleted=0" );
                            }
                            foreach($sql_lc as $row)
                            {
                                $inv_arr[$row[csf("id")]]["id_lc"]=$row[csf("id")];
                                $inv_arr[$row[csf("id")]]["invoice_no"]=$row[csf("invoice_no")];
                                $inv_arr[$row[csf("id")]]["invoice_date"]=$row[csf("invoice_date")];
                                $inv_arr[$row[csf("id")]]["invoice_quantity"]=$row[csf("invoice_quantity")];
                                $inv_arr[$row[csf("id")]]["invoice_value"]=$row[csf("invoice_value")];
                                $inv_arr[$row[csf("id")]]["lc_sc_id"]=$row[csf("lc_sc_id")];
                                $inv_arr[$row[csf("id")]]["is_lc"]=$row[csf("is_lc")];
                                $in_hand +=$row[csf("invoice_value")];
                            }
                        }

                        if(!empty($sc_id_arr))
                        {
                            //echo $cashInAdv_sc_id."=========================================================";die;
                            if($cashInAdv_sc_id) $without_cashInAdvSc = " and lc_sc_id not in($cashInAdv_sc_id) "; else $without_cashInAdvSc = "";
                            if($db_type==2)
                            {
                                $sql_lc=sql_select("select id, invoice_no, invoice_date, invoice_quantity, net_invo_value as invoice_value, lc_sc_id, is_lc from com_export_invoice_ship_mst  where is_lc=2 and lc_sc_id in(".implode(',',$sc_id_arr).") $without_cashInAdvSc and id not in($sub_inv_id_sc)  and  status_active=1 and is_deleted=0");
                            }
                            else if($db_type==0)
                            {
                                $sql_lc=sql_select("select id, invoice_no, invoice_date, invoice_quantity, net_invo_value as invoice_value, lc_sc_id, is_lc from com_export_invoice_ship_mst  where is_lc=2 and lc_sc_id in(".implode(',',$sc_id_arr).") $without_cashInAdvSc and id not in($sub_inv_id_sc)  and  status_active=1 and is_deleted=0");
                            }

                            foreach($sql_lc as $row)
                            {
                                $inv_arr[$row[csf("id")]]["id_sc"]=$row[csf("id")];
                                $inv_arr[$row[csf("id")]]["invoice_no"]=$row[csf("invoice_no")];
                                $inv_arr[$row[csf("id")]]["invoice_date"]=$row[csf("invoice_date")];
                                $inv_arr[$row[csf("id")]]["invoice_quantity"]=$row[csf("invoice_quantity")];

                                $inv_arr[$row[csf("id")]]["invoice_value"]=$row[csf("invoice_value")];
                                $inv_arr[$row[csf("id")]]["lc_sc_id"]=$row[csf("lc_sc_id")];
                                $inv_arr[$row[csf("id")]]["is_lc"]=$row[csf("is_lc")];
                                $in_hand +=$row[csf("invoice_value")];
                            }
                        }
                        //var_dump($inv_arr);die;
                        ?>
                        <td style="vertical-align:top" width="450">
                            <table width="450" style="vertical-align:top">
                                <tr>
                                    <td width="200">Total File Value</td>
                                    <td width="100" align="right"></td>
                                    <td width="100" style="font-weight:bold;" align="right">&nbsp;&nbsp;
                                        <?
                                        $file_value=$total_sales_contct_value_top+$balance+$total_direct_sc_val+$total_direct_lc_val;
                                        echo number_format($file_value,2);//."_____".$lc_id."_____".$sc_id
                                            
                                        $attach_order_id=""; $powiseJobNoArr=array();
                                        $attach_value_sales=$attach_value_lc=0;
                                        if (!empty($sc_id_arr))
                                        {
                                            $attach_sales_sql="select b.id as po_id, c.job_no, (a.attached_qnty*c.total_set_qnty) as attached_qnty, a.attached_value
                                            from com_sales_contract_order_info a, wo_po_break_down b, wo_po_details_master c 
                                            where a.wo_po_break_down_id=b.id and b.job_no_mst=c.job_no and a.com_sales_contract_id in(". implode(',',$sc_id_arr).") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
                                            //echo $attach_sales_sql;die;
                                            $attach_sales_sql_result=sql_select($attach_sales_sql);
                                            foreach($attach_sales_sql_result as $row)
                                            {
                                                $attachQty_sales+=$row[csf("attached_qnty")];
                                                $attach_value_sales+=$row[csf("attached_value")];
                                                $attach_order_id.=$row[csf("po_id")].",";
                                                $powiseJobNoArr[$row[csf("po_id")]]=$row[csf("job_no")];
                                            }
                                        }
                                        //echo $attach_order_id.test;die;
                                        if (!empty($lc_id_arr))
                                        {
                                            $attach_lc_sql="select b.id as po_id, c.job_no, (a.attached_qnty*c.total_set_qnty) as attached_qnty, a.attached_value
                                            from com_export_lc_order_info a, wo_po_break_down b, wo_po_details_master c 
                                            where a.wo_po_break_down_id=b.id and b.job_no_mst=c.job_no and a.com_export_lc_id in(". implode(',',$lc_id_arr).") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
                                            $attach_lc_sql_result=sql_select($attach_lc_sql);
                                            foreach($attach_lc_sql_result as $row)
                                            {
                                                $attachQty_lc+=$row[csf("attached_qnty")];
                                                $attach_value_lc+=$row[csf("attached_value")];
                                                $attach_order_id.=$row[csf("po_id")].",";
                                                $powiseJobNoArr[$row[csf("po_id")]]=$row[csf("job_no")];
                                            }
                                        }
                                        $attach_order_id=implode(",",array_unique(explode(",",chop($attach_order_id,","))));
                                        $total_attach_value=$attach_value_sales+$attach_value_lc;
                                        ?>
                                    </td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>Total Attach Value</td>
                                    <td align="right"></td>
                                    <td align="right" style="font-weight:bold">&nbsp;&nbsp;
                                    <a href="##" onClick="fnc_attach_order_details('<? echo $cbo_company_name;?>','<? echo $txt_file_no;?>','<? echo $attach_order_id;?>','attach_order_details')"><? echo  number_format($total_attach_value,2); ?></a>
                                    </td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>Total Shipment</td>
                                    <td align="right"></td>
                                    <td align="right" style="font-weight:bold">&nbsp;&nbsp;
                                    <?
                                    //var_dump($lc_id_arr);var_dump($sc_id_arr);
                                    $adjustment_arr=array();
                                    if(!empty($lc_id_arr))
                                    {
                                        if($db_type==0)
                                        {
                                            $sql_lc=sql_select("SELECT b.export_lc_no, a.id as id, a.invoice_no, a.invoice_quantity as current_invoice_qnty, a.invoice_value as current_invoice_value, a.net_invo_value
                                            from com_export_invoice_ship_mst a, com_export_lc b
                                            where a.lc_sc_id=b.id and a.lc_sc_id in(". implode(',',$lc_id_arr).") and a.is_lc=1 and a.status_active=1 and benificiary_id like '$cbo_company_name' and  buyer_id like '$cbo_buyer_name'");
                                        }
                                        else if($db_type==2)
                                        {
                                            $sql_lc=sql_select("SELECT b.export_lc_no, a.id as id, a.invoice_no, a.invoice_quantity as current_invoice_qnty, a.invoice_value as current_invoice_value, a.net_invo_value
                                            from com_export_invoice_ship_mst a, com_export_lc b
                                            where a.lc_sc_id=b.id and a.lc_sc_id in(". implode(',',$lc_id_arr).") and a.is_lc=1 and a.status_active=1 and benificiary_id like '$cbo_company_name' and  buyer_id like '$cbo_buyer_name'");
                                        }
                                        $total_shipment_val=0;$shp_inv_id=array();$total_discount=0;
                                        foreach($sql_lc as $row_lc_result)
                                        {
                                            $shp_inv_id[]=$row_lc_result[csf("id")];
                                            $total_shipment_val += $row_lc_result[csf("current_invoice_value")];
                                            $total_discount +=($row_lc_result[csf("current_invoice_value")]- $row_lc_result[csf("net_invo_value")]);

                                            $adjustment_arr[$row_lc_result[csf("id")]]["id"]=$row_lc_result[csf("id")];
                                            $adjustment_arr[$row_lc_result[csf("id")]]["lc_sc_no"]=$row_lc_result[csf("export_lc_no")];
                                            $adjustment_arr[$row_lc_result[csf("id")]]["invoice_no"]=$row_lc_result[csf("invoice_no")];
                                            $adjustment_arr[$row_lc_result[csf("id")]]["current_invoice_qnty"]=$row_lc_result[csf("current_invoice_qnty")];
                                            $adjustment_arr[$row_lc_result[csf("id")]]["current_invoice_value"]=$row_lc_result[csf("current_invoice_value")];
                                            $adjustment_arr[$row_lc_result[csf("id")]]["net_invo_value"]=$row_lc_result[csf("net_invo_value")];
                                        }
                                    }

                                    if(!empty($sc_id_arr))
                                    {
                                        if($db_type==0)
                                        {
                                            $sql_cur_ship ="SELECT contract_no, a.id as id, a.invoice_no, a.invoice_quantity as current_invoice_qnty, a.invoice_value as current_invoice_value, a.net_invo_value
                                            from com_export_invoice_ship_mst a, com_sales_contract b
                                            where a.lc_sc_id=b.id and a.lc_sc_id in(".implode(',',$sc_id_arr).") and a.is_lc=2 and a.status_active=1 and benificiary_id like '$cbo_company_name' and  buyer_id like '$cbo_buyer_name'";
                                            $sql_sc_q=sql_select($sql_cur_ship);
                                        }
                                        else if($db_type==2)
                                        {
                                            $sql_cur_ship = "SELECT contract_no, a.id as id, a.invoice_no, a.invoice_quantity as current_invoice_qnty, a.invoice_value as current_invoice_value, a.net_invo_value
                                            from com_export_invoice_ship_mst a, com_sales_contract b
                                            where a.lc_sc_id=b.id and a.lc_sc_id in(".implode(',',$sc_id_arr).") and a.is_lc=2 and a.status_active=1 and benificiary_id like '$cbo_company_name' and  buyer_id like '$cbo_buyer_name'";
                                            $sql_sc_q=sql_select($sql_cur_ship);
                                        }
                                        foreach($sql_sc_q as $row_sc_result)
                                        {
                                            $shp_inv_id[]=$row_sc_result[csf("id")];
                                            $total_shipment_val += $row_sc_result[csf("current_invoice_value")];
                                            $total_discount += ($row_sc_result[csf("current_invoice_value")]-$row_sc_result[csf("net_invo_value")]);

                                            $adjustment_arr[$row_sc_result[csf("id")]]["id"]=$row_sc_result[csf("id")];
                                            $adjustment_arr[$row_sc_result[csf("id")]]["lc_sc_no"]=$row_sc_result[csf("contract_no")];//$row_sc_result[csf("lc_sc_no")];
                                            $adjustment_arr[$row_sc_result[csf("id")]]["invoice_no"]=$row_sc_result[csf("invoice_no")];
                                            $adjustment_arr[$row_sc_result[csf("id")]]["current_invoice_qnty"]=$row_sc_result[csf("current_invoice_qnty")];
                                            $adjustment_arr[$row_sc_result[csf("id")]]["current_invoice_value"]=$row_sc_result[csf("current_invoice_value")];
                                            $adjustment_arr[$row_sc_result[csf("id")]]["net_invo_value"]=$row_sc_result[csf("net_invo_value")];
                                        }

                                    }
                                    $lc_id=implode(',',$lc_id_arr); $sc_id=implode(',',$sc_id_arr); //hidden_lc_sc_id
                                    ?>
                                    <a href="##" onClick="fnc_amount_detail('<? echo $cbo_company_name;?>','<? echo $lc_id;?>','<? echo $sc_id;?>','invoice_details','<? echo $cbo_buyer_name; ?>')"><? echo  number_format($total_shipment_val,2); ?></a>
                                    </td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>Payment Realized</td>
                                    <td align="right">
                                    <?
                                    $total_break_ship_val=0;
                                    $total_break_ship_val=$total_break_ship_val+$payment_realized;
                                    echo number_format(($payment_realized),2);
                                    ?>
                                    </td>
                                    <td></td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>Document Under Collection/Rlz.</td>
                                    <td align="right">
                                        <?
                                        //var_dump($submission_id_sc);die;
                                        if($submission_id_lc)
                                        {
                                            $sub_as_purchase_amt_lc=return_field_value("sum(b.lc_sc_curr) as net_invo_value","com_export_doc_submission_mst a, com_export_doc_sub_trans b"," a.id=b.doc_submission_mst_id AND a.submit_type=2 and b.doc_submission_mst_id in($submission_id_lc)","net_invo_value");
                                        }
                                        if($submission_id_sc)
                                        {
                                            $sub_as_purchase_amt_sc=return_field_value("sum(b.lc_sc_curr) as net_invo_value","com_export_doc_submission_mst a, com_export_doc_sub_trans b"," a.id=b.doc_submission_mst_id AND a.submit_type=2 and b.doc_submission_mst_id in($submission_id_sc) ","net_invo_value");
                                        }

                                        $sub_as_purchase_amt=$sub_as_purchase_amt_lc+$sub_as_purchase_amt_sc;
                                        $sub_as_collection_rlz=$sub_as_collection-$sub_as_purchase_amt;
                                        $total_break_ship_val=$total_break_ship_val+$sub_as_collection_rlz;
                                        echo number_format(($sub_as_collection_rlz),2);
                                        ?>
                                    </td>
                                    <td></td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>Document Purchased</td>
                                    <td align="right">
                                        <?
                                            $total_break_ship_val=$total_break_ship_val+$sub_as_purchase_amt;
                                            echo number_format(($sub_as_purchase_amt),2);
                                        ?>
                                    </td>
                                    <td></td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>Document In Hand</td>
                                    <td align="right">
                                        <?
                                            $total_break_ship_val=$total_break_ship_val+$in_hand;
                                            echo number_format($in_hand,2);
                                        ?>
                                    </td>
                                    <td></td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>Short Realization</td>
                                    <td align="right">
                                    <?
                                        $total_break_ship_val=$total_break_ship_val+$payment_realized_deduction;
                                        echo number_format($payment_realized_deduction,2) ;
                                    ?>
                                    </td>
                                    <td></td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>Discount(<span style="font-size:9px; font-family:'Times New Roman', Times, serif">Gross Invoice value-Net Invoice Value</span>)</td>
                                    <td align="right">
                                        <?
                                            $total_break_ship_val=$total_break_ship_val+$total_discount;
                                            //$st_ex_ship_perc = ($payment_realized_deduction*100)/$payment_realized;
                                            echo number_format($total_discount,2);
                                        ?>
                                    </td>
                                    <td></td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td colspan="4">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td align="right" style="font-weight:bold;">Total:</td>
                                    <td align="right" style="border-top-style:solid;border-top-width:1px;font-weight:bold;">
                                        <?
                                            echo number_format($total_break_ship_val,2);
                                        ?>
                                    </td>
                                    <td></td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>% of Short/Excess Shipment</td>
                                    <td align="right">
                                        <?
                                            $st_ex_ship_perc = ($payment_realized_deduction*100)/$payment_realized;
                                            echo number_format($st_ex_ship_perc,2);
                                        ?>%
                                    </td>
                                    <td></td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>Balance Shipment</td>
                                    <td align="right">
                                    </td>
                                    <td align="right" style="font-weight:bold;">&nbsp;&nbsp;
                                        <?
                                            $shipment_balance =  $file_value-$total_shipment_val;
                                            echo number_format($shipment_balance,2);
                                        ?>
                                    </td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>Attached Order Qty</td>
                                    <td align="right">
                                    </td>
                                    <td align="right" style="font-weight:bold;">&nbsp;&nbsp;
                                        <?
                                        //print_r($lc_id_arr);echo "cks";die;
                                        //echo $attach_order_id.pk;die;

                                        $attachQty=$attachQty_sales+$attachQty_lc;                               
                                        echo number_format($attachQty,2);
                                        //echo $attach_order_id.test;die;
                                        $budge_btb_open_amt=0;
                                        
                                        if($attach_order_id!="")
                                        {
                                            $condition= new condition();
                                            $condition->po_id(" in( $attach_order_id ) ");
                                            
                                            $condition->init();
                                            $fabric= new fabric($condition);
                                            $fabric_costing_arr=$fabric->getAmountArray_by_order_knitAndwoven_greyAndfinish();
                                            
                                            $yarn= new yarn($condition);
                                            $yarn_costing_arr=$yarn->getOrderWiseYarnAmountArray();
                                            
                                            $trims= new trims($condition);
                                            $trims_costing_arr=$trims->getAmountArray_by_order();
                                            
                                            $emblishment= new emblishment($condition);
                                            $emblishment_costing_arr=$emblishment->getAmountArray_by_orderAndEmbname();
                                            //print_r($emblishment_costing_arr);
                                            
                                            $wash= new wash($condition);
                                            $emblishment_costing_arr_name_wash=$wash->getAmountArray_by_orderAndEmbname();

                                            $budge_btb_open_amt=0;
                                            $attach_order_id_arr=explode(",",$attach_order_id);
                                            //$attach_order_id_arr=array(0=>"51627",1=>"51628");
                                            foreach($attach_order_id_arr as $bompoid)
                                            {
                                                $job_wise_budge_amt[$powiseJobNoArr[$bompoid]]['0']+=array_sum($fabric_costing_arr['sweater']['grey'][$bompoid])+array_sum($fabric_costing_arr['knit']['grey'][$bompoid])+array_sum($fabric_costing_arr['woven']['grey'][$bompoid]);
                                                $job_wise_budge_amt[$powiseJobNoArr[$bompoid]]['1']+=$yarn_costing_arr[$bompoid];
                                                $job_wise_budge_amt[$powiseJobNoArr[$bompoid]]['2']+=$trims_costing_arr[$bompoid];
                                                $job_wise_budge_amt[$powiseJobNoArr[$bompoid]]['3']+=$emblishment_costing_arr[$bompoid][1]+$emblishment_costing_arr_name_wash[$bompoid][3]+$emblishment_costing_arr[$bompoid][4]+$emblishment_costing_arr[$bompoid][5];
                                            }
                                            
                                            $budge_btb_open_amt=$fab_budge_cost=$yarn_budge_cost=$trims_budge_cost=0;
                                            foreach($job_wise_budge_amt as $job_no_ref=>$job_data)
                                            {
                                                $budge_btb_open_amt+=array_sum($job_data);
                                                $fab_budge_cost+=$job_data[0];
                                                $yarn_budge_cost+=$job_data[1];
                                                $trims_budge_cost+=$job_data[2];
                                            }
                                        }
                                        if(empty($lc_id_arr)) $lc_Ids_arr=0;  else $lc_Ids_arr=implode(',',$lc_id_arr);
                                        if(empty($sc_id_arr)) $sc_Ids_arr=0;  else $sc_Ids_arr=implode(',',$sc_id_arr);

                                        if($db_type==0)
                                        {
                                            $btb_mst_lc_id=return_field_value("group_concat(distinct import_mst_id) as import_mst_id"," com_btb_export_lc_attachment","lc_sc_id in($lc_Ids_arr) and is_lc_sc=0 and status_active=1 and is_deleted=0","import_mst_id");
                                            $btb_mst_sc_id=return_field_value("group_concat(distinct import_mst_id) as import_mst_id","com_btb_export_lc_attachment","lc_sc_id in($sc_Ids_arr) and is_lc_sc=1  and status_active=1 and is_deleted=0","import_mst_id");
                                        }
                                        else if($db_type==2)
                                        {
                                            $btb_mst_lc_id=return_field_value("LISTAGG(CAST(import_mst_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY import_mst_id) as import_mst_id"," com_btb_export_lc_attachment","lc_sc_id in($lc_Ids_arr) and is_lc_sc=0  and status_active=1 and is_deleted=0","import_mst_id");
                                            $btb_mst_sc_id=return_field_value("LISTAGG(CAST(import_mst_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY import_mst_id) as import_mst_id","com_btb_export_lc_attachment","lc_sc_id in($sc_Ids_arr) and is_lc_sc=1  and status_active=1 and is_deleted=0","import_mst_id");
                                        }
                                        if($btb_mst_lc_id=="") $btb_mst_lc_id=0;
                                        if($btb_mst_sc_id=="") $btb_mst_sc_id=0;

                                        $mst_id=$btb_mst_lc_id.",".$btb_mst_sc_id;
                                        $sort_val=(explode(",",$mst_id));
                                        asort($sort_val);
                                        $mst_id=implode(",",$sort_val);

                                        $sql_btb=sql_select("select
                                        a.id, sum(a.lc_value) as lc_value, a.currency_id, a.lc_date, a.importer_id, 
                                        max(case when a.payterm_id=1 THEN a.id else 0 end) as at_sight_btb_lc_id,
                                        max(case when a.payterm_id=2 THEN a.id else 0 end) as usance_btb_lc_id,
                                        max(case when a.payterm_id=3 THEN a.id else 0 end) as cash_btb_lc_id,
                                        sum(case when a.payterm_id=3 THEN a.lc_value else 0 end) as cash_in_advance
                                        from com_btb_lc_master_details a
                                        where a.id in($mst_id)  and a.is_deleted=0 and a.status_active=1 group by a.id, a.currency_id, a.lc_date, a.importer_id");
                                        $btb_id="";
                                        foreach($sql_btb as $row)
                                        {
                                            $btb_id.=$row[csf("id")].",";
											if($row[csf("currency_id")]==1)
											{
												$conversion_rate=set_conversion_rate(2, $row[csf("lc_date")],$row[csf("importer_id")]);
												$row[csf("lc_value")]=$row[csf("lc_value")]/$conversion_rate;
											}
											
                                            $btb_open_value +=$row[csf("lc_value")];
                                            $at_sight_lc_id .=$row[csf("at_sight_btb_lc_id")].",";
                                            $usance_lc_id .=$row[csf("usance_btb_lc_id")].",";
                                            $cash_lc_id .=$row[csf("cash_btb_lc_id")].",";
                                            $cash_in_advance +=$row[csf("cash_in_advance")];
                                        }
                                        $btb_id=chop($btb_id,",");
                                        $atsite_accep_id=substr($at_sight_lc_id, 0, -1);
                                        $usance_paid_lc_id=substr($usance_lc_id, 0, -1);
                                        $cash_lc_id=substr($cash_lc_id, 0, -1);
                                        $btb_tobe_open_value=$budge_btb_open_amt-$btb_open_value;
                                        ?>
                                    </td>
                                    <td>&nbsp;</td>
                                </tr>
                            </table>
                        </td>
                        <td style="vertical-align:top" width="450">
                            <table width="400" style="vertical-align:top">
                                <tr>
                                    <td width="220" title="<? //print_r($job_wise_budge_amt); ?>">BTB to be Open</td>
                                    <td width="100" align="right" style="font-weight:bold;" title="budget(fabric purchase+yarn+trims+emb(without embro))cost-btb open value; <? echo $budge_btb_open_amt."-".$btb_open_value;?>"><? echo number_format($btb_tobe_open_value,2); ?></td>
                                    <td>&nbsp; </td>
                                </tr>
                                <tr>
                                    <td width="220">Total BTB Opened</td>
                                    <td width="100" align="right" style="font-weight:bold;">
                                        <input type="hidden" id="hidden_btb_id" value="<? echo $btb_id; ?>">
                                        <a href="##" onClick="btb_open('btb_open','<? echo $btb_id; ?>','<? echo $txt_file_no.'*'.$buyer_ref_id; ?>')"><? echo number_format($btb_open_value,2); ?> </a>
                                    </td>
                                    <td>&nbsp; </td>
                                </tr>
                                <tr>
                                    <td>BTB Percentage</td>
                                    <td align="right" style="font-weight:bold;"><? $btb_percent=($btb_open_value/$file_value)*100; echo number_format($btb_percent,2); ?>%</td>
                                    <td>&nbsp; </td>
                                </tr>
                                <tr>
                                    <td>Total BTB Accepted</td>
                                    <td align="right" style="font-weight:bold;" title="<? echo "all btb id=".$mst_id ?>">
                                        <?
                                        if($btb_id=="") $btb_id=0;
                                        if($atsite_accep_id=="") $atsite_accep_id=0;
                                        //echo $btb_id;die;
                                        $bill_accepted=return_field_value("sum(current_acceptance_value) as current_acceptance_value","com_import_invoice_dtls","btb_lc_id in($btb_id) and status_active=1 and is_deleted=0","current_acceptance_value");

                                        //$atsite_accepted=return_field_value("sum(current_acceptance_value) as current_acceptance_value","com_import_invoice_dtls","btb_lc_id in($atsite_accep_id) and status_active=1 and is_deleted=0","current_acceptance_value");
										$atsite_accep_sql=sql_select("select a.BTB_LC_ID, a.CURRENT_ACCEPTANCE_VALUE from com_import_invoice_dtls a, com_import_payment_com b where a.IMPORT_INVOICE_ID=b.INVOICE_ID and a.status_active=1 and b.status_active=1 and a.btb_lc_id in($atsite_accep_id)");
										$atsite_accep_id=array();
										foreach($atsite_accep_sql as $val)
										{
											$atsite_accepted+=$val["CURRENT_ACCEPTANCE_VALUE"];
											$atsite_accep_id[$val["BTB_LC_ID"]]=$val["BTB_LC_ID"];
										}

                                        //echo $btb_inv_id;
                                        ?>

                                        <input type="hidden" id="hidden_acept_id" value="<? echo $btb_id; ?>">
                                        <a href="##" onClick="btb_open('btb_accep','<? echo $btb_id; ?>','<? echo $txt_file_no.'*'.$buyer_ref_id; ?>')">
                                        <?
                                        echo number_format($bill_accepted,2);
                                        //echo $btb_inv_id;
                                        ?></a>
                                    </td>
                                    <td>&nbsp; </td>
                                </tr>
                                <tr>
                                    <td>Total Paid</td>
                                    <?
                                    if($cash_lc_id=="") $cash_lc_id=0;
                                    if($usance_paid_lc_id=="") $usance_paid_lc_id=0;
                                    $paid2_sql=sql_select("SELECT a.id, a.accepted_ammount as paid from com_import_payment a, com_import_invoice_dtls b where b.import_invoice_id=a.INVOICE_ID and b.btb_lc_id in($usance_paid_lc_id) and b.status_active=1 and a.status_active=1");

                                    foreach($paid2_sql as $row)
                                    {
                                        $all_usence_paid_id.=$row[csf("id")].",";
                                        if($paid_id_tes[$row[csf("id")]]=="")
                                        {
                                            $paid_id_tes[$row[csf("id")]]=$row[csf("id")];
                                            $paid2+=$row[csf("paid")];
                                        }
                                    }

                                    $all_usence_paid_id=chop($all_usence_paid_id,",");

                                    $paid=($paid2+$atsite_accepted+$cash_in_advance);

                                    $cash_lc_id=implode(",",array_unique(explode(",",$cash_lc_id)));
                                    $atsite_accep_id=implode(",",array_unique($atsite_accep_id));
                                    $usance_paid_lc_id=implode(",",array_unique(explode(",",$usance_paid_lc_id)));
                                    $all_usence_paid_id=implode(",",array_unique(explode(",",$all_usence_paid_id)));
                                    $paid_all_id=implode(",",array_unique(explode(",",$btb_id)))."_".$cash_lc_id."_".$atsite_accep_id."_".$all_usence_paid_id;
                                    //echo $usance_paid_lc_id;

                                    ?>
                                    <td align="right" style="font-weight:bold;" title="<? echo $paid2."==".$atsite_accepted."==".$cash_in_advance;?>">

                                    <a href="##" onClick="btb_open('btb_paid','<? echo $paid_all_id; ?>','<? echo $txt_file_no.'*'.$buyer_ref_id; ?>')">
                                    <?
                                    echo number_format($paid,2);
                                    ?>
                                    </a>
                                    </td>
                                    <td>&nbsp; </td>
                                </tr>
                                <tr>
                                    <td>Balance</td>
                                    <td align="right" style="font-weight:bold;">
                                        <?
                                            $bal= $bill_accepted-$paid;
                                            echo number_format($bal,2);

                                        ?>
                                    </td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>Yet To Acceptance</td>
                                    <td align="right" style="font-weight:bold;">
                                        <a href="##" onClick="btb_open('yet_to_accept_pop_up','<? echo $btb_id; ?>','<? echo $txt_file_no.'*'.$buyer_ref_id; ?>')">
                                        <?
                                            $yet_accept= $btb_open_value-$bill_accepted;
                                            echo number_format($yet_accept,2);
                                        ?>
                                        </a>
                                    </td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>BTB Margin A/C Balance</td>
                                    <td align="right" style="font-weight:bold;" title="<? echo "BTB Margine=".$total_btb_margine." ; Paid = ".$paid; ?>">
                                        <?
                                            $total_btb_margine_balance=$total_btb_margine-$paid;//$total_btb_margine
                                            echo number_format($total_btb_margine_balance,2);
                                        ?>
                                    </td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>PC Amount</td>
                                    <td align="right" style="font-weight:bold;">
                                        <?
                                        //print_r($sc_id_arr);
                                        if (!empty($lc_id_arr))
                                        {
                                            $pc_amount_lc= return_field_value("sum(b.loan_amount) as loan_amount", "com_pre_export_finance_dtls b, com_pre_export_lc_wise_dtls c","b.id = c.pre_export_dtls_id and c.export_type=1 and b.status_active=1 and b.is_deleted=0 and c.lc_sc_id in(". implode(',',$lc_id_arr).") and b.loan_type=20","loan_amount");
                                        }

                                        if (!empty($sc_id_arr))
                                        {
                                            $pc_amount_sc= return_field_value("sum(b.loan_amount) as loan_amount", "com_pre_export_finance_dtls b, com_pre_export_lc_wise_dtls c","b.id = c.pre_export_dtls_id and c.export_type=2 and b.status_active=1 and b.is_deleted=0 and c.lc_sc_id in(". implode(',',$sc_id_arr).") and b.loan_type=20","loan_amount");
                                        } 

                                        $pc_amount = $pc_amount_sc + $pc_amount_lc;
                                        echo number_format($pc_amount,2);
                                        ?>
                                    </td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td colspan="3">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                        <?
                                        	$sql= "SELECT a.id as BTB_ID, a.LC_TYPE_ID, a.PAYTERM_ID, a.lc_value as LC_VALUE, e.id as INV_ID, d.id as ACCP_DTLS_ID, d.current_acceptance_value as INV_AMOUNT, b.id as PI_DTLS_ID, b.ITEM_CATEGORY_ID, b.NET_PI_AMOUNT
                                            from com_btb_lc_master_details a, com_pi_item_details b, com_btb_lc_pi c
                                            left join com_import_invoice_dtls d on d.pi_id=c.pi_id and d.btb_lc_id=c.com_btb_lc_master_details_id and d.status_active=1
                                            left join com_import_invoice_mst e on e.id=d.import_invoice_id and e.status_active=1
                                            where a.id in ($btb_id) and a.id=c.com_btb_lc_master_details_id and b.pi_id=c.pi_id and a.status_active=1 and b.status_active=1 and c.status_active=1 ";
                                            //  echo $sql;
 
                                            $sql_result=sql_select($sql);
                                            $btb_check=$inv_check=$pi_check=array();
                                            $fund_bulid_btb_id=$all_btb_id="";
                                            foreach($sql_result as $row)
                                            {
                                                
                                                if($btb_check[$row["BTB_ID"]]=="")
                                                {
                                                    $btb_check[$row["BTB_ID"]]=$row["BTB_ID"];
                                                    if($row["LC_TYPE_ID"]==3)
                                                    {
                                                        $fund_bulid_btb_id.=$row["BTB_ID"].",";
                                                        $fund_bulid_open_amount+=$row["LC_VALUE"];
                                                    }
                                                    else
                                                    {
                                                        $all_btb_id.=$row["BTB_ID"].",";
                                                        if($row["ITEM_CATEGORY_ID"]==1){$yarn_open_amount+=$row["LC_VALUE"];}
                                                        else if($row["ITEM_CATEGORY_ID"]==2 || $row["ITEM_CATEGORY_ID"]==3){$fabric_open_amount+=$row["LC_VALUE"];}
                                                        else if($row["ITEM_CATEGORY_ID"]==4){$trims_open_amount+=$row["LC_VALUE"];}
                                                        else {$others_open_amount+=$row["LC_VALUE"];}
                                                        if($row["PAYTERM_ID"]==3)
                                                        {
                                                            if($row["ITEM_CATEGORY_ID"]==1){$yarn_cash_paid+=$row["LC_VALUE"];}
                                                            else if($row["ITEM_CATEGORY_ID"]==2 || $row["ITEM_CATEGORY_ID"]==3){$fabric_cash_paid+=$row["LC_VALUE"];}
                                                            else if($row["ITEM_CATEGORY_ID"]==4){$trims_cash_paid+=$row["LC_VALUE"];}
                                                            else {$others_cash_paid+=$row["LC_VALUE"];}
                                                        }
                                                    }
                                                }
                                                
                                                if($inv_check[$row["ACCP_DTLS_ID"]]=="")
                                                {
                                                    $inv_check[$row["ACCP_DTLS_ID"]]=$row["ACCP_DTLS_ID"];
                                                    if($row["ITEM_CATEGORY_ID"]==1){$yarn_accep_amount+=$row["INV_AMOUNT"];}
                                                    else if($row["ITEM_CATEGORY_ID"]==2 || $row["ITEM_CATEGORY_ID"]==3){$fabric_accep_amount+=$row["INV_AMOUNT"];}
                                                    else if($row["ITEM_CATEGORY_ID"]==4){$trims_accep_amount+=$row["INV_AMOUNT"];}
                                                    else {$others_accep_amount+=$row["INV_AMOUNT"];}
                                                }
                                            }
                                            $all_btb_id=implode(",",array_unique(explode(",",chop($all_btb_id,","))));
                                            $fund_bulid_btb_id=implode(",",array_unique(explode(",",chop($fund_bulid_btb_id,","))));

                                            $usance_paid_sql="SELECT a.ID, a.accepted_ammount as PAID, c.ITEM_CATEGORY_ID from com_import_payment a, com_import_invoice_dtls b, com_pi_item_details c where b.import_invoice_id=a.INVOICE_ID and b.btb_lc_id in($usance_paid_lc_id) and b.pi_id=c.pi_id and b.status_active=1 and a.status_active=1 and c.status_active=1";
                                            // echo $usance_paid_sql;
                                            $usance_paid_result=sql_select($usance_paid_sql);
                                            foreach($usance_paid_result as $row)
                                            {
                                                if($usance_paid_chk[$row["ID"]]=="")
                                                {
                                                    $usance_paid_chk[$row["ID"]]=$row["ID"];
                                                    if($row["ITEM_CATEGORY_ID"]==1){$yarn_usence_paid+=$row["PAID"];}
                                                    else if($row["ITEM_CATEGORY_ID"]==2 || $row["ITEM_CATEGORY_ID"]==3){$fabric_usence_paid+=$row["PAID"];}
                                                    else if($row["ITEM_CATEGORY_ID"]==4){$trims_usence_paid+=$row["PAID"];}
                                                    else {$others_usence_paid+=$row["PAID"];}
                                                }
                                            }

                                            $atsite_paid_sql="SELECT a.ID, a.current_acceptance_value as ACCEPTANCE_VALUE, b.ITEM_CATEGORY_ID from com_import_invoice_dtls a, com_pi_item_details b where a.btb_lc_id in($atsite_accep_id) and a.pi_id=b.pi_id and b.status_active=1 and a.status_active=1 ";
                                            // echo $atsite_paid_sql;
                                            $atsite_paid_result=sql_select($atsite_paid_sql);
                                            foreach($atsite_paid_result as $row)
                                            {
                                                if($atsite_paid_chk[$row["ID"]]=="")
                                                {
                                                    $atsite_paid_chk[$row["ID"]]=$row["ID"];
                                                    if($row["ITEM_CATEGORY_ID"]==1){$yarn_atsite_paid+=$row["ACCEPTANCE_VALUE"];}
                                                    else if($row["ITEM_CATEGORY_ID"]==2 || $row["ITEM_CATEGORY_ID"]==3){$fabric_atsite_paid+=$row["ACCEPTANCE_VALUE"];}
                                                    else if($row["ITEM_CATEGORY_ID"]==4){$trims_atsite_paid+=$row["ACCEPTANCE_VALUE"];}
                                                    else {$others_atsite_paid+=$row["ACCEPTANCE_VALUE"];}
                                                }
                                            }
                                        
                                        ?>
                                        <table  width="400" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all" id="" align="left"> 
                                            <thead>
                                                <tr>
                                                    <th colspan="5">BTB LC Details</th>
                                                </tr>
                                                <tr>
                                                    <th width="80">Particulars</th>
                                                    <th width="80">Yarn</th>
                                                    <th width="80">Trims</th>
                                                    <th width="80">Fabric</th>
                                                    <th>Others</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><b>Open</b></td>
                                                    <td align="right">                                        
                                                        <a href="##" onClick="open_summary('open_summary_pop_up','<?=$all_btb_id; ?>','<?=$txt_file_no; ?>','<?=$buyer_ref_id; ?>','<?=$yarn_budge_cost; ?>',800,'Yarn Open Search',1,1)">
                                                        <?echo number_format($yarn_open_amount,2); ?>
                                                        </a>
                                                    </td>
                                                    <td align="right">
                                                        <a href="##" onClick="open_summary('open_summary_pop_up','<?=$all_btb_id; ?>','<?=$txt_file_no; ?>','<?=$buyer_ref_id; ?>','<?=$trims_budge_cost; ?>',800,'Trims Open Search',1,2)">
                                                        <?echo number_format($trims_open_amount,2); ?>
                                                        </a>
                                                    </td>
                                                    <td align="right">
                                                        <a href="##" onClick="open_summary('open_summary_pop_up','<?=$all_btb_id; ?>','<?=$txt_file_no; ?>','<?=$buyer_ref_id; ?>','<?=$fab_budge_cost; ?>',800,'Fabric Open Search',1,3)">
                                                        <?echo number_format($fabric_open_amount,2); ?>
                                                        </a>
                                                    </td>
                                                    <td align="right">
                                                        <a href="##" onClick="open_summary('open_summary_pop_up','<?=$all_btb_id; ?>','<?=$txt_file_no; ?>','<?=$buyer_ref_id; ?>','<?=$service_budge_cost; ?>',800,'Others Open Search',1,4)">
                                                        <?echo number_format($others_open_amount,2); ?>
                                                        </a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><b>Acceptance</b></td>
                                                    <td align="right">                                        
                                                        <a href="##" onClick="open_summary('open_summary_pop_up','<?=$all_btb_id; ?>','<?=$txt_file_no; ?>','<?=$buyer_ref_id; ?>','<?=$yarn_budge_cost; ?>',800,'Yarn Acceptance Search',2,1)">
                                                        <?echo number_format($yarn_accep_amount,2); ?>
                                                        </a>
                                                    </td>
                                                    <td align="right">
                                                        <a href="##" onClick="open_summary('open_summary_pop_up','<?=$all_btb_id; ?>','<?=$txt_file_no; ?>','<?=$buyer_ref_id; ?>','<?=$trims_budge_cost; ?>',800,'Trims Acceptance Search',2,2)">
                                                        <?echo number_format($trims_accep_amount,2); ?>
                                                        </a>
                                                    </td>
                                                    <td align="right">
                                                        <a href="##" onClick="open_summary('open_summary_pop_up','<?=$all_btb_id; ?>','<?=$txt_file_no; ?>','<?=$buyer_ref_id; ?>','<?=$fab_budge_cost; ?>',800,'Fabric Acceptance Search',2,3)">
                                                        <?echo number_format($fabric_accep_amount,2); ?>
                                                        </a>
                                                    </td>
                                                    <td align="right">
                                                        <a href="##" onClick="open_summary('open_summary_pop_up','<?=$all_btb_id; ?>','<?=$txt_file_no; ?>','<?=$buyer_ref_id; ?>','<?=$service_budge_cost; ?>',800,'Others Acceptance Search',2,4)">
                                                        <?echo number_format($others_accep_amount,2); ?>
                                                        </a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><b>Paid</b></td>
                                                    <td align="right">
                                                        <a href="##" onClick="open_summary('open_summary_pop_up','<? echo $all_btb_id; ?>','<? echo $txt_file_no; ?>','<? echo $buyer_ref_id; ?>','<? echo $yarn_budge_cost; ?>',800,'Yarn Paid Search',3,1)">
                                                        <?echo number_format($yarn_cash_paid+$yarn_usence_paid+$yarn_atsite_paid,2); ?>
                                                        </a>
                                                    </td>
                                                    <td align="right">
                                                        <a href="##" onClick="open_summary('open_summary_pop_up','<? echo $all_btb_id; ?>','<? echo $txt_file_no; ?>','<? echo $buyer_ref_id; ?>','<? echo $trims_budge_cost; ?>',800,'Trims Paid Search',3,2)">
                                                        <?echo number_format($trims_cash_paid+$trims_usence_paid+$trims_atsite_paid,2); ?>
                                                        </a>
                                                    </td>
                                                    <td align="right">
                                                        <a href="##" onClick="open_summary('open_summary_pop_up','<? echo $all_btb_id; ?>','<? echo $txt_file_no; ?>','<? echo $buyer_ref_id; ?>','<? echo $fab_budge_cost; ?>',800,'Fabric Paid Search',3,3)">
                                                        <?echo number_format($fabric_cash_paid+$fabric_usence_paid+$fabric_atsite_paid,2); ?>
                                                        </a>
                                                    </td>
                                                    <td align="right">
                                                        <a href="##" onClick="open_summary('open_summary_pop_up','<? echo $all_btb_id; ?>','<? echo $txt_file_no; ?>','<? echo $buyer_ref_id; ?>','<? echo $service_budge_cost; ?>',800,'Others Paid Search',3,4)">
                                                        <?echo number_format($others_cash_paid+$others_usence_paid+$others_atsite_paid,2); ?>
                                                        </a>
                                                    </td>
                                                </tr>                                               
                                                <tr>
                                                    <td><b>Balance</b></td>
                                                    <td align="right">
                                                    <?echo number_format($yarn_accep_amount-($yarn_cash_paid+$yarn_usence_paid+$yarn_atsite_paid),2); ?>
                                                    </td>
                                                    <td align="right">
                                                    <?echo number_format($trims_accep_amount-($trims_cash_paid+$trims_usence_paid+$trims_atsite_paid),2); ?>
                                                    </td>
                                                    <td align="right">
                                                    <?echo number_format($fabric_accep_amount-($fabric_cash_paid+$fabric_usence_paid+$fabric_atsite_paid),2); ?>
                                                    </td>
                                                    <td align="right">
                                                    <?echo number_format($others_accep_amount-($others_cash_paid+$others_usence_paid+$others_atsite_paid),2); ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><b>Fund Buildup</b></td>
                                                    <td align="right"></td>
                                                    <td align="right"></td>
                                                    <td align="right"></td>
                                                    <td align="right">
                                                        <a href="##" onClick="open_summary('open_summary_pop_up','<?=$fund_bulid_btb_id; ?>','<? echo $txt_file_no; ?>','<? echo $buyer_ref_id; ?>','',800,'Others Fund Buildup Search',4,4)">
                                                        <?echo number_format($fund_bulid_open_amount,2); ?>
                                                        </a>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </fieldset>
        </div>
        <?
    }
    else if($rpt_type==3)
    {
        ?>
        <div style="width:1900px;" id="scroll_body">
            <fieldset style="width:100%">
                <table width="1200" cellpadding="0" cellspacing="0" id="caption" align="left">
                    <tr>
                        <td align="center" width="100%" colspan="9" class="form_caption" ><strong style="font-size:18px">Company Name:<? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
                    </tr>
                    <tr>
                        <td align="center" width="100%" colspan="9" class="form_caption" ><strong style="font-size:18px"><? echo $report_title; ?></strong></td>
                    </tr>
                    <tr>
                        <td width="100%" style="font-size:16px; font-weight:bold">File No:&nbsp;<? echo $txt_file_no; ?></td>
                    </tr>
                </table>
                <table width="1500" align="left">
                    <tr>
                        <td width="500">
                            <table width="450">
                                <tr>
                                    <td width="250"><b>Sales Contact (Finance/Lc-Sc):</b></td>
                                    <td align="center" width="100"><b>Value</b></td>
                                    <td>&nbsp;</td>
                                </tr>
                                <?
                                $sales_ref_finance= sql_select("select id, buyer_name, contract_no, contract_value ,FOREIGN_COMN_VALUE,LOCAL_COMN_VALUE from com_sales_contract where beneficiary_name like '$cbo_company_name' and  buyer_name like '$cbo_buyer_name' and  internal_file_no like '$txt_file_no' and lien_bank like '$cbo_lein_bank' and sc_year like '$hide_year' and  ( converted_from is null or converted_from=0)  and  convertible_to_lc!=2 and is_deleted='0' and status_active='1' order by id");
                                $sc_id_arr=array();$lc_id_arr=array();
                                foreach($sales_ref_finance as $sal_ref) // Master Job  table queery ends here
                                {
                                    $sc_id_arr[]=$sal_ref[csf('id')];
                                    ?>
                                    <tr>
                                        <td>
                                        <?
                                        $sales_contct_ref= $sal_ref[csf('contract_no')];
                                        echo $sales_contct_ref;
                                        ?>
                                        &nbsp;Buyer: <?php echo $buyer_name_arr[$sal_ref[csf('buyer_name')]]; $buyer_ref_id=$sal_ref[csf('buyer_name')];?>
                                        </td>
                                        <td align="right">
                                        <?
                                        $sales_contct_value_finance= $sal_ref[csf('contract_value')];
                                        echo number_format($sales_contct_value_finance,2);

                                        ?>
                                        </td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <?
                                    $total_sales_contct_value_finance += $sales_contct_value_finance;
                                    $foreign_comn_value += $sal_ref[csf('FOREIGN_COMN_VALUE')];
                                    $local_comn_value += $sal_ref[csf('LOCAL_COMN_VALUE')];
                                }
                                ?>
                                <tr align="right">
                                    <td><b>Total</b></td>
                                    <td style="border-top-style: solid;border-top-width: 1px;"><b><?php  echo number_format($total_sales_contct_value_finance,2);?></b></td>
                                    <td>&nbsp;</td>
                                </tr>
                            </table>

                            <table width="450">
                                <tr>
                                    <td width="250"><b>Replacement(Lc/Sc)</b></td>
                                    <td align="center" width="100"><b>Value</b></td>
                                    <td width="100"></td>
                                </tr>
                                    <? 
                                    $sales_ref3= sql_select("SELECT id, buyer_name, contract_no as lc_sc_no, contract_value as lc_sc_val, 1 as type,foreign_comn_value,local_comn_value
                                    from com_sales_contract
                                    where beneficiary_name like '$cbo_company_name' and  buyer_name like '$cbo_buyer_name' and  internal_file_no like '$txt_file_no' and lien_bank like '$cbo_lein_bank' and sc_year like '$hide_year' and converted_from!=0 and is_deleted='0' and status_active='1'
                                    union all
                                    select id, buyer_name, export_lc_no as lc_sc_no, lc_value as lc_sc_val, 2 as type,foreign_comn_value,local_comn_value
                                    from com_export_lc
                                    where  beneficiary_name like '$cbo_company_name' and  buyer_name like '$cbo_buyer_name' and  internal_file_no like '$txt_file_no' and lien_bank like '$cbo_lein_bank' and lc_year like '$hide_year' and is_deleted='0' and status_active='1' and replacement_lc='1'
                                    order by id");                                                              

                                    $sales_contract_id="";$sales_contract_number="";
                                    foreach($sales_ref3 as $sales_ref)  // Master Job  table queery ends here
                                    {
                                        if($sales_ref[csf('type')]==1)
                                        {
                                            $sc_id_arr[]=$sales_ref[csf('id')];          
                                        }
                                        else
                                        {
                                            $lc_id_arr[]=$sales_ref[csf('id')];                                 
                                        } 
                                        //print_r($sc_id_attachQty_arr);                                    

                                        ?>
                                        <tr>
                                            <td>
                                            <?
                                            $sales_contct_ref= $sales_ref[csf('lc_sc_no')];
                                            echo $sales_contct_ref;
                                            ?>
                                            &nbsp;Buyer: <?php echo $buyer_name_arr[$sales_ref[csf('buyer_name')]]?>
                                            </td>
                                            <td align="right">
                                            <?
                                            $replace_lc_sc_val= $sales_ref[csf('lc_sc_val')];
                                            echo number_format($replace_lc_sc_val,2);
                                            ?>
                                            </td>
                                            <td>&nbsp;</td>
                                        </tr>
                                        <?
                                        $total_sales_contct_value_top += $replace_lc_sc_val;
                                        $foreign_comn_value += $sales_ref[csf('FOREIGN_COMN_VALUE')];
                                        $local_comn_value += $sales_ref[csf('LOCAL_COMN_VALUE')];
                                    }
                                
                                    ?>
                                <tr align="right">
                                    <td>&nbsp;</td>
                                    <td style="border-top-style: solid;border-top-width: 1px;"><b><? echo number_format($total_sales_contct_value_top,2)?></b></td>
                                    <td></td>
                                </tr>
                            </table>

                            <table width="450">
                                <tr>
                                    <td width="250"><b>Balance:</b></td>
                                    <td align="center" width="100"><b>Amount</b></td>
                                    <td width="100"></td>
                                </tr>
                                <tr>
                                    <td>
                                    </td>
                                    <td align="right" style="border-top-style: solid;border-top-width: 1px;"><b>
                                    <?
                                    //balance show here
                                    $balance=$total_sales_contct_value_finance-$total_sales_contct_value_top;
                                    if($balance<0) $balance=0;
                                    echo number_format($balance,2);
                                    ?></b>
                                    </td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr align="right">
                                    <td>&nbsp;</td>
                                    <td ><? //echo number_format($total_sales_contct_value_top,2)?></td>
                                    <td></td>
                                </tr>
                            </table>

                            <table width="450">
                                <tr>
                                    <td width="250"><b>Salse Contact(Direct)</b></td>
                                    <td align="center" width="100"><b>Value</b></td>
                                    <td width="100"></td>
                                </tr>

                                <?
                                $sales_direct= sql_select("select id, buyer_name, contract_no, contract_value ,pay_term,foreign_comn_value,local_comn_value
                                from com_sales_contract
                                where beneficiary_name like '$cbo_company_name' and  buyer_name like '$cbo_buyer_name' and  internal_file_no like '$txt_file_no' and lien_bank like '$cbo_lein_bank' and sc_year like '$hide_year' and convertible_to_lc=2 and is_deleted='0'  and  ( converted_from is null or converted_from=0)  and status_active='1' order by id");

                                foreach($sales_direct as $exp_ref)  // Master Job  table queery ends here
                                {

                                    $sc_id_arr[]=$exp_ref[csf('id')];
                                    //if($exp_ref[csf('pay_term')] == 3)
                                    //{
                                        $cashInAdv_sc_id_arr[] = $exp_ref[csf('id')];
                                    //}
                                    ?>
                                    <tr>
                                        <td><?
                                        $export_lc= $exp_ref[csf('contract_no')];
                                        echo $export_lc;
                                        ?>
                                        &nbsp;Buyer: <? echo  $buyer_name_arr[$exp_ref[csf('buyer_name')]]; ?>
                                        </td>
                                        <td align="right">
                                        <?
                                            $sc_direct_val= $exp_ref[ csf('contract_value')];
                                            echo number_format($sc_direct_val,2);
                                        ?>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <?
                                    $total_direct_sc_val+= $sc_direct_val;
                                    //$total_max_btb_sale_limit_value += $max_btb_sale_limit_value;
                                    $foreign_comn_value += $exp_ref[csf('FOREIGN_COMN_VALUE')];
                                    $local_comn_value += $exp_ref[csf('LOCAL_COMN_VALUE')];
                                }
                                //var_dump( $sc_id_arr);die;
                                ?>
                                <tr align="right">
                                    <td></td>
                                    <td style="border-top-style: solid;border-top-width: 1px;"><b><? echo number_format($total_direct_sc_val,2)?></b></td>
                                </tr>
                            </table>

                            <table width="450">
                                <tr>
                                    <td width="250"><b>Lc(Direct)</b></td>
                                    <td align="center" width="100"><b>Value</b></td>
                                    <td width="100"></td>
                                </tr>
                                <?
                                $exp_ref3= sql_select("select id, buyer_name, export_lc_no as lc_sc_no, lc_value as lc_sc_val, 2 as type,foreign_comn_value,local_comn_value
                                from com_export_lc
                                where  beneficiary_name like '$cbo_company_name' and  buyer_name like '$cbo_buyer_name' and  internal_file_no like '$txt_file_no' and lien_bank like '$cbo_lein_bank' and lc_year like '$hide_year'  and is_deleted='0' and status_active='1' and replacement_lc=2");
                                //echo 	"select id, buyer_name, export_lc_no as lc_sc_no, lc_value as lc_sc_val, 2 as type from	com_export_lc where	beneficiary_name like '$cbo_company_name' and  buyer_name like '$cbo_buyer_name' and  internal_file_no like '$txt_file_no' and lien_bank like '$cbo_lein_bank' and lc_year like '$hide_year'  and is_deleted='0' and status_active='1' and replacement_lc=2";die;

                                foreach($exp_ref3 as $exp_ref)  // Master Job  table queery ends here
                                {
                                    $lc_id_arr[]=$exp_ref[csf('id')];
                                    ?>
                                    <tr>
                                        <td><?
                                        $export_lc= $exp_ref[csf('lc_sc_no')];
                                        echo $export_lc;
                                        ?>
                                        &nbsp;Buyer: <? echo  $buyer_name_arr[$exp_ref[csf('buyer_name')]]; ?>
                                        </td>

                                        <td align="right"><?

                                        $direct_lc_val= $exp_ref[ csf('lc_sc_val')];
                                        echo number_format($direct_lc_val,2);
                                        ?>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <?
                                    $total_direct_lc_val+= $direct_lc_val;

                                    $foreign_comn_value += $exp_ref[csf('FOREIGN_COMN_VALUE')];
                                    $local_comn_value += $exp_ref[csf('LOCAL_COMN_VALUE')];
                                }
                                ?>
                                <tr align="right">
                                    <td></td>
                                    <td style="border-top-style: solid;border-top-width: 1px;"><b><? echo number_format($total_direct_lc_val,2)?></b></td>
                                </tr>

                                <tr>
                                    <td width="250">Local Commision</td>
                                    <td align="right" width="100"><b><? echo number_format($local_comn_value,2) ;?></b></td>
                                    <td width="100"></td>
                                </tr>
                                 <tr>
                                    <td width="250">Foreign Commision</td>
                                    <td align="right" width="100"><b><? echo number_format($foreign_comn_value,2) ;?></b></td>
                                    <td width="100"><b></b></td>
                                </tr>
                                 <tr>
                                    <? 
                                    $lc_tot_value = $total_sales_contct_value_finance + $total_sales_contct_value_top + $total_direct_sc_val +$total_direct_lc_val;
                                    $net_tot_lc_val = $lc_tot_value - $local_comn_value -$foreign_comn_value ;
                                    ?>
                                    <td width="250">Net File Value</td>
                                    <td align="right" width="100"><b><? echo number_format($net_tot_lc_val,2);?></b></td>
                                    <td width="100"><b></b></td>
                                </tr>
                                
                            </table>
                        </td>

                        <?
                        //$file_value=$total_sales_contct_value_top+$balance+$total_direct_sc_val+$total_direct_lc_val;
                        $i = 1;$realize_lc_arr=array();$submit_id_lc=0;//$relize_arr=array();$distributed_arr=array();$submit_inv_arr=array();
                        $lc_id_arr=array_unique($lc_id_arr);$sc_id_arr=array_unique($sc_id_arr);
                        
                        $sub_inv_id_lc=0;$sub_inv_id_sc=0;$payment_realized=0;$payment_realized_deduction=0;
                        //var_dump($lc_id_arr);
                        if(!empty($lc_id_arr))
                        {
                            /*if($db_type==0)
                            {
                                $submision_id_lc_rlz=return_field_value("group_concat(distinct doc_submission_mst_id) as id","com_export_doc_submission_invo","is_lc=1 and lc_sc_id in(".implode(',',$lc_id_arr).")","id");

                            }
                            else if($db_type==2)
                            {
                                $submision_id_lc_rlz=return_field_value("LISTAGG(CAST(doc_submission_mst_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY doc_submission_mst_id) as id","com_export_doc_submission_invo","is_lc=1 and lc_sc_id in(".implode(',',$lc_id_arr).")","id");
                                $submision_id_lc_rlz=implode(",",array_unique(explode(",",$submision_id_lc_rlz)));
                            }*/


                            $sub_rlz_sql=sql_select("select doc_submission_mst_id as sub_id from com_export_doc_submission_invo where is_lc=1 and lc_sc_id in(".implode(',',$lc_id_arr).")");
                            $submision_id_lc_rlz="";
                            foreach($sub_rlz_sql as $row)
                            {
                                if($submision_id_lc_rlz=="") $submision_id_lc_rlz=$row[csf("sub_id")]; 
                                else $submision_id_lc_rlz .=",".$row[csf("sub_id")];
                            }

                            //$invoice_id_lc_arr=array_chunk(array_unique(explode(",",$invoice_id_lc)),999);
                            if($submision_id_lc_rlz!="")
                            {
                                $submision_id_lc_rlz=array_chunk(array_unique(explode(",",$submision_id_lc_rlz)),999);
                                if($db_type==0)
                                {
                                    $sql_sub_lc_rlz="select a.id as relz_id, a.invoice_bill_id as sub_id, a.received_date, b.type, b.account_head, b.document_currency, c.lc_sc_id from com_export_proceed_realization a, com_export_proceed_rlzn_dtls b, com_export_doc_submission_invo c where a.id=b.mst_id and c.doc_submission_mst_id=a.invoice_bill_id";
                                    $p=1;
                                    foreach($submision_id_lc_rlz as $sub_id_lc_rlz)
                                    {
                                        if($p==1) $sql_sub_lc_rlz .=" and (a.invoice_bill_id in(".implode(',',$sub_id_lc_rlz).")"; else  $sql_sub_lc_rlz .=" or a.invoice_bill_id  in(".implode(',',$sub_id_lc_rlz).")";
                                        $p++;
                                    }

                                    $sql_sub_lc_rlz .=") and a.is_invoice_bill=1 and a.status_active=1 and a.is_deleted=0 group by b.id order by c.lc_sc_id,a.id";

                                    // AND a.invoice_bill_id in($submision_id_lc_rlz) and a.is_invoice_bill=1 and a.status_active=1 and a.is_deleted=0 group by b.id order by c.lc_sc_id,a.id";
                                }
                                else if($db_type==2)
                                {
                                    $sql_sub_lc_rlz="select a.id as relz_id, a.invoice_bill_id as sub_id, a.received_date, b.type, b.account_head, b.document_currency, c.lc_sc_id
                                    from com_export_proceed_realization a, com_export_proceed_rlzn_dtls b, com_export_doc_submission_invo c
                                    where a.id=b.mst_id and c.doc_submission_mst_id=a.invoice_bill_id";
                                    $p=1;
                                    foreach($submision_id_lc_rlz as $sub_id_lc_rlz)
                                    {
                                        if($p==1) $sql_sub_lc_rlz .=" and (a.invoice_bill_id in(".implode(',',$sub_id_lc_rlz).")"; else  $sql_sub_lc_rlz .=" or a.invoice_bill_id  in(".implode(',',$sub_id_lc_rlz).")";
                                        $p++;
                                    }

                                    $sql_sub_lc_rlz .=") and a.is_invoice_bill=1 and a.status_active=1 and a.is_deleted=0
                                    group by b.id , b.type, b.account_head, b.document_currency,a.id, a.invoice_bill_id, a.received_date, c.lc_sc_id
                                    order by c.lc_sc_id,a.id";
                                }

                                //echo $sql_sub_lc_rlz."<br>jahid**********<br>";
                                $sql_result_sub_lc_rlz=sql_select($sql_sub_lc_rlz);

                                foreach($sql_result_sub_lc_rlz as $row)
                                {
                                    if($submit_id_lc==0) $submit_id_lc=$row[csf("sub_id")]; 
                                    else $submit_id_lc= $submit_id_lc.",".$row[csf("sub_id")];
                                    $realize_lc_arr[$row[csf('relz_id')]]['sub_id']=$row[csf('sub_id')];
                                    $realize_lc_arr[$row[csf('relz_id')]]['rlz_received_date']=$row[csf('received_date')];

                                    if($row[csf('type')]==0)
                                    {
                                        $realize_lc_arr[$row[csf('relz_id')]]['deduct_realize']+=$row[csf('document_currency')];
                                        $payment_realized_deduction+=$row[csf('document_currency')];
                                    }
                                    else
                                    {
                                        $realize_lc_arr[$row[csf('relz_id')]]['distribute_realize']+=$row[csf('document_currency')];
                                        $payment_realized+=$row[csf('document_currency')];
                                    }

                                    if(!in_array($row[csf('relz_id')],$temp_arr))
                                    {
                                        //$payment_realized+=$row[csf('distribute_realize')];
                                        //$payment_realized_deduction+=$row[csf('deduct_realize')];
                                        $temp_arr[]=$row[csf('relz_id')];
                                    }

                                    if($row[csf("account_head")]==6)
                                    {
                                        $realize_lc_arr[$row[csf("relz_id")]]["erq"]+=$row[csf("document_currency")];
                                    }
                                    else if($row[csf("account_head")]==5)
                                    {
                                        $realize_lc_arr[$row[csf("relz_id")]]["btb"]+=$row[csf("document_currency")];
                                        $total_btb_margine+=$row[csf("document_currency")];
                                    }
                                    else if($row[csf("account_head")]==10)
                                    {
                                        $realize_lc_arr[$row[csf("relz_id")]]["cd"]+=$row[csf("document_currency")];
                                    }
                                    else if($row[csf("account_head")]==15)
                                    {
                                        $realize_lc_arr[$row[csf("relz_id")]]["cc"]+=$row[csf("document_currency")];
                                    }
                                    else if($row[csf("account_head")]==65)
                                    {
                                        $realize_lc_arr[$row[csf("relz_id")]]["fdbc"]+=$row[csf("document_currency")];
                                    }
                                    else if($row[csf("account_head")]==81)
                                    {
                                        $realize_lc_arr[$row[csf("relz_id")]]["sun_ac"]+=$row[csf("document_currency")];
                                    }
                                    else if($row[csf("account_head")]==11)
                                    {
                                        $realize_lc_arr[$row[csf("relz_id")]]["std"]+=$row[csf("document_currency")];
                                    }

                                    /*$payment_realized+=$realize_lc_arr[$row[csf('relz_id')]]['distribute_realize']=$row[csf('distribute_realize')];
                                    $payment_realized_deduction+=$realize_lc_arr[$row[csf('relz_id')]]['deduct_realize']=$row[csf('deduct_realize')];*/
                                }
                            }
                        }
                        //var_dump($realize_lc_arr);die;
                        // LISTAGG(CAST( b.id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.id) as tr_id
                        $submit_id_lc=implode(",",array_unique(explode(",",$submit_id_lc)));

                        if($db_type==0)
                        {
                            $sql_re=sql_select("SELECT d.id as rlz_id, group_concat(distinct b.invoice_id) as inv_id,group_concat(distinct b.doc_submission_mst_id) as sub_id, group_concat(distinct c.invoice_no) as invoice_no, sum(b.net_invo_value) as net_invo_value, group_concat(distinct a.bank_ref_no) as bank_ref_no,group_concat(distinct a.submit_date) as bank_ref_date, sum(case when a.submit_type=1 then b.net_invo_value else 0 end) as sub_collection, group_concat(distinct a.negotiation_date) as negotiation_date, sum(a.total_negotiated_amount) as total_negotiated_amount, group_concat(distinct c.lc_sc_id) as lc_sc_id, c.is_lc,sum(c.invoice_quantity) as invoice_quantity
                            FROM com_export_doc_submission_mst a, com_export_doc_submission_invo b,  com_export_invoice_ship_mst c,com_export_proceed_realization d
                            WHERE b.doc_submission_mst_id=a.id and b.invoice_id=c.id and d.invoice_bill_id=a.id and d.is_invoice_bill='1'  and c.is_lc=1 AND a.id in($submit_id_lc) and  a.status_active=1 and a.is_deleted=0
                            group by d.id , c.is_lc
                            order by  c.lc_sc_id,d.id");
                        }
                        else
                        {
                            $sql_re=sql_select("SELECT d.id as rlz_id, LISTAGG(CAST( b.invoice_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.invoice_id) as inv_id, b.doc_submission_mst_id as sub_id, LISTAGG(CAST( c.invoice_no  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.invoice_no) as invoice_no, sum(b.net_invo_value) as net_invo_value, a.bank_ref_no , a.submit_date  as bank_ref_date, sum(case when a.submit_type=1 then b.net_invo_value else 0 end) as sub_collection, a.negotiation_date , sum(a.total_negotiated_amount) as total_negotiated_amount, b.lc_sc_id as lc_sc_id, c.is_lc,sum(c.invoice_quantity) as invoice_quantity
                            FROM com_export_doc_submission_mst a, com_export_doc_submission_invo b,  com_export_invoice_ship_mst c,com_export_proceed_realization d
                            WHERE b.doc_submission_mst_id=a.id and b.invoice_id=c.id and d.invoice_bill_id=a.id and d.is_invoice_bill='1' and c.is_lc=1 AND a.id in($submit_id_lc) and  a.status_active=1 and a.is_deleted=0
                            AND b.status_active = 1 AND b.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0 AND d.status_active = 1 AND d.is_deleted = 0
                            group by d.id , b.doc_submission_mst_id,a.bank_ref_no ,a.submit_date,a.negotiation_date,b.lc_sc_id,c.is_lc
                            order by b.lc_sc_id,d.id ");
                            
                        }

                        foreach($sql_re as $result)
                        {
                            if($sub_inv_id_lc==0) $sub_inv_id_lc=$result[csf("inv_id")]; else $sub_inv_id_lc=$sub_inv_id_lc.",".$result[csf("inv_id")];
                            $submit_inv_arr[]=$result[csf("inv_id")];
                            $realize_lc_arr[$result[csf("rlz_id")]]['rlz_id']=$result[csf("rlz_id")];
                            $realize_lc_arr[$result[csf("rlz_id")]]['inv_id']=$result[csf("inv_id")];
                            $realize_lc_arr[$result[csf("rlz_id")]]['sub_id']=$result[csf("sub_id")];
                            $realize_lc_arr[$result[csf("rlz_id")]]['invoice_no']=$result[csf("invoice_no")];
                            $realize_lc_arr[$result[csf("rlz_id")]]['net_invo_value']=$result[csf("net_invo_value")];
                            $realize_lc_arr[$result[csf("rlz_id")]]['bank_ref_no']=$result[csf("bank_ref_no")];
                            $realize_lc_arr[$result[csf("rlz_id")]]['invoice_quantity']=$result[csf("invoice_quantity")];
                            $realize_lc_arr[$result[csf("rlz_id")]]['sub_collection']=$result[csf("sub_collection")];
                            $realize_lc_arr[$result[csf("rlz_id")]]['bank_ref_date']=$result[csf("bank_ref_date")];
                            //$realize_lc_arr[$result[csf("rlz_id")]]['purchase_amount']=$result[csf("purchase_amount")];
                            $realize_lc_arr[$result[csf("rlz_id")]]['negotiation_date']=$result[csf("negotiation_date")];
                            $realize_lc_arr[$result[csf("rlz_id")]]['total_negotiated_amount']=$result[csf("total_negotiated_amount")];
                            $realize_lc_arr[$result[csf("rlz_id")]]['lc_sc_id']=$result[csf("lc_sc_id")];
                            $relz_ids .= $result[csf("rlz_id")].',';
                        }

	                    $all_relz_ids = ltrim(implode(",", array_unique(explode(",", chop($relz_ids, ",")))), ',');
                        $rlz_sql = "SELECT B.ID AS RLZ_ID ,A.ACCOUNT_HEAD , a.DOCUMENT_CURRENCY as DOCUMENT_CURRENCY FROM com_export_proceed_rlzn_dtls a, com_export_proceed_realization b
                        WHERE a.mst_id = b.id  and b.id in($all_relz_ids) and a.ACCOUNT_HEAD in(61,62) and  a.status_active=1 and b.status_active=1 ";
                        $sql_rlz_res= sql_select($rlz_sql);
                        $realize_foreign_com=array();
                        $realize_loc_com=array();
                        foreach($sql_rlz_res as $row)
                        {
                            $realize_foreign_com[$row[("RLZ_ID")]][$row[("ACCOUNT_HEAD")]]['for_com']=$row["DOCUMENT_CURRENCY"];
                            $realize_loc_com[$row[("RLZ_ID")]][$row[("ACCOUNT_HEAD")]]['loc_com']=$row["DOCUMENT_CURRENCY"];
                        }


                        if($sub_inv_id_lc!=0) $sub_inv_id_lc=implode(",",array_unique(explode(",",$sub_inv_id_lc)));
                        //var_dump($realize_lc_arr);die;
                        //var_dump($relize_arr);
                        $submit_id_lc=implode(",",array_unique(explode(",",$submit_id_lc)));
                        $k = 1;$realize_sc_arr=array();$submit_id_sc=0;
                        //$sc_relize_arr=array();$sc_distributed_arr=array();

                        if(!empty($sc_id_arr))
                        {
                            $submision_id_sc_rlz="";
                            //echo "select doc_submission_mst_id as sub_id from com_export_doc_submission_invo where is_lc=2 and lc_sc_id in(".implode(',',$sc_id_arr).")";die;
                            $sub_rlz_sql=sql_select("select doc_submission_mst_id as sub_id from com_export_doc_submission_invo where is_lc=2 and lc_sc_id in(".implode(',',$sc_id_arr).")");
                            //echo "<pre>";print_r($sub_rlz_sql);die;
                            foreach($sub_rlz_sql as $row)
                            {
                                $sub_lc_ids[$row[csf("sub_id")]]=$row[csf("sub_id")];
                            }
                            $submision_id_sc_rlz=implode(",",$sub_lc_ids);
                            //echo $submision_id_sc_rlz.test;die;
                            if($submision_id_sc_rlz!="" || !empty($cashInAdv_sc_id_arr))
                            {
                                $submision_id_sc_rlz=array_chunk(array_unique(explode(",",$submision_id_sc_rlz)),999);
                                if($db_type==0)
                                {
                                    $sql_rlz_query="select a.id as relz_id, a.invoice_bill_id as sub_id, a.received_date, b.type, b.account_head, b.document_currency, c.lc_sc_id , null as invoice_no, 1 as invoice_type from com_export_proceed_realization a, com_export_proceed_rlzn_dtls b, com_export_doc_submission_invo c where a.id=b.mst_id and c.doc_submission_mst_id=a.invoice_bill_id";
                                    
                                    $p=1;
                                    foreach($submision_id_sc_rlz as $sub_id_sc_rlz)
                                    {
                                        if($p==1) $sql_rlz_query .=" and (a.invoice_bill_id in(".implode(',',$sub_id_sc_rlz).")"; else  $sql_rlz_query .=" or a.invoice_bill_id  in(".implode(',',$sub_id_sc_rlz).")";
                                        $p++;
                                    }
                                    $sql_rlz_query .=") and a.is_invoice_bill=1 and a.status_active=1 and a.is_deleted=0 group by b.id order by c.lc_sc_id,a.id";
                                }
                                else if($db_type==2)
                                {
                                    $sql_rlz_query="select a.id as relz_id, a.invoice_bill_id as sub_id, a.received_date, b.type, b.account_head, b.document_currency, c.lc_sc_id, null as invoice_no, 1 as invoice_type from com_export_proceed_realization a, com_export_proceed_rlzn_dtls b, com_export_doc_submission_invo c where a.id=b.mst_id and c.doc_submission_mst_id=a.invoice_bill_id ";
                                    //echo $sql_rlz_query;die;
                                    $p=1;
                                    foreach($submision_id_sc_rlz as $sub_id_sc_rlz)
                                    {
                                        if($p==1) $sql_rlz_query .=" and (a.invoice_bill_id in(".implode(',',$sub_id_sc_rlz).")"; else  $sql_rlz_query .=" or a.invoice_bill_id  in(".implode(',',$sub_id_sc_rlz).")";
                                        $p++;
                                    }
                                    $sql_rlz_query .=") and a.is_invoice_bill=1 and a.status_active=1 and a.is_deleted=0 and b.status_active = 1 and b.is_deleted=0
                                    group by b.id , b.type, b.account_head, b.document_currency,a.id, a.invoice_bill_id, a.received_date, c.lc_sc_id";
                                }

                                //print_r($submision_id_sc_rlz);die;
                                if($sql_rlz_query) $sql_rlz_query .= " union all ";
                                //echo $sql_rlz_query;die;
                                //implode(",",array_filter(array_unique($cashInAdv_sc_id_arr)));


                                $cashInAdv_sc_id = implode("','",array_filter(array_unique($cashInAdv_sc_id_arr)));
                                $cashScCond=""; $cashInAdv_sc_id_arr=explode(",",$cashInAdv_sc_id);
                                if($cashInAdv_sc_id =="") {$cashInAdv_sc_id=0;}
                                $cashINAdvScIdCond = "";
                                if($db_type==2 && count($cashInAdv_sc_id_arr)>999)
                                {
                                    $job_no_chunk_arr=array_chunk($cashInAdv_sc_id_arr,999) ;
                                    foreach($job_no_chunk_arr as $chunk_arr)
                                    {
                                        $chunk_arr_value=implode(",",$chunk_arr);
                                        $cashScCond.=" c.lc_sc_id in($chunk_arr_value) or ";
                                    }

                                    $cashINAdvScIdCond.=" and (".chop($cashScCond,'or ').")";
                                }
                                else
                                {
                                    $cashINAdvScIdCond=" and c.lc_sc_id in($cashInAdv_sc_id)";
                                }

                                $sql_rlz_query .= " select a.id as relz_id, null as sub_id, a.received_date, b.type, b.account_head, b.document_currency, a.invoice_bill_id as sub_id, c.invoice_no, 2 as invoice_type 
                                from com_export_proceed_realization a, com_export_proceed_rlzn_dtls b , com_export_invoice_ship_mst c 
                                where a.id=b.mst_id and A.INVOICE_BILL_ID = c.id  and a.is_invoice_bill=2 and a.status_active=1 and a.is_deleted=0 and b.status_active = 1 and b.is_deleted=0 $cashINAdvScIdCond  and c.is_lc= 2 
                                group by b.id , b.type, b.account_head, b.document_currency, a.id, a.invoice_bill_id, a.received_date,c.invoice_no order by sub_id, relz_id";


                                //echo $sql_rlz_query."<br>nahid**************<br>";die;
                                $sql_rlz=sql_select($sql_rlz_query);
                                //print_r($sql_rlz);die;
                                foreach($sql_rlz as $row)
                                {
                                    if($submit_id_sc==0) $submit_id_sc=$row[csf("sub_id")]; else $submit_id_sc= $submit_id_sc.",".$row[csf("sub_id")];
                                    $realize_sc_arr[$row[csf('relz_id')]]['sub_id']=$row[csf('sub_id')];
                                    $realize_sc_arr[$row[csf('relz_id')]]['rlz_received_date']=$row[csf('received_date')];
                                    if($row[csf('invoice_type')] == 2){
                                        $realize_sc_arr[$row[csf("relz_id")]]['invoice_no']=$row[csf('invoice_no')];
                                    }


                                    if($row[csf('type')]==0)
                                    {
                                        $realize_sc_arr[$row[csf('relz_id')]]['deduct_realize']+=$row[csf('document_currency')];
                                        $payment_realized_deduction+=$row[csf('document_currency')];
                                    }
                                    else
                                    {
                                        $realize_sc_arr[$row[csf('relz_id')]]['distribute_realize']+=$row[csf('document_currency')];
                                        $payment_realized+=$row[csf('document_currency')];
                                    }

                                    if(!in_array($row[csf('relz_id')],$temp_arr))
                                    {
                                        //$payment_realized+=$row[csf('distribute_realize')];
                                        //$payment_realized_deduction+=$row[csf('deduct_realize')];
                                        $temp_arr[]=$row[csf('relz_id')];
                                    }

                                    if($row[csf("account_head")]==6)
                                    {
                                        $realize_sc_arr[$row[csf("relz_id")]]["erq"]+=$row[csf("document_currency")];
                                    }
                                    else if($row[csf("account_head")]==5)
                                    {
                                        $realize_sc_arr[$row[csf("relz_id")]]["btb"]+=$row[csf("document_currency")];
                                        $total_btb_margine+=$row[csf("document_currency")];
                                    }
                                    else if($row[csf("account_head")]==10)
                                    {
                                        $realize_sc_arr[$row[csf("relz_id")]]["cd"]+=$row[csf("document_currency")];
                                    }
                                    else if($row[csf("account_head")]==15)
                                    {
                                        $realize_sc_arr[$row[csf("relz_id")]]["cc"]+=$row[csf("document_currency")];
                                    }
                                    else if($row[csf("account_head")]==65)
                                    {
                                        $realize_sc_arr[$row[csf("relz_id")]]["fdbc"]+=$row[csf("document_currency")];
                                    }
                                    else if($row[csf("account_head")]==81)
                                    {
                                        $realize_sc_arr[$row[csf("relz_id")]]["sun_ac"]+=$row[csf("document_currency")];
                                    }
                                    else if($row[csf("account_head")]==11)
                                    {
                                        $realize_sc_arr[$row[csf("relz_id")]]["std"]+=$row[csf("document_currency")];
                                    }
                                }
                            }
                        }
                        //echo "<pre>";print_r($realize_sc_arr);die;
                        $submit_id_sc=implode(",",array_unique(explode(",",$submit_id_sc)));
                        //echo $submit_id_sc.tes;die;
                        if($submit_id_sc == "") $submit_id_sc = 0;

                        if($db_type==0)
                        {
                            $sql_sc=sql_select("SELECT d.id as rlz_id, group_concat(distinct b.invoice_id) as inv_id,group_concat(distinct a.id) as sub_id,  group_concat(distinct c.invoice_no  ) as invoice_no, sum(b.net_invo_value) as net_invo_value, group_concat(distinct a.bank_ref_no) as bank_ref_no, group_concat(distinct a.submit_date) as bank_ref_date, sum(case when a.submit_type=1 then b.net_invo_value else 0 end) as sub_collection, group_concat(distinct a.negotiation_date) as negotiation_date, sum(a.total_negotiated_amount) as total_negotiated_amount, group_concat(distinct c.lc_sc_id) as lc_sc_id, c.is_lc,sum(c.invoice_quantity) as invoice_quantity
                            FROM com_export_doc_submission_mst a, com_export_doc_submission_invo b,  com_export_invoice_ship_mst c,com_export_proceed_realization d
                            WHERE b.doc_submission_mst_id=a.id and b.invoice_id=c.id and d.invoice_bill_id=a.id and d.is_invoice_bill='1'  and c.is_lc=2 AND a.id in($submit_id_sc) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0
                            group by d.id, c.is_lc
                            order by b.lc_sc_id,d.id");
                        }
                        else if($db_type==2)
                        {                            
                            $sql_sc=sql_select("SELECT d.id as rlz_id, rtrim(xmlagg(xmlelement(e,b.invoice_id,',').extract('//text()') order by b.invoice_id).GetClobVal(),',') as inv_id, a.id as sub_id, rtrim(xmlagg(xmlelement(e,c.invoice_no,',').extract('//text()') order by c.invoice_no).GetClobVal(),',') as invoice_no, sum(b.net_invo_value) as net_invo_value,a.bank_ref_no,a.submit_date as bank_ref_date, sum(case when a.submit_type=1 then b.net_invo_value else 0 end) as sub_collection,a.negotiation_date, sum(a.total_negotiated_amount) as total_negotiated_amount,b.lc_sc_id as lc_sc_id, c.is_lc,sum(c.invoice_quantity) as invoice_quantity
                            FROM com_export_doc_submission_mst a, com_export_doc_submission_invo b,  com_export_invoice_ship_mst c, com_export_proceed_realization d
                            WHERE b.doc_submission_mst_id=a.id and b.invoice_id=c.id and d.invoice_bill_id=a.id and d.is_invoice_bill='1'  and c.is_lc=2 AND a.id in($submit_id_sc) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0
                            group by d.id,a.id,a.bank_ref_no,a.submit_date,a.negotiation_date,b.lc_sc_id,c.is_lc
                            order by b.lc_sc_id,d.id");
                            
                        }
                        
                    
                        foreach($sql_sc as $result)
                        {
                            if($sub_inv_id_sc==0) $sub_inv_id_sc=$result[csf("inv_id")]->load(); else $sub_inv_id_sc=$sub_inv_id_sc.",".$result[csf("inv_id")]->load();
                            $submit_inv_arr[]=$result[csf("inv_id")]->load();
                            $realize_sc_arr[$result[csf("rlz_id")]]['rlz_id']=$result[csf("rlz_id")];
                            $realize_sc_arr[$result[csf("rlz_id")]]['inv_id']=$result[csf("inv_id")]->load();
                            $realize_sc_arr[$result[csf("rlz_id")]]['sub_id']=$result[csf("sub_id")];
                            $realize_sc_arr[$result[csf("rlz_id")]]['invoice_no']=$result[csf("invoice_no")]->load();
                            $realize_sc_arr[$result[csf("rlz_id")]]['invoice_quantity']=$result[csf("invoice_quantity")];
                            $realize_sc_arr[$result[csf("rlz_id")]]['net_invo_value']=$result[csf("net_invo_value")];
                            $realize_sc_arr[$result[csf("rlz_id")]]['bank_ref_no']=$result[csf("bank_ref_no")];
                            $realize_sc_arr[$result[csf("rlz_id")]]['sub_collection']=$result[csf("sub_collection")];
                            $realize_sc_arr[$result[csf("rlz_id")]]['bank_ref_date']=$result[csf("bank_ref_date")];
                            //$realize_sc_arr[$result[csf("rlz_id")]]['purchase_amount']=$result[csf("purchase_amount")];
                            $realize_sc_arr[$result[csf("rlz_id")]]['negotiation_date']=$result[csf("negotiation_date")];
                            $realize_sc_arr[$result[csf("rlz_id")]]['total_negotiated_amount']=$result[csf("total_negotiated_amount")];
                            $realize_sc_arr[$result[csf("rlz_id")]]['lc_sc_id']=$result[csf("lc_sc_id")];
                            $realize_sc_arr[$result[csf("rlz_id")]]['is_lc']=$result[csf("is_lc")];
                            $relz_sc_ids .= $result[csf("rlz_id")].',';
                        }

	                    $all_relz_sc_ids = ltrim(implode(",", array_unique(explode(",", chop($relz_sc_ids, ",")))), ',');
                        $rlz_sc_sql = "SELECT B.ID AS RLZ_ID ,A.ACCOUNT_HEAD , a.DOCUMENT_CURRENCY as DOCUMENT_CURRENCY FROM com_export_proceed_rlzn_dtls a, com_export_proceed_realization b
                        WHERE a.mst_id = b.id  and b.id in($all_relz_sc_ids) and a.ACCOUNT_HEAD in(61,62) and  a.status_active=1 and b.status_active=1 ";
                        $sql_rlz_sc_res= sql_select($rlz_sc_sql);
                        $realize_sc_foreign_com=array();
                        $realize_sc_loc_com=array();
                        foreach($sql_rlz_sc_res as $row)
                        {
                            $realize_sc_foreign_com[$row[("RLZ_ID")]][$row[("ACCOUNT_HEAD")]]['for_com']=$row["DOCUMENT_CURRENCY"];
                            $realize_sc_loc_com[$row[("RLZ_ID")]][$row[("ACCOUNT_HEAD")]]['loc_com']=$row["DOCUMENT_CURRENCY"];
                        }



                        if($sub_inv_id_sc!=0) $sub_inv_id_sc=implode(",",array_unique(explode(",",$sub_inv_id_sc)));
                        //var_dump($sub_inv_id_sc);die;
                        //echo $sub_inv_id_sc; die;

                        $submit_id_lc=implode(",",array_unique(explode(",",$submit_id_lc)));
                        $submit_id_sc=implode(",",array_unique(explode(",",$submit_id_sc)));
                        $submission_id_lc=0;$submission_id_sc=0;$sub_as_collection=0;
                        $sub_lc_arr=array();
                        //echo "ttt<pre>";print_r($lc_id_arr);die;
                        if(!empty($lc_id_arr))
                        {
                            if($db_type==0)
                            {
                                $sql_re=sql_select("SELECT group_concat(distinct b.invoice_id) as inv_id,a.id as sub_id, group_concat(distinct c.invoice_no ) as invoice_no, sum(b.net_invo_value) as net_invo_value, a.bank_ref_no, a.submit_date, a.bank_ref_date, sum(case when a.submit_type=1 then b.net_invo_value else 0 end) as sub_collection, a.negotiation_date, sum(a.total_negotiated_amount) as total_negotiated_amount, c.lc_sc_id as lc_sc_id, c.is_lc, sum(c.invoice_quantity) as invoice_quantity
                                FROM com_export_doc_submission_mst a, com_export_doc_submission_invo b,  com_export_invoice_ship_mst c
                                WHERE b.doc_submission_mst_id=a.id and b.invoice_id=c.id and b.is_lc='1' and a.id not in($submit_id_lc) AND b.lc_sc_id in(".implode(',',$lc_id_arr).")  AND a.status_active='1' and a.is_deleted=0 and a.entry_form=40
                                group by a.id, a.bank_ref_no, a.submit_date, a.bank_ref_date, a.negotiation_date, c.lc_sc_id, c.is_lc
                                order by c.lc_sc_id,a.id");
                            }
                            else if($db_type==2)
                            {
                                $sql_re=sql_select("SELECT LISTAGG(CAST(  b.invoice_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.invoice_id) as inv_id,a.id as sub_id, LISTAGG(CAST(   c.invoice_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.invoice_no) as invoice_no, sum(b.net_invo_value) as net_invo_value, a.bank_ref_no, a.submit_date, a.bank_ref_date, sum(case when a.submit_type=1 then b.net_invo_value else 0 end) as sub_collection, a.negotiation_date, sum(a.total_negotiated_amount) as total_negotiated_amount, c.lc_sc_id as lc_sc_id, c.is_lc,sum(c.invoice_quantity) as invoice_quantity
                                FROM com_export_doc_submission_mst a, com_export_doc_submission_invo b,  com_export_invoice_ship_mst c
                                WHERE b.doc_submission_mst_id=a.id and b.invoice_id=c.id and b.is_lc='1' and a.id not in($submit_id_lc) AND b.lc_sc_id in(".implode(',',$lc_id_arr).")  AND a.status_active='1' and a.is_deleted=0  and a.entry_form=40
                                group by a.id, a.bank_ref_no, a.submit_date, a.bank_ref_date, a.negotiation_date, c.lc_sc_id, c.is_lc
                                order by c.lc_sc_id, a.id");
                            }
                        }

                        foreach($sql_re as $result)
                        {
                            if($submission_id_lc==0) $submission_id_lc=$result[csf("sub_id")]; else $submission_id_lc=$submission_id_lc.",".$result[csf("sub_id")];
                            if($sub_inv_id_lc==0) $sub_inv_id_lc=$result[csf("inv_id")]; else $sub_inv_id_lc=$sub_inv_id_lc.",".$result[csf("inv_id")];
                            $submit_inv_arr[]=$result[csf("inv_id")];
                            $sub_lc_arr[$result[csf("sub_id")]]['sub_id']=$result[csf("sub_id")];
                            $sub_lc_arr[$result[csf("sub_id")]]['inv_id']=$result[csf("inv_id")];
                            $sub_lc_arr[$result[csf("sub_id")]]['invoice_no']=$result[csf("invoice_no")];
                            $sub_lc_arr[$result[csf("sub_id")]]['invoice_quantity']=$result[csf("invoice_quantity")];
                            $sub_lc_arr[$result[csf("sub_id")]]['net_invo_value']=$result[csf("net_invo_value")];
                            $sub_lc_arr[$result[csf("sub_id")]]['bank_ref_no']=$result[csf("bank_ref_no")];
                            $sub_lc_arr[$result[csf("sub_id")]]['sub_collection']=$result[csf("sub_collection")];
                            $sub_lc_arr[$result[csf("sub_id")]]['submit_date']=$result[csf("submit_date")];
                            $sub_lc_arr[$result[csf("sub_id")]]['bank_ref_date']=$result[csf("bank_ref_date")];
                            $sub_lc_arr[$result[csf("sub_id")]]['purchase_amount']=$result[csf("purchase_amount")];
                            $sub_lc_arr[$result[csf("sub_id")]]['negotiation_date']=$result[csf("negotiation_date")];
                            $sub_lc_arr[$result[csf("sub_id")]]['total_negotiated_amount']=$result[csf("total_negotiated_amount")];
                            $sub_lc_arr[$result[csf("sub_idsub_lc_arr")]]['lc_sc_id']=$result[csf("lc_sc_id")];
                            $sub_lc_arr[$result[csf("sub_id")]]['is_lc']=$result[csf("is_lc")];
                            $sub_as_collection+=($result[csf("net_invo_value")]);
                        }

                        $sub_sc_arr=array();
                        if(!empty($sc_id_arr))
                        {
                            // LISTAGG(CAST( b.id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.id) as tr_id
                            if($db_type==0)
                            {
                                $sql_sc=sql_select("SELECT group_concat(distinct b.invoice_id) as inv_id,a.id as sub_id, group_concat(distinct c.invoice_no) as invoice_no, sum(b.net_invo_value) as net_invo_value, a.bank_ref_no, a.submit_date, a.bank_ref_date, sum(case when a.submit_type=1 then b.net_invo_value else 0 end) as sub_collection, a.negotiation_date, sum(a.total_negotiated_amount) as total_negotiated_amount,  group_concat(distinct c.lc_sc_id) as lc_sc_id, c.is_lc,sum(c.invoice_quantity) as invoice_quantity, group_concat(distinct c.id) as sub_invoice_id
                                FROM com_export_doc_submission_mst a, com_export_doc_submission_invo b,  com_export_invoice_ship_mst c
                                WHERE b.doc_submission_mst_id=a.id and b.invoice_id=c.id  and b.is_lc='2' and a.id not in($submit_id_sc) AND b.lc_sc_id in(".implode(',',$sc_id_arr).") and a.company_id='$cbo_company_name' AND a.status_active='1' and a.is_deleted=0 and a.entry_form=40
                                group by a.id, a.bank_ref_no, a.submit_date, a.bank_ref_date, a.negotiation_date, c.is_lc
                                order by c.lc_sc_id,a.id");
                            }
                            else if($db_type==2)
                            {
                                $sql_sc=sql_select("SELECT LISTAGG(CAST( b.invoice_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.invoice_id) as inv_id,a.id as sub_id, LISTAGG(CAST(c.invoice_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.invoice_no)  as invoice_no, sum(b.net_invo_value) as net_invo_value, a.bank_ref_no, a.submit_date, a.bank_ref_date, sum(case when a.submit_type=1 then b.net_invo_value else 0 end) as sub_collection, a.negotiation_date, sum(a.total_negotiated_amount) as total_negotiated_amount,  LISTAGG(CAST( c.lc_sc_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.lc_sc_id) as lc_sc_id, c.is_lc,sum(c.invoice_quantity) as invoice_quantity, LISTAGG(CAST( c.id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.id) as sub_invoice_id
                                FROM com_export_doc_submission_mst a, com_export_doc_submission_invo b,  com_export_invoice_ship_mst c
                                WHERE b.doc_submission_mst_id=a.id and b.invoice_id=c.id  and b.is_lc='2' and a.id not in($submit_id_sc) AND b.lc_sc_id in(".implode(',',$sc_id_arr).") and a.company_id='$cbo_company_name' AND a.status_active='1' and a.is_deleted=0 and a.entry_form=40
                                group by a.id, a.bank_ref_no, a.submit_date, a.bank_ref_date, a.negotiation_date, c.is_lc
                                order by a.id");
                            }
                        }

                        foreach($sql_sc as $result)
                        {
                            if($submission_id_sc=0) $submission_id_sc=$result[csf("sub_id")]; else $submission_id_sc=$submission_id_sc.",".$result[csf("sub_id")];
                            //if($sc_submit_id==0) $sc_submit_id=$result[csf("sub_id")]; else $sc_submit_id=$sc_submit_id.",".$result[csf("sub_id")];
                            if($sub_inv_id_sc==0) $sub_inv_id_sc=$result[csf("sub_invoice_id")]; else $sub_inv_id_sc=$sub_inv_id_sc.",".$result[csf("sub_invoice_id")];

                            $submit_inv_arr[]=$result[csf("inv_id")];
                            $sub_sc_arr[$result[csf("sub_id")]]['sub_id']=$result[csf("sub_id")];
                            $sub_sc_arr[$result[csf("sub_id")]]['inv_id']=$result[csf("inv_id")];
                            $sub_sc_arr[$result[csf("sub_id")]]['invoice_no']=$result[csf("invoice_no")];
                            $sub_sc_arr[$result[csf("sub_id")]]['invoice_quantity']=$result[csf("invoice_quantity")];
                            $sub_sc_arr[$result[csf("sub_id")]]['net_invo_value']=$result[csf("net_invo_value")];
                            $sub_sc_arr[$result[csf("sub_id")]]['bank_ref_no']=$result[csf("bank_ref_no")];
                            $sub_sc_arr[$result[csf("sub_id")]]['sub_collection']=$result[csf("sub_collection")];
                            $sub_sc_arr[$result[csf("sub_id")]]['submit_date']=$result[csf("submit_date")];
                            $sub_sc_arr[$result[csf("sub_id")]]['bank_ref_date']=$result[csf("bank_ref_date")];
                            $sub_sc_arr[$result[csf("sub_id")]]['purchase_amount']=$result[csf("purchase_amount")];
                            $sub_sc_arr[$result[csf("sub_id")]]['negotiation_date']=$result[csf("negotiation_date")];
                            $sub_sc_arr[$result[csf("sub_id")]]['total_negotiated_amount']=$result[csf("total_negotiated_amount")];
                            $sub_sc_arr[$result[csf("sub_id")]]['lc_sc_id']=$result[csf("lc_sc_id")];
                            $sub_sc_arr[$result[csf("sub_id")]]['is_lc']=$result[csf("is_lc")];

                            $sub_as_collection+=($result[csf("net_invo_value")]);
                        }
                        //var_dump($sub_sc_arr);die;


                        $sub_buyer_arr=array();
 
                        if(!empty($sc_id_arr))
                        {
                            // LISTAGG(CAST( b.id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.id) as tr_id
                            if($db_type==0)
                            {
                            $buyer_sub_sc="SELECT a.id as sub_id, a.buyer_id, a.submit_date, group_concat(distinct b.invoice_id) as inv_id, sum(b.net_invo_value) as net_invo_value, group_concat(distinct c.invoice_no) as invoice_no, c.lc_sc_id as lc_sc_id, sum(c.invoice_quantity) as invoice_quantity
                                FROM com_export_doc_submission_mst a, com_export_doc_submission_invo b,  com_export_invoice_ship_mst c
                                WHERE b.doc_submission_mst_id=a.id and b.invoice_id=c.id and b.is_converted=0   and b.is_lc='2' AND b.lc_sc_id in(".implode(',',$sc_id_arr).") and a.company_id='$cbo_company_name' AND a.status_active='1' and a.is_deleted=0 AND b.status_active='1' and b.is_deleted=0 and a.entry_form=39 and b.invoice_id not in(select q.invoice_id from com_export_doc_submission_mst p, com_export_doc_submission_invo q where p.id=q.doc_submission_mst_id and p.entry_form=40 and p.status_active=1 and p.is_deleted=0 and q.status_active=1 and q.is_deleted=0)
                                group by a.id, a.buyer_id, a.submit_date, c.lc_sc_id order by a.id";
                            }
                            else if($db_type==2)
                            {
                                $buyer_sub_sc="SELECT a.id as sub_id, a.buyer_id, a.submit_date, LISTAGG(CAST( b.invoice_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.invoice_id) as inv_id, sum(b.net_invo_value) as net_invo_value, LISTAGG(CAST(c.invoice_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.invoice_no)  as invoice_no, c.lc_sc_id as lc_sc_id, sum(c.invoice_quantity) as invoice_quantity
                                FROM com_export_doc_submission_mst a, com_export_doc_submission_invo b,  com_export_invoice_ship_mst c
                                WHERE b.doc_submission_mst_id=a.id and b.invoice_id=c.id and b.is_converted=0 and b.is_lc='2' AND b.lc_sc_id in(".implode(',',$sc_id_arr).") and a.company_id='$cbo_company_name' AND a.status_active='1' and a.is_deleted=0 AND b.status_active='1' and b.is_deleted=0 and a.entry_form=39 and b.invoice_id not in(select q.invoice_id from com_export_doc_submission_mst p, com_export_doc_submission_invo q where p.id=q.doc_submission_mst_id and p.entry_form=40 and p.status_active=1 and p.is_deleted=0 and q.status_active=1 and q.is_deleted=0)
                                group by a.id, a.buyer_id, a.submit_date, c.lc_sc_id order by a.id";
                                
                            }
                            //echo $buyer_sub_sc;
                            $sql_buyer_sub_sc=sql_select($buyer_sub_sc);
                        }

                        foreach($sql_buyer_sub_sc as $result)
                        {
                            if($submission_id_sc=0) $submission_id_sc=$result[csf("sub_id")]; else $submission_id_sc=$submission_id_sc.",".$result[csf("sub_id")];
                            //if($sc_submit_id==0) $sc_submit_id=$result[csf("sub_id")]; else $sc_submit_id=$sc_submit_id.",".$result[csf("sub_id")];
                            if($sub_inv_id_sc==0) $sub_inv_id_sc=$result[csf("inv_id")]; else $sub_inv_id_sc=$sub_inv_id_sc.",".$result[csf("inv_id")];

                            $submit_inv_arr[]=$result[csf("inv_id")];
                            $sub_buyer_arr[$result[csf("sub_id")]]['sub_id']=$result[csf("sub_id")];
                            $sub_buyer_arr[$result[csf("sub_id")]]['buyer_id']=$result[csf("buyer_id")];
                            $sub_buyer_arr[$result[csf("sub_id")]]['bank_ref_date']=$result[csf("submit_date")];
                            $sub_buyer_arr[$result[csf("sub_id")]]['inv_id']=$result[csf("inv_id")];
                            $sub_buyer_arr[$result[csf("sub_id")]]['invoice_no']=$result[csf("invoice_no")];
                            $sub_buyer_arr[$result[csf("sub_id")]]['invoice_quantity']=$result[csf("invoice_quantity")];
                            $sub_buyer_arr[$result[csf("sub_id")]]['net_invo_value']=$result[csf("net_invo_value")];
                            $sub_buyer_arr[$result[csf("sub_id")]]['lc_sc_id']=$result[csf("lc_sc_id")];
                            $sub_as_collection+=($result[csf("net_invo_value")]);
                        }
                        
                        $inv_arr=array();$in_hand=0;
                        if(!empty($lc_id_arr))
                        {
                            if($db_type==2)
                            {
                                $sql_lc=sql_select("select id, invoice_no, invoice_date, invoice_quantity, net_invo_value as invoice_value, lc_sc_id, is_lc from com_export_invoice_ship_mst  where is_lc=1 and lc_sc_id in(".implode(',',$lc_id_arr).") and id not in($sub_inv_id_lc) and  status_active=1 and is_deleted=0" );
                            }
                            else if($db_type==0)
                            {
                                $sql_lc=sql_select("select id, invoice_no, invoice_date, invoice_quantity, net_invo_value as invoice_value, lc_sc_id, is_lc from com_export_invoice_ship_mst  where is_lc=1 and lc_sc_id in(".implode(',',$lc_id_arr).") and id not in($sub_inv_id_lc) and  status_active=1 and is_deleted=0" );
                            }
                            foreach($sql_lc as $row)
                            {
                                $inv_arr[$row[csf("id")]]["id_lc"]=$row[csf("id")];
                                $inv_arr[$row[csf("id")]]["invoice_no"]=$row[csf("invoice_no")];
                                $inv_arr[$row[csf("id")]]["invoice_date"]=$row[csf("invoice_date")];
                                $inv_arr[$row[csf("id")]]["invoice_quantity"]=$row[csf("invoice_quantity")];
                                $inv_arr[$row[csf("id")]]["invoice_value"]=$row[csf("invoice_value")];
                                $inv_arr[$row[csf("id")]]["lc_sc_id"]=$row[csf("lc_sc_id")];
                                $inv_arr[$row[csf("id")]]["is_lc"]=$row[csf("is_lc")];
                                $in_hand +=$row[csf("invoice_value")];
                            }
                        }

                        // print_r($sc_id_arr);die;

                        if(!empty($sc_id_arr))
                        {
                            //echo $sub_inv_id_sc."=========================================================";die;
                            if($cashInAdv_sc_id) $without_cashInAdvSc = " and lc_sc_id not in($cashInAdv_sc_id) "; else $without_cashInAdvSc = "";
                            if($db_type==2)
                            {
                                $sql_lc=sql_select("select id, invoice_no, invoice_date, invoice_quantity, net_invo_value as invoice_value, lc_sc_id, is_lc from com_export_invoice_ship_mst  where is_lc=2 and lc_sc_id in(".implode(',',$sc_id_arr).")  and id not in($sub_inv_id_sc)  and  status_active=1 and is_deleted=0");
                            }
                            else if($db_type==0)
                            {
                                $sql_lc=sql_select("select id, invoice_no, invoice_date, invoice_quantity, net_invo_value as invoice_value, lc_sc_id, is_lc from com_export_invoice_ship_mst  where is_lc=2 and lc_sc_id in(".implode(',',$sc_id_arr).") $without_cashInAdvSc and id not in($sub_inv_id_sc)  and  status_active=1 and is_deleted=0");
                            }
                            //echo "select id, invoice_no, invoice_date, invoice_quantity, net_invo_value as invoice_value, lc_sc_id, is_lc from com_export_invoice_ship_mst  where is_lc=2 and lc_sc_id in(".implode(',',$sc_id_arr).") $without_cashInAdvSc and id not in($sub_inv_id_sc)  and  status_active=1 and is_deleted=0";die;
                            foreach($sql_lc as $row)
                            {
                                $inv_arr[$row[csf("id")]]["id_sc"]=$row[csf("id")];
                                $inv_arr[$row[csf("id")]]["invoice_no"]=$row[csf("invoice_no")];
                                $inv_arr[$row[csf("id")]]["invoice_date"]=$row[csf("invoice_date")];
                                $inv_arr[$row[csf("id")]]["invoice_quantity"]=$row[csf("invoice_quantity")];

                                $inv_arr[$row[csf("id")]]["invoice_value"]=$row[csf("invoice_value")];
                                $inv_arr[$row[csf("id")]]["lc_sc_id"]=$row[csf("lc_sc_id")];
                                $inv_arr[$row[csf("id")]]["is_lc"]=$row[csf("is_lc")];
                                $in_hand +=$row[csf("invoice_value")];
                            }
                        }
                        // var_dump($inv_arr);die;
                        ?>
                        <td style="vertical-align:top" width="550">
                            <table width="450" style="vertical-align:top">
                                <tr>
                                    <td width="220">Total File Value</td>
                                    <td width="115" align="right"></td>
                                    <td width="115" style="font-weight:bold;" align="right">&nbsp;&nbsp;
                                        <?
                                        $file_value=$total_sales_contct_value_top+$balance+$total_direct_sc_val+$total_direct_lc_val;
                                        //echo number_format($file_value,2);//."_____".$lc_id."_____".$sc_id
                                        echo number_format($net_tot_lc_val,2);//."_____".$lc_id."_____".$sc_id
                                            
                                        $attach_order_id=""; $powiseJobNoArr=array();
                                        $attach_value_sales=$attach_value_lc=0;
                                        if (!empty($sc_id_arr))
                                        {
                                            $attach_sales_sql="select b.id as po_id, c.job_no, (c.job_quantity*c.total_set_qnty) as job_quantity, (a.attached_qnty*c.total_set_qnty) as attached_qnty, a.attached_value
                                            from com_sales_contract_order_info a, wo_po_break_down b, wo_po_details_master c 
                                            where a.wo_po_break_down_id=b.id and b.job_no_mst=c.job_no and a.com_sales_contract_id in(". implode(',',$sc_id_arr).") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
                                            //echo $attach_sales_sql;die;
                                            $attach_sales_sql_result=sql_select($attach_sales_sql);
                                            foreach($attach_sales_sql_result as $row)
                                            {
                                                $attachQty_sales+=$row[csf("attached_qnty")];
                                                $attach_value_sales+=$row[csf("attached_value")];
                                                $attach_order_id.=$row[csf("po_id")].",";
                                                $powiseJobNoArr[$row[csf("po_id")]]=$row[csf("job_no")];
                                                if ($jobwisejobQntySC[$row[csf("job_no")]]=="")
                                                {
                                                    $total_jobQntySC+=$row[csf("job_quantity")];
                                                    $jobwisejobQntySC[$row[csf("job_no")]]=$row[csf("job_no")];
                                                }
                                            }
                                        }
                                        //echo $total_jobQntySC;
                                        if (!empty($lc_id_arr))
                                        {
                                            $attach_lc_sql="select b.id as po_id, c.job_no, (c.job_quantity*c.total_set_qnty) as job_quantity, (a.attached_qnty*c.total_set_qnty) as attached_qnty, a.attached_value
                                            from com_export_lc_order_info a, wo_po_break_down b, wo_po_details_master c 
                                            where a.wo_po_break_down_id=b.id and b.job_no_mst=c.job_no and a.com_export_lc_id in(". implode(',',$lc_id_arr).") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
                                            $attach_lc_sql_result=sql_select($attach_lc_sql);
                                            foreach($attach_lc_sql_result as $row)
                                            {
                                                $attachQty_lc+=$row[csf("attached_qnty")];
                                                $attach_value_lc+=$row[csf("attached_value")];
                                                $attach_order_id.=$row[csf("po_id")].",";
                                                $powiseJobNoArr[$row[csf("po_id")]]=$row[csf("job_no")];
                                                if ($jobwisejobQntyLC[$row[csf("job_no")]]=="")
                                                {
                                                    $total_jobQntyLC+=$row[csf("job_quantity")];
                                                    $jobwisejobQntyLC[$row[csf("job_no")]]=$row[csf("job_no")];
                                                }
                                            }
                                        }
                                        $attach_order_id=implode(",",array_unique(explode(",",chop($attach_order_id,","))));
                                        $total_attach_value=$attach_value_sales+$attach_value_lc;
                                        $total_lcsc_attach_quantity=$attachQty_sales+$attachQty_lc;
                                        $total_job_quantity=$total_jobQntySC+$total_jobQntyLC;
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Total Attach Value</td>
                                    <td align="right"></td>
                                    <td align="right" style="font-weight:bold">&nbsp;&nbsp;
                                    <a href="##" onClick="fnc_attach_order_details('<? echo $cbo_company_name;?>','<? echo $txt_file_no;?>','<? echo $attach_order_id;?>','attach_order_details')"><? echo  number_format($total_attach_value,2); ?></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Total Shipment</td>
                                    <td align="right"></td>
                                    <td align="right" style="font-weight:bold">&nbsp;&nbsp;
                                    <?
                                    //var_dump($lc_id_arr);var_dump($sc_id_arr);
                                    $adjustment_arr=array();
                                    if(!empty($lc_id_arr))
                                    {
                                        if($db_type==0)
                                        {
                                            $sql_lc=sql_select("SELECT b.export_lc_no, a.id as id, a.invoice_no, a.invoice_quantity as current_invoice_qnty, a.invoice_value as current_invoice_value, a.net_invo_value
                                            from com_export_invoice_ship_mst a, com_export_lc b
                                            where a.lc_sc_id=b.id and a.lc_sc_id in(". implode(',',$lc_id_arr).") and a.is_lc=1 and a.status_active=1 and benificiary_id like '$cbo_company_name' and  buyer_id like '$cbo_buyer_name'");
                                        }
                                        else if($db_type==2)
                                        {
                                            $sql_lc=sql_select("SELECT b.export_lc_no, a.id as id, a.invoice_no, a.invoice_quantity as current_invoice_qnty, a.invoice_value as current_invoice_value, a.net_invo_value
                                            from com_export_invoice_ship_mst a, com_export_lc b
                                            where a.lc_sc_id=b.id and a.lc_sc_id in(". implode(',',$lc_id_arr).") and a.is_lc=1 and a.status_active=1 and benificiary_id like '$cbo_company_name' and  buyer_id like '$cbo_buyer_name'");
                                        }
                                        $total_shipment_val=0;$shp_inv_id=array();$total_discount=0;
                                        foreach($sql_lc as $row_lc_result)
                                        {
                                            $shp_inv_id[]=$row_lc_result[csf("id")];
                                            $total_shipment_val += $row_lc_result[csf("current_invoice_value")];
                                            $total_discount +=($row_lc_result[csf("current_invoice_value")]- $row_lc_result[csf("net_invo_value")]);

                                            $adjustment_arr[$row_lc_result[csf("id")]]["id"]=$row_lc_result[csf("id")];
                                            $adjustment_arr[$row_lc_result[csf("id")]]["lc_sc_no"]=$row_lc_result[csf("export_lc_no")];
                                            $adjustment_arr[$row_lc_result[csf("id")]]["invoice_no"]=$row_lc_result[csf("invoice_no")];
                                            $adjustment_arr[$row_lc_result[csf("id")]]["current_invoice_qnty"]=$row_lc_result[csf("current_invoice_qnty")];
                                            $adjustment_arr[$row_lc_result[csf("id")]]["current_invoice_value"]=$row_lc_result[csf("current_invoice_value")];
                                            $adjustment_arr[$row_lc_result[csf("id")]]["net_invo_value"]=$row_lc_result[csf("net_invo_value")];
                                        }
                                    }

                                    if(!empty($sc_id_arr))
                                    {
                                        if($db_type==0)
                                        {
                                            $sql_cur_ship ="SELECT contract_no, a.id as id, a.invoice_no, a.invoice_quantity as current_invoice_qnty, a.invoice_value as current_invoice_value, a.net_invo_value
                                            from com_export_invoice_ship_mst a, com_sales_contract b
                                            where a.lc_sc_id=b.id and a.lc_sc_id in(".implode(',',$sc_id_arr).") and a.is_lc=2 and a.status_active=1 and benificiary_id like '$cbo_company_name' and  buyer_id like '$cbo_buyer_name'";
                                            $sql_sc_q=sql_select($sql_cur_ship);
                                        }
                                        else if($db_type==2)
                                        {
                                            $sql_cur_ship = "SELECT contract_no, a.id as id, a.invoice_no, a.invoice_quantity as current_invoice_qnty, a.invoice_value as current_invoice_value, a.net_invo_value
                                            from com_export_invoice_ship_mst a, com_sales_contract b
                                            where a.lc_sc_id=b.id and a.lc_sc_id in(".implode(',',$sc_id_arr).") and a.is_lc=2 and a.status_active=1 and benificiary_id like '$cbo_company_name' and  buyer_id like '$cbo_buyer_name'";
                                            $sql_sc_q=sql_select($sql_cur_ship);
                                        }
                                        foreach($sql_sc_q as $row_sc_result)
                                        {
                                            $shp_inv_id[]=$row_sc_result[csf("id")];
                                            $total_shipment_val += $row_sc_result[csf("current_invoice_value")];
                                            $total_discount += ($row_sc_result[csf("current_invoice_value")]-$row_sc_result[csf("net_invo_value")]);

                                            $adjustment_arr[$row_sc_result[csf("id")]]["id"]=$row_sc_result[csf("id")];
                                            $adjustment_arr[$row_sc_result[csf("id")]]["lc_sc_no"]=$row_sc_result[csf("contract_no")];//$row_sc_result[csf("lc_sc_no")];
                                            $adjustment_arr[$row_sc_result[csf("id")]]["invoice_no"]=$row_sc_result[csf("invoice_no")];
                                            $adjustment_arr[$row_sc_result[csf("id")]]["current_invoice_qnty"]=$row_sc_result[csf("current_invoice_qnty")];
                                            $adjustment_arr[$row_sc_result[csf("id")]]["current_invoice_value"]=$row_sc_result[csf("current_invoice_value")];
                                            $adjustment_arr[$row_sc_result[csf("id")]]["net_invo_value"]=$row_sc_result[csf("net_invo_value")];
                                        }

                                    }
                                    $lc_id=implode(',',$lc_id_arr); $sc_id=implode(',',$sc_id_arr); //hidden_lc_sc_id
                                    ?>
                                    <a href="##" onClick="fnc_amount_detail('<? echo $cbo_company_name;?>','<? echo $lc_id;?>','<? echo $sc_id;?>','invoice_details','<? echo $cbo_buyer_name; ?>')"><? echo  number_format($total_shipment_val,2); ?></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Payment Realized</td>
                                    <td align="right">
                                    <?
                                    $total_break_ship_val=0;
                                    $total_break_ship_val=$total_break_ship_val+$payment_realized;
                                    echo number_format(($payment_realized),2);
                                    ?>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Document Under Collection/Rlz.</td>
                                    <td align="right">
                                        <?
                                        //var_dump($submission_id_sc);die;
                                        if($submission_id_lc)
                                        {
                                            $sub_as_purchase_amt_lc=return_field_value("sum(b.lc_sc_curr) as net_invo_value","com_export_doc_submission_mst a, com_export_doc_sub_trans b"," a.id=b.doc_submission_mst_id AND a.submit_type=2 and b.doc_submission_mst_id in($submission_id_lc)","net_invo_value");
                                        }
                                        if($submission_id_sc)
                                        {
                                            $sub_as_purchase_amt_sc=return_field_value("sum(b.lc_sc_curr) as net_invo_value","com_export_doc_submission_mst a, com_export_doc_sub_trans b"," a.id=b.doc_submission_mst_id AND a.submit_type=2 and b.doc_submission_mst_id in($submission_id_sc) ","net_invo_value");
                                        }

                                        $sub_as_purchase_amt=$sub_as_purchase_amt_lc+$sub_as_purchase_amt_sc;
                                        $sub_as_collection_rlz=$sub_as_collection-$sub_as_purchase_amt;
                                        $total_break_ship_val=$total_break_ship_val+$sub_as_collection_rlz;
                                        echo number_format(($sub_as_collection_rlz),2);
                                        ?>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Document Purchased</td>
                                    <td align="right">
                                        <?
                                            $total_break_ship_val=$total_break_ship_val+$sub_as_purchase_amt;
                                            echo number_format(($sub_as_purchase_amt),2);
                                        ?>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Document In Hand</td>
                                    <td align="right">
                                        <?
                                            $total_break_ship_val=$total_break_ship_val+$in_hand;
                                            echo number_format($in_hand,2);
                                        ?>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Short Realization</td>
                                    <td align="right">
                                    <?
                                        $total_break_ship_val=$total_break_ship_val+$payment_realized_deduction;
                                        echo number_format($payment_realized_deduction,2) ;
                                    ?>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Discount(<span style="font-size:9px; font-family:'Times New Roman', Times, serif">Gross Invoice value-Net Invoice Value</span>)</td>
                                    <td align="right">
                                        <?
                                            $total_break_ship_val=$total_break_ship_val+$total_discount;
                                            //$st_ex_ship_perc = ($payment_realized_deduction*100)/$payment_realized;
                                            echo number_format($total_discount,2);
                                        ?>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="3">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td align="right" style="font-weight:bold;">Total:</td>
                                    <td align="right" style="border-top-style:solid;border-top-width:1px;font-weight:bold;">
                                        <?
                                            echo number_format($total_break_ship_val,2);
                                        ?>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>% of Short/Excess Shipment</td>
                                    <td align="right">
                                        <?
                                            $st_ex_ship_perc = ($payment_realized_deduction*100)/$payment_realized;
                                            echo number_format($st_ex_ship_perc,2);
                                        ?>%
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Balance Shipment</td>
                                    <td align="right">
                                    </td>
                                    <td align="right" style="font-weight:bold;">&nbsp;&nbsp;
                                        <?
                                            $shipment_balance =  $file_value-$total_shipment_val;
                                            echo number_format($shipment_balance,2);
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Attached Order Qty</td>
                                    <td align="right">
                                    </td>

                                
                                    <td align="right" style="font-weight:bold;">&nbsp;&nbsp;
                                        <?
                                        // print_r($lc_id_arr);echo "cks";die;
                                        
                                        
                                        //echo $attach_order_id.pk;die;

                                        $attachQty=$attachQty_sales+$attachQty_lc;                               
                                        echo number_format($attachQty,2);
                                        //echo $attach_order_id.test;die;
                                        $budge_btb_open_amt=0;
                                        
                                        if($attach_order_id!="")
                                        {
                                            $condition= new condition();
                                            $condition->po_id(" in( $attach_order_id ) ");
                                            
                                            $condition->init();
                                            $fabric= new fabric($condition);
                                            $fabric_costing_arr=$fabric->getAmountArray_by_order_knitAndwoven_greyAndfinish();

                                            $conversion= new conversion($condition);
                                            $conversion_costing_arr=$conversion->getAmountArray_by_order();
                                            //echo '<pre>';print_r($conversion_costing_arr);
                                            //echo $conversion->getQuery();die;
                                            
                                            $yarn= new yarn($condition);
                                            $yarn_costing_arr=$yarn->getOrderWiseYarnAmountArray();
                                            
                                            $trims= new trims($condition);
                                            $trims_costing_arr=$trims->getAmountArray_by_order();
                                            
                                            $emblishment= new emblishment($condition);
                                            $emblishment_costing_arr=$emblishment->getAmountArray_by_orderAndEmbname();
                                            //print_r($emblishment_costing_arr);
                                            
                                            $wash= new wash($condition);
                                            $emblishment_costing_arr_name_wash=$wash->getAmountArray_by_orderAndEmbname();
                                                                                    
                                            $budge_btb_open_amt=0;
                                            $attach_order_id_arr=explode(",",$attach_order_id);
                                            //$attach_order_id_arr=array(0=>"51627",1=>"51628");
                                            foreach($attach_order_id_arr as $bompoid)
                                            {
                                                $job_wise_budge_amt[$powiseJobNoArr[$bompoid]]['0']+=array_sum($fabric_costing_arr['sweater']['grey'][$bompoid])+array_sum($fabric_costing_arr['knit']['grey'][$bompoid])+array_sum($fabric_costing_arr['woven']['grey'][$bompoid]);
                                                $job_wise_budge_amt[$powiseJobNoArr[$bompoid]]['1']+=$yarn_costing_arr[$bompoid];
                                                $job_wise_budge_amt[$powiseJobNoArr[$bompoid]]['2']+=$trims_costing_arr[$bompoid];
                                                $job_wise_budge_amt[$powiseJobNoArr[$bompoid]]['3']+=$emblishment_costing_arr[$bompoid][1]+$emblishment_costing_arr_name_wash[$bompoid][3]+$emblishment_costing_arr[$bompoid][4]+$emblishment_costing_arr[$bompoid][5];
                                               $job_wise_budge_amt[$powiseJobNoArr[$bompoid]]['4']+=array_sum($conversion_costing_arr[$bompoid]);
                                            }
                                            
                                            $budge_btb_open_amt=$fab_budge_cost=$yarn_budge_cost=0;
                                            $trims_budge_cost=$embel_budge_cost=$conversion_budge_cost=0;
                                            foreach($job_wise_budge_amt as $job_no_ref=>$job_data)
                                            {
                                                $budge_btb_open_amt+=array_sum($job_data);
                                                $fab_budge_cost+=$job_data[0];
                                                $yarn_budge_cost+=$job_data[1];
                                                $trims_budge_cost+=$job_data[2];
                                                $embel_budge_cost+=$job_data[3];
                                                $conversion_budge_cost+=$job_data[4];
                                            }
                                        }
                                        if(empty($lc_id_arr)) $lc_Ids_arr=0;  else $lc_Ids_arr=implode(',',$lc_id_arr);
                                        if(empty($sc_id_arr)) $sc_Ids_arr=0;  else $sc_Ids_arr=implode(',',$sc_id_arr);

                                        if($db_type==0)
                                        {
                                            $btb_mst_lc_id=return_field_value("group_concat(distinct import_mst_id) as import_mst_id"," com_btb_export_lc_attachment","lc_sc_id in($lc_Ids_arr) and is_lc_sc=0 and status_active=1 and is_deleted=0","import_mst_id");
                                            //echo "select group_concat(distinct import_mst_id) as import_mst_id from com_btb_export_lc_attachment where lc_sc_id in($sc_Ids_arr) and is_lc_sc=1  and status_active=1 and is_deleted=0";
                                            $btb_mst_sc_id=return_field_value("group_concat(distinct import_mst_id) as import_mst_id","com_btb_export_lc_attachment","lc_sc_id in($sc_Ids_arr) and is_lc_sc=1  and status_active=1 and is_deleted=0","import_mst_id");
                                        }
                                        else if($db_type==2)
                                        {
                                            $btb_mst_lc_id=return_field_value("LISTAGG(CAST(import_mst_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY import_mst_id) as import_mst_id"," com_btb_export_lc_attachment","lc_sc_id in($lc_Ids_arr) and is_lc_sc=0  and status_active=1 and is_deleted=0","import_mst_id");
                                            $btb_mst_sc_id=return_field_value("LISTAGG(CAST(import_mst_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY import_mst_id) as import_mst_id","com_btb_export_lc_attachment","lc_sc_id in($sc_Ids_arr) and is_lc_sc=1  and status_active=1 and is_deleted=0","import_mst_id");
                                        }
                                        if($btb_mst_lc_id=="") $btb_mst_lc_id=0;
                                        if($btb_mst_sc_id=="") $btb_mst_sc_id=0;

                                        $mst_id=$btb_mst_lc_id.",".$btb_mst_sc_id;
                                        $sort_val=(explode(",",$mst_id));
                                        asort($sort_val);
                                        $mst_id=implode(",",$sort_val);

                                        $sql_btb=sql_select("select
                                        a.id, sum(a.lc_value) as lc_value, a.currency_id, a.lc_date, a.importer_id, 
                                        max(case when a.payterm_id=1 THEN a.id else 0 end) as at_sight_btb_lc_id,
                                        max(case when a.payterm_id=2 THEN a.id else 0 end) as usance_btb_lc_id,
                                        max(case when a.payterm_id=3 THEN a.id else 0 end) as cash_btb_lc_id,
                                        sum(case when a.payterm_id=3 THEN a.lc_value else 0 end) as cash_in_advance
                                        from com_btb_lc_master_details a
                                        where a.id in($mst_id)  and a.is_deleted=0 and a.status_active=1 group by a.id, a.currency_id, a.lc_date, a.importer_id");
                                        $btb_id="";
                                        foreach($sql_btb as $row)
                                        {
                                            $btb_id.=$row[csf("id")].",";
											if($row[csf("currency_id")]==1)
											{
												$conversion_rate=set_conversion_rate(2, $row[csf("lc_date")],$row[csf("importer_id")]);
												$row[csf("lc_value")]=$row[csf("lc_value")]/$conversion_rate;
											}
											
                                            $btb_open_value +=$row[csf("lc_value")];
                                            $at_sight_lc_id .=$row[csf("at_sight_btb_lc_id")].",";
                                            $usance_lc_id .=$row[csf("usance_btb_lc_id")].",";
                                            $cash_lc_id .=$row[csf("cash_btb_lc_id")].",";
                                            $cash_in_advance +=$row[csf("cash_in_advance")];
                                        }
                                        $btb_id=chop($btb_id,",");
                                        $atsite_accep_id=substr($at_sight_lc_id, 0, -1);
                                        $usance_paid_lc_id=substr($usance_lc_id, 0, -1);
                                        $cash_lc_id=substr($cash_lc_id, 0, -1);
                                        //$btb_tobe_open_value=$budge_btb_open_amt-$btb_open_value;
                                        $btb_tobe_open_value=((($fab_budge_cost+$yarn_budge_cost+$conversion_budge_cost+$trims_budge_cost+$embel_budge_cost)/$total_job_quantity)*$total_lcsc_attach_quantity)-$btb_open_value;
                                        //echo 'system'.$conversion_budge_cost;
                                        ?>
                                    </td> 
                                
                                    
                                    
                                </tr>
                            </table>
                        </td>
                        <td style="vertical-align:top" width="450">
                            <table width="400" style="vertical-align:top">
                                <tr>
                                    <td width="220" title="<? //print_r($job_wise_budge_amt); ?>">BTB to be Open</td>
                                    <td width="100" align="right" style="font-weight:bold;" title="(((fab_budge_cost+yarn_budge_cost+conversion_budge_cost+trims_budge_cost+embel_budge_cost)/total_job_quantity)*total_lcsc_attach_quantity)-btb_open_value">
                                    <?
                                    echo number_format($btb_tobe_open_value,2);
                                    ?>
                                </td>
                                <td>&nbsp; </td>
                                </tr>
                                <tr>
                                    <td width="220">Total BTB Opened</td>
                                    <td width="100" align="right" style="font-weight:bold;">
                                        <input type="hidden" id="hidden_btb_id" value="<? echo $btb_id; ?>">
                                        <a href="##" onClick="btb_open('btb_open','<? echo $btb_id; ?>','<? echo $txt_file_no.'*'.$buyer_ref_id; ?>')"><? echo number_format($btb_open_value,2); ?> </a>
                                    </td>
                                    <td>&nbsp; </td>
                                </tr>
                                <tr>
                                    <td>BTB Percentage</td>
                                    <td align="right" style="font-weight:bold;" title="(Total BTB Opened/Total File Value)*100"><? 
                                    //$btb_percent=($btb_open_value/$file_value)*100; 
                                    $btb_percent=($btb_open_value/$net_tot_lc_val)*100; 
                                    
                                    echo number_format($btb_percent,2); ?>%</td>
                                    <td>&nbsp; </td>
                                </tr>
                                <tr>
                                    <td>Total BTB Accepted</td>
                                    <td align="right" style="font-weight:bold;" title="<? echo "all btb id=".$mst_id ?>">
                                        <?
                                        if($btb_id=="") $btb_id=0;
                                        if($atsite_accep_id=="") $atsite_accep_id=0;
                                        //echo $btb_id;die;
                                        //echo "select sum(current_acceptance_value) as current_acceptance_value from com_import_invoice_dtls where btb_lc_id in($btb_id) group by btb_lc_id";die;
                                        $sql_accep="select a.id as btb_id, a.currency_id, a.lc_date, a.importer_id, sum(b.current_acceptance_value) as current_acceptance_value from com_btb_lc_master_details a, com_import_invoice_dtls b where a.id=b.btb_lc_id and b.btb_lc_id in($btb_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.currency_id, a.lc_date, a.importer_id";
                                        $sql_accep_res=sql_select($sql_accep);
                                        $paid_lc_conversion_arr=array();
                                        foreach($sql_accep_res as $row)
                                        {
                                            if($row[csf("currency_id")]==1)
                                            {
                                                $conversion_rate=set_conversion_rate(2, $row[csf("lc_date")],$row[csf("importer_id")]);
                                                $bill_accepted +=$row[csf("current_acceptance_value")]/$conversion_rate;
                                                $paid_lc_conversion_arr[$row[csf("btb_id")]]=$conversion_rate;
                                            }
                                            else
                                            {
                                                $bill_accepted +=$row[csf("current_acceptance_value")];
                                            }                                            
                                        }
                                        //$bill_accepted=return_field_value("sum(current_acceptance_value) as current_acceptance_value","com_import_invoice_dtls","btb_lc_id in($btb_id) and status_active=1 and is_deleted=0","current_acceptance_value");

                                        //$atsite_accepted=return_field_value("sum(current_acceptance_value) as current_acceptance_value","com_import_invoice_dtls","btb_lc_id in($atsite_accep_id) and status_active=1 and is_deleted=0","current_acceptance_value");
										//echo "select a.BTB_LC_ID, a.CURRENT_ACCEPTANCE_VALUE from com_import_invoice_dtls a, com_import_payment_com b where a.IMPORT_INVOICE_ID=b.INVOICE_ID and a.status_active=1 and b.status_active=1 and a.btb_lc_id in($atsite_accep_id)";
										$atsite_accep_sql=sql_select("select a.BTB_LC_ID, a.CURRENT_ACCEPTANCE_VALUE from com_import_invoice_dtls a, com_import_payment_com b where a.IMPORT_INVOICE_ID=b.INVOICE_ID and a.status_active=1 and b.status_active=1 and a.btb_lc_id in($atsite_accep_id)");
										$atsite_accep_id=array();
										foreach($atsite_accep_sql as $val)
										{
											$atsite_accepted+=$val["CURRENT_ACCEPTANCE_VALUE"];
											$atsite_accep_id[$val["BTB_LC_ID"]]=$val["BTB_LC_ID"];
										}
										//echo "kkk";print_r($atsite_accep_id);die;

                                        //echo $btb_inv_id;
                                        ?>

                                        <input type="hidden" id="hidden_acept_id" value="<? echo $btb_id; ?>">
                                        <a href="##" onClick="btb_open('btb_accep','<? echo $btb_id; ?>','<? echo $txt_file_no.'*'.$buyer_ref_id; ?>')">
                                        <?
                                        echo number_format($bill_accepted,2);
                                        //echo $btb_inv_id;
                                        ?></a>
                                    </td>
                                    <td>&nbsp; </td>
                                </tr>
                                <tr>
                                    <td>Total Paid</td>
                                    <?
                                    if($cash_lc_id=="") $cash_lc_id=0;
                                    if($usance_paid_lc_id=="") $usance_paid_lc_id=0;
                                    $paid2_sql=sql_select("select a.id, b.btb_lc_id, a.accepted_ammount as paid from com_import_payment a, com_import_invoice_dtls b where b.import_invoice_id=a.INVOICE_ID and b.btb_lc_id in($usance_paid_lc_id) and b.status_active=1 and a.status_active=1");//die;
                                    //echo "select a.id, a.accepted_ammount as paid from com_import_payment a, com_import_invoice_dtls b where b.import_invoice_id=a.INVOICE_ID and b.btb_lc_id in($usance_paid_lc_id) and b.status_active=1 and a.status_active=1";
                                    foreach($paid2_sql as $row)
                                    {
                                        $all_usence_paid_id.=$row[csf("id")].",";
                                        if($paid_id_tes[$row[csf("id")]]=="")
                                        {
                                            $paid_id_tes[$row[csf("id")]]=$row[csf("id")];
                                            if ($paid_lc_conversion_arr[$row[csf("btb_lc_id")]]){
                                                $paid2+=$row[csf("paid")]/$paid_lc_conversion_arr[$row[csf("btb_lc_id")]];
                                            } else {
                                                $paid2+=$row[csf("paid")];
                                            }
                                            
                                        }
                                    }
                                    //echo $paid2;
                                    $all_usence_paid_id=chop($all_usence_paid_id,",");
                                    //$paid2=return_field_value("sum(accepted_ammount) as paid"," com_import_payment","lc_id in($usance_paid_lc_id) and status_active='1'","paid");

                                    $paid=($paid2+$atsite_accepted+$cash_in_advance);
                                    //$paid=($atsite_accepted+$cash_in_advance);
                                    $cash_lc_id=implode(",",array_unique(explode(",",$cash_lc_id)));
                                    $atsite_accep_id=implode(",",array_unique($atsite_accep_id));
									
                                    $usance_paid_lc_id=implode(",",array_unique(explode(",",$usance_paid_lc_id)));
                                    $all_usence_paid_id=implode(",",array_unique(explode(",",$all_usence_paid_id)));
                                    //$paid_all_id=$cash_lc_id."_".$atsite_accep_id."_".$all_usence_paid_id;
                                    $paid_all_id=implode(",",array_unique(explode(",",$btb_id)))."_".$cash_lc_id."_".$atsite_accep_id."_".$all_usence_paid_id;
                                    //echo $usance_paid_lc_id;

                                    ?>
                                    <td align="right" style="font-weight:bold;" title="<? echo $paid2."==".$atsite_accepted."==".$cash_in_advance;?>">

                                    <a href="##" onClick="btb_open('btb_paid','<? echo $paid_all_id; ?>','<? echo $txt_file_no.'*'.$buyer_ref_id; ?>')">
                                    <?
                                    echo number_format($paid,2);
                                    ?>
                                    </a>
                                    </td>
                                    <td>&nbsp; </td>
                                </tr>
                                <tr>
                                    <td>Balance</td>
                                    <td align="right" style="font-weight:bold;">
                                        <?
                                            $bal= $bill_accepted-$paid;
                                            echo number_format($bal,2);

                                        ?>
                                    </td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>Yet To Acceptance</td>
                                    <td align="right" style="font-weight:bold;">
                                        <a href="##" onClick="btb_open('yet_to_accept_pop_up','<? echo $btb_id; ?>','<? echo $txt_file_no.'*'.$buyer_ref_id; ?>')">
                                        <?
                                            $yet_accept= $btb_open_value-$bill_accepted;
                                            echo number_format($yet_accept,2);

                                        ?>
                                        </a>
                                    </td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>BTB Margin A/C Balance</td>
                                    <td align="right" style="font-weight:bold;" title="<? echo "BTB Margine=".$total_btb_margine." ; Paid = ".$paid; ?>">
                                        <?
                                            $total_btb_margine_balance=$total_btb_margine-$paid;//$total_btb_margine
                                            echo number_format($total_btb_margine_balance,2);
                                        ?>
                                    </td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>PC Amount</td>
                                    <td align="right" style="font-weight:bold;">
                                        <?
                                        //print_r($sc_id_arr);
                                        if (!empty($lc_id_arr))
                                        {
                                            $pc_amount_lc= return_field_value("sum(b.loan_amount) as loan_amount", "com_pre_export_finance_dtls b, com_pre_export_lc_wise_dtls c","b.id = c.pre_export_dtls_id and c.export_type=1 and b.status_active=1 and b.is_deleted=0 and c.lc_sc_id in(". implode(',',$lc_id_arr).") and b.loan_type=20","loan_amount");
                                        }

                                        if (!empty($sc_id_arr))
                                        {
                                            $pc_amount_sc= return_field_value("sum(b.loan_amount) as loan_amount", "com_pre_export_finance_dtls b, com_pre_export_lc_wise_dtls c","b.id = c.pre_export_dtls_id and c.export_type=2 and b.status_active=1 and b.is_deleted=0 and c.lc_sc_id in(". implode(',',$sc_id_arr).") and b.loan_type=20","loan_amount");
                                        } 

                                        $pc_amount = $pc_amount_sc + $pc_amount_lc;
                                        echo number_format($pc_amount,2);
                                        ?>
                                    </td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>Balance Amount this LC/SC</td>
                                    <td align="right" style="font-weight:bold;" title="[Total Net Value - Total Paid Amount]">
                                        <?
                                        $tot_bl = $net_tot_lc_val - $paid;
                                        echo number_format($tot_bl,2)?>
                                        <!-- <script>
                                            // Get the content from 'dev_id'
                                            var content = document.getElementById('dev_id').innerHTML;
                                            // Set the content of 'target_div' to be the same as 'dev_id'
                                            document.getElementById('target_div').innerHTML = content;                                  
                                        </script>
                                        <div id="target_div"></div> -->
                                    </td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td colspan="3">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td colspan="3">BTB Accepted : &nbsp; &nbsp;
                                    <input type="button" class="formbutton" id="btn_trims" style="width:70px;" value="Trims" onClick="btb_open_category('btb_accep_category','<? echo $btb_id; ?>','<? echo $txt_file_no.'*'.$buyer_ref_id; ?>','<? echo str_replace("'","",$cbo_buyer_name); ?>','<? echo $trims_budge_cost; ?>','4')" /> &nbsp;
                                    <input type="button" class="formbutton" id="btn_yarn" style="width:70px;" value="Yarn" onClick="btb_open_category('btb_accep_category','<? echo $btb_id; ?>','<? echo $txt_file_no.'*'.$buyer_ref_id; ?>','<? echo str_replace("'","",$cbo_buyer_name); ?>','<? echo $yarn_budge_cost; ?>','1')" /> &nbsp;
                                    <input type="button" class="formbutton" id="btn_fabric" style="width:70px;" value="Fabric" onClick="btb_open_category('btb_accep_category','<? echo $btb_id; ?>','<? echo $txt_file_no.'*'.$buyer_ref_id; ?>','<? echo str_replace("'","",$cbo_buyer_name); ?>','<? echo $fab_budge_cost; ?>','2,3')" /> &nbsp;
                                    <input type="button" class="formbutton" id="btn_service" style="width:70px; display:none;" value="Service" onClick="btb_open_category('btb_accep_category','<? echo $btb_id; ?>','<? echo $txt_file_no.'*'.$buyer_ref_id; ?>','<? echo str_replace("'","",$cbo_buyer_name); ?>','<? echo $service_budge_cost; ?>','12,74,25,104,102,103,24,31')" /> &nbsp;
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                <table width="2150" cellpadding="0" cellspacing="0" border="0" rules="all" id="" align="left">
                    <tr>
                        <td><strong style="font:Arial, Helvetica, sans-serif; font-size:16px;">Document Realized:</strong></td>
                    </tr>
                </table>
                <table width="2150" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all" id="" align="left">
                    <thead>
                        <tr>
                            <th  rowspan="2">Sl</th>
                            <th width="120" rowspan="2">Invoice No.</th>
                            <th width="140" rowspan="2">Export Bill No.</th>
                            <th width="80" rowspan="2">Bill Date</th>
                            <th width="70" rowspan="2">Inv/Bill Qty/Pcs</th>
                            <th width="85" rowspan="2">Bill Amount</th>
                            <th width="85">Sub Under Collection</th>
                            <th colspan="4">Sub. Under Purchase</th>
                            <th colspan="3">Realized</th>
                            <th colspan="12">Proceeds Distribution A/C</th>
                        </tr>
                        <tr>
                            <th>Amount</th>
                            <th width="90">Bill Amount</th>
                            <th width="90">Purchase Amount</th>
                            <th width="50">(%)</th>
                            <th width="80">Purchase Date</th>
                            <th width="85">Amount</th>
                            <th width="80">Date</th>
                            <th width="85">Short Realization</th>
                            <th width="75">ERQ A/C</th>
                            <th width="75">BTB Margin A/C</th>
                            <th width="75">Sundry A/C</th>
                            <th width="75">STD A/C</th>
                            <th width="75">CD A/C</th>
                            <th width="75">CC A/C</th>
                            <th width="75">FDBC</th>
                            <th width="100">Others Fund[sinking]/Free Fund</th>
                            <th width="75">Local <br> Commi.</th>
                            <th width="75">Foreign <br> Commi.</th>
                            <th width="85">Exp & Adj.</th>
                            <th width="80">Balance</th>
                        </tr>
                    </thead>
                </table>
                <table width="2150" rules="all" class="rpt_table" align="left" id="" border="1">
                    <tbody>
                        <?
                        $lc_num_chack=array();$i=1;$c=1;
                        foreach ($realize_lc_arr as $key=>$val)
                        {
                            $submision_id=$val[('sub_id')];
                            $purchase_amt=return_field_value("sum(b.lc_sc_curr) as dom_curr"," com_export_doc_sub_trans b","b.doc_submission_mst_id in(".$val[("sub_id")].") group by b.doc_submission_mst_id ","dom_curr");
                            if(!in_array($val["lc_sc_id"],$lc_num_chack))
                            {
                                $lc_num_chack[]=$val["lc_sc_id"];
                                if($c!=1)
                                {
                                    ?>
                                    <tr align="right" bgcolor="#CCCCCC">
                                        <td><p><strong></strong></p>&nbsp;</td>
                                        <td><p><strong></strong></p>&nbsp;</td>
                                        <td><p><strong></strong></p>&nbsp;</td>
                                        <td><strong>Sub-Total</strong>&nbsp;</td>
                                        <td><p><strong><? echo number_format($lc_bill_qty,0); $gt_lc_bill_qty+=$lc_bill_qty; ?></strong></p>&nbsp;</td>
                                        <td><p><strong><? echo number_format($lc_bill_value,2); $gt_lc_bill_value+=$lc_bill_value; ?></strong></p>&nbsp;</td>
                                        <td><p><strong><? echo number_format($lc_doc_col_value,2); $gt_lc_doc_col_value+=$lc_doc_col_value; ?></strong></p>&nbsp;</td>
                                        <td><p><strong><? echo number_format($lc_bill_value_sub,2); $gt_sub_lc_bill_value+=$lc_bill_value_sub; ?></strong></p>&nbsp;</td>
                                        <td><p><strong><? echo number_format($lc_doc_pur_value,2); $gt_lc_doc_pur_value+=$lc_doc_pur_value; ?></strong></p>&nbsp;</td>
                                        <td><p><strong></strong></p></td>
                                        <td><p><strong></strong></td>
                                        <td><p><strong><? echo number_format($lc_doc_distribute_value,2); $gt_lc_doc_distribute_value+=$lc_doc_distribute_value;?></strong></p>&nbsp;</td>
                                        <td></td>
                                        <td><p><strong><? echo number_format($lc_realized_deduct,2); $gt_lc_realized_deduct+=$lc_realized_deduct;?></strong></p>&nbsp;</td>
                                        <td><p><strong><? echo number_format($lc_erq,2); $gt_lc_erq+=$lc_erq; ?></strong></p>&nbsp;</td>
                                        <td><p><strong><? echo number_format($lc_btb,2); $gt_lc_btb+=$lc_btb; ?> </strong></p>&nbsp;</td>
                                        <td><p><strong><? echo number_format($lc_sundry_ac,2); $gt_lc_sundry_ac+=$lc_sundry_ac; ?></strong></p>&nbsp;</td>
                                        <td><p><strong><? echo number_format($lc_std_spaecial,2); $gt_lc_std_spaecial+=$lc_std_spaecial; ?></strong></p>&nbsp;</td>
                                        <td><p><strong><? echo number_format($lc_cd,2); $gt_lc_cd+=$lc_cd; ?></strong></p>&nbsp;</td>
                                        <td><p><strong><? echo number_format($lc_cc,2); $gt_lc_cc+=$lc_cc; ?></strong></p>&nbsp;</td>
                                        <td><p><strong><? echo number_format($lc_fdbc,2); $gt_lc_fdbc+=$lc_fdbc; ?></strong></p>&nbsp;</td>
                                        <td><p><strong><? echo number_format($lc_exp_adj,2); $gt_lc_exp_adj+=$lc_exp_adj;?></strong></p>&nbsp;</td>
                                        <td><p><strong><? echo number_format($lc_exp_adj,2); $gt_lc_exp_adj+=$lc_exp_adj;?></strong></p>&nbsp;</td>
                                        <td></td>
                                        <td></td>
                                        <td><p><strong><? //echo number_format($sc_exp_adj,2); $gt_sc_exp_adj+=$sc_exp_adj; ?></strong></p>&nbsp;</td>
                                    </tr>
                                    <?
                                }
                                $c++;
                                $lc_bill_qty =0;
                                $lc_bill_value =0;
                                $lc_doc_col_value =0;
                                $lc_doc_pur_value =0;
                                $lc_doc_distribute_value =0;
                                $lc_realized_deduct = 0;
                                $lc_bill_value_sub=0;
                                $lc_erq = 0;
                                $lc_btb = 0;
                                $lc_cd = 0;
                                $lc_cc =0;
                                $lc_sundry_ac =0;
                                $lc_std_spaecial =0;
                                $lc_fdbc =0;
                                $lc_document_other_found =0;
                                $lc_exp_adj =0;
                                $lc_short_ship_export =0;
                                ?>
                                <tr>
                                    <td colspan="26" style="background-color:#FDF4EF"><b>
                                    <?/*
                                    $export_number_rlz=return_field_value("export_lc_no","com_export_lc","id='".$val[('lc_sc_id')]."' and status_active=1","export_lc_no");
                                    echo "Export L/C No."." - ".$export_number_rlz; */

                                    $lc_sc_ids=$val[('lc_sc_id')];
                                    $export_number_rlz='';
                                    $lc_sc_id=explode(",",$lc_sc_ids);
                                    $lc_sc_id=array_unique($lc_sc_id);
                                    foreach($lc_sc_id as $lcsc_id)
                                    {
                                        $export_number_rlz.=return_field_value("export_lc_no","com_export_lc","id=$lcsc_id and status_active=1","export_lc_no").",";
                                    }
                                    echo "Export L/C No."." - ".chop($export_number_rlz,",");
                                    ?>
                                    </b>
                                    </td>
                                </tr>
                                <?
                            }
                            if ($i%2==0)
                            $bgcolor="#E9F3FF";
                            else
                            $bgcolor="#FFFFFF";
                            ?>

                            <tr  bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                <td ><? echo $i;?></td>
                                <td width="120"><p><? echo $val[('invoice_no')];?></p></td>
                                <td width="140" ><? echo $val[('bank_ref_no')];?></td>
                                <td width="80" align="center"><? if($val[('bank_ref_date')]!='0000-00-00') echo change_date_format($val[('bank_ref_date')]); else echo "00-00-0000"; ?></td>
                                <td width="70" align="right">
                                <?
                                /*$id=$val[csf('inv_id')];
                                $inv_qty=return_field_value("sum(a.current_invoice_qnty) as current_invoice_qnty"," com_export_invoice_ship_dtls a, com_export_doc_submission_invo b ","a.mst_id=b.invoice_id and b.invoice_id in($id) and a.status_active=1","current_invoice_qnty");
                                echo $inv_qty;*/
                                $inv_qty=$val[('invoice_quantity')]; echo  $inv_qty;
                                ?>
                                </td>
                                <td width="85" align="right"><? echo number_format($val[('net_invo_value')],2); ?></td>
                                <td width="85" align="right"><? echo number_format($val[('sub_collection')],2); ?></td>
                                <td width="90" align="right"><? if($purchase_amt) echo number_format($val[('net_invo_value')],2); else echo "0.00";?></td>
                                <td width="90" align="right"><? echo number_format($purchase_amt,2);
                                /*$purchase_amt_sub_rlz=return_field_value("sum(b.lc_sc_curr) as dom_curr"," com_export_doc_sub_trans b","b.doc_submission_mst_id='".$val[csf('sub_id')]."'","dom_curr");
                                echo number_format($purchase_amt_sub_rlz,2);*/
                                //echo $submision_id;
                                ?>
                                </td>
                                <td width="50" align="right"><? echo number_format((($purchase_amt/$val[('net_invo_value')])*100),2)."%"; ?> </td>
                                <td width="80" align="center"><?  if($val[('negotiation_date')]!='0000-00-00') echo change_date_format($val[('negotiation_date')]); else echo "00-00-0000"; ?></td>
                                <td width="85" align="right"><? echo number_format($val[('distribute_realize')],2);?></td>
                                <td width="80" align="center"><? if($val[('rlz_received_date')]!='0000-00-00')  echo change_date_format($val[('rlz_received_date')]);  ?></td>
                                <td width="85" align="right"><? echo number_format($val[('deduct_realize')],2);?></td>
                                <td width='75' align='right'><?  echo number_format($val[('erq')],2);?></td>
                                <td width='75' align='right'><?  echo number_format($val[('btb')],2);?></td>
                                <td width='75' align='right'><?  echo number_format($val[('sun_ac')],2);?></td>
                                <td width='75' align='right'><?  echo number_format($val[('std')],2);?></td>
                                <td width='75' align='right'><?  echo number_format($val[('cd')],2);?></td>
                                <td width='75' align='right'><?  echo number_format($val[('cc')],2);?></td>
                                <td width='75' align='right'><?  echo number_format($val[('fdbc')],2);?></td>
                                <td width='100' align='right'> <?
                                    $document_other_found = return_field_value("sum(document_currency)","com_export_proceed_rlzn_dtls a, com_export_proceed_realization b"," a.mst_id = b.id AND a.type = '1' AND b.is_invoice_bill = 1 AND b.invoice_bill_id='".$val[('sub_id')]."' AND a.account_head in (87) ");
                                    echo $document_other_found;
                                ?></td>
                                <td width='75' align='right'><?echo number_format($realize_loc_com[$val[("rlz_id")]][62]['loc_com'],2);?></td>
                                <td width='75' align='right'><?echo number_format($realize_foreign_com[$val[("rlz_id")]][61]['for_com'],2);?></td>

                                
                                <td  align="right" width="85" >
                                <?
                                    $document_adj = return_field_value("sum(document_currency)","com_export_proceed_rlzn_dtls a, com_export_proceed_realization b"," a.mst_id = b.id AND a.type = '1' AND b.is_invoice_bill = 1 AND b.invoice_bill_id='".$val[('sub_id')]."' AND a.account_head not in (5,6,10,15,65,81,11,87) ");
                                    echo "<a href='#report_detals' onclick= \"openmypage_v2('$submision_id');\">".number_format($document_adj,2)."</a>";
                                ?></td>
                                <td  align="right" width="85" ><? $balen=($val[('net_invo_value')]-($val[('distribute_realize')]+$val[('deduct_realize')])); echo number_format($balen,2);  ?></td>
                            </tr>
                            <?
                            $lc_bill_qty+=$inv_qty;
                            $lc_bill_value +=$val[('net_invo_value')];
                            $lc_doc_col_value +=$val[('sub_collection')];
                            if($purchase_amt) $lc_bill_value_sub +=$val[('net_invo_value')];
                            $lc_doc_pur_value +=$purchase_amt;
                            $lc_doc_distribute_value +=$val[('distribute_realize')];
                            $lc_realized_deduct += $val[('deduct_realize')];
                            $lc_erq += $val[('erq')];
                            $lc_btb += $val[('btb')];
                            $lc_cd+= $val[('cd')];
                            $lc_cc += $val[('cc')];
                            $lc_sundry_ac+= $val[('sun_ac')];
                            $lc_std_spaecial += $val[('std')];
                            $lc_fdbc += $val[('fdbc')];
                            $lc_document_other_found += $document_other_found;
                            $lc_exp_adj += $document_adj;
                            $lc_short_ship_export += $document_adj;
                            $tot_loc_com +=$realize_loc_com[$val[("rlz_id")]][62]['loc_com'];
                            $tot_for_com +=$realize_foreign_com[$val[("rlz_id")]][61]['for_com'];
                            $i++;
                        }
                        if(!empty($realize_lc_arr))
                        {
                            ?>
                            <tr align="right" bgcolor="#CCCCCC">
                                <td><p><strong></strong></p>&nbsp;</td>
                                <td><p><strong></strong></p>&nbsp;</td>
                                <td><p><strong></strong></p>&nbsp;</td>
                                <td><strong>Sub-Total</strong>&nbsp;</td>
                                <td><p><strong><? echo number_format($lc_bill_qty,0); $gt_lc_bill_qty+=$lc_bill_qty; ?></strong></p>&nbsp;</td>
                                <td><p><strong><? echo number_format($lc_bill_value,2); $gt_lc_bill_value+=$lc_bill_value; ?></strong></p>&nbsp;</td>
                                <td><p><strong><? echo number_format($lc_doc_col_value,2); $gt_lc_doc_col_value+=$lc_doc_col_value; ?></strong></p>&nbsp;</td>
                                <td><p><strong><? echo number_format($lc_bill_value_sub,2); $gt_sub_lc_bill_value+=$lc_bill_value_sub; ?></strong></p>&nbsp;</td>
                                <td><p><strong><? echo number_format($lc_doc_pur_value,2); $gt_lc_doc_pur_value+=$lc_doc_pur_value; ?></strong></p>&nbsp;</td>
                                <td><p><strong></strong></p></td>
                                <td><p><strong></strong></td>
                                <td><p><strong><? echo number_format($lc_doc_distribute_value,2); $gt_lc_doc_distribute_value+=$lc_doc_distribute_value;?></strong></p>&nbsp;</td>
                                <td></td>
                                <td><p><strong><? echo number_format($lc_realized_deduct,2); $gt_lc_realized_deduct+=$lc_realized_deduct;?></strong></p>&nbsp;</td>
                                <td><p><strong><? echo number_format($lc_erq,2); $gt_lc_erq+=$lc_erq; ?></strong></p>&nbsp;</td>
                                <td><p><strong><? echo number_format($lc_btb,2); $gt_lc_btb+=$lc_btb; ?> </strong></p>&nbsp;</td>
                                <td><p><strong><? echo number_format($lc_sundry_ac,2); $gt_lc_sundry_ac+=$lc_sundry_ac; ?></strong></p>&nbsp;</td>
                                <td><p><strong><? echo number_format($lc_std_spaecial,2); $gt_lc_std_spaecial+=$lc_std_spaecial; ?></strong></p>&nbsp;</td>
                                <td><p><strong><? echo number_format($lc_cd,2); $gt_lc_cd+=$lc_cd; ?></strong></p>&nbsp;</td>
                                <td><p><strong><? echo number_format($lc_cc,2); $gt_lc_cc+=$lc_cc; ?></strong></p>&nbsp;</td>
                                <td><p><strong><? echo number_format($lc_fdbc,2); $gt_lc_fdbc+=$lc_fdbc; ?></strong></p>&nbsp;</td>
                                <td><p><strong><? echo number_format($lc_document_other_found,2); $gt_lc_document_other_found+=$lc_document_other_found;?></strong></p>&nbsp;</td>


                                <td><p><strong><? echo number_format($tot_loc_com,2); $gt_tot_loc_com+=$tot_loc_com;?></strong></p>&nbsp;</td>
                                <td><p><strong><? echo number_format($tot_for_com,2); $gt_tot_for_com+=$tot_for_com;?></strong></p>&nbsp;</td>


                                <td><p><strong><? echo number_format($lc_exp_adj,2); $gt_lc_exp_adj+=$lc_exp_adj;?></strong></p>&nbsp;</td>
                                <td><p><strong><? //echo number_format($sc_exp_adj,2); $gt_sc_exp_adj+=$sc_exp_adj; ?></strong></p>&nbsp;</td>
                            </tr>
                            <?
                        }

                        $sc_num_chack=array();$j=1;$d=1;
                        //echo "<pre>";
                        //print_r($realize_sc_arr);echo "test";die;
                        foreach ($realize_sc_arr as $key=>$val)
                        {
                            $submision_id=$val[('sub_id')];
                            $purchase_amt_sc=return_field_value("sum(b.lc_sc_curr) as dom_curr"," com_export_doc_sub_trans b","b.doc_submission_mst_id in(".$val[("sub_id")].") ","dom_curr");

                            if(!in_array($val["lc_sc_id"],$sc_num_chack))
                            {
                                $sc_num_chack[]=$val["lc_sc_id"];
                                if($d!=1)
                                {
                                    ?>
                                    <tr align="right" bgcolor="#CCCCCC">
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td><b>Sub-Total</b>&nbsp;</td>
                                        <td><b><? echo number_format($sc_bill_qty,0); $gt_sc_bill_qty+=$sc_bill_qty; ?></b>&nbsp;</td>
                                        <td><b><? echo number_format($sc_bill_value,2); $gt_sc_bill_value+=$sc_bill_value; ?></b>&nbsp;</td>
                                        <td><b><? echo number_format($sc_doc_col_value,2); $gt_sc_doc_col_value+=$sc_doc_col_value; ?></b></td>
                                        <td><b><? echo number_format($sc_bill_value_sub,2); $gt_sub_sc_bill_value+=$sc_bill_value_sub; ?></b></td>
                                        <td><b><? echo number_format($sc_doc_pur_value,2); $gt_sc_doc_pur_value+=$sc_doc_pur_value;  ?></b></td>
                                        <td></td>
                                        <td></td>
                                        <td><b><? echo number_format($sc_doc_distribute_value,2); $gt_sc_doc_distribute_value+= $sc_doc_distribute_value; ?></b></td>
                                        <td></td>
                                        <td><b><? echo number_format($sc_realized_deduct,2); $gt_sc_realized_deduct+= $sc_realized_deduct; ?></b></td>
                                        <td><b><? echo number_format($sc_erq,2); $gt_sc_erq+=$sc_erq; ?></b></td>
                                        <td><b><? echo number_format($sc_btb,2); $gt_sc_btb+=$sc_btb; ?> </b></td>
                                        <td><b><? echo number_format($sc_sundry_ac,2); $gt_sc_sundry_ac+= $sc_sundry_ac; ?></b></td>
                                        <td><b><? echo number_format($sc_std_spaecial,2); $gt_sc_std_spaecial+= $sc_std_spaecial; ?></b></td>
                                        <td><b><? echo number_format($sc_cd,2); $gt_sc_cd+=$sc_cd; ?></b></td>
                                        <td><b><? echo number_format($sc_cc,2); $gt_sc_cc+= $sc_cc; ?></b></td>
                                        <td><b><? echo number_format($sc_fdbc,2); $gt_sc_fdbc+= $sc_fdbc; ?></b></td>
                                        <td><b><? echo number_format($sc_document_other_found,2); $gt_sc_document_other_found+=$sc_document_other_found; ?></b></td>
                                        <td>rrww</td>
                                        <td>ooww</td>
                                        <td><b><? echo number_format($sc_exp_adj,2); $gt_sc_exp_adj+=$sc_exp_adj; ?></b></td>
                                        <td><b><? //echo number_format($sc_exp_adj,2); $gt_sc_exp_adj+=$sc_exp_adj; ?></b></td>
                                    </tr>
                                    <?
                                }
                                $d++;
                                $sc_bill_qty =0;
                                $sc_bill_value =0;
                                $sc_doc_col_value =0;
                                $sc_doc_pur_value =0;
                                $sc_doc_distribute_value =0;
                                $sc_realized_deduct =0;
                                $sc_erq =0;
                                $sc_btb =0;
                                $sc_cd =0;
                                $sc_cc =0;
                                $sc_sundry_ac =0;
                                $sc_std_spaecial =0;
                                $sc_fdbc =0;
                                $sc_document_other_found =0;
                                $sc_exp_adj =0;
                                $sc_short_ship_export =0;
                                ?>
                                <tr>
                                    <td colspan="24" style="background-color:#FDF4EF"><b>
                                    <?
                                    $salse_nubmer_rlz=return_field_value("contract_no","com_sales_contract","id='".$val[('lc_sc_id')]."' and status_active=1");
                                    echo "Export Salse Contact No."." - ".$salse_nubmer_rlz;
                                    ?></b></td>
                                </tr>
                                <?
                                //if($val[csf('bank_ref_no')]!= '0000-00-00') echo change_date_format($val[csf('bank_ref_no')]); else echo  '00-00-0000';
                            }

                            if ($i%2==0) $bgcolor="#E9F3FF";
                            else $bgcolor="#FFFFFF";
                            ?>
                            <tr  bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                <td ><? echo $j;?></td>
                                <td width="120" ><p><? echo $val[('invoice_no')];?></p></td>
                                <td width="140" ><? echo $val[('bank_ref_no')];?></td>
                                <td width="80" align="center"><? if($val[ 'bank_ref_date']!='0000-00-00') echo change_date_format($val[ 'bank_ref_date']); else echo "00-00-0000"; ?></td>
                                <td width="70" align="right">
                                <?
                                /*$id=$val[csf('inv_id')];
                                $inv_qty=return_field_value("sum(a.current_invoice_qnty) as current_invoice_qnty"," com_export_invoice_ship_dtls a, com_export_doc_submission_invo b ","a.mst_id=b.invoice_id and b.invoice_id in($id) and a.status_active=1","current_invoice_qnty");*/
                                $inv_qty=$val[('invoice_quantity')];
                                echo $inv_qty;
                                ?>
                                </td>
                                <td width="85" align="right"><? echo number_format($val[('net_invo_value')],2); ?></td>
                                <td width="85" align="right">
                                <?
                                    echo number_format($val[('sub_collection')],2);
                                ?>
                                </td>
                                <td width="90" align="right"><? if($purchase_amt_sc) echo number_format($val[('net_invo_value')],2); else echo "0.00";?></td>
                                <td width="90" align="right">
                                <?
                                echo number_format($purchase_amt_sc,2);
                                ?>
                                </td>
                                <td width="50" align="right"><? echo number_format((($purchase_amt_sc/$val[('net_invo_value')])*100),2)."%"; ?></td>
                                <td width="80" align="center"><? if($val["negotiation_date"]!='0000-00-00') echo change_date_format($val[('negotiation_date')]); else echo "00-00-0000"; ?></td>
                                <td width="85" align="right"><? echo number_format($val[('distribute_realize')],2);?></td>
                                <td width="80" align="center"><? if($val[('rlz_received_date')]!='0000-00-00')  echo change_date_format($val[('rlz_received_date')]); else echo "00-00-0000"; ?></td>
                                <td width="85" align="right"><? echo number_format($val[('deduct_realize')],2);?></td>
                                <td width='75' align='right'><?  echo number_format($val[('erq')],2);?></td>
                                <td width='75' align='right'><?  echo number_format($val[('btb')],2);?></td>
                                <td width='75' align='right'><?  echo number_format($val[('sun_ac')],2);?></td>
                                <td width='75' align='right'><?  echo number_format($val[('std')],2);?></td>
                                <td width='75' align='right'><?  echo number_format($val[('cd')],2);?></td>
                                <td width='75' align='right'><?  echo number_format($val[('cc')],2);?></td>
                                <td width='75' align='right'><?  echo number_format($val[('fdbc')],2);?></td>

                                <td width='100' align='right'>
                                <?
                                    $document_other_found = return_field_value("sum(document_currency)","com_export_proceed_rlzn_dtls a, com_export_proceed_realization b"," a.mst_id = b.id AND a.type = '1' AND b.is_invoice_bill = 1 AND b.invoice_bill_id='".$val[('sub_id')]."' AND a.account_head in (87)");
                                    echo "<a href='#report_detals' onclick= \"openmypage('$submision_id');\">".number_format($document_other_found,2)."</a>";
                                ?>
                                </td>

                                <td width='75' align='right'><?echo number_format($realize_sc_loc_com[$val[("rlz_id")]][62]['loc_com'],2);?></td>
                                <td width='75' align='right'><?echo number_format($realize_sc_foreign_com[$val[("rlz_id")]][61]['for_com'],2);?></td>


                                <td  align="right"  width="85">
                                <?
                                    $document_adj = return_field_value("sum(document_currency)","com_export_proceed_rlzn_dtls a, com_export_proceed_realization b"," a.mst_id = b.id AND a.type = '1' AND b.is_invoice_bill = 1 AND b.invoice_bill_id='".$val[('sub_id')]."' AND a.account_head not in (5,6,10,15,65,81,11,87)");
                                    echo "<a href='#report_detals' onclick= \"openmypage('$submision_id');\">".number_format($document_adj,2)."</a>";
                                ?>
                                </td>
                                <td  align="right" width="85" ><? $balen_sc=($val[('net_invo_value')]-($val[('distribute_realize')]+$val[('deduct_realize')])); echo number_format($balen_sc,2);  ?></td>
                            </tr>
                            <?
                            $sc_bill_qty+=$inv_qty;
                            $sc_bill_value +=$val[('net_invo_value')];
                            $sc_doc_col_value +=$val[('sub_collection')];
                            if($purchase_amt_sc_sc) $sc_bill_value_sub +=$val[('net_invo_value')];
                            $sc_doc_pur_value +=$purchase_amt_sc;
                            $sc_doc_distribute_value +=$val[('distribute_realize')];
                            $sc_realized_deduct += $val[('deduct_realize')];
                            $sc_erq += $val[('erq')];
                            $sc_btb += $val[('btb')];
                            $sc_cd += $val[('cd')];
                            $sc_cc += $val[('cc')];
                            $sc_sundry_ac+= $val[('sun_ac')];
                            $sc_std_spaecial += $val[('std')];
                            $sc_fdbc += $val[('fdbc')];
                            $sc_document_other_found+= $document_other_found;
                            $sc_exp_adj += $document_adj;
                            $sc_short_ship_export += $document_adj;
                            $tot_sc_loc_com +=$realize_sc_loc_com[$val[("rlz_id")]][62]['loc_com'];
                            $tot_for_sc_com +=$realize_sc_foreign_com[$val[("rlz_id")]][61]['for_com'];
                            $i++;$j++;
                        }
                        if(!empty($realize_sc_arr))
                        {
                            ?>
                            <tr align="right" bgcolor="#CCCCCC">
                                <td></td>
                                <td></td>
                                <td></td>
                                <td><b>Sub-Total</b></td>
                                <td><b><? echo number_format($sc_bill_qty,0); $gt_sc_bill_qty+=$sc_bill_qty; ?></b></td>
                                <td><b><? echo number_format($sc_bill_value,2); $gt_sc_bill_value+=$sc_bill_value; ?></b></td>
                                <td><b><? echo number_format($sc_doc_col_value,2); $gt_sc_doc_col_value+=$sc_doc_col_value; ?></b></td>
                                <td><b><? echo number_format($sc_bill_value_sub,2); $gt_sub_sc_bill_value+=$sc_bill_value_sub; ?></b></td>
                                <td><b><? echo number_format($sc_doc_pur_value,2); $gt_sc_doc_pur_value+=$sc_doc_pur_value;  ?></b></td>
                                <td></td>
                                <td></td>
                                <td><b><? echo number_format($sc_doc_distribute_value,2); $gt_sc_doc_distribute_value+= $sc_doc_distribute_value; ?></b></td>
                                <td></td>
                                <td><b><? echo number_format($sc_realized_deduct,2); $gt_sc_realized_deduct+= $sc_realized_deduct; ?></b></td>
                                <td><b><? echo number_format($sc_erq,2); $gt_sc_erq+=$sc_erq; ?></b></td>
                                <td><b><? echo number_format($sc_btb,2); $gt_sc_btb+=$sc_btb; ?> </b></td>
                                <td><b><? echo number_format($sc_sundry_ac,2); $gt_sc_sundry_ac+= $sc_sundry_ac; ?></b></td>
                                <td><b><? echo number_format($sc_std_spaecial,2); $gt_sc_std_spaecial+= $sc_std_spaecial; ?></b></td>
                                <td><b><? echo number_format($sc_cd,2); $gt_sc_cd+=$sc_cd; ?></b></td>
                                <td><b><? echo number_format($sc_cc,2); $gt_sc_cc+= $sc_cc; ?></b></td>
                                <td><b><? echo number_format($sc_fdbc,2); $gt_sc_fdbc+= $sc_fdbc; ?></b></td>
                                <td><b><? echo number_format($sc_document_other_found,2); $gt_sc_document_other_found+=$sc_document_other_found; ?></b></td>

                                <td><p><strong><? echo number_format($tot_sc_loc_com,2); $gt_tot_sc_loc_com+=$tot_sc_loc_com;?></strong></p>&nbsp;</td>
                                <td><p><strong><? echo number_format($tot_for_sc_com,2); $gt_tot_sc_for_com+=$tot_for_sc_com;?></strong></p>&nbsp;</td>


                                <td><b><? echo number_format($sc_exp_adj,2); $gt_sc_exp_adj+=$sc_exp_adj; ?></b></td>
                                <td><b><? //echo number_format($sc_exp_adj,2); $gt_sc_exp_adj+=$sc_exp_adj; ?></b></td>
                            </tr>
                            <?
                        }
                        ?>
                    </tbody>
                </table>

                <table width="2150" rules="all" border="1" class="rpt_table" align="left" style="margin-bottom:20px;">

                    <tfoot>
                        <tr align="right">
                            <th colspan="4"><b>Grand Total</b></th>
                            <th width="70"><? echo number_format($gt_lc_bill_qty+$gt_sc_bill_qty,0);?></th>
                            <th width="85"><b><? echo number_format($gt_lc_bill_value+$gt_sc_bill_value,2);?></b></th>
                            <th width="85"><b><? echo number_format($gt_lc_doc_col_value+$gt_sc_doc_col_value,2);?></b></th>
                            <th width="90"><b><? echo number_format($gt_sub_lc_bill_value+$gt_sub_sc_bill_value,2);?></b></th>
                            <th width="90"><b><? echo number_format($gt_lc_doc_pur_value+$gt_sc_doc_pur_value,2);?></b></th>
                            <th width="50"></th>
                            <th width="80"></th>
                            <th width="85"><b><? echo number_format($gt_lc_doc_distribute_value+$gt_sc_doc_distribute_value,2);?></b></th>
                            <th width="80"></th>
                            <th width="85"><b><? echo number_format($gt_lc_realized_deduct+$gt_sc_realized_deduct,2);?></b></th>
                            <th width="75"><b><? echo number_format($gt_lc_erq+$gt_sc_erq,2);?></b></th>
                            <th width="75"><b><? echo number_format($gt_lc_btb+$gt_sc_btb,2);?> </b></th>
                            <th width="75"><b><? echo number_format($gt_lc_sundry_ac+$gt_sc_sundry_ac,2);?></b></th>
                            <th width="75"><b><? echo number_format($gt_lc_std_spaecial+$gt_sc_std_spaecial,2);?></b></th>
                            <th width="75"><b><? echo number_format($gt_lc_cd+$gt_sc_cd,2);?></b></th>
                            <th width="75"><b><? echo number_format($gt_lc_cc+$gt_sc_cc,2);?></b></th>
                            <th width="75"><b><? echo number_format($gt_lc_fdbc+$gt_sc_fdbc,2);?></b></th>
                            <th width="100"><b><? echo number_format($gt_lc_document_other_found+$gt_sc_document_other_found,2);?></b></th>
                            <th width="75"><b><?echo number_format($gt_tot_loc_com+$gt_tot_sc_loc_com,2);?></b></th>
                            <th width="75"><b><?echo number_format($gt_tot_for_com+$gt_tot_sc_for_com,2);?></b></th>
                            
                            <th width="85"><b><? echo number_format($gt_lc_exp_adj+$gt_sc_exp_adj,2);?></b></th>
                            <th width="85"><b><? //echo number_format($gt_lc_exp_adj+$gt_sc_exp_adj,2);?></b></th>
                        </tr>
                    </tfoot>
                </table>

                <table width="1900" cellpadding="0" cellspacing="0" border="0" rules="all" id="" align="left">
                    <tr>
                    <td><strong style="font:Arial, Helvetica, sans-serif; font-size:16px;">Document Submitted Bank:</strong></td>
                    </tr>
                </table>

                <table width="1140" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all" id="" align="left">
                    <thead>
                        <tr>
                            <th  rowspan="2">Sl</th>
                            <th width="130" rowspan="2">Invoice No.</th>
                            <th width="150" rowspan="2">Export Bill No.</th>
                            <th width="80" rowspan="2">Bill Date</th>
                            <th width="80" rowspan="2">Submission Date</th>
                            <th width="100" rowspan="2">Inv/Bill Qty/Pcs</th>
                            <th width="100" rowspan="2">Bill Value</th>
                            <th width="100">Sub Under Collection</th>
                            <th colspan="4">Sub. Under Purchase</th>
                        </tr>
                        <tr>
                            <th>Amount</th>
                            <th width="105">Bill Amount</th>
                            <th width="105">Purchase Amount</th>
                            <th width="50">(%)</th>
                            <th width="90">Purchase Date</th>
                        </tr>
                    </thead>
                </table>

                <table width="1140" rules="all" class="rpt_table" align="left" id="" border="1">
                    <tbody>
                        <?
                        $sub_invoice=return_library_array("select id, invoice_no from com_export_invoice_ship_mst where is_lc=1 and lc_sc_id in($lc_Ids_arr) and status_active=1 and is_deleted=0 union all select id, invoice_no from com_export_invoice_ship_mst where is_lc=2 and lc_sc_id in($sc_Ids_arr) and status_active=1 and is_deleted=0","id","invoice_no");					
                        $sub_lc_num_chack=array();$k=1;$o=1;
                        foreach ($sub_lc_arr as $key=>$val)
                        {
                            if(!in_array($val["lc_sc_id"],$sub_lc_num_chack))
                            {
                                $sub_lc_num_chack[]=$val["lc_sc_id"];
                                if($o!=1)
                                {
                                    ?>
                                    <tr align="right" bgcolor="#CCCCCC">
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td><b>Sub-Total</b></td>
                                        <td><b><? echo number_format($sub_lc_bill_qty,0); $gt_sub_lc_bill_qty+=$sub_lc_bill_qty; ?></b></td>
                                        <td><b><? echo number_format($sub_lc_bill_value,2); $gt_submiss_lc_bill_value+=$sub_lc_bill_value; ?></b></td>
                                        <td><b><? echo number_format($sub_lc_doc_col_value,2); $gt_sub_lc_doc_col_value+=$sub_lc_doc_col_value; ?></b></td>
                                        <td><b><? echo number_format($sub_lc_bill_value_sub,2); $gtsub_sub_lc_bill_value+=$sub_lc_bill_value_sub; ?></b></td>
                                        <td><b><? echo number_format($sub_lc_doc_pur_value,2); $gt_sub_lc_doc_pur_value+=$sub_lc_doc_pur_value; ?></b></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <?
                                }
                                $o++;
                                $sub_lc_bill_qty =0;
                                $sub_lc_bill_value =0;
                                $sub_lc_doc_col_value =0;
                                $sub_lc_doc_pur_value =0;

                                ?>
                                <tr>
                                    <td colspan="12" style="background-color:#FDF4EF"><b>
                                    <?
                                    /*if($val[csf('is_lc')]==1) $export_number=return_field_value("export_lc_no","com_export_lc","id='".$val[('lc_sc_id')]."' and status_active=1");
                                    echo "Export L/C No."." - ".$export_number; */
                                    if($val[('is_lc')]==1)
                                    {
                                        $export_number='';
                                        $lc_sc_ids=$val[('lc_sc_id')];
                                        $export_number.=return_field_value("export_lc_no","com_export_lc","id=$lc_sc_ids and status_active=1","export_lc_no");
                                    }
                                    echo "Export L/C No."." - ".$export_number;
                                    ?>
                                    </b>
                                    </td>
                                </tr>
                                <?
                            }
                            if ($i%2==0) $bgcolor="#E9F3FF";
                            else $bgcolor="#FFFFFF";
                            ?>
                            <tr  bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                <td ><? echo $k;?></td>
                                <td width="130" ><p>
                                <? 
                                $inv_id_arr=array_unique(explode(",",$val[('inv_id')]));
                                $all_invoice_no="";
                                foreach($inv_id_arr as $inv_ids)
                                {
                                    $all_invoice_no.='<a href="##" onClick="fnc_invoice_show('.$inv_ids.')" >'.$sub_invoice[$inv_ids].'</a>,';
                                }
                                $all_invoice_no=chop($all_invoice_no,",");
                                echo $all_invoice_no;
                                ?></p></td>
                                <td width="150" ><? echo $val[('bank_ref_no')];?></td>
                                <td width="80" align="center"><? echo change_date_format($val[('bank_ref_date')]);?></td>
                                <td width="80" align="center"><? echo change_date_format($val[('submit_date')]);?></td>
                                <td width="100" align="right">
                                <?
                                /*$id=$val[csf('inv_id')];
                                $inv_qty=return_field_value("sum(a.current_invoice_qnty) as current_invoice_qnty"," com_export_invoice_ship_dtls a, com_export_doc_submission_invo b ","a.mst_id=b.invoice_id and b.invoice_id in($id) and a.status_active=1","current_invoice_qnty")*/
                                $inv_qty=$val[('invoice_quantity')];
                                echo $inv_qty;
                                $submision_id=$val[('sub_id')];
                                $purchase_amt_sub=return_field_value("sum(b.lc_sc_curr) as dom_curr"," com_export_doc_sub_trans b","b.doc_submission_mst_id='$submision_id' group by b.doc_submission_mst_id ","dom_curr");

                                ?>
                                </td>
                                <td width="100" align="right"><? echo number_format($val[('net_invo_value')],2); ?></td>
                                <td width="100" align="right"><? echo number_format($val[('sub_collection')],2);?></td>
                                <td width="105" align="right"><? if($purchase_amt_sub) echo number_format($val[('net_invo_value')],2); else echo "0.00";?></td>
                                <td width="105" align="right">
                                <?
                                echo number_format($purchase_amt_sub,2);
                                //echo $submision_id;
                                ?>
                                </td>
                                <td width="50" align="right"><? echo number_format((($purchase_amt_sub/$val[('net_invo_value')])*100),2)."%"; ?> </td>
                                <td width="90" align="center"><? if($val[('negotiation_date')]!='0000-00-00') echo change_date_format($val[('negotiation_date')]);?></td>
                            </tr>
                            <?
                            $sub_lc_bill_qty +=$inv_qty;
                            $sub_lc_bill_value +=$val[('net_invo_value')];
                            if($purchase_amt_sub) $sub_lc_bill_value_sub +=$val[('net_invo_value')];
                            $sub_lc_doc_col_value +=$val[('sub_collection')];
                            $sub_lc_doc_pur_value +=$purchase_amt_sub;
                            $i++;$k++;
                        }
                        if(!empty($sub_lc_arr))
                        {
                        ?>
                            <tr align="right" bgcolor="#CCCCCC">
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td><b>Sub-Total</b></td>
                                <td><b><? echo number_format($sub_lc_bill_qty,0); $gt_sub_lc_bill_qty+=$sub_lc_bill_qty; ?></b></td>
                                <td><b><? echo number_format($sub_lc_bill_value,2); $gt_submiss_lc_bill_value+=$sub_lc_bill_value; ?></b></td>
                                <td><b><? echo number_format($sub_lc_doc_col_value,2); $gt_sub_lc_doc_col_value+=$sub_lc_doc_col_value; ?></b></td>
                                <td><b><? echo number_format($sub_lc_bill_value_sub,2); $gtsub_sub_lc_bill_value+=$sub_lc_bill_value_sub; ?></b></td>
                                <td><b><? echo number_format($sub_lc_doc_pur_value,2); $gt_sub_lc_doc_pur_value+=$sub_lc_doc_pur_value; ?></b></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <?
                            //$submision_id=0;
                        }

                        //echo "press <pre>";print_r($sub_sc_arr);
                        $sub_sc_num_chack=array();$m=1;$p=1;
                        foreach ($sub_sc_arr as $key=>$val)
                        {
                            if(!in_array($val["lc_sc_id"],$sub_sc_num_chack))
                            {
                                $sub_sc_num_chack[]=$val["lc_sc_id"];
                                if($p!=1)
                                {
                                    ?>
                                    <tr align="right" bgcolor="#CCCCCC">
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td><b>Sub-Total</b></td>
                                        <td><b><? echo number_format($sub_sc_bill_qty,0); $gt_sub_sc_bill_qty+=$sub_sc_bill_qty; ?></b></td>
                                        <td><b><? echo number_format($sub_sc_bill_value,2); $gt_submiss_sc_bill_value+=$sub_sc_bill_value; ?></b></td>
                                        <td><b><? echo number_format($sub_sc_doc_col_value,2); $gt_sub_sc_doc_col_value+=$sub_sc_doc_col_value; ?></b></td>
                                        <td><b><? echo number_format($sub_sc_bill_value,2); $gtsub_sub_sc_bill_value+=$sub_sc_bill_value; ?></b></td>
                                        <td><b><? echo number_format($sub_sc_doc_pur_value,2); $gt_sub_sc_doc_pur_value+=$sub_sc_doc_pur_value; ?></b></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <?
                                }
                                $p++;
                                $sub_sc_bill_qty =0;
                                $sub_sc_bill_value =0;
                                $sub_sc_doc_col_value =0;
                                $sub_sc_doc_pur_value =0;
                                ?>
                                <tr>
                                    <td colspan="12" style="background-color:#FDF4EF"><b>
                                    <?
                                    /*if($val[('is_lc')]==2) $export_number=return_field_value("contract_no","com_sales_contract","id='".$val[('lc_sc_id')]."' and status_active=1");
                                    echo "Export Salse Contact No."." - ".$export_number;*/
                                    $lc_sc_ids=$val[('lc_sc_id')];
                                    if($val[('is_lc')]==2)
                                    {
                                        $export_number='';
                                        $lc_sc_id=explode(",",$lc_sc_ids);
                                        $lc_sc_id=array_unique($lc_sc_id);
                                        foreach($lc_sc_id as $lcsc_id)
                                        {
                                            //$export_number.=return_field_value("export_lc_no","com_export_lc","id=$lcsc_id and status_active=1","export_lc_no").",";
                                            $export_number=return_field_value("contract_no","com_sales_contract","id=$lcsc_id and status_active=1","contract_no");
                                        }
                                    }
                                    echo "Export Salse Contact No."." - ".chop($export_number,",");
                                    ?></b></td>
                                </tr>
                                <?
                            }
                            if ($i%2==0) $bgcolor="#E9F3FF";
                            else $bgcolor="#FFFFFF";
                            ?>
                                <tr  bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                <td ><? echo $m;?></td>
                                <td width="130" align="center"><p>
                                <? 
                                $inv_id_arr=array_unique(explode(",",$val[('inv_id')]));
                                $all_invoice_no="";
                                foreach($inv_id_arr as $inv_ids)
                                {
                                    $all_invoice_no.='<a href="##" onClick="fnc_invoice_show('.$inv_ids.')" >'.$sub_invoice[$inv_ids].'</a>,';
                                }
                                $all_invoice_no=chop($all_invoice_no,",");
                                echo $all_invoice_no;
                                //echo $val[('invoice_no')];
                                ?></p></td>
                                <td width="150" align="center"><? echo $val[('bank_ref_no')];?></td>
                                <td width="80" align="center"><? echo change_date_format($val[('bank_ref_date')]);?></td>
                                <td width="80" align="center"><? echo change_date_format($val[('submit_date')]);?></td>
                                <td width="100" align="right">
                                <?
                                /*$id=$val[csf('inv_id')];
                                $inv_qty=return_field_value("sum(a.current_invoice_qnty) as current_invoice_qnty"," com_export_invoice_ship_dtls a, com_export_doc_submission_invo b ","a.mst_id=b.invoice_id and b.invoice_id in($id) and a.status_active=1","current_invoice_qnty");*/
                                $inv_qty=$val[('invoice_quantity')];
                                echo $inv_qty;
                                $submision_id=$val[('sub_id')];
                                $purchase_amt=return_field_value("sum(b.lc_sc_curr) as dom_curr"," com_export_doc_sub_trans b","b.doc_submission_mst_id='$submision_id' ","dom_curr");
                                ?>
                                </td>
                                <td width="100" align="right"><? echo number_format($val[('net_invo_value')],2); ?></td>
                                <td width="100" align="right"><? echo number_format($val[('sub_collection')],2);?></td>
                                <td width="105" align="right"><? if($purchase_amt) echo number_format($val[('net_invo_value')],2); else echo "0.00";?></td>
                                <td width="105" align="right">
                                <?
                                echo number_format($purchase_amt,2);
                                ?>
                                </td>
                                <td width="50" align="right"><? echo number_format((($purchase_amt/$val[('net_invo_value')])*100),2)."%"; ?></td>
                                <td width="90" align="center"><? echo change_date_format($val[('negotiation_date')]);?></td>
                            </tr>
                            <?
                            $sub_sc_bill_qty +=$inv_qty;
                            $sub_sc_bill_value +=$val[('net_invo_value')];
                            $sub_sc_doc_col_value +=$val[('sub_collection')];
                            $sub_sc_doc_pur_value +$val[('purchase_amount')];
                            $i++;$m++;
                        }
                        if(!empty($sub_sc_arr))
                        {
                            ?>
                            <tr align="right" bgcolor="#CCCCCC">
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td><b>Sub-Total</b></td>
                                <td><b><? echo number_format($sub_sc_bill_qty,0); $gt_sub_sc_bill_qty+=$sub_sc_bill_qty; ?></b></td>
                                <td><b><? echo number_format($sub_sc_bill_value,2); $gt_submiss_sc_bill_value+=$sub_sc_bill_value; ?></b></td>
                                <td><b><? echo number_format($sub_sc_doc_col_value,2); $gt_sub_sc_doc_col_value+=$sub_sc_doc_col_value; ?></b></td>
                                <td><b><? echo number_format($sub_sc_bill_value,2); $gtsub_sub_sc_bill_value+=$sub_sc_bill_value; ?></b></td>
                                <td><b><? echo number_format($sub_sc_doc_pur_value,2); $gt_sub_sc_doc_pur_value+=$sub_sc_doc_pur_value; ?></b></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <?
                        }
                        ?>
                    </tbody>
                </table>

                <table width="1140" rules="all" border="1" class="rpt_table" align="left" style="margin-bottom:20px;">
                    <tfoot>
                        <tr align="right">
                            <th colspan="5"><b>Grand Total</b></th>
                            <th width="100"><? echo number_format($gt_sub_lc_bill_qty+$gt_sub_sc_bill_qty,0);?></th>
                            <th width="100"><b><? echo number_format($gt_submiss_lc_bill_value+$gt_submiss_sc_bill_value,2);?></b></th>
                            <th width="100"><b><? echo number_format($gt_sub_lc_doc_col_value+$gt_sub_sc_doc_col_value,2);?></b></th>
                            <th width="105"><b><? echo number_format($gtsub_sub_lc_bill_value+$gtsub_sub_sc_bill_value,2);?></b></th>
                            <th width="105"><b><? echo number_format($gt_sub_lc_doc_pur_value+$gt_sub_sc_doc_pur_value,2);?></b></th>
                            <th width="50"></th>
                            <th width="90"></th>
                        </tr>
                    </tfoot>
                </table>

                <table width="1900" cellpadding="0" cellspacing="0" border="0" rules="all" id="" align="left">
                    <tr>
                    <td><strong style="font:Arial, Helvetica, sans-serif; font-size:16px;">Document Submitted Buyer:</strong></td>
                    </tr>
                </table>

                <table width="1070" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all" id="" align="left">
                    <thead>
                        <tr>
                            <th width="50">Sl</th>
                            <th width="150">Buyer</th>
                            <th width="400">Invoice No.</th>
                            <th width="100">System Id.</th>
                            <th width="100">Submit Date</th>
                            <th width="100">Submit Qty</th>
                            <th >Submit Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?
                     

                        $sub_sc_num_chack=array();$m=1;$p=1;
                        foreach ($sub_buyer_arr as $key=>$val)
                        {
                            if(!in_array($val["lc_sc_id"],$sub_sc_num_chack))
                            {
                                $sub_sc_num_chack[]=$val["lc_sc_id"];
                                if($p!=1)
                                {
                                    ?>
                                    <tr align="right" bgcolor="#CCCCCC">
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td><b>Sub-Total</b></td>
                                        <td><b><? echo number_format($submit_qty,0); $gt_submit_qty+=$submit_qty; ?></b></td>
                                        <td><b><? echo number_format($submit_value,2); $gt_submit_value+=$submit_value; ?></b></td>
                                    </tr>
                                    <?
                                }
                                $p++;
                                $submit_qty =$submit_value =0;
                                ?>
                                <tr>
                                    <td colspan="7" style="background-color:#FDF4EF"><b>
                                    <?
                                    if($val[('is_lc')]==2) $export_number=return_field_value("contract_no","com_sales_contract","id in(".$val[('lc_sc_id')].") and status_active=1");
                                    echo "Export Salse Contact No."." - ".$export_number;
                                    ?></b></td>
                                </tr>
                                <?
                            }
                            if ($i%2==0)
                            $bgcolor="#E9F3FF";
                            else
                            $bgcolor="#FFFFFF";
                            ?>                            
                            <tr  bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                <td width="50" align="center"><? echo $m;?></td>
                                <td width="150" ><? echo $buyer_name_arr[$val[('buyer_id')]];?></td>
                                <td width="400" ><p>
                                <? 
                                $inv_id_arr=array_unique(explode(",",$val[('inv_id')]));
                                $all_invoice_no="";
                                foreach($inv_id_arr as $inv_ids)
                                {
                                    $all_invoice_no.='<a href="##" onClick="fnc_invoice_show('.$inv_ids.')" >'.$sub_invoice[$inv_ids].'</a>,';
                                }
                                $all_invoice_no=chop($all_invoice_no,",");
                                echo $all_invoice_no;
                                //echo $val[('invoice_no')];
                                ?></p></td>
                                <td width="100" align="center"><? echo $val[('sub_id')];?></td>
                                <td width="100" align="center"><? echo change_date_format($val[('bank_ref_date')]);?></td>
                                <td width="100" align="right"><? echo  number_format($val[('invoice_quantity')],0);?> </td>
                                <td align="right"><? echo number_format($val[('net_invo_value')],2); ?></td>
                            </tr>
                            <?
                            $submit_qty +=$val[('invoice_quantity')];
                            $submit_value +=$val[('net_invo_value')];
                            $i++;$m++;
                        }
                        if(!empty($sub_buyer_arr))
                        {
                            ?>
                            <tr align="right" bgcolor="#CCCCCC">
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td><b>Sub-Total</b></td>
                                <td><b><? echo number_format($submit_qty,0); $gt_submit_qty+=$submit_qty; ?></b></td>
                                <td><b><? echo number_format($submit_value,2); $gt_submit_value+=$submit_value; ?></b></td>
                            </tr>
                            <?
                        }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr align="right">
                            <th width="50"></th>
                            <th width="150"></th>
                            <th width="400"></th>
                            <th width="100"></th>
                            <th width="100"><b>Grand Total</b></th>
                            <th width="100"><? echo number_format($gt_submit_qty,0);?></th>
                            <th ><b><? echo number_format($gt_submit_value,2);?></b></th>
                        </tr>
                    </tfoot>
                </table>

                <table border="0" width="1900"><tr>&nbsp;</tr></table><br>

                <table width="1900" cellpadding="0" cellspacing="0" border="0" rules="all" id="" align="left">
                    <tr>
                    <td><strong style="font:Arial, Helvetica, sans-serif; font-size:16px;">Un-Submitted Invoice:</strong></td>
                    </tr>
                </table>

                <table width="750" cellpadding="0" cellspacing="0" border="1" rules="all" align="left" class="rpt_table">
                    <thead>
                        <tr>
                            <th width="50">SL</td>
                            <th width="170">LC/SC No</td>
                            <th width="180">Invoice No</td>
                            <th width="90">Invoice Date</td>
                            <th width="100">Invoice Quantity</td>
                            <th>Invoice Value</td>
                        </tr>
                    </thead>
                    <tbody>
                    <?
                    $lc_no_arr=return_library_array( "select id,export_lc_no from com_export_lc where status_active=1",'id','export_lc_no');
                    $sc_no_arr=return_library_array( "select id,contract_no from com_sales_contract where status_active=1",'id','contract_no');

                    //var_dump($sql);

                    // print_r($inv_arr);
                    $k=1;
                    foreach($inv_arr as $inv_id=>$row)
                    {
                        if ($i%2==0) $bgcolor="#E9F3FF";
                        else $bgcolor="#FFFFFF";
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td align="center"><? echo $k;?></td>
                            <td><? if($row[('is_lc')]==1) echo $lc_no_arr[$row[('lc_sc_id')]]; else echo  $sc_no_arr[$row[('lc_sc_id')]]?></td>
                            <td><a href="##" onClick="fnc_invoice_show('<? echo $inv_id;?>')"><? echo $row[('invoice_no')]?></a></td>
                            <td align="center"><? echo change_date_format($row[('invoice_date')]);?></td>
                            <td align="right"><? echo number_format($row[('invoice_quantity')],2);?></td>
                            <td align="right"><? echo number_format($row[('invoice_value')],2);?></td>
                        </tr>
                        <?
                        $total_unsubmit+=$row[('invoice_value')];
                        $total_invoice_quantity+=$row[('invoice_quantity')];
                        $k++;$i++;
                    }
                    ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4" align="right">Total</td>
                            <th  align="right"><? echo number_format($total_invoice_quantity,2);?></td>
                            <th  align="right"><? echo number_format($total_unsubmit,2);?></td>
                        </tr>
                    </tfoot>
                </table>

                <table border="0" width="1900"><tr>&nbsp;</tr></table><br>

                <table width="1900" cellpadding="0" cellspacing="0" border="0" rules="all" id="" align="left">
                    <tr>
                    <td><strong style="font:Arial, Helvetica, sans-serif; font-size:16px;">Discount/Adjustment Details:</strong></td>
                    </tr>
                </table>

                <table width="750" cellpadding="0" cellspacing="0" border="1" rules="all" align="left" class="rpt_table">
                    <thead>
                        <tr>
                            <th width="30">Sl</td>
                            <th width="140">LC/SC No</td>
                            <th width="140">Invoice No</td>
                            <th width="110">Invoice Quantity</td>
                            <th width="110">Gross Value</td>
                            <th width="110">Discount/Commission</td>
                            <th >Net Value</td>
                        </tr>
                    </thead>
                    <tbody>
                    <?
                    //$lc_no_arr=return_library_array( "select id,export_lc_no from com_export_lc where status_active=1",'id','export_lc_no');
                    //$sc_no_arr=return_library_array( "select id,contract_no from com_sales_contract where status_active=1",'id','contract_no');

                    //var_dump($sql);
                    $p=1;
                    foreach($adjustment_arr as $inv_id=>$row)
                    {
                        if ($i%2==0)
                        $bgcolor="#E9F3FF";
                        else
                        $bgcolor="#FFFFFF";
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td align="center"><? echo $p;?></td>
                            <td><? echo $row['lc_sc_no']?></td>
                            <td><a href="##" onClick="fnc_invoice_show('<? echo $inv_id;?>')"><? echo $row['invoice_no']?></a></td>
                            <td align="right"><?  echo number_format($row['current_invoice_qnty'],2); $to_current_invoice_qnty+=$row['current_invoice_qnty'];?></td>
                            <td align="right"><? echo number_format($row['current_invoice_value'],2); $to_adjst_gross+=$row['current_invoice_value'];?></td>
                            <td align="right"><?  $adjust_amt=$row['current_invoice_value']-$row['net_invo_value']; echo number_format($adjust_amt,2); $to_adjust+=$adjust_amt;?></td>
                            <td align="right"><? echo number_format($row['net_invo_value'],2); $to_adjust_net+=$row['net_invo_value'];?></td>
                        </tr>
                        <?
                        $i++;$p++;
                    }
                    ?>
                    </tbody>
                    <tfoot>
                        <tr >
                            <th colspan="3"><strong>Total</strong></td>
                            <td align="right"><? echo number_format($to_current_invoice_qnty,2)?></td>
                            <td align="right"><? echo number_format($to_adjst_gross,2)?></td>
                            <td align="right"><? echo number_format($to_adjust,2)?></td>
                            <td align="right"><? echo number_format($to_adjust_net,2)?></td>
                        </tr>
                    </tfoot>
                </table>

                <table border="0" width="1900"><tr>&nbsp;</tr></table><br>

                <table width="1900" cellpadding="0" cellspacing="0" border="0" rules="all" id="" align="left">
                    <tr>
                    <td><strong style="font:Arial, Helvetica, sans-serif; font-size:16px;">Import Status :</strong></td>
                    </tr>
                </table>
                <table width="1550" cellpadding="0" cellspacing="0" border="1" rules="all" align="left" class="rpt_table">
                    <thead>
                        <tr>
                            <th width="50">Sl</td>
                            <th width="120">BTB LC NO</td>
                            <th width="80">LC Date</td>
                            <th width="100">LC Value</td>
                            <th width="80">Ship Date</td>
                            <th width="100">Pay Term</td>
                            <th width="100">Bank Ref No</td>
                            <th width="120">Invoice NO</td>
                            <th width="80">Invoice Date</td>
                            <th width="100">Invoice Value</td>
                            <th width="80">Maturaty Date</td>
                            <th width="100">Paid Amount</td>
                            <th width="80">Paid Date</td>
                            <th width="120">Supplier Name</td>
                            <th width="120">Item Category</td>
                            <th >PI Qty. (Kg)</td>
                        </tr>
                    </thead>

                    <tbody>

                    <?
                    //$lc_no_arr=return_library_array( "select id,export_lc_no from com_export_lc where status_active=1",'id','export_lc_no');
                    //$sc_no_arr=return_library_array( "select id,contract_no from com_sales_contract where status_active=1",'id','contract_no');

                    //var_dump($sql);
                    $lc_wise_value=array();
                    $all_btb_id=$btb_mst_lc_id.",".$btb_mst_sc_id;
                    $all_btb_id=implode(",",array_unique(explode(",",$all_btb_id)));
                    //echo $all_btb_id;
                    $btb_inv_sql="SELECT a.id as btb_id, a.lc_number, a.lc_date, a.lc_value, a.last_shipment_date, a.payterm_id, a.supplier_id, a.item_category_id, sum(c.current_acceptance_value) as invoice_value, d.id as invoice_id, d.invoice_no, d.invoice_date, d.maturity_date, 1 as type, d.bank_ref
                    from com_btb_lc_master_details a, com_btb_export_lc_attachment b, com_import_invoice_dtls c, com_import_invoice_mst d
                    where a.id=b.import_mst_id and b.import_mst_id=c.btb_lc_id and c.import_invoice_id=d.id and c.current_acceptance_value>0 and a.payterm_id<>3 and a.id in($all_btb_id) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1
                    group by a.id, a.lc_number, a.lc_date, a.lc_value, a.last_shipment_date, a.payterm_id, a.supplier_id, a.item_category_id, d.id, d.invoice_no, d.invoice_date, d.maturity_date, d.bank_ref
                    union all
                    select a.id as btb_id, a.lc_number, a.lc_date, a.lc_value, a.last_shipment_date, a.payterm_id, a.supplier_id, a.item_category_id, null as invoice_value, null as invoice_id, null as invoice_no, null as invoice_date, null as maturity_date, 2 as type, null as bank_ref
                    from com_btb_lc_master_details a, com_btb_export_lc_attachment b
                    where a.id=b.import_mst_id and a.payterm_id=3 and a.id in($all_btb_id) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";

                    // echo $btb_inv_sql;

                    $btb_inv_result=sql_select($btb_inv_sql);
                    $all_import_invoice_id=array();
                    $all_pi_id="";
                    $btb_id=$pi_category=array();
                    foreach($btb_inv_result as $row)
                    {
                        if($row[csf("invoice_id")]!="") $all_import_invoice_id[$row[csf("invoice_id")]]=$row[csf("invoice_id")];
                        $btb_id[$row[csf("btb_id")]]=$row[csf("btb_id")];
                    }
                    $btb_id=where_con_using_array($btb_id,0,'a.com_btb_lc_master_details_id');
                    $pi_sql=sql_select("SELECT a.com_btb_lc_master_details_id as BTB_ID, b.item_category_id as ITEM_CATEGORY_ID from com_btb_lc_pi a, com_pi_master_details b where a.pi_id=b.id and a.status_active=1 and b.status_active=1");
                    foreach($pi_sql as $row)
                    {
                        $pi_category[$row["BTB_ID"]].=$item_category[$row["ITEM_CATEGORY_ID"]].',';
                    }


                    if(count($all_import_invoice_id)>0)
                    {
                        $pi_sql=sql_select("select a.import_invoice_id, b.quantity from com_import_invoice_dtls a, com_pi_item_details b where a.pi_id=b.pi_id and b.status_active=1 and b.is_deleted=0 and a.import_invoice_id in(".implode(",",$all_import_invoice_id).")");
                        $pi_data=array();
                        foreach($pi_sql as $row)
                        {
                            $pi_data[$row[csf("import_invoice_id")]]=$row[csf("quantity")];
                        }
                        $paid2=return_field_value("sum(accepted_ammount) as paid"," com_import_payment","lc_id in($usance_paid_lc_id) and status_active='1'","paid");

                        /*echo "select id, invoice_id, accepted_ammount from com_import_payment where lc_id in($usance_paid_lc_id) and status_active='1' <br>
                        select id, invoice_id, payment_date, accepted_ammount from com_import_payment where invoice_id in(".implode(",",$all_import_invoice_id).") and status_active=1 and is_deleted=0";*/

                        $payment_sql=sql_select("select id, invoice_id, payment_date, accepted_ammount from com_import_payment where invoice_id in(".implode(",",$all_import_invoice_id).") and status_active=1 and is_deleted=0");
                        $payment_data=array();
                        foreach($payment_sql as $row)
                        {
                            $payment_data[$row[csf("invoice_id")]]["payment_date"]=$row[csf("payment_date")];
                            $payment_data[$row[csf("invoice_id")]]["accepted_ammount"]+=$row[csf("accepted_ammount")];
                        }
                    }

                    $p=1;
                    foreach($btb_inv_result as $row)
                    {
                        if ($i%2==0)
                        $bgcolor="#E9F3FF";
                        else
                        $bgcolor="#FFFFFF";
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td align="center"><? echo $p;?></td>
                            <td><p><? echo $row[csf('lc_number')];?>&nbsp;</p></td>
                            <td align="center"><? if($row[csf('lc_date')]!="" && $row[csf('lc_date')]!="0000-00-00") echo change_date_format($row[csf('lc_date')]);?></td>
                            <td align="right">
                                <?
                                    echo number_format($row[csf('lc_value')],2);
                                    //$tot_lc_value+=$row[csf('lc_value')];
                                    $lc_wise_value[$row[csf('lc_number')]]=$row[csf('lc_value')];
                                ?>
                            </td>
                            <td align="center"><?  if($row[csf('last_shipment_date')]!="" && $row[csf('last_shipment_date')]!="0000-00-00") echo change_date_format($row[csf('last_shipment_date')]); ?></td>
                            <td><p><? echo $pay_term[$row[csf('payterm_id')]];?>&nbsp;</p></td>
                            <td><p><? echo $row[csf("bank_ref")];?>&nbsp;</p></td>
                            <td><p><? echo $row[csf('invoice_no')];?>&nbsp;</p></td>
                            <td align="center"><? if($row[csf('invoice_date')]!="" && $row[csf('invoice_date')]!="0000-00-00") echo change_date_format($row[csf('invoice_date')]);?></td>
                            <td align="right"><? echo number_format($row[csf('invoice_value')],2); $tot_invoice_value+=$row[csf('invoice_value')];?></td>
                            <td align="center"><? if($row[csf('maturity_date')]!="" && $row[csf('maturity_date')]!="0000-00-00") echo change_date_format($row[csf('maturity_date')]);?></td>
                            <td align="right" title="<? echo $row[csf("type")]; ?>">
                            <?
                            if($row[csf("type")]==1)
                            {
                                echo  number_format($payment_data[$row[csf("invoice_id")]]["accepted_ammount"],2);
                                if($inv_check[$row[csf("invoice_id")]]=="")
                                {
                                    $inv_check[$row[csf("invoice_id")]]=$row[csf("invoice_id")];
                                    $tot_payment_amt+=$payment_data[$row[csf("invoice_id")]]["accepted_ammount"];
                                }
                            }
                            else
                            {
                                echo  number_format($row[csf("lc_value")],2);
                                $tot_payment_amt+=$row[csf("lc_value")];
                            }

                            ?></td>
                            <td align="center"><? if($payment_data[$row[csf("invoice_id")]]["payment_date"]!="" && $payment_data[$row[csf("invoice_id")]]["payment_date"]!="0000-00-00") echo change_date_format($payment_data[$row[csf("invoice_id")]]["payment_date"]);?></td>
                            <td><p><? echo $suplier_name_arr[$row[csf('supplier_id')]];?>&nbsp;</p></td>
                            <td><p><? echo implode(", ",array_unique(explode(",",chop($pi_category[$row["BTB_ID"]],','))));?>&nbsp;</p></td>
                            <td align="right">
                            <?
                            if($row[csf('item_category_id')]==1)
                            {
                                echo number_format($pi_data[$row[csf("invoice_id")]],2);
                                $tot_pi_qnty+=$pi_data[$row[csf("invoice_id")]];
                            }
                            ?></td>
                        </tr>
                        <?
                        $i++;$p++;
                    }
                    ?>
                    </tbody>
                    <tfoot>
                        <tr >
                            <!-- <th  align="right"><? //echo number_format($tot_lc_value,2);?></td> -->
                            <!-- <th colspan="8" align="right">Total:</td> -->
                            <th colspan="3" align="right">Total:</td>
                            <th align="right"><? echo number_format(array_sum($lc_wise_value),2); //number_format($tot_lc_value,2);?></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <th align="right"><? echo number_format($tot_invoice_value,2);?></td>
                            <td>&nbsp;</td>
                            <th align="right"><? echo number_format($tot_payment_amt,2);?></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <th align="right"><? if($row[csf('item_category_id')]==1) echo number_format($tot_pi_qnty,2);?></td>
                        </tr>
                    </tfoot>
                </table>
            </fieldset>
        </div>
        <?
        echo  "<div id='dev_id' style='display: none;'>" . $total_ad_ammount=number_format($to_adjust_net - $tot_payment_amt, 2) . "</div>";
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

if ($action=="acount_head_details")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$sub_id=str_replace("'","",$sub_id);
	//echo "select id, account_head FROM com_export_proceed_rlzn_dtls WHERE type = '1' group by id,account_head";die;
	$acount_head=return_library_array("select  account_head FROM com_export_proceed_rlzn_dtls WHERE type = '1' group by account_head","account_head","account_head");
	//var_dump($acount_head);die;
	$i=1;
	$loop=count($acount_head);
	if($loop>0)
	{
		$sql="SELECT ";
		foreach($acount_head as $key=>$val)
		{
			$sql.="sum(CASE WHEN account_head =$val  THEN document_currency else 0 END) AS sub_val_$val ";
			if($i<$loop) $sql .=",";
			$i++;
		}

		$sql .= "FROM com_export_proceed_rlzn_dtls a, com_export_proceed_realization b
				WHERE a.mst_id = b.id AND a.type = '1' AND b.is_invoice_bill = 1 AND b.invoice_bill_id  in($sub_id) and a.account_head not in(5,6,10,15,65,81,11) group by b.invoice_bill_id";
	}

	//echo $sql;die;
	$sql_re=sql_select($sql);
	?>
    <fieldset style="width:590px">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center" width="100%" style="overflow-x:auto;">
            <thead>
            <?
			//var_dump($sql_re);
			$j=1;$k=1;
			foreach($sql_re as $key=>$row)
			{
				if($k==1)
				{
					?>
					<tr>
					<?
					foreach($acount_head as $ac_key=>$ac_val)
					{
						if($row[csf("sub_val_$ac_val")]!=0)
						{
							?>
							<td><? echo  $commercial_head[$ac_val] ; ?></td>
							<?
						}
						$j++;
					}
					?>
					</tr>
					<?
				}
				?>
            	<tr>
                <?
				foreach($acount_head as $ac_key=>$ac_val)
                {
					if($row[csf("sub_val_$ac_val")]!=0)
					{
						?>
						<td ><? echo   number_format($row[csf("sub_val_$ac_val")],2); ?></td>
						<?
					}
				}
				?>
                </tr>
                <?
				$k++;
			}
			?>
            </thead>
        </table>
    </fieldset>
    <?
    exit();
}

if ($action=="acount_head_details_v2")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$sub_id=str_replace("'","",$sub_id);
	//echo "select id, account_head FROM com_export_proceed_rlzn_dtls WHERE type = '1' group by id,account_head";die;
	$acount_head=return_library_array("select  account_head FROM com_export_proceed_rlzn_dtls WHERE type = '1' group by account_head","account_head","account_head");
	//var_dump($acount_head);die;
	$i=1;
	$loop=count($acount_head);
	if($loop>0)
	{
		$sql="SELECT ";
		foreach($acount_head as $key=>$val)
		{
			$sql.="sum(CASE WHEN account_head =$val  THEN document_currency else 0 END) AS sub_val_$val ";
			if($i<$loop) $sql .=",";
			$i++;
		}
        //echo $sql;die;

		$sql .= "FROM com_export_proceed_rlzn_dtls a, com_export_proceed_realization b
				WHERE a.mst_id = b.id AND a.type = '1' AND b.is_invoice_bill = 1 AND b.invoice_bill_id  in($sub_id) and a.account_head not in(5,6,10,15,61,62,65,81,11,87) group by b.invoice_bill_id";
	}

	//echo $sql;die;
	$sql_re=sql_select($sql);
	?>
    <fieldset style="width:590px">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center" width="100%" style="overflow-x:auto;">
            <thead>
            <?
			//var_dump($sql_re);
			$j=1;$k=1;
			foreach($sql_re as $key=>$row)
			{
				if($k==1)
				{
					?>
					<tr>
					<?
					foreach($acount_head as $ac_key=>$ac_val)
					{
						if($row[csf("sub_val_$ac_val")]!=0)
						{
							?>
							<td align="center"><? echo  $commercial_head[$ac_val] ; ?></td>
							<?
						}
						$j++;
					}
					?>
					</tr>
					<?
				}
				?>
            	<tr>
                <?
				foreach($acount_head as $ac_key=>$ac_val)
                {
					if($row[csf("sub_val_$ac_val")]!=0)
					{
						?>
						<td align="center" ><? echo   number_format($row[csf("sub_val_$ac_val")],2); ?></td>
						<?
					}
				}
				?>
                </tr>
                <?
				$k++;
			}
			?>
            </thead>
        </table>
    </fieldset>
    <?
    exit();
}

if ($action=="load_drop_down_search")
{
	$data=explode('_',$data);
	if($data[1]==1) echo '<input type="text" name="txt_search_common" id="txt_search_common" class="text_boxes" style="width:160px" autocomplete=off />';
	if($data[1]==2) echo create_drop_down( "txt_search_common", 170, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90))  $buyer_cond order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" ,0);
	if($data[1]==3) echo create_drop_down( "txt_search_common", 170, "select bank_name,id from lib_bank where is_deleted=0  and status_active=1 and lien_bank=1 order by bank_name","id,bank_name", 1, "-- All Lein Bank --", $selected, "",0,"" );

    if($data[1]==4) echo '<input type="text" name="txt_search_common" id="txt_search_common" class="text_boxes" style="width:160px" autocomplete=off />';
	exit();
}

if ($action=="file_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	//echo $lien_bank;die;
    ?>
	<script>
		function js_set_value(str)
		{
			$("#hide_file_no").val(str);
			parent.emailwindow.hide();
		}
		function set_caption(id)
		{
		if(id==1)  document.getElementById('search_by_td_up').innerHTML='Enter File No';
		if(id==2)  document.getElementById('search_by_td_up').innerHTML='Enter Buyer Name';
		if(id==3)  document.getElementById('search_by_td_up').innerHTML='Enter Lein Bank';
	    if(id==4)  document.getElementById('search_by_td_up').innerHTML='Enter SC/LC';
		}
	</script>
	</head>
	<body>
	    <div style="width:530px">
	    <form name="search_order_frm"  id="search_order_frm">
	    <fieldset style="width:530px">
	        <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center" width="100%">
	            <thead>
	            	<th>Year</th>
	                <th>Search By</th>
	                <th id="search_by_td_up">Enter File No</td>
	                <th>
    	                <input type="hidden" name="txt_company_id" id="txt_company_id" value="<?  echo $company_id; ?>"/>
    	                <input type="hidden" name="txt_buyer_id" id="txt_buyer_id" value="<?  echo $buyer_id; ?>"/>
    	                <input type="hidden" name="txt_lien_bank_id" id="txt_lien_bank_id" value="<?  echo $lien_bank; ?>"/>
    	                <input type="hidden" name="txt_sclc_id" id="txt_sclc_id" value="<? //echo ?>"/>
    	                <input type="hidden" name="txt_selected_file" id="txt_selected_file" value=""/>
	                </th>
	            </thead>
	            <tbody>

	                <tr class="general">
	                	<td>
	                    <?
						$sql=sql_select("select lc_year as lc_sc_year from com_export_lc where beneficiary_name='$company_id' and status_active=1 and is_deleted=0  union all select sc_year as lc_sc_year from com_sales_contract where beneficiary_name='$company_id' and status_active=1 and is_deleted=0");
						foreach($sql as $row)
						{
							$lc_sc_year[$row[csf("lc_sc_year")]]=$row[csf("lc_sc_year")];
						}
						echo create_drop_down( "cbo_year", 100,$lc_sc_year,"", 1, "-- Select --",$cbo_year);
						?>
	                    </td>
	                    <td>
	                    <?
						$sarch_by_arr=array(1=>"File No",2=>"Buyer",3=>"Lien Bank",4=>"SC/LC");
						echo create_drop_down( "cbo_search_by", 130,$sarch_by_arr,"", 0, "-- Select Search --", 1,"load_drop_down( 'file_wise_export_status_controller',document.getElementById('txt_company_id').value+'_'+this.value, 'load_drop_down_search', 'search_by_td' );set_caption(this.value)");
						?>
	                    </td>
	                    <td align="center" id="search_by_td">
	                    <input type="text" name="txt_search_common" id="txt_search_common" class="text_boxes" style="width:160px" autocomplete=off />
	                    </td>
	                    <td>
	                    <input type="button" name="show" id="show" onClick="show_list_view(document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+<?  echo $company_id; ?>+'_'+<?  echo $buyer_id; ?>+'_'+<?  echo $lien_bank;?>+'_'+document.getElementById('cbo_year').value,'search_file_info','search_div_file','file_wise_export_status_controller','setFilterGrid(\'list_view\',-1)')" class="formbutton" style="width:100px;" value="Show" />
	                    </td>
	                </tr>
	            </tbody>
	        </table>
	        <table width="100%">
	            <tr>
	                <td>
	                <div style="width:560px; margin-top:5px" id="search_div_file" align="left"></div>
	                </td>
	            </tr>
	        </table>
	    </fieldset>
	    </form>
	    </div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
    <?
	exit();
}

if($action=="yet_to_accept_pop_up")
{
    extract($_REQUEST);    
    echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
    ?>
      
    </head>
    <body>
    <?

	$hidden_btb_id=str_replace("'","",$hidden_btb_id);
    $file_buyer=explode("*",str_replace("'","",$file_buyer));
    $file_no=$file_buyer[0];
    $buyer_name=$buyer_name_arr[$file_buyer[1]];
    //echo $hidden_btb_id;die;

    if($db_type==0) // for mysql
    {
        $sql_btb_opened= "select a.id, a.lc_number, a.lc_date, a.lc_value, a.supplier_id, a.item_category_id, sum(b.quantity) as btb_lc_opened
        from com_btb_lc_master_details a, com_btb_lc_pi c, com_pi_item_details b
        where a.id=c.com_btb_lc_master_details_id and c.pi_id = b.pi_id and a.id in($hidden_btb_id) and a.is_deleted=0 and a.status_active=1 
        group by a.id, a.lc_number, a.lc_date, a.lc_value, a.item_category_id, a.supplier_id"; 
    }
    else if($db_type==2) // for oracle
    {
        $sql_btb_opened= "select a.id,a.lc_number,a.lc_date,a.lc_value,a.supplier_id,a.item_category_id, sum (b.quantity) as btb_lc_opened, 0 as cumulative_acceptance
        from com_btb_lc_master_details a, com_btb_lc_pi c, com_pi_item_details b
        where a.id=c.com_btb_lc_master_details_id and c.pi_id = b.pi_id and a.id in ($hidden_btb_id) and a.is_deleted = 0 and a.status_active = 1
        group by a.id,a.lc_number,a.lc_date,a.lc_value,a.item_category_id,a.supplier_id
        union all
        select a.id, a.lc_number, a.lc_date, a.lc_value, a.supplier_id, a.item_category_id, 0 as btb_lc_opened, sum (b.current_acceptance_value) as cumulative_acceptance
        from com_btb_lc_master_details a, com_import_invoice_dtls b
        where a.id = b.btb_lc_id and a.id in ($hidden_btb_id) and a.is_deleted = 0 and a.status_active = 1 and b.status_active = 1
        group by a.id, a.lc_number, a.lc_date, a.lc_value, a.item_category_id, a.supplier_id";
    }
    //echo $sql_btb_opened;die;

    $sql_btb_result=sql_select($sql_btb_opened);
    foreach ($sql_btb_result as $row) {
        $btb_opened_data_array[$row[csf('id')]]['lc_id'] = $row[csf('id')];
        $btb_opened_data_array[$row[csf('id')]]['lc_number'] = $row[csf('lc_number')];
        $btb_opened_data_array[$row[csf('id')]]['lc_date'] = $row[csf('lc_date')];
        $btb_opened_data_array[$row[csf('id')]]['lc_value'] = $row[csf('lc_value')];
        $btb_opened_data_array[$row[csf('id')]]['supplier_id'] = $row[csf('supplier_id')];
        $btb_opened_data_array[$row[csf('id')]]['item_category_id'] = $row[csf('item_category_id')];
        $btb_opened_data_array[$row[csf('id')]]['btb_lc_opened'] += $row[csf('btb_lc_opened')];
        $btb_opened_data_array[$row[csf('id')]]['cumulative_acceptance'] += $row[csf('cumulative_acceptance')];
    }
    //print_r($btb_opened_data_array);//die;
    ?>        
    <div>
        <div style="width:760px; margin-top: 20px;" id="report_div">
            <table cellspacing="0" width="740" border="1" rules="all" class="rpt_table" >
                <thead>
                    <tr>
                        <th width="50">SL</td>
                        <th width="130">LC No</td>
                        <th width="100">LC Value</td>
                        <th width="100">Item Cetagory</td>
                        <th width="130">Supplier</td>
                        <th width="100">Cumulative Acceptance</td>
                        <th width="130">Balance</td>
                        <!-- <th width="100">PI Qty</td> -->
                    </tr>
                </thead>
            </table>
            <div style="width:760px; max-height:320px; overflow-y:scroll;" id="scroll_body" >
                <table width="740" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all" >
                    <tbody id="table_id">
                    <?
                    $i=1;
                    //print_r($item_category);
                    foreach($btb_opened_data_array as $row)
                    {
                        //echo $row[csf("item_category_id")];die;//1,4
                        $catagory = $row["item_category_id"];
                        $catArr = explode(',', $catagory);                        
                        $arrUni = array_unique($catArr);

                        if ($i%2==0) $bgcolor="#E9F3FF";
                        else $bgcolor="#FFFFFF";
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i;?>">
                            <td width="50" align="center"><?  echo $i; ?></td>
                            <td width="130" align="center"><p><?  echo $row["lc_number"]; ?>&nbsp;</p></td>
                            <td width="100" align="center">
                                <p>
                                    <? echo number_format($row["lc_value"],2);
                                    $total_lc_val+=$row["lc_value"]; ?>
                                </p>
                            </td>
                            <td width="100" align="center"><p>
                                <?
                                    $catName = "";
                                    foreach ($arrUni as $key => $value) {
                                        if ($catName == "") {
                                           echo $catName = $item_category[$value];
                                        }
                                        else
                                        {
                                            echo $catName = ", ".$item_category[$value];
                                        }
                                        //echo $item_category[$value];
                                    }
                                ?></p></td>
                            <td width="130" align="center"><p><?  echo $suplier_name_arr[$row["supplier_id"]]; ?></p></td>
                            <td  width="100" align="right"><p><?  echo number_format($row["cumulative_acceptance"],2);  $total_cumulative_val+=$row["cumulative_acceptance"];?></p></td>
                            
                            <? 
                                $balance = ($row["lc_value"]*1)-($row["cumulative_acceptance"]*1);
                                $total_balance +=$balance;
                            ?>
                            <td align="center" width="130"><?  echo number_format($balance,2); ?></td>
                        </tr>
                        <?
                        $i++;
                    }
                    ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="5" >Total</td>
                            <th><? echo number_format($total_cumulative_val,2); ?> </th>
                            <th ><? echo number_format($total_balance,2); ?> </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    <script>event.preventDefault();</script>
    </body>
    </html>
    <?
    exit();
}

if ($action=="search_file_info")
{
	$ex_data = explode("_",$data);
	//print_r($ex_data);die;
	$cbo_search_by = $ex_data[0];
	$txt_search_common = $ex_data[1];
	$company_id = $ex_data[2];
	$buyer_id = $ex_data[3];
	$lien_bank_id = $ex_data[4];
	$cbo_year = $ex_data[5];
	//echo $cbo_year; die;
	if($buyer_id!=0) $buy_query="and buyer_name='$buyer_id'"; else  $buy_query="";
	if($lien_bank_id!=0) $lien_bank_id="and lien_bank='$lien_bank_id'"; else  $lien_bank_id="";
	if($cbo_year!=0)
	{
		$year_cond_sc="and sc_year='$cbo_year'";
		$year_cond_lc="and lc_year='$cbo_year'";
	}
	else
	{
		$year_cond_sc="";
		$year_cond_lc="";
	}
	//$year_cond_sc="and sc_year='".date("Y")."'";
	//$year_cond_lc="and lc_year='".date("Y")."'";
	//echo $lien_bank_id;die;

	//if($txt_search_common==0)$txt_search_common="";

    $txt_search_common = trim($txt_search_common);
    $search_cond ="";$search_cond_lc="";$search_cond_sc="";
    if($txt_search_common!="")
    {
        if($cbo_search_by==1)
        {
            $search_cond .= " and internal_file_no like '%$txt_search_common%'";
        }
        else if($cbo_search_by==2)
        {
            $search_cond .= " and buyer_name='$txt_search_common'";
        }
        else if($cbo_search_by==3)
        {
            $search_cond .= " and lien_bank='$txt_search_common'";
        }
        else if($cbo_search_by==4)
        {
            $search_cond_lc .= " and export_lc_no='$txt_search_common'";
            $search_cond_sc .= " and contract_no='$txt_search_common'";
        }
    }
    //echo $cbo_search_by."**".$txt_search_common; die;
    //echo $cbo_search_by."**".$search_cond_lc."**".$search_cond_sc; die;
    if($db_type == 0)
    {
        $sql = "select a.id,a.beneficiary_name, a.internal_file_no, a.lien_bank, a.buyer_name ,  a.lc_sc_year , group_concat(a.export_lc_no) as export_lc_no
        from (
             select id,beneficiary_name, internal_file_no, lien_bank, buyer_name , lc_year as lc_sc_year, group_concat(export_lc_no) as export_lc_no, 'export' as type
              from com_export_lc
             where beneficiary_name='$company_id' and status_active=1 and is_deleted=0 $buy_query $lien_bank_id $year_cond_lc $search_cond $search_cond_lc
             group by id,internal_file_no, lc_year, beneficiary_name, buyer_name , lien_bank
             union all
             select id,beneficiary_name, internal_file_no, lien_bank, buyer_name, sc_year as lc_sc_year,group_concat(contract_no) as export_lc_no, 'import' as type
             from com_sales_contract
             where beneficiary_name='$company_id'
              and status_active=1 and is_deleted=0 $buy_query $lien_bank_id $year_cond_sc $search_cond $search_cond_sc
             group by id,internal_file_no, sc_year, beneficiary_name, buyer_name , lien_bank
         ) a
          group by a.id,a.beneficiary_name, a.internal_file_no, a.lien_bank, a.buyer_name , a.lc_sc_year";
    }
    else
    {
        $sql = "select a.id,a.beneficiary_name, a.internal_file_no, a.lien_bank, a.buyer_name ,  a.lc_sc_year , listagg(cast(a.export_lc_no as varchar(4000)),',') within group(order by a.export_lc_no) as export_lc_no
        from (
             select id,beneficiary_name, internal_file_no, lien_bank, buyer_name , lc_year as lc_sc_year, listagg(cast(export_lc_no as varchar(4000)),',') within group(order by export_lc_no) as export_lc_no, 'export' as type
              from com_export_lc
             where beneficiary_name='$company_id' and status_active=1 and is_deleted=0 $buy_query $lien_bank_id $year_cond_lc $search_cond $search_cond_lc
             group by id,internal_file_no, lc_year, beneficiary_name, buyer_name , lien_bank
             union all
             select id,beneficiary_name, internal_file_no, lien_bank, buyer_name, sc_year as lc_sc_year,listagg(cast(contract_no as varchar(4000)),',') within group(order by contract_no) as export_lc_no, 'import' as type
             from com_sales_contract
             where beneficiary_name='$company_id'
              and status_active=1 and is_deleted=0 $buy_query $lien_bank_id $year_cond_sc $search_cond $search_cond_sc
             group by id,internal_file_no, sc_year, beneficiary_name, buyer_name , lien_bank
         ) a
          group by a.id,a.beneficiary_name, a.internal_file_no, a.lien_bank, a.buyer_name , a.lc_sc_year";
		  //echo $sql;
    }	
    /*
	if($txt_search_common!="" && $cbo_search_by==1)
	{

		$sql="select beneficiary_name, internal_file_no, lien_bank, buyer_name, lc_year as lc_sc_year,export_lc_no from com_export_lc where beneficiary_name='$company_id' and internal_file_no like '%$txt_search_common%' and status_active=1 and is_deleted=0 $buy_query $lien_bank_id $year_cond_lc  group by internal_file_no, lc_year, beneficiary_name, buyer_name , lien_bank,export_lc_no
		union all
		select beneficiary_name, internal_file_no, lien_bank, buyer_name, sc_year as lc_sc_year,contract_no as  export_lc_no from com_sales_contract where internal_file_no not in(select internal_file_no from com_export_lc where beneficiary_name='$company_id' and status_active=1 and is_deleted=0 and internal_file_no like '%$txt_search_common%' $year_cond_lc) and beneficiary_name='$company_id' and status_active=1 and is_deleted=0 and internal_file_no like '%$txt_search_common%' $buy_query $lien_bank_id $year_cond_sc group by internal_file_no, sc_year, beneficiary_name, buyer_name , lien_bank,contract_no";
	}
	else if($txt_search_common!="" && $cbo_search_by==2)
	{

		$sql="select beneficiary_name, internal_file_no, lien_bank, buyer_name, lc_year as lc_sc_year,export_lc_no from com_export_lc where beneficiary_name='$company_id' and buyer_name='$txt_search_common' and status_active=1 and is_deleted=0 $lien_bank_id  $year_cond_lc  group by internal_file_no, lc_year, beneficiary_name, buyer_name , lien_bank,export_lc_no
		union all
		select beneficiary_name, internal_file_no, lien_bank, buyer_name, sc_year as lc_sc_year,contract_no as export_lc_no from com_sales_contract where internal_file_no not in(select internal_file_no from com_export_lc where beneficiary_name='$company_id' and status_active=1 and is_deleted=0 and buyer_name='$txt_search_common' $year_cond_lc) and beneficiary_name='$company_id' and status_active=1 and is_deleted=0 and buyer_name='$txt_search_common' $lien_bank_id $year_cond_sc group by internal_file_no, sc_year, beneficiary_name, buyer_name , lien_bank,contract_no";
	}
	else if($txt_search_common!="" && $cbo_search_by==3)
	{
		//echo $txt_search_common; die;

		$sql="select beneficiary_name, internal_file_no, lien_bank, buyer_name, lc_year as lc_sc_year,export_lc_no from com_export_lc where beneficiary_name='$company_id' and lien_bank='$txt_search_common' and status_active=1 and is_deleted=0 $buy_query  $year_cond_lc  group by internal_file_no, lc_year, beneficiary_name, buyer_name , lien_bank,export_lc_no
		union all
		select beneficiary_name, internal_file_no, lien_bank, buyer_name, sc_year as lc_sc_year,contract_no as export_lc_no from com_sales_contract where internal_file_no not in(select internal_file_no from com_export_lc where beneficiary_name='$company_id' and status_active=1 and is_deleted=0 and lien_bank='$txt_search_common'  $year_cond_lc) and beneficiary_name='$company_id' and status_active=1 and is_deleted=0 and lien_bank='$txt_search_common' $buy_query $year_cond_sc  group by internal_file_no, sc_year, beneficiary_name, buyer_name , lien_bank,contract_no";
	}

    else if($txt_search_common!="" && $cbo_search_by==4)
    {


        $sql="select beneficiary_name, internal_file_no, lien_bank, buyer_name, lc_year as lc_sc_year,export_lc_no from com_export_lc where beneficiary_name='$company_id' and export_lc_no='$txt_search_common' and status_active=1 and is_deleted=0 $buy_query  $year_cond_lc  group by internal_file_no, lc_year, beneficiary_name, buyer_name , lien_bank,export_lc_no
        union all
        select beneficiary_name, internal_file_no, lien_bank, buyer_name, sc_year as lc_sc_year, contract_no as  export_lc_no from com_sales_contract where internal_file_no not in(select internal_file_no from com_export_lc where beneficiary_name='$company_id' and status_active=1 and is_deleted=0 and export_lc_no='$txt_search_common'  $year_cond_lc) and beneficiary_name='$company_id' and status_active=1 and is_deleted=0 and contract_no='$txt_search_common' $buy_query $year_cond_sc  group by internal_file_no, sc_year, beneficiary_name, buyer_name , lien_bank,contract_no";
    }

	else
	{
		$sql="select beneficiary_name, internal_file_no, lien_bank, buyer_name , lc_year as lc_sc_year,export_lc_no from com_export_lc where beneficiary_name='$company_id' and status_active=1 and is_deleted=0 $buy_query $lien_bank_id  $year_cond_lc group by internal_file_no, lc_year, beneficiary_name, buyer_name , lien_bank,export_lc_no
		union all
		select beneficiary_name, internal_file_no, lien_bank, buyer_name, sc_year as lc_sc_year,contract_no as export_lc_no from com_sales_contract where internal_file_no not in(select internal_file_no from com_export_lc where beneficiary_name='$company_id' and status_active=1 and is_deleted=0  $year_cond_lc) and beneficiary_name='$company_id' and status_active=1 and is_deleted=0 $buy_query $lien_bank_id $year_cond_sc group by internal_file_no, sc_year, beneficiary_name, buyer_name , lien_bank,contract_no";
	}
    */
	//echo $sql;
	?>
    <div style="width:560px">
    <form name="display_file"  id="display_file">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center" width="100%">
            <thead>
                <th width="60">Sl NO.</td>
                <th width="80">File NO</td>
                <th width="80">Year</td>
                <th width="130"> Buyer</td>
                <th width="100"> Lien Bank</td>
                <th >SC/LC No.</td>

            </thead>
            </table>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center" width="100%" id="list_view">
            <tbody>
            <?
			$sll_result=sql_select($sql);
			$i=1;
			foreach($sll_result as $row)
			{
				if ($i%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				//echo $row[csf("internal_file_no")];die;
			    ?>
                <tr bgcolor="<? echo $bgcolor; ?>"  onclick="js_set_value('<? echo $row[csf("internal_file_no")];?>,<? echo $row[csf("lc_sc_year")];?>,<? echo $row[csf("buyer_name")];?>,<? echo $row[csf("lien_bank")];?>,<? echo $row[csf("id")];?>')" id="search<? echo $row[csf("id")]; ?>" style="cursor:pointer">
                    <td align="center" width="60"> <? echo $i;?></td>
                    <td align="center" width="80"><? echo $row[csf("internal_file_no")];  ?></td>
                    <td align="center" width="80"><? echo $row[csf("lc_sc_year")];  ?></td>
                    <td width="130"><? echo $buyer_name_arr[$row[csf("buyer_name")]];  ?></td>
                    <td width="100"><? echo $lein_bank_arr[$row[csf("lien_bank")]];  ?></td>
                    <td><p><? echo $row[csf("export_lc_no")];  ?></p></td>
                </tr>
                <?
				$i++;
			}
			?>
            </tbody>
            <input type="hidden" id="hide_file_no" name="hide_file_no"  />
        </table>
    </form>
    <script>setFilterGrid('list_view',-1)</script>
    </div>
    <?
}

if ($action=="btb_open_old")
{
	ob_start();
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	//echo load_html_head_contents("Subprocess Details", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	$hidden_btb_id=str_replace("'","",$hidden_btb_id);
	$file_buyer=explode("*",str_replace("'","",$file_buyer));
	$file_no=$file_buyer[0];
	$buyer_name=$buyer_name_arr[$file_buyer[1]];
	//echo $hidden_btb_id;die;
	$sql= "select a.id, a.lc_number, a.lc_date, a.lc_value, a.pi_id, a.supplier_id, a.item_category_id, sum(c.quantity) as quantity from com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_item_details c where a.id=b.com_btb_lc_master_details_id and b.pi_id=c.pi_id and a.id in($hidden_btb_id) and a.is_deleted=0 and a.status_active=1 group by a.id, a.lc_number, a.lc_date, a.lc_value, a.pi_id, a.supplier_id, a.item_category_id "; $sql_result=sql_select($sql);
	?>
	<script>
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><title>BTB Open</title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css"/></head><body><div style="width:820px; margin-top:20px;"><? echo "<b>File No: " .$file_no."&nbsp;&nbsp;&nbsp;&nbsp; Buyer Name: ".$buyer_name."</b><br>&nbsp;<br>"; ?></div>'+document.getElementById('popup_body').innerHTML+'</body</html>');
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="320px";
	}
    </script>
    <div id="report_container">
	    <table width="800" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
	    	<tr>
	            <td align="center">
	                <input type="button" class="formbutton" onClick="new_window()" style="width:100px;" value="Print" >
	                <input type="button" id="excel_preveiw" class="formbutton" style="width:100px;" value="Excel Preveiw" >
	           </td>
	        </tr>
	    </table><br>
	    <div id="popup_body" style="width:820px;">
		<table width="800" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
	        <thead>
	        	<tr>
	            	<th width="50">SL</td>
	                <th width="130">BTB Lc No</td>
	                <th width="80">Lc Date</td>
	                <th width="100">Amount</td>
	                <th width="130">PI No.</td>
	                <th width="130">Supplier</td>
	                <th width="80">Item Cetagory</td>
	                <th >PI Qty</td>
	            </tr>
	        </thead>
	    </table>
	    <div style="width:820px; max-height:320px; overflow-y:scroll" id="scroll_body">
		<table width="800" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all" id= "tbody_id">
	        <tbody>
	        <?

			$i=1;
			foreach($sql_result as $row)
			{
			    if ($i%2==0) $bgcolor="#E9F3FF";
			    else $bgcolor="#FFFFFF";
			    ?>
	        	<tr bgcolor="<? echo $bgcolor; ?>">
	            	<td width="50"><?  echo $i; ?></td>
	                <td width="130"><?  echo $row[csf("lc_number")]; ?></td>
	                <td width="80" align="center"><? if($row[csf("lc_date")]!='0000-00-00')  echo change_date_format($row[csf("lc_date")]); else echo ""; ?></td>
	                <td  width="100" align="right"><?  echo number_format($row[csf("lc_value")],2);  $total_val+=$row[csf("lc_value")];?></td>
	                <td width="130">
					<p><?
					  $po_id=explode(",",$row[csf("pi_id")]);
					  $k=1;
					  foreach($po_id as $row_po_id)
					  {
						  if($k!=1) echo ", ";
						  echo  $pi_no_arr[$row_po_id];
						$k++;
					  }
					?></p>
	                </td>
	                <td width="130"><?  echo $suplier_name_arr[$row[csf("supplier_id")]]; ?></td>
	                <td width="80"><?  echo $item_category[$row[csf("item_category_id")]]; ?></td>
	                <td align="center"><?  echo $row[csf("quantity")]; ?></td>
	            </tr>
	            <?
			    $i++;
			}
			?>
	        </tbody>
	        <tfoot>
	        	<tr>
	            	<th >&nbsp;</td>
	                <th >&nbsp;</td>
	                <th >Total</td>
	                <td><? echo number_format($total_val,2); ?> </td>
	                <th >&nbsp;</td>
	                <th >&nbsp;</td>
	                <th >&nbsp;</td>
	                <th >&nbsp;</td>
	            </tr>
	        </tfoot>
		</table>
	    </div>
	    </div>
	</div>
	<?
    $name=time();
    $filename=$user_name."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc,ob_get_contents());
    $filename= $user_name."_".$name.".xls";
    ?>
    <script type="text/javascript">
        setFilterGrid('tbody_id',-1);
        $("#excel_preveiw").click(function(e) {
        //window.open("<? echo $filename ; ?>", + $('#report_container').html());
        window.open("<? echo $filename ; ?>");
        e.preventDefault();
        });
    </script>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    <?
    exit();
}

if ($action=="btb_accep")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	$test = extract($_REQUEST);
	$hidden_btb_id=str_replace("'","",$hidden_btb_id);
    $btb = explode(',', $hidden_btb_id);
    //print_r($btb);die;
    $btb_id = "";
    foreach ($btb as $key => $btb_value)
    {
        //echo $btb_value."<br>";
        if ($btb_id=="") {
            $btb_id .= "'".$btb_value."'";
        }
        else{
            $btb_id .= ","."'".$btb_value."'";
        }
    }
    //echo $btb_id;
    //echo $hidden_btb_id;die;
	if($db_type==0)
	{
	   $sql= "select a.id, a.invoice_no, b.current_acceptance_value as inv_amount, a.maturity_date, a.invoice_date,c.lc_number, c.supplier_id, c.currency_id, c.lc_date, c.importer_id, e.item_category_id
        from com_import_invoice_mst a, com_import_invoice_dtls b, com_btb_lc_master_details c, com_btb_lc_pi d, com_pi_item_details e
        where a.id=b.import_invoice_id and c.id=d.com_btb_lc_master_details_id and d.pi_id=e.pi_id and a.btb_lc_id=c.id and a.btb_lc_id in($btb_id)and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
        group by a.id,a.invoice_no,a.maturity_date,a.invoice_date,c.supplier_id, c.currency_id, c.lc_date, c.importer_id, e.item_category_id,b.current_acceptance_value,c.lc_number";
        
	}
	else if($db_type==2)
	{
	    $sql= "select a.id, a.invoice_no, b.current_acceptance_value as inv_amount, a.maturity_date, a.invoice_date, c.lc_number, c.supplier_id, c.currency_id, c.lc_date, c.importer_id, e.item_category_id
        from com_import_invoice_mst a, com_import_invoice_dtls b, com_btb_lc_master_details c, com_btb_lc_pi d, com_pi_item_details e
        where a.id=b.import_invoice_id and c.id=d.com_btb_lc_master_details_id and d.pi_id=e.pi_id and a.btb_lc_id=c.id and a.btb_lc_id in($btb_id)and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
        group by a.id,a.invoice_no,a.maturity_date,a.invoice_date,c.supplier_id, c.currency_id, c.lc_date, c.importer_id, e.item_category_id,b.current_acceptance_value,c.lc_number";
	}
	//echo $sql;//die;
	?>
    <script>
        function print_window()
        {
            document.getElementById('scroll_body').style.overflow="auto";
            document.getElementById('scroll_body').style.maxHeight="none";
            $(".flt").css("display","none");
            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
            '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title>Total BTB Accepted</title></head><body><div style="width:800px; margin-top:20px;"><? echo ""; ?></div>'+document.getElementById('report_div').innerHTML+'</body</html>');
            d.close();
            document.getElementById('scroll_body').style.overflowY="scroll";
            document.getElementById('scroll_body').style.maxHeight="320px";
            $(".flt").css("display","block");
        }
    </script>
    <div style="width:800px;" align="center">
        <input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp;
        <div id="report_container"> </div>
    </div>
    <?
    ob_start();
    ?>
    <div style="width:800px;" id="report_div">
    	<table width="780" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="left" rules="all">
            <thead>
            	<tr>
                	<th width="50">SL</td>
                    <th width="110">Invoice No</td>
                    <th width="110">Amount</td>
                    <th width="80">Maturity date</td>
                    <th width="80">Invoice date</td>
                    <th width="110">Lc No</td>
                    <th width="110">Item Cetagory</td>
                    <th >Supplier</td>
                </tr>
            </thead>
        </table>
        <div style="width:800px; max-height:320px; overflow-y:scroll" id="scroll_body" >
            <table width="780" cellspacing="0" cellpadding="0" id="table_id5" border="1" class="rpt_table" rules="all">
                <tbody >
                <?
        		$sql_result=sql_select($sql);
        		$i=1;
        		foreach($sql_result as $row)
        		{
        		    if ($i%2==0) $bgcolor="#E9F3FF";
        		    else $bgcolor="#FFFFFF";
                    if($row[csf("currency_id")]==1)
                    {
                        $conversion_rate=set_conversion_rate(2, $row[csf("lc_date")],$row[csf("importer_id")]);
                        $inv_amount=$row[csf("inv_amount")]/$conversion_rate;
                    }
                    else $inv_amount=$row[csf("inv_amount")];
        		    ?>
                	<tr bgcolor="<? echo $bgcolor; ?>">
                    	<td  width="50" ><?  echo $i; ?></td>
                        <td width="110"><p style="word-break: break-all;"><? echo $row[csf("invoice_no")]; ?></p></td>
                        <td align="right" width="110"><?  echo number_format($inv_amount,2); $total_amount+=$inv_amount; ?></td>
                        <td  align="center" width="80.">
        				<?
        				if ($row[csf("maturity_date")]!='0000-00-00') echo change_date_format($row[csf("maturity_date")]); else echo "";
        				?>
                        </td>
                        <td  align="center" width="80">
        				<?
        				if ($row[csf("invoice_date")]!='0000-00-00') echo change_date_format($row[csf("invoice_date")]); else echo "";
        				?>
                        </td>
                        <td width="110"><p style="word-break: break-all;mso-number-format:'\@';"><?  echo $row[csf("lc_number")]; ?></p></td>
                        <td width="110"><?  echo $item_category[$row[csf("item_category_id")]]; ?></td>
                        <td ><?  echo $suplier_name_arr[$row[csf("supplier_id")]]; ?></td>
                    </tr>
                    <?
    		        $i++;
    		    }
    		    ?>
                </tbody>
                
    	    </table>
        </div>
        <table width="780" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all" >
            <tfoot>
                <tr>
                    <th width="50">&nbsp;</th>
                    <th width="110">Total</th>
                    <th width="110" id="value_total_amnt"><? echo number_format($total_amount,2); ?></th>
                    <th width="80">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="110">&nbsp;</th>
                    <th width="110">&nbsp;</th>
                    <th >&nbsp;</th>
                </tr>
            </tfoot>
        </table>
        <script type="text/javascript">
                var tableFilters2 = 
                {
                    col_operation: {
                    id: ["value_total_amnt"],
                    col: [2],
                    operation: ["sum"],
                    write_method: ["innerHTML"]
                    }
                }
            setFilterGrid("table_id5",-1,tableFilters2);
            </script>
            <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </div>
    <?
    $html=ob_get_contents();
    ob_flush();

    foreach (glob("$user_id*.xls") as $filename)
    {
       @unlink($filename);
    }

    //html to xls convert
    $name=time();
    $name=$user_id."_".$name.".xls";
    $create_new_excel = fopen(''.$name, 'w');
    $is_created = fwrite($create_new_excel,$html);
    ?>
    <input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
    <script>
        setFilterGrid('table_id',-1);
        $(document).ready(function(e) {
            document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
        });
    </script>
    <?
	exit();
}


if ($action=="btb_accep_category")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	$test = extract($_REQUEST);
	$hidden_btb_id=str_replace("'","",$hidden_btb_id);
    $btb = explode(',', $hidden_btb_id);
	$item_cat=str_replace("'","",$item_cat);
	$buyer=str_replace("'","",$buyer);
	$cat_wise_budge_val=str_replace("'","",$cat_wise_budge_val);
	$file_buyer=explode("*",str_replace("'","",$file_buyer));
    $file_no=$file_buyer[0];
    $buyer_name=$buyer_name_arr[$file_buyer[1]];
	
    //print_r($btb);die;
    $btb_id = "";
    foreach ($btb as $key => $btb_value)
    {
        //echo $btb_value."<br>";
        if ($btb_id=="") {
            $btb_id .= "'".$btb_value."'";
        }
        else{
            $btb_id .= ","."'".$btb_value."'";
        }
    }
    //echo $btb_id;
	 $sql= "select a.id, a.invoice_no, b.current_acceptance_value as inv_amount, a.maturity_date, a.invoice_date,c.lc_number, c.supplier_id, e.item_category_id
	from com_import_invoice_mst a, com_import_invoice_dtls b, com_btb_lc_master_details c, com_btb_lc_pi d, com_pi_item_details e
	where a.id=b.import_invoice_id and c.id=d.com_btb_lc_master_details_id and d.pi_id=e.pi_id and a.btb_lc_id=c.id and a.btb_lc_id in($btb_id)and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
	group by a.id,a.invoice_no,a.maturity_date,a.invoice_date,c.supplier_id, e.item_category_id,b.current_acceptance_value,c.lc_number";
    //echo $hidden_btb_id;die;
	$sql= "select a.id as inv_id, a.invoice_no, a.maturity_date, a.invoice_date, c.lc_number, c.supplier_id, b.id as accp_dtls_id, b.current_acceptance_value as inv_amount, e.id as pi_dtls_id, e.item_category_id, e.net_pi_amount
	from com_import_invoice_mst a, com_import_invoice_dtls b, com_btb_lc_master_details c, com_btb_lc_pi d, com_pi_item_details e
	where a.id=b.import_invoice_id and b.btb_lc_id=c.id and c.id=d.com_btb_lc_master_details_id and d.pi_id=e.pi_id and d.pi_id=b.pi_id and b.pi_id=e.pi_id and a.btb_lc_id in($btb_id)and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and e.item_category_id in($item_cat)";
	//echo $sql;//die;
	$sql_result=sql_select($sql);
	$dtls_data=array();
	foreach($sql_result as $row)
	{
		$dtls_data[$row[csf("inv_id")]]["inv_id"]=$row[csf("inv_id")];
		$dtls_data[$row[csf("inv_id")]]["invoice_no"]=$row[csf("invoice_no")];
		$dtls_data[$row[csf("inv_id")]]["maturity_date"]=$row[csf("maturity_date")];
		$dtls_data[$row[csf("inv_id")]]["invoice_date"]=$row[csf("invoice_date")];
		$dtls_data[$row[csf("inv_id")]]["lc_number"]=$row[csf("lc_number")];
		$dtls_data[$row[csf("inv_id")]]["supplier_id"]=$row[csf("supplier_id")];
		$dtls_data[$row[csf("inv_id")]]["item_category_id"]=$row[csf("item_category_id")];
		
		if($inv_dtls_check[$row[csf("accp_dtls_id")]]=="")
		{
			$inv_dtls_check[$row[csf("accp_dtls_id")]]=$row[csf("accp_dtls_id")];
			$dtls_data[$row[csf("inv_id")]]["inv_amount"]+=$row[csf("inv_amount")];
			$tot_accep_amount+=$row[csf("inv_amount")];
		}
		
		if($pi_dtls_check[$row[csf("pi_dtls_id")]]=="")
		{
			$pi_dtls_check[$row[csf("pi_dtls_id")]]=$row[csf("pi_dtls_id")];
			$tot_btb_amount+=$row[csf("net_pi_amount")];
		}
	}
	
	?>
    <script>
        function print_window()
        {
            document.getElementById('scroll_body').style.overflow="auto";
            document.getElementById('scroll_body').style.maxHeight="none";
            $(".flt").css("display","none");
            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
            '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title>Total BTB Accepted</title></head><body><div style="width:800px; margin-top:20px;"><? echo ""; ?></div>'+document.getElementById('report_div').innerHTML+'</body</html>');
            d.close();
            document.getElementById('scroll_body').style.overflowY="scroll";
            document.getElementById('scroll_body').style.maxHeight="320px";
            $(".flt").css("display","block");
        }
    </script>
    <div style="width:800px;" align="center">
        <input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp;
        <div id="report_container"> </div>
    </div>
    <?
	// if(count($sql_result)<1) die;
    ob_start();
	
    ?>
    <div style="width:800px;" id="report_div">
    	<table width="780" cellspacing="0" cellpadding="0" border="0" align="left" rules="all">
            <tr>
            	<td colspan="6">&nbsp;</td>
            </tr>
            <tr>
                <td width="110">File No :</td><td width="150"><? echo $file_no; ?></td>
                <td width="110">Buyer Name :</td><td width="150"><? echo $buyer_name_arr[$buyer]; ?></td>
                <td width="110">Item Budgeted Value :</td><td><? echo number_format($cat_wise_budge_val,2); ?></td>
            </tr>
            <tr>
                <td>BTB to be opned :</td><td title="total budget value - total btb open value"><? $btb_tobe_open=$cat_wise_budge_val-$tot_btb_amount; echo number_format($btb_tobe_open,2); ?></td>
                <td>&nbsp;</td><td>&nbsp;</td>
                <td>Total BTB Open:</td><td><? echo number_format($tot_btb_amount,2); ?></td>
            </tr>
            <tr>
                <td>&nbsp;</td><td>&nbsp;</td>
                <td>Acceptance balance:</td><td title="total btb open value - total acceptance value"><? $btb_balance=$tot_btb_amount-$tot_accep_amount; echo number_format($btb_balance,2); ?></td>
                <td>&nbsp;</td><td>&nbsp;</td>
            </tr>
            <tr>
            	<td colspan="6">&nbsp;</td>
            </tr>
        </table>
    	<table width="780" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="left" rules="all">
            <thead>
            	<tr>
                	<th width="50">SL</td>
                    <th width="110">Invoice No</td>
                    <th width="110">Amount</td>
                    <th width="80">Maturity date</td>
                    <th width="80">Invoice date</td>
                    <th width="110">Lc No</td>
                    <th width="110">Item Cetagory</td>
                    <th >Supplier</td>
                </tr>
            </thead>
        </table>
        <div style="width:800px; max-height:320px; overflow-y:scroll" id="scroll_body" >
            <table width="780" cellspacing="0" cellpadding="0" id="table_id5" border="1" class="rpt_table" rules="all">
                <tbody >
                <?
        		
        		$i=1;
        		foreach($dtls_data as $row)
        		{
        		    if ($i%2==0) $bgcolor="#E9F3FF";
        		    else $bgcolor="#FFFFFF";
        		    ?>
                	<tr bgcolor="<? echo $bgcolor; ?>">
                    	<td  width="50" ><?  echo $i; ?></td>
                        <td width="110"><p style="word-break: break-all;"><?  echo $row["invoice_no"]; ?></p></td>
                        <td align="right" width="110"><?  echo number_format($row["inv_amount"],2); $total_amount+=$row["inv_amount"]; ?></td>
                        <td  align="center" width="80.">
        				<?
        				if ($row["maturity_date"]!='0000-00-00') echo change_date_format($row["maturity_date"]); else echo "";
        				?>
                        </td>
                        <td  align="center" width="80">
        				<?
        				if ($row["invoice_date"]!='0000-00-00') echo change_date_format($row["invoice_date"]); else echo "";
        				?>
                        </td>
                        <td width="110"><p style="word-break: break-all;mso-number-format:'\@';"><?  echo $row["lc_number"]; ?></p></td>
                        <td width="110"><?  echo $item_category[$row["item_category_id"]]; ?></td>
                        <td ><?  echo $suplier_name_arr[$row["supplier_id"]]; ?></td>
                    </tr>
                    <?
    		        $i++;
    		    }
    		    ?>
                </tbody>
    	    </table>
        </div>
        <table width="780" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all" >
            <tfoot>
                <tr>
                    <th width="50">&nbsp;</th>
                    <th width="110">Total</th>
                    <th width="110" id="value_total_amnt"><? echo number_format($total_amount,2); ?></th>
                    <th width="80">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="110">&nbsp;</th>
                    <th width="110">&nbsp;</th>
                    <th >&nbsp;</th>
                </tr>
            </tfoot>
        </table>
        <script type="text/javascript">
                var tableFilters2 = 
                {
                    col_operation: {
                    id: ["value_total_amnt"],
                    col: [2],
                    operation: ["sum"],
                    write_method: ["innerHTML"]
                    }
                }
            setFilterGrid("table_id5",-1,tableFilters2);
            </script>
            <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </div>
    <?
    $html=ob_get_contents();
    ob_flush();

    foreach (glob("$user_id*.xls") as $filename)
    {
       @unlink($filename);
    }

    //html to xls convert
    $name=time();
    $name=$user_id."_".$name.".xls";
    $create_new_excel = fopen(''.$name, 'w');
    $is_created = fwrite($create_new_excel,$html);
    ?>
    <input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
    <script>
        setFilterGrid('table_id',-1);
        $(document).ready(function(e) {
            document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
        });
    </script>
    <?
	exit();
}

if ($action=="btb_paid")
{

  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$hidden_btb_id=str_replace("'","",$hidden_btb_id);
	//echo $hidden_btb_id;die;
	$paid_id_arr=explode("_",$hidden_btb_id);
	//var_dump($paid_id_arr); die;
	//$atsite_accepted=return_field_value("sum(current_acceptance_value) as current_acceptance_value","com_import_invoice_dtls","btb_lc_id in($atsite_accep_id)","current_acceptance_value");

	//echo "select invoice_id,sum(accepted_ammount) as paid from com_import_payment where lc_id in(".$paid_id_arr[2].") and status_active='1' group by invoice_id";die;
	$usence_paid_arr=return_library_array( "select invoice_id, sum(accepted_ammount) as paid from com_import_payment where is_deleted=0  and status_active=1 group by invoice_id",'invoice_id','paid');
	//var_dump($usence_paid_arr);die;

	if($paid_id_arr[3]!="")
	{
		if($db_type==0)
		{
			$paid_id_arr_index2="and d.id in(".$paid_id_arr[3].")";
		}
		else
		{
			$paid_id_arr_index2=" and d.id in(".$paid_id_arr[3].")";
		}
	}
	else
	{
	 	$paid_id_arr_index2="";
	}

	if($db_type==0)
	{
	   $sql= "select a.id, a.invoice_no, a.document_value as inv_amount, a.maturity_date, a.invoice_date, c.lc_number as lc_number, c.supplier_id, c.currency_id, c.lc_date, c.importer_id, f.item_category_id, max(d.payment_date) as payment_date
        from com_import_invoice_mst a, com_import_invoice_dtls b, com_btb_lc_master_details c, com_import_payment d, com_btb_lc_pi e, com_pi_item_details f
        where a.id=b.import_invoice_id and b.btb_lc_id=c.id and a.id=d.invoice_id and c.id=e.com_btb_lc_master_details_id and e.pi_id=f.pi_id $paid_id_arr_index2 and d.status_active=1 and b.btb_lc_id in($paid_id_arr[0])
        group by a.id,a.invoice_no,a.document_value,a.maturity_date,a.invoice_date,c.supplier_id,c.currency_id, c.lc_date, c.importer_id,f.item_category_id,c.lc_number ";

	}
	else if($db_type==2)
	{
        $sql= "select a.id, a.invoice_no, a.document_value as inv_amount, a.maturity_date, a.invoice_date, c.lc_number as lc_number, c.supplier_id, c.currency_id, c.lc_date, c.importer_id, f.item_category_id, max(d.payment_date) as payment_date
		from com_import_invoice_mst a, com_import_invoice_dtls b, com_btb_lc_master_details c, com_import_payment d, com_btb_lc_pi e, com_pi_item_details f
		where a.id=b.import_invoice_id and b.btb_lc_id=c.id and a.id=d.invoice_id and c.id=e.com_btb_lc_master_details_id and e.pi_id=f.pi_id $paid_id_arr_index2 and d.status_active=1 and b.btb_lc_id in($paid_id_arr[0])
		group by a.id,a.invoice_no,a.document_value,a.maturity_date,a.invoice_date,c.supplier_id,c.currency_id, c.lc_date, c.importer_id,f.item_category_id,c.lc_number ";
	}


	//echo $sql;//die;
	$sql_result=sql_select($sql);$paid_result=array();
	/*foreach($sql_result as $row)
	{
		$paid_result[$row[csf("btb_id")]]["btb_id"]=$row[csf("btb_id")];
		$paid_result[$row[csf("btb_id")]]["btb_id"]=$row[csf("btb_id")];
		$paid_result[$row[csf("btb_id")]]["btb_id"]=$row[csf("btb_id")];
		$paid_result[$row[csf("btb_id")]]["btb_id"]=$row[csf("btb_id")];
	}*/
    ?>
    <script>
        function print_window()
        {
            document.getElementById('scroll_body').style.overflow="auto";
            document.getElementById('scroll_body').style.maxHeight="none";
            $(".flt").css("display","none");
            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
            '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title>Total BTB Paid</title></head><body><div style="width:800px; margin-top:20px;"><? echo ""; ?></div>'+document.getElementById('report_div').innerHTML+'</body</html>');
            d.close();
            document.getElementById('scroll_body').style.overflowY="scroll";
            document.getElementById('scroll_body').style.maxHeight="320px";
            $(".flt").css("display","block");
        }
    </script>
    <div style="width:828px;" align="center">
        <input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp;
        <div id="report_container"> </div>
    </div>
    <?
        ob_start();
    ?>
    <div style="width:828px;" id="report_div">
        <?
    	if(!empty($sql_result))
    	{
    	    ?>
            <table width="828" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="left" rules="all">
                <thead>
                    <tr>
                       <th width="30">SL</td>
                        <th width="100">Invoice No</td>
                        <th width="80">Amount</td>
                        <th width="75">Maturity Date</td>
                        <th width="75">Invoice Date</td>
                        <th width="95">Lc No</td>
                        <th width="100">Item Cetagory</td>
                        <th width="100">Supplier</td>
                        <th width="80">Paid Amount</td>
                        <th >Paid Date</td>
                    </tr>
                </thead>
            </table>
            <div style="width:828px; max-height:320px; overflow-y:scroll" id="scroll_body" >
                <table width="810" cellspacing="0" cellpadding="0" id="table_id5" border="1" class="rpt_table" rules="all" >
                    <tbody >
                        <?
                        $i=1;
                        $inv_amount=0;
                        foreach($sql_result as $row)
                        {
                            if ($i%2==0) $bgcolor="#E9F3FF";
                            else $bgcolor="#FFFFFF";
                            if($row[csf("currency_id")]==1)
							{
								$conversion_rate=set_conversion_rate(2, $row[csf("lc_date")],$row[csf("importer_id")]);
								$inv_amount=$row[csf("inv_amount")]/$conversion_rate;
							}
                            else $inv_amount=$row[csf("inv_amount")];
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>">
                                <td width="30"><?  echo $i; ?></td>
                                <td width="100"><p><?  echo $row[csf("invoice_no")]; ?>&nbsp;</p></td>
                                <td width="80" align="right"><? echo number_format($inv_amount,2); $total_inv_amount+=$inv_amount; ?></td>
                                <td width="75" align="center"><p>
                                <?
                                if ($row[csf("maturity_date")]!='0000-00-00') echo change_date_format($row[csf("maturity_date")]); else echo "";
                                ?>
                                &nbsp;</p></td>
                                <td width="75" align="center"><p>
                                <?
                                if ($row[csf("invoice_date")]!='0000-00-00') echo change_date_format($row[csf("invoice_date")]); else echo "";
                                ?>
                                &nbsp;</p></td>
                                <td width="95"><p style="mso-number-format:'\@';"><?  echo implode(",",array_unique(explode(",",$row[csf("lc_number")]))); ?>&nbsp;</p></td>
                                <td  width="100"><p><?  echo $item_category[$row[csf("item_category_id")]]; ?>&nbsp;</p></td>
                                <td width="100"><p><?  echo $suplier_name_arr[$row[csf("supplier_id")]]; ?>&nbsp;</p></td>
                                <td width="80" align="right">
                                <?
                                  $usence_paid_amt=$usence_paid_arr[$row[csf("id")]];
                                  echo number_format($usence_paid_amt,2); $total_accept_amount+=$usence_paid_amt;
                                  $tem_arr[]=$row[csf("btb_lc_id")];
                                ?>
                                </td>
                                <td align="center"><p>
                                <?
                                  if ($row[csf("payment_date")]!='0000-00-00') echo change_date_format($row[csf("payment_date")]); else echo "";
                                  $tem_date_arr[]=$row[csf("btb_lc_id")];
                                ?>&nbsp;</p></td>
                            </tr>
                            <?
                            $i++;
                        }
                        ?>
                    </tbody>
                    
                </table>
                <table width="810" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all" >
                    <tfoot>
                        <tr>
                            <th width="30">&nbsp;</th>
                            <th width="100">Total:</th>
                            <th width="80" id="value_total_inv_amount"><? echo number_format($total_inv_amount,2); ?></th>
                            <th width="75">&nbsp;</th>
                            <th width="75">&nbsp;</th>
                            <th width="95">&nbsp;</th>
                            <th width="100">&nbsp;</th>
                            <th width="100">Total:</th>
                            <th width="80" id="value_total_accept_amount"><? echo number_format($total_accept_amount,2); ?></th>
                            <th >&nbsp;</th>
                        </tr>
                    </tfoot>
                </table>
                <script type="text/javascript">
                var tableFilters2 = 
                {
                    col_operation: {
                    id: ["value_total_inv_amount","value_total_accept_amount"],
                    col: [2,8],
                    operation: ["sum","sum"],
                    write_method: ["innerHTML","innerHTML"]
                    }
                }
            setFilterGrid("table_id5",-1,tableFilters2);
            </script>
            <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
            </div>
            <?
        }
        if($db_type==0)
        {
            $sql = "select a.id, a.invoice_no, sum(b.current_acceptance_value) as inv_amount, a.maturity_date, a.invoice_date, group_concat(distinct c.lc_number) as lc_number, c.supplier_id, c.currency_id, c.lc_date, c.importer_id, c.item_category_id 
            from com_import_invoice_mst a, com_import_invoice_dtls b, com_btb_lc_master_details c 
            where a.id=b.import_invoice_id and a.btb_lc_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.btb_lc_id in(".$paid_id_arr[2].") 
            group by a.id,a.invoice_no,a.maturity_date,a.invoice_date,c.supplier_id, c.currency_id, c.lc_date, c.importer_id, c.item_category_id"; 
        }
        else if($db_type==2)
        {
            $sql= "select a.id, a.invoice_no, sum(b.current_acceptance_value) as inv_amount, a.maturity_date, a.invoice_date, LISTAGG(CAST( c.lc_number AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.lc_number) as lc_number, c.supplier_id, c.currency_id, c.lc_date, c.importer_id, c.item_category_id 
            from com_import_invoice_mst a, com_import_invoice_dtls b, com_btb_lc_master_details c 
            where a.id=b.import_invoice_id and a.btb_lc_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.btb_lc_id in(".$paid_id_arr[2].") 
            group by a.id,a.invoice_no,a.maturity_date,a.invoice_date,c.supplier_id,c.currency_id, c.lc_date, c.importer_id, c.item_category_id"; 
        }

        //echo $sql;die;
        $sql_result=sql_select($sql);
        if(!empty($sql_result))
        {
            ?>
            <br>
            <div style="float: left;">
            <legend style="width:780px;">At-Site Accepted</legend>
                <table width="780" cellspacing="0" cellpadding="0" border="1" class="rpt_table" style="float: left;" rules="all">
                    <thead>
                        <tr>
                            <th width="50">SL</td>
                            <th width="110">Invoice No</td>
                            <th width="110">Amount</td>
                            <th width="80">Maturity date</td>
                            <th width="80">Invoice date</td>
                            <th width="110">Lc No</td>
                            <th width="110">Item Cetagory</td>
                            <th >Supplier</td>
                        </tr>
                    </thead>
                </table>
                <div style="width:780px; max-height:320px;" >
                    <table width="780" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all" >
                        <tbody>
                        <?
                        $i=1;
                        $inv_amount=0;
                        foreach($sql_result as $row)
                        {
                            if ($i%2==0) $bgcolor="#E9F3FF";
                            else $bgcolor="#FFFFFF";
                            if($row[csf("currency_id")]==1)
							{
								$conversion_rate=set_conversion_rate(2, $row[csf("lc_date")],$row[csf("importer_id")]);
								$inv_amount=$row[csf("inv_amount")]/$conversion_rate;
							}
                            else $inv_amount=$row[csf("inv_amount")];
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>">
                                <td><?  echo $i; ?></td>
                                <td ><p><?  echo $row[csf("invoice_no")]; ?>&nbsp;</p></td>
                                <td  align="right"><?  echo number_format($inv_amount,2); $total_amount+=$inv_amount; ?></td>
                                <td  align="center"><p>
                                <?
                                if ($row[csf("maturity_date")]!='0000-00-00') echo change_date_format($row[csf("maturity_date")]); else echo "";
                                ?>
                                &nbsp;</p></td>
                                <td  align="center"><p>
                                <?
                                if ($row[csf("invoice_date")]!='0000-00-00') echo change_date_format($row[csf("invoice_date")]); else echo "";
                                ?>
                                &nbsp;</p></td>
                                <td ><p style="word-break: break-all;mso-number-format:'\@';"><?  echo implode(",",array_unique(explode(",",$row[csf("lc_number")]))); ?>&nbsp;</p></td>
                                <td  ><p><?  echo $item_category[$row[csf("item_category_id")]]; ?>&nbsp;</p></td>
                                <td ><p><?  echo $suplier_name_arr[$row[csf("supplier_id")]]; ?>&nbsp;</p></td>
                            </tr>
                            <?
                            $i++;
                        }
                        ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th width="50">&nbsp;</td>
                                <th width="110">Total</td>
                                <th width="110"><? echo number_format($total_amount,2); ?></td>
                                <th width="80">&nbsp;</td>
                                <th width="80">&nbsp;</td>
                                <th width="110">&nbsp;</td>
                                <th width="110">&nbsp;</td>
                                <th>&nbsp;</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <?
        }

        $sql= "select a.id, a.lc_number, a.currency_id, a.lc_date, a.importer_id, a.lc_value, a.pi_id, a.supplier_id, a.item_category_id from com_btb_lc_master_details a where a.id in(".$paid_id_arr[1].")"; //echo $sql;die;
        $sql_result=sql_select($sql);

        if(!empty($sql_result))
        {
            ?>
        	<br>
        	<div style="float: left;">
            <legend style="width:752px;">Cash In Advance</legend>
                <table width="750" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="left" rules="all">
                    <thead>
                        <tr>
                            <th width="50">SL</td>
                            <th width="130">BTB Lc No</td>
                            <th width="80">Lc Date</td>
                            <th width="100">Amount</td>
                            <th width="150">Supplier</td>
                            <th >Item Cetagory</td>
                        </tr>
                    </thead>
                </table>
                <div style="width:780px; max-height:320px;" id="scroll_body">
                    <table width="750" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all" >
                        <tbody>
                        <?
                        $i=1;
                        foreach($sql_result as $row)
                        {
                            if ($i%2==0) $bgcolor="#E9F3FF";
                            else $bgcolor="#FFFFFF";
                            if($row[csf("currency_id")]==1)
							{
								$conversion_rate=set_conversion_rate(2, $row[csf("lc_date")],$row[csf("importer_id")]);
								$lc_value=$row[csf("lc_value")]/$conversion_rate;
							}
                            else $lc_value=$row[csf("lc_value")];
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>">
                                <td ><?  echo $i; ?></td>
                                <td ><p style="mso-number-format:'\@';"><?  echo $row[csf("lc_number")]; ?>&nbsp;</p></td>
                                <td  align="center"><p><? if($row[csf("lc_date")]!='0000-00-00')  echo change_date_format($row[csf("lc_date")]); else echo ""; ?>&nbsp;</p></td>
                                <td  align="right"><?  echo number_format($lc_value,2);  $total_val+=$lc_value;?></td>
                                <td align="center"><p><?  echo $suplier_name_arr[$row[csf("supplier_id")]]; ?>&nbsp;</p></td>
                                <td><p><?  echo $item_category[$row[csf("item_category_id")]]; ?>&nbsp;</p></td>
                            </tr>
                            <?
                            $i++;
                        }
                        ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th width="50">&nbsp;</td>
                                <th width="130">&nbsp;</td>
                                <th width="80">Total</td>
                                <th width="100"><? echo number_format($total_val,2); ?> </td>
                                <th width="150">&nbsp;</td>
                                <th>&nbsp;</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <?
        }
        //exit();
        ?>
	</div>

  	<!--   </div> -->
    <?
    $html=ob_get_contents();
    ob_flush();

    foreach (glob("$user_id*.xls") as $filename)
    {
        @unlink($filename);
    }

    //html to xls convert
    $name=time();
    $name=$user_id."_".$name.".xls";
    $create_new_excel = fopen(''.$name, 'w');
    $is_created = fwrite($create_new_excel,$html);

    ?>
    <input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
    <script>
        setFilterGrid('table_id',-1);
        $(document).ready(function(e) {
            document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
        });
    </script>
    <?
	exit();
}

if($action=="btb_open")
{
	echo load_html_head_contents("Subprocess Details", "../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);

	$hidden_btb_id=str_replace("'","",$hidden_btb_id);
    $file_buyer=explode("*",str_replace("'","",$file_buyer));
    $file_no=$file_buyer[0];
    $buyer_name=$buyer_name_arr[$file_buyer[1]];
    //echo $hidden_btb_id;die;

    if($db_type==0) // for mysql
    {
        $sql= "select a.id, a.lc_number, a.lc_date, a.lc_value, a.pi_id, a.supplier_id, GROUP_CONCAT(c.item_category_id) as item_category_id, sum(c.quantity) as quantity
        from com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_item_details c
        where a.id=b.com_btb_lc_master_details_id and b.pi_id=c.pi_id and a.id in($hidden_btb_id) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and c.status_active=1
        group by a.id, a.lc_number, a.lc_date, a.lc_value, a.pi_id, a.supplier_id";

        //or
        /*
        select a.id, a.lc_number, a.lc_date, a.lc_value, a.pi_id, a.supplier_id,
        GROUP_CONCAT(DISTINCT c.item_category_id) as item_category_id,
        sum(c.quantity) as quantity
        from com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_item_details c
        where a.id=b.com_btb_lc_master_details_id and b.pi_id=c.pi_id and a.id in(1,2,3,4,5,6) and a.is_deleted=0 and a.status_active=1
        group by a.id, a.lc_number, a.lc_date, a.lc_value, a.pi_id, a.supplier_id
        */
    }
    else if($db_type==2) // for oracle
    {
        $sql= "select a.id, a.lc_number, a.lc_date, a.lc_value, a.pi_id, a.supplier_id, rtrim(xmlagg(xmlelement(e,c.item_category_id,', ').extract('//text()') order by c.item_category_id).getclobval(),', ') as item_category_id, sum(c.quantity) as quantity, a.currency_id, a.lc_date, a.importer_id
        from com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_item_details c
        where a.id=b.com_btb_lc_master_details_id and b.pi_id=c.pi_id and a.id in($hidden_btb_id) and a.is_deleted=0 and a.status_active=1
        group by a.id, a.lc_number, a.lc_date, a.lc_value, a.pi_id, a.supplier_id, a.currency_id, a.lc_date, a.importer_id";

        /* or
        select a.id, a.lc_number, a.lc_date, a.lc_value, a.pi_id, a.supplier_id,
        REGEXP_REPLACE(listagg(cast(c.item_category_id as varchar(4000)),',') within group (order by c.item_category_id) ,'([^,]+)(,\1)+', '\1') as item_category_id ,
        sum(c.quantity) as quantity
        from com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_item_details c
        where a.id=b.com_btb_lc_master_details_id and b.pi_id=c.pi_id and a.id in(1140,1145,1201)
        and a.is_deleted=0 and a.status_active=1
        group by a.id, a.lc_number, a.lc_date, a.lc_value, a.pi_id, a.supplier_id
        */
    }

    /*
    $sql= "select a.id, a.lc_number, a.lc_date, a.lc_value, a.pi_id, a.supplier_id, a.item_category_id, sum(c.quantity) as quantity from com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_item_details c where a.id=b.com_btb_lc_master_details_id and b.pi_id=c.pi_id and a.id in($hidden_btb_id) and a.is_deleted=0 and a.status_active=1 group by a.id, a.lc_number, a.lc_date, a.lc_value, a.pi_id, a.supplier_id, a.item_category_id "; */
    //echo $sql;
    $sql_result=sql_select($sql);
    ?>
    <script>
        function print_window()
        {
            document.getElementById('scroll_body').style.overflow="auto";
            document.getElementById('scroll_body').style.maxHeight="none";
            $(".flt").css("display","none");
            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
            '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title>Total BTB Opened Details</title></head><body><div style="width:820px; margin-top:20px;"><? echo "<b>File No: " .$file_no."&nbsp;&nbsp;&nbsp;&nbsp; Buyer Name: ".$buyer_name."</b><br>&nbsp;<br>"; ?></div>'+document.getElementById('report_div').innerHTML+'</body</html>');
            d.close();
            document.getElementById('scroll_body').style.overflowY="scroll";
            document.getElementById('scroll_body').style.maxHeight="320px";
            $(".flt").css("display","block");
        }
    </script>
    <div>
        <div style="width:820px;" align="center">
            <input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp;
            <div id="report_container"> </div>
        </div>
        <?
        ob_start();
        ?>

        <div style="width:820px;" id="report_div">
            <table cellspacing="0" width="800" border="1" rules="all" class="rpt_table">
                <thead>
                    <tr>
                        <th width="50">SL</td>
                        <th width="130">BTB Lc No</td>
                        <th width="80">Lc Date</td>
                        <th width="100">Amount</td>
                        <th width="130">PI No.</td>
                        <th width="130">Supplier</td>
                        <th width="80">Item Cetagory</td>
                        <th width="100">PI Qty</td>
                    </tr>
                </thead>
            </table>
            <div style="width:820px; max-height:320px; overflow-y:scroll" id="scroll_body" >
                <table width="800" cellspacing="0" cellpadding="0" id="table_id5" border="1" class="rpt_table" rules="all" >
                    <tbody >
                    <?
                    $i=1;
                    //print_r($item_category);
                    foreach($sql_result as $row)
                    {
                        //echo $row[csf("item_category_id")];die;//1,4
                        $arrUni = array_unique(explode(",",$row[csf("item_category_id")]->load()));
                        //$catArr = explode(',', $catagory);
                        //print_r($catArr);
                        //print_r(array_unique($catArr));
                        //$arrUni = array_unique($catArr);
						
						if($row[csf("currency_id")]==1)
						{
							$conversion_rate=set_conversion_rate(2, $row[csf("lc_date")],$row[csf("importer_id")]);
							$row[csf("lc_value")]=$row[csf("lc_value")]/$conversion_rate;
						}

                        if ($i%2==0) $bgcolor="#E9F3FF";
                        else $bgcolor="#FFFFFF";
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i;?>">
                            <td width="50" align="center"><?  echo $i; ?></td>
                            <td width="130" align="center"><p><?  echo $row[csf("lc_number")]; ?>&nbsp;</p></td>
                            <td width="80" align="center"><p><? if($row[csf("lc_date")]!='0000-00-00')  echo change_date_format($row[csf("lc_date")]); else echo ""; ?></p></td>
                            <td  width="100" align="right"><p><?  echo number_format($row[csf("lc_value")],2);  $total_val+=$row[csf("lc_value")];?></p></td>
                            <td width="130" align="center">
                            <p><?
                              $po_id=explode(",",$row[csf("pi_id")]);
                              $k=1;
                              foreach($po_id as $row_po_id)
                              {
                                  if($k!=1) echo ", ";
                                  echo  $pi_no_arr[$row_po_id];
                                $k++;
                              }
                            ?></p>
                            </td>
                            <td width="130" align="center"><p><?  echo $suplier_name_arr[$row[csf("supplier_id")]]; ?></p></td>
                            <td width="80" align="center"><p>
                                <?
                                    $catName = "";
                                    foreach ($arrUni as $key => $value) {
                                        if ($catName == "") {
                                           $catName = $item_category[$value];
                                        }
                                        else
                                        {
                                            $catName = ", ".$item_category[$value];
                                        }
                                        echo rtrim($catName, ', ');                                    
                                    }
                                    
                                ?></p></td>
                            <td align="right" width="100"><?  echo $row[csf("quantity")]; $pi_qty+=$row[csf("quantity")]; ?></td>
                        </tr>
                        <?
                        $i++;
                    }
                    ?>
                    </tbody>
                    
                </table>
            </div>
            <table width="800" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all" >
                <tfoot>
                        <tr>
                            <th width="50">&nbsp;</th>
                            <th width="130">&nbsp;</th>
                            <th width="80" align="right">Total</th>
                            <th width="100" align="right" id="value_total_amnt"><? echo number_format($total_val,2); ?> </th>
                            <th  width="130">&nbsp;</th>
                            <th width="130">&nbsp;</th>
                            <th width="80" align="right"> Total PI Qty</th>
                            <th width="100" align="right" id="total_pi_qty"><? echo number_format($pi_qty,2)?></th>
                        </tr>
                    </tfoot>
            </table>
            <script type="text/javascript">
                var tableFilters2 = 
                {
                    col_operation: {
                    id: ["value_total_amnt","total_pi_qty"],
                    col: [3,7],
                    operation: ["sum","sum"],
                    write_method: ["innerHTML","innerHTML"]
                    }
                }
            setFilterGrid("table_id5",-1,tableFilters2);
            </script>
            <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
        </div>
        <?
        $html=ob_get_contents();
        ob_flush();

        foreach (glob("$user_id*.xls") as $filename)
        {
           @unlink($filename);
        }
        //html to xls convert
        $name=time();
        $name=$user_id."_".$name.".xls";
        $create_new_excel = fopen(''.$name, 'w');
        $is_created = fwrite($create_new_excel,$html);
        ?>
        <input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
        <script>
            setFilterGrid('table_id',-1);
            $(document).ready(function(e) {
                document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
            });
        </script>
    </div>
    <?
    exit();
}

if($action=="invoice_details")
{
    echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
    extract($_REQUEST);
	$lc_ids = trim($lc_ids);
	$sc_ids = trim($sc_ids);
	$company = trim($company);
	$cbo_buyer_name = trim($cbo_buyer_name);
	//echo $lc_ids;
    ?>
    <script>
    function print_window()
    {
        document.getElementById('scroll_body').style.overflow="auto";
        document.getElementById('scroll_body').style.maxHeight="none";

        var w = window.open("Surprise", "#");
        var d = w.document.open();
        d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');

        d.close();
        document.getElementById('scroll_body').style.overflowY="scroll";
        document.getElementById('scroll_body').style.maxHeight="230px";
    }

    function fnClosed()
    {
        parent.emailwindow.hide();
    }
    </script>
    <style type="text/css">
    .wrd_brk{word-break: break-all; word-wrap: break-word;}
    </style>
    <div align="center">
        <input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
        <input type="button" value="Close" onClick="fnClosed()" style="width:100px"  class="formbutton"/>
        <div id="report_container" align="left">
            <table style="margin-top:2px" class="rpt_table" border="0" rules="all" width="1350" cellpadding="0" cellspacing="0" align="left">
               <thead>
                    <tr>
                        <th colspan="18">Contract Details</td>
                    </tr>
                    <tr>
                        <th width="30">SL</td>
                        <th width="80">Invoice No</td>
                        <th width="80">Invoice Date</td>
                        <th width="80">EXP No</td>
                        <th width="80">EXP DTE</td>
                        <th width="50">SC/LC</td>
                        <th width="100">SC/LC No</td>
                        <th width="80">Invoice Qty Pcs</td>
                        <th width="80">Invoice value</td>
                        <th width="80">Net Invoice Amount</td>
                        <th width="50">Currency</td>
                        <th width="80">Doc Submit Buyer</td>
                        <th width="80">Doc Submit Bank</td>
                        <th width="80">Bank Bill No</td>
                        <th width="80">Bank Bill Date</td>
                        <th width="80">Actual Realized Date</td>
                        <th width="80">Realization Amount</td>
                        <th>Realization Balance</td>
                    </tr>
                </thead>
            </table>
            <div style="width:1370px; overflow-y:scroll; max-height:205px" id="scroll_body" align="left" >
                <table class="rpt_table" border="0" rules="all" width="1350" cellpadding="0" cellspacing="0" align="left">
                    <?
                    $adjustment_arr=array();
                    if(!empty($lc_ids))
                    {
                        if($db_type==0)
                        {
                            $lc_sql="SELECT a.id as id, a.invoice_no, a.invoice_date, a.exp_form_no, a.exp_form_date, a.is_lc, a.lc_sc_id, a.invoice_quantity, a.invoice_value, a.net_invo_value, a.benificiary_id, b.export_lc_no, b.currency_name, c.lien_bank, c.submit_to, c.submit_type,c.submit_date,c.buyer_id, c.bank_ref_no, c.bank_ref_date, d.invoice_id, e.received_date, e.is_invoice_bill, sum( f.document_currency ) as doc_realization_amount
	                            from com_export_lc b, com_export_invoice_ship_mst a
								left join com_export_doc_submission_mst c on a.id = d.invoice_id and d.is_lc=1
								left join com_export_doc_submission_invo d on d.doc_submission_mst_id = c.id
	                            where a.lc_sc_id=b.id and a.is_lc=1 and a.lc_sc_id in($lc_ids) and a.status_active=1 and a.benificiary_id like '%$company%' and  a.buyer_id like '%$cbo_buyer_name%'
								group by a.id, a.invoice_no, a.invoice_date, a.exp_form_no, a.exp_form_date, a.is_lc, a.invoice_quantity, a.invoice_value, a.net_invo_value, b.export_lc_no, b.currency_name, c.bank_ref_no, c.bank_ref_date, c.submit_type";
                        }
                        else if($db_type==2)
                        {
                            $lc_sql = "select a.id as id, a.invoice_no, a.invoice_date, a.exp_form_no, a.exp_form_date, a.is_lc, a.lc_sc_id, a.invoice_quantity, a.invoice_value, a.net_invo_value, a.benificiary_id, b.export_lc_no, b.currency_name, c.lien_bank,c.submit_to, c.submit_type,c.submit_date,c.buyer_id, c.bank_ref_no, c.bank_ref_date, d.invoice_id, e.received_date, e.is_invoice_bill, sum( f.document_currency ) as doc_realization_amount
 							from com_export_lc b, com_export_invoice_ship_mst a 
 							left join com_export_doc_submission_invo d on a.id = d.invoice_id and d.is_lc=1
 							left join  com_export_doc_submission_mst c on d.doc_submission_mst_id = c.id
 							left join com_export_proceed_realization e on e.invoice_bill_id = c.id 
 							left join com_export_proceed_rlzn_dtls f on e.id = f.mst_id
 							where a.lc_sc_id=b.id and a.lc_sc_id in($lc_ids) and a.is_lc=1 and a.status_active=1 and a.benificiary_id like '%$company%' and  a.buyer_id like '%$cbo_buyer_name%' 
							group by a.id, a.invoice_no, a.invoice_date, a.exp_form_no, a.exp_form_date, a.is_lc, a.lc_sc_id, a.invoice_quantity, a.invoice_value, a.net_invo_value, a.benificiary_id, b.export_lc_no, b.currency_name, c.lien_bank, c.submit_to, c.submit_type, c.submit_date, c.buyer_id, c.bank_ref_no, c.bank_ref_date, d.invoice_id, e.received_date, e.is_invoice_bill";
                        }
						//echo $lc_sql;
						$sql_lc=sql_select($lc_sql);
                        $total_shipment_val=0;$total_discount=0;$total_invoice_value=0;
                        foreach($sql_lc as $row_lc_result)
                        {
							if(!in_array($row_lc_result[csf("id")], $invo_id_check)){
                                $invo_id_check[$row_lc_result[csf("id")]] = $row_lc_result[csf("id")];
                                $total_invoice_quantity += $row_lc_result[csf("invoice_quantity")];
								$total_invoice_value += $row_lc_result[csf("invoice_value")];
								$total_net_invoice_amount += $row_lc_result[csf("net_invo_value")];
                               // print_r( $invo_id_check);
                            }
							$total_realization_amount        +=$row_lc_result[csf('realization_amount')];
							$total_realization_balance        +=$row_lc_result[csf('net_invo_value')];

                            $adjustment_arr[$row_lc_result[csf("id")]]["id"]=$row_lc_result[csf("id")];
                            $adjustment_arr[$row_lc_result[csf("id")]]["lc_sc_no"]=$row_lc_result[csf("export_lc_no")];//$row_sc_result[csf("lc_sc_no")];
                            $adjustment_arr[$row_lc_result[csf("id")]]["invoice_no"]=$row_lc_result[csf("invoice_no")];
                            $adjustment_arr[$row_lc_result[csf("id")]]["invoice_date"]=$row_lc_result[csf("invoice_date")];
                            $adjustment_arr[$row_lc_result[csf("id")]]["exp_form_no"]=$row_lc_result[csf("exp_form_no")];
                            $adjustment_arr[$row_lc_result[csf("id")]]["exp_form_date"]=$row_lc_result[csf("exp_form_date")];
                            $adjustment_arr[$row_lc_result[csf("id")]]["is_lc"]=$row_lc_result[csf("is_lc")];
                            $adjustment_arr[$row_lc_result[csf("id")]]["lc_sc_id"]=$row_lc_result[csf("lc_sc_id")];
                            $adjustment_arr[$row_lc_result[csf("id")]]["invoice_quantity"]=$row_lc_result[csf("invoice_quantity")];
                            $adjustment_arr[$row_lc_result[csf("id")]]["invoice_value"]=$row_lc_result[csf("invoice_value")];
                            $adjustment_arr[$row_lc_result[csf("id")]]["net_invo_value"]=$row_lc_result[csf("net_invo_value")];
                            $adjustment_arr[$row_lc_result[csf("id")]]["currency"]=$row_lc_result[csf("currency_name")];
							if($row_sc_result[csf("submit_to")] == 2){
								$adjustment_arr[$row_lc_result[csf("id")]]["doc_sub_buyer"]=$row_lc_result[csf("submit_date")];
							}else{
								$adjustment_arr[$row_lc_result[csf("id")]]["doc_sub_bank"]=$row_lc_result[csf("submit_date")];
							}

                            $adjustment_arr[$row_lc_result[csf("id")]]["bank_ref_no"]=$row_lc_result[csf("bank_ref_no")];
                            $adjustment_arr[$row_lc_result[csf("id")]]["bank_ref_date"]=$row_lc_result[csf("bank_ref_date")];
                            $adjustment_arr[$row_lc_result[csf("id")]]["actual_realized_date"]=$row_lc_result[csf("received_date")];
                            $adjustment_arr[$row_lc_result[csf("id")]]["realization_balance"]=$row_lc_result[csf("net_invo_value")] - $row_lc_result[csf("doc_realization_amount")];
                            $adjustment_arr[$row_lc_result[csf("id")]]["realization_amount"]=$row_lc_result[csf("doc_realization_amount")];
                        }
                    }
                    
                    if(!empty($sc_ids))
                    {
                        if($db_type==0)
                        {
							$sql="SELECT a.id as id, a.invoice_no, a.invoice_date, a.exp_form_no, a.exp_form_date, a.is_lc, a.lc_sc_id, a.invoice_quantity, a.invoice_value, a.net_invo_value, a.benificiary_id, b.contract_no, b.currency_name, b.contract_no, c.lien_bank,c.submit_to, c.submit_type,c.submit_date,c.buyer_id, c.bank_ref_no, c.bank_ref_date, d.invoice_id, d.net_invo_value, e.received_date, e.is_invoice_bill, sum( f.document_currency  ) as doc_realization_amount
    						from com_sales_contract b, com_export_invoice_ship_mst a
    						left join com_export_doc_submission_invo d on a.id = d.invoice_id and d.is_lc=2 
    						left join com_export_doc_submission_mst c on d.doc_submission_mst_id = c.id
    						left join com_export_proceed_realization e on e.invoice_bill_id = c.id 
    						left join com_export_proceed_rlzn_dtls f on e.id = f.mst_id
    						where a.lc_sc_id=b.id and a.is_lc=2 and a.lc_sc_id in($sc_ids) and a.status_active=1 and a.benificiary_id in($company)  and  a.buyer_id like '%$cbo_buyer_name%' 
    						group by a.id, a.invoice_no, a.invoice_date, a.exp_form_no, a.exp_form_date, a.is_lc, a.lc_sc_id, a.invoice_quantity, a.invoice_value, a.net_invo_value, a.benificiary_id, b.contract_no, b.currency_name, b.contract_no, c.lien_bank, c.submit_to, c.submit_type, c.submit_date, c.buyer_id, c.bank_ref_no, c.bank_ref_date, d.invoice_id, d.net_invo_value, e.received_date, e.is_invoice_bill";
                        }
                        else if($db_type==2)
                        {
                            $sql="SELECT a.id as id, a.invoice_no, a.invoice_date, a.exp_form_no, a.exp_form_date, a.is_lc, a.lc_sc_id, a.invoice_quantity, a.invoice_value, a.net_invo_value, a.benificiary_id, b.contract_no, b.currency_name, b.contract_no, c.lien_bank, c.submit_to, c.submit_type, c.submit_date, c.buyer_id, c.bank_ref_no, c.bank_ref_date, d.invoice_id, e.received_date, e.is_invoice_bill, sum(f.document_currency) as doc_realization_amount
                                from com_sales_contract b, com_export_invoice_ship_mst a
    							left join com_export_doc_submission_invo d on a.id = d.invoice_id and d.is_lc=2
    							left join com_export_doc_submission_mst c on d.doc_submission_mst_id = c.id 
    							left join com_export_proceed_realization e on e.invoice_bill_id = c.id 
    							left join com_export_proceed_rlzn_dtls f on e.id = f.mst_id
                                where a.lc_sc_id=b.id and a.is_lc=2 and a.lc_sc_id in($sc_ids) and a.status_active=1 and a.benificiary_id in($company) and a.buyer_id like '%$cbo_buyer_name%' 
    							group by a.id, a.invoice_no, a.invoice_date, a.exp_form_no, a.exp_form_date, a.is_lc, a.lc_sc_id, a.invoice_quantity, a.invoice_value, a.net_invo_value, a.benificiary_id, b.contract_no, b.currency_name, b.contract_no, c.lien_bank, c.submit_to, c.submit_type, c.submit_date, c.buyer_id, c.bank_ref_no, c.bank_ref_date, d.invoice_id, e.received_date, e.is_invoice_bill";
                        }
						
						$sql_sc_q=sql_select($sql);

                        $invo_id_check=array();$net_invo_check=array();
                        foreach($sql_sc_q as $row_sc_result)
                        {

                            $adjustment_arr[$row_sc_result[csf("id")]]["id"]=$row_sc_result[csf("id")];
                            $adjustment_arr[$row_sc_result[csf("id")]]["lc_sc_no"]=$row_sc_result[csf("contract_no")];//$row_sc_result[csf("lc_sc_no")];
                            $adjustment_arr[$row_sc_result[csf("id")]]["invoice_no"]=$row_sc_result[csf("invoice_no")];
                            $adjustment_arr[$row_sc_result[csf("id")]]["invoice_date"]=$row_sc_result[csf("invoice_date")];
                            $adjustment_arr[$row_sc_result[csf("id")]]["exp_form_no"]=$row_sc_result[csf("exp_form_no")];
                            $adjustment_arr[$row_sc_result[csf("id")]]["exp_form_date"]=$row_sc_result[csf("exp_form_date")];
                            $adjustment_arr[$row_sc_result[csf("id")]]["is_lc"]=$row_sc_result[csf("is_lc")];
                            $adjustment_arr[$row_sc_result[csf("id")]]["lc_sc_id"]=$row_sc_result[csf("lc_sc_id")];
                            $adjustment_arr[$row_sc_result[csf("id")]]["invoice_quantity"]=$row_sc_result[csf("invoice_quantity")];
                            $adjustment_arr[$row_sc_result[csf("id")]]["invoice_value"]=$row_sc_result[csf("invoice_value")];
                            $adjustment_arr[$row_sc_result[csf("id")]]["net_invo_value"]=$row_sc_result[csf("net_invo_value")];
                            $adjustment_arr[$row_sc_result[csf("id")]]["currency"]=$row_sc_result[csf("currency_name")];
							if($row_sc_result[csf("submit_to")] == 2){
								$adjustment_arr[$row_sc_result[csf("id")]]["doc_sub_buyer"]=$row_sc_result[csf("submit_date")];
							}else{
								$adjustment_arr[$row_sc_result[csf("id")]]["doc_sub_bank"]=$row_sc_result[csf("submit_date")];
							}

                            $adjustment_arr[$row_sc_result[csf("id")]]["bank_ref_no"]=$row_sc_result[csf("bank_ref_no")];
                            $adjustment_arr[$row_sc_result[csf("id")]]["bank_ref_date"]=$row_sc_result[csf("bank_ref_date")];
                            $adjustment_arr[$row_sc_result[csf("id")]]["actual_realized_date"]=$row_sc_result[csf("received_date")];
                            $adjustment_arr[$row_sc_result[csf("id")]]["realization_balance"]=$row_sc_result[csf("net_invo_value")] - $row_sc_result[csf("doc_realization_amount")];
                            $adjustment_arr[$row_sc_result[csf("id")]]["realization_amount"]=$row_sc_result[csf("doc_realization_amount")];
                            
                            if(!in_array($row_sc_result[csf("id")], $invo_id_check)){
                                $invo_id_check[$row_sc_result[csf("id")]] = $row_sc_result[csf("id")];
                                $total_invoice_value +=$row_sc_result[csf("invoice_value")];
								$total_invoice_quantity += $row_sc_result[csf("invoice_quantity")];
								$total_net_invoice_amount +=$row_sc_result[csf("net_invo_value")];
								//$test_data.=$row_sc_result[csf("id")]."=".$row_sc_result[csf("invoice_value")].",";
                               // print_r( $invo_id_check);
                            }
                            /*if(!in_array($row_sc_result[csf("id")], $net_invo_check)){
                                $net_invo_check[$row_sc_result[csf("id")]] = $row_sc_result[csf("id")];
                                $total_net_invoice_amount +=$row_sc_result[csf("net_invo_value")];
                            }*/
							$total_realization_amount        +=$conRow[csf('realization_amount')];
                            $total_realization_balance        +=$conRow[csf('net_invo_value')];
                        }
                    }
                    //print_r($invo_id_check);//die;
					$i=1;
                    foreach($adjustment_arr as $conRow)
                    {
                        if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
                        ?>
                        <tbody>
                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                <td width="30" align="center"><? echo $i; ?></td>
                                <td width="80" align="center" class="wrd_brk"><p><? echo $conRow['invoice_no']; ?></p></td>
                                <td width="80" align="center" class="wrd_brk"><p><? echo change_date_format($conRow['invoice_date']); ?></p></td>
                                <td width="80" align="center" class="wrd_brk"><p><? echo $conRow['exp_form_no']; ?></p></td>
                                <td width="80" align="center" class="wrd_brk"><p><? echo $conRow['exp_form_date']; ?></p></td>
                                <td width="50" align="center" class="wrd_brk"><p><? echo ($conRow['is_lc'] == 2) ?  "SC": "LC"; ?></p></td>
                                <td width="100" align="center" class="wrd_brk"><p><? echo $conRow['lc_sc_no']; ?></p></td>
                                <td width="80" align="right" class="wrd_brk"><p><? echo $conRow['invoice_quantity']; ?></p></td>
                                <td width="80" align="right" class="wrd_brk"><p><? echo $conRow['invoice_value']; ?></p></td>
                                <td width="80" align="right" class="wrd_brk"><p><? echo $conRow['net_invo_value']; ?></p></td>
                                <td width="50" align="center" class="wrd_brk"><p><? echo $currency[$conRow['currency']]; ?></p></td>
                                <td width="80" align="center" class="wrd_brk"><p><? echo $conRow['doc_sub_buyer']; ?></p></td>
                                <td width="80" align="center" class="wrd_brk"><p><? echo $conRow['doc_sub_bank']; ?></p></td>
                                <td width="80" align="center" class="wrd_brk"><p><? echo $conRow['bank_ref_no']; ?></p></td>
                                <td width="80" align="center" class="wrd_brk"><p><? echo $conRow['bank_ref_date']; ?></p></td>
                                <td width="80" align="center" class="wrd_brk"><p><? echo $conRow['actual_realized_date']; ?></p></td>
                                <td width="80" align="right" class="wrd_brk"><p><? echo $conRow['realization_amount']; ?></p></td>
                                <td align="right" class="wrd_brk"><p><? echo $conRow['realization_balance']; ?></p></td>
                            </tr>
                        </tbody>
                        <?
                        $i++;
                    }
                    ?>
                    <tfoot class="tbl_bottom">
                        <tr>
                            <td colspan="7" align="right">Total</td>
                            <td align="right" class="wrd_brk"><p><? echo number_format($total_invoice_quantity,2); ?></p></td>
                            <td align="right" class="wrd_brk"><p><? echo number_format($total_invoice_value,2); ?></p></td>
                            <td align="right" class="wrd_brk"><p><? echo number_format($total_net_invoice_amount,2); ?></p></td>
                            <td colspan="7" align="right" class="wrd_brk"><p><? echo number_format($total_realization_amount,2); ?></p></td>
                            <td colspan="7" align="right" class="wrd_brk"><p><? echo number_format($total_realization_balance,2); ?></p></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    <?
    exit();
}


if($action=="attach_order_details")
{
    echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
    extract($_REQUEST);
	$company = trim($company);
	$txt_file_no = trim($txt_file_no);
	$attach_order_id = trim($attach_order_id);
    $trimmed_ids = explode(',', trim($attach_order_id));

    $con = connect();
	$rid=execute_query("delete from GBL_TEMP_ENGINE where entry_form=177 and user_id=$user_id");
	if($rid) oci_commit($con);
    if(!empty($trimmed_ids)){
        fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 177, 1, $trimmed_ids,$empty_arr);
        $attach_sql="SELECT p.id as LC_SC_ID, p.contract_no as LC_SC_NO, b.id as PO_ID, b.po_number as PO_NUMBER, b.job_no_mst as JOB_NO_MST, b.pub_shipment_date as PUB_SHIPMENT_DATE, c.order_uom as ORDER_UOM, (b.po_quantity*c.total_set_qnty) as PO_QUANTITY_PCS, b.po_total_price as PO_TOTAL_PRICE,a.ATTACHED_QNTY as ATCH_QTY,a.ATTACHED_RATE, (a.attached_qnty*c.total_set_qnty) as ATTACHED_QNTY, c.style_ref_no AS STYLE_REF_NO,listagg(cast(d.gmts_item_id as varchar2(4000)),',') within group (order by d.gmts_item_id) as GMTS_ITEM_ID , a.attached_value as ATTACHED_VALUE, 1 as TYPE
        from com_sales_contract p, com_sales_contract_order_info a, wo_po_break_down b, wo_po_details_master c ,wo_po_details_mas_set_details d, GBL_TEMP_ENGINE e
        where p.id=a.com_sales_contract_id and a.wo_po_break_down_id=b.id and b.job_no_mst=c.job_no and c.id=d.job_id and b.id=e.REF_VAL and e.entry_form=177 and e.ref_from=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and p.internal_file_no='$txt_file_no' 
        group by p.id,p.contract_no,b.id, b.po_number, b.job_no_mst , b.pub_shipment_date, c.order_uom,b.po_quantity,c.total_set_qnty,b.po_total_price,a.ATTACHED_RATE,a.attached_qnty, c.style_ref_no, a.attached_value 
        union all
        select p.id as LC_SC_ID, p.export_lc_no as LC_SC_NO, b.id as PO_ID, b.po_number as PO_NUMBER, b.job_no_mst as JOB_NO_MST, b.pub_shipment_date as PUB_SHIPMENT_DATE, c.order_uom as ORDER_UOM, (b.po_quantity*c.total_set_qnty) as PO_QUANTITY_PCS, b.po_total_price as PO_TOTAL_PRICE,a.ATTACHED_QNTY as ATCH_QTY,a.ATTACHED_RATE, (a.attached_qnty*c.total_set_qnty) as ATTACHED_QNTY ,c.style_ref_no AS STYLE_REF_NO ,listagg(cast(d.gmts_item_id as varchar2(4000)),',') within group (order by d.gmts_item_id) as GMTS_ITEM_ID, a.attached_value as ATTACHED_VALUE, 2 as TYPE
        from com_export_lc p, com_export_lc_order_info a, wo_po_break_down b, wo_po_details_master c, wo_po_details_mas_set_details d, GBL_TEMP_ENGINE e 
        where p.id=a.com_export_lc_id and a.wo_po_break_down_id=b.id and b.job_no_mst=c.job_no and c.id=d.job_id and  b.id=e.REF_VAL and e.entry_form=177 and e.ref_from=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and p.internal_file_no='$txt_file_no'
        group by p.id,p.export_lc_no,b.id, b.po_number, b.job_no_mst , b.pub_shipment_date, c.order_uom,b.po_quantity,c.total_set_qnty,b.po_total_price,a.ATTACHED_RATE,a.attached_qnty, c.style_ref_no, a.attached_value ";
    }
    // echo $attach_sql;//die;
    $attach_sql_result=sql_select($attach_sql);

    $invoice_sql="SELECT a.INVOICE_NO, b.PO_BREAKDOWN_ID 
    from com_export_invoice_ship_mst a, com_export_invoice_ship_dtls b
    where a.id=b.mst_id and b.po_breakdown_id in($attach_order_id) and a.status_active=1 and b.status_active=1  ";
    // echo $invoice_sql;die;

    $invoice_sql_result=sql_select($invoice_sql);
    $invoice_arr=array();
    foreach($invoice_sql_result as $row)
    {
        $invoice_arr[$row["PO_BREAKDOWN_ID"]].=$row["INVOICE_NO"].", ";
    }

    
    // echo $attach_sql;die;
    $attach_sql_result=sql_select($attach_sql);
    ?>
    <script>
        function print_window()
        {
            document.getElementById('scroll_body').style.overflow="auto";
            document.getElementById('scroll_body').style.maxHeight="none";

            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');

            d.close();
            document.getElementById('scroll_body').style.overflowY="scroll";
            document.getElementById('scroll_body').style.maxHeight="230px";
        }

        function fnClosed()
        {
            parent.emailwindow.hide();
        }
    </script>
    <style type="text/css">
    .wrd_brk{word-break: break-all; word-wrap: break-word;}
    </style>
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
    ?>
    <div align="center">
        <a href="<?=$filename;?>" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>
        <input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
        <input type="button" value="Close" onClick="fnClosed()" style="width:100px"  class="formbutton"/>
        <?ob_start();?>
        <div id="report_container" align="left">
        	<table style="margin-top:2px" class="rpt_table" border="1" rules="all" width="600" cellpadding="0" cellspacing="0" align="left">
               <thead>
                    <tr>
                        <th width="400">Company Name</td>
                        <th>File No</td>
                    </tr>
                </thead>
                <tbody>
                	<tr>
                    	<td><? echo $company_arr[$company];?></td>
                        <td><? echo $txt_file_no;?></td>
                    </tr>
                </tbody>
            </table>
            <table style="margin-top:2px" class="rpt_table" border="1" rules="all" width="1430" cellpadding="0" cellspacing="0" align="left">
               <thead>
                    <tr>
                        <th width="30">SL</td>
                        <th width="120">Order No</td>
                        <th width="120">Invoice No</td>
                        <th width="120">Job No</td>
                        <th width="120">LC/SC No</td>
                        <th width="80">LC/SC</td>
                        <th width="100">Order Qnty</td>
                        <th width="100">Style</td>
                        <th width="100">Item</td>
                        <th width="60">UOM</td>
                        <th width="80">FOB</td>
                        <th width="100">Order Value</td>
                        <th width="100">Attach Qty</td>
                        <th width="100">Attach Value</td>
                        <th>Shipment Date</td>
                    </tr>
                </thead>
            </table>
            <div style="width:1450px; overflow-y:scroll; max-height:205px" id="scroll_body" align="left" >
                <table class="rpt_table" border="1" rules="all" width="1430" cellpadding="0" cellspacing="0" align="left">
                    <?
					$i=1;
                    foreach($attach_sql_result as $conRow)
                    {
                        $sep_item="";
                      
                        $items=explode(",",$conRow['GMTS_ITEM_ID']);
                        foreach($items as $item){

                            $sep_item.=$garments_item[$item].",";
                        }
                        if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
                        ?>
                        <tbody>
                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                <td width="30" align="center"><? echo $i; ?></td>
                                <td width="120" align="center" class="wrd_brk"><p><? echo $conRow['PO_NUMBER']; ?></p></td>
                                <td width="120" align="center" class="wrd_brk"><p><? echo rtrim($invoice_arr[$conRow['PO_ID']],", "); ?></p></td>
                                <td width="120" align="center" class="wrd_brk"><p><? echo $conRow['JOB_NO_MST']; ?></p></td>
                                <td width="120" align="center" class="wrd_brk"><p><? echo $conRow['LC_SC_NO']; ?></p></td>
                                <td width="80" align="center" class="wrd_brk"><p><? echo ($conRow['TYPE'] == 1) ?  "SC": "LC"; ?></p></td>
                                <td width="100" align="right" class="wrd_brk"><p><? echo $conRow['PO_QUANTITY_PCS']; ?></p></td>
                                <td width="100" class="wrd_brk"><p><? echo $conRow['STYLE_REF_NO']; ?></p></td>
                                <td width="100"  class="wrd_brk"><p><? echo rtrim($sep_item,","); ?></p></td>
                                <td width="60" align="center" class="wrd_brk"><p><? echo $unit_of_measurement[$conRow['ORDER_UOM']]; ?></p></td>
                              
                                <td width="80" align="right" class="wrd_brk"><p><? echo number_format($conRow['ATTACHED_RATE'],2); ?></p></td>

                                <td width="100" align="right" class="wrd_brk"><p><? echo number_format($conRow['PO_TOTAL_PRICE'],2); ?></p></td>

                                <td width="100" align="right" class="wrd_brk"><p><? echo number_format($conRow['ATCH_QTY'],2); ?></p></td>

                                <td width="100" align="right" class="wrd_brk"><p><? echo number_format($conRow['ATTACHED_VALUE'],2); ?></p></td>
                                <td align="right" class="wrd_brk"><p><? echo change_date_format($conRow['PUB_SHIPMENT_DATE']); ?></p></td>
                            </tr>
                        </tbody>
                        <?
                        $i++;
                        $total_order_val+=$conRow['PO_TOTAL_PRICE'];
                        $total_atch_qty+=$conRow['ATCH_QTY'];
						$total_order_qty+=$conRow['PO_QUANTITY_PCS'];
						$total_attach_value+=$conRow['ATTACHED_VALUE'];
                    }
                    ?>
                    <tfoot class="tbl_bottom">
                        <tr>
                            <td colspan="6" align="right">Total</td>
                            <td align="right" class="wrd_brk"><p><? echo number_format($total_order_qty,2); ?></p></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td align="right" class="wrd_brk"><p><? echo number_format($total_order_val,2); ?></p></td>
                            <td align="right" class="wrd_brk"><p><? echo number_format($total_atch_qty,2); ?></p></td>
                            <td align="right" class="wrd_brk"><p><? echo number_format($total_attach_value,2); ?></p></td>
                            <td colspan="3" align="right" class="wrd_brk"><p>&nbsp;</p></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    <?
    $r_id6=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form=177");
	oci_commit($con);
	disconnect($con);
    $is_created = fwrite($create_new_doc,ob_get_contents());
    exit();
}

if ($action=="open_summary_pop_up")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	$test = extract($_REQUEST);
	$btb_id=str_replace("'","",$btb_id);
	$buyer=str_replace("'","",$buyer_id);
	$file_no=str_replace("'","",$file_no);
	$cat_wise_budge_val=str_replace("'","",$cat_wise_budge_val);

	if($category_type==1){$category_search=" and e.item_category_id=1 ";}
	else if($category_type==2){$category_search=" and e.item_category_id=4 ";}
	else if($category_type==3){$category_search=" and e.item_category_id in(2,3) ";}
	else if($category_type==4){$category_search=" and e.item_category_id not in(1,2,3,4) ";}
    //echo $btb_id;
	
	?>
    <script>
        function print_window()
        {
            document.getElementById('scroll_body').style.overflow="auto";
            document.getElementById('scroll_body').style.maxHeight="none";
            $(".flt").css("display","none");
            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
            '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title>Total BTB Accepted</title></head><body><div style="width:800px; margin-top:20px;"><? echo ""; ?></div>'+document.getElementById('report_div').innerHTML+'</body</html>');
            d.close();
            document.getElementById('scroll_body').style.overflowY="scroll";
            document.getElementById('scroll_body').style.maxHeight="320px";
            $(".flt").css("display","block");
        }
    </script>
    <div style="width:800px;" align="center">
        <input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp;
        <div id="report_container"> </div>
    </div>
    <?
	ob_start();
    if($dtls_type==1)
    {
        $sql= "SELECT c.lc_number, c.lc_date, c.supplier_id, c.lc_value, c.garments_qty, e.item_category_id, e.net_total_amount, e.pi_number
        from com_btb_lc_master_details c, com_btb_lc_pi d, com_pi_master_details e
        where c.id=d.com_btb_lc_master_details_id and d.pi_id=e.id and c.id in($btb_id) and c.status_active=1 and d.status_active=1 and e.status_active=1  $category_search";
        // echo $sql;
        $sql_result=sql_select($sql);
        ?>
        <div style="width:800px;" id="report_div">
            <table width="780" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="left" rules="all">
                <thead>
                    <tr>
                        <th width="50">SL</th>
                        <th width="110">BTB Lc No</th>
                        <th width="80">Lc Date</th>
                        <th width="80">BTB GMTS QTY[Pcs]</th>
                        <th width="80">Amount</th>
                        <th width="110">PI No.</th>
                        <th width="100">Supplier</th>
                        <th width="100">Item Cetagory</th>
                        <th >PI Qty</th>
                    </tr>
                </thead>
            </table>
            <div style="width:800px; max-height:320px; overflow-y:scroll" id="scroll_body" >
                <table width="780" cellspacing="0" cellpadding="0" id="table_summary_popup" border="1" class="rpt_table" rules="all">
                    <tbody >
                    <?
                    
                    $i=1;
                    foreach($sql_result as $row)
                    {
                        if ($i%2==0) $bgcolor="#E9F3FF";
                        else $bgcolor="#FFFFFF";
                        ?>
                        <tr bgcolor="<?=$bgcolor; ?>">
                            <td width="50"><?=$i; ?></td>
                            <td width="110"><?=$row["LC_NUMBER"]; ?></td>
                            <td width="80"><?=change_date_format($row["LC_DATE"]); ?></td>
                            <td width="80"><?=number_format($row["GARMENTS_QTY"],2); ?></td>
                            <td width="80"><?=number_format($row["LC_VALUE"],2); ?></td>
                            <td width="110"><?=$row["PI_NUMBER"]; ?></td>
                            <td width="100"><?=$suplier_name_arr[$row["SUPPLIER_ID"]]; ?></td>
                            <td width="100"><?=$item_category[$row["ITEM_CATEGORY_ID"]]; ?></td>
                            <td ><?=number_format($row["NET_TOTAL_AMOUNT"],2); ?></td>
                        </tr>
                        <?
                        $i++;
                    }
                    ?>
                    </tbody>
                </table>
            </div>

            <script type="text/javascript">
                setFilterGrid("table_summary_popup",-1);
            </script>
            <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
        </div>
        <?
    }
    if($dtls_type==2)
    {
        $sql= "SELECT a.id as inv_id, a.invoice_no, a.maturity_date, a.invoice_date, c.lc_number, c.supplier_id, b.id as accp_dtls_id, b.current_acceptance_value as inv_amount, e.id as pi_dtls_id, e.item_category_id, e.net_pi_amount
        from com_import_invoice_mst a, com_import_invoice_dtls b, com_btb_lc_master_details c, com_btb_lc_pi d, com_pi_item_details e
        where a.id=b.import_invoice_id and b.btb_lc_id=c.id and c.id=d.com_btb_lc_master_details_id and d.pi_id=e.pi_id and d.pi_id=b.pi_id and b.pi_id=e.pi_id and c.id in($btb_id)and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $category_search";
        //echo $sql;//die;
        $sql_result=sql_select($sql);
        $dtls_data=array();
        foreach($sql_result as $row)
        {
            $dtls_data[$row[csf("inv_id")]]["inv_id"]=$row[csf("inv_id")];
            $dtls_data[$row[csf("inv_id")]]["invoice_no"]=$row[csf("invoice_no")];
            $dtls_data[$row[csf("inv_id")]]["maturity_date"]=$row[csf("maturity_date")];
            $dtls_data[$row[csf("inv_id")]]["invoice_date"]=$row[csf("invoice_date")];
            $dtls_data[$row[csf("inv_id")]]["lc_number"]=$row[csf("lc_number")];
            $dtls_data[$row[csf("inv_id")]]["supplier_id"]=$row[csf("supplier_id")];
            $dtls_data[$row[csf("inv_id")]]["item_category_id"]=$row[csf("item_category_id")];
            
            if($inv_dtls_check[$row[csf("accp_dtls_id")]]=="")
            {
                $inv_dtls_check[$row[csf("accp_dtls_id")]]=$row[csf("accp_dtls_id")];
                $dtls_data[$row[csf("inv_id")]]["inv_amount"]+=$row[csf("inv_amount")];
                $tot_accep_amount+=$row[csf("inv_amount")];
            }
            
            if($pi_dtls_check[$row[csf("pi_dtls_id")]]=="")
            {
                $pi_dtls_check[$row[csf("pi_dtls_id")]]=$row[csf("pi_dtls_id")];
                $tot_btb_amount+=$row[csf("net_pi_amount")];
            }
        }
        ?>
        <div style="width:800px;" id="report_div">
            <table width="780" cellspacing="0" cellpadding="0" border="0" align="left" rules="all">
                <tr>
                    <td colspan="6">&nbsp;</td>
                </tr>
                <tr>
                    <td width="110">File No :</td><td width="150"><? echo $file_no; ?></td>
                    <td width="110">Buyer Name :</td><td width="150"><? echo return_field_value("buyer_name","lib_buyer","id=$buyer","buyer_name"); ?></td>
                    <td width="110">Item Budgeted Value :</td><td><? echo number_format($cat_wise_budge_val,2); ?></td>
                </tr>
                <tr>
                    <td>BTB to be opned :</td><td title="total budget value - total btb open value"><? $btb_tobe_open=$cat_wise_budge_val-$tot_btb_amount; echo number_format($btb_tobe_open,2); ?></td>
                    <td>&nbsp;</td><td>&nbsp;</td>
                    <td>Total BTB Open:</td><td><? echo number_format($tot_btb_amount,2); ?></td>
                </tr>
                <tr>
                    <td>&nbsp;</td><td>&nbsp;</td>
                    <td>Acceptance balance:</td><td title="total btb open value - total acceptance value"><? $btb_balance=$tot_btb_amount-$tot_accep_amount; echo number_format($btb_balance,2); ?></td>
                    <td>&nbsp;</td><td>&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="6">&nbsp;</td>
                </tr>
            </table>
            <table width="780" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="left" rules="all">
                <thead>
                    <tr>
                        <th width="50">SL</th>
                        <th width="110">Invoice No</th>
                        <th width="110">Amount</th>
                        <th width="80">Maturity date</th>
                        <th width="80">Invoice date</th>
                        <th width="110">Lc No</th>
                        <th width="110">Item Cetagory</th>
                        <th >Supplier</th>
                    </tr>
                </thead>
            </table>
            <div style="width:800px; max-height:320px; overflow-y:scroll" id="scroll_body" >
                <table width="780" cellspacing="0" cellpadding="0" id="table_summary_popup" border="1" class="rpt_table" rules="all">
                    <tbody >
                    <?
                    
                    $i=1;
                    foreach($dtls_data as $row)
                    {
                        if ($i%2==0) $bgcolor="#E9F3FF";
                        else $bgcolor="#FFFFFF";
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>">
                            <td  width="50" ><?  echo $i; ?></td>
                            <td width="110"><p style="word-break: break-all;"><?  echo $row["invoice_no"]; ?></p></td>
                            <td align="right" width="110"><?  echo number_format($row["inv_amount"],2); $total_amount+=$row["inv_amount"]; ?></td>
                            <td  align="center" width="80.">
                            <?
                            if ($row["maturity_date"]!='0000-00-00') echo change_date_format($row["maturity_date"]); else echo "";
                            ?>
                            </td>
                            <td  align="center" width="80">
                            <?
                            if ($row["invoice_date"]!='0000-00-00') echo change_date_format($row["invoice_date"]); else echo "";
                            ?>
                            </td>
                            <td width="110"><p style="word-break: break-all;mso-number-format:'\@';"><?  echo $row["lc_number"]; ?></p></td>
                            <td width="110"><?  echo $item_category[$row["item_category_id"]]; ?></td>
                            <td ><?  echo $suplier_name_arr[$row["supplier_id"]]; ?></td>
                        </tr>
                        <?
                        $i++;
                    }
                    ?>
                    </tbody>
                </table>
            </div>
            <table width="780" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all" >
                <tfoot>
                    <tr>
                        <th width="50">&nbsp;</th>
                        <th width="110">Total</th>
                        <th width="110" id="value_total_amnt"><? echo number_format($total_amount,2); ?></th>
                        <th width="80">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="110">&nbsp;</th>
                        <th width="110">&nbsp;</th>
                        <th >&nbsp;</th>
                    </tr>
                </tfoot>
            </table>
            <script type="text/javascript">
                    var tableFilters2 = 
                    {
                        col_operation: {
                        id: ["value_total_amnt"],
                        col: [2],
                        operation: ["sum"],
                        write_method: ["innerHTML"]
                        }
                    }
                setFilterGrid("table_summary_popup",-1,tableFilters2);
                </script>
                <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
        </div>
        <?
    }
    if($dtls_type==3)
    {

        $sql= "SELECT a.id as inv_id, a.invoice_no, a.document_value as PAID, a.maturity_date, a.invoice_date, c.lc_number, c.supplier_id, b.id as accp_dtls_id, b.current_acceptance_value as inv_amount, e.item_category_id, a.bank_acc_date as payment_date
        from com_import_invoice_mst a, com_import_invoice_dtls b, com_btb_lc_master_details c, com_btb_lc_pi d, com_pi_master_details e
        where a.id=b.import_invoice_id and b.btb_lc_id=c.id and c.id=d.com_btb_lc_master_details_id and d.pi_id=e.id and d.pi_id=b.pi_id and b.pi_id=e.id and c.id in($btb_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.payterm_id=1 $category_search
        union all 
        SELECT a.id as inv_id, a.invoice_no, f.accepted_ammount as PAID, a.maturity_date, a.invoice_date, c.lc_number, c.supplier_id, b.id as accp_dtls_id, b.current_acceptance_value as inv_amount, e.item_category_id, f.payment_date as payment_date
        from com_import_invoice_mst a, com_import_invoice_dtls b, com_btb_lc_master_details c, com_btb_lc_pi d, com_pi_master_details e, com_import_payment f
        where a.id=b.import_invoice_id and b.btb_lc_id=c.id and c.id=d.com_btb_lc_master_details_id and d.pi_id=e.id and d.pi_id=b.pi_id and b.pi_id=e.id and c.id in($btb_id) and a.id=f.invoice_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.payterm_id=2 $category_search";
        // echo $sql;
        $sql_result=sql_select($sql);
        ?>
        <div style="width:800px;" id="report_div">
            <table width="780" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="left" rules="all">
                <thead>
                    <tr>
                        <th width="30">SL</th>
                        <th width="90">Invoice No</th>
                        <th width="80">Amount</th>
                        <th width="80">Maturity date</th>
                        <th width="80">Invoice date</th>
                        <th width="100">Lc No</th>
                        <th width="80">Item Cetagory</th>
                        <th width="100">Supplier</th>
                        <th width="80">Paid Amount</th>
                        <th >Paid Date</th>
                    </tr>
                </thead>
            </table>
            <div style="width:800px; max-height:320px; overflow-y:scroll" id="scroll_body" >
                <table width="780" cellspacing="0" cellpadding="0" id="table_summary_popup" border="1" class="rpt_table" rules="all">
                    <tbody >
                    <?
                    
                    $i=1;
                    foreach($sql_result as $row)
                    {
                        if ($i%2==0) $bgcolor="#E9F3FF";
                        else $bgcolor="#FFFFFF";
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>">
                            <td  width="30" ><?  echo $i; ?></td>
                            <td width="90"><p style="word-break: break-all;"><?  echo $row["INVOICE_NO"]; ?></p></td>
                            <td align="right" width="80"><?  echo number_format($row["INV_AMOUNT"],2); $total_amount+=$row["INV_AMOUNT"]; ?></td>
                            <td align="center" width="80.">
                            <? if ($row["MATURITY_DATE"]!='0000-00-00') echo change_date_format($row["MATURITY_DATE"]); else echo "";?>
                            </td>
                            <td align="center" width="80">
                            <?if ($row["INVOICE_DATE"]!='0000-00-00') echo change_date_format($row["INVOICE_DATE"]); else echo "";?>
                            </td>
                            <td width="100"><p style="word-break: break-all;mso-number-format:'\@';"><?  echo $row["LC_NUMBER"]; ?></p></td>
                            <td width="80"><?  echo $item_category[$row["ITEM_CATEGORY_ID"]]; ?></td>
                            <td width="100"><? echo $suplier_name_arr[$row["SUPPLIER_ID"]]; ?></td>
                            <td width="80" align="right" ><? echo number_format($row["PAID"],2); ?></td>
                            <td align="center"><?if ($row["PAYMENT_DATE"]!='0000-00-00') echo change_date_format($row["PAYMENT_DATE"]); else echo "";?></td>
                        </tr>
                        <?
                        $i++;
                    }
                    ?>
                    </tbody>
                </table>
            </div>
            <script type="text/javascript">
            setFilterGrid("table_summary_popup",-1,tableFilters2);
            </script>
            <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
        </div>
        <?
    }
    if($dtls_type==4)
    {
        $sql= "SELECT c.lc_number, c.lc_date, c.supplier_id, c.lc_value, c.garments_qty, e.item_category_id, e.net_total_amount, e.pi_number
        from com_btb_lc_master_details c, com_btb_lc_pi d, com_pi_master_details e
        where c.id=d.com_btb_lc_master_details_id and d.pi_id=e.id and c.id in($btb_id) and c.lc_type_id=3 and c.status_active=1 and d.status_active=1 and e.status_active=1 ";
        // echo $sql;
        $sql_result=sql_select($sql);
        ?>
        <div style="width:800px;" id="report_div">
            <table width="780" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="left" rules="all">
                <thead>
                    <tr>
                        <th width="50">SL</th>
                        <th width="110">BTB Lc No</th>
                        <th width="80">Lc Date</th>
                        <th width="80">BTB GMTS QTY[Pcs]</th>
                        <th width="80">Amount</th>
                        <th width="110">PI No.</th>
                        <th width="100">Supplier</th>
                        <th width="100">Item Cetagory</th>
                        <th >PI Qty</th>
                    </tr>
                </thead>
            </table>
            <div style="width:800px; max-height:320px; overflow-y:scroll" id="scroll_body" >
                <table width="780" cellspacing="0" cellpadding="0" id="table_summary_popup" border="1" class="rpt_table" rules="all">
                    <tbody >
                    <?
                    
                    $i=1;
                    foreach($sql_result as $row)
                    {
                        if ($i%2==0) $bgcolor="#E9F3FF";
                        else $bgcolor="#FFFFFF";
                        ?>
                        <tr bgcolor="<?=$bgcolor; ?>">
                            <td width="50"><?=$i; ?></td>
                            <td width="110"><?=$row["LC_NUMBER"]; ?></td>
                            <td width="80"><?=change_date_format($row["LC_DATE"]); ?></td>
                            <td width="80"><?=number_format($row["GARMENTS_QTY"],2); ?></td>
                            <td width="80"><?=number_format($row["LC_VALUE"],2); ?></td>
                            <td width="110"><?=$row["PI_NUMBER"]; ?></td>
                            <td width="100"><?=$suplier_name_arr[$row["SUPPLIER_ID"]]; ?></td>
                            <td width="100"><?=$item_category[$row["ITEM_CATEGORY_ID"]]; ?></td>
                            <td ><?=number_format($row["NET_TOTAL_AMOUNT"],2); ?></td>
                        </tr>
                        <?
                        $i++;
                    }
                    ?>
                    </tbody>
                </table>
            </div>

            <script type="text/javascript">
                setFilterGrid("table_summary_popup",-1);
            </script>
            <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
        </div>
        <?
    }

	

    $html=ob_get_contents();
    ob_flush();

    foreach (glob("$user_id*.xls") as $filename)
    {
       @unlink($filename);
    }

    //html to xls convert
    $name=time();
    $name=$user_id."_".$name.".xls";
    $create_new_excel = fopen(''.$name, 'w');
    $is_created = fwrite($create_new_excel,$html);
    ?>
    <input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
    <script>
        $(document).ready(function(e) {
            document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
        });
    </script>
    <?
	exit();
}

disconnect($con);
?>
