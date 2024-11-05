    <?
	require('maindata.php');
	$i=1;
    ob_start();
	?>
    
     <table border="0" cellpadding="2">
        
        <tr>
        <td width="319"></td>
        <td width="319" align="right">EXPORT REGD.NO.RA- <? echo $erc_no;?></td> 
        </tr>
        <tr>
        </table>
    <table cellpadding="2" border="1">
        <tr>
        <td width="319" rowspan="2"> 
         <br/>
       <strong><u>For Account & Risk Messrs:</u></strong><br/>
        <?
        echo  $applicant."<br/>";
	    echo  $applicantAddress;
	    ?>
        </td>
        <td width="319">
        <br/>
        No & Date of invoice<br/>
        <?php echo $invoice_no;  ?><br/>
        DT: <? echo change_date_format($invoice_date);?>
        </td>
        </tr>
        <tr>
        <td width="319" title="Issuing Bank from LC">
        <br/>
         No & Date of Contract:<br/>
        <?php echo $lc_sc_no; ?> DT: <? echo $lc_sc_date;?>
        </td>
        </tr>
        
        <tr>
        <td width="319" rowspan="2"> 
        <br/>
        Notify Party<br/>
        <?
		echo "1.&nbsp;".$buyer_name_arr[$consignee]["buyer_name"]."<br/>";
	    echo "&nbsp;&nbsp;".$buyer_name_arr[$consignee]["address_1"]."<br/>";
	    echo "2.&nbsp;".$buyer_name_arr[$notifying_party]["buyer_name"]."<br/>";
	    echo "&nbsp;&nbsp;".$buyer_name_arr[$notifying_party]["address_1"];
		?>
        </td>
          <td width="319"> 
        <br/>
        Contract Issue:<br/>
         <?
        echo  $applicant."<br/>";
	    echo  $applicantAddress;
	    ?>
        </td>
        </tr>
        <tr>
      
        <td width="319">
        <br/>
        Remarks:<br/>
        HAWB NO:<?php echo $bl_no;  ?>   DATE: <?  if($bl_date!="" && $bl_date!="0000-00-00") echo change_date_format($bl_date); ?>
        </td>
        </tr>
        <tr align="center">
        <td width="160">
        Port OF Loading<br/>
        <? echo $port_of_loading; ?>
        </td>
        <td width="159">
         Final Destination<br/>
          <? echo "&nbsp;&nbsp;".$place_of_delivery; ?>
        </td>
        <td width="160"> 
        CARRIER <br/> 
		<? echo $carrier;?>
        </td>
         <td width="159"> 
        Date of Sailing<br/>
        <?  if($etd!="" && $etd!="0000-00-00") echo change_date_format($etd);?>
        </td>
        </tr>
        
        <tr style="font-size:small; font-weight:bold" align="center">
        <td width="160">
        Marks and No. of packages
        </td>
        <td width="159">
        Description of goods
        </td>
        <td width="100"> 
         Quantity <br/> IN PCS
        </td>
        <td width="119">
        UNIT PRICE <br/>CFR<br/> NUERNBERG <br/>GERMANY
        </td>
         <td width="100">
        Amount <br/>IN U.S<br/> DOLLAR
        </td>
        </tr>
        
        <?
		foreach($result as $row)
		{
			?>
            <tr style="font-size:small">
            	
                <td width="160">
				<? echo  $applicant."<br/>";?>
                Order Number:<? echo $row[csf("po_number")]; ?> <br/>
                Article Number:<? echo $art_num_arr[$row[csf("po_breakdown_id")]]; ?> <br/>
                Lot Number:<? //echo $row[csf("po_number")]; ?> <br/>
                Lot Sorting:<? //echo $row[csf("po_number")]; ?> <br/>
                Content in  Pieces:<? //echo $row[csf("po_number")]; ?> <br/>
                Carton Number:<? //echo $row[csf("po_number")]; ?> <br/>
                Gross Weight:<? //echo $row[csf("po_number")]; ?> <br/>
                Net Weight:<? //echo $row[csf("po_number")]; ?> <br/>
                Carton Measurement:<? //echo $row[csf("po_number")]; ?> 
                </td>
                <td width="159">
                <? echo $order_la_data[$row[csf("po_breakdown_id")]]["fabric_description"]; ?><br/>
				<? echo implode(",",$itemIdArr[$row[csf('po_breakdown_id')]]); ?><br/>
                ORDER NO :<? echo $row[csf("po_number")]; ?><br/>
                CAT NO :<? echo $order_la_data[$row[csf("po_breakdown_id")]]["category_no"]; ?><br/>
                H.S CODE NO :<? echo $order_la_data[$row[csf("po_breakdown_id")]]["hs_code"]; ?><br/><br/>
                </td>
                <td width="100" align="right"><? echo $row[csf('current_invoice_qnty')]*$setQtyArr[$row[csf('po_breakdown_id')]];?> PCS</td>
                <td width="119" align="right"> <? echo $row[csf('current_invoice_rate')];?></td>
                <td width="100" align="right"> <? echo $row[csf('current_invoice_value')];?></td>
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
        <td width="319" align="right">
       	Total
        </td>
        <td width="100" align="right">
      	<? echo number_format($total_qnty,0,".",",")." Pcs" ?>
        </td>
        <td width="119" align="right">
       
        </td>
        <td width="100" align="right">
        $ <? echo number_format($total_value,2,".",",") ?>
        </td>
        </tr>
        
        <tr>
        <td width="319" align="right">
       	Discount
        </td>
        <td width="100" align="right"></td>
        <td width="119" align="right"></td>
        <td width="100" align="right">
        $ <? echo number_format($total_discount,2,".",",") ?>
        </td>
        </tr>
        
        <tr>
        <td width="319" align="right">
       	Net Total
        </td>
        <td width="100" align="right"></td>
        <td width="119" align="right"></td>
        <td width="100" align="right">
        $ <? echo number_format($net_invo_value,2,".",",") ?>
        </td>
        </tr>
        
        <tr>
        <td width="638">
        SAY: <? echo number_to_words(def_number_format($net_invo_value,2,""),"USD", "CENTS");?>
        </td>
        </tr>
    </table>
    <br/>
    <table border="0" cellpadding="2">
        
        <tr>
        <td width="319">TOTAL:<? echo number_format($total_carton_qnty,0,".",","); ?> CARTONS</td>
        <td width="319"></td> 
        </tr>
        <tr>
        <td width="319">COUNTRY OF ORIGIN:BANGLADESH</td>
        <td width="319"></td> 
        </tr>
        <tr>
        <td width="638">EXP FORM NO :<?php echo $exp_form_no; ?> DT: <? if($exp_form_date!="" && $exp_form_date!="0000-00-00" ) echo change_date_format($exp_form_date);?></td>
        </tr>
        
        <tr>
        <td width="159">N. WEIGHT</td>
        <td width="160"><? echo number_format($net_weight,2); ?> KGS</td>
        <td width="319"></td> 
        </tr>
        <tr>
        <td width="159">G. WEIGHT</td>
        <td width="160"><? echo number_format($gross_weight,2); ?> KGS</td>
        <td width="319"></td> 
        </tr>
        <tr>
        <td width="159">CBM</td>
        <td width="160"><? echo number_format($cbm_qnty,2); ?></td>
        <td width="319"></td> 
        </tr>
        </table>
	<?
	$HTM=ob_get_contents();
	ob_end_clean();
	$invoice=new invoice($HTM,$header,'',true,true,true);
	?>