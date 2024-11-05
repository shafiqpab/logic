<? 
/*-------------------------------------------- Comments -----------------------
GBL ENTRY FROM	: 	
REF FORM	    :	
*/

header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
$user_id=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action']; 

// ============================================================================
//                           CUSTOM FUNCTIONS 
// ============================================================================
if (!function_exists('pre')) 
{
  function pre($array)
  {
    echo "<pre>";
    print_r($array);
    echo "</pre>";
  }
}
if (!function_exists('num_format')) 
{
  function is_num($num)
  {
    return (is_infinite($num) || is_nan($num)) ? 0 : $num;
  }
}

// ============================================================================
//                              DROP DOWNS 
// ============================================================================
if ($action=="load_drop_down_buyer")
{  
    extract($_REQUEST); 
	echo create_drop_down( "cbo_buyer_name", 150, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($choosenCompany) and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  $buyer_cond group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "- All Buyer -", $selected, "" );     	 
	exit();
}
if ($action=="load_drop_down_location")
{
  extract($_REQUEST); 
  echo create_drop_down( "cbo_location_id", 150, "SELECT id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id in( $choosenCompany) group by id,location_name  order by location_name","id,location_name", 0, "-- Select location --", $selected, "" );
  exit();
}
if($action=="load_drop_down_wash_company")
{
	$explode_data = explode("**",$data);
	$data = $explode_data[0];
	$selected_company = $explode_data[1];

	if($data==3)
	{
        echo create_drop_down( "cbo_emb_company", 150, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=23 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "--Select--", 0, "" );	
	}
	else if($data==1)
		echo create_drop_down( "cbo_emb_company", 150, "select id,company_name from lib_company comp where is_deleted=0 and status_active=1 $company_cond order by company_name","id,company_name", 1, "--- Select ---", "", "load_drop_down( 'requires/wash_send_and_received_challan_controller', this.value, 'load_drop_down_wash_location', 'wash_location_td' );",0,0 );
	else
		echo create_drop_down( "cbo_emb_company", 170, $blank_array,"", 1, "--- Select ---", $selected, "",0 );

	exit();
}

if ($action == "load_drop_down_wash_location") 
{
    echo create_drop_down( "cbo_wash_location", 150, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "",0 );
}


/*
|--------------------------------------------------------------------------
| job_no_popup
|--------------------------------------------------------------------------
|
*/
if($action	==	"job_style_popup")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);  
	?>

	<script>
		var selected_id = new Array();
		var selected_name = new Array();
		var selected_attach_id = new Array();
		function toggle( x, origColor )
		{
			var newColor = 'yellow';
			if ( x.style )
			{
				x.style.backgroundColor = ( newColor == x.style.backgroundColor ) ? origColor : newColor;
			}
		}

		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ )
			{
				$('#tr_'+i).trigger('click');
			}
		}

		function js_set_value(id,popupFor)
		{ 
			var str=id.split("_");
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
			var strdt=str[2];
			str=str[1];

			if( jQuery.inArray(  str , selected_id ) == -1 ) {
				selected_id.push( str );
				selected_name.push( strdt );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str  ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i,1 );
			}
			var id = '';
			var ddd='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				ddd += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			ddd = ddd.substr( 0, ddd.length - 1 );
			$('#hide_job_id').val( id );
			$('#hide_job_no').val( ddd );
			$('#hide_popup_for').val(popupFor);
		}

	</script>
    </head>
    <body>
        <div align="center">
            <form name="styleRef_form" id="styleRef_form">
                <fieldset style="width:580px;">
                    <table width="570" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
                        <thead>
                            <th>Buyer</th>
                            <th>Job Year</th>
                            <th>Search By</th>
                            <th id="search_by_td_up" width="170">Please Enter <?= $popupFor == 1 ? "Job No" : "Style Ref" ?></th>
                            <th>
                                <input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');">
                                <input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                                <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                                <input type="hidden" name="hide_popup_for" id="hide_popup_for" value="" />
                            </th>
                        </thead>
                        <tbody>
                            <tr>
                                <td align="center">
                                    <?php
                                    $type = 1;
                                    if($type == 1)
                                        $party="1,3,21,90";
                                    else
                                        $party="80";
										
									//is_disabled
									$is_disabled = ($buyer_name != 0 ? '1' : '0');

                                    echo create_drop_down( "cbo_buyer_name", 140, "SELECT buy.id, buy.buyer_name FROM lib_buyer buy, lib_buyer_tag_company b WHERE buy.status_active =1 AND buy.is_deleted=0 AND b.buyer_id=buy.id AND b.tag_company IN (".$companyID.") AND buy.id IN (SELECT buyer_id FROM lib_buyer_party_type WHERE party_type IN (".$party.")) GROUP BY buy.id, buy.buyer_name ORDER BY buy.buyer_name","id,buyer_name", "1", "-- All Buyer--",$buyer_name, "", $is_disabled);
                                    ?>
                                </td>
                                <td>
                                    <?
                                        echo create_drop_down( "txt_job_year", 80, $year,"", 1, "-- Select year --", date('Y'), "","");
                                    ?>
                                </td> 
                                <td align="center">
                                    <? 
									$search_by_arr=array(1=>"Job No",2=>"Style Ref"); 
                                    $dd="change_search_event(this.value, '0*0', '0*0', '../') ";
                                    echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", $popupFor,$dd,0 );
                                    ?>
                                </td>
                                <td align="center" id="search_by_td">
									
                                    <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
                                </td>
                                <td align="center">
                                    <input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<?php echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_job_year').value+'**'+'<? echo $cbo_month_id; ?>'+'**'+'<? echo $party; ?>'+'**'+'<?= $popupFor?>', 'create_job_no_search_list_view', 'search_div', 'wash_send_and_received_challan_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div style="margin-top:15px" id="search_div"></div>
                </fieldset>
            </form>
        </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
} 
/*
|--------------------------------------------------------------------------
| create_job_no_search_list_view
|--------------------------------------------------------------------------
|
*/
if($action	==	"create_job_no_search_list_view")
{
	$data = explode('**',$data);
	$company_id = $data[0];
	$year_id = $data[4];
	$month_id = $data[5];
	$party = $data[6];
	/*
	|--------------------------------------------------------------------------
	| buyer checking
	|--------------------------------------------------------------------------
	|
	*/
	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="")
				$buyer_id_cond=" AND buyer_name IN (".$_SESSION['logic_erp']["buyer_id"].")";
			else
				$buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" AND buyer_name = ".$data[1]."";
	}

	$search_by = $data[2];
	$search_string = "%".trim($data[3])."%";

	if($search_by == 2)
		$search_field = "style_ref_no";
	else
		$search_field = "job_no";

	if($db_type == 0)
	{
		if($year_id != 0)
			$year_search_cond = " AND year(insert_date) = ".$year_id."";
		else
			$year_search_cond = "";
		$year_cond = "year(insert_date) AS year";
	}
	else if($db_type==2)
	{
		if($year_id != 0)
			$year_search_cond = " AND TO_CHAR(insert_date,'YYYY') = ".$year_id."";
		else
			$year_search_cond="";
		$year_cond = "TO_CHAR(insert_date,'YYYY') AS year";
	}
	
   
	$company_arr = return_library_array( "SELECT id, company_name FROM lib_company WHERE id IN(".$company_id.")", "id", "company_name" );
	$buyer_arr = return_library_array( "SELECT buy.id, buy.buyer_name FROM lib_buyer buy, lib_buyer_tag_company b WHERE buy.status_active =1 AND buy.is_deleted=0 AND b.buyer_id=buy.id AND b.tag_company IN (".$company_id.") AND buy.id IN (SELECT buyer_id FROM lib_buyer_party_type WHERE party_type IN (".$party.")) GROUP BY buy.id, buy.buyer_name ORDER BY buy.buyer_name","id", "buyer_name");
	$arr = array (0=>$company_arr, 1=>$buyer_arr);
	
	$sql= "SELECT id, job_no, job_no_prefix_num, company_name, buyer_name, style_ref_no, ".$year_cond." FROM wo_po_details_master WHERE status_active=1 AND is_deleted=0 AND company_name IN(".$company_id.") AND ".$search_field." LIKE '".$search_string."' ".$buyer_id_cond." ".$year_search_cond." ".$month_cond." ORDER BY job_no DESC";
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,80,80,60","620","270",0, $sql , "js_set_value", "id,job_no_prefix_num", "$popupFor", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0,0','',1) ;
	exit();
} 
// ============================================================================
//                              REPORTS 
// ============================================================================
if ($action=='report_generate') 
{  
    extract($_POST);
    // pre($_POST); die;

    $company_id     = str_replace("'","",$cbo_company_id);
    $location_id    = str_replace("'","",$cbo_location_id);
    $year           = str_replace("'","",$cbo_year); 
    $buyer_name     = str_replace("'","",$cbo_buyer_name); 
    $report_basis   = str_replace("'","",$cbo_report_basis); 
    $source         = str_replace("'","",$cbo_source); 
    $sending_comp   = str_replace("'","",$cbo_sending_comp); 
    $emb_company    = str_replace("'","",$cbo_emb_company); 
    $wash_location  = str_replace("'","",$cbo_wash_location); 
    $job_no         = str_replace("'","",$txt_job_no); 
    $date_from      = str_replace("'","",$txt_date_from); 
    $date_to        = str_replace("'","",$txt_date_to); 
    // echo $date_from ; die;
    // ============================================================================================================
    //												Library 
    // ============================================================================================================ 

    if ($type==1) //Show
    {
       
        $buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  ); 
        $company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
        $location_library=return_library_array( "select id,location_name from lib_location", "id", "location_name"  );
        $wash_comp_arr=$company_library;
        if($source ==3) //Out-bound
        {
            $wash_comp_arr = return_library_array("select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=23 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id","supplier_name" );
 
        } 
        // =========================================================================================================
                                                //ISSUE OR RECEIVE DATA DATA
        // =========================================================================================================

        $cond_sql ="";
        $cond_sql .= $company_id    ?" and e.company_name in($company_id) " :"";
        $cond_sql .= $location_id   ?" and c.location_id in($location_id) " :"";
        $cond_sql .= $year          ?" and to_char(e.insert_date,'YYYY')='$year' " :"";
        $cond_sql .= $buyer_name    ?" and e.buyer_name=$buyer_name" :"";
        $cond_sql .= $report_basis  ?" and a.production_type=$report_basis " :"";
        $cond_sql .= $source        ?" and a.production_source=$source " :"";
        $cond_sql .= $sending_comp  ?" and a.sending_company=$sending_comp " :"";
        $cond_sql .= $emb_company   ?" and a.serving_company=$emb_company " :"";
        $cond_sql .= $wash_location ?" and a.location=$wash_location " :"";
        $cond_sql .= $job_no        ?" and e.job_no_prefix_num in($job_no) " :"";
        $cond_sql .= ($date_from && $date_to) ?" and a.production_date between '".change_date_format($date_from,'dd-mm-yyyy','-',1)."' and '".change_date_format($date_to,'dd-mm-yyyy','-',1)."'"  : ""; 
        // echo $cond_sql; die;

        $sql="select a.serving_company as wash_comp,a.location as wash_location,a.production_date as prod_date,b.production_qnty as prod_qty,c.id as sys_id, c.sending_company,c.sys_number,a.embel_type as wash_type,d.po_number,d.id as po_id,e.id as job_id,e.company_name as lc_comp,e.job_no,e.style_ref_no as style,e.buyer_name,to_char(e.insert_date,'YYYY') as job_year from pro_garments_production_mst a, pro_garments_production_dtls b,pro_gmts_delivery_mst c,wo_po_break_down d, wo_po_details_master e where a.id=b.mst_id and c.id=a.delivery_mst_id and a.po_break_down_id=d.id and d.job_id=e.id $cond_sql and a.embel_name=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 order by c.id asc"; 
        $sql_res = sql_select($sql); 
        if (count($sql_res) == 0 ) {
        echo "<h1 style='color:red; font-size: 17px;text-align:center;margin-top:20px;'> ** Data Not Found ** </h1>" ;
        die();
        }
        $data_arr = array();
        foreach ($sql_res as $v) 
        {
            $data_arr[$v['SYS_ID']][$v['BUYER_NAME']][$v['JOB_ID']][$v['PO_ID']][$v['WASH_TYPE']]['LC_COMP']        = $v['LC_COMP'];
            $data_arr[$v['SYS_ID']][$v['BUYER_NAME']][$v['JOB_ID']][$v['PO_ID']][$v['WASH_TYPE']]['SENDING_COMPANY']= $v['SENDING_COMPANY'];
            $data_arr[$v['SYS_ID']][$v['BUYER_NAME']][$v['JOB_ID']][$v['PO_ID']][$v['WASH_TYPE']]['SYS_NUMBER']     = $v['SYS_NUMBER'];
            $data_arr[$v['SYS_ID']][$v['BUYER_NAME']][$v['JOB_ID']][$v['PO_ID']][$v['WASH_TYPE']]['PROD_DATE']      = $v['PROD_DATE'];
            $data_arr[$v['SYS_ID']][$v['BUYER_NAME']][$v['JOB_ID']][$v['PO_ID']][$v['WASH_TYPE']]['JOB_YEAR']       = $v['JOB_YEAR'];
            $data_arr[$v['SYS_ID']][$v['BUYER_NAME']][$v['JOB_ID']][$v['PO_ID']][$v['WASH_TYPE']]['JOB_NO']         = $v['JOB_NO'];
            $data_arr[$v['SYS_ID']][$v['BUYER_NAME']][$v['JOB_ID']][$v['PO_ID']][$v['WASH_TYPE']]['STYLE']          = $v['STYLE'];
            $data_arr[$v['SYS_ID']][$v['BUYER_NAME']][$v['JOB_ID']][$v['PO_ID']][$v['WASH_TYPE']]['PO_NUMBER']      = $v['PO_NUMBER'];
            $data_arr[$v['SYS_ID']][$v['BUYER_NAME']][$v['JOB_ID']][$v['PO_ID']][$v['WASH_TYPE']]['WASH_TYPE']      = $v['WASH_TYPE'];
            $data_arr[$v['SYS_ID']][$v['BUYER_NAME']][$v['JOB_ID']][$v['PO_ID']][$v['WASH_TYPE']]['WASH_COMP']      = $v['WASH_COMP'];
            $data_arr[$v['SYS_ID']][$v['BUYER_NAME']][$v['JOB_ID']][$v['PO_ID']][$v['WASH_TYPE']]['WASH_LOCATION']  = $v['WASH_LOCATION'];
            $data_arr[$v['SYS_ID']][$v['BUYER_NAME']][$v['JOB_ID']][$v['PO_ID']][$v['WASH_TYPE']]['PROD_QTY']       += $v['PROD_QTY'];
        }
        // pre($data_arr); die;
        // ============================================================================
        //                             ROW SPAN CALCULATION
        // ============================================================================
        foreach ($data_arr as $sys_id => $sys_arr) 
        {
            foreach ($sys_arr as $buyer_id => $buyer_arr) 
            {	  
                foreach ($buyer_arr as $job_id => $job_arr) 
                {
                    foreach ($job_arr as $po_id => $po_arr) 
                    {  
                        foreach ($po_arr as $wash_type => $v) 
                        {  
                            $sys_row_span_arr[$sys_id] ++;
                            $buyer_row_span_arr[$sys_id][$buyer_id] ++;
                            $job_row_span_arr[$sys_id][$buyer_id][$job_id] ++;
                            $po_row_span_arr[$sys_id][$buyer_id][$job_id][$po_id] ++;
                            $wash_row_span_arr[$sys_id][$buyer_id][$job_id][$po_id][$wash_type] ++;
                        }
                    }   
                } 
            }  
        } 
        $width = 1310 ; 
        ?>
        <style>
        
        tbody tr th{
            border: 1px solid #8DAFDA;
        }
        </style>
        <fieldset style= "width:<? echo $width+20;?>px;"> 
            <table width="100%" cellspacing="0"> 
            <table width="<? echo $width;?>" cellpadding="0" cellspacing="0" id="tbl_caption" align="center">
                <thead class="form_caption" >
                    <tr>
                        <td colspan="22" align="center" style="font-size:18px; font-weight:bold; padding: 10px 0 10px 0;" >Wash Send and Received Challan</td>
                    </tr>  
                </thead>
            </table>	
            <div align="center" style= "width:<? echo $width+20;?>px; margin:0 auto; padding:0;">  
                <table border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="<? echo $width;?>" rules="all" id="rpt_table_header" align="left">
					<thead class="form_caption" >	 
                        <tr>	 	 	 	 	 	
                            <th width="60">Check Box</th>
                            <th width="40">Sl No.</th>
                            <th width="110">Lc Company</th>
                            <th width="80">Buyer Name</th>
                            <th width="110"><?= $report_basis==2 ? "Sending " : "Receiving " ?>Company</th>
                            <th width="100">System ID</th>
                            <th width="80"><?= $basis_arr[$report_basis] ?>. Date</th>
                            <th width="50">Job Year</th>
                            <th width="80">Job No </th>
                            <th width="120">Style Ref. </th>
                            <th width="100">Order No. </th> 
                            <th width="100">Wash. Type </th> 
                            <th width="110">Wash Company </th>
                            <th width="110">Location </th>
                            <th width="60"><?= $basis_arr[$report_basis] ?>.Qty </th>
                        </tr>	
                    </thead>
                </table>
                <div style="width:<?= $width+20;?>px; max-height:250px; float:left; overflow-y:scroll;" id="scroll_body">
                    <table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body" width="<?= $width; ?>" rules="all" align="left">
                        <tbody>
                            <?
                            $i = 0 ; 
                            $total_issue_qty=0;
                            foreach ($data_arr as $sys_id => $sys_arr) 
                            {
                                $a=1;
                                foreach ($sys_arr as $buyer_id => $buyer_arr) 
                                {	
                                    $b=1;  
                                    foreach ($buyer_arr as $job_id => $job_arr) 
                                    {
                                        $c=1;
                                        foreach ($job_arr as $po_id => $po_arr) 
                                        {  
                                            $d=1;
                                            foreach ($po_arr as $wash_type => $v) 
                                            { 
                                                // rowspan 
                                                $sys_rowspan        = $sys_row_span_arr[$sys_id]; 
                                                $buyer_rowspan      = $buyer_row_span_arr[$sys_id][$buyer_id];
                                                $job_rowspan        = $job_row_span_arr[$sys_id][$buyer_id][$job_id];
                                                $po_rowspan         = $po_row_span_arr[$sys_id][$buyer_id][$job_id][$po_id];
                                                if ($i % 2 == 0)  $bgcolor = "#E9F3FF";  else $bgcolor = "#FFFFFF";
                                                ?>
                                                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                                                        <? 
                                                            if ($a==1) 
                                                            {
                                                                ?>
                                                                    <td valign="middle" rowspan="<?= $sys_rowspan ?>" align="center" width="60">
                                                                        <input type="checkbox" id="tbl_<? echo ++$i; ?>"  onClick="fnc_checkbox_check(<? echo $i; ?>);"  />

                                                                        <input type="hidden" id="sys_id_<? echo $i; ?>" value="<?= $sys_id; ?>" />
                                                                        <input type="hidden" id="issue_to_<? echo $i; ?>" value="<?=$v['SENDING_COMPANY'] ; ?>" />
                                                                        <input type="hidden" id="lc_comp_<? echo $i; ?>" value="<?=$v['LC_COMP'] ; ?>" />
                                                                        <input type="hidden" id="wash_comp_<? echo $i; ?>" value="<?=$v['WASH_COMP'] ; ?>" />
                                                                    </td>
                                                                    <td valign="middle" rowspan="<?= $sys_rowspan ?>" width="40" align="center"><?= $i; ?></td>
                                                                    <td valign="middle" rowspan="<?= $sys_rowspan ?>" width="110"><?= $company_library[$v['LC_COMP']]; ?></td> 
                                                                <?   
                                                            } 
                                                            if ($b==1) 
                                                            {
                                                                ?>
                                                                    <td valign="middle" rowspan="<?= $buyer_rowspan ?>" width="80"><?= $buyer_library[$buyer_id] ?></td>
                                                                <?   
                                                            } 
                                                            if ($a==1) 
                                                            {
                                                                ?>
                                                                    <td valign="middle" rowspan="<?= $sys_rowspan ?>" width="110"><?= $company_library[$v['SENDING_COMPANY']]; ?></td>
                                                                    <td valign="middle" rowspan="<?= $sys_rowspan ?>" width="100"><?= $v['SYS_NUMBER']; ?></td>
                                                                    <td valign="middle" rowspan="<?= $sys_rowspan ?>" width="80"><?= $v['PROD_DATE'] ?></td> 
                                                                <?   
                                                            }
                                                            if ($c==1) 
                                                            {
                                                                ?>
                                                                    <td valign="middle" rowspan="<?= $job_rowspan ?>" width="50" align="center"><?= $v['JOB_YEAR']; ?></td>
                                                                    <td valign="middle" rowspan="<?= $job_rowspan ?>" width="80"><?= $v['JOB_NO']; ?> </td>
                                                                    <td valign="middle" rowspan="<?= $job_rowspan ?>" width="120"><?= $v['STYLE']; ?> </td>
                                                                <?   
                                                            }
                                                            if ($d==1) 
                                                            {
                                                                ?>   
                                                                    <td valign="middle" rowspan="<?= $po_rowspan ?>" width="100"><?= $v['PO_NUMBER']; ?> </td> 
                                                                <?   
                                                            }
                                                        ?>    
                                                        <td width="100"><?= $emblishment_wash_type[$wash_type]; ?> </td> 
                                                        <td width="110"><?= $wash_comp_arr[$v['WASH_COMP']]; ?> </td>
                                                        <td width="110"><?= $location_library[$v['WASH_LOCATION']]; ?> </td>     
                                                        <td width="60" align="right"><?= $v['PROD_QTY']; ?></td>
                                                    </tr> 
                                                <?
                                                $a++;$b++;$c++;$d++;
                                                $total_issue_qty+=$v['PROD_QTY'];
                                            }  
                                        }
                                    }
                                }              	
                            }
                            
                            ?>
                        </tbody>
                    </table> 
                </div> 
                <div style="width:<?= $width+20;?>px;float:left;">
					<table style="float:left;" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all" width="<?= $width;?>" id="" >
						<tfoot>
                            <tr>	 	 	 	 	 	
                                <th width="60"></th>
                                <th width="40"></th>
                                <th width="110"></th>
                                <th width="80"></th>
                                <th width="110"></th>
                                <th width="100"></th>
                                <th width="80"></th>
                                <th width="50"></th>
                                <th width="80"> </th>
                                <th width="120"></th>
                                <th width="100"></th> 
                                <th width="100"></th> 
                                <th width="110"></th>
                                <th width="110">Total:</th>
                                <th width="60"><? echo $total_issue_qty;?></th>
                            </tr>	
						</tfoot>
					</table>
				</div>
            </div>
        </fieldset>
        <? 
    }

    foreach (glob($user_id."_*.xls") as $filename)
	{		
		@unlink($filename);
	}
	$name=$user_id."_".time().".xls";
	$create_new_excel = fopen($name, 'w');	
	$is_created = fwrite($create_new_excel,ob_get_contents());
	echo "####".$name;
	exit();
}

// ============================================================================
//                              Print Button 
// ============================================================================
	
if($action=="delivery_challan_print")
{
    $data=explode('*', $data);
    // echo"<pre>" ;print_r($data);die;
    $prod_type = $data[0];
    $sys_ids=implode(',',explode("_",$data[1])); 
    $title = $data[2];
    $delivery_date = $data[3];
	//print_r ($data);
    $image_arr = return_library_array( "SELECT master_tble_id,image_location FROM common_photo_library WHERE form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$company_library = return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$supplier_library = return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name"  );
	$buyer_library = return_library_array( "select id, short_name from   lib_buyer", "id", "short_name"  );
	$address_arr = return_library_array( "select id, location_name from lib_location",'id','location_name');
	$color_library = return_library_array( "select id, color_name from lib_color",'id','color_name');
	$size_library = return_library_array( "select id, size_name from lib_size",'id','size_name');
	$supplier_address = return_library_array( "select id,address_1 from  lib_supplier", "id","address_1"  );
    
	$sql = "SELECT a.embel_type,a.item_number_id as item,a.sending_company,a.sending_location,a.serving_company as wash_comp, a.location as wash_location, a.entry_break_down_type as prod_variable,b.production_qnty as prod_qty,c.id as po_id,c.po_number,d.color_number_id as color,d.size_number_id,e.company_name as lc_company,e.buyer_name,e.job_no,e.id as job_id,e.style_ref_no,f.id as sys_id,f.sys_number,f.production_source as source,f.delivery_date,a.remarks FROM pro_garments_production_mst a,pro_garments_production_dtls b,wo_po_break_down c,wo_po_color_size_breakdown d,wo_po_details_master e,pro_gmts_delivery_mst f WHERE a.id=b.mst_id and a.delivery_mst_id=f.id and a.po_break_down_id=c.id and c.id=d.po_break_down_id and e.id=c.job_id and b.color_size_break_down_id=d.id and f.id in($sys_ids) and a.production_type=$prod_type  and a.embel_name=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 order by a.id,d.size_number_id";
	// echo $sql;die;
	$sql_res =sql_select($sql);
    if (count($sql_res) == 0 ) {
        echo "<h1 style='color:red; font-size: 17px;text-align:center;margin-top:20px;'> ** Data Not Found ** </h1>" ;
		die();
	}
	$size_arr = array();
	$prod_arr = array();
	$data_arr = array();
	$total_arr = array();
	$color_width =0;
	foreach ($sql_res as  $v) 
	{
		$lc_company			= $v['LC_COMPANY'];
        $wash_comp			= $v['WASH_COMP'];
		$wash_location		= $v['WASH_LOCATION'];
		$sending_company 	= $v['SENDING_COMPANY'];
		$sending_location 	= $v['SENDING_LOCATION']; 
		$source 			= $v['SOURCE'];
		$prod_variable 		= $v['PROD_VARIABLE'];
		
		$data_arr[$v['SYS_ID']][$v['BUYER_NAME']][$v['JOB_ID']][$v['PO_ID']][$v['EMBEL_TYPE']][$v['ITEM']][$v['COLOR']]['LC_COMPANY'] 	= $v['LC_COMPANY'];
		$data_arr[$v['SYS_ID']][$v['BUYER_NAME']][$v['JOB_ID']][$v['PO_ID']][$v['EMBEL_TYPE']][$v['ITEM']][$v['COLOR']]['SYS_NUMBER'] 	= $v['SYS_NUMBER'];
		$data_arr[$v['SYS_ID']][$v['BUYER_NAME']][$v['JOB_ID']][$v['PO_ID']][$v['EMBEL_TYPE']][$v['ITEM']][$v['COLOR']]['JOB_NO'] 		= $v['JOB_NO'];
		$data_arr[$v['SYS_ID']][$v['BUYER_NAME']][$v['JOB_ID']][$v['PO_ID']][$v['EMBEL_TYPE']][$v['ITEM']][$v['COLOR']]['STYLE_REF_NO'] = $v['STYLE_REF_NO'];
		$data_arr[$v['SYS_ID']][$v['BUYER_NAME']][$v['JOB_ID']][$v['PO_ID']][$v['EMBEL_TYPE']][$v['ITEM']][$v['COLOR']]['PO_NUMBER'] 	= $v['PO_NUMBER']; 
		$data_arr[$v['SYS_ID']][$v['BUYER_NAME']][$v['JOB_ID']][$v['PO_ID']][$v['EMBEL_TYPE']][$v['ITEM']][$v['COLOR']]['ISS_QTY'] 		+= $v['PROD_QTY'];
        $data_arr[$v['SYS_ID']][$v['BUYER_NAME']][$v['JOB_ID']][$v['PO_ID']][$v['EMBEL_TYPE']][$v['ITEM']][$v['COLOR']]['REMARKS'] = $v['REMARKS'];
        
		if ($prod_variable ==2) 	//color Level
		{
            $color_width = 100;
		}
		elseif ($prod_variable ==3) //color and Size Level
		{
            $size_arr[$v['SIZE_NUMBER_ID']]=$v['SIZE_NUMBER_ID'];
			$data_arr[$v['SYS_ID']][$v['BUYER_NAME']][$v['JOB_ID']][$v['PO_ID']][$v['EMBEL_TYPE']][$v['ITEM']][$v['COLOR']][$v['SIZE_NUMBER_ID']]['ISS_QTY'] += $v['PROD_QTY'];
			$total_arr[$v['SIZE_NUMBER_ID']] += $v['PROD_QTY'];
			$color_width = 100;
		}
		
        
	}
    $size_ids = implode(',',$size_arr);
    $prod_size_array = return_library_array( "select id, size_name from lib_size where id in ($size_ids) order by sequence asc,id asc",'id','id');

    if($source == 1){
		$company = $company_library[$wash_comp];
		$wash_address = $address_arr[$wash_location];
	}else if($source == 3){
		$company = $supplier_library[$wash_comp];
		$wash_address = "";
	}

    //echo $lc_company;die;
    
    $basis_arr = array(2=>"Issue",3=>"Receive");
    // ============================================================================
    //                             ROW SPAN CALCULATION
    // ============================================================================
    foreach ($data_arr as $sys_id => $sys_arr) 
    {
        foreach ($sys_arr as $buyer_id => $buyer_arr) 
        {	  
            foreach ($buyer_arr as $job_id => $job_arr) 
            {
                foreach ($job_arr as $po_id => $po_arr) 
                {  
                    foreach ($po_arr as $wash_type => $wash_type_arr) 
                    { 
                        foreach ($wash_type_arr as $item_id => $item_arr) 
                        { 
                            foreach ($item_arr as $color_id => $v) 
                            {
                                $sys_row_span_arr[$sys_id] ++;
                                $buyer_row_span_arr[$sys_id][$buyer_id] ++;
                                $job_row_span_arr[$sys_id][$buyer_id][$job_id] ++;
                                $po_row_span_arr[$sys_id][$buyer_id][$job_id][$po_id] ++;
                                $wash_row_span_arr[$sys_id][$buyer_id][$job_id][$po_id][$wash_type] ++;
                                $item_row_span_arr[$sys_id][$buyer_id][$job_id][$po_id][$wash_type][$item_id] ++;
                            }  
                        }  
                    }
                }   
            } 
        }  
    }	 
	$size_count = count($size_arr);
	$width = 1250+$color_width+($size_count*50);
    echo load_html_head_contents($title, "../", 1, 1,'','','');
  
    $lastID = sql_select("SELECT max(ID) as id FROM common_photo_library WHERE form_name = 'group_logo'");
    $imageGroupLogo = sql_select("select id, image_location from common_photo_library where id='".$lastID[0]['ID']."' and form_name='group_logo'");

    // echo $sql="select id, image_location from common_photo_library where id='".$lastID[0]['ID']."' and form_name='group_logo'";
 
     ?>

    <style>
        #print_table td{
            font-size: 15px;
        }
        </style>
	<div style="width:<?= $width+20?>px;">
		<div style="display: flex;"> 
			<div style="text-align:center;width:<?= $width-100?>px">
				<p style="font-size:22px;margin:0;"><strong><? echo $company_library[$sending_company]; ?></strong></p>
				<p style="font-size:16px;margin:0;"><?= $address_arr[$sending_location]?></p>
                <?
                if($prod_type==2)
                {
                    ?>
                    <p style="font-size:18px;margin:0;"><strong> Issue   Delivery  Challan </strong></p>
                    <?
                }else 
                {
                    ?>
                    <p style="font-size:18px;margin:0;"><strong> Receive  Delivery  Challan </strong></p>
                    <?
                }
                ?>
			</div> 
		</div>	
			<div>
                <tr>
                    <td align="left">
                        <?php

                            $imageGroupLogo = sql_select("select master_tble_id, image_location from common_photo_library where master_tble_id='".$lc_company."' and form_name='company_details' and file_type=1");

                            foreach($imageGroupLogo as $image_row)
                            {
                            // echo '<img src="../../../'.$image_path[csf('image_location')].'" style="width:80px;height:80px;margin:3px;border:1px solid #BBB; border-radius:3px;" /> ';     
                        ?>
                            <img src='../../<? echo $image_row[csf('image_location')]; ?>' height='60' width='200' align="middle" />
                        <?
                        }
                        ?>
                    </td>
                </tr>
                <div style="display: flex; margin-left:30px;">
			        <div style="text-align:center;width:<?= ($width*0.75)?>px;">
                        <table>
                            <tr>
                                <td style="font-size:20px"><strong>Wash Company</strong> </td>
                                <td align="left" ><strong>: </strong> <?= $company; ?></td>
                            </tr>	
                            <tr>
                                <td  style="font-size:20px"><strong>Address</strong> </td>
                                <td align="left"><strong>: </strong> <?= $wash_address; ?></td>
                            </tr> 
                        </table>
			        </div> 


			        <div style="width:<?= ($width*0.25)?>px">
                        <table>
                            <tr>
                                <td style="font-size:20px;margin-left:30px;"><strong>Delivery Date</strong></td>
                                <td><strong>: </strong> <?=$delivery_date?></td>
                            </tr>	
                            <tr>
                                <td style="font-size:20px;margin-left:30px;"><strong>Source</strong></td>
                                <td><strong>: </strong> <?= $knitting_source[$source] ?></td>
                            </tr> 
                        </table>
			        </div> 
                </div>
            </div>
            
		<table width="<?= $width+20?>" cellspacing="0" align="right" border="1" cellspacing="0"  style="margin-top: 30px;" id="print_table">
			<thead>
				<tr>
					<th width="30">SL</th>
					<th width="120">Lc Company</th>
					<th width="140">System ID</th>
					<th width="100">Buyer Name</th>
					<th width="100">Job No</th>
					<th width="130">Style Ref.</th>
					<th width="100">Order No.</th>
					<th width="100">Wash.Type</th>
					<th width="100">Item</th>
					<?
                    if ( in_array($prod_variable,[2,3])  ) 
                    {
                        ?>
                            <th width="100">Color/Size</th>
                        <?	
                    }
					?>
					<?
                    if ($prod_variable==3) //Color Size Level
                    {
                        foreach ($prod_size_array as $size_id) 
                        { 
                            ?>
                                <th width="50"><?= $size_library[$size_id] ?></th>
                            <?
                        } 
                    }
					?> 
                    <?
                    if($prod_type==2)
                    {
                        ?>
                        <th width="70">Issue.Qty</th> 
                        <?
                    }else 
                    {
                        ?>
                        <th width="70">Receive.Qty</th> 
                        <?
                    }
                    ?>
					<!-- <th width="70">Issue.Qty</th>  -->
                    <th width="150" style="word-wrap:break-word; word-break: break-all;">Remarks</th> 
				</tr>
			</thead>
			<tbody>
				<?
                $i=1;
                $total_prod_qty =0;
                foreach ($data_arr as $sys_id => $sys_arr) 
                {
                    $a=1;
                    foreach ($sys_arr as $buyer_id => $buyer_arr) 
                    {	
                        $b=1;
                        foreach ($buyer_arr as $job_id => $job_arr) 
                        {
                            $c=1;
                            foreach ($job_arr as $po_id => $po_arr) 
                            {
                                $d=1;
                                foreach ($po_arr as $wash_type => $wash_type_arr) 
                                {
                                    $e=1;
                                    foreach ($wash_type_arr as $item_id => $item_arr) 
                                    {
                                        $f=1;
                                        foreach ($item_arr as $color_id => $v) 
                                        {
                                            $total_prod_qty +=$v['ISS_QTY'];

                                            // rowspan 
                                            $sys_rowspan        = $sys_row_span_arr[$sys_id]; 
                                            $buyer_rowspan      = $buyer_row_span_arr[$sys_id][$buyer_id];
                                            $job_rowspan        = $job_row_span_arr[$sys_id][$buyer_id][$job_id];
                                            $po_rowspan         = $po_row_span_arr[$sys_id][$buyer_id][$job_id][$po_id];
                                            $wash_type_rowspan  = $wash_row_span_arr[$sys_id][$buyer_id][$job_id][$po_id][$wash_type];
                                            $item_rowspan       = $item_row_span_arr[$sys_id][$buyer_id][$job_id][$po_id][$wash_type][$item_id];
                                            ?>
                                                <tr>
                                                    
                                                    <? 
                                                        if($a==1) 
                                                        {
                                                            ?>
                                                                <td rowspan="<?=$sys_rowspan ?>" align="center"><?=$i; ?></td>
                                                                <td rowspan="<?=$sys_rowspan ?>"> <?= $company_library[$v['LC_COMPANY']] ?></td> 
                                                                <td rowspan="<?=$sys_rowspan ?>" > <?= $v['SYS_NUMBER'] ?> </td> 
                                                            <?  
                                                        }
                                                        if($b==1)
                                                        {
                                                            ?>
                                                                <td rowspan="<?=$buyer_rowspan  ?>" > <?= $buyer_library[$buyer_id] ?> </td> 
                                                            <?  
                                                        }
                                                        if($c==1)
                                                        {
                                                            ?>
                                                                <td rowspan="<?=$job_rowspan  ?>" > <?= $v['JOB_NO'] ?> </td> 
                                                                <td rowspan="<?=$job_rowspan  ?>" > <?= $v['STYLE_REF_NO'] ?> </td> 
                                                            <?  
                                                        }
                                                        if($d==1)
                                                        {
                                                            ?> 
                                                                <td rowspan="<?=$po_rowspan  ?>" > <?= $v['PO_NUMBER'] ?> </td>
                                                            <?  
                                                        }
                                                        if($e==1)
                                                        {
                                                            ?>   
                                                                <td rowspan="<?=$wash_type_rowspan  ?>" > <?= $emblishment_wash_type[$wash_type] ?> </td>  
                                                            <?  
                                                        }
                                                        if($f==1)
                                                        {
                                                            ?> 
                                                                <td  rowspan="<?=$item_rowspan  ?>" > <?= $garments_item[$item_id] ?> </td>
                                                                <?  
                                                        } 
                                                    ?>   
                                                    <?
                                                        if ( in_array($prod_variable,[2,3])  ) 
                                                        {
                                                            ?>
                                                                <td><?= $color_library[$color_id] ?></td>  
                                                            <?	
                                                        }
                                                    ?>
                                                    <?
                                                        if ($prod_variable==3) //Color Size Level
                                                        {
                                                            foreach ($prod_size_array as $size) 
                                                            {
                                                                $size_qty = $v[$size]['ISS_QTY'];
                                                                ?>
                                                                    <th align="center"><?=$size_qty?></th>
                                                                <?
                                                            }
                                                                
                                                        }
                                                    ?>
                                                    <th align="center"><?= $v['ISS_QTY']?> </th>
                                                    <td style="word-wrap:break-word; word-break: break-all;"><?=$v["REMARKS"];?> </td>
                                                </tr>
                                            <?
                                            $a++;$b++;$c++;$d++;$e++;$f++;$i++;
                                        }  
                                    } 
                                }
                            } 
                        } 
                    } 
                }
				?>	
			</tbody>
			<tfoot>
				<th colspan="8"></th>
				<th >Total</th>
				<?
					if ( in_array($prod_variable,[2,3])  ) 
					{
						?>
							<th></th>
						<?	
					}
				?>
				<?
					if ($prod_variable==3) //Color Size Level
					{
						foreach ($size_arr as $size) 
						{
							$ttlsize_qty = $total_arr[$size];
							?>
								<th align="center"><?=$ttlsize_qty?></th>
							<?
						}
							
					}
				?>
				<th align="center"><?= $total_prod_qty ?></th>
                <th align="center"><? ?></th>
			</tfoot>
            <caption style="caption-side:bottom; text-align: left; margin-top: 20px;"><b>In Words:</b> <span><?php echo number_to_words($total_prod_qty)." Pcs"; ?></span></caption>
		</table>
        <?
        echo signature_table(323, $lc_company, "990px");
        ?>
	</div>
	<?
	exit();
}
?>
?>