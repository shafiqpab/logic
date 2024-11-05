<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');

$user_name=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$buyer_arr = return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');


//--------------------------------------------------------------------------------------------------------------------

if ($action=="load_drop_down_location")
{
    extract($_REQUEST);

	echo create_drop_down( "cbo_location_id", 150, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id in($choosenCompany) order by location_name","id,location_name", 1, "--Select Location--", $selected, "",0 );
	exit();
}

if($action == "fabric_type_popup")
{
    echo load_html_head_contents("Fabric Type Info", "../../../", 1, 1, $unicode,1,1);

    extract($_REQUEST);
    ?>
        <script>
            var selected_id = new Array();
            var selected_name = new Array();

            function check_all_data()
            {
                var tbl_row_count = document.getElementById('table_body').rows.length;

                tbl_row_count = tbl_row_count - 1;
                for (var i = 1; i <= tbl_row_count; i++)
                {
                    js_set_value(i);
                }
            }

            function toggle(x, origColor)
            {
                var newColor = 'yellow';
                if (x.style) {
                    x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
                }
            }

            function js_set_value(str)
            {
                toggle(document.getElementById('search' + str), '#FFFFCC');

                if (jQuery.inArray($('#txt_individual_id' + str).val(), selected_id) == -1)
                {
                    selected_id.push($('#txt_individual_id' + str).val());
                    selected_name.push($('#txt_individual' + str).val());

                }
                else
                {
                    for (var i = 0; i < selected_id.length; i++)
                    {
                        if (selected_id[i] == $('#txt_individual_id' + str).val()) break;
                    }
                    selected_id.splice(i, 1);
                    selected_name.splice(i, 1);
                }

                var id = '';
                var name = '';
                for (var i = 0; i < selected_id.length; i++) {
                    id += selected_id[i] + ',';
                    name += selected_name[i] + ',';
                }

                id = id.substr(0, id.length - 1);
                name = name.substr(0, name.length - 1);

                $('#hidden_fabric_type_id').val(id);
                $('#hidden_fabric_type').val(name);
            }
        </script>
        </head>
        <fieldset style="width:390px">

            <input type="hidden" name="hidden_fabric_type" id="hidden_fabric_type" value="">
            <input type="hidden" name="hidden_fabric_type_id" id="hidden_fabric_type_id" value="">
            <table width="370" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
                <thead>
                    <tr>
                        <th width="50">SL</th>
                        <th width="50">Id</th>
                        <th width="">Fabric Type</th>
                    </tr>
                </thead>
            </table>
            <div style="width:390px; overflow-y:scroll; max-height:300px" id="scroll_body">
                <table width="370" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
                    <?

                    $determination_sql = sql_select("SELECT a.id,a.construction from lib_yarn_count_determina_mst a where  a.status_active=1 and a.is_deleted=0 ");
                    $i = 1;
                    foreach ($determination_sql as $row)
                    {
                        if ($i % 2 == 0)
                            $bgcolor = "#E9F3FF";
                        else
                            $bgcolor = "#FFFFFF";


                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="js_set_value(<? echo $i; ?>)" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>">
                            <td width="50">
                                <? echo $i; ?>
                                <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $row[csf("id")]; ?>" />
                                <input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $row[csf("construction")]; ?>" />
                            </td>
                            <td width="50">
                                <p><? echo $row[csf("id")]; ?></p>
                            </td>
                            <td width="">
                                <p title="<? echo $row[csf("id")];?>"><? echo $row[csf("construction")]; ?></p>
                            </td>
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
            setFilterGrid('table_body', -1);
        </script>
    <?
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$working_company = str_replace("'","",$cbo_working_company_id);
	$location_id     = str_replace("'","",$cbo_location_id);
	$fabric_type     = str_replace("'","",$txt_fabric_type);
	$fabric_type_id  = str_replace("'","",$txt_fabric_type_id);
	$year            = str_replace("'","",$cbo_year);
	$txt_date_from   = str_replace("'","",$txt_date_from);
	$txt_date_to     = str_replace("'","",$txt_date_to);
	$report_type     = str_replace("'","",$report_type);

    $companyArr = return_library_array("select id,company_name from lib_company", "id", "company_name");

	if ($working_company==0) $knitting_com_cond =""; else $knitting_com_cond =" and a.knitting_company in($working_company)";
	if ($location_id==0) $knitting_location_cond =""; else $knitting_location_cond =" and a.knitting_location_id in($location_id)";
	if ($fabric_type_id==0) $febric_description_id_cond =""; else $febric_description_id_cond =" and b.febric_description_id in($fabric_type_id)";
	if($db_type==0) $year_field_by="and YEAR(a.insert_date)"; else if($db_type==2) $year_field_by=" and to_char(a.insert_date,'YYYY')";
	if(trim($year)!=0) $year_cond =" $year_field_by=$year"; else $year_cond="";

	if($txt_date_from && $txt_date_to)
	{
		if($db_type==0)
		{
			$date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
			$date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
			$date_cond =" AND a.receive_date BETWEEN '$date_from' AND '$date_to'";
		}
		if($db_type==2)
		{
			$date_from=change_date_format($txt_date_from,'','',1);
			$date_to=change_date_format($txt_date_to,'','',1);
			$date_cond =" AND a.receive_date BETWEEN '$date_from' AND '$date_to'";
		}
	}

    $con = connect();
	$r_id1=execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_name." and ref_from in (1) and ENTRY_FORM = 172");
	if($r_id1)
	{
		oci_commit($con);
        disconnect($con);
	}

    $sql_knitting = "SELECT a.knitting_company, b.febric_description_id,
    SUM (CASE WHEN k.booking_type = 1 AND k.is_short = 2 THEN c.quantity ELSE 0 END) AS mainbookingqnty,
    SUM (CASE WHEN k.booking_type = 1 AND k.is_short = 1 THEN c.quantity ELSE 0 END) AS shortbookingqnty
    FROM wo_booking_mst k, ppl_planning_info_entry_mst h, ppl_planning_info_entry_dtls i, inv_receive_master a,pro_grey_prod_entry_dtls b, order_wise_pro_details c, wo_po_break_down d, wo_po_details_master e
    WHERE k.booking_no = h.booking_no AND h.id = i.mst_id AND i.id = a.booking_id AND k.booking_type IN (1) ANd k.is_short in(1,2) AND a.id = b.mst_id AND c.po_breakdown_id = d.id AND a.entry_form = 2 AND a.item_category = 13 AND b.id = c.dtls_id AND d.job_no_mst = e.job_no AND c.entry_form = 2 AND c.trans_type = 1 AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND a.receive_basis = 2 AND b.is_deleted = 0 AND c.status_active = 1 AND c.is_sales = 0 AND c.is_deleted = 0  $knitting_com_cond $knitting_location_cond $febric_description_id_cond $year_cond $date_cond group by a.knitting_company, b.febric_description_id"; //AND k.booking_no in('FAL-Fb-24-00046','FAL-Fb-24-00052')
    //echo $sql_knitting;
    $sql_knitting_rslt=sql_select($sql_knitting);

    if(count($sql_knitting_rslt)==0)
	{
		?>
		<div style="margin:20px auto; width: 90%">
			<div class="alert alert-error">
			  <strong>Data not found!</strong> Please try again.
			</div>
		</div>
		<?
		die();
	}

    $febDescIdChk = array();
    $all_febDescId = array();
    foreach($sql_knitting_rslt as $row)
    {
        if($febDescIdChk[$row[csf('febric_description_id')]] == "")
		{
			$febDescIdChk[$row[csf('febric_description_id')]] = $row[csf('febric_description_id')];
			$all_febDescId[$row[csf('febric_description_id')]] = $row[csf('febric_description_id')];
		}
    }

    if(!empty($all_febDescId))
	{
		fnc_tempengine("GBL_TEMP_ENGINE", $user_name, 172, 1,$all_febDescId, $empty_arr); //recv id
		//die;
        $sql_deter = "SELECT a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b, GBL_TEMP_ENGINE x where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=x.ref_val and x.user_id=$user_name and x.entry_form=172 and x.ref_from=1";
        //echo $sql_deter;die;
        $data_array = sql_select($sql_deter);
        if (count($data_array) > 0)
        {
            foreach ($data_array as $row)
            {
                if (array_key_exists($row[csf('id')], $composition_arr))
                {
                    $composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
                }
                else
                {
                    $composition_arr[$row[csf('id')]] = $row[csf('construction')] . "**" . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
                }
            }
        }
    }

    $con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_name." and ref_from in (1) and ENTRY_FORM=172");
	oci_commit($con);
	disconnect($con);

	ob_start();

    ?>
     <style>
        .wrd_brk{word-break: break-all;word-wrap: break-word;}
    </style>
    <div>
        <fieldset style="width:950px;">
            <table width="960" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="" align="left">
                <tr>
                    <td align="center" width="100%" colspan="8" class="form_caption" style="font-size:18px;border:none;"><? echo "Fabric Type Wise Knitting Production"; ?></td>
                </tr>
                <tr>
                    <td align="center" width="100%" colspan="8" class="form_caption" style="font-size:12px; border:none;" ><strong><? echo "From ".change_date_format(str_replace("'","",$txt_date_from))." To ".change_date_format(str_replace("'","",$txt_date_to)); ?></strong></td>

                </tr>
            </table>
            <br/>
            <div >
                <table class="rpt_table" width="960" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
                    <thead>
                        <tr>
                            <th width="30" class="wrd_brk">SL</th>
                            <th width="150" class="wrd_brk">Working Company</th>
                            <th width="180" class="wrd_brk">Fabric Type</th>
                            <th width="200" class="wrd_brk">Composition</th>
                            <th width="100" class="wrd_brk">Main Booking Knit.Pro.</th>
                            <th width="100" class="wrd_brk">Short Booking Knit. Pro.</th>
                            <th width="100" class="wrd_brk">Total Knit Production KG</th>
                            <th width="" class="wrd_brk">Short%</th>
                        </tr>
                    </thead>
                </table>
                <div style=" max-height:350px; width:980px; overflow-y:scroll;" id="scroll_body">
                    <table class="rpt_table" id="table_body" width="960" cellpadding="0" cellspacing="0" border="1" rules="all">
                        <tbody>
                            <?
                            $i=1;
                            $gt_mainbookingqnty=$gt_shortbookingqnty=$gt_knit_production=$gt_short_perc = 0;
                            foreach($sql_knitting_rslt as $row)
                            {
                                if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

                                $total_knit_production = $row[csf('mainbookingqnty')]+$row[csf('shortbookingqnty')];

                                $feb_desc = explode('**',$composition_arr[$row[csf('febric_description_id')]]);
                                $f_type = $feb_desc[0];
                                $f_desc = $feb_desc[1];

                                ?>
                                <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')">
                                    <td width="30" class="wrd_brk" align="center"><?=$i;?></td>
                                    <td width="150" class="wrd_brk" align="center"><?=$companyArr[$row[csf('knitting_company')]];?></td>
                                    <td width="180" class="wrd_brk" align="center" title="<?="Febric Description Id=".$row[csf('febric_description_id')];?>"><?=$f_type;?></td>
                                    <td width="200" class="wrd_brk" align="center" title="<?="Febric Description Id=".$row[csf('febric_description_id')];?>"><?=$f_desc;?></td>
                                    <td width="100" class="wrd_brk" align="right"><?=number_format($row[csf('mainbookingqnty')],2,'.','');?></td>
                                    <td width="100" class="wrd_brk" align="right"><?=number_format($row[csf('shortbookingqnty')],2,'.','');?></td>
                                    <td width="100" class="wrd_brk" align="right"><?=number_format($total_knit_production,2,'.','');?></td>
                                    <td width="" class="wrd_brk" align="right" title="{ Short*100)/Main }">
                                        <?
                                            $short_perc = ($row[csf('shortbookingqnty')]*100)/$row[csf('mainbookingqnty')];
                                            echo number_format($short_perc,2,'.','');
                                        ?>
                                    </td>
                                </tr>
                                <?
                                $i++;
                                $gt_mainbookingqnty +=$row[csf('mainbookingqnty')];
                                $gt_shortbookingqnty +=$row[csf('shortbookingqnty')];
                                $gt_knit_production +=$total_knit_production;
                                $gt_short_perc +=$short_perc;
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <table class="rpt_table" width="960" cellpadding="0" cellspacing="0" border="1" rules="all" >
                    <tfoot>
                        <tr>
                            <td width="30" class="wrd_brk">&nbsp;</td>
                            <td width="150" class="wrd_brk">&nbsp;</td>
                            <td width="180" class="wrd_brk">&nbsp;</td>
                            <td width="200" class="wrd_brk" align="right"><b>Total :</b></td>
                            <td width="100" class="wrd_brk" align="right" id="val_total_main_booking_qty"><?=number_format($gt_mainbookingqnty,2,'.','');?></td>
                            <td width="100" class="wrd_brk" align="right" id="val_total_short_booking_qty"><?=number_format($gt_shortbookingqnty,2,'.','');?></td>
                            <td width="100" class="wrd_brk" align="right" id="val_total_knitting_production_qty"><?=number_format($gt_knit_production,2,'.','');?></td>
                            <td width="" class="wrd_brk" align="right" id="val_total_short_perc"><?=number_format($gt_short_perc,2,'.','');?></td>

                        </tr>
                    </tfoot>
                </table>
            </div>
        </fieldset>
    </div>
    <?

    $html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
        @unlink($filename);
    }
    $name=time();
    $filename=$user_name."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html####$filename####$report_type";
    exit();
}


?>