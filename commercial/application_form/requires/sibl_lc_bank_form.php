<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="btb_application_form")
{
	$sql = "SELECT	importer_id, issuing_bank_id, lc_value, lc_date, currency_id, tenor, supplier_id, item_category_id, pi_id, port_of_loading, port_of_discharge, delivery_mode_id, last_shipment_date, lc_expiry_date, inco_term_place, doc_presentation_days, origin,inco_term_id, insurance_company_name, cover_note_no, cover_note_date, MATURITY_FROM_ID, payterm_id, tolerance, lca_no, lc_number, lc_date, btb_system_id, pi_id, partial_shipment, transhipment, garments_qty, uom_id, advising_bank, advising_bank_address, remarks
	from com_btb_lc_master_details
	where id='$data' ";
	//echo $sql;
	$data_array=sql_select($sql);
	$all_pi_ids=$data_array[0][csf("pi_id")];
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
	$swift_code = return_field_value("swift_code","lib_bank","id=".$data_array[0][csf("issuing_bank_id")],"swift_code");
	$currency_sign = $currency_sign_arr[$data_array[0][csf("currency_id")]];
	$supplier_name = return_field_value("supplier_name","lib_supplier","id=".$data_array[0][csf("supplier_id")],"supplier_name");
	$supplier_add = return_field_value("address_1","lib_supplier","id=".$data_array[0][csf("supplier_id")],"address_1");

	if($all_pi_ids!="")
	{
		$pi_sql="select a.ID as PI_ID, a.PI_NUMBER, a.HS_CODE, a.PI_DATE, a.ITEM_CATEGORY_ID, b.EMBELL_NAME,b.SERVICE_TYPE from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.status_active=1 and b.status_active=1 and a.id in($all_pi_ids) order by a.ID";
		// echo $pi_sql;die;
		$pi_sql_resutl=sql_select($pi_sql);
		$pi_datas=array();
		foreach($pi_sql_resutl as $row)
		{
			$pi_datas[$row["PI_ID"]]["PI_NUMBER"]=$row["PI_NUMBER"];
			$pi_datas[$row["PI_ID"]]["HS_CODE"]=$row["HS_CODE"];
			$pi_datas[$row["PI_ID"]]["PI_DATE"]=$row["PI_DATE"];
			$pi_datas[$row["PI_ID"]]["ITEM_CATEGORY_ID"]=$row["ITEM_CATEGORY_ID"];
			$pi_datas[$row["PI_ID"]]["SERVICE_TYPE"]=$row["SERVICE_TYPE"];
			$pi_datas[$row["PI_ID"]]["EMBELL_NAME"].=$row["EMBELL_NAME"].",";
		}
		
	}
	
	
	//$pi_number_arr=return_library_array( "SELECT id, pi_number from com_pi_master_details where id in(".$data_array[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0",'id','pi_number');
//	//$pi_date_arr=return_library_array( "SELECT id, pi_date from com_pi_master_details where id in(".$data_array[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0",'id','pi_date');
//	$hs_code_arr=return_library_array( "SELECT id, HS_CODE from com_pi_master_details where id in(".$data_array[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0",'id','HS_CODE');
//
//	 $pi_date = return_field_value("pi_date","com_pi_master_details"," status_active = 1 AND is_deleted = 0 AND id=".$data_array[0][csf("pi_id")],"pi_date");
//     $pi_max_date=return_field_value("max(pi_date)","com_pi_master_details"," status_active = 1 AND is_deleted = 0 AND id in(".$data_array[0][csf("pi_id")].")");

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
        body{font-size: 14px; line-height: 14px; }
        .template{ width: 960px; margin: 0 auto; }
        .clear{ overflow: hidden; }
        .headersection{ height: 145px; margin-top: 20px;}
        .title {
            float: left;
            /* margin-left: 5px; */
            margin-top: 8px;
            width: 800px;
            }
        .title-box{
            float: right;
            width: 120px;
            height: 120px;
            border: 1px solid black;
            text-align: center;
        }
        /* Thick red border */
        hr.new4 {
            border: 1px solid black;
            width: 282px;
            margin-left: 204px;
        }
            
        .lc-date{
            width: 465px;
            float: right;
            margin-top: 10px;
        }

        .lc-no{
            border: 1px solid black;            
            height: 30px;
        }
        .date{
            width: 465px;
            float: right;
        }
        .date-span{
            border-bottom: 2px dotted black;
            width: 250px;
            margin-left: 5px;
        }
        .maintop {
            margin-top: 100px;
            background-color: #30778b;
            text-align: center;
            height: 43px;
        }
        
        .maintop h1 {
        color: #f6faf8;
        margin-top: 15px;
        }

        .box{
            width: 30px;                      
            height: 30px;
            border: 1px solid grey; 
        }
        table{
            width: 960px;
        }
        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
        }
        /* .table-span{
            color: red;
        } */
        .od{
            border-top: 1px solid black;
            margin-left: 190px;
            border-right: 200px;
            
        }
        /* .hr-lc{
            margin-left: 130px;
            border-bottom: 1px solid black;
        }
        .hr-date{
            margin-left: 100px;
            border-bottom: .1em solid black;
        } */
        .doc5{
            border-top: .1px solid black;
            margin-left: 278px;
            width: 330px;
            margin-top: 0px;
        }
        .doc6{
            border-bottom: .1px solid black;
            margin-left: 372px;
            width: 237px;
            margin-top: 0px;
        }
        .doc7{
            border-top: .1px solid black;
            margin-left: 211px;
            width: 396px;
        }
        .lc-date1{
            border-bottom: 1px solid black;
            width: 203px;
            margin-left: 88px;
        }
        .lc-no1{
            border-bottom: 1px solid black;
            width: 176px;
            margin-left: 115px;
        }
        .fport{
            font-size: 19px;
            margin-top: 2px;
            margin-bottom: -14px;
            border-left: 1px solid black;
            height: 21px;
            padding-left: 10px;
        }

        .table-port{
            margin-left: 10px;
        }
        .doc8{
            border-bottom: .1px solid black;
            width: 200px;
            text-align: center;
            font-size: 16px;
        }
        .doc9{
            border-bottom: .1px solid black;
            width: 200px;
            text-align: center;
            font-size: 16px;
        }
        .security{
            position: relative;
            width: 268px;
            height: 85px;
            border: 1px solid black;
            float: left;
        }
        .security1{
            position: absolute;
            width: 242px;
            height: 85px;
            border: 1px solid black;
            margin-left: 345px;
        }
        .security2{
            position: absolute;
            width: 330px;
            height: 85px;
            margin-left: 628px;
            
        }
        .mainfooter {
            margin-top: 10px;
            background-color: #30778b;
            text-align: center;
            height: 30px;
        }
       
        .mainfooter h3 {
        color: #f6faf8;
        font-size: 20px;
        margin-top: 8px;
        }

        .table-port1{
            font-size: 18px;
            text-justify: inter-word;
            padding: 4px;
            margin: 0px; 
            line-height: 19px;
        }
        .s2{
        position: relative;
        opacity: 0.5;
        right: 15px;
        top: 3px;
        margin-right: -10px;
       }
	     .s3{
        position: relative;
    	left: -40px;
	 	bottom: 5px;
        margin-right: -20px;
       }
       @media print {
        .dd{
            background-color: #30778b !important;
            color: aliceblue;
            -webkit-print-color-adjust: exact; 
        }
        .mainfooter {
            background-color:#30778b !important;
            color: aliceblue;
            -webkit-print-color-adjust: exact; 
          
        }
        .maintop {
      
            background-color: #30778b !important;
            color: aliceblue;
            -webkit-print-color-adjust: exact; 
           
        }
        }

        .ot{
            font-size:19px; margin-top: 2px; margin-bottom: 5px;
        }
        .ot1{
            font-size:19px; margin-top: 2px; margin-bottom: 5px;
        }
        .ot2{
            font-size:19px; margin-top: 0px; margin-bottom: 9px;
        }
        .ot3{
            margin-bottom: 5px;
        }
       
        /* table, tr td.noBorder {
        border-top: 0;
        border-right: 0;
        } */

        .top-table-port{
            font-size: 19px; margin-top: 1px; margin-left: 10px; margin-bottom: 2px;
        }
        .top-table-port1{
            margin-left: 10px;
            margin-top: 0px;
            font-size: 18px;
            margin-bottom: 4px;
            line-height: 20px;
        }
        .top-table-port2{
            font-size: 19px; margin-top: 2px; margin-bottom: 4px;
        }
        .top-table-port3{
            font-size: 19px; margin-top: 2px; margin-bottom: -4px;
        }
        .top-table-port4{
            font-size: 19px; margin-top: 1px; margin-bottom: 2px;
        }
        .full-set{
            font-size: 19px; line-height: 20px; margin-bottom: 5px; margin-top: 5px;
        }
       
           
    </style>

<body>

    <div class="template clear">
        <div class="headersection clear">
            <div class="title clear">
                <img src="../application_form/form_image/sjbl_logo.jpg" width="800"> 
            </div>
            <div class="title-box clear">
                <p style="padding: 25px; font-size: 18px; line-height: 20px;">Adhesiv<br>Stamp</p>               
            </div>
        </div> 

        <hr class="new4 clear">
        <div class="lc-date">
            <div class="lc-no clear">
                <p style="margin-top: 10px; margin-left: 10px; font-size: 18px;">L/C NO. </p>
            </div>
            <div class="date clear">
                <p class="" style="font-size: 18px;">Date...........................................................</p>
            </div>
        </div>
        <div class="maintop clear">
            <h1>Application and Agreement for Irrevocable Letter of Credit</h1>
        </div>
        <div class="clear" style="margin-top: 5px;">
           
            <p style="font-size: 19px; margin-top: 2px; margin-bottom: 0px;">I/We would would request you to please open an irrevocable letter of credit letter of credit through your correspondent</p>
            <p style="font-size: 19px; margin-top: 5px;">by &nbsp;&nbsp;<span style='font-size:25px;'>&#9633;</span> <span class="s2" style='font-size:25px;'>&#9633;</span>&nbsp;&nbsp;   Mail/Airmail &nbsp;&nbsp;<span style='font-size:25px;'>&#9633;</span> <span class="s2" style='font-size:25px;'>&#9633;</span>&nbsp;&nbsp;  Wire in full &nbsp;&nbsp;&nbsp;&nbsp;<span style='font-size:25px;'>&#9633;</span> <span class="s2" style='font-size:25px;'>&#9633;</span>&nbsp;&nbsp;&nbsp;&nbsp;  Wire in brief, details by mail, Airmail as follows:</p>
        </div>
        <div class="clear" style="margin-top: -9px;">
            <table>
                <tr>
                    <td style="width: 330px;">
                        <p class="top-table-port" >Beneficiary Name :  </p>
                            <p class="table-span top-table-port1">
                                <b><? echo $supplier_name;?></b>
                            </p>
                        
                    </td>                
                    <td colspan="3">
                        <p class="top-table-port" >Address : </p>
                            <p class="table-span top-table-port1">
                               <? echo $supplier_add;?> , <? echo $origin;?>
                            </p>
                        
                    </td>                
                </tr>
                <tr>
                    <td style="width: 335px;">
                        <p class="top-table-port">
                            By order of and for account of : 
                            <p class="table-span top-table-port1"><b><? echo $company_name;?></b></p>
                        </p></td>                
                    <td colspan="3">
                            <p class="top-table-port">
                            Address : </p> 
                            <p class="table-span top-table-port1">
                                <? echo $company_add[$data_array[0][csf("importer_id")]]['plot_no'].",".$company_add[$data_array[0][csf("importer_id")]]['level_no'].", ".$company_add[$data_array[0][csf("importer_id")]]['road_no'].",<br/> ".$company_add[$data_array[0][csf("importer_id")]]['city'].", ".$country_array[$company_add[$data_array[0][csf("importer_id")]]['country_id']]; ?>
                            </p>
                       
                    </td>                
                </tr>
                <tr>
                    <td style="width: 330px;" valign="top">
                       <p class="top-table-port">
                        <span>L/C Amount...</span>
                        <span class="table-span" style="margin-left: 10px;"><? echo $currency_sign.' '.number_format($data_array[0][csf('lc_value')],2);?></span>
                       </p>
                       <p class="top-table-port1">
                        <span>In words :</span>
                        <span class="table-span" style="margin-left: 45px; text-align: justify; text-align: center;">
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
							$dcurrency='CENTS';
							}
							if($currency_id==3)
							{
							$mcurrency='EURO';
							$dcurrency='CENTS';
							}
						?>
                            <? echo $mcurrency.' '.number_to_words(number_format($data_array[0][csf('lc_value')],2), '', $dcurrency);?>
                        </span>
                        </p>
                    </td>                
                    <td valign="top">
                 
                        <span style='font-size:25px; margin-left: 10px; '>&#9633;</span>
                         <span class="s2" style='font-size:25px;'>&#9633;</span> &nbsp;&nbsp; 
                        
                      <? if($data_array[0][csf("payterm_id")]==1)
		                     	{
			                    echo "<span class='s3'> &#10004; </span>" ;
		                       	}
			            ?>
                         <strong style="font-size: 19px;"> At sight</strong><br>
                         
                        <span style='font-size:25px; margin-left: 10px;'>&#9633;</span>
                        <span class="s2" style='font-size:25px;'>&#9633;</span>&nbsp;&nbsp; 
                        <? if($data_array[0][csf("payterm_id")]==2)
		                     	{
									$pay_term_cond = $data_array[0][csf("tenor")];
			                    echo "<span class='s3'> &#10004; &nbsp; &nbsp; $pay_term_cond </span>" ;
		                       	} 
			            ?>
                        <span style="font-size: 19px;">Days usance</span><br>
                        <span style='font-size:25px; margin-left: 10px;'>&#9633;</span> 
                        <span class="s2" style='font-size:25px;'>&#9633;</span>&nbsp;&nbsp; 
                        <strong style="font-size: 17px;">Export Development Fund</strong>
                    </td>                
                    <td valign="top">
                        <span style='font-size:25px; margin-left: 10px;'>&#9633;</span> 
                        <span class="s2" style='font-size:25px;'>&#9633;</span>&nbsp;
                        <? if($data_array[0][csf("inco_term_id")]==1){ echo "<span class='s3'> &#10004; </span>" ; } ?>
                        <span style="font-size: 18px;">FOB</span>

                        <span style='font-size:25px; margin-left: 10px;'>&#9633;</span> 
                        <span class="s2" style='font-size:25px;'>&#9633;</span>&nbsp; 
                        <? if($data_array[0][csf("inco_term_id")]==2){ echo "<span class='s3'> &#10004; </span>" ; }?>
                        <span style="font-size: 18px;">CFR</span><br>

                        <span style='font-size:25px; margin-left: 10px;'>&#9633;</span> 
                        <span class="s2" style='font-size:25px;'>&#9633;</span>&nbsp;
                        <? if($data_array[0][csf("inco_term_id")]==4){ echo "<span class='s3'> &#10004; </span>" ; }?>
                        <span style="font-size: 18px;">FCA</span>

                        <span style='font-size:25px; margin-left: 10px;'>&#9633;</span> 
                        <span class="s2" style='font-size:25px;'>&#9633;</span>&nbsp;
                        <? if($data_array[0][csf("inco_term_id")]==5){ echo "<span class='s3'> &#10004; </span>" ;	} ?>
                        <span style="font-size: 18px;">CPT</span><br>

                        <span style='font-size:25px; margin-left: 10px;'>&#9633;</span> 
                        <span class="s2" style='font-size:25px;'>&#9633;</span>&nbsp;
                        <? if($data_array[0][csf("inco_term_id")]==6){ echo "<span class='s3'> &#10004; </span>" ; }?>
                        <span style="font-size: 18px;">EXW</span>
                    </td>                
                    <td>
                        <span style='font-size:25px; margin-left: 10px;'>&#9633;</span> 
                        <span class="s2" style='font-size:25px;'>&#9633;</span>&nbsp;
                         <? if($data_array[0][csf("maturity_from_id")]==1){echo "<span class='s3'> &#10004; </span>" ;} ?>
                        <span style="font-size: 19px;">By Acceptance</span><br>

                        <span style='font-size:25px; margin-left: 10px;'>&#9633;</span> 
                        <span class="s2" style='font-size:25px;'>&#9633;</span>&nbsp;
                         <? if($data_array[0][csf("maturity_from_id")]==2){echo "<span class='s3'> &#10004; </span>" ;} ?>
                        <span style="font-size: 19px;">By Shipment</span><br>
                        
                        <span style='font-size:25px; margin-left: 10px;'>&#9633;</span> 
                        <span class="s2" style='font-size:25px;'>&#9633;</span>&nbsp;
                         <? if($data_array[0][csf("maturity_from_id")]==3){echo "<span class='s3'> &#10004; </span>" ;} ?>
                        <span style="font-size: 19px;">By Negotiation</span><br>

                        <span style='font-size:25px; margin-left: 10px;'>&#9633;</span> 
                        <span class="s2" style='font-size:25px;'>&#9633;</span>&nbsp;
                         <? if($data_array[0][csf("maturity_from_id")]==4){echo "<span class='s3'> &#10004; </span>" ;} ?>
                        <span style="font-size: 19px;">By B/L</span><br>

                        <span style='font-size:25px; margin-left: 10px;'>&#9633;</span> 
                        <span class="s2" style='font-size:25px;'>&#9633;</span>&nbsp;
                         <? if($data_array[0][csf("maturity_from_id")]==5){echo "<span class='s3'> &#10004; </span>" ;} ?>
                        <span style="font-size: 19px;">By Delivery Challan</span><br>

                    </td>                
                </tr>
                <tr>
                    <td style="width: 700px; " colspan="3">
                        <p class="top-table-port">For shipment of :</p>
                        <span class="table-span">
                            <p class="top-table-port">1. 
							<? 
							$pi_id_arr=explode(",",$data_array[0][csf("pi_id")]);
                            $pi_num_date="";
							foreach($pi_id_arr as $pi_ids)
							{
								$pi_num_date.='PI: '.$pi_datas[$pi_ids]["PI_NUMBER"].'&nbsp;DT: '.change_date_format($pi_datas[$pi_ids]["PI_DATE"]).", ";
								$pi_hs_code.=$pi_datas[$pi_ids]["HS_CODE"].",";
								$service_type=$service_type[$pi_datas[$pi_ids]["SERVICE_TYPE"]];
								$emb_name_arr=explode(",",chop($pi_datas[$pi_ids]["EMBELL_NAME"],","));
								foreach($emb_name_arr as $emb_id)
								{
									if($emb_check[$emb_id]=="")
									{
										$emb_check[$emb_id]=$emb_id;
										$emb_name.=$emblishment_name_array[$emb_id].",";
									}
								}
							}
							//$pi_num=chop($pi_num,",");
                            $pi_hs_code=chop($pi_hs_code,",");$emb_name=chop($emb_name,",");
                            if($emb_name==''){$emb_name=$service_type;}
							echo $item_category[$pi_datas[$pi_ids]["ITEM_CATEGORY_ID"]]. " [".$emb_name."]";

                            ?> for 100% Export Oriented Knit Composite Readymade <? echo $business_nature;?> Industries.</p>
                            <p class="top-table-port">2. Quality, Quantity & unit price will be as <? echo rtrim($pi_num_date,', ');?></p>
                              <p class="top-table-port"><? if($data_array[0][csf("remarks")]!=''){echo "3. ".$data_array[0][csf("remarks")];}?></p>
                           <!-- <p class="top-table-port1">Date : 08.10.20, ASML/201008263/1022 Date:08.10.20, ASML/201008263/1023 Date:08.10.20,ASML/201008263/1024 Date:08.10.20</p>-->
                        </span>
                        <p class="top-table-port" style="margin-bottom: 5px;">(Please specify commodity only. Omit details as to grade, quantity, price etc.)</p>
                    </td>                
                    <td style="text-align: center; width: 200px;">
                        <p class="top-table-port" style="margin-top: -22px; margin-bottom: 46px;">Country of Origin</p>
                       
                        <span class="table-span" style="font-size: 16px;"> <? echo $origin;?></span>

                    </td>                
                </tr>
            </table>
        </div>
        <div class="clear">
            <p class="top-table-port4" style="margin-top: 7px; margin-bottom: 5px;">Documents required as Indicated By check (x)</p>
        
            <div style="float: left; font-size: 16px; position: relative;">
                <p class="ot2">
                    <span style='font-size:25px;'>&#9633;</span> 
                    <span class="s2" style='font-size:25px;'>&#9633;</span>
                    Commercial Invoice in Sextuplicate
                </p>
                <p class="ot2" style="margin-top: 5px;"><span style='font-size:25px;'>&#9633;</span> 
                    <span class="s2" style='font-size:25px;'>&#9633;</span>
                    Special Customs Invoice in Duplicate
                </p>
                <p class="ot2">
                    <span style='font-size:25px;'>&#9633;</span> 
                    <span class="s2" style='font-size:25px;'>&#9633;</span>
                    Packing List in sextuplicate
                </p>
                <p class="ot ">
                    <span style='font-size:25px;'>&#9633;</span> 
                    <span class="s2" style='font-size:25px;'>&#9633;</span>
                    Certificate of origine issued by
                </p>
                <p class="doc5 ot3"></p>
                <p class="ot ot3">
                    <span style='font-size:25px;'>&#9633;</span> 
                    <span class="s2" style='font-size:25px;'>&#9633;</span>
                    Preshipment inspection Certificate issued by 
                </p>
                <p class="doc6 ot3"></p>
                <p class="ot ot3">
                    <span style='font-size:25px;'>&#9633;</span> 
                    <span class="s2" style='font-size:25px;'>&#9633;</span>
                    Others Documents 
                </p>
               
                    <p class="doc7 ot1" style="margin-left: 214px; font-size: 19px; text-align: center; padding-top: 7px;">
                        (Specify the name of the Issuer )</p>
                   
            </div>
            <div style="position: absolute; margin-left: 460px; height: 10px;">
               
                   <table style="width: 500px; ">
                       <tr>
                           <td style="width: 270px;">
                               <p class="top-table-port" style="font-size: 17px !important">
                                Bangladesh Bank Reg. No.
                            </p>
                               <p style="border-bottom: 1px solid black; margin-left: 80px; font-size: 17px;">
                                <strong><? echo $bang_bank_reg_no ;?></strong></p>
                           </td>
                           <td >
                               <p class="top-table-port" style="margin-bottom: 0px; font-size: 18px !important">
                                  LCA No. <span style="margin-left: 40px;"><? echo $data_array[0][csf("lca_no")] ?></span>
                                </p>
                               <p class="lc-no1" style="margin-top: 3px;"></p>
                               <p class="top-table-port1" style="margin-bottom: 0px;">Date :</p>
                               <p class="lc-date1" style="margin-top: -3px;"></p>
                              
                           </td>
                       </tr>
                   </table>

            </div>
        </div>
        <div class="clear">
            <p class="full-set">
                Full set of clear <q>Shipped on Board</q> Ocean Bill of Lading/ Airway Bill/ Truck Receipt/ other documents relating to shipments drawn or endorsed the order of Shajalal Islami Bank Limited showing freight pre-paid/ freight to pay marked notify us and your Bank.
            </p>
        </div>
        <div class="clear">
            <p class="fport">From port... <? echo $data_array[0][csf("port_of_loading")];?>.  Country...<?=$origin;?> to(port).......<? echo $data_array[0][csf("port_of_discharge")];?>....  Bangladesh.</p><br>
            <table>
                <tr>
                    <td colspan="3" style="width: 350px;">
                        <p class="top-table-port">Insurance cover note/ policy no.</p>
                        <p class="table-span top-table-port1" ><b>  <? echo $data_array[0][csf("cover_note_no")];?> <? echo $data_array[0][csf("cover_note_date")];?> </b></p>
                        </td>                
                    <td colspan="4">
                        <p class="top-table-port">Name and Address of the Insurance Company</p>
                        <p class="table-span top-table-port1"> <? echo $data_array[0][csf('insurance_company_name')];?> </p>
                    </td>                
                </tr>
                <tr>
                    <td colspan="3" style="width: 350px;">
                        <p class="top-table-port2 table-port">
                            <span>Part shipment</span>
                            <span style='font-size:25px; margin-left: 10px;'>&#9633;</span> <span class="s2" style='font-size:25px;'>&#9633;</span>&nbsp;&nbsp;
                            <span> Allowed</span>
                            <span style='font-size:25px; margin-left: 10px;'>&#9633;</span> <span class="s2" style='font-size:25px;'>&#9633;</span>&nbsp;&nbsp; 
                            <span> Prohibited</span>
                        </p>
                    </td>                
                    <td colspan="4">
                        <p class="top-table-port2 table-port">
                            <span>Transshipment</span>
                            <span style='font-size:25px; margin-left: 10px;'>&#9633;</span> <span class="s2" style='font-size:25px;'>&#9633;</span>&nbsp;&nbsp; 
                            <span> Allowed</span>
                            <span style='font-size:25px; margin-left: 10px;'>&#9633;</span> <span class="s2" style='font-size:25px;'>&#9633;</span>&nbsp;&nbsp; 
                            <span> Prohibited</span>
                        </p>
                    </td>                
                </tr>
                <tr>
                    <td colspan="2" style="width: 350px;">
                        <p class="table-port top-table-port3">Last date of Shipment :</p>
                        <p class="doc8 table-span" style="font-size: 18px; padding-bottom: 5px;"><b><? echo $last_shipment_date;?> </b></p>
                    </td>                
                    <td colspan="2" style="width: 350px;">
                        <p class="table-port top-table-port3" >Date of Expiry :</p>
                        <p class="doc8 table-span" style="font-size: 18px; padding-bottom: 5px;"><b><? echo $lc_expiry_date;?></b></p>
                    </td>                                    
                    <td colspan="3" style="width: 350px;">
                        <p class="table-port top-table-port3">Time period for presentaion of :</p>
                        <p style="margin-left: 10px; font-size:18px">Documents <span style="width: 100px; padding-bottom: 5px;"><strong class="doc9 table-span" ><span style="margin-left: 70px; margin-right: 70px;"><? echo $data_array[0][csf("doc_presentation_days")];?></span></strong></span> days </>
                    </td>                
                </tr>
            </table>
        </div>
        <div class="clear">
            
            <p style='font-size:19px; margin-top: 5px; margin-bottom: 5px;'>Others terms and conditions, if any</p>         
            <p class="ot"><span style='font-size:25px;'>&#9633;</span> <span class="s2" style='font-size:25px;'>&#9633;</span> Advising Bank will be <? echo $data_array[0][csf("advising_bank")]." ".$data_array[0][csf("advising_bank_address")];?></p>
            <p class="ot"><span style='font-size:25px;'>&#9633;</span> <span class="s2" style='font-size:25px;'>&#9633;</span> BTB LC open against purchase Contract No. <span class="table-span"><strong>  <? echo implode(', ',$export_lc_sc_no_arr);?> </strong></span></p>
            <p class="ot"><span style='font-size:25px;'>&#9633;</span> <span class="s2" style='font-size:25px;'>&#9633;</span> Mushak-II & BTMA will be issued against <span class="table-span"><strong> ( ± 5% ) <!-- 350,000--><? echo $garments_qty = $data_array[0][csf("garments_qty")]; ?></strong></span><span style="font-size: 17px;">
            
            Pcs Readymade <? echo $business_nature;?>.</span></p>
            <p class="ot"><span style='font-size:25px;'>&#9633;</span> <span class="s2" style='font-size:25px;'>&#9633;</span> Cash Incentive will be drawn by <? echo $company_name;?></p>
            <p class="full-set">The Security Agreement on the reverse is hereby unconditionally acceptable to the undersigned and made applicable to this application and the Letter of Credit. </p>
           
        </div>

        <div class="clear">
            <div class="security"></div>          
            <div class="security1"></div>   
            <div class="security2" style="padding-left: 32px;">
                <table style="width: 300px; ">
                    <tr>
                        <td style="width: 10px;height: 85px;"></td>
                        <td style="width: 10px;height: 85px;"></td>
                        <td style="width: 10px;height: 85px;"></td>
                        <td style="width: 10px;height: 85px;"></td>
                        <td style="width: 10px;height: 85px;"></td>
                        <td style="width: 10px;height: 85px;"></td>
                        <td style="width: 10px;height: 85px;"></td>
                        <td style="width: 10px;height: 85px;"></td>
                        <td style="width: 10px;height: 85px;"></td>
                        <td style="width: 10px;height: 85px;"></td>
                        <td style="width: 10px;height: 85px;"></td>
                       
                   
                    </tr>
                </table>
            </div>
             
        </div>
         <div class="clear" style="font-size: 17px; margin-top: 10px; padding-bottom: 2px;">
             <span style="padding-right: 150px; ">Signature of Guarantor(if any)</span>
             <span  style="padding-right: 190px;">Signature of Applicants(s)</span>
             <span>Account No</span>
        </div>
      
        <div class="mainfooter clear">
            <h3>FOR BANK'S USE ONLY</h3>
        </div>
        <div class="clear" style="margin-top: 10px; ">
            <table>
                <tr>
                    <td rowspan="2" style="width: 350px;">
                        <p class="table-port1">L/C Amount in Foreign Currency</p>
                    </td>                
                    <td rowspan="2" style="width: 350px;">
                        <p class="table-port1">Exchange Rate</p>
                    </td>                                    
                    <td rowspan="2" style="width: 350px;">
                        <p class="table-port1" >Equivalent Taka</p>
                    </td>
                    <td colspan="2" style="width: 350px;">
                        <p class="table-port1">Margin</p>
                    </td>
                    <td colspan="3" style="width: 350px;">
                        
                    </td>                
                </tr>
                <tr>
                    
                    <td style="width: 350px;">
                        <p class="table-port1" >Percentage</p>
                    </td>
                    <td style="width: 350px;">
                        <p class="table-port1" >Amount</p>
                    </td>
                    <td style="width: 350px;">
                        <p class="table-port1">Originating Officer</p>
                    </td>
                    <td style="width: 350px;">
                        <p class="table-port1">Department In Charge</p>
                    </td>
                    <td  style="width: 350px;">
                       <p class="table-port1">Brance Manager</p> 
                    </td>                
                </tr>
                <tr>
                    <td style="width: 350px;" >
                        
                    </td>                
                    <td  style="width: 350px;">
                       
                    </td>                                    
                    <td  style="width: 350px;">
                       
                    </td>
                    <td style="width: 350px;">
                       
                    </td>
                    <td style="width: 350px;">
                      
                    </td>
                    <td style="width: 350px;">
                       
                    </td>
                    <td style="width: 350px;">
                       
                    </td>
                    <td  style="width: 350px;" >
                       <p class="table-port1">&nbsp;</p> 
                    </td>                
                </tr>
            </table>
        </div>
        <div class="clear" style="margin-top: 5px;">
            <small class="table-port1" style="font-size: 17px;">SJIBL-0139001, REL, January-16, 500 Pads</small>
        </div>

    </div>
    
    
    
</body>
    <?
	exit();
}

?>


