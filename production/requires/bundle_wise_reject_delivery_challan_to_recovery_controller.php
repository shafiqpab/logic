<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
//------------------------------------------------------------------------------------------------------
$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name" );
$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
$floor_arr=return_library_array( "select id,floor_name from lib_prod_floor",'id','floor_name');
$location_arr=return_library_array( "select id,location_name from lib_location",'id','location_name');
$supplier_arr=return_library_array( "select id, supplier_name from  lib_supplier",'id','supplier_name');

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 180, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "",0 );
	exit();
}

if ($action=="load_drop_down_working_location")
{
	echo create_drop_down( "cbo_working_location", 180, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "",0 );
	exit();
}

if ($action == "load_drop_down_line") 
{

    list($company_id, $location, $floor,$issue_date) = explode("_", $data);

    $nameArray = sql_select("select id, auto_update from variable_settings_production where company_name='$company_id' and variable_list=23 and status_active=1 and is_deleted=0");

    $prod_reso_allocation = $nameArray[0][csf('auto_update')];
    $cond = "";

    if ($prod_reso_allocation == 1) 
    {
        $line_library = return_library_array("SELECT id,line_name,sewing_line_serial from lib_sewing_line where status_active=1 order by sewing_line_serial", "id", "line_name");
        $line_array = array();

        if ($floor == 0 && $location != 0) $cond = " and a.location_id= $location";
        if ($floor != 0) $cond = " and a.floor_id= $floor";


        if($issue_date!="")
        {
	        if($db_type==0)
	        {
	            $issue_date = date("Y-m-d",strtotime($issue_date));
	        }
	        else
	        {
	            $issue_date = change_date_format(date("Y-m-d",strtotime($issue_date)),'','',1);
	        }

	        $cond.=" and b.pr_date='".$issue_date."'";
	    }


        if ($db_type == 0) 
        {
            $line_data = sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number,a.prod_resource_num  order by a.line_number asc, a.prod_resource_num asc, a.id asc");
        } 
        else if ($db_type == 2 || $db_type == 1) 
        {
            $line_data = sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 and a.company_id=$company_id $cond group by a.id, a.line_number,a.prod_resource_num  order by a.line_number asc, a.prod_resource_num asc, a.id asc");
        }

        $line_merge=9999;
        foreach($line_data as $row)
        {
            $line='';
            $line_number=explode(",",$row[csf('line_number')]);
            foreach($line_number as $val)
            {
                if(count($line_number)>1)
                {
                    $line_merge++;
                    $new_arr[$line_merge]=$row[csf('id')];
                }
                else
                {
                    if($new_arr[$line_library[$val]])
                    $new_arr[$line_library[$val]." "]=$row[csf('id')];
                    else
                        $new_arr[$line_library[$val]]=$row[csf('id')];
                }

                if($line=='') $line=$line_library[$val]; else $line.=",".$line_library[$val];
            }
            $line_array[$row[csf('id')]]=$line;
        }
        //ksort($new_arr);
        foreach($new_arr as $key=>$v)
        {
            $line_array_new[$v]=$line_array[$v];
        }
        echo create_drop_down( "cbo_line_no", 130,$line_array_new,"", 1, "--- Select ---", $selected, "",0,0 );

    } else {
        if ($floor == 0 && $location != 0) $cond = " and location_name= $location";
        if ($floor != 0) $cond = " and floor_name= $floor"; else  $cond = " and floor_name like('%%')";

        echo create_drop_down("cbo_line_no", 130, "select id,line_name,sewing_line_serial from lib_sewing_line where  is_deleted=0 and status_active=1 and floor_name!=0 $cond order by sewing_line_serial", "id,line_name", 1, "--- Select ---", $selected, "", 0, 0);
    }
    exit();
}

if ($action=="load_drop_down_line_no")
{
	list($company_id,$location,$floor,$issue_date)=explode("_",$data);
	// echo $data; die;

	$nameArray= sql_select("select id, auto_update from variable_settings_production where company_name='$company_id' and variable_list=23 and status_active=1 and is_deleted=0");

	$prod_reso_allocation = $nameArray[0][csf('auto_update')];
	$cond="";
	if($prod_reso_allocation==1)
	{
		$line_library=return_library_array( "select id,line_name from lib_sewing_line where status_active=1 and company_name='$company_id'", "id", "line_name"  );
		$line_array=array();

		if( $floor==0 && $location!=0 ) $cond = " and a.location_id= $location";
		if( $floor!=0 ) $cond = " and a.floor_id= $floor";

		if($db_type==0) $issue_date = date("Y-m-d",strtotime($issue_date));
		else $issue_date = change_date_format(date("Y-m-d",strtotime($issue_date)),'','',1);

		$cond.=" and b.pr_date='".$issue_date."'";

		if($db_type==0)
		{
			$line_data=sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 and company_id=$company_id group by a.id, a.line_number,a.prod_resource_num order by a.prod_resource_num asc, a.id asc");
		}
		else if($db_type==2 || $db_type==1)
		{
			$line_data=sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 and company_id=$company_id group by a.id, a.line_number,a.prod_resource_num  order by  a.prod_resource_num,a.id asc");
		}
		 $line_merge=9999;
		foreach($line_data as $row)
		{
			$line='';
			$line_number=explode(",",$row[csf('line_number')]);
			foreach($line_number as $val)
			{
				if(count($line_number)>1)
				{
					$line_merge++;
					$new_arr[$line_merge]=$row[csf('id')];
				}
				else
					$new_arr[$line_library[$val]]=$row[csf('id')];

				if($line=='') $line=$line_library[$val]; else $line.=",".$line_library[$val];
			}
			$line_array[$row[csf('id')]]=$line;
		}
		ksort($new_arr);
		foreach($new_arr as $key=>$v)
		{
			$line_array_new[$v]=$line_array[$v];
		}
		echo create_drop_down( "cbo_line_no", 120,$line_array_new,"", 1, "--- Select ---", $selected, "",0,0 );
	}
	else
	{
		if( $floor==0 && $location!=0 ) $cond = " and location_name= $location";
		if( $floor!=0 ) $cond = " and floor_name= $floor"; else  $cond = " and floor_name like('%%')";

		echo create_drop_down( "cbo_line_no", 120, "select id,line_name from lib_sewing_line where  is_deleted=0 and status_active=1 and floor_name!=0 and company_name=$company_id order by line_name","id,line_name", 1, "--- Select ---", $selected, "",0,0 );
	}
	exit();
}

if ($action == "load_variable_settings") {
    echo "$('#sewing_production_variable').val(0);\n";
    $sql_result = sql_select("select printing_emb_production,production_entry from variable_settings_production where company_name=$data and variable_list=1 and status_active=1");
    foreach ($sql_result as $result) {
        echo "$('#sewing_production_variable').val(" . $result[csf("printing_emb_production")] . ");\n";
        echo "$('#styleOrOrderWisw').val(" . $result[csf("production_entry")] . ");\n";
    }

    $delivery_basis = return_field_value("cut_panel_delevery", "variable_settings_production", "company_name=$data and variable_list=32 and status_active=1 and is_deleted=0");
    if ($delivery_basis == 3 || $delivery_basis == 2 || $delivery_basis == "") {$delivery_basis = 3;}else {$delivery_basis = 1;}
    // echo $delivery_basis;
    echo "$('#delivery_basis').val(" . $delivery_basis . ");\n";
    exit();
}

if ($action=="load_variable_settings_for_working_company")
{
	$sql_result = sql_select("select working_company_mandatory from variable_settings_production where company_name=$data and variable_list=41 and status_active=1");
	$working_company="";
 	foreach($sql_result as $row)
	{
		$working_company=$row[csf("working_company_mandatory")];
	}
	echo $working_company;
 	exit();
}

if($action=="load_drop_down_embro_issue_source")
{
    $user_id = $_SESSION['logic_erp']["user_id"];
    //========== user credential start ========
    $userCredential = sql_select("SELECT WORKING_UNIT_ID, unit_id as company_id, store_location_id, item_cate_id, company_location_id as location_id FROM user_passwd where id=$user_id");
    $working_unit_id = $userCredential[0][csf('WORKING_UNIT_ID')];

    $working_credential_cond = "";

    if ($working_unit_id > 0) 
    {
        $working_credential_cond = " and comp.id in($working_unit_id)";
    }

	$explode_data = explode("**",$data);
	$data = $explode_data[0];
	$selected_company = 0;//$explode_data[1]; // 0 Added for URMI

	if($data==3)
	{
		if($db_type==0)
		{
			echo create_drop_down( "cbo_emb_company", 180, "select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0 and find_in_set(23,party_type) order by supplier_name","id,supplier_name", 1, "--- Select ---", $selected, "load_drop_down( 'requires/bundle_wise_reject_delivery_challan_to_recovery_controller', this.value, 'load_drop_down_location', 'location_td' );" );
		}
		else
		{
			echo create_drop_down( "cbo_emb_company", 180, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in(22,23) and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "--Select--", 0, "load_drop_down( 'requires/bundle_wise_reject_delivery_challan_to_recovery_controller', this.value, 'load_drop_down_location', 'location_td' );" );
		}
	}
	else if($data==1)
		echo create_drop_down( "cbo_emb_company", 180, "select id,company_name from lib_company comp where is_deleted=0 and status_active=1 $working_credential_cond order by company_name","id,company_name", 1, "--- Select ---", $selected_company, "load_drop_down( 'requires/bundle_wise_reject_delivery_challan_to_recovery_controller', this.value, 'load_drop_down_location', 'location_td' );",0,0 );
	else
		echo create_drop_down( "cbo_emb_company", 180, $blank_array,"", 1, "--- Select ---", $selected, "load_drop_down( 'requires/bundle_wise_reject_delivery_challan_to_recovery_controller', this.value, 'load_drop_down_location', 'location_td' );",0 );

	exit();
}

if($action=="bundle_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);

	?>
	<script>
		function search_populate(str)
		{
			//alert(str);
			if(str==0)
			{
				document.getElementById('search_by_th_up').innerHTML="Order No";
			}
			else if(str==1)
			{
				document.getElementById('search_by_th_up').innerHTML="Job No";
			}
			else if(str==2)
			{
				document.getElementById('search_by_th_up').innerHTML="Order No";
			}

		}
		function check_all_data() {
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				if($("#search"+i).css("display") !='none'){
				 js_set_value( i );
				}
			}
		}
		var selected_id = new Array();
		var selected_qty = 0;
		var selected_line = new Array();
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value( str)
		{
			/*if(selected_line.length>0 && trim($('#td_line' + str).attr('title'))!='')
			{
				if( jQuery.inArray( $('#td_line' + str).attr('title'), selected_line ) == -1 ) {
					alert('Line mix not allowed, please check again.');
					return;
				}
			}
			else{
				if(trim($('#td_line' + str).attr('title'))!='')
				{
					selected_line.push( $('#td_line' + str).attr('title') );
					$('#hidden_bundle_line').val(  $('#td_line' + str).attr('title') );
				}
			}*/
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );

			/*if( jQuery.inArray( $('#txt_individual' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual' + str).val() );
				selected_qty += $('#hidden_qty' + str).val()*1;

			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				selected_qty -= $('#hidden_qty' + str).val()*1;
			}
			var id = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );

			if(selected_id.length==0) selected_line.length=0;

			$('#hidden_bundle_nos').val( id );
			$('#total_bndl_qty').text( selected_qty );*/
		}

		function fnc_close()
		{
			var hidden_data='';
			
			$("#tbl_list_search").find('tr:not(:first)').each(function()
			{
				var tr_id = $(this).attr("id");
				//var bgColor=$(this).css("background-color");bg
				var bgColor=document.getElementById(tr_id).style.backgroundColor;
				if(bgColor=='yellow')
				{
					var trData=$(this).find('input[name="trData[]"]').val();
					if(hidden_data=="")
					{
						hidden_data=trData;
					}
					else
					{
						hidden_data+="_"+trData;
					}
				}
			});
			
			$('#hidden_data').val( hidden_data );
			parent.emailwindow.hide();
		}
	

		function reset_hide_field()
		{
			$('#hidden_bundle_nos').val( '' );
			selected_id = new Array();
			selected_line.length=0;

		}

    </script>
	</head>
	<body>
	<div align="center" style="width:100%;" >
		<form name="searchwofrm"  id="searchwofrm">
			<fieldset style="width:810px;">
			<!-- <legend>Enter search words <input type="checkbox" value="1" name="is_exact" id="is_exact"  checked> is exact</legend> -->
	            <table cellpadding="0" cellspacing="0" width="550" border="1" rules="all" class="rpt_table">
	                <thead>
	                    <th>Company</th>
	                    <th>Buyer</th>
	                    <th>Search By</th>
	                    <th id="search_by_th_up">Job No</th>
	                    <th>Prod. Date</th>
	                    <th>Line</th>
	                    <th>
	                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
	                        <input type="hidden" name="hidden_data" id="hidden_data">
	                    </th>
	                </thead>
	                <tr class="general">
	                    <td align="center">
	                    <?
							echo create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select --", $company_name, "",1 );
						?>
	                    </td>
	                    <td align="center">
	                        <?
							echo create_drop_down( "cbo_buyer_name", 130, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_name' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select --", $selected, "" );
							?>
	                    </td>
	                    <td align="center" id="search_by_td">
	                        <?
	                        $search_by = array(1=>"Job No",2=>"Order No");
							echo create_drop_down( "cbo_search_by", 130, $search_by, "", 1, "-- Select --", $selected, "search_populate(this.value)" );
							?>
	                    </td>
	                    <td>
	                    	<input type="text" name="txt_search_common" id="txt_search_common" style="width:80px" class="text_boxes" />
	                    </td>
	                    <td>
	                    	<input type="text" name="txt_prod_date" id="txt_prod_date" style="width:80px" class="datepicker " />
	                    </td>
	                    <td id="sewing_line_td">
	                    	<?
							echo create_drop_down( "cbo_line_no", 130, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select --", $selected, "" );
							?>
	                    </td>
	            		<td align="center">
	                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view (<? echo $company; ?>+'_'+document.getElementById('cbo_buyer_name').value+'_'+'<? echo trim($bundleNo,','); ?>'+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_prod_date').value+'_'+document.getElementById('cbo_line_no').value, 'create_bundle_search_list_view', 'search_div', 'bundle_wise_reject_delivery_challan_to_recovery_controller', 'setFilterGrid(\'tbl_list_search\',-1);reset_hide_field();')" style="width:100px;" />
	                     </td>
	                </tr>
	           </table>
	           <div style="width:100%; margin-top:5px; margin-left:10px" id="search_div" align="left"></div>
			</fieldset>
		</form>
	</div>
	</body>
	<script type="text/javascript">
		load_drop_down( 'bundle_wise_reject_delivery_challan_to_recovery_controller', document.getElementById('cbo_company_name').value+'_0_0_'+document.getElementById('txt_prod_date').value, 'load_drop_down_line', 'sewing_line_td' );
		$("#txt_prod_date").change(function()
		{
			load_drop_down( 'bundle_wise_reject_delivery_challan_to_recovery_controller', document.getElementById('cbo_company_name').value+'_0_0_'+document.getElementById('txt_prod_date').value, 'load_drop_down_line', 'sewing_line_td' );
		});
	</script>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="create_bundle_search_list_view")
{
 	$ex_data = explode("_",$data);
	$company = $ex_data[0];
	$buyer_name = $ex_data[1];
	$selectedBuldle=$ex_data[2];
	$search_by=$ex_data[3];
	$search_common=$ex_data[4];
	$prod_date=$ex_data[5];
	$line_id=$ex_data[6];

	if($buyer_name==0 && $search_by==0 && $search_common=="" && $prod_date=="")
	{
		?>
		<div class="alert alert-danger" style="width: 80%;font-weight: bold;">Please enter buyer name,search by or production date value. </div>
		<?
		die();
	}

	$sql_cond = " and f.company_name=$company";
	$sql_cond .= ($buyer_name!=0) ? " and f.buyer_name=$buyer_name" : "";
	$sql_cond .= ($prod_date!="") ? " and a.production_date='".date('d-M-Y',strtotime($prod_date))."'" : "";
	$sql_cond .= ($line_id!=0) ? " and a.sewing_line=$line_id" : "";
	if($search_common!="" && $search_by!=0)
	{
		if($search_by==1)
		{
			$sql_cond .= " and f.job_no_prefix_num=$search_common";
		}
		else
		{
			$sql_cond .= " and e.po_number='$search_common'";
		}
	}

	if($selectedBuldle!="")
	{
		$bndl_no_arr = explode(",", $selectedBuldle);
		$bndl_no_arr = array_map('trim', $bndl_no_arr);
		// print_r($bndl_no_arr);
		// $sql_cond .= " and c.bundle_no not in("."'".implode("','", trim($bndl_no_arr))."'".")";
		$sql_cond .= where_con_using_array($bndl_no_arr,"1","c.bundle_no not");
	}


	$company_short_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$buyer_short_name_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
	$floor_arr=return_library_array( "select id, floor_name from lib_prod_floor",'id','floor_name');
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
	$sewing_library=return_library_array( "select id,line_name from  lib_sewing_line", "id", "line_name");
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');

	$nameArray = sql_select("SELECT id, auto_update from variable_settings_production where company_name='$company' and variable_list=23 and status_active=1 and is_deleted=0");
    $prod_reso_allocation = $nameArray[0][csf('auto_update')];
	
	$sql="SELECT c.cut_no, c.bundle_no,c.barcode_no, d.po_break_down_id, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.bundle_no,c.barcode_no, f.buyer_name,f.style_ref_no as style,a.production_date,a.shift_name,a.sewing_line,a.floor_id, e.po_number,f.job_no_prefix_num as job_no,a.prod_reso_allo,a.challan_no,e.id as po_id,d.id as color_size_id,
		NVL(sum(CASE WHEN a.production_type ='5' and c.production_type ='5' and c.is_rescan=0 THEN c.reject_qty ELSE 0 END),0) - NVL(sum(CASE WHEN a.production_type ='5' and c.production_type ='5' and c.is_rescan!=0 THEN c.production_qnty ELSE 0 END),0) - NVL(sum(CASE WHEN a.production_type ='5' and c.production_type ='5' THEN c.replace_qty ELSE 0 END),0) AS rej_qty 
		from pro_garments_production_mst a, pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e, wo_po_details_master f 
		where d.job_id=f.id and a.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id $sql_cond and c.production_type=5 and a.status_active=1 and a.is_deleted=0 and c.reject_qty>0 and c.bundle_no is not null
		group by c.cut_no, c.bundle_no,c.barcode_no, d.po_break_down_id,d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.bundle_no,c.barcode_no, f.buyer_name,f.style_ref_no,a.production_date,a.shift_name,a.sewing_line,a.floor_id, e.po_number,f.job_no_prefix_num,a.prod_reso_allo,a.challan_no,e.id,d.id 
		order by  c.cut_no,c.bundle_no desc";

	// echo $sql;die;
	$result = sql_select($sql);

	$po_id_array = array();
	foreach ($result as $val) 
	{
		$po_id_array[$val['PO_ID']] = $val['PO_ID'];
	}
	$po_id_cond = where_con_using_array($po_id_array,0,"po_id");
	// ================== getting recv bundle =====================
	$sql = "SELECT bundle_no from reject_delivery_challan_to_recovery_dtls where status_active=1 and is_deleted=0 $po_id_cond";
	$res = sql_select($sql);
	$rcv_bundle_arr = array();
	foreach ($res as $val) 
	{
		$rcv_bundle_arr[$val['BUNDLE_NO']] = $val['BUNDLE_NO'];
	}
	
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1230" class="rpt_table">
        <thead>
            <th width="30">SL</th>
            <th width="50">Buyer</th>
            <th width="50">Job No</th>
            <th width="90">Order No</th>
            <th width="100">Style</th>
            <th width="100">Gmts. Item</th>
            <th width="100">Country</th>
            <th width="60">Prod. Date</th>
            <th width="40">Shift</th>
            <th width="80">Color Type</th>
            <th width="60">Size</th>
            <th width="60">Reject Qty</th>
            <th width="80">Floor</th>
            <th width="50">Line</th>
            <th width="60">Output Challan</th>
            <th width="90">Barcode</th>
            <th width="90">Bundle</th>
            <th></th>
        </thead>
	</table>
	<div style="width:1250px; max-height:230px; overflow-y:scroll" id="list_container_batch" align="left">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1230" class="rpt_table" id="tbl_list_search">
        	<tbody>
        <?
            $i=1;
            foreach ($result as $row)
            {
            	if($rcv_bundle_arr[$row['BUNDLE_NO']]=="")
            	{
					if($row['REJ_QTY']>0)
					{
						$line_name = "";
						if($row[csf('prod_reso_allo')]==1)
						{
							$line_number=explode(",",$prod_reso_arr[$row['SEWING_LINE']]);
							foreach($line_number as $val)
							{
								if($line_name=='') $line_name=$sewing_library[$val]; else $line_name.=",".$sewing_library[$val];
							}
						}
						else 
						{
							$line_name=$sewing_library[$row['SEWING_LINE']];
						}

						$data = $company."**".$buyer_short_name_arr[$row['BUYER_NAME']]."**".$row['BUYER_NAME']."**".$row['JOB_NO']."**".$row['PO_NUMBER']."**".$row['PO_ID']."**".$row['STYLE']."**".$row['ITEM_NUMBER_ID']."**".$garments_item[$row['ITEM_NUMBER_ID']]."**".$row['COUNTRY_ID']."**".$country_arr[$row['COUNTRY_ID']]."**".$row['PRODUCTION_DATE']."**".$shift_name[$row['SHIFT_NAME']]."**".$row['SHIFT_NAME']."**".$color_arr[$row['COLOR_NUMBER_ID']]."**".$row['COLOR_NUMBER_ID']."**".$size_arr[$row['SIZE_NUMBER_ID']]."**".$row['SIZE_NUMBER_ID']."**".$row['REJ_QTY']."**".$floor_arr[$row['FLOOR_ID']]."**".$row['FLOOR_ID']."**".$line_name."**".$row['SEWING_LINE']."**".$row['CHALLAN_NO']."**".$row['BARCODE_NO']."**".$row['BUNDLE_NO']."**".$row['PO_ID']."**".$row['COLOR_SIZE_ID'];

						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";					
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)">
							<td width="30">
								<? echo $i; ?>
								<input type="hidden" name="trData[]" id="trData<? echo $i; ?>" value="<? echo $data; ?>"/>
							</td>
		                    <td width="50"><?=$buyer_short_name_arr[$row['BUYER_NAME']];?></td>
		                    <td width="50"><?=$row['JOB_NO'];?></td>
		                    <td width="90"><?=$row['PO_NUMBER'];?></td>
		                    <td width="100"><?=$row['STYLE'];?></td>
		                    <td width="100"><?=$garments_item[$row['ITEM_NUMBER_ID']];?></td>
		                    <td width="100"><?=$country_arr[$row['COUNTRY_ID']];?></td>
		                    <td width="60"><?=change_date_format($row['PRODUCTION_DATE']);?></td>
		                    <td width="40"><?=$shift_name[$row['SHIFT_NAME']];?></td>
		                    <td width="80"><?=$color_arr[$row['COLOR_NUMBER_ID']];?></td>
		                    <td width="60"><?=$size_arr[$row['SIZE_NUMBER_ID']];?></td>
		                    <td width="60" align="right"><?=$row['REJ_QTY'];?></td>
		                    <td width="80"><?=$floor_arr[$row['FLOOR_ID']];?></td>
		                    <td width="50"><?=$line_name;?></td>
		                    <td width="60"><?=$row['CHALLAN_NO'];?></td>
		                    <td width="90"><?=$row['BARCODE_NO'];?></td>
		                    <td width="90"><?=$row['BUNDLE_NO'];?></td>
		                    <td></td>
						</tr>
						<?
						$i++;
					}
				}
			}
        	?>
        	</tbody>
        </table>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1230" class="rpt_table">  	
        	<tfoot>
        		<tr>
        			<th width="30"></th>
                    <th width="50"></th>
                    <th width="50"></th>
                    <th width="90"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="60"></th>
                    <th width="40"></th>
                    <th width="80"></th>
                    <th width="60"></th>
                    <th width="60"></th>
                    <th width="80"></th>
                    <th width="50"></th>
                    <th width="60"></th>
                    <th width="90"></th>
                    <th width="90"></th>
                    <th></th>
        		</tr>
        	</tfoot>
        </table>
    </div>
    <table width="830">
        <tr>
            <td align="center" >
                <span  style="float:left;"> <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All</span>
                <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
            </td>
        </tr>
    </table>
	<?
	exit();
}


if ($action=="save_update_delete")
{
	/*$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); */
	//$process = array( &$_POST );
	extract( $_POST );	
	//echo "10**".$operation.'systm';
	if ($operation==0)  // Insert Here
	{ 	
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if($db_type==0) $year_cond="YEAR(insert_date)"; 
		else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
		else $year_cond="";//defined Later
		
		$new_mrr_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'RDCR', date("Y",time()), 5, "select sys_number_prefix, sys_number_prefix_num from reject_delivery_challan_to_recovery_mst where company_id=$cbo_company_name and $year_cond=".date('Y',time())." order by id desc ", "sys_number_prefix","sys_number_prefix_num"));
		$id=return_next_id( "id", " reject_delivery_challan_to_recovery_mst", 1 ) ;
				 
		$field_array="id,sys_number_prefix,sys_number_prefix_num,sys_number,company_id,working_company,location_id,source,entry_date,entry_form,remarks,inserted_by,insert_date";
		$data_array="(".$id.",'".$new_mrr_number[1]."',".$new_mrr_number[2].",'".$new_mrr_number[0]."',".$cbo_company_name.",".$cbo_emb_company.",".$cbo_location.",".$cbo_source.",".$txt_issue_date.",525,".$txt_remark.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		
		$dtls_id = return_next_id( "id", "reject_delivery_challan_to_recovery_dtls", 1 );
		
		$field_array_dtls="id, mst_id, buyer_id,style,po_number, po_id, job_no, item_id, country_id, color_id,  size_id,line_id,shift_id,output_challan,prod_date,floor_id,qty,bundle_no,barcode_no, inserted_by, insert_date";
		for($j=1;$j<=$tot_row;$j++)
		{ 
			$buyerId="buyerId_".$j;
			$jobNo="jobNo_".$j;
			$orderId="orderId_".$j;
			$gmtsitemId="gmtsitemId_".$j;
			$countryId="countryId_".$j;
			$lineId="lineId_".$j;
			$colorSizeId="colorSizeId_".$j;
			$qty="qty_".$j;
			$colorId="colorId_".$j;
			$sizeId="sizeId_".$j;
			$styleRef="styleRef_".$j;
			$poNumber="poNumber_".$j;

			$prodDate="prodDate_".$j;
			$shiftId="shiftId_".$j;
			$floorId="floorId_".$j;
			$outChallan="outChallan_".$j;
			$bundleNo="bundleNo_".$j;
			$barcodeNo="barcodeNo_".$j;
			
			if($data_array_dtls!="") $data_array_dtls.=",";
			$data_array_dtls.="(".$dtls_id.",".$id.",".$$buyerId.",'".$$styleRef."','".$$poNumber."','".$$orderId."','".$$jobNo."','".$$gmtsitemId."','".$$countryId."','".$$colorId."','".$$sizeId."','".$$lineId."','".$$shiftId."','".$$outChallan."','".$$prodDate."','".$$floorId."','".$$qty."','".$$bundleNo."','".$$barcodeNo."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$dtls_id = $dtls_id+1;			
		}
		
		//echo "10**$data_array_dtls"; die;
		// echo "10**insert into reject_delivery_challan_to_recovery_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
		$rID=sql_insert("reject_delivery_challan_to_recovery_mst",$field_array,$data_array,0);
		$rID2=sql_insert("reject_delivery_challan_to_recovery_dtls",$field_array_dtls,$data_array_dtls,1);
		// echo "10**".$rID."&&".$rID2;oci_rollback($con);die;

		if($db_type==0)
		{
			if($rID && $rID2)
			{
				mysql_query("COMMIT");  
				echo "0**".$id."**".$new_mrr_number[0];
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**0**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2)
			{
				oci_commit($con);  
				echo "0**".$id."**".$new_mrr_number[0];
			}
			else
			{
				oci_rollback($con);
				echo "5**0**0";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$field_array="company_id*working_company*location_id*source*entry_date*updated_by*update_date";
		$data_array=$cbo_company_name."*".$cbo_emb_company."*".$cbo_location."*".$cbo_source."*".$txt_issue_date."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		$field_array_dtls="id, mst_id, buyer_id,style,po_number, po_id, job_no, item_id, country_id, color_id,  size_id,line_id,shift_id,output_challan,prod_date,floor_id,qty, bundle_no, barcode_no, inserted_by, insert_date";
		$dtls_id = return_next_id( "id", "reject_delivery_challan_to_recovery_dtls", 1 );
		$deleted_id='';
		for($j=1;$j<=$tot_row;$j++)
		{ 
			$buyerId="buyerId_".$j;
			$jobNo="jobNo_".$j;
			$orderId="orderId_".$j;
			$gmtsitemId="gmtsitemId_".$j;
			$countryId="countryId_".$j;
			$lineId="lineId_".$j;
			$colorSizeId="colorSizeId_".$j;
			$qty="qty_".$j;
			$colorId="colorId_".$j;
			$sizeId="sizeId_".$j;
			$styleRef="styleRef_".$j;
			$poNumber="poNumber_".$j;

			$prodDate="prodDate_".$j;
			$shiftId="shiftId_".$j;
			$floorId="floorId_".$j;
			$outChallan="outChallan_".$j;
			$bundleNo="bundleNo_".$j;
			$barcodeNo="barcodeNo_".$j;
			$dtlsId="dtlsId_".$j;
			
			if($data_array_dtls!="") $data_array_dtls.=",";
			$data_array_dtls.="(".$dtls_id.",".$update_id.",".$$buyerId.",'".$$styleRef."','".$$poNumber."','".$$orderId."','".$$jobNo."','".$$gmtsitemId."','".$$countryId."','".$$colorId."','".$$sizeId."','".$$lineId."','".$$shiftId."','".$$outChallan."','".$$prodDate."','".$$floorId."','".$$qty."','".$$bundleNo."','".$$barcodeNo."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$dtls_id = $dtls_id+1;	
			$deleted_id.=($deleted_id=="") ? $$dtlsId : ",".$$dtlsId;		
		}
		// echo "10**".$deleted_id;die();
		$rID=true; $rID2=true; $rID3=true;
		if(str_replace("'", "", $update_id)!="")
		{
			$field_array_status="updated_by*update_date*status_active*is_deleted";
			$data_array_status=$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
			$rID3=sql_multirow_update("reject_delivery_challan_to_recovery_dtls",$field_array_status,$data_array_status,"mst_id",$update_id,0);
		}
		// echo "10**insert into reject_delivery_challan_to_recovery_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;

		$rID=sql_update("reject_delivery_challan_to_recovery_mst",$field_array,$data_array,"id",$update_id,0);		
		
		if($data_array_dtls!="")
		{
			$rID2=sql_insert("reject_delivery_challan_to_recovery_dtls",$field_array_dtls,$data_array_dtls,1);
		}

		
		// echo "10**".$rID."&&".$rID2."&&".$rID3;die;
		
		if($db_type==0)
		{
			if($rID && $rID2 && $rID3 && $rID3)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_challan_no);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**".str_replace("'", '', $update_id)."**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2 && $rID3 && $rID3)
			{
				oci_commit($con);  
				echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_challan_no);
			}
			else
			{
				oci_rollback($con);
				echo "6**".str_replace("'", '', $update_id)."**1";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2) // Delete Here-------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		
		// master table delete here---------------------------------------
		$mst_id = return_field_value("id","reject_delivery_challan_to_recovery_mst","id=$update_id");
		if($mst_id=="" || $mst_id==0){ echo "15**0"; disconnect($con);die;}

		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array=$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";

		$deleted_id='';
		for($j=1;$j<=$tot_row;$j++)
		{ 
			$dtlsId="dtlsId".$j;
			$deleted_id.=($deleted_id=="") ? $dtlsId : ",".$dtlsId;
		}
		
		if($deleted_id!="")
		{
			$field_array_dtls_status="updated_by*update_date*status_active*is_deleted";
			$data_array_dtls_status=$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
			$dtlsrID=sql_multirow_update("reject_delivery_challan_to_recovery_dtls",$field_array_dtls_status,$data_array_dtls_status,"id",$deleted_id,0);
		}

		$rID=sql_update("reject_delivery_challan_to_recovery_mst",$field_array,$data_array,"id",$mst_id,0);

		//echo "10**".$field_array_dtls_status."=".$data_array_dtls_status.'='.$deleted_id;oci_commit($con);die;
		//echo "10**".$rID."&&".$dtlsrID;oci_commit($con);die;

		if($db_type==0)
		{
			if($rID && $dtlsrID)
			{
				mysql_query("COMMIT");
				echo "2**".str_replace("'","",$txt_requisition_no);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$txt_requisition_no);
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID && $dtlsrID)
			{
				oci_commit($con);
				echo "2**".str_replace("'","",$txt_requisition_no);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_requisition_no);
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="populate_list_view")
{
 	$ex_data = explode("_",$data);
	$id = $ex_data[0];
	
	$company_short_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$buyer_short_name_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$floor_arr=return_library_array( "select id, floor_name from lib_prod_floor", "id", "floor_name");
	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
	$sewing_library=return_library_array( "select id,line_name from  lib_sewing_line", "id", "line_name");
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');

		
	$sql="SELECT  a.id,a.company_id,a.source,a.working_company,a.location_id,b.id as dtlsid,b.job_no,b.buyer_id,b.style,b.po_number,b.po_id,b.item_id,b.country_id,b.color_id,b.size_id,b.line_id,b.shift_id,b.output_challan,b.bundle_no,b.barcode_no,b.floor_id,b.prod_date,b.qty
		from reject_delivery_challan_to_recovery_mst a, reject_delivery_challan_to_recovery_dtls b
		where a.id=b.mst_id and a.id=$id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";

	// echo $sql;die;
	$result = sql_select($sql);
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1230" class="rpt_table">
        <thead>
            <th width="30">SL</th>
            <th width="50">Buyer</th>
            <th width="50">Job No</th>
            <th width="90">Order No</th>
            <th width="100">Style</th>
            <th width="100">Gmts. Item</th>
            <th width="100">Country</th>
            <th width="60">Prod. Date</th>
            <th width="40">Shift</th>
            <th width="80">Color Type</th>
            <th width="60">Size</th>
            <th width="60">Reject Qty</th>
            <th width="80">Floor</th>
            <th width="50">Line</th>
            <th width="60">Output Challan</th>
            <th width="90">Barcode</th>
            <th width="90">Bundle</th>
            <th></th>
        </thead>
	</table>
	<div style="width:1250px; max-height:230px; overflow-y:scroll" id="list_container_batch" align="left">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1230" class="rpt_table" id="tbl_details">
        	<tbody>
        <?
            $i=1;
            foreach ($result as $row)
            {
				$line_name = "";
				// if($row[csf('prod_reso_allo')]==1)
				// {
					$line_number=explode(",",$prod_reso_arr[$row['LINE_ID']]);
					foreach($line_number as $val)
					{
						if($line_name=='') $line_name=$sewing_library[$val]; else $line_name.=",".$sewing_library[$val];
					}
				// }
				// else 
				// {
				// 	$line_name=$sewing_library[$row['SEWING_LINE']];
				// }

				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";					
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $i; ?>)" id="tr_<? echo $i;?>">
					<td width="30"><? echo $i; ?></td>
                    <td width="50"><?=$buyer_short_name_arr[$row['BUYER_ID']];?></td>
                    <td width="50"><?=$row['JOB_NO'];?></td>
                    <td width="90"><?=$row['PO_NUMBER'];?></td>
                    <td width="100"><?=$row['STYLE'];?></td>
                    <td width="100"><?=$garments_item[$row['ITEM_ID']];?></td>
                    <td width="100"><?=$country_arr[$row['COUNTRY_ID']];?></td>
                    <td width="60"><?=change_date_format($row['PROD_DATE']);?></td>
                    <td width="40"><?=$shift_name[$row['SHIFT_ID']];?></td>
                    <td width="80"><?=$color_arr[$row['COLOR_ID']];?></td>
                    <td width="60"><?=$size_arr[$row['SIZE_ID']];?></td>
                    <td width="60" align="right"><?=$row['QTY'];?></td>
                    <td width="80"><?=$floor_arr[$row['FLOOR_ID']];?></td>
                    <td width="50"><?=$line_name;?></td>
                    <td width="60"><?=$row['OUTPUT_CHALLAN'];?></td>
                    <td width="90"><?=$row['BARCODE_NO'];?></td>
                    <td width="90">
                    	<?=$row['BUNDLE_NO'];?>
                    	<input type="hidden" name="dtlsId[]" id="dtlsId" value="<?=$row['DTLSID'];?>">
                    	<input type="hidden" name="jobNo[]" id="jobNo" value="<?=$row['JOB_NO'];?>">
                    	<input type="hidden" name="buyerId[]" id="buyerId" value="<?=$row['BUYER_ID'];?>">
                    	<input type="hidden" name="orderId[]" id="orderId" value="<?=$row['PO_ID'];?>">
                    	<input type="hidden" name="itemId[]" id="itemId" value="<?=$row['ITEM_ID'];?>">
                    	<input type="hidden" name="countryId[]" id="countryId" value="<?=$row['COUNTRY_ID'];?>">
                    	<input type="hidden" name="colorSizeId[]" id="colorSizeId" value="">
                    	<input type="hidden" name="lineId[]" id="lineId" value="<?=$row['LINE_ID'];?>">
                    	<input type="hidden" name="prodDate[]" id="prodDate" value="<?=$row['PROD_DATE'];?>">
                    	<input type="hidden" name="shiftId[]" id="shiftId" value="<?=$row['SHIFT_ID'];?>">
                    	<input type="hidden" name="floorId[]" id="floorId" value="<?=$row['FLOOR_ID'];?>">
                    	<input type="hidden" name="outChallan[]" id="outChallan" value="<?=$row['OUTPUT_CHALLAN'];?>">
                    	<input type="hidden" name="barcodeNo[]" id="barcodeNo" value="<?=$row['BARCODE_NO'];?>">
                    	<input type="hidden" name="bundleNo[]" id="bundleNo" value="<?=$row['BUNDLE_NO'];?>">
                    	<input type="hidden" name="qty[]" id="qty" value="<?=$row['QTY'];?>">
                    	<input type="hidden" name="colorId[]" id="colorId" value="<?=$row['COLOR_ID'];?>">
                    	<input type="hidden" name="sizeId[]" id="sizeId" value="<?=$row['SIZE_ID'];?>">
                    	<input type="hidden" name="poNumber[]" id="poNumber" value="<?=$row['PO_NUMBER'];?>">
                    	<input type="hidden" name="styleRef[]" id="styleRef" value="<?=$row['STYLE'];?>">                    		
                    </td>
                    <td>
                    	<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:20px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
                    </td>
				</tr>
				<?
				$i++;
				
			}
        	?>
        	</tbody>
        </table>
    </div>
	<?
	exit();
}



if ($action=="challan_no_popup")
{
	echo load_html_head_contents("Challan Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	?>

	<script>

		function js_set_value(id)
		{
			$('#hidden_mst_id').val(id);
			parent.emailwindow.hide();
		}

    </script>

	</head>

	<body>
	<div align="center" style="width:930px;">
		<form name="searchwofrm"  id="searchwofrm">
			<fieldset style="width:920px;">
			<legend>Enter search words</legend>
	            <table cellpadding="0" cellspacing="0" width="800" border="1" rules="all" class="rpt_table">
	                <thead>
	                	<th width="180">Sewing Company</th>
	                    <th width="100">Order No</th>
	                    <th width="100">Challan No</th>
	                    <th width="100">Cutting No</th>
	                    <th width="100">Bundle No</th>
	                    <th width="120">Line No</th>
	                    <th width="70">Output Date</th>
	                    <th>
	                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
	                        <input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_name; ?>">
	                    	<input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="text_boxes" value="">
	                    </th>
	                </thead>
	                <tr class="general">
	                	<td align="center" id="emb_company_td">
	                    	<?
								echo create_drop_down( "cbo_emb_company", 180, $line_library,"", 1, "--- Select ---", $selected, "" );
							?>
	                    </td>
	                    <td><input type="text" style="width:90px" class="text_boxes"  name="txt_order_no" id="txt_order_no" /></td>
	                    <td><input type="text" style="width:90px" class="text_boxes"  name="txt_search_common" id="txt_search_common" /></td>
	                    <td><input type="text" style="width:90px" class="text_boxes"  name="txt_cut_no" id="txt_cut_no" /></td>
	                    <td><input type="text" style="width:90px" class="text_boxes"  name="txt_bundle_no" id="txt_bundle_no" /></td>
	                    <td id="line_no_id">
	                    	<?
							//	$line_library=return_library_array( "select id,line_name from lib_sewing_line where company_name=$cbo_company_name", "id", "line_name"  );
							//	echo create_drop_down( "cbo_line_no", 120, $line_library,"", 1, "--- Select ---", $selected, "" );
									echo create_drop_down( "cbo_line_no", 120, $blank_array,"", 1, "-- Select Floor --", $selected, "" );

							?>
	                    </td>
	                    <td align="center">
	                    	<input type="text" name="txt_issue_date" id="txt_issue_date" value="" class="datepicker" style="width:60px;"  />
	                    </td>

	            		<td align="center">
	                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_line_no').value+'_'+document.getElementById('txt_issue_date').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_emb_company').value+'_'+document.getElementById('txt_order_no').value+'_<?php echo $cbo_source; ?>'+'_'+document.getElementById('txt_cut_no').value+'_'+document.getElementById('txt_bundle_no').value, 'create_challan_search_list_view', 'search_div', 'bundle_wise_reject_delivery_challan_to_recovery_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
	                     </td>

	                </tr>
	           </table>
	           <div style="width:100%; margin-top:10px; margin-left:3px" id="search_div" align="left"></div>
			</fieldset>
		</form>
	</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	<script>
		load_drop_down( 'bundle_wise_reject_delivery_challan_to_recovery_controller', '<?php echo $cbo_serving_company; ?>_<?php echo $cbo_location; ?>_<?php echo $cbo_floor; ?>_<?php echo $txt_issue_date; ?>', 'load_drop_down_line_no', 'line_no_id' );

		load_drop_down('bundle_wise_reject_delivery_challan_to_recovery_controller','<?php echo $cbo_source; ?>_<?php echo $cbo_serving_company; ?>'  , 'load_drop_down_sewing_company', 'emb_company_td');

	</script>

	</html>
	<?

	exit();
}

if($action=="create_challan_search_list_view")
{
	list($challan,$line_no,$issue_date,$company_id,$sew_company,$order_no, $cbo_source,$cutting_no,$bundle_no) = explode("_",$data);
	$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
	$search_string="%".trim($data[0])."";
	if($challan!=''){$challan_con=" and a.sys_number_prefix_num ='$challan'";}

	if($db_type==0) {$year_field="YEAR(a.insert_date) as year"; }
	else if($db_type==2) {$year_field="to_char(a.insert_date,'YYYY') as year";   }
	else $year_field="";//defined Later


	if($order_no!=''){$order_con=" and a.po_number like('%$order_no%')";}else{$order_con="";}
	if($db_type==0) if($issue_date!='') $issue_date_con = "and a.entry_date = '".change_date_format($issue_date, "yyyy-mm-dd", "-")."'"; else $issue_date_con ="";
	else if($db_type==2) if($issue_date!='')$issue_date_con = "and a.entry_date = '".change_date_format($issue_date,'','',1)."'"; else $issue_date_con ="";

	if($sew_company!=0){$sew_company_con=" and a.working_company=$sew_company";}
	$bundle_no_cond=($bundle_no)? " and a.bundle_no='".$bundle_no."'" : " ";
	$line_con=($line_no)? " and a.line_id like '%".$line_no ."%'" : " ";

	$sql = "SELECT a.id, $year_field, a.sys_number_prefix_num, a.sys_number, a.entry_date,a.source, a.working_company, a.location_id from reject_delivery_challan_to_recovery_mst a where a.status_active=1 and a.is_deleted=0 $challan_con $order_con $issue_date $sew_company_con $bundle_no_cond $line_con order by a.sys_number_prefix_num desc";
	// echo $sql;die();

	$nameArray= sql_select("select id, auto_update from variable_settings_production where company_name='$company_id' and variable_list=23 and status_active=1 and is_deleted=0");
	$prod_reso_allocation = $nameArray[0][csf('auto_update')];

	//  echo $sql;//die;
	$result = sql_select($sql);
	$floor_arr=return_library_array( "select id,floor_name from lib_prod_floor",'id','floor_name');
	$location_arr=return_library_array( "select id,location_name from lib_location",'id','location_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');

	$resource_alocate_line=return_library_array( "select id, line_number from prod_resource_mst", "id", "line_number"  );
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="900" class="rpt_table" align="left">
        <thead>
            <th width="30">SL</th>
            <th width="40">Challan</th>
            <th width="40">Year</th>
            <th width="60">Challan Date</th>
            <th width="60">Source</th>
            <th width="110">Sewing Company</th>
            <th width="110">Location</th>
        </thead>
	</table>
	<div style="width:920px; max-height:240px; overflow-y:scroll" id="list_container_batch" align="left">
        <table align="left" cellspacing="0" cellpadding="0" border="1" rules="all" width="900" class="rpt_table" id="tbl_list_search">
        <?
            $i=1;
            foreach ($result as $row)
            {
                if ($i%2==0)  $bgcolor="#E9F3FF";
                else $bgcolor="#FFFFFF";

                if($row[csf('source')]==1) $serv_comp=$company_arr[$row[csf('working_company')]];
				else $serv_comp=$supplier_arr[$row[csf('working_company')]];
        	?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $row[csf('id')]; ?>);">
                    <td width="30"><? echo $i; ?></td>
                    <td width="40"><p>&nbsp;<? echo $row[csf('sys_number_prefix_num')]; ?></p></td>
                    <td width="40" align="center"><p><? echo $row[csf('year')]; ?></p></td>
                    <td width="60" align="center"><p><? echo change_date_format($row[csf('entry_date')]); ?></p></td>
                    <td width="60" align="center"><p><? echo $knitting_source[$row[csf('source')]]; ?></p></td>
                    <td width="110"><p><? echo $serv_comp; ?></p></td>
                    <td width="110"><p><? echo $location_arr[$row[csf('location_id')]]; ?></p></td>
                    
                </tr>
        	<?
            $i++;
            }
        	?>
        </table>
    </div>
	<?
    exit();
}

if ($action == "load_drop_down_sewing_company") 
{
    $explode_data = explode("_", $data);
    $data = $explode_data[0];
    $serving_company =$explode_data[1];// $explode_data[1];

    if ($data == 3)
	{
        if ($db_type == 0)
		{
            echo create_drop_down("cbo_emb_company", 180, "select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0 and find_in_set(22,23,party_type) order by supplier_name", "id,supplier_name", 1, "--- Select ---", $serving_company,"",1);
        }
		else
		{
            echo create_drop_down("cbo_emb_company", 180, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in(22,23) and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "--Select--", $serving_company,"",1);
        }
    }
	else if ($data == 1)
	{
		echo create_drop_down("cbo_emb_company", 180, "select id,company_name from lib_company comp where is_deleted=0 and status_active=1 $company_cond order by company_name", "id,company_name", 1, "--- Select ---", $serving_company,"",1);

	}
    else
	{
        echo create_drop_down("cbo_emb_company", 180, $blank_array, "", 1, "--- Select ---", $selected, "", 0);
	}

    exit();
}



if($action=='populate_data_from_challan_popup')
{
	$data_array=sql_select("SELECT id, sys_number, company_id, source, entry_date,working_company,location_id,remarks from reject_delivery_challan_to_recovery_mst where id='$data'");
	foreach ($data_array as $row)
	{ 
		$lay_plan_cutting_no=return_field_value( "cutting_no","ppl_cut_lay_mst","id='".$row[csf("lay_plan_id")]."'");
		
		echo "document.getElementById('txt_challan_no').value 			= '".$row[csf("sys_number")]."';\n";
		echo "document.getElementById('cbo_company_name').value 				= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_emb_company').value 		= '".$row[csf("working_company")]."';\n";
		
		// echo "load_drop_down( 'requires/bundle_wise_reject_delivery_challan_to_recovery_controller', '".$row[csf("working_company")]."', 'load_drop_down_location','location_td');\n";

		echo "document.getElementById('cbo_source').value 				= '".$row[csf("source")]."';\n";
		echo "document.getElementById('cbo_location').value 				= '".$row[csf("location_id")]."';\n";
		echo "document.getElementById('txt_issue_date').value 				= '".change_date_format($row[csf("entry_date")])."';\n";
		echo "document.getElementById('update_id').value 					= '".$row[csf("id")]."';\n";
		echo "document.getElementById('txt_remark').value 					= '".$row[csf("remarks")]."';\n";
		
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_reject_delivery_challan_to_recovery_entry',1);\n";  
		exit();
	}
}

if($action=="challan_print")
{
	echo load_html_head_contents("Embellishment Delivery Entry","../../", 1, 1, $unicode,'','');
	?>
	<link href="../../css/style_common.css" rel="stylesheet" type="text/css" media="all">
	<?
	extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name");
	$location_library=return_library_array( "select id, location_name from  lib_location", "id", "location_name");
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	
	$sql="SELECT id, sys_number_prefix, sys_number_prefix_num, sys_number, company_id, location_id, source, entry_date, remarks,working_company from reject_delivery_challan_to_recovery_mst where entry_form=525 and id='$data[1]' and status_active=1 and is_deleted=0 ";

	$dataArray=sql_select($sql);
	$delivery_mst_id =$dataArray[0][csf('id')];
	?>
	<div style="width:930px;">
    <table width="900" cellspacing="0" align="right">
        <tr>
            <td colspan="6" align="center" style="font-size:24px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
        </tr>
        <tr class="form_caption">
        	<td colspan="6" align="center" style="font-size:14px">
				<?

					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0] and status_active=1 and is_deleted=0");
					foreach ($nameArray as $result)
					{
					?>
						Plot No: <? echo $result[csf('plot_no')]; ?>
						Level No: <? echo $result[csf('level_no')]?>
						Road No: <? echo $result[csf('road_no')]; ?>
						Block No: <? echo $result[csf('block_no')];?>
						City No: <? echo $result[csf('city')];?>
						Zip Code: <? echo $result[csf('zip_code')]; ?>
						Province No: <?php echo $result[csf('province')];?>
						Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br>
						Email Address: <? echo $result[csf('email')];?>
						Website No: <? echo $result[csf('website')];

					}
                ?>
            </td>
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:20px"><u><strong><?=$data[2];?></strong></u></td>
        </tr>
        <tr>
            <td width="125"><strong>Challan No:</strong></td> <td width="175px"><? echo $dataArray[0][csf('sys_number')]; ?></td>
            <td width="125"><strong>Source</strong></td><td width="175px"><? echo $knitting_source[$dataArray[0][csf('source')]]; ?></td>
            <td colspan="2" rowspan="3"  id="barcode_img_id">QR Code
            </td>
        </tr>
        <tr>
            <td><strong>Sew.Company:</strong></td><td>
				<?
					if($dataArray[0][csf('source')]==1) echo $company_library[$dataArray[0][csf('working_company')]];
					else echo $supplier_library[$dataArray[0][csf('working_company')]];

                ?>
            </td>
            <td><strong>Location:</strong></td><td><? echo $location_library[$dataArray[0][csf('location_id')]]; ?></td>
        </tr>
        <tr>
        	<td><strong>Remarks :</strong></td><td><? echo $dataArray[0][csf('remarks')]; ?></td>
            <td><strong>Delivery Date :</strong></td><td><? echo change_date_format($dataArray[0][csf('entry_date')]); ?></td>
        </tr>
    </table>
    <?
    $company_short_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$buyer_short_name_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
	$floor_arr=return_library_array( "select id, floor_name from lib_prod_floor",'id','floor_name');
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
	$sewing_library=return_library_array( "select id,line_name from  lib_sewing_line", "id", "line_name");
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');

	// print_r($floor_arr);
	$sql="SELECT  a.id,a.company_id,a.source,a.working_company,a.location_id,b.id as dtlsid,b.job_no,b.buyer_id,b.style,b.po_number,b.po_id,b.item_id,b.country_id,b.color_id,b.size_id,b.line_id,b.shift_id,b.output_challan,b.bundle_no,b.barcode_no,b.floor_id,b.prod_date,b.qty
		from reject_delivery_challan_to_recovery_mst a, reject_delivery_challan_to_recovery_dtls b
		where a.id=b.mst_id and a.id=$data[1] and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";

	// echo $sql;die;
	$result = sql_select($sql);
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1230" class="rpt_table">
        <thead>
            <th width="30">SL</th>
            <th width="50">Buyer</th>
            <th width="50">Job No</th>
            <th width="90">Order No</th>
            <th width="100">Style</th>
            <th width="100">Gmts. Item</th>
            <th width="100">Country</th>
            <th width="60">Prod. Date</th>
            <th width="40">Shift</th>
            <th width="80">Color Type</th>
            <th width="60">Size</th>
            <th width="60">Reject Qty</th>
            <th width="80">Floor</th>
            <th width="50">Line</th>
            <th width="60">Output Challan</th>
            <th width="90">Barcode</th>
            <th width="90">Bundle</th>
        </thead>
	</table>
	<div style="width:1250px; max-height:100%; overflow-y:auto;" id="list_container_batch" align="left">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1230" class="rpt_table" id="tbl_details">
        	<tbody>
        <?
            $i=1;
            $tot = 0;
            foreach ($result as $row)
            {
				$line_name = "";
				// if($row[csf('prod_reso_allo')]==1)
				// {
					$line_number=explode(",",$prod_reso_arr[$row['LINE_ID']]);
					foreach($line_number as $val)
					{
						if($line_name=='') $line_name=$sewing_library[$val]; else $line_name.=",".$sewing_library[$val];
					}
				// }
				// else 
				// {
				// 	$line_name=$sewing_library[$row['SEWING_LINE']];
				// }

				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";					
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $i; ?>)" id="tr_<? echo $i;?>">
					<td width="30"><p><? echo $i; ?></p></td>
                    <td width="50"><p><?=$buyer_short_name_arr[$row['BUYER_ID']];?></p></td>
                    <td width="50"><p><?=$row['JOB_NO'];?></p></td>
                    <td width="90"><p><?=$row['PO_NUMBER'];?></p></td>
                    <td width="100"><p><?=$row['STYLE'];?></p></td>
                    <td width="100"><p><?=$garments_item[$row['ITEM_ID']];?></p></td>
                    <td width="100"><p><?=$country_arr[$row['COUNTRY_ID']];?></p></td>
                    <td width="60"><p><?=change_date_format($row['PROD_DATE']);?></p></td>
                    <td width="40"><p><?=$shift_name[$row['SHIFT_ID']];?></p></td>
                    <td width="80"><p><?=$color_arr[$row['COLOR_ID']];?></p></td>
                    <td width="60"><p><?=$size_arr[$row['SIZE_ID']];?></p></td>
                    <td width="60" align="right"><p><?=$row['QTY'];?></p></td>
                    <td width="80"><p><?=$floor_arr[$row['FLOOR_ID']];?></p></td>
                    <td width="50"><p><?=$line_name;?></p></td>
                    <td width="60"><p><?=$row['OUTPUT_CHALLAN'];?></p></td>
                    <td width="90"><p><?=$row['BARCODE_NO'];?></p></td>
                    <td width="90"><p><?=$row['BUNDLE_NO'];?></p></td>
				</tr>
				<?
				$i++;
				$tot += $row['QTY'];
				
			}
        	?>
        	</tbody>
        	<tfoot>
        		<th colspan="11">Total</th>
        		<th><?=number_format($tot,0);?></th>
        		<th colspan="5"></th>
        	</tfoot>
        </table>
    </div>
         
        <br>
		 <?
            echo signature_table(28, $data[0], "900px");
         ?>
	</div>
	</div>
   	<script type="text/javascript" src="../../js/jquery.js"></script>
	<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
	<script>
	function generateBarcode( valuess ){
			var value = valuess;//$("#barcodeValue").val();
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer ='bmp';// $("input[name=renderer]:checked").val();
			var settings = {
			  output:renderer,
			  bgColor: '#FFFFFF',
			  color: '#000000',
			  barWidth: 1,
			  barHeight: 30,
			  moduleSize:5,
			  posX: 10,
			  posY: 20,
			  addQuietZone: 1
			};

			 value = {code:value, rect: false};
			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
	    generateBarcode('<? echo $dataArray[0][csf('sys_number')]; ?>');
	 </script>
	<?
	exit();
}

?>
