    <?
	require('maindata.php');
	$i=1;
    ob_start();
	?>
    <table cellpadding="2" border="1">
        <tr>
            <td width="319" rowspan="2"><br/><strong><u>SHIPPER/EXPORTER:</u></strong><br/>
            <b><?php echo $company_name; ?></b>
            <?php
            if($city!="")  $comany_details.= "<br>".$city.", ";
            if($country_id!="")  $comany_details.="<br>".$country_name.".";
            echo  $comany_details;
            ?><br/>
            <strong><u>FOR ACCOUNT & RISK OF:</u></strong><br/>
            <?
            echo  $applicant."<br/>";
            echo  $applicantAddress;
            ?>
            </td>
            <td width="200">
            &nbsp;
            <br/>
            Invoice No.: <?php echo $invoice_no;  ?><br/>
            EXP No. <?php echo $exp_form_no; ?><br/>
            L/C No. <?php echo $lc_sc_no; ?><br/>
            B/L No. <?php echo $bl_no; ?>
            </td>
            <td width="119" style="border:none">
            &nbsp;
            <br/>
             Date: <? echo change_date_format($invoice_date);?><br/>
             Date:<? if($exp_form_date!="" && $exp_form_date!="0000-00-00" ) echo change_date_format($exp_form_date);?><br/>
             Date:<? echo $lc_sc_date;?><br/>
             Date:<?  if($bl_date!="" && $bl_date!="0000-00-00") echo change_date_format($bl_date); ?><br/>
            </td>
        </tr>
        <tr>
            <td width="319" title="Issuing Bank from LC">
            <br/>
            <strong><u>Issueing Bank:</u></strong><br/>
            
            <? echo $issuing_bank_name;  ?>
            </td>
            </tr>
            
            <tr>
            <td width="319" title="Applicant Name from LC AND ADDRESS (BUYER)"> 
            <br/>
            <strong><u>Buying Agent:</u></strong><br/>
            <?
            echo $agent."<br/>";
            echo $agentAddress;
            ?>
           
                
            </td>
            <td width="319" title=" Lien Bank from LC (BANK LIBRARY)">
            <br/>
            <strong><u>Beneficiary's Bank Name & Address:</u></strong><br/>
            <?
            echo "&nbsp;&nbsp;".$bank_name_arr[$lien_bank]["bank_name"]."<br/>";
            echo $bank_name_arr[$lien_bank]["address"];
            ?>
            </td>
        </tr>
        <tr>
            <td width="319"> 
            <br/>
            <strong><u>Consignee/Notify Party:</u></strong><br/>
            <?
            echo "&nbsp;&nbsp;".$buyer_name_arr[$consignee]["buyer_name"]."<br/>";
            echo "&nbsp;&nbsp;".$buyer_name_arr[$consignee]["address_1"]."<br/>";
            echo "&nbsp;&nbsp;".$buyer_name_arr[$notifying_party]["buyer_name"]."<br/>";
            echo "&nbsp;&nbsp;".$buyer_name_arr[$notifying_party]["address_1"];
            ?>
            </td>
            <td width="200">
            SAILING ON / ABOUT:<br/>
            &nbsp;&nbsp;
            </td>
             <td width="119">
            PAYMENT TERMS :<br/>
           <? echo "&nbsp;&nbsp;".$pay_term[$pay_term_id];?>
            </td>
        </tr>
        <tr>
            <td width="200">
            CARRIER : <? echo $carrier;?>
            </td>
            <td width="119">
            SHIP MODE : <? echo $shipment_mode[$shipping_mode];?>
            </td>
            <td width="319"> 
             VESSEL NAME: <? echo $mother_vessel;?>
            </td>
        </tr>
        
        <tr style="font-size:small; font-weight:bold" align="center">
            <td width="80"><u>Port OF Loading:-</u><? echo $port_of_loading; ?></td>
            <td width="70"><u>Port OF Dischage:-</u><? echo $port_of_discharge;?> </td>
            <td width="268"><u>Final Destination:-</u><br/><? echo "&nbsp;&nbsp;".$place_of_delivery; ?></td>
            <td width="140" title="Inco Term from LC OR INVOICE">SHIPPING TERMS:<br/><? echo "&nbsp;&nbsp;".$incoterm[$inco_term].",".$inco_term_place; ?></td>
            <td width="80"><u>Country of Origin:-</u><?  echo "&nbsp;&nbsp;Bangladesh";?></td>
        </tr>
        
        <tr style="font-size:small; font-weight:bold" align="center">
            <td width="80"> ITEM DESCRIPTION</td>
            <td width="30">DEL. NO</td>
            <td width="40">CASE NO</td>
            <td width="88">STYLE / ARTICLE NO</td>
            <td width="80">PO NO</td>
            <td width="100">AC. PO NO</td>
            <td width="70">QNTY PCS/PACK</td>
            <td width="70">UNIT PRICE US$</td>
            <td width="80">TOTAL AMOUNT</td>
        </tr>
        
        <?
		foreach($result as $row)
		{
			?>
            <tr style="font-size:small">
            	<?
				if($i==1)
				{
					?> 
                	<td width="80" rowspan="<? echo $row_span; ?>" valign="top"><?  echo $item_description; ?></td>
                	<?
                }
				?>
                <td width="30" align="center"><?  echo $delv_no; ?></td>
                <td width="40"></td>
                <td width="88"><? echo $row[csf('style_ref_no')]; ?></td>
                <td width="80"><? echo $row[csf("po_number")]; ?></td>
                <td width="100"><? echo $acc_po_num_arr[$row[csf("po_breakdown_id")]]; ?></td>
                <td width="50" align="right"><? echo number_format($row[csf('current_invoice_qnty')],0,".",",");  ?></td>
                <td width="20" align="right"><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></td>
                <td width="70" align="right"><?  echo number_format($row[csf("current_invoice_rate")],2); ?></td>
                <td width="20" align="right">US$</td>
                <td width="60" align="right"><? echo number_format($row[csf("current_invoice_value")],2,".",","); ?></td>
            </tr>
            <?
			$total_value+=$row[csf("current_invoice_value")];
			$total_qnty+=$row[csf("current_invoice_qnty")]*$setQtyArr[$row[csf('po_breakdown_id')]];
			$last_uom=$unit_of_measurement[$row[csf('order_uom')]];
			$total_po_carton_qnty+=$carton_arr[$row[csf('po_breakdown_id')]];
			$i++;
		}
		?>
        <tr>
            <td width="418" align="right">Total</td>
            <td width="70" align="right"><? echo number_format($total_qnty,0,".",",")." Pcs" ?></td>
            <td width="70"></td>
            <td width="80" align="right"><? echo "US$&nbsp;&nbsp;".number_format($total_value,2,".",","); ?></td>
        </tr>
        
        <tr>
            <td width="418" align="right">
            Deduction
            </td>
            <td width="70" align="right"></td>
            <td width="70"></td>
            <td width="80" align="right">
            <? echo number_format($total_discount,2,".",","); ?>
            </td>
        </tr>
        
        <tr>
            <td width="418" align="right">
            Net Total
            </td>
            <td width="70" align="right"></td>
            <td width="70"></td>
             <td width="80" align="right">
            <? echo  "US$&nbsp;&nbsp;".number_format($net_invo_value,2,".",","); ?>
            </td>
        </tr>
        
        <tr>
            <td width="638">
            SAY: <? echo number_to_words(def_number_format($net_invo_value,2,""),"USD", "CENTS");?>
            </td>
        </tr>
        <tr>
            <td width="218">
            FOR <? echo strtoupper($company_name);?>
            <br/>
            <br/>
            <br/>
            <br/>
            <br/>
            <br/>
            <br/>
            <br/>
            Authorized Signature
            <br/>
            <br/>
            CAT. # <? echo  $category_no ;?><br/>
            HS. CODE : <? echo  $hs_code ;?>
            </td>
            <td width="210">
            <br/>
            <br/>
            <br/>
            <br/>
            TOTAL QTY&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;<? echo number_format($total_qnty,0,".",","); ?> PCS<br/>
            TOTAL CTN&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;<? echo number_format($total_carton_qnty,0,".",","); ?> CTNS<br/>
            TOTAL N. WT&nbsp;&nbsp;:&nbsp;<? echo number_format($net_weight,2); ?> KG<br/>
            TOTAL G. WT&nbsp;&nbsp;:&nbsp;<? echo number_format($gross_weight,2); ?> KG<br/>
            TOTAL VOL&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;<? echo number_format($cbm_qnty,2); ?> CBM<br/>
            </td>
            <td width="210">
            <strong><u>SHIPPING MARK:</u></strong><br/>
             SUPPLIER: Li & Fung<br/>
             SUPPLIER NO:<br/>
             ITEM NO:<br/>
             CONTARCT NO:<br/>
             SIZE:<br/>
             QTY:<br/>
             CARTON NO:<br/>
             GROSS WEIGHT:<br/>
             NET WEIGHT:<br/>
            
            </td>
        </tr>
    </table>
	<?
	$HTM=ob_get_contents();
	ob_end_clean();
	$invoice=new invoice($HTM,$header,'');
	?>