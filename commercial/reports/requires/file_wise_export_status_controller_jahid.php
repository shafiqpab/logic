<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');


$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];


$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
$lein_bank_arr=return_library_array( "select bank_name,id from lib_bank where is_deleted=0  and status_active=1 and lien_bank=1 order by bank_name",'id','bank_name');
$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer where is_deleted=0  and status_active=1 order by buyer_name",'id','buyer_name');
$suplier_name_arr=return_library_array( "select id,supplier_name from  lib_supplier where is_deleted=0  and status_active=1 order by supplier_name",'id','supplier_name');
$pi_no_arr=return_library_array( "select id,pi_number from  com_pi_master_details where status_active=1",'id','pi_number');


if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 170, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "",0 );
	exit();
}

if ($action=="load_drop_down_lc_year")
{
	$sql="select distinct(lc_year) as lc_sc_year from com_export_lc where beneficiary_name=1 and status_active=1 and is_deleted=0 union all select distinct(sc_year) as lc_sc_year from com_sales_contract where beneficiary_name=1 and status_active=1 and is_deleted=0 and sc_year not in(select distinct(lc_year) as lc_sc_year from com_export_lc where beneficiary_name=1 and status_active=1 and is_deleted=0) order by lc_sc_year";
	//$sql="select distinct(lc_year) as lc_sc_year from com_export_lc where beneficiary_name=$data and status_active=1 and is_deleted=0  union all select distinct(sc_year) as lc_sc_year from com_sales_contract where beneficiary_name=$data and status_active=1 and is_deleted=0";
	echo create_drop_down( "hide_year", 100,$sql,"lc_sc_year,lc_sc_year", 1, "-- Select --", 1,"");
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	 $cbo_based_on=str_replace("'","",$cbo_based_on);
	 $cbo_company_name=str_replace("'","",$cbo_company_name); $cbo_buyer_name=str_replace("'","",$cbo_buyer_name); $cbo_lein_bank=str_replace("'","",$cbo_lein_bank); $txt_file_no=str_replace("'","",$txt_file_no);$hide_year=str_replace("'","",$hide_year);
	 //echo $hide_year;die;
	 //echo $cbo_company_name.'____'.$cbo_buyer_name.'____'.$cbo_lein_bank.'____'.$txt_file_no; die;
	if($cbo_company_name!=0) $cbo_company_name = $cbo_company_name; else $cbo_company_name="%%";
	if($cbo_buyer_name == 0) $cbo_buyer_name="%%"; else $cbo_buyer_name = $cbo_buyer_name;
	if($cbo_lein_bank == 0) $cbo_lein_bank="%%"; else $cbo_lein_bank = $cbo_lein_bank;
	if(trim($txt_file_no)!="") $txt_file_no =$txt_file_no; else $txt_file_no="%%";
	if(trim($hide_year)!="") $hide_year =$hide_year; else $hide_year="%%";
	
	ob_start();
	

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
                    
                    $sc_id_arr=array();
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
						from 
								com_sales_contract 
						where 
								beneficiary_name like '$cbo_company_name' and  buyer_name like '$cbo_buyer_name' and  internal_file_no like '$txt_file_no' and lien_bank like '$cbo_lein_bank' and sc_year like '$hide_year' and converted_from!=0 and is_deleted='0' and status_active='1'
								
						union all 
						
						 select id, buyer_name, export_lc_no as lc_sc_no, lc_value as lc_sc_val, 2 as type 
						 from 
						 		com_export_lc 
						 where 
						 		beneficiary_name like '$cbo_company_name' and  buyer_name like '$cbo_buyer_name' and  internal_file_no like '$txt_file_no' and lien_bank like '$cbo_lein_bank' and lc_year like '$hide_year' and is_deleted='0' and status_active='1' and replacement_lc='1'
						  order by id");
						
                        $sales_contract_id="";$sales_contract_number="";
                        foreach($sales_ref3 as $sales_ref)  // Master Job  table queery ends here
                        {
						if($sales_ref[csf('type')]==1)$sc_id_arr[]=$sales_ref[csf('id')]; else $lc_id_arr[]=$sales_ref[csf('id')];
						
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
	
                        $sales_direct= sql_select("select id, buyer_name, contract_no, contract_value 
						from 
								com_sales_contract 
						where 
								beneficiary_name like '$cbo_company_name' and  buyer_name like '$cbo_buyer_name' and  internal_file_no like '$txt_file_no' and lien_bank like '$cbo_lein_bank' and sc_year like '$hide_year' and convertible_to_lc=2 and is_deleted='0'  and  ( converted_from is null or converted_from=0)  and status_active='1' order by id");
                        foreach($sales_direct as $exp_ref)  // Master Job  table queery ends here
                        {
                        
                            $sc_id_arr[]=$exp_ref[csf('id')];
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
						from 
								com_export_lc 
						where 
								beneficiary_name like '$cbo_company_name' and  buyer_name like '$cbo_buyer_name' and  internal_file_no like '$txt_file_no' and lien_bank like '$cbo_lein_bank' and lc_year like '$hide_year'  and is_deleted='0' and status_active='1' and replacement_lc=2");
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
		//var_dump($lc_id_arr);var_dump($sc_id_arr);die;
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
				if($submision_id_lc_rlz=="") $submision_id_lc_rlz=$row[csf("sub_id")]; else $submision_id_lc_rlz .=",".$row[csf("sub_id")];
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
					if($submit_id_lc==0) $submit_id_lc=$row[csf("sub_id")]; else $submit_id_lc= $submit_id_lc.",".$row[csf("sub_id")];
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
		FROM 
				com_export_doc_submission_mst a, com_export_doc_submission_invo b,  com_export_invoice_ship_mst c,com_export_proceed_realization d
		WHERE 
				b.doc_submission_mst_id=a.id and b.invoice_id=c.id and d.invoice_bill_id=a.id and d.is_invoice_bill='1'  and c.is_lc=1 AND a.id in($submit_id_lc) and  a.status_active=1 and a.is_deleted=0  
				group by d.id , c.is_lc
				order by  c.lc_sc_id,d.id");
		}
		else
		{
		$sql_re=sql_select("SELECT d.id as rlz_id, LISTAGG(CAST( b.invoice_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.invoice_id) as inv_id, b.doc_submission_mst_id as sub_id, LISTAGG(CAST( c.invoice_no  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.invoice_no) as invoice_no, sum(b.net_invo_value) as net_invo_value, a.bank_ref_no , a.submit_date  as bank_ref_date, sum(case when a.submit_type=1 then b.net_invo_value else 0 end) as sub_collection, a.negotiation_date , sum(a.total_negotiated_amount) as total_negotiated_amount, b.lc_sc_id as lc_sc_id, c.is_lc,sum(c.invoice_quantity) as invoice_quantity
		FROM 
				com_export_doc_submission_mst a, com_export_doc_submission_invo b,  com_export_invoice_ship_mst c,com_export_proceed_realization d
		WHERE 
				b.doc_submission_mst_id=a.id and b.invoice_id=c.id and d.invoice_bill_id=a.id and d.is_invoice_bill='1' and c.is_lc=1 AND a.id in($submit_id_lc) and  a.status_active=1 and a.is_deleted=0  
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
		$k = 1;$realize_sc_arr=array();$submit_id_sc=0;//$sc_relize_arr=array();$sc_distributed_arr=array();
		
		if(!empty($sc_id_arr))
		{
			/*if($db_type==0)
			{
			$submision_id_sc_rlz=return_field_value("group_concat(distinct doc_submission_mst_id) as id","com_export_doc_submission_invo","is_lc=2 and lc_sc_id in(".implode(',',$sc_id_arr).")","id");
			
			}
			else if($db_type==2)
			{
			$submision_id_sc_rlz=return_field_value(" LISTAGG(CAST( doc_submission_mst_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY doc_submission_mst_id) as id","com_export_doc_submission_invo","is_lc=2 and lc_sc_id in(".implode(',',$sc_id_arr).")","id");
			$submision_id_sc_rlz=implode(",",array_unique(explode(",",$submision_id_sc_rlz)));
			}*/
			
			$submision_id_sc_rlz="";
			$sub_rlz_sql=sql_select("select doc_submission_mst_id as sub_id from com_export_doc_submission_invo where is_lc=2 and lc_sc_id in(".implode(',',$sc_id_arr).")");
			foreach($sub_rlz_sql as $row)
			{
				if($submision_id_sc_rlz == "") $submision_id_sc_rlz=$row[csf("sub_id")]; else $submision_id_sc_rlz .=",".$row[csf("sub_id")];
			}
			
			
			if($submision_id_sc_rlz!="")
			{
				$submision_id_sc_rlz=array_chunk(array_unique(explode(",",$submision_id_sc_rlz)),999);
				if($db_type==0)
				{
					$sql_rlz_query="select a.id as relz_id, a.invoice_bill_id as sub_id, a.received_date, b.type, b.account_head, b.document_currency, c.lc_sc_id from com_export_proceed_realization a, com_export_proceed_rlzn_dtls b, com_export_doc_submission_invo c where a.id=b.mst_id and c.doc_submission_mst_id=a.invoice_bill_id";
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
					$sql_rlz_query="select a.id as relz_id, a.invoice_bill_id as sub_id, a.received_date, b.type, b.account_head, b.document_currency, c.lc_sc_id from com_export_proceed_realization a, com_export_proceed_rlzn_dtls b, com_export_doc_submission_invo c where a.id=b.mst_id and c.doc_submission_mst_id=a.invoice_bill_id ";
					$p=1;
					foreach($submision_id_sc_rlz as $sub_id_sc_rlz)
					{
						if($p==1) $sql_rlz_query .=" and (a.invoice_bill_id in(".implode(',',$sub_id_sc_rlz).")"; else  $sql_rlz_query .=" or a.invoice_bill_id  in(".implode(',',$sub_id_sc_rlz).")";
						
						$p++;
					}
					$sql_rlz_query .=") and a.is_invoice_bill=1 and a.status_active=1 and a.is_deleted=0 
				group by b.id , b.type, b.account_head, b.document_currency,a.id, a.invoice_bill_id, a.received_date, c.lc_sc_id
				order by c.lc_sc_id,a.id";
				
				}
				//echo $sql_rlz_query."<br>nahid**************<br>";
				$sql_rlz=sql_select($sql_rlz_query);
				
				foreach($sql_rlz as $row)
				{
					if($submit_id_sc==0) $submit_id_sc=$row[csf("sub_id")]; else $submit_id_sc= $submit_id_sc.",".$row[csf("sub_id")];
					$realize_sc_arr[$row[csf('relz_id')]]['sub_id']=$row[csf('sub_id')];
					$realize_sc_arr[$row[csf('relz_id')]]['rlz_received_date']=$row[csf('received_date')];
					
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
		//var_dump($realize_sc_arr);die;
		$submit_id_sc=implode(",",array_unique(explode(",",$submit_id_sc)));
		if($db_type==0)
		{
		$sql_sc=sql_select("SELECT d.id as rlz_id, group_concat(distinct b.invoice_id) as inv_id,group_concat(distinct a.id) as sub_id,  group_concat(distinct c.invoice_no  ) as invoice_no, sum(b.net_invo_value) as net_invo_value, group_concat(distinct a.bank_ref_no) as bank_ref_no, group_concat(distinct a.submit_date) as bank_ref_date, sum(case when a.submit_type=1 then b.net_invo_value else 0 end) as sub_collection, group_concat(distinct a.negotiation_date) as negotiation_date, sum(a.total_negotiated_amount) as total_negotiated_amount, group_concat(distinct c.lc_sc_id) as lc_sc_id, c.is_lc,sum(c.invoice_quantity) as invoice_quantity
		FROM 
				com_export_doc_submission_mst a, com_export_doc_submission_invo b,  com_export_invoice_ship_mst c,com_export_proceed_realization d
		WHERE 
				b.doc_submission_mst_id=a.id and b.invoice_id=c.id and d.invoice_bill_id=a.id and d.is_invoice_bill='1'  and c.is_lc=2 AND a.id in($submit_id_sc) and a.status_active=1 and a.is_deleted=0 
		group by d.id, c.is_lc
		order by b.lc_sc_id,d.id");
		}
		else if($db_type==2)
		{
		$sql_sc=sql_select("SELECT d.id as rlz_id, LISTAGG(CAST( b.invoice_id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.invoice_id) as inv_id,a.id as sub_id,  LISTAGG(CAST( c.invoice_no  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.invoice_no) as invoice_no, sum(b.net_invo_value) as net_invo_value,a.bank_ref_no,a.submit_date, sum(case when a.submit_type=1 then b.net_invo_value else 0 end) as sub_collection,a.negotiation_date, sum(a.total_negotiated_amount) as total_negotiated_amount,b.lc_sc_id as lc_sc_id, c.is_lc,sum(c.invoice_quantity) as invoice_quantity
		FROM 
				com_export_doc_submission_mst a, com_export_doc_submission_invo b,  com_export_invoice_ship_mst c,com_export_proceed_realization d
		WHERE 
				b.doc_submission_mst_id=a.id and b.invoice_id=c.id and d.invoice_bill_id=a.id and d.is_invoice_bill='1'  and c.is_lc=2 AND a.id in($submit_id_sc) and a.status_active=1 and a.is_deleted=0 
		group by d.id,a.id,a.bank_ref_no,a.submit_date,a.negotiation_date,b.lc_sc_id,c.is_lc
		order by b.lc_sc_id,d.id");
		
		}
		
		foreach($sql_sc as $result)
		{
			if($sub_inv_id_sc==0) $sub_inv_id_sc=$result[csf("inv_id")]; else $sub_inv_id_sc=$sub_inv_id_sc.",".$result[csf("inv_id")];
			$submit_inv_arr[]=$result[csf("inv_id")];
			$realize_sc_arr[$result[csf("rlz_id")]]['inv_id']=$result[csf("inv_id")];
			$realize_sc_arr[$result[csf("rlz_id")]]['sub_id']=$result[csf("sub_id")];
			$realize_sc_arr[$result[csf("rlz_id")]]['invoice_no']=$result[csf("invoice_no")];
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
		
		
		$submit_id_lc=implode(",",array_unique(explode(",",$submit_id_lc)));
		$submit_id_sc=implode(",",array_unique(explode(",",$submit_id_sc)));
		$submission_id_lc=0;$submission_id_sc=0;$sub_as_collection=0;
		$sub_lc_arr=array();		
		if(!empty($lc_id_arr))
		{
		
			if($db_type==0)
			{
			$sql_re=sql_select("SELECT group_concat(distinct b.invoice_id) as inv_id,a.id as sub_id, group_concat(distinct c.invoice_no ) as invoice_no, sum(b.net_invo_value) as net_invo_value, a.bank_ref_no, a.bank_ref_date, sum(case when a.submit_type=1 then b.net_invo_value else 0 end) as sub_collection, a.negotiation_date, sum(a.total_negotiated_amount) as total_negotiated_amount, group_concat(distinct c.lc_sc_id) as lc_sc_id, c.is_lc,sum(c.invoice_quantity) as invoice_quantity
			FROM 
			com_export_doc_submission_mst a, com_export_doc_submission_invo b,  com_export_invoice_ship_mst c
			WHERE 
			b.doc_submission_mst_id=a.id and b.invoice_id=c.id and b.is_lc='1' and a.id not in($submit_id_lc) AND b.lc_sc_id in(".implode(',',$lc_id_arr).")  AND a.status_active='1' and a.is_deleted=0 and a.entry_form=40
			group by a.id, a.bank_ref_no, a.bank_ref_date, a.negotiation_date, c.is_lc
			order by c.lc_sc_id,a.id");
			}
			else if($db_type==2)
			{
			$sql_re=sql_select("SELECT LISTAGG(CAST(  b.invoice_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.invoice_id) as inv_id,a.id as sub_id, LISTAGG(CAST(   c.invoice_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.invoice_no) as invoice_no, sum(b.net_invo_value) as net_invo_value, a.bank_ref_no, a.bank_ref_date, sum(case when a.submit_type=1 then b.net_invo_value else 0 end) as sub_collection, a.negotiation_date, sum(a.total_negotiated_amount) as total_negotiated_amount, LISTAGG(CAST(c.lc_sc_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.lc_sc_id) as lc_sc_id, c.is_lc,sum(c.invoice_quantity) as invoice_quantity
			FROM 
			com_export_doc_submission_mst a, com_export_doc_submission_invo b,  com_export_invoice_ship_mst c
			WHERE 
			b.doc_submission_mst_id=a.id and b.invoice_id=c.id and b.is_lc='1' and a.id not in($submit_id_lc) AND b.lc_sc_id in(".implode(',',$lc_id_arr).")  AND a.status_active='1' and a.is_deleted=0  and a.entry_form=40
			group by a.id, a.bank_ref_no, a.bank_ref_date, a.negotiation_date, c.is_lc
			order by a.id");
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
			$sub_lc_arr[$result[csf("sub_id")]]['bank_ref_date']=$result[csf("bank_ref_date")];
			$sub_lc_arr[$result[csf("sub_id")]]['purchase_amount']=$result[csf("purchase_amount")];
			$sub_lc_arr[$result[csf("sub_id")]]['negotiation_date']=$result[csf("negotiation_date")];
			$sub_lc_arr[$result[csf("sub_id")]]['total_negotiated_amount']=$result[csf("total_negotiated_amount")];
			$sub_lc_arr[$result[csf("sub_id")]]['lc_sc_id']=$result[csf("lc_sc_id")];
			$sub_lc_arr[$result[csf("sub_id")]]['is_lc']=$result[csf("is_lc")];
			
			$sub_as_collection+=($result[csf("net_invo_value")]);
		}
		
		$sub_sc_arr=array();
		if(!empty($sc_id_arr))
		{
			// LISTAGG(CAST( b.id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.id) as tr_id
			if($db_type==0)
			{
			$sql_sc=sql_select("SELECT group_concat(distinct b.invoice_id) as inv_id,a.id as sub_id, group_concat(distinct c.invoice_no) as invoice_no, sum(b.net_invo_value) as net_invo_value, a.bank_ref_no, a.bank_ref_date, sum(case when a.submit_type=1 then b.net_invo_value else 0 end) as sub_collection, a.negotiation_date, sum(a.total_negotiated_amount) as total_negotiated_amount,  group_concat(distinct c.lc_sc_id) as lc_sc_id, c.is_lc,sum(c.invoice_quantity) as invoice_quantity, group_concat(distinct c.id) as sub_invoice_id
			FROM com_export_doc_submission_mst a, com_export_doc_submission_invo b,  com_export_invoice_ship_mst c
			WHERE b.doc_submission_mst_id=a.id and b.invoice_id=c.id  and b.is_lc='2' and a.id not in($submit_id_sc) AND b.lc_sc_id in(".implode(',',$sc_id_arr).") and a.company_id='$cbo_company_name' AND a.status_active='1' and a.is_deleted=0 and a.entry_form=40
			group by a.id, a.bank_ref_no, a.bank_ref_date, a.negotiation_date, c.is_lc
			order by c.lc_sc_id,a.id");
			}
			else if($db_type==2)
			{
			$sql_sc=sql_select("SELECT LISTAGG(CAST( b.invoice_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.invoice_id) as inv_id,a.id as sub_id, LISTAGG(CAST(c.invoice_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.invoice_no)  as invoice_no, sum(b.net_invo_value) as net_invo_value, a.bank_ref_no, a.bank_ref_date, sum(case when a.submit_type=1 then b.net_invo_value else 0 end) as sub_collection, a.negotiation_date, sum(a.total_negotiated_amount) as total_negotiated_amount,  LISTAGG(CAST( c.lc_sc_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.lc_sc_id) as lc_sc_id, c.is_lc,sum(c.invoice_quantity) as invoice_quantity, LISTAGG(CAST( c.id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.id) as sub_invoice_id
			FROM com_export_doc_submission_mst a, com_export_doc_submission_invo b,  com_export_invoice_ship_mst c
			WHERE b.doc_submission_mst_id=a.id and b.invoice_id=c.id  and b.is_lc='2' and a.id not in($submit_id_sc) AND b.lc_sc_id in(".implode(',',$sc_id_arr).") and a.company_id='$cbo_company_name' AND a.status_active='1' and a.is_deleted=0 and a.entry_form=40
			group by a.id, a.bank_ref_no, a.bank_ref_date, a.negotiation_date, c.is_lc
			order by a.id");
			}
		}
		
		/*echo "SELECT LISTAGG(CAST( b.invoice_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.invoice_id) as inv_id,a.id as sub_id, LISTAGG(CAST(c.invoice_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.invoice_no)  as invoice_no, sum(b.net_invo_value) as net_invo_value, a.bank_ref_no, a.bank_ref_date, sum(case when a.submit_type=1 then b.net_invo_value else 0 end) as sub_collection, a.negotiation_date, sum(a.total_negotiated_amount) as total_negotiated_amount,  LISTAGG(CAST( c.lc_sc_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.lc_sc_id) as lc_sc_id, c.is_lc,sum(c.invoice_quantity) as invoice_quantity, LISTAGG(CAST( c.id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.id) as sub_invoice_id
			FROM com_export_doc_submission_mst a, com_export_doc_submission_invo b,  com_export_invoice_ship_mst c
			WHERE b.doc_submission_mst_id=a.id and b.invoice_id=c.id  and b.is_lc='2' and a.id not in($submit_id_sc) AND b.lc_sc_id in(".implode(',',$sc_id_arr).") and a.company_id='$cbo_company_name' AND a.status_active='1' and a.is_deleted=0 
			group by a.id ,a.bank_ref_no, a.bank_ref_date, a.negotiation_date, c.is_lc
			order by c.lc_sc_id,a.id";die;*/
			
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
				$sql_buyer_sub_sc=sql_select("SELECT a.id as sub_id, a.buyer_id, a.submit_date, group_concat(distinct b.invoice_id) as inv_id, sum(b.net_invo_value) as net_invo_value, group_concat(distinct c.invoice_no) as invoice_no, c.lc_sc_id as lc_sc_id, sum(c.invoice_quantity) as invoice_quantity
				FROM com_export_doc_submission_mst a, com_export_doc_submission_invo b,  com_export_invoice_ship_mst c
				WHERE b.doc_submission_mst_id=a.id and b.invoice_id=c.id and b.is_converted=0   and b.is_lc='2' AND b.lc_sc_id in(".implode(',',$sc_id_arr).") and a.company_id='$cbo_company_name' AND a.status_active='1' and a.is_deleted=0 AND b.status_active='1' and b.is_deleted=0 and a.entry_form=39  
				group by a.id, a.buyer_id, a.submit_date, c.lc_sc_id order by a.id");
			}
			else if($db_type==2)
			{
				$sql_buyer_sub_sc=sql_select("SELECT a.id as sub_id, a.buyer_id, a.submit_date, LISTAGG(CAST( b.invoice_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.invoice_id) as inv_id, sum(b.net_invo_value) as net_invo_value, LISTAGG(CAST(c.invoice_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.invoice_no)  as invoice_no, c.lc_sc_id as lc_sc_id, sum(c.invoice_quantity) as invoice_quantity
				FROM com_export_doc_submission_mst a, com_export_doc_submission_invo b,  com_export_invoice_ship_mst c
				WHERE b.doc_submission_mst_id=a.id and b.invoice_id=c.id and b.is_converted=0 and b.is_lc='2' AND b.lc_sc_id in(".implode(',',$sc_id_arr).") and a.company_id='$cbo_company_name' AND a.status_active='1' and a.is_deleted=0 AND b.status_active='1' and b.is_deleted=0 and a.entry_form=39 
				group by a.id, a.buyer_id, a.submit_date, c.lc_sc_id order by a.id");
			}
		}
		
		
		/*echo "SELECT LISTAGG(CAST( b.invoice_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.invoice_id) as inv_id,a.id as sub_id, LISTAGG(CAST(c.invoice_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.invoice_no)  as invoice_no, sum(b.net_invo_value) as net_invo_value, a.bank_ref_no, a.bank_ref_date, sum(case when a.submit_type=1 then b.net_invo_value else 0 end) as sub_collection, a.negotiation_date, sum(a.total_negotiated_amount) as total_negotiated_amount,  LISTAGG(CAST( c.lc_sc_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.lc_sc_id) as lc_sc_id, c.is_lc,sum(c.invoice_quantity) as invoice_quantity, LISTAGG(CAST( c.id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.id) as sub_invoice_id
			FROM com_export_doc_submission_mst a, com_export_doc_submission_invo b,  com_export_invoice_ship_mst c
			WHERE b.doc_submission_mst_id=a.id and b.invoice_id=c.id  and b.is_lc='2' and a.id not in($submit_id_sc) AND b.lc_sc_id in(".implode(',',$sc_id_arr).") and a.company_id='$cbo_company_name' AND a.status_active='1' and a.is_deleted=0 
			group by a.id ,a.bank_ref_no, a.bank_ref_date, a.negotiation_date, c.is_lc
			order by c.lc_sc_id,a.id";die;*/
			
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
					$sql_lc=sql_select("select id, invoice_no, invoice_date,net_invo_value as invoice_value, lc_sc_id, is_lc from com_export_invoice_ship_mst  where is_lc=1 and lc_sc_id in(".implode(',',$lc_id_arr).") and id not in($sub_inv_id_lc) and  status_active=1 and is_deleted=0" );
				}
				else if($db_type==0)
				{
					$sql_lc=sql_select("select id, invoice_no, invoice_date,net_invo_value as invoice_value, lc_sc_id, is_lc from com_export_invoice_ship_mst  where is_lc=1 and lc_sc_id in(".implode(',',$lc_id_arr).") and id not in($sub_inv_id_lc) and  status_active=1 and is_deleted=0" );
				}
				foreach($sql_lc as $row)
				{
					$inv_arr[$row[csf("id")]]["id_lc"]=$row[csf("id")];
					$inv_arr[$row[csf("id")]]["invoice_no"]=$row[csf("invoice_no")];
					$inv_arr[$row[csf("id")]]["invoice_date"]=$row[csf("invoice_date")];
					$inv_arr[$row[csf("id")]]["invoice_value"]=$row[csf("invoice_value")];
					$inv_arr[$row[csf("id")]]["lc_sc_id"]=$row[csf("lc_sc_id")];
					$inv_arr[$row[csf("id")]]["is_lc"]=$row[csf("is_lc")];
					$in_hand +=$row[csf("invoice_value")];
				}
			}
			
			if(!empty($sc_id_arr))
			{
				if($db_type==2)
				{
					$sql_lc=sql_select("select id, invoice_no, invoice_date,net_invo_value as invoice_value, lc_sc_id, is_lc from com_export_invoice_ship_mst  where is_lc=2 and lc_sc_id in(".implode(',',$sc_id_arr).") and id not in($sub_inv_id_sc)  and  status_active=1 and is_deleted=0");
				}
				else if($db_type==0)
				{
					$sql_lc=sql_select("select id, invoice_no, invoice_date,net_invo_value as invoice_value, lc_sc_id, is_lc from com_export_invoice_ship_mst  where is_lc=2 and lc_sc_id in(".implode(',',$sc_id_arr).") and id not in($sub_inv_id_sc)  and  status_active=1 and is_deleted=0");
				}
				foreach($sql_lc as $row)
				{
					$inv_arr[$row[csf("id")]]["id_sc"]=$row[csf("id")];
					$inv_arr[$row[csf("id")]]["invoice_no"]=$row[csf("invoice_no")];
					$inv_arr[$row[csf("id")]]["invoice_date"]=$row[csf("invoice_date")];
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
                    <td width="115" align="right">     		
                        
                    </td>
                    <td width="115" style="font-weight:bold;" align="right">&nbsp;&nbsp;
						<?
							$file_value=$total_sales_contct_value_top+$balance+$total_direct_sc_val+$total_direct_lc_val;
							echo number_format($file_value,2);//."_____".$lc_id."_____".$sc_id
                        ?>
                    </td>
                </tr>
                 <tr>
                    <td>Total Shipment</td>
                    <td align="right">     		
					
                    </td>
                    <td align="right" style="font-weight:bold">&nbsp;&nbsp;
                    <?
					
					//var_dump($lc_id_arr);var_dump($sc_id_arr);
					$adjustment_arr=array();
					if(!empty($lc_id_arr))
					{
						if($db_type==0)
						{
							$sql_lc=sql_select("SELECT b.export_lc_no, a.id as id, a.invoice_no, a.invoice_quantity as current_invoice_qnty, a.invoice_value as current_invoice_value, a.net_invo_value 
							from 
									com_export_invoice_ship_mst a, com_export_lc b
							where 
									a.lc_sc_id=b.id and a.lc_sc_id in(". implode(',',$lc_id_arr).") and a.is_lc=1 and a.status_active=1 and benificiary_id like '$cbo_company_name' and  buyer_id like '$cbo_buyer_name'");
						}
						else if($db_type==2)
						{
							$sql_lc=sql_select("SELECT b.export_lc_no, a.id as id, a.invoice_no, a.invoice_quantity as current_invoice_qnty, a.invoice_value as current_invoice_value, a.net_invo_value 
						from 
								com_export_invoice_ship_mst a, com_export_lc b
						where 
								a.lc_sc_id=b.id and a.lc_sc_id in(". implode(',',$lc_id_arr).") and a.is_lc=1 and a.status_active=1 and benificiary_id like '$cbo_company_name' and  buyer_id like '$cbo_buyer_name'");
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
							$adjustment_arr[$row_lc_result[csf("id")]]["current_invoice_value"]=$row_lc_result[csf("current_invoice_value")];
							$adjustment_arr[$row_lc_result[csf("id")]]["net_invo_value"]=$row_lc_result[csf("net_invo_value")];
						}
					}
					
					if(!empty($sc_id_arr))
					{
						if($db_type==0)
						{
							$sql_lc=sql_select("SELECT contract_no, a.id as id, a.invoice_no, a.invoice_quantity as current_invoice_qnty, a.invoice_value as current_invoice_value, a.net_invo_value
							from 
									com_export_invoice_ship_mst a, com_sales_contract b
							where 
									a.lc_sc_id=b.id and a.lc_sc_id in(".implode(',',$sc_id_arr).") and a.is_lc=2 and a.status_active=1 and benificiary_id like '$cbo_company_name' and  buyer_id like '$cbo_buyer_name'");
						}
						else if($db_type==2)
						{
							$sql_lc=sql_select("SELECT contract_no, a.id as id, a.invoice_no, a.invoice_quantity as current_invoice_qnty, a.invoice_value as current_invoice_value, a.net_invo_value
						from 
								com_export_invoice_ship_mst a, com_sales_contract b
						where 
								a.lc_sc_id=b.id and a.lc_sc_id in(".implode(',',$sc_id_arr).") and a.is_lc=2 and a.status_active=1 and benificiary_id like '$cbo_company_name' and  buyer_id like '$cbo_buyer_name'");
						}
						foreach($sql_lc as $row_sc_result)
						{
							$shp_inv_id[]=$row_sc_result[csf("id")];
							$total_shipment_val += $row_sc_result[csf("current_invoice_value")];
							$total_discount += ($row_sc_result[csf("current_invoice_value")]-$row_sc_result[csf("net_invo_value")]);
							
							$adjustment_arr[$row_sc_result[csf("id")]]["id"]=$row_sc_result[csf("id")];
							$adjustment_arr[$row_sc_result[csf("id")]]["lc_sc_no"]=$row_sc_result[csf("lc_sc_no")];
							$adjustment_arr[$row_sc_result[csf("id")]]["invoice_no"]=$row_sc_result[csf("invoice_no")];
							$adjustment_arr[$row_sc_result[csf("id")]]["current_invoice_value"]=$row_sc_result[csf("current_invoice_value")];
							$adjustment_arr[$row_sc_result[csf("id")]]["net_invo_value"]=$row_sc_result[csf("net_invo_value")];
						}
					
					}
                    echo number_format($total_shipment_val,2,".",",");
					
					//var_dump($adjustment_arr);die;
                    ?>
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
							if(is_nan($st_ex_ship_perc)){ echo $st_ex_ship_perc=0;} else { $st_ex_ship_perc;}
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
             </table>
            </td>
            <td style="vertical-align:top" width="450">
            <table width="400" style="vertical-align:top">	     	
                <tr>
                    <td width="220">Total BTB Opened</td>
                    <td width="100" align="right" style="font-weight:bold;">
                        <?
						if(empty($lc_id_arr)) $lc_id_arr=0;  else $lc_id_arr=implode(',',$lc_id_arr);
						if(empty($sc_id_arr)) $sc_id_arr=0;  else $sc_id_arr=implode(',',$sc_id_arr);
						
						if($db_type==0)
						{
							$btb_mst_lc_id=return_field_value("group_concat(distinct import_mst_id) as import_mst_id"," com_btb_export_lc_attachment","lc_sc_id in($lc_id_arr) and is_lc_sc=0 and status_active=1 and is_deleted=0","import_mst_id");
							//echo "select group_concat(distinct import_mst_id) as import_mst_id from com_btb_export_lc_attachment where lc_sc_id in($sc_id_arr) and is_lc_sc=1  and status_active=1 and is_deleted=0";
							$btb_mst_sc_id=return_field_value("group_concat(distinct import_mst_id) as import_mst_id","com_btb_export_lc_attachment","lc_sc_id in($sc_id_arr) and is_lc_sc=1  and status_active=1 and is_deleted=0","import_mst_id");
						}
						else if($db_type==2)
						{
							$btb_mst_lc_id=return_field_value("LISTAGG(CAST(import_mst_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY import_mst_id) as import_mst_id"," com_btb_export_lc_attachment","lc_sc_id in($lc_id_arr) and is_lc_sc=0  and status_active=1 and is_deleted=0","import_mst_id");
							$btb_mst_sc_id=return_field_value("LISTAGG(CAST(import_mst_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY import_mst_id) as import_mst_id","com_btb_export_lc_attachment","lc_sc_id in($sc_id_arr) and is_lc_sc=1  and status_active=1 and is_deleted=0","import_mst_id");
						}
						if($btb_mst_lc_id=="") $btb_mst_lc_id=0;
						if($btb_mst_sc_id=="") $btb_mst_sc_id=0;
						
						$mst_id=$btb_mst_lc_id.",".$btb_mst_sc_id;
						$sort_val=(explode(",",$mst_id));
						asort($sort_val);
						$mst_id=implode(",",$sort_val);
						//echo $lc_id_arr;die;
						
						/*echo "select 
						a.id, 
						sum(a.lc_value) as lc_value, 
						max(case when a.payterm_id=1 THEN a.id else 0 end) as at_sight_btb_lc_id,
						max(case when a.payterm_id=2 THEN a.id else 0 end) as usance_btb_lc_id,
						max(case when a.payterm_id=3 THEN a.id else 0 end) as cash_btb_lc_id,
						sum(case when a.payterm_id=3 THEN a.lc_value else 0 end) as cash_in_advance
						from
								com_btb_lc_master_details a
						where
								 a.id in($mst_id) group by a.id";die;*/
						
						$sql_btb=sql_select("select 
						a.id, 
						sum(a.lc_value) as lc_value, 
						max(case when a.payterm_id=1 THEN a.id else 0 end) as at_sight_btb_lc_id,
						max(case when a.payterm_id=2 THEN a.id else 0 end) as usance_btb_lc_id,
						max(case when a.payterm_id=3 THEN a.id else 0 end) as cash_btb_lc_id,
						sum(case when a.payterm_id=3 THEN a.lc_value else 0 end) as cash_in_advance
						from
								com_btb_lc_master_details a
						where
								 a.id in($mst_id) group by a.id");
						$btb_id=0;
						foreach($sql_btb as $row)
						{
							if($row[csf("id")]!=""){ if($btb_id==0) $btb_id=$row[csf("id")]; else $btb_id=$btb_id.",".$row[csf("id")];}
							$btb_open_value +=$row[csf("lc_value")];
							$at_sight_lc_id .=$row[csf("at_sight_btb_lc_id")].",";
							$usance_lc_id .=$row[csf("usance_btb_lc_id")].",";
							$cash_lc_id .=$row[csf("cash_btb_lc_id")].",";
							$cash_in_advance +=$row[csf("cash_in_advance")];
						}
						$atsite_accep_id=substr($at_sight_lc_id, 0, -1);
						$usance_paid_lc_id=substr($usance_lc_id, 0, -1);
						$cash_lc_id=substr($cash_lc_id, 0, -1);
						//echo $cash_in_advance;die;
						
							
                        ?>
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
                    <td align="right" style="font-weight:bold;">
                        <?
						if($btb_id=="") $btb_id=0;
						if($atsite_accep_id=="") $atsite_accep_id=0;
						//echo $btb_id;die;
						//echo "select sum(current_acceptance_value) as current_acceptance_value from com_import_invoice_dtls where btb_lc_id in($btb_id) group by btb_lc_id";die;
						$bill_accepted=return_field_value("sum(current_acceptance_value) as current_acceptance_value","com_import_invoice_dtls","btb_lc_id in($btb_id) and status_active=1 and is_deleted=0","current_acceptance_value");
						
						$atsite_accepted=return_field_value("sum(current_acceptance_value) as current_acceptance_value","com_import_invoice_dtls","btb_lc_id in($atsite_accep_id) and status_active=1 and is_deleted=0","current_acceptance_value");
						
						
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
                    <td align="right" style="font-weight:bold;">
                    <?
					if($cash_lc_id=="") $cash_lc_id=0;
					if($usance_paid_lc_id=="") $usance_paid_lc_id=0;
					//echo "select sum(accepted_ammount) as paid from com_import_payment where lc_id in($usance_paid_lc_id) and  payment_head=40 and status_active='1'";die;
					$paid2=return_field_value("sum(accepted_ammount) as paid"," com_import_payment","lc_id in($usance_paid_lc_id) and  payment_head=40 and status_active='1'","paid");
					$paid=($paid2+$atsite_accepted+$cash_in_advance);
					$cash_lc_id=implode(",",array_unique(explode(",",$cash_lc_id)));
					$atsite_accep_id=implode(",",array_unique(explode(",",$atsite_accep_id)));
					$usance_paid_lc_id=implode(",",array_unique(explode(",",$usance_paid_lc_id)));
					$paid_all_id=$cash_lc_id."_".$atsite_accep_id."_".$usance_paid_lc_id;
					
                     ?>
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
                        <?
                            $yet_accept= $btb_open_value-$bill_accepted;
                            echo number_format($yet_accept,2);

                        ?>
                     </td>      		
                    <td>&nbsp;</td>
                </tr>
                <tr>  
                    <td>BTB Margin A/C Balance</td>
                    <td align="right" style="font-weight:bold;"> 
                        <?
                            $total_btb_margine_balance=$total_btb_margine-$paid;//$total_btb_margine
                            echo number_format($total_btb_margine_balance,2);

                        ?>
                     </td>      		
                    <td>&nbsp;</td>
                </tr>
             </table>
            </td>
        </tr>
    </table>
    <? //var_dump($sc_id_arr);echo "<br>___________<br>"; var_dump($lc_id_arr);echo "<br>___________<br>"; ?>
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
                            <?
                            $export_number_rlz=return_field_value("export_lc_no","com_export_lc","id='".$val[('lc_sc_id')]."' and status_active=1","export_lc_no");
                            echo "Export L/C No."." - ".$export_number_rlz; 
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
						<td width="85" align="right">
						<?
						echo number_format($val[('sub_collection')],2);
						 

						?>
                        </td>
						<td width="90" align="right"><? if($purchase_amt) echo number_format($val[('net_invo_value')],2); else echo "0.00";?></td>
						<td width="90" align="right">
						<? 
						
						echo number_format($purchase_amt,2); 
						
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
				
				if ($i%2==0)
				$bgcolor="#E9F3FF";
				else
				$bgcolor="#FFFFFF";
				?>
				
                    
                    <tr  bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td ><? echo $j;?></td>
                    <td width="120" ><p><? echo $val[('invoice_no')];?></p></td>
                    <td width="140" ><? echo $val[('bank_ref_no')];?></td>
                    <td width="80" align="center"><? if($val[ 'bank_ref_date']!='0000-00-00') echo $val[ 'bank_ref_date']; else echo "00-00-0000"; ?></td>
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
    
    <table width="1070" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all" id="" align="left">
        <thead>
            <tr>
                <th  rowspan="2">Sl</th>
                <th width="130" rowspan="2">Invoice No.</th>
                <th width="150" rowspan="2">Export Bill No.</th>
                <th width="90" rowspan="2">Bill Date</th>
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
    
    <table width="1070" rules="all" class="rpt_table" align="left" id="" border="1">
        <tbody>
			<? 
				
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
                        <td colspan="11" style="background-color:#FDF4EF"><b>
                        <?
                        if($val[('is_lc')]==2) $export_number=return_field_value("contract_no","com_sales_contract","id='".$val[('lc_sc_id')]."' and status_active=1");
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
                    <td ><? echo $m;?></td>
                    <td width="130" align="center"><p><? echo $val[('invoice_no')];?></p></td>
                    <td width="150" align="center"><? echo $val[('bank_ref_no')];?></td>
                    <td width="90" align="center"><? echo change_date_format($val[('bank_ref_date')]);?></td>
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
    
    <table width="1070" rules="all" border="1" class="rpt_table" align="left" style="margin-bottom:20px;">
        <tfoot>
            <tr align="right">
                <th colspan="4"><b>Grand Total</b></th>
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
                    <td width="400" ><p><? echo $val[('invoice_no')];?></p></td>
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
    
    <table width="700" cellpadding="0" cellspacing="0" border="1" rules="all" align="left" class="rpt_table">
        <thead>
            <tr>
            	<th width="50">SL</th>
                <th width="170">LC/SC No</th>
                <th width="180">Invoice No</th>
                <th width="150">Invoice Date</th>
                <th width="150">Invoice Value</th>	   			
            </tr>
        </thead>
        
        <? 
        $lc_no_arr=return_library_array( "select id,export_lc_no from com_export_lc where status_active=1",'id','export_lc_no');
        $sc_no_arr=return_library_array( "select id,contract_no from com_sales_contract where status_active=1",'id','contract_no');
      
        //var_dump($sql);
		$k=1;
        foreach($inv_arr as $row)
        {
			if ($i%2==0)
			$bgcolor="#E9F3FF";
			else
			$bgcolor="#FFFFFF";
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
            	<td width="50" align="center"><? echo $k;?></td>
				<td width="170"><? if($row[('is_lc')]==1) echo $lc_no_arr[$row[('lc_sc_id')]]; else echo  $sc_no_arr[$row[('lc_sc_id')]]?></td>
				<td width="180"><? echo $row[('invoice_no')]?></td>
				<td width="150" align="center"><? echo change_date_format($row[('invoice_date')]);?></td>
				<td  align="right"><? echo number_format($row[('invoice_value')],2);?></td>
			</tr>
			<?
			$total_unsubmit+=$row[('invoice_value')];
			$k++;$i++;
        }
        ?>
        <tr align="right" bgcolor="<? echo "#E9F3FF"; ?>">
            <td colspan="4"><strong>Total</strong></td>
            <td><strong><? echo number_format($total_unsubmit,2)?></strong></td>
        </tr>
    </table>
    
    
    <table border="0" width="1900"><tr>&nbsp;</tr></table><br>
    
    <table width="1900" cellpadding="0" cellspacing="0" border="0" rules="all" id="" align="left">
        <tr>
        <td><strong style="font:Arial, Helvetica, sans-serif; font-size:16px;">Discount/Adjustment Details:</strong></td>
        </tr>
    </table>
    <table width="700" cellpadding="0" cellspacing="0" border="1" rules="all" align="left" class="rpt_table">
        <thead>
            <tr>
                <th width="50">Sl</th>
                <th width="150">LC/SC No</th>
                <th width="150">Invoice No</th>
                <th width="120">Gross Value</th>
                <th width="120">Adjustment</th>
                <th >Net Value</th>	   			
            </tr>
        </thead>
        
        <tbody>
        
        <? 
        //$lc_no_arr=return_library_array( "select id,export_lc_no from com_export_lc where status_active=1",'id','export_lc_no');
        //$sc_no_arr=return_library_array( "select id,contract_no from com_sales_contract where status_active=1",'id','contract_no');
      
        //var_dump($sql);
		$p=1;
        foreach($adjustment_arr as $row)
        {
			if ($i%2==0)
			$bgcolor="#E9F3FF";
			else
			$bgcolor="#FFFFFF";
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
				<td align="center"><? echo $p;?></td>
				<td><? echo $row['lc_sc_no']?></td>
                <td><? echo $row['invoice_no']?></td>
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
                <th colspan="3"><strong>Total</strong></th>
                <th><? echo number_format($to_adjst_gross,2)?></th>
                <th><? echo number_format($to_adjust,2)?></th>
                <th><? echo number_format($to_adjust_net,2)?></th>
            </tr>
        </tfoot>
            
    </table>
    
</fieldset>
</div>
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
		
		$sql .= "FROM  
					com_export_proceed_rlzn_dtls a, com_export_proceed_realization b 
				WHERE 
					a.mst_id = b.id AND a.type = '1' AND b.is_invoice_bill = 1 AND b.invoice_bill_id  in($sub_id) and a.account_head not in(5,6,10,15,65,81,11) group by b.invoice_bill_id";
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
						<td ><? echo  $row[csf("sub_val_$ac_val")]; ?></td>
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


}





if ($action=="load_drop_down_search")
{
	$data=explode('_',$data);
	if($data[1]==1) echo '<input type="text" name="txt_search_common" id="txt_search_common" class="text_boxes" style="width:160px" autocomplete=off />';
	if($data[1]==2) echo create_drop_down( "txt_search_common", 170, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90))  $buyer_cond order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" ,0);
	if($data[1]==3) echo create_drop_down( "txt_search_common", 170, "select bank_name,id from lib_bank where is_deleted=0  and status_active=1 and lien_bank=1 order by bank_name","id,bank_name", 1, "-- All Lein Bank --", $selected, "",0,"" );
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
                <th id="search_by_td_up">Enter File No</th>
                <th> 
                <input type="hidden" name="txt_company_id" id="txt_company_id" value="<?  echo $company_id; ?>"/> 
                <input type="hidden" name="txt_buyer_id" id="txt_buyer_id" value="<?  echo $buyer_id; ?>"/>
                <input type="hidden" name="txt_lien_bank_id" id="txt_lien_bank_id" value="<?  echo $lien_bank; ?>"/> 
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
					$sarch_by_arr=array(1=>"File No",2=>"Buyer",3=>"Lien Bank"); 
					echo create_drop_down( "cbo_search_by", 130,$sarch_by_arr,"", 1, "-- Select Search --", 1,"load_drop_down( 'file_wise_export_import_status_controller',document.getElementById('txt_company_id').value+'_'+this.value, 'load_drop_down_search', 'search_by_td' );set_caption(this.value)");
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

	if($txt_search_common==0)$txt_search_common="";
	
	if($txt_search_common!="" && $cbo_search_by==1)
	{
		
		$sql="select beneficiary_name, internal_file_no, lien_bank, buyer_name, lc_year as lc_sc_year from com_export_lc where beneficiary_name='$company_id' and internal_file_no like '%$txt_search_common%' and status_active=1 and is_deleted=0 $buy_query $lien_bank_id $year_cond_lc  group by internal_file_no, lc_year, beneficiary_name, buyer_name , lien_bank
		union all
		select beneficiary_name, internal_file_no, lien_bank, buyer_name, sc_year as lc_sc_year from com_sales_contract where internal_file_no not in(select internal_file_no from com_export_lc where beneficiary_name='$company_id' and status_active=1 and is_deleted=0 and internal_file_no like '%$txt_search_common%' $year_cond_lc) and beneficiary_name='$company_id' and status_active=1 and is_deleted=0 and internal_file_no like '%$txt_search_common%' $buy_query $lien_bank_id $year_cond_sc group by internal_file_no, sc_year, beneficiary_name, buyer_name , lien_bank";
	}
	else if($txt_search_common!="" && $cbo_search_by==2)
	{
		
		$sql="select beneficiary_name, internal_file_no, lien_bank, buyer_name, lc_year as lc_sc_year from com_export_lc where beneficiary_name='$company_id' and buyer_name='$txt_search_common' and status_active=1 and is_deleted=0 $lien_bank_id  $year_cond_lc  group by internal_file_no, lc_year, beneficiary_name, buyer_name , lien_bank
		union all
		select beneficiary_name, internal_file_no, lien_bank, buyer_name, sc_year as lc_sc_year from com_sales_contract where internal_file_no not in(select internal_file_no from com_export_lc where beneficiary_name='$company_id' and status_active=1 and is_deleted=0 and buyer_name='$txt_search_common' $year_cond_lc) and beneficiary_name='$company_id' and status_active=1 and is_deleted=0 and buyer_name='$txt_search_common' $lien_bank_id $year_cond_sc group by internal_file_no, sc_year, beneficiary_name, buyer_name , lien_bank";
	}
	else if($txt_search_common!="" && $cbo_search_by==3)
	{
		//echo $txt_search_common; die;
		
		$sql="select beneficiary_name, internal_file_no, lien_bank, buyer_name, lc_year as lc_sc_year from com_export_lc where beneficiary_name='$company_id' and lien_bank='$txt_search_common' and status_active=1 and is_deleted=0 $buy_query  $year_cond_lc  group by internal_file_no, lc_year, beneficiary_name, buyer_name , lien_bank
		union all
		select beneficiary_name, internal_file_no, lien_bank, buyer_name, sc_year as lc_sc_year from com_sales_contract where internal_file_no not in(select internal_file_no from com_export_lc where beneficiary_name='$company_id' and status_active=1 and is_deleted=0 and lien_bank='$txt_search_common'  $year_cond_lc) and beneficiary_name='$company_id' and status_active=1 and is_deleted=0 and lien_bank='$txt_search_common' $buy_query $year_cond_sc  group by internal_file_no, sc_year, beneficiary_name, buyer_name , lien_bank";
	}
	else
	{
		$sql="select beneficiary_name, internal_file_no, lien_bank, buyer_name , lc_year as lc_sc_year from com_export_lc where beneficiary_name='$company_id' and status_active=1 and is_deleted=0 $buy_query $lien_bank_id  $year_cond_lc group by internal_file_no, lc_year, beneficiary_name, buyer_name , lien_bank
		union all
		select beneficiary_name, internal_file_no, lien_bank, buyer_name, sc_year as lc_sc_year from com_sales_contract where internal_file_no not in(select internal_file_no from com_export_lc where beneficiary_name='$company_id' and status_active=1 and is_deleted=0  $year_cond_lc) and beneficiary_name='$company_id' and status_active=1 and is_deleted=0 $buy_query $lien_bank_id $year_cond_sc group by internal_file_no, sc_year, beneficiary_name, buyer_name , lien_bank";
	}
	//echo $sql;
	?>
   <div style="width:560px">
    <form name="display_file"  id="display_file">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center" width="100%">
            <thead>
                <th width="60">Sl NO.</th>
                <th width="100">File NO</th>
                <th width="100">Year</th>
                <th width="140"> Buyer</th>
                <th> Lein Bank</th>
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
                <tr bgcolor="<? echo $bgcolor; ?>"  onclick="js_set_value('<? echo $row[csf("internal_file_no")];?>,<? echo $row[csf("lc_sc_year")];?>')" id="search<? echo $row[csf("id")]; ?>" style="cursor:pointer">
                    <td align="center" width="60"> <? echo $i;?></td>
                    <td align="center" width="100"><? echo $row[csf("internal_file_no")];  ?></td>
                    <td align="center" width="100"><? echo $row[csf("lc_sc_year")];  ?></td>
                    <td width="140"><? echo $buyer_name_arr[$row[csf("buyer_name")]];  ?></td>
                    <td><? echo $lein_bank_arr[$row[csf("lien_bank")]];  ?></td>
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

if ($action=="btb_open")
{
	
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$hidden_btb_id=str_replace("'","",$hidden_btb_id);
	$file_buyer=explode("*",str_replace("'","",$file_buyer));
	$file_no=$file_buyer[0];
	$buyer_name=$buyer_name_arr[$file_buyer[1]];
	//echo $hidden_btb_id;die;
	
	$sql= "select 
				a.id,
				a.lc_number,
				a.lc_date,
				a.lc_value,
				a.pi_id,
				a.supplier_id,
				a.item_category_id
			from
				 com_btb_lc_master_details a
			where
				a.id in($hidden_btb_id) and a.is_deleted=0 and a.status_active=1 ";
				//echo $sql;
	$sql_result=sql_select($sql);
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
    
    <table width="800" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
    	<tr><td align="center"><input type="button" class="formbutton" onClick="new_window()" style="width:100px;" value="Print" ></td></tr>
    </table><br>
    <div id="popup_body" style="width:820px;">
	<table width="800" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
        <thead>
        	<tr>
            	<th width="50">SL</th>
                <th width="130">BTB Lc No</th>
                <th width="80">Lc Date</th>
                <th width="100">Amount</th>
                <th width="150">PI No.</th>
                <th width="150">Supplier</th>
                <th >Item Cetagory</th>	
            </tr>
        </thead>
    </table>
    <div style="width:820px; max-height:320px; overflow-y:scroll" id="scroll_body">
	<table width="800" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
        <tbody>
        <?
		$i=1;
		foreach($sql_result as $row)
		{
		if ($i%2==0)
		$bgcolor="#E9F3FF";
		else
		$bgcolor="#FFFFFF";
		
		?>
        	<tr bgcolor="<? echo $bgcolor; ?>">
            	<td width="50"><?  echo $i; ?></td>
                <td width="130"><?  echo $row[csf("lc_number")]; ?></td>
                <td width="80" align="center"><? if($row[csf("lc_date")]!='0000-00-00')  echo change_date_format($row[csf("lc_date")]); else echo ""; ?></td>
                <td  width="100" align="right"><?  echo number_format($row[csf("lc_value")],2);  $total_val+=$row[csf("lc_value")];?></td>
                <td width="150">
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
                <td width="150"><?  echo $suplier_name_arr[$row[csf("supplier_id")]]; ?></td>
                <td><?  echo $item_category[$row[csf("item_category_id")]]; ?></td>	
            </tr>
        <?
		$i++;
		}
		?>
        </tbody>
        <tfoot>
        	<tr>
            	<th >&nbsp;</th>
                <th >&nbsp;</th>
                <th >Total</th>
                <th><? echo number_format($total_val,2); ?> </th>
                <th >&nbsp;</th>
                <th >&nbsp;</th>
                <th >&nbsp;</th>	
            </tr>
        </tfoot>
	</table>
    </div>
    </div>
	<?
exit(); 
}

if ($action=="btb_accep")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$hidden_btb_id=str_replace("'","",$hidden_btb_id);
	if($db_type==0)
	{
	$sql= "select 
				a.id,
				a.invoice_no,
				sum(b.current_acceptance_value) as inv_amount,
				a.maturity_date,
				a.invoice_date,
				group_concat(distinct c.lc_number) as lc_number,
				c.supplier_id,
				c.item_category_id
			from
				  com_import_invoice_mst a, com_import_invoice_dtls b, com_btb_lc_master_details c
			where
				a.id=b.import_invoice_id and a.btb_lc_id=c.id and a.btb_lc_id in($hidden_btb_id) and b.status_active=1 and b.is_deleted=0 
				group by a.id,a.invoice_no,a.maturity_date,a.invoice_date,c.supplier_id,c.item_category_id";
	}
	else if($db_type==2)
	{
	$sql= "select 
				a.id,
				a.invoice_no,
				sum(b.current_acceptance_value) as inv_amount,
				a.maturity_date,
				a.invoice_date,
				LISTAGG(CAST( c.lc_number AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.lc_number) as lc_number,
				c.supplier_id,
				c.item_category_id
			from
				  com_import_invoice_mst a, com_import_invoice_dtls b, com_btb_lc_master_details c
			where
				a.id=b.import_invoice_id and a.btb_lc_id=c.id and a.btb_lc_id in($hidden_btb_id)and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  
				group by a.id,a.invoice_no,a.maturity_date,a.invoice_date,c.supplier_id,c.item_category_id";	
	}
				//echo $sql;die;
				
	?>
	<table width="780" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
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
        <tbody>
        <?
		$sql_result=sql_select($sql);
		$i=1;
		foreach($sql_result as $row)
		{
		if ($i%2==0)
		$bgcolor="#E9F3FF";
		else
		$bgcolor="#FFFFFF";
		?>
        	<tr bgcolor="<? echo $bgcolor; ?>">
            	<td ><?  echo $i; ?></td>
                <td ><?  echo $row[csf("invoice_no")]; ?></td>
                <td align="right"><?  echo number_format($row[csf("inv_amount")],2); $total_amount+=$row[csf("inv_amount")]; ?></td>
                <td  align="center">
				<?
				if ($row[csf("maturity_date")]!='0000-00-00') echo change_date_format($row[csf("maturity_date")]); else echo ""; 
				?>
                </td>
                <td  align="center">
				<?
				if ($row[csf("invoice_date")]!='0000-00-00') echo change_date_format($row[csf("invoice_date")]); else echo ""; 
				?>
                </td>
                <td ><?  echo $row[csf("lc_number")]; ?></td>
                <td ><?  echo $item_category[$row[csf("item_category_id")]]; ?></td>
                <td ><?  echo $suplier_name_arr[$row[csf("supplier_id")]]; ?></td>	
            </tr>
        <?
		$i++;
		}
		?>
        </tbody>
        <tfoot>
        	<tr>
                <th >&nbsp;</th>
                <th >Total</th>
                <th ><? echo number_format($total_amount,2); ?></th>
                <th >&nbsp;</th>
                <th >&nbsp;</th>
                <th >&nbsp;</th>
                <th >&nbsp;</th>
                <th >&nbsp;</th>		
            </tr>
        </tfoot>
	</table>    
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
	
	//echo "select sum(accepted_ammount) as paid from com_import_payment where lc_id in(".$paid_id_arr[2].") and  payment_head=40 and status_active='1'";die;
	$usence_paid_arr=return_library_array( "select invoice_id,sum(accepted_ammount) as paid from  com_import_payment where is_deleted=0  and status_active=1 and  payment_head=40 group by invoice_id",'invoice_id','paid');
	//var_dump($usence_paid_arr);die;
	if($db_type==0)
	{
	$sql= "select 
				a.id,
				c.id as btb_lc_id,
				a.invoice_no,
				a.document_value as inv_amount,
				a.maturity_date,
				a.invoice_date,
				group_concat(distinct c.lc_number) as lc_number,
				c.supplier_id,
				c.item_category_id,
				max(d.payment_date) as payment_date
			from
				  com_import_invoice_mst a,  com_btb_lc_master_details c, com_import_payment d
			where
				a.btb_lc_id=c.id and a.id=d.invoice_id and d.lc_id in(".$paid_id_arr[2].") and d.status_active=1
				group by a.id,a.invoice_no,a.document_value,a.maturity_date,a.invoice_date,c.supplier_id,c.item_category_id";
	}
	else if($db_type==2)
	{
	$sql= "select 
				a.id,
				a.invoice_no,
				a.document_value as inv_amount,
				a.maturity_date,
				a.invoice_date,
				LISTAGG(CAST( c.lc_number AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.lc_number) as lc_number,
				c.supplier_id,
				c.item_category_id,
				max(d.payment_date) as payment_date
			from
				  com_import_invoice_mst a, com_import_invoice_dtls b, com_btb_lc_master_details c, com_import_payment d
			where
				a.btb_lc_id=c.id and a.id=d.invoice_id and d.lc_id in(".$paid_id_arr[2].") and d.status_active=1 
				group by a.id,a.invoice_no,a.document_value,a.maturity_date,a.invoice_date,c.supplier_id,c.item_category_id";
	}
	
	
				//echo $sql;die;
	$sql_result=sql_select($sql);$paid_result=array();
	/*foreach($sql_result as $row)
	{
		$paid_result[$row[csf("btb_id")]]["btb_id"]=$row[csf("btb_id")];
		$paid_result[$row[csf("btb_id")]]["btb_id"]=$row[csf("btb_id")];
		$paid_result[$row[csf("btb_id")]]["btb_id"]=$row[csf("btb_id")];
		$paid_result[$row[csf("btb_id")]]["btb_id"]=$row[csf("btb_id")];
	}*/
	if(!empty($sql_result))
	{
	?>
    <div style="width:828px; overflow-y:scroll; max-height:370px;font-size:12px; overflow-x:hidden;" id="scroll_body">
    <legend>Usance Payment</legend>
	<table width="810" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
        <thead>
        	<tr>
            	<th width="30">SL</th>
                <th width="100">Invoice No</th>
                <th width="80">Amount</th>
                <th width="75">Maturity Date</th>
                <th width="75">Invoice Date</th>
                <th width="95">Lc No</th>
                <th width="100">Item Cetagory</th>
                <th width="100">Supplier</th>
                <th width="80">Paid Amount</th>
                <th >Paid Date</th>		
            </tr>
        </thead>
        <?
		$i=1;
		foreach($sql_result as $row)
		{
		
		if ($i%2==0)
		$bgcolor="#E9F3FF";
		else
		$bgcolor="#FFFFFF";
		
		?>
        <tbody>
        	<tr bgcolor="<? echo $bgcolor; ?>">
            	<td ><?  echo $i; ?></td>
                <td ><?  echo $row[csf("invoice_no")]; ?></td>
                <td  align="right"><? echo number_format($row[csf("inv_amount")],2); $total_inv_amount+=$row[csf("inv_amount")]; ?></td>
                <td  align="center">
				<?
				if ($row[csf("maturity_date")]!='0000-00-00') echo change_date_format($row[csf("maturity_date")]); else echo ""; 
				?>
                </td>
                <td  align="center">
				<?
				if ($row[csf("invoice_date")]!='0000-00-00') echo change_date_format($row[csf("invoice_date")]); else echo ""; 
				?>
                </td>
                <td ><?  echo $row[csf("lc_number")]; ?></td>
                <td  ><?  echo $item_category[$row[csf("item_category_id")]]; ?></td>
                <td ><?  echo $suplier_name_arr[$row[csf("supplier_id")]]; ?></td>
                <td  align="right">
				<?
				  $usence_paid_amt=$usence_paid_arr[$row[csf("id")]];
				  echo number_format($usence_paid_amt,2); $total_accept_amount+=$usence_paid_amt;
				  $tem_arr[]=$row[csf("btb_lc_id")];
				?>
                </td>
                <td align="center">
				<?
				  if ($row[csf("payment_date")]!='0000-00-00') echo change_date_format($row[csf("payment_date")]); else echo ""; 
				  $tem_date_arr[]=$row[csf("btb_lc_id")];
				?></td>	
            </tr>
        <?
		$i++;
		}
		?>
        </tbody>
        <tfoot>
        	<tr>
            	<th>&nbsp;</th>
                <th >Total:</th>
                <th ><? echo number_format($total_inv_amount,2); ?></th>
                <th >&nbsp;</th>
                <th >&nbsp;</th>
                <th >&nbsp;</th>
                <th >&nbsp;</th>
                <th >Total:</th>
                <th ><? echo number_format($total_accept_amount,2); ?></th>
                <th >&nbsp;</th>		
            </tr>
        </tfoot>
	</table>
	<?
	}
	if($db_type==0)
	{
	$sql= "select 
				a.id,
				a.invoice_no,
				sum(b.current_acceptance_value) as inv_amount,
				a.maturity_date,
				a.invoice_date,
				group_concat(distinct c.lc_number) as lc_number,
				c.supplier_id,
				c.item_category_id
			from
				  com_import_invoice_mst a, com_import_invoice_dtls b, com_btb_lc_master_details c
			where
				a.id=b.import_invoice_id and a.btb_lc_id=c.id and b.btb_lc_id in(".$paid_id_arr[1].") 
			group by 
				a.id,a.invoice_no,a.maturity_date,a.invoice_date,c.supplier_id,c.item_category_id";
	}
	else if($db_type==2)
	{
	$sql= "select 
				a.id,
				a.invoice_no,
				sum(b.current_acceptance_value) as inv_amount,
				a.maturity_date,
				a.invoice_date,
				LISTAGG(CAST( c.lc_number AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.lc_number) as lc_number,
				c.supplier_id,
				c.item_category_id
			from
				  com_import_invoice_mst a, com_import_invoice_dtls b, com_btb_lc_master_details c
			where
				a.id=b.import_invoice_id and a.btb_lc_id=c.id and b.btb_lc_id in(".$paid_id_arr[1].") 
			group by 
				a.id,a.invoice_no,a.maturity_date,a.invoice_date,c.supplier_id,c.item_category_id";
	}
	
				//echo $sql;die;
	$sql_result=sql_select($sql);
	if(!empty($sql_result))
	{			
	?>
    <br>
    <legend style="width:782px;">At-Site Accepted</legend>
	<table width="780" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
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
        <tbody>
        <?
		$i=1;
		foreach($sql_result as $row)
		{
		if ($i%2==0)
		$bgcolor="#E9F3FF";
		else
		$bgcolor="#FFFFFF";
		
		?>
        	<tr bgcolor="<? echo $bgcolor; ?>">
            	<td><?  echo $i; ?></td>
                <td ><?  echo $row[csf("invoice_no")]; ?></td>
                <td  align="right"><?  echo number_format($row[csf("inv_amount")],2); $total_amount+=$row[csf("inv_amount")]; ?></td>
                <td  align="center">
				<?
				if ($row[csf("maturity_date")]!='0000-00-00') echo change_date_format($row[csf("maturity_date")]); else echo ""; 
				?>
                </td>
                <td  align="center">
				<?
				if ($row[csf("invoice_date")]!='0000-00-00') echo change_date_format($row[csf("invoice_date")]); else echo ""; 
				?>
                </td>
                <td ><?  echo $row[csf("lc_number")]; ?></td>
                <td  ><?  echo $item_category[$row[csf("item_category_id")]]; ?></td>
                <td ><?  echo $suplier_name_arr[$row[csf("supplier_id")]]; ?></td>	
            </tr>
        <?
		$i++;
		}
		?>
        </tbody>
        <tfoot>
        	<tr>
            	<th>&nbsp;</th>
                <th>Total</th>
                <th><? echo number_format($total_amount,2); ?></th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>	
            </tr>
        </tfoot>
	</table> 
    <?
	}
	$sql= "select 
				a.id,
				a.lc_number,
				a.lc_date,
				a.lc_value,
				a.pi_id,
				a.supplier_id,
				a.item_category_id
			from
				 com_btb_lc_master_details a
			where
				a.id in(".$paid_id_arr[0].")";
				//echo $sql;die;
	$sql_result=sql_select($sql);
	if(!empty($sql_result))
	{
	?>
    <br>
    <legend style="width:752px;">Cash In Advance</legend>
	<table width="750" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
        <thead>
        	<tr>
            	<th width="50">SL</th>
                <th width="130">BTB Lc No</th>
                <th width="80">Lc Date</th>
                <th width="100">Amount</th>
                <th width="150">Supplier</th>
                <th >Item Cetagory</th>	
            </tr>
        </thead>
        <tbody>
        <?
		$i=1;
		foreach($sql_result as $row)
		{
		if ($i%2==0)
		$bgcolor="#E9F3FF";
		else
		$bgcolor="#FFFFFF";
		
		?>
        	<tr bgcolor="<? echo $bgcolor; ?>">
            	<td ><?  echo $i; ?></td>
                <td ><?  echo $row[csf("lc_number")]; ?></td>
                <td  align="center"><? if($row[csf("lc_date")]!='0000-00-00')  echo change_date_format($row[csf("lc_date")]); else echo ""; ?></td>
                <td  align="right"><?  echo number_format($row[csf("lc_value")],2);  $total_val+=$row[csf("lc_value")];?>&nbsp;</td>
                <td align="center"><?  echo $suplier_name_arr[$row[csf("supplier_id")]]; ?></td>
                <td><?  echo $item_category[$row[csf("item_category_id")]]; ?></td>	
            </tr>
        <?
		$i++;
		}
		?>
        </tbody>
        <tfoot>
        	<tr>
            	<th >&nbsp;</th>
                <th >&nbsp;</th>
                <th >Total</th>
                <th ><? echo number_format($total_val,2); ?> </th>
                <th >&nbsp;</th>
                <th >&nbsp;</th>	
            </tr>
        </tfoot>
	</table>   
    </div>
	<?
	}
	exit(); 
	}

disconnect($con);
?>
