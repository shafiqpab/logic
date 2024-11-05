<?
    require('maindata.php');
    $i = 1;
    ob_start();
    ?>
    <table cellpadding="2" border="1">
        <tr>
            <td width="319">
                <br />
                <b>SUPPLIER/SHIPPER:</b>
                <br />
                <b><?php echo $company_name; ?></b>
                <?php
                if ($city != "")  $comany_details .= "<br />" . $city . ", ";
                if ($country_id != "")  $comany_details .= "<br />" . $country_name . ".";
                echo  $comany_details;
                ?><br />
            </td>
            <td width="319">
                <br />
                <table border="0">
                    <tr>
                        <td width="80">Invoice No.</td>
                        <td width="239"> : <?php echo $invoice_no;  ?> </td>
                    </tr>
                    <tr>
                        <td width="80">Date</td>
                        <td width="239"> : <? echo change_date_format($invoice_date); ?> </td>
                    </tr>
                    <tr>
                        <td width="80">EXP No.</td>
                        <td width="239"> : <?php echo $exp_form_no; ?> </td>
                    </tr>
                    <tr>
                        <td width="80">Date</td>
                        <td width="239"> : <?php if ($exp_form_date != "" && $exp_form_date != "0000-00-00") echo change_date_format($exp_form_date); ?> </td>
                    </tr>
                    <tr>
                        <td width="80">CONTACT NO.</td>
                        <td width="239"> : <?php echo $lc_sc_no; ?> </td>
                    </tr>
                    <tr>
                        <td width="80">Date</td>
                        <td width="239"> : <?php echo $lc_sc_date; ?> </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td width="319">
                <br />
                <b>BENEFICIARY BANK:</b><br />
                <b>To The Order Of :</b><br />
                <?php echo $bank_name_arr[$lien_bank]["bank_name"]."<br />".$bank_name_arr[$lien_bank]["address"]; ?><br />
                <b>SWIFT :</b> <?=$bank_name_arr[$lien_bank]["swift_code"];?> <br />
                <b>ACCOUNT NO :</b> <?=$bank_acc_arr[$lien_bank][6]["account_no"];?><br />
            </td>
            <td width="319">
                <br />
                <b>BUYER'S BANK :</b><br />
                <?php echo $bank_name_arr[$issuing_bank_name]["bank_name"]."<br />".$bank_name_arr[$issuing_bank_name]["address"]; ?>
            </td>
        </tr>
        <tr>
            <td width="319">
                <br />
                <strong>APPLICANT:</strong><br />
                <? echo $applicant."<br/>".$applicantAddress; ?>
            </td>
            <td width="319">
                <br />
                <strong>NOTIFY:</strong><br />
                <?
                echo $buyer_name_arr[$notifying_party]["buyer_name"] . "<br/>";
                echo $buyer_name_arr[$notifying_party]["address_1"];
                ?>
            </td>
        </tr>
        <tr>
            <td width="319">
                <br />
                CONSIGNEE : <br />
                <?
                    echo $buyer_name_arr[$consignee]["buyer_name"]."<br/>";
                    echo $buyer_name_arr[$consignee]["address_1"];
                ?>
            </td>
            <td width="319"></td>
        </tr>
        <tr>
            <td width="319">
                <br />
                <table border="0">
                    <tr>
                        <td width="150">COUNTRY OF ORIGIN</td>
                        <td width="169"> : <? echo "Bangladesh"; ?></td>
                    </tr>
                    <tr>
                        <td width="150">MODE OF SHIPMENT</td>
                        <td width="169"> : <? echo $shipment_mode[$shipping_mode]; ?></td>
                    </tr>
                    <tr>
                        <td width="150">PORT OF LOADING</td>
                        <td width="169"> : <? echo $port_of_loading; ?></td>
                    </tr>
                    <tr>
                        <td width="150">INCOTERM</td>
                        <td width="169"> : <? echo $incoterm[$inco_term] . "," . $inco_term_place; ?></td>
                    </tr>
                </table>
            </td>
            <td width="319">
                <br />
                <table border="0">
                    <tr>
                        <td width="150">COUNTRY OF DESTINATION</td>
                        <td width="169"> : <? echo $place_of_delivery; ?></td>
                    </tr>
                    <tr>
                        <td width="150">PORT OF DISCHARGE</td>
                        <td width="169"> : <? echo $port_of_discharge; ?></td>
                    </tr>
                    <tr>
                        <td width="150">VESSEL NAME</td>
                        <td width="169"> : <? echo $mother_vessel; ?></td>
                    </tr>
                    <tr>
                        <td width="150">FCR NO.</td>
                        <td width="169"> :</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr style="font-size:small; font-weight:bold" align="center">
            <td width="100">SHIPPING MARK</td>
            <td width="170">DESCRIPTION OF GOODS <br />(ORDER & DEPARTMENT NUMBER)</td>
            <td width="88">KEY<br />CODE</td>
            <td width="62">CTN</td>
            <td width="58">QTY IN PKS</td>
            <td width="80">UNIT PRICE (FCA) US DOLLAR</td>
            <td width="80">TOTAL US DOLLAR</td>
        </tr>
        <tr>
            <td width="638">
                <br />
                <table border="0" cellpadding="2">
                    <?
                    $row_span=count($result);
                    $i=1;
                    foreach ($result as $row) {
                        ?>
                        <tr style="font-size:small; font-weight:bold">
                            <?
                                if($i==1)
                                {
                                    ?>
                                        <td rowspan="<?=$row_span;?>" width="100" style="border-right: 1px solid #000">
                                            <br />
                                            PORT<br />
                                            DEPART NO.<br />
                                            ORDER NO.<br />
                                            KEY CODE<br />
                                            STYLE NO.<br />
                                            QTY. ISSUE PACK<br />
                                            QTY. SHIPPER PACK<br />
                                            MADE IN BANGLADESH<br />
                                            CTN NO.<br />
                                        </td>
                                    <?
                                }
                            ?>
                            <td width="170" style="border-right: 1px solid #000">
                                <br />
                                <? echo implode(",",$itemIdArr[$row[csf('po_breakdown_id')]]); ?> <br />
                                ORDER NO. <? echo $row[csf("po_number")]; ?><br />
                                STYLE NO. <? echo $row[csf('style_ref_no')]; ?>
                            </td>
                            <td width="88" style="border-right: 1px solid #000">
                                <br />
                                <? echo implode("<br />",explode(",",$articleNoArr[$row[csf('po_breakdown_id')]])); ?>
                            </td>
                            <td width="62" align="right" style="border-right: 1px solid #000">
                                <? echo number_format($carton_inv_arr[$row[csf('po_breakdown_id')]][$id], 0, ".", ","); ?>
                            </td>                            
                            <td width="58" align="right" style="border-right: 1px solid #000">
                                <? echo $row[csf('current_invoice_qnty')]; ?>
                            </td>
                            <td width="80" align="right" style="border-right: 1px solid #000">
                                <? echo $row[csf('current_invoice_rate')]; ?>
                            </td>
                            <td width="80" align="right">
                                <? echo $row[csf('current_invoice_value')]; ?>
                            </td>
                        </tr>
                        <?
                        $total_value += $row[csf("current_invoice_value")];
                        $total_qnty += $row[csf("current_invoice_qnty")] * $setQtyArr[$row[csf('po_breakdown_id')]];
                        $last_uom = $unit_of_measurement[$row[csf('order_uom')]];
                        $total_po_carton_qnty += $carton_inv_arr[$row[csf('po_breakdown_id')]][$id];
                        $hs_code_arr_cat[$order_la_data[$row[csf("po_breakdown_id")]]["hs_code"]] = $order_la_data[$row[csf("po_breakdown_id")]]["category_no"];
                        $hs_code_arr_qty[$order_la_data[$row[csf("po_breakdown_id")]]["hs_code"]] += $row[csf("current_invoice_qnty")] * $setQtyArr[$row[csf('po_breakdown_id')]];
                        $i++;
                    }
                    ?>
                </table>
            </td>
        </tr>
        <tr>
            <td width="358" align="right">
                Total :
            </td>
            <td width="62" align="right">
                <? echo $total_po_carton_qnty; ?>
            </td>
            <td width="58" align="right">
                <? echo number_format($total_qnty, 0, ".", ",") . " Pcs" ?>
            </td>
            <td width="80">
            </td>
            <td width="80" align="right">
                <? echo number_format($total_value, 2, ".", ","); ?>
            </td>
        </tr>
        <tr><td width="638"><br /> </td></tr>
        <tr>
            <td width="438">
                <br />
                I DECLARE THAT : <br />	
                A) THE LAST PROCESS IN THE MANUFACTURE OF THE GOODS DESCRIBED ON THIS INVOICE WAS PERFORMED IN BANGLADESH AND	<br />			
                B) NOT LESS THAN 50 PERCENT OF THEIR TOTAL FACTORY COST IS REPRESENTED BY THE SUM OF THE ALLOWABLE EXPENDITURE OF THE FACTORY ON MATARIALS, LABOUR AND OVERHEADS AND THE COST OF INNER CONTAINERS OF BANGLADESH. 	<br />				
                C) EXPENDITURE OF THE FACTORY ON MATERIALS PRODUCED OR MANUFACTURED IN BANGLADESH THAT HAS BEEN INCLUDED IN AGGREGRATE IN THE ALLOWABLE EXPENDITURE OF THE FACTORY ON MATERIALS DOES NOT EXCEED 25% OF THE TOTAL FACTORY COST OF THE GOODS."				
            </td>
            <td width="200">
                <table border="0" cellpadding="2">
                    <tr>
                        <td width="130">TOTAL QTY</td>
                        <td width="70"> : <? echo number_format($total_qnty, 0, ".", ","); ?></td>
                    </tr>
                    <tr>
                        <td width="130">TOTAL INNER BLISTER QTY</td>
                        <td width="70"> :</td>
                    </tr>
                    <tr>
                        <td width="130">TOTAL CARTON</td>
                        <td width="70"> : <? echo number_format($total_carton_qnty, 0, ".", ","); ?></td>
                    </tr>
                    <tr>
                        <td width="130">TOTAL GROSS WEIGHT</td>
                        <td width="750"> : <? echo number_format($gross_weight, 2); ?> KGS</td>
                    </tr>
                    <tr>
                        <td width="130">TOTAL NET WEIGHT</td>
                        <td width="70"> : <? echo number_format($net_weight, 2); ?> KGS</td>
                    </tr>
                    <tr>
                        <td width="130">TOTAL CBM</td>
                        <td width="70"> : <? echo number_format($cbm_qnty, 2); ?></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <?
    $HTM = ob_get_contents();
    ob_end_clean();
    $invoice = new invoice($HTM, $header);
?>