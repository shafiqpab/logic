<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']['user_id'];
if ($_SESSION['logic_erp']['user_id'] == "") {
    header("location:login.php");
    die;
}
$permission = $_SESSION['page_permission'];
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

//load drop down supplier
if ($action == "load_drop_down_supplier")
{
	if($data){$companyCon=" and a.tag_company='$data'";}
	else{$companyCon="";}
	echo create_drop_down("cbo_supplier_id", 110, "select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id $companyCon and b.party_type =2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name", "id,supplier_name", 1, "-- Select Supplier--", $selected, "", 0);
	exit();
}

if($action == "composition_popup")
{
	echo load_html_head_contents("Composition Info","../../../../", 1, 1, '','1','');
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

        
		$result=sql_select("select id,composition_name from  lib_composition_array where status_active in(1,2) and is_deleted=0 order by composition_name");
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

if ($action == "report_generate") 
{
    $process = array(&$_POST);
    extract(check_magic_quote_gpc($process));
    
    $cbo_appoval_status=str_replace("'","",$cbo_appoval_status);
    $cbo_qc_status=str_replace("'","",$cbo_qc_status);
    $search_type=str_replace("'","",$search_type);

    // var_dump($cbo_qc_status);

    $cfadCond = '';
    if($cbo_appoval_status>1)
    {
        if($cbo_appoval_status==2)
        {
            $cfadCond = " and e.comments_author_acceptance in(1,2,3)";
        }
        else
        {
            $cfadCond = " and e.comments_author_acceptance in(4)";
        }

    }
    else
    {
       
        if($cbo_qc_status==0)
        {
            $cfadCond = " and e.comments_author_acceptance in(1,2,3,4)";
        }
        else
        {
            $cfadCond = " and e.comments_author_acceptance in($cbo_qc_status)";
        }
    }
    

    if (str_replace("'", "", $cbo_supplier_id) != 0)
        $supplierCond = " and b.supplier_id in(" . str_replace("'", "", $cbo_supplier_id) . ")";

    if (str_replace("'", "", $txt_composition_id) != "")
    {
        $composition_id = str_replace(",", "','", $txt_composition_id);
        $composition_id_cond = " and c.yarn_comp_type1st in($composition_id)";
	}

    if (str_replace("'", "", $cbo_yarn_type) != "")
        $yarn_type_cond = " and c.yarn_type in(" . str_replace("'", "", $cbo_yarn_type) . ")";
    if (str_replace("'", "", $cbo_yarn_count) != "")
        $yarn_count_cond = " and c.yarn_count_id in(" . str_replace("'", "", $cbo_yarn_count) . ")";

	$txt_lot_no = str_replace("'", "", trim($txt_lot_no));	
	$lot_no = '';		
	if ($txt_lot_no != "")
	{
		$lot_no = " and c.lot='".$txt_lot_no."'";
	}

    if (str_replace("'", "", $cbo_issue_purpose) != "" && str_replace("'", "", $cbo_issue_purpose) != 0){
        $issue_purpose_cond = " and a.issue_purpose in (".str_replace("'", "", $cbo_issue_purpose).")";
    }

    
    if($search_type ==1)
	{
        if (str_replace("'", "", trim($txt_date_from)) != "" && str_replace("'", "", trim($txt_date_to)) != "")
        {
            $test_date_cond = " and d.test_date between " . trim($txt_date_from) . " AND " . trim($txt_date_to) . "";
        }
        else
        {
            $test_date_cond = "";
        }
    }
    else
	{
        if (str_replace("'", "", trim($txt_date_from)) != "" && str_replace("'", "", trim($txt_date_to)) != "")
        {
            $rcv_date_cond = " and a.receive_date between " . trim($txt_date_from) . " AND " . trim($txt_date_to) . "";
        }
        else
        {
            $rcv_date_cond = "";
        }
    }

    // $composition_arr = return_library_array("select id,composition_name from  lib_composition_array where status_active=1 and is_deleted=0 
    // order by composition_name", "id", "composition_name");
    $color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name");
    $count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
    $supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
    $yarn_test_for_arr = array(1=>'Bulk Yarn',2=>'Sample Yarn');


    if ($type==1) // Show Button
    {
        //for date

        $sql = "SELECT a.receive_date, c.id as prod_id,c.lot,c.yarn_count_id, c.yarn_comp_type1st,c.yarn_comp_percent1st,c.yarn_comp_type2nd,c.yarn_comp_percent2nd, c.color as yarn_color, 
        c.supplier_id, c.yarn_type, b.cons_quantity, d.ready_to_approved,d.approved,d.id as yarn_test_id, d.test_date, d.test_for, d.specimen_wgt, d.specimen_length, d.color, d.receive_qty, d.lc_number, 
        d.lc_qty, d.actual_yarn_count, d.actual_yarn_count_phy, d.yarn_apperance_grad, d.yarn_apperance_phy, d.actual_yarn_comp, 
        d.actual_yarn_comp_phy, d.pilling, d.pilling_phy, d.brusting, d.brusting_phy, d.twist_per_inc, d.twist_per_inc_phy, 
        d.moisture_content, d.moisture_content_phy, d.ipi_value, d.ipi_value_phy, d.csp_minimum, d.csp_minimum_phy, d.csp_actual, 
        d.csp_actual_phy, d.thin_yarn, d.thin_yarn_phy, d.thick, d.thick_phy, d.u, d.u_phy, d.cv, d.cv_phy, d.neps_per_km, d.neps_per_km_phy, 
        d.heariness, d.heariness_phy, d.counts_cv, d.counts_cv_phy, d.system_result, d.grey_gsm, d.grey_wash_gsm, d.required_gsm, 
        d.required_dia, d.machine_dia, d.stich_length, d.grey_gsm_dye, d.batch, d.finish_gsm, d.finish_dia, d.length, d.width, 
        e.comments_knit_acceptance, e.comments_knit, e.comments_dye_acceptance, e.comments_dye, e.comments_author_acceptance, 
        e.comments_author from inv_receive_master a, inv_transaction b, product_details_master c, inv_yarn_test_mst d, 
        inv_yarn_test_comments e where a.id=b.mst_id and b.prod_id=c.id and c.id=d.prod_id and d.id=e.mst_table_id and a.company_id=$cbo_company_name and a.item_category=1 and 
        b.transaction_type=1 $lot_no $supplierCond $yarn_type_cond $yarn_count_cond $composition_id_cond $test_date_cond $rcv_date_cond $cfadCond and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 ";

        //echo $sql; 
        $result = sql_select($sql);

        if(count($result)==0)
        {
            ?>
            <div class="alert alert-danger">Data not found! Please try again.</div>
            <?
            die();
        }

        $con = connect();	
        $r_id1=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (1990)");
        if($r_id1)
        {
            oci_commit($con);
        }

        $all_yarn_test_id_arr = array();
        foreach($result as $row)
	    {
            $data_array[$row[csf("prod_id")]][$row[csf("color")]]['lot']=$row[csf("lot")];
            $data_array[$row[csf("prod_id")]][$row[csf("color")]]['yarn_count_id']=$row[csf("yarn_count_id")];
            $data_array[$row[csf("prod_id")]][$row[csf("color")]]['yarn_comp_type1st']=$row[csf("yarn_comp_type1st")];
            $data_array[$row[csf("prod_id")]][$row[csf("color")]]['yarn_comp_percent1st']=$row[csf("yarn_comp_percent1st")];
            $data_array[$row[csf("prod_id")]][$row[csf("color")]]['yarn_comp_type2nd']=$row[csf("yarn_comp_type2nd")];
            $data_array[$row[csf("prod_id")]][$row[csf("color")]]['yarn_comp_percent2nd']=$row[csf("yarn_comp_percent2nd")];
            $data_array[$row[csf("prod_id")]][$row[csf("color")]]['yarn_color']=$row[csf("yarn_color")];
            $data_array[$row[csf("prod_id")]][$row[csf("color")]]['yarn_type']=$row[csf("yarn_type")];
            $data_array[$row[csf("prod_id")]][$row[csf("color")]]['test_date']=$row[csf("test_date")];
            $data_array[$row[csf("prod_id")]][$row[csf("color")]]['supplier_id']=$row[csf("supplier_id")];
            $data_array[$row[csf("prod_id")]][$row[csf("color")]]['receive_date']=$row[csf("receive_date")];
            $data_array[$row[csf("prod_id")]][$row[csf("color")]]['test_for'] =$row[csf("test_for")];
            $data_array[$row[csf("prod_id")]][$row[csf("color")]]['ready_to_approved']=$row[csf("ready_to_approved")];
            $data_array[$row[csf("prod_id")]][$row[csf("color")]]['comments_knit_acceptance']=$row[csf("comments_knit_acceptance")];
            $data_array[$row[csf("prod_id")]][$row[csf("color")]]['comments_dye_acceptance']=$row[csf("comments_dye_acceptance")];
            $data_array[$row[csf("prod_id")]][$row[csf("color")]]['comments_author_acceptance']=$row[csf("comments_author_acceptance")];
            $data_array[$row[csf("prod_id")]][$row[csf("color")]]['actual_yarn_count_phy']=$row[csf("actual_yarn_count_phy")];
            $data_array[$row[csf("prod_id")]][$row[csf("color")]]['yarn_apperance_phy']=$row[csf("yarn_apperance_phy")];
            $data_array[$row[csf("prod_id")]][$row[csf("color")]]['twist_per_inc_phy']=$row[csf("twist_per_inc_phy")];
            $data_array[$row[csf("prod_id")]][$row[csf("color")]]['moisture_content_phy']=$row[csf("moisture_content_phy")];
            $data_array[$row[csf("prod_id")]][$row[csf("color")]]['ipi_value_phy']=$row[csf("ipi_value_phy")];
            $data_array[$row[csf("prod_id")]][$row[csf("color")]]['csp_minimum_phy']=$row[csf("csp_minimum_phy")];
            $data_array[$row[csf("prod_id")]][$row[csf("color")]]['csp_actual_phy']=$row[csf("csp_actual_phy")];
            $data_array[$row[csf("prod_id")]][$row[csf("color")]]['thin_yarn_phy']=$row[csf("thin_yarn_phy")];
            $data_array[$row[csf("prod_id")]][$row[csf("color")]]['thick_phy']=$row[csf("thick_phy")];
            $data_array[$row[csf("prod_id")]][$row[csf("color")]]['u_phy']=$row[csf("u_phy")];
            $data_array[$row[csf("prod_id")]][$row[csf("color")]]['cv_phy']=$row[csf("cv_phy")];
            $data_array[$row[csf("prod_id")]][$row[csf("color")]]['neps_per_km_phy']=$row[csf("neps_per_km_phy")];
            $data_array[$row[csf("prod_id")]][$row[csf("color")]]['heariness_phy']=$row[csf("heariness_phy")];
            $data_array[$row[csf("prod_id")]][$row[csf("color")]]['counts_cv_phy']=$row[csf("counts_cv_phy")];
            $data_array[$row[csf("prod_id")]][$row[csf("color")]]['actual_yarn_comp_phy']=$row[csf("actual_yarn_comp_phy")];
            $data_array[$row[csf("prod_id")]][$row[csf("color")]]['pilling_phy']=$row[csf("pilling_phy")];
            $data_array[$row[csf("prod_id")]][$row[csf("color")]]['brusting_phy']=$row[csf("brusting_phy")];
            $data_array[$row[csf("prod_id")]][$row[csf("color")]]['system_result']=$row[csf("system_result")];
            $data_array[$row[csf("prod_id")]][$row[csf("color")]]['rcv_qnty'] +=$row[csf("cons_quantity")];
            $data_array[$row[csf("prod_id")]][$row[csf("color")]]['comments_author'] =$row[csf("comments_author")];
            $data_array[$row[csf("prod_id")]][$row[csf("color")]]['comments_knit'] =$row[csf("comments_knit")];
            $data_array[$row[csf("prod_id")]][$row[csf("color")]]['comments_dye'] =$row[csf("comments_dye")];

            if($bookingIdChk[$row[csf('yarn_test_id')]] == "")
            {
                $bookingIdChk[$row[csf('yarn_test_id')]] = $row[csf('yarn_test_id')]; 
                $all_yarn_test_id_arr[$row[csf("yarn_test_id")]] = $row[csf("yarn_test_id")];
            }
        }

        // echo "<pre>";
        // print_r($data_array); 

        if(!empty($all_yarn_test_id_arr))
        {	
            fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 1990, 1,$all_yarn_test_id_arr, $empty_arr);
            

            $sql_dtls_knit = "SELECT a.id, a.prod_id, a.color, b.id as dtls_id, b.testing_parameters, b.fabric_point, b.result, b.acceptance, b.fabric_class, 
            b.remarks, b.fab_type, b.testing_parameters_id from inv_yarn_test_mst a,  inv_yarn_test_dtls b, GBL_TEMP_ENGINE c where a.id=b.mst_id and 
            b.fab_type in(1,2) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=c.ref_val and c.user_id=$user_id and c.entry_form=1990";
            //echo $sql_dtls_knit;die;
            $sql_dtls_rslt_knit = sql_select($sql_dtls_knit);
    
            $phy_kniting_data_array= array();
            $phy_dye_data_array= array();
            foreach($sql_dtls_rslt_knit as $row)
            {
                if ($row[csf('fab_type')]==1)
                {
                    $phy_kniting_data_array[$row[csf("prod_id")]][$row[csf("color")]][$row[csf("testing_parameters_id")]]['acceptance']=$row[csf("acceptance")];
                }
                else
                {
                    $phy_dye_data_array[$row[csf("prod_id")]][$row[csf("color")]][$row[csf("testing_parameters_id")]]['acceptance']=$row[csf("acceptance")];
                }
            }
    
            // echo "<pre>";
            // print_r($phy_dye_data_array); 
        }


        $con = connect();	
        $r_id111=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (1990)");
        if($r_id111)
        {
            oci_commit($con);
        }

        $prod_id_count=array();
        foreach($data_array as $k_prod_id=>$v_prod_id)
        {
            foreach($v_prod_id as $color=> $row)
            {
                $prod_id_count[$k_prod_id]++;
            }
        }



        
        $value_width = 1735+69*80;
        $span = 86;
       
        ob_start();

        
        ?>
        <style>
            .wrd_brk{word-break: break-all;word-wrap: break-word;}          
        </style>
        
        <fieldset style="width:<? echo $value_width + 18; ?>px;">
            <table cellpadding="0" cellspacing="0" width="<? echo $value_width; ?>" style="float: left;">
                <tr>
                    <td align="center" width="100%" colspan="<? echo $span; ?>" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
                </tr>
                <tr>
                    <td align="center" width="100%" colspan="<? echo $span; ?>" style="font-size:16px"><strong><? echo $company_arr[str_replace("'", "", $cbo_company_name)]; ?></strong></td>
                </tr>
                <tr>
                    <td align="center" width="100%" colspan="<? echo $span; ?>" style="font-size:16px"><strong>From Date: <? echo change_date_format(str_replace("'", "", $txt_date_from)); ?> To Date: <? echo change_date_format(str_replace("'", "", $txt_date_to)); ?></strong></td>
                </tr>
            </table>

            <div style="width:<? echo $value_width + 18?>px;">
				<table width="<? echo $value_width; ?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" style="" align="left">
					<thead>
                        <tr>
                            <th width="35" rowspan="2">SL</th>
							<th width="80" rowspan="2">Prod.ID</th>
							<th width="100" rowspan="2">Lot</th>
							<th width="60" rowspan="2">Count</th>
							<th width="100" rowspan="2">Composition</th>
							<th width="70" rowspan="2">Color</th>
							<th width="100" rowspan="2">Yarn Type</th>
                            <th width="100" rowspan="2">Supplier</th>
                            <th width="80" rowspan="2">Receive Qty</th>
                            <th width="80" rowspan="2">Test For</th>
                            <th width="100" rowspan="2">Color Range</th>
                            <th width="80" rowspan="2">QC Date</th>
                            <th width="100" rowspan="2" style="background: #A9D08E;">QC Status</th>
                            <th width="100" rowspan="2">Authorize Dept Approval</th>
                            <th width="150" rowspan="2">Comments</th>
                            <th width="100" rowspan="2">Knitting Dept. Approval</th>
                            <th width="100" rowspan="2">Dyeing/Finishing Dept. Approval</th>
                            <th width="200" rowspan="2">Remarks</th>
                            <th width="1440" colspan="18" style="background: #9BC2E6;">Numerical Test Knitting</th>
                            <th width="1920" colspan="24" style="background: #FFD966;">Physical Test for Knitting</th>
                            <th width="2160" colspan="27" style="background: #A9D08E;">Physical Test for Dyeing And Finishing</th>
                        </tr>
						<tr height="100">
                            
                            <th width="80" style="vertical-align:middle"><div class="rotate_90_deg">Actual Yarn Count </div></th>
                            <th width="80" style="vertical-align:middle"><div class="rotate_90_deg">Yarn Appearance (Grade) </div></th>
                            <th width="80" style="vertical-align:middle"><div class="rotate_90_deg">Twist Per Inch (TPI) </div></th>
                            <th width="80" style="vertical-align:middle"><div class="rotate_90_deg">Moisture Content </div></th>
                            <th width="80" style="vertical-align:middle"><div class="rotate_90_deg">IPI Value (Uster) </div></th>
                            <th width="80" style="vertical-align:middle"><div class="rotate_90_deg">CSP Minimum </div></th>
                            <th width="80" style="vertical-align:middle"><div class="rotate_90_deg">CSP Actual </div></th>
                            <th width="80" style="vertical-align:middle"><div class="rotate_90_deg">Thin Yarn </div></th>
                            <th width="80" style="vertical-align:middle"><div class="rotate_90_deg">Thick </div></th>
                            <th width="80" style="vertical-align:middle"><div class="rotate_90_deg">U % </div></th>
                            <th width="80" style="vertical-align:middle"><div class="rotate_90_deg">CV % </div></th>
                            <th width="80" style="vertical-align:middle"><div class="rotate_90_deg">Neps Per KM </div></th>
                            <th width="80" style="vertical-align:middle"><div class="rotate_90_deg">Heariness % </div></th>
                            <th width="80" style="vertical-align:middle"><div class="rotate_90_deg">Counts CV % </div></th>
                            <th width="80" style="vertical-align:middle"><div class="rotate_90_deg">Actual Yarn Composition </div></th>
                            <th width="80" style="vertical-align:middle"><div class="rotate_90_deg">Pilling Test </div></th>
                            <th width="80" style="vertical-align:middle"><div class="rotate_90_deg">Brusting Test </div></th>
                            <th width="80" style="vertical-align:middle"><div class="rotate_90_deg">System Result </div></th>

                            <th width="80" style="vertical-align:middle"><div class="rotate_90_deg">Stripe(Patta) </div></th>
                            <th width="80" style="vertical-align:middle"><div class="rotate_90_deg">Thick & Thin Yarn </div></th>
                            <th width="80" style="vertical-align:middle"><div class="rotate_90_deg">Neps </div></th>
                            <th width="80" style="vertical-align:middle"><div class="rotate_90_deg">Poly-Propaline(Plastic Conta) </div></th>
                            <th width="80" style="vertical-align:middle"><div class="rotate_90_deg">Color Conta/Yarn </div></th>
                            <th width="80" style="vertical-align:middle"><div class="rotate_90_deg">Dead Fiber </div></th>
                            <th width="80" style="vertical-align:middle"><div class="rotate_90_deg">No Of Slub </div></th>
                            <th width="80" style="vertical-align:middle"><div class="rotate_90_deg">No Of Hole </div></th>
                            <th width="80" style="vertical-align:middle"><div class="rotate_90_deg">No Of Slub Hole </div></th>
                            <th width="80" style="vertical-align:middle"><div class="rotate_90_deg">Moisture Efect </div></th>
                            <th width="80" style="vertical-align:middle"><div class="rotate_90_deg">No Of Yarn Breakage </div></th>
                            <th width="80" style="vertical-align:middle"><div class="rotate_90_deg">No Of Setup </div></th>
                            <th width="80" style="vertical-align:middle"><div class="rotate_90_deg">Knotting End </div></th>
                            <th width="80" style="vertical-align:middle"><div class="rotate_90_deg">Haireness </div></th>
                            <th width="80" style="vertical-align:middle"><div class="rotate_90_deg">Hand Feel </div></th>
                            <th width="80" style="vertical-align:middle"><div class="rotate_90_deg">Twisting </div></th>
                            <th width="80" style="vertical-align:middle"><div class="rotate_90_deg">Contamination </div></th>
                            <th width="80" style="vertical-align:middle"><div class="rotate_90_deg">Foregin Fiber </div></th>
                            <th width="80" style="vertical-align:middle"><div class="rotate_90_deg">Oil Stain Yarn </div></th>
                            <th width="80" style="vertical-align:middle"><div class="rotate_90_deg">Foreign Matters </div></th>
                            <th width="80" style="vertical-align:middle"><div class="rotate_90_deg">Unlevel </div></th>
                            <th width="80" style="vertical-align:middle"><div class="rotate_90_deg">Double Yarn </div></th>
                            <th width="80" style="vertical-align:middle"><div class="rotate_90_deg">Fiber Migration </div></th>
                            <th width="80" style="vertical-align:middle"><div class="rotate_90_deg">Excessive Hard Yarn </div></th>
                            <th width="80" style="vertical-align:middle"><div class="rotate_90_deg">Stripe(Patta) </div></th>
                            <th width="80" style="vertical-align:middle"><div class="rotate_90_deg">Thick & Thin Yarn </div></th>
                            <th width="80" style="vertical-align:middle"><div class="rotate_90_deg">Neps </div></th>
                            <th width="80" style="vertical-align:middle"><div class="rotate_90_deg">Color Conta </div></th>
                            <th width="80" style="vertical-align:middle"><div class="rotate_90_deg">Dead Fiber/Cotton </div></th>
                            <th width="80" style="vertical-align:middle"><div class="rotate_90_deg">No Of Slub </div></th>
                            <th width="80" style="vertical-align:middle"><div class="rotate_90_deg">No Of Hole </div></th>
                            <th width="80" style="vertical-align:middle"><div class="rotate_90_deg">No Of Slub Hole </div></th>
                            <th width="80" style="vertical-align:middle"><div class="rotate_90_deg">Moisture Efect </div></th>
                            <th width="80" style="vertical-align:middle"><div class="rotate_90_deg">Shrinkage </div></th>
                            <th width="80" style="vertical-align:middle"><div class="rotate_90_deg">Dye Pick Up% </div></th>
                            <th width="80" style="vertical-align:middle"><div class="rotate_90_deg">Enzyme Dosting % </div></th>
                            <th width="80" style="vertical-align:middle"><div class="rotate_90_deg">Knotting End </div></th>
                            <th width="80" style="vertical-align:middle"><div class="rotate_90_deg">Haireness </div></th>
                            <th width="80" style="vertical-align:middle"><div class="rotate_90_deg">Hand Feel </div></th>
                            <th width="80" style="vertical-align:middle"><div class="rotate_90_deg">Contamination </div></th>
                            <th width="80" style="vertical-align:middle"><div class="rotate_90_deg">Soft Yarn/Loose Yarn </div></th>
                            <th width="80" style="vertical-align:middle"><div class="rotate_90_deg">Oil Stain Yarn </div></th>
                            <th width="80" style="vertical-align:middle"><div class="rotate_90_deg">Bad Piecing </div></th>
                            <th width="80" style="vertical-align:middle"><div class="rotate_90_deg">Oily Slub </div></th>
                            <th width="80" style="vertical-align:middle"><div class="rotate_90_deg">Foreign Matters </div></th>
                            <th width="80" style="vertical-align:middle"><div class="rotate_90_deg">Black Specks Test </div></th>
                            <th width="80" style="vertical-align:middle"><div class="rotate_90_deg">Cotton Seeds Test </div></th>
                            <th width="80" style="vertical-align:middle"><div class="rotate_90_deg">Bursting </div></th>
                            <th width="80" style="vertical-align:middle"><div class="rotate_90_deg">Pilling </div></th>
                            <th width="80" style="vertical-align:middle"><div class="rotate_90_deg">Lustre </div></th>
							<th width="" style="vertical-align:middle"><div class="rotate_90_deg">Process loss % </div>
													
						</tr>
					</thead>
				<!-- </table>
			  	<style> 
					.breakAll{
						word-break:break-all;
						word-wrap: break-word;
					}
				</style>
				<div style="width:<? //echo $value_width + 18?>px; float:left; max-height:400px; overflow-y:scroll" id="scroll_body">
					<table width="<? //echo $value_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_body" style="float:left;"> -->
						<tbody id="scroll_body">
                        <?
                        $i=1;
                        foreach($data_array as $k_prod_id=>$v_prod_id)
                        {
                            foreach($v_prod_id as $color=> $row)
                            {
                                if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF"; 

                                $prod_id_span =  $prod_id_count[$k_prod_id];

                                $compositionDetails = $composition[$row["yarn_comp_type1st"]] . " " . $row["yarn_comp_percent1st"] . "%\n";
                                if ($row[csf("yarn_comp_type2nd")] != 0)
                                    $compositionDetails .= $composition[$row["yarn_comp_type2nd"]] . " " . $row["yarn_comp_percent2nd"] . "%";

                                ?>
                                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>');" id="tr_2nd<? echo $i; ?>">
                           
                                <?
                                if(!in_array($k_prod_id,$prodId_chk))
                                {
                                    $prodId_chk[]=$k_prod_id;
                                    ?>
                                    <td width="35" rowspan="<? echo $prod_id_span ;?>" valign="middle"><? echo $i;?></td>
                                    <td width="80" rowspan="<? echo $prod_id_span ;?>" valign="middle"><? echo $k_prod_id;?></td>
                                    <td width="100" rowspan="<? echo $prod_id_span ;?>" valign="middle"><? echo $row["lot"];?></td>
                                    <td width="60" rowspan="<? echo $prod_id_span ;?>" valign="middle"><? echo $count_arr[$row["yarn_count_id"]];?></td>
                                    <td width="100" rowspan="<? echo $prod_id_span ;?>" valign="middle"><? echo $compositionDetails;?></td>
                                    <td width="70" rowspan="<? echo $prod_id_span ;?>" valign="middle"><? echo $color_arr[$row["yarn_color"]];?></td>
                                    <td width="100" rowspan="<? echo $prod_id_span ;?>" valign="middle"><? echo $yarn_type[$row["yarn_type"]];?></td>
                                    <td width="100" rowspan="<? echo $prod_id_span ;?>" valign="middle"><? echo $supplier_arr[$row["supplier_id"]];?> </td>
                                    <td width="80" rowspan="<? echo $prod_id_span ;?>" valign="middle" align="right"><? echo number_format($row["rcv_qnty"],2);?> </td>
                                    

                                <? } ?>
                                    <td width="80" class="breakAll"> <? echo  $yarn_test_for_arr[$row["test_for"]];?></td>
                                    <td width="100" class="breakAll"><? echo $color_range[$color];?></td>
                                    <td width="80" class="breakAll"><? echo $row["test_date"];?></td>
                                    <td width="100" class="breakAll">
                                        <? 
                                        if($row["comments_author_acceptance"] == 1 || $row["comments_author_acceptance"] == 2 || $row["comments_author_acceptance"] == 3)
                                        {
                                            echo "QC Pass";
                                        }
                                        else
                                        {
                                            echo "Reject";
                                        }
                                        ?>
                                    </td>
                                    <td width="100" class="breakAll"><? echo $comments_acceptance_arr[$row["comments_author_acceptance"]];?></td>
                                    <td width="150" class="breakAll"><? echo $row["comments_author"];?> </td>
                                    <td width="100" class="breakAll"><? echo $comments_acceptance_arr[$row["comments_knit_acceptance"]];?></td>
                                    <td width="100" class="breakAll"><? echo $comments_acceptance_arr[$row["comments_dye_acceptance"]];?></td>
                                    <td width="200" class="breakAll">
                                        <? 
                                        if($row["comments_dye"])
                                        {
                                            echo $row["comments_knit"].','.$row["comments_dye"];
                                        }
                                        else
                                        {
                                            echo $row["comments_knit"];
                                        }
                                        
                                        ?> 
                                    </td>
                                    <td width="80" class="breakAll"><? echo $row["actual_yarn_count_phy"];?></td>
                                    <td width="80" class="breakAll"><? echo $row["yarn_apperance_phy"];?></td>
                                    <td width="80" class="breakAll"><? echo $row["twist_per_inc_phy"];?></td>
                                    <td width="80" class="breakAll"><? echo $row["moisture_content_phy"];?> </td>
                                    <td width="80" class="breakAll"><? echo $row["ipi_value_phy"];?></td>
                                    <td width="80" class="breakAll"><? echo $row["csp_minimum_phy"];?></td>
                                    <td width="80" class="breakAll"><? echo $row["csp_actual_phy"];?></td>
                                    <td width="80" class="breakAll"><? echo $row["thin_yarn_phy"];?></td>
                                    <td width="80" class="breakAll"><? echo $row["thick_phy"];?></td>
                                    <td width="80" class="breakAll"><? echo $row["u_phy"];?></td>
                                    <td width="80" class="breakAll"><? echo $row["cv_phy"];?></td>
                                    <td width="80" class="breakAll"><? echo $row["neps_per_km_phy"];?></td>
                                    <td width="80" class="breakAll"><? echo $row["heariness_phy"];?></td>
                                    <td width="80" class="breakAll"><? echo $row["counts_cv_phy"];?></td>
                                    <td width="80" class="breakAll"><? echo $row["actual_yarn_comp_phy"];?></td>
                                    <td width="80" class="breakAll"><? echo $row["pilling_phy"];?></td>
                                    <td width="80" class="breakAll"><? echo $row["brusting_phy"];?></td>
                                    <td width="80" class="breakAll"><? echo $row["system_result"];?> </td>
                                    <td width="80" >
                                        <? 
                                        if($phy_kniting_data_array[$k_prod_id][$color][1]['acceptance'] !='')
                                        {
                                            echo $yes_no[$phy_kniting_data_array[$k_prod_id][$color][1]['acceptance']];
                                        }
                                        else
                                        {
                                            echo '&nbsp;';
                                        }
                                        ?>
                                    </td>
                                    <td width="80" >
                                        <? 
                                        if($phy_kniting_data_array[$k_prod_id][$color][2]['acceptance'] !='')
                                        {
                                            echo $yes_no[$phy_kniting_data_array[$k_prod_id][$color][2]['acceptance']];
                                        }
                                        else
                                        {
                                            echo '&nbsp;';
                                        }
                                        ?> 
                                    </td>
                                    <td width="80" >
                                        <? 
                                        if($phy_kniting_data_array[$k_prod_id][$color][3]['acceptance'] !='')
                                        {
                                            echo $yes_no[$phy_kniting_data_array[$k_prod_id][$color][3]['acceptance']];
                                        }
                                        else
                                        {
                                            echo '&nbsp;';
                                        }
                                        ?> 
                                    </td>
                                    <td width="80" >
                                        <? 
                                        if($phy_kniting_data_array[$k_prod_id][$color][4]['acceptance'] !='')
                                        {
                                            echo $yes_no[$phy_kniting_data_array[$k_prod_id][$color][4]['acceptance']];
                                        }
                                        else
                                        {
                                            echo '&nbsp;';
                                        }
                                        ?> 
                                    </td>
                                    <td width="80" >
                                        <? 
                                        if($phy_kniting_data_array[$k_prod_id][$color][5]['acceptance'] !='')
                                        {
                                            echo $yes_no[$phy_kniting_data_array[$k_prod_id][$color][5]['acceptance']];
                                        }
                                        else
                                        {
                                            echo '&nbsp;';
                                        }
                                        ?> 
                                    </td>
                                    <td width="80" >
                                        <? 
                                        if($phy_kniting_data_array[$k_prod_id][$color][6]['acceptance'] !='')
                                        {
                                            echo $yes_no[$phy_kniting_data_array[$k_prod_id][$color][6]['acceptance']];
                                        }
                                        else
                                        {
                                            echo '&nbsp;';
                                        }
                                        ?> 
                                    </td>
                                    <td width="80" >
                                        <? 
                                        if($phy_kniting_data_array[$k_prod_id][$color][7]['acceptance'] !='')
                                        {
                                            echo $yes_no[$phy_kniting_data_array[$k_prod_id][$color][7]['acceptance']];
                                        }
                                        else
                                        {
                                            echo '&nbsp;';
                                        }
                                        ?> 
                                    </td>
                                    <td width="80" >
                                        <? 
                                        if($phy_kniting_data_array[$k_prod_id][$color][8]['acceptance'] !='')
                                        {
                                            echo $yes_no[$phy_kniting_data_array[$k_prod_id][$color][8]['acceptance']];
                                        }
                                        else
                                        {
                                            echo '&nbsp;';
                                        }
                                        ?> 
                                    </td>
                                    <td width="80" >
                                        <? 
                                        if($phy_kniting_data_array[$k_prod_id][$color][9]['acceptance'] !='')
                                        {
                                            echo $yes_no[$phy_kniting_data_array[$k_prod_id][$color][9]['acceptance']];
                                        }
                                        else
                                        {
                                            echo '&nbsp;';
                                        }
                                        ?> 
                                    </td>
                                    <td width="80" >
                                        <? 
                                        if($phy_kniting_data_array[$k_prod_id][$color][10]['acceptance'] !='')
                                        {
                                            echo $yes_no[$phy_kniting_data_array[$k_prod_id][$color][10]['acceptance']];
                                        }
                                        else
                                        {
                                            echo '&nbsp;';
                                        }
                                        ?>
                                    </td>
                                    <td width="80" >
                                        <? 
                                        if($phy_kniting_data_array[$k_prod_id][$color][11]['acceptance'] !='')
                                        {
                                            echo $yes_no[$phy_kniting_data_array[$k_prod_id][$color][11]['acceptance']];
                                        }
                                        else
                                        {
                                            echo '&nbsp;';
                                        }
                                        ?>
                                    </td>
                                    <td width="80" >
                                        <? 
                                        if($phy_kniting_data_array[$k_prod_id][$color][12]['acceptance'] !='')
                                        {
                                            echo $yes_no[$phy_kniting_data_array[$k_prod_id][$color][12]['acceptance']];
                                        }
                                        else
                                        {
                                            echo '&nbsp;';
                                        }
                                        ?>
                                    </td>
                                    <td width="80" >
                                        <? 
                                        if($phy_kniting_data_array[$k_prod_id][$color][13]['acceptance'] !='')
                                        {
                                            echo $yes_no[$phy_kniting_data_array[$k_prod_id][$color][13]['acceptance']];
                                        }
                                        else
                                        {
                                            echo '&nbsp;';
                                        }
                                        ?>
                                    </td>
                                    <td width="80" >
                                        <? 
                                        if($phy_kniting_data_array[$k_prod_id][$color][14]['acceptance'] !='')
                                        {
                                            echo $yes_no[$phy_kniting_data_array[$k_prod_id][$color][14]['acceptance']];
                                        }
                                        else
                                        {
                                            echo '&nbsp;';
                                        }
                                        ?>
                                    </td>
                                    <td width="80" >
                                        <? 
                                        if($phy_kniting_data_array[$k_prod_id][$color][15]['acceptance'] !='')
                                        {
                                            echo $yes_no[$phy_kniting_data_array[$k_prod_id][$color][15]['acceptance']];
                                        }
                                        else
                                        {
                                            echo '&nbsp;';
                                        }
                                        ?>
                                    </td>
                                    <td width="80" >
                                        <? 
                                        if($phy_kniting_data_array[$k_prod_id][$color][16]['acceptance'] !='')
                                        {
                                            echo $yes_no[$phy_kniting_data_array[$k_prod_id][$color][16]['acceptance']];
                                        }
                                        else
                                        {
                                            echo '&nbsp;';
                                        }
                                        ?>
                                    </td>
                                    <td width="80" >
                                        <? 
                                        if($phy_kniting_data_array[$k_prod_id][$color][17]['acceptance'] !='')
                                        {
                                            echo $yes_no[$phy_kniting_data_array[$k_prod_id][$color][17]['acceptance']];
                                        }
                                        else
                                        {
                                            echo '&nbsp;';
                                        }
                                        ?>
                                    </td>
                                    <td width="80" >
                                        <? 
                                        if($phy_kniting_data_array[$k_prod_id][$color][18]['acceptance'] !='')
                                        {
                                            echo $yes_no[$phy_kniting_data_array[$k_prod_id][$color][18]['acceptance']];
                                        }
                                        else
                                        {
                                            echo '&nbsp;';
                                        }
                                        ?>
                                    </td>
                                    <td width="80" >
                                        <? 
                                        if($phy_kniting_data_array[$k_prod_id][$color][19]['acceptance'] !='')
                                        {
                                            echo $yes_no[$phy_kniting_data_array[$k_prod_id][$color][19]['acceptance']];
                                        }
                                        else
                                        {
                                            echo '&nbsp;';
                                        }
                                        ?>
                                    </td>
                                    <td width="80" >
                                        <? 
                                        if($phy_kniting_data_array[$k_prod_id][$color][20]['acceptance'] !='')
                                        {
                                            echo $yes_no[$phy_kniting_data_array[$k_prod_id][$color][20]['acceptance']];
                                        }
                                        else
                                        {
                                            echo '&nbsp;';
                                        }
                                        ?>
                                    </td>
                                    <td width="80" >
                                        <? 
                                        if($phy_kniting_data_array[$k_prod_id][$color][21]['acceptance'] !='')
                                        {
                                            echo $yes_no[$phy_kniting_data_array[$k_prod_id][$color][21]['acceptance']];
                                        }
                                        else
                                        {
                                            echo '&nbsp;';
                                        }
                                        ?>
                                    </td>
                                    <td width="80" >
                                        <? 
                                        if($phy_kniting_data_array[$k_prod_id][$color][22]['acceptance'] !='')
                                        {
                                            echo $yes_no[$phy_kniting_data_array[$k_prod_id][$color][22]['acceptance']];
                                        }
                                        else
                                        {
                                            echo '&nbsp;';
                                        }
                                        ?>
                                    </td>
                                    <td width="80" >
                                        <? 
                                        if($phy_kniting_data_array[$k_prod_id][$color][23]['acceptance'] !='')
                                        {
                                            echo $yes_no[$phy_kniting_data_array[$k_prod_id][$color][23]['acceptance']];
                                        }
                                        else
                                        {
                                            echo '&nbsp;';
                                        }
                                        ?>
                                    </td>
                                    <td width="80" >
                                        <? 
                                        if($phy_kniting_data_array[$k_prod_id][$color][24]['acceptance'] !='')
                                        {
                                            echo $yes_no[$phy_kniting_data_array[$k_prod_id][$color][24]['acceptance']];
                                        }
                                        else
                                        {
                                            echo '&nbsp;';
                                        }
                                        ?>
                                    </td>
                                    <td width="80" >
                                        <? 
                                        if($phy_dye_data_array[$k_prod_id][$color][1]['acceptance'] !='')
                                        {
                                            echo $yes_no[$phy_dye_data_array[$k_prod_id][$color][1]['acceptance']];
                                        }
                                        else
                                        {
                                            echo '&nbsp;';
                                        }
                                        ?>
                                    </td>
                                    <td width="80" >
                                        <? 
                                        if($phy_dye_data_array[$k_prod_id][$color][2]['acceptance'] !='')
                                        {
                                            echo $yes_no[$phy_dye_data_array[$k_prod_id][$color][2]['acceptance']];
                                        }
                                        else
                                        {
                                            echo '&nbsp;';
                                        }
                                        ?>
                                    </td>
                                    <td width="80" >
                                        <? 
                                        if($phy_dye_data_array[$k_prod_id][$color][3]['acceptance'] !='')
                                        {
                                            echo $yes_no[$phy_dye_data_array[$k_prod_id][$color][3]['acceptance']];
                                        }
                                        else
                                        {
                                            echo '&nbsp;';
                                        }
                                        ?>
                                    </td>
                                    <td width="80" >
                                        <? 
                                        if($phy_dye_data_array[$k_prod_id][$color][4]['acceptance'] !='')
                                        {
                                            echo $yes_no[$phy_dye_data_array[$k_prod_id][$color][4]['acceptance']];
                                        }
                                        else
                                        {
                                            echo '&nbsp;';
                                        }
                                        ?>
                                        </td>
                                    <td width="80" >
                                        <? 
                                        if($phy_dye_data_array[$k_prod_id][$color][5]['acceptance'] !='')
                                        {
                                            echo $yes_no[$phy_dye_data_array[$k_prod_id][$color][5]['acceptance']];
                                        }
                                        else
                                        {
                                            echo '&nbsp;';
                                        }
                                        ?>
                                    </td>
                                    <td width="80" >
                                        <? 
                                        if($phy_dye_data_array[$k_prod_id][$color][6]['acceptance'] !='')
                                        {
                                            echo $yes_no[$phy_dye_data_array[$k_prod_id][$color][6]['acceptance']];
                                        }
                                        else
                                        {
                                            echo '&nbsp;';
                                        }
                                        ?>
                                    </td>
                                    <td width="80" >
                                        <? 
                                        if($phy_dye_data_array[$k_prod_id][$color][7]['acceptance'] !='')
                                        {
                                            echo $yes_no[$phy_dye_data_array[$k_prod_id][$color][7]['acceptance']];
                                        }
                                        else
                                        {
                                            echo '&nbsp;';
                                        }
                                        ?>
                                    </td>
                                    <td width="80" >
                                        <? 
                                        if($phy_dye_data_array[$k_prod_id][$color][8]['acceptance'] !='')
                                        {
                                            echo $yes_no[$phy_dye_data_array[$k_prod_id][$color][8]['acceptance']];
                                        }
                                        else
                                        {
                                            echo '&nbsp;';
                                        }
                                        ?>
                                    </td>
                                    <td width="80" >
                                        <? 
                                        if($phy_dye_data_array[$k_prod_id][$color][9]['acceptance'] !='')
                                        {
                                            echo $yes_no[$phy_dye_data_array[$k_prod_id][$color][9]['acceptance']];
                                        }
                                        else
                                        {
                                            echo '&nbsp;';
                                        }
                                        ?>
                                    </td>
                                    <td width="80" >
                                        <? 
                                        if($phy_dye_data_array[$k_prod_id][$color][10]['acceptance'] !='')
                                        {
                                            echo $yes_no[$phy_dye_data_array[$k_prod_id][$color][10]['acceptance']];
                                        }
                                        else
                                        {
                                            echo '&nbsp;';
                                        }
                                        ?>
                                    </td>
                                    <td width="80" >
                                        <? 
                                        if($phy_dye_data_array[$k_prod_id][$color][11]['acceptance'] !='')
                                        {
                                            echo $yes_no[$phy_dye_data_array[$k_prod_id][$color][11]['acceptance']];
                                        }
                                        else
                                        {
                                            echo '&nbsp;';
                                        }
                                        ?>
                                    </td>
                                    <td width="80" >
                                        <? 
                                        if($phy_dye_data_array[$k_prod_id][$color][12]['acceptance'] !='')
                                        {
                                            echo $yes_no[$phy_dye_data_array[$k_prod_id][$color][12]['acceptance']];
                                        }
                                        else
                                        {
                                            echo '&nbsp;';
                                        }
                                        ?>
                                    </td>
                                    <td width="80" >
                                        <? 
                                        if($phy_dye_data_array[$k_prod_id][$color][13]['acceptance'] !='')
                                        {
                                            echo $yes_no[$phy_dye_data_array[$k_prod_id][$color][13]['acceptance']];
                                        }
                                        else
                                        {
                                            echo '&nbsp;';
                                        }
                                        ?>
                                    </td>
                                    <td width="80" >
                                        <? 
                                        if($phy_dye_data_array[$k_prod_id][$color][14]['acceptance'] !='')
                                        {
                                            echo $yes_no[$phy_dye_data_array[$k_prod_id][$color][14]['acceptance']];
                                        }
                                        else
                                        {
                                            echo '&nbsp;';
                                        }
                                        ?>
                                    </td>
                                    <td width="80" >
                                        <? 
                                        if($phy_dye_data_array[$k_prod_id][$color][15]['acceptance'] !='')
                                        {
                                            echo $yes_no[$phy_dye_data_array[$k_prod_id][$color][15]['acceptance']];
                                        }
                                        else
                                        {
                                            echo '&nbsp;';
                                        }
                                        ?>
                                    </td>
                                    <td width="80" >
                                        <? 
                                        if($phy_dye_data_array[$k_prod_id][$color][16]['acceptance'] !='')
                                        {
                                            echo $yes_no[$phy_dye_data_array[$k_prod_id][$color][16]['acceptance']];
                                        }
                                        else
                                        {
                                            echo '&nbsp;';
                                        }
                                        ?>
                                    </td>
                                    <td width="80" >
                                        <? 
                                        if($phy_dye_data_array[$k_prod_id][$color][17]['acceptance'] !='')
                                        {
                                            echo $yes_no[$phy_dye_data_array[$k_prod_id][$color][17]['acceptance']];
                                        }
                                        else
                                        {
                                            echo '&nbsp;';
                                        }
                                        ?>
                                    </td>
                                    <td width="80" >
                                        <? 
                                        if($phy_dye_data_array[$k_prod_id][$color][18]['acceptance'] !='')
                                        {
                                            echo $yes_no[$phy_dye_data_array[$k_prod_id][$color][18]['acceptance']];
                                        }
                                        else
                                        {
                                            echo '&nbsp;';
                                        }
                                        ?>
                                    </td>
                                    <td width="80" >
                                        <? 
                                        if($phy_dye_data_array[$k_prod_id][$color][19]['acceptance'] !='')
                                        {
                                            echo $yes_no[$phy_dye_data_array[$k_prod_id][$color][19]['acceptance']];
                                        }
                                        else
                                        {
                                            echo '&nbsp;';
                                        }
                                        ?>
                                    </td>
                                    <td width="80" >
                                        <? 
                                        if($phy_dye_data_array[$k_prod_id][$color][20]['acceptance'] !='')
                                        {
                                            echo $yes_no[$phy_dye_data_array[$k_prod_id][$color][20]['acceptance']];
                                        }
                                        else
                                        {
                                            echo '&nbsp;';
                                        }
                                        ?>
                                    </td>
                                    <td width="80" >
                                        <? 
                                        if($phy_dye_data_array[$k_prod_id][$color][21]['acceptance'] !='')
                                        {
                                            echo $yes_no[$phy_dye_data_array[$k_prod_id][$color][21]['acceptance']];
                                        }
                                        else
                                        {
                                            echo '&nbsp;';
                                        }
                                        ?>
                                    </td>
                                    <td width="80" >
                                        <? 
                                        if($phy_dye_data_array[$k_prod_id][$color][22]['acceptance'] !='')
                                        {
                                            echo $yes_no[$phy_dye_data_array[$k_prod_id][$color][22]['acceptance']];
                                        }
                                        else
                                        {
                                            echo '&nbsp;';
                                        }
                                        ?>
                                    </td>
                                    <td width="80" >
                                        <? 
                                        if($phy_dye_data_array[$k_prod_id][$color][23]['acceptance'] !='')
                                        {
                                            echo $yes_no[$phy_dye_data_array[$k_prod_id][$color][23]['acceptance']];
                                        }
                                        else
                                        {
                                            echo '&nbsp;';
                                        }
                                        ?>
                                    </td>
                                    <td width="80" >
                                        <? 
                                        if($phy_dye_data_array[$k_prod_id][$color][24]['acceptance'] !='')
                                        {
                                            echo $yes_no[$phy_dye_data_array[$k_prod_id][$color][24]['acceptance']];
                                        }
                                        else
                                        {
                                            echo '&nbsp;';
                                        }
                                        ?>
                                    </td>
                                    <td width="80" >
                                        <? 
                                        if($phy_dye_data_array[$k_prod_id][$color][25]['acceptance'] !='')
                                        {
                                            echo $yes_no[$phy_dye_data_array[$k_prod_id][$color][25]['acceptance']];
                                        }
                                        else
                                        {
                                            echo '&nbsp;';
                                        }
                                        ?>
                                    </td>
                                    <td width="80" >
                                        <? 
                                        if($phy_dye_data_array[$k_prod_id][$color][26]['acceptance'] !='')
                                        {
                                            echo $yes_no[$phy_dye_data_array[$k_prod_id][$color][26]['acceptance']];
                                        }
                                        else
                                        {
                                            echo '&nbsp;';
                                        }
                                        ?>
                                    </td>
                                    <td width="" >
                                        <? 
                                        if($phy_dye_data_array[$k_prod_id][$color][27]['acceptance'] !='')
                                        {
                                            echo $yes_no[$phy_dye_data_array[$k_prod_id][$color][27]['acceptance']];
                                        }
                                        else
                                        {
                                            echo '&nbsp;';
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <?
                            }  
                             
                            $i++;
                        }  
                        ?>   
								
						</tbody>
					</table>
				</div>
			</div>
        </fieldset>   
        <?
    }

    foreach (glob("$user_id*.xls") as $filename) {
        if (@filemtime($filename) < (time() - $seconds_old))
            @unlink($filename);
    }
    //---------end------------//
    $name = time();
    $filename = $user_id . "_" . $name . ".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, ob_get_contents());
    $filename = $user_id . "_" . $name . ".xls";
    echo "$total_data####$filename";
    exit();
}

?>
