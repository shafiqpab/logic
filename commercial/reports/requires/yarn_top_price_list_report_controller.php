<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if ($_SESSION['logic_erp']['user_id'] == "")
{
	header("location:login.php");
	die;
}
$permission = $_SESSION['page_permission'];
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

//--------------------------------------------------------------------------------------------


if ($action == "generate_report")
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	// echo $txt_excange_rate;die;

	$companyArr = return_library_array("select id,company_short_name from lib_company", "id", "company_short_name");

	$supplierArr = return_library_array("select id, short_name from lib_supplier where status_active=1 and is_deleted=0", "id", "short_name");
    $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$merchandiser_arr=return_library_array( "select id,team_member_name from lib_mkt_team_member_info", "id","team_member_name");
	$supplier_arr = return_library_array("select DISTINCT(c.supplier_name),c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company in ($cbo_company) and b.party_type =2 and c.status_active=1 and c.is_deleted=0 order by c.supplier_name", "id", "supplier_name");

    $count_arr=return_library_array("select id, yarn_count from lib_yarn_count WHERE status_active = 1 AND is_deleted = 0 ORDER BY yarn_count",'id','yarn_count');

    $color_library=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name"  );

    $yarn_type = return_library_array("select YARN_TYPE_ID,YARN_TYPE_SHORT_NAME from lib_yarn_type where is_deleted=0 and status_active=1 order by YARN_TYPE_SHORT_NAME", "YARN_TYPE_ID", "YARN_TYPE_SHORT_NAME");


	$buy_short_name_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
	$yarnTestQalityCommentsArr = return_library_array("select prod_id, yarn_quality_coments from inv_yarn_test_mst where  status_active=1 and is_deleted=0", 'prod_id', 'yarn_quality_coments');
	$yarnTestArr = return_library_array("select prod_id, lot_number from inv_yarn_test_mst where status_active=1 and is_deleted=0", 'prod_id', 'lot_number');
	$floor_room_rack_arr = return_library_array("select floor_room_rack_id,floor_room_rack_name from lib_floor_room_rack_mst", 'floor_room_rack_id', 'floor_room_rack_name');
	//echo '<pre>';print_r($yarnTestArr);die;

    //echo  $cbo_company."--".$cbo_count."--".$txt_count_id.'--'.$txt_composition.'--'.$txt_composition_id.'--'.$yarn_type.'--'.$yarn_type_id.'--'.$cbo_price.'--'.$from_date.'--'.$to_date;


    if($from_date !="" && $to_date !=""){
        $pi_date_cond = "AND A.PI_DATE BETWEEN '".change_date_format($from_date,'','',1)."' and '".change_date_format($to_date,'','',1)."'";
    }
    if($cbo_count > 0)
    {
        $count_con = " AND B.COUNT_NAME IN ($cbo_count)";
    }

    if($txt_composition_id!='')
    {
        $com_con = " AND B.YARN_COMPOSITION_ITEM1 IN ($txt_composition_id)";
    }

    if($yarn_type_id!='')
    {
        $type_con = " AND B.YARN_TYPE IN ($yarn_type_id)";
    }

    $main_sql = "SELECT A.ID AS PI_MST_ID,A.PI_NUMBER,A.PI_DATE,A.IMPORTER_ID,A.SUPPLIER_ID,B.WORK_ORDER_NO , B.RATE ,B.COLOR_ID,B.COUNT_NAME,B.YARN_COMPOSITION_ITEM1,B.YARN_TYPE,B.WORK_ORDER_DTLS_ID,C.STYLE_NO,C.JOB_NO,c.JOB_ID,C.MST_ID AS MST_ID
    FROM COM_PI_MASTER_DETAILS A ,COM_PI_ITEM_DETAILS B ,WO_NON_ORDER_INFO_DTLS C
    WHERE A.ID= B.PI_ID AND B.WORK_ORDER_DTLS_ID = C.ID AND A.IMPORTER_ID IN ($cbo_company)  $count_con $com_con $type_con $pi_date_cond AND A.STATUS_ACTIVE=1 AND A.IS_DELETED = 0
    AND B.STATUS_ACTIVE=1 AND B.IS_DELETED = 0  AND C.STATUS_ACTIVE=1 AND C.IS_DELETED = 0 
    ORDER BY a.IMPORTER_ID,b.RATE ASC , a.PI_DATE DESC ";

    // echo $main_sql;
    $main_sql_result = sql_select($main_sql);
    $wo_id_arr = array();
    $job_id_arr = array();
    foreach ($main_sql_result as $row) {
        $yarn_keys = $row['IMPORTER_ID']."**".$row['SUPPLIER_ID']."**".$row['PI_MST_ID']."**".$row['COUNT_NAME']."**".$row['YARN_COMPOSITION_ITEM1']."**".$row['YARN_TYPE']."**".$row['COLOR_ID']."**".$row['RATE'];

        $all_data_arr[$yarn_keys]['PI_NUMBER'] = $row['PI_NUMBER'];
        $all_data_arr[$yarn_keys]['PI_DATE'] = $row['PI_DATE'];
        $all_data_arr[$yarn_keys]['IMPORTER_ID'] = $row['IMPORTER_ID'];
        $all_data_arr[$yarn_keys]['SUPPLIER_ID'] = $row['SUPPLIER_ID'];
        $all_data_arr[$yarn_keys]['WORK_ORDER_NO'] = $row['WORK_ORDER_NO'];
        $all_data_arr[$yarn_keys]['RATE'] = $row['RATE'];
        $all_data_arr[$yarn_keys]['COLOR_ID'] = $row['COLOR_ID'];
        $all_data_arr[$yarn_keys]['COUNT_NAME'] = $row['COUNT_NAME'];
        $all_data_arr[$yarn_keys]['YARN_COMPOSITION_ITEM1'] = $row['YARN_COMPOSITION_ITEM1'];
        $all_data_arr[$yarn_keys]['YARN_TYPE'] = $row['YARN_TYPE'];
        $all_data_arr[$yarn_keys]['WORK_ORDER_DTLS_ID'] = $row['WORK_ORDER_DTLS_ID'];
        $all_data_arr[$yarn_keys]['MST_ID'] = $row['MST_ID'];
        $all_data_arr[$yarn_keys]['JOB_NO_MST'] = $row['JOB_NO'];
        $supplier_ids .= $row['SUPPLIER_ID'].',';
        $wo_id_arr[$row['MST_ID']] = $row['MST_ID'];
        $job_id_arr[$row['JOB_ID']] = $row['JOB_ID'];

        $pi_wo[$row['PI_NUMBER']]['STYLE_NO'] .= $row['STYLE_NO'].",";
        $pi_wo[$row['PI_NUMBER']]['JOB_NO'] .= substr($row['JOB_NO'],-3).",";
        $pi_wo[$row['PI_NUMBER']]['MST_ID'] .= $row['MST_ID'].",";

        $wo_details[$row['MST_ID']]['WORK_ORDER_NO'] = substr($row['WORK_ORDER_NO'],-3);

    }
    $all_supp_id = ltrim(implode(",", array_unique(explode(",", chop($supplier_ids, ",")))), ',');
    
   

    $stock_sql = "SELECT A.ID,  A.COMPANY_ID ,A.SUPPLIER_ID,A.YARN_COUNT_ID,A.YARN_COMP_TYPE1ST,A.COLOR, A.YARN_TYPE,A.CURRENT_STOCK
    FROM PRODUCT_DETAILS_MASTER A
    WHERE A.COMPANY_ID IN ($cbo_company)  AND A.SUPPLIER_ID IN ($all_supp_id) and A.STATUS_ACTIVE = 1 AND
    A.IS_DELETED = 0";
	//echo $stock_sql;//die;
	$result = sql_select($stock_sql);

    foreach ($result  as $row) 
    {
        // $stock_keys = $row['YARN_COUNT_ID']."**".$row['YARN_COMP_TYPE1ST']."**".$row['YARN_TYPE']."**".$row['COLOR'];
        $stock_data_arr[$row['COMPANY_ID']][$row['SUPPLIER_ID']][$row['YARN_COUNT_ID']][$row['YARN_COMP_TYPE1ST']][$row['YARN_TYPE']][$row['COLOR']]['CURRENT_STOCK'] += $row['CURRENT_STOCK'];
    }
    //  echo "<pre>";
    //  print_r($stock_data_arr);
    
    $sql_job=sql_select("SELECT BUYER_NAME,JOB_NO,DEALING_MARCHANT FROM wo_po_details_master where COMPANY_NAME in($cbo_company)");
    $buyer_marcent_arr=array();
    foreach($sql_job as $val){
        $buyer_marcent_arr[$val["JOB_NO"]]['BUYER_NAME']=$val["BUYER_NAME"];
        $buyer_marcent_arr[$val["JOB_NO"]]['DEALING_MARCHANT'].=$val["DEALING_MARCHANT"];
    }

    ob_start();	
    
    ?>
    <div width="1770">	
		<table cellspacing="0" width="1770"  border="1" rules="all" class="rpt_table"  align="left">
			<thead>
				<tr>
					<th width="40"><p> SL</p></th>
					<th width="80"><p> Company</p></th>
					<th width="50"><p> Count</p></th>
					<th width="180"><p> Composition</p></th>
					<th width="100"><p> Yarn Type</p></th>
					<th width="120"><p> Color</p></th>
					<th width="80"><p> Stock Qty</p></th>
					<th width="80"><p> Unit Price Lbs</p></th>
					<th width="150"><p> Dyeing Mill</p></th>
					<th width="200"><p> Style Ref</p></th>
					<th width="120"><p> Job No</p></th>
					<th width="100"><p> Buyer</p></th>
					<th width="100"><p>Dealing Merchant</p></th>
					<th width="120"><p> PI No</p></th>
					<th width="100"><p> PI Date</p></th>
					<th ><p> WO No</p></th>
				</tr>
			</thead>
        </table>

        <div style="width:1770px; overflow-y:scroll; max-height:380px; margin-left: 2px;" id="scroll_body">
            <table class="rpt_table" border="1" rules="all" width="1770" cellpadding="0" cellspacing="0" id="table_body" align="left">
			<tbody>
            <?
            $i=1;
            foreach($all_data_arr as $row)
            {
                if (fmod($i,2)==0) $bgcolor='#E9F3FF';
                else $bgcolor='#FFFFFF';
                
                $cur_stock = $stock_data_arr[$row['IMPORTER_ID']][$row['SUPPLIER_ID']][$row['COUNT_NAME']][$row['YARN_COMPOSITION_ITEM1']][$row['YARN_TYPE']][$row['COLOR_ID']]['CURRENT_STOCK'];

                ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="40"><p><?echo $i;?></p></td>
                    <td width="80"><p><?echo $companyArr[$row['IMPORTER_ID']];?></p></td>
                    <td width="50"><p><?echo $count_arr[$row['COUNT_NAME']];?></p></td>
                    <td width="180" style="word-break: break-all;"><p><?echo $composition[$row['YARN_COMPOSITION_ITEM1']];?></p></td>
                    <td width="100"><p><?echo $yarn_type[$row['YARN_TYPE']]?></p></td>
                    <td width="120" style="word-break: break-all;" ><p><? echo $color_library[$row['COLOR_ID']]?></p></td>
                    <td width="80" align="right"><p><? echo number_format($cur_stock,2);?></p></td> 
                    <td width="80" align="right"><b><? echo number_format($row['RATE'],4); ?></b>&nbsp;</td>
                    <td width="150" style="word-break: break-all;"><p><? echo $supplier_arr[$row['SUPPLIER_ID']];?></p></td>
                    <td width="200" style="word-break: break-all;"><p><? 
                        echo ltrim(implode(",", array_unique(explode(",", chop($pi_wo[$row['PI_NUMBER']]['STYLE_NO'], ",")))), ',');?></p>
                    </td>  
                    <td width ="120" style="word-break: break-all;"><p><? 
                        echo ltrim(implode(",", array_unique(explode(",", chop($pi_wo[$row['PI_NUMBER']]['JOB_NO'], ",")))), ',');?></p>
                    </td>  
                    <td width="100" style="word-break: break-all;"><p><? echo $buyer_arr[$buyer_marcent_arr[$row["JOB_NO_MST"]]['BUYER_NAME']];?></p></td>
                    <td width="100" style="word-break: break-all;"><p><? echo $merchandiser_arr[$buyer_marcent_arr[$row["JOB_NO_MST"]]['DEALING_MARCHANT']];?></p></td>                    
                    <td width="120" style="word-break: break-all;"><p><?echo $row['PI_NUMBER'];?></p></td>
                    <td width="100"><p><?echo change_date_format($row['PI_DATE']);?></p></td>
                            
                    <td style="word-break: break-all;"> <p>
                        <?
                        $wo_no_dtls_arr = array_filter(array_unique(explode(",", $pi_wo[$row['PI_NUMBER']]['MST_ID'])));
                        foreach ($wo_no_dtls_arr as $wo_no_dtls) 
                        {
                            ?>
                            <a href='##' onClick="generate_report2(<? echo $row['IMPORTER_ID']. "," . $wo_no_dtls; ?>)"><? echo ltrim(implode(",", array_unique(explode(",", chop( $wo_details[$wo_no_dtls]['WORK_ORDER_NO'], ",")))), ','); ?></a>
                            <?
                        }
                        ?></p>
                    </td>

                </tr>
                <?
                $i++;
            }
                    
            ?>
			
			</tbody>
		    </table>
        </div>	
	</div>
    <?

    foreach (glob("$user_id*.xls") as $filename)
    {
        @unlink($filename);
    }
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc,ob_get_contents());
    echo "$total_data****$filename";
    exit();
}

if($action == "yarn_count_popup")
{
    echo load_html_head_contents("Yarn Count Info","../../../", 1, 1, '','1','');
    extract($_REQUEST);
    ?>
    <script>
    var selected_id = new Array(); var selected_name = new Array();

    function check_all_data()
    {
        var tbl_row_count = document.getElementById( 'table_body' ).rows.length;

        tbl_row_count = tbl_row_count-1;
        for( var i = 1; i <= tbl_row_count; i++ ) {
            js_set_value( i );
        }
    }

    function toggle( x, origColor )
    {
        var newColor = 'yellow';
        if ( x.style ) {
            x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
        }
    }

    function js_set_value( str )
    {

        toggle( document.getElementById( 'search' + str ), '#FFFFCC' );

        if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
            selected_id.push( $('#txt_individual_id' + str).val() );
            selected_name.push( $('#txt_individual' + str).val() );

        }
        else {
            for( var i = 0; i < selected_id.length; i++ ) {
                if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
            }
            selected_id.splice( i, 1 );
            selected_name.splice( i, 1 );
        }

        var id = ''; var name = '';
        for( var i = 0; i < selected_id.length; i++ ) {
            id += selected_id[i] + ',';
            name += selected_name[i] + ',';
        }

        id = id.substr( 0, id.length - 1 );
        name = name.substr( 0, name.length - 1 );

        $('#hidden_yarn_count_id').val(id);
        $('#hidden_yarn_count').val(name);
    }
    </script>
    </head>
    <fieldset style="width:390px">

        <input type="hidden" name="hidden_yarn_count" id="hidden_yarn_count" value="">
        <input type="hidden" name="hidden_yarn_count_id" id="hidden_yarn_count_id" value="">
        <table width="370" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
            <thead>
                <tr>
                    <th width="50">SL</th>
                    <th width="">Yarn Count Name</th>
                </tr>
            </thead>
        </table>
        <div style="width:390px; overflow-y:scroll; max-height:300px" id="scroll_body">
            <table width="370" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
        <?
        $result=sql_select("select id,yarn_count from lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count");
        $i = 1;
        foreach ($result as $row)
        {
            if ($i % 2 == 0)
                $bgcolor = "#E9F3FF";
            else
                $bgcolor = "#FFFFFF";
            ?>
            <tr bgcolor="<? echo $bgcolor; ?>" onclick="js_set_value(<? echo $i; ?>)" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>">
                <td width="50">
                    <? echo $i; ?>
                    <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $row[csf("id")]; ?>"/>
                    <input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $row[csf("yarn_count")]; ?>"/>
                </td>
                <td width=""><p><? echo $row[csf("yarn_count")]; ?></p></td>
            </tr>
            <?
            $i++;
        }
        ?>

        </table>
        </div>
        <table width="390" cellspacing="0" cellpadding="0" style="border:none" align="center">
            <tr>
                <td align="center" height="30" valign="bottom">
                    <div style="width:100%">
                        <div style="width:50%; float:left" align="left">
                            <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
                        </div>
                        <div style="width:50%; float:left" align="left">
                            <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </fieldset>
    <script type="text/javascript">
        setFilterGrid('table_body',-1);
    </script>
    <?
}


if($action == "composition_popup")
{
    echo load_html_head_contents("Composition Info","../../../", 1, 1, '','1','');
    extract($_REQUEST);
    ?>
    <script>
    var selected_id = new Array(); var selected_name = new Array();

    function check_all_data()
    {
        var tbl_row_count = document.getElementById( 'table_body' ).rows.length;

        tbl_row_count = tbl_row_count-1;
        for( var i = 1; i <= tbl_row_count; i++ ) {
            js_set_value( i );
        }
    }

    function toggle( x, origColor )
    {
        var newColor = 'yellow';
        if ( x.style ) {
            x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
        }
    }

    function set_all()
    {
        var old=document.getElementById('txt_pre_composition_row_id').value;
        if(old!="")
        {
            old=old.split(",");
            for(var k=0; k<old.length; k++)
            {
                js_set_value( old[k] )
            }
        }
    }

    function js_set_value( str )
    {

        toggle( document.getElementById( 'search' + str ), '#FFFFCC' );

        if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
            selected_id.push( $('#txt_individual_id' + str).val() );
            selected_name.push( $('#txt_individual' + str).val() );

        }
        else {
            for( var i = 0; i < selected_id.length; i++ ) {
                if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
            }
            selected_id.splice( i, 1 );
            selected_name.splice( i, 1 );
        }

        var id = ''; var name = '';
        for( var i = 0; i < selected_id.length; i++ ) {
            id += selected_id[i] + ',';
            name += selected_name[i] + ',';
        }

        id = id.substr( 0, id.length - 1 );
        name = name.substr( 0, name.length - 1 );

        $('#hidden_composition_id').val(id);
        $('#hidden_composition').val(name);
    }
    </script>
    </head>
    <fieldset style="width:390px">
        <legend>Yarn Receive Details</legend>
        <input type="hidden" name="hidden_composition" id="hidden_composition" value="">
        <input type="hidden" name="hidden_composition_id" id="hidden_composition_id" value="">
        <table width="370" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
            <thead>
                <tr>
                    <th colspan="2">
                        <? echo create_drop_down( "cbo_string_search_type", 150, $string_search_type,'', 1, "-- Searching Type --" ); ?>
                    </th>
                </tr>
                <tr>
                    <th width="50">SL</th>
                    <th width="">Composition Name</th>
                </tr>
            </thead>
        </table>
        <div style="width:390px; overflow-y:scroll; max-height:300px" id="scroll_body">
            <table width="370" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
        <?
        $i = 1;

        $result=sql_select("select id,composition_name from  lib_composition_array where status_active=1 and is_deleted=0 order by composition_name");
        $pre_composition_id_arr=explode(",",$pre_composition_id);
        foreach ($result as $row)
        {
            if ($i % 2 == 0)
                $bgcolor = "#E9F3FF";
            else
                $bgcolor = "#FFFFFF";


            if(in_array($row[csf("id")],$pre_composition_id_arr))
            {
                if($pre_composition_ids=="") $pre_composition_ids=$i; else $pre_composition_ids.=",".$i;
            }
            ?>
            <tr bgcolor="<? echo $bgcolor; ?>" onclick="js_set_value(<? echo $i; ?>)" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>">
                <td width="50">
                    <? echo $i; ?>
                    <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $row[csf("id")]; ?>"/>
                    <input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $row[csf("composition_name")]; ?>"/>
                </td>
                <td width=""><p><? echo $row[csf("composition_name")]; ?></p></td>
            </tr>
            <?
            $i++;
        }
        ?>
        <input type="hidden" name="txt_pre_composition_row_id" id="txt_pre_composition_row_id" value="<?php echo $pre_composition_ids; ?>"/>
        </table>
        </div>
        <table width="390" cellspacing="0" cellpadding="0" style="border:none" align="center">
            <tr>
                <td align="center" height="30" valign="bottom">
                    <div style="width:100%">
                        <div style="width:50%; float:left" align="left">
                            <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
                        </div>
                        <div style="width:50%; float:left" align="left">
                            <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </fieldset>
    <script type="text/javascript">
        setFilterGrid('table_body',-1);
        set_all();
    </script>
    <?
}

if($action == "yarn_type_popup")
{
    echo load_html_head_contents("Yarn Type Info","../../../", 1, 1, '','1','');
    extract($_REQUEST);
    ?>
    <script>
    var selected_id = new Array(); var selected_name = new Array();

    function check_all_data()
    {
        var tbl_row_count = document.getElementById( 'table_body' ).rows.length;

        tbl_row_count = tbl_row_count-1;
        for( var i = 1; i <= tbl_row_count; i++ ) {
            js_set_value( i );
        }
    }

    function toggle( x, origColor )
    {
        var newColor = 'yellow';
        if ( x.style ) {
            x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
        }
    }

    function js_set_value( str )
    {

        toggle( document.getElementById( 'search' + str ), '#FFFFCC' );

        if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
            selected_id.push( $('#txt_individual_id' + str).val() );
            selected_name.push( $('#txt_individual' + str).val() );

        }
        else {
            for( var i = 0; i < selected_id.length; i++ ) {
                if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
            }
            selected_id.splice( i, 1 );
            selected_name.splice( i, 1 );
        }

        var id = ''; var name = '';
        for( var i = 0; i < selected_id.length; i++ ) {
            id += selected_id[i] + ',';
            name += selected_name[i] + ',';
        }

        id = id.substr( 0, id.length - 1 );
        name = name.substr( 0, name.length - 1 );

        $('#hidden_yarn_type_id').val(id);
        $('#hidden_yarn_type').val(name);
    }
    </script>
    </head>
    <fieldset style="width:390px">
        <input type="hidden" name="hidden_yarn_type" id="hidden_yarn_type" value="">
        <input type="hidden" name="hidden_yarn_type_id" id="hidden_yarn_type_id" value="">
        <table width="370" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
            <thead>
                <tr>
                    <th width="50">SL</th>
                    <th width="">Yarn Type Name</th>
                </tr>
            </thead>
        </table>
        <div style="width:390px; overflow-y:scroll; max-height:300px" id="scroll_body">
            <table width="370" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
        <?
        $i = 1;
        foreach ($yarn_type as $key=> $val)
        {
            //var_dump($val);
            if ($i % 2 == 0)
                $bgcolor = "#E9F3FF";
            else
                $bgcolor = "#FFFFFF";
            ?>
            <tr bgcolor="<? echo $bgcolor; ?>" onclick="js_set_value(<? echo $i; ?>)" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>">
                <td width="50">
                    <? echo $i; ?>
                    <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $key; ?>"/>
                    <input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $val; ?>"/>
                </td>
                <td width=""><p><? echo $val; ?></p></td>
            </tr>
            <?
            $i++;
        }
        ?>

        </table>
        </div>
        <table width="390" cellspacing="0" cellpadding="0" style="border:none" align="center">
            <tr>
                <td align="center" height="30" valign="bottom">
                    <div style="width:100%">
                        <div style="width:50%; float:left" align="left">
                            <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
                        </div>
                        <div style="width:50%; float:left" align="left">
                            <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </fieldset>
    <script type="text/javascript">
        setFilterGrid('table_body',-1);
    </script>
    <?
}

if ($action=="print_to_html_report")
{
    extract($_REQUEST);
    $data=explode('*',$data);
    echo load_html_head_contents($data[2],"../../", 1, 1, $unicode,'',''); 
    // print_r ($data);
  
    $user_level_library=return_library_array( "select id, user_level from user_passwd where id=$user_id", "id", "user_level"  );
    //if(($data[4]==1 || $data[4]==0) && $user_level_library[$user_id]==2)
    if(($data[4]==1 && $user_level_library[$user_id]==2) || ($data[4]==0))
    {
            
        $company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
        $location=return_field_value("city","lib_company","id=$data[0]" );
        $address=return_field_value("address","lib_location","id=$data[0]");
        $lib_country_arr=return_library_array( "select id,country_name from lib_country","id", "country_name"  );
        
    
        $item_name_arr=return_library_array("select id,item_name from lib_item_group", "id","item_name");
        $supplier_name_library = return_library_array('SELECT id,supplier_name FROM lib_supplier','id','supplier_name');
        $lib_terms_condition=return_library_array( "select id, terms from lib_terms_condition",'id','terms');
        $color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
        $count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
        $buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
        

        $sql_data = sql_select("SELECT distinct a.id, a.wo_number_prefix_num,b.requisition_no,d.booking_no, a.wo_number, a.buyer_po, a.delivery_place, a.wo_date, a.currency_id, a.supplier_id, a.attention, a.buyer_name, a.style, a.wo_basis_id, a.item_category, a.delivery_date, a.source, a.pay_mode,a.payterm_id, a.remarks,a.do_no, a.insert_date  FROM  wo_non_order_info_mst a,wo_non_order_info_dtls b,inv_purchase_requisition_mst c,inv_purchase_requisition_dtls d WHERE a.id=b.mst_id and c.id=d.mst_id and c.requ_no=b.requisition_no and a.id = $data[1] and b.mst_id=$data[1]");

        
        $requisition_no='';
        $booking_no='';
        foreach($sql_data as $row)
        {
        //	echo '<pre>';
        //	print_r($row);
            $work_order_no=$row[csf("wo_number")];
            $item_category_id=$row[csf("item_category")];
            $supplier_id=$row[csf("supplier_id")];
            $work_order_date=$row[csf("wo_date")];
            $currency_id=$row[csf("currency_id")];
            $buyer_name=$row[csf("buyer_name")];
            $style=$row[csf("style")];
            $wo_basis_id=$row[csf("wo_basis_id")];
            $pay_mode_id=$row[csf("pay_mode")];
            $pay_term_id=$row[csf("payterm_id")];
            $source=$row[csf("source")];
            $delivery_date=$row[csf("delivery_date")];
            $attention=$row[csf("attention")];
    
            $delivery_place=$row[csf("delivery_place")];
            $do_no=$row[csf("do_no")];
            $remarks=$row[csf("remarks")];
            $insert_date=$row[csf("insert_date")];
            $requisition_no.=$row[csf("requisition_no")].',';
            if($row[csf("booking_no")]=='0')
            {
                continue;
            }
            else
            {
                $booking_no.=$row[csf("booking_no")].',';
            }
           
        }
        //$pay_mode=array(1=>"Credit",2=>"Import",3=>"In House",4=>"Cash",5=>"Within Group");
        if($pay_mode_id=='1')
        {
        $pay_mode_str='Credit';
        }
        else if($pay_mode_id=='2')
        {
        $pay_mode_str='Import';
        }
            else if($pay_mode_id=='3')
        {
        $pay_mode_str='In House';
        }
            else if($pay_mode_id=='4')
        {
        $pay_mode_str='Cash';
        }
            else if($pay_mode_id=='5')
        {
        $pay_mode_str='Within Group';
        }
        //$source=array(1=>"Abroad",2=>"EPZ",3=>"Non-EPZ");
        
            if($source=='1')
            {
            $source_str='Abroad';
            }else if($source=='2')
            {
            $source_str='EPZ';
            }else if($source=='3')
            {
            $source_str='Non-EPZ';
            }
            
        //$pay_mode=return_field_value("pa","lib_company","id=$data[0]" );
        $sql_job=sql_select("select a.id, a.job_no, a.style_ref_no, a.buyer_name,b.id as po_id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
        foreach($sql_job as $row)
        {
            $buyer_job_arr[$row[csf("po_id")]]["po_id"]=$row[csf("po_id")];
            $buyer_job_arr[$row[csf("po_id")]]["po_number"]=$row[csf("po_number")];
            $buyer_job_arr[$row[csf("po_id")]]["buyer_name"]=$row[csf("buyer_name")];
            $buyer_job_arr[$row[csf("po_id")]]["job_no"]=$row[csf("job_no")];
            $buyer_job_arr[$row[csf("po_id")]]["style_ref_no"]=$row[csf("style_ref_no")];
        }
    
    
        $sql_supplier = sql_select("SELECT id,supplier_name,contact_no,country_id,web_site,email,address_1,address_2,address_3,address_4 FROM  lib_supplier WHERE id = '$supplier_id'");
    
        foreach($sql_supplier as $supplier_data) 
        {//contact_no 	
            $row_mst[csf('supplier_id')];
            
            if($supplier_data[csf('address_1')]!='')$address_1 = $supplier_data[csf('address_1')].','.' ';else $address_1='';
            if($supplier_data[csf('address_2')]!='')$address_2 = $supplier_data[csf('address_2')].','.' ';else $address_2='';
            if($supplier_data[csf('address_3')]!='')$address_3 = $supplier_data[csf('address_3')].','.' ';else $address_3='';
            if($supplier_data[csf('address_4')]!='')$address_4 = $supplier_data[csf('address_4')].','.' ';else $address_4='';
            if($supplier_data[csf('contact_no')]!='')$contact_no = $supplier_data[csf('contact_no')].','.' ';else $contact_no='';
            if($supplier_data[csf('web_site')]!='')$web_site = $supplier_data[csf('web_site')].','.' ';else $web_site='';
            if($supplier_data[csf('supplier_name')]!='')$supplier_name = $supplier_data[csf('supplier_name')].','.' ';else $supplier_name='';
            if($supplier_data[csf('email')]!='')$email = $supplier_data[csf('email')].','.' ';else $email='';
            //if($supplier_data[csf('country_id')]!=0)$country = $supplier_data[csf('country_id')].','.' ';else $country='';
            $country = $supplier_data['country_id'];
            $supplier_name=$supplier_name;
            $supplier_address = $address_1;
            $supplier_country =$country;
            $supplier_phone =$contact_no;
            $supplier_email = $email;
        }
        $image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
        $varcode_booking_no=$work_order_no;
        ?>
        <div style="width:930px;">
        <table width="900" cellspacing="0" align="center">
            <tr>
                <td rowspan="3" width="70"><img src="../../../<? echo $image_location; ?>" height="70" width="200"></td>
                <td colspan="2" style="font-size:xx-large;" align="center"><strong><? echo $company_library[$data[0]]; ?></strong></td>
                <td rowspan="3" id="barcode_img_id"> </td>
            </tr>
            <tr class="form_caption">
                <td colspan="2" align="center" style="font-size:14px"><? echo $location; ?></td>  
            </tr>
            <tr>
                <td colspan="2" align="center" style="font-size:x-large"><strong><? echo $data[2]; ?></strong></td>
            </tr>
        </table>
        <table width="900" cellspacing="0" align="center">
            <tr>
                <td width="300" align="left"><strong>To</strong>,&nbsp;<? echo $attention ?></td>
                <td width="150"><strong>WO Number:</strong></td>
                <td width="150" align="left"><? echo $work_order_no; ?></td>
                <td><strong>Pay Mode:</strong></td>
                <td align="left"><? echo $pay_mode_str; ?></td>
            </tr>
            <tr>
                <td rowspan="4"><? echo $supplier_name_library[$supplier_id]; echo "<br>"; echo $supplier_address;  echo  $lib_country_arr[$country]; echo "<br>"; echo "Mobile :".$supplier_phone; echo "<br>"; echo "Mail :".$supplier_email; ?></td>
                <td width="150" align="left" ><strong>WO Date :</strong></td>
                <td width="150" align="left"><? echo change_date_format($work_order_date); ?></td>
                <td><strong>Currency:</strong></td>
                <td align="left"><? echo $currency[$currency_id]; ?></td>
            </tr>
            <tr>
                <td ><strong>Delivery Date :</strong></td>
                <td><? echo change_date_format($delivery_date); ?></td>
                <td align="left"><strong>Source</strong></td>
                <td align="left" ><? echo $source_str; ?></td>
            </tr>
            <tr>
                <td align="left" ><strong>Print Date:</strong></td>
                <td> <? $pc_day_time=explode(" ",$pc_date_time); echo change_date_format($pc_day_time[0]); echo " ".$pc_day_time[1]." ".$pc_day_time[2]; ?></td>
                            <td align="left" ><strong>WO Basis:</strong></td>
                <td align="left" ><? echo $wo_basis[$wo_basis_id]; ?></td>
            </tr>
            <tr>
                <td ><strong>Pay Term :</strong></td>
                <td><? echo $pay_term[$pay_term_id]; ?></td>
                <td align="left"><strong>Tenor</strong></td>
                <td align="left" ><? echo $source; ?></td>
            </tr>
            <tr>
                <td></td>
                <td ><strong>Req.NO:</strong></td>
                <td colspan="3"><? echo $requisition_no; ?></td>
                
            </tr>
            <tr>
                <td></td>
                <td ><strong>Fab.Booking No :</strong></td>
                <td colspan="3"><? echo $booking_no;  ?></td>
            </tr>
            <tr>
                <td>Dear Sir,</td>
            </tr>
            <tr>
                <td colspan="3">
                Pleased to inform You that Your price offer has been accepted with the following terms .							
                </td>
            </tr>
        </table>
                <br>
                <?
                if($wo_basis_id==3)
                {
                    $buy_job_sty="Buyer Job Style";
                }
                else
                {
                    $buy_job_sty="Buyer Style";
                }
                ?>
        <table align="center" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center">
                <th width="30">SL</th>
                <th width="70">Color</th>
                <th width="60">Count</th>
                <th width="250">Item Description</th>
                <th width="50" >UOM</th>
                <th width="70">Quantity </th>
                <th width="60">Rate</th> 
                <th width="60">Amount</th>
            </thead>
            <tbody>
    <?
        $store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
        
        $i=1; $buy_job_sty_val="";$carrency_id="";
        $mst_id=$dataArray[0][csf('id')];
    
        $sql_dtls="Select a.po_breakdown_id, a.color_name, a.yarn_count, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.uom, a.supplier_order_quantity, a.rate, a.amount, b.currency_id from wo_non_order_info_dtls a, wo_non_order_info_mst b  where a.mst_id=b.id and a.mst_id='$data[1]' and a.status_active=1 and a.is_deleted=0";
        $sql_result = sql_select($sql_dtls); 	
        foreach($sql_result as $row)
        {
                $feb_des='';
                if($row[csf("yarn_comp_type2nd")]==0)
                {
                    $feb_des=$composition[$row[csf("yarn_comp_type1st")]].' '.$row[csf("yarn_comp_percent1st")].' %, '.$yarn_type[$row[csf("yarn_type")]];
                }
                else if( $row[csf("yarn_comp_type2nd")]!=0)
                {
                    $feb_des=$composition[$row[csf("yarn_comp_type1st")]].' '.$row[csf("yarn_comp_percent1st")].' %,'.$composition[$row[csf("yarn_comp_type2nd")]].' '.$row[csf("yarn_comp_percent2nd")].' %, '.$yarn_type[$row[csf("yarn_type")]];
                }
                
                $key=$row[csf("po_breakdown_id")].$row[csf("color_name")].$row[csf("yarn_count")].$feb_des.$row[csf("uom")];
                $dataArr[$key]=array(
                    po_breakdown_id=>$row[csf("po_breakdown_id")],
                    color_name=>$row[csf("color_name")],
                    yarn_count=>$row[csf("yarn_count")],
                    uom=>$row[csf("uom")],
                    feb_des=>$feb_des
                );
                $qtyArr[$key]+=$row[csf("supplier_order_quantity")];
                $amuArr[$key]+=$row[csf("amount")];
                
                $carrency_id=$row[csf('currency_id')];
                if($carrency_id==1){$paysa_sent="Paisa";} else if($carrency_id==2){$paysa_sent="CENTS";}         
        }
        
        //var_dump($dataArr);
        
        //var_dump($amuArr);
        //	var_dump($qtyArr);
        
        //echo $sql_dtls;
        $sql_result = sql_select($sql_dtls); 	
        foreach($dataArr as $key=>$row)
        {
            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                if($wo_basis_id==2)
                {
                    $buyer_name_val="";
                    $buyer_id=explode(',',$buyer_name);
                    foreach($buyer_id as $val)
                    {
                        if($buyer_name_val=="") $buyer_name_val=$buyer_arr[$val]; else $buyer_name_val.=', '.$buyer_arr[$val];
                    }
                    
                    $buy_job_sty_val=$buyer_name_val."<br>".$style;
                }
                else if($wo_basis_id==3)
                {
                    if($row["po_breakdown_id"]!="" && $row["po_breakdown_id"]!=0)
                    {
                        $buyer_name_val=$buyer_arr[$buyer_job_arr[$row["po_breakdown_id"]]["buyer_name"]]."<br>".$buyer_job_arr[$row["po_breakdown_id"]]["job_no"]."<br>".$buyer_job_arr[$row["po_breakdown_id"]]["style_ref_no"]."<br>";
                    }
                    $buy_job_sty_val=$buyer_name_val;
                }   
                
            ?>
                <tr bgcolor="<? echo $bgcolor; ?>">
                    <td align="center"><? echo $i; ?></td>
                    <?
                    
                    ?>
                    
                    <td align="center"><p><? echo $color_arr[$row["color_name"]]; ?></p></td>
                    <td align="center"><p><? echo $count_arr[$row["yarn_count"]]; ?></p></td>
                    <td align="center"><p><? echo $row[feb_des]; ?></p></td>
                    <td align="center"><p><? echo $unit_of_measurement[$row["uom"]]; ?></p></td>
                    <td align="right"><p><? echo number_format($qtyArr[$key],2); ?></p></td>
                    <td align="right"><p><? echo number_format($amuArr[$key]/$qtyArr[$key],4,".",""); ?></p></td>
                    <td align="right"><p><? echo number_format($amuArr[$key],2,".","");?></p></td>
                    
                </tr>
                <? $i++; 
        } ?>
            </tbody>
            <tfoot>
                    <th colspan="5" align="right">Total </th>
                    <th align="right"><? echo number_format(array_sum($qtyArr),0); ?></th>
                    <th>&nbsp;</th>
                    <th align="right"><? echo $word_amount=number_format(array_sum($amuArr),2,".",""); ?></th>
                    <tr>
                    <th colspan="8" align="left">In words: <span style="font-weight:normal !important;"><? echo number_to_words($word_amount,$currency[$carrency_id],$paysa_sent); ?></span></th>
                    
                    </tr>
            </tfoot>
        </table>
        
        <br>
        <table  width="900" class="rpt_table" border="1" cellpadding="0" cellspacing="0" align="center" rules="all">
            <thead>
            <th width="3%">Sl</th><th width="97%">Terms & Condition/Note</th>
            </thead>
            <tbody>
                <?
                //echo "select terms_and_condition from wo_non_order_info_mst where id='$data[1]'"; 
                $data_array=sql_select("select terms_and_condition from wo_non_order_info_mst where id='$data[1]'");
                //echo count($data_array);
                if ( count($data_array)>0)
                {
                    $i=0;$k=0;
                    foreach( $data_array as $row )
                    {
                        $term_id=explode(",",$row[csf('terms_and_condition')]);
                        //print_r($term_id);
                        $i++;
                        foreach($term_id as $row_term)
                        {
                            $k++;
                            echo "<tr> <td>
                            $k</td><td> $lib_terms_condition[$row_term]</td></tr>";
                        }
                    }
                }
                else
                {
                    $i=0;
                    $data_array=sql_select("select id, terms from  lib_terms_condition");// quotation_id='$data'
                    //echo count($data_array)."jahid";
                    foreach( $data_array as $row )
                    {
                        $i++;
                        ?>
                        <tr>
                            <td>
                            <? echo $i;?>
                            </td>
                            <td>
                            <? echo $row[csf('terms')]; ?>
                            </td>
                        </tr>
                        <? 
                    }
                } 
                ?>
            
            </tbody>
        </table>
                <div style="margin:20px 0px 0px 20px;">
                Your scheduled delivery with quality and co-operation will be highly appreciated. <br>
                Thank You						
                </div>
            <?
            echo signature_table(42, $data[0], "900px");
            ?>
        </div>
        <script type="text/javascript" src="../../js/jquery.js"></script>
        <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
        <script>
        fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');
        </script>
        <?
        exit();	
        }
        else{}
}



	?>
