<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="btb_application_form")
{

	 $sql = "SELECT	importer_id,issuing_bank_id,lc_value,lc_date,currency_id,tenor,supplier_id,item_category_id,pi_id,port_of_loading,port_of_discharge,delivery_mode_id,last_shipment_date,lc_expiry_date,inco_term_place,doc_presentation_days,origin,inco_term_id,insurance_company_name,lcaf_no,cover_note_no,cover_note_date,payterm_id,tolerance,lca_no,lc_number,lc_date,btb_system_id,pi_id,partial_shipment,transhipment from com_btb_lc_master_details where id='$data'";
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
            $sql_sc=sql_select("SELECT id,contract_no,contrct_date FROM com_sales_contract where id=".$row[csf('lc_sc_id')]);
            foreach($sql_sc as $sc_row)
            {
                $export_lc_sc_no_arr[$sc_row[csf("id")]]=$sc_row[csf("contract_no")]." DT :".$sc_row[csf("contrct_date")];
                
            }
        }
               
        $lc_sc_id_arr[]=$row[csf('lc_sc_id')];

    }


	$order_pcs_qty = return_field_value("sum(b.order_quantity) as order_quantity","com_export_lc_order_info a,wo_po_color_size_breakdown b","b.po_break_down_id=a.wo_po_break_down_id and a.com_export_lc_id in(".implode(',',$lc_sc_id_arr).")","order_quantity");


//--------------lib
	$currency_sign_arr=array(1=>'৳',2=>'$',3=>'€',4=>'€',5=>'$',6=>'£',7=>'¥');
	$company_name = return_field_value("company_name","lib_company","id=".$data_array[0][csf("importer_id")],"company_name");
    $irc_no = return_field_value("irc_no","lib_company","id=".$data_array[0][csf("importer_id")],"irc_no");
    $company_irc_expiry_date = return_field_value("irc_expiry_date","lib_company","id=".$data_array[0][csf("importer_id")],"irc_expiry_date");
    if($company_irc_expiry_date!="")
	{
		$month_irc=date('m',strtotime($company_irc_expiry_date)); 
		$year_irc=date('Y',strtotime($company_irc_expiry_date)); 
		$irc_date = strtotime($company_irc_expiry_date);
		if($year_irc>6)
		{
			$renewal_irc=date('Y', mktime(0,0,0,1,1,date('Y',$irc_date)+1));
		}
		else
		{
			$renewal_irc=date('Y', mktime(0,0,0,1,1,date('Y',$irc_date)-1));
		}
		if($year_irc>$renewal_irc){$renewal_year=$renewal_irc.'-'.$year_irc;}else{$renewal_year=$year_irc.'-'.$renewal_irc;}
	}

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

	$bank_address = return_field_value("ADDRESS","lib_bank","id=".$data_array[0][csf("issuing_bank_id")],"ADDRESS");
	$currency_sign = $currency_sign_arr[$data_array[0][csf("currency_id")]];


	$supplier_name = return_field_value("supplier_name","lib_supplier","id=".$data_array[0][csf("supplier_id")],"supplier_name");
	$supplier_add = return_field_value("address_1","lib_supplier","id=".$data_array[0][csf("supplier_id")],"address_1");

	//echo "select id, pi_number from com_pi_master_details where id in(".$data_array[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0";
	$pi_number_arr=return_library_array( "SELECT id, pi_number from com_pi_master_details where id in(".$data_array[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0",'id','pi_number');
	
	$hs_code_arr=return_library_array( "SELECT id, HS_CODE from com_pi_master_details where id in(".$data_array[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0",'id','HS_CODE');

	$pi_date = return_field_value("pi_date","com_pi_master_details"," status_active = 1 AND is_deleted = 0 AND id=".$data_array[0][csf("pi_id")],"pi_date");



	//echo $total_pi=count($pi_number_arr);

	$pi_numbers;

	if(count($pi_number_arr)>1){
		foreach($pi_number_arr as $row){
			$pi_numbers .= $row.",";
		}
		$pi_date='';
	}
	else
	{
		$pi_numbers = $pi_number_arr[$data_array[0][csf("pi_id")]];
		$pi_date=date('d.m.Y',strtotime($pi_date));
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

   <style>
        body{margin:0;padding:0; font-size: 90%;
            background: url("../application_form/form_image/image/prime_cf7.jpg");
            background-size:21.59cm 30.56cm;
            background-repeat: no-repeat;
        }
        .template{ width: 960px;}
        .clear{ overflow: hidden; }
        p{margin:0;padding:0;}
        #position0{
            position: absolute;
            margin-top: 60px;
            margin-left: 50px;
            width: 350px;
        }
        #position1{
            position: absolute;
            margin-top: 240px;
            margin-left: 260px;
        }
        #position2{
            position: absolute;
            margin-top: 310px;
            margin-left: 240px;
        }
        #position3{
            position: absolute;
            margin-top: 370px;
            margin-left: 160px;
        }
        #position4{
            position: absolute;
            margin-top: 370px;
            margin-left: 520px;
        }
        #position5{
            position: absolute;
            margin-top: 410px;
            margin-left: 50px;
        }
        #position6{
            position: absolute;
            margin-top: 403px;
            margin-left: 195px;
            width: 190px;
        }
        #position7{
            position: absolute;
            margin-top: 385px;
            margin-left: 405px;
        }
         #position8{
            position: absolute;
            margin-top: 425px;
            margin-left: 405px;
        }
        #position9{
            position: absolute;
            margin-top: 385px;
            margin-left: 525px;
        }
        #position10{
            position: absolute;
            margin-top: 385px;
            margin-left: 585px;
        }
        /* #position8{
            position: absolute;
            margin-top: 400px;
            margin-left: 560px;
        } */
        /* #position9{
            position: absolute;
            margin-top: 400px;
            margin-left: 630px;
        } */
        /* #position10{
            position: absolute;
            margin-top: 440px;
            margin-left: 415px;
        } */
        #position11{
            position: absolute;
            margin-top: 420px;
            margin-left: 525px;
        }
        #position12{
            position: absolute;
            margin-top: 420px;
            margin-left: 580px;
        }
      
        #position13{
            position: absolute;
            margin-top: 465px;
            margin-left: 130px;
        }
        #position14{
            position: absolute;
            margin-top: 520px;
            margin-left: 350px;
        }
        #position15{
            position: absolute;
            margin-top: 540px;
            margin-left: 105px;
        }
        #position16{
            position: absolute;
            margin-top: 540px;
            margin-left: 280px;
        }
        #position17{
            position: absolute;
            margin-top: 590px;
            margin-left: 280px;
        }
        #position18{
            position: absolute;
            margin-top: 585px;
            margin-left: 420px;
        }
        #position19{
            position: absolute;
            margin-top: 635px;
            margin-left: 70px;
        }
        #position20{
            position: absolute;
            margin-top: 665px;
            margin-left: 155px;
        }
        #position21{
            position: absolute;
            margin-top: 665px;
            margin-left: 385px;
        }
        #position22{
            position: absolute;
            margin-top: 710px;
            margin-left: 40px;
        }
        #position23{
            position: absolute;
            margin-top: 710px;
            margin-left: 385px;
        }
        #position24{
            position: absolute;
            margin-top: 735px;
            margin-left: 40px;
        }
        #position25{
            position: absolute;
            margin-top: 780px;
            margin-left: 40px;
        }
        #position26{
            position: absolute;
            margin-top: 840px;
            margin-left: 190px;
        }
        #position27{
            position: absolute;
            margin-top: 840px;
            margin-left: 535px;
        }
        #position28{
            position: absolute;
            margin-top: 890px;
            margin-left: 410px;
        }
        #position33{
            position: absolute;
            margin-top: 890px;
            margin-left: 110px;
        }
        #position29{
            position: absolute;
            margin-top: 928px;
            margin-left: 140px;
        }
        #position30{
            position: absolute;
            margin-top: 928px;
            margin-left: 240px;
        }       
        #position31{
            position: absolute;
            margin-top: 928px;
            margin-left: 508px;
        }
        #position32{
            position: absolute;
            margin-top: 928px;
            margin-left: 618px;
        }
        #position34{
            position: absolute;
            margin-top: 555px;
            margin-left: 120px;
        }
        #position35{
            position: absolute;
            margin-top: 600px;
            margin-left: 100px;
        }
    </style>

<body>
    <div class="template clear">
        <div id="position0">
        <p><? echo $branch;?></p>
            <p><? echo $bank_address;?></p>
        </div>
        <div id="position1">
            <p><? echo $supplier_name;?>,
             <? echo $supplier_add;?>
            </p>
        </div>
        <div id="position2">
            <p><? echo $company_name;?>,
                <? echo $company_add[$data_array[0][csf("importer_id")]]['plot_no'].",".$company_add[$data_array[0][csf("importer_id")]]['level_no'].", ".$company_add[$data_array[0][csf("importer_id")]]['road_no'].",<br/> ".$company_add[$data_array[0][csf("importer_id")]]['city'].", ".$country_array[$company_add[$data_array[0][csf("importer_id")]]['country_id']]; ?>
            </p>
           
        </div>
        <div id="position3">
            <p><? echo $company_add[$data_array[0][csf("importer_id")]]['tin_number'];?> </p>
        </div>
        <div id="position4">
            <p><? echo $company_add[$data_array[0][csf("importer_id")]]['vat_number'];?></p>
        </div>
        <div id="position5">
            <p>
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
                <? echo $currency_sign.' '.number_format($data_array[0][csf('lc_value')],2);?> 
            </p>
        </div>
        <div id="position6">
            <p>
                <? echo number_to_words(number_format($data_array[0][csf('lc_value')],2), $mcurrency, $dcurrency);?>
            </p>
        </div>

        <? 
            if($data_array[0][csf("payterm_id")]==1){
                $pay_term_cond = $pay_term[$data_array[0][csf("payterm_id")]];
                echo "<div id='position7'><p>&#10003; $pay_term_cond</p></div>";
            }else{
                $pay_term_cond = $data_array[0][csf("tenor")];
                echo "<div id='position8'><p>&#10003; &nbsp;$pay_term_cond </p></div>";
            } 
        ?>  

        <!-- <div id="position7"><p>&#10003; </p></div>
        <div id="position8"><p>&#10003; </p></div> -->

         <?
            $inco_term_id = $data_array[0][csf("inco_term_id")];
        //  echo $inco_term_id ;
            //$mcurrency, $dcurrency;
        //  $inco_term_id="";
            if($inco_term_id==2)
            {
            echo "<div id='position9'><p>&#10003; </p></div>" ;
            }
            elseif($inco_term_id==1)
            {
            echo "<div id='position10'><p>&#10003; </p></div>" ;
            }
            elseif($inco_term_id==3)
            {
            echo "<div id='position11'><p>&#10003; </p></div>" ;
            }
            elseif($inco_term_id !=2 || $inco_term_id !=1 || $inco_term_id !=3)
            {
            echo '';
            }
        ?>
       <!--  <div id="position9"><p>&#10003; </p></div>
        <div id="position10"><p>&#10003;</p></div>
        <div id="position11"><p>&#10003; </p></div>
        <div id="position12"><p>&#10003; </p></div> -->

        <div id="position13">
        <p><? echo $item_category[$data_array[0][csf("item_category_id")]];
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
        For 100% Export Oriented Knit Composite Readymade <? echo $business_nature;?> Industries.</p>
        <p>SALES CONTRACT NO: <? echo implode(', ',$export_lc_sc_no_arr);?></p>
            <p> 
        </div>
        <div id="position14">
            <p><? echo chop($pi_numbers,",");?> </p>
        </div>
        <div id="position15">
            <p> <? echo chop($pi_date,",");?></p>
        </div>
        <div id="position16">
            <p><? echo $supplier_name;?></p>
        </div>
        <div id="position17">
            <p><? echo $data_array[0][csf("lcaf_no")];?></p>
        </div>
        <div id="position18">
            <p><? echo $irc_no;?></p>
        </div>
        <div id="position19">
            <!-- <p>irc no 19</p> -->
        </div>
        <div id="position20">
            <p><? echo $data_array[0][csf("port_of_loading")];?> </p>
        </div>
        <div id="position21">
            <p><? echo $data_array[0][csf("port_of_discharge")];?></p>
        </div>
        <div id="position22">
            <!-- <p>&#10003; </p> -->
        </div>
        <div id="position23">
            <!-- <p>&#10003;</p> -->
        </div>
        <div id="position24">
            <!-- <p>&#10003;</p> -->
        </div>
        <div id="position25">
            <!-- <p>&#10003;</p> -->
        </div>
        <div id="position26">
            <p><? echo $last_shipment_date;?></p>
        </div>
        <div id="position27">
            <p><? echo $lc_expiry_date?></p>
        </div>
        <div id="position33">
            <p>
            <? echo $data_array[0][csf("cover_note_no")] ."<br>". "Date : ". $data_array[0][csf("cover_note_date")]?>
            </p>
        </div>
        <div id="position28">
            <p>
                <? echo $data_array[0][csf("insurance_company_name")];?>
            </p>
        </div>
        <?  
            $partial_shipment = $data_array[0][csf("partial_shipment")];
                if($partial_shipment==1)
                {
                echo "<div id='position29'><p>&#10003;</p></div>" ;
                }
                elseif($partial_shipment==2)
                {
                echo "<div id='position30'><p>&#10003; </p></div>" ;
                }
                elseif($partial_shipment !=1 || $partial_shipment !=2)
                {
                echo '';
                }

        ?>
        <!-- <div id="position29"><p>&#10003; </p></div>
        <div id="position30"><p>&#10003; </p></div> -->
        <?  
            $transhipment = $data_array[0][csf("transhipment")];
                if($transhipment==1)
                {
                echo "<div id='position31'><p>&#10003; </p> </div>" ;
                }
                elseif($transhipment==2)
                {
                echo "<div id='position32'> <p> &#10003; </p></div>" ;
                }
                elseif($transhipment !=1 || $transhipment !=2)
                {
                echo '';
                }

        ?>
        <!-- <div id="position31"><p>&#10003; </p> </div>
        <div id="position32"> <p> &#10003; </p></div> -->
        <div id="position34">
            <p> <? echo $hs_code;?></p>
        </div>
        <div id="position35">
        <p><? echo $origin;?></p>
        </div>
        
    </div>
</body>
    <?
	exit();
}

?>

