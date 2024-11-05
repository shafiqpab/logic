<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');

if($action=="batch_no_creation")
{
	$batch_no_creation=return_field_value("batch_no_creation","variable_settings_production","company_name ='$data' and variable_list=24 and is_deleted=0 and status_active=1");

	if($batch_no_creation!=1) $batch_no_creation=0;
	
	echo "document.getElementById('batch_no_creation').value 				= '".$batch_no_creation."';\n";
	echo "$('#txt_batch_number').val('');\n";
	echo "$('#update_id').val('');\n";
	if($batch_no_creation==1)
	{
		echo "$('#txt_batch_number').attr('readonly','readonly');\n";
	}
	else
	{
		echo "$('#txt_batch_number').removeAttr('readonly','readonly');\n";
	}
	exit();	
}

if ($action == "company_wise_report_button_setting")
{
	extract($_REQUEST);
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."'  and module_id=20 and report_id=100 and is_deleted=0 and status_active=1");

	$print_report_format_arr=explode(",",$print_report_format);
	echo "$('#print').hide();\n";
	
	if($print_report_format != "")
	{
		foreach($print_report_format_arr as $id)
		{
			if($id==78){echo "$('#print').show();\n";}			
		}
	}	
	exit();
}

if ($action=="load_drop_down_working_com")
{
	echo create_drop_down( "cbo_working_company", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/sweater_batch_creation_controller', this.value, 'load_drop_down_location', 'working_location_td' )",0);
	exit();	 
}

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_working_location", 140, "select id, location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "",0 );	
	exit();
}

if($action == "load_drop_down_sub_operation")
{
	switch ($data) {
		case "1":
			//echo "Your favorite color is red!";
			$load_data = '1,2';
			break;
		case "2":
			$load_data = '1,2,3,4,5';
			break;
		case "3":
			$load_data = '3,4,5';
			break;
			case "4":
			$load_data = '3,4,5';
			break;
		default:
			$load_data = '';
	}
	echo create_drop_down( "cbo_sub_operation",140, $wash_sub_operation_arr,"","", "", 0, "",'',$load_data,'','','',9);
	exit();
}

if ($action == "bundle_popup") 
{
    extract($_REQUEST);
    echo load_html_head_contents("Bundle Popup Info", "../../../", 1, 1, $unicode);
    ?>
    <script>

        function check_all_data() {
            var tbl_row_count = document.getElementById('tbl_list_search').rows.length;
            for (var i = 1; i <= tbl_row_count; i++) {
                if ($("#search" + i).css("display") != 'none') {
                    js_set_value(i);
                }
            }
        }
        var selected_id = new Array();

        function toggle(x, origColor) {
            var newColor = 'yellow';
            if (x.style) {
                x.style.backgroundColor = ( newColor == x.style.backgroundColor ) ? origColor : newColor;
            }
        }

        function js_set_value(str)
		{ 
			var tdColor=$('#tdColor_'+str).text();
			var tdcolorid=$("#tdColor_"+str).attr('colorid');
			
			if ( trim(tdColor)!="" && $('#hidden_color').val()!="" && trim(tdColor)!=$('#hidden_color').val() )
			{
				alert('Color Mixing Not Allowed')
				return;
			}
			
            toggle(document.getElementById('search' + str), '#FFFFCC');
			$('#hidden_color').val( tdColor );
			$('#hidden_color_id').val( tdcolorid );

            if (jQuery.inArray($('#txt_individual' + str).val(), selected_id) == -1) {
                selected_id.push($('#txt_individual' + str).val());
            }
            else {
                for (var i = 0; i < selected_id.length; i++) {
                    if (selected_id[i] == $('#txt_individual' + str).val()) break;
                }
                selected_id.splice(i, 1);
            }
            var id = '';
            for (var i = 0; i < selected_id.length; i++) {
                id += selected_id[i] + ',';
            }
            id = id.substr(0, id.length - 1);

            $('#hidden_bundle_nos').val(id);
        }

        function fnc_close() {
            document.getElementById('hidden_source_cond').value=document.getElementById('source_cond').value;
            parent.emailwindow.hide();
        }

        function reset_hide_field() {
            $('#hidden_bundle_nos').val('');
			$('#hidden_color').val('');
			$('#hidden_color_id').val('');
            selected_id = new Array();
        }

    </script>
    </head>
    <body>
    <div align="center" style="width:100%;">
        <form name="searchwofrm" id="searchwofrm">
            <fieldset style="width:810px;">
                <legend>Enter search words <input type="checkbox" value="1" name="is_exact" id="is_exact" >is exact</legend>
                <table cellpadding="0" cellspacing="0" width="550" border="1" rules="all" class="rpt_table">
                    <thead>
                        <th width="100">Bundle No</th>
                        <th width="100">QR Code No</th>
                        <th width="60">Job Year</th>
                        <th width="80">Job No</th>
                        <th width="100">Style</th>
                        <th width="100">Order No</th>
                        <th width="100" class="must_entry_caption">Lot Ratio No</th>
                        <th>
                            <input type="reset" name="reset" id="reset" value="Reset" style="width:70px" class="formbutton"/>
                            <input type="hidden" name="hidden_bundle_nos" id="hidden_bundle_nos">
                            <input type="hidden" name="hidden_color" id="hidden_color">
                            <input type="hidden" name="hidden_color_id" id="hidden_color_id" value="<?=$colorId; ?>">
                            <input type="hidden" name="hidden_source_cond" id="hidden_source_cond"> 
                        </th>
                    </thead>
                    <tr class="general">
                    	<td><input type="text" name="txtbundle_no" id="txtbundle_no" style="width:90px" class="text_boxes"/> </td>
                        <td><input type="text" name="txt_qr_code" id="txt_qr_code" style="width:90px" class="text_boxes"/> </td>
                        <td><? echo create_drop_down( "cbo_job_year", 60, $year,'', "", '-- Select --',date("Y",time()), "",'','','','' ); ?></td>               
                        <td><input type="text" style="width:70px" class="text_boxes" name="txt_job_no" id="txt_job_no"/></td>
                        <td><input type="text" style="width:90px" class="text_boxes" name="txt_style_ref" id="txt_style_ref"/></td>
                        <td><input type="text" style="width:90px" class="text_boxes" name="txt_order_no" id="txt_order_no"/></td>
                        <td><input type="text" name="txt_lot_ratio_no" id="txt_lot_ratio_no" style="width:90px" class="text_boxes"/></td>
                        <td>
                        <input type="button" name="button2" class="formbutton" value="Show"
                        onClick="show_list_view( $('#txt_order_no').val()+'_'+'<? echo $company_id; ?>'+'_'+$('#txtbundle_no').val()+'_'+'<? echo $bundleNo; ?>'+'_'+$('#txt_job_no').val()+'_'+$('#cbo_job_year').val()+'_'+$('#txt_style_ref').val()+'_'+$('#txt_lot_ratio_no').val()+'_'+$('#txt_qr_code').val()+'_'+$('#is_exact').is(':checked')+'_'+$('#hidden_color_id').val(), 'create_bundle_search_list_view', 'search_div', 'sweater_batch_creation_controller', 'setFilterGrid(\'tbl_list_search\',-1);reset_hide_field();');" style="width:100px;"/>
                        </td>
                    </tr>
                </table>
                <div style="width:100%; margin-top:5px; margin-left:10px" id="search_div" align="left"></div>
            </fieldset>
        </form>
    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if($action=="populate_mst_data")
{
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$data=explode("_",$data);
	$sql_pop="SELECT a.id, a.sys_number_prefix_num, a.sys_number, a.company_id, a.location_id, a.production_source, a.working_company_id, a.working_location_id, a.floor_id, c.color_number_id
    FROM pro_gmts_delivery_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c 
    where a.entry_form=383 and b.entry_form=383 and a.id=b.delivery_mst_id and b.color_size_break_down_id=c.id and b.barcode_no='$data[0]' and a.company_id='$data[1]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$sql_pop_res=sql_select($sql_pop);
	foreach($sql_pop_res as $row)
    {
		echo $row[csf('company_id')].'_'.$row[csf('location_id')].'_'.$row[csf('production_source')].'_'.$row[csf('working_location_id')].'_'.$row[csf('working_location_id')].'_'.$color_arr[$row[csf('color_number_id')]].'_'.$row[csf('color_number_id')];
	}
	exit();
}

if ($action == "create_bundle_search_list_view") 
{
    $ex_data = explode("_", $data);
    $txt_order_no = "%" . trim($ex_data[0]) . "%";
    $company = $ex_data[1];
    //$bundle_no = "%".trim($ex_data[2])."%";
    if (trim($ex_data[2])) $bundle_no= "".trim($ex_data[2]).""; else $bundle_no="%".trim($ex_data[2])."%";

    $selectedBuldle = $ex_data[3];
    $job_no = $ex_data[4];
    $order_no =str_replace("'","", $ex_data[0]);
    $bndl_no =str_replace("'","", $ex_data[2]);
    $cut_no = $ex_data[7];
    $syear = $ex_data[5]; 
	$style = $ex_data[6];
	$qr_code = $ex_data[8];
    $is_exact=$ex_data[9];
	$colorId=$ex_data[10];
    
    $company_short_arr = return_library_array("select id, company_short_name from lib_company", 'id', 'company_short_name');
    $cutConvertToInt = convertToInt('c.cut_no', array($company_short_arr[$company], '-'), 'cut_no');
    $bundleConvertToInt = convertToInt('c.bundle_no', array($company_short_arr[$company], '-', "/"), 'order_bundle_no');

    $size_arr = return_library_array("select id, size_name from lib_size", 'id', 'size_name');
    $color_arr = return_library_array("select id, color_name from lib_color", "id", "color_name");
    $country_arr = return_library_array("select id, country_name from lib_country", 'id', 'country_name');

   // $scanned_bundle_arr = return_library_array("select b.bundle_no, b.bundle_no from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and b.production_type=55 and  b.status_active=1 and b.is_deleted=0", 'bundle_no', 'bundle_no');
    $cutting_no =trim($company_short_arr[$company].'-'.$syear.'-'.str_pad($cut_no,6,"0",STR_PAD_LEFT));
    $where_con = '';
    if($ex_data[2]) $where_con .= " and c.bundle_no like '%" . trim($ex_data[2]) . "'";
	if($qr_code!="") $qrCond= " and c.barcode_no='".trim($qr_code)."'";
	
	$bndlCon=""; $cutCon=""; $cutCon_a=""; $jobCon=""; $styleCond=""; $orderCon="";
	if($is_exact=='true')
	{
		if($ex_data[0]!="") $orderCon= "and e.po_number='".trim($ex_data[0])."'";
		if($cut_no!='')
		{
			$cutCon=" and c.cut_no = '$cut_no'";
            $cutCon_a=" cutno = '$cut_no'";
		}
		if($job_no!='') $jobCon=" and f.job_no = '$job_no'";
		if($bndl_no!='') $bndlCon=" and c.bundle_no = '$bndl_no'";
		if($style!='') $styleCond=" and f.style_ref_no = '$style'";
	}
	else
	{
		if($ex_data[0]!="") $orderCon= " and e.po_number like  '%" . trim($ex_data[0]) . "%'";
		if($cut_no!='')
		{
			$cutCon=" and c.cut_no like '%".$cut_no."%'";
            $cutCon_a=" and cutno like '%".$cut_no."%'";
		}
		if($job_no!='') $jobCon=" and f.job_no like '%$job_no%'";
		if($bndl_no!='') $bndlCon=" and c.bundle_no like '%$bndl_no%'";
		if($style!='') $styleCond=" and f.style_ref_no like '%$style%'";
	}
    $tmp_cut=trim($company_short_arr[$company].'-'.$syear.'-'.str_pad($cut_no,6,"0",STR_PAD_LEFT));
	
	$yearCond="";
	
	if($jyear!=0 && ($job_no!='' || $styleCond!='' || $ex_data[0]!=''))
	{
		if($db_type==0) $yearCond="YEAR(f.insert_date)=$jyear"; else if($db_type==2) $yearCond=" to_char(f.insert_date,'YYYY')=$jyear";
	}
	//$sql_bundle="select id, bundle_no, barcode_no from pro_bundle_batch_dtls where mst_id='$mst_id' and status_active=1 and is_deleted=0";
	//echo "select bundle_no, bundle_no from pro_bundle_batch_dtls where cutno='".$tmp_cut."' and status_active=1 and is_deleted=0";
    
    $scanned_bundle_arr = return_library_array("select bundle_no, bundle_no from pro_bundle_batch_dtls where status_active=1 and is_deleted=0", 'bundle_no', 'bundle_no');//cutno='".$tmp_cut."' and 
    /*foreach (explode(",", $selectedBuldle) as $bn) {
        $scanned_bundle_arr[$bn] = $bn;
    }*/
	//print_r($scanned_bundle_arr);

    $scanne=sql_select( "select bundle_no, sum(bundle_no) AS qty_gm from pro_bundle_batch_dtls where and status_active=1 and is_deleted=0 $cutCon_a group by bundle_no");
    foreach($scanne as $row)
    {
        $duplicate_bundle[$row[csf("bundle_no")]]+=$row[csf("qty_gm")];
    }
    //print_r($scanned_bundle_arr);
     
    $last_operation=gmt_production_validation_script( 80, 1,'', $cutting_no, $production_squence);
    //$last_operation=gmt_production_validation_script( 4, 1 );
    // print_r($last_operation);
	
	if($colorId!="" && $colorId!=0) $colorIdCond="and d.color_number_id='$colorId'"; else $colorIdCond="";
    ?>
     <table cellspacing="0" cellpadding="0" border="1" rules="all" width="830" class="rpt_table">
        <thead>
            <th width="40">SL</th>
            <th width="50">Job Year</th>
            <th width="50">Job No</th>
            <th width="90">Order No</th>
            <th width="130">Gmts Item</th>
            <th width="110">Country</th>
            <th width="100">Color</th>
            <th width="50">Size</th>
            <th width="70">Cut No</th>
            <th width="80">Bundle No</th>
            <th>Bundle Qty.</th>
        </thead>
    </table>
    <div style="width:850px; max-height:210px; overflow-y:scroll" id="list_container_batch" align="left">    
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="830" class="rpt_table" id="tbl_list_search">  
        <?
            $i=1;
            $last_operation_string='';  
            //foreach($last_operation as  $item_id=>$operation_cond)
            foreach($last_operation as  $item_id=>$operation_cond)
            {
				//echo $operation_cond;
                if( $operation_cond!=0 ) $operation_conds=" and d.id in (".ltrim($operation_cond,",").") ";
                else
                {
                    $sqld = sql_select( "SELECT c.bundle_no, SUM(c.reject_qty) as reject_qty, SUM(c.alter_qty) as alter_qty, SUM(c.spot_qty) as spot_qty, SUM(c.replace_qty) as replace_qty  from pro_garments_production_mst a, pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e, wo_po_details_master f where d.job_no_mst=f.job_no and a.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and a.company_id=$company  and c.production_type in (78)  
					$qrCond $orderCon $bndlCon $yearCond $jobCon $cutCon $styleCond 
					and a.status_active=1 and a.is_deleted=0 group by  c.bundle_no order by  length(c.bundle_no) asc, c.bundle_no asc" );
                    foreach($sqld as $arows)
                    {
                        $reject_qty[$arows[csf('bundle_no')]]+=$arows[csf('reject_qty')];
                        $alter_qty[$arows[csf('bundle_no')]]+=$arows[csf('alter_qty')];
                        $spt_qty[$arows[csf('bundle_no')]]+=$arows[csf('spot_qty')];
                        $replace_qty[$arows[csf('bundle_no')]]+=$arows[csf('replace_qty')];
                    }
                }
                
                //echo $last_operation_string;
                $sql="SELECT c.cut_no, d.po_break_down_id, d.job_no_mst, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.bundle_no, sum(c.production_qnty) as qty, sum(c.replace_qty) as replace_qty, e.po_number, c.barcode_no from pro_garments_production_mst a, pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e, wo_po_details_master f where d.job_no_mst=f.job_no and a.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and a.company_id=$company 
				$qrCond $orderCon $bndlCon $yearCond $jobCon $cutCon $styleCond $colorIdCond
				and a.status_active=1 and a.is_deleted=0 and a.production_type=78 group by c.cut_no, c.bundle_no, d.po_break_down_id, d.job_no_mst, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.bundle_no, e.po_number,c.barcode_no order by c.cut_no, length(c.bundle_no) asc, c.bundle_no asc";
                
                //echo $sql;
                $result = sql_select($sql); 
                foreach ($result as $row)
                {  
                	// echo $row[csf('qty')]."=".$row[csf('bundle_no')]."*";  -$row[csf('replace_qty')]
                    $row[csf('qty')] = (($row[csf('qty')]) ) - ($reject_qty[$row[csf('bundle_no')]] + $alter_qty[$row[csf('bundle_no')]]+ $spt_qty[$row[csf('bundle_no')]]);
                    $balance_qnty=$row[csf('qty')]-$duplicate_bundle[$row[csf("bundle_no")]];
                    if($scanned_bundle_arr[$row[csf('bundle_no')]]=="" && $row[csf('qty')]>0 && $balance_qnty>0)
                    {
                        if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                        list($shortName,$year,$job)=explode('-',$row[csf('job_no_mst')]);   
                    ?>
                        <tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?=$i;?>" onClick="js_set_value(<?=$i; ?>)"> 
                            <td width="40"><?=$i; ?>
                                 <input type="hidden" name="txt_individual" id="txt_individual<?=$i; ?>" value="<?=$row[csf('barcode_no')]; ?>"/>
                            </td>
                            <td width="50" align="center" style="word-break:break-all"><?=$year; ?></td>
                            <td width="50" align="center" style="word-break:break-all"><?=$job*1; ?></td>
                            <td width="90" style="word-break:break-all"><?=$row[csf('po_number')]; ?></td>
                            <td width="130" style="word-break:break-all"><?=$garments_item[$row[csf('item_number_id')]]; ?></td>
                            <td width="110" style="word-break:break-all"><?=$country_arr[$row[csf('country_id')]]; ?></td>
                            <td width="100" style="word-break:break-all" id="tdColor_<?=$i; ?>" colorid="<?=$row[csf('color_number_id')]; ?>"><?=$color_arr[$row[csf('color_number_id')]]; ?></td>
                            <td width="50" align="center" style="word-break:break-all"><?=$size_arr[$row[csf('size_number_id')]]; ?></td>
                            <td width="70" style="word-break:break-all"><?=$row[csf('cut_no')]; ?></td>
                            <td width="80" style="word-break:break-all"><?=$row[csf('bundle_no')]; ?></td>
                            <td align="center"><?=$row[csf('qty')]; ?>&nbsp;</td>
                        </tr>
                        <?
                        $i++;
                    }
                }
            }
            if(empty($last_operation))
            {
                die;
                $sql="SELECT c.cut_no,d.po_break_down_id, d.job_no_mst, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.bundle_no, sum(c.production_qnty) as qty, e.po_number,c.barcode_no from pro_garments_production_mst a, pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e,wo_po_details_master f where d.job_no_mst=f.job_no and a.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and a.company_id=$company and e.po_number like '$txt_order_no' and c.bundle_no like '$bundle_no' and  c.production_type=1 $jobCon $cutCon  and a.status_active=1 and a.is_deleted=0 group by c.cut_no, c.bundle_no, d.po_break_down_id, d.job_no_mst, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.bundle_no, e.po_number,c.barcode_no order by c.cut_no, length(c.bundle_no) asc, c.bundle_no asc";
                
                //order by c.cut_no, c.bundle_no DESC
                $last_operation_string='';
                $result = sql_select($sql); 
                foreach ($result as $row)
                {  
                    if($scanned_bundle_arr[$row[csf('bundle_no')]]=="")
                    {
                        if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                        list($shortName,$year,$job)=explode('-',$row[csf('job_no_mst')]);   
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)"> 
                            <td width="40"><? echo $i; ?>
                                 <input type="hidden" name="txt_individual" id="txt_individual<?php echo $i; ?>" value="<?php echo $row[csf('barcode_no')]; ?>"/>
                            </td>
                            <td width="50" align="center"><p><? echo $year; ?></p></td>
                            <td width="50" align="center"><p><? echo $job*1; ?></p></td>
                            <td width="90"><p><? echo $row[csf('po_number')]; ?></p></td>
                            <td width="130"><p><? echo $garments_item[$row[csf('item_number_id')]]; ?></p></td>
                            <td width="110"><p><? echo $country_arr[$row[csf('country_id')]]; ?></p></td>
                            <td width="100"><p><? echo $color_arr[$row[csf('color_number_id')]]; ?></p></td>
                            <td width="50"><p><? echo $size_arr[$row[csf('size_number_id')]]; ?></p></td>
                            <td width="70"><? echo $row[csf('cut_no')]; ?></td>
                            <td width="80"><? echo $row[csf('bundle_no')]; ?></td>
                            <td align="right"><? echo $row[csf('qty')]; ?>&nbsp;&nbsp;</td>
                        </tr>
                    <?
                        $i++;
                    }
                }
            }
            ?>
            <input type="hidden" name="source_cond"  value="<?php echo $last_operation_string; ?>" id="source_cond"  />
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

if ($action == "challan_duplicate_check") 
{
    $data=explode("__",$data);
    $result=sql_select("select a.sys_number,b.bundle_no from pro_gmts_delivery_mst a, pro_garments_production_dtls b where a.id=b.delivery_mst_id and b.barcode_no='$data[0]' and b.production_type=80 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.sys_number, b.bundle_no");
    foreach ($result as $row) 
    {
       echo "Bundle No " . $row[csf('bundle_no')] . " Found in Challan No " . $row[csf('sys_number')] . ".";
      //echo "2_".$row[csf('bundle_no')]."**".$row[csf('sys_number')];
      // die;
    }
    exit();
}

if ($action == "populate_bundle_data") 
{
    $ex_data = explode("**", $data);
    $bundle = explode(",", $ex_data[0]);
    $mst_id = explode(",", $ex_data[2]);
    $bundle_nos = "'" . implode("','", $bundle) . "'";
    $vscan=$ex_data[4];
    $source_cond=$ex_data[5];
	$colorId=$ex_data[6];
	
	$bundle_count=count(explode(",",$bundle_nos)); $bundle_nosCond=""; $scnbundle_nos_cond=""; $cutbundle_nos_cond="";
	if($db_type==2 && $bundle_count>400)
	{
		$bundle_nosCond=" and (";
		$scnbundle_nos_cond=" and (";
		$cutbundle_nos_cond=" and (";
		$bundleArr=array_chunk(explode(",",$bundle_nos),399);
		foreach($bundleArr as $bundleNos)
		{
			$bundleNos=implode(",",$bundleNos);
			$bundle_nosCond.=" c.barcode_no in($bundleNos) or ";
			$scnbundle_nos_cond.=" b.barcode_no in($bundleNos) or ";
			$cutbundle_nos_cond.=" barcode_no in($bundleNos) or ";
		}
		$bundle_nosCond=chop($bundle_nosCond,'or ');
		$bundle_nosCond.=")";
		
		$scnbundle_nos_cond=chop($scnbundle_nos_cond,'or ');
		$scnbundle_nos_cond.=")";
		
		$cutbundle_nos_cond=chop($cutbundle_nos_cond,'or ');
		$cutbundle_nos_cond.=")";
	}
	else
	{
		$bundle_nosCond=" and c.barcode_no in ($bundle_nos)";
		$scnbundle_nos_cond=" and b.barcode_no in ($bundle_nos)";
		$cutbundle_nos_cond=" and barcode_no in ($bundle_nos)";
	}
	
	$gmLbsArr=array();
	$sql_gmLbs="select barcode_no, bundle_no, bundle_qty, bundle_qtygm from pro_garments_production_dtls where production_type=51 and status_active=1 and is_deleted=0 $cutbundle_nos_cond";
	$sql_gmLbs_res = sql_select($sql_gmLbs);
	foreach($sql_gmLbs_res as $row)
	{
		$gmLbsArr[$row[csf('barcode_no')]]['lbs']=$row[csf('bundle_qty')];
		$gmLbsArr[$row[csf('barcode_no')]]['gm']=$row[csf('bundle_qtygm')];
	}
	unset($sql_gmLbs_res);
	
	$knitComFloorArr=array();
	$sql_knit="select c.barcode_no, a.serving_company, a.sending_location, a.floor_id from pro_garments_production_mst a, pro_garments_production_dtls c where a.id=c.mst_id and a.production_type=56 and c.production_type=56 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $bundle_nos_cond";
	$sql_knit_res=sql_select($sql_knit);
	
	foreach($sql_knit_res as $row)
	{
		$knitComFloorArr[$row[csf('barcode_no')]]['kcomp']=$row[csf('sending_location')];
		$knitComFloorArr[$row[csf('barcode_no')]]['kfloor']=$row[csf('floor_id')];
	}
	
	/*$rmg_no_sql="select barcode_no, number_start, number_end from ppl_cut_lay_bundle where status_active=1 and is_deleted=0 $cutbundle_nos_cond ";
	$rmg_no_arr=array();
	$rmg_no_sql_res = sql_select($rmg_no_sql);
	foreach($rmg_no_sql_res as $row)
	{
		$rmg_no_arr[$row[csf('barcode_no')]]['from']=$row[csf('number_start')];
		$rmg_no_arr[$row[csf('barcode_no')]]['to']=$row[csf('number_end')];
	}
	unset($rmg_no_sql_res);*/
	
   // $scanned_bundle_arr = return_library_array("select b.bundle_no, b.bundle_no from pro_garments_production_mst a, pro_garments_production_dtls b where a.id=b.mst_id and b.production_type=80 and b.status_active=1 and b.is_deleted=0 $scnbundle_nos_cond", 'bundle_no', 'bundle_no');
	$scanned_bundle_arr = return_library_array("select bundle_no, bundle_no from pro_bundle_batch_dtls where status_active=1 and is_deleted=0", 'bundle_no', 'bundle_no');
    $size_arr = return_library_array("select id, size_name from lib_size", 'id', 'size_name');
    $color_arr = return_library_array("select id, color_name from lib_color", "id", "color_name");
    $country_arr = return_library_array("select id, country_name from lib_country", 'id', 'country_name');
    $buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
	$floorArr = return_library_array("select id, floor_name from lib_prod_floor", 'id', 'floor_name');
	$company_short_arr = return_library_array("select id, company_short_name from lib_company", 'id', 'company_short_name');
    
    $year_field = "";
    if ($db_type == 0) $year_field = "YEAR(f.insert_date)"; else if ($db_type == 2) $year_field = "to_char(f.insert_date,'YYYY')";
     
    $last_operation=array();
    //$last_operation=gmt_production_validation_script( 4, 1);

    //$cutting_no=return_field_value("cut_no", "pro_garments_production_dtls", "1=1 $cutbundle_nos_cond");
    //$cutting_no =trim($company_short_arr[$company].'-'.$syear.'-'.str_pad($cut_no,6,"0",STR_PAD_LEFT));
	if($colorId!="" && $colorId!=0) $colorIdCond="and d.color_number_id='$colorId'"; else $colorIdCond="";
	 
	$sql="SELECT a.sending_location, a.floor_id, max(c.id) as prdid, d.id as colorsizeid, e.id as po_id, f.job_no_prefix_num, MAX($year_field) as year, f.buyer_name, f.style_ref_no, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.cut_no, c.bundle_no, c.barcode_no, sum(c.production_qnty) as production_qnty, e.po_number, c.barcode_no from pro_garments_production_mst a, pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e, wo_po_details_master f where f.company_name='$ex_data[3]' and a.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and e.job_no_mst=f.job_no and a.production_type=78 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and e.status_active in(1,2,3) and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 $bundle_nosCond $colorIdCond group by a.sending_location, a.floor_id, d.id, e.id, f.job_no_prefix_num, f.buyer_name, f.style_ref_no, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id,c.cut_no, c.bundle_no, c.barcode_no, e.po_number order by c.cut_no, length(c.bundle_no) asc, c.bundle_no asc";
	
	//echo $sql;// die;
	$result = sql_select($sql);
	$count=count($result);
	$i=$ex_data[1]+$count;
	foreach ($result as $row)
	{ 
		//echo $scanned_bundle_arr[$row[csf('bundle_no')]];
		if(trim($scanned_bundle_arr[$row[csf('bundle_no')]])=="")
		{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$qty = ($row[csf('production_qnty')] ) - ($reject_qty[$row[csf('bundle_no')]] + $alter_qty[$row[csf('bundle_no')]]+ $spt_qty[$row[csf('bundle_no')]]);
			?>
			<tr bgcolor="<?=$bgcolor; ?>" id="tr_<?=$i;?>"> 
				<td width="30" align="center"><?=$i; ?></td>
				<td width="80" id="bundle_<?=$i; ?>" title="<?=$row[csf('barcode_no')]; ?>"><?=$row[csf('bundle_no')]; ?></td>
				<td width="90" id="barcode_<?=$i; ?>" title="<?=$row[csf('barcode_no')]; ?>"><?=$row[csf('barcode_no')]; ?></td>
                
                <td width="100" style="word-break:break-all;" align="left"><?=$color_arr[$row[csf('color_number_id')]]; ?></td>
				<td width="60" style="word-break:break-all;" align="center">&nbsp;<?=$size_arr[$row[csf('size_number_id')]]; ?></td>
				<td width="65" id="prodQty_<?=$i; ?>" align="right"><?=$qty; ?>&nbsp;</td>
                <td width="65" id="prodQtyGm_<?=$i; ?>" align="right"><?=$gmLbsArr[$row[csf('barcode_no')]]['gm']; ?>&nbsp;</td>
                <td width="65" id="prodQtyLbs_<?=$i; ?>" align="right"><?=$gmLbsArr[$row[csf('barcode_no')]]['lbs']; ?>&nbsp;</td>
                <td width="70" style="word-break:break-all;" align="center"><?=$company_short_arr[$knitComFloorArr[$row[csf('barcode_no')]]['kcomp']]; ?></td>
				<td width="90" style="word-break:break-all;" align="center"><?=$floorArr[$knitComFloorArr[$row[csf('barcode_no')]]['kfloor']]; ?></td>
                
                <td width="50" align="center"><?=$row[csf('year')]; ?></td>
				<td width="60" align="center"><?=$row[csf('job_no_prefix_num')]; ?></td>
                <td width="65" style="word-break:break-all;" align="left"><?=$buyer_arr[$row[csf('buyer_name')]]; ?></td>
				<td width="90" style="word-break:break-all;" align="left"><?=$row[csf('style_ref_no')]; ?></td>
				<td width="90" style="word-break:break-all;" align="left"><?=$row[csf('po_number')]; ?></td>
				<td width="100" style="word-break:break-all;" align="left"><?=$garments_item[$row[csf('item_number_id')]]; ?></td>
				<td width="80" style="word-break:break-all;" align="left"><?=$country_arr[$row[csf('country_id')]]; ?></td>
				
				<td id="button_1" align="center">
					<input type="button" id="decrease_<?=$i; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<?=$i; ?>);" />
					
					<input type="hidden" name="txtcutNo[]" id="txtcutNo_<?=$i; ?>" value="<?=$row[csf('cut_no')]; ?>"/>
					<input type="hidden" name="txtcolorSizeId[]" id="txtcolorSizeId_<?=$i; ?>" value="<?=$row[csf('colorsizeid')]; ?>"/>
					<input type="hidden" name="txtorderId[]" id="txtorderId_<?=$i; ?>" value="<?=$row[csf('po_id')]; ?>"/>
					<input type="hidden" name="txtgmtsitemId[]" id="txtgmtsitemId_<?=$i; ?>" value="<?=$row[csf('item_number_id')]; ?>"/>
					<input type="hidden" name="txtcountryId[]" id="txtcountryId_<?=$i; ?>" value="<?=$row[csf('country_id')]; ?>"/>
					<input type="hidden" name="txtcolorId[]" id="txtcolorId_<?=$i; ?>" value="<?=$row[csf('color_number_id')]; ?>"/>
					<input type="hidden" name="txtsizeId[]" id="txtsizeId_<?=$i; ?>" value="<?=$row[csf('size_number_id')]; ?>"/>
					<input type="hidden" name="txtqty[]" id="txtqty_<?=$i; ?>" value="<?=$qty; ?>"/>
                    <input type="hidden" name="txtqtygm[]" id="txtqtygm_<?=$i; ?>" value="<?=$gmLbsArr[$row[csf('barcode_no')]]['gm']; ?>"/>
                    <input type="hidden" name="txtqtylbs[]" id="txtqtylbs_<?=$i; ?>" value="<?=$gmLbsArr[$row[csf('barcode_no')]]['lbs']; ?>"/>
					<input type="hidden" name="dtlsId[]" id="dtlsId_<?=$i; ?>" value="<? //=$row[csf('prdid')]; ?>"/> 
				</td>
			</tr>
			<?
			$i--;
		}
	}
    exit(); 
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if (str_replace("'", "", $txt_ext_no) != "" || $db_type == 0) {
		$extention_no_cond  = "extention_no=$txt_ext_no";
		$extention_no_cond2 = "and batch_ext_no=$txt_ext_no";
	} else {
		$extention_no_cond  = "extention_no is null";
		$extention_no_cond2 = "and batch_ext_no is null";
	}

	if ($db_type == 0) $extention_no_cond_valid = " and a.extention_no=0"; else $extention_no_cond_valid = " and a.extention_no is null";
	
	if($operation==0)  // Insert Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$batch_update_id=''; $batch_no_creation=str_replace("'","",$batch_no_creation);
		$color_id=str_replace("'","",$hidden_color_id);
		
		if(str_replace("'","",$update_id)=="")
		{
			$id = return_next_id_by_sequence("PRO_BUNDLE_BATCH_MST_PK_SEQ", "pro_bundle_batch_mst", $con);
			$serial_no=date("y",strtotime($pc_date_time))."-".$id;
		 	if($batch_no_creation==1)
			{
				$txt_batch_number="'".$id."'";
			}
			else
			{
				if(is_duplicate_field( "batch_no", "pro_bundle_batch_mst", "batch_no=$txt_batch_number and $extention_no_cond and entry_form=389" )==1)
				{
					//check_table_status( $_SESSION['menu_id'],0);
					echo "11**0"; 
					disconnect($con); die;			
				}
				$txt_batch_number=$txt_batch_number;
			}

			$field_array="id, company_id, working_company, working_location, batch_against, gmts_type, batch_date, batch_no, extention_no, entry_form, shift_id, color_id, batch_weight, supervisor_name, operator_name, process_id, dur_req_hr, dur_req_min, machine_no, operation_type, sub_operation, remarks, inserted_by, insert_date, status_active, is_deleted";
			$data_array="(".$id.",".$cbo_company_id.",".$cbo_working_company.",".$cbo_working_location.",".$cbo_batch_against.",".$cbo_gmts_type.",".$txt_batch_date.",".$txt_batch_number.",".$txt_ext_no.",389,".$cbo_shift.",".$color_id.",".$txt_batch_weight.",".$txt_supervisor.",".$txt_operator.",".$txt_process_id.",".$txt_du_req_hr.",".$txt_du_req_min.",".$machine_id.",".$cbo_operation.",".$cbo_sub_operation.",".$txt_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			$batch_update_id=$id;
		}
		else
		{
			$serial_no=str_replace("'","",$txt_batch_sl_no);
			if($batch_no_creation!=1)
			{
				if(is_duplicate_field("batch_no","pro_bundle_batch_mst", "batch_no=$txt_batch_number and $extention_no_cond and entry_form=389 and id<>$update_id" )==1)
				{
					//check_table_status( $_SESSION['menu_id'],0);
					echo "11**0"; 
					disconnect($con); die;			
				}
			}
			
			$field_array_update="gmts_type*working_company*working_location*batch_no*batch_date*extention_no*shift_id*color_id*batch_weight*supervisor_name*operator_name*process_id*dur_req_hr*dur_req_min*machine_no*operation_type*sub_operation*remarks*updated_by*update_date";
			$data_array_update="".$cbo_gmts_type."*".$cbo_working_company."*".$cbo_working_location."*".$txt_batch_number."*".$txt_batch_date."*".$txt_ext_no."*".$cbo_shift."*".$color_id."*".$txt_batch_weight."*".$txt_supervisor."*".$txt_operator."*".$txt_process_id."*".$txt_du_req_hr."*".$txt_du_req_min."*".$machine_id."*".$cbo_operation."*".$cbo_sub_operation."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			$batch_update_id=str_replace("'","",$update_id);
		}
		
		//$id_dtls=return_next_id( "id", "pro_batch_create_dtls", 1 ) ;
		$field_array_dtls="id, mst_id, po_id, colorsizeid, bundle_no, barcode_no, colorid, sizeid, cutno, gmtsitemid, countryid, qty_pcs, qty_gm, qty_lbs, inserted_by, insert_date, status_active, is_deleted";
		$batch_balance='';
		for($i=1;$i<=$tot_row;$i++)
		{
			$id_dtls = return_next_id_by_sequence("PRO_BUNDLE_BATCH_DTLS_PK_SEQ", "pro_bundle_batch_dtls", $con);
			
			$bundleNo 		="bundleNo_".$i;
			$barcodeNo 		="barcodeNo_".$i;			
			$colorId 		="colorId_".$i;
			$sizeId		 	="sizeId_".$i;
			$colorSizeId	="colorSizeId_".$i;
			$qty	 		="qty_".$i;
			$qtygm	 		="qtygm_".$i;
			$qtylbs	 		="qtylbs_".$i;
			$orderId 		="orderId_".$i;
			$countryId	 	="countryId_".$i;
			$gmtsitemId 	="gmtsitemId_".$i;
			$txtcutNo		="txtcutNo_".$i;
			$dtlsId			="dtlsId_".$i;

			if($data_array_dtls!="") $data_array_dtls.=","; 	
			$data_array_dtls.="(".$id_dtls.",".$batch_update_id.",'".$$orderId."','".$$colorSizeId."','".$$bundleNo."','".$$barcodeNo."','".$$colorId."','".$$sizeId."','".$$txtcutNo."','".$$gmtsitemId."','".$$countryId."','".$$qty."','".$$qtygm."','".$$qtylbs."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)"; 
			//$id_dtls=$id_dtls+1;
		}
		//echo "10**0**0"; disconnect($con); die;
		$flag=1;
		
		if(str_replace("'","",$update_id)=="")
		{
			$rID=sql_insert("pro_bundle_batch_mst",$field_array,$data_array,0);
			if($rID==1 && $flag==1) $flag=1; else $flag=0;
		}
		else
		{
			$rID=sql_update("pro_bundle_batch_mst",$field_array_update,$data_array_update,"id",$update_id,1);
			if($rID==1 && $flag==1) $flag=1; else $flag=0; 
		}
		
		//echo "10**insert into pro_bundle_batch_dtls (".$field_array_dtls.") values ".$data_array_dtls;disconnect($con); die;
		$rID2=sql_insert("pro_bundle_batch_dtls",$field_array_dtls,$data_array_dtls,1);
		if($rID2==1 && $flag==1) $flag=1; else $flag=0; 
		
		//check_table_status( $_SESSION['menu_id'],0);
		//echo "10**".$rID.'='.$rID2.'='.$flag;disconnect($con); die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".$batch_update_id."**".$serial_no;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**0**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);   
				echo "0**".$batch_update_id."**".$serial_no;
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
		$color_id=str_replace("'","",$hidden_color_id);
		$batch_no_creation=str_replace("'","",$batch_no_creation);
		$batch_update_id=str_replace("'","",$update_id);
		$serial_no=str_replace("'","",$txt_batch_sl_no);
		
		if($batch_no_creation!=1)
		{
			if(is_duplicate_field( "batch_no", "pro_bundle_batch_mst", "batch_no=$txt_batch_number and $extention_no_cond and entry_form=389 and id<>$update_id" )==1)
			{
				//check_table_status( $_SESSION['menu_id'],0);
				echo "11**0"; 
				disconnect($con); die;			
			}
		}
		
		for($i=1; $i<=$tot_row; $i++)
        {   
            $txtbarcode 	="barcodeNo_".$i;       
            $barcodeCheckArr[$$txtbarcode]=$$txtbarcode;       
        }
        $barcode_str ="'".implode("','",$barcodeCheckArr)."'";
		
		$sql_dtls="select id from pro_bundle_batch_dtls where mst_id=$update_id and status_active=1 and is_deleted=0";
		$nameArray=sql_select($sql_dtls); $dtls_update_id_array=array();
		foreach($nameArray as $row)
		{
			$dtls_update_id_array[]=$row[csf('id')];
		}
		
		$field_array_update="gmts_type*working_company*working_location*batch_no*batch_date*extention_no*shift_id*color_id*batch_weight*supervisor_name*operator_name*process_id*dur_req_hr*dur_req_min*machine_no*operation_type*sub_operation*remarks*updated_by*update_date";
			$data_array_update="".$cbo_gmts_type."*".$cbo_working_company."*".$cbo_working_location."*".$txt_batch_number."*".$txt_batch_date."*".$txt_ext_no."*".$cbo_shift."*".$color_id."*".$txt_batch_weight."*".$txt_supervisor."*".$txt_operator."*".$txt_process_id."*".$txt_du_req_hr."*".$txt_du_req_min."*".$machine_id."*".$cbo_operation."*".$cbo_sub_operation."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$field_array_dtls="id, mst_id, po_id, colorsizeid, bundle_no, barcode_no, colorid, sizeid, cutno, gmtsitemid, countryid, qty_pcs, qty_gm, qty_lbs, inserted_by, insert_date, status_active, is_deleted";
		$data_array_dtls="";
		$field_array_dtls_update="po_id*colorsizeid*bundle_no*barcode_no*colorid*sizeid*cutno*gmtsitemid*countryid*qty_pcs*qty_gm*qty_lbs*updated_by*update_date";
		for($i=1; $i<=$tot_row; $i++)
		{
			$bundleNo 		="bundleNo_".$i;
			$barcodeNo 		="barcodeNo_".$i;			
			$colorId 		="colorId_".$i;
			$sizeId		 	="sizeId_".$i;
			$colorSizeId	="colorSizeId_".$i;
			$qty	 		="qty_".$i;
			$qtygm	 		="qtygm_".$i;
			$qtylbs	 		="qtylbs_".$i;
			$orderId 		="orderId_".$i;
			$countryId	 	="countryId_".$i;
			$gmtsitemId 	="gmtsitemId_".$i;
			$txtcutNo		="txtcutNo_".$i;
			$dtlsId			="dtlsId_".$i;
			
			if(str_replace("'","",$$dtlsId)!="")
			{
				$id_arr[]=str_replace("'",'',$$dtlsId);
				$data_array_dtls_update[str_replace("'",'',$$dtlsId)] = explode("*",($$orderId."*'".$$colorSizeId."'*'".$$bundleNo."'*'".$$barcodeNo."'*'".$$colorId."'*'".$$sizeId."'*'".$$txtcutNo."'*'".$$gmtsitemId."'*'".$$countryId."'*'".$$qty."'*'".$$qtygm."'*'".$$qtylbs."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			}
			else
			{
				$id_dtls = return_next_id_by_sequence("PRO_BUNDLE_BATCH_DTLS_PK_SEQ", "pro_bundle_batch_dtls", $con);
				
				if($data_array_dtls!="") $data_array_dtls.=","; 	
				$data_array_dtls.="(".$id_dtls.",".$batch_update_id.",'".$$orderId."','".$$colorSizeId."','".$$bundleNo."','".$$barcodeNo."','".$$colorId."','".$$sizeId."','".$$txtcutNo."','".$$gmtsitemId."','".$$countryId."','".$$qty."','".$$qtygm."','".$$qtylbs."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			}
		}
		$flag=1;
		//echo "10**".print_r($data_array_dtls_update);
		$rID=sql_update("pro_bundle_batch_mst",$field_array_update,$data_array_update,"id",$update_id,1);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		
		//echo "10**". bulk_update_sql_statement( "pro_bundle_batch_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $id_arr );disconnect($con); die;
		if($data_array_dtls_update!="")
		{
			$rID1=execute_query(bulk_update_sql_statement( "pro_bundle_batch_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $id_arr ));
			if($rID1==1 && $flag==1) $flag=1; else $flag=0; 
		}
		//echo $flag;die;
		//echo "6**0**insert into pro_bundle_batch_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
		if($data_array_dtls!="")
		{
			$rID2=sql_insert("pro_bundle_batch_dtls",$field_array_dtls,$data_array_dtls,1);
			if($rID2==1 && $flag==1) $flag=1; else $flag=0; 
		}
		
		if(implode(',',$id_arr)!="")
		{
			$distance_delete_id=implode(',',array_diff($dtls_update_id_array,$id_arr));
		}
		else
		{
			$distance_delete_id=implode(',',$dtls_update_id_array);
		}
		if(str_replace("'",'',$distance_delete_id)!="")
		{
			$rID3=execute_query( "update pro_bundle_batch_dtls set updated_by='".$_SESSION['logic_erp']['user_id']."', update_date='".$pc_date_time."', status_active=0, is_deleted=1 where id in ($distance_delete_id) and status_active=1 and is_deleted=0 ",0);
			if($rID3==1 && $flag==1) $flag=1; else $flag=0;
		}
		
		//echo "10**".$rID.'='.$rID1.'='.$rID2.'='.$flag; die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".$batch_update_id."**".$serial_no;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**0**1";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "1**".$batch_update_id."**".$serial_no;
			}
			else
			{
				oci_rollback($con);
				echo "6**0**1";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)  //Delete here======================================================================================
	{
		$con = connect();		
		if($db_type==0)	{ mysql_query("BEGIN"); }
		
		$updateid=str_replace("'", "", $update_id);
		$batch_number=str_replace("'", "", $txt_batch_number);
		//$subprocess = str_replace("'", "", $cbo_sub_process);
		
		//echo "10**".$updateid."==".$batch_number;
		
		if ($updateid== "" || $batch_number== "") 
		{
			echo "15";
			disconnect($con);
			exit();
		}
		
		for($i=1;$i<=$total_row; $i++)
		{
			$updateIdDtls="updateIdDtls_".$i;
			if(str_replace("'","",$$updateIdDtls)!="")
			{
				$inv_transaction_data_arr[str_replace("'",'',$$updateIdDtls)]=explode("*",("0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				$deleted_id_arr[]=str_replace("'",'',$$updateIdDtls);
			}
			
		}
		//print_r($deleted_id_arr); die;
		
			$mrrsql= sql_select("select  recipe_no, batch_id  from  pro_recipe_entry_mst where batch_id=$updateid  and  entry_form = 300  and  status_active=1 and  is_deleted=0");
			$mrr_data=array();
			foreach($mrrsql as $row)
			{
				$all_recipe_no.=$row[csf('recipe_no')].",";
			}
			$all_batch_no=chop($all_recipe_no,",");
			$all_recipe_no=chop($batch_number,",");
			
			$all_recipe_trans_id_count=count($mrrsql);
			if($all_recipe_trans_id_count)
			{
				if($all_recipe_trans_id_count>0)
				{
					echo "50**Delete restricted, This Information is used in another Table."."  Recipe Number ".$do_rcv_number=str_replace("'","",$all_recipe_no)."  Batch Number ".$do_rcv_number=str_replace("'","",$all_batch_no); 
					disconnect($con); 
					oci_rollback($con); die;
				}
			}
			
				$field_arr="status_active*is_deleted*updated_by*update_date";
				$data_arr="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				$rID=sql_update("pro_batch_create_mst",$field_arr,$data_arr,"id",$update_id,0);	
				$rID1=execute_query(bulk_update_sql_statement("pro_batch_create_dtls","id",$field_arr,$inv_transaction_data_arr,$deleted_id_arr));
				if($rID) $flag=1; else $flag=0;
				if($rID1) $flag=1; else $flag=0;
			
			//echo "10**".$rID."==".$rID1; die;
		if ($db_type == 0) 
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "2**" . str_replace("'", "", $txt_req_no);
			} 
			else 
			{
				mysql_query("ROLLBACK");
				echo "10**" . str_replace("'", "", $txt_req_no);
			}
		} 
		else if ($db_type == 2 || $db_type == 1) 
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "2**" . str_replace("'", "", $txt_req_no);
			}
			 else 
			{
				oci_rollback($con);
				echo "10**" . str_replace("'", "", $txt_req_no);
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="batch_popup")
{
  	echo load_html_head_contents("Batch Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
	?>
	<script>
	
		function js_set_value( batch_id,operation_id,sub_operation_id,operation_type,po_id,color_id,wet_batch_id,batch_no,ext_from) 
		{
			//alert(sub_operation_id);
			document.getElementById('hidden_batch_id').value=batch_id;
			document.getElementById('hidden_batch_no').value = batch_no;
			document.getElementById('hidden_operation_id').value=operation_id;
			document.getElementById('hidden_sub_operation_id').value=sub_operation_id;
			document.getElementById('po_id').value=po_id;
			document.getElementById('operation_type_id').value=operation_type;
			document.getElementById('batch_color_id').value=color_id;
			document.getElementById('hidden_ext_from').value = ext_from;
			document.getElementById('hidden_unloaded_batch').value = wet_batch_id;
			parent.emailwindow.hide();
		}
	
    </script>
    </head>
    <body>
    <div align="center">
        <fieldset style="width:830px;margin-left:4px;">
            <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
                <table cellpadding="0" cellspacing="0" width="750" class="rpt_table">
                    <thead>
                        <th>Search By</th>
                        <th>Search</th>
                        <th>Date Range</th>
                        <th>
                            <input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                            <input type="hidden" name="hidden_batch_id" id="hidden_batch_id" value="">
                            <input type="hidden" name="hidden_operation_id" id="hidden_operation_id" value="">
                            <input type="hidden" name="hidden_sub_operation_id" id="hidden_sub_operation_id" value="">
                            <input type="hidden" name="po_id" id="po_id" value="">
                            <input type="hidden" name="operation_type_id" id="operation_type_id" value="">
                            <input type="hidden" name="batch_color_id" id="batch_color_id" value="">
                            <input type="hidden" name="hidden_ext_from" id="hidden_ext_from" value="">
                            <input type="hidden" name="hidden_batch_no" id="hidden_batch_no" value="">
                            <input type="hidden" name="hidden_unloaded_batch" id="hidden_unloaded_batch" value="">
                        </th> 
                    </thead>
                    <tr class="general">
                        <td>	
                            <?
                                $search_by_arr=array(1=>"Batch No");//,2=>"Style No"
                                echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
                            ?>
                        </td>                 
                        <td><input type="text" style="width:140px" class="text_boxes"  name="txt_search_common" id="txt_search_common" /></td> 
                        <td>
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px" placeholder="From Date" value="">
                                &nbsp; To
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px"  placeholder="To Date"  value="">
                        </td>						
                        <td><input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>+'_'+<? echo $batch_against; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_batch_search_list_view', 'search_div', 'sweater_batch_creation_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" /></td>
                    </tr>
                    <tfoot>
                        <tr>
                            <td colspan="4" align="center"><? echo load_month_buttons(1); ?></td>
                        </tr>
                    </tfoot>
                </table>
                <div id="search_div" style="margin-top:10px"></div>   
            </form>
        </fieldset>
    </div>    
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if($action=="create_batch_search_list_view")
{
	$data=explode('_',$data);
	
	$search_string=trim($data[0]);
	$search_by =$data[1];
	$company_id =$data[2];
	$batch_against_id=$data[3];
	$date_from=$data[4];
	$date_to=$data[5];
	
	if($search_string!='')
	{
		if($search_by==1) $search_field="and a.batch_no like '$search_string'";
		//else if($search_by==2) $buyer_style_cond=" and b.buyer_style_ref like '%$search_string%'";
		//else $search_field='booking_no';
	}
	 
	/*$order_buyer_po_array=array();
	$buyer_po_arr=array();
	$order_buyer_po='';
	$order_sql ="select b.id,b.buyer_po_no,b.buyer_style_ref from subcon_ord_mst a, subcon_ord_dtls b where a.id=b.mst_id and a.entry_form='295' $buyer_style_cond"; 
	$order_sql_res=sql_select($order_sql);
	foreach ($order_sql_res as $row)
	{
		$order_buyer_po_array[]=$row[csf("id")];
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("buyer_style_ref")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("buyer_po_no")];
	}
	//unset($order_sql_res);
	$order_buyer_po=implode(",",$order_buyer_po_array);
	//echo $order_buyer_po; 
	if ($order_buyer_po!="") $order_order_buyer_poCond=" and b.po_id in ($order_buyer_po)"; else $order_order_buyer_poCond="";*/
	 
	 
	 

	/*$po_ids=''; 
	$buyer_po_arr=array();
	if($search_by==2 && $data[0]!='')
	{
		if($db_type==0) $id_cond="group_concat(b.id) as id";
		else if($db_type==2) $id_cond="rtrim(xmlagg(xmlelement(e,b.id,',').extract('//text()') order by b.id).GetClobVal(),',') as id";
		//echo "select $id_cond from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $job_cond $style_cond $po_cond";
		
		$po_ids = return_field_value("$id_cond", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $style_cond ", "id");
		
		//echo $po_ids;
		if($db_type==2 && $po_ids!="") $po_ids = $po_ids->load();
		if ($po_ids!="")
		{
			$po_ids=explode(",",$po_ids);
			$po_idsCond=""; $poIdsCond="";
			//echo count($po_ids); die;
			if($db_type==2 && count($po_ids)>=999)
			{
				$chunk_arr=array_chunk($po_ids,999);
				foreach($chunk_arr as $val)
				{
					$ids=implode(",",$val);
					if($po_idsCond=="")
					{
						$po_idsCond.=" and ( b.buyer_po_id in ( $ids) ";
						$poIdsCond.=" and ( b.id in ( $ids) ";
					}
					else
					{
						$po_idsCond.=" or  b.buyer_po_id in ( $ids) ";
						$poIdsCond.=" or  b.id in ( $ids) ";
					}
				}
				$po_idsCond.=")";
				$poIdsCond.=")";
			}
			else
			{
				$ids=implode(",",$po_ids);
				$po_idsCond.=" and b.buyer_po_id in ($ids) ";
				$poIdsCond.=" and b.id in ($ids) ";
			}
		}
		else if($po_ids=="" && ($search_by==2 && $data[0]!=''))
		{
			echo "Not Found"; die;
		}
		//echo $po_idsCond;
	}*/
	
	/*
	$po_sql ="Select a.style_ref_no,a.job_no_prefix_num, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $poIdsCond";
	$po_sql_res=sql_select($po_sql); 
	$buyer_style_arr=array();
	foreach ($po_sql_res as $row)
	{
		$buyer_style_arr[$row[csf("id")]]=$row[csf("style_ref_no")];
		//$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
		//$buyer_po_arr[$row[csf("id")]]['job']=$row[csf("job_no_prefix_num")];
	}
	unset($po_sql_res);*/

	if($data[0] != "") $date_cond="";
	else
	{
		if($date_from != "" && $date_to != ""){
			if($db_type==0)
			{
				$date_cond=" and a.batch_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
			}
			else
			{
				$date_cond=" and a.batch_date between '".change_date_format($date_from, "", "",1)."' and '".change_date_format($date_to, "", "",1)."'";
			}
		}
	}

	//$batch_cond.=" and a.batch_against=$batch_against_id";
	$batch_cond = "";
	if ($batch_against_id != 11) $batch_cond = " and a.batch_against=$batch_against_id";
	
	//$arr=array(2=>$po_name_arr,5=>$batch_against,6=>$color_arr,7=>$buyer_style_arr);

	if($db_type==2) 
	{
		$group_concat_id=" ,listagg(b.po_id,',') within group (order by b.po_id) as po_id" ;
	}
	else if($db_type==0)
	{
		$group_concat_id=",group_concat(b.po_id) as po_id" ;
	}
	
	  $sql = "select a.id, a.batch_no, a.extention_no, a.batch_weight, a.batch_date, a.batch_against, a.color_id $group_concat_id, b.po_id, a.gmts_type, a.sub_operation, a.operation_type, a.re_wash_from, sum(b.qty_pcs) as qty_pcs from pro_bundle_batch_mst a, pro_bundle_batch_dtls b where a.id=b.mst_id and a.company_id=$company_id  $search_field $date_cond and a.status_active=1 and a.entry_form=389 and a.is_deleted=0 $batch_cond $order_order_buyer_poCond group by a.id, a.batch_no, a.extention_no, a.batch_weight, a.batch_date, a.batch_against, a.color_id, b.po_id, a.gmts_type, a.sub_operation, a.operation_type, a.re_wash_from order by a.id DESC"; 
	  
	//echo $sql;	 
	/*echo  create_list_view("tbl_list_search", "Batch No,Ext. No,Order No,Batch Weight, Batch Date,Batch Against, Color,Style No", "100,70,150,80,80,80,80,80","810","250",0, $sql, "js_set_value", "id", "", 1, "0,0,id,0,0,batch_against,color_id,", $arr, "batch_no,extention_no,id,batch_weight,batch_date,batch_against,color_id", "",'','0,0,0,2,3,0');*/
	$result = sql_select($sql);
	
	
	$batch_id=array(); $poIdArr=array();
	foreach ($result as $row) {
		$batch_id[] .= $row[csf("id")];
		$poIdArr[].= $row[csf("po_id")];
	}
	
	$poId=implode(",",array_filter(array_unique($poIdArr)));
	
	$rewash_batch_id=implode(",",$batch_id);
	$rewash_batch_cond = "";
	if ($rewash_batch_id !="") $rewash_batch_cond = " and a.recipe_id in (".$rewash_batch_id.")";
	
	$sql_wet_batch="select a.id ,a.recipe_id  from subcon_embel_production_mst a,subcon_embel_production_dtls b
			where  a.id=b.mst_id and a.entry_form in(301) $rewash_batch_cond
			and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.rewash_qty>0 group by a.id,a.recipe_id ";
			
	$sql_wet_batch_data=sql_select($sql_wet_batch);
	foreach ($sql_wet_batch_data as $row)
	{
		$wet_batch_arr[$row[csf('recipe_id')]] = $row[csf('recipe_id')];
	}
	$re_dyeing_from = return_library_array("select re_dyeing_from from pro_batch_create_mst where re_dyeing_from <>0 and status_active = 1 and is_deleted = 0","re_dyeing_from","re_dyeing_from");
	//print_r($wet_batch_arr);
	
	$po_arr=return_library_array( "select id, po_number from wo_po_break_down where id in ($poId)",'id','po_number');
	$color_arr = return_library_array("select id, color_name from lib_color", "id", "color_name");
	?>
    <div>
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="930" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="100">Batch No</th>
                <th width="70">Ext. No</th>
                <th width="150">Order No</th>
                <th width="100">GMT Qty</th>
                <th width="80">Batch Weight</th>
                <th width="100">Operation</th>
                <th width="80">Batch Date</th>
                <th width="80">Batch Against</th>
                <th>Color</th>
            </thead>
     	</table>
     <div style="width:930px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="910" class="rpt_table" id="tbl_po_list">
			<?
			$i=1;
            foreach( $result as $row )
            {
				if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				 
				if($re_dyeing_from[$row[csf('id')]]) $ext_from = $re_dyeing_from[$row[csf('id')]]; else $ext_from = "0";
				 
				$order_no='';
				$order_id=array_unique(explode(",",$row[csf("po_id")]));
				foreach($order_id as $val)
				{
					if($order_no=="") $order_no=$po_arr[$val]; else $order_no.=",".$po_arr[$val];
				}
				$order_no=implode(",",array_unique(explode(",",$order_no)));
				?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $row[csf("id")];?>','<? echo $row[csf("gmts_type")];?>','<? echo $row[csf("sub_operation")];?>','<? echo $row[csf("operation_type")];?>','<? echo $row[csf("po_id")];?>','<? echo $row[csf("color_id")];?>','<? echo $wet_batch_arr[$row[csf('id')]]; ?>','<? echo $row[csf('batch_no')]; ?>','<? echo $ext_from; ?>');" > 
                    <td width="30" align="center"><? echo $i; ?></td>
                    <td width="100"><? echo $row[csf("batch_no")]; ?></td>
                    <td width="70"><? echo $row[csf("extention_no")]; ?></td>
                    <td width="150"><? echo $order_no; ?></td>
                    <td width="100" align="right"><? echo $row[csf("qty_pcs")]; ?></td>
                    <td width="80" align="right"><? echo $row[csf("batch_weight")]; ?></td>
                    <td width="100" ><? echo $wash_operation_arr[$row[csf("operation_type")]]; ?></td>
                    <td width="80" ><? echo change_date_format($row[csf("batch_date")]); ?></td>
                    <td width="80"><? echo $batch_against[$row[csf("batch_against")]]; ?></td>
                    <td style="word-break:break-all"><? echo $color_arr[$row[csf("color_id")]]; ?></td>
                </tr>
            <? 
            $i++;
		}
   		?>
			</table>
		</div>
     </div>
     <?	
	exit();	
}

if($action=="populate_data_from_search_popup")
{
	$data=explode("**",$data);
	$batch_id=$data[2];
	$batch_against=$data[0];
	$batch_for=$data[1];
	$po_id=$data[2];
	$operation_type_id=$data[3];
	$batch_color_id=$data[4];
	$unloaded_batch = $data[7];
	$ext_from = $data[8];
	$company_id = $data[9];
	$batch_no = $data[10];
	
	if($db_type==0) $year_field="DATE_FORMAT(a.insert_date,'%y')"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YY')";
	else $year_cond="";//defined Later
		
	$machine_arr=return_library_array( "select id, machine_no from lib_machine_name",'id','machine_no');
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
		
	$incrementExtentionNo="";
	if($batch_against==11) // Re-dyeing- Extention sequence maintain
	{
		if($unloaded_batch!="" && $ext_from ==0)
		{
			$exists_data_no = sql_select("select a.batch_no, max(a.extention_no) as max_extention_no from pro_bundle_batch_mst a where a.batch_no='".$batch_no."' and a.company_id= $company_id and a.status_active = 1 and a.is_deleted = 0 group by batch_no");
			$exists_extention_no = $exists_data_no[0][csf('max_extention_no')];
			if($exists_extention_no>0)
			{
				$incrementExtentionNo = $exists_extention_no+1;
			}
			else
			{
				$incrementExtentionNo = 1;
			}
		}
	}

	$data_array=sql_select("select a.id, a.company_id, a.working_company, a.working_location, a.batch_no, a.extention_no, a.batch_weight, a.batch_date, a.batch_against, a.color_id, a.process_id, a.dur_req_hr, a.dur_req_min, a.remarks, a.shift_id, a.machine_no, a.operator_name, a.supervisor_name, a.operation_type, a.gmts_type, a.sub_operation, b.po_id, $year_field as year from pro_bundle_batch_mst a, pro_bundle_batch_dtls b where a.id='$batch_id' and a.id=b.mst_id and b.mst_id=$batch_id and a.status_active=1 and a.is_deleted=0 ");
	
	$gmts_type_arr=return_library_array( "select b.id, a.gmts_type from subcon_ord_mst a, subcon_ord_dtls b where a.id=b.mst_id and a.subcon_job=b.job_no_mst and a.entry_form=295",'id','gmts_type');
	//print_r($gmts_type_arr);
	
	foreach ($data_array as $row)
	{
		if($incrementExtentionNo=="")
		{
			if($row[csf("extention_no")] == 0) $incrementExtentionNo = ''; else $incrementExtentionNo = $row[csf("extention_no")];
		}

		$serial_no=$row[csf("id")]."-".$row[csf("year")];
		$process_name='';
		$process_id_array=explode(",",$row[csf("process_id")]);
		foreach($process_id_array as $val)
		{
			if($process_name=="") $process_name=$wash_type[$val]; else $process_name.=",".$wash_type[$val];
		}
		
		echo "document.getElementById('txt_batch_sl_no').value = '".$serial_no."';\n";
		
		if($batch_against==11)
		{
			echo "document.getElementById('cbo_batch_against').value = '".$batch_against."';\n"; 
		}
		else
		{
			echo "document.getElementById('cbo_batch_against').value = '".$row[csf("batch_against")]."';\n"; 	
		}
		
		echo "document.getElementById('cbo_gmts_type').value = '".$row[csf("gmts_type")]."';\n";  
		echo "document.getElementById('cbo_sub_operation').value = '".$row[csf("sub_operation")]."';\n";
		echo "load_drop_down( 'requires/sweater_batch_creation_controller', '".$row[csf("operation_type")]."', 'load_drop_down_sub_operation', 'sub_operation');\n";
		echo "set_multiselect('cbo_sub_operation','0','0','0','0');\n";
		echo "set_multiselect('cbo_sub_operation','0','1','".($row[csf("sub_operation")])."','0');\n"; 
		  
		echo "document.getElementById('txt_batch_date').value = '".change_date_format($row[csf("batch_date")])."';\n";  
		echo "document.getElementById('txt_batch_weight').value = '".$row[csf("batch_weight")]."';\n";  
		echo "document.getElementById('cbo_company_id').value = '".$row[csf("company_id")]."';\n"; 
		
		echo "load_drop_down( 'requires/sweater_batch_creation_controller', '".$row[csf("company_id")]."', 'load_drop_down_working_com', 'working_com');\n";
		echo "document.getElementById('cbo_working_company').value = '".$row[csf("working_company")]."';\n";  
		echo "load_drop_down('requires/sweater_batch_creation_controller','".$row[csf("working_company")]."', 'load_drop_down_location', 'working_location_td');\n"; 
		echo "document.getElementById('cbo_working_location').value = '".$row[csf("working_location")]."';\n"; 
		echo "document.getElementById('txt_batch_number').value = '".$row[csf("batch_no")]."';\n";  
		echo "document.getElementById('txt_ext_no').value = '" . $incrementExtentionNo . "';\n";  
		echo "document.getElementById('txt_batch_color').value = '".$color_arr[$row[csf("color_id")]]."';\n";
		echo "document.getElementById('txt_machine_no').value = '".$machine_arr[$row[csf("machine_no")]]."';\n";  
		echo "document.getElementById('machine_id').value = '".$row[csf("machine_no")]."';\n";    
		echo "document.getElementById('txt_process_id').value = '".$row[csf("process_id")]."';\n";
		echo "document.getElementById('txt_du_req_hr').value = '".$row[csf("dur_req_hr")]."';\n";
		echo "document.getElementById('txt_du_req_min').value = '".$row[csf("dur_req_min")]."';\n";
		echo "document.getElementById('txt_remarks').value = '".$row[csf("remarks")]."';\n";
		echo "document.getElementById('update_id').value = '".$row[csf("id")]."';\n";
		echo "document.getElementById('cbo_operation').value = '".$row[csf("operation_type")]."';\n";

		echo "document.getElementById('cbo_shift').value = '".$row[csf("shift_id")]."';\n";
		echo "document.getElementById('txt_operator').value = '".$row[csf("operator_name")]."';\n";
		echo "document.getElementById('txt_supervisor').value = '".$row[csf("supervisor_name")]."';\n";

		if ($row[csf("batch_against")] == 11)
		{
			echo "document.getElementById('cbo_batch_against').value = '" . $batch_against . "';\n";
			echo "$('#txt_batch_number').attr('readOnly','readOnly');\n";
			//$prv_batch_against = return_field_value("batch_against", "pro_batch_create_mst", "id='" . $row[csf("re_dyeing_from")] . "'");
			//echo "document.getElementById('hide_batch_against').value = '" . $prv_batch_against . "';\n";
			echo "document.getElementById('hide_update_id').value = '" . $row[csf("id")] . "';\n";
		}
		else
		{
			echo "document.getElementById('hide_update_id').value = '';\n";
		}
		echo "document.getElementById('ext_from').value = '".$ext_from."';\n";

		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_batch_creation',1);\n";	 
		echo "document.getElementById('hidden_color_id').value = '".$row[csf("color_id")]."';\n";
	}
	exit();
}

if($action=="populate_bundle_data_update")
{
    $ex_data = explode("**",$data);
	$company_id=$ex_data[0];
    $mst_id=$ex_data[1];
	
	$sql_bundle="select id, bundle_no, barcode_no from pro_bundle_batch_dtls where mst_id='$mst_id' and status_active=1 and is_deleted=0";
	$sql_bundle_res=sql_select($sql_bundle); $bundleNo=""; $bundle_count=0; $updateidArr=array();
	foreach($sql_bundle_res as $row)
	{
		$bundle_count++;
		$bundleNo.="'".$row[csf("barcode_no")]."',";
		$scanned_bundle_arr[$row[csf("bundle_no")]]=$row[csf("bundle_no")];
		$updateidArr[$row[csf("barcode_no")]]=$row[csf("id")];
	}
	unset($sql_bundle_res);
	
    $bundleNo=chop($bundleNo,',');
    $bundle_nos_cond=""; $cutbundle_nos_cond='';
    if($db_type==2 && $bundle_count>400)
    {
        $bundle_nos_cond=" and (";
		$cutbundle_nos_cond=" and (";
        $bundleArr=array_chunk(explode(",",trim($bundleNo)),399);
        foreach($bundleArr as $bundleNos)
        {
            $bundleNos=implode(",",$bundleNos);
            $bundle_nos_cond.=" c.barcode_no in($bundleNos) or ";
			$cutbundle_nos_cond.=" barcode_no in($bundleNos) or ";
        }
        $bundle_nos_cond=chop($bundle_nos_cond,'or ');
        $bundle_nos_cond.=")";
		
		$cutbundle_nos_cond=chop($cutbundle_nos_cond,'or ');
        $cutbundle_nos_cond.=")";
    }
    else
    {
        $bundle_nos_cond=" and c.barcode_no in ($bundleNo)";
		$cutbundle_nos_cond=" and barcode_no in ($bundleNo)";
    }
	
	$floorArr = return_library_array("select id, floor_name from lib_prod_floor", 'id', 'floor_name');
	$company_short_arr = return_library_array("select id, company_short_name from lib_company", 'id', 'company_short_name');
	
	$gmLbsArr=array();
	$sql_gmLbs="select barcode_no, bundle_no, bundle_qty, bundle_qtygm from pro_garments_production_dtls where production_type=51 and status_active=1 and is_deleted=0 $cutbundle_nos_cond";
	$sql_gmLbs_res = sql_select($sql_gmLbs);
	foreach($sql_gmLbs_res as $row)
	{
		$gmLbsArr[$row[csf('barcode_no')]]['lbs']=$row[csf('bundle_qty')];
		$gmLbsArr[$row[csf('barcode_no')]]['gm']=$row[csf('bundle_qtygm')];
	}
	unset($sql_gmLbs_res);
	
	$knitComFloorArr=array();
	$sql_knit="select c.barcode_no, a.sending_location, a.floor_id from pro_garments_production_mst a, pro_garments_production_dtls c where a.id=c.mst_id and a.production_type=56 and c.production_type=56 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $bundle_nos_cond";
	$sql_knit_res=sql_select($sql_knit);
	foreach($sql_knit_res as $row)
	{
		$knitComFloorArr[$row[csf('barcode_no')]]['kcomp']=$row[csf('sending_location')];
		$knitComFloorArr[$row[csf('barcode_no')]]['kfloor']=$row[csf('floor_id')];
	}
	
    $size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
    $color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
    $country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
    $buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
    
    //$output_bundles=return_library_array( "select b.bundle_no, sum(b.production_qnty) as issue_qty from pro_gmts_delivery_mst a,pro_garments_production_dtls b where  a.id=b.delivery_mst_id and b.production_type=78 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $bundle_nos_cond group by b.bundle_no",'bundle_no','issue_qty');
	
    $year_field="";
    if($db_type==0) $year_field="YEAR(f.insert_date)"; else if($db_type==2)$year_field="to_char(f.insert_date,'YYYY')";
	
	/*$sql="SELECT a.serving_company, a.floor_id, c.id as prdid, d.id as colorsizeid, e.id as po_id, f.job_no_prefix_num, $year_field as year, f.buyer_name, f.style_ref_no, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.cut_no, c.bundle_no, c.barcode_no, c.production_qnty as production_qnty, e.po_number, c.barcode_no 
	from pro_garments_production_mst a,pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e, wo_po_details_master f where f.company_name='$company_id' and a.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and e.job_no_mst=f.job_no and c.delivery_mst_id='$mst_id' and a.production_type=78 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and e.status_active in(1,2,3) and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 $bundle_nosCond  order by c.cut_no, length(c.bundle_no) asc, c.bundle_no asc";*/
	
	$sql="SELECT a.sending_location, a.floor_id, max(c.id) as prdid, d.id as colorsizeid, e.id as po_id, f.job_no_prefix_num, MAX($year_field) as year, f.buyer_name, f.style_ref_no, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.cut_no, c.bundle_no, c.barcode_no, sum(c.production_qnty) as production_qnty, e.po_number, c.barcode_no from pro_garments_production_mst a, pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e, wo_po_details_master f where f.company_name='$company_id' and a.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and e.job_no_mst=f.job_no and a.production_type=78 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and e.status_active in(1,2,3) and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 $bundle_nos_cond group by a.sending_location, a.floor_id, d.id, e.id, f.job_no_prefix_num, f.buyer_name, f.style_ref_no, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id,c.cut_no, c.bundle_no, c.barcode_no, e.po_number order by c.cut_no, length(c.bundle_no) asc, c.bundle_no asc";
    // echo $sql;// die;
	$result = sql_select($sql);
	
	$count=count($result);
	$i=$count;
	foreach ($result as $row)
	{ 
		if(trim($scanned_bundle_arr[$row[csf('bundle_no')]])!="" || $mst_id!="")
		{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$qty = ($row[csf('production_qnty')] ) - ($reject_qty[$row[csf('bundle_no')]] + $alter_qty[$row[csf('bundle_no')]]+ $spt_qty[$row[csf('bundle_no')]]);
			?>
			<tr bgcolor="<?=$bgcolor; ?>" id="tr_<?=$i;?>"> 
				<td width="30" align="center"><?=$i; ?></td>
				<td width="80" id="bundle_<?=$i; ?>" title="<?=$row[csf('barcode_no')]; ?>"><?=$row[csf('bundle_no')]; ?></td>
				<td width="90" id="barcode_<?=$i; ?>" title="<?=$row[csf('barcode_no')]; ?>"><?=$row[csf('barcode_no')]; ?></td>
                <td width="100" style="word-break:break-all;" align="left"><?=$color_arr[$row[csf('color_number_id')]]; ?></td>
				<td width="60" style="word-break:break-all;" align="center">&nbsp;<?=$size_arr[$row[csf('size_number_id')]]; ?></td>
				<td width="65" id="prodQty_<?=$i; ?>" align="right"><?=$qty; ?>&nbsp;</td>
                <td width="65" id="prodQtyGm_<?=$i; ?>" align="right"><?=$gmLbsArr[$row[csf('barcode_no')]]['gm']; ?>&nbsp;</td>
                <td width="65" id="prodQtyLbs_<?=$i; ?>" align="right"><?=$gmLbsArr[$row[csf('barcode_no')]]['lbs']; ?>&nbsp;</td>
                <td width="70" align="center"><?=$company_short_arr[$knitComFloorArr[$row[csf('barcode_no')]]['kcomp']]; ?></td>
				<td width="90" align="center"><?=$floorArr[$knitComFloorArr[$row[csf('barcode_no')]]['kfloor']]; ?></td>
                
                <td width="50" align="center"><?=$row[csf('year')]; ?></td>
				<td width="60" align="center"><?=$row[csf('job_no_prefix_num')]; ?></td>
                <td width="65"><?=$buyer_arr[$row[csf('buyer_name')]]; ?></td>
				<td width="90" style="word-break:break-all;"><?=$row[csf('style_ref_no')]; ?></td>
				<td width="90" style="word-break:break-all;" align="left"><?=$row[csf('po_number')]; ?></td>
				<td width="100" style="word-break:break-all;" align="left"><?=$garments_item[$row[csf('item_number_id')]]; ?></td>
				<td width="80" style="word-break:break-all;" align="left"><?=$country_arr[$row[csf('country_id')]]; ?></td>
				<td id="button_1" align="center">
					<input type="button" id="decrease_<?=$i; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<?=$i; ?>);" />
					
					<input type="hidden" name="txtcutNo[]" id="txtcutNo_<?=$i; ?>" value="<?=$row[csf('cut_no')]; ?>"/>
					<input type="hidden" name="txtcolorSizeId[]" id="txtcolorSizeId_<?=$i; ?>" value="<?=$row[csf('colorsizeid')]; ?>"/>
					<input type="hidden" name="txtorderId[]" id="txtorderId_<?=$i; ?>" value="<?=$row[csf('po_id')]; ?>"/>
					<input type="hidden" name="txtgmtsitemId[]" id="txtgmtsitemId_<?=$i; ?>" value="<?=$row[csf('item_number_id')]; ?>"/>
					<input type="hidden" name="txtcountryId[]" id="txtcountryId_<?=$i; ?>" value="<?=$row[csf('country_id')]; ?>"/>
					<input type="hidden" name="txtcolorId[]" id="txtcolorId_<?=$i; ?>" value="<?=$row[csf('color_number_id')]; ?>"/>
					<input type="hidden" name="txtsizeId[]" id="txtsizeId_<?=$i; ?>" value="<?=$row[csf('size_number_id')]; ?>"/>
					<input type="hidden" name="txtqty[]" id="txtqty_<?=$i; ?>" value="<?=$qty; ?>"/>
                    <input type="hidden" name="txtqtygm[]" id="txtqtygm_<?=$i; ?>" value="<?=$gmLbsArr[$row[csf('barcode_no')]]['gm']; ?>"/>
                    <input type="hidden" name="txtqtylbs[]" id="txtqtylbs_<?=$i; ?>" value="<?=$gmLbsArr[$row[csf('barcode_no')]]['lbs']; ?>"/>
					<input type="hidden" name="dtlsId[]" id="dtlsId_<?=$i; ?>" value="<?=$updateidArr[$row[csf("barcode_no")]]; ?>"/> 
				</td>
			</tr>
			<?
			$i--;
		}
	}
    exit(); 
}

if($action=="process_name_popup")
{
  	echo load_html_head_contents("Process Name Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
	?>
		<script>
		
			$(document).ready(function(e) {
				setFilterGrid('tbl_list_search',-1);
			});
			
			var selected_id = new Array(); var selected_name = new Array();
			
			function check_all_data() 
			{
				var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length; 
	
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
				var old=document.getElementById('txt_process_row_id').value; 
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
				
				$('#hidden_process_id').val(id);
				$('#hidden_process_name').val(name);
			}
		</script>
	</head>
	<body>
	<div align="center">
		<fieldset style="width:370px;margin-left:10px">
			<input type="hidden" name="hidden_process_id" id="hidden_process_id" class="text_boxes" value="">
			<input type="hidden" name="hidden_process_name" id="hidden_process_name" class="text_boxes" value="">
			<form name="searchprocessfrm_1"  id="searchprocessfrm_1" autocomplete="off">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="350" class="rpt_table" >
					<thead>
						<th width="50">SL</th>
						<th>Process Name</th>
					</thead>
				</table>
				<div style="width:350px; overflow-y:scroll; max-height:280px;" id="buyer_list_view" align="center">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="332" class="rpt_table" id="tbl_list_search" >
					<?
						$i=1; $process_row_id=''; 
	
						$hidden_process_id=explode(",",$txt_process_id);
						foreach($wash_type as $id=>$name)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							 
							if(in_array($id,$hidden_process_id)) 
							{ 
								if($process_row_id=="") $process_row_id=$i; else $process_row_id.=",".$i;
							}
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)"> 
								<td width="50" align="center"><?php echo "$i"; ?>
									<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $id; ?>"/>	
									<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $name; ?>"/>
									<input type="hidden" name="txt_mandatory" id="txt_mandatory<?php echo $i ?>" value="<? echo $mandatory; ?>"/>
								</td>	
								<td><p><? echo $name; ?></p></td>
							</tr>
							<?
							$i++;
						}
					?>
						<input type="hidden" name="txt_process_row_id" id="txt_process_row_id" value="<?php echo $process_row_id; ?>"/>
					</table>
				</div>
				 <table width="350" cellspacing="0" cellpadding="0" style="border:none" align="center">
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
			</form>
		</fieldset>
	</div>    
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	<script>set_all();</script>
	</html>
	<?
	exit();
}

if($action=="batch_card_print") 
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$company=$data[0];
	$batchagainst=$data[5];
	$batch_update_id=$data[1];
	//$batch_mst_update_id=str_pad($batch_update_id,10,'0',STR_PAD_LEFT);
	$batch_mst_update_id=$data[3];
	$batch_sl_no=$data[2];
	//echo $data[0]."**".$data[1]."**".$data[2]."**".$data[3]."**".$data[4]."**".$data[5];die;
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$machine_arr=return_library_array( "select id, machine_no from lib_machine_name",'id','machine_no');
	$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');

	$dataArray=sql_select("select a.batch_no, a.color_id, a.batch_date, a.batch_against, a.shift_id, a.batch_weight, a.machine_no, a.working_company, a.process_id, a.remarks, a.operator_name, a.supervisor_name, b.po_id, b.id, b.qty_pcs from pro_bundle_batch_mst a, pro_bundle_batch_dtls b where a.id=$data[1] and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	//print_r($dataArray);

	$order_num=array(); $cust_buyer_arr=array(); $cust_style_ref_arr=array(); $party_id_arr=array(); $Gmts_qty=0;
	foreach ($dataArray as $value) 
	{
		if($all_po_id=="") $all_po_id=$value[csf("po_id")];else $all_po_id.=",".$value[csf("po_id")];
	}
	
	$poIds=chop($all_po_id,','); $po_cond_for_in="";
	$po_ids=count(array_unique(explode(",",$all_po_id)));
	if($db_type==2 && $po_ids>1000)
	{
		$po_cond_for_in=" and (";
		$poIdsArr=array_chunk(explode(",",$poIds),999);
		foreach($poIdsArr as $ids)
		{
			$ids=implode(",",$ids);
			//$poIds_cond.=" po_break_down_id in($ids) or ";
			$po_cond_for_in.=" b.id in($ids) or"; 
		}
		$po_cond_for_in=chop($po_cond_for_in,'or ');
		$po_cond_for_in.=")";
	}
	else
	{
		$po_ids=implode(",",(array_unique(explode(",",$all_po_id))));
		$po_cond_for_in=" and b.id in($po_ids)";
	}
	
	$order_sql=sql_select("select a.id as job_id, a.job_no, a.job_no_prefix_num, a.style_ref_no, a.buyer_name, b.id as po_id, b.po_number,a.dealing_marchant,a.team_leader,b.grouping  from  wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 $po_cond_for_in");
	$order_data_arr=array();
	foreach($order_sql as $row)
	{
		$order_data_arr[$row[csf("po_id")]]["po_id"]=$row[csf("po_id")];
		$order_data_arr[$row[csf("po_id")]]["job_no"]=$row[csf("job_no")];
		$order_data_arr[$row[csf("po_id")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
		$order_data_arr[$row[csf("po_id")]]["buyer_name"]=$row[csf("buyer_name")];
		$order_data_arr[$row[csf("po_id")]]["style_ref_no"]=$row[csf("style_ref_no")];
		$order_data_arr[$row[csf("po_id")]]["po_number"]=$row[csf("po_number")];
	}
	unset($order_sql);
	$i=0;
	foreach($dataArray as $row)
	{
		$i++;
		$buyerName_data[]	=$party_arr[$order_data_arr[$row[csf("po_id")]]["buyer_name"]];
		$jobNo[]			=$order_data_arr[$row[csf("po_id")]]["job_no"];
		$styleRef[]			=$order_data_arr[$row[csf("po_id")]]["style_ref_no"];
		$gmts_qty+=$row[csf('qty_pcs')];
	}
	if($db_type==0) $printtime = date("Y-m-d H:i:s",time());
	else $printtime = date("d-M-Y h:i:s A",time());

	?>
	<table width="920" cellspacing="0" align="center" border="0">
		<tr>
			<td colspan="2" align="center" style="font-size:22px"><strong><?=$company_library[$dataArray[0][csf("working_company")]]; ?></strong></td>
            <td rowspan="2"><div id="qrcode"></div></td>
		</tr>
		<tr>
			<td colspan="2" align="center" style="font-size:16px;">
				<strong>Batch Card - <?=$batch_against[$dataArray[0][csf("batch_against")]]; ?></strong>
			</td>
		</tr>
        <tr>
        	<td colspan="3" align="right" style="font-size:12px">Print Time: <?=$printtime; ?></td>
        </tr>
	</table>
	<table width="920" cellspacing="0" align="center" border="1" rules="all" class="rpt_table" >
		<tr>
			<td width="130"><strong>Batch No.:</strong></td>
			<td width="175" style="word-break:break-all;"><?=$data[3]; ?> </td>	
			<td width="130"><strong>Buyer :</strong></td>
			<td width="175" style="word-break:break-all;"><?=implode(",", array_unique($buyerName_data)); ?></td>
            <td width="130"><strong>Job No :</strong></td>
			<td style="word-break:break-all;"><?=implode(",", array_unique($jobNo)); ?></td>		
		</tr>
		<tr>
			<td><strong>Batch Date :</strong></td>
			<td><?=change_date_format($dataArray[0][csf("batch_date")]); ?> </td>	
			<td style="word-break:break-all;"><strong>Batch Color:</strong></td>	
			<td><?=$color_arr[$dataArray[0][csf("color_id")]]; ?> </td>
            <td><strong>Shift</strong></td>	
			<td><?=$shift_name[$dataArray[0][csf("shift_id")]]; ?> </td>		
		</tr>
        <tr>
			<td><strong>Style Ref.:</strong></td>
			<td colspan="3" style="word-break:break-all;"><?=implode(",", array_unique($styleRef)); ?></td>
            <td><strong>Batch Weight:</strong></td>
			<td align="center"><?=number_format(($dataArray[0][csf("batch_weight")]/1000),2); ?> [KG]</td>
		</tr>
        <tr>
			<td><strong>Gmt Qty(Pcs):</strong></td>	
			<td align="center"><?=$gmts_qty; ?></td>	
			<td><strong>Wash Type:</strong></td>	
			<td colspan="3" style="word-break:break-all;">
				<? 
					$process_id_array=explode(",",$dataArray[0][csf("process_id")]);
					foreach($process_id_array as $val)
					{
						if($process_name=="") $process_name=$wash_type[$val]; else $process_name.=",".$wash_type[$val];
					}
					echo $process_name;
				?>
			</td>	
		</tr>
		<tr>
			<td><strong>Operator :</strong></td>
			<td style="word-break:break-all;"><?=$dataArray[0][csf("operator_name")]; ?></td>
            <td><strong>No Of Bundle:</strong></td>	
			<td align="center"><?=$i; ?></td>
            <td><strong>M/C No.:</strong></td>
			<td style="word-break:break-all;"><?=$machine_arr[$dataArray[0][csf("machine_no")]]; ?></td>
		</tr>
		<tr>
        	<td><strong>Supervisor</strong></td>
			<td style="word-break:break-all;"><?=$dataArray[0][csf("supervisor_name")]; ?></td>
			<td><strong>Dryer No.:</strong></td>	
			<td>&nbsp;</td>	
            <td><strong>Dryer Op. Name:</strong></td>	
			<td>&nbsp;</td>	
		</tr>
		<tr>
			<td><strong>Remarks</strong></td>
			<td colspan="5" style="word-break:break-all;"><?=$dataArray[0][csf("remarks")]; ?></td>		
		</tr>
	</table>
    <br>
    <br>
    <table width="920" cellspacing="0" align="center" rules="all">
        <tr>
        	<td align="center">Prepared By</td>
            <td align="center">Check By</td>
            <td align="center">Authorized By</td>
        </tr>
    </table>
    
    <script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquery.qrcode.min.js"></script>
    <script>
        var main_value='<?=$dataArray[0][csf("batch_no")]; ?>';
        $('#qrcode').qrcode(main_value);
    </script>
	<?
	exit();
}

if($action=="machineNo_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$cbo_company_id=str_replace("'","",$cbo_company_id); 
	$cbo_batch_against=str_replace("'","",$cbo_batch_against);

	if($cbo_batch_against==6){$category=6;}
	//else if($cbo_batch_against==7){$category=2;}
	//else if($cbo_batch_against==10){$category=3;}

	?>
    <script>
    function js_set_value(data)
    {
		var data=data.split("_");
		$("#hidden_machine_id").val(data[0]);
		$("#hidden_machine_name").val(data[1]); 
		parent.emailwindow.hide();
    }
	</script>
    
    <input type="hidden" id="hidden_machine_id" name="hidden_machine_id">
    <input type="hidden" id="hidden_machine_name" name="hidden_machine_name">
    
<? 
	 $location_name=return_library_array( "select location_name,id from  lib_location where is_deleted=0", "id", "location_name"  );
	 $floor=return_library_array( "select floor_name,id from  lib_prod_floor where is_deleted=0", "id", "floor_name"  );
	 $arr=array(0=>$location_name,1=>$floor);  
	 
	 $sql="select location_id,floor_id,machine_no,machine_group,dia_width,gauge,id from lib_machine_name where is_deleted=0 and status_active=1 and company_id='$cbo_company_id' and category_id in ($category)";
     echo create_list_view ( "list_view", "Location Name,Floor Name,Machine No,Machine Group,Dia Width,Gauge", "150,140,100,120,80","740","300",1, $sql, "js_set_value", "id,machine_no","", 1, "location_id,floor_id,0,0,0,0", $arr, "location_id,floor_id,machine_no,machine_group,dia_width,gauge", "", 'setFilterGrid("list_view",-1);','') ;

	exit();	 
}

if($action == "show_color_listview")
{
	$data = explode("*", $data);
	
	//print_r($data);
	$poId = $data[0];
	$cboItem = $data[1];
	$rowNum = $data[2];
	$hiddenOperationTypeId = $data[3]*1;
	$ColorId = $data[4];
	$PoNo = $data[5];
	$batch_no = $data[6];
	$batch_id = $data[7];
	$batch_dtls_id = $data[8];
	$operationtypeId = $data[9]*1;
	$update_id = $data[10];
	
	
	//echo $update_id."mahbub";die;
	//////////////// start previous backup 
	/*$batch_qty_arr=array();
	$batch_dtls_sql="select a.color_id, b.po_id, b.prod_id, sum(b.roll_no) as roll_no from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_id, b.prod_id,a.color_id";
	$batchArray = sql_select($batch_dtls_sql);
	foreach ($batchArray as $value) 
	{
		$batch_qty_arr[$value[csf("po_id")]][$value[csf("prod_id")]][$value[csf("color_id")]]=$value[csf("roll_no")];
	}*/
	
	
	/*$issue_qty_arr=array();
	$issue_dtls_sql="select b.quantity,b.uom,b.job_dtls_id, b.buyer_po_id from sub_material_mst a, sub_material_dtls b where  a.entry_form=297 and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ";
	$issueArray = sql_select($issue_dtls_sql);
	foreach ($issueArray as $value) 
	{
		$issue_qty_arr[$value[csf("job_dtls_id")]]+=$value[csf("quantity")];
	}
*/
	///////////////////////////// end previous backup
	
	
	 $batch_qty_arr=array();
	 $batch_dtls_sql="select a.color_id, b.po_id, b.prod_id,c.mst_id,c.job_no_mst,c.buyer_po_no,c.buyer_po_id, sum(b.roll_no) as roll_no from pro_batch_create_mst a, pro_batch_create_dtls b,subcon_ord_dtls c where a.entry_form=316 and a.id=b.mst_id and  b.po_id=c.id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.po_id, b.prod_id,a.color_id,c.mst_id,c.job_no_mst,c.buyer_po_no,c.buyer_po_id";
	$batchArray = sql_select($batch_dtls_sql);
	foreach ($batchArray as $value) 
	{
		$batch_qty_arr[$value[csf("job_no_mst")]][$value[csf("buyer_po_no")]][$value[csf("color_id")]]+=$value[csf("roll_no")];
	}
	//echo "<pre>";
	//print_r($batch_qty_arr);
	



	$issue_qty_arr=array();
	$issue_dtls_sql="select b.quantity,b.uom,b.job_dtls_id,a.embl_job_no,c.gmts_color_id,c.buyer_po_no,c.mst_id,c.buyer_po_id,c.job_no_mst from sub_material_mst a, sub_material_dtls b,subcon_ord_dtls c where  a.entry_form=297 and a.id=b.mst_id and  c.id=b.job_dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 ";
	
	$issueArray = sql_select($issue_dtls_sql);
	foreach ($issueArray as $value) 
	{
		$issue_qty_arr[$value[csf("job_no_mst")]][$value[csf("buyer_po_no")]][$value[csf("gmts_color_id")]]+=$value[csf("quantity")];
	}
//echo $ColorId."mahbub";die;
	//$sql="select b.order_uom,c.order_id, c.item_id, c.color_id, sum(c.qnty) as po_qnty from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c where a.id=c.mst_id and b.id=c.order_id and a.subcon_job=b.job_no_mst and c.order_id in ($poId) and c.item_id in ($cboItem) group by c.order_id, c.color_id, c.item_id,b.order_uom";
	
	
	$cbo_Item_cond = "";
	if ($cboItem !="") $cbo_Item_cond = " and b.gmts_item_id in (".$cboItem.")";
	
	$poId_cond = "";
	if ($poId !="") $poId_cond = " and b.id in (".$poId.")";
	
	
	 $sql="select b.order_uom, b.id,b.mst_id, b.order_no, b.gmts_item_id as item_id, b.gmts_color_id as color_id, b.order_quantity as po_qnty,a.within_group, a.party_id,b.buyer_po_id,b.buyer_po_no,a.gmts_type,b.job_no_mst from subcon_ord_mst a, subcon_ord_dtls b where a.id=b.mst_id and a.subcon_job=b.job_no_mst and a.entry_form=295 $poId_cond $cbo_Item_cond order by id DESC";
	
	
	
	//$sql = "select a.subcon_job as job_no, a.party_id as buyer_name, b.order_uom, b.gmts_item_id as gmts_item_id, b.gmts_color_id as color_id, b.id, b.order_no as po_number, b.order_quantity as po_qnty from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.id=b.mst_id and a.entry_form=295 and a.company_id=$company_id $color_cond $party_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by b.id DESC";
	
	$color_id_cond = "";
	if ($ColorId !="") $color_id_cond = " and a.color_id in (".$ColorId.")";
	
	$po_id_cond = "";
	if ($poId !="") $po_id_cond = " and b.po_id in (".$poId.")";
	
	
	
/*  $operation_type_sql="select a.color_id,a.operation_type, b.id, b.po_id, b.prod_id,b.roll_no as batch_qnty, b.buyer_po_id from pro_batch_create_mst a, pro_batch_create_dtls b where  b.po_id in($poId) and a.color_id in($ColorId)  and a.entry_form=316 and  a.id=b.mst_id and b.status_active=1 and b.is_deleted=0  order by b.id";*/ 

$operation_type_sql="select a.color_id,a.operation_type, b.id, b.po_id, b.prod_id,b.roll_no as batch_qnty, b.buyer_po_id from pro_batch_create_mst a, pro_batch_create_dtls b where  a.entry_form=316 and  a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 $po_id_cond $color_id_cond  order by b.id";
	$data_array=sql_select($operation_type_sql); 
	$operation_type_array=array();
	
	foreach($data_array as $row)
	{
		$operation_type_array[$row[csf('po_id')]][$row[csf('operation_type')]]['operation_type']=$row[csf('operation_type')];	
		$operation_type_array[$row[csf('po_id')]][$row[csf('operation_type')]]['color_id']=$row[csf('color_id')];
		$operation_type_array[$row[csf('po_id')]][$row[csf('operation_type')]]['batch_qnty']+=$row[csf('batch_qnty')];	
	}
	//echo "<pre>";
	//print_r($operation_type_array);
	
	
	
	//echo $update_id."mahbub";die;
	
	if($update_id!="")
	{
	
	/* $current_operation_type_sql="select a.color_id,a.operation_type, b.id, b.po_id, b.prod_id, b.roll_no as batch_qnty, b.buyer_po_id from pro_batch_create_mst a, pro_batch_create_dtls b where  b.po_id in($poId) and a.color_id in($ColorId)  and a.entry_form=316 and  a.id=b.mst_id and b.status_active=1 and b.is_deleted=0  and a.id<>$update_id order by b.id";*/
	 
	 $current_operation_type_sql="select a.color_id,a.operation_type, b.id, b.po_id, b.prod_id, b.roll_no as batch_qnty, b.buyer_po_id from pro_batch_create_mst a, pro_batch_create_dtls b where   a.entry_form=316 and  a.id=b.mst_id and b.status_active=1 and b.is_deleted=0   and a.id<>$update_id $po_id_cond  $color_id_cond  order by b.id";
	$current_data_array=sql_select($current_operation_type_sql); 
	
	}
	$current_operation_type_array=array();
	 
	foreach($current_data_array as $row)
	{
		$current_operation_type_array[$row[csf('po_id')]][$row[csf('operation_type')]]['operation_type']=$row[csf('operation_type')];	
		$current_operation_type_array[$row[csf('po_id')]][$row[csf('operation_type')]]['color_id']=$row[csf('color_id')];
		$current_operation_type_array[$row[csf('po_id')]][$row[csf('operation_type')]]['batch_qnty']+=$row[csf('batch_qnty')];	
	}
	
	//echo "<pre>";
	//print_r($operation_type_array);
	
	//echo $OperationTypeId."mahbub"; die;
	
	
			

	$i = 1;
	$nameArray = sql_select($sql);
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="330" class="rpt_table color_tble" style="float: left;">
		<thead>
			<th width="25">SL</th>
			<th width="60">PO No</th>
			<th width="100">Gmts Item</th>
			<th width="80">Batch Color</th>
			<th width="75">Meterial Issue Qty (Pcs) </th>
			<th width="75">Total Batch Qty (Pcs)</th>     
			<th width="">Balance (Pcs)</th>              
		</thead>
		<tbody>
		<?
		foreach ($nameArray as $selectResult) 
		{
				if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
				
				//$operationtypeId;
				
			
			$prv_first_wash_qty=$operation_type_array[$selectResult[csf("id")]][1]['batch_qnty'];
			$prv_fainal_wash_qty=$operation_type_array[$selectResult[csf("id")]][2]['batch_qnty'];
			$prv_first_dyeing_qty=$operation_type_array[$selectResult[csf("id")]][3]['batch_qnty'];
			$prv_secend_dyeing_qty=$operation_type_array[$selectResult[csf("id")]][4]['batch_qnty'];
					
			if(str_replace("'","",$update_id)=="")
			{
			//echo $hiddenOperationTypeId."==".$operationtypeId."===".$prv_first_dyeing_qty;;
				if($hiddenOperationTypeId=="" && $operationtypeId=="")//done
				{
					//echo $prv_first_wash_qty."==".$prv_fainal_wash_qty."==".$prv_first_dyeing_qty."==".$prv_secend_dyeing_qty;
					$issue_qty=$issue_qty_arr[$selectResult[csf("job_no_mst")]][$selectResult[csf("buyer_po_no")]][$selectResult[csf("color_id")]];
					$batch_qty=$batch_qty_arr[$selectResult[csf("job_no_mst")]][$selectResult[csf("buyer_po_no")]][$selectResult[csf("color_id")]];
					//$batch_balance=($issue_qty)-$batch_qty;
					$batch_balance=($issue_qty)-$batch_qty;
				}
				else if($hiddenOperationTypeId=="" && $operationtypeId==1)//done
				{
					//echo $prv_first_wash_qty."==".$prv_fainal_wash_qty."==".$prv_first_dyeing_qty."==".$prv_secend_dyeing_qty;
					if($prv_fainal_wash_qty=="")
					{
						$issue_qty=$issue_qty_arr[$selectResult[csf("job_no_mst")]][$selectResult[csf("buyer_po_no")]][$selectResult[csf("color_id")]];
						$batch_qty=$batch_qty_arr[$selectResult[csf("job_no_mst")]][$selectResult[csf("buyer_po_no")]][$selectResult[csf("color_id")]];
						//$batch_balance=($issue_qty)-$batch_qty;
						$batch_balance=($issue_qty)-$batch_qty;
					}
				}
				else if($hiddenOperationTypeId=="" && $operationtypeId==2)//done
				{
					//echo $prv_first_wash_qty."==".$prv_fainal_wash_qty."==".$prv_first_dyeing_qty."==".$prv_secend_dyeing_qty;
						$issue_qty=$issue_qty_arr[$selectResult[csf("job_no_mst")]][$selectResult[csf("buyer_po_no")]][$selectResult[csf("color_id")]];
						$batch_qty=$batch_qty_arr[$selectResult[csf("job_no_mst")]][$selectResult[csf("buyer_po_no")]][$selectResult[csf("color_id")]];
						//$batch_balance=($issue_qty)-$batch_qty;
						$batch_balance=($issue_qty)-$batch_qty;
					
				}
				else if($hiddenOperationTypeId=="" && $operationtypeId==3)//done
				{
					//echo $prv_first_wash_qty."==".$prv_fainal_wash_qty."==".$prv_first_dyeing_qty."==".$prv_secend_dyeing_qty;
					if($prv_secend_dyeing_qty=="")
					{
						$issue_qty=$issue_qty_arr[$selectResult[csf("job_no_mst")]][$selectResult[csf("buyer_po_no")]][$selectResult[csf("color_id")]];
						$batch_qty=$batch_qty_arr[$selectResult[csf("job_no_mst")]][$selectResult[csf("buyer_po_no")]][$selectResult[csf("color_id")]];
						//$batch_balance=($issue_qty)-$batch_qty;
						$batch_balance=($issue_qty)-$batch_qty;
					}
				}
				else if($hiddenOperationTypeId=="" && $operationtypeId==4)//done
				{
					//echo $prv_first_wash_qty."==".$prv_fainal_wash_qty."==".$prv_first_dyeing_qty."==".$prv_secend_dyeing_qty;
					$issue_qty=$issue_qty_arr[$selectResult[csf("job_no_mst")]][$selectResult[csf("buyer_po_no")]][$selectResult[csf("color_id")]];
					$batch_qty=$batch_qty_arr[$selectResult[csf("job_no_mst")]][$selectResult[csf("buyer_po_no")]][$selectResult[csf("color_id")]];
					//$batch_balance=($issue_qty)-$batch_qty;
					$batch_balance=($issue_qty)-$batch_qty;
				}
				else if($hiddenOperationTypeId==1 && $operationtypeId==1)//done
				{
					//echo $prv_first_wash_qty."==".$prv_fainal_wash_qty."==".$prv_first_dyeing_qty."==".$prv_secend_dyeing_qty;
					if($prv_fainal_wash_qty=="")
					{
						$issue_qty=$issue_qty_arr[$selectResult[csf("job_no_mst")]][$selectResult[csf("buyer_po_no")]][$selectResult[csf("color_id")]];
						$batch_qty=$prv_first_wash_qty;
						//$batch_balance=($issue_qty)-$batch_qty;
						$batch_balance=($issue_qty)-$batch_qty;
					}
				}
				else if($hiddenOperationTypeId==1 && $operationtypeId==2)//done
				{
					$issue_qty=$prv_first_wash_qty;
					if($prv_fainal_wash_qty!="")
					{
						$batch_qty=$prv_fainal_wash_qty;
					}
					else
					{
						$batch_qty=0;
					}
					$batch_balance=($issue_qty)-$batch_qty;
					//$batch_balance=$prv_first_wash_qty;
				}
				else if($hiddenOperationTypeId==2 && $operationtypeId==1)//done
				{
					if($prv_fainal_wash_qty=="")
					{
						$issue_qty=$issue_qty_arr[$selectResult[csf("job_no_mst")]][$selectResult[csf("buyer_po_no")]][$selectResult[csf("color_id")]];
						$batch_qty=$prv_first_wash_qty;
						//$batch_balance=($issue_qty)-$batch_qty;
						$batch_balance=($issue_qty)-$batch_qty;
					}
				}
				else if ($hiddenOperationTypeId==2 && $operationtypeId==2)//done
				{
					//echo $prv_fainal_wash_qty."==".$prv_first_wash_qty."===".$update_id;
					if($prv_fainal_wash_qty!="" && $prv_first_wash_qty!="")
					{
						
						//echo $hiddenOperationTypeId."==".$operationtypeId."===".$update_id;
						$issue_qty=$prv_first_wash_qty;
						$batch_qty=$prv_fainal_wash_qty;
						$batch_balance=($issue_qty)-$batch_qty;
					}
					else
					{
						$issue_qty=$issue_qty_arr[$selectResult[csf("job_no_mst")]][$selectResult[csf("buyer_po_no")]][$selectResult[csf("color_id")]];
						$batch_qty=$batch_qty_arr[$selectResult[csf("job_no_mst")]][$selectResult[csf("buyer_po_no")]][$selectResult[csf("color_id")]];
						$batch_balance=($issue_qty)-$batch_qty;
					}
				}
				else if ($hiddenOperationTypeId==3 && $operationtypeId==3)
				{	
					//$issue_qty=$issue_qty_arr[$selectResult[csf("job_no_mst")]][$selectResult[csf("buyer_po_no")]][$selectResult[csf("color_id")]];
					//$batch_qty=$batch_qty_arr[$selectResult[csf("job_no_mst")]][$selectResult[csf("buyer_po_no")]][$selectResult[csf("color_id")]];
					//$batch_balance=($issue_qty)-$batch_qty;
					//$batch_balance=($issue_qty)-$batch_qty;
					
					//echo $prv_first_wash_qty."==".$prv_fainal_wash_qty."==".$prv_first_dyeing_qty."==".$prv_secend_dyeing_qty;
					if($prv_secend_dyeing_qty=="")
					{
						$issue_qty=$issue_qty_arr[$selectResult[csf("job_no_mst")]][$selectResult[csf("buyer_po_no")]][$selectResult[csf("color_id")]];
						$batch_qty=$prv_first_dyeing_qty;
						//$batch_balance=($issue_qty)-$batch_qty;
						$batch_balance=($issue_qty)-$batch_qty;
					}
					
					
					
				}
				else if ($hiddenOperationTypeId==3 && $operationtypeId==4)
				{	
					$issue_qty=$prv_first_dyeing_qty;
					
					if($prv_secend_dyeing_qty!="")
					{
						$batch_qty=$prv_secend_dyeing_qty;
					}
					else
					{
						$batch_qty=0;
					}
					$batch_balance=($issue_qty)-$batch_qty;
				}
				else if($hiddenOperationTypeId==4 && $operationtypeId==3)
				{
					if($prv_secend_dyeing_qty=="")
					{
						$issue_qty=$issue_qty_arr[$selectResult[csf("job_no_mst")]][$selectResult[csf("buyer_po_no")]][$selectResult[csf("color_id")]];
						$batch_qty=$prv_first_dyeing_qty;
						//$batch_balance=($issue_qty)-$batch_qty;
						$batch_balance=($issue_qty)-$batch_qty;
					}
				}
				else if ($hiddenOperationTypeId==4 && $operationtypeId==4)
				{
					
					if($prv_secend_dyeing_qty!="" && $prv_secend_dyeing_qty!="")
					{	
						
						$issue_qty=$prv_first_dyeing_qty;
						$batch_qty=$prv_secend_dyeing_qty;
						$batch_balance=($issue_qty)-$batch_qty;
					}
					else
					{
						$issue_qty=$issue_qty_arr[$selectResult[csf("job_no_mst")]][$selectResult[csf("buyer_po_no")]][$selectResult[csf("color_id")]];
						$batch_qty=$batch_qty_arr[$selectResult[csf("job_no_mst")]][$selectResult[csf("buyer_po_no")]][$selectResult[csf("color_id")]];
						$batch_balance=($issue_qty)-$batch_qty;
					}
				}
				
		}
				
			if(str_replace("'","",$update_id)!="")
			{
					$total_prv_first_wash_qty=$current_operation_type_array[$selectResult[csf("id")]][1]['batch_qnty'];
					$total_fainal_wash_qty=$current_operation_type_array[$selectResult[csf("id")]][2]['batch_qnty'];
					$total_first_dyeing_qty=$current_operation_type_array[$selectResult[csf("id")]][3]['batch_qnty'];
					$total_secend_dyeing_qty=$current_operation_type_array[$selectResult[csf("id")]][4]['batch_qnty'];
					//echo $hiddenOperationTypeId."==".$operationtypeId."===".$update_id; die;
						if($operationtypeId==1)
						{
							
							if($total_fainal_wash_qty=="")
							{
							//echo $hiddenOperationTypeId."==".$operationtypeId."===".$update_id; die;
								$issue_qty=$issue_qty_arr[$selectResult[csf("job_no_mst")]][$selectResult[csf("buyer_po_no")]][$selectResult[csf("color_id")]];
								//$batch_qty=$batch_qty_arr[$selectResult[csf("job_no_mst")]][$selectResult[csf("buyer_po_no")]][$selectResult[csf("color_id")]];
								
								if($total_prv_first_wash_qty=="")
								{
									$batch_qty=0;
								}
								else
								{
									$batch_qty=$total_prv_first_wash_qty;
								}
								
								$batch_balance=($issue_qty)-$batch_qty;
							}
							
							
							
							/*$issue_qty=$issue_qty_arr[$selectResult[csf("job_no_mst")]][$selectResult[csf("buyer_po_no")]][$selectResult[csf("color_id")]];
							$batch_qty=$batch_qty_arr[$selectResult[csf("job_no_mst")]][$selectResult[csf("buyer_po_no")]][$selectResult[csf("color_id")]];
							$batch_balance=($issue_qty)-$batch_qty;*/
						}
						else if($operationtypeId==2)
						{
							
							if($prv_first_wash_qty=="")
							{
								$issue_qty=$issue_qty_arr[$selectResult[csf("job_no_mst")]][$selectResult[csf("buyer_po_no")]][$selectResult[csf("color_id")]];
							}
							else
							{
								$issue_qty=$prv_first_wash_qty;
							}
							
							if($total_fainal_wash_qty=="")
							{
								$batch_qty=0;
							}
							else
							{
								$batch_qty=$total_fainal_wash_qty;
							}
							
							$batch_balance=($issue_qty)-$batch_qty;
						}
						else if($operationtypeId==3)
						{
							
							if($total_secend_dyeing_qty=="")
							{
								//echo $hiddenOperationTypeId."==".$operationtypeId."===".$update_id; die;
								$issue_qty=$issue_qty_arr[$selectResult[csf("job_no_mst")]][$selectResult[csf("buyer_po_no")]][$selectResult[csf("color_id")]];
								//$batch_qty=$batch_qty_arr[$selectResult[csf("job_no_mst")]][$selectResult[csf("buyer_po_no")]][$selectResult[csf("color_id")]];
								
								if($total_first_dyeing_qty=="")
								{
									$batch_qty=0;
								}
								else
								{
									$batch_qty=$total_first_dyeing_qty;
								}
								
								$batch_balance=($issue_qty)-$batch_qty;
							}
							
						}
						else if($operationtypeId==4)
						{
							
							if($prv_first_dyeing_qty=="")
							{
								$issue_qty=$issue_qty_arr[$selectResult[csf("job_no_mst")]][$selectResult[csf("buyer_po_no")]][$selectResult[csf("color_id")]];
							}
							else
							{
								$issue_qty=$prv_first_dyeing_qty;
							}
							
							
							if($total_secend_dyeing_qty=="")
							{
								$batch_qty=0;
							}
							else
							{
								$batch_qty=$total_secend_dyeing_qty;
							}
							
							$batch_balance=($issue_qty)-$batch_qty;
						}
					
				}
				
				
				
				
							/*$issue_qty=$issue_qty_arr[$selectResult[csf("job_no_mst")]][$selectResult[csf("buyer_po_no")]][$selectResult[csf("color_id")]];
							$batch_qty=$batch_qty_arr[$selectResult[csf("job_no_mst")]][$selectResult[csf("buyer_po_no")]][$selectResult[csf("color_id")]];
							$batch_balance=($issue_qty)-$batch_qty;*/
				
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>" onClick="color_set_value('<? echo $color_arr[$selectResult[csf('color_id')]]; ?>','<? echo $batch_balance; ?>','<? echo $rowNum; ?>','<? echo $selectResult[csf('within_group')]; ?>','<? echo $selectResult[csf('party_id')]; ?>','<? echo $selectResult[csf('buyer_po_id')]; ?>','<? echo $selectResult[csf('gmts_type')]; ?>','<? echo $selectResult[csf('buyer_po_no')]; ?>')"> 
				<td align="center" title=""><? echo $i; ?> </td>

				<td align="center"><? echo $selectResult[csf('order_no')]; ?></td>
				<td align="center"><? echo $garments_item[$selectResult[csf('item_id')]]; ?></td>

				<td title="<? echo $selectResult[csf('color_id')]; ?>"><p><? echo $color_arr[$selectResult[csf('color_id')]]; ?></p></td>
				<td align="center"><p><? echo $issue_qty;//$issue_qty_arr[$selectResult[csf("id")]]; ?></p></td>
				<td align="center"><p><? echo $batch_qty;//$batch_qty_arr[$selectResult[csf("id")]][$selectResult[csf("item_id")]][$selectResult[csf("color_id")]]; ?></p></td>
				<td align="center" title=""><p><? echo $batch_balance; ?></p></td>
			</tr>
			<?
			$i++;
		}
		?>
		</tbody>
	</table>
	<?
	exit();
}