<?php
date_default_timezone_set("Asia/Dhaka");

require_once('../../includes/common.php');
require_once('../../mailer/class.phpmailer.php');
require_once('../setting/mail_setting.php');
 
extract($_REQUEST);

//localhost/platform-v3.5/auto_mail/woven/sample_requisition_with_booking_auto_mail.php?sys_id=UG-20-00020
 
//$company_library = return_library_array( "select id, COMPANY_NAME from lib_company where status_active=1 and is_deleted=0", "id", "COMPANY_NAME",$con);
//$lib_buyer = return_library_array( "SELECT ID,BUYER_NAME FROM LIB_BUYER WHERE IS_DELETED=0 AND STATUS_ACTIVE=1", "ID", "BUYER_NAME");
//$brandArr = return_library_array("select id,brand_name from  lib_buyer_brand ","id","brand_name");
//$season_lib = return_library_array("select id,season_name from lib_buyer_season","id","season_name");
	
	if($db_type==0)
	{
		$current_date = date("Y-m-d H:i:s",strtotime(add_time(date("H:i:s",time()),0)));
		$previous_date = date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))); 
	}
	else
	{
		$current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("H:i:s",time()),0))),'','',1);
		$previous_date = change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))),'','',1);
	}
	

	 
	


/*	$imgSql="select FILE_TYPE,IMAGE_LOCATION,REAL_FILE_NAME, MASTER_TBLE_ID, FORM_NAME from common_photo_library where form_name in('quotation_inquery') and is_deleted=0  ".where_con_using_array(array($mstRow[ID]),1,'MASTER_TBLE_ID')."";
	$imgSqlResult=sql_select($imgSql);
	foreach($imgSqlResult as $rows){
		$att_file_arr[]='../'.$rows[IMAGE_LOCATION].'**'.$rows[REAL_FILE_NAME];
	}*/
	//echo $imgSql;
		
			
			
			$sysSql = "select ID, COMPANY_ID,REQUISITION_NUMBER,BUYER_NAME,BRAND_ID  from sample_development_mst where  entry_form_id=449 and is_deleted=0 and status_active=1  AND REQUISITION_NUMBER = '".$sys_id."'";
			//echo $sysSql;die;
			foreach(sql_select($sysSql) as $vals)
			{
				$company_id=$vals[COMPANY_ID];
				$brand_id=$vals[BRAND_ID];
				$buyer_id=$vals[BUYER_NAME];
				
				$sys_id=$vals[REQUISITION_NUMBER];
				$company_id=$vals[COMPANY_ID];
				$mst_id=$vals[ID];
			}
			$is_booking = sql_select("SELECT booking_no from wo_non_ord_samp_booking_dtls where style_id=$mst_id and status_active=1 and is_deleted=0 and entry_form_id=140 group by booking_no  ");
			$booking_no=$is_booking[0][csf('booking_no')];

        	//echo $mst_id;die;

			
            //$dealing_merchant_library=return_library_array( "select id,team_member_name from lib_mkt_team_member_info", "id", "team_member_name");
            //$team_leader_arr=return_library_array( "select id,team_leader_name from lib_marketing_team", "id", "team_leader_name");
			
           $team_leader_result_arr=sql_select( "select ID,TEAM_LEADER_NAME,TEAM_LEADER_EMAIL from lib_marketing_team where STATUS_ACTIVE=1 and IS_DELETED=0");
		   foreach($team_leader_result_arr as $row){
			  $team_leader_arr[$row[ID]] =$row[TEAM_LEADER_NAME];
			  $team_leader_mail_arr[$row[ID]] =$row[TEAM_LEADER_EMAIL];
		   }
			
           $dealing_merchant_result_library=sql_select( "select ID,TEAM_MEMBER_NAME,TEAM_MEMBER_EMAIL from lib_mkt_team_member_info where STATUS_ACTIVE=1 and IS_DELETED=0");
		   foreach($dealing_merchant_result_library as $row){
			  $dealing_merchant_library[$row[ID]] =$row[TEAM_MEMBER_NAME];
			  $dealing_merchant_mail_library[$row[ID]] =$row[TEAM_MEMBER_EMAIL];
		   }



            $company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
            $supplier_library=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name");
            $company_img=sql_select("select image_location  from common_photo_library  where master_tble_id='$company_id' and form_name='company_details' and is_deleted=0 and file_type=1");
            $buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
        
            $sample_library=return_library_array( "select id, sample_name from lib_sample", "id", "sample_name");
            $size_library=return_library_array( "select id, size_name from lib_size where status_active =1 and is_deleted=0 ", "id", "size_name");
            $color_library=return_library_array( "select id, color_name from lib_color where status_active =1 and is_deleted=0 ", "id", "color_name");
            $season_arr=return_library_array( "select id,season_name from lib_buyer_season  where status_active =1 and is_deleted=0 ", "id", "season_name");
            $brandArr=return_library_array( "select id, brand_name from lib_buyer_brand", "id", "brand_name");
            $trims_group_lib=return_library_array( "select id,item_name from lib_item_group  where status_active =1 and is_deleted=0", "id", "item_name");
            //concate(buyer_name,'_',contact_person)
            $appDate=return_field_value("approved_date","approval_history","entry_form=25 and mst_id='$mst_id' order by id desc");
            $appBy=return_field_value("approved_by","approval_history","entry_form=25 and mst_id='$mst_id'");
            $imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='sample_details_1' and file_type=1",'master_tble_id','image_location');
         
		 
            $user_arr=return_library_array( "select id, user_name from user_passwd", "id", "user_name");
		 
           $user_result_library=sql_select( "select id, USER_NAME,USER_EMAIL from user_passwd where STATUS_ACTIVE=1 and IS_DELETED=0");
		   foreach($user_result_library as $row){
			  $user_arr[$row[ID]] =$row[user_name];
			  $user_mail_arr[$row[ID]] =$row[USER_EMAIL];
		   }

		 
		 ob_start();
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
                        <td colspan="4" style="font-size:20px;"><strong><b><? echo $company_library[$company_id]; ?></b></strong></td>
                        <td width="200">
                            <?
                            $nameArray_approved=sql_select( "SELECT approved_by,approved_date,a.TEAM_LEADER,a.DEALING_MARCHANT from wo_non_ord_samp_booking_mst a, approval_history b where a.id=b.mst_id and a.booking_no='$booking_no' and b.entry_form=9 and a.status_active =1 and a.is_deleted=0 order by b.id desc ");
                            $approved_by= $user_arr[$nameArray_approved[0][csf("approved_by")]];
                            $approved_date= change_date_format($nameArray_approved[0][csf("approved_date")]);
                            
							
							
							?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5">
                            <?
                            $val=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$company_id");
                            echo ($val[0][csf('plot_no')])?   $val[0][csf('plot_no')].',': "";
                            echo ($val[0][csf('level_no')])?  $val[0][csf('level_no')].',': "";
                            echo ($val[0][csf('road_no')])?   $val[0][csf('road_no')].',': "";
                            echo ($val[0][csf('block_no')])?  $val[0][csf('block_no')].',': "";
                            echo ($val[0][csf('city')])?      $val[0][csf('city')].',': "";
                            echo ($val[0][csf('zip_code')])?  $val[0][csf('zip_code')].',': "";
                            echo ($val[0][csf('province')])?  $val[0][csf('province')].',': "";
                            echo($val[0][csf('country_id')])? $country_arr[$val[0][csf('country_id')]]: "";
                            echo ($val[0][csf('email')])?    "</br>". $val[0][csf('email')].',': "</br>";
                            echo($val[0][csf('website')])?    $val[0][csf('website')]: "";
        
                            $sql="SELECT id, requisition_number, requisition_number_prefix_num, style_ref_no, buyer_name, product_dept, dealing_marchant, agent_name, buyer_ref, bh_merchant, estimated_shipdate, team_leader, season_buyer_wise, remarks, quotation_id, sample_stage_id, requisition_date, material_delivery_date, season_year, brand_id,INSERTED_BY from sample_development_mst where  id='$mst_id' and entry_form_id=449 and  is_deleted=0  and status_active=1";
                            $dataArray=sql_select($sql);
                            $barcode_no=$dataArray[0][csf('requisition_number')];
                            if($dataArray[0][csf("sample_stage_id")]==1)
                            {
                                $job_lib=return_library_array( "SELECT a.id,min(b.shipment_date) as shipment_date  from wo_po_details_master  a,wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and a.company_name='$company_id' GROUP BY a.id", "id", "shipment_date"  );
                            }
                            $sqls="SELECT style_desc, supplier_id, revised_no, buyer_req_no, source, booking_date, attention,TEAM_LEADER,DEALING_MARCHANT from wo_non_ord_samp_booking_mst where booking_no='$booking_no' and is_deleted=0 and status_active=1";
                            $dataArray_book=sql_select($sqls);
							
							$TEAM_LEADER=$dataArray_book[0][TEAM_LEADER];
							$DEALING_MARCHANT=$dataArray_book[0][DEALING_MARCHANT];

                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" style="font-size:medium"><strong style="font-size:18px"> <u>Sample Program Without Order</u></strong></td>
                        <td colspan="2" width="250"><b>Approved By :<?=$approved_by ?></b> </br><b>Approved Date :<?=$approved_date ?></b> </td>
                    </tr>
                </table>
        
                <table width="1100" cellspacing="0" border="0" class="rpt_table" style="font-family: Arial Narrow;margin-left: 20px;" >
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
                        <td><?=$booking_no;?></td>
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
                        <td><?=$team_leader_arr[$dataArray[0][csf('team_leader')]];?></td>
                        <td><strong>Dealing Merchant:</strong></td>
                        <td><?=$dealing_merchant_library[$dataArray[0][csf('dealing_marchant')]];?></td>
                    </tr>
                    <tr>
                        <td><strong>Remarks/Desc:</strong></td>
                        <td colspan="7" style="word-wrap: break-word;word-break: break-all;"><?=$dataArray[0][csf('remarks')];?></td>
                    </tr>
                </table>
                <br>
                <?
                $sql_fab="SELECT a.sample_name, a.process_loss_percent, a.gmts_item_id, b.color_id, b.contrast, b.qnty, a.delivery_date, a.fabric_description, a.determination_id, a.body_part_id, a.fabric_source, a.remarks_ra, a.gsm, a.dia, a.color_type_id, a.width_dia_id, a.uom_id, b.process_loss_percent, a.weight_type, a.cuttable_width, b.grey_fab_qnty, b.fabric_color from sample_development_fabric_acc a, sample_development_rf_color b, wo_non_ord_samp_booking_dtls c where a.id=b.dtls_id and a.sample_mst_id=b.mst_id and a.id=c.dtls_id and c.fabric_color=b.fabric_color and c.gmts_color=b.color_id and c.dtls_id=b.dtls_id  and b.grey_fab_qnty=c.grey_fabric and a.form_type=1 and b.qnty>0 and c.grey_fabric>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.sample_mst_id='$mst_id' and b.mst_id='$mst_id'  ";
                //echo $sql_fab; die;
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
        
                $sql_sample_dtls= "SELECT a.sample_name, a.article_no, a.sample_color from sample_development_dtls a, lib_color b where a.status_active=1 and a.is_deleted=0 and a.entry_form_id=449  and sample_mst_id='$mst_id' and b.status_active=1 and b.id=a.sample_color  group by a.sample_name, a.article_no, a.sample_color";
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
                <table class="rpt_table" width="1460"  border="1" cellpadding="0" cellspacing="0" rules="all">
                    <thead>
                        <tr>
                            <th colspan="19">Required Fabric</th>
                        </tr>
                        <tr>
                            <th width="30">SL</th>
                            <th width="110">Sample Type</th>
                            <th width="80">Fab. Deli Date</th>
                            <th width="40">Fabric Source</th>
                            <th width="120">Body Part</th>
                            <th width="80">RD No</th>
                            <th width="80">Ref. No</th>
                            <th width="200">Fabric Desc & Composition</th>
                            <th width="80">Color Type</th>
                            <th width="80">Gmt. Color</th>
                            <th width="80">Fab. Color</th>
                            <th width="55">Fabric Weight</th>
                            <th width="55">F.Weight Type</th>
                            <th width="60">Width</th>
                            
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
                <?
                $sample_color_arr=return_library_array( "select id, sample_color from sample_development_dtls", "id", "sample_color");
                $sql_qry="SELECT id, sample_mst_id, sample_name, gmts_item_id, smv, article_no, sample_color, sample_prod_qty, submission_qty, delv_start_date, delv_end_date, sample_charge, sample_curency, sent_to_buyer_date, comments from sample_development_dtls where status_active=1 and is_deleted=0 and entry_form_id=449 and sample_mst_id='$mst_id' order by id asc";
        
                $sql_qry_color="SELECT a.id, a.sample_mst_id, a.sample_name, a.gmts_item_id, a.smv, a.article_no, a.sample_color, a.sample_prod_qty, a.submission_qty, a.delv_start_date, a.delv_end_date, a.sample_charge, a.sample_curency, a.sent_to_buyer_date, a.comments, c.dtls_id, c.size_id, c.bh_qty, c.self_qty, c.test_qty, c.plan_qty, c.dyeing_qty from sample_development_dtls a, sample_development_size c where a.id=c.dtls_id and a.status_active =1 and a.is_deleted=0 and a.entry_form_id=449 and a.sample_mst_id='$mst_id' order by a.id asc";
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
                <div style="width: 1500px">
                    <div style="width: 1100px; float: left">
                        <table align="left" cellspacing="0" border="1" width="1100" class="rpt_table" rules="all">
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
                                    <th rowspan="2" width="55">Submn Qty</th>
                                    <th rowspan="2"  width="70">Buyer Submisstion Date</th>
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
                                </tr>
                            </tbody>
                        </table>
                        <br>&nbsp;
                        <table align="left" cellspacing="0" border="1" width="1100" class="rpt_table" rules="all">
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
                                    <th>Remarks </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?
                                $sql_qryA="SELECT id, sample_mst_id, sample_name_ra, gmts_item_id_ra, trims_group_ra, description_ra, brand_ref_ra, uom_id_ra, req_dzn_ra, req_qty_ra, remarks_ra, delivery_date, supplier_id, nominated_supp_multi, fabric_source from sample_development_fabric_acc where status_active =1 and is_deleted=0 and form_type=2 and sample_mst_id='$mst_id' order by id asc";
        
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
                                    <td colspan="8" align="center"><b>Total </b></td>
                                    <td align="right"><b><? echo number_format($req_qty_ra,2);?> </b></td>
                                    <td>&nbsp;</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div style="width: 400px; float: left">
                        <? 
                            $image_arr = sql_select("select image_location,form_name  from common_photo_library  where master_tble_id='$mst_id' and form_name in ('samplereqbackimage_1','samplereqfrontimage_1') and is_deleted=0 and file_type=1");
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
                            <td><img width="170" height="300" src="../../<? echo $samplereqfrontimage; ?>"</td>
                            <td><img width="170" height="300" src="../../<? echo $samplereqbackimage; ?>"</td>
        
                        </tr>
                        </table>
                    </div>
                </div>
                
                <br>
                
                <?
                $sqlEmbl="SELECT name_re from sample_development_fabric_acc where sample_mst_id='$mst_id' and form_type=3 and is_deleted=0  and status_active=1 group by name_re order by name_re DESC";
                $sqlEmblData=sql_select($sqlEmbl);
                
                foreach($sqlEmblData as $erow)
                {
                    $embNameId=$erow[csf('name_re')];
                ?>
                <table align="left" cellspacing="0" border="1" width="740px" class="rpt_table" rules="all">
                    <thead>
                        <tr>
                            <td colspan="8" align="center"><strong>Required <?=$emblishment_name_array[$erow[csf('name_re')]]; ?></td>
                        </tr>
                        <tr>
                            <th width="30">Sl</th>
                            <th width="100">Sample Name</th>
                            <th width="110">Garment Item</th>
                            <th width="110">Body Part</th>
                            <th width="100">Supplier</th>
                            <th width="70"><?=$emblishment_name_array[$erow[csf('name_re')]]; ?> Type</th>
                            <th width="100"><?=$emblishment_name_array[$erow[csf('name_re')]]; ?> Del.Date</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?
                        $sql_qry="SELECT id, sample_mst_id, sample_name_re, gmts_item_id_re, name_re, type_re, remarks_re, body_part_id, delivery_date, supplier_id from sample_development_fabric_acc where sample_mst_id='$mst_id' and form_type=3 and is_deleted=0 and name_re='$embNameId' and status_active=1 order by id asc";
        
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
                        $data_array=sql_select("select id, terms from  wo_booking_terms_condition where entry_form=140 and booking_no='$booking_no'");
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
                <? //echo signature_table(207, $company_id, "930px",$cbo_template_id); ?>
            </div>
           
            <?
            
        
        
        

	   
		//$mstRow[BRAND_ID]
		
	$message=ob_get_contents();
	ob_clean();

	$to='';
	$sql = "SELECT a.BRAND_IDS,a.BUYER_IDS,c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id  and a.mail_item=b.MAIL_ITEM_MST and a.mail_item=41 and b.mail_user_setup_id=c.id and a.company_id =".$company_id."  and   A.IS_DELETED=0 and A.STATUS_ACTIVE=1 AND a.MAIL_TYPE=1 and b.IS_DELETED=0 and b.STATUS_ACTIVE=1  and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
	//  echo $sql;die;
	
	
	$mail_sql=sql_select($sql);
	$receverMailArr=array();
	foreach($mail_sql as $row)
	{
		$buyerArr=explode(',',$row[BUYER_IDS]);
		$brandArr=explode(',',$row[BRAND_IDS]);
		foreach($buyerArr as $buyerid){
			foreach($brandArr as $brandid){
				$receverMailArr[$buyerid][$brandid][$row[csf('email_address')]]=$row[csf('email_address')];
			}
		}
		
	}


	$mailDataArr[]=$user_mail_arr[$dataArray[0][csf("INSERTED_BY")]];
	$mailDataArr[]=$team_leader_mail_arr[$TEAM_LEADER];
	$mailDataArr[]=$dealing_merchant_mail_library[$DEALING_MARCHANT];
	$mailDataArr[]=implode(',',($receverMailArr[$buyer_id][$brand_id]));
	
	$to=implode(',',$mailDataArr);
 	
	$subject="Sample Requisition";
	$header=mailHeader();
	echo $to.$message;	
		
		if($_REQUEST['isview']==1){
			echo $to.$message;
		}
		else{
			if($to!=""){echo sendMailMailer( $to, $subject, $message, $from_mail,$att_file_arr );}
		}
		

	
		
?>