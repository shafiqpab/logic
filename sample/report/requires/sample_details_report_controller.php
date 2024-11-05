<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_name=$_SESSION['logic_erp']['user_id'];



$sample_name_arr=return_library_array( "select id,sample_name  from lib_sample where is_deleted=0 and status_active=1 order by sample_name", "id", "sample_name"  );
$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
$size_arr=return_library_array( "select id, size_name from lib_size where status_active=1 and is_deleted=0","id","size_name");


if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 110, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- Select --", $selected, "" );
	exit();
}

if($action=="report_generate")
{
    $process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
 	$req_no=str_replace("'", "", $txt_req_no);
    $booking_no=str_replace("'", "", $txt_booking_no);
     
    $group=str_replace("'", "", $txt_internal_ref);
    $file=str_replace("'", "", $txt_file_no);
    $order=str_replace("'", "", $txt_order_no);
    $txt_job=str_replace("'", "", $txt_job_no);
    $sample_year=str_replace("'", "", $cbo_year);
    $year_cond="";
    if($db_type==2)
    {
        $year_cond=($sample_year)? " and  to_char(a.insert_date,'YYYY')=$sample_year" : " ";
    }
    else
    {
        $year_cond=($sample_year)? " and year(a.insert_date)=$sample_year" : " ";
    }

 	if(str_replace("'","",$cbo_company_name)==0) $company_name=""; else $company_name=" and a.company_id=$cbo_company_name";
	if(str_replace("'","",$cbo_sample_stage)==0) $sample_stages=""; else $sample_stages=" and a.sample_stage_id=$cbo_sample_stage";
	if(str_replace("'","",$cbo_buyer_name)==0) $buyer_name="";else $buyer_name=" and a.buyer_name=$cbo_buyer_name";
 	if(str_replace("'","",trim($cbo_dealing_merchant))==0) $dealing_merchant=""; else $dealing_merchant=" and a.dealing_marchant=$cbo_dealing_merchant";
	if(str_replace("'","",trim($txt_date_from))=="" || str_replace("'","",trim($txt_date_to))=="") $txt_date="";
	else $txt_date=" and a.requisition_date between $txt_date_from and $txt_date_to";

	if($req_no=="") $req_no=""; else $req_no=" and a.requisition_number_prefix_num like '%$req_no%' ";
    if($booking_no=="") $booking_no=""; else $booking_no_cond=" and a.booking_no_prefix_num like '%$booking_no%' ";
	if(str_replace("'","",$txt_job)=="") $job_no=""; else $job_no=" and a.quotation_id in(select id from wo_po_details_master where job_no like '%$txt_job%' and company_name=$cbo_company_name)";

    if(str_replace("'","",$txt_internal_ref)=="") $internal_ref=""; else $internal_ref=" and a.quotation_id in(select a.id from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and  b.grouping like '%$group%'  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$cbo_company_name)";

    if(str_replace("'","",$txt_file_no)=="") $file_no=""; else $file_no=" and a.quotation_id in(select a.id from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and  b.file_no like '%$file%'  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$cbo_company_name)";

    if(str_replace("'","",$txt_order_no)=="") $order_no=""; else $order_no=" and a.quotation_id in(select a.id from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and  b.po_number like '%$order%'  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$cbo_company_name)";

    $fromDate = change_date_format( str_replace("'","",trim($txt_date_from)) );
    $toDate = change_date_format( str_replace("'","",trim($txt_date_to)) );
    $style=str_replace("'", "", $txt_style_ref);
	if($style=='') $style_ref="";else $style_ref=" and a.style_ref_no like '%$style%'";

    $lib_body_part=return_library_array( "select id,body_part_full_name from lib_body_part  where status_active=1 and is_deleted=0", "id", "body_part_full_name");
    $lib_item_name=return_library_array( "select id,item_name from lib_item_group where item_category=4 and is_deleted=0  and status_active=1 order by item_name", "id", "item_name");

    $dealing_arr=return_library_array( "select id, team_member_name from lib_mkt_team_member_info where status_active=1 and is_deleted=0",'id','team_member_name');
    $season_arr=return_library_array( "select id, season_name from lib_buyer_season where status_active=1 and is_deleted=0","id","season_name");
    $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0",'id','buyer_name');
    $rcv_date_arr=return_library_array( "select booking_no, receive_date from inv_receive_master where status_active=1 and is_deleted=0",'booking_no','receive_date');

    $booking_sql=sql_select("SELECT a.id,b.booking_no from wo_po_details_master a,wo_booking_mst b where a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
    foreach($booking_sql as $booking_value)
    {
        $booking_arr[$booking_value[csf("id")]]=$booking_value[csf("booking_no")];
    }


        $booking_without_order_sql=sql_select("SELECT b.style_id,a.booking_no from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no $booking_no_cond $year_cond and a.status_active=1 and b.status_active=1  group by  b.style_id,a.booking_no");
    
        $reqIdArr=array();
        foreach($booking_without_order_sql as $vals)
        {
                $booking_without_order_arr[$vals[csf("style_id")]]=$vals[csf("booking_no")];        
                $reqIdArr[$vals[csf("style_id")]]=$vals[csf("style_id")];
               
        }



   
    
    if($db_type==0) $yearCond="YEAR(a.insert_date)"; else if($db_type==2) $yearCond="to_char(a.insert_date,'YYYY')";

             if($booking_no!=="") {

                $query="SELECT a.id, a.requisition_date, a.quotation_id, a.company_id, a.is_approved, a.is_acknowledge, a.dealing_marchant, a.requisition_number_prefix_num, to_char(a.insert_date,'YYYY') as year,
                a.bh_merchant, a.buyer_name, a.sample_stage_id, a.style_ref_no, a.season, b.id as dtls_id, b.sample_name, b.gmts_item_id, b.sample_color, b.smv, b.sample_charge, b.sample_curency, 
                b.sample_prod_qty, b.delv_start_date, b.delv_end_date, b.is_complete_prod, a.refusing_cause ,b.size_data,a.qrr_date,a.factory_merchant,a.remarks 
                from sample_development_mst a,sample_development_dtls b where a.id=b.sample_mst_id   and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and a.entry_form_id=203 
                and b.entry_form_id=203    $txt_date $company_name $buyer_name $sample_stages $dealing_merchant $req_no $job_no $internal_ref $style_ref $file_no $order_no $year_cond ".where_con_using_array($reqIdArr,1,'a.id')." order by a.id DESC";


            }else{

                $query="SELECT a.id, a.requisition_date, a.quotation_id, a.company_id, a.is_approved, a.is_acknowledge, a.dealing_marchant, a.requisition_number_prefix_num, to_char(a.insert_date,'YYYY') as year,
            a.bh_merchant, a.buyer_name, a.sample_stage_id, a.style_ref_no, a.season, b.id as dtls_id, b.sample_name, b.gmts_item_id, b.sample_color, b.smv, b.sample_charge, b.sample_curency, 
            b.sample_prod_qty, b.delv_start_date, b.delv_end_date, b.is_complete_prod, a.refusing_cause ,b.size_data ,a.qrr_date,a.factory_merchant,a.remarks 
            from sample_development_mst a,sample_development_dtls b where a.id=b.sample_mst_id   and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and a.entry_form_id=203 
            and b.entry_form_id=203    $txt_date $company_name $buyer_name $sample_stages $dealing_merchant $req_no $job_no $internal_ref $style_ref $file_no $order_no $year_cond  order by a.id DESC";
           
          }


        
        //   echo  $query;
                
            $sql=sql_select($query);$req_wise_data_arr=array();
            foreach ($sql as $key => $value)
            {

                $req_id_arr[$value[csf('id')]]=$value[csf('requisition_number_prefix_num')];
                $reqArr[$value[csf('id')]]=$value[csf('id')];
             
                $size_arr=explode("__",$value[csf('size_data')]);

                foreach($size_arr as $val){

                        $sizeData=explode("_",$val);
                    $key=$value[csf('requisition_number_prefix_num')]."*".$value[csf('sample_color')]."*".$sizeData[0];
                    
                    $req_wise_data_arr[$key]['bh_qty']+=$sizeData[1];
                    $req_wise_data_arr[$key]['plan_qty']+=$sizeData[2];
                    $req_wise_data_arr[$key]['dyeing_qty']+=$sizeData[3];
                    $req_wise_data_arr[$key]['test_qty']+=$sizeData[4];
                    $req_wise_data_arr[$key]['self_qty']+=$sizeData[5];
                    $req_wise_data_arr[$key]['sample_dev_qty']+=$sizeData[6];
                    $req_wise_data_arr[$key]['test_fit_qty']+=$sizeData[7];
                    $req_wise_data_arr[$key]['others_qty']+=$sizeData[8];
                    $req_wise_data_arr[$key]['sub_total_qty']+=$sizeData[1]+$sizeData[2]+$sizeData[3]+$sizeData[4]+$sizeData[5]+$sizeData[6]+$sizeData[7]+$sizeData[8];

                    $req_wise_data_arr[$key]['id']=$value[csf('id')];
                    $req_wise_data_arr[$key]['season']=$value[csf('season')];
                    $req_wise_data_arr[$key]['remarks']=$value[csf('remarks')];
                    $req_wise_data_arr[$key]['factory_merchant']=$value[csf('factory_merchant')];
                    $req_wise_data_arr[$key]['style_ref_no']=$value[csf('style_ref_no')];
                    $req_wise_data_arr[$key]['buyer_name']=$value[csf('buyer_name')];
                    $req_wise_data_arr[$key]['qrr_date']=$value[csf('qrr_date')];
                    $req_wise_data_arr[$key]['prog_date']=$value[csf('requisition_date')];;
                    $req_wise_data_arr[$key]['item'].=$garments_item[$value[csf('gmts_item_id')]].",";;
                    $req_wise_data_arr[$key]['sample_name'].=$sample_name_arr[$value[csf('sample_name')]].",";;
                }
               
                
            } 

            // echo "<pre>";
            // print_r($req_wise_data_arr);
            $sql_fabric="SELECT id,sample_mst_id,sample_name,gmts_item_id,body_part_id,fabric_nature_id,fabric_description,gsm,dia,sample_color,color_type_id,width_dia_id,uom_id,required_dzn,required_qty,color_data, determination_id,fabric_source,delivery_date,process_loss_percent,grey_fab_qnty,remarks_ra, collar_cuff_breakdown,yarn_dtls from sample_development_fabric_acc where  form_type=1 and  is_deleted=0  and status_active=1 order by id ASC";
            $sql_resultf =sql_select($sql_fabric); 
            foreach($sql_resultf as $row)
			{

                $req_id=$req_id_arr[$row[csf('sample_mst_id')]];
                $sample_req_data_arr[$req_id]['fabric_description'] .=$row[csf('fabric_description')].";";
                $sample_req_data_arr[$req_id]['body_part_id'] .=$lib_body_part[$row[csf('body_part_id')]].",";
                $sample_req_data_arr[$req_id]['fin_fab_req_qty']+=$row[csf('required_qty')];
            }

            

            $prod_sql2=sql_select("SELECT   a.sample_development_id as smp_dev_id, a.sample_name, a.gmts_item_id, a.ex_factory_qty, a.shiping_status ,b.color_id      from sample_ex_factory_mst c, sample_ex_factory_dtls a, sample_ex_factory_colorsize b where c.id=a.sample_ex_factory_mst_id and a.id=b.sample_ex_factory_dtls_id and c.delivery_basis=1  and c.status_active=1  and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.entry_form_id=132     ".where_con_using_array($reqArr,1,'a.sample_development_id')." and b.entry_form_id=132 and c.entry_form_id=132 and b.color_id IS NOT NULL group by a.sample_development_id, a.sample_name, a.gmts_item_id, a.ex_factory_qty, a.shiping_status,b.color_id");
           
        
            $string3="";
            foreach($prod_sql2 as $val)
            {
                $string3=$val[csf('smp_dev_id')].'*'.$val[csf('sample_name')].'*'.$val[csf('gmts_item_id')];
                $all_data_arr[$string3]['ex_factory_qty']+=$val[csf('ex_factory_qty')];
                $all_data_arr[$string3]['shiping_status']=$val[csf('shiping_status')];
                
            }
        
            $prod_sql3=sql_select("SELECT a.id,a.sample_development_id as sample_mst_id,a.sample_name,a.gmts_item_id, a.delivery_date
            from sample_ex_factory_dtls a where  a.status_active=1 and a.is_deleted=0  and a.entry_form_id=396 
            ".where_con_using_array($reqArr,1,'a.sample_development_id')." group by a.id,a.sample_development_id ,a.sample_name,a.gmts_item_id, a.delivery_date order by a.delivery_date ");
        
            
            $string4="";
            foreach($prod_sql3 as $val)
            {
                $req_id=$req_id_arr[$val[csf('sample_mst_id')]];
                $sample_req_data_arr[$req_id]['delivery_date']=$val[csf('delivery_date')];
               
            }

            $sql_access="SELECT id,sample_mst_id,sample_name_ra,gmts_item_id_ra,trims_group_ra,description_ra,brand_ref_ra,uom_id_ra,req_dzn_ra,req_qty_ra,remarks_ra,supplier_id,delivery_date,fabric_source from sample_development_fabric_acc where  form_type=2 and  is_deleted=0  and status_active=1 order by id ASC";
            $access_result =sql_select($sql_access);  $i=1;
            foreach($access_result as $row)
			{

                $req_id=$req_id_arr[$row[csf('sample_mst_id')]];
                $sample_req_data_arr[$req_id]['access_details'] .=$lib_item_name[$row[csf('trims_group_ra')]].";";
               
            }

            $sql_emble="SELECT id,sample_mst_id,sample_name_re,gmts_item_id_re,name_re,type_re,remarks_re,body_part_id,delivery_date,supplier_id,color_size_breakdown,fin_fab_qnty,rate,amount from sample_development_fabric_acc where form_type=3 and  is_deleted=0  and status_active=1  order by id ASC";
            $emble_result =sql_select($sql_emble);  

            foreach($emble_result as $row)
			{
               
                $req_id=$req_id_arr[$row[csf('sample_mst_id')]];
                $sample_req_data_arr[$req_id]['emble_details'] .=$emblishment_name_array[$row[csf('name_re')]].";";
                
                if($row[csf('name_re')]==3){
                    $sample_req_data_arr[$req_id]['wash_spwork'] .=$emblishment_wash_type[$row[csf('type_re')]].";";
                }elseif($row[csf('name_re')]==4){
                    $sample_req_data_arr[$req_id]['wash_spwork'] .=$emblishment_spwork_type[$row[csf('type_re')]].";";
                }
              
               
            }
          
            

            $req_row_arr=array();$clr_row_arr=array();$color_wise_total=array();$reqArr=array();
            foreach ($req_wise_data_arr as $reqKeyDtls => $value)
            {
                $dtls_data=explode("*",$reqKeyDtls);
                $req_key=$dtls_data[0];

                // $sample_key=$dtls_data[1];
                $color_key=$dtls_data[1];
                $size_key=$dtls_data[2];

                $req_row_arr[$req_key]+=1;
                $clr_row_arr[$req_key][$color_key]+=1;
                $color_wise_total[$req_key][$color_key]+=$value['sub_total_qty'];
                $reqArr[$req_key]=$req_key;
            }

            // echo "<pre>";
            // print_r($clr_row_arr);

            ?>
       <div>
        <table cellspacing="0" cellpadding="0" border="1" class="rpt_table" width="3325" rules="all" id="table_header" >
            <thead>
                
                <tr>
                    
                    <th width="100">Sample Requisition No.</th>       
                    <th width="100">QRR Date</th>    
                    <th width="100">Sample Program Date</th>       
                    <th width="100">Buyer</th>                   
                    <th width="100">FFL Merchant</th>
                    <th width="100">Style Name</th>
                    <th width="100">Img</th>
                    
                    <th width="100">Item Name</th>
                    <th width="100">Types of Sample </th>
                    <th width="150">PLM No./Order No.</th>

                    <th width="95">Season/Dept.</th>
                    <th width="80">FFL/Booking No.</th>
                    <th width="110">M-List / Block Number</th>
                    <th width="200">Fabrication</th>
                    <th width="150">Additional fabrics/Body Part Fabric</th>
                    <th width="100">Color</th>
                    <th width="90">Size </th>
                    <th width="80">BH Qty</th>
                    <th width="90">Plan qty</th>
                    <th width="90">Dyeing qty</th>
                    <th width="90">Test Qty</th>
                    <th width="80">Self qty</th>
                    <th width="90">Samp. Dept qty</th>
                    <th width="90">Test Fit qty</th>
                    <th width="90">Others Qty</th>
                    <th width="70">Sub Total</th>
                    <th width="70">Total Qty (pcs)</th>
                    <th width="90">Embellishment  Name</th>
                    <th width="90">Wash Or Special Treatment </th>
                   
                    <th width="80">Accessories Details</th>
                    <th width="80">Last Materials Received date</th>                 
                  
                    <th width="80">Sample Submitted To MnM Date</th>
                    <th>Special Buyer comments</th>
                </tr>
            </thead>
        </table>
        <div style="max-height:800px; overflow-y:scroll; width:3345px;" id="scroll_body">
        <table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="3325" rules="all" id="table_body">
            <tbody>

            <?

            $imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='sample_requisition_2' and file_type=1",'master_tble_id','image_location');

        // $key=$value[csf('requisition_number_prefix_num')]."*".$value[csf('sample_name')]."*".$value[csf('gmts_item_id')]."*".$value[csf('sample_color')]."*".$sizeData[0];


            $i=1;$string2="";$rs=1;$cr=1;$req_id="";$color_row_id="";
            foreach ($req_wise_data_arr as $reqKeyDtls => $value)
            {
                $dtls_data=explode("*",$reqKeyDtls);
                $req_key=$dtls_data[0];
                // $sample_key=$dtls_data[1];
                $color_key=$dtls_data[1];
                $size_key=$dtls_data[2];

                $desc=$sample_req_data_arr[$req_key]['fabric_description'];
                $body_part_id=$sample_req_data_arr[$req_key]['body_part_id'];            
                $booking=$booking_without_order_arr[$value["id"]];

                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                       
                       
                         <?
                         if($req_id!==$req_key){?>
                        <td width="100" align="center"  title="<?=$value['id'];?>" rowspan="<?=$req_row_arr[$req_key];?>"><div style="word-wrap:break-word; width:100px"><? echo  $req_key; ?></div></td>
                        <td width="100"  rowspan="<?=$req_row_arr[$req_key];?>"><div style="word-wrap:break-word; width:100px"><?=change_date_format($value["qrr_date"]); ?></div></td>
                        <td width="100"  rowspan="<?=$req_row_arr[$req_key];?>"><div style="word-wrap:break-word; width:100px"><?=change_date_format($value["prog_date"]);?></div></td>
                        <td width="100" align="center"  rowspan="<?=$req_row_arr[$req_key];?>"><div style="word-wrap:break-word; width:100px"><? echo $buyer_arr[$value['buyer_name']]; ?></div></td>       
                        <td width="100" align="center"  rowspan="<?=$req_row_arr[$req_key];?>"><div style="word-wrap:break-word; width:100px"><?=$dealing_arr[$value["factory_merchant"]];     ?></div> </td>
                        <td width="100" align="center"  rowspan="<?=$req_row_arr[$req_key];?>"><div style="word-wrap:break-word; width:100px"><? echo  $value['style_ref_no'] ; ?></div></td>
                        <td width="100"  rowspan="<?=$req_row_arr[$req_key];?>"><div style="word-wrap:break-word; width:100px"><img  src='<? echo "../../".$imge_arr[$value['id']]; ?>' height='80%' width='100%' /></div></td>
                        
                        <td width="100"   rowspan="<?=$req_row_arr[$req_key];?>"><div style="word-wrap:break-word; width:100px"><? echo $value["item"];; ?></div></td>
                        <td width="100"   rowspan="<?=$req_row_arr[$req_key];?>"><div style="word-wrap:break-word; width:100px"><? echo $value["sample_name"]; ?></div></td>
                        <td width="150"   rowspan="<?=$req_row_arr[$req_key];?>"><div style="word-wrap:break-word; width:150px">&nbsp;<? echo $sample_req_data_arr[$req_key][$sample_key]['fabric_description']; ?></div></td>
                        <td width="95" align="center"  rowspan="<?=$req_row_arr[$req_key];?>"><div style="word-wrap:break-word; width:95px"> <? echo $season_arr[$value['season']] ; ?></div></td>                    
                        <td width="80"   rowspan="<?=$req_row_arr[$req_key];?>"><div style="word-wrap:break-word; width:80px"><?=$booking_without_order_arr[$value["id"]];     ?></div></td>
                     
                        
                        <td width="110"  rowspan="<?=$req_row_arr[$req_key];?>"><div style="word-wrap:break-word; width:110px"><?=$value['remarks'];?></div></td>
                        <td width="200"   rowspan="<?=$req_row_arr[$req_key];?>"><div style="word-wrap:break-word; width:200px"><?=$desc; ?></div></td>
                        <td width="150"  rowspan="<?=$req_row_arr[$req_key];?>"><div style="word-wrap:break-word; width:150px"><?=$body_part_id; ?></div></td>
                        <?}
                       
                        ?>
                        <td width="100" ><div style="word-wrap:break-word; width:100px"><?=$color_arr[$color_key];?></div></td>
                       
                        <td width="90"><div style="word-wrap:break-word; width:90px"><?=$size_key;?></div></td>
                        <td width="80" align="right"><div style="word-wrap:break-word; width:80px"><?=$value['bh_qty']  ; ?></div></td>
                        <td width="90" align="right" ><div style="word-wrap:break-word; width:90px"><?=$value['plan_qty']  ; ?></div> </td>
                        <td width="90" align="right"><div style="word-wrap:break-word; width:90px"><?=$value['dyeing_qty']; ?></div></td>
                        <td width="90" align="right"><div style="word-wrap:break-word; width:90px"><?=$value['test_qty'];?></div></td>
                        <td width="80" align="right"><div style="word-wrap:break-word; width:80px"><?=$value['self_qty']  ; ?></div></td>
                        <td width="90" align="right" ><div style="word-wrap:break-word; width:90px"><?=$value['sample_dev_qty']  ; ?> </div></td>
                        <td width="90" align="right"><div style="word-wrap:break-word; width:90px"><?=$value['test_fit_qty']; ?></div></td>
                        <td width="90" align="right"><div style="word-wrap:break-word; width:90px"><?=$value['others_qty'];?></div></td>
                        <td width="70" align="right"><div style="word-wrap:break-word; width:70px"><?=$value['sub_total_qty']; ?></div></td>
                      
                        <td width="70" align="center" ><div style="word-wrap:break-word; width:70px"><?=$color_wise_total[$req_key][$color_key]; ?></div></td>
                        <?
                        
                        if($req_id!==$req_key){?>
                        <td width="90"   rowspan="<?=$req_row_arr[$req_key];?>"><div style="word-wrap:break-word; width:90px"><?=$sample_req_data_arr[$req_key]['emble_details']; ?></div></td>
                        <td width="90"   rowspan="<?=$req_row_arr[$req_key];?>"><div style="word-wrap:break-word; width:90px"><?=$sample_req_data_arr[$req_key]['wash_spwork']; ?> </div> </td>
                        <td width="80"   rowspan="<?=$req_row_arr[$req_key];?>"><div style="word-wrap:break-word; width:80px"><?=$sample_req_data_arr[$req_key]['access_details']; ?></div></td>
                        <td width="80"   rowspan="<?=$req_row_arr[$req_key];?>"><div style="word-wrap:break-word; width:80px"><?=change_date_format($rcv_date_arr[$booking]); ?></div></td>
                        <td width="80"   rowspan="<?=$req_row_arr[$req_key];?>"><div style="word-wrap:break-word; width:80px"><?=change_date_format($sample_req_data_arr[$req_key]['delivery_date']);;  ?></div></td>
                        <td  rowspan="<?=$req_row_arr[$req_key];?>"><div style="word-wrap:break-word; "><?=$all_data_arr[$string2]['wash_rcv']; ?></div></td>
                        <?}?>
                    </tr>
                <?
                $rs++;
                $color_row_id=$req_key."*".$color_key;
                $req_id=$req_key;
             

        }
                ?>
            </tbody>
        </table>
       
        </div>
        
    </div>
	<?
	exit();
}







if($action=="remarks_view")
{
    extract($_REQUEST);
    echo load_html_head_contents("Other Reject View", "../../../", 1, 1,$unicode,'','');
    $arr=array(0=>$sample_name_arr,1=>$color_arr);
 ?>
  <fieldset>
        <legend>Sewing In</legend>
        <?
      
       

       
             $sql= "SELECT a.sample_development_id as smp_dev_id,a.company_id, b.sample_dtls_row_id, b.sample_name, b.item_number_id, b.embel_name,c.color_id , b.entry_form_id, b.qc_pass_qty, b.reject_qty from sample_sewing_output_mst a, sample_sewing_output_dtls b, sample_sewing_output_colorsize c where a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and b.entry_form_id in (337) and a.sample_development_id=$sample_dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
             //  echo $sql;die;
             echo  create_list_view ( "list_view", "Sample Name,Color Name,Qty", "80,100,150","400","220",1, $sql, "", "","", 1, 'sample_name,color_id,0', $arr, "sample_name,color_id,qc_pass_qty", "../../requires/sample_details_report_controller", '','0,0,0');
        ?>
    </fieldset>
    <fieldset>
        <legend>Sewing Output</legend>
        <?
      
       

       
             $sql= "SELECT a.sample_development_id as smp_dev_id,a.company_id, b.sample_dtls_row_id, b.sample_name, b.item_number_id, b.embel_name,c.color_id , b.entry_form_id, b.qc_pass_qty, b.reject_qty from sample_sewing_output_mst a, sample_sewing_output_dtls b, sample_sewing_output_colorsize c where a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and b.entry_form_id in (130) and a.sample_development_id=$sample_dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
             //  echo $sql;die;
             echo  create_list_view ( "list_view", "Sample Name,Color Name,Qty", "80,100,150","400","220",1, $sql, "", "","", 1, 'sample_name,color_id,0', $arr, "sample_name,color_id,qc_pass_qty", "../../requires/sample_details_report_controller", '','0,0,0');
        ?>
    </fieldset>
    <fieldset>
    <legend>Cutting</legend>
        <?
             $sql= "SELECT a.sample_development_id as smp_dev_id,a.company_id, b.sample_dtls_row_id, b.sample_name, b.item_number_id, b.embel_name,c.color_id , b.entry_form_id, b.qc_pass_qty , b.reject_qty from sample_sewing_output_mst a, sample_sewing_output_dtls b, sample_sewing_output_colorsize c where a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and b.entry_form_id in (127) and a.sample_development_id=$sample_dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
             //  echo $sql;die;
             echo  create_list_view ( "list_view", "Sample Name,Color Name, Qty", "80,100,150","400","220",1, $sql, "", "","", 1, 'sample_name,color_id,0', $arr, "sample_name,color_id,qc_pass_qty", "../../requires/sample_details_report_controller", '','0,0,0');

      
	    ?>
    </fieldset>

   

    <fieldset>
    <legend>Print Issue</legend>
        <?
             $sql= "SELECT a.sample_development_id as smp_dev_id,a.company_id, b.sample_dtls_row_id, b.sample_name, b.item_number_id, b.embel_name,c.color_id , b.entry_form_id, b.qc_pass_qty, b.reject_qty from sample_sewing_output_mst a, sample_sewing_output_dtls b, sample_sewing_output_colorsize c where a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and b.entry_form_id in (338) and a.sample_development_id=$sample_dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.embel_name=1";
             //  echo $sql;die;
             echo  create_list_view ( "list_view", "Sample Name,Color Name,Qty", "80,100,150","400","220",1, $sql, "", "","", 1, 'sample_name,color_id,0', $arr, "sample_name,color_id,qc_pass_qty", "../../requires/sample_details_report_controller", '','0,0,0');

      
	    ?>
    </fieldset>

    <fieldset>
    <legend>Print Rcv</legend>
        <?
             $sql= "SELECT a.sample_development_id as smp_dev_id,a.company_id, b.sample_dtls_row_id, b.sample_name, b.item_number_id, b.embel_name,c.color_id , b.entry_form_id, b.qc_pass_qty, b.reject_qty from sample_sewing_output_mst a, sample_sewing_output_dtls b, sample_sewing_output_colorsize c where a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and b.entry_form_id in (128) and a.sample_development_id=$sample_dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.embel_name=1";
             //  echo $sql;die;
             echo  create_list_view ( "list_view", "Sample Name,Color Name,Qty", "80,100,150","400","220",1, $sql, "", "","", 1, 'sample_name,color_id,0', $arr, "sample_name,color_id,qc_pass_qty", "../../requires/sample_details_report_controller", '','0,0,0');

      
	    ?>
    </fieldset>
    <fieldset>
    <legend>Embroidery Issue</legend>
        <?
             $sql= "SELECT a.sample_development_id as smp_dev_id,a.company_id, b.sample_dtls_row_id, b.sample_name, b.item_number_id, b.embel_name,c.color_id , b.entry_form_id, b.qc_pass_qty , b.reject_qty from sample_sewing_output_mst a, sample_sewing_output_dtls b, sample_sewing_output_colorsize c where a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and b.entry_form_id in (338) and a.sample_development_id=$sample_dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.embel_name=2";
             //  echo $sql;die;
             echo  create_list_view ( "list_view", "Sample Name,Color Name,Qty", "80,100,150","400","220",1, $sql, "", "","", 1, 'sample_name,color_id,0', $arr, "sample_name,color_id,qc_pass_qty", "../../requires/sample_details_report_controller", '','0,0,0');

      
	    ?>
    </fieldset>
    <fieldset>
    <legend>Embroidery</legend>
        <?
             $sql= "SELECT a.sample_development_id as smp_dev_id,a.company_id, b.sample_dtls_row_id, b.sample_name, b.item_number_id, b.embel_name,c.color_id , b.entry_form_id, b.qc_pass_qty , b.reject_qty from sample_sewing_output_mst a, sample_sewing_output_dtls b, sample_sewing_output_colorsize c where a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and b.entry_form_id in (128) and a.sample_development_id=$sample_dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.embel_name=2";
             //  echo $sql;die;
             echo  create_list_view ( "list_view", "Sample Name,Color Name,Qty", "80,100,150","400","220",1, $sql, "", "","", 1, 'sample_name,color_id,0', $arr, "sample_name,color_id,qc_pass_qty", "../../requires/sample_details_report_controller", '','0,0,0');

      
	    ?>
    </fieldset>
    <fieldset>
    <legend>Wash Issue</legend>
        <?
             $sql= "SELECT a.sample_development_id as smp_dev_id,a.company_id, b.sample_dtls_row_id, b.sample_name, b.item_number_id, b.embel_name,c.color_id , b.entry_form_id, b.qc_pass_qty, b.reject_qty from sample_sewing_output_mst a, sample_sewing_output_dtls b, sample_sewing_output_colorsize c where a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and b.entry_form_id in (338) and a.sample_development_id=$sample_dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.embel_name=3";
             //  echo $sql;die;
             echo  create_list_view ( "list_view", "Sample Name,Color Name,Qty", "80,100,150","400","220",1, $sql, "", "","", 1, 'sample_name,color_id,0', $arr, "sample_name,color_id,qc_pass_qty", "../../requires/sample_details_report_controller", '','0,0,0');
      
	    ?>
    </fieldset>
    <fieldset>
    <legend>Wash Rcv</legend>
        <?
             $sql= "SELECT a.sample_development_id as smp_dev_id,a.company_id, b.sample_dtls_row_id, b.sample_name, b.item_number_id, b.embel_name,c.color_id , b.entry_form_id, b.qc_pass_qty, b.reject_qty from sample_sewing_output_mst a, sample_sewing_output_dtls b, sample_sewing_output_colorsize c where a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and b.entry_form_id in (128) and a.sample_development_id=$sample_dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.embel_name=3";
             //  echo $sql;die;
             echo  create_list_view ( "list_view", "Sample Name,Color Name,Qty", "80,100,150","400","220",1, $sql, "", "","", 1, 'sample_name,color_id,0', $arr, "sample_name,color_id,qc_pass_qty", "../../requires/sample_details_report_controller", '','0,0,0');
      
	    ?>
    </fieldset>

    <fieldset>
    <legend>Sp. Works Issue</legend>
        <?
             $sql= "SELECT a.sample_development_id as smp_dev_id,a.company_id, b.sample_dtls_row_id, b.sample_name, b.item_number_id, b.embel_name,c.color_id , b.entry_form_id, b.qc_pass_qty , b.reject_qty from sample_sewing_output_mst a, sample_sewing_output_dtls b, sample_sewing_output_colorsize c where a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and b.entry_form_id in (338) and a.sample_development_id=$sample_dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.embel_name=4";
             //  echo $sql;die;
             echo  create_list_view ( "list_view", "Sample Name,Color Name, Qty", "80,100,150","400","220",1, $sql, "", "","", 1, 'sample_name,color_id,0', $arr, "sample_name,color_id,qc_pass_qty", "../../requires/sample_details_report_controller", '','0,0,0');

      
	    ?>
    </fieldset>

    <fieldset>
    <legend>Sp. Works Rcv</legend>
        <?
             $sql= "SELECT a.sample_development_id as smp_dev_id,a.company_id, b.sample_dtls_row_id, b.sample_name, b.item_number_id, b.embel_name,c.color_id , b.entry_form_id, b.qc_pass_qty , b.reject_qty from sample_sewing_output_mst a, sample_sewing_output_dtls b, sample_sewing_output_colorsize c where a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and b.entry_form_id in (128) and a.sample_development_id=$sample_dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.embel_name=4";
             //  echo $sql;die;
             echo  create_list_view ( "list_view", "Sample Name,Color Name, Qty", "80,100,150","400","220",1, $sql, "", "","", 1, 'sample_name,color_id,0', $arr, "sample_name,color_id,qc_pass_qty", "../../requires/sample_details_report_controller", '','0,0,0');

      
	    ?>
    </fieldset>




 <?
}
if($action=="sample_other_reject_view")
{
    extract($_REQUEST);
    echo load_html_head_contents("Other Reject View", "../../../", 1, 1,$unicode,'','');
    $arr=array(0=>$sample_name_arr,1=>$color_arr);
 ?>
    <fieldset>
    <legend>Cutting</legend>
        <?
             $sql= "SELECT a.sample_development_id as smp_dev_id,a.company_id, b.sample_dtls_row_id, b.sample_name, b.item_number_id, b.embel_name,c.color_id , b.entry_form_id, b.qc_pass_qty as qc_pass_qty, b.reject_qty from sample_sewing_output_mst a, sample_sewing_output_dtls b, sample_sewing_output_colorsize c where a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and b.entry_form_id in (127) and a.sample_development_id=$sample_dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
             //  echo $sql;die;
             echo  create_list_view ( "list_view", "Sample Name,Color Name,Reject Qty", "80,100,150","400","220",1, $sql, "", "","", 1, 'sample_name,color_id,0', $arr, "sample_name,color_id,reject_qty", "../../requires/sample_details_report_controller", '','0,0,0');

      
	    ?>
    </fieldset>

   

    <fieldset>
    <legend>Print</legend>
        <?
             $sql= "SELECT a.sample_development_id as smp_dev_id,a.company_id, b.sample_dtls_row_id, b.sample_name, b.item_number_id, b.embel_name,c.color_id , b.entry_form_id, b.qc_pass_qty as qc_pass_qty, b.reject_qty from sample_sewing_output_mst a, sample_sewing_output_dtls b, sample_sewing_output_colorsize c where a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and b.entry_form_id in (128) and a.sample_development_id=$sample_dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.embel_name=1";
             //  echo $sql;die;
             echo  create_list_view ( "list_view", "Sample Name,Color Name,Reject Qty", "80,100,150","400","220",1, $sql, "", "","", 1, 'sample_name,color_id,0', $arr, "sample_name,color_id,reject_qty", "../../requires/sample_details_report_controller", '','0,0,0');

      
	    ?>
    </fieldset>
    <fieldset>
    <legend>Embroidery</legend>
        <?
             $sql= "SELECT a.sample_development_id as smp_dev_id,a.company_id, b.sample_dtls_row_id, b.sample_name, b.item_number_id, b.embel_name,c.color_id , b.entry_form_id, b.qc_pass_qty as qc_pass_qty, b.reject_qty from sample_sewing_output_mst a, sample_sewing_output_dtls b, sample_sewing_output_colorsize c where a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and b.entry_form_id in (128) and a.sample_development_id=$sample_dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.embel_name=2";
             //  echo $sql;die;
             echo  create_list_view ( "list_view", "Sample Name,Color Name,Reject Qty", "80,100,150","400","220",1, $sql, "", "","", 1, 'sample_name,color_id,0', $arr, "sample_name,color_id,reject_qty", "../../requires/sample_details_report_controller", '','0,0,0');

      
	    ?>
    </fieldset>

    <fieldset>
    <legend>Wash</legend>
        <?
             $sql= "SELECT a.sample_development_id as smp_dev_id,a.company_id, b.sample_dtls_row_id, b.sample_name, b.item_number_id, b.embel_name,c.color_id , b.entry_form_id, b.qc_pass_qty as qc_pass_qty, b.reject_qty from sample_sewing_output_mst a, sample_sewing_output_dtls b, sample_sewing_output_colorsize c where a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and b.entry_form_id in (128) and a.sample_development_id=$sample_dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.embel_name=3";
             //  echo $sql;die;
             echo  create_list_view ( "list_view", "Sample Name,Color Name,Reject Qty", "80,100,150","400","220",1, $sql, "", "","", 1, 'sample_name,color_id,0', $arr, "sample_name,color_id,reject_qty", "../../requires/sample_details_report_controller", '','0,0,0');
      
	    ?>
    </fieldset>

    <fieldset>
    <legend>Sp. Works</legend>
        <?
             $sql= "SELECT a.sample_development_id as smp_dev_id,a.company_id, b.sample_dtls_row_id, b.sample_name, b.item_number_id, b.embel_name,c.color_id , b.entry_form_id, b.qc_pass_qty as qc_pass_qty, b.reject_qty from sample_sewing_output_mst a, sample_sewing_output_dtls b, sample_sewing_output_colorsize c where a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and b.entry_form_id in (128) and a.sample_development_id=$sample_dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.embel_name=4";
             //  echo $sql;die;
             echo  create_list_view ( "list_view", "Sample Name,Color Name,Reject Qty", "80,100,150","400","220",1, $sql, "", "","", 1, 'sample_name,color_id,0', $arr, "sample_name,color_id,reject_qty", "../../requires/sample_details_report_controller", '','0,0,0');

      
	    ?>
    </fieldset>




 <?
}
if($action=='sample_sewing_reject_view')
{
    extract($_REQUEST);
    echo load_html_head_contents("Remarks", "../../../", 1, 1,$unicode,'','');


    ?>
    <fieldset>
        <legend>Sewing Output</legend>
        <?
      
       

        $arr=array(0=>$sample_name_arr,1=>$color_arr);
             $sql= "SELECT a.sample_development_id as smp_dev_id,a.company_id, b.sample_dtls_row_id, b.sample_name, b.item_number_id, b.embel_name,c.color_id , b.entry_form_id, b.qc_pass_qty as qc_pass_qty, b.reject_qty from sample_sewing_output_mst a, sample_sewing_output_dtls b, sample_sewing_output_colorsize c where a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and b.entry_form_id in (130) and a.sample_development_id=$sample_dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
             //  echo $sql;die;
             echo  create_list_view ( "list_view", "Sample Name,Color Name,Reject Qty", "80,100,150","400","220",1, $sql, "", "","", 1, 'sample_name,color_id,0', $arr, "sample_name,color_id,reject_qty", "../../requires/sample_details_report_controller", '','0,0,0');
        ?>
    </fieldset>

  <? 
     
}
if($action=='ex_factory_date_view')
{
    extract($_REQUEST);
    echo load_html_head_contents("MnM Ex-Factory Date Details", "../../../", 1, 1,$unicode,'','');
	?>
	<fieldset>
        <legend> MnM Ex-Factory Date Details</legend>
        <div style="width:410px; margin-top:10px">
            <table cellspacing="0" width="100%" class="rpt_table" cellpadding="0" border="1" rules="all">
                <thead>
                    <th width="40">SL</th>
                    <th width="100">Delivery Date</th>
                    <th width="100">Delivery Challan No.</th>
                    <th width="100">Delivery Qty</th>
                   
                </thead>
                <?
                $sql="SELECT c.sys_number,  a.ex_factory_qty, a.delivery_date	from sample_ex_factory_mst c, sample_ex_factory_dtls a, sample_ex_factory_colorsize b where c.id=a.sample_ex_factory_mst_id and a.id=b.sample_ex_factory_dtls_id and a.sample_development_id = $sample_req_mst_id and a.sample_name=$sample_name  and c.status_active=1 and c.is_deleted=0 and a.status_active=1  and a.is_deleted=0 and a.entry_form_id=132 and b.entry_form_id=132 and c.entry_form_id=132 and b.color_id IS NOT NULL";

               
                $sql_sel=sql_select($sql);
           
                $i=1;$total=0;
                foreach($sql_sel as  $value)
                {
					
						?>
						<tr>
							
                            <td width="40"  align="center"><? echo $i; ?></td>
                            <td width="100" align="center"><?=$value[csf('delivery_date')];?></td>
                            <td width="100" align="center"><?=$value[csf('sys_number')]; ?></td>
                            <td width="100" align="center"><? echo $value[csf('ex_factory_qty')]; ?></td>
						</tr>
						<?
						$i++;
						$total+=$value[csf('ex_factory_qty')];
						
                }
                ?>
                <tr>
                    <td colspan="3" align="right"><b> Grand Total</b></td>
                    <td align="center"><b><? echo $total; ?></b></td>
                </tr>
            </table>
        </div>
	</fieldset>
	<?
	exit();
}
if($action=='reject_qty_view')
{
    extract($_REQUEST);
    echo load_html_head_contents("Remarks", "../../.../", 1, 1,$unicode,'','');
 ?>
    <fieldset>
    <legend>Cuttings Reject</legend>
        <?
             $sql= "select sample_sewing_output_mst_id, sample_name,sample_dtls_row_id,item_number_id, sum(qc_pass_qty) as qc,sum(reject_qty) as reject from sample_sewing_output_dtls where sample_dtls_row_id='$sample_dtls_id' and entry_form_id=127 and is_deleted=0 and status_active=1 group by   sample_sewing_output_mst_id, sample_name,sample_dtls_row_id,item_number_id";
             //echo $sql;die;
             $arr =array(0=>$sample_name_arr,1=>$garments_item);
              echo  create_list_view ( "list_view_1", "Sample Name,Item,Qc Qty,Reject Qty", "80,100,100,150","500","220",1, $sql, "", "","", 1, 'sample_name,item_number_id,0,0', $arr, "sample_name,item_number_id,qc,reject", "../requires/sample_details_report_controller", '','0,0,0');

        ?>
    </fieldset>

     <fieldset>
    <legend>Embellishment Reject</legend>
        <?
             $sql= "select sample_sewing_output_mst_id, sample_name,sample_dtls_row_id,item_number_id,   sum(qc_pass_qty) as qc,sum(reject_qty) as reject  from sample_sewing_output_dtls where sample_dtls_row_id='$sample_dtls_id' and entry_form_id=128 and is_deleted=0 and status_active=1 group by  sample_sewing_output_mst_id, sample_name,sample_dtls_row_id,item_number_id  ";
              // echo $sql;die;
              $arr =array(0=>$sample_name_arr,1=>$garments_item);
             echo  create_list_view ( "list_view_1", "Sample Name,Item,Qc Qty,Reject Qty", "80,100,100,150","500","220",1, $sql, "", "","", 1, 'sample_name,item_number_id,0,0', $arr, "sample_name,item_number_id,qc,reject", "../../requires/sample_details_report_controller", '','0,0,0');

        ?>
    </fieldset>


    <fieldset>
    <legend>Sewing Reject</legend>
        <?
             $sql= "select sample_sewing_output_mst_id, sample_name,sample_dtls_row_id,item_number_id,   sum(qc_pass_qty) as qc,sum(reject_qty) as reject  from sample_sewing_output_dtls where sample_dtls_row_id='$sample_dtls_id' and entry_form_id=130 and is_deleted=0 and status_active=1 group by  sample_sewing_output_mst_id, sample_name,sample_dtls_row_id,item_number_id  ";
              // echo $sql;die;
              $arr =array(0=>$sample_name_arr,1=>$garments_item);
             echo  create_list_view ( "list_view_1", "Sample Name,Item,Qc Qty,Reject Qty", "80,100,100,150","500","220",1, $sql, "", "","", 1, 'sample_name,item_number_id,0,0', $arr, "sample_name,item_number_id,qc,reject", "../../requires/sample_details_report_controller", '','0,0,0');

        ?>
    </fieldset>

    <fieldset>
    <legend>Dyeing and Wash Reject</legend>
        <?
             $sql= "select  sample_sewing_output_mst_id, sample_name,sample_dtls_row_id,item_number_id,   sum(qc_pass_qty) as qc,sum(reject_qty) as reject  from sample_sewing_output_dtls where sample_dtls_row_id='$sample_dtls_id' and entry_form_id=131 and is_deleted=0 and status_active=1 group by  sample_sewing_output_mst_id, sample_name,sample_dtls_row_id,item_number_id  ";
             $arr =array(0=>$sample_name_arr,1=>$garments_item);
           //  echo $sql;die;
             echo  create_list_view ( "list_view_1", "Sample Name,Item,Qc Qty,Reject Qty", "80,100,100,150","500","220",1, $sql, "", "","", 1, 'sample_name,item_number_id,0,0', $arr, "sample_name,item_number_id,qc,reject", "../../requires/sample_details_report_controller", '','0,0,0,0');

        ?>
    </fieldset>


 <?
}




?>