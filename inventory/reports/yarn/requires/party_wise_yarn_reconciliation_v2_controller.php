<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//==============================================



if($action=="party_popup")
{
	echo load_html_head_contents("Party Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		// var selected_id = new Array; var selected_name = new Array;
		// function check_all_data()
		// {
		// 	var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
		// 	tbl_row_count = tbl_row_count - 1;

		// 	for( var i = 1; i <= tbl_row_count; i++ )
		// 	{
		// 		$('#tr_'+i).trigger('click'); 
		// 	}
		// }
		
		// function toggle( x, origColor ) {
		// 	var newColor = 'yellow';
		// 	if ( x.style ) {
		// 		x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
		// 	}
		// }
		
		// function js_set_value( str ) {
			
		// 	if (str!="") str=str.split("_");
			 
		// 	toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );
			 
		// 	if( jQuery.inArray( str[1], selected_id ) == -1 ) {
		// 		selected_id.push( str[1] );
		// 		selected_name.push( str[2] );
		// 	}
		// 	else {
		// 		for( var i = 0; i < selected_id.length; i++ ) {
		// 			if( selected_id[i] == str[1] ) break;
		// 		}
		// 		selected_id.splice( i, 1 );
		// 		selected_name.splice( i, 1 );
		// 	}
		// 	var id = ''; var name = '';
		// 	for( var i = 0; i < selected_id.length; i++ ) {
		// 		id += selected_id[i] + ',';
		// 		name += selected_name[i] + ',';
		// 	}
			
		// 	id = id.substr( 0, id.length - 1 );
		// 	name = name.substr( 0, name.length - 1 );
			
		// 	$('#hide_party_id').val( id );
		// 	$('#hide_party_name').val( name );
		// }

		function js_set_value(str)
        {
            var splitData = str.split("_");
			//alert(splitData);
            $("#hide_party_id").val(splitData[1]);
            $("#hide_party_name").val(splitData[2]);
            parent.emailwindow.hide();
        }
    </script>
        <input type="hidden" name="hide_party_name" id="hide_party_name" value="" />
        <input type="hidden" name="hide_party_id" id="hide_party_id" value="" />
	<?

	if ($cbo_knitting_source==3)
	{
		$sql="select a.id, a.supplier_name as party_name from lib_supplier a, lib_supplier_party_type b,lib_supplier_tag_company c where a.id=b.supplier_id and c.supplier_id=b.supplier_id and c.tag_company=$companyID and b.party_type in(1,9,20) and a.status_active=1  group by a.id, a.supplier_name order by a.supplier_name";
	}
	elseif($cbo_knitting_source==1)
	{
		$sql="select id, company_name as party_name from lib_company where status_active=1 and is_deleted=0 order by company_name";
	}

	echo create_list_view("tbl_list_search", "Party Name", "380","380","270",0, $sql , "js_set_value", "id,party_name", "", 1, "0", $arr , "party_name", "",'setFilterGrid("tbl_list_search",-1);','0','',1) ;
	
   exit(); 
} 

if($action=="knitting_production_popup")
{
	echo load_html_head_contents("production Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
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
				name += selected_name[i] + ',';
			}
			
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			$('#hide_production').val(name);
			$('#hide_production_id').val(id);
		}
    </script>
        <input type="hidden" name="hide_production" id="hide_production" value="" />
        <input type="hidden" name="hide_production_id" id="hide_production_id" value="" />
	<?

    $sql = "SELECT a.id, a.recv_number from inv_receive_master a where a.receive_basis = 2 and a.item_category = 13 and a.entry_form = 2 and a.roll_maintained = 1 and a.company_id = ".$companyID." and a.knitting_company = ".$txt_knit_comp_id." and a.knitting_source = ".$cbo_knitting_source." and a.status_active = 1 and a.is_deleted = 0 ";  
    //echo $sql;

	echo create_list_view("tbl_list_search", "Production ID", "380","380","270",0, $sql , "js_set_value", "id,recv_number", "", 1, "0", $arr , "recv_number", "",'setFilterGrid("tbl_list_search",-1);','0','',1) ;
	
   exit(); 
} 

if($action=="report_generate")  // If any one modify this action please same time modify [ $action=="pdf_generate" ]
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name");
	$buyer_arr=return_library_array( "select id, short_name from  lib_buyer", "id", "short_name");
	$brand_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');
	$store_arr=return_library_array( "select id, store_name from lib_store_location", "id", "store_name");
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	
	$type=str_replace("'","",$type);
    $cbo_company_name=str_replace("'","",$cbo_company_name);
    $txt_knitting_com_id=str_replace("'","",$txt_knitting_com_id);
    $cbo_knitting_source=str_replace("'","",$cbo_knitting_source);
    $txt_production_id=str_replace("'","",$txt_production_id);

   // var_dump($cbo_company_name);
	if ($cbo_company_name=="")
		$company_cond_1="";
	else
		$company_cond_1=" AND a.company_id in ($cbo_company_name)";

    
    if ($txt_knitting_com_id=='')
        $party_cond_1="";
    else
        $party_cond_1=" AND a.knitting_company in ($txt_knitting_com_id)";   
        
    //for knitting source condition
	if ($cbo_knitting_source==0)
	{
		$knit_source_cond="";
	}
	else
	{
		$knit_source_cond=" AND a.knitting_source=$cbo_knitting_source";
	} 
    
    if ($txt_production_id=="")
		$production_cond_1="";
	else
		$production_cond_1=" AND a.id in ($txt_production_id)";

   
	//for Show button
	if($type==1)
	{
        /*
        |--------------------------------------------------------------------------
        | for Knitting Production
        |--------------------------------------------------------------------------
        */
        $con = connect();
        $r_id111=execute_query("DELETE FROM TMP_BOOKING_ID WHERE USERID=$user_id ");
		$r_id222=execute_query("DELETE FROM TMP_FEB_DES_ID WHERE USERID=$user_id ");
		$r_id333=execute_query("DELETE FROM TMP_REQS_NO WHERE USERID=$user_id ");
		oci_commit($con);
		disconnect($con);

		$sql_receive_mst = "SELECT a.id as mst_id, a.recv_number, a.receive_date, a.challan_no, a.booking_id, a.booking_no, a.knitting_company, a.receive_basis, a.entry_form, b.grey_receive_qnty, b.febric_description_id, b.yarn_prod_id 
        from inv_receive_master a, pro_grey_prod_entry_dtls b 
        where a.id=b.mst_id AND a.receive_basis = 2 AND a.item_category = 13 AND a.entry_form = 2 AND a.roll_maintained = 1 AND a.company_id = ".$cbo_company_name." AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 ".$knit_source_cond.$party_cond_1.$production_cond_1; 
		//echo $sql_receive_mst;//die;
		$rcv_mst_rslt = sql_select($sql_receive_mst);
		foreach ($rcv_mst_rslt as $row) 
        {
			if($bookingIdChk[$row[csf('booking_id')]] == "")
			{
				$bookingIdChk[$row[csf('booking_id')]] = $row[csf('booking_id')];
				$all_booking_id_arr[$row[csf("booking_id")]] = $row[csf("booking_id")];
			}

			if($febDesIdChk[$row[csf('febric_description_id')]] == "")
			{
				$febDesIdChk[$row[csf('febric_description_id')]] = $row[csf('febric_description_id')];
				$all_febdes_id_arr[$row[csf("febric_description_id")]] = $row[csf("febric_description_id")];
			}
		}

		$all_booking_id_arr = array_filter($all_booking_id_arr);
		if(!empty($all_booking_id_arr))
		{
			$con = connect();
			foreach($all_booking_id_arr as $bookingId)
			{
				execute_query("INSERT INTO TMP_BOOKING_ID(BOOKING_ID,USERID) VALUES(".$bookingId.", ".$user_id.")");
				oci_commit($con);
			}
		}
		//die;
		$all_febdes_id_arr = array_filter($all_febdes_id_arr);
		if(!empty($all_febdes_id_arr))
		{
			$con = connect();
			foreach($all_febdes_id_arr as $febdesId)
			{
				execute_query("INSERT INTO TMP_FEB_DES_ID(FEB_DES_ID,USERID) VALUES(".$febdesId.", ".$user_id.")");
				oci_commit($con);
			}
		}
		//die;
		
		/*
        |--------------------------------------------------------------------------
        | for reqsition  
        |--------------------------------------------------------------------------
        */
		
        $reqsition_sql = "SELECT a.knit_id, a.requisition_no from ppl_yarn_requisition_entry a, tmp_booking_id b 
        where a.knit_id=b.booking_id and b.userid=$user_id and a.status_active=1 and a.is_deleted=0";
        //echo $reqsition_sql;die;
        $reqsition_rslt = sql_select($reqsition_sql);
        $reqChk = array();
        $progInfoArr = array();
        $reqInfoArr = array();
        foreach ($reqsition_rslt as $row) 
        {
            if($reqChk[$row[csf('requisition_no')]] == "")
            {
                $reqChk[$row[csf('requisition_no')]] = $row[csf('requisition_no')];
				$all_req_no_arr[$row[csf("requisition_no")]] = $row[csf("requisition_no")];
            }
            $progInfoArr[$row[csf('requisition_no')]]['knit_id'] = $row[csf('knit_id')];
            $reqInfoArr[$row[csf('knit_id')]]['requisition_no'] = $row[csf('requisition_no')];
        }
        unset($reqsition_rslt);

		$all_req_no_arr = array_filter($all_req_no_arr);
		if(!empty($all_req_no_arr))
		{
			$con = connect();
			foreach($all_req_no_arr as $reqNo)
			{
				execute_query("INSERT INTO TMP_REQS_NO(REQS_NO,USERID) VALUES(".$reqNo.", ".$user_id.")");
				oci_commit($con);
			}
		}
		//die;

		/*
        |--------------------------------------------------------------------------
        | for determination  
        |--------------------------------------------------------------------------
        */
		// echo "select a.id, b.count_id,b.type_id, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b, tmp_feb_des_id c where a.id=b.mst_id and a.id=c.feb_des_id and c.userid=$user_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ";die;
		$sql_determination = sql_select("select a.id, b.count_id,b.type_id, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b, tmp_feb_des_id c where a.id=b.mst_id and a.id=c.feb_des_id and c.userid=$user_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ");
    		
		$determination_arr = array();
		foreach ($sql_determination as $row) 
		{
			$determination_arr[$row[csf('id')]][$row[csf('count_id')]][$row[csf('copmposition_id')]][$row[csf('type_id')]] = $row[csf('percent')];
		}

        /*
        |--------------------------------------------------------------------------
        | for issue  
        |--------------------------------------------------------------------------
        */
        
        $sql_issue = "SELECT a.knit_dye_company as knitting_company, a.issue_number, a.issue_date, a.challan_no, c.id as prod_id, c.lot, c.brand, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_comp_type2nd, c.yarn_comp_percent2nd, c.yarn_type, c.color, b.cons_quantity, b.cons_rate, b.cons_amount, b.requisition_no 
        from inv_issue_master a, inv_transaction b, product_details_master c, tmp_reqs_no d 
        where a.id=b.mst_id and b.prod_id=c.id and b.requisition_no=d.reqs_no and d.userid=$user_id and a.company_id = ".$cbo_company_name." and a.entry_form=3 and a.item_category=1 and b.transaction_type=2 and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ";
        //echo $sql_issue;//die;

        $sql_issue_rslt=sql_select($sql_issue);
      
        $issueInfoArr = array();
        $issueDataArr = array();
        $issueInfoSummeryArr = array();
        $issuePercentageInfoArr = array();
		$yarn_percentage=0;
        foreach($sql_issue_rslt as $row)
        {
            $issueInfoArr[$row[csf('knitting_company')]][$row[csf('prod_id')]][$row[csf('requisition_no')]]['yarn_count_id'] = $row[csf('yarn_count_id')];
            $issueInfoArr[$row[csf('knitting_company')]][$row[csf('prod_id')]][$row[csf('requisition_no')]]['yarn_comp_type1st'] = $row[csf('yarn_comp_type1st')];
            $issueInfoArr[$row[csf('knitting_company')]][$row[csf('prod_id')]][$row[csf('requisition_no')]]['yarn_comp_percent1st'] = $row[csf('yarn_comp_percent1st')];
            $issueInfoArr[$row[csf('knitting_company')]][$row[csf('prod_id')]][$row[csf('requisition_no')]]['yarn_comp_type2nd'] = $row[csf('yarn_comp_type2nd')];
            $issueInfoArr[$row[csf('knitting_company')]][$row[csf('prod_id')]][$row[csf('requisition_no')]]['yarn_comp_percent2nd'] = $row[csf('yarn_comp_percent2nd')];
            $issueInfoArr[$row[csf('knitting_company')]][$row[csf('prod_id')]][$row[csf('requisition_no')]]['yarn_type'] = $row[csf('yarn_type')];
            $issueInfoArr[$row[csf('knitting_company')]][$row[csf('prod_id')]][$row[csf('requisition_no')]]['color'] = $row[csf('color')];
            $issueInfoArr[$row[csf('knitting_company')]][$row[csf('prod_id')]][$row[csf('requisition_no')]]['lot'] = $row[csf('lot')];
            $issueInfoArr[$row[csf('knitting_company')]][$row[csf('prod_id')]][$row[csf('requisition_no')]]['brand'] = $row[csf('brand')];
            $issueInfoArr[$row[csf('knitting_company')]][$row[csf('prod_id')]][$row[csf('requisition_no')]]['issue_qty'] += $row[csf('cons_quantity')];
            $issueInfoArr[$row[csf('knitting_company')]][$row[csf('prod_id')]][$row[csf('requisition_no')]]['rate'] = $row[csf('cons_rate')];
            $issueInfoArr[$row[csf('knitting_company')]][$row[csf('prod_id')]][$row[csf('requisition_no')]]['issue_value'] += $row[csf('cons_amount')];

			$issueDataArr[$row[csf('knitting_company')]][$row[csf('prod_id')]][$row[csf('requisition_no')]]['rate'] = $row[csf('cons_rate')];

			// ------------- Issue Details Summery -----------------------

			$issueInfoSummeryArr[$row[csf('knitting_company')]][$row[csf('issue_number')]]['issue_date'] = $row[csf('issue_date')];
			$issueInfoSummeryArr[$row[csf('knitting_company')]][$row[csf('issue_number')]]['challan_no'] = $row[csf('challan_no')];
			$issueInfoSummeryArr[$row[csf('knitting_company')]][$row[csf('issue_number')]]['issue_qty'] += $row[csf('cons_quantity')];
			$issueInfoSummeryArr[$row[csf('knitting_company')]][$row[csf('issue_number')]]['issue_value'] += $row[csf('cons_amount')];

			
			$issuePercentageInfoArr[$row[csf('knitting_company')]][$row[csf('prod_id')]][$row[csf('requisition_no')]]['yarn_des'] .=$row[csf('yarn_count_id')].'**'.$row[csf('yarn_comp_type1st')].'**'.$row[csf('yarn_type')].',';
            
        }
        unset($sql_issue_rslt);
       //echo "<pre>";print_r($issuePercentageInfoArr);echo "</pre>";


        /*
        |--------------------------------------------------------------------------
        | for issue return 
        |--------------------------------------------------------------------------
        */
        $sql_iss_rtn = "SELECT a.id, a.recv_number, a.receive_date, a.challan_no, a.booking_id, a.booking_no,a.knitting_company, a.receive_basis, a.entry_form, b.item_category, b.cons_quantity, b.cons_amount,b.prod_id from inv_receive_master a, inv_transaction b, tmp_reqs_no c where a.id=b.mst_id and a.booking_id=c.reqs_no and c.userid=$user_id and a.receive_basis = 3 and a.item_category = 1 and a.entry_form = 9 and a.company_id = ".$cbo_company_name." and b.item_category = 1 and b.transaction_type = 4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ";
        //echo $sql_iss_rtn; die;
        $sql_iss_rtn_rslt=sql_select($sql_iss_rtn);
      
        $issueRtnArr = array();
        $issueRtnSummeryArr = array();
        foreach($sql_iss_rtn_rslt as $row)
        {
            /*
            |--------------------------------------------------------------------------
            | for Yarn Issue Return
            | if receive_basis = 3(Requisition) and
            | entry_form = 9(Yarn Issue Return) and
            | item_category = 1(Yarn) then
            | tbl inv_receive_master booking_id/booking_no = requisition_no
            |--------------------------------------------------------------------------
            */
           
            $issueRtnArr[$row[csf('knitting_company')]][$row[csf('prod_id')]][$row[csf('booking_id')]]['issue_rtn_qty'] += $row[csf('cons_quantity')];
            $issueRtnArr[$row[csf('knitting_company')]][$row[csf('prod_id')]][$row[csf('booking_id')]]['issue_rtn_value'] += $row[csf('cons_amount')];

			// ------------- Issue Return Details Summery -----------------------

			$issueRtnSummeryArr[$row[csf('knitting_company')]][$row[csf('recv_number')]]['receive_date'] = $row[csf('receive_date')];
			$issueRtnSummeryArr[$row[csf('knitting_company')]][$row[csf('recv_number')]]['challan_no'] = $row[csf('challan_no')];
			$issueRtnSummeryArr[$row[csf('knitting_company')]][$row[csf('recv_number')]]['issue_rtn_qty'] += $row[csf('cons_quantity')];
			$issueRtnSummeryArr[$row[csf('knitting_company')]][$row[csf('recv_number')]]['issue_rtn_value'] += $row[csf('cons_amount')];
               
            
        }
        unset($sql_iss_rtn_rslt);
		//echo "<pre>";print_r($issueRtnSummeryArr);echo "</pre>";
		
		// echo "select a.mst_id, a.process_loss, a.rate, a.process_id from conversion_process_loss a, tmp_feb_des_id b where a.mst_id=b.feb_des_id and b.userid=$user_id and a.process_id in (1,3,4) and a.status_active=1 and a.is_deleted=0";die;
		$processloss_sql = sql_select("select a.mst_id, a.process_loss, a.rate, a.process_id from conversion_process_loss a, tmp_feb_des_id b where a.mst_id=b.feb_des_id and b.userid=$user_id and a.process_id in (1,3,4) and a.status_active=1 and a.is_deleted=0");
		$process_lossArr = array();
    	foreach ($processloss_sql as $row) 
    	{
			$process_lossArr[$row[csf('mst_id')]]['process_loss'] += $row[csf('process_loss')];
    	}

		/*
        |--------------------------------------------------------------------------
        | for Receive Details Summery  
        |--------------------------------------------------------------------------
        */
		

        $sql_material_rcv = "SELECT a.id as mst_id, a.recv_number, a.receive_date, a.challan_no, a.booking_id, a.booking_no, a.knitting_company, a.receive_basis, a.entry_form,c.used_qty, c.prod_id 
        from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_material_used_dtls c 
        where a.id=b.mst_id and a.id=c.mst_id AND b.id=c.DTLS_ID AND a.receive_basis = 2 AND a.item_category = 13 AND a.entry_form = 2 AND a.roll_maintained = 1 AND a.company_id = ".$cbo_company_name." and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 ".$knit_source_cond.$party_cond_1.$production_cond_1; 
        
        //echo $sql_material_rcv;//die;
        $rcv_material_rslt = sql_select($sql_material_rcv);
        $rcvmatInfoArr = array();
		$rcvInfoSummeryArr = array();
        foreach ($rcv_material_rslt as $row) 
        {
            $rcvmatInfoArr[$row[csf('knitting_company')]][$row[csf('prod_id')]][$row[csf('booking_id')]]['used_qty'] += $row[csf('used_qty')];
			$rcvmatInfoSummeryArr[$row[csf('knitting_company')]][$row[csf('recv_number')]][$row[csf('prod_id')]][$row[csf('booking_id')]]['used_qty'] += $row[csf('used_qty')];
			$rcvmatInfoSummeryArr[$row[csf('knitting_company')]][$row[csf('recv_number')]][$row[csf('prod_id')]][$row[csf('booking_id')]]['used_value'] += $row[csf('used_qty')]*$issueInfoArr[$row[csf('knitting_company')]][$row[csf('prod_id')]][$reqInfoArr[$row[csf('booking_id')]]['requisition_no']]['rate'];

			$check_booking_id_arr[] = $row[csf('booking_id')];
        }
        unset($rcv_material_rslt);
        //echo "<pre>";print_r($rcvmatInfoArrr);echo "</pre>";


		foreach ($rcv_mst_rslt as $row) 
        {
			if(strpos($row[csf("yarn_prod_id")], ",")==true)
			{
				$multi_prod_id=explode(",",$row[csf("yarn_prod_id")]);
				foreach($multi_prod_id as $m_prod_id)
				{ 
					
					$rcvInfoSummeryArr[$row[csf('knitting_company')]][$row[csf('recv_number')]]['receive_date']= $row[csf('receive_date')];
					$rcvInfoSummeryArr[$row[csf('knitting_company')]][$row[csf('recv_number')]]['challan_no'] = $row[csf('challan_no')];

					if (in_array($row[csf("booking_id")], $check_booking_id_arr)) 
					{
						$rcvInfoArr[$row[csf('knitting_company')]][$m_prod_id][$row[csf('booking_id')]]['used_qty'] = $rcvmatInfoArr[$row[csf('knitting_company')]][$m_prod_id][$row[csf('booking_id')]]['used_qty'];
						$rcvInfoSummeryArr[$row[csf('knitting_company')]][$row[csf('recv_number')]]['used_qty'] +=$rcvmatInfoSummeryArr[$row[csf('knitting_company')]][$row[csf('recv_number')]][$m_prod_id][$row[csf('booking_id')]]['used_qty'];
						
						$rcvInfoSummeryArr[$row[csf('knitting_company')]][$row[csf('recv_number')]]['used_value'] +=$rcvmatInfoSummeryArr[$row[csf('knitting_company')]][$row[csf('recv_number')]][$m_prod_id][$row[csf('booking_id')]]['used_value'];
					}
					else
					{
						$yarn_des = $issuePercentageInfoArr[$row[csf('knitting_company')]][$m_prod_id][$reqInfoArr[$row[csf('booking_id')]]['requisition_no']]['yarn_des'];
						$yarn_des_arr = array_unique(explode(",",chop($yarn_des ,",")));
						
						$yarn_percentage=0;
						foreach ($yarn_des_arr as $val) 
						{
							$yarnInfoArr = array_unique(explode("**",chop($val ,",")));
							//echo "<pre>";print_r($yarnInfoArr);echo "</pre>";
							$yarn_percentage += $determination_arr[$row[csf('febric_description_id')]][$yarnInfoArr[0]][$yarnInfoArr[1]][$yarnInfoArr[2]];
						}
						

						$process_loss = $process_lossArr[$row[csf('febric_description_id')]]['process_loss'];
						$net_used = ($row[csf('grey_receive_qnty')] * $yarn_percentage) / 100;
						$process_loss_used = ($net_used * 100) / (100 - $process_loss);
						//$process_loss_used =$yarn_percentage;
						$rcvInfoArr[$row[csf('knitting_company')]][$m_prod_id][$row[csf('booking_id')]]['used_qty'] = $process_loss_used;
						$rcvInfoSummeryArr[$row[csf('knitting_company')]][$row[csf('recv_number')]]['used_qty']  += $process_loss_used;
						$rcvInfoSummeryArr[$row[csf('knitting_company')]][$row[csf('recv_number')]]['used_value'] += $process_loss_used*$issueDataArr[$row[csf('knitting_company')]][$m_prod_id][$reqInfoArr[$row[csf('booking_id')]]['requisition_no']]['rate'];
					}
				}
			}
			else
			{
				$rcvInfoSummeryArr[$row[csf('knitting_company')]][$row[csf('recv_number')]]['receive_date']= $row[csf('receive_date')];
				$rcvInfoSummeryArr[$row[csf('knitting_company')]][$row[csf('recv_number')]]['challan_no'] = $row[csf('challan_no')];

				if (in_array($row[csf("booking_id")], $check_booking_id_arr)) 
				{
					$rcvInfoArr[$row[csf('knitting_company')]][$row[csf("yarn_prod_id")]][$row[csf('booking_id')]]['used_qty'] = $rcvmatInfoArr[$row[csf('knitting_company')]][$row[csf("yarn_prod_id")]][$row[csf('booking_id')]]['used_qty'];
					$rcvInfoSummeryArr[$row[csf('knitting_company')]][$row[csf('recv_number')]]['used_qty']=$rcvmatInfoSummeryArr[$row[csf('knitting_company')]][$row[csf('recv_number')]][$row[csf("yarn_prod_id")]][$row[csf('booking_id')]]['used_qty'];
					$rcvInfoSummeryArr[$row[csf('knitting_company')]][$row[csf('recv_number')]]['used_value']=$rcvmatInfoSummeryArr[$row[csf('knitting_company')]][$row[csf('recv_number')]][$row[csf("yarn_prod_id")]][$row[csf('booking_id')]]['used_value'];
				}
				else
				{
					$yarn_des = $issuePercentageInfoArr[$row[csf('knitting_company')]][$row[csf("yarn_prod_id")]][$reqInfoArr[$row[csf('booking_id')]]['requisition_no']]['yarn_des'];
					$yarn_des_arr = array_unique(explode(",",chop($yarn_des ,",")));
					
					$yarn_percentage=0;
					foreach ($yarn_des_arr as $val) 
					{
						$yarnInfoArr = array_unique(explode("**",chop($val ,",")));
						//echo "<pre>";print_r($yarnInfoArr);echo "</pre>";
						$yarn_percentage += $determination_arr[$row[csf('febric_description_id')]][$yarnInfoArr[0]][$yarnInfoArr[1]][$yarnInfoArr[2]];
					}
					

					$process_loss = $process_lossArr[$row[csf('febric_description_id')]]['process_loss'];
					$net_used = ($row[csf('grey_receive_qnty')] * $yarn_percentage) / 100;
					$process_loss_used = ($net_used * 100) / (100 - $process_loss);
					//$process_loss_used =$yarn_percentage;
					$rcvInfoArr[$row[csf('knitting_company')]][$row[csf("yarn_prod_id")]][$row[csf('booking_id')]]['used_qty'] += $process_loss_used;
					$rcvInfoSummeryArr[$row[csf('knitting_company')]][$row[csf('recv_number')]]['used_qty']  += $process_loss_used;
					$rcvInfoSummeryArr[$row[csf('knitting_company')]][$row[csf('recv_number')]]['used_value'] += $process_loss_used*$issueDataArr[$row[csf('knitting_company')]][$row[csf("yarn_prod_id")]][$reqInfoArr[$row[csf('booking_id')]]['requisition_no']]['rate'];
				}
			}
			
        }
		unset($rcv_mst_rslt);
		//echo "<pre>";print_r($rcvInfoArr);echo "</pre>";

		$imge_arr=return_library_array( "select master_tble_id, image_location from common_photo_library where form_name='group_logo' and file_type=1",'master_tble_id','image_location');

		$com_info = "SELECT a.id, b.id as group_id,b.group_name, b.address 
        from lib_company a, lib_group b 
        where a.group_id=b.id AND a.id = ".$cbo_company_name." and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 "; 
        //echo $com_info;die;
        $com_info_rslt = sql_select($com_info);
		$group_info_arr = array();
		foreach ($com_info_rslt as $row) 
		{
			$group_info_arr[$row[csf('id')]]['group_name']=$row[csf('group_name')];
			$group_info_arr[$row[csf('id')]]['address']=$row[csf('address')];
			$group_info_arr[$row[csf('id')]]['group_id']=$row[csf('group_id')];
		}

		$r_id111=execute_query("DELETE FROM TMP_BOOKING_ID WHERE USERID=$user_id ");
		$r_id222=execute_query("DELETE FROM TMP_FEB_DES_ID WHERE USERID=$user_id ");
		$r_id333=execute_query("DELETE FROM TMP_REQS_NO WHERE USERID=$user_id ");
		oci_commit($con);
		disconnect($con);
		?>
		<style>
			.wrd_brk{word-break: break-all;word-wrap: break-word;}          
		</style>
		<?
     
        ob_start();
		?>
		 
		<fieldset style="width:1510px">
			
			<table width="1500" cellpadding="0" cellspacing="0" id="caption" align="left">
				<tr>  
				   <td align="left" width="100%" style="font-size:22px"><? echo "Party Wise Yarn Reconciliation Report"; ?></td>
				</tr> 
				<br>
				<tr>
				   <td align="left" width="100%" style="font-size:16px">As of &nbsp;&nbsp;<? echo date('d-m-Y');?></td>
				</tr> 
			</table>
			<br>
			<table width="600" cellpadding="0" cellspacing="0" border="1" rules="all" align="left" class="rpt_table" style="margin-bottom: 10px;">
				<tr bgcolor="#FFFFFF">  
				   <td align="left" width="200" style="font-size:12px; font-weight:bold">&nbsp;Unit Name</td>
				   <td align="left" width="400" style="font-size:12px; font-weight:normal">&nbsp;<? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></td>
				</tr> 
				<tr bgcolor="#E9F3FF">
					<td align="left" width="200" style="font-size:12px; font-weight:bold">&nbsp;Party Name</td>
				   <td align="left" width="400" style="font-size:12px; font-weight:normal">
					<?
						if($cbo_knitting_source==1)
						echo "&nbsp;".$company_arr[$txt_knitting_com_id];
						else if($cbo_knitting_source==3)
						echo "&nbsp;".$supplier_arr[$txt_knitting_com_id];
					?>
					</td>
				</tr> 
				<tr bgcolor="#FFFFFF">
					<td align="left" width="200" style="font-size:12px; font-weight:bold">&nbsp;Party Account Code</td>
				   <td align="left" width="400" style="font-size:12px; font-weight:normal">&nbsp;<? echo $txt_knitting_com_id;?></td>
				</tr> 
			</table>
			<div style="width:1510px;">
			<table width="1500" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
				<thead>
                    <tr>
                        <th width="30" class="wrd_brk"></th>
                        <th width="650" colspan="6" class="wrd_brk" style="font-size:14px; font-weight:bold">Yarn Details</th>
                        <th width="200" colspan="2" class="wrd_brk" style="font-size:14px; font-weight:bold">Issued</th>
                        <th width="200" colspan="2" class="wrd_brk" style="font-size:14px; font-weight:bold">Received/Used</th>
                        <th width="200" colspan="2" class="wrd_brk" style="font-size:14px; font-weight:bold">Yarn Return</th>
                        <th width="200" colspan="2" class="wrd_brk" style="font-size:14px; font-weight:bold">Balance</th>
                    </tr>
                    <tr>
                        <th width="30" class="wrd_brk" style="font-size:14px; font-weight:bold">SL</th>
                        <th width="100" class="wrd_brk" style="font-size:14px; font-weight:bold">Yarn Count</th>
                        <th width="150" class="wrd_brk" style="font-size:14px; font-weight:bold">Composition</th>
                        <th width="100" class="wrd_brk" style="font-size:14px; font-weight:bold">Yarn Type</th>
                        <th width="100" class="wrd_brk" style="font-size:14px; font-weight:bold">Color</th>
                        <th width="100" class="wrd_brk" style="font-size:14px; font-weight:bold">Lot</th>
                        <th width="100" class="wrd_brk" style="font-size:14px; font-weight:bold">Brand</th>
                        <th width="100" class="wrd_brk" style="font-size:14px; font-weight:bold">Qty</th>
                        <th width="100" class="wrd_brk" style="font-size:14px; font-weight:bold">Value</th>
                        <th width="100" class="wrd_brk" style="font-size:14px; font-weight:bold" >Qty</th>
                        <th width="100" class="wrd_brk" style="font-size:14px; font-weight:bold">Value</th>
                        <th width="100" class="wrd_brk" style="font-size:14px; font-weight:bold">Qty</th>
                        <th width="100" class="wrd_brk" style="font-size:14px; font-weight:bold">Value</th>
                        <th width="100" class="wrd_brk" style="font-size:14px; font-weight:bold">Qty</th>
                        <th class="wrd_brk" style="font-size:14px; font-weight:bold">Value</th>
                    </tr>
					
				</thead>
				<tbody id="scroll_body"> 
					<?
		
					$i = 1;
					$tot_issue_qty = $tot_issue_valie = $tot_used_qty = $tot_used_value = $tot_issue_rtn_qty = $tot_issue_rtn_value = $tot_balance_qnty = $tot_balance_qnty = 0;$used_qty=0;
					foreach($issueInfoArr as $party_id=>$party_data)
					{
						krsort($party_data);
						foreach($party_data as $prod_id=>$prod_data)
						{
							foreach($prod_data as $requisition_no=>$row)
							{
								//var_dump($requisition_no);
								if ($i%2==0)
									$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";

								$composition_string = $composition[$row['yarn_comp_type1st']] . " " . $row['yarn_comp_percent1st'] . "%";
								if ($row['yarn_comp_type2nd'] != 0) $composition_string .= " " . $composition[$row['yarn_comp_type2nd']] . " " . $row['yarn_comp_percent2nd'] . "%";    

								$issue_rtn_qty = $issueRtnArr[$party_id][$prod_id][$requisition_no]['issue_rtn_qty'];
								$issue_rtn_value = $issueRtnArr[$party_id][$prod_id][$requisition_no]['issue_rtn_value'];
								
								$used_qty = $rcvInfoArr[$party_id][$prod_id][$progInfoArr[$requisition_no]['knit_id']]['used_qty'];
								//$used_qty = $progInfoArr[$requisition_no]['knit_id'];
								$used_value = $used_qty*$row['rate'];
								$balance_qnty = $row['issue_qty']-$used_qty-$issue_rtn_qty;
								$balance_value = $balance_qnty*$row['rate'];

								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
									<td width="30" class="wrd_brk" style="font-size:12px; "><? echo $i; ?></td>
									<td width="100" class="wrd_brk" style="font-size:12px; "><? echo $count_arr[$row['yarn_count_id']]; ?>&nbsp;</td>
									<td width="150" class="wrd_brk" style="font-size:12px; "><? echo  $composition_string; ?>&nbsp;</td>
									<td width="100" class="wrd_brk" style="font-size:12px; "><? echo $yarn_type[$row['yarn_type']]; ?>&nbsp;</td>
									<td width="100" class="wrd_brk" style="font-size:12px; "><? echo $color_arr[$row['color']]; ?>&nbsp;</td>                                
									<td width="100" class="wrd_brk" style="font-size:12px; "><? echo $row['lot']; ?>&nbsp;</td>
									<td width="100" class="wrd_brk" style="font-size:12px; "><? echo $brand_arr[$row['brand']]; ?>&nbsp;</td>
									<td width="100" align="right" class="wrd_brk" style="font-size:12px; "><? echo number_format($row['issue_qty'],2); ?>&nbsp;</td>
									<td width="100" align="right" class="wrd_brk" style="font-size:12px; "><? echo number_format($row['issue_value'],2); ?>&nbsp;</td>
									<td width="100" align="right" class="wrd_brk" style="font-size:12px; "><? echo number_format($used_qty,2); ?>&nbsp;</td>
									<td width="100" align="right" class="wrd_brk" style="font-size:12px; "><? echo number_format($used_value,2); ?>&nbsp;</td>
									<td width="100" align="right" class="wrd_brk" style="font-size:12px; "><? echo number_format($issue_rtn_qty,2); ?></td>
									<td width="100" align="right" class="wrd_brk" style="font-size:12px; "><? echo number_format($issue_rtn_value,2); ?>&nbsp;</td>
									<td width="100" align="right" class="wrd_brk" style="font-size:12px; " title="( Issued Qnty-Receive Qnty-Issue Return Qnty )"><? echo number_format($balance_qnty,2); ?>&nbsp;</td>
									<td align="right" class="wrd_brk" style="font-size:12px; " title="( Issued Value-Receive Value-Issue Return Value )"><? echo number_format($balance_value,2); ?></td> 
								</tr>
								<?
								$i++;
								
								$tot_issue_qty+=$row['issue_qty'];
								$tot_issue_value+=$row['issue_value'];
								$tot_used_qty+=$used_qty;
								$tot_used_value+=$used_value;
								$tot_issue_rtn_qty+=$issue_rtn_qty;
								$tot_issue_rtn_value+=$issue_rtn_value;
								$tot_balance_qnty+=$balance_qnty;
								$tot_balance_value+=$balance_value;
									
							}
						}
					}
					
					?>
				</tbody> 
				<tfoot>
					<tr bgcolor="#CCCCCC">
						<td width="30">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="150">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100" align="right" style="font-size:12px; font-weight:bold">Total :</td>
						<td width="100" align="right" style="font-size:12px; font-weight:bold"><? echo number_format($tot_issue_qty,2); ?>&nbsp;</td>
						<td width="100" align="right" style="font-size:12px; font-weight:bold"><? echo number_format($tot_issue_value,2); ?>&nbsp;</td>
						<td width="100" align="right" style="font-size:12px; font-weight:bold"><? echo number_format($tot_used_qty,2); ?>&nbsp;</td>
						<td width="100" align="right" style="font-size:12px; font-weight:bold"><? echo number_format($tot_used_value,2); ?>&nbsp;</td>
						<td width="100" align="right" style="font-size:12px; font-weight:bold"><? echo number_format($tot_issue_rtn_qty,2); ?>&nbsp;</td>
						<td width="100" align="right" style="font-size:12px; font-weight:bold"><? echo number_format($tot_issue_rtn_value,2); ?>&nbsp;</td>
						<td width="100" align="right" style="font-size:12px; font-weight:bold"><? echo number_format($tot_balance_qnty,2); ?>&nbsp;</td>
						<td align="right" style="font-size:12px; font-weight:bold"><? echo number_format($tot_balance_value,2); ?>&nbsp;</td>
					</tr>
					<tr bgcolor="#CCCCCC">
						<td width="30">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="150">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100" align="right" style="font-size:12px; font-weight:bold">Balance %</td>
						<td width="100" align="right" style="font-size:12px; font-weight:bold" title="( total balance qnty/total issue qty*100 )"><? echo number_format(($tot_balance_qnty/$tot_issue_qty)*100,2); ?>&nbsp;</td>
						<td align="right" style="font-size:12px; font-weight:bold" title="( total balance value/total issue value*100 )"><? echo number_format(($tot_balance_value/$tot_issue_value)*100,2); ?>&nbsp;</td>
					</tr>
				</tfoot>
			</table>
			</div>
			<br>
			<div style="width:1510px;">
			<table cellpadding="0" width="490" cellspacing="0" align="left" class="rpt_table" rules="all" border="1">
				<thead>
					<tr>
						<th colspan="6" style="font-size:14px; font-weight:bold">Issued Details</th>
					</tr>
					<tr>
						<th width="30" style="font-size:12px; font-weight:bold" >SL</th>
						<th width="80" style="font-size:12px; font-weight:bold">Date</th>
						<th width="100" style="font-size:12px; font-weight:bold">Issue ID</th>
						<th width="100" style="font-size:12px; font-weight:bold">Challan no.</th>
						<th width="90" style="font-size:12px; font-weight:bold">Issue Qty</th>
						<th width="90" style="font-size:12px; font-weight:bold">Issue Value</th>
					</tr>
				</thead>
				<tbody>
					<?
					$i=1;
					$total_issue_qty = $total_issue_value = 0;
					foreach ($issueInfoSummeryArr as $k_party=>$v_party) 
					{
						krsort($v_party);
						foreach ($v_party as $k_issue=>$row) 
						{
							//var_dump($row);
							if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
							
							?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('rtr<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="rtr<? echo $i; ?>">
									<td width="30" style="word-break: break-all; font-size:12px; font-weight:normal" align="center"><? echo $i;?></td>
									<td width="80" style="word-break: break-all; font-size:12px; font-weight:normal"><? echo change_date_format($row['issue_date']);?></td>
									<td width="100" style="word-break: break-all; font-size:12px; font-weight:normal"><? echo $k_issue;?></td>
									<td width="100" style="word-break: break-all; font-size:12px; font-weight:normal"><? echo $row['challan_no'];?></td>
									<td width="90" style="word-break: break-all; font-size:12px; font-weight:normal" align="right"><? echo number_format($row['issue_qty'],2)?></td>
									<td width="90" style="word-break: break-all; font-size:12px; font-weight:normal" align="right"><? echo number_format($row['issue_value'],2)?></td>
								</tr>
							<?
							$i++;
							$total_issue_qty +=$row['issue_qty'];
							$total_issue_value +=$row['issue_value'];
						}
					}
					?>
					
				</tbody>
				<tfoot>
					<tr bgcolor="#CCCCCC">
						<td colspan="4" align="right" style="word-break: break-all; font-size:12px; font-weight:bold">Total :</td>
						<td align="right" style="word-break: break-all; font-size:12px; font-weight:bold"><? echo number_format($total_issue_qty,2)?></td>
						<td align="right" style="word-break: break-all; font-size:12px; font-weight:bold"><? echo number_format($total_issue_value,2)?></td>
					</tr>
				</tfoot>
			</table>

			<table cellpadding="0" width="1" cellspacing="0" align="left" rules="all" border="1">
				<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
			</table>
			
			<table cellpadding="0" width="490" cellspacing="0" align="left" class="rpt_table" rules="all" border="1">
				<thead>
					<tr>
						<th colspan="6" style="font-size:14px; font-weight:bold">Receive Details</th>
					</tr>
					<tr>
						<th width="30" style="font-size:12px; font-weight:bold">SL</th>
						<th width="80" style="font-size:12px; font-weight:bold">Date</th>
						<th width="100" style="font-size:12px; font-weight:bold">MRR No.</th>
						<th width="100" style="font-size:12px; font-weight:bold">Challan no.</th>
						<th width="90" style="font-size:12px; font-weight:bold">Rcv. Qty</th>
						<th width="90" style="font-size:12px; font-weight:bold">Rcv. Value</th>
					</tr>
				</thead>
				<tbody>
					<?
					$i=1;
					$total_rcv_qty = $total_rcv_value = 0;
					foreach ($rcvInfoSummeryArr as $k_party=>$v_party) 
					{
						krsort($v_party);
						foreach ($v_party as $k_rcv=>$row) 
						{
							
							//var_dump($row);
							if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
							
							?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('rtr<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="rtr<? echo $i; ?>">
									<td width="30" style="word-break: break-all; font-size:12px; font-weight:normal" align="center"><? echo $i;?></td>
									<td width="80" style="word-break: break-all; font-size:12px; font-weight:normal"><? echo change_date_format($row['receive_date']);?></td>
									<td width="100" style="word-break: break-all; font-size:12px; font-weight:normal"><? echo $k_rcv;?></td>
									<td width="100" style="word-break: break-all; font-size:12px; font-weight:normal"><? echo $row['challan_no'];?></td>
									<td width="90" style="word-break: break-all; font-size:12px; font-weight:normal" align="right"><? echo number_format($row['used_qty'],2)?></td>
									<td width="90" style="word-break: break-all; font-size:12px; font-weight:normal" align="right"><? echo number_format($row['used_value'],2)?></td>
								</tr>
							<?
							$i++;
							$total_rcv_qty +=$row['used_qty'];
							$total_rcv_value +=$row['used_value'];
							
						}
					}
					?>
					
				</tbody>
				<tfoot>
					<tr bgcolor="#CCCCCC">
						<td colspan="4" align="right" style="font-size:12px; font-weight:bold">Total :</td>
						<td align="right" style="font-size:12px; font-weight:bold"><? echo number_format($total_rcv_qty,2)?></td>
						<td align="right" style="font-size:12px; font-weight:bold"><? echo number_format($total_rcv_value,2)?></td>
					</tr>
				</tfoot>
			</table>
			<table cellpadding="0" width="1" cellspacing="0" align="left" rules="all" border="1">
				<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
			</table>

			<table cellpadding="0" width="490" cellspacing="0" align="left" class="rpt_table" rules="all" border="1">
				<thead>
					<tr>
						<th colspan="6" style="font-size:14px; font-weight:bold">Returned Details</th>
					</tr>
					<tr>
						<th width="30" style="font-size:12px; font-weight:bold">SL</th>
						<th width="80" style="font-size:12px; font-weight:bold">Date</th>
						<th width="100" style="font-size:12px; font-weight:bold">Return ID</th>
						<th width="100" style="font-size:12px; font-weight:bold">Challan no.</th>
						<th width="90" style="font-size:12px; font-weight:bold">Rtn. Qty</th>
						<th width="90" style="font-size:12px; font-weight:bold">Rtn. Value</th>
					</tr>
				</thead>
				<tbody>
					<?
					$i=1;
					$total_issue_rtn_qty = 0;
					$total_issue_rtn_value = 0;
					foreach ($issueRtnSummeryArr as $k_party=>$v_party) 
					{
						krsort($v_party);
						foreach ($v_party as $k_issue_rtn=>$row) 
						{
							
							//var_dump($row);
							if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
							
							?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('rtr<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="rtr<? echo $i; ?>">
									<td width="30" style="word-break: break-all; font-size:12px; font-weight:normal" align="center"><? echo $i;?></td>
									<td width="80" style="word-break: break-all; font-size:12px; font-weight:normal"><? echo change_date_format($row['receive_date']);?></td>
									<td width="100" style="word-break: break-all; font-size:12px; font-weight:normal"><? echo $k_issue_rtn;?></td>
									<td width="100" style="word-break: break-all; font-size:12px; font-weight:normal"><? echo $row['challan_no'];?></td>
									<td width="90" style="word-break: break-all; font-size:12px; font-weight:normal" align="right"><? echo number_format($row['issue_rtn_qty'],2)?></td>
									<td width="90" style="word-break: break-all; font-size:12px; font-weight:normal" align="right"><? echo number_format($row['issue_rtn_value'],2)?></td>
								</tr>
							<?
							$i++;
							$total_issue_rtn_qty +=$row['issue_rtn_qty'];
							$total_issue_rtn_value +=$row['issue_rtn_value'];
							
						}
					}
					?>
					
				</tbody>
				<tfoot>
					<tr bgcolor="#CCCCCC">
						<td colspan="4" align="right" style="font-size:12px; font-weight:bold">Total :</td>
						<td align="right" style="font-size:12px; font-weight:bold"><? echo number_format($total_issue_rtn_qty,2)?></td>
						<td align="right" style="font-size:12px; font-weight:bold"><? echo number_format($total_issue_rtn_value,2)?></td>
					</tr>
				</tfoot>
			</table>
			</div>
			
		</fieldset>        
		<?
	}

	// foreach (glob("$user_id*.pdf") as $filename) 
	// {
	// 	if( @filemtime($filename) < (time()-$seconds_old) )
	// 	@unlink($filename);
	// }
	
	// require('../../../../ext_resource/mpdf60/mpdf.php');
	// $mpdf = new mPDF('', 'A4', '', '', 10, 10, 10, 35, 3, 3);	
	// $mpdf->WriteHTML(ob_get_contents(),2);
	// $user_id=$_SESSION['logic_erp']['user_id'];
	// $REAL_FILE_NAME = $user_id.'.pdf';
	// $file_path=$REAL_FILE_NAME;
	// $mpdf->Output($file_path, 'F');
	
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
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";

	exit();
}

if($action=="pdf_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name");
	$buyer_arr=return_library_array( "select id, short_name from  lib_buyer", "id", "short_name");
	$brand_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');
	$store_arr=return_library_array( "select id, store_name from lib_store_location", "id", "store_name");
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	
	$type=str_replace("'","",$type);
    $cbo_company_name=str_replace("'","",$cbo_company_name);
    $txt_knitting_com_id=str_replace("'","",$txt_knitting_com_id);
    $cbo_knitting_source=str_replace("'","",$cbo_knitting_source);
    $txt_production_id=str_replace("'","",$txt_production_id);

   // var_dump($cbo_company_name);
	if ($cbo_company_name=="")
		$company_cond_1="";
	else
		$company_cond_1=" AND a.company_id in ($cbo_company_name)";

    
    if ($txt_knitting_com_id=='')
        $party_cond_1="";
    else
        $party_cond_1=" AND a.knitting_company in ($txt_knitting_com_id)";   
        
    //for knitting source condition
	if ($cbo_knitting_source==0)
	{
		$knit_source_cond="";
	}
	else
	{
		$knit_source_cond=" AND a.knitting_source=$cbo_knitting_source";
	} 
    
    if ($txt_production_id=="")
		$production_cond_1="";
	else
		$production_cond_1=" AND a.id in ($txt_production_id)";

   
	//for Show button
	if($type==1)
	{
        /*
        |--------------------------------------------------------------------------
        | for Knitting Production
        |--------------------------------------------------------------------------
        */
        $con = connect();
        $r_id111=execute_query("DELETE FROM TMP_BOOKING_ID WHERE USERID=$user_id ");
		$r_id222=execute_query("DELETE FROM TMP_FEB_DES_ID WHERE USERID=$user_id ");
		$r_id333=execute_query("DELETE FROM TMP_REQS_NO WHERE USERID=$user_id ");
		oci_commit($con);
		disconnect($con);

		$sql_receive_mst = "SELECT a.id as mst_id, a.recv_number, a.receive_date, a.challan_no, a.booking_id, a.booking_no, a.knitting_company, a.receive_basis, a.entry_form, b.grey_receive_qnty, b.febric_description_id, b.yarn_prod_id 
        from inv_receive_master a, pro_grey_prod_entry_dtls b 
        where a.id=b.mst_id AND a.receive_basis = 2 AND a.item_category = 13 AND a.entry_form = 2 AND a.roll_maintained = 1 AND a.company_id = ".$cbo_company_name." AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 ".$knit_source_cond.$party_cond_1.$production_cond_1; 
		//echo $sql_receive_mst;//die;
		$rcv_mst_rslt = sql_select($sql_receive_mst);
		foreach ($rcv_mst_rslt as $row) 
        {
			if($bookingIdChk[$row[csf('booking_id')]] == "")
			{
				$bookingIdChk[$row[csf('booking_id')]] = $row[csf('booking_id')];
				$all_booking_id_arr[$row[csf("booking_id")]] = $row[csf("booking_id")];
			}

			if($febDesIdChk[$row[csf('febric_description_id')]] == "")
			{
				$febDesIdChk[$row[csf('febric_description_id')]] = $row[csf('febric_description_id')];
				$all_febdes_id_arr[$row[csf("febric_description_id")]] = $row[csf("febric_description_id")];
			}
		}

		$all_booking_id_arr = array_filter($all_booking_id_arr);
		if(!empty($all_booking_id_arr))
		{
			$con = connect();
			foreach($all_booking_id_arr as $bookingId)
			{
				execute_query("INSERT INTO TMP_BOOKING_ID(BOOKING_ID,USERID) VALUES(".$bookingId.", ".$user_id.")");
				oci_commit($con);
			}
		}
		//die;
		$all_febdes_id_arr = array_filter($all_febdes_id_arr);
		if(!empty($all_febdes_id_arr))
		{
			$con = connect();
			foreach($all_febdes_id_arr as $febdesId)
			{
				execute_query("INSERT INTO TMP_FEB_DES_ID(FEB_DES_ID,USERID) VALUES(".$febdesId.", ".$user_id.")");
				oci_commit($con);
			}
		}
		//die;
		
		/*
        |--------------------------------------------------------------------------
        | for reqsition  
        |--------------------------------------------------------------------------
        */
		
        $reqsition_sql = "SELECT a.knit_id, a.requisition_no from ppl_yarn_requisition_entry a, tmp_booking_id b 
        where a.knit_id=b.booking_id and b.userid=$user_id and a.status_active=1 and a.is_deleted=0";
        //echo $reqsition_sql;die;
        $reqsition_rslt = sql_select($reqsition_sql);
        $reqChk = array();
        $progInfoArr = array();
        $reqInfoArr = array();
        foreach ($reqsition_rslt as $row) 
        {
            if($reqChk[$row[csf('requisition_no')]] == "")
            {
                $reqChk[$row[csf('requisition_no')]] = $row[csf('requisition_no')];
				$all_req_no_arr[$row[csf("requisition_no")]] = $row[csf("requisition_no")];
            }
            $progInfoArr[$row[csf('requisition_no')]]['knit_id'] = $row[csf('knit_id')];
            $reqInfoArr[$row[csf('knit_id')]]['requisition_no'] = $row[csf('requisition_no')];
        }
        unset($reqsition_rslt);

		$all_req_no_arr = array_filter($all_req_no_arr);
		if(!empty($all_req_no_arr))
		{
			$con = connect();
			foreach($all_req_no_arr as $reqNo)
			{
				execute_query("INSERT INTO TMP_REQS_NO(REQS_NO,USERID) VALUES(".$reqNo.", ".$user_id.")");
				oci_commit($con);
			}
		}
		//die;

		/*
        |--------------------------------------------------------------------------
        | for determination  
        |--------------------------------------------------------------------------
        */
		// echo "select a.id, b.count_id,b.type_id, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b, tmp_feb_des_id c where a.id=b.mst_id and a.id=c.feb_des_id and c.userid=$user_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ";die;
		$sql_determination = sql_select("select a.id, b.count_id,b.type_id, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b, tmp_feb_des_id c where a.id=b.mst_id and a.id=c.feb_des_id and c.userid=$user_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ");
    		
		$determination_arr = array();
		foreach ($sql_determination as $row) 
		{
			$determination_arr[$row[csf('id')]][$row[csf('count_id')]][$row[csf('copmposition_id')]][$row[csf('type_id')]] = $row[csf('percent')];
		}

        /*
        |--------------------------------------------------------------------------
        | for issue  
        |--------------------------------------------------------------------------
        */
        
        $sql_issue = "SELECT a.knit_dye_company as knitting_company, a.issue_number, a.issue_date, a.challan_no, c.id as prod_id, c.lot, c.brand, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_comp_type2nd, c.yarn_comp_percent2nd, c.yarn_type, c.color, b.cons_quantity, b.cons_rate, b.cons_amount, b.requisition_no 
        from inv_issue_master a, inv_transaction b, product_details_master c, tmp_reqs_no d 
        where a.id=b.mst_id and b.prod_id=c.id and b.requisition_no=d.reqs_no and d.userid=$user_id and a.company_id = ".$cbo_company_name." and a.entry_form=3 and a.item_category=1 and b.transaction_type=2 and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ";
        //echo $sql_issue;//die;

        $sql_issue_rslt=sql_select($sql_issue);
      
        $issueInfoArr = array();
        $issueDataArr = array();
        $issueInfoSummeryArr = array();
        $issuePercentageInfoArr = array();
		$yarn_percentage=0;
        foreach($sql_issue_rslt as $row)
        {
            $issueInfoArr[$row[csf('knitting_company')]][$row[csf('prod_id')]][$row[csf('requisition_no')]]['yarn_count_id'] = $row[csf('yarn_count_id')];
            $issueInfoArr[$row[csf('knitting_company')]][$row[csf('prod_id')]][$row[csf('requisition_no')]]['yarn_comp_type1st'] = $row[csf('yarn_comp_type1st')];
            $issueInfoArr[$row[csf('knitting_company')]][$row[csf('prod_id')]][$row[csf('requisition_no')]]['yarn_comp_percent1st'] = $row[csf('yarn_comp_percent1st')];
            $issueInfoArr[$row[csf('knitting_company')]][$row[csf('prod_id')]][$row[csf('requisition_no')]]['yarn_comp_type2nd'] = $row[csf('yarn_comp_type2nd')];
            $issueInfoArr[$row[csf('knitting_company')]][$row[csf('prod_id')]][$row[csf('requisition_no')]]['yarn_comp_percent2nd'] = $row[csf('yarn_comp_percent2nd')];
            $issueInfoArr[$row[csf('knitting_company')]][$row[csf('prod_id')]][$row[csf('requisition_no')]]['yarn_type'] = $row[csf('yarn_type')];
            $issueInfoArr[$row[csf('knitting_company')]][$row[csf('prod_id')]][$row[csf('requisition_no')]]['color'] = $row[csf('color')];
            $issueInfoArr[$row[csf('knitting_company')]][$row[csf('prod_id')]][$row[csf('requisition_no')]]['lot'] = $row[csf('lot')];
            $issueInfoArr[$row[csf('knitting_company')]][$row[csf('prod_id')]][$row[csf('requisition_no')]]['brand'] = $row[csf('brand')];
            $issueInfoArr[$row[csf('knitting_company')]][$row[csf('prod_id')]][$row[csf('requisition_no')]]['issue_qty'] += $row[csf('cons_quantity')];
            $issueInfoArr[$row[csf('knitting_company')]][$row[csf('prod_id')]][$row[csf('requisition_no')]]['rate'] = $row[csf('cons_rate')];
            $issueInfoArr[$row[csf('knitting_company')]][$row[csf('prod_id')]][$row[csf('requisition_no')]]['issue_value'] += $row[csf('cons_amount')];

			$issueDataArr[$row[csf('knitting_company')]][$row[csf('prod_id')]][$row[csf('requisition_no')]]['rate'] = $row[csf('cons_rate')];

			// ------------- Issue Details Summery -----------------------

			$issueInfoSummeryArr[$row[csf('knitting_company')]][$row[csf('issue_number')]]['issue_date'] = $row[csf('issue_date')];
			$issueInfoSummeryArr[$row[csf('knitting_company')]][$row[csf('issue_number')]]['challan_no'] = $row[csf('challan_no')];
			$issueInfoSummeryArr[$row[csf('knitting_company')]][$row[csf('issue_number')]]['issue_qty'] += $row[csf('cons_quantity')];
			$issueInfoSummeryArr[$row[csf('knitting_company')]][$row[csf('issue_number')]]['issue_value'] += $row[csf('cons_amount')];

			
			$issuePercentageInfoArr[$row[csf('knitting_company')]][$row[csf('prod_id')]][$row[csf('requisition_no')]]['yarn_des'] .=$row[csf('yarn_count_id')].'**'.$row[csf('yarn_comp_type1st')].'**'.$row[csf('yarn_type')].',';
            
        }
        unset($sql_issue_rslt);
       //echo "<pre>";print_r($issuePercentageInfoArr);echo "</pre>";


        /*
        |--------------------------------------------------------------------------
        | for issue return 
        |--------------------------------------------------------------------------
        */
        $sql_iss_rtn = "SELECT a.id, a.recv_number, a.receive_date, a.challan_no, a.booking_id, a.booking_no,a.knitting_company, a.receive_basis, a.entry_form, b.item_category, b.cons_quantity, b.cons_amount,b.prod_id from inv_receive_master a, inv_transaction b, tmp_reqs_no c where a.id=b.mst_id and a.booking_id=c.reqs_no and c.userid=$user_id and a.receive_basis = 3 and a.item_category = 1 and a.entry_form = 9 and a.company_id = ".$cbo_company_name." and b.item_category = 1 and b.transaction_type = 4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ";
        //echo $sql_iss_rtn; die;
        $sql_iss_rtn_rslt=sql_select($sql_iss_rtn);
      
        $issueRtnArr = array();
        $issueRtnSummeryArr = array();
        foreach($sql_iss_rtn_rslt as $row)
        {
            /*
            |--------------------------------------------------------------------------
            | for Yarn Issue Return
            | if receive_basis = 3(Requisition) and
            | entry_form = 9(Yarn Issue Return) and
            | item_category = 1(Yarn) then
            | tbl inv_receive_master booking_id/booking_no = requisition_no
            |--------------------------------------------------------------------------
            */
           
            $issueRtnArr[$row[csf('knitting_company')]][$row[csf('prod_id')]][$row[csf('booking_id')]]['issue_rtn_qty'] += $row[csf('cons_quantity')];
            $issueRtnArr[$row[csf('knitting_company')]][$row[csf('prod_id')]][$row[csf('booking_id')]]['issue_rtn_value'] += $row[csf('cons_amount')];

			// ------------- Issue Return Details Summery -----------------------

			$issueRtnSummeryArr[$row[csf('knitting_company')]][$row[csf('recv_number')]]['receive_date'] = $row[csf('receive_date')];
			$issueRtnSummeryArr[$row[csf('knitting_company')]][$row[csf('recv_number')]]['challan_no'] = $row[csf('challan_no')];
			$issueRtnSummeryArr[$row[csf('knitting_company')]][$row[csf('recv_number')]]['issue_rtn_qty'] += $row[csf('cons_quantity')];
			$issueRtnSummeryArr[$row[csf('knitting_company')]][$row[csf('recv_number')]]['issue_rtn_value'] += $row[csf('cons_amount')];
               
            
        }
        unset($sql_iss_rtn_rslt);
		//echo "<pre>";print_r($issueRtnSummeryArr);echo "</pre>";
		
		// echo "select a.mst_id, a.process_loss, a.rate, a.process_id from conversion_process_loss a, tmp_feb_des_id b where a.mst_id=b.feb_des_id and b.userid=$user_id and a.process_id in (1,3,4) and a.status_active=1 and a.is_deleted=0";die;
		$processloss_sql = sql_select("select a.mst_id, a.process_loss, a.rate, a.process_id from conversion_process_loss a, tmp_feb_des_id b where a.mst_id=b.feb_des_id and b.userid=$user_id and a.process_id in (1,3,4) and a.status_active=1 and a.is_deleted=0");
		$process_lossArr = array();
    	foreach ($processloss_sql as $row) 
    	{
			$process_lossArr[$row[csf('mst_id')]]['process_loss'] += $row[csf('process_loss')];
    	}

		/*
        |--------------------------------------------------------------------------
        | for Receive Details Summery  
        |--------------------------------------------------------------------------
        */
		

        $sql_material_rcv = "SELECT a.id as mst_id, a.recv_number, a.receive_date, a.challan_no, a.booking_id, a.booking_no, a.knitting_company, a.receive_basis, a.entry_form,c.used_qty, c.prod_id 
        from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_material_used_dtls c 
        where a.id=b.mst_id and a.id=c.mst_id AND b.id=c.DTLS_ID AND a.receive_basis = 2 AND a.item_category = 13 AND a.entry_form = 2 AND a.roll_maintained = 1 AND a.company_id = ".$cbo_company_name." and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 ".$knit_source_cond.$party_cond_1.$production_cond_1; 
        
        //echo $sql_material_rcv;//die;
        $rcv_material_rslt = sql_select($sql_material_rcv);
        $rcvmatInfoArr = array();
		$rcvInfoSummeryArr = array();
        foreach ($rcv_material_rslt as $row) 
        {
            $rcvmatInfoArr[$row[csf('knitting_company')]][$row[csf('prod_id')]][$row[csf('booking_id')]]['used_qty'] += $row[csf('used_qty')];
			$rcvmatInfoSummeryArr[$row[csf('knitting_company')]][$row[csf('recv_number')]][$row[csf('prod_id')]][$row[csf('booking_id')]]['used_qty'] += $row[csf('used_qty')];
			$rcvmatInfoSummeryArr[$row[csf('knitting_company')]][$row[csf('recv_number')]][$row[csf('prod_id')]][$row[csf('booking_id')]]['used_value'] += $row[csf('used_qty')]*$issueInfoArr[$row[csf('knitting_company')]][$row[csf('prod_id')]][$reqInfoArr[$row[csf('booking_id')]]['requisition_no']]['rate'];

			$check_booking_id_arr[] = $row[csf('booking_id')];
        }
        unset($rcv_material_rslt);
        //echo "<pre>";print_r($rcvmatInfoArrr);echo "</pre>";


		foreach ($rcv_mst_rslt as $row) 
        {
			if(strpos($row[csf("yarn_prod_id")], ",")==true)
			{
				$multi_prod_id=explode(",",$row[csf("yarn_prod_id")]);
				foreach($multi_prod_id as $m_prod_id)
				{ 
					
					$rcvInfoSummeryArr[$row[csf('knitting_company')]][$row[csf('recv_number')]]['receive_date']= $row[csf('receive_date')];
					$rcvInfoSummeryArr[$row[csf('knitting_company')]][$row[csf('recv_number')]]['challan_no'] = $row[csf('challan_no')];

					if (in_array($row[csf("booking_id")], $check_booking_id_arr)) 
					{
						$rcvInfoArr[$row[csf('knitting_company')]][$m_prod_id][$row[csf('booking_id')]]['used_qty'] = $rcvmatInfoArr[$row[csf('knitting_company')]][$m_prod_id][$row[csf('booking_id')]]['used_qty'];
						$rcvInfoSummeryArr[$row[csf('knitting_company')]][$row[csf('recv_number')]]['used_qty'] +=$rcvmatInfoSummeryArr[$row[csf('knitting_company')]][$row[csf('recv_number')]][$m_prod_id][$row[csf('booking_id')]]['used_qty'];
						
						$rcvInfoSummeryArr[$row[csf('knitting_company')]][$row[csf('recv_number')]]['used_value'] +=$rcvmatInfoSummeryArr[$row[csf('knitting_company')]][$row[csf('recv_number')]][$m_prod_id][$row[csf('booking_id')]]['used_value'];
					}
					else
					{
						$yarn_des = $issuePercentageInfoArr[$row[csf('knitting_company')]][$m_prod_id][$reqInfoArr[$row[csf('booking_id')]]['requisition_no']]['yarn_des'];
						$yarn_des_arr = array_unique(explode(",",chop($yarn_des ,",")));
						
						$yarn_percentage=0;
						foreach ($yarn_des_arr as $val) 
						{
							$yarnInfoArr = array_unique(explode("**",chop($val ,",")));
							//echo "<pre>";print_r($yarnInfoArr);echo "</pre>";
							$yarn_percentage += $determination_arr[$row[csf('febric_description_id')]][$yarnInfoArr[0]][$yarnInfoArr[1]][$yarnInfoArr[2]];
						}
						

						$process_loss = $process_lossArr[$row[csf('febric_description_id')]]['process_loss'];
						$net_used = ($row[csf('grey_receive_qnty')] * $yarn_percentage) / 100;
						$process_loss_used = ($net_used * 100) / (100 - $process_loss);
						//$process_loss_used =$yarn_percentage;
						$rcvInfoArr[$row[csf('knitting_company')]][$m_prod_id][$row[csf('booking_id')]]['used_qty'] = $process_loss_used;
						$rcvInfoSummeryArr[$row[csf('knitting_company')]][$row[csf('recv_number')]]['used_qty']  += $process_loss_used;
						$rcvInfoSummeryArr[$row[csf('knitting_company')]][$row[csf('recv_number')]]['used_value'] += $process_loss_used*$issueDataArr[$row[csf('knitting_company')]][$m_prod_id][$reqInfoArr[$row[csf('booking_id')]]['requisition_no']]['rate'];
					}
				}
			}
			else
			{
				$rcvInfoSummeryArr[$row[csf('knitting_company')]][$row[csf('recv_number')]]['receive_date']= $row[csf('receive_date')];
				$rcvInfoSummeryArr[$row[csf('knitting_company')]][$row[csf('recv_number')]]['challan_no'] = $row[csf('challan_no')];

				if (in_array($row[csf("booking_id")], $check_booking_id_arr)) 
				{
					$rcvInfoArr[$row[csf('knitting_company')]][$row[csf("yarn_prod_id")]][$row[csf('booking_id')]]['used_qty'] = $rcvmatInfoArr[$row[csf('knitting_company')]][$row[csf("yarn_prod_id")]][$row[csf('booking_id')]]['used_qty'];
					$rcvInfoSummeryArr[$row[csf('knitting_company')]][$row[csf('recv_number')]]['used_qty']=$rcvmatInfoSummeryArr[$row[csf('knitting_company')]][$row[csf('recv_number')]][$row[csf("yarn_prod_id")]][$row[csf('booking_id')]]['used_qty'];
					$rcvInfoSummeryArr[$row[csf('knitting_company')]][$row[csf('recv_number')]]['used_value']=$rcvmatInfoSummeryArr[$row[csf('knitting_company')]][$row[csf('recv_number')]][$row[csf("yarn_prod_id")]][$row[csf('booking_id')]]['used_value'];
				}
				else
				{
					$yarn_des = $issuePercentageInfoArr[$row[csf('knitting_company')]][$row[csf("yarn_prod_id")]][$reqInfoArr[$row[csf('booking_id')]]['requisition_no']]['yarn_des'];
					$yarn_des_arr = array_unique(explode(",",chop($yarn_des ,",")));
					
					$yarn_percentage=0;
					foreach ($yarn_des_arr as $val) 
					{
						$yarnInfoArr = array_unique(explode("**",chop($val ,",")));
						//echo "<pre>";print_r($yarnInfoArr);echo "</pre>";
						$yarn_percentage += $determination_arr[$row[csf('febric_description_id')]][$yarnInfoArr[0]][$yarnInfoArr[1]][$yarnInfoArr[2]];
					}
					

					$process_loss = $process_lossArr[$row[csf('febric_description_id')]]['process_loss'];
					$net_used = ($row[csf('grey_receive_qnty')] * $yarn_percentage) / 100;
					$process_loss_used = ($net_used * 100) / (100 - $process_loss);
					//$process_loss_used =$yarn_percentage;
					$rcvInfoArr[$row[csf('knitting_company')]][$row[csf("yarn_prod_id")]][$row[csf('booking_id')]]['used_qty'] += $process_loss_used;
					$rcvInfoSummeryArr[$row[csf('knitting_company')]][$row[csf('recv_number')]]['used_qty']  += $process_loss_used;
					$rcvInfoSummeryArr[$row[csf('knitting_company')]][$row[csf('recv_number')]]['used_value'] += $process_loss_used*$issueDataArr[$row[csf('knitting_company')]][$row[csf("yarn_prod_id")]][$reqInfoArr[$row[csf('booking_id')]]['requisition_no']]['rate'];
				}
			}
			
        }
		unset($rcv_mst_rslt);
		//echo "<pre>";print_r($rcvInfoArr);echo "</pre>";

		// $company_arr = return_library_array("select id,company_name from lib_company", "id", "company_name");
		 $imge_arr=return_library_array( "select master_tble_id, image_location from common_photo_library where form_name='group_logo' and file_type=1",'master_tble_id','image_location');

		$com_info = "SELECT a.id, b.id as group_id,b.group_name, b.address 
        from lib_company a, lib_group b 
        where a.group_id=b.id AND a.id = ".$cbo_company_name." and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 "; 
        //echo $com_info;die;
        $com_info_rslt = sql_select($com_info);
		$group_info_arr = array();
		foreach ($com_info_rslt as $row) 
		{
			$group_info_arr[$row[csf('id')]]['group_name']=$row[csf('group_name')];
			$group_info_arr[$row[csf('id')]]['address']=$row[csf('address')];
			$group_info_arr[$row[csf('id')]]['group_id']=$row[csf('group_id')];
		}

		$r_id111=execute_query("DELETE FROM TMP_BOOKING_ID WHERE USERID=$user_id ");
		$r_id222=execute_query("DELETE FROM TMP_FEB_DES_ID WHERE USERID=$user_id ");
		$r_id333=execute_query("DELETE FROM TMP_REQS_NO WHERE USERID=$user_id ");
		oci_commit($con);
		disconnect($con);
		
		?>
		<style>
			.wrd_brk{word-break: break-all;word-wrap: break-word;}          
		</style>
		<?
     
        ob_start();
		?>
		 
		<fieldset style="width:1510px">
			<table width="100%" cellpadding="0" cellspacing="0" >
				<tr>
					<td width="70" align="right"> 

						<img  src='<? echo base_url($imge_arr[$group_info_arr[$cbo_company_name]['group_id']]); ?>' height='80' width='100' /> 
						
					</td>
					<td>
						<table width="100%" style="margin-top:10px">
							<tr>
							<td align="center" width="100%" style="font-size:18px"><b><? echo $group_info_arr[$cbo_company_name]['group_name']; ?></b></td>
							</tr>
							<tr>
							<td align="center" width="100%" style="font-size:16px"><b><? echo $group_info_arr[$cbo_company_name]['address']; ?></b></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			<table width="1500" cellpadding="0" cellspacing="0" id="caption" align="left">
				
				<tr>  
				   <td align="left" width="100%" style="font-size:22px"><? echo "Party Wise Yarn Reconciliation Report"; ?></td>
				</tr> 
				<br>
				<tr>
				   <td align="left" width="100%" style="font-size:20px">As of &nbsp;&nbsp;<? echo date('d-m-Y');?></td>
				</tr> 
			</table>
			<br>
			<table width="350" cellpadding="0" cellspacing="0" rules="all" align="left" border="1"  style="margin-bottom: 10px; ">
				<tr >  
				   <td align="left" width="150" style="font-size:10px; font-weight:normal; ">&nbsp;Unit Name</td>
				   <td align="left" width="200" style="font-size:10px; font-weight:normal; ">&nbsp;<? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></td>
				</tr> 
				<tr >
					<td align="left" width="150" style="font-size:10px; font-weight:normal; ">&nbsp;Party Name</td>
				   <td align="left" width="200" style="font-size:10px; font-weight:normal; ">
					<?
						if($cbo_knitting_source==1)
						echo "&nbsp;".$company_arr[$txt_knitting_com_id];
						else if($cbo_knitting_source==3)
						echo "&nbsp;".$supplier_arr[$txt_knitting_com_id];
					?>
					</td>
				</tr> 
				<tr >
					<td align="left" width="150" style="font-size:10px; font-weight:normal;  ">&nbsp;Party Account Code</td>
				   <td align="left" width="200" style="font-size:10px; font-weight:normal; ">&nbsp;<? echo $txt_knitting_com_id;?></td>
				</tr> 
			</table>
			<div style="width:1510px;">
			<table width="1500" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
				<thead>
                    <tr>
                        <th width="30" class="wrd_brk"></th>
                        <th width="650" colspan="6" class="wrd_brk" style="font-size:20px; font-weight:bold">Yarn Details</th>
                        <th width="200" colspan="2" class="wrd_brk" style="font-size:20px; font-weight:bold">Issued</th>
                        <th width="200" colspan="2" class="wrd_brk" style="font-size:20px; font-weight:bold">Received/Used</th>
                        <th width="200" colspan="2" class="wrd_brk" style="font-size:20px; font-weight:bold">Yarn Return</th>
                        <th width="200" colspan="2" class="wrd_brk" style="font-size:20px; font-weight:bold">Balance</th>
                    </tr>
                    <tr>
                        <th width="30" class="wrd_brk" style="font-size:20px; font-weight:bold">SL</th>
                        <th width="100" class="wrd_brk" style="font-size:20px; font-weight:bold">Yarn Count</th>
                        <th width="150" class="wrd_brk" style="font-size:20px; font-weight:bold">Composition</th>
                        <th width="100" class="wrd_brk" style="font-size:20px; font-weight:bold">Yarn Type</th>
                        <th width="100" class="wrd_brk" style="font-size:20px; font-weight:bold">Color</th>
                        <th width="100" class="wrd_brk" style="font-size:20px; font-weight:bold">Lot</th>
                        <th width="100" class="wrd_brk" style="font-size:20px; font-weight:bold">Brand</th>
                        <th width="100" class="wrd_brk" style="font-size:20px; font-weight:bold">Qty</th>
                        <th width="100" class="wrd_brk" style="font-size:20px; font-weight:bold">Value</th>
                        <th width="100" class="wrd_brk" style="font-size:20px; font-weight:bold" >Qty</th>
                        <th width="100" class="wrd_brk" style="font-size:20px; font-weight:bold">Value</th>
                        <th width="100" class="wrd_brk" style="font-size:20px; font-weight:bold">Qty</th>
                        <th width="100" class="wrd_brk" style="font-size:20px; font-weight:bold">Value</th>
                        <th width="100" class="wrd_brk" style="font-size:20px; font-weight:bold">Qty</th>
                        <th class="wrd_brk" style="font-size:20px; font-weight:bold">Value</th>
                    </tr>
					
				</thead>
				<tbody id="scroll_body"> 
					<?
		
					$i = 1;
					$tot_issue_qty = $tot_issue_valie = $tot_used_qty = $tot_used_value = $tot_issue_rtn_qty = $tot_issue_rtn_value = $tot_balance_qnty = $tot_balance_qnty = 0;$used_qty=0;
					foreach($issueInfoArr as $party_id=>$party_data)
					{
						krsort($party_data);
						foreach($party_data as $prod_id=>$prod_data)
						{
							foreach($prod_data as $requisition_no=>$row)
							{
								//var_dump($requisition_no);
								//if ($i%2==0) $bgcolor="#E9F3FF";else $bgcolor="#FFFFFF";

								$composition_string = $composition[$row['yarn_comp_type1st']] . " " . $row['yarn_comp_percent1st'] . "%";
								if ($row['yarn_comp_type2nd'] != 0) $composition_string .= " " . $composition[$row['yarn_comp_type2nd']] . " " . $row['yarn_comp_percent2nd'] . "%";    

								$issue_rtn_qty = $issueRtnArr[$party_id][$prod_id][$requisition_no]['issue_rtn_qty'];
								$issue_rtn_value = $issueRtnArr[$party_id][$prod_id][$requisition_no]['issue_rtn_value'];
								
								$used_qty = $rcvInfoArr[$party_id][$prod_id][$progInfoArr[$requisition_no]['knit_id']]['used_qty'];
								//$used_qty = $progInfoArr[$requisition_no]['knit_id'];
								$used_value = $used_qty*$row['rate'];
								$balance_qnty = $row['issue_qty']-$used_qty-$issue_rtn_qty;
								$balance_value = $balance_qnty*$row['rate'];

								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
									<td width="30" class="wrd_brk" style="font-size:18px; "><? echo $i; ?></td>
									<td width="100" class="wrd_brk" style="font-size:18px; "><? echo $count_arr[$row['yarn_count_id']]; ?>&nbsp;</td>
									<td width="150" class="wrd_brk" style="font-size:18px; "><? echo  $composition_string; ?>&nbsp;</td>
									<td width="100" class="wrd_brk" style="font-size:18px; "><? echo $yarn_type[$row['yarn_type']]; ?>&nbsp;</td>
									<td width="100" class="wrd_brk" style="font-size:18px; "><? echo $color_arr[$row['color']]; ?>&nbsp;</td>                                
									<td width="100" class="wrd_brk" style="font-size:18px; "><? echo $row['lot']; ?>&nbsp;</td>
									<td width="100" class="wrd_brk" style="font-size:18px; "><? echo $brand_arr[$row['brand']]; ?>&nbsp;</td>
									<td width="100" align="right" class="wrd_brk" style="font-size:18px; "><? echo number_format($row['issue_qty'],2); ?>&nbsp;</td>
									<td width="100" align="right" class="wrd_brk" style="font-size:18px; "><? echo number_format($row['issue_value'],2); ?>&nbsp;</td>
									<td width="100" align="right" class="wrd_brk" style="font-size:18px; "><? echo number_format($used_qty,2); ?>&nbsp;</td>
									<td width="100" align="right" class="wrd_brk" style="font-size:18px; "><? echo number_format($used_value,2); ?>&nbsp;</td>
									<td width="100" align="right" class="wrd_brk" style="font-size:18px; "><? echo number_format($issue_rtn_qty,2); ?></td>
									<td width="100" align="right" class="wrd_brk" style="font-size:18px; "><? echo number_format($issue_rtn_value,2); ?>&nbsp;</td>
									<td width="100" align="right" class="wrd_brk" style="font-size:18px; " title="( Issued Qnty-Receive Qnty-Issue Return Qnty )"><? echo number_format($balance_qnty,2); ?>&nbsp;</td>
									<td align="right" class="wrd_brk" style="font-size:18px; " title="( Issued Value-Receive Value-Issue Return Value )"><? echo number_format($balance_value,2); ?></td> 
								</tr>
								<?
								$i++;
								
								$tot_issue_qty+=$row['issue_qty'];
								$tot_issue_value+=$row['issue_value'];
								$tot_used_qty+=$used_qty;
								$tot_used_value+=$used_value;
								$tot_issue_rtn_qty+=$issue_rtn_qty;
								$tot_issue_rtn_value+=$issue_rtn_value;
								$tot_balance_qnty+=$balance_qnty;
								$tot_balance_value+=$balance_value;
									
							}
						}
					}
					
					?>
				</tbody> 
				<tfoot>
					<tr >
						<td width="30">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="150">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100" align="right" style="font-size:18px; font-weight:bold">Total :</td>
						<td width="100" align="right" style="font-size:18px; font-weight:bold"><? echo number_format($tot_issue_qty,2); ?>&nbsp;</td>
						<td width="100" align="right" style="font-size:18px; font-weight:bold"><? echo number_format($tot_issue_value,2); ?>&nbsp;</td>
						<td width="100" align="right" style="font-size:18px; font-weight:bold"><? echo number_format($tot_used_qty,2); ?>&nbsp;</td>
						<td width="100" align="right" style="font-size:18px; font-weight:bold"><? echo number_format($tot_used_value,2); ?>&nbsp;</td>
						<td width="100" align="right" style="font-size:18px; font-weight:bold"><? echo number_format($tot_issue_rtn_qty,2); ?>&nbsp;</td>
						<td width="100" align="right" style="font-size:18px; font-weight:bold"><? echo number_format($tot_issue_rtn_value,2); ?>&nbsp;</td>
						<td width="100" align="right" style="font-size:18px; font-weight:bold"><? echo number_format($tot_balance_qnty,2); ?>&nbsp;</td>
						<td align="right" style="font-size:18px; font-weight:bold"><? echo number_format($tot_balance_value,2); ?>&nbsp;</td>
					</tr>
					<tr >
						<td width="30">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="150">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100" align="right" style="font-size:18px; font-weight:bold">Balance %</td>
						<td width="100" align="right" style="font-size:18px; font-weight:bold" title="( total balance qnty/total issue qty*100 )"><? echo number_format(($tot_balance_qnty/$tot_issue_qty)*100,2); ?>&nbsp;</td>
						<td align="right" style="font-size:18px; font-weight:bold" title="( total balance value/total issue value*100 )"><? echo number_format(($tot_balance_value/$tot_issue_value)*100,2); ?>&nbsp;</td>
					</tr>
				</tfoot>
			</table>
			</div>
			<br>
			<div style="width:1510px;">
			<table cellpadding="0" width="490" cellspacing="0" align="left" class="rpt_table" rules="all" border="1">
				<thead>
					<tr>
						<th colspan="6" style="font-size:10px; font-weight:bold">Issued Details</th>
					</tr>
					<tr>
						<th width="30" style="font-size:10px; font-weight:bold" >SL</th>
						<th width="80" style="font-size:10px; font-weight:bold">Date</th>
						<th width="100" style="font-size:10px; font-weight:bold">Issue ID</th>
						<th width="100" style="font-size:10px; font-weight:bold">Challan no.</th>
						<th width="90" style="font-size:10px; font-weight:bold">Issue Qty</th>
						<th width="90" style="font-size:10px; font-weight:bold">Issue Value</th>
					</tr>
				</thead>
				<tbody>
					<?
					$i=1;
					$total_issue_qty = $total_issue_value = 0;
					foreach ($issueInfoSummeryArr as $k_party=>$v_party) 
					{
						krsort($v_party);
						foreach ($v_party as $k_issue=>$row) 
						{
							//var_dump($row);
							//if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
							
							?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('rtr<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="rtr<? echo $i; ?>">
									<td width="30" style="word-break: break-all; font-size:9px; font-weight:normal" align="center"><? echo $i;?></td>
									<td width="80" style="word-break: break-all; font-size:9px; font-weight:normal"><? echo change_date_format($row['issue_date']);?></td>
									<td width="100" style="word-break: break-all; font-size:9px; font-weight:normal"><? echo $k_issue;?></td>
									<td width="100" style="word-break: break-all; font-size:9px; font-weight:normal"><? echo $row['challan_no'];?></td>
									<td width="90" style="word-break: break-all; font-size:9px; font-weight:normal" align="right"><? echo number_format($row['issue_qty'],2)?></td>
									<td width="90" style="word-break: break-all; font-size:9px; font-weight:normal" align="right"><? echo number_format($row['issue_value'],2)?></td>
								</tr>
							<?
							$i++;
							$total_issue_qty +=$row['issue_qty'];
							$total_issue_value +=$row['issue_value'];
						}
					}
					?>
					
				</tbody>
				<tfoot>
					<tr>
						<td colspan="4" align="right" style="word-break: break-all; font-size:9px; font-weight:bold">Total :</td>
						<td align="right" style="word-break: break-all; font-size:9px; font-weight:bold"><? echo number_format($total_issue_qty,2)?></td>
						<td align="right" style="word-break: break-all; font-size:9px; font-weight:bold"><? echo number_format($total_issue_value,2)?></td>
					</tr>
				</tfoot>
			</table>

			<br>
			
			<table width="490" cellspacing="0" border="1">
				<thead>
					<tr>
						<th colspan="6" style="font-size:9px; font-weight:bold">Receive Details</th>
					</tr>
					<tr>
						<th width="30" style="font-size:9px; font-weight:bold">SL</th>
						<th width="80" style="font-size:9px; font-weight:bold">Date</th>
						<th width="100" style="font-size:9px; font-weight:bold">MRR No.</th>
						<th width="100" style="font-size:9px; font-weight:bold">Challan no.</th>
						<th width="90" style="font-size:9px; font-weight:bold">Rcv. Qty</th>
						<th width="90" style="font-size:9px; font-weight:bold">Rcv. Value</th>
					</tr>
				</thead>
				<tbody>
					<?
					$i=1;
					$total_rcv_qty = $total_rcv_value = 0;
					foreach ($rcvInfoSummeryArr as $k_party=>$v_party) 
					{
						krsort($v_party);
						foreach ($v_party as $k_rcv=>$row) 
						{
							
							//var_dump($row);
							//if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
							
							?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('rtr<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="rtr<? echo $i; ?>">
									<td width="30" style="word-break: break-all; font-size:9px; font-weight:normal" align="center"><? echo $i;?></td>
									<td width="80" style="word-break: break-all; font-size:9px; font-weight:normal"><? echo change_date_format($row['receive_date']);?></td>
									<td width="100" style="word-break: break-all; font-size:9px; font-weight:normal"><? echo $k_rcv;?></td>
									<td width="100" style="word-break: break-all; font-size:9px; font-weight:normal"><? echo $row['challan_no'];?></td>
									<td width="90" style="word-break: break-all; font-size:9px; font-weight:normal" align="right"><? echo number_format($row['used_qty'],2)?></td>
									<td width="90" style="word-break: break-all; font-size:9px; font-weight:normal" align="right"><? echo number_format($row['used_value'],2)?></td>
								</tr>
							<?
							$i++;
							$total_rcv_qty +=$row['used_qty'];
							$total_rcv_value +=$row['used_value'];
							
						}
					}
					?>
					
				</tbody>
				<tfoot>
					<tr >
						<td colspan="4" align="right" style="font-size:9px; font-weight:bold">Total :</td>
						<td align="right" style="font-size:9px; font-weight:bold"><? echo number_format($total_rcv_qty,2)?></td>
						<td align="right" style="font-size:9px; font-weight:bold"><? echo number_format($total_rcv_value,2)?></td>
					</tr>
				</tfoot>
			</table>
			<br>

			<table cellpadding="0" width="490" cellspacing="0" align="left" class="rpt_table" rules="all" border="1">
				<thead>
					<tr>
						<th colspan="6" style="font-size:9px; font-weight:bold">Returned Details</th>
					</tr>
					<tr>
						<th width="30" style="font-size:9px; font-weight:bold">SL</th>
						<th width="80" style="font-size:9px; font-weight:bold">Date</th>
						<th width="100" style="font-size:9px; font-weight:bold">Return ID</th>
						<th width="100" style="font-size:9px; font-weight:bold">Challan no.</th>
						<th width="90" style="font-size:9px; font-weight:bold">Rtn. Qty</th>
						<th width="90" style="font-size:9px; font-weight:bold">Rtn. Value</th>
					</tr>
				</thead>
				<tbody>
					<?
					$i=1;
					$total_issue_rtn_qty = 0;
					$total_issue_rtn_value = 0;
					foreach ($issueRtnSummeryArr as $k_party=>$v_party) 
					{
						krsort($v_party);
						foreach ($v_party as $k_issue_rtn=>$row) 
						{
							
							//var_dump($row);
							//if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
							
							?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('rtr<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="rtr<? echo $i; ?>">
									<td width="30" style="word-break: break-all; font-size:9px; font-weight:normal" align="center"><? echo $i;?></td>
									<td width="80" style="word-break: break-all; font-size:9px; font-weight:normal"><? echo change_date_format($row['receive_date']);?></td>
									<td width="100" style="word-break: break-all; font-size:9px; font-weight:normal"><? echo $k_issue_rtn;?></td>
									<td width="100" style="word-break: break-all; font-size:9px; font-weight:normal"><? echo $row['challan_no'];?></td>
									<td width="90" style="word-break: break-all; font-size:9px; font-weight:normal" align="right"><? echo number_format($row['issue_rtn_qty'],2)?></td>
									<td width="90" style="word-break: break-all; font-size:9px; font-weight:normal" align="right"><? echo number_format($row['issue_rtn_value'],2)?></td>
								</tr>
							<?
							$i++;
							$total_issue_rtn_qty +=$row['issue_rtn_qty'];
							$total_issue_rtn_value +=$row['issue_rtn_value'];
							
						}
					}
					?>
					
				</tbody>
				<tfoot>
					<tr >
						<td colspan="4" align="right" style="font-size:9px; font-weight:bold">Total :</td>
						<td align="right" style="font-size:9px; font-weight:bold"><? echo number_format($total_issue_rtn_qty,2)?></td>
						<td align="right" style="font-size:9px; font-weight:bold"><? echo number_format($total_issue_rtn_value,2)?></td>
					</tr>
				</tfoot>
			</table>
			</div>
			
		</fieldset>        
		<?
	}

	foreach (glob("$user_id*.pdf") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	
	require('../../../../ext_resource/mpdf60/mpdf.php');
	$mpdf = new mPDF('', 'A4', '', '', 10, 10, 10, 35, 3, 3);	
	$mpdf->WriteHTML(ob_get_contents(),2);
	$user_id=$_SESSION['logic_erp']['user_id'];
	$REAL_FILE_NAME = $user_id.'.pdf';
	$file_path=$REAL_FILE_NAME;
	$mpdf->Output($file_path, 'F');

	exit();
}

