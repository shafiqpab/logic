<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../includes/common.php');
extract($_REQUEST);

if ($action=="load_drop_down_location")
{
    echo create_drop_down( "cbo_location", 160, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "","","","","","",3 );          
    exit();
}

if ($action=="get_financial_parameter_data")
{
    $dataEx = explode("__", $data);
    $date = date('d-M-Y',strtotime($dataEx[1]));

    $rate_for = $dataEx[0];
    $po_id = $dataEx[4];
    $item_id = $dataEx[5];
    $job_id = $dataEx[7];
    $sl = explode("_", $dataEx[2]);

    // ========================== getting previous wo qty ===============================
    $wo_qty = return_field_value("sum(wo_qty) as wo_qty","garments_service_wo_dtls","po_id=$po_id and item_id=$item_id and rate_for=$rate_for and status_active=1 and is_deleted=0","wo_qty");
    $po_qty = return_field_value("sum(order_quantity) as order_quantity","wo_po_color_size_breakdown","po_break_down_id=$po_id and item_number_id=$item_id and status_active=1 and is_deleted=0 ","order_quantity");
    $set_qty = return_field_value("total_set_qnty","wo_po_details_master","id=$job_id and status_active=1 and is_deleted=0 ","total_set_qnty");
    $balance = $po_qty - $wo_qty;
    $balance2 = $balance+$dataEx[6];

    // ========================= get cm ====================
    $costing_per_sql=sql_select("SELECT a.job_id,a.costing_per,a.costing_date,b.cm_cost from wo_pre_cost_mst a, wo_pre_cost_dtls b where a.job_id=$job_id and a.job_id=b.job_id");
    $costing_per_arr=array();
    foreach($costing_per_sql as $cost_val)
    {
        $costing_per_arr[$cost_val['JOB_ID']]['costing_per'] = $cost_val['COSTING_PER'];
        $costing_per_arr[$cost_val['JOB_ID']][change_date_format($cost_val['COSTING_DATE'],'','',1)] = $cost_val['CM_COST'];
    }

    $sql_std_para=sql_select("SELECT a.COST_PER_MINUTE,a.APPLYING_PERIOD_DATE, a.APPLYING_PERIOD_TO_DATE,b.PARTICULAR_VALUE from lib_standard_cm_entry a,lib_standard_cm_entry_min_dtls b where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and b.particular=$dataEx[0] and a.company_id=$dataEx[3]");
    $cm_cost=0;    
    foreach($sql_std_para as $row )
    {
        $applying_period_date=change_date_format($row[csf('applying_period_date')],'','',1);
        $applying_period_to_date=change_date_format($row[csf('applying_period_to_date')],'','',1);
        $diff=datediff('d',$applying_period_date,$applying_period_to_date);
        for($j=0;$j<$diff;$j++)
        {
            //$newdate =change_date_format(add_date(str_replace("'","",$applying_period_date),$j),'','',1);
            $date_all=add_date(str_replace("'","",$applying_period_date),$j);
            $newdate =change_date_format($date_all,'','',1);
            $financial_para[$newdate][cost_per_minute]=$row[csf('cost_per_minute')];
            $financial_para[$newdate][particular_value]=$row[csf('particular_value')];
            if($cm_cost==0)
            {
                $cm_cost = $costing_per_arr[$job_id][$newdate];
            }
        }
    }
    // print_r($financial_para);
    // print_r($costing_per_arr); echo $job_id."==".$date."==".$costing_per_arr[29255]['25-Apr-2022'];
    
    $costingPer = $costing_per_arr[$job_id]['costing_per'];            
    if($costingPer==1) $pcs_value=1*12*$set_qty;
    else if($costingPer==2) $pcs_value=1*1*$set_qty;
    else if($costingPer==3) $pcs_value=2*12*$set_qty;
    else if($costingPer==4) $pcs_value=3*12*$set_qty;
    else if($costingPer==5) $pcs_value=4*12*$set_qty;

    // echo $cm_cost."/".$pcs_value;die();
    $cm_cost_pcs = $cm_cost/$pcs_value;


    $cost_per_minute = $financial_para[$date][cost_per_minute];
    $particular_value = $financial_para[$date][particular_value];
    // $rate = ($cost_per_minute*$particular_value)/100;
    $rate = ($cm_cost_pcs*$particular_value)/100;
    // echo $cm_cost_pcs."/".$particular_value."*100";
    echo "document.getElementById('".$dataEx[2]."').value  = '".number_format($rate,4)."';\n"; 
    echo "document.getElementById('txtwoqty_".$sl[1]."').value  = '".($balance)."';\n"; 
    echo "document.getElementById('original_".$sl[1]."').value  = '".($balance)."';\n"; 
    echo "document.getElementById('previous_".$sl[1]."').value  = '".($wo_qty)."';\n"; 
    exit();
}


if($action=="check_conversion_rate") //Conversion Exchange Rate
{ 
    $data=explode("**",$data);
    if($db_type==0)
    {
        $conversion_date=change_date_format($data[1], "Y-m-d", "-",1);
    }
    else
    {
        $conversion_date=change_date_format($data[1], "d-M-y", "-",1);
    }
    $currency_rate=set_conversion_rate( $data[0], $conversion_date,$data[2] );
    echo "1"."_".$currency_rate;
    exit(); 
}

if ($action=="set_rate_and_amount_in_details_part")
{
    // die();
    $dataEx = explode("__", $data);
    $date = date('d-M-Y',strtotime($dataEx[1]));

    $tot_rows = $dataEx[0];
    $company_id = $dataEx[2];
    $currency_id = $dataEx[3];
    $currency_id = ($currency_id==1)?2:1;
    // $a=1;
    // $jobId="jobId".$a;
    // echo $$jobId;

    $currency_rate=set_conversion_rate( $currency_id, $date,$company_id );

    for($i=1;$i<=$tot_rows;$i++)
    {        
        $jobId = "jobId".$i;
        $poId = "poId".$i;
        $itemId = "itemId".$i;
        $rateFor = "rateFor".$i;
        $jobid = $$jobId;
        $poid = $$poId;
        $itemid = $$itemId;
        $ratefor = $$rateFor;
        // ========================= get cm ====================
        $costing_per_sql=sql_select("SELECT a.job_id,a.costing_per,a.costing_date,b.cm_cost from wo_pre_cost_mst a, wo_pre_cost_dtls b where a.job_id in($jobid) and a.job_id=b.job_id");
        $costing_per_arr=array();
        foreach($costing_per_sql as $cost_val)
        {
            $costing_per_arr[$cost_val['JOB_ID']]['costing_per'] = $cost_val['COSTING_PER'];
            $costing_per_arr[$cost_val['JOB_ID']][change_date_format($cost_val['COSTING_DATE'],'','',1)] = $cost_val['CM_COST'];
        }

        // ========================== getting previous wo qty ===============================
        $wo_qty = return_field_value("sum(wo_qty) as wo_qty","garments_service_wo_dtls","po_id=$poid and item_id=$itemid and rate_for=$ratefor and status_active=1 and is_deleted=0","wo_qty");
        $po_qty = return_field_value("sum(order_quantity) as order_quantity","wo_po_color_size_breakdown","po_break_down_id=$poid and item_number_id=$itemid and status_active=1 and is_deleted=0 ","order_quantity");
        $set_qty = return_field_value("total_set_qnty","wo_po_details_master","id=$jobid and status_active=1 and is_deleted=0 ","total_set_qnty");

        $sql_std_para=sql_select("SELECT a.COST_PER_MINUTE,a.APPLYING_PERIOD_DATE, a.APPLYING_PERIOD_TO_DATE,b.PARTICULAR,b.PARTICULAR_VALUE from lib_standard_cm_entry a,lib_standard_cm_entry_min_dtls b where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and b.particular=$ratefor  and a.company_id=$company_id");
        $cm_cost=0;    
        foreach($sql_std_para as $row )
        {
            $applying_period_date=change_date_format($row[csf('applying_period_date')],'','',1);
            $applying_period_to_date=change_date_format($row[csf('applying_period_to_date')],'','',1);
            $diff=datediff('d',$applying_period_date,$applying_period_to_date);
            for($j=0;$j<$diff;$j++)
            {
                //$newdate =change_date_format(add_date(str_replace("'","",$applying_period_date),$j),'','',1);
                $date_all=add_date(str_replace("'","",$applying_period_date),$j);
                $newdate =change_date_format($date_all,'','',1);
                $financial_para[$newdate][cost_per_minute]=$row[csf('cost_per_minute')];
                $financial_para[$newdate][particular_value]=$row[csf('particular_value')];
                if($cm_cost==0)
                {
                    $cm_cost = $costing_per_arr[$jobid][$newdate]*$currency_rate;
                }
            }
        }
        // print_r($financial_para);
        // print_r($costing_per_arr); echo $jobid."==".$date."==".$costing_per_arr[29255]['25-Apr-2022'];
        
        $costingPer = $costing_per_arr[$jobid]['costing_per'];            
        if($costingPer==1) $pcs_value=1*12*$set_qty;
        else if($costingPer==2) $pcs_value=1*1*$set_qty;
        else if($costingPer==3) $pcs_value=2*12*$set_qty;
        else if($costingPer==4) $pcs_value=3*12*$set_qty;
        else if($costingPer==5) $pcs_value=4*12*$set_qty;

        // echo $cm_cost."/".$pcs_value;die();
        $cm_cost_pcs = $cm_cost/$pcs_value;


        $cost_per_minute = $financial_para[$date][cost_per_minute];
        $particular_value = $financial_para[$date][particular_value];
        // $rate = ($cost_per_minute*$particular_value)/100;
        $rate = ($cm_cost_pcs*$particular_value)/100;
        $amount = $wo_qty*$rate;
        // echo $cm_cost_pcs."/".$particular_value."*100";
        echo "document.getElementById('txtavgrate_".$i."').value  = '".number_format($rate,4)."';\n"; 
        echo "document.getElementById('txtdtlamount_".$i."').value  = '".number_format($amount,4)."';\n"; 
        // echo "document.getElementById('txtwoqty_".$i."').value  = '".($balance)."';\n"; 
        // echo "document.getElementById('original_".$i."').value  = '".($balance)."';\n"; 
        // echo "document.getElementById('previous_".$i."').value  = '".($wo_qty)."';\n"; 
    }
    exit();
}


if ($action=="load_drop_down_working_company")
{
    $data=explode("**", $data);
    if($data[0]==1)
    {
        echo create_drop_down("cbo_working_company", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0  order by comp.company_name","id,company_name",1,"-- Select Company --", $selected,"","","");
    }else{
        $sql="SELECT a.id,a.supplier_name FROM lib_supplier a,lib_supplier_party_type b,lib_supplier_tag_company c WHERE a.id=b.supplier_id and a.id=c.supplier_id and b.party_type  in(22,36) and c.tag_company =$data[1]  and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name";
       // echo $sql;
        echo create_drop_down( "cbo_working_company", 160, $sql,"id,supplier_name", 1, "-- Select Company --", $selected, "","","","","","",3 ); 
    }
             
    exit();
}

//$service_provider_arr=return_library_array("SELECT a.id,a.supplier_name FROM lib_supplier a,lib_supplier_party_type b,lib_supplier_tag_company c WHERE a.id=b.supplier_id and a.id=c.supplier_id and b.party_type =36 and c.tag_company=$cbo_company_id order by supplier_name",'id','supplier_name');

$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier where status_active=1 and is_deleted=0 order by supplier_name",'id','supplier_name');
$buyer_arr=return_library_array( "select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0 order by buyer_name",'id','buyer_name');

$subcon_buyer_arr=return_library_array( "select id,cust_buyer from subcon_ord_dtls where status_active=1 and is_deleted=0 order by cust_buyer",'id','cust_buyer');

//$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
//$size_arr = return_library_array("select id, size_name from lib_size","id","size_name");
$company_arr = return_library_array("select id, company_name from lib_company order by company_name","id","company_name");


if($action=="load_details_entry")
{ 

    list($job_str,$order_source_id,$company_id,$OrdRceveCompId,$serial)=explode("**",$data);

    foreach(explode("__",$job_str) as $job_item_po){
        list($job_id,$item_id,$order_id,)=explode("*",$job_item_po);
        $jobArr[$job_id]=$job_id;
        $itemArr[$item_id]=$item_id;
        $poArr[$order_id]=$order_id;
    }
    
    $sql="SELECT a.id,a.job_no,a.job_no_prefix_num,a.buyer_name,a.style_ref_no,b.gmts_item_id,c.id as po_id,c.po_number,c.po_quantity,a.client_id from wo_po_details_master a,wo_po_details_mas_set_details b,wo_po_break_down c where a.job_no=b.job_no and a.job_no=c.job_no_mst and a.company_name=$OrdRceveCompId and a.job_no in('".implode("','",$jobArr)."') and c.id in(".implode(',',$poArr).") and b.gmts_item_id in(".implode(',',$itemArr).")  and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
    //echo $sql;die;
    $sql_result = sql_select($sql);

    $client_arr=return_library_array("SELECT a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id   group by a.id , a.buyer_name order by buyer_name ",'id','buyer_name');


    $i=($serial+1);
    foreach($sql_result as $row)
    {
        $po_id=$row[csf('po_id')];
        $item_id=$row[csf('gmts_item_id')];
        $style_ref=$row[csf('style_ref_no')];
        $qty_res=sql_select("SELECT rate_for, sum(wo_qty) as wo_qty from garments_service_wo_dtls where status_active=1 and po_id=$po_id and item_id=$item_id and style_ref='$style_ref' group by rate_for");
        $remain=$qty_res[0][csf('wo_qty')];
        // $remain=0;
        ?>

       
                                
        <tr>
          
            <td>
                <?
                    echo create_drop_down( "cboOrdRceveCompId_".$i, 100, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 order by comp.company_name","id,company_name", 1, "--Select Company--",$OrdRceveCompId, "",1 );
                ?>
            </td>
            <td>
                 <input type="hidden" id="detailsUpdateId_<? echo $i; ?>" name="detailsUpdateId_<? echo $i; ?>" value="" />
                 <input type="text" name="txtjobno_<? echo $i; ?>" id="txtjobno_<? echo $i; ?>" class="text_boxes" style="width:100px;" placeholder="Double click to search" readonly disabled onDblClick="openmypage_job_no(1);" value="<? echo $row[csf("job_no")];?>" />
                 <input type="hidden" name="txtjobid_<? echo $i; ?>" id="txtjobid_<? echo $i; ?>" value="<? echo $row[csf("id")];?>" />
            </td>
             <td>
                 <input type="text" name="txtbuyer_<? echo $i; ?>" id="txtbuyer_<? echo $i; ?>" class="text_boxes" style="width:80px;" value="<? echo $buyer_arr[$row[csf("buyer_name")]];?>" readonly />
                 <input type="hidden" name="txtbuyerid_<? echo $i; ?>" id="txtbuyerid_<? echo $i; ?>" value="<? echo $row[csf("buyer_name")];?>" />
            </td>
             <td>
                <?php $client=$client_arr[$row[csf('client_id')]]; ?>
                 <input type="text" name="client_1" id="client_1" class="text_boxes" style="width:80px;" value="<?php echo $client; ?>" readonly />
                 <input type="hidden" name="clientid_1" id="clientid_1" value="<?php echo $row[csf('client_id')];?>" />
            </td>
             <td>
                 <input type="text" name="txtstyle_<? echo $i; ?>" id="txtstyle_<? echo $i; ?>" class="text_boxes" style="width:80px;" value="<? echo $row[csf("style_ref_no")];?>" readonly />
            </td>

            <td>
                 <input type="text" name="txtitem_<? echo $i; ?>" id="txtitem_<? echo $i; ?>" class="text_boxes" style="width:80px;" value="<? echo $garments_item[$row[csf("gmts_item_id")]];?>" readonly />
                 <input type="hidden" name="txtitemid_<? echo $i; ?>" id="txtitemid_<? echo $i; ?>" value="<? echo $row[csf("gmts_item_id")];?>" />
            </td>
            
            <td>
                
                  <input type="hidden" name="poid_<? echo $i; ?>" id="poid_<? echo $i; ?>" value="<? echo $row[csf("po_id")];?>" />

                 <input type="text" name="po_<? echo $i; ?>" id="po_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf("po_number")];?>" style="width:80px;" readonly>
            </td>

           <td>
                <input type="text" name="poqty_<? echo $i; ?>" id="poqty_<? echo $i; ?>" class="text_boxes_numeric" style="width:100px;"  value="<? echo number_format($row[csf("po_quantity")],2,'.','');?>"  readonly />
           
            
            </td>
           
          
           
            <td>
                <? 
                echo create_drop_down( "colortype_".$i, 90, $color_type,"",1, "--Select--", 1,"",0,"" ); 
                ?>                                    
            </td>
            <td>
                <? 
                echo create_drop_down( "cboratefor_".$i, 90, $rate_for,"",1, "--Select--", 1,"add_particular_rate(this.value,this.id)",0,"20,30,40" ); 
                ?>                                    
            </td>
            <td>
                <input type="text" name="txtwoqty_<? echo $i; ?>" id="txtwoqty_<? echo $i; ?>" class="text_boxes_numeric" style="width:100px;"  value="<? echo number_format($row[csf("po_quantity")]-$remain,2,'.','');?>" onKeyUp="calculate()" />
                <input type="hidden" name="original_<? echo $i; ?>" id="original_<? echo $i; ?>" value="<? echo number_format($row[csf("po_quantity")]-$remain,2,'.','');?>">
                <input type="hidden" name="previous_<? echo $i; ?>" id="previous_<? echo $i; ?>" value="">
                <input type="hidden" name="original_rate_<? echo $i; ?>" id="original_rate_<? echo $i; ?>" value="<? echo number_format($row[csf("avg_rate")],2, '.', '');?>">
            
            </td>
            <td>
                <? 
                echo create_drop_down( "cbodtlsuom_".$i, 80, $unit_of_measurement,"",1, "--Select--", 0,"",0,"1,2,58" ); 
                ?>                                    
            </td>
            <td>
               
                 <input type="text" name="txtavgrate_<? echo $i; ?>" id="txtavgrate_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px;" value="<? echo number_format($row[csf("avg_rate")],2, '.', '');?>" onKeyUp="calculate()" />
            </td>
            
            <td>
                 <input type="text" name="txtdtlamount_<? echo $i; ?>" id="txtdtlamount_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px;"  />
            </td>
            
            <td>
                 <input type="text" name="txtremarks_<? echo $i; ?>" id="txtremarks_<? echo $i; ?>" class="text_boxes" style="width:80px;"  />
            </td>
            
            <td align="center">
                <input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:27px" class="formbuttonplasminus" value="+" onClick="fn_addRow(<? echo $i; ?>,0)"/>
                <input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:27px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>,0);"/>
            </td>
            
            
        </tr>

        <?
        $i++;
    }
    //---------------------------
    exit();
}


if ($action=="job_no_popup")
{
    echo load_html_head_contents("System ID Info", "../../", 1, 1,'','','');
    extract($_REQUEST);
    ?>
    <script>
        

    function toggle( x, origColor ) {
        
        var newColor = 'yellow';
        if ( x.style ) {
            x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
        }
    }

    
    var selected_id = new Array;
    function js_set_value(str,id)
    {       
        
        /*          for( var m = 0; m < str.length; m++ ) {
                var a2=job_ids[m]+order_ids[m]+buyer_ids[m]+item_ids[m];
                if( a1 == a2 )
                {
                    alert("Same Job Order and Item Found in this Job");
                    return;
                    break;
                }
            }
        
    */       
         toggle( document.getElementById( 'tr_' + id ), '#FFFFCC' );
        
        if( jQuery.inArray( str, selected_id ) == -1 ) {
            selected_id.push(str);
            
        }
        else {
            for( var i = 0; i < selected_id.length; i++ ) {
                if( selected_id[i] == str ) break;
            }
            selected_id.splice( i, 1 );
        }
            
        
        var jobno='';
        for( var i = 0; i < selected_id.length; i++ ) {
            jobno += selected_id[i] + '__';
        }
            
        jobno = jobno.substr( 0, jobno.length - 2 );
        
        $('#txt_selected_id').val( jobno );
        
             
    }
            
        
    function close_popup()
    {
         parent.emailwindow.hide();
    
    }
        
    function fnc_close_popup_reponse()
    {
        if(http.readyState == 4) 
        {
            var reponse=http.responseText;
            if(reponse==0){parent.emailwindow.hide();}
            else{alert(reponse+" Item Found in this Job");}
        }
    }
        
        
    </script>
    </head>

    <body>
    <div align="center" style="width:1040px;">
        <form name="searchsystemidfrm"  id="searchsystemidfrm">
            <fieldset style="width:1030px;">
            <legend>Enter search words</legend>
                <table cellpadding="0" cellspacing="0" width="1000" border="1" rules="all" class="rpt_table">
                    <thead>
                        <th>Year</th>
                        <th>Buyer Name</th>
                        <th>Style</th>
                        <th>Job</th>
                        <th>Gmts Item</th>
                        <th>Po</th>
                        <th>
                            <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
                            <input type="hidden" name="txt_company_id" id="txt_company_id" value="<? echo $cbo_company_id; ?>">
                            <input type="hidden" name="order_source" id="order_source" value="<? echo $order_source; ?>">
                        </th>
                    </thead>
                    <tr>
                         <td align="center">
                            <?
                                echo create_drop_down( "cbo_year", 80, $year,"", 1, "-- Select --", date("Y",time()+2100), "",0 );
                            ?>
                        </td>
                        <td align="center">
                            <?
                                if($order_source==1){
                                    echo create_drop_down( "cbo_buyer_id", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$cbo_company_id $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",0 );
                                }
                                else if($order_source==2){
                                    echo create_drop_down( "cbo_buyer_id", 150, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in ($cbo_company_id))  group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",0 );
                                    
                                }
                            ?>
                        </td>
                       
                        <td align="center">
                            <input type="text" style="width:100px;" class="text_boxes"  name="txt_style_no" id="txt_style_no" />
                        </td>
                         <td align="center">
                            <input type="text" style="width:100px;" class="text_boxes"  name="txt_job_no" id="txt_job_no" />
                        </td>
                        <td>
                            <?php  echo create_drop_down( "gmts_item_id", 130, $garments_item,"", 1, "-- Select --", 0, "",0 ); ?>
                        </td>
                        <td align="center">
                            <input type="text" style="width:100px;" class="text_boxes"  name="txt_buyer_order" id="txt_buyer_order" />
                        </td>
                        <td align="center">
                            
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_buyer_order').value+'_'+document.getElementById('txt_style_no').value+'_'+document.getElementById('cbo_buyer_id').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('order_source').value+'_'+document.getElementById('cbo_year').value+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('gmts_item_id').value, 'create_job_no_list_view', 'search_div', 'garments_service_work_order_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                            
                        </td>
                    </tr>
                </table>
                <table width="100%" style="margin-top:5px;">
                    <tr>
                        <td colspan="5">
                            <div style="width:100%; margin-top:10px; margin-left:3px;" id="search_div" align="left"></div>
                        </td>
                    </tr>
                </table>
            </fieldset>
        </form>
    </div>
    </body>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
}

if($action=="create_job_no_list_view")
{
    
   // print_r($data) ;die;

   // po_style_65_3_1_2020_job_83
    
    list($buyer_order,$style_no,$buyer_id,$company_id,$order_source,$cob_year,$job_no,$item_id)=explode("_",$data); 


    if($order_source==1)
    {

        if($buyer_id==0)$buyer_id=" "; else $buyer_id=" and a.buyer_name =$buyer_id ";  
        
        if($buyer_order=='') $buyer_order=" "; else $buyer_order=" and c.po_number like('%".trim($buyer_order)."%') ";  
        if($style_no=='')$style_no=" "; else $style_no=" and a.style_ref_no='$style_no' ";  
        if($job_no=='')$job_no=" "; else $job_no=" and a.job_no_prefix_num='$job_no' ";    
        if($item_id==0)$item_id=" "; else $item_id=" and b.gmts_item_id='$item_id' ";    
        
            if($db_type==0)
            {
            
                if($cob_year=='')$cob_year=""; else $cob_year="and year(a.insert_date)='$cob_year'";    
            
                
            }
            else
            {
                if($cob_year=='')$cob_year=""; else $cob_year="and to_char(a.insert_date,'YYYY')='$cob_year'";  
                
                
            }
            $sql = "SELECT a.id,a.job_no,a.job_no_prefix_num,a.buyer_name,a.style_ref_no,to_char(a.insert_date,'YYYY') as year,b.gmts_item_id,c.id as po_id,c.po_number,c.po_quantity,a.client_id from wo_po_details_master a,wo_po_details_mas_set_details b,wo_po_break_down c where a.job_no=b.job_no and a.job_no=c.job_no_mst and a.company_name=$company_id  and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $buyer_id  $buyer_order  $style_no $cob_year $item_id $job_no  and a.status_active=1 group by a.id,a.job_no,a.job_no_prefix_num,a.buyer_name,a.style_ref_no,a.insert_date,b.gmts_item_id,c.id,c.po_number,c.po_quantity,a.client_id";

            // $sql = "SELECT a.id,a.job_no,a.job_no_prefix_num,a.buyer_name,a.style_ref_no,to_char(a.insert_date,'YYYY') as year,b.gmts_item_id,c.id as po_id,c.po_number,c.po_quantity,a.client_id,sum(d.wo_qty) as wo_qty,d.rate_for from wo_po_details_master a,wo_po_details_mas_set_details b,wo_po_break_down c left join garments_service_wo_dtls d on c.id=d.po_id AND d.status_active = 1 where a.job_no=b.job_no and a.job_no=c.job_no_mst and a.company_name=$company_id  and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $buyer_id  $buyer_order  $style_no $cob_year $item_id $job_no  and a.status_active=1 group by a.id,a.job_no,a.job_no_prefix_num,a.buyer_name,a.style_ref_no,a.insert_date,b.gmts_item_id,c.id,c.po_number,c.po_quantity,a.client_id,d.rate_for";//and a.style_ref_no = d.style_ref and b.gmts_item_id=d.item_id  and d.status_active=1 
           // echo $sql;

           
           

        
    }
    else
    {
        if($buyer_id==0)$buyer_id=" "; else $buyer_id=" and a.party_id =$buyer_id ";  
        
        if($buyer_order=='')$buyer_order=" "; else $buyer_order=" and b.order_no like('%".trim($buyer_order)."%') ";   
        if($style_no=='')$style_no=" "; else $style_no=" and b.cust_style_ref='$style_no' ";  
        if($job_no=='')$job_no=" "; else $job_no=" and a.job_no_prefix_num='$job_no' ";    
        if($item_id==0)$item_id=" "; else $item_id=" and c.item_id='$item_id' ";    
        
            if($db_type==0)
            {
            
                if($cob_year=='')$cob_year=""; else $cob_year="and year(a.insert_date)='$cob_year'";    
            
               
            }
            else
            {
                if($cob_year=='')$cob_year=""; else $cob_year="and to_char(a.insert_date,'YYYY')='$cob_year'";  
                
               
            }

            $sql = "SELECT a.id,a.subcon_job as job_no,a.job_no_prefix_num,a.party_id as buyer_name,b.cust_style_ref as style_ref_no,to_char(a.insert_date,'YYYY') as year,c.item_id as gmts_item_id,b.id as po_id,b.order_no as po_number,b.order_quantity as po_quantity,0 as client_id from subcon_ord_mst a,subcon_ord_dtls b,subcon_ord_breakdown c where a.id=b.mst_id and a.id=c.mst_id and b.id=c.order_id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $buyer_id  $buyer_order  $style_no $cob_year $item_id $job_no"; 
            
    }
    // echo $sql;
     $result = sql_select($sql);

    $buyer_part=return_library_array("SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id'   and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id","buyer_name");

    ?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1000" class="rpt_table">
        <thead>
            <th width="50">SL</th>
            <th width="60">Year</th>
            <th width="180">Buyer</th>
            <th width="170">Style</th>
            <th width="80">Job No</th>
            <th width="180">Item</th>
            <th width="140">PO</th>
            <th >Qty</th>



        </thead>
    </table>
    <div style="width:1020px; max-height:330px; overflow-y:scroll" id="list_container_batch" align="left">   
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1000" class="rpt_table" id="tbl_list_search">  
        <?
            $i=1;
            foreach ($result as $row)
            {  
                $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";  
                                   
               ?>
                <tr id="tr_<? echo $row[csf('id')].$i; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('job_no')].'*'.$row[csf('gmts_item_id')].'*'.$row[csf('po_id')]; ?>',<? echo $row[csf('id')].$i; ?>);"> 
                    <td width="50" align="center"><? echo $i; ?></td>
                    <td width="60" align="center"><p><? echo $row[csf('year')]; ?></p></td>
                    <td width="180"><p><? if($order_source==1)echo $buyer_arr[$row[csf('buyer_name')]]; else echo $buyer_part[$row[csf('buyer_name')]]; ?></p></td>
                    <td width="170"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
                    <td width="80" align="center"><p><? echo $row[csf('job_no_prefix_num')]; ?></p></td>
                    <td width="180"><p><? echo $garments_item[$row[csf('gmts_item_id')]]; ?></p></td>
                    <td width="140"><p><? echo $row[csf('po_number')]; ?></p></td>
                    <td  align="right"><p><? echo number_format($row[csf('po_quantity')]); ?> &nbsp;</p></td>
                </tr>
                <?
                $i++;
             
            }
            ?>
        </table>
    </div>
        <table width="100%">
            <tr>
                <td align="center">
                    <input type="hidden" name="txt_selected_id" id="txt_selected_id" value="">
                    <input type="button" value="Close" class="formbutton" onClick="close_popup();" />
                </td>
            </tr>
        </table>
        
    <?
    exit();
}


if ($action=="systemId_popup")
{
    echo load_html_head_contents("System ID Info", "../../", 1, 1,'','','');
    ?>
    <script>
        function js_set_value(id)
        { 
            $('#hidden_mst_id').val(id);
            parent.emailwindow.hide();
        }
    </script>
    
    
    </head>

    <body>
    <div align="center" style="width:840px;">
        <form name="searchsystemidfrm"  id="searchsystemidfrm">
            <fieldset style="width:830px;">
            <legend>Enter search words</legend>
                <table cellpadding="0" cellspacing="0" width="100%" border="1" rules="all" class="rpt_table">
                    <thead>
                        <th>System No</th>
                        <th>Buyer</th>
                        <th>PO</th>
                        <th>Rate For</th>
                        <th colspan="2">WO Date</th>
                        <th>
                            <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
                            <input type="hidden" name="txt_company_id" id="txt_company_id" value="<? echo $cbo_company_id; ?>">
                            <input type="hidden" id="hidden_mst_id">
                        </th>
                    </thead>
                    <tr>
                        <td align="center">
                            <input type="text" name="txt_system_id" id="txt_system_id" class="text_boxes" style="width:100px;" />
                        </td>
                        <td align="center">
                            <?
                                //echo create_drop_down( "cbo_service_provider_id", 150, $service_provider_arr,"", 1, "-- Select --", 0, "",0 );
                                
                            echo create_drop_down( "cbo_buyer_name", 120, "select buyer_name,id from lib_buyer where is_deleted=0  and status_active=1 order by buyer_name","id,buyer_name", 1, "--Select Buyer--", 0, "",0 );                          
                                
                            ?>
                        </td>
                        <td align="center">
                            <input type="text" id="txt_order" name="txt_order" class="text_boxes" style="width:100px;"/>
                        </td>
                        <td align="center">
                            <?
                                echo create_drop_down("cbo_rate_for", 100, $rate_for,"", 1,"-- Select --", 0,"","","20,30,40");
                            ?>
                        </td>
                        <td align="center">
                            <input type="text" style="width:100px;" class="datepicker"  name="txt_from_date" id="txt_from_date" readonly />   
                        </td>
                        <td align="center">
                            <input type="text" style="width:100px;" class="datepicker"  name="txt_to_date" id="txt_to_date" readonly />   
                        </td>
                        <td align="center">
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_system_id').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_order').value+'_'+document.getElementById('cbo_rate_for').value+'_'+document.getElementById('txt_from_date').value+'_'+document.getElementById('txt_to_date').value+'_'+document.getElementById('txt_company_id').value, 'price_rate_list_view', 'search_div', 'garments_service_work_order_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                        </td>
                    </tr>
                </table>
                <table width="100%" style="margin-top:5px;">
                    <tr>
                        <td colspan="5">
                            <div style="margin-top:10px; margin-left:3px;" id="search_div" align="left"></div>
                        </td>
                    </tr>
                </table>
            </fieldset>
        </form>
    </div>
    </body>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}


if($action=="price_rate_list_view")
{
    list($sysid,$buyer,$po_number,$fill_for,$from_date,$to_date,$company_id)=explode("_",$data);    
    $supp_arr = return_library_array("SELECT a.id,a.supplier_name FROM lib_supplier a,lib_supplier_party_type b,lib_supplier_tag_company c WHERE a.id=b.supplier_id and a.id=c.supplier_id and b.party_type  in(22,36) and c.tag_company =$company_id  and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id","supplier_name");

    
    if($sysid=='')$sysid=" "; else $sysid=" and a.sys_number_prefix_num='$sysid'";  
    if($buyer==0)$buyer=" "; else $buyer=" and b.buyer_id ='$buyer'";   
    if($fill_for==0)$fill_for=" "; else $fill_for=" and b.rate_for='$fill_for'";    

    if($po_number=="")$order_con=" "; else $order_con=" and c.po_number like('%".$po_number."%')";  

    if($from_date!='' && $to_date!=''){ 
        if($db_type==0){
            
            $from_date=change_date_format($from_date);
            $to_date=change_date_format($to_date);
        }
        else
        {
            $from_date=change_date_format($from_date,'','',-1);
            $to_date=change_date_format($to_date,'','',-1);
        }
        $date_con=" and a.wo_date BETWEEN '$from_date' and '$to_date'"; 
    }
    else
    {
        $date_con="";   
    }
    

    
    $sql = "SELECT a.id,a.sys_number, a.working_company_id,a.cbo_source as source, a.wo_date,sum(b.wo_qty) as wo_qty from garments_service_wo_mst a, garments_service_wo_dtls b,wo_po_break_down c where a.id=b.mst_id and b.po_id=c.id and a.company_id=$company_id  $sysid  $buyer  $fill_for $date_con  $order_con and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 group by a.id,a.sys_number, a.working_company_id,a.cbo_source, a.wo_date order by a.id desc"; 
   // echo $sql;
    $result = sql_select($sql);
    
    
    

    ?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="820" class="rpt_table">
        <thead>
            <th width="50">SL</th>
            <th width="150">System Number</th>
            <th>Working Company</th>
            <th width="100">WO Qty</th>
            <th width="112">Rate For</th>
        </thead>
    </table>
    <div style="width:815px; max-height:220px; overflow-y:scroll" id="list_container_batch" align="left">    
        <table cellspacing="0" cellpadding="0" border="1" rules="all"  width="797" class="rpt_table" id="tbl_list_search">  
        <?
            
            
            $i=1;
            foreach ($result as $row)
            {  
                $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";  
            
            ?>
                <tr id="tr_<? echo $row[csf('id')]; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"  onClick="js_set_value(<? echo $row[csf('id')]; ?>)" > 
                    <td width="50" align="center"><? echo $i; ?></td>
                    <td width="150" align="center"><p><? echo $row[csf('sys_number')]; ?></p></td>
                    <td>
                        <p>
                        <?php 

                            if($row[csf('source')]==1)
                            {
                                echo $company_arr[$row[csf('working_company_id')]];
                            }else{
                                echo $supp_arr[$row[csf('working_company_id')]];
                            }

                         ?>
                      
                            
                        </p>
                    </td>
                    <td width="100" align="right"><p><? echo number_format($row[csf('wo_qty')],2,'.',''); ?></p></td>
                    <td width="90" align="center"><? echo $rate_for[$row[csf('rate_for')]]; ?></td>
                </tr>
            <?
            $i++;
            }
            ?>
        </table>
    </div>
    
    <?


    exit();
}


if($action=="check_unique")
{
    
    $operation_arr=explode("__",$operation);
    $flag=0;
    foreach($operation_arr as $operation_values)
    { 
    list($id,$job_no,$buyer_id,$buyer_name,$style_ref_no,$gmts_item_id,$gmts_item,$po_id,$po_number)=explode("**",$operation_values);
    
    $is_duplicate = is_duplicate_field( "id", "piece_rate_wo_dtls", "mst_id='$mst_id' and job_id='$id' and order_id='$po_id' and item_id='$gmts_item_id'" );//
    
    if($is_duplicate==1){
        if($items=='')$items=$gmts_item; else $items.=' and '.$gmts_item;
        $flag=1;
        }
        else
        {
        $flag=0;
        }
    }
    
    if($flag==1){echo $items;}else{echo 0;}

    exit();
}


if($action=="show_price_rate_wo_listview___off")
{


    if($db_type==0)
    {
        $sql = "select a.id,a.company_id,a.service_provider_id,group_concat(b.item_id) as item_id,group_concat(b.buyer_id) as buyer_id,group_concat(b.order_id) as order_id,b.order_source,b.job_id from  piece_rate_wo_mst a,piece_rate_wo_dtls b where a.id=b.mst_id and a.id=$data and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.job_id,b.order_source,a.company_id,a.service_provider_id,a.id"; 
    }
    else
    {
         $sql = "select a.id,a.company_id,a.service_provider_id,LISTAGG(CAST(b.item_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.item_id) as item_id,LISTAGG(CAST(b.buyer_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.buyer_id) as buyer_id,LISTAGG(CAST(b.order_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.order_id) as order_id,b.order_source,b.job_id from  piece_rate_wo_mst a,piece_rate_wo_dtls b where a.id=b.mst_id and a.id=$data and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.job_id,b.order_source,a.company_id,a.service_provider_id,a.id"; 
    }
    
       // echo $sql; 
    $result = sql_select($sql);
    foreach ($result as $row)
    {  
        $poIdArr[$row[csf('order_source')]][]=$row[csf('order_id')];
        //$jobIdArr[$row[csf('order_source')]]=$row[csf('job_id')];
    }


   $sql="select id,job_no_mst,po_number from wo_po_break_down where status_active = 1 and is_deleted = 0 ";
    $p=1;
    
    $po_id_chunk_arr=array_chunk(array_unique(explode(',',implode(',',$poIdArr[1]))),999);
    foreach($po_id_chunk_arr as $jobIdArr)
    {
        if($p==1) $sql .="  and ( id in(".implode(",",$jobIdArr).")"; 
        else  $sql .=" or id in(".implode(",",$jobIdArr).")";
        
        $p++;
    }
    $sql .=")";


    $po_sql_result = sql_select($sql);
    foreach($po_sql_result as $row)
    {
        $job_arr[$row[csf('id')]]=$row[csf('job_no_mst')];
        $po_number_arr[$row[csf('id')]]=$row[csf('po_number')];
        
    }


    ?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="900" class="rpt_table">
        <thead>
            <th width="50">SL</th>
            <th width="100">Job Number</th>
            <th width="120">Company</th>
            <th width="120">Service Provider</th>
            <th width="200">Order No</th>
            <th width="150">Buyer</th>
            <th>Item</th>
        </thead>
        
        
    </table>
    <div style="width:900px; max-height:230px; overflow-y:scroll" id="list_container_batch" align="left">    
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table" id="tbl_list_search">  
        <?
            $i=1;
            foreach ($result as $row)
            {  
             
                if($row[csf('order_source')]==1)
                {
                    $job_arrs=$job_arr; $po_number_arrs=$po_number_arr; $buyer_arrs=$buyer_arr;
                }
                else
                {
                    $job_arrs=$subcon_job_arr; $po_number_arrs=$subcon_po_number_arr;$buyer_arrs=$subcon_buyer_arr;
                }       
              
             
             
              $item_conca='';
              $items=array_unique(explode(",",$row[csf('item_id')]));
              foreach($items as $item_id)
              {
                if($item_conca=='')$item_conca=$garments_item[$item_id]; else $item_conca.=','.$garments_item[$item_id];  
              }
                
              $order_conca='';
              $orders=array_unique(explode(",",$row[csf('order_id')]));
              foreach($orders as $order_id)
              {
                if($order_conca=='')$order_conca=$po_number_arrs[$order_id]; else $order_conca.=','.$po_number_arrs[$order_id]; 
                $job_no=$job_arrs[$order_id];
              }
                
                
              $buyer_conca='';
              $buyers=array_unique(explode(",",$row[csf('buyer_id')]));
              foreach($buyers as $buyer)
              {
                if($buyer_conca=='')$buyer_conca=$buyer_arrs[$buyer]; else $buyer_conca.=','.$buyer_arrs[$buyer];  
              }
                
              $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";    
                
            ?>
                <tr id="tr_<? echo $row[csf('id')]; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="show_list_view('<? echo $row[csf('id')].'_'.$row[csf('job_id')]; ?>', 'populate_price_rat_dtls_form_data', 'details_entry_list_view', 'requires/garments_service_work_order_controller', '');set_button_status(1, '<? echo $_SESSION['page_permission']; ?>', 'fnc_prices_rate_wo',1)" > 
                    <td width="50" align="center"><? echo $i; ?></td>
                    <td width="100" align="center"><? echo $job_no; ?></td>
                    <td width="120" align="center"><? echo $company_arr[$row[csf('company_id')]]; ?></td>
                    <td width="120" align="center"><p><? echo $supplier_arr[$row[csf('service_provider_id')]]; ?></p></td>
                    <td width="200"><p><? echo $order_conca; ?></p></td>
                    <td width="150"><p><? echo $buyer_conca; ?></p></td>
                    <td><p><? echo $item_conca; ?></p></td>
                </tr>
            <?
            $i++;
            }
            ?>
        </table>
    </div>
    <?


    exit();
}


if($action=='populate_price_rat_dtls_form_data')
{
    //list($mst_id,$job_id)=explode('_',$data);

    $sys_number = return_field_value("sys_number","garments_service_wo_mst","id=$data and status_active=1 and is_deleted=0","sys_number");

    $sql_check=sql_select("SELECT a.production_type, a.wo_order_no from pro_garments_production_mst a where a.status_active=1 and a.wo_order_no='$sys_number' and a.production_type in(1,5,8) ");
    $prod_type_wise_wo = array();
    foreach ($sql_check as $val) 
    {
        if($val['PRODUCTION_TYPE']==1)
        {
            $rateFor = 20;
        }
        elseif ($val['PRODUCTION_TYPE']==5) 
        {
            $rateFor = 30;
        }
        elseif ($val['PRODUCTION_TYPE']==8) 
        {
            $rateFor = 40;
        }
        $prod_type_wise_wo[$rateFor] = $val['WO_ORDER_NO'];
    }

   

    /*$sql_check2=sql_select("SELECT a.wo_order_no from inv_issue_master a where a.status_active=1 and a.wo_order_no='$sys_number'");
    $sql_check3=sql_select("SELECT a.wo_order_no from subcon_outbound_bill_dtls a where a.status_active=1 and a.wo_order_no='$sys_number'");

    $button_disabled = 0;

    if(count($sql_check)!=0 || count($sql_check2)!=0 || count($sql_check3)!=0)
    {
        $button_disabled = 1;
    }*/

 
    $sql = "SELECT id, mst_id, order_source, job_id, po_id, buyer_id, item_id, style_ref,color_type,rate_for,wo_qty,uom, avg_rate,amount, remarks,ord_recev_company,client_id,po_qty from garments_service_wo_dtls where mst_id=$data and status_active=1 and is_deleted=0"; 
   // echo $sql;die;
    $i=1;
    $data_array=sql_select($sql);

    $po_ids=array();
    $dtls_ids=array();
    foreach ($data_array as $row) {
        array_push($po_ids, $row[csf('po_id')]);
        array_push($dtls_ids, $row[csf('id')]);
    }

    $po_ids=array_unique($po_ids);
    $po_id_string= implode(",", $po_ids);
    $dtls_ids=array_unique($dtls_ids);
    $dtls_id_string= implode(",", $dtls_ids);

    // $sql="SELECT a.id,a.job_no,a.job_no_prefix_num,a.buyer_name,a.style_ref_no,b.gmts_item_id,c.id as po_id,c.po_number,c.po_quantity,a.client_id,( select sum(l.wo_qty) from garments_service_wo_dtls l where c.id = l.po_id and a.style_ref_no = l.style_ref and b.gmts_item_id=l.item_id and l.status_active=1) as wo_qty from wo_po_details_master a,wo_po_details_mas_set_details b,wo_po_break_down c where a.job_no=b.job_no and a.job_no=c.job_no_mst and c.id in(".$po_id_string.")  and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0";

    $sql="SELECT a.id,a.job_no,a.job_no_prefix_num,a.buyer_name,a.style_ref_no,b.gmts_item_id,c.id as po_id,c.po_number,c.po_quantity,a.client_id,d.wo_qty as wo_qty,d.rate_for from wo_po_details_master a,wo_po_details_mas_set_details b,wo_po_break_down c,garments_service_wo_dtls d where a.id=b.job_id and a.job_no=c.job_no_mst and d.mst_id=$data  and c.id=d.po_id  and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.style_ref_no = d.style_ref and b.gmts_item_id=d.item_id and a.status_active=1 and d.status_active=1"; //and c.id in(".$po_id_string.")
   // echo $sql;die;
    $sql_result = sql_select($sql);
    $po_wise_data=array();
    foreach ($sql_result as $row) 
    {
        $po_wise_data[$row[csf('po_id')]][$row[csf('gmts_item_id')]][$row[csf('rate_for')]]['po_number']=$row[csf('po_number')];
        $po_wise_data[$row[csf('po_id')]][$row[csf('gmts_item_id')]][$row[csf('rate_for')]]['job_no']=$row[csf('job_no')];
        $po_wise_data[$row[csf('po_id')]][$row[csf('gmts_item_id')]][$row[csf('rate_for')]]['wo_qty']=$row[csf('wo_qty')];
        
    }
    // echo "<pre>";print_r($po_wise_data);

    $client_arr=return_library_array("SELECT a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id  group by a.id , a.buyer_name order by buyer_name ",'id','buyer_name');
    
    $sql_check="SELECT wo_dtls_id from piece_rate_bill_dtls where status_active=1 and wo_dtls_id in (".$dtls_id_string.")";
   // echo $sql_check;die;

    $result_check=sql_select($sql_check);
    $yesrow=array();
    foreach ($result_check as $row) {
        $yesrow[$row[csf('wo_dtls_id')]]=$row[csf('wo_dtls_id')];
    }

    // ===================== getting previous rcv qty =============================
    $sql = "SELECT po_id, item_id, rate_for,wo_qty from garments_service_wo_dtls where mst_id!=$data and po_id in($po_id_string) and status_active=1 and is_deleted=0";
    // echo $sql;
    $res = sql_select($sql);
    $prev_rcv_qty_array = array();
    foreach ($res as $val) 
    {
        $prev_rcv_qty_array[$val[csf('po_id')]][$val[csf('item_id')]][$val[csf('rate_for')]] += $val[csf('wo_qty')];
    }
    
    foreach ($data_array as $row)
    { 

        $check=$yesrow[$row[csf('id')]];
        $on=0;
        if(empty($check))
        {
            $disabled='';
        }else{
            $disabled='disabled';
            $on=1;
        }
        $remian=max(($row[csf('po_qty')]-$po_wise_data[$row[csf('po_id')]][$row[csf('item_id')]][$row[csf('rate_for')]]['wo_qty']),$row[csf('wo_qty')]);
        $previous_qty = $prev_rcv_qty_array[$row[csf('po_id')]][$row[csf('item_id')]][$row[csf('rate_for')]];


        if($prod_type_wise_wo[$row[csf('rate_for')]]!="")
        {
            $button_disabled=1;
            $button_disabled2="disabled title='Allready used'";
        }
        else
        {
            $button_disabled=0;
            $button_disabled2="";
        }
        ?>
        

    
           <tr>
          
            <td>
                <?
                    echo create_drop_down( "cboOrdRceveCompId_".$i, 100, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 order by comp.company_name","id,company_name", 1, "--Select Company--",$row[csf('ord_recev_company')], "",1 );
                ?>
            </td>
            <td>
                 <input type="hidden" id="detailsUpdateId_<? echo $i; ?>" name="detailsUpdateId_<? echo $i; ?>" value="<?php echo $row[csf('id')]?>" />
                 <input type="text" name="txtjobno_<? echo $i; ?>" id="txtjobno_<? echo $i; ?>" class="text_boxes" style="width:100px;" placeholder="Double click to search" readonly disabled onDblClick="openmypage_job_no(1);" value="<? echo  $po_wise_data[$row[csf('po_id')]][$row[csf('item_id')]][$row[csf('rate_for')]]['job_no'];?>" />
                 <input type="hidden" name="txtjobid_<? echo $i; ?>" id="txtjobid_<? echo $i; ?>" value="<? echo $row[csf("job_id")];?>" />
            </td>
             <td>
                 <input type="text" name="txtbuyer_<? echo $i; ?>" id="txtbuyer_<? echo $i; ?>" class="text_boxes" style="width:80px;" value="<? echo $buyer_arr[$row[csf("buyer_id")]];?>" readonly disabled />
                 <input type="hidden" name="txtbuyerid_<? echo $i; ?>" id="txtbuyerid_<? echo $i; ?>" value="<? echo $row[csf("buyer_id")];?>" />
            </td>
             <td>
                 <input type="text" name="client_<? echo $i; ?>" id="client_<? echo $i; ?>" class="text_boxes" style="width:80px;" value="<?php echo $client_arr[$row[csf('client_id')]]; ?>" readonly disabled />
                 <input type="hidden" name="clientid_<? echo $i; ?>" id="clientid_<? echo $i; ?>" value="<?php echo $row[csf('client_id')];?>" />
            </td>
             <td>
                 <input type="text" name="txtstyle_<? echo $i; ?>" id="txtstyle_<? echo $i; ?>" class="text_boxes" style="width:80px;" value="<? echo $row[csf("style_ref")];?>" disabled readonly />
            </td>

            <td>
                 <input type="text" name="txtitem_<? echo $i; ?>" id="txtitem_<? echo $i; ?>" class="text_boxes" style="width:80px;" value="<? echo $garments_item[$row[csf("item_id")]];?>" readonly disabled />
                 <input type="hidden" name="txtitemid_<? echo $i; ?>" id="txtitemid_<? echo $i; ?>" value="<? echo $row[csf("item_id")];?>" />
            </td>
            
            <td>
                
                  <input type="hidden" name="poid_<? echo $i; ?>" id="poid_<? echo $i; ?>" value="<? echo $row[csf('po_id')];?>" />

                 <input type="text" name="po_<? echo $i; ?>" id="po_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $po_wise_data[$row[csf('po_id')]][$row[csf('item_id')]][$row[csf('rate_for')]]['po_number'];?>" disabled style="width:80px;" readonly>
            </td>

           <td>
                <input type="text" name="poqty_<? echo $i; ?>" id="poqty_<? echo $i; ?>" class="text_boxes_numeric" style="width:100px;"  value="<? echo number_format($row[csf("po_qty")],2,'.','');?>" disabled  readonly />
           
            
            </td>
           
          
           
            <td>
                <? 
                echo create_drop_down( "colortype_".$i, 90, $color_type,"",1, "--Select--", $row[csf('color_type')],"",$on,"" ); 
                ?>                                    
            </td>
            <td>
                <? 
                echo create_drop_down( "cboratefor_".$i, 90, $rate_for,"",1, "--Select--", $row[csf('rate_for')],"add_particular_rate(this.value,this.id)",$on,"20,30,40" ); 
                ?>                                    
            </td>
            <td>
                <input type="text" name="txtwoqty_<? echo $i; ?>" id="txtwoqty_<? echo $i; ?>" class="text_boxes_numeric" style="width:100px;"  value="<? echo number_format($row[csf("wo_qty")],2,'.','');?>" onKeyUp="calculate()" <?php echo $button_disabled2; ?> />
                <input type="hidden" name="original_<? echo $i; ?>" id="original_<? echo $i; ?>" value="<?php echo number_format($remian,2,'.',''); ?>">
                <input type="hidden" name="previous_<? echo $i; ?>" id="previous_<? echo $i; ?>" value="<? echo number_format($previous_qty,2,'.','');?>">
                <input type="hidden" name="original_rate_<? echo $i; ?>" id="original_rate_<? echo $i; ?>" value="<? echo number_format($row[csf("avg_rate")],2, '.', '');?>">
            
            </td>
            <td>
                <? 
                echo create_drop_down( "cbodtlsuom_".$i, 80, $unit_of_measurement,"",1, "--Select--", $row[csf('uom')],"",$on,"1,2,58" ); 
                ?>                                    
            </td>
            <td>
                 <input type="text" name="txtavgrate_<? echo $i; ?>" id="txtavgrate_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px;" value="<? echo number_format($row[csf("avg_rate")],2,'.','');?>" onKeyUp="calculate()" <?php echo $disabled; ?> />
            </td>
            
            <td>
                 <input type="text" name="txtdtlamount_<? echo $i; ?>" id="txtdtlamount_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px;" value="<?php echo number_format($row[csf('amount')],2,'.','');?>"  <?php echo $disabled; ?> />
            </td>
            
            <td>
                 <input type="text" name="txtremarks_<? echo $i; ?>" id="txtremarks_<? echo $i; ?>" class="text_boxes" style="width:80px;" value="<?php echo $row[csf('remarks')];?>" <?php echo $disabled; ?>  />
            </td>
            
            <td align="center">
                
                <input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:27px" class="formbuttonplasminus" value="+" onClick="fn_addRow(<? echo $i; ?>,0)" />
               
                   
                         <input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:27px" class="formbuttonplasminus <?php echo empty($check)? '': 'formbutton_disabled'; ?>" value="-" onClick="fn_deleteRow(<? echo $i; ?>,<?=$button_disabled;?>);" />
                       
            </td>
            
            
        </tr> 


        <?
        $i++; 
    }
    
    
    //echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_prices_rate_wo',1);\n"; 
        
    exit(); 
}


if($action=='populate_price_rat_mst_form_data')
{
    
    
    $sql = "SELECT id,sys_number, company_id,cbo_source as source, working_company_id,pay_mode, wo_date, attension, currency, exchange_rate,location, remarks from garments_service_wo_mst where id=$data and status_active=1 and is_deleted=0"; 
    
    $data_array=sql_select($sql);
    foreach ($data_array as $row)
    { 
        echo "document.getElementById('update_id').value                    = '".$row[csf("id")]."';\n";
        echo "document.getElementById('txt_system_id').value                = '".$row[csf("sys_number")]."';\n";
        echo "document.getElementById('cbo_company_id').value               = '".$row[csf("company_id")]."';\n";
        echo "document.getElementById('cbo_source').value                 = '".$row[csf("source")]."';\n";
        // echo "document.getElementById('cbo_rate_for').value                 = '".$row[csf("rate_for")]."';\n";
        echo "document.getElementById('txt_attention').value                = '".$row[csf("attension")]."';\n";
        echo "document.getElementById('cbo_currency').value                 = '".$row[csf("currency")]."';\n";
        echo "document.getElementById('txt_exchange_rate').value            = '".$row[csf("exchange_rate")]."';\n";
        echo "document.getElementById('txt_remarks_mst').value              = '".$row[csf("remarks")]."';\n";
        echo "document.getElementById('cbo_location').value                 = '".$row[csf("location")]."';\n";

        echo "load_drop_down( 'requires/garments_service_work_order_controller', document.getElementById('cbo_source').value+'**'+document.getElementById('cbo_company_id').value, 'load_drop_down_working_company', 'working_company_td' );\n"; 
       
        echo "document.getElementById('cbo_pay_mode').value                 = '".$row[csf("pay_mode")]."';\n";
        echo "document.getElementById('txt_wo_date').value                  = '".change_date_format($row[csf("wo_date")])."';\n";
        echo "document.getElementById('cbo_working_company').value      = '".$row[csf("working_company_id")]."';\n";

        echo "$('#cbo_company_id').attr('disabled','disabled');\n";
        /*      echo "$('#cbo_rate_for').attr('disabled','disabled');\n";
                echo "$('#txt_wo_date').attr('disabled','disabled');\n";
                echo "$('#cbo_currency').attr('disabled','disabled');\n";
                echo "$('#txt_exchange_rate').attr('disabled','disabled');\n";
                echo "$('#cbo_location').attr('disabled','disabled');\n";
        */      
        exit();
    }
}


if($action=="price_rate_wo_print")
{
    extract($_REQUEST);

    $company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");

    $sql = "SELECT working_company_id,sys_number,wo_date,company_id,attension,currency,pay_mode,cbo_source as source from garments_service_wo_mst where id='$data' and status_active=1 and is_deleted=0"; 
    //echo $sql;
    $data_array=sql_select($sql);
    $company_id=$data_array[0][csf("company_id")];
    $attension=$data_array[0][csf("attension")];
    $sys_number=$data_array[0][csf("sys_number")];
    $currency=$data_array[0][csf("currency")];
    $pay_mode=$data_array[0][csf("pay_mode")];
    $source=$data_array[0][csf("source")];
    $comp_info=sql_select("SELECT a.*,b.country_name from lib_company a,lib_country b where a.country_id=b.id and a.id='$company_id'");
     

    $data_arr=sql_select("SELECT a.id,a.supplier_name FROM lib_supplier a,lib_supplier_party_type b,lib_supplier_tag_company c WHERE a.id=b.supplier_id and a.id=c.supplier_id and b.party_type in(22,36) and c.tag_company =$company_id");
        foreach ($data_arr as $row)
        { 
           $sp_arr[$row[csf("id")]]=$row[csf("supplier_name")];
        }

    $image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$company_id'","image_location");
    ?>
    
    <table cellspacing="0" cellpadding="0" border="1" rules="all" >
        <tr>      
            <td  align="left" colspan="3">
                <img  src='../../<? echo $image_location; ?>' height='70' width='180' />
            </td>   
            <td colspan="10" align="center"><b style="font-size:36px; font-weight:bold;">
                <? echo $company_library[$data_array[0][csf("company_id")]];//$comp_info[0][csf("company_name")]; ?></b><br>
                <? echo $comp_info[0][csf("plot_no")];?>,
                <? echo $comp_info[0][csf("level_no")];?>,
                <? echo $comp_info[0][csf("road_no")];?>,
                <? echo $comp_info[0][csf("block_no")];?>,
                <? echo $comp_info[0][csf("city")];?>,
                <? echo $comp_info[0][csf("zip_code")];?>,
                <? echo $comp_info[0][csf("province")];?>,
                <? echo $comp_info[0][csf("country_name")];?><br>
                <? echo $comp_info[0][csf("email")];?>,
                <? echo $comp_info[0][csf("website")];?><br> 
                <? if($comp_info[0][csf("bin_no")]!='') echo "<br> BIN: ".$comp_info[0][csf("bin_no")]; ?>               
            </td>            
       </tr> 
       <tr>
           <td colspan="12" align="center">
               Garments Service Work Order
           </td>
       </tr>
       <tr>
            <td colspan="12" align="center">Work Order No.: <b><? echo $data_array[0][csf("sys_number")];?></b></td>
       </tr>
       <tr>
            <td colspan="6"><b>Work Order To :</b> <? echo $source==1 ? $company_library[$data_array[0][csf("working_company_id")]]: $sp_arr[$data_array[0][csf("working_company_id")]]; ?></td>
            <td colspan="7">Attention : <? echo $attension;?></td>
       </tr> 
    </table>
    <br>
    <table cellspacing="5" cellpadding="5" border="1" rules="all" >  
        <tr>
            <th width="35">SL</th>
            <th >Buyer</th>
            <th >Style</th>
            <th >Job No</th>
            <th >Order No</th>
            <th >Gmt.Item</th>
            <th >Rate For</th>           
            <th >WO Qty</th>
            <th >UOM</th>
            <th >Rate</th>
            <th>Amount</th>
            <th>Remark</th>
        </tr>
        <?


        //$sql = "select id,order_source, job_id, order_id, buyer_id, item_id, color_type, wo_qty,uom, avg_rate,amount from  piece_rate_wo_dtls where mst_id='$data' and status_active=1 and is_deleted=0"; 
        
        $sql = "SELECT a.id,a.job_id, a.po_id,a.buyer_id,a.item_id,a.color_type,a.rate_for,a.wo_qty,a.uom,a.avg_rate,
                   a.amount, a.po_qty,a.style_ref,a.remarks
              from garments_service_wo_dtls a
             where  a.mst_id = $data and a.status_active = 1 and a.is_deleted = 0 
             "; 
        // echo $sql;     
        $data_array=sql_select($sql);
        $po_id_arr[1][0]=0;$po_id_arr[2][0]=0;
        foreach ($data_array as $row)
        { 
            $po_id_arr[$row[csf("po_id")]]=$row[csf("po_id")];
            $po_id_string.=$row[csf('po_id')].",";
        }
        
         $po_id_string=chop( $po_id_string,",");
        
        $order_sql="SELECT id, po_number,job_no_mst, 1 as order_source from wo_po_break_down where id in(".implode(',',$po_id_arr[1]).") and status_active=1 and is_deleted=0";
        $order_sql_result_arr=sql_select($order_sql);
        foreach ($order_sql_result_arr as $row)
        { 
            $jobOrderdataArr['po'][$row[csf('id')]]=$row[csf('po_number')];
            $jobOrderdataArr['job'][$row[csf('id')]]=$row[csf('job_no_mst')];
        }
        
        $sql="SELECT a.id,a.job_no,a.job_no_prefix_num,a.buyer_name,a.style_ref_no,b.gmts_item_id,c.id as po_id,c.po_number,c.po_quantity,a.client_id from wo_po_details_master a,wo_po_details_mas_set_details b,wo_po_break_down c where a.job_no=b.job_no and a.job_no=c.job_no_mst and c.id in(".$po_id_string.")  and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
        // echo $sql;die;
        $sql_result = sql_select($sql);
        $po_wise_data=array();
        foreach ($sql_result as $row) 
        {
            $po_wise_data[$row[csf('po_id')]]['po_number']=$row[csf('po_number')];
            $po_wise_data[$row[csf('po_id')]]['job_no']=$row[csf('job_no')];
            
        }         
        
        $sl=1;
        foreach ($data_array as $row)
        {        
            ?>
            <tr>
                <td align="center"><? echo $sl;?></td>
                <td><? echo $buyer_arr[$row[csf("buyer_id")]];?></td>
                <td><? echo $row[csf("style_ref")];?></td>
                <td><? echo $po_wise_data[$row[csf('po_id')]]['job_no'];?></td>
                <td><? echo $po_wise_data[$row[csf('po_id')]]['po_number'];?></td>
                <td><? echo $garments_item[$row[csf("item_id")]];?></td>
                <td><? echo $rate_for[$row[csf("rate_for")]];?></td>
                <td align="right"><? echo number_format($row[csf("wo_qty")],2); $tot_wo_qty+=$row[csf("wo_qty")];?></td>
                <td align="center"><? echo $unit_of_measurement[$row[csf("uom")]];?></td>
                <td align="right"><? echo number_format($row[csf("avg_rate")],2);?></td>
                <td align="right"><? echo number_format($row[csf("amount")],2); $tot_amount+=$row[csf("amount")];?></td>
                <td align="left"><? echo $row[csf("remarks")];?></td>
            </tr>
            <? 
            $sl++;  
        }
        ?>
        <tr>
            <th colspan="7" align="right">Total : </th>
            <th align="right"><? echo number_format($tot_wo_qty,2);?></th>
            <th></th>
            <th></th>
            <th align="right"><? echo number_format($tot_amount,2);?></th>
        </tr>        
    </table>
    <table  width="700">
        <tr>
            <td >In Words: <?
                $cur=$currency[$currency];
                if($currency==1){ $paysa_sent="Paisa"; } else if($currency==2){ $paysa_sent="CENTS"; }
              echo number_to_words(number_format($tot_amount,2,'.',''),$cur,$paysa_sent); 
             ?></td>
        </tr>
    </table>

    <table width="700">
        <?php echo get_spacial_instruction($sys_number,600); ?>
       <? echo signature_table(248, $company_id, "700px"); ?>
    </table>
    <br>
    <div style=" width:700px;">
            
    </div>
    <?
    exit();
}


if ($action=="save_update_delete")
{
    $process = array( &$_POST );
    //print_r($process);die;
    extract(check_magic_quote_gpc( $process )); 
    
    if ($operation==0)  // Insert Here
    { 
        $con = connect();
        if($db_type==0)
        {
            mysql_query("BEGIN");
        }
        
        
        $flag=1;
        if(str_replace("'","",$update_id)=="")
        {
            if($db_type==0) $year_cond="YEAR(insert_date)"; 
            else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
            else $year_cond="";//defined Later
            
            $id = return_next_id_by_sequence("garments_service_wo_mst_seq", "garments_service_wo_mst", $con);

        
            
            // master part--------------------------------------------------------------;
            $price_rate_wo_system_id=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'GSWO', date("Y",time()), 5, "select sys_number_prefix, sys_number_prefix_num from garments_service_wo_mst where company_id=$cbo_company_id and status_active=1 and $year_cond=".date('Y',time())." order by id desc", "sys_number_prefix", "sys_number_prefix_num" ));
            
            
            $field_array_mst="id,sys_number_prefix,sys_number_prefix_num,sys_number,company_id,working_company_id,pay_mode,cbo_source,wo_date,attension,currency,exchange_rate,location,remarks,inserted_by,insert_date,status_active,is_deleted";
            
            $data_array_mst="(".$id.",'".$price_rate_wo_system_id[1]."',".$price_rate_wo_system_id[2].",'".$price_rate_wo_system_id[0]."',".$cbo_company_id.",".$cbo_working_company.",".$cbo_pay_mode.",".$cbo_source.",".$txt_wo_date.",'".str_replace("'","", $txt_attention)."',".$cbo_currency.",".$txt_exchange_rate.",".$cbo_location.",'".str_replace("'","", $txt_remarks_mst)."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0')";
            
            // details part--------------------------------------------------------------;

            $field_array_dtls="id, mst_id,ord_recev_company, job_id, po_id, buyer_id,client_id, item_id, style_ref,color_type,rate_for, wo_qty,po_qty,uom, avg_rate,amount, remarks, inserted_by, insert_date,status_active,is_deleted";
            
            $id_dtls = return_next_id_by_sequence("garments_service_wo_dtls_seq", "garments_service_wo_dtls", $con); 
            
            
            $tot_rows= str_replace("'","",$tot_rows);
            
            for($i=1; $i<=$tot_rows; $i++)
            {
                
                $cbo_ord_rceve_comp_id='cboOrdRceveCompId_'.$i;
                $txtjobid='txtjobid_'.$i;
                $txtpoid='poid_'.$i;
                
                $txtbuyerid='txtbuyerid_'.$i;
                $txtitemid='txtitemid_'.$i;
                $txtstyle='txtstyle_'.$i;
                $colortype='colortype_'.$i;
                $ratefor='cboratefor_'.$i;
                $client_id='clientid_'.$i;
                
                $txtwoqty='txtwoqty_'.$i;
                $txtpoqty='poqty_'.$i;
                $txtavgrate='txtavgrate_'.$i;
                $txtremarks='txtremarks_'.$i;
                
                $cbodtlsuom='cbodtlsuom_'.$i;
                $txtdtlamount='txtdtlamount_'.$i;

                // ============= chk currency
                $cbo_currency = str_replace("'", "", $cbo_currency);
                $txt_exchange_rate = str_replace("'", "", $txt_exchange_rate);
                if($cbo_currency !=1) // 1 for taka
                {
                    $txtavgrate = $txtavgrate/$txt_exchange_rate;   
                    $txtdtlamount =  number_format(($txtwoqty * $txtavgrate),2);
                }
    
                if(str_replace("'",'',$$txtwoqty)!="")
                {
                    if($i>1)
                    {
                        $data_array_dtls.=",";
                    }
                    $data_array_dtls.="(".$id_dtls.",".$id.",".$$cbo_ord_rceve_comp_id.",".$$txtjobid.",".$$txtpoid.",".$$txtbuyerid.",".$$client_id.",".$$txtitemid.",".$$txtstyle.",".$$colortype.",".$$ratefor.",".$$txtwoqty.",".$$txtpoqty.",".$$cbodtlsuom.",".$$txtavgrate.",".$$txtdtlamount.",".$$txtremarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0')";
                    $id_dtls++;
                }
            }


        }
        
           // echo "10**".$data_array_dtls; die;

        $rID1=sql_insert("garments_service_wo_mst",$field_array_mst,$data_array_mst,0);
        

        $rID2=sql_insert("garments_service_wo_dtls",$field_array_dtls,$data_array_dtls,0);
        
        //echo "10**insert into dyeing_work_order_mst (".$field_array.") values ".$data_array;;

       
        
        
        // echo "10** ".$rID2."**".$rID3;print_r($data_array_wo_dtls); die;
        
        if($db_type==0)
        {
            if($rID1 && $rID2)
            {
                mysql_query("COMMIT");  
                echo "0**".$id."**".$price_rate_wo_system_id[0]."**0";
            }
            else
            {
                mysql_query("ROLLBACK"); 
                //echo "10**".$rID1."**".$rID2;
                 echo "10** insert into garments_service_wo_dtls($field_array_dtls)values".$data_array_dtls;die;
            }
        }
        else if($db_type==2 || $db_type==1 )
        {
            if($rID1 && $rID2)
            {
                oci_commit($con);  
                echo "0**".$id."**".$price_rate_wo_system_id[0]."**0";
            }
            else
            {
                oci_rollback($con);
                //echo "10**".$rID1."**".$rID2;
                 echo "10** insert into garments_service_wo_dtls($field_array_dtls)values".$data_array_dtls;die;
            }
        }
        
                
        disconnect($con);
        die;
    }
    
    else if ($operation==1)   // Update Here
    { 
        $con = connect();
        if($db_type==0)
        {
            mysql_query("BEGIN");
        }
        
        $flag=1;

        $field_array_mst="company_id*working_company_id*pay_mode*cbo_source*wo_date*attension*currency*exchange_rate*location*remarks*updated_by*update_date";
        $data_array_mst="".$cbo_company_id."*".$cbo_working_company."*".$cbo_pay_mode."*".$cbo_source."*".$txt_wo_date."*".$txt_attention."*".$cbo_currency."*".$txt_exchange_rate."*".$cbo_location."*".$txt_remarks_mst."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
        
        //-----------------------------------------------------         
        $field_array_dtls_up="ord_recev_company*job_id*po_id*buyer_id*item_id*style_ref*color_type*rate_for*wo_qty*uom*avg_rate*amount*remarks*updated_by*update_date*client_id*po_qty";
        $field_array_dtls="id, mst_id, ord_recev_company,job_id, po_id, buyer_id, item_id, style_ref,color_type,rate_for,wo_qty, uom, avg_rate, amount, remarks,client_id,po_qty, inserted_by, insert_date, status_active, is_deleted";
         
        
        $tot_rows=str_replace("'","",$tot_rows);
        $f=1;$df=1;

        for($i=1; $i<=$tot_rows; $i++)
        {

            
            $cbo_ord_rceve_comp_id='cboOrdRceveCompId_'.$i;
            $txtjobid='txtjobid_'.$i;
            $txtpoid='poid_'.$i;
            
            $txtbuyerid='txtbuyerid_'.$i;
            $txtitemid='txtitemid_'.$i;
            $txtstyle='txtstyle_'.$i;
            $colortype='colortype_'.$i;
            $ratefor='cboratefor_'.$i;
            $txtwoqty='txtwoqty_'.$i;
            $txtavgrate='txtavgrate_'.$i;
            $txtremarks='txtremarks_'.$i;
            
            $cbodtlsuom='cbodtlsuom_'.$i;
            $txtdtlamount='txtdtlamount_'.$i;
            $details_update_id='detailsUpdateId_'.$i;           
           
            $client_id='clientid_'.$i;
            $txtpoqty='poqty_'.$i;

            $cbo_order_source=str_replace("'",'',$$cbo_order_source);
            $cbo_ord_rceve_comp_id=str_replace("'",'',$$cbo_ord_rceve_comp_id);
            $txtjobid=str_replace("'","",$$txtjobid);
            $txtpoid=str_replace("'","",$$txtpoid);
            $txtbuyerid=str_replace("'","",$$txtbuyerid);
            $txtitemid=str_replace("'","",$$txtitemid);
            $txtstyle=str_replace("'","",$$txtstyle);
            $colortype=str_replace("'","",$$colortype);
            $ratefor=str_replace("'","",$$ratefor);
            $txtwoqty=str_replace("'","",$$txtwoqty);
            $txtavgrate=str_replace("'","",$$txtavgrate);
            $txtremarks=str_replace("'","",$$txtremarks);
            $cbodtlsuom=str_replace("'","",$$cbodtlsuom);
            $txtdtlamount=str_replace("'","",$$txtdtlamount);
            $client_id=str_replace("'","",$$client_id);
            $txtpoqty=str_replace("'","",$$txtpoqty);

            // ============= chk currency
            $cbo_currency = str_replace("'", "", $cbo_currency);
            $txt_exchange_rate = str_replace("'", "", $txt_exchange_rate);
            if($cbo_currency !=1) // 1 for taka
            {  
                // echo $txtavgrate."/".$txt_exchange_rate."<br>";  die(); 
                $txtavgrate = $txtavgrate/$txt_exchange_rate; 
                $txtdtlamount =  number_format(($txtwoqty * $txtavgrate),2);
            }
            
    
            if(str_replace("'","",$$details_update_id)!="")
            {
                 //this is for update dels
                $all_dtls_id[]=str_replace("'","",$$details_update_id);
                $update_dtls_id[]=str_replace("'","",$$details_update_id);
                $data_array_dtls_up[str_replace("'","",$$details_update_id)] =explode("*",("'".$cbo_ord_rceve_comp_id."'*'".$txtjobid."'*'".$txtpoid."'*'".$txtbuyerid."'*'".$txtitemid."'*'".$txtstyle."'*'".$colortype."'*'".$ratefor."'*'".$txtwoqty."'*'".$cbodtlsuom."'*'".$txtavgrate."'*'".$txtdtlamount."'*'".$txtremarks."'*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'*'".$client_id."'*'".$txtpoqty."'"));
            }
            else
            {
               //this is for news insert dels   
                if($txtwoqty!="")
                { 
                    if($data_array_dtls!='')
                    {
                        $data_array_dtls.=",";
                    }


                    $id_dtls = return_next_id_by_sequence("garments_service_wo_dtls_seq", "garments_service_wo_dtls", $con);

                    $all_dtls_id[]=$id_dtls;
                    $data_array_dtls.="('".$id_dtls."',".$update_id.",'".$cbo_ord_rceve_comp_id."','".$txtjobid."','".$txtpoid."','".$txtbuyerid."','".$txtitemid."','".$txtstyle."','".$colortype."','".$ratefor."','".$txtwoqty."','".$cbodtlsuom."','".$txtavgrate."','".$txtdtlamount."','".$txtremarks."','".$client_id."','".$txtpoqty."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0')";
                        
                    $f++;
                }
            }

        }
            
        
            
        $rID1=sql_update("garments_service_wo_mst",$field_array_mst,$data_array_mst,"id","".$update_id."",1);
        
        // echo "10**".bulk_update_sql_statement("garments_service_wo_dtls", "id",$field_array_dtls_up,$data_array_dtls_up,$update_dtls_id );die();
        
        $rID2=execute_query(bulk_update_sql_statement("garments_service_wo_dtls", "id",$field_array_dtls_up,$data_array_dtls_up,$update_dtls_id ));

        $rID2_insert=true;
        if($data_array_dtls!='')
        {
            $rID2_insert=sql_insert("garments_service_wo_dtls",$field_array_dtls,$data_array_dtls,0);
        }
        // echo "10**insert into garments_service_wo_dtls ($field_array_dtls) values $data_array_dtls";die();
        
        $delete1 = execute_query("update garments_service_wo_dtls set is_deleted=1,status_active=0,updated_by=".$_SESSION['logic_erp']['user_id'].",update_date='".$pc_date_time."' where mst_id=$update_id and id not in(".implode(',',$all_dtls_id).")", 0);
        
        // echo "10**$rID1 && $rID2 && $rID2_insert && $delete1";die();
        
        if($db_type==0)
        {
            if($rID1 && $rID2 && $rID2_insert && $delete1)
            {
                mysql_query("COMMIT");  
                echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_system_id)."**0";
            }
            else
            {
                mysql_query("ROLLBACK"); 
                echo "10**".$rID1."**".$rID2 ."**". $rID2_insert ."**". $delete1;
            }
        }
        else if($db_type==2 || $db_type==1 )
        {
            if($rID1 && $rID2 && $rID2_insert && $delete1)
            {
                oci_commit($con);  
                echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_system_id)."**0";
            }
            else
            {
                oci_rollback($con);
                echo "10**".$rID1."**".$rID2 ."**". $rID2_insert ."**". $delete1;
            }
        }
        disconnect($con);
        die;
        
        
    
    }
    else if ($operation==2)   // Delete Here
    {
        $con = connect();
        if($db_type==0)
        {
            mysql_query("BEGIN");
        }

        $sql_check=sql_select("SELECT a.wo_order_no from pro_garments_production_mst a where a.status_active=1 and a.wo_order_no=$txt_system_id");
        $sql_check2=sql_select("SELECT a.wo_order_no from inv_issue_master a where a.status_active=1 and a.wo_order_no=$txt_system_id");
        $sql_check3=sql_select("SELECT a.wo_order_no from subcon_outbound_bill_dtls a where a.status_active=1 and a.wo_order_no=$txt_system_id");
        // echo "10**".count($sql_check)."*".count($sql_check2)."*".count($sql_check3);die();
        if( count($sql_check)==0 && count($sql_check2)==0 && count($sql_check3)==0)
        {           

            $field_array="updated_by*update_date*status_active*is_deleted";
            $data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
            $delete1=sql_delete("garments_service_wo_mst",$field_array,$data_array,"id","".$update_id."",0);
            $delete2=sql_delete("garments_service_wo_dtls",$field_array,$data_array,"mst_id","".$update_id."",0);

        }
        else
        {
           
            echo "111**1" ;
            disconnect($con);
            die;
        }
       
        // echo "10";die();

        if($db_type==0)
        {
            if($delete1 && $delete2 )
            {
                mysql_query("COMMIT");  
                echo "2**0";
            }
            else
            {
                mysql_query("ROLLBACK"); 
                echo "10**".$delete1."**".$delete2 ;
            }
        }
        else if($db_type==2 || $db_type==1 )
        {
            if($delete1 && $delete2)
            {
                oci_commit($con);  
                echo "2**0";
            }
            else
            {
                oci_rollback($con);
                 echo "10**".$delete1."**".$delete2 ;
            }
        }
        disconnect($con);
        die;

    }
    
}
?>