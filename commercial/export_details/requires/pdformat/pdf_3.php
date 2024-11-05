    <?	
    require('maindata.php');
	$i=1;
    ob_start();
	?>
    <table cellpadding="2" border="1">
        <tr>
            <td width="638">
                <table border="0" cellpadding="2">
                    <tr>
                    <td width="311" rowspan="2">
                    <br/>
                    <b><u>SUPPLIER.</u></b><br/>
                    <b><?php echo $company_name; ?></b>
                    <?php
                    if($city!="")  $comany_details.= "<br>".$city.", ";
                    if($country_id!="")  $comany_details.="<br>".$country_name.".";
                    echo  $comany_details;
                    ?>
                    </td>
                    <td width="160" style="border-bpttom:1px solid #000;border-right:1px solid #000;border-left:1px solid #000;;border-bottom:1px solid #000">INDITEX CODE:</td>
                    <td width="158" style="border-bottom:1px solid #000">SEASON:</td>
                    </tr>
                    <tr>
                    <td width="160"></td>
                    <td width="158"></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
        <td width="319"> 
        <br/>
        <table border="0" cellpadding="2">
        <tr>
        <td width="318">
        <br/>
        <b><u>1) SHIPPERS/EXPORTER:</u></b>
        <br/>
        <b><?php echo $company_name; ?></b>
        <?php
        //if($city!="")  $comany_details.= "<br>".$city.", ";
        //if($country_id!="")  $comany_details.="<br>".$country_name.".";
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
        <td width="315" style="border-bottom:1px solid #000"><b><u>Invoice No.</u></b><?php echo $invoice_no;  ?><br/>Date: <? echo change_date_format($invoice_date);?></td>
        </tr>
        <tr>
        <td width="315" style="border-bottom:1px solid #000"><b><u>EXPORT L/C NO :</u></b><?php echo $lc_sc_no;  ?><br/>Date: <? echo change_date_format($lc_sc_date);?></td>
        </tr>
        </table>
        </td>
        </tr>
        
        <tr>
        <td width="319"> 
         <br/>
        <strong><u>2) BUYER/APPLICANT:</u></strong><br/>
        <?
         echo  $applicant."<br/>";
	     echo  $applicantAddress;
	    ?>
        </td>
         
        <td width="319">
        <br/>
            <table border="0">
            <tr>
        <td width="315" style="border-bottom:1px solid #000"><b><u>EXP No. :</u></b><?php echo $exp_form_no;  ?><br/>Date: <? if($exp_form_date!="" && $exp_form_date!="0000-00-00" ) echo change_date_format($exp_form_date);?></td>
        </tr>
        <tr>
        <td width="315"><b><u>L/C Issueing Bank: </u></b><br/><? echo "&nbsp;&nbsp;".$issuing_bank_name;  ?></td>
        </tr>
           </table>
        </td>
        </tr>
        <tr>
        <td width="319"> 
          <br/>
        <strong><u>3) NOTIFY PARTY:</u></strong><br/>
        <?
        echo "&nbsp;&nbsp;".$buyer_name_arr[$notifying_party]["buyer_name"]."<br/>";
	    echo "&nbsp;&nbsp;".$buyer_name_arr[$notifying_party]["address_1"];
        ?>
        </td>
        <td width="319" rowspan="2">
        <br/>
        <strong><u>CONSIGNED</u></strong><br/>
        <?
		echo "&nbsp;&nbsp;".$buyer_name_arr[$consignee]["buyer_name"]."<br/>";
	    echo "&nbsp;&nbsp;".$buyer_name_arr[$consignee]["address_1"]."<br/>";
		?>
        </td>
        </tr>
        
        <tr>
        <td width="159">
        <br/>
        <b><u>PROT OF LOADING: </u></b><br/>
        <? echo $port_of_loading; ?>
        </td>
        <td width="160">
        <br/>
        <b><u>FINAL DESTINATION: </u></b><br/>
        <? echo $place_of_delivery; ?>
        </td>
        </tr>
        <tr>
        <td width="159">
        <br/>
        <b><u>CARRIER: </u></b><br/>
        <? echo $carrier; ?>
        </td>
        <td width="160">
        <br/>
        <b><u> SAILING ON OR ABOUT</u></b><br/>
         &nbsp;&nbsp;
        </td>
        <td width="319"><b><u>12) REMARKS </u></b><? //echo $mother_vessel;?></td>
        </tr>
        
        <tr>
        <td width="319">
        <br/>
        <b><u> SHIPPING MARK</u></b><br/>
        <table border="0">
        <tr>
        <td width="150"><b>LEFTIES</b></td> <td width="150">DIVISION:</td>
        </tr>
        <tr>
        <td width="150">INDITEX STYLE NO:</td> <td width="150">INDITEX INVOICE NUMBER:</td>
        </tr>
        <tr>
        <td width="150">COLOR:</td> <td width="150">SUPPLIER:</td>
        </tr>
        <tr>
        <td width="150">SIZE:</td> <td width="150">ORDER NUMBER:</td>
        </tr>
        <tr>
        <td width="150">QUANTITY:</td> <td width="150">BARCODE:</td>
        </tr>
        <tr>
        <td width="300"><b>VESSEL NAME:<? echo $mother_vessel;?></b></td> 
        </tr>
        </table>
        
        </td>
        <td width="319"> 
        <br/> 
            <table border="0">
            <tr>
            <td width="315" style="border-bottom:1px solid #000"><b><u>13) TERMS OF DELEVERY: <?php echo $pay_term[$pay_term_id];  ?></u></b></td>
            </tr>
            <tr>
            <td width="315" style="border-bottom:1px solid #000"><b><u>14) EXP. REGN NO.</u></b> <?php //echo $lc_sc_no;  ?></td>
            </tr>
            <tr>
            <td width="315"><b><u>NEGOTIATING BANK OF SHIPPER/EXPORTER:</u></b><br/><? echo $negotiating_bank_text ?></td>
            </tr>
            <tr>
            <td width="150"><b><u>BL/NO:</u></b><? echo $bl_no ?></td><td width="150"><b><u>DATE:</u></b><?  if($bl_date!="" && $bl_date!="0000-00-00") echo change_date_format($bl_date); ?></td>
            </tr>
            </table>
        </td>
        </tr>
        <tr style="font-size:small; font-weight:bold" align="center">
        <td width="100">
        STYLE NO.
        </td>
        <td width="62" >
        ORDER NO
        </td>
        <td width="200" >
        DESCRIPTION
        </td>
        <td width="138">
         QUANTITY<br />
         PCS
        </td>
        <td width="58" > 
        Unit PRICE US$/PCS
        </td>
        <td width="80" > 
       AMOUNT US$
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
            <td width="98" style="border-right: 1px solid #000">
            <br/>
            <? echo $row[csf('style_ref_no')];?>
            </td>
            <td width="62" style="border-right: 1px solid #000">
            <br/>
            <? echo $row[csf("po_number")]; ?>
            </td>
            <td width="200" align="left" style="border-right: 1px solid #000">
            <br/>
            <? echo $order_la_data[$row[csf("po_breakdown_id")]]["fabric_description"]; ?><br/>
            <? echo implode(",",$itemIdArr[$row[csf('po_breakdown_id')]]); ?>
            
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
        <tr>
        <td width="98" style="border-right: 1px solid #000">
        </td>
        <td width="62" style="border-right: 1px solid #000">
        </td>
        <td width="200" style="border-right: 1px solid #000">
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <table border="0">
		   <? 
           foreach($hs_code_arr_qty as $key=>$value)
           {
			   ?>
               <tr>
               <td width="50">
               <br/>
               HS CODE
               </td>
               <td width="70"> :
                <? echo  $key;?>
               </td>
               <td width="80"><? echo $value; ?>PCS</td>
               </tr>
               <tr>
               <td width="50">
               <br/>
               CAT
               </td>
               <td width="70"> :
                <? echo $hs_code_arr_cat[$key];?>
               </td>
               <td width="80"></td>
               </tr>
               <?
           }
           ?>
           </table>
        </td>
        <td width="138" align="right" style="border-right: 1px solid #000">
        </td>
        <td width="58" style="border-right: 1px solid #000">
        </td>
        <td width="80" align="right">
        </td>
        </tr>
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
        <td width="58">
        </td>
         <td width="80" align="right">
        <? echo "US$&nbsp;&nbsp;".number_format($total_value,2,".",","); ?>
        </td>
        </tr>
        <td width="362" align="right">
       	Discount :
        </td>
         <td width="58" align="right"></td>
        <td width="80" align="right"></td>
        <td width="58"></td>
         <td width="80" align="right">
        <? echo number_format($total_discount,2,".",","); ?>
        </td>
        </tr>
        
        <tr>
        <td width="362" align="right">
       	Net Total :
        </td>
        <td width="58" align="right"></td>
        <td width="80" align="right"></td>
        <td width="58">
        </td>
        <td width="80" align="right">
        <? echo "US$&nbsp;&nbsp;".number_format($net_invo_value,2,".",","); ?>
        </td>
        </tr>
        <tr>
        <td width="638" align="center">
        SAY: <? echo "( ".number_to_words(def_number_format($net_invo_value,2,""),"USD", "CENTS")." Only )";?>
        </td>
        </tr>
       
        
        
        <tr style="border:none">
        <td width="362" style="border:none">
        <br/>
        <table border="0" cellpadding="2">
        <tr>
        <td width="100">NO OF CARTON</td>
        <td width="40">:</td> 
        <td width="80" align="right"><? echo number_format($total_carton_qnty,0,".",","); ?> CTNS</td> 
        <td width="142"></td>
        </tr>
        <tr>
        <td width="100">TOTAL QUANTITY</td>
        <td width="40">:</td> 
        <td width="80" align="right"><? echo number_format($total_qnty,0,".",","); ?> PCS</td> 
        <td width="142"></td>
        </tr>
        
        <tr>
        <td width="100">NET WEIGHT</td>
        <td width="40">:</td> 
        <td width="80" align="right"><? echo number_format($net_weight,2); ?> KGS</td> 
        <td width="142"></td>
        </tr>
        <tr>
        <td width="100">GROSS WEIGHT</td>
        <td width="40">:</td> 
        <td width="80" align="right"><? echo number_format($gross_weight,2); ?> KGS</td> 
        <td width="142"></td>
        </tr>
        <tr>
        <td width="100">TOTAL NET WEIGHT/CTN</td>
        <td width="40">:</td> 
        <td width="80" align="right"><? //echo number_format($net_weight,2); ?> KGS</td> 
        <td width="142"></td>
        </tr>
        <tr>
        <td width="100">TOTAL GROSS WEIGHT</td>
        <td width="40">:</td> 
        <td width="80" align="right"><? //echo number_format($gross_weight,2); ?> KGS</td> 
        <td width="142"></td>
        </tr>
        <tr>
        <td width="100">CARTON MEASURMENT</td>
        <td width="40">:</td> 
        <td width="80" align="right"><? //echo number_format($gross_weight,2); ?> KGS</td> 
        <td width="142"></td>
        </tr>
        <tr>
        <td width="100">TOTAL VOLUME</td>
        <td width="40">:</td> 
        <td width="80" align="right"><? echo number_format($cbm_qnty,2); ?> CBM</td> 
        <td width="142"></td>
        </tr>
        </table>
        </td>
        <td width="276" align="right" style="border:none">
        FOR <? echo strtoupper($company_name_arr[$benificiary_id]["company_name"]);?>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        ......................................................<br/>
        Authorized Signature
        </td>
        </tr>
    </table>
	<?
	$HTM=ob_get_contents();
	ob_end_clean();
	$invoice=new invoice($HTM,$header,'',false,true,true);
	?>