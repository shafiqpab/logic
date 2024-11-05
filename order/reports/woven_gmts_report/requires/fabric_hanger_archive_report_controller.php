<?
    header('Content-type:text/html; charset=utf-8');
    session_start();
    include('../../../../includes/common.php');

    $user_id = $_SESSION['logic_erp']["user_id"];
    if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
    $permission=$_SESSION['page_permission'];

    $data=$_REQUEST['data'];
    $action=$_REQUEST['action'];
    $user_id=$_SESSION['logic_erp']['user_id'];

    if ($action=="report_generate")
    {
        extract($_REQUEST);
        $company=str_replace("'","",$cbo_company_name);
        $date_from=str_replace("'","",$txt_date_from);
        $date_to=str_replace("'","",$txt_date_to);
        $dispo_no=str_replace("'","",$txt_dispo_no);

        if($company!=0) $CompanyCond=" and company_id='$company'"; else $company="";

        if( $date_from=="" && $date_to=="" ) $date_cond=""; else $date_cond=" and insert_date between '".$date_from."' and '".$date_to."'";
      
        if( $date_from=="" && $date_to=="" ) $date_cond=""; else $date_cond=" and insert_date between '".change_date_format($date_from,'dd-mm-yyyy','-',1)."' and '".change_date_format($date_to,'dd-mm-yyyy','-',1)."'";
       
        if($dispo_no!="") $DispoCond=" and dispo_no='$dispo_no'"; else $dispo_no="";
        $company_array=return_library_array( "select id, company_name from lib_company",'id','company_name');
        //$rowData_arr=array();
        $sql_mst=sql_select( "select id,company_id,buyer_id,dispo_no,fabric_hanger_date,location_id,fabric_type,finish_width,fab_construction,determination_id,fab_composition,fabric_gsm,finish_type,wash_type,print_type,floor_id,room,rack,shelf,bin,sample_ref_type,system_number,status_active,insert_date from wo_fabric_hanger_archive_mst where company_id=$company $CompanyCond $date_cond $DispoCond order by id asc" );

            ob_start();
            ?>
            <fieldset style="margin-top: 20px;">
                <table width="1820px" cellspacing="0" >
                    <!-- <tr style="border:none;">
                        <td align="center" style="border:none;font-size:16px; font-weight:bold" colspan="19"> <? //echo $report_title; ?></td>
                    </tr> -->
                    <tr style="border:none;">
                        <td align="center" style="border:none;font-size:12px; font-weight:bold" colspan="19"> <? if( $date_from!="" && $date_to!="" ) echo "From  ".change_date_format($date_from)."  To  ".change_date_format($date_to);?></td>
                    </tr>
                </table>
                <table width="1820px" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
                    <thead>
                        <tr>
                            <th width="120">Company</th>
                            <th width="100">Buyer</th>
                            <th width="100">Dispo No</th>
                            <th width="100">Date</th>
                            <th width="100">Meeting Minutes</th>
                            <th width="100">Fabric Type</th>
                            <th width="70">Finish Width</th>
                            <th width="180">Fab. Construction</th>
                            <th width="150">Fab. Composition</th>
                            <th width="90">GSM/Ounce</th>
                            <th width="100">Finish Type</th>
                            <th width="100">Wash Type</th>
                            <th width="100">Print Type</th>
                            <th width="120">Sample Ref Type</th>
                            <th width="80">Floor</th>
                            <th width="70">Room</th>
                            <th width="70">Rack</th>
                            <th width="70">Shelf</th>
                            <th width="70">Bin</th>
                        </tr>
                    </thead>
                </table>
                <div style="max-height:350px;  width:1820px" id="scroll_body" >
                    <table width="1820px" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all" id="table_body">
                    <?
                    $i=0;
                    foreach($sql_mst as $row)
                    {
                        $floor_arr=return_library_array( "select b.floor_id as id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.floor_id   and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0
                        group by b.floor_id,a.floor_room_rack_name  order by a.floor_room_rack_name asc",'id','floor_room_rack_name');
                        $room_arr=return_library_array( "select b.room_id as id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.room_id    and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0
                        group by b.room_id,a.floor_room_rack_name  order by a.floor_room_rack_name asc",'id','floor_room_rack_name');
                        $rack_arr=return_library_array( "select b.rack_id as id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.rack_id   and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0
                        group by b.rack_id,a.floor_room_rack_name  order by a.floor_room_rack_name asc",'id','floor_room_rack_name');
                        $shelf_arr=return_library_array( "select b.shelf_id as id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.shelf_id    and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0
                        group by b.shelf_id,a.floor_room_rack_name  order by a.floor_room_rack_name asc",'id','floor_room_rack_name');
                        $bin_arr=return_library_array( "select b.bin_id as id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.bin_id    and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0
                        group by b.bin_id,a.floor_room_rack_name  order by a.floor_room_rack_name asc",'id','floor_room_rack_name');
                        $finish_types = array(1=>"Regular",2=>"Peach",3=>"Brush");
                        $wash_types = $emblishment_wash_type;
                        $print_types = $emblishment_print_type;
                        $sample_ref_types = array(1=>"SSM-Yarn Dyed Sample",2=>"SSD-Solid Dyed Sample",3=>"SSR-Rotary Print Sample",4=>"SSP-Digital Print Sample");
                        
                            $company_id=$company_array[$row[csf('company_id')]];
                            $buyer_id=$row[csf('buyer_id')];
                            $dispo_no=$row[csf('dispo_no')];
                            $fabric_hanger_date=$$row[csf('fabric_hanger_date')];
                            $fabric_type=$row[csf('fabric_type')];
                            $finish_width=$row[csf('finish_width')];
                            $fab_construction=$row[csf('fab_construction')];
                            $fab_composition=$row[csf('fab_composition')];
                            $fabric_gsm=$row[csf('fabric_gsm')];
                            $finish_type=$finish_types[$row[csf('finish_type')]];
                            $wash_type=$wash_types[$row[csf('wash_type')]];
                            $print_type=$print_types[$row[csf('print_type')]];
                            $sample_ref_type=$sample_ref_types[$row[csf('sample_ref_type')]];
                            $floor_id=$floor_arr[$row[csf('floor_id')]];
                            $room= $room_arr[$row[csf('room')]];
                            $rack=$rack_arr[$row[csf('rack')]];
                            $shelf=$shelf_arr[$row[csf('shelf')]];
                            $bin=$bin_arr[$row[csf('bin')]];
                            if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                     ?>
                                              
                                <tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>')" id="tr_<?=$i; ?>">
                                    <td width="120" align="center" style="word-break:break-all"><?= $company_id;?></td>
                                    <td width="100" align="center" style="word-break:break-all"></td>	
                                    <td width="100" align="center" style="word-break:break-all"><?=$dispo_no; ?></td>
                                    <td width="100" align="center" style="word-break:break-all"></td>
                                    <td width="100" align="center" style="word-break:break-all"></td>
                                    <td width="100" align="center" style="word-break:break-all"><?=$fabric_type; ?></td>
                                    <td width="70" align="center"><?=$finish_width; ?></td>
                                    <td width="180" align="center" style="word-break:break-all"><?=$fab_construction; ?></td>
                                    <td width="150" align="center" style="word-break:break-all"><?=$fab_composition; ?></td>
                                    <td width="90" align="center" ><?=$fabric_gsm; ?></td>
                                    <td width="100" align="center" style="word-break:break-all"><?=$finish_type; ?></td>
                                    <td width="100" align="center" style="word-break:break-all"><?=$wash_type; ?></td>
                                    <td width="100" align="center" style="word-break:break-all"><?=$print_type; ?></td>
                                    <td width="120" align="center" style="word-break:break-all"><?=$sample_ref_type; ?></td>
                                    <td width="80" align="center" style="word-break:break-all"><?=$floor_id; ?></td>
                                    <td width="70" align="center" style="word-break:break-all"><?=$room; ?></td>
                                    <td width="70" align="center" style="word-break:break-all"><?=$rack; ?></td>
                                    <td width="70" align="center" style="word-break:break-all"><?=$shelf; ?></td>
                                    <td width="70" align="center" style="word-break:break-all"><?=$bin; ?></td>
                                </tr>
                                <?
                                $i++;
                    }
                    ?>
                    </table>
                </div>
            </fieldset>
            <?
            $html = ob_get_contents();
            ob_clean();
            //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
            foreach (glob("*.xls") as $filename) 
            {
                @unlink($filename);
            }
            //---------end------------//
            $name=time();
            $filename=$user_id."_".$name.".xls";
            $create_new_doc = fopen($filename, 'w');	
            $is_created = fwrite($create_new_doc, $html);
            echo "$html**$filename"; 
            exit();       
    }

?>
