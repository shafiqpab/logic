<?
    require('maindata.php');
    $i = 1;
    ob_start();
    ?>
    <table cellpadding="2" border="1">
        <tr>
            <td width="319">
                <br />
                <table border="0" cellpadding="2">
                    <tr>
                        <td width="318">
                            <br />
                            <b><u>Shipper/Exporter/Manufacturer:</u></b>
                            <br />
                            <b><?php echo $company_name; ?></b>
                            <?php
                            if ($city != "")  $comany_details .= "<br>" . $city . ", ";
                            if ($country_id != "")  $comany_details .= "<br>" . $country_name . ".";
                            echo  $comany_details;
                            ?><br />
                            <br />
                            <br />
                        </td>
                    </tr>
                    <tr>
                        <td width="158" style="border-top:1px solid #000;border-right:1px solid #000">
                            <br />
                            EPB REG NO. :
                        </td>
                        <td width="158">
                            <br />
                        </td>
                    </tr>
                </table>
            </td>
            <td width="319">
                <br />
                <table border="0">
                    <tr>
                        <td width="80">Invoice No.</td>
                        <td width="120"> : <?php echo $invoice_no;  ?> </td>
                        <td width="119"> Date: <? echo change_date_format($invoice_date); ?></td>
                    </tr>
                    <tr>
                        <td width="80">EXP No.</td>
                        <td width="120"> : <?php echo $exp_form_no; ?> </td>
                        <td width="119"> Date: <? if ($exp_form_date != "" && $exp_form_date != "0000-00-00") echo change_date_format($exp_form_date); ?></td>
                    </tr>
                    <tr>
                        <td width="80">CONT. No.</td>
                        <td width="120"> : <?php echo $lc_sc_no; ?> </td>
                        <td width="119"> Date: <? echo $lc_sc_date; ?></td>
                    </tr>
                    <tr>
                        <td width="80">Issueing Bank</td>
                        <td width="239"> : <? echo "&nbsp;&nbsp;" . $issuing_bank_name;  ?></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td width="160">
                <br />
                <strong><u>Applicant:</u></strong><br />
                <?
                echo  $applicant . "<br/>";
                echo  $applicantAddress;
                ?>
            </td>
            <td width="159">
                <br />
                <strong><u>Notify:</u></strong><br />
                <?
                echo "&nbsp;&nbsp;" . $buyer_name_arr[$notifying_party]["buyer_name"] . "<br/>";
                echo "&nbsp;&nbsp;" . $buyer_name_arr[$notifying_party]["address_1"];
                ?>
            </td>
            <td width="319">
                <br />
                <table border="0">
                    <tr>
                        <td idth="319"><strong><u>Remarks:</u></strong></td>
                    </tr>
                    <tr>
                        <td width="90">Country of Origin</td>
                        <td width="229"> : <? echo "Bangladesh"; ?></td>
                    </tr>
                    <tr>
                        <td width="90">Payment Terms</td>
                        <td width="229"> : <? echo $pay_term[$pay_term_id]; ?></td>
                    </tr>
                    <tr>
                        <td width="90">Mode of Shipment</td>
                        <td width="229"> : <? echo $shipment_mode[$shipping_mode]; ?></td>
                    </tr>
                    <tr>
                        <td width="90">Delivery Condition</td>
                        <td width="229"> : <? echo $incoterm[$inco_term] . "," . $inco_term_place; ?></td>
                    </tr>
                    <tr>
                        <td width="90">Port of Loading</td>
                        <td width="229"> : <? echo $port_of_loading; ?></td>
                    </tr>
                    <tr>
                        <td width="90">Port of Discharge</td>
                        <td width="229"> : <? echo $port_of_discharge; ?></td>
                    </tr>
                    <tr>
                        <td width="90">Final Destination</td>
                        <td width="229"> : <? echo $place_of_delivery; ?></td>
                    </tr>
                    <tr>
                        <td width="90">B/L No. & Date</td>
                        <td width="129"> : <?php echo $bl_no; ?> </td>
                        <td width="100">Date:<? if ($bl_date != "" && $bl_date != "0000-00-00") echo change_date_format($bl_date); ?></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td width="319">
            </td>
            <td width="319">VESSEL NAME: <? echo $mother_vessel; ?></td>
        </tr>
        <tr style="font-size:small; font-weight:bold" align="left">
            <td width="638">
                <br />
                Negotiating Bank&nbsp;: <? echo $negotiating_bank_text ?><br />
                Account Number&nbsp;: <br />
                Swift Number&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; :
            </td>
        </tr>
        <tr style="font-size:small; font-weight:bold" align="center">
            <td width="90" rowspan="2">Style no.</td>
            <td width="62" rowspan="2">PO</td>
            <td width="62" rowspan="2">Actual PO</td>
            <td width="180" rowspan="2">DESCRIPTION OF GOODS</td>
            <td width="120">QUANTITY</td>
            <td width="50" rowspan="2">Unit Price US$</td>
            <td width="73" rowspan="2">VALUE US$</td>
        </tr>
        <tr style="font-size:small; font-weight:bold" align="center">
            <td width="50">CTN</td>
            <td width="70">PCS/SET</td>
        </tr>
        <tr>
            <td width="638">
                <br />
                <table border="0" cellpadding="2">
                    <?
                    $po_test=array();
                    foreach ($result as $row) 
					{
						if($row[csf('actual_po_infos')]!="")
						{
							$acc_po_ref_arr=explode("**",$row[csf('actual_po_infos')]);
							foreach($acc_po_ref_arr as $acc_data)
							{
								$acc_data_arr=explode("=",$acc_data);
								?>
								<tr style="font-size:small; font-weight:bold">
                                    <td width="88" style="border-right: 1px solid #000">
                                        <br />
                                        <? if($po_test[$row[csf('po_breakdown_id')]]=="") echo $row[csf('style_ref_no')]; ?>
                                    </td>
                                    <td width="62" style="border-right: 1px solid #000">
                                        <br />
                                        <? if($po_test[$row[csf('po_breakdown_id')]]=="") echo $row[csf("po_number")]; ?>
                                    </td>
                                    <td width="62" style="border-right: 1px solid #000">
                                        <br />
                                        <? echo $acc_data_arr[2]; ?>
                                    </td>
                                    <td width="180" align="left" style="border-right: 1px solid #000">
                                        <? if($po_test[$row[csf('po_breakdown_id')]]=="") echo $order_la_data[$row[csf("po_breakdown_id")]]["fabric_description"]; ?><br />
                                        <? if($po_test[$row[csf('po_breakdown_id')]]=="") echo implode(",", $itemIdArr[$row[csf('po_breakdown_id')]]); ?>
                                    </td>
                                    <td width="50" align="right" style="border-right: 1px solid #000">
                                        <? if($po_test[$row[csf('po_breakdown_id')]]=="") echo number_format($carton_inv_arr[$row[csf('po_breakdown_id')]][$id], 0, ".", ","); ?>
                                    </td>
                                    <td width="70" align="right" style="border-right: 1px solid #000">
                                        <? echo $acc_data_arr[1]; ?>
                                    </td>
                                    <td width="50" align="right" style="border-right: 1px solid #000">
                                        <? echo $row[csf('current_invoice_rate')]; ?>
                                    </td>
                                    <td width="73" align="right">
                                        <? $acc_po_wise_value=$acc_data_arr[1]*$row[csf('current_invoice_rate')]; echo number_format($acc_po_wise_value,2); ?>
                                    </td>
                                </tr>
                                <?
								$po_test[$row[csf('po_breakdown_id')]]=$row[csf('po_breakdown_id')];
							}
						}
						else
						{
							?>
                            <tr style="font-size:small; font-weight:bold">
                                <td width="88" style="border-right: 1px solid #000">
                                    <br />
                                    <? echo $row[csf('style_ref_no')]; ?>
                                </td>
                                <td width="62" style="border-right: 1px solid #000">
                                    <br />
                                    <? echo $row[csf("po_number")]; ?>
                                </td>
                                <td width="62" style="border-right: 1px solid #000">
                                    <br />
                                    <? echo $row[csf("po_number")]; ?>
                                </td>
                                <td width="180" align="left" style="border-right: 1px solid #000">
                                    <? echo $order_la_data[$row[csf("po_breakdown_id")]]["fabric_description"]; ?><br />
                                    <? echo implode(",", $itemIdArr[$row[csf('po_breakdown_id')]]); ?>
                                </td>
                                <td width="50" align="right" style="border-right: 1px solid #000">
                                    <? echo number_format($carton_inv_arr[$row[csf('po_breakdown_id')]][$id], 0, ".", ","); ?>
                                </td>
                                <td width="70" align="right" style="border-right: 1px solid #000">
                                    <? echo $row[csf('current_invoice_qnty')]; ?>
                                </td>
                                <td width="50" align="right" style="border-right: 1px solid #000">
                                    <? echo $row[csf('current_invoice_rate')]; ?>
                                </td>
                                <td width="73" align="right">
                                    <? echo $row[csf('current_invoice_value')]; ?>
                                </td>
                            </tr>
                            <?
						}
                    	
                        $total_value += $row[csf("current_invoice_value")];
                        $total_qnty += $row[csf("current_invoice_qnty")] * $setQtyArr[$row[csf('po_breakdown_id')]];
                        $last_uom = $unit_of_measurement[$row[csf('order_uom')]];
                        $total_po_carton_qnty += $carton_inv_arr[$row[csf('po_breakdown_id')]][$id];
                        $hs_code_arr_cat[$order_la_data[$row[csf("po_breakdown_id")]]["hs_code"]] = $order_la_data[$row[csf("po_breakdown_id")]]["category_no"];
                        $hs_code_arr_qty[$order_la_data[$row[csf("po_breakdown_id")]]["hs_code"]] += $row[csf("current_invoice_qnty")] * $setQtyArr[$row[csf('po_breakdown_id')]];
                        $i++;
                    }
                    ?>
                    <tr>
                        <td width="88" style="border-right: 1px solid #000"></td>
                        <td width="62" style="border-right: 1px solid #000"></td>
                        <td width="62" style="border-right: 1px solid #000"></td>
                        <td width="180" style="border-right: 1px solid #000"><br /> <br /><br /><br /><br /> <br /><br />
                            <table border="0">
                                <tr>
                                    <td width="60"><br />HS CODE</td>
                                    <td width="90"> :<? echo  $hs_code; ?></td>
                                    <td width="30"></td>
                                </tr>
                                <tr>
                                    <td width="60"><br />Category No</td>
                                    <td width="90"> :<? echo $category_no; ?></td>
                                    <td width="30"></td>
                                </tr>
                            </table>
                        </td>
                        <td width="50" align="right" style="border-right: 1px solid #000"></td>
                        <td width="70" align="right" style="border-right: 1px solid #000">
                        </td>
                        <td width="50" style="border-right: 1px solid #000">
                        </td>
                        <td width="73" align="right">
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td width="394" align="right">
                Total :
            </td>
            <td width="50" align="right">
                <? echo $total_po_carton_qnty; ?>
            </td>
            <td width="70" align="right">
                <? echo number_format($total_qnty, 0, ".", ",") . " Pcs" ?>
            </td>
            <td width="50">
            </td>
            <td width="73" align="right">
                <? echo "US$&nbsp;&nbsp;" . number_format($total_value, 2, ".", ","); ?>
            </td>
        </tr>
        <tr>
            <td width="394" align="right">Upcharge :</td>
            <td width="50" align="right"></td>
            <td width="70" align="right"></td>
            <td width="50"></td>
            <td width="73" align="right">
                <? echo number_format($upcharge_ammount, 2, ".", ","); ?>
            </td>
        </tr>
        <tr>
            <td width="394" align="right">Discount :</td>
            <td width="50" align="right"></td>
            <td width="70" align="right"></td>
            <td width="50"></td>
            <td width="73" align="right">
                <? echo number_format($discount_ammount, 2, ".", ","); ?>
            </td>
        </tr>
        <tr>
            <td width="394" align="right">Commission Amount :</td>
            <td width="50" align="right"></td>
            <td width="70" align="right"></td>
            <td width="50"></td>
            <td width="73" align="right"><? echo number_format($commission, 2, ".", ","); ?></td>
        </tr>
        <?if($bonus_ammount!=""){?>
        <tr>
            <td width="394" align="right">Inspection Amount Head :</td>
            <td width="50" align="right"></td>
            <td width="70" align="right"></td>
            <td width="50"></td>
            <td width="73" align="right"><? echo number_format($bonus_ammount, 2, ".", ","); ?></td>
        </tr>
        <?
        }if($claim_ammount!=""){?>
        <tr>
            <td width="394" align="right"> Claim Amount  :</td>
            <td width="50" align="right"></td>
            <td width="70" align="right"></td>
            <td width="50"></td>
            <td width="73" align="right"><? echo number_format($claim_ammount, 2, ".", ","); ?></td>
        </tr>
        <?}
        if($atsite_discount_amt!=0){?>
        <tr>
            <td width="394" align="right">Discount For At Sight Payment Amount  :</td>
            <td width="50" align="right"></td>
            <td width="70" align="right"></td>
            <td width="50"></td>
            <td width="73" align="right"><? echo number_format($atsite_discount_amt, 2, ".", ","); ?></td>
        </tr>
        <?}?>
        <tr>
            <td width="394" align="right">Other Deduction Amount :</td>
            <td width="50" align="right"></td>
            <td width="70" align="right"></td>
            <td width="50"></td>
            <td width="73" align="right"><? echo number_format($other_discount_amt, 2, ".", ","); ?></td>
        </tr>
        <tr>
            <td width="394" align="right">
                Net Total :
            </td>
            <td width="50" align="right"></td>
            <td width="70" align="right"></td>
            <td width="50">
            </td>
            <td width="73" align="right">
                <? echo "US$&nbsp;&nbsp;" . number_format($net_invo_value, 2, ".", ","); ?>
            </td>
        </tr>
        <tr>
            <td width="638" align="center">
                SAY: <? echo "( " . number_to_words(def_number_format($net_invo_value, 2, ""), "USD", "CENTS") . " Only )"; ?>
            </td>
        </tr>
        <tr>
            <td width="638">
                <br />
                <b>Statement On Origin :</b> The exporter <b><?= $company_name; ?>. REX NO: ...................... </b>of the products covered by this documents declares that,except where otherwise clearly indicated,these products are of. Bangladesh preferential origin according to rules of origin of the Generalized system of preferences of the European Union and that the origin criterion met is W.........................
            </td>
        </tr>
        <tr>
            <td width="638">
                <br />
                <table border="0">
                    <tr>
                        <td width="638">
                            <br />
                            <b>Shiping Mark:</b>
                        </td>
                    </tr>
                    <tr>
                        <td width="219">
                            <br />
                            <u>Main Mark</u><br />
                            <?
                            $all_main_mark = "";
                            foreach ($main_mark_arr as $val) {
                                $all_main_mark .= $val . "<br>";
                            }
                            $all_main_mark = chop($all_main_mark, " <br> ");
                            echo  $all_main_mark;
                            ?>
                        </td>
                        <td width="219"><u>Side Mark</u><br />
                            <?
                            $all_side_mark = "";
                            foreach ($side_mark_arr as $val) {
                                $all_side_mark .= $val . "<br>";
                            }
                            $all_side_mark = chop($all_side_mark, " <br> ");
                            echo  $all_side_mark;
                            ?>
                        </td>
                        <td width="200"></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr style="border:none">
            <td width="394" style="border:none">
                <br />
                <table border="0" cellpadding="2">
                    <tr>
                        <td width="100">Total Qty of PCS</td>
                        <td width="40">:</td>
                        <td width="80" align="right"><? echo number_format($total_qnty, 0, ".", ","); ?> PCS</td>
                        <td width="142"></td>
                    </tr>
                    <tr>
                        <td width="100">Total Qty of CTNS</td>
                        <td width="40">:</td>
                        <td width="80" align="right"><? echo number_format($total_po_carton_qnty, 0, ".", ","); ?> CTNS</td>
                        <td width="142"></td>
                    </tr>
                    <tr>
                        <td width="100">Total Net Weight</td>
                        <td width="40">:</td>
                        <td width="80" align="right"><? echo number_format($net_weight, 2); ?> KGS</td>
                        <td width="142"></td>
                    </tr>
                    <tr>
                        <td width="100">Total Gross Weight</td>
                        <td width="40">:</td>
                        <td width="80" align="right"><? echo number_format($gross_weight, 2); ?> KGS</td>
                        <td width="142"></td>
                    </tr>
                    <tr>
                        <td width="100">Total Volume</td>
                        <td width="40">:</td>
                        <td width="80" align="right"><? echo number_format($cbm_qnty, 2); ?> CBM</td>
                        <td width="142"></td>
                    </tr>
                </table>
            </td>
            <td width="244" align="right" style="border:none">
                FOR <? echo strtoupper($company_name_arr[$benificiary_id]["company_name"]); ?>
                <br /><br /><br /><br /><br /><br /><br /><br />
                ......................................................<br />
                Authorized Signature
            </td>
        </tr>
    </table>
    <?
    $HTM = ob_get_contents();
    ob_end_clean();
    $invoice = new invoice($HTM, $header);
?>