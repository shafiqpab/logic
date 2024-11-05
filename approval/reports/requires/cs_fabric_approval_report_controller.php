<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");
require_once('../../../includes/common.php');
$user_id = $_SESSION['logic_erp']['user_id'];
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];


if($action=='cs_no_popup')
{
	echo load_html_head_contents("Supplier List", "../../../", 1, 1, '','','');
	extract($_REQUEST);
    $ex_data=explode('_',$data);
    $company_id = $ex_data[0];
    $cs_year    = $ex_data[1];
    $year_cond="";
    if ($cs_year>0) $year_cond=" and to_char(a.insert_date,'YYYY')=".trim($cs_year);
    
	?>
	<script>

		$(document).ready(function(e) {
			setFilterGrid('tbl_list_search',-1);
		});

		function js_set_value( str )
		{
			var id = $('#txt_individual_id' + str).val();
			var no= $('#txt_individual_name' + str).val();
			$('#hidden_cs_id').val(id);
			$('#hidden_cs_no').val(no);
			parent.emailwindow.hide();
		}
    </script>

	</head>
	<?
	$result = sql_select("SELECT a.id as ID, a.sys_number as CS_NO from req_comparative_mst a where a.company_name=$company_id and a.entry_form=512 and a.status_active=1 and a.is_deleted=0 $year_cond order by a.id desc");
	?>
	<body>
	<div align="center">
		<fieldset style="width:370px;margin-left:10px">
	    	<input type="hidden" name="hidden_cs_id" id="hidden_cs_id">
	    	<input type="hidden" name="hidden_cs_no" id="hidden_cs_no">
	        <form name="searchprocessfrm_1"  id="searchprocessfrm_1" autocomplete="off">
	            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="350" class="rpt_table" >
	                <thead>
	                    <th width="50">SL</th>
	                    <th>CS No</th>
	                </thead>
	            </table>
	            <div style="width:350px; overflow-y:scroll; max-height:300px;" id="buyer_list_view" align="center">
	                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="332" class="rpt_table" id="tbl_list_search" align="left">
	                <?
	                    $i=1;
	                    foreach($result as $row)
	                    {
	                        if ($i%2==0) $bgcolor='#E9F3FF'; 
	                        else $bgcolor='#FFFFFF';
	                        ?>
	                        <tr bgcolor="<?= $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?= $i;?>" onClick="js_set_value(<?= $i; ?>)">
                                <td width="50" align="center"><?php echo "$i"; ?>
                                    <input type="hidden" name="txt_individual_id" id="txt_individual_id<?= $i ?>" value="<?= $row['ID']; ?>"/>
                                    <input type="hidden" name="txt_individual_name" id="txt_individual_name<?= $i ?>" value="<?= $row['CS_NO']; ?>"/>
                                </td>
                                <td><p><?= $row['CS_NO']; ?></p></td>
	                        </tr>
	                        <?
	                        $i++;
	                    }
	                ?>
	                </table>
	            </div>
	        </form>
	    </fieldset>
	</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action == "report_generate") 
{
    $process = array(&$_POST);
    extract(check_magic_quote_gpc($process));

    $cbo_company_name = str_replace("'", "", $cbo_company_name);
    $cbo_item_category_id = str_replace("'", "", $cbo_item_category_id);
    $cbo_year = str_replace("'", "", $cbo_year);
    $txt_cs_no = str_replace("'", "", $txt_cs_no);
    $cbo_date_type = str_replace("'", "", $cbo_date_type);
    $cbo_type = str_replace("'", "", $cbo_type);
    $txt_date_from = str_replace("'", "", $txt_date_from);
    $txt_date_to = str_replace("'", "", $txt_date_to);

    $sql_cond="";
    if ($cbo_company_name > 0) $sql_cond .= " and a.company_name = $cbo_company_name";
    if ($cbo_year > 0) $sql_cond .= " and to_char(a.insert_date,'YYYY') = $cbo_year";
    if ($txt_cs_no != "") $sql_cond .= " and a.sys_number like '%$txt_cs_no%'";
    if ($txt_date_from!="" && $txt_date_to!=""){
        if ($cbo_date_type==1) $sql_cond .= " and a.cs_date between '".$txt_date_from."' and '".$txt_date_to."'";
        else $sql_cond .= " and d.approved_date between '".$txt_date_from."' and '".$txt_date_to." 11:59:59 PM'";
    }    

    if ($cbo_type == 3) $sql_cond .= " and a.approved=1";
    elseif ($cbo_type == 2) $sql_cond .= " and a.approved=3";
    else $sql_cond .= " and a.approved in(0,2)";

    
    $sql_com_res=sql_select("SELECT id as ID, company_name as COMPANY_NAME, company_short_name as COMPANY_SHORT_NAME from lib_company");
    foreach($sql_com_res as $row){
        $company_arr[$row['ID']]['name'] = $row['COMPANY_NAME'];
        $company_arr[$row['ID']]['short_name'] = $row['COMPANY_SHORT_NAME'];
    }
    $buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
    $supplier_arr = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
    $designation_array = return_library_array("SELECT id, custom_designation from lib_designation", "id", "custom_designation");

    $user_name_array = array();
    $userData = sql_select("SELECT id as ID, user_name as USER_NAME, user_full_name as USER_FULL_NAME, designation as DESIGNATION FROM user_passwd where valid=1");
    foreach ($userData as $user_row) {
        $user_name_array[$user_row['ID']]['name'] = $user_row['USER_NAME'];
        $user_name_array[$user_row['ID']]['full_name'] = $user_row['USER_FULL_NAME'];
        $user_name_array[$user_row['ID']]['designation'] = $designation_array[$user_row['DESIGNATION']];
    }
    unset($userData);

    $signatory_sql_res = sql_select("SELECT user_id as USER_ID, sequence_no as SEQUENCE_NO, bypass as BYPASS from electronic_approval_setup where entry_form=57 and is_deleted=0  order by sequence_no"); //company_id=$cbo_company_name
    $signatory_data_arr=array();
    foreach ($signatory_sql_res as $sval) {
        $signatory_data_arr[$sval['USER_ID']]['user_id'] = $sval['USER_ID'];
        $signatory_data_arr[$sval['USER_ID']]['bypass'] = $sval['BYPASS'];
    }
    unset($signatory_sql_res);
    //echo '<pre>';print_r($signatory_data_arr);
   
    if($cbo_date_type==1)
    {
        $sql = "SELECT a.id as MST_ID, a.company_name as COMPANY_ID, a.sys_number as CS_NO, cs_date as CS_DATE, to_char(a.insert_date,'YYYY') as CS_YEAR, a.inserted_by as INSERT_BY, b.id as DTLS_ID, b.item_description as ITEM_DESC, b.req_qty as REQ_QTY, b.req_rate as REQ_RATE, b.uom as UOM, b.job_id as JOB_ID, c.id as SUPP_DTLS_ID, c.supp_id as SUPP_ID, c.quoted_price as QUOTED_PRICE, c.neg_price as NEG_PRICE, c.con_price as CON_PRICE, c.supp_type as SUPP_TYPE
        FROM req_comparative_mst a, req_comparative_dtls b, req_comparative_supp_dtls c
        WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=512 and b.item_category_id=3 $sql_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by a.cs_date desc";
    }
    else
    {
        $sql = "SELECT a.id as MST_ID, a.company_name as COMPANY_ID, a.sys_number as CS_NO, cs_date as CS_DATE, to_char(a.insert_date,'YYYY') as CS_YEAR, a.inserted_by as INSERT_BY, b.id as DTLS_ID, b.item_description as ITEM_DESC, b.req_qty as REQ_QTY, b.req_rate as REQ_RATE, b.uom as UOM, b.job_id as JOB_ID, c.id as SUPP_DTLS_ID, c.supp_id as SUPP_ID, c.quoted_price as QUOTED_PRICE, c.neg_price as NEG_PRICE, c.con_price as CON_PRICE, c.supp_type as SUPP_TYPE, d.approved_date as APPROVED_DATE
        FROM req_comparative_mst a, req_comparative_dtls b, req_comparative_supp_dtls c, approval_history d
        WHERE a.id=b.mst_id and b.id=c.dtls_id and a.id=d.mst_id and a.entry_form=512 and b.item_category_id=3 and d.entry_form=57 and d.current_approval_status=1 $sql_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.company_name, a.sys_number, cs_date, to_char(a.insert_date,'YYYY'), a.inserted_by, b.id, b.item_description, b.req_qty, b.req_rate, b.uom, b.job_id, c.id, c.supp_id, c.quoted_price, c.neg_price, c.con_price, c.supp_type, d.approved_date order by a.cs_date desc";
    }
    //echo $sql;die;
    $sql_result = sql_select($sql);
    $mst_id_arr=array();
    $job_id_arr=array();
    foreach ($sql_result as $row) {
        if ($row['JOB_ID'] > 0){
            $job_id_arr[$row['JOB_ID']] = $row['JOB_ID'];
            $mst_id_arr[$row['JOB_ID']]=$row['MST_ID'];
        }
        $mst_data_keys=$row['MST_ID'].'__'.$row['COMPANY_ID'].'__'.$row['CS_NO'].'__'.$row['CS_DATE'].'__'.$row['CS_YEAR'].'__'.$row['INSERT_BY'].'__'.$row['APPROVED_DATE'];
        $dtls_data_keys=$row['DTLS_ID'].'__'.$row['ITEM_DESC'].'__'.$row['REQ_QTY'].'__'.$row['REQ_RATE'].'__'.$row['UOM'].'__'.$row['JOB_ID'];
        $all_data_arr[$mst_data_keys][$dtls_data_keys][$row['SUPP_DTLS_ID']]['supp_id']=$row['SUPP_ID'];
        $all_data_arr[$mst_data_keys][$dtls_data_keys][$row['SUPP_DTLS_ID']]['quoted_price']=$row['QUOTED_PRICE'];
        $all_data_arr[$mst_data_keys][$dtls_data_keys][$row['SUPP_DTLS_ID']]['neg_price']=$row['NEG_PRICE'];
        $all_data_arr[$mst_data_keys][$dtls_data_keys][$row['SUPP_DTLS_ID']]['con_price']=$row['CON_PRICE'];
        $all_data_arr[$mst_data_keys][$dtls_data_keys][$row['SUPP_DTLS_ID']]['supp_type']=$row['SUPP_TYPE'];
        
    }
    //echo '<pre>';print_r($all_data_arr);

    $con = connect();
    $rID=execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form in (9) and ref_from in(1)");
    if ($rID) oci_commit($con);
    if(!empty($job_id_arr))
	{
	 	fnc_tempengine("gbl_temp_engine", $user_id, 9, 1, $job_id_arr, $empty_arr);
        $sql_job="SELECT a.id as JOB_ID, a.job_no_prefix_num as JOB_NO_PREFIX_NUM, a.style_ref_no as STYLE_REF_NO, a.buyer_name as BUYER_NAME from wo_po_details_master a, gbl_temp_engine b where a.id=b.ref_val and a.is_deleted=0 and b.user_id=$user_id and b.entry_form=9 and b.ref_from=1";
        $sql_job_res=sql_select($sql_job);
        $job_info_arr=array();
        foreach ($sql_job_res as $row)
        {
            $job_info_arr[$mst_id_arr[$row['JOB_ID']]]['job_no_prefix_num'].=$row['JOB_NO_PREFIX_NUM'].', ';
            $job_info_arr[$mst_id_arr[$row['JOB_ID']]]['style_ref_no'].=$row['STYLE_REF_NO'].', ';
            $job_info_arr[$mst_id_arr[$row['JOB_ID']]]['buyer_name']=$row['BUYER_NAME'];// multiple job but buyer same
        }
        unset($sql_job_res);
	}
    
    //echo '<pre>';print_r($job_info_arr);disconnect($con);die;
    $rID2=execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form in (9) and ref_from in(1)");
    if ($rID2) oci_commit($con);
    disconnect($con);
    $width = 1850;

    $rowspan_mst_arr=array();
    foreach ($all_data_arr as $all_mst_data => $dtls_data)
    {
        $mstId_key=explode('__',$all_mst_data);
        foreach ($dtls_data as $all_dtls_data => $supp_dtls_data)
        {
            $rowspan_mst_arr[$mstId_key[0]] += count($supp_dtls_data);
        }
    }
    //echo '<pre>';print_r($rowspan_mst_arr);
    ob_start();
    ?>
    <fieldset style="width:<?= $width + 20; ?>px;">
        <table cellpadding="0" cellspacing="0" width="<?= $width; ?>">
            <tr>
                <td align="center" width="100%" colspan="9" style="font-size:20px"><strong><?= $report_title; ?></strong></td>
            </tr>
        </table>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?= $width; ?>" class="rpt_table" align="left">
            <thead>
                <th width="35">SL</th>
                <th width="60">Company</th>
                <th width="120">Buyer Name</th>
                <th width="80">Job No</th>
                <th width="120">Style Name</th>
                <th width="50">CS Year</th>
                <th width="70">CS No</th>
                <th width="70">CS Date</th>
                <th width="150">Items Description</th>
                <th width="80">Req. Qty.</th>
                <th width="60">UOM</th>
                <th width="80">Costing Price</th>
                <th width="80">Quoted Price</th>
                <th width="80">Last Price</th>
                <th width="80">Neg. Price</th>
                <th width="150">Supplier Name</th>
                <th width="80">CS Insert By</th>
                <th width="80">Signatory</th>
                <th width="100">Designation</th>
                <th width="50">Can Bypass</th>
                <th width="70">Approval Date</th>
                <th>Approval Time</th>
            </thead>
        </table>
        <div style="width:<?= $width + 20; ?>px; overflow-y:scroll; max-height:310px;" id="scroll_body">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?= $width; ?>" class="rpt_table" align="left">
                <tbody>
                    <?
                    $i = 1;                    
                    foreach ($all_data_arr as $all_mst_data => $dtls_data)
                    {
                        $mst_data=explode("__",$all_mst_data);                                               
                        $mst_id     = $mst_data[0];
                        $company_id = $mst_data[1];
                        $cs_no      = $mst_data[2];
                        $cs_date    = $mst_data[3];
                        $cs_year    = $mst_data[4];
                        $insert_by  = $mst_data[5];
                        $approved_date  = $mst_data[6];
                        $rowspan_mst = $rowspan_mst_arr[$mst_id];
                        $rowspan_dtls="";
                        foreach ($dtls_data as $all_dtls_data => $supp_dtls_data)
                        {
                            $dtls_data=explode("__",$all_dtls_data);
                            $rowspan_dtls = count($supp_dtls_data);
                            $dtls_id    = $dtls_data[0];
                            $item_desc  = $dtls_data[1];
                            $req_qty    = $dtls_data[2];
                            $req_rate   = $dtls_data[3];
                            $uom        = $dtls_data[4];
                            $job_id     = $dtls_data[5];

                            foreach ($supp_dtls_data as $supp_dtls_id => $row)
                            {                               
                                $bgcolor=($i % 2 == 0) ? "#E9F3FF" : "#FFFFFF";
                                if ($row['supp_type']==1) $comp_supp_name=$supplier_arr[$row['supp_id']];
                                else $comp_supp_name=$company_arr[$row['supp_id']]['name'];
                                ?>
                                <tr bgcolor="<?= $bgcolor; ?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor; ?>')" id="tr_<?= $i; ?>">                           
                                    <? 
                                    if ($check_mst_arr[$mst_id]=="")
                                    {
                                        ?>
                                        <td width="35" rowspan="<? echo $rowspan_mst; ?>" align="center"><? echo $i; ?></td>
                                        <td width="60" rowspan="<? echo $rowspan_mst; ?>" align="center"><? echo $company_arr[$company_id]['short_name']; ?></td>
                                        <td width="120" rowspan="<? echo $rowspan_mst; ?>"><? echo $buyer_arr[$job_info_arr[$mst_id]['buyer_name']]; ?></td>
                                        <td width="80" rowspan="<? echo $rowspan_mst; ?>"><? echo rtrim($job_info_arr[$mst_id]['job_no_prefix_num'],', '); ?></td>
                                        <td width="120" rowspan="<? echo $rowspan_mst; ?>"><? echo rtrim($job_info_arr[$mst_id]['style_ref_no'],', '); ?></td>
                                        <td width="50" rowspan="<? echo $rowspan_mst; ?>" align="center"><? echo $cs_year; ?></td>
                                        <td width="70" rowspan="<? echo $rowspan_mst; ?>" align="center"><a href='##' onClick="generate_report('<? echo $mst_id; ?>','<? echo $company_id; ?>')"><b><? echo $cs_no; ?></b></a></td>
                                        <td width="70" rowspan="<? echo $rowspan_mst; ?>" align="center"><? echo change_date_format($cs_date); ?></td>                                       
                                        <?
                                        $check_mst_arr[$mst_id]=$mst_id;
                                    }
                                    if ($check_dtls_arr[$dtls_id]=="")
                                    {
                                        ?>
                                        <td width="150" rowspan="<? echo $rowspan_dtls; ?>"><? echo $item_desc; ?></td>
                                        <td width="80" rowspan="<? echo $rowspan_dtls; ?>" align="right"><? echo number_format($req_qty,4); ?></td>
                                        <td width="60" rowspan="<? echo $rowspan_dtls; ?>" align="center"><? echo $unit_of_measurement[$uom]; ?></td>
                                        <td width="80" rowspan="<? echo $rowspan_dtls; ?>" align="right"><? echo number_format($req_rate,2); ?></td>
                                        <?
                                        $check_dtls_arr[$dtls_id]=$dtls_id;
                                    }
                                    ?>
                                    <td width="80" align="center"><? echo $row['quoted_price']; ?></td>
                                    <td width="80" align="center"><? echo $row['con_price']; ?></td>
                                    <td width="80" align="center"><? echo $row['neg_price']; ?></td>
                                    <td width="150" ><? echo $comp_supp_name; ?></td>
                                    <?
                                    if ($check_mst_arr2[$mst_id]=="")
                                    {
                                        ?>
                                        <td width="80" rowspan="<? echo $rowspan_mst; ?>" align="center"><? echo $user_name_array[$insert_by]['name']; ?></td>
                                        <td rowspan="<? echo $rowspan_mst; ?>">
                                            <table width="100%" cellspacing="0" cellpadding="0" border="1" rules="all"  >
                                                <?
                                                $approval_date=$approval_time="";
                                                if ($approved_date !=""){
                                                    $approval_date=date('d-m-Y', strtotime($approved_date));
                                                    $approval_time=date('h:i:s A', strtotime($approved_date));
                                                }
                                                foreach ($signatory_data_arr as $val)
                                                {
                                                    ?>
                                                    <tr>
                                                        <td width="80" ><? echo $user_name_array[$val['user_id']]['name']; ?></td>
                                                        <td width="100"><? echo $user_name_array[$val['user_id']]['designation']; ?></td>
                                                        <td width="50"><? echo $yes_no[$val['bypass']]; ?></td>
                                                        <td width="70"><? echo $approval_date; ?></td>
                                                        <td ><? echo $approval_time; ?></td>
                                                    </tr>                                            
                                                    <?
                                                }
                                                ?>
                                            </table>
                                        </td>
                                        <?
                                        $check_mst_arr2[$mst_id]=$mst_id;
                                    }
                                    ?>                                                                       
                                </tr>
                                <?                                
                            }
                        }
                        $i++;
                    }
                    ?>                   
                </tbody>
            </table>
        </div>
    </fieldset>
    <?
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	exit(); 	
}
//----------------END--------------

?>