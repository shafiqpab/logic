<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="btb_application_form")
{

	 $sql = "SELECT	btb_system_id,importer_id,issuing_bank_id,lc_value,lc_date,currency_id,tenor,supplier_id,item_category_id,pi_id,port_of_loading,port_of_discharge,delivery_mode_id,last_shipment_date,lc_expiry_date,inco_term_place,doc_presentation_days,origin,inco_term_id,insurance_company_name,cover_note_no,cover_note_date,maturity_from_id,payterm_id,tolerance,lca_no,lc_number,lc_date,btb_system_id,pi_id,partial_shipment,transhipment, garments_qty, advising_bank, advising_bank_address, lcaf_no from com_btb_lc_master_details where id='$data'";
	 // echo $sql;
	$data_array=sql_select($sql);
  $btb_sys_no=$data_array[0][csf("btb_system_id")];
   //echo "select lc_sc_id, is_lc_sc from com_btb_export_lc_attachment where import_mst_id=$data and is_deleted=0 and status_active=1";
   $is_lc_sc_sql=sql_select("select lc_sc_id, is_lc_sc from com_btb_export_lc_attachment where import_mst_id=$data and is_deleted=0 and status_active=1");
   if(empty($is_lc_sc_sql)) {echo "May be attachment not complete yet";die;}
   foreach($is_lc_sc_sql as $row)
   {
       if($row[csf('is_lc_sc')]==0){
           $sql_lc=sql_select("SELECT id,export_lc_no, lc_date FROM com_export_lc  where id=".$row[csf('lc_sc_id')]);
           foreach($sql_lc as $lc_row)
           {
               $export_lc_sc_no_arr[$lc_row[csf("id")]]="<strong>".$lc_row[csf("export_lc_no")]."</strong> Dated <strong>".$lc_row[csf("lc_date")]."</strong>";
           }
       }
       else{
           $sql_sc=sql_select("SELECT id,contract_no,contract_date FROM com_sales_contract where id=".$row[csf('lc_sc_id')]);
           foreach($sql_sc as $sc_row)
           {
               $export_lc_sc_no_arr[$sc_row[csf("id")]]="<strong>".$sc_row[csf("contract_no")]."</strong> Dated <strong>".$sc_row[csf("contract_date")]."</strong>";
           }
       }
              
       $lc_sc_id_arr[]=$row[csf('lc_sc_id')];

   }


	$order_pcs_qty = return_field_value("sum(b.order_quantity) as order_quantity","com_export_lc_order_info a,wo_po_color_size_breakdown b","b.po_break_down_id=a.wo_po_break_down_id and a.com_export_lc_id in(".implode(',',$lc_sc_id_arr).")","order_quantity");


//--------------lib
	$currency_sign_arr=array(1=>'৳',2=>'$',3=>'€',4=>'€',5=>'$',6=>'£',7=>'¥');
  $company_name = return_field_value("company_name","lib_company","id=".$data_array[0][csf("importer_id")],"company_name");
  $bang_bank_reg_no = return_field_value("bang_bank_reg_no","lib_company","id=".$data_array[0][csf("importer_id")],"bang_bank_reg_no");

	$country_array = return_library_array("select id,country_name from lib_country where is_deleted=0","id","country_name");
	//echo "select id,plot_no,road_no,block_no,country_id,city,zip_code,irc_no,tin_number,vat_number,bang_bank_reg_no from lib_company where id = ".$data_array[0][csf('importer_id')]."";
	$address = sql_select("SELECT id,plot_no,level_no,road_no,block_no,country_id,city,zip_code,irc_no,tin_number,vat_number,bang_bank_reg_no,bin_no from lib_company where id = ".$data_array[0][csf('importer_id')]."");
  $company_address='';
	foreach($address as $row){
		$company_address.= $row[csf('plot_no')];
    if( $row[csf('level_no')]!=''){$company_address.= ', '.$row[csf('level_no')];}
    if( $row[csf('road_no')]!=''){$company_address.= ', '.$row[csf('road_no')];}
    if( $row[csf('block_no')]!=''){$company_address.= ', '.$row[csf('block_no')];}
    if( $row[csf('city')]!=''){$company_address.= ', '.$row[csf('city')];}
    if( $row[csf('zip_code')]!=''){$company_address.= ', '.$row[csf('zip_code')];}
    if( $row[csf('country_id')]!=''){$company_address.= ', '.$country_array[$row[csf('country_id')]];}
		$company_add[$row[csf('id')]]['irc_no'] = $row[csf('irc_no')];
		$company_add[$row[csf('id')]]['tin_number'] = $row[csf('tin_number')];
		$company_add[$row[csf('id')]]['vat_number'] = $row[csf('vat_number')];
		$company_add[$row[csf('id')]]['bin_number'] = $row[csf('bin_no')];
		$company_add[$row[csf('id')]]['bang_bank_reg_no'] = $row[csf('bang_bank_reg_no')];
	}
	//print_r($company_add);
  $factory_arr = sql_select("select id,address from lib_location where company_id='".$data_array[0][csf('importer_id')]."' and is_deleted=0");
	$factory_add='';
	foreach($factory_arr as $factory){
		if($factory_add!='' && $factory[csf('address')] !=''){ $factory_add.= ", ".$factory[csf('address')]; }else{ $factory_add.= $factory[csf('address')]; }
	}

	$branch = return_field_value("branch_name","lib_bank","id=".$data_array[0][csf("issuing_bank_id")],"branch_name");
    
	$bank_name = return_field_value("bank_name","lib_bank","id=".$data_array[0][csf("issuing_bank_id")],"bank_name");
	$bank_address = return_field_value("ADDRESS","lib_bank","id=".$data_array[0][csf("issuing_bank_id")],"ADDRESS");
	$currency_sign = $currency_sign_arr[$data_array[0][csf("currency_id")]];


	$supplier_name = return_field_value("supplier_name","lib_supplier","id=".$data_array[0][csf("supplier_id")],"supplier_name");
	$supplier_add = return_field_value("address_1","lib_supplier","id=".$data_array[0][csf("supplier_id")],"address_1");

	//echo "select id, pi_number from com_pi_master_details where id in(".$data_array[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0";
	//$pi_number_arr=return_library_array( "SELECT id, pi_number from com_pi_master_details where id in(".$data_array[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0",'id','pi_number');
	$pi_cate_arr=return_library_array( "SELECT id, item_category_id from com_pi_master_details where id in(".$data_array[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0",'id','item_category_id');
	
	$hs_code_arr=return_library_array( "SELECT id, HS_CODE from com_pi_master_details where id in(".$data_array[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0",'id','HS_CODE');

	$pi_number_date='';
  	if ($data_array[0][csf("pi_id")] != ""){
      $sql_pi=sql_select("SELECT id, pi_number, pi_date from com_pi_master_details where id in(".$data_array[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0");
      foreach($sql_pi as $row){
        $pi_number_date.='PI: '.$row[csf('pi_number')].'&nbsp;DT: '.change_date_format($row[csf('pi_date')]).', ';
      }
  	}



	//echo $total_pi=count($pi_number_arr);
  $pi_category= $pi_cate_arr[$data_array[0][csf("pi_id")]];

	if($data_array[0][csf("last_shipment_date")]!=''){
		$last_shipment_date=date('d.m.Y',strtotime($data_array[0][csf("last_shipment_date")]));
	}
	else
	{
		$last_shipment_date='';
	}


	if($data_array[0][csf("lc_expiry_date")]!=''){
		$lc_expiry_date=date('d.m.Y',strtotime($data_array[0][csf("lc_expiry_date")]));
	}
	else
	{
		$lc_expiry_date='';
	}
	
	if(count($hs_code_arr)>1){
		foreach($hs_code_arr as $row){
			$hs_code .= $row.",";
		}
		
	}
	else
	{
		$hs_code = $hs_code_arr[$data_array[0][csf("pi_id")]];
	}
	
	$origin = return_field_value("country_name"," lib_country","id=".$data_array[0][csf("origin")],"country_name");

	$inco_term_id=$incoterm[$data_array[0][csf("inco_term_id")]];



	if($data_array[0][csf("cover_note_date")]!=''){
		$cover_note_date=date('d.m.Y',strtotime($data_array[0][csf("cover_note_date")]));
	}
	else
	{
		$cover_note_date='';
	}

	if($data_array[0][csf("payterm_id")]==1){
		$pay_term_cond = $pay_term[$data_array[0][csf("payterm_id")]];
	}else{
		$pay_term_cond = $data_array[0][csf("tenor")]. "Day's";
	}
	if($data_array[0][csf("doc_presentation_days")] == ""){
		$doc_presentation_days = "";
	}else{
		$doc_presentation_days = $data_array[0][csf("doc_presentation_days")]."Days";
	}

  ?>
  <?
    
    $nature=return_field_value("business_nature","lib_company","id=".$data_array[0][csf("importer_id")],"business_nature");
    $business_nature_arr= explode(",", $nature);
    $business_nature;
    if(count($business_nature_arr)>0){
        foreach($business_nature_arr as $row){
            if($row==2){
                $business_nature .= "Knit ";
            }elseif($row==3){
                $business_nature .= "Woven ";
            }elseif($row==4){
                $business_nature .= "Trims ";
            }elseif($row==5){
                $business_nature .= "Print ";
            }elseif($row==6){
                $business_nature .= "Embroidery ";
            }elseif($row==7){
                $business_nature .= "Wash ";
            }elseif($row==8){
                $business_nature .= "Yarn Dyeing ";
            }elseif($row==9){
                $business_nature .= "AOP ";
            }elseif($row==100){
                $business_nature .= "Sweater ";
            }
        }
    }
  ?>

  <style>
    body{
      margin:0;
      padding: 0;
      width: 8.5in;
      /* height: 14in; */
    }
    .clearfix::after {
      content: "";
      clear: both;
      display: table;
    }
    #div1{
        margin: 0;
    }
    #div2{
      border-style: solid;
      border-width: 2px;
      height: 80px;
      width: 110px;
      padding-top:45px;
      text-align:center;
      float:right;
    }
    #div3{
      border-style: solid;
      border-width: 2px;
      height: 40px;
      width: 200px;
      margin-top: 8px;
      margin-bottom: 8px;
      padding-left: 2px;
      float:right;
    }
    .div3a
    {
      margin-left:335px;
    }
    ol#menu li {
      display:inline;
      list-style-type: katakana-iroha;
      float:left;
    }

    table, th, td {
      border: 1px solid black;
      border-collapse: collapse;
      padding-top: 2px;
      padding-bottom: 2px;
      padding-left: 5px;
    }
    table{
        margin-top: 10px;
        margin-bottom: 10px;
    }
    .div5{
      float:right;
      border-left: 1px solid;
      border-bottom: 1px solid;
      margin-top: 0;
      margin-bottom: 5px;
    }
    .div6{
      float: left;
      width: 363px;
    }
    .div10{
      float: left;
      width: 420px;
    }
    .div7{
      float: left;
      border-top: 1px solid;
      margin-right: 25px;
      margin-top: 20px;
      margin-bottom: 10px;
    }
    .div8{
      position: relative;
      opacity: 0.5;
      right: 10px;
      top: 3px;
      margin-right: -5px;
    }
    .div9{
      position: relative;
      z-index: 100;
    }
    div{
      margin-top: 1px;
      margin-bottom: 1px;
    }
    .s3{
      position: relative;
      left: -26px;
      bottom: 3px;
      margin-right: -20px;
    }
    .underline 
    {
      border-bottom: 1px solid currentColor;
      display: inline-block;
      line-height: 0.85;
      width:300px;
    }
      </style>

  <body>
    <div>
      <h1 id="div1">United Commercial Bank Ltd.</h1>
      <div class="clearfix">
        <div style="float: left;">
            <span class="underline">The Manager,</span><br>
            <span class="underline">Foreign Exchange Branch</span><br>
            <span class="underline">20, Dilkusha C/A. </span><br>
            <span class="underline">Dhaka, Bangladesh.</span>
        </div>
        <div id="div2">STAMP</div>
      </div>
      <div class="clearfix">
        <div id="div3">L/C NO.</div>
      </div>
      <div class="clearfix">
        <div  style="float: right;" class="clearfix">Date............................................</div>
      </div>
      <h3 style="text-align:center;">APPLICATION AND AGREEMENT FOR CONFIRMED IRREVOCABLE<br> WITHOUT RECOURSE TO DRAWERS LETTER OF CREDIT</h3>
      <div style="font-size: 80%;">
        Please open confirmed irrevocable letter of credit through your correspondent by &nbsp;
        <!-- <svg width="10" height="10">
          <rect width="10" height="10"  style="fill:#fdfdfd;stroke-width:3;stroke:rgb(0, 0, 0)" /> 
        </svg> -->
        &#11036;
        Mail/Airmail &nbsp;
        &#11036;
        Teletransmission in full &nbsp;
        &#11036;
        Teletransmission in &nbsp;
        brief details of which are as follows:
        <span class="div3a">&#11036;
        Swift</span>
         
      </div>
      <div class="clearfix"></div>
      <div>
        <table style="width: 100%">
          <tbody>
            <tr height="80" valign="top">
              <td width="728"colspan="5">
                <div class="clearfix">
                  <div style="float: left; width:220px;">Beneficiary's Name & Address : </div>
                  <div  style="float: left;"> <? echo $supplier_name.'<br> '.$supplier_add;?> </div>
                </div>
              </td>
            </tr>
            <tr height="80" valign="top">
              <td width="728"colspan="5">
                <div class="clearfix">
                  <div style="float: left; width:220px;">Opener's Name & Address: </div>
                  <div  style="float: left;"> <? echo $company_name.'<br> '.$company_address; ?><br>VAT # <? echo $company_add[$data_array[0][csf("importer_id")]]['vat_number'].', BIN # '.$company_add[$data_array[0][csf("importer_id")]]['bin_number'];?> </div>
                </div>
              </td>
            </tr>
            <tr>
              <td width="145" valign="top" >
                Draft amount: <br>
                <b><? echo $currency_sign.' '.number_format($data_array[0][csf('lc_value')],2);?></b>
              </td>
              <td width="145" valign="top">
                In Words: U.S. Dollar
                <?
                  $currency_id = $data_array[0][csf("currency_id")];
                  //$mcurrency, $dcurrency;
                  $dcurrency="";
                  if($currency_id==1)
                  {
                  $mcurrency='Taka';
                  $dcurrency='Paisa';
                  }
                  if($currency_id==2)
                  {
                  $mcurrency='USD';
                  $dcurrency='cents';
                  }
                  if($currency_id==3)
                  {
                  $mcurrency='EURO';
                  $dcurrency='cents';
                  }
                ?>
                <b><? echo number_to_words(number_format($data_array[0][csf('lc_value')],2), '', $dcurrency)." Only";?></b>
              </td>
              <td width="145" valign="top" >
                    &#11036;
                    <? if($data_array[0][csf("payterm_id")]==1)
                        {
                          echo "<span class='s3'> &#10004; </span>" ;
                        }
                    ?>
                    At sight &nbsp; &nbsp; <br>
                    &#11036;
                  <? if($data_array[0][csf("payterm_id")]==2)
                    {
                      $pay_term_cond = $data_array[0][csf("tenor")];
                      echo "<span class='s3'> &#10004;</span>&nbsp; &nbsp;". $pay_term_cond  ;
                    } 
                    ?>
                Days usance&nbsp; &nbsp; 
              </td>
              <td width="145" valign="top" >
                
                  &#11036;
                  <? if($data_array[0][csf("inco_term_id")]==1){echo "<span class='s3'> &#10004;</span>" ;} ?>
                  FOB&nbsp; &nbsp; 

                  &#11036;
                  <? if($data_array[0][csf("inco_term_id")]==2){echo "<span class='s3'> &#10004;</span>" ;} ?>
                  CFR&nbsp; &nbsp; <br>

                  &#11036;
                  <? if($data_array[0][csf("inco_term_id")]==4){  echo "<span class='s3'> &#10004;</span>" ; } ?>
                  FCA&nbsp; &nbsp; 

                  &#11036;
                  <? if($data_array[0][csf("inco_term_id")]==5){echo "<span class='s3'> &#10004;</span>" ;} ?>
                  CPT&nbsp; &nbsp; <br>

                  &#11036;
                  <? if($data_array[0][csf("inco_term_id")]==6){  echo "<span class='s3'> &#10004;</span>" ; } ?>
                  EXW&nbsp; &nbsp; 
              </td>
              <td width="145" valign="top" >
                Drawn on <br>
                &#11036;
                  <? 
                  ?>
                  Us&nbsp; <br>
                  &#11036;
                <? 
                ?>
                Them&nbsp; &nbsp; 
              </td>
            </tr>
            <tr>
              <td colspan="3" style="border-bottom:none;text-align: right;" >
                Please specify commodities, price, quantity, indent no. etc.
              </td>
              <td colspan="2">
                  Country of origin : <br>
                  <? echo $origin;?>
              </td>
            </tr>
            <tr height="80" valign="top">
              <td colspan="5" style="border-top:none;">
                <?=$item_category[$pi_category];?> for 100% Export Oriented Readymade Garments Knit Industries. As per Supplier`s <strong><? echo rtrim($pi_number_date,', '); ?></strong>
              </td>
            </tr>
            <tr>
              <td colspan="5" style="border-bottom:none;">
                DOCUMENTS REQUIRED AS INDICATED BY CHECK (X)
              </td>
            </tr>
            <tr height="40">
              <td colspan="2" style="border-top:none;border-bottom:none;">
                &#11036;
                Commercial invoice in sextuplicate
              </td>
              <td colspan="3" >
                <div class="clearfix">
                  <div style="float: left;">
                    Bangladesh Bank registration No. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br>
                    <? echo $bang_bank_reg_no ;?></div>
                  <div >Import Licence/LCAF No. <br><? echo $data_array[0][csf("lcaf_no")] ;?></div>
                </div>
              </td>
            </tr>
            <tr>
              <td colspan="2" style="border-top:none;border-bottom:none;">
                &#11036;
                Special customs invoice in duplicate
              </td>
              <td colspan="3" >
                <div class="clearfix">
                  <div style="float: left;">
                    H.S Code : <? echo $hs_code;?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                  </div>
                  <div >IRC. NO. <? echo $company_add[$data_array[0][csf("importer_id")]]['irc_no'];?></div>
                </div>
              </td>
            </tr>
            <tr>
              <td colspan="5" style="border-top:none;border-bottom:none;">
                <div class="clearfix">
                  <div style="float: left;">
                    &#11036;
                    Other documents :
                  </div>
                  <div  style="float: left;"> <div class="div7" style="width: 450px;">(if special documents are required please specify name of issuer)
                  </div> 
                </div>              
              </td>
            </tr>
            <tr>
              <td colspan="5" style="border-top:none;border-bottom:none;padding-top:20px;">
                &#11036;
                Full set of clean on board bills of lading &nbsp;
                &#11036;
                Airway Bill &nbsp;
                &#11036;
                Post parcel &nbsp;
                &#11036;
                Relating to shipment &nbsp;
                &#11036;
                T/R &nbsp;
                &#11036;
                R/R &nbsp;
              </td>
            </tr>
            <tr>
              <td colspan="5" style="text-align: center;border-bottom:none;border-top:none;">
                from &nbsp;<span style="border-bottom:1px solid black;"><? echo $data_array[0][csf('port_of_loading')];?></span> &nbsp;to&nbsp; <span style="border-bottom:1px solid black;"><? echo $data_array[0][csf('port_of_discharge')];?></span> &nbsp;drawn
              </td>
            </tr>
            <tr>
              <td colspan="5" style="text-align: center;border-top:none;border-bottom:none;">
                (in each case please certify port of country only)
              </td>
            </tr>
            <tr>
              <td colspan="2" style="text-align: center;border-top:none;border-right:none;padding-top:20px;">
                to the order of United Commercial Bank Ltd.
              </td>
              <td colspan="3" style="text-align: center;border-top:none;border-left:none;padding-top:20px;">
                Marked notify above account party.
              </td>
            </tr>
            <tr>
              <td colspan="2" >
                Insurance cover note/policy no. : <?echo $data_array[0][csf('cover_note_no')];?><br>
                Date. : <?echo date('d.m.Y',strtotime($data_array[0][csf('cover_note_date')]));?><br>
                Amount-Tk. :
              </td>
              <td colspan="3" valign='top' >
                (Name and address of Insurance Company.) <br>
                <?echo $data_array[0][csf('insurance_company_name')];?>
              </td>
            </tr>
            <tr>
              <td colspan="2" >
                &#11036; Part Shipment&nbsp; 
                &#11036;
                <?  
                  $partial_shipment = $data_array[0][csf("partial_shipment")];
                    if($partial_shipment==1)
                    {
                    echo "<span class='s3'>&#10003;</span>" ;
                    }
                    elseif($partial_shipment !=1)
                    {
                    echo '';
                    }

                ?>
                Allowed&nbsp; 
                
                &#11036;
                <?  
                  $partial_shipment = $data_array[0][csf("partial_shipment")];
                    if($partial_shipment==2)
                    {
                    echo "<span class='s3'>&#10003;</span>" ;
                    }
                    elseif($partial_shipment !=2)
                    {
                    echo '';
                    }
                ?>
                Prohibited
              </td>
              <td colspan="3" >
                &#11036; Transhipment&nbsp; 
                &#11036;
                <?  
                  $transhipment = $data_array[0][csf("transhipment")];
                  if($transhipment==1)
                  {
                  echo "<span class='s3'>&#10003;</span>" ;
                  }
                  elseif($transhipment !=1)
                  {
                  echo '';
                  }

                ?>
                Allowed&nbsp; 
                &#11036;
                <?  
                  $transhipment = $data_array[0][csf("transhipment")];
                  if($transhipment==2)
                  {
                  echo "<span class='s3'>&#10003;</span>" ;
                  }
                  elseif($transhipment !=2)
                  {
                  echo '';
                  }
                ?>
                Prohibited
              </td>
            </tr>
            <tr>
              <td colspan="2">
                Last date of shipment :&nbsp; <? echo $last_shipment_date;?>
              </td>
              <td colspan="3">
                Last date of negotiation :&nbsp; <? echo $lc_expiry_date;?>
              </td>
            </tr>
            <tr valign="top">
              <td colspan="5">
                Other terms and conditions if any: 
                i) Foreign Bank's Charges on opener's/beneficiary's A/C <br>
                ii) This is Back to Back L/C against Sales Contract No.: <? echo implode(', ',$export_lc_sc_no_arr);?><br>
                <!-- iii) <br>
                iv) <br> -->
                <?
                  $roman_arr=array(1=>"iii)",2=>"iv)",3=>"v)",4=>"vi)",5=>"vii)");
                    $sql_term= sql_select("select terms from wo_booking_terms_condition where booking_no='$btb_sys_no' ");
                    $i=1;
                    foreach ($sql_term as $value) {
                      echo $roman_arr[$i]." ".$value[csf('terms')]."</br>";
                      $i++;
                    }
                  ?>
              </td>
            </tr>
          </tbody>
        </table>
        <div>
          C.F.- 7
        </div>
      </div>
    </div>
  </body>
  <?
  exit();
}

?>


