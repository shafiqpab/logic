<? 
    /*-------------------------------------------- Comments
    Version                  :  
    Purpose			         : 	This Entry page for Fabric Hanger Archive Entry
    Functionality	         :
    JS Functions	         :
    Created by		         :	MD. SAKIBUL ISLAM
    Creation date 	         : 	30 MAY, 2023
    Requirment Client        :
    Requirment By            :
    Requirment type          :
    Requirment               :
    Affected page            :
    Affected Code            :
    DB Script                :Table Name- WO_FABRIC_HANGER_ARCHIVE_MST
    Updated by 		         :
    Update date		         :
    QC Performed BY	         :
    QC Date			         :
    Comments		         : 
    -----------------------------------------------------*/ 
    header('Content-type:text/html; charset=utf-8');
    session_start();
    $api_key='';
    if(isset($_REQUEST['api_key']))
    {
        $api_key=$_REQUEST['api_key'];
    }
    if( !isset($_SESSION['logic_erp']['user_id']) && $api_key !='logic_api_key_2609202332029062') header("location:login.php");
    include('../../../includes/common.php');
    include('../../../includes/class4/class.conditions.php');
    include('../../../includes/class4/class.reports.php');
    include('../../../includes/class4/class.fabrics.php');
    include('../../../includes/class4/class.yarns.php');
    include('../../../includes/class4/class.trims.php');
    include('../../../includes/class4/class.emblishments.php');
    include('../../../includes/class4/class.washes.php');

    $data=$_REQUEST['data'];
    $action=$_REQUEST['action'];
    $permission=$_SESSION['page_permission'];
    $user_id=$_SESSION['logic_erp']['user_id'];
    $user_ip=$_SESSION['logic_erp']["user_ip"];
    $company=$data[1];
    $finish_types = array(1=>"Regular",2=>"Peach",3=>"Brush");
    $wash_types = $emblishment_wash_type;
    $print_types = $emblishment_print_type;
    $sample_ref_types = array(1=>"SSM-Yarn Dyed Sample",2=>"SSD-Solid Dyed Sample",3=>"SSR-Rotary Print Sample",4=>"SSP-Digital Print Sample");

    //---------------------------------------------------- Start---------------------------------------------------------------------------

if ($action=="data_to_form_new")
{
    
    extract($_REQUEST);
    // print_r($data);exit;
    $sql="SELECT id, type, construction from  lib_yarn_count_determina_mst where is_deleted=0 and id=$data order by id DESC";
    $result = sql_select($sql);
    $composition_arr=array();
    $lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
    $lib_yarn_count_fab_deterArr=return_library_array( "select construction,id from lib_yarn_count_determina_mst", "construction", "id");
    $lib_yarn_count_fab_compositionArr=return_library_array( "select composition_name,id from lib_composition_array", "composition_name", "id");
    $user_arr=return_library_array( "select user_full_name,id from user_passwd", "id", "user_full_name");
    $lib_group_short=return_library_array( "select id,group_short_name from lib_group where id=1 and status_active=1", "id", "group_short_name");
    $group_short_name=$lib_group_short[1];
    $sql_q="select mst_id, copmposition_id, percent, count_id, type_id from lib_yarn_count_determina_dtls where is_deleted=0  order by id";


    
    $data_array=sql_select($sql_q);
    if (count($data_array)>0)
    {
        foreach( $data_array as $row )
        {
            $compo_per="";
            if(($row[csf('percent')]*1)>0) $compo_per=$row[csf('percent')]."% "; else $compo_per="";
            if(array_key_exists($row[csf('mst_id')],$composition_arr))
            {
                $composition_arr[$row[csf('mst_id')]]=$composition_arr[$row[csf('mst_id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$compo_per.$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
            }
            else
            {
                $composition_arr[$row[csf('mst_id')]]=$composition[$row[csf('copmposition_id')]]." ".$compo_per.$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]];
            }
            $sys_code=$group_short_name.'-'.$row[csf('mst_id')];
            $sysCodeArr[$row[csf('mst_id')]]=$sys_code;
        }
    }
        echo "document.getElementById('txt_fab_type_1').value  			= '".$result[0][csf("type")]."';\n";
        echo "document.getElementById('txtconstruction_1').value  	= '".($result[0][csf("construction")])."';\n";
        echo "document.getElementById('txtcomposition_1').value  		= '".( $composition_arr[$data])."';\n"; 
        echo "document.getElementById('yarnCountDeterminationId_1').value  	= '".($data)."';\n";
        echo "document.getElementById('cbocomposition_1').value  		= '".( $composition_arr[$data])."';\n"; 
    
    exit();
}

if($action=="list_view_div"){
    $company_array=return_library_array( "select id, company_name from lib_company",'id','company_name');
    $buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
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

    $row_status = array(1 => "Active", 2 => "InActive", 3 => "Cancelled");
    $sql="select id,company_id,buyer_id,dispo_no,fabric_type,finish_width,fab_construction,fab_composition,fabric_gsm,finish_type,wash_type,print_type,floor_id,room,rack,shelf,sample_ref_type,system_number,status_active from wo_fabric_hanger_archive_mst where is_deleted=0 and id=$data order by id ASC";
    $arr=array (0=>$company_array,1=>$buyer_name_arr,8=>$finish_types,9=> $wash_types,10=>$print_types,11=>$sample_ref_types,12=>$floor_arr,13=>$room_arr,14=>$rack_arr,15=>$shelf_arr,17=>$row_status); 
    echo  create_list_view ( "list_view", "Company,Buyer,Dispo No,Fabric Type,Finish Width,Fab. Construction,Fab. Composition,Fabric GSM/ounce, Finish Type,Wash Type,Print Type,Sample Ref Type,Floor, Room, Rack,Shelf,SYS ID,Status", "120,100,70,100,70,80,120,60,70,63,63,120,63,63,63,63,100","1564","220",0, $sql, "get_php_form_data", "id", "'load_php_data_to_form'", 1, "company_id,buyer_id,0,0,0,0,0,0,finish_type,wash_type,print_type,sample_ref_type,floor_id,room,rack,shelf,0,status_active", $arr , "company_id,buyer_id,dispo_no,fabric_type,finish_width,fab_construction,fab_composition,fabric_gsm,finish_type,wash_type,print_type,sample_ref_type,floor_id,room,rack,shelf,system_number,status_active", "../woven_gmts/requires/fabric_hanger_archive_entry_controller", 'setFilterGrid("list_view",-1);','0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0');
    exit();

}
if($action=="load_php_data_to_form"){
    $company_array=return_library_array( "select id, company_name from lib_company",'id','company_name');
    $nameArray=sql_select( "select id,company_id,buyer_id,dispo_no,fabric_hanger_date,location_id,fabric_type,finish_width,fab_construction,determination_id,fab_composition,fabric_gsm,finish_type,wash_type,print_type,floor_id,room,rack,shelf,bin,sample_ref_type,system_number,status_active,fabric_ounce,article_no from wo_fabric_hanger_archive_mst where id='$data'" );

    foreach ($nameArray as $inf)
    {
        echo "document.getElementById('cbo_company_name').value = '".($inf[csf("company_id")])."';\n";    
        echo "document.getElementById('cbo_buyer_name').value  = '".($inf[csf("buyer_id")])."';\n";
        echo "document.getElementById('txt_dispo_no').value  = '".($inf[csf("dispo_no")])."';\n";
        echo "document.getElementById('date_field').value  = '".change_date_format($inf[csf("fabric_hanger_date")])."';\n";
        echo "load_drop_down( 'requires/fabric_hanger_archive_entry_controller', '".($inf[csf("company_id")])."', 'load_drop_down_location', 'location' );\n";
        echo "document.getElementById('cbo_location_name').value  = '".($inf[csf("location_id")])."';\n";  
        $comStr=$inf[csf('company_id')].'_'.$inf[csf('location_id')];
        echo "load_drop_down( 'requires/fabric_hanger_archive_entry_controller',  '".$comStr."', 'load_drop_down_floor', 'floor_td' );\n";
        echo "document.getElementById('cbo_floor_id').value  = '".($inf[csf("floor_id")])."';\n"; 
        $comFloorStr=$inf[csf('company_id')].'**'.$inf[csf('location_id')].'**'.$inf[csf('floor_id')];
        echo "load_drop_down( 'requires/fabric_hanger_archive_entry_controller', '".$comFloorStr."', 'load_drop_down_room', 'room_td' );\n";
        echo "document.getElementById('cbo_room').value  = '".($inf[csf("room")])."';\n"; 
        $comroomStr=$inf[csf('company_id')].'**'.$inf[csf('location_id')].'**'.$inf[csf('floor_id')].'**'.$inf[csf('room')];
        echo "load_drop_down( 'requires/fabric_hanger_archive_entry_controller', '".$comroomStr."', 'load_drop_down_rack', 'rack_td' );\n";
        echo "document.getElementById('txt_rack').value  = '".($inf[csf("rack")])."';\n"; 

        $comshelfStr=$inf[csf('company_id')].'**'.$inf[csf('location_id')].'**'.$inf[csf('floor_id')].'**'.$inf[csf('room')].'**'.$inf[csf('rack')];
        echo "load_drop_down( 'requires/fabric_hanger_archive_entry_controller', '".$comshelfStr."', 'load_drop_down_shelf', 'shelf_td' );\n";
        echo "document.getElementById('txt_shelf').value  = '".($inf[csf("shelf")])."';\n"; 
        $comshelfStr=$inf[csf('company_id')].'**'.$inf[csf('location_id')].'**'.$inf[csf('floor_id')].'**'.$inf[csf('room')].'**'.$inf[csf('rack')].'**'.$inf[csf('shelf')];
        echo "load_drop_down( 'requires/fabric_hanger_archive_entry_controller', '".$comshelfStr."', 'load_drop_down_bin', 'bin_td' );\n";
        echo "document.getElementById('txt_bin').value  = '".($inf[csf("bin")])."';\n"; 

        echo "document.getElementById('txt_fab_type_1').value  = '".($inf[csf("fabric_type")])."';\n"; 
        echo "document.getElementById('txt_finish_width').value  = '".($inf[csf("finish_width")])."';\n"; 
        echo "document.getElementById('txtconstruction_1').value  = '".($inf[csf("fab_construction")])."';\n"; 
        echo "document.getElementById('yarnCountDeterminationId_1').value  = '".($inf[csf("determination_id")])."';\n";
        echo "document.getElementById('txtcomposition_1').value  = '".($inf[csf("fab_composition")])."';\n"; 
        echo "document.getElementById('txt_fabric_gsm').value  = '".($inf[csf("fabric_gsm")])."';\n"; 
        echo "document.getElementById('cboFinishType_1').value  = '".($inf[csf("finish_type")])."';\n"; 
        echo "document.getElementById('cboWashType_1').value  = '".($inf[csf("wash_type")])."';\n"; 
        echo "document.getElementById('cboPrintType_1').value  = '".($inf[csf("print_type")])."';\n"; 
        //echo "document.getElementById('txt_floor').value  = '".($inf[csf("floor_id")])."';\n"; 
        
        echo "document.getElementById('cboSample_ref_types_1').value  = '".($inf[csf("sample_ref_type")])."';\n"; 
        echo "document.getElementById('cbo_status').value  = '".($inf[csf("status_active")])."';\n"; 
        echo "document.getElementById('update_id').value  = '".($inf[csf("id")])."';\n";
        echo "document.getElementById('txt_system_id').value  = '".($inf[csf("system_number")])."';\n";

        echo "document.getElementById('txt_fabric_ounce').value  = '".($inf[csf("fabric_ounce")])."';\n";
        echo "document.getElementById('txt_article_no').value  = `".($inf[csf("article_no")])."`;\n";

        echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fabric_hanger_archive_info',1);\n";  
    }
    exit();
}
if($action=="fabric_description_popup")
{
    echo load_html_head_contents("Consumption Entry","../../../", 1, 1, $unicode);
    extract($_REQUEST);
    ?>
    <script>
    function toggle( x, origColor )
    {
        var newColor = 'yellow';
        if ( x.style ) {
            x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
        }
    }
    function get_php_form_data(id){
            $("#id").val(id);
            parent.emailwindow.hide();
    }
    </script>
    </head>
    <body>
    <div align="center">
    <form>
    <input type="hidden" id="id" name="id" />
    <?

    $composition_arr=array();
    $lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
    $user_arr=return_library_array( "select user_full_name,id from user_passwd", "id", "user_full_name");
    $lib_group_short=return_library_array( "select id,group_short_name from lib_group where id=1 and status_active=1", "id", "group_short_name");
    $group_short_name=$lib_group_short[1];
    $sql_q="select mst_id, copmposition_id, percent, count_id, type_id from lib_yarn_count_determina_dtls where is_deleted=0  order by id";
                    
    $data_array=sql_select($sql_q);
    if (count($data_array)>0)
    {
        foreach( $data_array as $row )
        {
            $compo_per="";
            if(($row[csf('percent')]*1)>0) $compo_per=$row[csf('percent')]."% "; else $compo_per="";
            if(array_key_exists($row[csf('mst_id')],$composition_arr))
            {
                $composition_arr[$row[csf('mst_id')]]=$composition_arr[$row[csf('mst_id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$compo_per.$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
            }
            else
            {
                $composition_arr[$row[csf('mst_id')]]=$composition[$row[csf('copmposition_id')]]." ".$compo_per.$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]];
            }
                $sys_code=$group_short_name.'-'.$row[csf('mst_id')];
                $sysCodeArr[$row[csf('mst_id')]]=$sys_code;
        }
    }
    $sql="SELECT id,fab_nature_id, type, rd_no, construction, gsm_weight, weight_type, design, fabric_ref, color_range_id,inserted_by,status_active,full_width,cutable_width,shrinkage_l,shrinkage_w from  lib_yarn_count_determina_mst where is_deleted=0 and entry_form=426 order by id DESC";				
    $arr=array (2=>$composition_arr);

    echo  create_list_view ( "list_view", "Type,Construction,Composition", "100,100,200","400","350",0, $sql, "get_php_form_data", "id", "'load_php_data_to_form'",1, "0,0,id", $arr , "type,construction,id", "requires/fabric_determination_controller",'setFilterGrid("list_view",-1);','0,0,0') ;
    ?>
    </form>
    </div>
    </body>
    </html>
    <?
    exit();
}
if($action=="load_drop_down_buyer"){
    echo create_drop_down( "cbo_buyer_name", 100, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
    exit();
}
if ($action=="load_room_rack_self_bin")
{
    $explodeData = explode('*', $data);
    $data=implode('*', $explodeData);
    load_room_rack_self_bin("requires/fabric_hanger_archive_entry_controller",$data);
    exit();
}
        
if ($action=="load_drop_down_location")
{
        echo create_drop_down( "cbo_location_name", 100, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0  order by location_name","id,location_name", 1, "Select Location", 0,"load_floor();" ); 
        exit();
}
if ($action=="load_drop_down_floor")
{
    $data_ref=explode("_",$data);//fnc_load_room_rack_shelf_bin(1);
    $company_id=$data[0];
    $location_id=$data[1];
        $floor_data="select b.floor_id as id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.floor_id  and a.company_id='$data_ref[0]'  and b.location_id in($data_ref[1]) and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0
    group by b.floor_id,a.floor_room_rack_name  order by a.floor_room_rack_name asc";

    echo create_drop_down( "cbo_floor_id", 100, $floor_data,"id,floor_room_rack_name", 1, "--Select--", 0,"load_drop_down( 'requires/fabric_hanger_archive_entry_controller', $('#cbo_company_name').val()+'**'+$('#cbo_location_name').val()+'**'+this.value, 'load_drop_down_room', 'room_td' );" );  	 
    exit();
}
if ($action=="load_drop_down_room")
{
    $data_ref = explode('**', $data);
        $room_data="select b.room_id as id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.room_id  and a.company_id='$data_ref[0]' and b.floor_id in($data_ref[2]) and b.location_id in($data_ref[1]) and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0
    group by b.room_id,a.floor_room_rack_name  order by a.floor_room_rack_name asc";

    echo create_drop_down( "cbo_room", 100, $room_data,"id,floor_room_rack_name", 1, "Select Room", 0,"load_drop_down( 'requires/fabric_hanger_archive_entry_controller', $('#cbo_company_name').val()+'**'+$('#cbo_location_name').val()+'**'+$('#cbo_floor_id').val()+'**'+this.value, 'load_drop_down_rack', 'rack_td' );" );  	 
        exit();
        
}
if ($action=="load_drop_down_rack")
{
    $data_ref = explode('**', $data);
    $room_data="select b.rack_id as id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.rack_id  and a.company_id='$data_ref[0]' and b.floor_id in($data_ref[2]) and b.location_id in($data_ref[1]) and b.room_id in($data_ref[3]) and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0
    group by b.rack_id,a.floor_room_rack_name  order by a.floor_room_rack_name asc";
    

    echo create_drop_down( "txt_rack", 100, $room_data,"id,floor_room_rack_name", 1, "Select Rack", 0,"load_drop_down( 'requires/fabric_hanger_archive_entry_controller', $('#cbo_company_name').val()+'**'+$('#cbo_location_name').val()+'**'+$('#cbo_floor_id').val()+'**'+$('#cbo_room').val()+'**'+this.value, 'load_drop_down_shelf', 'shelf_td' );" );  	 
    exit();
        
}
if ($action=="load_drop_down_shelf")
{
    $data_ref = explode('**', $data);
    $room_data="select b.shelf_id as id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.shelf_id  and a.company_id='$data_ref[0]' and b.location_id in($data_ref[1]) and b.floor_id in($data_ref[2]) and b.room_id in($data_ref[3])   and b.rack_id in($data_ref[4])   and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0
    group by b.shelf_id,a.floor_room_rack_name  order by a.floor_room_rack_name asc";
    

    echo create_drop_down( "txt_shelf", 100, $room_data,"id,floor_room_rack_name", 1, "Select Shelf", 0,"load_drop_down( 'requires/fabric_hanger_archive_entry_controller', $('#cbo_company_name').val()+'**'+$('#cbo_location_name').val()+'**'+$('#cbo_floor_id').val()+'**'+$('#cbo_room').val()+'**'+$('#txt_rack').val()+'**'+this.value, 'load_drop_down_bin', 'bin_td' );" );  	 
    exit();
        
}
if ($action=="load_drop_down_bin")
{
    $data_ref = explode('**', $data);
        $room_data="select b.bin_id as id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.bin_id  and a.company_id='$data_ref[0]' and b.location_id in($data_ref[1]) and b.floor_id in($data_ref[2]) and b.room_id in($data_ref[3])   and b.rack_id in($data_ref[4])  and b.shelf_id in($data_ref[5])   and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0
    group by b.bin_id,a.floor_room_rack_name  order by a.floor_room_rack_name asc";
    
    echo create_drop_down( "txt_bin", 100, $room_data,"id,floor_room_rack_name", 1, "Select Bin", 0,"" );  	 
    exit();
        
}
if($action=="mrr_popup")
{
        echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
        extract($_REQUEST);
        $season_buyer_wise_arr = return_library_array("select id,season_name from  lib_buyer_season","id","season_name");
        $brandArr = return_library_array("select id,brand_name from  lib_buyer_brand ","id","brand_name");
    ?>
        <script>
            function js_set_value(mrr)
            {
                $("#hidden_issue_number").val(mrr); // mrr number
                parent.emailwindow.hide();
            }
            function fnc_buyer()
            {
                load_drop_down( 'fabric_hanger_archive_entry_controller', <?=$company;?>, 'load_drop_down_buyer', 'buyer_td' )
            }
        </script>
    
        </head>
        <body>
            <div align="center" style="width:100%;" >
                <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off" >
                    <table width="700" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
                        <thead>
                            <tr>
                                <th colspan="10" type="hidden"><? //echo create_drop_down( "cbo_string_search_type", 160, $string_search_type,'', 1, "--Searching Type--" ); ?></th>
                            </tr>
                            <tr>
                                <th width="150" class="must_entry_caption">Company Name</th>
                                <th width="150">Buyer Name</th>
                                <th width="100">Dispo No</th>
                                <th width="100" colspan="2"> Date </th>
                                <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:80px" class="formbutton"  /></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td><? echo create_drop_down( "cbo_company_name", 150, "select comp.id,comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $company, "load_drop_down( 'fabric_hanger_archive_entry_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );",1); ?></td>
                                <td id="buyer_td"><?  echo create_drop_down( "cbo_buyer_name", 100, "select id,buyer_name from lib_buyer  where status_active =1 and is_deleted=0  order by buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "" ,0);

                                // echo create_drop_down( "cbo_buyer_name", 100, "select id,buyer_name from lib_buyer  where status_active =1 and is_deleted=0  order by buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "load_drop_down( 'requires/fabric_hanger_archive_entry_controller', this.value+'_'+document.getElementById('cbo_company_name').value);" ,0); ?></td>
                                
                                <td><input type="text" style="width:50px" class="text_boxes"  name="txt_dispo_no" id="txt_dispo_no" /></td>
                                <td colspan="2"><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" placeholder="Date" /> <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px" placeholder="Date" /></td>
                                <td><input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+document.getElementById('txt_dispo_no').value, 'create_list_view_div', 'search_div', 'fabric_hanger_archive_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:80px;" />
                                </td>
                            </tr>
                            <tr>
                                <td align="center" valign="middle" colspan="7"><input type="hidden" id="hidden_issue_number" value="" /></td>
                            </tr>
                        </tbody>
                    </table>
                    <div align="center" valign="top" id="search_div"> </div>
                </form>
            </div>
            <script> 
                fnc_buyer();
            </script>
        </body>
        <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
        </html>
    <?
    exit();
}
if($action=="create_list_view_div"){
    $ex_data = explode("_",$data);
    $buyer = $ex_data[0];
    $from_date = $ex_data[1];
    $to_date = $ex_data[2];
    $company = $ex_data[3];
    $dispo_no = $ex_data[4];
    $date_cond="";
    if($from_date!="" && $to_date!="")
    {
        $from_date=change_date_format($from_date,'yyyy-mm-dd',"-",1);
        $to_date=change_date_format($to_date,'yyyy-mm-dd',"-",1);

        $date_cond=" and fabric_hanger_date between '$from_date' and '$to_date' ";
        
    }
    if($company==0) $company_name=""; else $company_name=" and company_id=$company";
    if($buyer==0) $buyer_conD=""; else $buyer_conD=" and buyer_id=$buyer";
    if($dispo_no=="") $dispo_no_cond=""; else $dispo_no_cond="and dispo_no='$dispo_no'";
    
    $floor_arr=return_library_array( "select b.floor_id as id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.floor_id   and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0
    group by b.floor_id,a.floor_room_rack_name  order by a.floor_room_rack_name asc",'id','floor_room_rack_name');
    $room_arr=return_library_array( "select b.room_id as id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.room_id    and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0
    group by b.room_id,a.floor_room_rack_name  order by a.floor_room_rack_name asc",'id','floor_room_rack_name');
    $rack_arr=return_library_array( "select b.rack_id as id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.rack_id   and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0
    group by b.rack_id,a.floor_room_rack_name  order by a.floor_room_rack_name asc",'id','floor_room_rack_name');
    $shelf_arr=return_library_array( "select b.shelf_id as id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.shelf_id    and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0
    group by b.shelf_id,a.floor_room_rack_name  order by a.floor_room_rack_name asc",'id','floor_room_rack_name');
    $company_array=return_library_array( "select id, company_name from lib_company",'id','company_name');
    $buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
    $row_status = array(1 => "Active", 2 => "InActive", 3 => "Cancelled");
        $sql="select id,company_id,buyer_id,dispo_no,fabric_type,finish_width,fab_construction,fab_composition,fabric_gsm,finish_type,wash_type,print_type,floor_id,room,rack,shelf,sample_ref_type,system_number,status_active from wo_fabric_hanger_archive_mst where is_deleted=0 and company_id=$company $date_cond $dispo_no_cond $buyer_conD order by id ASC";
    $arr=array (0=>$company_array,1=>$buyer_name_arr,8=>$finish_types,9=> $wash_types,10=>$print_types,11=>$sample_ref_types,12=>$floor_arr,13=>$room_arr,14=>$rack_arr,15=>$shelf_arr,17=>$row_status); 
    echo  create_list_view ( "list_view", "Company,Buyer,Dispo No,Fabric Type,Finish Width,Fab. Construction,Fab. Composition,Fabric GSM/ounce, Finish Type,Wash Type,Print Type,Sample Ref Type,Floor, Room, Rack,Shelf,SYS ID,Status", "120,100,70,100,70,80,120,60,70,63,63,120,63,63,63,63,100","1564","220",0, $sql, "js_set_value", "system_number,id", "", 1, "company_id,buyer_id,0,0,0,0,0,0,finish_type,wash_type,print_type,sample_ref_type,floor_id,room,rack,shelf,0,status_active", $arr , "company_id,buyer_id,dispo_no,fabric_type,finish_width,fab_construction,fab_composition,fabric_gsm,finish_type,wash_type,print_type,sample_ref_type,floor_id,room,rack,shelf,system_number,status_active", "../woven_gmts/requires/fabric_hanger_archive_entry_controller", 'setFilterGrid("list_view",-1);','0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0');
    exit();

}
if($action=="fabric_hanger_sticker_print")
{
    $data=explode("***",$data);
    $mst_id=$data[0];
    $system_no=$data[1];

    $PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'qrcode_image'.DIRECTORY_SEPARATOR.$system_no.DIRECTORY_SEPARATOR;
    $PNG_WEB_DIR = 'qrcode_image/'.$system_no.'/';
    foreach (glob($PNG_WEB_DIR."*.png") as $filename) {
        @unlink($filename);
    }
    if (!file_exists($PNG_TEMP_DIR)) mkdir($PNG_TEMP_DIR);
    $filename = $PNG_TEMP_DIR.'test.png';
    $errorCorrectionLevel = 'L';
    $matrixPointSize = 4;

    include "../../../ext_resource/phpqrcode/qrlib.php";
    require_once("../../../ext_resource/mpdf60/mpdf.php");

    $mpdf = new mPDF('',    // mode - default ''
    array(66,44),//array(297,210),		// array(65,210),    // format - A4, for example, default ''
        8,     // font size - default 0
        '',    // default font family
        1,    // margin_left
        1,    // margin right
        1,     // margin top
        1,    // margin bottom
        1,     // margin header
        1,     // margin footer
        'P');
    $i 		=1;
    $html 	='';
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
    
    $company_array=return_library_array( "select id, company_name from lib_company",'id','company_name');
    $working_location_library=return_library_array( "select id, location_name from lib_location", "id", "location_name"  );
    $working_address_library=return_library_array( "select id, address from lib_location", "id", "address"  );
    $sql_name=sql_select("SELECT id,company_id,buyer_id,dispo_no,fabric_hanger_date,location_id,fabric_type,finish_width,fab_construction,determination_id,fab_composition,fabric_gsm,finish_type,wash_type,print_type,floor_id,room,rack,shelf,bin,sample_ref_type,system_number,status_active,fabric_ounce,article_no from wo_fabric_hanger_archive_mst where id='$mst_id'");
    
    
    foreach($sql_name as $val)
    {
        $company_id				=$val[csf('company_id')];
        $buyer_id				=$val[csf('buyer_id')];
        $dispo_no				=$val[csf('dispo_no')];
        $fabric_hanger_date		=change_date_format($val[csf('fabric_hanger_date')]);
        $fab_construction		=$val[csf('fab_construction')];
        $fab_composition 	    =$val[csf('fab_composition')];
        $fabric_type_weave      =$val[csf('fabric_type')];
        $finish_width 			=$val[csf('finish_width')];
        $fabric_gsm 			=$val[csf('fabric_gsm')];
        $finish_type 			=$val[csf('finish_type')];
        $floor_id 			    =$floor_arr[$val[csf('floor_id')]];
        $room 			        =$room_arr[$val[csf('room')]];
        $rack 			        =$rack_arr[$val[csf('rack')]];
        $shelf 			        =$shelf_arr[$val[csf('shelf')]];
        $bin 			        =$bin_arr[$val[csf('bin')]];
        $location               =$val[csf('location_id')];

    }
    $finish_types = array(1=>"Regular",2=>"Peach",3=>"Brush");
    $company_img=sql_select("select image_location  from common_photo_library  where master_tble_id='$company_id' and form_name='company_details' and is_deleted=0 and file_type=1");
    //substr($fabric_type_weave, 0, 20)  substr($finish_type, 0, 20)

        $i=1;
                foreach ($sql_name as $val) 
            {
                $mpdf->AddPage('',    // mode - default ''
                array(66,44),//array(297,210),		// array(65,210),    // format - A4, for example, default ''
                    8,     // font size - default 0
                    '',    // default font family
                    1,    // margin_left
                    1,    // margin right
                    1,     // margin top
                    1,    // margin bottom
                    1,     // margin header
                    1,     // margin footer
                    'P');
                    $qrcode_text = $val[csf("id")] ."**". $val[csf("dispo_no")];
                $html .= '<style>
                            td, th {
                                border: .2px solid black;
                            }
                        </style>';
                        $html .='<table width="100%" border="0" style="border:none;">';
                        $html .='<tr style="border:none;">';
                        $filename = $PNG_TEMP_DIR.'test'.md5($val[csf("dispo_no")]).'.png';
                        QRcode::png($qrcode_text, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
                        $html .= '<td style="border:none; "><table cellpadding="0" cellspacing="0" width="100%" height="100%" class="" style="font-weight:normal;margin:0 auto;font-size:9px;" rules="all" id="" border="none" align="left">
                                <tr>
                                    <td colspan="2" >
                                        <table border="0" width="100%">
                                        <tr>
                                        <td width="30px" align="center" border="none">
                                        <img src="'.base_url($company_img[0][csf("image_location")]).'" height="30" width="30">
                                        </td>
                                        <td  width="50px" style="padding: 0px 0px 0px 0px;margin-top:3px;font-weight:bold;font-size:12px;" align="center">
                                        ' . substr($company_array[$val[csf('company_id')]], 0, 100) .'<br><hr> '.substr($working_address_library[$val[csf('location_id')]], 0, 100).'<br>'.'
                                        </td>
                                        </tr>
                                        </table>
                                    </td>
                                
                                <tr>
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;font-size:9px;"  >DISPO NO</td>
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;font-size:9px;"  width="70%">' . substr($dispo_no=$val[csf('dispo_no')], 0, 15) . '</td>
                                </tr>

                                <tr>
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;font-size:9px;"  >Article No.</td>
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;font-size:9px;">' . substr($dispo_no=$val[csf('article_no')], 0, 15) . '</td>
                                </tr>
                                <tr>
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;font-size:9px;"  >COMPOSITION</td>
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;font-size:9px;">'.$val[csf('fab_composition')] . '</td>
                                </tr>
                                <tr>
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;font-size:9px;" rowspan="2" >WEIGHT</td>
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;font-size:9px;">'.$val[csf('fabric_gsm')] . ' GSM</td>
                                </tr>
                                
                                <tr>
                                    
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;font-size:9px;" >'.$val[csf('fabric_ounce')].' OZ </td>

                                </tr>
                                <tr>
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;font-size:9px;">FINISH TYPE</td>
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;font-size:9px;" >' . substr($finish_types[$val[csf('finish_type')]], 0, 16). '</td>
                                </tr>
                                
                                <tr>
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;font-size:9px;">RACK </td>
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;font-size:9px;">' . substr($rack, 0, 16).'</td>
                                </tr>
                                <tr >
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;font-size:9px;">SHELF</td>
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;font-size:9px;">'. substr($shelf, 0, 17) .'</td>
                                </tr>
                                <tr>
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;font-size:9px;">BIN</td>
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;font-size:9px;" >' . substr($bin, 0, 16). '</td>
                                </tr>
                                <tr>
                                    <td  width="95%" colspan="2"  align="center">
                                        <div id="div_'.$i.'"><img src="'.$PNG_WEB_DIR.basename($filename).'" height="40" width=""></div>
                                    </td>
                                </tr>
                            </table></td>';
                    $html .='</tr>';
                $html .= "</table>";
                $mpdf->WriteHTML($html);
                $html='';
                $i++;
            }
    foreach (glob("*.pdf") as $filename) {
        @unlink($filename);
    }
    $name = 'fabrichanger_' . date('j-M-Y_h-iA') .'_'.$user_id.'.pdf';
    $mpdf->Output($name, 'F');
    echo "1###$name";

    exit();

}

if($action=="fabric_print_button")
{
    $data=explode("**",$data);
    $mst_id=$data[0];
    $system_no=$data[1];

    $sql_name=sql_select("SELECT id,company_id,buyer_id,dispo_no,fabric_hanger_date,location_id,fabric_type,finish_width,fab_construction,determination_id,fab_composition,fabric_gsm,finish_type,wash_type,print_type,floor_id,room,rack,shelf,bin,sample_ref_type,system_number,status_active,fabric_ounce,article_no from wo_fabric_hanger_archive_mst where id='$mst_id'");
    
    
    foreach($sql_name as $val)
    {
        $company_id				=$val[csf('company_id')];
        $buyer_id				=$val[csf('buyer_id')];
        $dispo_no				=$val[csf('dispo_no')];
        $fabric_hanger_date		=change_date_format($val[csf('fabric_hanger_date')]);
        $fab_construction		=$val[csf('fab_construction')];
        $fab_composition 	    =$val[csf('fab_composition')];
        $fabric_type_weave      =$val[csf('fabric_type')];
        $finish_width 			=$val[csf('finish_width')];
        $fabric_gsm 			=$val[csf('fabric_gsm')];
        $finish_type 			=$val[csf('finish_type')];
        $floor_id 			    =$floor_arr[$val[csf('floor_id')]];
        $room 			        =$room_arr[$val[csf('room')]];
        $rack 			        =$rack_arr[$val[csf('rack')]];
        $shelf 			        =$shelf_arr[$val[csf('shelf')]];
        $bin 			        =$bin_arr[$val[csf('bin')]];
        $location               =$val[csf('location_id')];
        $system_no              =$val[csf('system_number')];
    }

    $PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'qrcode_image'.DIRECTORY_SEPARATOR.$system_no.DIRECTORY_SEPARATOR;
    $PNG_WEB_DIR = 'qrcode_image/'.$system_no.'/';
    foreach (glob($PNG_WEB_DIR."*.png") as $filename) {
        @unlink($filename);
    }
    if (!file_exists($PNG_TEMP_DIR)) mkdir($PNG_TEMP_DIR);
    $filename = $PNG_TEMP_DIR.'test.png';
    $errorCorrectionLevel = 'L';
    $matrixPointSize = 4;

    include "../../../ext_resource/phpqrcode/qrlib.php";
    require_once("../../../ext_resource/mpdf60/mpdf.php");

    $mpdf = new mPDF('',    // mode - default ''
    array(66,44),//array(297,210),		// array(65,210),    // format - A4, for example, default ''
        8,     // font size - default 0
        '',    // default font family
        1,    // margin_left
        1,    // margin right
        1,     // margin top
        1,    // margin bottom
        1,     // margin header
        1,     // margin footer
        'P');
    $i 		=1;
    $html 	='';
    
   
    $finish_types = array(1=>"Regular",2=>"Peach",3=>"Brush");
    
    //substr($fabric_type_weave, 0, 20)  substr($finish_type, 0, 20)

        $i=1;$fabricType="";$fabric_type_val="";
                foreach ($sql_name as $val) 
            {
                
                
                if($val[csf("fabric_gsm")] !=""){
                    $fabricType="GSM";
                    $fabricType2="GSM";
                    $fabric_type_val=$val[csf("fabric_gsm")];
                }else{
                    $fabricType="Ounce";
                    $fabricType2="OZ";
                    $fabric_type_val=$val[csf("fabric_ounce")];
                }

                $mpdf->AddPage('',    // mode - default ''
                array(66,44),//array(297,210),		// array(65,210),    // format - A4, for example, default ''
                    8,     // font size - default 0
                    '',    // default font family
                    1,    // margin_left
                    1,    // margin right
                    1,     // margin top
                    1,    // margin bottom
                    1,     // margin header
                    1,     // margin footer
                    'P');
                    $qrcode_text = $val[csf("id")] ."**". $val[csf("dispo_no")];
                $html .= '<style>
                            td, th {
                                border: .2px solid black;
                            }
                        </style>';
                        $html .='<table width="100%" border="0" style="border:none;">';
                        $html .='<tr style="border:none;">';
                        $filename = $PNG_TEMP_DIR.'test'.md5($val[csf("dispo_no")]).'.png';
                        QRcode::png($qrcode_text, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
                        $html .= '<td style="border:none; "><table cellpadding="0" cellspacing="0" width="100%" height="100%" class="" style="font-weight:normal;margin:0 auto;font-size:9px;" rules="all" id="" border="none" align="left">
                                <tr>
                                    <td colspan="2" >
                                        <table border="0" width="100%">
                                        <tr>
                                        <td width="30px" align="center" border="none" style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;font-size:25px;" >DISPO NO -' . substr($dispo_no=$val[csf('dispo_no')], 0, 15) . '
                                        </td>
                                        
                                        </tr>
                                        </table>
                                    </td>
                                
                                <tr>
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;font-size:9px;"  >Fabric Type</td>
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;font-size:9px;"  width="70%">' . substr($val[csf('fabric_type')], 0, 15) . '</td>
                                </tr>

                                <tr>
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;font-size:9px;"  >Finish Width</td>
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;font-size:9px;">' . substr($val[csf('finish_width')], 0, 15) . '</td>
                                </tr>
                                <tr>
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;font-size:9px;"  >Fab. Construction</td>
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;font-size:9px;">'.$val[csf('fab_construction')] . '</td>
                                </tr>
                                <tr>
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;font-size:9px;" >Fab. Composition</td>
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;font-size:9px;">'.$val[csf('fab_composition')] . '</td>
                                </tr>
                                
                                

                                <tr>
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;font-size:9px;">Fabric '.$fabricType.'</td>
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;font-size:9px;" >' . substr($fabric_type_val, 0, 16).' '.$fabricType2.'</td>
                                </tr>
                                
                                <tr>
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;font-size:9px;">Finish Type </td>
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;font-size:9px;">' . substr($finish_types[$val[csf('finish_type')]], 0, 16).'</td>
                                </tr>
                                <tr >
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;font-size:9px;">Wash Type</td>
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;font-size:9px;">'. substr($wash_types[$val[csf('wash_type')]], 0, 17) .'</td>
                                </tr>
                                <tr>
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;font-size:9px;">Print Type</td>
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;font-size:9px;" >' . substr($print_types[$val[csf('print_type')]], 0, 16). '</td>
                                </tr>
                                <tr>
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;font-size:9px;">Sample Ref Type</td>
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;font-size:9px;" >' . substr($sample_ref_types[$val[csf('sample_ref_type')]], 0, 16). '</td>
                                </tr>
                               
                            </table></td>';
                    $html .='</tr>';
                $html .= "</table>";
                $mpdf->WriteHTML($html);
                $html='';
                $i++;
            }
    foreach (glob("*.pdf") as $filename) {
        @unlink($filename);
    }
    $name = 'fabrichanger_' . date('j-M-Y_h-iA') .'_'.$user_id.'.pdf';
    $mpdf->Output($name, 'F');
    echo base_url('order/woven_gmts/requires/'.$name);

    exit();

}
if($action="save_update_delete"){
            $process = array( &$_POST );
            extract(check_magic_quote_gpc( $process )); 
            //echo $_SESSION['menu_id'];die;
            //---------------------------------------------------- Insert Here---------------------------------------------------------------------------
            $txt_fabric_ounce   = str_replace("'","",$txt_fabric_ounce);
            $txt_article_no     = str_replace("'","",$txt_article_no);
            $txt_fabric_gsm     = str_replace("'","",$txt_fabric_gsm);
            if ($operation==0) 
            {
                
                    $con = connect();
                    if($db_type==0)
                    {
                        mysql_query("BEGIN");
                    }
                    if($db_type==0) $year_cond="YEAR(insert_date)";
			        else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			        else $year_cond="";//defined Later
                    check_table_status( $_SESSION['menu_id'],1);
                    $id=return_next_id( "id", "wo_fabric_hanger_archive_mst", 0 ) ;
                    $field_array="id,system_number_prefix, system_number_prefix_num, system_number,company_id,buyer_id,dispo_no,fabric_hanger_date,location_id,fabric_type,finish_width,fab_construction,fab_composition,determination_id,fabric_gsm,finish_type,wash_type,print_type,floor_id,room,rack,shelf,bin,sample_ref_type,fabric_ounce,article_no,inserted_by,insert_date,status_active,is_deleted";
                    $new_return_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'FHA', date("Y",time()), 5, "select system_number_prefix,system_number_prefix_num from wo_fabric_hanger_archive_mst where company_id=$cbo_company_name and $year_cond=".date('Y',time())." order by id DESC", "system_number_prefix", "system_number_prefix_num" ));

                    $data_array="(".$id.",'".$new_return_number[1]."','".$new_return_number[2]."','".$new_return_number[0]."',".$cbo_company_name.",".$cbo_buyer_name.",".$txt_dispo_no.",".$date_field.",".$cbo_location_name.",".$txt_fab_type_1.",".$txt_finish_width.",".$txtconstruction_1.",".$txtcomposition_1.",".$yarnCountDeterminationId_1.",'".$txt_fabric_gsm."',".$cboFinishType_1.",".$cboWashType_1.",".$cboPrintType_1.",".$cbo_floor_id.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$txt_bin.",".$cboSample_ref_types_1.",'".$txt_fabric_ounce."','".$txt_article_no."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_status.",0)"; 
                    //echo "insert into wo_fabric_hanger_archive_mst($field_array)values".$data_array;die;
                    $rID=sql_insert("wo_fabric_hanger_archive_mst",$field_array,$data_array,0);
                    check_table_status( $_SESSION['menu_id'],0);
                    $system_id=$new_return_number[0];
                    if($db_type==2 || $db_type==1 )
                    {
                        if($rID==1)
                        {
                            oci_commit($con);  
                            echo "0**".$id."**".$system_id;
                        }
                        else
                        {
                            oci_rollback($con);
                            echo "10**";
                        }
                    }
                    disconnect($con);
                    die;
               
            }
            //---------------------------------------------------- Update Here---------------------------------------------------------------------------
            else if ($operation==1)
            {
                
                    $con = connect();
                    if($db_type==0)
                    {
                        mysql_query("BEGIN");
                    }
                    
                    $field_array="buyer_id*dispo_no*fabric_hanger_date*location_id*fabric_type*finish_width*fab_construction*fab_composition*determination_id*fabric_gsm*finish_type*wash_type*print_type*floor_id*room*rack*shelf*bin*sample_ref_type*system_number*fabric_ounce*article_no*updated_by*update_date*status_active*is_deleted";
                    $data_array="".$cbo_buyer_name."*".$txt_dispo_no."*".$date_field."*".$cbo_location_name."*".$txt_fab_type_1."*".$txt_finish_width."*".$txtconstruction_1."*".$txtcomposition_1."*".$yarnCountDeterminationId_1."*'".$txt_fabric_gsm."'*".$cboFinishType_1."*".$cboWashType_1."*".$cboPrintType_1."*".$cbo_floor_id."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$txt_bin."*".$cboSample_ref_types_1."*".$txt_system_id."*'".$txt_fabric_ounce."'*'".$txt_article_no."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_status."*0";
                   
                    $rID=sql_update("wo_fabric_hanger_archive_mst",$field_array,$data_array,"id","".$update_id."",1);
                   // echo $rID;
                    
                   
                    if($db_type==2 || $db_type==1 )
                    {
                    if($rID)
                        {
                            oci_commit($con);   
                            echo "1**".str_replace("'","",$update_id);
                        }
                        else{
                            oci_rollback($con);
                            echo "10**".$rID.'=Mamaun';
                        }
                    }
                    disconnect($con);
                    die;
                //}
                //disconnect($con);			
            }
            exit();
            //---------------------------------------------------- Delete Here---------------------------------------------------------------------------
         
}

 

 
?>