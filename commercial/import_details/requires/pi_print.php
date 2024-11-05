<? 
include('../../../includes/common.php');
session_start();
extract($_REQUEST);

$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name"  );
$item_group_library = return_library_array('SELECT id, item_name FROM lib_item_group','id','item_name'); 
$yarn_count = return_library_array('SELECT id,yarn_count FROM lib_yarn_count','id','yarn_count');

$supplier_name_library = return_library_array('SELECT id,supplier_name FROM lib_supplier','id','supplier_name');
$importer_name_library = return_library_array('SELECT id,company_name FROM lib_company','id','company_name');

$buyer_short_name_arr=return_library_array("select id,short_name from lib_buyer",'id','short_name');

?>
<link href="../../css/style_common.css" rel="stylesheet" type="text/css" />

<?
if($action=="print") 
{
    $data = explode('*',$data);
    $item_category_id=$data[2];
    $sql_company = sql_select("SELECT * FROM lib_company WHERE id=$data[0] and is_deleted=0 and status_active=1");
    foreach($sql_company as $company_data) 
    {
        if($company_data[csf('plot_no')]!='')$plot_no = 'Plot No.#'.$company_data[csf('plot_no')].','.' ';else $plot_no='';
        if($company_data[csf('level_no')]!='')$level_no = 'Level No.#'.$company_data[csf('level_no')].','.' ';else $level_no='';
        if($company_data[csf('road_no')]!='')$road_no = 'Road No.#'.$company_data[csf('road_no')].','.' ';else $road_no='';
        if($company_data[csf('block_no')]!='')$block_no = 'Block No.#'.$company_data[csf('block_no')].','.' ';else $block_no='';
        if($company_data[csf('city')]!='')$city = $company_data[csf('city')].','.' ';else $city='';
        if($company_data[csf('zip_code')]!='')$zip_code = '-'.$company_data[csf('zip_code')].','.' ';else $zip_code='';
        if($company_data[csf('country_id')]!=0)$country = $company_data[csf('country_id')].','.' ';else $country='';
        
        $company_address = $plot_no.$level_no.$road_no.$block_no.$city.$zip_code.$country;
    }    
    
?>
<div style="width:1000px">
    <? 
        $cbo_pi_basis_id='';
       /* $sql_mst = sql_select("select id,item_category_id,importer_id,supplier_id,pi_number,pi_date,last_shipment_date,pi_validity_date,currency_id,source,hs_code,internal_file_no ,intendor_name,pi_basis_id,remarks, import_pi, within_group, approved,total_amount,upcharge,discount,net_total_amount from com_pi_master_details where id= $data[1]");*/
        //open it/*$sql_mst = sql_select("select id,pi_basis_id,item_category_id,importer_id,supplier_id,pi_number,pi_date,last_shipment_date,pi_validity_date,currency_id,source,hs_code,internal_file_no ,intendor_name,pi_basis_id,remarks, import_pi, within_group, approved,total_amount,upcharge,discount,net_total_amount from com_pi_master_details where id= $data[1]");*/
       
        $sql_mst = sql_select("select distinct (a.id),a.pi_basis_id,a.item_category_id,a.importer_id,a.supplier_id,a.pi_number,a.pi_date,a.last_shipment_date,a.pi_validity_date,a.currency_id,a.source,a.hs_code,a.internal_file_no ,a.intendor_name,a.pi_basis_id,a.remarks, a.import_pi, a.within_group, a.approved,a.total_amount,a.upcharge,a.discount,a.net_total_amount ,b.work_order_no,c.wo_basis_id 
        from com_pi_master_details a ,com_pi_item_details b left join wo_non_order_info_mst c  on c.wo_number=b.work_order_no and b.work_order_id=c.id
        where b.pi_id = a.id  and a.id= $data[1]");//new

        /*echo "select a.id,a.pi_basis_id,a.item_category_id,a.importer_id,a.supplier_id,a.pi_number,a.pi_date,a.last_shipment_date,a.pi_validity_date,a.currency_id,a.source,a.hs_code,a.internal_file_no ,a.intendor_name,a.pi_basis_id,a.remarks, a.import_pi, a.within_group, a.approved,a.total_amount,a.upcharge,a.discount,a.net_total_amount ,b.work_order_no,c.wo_basis_id from com_pi_master_details a ,com_pi_item_details b,wo_non_order_info_mst c where b.pi_id = a.id and c.wo_number=b.work_order_no and a.id= $data[1]";die;*/

        $wo_basis_id = $sql_mst[0][csf("wo_basis_id")];//new 
        $pi_basis_id = $sql_mst[0][csf("pi_basis_id")];//new
       // echo $pi_basis_id;die;//new

        $total_amount=$sql_mst[0][csf("total_amount")];
        $upcharge=$sql_mst[0][csf("upcharge")];
        $discount=$sql_mst[0][csf("discount")];
        $net_total_amount=$sql_mst[0][csf("net_total_amount")];
        
        $i = 0; $total_ammount = 0;
        if($sql_mst[0][csf('import_pi')]==1)
        {
        ?>
            <div style="margin-left:10px">
                <table width="100%">
                    <tr>
                        <td style="font-size:20px;" align="center" colspan="6">
                            <strong>
                                <? 
                                    if($sql_mst[0][csf('within_group')]==1)
                                    {
                                        $buyer=$importer_name_library[$sql_mst[0][csf('importer_id')]];
                                        $address=$company_address;
                                    }
                                    else
                                    {
                                        $buyer=return_field_value("buyer_name","lib_buyer","id='".$sql_mst[0][csf('importer_id')]."'");
                                        $buyerData=sql_select("select buyer_name, address_1 from lib_buyer where id='".$sql_mst[0][csf('importer_id')]."'");
                                        $buyer=$buyerData[0][csf('buyer_name')];
                                        $address=$buyerData[0][csf('address_1')];
                                    }
                                    echo $buyer;
                                ?>
                            </strong>
                        </td>
                    </tr>
                    <tr>
                        <td width="100" align="right">From</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td width="200"><? echo $importer_name_library[$sql_mst[0][csf('supplier_id')]]; ?></td>
                        <td width="100">PI No:</td>
                        <td width="250"><? echo $sql_mst[0][csf('pi_number')];?></td>
                        <td width="150">Within Group:</td>
                        <td><? echo $yes_no[$sql_mst[0][csf('within_group')]];?></td>

                    </tr>
                    <tr>
                        <td></td>
                        <td rowspan="3"><? echo $address ;?></td>
                        <td>PI Date:</td>
                        <td><? echo change_date_format($sql_mst[0][csf('pi_date')]);?></td>
                        <td>Last Shipment Date:</td>
                        <td><? echo change_date_format($sql_mst[0][csf('last_shipment_date')]);?></td>
                    </tr> 
                    <tr>
                        <td></td>
                        <td>Currency:</td>
                        <td><? echo $currency[$sql_mst[0][csf('currency_id')]];?></td>
                        <td>Validity:</td>
                        <td><? echo change_date_format($sql_mst[0][csf('pi_validity_date')]);?></td>

                    </tr> 
                    <tr>
                        <td colspan="5" style="text-align:center; color:#FF0000; font-weight:bold; font-size:16px;"><? if($sql_mst[0][csf('approved')]==1) echo "Approved"; else echo "&nbsp;"; ?></td>
                    </tr>
                </table>
                <br>
                <table class="rpt_table" width="100%" cellspacing="1" rules="all" border="1">
                    <thead>
                        <th>Job No</th>
                        <th>Construction</th>
                        <th>Composition</th>
                        <th>Color</th>                  
                        <th>GSM</th>
                        <th>Dia/Width</th>
                        <th>UOM</th>
                        <th>Quantity</th>
                        <th>Rate</th>
                        <th>Amount</th>
                    </thead>
                    <tbody>
                    <?
                        $color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" ); $total_ammount = 0; $total_quantity=0;
                        $sql = "select id, work_order_no, color_id, fabric_construction as construction, fabric_composition as composition, gsm, dia_width, uom, quantity, rate, amount from com_pi_item_details where pi_id='$data[1]' and quantity>0 and status_active=1 and is_deleted=0";
                        $data_array=sql_select($sql);
                        foreach($data_array as $row)
                        {
                        ?>
                            <tr>
                                <td><?php echo $row[csf('work_order_no')]; ?></td>
                                <td><?php echo $row[csf('construction')]; ?></td>
                                <td><?php echo $row[csf('composition')]; ?></td>
                                <td><?php echo $color_library[$row[csf('color_id')]]; ?></td>
                                <td><?php echo $row[csf('gsm')]; ?></td>
                                <td><?php echo $row[csf('dia_width')]; ?></td>
                                <td><?php echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                                <td align="right"><?php echo number_format($row[csf('quantity')],2); $total_quantity += $row[csf('quantity')]; ?></td>
                                <td align="right"><?php echo number_format($row[csf('rate')],4); ?></td>
                                <td align="right"><?php echo number_format($row[csf('amount')],4); $total_ammount += $row[csf('amount')];  ?></td>
                            </tr>
                        <? 
                        } 
                        ?>
                        <tr>
                            <td align="right" colspan="7">Total</td> 
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td align="right"><? echo number_format($total_ammount,4); ?></td>
                        </tr>
                    </tbody> 
                </table>
                <table>
                    <tr height="20"><td></td></tr>
                    <tr>
                        <td valign="top"><strong>In-Words: </strong></td>
                        <td><? echo number_to_words(number_format($total_ammount,4, '.', ''),'USD','Cent');?></td>
                    </tr>
                    <tr height="50"><td></td></tr>
                </table>
                <table>
                    <tr height="20"><td></td></tr>
                    <tr>
                        <td style="border-top-style:dotted; border-top-width:3px;">Authorized Signature</td>
                    </tr>
                    <tr height="50"><td></td></tr>
                </table>
            </div>
        <?
        }
        else
        {

            //echo "<pre>";print_r($sql_mst);die;

            if($wo_basis_id==3 && $pi_basis_id==1)
            {
                $sql_buyer_style = "select d.buyer_name, d.style_ref_no
            from com_pi_item_details a, wo_non_order_info_dtls b, wo_po_break_down c, wo_po_details_master d 
            where a.work_order_dtls_id = b.id and  b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and a.pi_id='$data[1]' and a.status_active=1 and a.is_deleted=0";
            $buyer_style_result=sql_select($sql_buyer_style);
            $all_buyer=$all_style="";
            foreach($buyer_style_result as $row)
                {
                    if($buyer_test[$row[csf("buyer_name")]]=="")
                    {
                        $buyer_test[$row[csf("buyer_name")]]=$row[csf("buyer_name")];
                        $all_buyer.=$buyer_short_name_arr[$row[csf("buyer_name")]].",";
                    }
                    if($style_test[$row[csf("style_ref_no")]]=="")
                    {
                        $style_test[$row[csf("style_ref_no")]]=$row[csf("style_ref_no")];
                        $all_style.=$row[csf("style_ref_no")].",";
                    }
                }
                $all_buyer=substr($all_buyer,0,-1);
                $all_style=substr($all_style,0,-1);

            }
            else if($wo_basis_id==1 && $pi_basis_id==1)
            {
               //echo "hello 1";die;
                $sql_buyer_style = "select b.buyer_id, b.style_no
            from com_pi_item_details a, wo_non_order_info_dtls b
            where a.work_order_dtls_id = b.id and a.pi_id='$data[1]' and a.status_active=1 and a.is_deleted=0";
            //echo  $sql_buyer_style;die;
            $buyer_style_result=sql_select($sql_buyer_style);
            $all_buyer=$all_style="";
            foreach($buyer_style_result as $row)
                {
                    if($buyer_test[$row[csf("buyer_id")]]=="")
                    {
                        $buyer_test[$row[csf("buyer_id")]]=$row[csf("buyer_id")];
                        $all_buyer.=$buyer_short_name_arr[$row[csf("buyer_id")]].",";
                        
                    }
                    if($style_test[$row[csf("style_no")]]=="")
                    {
                        $style_test[$row[csf("style_no")]]=$row[csf("style_no")];
                        $all_style.=$row[csf("style_no")].",";
                    }
                }
                $all_buyer=substr($all_buyer,0,-1);
                $all_style=substr($all_style,0,-1);

            }
            else if($wo_basis_id==2 && $pi_basis_id==1)
            {
                
                 $sql_buyer_style = "select c.buyer_name, c.style
                from com_pi_item_details a, wo_non_order_info_dtls b,wo_non_order_info_mst c
                where a.work_order_dtls_id = b.id and b.mst_id = c.id and a.pi_id='$data[1]' and a.status_active=1 and a.is_deleted=0";

                $buyer_style_result=sql_select($sql_buyer_style);
                $all_buyer=$all_style="";
                foreach($buyer_style_result as $row)
                    {
                        if($buyer_test[$row[csf("buyer_name")]]=="")
                        {
                            $buyer_test[$row[csf("buyer_name")]]=$row[csf("buyer_name")];
                            $all_buyer.=$buyer_short_name_arr[$row[csf("buyer_name")]].",";
                            
                        }
                        if($style_test[$row[csf("style")]]=="")
                        {
                            $style_test[$row[csf("style")]]=$row[csf("style")];
                            $all_style.=$row[csf("style")].",";
                        }
                    }
                $all_buyer=substr($all_buyer,0,-1);
                $all_style=substr($all_style,0,-1);
            }
            else
            {

                $all_buyer="";$all_style="";
               // echo "hello";die;
                
            }

            
			

        ?>
            <div style="width:100%">
                <table width="100%">
                <?  

   
                foreach($sql_mst as $row_mst) 
                {
                    $i++;
                    $supplier_id=$row_mst[csf('supplier_id')];
                    $sql_supplier=sql_select("SELECT id,supplier_name,country_id,web_site,email,address_1,address_2,address_3,address_4 FROM lib_supplier WHERE id=$supplier_id");

                    foreach($sql_supplier as $supplier_data) 
                    {
                        if($supplier_data[csf('address_1')]!='')$address_1 = $supplier_data[csf('address_1')].','.' ';else $address_1='';
                        if($supplier_data[csf('address_2')]!='')$address_2 = $supplier_data[csf('address_2')].','.' ';else $address_2='';
                        if($supplier_data[csf('address_3')]!='')$address_3 = $supplier_data[csf('address_3')].','.' ';else $address_3='';
                        if($supplier_data[csf('address_4')]!='')$address_4 = $supplier_data[csf('address_4')].','.' ';else $address_4='';
                        if($supplier_data[csf('web_site')]!='')$web_site = $supplier_data[csf('web_site')].','.' ';else $web_site='';
                        if($supplier_data[csf('email')]!='')$email = $supplier_data[csf('email')].','.' ';else $email='';
                        if($supplier_data[csf('country_id')]!=0)$country = $supplier_data[csf('country_id')].','.' ';else $country='';
                        
                        $supplier_address = $address_1.$address_2.$address_3.$address_4.$web_site.$email.$country;
                    }
                    ?>
                    <tr>
                       <td style="font-size:20px;" align="center" colspan="6"><strong><? echo $supplier_name_library[$row_mst[csf('supplier_id')]]; ?></strong></td>
                    </tr>
                    <tr>
                       <td style="font-size:12px;" align="center" colspan="6"><strong><? echo $supplier_address; ?></strong></td>
                    </tr>
                    <tr>
                       <td width="100" align="right">To</td>
                    </tr>
                    <tr>
                       <td></td>
                       <td width="200"><? echo $importer_name_library[$row_mst[csf('importer_id')]];?></td>
                       <td width="100">Pi No:</td>
                       <td width="250"><? echo $row_mst[csf('pi_number')];?></td>
                        <td width="150">HS Code:</td>
                       <td><? echo $row_mst[csf('hs_code')];?></td>
                    </tr>
                    <tr>
                       <td></td>
                       <td rowspan="3"><? echo $company_address ;?></td>
                       <td>Pi Date:</td>
                       <td><? echo change_date_format($row_mst[csf('pi_date')]);?></td>
                       <td>Last Shipment Date:</td>
                       <td><? echo change_date_format($row_mst[csf('last_shipment_date')]);?></td>
                    </tr> 
                    <tr>
                       <td></td>
                       <td>Currency:</td>
                       <td><? echo $currency[$row_mst[csf('currency_id')]];?></td>
                       <td>Validity:</td>
                       <td><? echo change_date_format($row_mst[csf('pi_validity_date')]);?></td>
                    </tr> 
                    <tr>
                       <td></td> 
                       <td>Indentor:</td>
                       <td><? echo $supplier_name_library[$row_mst[csf('intendor_name')]];?></td>
                       <td></td>
                       <td></td>
                    </tr> 
                    <tr>  
                       <td></td>
                       <td></td>
                       <td>Buyer Name:</td>
                       <td><?  echo $all_buyer; ?></td>
                    </tr>
                    <tr> 
                       <td></td>
                       <td></td>   
                       <td>Style:</td>
                       <td><? echo  $all_style;?></td>
                    </tr>
                    <tr>
                        <td colspan="6" style="text-align:center; color:#FF0000; font-size:22px;"><? if($sql_mst[0][csf('approved')]==1) echo "Approved"; else echo "&nbsp;"; ?></td>
                    </tr> 
                 <?
                 $cbo_pi_basis_id=$row_mst[csf('pi_basis_id')];   
                } 
                ?>
             </table>
          </div> 
          <div style="height:25px;"></div>
          <div style="width:1000px; margin-left:10px">
            <? 
            if($item_category_id==1)
            {
                $data_array=sql_select("SELECT a.work_order_no,a.id,a.color_id,a.count_name,a.yarn_composition_item1,a.yarn_composition_percentage1,a.yarn_composition_item2,a.yarn_composition_percentage2,a.yarn_type,a.uom,a.quantity,a.rate,a.amount FROM com_pi_item_details a WHERE a.pi_id = $data[1] and a.status_active=1 and a.is_deleted=0 ORDER BY a.id ASC");  
             
            $yarn_count = return_library_array('SELECT id,yarn_count FROM lib_yarn_count','id','yarn_count');
            $color_name = return_library_array('SELECT id,color_name FROM lib_color','id','color_name');
            ?>
            <table class="rpt_table" width="100%" cellspacing="1" rules="all">
                <thead>
                    <tr>
                        <?php if( $cbo_pi_basis_id == 1 ) { ?>
                        <th class="header">WO</th>
                        <?php } ?>
                        <th class="header">Color</th>
                        <th class="header">Count</th>
                        <th class="header" colspan="4">Composition</th>
                        <th class="header">Yarn Type</th>
                        <th class="header">UOM</th>
                        <th class="header">Quantity</th>
                        <th class="header">Rate</th>
                        <th class="header">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 0;
                     $total_quantity = 0;
                     $total_ammount = 0;
                    foreach($data_array as $row) 
                    {
                        $i++;
                        
                        if( $i % 2 == 0 ) $bgcolor="#E9F3FF";
                        else $bgcolor = "#FFFFFF";//get_php_form_data( theemail.value, "populate_data_from_search_popup", "requires/pi_controller" );
                    ?>
                     
                    <tr bgcolor="<? echo $bgcolor; ?>"  height="25" >
                            <?php if( $cbo_pi_basis_id  == 1 ) { ?>
                            <td width="50"><?php echo $row[csf('work_order_no')]; ?></td>
                            <?php } ?>
                            <td width="<?php if( $cbo_pi_basis_id == 1 ) echo "80"; else echo "110"; ?>"><?php echo $color_name[$row[csf('color_id')]]; ?></td>
                            <td width="<?php if( $cbo_pi_basis_id == 1 ) echo "80"; else echo "85"; ?>"><?php echo $yarn_count[$row[csf('count_name')]]; ?></td>
                            <td width="<?php if( $cbo_pi_basis_id == 1 ) echo "85"; else echo "90"; ?>"><?php echo $composition[$row[csf('yarn_composition_item1')]]; ?></td>
                            <td width="25"><?php echo $row[csf('yarn_composition_percentage1')]; ?>%</td>
                            <td width="<?php if( $cbo_pi_basis_id == 1 ) echo "85"; else echo "90"; ?>"><?php echo $composition[$row[csf('yarn_composition_item2')]]; ?></td>
                            <td width="25"><?php echo $row[csf('yarn_composition_percentage2')]; ?>%</td>
                            <td width="<?php if( $cbo_pi_basis_id == 1 ) echo "90"; else echo "135"; ?>">
                            <?php if( $row[csf('yarn_type')] != 0 ) echo $yarn_type[$row[csf('yarn_type')]]; ?>
                            </td>
                            <td width="<?php if( $cbo_pi_basis_id == 1 ) echo "80"; else echo "85"; ?>">
                            <?php if( $row[csf('uom')] != 0 ) echo $unit_of_measurement[$row[csf('uom')]]; ?>
                            </td>
                            <td width="61"  align="right"><?php echo number_format($row[csf('quantity')],2);  $total_quantity += $row[csf('quantity')];?></td>
                            <td width="45" align="right"><?php echo number_format($row[csf('rate')],4); ?></td>
                            <td width="75" align="right"><?php echo number_format($row[csf('amount')],4);  $total_ammount += $row[csf('amount')];?></td>
                        </tr>
              <?php } ?>
                    
                        <tr class="tbl_bottom" height="25">
                            <? 
                                if( $cbo_pi_basis_id == 1) $colspan="9"; else $colspan="8";
                            ?>
                            <td colspan="<? echo $colspan; ?>">Sum</td> 
                            <td><? echo number_format($total_quantity,2);?></td>
                            <td></td>
                            <td><? echo number_format($total_ammount,4); ?></td>
                        </tr> 
                        <tr class="tbl_bottom">
                        <? 
                            if( $cbo_pi_basis_id == 1) $colspan="9"; else $colspan="8";
                        ?>
                        <td  colspan = "<? echo $colspan; ?>" align="right">Upcharge</td> 
                        <td><? //echo number_format($total_quantity,2); $total_quantity = 0;?></td>
                        <td></td>
                        <td><? echo number_format($upcharge,4); ?></td>
                    </tr>
                    <tr class="tbl_bottom">
                        <? 
                            if( $cbo_pi_basis_id == 1) $colspan="9"; else $colspan="8";
                        ?>
                        <td  colspan = "<? echo $colspan; ?>" align="right">Discount</td> 
                        <td><? //echo number_format($total_quantity,2); $total_quantity = 0;?></td>
                        <td></td>
                        <td><? echo number_format($discount,4); ?></td>
                    </tr>
                    <tr class="tbl_bottom">
                        <? 
                            if( $cbo_pi_basis_id == 1) $colspan="9"; else $colspan="8";
                        ?>
                        <td  colspan = "<? echo $colspan; ?>" align="right">Net Total</td> 
                        <td><? //echo number_format($total_quantity,2); $total_quantity = 0;?></td>
                        <td></td>
                        <td><? echo number_format($net_total_amount,4); ?></td>
                    </tr>
                     </tbody> 
                    </table>
                    <table>
                        <tr height="20"></tr>
                        <tr>
                            <td valign="top"><strong>In-Words: </strong></td>
                            <td><? echo number_to_words(number_format($total_ammount,4, '.', ''),'USD','Cent');?></td>
                        </tr>
                        <tr> 
                        <tr height="50"></tr>
                    </table>
                    <table>
                        <tr height="20"></tr>
                        <tr>
                            <td style="border-top-style:dotted; border-top-width:3px;">Authorized Signature</td>
                        </tr>
                        <tr> 
                        <tr height="50"></tr>
                    </table>
              <? 
              }
              else if($data[2]==2 || $data[2]==13) // knit
              {
                ?>  
                <table class="rpt_table" width="100%" cellspacing="1" rules="all">
                <thead>
                    <tr>
                         
                        <?php if( $cbo_pi_basis_id == 1 ) { ?>
                        <th>WO</th>
                        <?php } ?> 
                        <th>Fabric Type</th>
                        <th>Construction</th>
                        <th>Composition</th>
                        <th>Color</th>                  
                        <th>GSM</th>
                        <th>Dia/Width</th>
                        <th>UOM</th>
                        <th>Quantity</th>
                        <th>Rate</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    
                    $data_array=sql_select("SELECT a.id,a.pi_id,a.work_order_no,a.color_id,a.fabric_composition,a.fabric_construction,a.gsm,a.dia_width,a.uom,a.quantity,a.rate,amount,a.service_type,a.status_active FROM com_pi_item_details a WHERE a.pi_id = $data[1] and a.status_active=1 and a.is_deleted=0 ORDER BY a.id ASC");  
                     
                     
                    $color_name = return_library_array('SELECT id,color_name FROM lib_color','id','color_name');
                    $total_ammount = 0; $i = 0;
                    foreach($data_array as $row) 
                    {
                        $i++;
                        
                        if( $i % 2 == 0 ) $bgcolor="#E9F3FF";
                        else $bgcolor = "#FFFFFF";//get_php_form_data( theemail.value, "populate_data_from_search_popup", "requires/pi_controller" );
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <?php if( $cbo_pi_basis_id  == 1 ) { ?>
                        <td><?php echo $row[csf('work_order_no')]; ?></td>
                        <?php } ?>
                        <td><?php echo $row[csf('fabric_type')]; ?></td>
                        <td><?php echo $row[csf('fabric_construction')]; ?></td>
                        <td><?php echo $row[csf('fabric_composition')]; ?></td>
                        <td><?php echo $color_name[$row[csf('color_id')]]; ?></td>
                        <td><?php echo $row[csf('gsm')]; ?></td>
                        <td><?php echo $row[csf('dia_width')]; ?></td>
                        <td><?php echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                        <td  align="right"><?php echo number_format($row[csf('quantity')],2); $total_quantity += $row[csf('quantity')]; ?></td>
                        <td  align="right"><?php echo number_format($row[csf('rate')],4); ?></td>
                        <td  align="right"><?php echo number_format($row[csf('amount')],4); $total_ammount += $row[csf('amount')];  ?></td>
                    </tr>
                    <?php } ?>
                     <tr class="tbl_bottom">
                            <? 
                                if( $cbo_pi_basis_id == 1) $colspan="8"; else $colspan="7";
                            ?>
                            <td colspan="<? echo $colspan; ?>">Sum</td> 
                            <td><? echo number_format($total_quantity,2); $total_quantity = 0;?></td>
                            <td>&nbsp;</td>
                            <td><? echo number_format($total_ammount,4); ?></td>
                        </tr>
                        
                         <tr class="tbl_bottom">
                        <? 
                           if( $cbo_pi_basis_id == 1) $colspan="8"; else $colspan="7";
                        ?>
                        <td  colspan = "<? echo $colspan; ?>" align="right">Upcharge</td> 
                        <td><? //echo number_format($total_quantity,2); $total_quantity = 0;?></td>
                        <td></td>
                        <td><? echo number_format($upcharge,4); ?></td>
                    </tr>
                    <tr class="tbl_bottom">
                        <? 
                           if( $cbo_pi_basis_id == 1) $colspan="8"; else $colspan="7";
                        ?>
                        <td  colspan = "<? echo $colspan; ?>" align="right">Discount</td> 
                        <td><? //echo number_format($total_quantity,2); $total_quantity = 0;?></td>
                        <td></td>
                        <td><? echo number_format($discount,4); ?></td>
                    </tr>
                    <tr class="tbl_bottom">
                        <? 
                            if( $cbo_pi_basis_id == 1) $colspan="8"; else $colspan="7";
                        ?>
                        <td  colspan = "<? echo $colspan; ?>" align="right">Net Total</td> 
                        <td><? //echo number_format($total_quantity,2); $total_quantity = 0;?></td>
                        <td></td>
                        <td><? echo number_format($net_total_amount,4); ?></td>
                    </tr>
                </tbody> 
                </table>
                <table>
                    <tr height="20"></tr>
                    <tr>
                        <td valign="top"><strong>In-Words: </strong></td>
                        <td><? echo number_to_words(number_format($total_ammount,4, '.', ''),'USD','Cent');?></td>
                    </tr>
                    <tr> 
                    <tr height="50"></tr>
                </table>
                <table>
                    <tr height="20"></tr>
                    <tr>
                        <td style="border-top-style:dotted; border-top-width:3px;">Authorized Signature</td>
                    </tr>
                    <tr> 
                    <tr height="50"></tr>
                </table>
            <? 
            }
            else if($data[2]==3 || $data[2]==14)//Woven Fabric //Grey Fabric Woven
            {
            ?>
            <table class="rpt_table" width="100%" cellspacing="1" rules="all">
            <thead>
                <tr>
                    <?php if( $cbo_pi_basis_id == 1 ) { ?>
                    <th>&nbsp;</th>
                    <th>WO</th>
                    <?php } ?> 
                    <th>Fabric Type</th>
                    <th>Construction</th>
                    <th>Composition</th> 
                    <th>Color</th>
                    <th>Weight</th>
                    <th>Width</th>
                    <th>UOM</th>
                    <th>Quantity</th>
                    <th>Rate</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
            <?
            $data_array=sql_select("SELECT a.id,a.pi_id,a.work_order_no,a.color_id,a.fabric_composition,a.fabric_construction,a.gsm,a.dia_width,a.uom,a.quantity,a.rate,amount,a.service_type,a.status_active FROM com_pi_item_details a WHERE a.pi_id = $data[1] and a.status_active=1 and a.is_deleted=0 ORDER BY a.id ASC");   
            
                    $total_ammount = 0;
                    $color_name = return_library_array('SELECT id,color_name FROM lib_color','id','color_name');
                    
                    $i = 0;
                    foreach($data_array as $row) 
                    {
                        ?>
                    <tr bgcolor="<? echo $bgcolor; ?>"   height="25" >
                        <?php if( $cbo_pi_basis_id == 1 ) { ?>
                        <th>&nbsp;</th>
                        <td><?php echo $row[csf('work_order_no')]; ?></td>
                        <?php }  ?>
                        <td><?php echo $row[csf('fabric_type')]; ?></td>
                        <td><?php echo $row[csf('fabric_construction')]; ?></td>
                        <td><?php echo $row[csf('fabric_composition')]; ?></td>
                        <td><?php echo $color_name[$row[csf('color_id')]]; ?></td>
                         <td><?php echo $row[csf('weight')]; ?></td>
                        <td><?php echo $row[csf('width')]; ?></td>
                        <td><?php echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                        <td  align="right"><?php echo number_format($row[csf('quantity')],2); $total_quantity += $row[csf('quantity')];?></td>
                        <td  align="right"><?php echo number_format($row[csf('rate')],4); ?></td>
                        <td  align="right"><?php echo number_format($row[csf('amount')],4); $total_ammount += $row[csf('amount')]; ?></td>
                    </tr>
                    <?php 
                   } 
                   ?>
                   <tr class="tbl_bottom" height="25">
                             <? 
                                if( $cbo_pi_basis_id == 1) $colspan="6"; else $colspan="7";
                            ?>
                            <td  colspan = "<? echo $colspan+3; ?>" align="right">Sum</td> 
                            <td><? echo number_format($total_quantity,2); $total_quantity = 0;?></td>
                            <td></td>
                            <td><? echo number_format($total_ammount,4); ?></td>
                     </tr>
                     <tr class="tbl_bottom">
                        <? 
                            if( $cbo_pi_basis_id == 1) $colspan="6"; else $colspan="7";
                        ?>
                        <td  colspan = "<? echo $colspan+3; ?>" align="right">Upcharge</td> 
                        <td><? //echo number_format($total_quantity,2); $total_quantity = 0;?></td>
                        <td></td>
                        <td><? echo number_format($upcharge,4); ?></td>
                    </tr>
                    <tr class="tbl_bottom">
                        <? 
                            if( $cbo_pi_basis_id == 1) $colspan="6"; else $colspan="7";
                        ?>
                        <td  colspan = "<? echo $colspan+3; ?>" align="right">Discount</td> 
                        <td><? //echo number_format($total_quantity,2); $total_quantity = 0;?></td>
                        <td></td>
                        <td><? echo number_format($discount,4); ?></td>
                    </tr>
                    <tr class="tbl_bottom">
                        <? 
                             if( $cbo_pi_basis_id == 1) $colspan="6"; else $colspan="7";
                        ?>
                        <td  colspan = "<? echo $colspan+3; ?>" align="right">Net Total</td> 
                        <td><? //echo number_format($total_quantity,2); $total_quantity = 0;?></td>
                        <td></td>
                        <td><? echo number_format($net_total_amount,4); ?></td>
                    </tr>
            </tbody>
            </table>
            <table>
                <tr height="20"></tr>
                <tr>
                    <td valign="top"><strong>In-Words: </strong></td>
                    <td><? echo number_to_words(number_format($total_ammount,4, '.', ''),'USD','Cent');?></td>
                </tr>
                <tr> 
                <tr height="50"></tr>
            </table>
            <table>
                <tr height="20"></tr>
                <tr>
                    <td style="border-top-style:dotted; border-top-width:3px;">Authorized Signature</td>
                </tr>
                <tr> 
                <tr height="50"></tr>
            </table>
            <? 
            }
            else if($item_category_id==8 || $item_category_id==9 || $item_category_id==10 || $item_category_id==11)
            {
            ?>
               <table class="rpt_table" width="100%" cellspacing="1" rules="all">
                <thead>
                    <tr>
                        <?php if( $cbo_pi_basis_id == 1 ) { ?>
                        <th>WO</th>
                        <?php }?>
                        <th>Item Group</th>
                        <th>Item Description</th>
                        <th>UOM</th>
                        <th>Quantity</th>
                        <th>Rate</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                     $data_array=sql_select("SELECT a.id,a.pi_id,a.work_order_no,a.color_id,a.item_group,a.item_description,a.gsm,a.dia_width,a.uom,a.quantity,a.rate,amount,a.service_type,a.status_active FROM com_pi_item_details a WHERE a.pi_id = $data[1] and a.status_active=1 and a.is_deleted=0 ORDER BY a.id ASC");    
                     
                    $item_group_library = return_library_array('SELECT id, item_name FROM lib_item_group','id','item_name'); 
                    $i = 0;
                    $total_ammount = 0;
                    foreach($data_array as $row) 
                    {
                     
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" height="25" >
                        <?php if( $cbo_pi_basis_id == 1 ) { ?>
                        <td><?php echo $row[csf('work_order_no')]; ?></td>
                        <?php }  ?>
                        <td><?php echo $item_group_library[$row[csf('item_group')]]; ?></td>
                        <td><?php echo $row[csf('item_description')]; ?></td>
                        <td><?php echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                        <td  align="right"><?php echo number_format($row[csf('quantity')],2); $total_quantity += $row[csf('quantity')];?></td>
                        <td  align="right"><?php echo number_format($row[csf('rate')],4); ?></td>
                        <td  align="right"><?php echo number_format($row[csf('amount')],4); $total_ammount += $row[csf('amount')]; ?></td>
                    </tr>
                    <?php 
                   } 
                   ?>
                   <tr class="tbl_bottom" height="25">
                             <? 
                                if( $cbo_pi_basis_id == 1) $colspan="2"; else $colspan="3";
                            ?>
                            <td  colspan = "<? echo $colspan+2; ?>" align="right">Sum</td> 
                            <td><? echo number_format($total_quantity,2); $total_quantity = 0;?></td>
                            <td></td>
                            <td><? echo number_format($total_ammount,4); ?></td>
                     </tr>
                     <tr class="tbl_bottom">
                        <? 
                            if( $cbo_pi_basis_id == 1) $colspan="2"; else $colspan="3";
                        ?>
                        <td  colspan = "<? echo $colspan+2; ?>" align="right">Upcharge</td> 
                        <td><? //echo number_format($total_quantity,2); $total_quantity = 0;?></td>
                        <td></td>
                        <td><? echo number_format($upcharge,4); ?></td>
                    </tr>
                    <tr class="tbl_bottom">
                        <? 
                             if( $cbo_pi_basis_id == 1) $colspan="2"; else $colspan="3";
                        ?>
                        <td  colspan = "<? echo $colspan+2; ?>" align="right">Discount</td> 
                        <td><? //echo number_format($total_quantity,2); $total_quantity = 0;?></td>
                        <td></td>
                        <td><? echo number_format($discount,4); ?></td>
                    </tr>
                    <tr class="tbl_bottom">
                        <? 
                             if( $cbo_pi_basis_id == 1) $colspan="2"; else $colspan="3";
                        ?>
                        <td  colspan = "<? echo $colspan+2; ?>" align="right">Net Total</td> 
                        <td><? //echo number_format($total_quantity,2); $total_quantity = 0;?></td>
                        <td></td>
                        <td><? echo number_format($net_total_amount,4); ?></td>
                    </tr>
              </tbody>
             </table>
            <table>
                <tr height="20"></tr>
                <tr>
                    <td valign="top"><strong>In-Words: </strong></td>
                    <td><? echo number_to_words(number_format($total_ammount,4, '.', ''),'USD','Cent');?></td>
                </tr>
                <tr> 
                <tr height="50"></tr>
            </table>
            <table>
                <tr height="20"></tr>
                <tr>
                    <td style="border-top-style:dotted; border-top-width:3px;">Authorized Signature</td>
                </tr>
                <tr> 
                <tr height="50"></tr>
            </table>
            
             <? }
                else if($data[2]==4)//Accessories  
                {
                ?>
               <table class="rpt_table" width="100%" cellspacing="1" rules="all">
                <thead>
                    <tr>
                        <?php if( $cbo_pi_basis_id == 1 ) { ?>
                        <th class="header">WO</th>
                        <?php } ?>
    
                        <th>Item Group</th>
                        <th>Item Description</th>
                        <th>Gmts Color</th>
                        <th>Gmts Size</th>
                        <th>Item Color</th>
                        <th>Item Size</th>
                        <th>UOM</th>
                        <th>Quantity</th>
                        <th>Rate</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
					 $data_array=sql_select("SELECT a.id, a.pi_id, a.work_order_no, a.color_id, a.size_id, a.item_color, a.item_size, a.item_group, a.item_description, a.gsm, a.dia_width, a.uom, a.quantity, a.rate, a.amount, a.service_type, a.status_active FROM com_pi_item_details a WHERE a.pi_id = $data[1] and a.status_active=1 and a.is_deleted=0 ORDER BY a.id ASC");   
                    
                    $item_group_library = return_library_array('SELECT id, item_name FROM lib_item_group','id','item_name'); 
                    $i = 0;
                    $total_ammount = 0;
                    foreach($data_array as $row) 
                    {
                     
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <?php if( $cbo_pi_basis_id == 1 ) { ?>
                        <td><?php echo $row[csf('work_order_no')]; ?></td>
                        <?php } ?>
                        <td><?php echo $item_group_library[$row[csf('item_group')]]; ?></td>
                        <td><?php echo $row[csf('item_description')]; ?></td>
                        <td><?php echo $color_library[$row[csf('color_id')]]; ?></td>
                        <td><?php echo $size_library[$row[csf('size_id')]]; ?></td>
                        <td><?php echo $color_library[$row[csf('item_color')]]; ?></td>
                        <td><?php echo $row[csf('item_size')]; ?></td>
                        <td><?php echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                        <td  align="right"><?php echo number_format($row[csf('quantity')],2); $total_quantity += $row[csf('quantity')];?></td>
                        <td  align="right"><?php echo number_format($row[csf('rate')],4); ?></td>
                        <td  align="right"><?php echo number_format($row[csf('amount')],4); $total_ammount += $row[csf('amount')]; ?></td>
                    </tr>
                    <?php 
                   } 
                   ?>
                   <tr class="tbl_bottom">
                        <? 
                            if( $cbo_pi_basis_id == 1) $colspan="8"; else $colspan="7";
                        ?>
                        <td  colspan = "<? echo $colspan; ?>" align="right">Sum</td> 
                        <td><? echo number_format($total_quantity,2); $total_quantity = 0;?></td>
                        <td></td>
                        <td><? echo number_format($total_ammount,4); ?></td>
                    </tr>
                     <tr class="tbl_bottom">
                        <? 
                            if( $cbo_pi_basis_id == 1) $colspan="8"; else $colspan="7";
                        ?>
                        <td  colspan = "<? echo $colspan; ?>" align="right">Upcharge</td> 
                        <td><? //echo number_format($total_quantity,2); $total_quantity = 0;?></td>
                        <td></td>
                        <td><? echo number_format($upcharge,4); ?></td>
                    </tr>
                    <tr class="tbl_bottom">
                        <? 
                            if( $cbo_pi_basis_id == 1) $colspan="8"; else $colspan="7";
                        ?>
                        <td  colspan = "<? echo $colspan; ?>" align="right">Discount</td> 
                        <td><? //echo number_format($total_quantity,2); $total_quantity = 0;?></td>
                        <td></td>
                        <td><? echo number_format($discount,4); ?></td>
                    </tr>
                    <tr class="tbl_bottom">
                        <? 
                            if( $cbo_pi_basis_id == 1) $colspan="8"; else $colspan="7";
                        ?>
                        <td  colspan = "<? echo $colspan; ?>" align="right">Net Total</td> 
                        <td><? //echo number_format($total_quantity,2); $total_quantity = 0;?></td>
                        <td></td>
                        <td><? echo number_format($net_total_amount,4); ?></td>
                    </tr>
                    
                    
                </tbody>
            </table>
            <table>
                <tr height="20"></tr>
                <tr>
                    <td valign="top"><strong>In-Words: </strong></td>
                    <td><? echo number_to_words(number_format($total_ammount,4, '.', ''),'USD','Cent');?></td>
                </tr>
                <tr> 
                <tr height="50"></tr>
            </table>
            <table>
                <tr height="20"></tr>
                <tr>
                    <td style="border-top-style:dotted; border-top-width:3px;">Authorized Signature</td>
                </tr>
                <tr> 
                <tr height="50"></tr>
            </table>
             <? 
             }
             else if($data[2]==5 || $data[2]==6 || $data[2]==7)
             {
                ?>
               <table class="rpt_table" width="100%" cellspacing="1" rules="all">
                <thead>
                    <tr>
                        <?php if( $cbo_pi_basis_id == 1 ) { ?>
                        <th class="header">WO</th>
                        <?php } ?>
                        <th class="header">Item Group</th>
                        <th class="header">Item Description</th>
                        <th class="header">UOM</th>
                        <th class="header">Quantity</th>
                        <th class="header">Rate</th>
                        <th class="header">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                      $data_array=sql_select("SELECT a.id,a.pi_id, a.work_order_no, a.color_id, a.item_group, a.item_description, a.gsm,a.dia_width,a.uom,a.quantity,a.rate,amount,a.service_type,a.status_active FROM com_pi_item_details a WHERE a.pi_id = $data[1] and a.status_active=1 and a.is_deleted=0 ORDER BY a.id ASC"); 
                       
                    $item_group_library = return_library_array('SELECT id, item_name FROM lib_item_group','id','item_name'); 
                     
                    $i = 0;
                    $total_ammount = 0;
                    foreach($data_array as $row) 
                    {
                     
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>"  height="25" >
                        <?php if( $cbo_pi_basis_id == 1 ) { ?>
                        <td><?php echo $row[csf('work_order_no')]; ?></td>
                        <?php } ?>
                        <td><?php echo $item_group_library[$row[csf('item_group')]]; ?></td>
                        <td><?php echo $row[csf('item_description')]; ?></td>
                        <td><?php echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                        <td  align="right"><?php echo number_format($row[csf('quantity')],2);  $total_quantity += $row[csf('quantity')];?></td>
                        <td  align="right"><?php echo $row[csf('rate')]; ?></td>
                        <td  align="right"><?php echo number_format($row[csf('amount')],4);  $total_ammount += $row[csf('amount')]; ?></td>
                    </tr>
                    <?php } ?>
                     <tr class="tbl_bottom" height="25">
                             <? 
                                if( $cbo_pi_basis_id == 1) $colspan="2"; else $colspan="3";
                            ?>
                            <td  colspan = "<? echo $colspan+2; ?>" align="right">Sum</td> 
                            <td><? echo number_format($total_quantity,2); $total_quantity = 0;?></td>
                            <td></td>
                            <td><? echo number_format($total_ammount,4); ?></td>
                     </tr>
                     <tr class="tbl_bottom">
                        <? 
                            if( $cbo_pi_basis_id == 1) $colspan="2"; else $colspan="3";
                        ?>
                        <td  colspan = "<? echo $colspan+2; ?>" align="right">Upcharge</td> 
                        <td><? //echo number_format($total_quantity,2); $total_quantity = 0;?></td>
                        <td></td>
                        <td><? echo number_format($upcharge,4); ?></td>
                    </tr>
                    <tr class="tbl_bottom">
                        <? 
                              if( $cbo_pi_basis_id == 1) $colspan="2"; else $colspan="3";
                        ?>
                        <td  colspan = "<? echo $colspan+2; ?>" align="right">Discount</td> 
                        <td><? //echo number_format($total_quantity,2); $total_quantity = 0;?></td>
                        <td></td>
                        <td><? echo number_format($discount,4); ?></td>
                    </tr>
                    <tr class="tbl_bottom">
                        <? 
                              if( $cbo_pi_basis_id == 1) $colspan="2"; else $colspan="3";
                        ?>
                        <td  colspan = "<? echo $colspan+2; ?>" align="right">Net Total</td> 
                        <td><? //echo number_format($total_quantity,2); $total_quantity = 0;?></td>
                        <td></td>
                        <td><? echo number_format($net_total_amount,4); ?></td>
                    </tr>
               </tbody>
            </table>
           <table>
                <tr height="20"></tr>
                <tr>
                    <td valign="top"><strong>In-Words: </strong></td>
                    <td><? echo number_to_words(number_format($total_ammount,4, '.', ''),'USD','Cent');?></td>
                </tr>
                <tr> 
                <tr height="50"></tr>
            </table>
            <table>
                <tr height="20"></tr>
                <tr>
                    <td style="border-top-style:dotted; border-top-width:3px;">Authorized Signature</td>
                </tr>
                <tr> 
                <tr height="50"></tr>
            </table>
             <?
            }
            else if($item_category_id==12)
            {
            ?>
             <table class="rpt_table" width="100%" cellspacing="1" rules="all">
                <thead>
                    <tr>
                        <?php if( $cbo_pi_basis_id == 1 ) { ?>
                        <th>WO</th>
                        <?php } ?>
                        <th>Service Type</th>
                        <th>Item Description</th>
                        <th>UOM</th>
                        <th>Quantity</th>
                        <th>Rate</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                <?
                     $data_array=sql_select("SELECT a.id,a.pi_id,a.work_order_no,a.item_description,a.uom,a.quantity,a.rate,amount,a.service_type,a.status_active FROM com_pi_item_details a WHERE  a.pi_id = $data[1] and a.status_active=1 and a.is_deleted=0 ORDER BY a.id ASC");     
                     
                    $i = 0;
                    $total_ammount = 0;
                    foreach($data_array as $row) 
                    {
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <?php if( $cbo_pi_basis_id == 1 ) { $colspan=4; ?>
                        <td><?php echo $row[csf('work_order_no')]; ?></td>
                        <?php } else $colspan=3; ?>
                        <td><?php echo $service_type[$row[csf('service_type')]]; ?></td>
                        <td><?php echo ($row[csf('item_description')]); ?></td>
                        <td><?php echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                        <td  align="right"><?php echo number_format($row[csf('quantity')],2);  $total_quantity += $row[csf('quantity')];?></td>
                        <td  align="right"><?php echo number_format($row[csf('rate')],4); ?></td>
                        <td  align="right"><?php echo number_format($row[csf('amount')],4);  $total_ammount += $row[csf('amount')];  ?></td>
                    </tr>
                    <?php } ?>
                    
                    <tr class="tbl_bottom" height="25">
                            
                            <td  colspan = "<? echo $colspan; ?>" align="right">Sum</td> 
                            <td><? echo number_format($total_quantity,2); $total_quantity = 0;?></td>
                            <td></td>
                            <td><? echo number_format($total_ammount,4); ?></td>
                     </tr>
                     <tr class="tbl_bottom">
                        
                        <td  colspan = "<? echo $colspan; ?>" align="right">Upcharge</td> 
                        <td><? //echo number_format($total_quantity,2); $total_quantity = 0;?></td>
                        <td></td>
                        <td><? echo number_format($upcharge,4); ?></td>
                    </tr>
                    <tr class="tbl_bottom">
                        
                        <td  colspan = "<? echo $colspan; ?>" align="right">Discount</td> 
                        <td><? //echo number_format($total_quantity,2); $total_quantity = 0;?></td>
                        <td></td>
                        <td><? echo number_format($discount,4); ?></td>
                    </tr>
                    <tr class="tbl_bottom">
                        
                        <td  colspan = "<? echo $colspan; ?>" align="right">Net Total</td> 
                        <td><? //echo number_format($total_quantity,2); $total_quantity = 0;?></td>
                        <td></td>
                        <td><? echo number_format($net_total_amount,4); ?></td>
                    </tr>
                </tbody>
                </table>
               <table>
                    <tr height="20"></tr>
                    <tr>
                        <td valign="top"><strong>In-Words: </strong></td>
                        <td><? echo number_to_words(number_format($total_ammount,4, '.', ''),'USD','Cent');?></td>
                    </tr>
                    <tr> 
                    <tr height="50"></tr>
                </table>
                <table>
                    <tr height="20"></tr>
                    <tr>
                        <td style="border-top-style:dotted; border-top-width:3px;">Authorized Signature</td>
                    </tr>
                    <tr> 
                    <tr height="50"></tr>
                </table>
             <? 
             }
             else if($item_category_id==24)
             {
                $yarn_count = return_library_array('SELECT id,yarn_count FROM lib_yarn_count','id','yarn_count');
                $color_name = return_library_array('SELECT id,color_name FROM lib_color','id','color_name');
             ?>
             <table class="rpt_table" width="100%" cellspacing="1" rules="all">
                <thead>
                    <tr>
                        <?php if( $cbo_pi_basis_id == 1 ) { ?>
                        <th>WO</th>
                        <?php } ?>
                        <th>Lot</th>
                        <th>Count</th>
                        <th>Yarn Description</th>
                        <th>Yarn Color</th>
                        <th>Color Range</th>
                        <th>UOM</th>
                        <th>Quantity</th>
                        <th>Rate</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                <?
                    $data_array=sql_select("SELECT work_order_no, lot_no,count_name,item_description,yarn_color,color_range,uom,quantity,rate,amount FROM com_pi_item_details WHERE  pi_id = $data[1] and status_active=1 and is_deleted=0 ORDER BY id ASC");    
                    $i = 0; $total_ammount = 0; $total_quantity=0;
                    foreach($data_array as $row) 
                    {
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" >
                            <?php if( $cbo_pi_basis_id == 1 ) { $colspan=7; ?>
                            <td><?php echo $row[csf('work_order_no')]; ?></td>
                            <?php } else $colspan=6; ?>
                            <td><?php echo $row[csf('lot_no')]; ?></td>
                            <td><?php echo $yarn_count[$row[csf('count_name')]]; ?></td>
                            <td><?php echo $row[csf('item_description')]; ?></td>
                            <td><?php echo $color_name[$row[csf('yarn_color')]]; ?></td>
                            <td><?php echo $color_range[$row[csf('color_range')]]; ?></td>
                            <td><?php echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                            <td align="right"><?php echo number_format($row[csf('quantity')],2);  $total_quantity += $row[csf('quantity')];?></td>
                            <td align="right"><?php echo number_format($row[csf('rate')],4); ?></td>
                            <td align="right"><?php echo number_format($row[csf('amount')],4);  $total_ammount += $row[csf('amount')];  ?></td>
                        </tr>
                    <?php 
                    }
                    ?>
                    <tr class="tbl_bottom" height="25">
                        <td colspan="<? echo $colspan; ?>" align="right">Sum</td> 
                        <td><? echo number_format($total_quantity,2); ?></td>
                        <td></td>
                        <td><? echo number_format($total_ammount,4); ?></td>
                    </tr>
                     <tr class="tbl_bottom">
                        
                        <td  colspan = "<? echo $colspan; ?>" align="right">Upcharge</td> 
                        <td><? //echo number_format($total_quantity,2); $total_quantity = 0;?></td>
                        <td></td>
                        <td><? echo number_format($upcharge,4); ?></td>
                    </tr>
                    <tr class="tbl_bottom">
                        
                        <td  colspan = "<? echo $colspan; ?>" align="right">Discount</td> 
                        <td><? //echo number_format($total_quantity,2); $total_quantity = 0;?></td>
                        <td></td>
                        <td><? echo number_format($discount,4); ?></td>
                    </tr>
                    <tr class="tbl_bottom">
                        
                        <td  colspan = "<? echo $colspan; ?>" align="right">Net Total</td> 
                        <td><? //echo number_format($total_quantity,2); $total_quantity = 0;?></td>
                        <td></td>
                        <td><? echo number_format($net_total_amount,4); ?></td>
                    </tr>
                </tbody>
                </table>
               <table>
                    <tr height="20"></tr>
                    <tr>
                        <td valign="top"><strong>In-Words: </strong></td>
                        <td><? echo number_to_words(number_format($total_ammount,4, '.', ''),'USD','Cent');?></td>
                    </tr>
                    <tr> 
                    <tr height="50"></tr>
                </table>
                <table>
                    <tr height="20"></tr>
                    <tr>
                        <td style="border-top-style:dotted; border-top-width:3px;">Authorized Signature</td>
                    </tr>
                    <tr> 
                    <tr height="50"></tr>
                </table>
             <? 
             }
             else if($item_category_id==25)
             {
                $color_name = return_library_array('SELECT id,color_name FROM lib_color','id','color_name');
             ?>
             <table class="rpt_table" width="100%" cellspacing="1" rules="all">
                <thead>
                    <tr>
                        <?php if( $cbo_pi_basis_id == 1 ) { ?>
                        <th>WO</th>
                        <?php } ?>
                        <th>Gmts Item</th>
                        <th>Embellishment Name</th>
                        <th>Embellishment Type</th>
                        <th>Gmts Color</th>
                        <th>UOM</th>
                        <th>Quantity</th>
                        <th>Rate</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                <?
                    $data_array=sql_select("SELECT work_order_no, gmts_item_id,embell_name,embell_type,color_id,uom,quantity,rate,amount FROM com_pi_item_details WHERE  pi_id = $data[1] and status_active=1 and is_deleted=0 ORDER BY id ASC");    
                    $i = 0; $total_ammount = 0; $total_quantity=0;
                    foreach($data_array as $row) 
                    {
                        $emb_arr=array();
                        if($row[csf('embell_name')]==1) $emb_arr=$emblishment_print_type;
                        else if($row[csf('embell_name')]==2) $emb_arr=$emblishment_embroy_type;
                        else if($row[csf('embell_name')]==3) $emb_arr=$emblishment_wash_type;
                        else if($row[csf('embell_name')]==4) $emb_arr=$emblishment_spwork_type;
                        else $emb_arr=$blank_array;
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" >
                            <?php if( $cbo_pi_basis_id == 1 ) { $colspan=6; ?>
                            <td><?php echo $row[csf('work_order_no')]; ?></td>
                            <?php } else $colspan=5; ?>
                            <td><?php echo $garments_item[$row[csf('gmts_item_id')]]; ?></td>
                            <td><?php echo $emblishment_name_array[$row[csf('embell_name')]]; ?></td>
                            <td><?php echo $emb_arr[$row[csf('embell_type')]]; ?></td>
                            <td><?php echo $color_name[$row[csf('color_id')]]; ?></td>
                            <td><?php echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                            <td align="right"><?php echo number_format($row[csf('quantity')],2);  $total_quantity += $row[csf('quantity')];?></td>
                            <td align="right"><?php echo number_format($row[csf('rate')],4); ?></td>
                            <td align="right"><?php echo number_format($row[csf('amount')],4);  $total_ammount += $row[csf('amount')];  ?></td>
                        </tr>
                    <?php 
                    }
                    ?>
                    <tr class="tbl_bottom" height="25">
                        <td colspan="<? echo $colspan; ?>" align="right">Sum</td> 
                        <td><? echo number_format($total_quantity,2); ?></td>
                        <td></td>
                        <td><? echo number_format($total_ammount,4); ?></td>
                    </tr>
                     <tr class="tbl_bottom">
                        
                        <td  colspan = "<? echo $colspan; ?>" align="right">Upcharge</td> 
                        <td><? //echo number_format($total_quantity,2); $total_quantity = 0;?></td>
                        <td></td>
                        <td><? echo number_format($upcharge,4); ?></td>
                    </tr>
                    <tr class="tbl_bottom">
                        
                        <td  colspan = "<? echo $colspan; ?>" align="right">Discount</td> 
                        <td><? //echo number_format($total_quantity,2); $total_quantity = 0;?></td>
                        <td></td>
                        <td><? echo number_format($discount,4); ?></td>
                    </tr>
                    <tr class="tbl_bottom">
                        
                        <td  colspan = "<? echo $colspan; ?>" align="right">Net Total</td> 
                        <td><? //echo number_format($total_quantity,2); $total_quantity = 0;?></td>
                        <td></td>
                        <td><? echo number_format($net_total_amount,4); ?></td>
                    </tr>
                </tbody>
                </table>
               <table>
                    <tr height="20"></tr>
                    <tr>
                        <td valign="top"><strong>In-Words: </strong></td>
                        <td><? echo number_to_words(number_format($total_ammount,4, '.', ''),'USD','Cent');?></td>
                    </tr>
                    <tr> 
                    <tr height="50"></tr>
                </table>
                <table>
                    <tr height="20"></tr>
                    <tr>
                        <td style="border-top-style:dotted; border-top-width:3px;">Authorized Signature</td>
                    </tr>
                    <tr> 
                    <tr height="50"></tr>
                </table>
             <?
             }
             ?>
          </div>
        <?
        }
        ?>  
    </div>
<?   
 }
 
?>


 