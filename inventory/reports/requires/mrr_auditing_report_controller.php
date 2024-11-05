<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
extract($_REQUEST);
$permission=$_SESSION['page_permission'];

include('../../../includes/common.php'); 

$user_id	= $_SESSION['logic_erp']['user_id'];
$data		= $_REQUEST['data'];
$action		= $_REQUEST['action'];
$menu_id	= $_SESSION['menu_id'];

$userCredential = sql_select("select unit_id as COMPANY_ID, item_cate_id as ITEM_CATE_ID, company_location_id as COMPANY_LOCATION_ID, store_location_id as STORE_LOCATION_ID from user_passwd where id=$user_id");

$store_credential_id = $userCredential[0]['STORE_LOCATION_ID'];
if ($store_credential_id !='') {
    $store_credential_id_cond = " and a.id in($store_credential_id)";
}

/*$userPrevItemcategory=return_field_value("item_cate_id", "user_passwd", "valid=1 and id='".$user_id."'","item_cate_id");*/

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 80, "select id,location_name from lib_location where company_id='$data' and status_active=1 and is_deleted=0 order by location_name","id,location_name", 1, "-- All --", 0, "load_drop_down( 'requires/mrr_auditing_report_controller', this.value+'**'+$data, 'load_drop_down_store', 'com_store_td' );" );
	exit();
}


if ($action=="load_drop_down_store")
{
	$data=explode('**',$data);
	if ($data[1]=='')  // company to store
	{
		echo create_drop_down( "cbo_store_name", 100, "select a.id, a.store_name from lib_store_location a, lib_location b where a.location_id=b.id and b.company_id=$data[0] and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $store_credential_id_cond order by store_name","id,store_name", 1, "--Select Store--", 0, "");
	}
	else if ($data[0]==0)  // all location cond
	{
		echo create_drop_down( "cbo_store_name", 100, "select a.id, a.store_name from lib_store_location a, lib_location b where a.location_id=b.id and b.company_id=$data[1] and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $store_credential_id_cond order by store_name","id,store_name", 1, "--Select Store--", 0, "");
	} 
	else  // location id wise store
	{
		echo create_drop_down( "cbo_store_name", 100, "select a.id, a.store_name from lib_store_location a, lib_location b where a.location_id=b.id and b.id=$data[0] and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $store_credential_id_cond order by store_name","id,store_name", 1, "--Select Store--", 0, "");
	}	
	exit();
}

if($action == "challan_popup")
{
    echo load_html_head_contents("Challan Info","../../../", 1, 1, '','','');
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

        function toggle( x, origColor ) {
            var newColor = 'yellow';
            if ( x.style ) {
                x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
            }
        }

        function set_all()
        {
            var old=document.getElementById('selected_ids').value;
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

            $('#selected_id').val(id);
            $('#selected_name').val(name);
        }
    </script>
    </head>
    <fieldset style="width:620px;">
        <legend>Challan Details</legend>
        <input type="hidden" name="selected_name" id="selected_name" value="">
        <input type="hidden" name="selected_id" id="selected_id" value="">
        <table width="620" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
            <thead>
            <tr>
                <th width="40">SL</th>
                <th width="100">Challan No.</th>
                <th width="120">MRR No.</th>
                <th width="70">MRR Date</th>
                <th width="120">Req./WO. Number</th>
                <th >Supplier Name</th>
            </tr>
            </thead>
        </table>
        <div style="width:620px; overflow-y:scroll; max-height:325px" id="scroll_body">
            <table width="600" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body" align="left">
                <?
                $i = 1;
                $cbo_company_name=str_replace("'","",$cbo_company_name);
                $supplier_arr = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");

                $sql_mrr = "SELECT a.ID, a.COMPANY_ID, a.CHALLAN_NO, a.BOOKING_NO, a.SUPPLIER_ID, a.RECEIVE_DATE, a.RECV_NUMBER
                from inv_receive_master a, inv_transaction b where a.id = b.mst_id and a.CHALLAN_NO is not null and  a.company_id=$cbo_company_name and b.transaction_type=1 and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
                group by  a.ID, a.COMPANY_ID, a.CHALLAN_NO, a.BOOKING_NO, a.SUPPLIER_ID, a.RECEIVE_DATE, a.RECV_NUMBER order by a.ID desc";

                $result=sql_select($sql_mrr);

                $selected_id_arr=explode(",",$selected_challan_id);
                foreach ($result as $row)
                {
                    if ($i % 2 == 0)
                        $bgcolor = "#E9F3FF";
                    else
                        $bgcolor = "#FFFFFF";

                    if(in_array($row[csf("id")],$selected_id_arr))
                    {
                        if($selected_ids=="") $selected_ids=$i; else $selected_ids.=",".$i;
                    }
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="js_set_value(<? echo $i; ?>)" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>">
                        <td width="40" align="center">
                            <? echo $i; ?>
                            <input type="hidden" name="txt_individual_id" id="txt_individual_id<? echo $i ?>" value="<? echo $row[csf("id")]; ?>"/>
                            <input type="hidden" name="txt_individual" id="txt_individual<? echo $i ?>" value="<? echo $row[csf("challan_no")]; ?>"/>
                        </td>
                        <td width="100" align="center"><p><? echo $row[csf("challan_no")]; ?></p></td>
                        <td width="120" align="center"><p><? echo $row[csf("recv_number")]; ?></p></td>
                        <td width="70" align="center"><p><? echo change_date_format($row[csf("receive_date")]); ?></p></td>
                        <td width="120" align="center"><p><? echo $row[csf("booking_no")]; ?></p></td>
                        <td align="center"><p><? echo $supplier_arr[$row[csf("supplier_id")]]; ?></p></td>
                    </tr>
                    <?
                    $i++;
                }
                ?>
                <input type="hidden" name="selected_ids" id="selected_ids" value="<? echo $selected_ids; ?>"/>
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
    exit;
}

if($action == "wo_popup")
{
    echo load_html_head_contents("Challan Info","../../../", 1, 1, '','','');
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

        function toggle( x, origColor ) {
            var newColor = 'yellow';
            if ( x.style ) {
                x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
            }
        }

        function set_all()
        {
            var old=document.getElementById('selected_ids').value;
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

            $('#selected_id').val(id);
            $('#selected_name').val(name);
        }
    </script>
    </head>
    <fieldset style="width:630px;">
        <legend>Work Order Details</legend>
        <input type="hidden" name="selected_name" id="selected_name" value="">
        <input type="hidden" name="selected_id" id="selected_id" value="">
        <table width="430" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
            <thead>
            <tr>
                <th width="40">SL</th>
                <th width="100">Challan No.</th>
                <th width="120">Req./WO. Number</th>
                <th >Supplier Name</th>
            </tr>
            </thead>
        </table>
        <div style="width:430px; overflow-y:scroll; max-height:325px" id="scroll_body">
            <table width="410" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body" align="left">
                <?
                $i = 1;
                $cbo_company_name=str_replace("'","",$cbo_company_name);
                $supplier_arr = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");

                $sql_mrr = "SELECT a.ID, a.COMPANY_ID, a.CHALLAN_NO, a.BOOKING_NO, a.SUPPLIER_ID, a.RECEIVE_DATE, a.RECV_NUMBER
                from inv_receive_master a, inv_transaction b where a.id = b.mst_id and a.receive_basis = 2 and a.BOOKING_NO is not null and  a.company_id=$cbo_company_name and b.transaction_type=1 and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
                group by  a.ID, a.COMPANY_ID, a.CHALLAN_NO, a.BOOKING_NO, a.SUPPLIER_ID, a.RECEIVE_DATE, a.RECV_NUMBER order by a.ID desc";

                $result=sql_select($sql_mrr);

                $selected_id_arr=explode(",",$selected_challan_id);
                foreach ($result as $row)
                {
                    if ($i % 2 == 0)
                        $bgcolor = "#E9F3FF";
                    else
                        $bgcolor = "#FFFFFF";

                    if(in_array($row[csf("id")],$selected_id_arr))
                    {
                        if($selected_ids=="") $selected_ids=$i; else $selected_ids.=",".$i;
                    }
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="js_set_value(<? echo $i; ?>)" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>">
                        <td width="40" align="center">
                            <? echo $i; ?>
                            <input type="hidden" name="txt_individual_id" id="txt_individual_id<? echo $i ?>" value="<? echo $row[csf("id")]; ?>"/>
                            <input type="hidden" name="txt_individual" id="txt_individual<? echo $i ?>" value="<? echo $row[csf("BOOKING_NO")]; ?>"/>
                        </td>
                        <td width="100" align="center"><p><? echo $row[csf("challan_no")]; ?></p></td>
                        <td width="120" align="center"><p><? echo $row[csf("booking_no")]; ?></p></td>
                        <td align="center"><p><? echo $supplier_arr[$row[csf("supplier_id")]]; ?></p></td>
                    </tr>
                    <?
                    $i++;
                }
                ?>
                <input type="hidden" name="selected_ids" id="selected_ids" value="<? echo $selected_ids; ?>"/>
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
    exit;
}
if ($action=="load_drop_down_supplier")
{
	echo create_drop_down( "cbo_suppler_name", 100, "select a.id, a.supplier_name from lib_supplier a, lib_supplier_tag_company b  where a.id=b.supplier_id and b.tag_company='$data' and a.status_active=1 and a.is_deleted=0 order by a.supplier_name","id,supplier_name", 1, "-- Select Supplier --", 0, "" );
	exit();
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));	

	$company_name		= str_replace("'","",$cbo_company_name);
	$cbo_location_id	= str_replace("'","",$cbo_location_id);
	$cbo_store_name		= str_replace("'","",$cbo_store_name);
	$cbo_item_category_id = str_replace("'","",$cbo_item_category_id);
	$txt_challan_no		= str_replace("'","",$txt_challan_no);
	$txt_challan_id		= str_replace("'","",$txt_challan_id);
	$txt_mrr_no			= str_replace("'","",$txt_mrr_no);
	$txt_date_from		= str_replace("'","",$txt_date_from);
	$txt_date_to		= str_replace("'","",$txt_date_to);
	$cbo_suppler_name	= str_replace("'","",$cbo_suppler_name);
	$txt_wo_no			= str_replace("'","",$txt_wo_no);
	$txt_wo_id			= str_replace("'","",$txt_wo_id);
	$txt_pi_no			= str_replace("'","",$txt_pi_no);
	$cbo_audit_type		= str_replace("'","",$cbo_audit_type);
	$cbo_date_basis		= str_replace("'","",$cbo_date_basis);
    $cbo_year           = str_replace("'","",$cbo_year);
    // echo $cbo_suppler_name; die;
    $basis_on=array(1=>"Embellishment",2=>"Embellishment Without Order",3=>"Lab Test",4=>"Knitting",5=>"Dyeing",6=>"Service WO");

	$search_cond = ''; $search_cond2 = '';$suppy_cond1=$suppy_cond3='';
	//if ($cbo_location_id>0) $search_cond .= " and a.location_id=$cbo_location_id";  
	// Location filter only search panel. Not filter data lvel
	if ($cbo_store_name>0)
    {
        $search_cond .= " and a.store_id=$cbo_store_name";
    }
    if($txt_challan_id != ''){
        if ($txt_challan_no != ''){
            $str_challan_id = "";
            $explode_challan = explode(',', $txt_challan_no);
            foreach ($explode_challan as $k => $v){
                if ($k == 0){
                    $str_challan_id .= "'".trim($v)."'";
                }else{
                    $str_challan_id .= ",'".trim($v)."'";
                }
            }
            $search_cond .= " and a.challan_no in ($str_challan_id)";
            $search_cond2 .= " and p.manual_challan in ($str_challan_id)";
        }
    }else{
        if ($txt_challan_no != '')
        {
            $search_cond .= " and a.challan_no ='$txt_challan_no'";
            $search_cond2 .= " and p.manual_challan ='$txt_challan_no'";
        }
    }
	if ($txt_mrr_no != '')
    {
        $search_cond .= " and a.recv_number_prefix_num='$txt_mrr_no'";
        $search_cond2 .= " and p.system_prefix_num='$txt_mrr_no'";
    }
	if ($cbo_suppler_name>0)
    {
        $suppy_cond1 .= " and a.SUPPLIER_ID=$cbo_suppler_name";
        $suppy_cond3 .= " and a.KNITTING_COMPANY=$cbo_suppler_name"; 
        $search_cond2 .= " and p.service_company_id=$cbo_suppler_name"; 
    }
    if($txt_wo_id != ''){
        $str_wo_id = "";
        $explode_wo_no = explode(',', $txt_wo_no);
        foreach ($explode_wo_no as $k => $v){
            if ($k == 0){
                $str_wo_id .= "'".trim($v)."'";
            }else{
                $str_wo_id .= ",'".trim($v)."'";
            }
        }
        if ($txt_wo_no != '')
        {
            $search_cond .= " and a.booking_no in ($str_wo_id)";
            $search_cond2 .= " and p.wo_booking_no in ($str_wo_id)";
        }
    }else{
        if ($txt_wo_no != '')
        {
            $search_cond .= " and a.booking_no like '%$txt_wo_no'";
            $search_cond2 .= " and p.wo_booking_no like '%$txt_wo_no'";
        }
    }
	if ($txt_pi_no != '')
    {
        $search_cond .= " and a.booking_no='$txt_pi_no'";
        $search_cond2 .= " and p.wo_booking_no='$txt_pi_no'";
    }
	
	$search_cond .= " and a.is_audited=$cbo_audit_type";
    $search_cond2 .= " and p.is_audited=$cbo_audit_type";
	$category_cond=''; $category_cond2='';
	if ($cbo_item_category_id>0)
    {
        $category_cond = " and b.item_category=$cbo_item_category_id";
        $category_cond2 = " and q.item_category=$cbo_item_category_id";
    }

	/*========== user credential  ========*/
	$userCredential = sql_select("select unit_id as COMPANY_ID, item_cate_id as ITEM_CATE_ID, company_location_id as COMPANY_LOCATION_ID, store_location_id as STORE_LOCATION_ID from user_passwd where id=$user_id");
	$category_credential_id = $userCredential[0]['ITEM_CATE_ID'];

	if ($category_credential_id !='') {	    
	    if ($cbo_item_category_id>0)
        {
            $category_cond = " and b.item_category=$cbo_item_category_id"; //Credential category search 
            $category_cond2 = " and q.item_category=$cbo_item_category_id"; //Credential category search
        }
	    else{
            $category_cond = " and b.item_category in($category_credential_id)"; // All credential category
            $category_cond2 = " and q.item_category in($category_credential_id)"; // All credential category
        }
	}
	/*========== End user credential  ========*/
	if ($txt_date_from != '' && $txt_date_to != '')
	{
		if($db_type==0)
		{
			$txt_date_from 	= date("Y-m-d", strtotime($txt_date_from));
			$txt_date_to 	= date("Y-m-d", strtotime($txt_date_to));
		}
        else
		{
			$txt_date_from 	= date("d-M-Y", strtotime($txt_date_from));
			$txt_date_to 	= date("d-M-Y", strtotime($txt_date_to));
		}
		if($cbo_date_basis==1){
			$date_cond 		= " and a.receive_date between '$txt_date_from' and '$txt_date_to'";
            $date_cond2 	= " and p.ackn_date between '".$txt_date_from."' and '".$txt_date_to." 11:59:59 PM'";
		}else {
			$date_cond =" and a.audit_date between '".$txt_date_from."' and '".$txt_date_to." 11:59:59 PM'";
            $date_cond2 =" and p.audit_date between '".$txt_date_from."' and '".$txt_date_to." 11:59:59 PM'";
		}
	}

    
		if ($db_type == 0)
		{
			if($cbo_year>0)
			{
				$year_cond=" and YEAR(a.insert_date)=$cbo_year";
                $year_cond2=" and YEAR(p.insert_date)=$cbo_year";
			}
		}
		else if ($db_type == 2)
		{
			if($cbo_year>0)
			{
				$year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
                $year_cond2= " and to_char(p.insert_date,'YYYY')=$cbo_year";
			}
		}
	    else {
                $year_cond="";
                $year_cond2="";
	    }     

	$company_arr	= return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$company_fullname_arr = return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supplier_arr	= return_library_array( "select id,supplier_name from lib_supplier",'id','supplier_name');
	$store_arr 		= return_library_array("select id,store_name from lib_store_location", "id", "store_name");
	$user_id_arr 		= return_library_array("select id,user_name from user_passwd", "id", "user_name");
	$user_name 		= return_library_array("select id,user_full_name from user_passwd", "id", "user_full_name");
	$item_category_name = return_library_array( "select id, short_name from lib_item_category_list",'id','short_name');	
     
	
	if($db_type==0) {
		$itemCat = "  group_concat(distinct(b.item_category))";
		$pi_wo_batch = " group_concat(distinct(b.pi_wo_batch_no))";
	} else {
		$itemCat = " listagg(b.item_category ,',') within group (order by b.item_category)";
		$pi_wo_batch = " listagg(b.pi_wo_batch_no ,',') within group (order by b.pi_wo_batch_no)";
	} 

    if($cbo_item_category_id == 114)
    {
        //main Query 
        $sql_mrr="SELECT p.ID, p.COMPANY_ID, p.entry_form_id as ENTRY_FORM, null as IS_MULTI, null as IS_APPROVED, null as LOCATION_ID, p.system_no as RECV_NUMBER, null as STORE_ID, p.manual_challan as CHALLAN_NO, p.system_prefix_num as RECV_NUMBER_PREFIX_NUM, p.wo_type as RECEIVE_BASIS, p.ackn_date as RECEIVE_DATE, null as CURRENCY_ID, p.exchange_rate as EXCHANGE_RATE, p.wo_booking_id as BOOKING_ID, p.wo_booking_no as BOOKING_NO, p.service_company_id as SUPPLIER_ID, null as LC_NO, p.AUDIT_BY, p.AUDIT_DATE, p.AUDIT_REMARK, p.IS_AUDITED, listagg(q.item_category ,',') within group (order by q.item_category) as ITEM_CATEGORY, sum(q.amount) as CONS_AMOUNT, 0 as CONS_AMOUNT_ACCESSORIES, 0 as ORDER_AMOUNT, 0 as ORDER_AMOUNT_ACCESSORIES, null as PI_WO_BATCH_ID, null as KNITTING_SOURCE, null as KNITTING_COMPANY, null as VARIABLE_SETTING,
        1 as type
        from wo_service_acknowledgement_mst p, wo_service_acknowledgement_dtls q
        where p.id=q.mst_id and p.company_id='$company_name' $date_cond2 $year_cond2 $search_cond2 and p.status_active=1 and p.is_deleted=0 and q.status_active=1 and q.is_deleted=0
        group by p.id, p.company_id, p.entry_form_id, p.system_no, p.manual_challan, p.system_prefix_num, p.wo_type, p.ackn_date, p.exchange_rate, p.wo_booking_id, p.wo_booking_no, p.service_company_id, p.audit_by, p.audit_date, p.audit_remark, p.is_audited";
    }else{
        //main Query 
        $sql_mrr="SELECT a.ID, a.COMPANY_ID, a.ENTRY_FORM, a.IS_MULTI, a.IS_APPROVED, a.LOCATION_ID, a.RECV_NUMBER, a.STORE_ID, a.CHALLAN_NO, a.RECV_NUMBER_PREFIX_NUM, a.RECEIVE_BASIS, a.RECEIVE_DATE, a.CURRENCY_ID, a.EXCHANGE_RATE, a.BOOKING_ID, a.BOOKING_NO, a.SUPPLIER_ID, a.LC_NO, a.AUDIT_BY, a.AUDIT_DATE, a.AUDIT_REMARK, a.IS_AUDITED, $itemCat as ITEM_CATEGORY, sum(b.cons_amount) as CONS_AMOUNT, sum(CASE WHEN a.entry_form=24 and b.payment_over_recv=0 and b.item_category=4 THEN b.cons_amount ELSE 0 END) AS CONS_AMOUNT_ACCESSORIES, sum(b.order_amount) as ORDER_AMOUNT, sum(CASE WHEN a.entry_form=24 and b.payment_over_recv=0 and b.item_category=4 THEN b.order_amount ELSE 0 END) AS ORDER_AMOUNT_ACCESSORIES, $pi_wo_batch as PI_WO_BATCH_ID, a.KNITTING_SOURCE, a.KNITTING_COMPANY, a.VARIABLE_SETTING, 0 as type
        from inv_receive_master a, inv_transaction b, product_details_master c 
        where a.id=b.mst_id and b.prod_id=c.id and a.company_id='$company_name' and b.transaction_type=1  $date_cond $year_cond $search_cond $suppy_cond1 $category_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
        group by a.id, a.company_id, a.entry_form,a.is_multi,a.is_approved, a.location_id, a.recv_number, a.store_id, a.challan_no, a.recv_number_prefix_num, a.receive_basis, a.receive_date, a.currency_id, a.exchange_rate, a.booking_id, a.booking_no, a.supplier_id, a.lc_no, a.audit_by, a.audit_date, a.audit_remark, a.is_audited, a.knitting_source, a.knitting_company, a.variable_setting
        union
        SELECT a.ID, a.COMPANY_ID, a.ENTRY_FORM, a.IS_MULTI, a.IS_APPROVED, a.LOCATION_ID, a.RECV_NUMBER, a.STORE_ID, a.CHALLAN_NO, a.RECV_NUMBER_PREFIX_NUM, a.RECEIVE_BASIS, a.RECEIVE_DATE, a.CURRENCY_ID, a.EXCHANGE_RATE, a.BOOKING_ID, a.BOOKING_NO, a.SUPPLIER_ID, a.LC_NO, a.AUDIT_BY, a.AUDIT_DATE, a.AUDIT_REMARK, a.IS_AUDITED, $itemCat as ITEM_CATEGORY, sum(b.cons_amount) as CONS_AMOUNT, sum(CASE WHEN a.entry_form=24 and b.payment_over_recv=0 and b.item_category=4 THEN b.cons_amount ELSE 0 END) AS CONS_AMOUNT_ACCESSORIES, sum(b.order_amount) as ORDER_AMOUNT, sum(CASE WHEN a.entry_form=24 and b.payment_over_recv=0 and b.item_category=4 THEN b.order_amount ELSE 0 END) AS ORDER_AMOUNT_ACCESSORIES, $pi_wo_batch as PI_WO_BATCH_ID, a.KNITTING_SOURCE, a.KNITTING_COMPANY, a.VARIABLE_SETTING, 0 as type
        from inv_receive_master a, inv_transaction b, product_details_master c 
        where a.id=b.mst_id and b.prod_id=c.id and a.company_id='$company_name' and b.transaction_type=1 and a.KNITTING_COMPANY>0  $date_cond $year_cond $suppy_cond3 $search_cond $category_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
        group by a.id, a.company_id, a.entry_form,a.is_multi,a.is_approved, a.location_id, a.recv_number, a.store_id, a.challan_no, a.recv_number_prefix_num, a.receive_basis, a.receive_date, a.currency_id, a.exchange_rate, a.booking_id, a.booking_no, a.supplier_id, a.lc_no, a.audit_by, a.audit_date, a.audit_remark, a.is_audited, a.knitting_source, a.knitting_company, a.variable_setting
        union
        SELECT p.ID, p.COMPANY_ID, p.entry_form_id as ENTRY_FORM, null as IS_MULTI, null as IS_APPROVED, null as LOCATION_ID, p.system_no as RECV_NUMBER, null as STORE_ID, p.manual_challan as CHALLAN_NO, p.system_prefix_num as RECV_NUMBER_PREFIX_NUM, p.wo_type as RECEIVE_BASIS, p.ackn_date as RECEIVE_DATE, null as CURRENCY_ID, p.exchange_rate as EXCHANGE_RATE, p.wo_booking_id as BOOKING_ID, p.wo_booking_no as BOOKING_NO, p.service_company_id as SUPPLIER_ID, null as LC_NO, p.AUDIT_BY, p.AUDIT_DATE, p.AUDIT_REMARK, p.IS_AUDITED, listagg(q.item_category ,',') within group (order by q.item_category) as ITEM_CATEGORY, sum(q.amount) as CONS_AMOUNT, 0 as CONS_AMOUNT_ACCESSORIES, 0 as ORDER_AMOUNT, 0 as ORDER_AMOUNT_ACCESSORIES, null as PI_WO_BATCH_ID, null as KNITTING_SOURCE, null as KNITTING_COMPANY, null as VARIABLE_SETTING,
        1 as type
        from wo_service_acknowledgement_mst p, wo_service_acknowledgement_dtls q
        where p.id=q.mst_id and p.company_id='$company_name' $date_cond2 $year_cond2 $search_cond2 $category_cond2 and p.status_active=1 and p.is_deleted=0 and q.status_active=1 and q.is_deleted=0
        group by p.id, p.company_id, p.entry_form_id, p.system_no, p.manual_challan, p.system_prefix_num, p.wo_type, p.ackn_date, p.exchange_rate, p.wo_booking_id, p.wo_booking_no, p.service_company_id, p.audit_by, p.audit_date, p.audit_remark, p.is_audited";
    }
	
	// echo $sql_mrr;die;
	$sql_mrr_res = sql_select($sql_mrr);

    $salers_id_arr=$req_id_arr=$wo_id_arr=$batch_id_arrr=$wo_non_order_arrr=$pi_Ids_arr=array();
	foreach ($sql_mrr_res as $val) {
		if ($val['RECEIVE_BASIS']==1 && $val['BOOKING_ID'] != 0) {			
            $pi_Ids_arr[$val['BOOKING_ID']] = $val['BOOKING_ID'];
		}
		else if ($val['ENTRY_FORM']==7 && $val['PI_WO_BATCH_ID'] !='' &&  $val['PI_WO_BATCH_ID'] !=0){			
            $batch_id_arrr[$val['PI_WO_BATCH_ID']] = $val['PI_WO_BATCH_ID'];
		}

		else if ($val['ENTRY_FORM']==558){		
            $wo_non_order_arrr[$val['BOOKING_ID']] = $val['BOOKING_ID'];
		}

		else if ($val['RECEIVE_BASIS'] ==2  && $val['BOOKING_ID'] != 0) {
            if(isset($val['BOOKING_ID'])){            
                $wo_id_arr[$val['BOOKING_ID']] = $val['BOOKING_ID'];
            }
			
		}
        else if ($val['RECEIVE_BASIS'] ==7 && $val['BOOKING_ID'] != 0) {
            if(isset($val['BOOKING_ID'])){
                $req_id_arr[$val['BOOKING_ID']] = $val['BOOKING_ID'];
            }           
		}
        else{
            if(isset($val['BOOKING_ID'])){
                $salers_id_arr[$val['BOOKING_ID']] = $val['BOOKING_ID'];
            }
        }     
	}

    // print_r($salers_id_arr);

    $con = connect();
	$rid=execute_query("delete from GBL_TEMP_ENGINE where entry_form=140 and user_id=$user_id");
	if($rid) oci_commit($con);

    if(!empty($salers_id_arr)){

        fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 140, 1, $salers_id_arr,$empty_arr);
        $sql_req=sql_select("SELECT a.ID,a.ENTRY_FORM, a.WITHIN_GROUP, a.COMPANY_ID, a.SALES_BOOKING_NO, a.BUYER_ID, a.JOB_NO from fabric_sales_order_mst a, gbl_temp_engine b where a.id=b.ref_val and b.entry_form=140 and b.ref_from=1 and b.user_id= $user_id");
        $sales_wo_arr=array();
        foreach($sql_req as $row){
            $sales_wo_arr[$row['ID']]['ENTRY_FORM']=$row['ENTRY_FORM'];
            $sales_wo_arr[$row['ID']]['ID']=$row['ID'];
            $sales_wo_arr[$row['ID']]['COMPANY_ID']=$row['COMPANY_ID'];
            $sales_wo_arr[$row['ID']]['SALES_BOOKING_NO']=$row['SALES_BOOKING_NO'];
            $sales_wo_arr[$row['ID']]['BUYER_ID']=$row['BUYER_ID'];
            $sales_wo_arr[$row['ID']]['WITHIN_GROUP']=$row['WITHIN_GROUP'];
            $sales_wo_arr[$row['ID']]['JOB_NO']=$row['JOB_NO'];
        }
    }

    if(!empty($req_id_arr)){

        fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 140, 2, $req_id_arr,$empty_arr);
        $sql_req=sql_select("SELECT a.ID, a.ENTRY_FORM, a.REMARKS, a.LOCATION_ID, a.IS_APPROVED from inv_purchase_requisition_mst a,gbl_temp_engine b where a.id=b.ref_val and b.entry_form=140 and b.ref_from=2 and b.user_id= $user_id");
        $req_arr=array();
        foreach($sql_req as $row){
            $req_arr[$row['ID']]['ENTRY_FORM']=$row['ENTRY_FORM'];
            $req_arr[$row['ID']]['ID']=$row['ID'];
            $req_arr[$row['ID']]['REMARKS']=$row['REMARKS'];
            $req_arr[$row['ID']]['LOCATION_ID']=$row['LOCATION_ID'];
            $req_arr[$row['ID']]['IS_APPROVED']=$row['IS_APPROVED'];
        }
    }

    if(!empty($wo_id_arr)){

        fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 140, 3, $wo_id_arr,$empty_arr);
        $booking_arr=array();
        $sql_wo="SELECT a.ID, a.ENTRY_FORM as BOOKING_ENTRY_FORM, a.FABRIC_SOURCE  from wo_booking_mst a, gbl_temp_engine b where a.id=b.ref_val and a.status_active=1 and b.entry_form=140 and b.ref_from=3 and b.user_id= $user_id";
        $sql_wo_arr=sql_select($sql_wo);
        foreach($sql_wo_arr as $row){
            $booking_arr[$row["ID"]]["BOOKING_ENTRY_FORM"]=$row["BOOKING_ENTRY_FORM"];
            $booking_arr[$row["ID"]]["FABRIC_SOURCE"]=$row["FABRIC_SOURCE"];
        }
    }
    // $all_wo_id=rtrim($work_order_Ids,",");

    if(!empty($wo_id_arr)){
        fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 140, 21, $wo_id_arr,$empty_arr);
        $wo_yarn_dying_arr=array();
        $sql_wo="SELECT a.ID, a.ENTRY_FORM as BOOKING_ENTRY_FORM, a.YDW_NO,a.SUPPLIER_ID,a.PAY_MODE,a.COMPANY_ID,a.BUDGET_VERSION  from WO_YARN_DYEING_MST a, gbl_temp_engine b where a.id=b.ref_val and a.status_active=1 and a.entry_form=41 and b.entry_form=140 and b.ref_from=21 and b.user_id= $user_id";
        $sql_wo_arr=sql_select($sql_wo);
        foreach($sql_wo_arr as $row){
            $wo_yarn_dying_arr[$row["ID"]]["BOOKING_ENTRY_FORM"]=$row["BOOKING_ENTRY_FORM"];
            $wo_yarn_dying_arr[$row["ID"]]["SUPPLIER_ID"]=$row["SUPPLIER_ID"];
            $wo_yarn_dying_arr[$row["ID"]]["COMPANY_ID"]=$row["COMPANY_ID"];
            $wo_yarn_dying_arr[$row["ID"]]["YDW_NO"]=$row["YDW_NO"];
            $wo_yarn_dying_arr[$row["ID"]]["PAY_MODE"]=$row["PAY_MODE"];
            $wo_yarn_dying_arr[$row["ID"]]["ID"]=$row["ID"];
            $wo_yarn_dying_arr[$row["ID"]]["BUDGET_VERSION"]=$row["BUDGET_VERSION"];
        }
    }


    if(!empty($wo_id_arr)){

        fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 140, 4, $wo_id_arr, $empty_arr);
        $bill_arr=array();
        $sql_bill_no="SELECT b.WO_PO_ID, a.BILL_NO, a.BILL_DATE,a.COMPANY_ID,a.ID,a.BILL_NUMBER,a.PARTY_ID,a.BUYER_ID,b.RECEIVE_ID
        FROM inv_bill_processing_mst a, inv_bill_processing_dtls b, gbl_temp_engine c 
        WHERE a.status_active = 1 AND a.id = b.mst_id AND b.WO_PO_ID=c.ref_val and c.entry_form=140 and c.ref_from=4 and c.user_id= $user_id";
        $sql_bill_arr=sql_select($sql_bill_no);
        foreach($sql_bill_arr as $row){
            $bill_arr[$row["WO_PO_ID"]]["BILL_NO"]=$row["BILL_NO"];
            $bill_arr[$row["WO_PO_ID"]]["BILL_DATE"]=$row["BILL_DATE"];
            $bill_arr[$row["WO_PO_ID"]]["COMPANY_ID"]=$row["COMPANY_ID"];
            $bill_arr[$row["WO_PO_ID"]]["ID"]=$row["ID"];
            $bill_arr[$row["WO_PO_ID"]]["BILL_NUMBER"]=$row["BILL_NUMBER"];
            $bill_arr[$row["WO_PO_ID"]]["PARTY_ID"]=$row["PARTY_ID"];
            $bill_arr[$row["WO_PO_ID"]]["BUYER_ID"]=$row["BUYER_ID"];
            $bill_arr[$row["WO_PO_ID"]]["RECEIVE_ID"]=$row["RECEIVE_ID"];
        }
    }

    if(!empty($wo_non_order_arrr)){

        fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 140, 5, $wo_non_order_arrr,$empty_arr);
        $subcon_bill_arr=array();
        $sql_subcon_bill_no="SELECT a.ID, a.BILL_NO, a.BILL_DATE, a.WO_NON_ORDER_INFO_MST_ID
        FROM SUBCON_OUTBOUND_BILL_MST a, gbl_temp_engine b
        WHERE STATUS_ACTIVE = 1 AND IS_DELETED=0 AND  a.WO_NON_ORDER_INFO_MST_ID=b.ref_val and b.entry_form=140 and b.ref_from=5 and b.user_id= $user_id";
        $sql_subcon_bill_arr=sql_select($sql_subcon_bill_no);
        foreach($sql_subcon_bill_arr as $row){
            $subcon_bill_arr[$row["WO_NON_ORDER_INFO_MST_ID"]]["ID"]=$row["ID"];
            $subcon_bill_arr[$row["WO_NON_ORDER_INFO_MST_ID"]]["BILL_NO"]=$row["BILL_NO"];
            $subcon_bill_arr[$row["WO_NON_ORDER_INFO_MST_ID"]]["BILL_DATE"]=$row["BILL_DATE"];
        }
    }

    if(!empty($wo_id_arr)){

        fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 140, 6, $wo_id_arr,$empty_arr);
        $stationary_purchase=array();
        $sql_wo_non="SELECT a.ENTRY_FORM, a.ID as DYES_UPDATE_ID, a.CURRENCY_ID, a.WO_BASIS_ID, a.PAY_MODE, a.SOURCE, a.DELIVERY_DATE, a.ATTENTION, a.REQUISITION_NO, a.WO_NUMBER, a.WO_DATE, a.LOCATION_ID  from wo_non_order_info_mst a, gbl_temp_engine b where a.id=b.ref_val and b.entry_form=140 and b.ref_from=6 and b.user_id= $user_id";
        $sql_stationary_arr=sql_select($sql_wo_non);
        foreach($sql_stationary_arr as $row){
            $stationary_purchase[$row["DYES_UPDATE_ID"]][$row["WO_NUMBER"]]["ENTRY_FORM"]=$row["ENTRY_FORM"];
            $stationary_purchase[$row["DYES_UPDATE_ID"]]["DYES_UPDATE_ID"]=$row["DYES_UPDATE_ID"];
            $stationary_purchase[$row["DYES_UPDATE_ID"]]["CURRENCY_ID"]=$row["CURRENCY_ID"];
            $stationary_purchase[$row["DYES_UPDATE_ID"]]["WO_BASIS_ID"]=$row["WO_BASIS_ID"];
            $stationary_purchase[$row["DYES_UPDATE_ID"]]["PAY_MODE"]=$row["PAY_MODE"];
            $stationary_purchase[$row["DYES_UPDATE_ID"]]["SOURCE"]=$row["SOURCE"];
            $stationary_purchase[$row["DYES_UPDATE_ID"]]["DELIVERY_DATE"]=$row["DELIVERY_DATE"];
            $stationary_purchase[$row["DYES_UPDATE_ID"]]["ATTENTION"]=$row["ATTENTION"];
            $stationary_purchase[$row["DYES_UPDATE_ID"]]["REQUISITION_NO"]=$row["REQUISITION_NO"];
            $stationary_purchase[$row["DYES_UPDATE_ID"]]["LOCATION_ID"]=$row["LOCATION_ID"];
            $stationary_purchase[$row["DYES_UPDATE_ID"]]["WO_NUMBER"]=$row["WO_NUMBER"];
            $stationary_purchase[$row["DYES_UPDATE_ID"]]["WO_DATE"]=$row["WO_DATE"];
        }
    }


    if(!empty($pi_Ids_arr)){

        fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 140, 6, $pi_Ids_arr,$empty_arr);
        $sql_pi_lc="SELECT a.id as PI_ID,a.item_category_id as ITEM_CATEGORY_ID, a.ENTRY_FORM, b.WORK_ORDER_NO, d.LC_NUMBER,e.ENTRY_FORM as BOOKING_ENTRY_FORM,e.FABRIC_SOURCE, f.entry_form as DYS_CAMICAL_ENTRY_FORM, f.ID as DYES_UPDATE_ID 
	    from com_pi_master_details a, gbl_temp_engine g, com_pi_item_details b 
        left join wo_booking_mst e on e.id = b.work_order_id
        left join wo_non_order_info_mst f on f.id = b.work_order_id
	    left join com_btb_lc_pi c on b.pi_id=c.pi_id and c.status_active=1 
	    left join com_btb_lc_master_details d on c.com_btb_lc_master_details_id=d.id and d.status_active=1
	    where a.id=b.pi_id and a.id=g.ref_val and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and g.entry_form=140 and g.ref_from=6 and g.user_id= $user_id 
	    group by a.id, b.work_order_no, d.lc_number, a.ITEM_CATEGORY_ID, a.ENTRY_FORM, e.ENTRY_FORM, e.FABRIC_SOURCE, f.ENTRY_FORM, f.ID";

	    $sql_pi_lc_res = sql_select($sql_pi_lc);
	    $work_order_no_arr=array(); $lc_number_arr=array();$pi_id_array=array();
         $catagorry_id_array=array(); $entry_form=array(); $booking_entry_form=array();
		foreach ($sql_pi_lc_res as $val) {
			if ($val['LC_NUMBER'] != '') {
				$lc_number_arr[$val['PI_ID']][$val['ITEM_CATEGORY_ID']]['LC_NUMBER']=$val['LC_NUMBER'];				
			}
            $pi_id_array[$val['PI_ID']][$val['ITEM_CATEGORY_ID']]['PI_ID']=$val['PI_ID'];
            $catagorry_id_array[$val['PI_ID']][$val['ITEM_CATEGORY_ID']]['ITEM_CATEGORY_ID']=$val['ITEM_CATEGORY_ID'];
            $entry_form[$val['PI_ID']][$val['ITEM_CATEGORY_ID']]['ENTRY_FORM']=$val['ENTRY_FORM'];
            $booking_entry_form[$val['PI_ID']][$val['ITEM_CATEGORY_ID']]['BOOKING_ENTRY_FORM']=$val['BOOKING_ENTRY_FORM'];
            $booking_entry_form[$val['PI_ID']][$val['ITEM_CATEGORY_ID']]['FABRIC_SOURCE']=$val['FABRIC_SOURCE'];
            $booking_entry_form[$val['PI_ID']][$val['ITEM_CATEGORY_ID']]['FABRIC_SOURCE']=$val['FABRIC_SOURCE'];
            $booking_entry_form[$val['PI_ID']][$val['ITEM_CATEGORY_ID']]['DYS_CAMICAL_ENTRY_FORM']=$val['DYS_CAMICAL_ENTRY_FORM'];
            $booking_entry_form[$val['PI_ID']][$val['ITEM_CATEGORY_ID']]['DYES_UPDATE_ID']=$val['DYES_UPDATE_ID'];
            $booking_entry_form[$val['PI_ID']][$val['ITEM_CATEGORY_ID']]['WORK_ORDER_NO']=$val['WORK_ORDER_NO'];

			if ($work_order_no_arr[$val['PI_ID']][$val['WORK_ORDER_NO']] == '') {
	            $work_order_no_arr[$val['PI_ID']][$val['WORK_ORDER_NO']] = $val['WORK_ORDER_NO'];
	            $work_order_no_arr[$val['PI_ID']]['WORK_ORDER_NO'] .= $val['WORK_ORDER_NO'].',';
	        }
		}
    }

    if ($batch_Ids != '')
    {
    	$batch_Ids = implode(',',array_flip(array_flip(explode(',', rtrim($batch_Ids,',')))));        
        $sql_batch_booking = sql_select("select id as BATCH_ID, BOOKING_NO from pro_batch_create_mst where id in($batch_Ids) and status_active=1 ");
        $batch_booking_arr=array();
        foreach ($sql_batch_booking as $val) {
        	$batch_booking_arr[$val['BATCH_ID']]=$val['BOOKING_NO'];
        }
    }

    if(!empty($wo_id_arr)){

        fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 140, 7, $wo_id_arr,$empty_arr);
        $sql_afterGoodRecPiLc="SELECT a.id as PI_ID, a.PI_NUMBER, b.WORK_ORDER_ID, b.WORK_ORDER_NO, d.LC_NUMBER, a.ITEM_CATEGORY_ID,
        a.ENTRY_FORM
	    from com_pi_master_details a, gbl_temp_engine c, com_pi_item_details b 
	    left join com_btb_lc_pi c on b.pi_id=c.pi_id and c.status_active=1 
	    left join com_btb_lc_master_details d on c.com_btb_lc_master_details_id=d.id and d.status_active=1 
	    where a.id=b.pi_id and b.work_order_id=c.ref_val  and a.status_active=1 and a.is_deleted=0 and a.importer_id = $company_name and b.status_active=1 and b.is_deleted=0  and c.entry_form=140 and c.ref_from=7 and c.user_id= $user_id 
	    group by a.id, a.pi_number, b.work_order_id, b.work_order_no, d.lc_number, a.ITEM_CATEGORY_ID,
        a.ENTRY_FORM";

	    $sql_afterGoodRecPiLc_res = sql_select($sql_afterGoodRecPiLc);
	    $fterGoodRec_PiNo_arr=array(); $fterGoodRec_lc_number_arr=array();
		foreach ($sql_afterGoodRecPiLc_res as $val) {
			if ($val['LC_NUMBER'] != '') {
				$fterGoodRec_lc_number_arr[$val['WORK_ORDER_ID']][$val['ITEM_CATEGORY_ID']]['LC_NUMBER']=$val['LC_NUMBER'];
			}
            $pi_id_array[$val['WORK_ORDER_ID']][$val['ITEM_CATEGORY_ID']]['PI_ID']=$val['PI_ID'];
            $catagorry_id_array[$val['WORK_ORDER_ID']][$val['ITEM_CATEGORY_ID']]['ITEM_CATEGORY_ID']=$val['ITEM_CATEGORY_ID'];
            $entry_form[$val['WORK_ORDER_ID']][$val['ITEM_CATEGORY_ID']]['ENTRY_FORM']=$val['ENTRY_FORM'];

			if ($fterGoodRec_PiNo_arr[$val['WORK_ORDER_ID']][$val['ITEM_CATEGORY_ID']][$val['PI_NUMBER']] == '') {
	            $fterGoodRec_PiNo_arr[$val['WORK_ORDER_ID']][$val['ITEM_CATEGORY_ID']][$val['PI_NUMBER']] = $val['PI_NUMBER'];
	            $fterGoodRec_PiNo_arr[$val['WORK_ORDER_ID']][$val['ITEM_CATEGORY_ID']]['PI_NUMBER'] = $val['PI_NUMBER'];
	        }
		}
    }

    if(!empty($batch_id_arrr)){

        fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 140, 8, $batch_id_arrr,$empty_arr);
        $sql_batch_booking = sql_select("SELECT a.id as BATCH_ID, a.BOOKING_NO from pro_batch_create_mst a, gbl_temp_engine b where a.id=b.ref_val and a.status_active=1 and b.entry_form=140 and b.ref_from=8 and b.user_id= $user_id");
        $batch_booking_arr=array();
        foreach ($sql_batch_booking as $val) {
        	$batch_booking_arr[$val['BATCH_ID']]=$val['BOOKING_NO'];
        }
    }

	$sql_currency=sql_select("select CONVERSION_RATE from currency_conversion_rate where currency=2 and company_id=$company_name and status_active=1 and is_deleted=0 order by con_date desc");
	$conversion_rate = $sql_currency[0]['CONVERSION_RATE'];

    $woven_fab_report_format=return_field_value("format_id"," lib_report_template","template_name ='".$company_name."'  and module_id=6 and report_id=125 and is_deleted=0 and status_active=1");
    $woven_fab_report_format_arr=explode(",",$woven_fab_report_format);
    $report_action = "";
    $format_id = 0;
    if(count($woven_fab_report_format_arr) > 0){
       if($woven_fab_report_format_arr[0] == 66){
           $report_action = "gwoven_finish_fabric_receive_print_3";
           $report_action1 = "gwoven_finish_fabric_receive_print_2";
           $format_id = 66;
       }elseif($woven_fab_report_format_arr[0] == 78){
           $report_action = "gwoven_finish_fabric_receive_print";
           $format_id = 78;
       }
    }
     $print_btn="";  $print_btn_req_wo=""; $partial_fab_wov_button_id="";
     $dayes_camical_button_id=""; $purces_req_button_id="";
    $general_item_report_arr = array("66"=>"general_item_receive_print_new","72"=>"general_item_receive_print_6","78"=>"general_item_receive_print","85"=>"general_item_receive_print_3","129"=>"general_item_receive_print_5","137"=>"general_item_receive_print_4","191"=>"general_item_receive_print_7","220"=>"general_item_receive_print_8");

    $gat_pi_print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$company_name."' and module_id=5 and report_id=183 and is_deleted=0 and status_active=1");	
    $gate_format_ids=explode(",",$gat_pi_print_report_format);
    $print_btn=$gate_format_ids[0];

    $gat_pi=return_field_value("format_id","lib_report_template","template_name ='".$company_name."' and module_id=5 and report_id=61 and is_deleted=0 and status_active=1");	
    $gate_format_ids=explode(",",$gat_pi);
    $purces_report_id=$gate_format_ids[0];

    $print_report_format=return_field_value("format_id"," lib_report_template","template_name ='".$company_name."'  and module_id=5 and report_id=30 and is_deleted=0 and status_active=1");
    $gate_format_ids=explode(",",$print_report_format);
    $other_purces_report_id=$gate_format_ids[0];

    $gat_req_print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$company_name."' and module_id=2 and report_id=26 and is_deleted=0 and status_active=1");	
    $print_format_ids=explode(",",$gat_req_print_report_format);
    $print_btn_req_wo=$print_format_ids[0];

    $print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$company_name."' and module_id=2 and report_id=35 and is_deleted=0 and status_active=1");
	$print_report_format_arr=explode(',',$print_report_format);
	$partial_fab_wov_button_id=$print_report_format_arr[0];

    $print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$company_name."' and module_id=5 and report_id in(132) and is_deleted=0 and status_active=1");
    $print_report_format_arr=explode(',',$print_report_format);
	$dayes_camical_button_id=$print_report_format_arr[0];

    $print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$company_name."'  and module_id=6 and report_id=39 and is_deleted=0 and status_active=1");
    $print_report_format_arr=explode(',',$print_report_format);
	$purces_req_button_id=$print_report_format_arr[0];

    $print_report_format=return_field_value("format_id"," lib_report_template","template_name ='".$company_name."'  and module_id=7 and report_id=67 and is_deleted=0 and status_active=1");
    $print_report_format_arr=explode(",",$print_report_format);
    $sales_print_button_id=$print_report_format_arr[0];

    $general_item_report_format=return_field_value("format_id"," lib_report_template","template_name ='".$company_name."'  and module_id=6 and report_id=194 and is_deleted=0 and status_active=1");
    $general_item_report_format_arr=explode(",",$general_item_report_format);

    $print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$company_name."'  and module_id=17 and report_id=259 and is_deleted=0 and status_active=1");
    $general_item_rec_report_format_arr=explode(",",$print_report_format);
    $row_matareal_report_id=$general_item_rec_report_format_arr[0];

    $print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$company_name."'  and module_id=6 and report_id=257 and is_deleted=0 and status_active=1");
    $general_item_rec_knitt_arr=explode(",",$print_report_format);
    $knit_gray_report_id=$general_item_rec_knitt_arr[0];

    $swo_print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$company_name."'  and module_id=19 and report_id=206 and is_deleted=0 and status_active=1");
    $service_work_order_arr=explode(",",$swo_print_report_format);
    $swo_report_id=$service_work_order_arr[0];

    $ydw_print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$company_name."'  and module_id=2 and report_id=7 and is_deleted=0 and status_active=1");
    $yarn_work_order_arr=explode(",",$ydw_print_report_format);
    $ydw_report_id=$yarn_work_order_arr[0];


    $print_report_format=return_field_value("format_id"," lib_report_template","template_name =$company_name  and module_id=6 and report_id=171 and is_deleted=0 and status_active=1");
    $print_report_format_ids=explode(",",$print_report_format);
    $print_btns=$print_report_format_ids[0];

    $print_report_format_ref=return_field_value("format_id","lib_report_template","template_name ='".$company_name."'  and module_id=6 and report_id=287 and is_deleted=0 and status_active=1");
    $trims_booking_report_format_one = 	return_field_value("format_id","lib_report_template","template_name ='".$company_name."' and module_id=6 and report_id in(230) and is_deleted=0 and status_active=1");
    $trims_booking_report_format = 	return_field_value("format_id","lib_report_template","template_name ='".$company_name."' and module_id=6 and report_id in(191) and is_deleted=0 and status_active=1");

    $trims_rec_report_id = return_field_value("format_id","lib_report_template","template_name ='".$company_name."' and module_id=6 and report_id in(287) and is_deleted=0 and status_active=1");
    $trims_rec_report_ids=explode(",",$trims_rec_report_id);
    $trims_rec_report_btns=$trims_rec_report_ids[0];

    
    $dyes_chemical_rcv_report_format = 	return_field_value("format_id","lib_report_template","template_name ='".$company_name."' and module_id=6 and report_id in(263) and is_deleted=0 and status_active=1");
    
    $general_item_action = "";
    $general_item_format_id = 0;
    if(count($general_item_report_format_arr) > 0){

        $general_item_action = $general_item_report_arr[$general_item_report_format_arr[0]];
        $general_item_format_id = $general_item_report_format_arr[0];
    }

	$tableWidth= 2050;
	ob_start();
        $html2 = '<form name="mrraudit_2" id="mrraudit_2">
        <fieldset style="width:2050px; margin-top:10px">
		<legend>MRR Auditing Report</legend>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2050" class="rpt_table" align="left">
				<thead>
					<th width="30">&nbsp;</th>
					<th width="30">SL</th>
					<th width="80">Company</th>
					<th width="100">Item Category</th>
					<th width="80">Store</th>
					<th width="80">Challan No</th>
					<th width="50">MRR No</th>               
					<th width="100">MRR Basis</th>
					<th width="60">MRR Date</th>
					<th width="50">Currency</th>
					<th width="50">Ex Rate</th>
					<th width="100">MRR Amount($)</th>
					<th width="100">MRR Amount (BDT)</th>
					<th width="180">Req./WO Number</th>
					<th width="100">Supplier</th>
					<th width="80">PI Number</th>					
					<th width="80">LC/TT Number</th>					
					<th width="80">Audit User</th>
					<th width="80">Auditor Name</th>
					<th width="80">Audit Date</th>
					<th width="80">Bill No</th>
					<th width="80">Bill Date</th>
					<th width="">Remarks</th>
				</thead>
			</table>
			<div style="width:2050px; overflow-y:scroll; max-height:330px;" id="scroll_body" align="left">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2030" class="rpt_table" id="table_body" align="left">
					<tbody>';
	?>
	<form name="mrraudit_2" id="mrraudit_2">
		<fieldset style="width:<? echo $tableWidth ; ?>px; margin-top:10px">
		<legend>MRR Auditing Report</legend>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tableWidth ; ?>" class="rpt_table" align="left">
				<thead>
					<th width="30">&nbsp;</th>
					<th width="30">SL</th>
					<th width="80">Company</th>
					<th width="100">Item Category</th>
					<th width="80">Store</th>
					<th width="80">Challan No</th>
					<th width="150">MRR No</th>               
					<th width="100">MRR Basis</th>
					<th width="60">MRR Date</th>
					<th width="50">Currency</th>
					<th width="50">Ex Rate</th>
					<th width="100">MRR Amount($)</th>
					<th width="100">MRR Amount (BDT)</th>
					<th width="180">Req./WO Number</th>
					<th width="100">Supplier</th>
					<th width="80">PI Number</th>					
					<th width="80">LC/TT Number</th>					
					<th width="80">Audit User</th>
					<th width="100">Auditor Name</th>
					<th width="80">Audit Date</th>
					<th width="100">Bill No</th>
					<th width="80">Bill Date</th>
					<th width="">Remarks</th>
				</thead>
			</table>
			<div style="width:<? echo $tableWidth ; ?>px; overflow-y:scroll; max-height:330px;" id="scroll_body" align="left">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tableWidth-20 ; ?>" class="rpt_table" id="table_body" align="left">
					<tbody>
						<?				
						$i=1; $j=0;	
						$tot_mrr_amount_doller_accessories=$tot_mrr_amount_doller=0;
						$tot_mrr_amount_accessories_tk=$tot_mrr_amount_tk=0;			
						// echo "<pre>";print_r($sql_mrr_res);die;
						foreach ($sql_mrr_res as $row)
						{
                            if($row['IS_MULTI']==3){ //Trims Receive Entry Multi Ref V3
                                $trims_booking_report_format = 	$trims_booking_report_format_one;
                            }
                            elseif($row['IS_MULTI']==1){
                                $print_report_format_ref_val= $print_report_format_ref;
                            }
                            else{
                                $trims_booking_report_format = 	$trims_booking_report_format;
                            }

                            $trims_booking_report_format_ids=explode(",",$trims_booking_report_format);
                            $trims_rec_ref_arr=explode(",",$print_report_format_ref_val);
                            $trim_report_action = "";
                            if(count($trims_booking_report_format_ids) > 0) {
                                if ($trims_booking_report_format_ids[0] == 86) {
                                    $trim_report_action = "trims_receive_entry_print";
                                }elseif ($trims_booking_report_format_ids[0] == 116) {
                                    $trim_report_action = "trims_receive_entry_print_2";
                                }
                                elseif ($trims_booking_report_format_ids[0] == 136) {
                                    $trim_report_action = "trims_receive_entry_print_4";
                                }
                        
                                elseif ($trims_booking_report_format_ids[0] == 78) {
                                    $trim_report_action = "trims_receive_entry_print";
                                }
                                elseif ($trims_booking_report_format_ids[0] == 84) {
                                    $trim_report_action = "trims_receive_entry_print2";
                                }
                            }
                            if(count($trims_rec_ref_arr) > 0) {                    
                                if ($trims_rec_ref_arr[0] == 86) {
                                    $trim_report_action = "trims_receive_entry_print";
                                }
                                elseif ($trims_rec_ref_arr[0] == 110) {
                                    $trim_report_action = "trims_receive_entry_print_2";
                                }
                            }

                            $dyes_chemical_rcv_report_format_ids=explode(",",$dyes_chemical_rcv_report_format);
                            $dyes_chemical_rcv_report_action = "";
                            if(count($dyes_chemical_rcv_report_format_ids) > 0) {
                                if ($dyes_chemical_rcv_report_format_ids[0] == 78) {
                                    $dyes_chemical_rcv_report_action = "chemical_dyes_receive_print";
                                }
                                elseif ($dyes_chemical_rcv_report_format_ids[0] == 84) {
                                    $dyes_chemical_rcv_report_action = "chemical_dyes_receive_print_new";
                                }
                            }

							if ($i%2==0) $bgcolor="#E9F3FF";
							else $bgcolor="#FFFFFF";

							if ($row['SUPPLIER_ID'] != 0) {
								$supplier=$supplier_arr[$row['SUPPLIER_ID']];
							} else if ($row['KNITTING_SOURCE']==1) {
								$supplier=$company_fullname_arr[$row['KNITTING_COMPANY']];
							} else {
								$supplier=$supplier_arr[$row['KNITTING_COMPANY']];
							}                           
							
							if($row['IS_AUDITED']==1) $checkedStatus = "checked='checked'";
							else $checkedStatus = '';
							?>

                            
							<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')"> 
								<td width="30" align="center" valign="middle">
									<input type="checkbox" id="chkAudit_<? echo $i; ?>" name="chkAudit" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" />
									<input type="hidden" id="hiddenid_<? echo $i; ?>" name="hiddenid_<? echo $i; ?>" value="<? echo $row['ID']; ?>"/>
									<input type="hidden" id="hiddenEntryForm_<? echo $i; ?>" name="hiddenEntryForm_<? echo $i; ?>" value="<? echo $row['ENTRY_FORM']; ?>"/>
								</td>   
								<td valign="middle" width="30" align="center"><? echo $i; ?></td>
								<td valign="middle" width="80" align="center" style="word-break:break-all;"><p><? echo $company_arr[$row['COMPANY_ID']]; ?></p></td>
								<td valign="middle" width="100"  align="center" style="word-break:break-all;"><p>
									<? 
									$item_category_names = ''; $item_id_arr = array();
                                    $item_id_arr= array_unique(explode(',', $row['ITEM_CATEGORY']));
                                    foreach($item_id_arr as $item_id) {
                                        $item_category_names .= $item_category[$item_id].',';
                                    }
                                    echo chop($item_category_names, ',');
									?>
								</p></td>
								<td valign="middle" width="80" align="center" style="word-break:break-all;"><p><? echo $store_arr[$row['STORE_ID']]; ?></p></td>
								<td valign="middle" width="80" align="center" style="word-break:break-all;"><p><? echo $row['CHALLAN_NO']; ?></p></td>
                                <?
                                if($row['ENTRY_FORM'] == 17)
                                {
                                    if($row['RECEIVE_BASIS'] == 1 && $format_id == 66)
                                    {
                                        ?>
								        <td valign="middle" width="150" align="center" style="word-break:break-all;" title="<? echo $row['RECV_NUMBER']; ?>"><a href="#" onclick="print_report( '<?=$row['COMPANY_ID']?>*<?=$row['ID']?>*<?=$row['BOOKING_ID']?>*Woven Finish Fabric Receive', '<?=$report_action1?>', '../finish_fabric/requires/woven_finish_fabric_receive_controller');"><? echo $row['RECV_NUMBER']; ?></a></td>
                                        <?
                                    } 
                                    elseif ($row['RECEIVE_BASIS'] != 1 && $format_id == 66)
                                    {
                                        ?>
                                        <td valign="middle" width="150" align="center" style="word-break:break-all;" title="<? echo $row['RECV_NUMBER']; ?>"><a href="#" onclick="print_report( '<?=$row['COMPANY_ID']?>*<?=$row['ID']?>*<?=$row['BOOKING_ID']?>*Woven Finish Fabric Receive', '<?=$report_action?>', '../finish_fabric/requires/woven_finish_fabric_receive_controller');"><? echo  $row['RECV_NUMBER']; ?></a></td>
                                        <?
                                    } 
                                    else 
                                    {
                                        ?>
                                        <td valign="middle" width="150" align="center" style="word-break:break-all;" title="<? echo $row['RECV_NUMBER']; ?>"><a href="#" onclick="print_report( '<?=$row['COMPANY_ID']?>*<?=$row['ID']?>*Woven Finish Fabric Receive', '<?=$report_action?>', '../finish_fabric/requires/woven_finish_fabric_receive_controller');"><? echo $row['RECV_NUMBER']; ?></a></td>
                                        <?
                                    }
                                }
                                else if($row['ENTRY_FORM'] == 20)
                                {
                                    ?>
                                    <td valign="middle" width="150" align="center" style="word-break:break-all;" title="<? echo $row['RECV_NUMBER']; ?>"><a style="color: #551a8b" target="_blank" href="../general_store/requires/general_item_receive_controller.php?data=<?=$row['COMPANY_ID']?>__<?=$row['ID']?>__General Item Receive__<?=$row['RECEIVE_BASIS']?>&action=<?=$general_item_action?>"><? echo $row['RECV_NUMBER']; ?></a></td>
                                    <?
                                }
                                else if($row['ENTRY_FORM'] == 263)
                                {
                                    ?>
                                    <td valign="middle" width="150" align="center" style="word-break:break-all;" title="<? echo $row['RECV_NUMBER']; ?>"> <a href="#" onClick="generate_row_matarial_item_rec_report('<?=$row_matareal_report_id?>','<? echo $row[csf("COMPANY_ID")];?>','<? echo $row[csf("ID")];?>','','<? echo $row[csf("RECEIVE_BASIS")];?>' )">  <? echo  $row['RECV_NUMBER']; ?></a> </td>
                                    <?
                                } 
                                else if($row['ENTRY_FORM'] == 22)
                                {
                                    ?>
                                    <td valign="middle" width="150" align="center" style="word-break:break-all;" title="<? echo $row['RECV_NUMBER']; ?>"> <a href="#" onClick="generate_knit_grey_fabric_receive_print('<?=$knit_gray_report_id?>','<? echo $row[csf("COMPANY_ID")];?>','<? echo $row[csf("ID")];?>','','<? echo $row[csf("BOOKING_NO")];?>','<? echo $row[csf("RECEIVE_BASIS")];?>','<? echo $row[csf("LOCATION_ID")];?>' )">  <? echo  $row['RECV_NUMBER']; ?></a> </td>
                                    <?
                                }  
                                elseif($row['ENTRY_FORM'] == 4)
                                {
                                    ?>
                                    <td valign="middle" width="150" align="center" style="word-break:break-all;" title="<? echo $row['RECV_NUMBER']; ?>"><a href="#" onclick="show_mrr_dtls('<? echo $row['COMPANY_ID']."__".$row['ID']."__".$row['RECV_NUMBER']."__".$row['ITEM_CATEGORY']."__".$row['VARIABLE_SETTING']."__".$row['RECEIVE_BASIS']."__".$row['LOCATION_ID']."__".$row['ENTRY_FORM']."__".$dyes_chemical_rcv_report_action."__".$row['IS_MULTI']; ?>');"><? echo  $row['RECV_NUMBER']; ?></a></td>
                                    <?
                                }
                                elseif($row['ENTRY_FORM'] == 558)
                                {
                                    $recv_date = date("d-M-Y",strtotime($row['RECEIVE_DATE']));
                                    ?>
                                    <td valign="middle" width="150" align="center" style="word-break:break-all;" title="<? echo $row['RECV_NUMBER']; ?>"><a href="#" onclick="show_mrr_dtls('<? echo $row['RECV_NUMBER']."__".$row['COMPANY_ID']."__".$row['RECEIVE_BASIS']."__".$row['BOOKING_NO']."__".$row['CHALLAN_NO']."__".$recv_date."__".$row['SUPPLIER_ID']."__".$row['ENTRY_FORM']."__".$row['AUDIT_REMARK']."__".$row['ID']."__".$row['EXCHANGE_RATE']; ?>');"><? echo  $row['RECV_NUMBER']; ?></a></td>
                                    <?
                                }
                                elseif($row['ENTRY_FORM'] == 24 && $row['IS_MULTI'] == 1){
                                    ?>
                                    <td valign="middle" width="150" align="center" style="word-break:break-all;" title="<? echo $row['RECV_NUMBER']; ?>"><a href="#" onClick="show_mrr_dtls('<? echo $row['COMPANY_ID']."__".$row['ID']."__".$row['RECV_NUMBER']."__".$row['ITEM_CATEGORY']."__".$row['VARIABLE_SETTING']."__".$row['RECEIVE_BASIS']."__".$row['LOCATION_ID']."__".$row['ENTRY_FORM']."__".$trim_report_action."__".$row['IS_MULTI']."__".$trims_rec_report_btns; ?>');"><? echo  $row['RECV_NUMBER']; ?></a></td>
                                    <?
                                } elseif($row['ENTRY_FORM'] == 58 && $row['RECEIVE_BASIS']==10){
                                    ?>                         
                                    <td width="150" align="center" style="word-break:break-all;"><a href="#" onClick="generate_report_grey_fabric_mrr('<?=$print_btns?>','<? echo $row["COMPANY_ID"];?>','<? echo $row["ID"];?>','<? echo $row["RECV_NUMBER"];?>','<? echo $row["LOCATION_ID"];?>','<? echo $row["STORE_ID"];?>');"><? echo $row['RECV_NUMBER']; ?></a></td>
                                    <?
                                }
                                else{
                                    ?>
                                    <td valign="middle" width="150" align="center" style="word-break:break-all;" title="<? echo $row['RECV_NUMBER']; ?>"><a href="#" onclick="show_mrr_dtls('<? echo $row['COMPANY_ID']."__".$row['ID']."__".$row['RECV_NUMBER']."__".$row['ITEM_CATEGORY']."__".$row['VARIABLE_SETTING']."__".$row['RECEIVE_BASIS']."__".$row['LOCATION_ID']."__".$row['ENTRY_FORM']."__".$trim_report_action."__".$row['IS_MULTI']; ?>');"><? echo  $row['RECV_NUMBER']; ?></a></td>
                                    <?
                                }
                                ?>
                                <?if($row['ENTRY_FORM']==558):?>
                                    <td valign="middle" width="100" style="word-break:break-all;"><p><? echo $basis_on[$row['RECEIVE_BASIS']]; ?></p></td>
                                <?else:?>
                                    <td valign="middle" width="100" style="word-break:break-all;"><p><? echo $receive_basis_arr[$row['RECEIVE_BASIS']]; ?></p></td>
                                <?endif;?>
								<td valign="middle" width="60" align="center" style="word-break:break-all;"><? echo change_date_format($row['RECEIVE_DATE']); ?>&nbsp;</td>
								<td valign="middle" width="50" align="center"><p><? echo $currency[$row['CURRENCY_ID']]; ?></p></td>
								<td valign="middle" width="50" align="center"><p><? echo $row['EXCHANGE_RATE']; ?></p></td>

								<td valign="middle" width="100" align="right"><p>
									<?
                                    if ($row['CURRENCY_ID'] == 1) 
                                    {
                                        $mrr_amount_doller=$row['ORDER_AMOUNT']/$conversion_rate;
                                        $mrr_amount_doller_accessories=$row['ORDER_AMOUNT_ACCESSORIES']/$conversion_rate;
                                    }
                                    else 
                                    {
                                        $mrr_amount_doller=$row['ORDER_AMOUNT'];
                                        $mrr_amount_doller_accessories=$row['ORDER_AMOUNT_ACCESSORIES'];
                                    }	
                                    if ($row['ENTRY_FORM'] == 24) 
                                    {
                                        $tot_mrr_amount_doller_accessories+=$mrr_amount_doller_accessories;
                                        echo number_format($mrr_amount_doller_accessories,2);
                                    }
                                    else 
                                    {
                                        $tot_mrr_amount_doller+=$mrr_amount_doller;
                                        echo number_format($mrr_amount_doller,2);
                                    } ?>
                                </p></td>

                                <td valign="middle" width="100" align="right"><p>
                                    <? 
                                    if ($row['CURRENCY_ID'] == 1)
                                    {
                                        $mrr_amount_tk=$mrr_amount_doller*$conversion_rate;
                                        $mrr_amount_accessories_tk=$mrr_amount_doller_accessories*$conversion_rate;
                                    } 
                                    else
                                    {
                                        $mrr_amount_tk=$row['CONS_AMOUNT'];
                                        $mrr_amount_accessories_tk=$row['CONS_AMOUNT_ACCESSORIES'];
                                    }
                                    if ($row['ENTRY_FORM'] == 24)
                                    {
                                        $tot_mrr_amount_accessories_tk+=$mrr_amount_accessories_tk;
                                        echo number_format($mrr_amount_accessories_tk,2);
                                    }
                                    else
                                    {
                                        $tot_mrr_amount_tk+=$mrr_amount_tk;
                                        echo number_format($mrr_amount_tk,2);
                                    }
                                    ?>										
                                </p></td>
                                <?
                                if($booking_entry_form[$row['BOOKING_ID']][$row['ITEM_CATEGORY']]['BOOKING_ENTRY_FORM']==271)
                                {
                                    ?>
                                    <td valign="middle" width="180" align="center"><p><a href="#" onClick="generate_wo_booking_print('<? echo $booking_entry_form[$row['BOOKING_ID']][$row['ITEM_CATEGORY']]['WORK_ORDER_NO'];?>','<? echo $partial_fab_wov_button_id;?>','<? echo $row[csf("COMPANY_ID")]; ?>','','<? echo $row['ITEM_CATEGORY']; ?>','<? echo $booking_entry_form[$row['BOOKING_ID']][$row['ITEM_CATEGORY']]['FABRIC_SOURCE'];?>','','','1','','','','')">                                       
                                        <? 
                                        if ($row['RECEIVE_BASIS'] == 1) {
                                            echo rtrim($work_order_no_arr[$row['BOOKING_ID']]['WORK_ORDER_NO'],',');
                                        } else if ($row['RECEIVE_BASIS'] == 5) {
                                            $batch_booking_names = ''; $batch_id_arr = array();
                                            $batch_id_arr= array_unique(explode(',', $row['PI_WO_BATCH_ID']));
                                            foreach($batch_id_arr as $batch_id){
                                                $batch_booking_names .= $batch_booking_arr[$batch_id].',';
                                            }
                                            echo rtrim($batch_booking_names, ',');
                                        }
                                        else{
                                            echo $row['BOOKING_NO'] === '0' ? '' : $row['BOOKING_NO'];
                                        }
                                        ?></a></p>
                                    </td>
                                    <?
                                }
                                elseif($booking_arr[$row["BOOKING_ID"]]["BOOKING_ENTRY_FORM"]== 108)
                                {
                                    ?>
                                    <td valign="middle" width="180" align="center"><p><a href="#" onClick="generate_knitting_booking_print('<? echo $row['BOOKING_NO'];?>','<? echo $partial_fab_wov_button_id;?>','<? echo $row[csf("COMPANY_ID")]; ?>','','<? echo $row['ITEM_CATEGORY']; ?>','<? echo $booking_arr[$row['BOOKING_ID']]['FABRIC_SOURCE'];?>','','','1','','','','')">                                       
                                        <? 
                                        if ($row['RECEIVE_BASIS'] == 1) {
                                            echo rtrim($work_order_no_arr[$row['BOOKING_ID']]['WORK_ORDER_NO'],',');
                                        } else if ($row['RECEIVE_BASIS'] == 5) {
                                            $batch_booking_names = ''; $batch_id_arr = array();
                                            $batch_id_arr= array_unique(explode(',', $row['PI_WO_BATCH_ID']));
                                            foreach($batch_id_arr as $batch_id){
                                                $batch_booking_names .= $batch_booking_arr[$batch_id].',';
                                            }
                                            echo rtrim($batch_booking_names, ',');
                                        }
                                        else{
                                            echo $row['BOOKING_NO'] === '0' ? '' : $row['BOOKING_NO'];
                                        }
                                        ?></a></p>
                                    </td>
                                    <?
                                }                               
                                elseif($booking_entry_form[$row['BOOKING_ID']][$row['ITEM_CATEGORY']]['DYS_CAMICAL_ENTRY_FORM']==145)
                                { 
                                    ?>
                                    <td valign="middle" width="180" align="center"><p><a href="#" onClick="generate_dyes_camical_print('<? echo $row[csf("COMPANY_ID")]; ?>','<? echo $dayes_camical_button_id;?>','<? echo $booking_entry_form[$row['BOOKING_ID']][$row['ITEM_CATEGORY']]['DYES_UPDATE_ID']; ?>','','')">
                                    <? 
                                    if ($row['RECEIVE_BASIS'] == 1) {
                                        echo rtrim($work_order_no_arr[$row['BOOKING_ID']]['WORK_ORDER_NO'],',');
                                    } else if ($row['RECEIVE_BASIS'] == 5) {
                                        $batch_booking_names = ''; $batch_id_arr = array();
                                        $batch_id_arr= array_unique(explode(',', $row['PI_WO_BATCH_ID']));
                                        foreach($batch_id_arr as $batch_id){
                                            $batch_booking_names .= $batch_booking_arr[$batch_id].',';
                                        }
                                        echo rtrim($batch_booking_names, ',');
                                    }
                                    else{
                                        echo $row['BOOKING_NO'] === '0' ? '' : $row['BOOKING_NO'];
                                    }
                                    ?>
                                    </a></p></td>  
                                    <? 
                                } 
                                 elseif($booking_entry_form[$row['BOOKING_ID']][$row['ITEM_CATEGORY']]['DYS_CAMICAL_ENTRY_FORM']==145)
                                { 
                                    ?>
                                    <td valign="middle" width="180" align="center"><p><a href="#" onClick="generate_dyes_camical_print('<? echo $row[csf("COMPANY_ID")]; ?>','<? echo $dayes_camical_button_id;?>','<? echo $booking_entry_form[$row['BOOKING_ID']][$row['ITEM_CATEGORY']]['DYES_UPDATE_ID']; ?>','','')">
                                    <? 
                                    if ($row['RECEIVE_BASIS'] == 1) {
                                        echo rtrim($work_order_no_arr[$row['BOOKING_ID']]['WORK_ORDER_NO'],',');
                                    } else if ($row['RECEIVE_BASIS'] == 5) {
                                        $batch_booking_names = ''; $batch_id_arr = array();
                                        $batch_id_arr= array_unique(explode(',', $row['PI_WO_BATCH_ID']));
                                        foreach($batch_id_arr as $batch_id){
                                            $batch_booking_names .= $batch_booking_arr[$batch_id].',';
                                        }
                                        echo rtrim($batch_booking_names, ',');
                                    }
                                    else{
                                        echo $row['BOOKING_NO'] === '0' ? '' : $row['BOOKING_NO'];
                                    }
                                    ?>
                                    </a></p></td>  
                                    <? 
                                } 
                                elseif($booking_entry_form[$row['BOOKING_ID']][$row['ITEM_CATEGORY']]['DYS_CAMICAL_ENTRY_FORM']==144)
                                { 
                                    ?>
                                    <td valign="middle" width="180" align="center"><p><a href="#" onClick="generate_dyes_camical_print('<? echo $row[csf("COMPANY_ID")]; ?>','<? echo $dayes_camical_button_id;?>','<? echo $booking_entry_form[$row['BOOKING_ID']][$row['ITEM_CATEGORY']]['DYES_UPDATE_ID']; ?>','','')">
                                    <? 
                                    if ($row['RECEIVE_BASIS'] == 1) {
                                       echo rtrim($work_order_no_arr[$row['BOOKING_ID']]['WORK_ORDER_NO'],',');
                                    } else if ($row['RECEIVE_BASIS'] == 5) {
                                       $batch_booking_names = ''; $batch_id_arr = array();
                                       $batch_id_arr= array_unique(explode(',', $row['PI_WO_BATCH_ID']));
                                       foreach($batch_id_arr as $batch_id){
                                           $batch_booking_names .= $batch_booking_arr[$batch_id].',';
                                       }
                                       echo rtrim($batch_booking_names, ',');
                                    }
                                    else{
                                       echo $row['BOOKING_NO'] === '0' ? '' : $row['BOOKING_NO'];
                                    }
                                    ?>
                                    </a></p></td>  
                                    <? 
                                }
                                elseif($stationary_purchase[$row["BOOKING_ID"]][$row["BOOKING_NO"]]["ENTRY_FORM"]==146)
                                { 
                                    ?>
                                    <td valign="middle" width="180" align="center"><p><a href="#" onClick="generate_stationary_purchase_order('<? echo $row[csf("COMPANY_ID")]; ?>','<? echo $purces_report_id;?>','<? echo $row[csf("BOOKING_NO")]; ?>','','<? echo $row[csf("SUPPLIER_ID")]; ?>','<? echo change_date_format($row[csf("receive_date")]); ?>','<? echo $stationary_purchase[$row["BOOKING_ID"]]["CURRENCY_ID"]; ?>','<? echo $stationary_purchase[$row["BOOKING_ID"]]["WO_BASIS_ID"]; ?>','<? echo $stationary_purchase[$row["BOOKING_ID"]]["PAY_MODE"]; ?>','<? echo $stationary_purchase[$row["BOOKING_ID"]]["SOURCE"]; ?>','<? echo change_date_format($stationary_purchase[$row["BOOKING_ID"]]["DELIVERY_DATE"]); ?>','<? echo $stationary_purchase[$row["BOOKING_ID"]]["ATTENTION"]; ?>','','<? echo $stationary_purchase[$row["BOOKING_ID"]]["REQUISITION_NO"]; ?>','','','<? echo $stationary_purchase[$row["BOOKING_ID"]]["DYES_UPDATE_ID"]; ?>','','<? echo $stationary_purchase[$row["BOOKING_ID"]]["LOCATION_ID"]; ?>',1,'','','','','','','')">
                                    <? 
                                    if ($row['RECEIVE_BASIS'] == 1) {
                                       echo rtrim($work_order_no_arr[$row['BOOKING_ID']]['WORK_ORDER_NO'],',');
                                    } else if ($row['RECEIVE_BASIS'] == 5) {
                                       $batch_booking_names = ''; $batch_id_arr = array();
                                       $batch_id_arr= array_unique(explode(',', $row['PI_WO_BATCH_ID']));
                                       foreach($batch_id_arr as $batch_id){
                                           $batch_booking_names .= $batch_booking_arr[$batch_id].',';
                                       }
                                       echo rtrim($batch_booking_names, ',');
                                    }
                                    else{
                                       echo $row['BOOKING_NO'] === '0' ? '' : $row['BOOKING_NO'];
                                    }
                                    ?>
                                    </a></p></td>  
                                    <? 
                                }

                                elseif($stationary_purchase[$row["BOOKING_ID"]][$row["BOOKING_NO"]]["ENTRY_FORM"]==147)
                                { 
                                    ?>
                                    <td valign="middle" width="180" align="center"><p><a href="#" onClick="generate_Others_Purchase_Order('<? echo $row[csf("COMPANY_ID")]; ?>','<? echo $other_purces_report_id;?>','<? echo $stationary_purchase[$row["BOOKING_ID"]]["DYES_UPDATE_ID"]; ?>','<? echo $stationary_purchase[$row["BOOKING_ID"]]["REQUISITION_NO"]; ?>','','<? echo $stationary_purchase[$row["BOOKING_ID"]]["LOCATION_ID"]; ?>',1,'<? echo $stationary_purchase[$row["BOOKING_ID"]]["WO_NUMBER"]; ?>','<? echo change_date_format($stationary_purchase[$row["BOOKING_ID"]]["WO_DATE"]); ?>','')">
                                    <? 
                                    if ($row['RECEIVE_BASIS'] == 1) {
                                       echo rtrim($work_order_no_arr[$row['BOOKING_ID']]['WORK_ORDER_NO'],',');
                                    } else if ($row['RECEIVE_BASIS'] == 5) {
                                       $batch_booking_names = ''; $batch_id_arr = array();
                                       $batch_id_arr= array_unique(explode(',', $row['PI_WO_BATCH_ID']));
                                       foreach($batch_id_arr as $batch_id){
                                           $batch_booking_names .= $batch_booking_arr[$batch_id].',';
                                       }
                                       echo rtrim($batch_booking_names, ',');
                                    }
                                    else{
                                       echo $row['BOOKING_NO'] === '0' ? '' : $row['BOOKING_NO'];
                                    }
                                    ?>
                                    </a></p></td>  
                                    <? 
                                }
                                elseif($req_arr[$row['BOOKING_ID']]['ENTRY_FORM']==69)
                                {
                                    ?>
                                    <td valign="middle" width="180" align="center"><p><a href="#" onClick="generate_purchase_requisition_print('<? echo $row[csf("COMPANY_ID")]; ?>','<? echo $purces_req_button_id;?>','<? echo $req_arr[$row['BOOKING_ID']]['ID']; ?>','<? echo $req_arr[$row['BOOKING_ID']]['REMARKS'];?>','','<? echo $req_arr[$row['BOOKING_ID']]['LOCATION_ID'];?>','<? echo $req_arr[$row['BOOKING_ID']]['IS_APPROVED'];?>')">
                                    <? 
                                    if ($row['RECEIVE_BASIS'] == 1) {
                                      echo rtrim($work_order_no_arr[$row['BOOKING_ID']]['WORK_ORDER_NO'],',');
                                    } else if ($row['RECEIVE_BASIS'] == 5) {
                                      $batch_booking_names = ''; $batch_id_arr = array();
                                      $batch_id_arr= array_unique(explode(',', $row['PI_WO_BATCH_ID']));
                                      foreach($batch_id_arr as $batch_id){
                                          $batch_booking_names .= $batch_booking_arr[$batch_id].',';
                                      }
                                      echo rtrim($batch_booking_names, ',');
                                    }
                                    else{
                                        echo $row['BOOKING_NO'] === '0' ? '' : $row['BOOKING_NO'];
                                    }
                                    ?>
                                   </a></p></td>  
                                    <? 
                                }
                                else if($booking_arr[$row['BOOKING_ID']]['BOOKING_ENTRY_FORM']==87)
                                { 
                                    ?> 
                                    <td valign="middle" width="180" align="center"><p><a href="#" onClick="generate_req_booking_print('<? echo $row['BOOKING_NO'];?>','<? echo $print_btn_req_wo;?>','<? echo $row[csf("COMPANY_ID")]; ?>','<? echo $row[csf("IS_APPROVED")]; ?>','','','','')">
                                    <? 
									if ($row['RECEIVE_BASIS'] == 1) {
										echo rtrim($work_order_no_arr[$row['BOOKING_ID']]['WORK_ORDER_NO'],',');
									} else if ($row['RECEIVE_BASIS'] == 5) {
										$batch_booking_names = ''; $batch_id_arr = array();
                                        $batch_id_arr= array_unique(explode(',', $row['PI_WO_BATCH_ID']));
                                        foreach($batch_id_arr as $batch_id){
                                            $batch_booking_names .= $batch_booking_arr[$batch_id].',';
                                        }
                                        echo rtrim($batch_booking_names, ',');
									}
									else{
                                        echo $row['BOOKING_NO'] === '0' ? '' : $row['BOOKING_NO'];
                                    }
									?>                               
                                    </a></p></td>  
                                    <? 
                                }
                                else if($sales_wo_arr[$row['BOOKING_ID']]['ENTRY_FORM']==109)
                                { 
                                    ?> 
                                    <td valign="middle" width="180" align="center"><p><a href="#" onClick="fabric_sales_order_entry_fnc('<? echo $sales_print_button_id;?>','<? echo $sales_wo_arr[$row['BOOKING_ID']]['WITHIN_GROUP'];?>','<? echo  $sales_wo_arr[$row['BOOKING_ID']]['COMPANY_ID']; ?>','','<? echo $sales_wo_arr[$row['BOOKING_ID']]['SALES_BOOKING_NO']; ?>','<? echo $sales_wo_arr[$row['BOOKING_ID']]['JOB_NO']; ?>','','<? echo $sales_wo_arr[$row['BOOKING_ID']]['ID']; ?>')">
                                     <? 
                                    if ($row['RECEIVE_BASIS'] == 1) {
                                       echo rtrim($work_order_no_arr[$row['BOOKING_ID']]['WORK_ORDER_NO'],',');
                                    } else if ($row['RECEIVE_BASIS'] == 5) {
                                       $batch_booking_names = ''; $batch_id_arr = array();
                                       $batch_id_arr= array_unique(explode(',', $row['PI_WO_BATCH_ID']));
                                       foreach($batch_id_arr as $batch_id){
                                           $batch_booking_names .= $batch_booking_arr[$batch_id].',';
                                       }
                                       echo rtrim($batch_booking_names, ',');
                                    }
                                    else{
                                       echo $row['BOOKING_NO'] === '0' ? '' : $row['BOOKING_NO'];
                                    }
                                    ?>                               
                                    </a></p></td>  
                                    <? 
                                }
                                elseif($row['ENTRY_FORM'] == 558)
                                {
                                    ?>
                                        <td valign="middle" width="180" align="center"><p>
                                            <a href="#" onClick="generate_swo_print('<? echo $row['COMPANY_ID'];?>','<? echo $swo_report_id;?>','<? echo $row[csf("BOOKING_ID")]; ?>','')">
                                            <? echo $row['BOOKING_NO'] === '0' ? '' : $row['BOOKING_NO']; ?>
                                            </a>
                                        </p></td>
                                    <?
                                }   
                                elseif($wo_yarn_dying_arr[$row['BOOKING_ID']]['BOOKING_ENTRY_FORM']==41)
                                {  
                                    ?>
                                    <td valign="middle" width="180" align="center"><p>
                                            <a href="#" onClick="generate_ydw_print('<? echo $wo_yarn_dying_arr[$row["BOOKING_ID"]]["COMPANY_ID"];?>','<? echo $wo_yarn_dying_arr[$row["BOOKING_ID"]]["SUPPLIER_ID"];?>','<? echo $wo_yarn_dying_arr[$row["BOOKING_ID"]]["YDW_NO"]; ?>','<? echo $wo_yarn_dying_arr[$row["BOOKING_ID"]]["PAY_MODE"]; ?>','<? echo  $wo_yarn_dying_arr[$row["BOOKING_ID"]]["ID"];?>','<?echo $ydw_report_id;?>','<? echo  $wo_yarn_dying_arr[$row["BOOKING_ID"]]["BUDGET_VERSION"];?>')">
                                            <? echo $row['BOOKING_NO'] === '0' ? '' : $row['BOOKING_NO']; ?>
                                            </a>
                                        </p>
                                    </td>
                                    <?
                                }  
                                                          
                                else 
                                { 
                                    ?>
                                    <td valign="middle" width="180" align="center"><p>
                                    <? 
                                    if ($row['RECEIVE_BASIS'] == 1) {
                                      echo rtrim($work_order_no_arr[$row['BOOKING_ID']]['WORK_ORDER_NO'],',');
                                    } else if ($row['RECEIVE_BASIS'] == 5) {
                                      $batch_booking_names = ''; $batch_id_arr = array();
                                      $batch_id_arr= array_unique(explode(',', $row['PI_WO_BATCH_ID']));
                                      foreach($batch_id_arr as $batch_id){
                                          $batch_booking_names .= $batch_booking_arr[$batch_id].',';
                                      }
                                      echo rtrim($batch_booking_names, ',');
                                    }
                                    else{
                                      echo $row['BOOKING_NO'] === '0' ? '' : $row['BOOKING_NO'];
                                    }
                                    ?>
                                    </p></td> 
                                    <? 
                                }
                                ?>																
								<td valign="middle" width="100" style="word-break:break-all;"><p><? echo $supplier; ?></p></td>
                                <?
                                
                                $itmCat = explode(',',$row['ITEM_CATEGORY']);
                                $item_category_wo = $itmCat[0];
                                if($item_category_wo!=''){
                                    $item_category_wo =$item_category_wo ;
                                }
                                else{
                                    $item_category_wo =$row['ITEM_CATEGORY'];
                                }
                                ?>
								<td valign="middle" width="80" style="word-break:break-all;"><p> <a href="#" onClick="generate_pi_proforma_report('<? echo $row[csf('company_id')]?>','<? echo $pi_id_array[$row['BOOKING_ID']][$item_category_wo]['PI_ID']?>','<? echo $print_btn ?>','<? echo $catagorry_id_array[$row['BOOKING_ID']][$item_category_wo]['ITEM_CATEGORY_ID']?>','<? echo $entry_form[$row['BOOKING_ID']][$item_category_wo]['ENTRY_FORM']?>')">                           
                                    <? 
                                    if($row['RECEIVE_BASIS'] == 1){
                                        echo $row['BOOKING_NO'];
                                    }elseif($row['RECEIVE_BASIS']==2){
                                        echo $fterGoodRec_PiNo_arr[$row['BOOKING_ID']][$row['ITEM_CATEGORY']]['PI_NUMBER'];
                                    }
									?>
                                    </a></p></td>								
								<td valign="middle" width="80" style="word-break:break-all;"><p>
									<? 
									if ($row['RECEIVE_BASIS'] == 1) {
										echo $lc_number_arr[$row['BOOKING_ID']][$row['ITEM_CATEGORY']]['LC_NUMBER'];
									} else  {
										echo $fterGoodRec_lc_number_arr[$row['BOOKING_ID']][$row['ITEM_CATEGORY']]['LC_NUMBER'];
									}
									?></p></td>							
								<td valign="middle" width="80" align="center" style="word-break:break-all;"><p><? echo $user_id_arr[$row['AUDIT_BY']]; ?></p></td>
								<td valign="middle" width="100" style="word-break:break-all;"><p><? echo $user_name[$row['AUDIT_BY']]; ?></p></td>
								<td valign="middle" width="80" align="center" style="word-break:break-all;"><? echo change_date_format($row['AUDIT_DATE']); ?>&nbsp;</td>
                                <? if($row['ENTRY_FORM'] == 558) : ?>
                                    <td valign="middle" width="100" align="center" style="word-break:break-all;">
                                        <a href="#" onClick="generate_service_bill_print('<?echo $company_name; ?>','<?echo $subcon_bill_arr[$row["BOOKING_ID"]]["ID"]; ?>','<?echo $subcon_bill_arr[$row["BOOKING_ID"]]["BILL_NO"]; ?>')">
                                            <? echo $subcon_bill_arr[$row["BOOKING_ID"]]["BILL_NO"]; ?> &nbsp;
                                        </a>
                                    </td>

                                    <td valign="middle" width="80" align="center" style="word-break:break-all;"><? echo $subcon_bill_arr[$row["BOOKING_ID"]]["BILL_DATE"]; ?>&nbsp;</td>
                                <?else : ?>
								    <td valign="middle" width="80" align="center" style="word-break:break-all;"> <a href="#" onClick="generate_bill_process_print('<?echo $bill_arr[$row["BOOKING_ID"]]["COMPANY_ID"]; ?>','<?echo $bill_arr[$row["BOOKING_ID"]]["ID"]; ?>','<?echo $bill_arr[$row["BOOKING_ID"]]["BILL_NUMBER"]; ?>','<?echo $bill_arr[$row["BOOKING_ID"]]["PARTY_ID"]; ?>','','','<? echo $bill_arr[$row["BOOKING_ID"]]["RECEIVE_ID"]; ?>','<? echo $bill_arr[$row["BOOKING_ID"]]["PARTY_ID"]; ?>','<?echo $bill_arr[$row["BOOKING_ID"]]["BILL_NO"]; ?>','<?echo $bill_arr[$row["BOOKING_ID"]]["BILL_DATE"]; ?>','<? echo $bill_arr[$row["BOOKING_ID"]]["BUYER_ID"]; ?>')">  <? echo $bill_arr[$row["BOOKING_ID"]]["BILL_NO"]; ?></a> &nbsp;</td>

								    <td valign="middle" width="80" align="center" style="word-break:break-all;"><? echo $bill_arr[$row["BOOKING_ID"]]["BILL_DATE"]; ?>&nbsp;</td>
                                <? endif; ?>

								<td valign="middle" width="" class="txtRemarksInput">
                                    <input type="text" name="txtRemarks_<?php echo $i; ?>" id="txtRemarks_<?php echo $i; ?>" value="<? echo $row['AUDIT_REMARK']; ?>" style="width:90%" class="text_boxes">
                                    <span style="display: none;"><? echo $row['AUDIT_REMARK']; ?></span>
                                </td>
							</tr>
							<?
                            $html2 .= '<tr bgcolor="'.$bgcolor .'" id="tr_'.$i.'" onClick="change_color(\'tr_'.$i.'\',\''.$bgcolor.'\')"> 
                            <td width="30" align="center" valign="middle">
                                <input type="checkbox" id="chkAudit_'.$i.'" name="chkAudit" onClick="change_color(\'tr_'.$i.'\',\''.$bgcolor.'\')" />
                                <input type="hidden" id="hiddenid_'.$i.'" name="hiddenid_'.$i.'" value="'.$row['ID'].'" />
                            </td>   
                            <td valign="middle" width="30" align="center">'.$i.'</td>
                            <td valign="middle" width="80" align="center" style="word-break:break-all;"><p>'.$company_arr[$row['COMPANY_ID']].'</p></td>
                            <td valign="middle" width="100" align="center" style="word-break:break-all;"><p>
                                '.chop($item_category_names, ',').'
                            </p></td>
                            <td valign="middle" width="80" align="center" style="word-break:break-all;"><p>'.$store_arr[$row['STORE_ID']].'</p></td>
                            <td valign="middle" width="80" align="center" style="word-break:break-all;"><p>'.$row['CHALLAN_NO'].'</p></td>';
                            
                            if($row['ENTRY_FORM'] == 17)
                            {
                                if($row['RECEIVE_BASIS'] == 1 && $format_id == 66)
                                {
                                    $html2 .= '<td valign="middle" width="50" align="center" style="word-break:break-all;">'.$row['RECV_NUMBER_PREFIX_NUM'].'</td>';
                                } 
                                elseif ($row['RECEIVE_BASIS'] != 1 && $format_id == 66)
                                {
                                    $html2 .= '<td valign="middle" width="50" align="center" style="word-break:break-all;">'.$row['RECV_NUMBER_PREFIX_NUM'].'</td>';
                                } 
                                else 
                                {
                                    $html2 .= '<td valign="middle" width="50" align="center" style="word-break:break-all;">'.$row['RECV_NUMBER_PREFIX_NUM'].'</td>';
                                }
                            }
                            else if($row['ENTRY_FORM'] == 20)
                            {
                                $html2 .= '<td valign="middle" width="50" align="center" style="word-break:break-all;">'.$row['RECV_NUMBER_PREFIX_NUM'].'</td>';
                            }
                            else if($row['ENTRY_FORM'] == 263)
                            {
                                $html2 .= '<td valign="middle" width="50" align="center" style="word-break:break-all;">'.$row['RECV_NUMBER_PREFIX_NUM'].'</td>';
                            } 
                            else if($row['ENTRY_FORM'] == 22)
                            {
                                $html2 .= '<td valign="middle" width="50" align="center" style="word-break:break-all;">'.$row['RECV_NUMBER_PREFIX_NUM'].'</td>';
                            }  
                            else
                            {
                                $html2 .= '<td valign="middle" width="50" align="center" style="word-break:break-all;">'.$row['RECV_NUMBER_PREFIX_NUM'].'</td>';
                            }
                            $html2 .= '<td valign="middle" width="100" style="word-break:break-all;"><p>'.$receive_basis_arr[$row['RECEIVE_BASIS']].'</p></td>
                            <td valign="middle" width="60" align="center" style="word-break:break-all;">'.change_date_format($row['RECEIVE_DATE']).'&nbsp;</td>
                            <td valign="middle" width="50" align="center"><p>'.$currency[$row['CURRENCY_ID']].'</p></td>
                            <td valign="middle" width="50" align="center"><p>'.$row['EXCHANGE_RATE'].'</p></td>
                            <td valign="middle" width="100" align="right"><p>';
                            if ($row['CURRENCY_ID'] == 1) 
                            {
                                $mrr_amount_doller=$row['ORDER_AMOUNT']/$conversion_rate;
                                $mrr_amount_doller_accessories=$row['ORDER_AMOUNT_ACCESSORIES']/$conversion_rate;
                            }
                            else 
                            {
                                $mrr_amount_doller=$row['ORDER_AMOUNT'];
                                $mrr_amount_doller_accessories=$row['ORDER_AMOUNT_ACCESSORIES'];
                            }	
                            if ($row['ENTRY_FORM'] == 24) 
                            {
                                $tot_mrr_amount_doller_accessories+=$mrr_amount_doller_accessories;
                                $html2 .= number_format($mrr_amount_doller_accessories,2);
                            }
                            else 
                            {
                                $tot_mrr_amount_doller+=$mrr_amount_doller;
                                $html2 .= number_format($mrr_amount_doller,2);
                            }
                            $html2 .= '</p></td>
                            <td valign="middle" width="100" align="right"><p>';
                                if ($row['CURRENCY_ID'] == 1)
                                {
                                    $mrr_amount_tk=$mrr_amount_doller*$conversion_rate;
                                    $mrr_amount_accessories_tk=$mrr_amount_doller_accessories*$conversion_rate;
                                } 
                                else
                                {
                                    $mrr_amount_tk=$row['CONS_AMOUNT'];
                                    $mrr_amount_accessories_tk=$row['CONS_AMOUNT_ACCESSORIES'];
                                }
                                if ($row['ENTRY_FORM'] == 24)
                                {
                                    $tot_mrr_amount_accessories_tk+=$mrr_amount_accessories_tk;
                                    $html2 .= number_format($mrr_amount_accessories_tk,2);
                                }
                                else
                                {
                                    $tot_mrr_amount_tk+=$mrr_amount_tk;
                                    $html2 .= number_format($mrr_amount_tk,2);
                                }
                                $html2 .= '</p></td>';
                            if($booking_entry_form[$row['BOOKING_ID']][$row['ITEM_CATEGORY']]['BOOKING_ENTRY_FORM']==271)
                            {
                                $html2 .= '<td valign="middle" width="180" align="right"><p>';
                                if ($row['RECEIVE_BASIS'] == 1) {
                                    $html2 .= rtrim($work_order_no_arr[$row['BOOKING_ID']]['WORK_ORDER_NO'],',');
                                } else if ($row['RECEIVE_BASIS'] == 5) {
                                    $batch_booking_names = ''; $batch_id_arr = array();
                                    $batch_id_arr= array_unique(explode(',', $row['PI_WO_BATCH_ID']));
                                    foreach($batch_id_arr as $batch_id){
                                        $batch_booking_names .= $batch_booking_arr[$batch_id].',';
                                    }
                                    $html2 .= rtrim($batch_booking_names, ',');
                                }
                                else{
                                    $html2 .= $row['BOOKING_NO'] === '0' ? '' : $row['BOOKING_NO'];
                                }
                                $html2 .= '</p></td>';
                            }
                            elseif($booking_arr[$row["BOOKING_ID"]]["BOOKING_ENTRY_FORM"]== 108)
                            {
                                $html2 .= '<td valign="middle" width="180" align="center"><p>';
                                    if ($row['RECEIVE_BASIS'] == 1) {
                                        $html2 .= rtrim($work_order_no_arr[$row['BOOKING_ID']]['WORK_ORDER_NO'],',');
                                    } else if ($row['RECEIVE_BASIS'] == 5) {
                                        $batch_booking_names = ''; $batch_id_arr = array();
                                        $batch_id_arr= array_unique(explode(',', $row['PI_WO_BATCH_ID']));
                                        foreach($batch_id_arr as $batch_id){
                                            $batch_booking_names .= $batch_booking_arr[$batch_id].',';
                                        }
                                        $html2 .= rtrim($batch_booking_names, ',');
                                    }
                                    else{
                                        $html2 .= $row['BOOKING_NO'] === '0' ? '' : $row['BOOKING_NO'];
                                    }
                                $html2 .= '</p></td>';
                            }                               
                            elseif($booking_entry_form[$row['BOOKING_ID']][$row['ITEM_CATEGORY']]['DYS_CAMICAL_ENTRY_FORM']==145)
                            {
                                $html2 .= '<td valign="middle" width="180" align="center"><p>';
                                    if ($row['RECEIVE_BASIS'] == 1) {
                                        $html2 .= rtrim($work_order_no_arr[$row['BOOKING_ID']]['WORK_ORDER_NO'],',');
                                    } else if ($row['RECEIVE_BASIS'] == 5) {
                                        $batch_booking_names = ''; $batch_id_arr = array();
                                        $batch_id_arr= array_unique(explode(',', $row['PI_WO_BATCH_ID']));
                                        foreach($batch_id_arr as $batch_id){
                                            $batch_booking_names .= $batch_booking_arr[$batch_id].',';
                                        }
                                        $html2 .= rtrim($batch_booking_names, ',');
                                    }
                                    else{
                                        $html2 .= $row['BOOKING_NO'] === '0' ? '' : $row['BOOKING_NO'];
                                    }
                                $html2 .= '</p></td>';
                            } 
                            elseif($booking_entry_form[$row['BOOKING_ID']][$row['ITEM_CATEGORY']]['DYS_CAMICAL_ENTRY_FORM']==145)
                            {
                                $html2 .= '<td valign="middle" width="180" align="center"><p>';
                                    if ($row['RECEIVE_BASIS'] == 1) {
                                        $html2 .= rtrim($work_order_no_arr[$row['BOOKING_ID']]['WORK_ORDER_NO'],',');
                                    } else if ($row['RECEIVE_BASIS'] == 5) {
                                        $batch_booking_names = ''; $batch_id_arr = array();
                                        $batch_id_arr= array_unique(explode(',', $row['PI_WO_BATCH_ID']));
                                        foreach($batch_id_arr as $batch_id){
                                            $batch_booking_names .= $batch_booking_arr[$batch_id].',';
                                        }
                                        $html2 .= rtrim($batch_booking_names, ',');
                                    }
                                    else{
                                        $html2 .= $row['BOOKING_NO'] === '0' ? '' : $row['BOOKING_NO'];
                                    }
                                $html2 .= '</p></td>';
                            } 
                            elseif($booking_entry_form[$row['BOOKING_ID']][$row['ITEM_CATEGORY']]['DYS_CAMICAL_ENTRY_FORM']==144)
                            {
                                $html2 .= '<td valign="middle" width="180" align="center"><p>';
                                    if ($row['RECEIVE_BASIS'] == 1) {
                                        $html2 .= rtrim($work_order_no_arr[$row['BOOKING_ID']]['WORK_ORDER_NO'],',');
                                    } else if ($row['RECEIVE_BASIS'] == 5) {
                                        $batch_booking_names = ''; $batch_id_arr = array();
                                        $batch_id_arr= array_unique(explode(',', $row['PI_WO_BATCH_ID']));
                                        foreach($batch_id_arr as $batch_id){
                                            $batch_booking_names .= $batch_booking_arr[$batch_id].',';
                                        }
                                        $html2 .= rtrim($batch_booking_names, ',');
                                    }
                                    else{
                                        $html2 .= $row['BOOKING_NO'] === '0' ? '' : $row['BOOKING_NO'];
                                    }
                                $html2 .= '</p></td>';
                            }
                            elseif($stationary_purchase[$row["BOOKING_ID"]][$row["BOOKING_NO"]]["ENTRY_FORM"]==146)
                            {
                                $html2 .= '<td valign="middle" width="180" align="center"><p>';
                                    if ($row['RECEIVE_BASIS'] == 1) {
                                        $html2 .= rtrim($work_order_no_arr[$row['BOOKING_ID']]['WORK_ORDER_NO'],',');
                                    } else if ($row['RECEIVE_BASIS'] == 5) {
                                        $batch_booking_names = ''; $batch_id_arr = array();
                                        $batch_id_arr= array_unique(explode(',', $row['PI_WO_BATCH_ID']));
                                        foreach($batch_id_arr as $batch_id){
                                            $batch_booking_names .= $batch_booking_arr[$batch_id].',';
                                        }
                                        $html2 .= rtrim($batch_booking_names, ',');
                                    }
                                    else{
                                        $html2 .= $row['BOOKING_NO'] === '0' ? '' : $row['BOOKING_NO'];
                                    }
                                $html2 .= '</p></td>';
                            }
                            elseif($stationary_purchase[$row["BOOKING_ID"]][$row["BOOKING_NO"]]["ENTRY_FORM"]==147)
                            {
                                $html2 .= '<td valign="middle" width="180" align="center"><p>';
                                    if ($row['RECEIVE_BASIS'] == 1) {
                                        $html2 .= rtrim($work_order_no_arr[$row['BOOKING_ID']]['WORK_ORDER_NO'],',');
                                    } else if ($row['RECEIVE_BASIS'] == 5) {
                                        $batch_booking_names = ''; $batch_id_arr = array();
                                        $batch_id_arr= array_unique(explode(',', $row['PI_WO_BATCH_ID']));
                                        foreach($batch_id_arr as $batch_id){
                                            $batch_booking_names .= $batch_booking_arr[$batch_id].',';
                                        }
                                        $html2 .= rtrim($batch_booking_names, ',');
                                    }
                                    else{
                                        $html2 .= $row['BOOKING_NO'] === '0' ? '' : $row['BOOKING_NO'];
                                    }
                                $html2 .= '</p></td>';
                            }
                            elseif($req_arr[$row['BOOKING_ID']]['ENTRY_FORM']==69)
                            {
                                $html2 .= '<td valign="middle" width="180" align="center"><p>';
                                    if ($row['RECEIVE_BASIS'] == 1) {
                                        $html2 .= rtrim($work_order_no_arr[$row['BOOKING_ID']]['WORK_ORDER_NO'],',');
                                    } else if ($row['RECEIVE_BASIS'] == 5) {
                                        $batch_booking_names = ''; $batch_id_arr = array();
                                        $batch_id_arr= array_unique(explode(',', $row['PI_WO_BATCH_ID']));
                                        foreach($batch_id_arr as $batch_id){
                                            $batch_booking_names .= $batch_booking_arr[$batch_id].',';
                                        }
                                        $html2 .= rtrim($batch_booking_names, ',');
                                    }
                                    else{
                                        $html2 .= $row['BOOKING_NO'] === '0' ? '' : $row['BOOKING_NO'];
                                    }
                                $html2 .= '</p></td>';
                            }
                            else if($booking_arr[$row['BOOKING_ID']]['BOOKING_ENTRY_FORM']==87)
                            {
                                $html2 .= '<td valign="middle" width="180" align="center"><p>';
                                    if ($row['RECEIVE_BASIS'] == 1) {
                                        $html2 .= rtrim($work_order_no_arr[$row['BOOKING_ID']]['WORK_ORDER_NO'],',');
                                    } else if ($row['RECEIVE_BASIS'] == 5) {
                                        $batch_booking_names = ''; $batch_id_arr = array();
                                        $batch_id_arr= array_unique(explode(',', $row['PI_WO_BATCH_ID']));
                                        foreach($batch_id_arr as $batch_id){
                                            $batch_booking_names .= $batch_booking_arr[$batch_id].',';
                                        }
                                        $html2 .= rtrim($batch_booking_names, ',');
                                    }
                                    else{
                                        $html2 .= $row['BOOKING_NO'] === '0' ? '' : $row['BOOKING_NO'];
                                    }
                                $html2 .= '</p></td>';
                            }
                            else if($sales_wo_arr[$row['BOOKING_ID']]['ENTRY_FORM']==109)
                            {
                                $html2 .= '<td valign="middle" width="180" align="center"><p>';
                                    if ($row['RECEIVE_BASIS'] == 1) {
                                        $html2 .= rtrim($work_order_no_arr[$row['BOOKING_ID']]['WORK_ORDER_NO'],',');
                                    } else if ($row['RECEIVE_BASIS'] == 5) {
                                        $batch_booking_names = ''; $batch_id_arr = array();
                                        $batch_id_arr= array_unique(explode(',', $row['PI_WO_BATCH_ID']));
                                        foreach($batch_id_arr as $batch_id){
                                            $batch_booking_names .= $batch_booking_arr[$batch_id].',';
                                        }
                                        $html2 .= rtrim($batch_booking_names, ',');
                                    }
                                    else{
                                        $html2 .= $row['BOOKING_NO'] === '0' ? '' : $row['BOOKING_NO'];
                                    }
                                $html2 .= '</p></td>';
                            }                              
                            else 
                            {
                                $html2 .= '<td valign="middle" width="180" align="center"><p>';
                                    if ($row['RECEIVE_BASIS'] == 1) {
                                        $html2 .= rtrim($work_order_no_arr[$row['BOOKING_ID']]['WORK_ORDER_NO'],',');
                                    } else if ($row['RECEIVE_BASIS'] == 5) {
                                        $batch_booking_names = ''; $batch_id_arr = array();
                                        $batch_id_arr= array_unique(explode(',', $row['PI_WO_BATCH_ID']));
                                        foreach($batch_id_arr as $batch_id){
                                            $batch_booking_names .= $batch_booking_arr[$batch_id].',';
                                        }
                                        $html2 .= rtrim($batch_booking_names, ',');
                                    }
                                    else{
                                        $html2 .= $row['BOOKING_NO'] === '0' ? '' : $row['BOOKING_NO'];
                                    }
                                $html2 .= '</p></td>';
                            }
                            $html2 .= '<td valign="middle" width="100" style="word-break:break-all;"><p>'.$supplier.'</p></td>
                            <td valign="middle" width="80" style="word-break:break-all;"><p>';
                                if($row['RECEIVE_BASIS'] == 1){
                                    $html2 .= $row['BOOKING_NO'];
                                }elseif($row['RECEIVE_BASIS']==2){
                                    $html2 .= $fterGoodRec_PiNo_arr[$row['BOOKING_ID']][$row['ITEM_CATEGORY']]['PI_NUMBER'];
                                    //$html2 .= $fterGoodRec_PiNo_arr[$row['BOOKING_ID']]['PI_NUMBER'];
                                }
                                $html2 .= '</p></td>
                            <td valign="middle" width="80" style="word-break:break-all;"><p>';
                                if ($row['RECEIVE_BASIS'] == 1) {
                                    $html2 .= $lc_number_arr[$row['BOOKING_ID']][$row['ITEM_CATEGORY']]['LC_NUMBER'];
                                } else  {
                                    $html2 .= $fterGoodRec_lc_number_arr[$row['BOOKING_ID']][$row['ITEM_CATEGORY']]['LC_NUMBER'];
                                }
                                $html2 .= '</p></td>
                            <td valign="middle" width="80" align="center" style="word-break:break-all;"><p>'.$user_id_arr[$row['AUDIT_BY']].'</p></td>
                            <td valign="middle" width="80" style="word-break:break-all;"><p>'.$user_name[$row['AUDIT_BY']].'</p></td>
                            <td valign="middle" width="80" align="center" style="word-break:break-all;">'.change_date_format($row['AUDIT_DATE']).'&nbsp;</td>
                            <td valign="middle" width="80" align="center" style="word-break:break-all;">'.$bill_arr[$row['BOOKING_ID']]['BILL_NO'].'</a> &nbsp;</td>
                            <td valign="middle" width="80" align="center" style="word-break:break-all;">'.$bill_arr[$row['BOOKING_ID']]['BILL_DATE'].'&nbsp;</td>
                            <td valign="middle" width="">'.$row['AUDIT_REMARK'].'</td>
                        </tr>';
							$i++;
						} 
                        $html2 .= '</tbody></table></div>';
						?>
					</tbody>
				</table>
			</div>
            <?
            $html2 .= '<table cellpadding="0" cellspacing="0" rules="all" border="1" width="2050" class="rpt_table" id="report_table_footer" align="left">
                <tfoot>
                    <tr>
                        <th width="30"><p>&nbsp;</p></th>
                        <th width="30"><p>&nbsp;</p></th>
                        <th width="80"><p>&nbsp;</p></th>
                        <th width="100"><p>&nbsp;</p></th>
                        <th width="80"><p>&nbsp;</p></th>
                        <th width="80"><p>&nbsp;</p></th>
                        <th width="50"><p>&nbsp;</p></th>
                        <th width="100"><p>&nbsp;</p></th>
                        <th width="60"><p>&nbsp;</p></th>
                        <th width="50"><p>&nbsp;</p></th>
                        <th width="50"><p>&nbsp;</p></th>
                        <th width="100"><p>'.number_format($tot_mrr_amount_doller_accessories+$tot_mrr_amount_doller,2).'</p></th>
		                <th width="100"><p>'.number_format($tot_mrr_amount_accessories_tk+$tot_mrr_amount_tk,2).'</p></th>
                        <th width="180"><p>&nbsp;</p></th>
		                <th width="100"><p>&nbsp;</p></th>
		                <th width="80"><p>&nbsp;</p></th>
		                <th width="80"><p>&nbsp;</p></th>
		                <th width="80"><p>&nbsp;</p></th>
		                <th width="80"><p>&nbsp;</p></th>
		                <th width="80"><p>&nbsp;</p></th>
		                <th width="80"><p>&nbsp;</p></th>
		                <th width="80"><p>&nbsp;</p></th>
		                <th ><p>&nbsp;</p></th>
                    </tr>
                </tfoot></table></fieldset></form>';
            ?>
			<table cellpadding="0" cellspacing="0" rules="all" border="1" width="<? echo $tableWidth ; ?>" class="rpt_table" id="report_table_footer" align="left">
				<tfoot>
					<tr>
						<th width="30"><p>&nbsp;</p></th>
						<th width="30"><p>&nbsp;</p></th>
		                <th width="80"><p>&nbsp;</p></th>
						<th width="100"><p>&nbsp;</p></th>
		                <th width="80"><p>&nbsp;</p></th>
		                <th width="80"><p>&nbsp;</p></th>
		                <th width="150"><p>&nbsp;</p></th>
		                <th width="100"><p>&nbsp;</p></th>
		                <th width="60"><p>&nbsp;</p></th>
		                <th width="50"><p>&nbsp;</p></th>
		                <th width="50"><p>Total:</p></th>
		                <th width="100" id="value_mrr_total_dlr_qnty"><p><? echo number_format($tot_mrr_amount_doller_accessories+$tot_mrr_amount_doller,2); ?></p></th>
		                <th width="100" id="value_mrr_total_bdt_qnty"><p><? echo number_format($tot_mrr_amount_accessories_tk+$tot_mrr_amount_tk,2); ?></p></th>
		                <th width="180"><p>&nbsp;</p></th>
		                <th width="100"><p>&nbsp;</p></th>
		                <th width="80"><p>&nbsp;</p></th>
		                <th width="80"><p>&nbsp;</p></th>
		                <th width="80"><p>&nbsp;</p></th>
		                <th width="100"><p>&nbsp;</p></th>
		                <th width="80"><p>&nbsp;</p></th>
		                <th width="100"><p>&nbsp;</p></th>
		                <th width="80"><p>&nbsp;</p></th>
		                <th ><p>&nbsp;</p></th>	        		
					</tr>
				</tfoot>
			</table>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tableWidth ; ?>" class="rpt_table" id="footer_line">
				<tfoot>
					<tr>
						<td width="30" align="center" ><input type="checkbox" id="all_check" onclick="check_all()" /></td>
						<td colspan="25" align="left">
						<input type="button" value="<? if($cbo_audit_type==1) echo "Un-Audited"; else echo "Audited"; ?>" class="formbutton" style="width:100px" onclick="fn_audited_un_audited()"/>
						</td> 
					</tr>
				</tfoot>
			</table>
		</fieldset>
	</form>
	<?
    $r_id6=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form=140");
	oci_commit($con);
	disconnect($con);
    $html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');    
    $is_created = fwrite($create_new_doc, $html2);
    echo "$html####$filename";
    exit();
}


if($action=="save_update_delete")
{	
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));	

	$cbo_audit_type		= str_replace("'","",$cbo_audit_type); 
	$checkedIds			= trim($data_all);

	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
     
    $sql_priviledge="SELECT b.show_priv as VISIBILITY, b.save_priv as INSERT_PER, b.edit_priv as EDIT_PER from user_priv_mst b where b.user_id=$user_id and b.main_menu_id=$menu_id and b.valid=1";
    $sql_priviledge_res=sql_select($sql_priviledge);

    // Audit Un-audit Permission
	if($cbo_audit_type==0) 
	{
		$audit_type = 1;
		if ($sql_priviledge_res[0]['INSERT_PER']!=1)
		{
			echo "50**You do not have permission for this button!!";
			disconnect($con);die;
		}	
	}
	else 
	{
		$audit_type = 0;
		if ($sql_priviledge_res[0]['EDIT_PER']!=1)
		{
			echo "50**You do not have permission for this button!!";
			disconnect($con);die;
		}
	}	
	
	$rID1 = $rID2 = 1;
	
	$field_array = "is_audited*audit_remark*audit_by*audit_date";

	$checkedIdArr = explode("__",$checkedIds);
	foreach($checkedIdArr as $row)
	{		
		$audit_data_arr = explode("**", $row );	
		
        $entry_form = $audit_data_arr[2];

        if($entry_form == 558)
        {
            $updated_id_arr1[] = $audit_data_arr[0];
            $data_array1[$audit_data_arr[0]] = explode("*",("".$audit_type."*'".$audit_data_arr[1]."'*". $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'"));
        }
        else
        {
            $updated_id_arr[] = $audit_data_arr[0];
            $data_array[$audit_data_arr[0]] = explode("*",("".$audit_type."*'".$audit_data_arr[1]."'*". $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'"));
        }
	}
    // echo "<pre>"; print_r($data_array); die;
    $rID1 = $rID2 = true;
    if(count($data_array1)>0)
    {
        $rID2 = execute_query(bulk_update_sql_statement("wo_service_acknowledgement_mst", "id", $field_array, $data_array1, $updated_id_arr1, 0), 1);
    }
    if(count($data_array)>0)
    {
        $rID1 = execute_query(bulk_update_sql_statement("inv_receive_master", "id", $field_array, $data_array, $updated_id_arr, 0), 1);
    }
	
	if($db_type==0)
	{
		if($rID1 && $rID2)
		{
			mysql_query("COMMIT");  
			echo "0**".$cbo_audit_type;  
		}
		else
		{
			mysql_query("ROLLBACK"); 
			echo "10**".$cbo_audit_type;
		}
	}
	
	if($db_type==2 )
	{		
		if($rID1 && $rID2)
		{
			oci_commit($con);
			echo "0**".$cbo_audit_type; 
		}
		else
		{
			oci_rollback($con);
			echo "10**".$cbo_audit_type;
		}
	}	
	disconnect($con);	
	exit();
}

?>