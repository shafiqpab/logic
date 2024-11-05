    <?
	require('maindata.php');
	$i=1;
    ob_start();
	?>
    <table cellpadding="2" border="1">
    <tr><td width="638" align="center"> <strong style="font-size:large">COMMERCIAL INVOICE</strong></td></tr>
        <tr>
            <td width="219" rowspan="3"> 
                <br/>
                <b><u>Exporter/Manufacturer:</u></b>
                <br/>
                <b><?php echo $company_name; ?></b>
                <?php
                if($city!="")  $comany_details.= "<br>".$city.", ";
                if($country_id!="")  $comany_details.="<br>".$country_name.".";
                echo  $comany_details;
                ?>
            </td>
            <td width="419">
                <br/>
                Invoice No & Date <br/>
                <table border="0" cellpadding="2">
                <tr>
                <td width="137"><?php echo $invoice_no;  ?></td>
                <td width="137">DT: <? echo change_date_format($invoice_date);?></td>
                <td width="137" align="center">Exporter's Ref No <br/><? echo $erc_no;?></td>
                </tr>
                </table>
            </td>
        </tr>
        <tr>
        <td width="419" title="Issuing Bank from LC">
        <br/>
        <table border="0" cellpadding="2">
        <tr>
        <td width="219">EXP NO.<?php echo $exp_form_no; ?></td>
        <td width="200">DT: <? if($exp_form_date!="" && $exp_form_date!="0000-00-00" ) echo change_date_format($exp_form_date);?></td>
        </tr>
        </table>
        </td>
        </tr>
        <tr>
        <td width="419" title="Issuing Bank from LC">
        <br/>
        <table border="0" cellpadding="2">
        <tr>
        <td width="418">Other Reference (s)</td>
        </tr>
        </table>
        </td>
        </tr>
        
        <tr>
        <td width="219" title="Issuing Bank from LC">
        </td>
        <td width="419" title="Issuing Bank from LC">
        <br/>
       <table border="0" cellpadding="2">
        <tr>
        <td width="219">ASN NO.XXX</td>
        <td width="200">SO: XXX</td>
        </tr>
        </table>
        </td>
        </tr>
        
        <tr>
        <td width="219"> 
        <br/>
        Consignee:<br/>
        <?
		echo $consignee;
		echo "&nbsp;&nbsp;".$buyer_name_arr[$consignee]["buyer_name"]."<br/>";
	    echo "&nbsp;&nbsp;".$buyer_name_arr[$consignee]["address_1"]."<br/>";
		?>
        </td>
        <td width="419"> 
        <br/>
        <table border="0" cellpadding="2">
        <tr>
        <td width="219">B/L NO: <?php echo $bl_no; ?></td>
        <td width="200">Date:<?  if($bl_date!="" && $bl_date!="0000-00-00") echo change_date_format($bl_date); ?></td>
        </tr>
        <tr>
        <td width="419">Buyer (If other than consignee)</td>
        </tr>
        <tr>
        <td width="419"><br/><br/><br/>VESSEL NAME:<? echo $mother_vessel;?></td>
        </tr>
        </table>
        </td>
        </tr>
        <tr>
        <td width="80">
        Pre-carriage by<br/>
        xxxx
        </td>
        <td width="139">
        Place of Receipt by<br/>
        xxx
        </td>
        <td width="219" align="center">
        Country of origin of Goods <br/>
        Bangladesh.
        </td>
        <td width="200" align="center">
        Country of Final Destination <br/>
        <? echo $place_of_delivery; ?>
        </td>
        </tr>
        
         <tr>
        <td width="80">
       Vessel/ Flight No:xxxx
        </td>
        <td width="139">
        VSL NAME:xxx<br/>
       &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Port of Loading<br/>
        <? echo $port_of_loading; ?>
        </td>
        <td width="419" align="center">
        Terms of Delivery and Payment <br/>
       <? echo $incoterm[$inco_term].",".$inco_term_place."&nbsp;&nbsp;".$pay_term[$pay_term_id]; ?>
        </td>
        </tr>
        
        <tr>
        <td width="80">
        <br/>
        Port of Discharge <br/>
        <? echo $port_of_discharge;?> 
        </td>
        <td width="139">
        <br/>
        Final of Destination<br/>
        <? echo $place_of_delivery; ?>
        </td>
        <td width="419">
        <br/>
        L/C NO:<?php echo $lc_sc_no; ?><br/>
        DATED:<? echo $lc_sc_date;?>
        </td>
        </tr>
        
        
        <tr style="font-size:small; font-weight:bold" align="center">
        <td width="80">
        Marks & Nos
        </td>
        <td width="139">
        No & Kind of Packages
        </td>
        <td width="189"> 
        Description of goods
        </td>
        <td width="100">
       Quantity
        </td>
         <td width="60">
       Unit Price
        </td>
        <td width="70">
        Amount
        </td>
        </tr>
        
        <tr style="font-size:small; font-weight:bold">
        <td width="80" rowspan="<? echo $row_span+1; ?>">
        <br/>
        Container No<br/>
        RNA RESOURCES<br/>
        GROUP LTD.<br/>
        DISTRIBUTOR:<br/>
        BRAND:<br/>
        SEASON:<br/>
        VENDOR PO:<br/>
        BUYER CODE:<br/>
        SIZE:<br/>
        QUANTITY:<br/>
        LOCATION: UAE <br/>
        CARTPN:<br/>
        NET WT: (KGS)<br/>
        CARTON:<br/>
        NET WT (KGS)<br/>
        GROSS WT (KGS)<br/>
        DIMENSION:<br/>
        ORIGIN:
        </td>
        <td width="50">
        PO NO
        </td>
        <td width="89">
        Buyer Code/Style
        </td>
        <td width="139"> 
        Description
        </td>
         <td width="50"> 
       HS CODE NO
        </td>
        <td width="50">
       IN PCS
        </td>
        <td width="50">
       IN CTN
        </td>
         <td width="60">
       FOB IN USD
        </td>
        <td width="70">
        Amount
        </td>
        </tr>
        
        <?
		foreach($result as $row)
		{
			?>
            <tr style="font-size:small; font-weight:bold" align="center">
        <td width="50">
        <? echo $row[csf("po_number")]; ?>
        </td>
        <td width="89">
        <? echo $row[csf('style_ref_no')]; ?>
        </td>
        <td width="139"> 
       <?  echo implode(",",$itemIdArr[$row[csf('po_breakdown_id')]]);?>
        </td>
         <td width="50" align="right"> 
      <? echo $order_la_data[$row[csf("po_breakdown_id")]]["hs_code"]; ?>
        </td>
        <td width="50" align="right">
       <? echo $row[csf('current_invoice_qnty')]*$setQtyArr[$row[csf('po_breakdown_id')]];?>
        </td>
        <td width="50" align="right">
       <? echo number_format($carton_arr[$row[csf('po_breakdown_id')]],0,".",","); ?>
        </td>
         <td width="60" align="right">
       <?  echo number_format($row[csf("current_invoice_rate")],2); ?>
        </td>
        <td width="70" align="right">
       <? echo number_format($row[csf("current_invoice_value")],2,".",","); ?>
        </td>
        </tr>
            <?
			$total_value+=$row[csf("current_invoice_value")];
			$total_qnty+=$row[csf("current_invoice_qnty")]*$setQtyArr[$row[csf('po_breakdown_id')]];
			$last_uom=$unit_of_measurement[$row[csf('order_uom')]];
			$total_po_carton_qnty+=$carton_arr[$row[csf('po_breakdown_id')]];
			$i++;
		}
		?>
        <tr style="font-size:small; font-weight:bold">
        <td width="80">
        
        </td>
        <td width="50">
        
        </td>
        <td width="89">
       
        </td>
        <td width="139"> 
        SB NO:
        </td>
         <td width="50"> 
      
        </td>
        <td width="50">
       
        </td>
        <td width="50">
       
        </td>
         <td width="60">
       
        </td>
        <td width="70">
       
        </td>
        </tr>
        <tr>
        <td width="408" align="right">
       	Total
        </td>
         <td width="50" align="right">
      	<? echo number_format($total_qnty,0,".",",") ?>
        </td>
        <td width="50" align="right">
      	<? //echo number_format($total_qnty,0,".",",")." Pcs" ?>
        </td>
        <td width="60" align="right"></td>
        <td width="70" align="right">
        $ <? echo number_format($total_value,2,".",",") ?>
        </td>
        </tr>
        
        <tr>
        <td width="408" align="right">
       	Discount
        </td>
        <td width="50" align="right"></td>
        <td width="50" align="right"></td>
        <td width="60" align="right"></td>
        <td width="70" align="right">
        $ <? echo number_format($total_discount,2,".",",") ?>
        </td>
        </tr>
        
        <tr>
        <td width="408" align="right">
       	Net Total
        </td>
        <td width="50" align="right"></td>
        <td width="50" align="right"></td>
        <td width="60" align="right"></td>
        <td width="70" align="right">
        $ <? echo number_format($net_invo_value,2,".",",") ?>
        </td>
        </tr>
        
        <tr>
        <td width="638">
        SAY: <? echo number_to_words(def_number_format($net_invo_value,2,""),"USD", "CENTS");?>
        </td>
        </tr>
         <tr>
        <td width="638">
        </td>
        </tr>
    </table>
	<?
	$HTM=ob_get_contents();
	ob_end_clean();
	$invoice=new invoice($HTM,$header,'',false,true,false);
	?>