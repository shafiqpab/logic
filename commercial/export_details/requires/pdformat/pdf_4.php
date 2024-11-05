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
        <td width="150" style="border-bottom:1px solid #000">Invoice Number</td>
        <td width="15" style="border-bottom:1px solid #000">:</td>
        <td width="150" style="border-bottom:1px solid #000"><?php echo $invoice_no;  ?></td>
        </tr>
        <tr>
        <td width="150" style="border-bottom:1px solid #000">Date</td>
        <td width="15" style="border-bottom:1px solid #000">:</td>
        <td width="150" style="border-bottom:1px solid #000"><? echo change_date_format($invoice_date);?></td>
        </tr>
        
        <tr>
        <td width="150" style="border-bottom:1px solid #000">L/C Number</td>
        <td width="15" style="border-bottom:1px solid #000">:</td>
        <td width="150" style="border-bottom:1px solid #000"><?php echo $lc_sc_no;  ?></td>
        </tr>
        <tr>
        <td width="150" style="border-bottom:1px solid #000">Date</td>
        <td width="15" style="border-bottom:1px solid #000">:</td>
        <td width="150" style="border-bottom:1px solid #000"><? echo change_date_format($lc_sc_date);?></td>
        </tr>
        
        <tr>
        <td width="150" style="border-bottom:1px solid #000">Exp No.</td>
        <td width="15" style="border-bottom:1px solid #000">:</td>
        <td width="150" style="border-bottom:1px solid #000"><?php echo $exp_form_no;  ?></td>
        </tr>
        <tr>
        <td width="150">Date</td>
        <td width="15">:</td>
        <td width="150"><? if($exp_form_date!="" && $exp_form_date!="0000-00-00" ) echo change_date_format($exp_form_date);?></td>
        </tr>
        </table>
        </td>
        </tr>
        
        <tr>
        <td width="319"> 
         <br/>
        <strong><u>FOR ACCOUNT & RISK OF:</u></strong><br/>
        <?
        echo  $applicant."<br/>";
	    echo  $applicantAddress;
		?>
        </td>
         
        <td width="319">
        <br/>
        <table border="0">
        <tr>
        <td width="150" style="border-bottom:1px solid #000">B/L No</td>
        <td width="15" style="border-bottom:1px solid #000">:</td>
        <td width="150" style="border-bottom:1px solid #000"><?php echo $bl_no;  ?></td>
        </tr>
        <tr>
        <td width="150" style="border-bottom:1px solid #000">Date</td>
        <td width="15" style="border-bottom:1px solid #000">:</td>
        <td width="150" style="border-bottom:1px solid #000"><?  if($bl_date!="" && $bl_date!="0000-00-00") echo change_date_format($bl_date); ?></td>
        </tr>
        
        <tr>
        <td width="150" style="border-bottom:1px solid #000">Container No</td>
        <td width="15" style="border-bottom:1px solid #000">:</td>
        <td width="150" style="border-bottom:1px solid #000"><?php //echo $lc_sc_no;  ?></td>
        </tr>
        <tr>
        <td width="150" >Vessel No</td>
        <td width="15" >:</td>
        <td width="150"><? echo $mother_vessel;?></td>
        </tr>
        
        </table>
        </td>
        </tr>
        <tr>
        <td width="212"> 
          <br/>
        <strong><u>NOTIFY:</u></strong><br/>
        <?
        echo "&nbsp;&nbsp;".$buyer_name_arr[$notifying_party]["buyer_name"]."<br/>";
	    echo "&nbsp;&nbsp;".$buyer_name_arr[$notifying_party]["address_1"];
        ?>
        </td>
        <td width="212"> 
          <br/>
        <strong><u>L/C Issueing Bank:</u></strong><br/>
        <? echo "&nbsp;&nbsp;".$issuing_bank_name;  ?>
        </td>
        <td width="214">
        <br/>
        <strong><u>SHIPPERS LIEN BANK</u></strong><br/>
        <?
		echo "&nbsp;&nbsp;".$bank_name_arr[$lien_bank]["bank_name"]."<br/>";
		echo $bank_name_arr[$lien_bank]["address"];
		?>
        </td>
        </tr>
        <tr>
        <td width="212"> 
        <br/>
        Country of Origin: BANGLADESH
        </td>
        <td width="212"> 
        Port of Loading
        </td>
        <td width="214">
        :<? echo $port_of_loading;?>
        </td>
        </tr>
        <tr>
        <td width="212"> 
        <br/>
        Term:  <? echo $pay_term[$pay_term_id];?>
        </td>
        <td width="212"> 
        Country of Destination
        </td>
        <td width="214">
        :<? echo $place_of_delivery; ?>
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
        <td width="138" align="right"><? echo "$&nbsp;&nbsp;".number_format($total_value,2,".",","); ?></td>
        </tr>
        
        <tr>
        <td width="362" align="right">
        Discount :
        </td>
        <td width="138" align="right"></td>
        <td width="138" align="right"><? echo number_format($total_discount,2,".",","); ?></td>
        </tr>
        
        <tr>
        <td width="362" align="right">
        Net Total :
        </td>
        <td width="138" align="right"></td>
        <td width="138" align="right"><? echo "$&nbsp;&nbsp;".number_format($net_invo_value,2,".",","); ?></td>
        </tr>
        
        <tr>
        <td width="500" align="right">
        LESS 7% INSPECTION CHARGE :
        </td>
        <td width="138" align="right"><? //echo "$&nbsp;&nbsp;".number_format($total_value,2,".",","); ?></td>
        </tr>
        
        <tr>
        <td width="638" align="center">
        SAY: <? echo "( ".number_to_words(def_number_format($net_invo_value,2,""),"USD", "CENTS")." Only )";?>
        </td>
        </tr>
       
        
        
        <tr style="border:none">
        <td width="638" style="border:none">
        <br/>
        <table border="0" cellpadding="2">
        
        <tr>
        <td width="100">Total Qty</td>
        <td width="40">:</td> 
        <td width="80" align="right"><? echo number_format($total_qnty,0,".",","); ?> Pcs</td> 
        <td width="142"></td>
        <td width="276" align="right" style="border:none" rowspan="6">
        
        </td>
        </tr>
        <tr>
        <td width="100">Total Ctn</td>
        <td width="40">:</td> 
        <td width="80" align="right"><? echo number_format($total_carton_qnty,0,".",","); ?> Ctns</td> 
        <td width="142"></td>
        </tr>
        <tr>
        <td width="100">Total Net Wt</td>
        <td width="40">:</td> 
        <td width="80" align="right"><? echo number_format($net_weight,2); ?> Kgs</td> 
        <td width="142"></td>
        </tr>
        <tr>
        <td width="100">Total Grs W</td>
        <td width="40">:</td> 
        <td width="80" align="right"><? echo number_format($gross_weight,2); ?> Kgs</td> 
        <td width="142"></td>
        </tr>
        <tr>
        <td width="100">Chargable Weight</td>
        <td width="40">:</td> 
        <td width="80" align="right"><? //echo number_format($net_weight,2); ?> KGS</td> 
        <td width="142"></td>
        </tr>
        <tr>
        <td width="100">Total CBM</td>
        <td width="40">:</td> 
        <td width="80" align="right"><? echo number_format($cbm_qnty,2); ?> CBM</td> 
        <td width="142"></td>
        </tr>
        </table>
        </td>
        
        </tr>
    </table>
	<?
	$HTM=ob_get_contents();
	ob_end_clean();
	$invoice=new invoice($HTM,$header,'',true,true,true);
	?>