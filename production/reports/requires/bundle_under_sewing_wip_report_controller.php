<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
//$user_name=$_SESSION['logic_erp']['user_id'];
$user_id=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$buyer_short_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name");
$country_arr=return_library_array( "select id,country_name from lib_country", "id", "country_name");
$lib_prod_floor=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name");
$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name" );
$line_library = return_library_array("select id,line_name from lib_sewing_line", "id", "line_name");
$resource_alocate_line = return_library_array("select id, line_number from prod_resource_mst", "id", "line_number");

//------------------------------------------------------------------------------------------
if ($action=="load_drop_down_buyer")
{
    echo create_drop_down( "cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );   
    exit();  
}
/*if ($action=="load_drop_down_floor")
{
    echo create_drop_down("cbo_floor_name", 150, "select a.id,a.floor_name from lib_prod_floor a where a.status_active =1 and a.is_deleted=0 and a.company_id='$data' and production_process=5 and status_active=1 group by a.id,a.floor_name order by a.id", "id,floor_name", 1, "-- Select Floor --", $selected, "", 0);

    exit();  
}*/

/*if($action=="set_floor_status"){
    //echo "select a.id,a.floor_name from lib_prod_floor a where a.status_active =1 and a.is_deleted=0 and a.company_id in ($data) and production_process=5 and status_active=1 group by a.id,a.floor_name order by a.id";
    echo create_drop_down("cbo_floor_name", 150, "select a.id,a.floor_name from lib_prod_floor a where a.status_active =1 and a.is_deleted=0 and a.company_id in ($data) and production_process=5 and status_active=1 group by a.id,a.floor_name order by a.id", "id,floor_name", 1, "-- Select Floor --", $selected, "", 0);
    exit();
}*/



if($action=="report_generate")
{ 
    // var_dump($_REQUEST);die();
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process )); 
    $cbo_company_name=str_replace("'","",$cbo_company_name);    

    $company_id=str_replace("'","",$cbo_company_name);
    $floor_id=str_replace("'","",$hidden_floor_id);
    $job_no=str_replace("*",",",str_replace("'","",$txt_job_no));
    $hidden_cut_no=str_replace("'","",$hidden_cut_no);
    $hidden_cut_no_arr = array_unique(explode("*", $hidden_cut_no));
    $cut_cond = "";
    foreach ($hidden_cut_no_arr as $val) 
    {
        $cut_cond .= ($cut_cond=="") ? "'$val'" : ",'$val'";
    }
    // echo $cut_cond;die();
    $hidden_style_no=str_replace("'","",$hidden_style_no);
    $hidden_style_arr = array_unique(explode("*", $hidden_style_no));
    $style_cond = "";
    foreach ($hidden_style_arr as $val) 
    {
        $style_cond .= ($style_cond=="") ? "'$val'" : ",'$val'";
    }

    $hidden_po_no=str_replace("*",",",str_replace("'","",$hidden_po_no));
    if($hidden_po_no != ""){        
    $hidden_po_no_arr= array_unique(explode(",", $hidden_po_no));
    foreach ($hidden_po_no_arr as $value) {
        $hidden_po_no_str.="'".$value."',";
    }
    $po_no_str=chop($hidden_po_no_str,",");
    //var_dump( $hidden_po_no_arr);die;
    //$hidden_po_no="'". implode("','", array_unique($hidden_po_no_arr))."'";
    if($po_no_str=="") $po_no = ""; else $po_no = "and c.po_number in ($po_no_str)";
    }

    if($job_no != '') $jobID_cond = " and d.job_no_prefix_num in($job_no)"; else $jobID_cond = "";
    if($floor_id != "") $floorID_cond = " and e.floor_id in ($floor_id)"; else $floorID_cond = '';
    if($company_id==0) $company_name=""; else $company_name=" and e.serving_company in ($company_id)";
    if($hidden_cut_no=="") $cutting_no=""; else $cutting_no=" and a.cut_no in($cut_cond)";
    if($hidden_style_no=="") $style_no = ""; else $style_no = " and d.style_ref_no in($style_cond)";

    if(str_replace("'","",trim($txt_date_from))=="" || str_replace("'","",trim($txt_date_to))=="") $txt_date="";
    else $txt_date=" and e.delivery_date between $txt_date_from and $txt_date_to";
    
    if ($db_type == 0) $year_field = "YEAR(a.insert_date) as year";
    else if ($db_type == 2) $year_field = "to_char(a.insert_date,'YYYY') as year";
    else $year_field = "";//defined Later
   // ======================  report show as per report setting select ==========================
    $print_report_format=return_field_value("format_id","lib_report_template","template_name =".$company_id." and module_id=7 and report_id=192 and is_deleted=0 and status_active=1");
    // print_r($print_report_format);
				
	$print_button=explode(",",$print_report_format);
	$print_button_first=array_shift($print_button);
	// echo $print_button_first.'D';//die;
	if($print_button_first==135) $bundle_wise_sewing_input_button="emblishment_issue_print_2";
	else if($print_button_first==136) $bundle_wise_sewing_input_button="emblishment_issue_print_3";
	else if($print_button_first==137) $bundle_wise_sewing_input_button="sewing_input_challan_print";
	else if($print_button_first==129) $bundle_wise_sewing_input_button="sewing_input_challan_print_5";
	else if($print_button_first==72) $bundle_wise_sewing_input_button="sewing_input_challan_print_8";
	else if($print_button_first==191) $bundle_wise_sewing_input_button="sewing_input_challan_print7";
	
	else  $bundle_wise_sewing_input_button="";

    $sql="SELECT a.id, a.cut_no, a.po_break_down_id, a.production_type, a.sewing_line, a.insert_date, b.bundle_no, c.po_number, d.style_ref_no, d.buyer_name, $year_field, a.production_quantity, b.production_qnty, e.floor_id, e.serving_company, e.delivery_date,e.id as sys_id, e.sys_number, f.country_id, f.color_number_id, f.size_number_id, f.item_number_id, f.job_no_mst,c.grouping
    from pro_garments_production_mst a, pro_garments_production_dtls b, pro_gmts_delivery_mst e, wo_po_color_size_breakdown f, wo_po_break_down c, wo_po_details_master d
    where a.id=b.mst_id and a.delivery_mst_id= e.id and b.delivery_mst_id= e.id and b.color_size_break_down_id=f.id and  f.po_break_down_id=c.id and c.job_id=d.id and
        a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.production_type=4 $company_name $txt_date $jobID_cond $floorID_cond $cutting_no $style_no $po_no order by a.id desc";
        // echo $sql; die();
    $result = sql_select($sql);
    if(count($result)<1)
    {
        echo '<div style="text-align: center;color: red;font-weight: bold;font-size: 20px;">Data not found.</div>';die();
    }
    $po_id_arr = array();
    foreach ($result as $val) 
    {
        $po_id_arr[$val[csf('po_break_down_id')]] = $val[csf('po_break_down_id')];
    }

    $poIds = implode(",", $po_id_arr);

    $input_challan_arr=array();
    $scanned_bundle_arr=return_library_array( "SELECT b.bundle_no from pro_garments_production_mst a, pro_garments_production_dtls b where a.id=b.mst_id and b.production_type=5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.serving_company in ($cbo_company_name) and a.po_break_down_id in($poIds)",'bundle_no','bundle_no');

    $bundle_sql=sql_select( "SELECT c.id,c.sys_number from pro_garments_production_mst a, pro_gmts_delivery_mst c where a.delivery_mst_id=c.id  and a.production_type=4  and a.status_active=1 and a.is_deleted=0  and a.po_break_down_id in($poIds)");
    foreach($bundle_sql as $row )
    {
        //$scanned_bundle_arr[$row[csf('bundle_no')]]['bundle_no']=$row[csf('bundle_no')];
        $input_challan_arr[$row[csf('sys_number')]]=$row[csf('sys_number')];
    }
    unset($bundle_sql);
    // print_r($input_challan_arr); die;

    $nameArray = sql_select("select id, auto_update from variable_settings_production where company_name in ($company_id) and variable_list=23 and status_active=1 and is_deleted=0");
    $prod_reso_allocation = $nameArray[0][csf('auto_update')];
    ob_start();

    ?>
    <script type="text/javascript">
             setFilterGrid('table_body',-1);

    </script>

    <br/>
    <div>
        <table width="1640" cellspacing="0" border="1" align="left" class="rpt_table" rules="all" id="table_header" >
            <thead>                
                    <th width="30">Sl.</th>    
                    <th width="80">Buyer Name</th>
                    <th width="120">Style No</th>
                    <th width="60">Job Year</th>
                    <th width="100">Job No</th>
                    <th width="100">Internal Ref.</th>
                    <th width="100">PO No</th>
                    <th width="100">Item Name</th>
                    <th width="80">Country</th>
                    <th width="100">Color</th>                    
                    <th width="60">GMT Size</th>
                    <th width="120">Cutting No</th>
                    <th width="120">Bundle No</th>
                    <th width="80">Bundle Qty</th>
                    <th width="70">Input Date</th>                    
                    <th width="120">Floor Name</th>
                    <th width="120">Input Line No</th>
                    <th width="144" >Input Challan No</th>                 
            </thead>
        </table>
        <div style="max-height:400px; overflow-y:scroll; width:1658px;" id="scroll_body">
                    <table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="1640" rules="all" id="table_body"> 

        
            <tbody>
            <?
            if(count($result) == 0){die;}

            $i=1; $k=1;
            foreach ($result as $row)
            {
                if($input_challan_arr[$row[csf('sys_number')]]!="")
                {
                    $td_color="#6699CC";
                }
                else
                {
                    $td_color="";
                }
                
                if($scanned_bundle_arr[$row[csf('bundle_no')]]=="")
                {
                    if ($i % 2 == 0)  $bgcolor = "#E9F3FF";  else $bgcolor = "#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                        <td width="30">&nbsp;<? echo $i;?></td>
                        <td width="80" align="center"><p>&nbsp;<? echo $buyer_short_library[$row[csf('buyer_name')]]; ?>&nbsp;</p></td>
                        <td width="120" align="center"><p>&nbsp;<? echo $row[csf('style_ref_no')]; ?></p></td>
                        <td width="60" align="center"><p>&nbsp;<? echo $row[csf('year')]; ?></p></td>
                        <td width="100" align="center"><p>&nbsp;<? echo $row[csf('job_no_mst')]; ?></p></td>
                        <td width="100" align="center"><p>&nbsp;<? echo $row[csf('grouping')]; ?></p></td>
                        <td width="100" align="center"><p>&nbsp;<? echo $row[csf('po_number')]; ?></p></td>
                        <td width="100" align="center"><p>&nbsp;<? echo $garments_item[$row[csf('item_number_id')]]; ?></p></td>                
                        <td width="80" align="center"><p>&nbsp;<? echo $country_arr[$row[csf('country_id')]]; ?></p></td>
                        <td width="100" align="center"><p>&nbsp;<? echo $color_library[$row[csf('color_number_id')]]; ?></p></td>                
                        <td width="60" align="center"><p>&nbsp;<? echo $size_library[$row[csf('size_number_id')]]; ?></p></td>
                        <td width="120" align="center"><p>&nbsp;<? echo $row[csf('cut_no')];  ?></p></td>
                        <td width="120" align="center"><p>&nbsp;<? echo $row[csf('bundle_no')];  ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($row[csf('production_qnty',2,'.','')]); ?></p></td>
                        <td width="70" align="center"><p>&nbsp;<? echo $row[csf('delivery_date')];  ?></p></td>
                        <td width="120" align="left"><p>&nbsp;<? echo $lib_prod_floor[$row[csf('floor_id')]];  ?></p></td>
                        
                        <td width="120" align="center"><p>&nbsp;
                            <?
                            if ($prod_reso_allocation == 1) {
                                $sewing_line = $resource_alocate_line[$row[csf('sewing_line')]];
                                $sewing_line_arr = explode(",", $sewing_line);
                                $sewing_line_name = "";
                                foreach ($sewing_line_arr as $line_id) {
                                    $sewing_line_name .= $line_library[$line_id] . ",";
                                }
                                $sewing_line_name = chop($sewing_line_name, ",");
                                echo $sewing_line_name;
                            } else {
                                echo $line_library[$row[csf('sewing_line')]];
                            }
                            ?></p>
                        </td>
                        <td width="144"  align="center" bgcolor="<? echo $td_color; ?>">
                            <p>&nbsp;
                                <?
                                // $bundle_id_sql=sql_select("SELECT id, sys_number FROM pro_gmts_delivery_mst  WHERE status_active = 1 AND is_deleted = 0 AND sys_number ='".$row[csf('sys_number')]."' GROUP BY id, sys_number");
                                ?>
                                <a href='#report_details' onclick="generate_report('<? echo $company_id; ?>','<? echo $row[SYS_ID]; ?>','<? echo $bundle_wise_sewing_input_button;?>');">
                                    <?
                                       echo $row[csf('sys_number')];
                                    ?>
                                </a>
                                <?// echo $row[csf('sys_number')]; ?>
                            </p>
                        </td>
                    </tr>
                    
                    <?
                $total_bundle_qty += $row[csf('production_qnty')];
                $i++;
                }
            }
        ?>
                 <tr style="font-weight: bold; font-size: 20px; background-color:#ccc">
                    <td colspan="13" align="right">Total</td>
                    <td align="right"> <?php echo $total_bundle_qty;?></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>

                </tbody>
                
        </table>
        </div>
        </div>
    <?
    foreach (glob("$user_id*.xls") as $filename) 
    {
        if( @filemtime($filename) < (time()-$seconds_old) )
        @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc,ob_get_contents());
    //$filename=$user_id."_".$name.".xls";
    echo "$total_data####$filename";

    exit();
}


if($action=="search_by_popup")
{
    echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
    extract($_REQUEST);
    ?>
     
    <script>
        
        var selected_job = new Array; 
        var selected_style = new Array;
        var selected_po = new Array;
        var selected_cut = new Array;
        
        function check_all_data()
        {
            var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
            tbl_row_count = tbl_row_count - 1;

            for( var i = 1; i <= tbl_row_count; i++ )
            {
                $('#tr_'+i).trigger('click'); 
            }
        }
        
        function toggle( x, origColor ) {
            var newColor = 'yellow';
            if ( x.style ) {
                x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
            }
        }
        
        function js_set_value( str ) {
            // alert(str);
            if (str!="") str=str.split("_");
             
            toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );
             
            if( jQuery.inArray( str[3], selected_cut ) == -1 ) 
            {
                selected_job.push( str[1] );
                selected_style.push( str[2] );
                selected_po.push( str[3] );
                selected_cut.push( str[4] );
                
            }
            else 
            {
                for( var i = 0; i < selected_job.length; i++ ) 
                {
                    if( selected_job[i] == str[1] ) break;
                }
                selected_job.splice( i, 1 );
                selected_style.splice( i, 1 );
                selected_po.splice( i, 1 );
                selected_cut.splice( i, 1 );
            }
            var job = ''; 
            var style = '';
            var po = '';
            var cut = '';
            for( var i = 0; i < selected_job.length; i++ ) 
            {
                job += selected_job[i] + '*';
                style += selected_style[i] + '*';
                po += selected_po[i] + '*';
                cut += selected_cut[i] + '*';
            }
            
            job = job.substr( 0, job.length - 1 );
            style = style.substr( 0, style.length - 1 );
            po = po.substr( 0, po.length - 1 );
            cut = cut.substr( 0, cut.length - 1 );
            
            $('#selected_job').val( job );
            $('#selected_style').val( style );
            $('#selected_po').val( po );
            $('#selected_cut').val( cut );
        }

        function dynamic_ttl_change(data)
        {
            var titles="";
            if(data==1)
            {
                titles="Job No";
            }
            else if(data==2)
            {
                titles="Style Ref."
            }
            else if(data==3)
            {
                titles="Po No.";
            }
            else if(data==4)
            {
                titles="Cut No.";
            }
            else
            {
                titles="Internal Ref.";
            }
            $("#dynamic_ttl").html(titles);
        }
    
    </script>

    </head>

    <body>
        <div align="center" style="width:100%;" >
            <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
                <table width="600" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
                    <thead>
                        
                        <tr>                     
                            <th width="150" class="must_entry_caption">Company Name</th>
                            <th width="130" class="">Buyer Name</th>
                            <th width="100">Search By</th>
                            <th width="100" id="dynamic_ttl">Job No</th>
                             <th>&nbsp;</th>
                        </tr>           
                    </thead>
                    <tr class="general">
                        <td>
                        <input type="hidden" id="selected_job">
                        <input type="hidden" id="selected_style"> 
                        <input type="hidden" id="selected_po"> 
                        <input type="hidden" id="selected_cut"> 
                            <?
                            $search_by_arr=array(1=>"Job No",2=>"Style Ref.",3=>"Po No",4=>"Cut No",5=>"Internal Ref.");
                             echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", '',"load_drop_down( 'bundle_under_sewing_wip_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td_popup' );" );

                             ?>
                        </td>
                        <td id="buyer_td_popup"><? asort($buyer_arrs);echo create_drop_down( "cbo_buyer_name", 130, $buyer_arrs,'', 1, "-- Select Buyer --" ); ?></td>
                        <td>
                            <? echo create_drop_down( "cbo_search_by", 100, $search_by_arr,'',1, "-- Select--", 1,"dynamic_ttl_change(this.value);" );
                            ?>
                            
                        </td>
                        <td><input type="text" style="width:100px" class="text_boxes"  name="txt_job_po_style_no" id="txt_job_po_style_no" /></td>
                        <input type="hidden" name="hidden_job_year" id="hidden_job_year" value="<? echo $job_year;?>">
                        
                        <td align="center">
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_job_po_style_no').value+'_'+document.getElementById('hidden_job_year').value, 'search_by_popup_list_view', 'search_div', 'bundle_under_sewing_wip_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)')" style="width:100px;" /></td>
                    </tr>
                    
                </table>
            </form>
        </div>
        <div id="search_div"></div>
    </body>         
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit(); 
}

if($action=="search_by_popup_list_view")
{
    $data=explode('_',$data);
    if(!$data[0])
    {
        echo "Select Company Name !!";die;
    }
    $str_cond="";
    $str_cond.=($data[0])? " and a.company_name='$data[0]' " : "";
    $str_cond.=($data[1])? " and a.buyer_name='$data[1]' " : "";
    if($data[3])
    {
        if($data[2]==1)
        {
            $str_cond.= " and a.job_no_prefix_num='$data[3]'";

        }
        else if($data[2]==2)
        {
            $str_cond.= " and a.style_ref_no like '%$data[3]%'";

        }
        else if($data[2]==3)
        {
            $str_cond.= " and b.po_number like '%$data[3]%'";

        }
        else if($data[2]==4)
        {
            $str_cond.= " and c.cut_no like '%$data[3]%'";

        }
        else if($data[2]==5)
        {
            $str_cond.= " and b.grouping like '%$data[3]%'";

        }
    }
    if($data[4])
    {
       if($db_type==2)
       {
         $str_cond.=" and to_char(a.insert_date,'YYYY')='$data[4]'";
       }
       else
       {
            $str_cond.=" and year(a.insert_date)='$data[4]'";
       }
    }

    $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
    $comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
    $arr=array (0=>$comp,1=>$buyer_arr);
    $sql= "SELECT a.id,b.po_number,a.job_no_prefix_num as job_no,a.style_ref_no,a.company_name,a.buyer_name,c.cut_no from wo_po_details_master a,wo_po_break_down b,pro_garments_production_mst c where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 $str_cond  group by a.id,b.po_number,a.job_no_prefix_num,a.style_ref_no,a.company_name,a.buyer_name,c.cut_no";
    // echo $sql;die;         
    echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Style Ref. No, Po No, Cut No.", "120,100,100,100,140,140","740","290",0, $sql , "js_set_value", "job_no,style_ref_no,po_number,cut_no","",1,"company_name,buyer_name,0,0,0,0,0",$arr,"company_name,buyer_name,job_no,style_ref_no,po_number,cut_no","",'','0,0,0,0,0,0','',1) ;
   exit(); 
}

if($action=="floor_popup")
{
    echo load_html_head_contents("Search By Popup", "../../../", 1, 1,'','','');
    extract($_REQUEST);
    //$im_data=explode('_',$data);
    //print_r ($im_data);
    ?>
    <script>
        
        var selected_id = new Array; var selected_name = new Array;
        
        function check_all_data()
        {
            var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
            tbl_row_count = tbl_row_count - 1;

            for( var i = 1; i <= tbl_row_count; i++ )
            {
                $('#tr_'+i).trigger('click'); 
            }
        }
        
        function toggle( x, origColor ) {
            var newColor = 'yellow';
            if ( x.style ) {
                x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
            }
        }
        
        function js_set_value( str ) {
            
            if (str!="") str=str.split("_");
             
            toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );
             
            if( jQuery.inArray( str[1], selected_id ) == -1 ) {
                selected_id.push( str[1] );
                selected_name.push( str[2] );
                
            }
            else {
                for( var i = 0; i < selected_id.length; i++ ) {
                    if( selected_id[i] == str[1] ) break;
                }
                selected_id.splice( i, 1 );
                selected_name.splice( i, 1 );
            }
            var id = ''; var name = '';
            for( var i = 0; i < selected_id.length; i++ ) {
                id += selected_id[i] + ',';
                name += selected_name[i] + '*';
            }
            
            id = id.substr( 0, id.length - 1 );
            name = name.substr( 0, name.length - 1 );
            
            $('#hid_floor_id').val( id );
            $('#hid_floor_name').val( name );
        }
        
        function hidden_field_reset()
        {
            $('#hid_floor_id').val('');
            $('#hid_floor_name').val( '' );
            selected_id = new Array();
            selected_name = new Array();
        }
    </script>
    </head>
    <input type="hidden" name="hid_floor_id" id="hid_floor_id" />
    <input type="hidden" name="hid_floor_name" id="hid_floor_name" />
    <?  
    $sql = "select a.id,a.floor_name from lib_prod_floor a where a.status_active =1 and a.is_deleted=0 and a.company_id in ($cbo_company) and production_process=5 and status_active=1 group by a.id,a.floor_name order by a.id";
    //echo  $sql;
    
    echo create_list_view("tbl_list_search", "Floor Name", "200","250","320",0, $sql , "js_set_value", "id,floor_name", "", 1, "0,0,0", $arr , "floor_name", "",'setFilterGrid(\'tbl_list_search\',-1);','0,0,0','',1) ;
    
   exit(); 
}