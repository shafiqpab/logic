    <?	
    require('maindata.php');
	$i=1;
    ob_start();
	?>
    <table cellpadding="2" border="1">
        
        <tr>
        <td width="319"> 
        <br/>
        <table border="0" cellpadding="2">
        <tr>
        <td width="118" style="border-bottom:1px solid #000; border-right:1px solid #000"><b>SHIPPERS/EXPORTER:</b></td>
        <td width="200">
        </td>
        </tr>
        <tr>
        <td width="318">
        <br/>
        <b><?php echo $company_name; ?></b>
        <?php
        if($city!="")  $comany_details.= "<br>".$city.", ";
        if($country_id!="")  $comany_details.="<br>".$country_name.".";
        echo  $comany_details;
        ?>
        </td>
        </tr>
        </table>
        </td>
        <td width="319">
         <br/>
        <table border="0">
        <tr>
        <td width="315"><u>Invoice Number & Date: </u></td>
        </tr>
        <tr>
        <td width="150" style="border-bottom:1px solid #000"><?php echo $invoice_no;  ?></td>
        <td width="15" style="border-bottom:1px solid #000"></td>
        <td width="150" style="border-bottom:1px solid #000">Date : <? echo change_date_format($invoice_date);?></td>
        </tr>
        <tr>
        <td width="200"> <strong><u>L/C Issueing Bank:</u></strong><br/>
        <? echo "&nbsp;&nbsp;".$issuing_bank_name;  ?></td>
        <td width="115"></td>
        </tr>
        
        </table>
        </td>
        </tr>
        
        <tr>
        <td width="319"> 
        <br/>
        <table border="0" cellpadding="2">
        <tr>
        <td width="118" style="border-bottom:1px solid #000; border-right:1px solid #000"><b> ACCOUNT & RISK OF:</b></td>
        <td width="200" style="border-bottom:1px solid #000;">
        </td>
        </tr>
        <tr>
        <td width="318">
        <br/>
       <?
        echo  $applicant."<br/>";
	    echo  $applicantAddress;
		?>
        </td>
        </tr>
        </table>
        </td>
         
        <td width="319">
        <br/>
        <table border="0">
        <tr>
        <td width="315" style="border-bottom:1px solid #000">
        <br />
        <u>REMARKS:</u>
        <br/>
        COUNTRY OF ORIGIN : BANGLADESH.
        </td>
        </tr>
        <tr>
        <td width="100" style="border-bottom:1px solid #000">EXP REG NO:</td>
        <td width="85" style="border-bottom:1px solid #000"><? echo $erc_no;?></td>
        <td width="50" style="border-bottom:1px solid #000"></td>
        <td width="80" style="border-bottom:1px solid #000"><?  //if($bl_date!="" && $bl_date!="0000-00-00") echo change_date_format($bl_date); ?></td>
        </tr>
        
        <tr>
        <td width="100">EXP NO</td>
        <td width="85">:<?php echo $exp_form_no; ?></td>
        <td width="50">Date</td>
        <td width="80">:<? if($exp_form_date!="" && $exp_form_date!="0000-00-00" ) echo change_date_format($exp_form_date);?></td>
        </tr>
       <tr>
        <td width="100" >L/C NO</td>
        <td width="85" >: <?php echo $lc_sc_no; ?></td>
        <td width="50" >Date</td>
        <td width="80" >:<? echo $lc_sc_date;?></td>
        </tr>
        
        </table>
        </td>
        </tr>
        <tr>
        <td width="319"> 
        <br/>
        <table border="0" cellpadding="2">
        <tr>
        <td width="118" style="border-bottom:1px solid #000; border-right:1px solid #000"><b> NOTIFY PARTY:</b></td>
        <td width="200">
        </td>
        </tr>
        <tr>
        <td width="318">
        <br/>
        <?
        echo "&nbsp;&nbsp;".$buyer_name_arr[$notifying_party]["buyer_name"]."<br/>";
	    echo "&nbsp;&nbsp;".$buyer_name_arr[$notifying_party]["address_1"];
        ?>
        <br/>
        <br/>
        <u>DELEVERY TERMS:</u>
        <br/>
        <? echo "&nbsp;&nbsp;".strtoupper($incoterm[$inco_term]).",".strtoupper($inco_term_place).", BANGLADESH (AS PER INCOTERM 2010)" ?><br/>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;PSID NO:xxxx
        </td>
        </tr>
        </table>
        </td>
     
        <td width="319">
        <br/>
       <table border="0">
        <tr>
        <td width="150">MODE OF SHIP</td>
        <td width="165" style="border-bottom:1px solid #000">: <? echo $shipment_mode[$shipping_mode];?></td>
        </tr>
        
         <tr>
        <td width="150">HB/L NO</td>
        <td width="165" style="border-bottom:1px solid #000">: <?php echo $bl_no;  ?></td>
        </tr>
       <tr>
        <td width="150">VSL NO</td>
        <td width="165" style="border-bottom:1px solid #000">: <? echo $mother_vessel;?></td>
        </tr>
        <tr>
        <td width="150">CONTAINER NO</td>
        <td width="165" style="border-bottom:1px solid #000">:<?php echo $co_no;  ?></td>
        </tr>
        <tr>
        <td width="150">TERMS OF PAYMENT</td>
        <td width="165">: <? echo $pay_term[$pay_term_id];?></td>
        </tr>
        </table>
        </td>
        </tr>
        
        
        <tr>
        <td width="319"> 
        <br/>
        <table border="0" cellpadding="2">
        <tr>
        <td width="118" style="border-bottom:1px solid #000; border-right:1px solid #000"><b> NEGOTIATING BANK:</b></td>
        <td width="200">
        </td>
        </tr>
        <tr>
        <td width="318">
        <br/>
        <? echo $negotiating_bank_text ?>
        </td>
        </tr>
        </table>
        </td>
     
        <td width="319">
        <br/>
        <u>SHIPMENT DETAILS:</u>
        <br/>
       <table border="0">
        <tr>
        <td width="150">PORT OF LANDING</td>
        <td width="165" style="border-bottom:1px solid #000">: <? echo $port_of_loading;?></td>
        </tr>
        
         <tr>
        <td width="150">PORT OF DISCHARGE</td>
        <td width="165" style="border-bottom:1px solid #000">:  <? echo $port_of_discharge;?> </td>
        </tr>
       <tr>
        <td width="150">COUNTRY OF ORIGIN</td>
        <td width="165">: BANGLADESH</td>
        </tr>
       
        </table>
        </td>
        </tr>
        
        
        
        
        
        <tr style="font-size:small; font-weight:bold" align="center">
        <td width="162">
        SHIPPING MARKS
        </td>
      
        <td width="200" >
        DESCRIPTION OF GOODS
        </td>
        <td width="138">
         QUANTITY IN PCS/PACK
        </td>
        <td width="58" > 
        UNIT PRICE <br />US DOLLAR
        </td>
        <td width="80" > 
       TOTAL AMOUNT <br />
       <? echo strtoupper($incoterm[$inco_term]).",".strtoupper($inco_term_place); ?>
        </td>
        </tr>
        <tr>
        <td width="638">
        <br/>
        <table border="0" cellpadding="2">
        <?
		foreach($result as $row)
		{
			$hs_code_arr_cat[$order_la_data[$row[csf("po_breakdown_id")]]["hs_code"]]=$order_la_data[$row[csf("po_breakdown_id")]]["category_no"];
			$hs_code_arr_qty[$order_la_data[$row[csf("po_breakdown_id")]]["hs_code"]]+=$row[csf("current_invoice_qnty")];
			?>
            <tr style="font-size:small; font-weight:bold">
            <td width="160" style="border-right: 1px solid #000">
            <br/>
            DESTINATION: NEW YORKER.<br/>
            BRAUNCCHWEIG.<br/>
            ORDER NO.<? echo $row[csf("po_number")]; ?><br/>
            ART:<? echo $art_num_arr[$row[csf("po_breakdown_id")]]; ?><br/>
            QTY:<br/>
            BOX NR:<br/>
            SORTIMENT A:<br/>
            COLOUR/SIZE:<br/>
            
            <br/>
            <? echo $row[csf('style_ref_no')];?>
            </td>
            <td width="200" align="left" style="border-right: 1px solid #000">
            <br/>
            <? echo implode(",",$itemIdArr[$row[csf('po_breakdown_id')]]).",".$order_la_data[$row[csf("po_breakdown_id")]]["fabric_description"]; ?><br/>
            ORDER NO :<? echo $row[csf("po_number")]; ?><br/>
            ARTICLE NO :<? echo $art_num_arr[$row[csf("po_breakdown_id")]]; ?><br/>
            CAT NO :<? echo $order_la_data[$row[csf("po_breakdown_id")]]["category_no"]; ?><br/>
            H.S CODE NO :<? echo $order_la_data[$row[csf("po_breakdown_id")]]["hs_code"]; ?><br/><br/>
            GSP APPLICABLE :YES
            
            </td>
           
            <td width="138" align="right" style="border-right: 1px solid #000">
            <? echo $row[csf('current_invoice_qnty')];?>
            </td>
            <td width="58" align="right" style="border-right: 1px solid #000"> 
            <? echo $row[csf('current_invoice_rate')];?>
            </td>
            <td width="80" align="right"> 
            <? echo $row[csf('current_invoice_value')];?>
            </td>
            </tr>
            <?
			$total_value+=$row[csf("current_invoice_value")];
			$total_qnty+=$row[csf("current_invoice_qnty")]*$setQtyArr[$row[csf('po_breakdown_id')]];
			$last_uom=$unit_of_measurement[$row[csf('order_uom')]];
			$total_po_carton_qnty+=$carton_arr[$row[csf('po_breakdown_id')]];
			$hs_code_arr_cat[$order_la_data[$row[csf("po_breakdown_id")]]["hs_code"]]=$order_la_data[$row[csf("po_breakdown_id")]]["category_no"];
			$hs_code_arr_qty[$order_la_data[$row[csf("po_breakdown_id")]]["hs_code"]]+=$row[csf("current_invoice_qnty")]*$setQtyArr[$row[csf('po_breakdown_id')]];
			$i++;
		}
		?>
        </table>
        </td>
        </tr>
        
        <tr>
        <td width="362" align="right">
        Total :
        </td>
        <td width="138" align="right">
        <? echo number_format($total_qnty,0,".",",")." Pcs" ?>
        </td>
        <td width="138" align="right"><? echo "$&nbsp;&nbsp;".number_format($total_value,2,".",","); ?></td>
        </tr>
        
        <tr>
        <td width="362" align="right">
        Discount :
        </td>
        <td width="138" align="right">
        <? echo number_format($total_qnty,0,".",",")." Pcs" ?>
        </td>
        <td width="138" align="right"><? echo "$&nbsp;&nbsp;".number_format($total_discount,2,".",","); ?></td>
        </tr>
        
        <tr>
        <td width="362" align="right">
        Net Total :
        </td>
        <td width="138" align="right">
        <? echo number_format($total_qnty,0,".",",")." Pcs" ?>
        </td>
        <td width="138" align="right"><? echo "$&nbsp;&nbsp;".number_format($net_invo_value,2,".",","); ?></td>
        </tr>
        
        
        <tr>
        <td width="638" align="">
        TOTAL US.DOLLARS: <? echo number_to_words(def_number_format($net_invo_value,2,""),"USD", "CENTS")." Only";?>
        </td>
        </tr>
    </table>
    <table border="0" cellpadding="2">
        
        <tr>
        <td width="142">TOTAL GARMENTS QTY</td>
        <td width="40">:</td> 
        <td width="80" align="right"><? echo number_format($total_qnty,0,".",","); ?> Pcs</td> 
        <td width="142"></td>
        <td width="100" align="right" style="border:none" rowspan="6">
        
        </td>
        </tr>
        <tr>
        <td width="142">TOTAL COATON  QTY</td>
        <td width="40">:</td> 
        <td width="80" align="right"><? echo number_format($total_carton_qnty,0,".",","); ?> Ctns</td> 
        <td width="100"></td>
        </tr>
        <tr>
        <td width="142">TOTAL NET WEIGHT</td>
        <td width="40">:</td> 
        <td width="80" align="right"><? echo number_format($net_weight,2); ?> Kgs</td> 
        <td width="100"></td>
        </tr>
        <tr>
        <td width="142">TOTAL GROSS WEIGHT</td>
        <td width="40">:</td> 
        <td width="80" align="right"><? echo number_format($gross_weight,2); ?> Kgs</td> 
        <td width="100"></td>
        </tr>
      
        <tr>
        <td width="142">TOTAL CBM</td>
        <td width="40">:</td> 
        <td width="80" align="right"><? echo number_format($cbm_qnty,2); ?> CBM</td> 
        <td width="100"></td>
        </tr>
         <tr>
        <td width="142">TOTAL MEASUREMENT</td>
        <td width="40">:</td> 
        <td width="80" align="right"><? echo $total_measurment; ?></td> 
        <td width="100"></td>
        </tr>
        </table>
	<?
	$HTM=ob_get_contents();
	ob_end_clean();
	$invoice=new invoice($HTM,$header,'',false,true,true);
	?>