<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$choosenCompany= $choosenCompany;

if ($action=="load_drop_down_company_location")
{
    extract($_REQUEST);
    //ITEM_CATEGORY
	echo create_drop_down( "cbo_location_name", 232, "select id,location_name from lib_location where company_id in ($choosenCompany) and status_active =1 and is_deleted=0 order by location_name","id,location_name", 0, "", '', '' ) .'**'. create_drop_down( "cbo_store_name", 232, "select id,store_name from lib_store_location where company_id in ($choosenCompany) and status_active=1 and is_deleted=0 order by store_name","id,store_name", 0, "", '', '');
    $inf=sql_select( "select * from user_passwd where id='"..$update_id.."' and valid=1" );
	echo "**".($inf[0][csf("company_location_id")])."*".($inf[0][csf("store_location_id")]);
	exit();
}

if($action=="show_list_view")
{
	 $custom_designation=return_library_array( "select id,custom_designation from lib_designation ",'id','custom_designation');
	 $Department=return_library_array( "select id,department_name from  lib_department ",'id','department_name');	;
	 $arr=array (2=>$custom_designation,3=>$Department);
	 echo  create_list_view ( "list_view", "User ID,Full Name,Designation,Department", "100,120,150,150,","630","220",0, "select id,user_name,department_id,user_full_name,designation from  user_passwd where VALID=1", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,department_id,designation,department_id", $arr , "user_name,user_full_name,designation,department_id", "requires/user_credentials_controller", 'setFilterGrid("list_view",-1);' ) ;
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if ($operation==1)   // Update Here
	{
        $con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		if(str_replace("'","",$update_id)>0)
		{
            $field_array="unit_id*company_location_id*store_location_id*buyer_id*supplier_id*item_cate_id*graph_id*created_on*created_by";
		    $data_array="".$cbo_company_name."*".$cbo_location_name."*".$cbo_store_name."*".$cbo_user_buyer."*".$cbo_user_supplier."*".$cbo_item_category."*".$cbo_graph_id."*'".$pc_date."'*'".$_SESSION['logic_erp']["user_id"]."'";
            $rID=sql_update("user_passwd",$field_array,$data_array,"id","".$update_id."",1);
		}
		else
		{
			echo "5**"; die;
		}

		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");
				echo "1**".$rID;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$rID;
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);
				echo "1**".$rID;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$rID;
			}
		}

		disconnect($con);
		die;
	}
	else if ($operation==2)   // Delete Here
	{
		$con = connect();

		$field_array="valid";
		$data_array="'0'";
		$rID=sql_update("user_passwd",$field_array,$data_array,"id","".$update_id."",1);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");
				echo "2****".$rID;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$rID;
			}
		}

		if($db_type==2 || $db_type==1 )
		{
			if($rID )
			{
				oci_commit($con);
				echo "2****".$rID;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$rID;
			}
		}
		disconnect($con);
		die;
	}
}


if ($action=="load_php_data_to_form")
{
    $buyer_array=return_library_array( "Select id,buyer_name from lib_buyer", "id", "buyer_name"  );
    $supplier_array=return_library_array( "Select id,supplier_name from lib_supplier", "id", "supplier_name");

    $nameArray=sql_select( "select * from user_passwd where id='$data' and valid=1" );

	foreach ($nameArray as $inf)
	{
		$bu=explode(",",$inf[csf("buyer_id")]);

		foreach($bu as $key)
		{
			if($buyer=="") $buyer=$buyer_array[$key]; else $buyer .=",".$buyer_array[$key];
		}

        $su=explode(",",$inf[csf("supplier_id")]);

		foreach($su as $key)
		{
			if($supplier=="") $supplier=$supplier_array[$key]; else $supplier .=",".$supplier_array[$key];
		}

		echo "document.getElementById('cbo_user_name_show').value = '".trim(($inf[csf("user_name")]))."';\n";
        echo "document.getElementById('cbo_user_buyer_show').value  = '".$buyer."';\n";
        echo "document.getElementById('cbo_user_buyer').value  = '".($inf[csf("buyer_id")])."';\n";
        echo "document.getElementById('cbo_user_supplier_show').value  = '".$supplier."';\n";
        echo "document.getElementById('cbo_user_supplier').value  = '".($inf[csf("supplier_id")])."';\n";
		echo "set_multiselect('cbo_company_name*cbo_location_name*cbo_store_name*cbo_item_category','0*0*0*0','1','".($inf[csf("unit_id")])."*".($inf[csf("company_location_id")])."*".($inf[csf("store_location_id")])."*".($inf[csf("item_cate_id")])."','0*0*0*0');\n";
		echo "document.getElementById('cbo_graph_id').value = '".trim(($inf[csf("graph_id")]))."';\n";
        echo "document.getElementById('update_id').value  = '".($inf[csf("id")])."';\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_user_creation',1);\n";
    }
}


// Buyer
if ($action=="buyer_selection_popup")
{
	echo load_html_head_contents("Buyer Selection Form","../../", $filter, '', $unicode);
	?>
    <script>
        var selected_id = new Array();
        var selected_name = new Array();

		function check_all_data(str) {
			tbl_row_count=str.split(',');
			for( var i = 0; i <= tbl_row_count.length; i++ ) {
				js_set_value( tbl_row_count[i] );
			}
		}

		function toggle(x,origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value( str ) {
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
			var id ='';
			var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
			}
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );

			$('#txt_selected_id').val( id );
			$('#txt_selected').val( name );
		}
    </script>
    <input type="hidden" name="txt_selected_id" id="txt_selected_id" value="" />
    <input type="hidden" name="txt_selected"  id="txt_selected" width="650px" value="" />

    <div>
        <div style="width:625px;" align="left">
            <table cellspacing="0" cellpadding="0" width="100%" class="rpt_table" >
                    <thead>
                        <th width="50" align="left">SL No</th>
                        <th width="130" align="left">Buyer Name</th>
                        <th width="120" align="left">Sub Con. Party</th>
                        <th width="100" align="left">Contact Person</th>
                        <th width="80" align="center">Contact NO.</th>
                        <th align="center">Email</th>
                    </thead>
            </table>
        </div>

        <div style="width:625px; overflow-y:scroll; min-height:50px; max-height:250px;" id="buyer_list_view" align="left">
            <table  cellspacing="0" cellpadding="0" width="100%" class="rpt_table" id="tbl_list_search" >
            <?php
$i = 1;
$nameArray = sql_select("select * from lib_buyer where is_deleted=0 and status_active=1  order by buyer_name");
foreach ($nameArray as $selectResult) {
	$id_arr[] = $selectResult[csf('id')];
	if ($i % 2 == 0) {
		$bgcolor = "#E9F3FF";
	} else {
		$bgcolor = "#FFFFFF";
	}

	?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $selectResult[csf('id')];?>" onclick="js_set_value(<? echo $selectResult[csf('id')];?>)">
                        <td width="50" align="center"><?php echo "$i"; ?>
                            <input type="hidden" name="txt_individual" id="txt_individual<? echo $selectResult[csf('id')];?>" value="<?php echo $selectResult[csf('buyer_name')]; ?>"/>
                            <input type="hidden" name="txt_individual_id" id="txt_individual_id<? echo $selectResult[csf('id')];?>" value="<?php echo $selectResult[csf('id')]; ?>"/>
                        </td>
                        <td width="130">&nbsp;
                            <?php
echo split_string($selectResult[csf('buyer_name')], 13);
	?>
                        </td>
                        <td width="120">
                            &nbsp;
                            <?php
$frm = $replacement_lc[$selectResult[csf(subcontract_party)]];
	echo $frm;
	?>
                        </td>
                        <td width="100">&nbsp; <?php echo split_string($selectResult[csf(contact_person)], 10); ?></td>
                        <td width="80">&nbsp; <?php echo split_string($selectResult[csf(contact_no)], 11); ?></td>
                        <td>&nbsp;<?php echo split_string($selectResult[csf(buyer_email)], 10); ?></td>
                    </tr>
                    <?php
$i++;
}
?>
            </table>
        </div>

        <div style="width:625px;" align="left">
            <table width="100%">
                <tr>
                    <td align="center" colspan="6" height="30" valign="bottom">
                        <div style="width:100%">
                                <div style="width:50%; float:left" align="left">
                                    <input type="checkbox" name="check_all" id="check_all" onclick="check_all_data('<? echo implode(',',$id_arr);?>')" /> Check / Uncheck All
                                </div>
                                <div style="width:50%; float:left" align="left">
                                <input type="button" name="close" onclick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                                </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <script>
        var buyer_data='<? echo $data;?>';
        buyer_arr=buyer_data.split(',');
        for(var i=0;i<=buyer_arr.length;i++)
        {
            js_set_value( buyer_arr[i] );
        }
    </script>
    <?
}

// Supplier
if ($action=="supplier_selection_popup")
{
	echo load_html_head_contents("Supplier Selection Form","../../", 1,1,$unicode,1,'');
	?>
    <script>
        var selected_id = new Array();
        var selected_name = new Array();

		function check_all_data(str) {
			tbl_row_count=str.split(',');
			for( var i = 0; i <= tbl_row_count.length; i++ ) {
				js_set_value( tbl_row_count[i] );
			}
		}

		function toggle( x='', origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value( str ) {


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
			var id ='';
			var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
			}
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );

			$('#txt_selected_id').val( id );
			$('#txt_selected').val( name );
		}
        setFilterGrid("tbl_sup_list_search",-1)
    </script>
    <input type="hidden" name="txt_selected_id" id="txt_selected_id" value="" />
    <input type="hidden" name="txt_selected"  id="txt_selected" width="650px" value="" />

    <div>
        <div style="width:625px;" align="left">
            <table cellspacing="0" cellpadding="0" width="100%" class="rpt_table" >
                    <thead>
                        <th width="50" align="left">SL No</th>
                        <th width="130" align="left">Supplier Name</th>
                        <th width="100" align="left">Contact Person</th>
                        <th width="80" align="center">Contact NO.</th>
                        <th align="center">Email</th>
                    </thead>
            </table>
        </div>

        <div style="width:625px; overflow-y:scroll; min-height:50px; max-height:250px;" id="buyer_list_view" align="left">
            <table  cellspacing="0" cellpadding="0" width="100%" class="rpt_table" id="tbl_sup_list_search" >
            <?php
$i = 1;
$nameArray = sql_select("select id,supplier_name,contact_person,email,contact_no from lib_supplier where is_deleted=0 and status_active=1  order by supplier_name");

foreach ($nameArray as $selectResult) {
	$id_arr[] = $selectResult[csf('id')];
	if ($i % 2 == 0) {
		$bgcolor = "#E9F3FF";
	} else {
		$bgcolor = "#FFFFFF";
	}

	?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $selectResult[csf('id')];?>" onclick="js_set_value(<? echo $selectResult[csf('id')];?>)">
                        <td width="50" align="center"><?php echo "$i"; ?>
                            <input type="hidden" name="txt_individual" id="txt_individual<? echo $selectResult[csf('id')];?>" value="<?php echo $selectResult[csf('supplier_name')]; ?>"/>
                            <input type="hidden" name="txt_individual_id" id="txt_individual_id<? echo $selectResult[csf('id')];?>" value="<?php echo $selectResult[csf('id')]; ?>"/>
                        </td>
                        <td width="130">&nbsp;<?php echo split_string($selectResult[csf('supplier_name')], 13); ?></td>
                        <td width="100">&nbsp; <?php echo split_string($selectResult[csf('contact_person')], 10); ?></td>
                        <td width="80">&nbsp; <?php echo split_string($selectResult[csf('contact_no')], 11); ?></td>
                        <td>&nbsp;<?php echo split_string($selectResult[csf('email')], 10); ?></td>

                    </tr>
                    <?php
$i++;
}
?>
            </table>
        </div>

        <div style="width:625px;" align="left">
            <table width="100%">
                <tr>
                    <td align="center" colspan="6" height="30" valign="bottom">
                        <div style="width:100%">
                                <div style="width:50%; float:left" align="left">
                                    <input type="checkbox" name="check_all" id="check_all" onclick="check_all_data('<? echo implode(',',$id_arr);?>')" /> Check / Uncheck All
                                </div>
                                <div style="width:50%; float:left" align="left">
                                <input type="button" name="close" onclick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                                </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <script>
        var supplier_data='<? echo $data;?>';
        supplier_arr=supplier_data.split(',');
        for(var i=0;i<=supplier_arr.length;i++)
        {
            js_set_value( supplier_arr[i] );
        }

    </script>
    <?
}

// user
if ($action=="user_selection_popup")
{
    echo load_html_head_contents("User Selection Form","../../",1,1,$unicode,1,'');
    ?>
    <input type="hidden" name="txt_selected_id" id="txt_selected_id" value="" />
    <input type="hidden" name="txt_selected"  id="txt_selected" width="330px" value="" />
    <div>
        <div style="width:300px;" align="left">
            <table cellspacing="0" cellpadding="0" width="100%" class="rpt_table" >
                <thead>
                    <th width="48" align="left">SL No</th>
                    <th width="128" align="left">User Name</th>
                </thead>
            </table>
        </div>
        <div style="width:300px; overflow-y:scroll; min-height:50px; max-height:250px;" id="buyer_list_view" align="left">
            <table  cellspacing="0" cellpadding="0" width="100%" class="rpt_table" id="tbl_list_search" >
                <?php
$i = 1;
$nameArray = sql_select("select * from user_passwd where valid=1");
foreach ($nameArray as $selectResult) {
	if ($i % 2 == 0) {
		$bgcolor = "#E9F3FF";
	} else {
		$bgcolor = "#FFFFFF";
	}

	if (in_array($selectResult[csf('id')], $cu)) {
		$bgcolor = "#FFFF00";
	}

	?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?php echo $selectResult[csf('id')]; ?>" onclick="js_set_value(<?php echo $selectResult[csf('id')]; ?>)">
                        <td width="50" align="center"><?php echo "$i"; ?>
                            <input type="hidden" name="txt_individual" id="txt_individual<?php echo $selectResult[csf('id')]; ?>" value="<?php echo $selectResult[csf('user_name')]; ?>"/>
                            <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $selectResult[csf('id')]; ?>" value="<?php echo $selectResult[csf('id')]; ?>"/>
                        </td>
                        <td width="130">&nbsp;
                            <?php echo split_string($selectResult[csf('user_name')], 13); ?>
                        </td>
                    </tr>
                    <?php
$i++;
}
?>
            </table>
        </div>
    </div>


<script>
    function js_set_value( str ) {
        selected_id = $('#txt_individual_id' + str).val();
        selected_name = $('#txt_individual' + str).val();
        $('#txt_selected_id').val( selected_id);
		$('#txt_selected').val( selected_name );
        parent.emailwindow.hide();
    }
setFilterGrid("tbl_list_search",-1)
</script>

<?
}
?>