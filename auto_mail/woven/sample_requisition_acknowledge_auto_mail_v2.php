<?php
date_default_timezone_set("Asia/Dhaka");

include('../../includes/common.php');
include('../../mailer/class.phpmailer.php');
include('../setting/mail_setting.php');

extract($_REQUEST);

	//localhost/platform-v3.5/auto_mail/woven/sample_requisition_acknowledge_auto_mail.php?req_id=1678&approval_type=1


    if($action=="sample_requisition_print") 
    {
        ob_start();
        extract($_REQUEST);
        $data=explode('*',$data);
        $cbo_template_id=$data[3];
        $path="../../";
        if(count($data)>3)
        {
            if($data[4]=='../')
            {
                $path=$data[4];
            }
        }
        if($data[2]==0)  $path='../';
         // echo $data[2].'DTTTTTTTTTTTTTTTT';

         $data[0]=$company_id;


        $company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
        $supplier_library=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
    
        $company_img=sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
    
    
        $buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
        $dealing_merchant_library=return_library_array( "select id,team_member_name from lib_mkt_team_member_info", "id", "team_member_name"  );
        $team_leader_arr=return_library_array( "select id,team_leader_name from lib_marketing_team", "id", "team_leader_name"  );
    
        $sample_library=return_library_array( "select id, sample_name from lib_sample", "id", "sample_name");
        $size_library=return_library_array( "select id, size_name from lib_size", "id", "size_name"  );
        $color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
        $season_arr=return_library_array( "select id,season_name from lib_buyer_season  where status_active =1 and is_deleted=0 ", "id", "season_name"  );
        $trims_group_lib=return_library_array( "select id,item_name from lib_item_group  where status_active =1 and is_deleted=0", "id", "item_name");
        //concate(buyer_name,'_',contact_person)
        $appDate=return_field_value("approved_date","approval_history","entry_form=25 and mst_id in($req_id) order by id desc");
        $appBy=return_field_value("approved_by","approval_history","entry_form=25 and mst_id in($req_id)");
        $user_arr=return_library_array( "select id, user_name from user_passwd", "id", "user_name"  );
        $imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='sample_details_1' and file_type=1",'master_tble_id','image_location');
    

     ?>
         <style>
            #mstDiv {
                margin:0px auto;
                width:1130px;
    
            }
            #mstDiv @media print {
    
            thead {display: table-header-group;}
    
            }
    
             @media print{
                html>body table.rpt_table {
                margin-left:12px;
                  }
    
             }
        </style>
    
            <div id="mstDiv">
    
             <table width="1100" cellspacing="0" border="0"  style="font-family: Arial Narrow;margin-left: 20px;" >
             <tr>
                 <td rowspan="4" valign="top" width="300"></td>
                 <td colspan="4" style="font-size: 24px;"><strong><b><? echo $company_library[$data[0]]; ?></b></strong></td>
                    <td width="200">
                    <?
    
                     $nameArray_approved=sql_select( "SELECT approved_by,approved_date from wo_non_ord_samp_booking_mst a, approval_history b where a.id=b.mst_id and a.booking_no='$data[2]' and b.entry_form=9 and a.status_active =1 and a.is_deleted=0 order by b.id desc ");
                     $approved_by= $user_arr[$nameArray_approved[0][csf("approved_by")]];
                     $approved_date= change_date_format($nameArray_approved[0][csf("approved_date")]);
                      ?>
                     </td>
             </tr>
    
    
    
    
                <tr>
                    <td colspan="5">
                        <?
    
                            $val=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
                            //echo ($val[0][csf('plot_no')])?   $val[0][csf('plot_no')].',': "";
                            echo ($val[0][csf('level_no')])?  $val[0][csf('level_no')].',': "";
                            echo ($val[0][csf('road_no')])?   $val[0][csf('road_no')].',': "";
                            echo ($val[0][csf('block_no')])?  $val[0][csf('block_no')].',': "";
                            echo ($val[0][csf('city')])?      $val[0][csf('city')].',': "";
                            echo ($val[0][csf('zip_code')])?  $val[0][csf('zip_code')].',': "";
                            echo ($val[0][csf('province')])?  $val[0][csf('province')].',': "";
                            echo($val[0][csf('country_id')])? $country_arr[$val[0][csf('country_id')]]: "";
                            echo ($val[0][csf('email')])?    "</br>". $val[0][csf('email')].',': "</br>";
                            echo($val[0][csf('website')])?    $val[0][csf('website')]: "";
                              $sql="SELECT id, requisition_number, requisition_number_prefix_num, style_ref_no, buyer_name, season, product_dept, dealing_marchant, agent_name, buyer_ref, bh_merchant, estimated_shipdate, team_leader, season_buyer_wise, remarks, quotation_id, sample_stage_id, requisition_date, material_delivery_date from sample_development_mst where  id in($req_id) and entry_form_id=203 and  is_deleted=0  and status_active=1";
                               $dataArray=sql_select($sql);
                               $barcode_no=$dataArray[0][csf('requisition_number')];
                               if($dataArray[0][csf("sample_stage_id")]==1)
                               {
                                    $job_lib=return_library_array( "SELECT a.id,min(b.shipment_date) as shipment_date  from wo_po_details_master  a,wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and a.company_name='$data[0]' GROUP BY a.id", "id", "shipment_date"  );
                               }
    
                                $sqls="SELECT style_desc,supplier_id,revised_no,buyer_req_no,source,team_leader,dealing_marchant,pay_mode,booking_date  from wo_non_ord_samp_booking_mst where  booking_no='$data[2]'  and  is_deleted=0  and status_active=1";
                               //echo $sqls;
                               $dataArray_book=sql_select($sqls);
                            // $style_desc= $dataArray_book[0][csf('style_desc')];
    
    
                        ?>
                    </td>
    
                </tr>
                <tr>
                    <td colspan="3" style="font-size:medium"><strong> <b>Sample Program Without Order</b></strong></td>
                     <td colspan="2" id="" width="250"><b>Approved By :<? echo $approved_by ?></b> </br><b>Approved Date :<? echo $approved_date ?></b> </td>
    
                </tr>
    
    
                </table>
    
                <table width="1100" cellspacing="0" border="0" class="rpt_table" style="font-family: Arial Narrow;margin-left: 20px;" >
                    <tr>
                        <td colspan="4" align="left"><strong>System No. &nbsp;<? echo $dataArray[0][csf("requisition_number")]; ?> </strong></td>
                        <td ><strong>Revise:</strong></td>
                        <td ><? echo $dataArray_book[0][csf('revised_no')];?></td>
                        <td colspan="2"></td>
                    </tr>
    
                    <tr>
                    <td width="100"><strong>Booking No: </strong></td>
                        <td width="130" align="left"><? echo $data[2];?></td>
                        <td width="120"  align="left">&nbsp;&nbsp;<strong>Style Ref:</strong></td>
                        <td width="110">&nbsp;<? echo $dataArray[0][csf('style_ref_no')];?></td>
                        <td width="110"   align="left"><strong>Sample Sub Date:</strong></td>
                        <td width="100" ><? echo change_date_format($dataArray[0][csf('material_delivery_date')]);?></td>
                        <td width="110"   align="left"><strong>Style Desc:</strong></td>
                        <td   ><? echo $dataArray_book[0][csf('style_desc')];?></td>
    
    
                    </tr>
                    <tr>
                        <td width="100"><strong>Buyer Name: </strong></td>
                        <td width="130" align="left"><? echo $buyer_library[$dataArray[0][csf('buyer_name')]];?></td>
                        <td width="120" style="word-break:break-all;" align="left">&nbsp;&nbsp;<strong>Season:</strong></td>
                        <td width="110">&nbsp;<? echo $season_arr[$dataArray[0][csf('season')]];?></td>
                        <td width="110"><strong>BH Merchandiser:</strong></td>
                        <td width="100"><? echo $dataArray[0][csf('bh_merchant')];?></td>
                        <td width="110"><strong>Remarks/Desc:</strong></td>
                        <td   style="word-wrap: break-word;word-break: break-all;" ><? echo $dataArray[0][csf('remarks')];?></td>
    
                    </tr>
                    <tr>
                        <td width="100"   align="left"><strong>Buyer Ref:</strong></td>
                        <td width="130" ><? echo $dataArray[0][csf('buyer_ref')];?></td>
                        <td width="120"  >&nbsp;&nbsp;<strong>Product Dept:</strong></td>
                        <td width="110" ><? echo $product_dept[$dataArray[0][csf('product_dept')]];?></td>
                        <td width="110"  ><strong>Supplier</strong></td>
                        <td width="100" ><? 
                        
                               if($dataArray_book[0][csf('pay_mode')]==1 || $dataArray_book[0][csf('pay_mode')]==2){
                                echo $supplier_library[$dataArray_book[0][csf('supplier_id')]];
                               }elseif($dataArray_book[0][csf('pay_mode')]==3 || $dataArray_book[0][csf('pay_mode')]==4 || $dataArray_book[0][csf('pay_mode')]==4){
                                echo $company_library[$dataArray_book[0][csf('supplier_id')]];
                               }
    
                        ?></td>
                        <td width="110"><strong>Est. Ship Date</strong></td>
                        <td ><? echo change_date_format($dataArray[0][csf('estimated_shipdate')]); ?></td>
    
                    </tr>
                    <tr>
                        <td width="100"><strong>Team Leader</strong></td>
                        <td width="130" ><? echo $team_leader_arr[$dataArray_book[0][csf('team_leader')]];?></td>
                        <td width="120"  >&nbsp;&nbsp;<strong>Dealing Merchandiser:</strong></td>
                        <td width="110" ><? echo $dealing_merchant_library[$dataArray_book[0][csf('dealing_marchant')]];?></td>
                        <td width="110"  ><strong>Sample Stage</strong></td>
                        <td width="100" ><? echo $sample_stage[$dataArray[0][csf('sample_stage_id')]];?></td>
                        <td width="110"><strong>Booking Date:</strong></td>
                        <td width="100"><?=change_date_format($dataArray_book[0][csf('booking_date')]);?></td>
    
                    </tr>
                </table>
    
                <table width="1100" cellspacing="0" border="0"   style="font-family: Arial Narrow;margin-left: 20px;" >
                 <tr>
                    <td width="250" align="left" valign="top" colspan="2">
                    <table align="left" cellspacing="0" border="0" width="90%" >
    
                    </table>
                </td>
                </tr>
    
    
    
                 <tr> <td colspan="6">&nbsp;</td></tr>
                <tr>
                    <td width="250" align="left" valign="top" colspan="2">
                    <?
                     $sql_sample_dtls= "SELECT a.sample_name, a.article_no, a.sample_color from sample_development_dtls a , lib_color b  where a.status_active=1 and a.is_deleted=0 and a.entry_form_id=203  and sample_mst_id in($req_id) and b.status_active=1 and a.status_active=1 and b.id=a.sample_color  group by a.sample_name,a.article_no,a.sample_color";
    
                    foreach(sql_select($sql_sample_dtls) as $key=>$value)
                    {
                        if($sample_wise_article_no[$value[csf("sample_name")]][$value[csf("sample_color")]]=="")
                        {
                            $sample_wise_article_no[$value[csf("sample_name")]][$value[csf("sample_color")]]=$value[csf("article_no")];
                        }
                        else
                        {
                            if(!in_array($value[csf("article_no")], $sample_wise_article_no))
                            {
                                $sample_wise_article_no[$value[csf("sample_name")]][$value[csf("sample_color")]].= ', '.$value[csf("article_no")];
                            }
    
                        }
                        
                        //$sample_wise_article_no[$value[csf("sample_name")]][$value[csf("sample_color")]]=$value[csf("article_no")];
    
                    }
                    /*$sql_book=sql_select("select dtls_id from wo_non_ord_samp_booking_dtls where style_id in($req_id) and status_active=1");
                    $dtls_id="";
                    foreach($sql_book as $row)
                    {
                        $dtls_id.=$row[csf("dtls_id")].',';
                    }
                    $dtls_ids=rtrim($dtls_id,',');
                    $dtls_ids=implode(",",array_unique(explode(",",$dtls_ids)));
                    if($dtls_ids) $dtls_id_cond="and a.id in($dtls_ids) ";else $dtls_id_cond="and a.id in(0)";*/
    
                     $color_sql="SELECT b.color_id ,b.process_loss_percent ,b.fabric_color,b.contrast,b.mst_id,b.dtls_id from  sample_development_rf_color b where b.status_active=1 and b.is_deleted=0 and b.qnty>0 and b.mst_id in($req_id) ";
                     $color_res=sql_select($color_sql);
                     $color_rf_data=array();
                     foreach ($color_res as $val) {
                         $color_rf_data[$val[csf('dtls_id')]][$val[csf('color_id')]][$val[csf('fabric_color')]]['process_loss_percent']=$val[csf('process_loss_percent')];
                         $color_rf_data[$val[csf('dtls_id')]][$val[csf('color_id')]][$val[csf('fabric_color')]]['contrast']=$val[csf('contrast')];
                     }
    
                //  $sql_fab="SELECT a.sample_name,a.gmts_item_id,b.color_id,b.contrast,c.finish_fabric as qnty,a.delivery_date,a.fabric_description,a.body_part_id, a.fabric_source,a.remarks_ra  ,a.gsm,a.dia, a.color_type_id,a.width_dia_id,a.uom_id,b.process_loss_percent,c.grey_fabric as grey_fab_qnty  from sample_development_fabric_acc a,sample_development_rf_color b, wo_non_ord_samp_booking_dtls c where a.id=b.dtls_id and  a.sample_mst_id=b.mst_id and a.id=c.dtls_id and c.fabric_color=b.fabric_color and c.gmts_color=b.color_id and c.dtls_id=b.dtls_id and c.style_id=a.sample_mst_id and c.style_id=b.mst_id and a.determination_id=c.lib_yarn_count_deter_id  and a.form_type=1 and b.qnty>0 and c.grey_fabric>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and a.sample_mst_id in($req_id) and b.mst_id in($req_id)  ";
    
                $sql_fab="SELECT a.sample_name,a.gmts_item_id,c.gmts_color as color_id,c.finish_fabric as qnty,a.delivery_date,a.fabric_description,a.body_part_id, a.fabric_source,a.remarks_ra  ,a.gsm,a.dia, a.color_type_id,a.width_dia_id,a.uom_id,c.grey_fabric as grey_fab_qnty,c.dtls_id,c.fabric_color  from sample_development_fabric_acc a, wo_non_ord_samp_booking_dtls c where  a.id=c.dtls_id and  c.style_id=a.sample_mst_id  and a.determination_id=c.lib_yarn_count_deter_id  and a.form_type=1 and c.grey_fabric>0 and a.status_active=1 and a.is_deleted=0  and c.status_active=1 and c.is_deleted=0  and a.sample_mst_id in($req_id) ";
                     $sql_fab_arr=array();
                     foreach(sql_select($sql_fab) as $vals)
                     {
                         $contrast=$color_rf_data[$vals[csf('dtls_id')]][$vals[csf('color_id')]][$vals[csf('fabric_color')]]['contrast'];
                         $process_loss_percent=$color_rf_data[$vals[csf('dtls_id')]][$vals[csf('color_id')]][$vals[csf('fabric_color')]]['process_loss_percent'];
    
                        $article_no=rtrim($sample_wise_article_no[$vals[csf("sample_name")]][$vals[csf("color_id")]],',');
                        $article_no=implode(",",array_unique(explode(",",$article_no)));
                        $sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$contrast]["qnty"]+=$vals[csf("qnty")];
                         $sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$contrast]["process_loss_percent"]=$process_loss_percent;
    
                         $sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$contrast]["grey_fab_qnty"]+=$vals[csf("grey_fab_qnty")];
    
                         $sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$contrast]["delivery_date"] =change_date_format($vals[csf("delivery_date")]);
    
                         $sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$contrast]["fabric_source"] =$vals[csf("fabric_source")];
    
                         $sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$contrast]["uom_id"] =$vals[csf("uom_id")];
                        $sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$contrast]["dia"] =$vals[csf("dia")];
    
                         $sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$contrast]["width_dia_id"] =$vals[csf("width_dia_id")];
    
                         $sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$contrast]["remarks"] =$vals[csf("remarks_ra")];
                         $sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$contrast]["color_type_id"] =$vals[csf("color_type_id")];
                     }
                     $sample_item_wise_span=array(); $sample_item_wise_color_span=array();
    
                  foreach($sql_fab_arr as $article_no=>$article_data) 
                  {
                    $article_no_span=0;
                    foreach($article_data as $sample_type_id=>$sampleType_data) 
                    {
                    $sample_type_span=0;
                    foreach($sampleType_data as $gmts_color_id=>$gmts_color_data)
                    {
                        $sample_span=0;
                        foreach($gmts_color_data as $body_part_id=>$body_part_data)
                        {
                            
                            //echo $gmts_color_id.'d';
    
                            foreach($body_part_data as $fab_id=>$fab_desc_data)
                            {
                                //$kk=0;
                                foreach($fab_desc_data as $colorType=>$colorType_data)
                                {
    
                                    foreach($colorType_data as $gsm_id=>$gsm_data)
                                    {
                                        foreach($gsm_data as $dia_id=>$dia_data)
                                        {
                                           foreach($dia_data as $dia_type_id=>$diatype_data)
                                           {
    
                                            foreach($diatype_data as $contrast_id=>$value)
                                            {
                                                $sample_span++;$sample_type_span++;$article_no_span++;
                                                //$kk++;
    
                                            }
                                                $article_wise_span[$article_no]=$article_no_span;
                                                $sample_item_wise_span[$article_no][$sample_type_id]=$sample_type_span;
                                                $sample_item_wise_color_span[$article_no][$sample_type_id][$gmts_color_id]=$sample_span;
                                          }
                                        }
    
                                    }
    
    
                                }
    
                                //$bodypart_item_wise_span[$sample_type][$gmts_item_id][$body_part_id]=$kk;
    
                            }
                        //	$sample_item_wise_span[$sample_type][$gmts_color_id]=$sample_span;
    
                          }
                         }
    
                        }
                    }
                    //echo "<pre>";
                    //print_r($sample_item_wise_color_span);die;
                    // echo "<pre>"; print_r($sample_wise_article_no);die;
    
                    ?>
                    <table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all">
                        <thead>
                        <tr>
                            <th colspan="19">Required Fabric</th>
                        </tr>
                            <tr>
                                <th width="30">Sl</th>
                                <th width="90">ALT / [C/W]</th>
                                <th width="110">Sample Type</th>
                                <th width="80">Gmt Color</th>
                                <th width="80">Fab. Deli Date</th>
                                <th width="120">Body Part</th>
                                <th width="200">Fabric Desc & Composition</th>
                                <th width="80">Color Type</th>
                                <th width="80">Fab.Color</th>
                                <th width="40">Item Size</th>
                                <th width="55">GSM</th>
                                <th width="55">Dia</th>
                                <th width="60">Width/Dia</th>
                                <th width="40">UOM</th>
                                <th width="60">Grey Qnty</th>
                                <th width="40">P. Loss</th>
                                <th width="80">Fin Fab Qnty</th>
                                <th width="80">Fabric Source</th>
                                <th width="80">Remarks</th>
    
                            </tr>
                        </thead>
                        <tbody>
                            <?
                            $p=1;
                            $total_finish=0;
                            $total_grey=0;
                            $total_process=0;
                 foreach($sql_fab_arr as $article_no=>$article_data) 
                 {
                    $aa=0;
                    foreach($article_data as $sample_type_id=>$sampleType_data) 
                    {
                    $nn=0;
                    foreach($sampleType_data as $gmts_color_id=>$gmts_color_data)
                    {
                        $cc=0;
                        foreach($gmts_color_data as $body_part_id=>$body_part_data)
                        {
                            
                            //echo $gmts_color_id.'d';
    
                            foreach($body_part_data as $fab_id=>$fab_desc_data)
                            {
                                //$kk=0;
                                foreach($fab_desc_data as $colorType=>$colorType_data)
                                {
    
                                    foreach($colorType_data as $gsm_id=>$gsm_data)
                                    {
                                        foreach($gsm_data as $dia_id=>$dia_data)
                                        {
    
                                            foreach($dia_data as $dia_type=>$diatype_data)
                                            {
                                                foreach($diatype_data as $contrast_id=>$value)
                                                {
    
                                                                 
                                                            ?>
                                                            <tr>
    
    
                                                                    
                                                                    <?
                                                                if($aa==0)
                                                                {
                                                                    ?>
                                                                    <td  rowspan="<? echo $article_wise_span[$article_no];?>"  align="center" style="word-wrap: break-word;word-break: break-all;"><? echo $p;$p++;?></td>
                                                                    <td   rowspan="<? echo $article_wise_span[$article_no];?>" align="center"><? echo $article_no;?></td>
                                                                    <?
                                                                }
                                                                if($nn==0)
                                                                {
                                                                    ?>
                                                                    
                                                                    <td   rowspan="<? echo $sample_item_wise_span[$article_no][$sample_type_id];?>"  align="center"><? echo $sample_library[$sample_type_id]; ?></td>
                                                                    
                                                                    <?
                                                                    
                                                                }
                                                                if($cc==0)
                                                                {
                                                                 ?>
                                                                 <td   align="center" rowspan="<? echo $sample_item_wise_color_span[$article_no][$sample_type_id][$gmts_color_id];?>"><? echo $color_library[$gmts_color_id];?> </td>
                                                                  <td   rowspan="<? echo $sample_item_wise_color_span[$article_no][$sample_type_id][$gmts_color_id];?>" align="center" ><? echo $value["delivery_date"];?> </td>
                                                                 <?
                                                                } ?>
    
                                                                
                                                                 <td width="120"  align="center"><? echo $body_part[$body_part_id];?></td>
                                                                 <td  align="center"><? echo $fab_id;?></td>
                                                                 <td  align="center"> <? echo $color_type[$colorType]; ?></td>
                                                                 <td  align="center"><? echo $contrast_id; ?></td>
                                                                 <td  align="center"><? echo $value["item_size"]; ?></td>
                                                                 <td  align="center"><? echo $gsm_id; ?></td>
                                                                 <td  align="center"><? echo $value["dia"]; ?></td>
                                                                 <td  align="center"><? echo $fabric_typee[$dia_type]; ?></td>
                                                                 <td   align="center"><? echo $unit_of_measurement[$value["uom_id"]];?></td>
    
                                                                 <td align="right"><? echo number_format($value["grey_fab_qnty"],2);?></td>
                                                                 <td align="right"><? echo $value["process_loss_percent"];?></td>
                                                                 <td align="right"><? echo number_format($value["qnty"],2);?></td>
    
                                                                 <td align="center"><? echo $fabric_source[$value["fabric_source"]];?></td>
                                                                 <td  align="center"><? echo $value["remarks"];?></td>
    
                                                            </tr>
    
    
                                                            <?
                                                            $nn++;$cc++;$aa++;
                                                            //$i++;
                                                            $total_finish +=$value["qnty"];
                                                            $total_grey +=$value["grey_fab_qnty"];
                                                            $total_process +=$value["process_loss_percent"];
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                  }
                                }
                            }
                         }
    
                            ?>
    
                            <tr>
                                <th colspan="14" align="right"><b>Total</b></th>
                                <th width="80" align="right"><? echo number_format($total_grey,2);?></th>
                                <th width="40" align="right">&nbsp;</th>
                                <th width="60" align="right"><? echo number_format($total_finish,2);?></th>
                                <th width="80" colspan="2"> </th>
    
                            </tr>
    
                        </tbody>
    
    
    
                    </table>
                    <br/>
    
    
    
        <?
    
                    $sample_color_arr=return_library_array( "select id, sample_color from sample_development_dtls", "id", "sample_color"  );
                              $sql_qry="SELECT id,sample_mst_id,sample_name,gmts_item_id,smv,article_no,sample_color,sample_prod_qty,submission_qty,delv_start_date,delv_end_date,sample_charge,sample_curency,sent_to_buyer_date,comments from sample_development_dtls where status_active =1 and is_deleted=0 and entry_form_id=203 and sample_mst_id in($req_id) order by id asc";
                                $sql_qry_color="SELECT a.id,a.sample_mst_id,a.sample_name,a.gmts_item_id,a.smv,a.article_no,a.sample_color,a.sample_prod_qty,a.submission_qty,a.delv_start_date,a.delv_end_date,a.sample_charge,a.sample_curency,a.sent_to_buyer_date,a.comments,c.dtls_id,c.size_id,c.bh_qty,c.self_qty,c.test_qty,c.plan_qty,c.dyeing_qty from sample_development_dtls a,sample_development_size c where a.id=c.dtls_id and  a.status_active =1 and a.is_deleted=0 and a.entry_form_id=203 and a.sample_mst_id in($req_id) order by a.id asc";
                             $size_type_arr=array(1=>"bh_qty",2=>"Self Qty",3=>"Test qty",4=>"Plan Qty",5=>"Dyeing Qty");
                             $color_size_arr=array();
                              foreach(sql_select($sql_qry_color) as $vals)
                             {
                                    if($vals[csf("bh_qty")]>0)
                                    {
                                    $color_size_arr[1][$vals[csf("size_id")]]='Bh Qty';
                                    $bh_qty=$vals[csf("bh_qty")];
                                    $color_size_dtls_qty_arr[1][$vals[csf("id")]][$vals[csf("size_id")]]=$bh_qty;
                                    }
                                    if($vals[csf("self_qty")]>0)
                                    {
                                    $color_size_arr[2][$vals[csf("size_id")]]='self qty';
                                    $color_size_dtls_qty_arr[2][$vals[csf("id")]][$vals[csf("size_id")]]=$vals[csf("self_qty")];
                                    }
                                    if($vals[csf("test_qty")]>0)
                                    {
                                    $color_size_arr[3][$vals[csf("size_id")]]='test qty';
                                    $color_size_dtls_qty_arr[3][$vals[csf("id")]][$vals[csf("size_id")]]=$vals[csf("test_qty")];
                                    }
                                    if($vals[csf("plan_qty")]>0)
                                    {
                                    $color_size_arr[4][$vals[csf("size_id")]]='plan qty';
                                    //$size_plan_arr[$vals[csf("size_id")]]=$vals[csf("size_id")];
                                    $color_size_dtls_qty_arr[4][$vals[csf("id")]][$vals[csf("size_id")]]=$vals[csf("plan_qty")];
    
                                    }
                                    if($vals[csf("dyeing_qty")]>0)
                                    {
                                    $color_size_arr[5][$vals[csf("size_id")]]='Dyeing qty';
                                    $color_size_dtls_qty_arr[5][$vals[csf("id")]][$vals[csf("size_id")]]=$vals[csf("dyeing_qty")];
    
                                    }
    
                                }
                                $tot_row=count($color_size_arr);
                                $result=sql_select($sql_qry);
    
        ?>
    
    
                        <table align="left" cellspacing="0" border="1" width="100%" class="rpt_table" rules="all">
                        <thead>
                            <tr>
                                    <td width="150" colspan="<? echo 12+$tot_row;?>" align="center"><strong>Sample Details</td>
                                </tr>
                                <tr>
                                        <th width="30" rowspan="2" align="center">Sl</th>
                                        <th width="100" rowspan="2" align="center">Sample Name</th>
                                        <th width="120" rowspan="2" align="center">Garment Item</th>
    
                                        <th width="55" rowspan="2" align="center">ALT / [C/W]</th>
                                        <th width="70" rowspan="2" align="center">Color</th>
                                        <?
                                        $tot_row_td=0;
                                        foreach($color_size_arr as $type_id=>$val)
                                        { ?>
                                            <th width="45" align="center" colspan="<? echo count($val);?>"> <?
                                                   echo  $size_type_arr[$type_id];
                                            ?></th>
                                            <?
    
                                        }
                                        ?>
                                        <th rowspan="2" width="55" align="center">Total</th>
                                        <th rowspan="2" width="55" align="center">Submn Qty</th>
                                        <th rowspan="2"  width="70" align="center">Buyer Submisstion Date</th>
                                        <th rowspan="2"  width="70" align="center">Remarks</th>
                                 </tr>
                                 <tr>
                                     <?
                                    foreach($color_size_arr as $type_id=>$data_size)
                                    {
                                        foreach($data_size as $size_id=>$data_val)
                                        {
                                        $tot_row_td++;
                                        ?>
                                            <th width="40" align="center"><? echo $size_library[$size_id]; ?></th>
                                            <?
                                        }
                                     }
    
                                     ?>
                                 </tr>
    
                        </thead>
                            <tbody>
    
                                <?
    
                                 $i=1;$k=0;
                                 $gr_tot_sum=0;
                                 $gr_sub_sum=0;
                                foreach($result as $row)
                                {
                                    $dtls_ids=$row[csf('id')];
                                     //$size_select=sql_select("SELECT  size_id,total_qty  from sample_development_size where  mst_id in($req_id) and status_active=1 and is_deleted=0 and dtls_id='$dtls_ids' ");
                                     $prod_sum=$prod_sum+$row[csf('sample_prod_qty')];
                                    $sub_sum=$sub_sum+$row[csf('submission_qty')];
    
                                ?>
                                <tr>
                                    <?
                                     $k++;
                                    ?>
                                    <td  align="center"><? echo $k;?></td>
                                    <td  align="left"><? echo $sample_library[$row[csf('sample_name')]];?></td>
                                    <td  align="left"><? echo $garments_item[$row[csf('gmts_item_id')]];?></td>
    
                                    <td   align="left"><? echo $row[csf('article_no')];?></td>
                                    <td width="70" align="left"><? echo $color_library[$row[csf('sample_color')]];?></td>
    
    
                                    <?
                                    $total_sizes_qty=0;
                                    $total_sizes_qty_subm=0;
                                      foreach($color_size_arr as $type_id=>$data_size)
                                    {
                                        foreach($data_size as $size_id=>$data_val)
                                        {
                                        $size_qty=$color_size_dtls_qty_arr[$type_id][$dtls_ids][$size_id];
                                        ?>
                                        <td align="right"><? echo $size_qty; ?></td>
                                        <?
                                            if($type_id==1)
                                            {
                                            $total_sizes_qty_subm+=$size_qty;
                                            }
                                            $total_sizes_qty+=$size_qty;
                                        }
                                    }
                                    ?>
                                    <td align="right"><? echo $total_sizes_qty;?></td>
                                    <td align="right"><? echo $total_sizes_qty_subm;?></td>
                                    <td   align="left"><? echo change_date_format($row[csf('sent_to_buyer_date')]);?> </td>
                                    <td   align="left"><? echo $row[csf('comments')];?> </td>
                                    <?
                                    $gr_tot_sum+=$total_sizes_qty;
                                     $gr_sub_sum+=$total_sizes_qty_subm;
                                }
                                ?>
                                </tr>
                                    <tr>
                                            <td colspan="<? echo 5+$tot_row_td; ?>" align="right"><b>Total</b></td>
                                             <td   align="right"><b><? echo number_format($gr_tot_sum,2);?> </b></td>
                                             <td  align="right"><b><? echo number_format($gr_sub_sum,2);?> </b></td>
                                            <td colspan="2"></td>
                                    </tr>
                            </tbody>
                            <tfoot>
                             </tfoot>
                       </table>
                     </td>
            </tr>
             <tr> <td colspan="6">&nbsp;</td></tr>
            <tr>
                <td width="250" align="left" valign="top" colspan="2">
    
                 </td>
            </tr>
    
            <tr>
                <td width="250" align="left" valign="top" colspan="2">
                    <table align="left" cellspacing="0" border="1" width="100%" class="rpt_table" rules="all">
                    <thead>
                        <tr>
                                <td width="150" colspan="10" align="center"><strong>Required Accessories</td>
                            </tr>
                            <tr>
                                    <th width="30" align="center">Sl</th>
                                    <th width="100" align="center">Sample Name</th>
                                    <th width="120" align="center">Garment Item</th>
                                    <th width="100" align="center">Trims Group</th>
                                    <th width="100" align="center">Description</th>
                                    <th width="100" align="center">Supplier</th>
                                    <th width="100" align="center">Brand/Supp.Ref</th>
                                     <th width="30" align="center">UOM</th>
                                    <th width="30" align="center">Req/Dzn </th>
                                    <th width="30" align="center">Req/Qty </th>
                                    <th width="80" align="center">Acc.Sour. </th>
                                    <th width="100" align="center">Acc Delivery Date </th>
                                    <th width="80" align="center">Remarks </th>
                             </tr>
                    </thead>
                        <tbody>
    
    
                            <?
                           $sql_qryA="SELECT id,sample_mst_id,sample_name_ra,gmts_item_id_ra,trims_group_ra,description_ra,brand_ref_ra,uom_id_ra,req_dzn_ra,req_qty_ra,remarks_ra,delivery_date,supplier_id,fabric_source from sample_development_fabric_acc where status_active =1 and is_deleted=0 and form_type=2 and sample_mst_id in($req_id) order by id asc";
    
                            $resultA=sql_select($sql_qryA);
                             $i=1;$k=0;
                             $req_dzn_ra=0;
                             $req_qty_ra=0;
                            foreach($resultA as $rowA)
                            {
                                $req_dzn_ra=$req_dzn_ra+$rowA[csf('req_dzn_ra')];
                                $req_qty_ra=$req_qty_ra+$rowA[csf('req_qty_ra')];
    
                            ?>
                            <tr>
                                <?
                                 $k++;
                                ?>
                                <td  align="center"><? echo $k;?></td>
                                <td  align="left"><? echo $sample_library[$rowA[csf('sample_name_ra')]];?></td>
                                <td  align="left"><? echo $garments_item[$rowA[csf('gmts_item_id_ra')]];?></td>
                                <td  align="left"><? echo $trims_group_lib[$rowA[csf('trims_group_ra')]];?></td>
                                <td  align="left"><? echo $rowA[csf('description_ra')];?></td>
                                <td  align="left"><? echo $supplier_library[$rowA[csf('supplier_id')]];?></td>
                                <td  align="left"><? echo $rowA[csf('brand_ref_ra')];?></td>
                                 <td  align="center"><? echo $unit_of_measurement[$rowA[csf('uom_id_ra')]];?></td>
                                <td  align="right"><? echo $rowA[csf('req_dzn_ra')];?></td>
                                <td  align="right"><? echo $rowA[csf('req_qty_ra')];?></td>
                                <td  align="left"><? echo $fabric_source[$rowA[csf('fabric_source')]];?></td>
                                <td  align="left"><? echo change_date_format($rowA[csf('delivery_date')]);?></td>
                                <td  align="left"><? echo $rowA[csf('remarks_ra')];?></td>
    
                                <?
                            }
    
                            ?>
    
    
    
    
                            </tr>
    
                              <tr>
                                        <td colspan="8" align="center"><b>Total </b></td>
                                        <!-- <td align="right"><b><? echo number_format($req_dzn_ra,2);?> </b></td> -->
                                          <td align="right"  ><b><? echo number_format($req_qty_ra,2);?> </b></td>
                                          <td>&nbsp;</td>
    
                                 </tr>
    
    
                        </tbody>
                        <tfoot>
    
                        </tfoot>
                   </table>
                 </td>
            </tr>
             <tr> <td colspan="6">&nbsp;</td></tr>
    
             <tr>
                <td width="250" align="left" valign="top" colspan="2">
                    <table align="left" cellspacing="0" border="1" width="100%" class="rpt_table" rules="all">
                    <thead>
                        <tr>
                                <td width="150" colspan="6" align="center"><strong>Required Emebellishment</td>
                            </tr>
                            <tr>
                                <th width="30" align="center">Sl</th>
                                <th width="100" align="center">Sample Name</th>
                                <th width="110" align="center">Garment Item</th>
                                <th width="110" align="center">Body Part</th>
                                <th width="100" align="center">Supplier</th>
                                <th width="60" align="center">Name</th>
                                <th width="70" align="center">Type</th>
                                <th width="100" align="center">Emb.Del.Date</th>
                                <th width="70" align="center">Remarks</th>
    
                             </tr>
                    </thead>
                        <tbody>
    
    
                            <?
                            $sql_qry="SELECT id,sample_mst_id,sample_name_re,gmts_item_id_re,name_re,type_re,remarks_re,body_part_id,delivery_date,supplier_id from sample_development_fabric_acc where sample_mst_id in($req_id) and form_type=3 and is_deleted=0  and status_active=1 order by id asc";
    
                            $result=sql_select($sql_qry);
                             $k=0;
                             $type_array=array(1=>$emblishment_print_type,2=>$emblishment_embroy_type,3=>$emblishment_wash_type,4=>$emblishment_spwork_type,5=>$emblishment_gmts_type);
                            foreach($result as $row)
                            {
    
                            ?>
                            <tr>
                                <?
                                 $k++;
                                ?>
                                <td  align="center"><? echo $k;?></td>
                                <td  align="left"><? echo $sample_library[$row[csf('sample_name_re')]];?></td>
                                <td  align="left"><? echo $garments_item[$row[csf('gmts_item_id_re')]];?></td>
                                <td  align="left"><? echo $body_part[$row[csf('body_part_id')]];?></td>
                                <td  align="left"><? echo $supplier_library[$row[csf('supplier_id')]];?></td>
                                <td  align="left"><? echo $emblishment_name_array[$row[csf('name_re')]];?></td>
                                <td  align="left">
                                <?
                                if($row[csf('name_re')]==1)
                                {
                                    echo $emblishment_print_type[$row[csf('type_re')]];
                                  }
                                  if($row[csf('name_re')]==2)
                                {
                                    echo $emblishment_embroy_type[$row[csf('type_re')]];
                                  }
                                  if($row[csf('name_re')]==3)
                                {
                                    echo $emblishment_wash_type[$row[csf('type_re')]];
                                  }
                                  if($row[csf('name_re')]==4)
                                {
                                    echo $emblishment_spwork_type[$row[csf('type_re')]];
                                  }
                                  if($row[csf('name_re')]==5)
                                {
                                    echo $emblishment_gmts_type[$row[csf('type_re')]];
                                  }
                                ?>
    
                                </td>
                                <td  align="left"><? echo change_date_format($row[csf('delivery_date')]);?></td>
                                <td  align="left"><? echo $row[csf('remarks_re')];?></td>
                                  <?
                            }
    
                            ?>
    
    
    
    
                            </tr>
    
    
                        </tbody>
                        <tfoot>
    
                        </tfoot>
                   </table>
    
                   <br>
                   <table>
                           <tr>
                               <td>
                                   <table  style="margin-top: 10px;" class="rpt_table" width="600" align="left" border="1" cellpadding="0" cellspacing="0" rules="all">
                                       <caption> <b> Yarn Required Summary </b> </caption>
                                           <thead>
                                               <tr align="center">
                                                   <th width="40">Sl</th>
                                                   <th>Yarn Desc.</th>
                                                    <th>Req. Qty</th> 
                                               </tr>
                                           </thead>
                                           <tbody>
                                           <?
                                           $lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count"  );
                                           $lib_supllier_arr=return_library_array( "select supplier_id,booking_no from wo_non_ord_samp_booking_mst where booking_no='$data[2]' and entry_form_id=140", "booking_no", "supplier_id"  );
                                       //	echo  "select supplier_id,booking_no from wo_non_ord_samp_booking_mst where booking_no='$data[2]' and entry_form_id=140";
                                           $tot_req_qty=0;//sample_development_mst
                                           //$data_array=sql_select("select b.booking_no,b.determin_id,b.count_id,b.copm_one_id,b.percent_one,b.type_id,b.cons_qnty from  sample_development_yarn_dtls b,sample_development_fabric_acc a where a.sample_mst_id=b.mst_id and a.determination_id=b.determin_id and b.status_active=1 and a.status_active=1 and b.mst_id in($req_id) and a.form_type=1 group by b.booking_no, b.determin_id, b.count_id, b.copm_one_id, b.percent_one, b.type_id, b.cons_qnty");
                                           $data_array=sql_select("SELECT b.booking_no,b.determin_id,b.count_id,b.copm_one_id,b.percent_one,b.type_id, sum (b.cons_qnty) as cons_qnty from  sample_development_yarn_dtls b where  b.status_active=1  and b.mst_id in($req_id) and b.determin_id in (select determination_id from sample_development_fabric_acc  where status_active=1 and sample_mst_id in($req_id) and form_type=1) group by b.booking_no,b.determin_id,b.count_id,b.copm_one_id,b.percent_one,b.type_id");
    
                                           //echo "select b.booking_no,b.determin_id,b.count_id,b.copm_one_id,b.percent_one,b.type_id,b.cons_qnty from  sample_development_yarn_dtls b,sample_development_fabric_acc a where a.sample_mst_id=b.mst_id and a.determination_id=b.determin_id and b.status_active=1 and a.status_active=1  and b.mst_id in($req_id) and a.form_type=1";
                                       
                                           if ( count($data_array)>0)
                                           {
                                               $l=1;
                                               foreach( $data_array as $key=>$row )
                                               {
                                                   $yarn_des=$lib_yarn_count[$row[csf("count_id")]].','.$composition[$row[csf("copm_one_id")]].','.$row[csf("percent_one")].'%,'.$yarn_type[$row[csf("type_id")]];
                                                   ?>
                                                       <tr>
                                                           <td> <? echo $l;?> </td>
                                                           <td> <? echo $yarn_des; ?> </td>
                                                           <td align="right"> <? echo number_format($row[csf("cons_qnty")],2); ?> </td>
                                                       </tr>
                                                   <?
                                                   $l++;
                                                   $tot_req_qty+=$row[csf("cons_qnty")];
                                               }
                                           }
    
                                           ?>
                                           <tr>
                                               <th  colspan="2" align="right"><b>Total</b></th>
                                               <th  align="right"><? echo number_format($tot_req_qty,2);?></th>
                                           </tr>
                                       </tbody>
                                   </table>
                               </td>
                               <td width="300">
                                   <?php 
    
                                       $sql_image=sql_select("select image_location from common_photo_library where master_tble_id='$data[2]' ");
    
                                    ?>
                                    <img src="<?=$path;?><?php echo $sql_image[0][csf('image_location')];?>" width="200" height="150" style="justify-content: center;text-align: center;float: right;">
                               </td>
                           </tr>
                   </table>
                       
                
                    <br>
                     <br>
    
                       <table  style="margin-top: 10px;" class="rpt_table" width="600" align="left" border="1" cellpadding="0" cellspacing="0" rules="all">
                        <thead>
                            <tr>
                                <th align="left" width="40">Sl</th>
                                <th align="left" >Special Instruction</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?
                        $data_array=sql_select("select id, terms from  wo_booking_terms_condition where entry_form=140 and booking_no='$data[2]'");
                        if ( count($data_array)>0)
                        {
                            $l=1;
                            foreach( $data_array as $key=>$row )
                            {
    
                                ?>
                                    <tr  align="">
                                        <td> <? echo $l;?> </td>
                                        <td> <? echo $row[csf("terms")]; ?> </td>
                                    </tr>
                                <?
                                $l++;
                            }
                        }
    
                        ?>
                    </tbody>
                </table>
                 </br>
    
    
                 </td>
            </tr>
             <tr> <td colspan="6">&nbsp;</td></tr>
    
            <tr>
                <td width="810" align="left" valign="top" colspan="2" >
                    <table align="left" cellspacing="0" width="810" class="rpt_table" >
                        <tr>
                            <td colspan="6">
                                <?
    
                                    $user_id=$_SESSION['logic_erp']['user_id'];
                                    $user_arr=return_library_array( "select id, USER_NAME from user_passwd where id=$user_id", "id", "USER_NAME");
                                    $prepared_by = $user_arr[$user_id];
                                      //echo signature_table(134, $data[0], "810px");
                                      echo signature_table(134, $data[0], "1080px",$cbo_template_id,$padding_top = 70,$prepared_by);
                                ?>
                            </td>
    
                        </tr>
    
                    </table>
                </td>
            </tr>
            </table>
    
        </div>
    
    
        
    
     <?




        $message=ob_get_contents();
        ob_clean();

        if($approval_type==1){
            $mail_item=42;
            $subject="Sample Requisition Acknowledge";
        }
        else
        {
            $mail_item=43;
            $subject="Sample Requisition Unacknowledge";
        }


	
        $to='';
        $sql = "SELECT a.BRAND_IDS,a.BUYER_IDS,c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id  and a.mail_item=b.MAIL_ITEM_MST and a.mail_item=$mail_item and b.mail_user_setup_id=c.id and a.company_id=$company_id and A.IS_DELETED=0 and A.STATUS_ACTIVE=1 AND a.MAIL_TYPE=1 and b.IS_DELETED=0 and b.STATUS_ACTIVE=1  and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
        // echo $sql;die;
    
        $mail_sql=sql_select($sql);
        $receverMailArr=array();
        foreach($mail_sql as $row)
        {
            $buyerArr=explode(',',$row[BUYER_IDS]);
            $brandArr=explode(',',$row[BRAND_IDS]);

            if(count($buyerArr)){
                foreach($buyerArr as $buyerid){
                    foreach($brandArr as $brandid){
                        $receverMailArr[$buyerid][$brandid][$row[csf('email_address')]]=$row[csf('email_address')];
                    }
                }
            }
            else{
                $receverMailArr[end($dataArr['BUYER_ID'])][$brandid][$row[csf('email_address')]]=$row[csf('email_address')];
            }
            
        }

        $to=implode(',',$receverMailArr[end($dataArr['BUYER_ID'])][$brand_id]);
        $header=mailHeader();

      //  echo $message;die;

        if($to!=""){echo sendMailMailer( $to, $subject, $message, $from_mail,$att_file_arr );}

        
        
        exit();
    }


    if($action=="sample_requisition_print1")
    {
    foreach(explode(',',$req_id) as $req_id){
    ob_start();
	extract($_REQUEST);
	$data=explode('*',$data);
	$cbo_template_id=$data[3];

    $data[0]=$company_id;



	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supplier_library=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name");
	$company_img=sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$dealing_merchant_library=return_library_array( "select id,team_member_name from lib_mkt_team_member_info", "id", "team_member_name");
	$team_leader_arr=return_library_array( "select id,team_leader_name from lib_marketing_team", "id", "team_leader_name");

	$sample_library=return_library_array( "select id, sample_name from lib_sample", "id", "sample_name");
	$size_library=return_library_array( "select id, size_name from lib_size where status_active =1 and is_deleted=0 ", "id", "size_name");
	$color_library=return_library_array( "select id, color_name from lib_color where status_active =1 and is_deleted=0 ", "id", "color_name");
	$season_arr=return_library_array( "select id,season_name from lib_buyer_season  where status_active =1 and is_deleted=0 ", "id", "season_name");
	$brandArr=return_library_array( "select id, brand_name from lib_buyer_brand", "id", "brand_name");
	$trims_group_lib=return_library_array( "select id,item_name from lib_item_group  where status_active =1 and is_deleted=0", "id", "item_name");
	//concate(buyer_name,'_',contact_person)
	$appDate=return_field_value("approved_date","approval_history","entry_form=25 and mst_id in($req_id) order by id desc");
	$appBy=return_field_value("approved_by","approval_history","entry_form=25 and mst_id in($req_id)");
	$user_arr=return_library_array( "select id, user_name from user_passwd", "id", "user_name");
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='sample_details_1' and file_type=1",'master_tble_id','image_location');
	$page_path=$data[4];
	if($page_path==0 && $page_path!='')
	{
		$page_path="../";
	}
	else
	{
		$page_path="../";
		// $page_path="../../";
		
	}
 ?>
	<!-- <style>
		#mstDiv {
			margin:0px auto;
			width:1130px;
		}
		#mstDiv @media print {
			thead {display: table-header-group;}
		}
		@media print{
			html>body table.rpt_table {
				margin-left:12px;
			}
		}
    </style> -->
	<div id="mstDiv">
        <table width="1100" cellspacing="0" border="0"  style="font-family: Arial Narrow;" >
            <tr>
                <td align="left" rowspan="4" valign="top" width="300"><img width="150" height="80" src="<? echo $page_path.$company_img[0][csf("image_location")]; ?>" ></td>
                <td align="center" colspan="4" style="font-size:20px;"><strong><b><? echo $company_library[$data[0]]; ?></b></strong></br>
				<?
                    $val=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
                    echo ($val[0][csf('plot_no')])?   $val[0][csf('plot_no')].',': "";
                    echo ($val[0][csf('level_no')])?  $val[0][csf('level_no')].',': "";
                    echo ($val[0][csf('road_no')])?   $val[0][csf('road_no')].',': "";
                    echo ($val[0][csf('block_no')])?  $val[0][csf('block_no')].',': "";
                    echo ($val[0][csf('city')])?      $val[0][csf('city')].',': "";
                    echo ($val[0][csf('zip_code')])?  $val[0][csf('zip_code')].',': "";
                    echo ($val[0][csf('province')])?  $val[0][csf('province')].',': "";
                    echo ($val[0][csf('country_id')])? $country_arr[$val[0][csf('country_id')]]: "";
                    echo ($val[0][csf('email')])?    "</br>". $val[0][csf('email')].',': "</br>";
                    echo ($val[0][csf('website')])?    $val[0][csf('website')]: "";

                    $sql="SELECT id, requisition_number, requisition_number_prefix_num, style_ref_no, buyer_name, product_dept, dealing_marchant, agent_name, buyer_ref, bh_merchant, estimated_shipdate, team_leader, season_buyer_wise, remarks, quotation_id, sample_stage_id, requisition_date, material_delivery_date, season_year, brand_id from sample_development_mst where  id in($req_id) and entry_form_id=449 and is_deleted=0 and status_active=1";
                    $dataArray=sql_select($sql);
                    $barcode_no=$dataArray[0][csf('requisition_number')];
					$quotation_id=$dataArray[0][csf("quotation_id")];
                    if($dataArray[0][csf("sample_stage_id")]==1)
                    {
                        $job_lib=return_library_array( "SELECT a.id,min(b.shipment_date) as shipment_date  from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and a.company_name='$data[0]' GROUP BY a.id", "id", "shipment_date");
                    }
					if($dataArray[0][csf("sample_stage_id")]==2)
                    {
                       $bodywashcolor=return_field_value("color","wo_quotation_inquery","id='$quotation_id'");
                    }
                    $sqls="SELECT style_desc, supplier_id, revised_no,team_leader, buyer_req_no, source, booking_date, attention from wo_non_ord_samp_booking_mst where booking_no='$data[2]' and is_deleted=0 and status_active=1";
                    $dataArray_book=sql_select($sqls);
                    ?>
			
			
			</td>
                <td align="center" width="200">
					<?
                    $nameArray_approved=sql_select( "SELECT approved_by,approved_date from wo_non_ord_samp_booking_mst a, approval_history b where a.id=b.mst_id and a.booking_no='$data[2]' and b.entry_form=9 and a.status_active =1 and a.is_deleted=0 order by b.id desc ");
                    $approved_by= $user_arr[$nameArray_approved[0][csf("approved_by")]];
                    $approved_date= change_date_format($nameArray_approved[0][csf("approved_date")]);
                    ?>
                </td>
            </tr>
            
            <tr>
                <td colspan="3" style="font-size:medium"><strong style="font-size:18px"> <u>Sample Program Without Order</u></strong></td>
                <td colspan="2" width="250"><b>Approved By :<?=$approved_by ?></b> </br><b>Approved Date :<?=$approved_date ?></b> </td>
            </tr>
        </table>

        <table width="1100" cellspacing="0" border="0" class="rpt_table" style="font-family: Arial Narrow;" >
        	<tr>
                <td width="130"><strong>System No.: </strong></td>
                <td width="130"><strong><?=$dataArray[0][csf("requisition_number")];?></strong></td>
                <td width="130"><strong>Booking Date:</strong></td>
                <td width="130"><?=$dataArray_book[0][csf('booking_date')];?></td>
                <td width="130"><strong>Sample Stage:</strong></td>
                <td width="130"><?=$sample_stage[$dataArray[0][csf('sample_stage_id')]];?></td>
                <td width="130"><strong>Revise:</strong></td>
                <td><?=$dataArray_book[0][csf('revised_no')];?></td>
            </tr>
            <tr>
                <td><strong>W/O No: </strong></td>
                <td><?=$data[2];?></td>
                <td><strong>Style Ref:</strong></td>
                <td><?=$dataArray[0][csf('style_ref_no')];?></td>
                <td><strong>Style Desc.:</strong></td>
                <td><?=$dataArray_book[0][csf('style_desc')];?></td>
                <td><strong>Sample Sub Date:</strong></td>
                <td><?=change_date_format($dataArray[0][csf('material_delivery_date')]);?></td>
            </tr>
            <tr>
                <td><strong>Buyer Name: </strong></td>
                <td><?=$buyer_library[$dataArray[0][csf('buyer_name')]];?></td>
                <td><strong>Season:</strong></td>
                <td><?=$season_arr[$dataArray[0][csf('season_buyer_wise')]];?></td>
                <td><strong>Season Year: </strong></td>
                <td><?=$dataArray[0][csf('season_year')];?></td>
                <td><strong>Brand:</strong></td>
                <td><?=$brandArr[$dataArray[0][csf('brand_id')]];?></td>
            </tr>
            <tr>
            	<td><strong>BH Merchant:</strong></td>
                <td><?=$dataArray[0][csf('bh_merchant')];?></td>
                <td><strong>Attention:</strong></td>
                <td style="word-wrap: break-word;word-break: break-all;" ><?=$dataArray_book[0][csf('attention')];?></td>
                <td><strong>Buyer Ref:</strong></td>
                <td><?=$dataArray[0][csf('buyer_ref')];?></td>
                <td><strong>Product Dept:</strong></td>
                <td><?=$product_dept[$dataArray[0][csf('product_dept')]];?></td>
            </tr>
            <tr>
            	<td><strong>Supplier:</strong></td>
                <td><?=$supplier_library[$dataArray_book[0][csf('supplier_id')]];?></td>
                <td><strong>Est. Ship Date:</strong></td>
                <td><?=change_date_format($dataArray[0][csf('estimated_shipdate')]);?></td>
                <td><strong>Team Leader:</strong></td>
                <td><?=$team_leader_arr[$dataArray_book[0][csf('team_leader')]];?></td>
                <td title="Booking "><strong>Dealing Merchant:</strong></td>
                <td><?=$dealing_merchant_library[$dataArray[0][csf('dealing_marchant')]];?></td>
            </tr>
            <tr>
            	<td><strong>Body/Wash Color:</strong></td>
                <td><?=$bodywashcolor; ?></td>
                <td><strong>Remarks/Desc:</strong></td>
                <td colspan="5" style="word-wrap: break-word;word-break: break-all;"><?=$dataArray[0][csf('remarks')];?></td>
            </tr>
        </table>
        <br>
		
        <?
        $sample_names = array();

        $sample_color_arr=return_library_array( "select id, sample_color from sample_development_dtls", "id", "sample_color");
        $sql_qry="SELECT id, sample_mst_id, sample_name, gmts_item_id, smv, article_no, sample_color, sample_prod_qty, submission_qty, delv_start_date, delv_end_date, sample_charge,measurement_chart, sample_curency, sent_to_buyer_date, comments from sample_development_dtls where status_active=1 and is_deleted=0 and entry_form_id=449 and sample_mst_id in($req_id) order by id asc";

        $sql_qry_color="SELECT a.id, a.sample_mst_id, a.sample_name, a.gmts_item_id, a.smv, a.article_no, a.sample_color, a.sample_prod_qty, a.submission_qty, a.delv_start_date, a.delv_end_date, a.sample_charge,a.measurement_chart, a.sample_curency, a.sent_to_buyer_date, a.comments, c.dtls_id, c.size_id, c.bh_qty, c.self_qty, c.test_qty, c.plan_qty, c.dyeing_qty from sample_development_dtls a, sample_development_size c where a.id=c.dtls_id and a.status_active =1 and a.is_deleted=0 and a.entry_form_id=449 and a.sample_mst_id in($req_id) order by a.id asc";
        $size_type_arr=array(1=>"BH Qty",2=>"Self Qty",3=>"Test qty",4=>"Plan Qty",5=>"Wash Qty");
        $color_size_arr=array();
        foreach(sql_select($sql_qry_color) as $vals)
        {
            if($vals[csf("bh_qty")]>0)
            {
                $color_size_arr[1][$vals[csf("size_id")]]='Bh Qty';
                $bh_qty=$vals[csf("bh_qty")];
                $color_size_dtls_qty_arr[1][$vals[csf("id")]][$vals[csf("size_id")]]=$bh_qty;
            }
            if($vals[csf("self_qty")]>0)
            {
                $color_size_arr[2][$vals[csf("size_id")]]='Self Qty';
                $color_size_dtls_qty_arr[2][$vals[csf("id")]][$vals[csf("size_id")]]=$vals[csf("self_qty")];
            }
            if($vals[csf("test_qty")]>0)
            {
                $color_size_arr[3][$vals[csf("size_id")]]='Test Qty';
                $color_size_dtls_qty_arr[3][$vals[csf("id")]][$vals[csf("size_id")]]=$vals[csf("test_qty")];
            }
            if($vals[csf("plan_qty")]>0)
            {
                $color_size_arr[4][$vals[csf("size_id")]]='Plan Qty';
                $color_size_dtls_qty_arr[4][$vals[csf("id")]][$vals[csf("size_id")]]=$vals[csf("plan_qty")];
            }
            if($vals[csf("dyeing_qty")]>0)
            {
                $color_size_arr[5][$vals[csf("size_id")]]='Wash Qty';
                $color_size_dtls_qty_arr[5][$vals[csf("id")]][$vals[csf("size_id")]]=$vals[csf("dyeing_qty")];
            }
        }
        $tot_row=count($color_size_arr);
        $result=sql_select($sql_qry);
       
        ?>
        <div style="width: 1570px">
        	<div style="width: 1170px; float: left">
        		<table align="left" cellspacing="0" border="1" width="1170" class="rpt_table" rules="all">
		            <thead>
		                <tr>
		                    <td width="150" colspan="<? echo 12+$tot_row;?>" align="center"><strong>Sample Details</td>
		                </tr>
		                <tr>
		                    <th width="30" rowspan="2">SL</th>
		                    <th width="100" rowspan="2">Sample Name</th>
		                    <th width="120" rowspan="2">Garment Item</th>
		                    <th width="70" rowspan="2">Sample Delv.  Date</th>
		                    <th width="70" rowspan="2">Color</th>
		                        <?
		                        $tot_row_td=0;
		                        foreach($color_size_arr as $type_id=>$val)
		                        {
		                            ?>
		                            <th width="45" align="center" colspan="<?=count($val);?>"><?=$size_type_arr[$type_id];?></th>
		                            <?
		                        }
		                        ?>
		                    <th rowspan="2" width="55">Total</th>
		                    <th rowspan="2" width="55">Submission Qty</th>
		                    <th rowspan="2" width="70">Buyer Submission Date</th>
							<th rowspan="2" width="70" >M-Chart No</th>
							<th rowspan="2">Remarks</th>
		                </tr>
		                <tr>
		                    <?
		                    foreach($color_size_arr as $type_id=>$data_size)
		                    {
		                        foreach($data_size as $size_id=>$data_val)
		                        {
		                            $tot_row_td++;
		                            ?>
		                            <th width="40" align="center"><?=$size_library[$size_id];?></th>
		                            <?
		                        }
		                    }
		                    ?>
		                </tr>
		            </thead>
		            <tbody>
		                <?
		                $i=1; $k=0; $gr_tot_sum=0; $gr_sub_sum=0;
		                foreach($result as $row)
		                {
		                    $dtls_ids=$row[csf('id')];
		                    $prod_sum=$prod_sum+$row[csf('sample_prod_qty')];
		                    $sub_sum=$sub_sum+$row[csf('submission_qty')];
		                    $k++;
		                    $sample_names[$sample_library[$row[csf('sample_name')]]]=$sample_library[$row[csf('sample_name')]];
		                    ?>
		                    <tr>
		                        <td align="center"><?=$k;?></td>
		                        <td align="left"><?=$sample_library[$row[csf('sample_name')]];?></td>
		                        <td align="left"><?=$garments_item[$row[csf('gmts_item_id')]];?></td>
		                        <td align="left"><?=change_date_format($row[csf('delv_end_date')]);?></td>
		                        <td align="left"><?=$color_library[$row[csf('sample_color')]];?></td>
		                        <?
		                        $total_sizes_qty=0;  $total_sizes_qty_subm=0;
		                        foreach($color_size_arr as $type_id=>$data_size)
		                        {
		                            foreach($data_size as $size_id=>$data_val)
		                            {
		                                $size_qty=$color_size_dtls_qty_arr[$type_id][$dtls_ids][$size_id];
		                                ?>
		                                <td align="right"><?=$size_qty;?></td>
		                                <?
		                                if($type_id==1)
		                                {
		                                $total_sizes_qty_subm+=$size_qty;
		                                }
		                                $total_sizes_qty+=$size_qty;
		                            }
		                        }
		                        ?>
		                        <td align="right"><?=$total_sizes_qty;?></td>
		                        <td align="right"><?=$total_sizes_qty_subm;?></td>
		                        <td align="left"><?=change_date_format($row[csf('sent_to_buyer_date')]);?> </td>
								<td align="center"><?=$row[csf('measurement_chart')];?> </td>
		                        <td align="left"><?=$row[csf('comments')];?> </td>
		                    </tr>
		                    <?
		                    $gr_tot_sum+=$total_sizes_qty;
		                    $gr_sub_sum+=$total_sizes_qty_subm;
		                }
		                ?>
		                <tr>
		                    <td colspan="<?=5 + $tot_row_td;?>" align="right"><b>Total</b></td>
		                    <td align="right"><b><?=$gr_tot_sum;?> </b></td>
		                    <td align="right"><b><?=$gr_sub_sum;?> </b></td>
		                    <td colspan="2">&nbsp;</td>
							<td colspan="2">&nbsp;</td>
		                </tr>
		            </tbody>
		        </table>
		
        	</div>
        	<div style="width: 400px; float: left">
        		<? 
        			$image_arr = sql_select("select image_location,form_name  from common_photo_library  where master_tble_id in($req_id) and form_name in ('samplereqbackimage_1','samplereqfrontimage_1') and is_deleted=0 and file_type=1");
        			foreach ($image_arr as $row) {
        				if($row[csf('form_name')] == 'samplereqfrontimage_1')
        				{
        					$samplereqfrontimage = $row[csf('image_location')];
        				}
        				if($row[csf('form_name')] == 'samplereqbackimage_1')
        				{
        					$samplereqbackimage = $row[csf('image_location')];
        				}
        			}
        		?>
        		<table align="left" cellspacing="0" border="1" width="340" class="rpt_table" rules="all">
        		<tr>
        			<td width="170" align="center">Front Image</td>
        			<td width="170" align="center">Back Image</td>
        		</tr>
        		<tr>
        			<td><img width="170" height="200" src="<? echo $page_path.$samplereqfrontimage; ?>"</td>
        			<td><img width="170" height="200" src="<? echo $page_path.$samplereqbackimage; ?>"</td>

        		</tr>
        		</table>
        	</div>
        </div>

		<?
		// $sql_fab="SELECT a.sample_name, a.process_loss_percent, a.gmts_item_id, b.color_id, b.contrast, b.qnty, a.delivery_date, a.fabric_description, a.determination_id, a.body_part_id,a.body_part_type_id, a.fabric_source, a.remarks_ra, a.gsm, a.dia, a.color_type_id, a.width_dia_id, a.uom_id, b.process_loss_percent, a.weight_type, a.cuttable_width, b.grey_fab_qnty, b.fabric_color from sample_development_fabric_acc a, sample_development_rf_color b, wo_non_ord_samp_booking_dtls c where a.id=b.dtls_id and a.sample_mst_id=b.mst_id and a.id=c.dtls_id and c.fabric_color=b.fabric_color and c.gmts_color=b.color_id and c.dtls_id=b.dtls_id  and b.grey_fab_qnty=c.grey_fabric and a.form_type=1 and b.qnty>0 and c.grey_fabric>0 and a.status_active=1 and a.is_deleted=0  and a.sample_mst_id in($req_id) and b.mst_id in($req_id)  ";

        $sql_fab="SELECT a.sample_name, a.process_loss_percent, a.gmts_item_id, b.color_id, b.contrast, b.qnty, a.delivery_date, a.fabric_description, a.determination_id, a.body_part_id,a.body_part_type_id, a.fabric_source, a.remarks_ra, a.gsm, a.dia, a.color_type_id, a.width_dia_id, a.uom_id, b.process_loss_percent, a.weight_type, a.cuttable_width, b.grey_fab_qnty, b.fabric_color from sample_development_fabric_acc a, sample_development_rf_color b where a.id=b.dtls_id and a.sample_mst_id=b.mst_id and a.form_type=1 and b.qnty>0 and a.status_active=1 and a.is_deleted=0  and a.sample_mst_id in($req_id) and b.mst_id in($req_id)  ";
		// and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
         //echo $sql_fab; 
        $sql_fab_arr=array(); $determination_id='';
        foreach(sql_select($sql_fab) as $vals)
        {
            $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("contrast")]]["qnty"]+=$vals[csf("qnty")];
			
            $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("contrast")]]["exdata"]=change_date_format($vals[csf("delivery_date")]).'__'.$vals[csf("fabric_source")].'__'.$vals[csf("uom_id")].'__'.$vals[csf("width_dia_id")].'__'.$vals[csf("remarks_ra")].'__'.$vals[csf("color_type_id")].'__'.$vals[csf("weight_type")].'__'.$vals[csf("cuttable_width")].'__'.$vals[csf("determination_id")];

            $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("contrast")]]["grey_fab_qnty"]+=$vals[csf("grey_fab_qnty")];
			if($determination_id=="") $determination_id=$vals[csf("determination_id")]; else $determination_id.=','.$vals[csf("determination_id")];
        }
        $sample_item_wise_span=array();
		
		$sqlRd="select id, fabric_ref, rd_no from lib_yarn_count_determina_mst where status_active=1 and is_deleted=0 and id in ($determination_id)";
		$sqlRdData=sql_select($sqlRd); $rdRefArr=array();
		foreach($sqlRdData as $row)
		{
			$rdRefArr[$row[csf("id")]]['ref']=$row[csf("fabric_ref")];
			$rdRefArr[$row[csf("id")]]['rd_no']=$row[csf("rd_no")];
		}
		
        /*echo '<pre>';
        print_r($sql_fab_arr); die;*/

        foreach($sql_fab_arr as $sample_type=>$colorType_data)
        {
            foreach($colorType_data as $colorType=>$gmts_color_data)
            {

                foreach($gmts_color_data as $gmts_color_id=>$body_part_data)
                {
                	$sample_span=0;
                    foreach($body_part_data as $body_part_id=>$fab_desc_data)
                    {
                        //$kk=0;
                        foreach($fab_desc_data as $fab_id=>$gsm_data)
                        {
                            foreach($gsm_data as $gsm_id=>$dia_data)
                            {
                                foreach($dia_data as $dia_id=>$color_data)
                                {
                                    foreach($color_data as $contrast_id=>$row)
                                    {
                                        $sample_span++;
                                        //$kk++;
                                    }
                                }
                            }
                        }
                        //$bodypart_item_wise_span[$sample_type][$gmts_item_id][$body_part_id]=$kk;
                    }
                    $sample_item_wise_span[$sample_type][$gmts_color_id]=$sample_span;
                }
            }
        }
 /*        echo "<pre>";
        print_r($sample_item_wise_span);die;*/

        $sql_sample_dtls= "SELECT a.sample_name, a.article_no, a.sample_color from sample_development_dtls a, lib_color b where a.status_active=1 and a.is_deleted=0 and a.entry_form_id=449  and sample_mst_id in($req_id) and b.status_active=1 and b.id=a.sample_color  group by a.sample_name, a.article_no, a.sample_color";
        foreach(sql_select($sql_sample_dtls) as $key=>$value)
        {
            if($sample_wise_article_no[$value[csf("sample_name")]][$value[csf("sample_color")]]=="")
            {
                $sample_wise_article_no[$value[csf("sample_name")]][$value[csf("sample_color")]]=$value[csf("article_no")];
            }
            else
            {
                if(!in_array($value[csf("article_no")], $sample_wise_article_no))
                {
                    $sample_wise_article_no[$value[csf("sample_name")]][$value[csf("sample_color")]].= ', '.$value[csf("article_no")];
                }
            }
        }
        // echo "<pre>"; print_r($sample_wise_article_no);die;

        ?>
        <table class="rpt_table" width="1555"  border="1" cellpadding="0" cellspacing="0" rules="all">
            <thead>
                <tr>
                    <th colspan="20">Required Fabric</th>
                </tr>
                <tr>
                    <th width="30">SL</th>
                    <th width="110">Sample Type</th>
                    <th width="80">Fab. Deli Date</th>
                    <th width="40">Fabric Source</th>
                    <th width="120">Body Part</th>
					<th width="100">Body Part Yype</th>
                    <th width="80">RD No</th>
                    <th width="80">Ref. No</th>
                    <th width="200">Fabric Desc & Composition</th>
                    <th width="80">Color Type</th>
                    <th width="80">Gmt. Color</th>
                    <th width="80">Fab. Color</th>
                    <th width="55">Fabric Weight</th>
                    <th width="55">F.Weight Type</th>
                    <th width="60">Full Width</th>
                    
                    <th width="55">Cuttable Width</th>
                    <th width="55">Width Type</th>
                    
                    <th width="40">UOM</th>
                    <th width="60">Fin Fabric Qty</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                <?
                $p=1; $total_finish=0; 
                foreach($sql_fab_arr as $sample_type=>$colorType_data)
                {
                    foreach($colorType_data as $colorType=>$gmts_color_data)
                    {
                        foreach($gmts_color_data as $gmts_color_id=>$body_part_data)
                        {
                            $nn=0;
                            foreach($body_part_data as $body_part_id=>$fab_desc_data)
                            {
                                foreach($fab_desc_data as $fab_id=>$gsm_data)
                                {
                                    foreach($gsm_data as $gsm_id=>$dia_data)
                                    {
                                        foreach($dia_data as $dia_id=>$color_data)
                                        {
                                            //$i=0;
                                            foreach($color_data as $contrast_id=>$value)
                                            {
												$exData=explode("__",$value["exdata"]);
												$delivery_date=$fabricSource=$uom_id=$width_dia_id=$remarks_ra=$color_type_id=$weight_type=$cuttable_width=$determination_id='';
												
												$delivery_date=$exData[0];
												$fabricSource=$exData[1];
												$uom_id=$exData[2];
												$width_dia_id=$exData[3];
												$remarks_ra=$exData[4];
												$color_type_id=$exData[5];
												$weight_type=$exData[6];
												$cuttable_width=$exData[7];
												$determination_id=$exData[8];
												
                                                ?>
                                                <tr>
                                                    <td align="center" style="word-wrap: break-word;word-break: break-all;"><?=$p;$p++;?></td>
                                                    <?
                                                   /* if($nn==0)
                                                    {*/
                                                        $rowspan=0;
                                                        //$rowspan=$sample_item_wise_span[$sample_type][$gmts_color_id];
                                                        ?>
                                                        <td rowspan="<?=$rowspan;?>" align="center"><?=$sample_library[$sample_type];?></td>
                                                        
                                                        <?
                                                        $nn++;
                                                    /*}*/
                                                    ?>
                                                    <td align="center"><?=$delivery_date; ?> </td>
                                                    <td style="word-break:break-all"><?=$fabric_source[$fabricSource]; ?></td>
                                                    <td style="word-break:break-all"><?=$body_part[$body_part_id]; ?></td>
													<td style="word-break:break-all"><?=$body_part_type[$body_part_type_id]; ?></td>
                                                    <td style="word-break:break-all"><?=$rdRefArr[$determination_id]['rd_no']; ?></td>
                                                    <td style="word-break:break-all"><?=$rdRefArr[$determination_id]['ref']; ?></td>
                                                    <td style="word-break:break-all"><?=$fab_id;?></td>
                                                    
                                                    <td style="word-break:break-all"><?=$color_type[$colorType]; ?></td>
                                                    <td style="word-break:break-all"><?=$color_library[$gmts_color_id]; ?></td>
                                                    <td style="word-break:break-all"><?=$contrast_id; ?></td>
                                                    <td style="word-break:break-all"><?=$gsm_id; ?></td>
                                                    <td style="word-break:break-all"><?=$fabric_weight_type[$weight_type]; ?></td>
                                                    <td style="word-break:break-all"><?=$dia_id; ?></td>
                                                    <td style="word-break:break-all"><?=$cuttable_width; ?></td>
                                                    <td style="word-break:break-all"><?=$fabric_typee[$width_dia_id]; ?></td>
                                                    <td style="word-break:break-all"><?=$unit_of_measurement[$uom_id]; ?></td>
                                                    <td align="right"><?=number_format($value["qnty"], 2); ?></td>
                                                    <td style="word-break:break-all"><?=$remarks_ra; ?></td>
                                                </tr>
                                                <?
                                                //$i++;
                                                $total_finish +=$value["qnty"];
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                ?>
                <tr>
                    <th colspan="17" align="right"><b>Total</b></th>
                    <th align="right"><?=number_format($total_finish, 2); ?></th>
                    <th>&nbsp;</th>
                </tr>
            </tbody>
        </table>
        <br/>
		      
		        <table align="left" cellspacing="0" border="1" width="1455" class="rpt_table" rules="all">
		            <thead>
		                <tr>
		                    <td colspan="10" align="center"><strong>Required Accessories</td>
		                </tr>
		                <tr>
		                    <th width="30">Sl</th>
		                    <th width="100">Sample Name</th>
		                    <th width="120">Garment Item</th>
		                    <th width="100">Trims Group</th>
		                    <th width="100">Description</th>
		                    <th width="100">Supplier</th>
		                    <th width="100">Brand/Supp.Ref</th>
		                    <th width="30">UOM</th>
		                    <th width="30">Req/Dzn</th>
		                    <th width="30">Req/Qty</th>
		                    <th width="80">Acc. Source</th>
		                    <th width="100">Acc Delivery Date</th>
		                    <th width="150">Remarks </th>
		                </tr>
		            </thead>
		            <tbody>
						<?
		                $sql_qryA="SELECT id, sample_mst_id, sample_name_ra, gmts_item_id_ra, trims_group_ra, description_ra, brand_ref_ra, uom_id_ra, req_dzn_ra, req_qty_ra, remarks_ra, delivery_date, supplier_id, nominated_supp_multi, fabric_source from sample_development_fabric_acc where status_active =1 and is_deleted=0 and form_type=2 and sample_mst_id in($req_id) order by id asc";

		                $resultA=sql_select($sql_qryA);
		                $i=1;$k=0; $req_dzn_ra=0; $req_qty_ra=0;
		                foreach($resultA as $rowA)
		                {
							$req_dzn_ra=$req_dzn_ra+$rowA[csf('req_dzn_ra')];
							$req_qty_ra=$req_qty_ra+$rowA[csf('req_qty_ra')];
							
							$nominated_supp_str="";
							 $exnominated_supp=explode(",",$rowA[csf('nominated_supp_multi')]);
							 foreach($exnominated_supp as $supp)
							 {
								if($nominated_supp_str=="") $nominated_supp_str=$supplier_library[$supp]; else $nominated_supp_str.=','.$supplier_library[$supp];
							 }
							$k++;
							?>
							<tr>
		                        <td align="center"><? echo $k;?></td>
		                        <td style="word-break:break-all"><? echo $sample_library[$rowA[csf('sample_name_ra')]];?></td>
		                        <td style="word-break:break-all"><? echo $garments_item[$rowA[csf('gmts_item_id_ra')]];?></td>
		                        <td style="word-break:break-all"><? echo $trims_group_lib[$rowA[csf('trims_group_ra')]];?></td>
		                        <td style="word-break:break-all"><? echo $rowA[csf('description_ra')];?></td>
		                        <td style="word-break:break-all"><?=$nominated_supp_str;?></td>
		                        <td align="left"><? echo $rowA[csf('brand_ref_ra')];?></td>
		                        <td align="center"><? echo $unit_of_measurement[$rowA[csf('uom_id_ra')]];?></td>
		                        <td align="right"><? echo $rowA[csf('req_dzn_ra')];?></td>
		                        <td align="right"><? echo $rowA[csf('req_qty_ra')];?></td>
		                        <td align="left"><? echo $fabric_source[$rowA[csf('fabric_source')]];?></td>
		                        <td align="left"><? echo change_date_format($rowA[csf('delivery_date')]);?></td>
		                        <td align="left"><? echo $rowA[csf('remarks_ra')];?></td>
							</tr>
							<?
		                }
		                ?>
		                <tr>
		                    <td colspan="9" align="center"><b>Total </b></td>
		                    <td align="right"><b><? echo number_format($req_qty_ra,2);?> </b></td>
		                    <td>&nbsp;</td>
		                </tr>
		            </tbody>
		        </table>
        
      	  <br>
        
			<?
			$sqlEmbl="SELECT name_re from sample_development_fabric_acc where sample_mst_id in($req_id) and form_type=3 and is_deleted=0  and status_active=1 group by name_re order by name_re DESC";
			$sqlEmblData=sql_select($sqlEmbl);
			
			foreach($sqlEmblData as $erow)
			{
				$embNameId=$erow[csf('name_re')];
			?>
			  <br>&nbsp;
			<table align="left" cellspacing="0" border="1" width="1455" class="rpt_table" rules="all">
				<thead>
					<tr>
						<td colspan="8" align="center" width="100"><strong>Required <?=$emblishment_name_array[$erow[csf('name_re')]]; ?></td>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="100">Sample Name</th>
						<th width="110">Garment Item</th>
						<th width="110">Body Part</th>
						<th width="110">Body Part Type</th>
						<th width="100">Supplier</th>
						<th width="70"><?=$emblishment_name_array[$erow[csf('name_re')]]; ?> Type</th>
						<th width="100"><?=$emblishment_name_array[$erow[csf('name_re')]]; ?> Del.Date</th>
						<th width="150">Remarks</th>
					</tr>
				</thead>
				<tbody>
					<?
					$sql_qry="SELECT id, sample_mst_id, sample_name_re, gmts_item_id_re, name_re, type_re, remarks_re, body_part_id,body_part_type_id, delivery_date, supplier_id from sample_development_fabric_acc where sample_mst_id in($req_id) and form_type=3 and is_deleted=0 and name_re='$embNameId' and status_active=1 order by id asc";
					//echo $sql_qry;die;

					$result=sql_select($sql_qry); $k=0;
				// $type_array=array(1=>$emblishment_print_type,2=>$emblishment_embroy_type,3=>$emblishment_wash_type,4=>$emblishment_spwork_type,5=>$emblishment_gmts_type);
					foreach($result as $row)
					{
						$k++;
						?>
						<tr>
							<td align="center"><? echo $k;?></td>
							<td align="left"><? echo $sample_library[$row[csf('sample_name_re')]];?></td>
							<td align="left"><? echo $garments_item[$row[csf('gmts_item_id_re')]];?></td>
							<td align="left"><? echo $body_part[$row[csf('body_part_id')]];?></td>
							<td align="left"><? echo $body_part_type[$row[csf('body_part_type_id')]];?></td>
							<td align="left"><? echo $supplier_library[$row[csf('supplier_id')]];?></td>
							<td align="left">
								<?
								if($row[csf('name_re')]==1) echo $emblishment_print_type[$row[csf('type_re')]];
								if($row[csf('name_re')]==2) echo $emblishment_embroy_type[$row[csf('type_re')]];
								if($row[csf('name_re')]==3) echo $emblishment_wash_type[$row[csf('type_re')]];
								if($row[csf('name_re')]==4) echo $emblishment_spwork_type[$row[csf('type_re')]];
								if($row[csf('name_re')]==5) echo $emblishment_gmts_type[$row[csf('type_re')]];
								?>
							</td>
							<td align="left"><? echo change_date_format($row[csf('delivery_date')]);?></td>
							<td align="left"><? echo $row[csf('remarks_re')];?></td>
						</tr>
						<?
					}
					?>
				</tbody>
			</table>
			
			<br>
        <?
		}
		?>
          
               	
        <table style="margin-top:10px;" class="rpt_table" width="600" align="left" border="1" cellpadding="0" cellspacing="0" rules="all">
            <thead>
                <tr>
                    <th width="40">Sl</th>
                    <th>Special Instruction</th>
                </tr>
            </thead>
            <tbody>
				<?
                $data_array=sql_select("select id, terms from  wo_booking_terms_condition where entry_form=140 and booking_no='$data[2]'");
                if(count($data_array)>0)
                {
					$l=1;
					foreach( $data_array as $key=>$row )
					{
						?>
						<tr>
                            <td><? echo $l;?> </td>
                            <td style="word-break:break-all"><? echo $row[csf("terms")]; ?> </td>
						</tr>
						<?
						$l++;
					}
                }
                ?>
            </tbody>
        </table>
        </br>
         <div style="clear:both;"></div>
         <table style="margin-top:10px;" class="rpt_table" width="680" align="left" border="1" cellpadding="0" cellspacing="0" rules="all">
            <thead>
                <tr>
                    <th width="40">Sl</th>
                    <th width="140">Sample Type</th>
                    <th width="80">Pattern Date</th>
                    <th width="80">Cutting Date</th>
                    <th width="80">Sewing Date</th>
                    <th width="80">Wash Send Date</th>
                    <th width="80">Wash Receive Date</th>
                    <th >Finishing Date</th>
                </tr>
            </thead>
            <tbody>
				<?
				$sql_smaple_plan = "select sample_plan from sample_requisition_acknowledge where entry_form = 54 and sample_mst_id  in($req_id)";
                $data_array_sample_plan=sql_select($sql_smaple_plan);
                if(count($data_array_sample_plan)>0)
                {
					$l=1;
					foreach( $data_array_sample_plan as $key=>$row )
					{

						$sample_plan = explode("**",$row[csf('sample_plan')])
						?>
						<tr>
                            <td><? echo $l;?> </td>
                            <td style="word-break:break-all"><? echo implode(",", $sample_names) ?> </td>
                            <td style="word-break:break-all"><? if(!empty($sample_plan[0])) echo change_date_format($sample_plan[0]); ?> </td>
                            <td style="word-break:break-all"><? if(!empty($sample_plan[1])) echo change_date_format($sample_plan[1]); ?> </td>
                            <td style="word-break:break-all"><? if(!empty($sample_plan[2])) echo change_date_format($sample_plan[2]); ?> </td>
                            <td style="word-break:break-all"><? if(!empty($sample_plan[3])) echo change_date_format($sample_plan[3]); ?> </td>
                            <td style="word-break:break-all"><? if(!empty($sample_plan[4])) echo change_date_format($sample_plan[4]); ?> </td>
                            <td style="word-break:break-all"><? if(!empty($sample_plan[5])) echo change_date_format($sample_plan[5]); ?> </td>
						</tr>
						<?
						$l++;
					}
                }
                ?>
            </tbody>
        </table>
        </br>
        <? echo signature_table(207, $data[0], "930px",$cbo_template_id); ?>
    </div>
   
	<?

$message=ob_get_contents();
ob_clean();

if($approval_type==1){
    $mail_item=42;
    $subject="Sample Requisition Acknowledge";
}
else
{
    $mail_item=43;
    $subject="Sample Requisition Unacknowledge";
}



        $to='';
        $sql = "SELECT a.BRAND_IDS,a.BUYER_IDS,c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id  and a.mail_item=b.MAIL_ITEM_MST and a.mail_item=$mail_item and b.mail_user_setup_id=c.id and a.company_id=$company_id and   A.IS_DELETED=0 and A.STATUS_ACTIVE=1  and b.IS_DELETED=0 and b.STATUS_ACTIVE=1  and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
       // echo $sql;die;

        $mail_sql=sql_select($sql);
        $receverMailArr=array();
        foreach($mail_sql as $row)
        {
            $buyerArr=explode(',',$row['BUYER_IDS']);
            $brandArr=explode(',',$row['BRAND_IDS']);

            if(count($buyerArr)){
                foreach($buyerArr as $buyerid){
                    //foreach($brandArr as $brandid){
                        $brandid=$brandid*1;
                        $receverMailArr[$buyerid][$brandid][$row[csf('email_address')]]=$row[csf('email_address')];
                    //}
                }
            }
            else{
                $receverMailArr[$dataArray[0][csf('buyer_name')]][0][$row[csf('email_address')]]=$row[csf('email_address')];
            }
            
        }

 

        $to=implode(',',$receverMailArr[$dataArray[0][csf('buyer_name')]][0]);
        $header=mailHeader();


        if($to!=""){echo sendMailMailer( $to, $subject, $message, $from_mail,$att_file_arr );}


    exit();
}
}



?>