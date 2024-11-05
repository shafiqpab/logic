    <?	
    require('maindata.php');
	$i=1;
    ob_start();
	?>
    <table cellpadding="2" border="1">
        
        <tr>
        <td width="319"> 
        <br/>
       <strong><u>For Account & Risk Messrs:</u></strong><br/>
        <?
        echo  $applicant."<br/>";
	    echo  $applicantAddress;
	    ?>
        </td>
        <td width="319" rowspan="2">
        <br/>
        <table border="0">
        <tr>
        <td width="80">Invoice No.</td><td width="120"> : <?php echo $invoice_no;  ?> </td><td width="119"> Date: <? echo change_date_format($invoice_date);?></td>
        </tr>
        <tr>
        <td width="80">EXP No.</td><td width="120"> : <?php echo $exp_form_no; ?> </td><td width="119"> Date: <? if($exp_form_date!="" && $exp_form_date!="0000-00-00" ) echo change_date_format($exp_form_date);?></td>
        </tr>
        <tr>
        <td width="80">L/C. No.</td><td width="120"> : <?php echo $lc_sc_no; ?> </td><td width="119"> Date: <? echo $lc_sc_date;?></td>
        </tr>
        <tr>
        <td width="80">B/L. No.</td><td width="120"> : <?php echo $bl_no; ?> </td><td width="119"> Date: <?  if($bl_date!="" && $bl_date!="0000-00-00") echo change_date_format($bl_date); ?></td>
        </tr>
        <tr>
        <td width="80">SHIPPING TERMS</td><td width="239"> : <? echo "&nbsp;&nbsp;".$incoterm[$inco_term].",".$inco_term_place; ?></td>
        </tr>
        <tr>
        <td width="80">POART OF LOADING</td><td width="239"> :  <? echo $port_of_loading; ?></td>
        </tr>
         <tr>
        <td width="80">DESTINATION</td><td width="239"> : <? echo "&nbsp;&nbsp;".$place_of_delivery; ?></td>
        </tr>
        <tr>
        <td width="80">COUNTRY OF ORIGIN</td><td width="239"> : BANGLADESH</td>
        </tr>
        <tr>
        <td width="80">VESSEL</td><td width="239"> :  <? echo $mother_vessel;?></td>
        </tr>
        <tr>
        <td width="80">CONTAINER NO</td><td width="239"> : xxx <? //echo $mother_vessel;?></td>
        </tr>
        <tr>
        <td width="319">
        <br/>
        <br/>
        <br/>
         Negotiating Bank&nbsp;: <br/>
		 <? echo $negotiating_bank_text ?>
        </td>
        </tr>
        <tr>
        <td width="319">
        <br/>
        <br/>
        <br/>
         Buyer Bank Details&nbsp;: <br/>xxx
		 <? //echo $negotiating_bank_text ?>
        </td>
        </tr>
        </table>
         
        </td>
        </tr>
        
        
        
        
        
        <tr>
        <td width="319"> 
      <br/>
        <strong><u>Consignee/Notify Party:</u></strong><br/>
        <?
		echo "1) &nbsp;&nbsp;".$buyer_name_arr[$consignee]["buyer_name"]."<br/>";
	    echo "&nbsp;&nbsp;".$buyer_name_arr[$consignee]["address_1"]."<br/><br/>";
	    echo "2) &nbsp;&nbsp;".$buyer_name_arr[$notifying_party]["buyer_name"]."<br/>";
	    echo "&nbsp;&nbsp;".$buyer_name_arr[$notifying_party]["address_1"];
		?>
        </td>
        </tr>
        
        
        
        
        
        <tr style="font-size:small; font-weight:bold" align="center">
        <td width="162">
        SHIPPING MARKS
        </td>
      
        <td width="200" >
        DESCRIPTION OF GOODS
        </td>
        <td width="58">
         No of Ctns
        </td>
        <td width="80">
         Quantity IN PCS/SET
        </td>
        <td width="58" > 
        Unit Price <br />fob per pcs
        </td>
        <td width="80" > 
        AMOUNT <br />(UUS$)
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
            CAMAIEU.<br/>
            FICHE COLIS.<br/>
            ORDER NO.<? //echo $row[csf("po_number")]; ?><br/>
            NUMBER OF CARTON:<? //echo $art_num_arr[$row[csf("po_breakdown_id")]]; ?><br/>
            NUMBER OF PCS:<br/>
            GROSS WEIGHT:<br/>
            GROSS WEIGHT:<br/>
            </td>
            <td width="200" align="left" style="border-right: 1px solid #000">
            <br/>
            <? echo $order_la_data[$row[csf("po_breakdown_id")]]["fabric_description"] ?><br/>
            <? echo implode(",",$itemIdArr[$row[csf('po_breakdown_id')]]);?><br/>
            STYLE NAME :xxx<? //echo $row[csf("style_ref_no")]; ?><br/>
            STYLE NO :<? echo $row[csf("style_ref_no")]; ?><br/>
            COLOR CODE :xxx<? //echo $row[csf("style_ref_no")]; ?><br/>
            ORDER NO :<? echo $row[csf("po_number")]; ?><br/>
            ARTICLE NO :<? echo $art_num_arr[$row[csf("po_breakdown_id")]]; ?><br/>
            CAT NO :<? echo $order_la_data[$row[csf("po_breakdown_id")]]["category_no"]; ?><br/>
            H.S CODE :<? echo $order_la_data[$row[csf("po_breakdown_id")]]["hs_code"]; ?><br/><br/>
            
            </td>
            <td width="58" align="right" style="border-right: 1px solid #000">
            <? echo number_format($carton_arr[$row[csf('po_breakdown_id')]],0,".",","); ?>
            </td>
            <td width="80" align="right" style="border-right: 1px solid #000">
            <? echo $row[csf('current_invoice_qnty')];?>
            </td>
            <td width="58" align="right" style="border-right: 1px solid #000"> 
            $&nbsp;&nbsp;&nbsp;&nbsp; <? echo $row[csf('current_invoice_rate')];?>
            </td>
            <td width="80" align="right"> 
            $&nbsp;&nbsp;&nbsp;&nbsp;<? echo $row[csf('current_invoice_value')];?>
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
        <td width="58" align="right">
        <? echo $total_carton_qnty; ?>
        </td>
        <td width="80" align="right">
        <? echo number_format($total_qnty,0,".",",")." Pcs" ?>
        </td>
        <td width="138" align="right"><? echo "$&nbsp;&nbsp;".number_format($total_value,2,".",","); ?></td>
        </tr>
        
        <tr>
        <td width="362" align="right">
        Discount :
        </td>
        <td width="58" align="right">
        <? echo $total_carton_qnty; ?>
        </td>
        <td width="80" align="right">
        <? echo number_format($total_qnty,0,".",",")." Pcs" ?>
        </td>
        <td width="138" align="right"><? echo "$&nbsp;&nbsp;".number_format($total_discount,2,".",","); ?></td>
        </tr>
        
        <tr>
        <td width="362" align="right">
        Net Total :
        </td>
        <td width="58" align="right">
        <? echo $total_carton_qnty; ?>
        </td>
        <td width="80" align="right">
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
        <td width="142">TOTAL PCS</td>
        <td width="40">:</td> 
        <td width="80" align="right"><? echo number_format($total_qnty,0,".",","); ?> Pcs</td> 
        <td width="142"></td>
        <td width="100" align="right" style="border:none" rowspan="6">
        
        </td>
        </tr>
        <tr>
        <td width="142">TOTAL COATONS</td>
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
        <td width="142">MEAS</td>
        <td width="40">:</td> 
        <td width="80" align="right"><? echo $total_measurment; ?> </td> 
        <td width="100"></td>
        </tr>
        </table>
	<?
	$HTM=ob_get_contents();
	ob_end_clean();
	$invoice=new invoice($HTM,$header,'',true,true,true);
	?>