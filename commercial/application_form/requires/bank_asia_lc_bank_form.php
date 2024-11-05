<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="btb_application_form")
{

	 $sql = "SELECT	importer_id,issuing_bank_id,lc_value,lc_date,currency_id,tenor,supplier_id,item_category_id,pi_id,port_of_loading,port_of_discharge,delivery_mode_id,last_shipment_date,lc_expiry_date,inco_term_place,doc_presentation_days,origin,inco_term_id,insurance_company_name,cover_note_no,cover_note_date,maturity_from_id,payterm_id,tolerance,lca_no,lc_number,lc_date,btb_system_id,pi_id,partial_shipment,transhipment, garments_qty, advising_bank, advising_bank_address from com_btb_lc_master_details where id='$data'";
	 // echo $sql;
	$data_array=sql_select($sql);

   //echo "select lc_sc_id, is_lc_sc from com_btb_export_lc_attachment where import_mst_id=$data and is_deleted=0 and status_active=1";
   $is_lc_sc_sql=sql_select("select lc_sc_id, is_lc_sc from com_btb_export_lc_attachment where import_mst_id=$data and is_deleted=0 and status_active=1");
   if(empty($is_lc_sc_sql)) {echo "May be attachment not complete yet";die;}
   foreach($is_lc_sc_sql as $row)
   {
       if($row[csf('is_lc_sc')]==0){
           $sql_lc=sql_select("SELECT id,export_lc_no, lc_date FROM com_export_lc  where id=".$row[csf('lc_sc_id')]);
           foreach($sql_lc as $lc_row)
           {
               $export_lc_sc_no_arr[$lc_row[csf("id")]]=$lc_row[csf("export_lc_no")]." DT :".$lc_row[csf("lc_date")];
           }
       }
       else{
           $sql_sc=sql_select("SELECT id,contract_no,contract_date FROM com_sales_contract where id=".$row[csf('lc_sc_id')]);
           foreach($sql_sc as $sc_row)
           {
               $export_lc_sc_no_arr[$sc_row[csf("id")]]=$sc_row[csf("contract_no")]." DT :".$sc_row[csf("contract_date")];
               
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
	$address = sql_select("SELECT id,plot_no,level_no,road_no,block_no,country_id,city,zip_code,irc_no,tin_number,vat_number,bang_bank_reg_no from lib_company where id = ".$data_array[0][csf('importer_id')]."");
	foreach($address as $row){
		$company_add[$row[csf('id')]]['plot_no'] = $row[csf('plot_no')];
		$company_add[$row[csf('id')]]['level_no'] = $row[csf('level_no')];
		$company_add[$row[csf('id')]]['road_no'] = $row[csf('road_no')];
		$company_add[$row[csf('id')]]['block_no'] = $row[csf('block_no')];
		$company_add[$row[csf('id')]]['country_id'] = $row[csf('country_id')];
		$company_add[$row[csf('id')]]['city'] = $row[csf('city')];
		$company_add[$row[csf('id')]]['zip_code'] = $row[csf('zip_code')];
		$company_add[$row[csf('id')]]['irc_no'] = $row[csf('irc_no')];
		$company_add[$row[csf('id')]]['tin_number'] = $row[csf('tin_number')];
		$company_add[$row[csf('id')]]['vat_number'] = $row[csf('vat_number')];
		$company_add[$row[csf('id')]]['bang_bank_reg_no'] = $row[csf('bang_bank_reg_no')];
	}
	//print_r($company_add);

	$branch = return_field_value("branch_name","lib_bank","id=".$data_array[0][csf("issuing_bank_id")],"branch_name");
    
	$bank_name = return_field_value("bank_name","lib_bank","id=".$data_array[0][csf("issuing_bank_id")],"bank_name");
	$bank_address = return_field_value("ADDRESS","lib_bank","id=".$data_array[0][csf("issuing_bank_id")],"ADDRESS");
	$currency_sign = $currency_sign_arr[$data_array[0][csf("currency_id")]];


	$supplier_name = return_field_value("supplier_name","lib_supplier","id=".$data_array[0][csf("supplier_id")],"supplier_name");
	$supplier_add = return_field_value("address_1","lib_supplier","id=".$data_array[0][csf("supplier_id")],"address_1");

	//echo "select id, pi_number from com_pi_master_details where id in(".$data_array[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0";
	//$pi_number_arr=return_library_array( "SELECT id, pi_number from com_pi_master_details where id in(".$data_array[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0",'id','pi_number');
	
	$hs_code_arr=return_library_array( "SELECT id, HS_CODE from com_pi_master_details where id in(".$data_array[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0",'id','HS_CODE');

	//$pi_date = return_field_value("pi_date","com_pi_master_details"," status_active = 1 AND is_deleted = 0 AND id=".$data_array[0][csf("pi_id")],"pi_date");



	//echo $total_pi=count($pi_number_arr);

  $pi_number_date='';
  if ($data_array[0][csf("pi_id")] != ""){
    $sql_pi=sql_select("SELECT id, pi_number, pi_date from com_pi_master_details where id in(".$data_array[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0");
    foreach($sql_pi as $row){
      $pi_number_date.='PI: '.$row[csf('pi_number')].'&nbsp;DT: '.change_date_format($row[csf('pi_date')]).', ';
    }
  }

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
    height: 50px;
    width: 70px;
    padding-top:30px;
    text-align:center;
    float:right;
    
}
    #div3{
    border-style: solid;
    border-width: 2px;
    height: 20px;
    width: 200px;
    margin-top: 8px;
    margin-bottom: 8px;
    padding-left: 2px;
    float:right;
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
    margin-top: 70px;
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
    left: -25px;
    bottom: 3px;
    margin-right: -20px;
    }
</style>

<body>
    <div>
            <h1 id="div1">Bank Asia Limited </h1>
            <div class="clearfix">
            <div style="float: left;">
            ......Scotia..................Branch
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
            <div style="font-size: 95%;">
          
                Please open a confirmed irrevocable letter of credit through your correspondent by &nbsp;
                    <svg width="10" height="10">
                            <rect width="10" height="10"  style="fill:#fdfdfd;stroke-width:3;stroke:rgb(0, 0, 0)" /> 
                          </svg>
                        Mail/Airmail &nbsp;
                    
                        <svg width="10" height="10">
                            <rect width="10" height="10"  style="fill:#fdfdfd;stroke-width:3;stroke:rgb(0, 0, 0)" /> 
                          </svg>
                        Wire in full &nbsp;
                    
                        <svg width="10" height="10">
                            <rect width="10" height="10"  style="fill:#fdfdfd;stroke-width:3;stroke:rgb(0, 0, 0)" /> 
                          </svg>
                        Wire 
                
                briefly, I as follows subject to Uniform Customers and Practice for Documentary Credit, ICC publication No. 500
            </div>
           <div class="clearfix"></div>
           <div>
            <table style="width: 100%">
                <tbody>
                <tr>
                <td width="363">
                <div>Application: <? echo $company_name;?></div>
                </td>
                <td colspan="2" width="364">
                <div>Beneficiary: <? echo $supplier_name;?></div>
                </td>
                </tr>
                <tr>
                <td width="363">
                <div>Complete Address:</div>
                <div style="font-size: 90%"><? echo $company_add[$data_array[0][csf("importer_id")]]['plot_no'].",".$company_add[$data_array[0][csf("importer_id")]]['level_no'].", ".$company_add[$data_array[0][csf("importer_id")]]['road_no'].",<br/> ".$company_add[$data_array[0][csf("importer_id")]]['city'].", ".$country_array[$company_add[$data_array[0][csf("importer_id")]]['country_id']]; ?></div>
                </td>
                <td colspan="2" width="364">
                <div>Complete Address:</div>
                <div><? echo $supplier_add;?> , <? echo $origin;?></div>
                </td>
                </tr>
                <tr>
                <td width="363">
                <div>L/C amount <? echo $currency_sign.' '.number_format($data_array[0][csf('lc_value')],2);?> In word:&hellip;  <?
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
                        $dcurrency='CENTS';
                        }
                        if($currency_id==3)
                        {
                        $mcurrency='EURO';
                        $dcurrency='CENTS';
                        }
                      ?>
                      <? echo number_to_words(number_format($data_array[0][csf('lc_value')],2), '', $dcurrency);?></div>
                </td>
                <td colspan="2" width="364">
                  <!-- <div>
                    
                      <svg width="10" height="10" class="div9">
                    <rect width="10" height="10"  style="fill:#fdfdfd00;stroke-width:3;stroke:rgb(0, 0, 0)" /> 
                        </svg>
                        <svg width="10" height="10" class="div8">
                        <rect width="10" height="10"  style="fill:#fdfdfd;stroke-width:3;stroke:rgb(0, 0, 0)" /> 
                        </svg>
                        <? if($data_array[0][csf("payterm_id")]==1)
                            {
                              echo "<span class='s3'> &#10004; </span>" ;
                            }
                        ?>

                      At sight&nbsp; &nbsp;
                      
                      <svg width="10" height="10" class="div9">
                          <rect width="10" height="10"  style="fill:#fdfdfd00;stroke-width:3;stroke:rgb(0, 0, 0)" /> 
                        </svg>
                        <svg width="10" height="10" class="div8">
                          <rect width="10" height="10"  style="fill:#fdfdfd;stroke-width:3;stroke:rgb(0, 0, 0)" /> 
                        </svg><? if($data_array[0][csf("inco_term_id")]==9)
                        {
                          echo "<span class='s3'> &#10004;</span>" ;
                        }
                      ?>
                      DAF&nbsp; &nbsp;
                      
                      <svg width="10" height="10" class="div9">
                          <rect width="10" height="10"  style="fill:#fdfdfd00;stroke-width:3;stroke:rgb(0, 0, 0)" /> 
                        </svg>
                        <svg width="10" height="10" class="div8">
                          <rect width="10" height="10"  style="fill:#fdfdfd;stroke-width:3;stroke:rgb(0, 0, 0)" /> 
                        </svg><? if($data_array[0][csf("inco_term_id")]==1)
                        {
                          echo "<span class='s3'> &#10004;</span>" ;
                        }
                      ?>
                      FOB&nbsp; &nbsp; 
                      
                      <svg width="10" height="10" class="div9">
                          <rect width="10" height="10"  style="fill:#fdfdfd00;stroke-width:3;stroke:rgb(0, 0, 0)" /> 
                        </svg>
                        <svg width="10" height="10" class="div8">
                          <rect width="10" height="10"  style="fill:#fdfdfd;stroke-width:3;stroke:rgb(0, 0, 0)" /> 
                        </svg><? if($data_array[0][csf("maturity_from_id")]==3)
                            {
                            echo "<span class='s3'> &#10004; </span>" ;
                              }
                    ?>
                      Negotiation</div>
                  <div>
                      <svg width="10" height="10" class="div9">
                          <rect width="10" height="10"  style="fill:#fdfdfd00;stroke-width:3;stroke:rgb(0, 0, 0)" /> 
                        </svg>
                        <svg width="10" height="10" class="div8">
                          <rect width="10" height="10"  style="fill:#fdfdfd;stroke-width:3;stroke:rgb(0, 0, 0)" /> 
                        </svg>
                      EDF</div>
                  <div>
                     
                    <svg width="10" height="10" class="div9">
                    
                        <rect width="10" height="10"  style="fill:#fdfdfd00;stroke-width:3;stroke:rgb(0, 0, 0)" /> 
                      </svg>
                      <svg width="10" height="10" class="div8">
                        <rect width="10" height="10"  style="fill:#fdfdfd;stroke-width:3;stroke:rgb(0, 0, 0)" /> 
                      </svg> 
                      <? if($data_array[0][csf("payterm_id")]==2)
                        {
                          $pay_term_cond = $data_array[0][csf("tenor")];
                          echo "<span class='s3'> &#10004; $pay_term_cond </span>" ;
                        } 
                      ?>  
                     
                    Days usance&nbsp; &nbsp; 
                    <? if($data_array[0][csf("inco_term_id")]==2)
                      {
                        echo "<span> &#10004;</span>" ;
                      }
			              ?>
                    <svg width="10" height="10" class="div9">
                        <rect width="10" height="10"  style="fill:#fdfdfd00;stroke-width:3;stroke:rgb(0, 0, 0)" /> 
                      </svg>
                      <svg width="10" height="10" class="div8">
                        <rect width="10" height="10"  style="fill:#fdfdfd;stroke-width:3;stroke:rgb(0, 0, 0)" /> 
                      </svg>
                    CFR&nbsp;&nbsp;
                    
                    <svg width="10" height="10" class="div9">
                        <rect width="10" height="10"  style="fill:#fdfdfd00;stroke-width:3;stroke:rgb(0, 0, 0)" /> 
                      </svg>
                      <svg width="10" height="10" class="div8">
                        <rect width="10" height="10"  style="fill:#fdfdfd;stroke-width:3;stroke:rgb(0, 0, 0)" /> 
                      </svg><? if($data_array[0][csf("maturity_from_id")]==1)
                            {
                            echo "<span class='s3'> &#10004; </span>" ;
                              }
                    ?>
                    Acceptance
                  </div> -->
                  <div>
                      <svg width="10" height="10" class="div9">
                        <rect width="10" height="10"  style="fill:#fdfdfd00;stroke-width:3;stroke:rgb(0, 0, 0)" /> 
                      </svg>
                      <svg width="10" height="10" class="div8">
                        <rect width="10" height="10"  style="fill:#fdfdfd;stroke-width:3;stroke:rgb(0, 0, 0)" /> 
                      </svg>
                      <? if($data_array[0][csf("payterm_id")]==1){echo "<span class='s3'> &#10004; </span>" ;}?>
                      At sight&nbsp; &nbsp;

                      <svg width="10" height="10" class="div9">
                        <rect width="10" height="10"  style="fill:#fdfdfd00;stroke-width:3;stroke:rgb(0, 0, 0)" /> 
                      </svg>
                      <svg width="10" height="10" class="div8">
                        <rect width="10" height="10"  style="fill:#fdfdfd;stroke-width:3;stroke:rgb(0, 0, 0)" /> 
                      </svg>
                      <? if($data_array[0][csf("inco_term_id")]==1){echo "<span class='s3'> &#10004; </span>" ;}?>
                       FOB&nbsp;&nbsp;

                      <svg width="10" height="10" class="div9">
                        <rect width="10" height="10"  style="fill:#fdfdfd00;stroke-width:3;stroke:rgb(0, 0, 0)" /> 
                      </svg>
                      <svg width="10" height="10" class="div8">
                        <rect width="10" height="10"  style="fill:#fdfdfd;stroke-width:3;stroke:rgb(0, 0, 0)" /> 
                      </svg>
                      <? if($data_array[0][csf("inco_term_id")]==2){echo "<span class='s3'> &#10004; </span>" ;}?>
                      CFR&nbsp; &nbsp;

                      <svg width="10" height="10" class="div9">
                        <rect width="10" height="10"  style="fill:#fdfdfd00;stroke-width:3;stroke:rgb(0, 0, 0)" /> 
                      </svg>
                      <svg width="10" height="10" class="div8">
                        <rect width="10" height="10"  style="fill:#fdfdfd;stroke-width:3;stroke:rgb(0, 0, 0)" /> 
                      </svg>
                      <? if($data_array[0][csf("inco_term_id")]==4){echo "<span class='s3'> &#10004; </span>" ;}?>

                      FCA&nbsp;&nbsp;
                      <svg width="10" height="10" class="div9">
                        <rect width="10" height="10"  style="fill:#fdfdfd00;stroke-width:3;stroke:rgb(0, 0, 0)" /> 
                      </svg>
                      <svg width="10" height="10" class="div8">
                        <rect width="10" height="10"  style="fill:#fdfdfd;stroke-width:3;stroke:rgb(0, 0, 0)" /> 
                      </svg>
                      <? if($data_array[0][csf("inco_term_id")]==5){echo "<span class='s3'> &#10004; </span>" ;}?>
                      CPT&nbsp; &nbsp;

                      <svg width="10" height="10" class="div9">
                        <rect width="10" height="10"  style="fill:#fdfdfd00;stroke-width:3;stroke:rgb(0, 0, 0)" /> 
                      </svg>
                      <svg width="10" height="10" class="div8">
                        <rect width="10" height="10"  style="fill:#fdfdfd;stroke-width:3;stroke:rgb(0, 0, 0)" /> 
                      </svg>
                      <? if($data_array[0][csf("inco_term_id")]==6){echo "<span class='s3'> &#10004; </span>" ;}?>
                      EXW&nbsp; &nbsp;
                  </div>
                  <div>
                    <svg width="10" height="10" class="div9">
                      <rect width="10" height="10"  style="fill:#fdfdfd00;stroke-width:3;stroke:rgb(0, 0, 0)" /> 
                    </svg>
                    <svg width="10" height="10" class="div8">
                      <rect width="10" height="10"  style="fill:#fdfdfd;stroke-width:3;stroke:rgb(0, 0, 0)" /> 
                    </svg> 
                    <? if($data_array[0][csf("payterm_id")]==2){
                        $pay_term_cond = $data_array[0][csf("tenor")];
                        echo "<span class='s3'> &#10004; $pay_term_cond </span>" ;
                      } 
                    ?>  
                    Days usance&nbsp; &nbsp; 

                    <svg width="10" height="10" class="div9">
                      <rect width="10" height="10"  style="fill:#fdfdfd00;stroke-width:3;stroke:rgb(0, 0, 0)" /> 
                    </svg>
                    <svg width="10" height="10" class="div8">
                      <rect width="10" height="10"  style="fill:#fdfdfd;stroke-width:3;stroke:rgb(0, 0, 0)" /> 
                    </svg>
                    <? if($data_array[0][csf("maturity_from_id")]==1){echo "<span class='s3'> &#10004; </span>" ;}?>
                    By Acceptance&nbsp; &nbsp;

                    <svg width="10" height="10" class="div9">
                      <rect width="10" height="10"  style="fill:#fdfdfd00;stroke-width:3;stroke:rgb(0, 0, 0)" /> 
                    </svg>
                    <svg width="10" height="10" class="div8">
                      <rect width="10" height="10"  style="fill:#fdfdfd;stroke-width:3;stroke:rgb(0, 0, 0)" /> 
                    </svg>
                    <? if($data_array[0][csf("maturity_from_id")]==2){echo "<span class='s3'> &#10004; </span>" ;}?>
                    By Shipment&nbsp; &nbsp;
                  </div>
                  <div>
                    <svg width="10" height="10" class="div9">
                      <rect width="10" height="10"  style="fill:#fdfdfd00;stroke-width:3;stroke:rgb(0, 0, 0)" /> 
                    </svg>
                    <svg width="10" height="10" class="div8">
                      <rect width="10" height="10"  style="fill:#fdfdfd;stroke-width:3;stroke:rgb(0, 0, 0)" /> 
                    </svg>
                    <? if($data_array[0][csf("maturity_from_id")]==3){echo "<span class='s3'> &#10004; </span>" ;}?>
                    By Negotiation&nbsp; &nbsp;

                    <svg width="10" height="10" class="div9">
                      <rect width="10" height="10"  style="fill:#fdfdfd00;stroke-width:3;stroke:rgb(0, 0, 0)" /> 
                    </svg>
                    <svg width="10" height="10" class="div8">
                      <rect width="10" height="10"  style="fill:#fdfdfd;stroke-width:3;stroke:rgb(0, 0, 0)" /> 
                    </svg>
                    <? if($data_array[0][csf("maturity_from_id")]==4){echo "<span class='s3'> &#10004; </span>" ;}?>
                    By B/L&nbsp; &nbsp;

                    <svg width="10" height="10" class="div9">
                      <rect width="10" height="10"  style="fill:#fdfdfd00;stroke-width:3;stroke:rgb(0, 0, 0)" /> 
                    </svg>
                    <svg width="10" height="10" class="div8">
                      <rect width="10" height="10"  style="fill:#fdfdfd;stroke-width:3;stroke:rgb(0, 0, 0)" /> 
                    </svg>
                    <? if($data_array[0][csf("maturity_from_id")]==5){echo "<span class='s3'> &#10004; </span>" ;}?>
                    By Delivery Challan&nbsp; &nbsp;
                  </div>
                </td>
                </tr>
                <tr>
                <td colspan="2" width="527">
                <div>For shipment of:</div>
                <div>1. <? echo $item_category[$data_array[0][csf("item_category_id")]];?> 
                for 100% Export Oriented Knit Composite Readymade <? echo $business_nature;?> Industries.</div>
                <div>2. Quality, Quantity &amp; unit price will be as per <? echo rtrim($pi_number_date,", ");?></div>
                <div style="text-align: center;">(Please specify commodity and details as to grade, quantity, price ect. )</div>
                </td>
                <td width="200">
                <div>Country of origin</div>
                <div>&nbsp;</div>
                <div><? echo $origin;?></div>
                </td>
                </tr>
                <tr>
                    <td colspan="3" width="727" valign="top">
                        <div style="float:left; font-size: 88%; width:350;">
                            DOCUMENTS REQUIRED AS INDICATED BY CHECK (X)
                        </div>
                        <div class="div5"  style="height: 40px;  padding-right: 60px; width:140;">
                            <span>LCA No. </span><span><? echo $data_array[0][csf("lca_no")] ?></span> <br>
                            Date:
                        </div>
                        <div class="div5" style="height: 40px; padding-right: 25px; width:230;">
                            Bangladesh Bank Registration No
                            <p style="padding-left:10px; margin: 0px;" ><? echo $bang_bank_reg_no ;?></p>
                        </div>
                        <div class="clearfix"></div>
                        <div class="div6">
                        <div>
                            <svg width="10" height="10" class="div9">
                                <rect width="10" height="10"  style="fill:#fdfdfd00;stroke-width:3;stroke:rgb(0, 0, 0)" /> 
                              </svg>
                              <svg width="10" height="10" class="div8">
                                <rect width="10" height="10"  style="fill:#fdfdfd;stroke-width:3;stroke:rgb(0, 0, 0)" /> 
                              </svg>
                            Commercial invoice in octuplicate
                        </div>
                        <div>
                            <svg width="10" height="10" class="div9">
                                <rect width="10" height="10"  style="fill:#fdfdfd00;stroke-width:3;stroke:rgb(0, 0, 0)" /> 
                              </svg>
                              <svg width="10" height="10" class="div8">
                                <rect width="10" height="10"  style="fill:#fdfdfd;stroke-width:3;stroke:rgb(0, 0, 0)" /> 
                              </svg>
                            Special customer invoice in duplicate
                        </div>
                    </div>
                        <div class="div10">
                        <div>
                            <svg width="10" height="10" class="div9">
                                <rect width="10" height="10"  style="fill:#fdfdfd00;stroke-width:3;stroke:rgb(0, 0, 0)" /> 
                              </svg>
                              <svg width="10" height="10" class="div8">
                                <rect width="10" height="10"  style="fill:#fdfdfd;stroke-width:3;stroke:rgb(0, 0, 0)" /> 
                              </svg>
                            Certificate of origin issued by...............................
                        </div>
                        <div>
                            <svg width="10" height="10" class="div9">
                                <rect width="10" height="10"  style="fill:#fdfdfd00;stroke-width:3;stroke:rgb(0, 0, 0)" /> 
                              </svg>
                              <svg width="10" height="10" class="div8">
                                <rect width="10" height="10"  style="fill:#fdfdfd;stroke-width:3;stroke:rgb(0, 0, 0)" /> 
                              </svg>
                            Preshipment inspection Certificate issued by ..............
                        </div>
                    </div>
                    <div class="clearfix"></div>
                        <div>
                            <svg width="10" height="10" class="div9">
                                <rect width="10" height="10"  style="fill:#fdfdfd00;stroke-width:3;stroke:rgb(0, 0, 0)" /> 
                              </svg>
                              <svg width="10" height="10" class="div8">
                                <rect width="10" height="10"  style="fill:#fdfdfd;stroke-width:3;stroke:rgb(0, 0, 0)" /> 
                              </svg>
                            Packing List in sextuplicate
                        </div>
                        <div>
                            <svg width="10" height="10" class="div9">
                                <rect width="10" height="10"  style="fill:#fdfdfd00;stroke-width:3;stroke:rgb(0, 0, 0)" /> 
                              </svg>
                              <svg width="10" height="10" class="div8">
                                <rect width="10" height="10"  style="fill:#fdfdfd;stroke-width:3;stroke:rgb(0, 0, 0)" /> 
                              </svg>
                            Others Documents (Please specify the name of the issuer)..............................................................
                        </div>
                        <div class="div6">
                            <svg width="10" height="10" class="div9">
                                <rect width="10" height="10"  style="fill:#fdfdfd00;stroke-width:3;stroke:rgb(0, 0, 0)" /> 
                              </svg>
                              <svg width="10" height="10" class="div8">
                                <rect width="10" height="10"  style="fill:#fdfdfd;stroke-width:3;stroke:rgb(0, 0, 0)" /> 
                              </svg>
                            Full set of clean on board ocean bill of lading
                        </div>
                        <div class="div10">
                            <svg width="10" height="10" class="div9">
                                <rect width="10" height="10"  style="fill:#fdfdfd00;stroke-width:3;stroke:rgb(0, 0, 0)" /> 
                              </svg>
                              <svg width="10" height="10" class="div8">
                                <rect width="10" height="10"  style="fill:#fdfdfd;stroke-width:3;stroke:rgb(0, 0, 0)" /> 
                              </svg>
                            Airway Bill   &nbsp;
                            <svg width="10" height="10" class="div9">
                                <rect width="10" height="10"  style="fill:#fdfdfd00;stroke-width:3;stroke:rgb(0, 0, 0)" /> 
                              </svg>
                              <svg width="10" height="10" class="div8">
                                <rect width="10" height="10"  style="fill:#fdfdfd;stroke-width:3;stroke:rgb(0, 0, 0)" /> 
                              </svg>
                            Post parcel Evidencing Shipment of Goods
                        </div>
                        <div class="clearfix"></div><br>
                        <div>
                            Form port…Beneficiary’s factory. Country… Bangladesh..
                            to(port)……Openner’s Factory… Bangladesh.
                        </div>
                        <div>
                            Drawn to the order Bank Asia Limited...............................notify applicants.
                        </div>
                    </td>
                </tr>
                <tr>
                <td width="363">
                <div>Insurance cover note/Policy no: <br><? echo $data_array[0][csf("cover_note_no")];?> <? echo $data_array[0][csf("cover_note_date")];?> </div><br>
                </td>
                <td colspan="2" width="364">
                <div>Insurance Co. Name &amp; Address : <br>  <? echo $data_array[0][csf("insurance_company_name")];?> </div><br>
                </td>
                </tr>
                <tr>
                <td width="363">
                <div>Part Shipment&nbsp; 
                    
                    <svg width="10" height="10" class="div9">
                        <rect width="10" height="10"  style="fill:#fdfdfd00;stroke-width:3;stroke:rgb(0, 0, 0)" /> 
                      </svg>
                      <svg width="10" height="10" class="div8">
                        <rect width="10" height="10"  style="fill:#fdfdfd;stroke-width:3;stroke:rgb(0, 0, 0)" /> 
                      </svg>
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
                   
                    <svg width="10" height="10" class="div9">
                        <rect width="10" height="10"  style="fill:#fdfdfd00;stroke-width:3;stroke:rgb(0, 0, 0)" /> 
                      </svg>
                      <svg width="10" height="10" class="div8">
                        <rect width="10" height="10"  style="fill:#fdfdfd;stroke-width:3;stroke:rgb(0, 0, 0)" /> 
                      </svg>
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
                    Prohibited</div>
                </td>
                <td colspan="2" width="364">
                <div>Transshipment&nbsp; 
                    <svg width="10" height="10" class="div9">
                        <rect width="10" height="10"  style="fill:#fdfdfd00;stroke-width:3;stroke:rgb(0, 0, 0)" /> 
                      </svg>
                      <svg width="10" height="10" class="div8">
                        <rect width="10" height="10"  style="fill:#fdfdfd;stroke-width:3;stroke:rgb(0, 0, 0)" /> 
                      </svg>
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
                    <svg width="10" height="10" class="div9">
                        <rect width="10" height="10"  style="fill:#fdfdfd00;stroke-width:3;stroke:rgb(0, 0, 0)" /> 
                      </svg>
                      <svg width="10" height="10" class="div8">
                        <rect width="10" height="10"  style="fill:#fdfdfd;stroke-width:3;stroke:rgb(0, 0, 0)" /> 
                      </svg>
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
                    Prohibited</div>
                </td>
                </tr>
                <tr>
                <td width="363">
                <div>Last Date Of Shipment:&nbsp; <? echo $last_shipment_date;?> </div>
                </td>
                <td colspan="2" width="364">
                <div>Expiry Date : <? echo $lc_expiry_date;?></div>
                </td>
                </tr>
                <tr>
                <td colspan="3" width="727">
                <div>Others terms and condition(all special terms &amp; conditions must be mentioned here)</div>
                <div>1. Advising Bank will be <? echo $data_array[0][csf("advising_bank")].", ".$data_array[0][csf("advising_bank_address")];?></div>
                <div>2. BTB LC open aginst Export LC/Contract # <? echo implode(', ',$export_lc_sc_no_arr);?></div>
               <div>3. Mushak-II &amp; BTMA will be issued against (&plusmn; 5%) <? echo $garments_qty = $data_array[0][csf("garments_qty")]; ?> Pcs Readymade <? echo $business_nature;?></div>
                <div>4. Cash Incentive will be drawn by <? echo $company_name;?></div>
                </td>
                </tr>
                </tbody>
                </table>
                <div>The security agreement on the reverse is hereby unconditionally acceptable to undersigned and made application to this application and the letter of credit.</div>
                <div>&nbsp;</div>
                <div class="div7" style="width: 240px;">Signature of Guarantor, if any &amp; date </div>
                <div class="div7" style="width: 185px;">Signature of Applicant</div>
                <div class="div7" style="width: 110px;">Date</div>
                <div class="div7" style="width: 180px;">Account No.</div>
                
                <table style="width: 100%">
                <tbody>
                <tr>
                <td colspan="9" width="734">
                <div style="text-align: center;">FOR BANK&rsquo;S USE ONLY</div>
                </td>
                </tr>
                <tr>
                <td colspan="2" width="127">
                <div>Draft Amount</div>
                </td>
                <td width="72">
                <div>Rate</div><br>
                </td>
                <td width="127">
                <div>Equivalent BDT</div>
                <div>Tk.</div>
                </td>
                <td width="153">
                <div>Margin @</div>
                <div>Tk.</div>
                </td>
                <td colspan="2" width="886">
                <div>Commission @ %</div>
                <div>Tk.</div>
                </td>
                <td width="88">
                <div>Add Com@</div>
                <div>Tk.</div>
                </td>
                <td width="82">
                <div>FCC</div>
                <div>Tk.</div>
                </td>
                </tr>
                <tr>
                <td width="82">
                <div>Stamp</div>
                <div>Tk.</div>
                </td>
                <td colspan="2" width="118">
                <div>Tlx/Mail</div>
                <div>Tk.</div>
                </td>
                <td width="127">
                <div>Others</div>
                <div>Tk.</div>
                </td>
                <td colspan="2" width="163">
                <div>&nbsp;</div>
                <div>Prepared by</div>
                </td>
                <td colspan="3" width="245">
                <div>&nbsp;</div>
                <div>Approved by</div>
                </td>
                </tr>
                </tbody>
                </table>
                <table>
                <tbody>
                <tr>
                <td width="103">
                <div>FXF-05</div>
                </td>
                </tr>
                </tbody>
                </table>
                <div>&nbsp;</div>
           </div>
    </div>
</body>
    <?
	exit();
}

?>


