<?
/*-------------------------------------------- Comments
Purpose			         :  This Form Will Create Sample Requisition Entry.
Functionality	         :
JS Functions	         :
Created by		         :	Rehan Uddin
Creation date 	         : 10/12/2016
Requirment Client        :
Requirment By            :
Requirment type          :
Requirment               :
Affected page            :
Affected Code            :
DB Script                :
Updated by 		         :
Update date		         :
QC Performed BY	         :
QC Date			         :
Comments		         :
*/
header('Content-type:text/html; charset=utf-8');
session_start();
if($_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
extract($_REQUEST);
include('../../../includes/common.php');
$data=$_REQUEST['data'];
$user_id=$_SESSION['logic_erp']['user_id'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];

$user_id=$_SESSION['logic_erp']['user_id'];
$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, company_location_id as location_id FROM user_passwd where id=$user_id");
$company_id = $userCredential[0][csf('company_id')];
$location_id = $userCredential[0][csf('location_id')];
$company_credential_cond = "";
if ($company_id >0) {
    $company_credential_cond = " and comp.id in($company_id)";
}
if ($location_id !='') {
    $location_credential_cond = " and id in($location_id)";
}

$color_arr=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name" );
$size_arr=return_library_array( "select id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name" );


function image_mandatory($data){
	//echo "select season_mandatory from variable_order_tracking where company_name=$data  and variable_list=44  and status_active=1 and is_deleted=0";
	$image_mandatory=return_field_value("image_mandatory", "variable_order_tracking", "company_name=$data  and variable_list=30  and status_active=1 and is_deleted=0");
	if($image_mandatory !="") return trim($image_mandatory); else return 2;
}
function signature_table1($report_id, $company, $width, $template_id="", $padding_top = 70,$prepared_by='') {
	if ($template_id != '') {
		$template_id = " and template_id=$template_id ";
	}
	$sql = sql_select("select designation,name,activities,prepared_by from variable_settings_signature where report_id=$report_id and company_id=$company   and status_active=1  $template_id order by sequence_no");


	if($sql[0][csf("prepared_by")]==1){
		list($prepared_by,$activities)=explode('**',$prepared_by);
		$sql_2[100] = array ( DESIGNATION => 'Prepared By' ,NAME => $prepared_by, ACTIVITIES =>$activities, PREPARED_BY => 0 );
		$sql=$sql_2+$sql;
	}

	$count = count($sql);
	$td_width = floor($width / $count);
	$standard_width = $count * 150;
	if ($standard_width > $width) {
		$td_width = 90;
	}
	$no_coloumn_per_tr = floor($width / $td_width);
	$i = 1;
	if ($count == 0) {$message = "<b>Note: This is Software Generated Copy , Signature is not Required.</b>";}
	$signature_data = '<table id="signatureTblId" width="' . $width . '" style="padding-top:' . $padding_top . 'px;"><tr><td width="100%" height="' . $padding_top . '" colspan="' . $count . '">' . $message . '</td></tr><tr>';
	foreach ($sql as $row) {
		$signature_data .= '<td width="' . $td_width . '" align="center" valign="top">
		<strong>' . $row[csf("activities")] . '</strong><br>
		<hr style="color:black; width:80%"> 
		<strong>' . $row[csf("designation")] . "</strong><br>" . $row[csf("name")] . '</td>';
		if ($i % $no_coloumn_per_tr == 0) {
			$signature_data .= '</tr><tr><td width="100%" height="70" colspan="' . $no_coloumn_per_tr . '"></td></tr>';
		}
		$i++;
	}
	$signature_data .= '</tr></table>';
	return $signature_data;
}
if($action=="populate_data_to_req_qty")
{
	$data=explode("___",$data);
	if($data[4]==1)
	{
		$color_name=explode('***', trim($data[3]));
		$col_id="";
		foreach($color_name as $vals)
		{
			$vals=trim($vals);
			if($col_id=="")
			{
				$col_sql="select id,color_name from lib_color where status_active=1 and is_deleted=0 and  color_name='$vals' and id in(select sample_color from sample_development_dtls where status_active=1 and is_deleted=0 and entry_form_id=203 and sample_mst_id='$data[2]')";
				$col_arr=sql_select($col_sql);
				$col_id=$col_arr[0][csf("id")];
			}
			else
			{
				$col_arr=sql_select("select id,color_name from lib_color where status_active=1 and is_deleted=0 and  color_name='$vals' and id in(select sample_color from sample_development_dtls where status_active=1 and is_deleted=0 and entry_form_id=203 and sample_mst_id='$data[2]')");
				$col_id.=','.$col_arr[0][csf("id")];
			}
		}
		$value=return_field_value("sum(sample_prod_qty)","sample_development_dtls","entry_form_id=203 and sample_name=$data[0] and gmts_item_id=$data[1] and sample_mst_id=$data[2] and status_active=1 and is_deleted=0 and sample_color in($col_id) ");
	}
	else
	{
		$value=return_field_value("sum(sample_prod_qty)","sample_development_dtls","entry_form_id=203 and sample_name=$data[0] and gmts_item_id=$data[1] and sample_mst_id=$data[2] and status_active=1 and is_deleted=0 ");
	}
	echo trim($value);
	exit();
}
if($action=="check_conversion_rate")
{
	$data=explode("**",$data);
	if($db_type==0)
	{
		$conversion_date=change_date_format($data[1], "Y-m-d", "-",1);
	}
	else
	{
		$conversion_date=change_date_format($data[1], "d-M-y", "-",1);
	}
	$currency_rate=set_conversion_rate( $data[0], $conversion_date );
	echo "1"."_".$currency_rate;
	exit();
}
if ($action=="load_drop_down_suplier")
{
	if($data==5 || $data==3)
	{
	 echo create_drop_down( "cbo_supplier_name", 130, "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", "id,company_name",1, "-- Select Supplier --", "", "",0,"" ); 
	}
	else
	{
	 echo create_drop_down( "cbo_supplier_name", 130, "select id,supplier_name from lib_supplier where status_active =1 and is_deleted=0 order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "",0 );
 	}
	exit();
}

if($action=="sample_wise_item_data")
{
	$data=explode("**",$data);
	$value=return_field_value("gmts_item_id","sample_development_dtls","entry_form_id=203 and sample_name=$data[1]  and sample_mst_id=$data[0] and status_active=1 and is_deleted=0  ");
	echo trim($value);
	exit();
}
if($action=="body_part_type")
{
	$bory_part_arr = sql_select("select body_part_type from lib_body_part where id=$data and status_active=1 and is_deleted=0");
	foreach ($bory_part_arr as $row) {
		$body_part_type = $row[csf('body_part_type')];
	}
	echo trim($body_part_type);
	exit();
}

if($action=="load_data_to_uom")
{
   	$value=return_field_value("trim_uom","lib_item_group","id=$data and status_active=1 and is_deleted=0");
	echo $value;
	exit();
}

if($action=="load_data_to_colorRF")
{
    $data=explode("_", $data);
    $color_library=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name" );

    $sql_color=sql_select("select distinct(sample_color) as sample_color from sample_development_dtls where entry_form_id=203 and sample_mst_id=$data[2] and sample_name=$data[0] and gmts_item_id=$data[1] and is_deleted=0  and status_active=1");
	//echo "select distinct(sample_color) as sample_color from sample_development_dtls where entry_form_id=203 and sample_mst_id=$data[2] and sample_name=$data[0] and gmts_item_id=$data[1] and is_deleted=0  and status_active=1";
	/*if(count($sql)==1)
	{
		echo "1_".$color_library[$sql[0][csf("sample_color")]]."_".$sql[0][csf("sample_color")]."_";
	}*/
	foreach($sql_color as $row)
	{
		$sample_color_arr[$row[csf("sample_color")]]=$color_library[$row[csf("sample_color")]];
	}
	if(count($sql_color)>0)
	{
		echo "1_".implode("***",$sample_color_arr)."_".$sql[0][csf("sample_color")]."_";
	}


	exit();
}

if ($action=="cbo_factory_merchant")
{
	echo create_drop_down( "cbo_factory_merchant", 130, "select id,team_member_name from lib_mkt_team_member_info where team_id='$data' and status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select Team Member --", $selected, "" );
	exit();
}

if($action=="company_wise_report_button_setting")
{
	extract($_REQUEST);
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."'  and module_id=2 and report_id=142 and is_deleted=0 and status_active=1");
	//echo $print_report_format.jahid;die;
	//$field_name, $table_name, $query_cond, $return_fld_name, $new_conn
	$print_report_format_arr=explode(",",$print_report_format);

	echo "$('#report').hide();\n";
	echo "$('#report1').hide();\n";
	echo "$('#report2').hide();\n";
	echo "$('#report3').hide();\n";
	echo "$('#report4').hide();\n";
	echo "$('#report6').hide();\n";
	echo "$('#report7').hide();\n";
	echo "$('#report8').hide();\n";
	if($print_report_format != "")
	{
		foreach($print_report_format_arr as $id)
		{
			if($id==109){echo "$('#report').show();\n";}
			if($id==110){echo "$('#report1').show();\n";}
			if($id==111){echo "$('#report2').show();\n";}
			if($id==45){echo "$('#report3').show();\n";}
			if($id==129){echo "$('#report4').show();\n";}
			if($id==161){echo "$('#report6').show();\n";}
			if($id==746){echo "$('#report7').show();\n";}
			if($id==220){echo "$('#report8').show();\n";}
		}
	}
	exit();
}

if($action=="auto_sd_color_generation")
{
	$data=explode("***",$data);
	$sql=sql_select("select sample_color from sample_development_dtls where entry_form_id=203 and sample_name=$data[0] and gmts_item_id=$data[1] and sample_mst_id=$data[2] and status_active=1 and is_deleted=0 ");
	$color="";
	$i=1;
	foreach($sql as $row)
	{
		$color .=$i.'_'.'BLUE'.'_'.$row[csf("sample_color")].'_'.''."-----";
		$i++;
	}
	echo chop($color,"-----");
	exit();
}

if($action=="check_data_in_fab_acc_for_sample_dtls")
{
	$data=explode("**",$data);
	$value1=return_field_value("count(id)","sample_development_fabric_acc","form_type=1 and sample_mst_id=$data[0] and status_active=1 and is_deleted=0");
	$SNdata1=return_field_value("wm_concat(sample_name_ra)","sample_development_fabric_acc","form_type=2 and sample_mst_id=$data[0] and status_active=1 and is_deleted=0 GROUP BY sample_mst_id");
	$GIdata1=return_field_value("wm_concat(gmts_item_id_ra)","sample_development_fabric_acc","form_type=2 and sample_mst_id=$data[0] and status_active=1 and is_deleted=0 GROUP BY sample_mst_id");
	$value2=return_field_value("count(id)","sample_development_fabric_acc","form_type=2 and sample_mst_id=$data[0] and status_active=1 and is_deleted=0");
	echo $value1."****".$value2."****".$SNdata1."****".$GIdata1;
	exit();
}

if($action=="check_data_in_fab_acc")
{
 	$value1=return_field_value("count(id)","sample_development_fabric_acc","form_type=1 and sample_mst_id=$data and status_active=1 and is_deleted=0");
	$value2=return_field_value("count(id)","sample_development_fabric_acc","form_type=2 and sample_mst_id=$data and status_active=1 and is_deleted=0");
	$value3=return_field_value("count(id)","sample_development_fabric_acc","form_type=3 and sample_mst_id=$data and status_active=1 and is_deleted=0");
	echo $value1."****".$value2."****".$value3;
	exit();
}

if($action=="load_drop_down_emb_type")
{
	$data=explode('_',$data);
	if($data[0]==1)
	{
		echo create_drop_down( "cboReType_".$data[1], 150,$emblishment_print_type,"", 1, "-- Select --", "", "","","" );
		die;
	}
	if($data[0]==2)
	{
		echo create_drop_down( "cboReType_".$data[1], 150,$emblishment_embroy_type,"", 1, "-- Select --", "", "","","" );
		die;
	}
	if($data[0]==3)
	{
		echo create_drop_down( "cboReType_".$data[1], 150,$emblishment_wash_type,"", 1, "-- Select --", "", "","","" );
		die;
	}
	if($data[0]==4)
	{
		echo create_drop_down( "cboReType_".$data[1], 150,$emblishment_spwork_type,"", 1, "-- Select --", "", "","","" );
		die;
	}
	if($data[0]==5)
	{
		echo create_drop_down( "cboReType_".$data[1], 150,$emblishment_gmts_type,"", 1, "-- Select --", "", "","","" );
		die;
	}
	exit();
}


if ($action=="load_drop_down_required_fabric_gmts_item")
{
	$data=explode("_", trim($data));
 	$sql=sql_select("select id,gmts_item_id from sample_development_dtls where is_deleted=0  and status_active=1 and entry_form_id=203 and sample_mst_id='$data[0]'");

	if($data[1]==1)
	{
			$gmts="";
			foreach ($sql as $row)
			 {
				 $gmts.=$row[csf("gmts_item_id")].",";
			 }
			 $gmts=chop($gmts,",");

			if(count($sql)>1)
		    {
		    	echo create_drop_down( "cboRfGarmentItem_1", 95, $garments_item,"", 0, "-- Select Item --", $selected, "",0,$gmts);
		    }
			    else
			    {
			    	echo create_drop_down( "cboRfGarmentItem_1", 95, $garments_item,"", 0, "-- Select Item --", $selected, "",0,$gmts);
			    }

    }
    	 else if($data[1]==2)
		{
			$gmts="";
			foreach ($sql as $row)
			 {
				 $gmts.=$row[csf("gmts_item_id")].",";
			 }
			 $gmts=chop($gmts,",");

			 if(count($sql)>1)
		   	 {
		    	echo create_drop_down( "cboRaGarmentItem_1", 100, $garments_item,"", 0, "-- Select Item --", $selected, "",0,$gmts);
		   	 }
					    else
					    {
					    	 echo create_drop_down( "cboRaGarmentItem_1", 100, $garments_item,"", 0, "-- Select Item --", $selected, "",0,$gmts);
					    }
	    }

			    else if($data[1]==3)
				{
					$gmts="";
					foreach ($sql as $row)
					 {
						 $gmts.=$row[csf("gmts_item_id")].",";
					 }
					 $gmts=chop($gmts,",");

					  if(count($sql)>1)
		    			{
		    	 			echo create_drop_down( "cboReGarmentItem_1", 95, $garments_item,"", 0, "-- Select Item --", $selected, "",0,$gmts);
		   				 }
						    else
						    {
						    	echo create_drop_down( "cboReGarmentItem_1", 95, $garments_item,"", 0, "-- Select Item --", $selected, "",0,$gmts);
						    }
			    }
	exit();
}

if ($action=="load_drop_down_required_fabric_sample_name")
{
	$data=explode("_", $data);
	$sql="select a.id,a.sample_name,b.id as dtls_id from  lib_sample a ,sample_development_dtls b where  a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.id=b.sample_name and b.entry_form_id=203 and b.sample_mst_id='$data[0]' group by a.id,a.sample_name,b.id order by b.id";
	$samp_array=array();
	$samp_result=sql_select($sql);
	if(count($samp_result)>0)
	{
		foreach($samp_result as $keys=>$vals)
		{
			$samp_array[$vals[csf("id")]]=$vals[csf("sample_name")];
		}

	}

	if($data[1]==1)
	{
		if(count($samp_result)>1)
		{
			echo create_drop_down( "cboRfSampleName_1", 95, $samp_array,"", 1, "select Sample", $selected,"sample_wise_item($data[0],this.value,1,1);");
		}
		else
		{
			echo create_drop_down( "cboRfSampleName_1", 95, $samp_array,"", 0, "select Sample", $selected,"");
		}
	}

	else if($data[1]==2)
	{

		if(count($samp_result)>1)
		{
			echo create_drop_down( "cboRaSampleName_1", 100, $samp_array,"", 1, "select Sample", $selected,"sample_wise_item($data[0],this.value,1,2);");
		}
		else
		{
			echo create_drop_down( "cboRaSampleName_1", 100, $samp_array,"", 0, "select Sample", $selected,"");
		}
	}
	else if($data[1]==3)
	{
		if(count($samp_result)>1)
		{
			echo create_drop_down( "cboReSampleName_1", 140, $samp_array,"", 1, "select Sample", $selected,"sample_wise_item($data[0],this.value,1,3);");
		}
		else
		{
			echo create_drop_down( "cboReSampleName_1", 140, $samp_array,"", 0, "select Sample", $selected,"");
		}
	}
	exit();
}


if($action=="fabric_description_popup")
{
	echo load_html_head_contents("Fabric Description Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>

	<script>
		function js_set_value(data)
		{
			var data=data.split('_');
			var fabric_yarn_description=return_global_ajax_value(data[0], 'fabric_yarn_description', '', 'sample_requisition_with_booking_controller');
			var fabric_yarn_description_arr=fabric_yarn_description.split("**");
			//var fabric_description=trim(data[2])+' '+trim(fabric_yarn_description_arr[0]);
			var fabric_description=trim(data[2])+' '+trim(data[5]);
			document.getElementById('fab_des_id').value=data[0];
			document.getElementById('fab_nature_id').value=data[1];
			document.getElementById('construction').value=trim(data[2]);
			document.getElementById('fab_gsm').value=trim(data[3]);
			document.getElementById('process_loss').value=trim(data[4]);
			document.getElementById('fab_desctiption').value=trim(fabric_description);
			document.getElementById('composition').value=trim(fabric_yarn_description_arr[0]);
			document.getElementById('yarn_desctiption').value=trim(fabric_yarn_description_arr[1]);
			parent.emailwindow.hide();
		}
		/*function toggle( x, origColor )
		{
			var newColor = 'yellow';
			document.getElementById(x).style.backgroundColor = ( newColor == document.getElementById(x).style.backgroundColor )? origColor : newColor;
		}*/
			</script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Construction</th>
                    <th>GSM/Weight</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th>
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                            <input type="text" style="width:130px" class="text_boxes" name="txt_construction" id="txt_construction" />
                        </td>
                        <td align="center">
                            <input type="text" style="width:130px" class="text_boxes" name="txt_gsm_weight" id="txt_gsm_weight" />
                        </td>
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $fabric_nature; ?>'+'**'+'<? echo $libyarncountdeterminationid; ?>'+'**'+document.getElementById('txt_construction').value+'**'+document.getElementById('txt_gsm_weight').value, 'fabric_description_popup_search_list_view', 'search_div', 'sample_requisition_with_booking_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:100px;" />
                        	<!-- toggle( 'tr_'+'<? echo $libyarncountdeterminationid; ?>', '#FFFFCC'); -->
                    </td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:10px" id="search_div"></div>
		</fieldset>

	</form>
    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if($action=="fabric_description_popup_search_list_view")
{
	extract($_REQUEST);
	list($fabric_nature,$libyarncountdeterminationid,$construction,$gsm_weight)=explode('**',$data);
	$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count"  );

	if($construction!=''){$search_con = " and a.construction like('%".trim($construction)."%')";}
	if($gsm_weight!=''){$search_con  .= " and a.gsm_weight like('%".trim($gsm_weight)."%')";}
	?>
	<script>

	</script>
	</head>
	<body>

		<div align="center">
			<form>
				<input type="hidden" id="fab_des_id" name="fab_des_id" />
				<input type="hidden" id="fab_nature_id" name="fab_des_id" />
				<input type="hidden" id="construction" name="construction" />
				<input type="hidden" id="composition" name="composition" />
				<input type="hidden" id="fab_gsm" name="fab_gsm" />
				<input type="hidden" id="process_loss" name="process_loss" />
				<input type="hidden" id="fab_desctiption" name="fab_desctiption" />
				<input type="hidden" id="yarn_desctiption" name="yarn_desctiption" />
			</form>
			<?
			$composition_arr=array();
			$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
			$arr=array (0=>$item_category, 3=>$color_range,6=>$composition,8=>$lib_yarn_count,9=>$yarn_type);
			$sql="SELECT a.fab_nature_id,a.construction,a.gsm_weight,a.color_range_id,a.stich_length,a.process_loss,b.copmposition_id,b.percent,b.count_id,b.type_id,a.id, c.fabric_composition_name from  lib_yarn_count_determina_mst a join lib_yarn_count_determina_dtls b on a.id=b.mst_id left join lib_fabric_composition c  on c.id = a.fabric_composition_id and c.status_active=1 and c.is_deleted=0 where a.status_active=1 and a.is_deleted=0  and  b.status_active=1 and b.is_deleted=0 order by b.id";
			$data_array=sql_select($sql);
			if (count($data_array)>0)
			{
				foreach( $data_array as $row )
				{
					if(array_key_exists($row[csf('id')],$composition_arr))
					{
						$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
					}
					else
					{
						$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
					}
				}
			}
			?>
			<table class="rpt_table" width="1000" cellspacing="0" cellpadding="0" border="0" rules="all">
				<thead>
					<tr>
						<th width="50">SL No</th>
						<th width="100">Fab Nature</th>
						<th width="100">Construction</th>
						<th width="100">GSM/Weight</th>
						<th width="100">Color Range</th>
						<th width="90">Stich Length</th>
						<th width="50">Process Loss</th>
						<th width="300">Composition</th>
						<th>Fabric Composition</th>
					</tr>
				</thead>
			</table>
			<div id="" style="max-height:350px; width:1000px; overflow-y:scroll">
				<table id="list_view" class="rpt_table" width="1000" height="" cellspacing="0" cellpadding="0" border="1" rules="all">
					<tbody>
						<?

						$sql_data=sql_select("SELECT a.fab_nature_id,a.construction,a.gsm_weight,a.color_range_id,a.stich_length,a.process_loss,a.id, c.fabric_composition_name from  lib_yarn_count_determina_mst a join lib_yarn_count_determina_dtls b on a.id=b.mst_id left join lib_fabric_composition c  on c.id = a.fabric_composition_id and c.status_active=1 and c.is_deleted=0 where a.fab_nature_id= '$fabric_nature' and  a.status_active=1 and a.is_deleted=0  and  b.status_active=1 and b.is_deleted=0 $search_con group by a.id,a.fab_nature_id,a.construction,a.gsm_weight,a.color_range_id,a.stich_length,a.process_loss,c.fabric_composition_name order by a.id");
						$i=1;
						foreach($sql_data as $row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							if($libyarncountdeterminationid==$row[csf('id')]) $bgcolor="yellow";
							$string_value=$composition_arr[$row[csf('id')]];
							?>
							<tr id="tr_<? echo $row[csf('id')]; ?>" bgcolor="<? echo $bgcolor; ?>" height="20" style="cursor:pointer; word-break:break-all;" onClick="js_set_value('<? echo $row[csf('id')]."_".$row[csf('fab_nature_id')]."_".$row[csf('construction')]."_".$row[csf('gsm_weight')]."_".$row[csf('process_loss')]."_".$string_value ?>')">
								<td width="50"><? echo $i; ?></td>
								<td width="100" align="left"><? echo $item_category[$row[csf('fab_nature_id')]]; ?></td>
								<td width="100" align="left"><? echo $row[csf('construction')]; ?></td>
								<td width="100" align="right"><? echo $row[csf('gsm_weight')]; ?></td>
								<td width="100" align="left"><? echo $color_range[$row[csf('color_range_id')]]; ?></td>
								<td width="90" align="right"><? echo $row[csf('stich_length')]; ?></td>
								<td width="50" align="right"><? echo $row[csf('process_loss')]; ?></td>
								<td width="300"><? echo $composition_arr[$row[csf('id')]]; ?></td>
								<td><? echo $row[csf('fabric_composition_name')]; ?></td>
							</tr>

							<?
							$i++;
						}
						?>
					</tbody>
				</table>
				<script>

				</script>
			</div>
		</div>
	</body>
	</html>
	<?
}

if($action =="fabric_yarn_description")
{
	$fab_description="";
	$yarn_description="";
	$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
	$sql="select a.fab_nature_id,a.construction,a.gsm_weight,a.color_range_id,a.stich_length,a.process_loss,b.copmposition_id,b.percent,b.count_id,b.type_id,a.id from  lib_yarn_count_determina_mst a,  lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id=$data and  a.status_active=1 and a.is_deleted=0  and  b.status_active=1 and b.is_deleted=0 order by a.id";
	$data_array=sql_select($sql);
	if (count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			if($fab_description!="")
			{
				$fab_description=$fab_description." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
			else
			{
				$fab_description=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}

			if($yarn_description!="")
			{
				$yarn_description=$yarn_description."__".$row[csf('count_id')]."_".$row[csf('copmposition_id')]."_100_".$row[csf('type_id')]."_".$row[csf('percent')];

			}
			else
			{
				$yarn_description=$row[csf('count_id')]."_".$row[csf('copmposition_id')]."_100_".$row[csf('type_id')]."_".$row[csf('percent')];

			}
		}
	}
	echo $fab_description."**".$yarn_description;

}
if($action=="process_loss_method_id")
{
	$data=explode("_",$data);
	$process_loss_method=return_field_value("process_loss_method", "variable_order_tracking", "company_name=$data[0]  and variable_list=18 and item_category_id=$data[1] and status_active=1 and is_deleted=0");
	echo $process_loss_method;
 }
if ($action=="color_popup_rf")
{
	echo load_html_head_contents("Sample Requisition Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$process_loss_method=return_field_value("process_loss_method", "variable_order_tracking", "company_name=$company  and variable_list=18 and item_category_id=2 and status_active=1 and is_deleted=0");
		?>

    <script>
		var permission='<? echo $permission; ?>';

		function add_break_down_tr( i )
		{
			var row_num=$('#col_tbl tbody tr').length;

			if (row_num!=i)
			{
				return false;
			}
			else
			{
				i++;
				$("#col_tbl tbody tr:last").clone().find("input,select").each(function(){
				$(this).attr({
              'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
              'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i },
              'value': function(_, value) { return value }
            });

				}).end().appendTo("#col_tbl");

				$("#col_tbl tbody tr:last").removeAttr('id').attr('id','row_'+i);
				$("#txtSL_"+i).val(i);
				$('#txtContrast_'+i).removeAttr("ondblclick").attr("ondblclick","copy_gmts_color_to_fab("+i+");");
				$('#increase_'+i).removeAttr("value").attr("value","+");
				$('#decrease_'+i).removeAttr("value").attr("value","-");
				$('#increase_'+i).removeAttr("onclick").attr("onclick","add_break_down_tr("+i+");");

				$('#decrease_'+i).removeAttr("onclick").attr("onclick","fn_deleteRow("+i+");");
 			}
		}

		function fn_deleteRows(rowNo)
		{
			var numRow=$('#col_tbl tbody tr').length;
			if(numRow==rowNo && rowNo!=1)
			{
				$('#col_tbl tbody tr:last').remove();
			}
			else
			{
			 //code
			}
		}


  function fn_deleteRow(rowNo)
    {

              var k=rowNo-1;

              $("table#col_tbl tbody tr:eq("+k+")").remove();
               var numRow = $('#col_tbl tbody tr').length;

				for(i = rowNo;i <= numRow;i++)
                {
                	//$('#txtSL_'+(i-1)).val(i);
                	$("#col_tbl tr:eq("+i+")").find("input,select").each(function() {
                		$('#txtSL_'+(i-1)).val(i);
					$(this).attr({
						'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
						'value': function(_, value) { return value }
					});
					$("#col_tbl tr:eq("+i+")").removeAttr('id').attr('id','row_'+i);
					$('#increase_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
					$('#decrease_'+i).removeAttr("onClick").attr("onClick","fn_deleteRow("+i+");");
					$('#txtContrast_'+i).removeAttr("ondblclick").attr("ondblclick","copy_gmts_color_to_fab("+i+");");


					});




                }
                for(i=1;i<=numRow;i++)
                {
                 	$('#txtSL_'+(i)).val(i);
                }



    }


		function fnc_close( )
		{
			var rowCount = $('#col_tbl tbody tr').length;
			//alert( rowCount );return;
			var breck_down_data="";
			var display_col="";
			var total_qnty=0;
			var total_loss=0;
			var total_grey=0;
			for(var i=1; i<=rowCount; i++)
			{
				if(breck_down_data=="")
				{
					breck_down_data+=($('#txtSL_'+i).val()*1)+'_'+$('#txtColor_'+i).val()+'_'+($('#hiddenColorId_'+i).val())*1+'_'+$('#txtContrast_'+i).val()+'_'+$('#txtQnty_'+i).val()*1+'_'+$('#txtProcessLoss_'+i).val()*1+'_'+$('#txtGreyQnty_'+i).val()*1;
					  display_col +=$('#txtColor_'+i).val() ;
				}
				else
				{
					breck_down_data+="-----"+($('#txtSL_'+i).val()*1)+'_'+$('#txtColor_'+i).val()+'_'+($('#hiddenColorId_'+i).val())*1+'_'+$('#txtContrast_'+i).val()+'_'+$('#txtQnty_'+i).val()*1+'_'+$('#txtProcessLoss_'+i).val()*1+'_'+$('#txtGreyQnty_'+i).val()*1;;
					  display_col +='***'+$('#txtColor_'+i).val() ;
				}
				total_qnty+=$('#txtQnty_'+i).val()*1;
				total_loss+=$('#txtProcessLoss_'+i).val()*1;
				total_grey+=$('#txtGreyQnty_'+i).val()*1;
			}
			var loss_per= ((total_grey-total_qnty)/total_qnty)*100;
 			document.getElementById('txtRfColorAllData').value=breck_down_data;
 			document.getElementById('displayAllcol').value=display_col;
 			document.getElementById('total_qnty_kg').value=total_qnty;
 			document.getElementById('total_loss').value=loss_per;
 			document.getElementById('total_grey').value=total_grey;
			parent.emailwindow.hide();
		}
		function copy_gmts_color_to_fab(id)
		{
			var gmts_color=$("#txtColor_"+id).val();
			$("#txtContrast_"+id).val(gmts_color);

		}

	function calculate_requirement(i)
     {
      	var cbo_company_name= '<? echo $company;?>';

     	var cbo_fabric_natu= 2;
      	//var process_loss_method_id=return_global_ajax_value(cbo_company_name+'_'+cbo_fabric_natu, 'process_loss_method_id', '', 'sample_requisition_with_booking_controller');
      	//alert(process_loss_method_id);
     	var txt_finish_qnty=(document.getElementById('txtQnty_'+i).value)*1;
     	var process_loss_method_id=(document.getElementById('process_loss_method').value)*1;
     	var processloss=(document.getElementById('txtProcessLoss_'+i).value)*1;
     	var WastageQty='';

     	if(process_loss_method_id==1)
     	{
     		WastageQty=txt_finish_qnty+txt_finish_qnty*(processloss/100);
     	}
     	else if(process_loss_method_id==2)
     	{
     		var devided_val = 1-(processloss/100);
     		var WastageQty=parseFloat(txt_finish_qnty/devided_val);
     	}
     	else
     	{
     		WastageQty=0;
     	}
     	WastageQty= number_format_common( WastageQty, 5, 0) ;
     	document.getElementById('txtGreyQnty_'+i).value= WastageQty;
     	//document.getElementById('txtAmount_'+i).value=number_format_common((document.getElementById('txtRate_'+i).value)*1*WastageQty,5,0);
     }

     function copy_all_field(id,value,type)
     {
      	var check_val=$('#checkboxId').is(':checked');
     	if(check_val==true)
     	{
     		var rows = $('#col_tbl tbody tr').length*1;
     		var id=id.split("_");
     		var position=id[1]*1;
     		var i;
     		for(i=position+1;i<=rows;i++)
     		{
     			if(type=='1')
     			{
     				$("#txtQnty_"+i).val(value);
     				calculate_requirement(i);
     			}
     			else if(type==2)
     			{
     				$("#txtProcessLoss_"+i).val(value);
     				calculate_requirement(i);
     			}
     			else if(type==3)
     			{
     				$("#txtContrast_"+i).val(value);
     			}
     		}

     	}



     }


    </script>

    <body>
		<div align="center" style="width:100%;" >
        <? echo load_freeze_divs ("../../../",$permission,1); ?>
        <form name="size_1" id="size_1">
			<fieldset style="width:520px;">
            <table align="center" cellspacing="0" width="520" class="rpt_table" border="1" rules="all" id="col_tbl" >
            	<thead>
            	<tr>
            		<td colspan="7" align="center">Copy<input type="checkbox" name="checkboxId" id="checkboxId" value="1"></td>
            	</tr>
            		<tr>
            			<th width="30" >SL</th>
            			<th width="70" >Gmts Color</th>
            			<th width="100" >Fab. Col/Contrast</th>
            			<th width="40" >Fin Qnty</th>
            			<th width="50" >Process Loss%</th>
            			<th width="50" >Grey Qnty</th>
            			<th width="70" >
            				<input type="hidden" id="mainupid" value="<? echo $mainId; ?>"/>
							<Input type="hidden" id="dtlsupid" value="<? echo $dtlId; ?>"/>
							<Input type="hidden" id="process_loss_method" value="<? echo $process_loss_method; ?>"/>

            			</th>
            		</tr>


            	</thead>
                <tbody>

                	<?
					$color_arr=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name" );
				 $sql_col="select id,sample_color from sample_development_dtls where entry_form_id=203 and sample_mst_id=$mainId and sample_name=$sampleName and gmts_item_id=$garmentItem and is_deleted=0  and status_active=1 order by id ASC";
					$sql_result =sql_select($sql_col);
					foreach ($sql_result as $row)
					{
						$sample_new_color_arr[$row[csf('sample_color')]]=$row[csf('sample_color')];
					}
					//print_r($sample_new_color_arr);
				$sql_rf_col="select c.id,c.color_id,c.qnty,c.contrast,c.color_id,c.fabric_color,c.grey_fab_qnty,c.process_loss_percent from sample_development_fabric_acc b,sample_development_rf_color c where  b.id=c.dtls_id and b.sample_mst_id=$mainId and b.sample_name=$sampleName and b.gmts_item_id=$garmentItem and c.dtls_id=$dtlId and b.is_deleted=0  and b.status_active=1 and c.is_deleted=0 and c.grey_fab_qnty>0  and c.status_active=1 and b.form_type=1 order by c.id ASC";
				$sql_color_result =sql_select($sql_rf_col);
				if($sql_color_result<=0)
				{
					$data=$data;
					$type=2;
				}
				else
				{
					$data=$sql_color_result;//From Rf Color table
					$type=1;
				}
				//echo $type.'dd'.$data;
                	if($data)
                	{
						if($type==2)
						{
                		$data_all=explode('-----',$data);
                		$count_tr=count($data_all);
						}
						else
						{
							$count_tr=count($sql_color_result);
							$data_all=$data;
						}
                		if($count_tr>0)
                		{
                			$i=1;
                			foreach ($data_all as $size_data)
                			{
							/*$txtSL=0;
							$txtColor='';
							$hiddenColorId=0;
							$txtContrast=''; */
							if($type==2)
							{
							$ex_size_data=explode('_',$size_data);
							$txtSL=$ex_size_data[0];
							$txtColor=$ex_size_data[1];
							$hiddenColorId=$ex_size_data[2];
							$txtContrast=$ex_size_data[3];
							$txtQnty=$ex_size_data[4];
							$txtProcessLoss=$ex_size_data[5];
							$txtGreyQnty=$ex_size_data[6];
							}
							else
							{
							//$ex_size_data=explode('_',$size_data);
							$txtSL=$ex_size_data[0];
							$txtColor=$color_arr[$size_data[csf('color_id')]];
							$hiddenColorId=$size_data[csf('color_id')];
							$txtContrast=$size_data[csf('contrast')];
							$txtQnty=$size_data[csf('qnty')];
							$txtProcessLoss=$size_data[csf('process_loss_percent')];
							$txtGreyQnty=$size_data[csf('grey_fab_qnty')];
							}
							 $current_ColorId.=$hiddenColorId.',';
							?>
							<tr id="row_<? echo $i; ?>">

								<td><input name="txtSL_<? echo $i; ?>" class="text_boxes" ID="txtSL_<? echo $i; ?>" value="<? echo $i; ?>" style="width:30px" /><input type="hidden" name="colorupid_<? echo $i; ?>" class="text_boxes" ID="colorupid_<? echo $i; ?>" value="" style="width:30px"></td>

								<td>
									<input  class="text_boxes" type="text" name="txtColor_<? echo $i; ?>"  ID="txtColor_<? echo $i; ?>" style="width:70px"   value="<? echo $txtColor; ?>"  title="<? echo $txtColor; ?>" disabled  />
									<input type="hidden"  name="hiddenColorId_<? echo $i; ?>" id="hiddenColorId_<? echo $i; ?>" value="<? echo $hiddenColorId ?>" title="<? echo $hiddenColorId ?>">

								</td>

								<td><input name="txtContrast_<? echo $i; ?>" class="text_boxes" ID="txtContrast_<? echo $i; ?>" style="width:100px" onChange="copy_all_field(this.id,this.value,'3');"
									value="<? echo $txtContrast;?>"  ondblclick="copy_gmts_color_to_fab(<? echo $i; ?>);" /></td>


									<td><input name="txtQnty_<? echo $i; ?>" class="text_boxes" ID="txtQnty_<? echo $i; ?>" style="width:40px" onBlur="calculate_requirement(<? echo $i; ?>);"  onchange="copy_all_field(this.id,this.value,'1');"  value="<? echo $txtQnty;?>"   /></td>

									<td><input name="txtProcessLoss_<? echo $i; ?>" class="text_boxes" ID="txtProcessLoss_<? echo $i; ?>" style="width:50px" onChange="copy_all_field(this.id,this.value,'2');" onBlur="calculate_requirement(<? echo $i; ?>);"  value="<? echo $txtProcessLoss;?>"   /></td>

									<td><input readonly name="txtGreyQnty_<? echo $i; ?>" class="text_boxes" ID="txtGreyQnty_<? echo $i; ?>" style="width:50px"  value="<? echo $txtGreyQnty;?>"   /></td>

									<td align="center">
										<input type="hidden" id="increase_<? echo $i; ?>" name="increase_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr( <? echo $i; ?> )" />
										<input type="hidden" id="decrease_<? echo $i; ?>" name="decrease_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
									</td>
								</tr>
								<?
								$i++;
							}
							$current_ColorId=rtrim($current_ColorId,',');
							$current_ColorIds=array_unique(explode(",",$current_ColorId));
							foreach($sample_new_color_arr as $color_id)//For New Color add From Sample
							{
								if(!in_array($color_id,$current_ColorIds))
								{
									?>
                                    <tr id="row_<? echo $i; ?>">

								<td><input name="txtSL_<? echo $i; ?>" class="text_boxes" ID="txtSL_<? echo $i; ?>" value="<? echo $i; ?>" style="width:30px" /><input type="hidden" name="colorupid_<? echo $i; ?>" class="text_boxes" ID="colorupid_<? echo $i; ?>" value="" style="width:30px"></td>

								<td>
									<input name="txtColor_<? echo $i; ?>" class="text_boxes" ID="txtColor_<? echo $i; ?>" style="width:70px" value="<? echo $color_arr[$color_id]; ?>" title="<? echo $color_arr[$color_id]; ?>" disabled  />
									<input type="hidden" name="hiddenColorId_<? echo $i; ?>" id="hiddenColorId_<? echo $i; ?>" value="<? echo $color_id ?>" title="<? echo $color_id ?>">

								</td>

								<td><input name="txtContrast_<? echo $i; ?>" class="text_boxes" ID="txtContrast_<? echo $i; ?>" style="width:100px" onChange="copy_all_field(this.id,this.value,'3');"
									value="<? //echo $txtContrast;?>"  ondblclick="copy_gmts_color_to_fab(<? echo $i; ?>);" /></td>


									<td><input name="txtQnty_<? echo $i; ?>" class="text_boxes" ID="txtQnty_<? echo $i; ?>" style="width:40px" onBlur="calculate_requirement(<? echo $i; ?>);"  onchange="copy_all_field(this.id,this.value,'1');"  value="<? //echo $txtQnty;?>"   /></td>

									<td><input name="txtProcessLoss_<? echo $i; ?>" class="text_boxes" ID="txtProcessLoss_<? echo $i; ?>" style="width:50px" onChange="copy_all_field(this.id,this.value,'2');" onBlur="calculate_requirement(<? echo $i; ?>);"  value="<? //echo $txtProcessLoss;?>"   /></td>

									<td><input readonly name="txtGreyQnty_<? echo $i; ?>" class="text_boxes" ID="txtGreyQnty_<? echo $i; ?>" style="width:50px"  value="<? //echo $txtGreyQnty;?>"   /></td>

									<td align="center">
										<input type="hidden" id="increase_<? echo $i; ?>" name="increase_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr( <? echo $i; ?> )" />
										<input type="hidden" id="decrease_<? echo $i; ?>" name="decrease_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
									</td>
								</tr>
								<?
								$i++;

								}
							}
						}
					}
					else
					{
						$color_library=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name" );
						$sql_col="select id,sample_color from sample_development_dtls where entry_form_id=203 and sample_mst_id=$mainId and sample_name=$sampleName and gmts_item_id=$garmentItem and is_deleted=0  and status_active=1 order by id ASC";
						$sql_result =sql_select($sql_col);
						$i=1;
						foreach($sql_result as $row)
						{
							//

							?>

							<tr id="row_<? echo $i; ?>">
								<td width="30" align="center" ><input name="txtSL_<? echo $i; ?>" class="text_boxes" ID="txtSL_<? echo $i; ?>" value="<? echo $i; ?>" style="width:30px" /><input type="hidden" name="colorupid_<? echo $i; ?>" class="text_boxes" ID="colorupid_<? echo $i; ?>" value="" style="width:30px"></td>

								<td width="70" align="center" ><input name="txtColor_<? echo $i; ?>" class="text_boxes" ID="txtColor_<? echo $i; ?>" value="<? echo $color_library[$row[csf('sample_color')]];  ?>" style="width:70px" disabled />
									<input type="hidden" name="hiddenColorId_<? echo $i; ?>" id="hiddenColorId_<? echo $i; ?>" value="<? echo $row[csf('sample_color')];  ?>">

								</td>

								<td width="100" align="center" ><Input name="txtContrast_<? echo $i; ?>" class="text_boxes" ID="txtContrast_<? echo $i; ?>" style="width:70px" onChange="copy_all_field(this.id,this.value,'3');" value="" onDblClick="copy_gmts_color_to_fab(<? echo $i; ?>);"/></td>


								<td width="40" align="center" ><Input name="txtQnty_<? echo $i; ?>" class="text_boxes" ID="txtQnty_<? echo $i; ?>" onBlur="calculate_requirement(<? echo $i; ?>);" onChange="copy_all_field(this.id,this.value,'1');" style="width:70px" value="" /></td>
								<td width="50" align="center" ><Input name="txtProcessLoss_<? echo $i; ?>" class="text_boxes" ID="txtProcessLoss_<? echo $i; ?>" style="width:70px" onChange="copy_all_field(this.id,this.value,'2');" value="" onBlur="calculate_requirement(<? echo $i; ?>);" /></td>
								<td width="50" align="center" ><Input name="txtGreyQnty_<? echo $i; ?>" readonly class="text_boxes" ID="txtGreyQnty_<? echo $i; ?>" style="width:70px" value="" /></td>

								<td align="center">
									<input type="hidden" id="increase_<? echo $i; ?>" name="increase_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr( <? echo $i; ?> )" />
									<input type="hidden" id="decrease_<? echo $i; ?>" name="decrease_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
								</td>
							</tr>
							<?
							$i++;
						}
					}
					?>
				</tbody>
            </table>
            <table align="center" cellspacing="0" width="520" class="rpt_table" border="1" rules="all" id="" >

                <tr>
                    <td colspan="7" align="center">
                        <input type="hidden" name="txtRfColorAllData" id="txtRfColorAllData" class="text_boxes"  value="" >
                        <input type="hidden" name="displayAllcol" id="displayAllcol">
                        <input type="hidden" name="total_qnty_kg" id="total_qnty_kg">
                        <input type="hidden" name="total_loss" id="total_loss">
                        <input type="hidden" name="total_grey" id="total_grey">
                     </td>
                </tr>
                <tr>
                    <td align="center" colspan="7" align="center" class="button_container">
                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
                    </td>
                </tr>
            </table>
            </fieldset>
            </form>
        </div>
    </body>
     <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if($action=="sample_requisition_print") 
{	
	extract($_REQUEST);
	$data=explode('*',$data);
	$cbo_template_id=$data[3];
	$path="../../";
	if(count($data)>3)
	{
		if($data[4]=='../')
		{
			$path=$data[4];
		}
	}
	if($data[2]==0)  $path='../';
	 // echo $data[2].'DTTTTTTTTTTTTTTTT';
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$supplier_library=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );

	$company_img=sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");


	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$dealing_merchant_library=return_library_array( "select id,team_member_name from lib_mkt_team_member_info", "id", "team_member_name"  );
	$team_leader_arr=return_library_array( "select id,team_leader_name from lib_marketing_team", "id", "team_leader_name"  );

	$sample_library=return_library_array( "select id, sample_name from lib_sample", "id", "sample_name");
	$size_library=return_library_array( "select id, size_name from lib_size", "id", "size_name"  );
	$color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
	$season_arr=return_library_array( "select id,season_name from lib_buyer_season  where status_active =1 and is_deleted=0 ", "id", "season_name"  );
	$trims_group_lib=return_library_array( "select id,item_name from lib_item_group  where status_active =1 and is_deleted=0", "id", "item_name");
	//concate(buyer_name,'_',contact_person)
	$appDate=return_field_value("approved_date","approval_history","entry_form=25 and mst_id='$data[1]' order by id desc");
	$appBy=return_field_value("approved_by","approval_history","entry_form=25 and mst_id='$data[1]'");
	$user_arr=return_library_array( "select id, user_name from user_passwd", "id", "user_name"  );
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='sample_details_1' and file_type=1",'master_tble_id','image_location');
	//$lip_yarn_count=return_library_array( "select id,fabric_composition_id from lib_yarn_count_determina_mst where  status_active=1", "id", "fabric_composition_id");
	$fabric_composition_arr=return_library_array( "select id,fabric_composition_name from lib_fabric_composition where  status_active=1", "id", "fabric_composition_name");

	$sql_fab=sql_select("select id,fabric_composition_id,construction from lib_yarn_count_determina_mst where  status_active=1");
	foreach($sql_fab as $row)
	{
		$lip_yarn_count[$row[csf("id")]]=$row[csf("fabric_composition_id")];
		$fab_constructArr[$row[csf("id")]]=$row[csf("construction")];
	}


 ?>
 	<style>
		#mstDiv {
			margin:0px auto;
			width:1130px;

		}
		#mstDiv @media print {

		thead {display: table-header-group;}

		}

		 @media print{
			html>body table.rpt_table {
			margin-left:12px;
	  		}

		 }
	</style>

		<div id="mstDiv">

	     <table width="1100" cellspacing="0" border="0"  style="font-family: Arial Narrow;margin-left: 20px;" >
	     <tr>
	     	<td rowspan="4" valign="top" width="300"><img width="150" height="80" src="<? echo base_url($company_img[0][csf("image_location")]); ?>" ></td>
	     	<td colspan="4" style="font-size: 24px;"><strong><b><? echo $company_library[$data[0]]; ?></b></strong></td>
	            <td width="200">
	            <?

	             $nameArray_approved=sql_select( "SELECT approved_by,approved_date from wo_non_ord_samp_booking_mst a, approval_history b where a.id=b.mst_id and a.booking_no='$data[2]' and b.entry_form=9 and a.status_active =1 and a.is_deleted=0 order by b.id desc ");
	             $approved_by= $user_arr[$nameArray_approved[0][csf("approved_by")]];
	             $approved_date= change_date_format($nameArray_approved[0][csf("approved_date")]);
	              ?>
	             </td>
	     </tr>




	        <tr>
	            <td colspan="5">
					<?

	                    $val=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
						//echo ($val[0][csf('plot_no')])?   $val[0][csf('plot_no')].',': "";
						echo ($val[0][csf('level_no')])?  $val[0][csf('level_no')].',': "";
						echo ($val[0][csf('road_no')])?   $val[0][csf('road_no')].',': "";
						echo ($val[0][csf('block_no')])?  $val[0][csf('block_no')].',': "";
						echo ($val[0][csf('city')])?      $val[0][csf('city')].',': "";
						echo ($val[0][csf('zip_code')])?  $val[0][csf('zip_code')].',': "";
						echo ($val[0][csf('province')])?  $val[0][csf('province')].',': "";
						echo($val[0][csf('country_id')])? $country_arr[$val[0][csf('country_id')]]: "";
						echo ($val[0][csf('email')])?    "</br>". $val[0][csf('email')].',': "</br>";
						echo($val[0][csf('website')])?    $val[0][csf('website')]: "";
						 $sql="SELECT id, requisition_number, requisition_number_prefix_num, style_ref_no, buyer_name, season, product_dept, dealing_marchant, agent_name, buyer_ref, bh_merchant, estimated_shipdate, team_leader, season_buyer_wise, remarks, quotation_id, sample_stage_id, requisition_date, material_delivery_date,control_no,internal_ref from sample_development_mst where  id='$data[1]' and entry_form_id=203 and  is_deleted=0  and status_active=1";
						  
	 					  $dataArray=sql_select($sql);
	 					  $barcode_no=$dataArray[0][csf('requisition_number')];
	 					  if($dataArray[0][csf("sample_stage_id")]==1)
	 					  {
	 					  	 $job_lib=return_library_array( "SELECT a.id,min(b.shipment_date) as shipment_date  from wo_po_details_master  a,wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and a.company_name='$data[0]' GROUP BY a.id", "id", "shipment_date"  );
	 					  }

	 					   $sqls="SELECT style_desc,supplier_id,revised_no,buyer_req_no,source,team_leader,dealing_marchant,pay_mode,booking_date  from wo_non_ord_samp_booking_mst where  booking_no='$data[2]'  and  is_deleted=0  and status_active=1";
						   //echo $sqls;
	 					  $dataArray_book=sql_select($sqls);
						// $style_desc= $dataArray_book[0][csf('style_desc')];


	                ?>
	            </td>

	        </tr>
	        <tr>
	            <td colspan="3" style="font-size:medium"><strong> <b>Sample Program Without Order</b></strong></td>
	             <td colspan="2" id="" width="250"><b>Approved By :<? echo $approved_by ?></b> </br><b>Approved Date :<? echo $approved_date ?></b> </td>

	        </tr>


	        </table>

	        <table width="1100" cellspacing="0" border="0" class="rpt_table" style="font-family: Arial Narrow;margin-left: 20px;" >
	        	<tr>
	        		<td colspan="4" align="left"><strong>System No. &nbsp;<? echo $dataArray[0][csf("requisition_number")]; ?> </strong></td>
	        		<td ><strong>Revise:</strong></td>
	        		<td ><? echo $dataArray_book[0][csf('revised_no')];?></td>
	        		<td colspan="2"></td>
	        	</tr>

	        	<tr>
	        		<td width="100"><strong>Booking No: </strong></td>
	        		<td width="130" align="left"><? echo $data[2];?></td>
	        		<td width="120"  align="left">&nbsp;&nbsp;<strong>Style Ref:</strong></td>
	        		<td width="110">&nbsp;<? echo $dataArray[0][csf('style_ref_no')];?></td>
	        		<td width="110"   align="left"><strong>Sample Sub Date:</strong></td>
	        		<td width="100" ><? echo change_date_format($dataArray[0][csf('material_delivery_date')]);?></td>
	        		<td width="110"   align="left"><strong>Style Desc:</strong></td>
	        		<td   ><? echo $dataArray_book[0][csf('style_desc')];?></td>
	        	</tr>
	        	<tr>
	        		<td width="100"><strong>Buyer Name: </strong></td>
	        		<td width="130" align="left"><? echo $buyer_library[$dataArray[0][csf('buyer_name')]];?></td>
	        		<td width="120" style="word-break:break-all;" align="left">&nbsp;&nbsp;<strong>Season:</strong></td>
	        		<td width="110">&nbsp;<? echo $season_arr[$dataArray[0][csf('season')]];?></td>
	        		<td width="110"><strong>BH Merchandiser:</strong></td>
	        		<td width="100"><? echo $dataArray[0][csf('bh_merchant')];?></td>
	        		<td width="110"><strong>Remarks/Desc:</strong></td>
	        		<td   style="word-wrap: break-word;word-break: break-all;" ><? echo $dataArray[0][csf('remarks')];?></td>

	        	</tr>
	        	<tr>
	        		<td width="100"   align="left"><strong>Buyer Ref:</strong></td>
	        		<td width="130" ><? echo $dataArray[0][csf('buyer_ref')];?></td>
	        		<td width="120"  >&nbsp;&nbsp;<strong>Product Dept:</strong></td>
	        		<td width="110" >&nbsp;<? echo $product_dept[$dataArray[0][csf('product_dept')]];?></td>
	        		<td width="110"  ><strong>Supplier:</strong></td>
	        		<td width="100" ><? 
					
						   if($dataArray_book[0][csf('pay_mode')]==1 || $dataArray_book[0][csf('pay_mode')]==2){
							echo $supplier_library[$dataArray_book[0][csf('supplier_id')]];
						   }elseif($dataArray_book[0][csf('pay_mode')]==3 || $dataArray_book[0][csf('pay_mode')]==4 || $dataArray_book[0][csf('pay_mode')]==4){
							echo $company_library[$dataArray_book[0][csf('supplier_id')]];
						   }

					?></td>
	        		<td width="110"><strong>Est. Ship Date:</strong></td>
	        		<td ><? echo change_date_format($dataArray[0][csf('estimated_shipdate')]); ?></td>

	        	</tr>
	            <tr>
					<td width="100"><strong>IR/Control No:</strong></td>
	        		<td width="130" ><? echo $dataArray[0][csf('internal_ref')];?></td>
	        		<td width="100">&nbsp;&nbsp;<strong>Team Leader:</strong></td>
	        		<td width="130" ><? echo $team_leader_arr[$dataArray_book[0][csf('team_leader')]];?></td>
	        		<td width="110"  ><strong>Sample Stage:</strong></td>
	        		<td width="100" ><? echo $sample_stage[$dataArray[0][csf('sample_stage_id')]];?></td>
	        		<td width="110"><strong>Booking Date:</strong></td>
                	<td width="100"><?=change_date_format($dataArray_book[0][csf('booking_date')]);?></td>

	        	</tr>
				<tr>
					<td colspan="2"><strong>Dealing Merchandiser:</strong></td>
	        		<td><? echo $dealing_merchant_library[$dataArray_book[0][csf('dealing_marchant')]];?></td>
				</tr>
	        </table>

	        <table width="1100" cellspacing="0" border="0"   style="font-family: Arial Narrow;margin-left: 20px;" >
	         <tr>
	        	<td width="250" align="left" valign="top" colspan="2">
	            <table align="left" cellspacing="0" border="0" width="90%" >

	        	</table>
	        </td>
	        </tr>



	         <tr> <td colspan="6">&nbsp;</td></tr>
	        <tr>
	        	<td width="250" align="left" valign="top" colspan="2">
	        	<?
				 $sql_sample_dtls= "SELECT a.sample_name, a.article_no, a.sample_color from sample_development_dtls a , lib_color b  where a.status_active=1 and a.is_deleted=0 and a.entry_form_id=203  and sample_mst_id='$data[1]' and b.status_active=1 and a.status_active=1 and b.id=a.sample_color  group by a.sample_name,a.article_no,a.sample_color";

				foreach(sql_select($sql_sample_dtls) as $key=>$value)
				{
					if($sample_wise_article_no[$value[csf("sample_name")]][$value[csf("sample_color")]]=="")
					{
						$sample_wise_article_no[$value[csf("sample_name")]][$value[csf("sample_color")]]=$value[csf("article_no")];
					}
					else
					{
						if(!in_array($value[csf("article_no")], $sample_wise_article_no))
						{
							$sample_wise_article_no[$value[csf("sample_name")]][$value[csf("sample_color")]].= ', '.$value[csf("article_no")];
						}

					}
					
					//$sample_wise_article_no[$value[csf("sample_name")]][$value[csf("sample_color")]]=$value[csf("article_no")];

				}
				/*$sql_book=sql_select("select dtls_id from wo_non_ord_samp_booking_dtls where style_id='$data[1]' and status_active=1");
				$dtls_id="";
				foreach($sql_book as $row)
				{
					$dtls_id.=$row[csf("dtls_id")].',';
				}
				$dtls_ids=rtrim($dtls_id,',');
				$dtls_ids=implode(",",array_unique(explode(",",$dtls_ids)));
				if($dtls_ids) $dtls_id_cond="and a.id in($dtls_ids) ";else $dtls_id_cond="and a.id in(0)";*/

				 $color_sql="SELECT b.color_id ,b.process_loss_percent ,b.fabric_color,b.contrast,b.mst_id,b.dtls_id from  sample_development_rf_color b where b.status_active=1 and b.is_deleted=0 and b.qnty>0 and b.mst_id='$data[1]' ";
				 $color_res=sql_select($color_sql);
				 $color_rf_data=array();
				 foreach ($color_res as $val) {
				 	$color_rf_data[$val[csf('dtls_id')]][$val[csf('color_id')]][$val[csf('fabric_color')]]['process_loss_percent']=$val[csf('process_loss_percent')];
				 	$color_rf_data[$val[csf('dtls_id')]][$val[csf('color_id')]][$val[csf('fabric_color')]]['contrast']=$val[csf('contrast')];
				 }

			//  $sql_fab="SELECT a.sample_name,a.gmts_item_id,b.color_id,b.contrast,c.finish_fabric as qnty,a.delivery_date,a.fabric_description,a.body_part_id, a.fabric_source,a.remarks_ra  ,a.gsm,a.dia, a.color_type_id,a.width_dia_id,a.uom_id,b.process_loss_percent,c.grey_fabric as grey_fab_qnty  from sample_development_fabric_acc a,sample_development_rf_color b, wo_non_ord_samp_booking_dtls c where a.id=b.dtls_id and  a.sample_mst_id=b.mst_id and a.id=c.dtls_id and c.fabric_color=b.fabric_color and c.gmts_color=b.color_id and c.dtls_id=b.dtls_id and c.style_id=a.sample_mst_id and c.style_id=b.mst_id and a.determination_id=c.lib_yarn_count_deter_id  and a.form_type=1 and b.qnty>0 and c.grey_fabric>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and a.sample_mst_id='$data[1]' and b.mst_id='$data[1]'  ";

			$sql_fab="SELECT a.sample_name,a.gmts_item_id,c.gmts_color as color_id,c.finish_fabric as qnty,a.delivery_date,a.fabric_description,a.body_part_id, a.fabric_source,a.remarks_ra  ,a.gsm,a.dia, a.color_type_id,a.width_dia_id,a.uom_id,a.determination_id,c.grey_fabric as grey_fab_qnty,c.dtls_id,c.fabric_color  from sample_development_fabric_acc a, wo_non_ord_samp_booking_dtls c where  a.id=c.dtls_id and  c.style_id=a.sample_mst_id  and a.determination_id=c.lib_yarn_count_deter_id  and a.form_type=1 and c.grey_fabric>0 and a.status_active=1 and a.is_deleted=0  and c.status_active=1 and c.is_deleted=0  and a.sample_mst_id='$data[1]'  ";
			
				 $sql_fab_arr=array();
				 foreach(sql_select($sql_fab) as $vals)
				 {
				 	$contrast=$color_rf_data[$vals[csf('dtls_id')]][$vals[csf('color_id')]][$vals[csf('fabric_color')]]['contrast'];
			 		$process_loss_percent=$color_rf_data[$vals[csf('dtls_id')]][$vals[csf('color_id')]][$vals[csf('fabric_color')]]['process_loss_percent'];

					$article_no=rtrim($sample_wise_article_no[$vals[csf("sample_name")]][$vals[csf("color_id")]],',');
					$article_no=implode(",",array_unique(explode(",",$article_no)));
					$sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("determination_id")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$contrast]["qnty"]+=$vals[csf("qnty")];
					
				 	$sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("determination_id")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$contrast]["process_loss_percent"]=$process_loss_percent;

				 	$sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("determination_id")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$contrast]["grey_fab_qnty"]+=$vals[csf("grey_fab_qnty")];

				 	$sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("determination_id")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$contrast]["delivery_date"] =change_date_format($vals[csf("delivery_date")]);

				 	$sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("determination_id")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$contrast]["fabric_source"] =$vals[csf("fabric_source")];

				 	$sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("determination_id")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$contrast]["uom_id"] =$vals[csf("uom_id")];
					$sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("determination_id")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$contrast]["dia"] =$vals[csf("dia")];

				 	$sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("determination_id")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$contrast]["width_dia_id"] =$vals[csf("width_dia_id")];

				 	$sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("determination_id")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$contrast]["remarks"] =$vals[csf("remarks_ra")];
				 	$sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("determination_id")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$contrast]["color_type_id"] =$vals[csf("color_type_id")];
				 }
				 $sample_item_wise_span=array(); $sample_item_wise_color_span=array();

			  foreach($sql_fab_arr as $article_no=>$article_data) 
	          {
				$article_no_span=0;
				foreach($article_data as $sample_type_id=>$sampleType_data) 
	        	{
				$sample_type_span=0;
				foreach($sampleType_data as $gmts_color_id=>$gmts_color_data)
	        	{
					$sample_span=0;
	        		foreach($gmts_color_data as $body_part_id=>$body_part_data)
	        		{
	        			
						//echo $gmts_color_id.'d';

	        			foreach($body_part_data as $fab_id=>$fab_desc_data)
	        			{
	        				//$kk=0;
	        				foreach($fab_desc_data as $colorType=>$colorType_data)
	        				{

	        					foreach($colorType_data as $gsm_id=>$gsm_data)
	        					{
	        						foreach($gsm_data as $dia_id=>$dia_data)
	        						{
									   foreach($dia_data as $dia_type_id=>$diatype_data)
	        						   {

	        							foreach($diatype_data as $contrast_id=>$value)
	        							{
	        								$sample_span++;$sample_type_span++;$article_no_span++;
	        								//$kk++;

	        							}
											$article_wise_span[$article_no]=$article_no_span;
											$sample_item_wise_span[$article_no][$sample_type_id]=$sample_type_span;
											$sample_item_wise_color_span[$article_no][$sample_type_id][$gmts_color_id]=$sample_span;
									  }
	        						}

	        					}


	        				}

	        				//$bodypart_item_wise_span[$sample_type][$gmts_item_id][$body_part_id]=$kk;

	        			}
	        		//	$sample_item_wise_span[$sample_type][$gmts_color_id]=$sample_span;

	        		  }
					 }

	        		}
				}
	        	//echo "<pre>";
	        	//print_r($sample_item_wise_color_span);die;
				// echo "<pre>"; print_r($sample_wise_article_no);die;

				?>
				<table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all">
					<thead>
					<tr>
						<th colspan="19">Required Fabric</th>
					</tr>
						<tr>
							<th width="30">Sl</th>
							<th width="90">ALT / [C/W]</th>
							<th width="110">Sample Type</th>
							<th width="80">Gmt Color</th>
							<th width="80">Fab. Deli Date</th>
							<th width="120">Body Part</th>
							<th width="200">Fabric Desc & Composition</th>
							<th width="80">Color Type</th>
							<th width="80">Fab.Color</th>
							<th width="40">Item Size</th>
							<th width="55">GSM</th>
							<th width="55">Dia</th>
							<th width="60">Width/Dia</th>
							<th width="40">UOM</th>
							<th width="60">Grey Qnty</th>
							<th width="40">P. Loss</th>
							<th width="80">Fin Fab Qnty</th>
							<th width="80">Fabric Source</th>
							<th width="80">Remarks</th>

						</tr>
					</thead>
					<tbody>
						<?
						$p=1;
						$total_finish=0;
						$total_grey=0;
						$total_process=0;
			 foreach($sql_fab_arr as $article_no=>$article_data) 
	         {
				$aa=0;
				foreach($article_data as $sample_type_id=>$sampleType_data) 
	        	{
				$nn=0;
				foreach($sampleType_data as $gmts_color_id=>$gmts_color_data)
	        	{
					$cc=0;
	        		foreach($gmts_color_data as $body_part_id=>$body_part_data)
	        		{
	        			
						//echo $gmts_color_id.'d';

	        			foreach($body_part_data as $fab_id=>$fab_desc_data)
	        			{
	        				//$kk=0;
	        				foreach($fab_desc_data as $colorType=>$colorType_data)
	        				{

	        					foreach($colorType_data as $gsm_id=>$gsm_data)
	        					{
	        						foreach($gsm_data as $dia_id=>$dia_data)
	        						{

	        							foreach($dia_data as $dia_type=>$diatype_data)
	        							{
											foreach($diatype_data as $contrast_id=>$value)
	        							    {

															 
														?>
														<tr>


																
																<?
															if($aa==0)
															{
																?>
	                                                            <td  rowspan="<? echo $article_wise_span[$article_no];?>"  align="center" style="word-wrap: break-word;word-break: break-all;"><? echo $p;$p++;?></td>
	                                                            <td   rowspan="<? echo $article_wise_span[$article_no];?>" align="center"><? echo $article_no;?></td>
	                                                            <?
															}
															if($nn==0)
															{
																?>
																
																<td   rowspan="<? echo $sample_item_wise_span[$article_no][$sample_type_id];?>"  align="center"><? echo $sample_library[$sample_type_id]; ?></td>
																
																<?
																
															}
															if($cc==0)
															{
															 ?>
	                                                         <td   align="center" rowspan="<? echo $sample_item_wise_color_span[$article_no][$sample_type_id][$gmts_color_id];?>"><? echo $color_library[$gmts_color_id];?> </td>
	                                                          <td   rowspan="<? echo $sample_item_wise_color_span[$article_no][$sample_type_id][$gmts_color_id];?>" align="center" ><? echo $value["delivery_date"];?> </td>
	                                                         <?
	                                                        } 
															 $fab_desc=$fab_constructArr[$fab_id].','.$fabric_composition_arr[$lip_yarn_count[$fab_id]];
															?>

															
															 <td width="120"  align="center"><? echo $body_part[$body_part_id];?></td>
															 <td  align="center"><? echo chop($fab_desc,","); //$fab_id;?></td>
															 <td  align="center"> <? echo $color_type[$colorType]; ?></td>
															 <td  align="center"><? echo $contrast_id; ?></td>
															 <td  align="center"><? echo $value["item_size"]; ?></td>
															 <td  align="center"><? echo $gsm_id; ?></td>
															 <td  align="center"><? echo $value["dia"]; ?></td>
															 <td  align="center"><? echo $fabric_typee[$dia_type]; ?></td>
															 <td   align="center"><? echo $unit_of_measurement[$value["uom_id"]];?></td>

															 <td align="right"><? echo number_format($value["grey_fab_qnty"],2);?></td>
															 <td align="right"><? echo $value["process_loss_percent"];?></td>
															 <td align="right"><? echo number_format($value["qnty"],2);?></td>

															 <td align="center"><? echo $fabric_source[$value["fabric_source"]];?></td>
															 <td  align="center"><? echo $value["remarks"];?></td>

														</tr>


														<?
														$nn++;$cc++;$aa++;
			        									//$i++;
														$total_finish +=$value["qnty"];
														$total_grey +=$value["grey_fab_qnty"];
														$total_process +=$value["process_loss_percent"];
													}
												}
											}
										}
									}
								}
							  }
							}
						}
			 		}

						?>

						<tr>
							<th colspan="14" align="right"><b>Total</b></th>
							<th width="80" align="right"><? echo number_format($total_grey,2);?></th>
							<th width="40" align="right">&nbsp;</th>
							<th width="60" align="right"><? echo number_format($total_finish,2);?></th>
							<th width="80" colspan="2"> </th>

						</tr>

					</tbody>



				</table>
				<br/>



	<?

				$sample_color_arr=return_library_array( "select id, sample_color from sample_development_dtls", "id", "sample_color"  );
	                      $sql_qry="SELECT id,sample_mst_id,sample_name,gmts_item_id,smv,article_no,sample_color,sample_prod_qty,submission_qty,delv_start_date,delv_end_date,sample_charge,sample_curency,sent_to_buyer_date,comments from sample_development_dtls where status_active =1 and is_deleted=0 and entry_form_id=203 and sample_mst_id='$data[1]' order by id asc";
						    $sql_qry_color="SELECT a.id,a.sample_mst_id,a.sample_name,a.gmts_item_id,a.smv,a.article_no,a.sample_color,a.sample_prod_qty,a.submission_qty,a.delv_start_date,a.delv_end_date,a.sample_charge,a.sample_curency,a.sent_to_buyer_date,a.comments,c.dtls_id,c.size_id,c.bh_qty,c.self_qty,c.test_qty,c.plan_qty,c.dyeing_qty from sample_development_dtls a,sample_development_size c where a.id=c.dtls_id and  a.status_active =1 and a.is_deleted=0 and a.entry_form_id=203 and a.sample_mst_id='$data[1]' order by a.id asc";
						 $size_type_arr=array(1=>"bh_qty",2=>"Self Qty",3=>"Test qty",4=>"Plan Qty",5=>"Dyeing Qty");
						 $color_size_arr=array();
						  foreach(sql_select($sql_qry_color) as $vals)
						 {
								if($vals[csf("bh_qty")]>0)
								{
								$color_size_arr[1][$vals[csf("size_id")]]='Bh Qty';
								$bh_qty=$vals[csf("bh_qty")];
								$color_size_dtls_qty_arr[1][$vals[csf("id")]][$vals[csf("size_id")]]=$bh_qty;
								}
								if($vals[csf("self_qty")]>0)
								{
								$color_size_arr[2][$vals[csf("size_id")]]='self qty';
								$color_size_dtls_qty_arr[2][$vals[csf("id")]][$vals[csf("size_id")]]=$vals[csf("self_qty")];
								}
								if($vals[csf("test_qty")]>0)
								{
								$color_size_arr[3][$vals[csf("size_id")]]='test qty';
								$color_size_dtls_qty_arr[3][$vals[csf("id")]][$vals[csf("size_id")]]=$vals[csf("test_qty")];
								}
								if($vals[csf("plan_qty")]>0)
								{
								$color_size_arr[4][$vals[csf("size_id")]]='plan qty';
								//$size_plan_arr[$vals[csf("size_id")]]=$vals[csf("size_id")];
								$color_size_dtls_qty_arr[4][$vals[csf("id")]][$vals[csf("size_id")]]=$vals[csf("plan_qty")];

								}
								if($vals[csf("dyeing_qty")]>0)
								{
								$color_size_arr[5][$vals[csf("size_id")]]='Dyeing qty';
								$color_size_dtls_qty_arr[5][$vals[csf("id")]][$vals[csf("size_id")]]=$vals[csf("dyeing_qty")];

								}

							}
							$tot_row=count($color_size_arr);
							$result=sql_select($sql_qry);

	?>


	            	<table align="left" cellspacing="0" border="1" width="100%" class="rpt_table" rules="all">
	            	<thead>
	            		<tr>
	                            <td width="150" colspan="<? echo 12+$tot_row;?>" align="center"><strong>Sample Details</td>
	                        </tr>
	                        <tr>
									<th width="30" rowspan="2" align="center">Sl</th>
									<th width="100" rowspan="2" align="center">Sample Name</th>
									<th width="120" rowspan="2" align="center">Garment Item</th>

									<th width="55" rowspan="2" align="center">ALT / [C/W]</th>
									<th width="70" rowspan="2" align="center">Color</th>
	                                <?
									$tot_row_td=0;
	                                foreach($color_size_arr as $type_id=>$val)
									{ ?>
										<th width="45" align="center" colspan="<? echo count($val);?>"> <?
	                                 		  echo  $size_type_arr[$type_id];
										?></th>
	                                    <?

									}
									?>
									<th rowspan="2" width="55" align="center">Total</th>
									<th rowspan="2" width="55" align="center">Submn Qty</th>
									<th rowspan="2"  width="70" align="center">Buyer Submisstion Date</th>
									<th rowspan="2"  width="70" align="center">Remarks</th>
	                         </tr>
	                         <tr>
	                         	<?
	                            foreach($color_size_arr as $type_id=>$data_size)
								{
									foreach($data_size as $size_id=>$data_val)
									{
									$tot_row_td++;
									?>
										<th width="40" align="center"><? echo $size_library[$size_id]; ?></th>
										<?
									}
	                         	}

	                         	?>
	                         </tr>

	            	</thead>
	                    <tbody>

	                        <?

	 						$i=1;$k=0;
	 						$gr_tot_sum=0;
	 						$gr_sub_sum=0;
							foreach($result as $row)
							{
								$dtls_ids=$row[csf('id')];
								 //$size_select=sql_select("SELECT  size_id,total_qty  from sample_development_size where  mst_id='$data[1]' and status_active=1 and is_deleted=0 and dtls_id='$dtls_ids' ");
	 							$prod_sum=$prod_sum+$row[csf('sample_prod_qty')];
								$sub_sum=$sub_sum+$row[csf('submission_qty')];

							?>
	                        <tr>
	                            <?
	 							$k++;
								?>
	                            <td  align="center"><? echo $k;?></td>
	                            <td  align="left"><? echo $sample_library[$row[csf('sample_name')]];?></td>
	                            <td  align="left"><? echo $garments_item[$row[csf('gmts_item_id')]];?></td>

	                            <td   align="left"><? echo $row[csf('article_no')];?></td>
	                            <td width="70" align="left"><? echo $color_library[$row[csf('sample_color')]];?></td>


	                            <?
	                            $total_sizes_qty=0;
	                            $total_sizes_qty_subm=0;
	                          	foreach($color_size_arr as $type_id=>$data_size)
								{
									foreach($data_size as $size_id=>$data_val)
									{
									$size_qty=$color_size_dtls_qty_arr[$type_id][$dtls_ids][$size_id];
	                            	?>
	                            	<td align="right"><? echo $size_qty; ?></td>
	                            	<?
										if($type_id==1)
										{
										$total_sizes_qty_subm+=$size_qty;
										}
										$total_sizes_qty+=$size_qty;
									}
	                            }
	                            ?>
	                            <td align="right"><? echo $total_sizes_qty;?></td>
	                            <td align="right"><? echo $total_sizes_qty_subm;?></td>
	                            <td   align="left"><? echo change_date_format($row[csf('sent_to_buyer_date')]);?> </td>
	                            <td   align="left"><? echo $row[csf('comments')];?> </td>
	                            <?
	                            $gr_tot_sum+=$total_sizes_qty;
	 							$gr_sub_sum+=$total_sizes_qty_subm;
	                        }
							?>
	                        </tr>
								<tr>
										<td colspan="<? echo 5+$tot_row_td; ?>" align="right"><b>Total</b></td>
	 									<td   align="right"><b><? echo number_format($gr_tot_sum,2);?> </b></td>
	 									<td  align="right"><b><? echo number_format($gr_sub_sum,2);?> </b></td>
										<td colspan="2"></td>
								</tr>
	                    </tbody>
	                    <tfoot>
	                     </tfoot>
	               </table>
	             </td>
        </tr>
         <tr> <td colspan="6">&nbsp;</td></tr>
        <tr>
        	<td width="250" align="left" valign="top" colspan="2">

             </td>
        </tr>

        <tr>
        	<td width="250" align="left" valign="top" colspan="2">
            	<table align="left" cellspacing="0" border="1" width="100%" class="rpt_table" rules="all">
            	<thead>
            		<tr>
                            <td width="150" colspan="10" align="center"><strong>Required Accessories</td>
                        </tr>
                        <tr>
								<th width="30" align="center">Sl</th>
								<th width="100" align="center">Sample Name</th>
								<th width="120" align="center">Garment Item</th>
								<th width="100" align="center">Trims Group</th>
								<th width="100" align="center">Description</th>
								<th width="100" align="center">Supplier</th>
								<th width="100" align="center">Brand/Supp.Ref</th>
 								<th width="30" align="center">UOM</th>
								<th width="30" align="center">Req/Dzn </th>
								<th width="30" align="center">Req/Qty </th>
								<th width="80" align="center">Acc.Sour. </th>
								<th width="100" align="center">Acc Delivery Date </th>
								<th width="80" align="center">Remarks </th>
                         </tr>
            	</thead>
                    <tbody>


                        <?
					   $sql_qryA="SELECT id,sample_mst_id,sample_name_ra,gmts_item_id_ra,trims_group_ra,description_ra,brand_ref_ra,uom_id_ra,req_dzn_ra,req_qty_ra,remarks_ra,delivery_date,supplier_id,fabric_source from sample_development_fabric_acc where status_active =1 and is_deleted=0 and form_type=2 and sample_mst_id='$data[1]' order by id asc";

						$resultA=sql_select($sql_qryA);
 						$i=1;$k=0;
 						$req_dzn_ra=0;
 						$req_qty_ra=0;
						foreach($resultA as $rowA)
						{
							$req_dzn_ra=$req_dzn_ra+$rowA[csf('req_dzn_ra')];
							$req_qty_ra=$req_qty_ra+$rowA[csf('req_qty_ra')];

						?>
                        <tr>
                            <?
 							$k++;
							?>
                            <td  align="center"><? echo $k;?></td>
                            <td  align="left"><? echo $sample_library[$rowA[csf('sample_name_ra')]];?></td>
                            <td  align="left"><? echo $garments_item[$rowA[csf('gmts_item_id_ra')]];?></td>
                            <td  align="left"><? echo $trims_group_lib[$rowA[csf('trims_group_ra')]];?></td>
                            <td  align="left"><? echo $rowA[csf('description_ra')];?></td>
                            <td  align="left"><? echo $supplier_library[$rowA[csf('supplier_id')]];?></td>
                            <td  align="left"><? echo $rowA[csf('brand_ref_ra')];?></td>
                             <td  align="center"><? echo $unit_of_measurement[$rowA[csf('uom_id_ra')]];?></td>
                            <td  align="right"><? echo $rowA[csf('req_dzn_ra')];?></td>
                            <td  align="right"><? echo $rowA[csf('req_qty_ra')];?></td>
                            <td  align="left"><? echo $fabric_source[$rowA[csf('fabric_source')]];?></td>
                            <td  align="left"><? echo change_date_format($rowA[csf('delivery_date')]);?></td>
                            <td  align="left"><? echo $rowA[csf('remarks_ra')];?></td>

                            <?
                        }

						?>




                        </tr>

                          <tr>
									<td colspan="8" align="center"><b>Total </b></td>
									<!-- <td align="right"><b><? echo number_format($req_dzn_ra,2);?> </b></td> -->
  									<td align="right"  ><b><? echo number_format($req_qty_ra,2);?> </b></td>
  									<td>&nbsp;</td>

 							</tr>


                    </tbody>
                    <tfoot>

                    </tfoot>
               </table>
             </td>
        </tr>
         <tr> <td colspan="6">&nbsp;</td></tr>

         <tr>
        	<td width="250" align="left" valign="top" colspan="2">
            	<table align="left" cellspacing="0" border="1" width="100%" class="rpt_table" rules="all">
            	<thead>
            		<tr>
                            <td width="150" colspan="6" align="center"><strong>Required Emebellishment</td>
                        </tr>
                        <tr>
                        	<th width="30" align="center">Sl</th>
                        	<th width="100" align="center">Sample Name</th>
                        	<th width="110" align="center">Garment Item</th>
                        	<th width="110" align="center">Body Part</th>
                        	<th width="100" align="center">Supplier</th>
                        	<th width="60" align="center">Name</th>
                        	<th width="70" align="center">Type</th>
                        	<th width="100" align="center">Emb.Del.Date</th>
                        	<th width="70" align="center">Remarks</th>

                         </tr>
            	</thead>
                    <tbody>


                        <?
                        $sql_qry="SELECT id,sample_mst_id,sample_name_re,gmts_item_id_re,name_re,type_re,remarks_re,body_part_id,delivery_date,supplier_id from sample_development_fabric_acc where sample_mst_id='$data[1]' and form_type=3 and is_deleted=0  and status_active=1 order by id asc";

						$result=sql_select($sql_qry);
 						$k=0;
 						$type_array=array(1=>$emblishment_print_type,2=>$emblishment_embroy_type,3=>$emblishment_wash_type,4=>$emblishment_spwork_type,5=>$emblishment_gmts_type);
						foreach($result as $row)
						{

						?>
                        <tr>
                            <?
 							$k++;
							?>
                            <td  align="center"><? echo $k;?></td>
                            <td  align="left"><? echo $sample_library[$row[csf('sample_name_re')]];?></td>
                            <td  align="left"><? echo $garments_item[$row[csf('gmts_item_id_re')]];?></td>
                            <td  align="left"><? echo $body_part[$row[csf('body_part_id')]];?></td>
                            <td  align="left"><? echo $supplier_library[$row[csf('supplier_id')]];?></td>
                            <td  align="left"><? echo $emblishment_name_array[$row[csf('name_re')]];?></td>
                            <td  align="left">
                            <?
                            if($row[csf('name_re')]==1)
                            {
                          	  echo $emblishment_print_type[$row[csf('type_re')]];
                          	}
                          	if($row[csf('name_re')]==2)
                            {
                          	  echo $emblishment_embroy_type[$row[csf('type_re')]];
                          	}
                          	if($row[csf('name_re')]==3)
                            {
                          	  echo $emblishment_wash_type[$row[csf('type_re')]];
                          	}
                          	if($row[csf('name_re')]==4)
                            {
                          	  echo $emblishment_spwork_type[$row[csf('type_re')]];
                          	}
                          	if($row[csf('name_re')]==5)
                            {
                          	  echo $emblishment_gmts_type[$row[csf('type_re')]];
                          	}
                            ?>

                            </td>
                            <td  align="left"><? echo change_date_format($row[csf('delivery_date')]);?></td>
                            <td  align="left"><? echo $row[csf('remarks_re')];?></td>
                              <?
                        }

						?>




                        </tr>


                    </tbody>
                    <tfoot>

                    </tfoot>
               </table>

               <br>
               <table>
               		<tr>
               			<td>
   				            <table  style="margin-top: 10px;" class="rpt_table" width="625" align="left" border="1" cellpadding="0" cellspacing="0" rules="all">
   				                <caption> <b> Yarn Required Summary </b> </caption>
   				                	<thead>
   				                    	<tr align="center">
   				                        	<th width="40">Sl</th>
   				                        	<th>Yarn Desc.</th>
   				                             <th>Req. Qty</th> 
   				                        </tr>
   				                    </thead>
   				                    <tbody>
   				                    <?
   									$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count"  );
   									$lib_supllier_arr=return_library_array( "select supplier_id,booking_no from wo_non_ord_samp_booking_mst where booking_no='$data[2]' and entry_form_id=140", "booking_no", "supplier_id"  );
   								//	echo  "select supplier_id,booking_no from wo_non_ord_samp_booking_mst where booking_no='$data[2]' and entry_form_id=140";
   									$tot_req_qty=0;//sample_development_mst
   									//$data_array=sql_select("select b.booking_no,b.determin_id,b.count_id,b.copm_one_id,b.percent_one,b.type_id,b.cons_qnty from  sample_development_yarn_dtls b,sample_development_fabric_acc a where a.sample_mst_id=b.mst_id and a.determination_id=b.determin_id and b.status_active=1 and a.status_active=1 and b.mst_id='$data[1]' and a.form_type=1 group by b.booking_no, b.determin_id, b.count_id, b.copm_one_id, b.percent_one, b.type_id, b.cons_qnty");
   									$data_array=sql_select("SELECT b.booking_no,b.determin_id,b.count_id,b.copm_one_id,b.percent_one,b.type_id, sum (b.cons_qnty) as cons_qnty from  sample_development_yarn_dtls b where  b.status_active=1  and b.mst_id='$data[1]' and b.determin_id in (select determination_id from sample_development_fabric_acc  where status_active=1 and sample_mst_id='$data[1]' and form_type=1) group by b.booking_no,b.determin_id,b.count_id,b.copm_one_id,b.percent_one,b.type_id");

   									//echo "select b.booking_no,b.determin_id,b.count_id,b.copm_one_id,b.percent_one,b.type_id,b.cons_qnty from  sample_development_yarn_dtls b,sample_development_fabric_acc a where a.sample_mst_id=b.mst_id and a.determination_id=b.determin_id and b.status_active=1 and a.status_active=1  and b.mst_id='$data[1]' and a.form_type=1";
   								
   									if ( count($data_array)>0)
   									{
   										$l=1;
   										foreach( $data_array as $key=>$row )
   										{
   											$yarn_des=$lib_yarn_count[$row[csf("count_id")]].','.$composition[$row[csf("copm_one_id")]].','.$row[csf("percent_one")].'%,'.$yarn_type[$row[csf("type_id")]];
   											?>
   				                            	<tr>
   				                                    <td> <? echo $l;?> </td>
   				                                    <td> <? echo $yarn_des; ?> </td>
   				                                    <td align="right"> <? echo number_format($row[csf("cons_qnty")],2); ?> </td>
   				                                </tr>
   				                            <?
   				                            $l++;
   											$tot_req_qty+=$row[csf("cons_qnty")];
   										}
   									}

   									?>
   				                    <tr>
   										<th  colspan="2" align="right"><b>Total</b></th>
   										<th  align="right"><? echo number_format($tot_req_qty,2);?></th>
   									</tr>
   				                </tbody>
   				            </table>
               			</td>
               			<td width="300">
               				<?php 

               					$sql_image=sql_select("select image_location from common_photo_library where master_tble_id='$data[1]' and form_name='sample_requisition_2'");

               				 ?>
               				 <img src="<?php echo base_url($sql_image[0][csf('image_location')]);?>" width="200" height="150" style="justify-content: center;text-align: center;float: right;">
               			</td>
               		</tr>
               </table>

			   <?
        	$sample_stripe_data=sql_select("SELECT a.body_part_id, b.contrast, b.color_id, b.grey_fab_qnty,c.id as strip_mst_id, c.stripe_color, c.measurement, c.uom, c.fabreq, c.yarn_dyed,c.totfidder FROM sample_development_fabric_acc a join sample_development_rf_color b on a.id=b.dtls_id join wo_sample_stripe_color c on a.id=c.sample_fab_dtls_id and b.color_id=c.color_number_id where a.status_active=1 and a.is_deleted=0 and a.color_type_id in (2,3,4,6,31,32,33,34) and a.form_type=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.sample_mst_id=$data[1]");
        	
        	foreach ($sample_stripe_data as $row) {
        		$key=$row[csf('body_part_id')].'*'.$row[csf('color_id')];
        		$sample_stripe_arr[$key]['body_part_id'] = $row[csf('body_part_id')];
        		$sample_stripe_arr[$key]['fabric_color'] = $row[csf('color_id')];
        		$sample_stripe_arr[$key]['fabric_qty'] = $row[csf('grey_fab_qnty')];
				
        		$sample_stripe_arr[$key]['stripe_color'][$row[csf('strip_mst_id')]][$row[csf('stripe_color')]]['color'] = $row[csf('stripe_color')];
        		$sample_stripe_arr[$key]['stripe_color'][$row[csf('strip_mst_id')]][$row[csf('stripe_color')]]['measurement'] = $row[csf('measurement')];
        		$sample_stripe_arr[$key]['stripe_color'][$row[csf('strip_mst_id')]][$row[csf('stripe_color')]]['uom'] = $row[csf('uom')];
        		$sample_stripe_arr[$key]['stripe_color'][$row[csf('strip_mst_id')]][$row[csf('stripe_color')]]['qty'] = $row[csf('fabreq')];
        		$sample_stripe_arr[$key]['stripe_color'][$row[csf('strip_mst_id')]][$row[csf('stripe_color')]]['yarn_dyed'] = $row[csf('yarn_dyed')];
				$sample_stripe_arr[$key]['stripe_color'][$row[csf('strip_mst_id')]][$row[csf('stripe_color')]]['totfidder'] = $row[csf('totfidder')];
        		
				$stripe_color_summ[$row[csf('stripe_color')]] += $row[csf('fabreq')];
        	}
        ?>
     
        <?
			$coller_cuff_data=sql_select("SELECT a.sample_color, a.size_id, a.item_size, a.qnty_pcs, c.body_part_type from sample_requisition_coller_cuff a join sample_development_fabric_acc b on b.id=a.dtls_id join lib_body_part c on b.body_part_id=c.id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.sample_mst_id=$data[1]");
			//echo "SELECT a.sample_color, a.size_id, a.item_size, a.qnty_pcs, c.body_part_type from sample_requisition_coller_cuff a join sample_development_fabric_acc b on b.id=a.dtls_id join lib_body_part c on b.body_part_id=c.id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.sample_mst_id=$data[1]";
			
			 
			$coller_data_arr=array(); $cuff_data_arr=array();
			foreach ($coller_cuff_data as $row) {
				if($row[csf('body_part_type')]==40)
				{
					$coller_size_arr[$row[csf('size_id')]]=$row[csf('size_id')];
					$color_size_data[$row[csf('size_id')]]['item_size']=$row[csf('item_size')];
					$color_color_data[$row[csf('sample_color')]][$row[csf('size_id')]]['qnty_pcs']=$row[csf('qnty_pcs')];
				}
				if($row[csf('body_part_type')]==50)
				{
					$cuff_size_arr[$row[csf('size_id')]]=$row[csf('size_id')];
					$cuff_size_data[$row[csf('size_id')]]['item_size']=$row[csf('item_size')];
					$cuff_color_data[$row[csf('sample_color')]][$row[csf('size_id')]]['qnty_pcs']=$row[csf('qnty_pcs')];
				}
			} 
			/*echo '<pre>';
			print_r($color_color_data); die;*/
        ?>
        <div style="width:1000px; margin-top: 10px;">
            <?
            $collar_cuff_percent_arr=array(); $collar_cuff_body_arr=array(); $collar_cuff_color_arr=array(); $collar_cuff_size_arr=array(); $collar_cuff_item_size_arr=array(); $color_size_sensitive_arr=array();

			$collar_cuff_sql="select b.id, b.gmts_item_id as item_number_id, a.qnty_pcs,a.sample_color as color_number_id, a.size_id as gmts_sizes, a.item_size, a.size_id as size_number_id,  e.body_part_full_name, e.body_part_type
			FROM sample_requisition_coller_cuff a left join lib_size s on a.size_id=s.id, sample_development_fabric_acc b, lib_body_part  e

			WHERE b.id=a.dtls_id   and b.body_part_id=e.id and e.body_part_type in (40,50)  and b.sample_mst_id=$data[1] and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and e.status_active=1 and e.is_deleted=0 order by  b.id,a.sample_color,s.sequence";
			//echo $collar_cuff_sql;
			$collar_cuff_sql_res=sql_select($collar_cuff_sql);
			$itemIdArr=array();

			foreach($collar_cuff_sql_res as $collar_cuff_row) 
			{
				$collar_cuff_percent_arr[$collar_cuff_row[csf('body_part_type')]][$collar_cuff_row[csf('body_part_full_name')]][$collar_cuff_row[csf('color_number_id')]][$collar_cuff_row[csf('gmts_sizes')]]=$collar_cuff_row[csf('colar_cuff_per')];
				$collar_cuff_body_arr[$collar_cuff_row[csf('body_part_type')]][$collar_cuff_row[csf('body_part_full_name')]]=$collar_cuff_row[csf('body_part_full_name')];
				$collar_cuff_size_arr[$collar_cuff_row[csf('body_part_type')]][$collar_cuff_row[csf('body_part_full_name')]][$collar_cuff_row[csf('size_number_id')]]=$collar_cuff_row[csf('size_number_id')];
				if(!empty($collar_cuff_row[csf('item_size')]))
				{
					$collar_cuff_item_size_arr[$collar_cuff_row[csf('body_part_full_name')]][$collar_cuff_row[csf('size_number_id')]][$collar_cuff_row[csf('item_size')]]=$collar_cuff_row[csf('item_size')];
				}
				
				$color_size_sensitive_arr[$collar_cuff_row[csf('body_part_full_name')]][$collar_cuff_row[csf('id')]][$collar_cuff_row[csf('color_number_id')]]=$collar_cuff_row[csf('qnty_pcs')];
				// $collar_cuff_size_Qty_arr[$collar_cuff_row[csf('body_part_type')]][$collar_cuff_row[csf('body_part_full_name')]][$collar_cuff_row[csf('id')]][$collar_cuff_row[csf('size_number_id')]]=$collar_cuff_row[csf('qnty_pcs')];

				$collar_cuff_size_Qty_arr[$collar_cuff_row[csf('body_part_type')]][$collar_cuff_row[csf('body_part_full_name')]][$collar_cuff_row[csf('id')]][$collar_cuff_row[csf('color_number_id')]][$collar_cuff_row[csf('size_number_id')]]=$collar_cuff_row[csf('qnty_pcs')];
				
				$itemIdArr[$collar_cuff_row[csf('body_part_type')]].=$collar_cuff_row[csf('item_number_id')].',';
			}
			unset($collar_cuff_sql_res);
			foreach($collar_cuff_body_arr as $body_type=>$body_name)
			{
				$gmtsItemId=array_filter(array_unique(explode(",",$itemIdArr[$body_type])));
				foreach($body_name as $body_val)
				{
					$count_collar_cuff=count($collar_cuff_size_arr[$body_type][$body_val]);
					$pre_grand_tot_collar=0; $pre_grand_tot_collar_order_qty=0;

					?>
                    <div style="max-height:1330px; overflow:auto; float:left; padding-top:5px; margin-left:5px; margin-bottom:5px; position:relative;font-size:18px;">
					<table width="625" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                        <tr>
                        	<td colspan="<? echo $count_collar_cuff+3; ?>" align="center"><b><? echo $body_val; ?> - Color Size Brakedown in Pcs.</b></td>
                        </tr>
                        <tr>
                            <td width="100">Size</td>
								<?
                                foreach($collar_cuff_size_arr[$body_type][$body_val]  as $size_number_id)
                                {
									?>
									<td align="center" style="border:1px solid black"><strong><? echo $size_library[$size_number_id];?></strong></td>
									<?
                                }
                                ?>
                            <td width="60" rowspan="2" align="center"><strong>Total</strong></td>
                            
                        </tr>
                        <tr>
                            <td style="font-size:12px"><? echo $body_val; ?> Size</td>
                            <?
                            foreach($collar_cuff_item_size_arr[$body_val]  as $size_number_id=>$size_number)
                            {
								if(count($size_number)>0)
								{
									 foreach($size_number  as $item_size=>$val)
									 {
										?>
										<td align="center" style="border:1px solid black"><strong><? echo $item_size;?></strong></td>
										<?
									 }
								}
								else
								{
									?>
									<td align="center" style="border:1px solid black"><strong> &nbsp;</strong></td>
									<?
								}
                            }
                            ?>
                        </tr>
                            <?

                            $pre_size_total_arr=array();
                            foreach($color_size_sensitive_arr[$body_val] as $fab_req_id=>$pre_cost_data)
                            {
								foreach($pre_cost_data as $color_number_id=>$color_number_data)
								{
									//foreach($color_number_data as $color_size_sensitive=>$color_break_down)
									//{
										$pre_color_total_collar=0;
										$pre_color_total_collar_order_qnty=0;
										 
										?>
										<tr>
											<td>
												<?
                                               
												 echo $color_library[$color_number_id];
                                                ?>
											</td>
											<?
											foreach($collar_cuff_size_arr[$body_type][$body_val] as $size_number_id)
											{
												?>
												<td align="center" style="border:1px solid black">
													<?   $collerqty=0;  
													$color_cuff_cut=0;
													// $color_cuff_cut=$collar_cuff_size_Qty_arr[$body_type][$body_val][$fab_req_id][$size_number_id];
													$color_cuff_cut=$collar_cuff_size_Qty_arr[$body_type][$body_val][$fab_req_id][$color_number_id][$size_number_id];
                                                	if($body_type==50){
														// $collerqty=$color_cuff_cut*2;
														$collerqty=$color_cuff_cut;
													}else{
														$collerqty=$color_cuff_cut;
													}
                                                    echo number_format($collerqty);
                                                    $pre_size_total_arr[$size_number_id]+=$collerqty;
                                                    $pre_color_total_collar+=$collerqty;
                                                    $pre_color_total_collar_order_qnty+=$color_cuff_cut;
                                                    ?>
												</td>
												<?
											}
											?>

											<td align="center"><? echo number_format($pre_color_total_collar); ?></td>
											 
												 
										</tr>
										<?
										$pre_grand_collar_ex_per+=$collar_ex_per;
										$pre_grand_tot_collar+=$pre_color_total_collar;
										$pre_grand_tot_collar_order_qty+=$pre_color_total_collar_order_qnty;
									
								}
							}
							?>
                        
                        <tr>
                            <td>Size Total</td>
								<?
                               // foreach($pre_size_total_arr  as $size_qty)
                               // {
                                	foreach($collar_cuff_size_arr[$body_type][$body_val] as $size_number_id)
									{
										$size_qty=$pre_size_total_arr[$size_number_id];
										?>
										<td style="border:1px solid black;  text-align:center"><? echo number_format($size_qty); ?></td>
										<?
									}

                               // }
                                ?>
                            <td style="border:1px solid black; text-align:center"><? echo number_format($pre_grand_tot_collar); ?></td>
                            <!-- <td align="center" style="border:1px solid black"><? echo number_format((($pre_grand_tot_collar-$pre_grand_tot_collar_order_qty)/$pre_grand_tot_collar_order_qty)*100,2); ?></td> -->
							 
                        </tr>
					</table>
                </div>
                <?
            }
        }
			?>
			 <br>
        <?
        	$sample_stripe_data=sql_select("SELECT a.body_part_id, b.contrast, b.color_id, b.grey_fab_qnty,c.id as strip_mst_id, c.stripe_color, c.measurement, c.uom, c.fabreq, c.yarn_dyed FROM sample_development_fabric_acc a join sample_development_rf_color b on a.id=b.dtls_id join wo_sample_stripe_color c on a.id=c.sample_fab_dtls_id and b.color_id=c.color_number_id where a.status_active=1 and a.is_deleted=0 and a.color_type_id in (2,3,4,6,31,32,33,34) and a.form_type=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.sample_mst_id=$update_id");
        	
        	foreach ($sample_stripe_data as $row) {
        		$key=$row[csf('body_part_id')].'*'.$row[csf('color_id')];
        		$sample_stripe_arr[$key]['body_part_id'] = $row[csf('body_part_id')];
        		$sample_stripe_arr[$key]['fabric_color'] = $row[csf('color_id')];
        		$sample_stripe_arr[$key]['fabric_qty'] = $row[csf('grey_fab_qnty')];
				
        		$sample_stripe_arr[$key]['stripe_color'][$row[csf('strip_mst_id')]][$row[csf('stripe_color')]]['color'] = $row[csf('stripe_color')];
        		$sample_stripe_arr[$key]['stripe_color'][$row[csf('strip_mst_id')]][$row[csf('stripe_color')]]['measurement'] = $row[csf('measurement')];
        		$sample_stripe_arr[$key]['stripe_color'][$row[csf('strip_mst_id')]][$row[csf('stripe_color')]]['uom'] = $row[csf('uom')];
        		$sample_stripe_arr[$key]['stripe_color'][$row[csf('strip_mst_id')]][$row[csf('stripe_color')]]['qty'] = $row[csf('fabreq')];
        		$sample_stripe_arr[$key]['stripe_color'][$row[csf('strip_mst_id')]][$row[csf('stripe_color')]]['yarn_dyed'] = $row[csf('yarn_dyed')];
        		
				$stripe_color_summ[$row[csf('stripe_color')]] += $row[csf('fabreq')];
        	}
        ?>
        <div style="width:1000px; ">
	        <table align="left" cellspacing="0" border="1" style="width:800px;float: left; right; margin-top: 5px;font-size:14px" class="rpt_table" rules="all">
	        	<thead>
	        		<tr>
	        			<th colspan="9">Stripe Details</th>
	        		</tr>
	        		<tr>
	        			<th width="30">SL</th>
	        			<th width="100">Body Part</th>
	        			<th width="60">Fabric Color</th>
	        			<th width="60">Fabric Qty(KG)</th>
	        			<th width="60">Stripe Color</th>
	        			<th width="60">Stripe Measurement</th>
	        			<th width="60">Stripe Uom</th>
	        			<th width="60">Qty.(KG)</th>
	        			<th width="60">Y/D Req.</th>
	        		</tr>
	        	</thead>
	        	<tbody>
	        		<? $sl=1;
	        		foreach ($sample_stripe_arr as $sdata) {
	        			$rowspan = count($sdata['stripe_color']);
	        			$i=1;
	        			foreach ($sdata['stripe_color'] as $stripe_mst) {
							foreach ($stripe_mst as $stripe_data) {
	        				if($i==1){
	        					$total_fabric += $sdata['fabric_qty'];
	        					$total_stripe_fabric += $stripe_data['qty'];
	        				?>
	        				<tr>
			        			<td rowspan="<?=$rowspan?>"><?= $sl; ?></td>
			        			<td rowspan="<?=$rowspan?>"><?= $body_part[$sdata['body_part_id']]; ?></td>
			        			<td rowspan="<?=$rowspan?>"><?= $color_library[$sdata['fabric_color']]; ?></td>
			        			<td align="right" rowspan="<?=$rowspan?>"><?= $sdata['fabric_qty']; ?></td>
			        			<td><?= $color_library[$stripe_data['color']]; ?></td>
			        			<td align="right"><?= $stripe_data['measurement']; ?></td>
			        			<td><?= $unit_of_measurement[$stripe_data['uom']]; ?></td>
			        			<td align="right"><?= $stripe_data['qty']; ?></td>
			        			<td><?= $yes_no[$stripe_data['yarn_dyed']]; ?></td>
			        		</tr>
	        				<?
	        					$i++;
	        				}
	        				else{
	        					$total_stripe_fabric += $stripe_data['qty'];
	        					?>
	        						<tr>
	        							<td><?= $color_library[$stripe_data['color']]; ?></td>
					        			<td align="right"><?= $stripe_data['measurement']; ?></td>
					        			<td><?= $unit_of_measurement[$stripe_data['uom']]; ?></td>
					        			<td align="right"><?= $stripe_data['qty']; ?></td>
					        			<td><?= $yes_no[$stripe_data['yarn_dyed']]; ?></td>
	        						</tr>
	        					<?
	        				}
	        			}
	        			$sl++;
						}
	        		} ?>
	        	</tbody>
	        	<tfoot>
	        		<tr>
	        			<th colspan="3">Total</th>
	        			<th align="right"><?= $total_fabric ?></th>
	        			<th></th>
	        			<th></th>
	        			<th></th>
	        			<th align="right"><?= $total_stripe_fabric ?></th>
	        			<th></th>
	        		</tr>
	        	</tfoot>
	        </table>
	        <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:180px; margin-left: 2px; float: right; right; margin-top: 5px;font-size:14px" rules="all">
		        <thead>
		        	<tr>
		        		<th colspan="3">Stripe Color wise Summary</th>
		        	</tr>
		        	<tr>
		        		<th>SL</th>
		        		<th>Stripe Color</th>
		        		<th>Qty.(KG)</th>
		        	</tr>
		        </thead>
		        <tbody>
		        	<?
		        	$sl=1;
		        	foreach ($stripe_color_summ as $color_id => $value) {
		        	 	$total_fabric_qty+= $value;
		        	?>
		        	<tr>
		        		<td><?= $sl ?></td>
		        		<td><?= $color_library[$color_id]; ?></td>
		        		<td><?= $value ?></td>
		        	</tr>
		        	<? $sl++;
		        	} ?>
		        </tbody>
		        <tfoot>
		        	<tr>
		        		<th colspan="2">Total</th>
		        		<th><?= $total_fabric_qty; ?></th>
		        	</tr>
		        </tfoot>
	        </table>
        </div>
                <br>
                 <br>

               	<table  style="margin-top: 10px;" class="rpt_table" width="600" align="left" border="1" cellpadding="0" cellspacing="0" rules="all">
                	<thead>
                    	<tr>
                        	<th align="left" width="40">Sl</th>
                        	<th align="left" >Special Instruction</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$data_array=sql_select("select id, terms from  wo_booking_terms_condition where entry_form=140 and booking_no='$data[2]'");
					if ( count($data_array)>0)
					{
						$l=1;
						foreach( $data_array as $key=>$row )
						{

							?>
                            	<tr  align="">
                                    <td> <? echo $l;?> </td>
                                    <td> <? echo $row[csf("terms")]; ?> </td>
                                </tr>
                            <?
                            $l++;
						}
					}

					?>
                </tbody>
            </table>
             </br>


             </td>
        </tr>
         <tr> <td colspan="6">&nbsp;</td></tr>

        <tr>
        	<td width="810" align="left" valign="top" colspan="2" >
            	<table align="left" cellspacing="0" width="810" class="rpt_table" >
                	<tr>
                    	<td colspan="6">
							<?

								$user_id=$_SESSION['logic_erp']['user_id'];
								$user_arr=return_library_array( "select id, USER_NAME from user_passwd where id=$user_id", "id", "USER_NAME");
								$prepared_by = $user_arr[$user_id];
                              	//echo signature_table(134, $data[0], "810px");
							  	echo signature_table(134, $data[0], "1080px",$cbo_template_id,$padding_top = 70,$prepared_by);
                            ?>
                        </td>

                    </tr>

                </table>
            </td>
        </tr>
        </table>

    </div>
   <script type="text/javascript" src="../../js/jquery.js"></script>
   <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
   <script>

	function fnc_generate_Barcodes( valuess, img_id )
	{
		var value = valuess;//$("#barcodeValue").val();
		var btype = 'code39';//$("input[name=btype]:checked").val();
		var renderer ='bmp';// $("input[name=renderer]:checked").val();
		var settings = {
		  output:renderer,
		  bgColor: '#FFFFFF',
		  color: '#000000',
		  barWidth: 1,
		  barHeight: 60,
		  moduleSize:5,
		  posX: 10,
		  posY: 20,
		  addQuietZone: 1
		};
		$("#"+img_id).html('11');
		 value = {code:value, rect: false};
		$("#"+img_id).show().barcode(value, btype, settings);
	}
   </script>
   <script type="text/javascript">
   	fnc_generate_Barcodes('<? echo $barcode_no; ?>','barcode_img_id');
   </script>

 <?
 exit();
}
if($action=="sample_requisition_print1")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$cbo_template_id=$data[3];
	$path="../../";
	if(count($data)>3)
	{
		if($data[4]=='../')
		{
			$path=$data[4];
		}
	}
	if($data[2]==0)  $path='../';
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$supplier_library=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
	$company_img=sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$dealing_merchant_library=return_library_array( "select id,team_member_name from lib_mkt_team_member_info", "id", "team_member_name"  );
	$team_leader_arr=return_library_array( "select id,team_leader_name from lib_marketing_team", "id", "team_leader_name");

	$sample_library=return_library_array( "select id, sample_name from lib_sample", "id", "sample_name");
	$size_library=return_library_array( "select id, size_name from lib_size", "id", "size_name"  );
	$color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
	$season_arr=return_library_array( "select id,season_name from lib_buyer_season  where status_active =1 and is_deleted=0 ", "id", "season_name"  );
	$trims_group_lib=return_library_array( "select id,item_name from lib_item_group  where status_active =1 and is_deleted=0", "id", "item_name");
	//concate(buyer_name,'_',contact_person)
	$appDate=return_field_value("approved_date","approval_history","entry_form=25 and mst_id='$data[1]' order by id desc");
	$appBy=return_field_value("approved_by","approval_history","entry_form=25 and mst_id='$data[1]'");
	$user_arr=return_library_array( "select id, user_name from user_passwd", "id", "user_name"  );
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='sample_details_1' and file_type=1",'master_tble_id','image_location');

	$sql="select id from electronic_approval_setup where company_id=$data[0] and page_id in(411,2883,937) and is_deleted=0";
	$res_result_arr = sql_select($sql);
	$approval_arr=array();
	foreach($res_result_arr as $row){
		$approval_arr[$row["ID"]]["ID"]=$row["ID"];
	}
 ?>
	<style>
		#mstDiv {
			margin:0px auto;
			width:1130px;
		}
		#mstDiv @media print {
			thead {display: table-header-group;}
		}
		@media print{
			html>body table.rpt_table {
				margin-left:3px;
			}
		}
    </style>
	<div id="mstDiv" style="float:left;">
        <table width="1100" cellspacing="0" border="0"  style="font-family: Arial Narrow;margin-left: 3px;" >
            <tr>
                <td rowspan="4" valign="top" width="300"><img width="150" height="80" src="<?=$path;?><? echo $company_img[0][csf("image_location")]; ?>"></td>
                <td colspan="4" style="font-size:20px;"><strong><b><? echo $company_library[$data[0]]; ?></b></strong></td>
                <td width="200">
					<?
                    $nameArray_approved=sql_select( "SELECT approved_by,approved_date from wo_non_ord_samp_booking_mst a, approval_history b where a.id=b.mst_id and a.booking_no='$data[2]' and b.entry_form=9 and a.status_active =1 and a.is_deleted=0 order by b.id desc ");
                    $approved_by= $user_arr[$nameArray_approved[0][csf("approved_by")]];
                    $approved_date= change_date_format($nameArray_approved[0][csf("approved_date")]);
                    ?>
                </td>
            </tr>
            <tr>
                <td colspan="5">
					<?
                    $val=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
                    echo ($val[0][csf('plot_no')])?   $val[0][csf('plot_no')].',': "";
                    echo ($val[0][csf('level_no')])?  $val[0][csf('level_no')].',': "";
                    echo ($val[0][csf('road_no')])?   $val[0][csf('road_no')].',': "";
                    echo ($val[0][csf('block_no')])?  $val[0][csf('block_no')].',': "";
                    echo ($val[0][csf('city')])?      $val[0][csf('city')].',': "";
                    echo ($val[0][csf('zip_code')])?  $val[0][csf('zip_code')].',': "";
                    echo ($val[0][csf('province')])?  $val[0][csf('province')].',': "";
                    echo($val[0][csf('country_id')])? $country_arr[$val[0][csf('country_id')]]: "";
                    echo ($val[0][csf('email')])?    "</br>". $val[0][csf('email')].',': "</br>";
                    echo($val[0][csf('website')])?    $val[0][csf('website')]: "";

                    $sql="SELECT id, requisition_number, requisition_number_prefix_num, style_ref_no, buyer_name, season, product_dept, dealing_marchant, agent_name, buyer_ref, bh_merchant, estimated_shipdate, team_leader, season_buyer_wise, remarks, quotation_id, sample_stage_id, requisition_date, material_delivery_date,qrr_date,is_approved from sample_development_mst where  id='$data[1]' and entry_form_id=203 and  is_deleted=0  and status_active=1";
                    $dataArray=sql_select($sql);
                    $barcode_no=$dataArray[0][csf('requisition_number')];
                    $is_approved=$dataArray[0][csf('is_approved')];
                    if($dataArray[0][csf("sample_stage_id")]==1)
                    {
                        $job_lib=return_library_array( "SELECT a.id,min(b.shipment_date) as shipment_date  from wo_po_details_master  a,wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and a.company_name='$data[0]' GROUP BY a.id", "id", "shipment_date"  );
                    }
                    //$sqls="SELECT style_desc, supplier_id, revised_no, buyer_req_no, source, booking_date, attention from wo_non_ord_samp_booking_mst where booking_no='$data[2]' and is_deleted=0 and status_active=1";
                    //$dataArray_book=sql_select($sqls);
					 $sqls="SELECT style_desc, supplier_id, attention, revised_no, buyer_req_no, source, team_leader, dealing_marchant, pay_mode, booking_date from wo_non_ord_samp_booking_mst where  booking_no='$data[2]' and is_deleted=0  and status_active=1";
 					 $dataArray_book=sql_select($sqls);
					 
					$booking_no=$data[2];
                    ?>
                </td>
            </tr>
            <tr>
                <td colspan="3" style="font-size:medium"><strong style="font-size:18px"> <u>Sample Program Without Order</u></strong></td>
                <td colspan="2" width="250"><b>Approved By :<? echo $approved_by ?></b> </br><b>Approved Date :<? echo $approved_date ?></b> </td>
            </tr>
        </table>

        <table width="1100" cellspacing="0" border="0" class="rpt_table" style="font-family: Arial Narrow;margin-left: 3px;" >
        	<tr>
                <td width="130"><strong>System No.: </strong></td>
                <td width="130"><strong><?=$dataArray[0][csf("requisition_number")];?></strong></td>
                <td width="130"><strong>Booking Date:</strong></td>
                <td width="130"><?=change_date_format($dataArray_book[0][csf('booking_date')]);?></td>
                <td width="130"><strong>Sample Stage</strong></td>
                <td width="130"><?=$sample_stage[$dataArray[0][csf('sample_stage_id')]];?></td>
                <td width="130"><strong>Revise:</strong></td>
                <td><?=$dataArray_book[0][csf('revised_no')];?></td>
            </tr>
            <tr>
                <td><strong>Booking No: </strong></td>
                <td><?=$data[2];?></td>
                <td><strong>Style Ref:</strong></td>
                <td><?=$dataArray[0][csf('style_ref_no')];?></td>
                <td><strong>Style Desc./Req. No:</strong></td>
                <td><?=$dataArray_book[0][csf('style_desc')];?></td>
                <td><strong>Sample Sub Date:</strong></td>
                <td><?=change_date_format($dataArray[0][csf('material_delivery_date')]);?></td>
            </tr>
            <tr>
                <td><strong>Buyer Name: </strong></td>
                <td><?=$buyer_library[$dataArray[0][csf('buyer_name')]];?></td>
                <td><strong>Season:</strong></td>
                <td><?=$season_arr[$dataArray[0][csf('season')]];?></td>
                <td><strong>BH Merchandiser:</strong></td>
                <td><?=$dataArray[0][csf('bh_merchant')];?></td>
                <td><strong>Attention:</strong></td>
                <td style="word-wrap: break-word;word-break: break-all;" ><?=$dataArray_book[0][csf('attention')];?></td>
            </tr>
            <tr>
                <td align="left"><strong>Buyer Ref:</strong></td>
                <td><?=$dataArray[0][csf('buyer_ref')];?></td>
                <td><strong>Product Dept:</strong></td>
                <td><?=$product_dept[$dataArray[0][csf('product_dept')]];?></td>
                <td><strong>Supplier</strong></td>
                <td><?
				
				if($dataArray_book[0][csf('pay_mode')]==1 || $dataArray_book[0][csf('pay_mode')]==2){
					echo $supplier_library[$dataArray_book[0][csf('supplier_id')]];
				   }elseif($dataArray_book[0][csf('pay_mode')]==3 || $dataArray_book[0][csf('pay_mode')]==4 || $dataArray_book[0][csf('pay_mode')]==4){
					echo $company_library[$dataArray_book[0][csf('supplier_id')]];
				   }
				?></td>
                <td><strong>Est. Ship Date</strong></td>
                <td><?=change_date_format($dataArray[0][csf('estimated_shipdate')]);?></td>
            </tr>
            <tr>
                <td><strong>Team Leader</strong></td>
                <td><?=$team_leader_arr[$dataArray[0][csf('team_leader')]];?></td>
                <td><strong>Dealing Merchandiser:</strong></td>
                <td><?=$dealing_merchant_library[$dataArray[0][csf('dealing_marchant')]];?></td>
                <td><strong>Qrr Date:</strong></td>
                <td colspan="3" style="word-wrap: break-word;word-break: break-all;"><?=$dataArray[0][csf('qrr_date')];?></td>
            </tr>
             <tr>
                
                <td><strong>Remarks/Desc/M.List:</strong></td>
                <td colspan="3" style="word-wrap: break-word;word-break: break-all;"><?=$dataArray[0][csf('remarks')];?></td>
            </tr>
            
        </table>
        <br>
		<?
       // $sql_fab="SELECT a.sample_name, a.process_loss_percent, a.gmts_item_id, b.color_id, b.contrast, b.qnty, a.delivery_date, a.fabric_description, a.body_part_id, a.fabric_source, a.remarks_ra, a.gsm, a.dia, a.color_type_id, a.width_dia_id, a.uom_id, b.process_loss_percent, b.grey_fab_qnty, b.fabric_color from sample_development_fabric_acc a,sample_development_rf_color b, wo_non_ord_samp_booking_dtls c where a.id=b.dtls_id and a.sample_mst_id=b.mst_id and a.id=c.dtls_id and c.fabric_color=b.fabric_color and c.gmts_color=b.color_id and c.dtls_id=b.dtls_id  and b.grey_fab_qnty=c.grey_fabric and a.form_type=1 and b.qnty>0 and c.grey_fabric>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and a.sample_mst_id='$data[1]' and b.mst_id='$data[1]'  ";
        //echo $sql_fab; die;

         $color_sql="SELECT b.color_id ,b.process_loss_percent ,b.fabric_color,b.contrast,b.mst_id,b.dtls_id, b.qnty from  sample_development_rf_color b where b.status_active=1 and b.is_deleted=0 and b.qnty>0 and b.mst_id='$data[1]' ";
		 $color_res=sql_select($color_sql);
		 $color_rf_data=array();
		 foreach ($color_res as $val) {
		 	$color_rf_data[$val[csf('dtls_id')]][$val[csf('color_id')]][$val[csf('fabric_color')]]['process_loss_percent']=$val[csf('process_loss_percent')];
		 	$color_rf_data[$val[csf('dtls_id')]][$val[csf('color_id')]][$val[csf('fabric_color')]]['contrast']=$val[csf('contrast')];
		 	$color_rf_data[$val[csf('dtls_id')]][$val[csf('color_id')]][$val[csf('fabric_color')]]['qnty']=$val[csf('qnty')];
		 }

		 $sql_fab="SELECT a.id ,a.sample_name,a.yarn_dtls, a.gmts_item_id, c.gmts_color as color_id,   a.delivery_date, a.fabric_description, a.body_part_id, a.fabric_source, a.remarks_ra, a.gsm, a.dia, a.color_type_id, a.width_dia_id, a.uom_id, c.grey_fabric as grey_fab_qnty, c.fabric_color,c.dtls_id,c.finish_fabric as qnty from sample_development_fabric_acc a, wo_non_ord_samp_booking_dtls c where  a.id=c.dtls_id and  a.sample_mst_id=c.style_id  and a.form_type=1  and c.grey_fabric>0 and a.status_active=1 and a.is_deleted=0  and c.status_active=1 and c.is_deleted=0  and a.sample_mst_id='$data[1]' ";

        $sql_fab_arr=array();
        foreach(sql_select($sql_fab) as $vals)
        {
        	$contrast=$color_rf_data[$vals[csf('dtls_id')]][$vals[csf('color_id')]][$vals[csf('fabric_color')]]['contrast'];
			 $process_loss_percent=$color_rf_data[$vals[csf('dtls_id')]][$vals[csf('color_id')]][$vals[csf('fabric_color')]]['process_loss_percent'];

            $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$contrast]["qnty"]+=$vals[csf("qnty")];
            $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$contrast]["process_loss_percent"]+=$process_loss_percent;

            $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$contrast]["grey_fab_qnty"]+=$vals[csf("grey_fab_qnty")];

            $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$contrast]["delivery_date"] =change_date_format($vals[csf("delivery_date")]);

            $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$contrast]["fabric_source"] =$vals[csf("fabric_source")];

            $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$contrast]["uom_id"] =$vals[csf("uom_id")];
            $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$contrast]["dia"] =$vals[csf("dia")];

            $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$contrast]["width_dia_id"] =$vals[csf("width_dia_id")];

            $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$contrast]["remarks"] =$vals[csf("remarks_ra")];
            $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$contrast]["color_type_id"] =$vals[csf("color_type_id")];
			$sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$contrast]["yarn_dtls"] =$vals[csf("yarn_dtls")];
			
			$sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$contrast]["dtls_id"] .=$vals[csf("id")].',';
			
			$fab_idArr[$vals[csf("id")]]=$vals[csf("id")];
        }
        $sample_item_wise_span=array();
        /*echo '<pre>';
        print_r($sql_fab_arr); die;*/

        foreach($sql_fab_arr as $sample_type=>$colorType_data)
        {
            foreach($colorType_data as $colorType=>$gmts_color_data)
            {

                foreach($gmts_color_data as $gmts_color_id=>$body_part_data)
                {
                	$sample_span=0;
                    foreach($body_part_data as $body_part_id=>$fab_desc_data)
                    {
                        //$kk=0;
                        foreach($fab_desc_data as $fab_id=>$gsm_data)
                        {
                            foreach($gsm_data as $gsm_id=>$dia_data)
                            {
                                foreach($dia_data as $dia_id=>$color_data)
                                {
                                    foreach($color_data as $contrast_id=>$row)
                                    {
                                        $sample_span++;
                                        //$kk++;
                                    }
                                }
                            }
                        }
                        //$bodypart_item_wise_span[$sample_type][$gmts_item_id][$body_part_id]=$kk;
                    }
                    $sample_item_wise_span[$sample_type][$gmts_color_id]=$sample_span;
                }
            }
        }
	/*        echo "<pre>";
        print_r($sample_item_wise_span);die;*/

        $sql_sample_dtls= "SELECT a.sample_name, a.article_no, a.sample_color from sample_development_dtls a, lib_color b where a.status_active=1 and a.is_deleted=0 and a.entry_form_id=203  and sample_mst_id='$data[1]' and b.status_active=1 and b.id=a.sample_color  group by a.sample_name, a.article_no, a.sample_color";
        foreach(sql_select($sql_sample_dtls) as $key=>$value)
        {
            if($sample_wise_article_no[$value[csf("sample_name")]][$value[csf("sample_color")]]=="")
            {
                $sample_wise_article_no[$value[csf("sample_name")]][$value[csf("sample_color")]]=$value[csf("article_no")];
            }
            else
            {
                if(!in_array($value[csf("article_no")], $sample_wise_article_no))
                {
                    $sample_wise_article_no[$value[csf("sample_name")]][$value[csf("sample_color")]].= ', '.$value[csf("article_no")];
                }
            }
        }
        // echo "<pre>"; print_r($sample_wise_article_no);die;

        ?>
        <table class="rpt_table" style="margin:5px;" width="1100"  border="1" cellpadding="0" cellspacing="0" rules="all">
            <thead>
                <tr>
                    <th colspan="21">Required Fabric</th>
                </tr>
                <tr>
                    <th width="30">SL</th>
                    <th width="90">ALT / [C/W]</th>
                    <th width="110">Sample Type</th>
                    <th width="80">Gmt Color</th>
                    <th width="80">Fab. Deli Date</th>
                    <th width="100">Body Part</th>
                    <th width="150">Fabric Desc & Composition</th>
                    <th width="80">Color Type</th>
                    <th width="80">Fab.Color</th>
                    <th width="40">Item Size</th>
                    <th width="55">GSM</th>
                    <th width="55">Dia</th>
                    <th width="60">Width/Dia</th>
                    <th width="40">UOM</th>
                    <th width="60">Grey Qty</th>
                    <th width="40">P. Loss</th>
                    <th width="80">Fin Fab Qty</th>
                    <th width="80">Fabric Source</th>
                    <th width="100">Yarn Dtls</th>
                    <th width="80">Image</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                <?
				  $nameArray_imge =sql_select("SELECT b.id,a.image_location FROM common_photo_library a,sample_development_fabric_acc b where  b.id= nvl(a.master_tble_id,0) and a.file_type=1 and a.form_name='required_fabric_1' and b.id in(".implode(",",$fab_idArr).") ");
				foreach($nameArray_imge as $row)
                {
					$fab_imgArr[$row[csf("id")]]=$row[csf("image_location")];
				}
				 
				  
                $p=1; $total_finish=0; $total_grey=0; $total_process=0;
                foreach($sql_fab_arr as $sample_type=>$colorType_data)
                {
                    foreach($colorType_data as $colorType=>$gmts_color_data)
                    {
                        foreach($gmts_color_data as $gmts_color_id=>$body_part_data)
                        {
                            $nn=0;
                            foreach($body_part_data as $body_part_id=>$fab_desc_data)
                            {
                                foreach($fab_desc_data as $fab_id=>$gsm_data)
                                {
                                    foreach($gsm_data as $gsm_id=>$dia_data)
                                    {
                                        foreach($dia_data as $dia_id=>$color_data)
                                        {
                                            //$i=0;
                                            foreach($color_data as $contrast_id=>$value)
                                            {
                                                
												$dtls_id=rtrim($value["dtls_id"],',');
												$dtls_Arr=array_unique(explode(',',$dtls_id));
												
												?>
                                                <tr>
                                                    <td  align="center" style="word-wrap: break-word;word-break: break-all;"><? echo $p;$p++;?></td>
                                                    <?
                                                   /* if($nn==0)
                                                    {*/
                                                        $rowspan=0;
                                                        //$rowspan=$sample_item_wise_span[$sample_type][$gmts_color_id];
                                                        ?>
                                                        <td rowspan="<?=$rowspan;?>" align="center"><?=ltrim($sample_wise_article_no[$sample_type][$gmts_color_id], ',');?></td>
                                                        <td rowspan="<?=$rowspan;?>" align="center"><p><?=$sample_library[$sample_type];?></p></td>
                                                        <td align="center" rowspan="<?=$rowspan;?>"><p><?=$color_library[$gmts_color_id];?> </p></td>
                                                        <?
                                                        $nn++;
                                                    /*}*/
                                                    ?>
                                                    <td align="center"><?=$value["delivery_date"];?> </td>
                                                    <td align="center" style="word-break:break-all"><p><?=$body_part[$body_part_id];?></p></td>
                                                    <td align="center" style="word-break:break-all"><p><?=$fab_id;?></p></td>
                                                    <td align="center" style="word-break:break-all"><?=$color_type[$colorType];?></td>
                                                    <td align="center" style="word-break:break-all"><?=$contrast_id;?></td>
                                                    <td align="center" style="word-break:break-all"><?=$value["item_size"];?></td>
                                                    <td align="center" style="word-break:break-all"><?=$gsm_id;?></td>
                                                    <td align="center" style="word-break:break-all"><?=$value["dia"];?></td>
                                                    <td align="center" style="word-break:break-all"><?=$fabric_typee[$value["width_dia_id"]];?></td>
                                                    <td align="center" style="word-break:break-all"><?=$unit_of_measurement[$value["uom_id"]];?></td>
                                                    <td align="right"><?=number_format($value["grey_fab_qnty"], 2);?></td>
                                                    <td align="right"><?=$value["process_loss_percent"];?></td>
                                                    <td align="right"><?=number_format($value["qnty"],2);?></td>
                                                    <td style="word-break:break-all"><?=$fabric_source[$value["fabric_source"]];?></td>
                                                     <td style="word-break:break-all"><?=$value["yarn_dtls"];?></td>
                                                     <td style="word-break:break-all"><?  $path='../../';
													 	foreach($dtls_Arr as $img)
														{
														 // echo $fab_imgArr[$img].'D';
														  if($fab_imgArr[$img]!='')
														  {
														?>
                                                        <b> <img src="<? echo $path.$fab_imgArr[$img]; ?>" width="45" height="auto" border="1" /></b>														<?  
														   }
														  }
													 ?></td>
                                                    <td style="word-break:break-all"><?=$value["remarks"];?></td>
                                                    
                                                </tr>
                                                <?
                                                //$i++;
                                                $total_finish +=$value["qnty"];
                                                $total_grey +=$value["grey_fab_qnty"];
                                                $total_process +=$value["process_loss_percent"];
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                ?>
                <tr>
                    <th colspan="14" align="right"><b>Total</b></th>
                    <th width="80" align="right"><?=number_format($total_grey, 2);?></th>
                    <th width="40" align="right">&nbsp;</th>
                    <th width="60" align="right"><?=number_format($total_finish, 2);?></th>
                    <th colspan="4">&nbsp;</th>
                </tr>
            </tbody>
        </table>
        <div> &nbsp; </div> <br/>
        <?
        $sample_color_arr=return_library_array( "select id, sample_color from sample_development_dtls", "id", "sample_color");
        $sql_qry="SELECT id, sample_mst_id, sample_name, gmts_item_id, smv, article_no, sample_color, sample_prod_qty, submission_qty, delv_start_date, delv_end_date, sample_charge, sample_curency, sent_to_buyer_date, comments from sample_development_dtls where status_active=1 and is_deleted=0 and entry_form_id=203 and sample_mst_id='$data[1]' order by id asc";

        $sql_qry_color="SELECT a.id, a.sample_mst_id, a.sample_name, a.gmts_item_id, a.smv, a.article_no, a.sample_color, a.sample_prod_qty, a.submission_qty, a.delv_start_date, a.delv_end_date, a.sample_charge, a.sample_curency, a.sent_to_buyer_date, a.comments, c.dtls_id, c.size_id, c.bh_qty, c.self_qty, c.test_qty, c.plan_qty, c.dyeing_qty,c.samp_dept_qty,c.others_qty,c.test_fit_qty from sample_development_dtls a, sample_development_size c where a.id=c.dtls_id and c.mst_id=a.sample_mst_id  and a.status_active =1 and a.is_deleted=0 and c.status_active =1 and c.is_deleted=0 and a.entry_form_id=203 and a.sample_mst_id='$data[1]' order by a.id asc";
        $size_type_arr=array(1=>"BH Qty",2=>"Self Qty",3=>"Test qty",4=>"Plan Qty",5=>"Dyeing Qty",6=>"Samp. Dept Qty",7=>"Others Qty",8=>"Test Fit Qty");
        $color_size_arr=array();
        foreach(sql_select($sql_qry_color) as $vals)
        {
            if($vals[csf("bh_qty")]>0)
            {
                $color_size_arr[1][$vals[csf("size_id")]]='Bh Qty';
                $bh_qty=$vals[csf("bh_qty")];
                $color_size_dtls_qty_arr[1][$vals[csf("id")]][$vals[csf("size_id")]]=$bh_qty;
            }
            if($vals[csf("self_qty")]>0)
            {
                $color_size_arr[2][$vals[csf("size_id")]]='self qty';
                $color_size_dtls_qty_arr[2][$vals[csf("id")]][$vals[csf("size_id")]]=$vals[csf("self_qty")];
            }
			if($vals[csf("samp_dept_qty")]>0)
            {
                $color_size_arr[6][$vals[csf("size_id")]]='samp. dept qty';
                $color_size_dtls_qty_arr[6][$vals[csf("id")]][$vals[csf("size_id")]]=$vals[csf("samp_dept_qty")];
            }
			if($vals[csf("others_qty")]>0)
            {
                $color_size_arr[7][$vals[csf("size_id")]]='others qty';
                $color_size_dtls_qty_arr[7][$vals[csf("id")]][$vals[csf("size_id")]]=$vals[csf("others_qty")];
            }
            if($vals[csf("test_qty")]>0)
            {
                $color_size_arr[3][$vals[csf("size_id")]]='test qty';
                $color_size_dtls_qty_arr[3][$vals[csf("id")]][$vals[csf("size_id")]]=$vals[csf("test_qty")];
            }
            if($vals[csf("plan_qty")]>0)
            {
                $color_size_arr[4][$vals[csf("size_id")]]='plan qty';
                $color_size_dtls_qty_arr[4][$vals[csf("id")]][$vals[csf("size_id")]]=$vals[csf("plan_qty")];
            }
            if($vals[csf("dyeing_qty")]>0)
            {
                $color_size_arr[5][$vals[csf("size_id")]]='Dyeing qty';
                $color_size_dtls_qty_arr[5][$vals[csf("id")]][$vals[csf("size_id")]]=$vals[csf("dyeing_qty")];
            }
			if($vals[csf("test_fit_qty")]>0)
            {
                $color_size_arr[8][$vals[csf("size_id")]]='test fit qty';
                $color_size_dtls_qty_arr[8][$vals[csf("id")]][$vals[csf("size_id")]]=$vals[csf("test_fit_qty")];
            }
			$sampIdArr[$vals[csf("id")]]=$vals[csf("id")];
        }
        $tot_row=count($color_size_arr);
        $result=sql_select($sql_qry);
        ?>
        <table align="left" style="margin:5px;" cellspacing="0" border="1" width="1100" class="rpt_table" rules="all">
            <thead>
                <tr>
                    <td width="150" colspan="<? echo 12+$tot_row;?>" align="center"><strong>Sample Details</strong></td>
                </tr>
                <tr>
                    <th width="30" rowspan="2">Sl</th>
                    <th width="100" rowspan="2">Sample Name</th>
                    <th width="120" rowspan="2">Garment Item</th>
                    <th width="70" rowspan="2">Sample Delv.  Date</th>
                    <th width="55" rowspan="2">ALT / [C/W]</th>
                    <th width="70" rowspan="2">Color</th>
                        <?
                        $tot_row_td=0;
                        foreach($color_size_arr as $type_id=>$val)
                        {
                            ?>
                            <th width="45" align="center" colspan="<?=count($val);?>"><?=$size_type_arr[$type_id];?></th>
                            <?
                        }
                        ?>
                    <th rowspan="2" width="55">Total</th>
                    <th rowspan="2" width="55">Submn Qty</th>
                    <th rowspan="2"  width="70">Buyer Submisstion Date</th>
                    <th rowspan="2">Image</th>
                    <th rowspan="2">Remarks</th>
                </tr>
                <tr>
                    <?
                    foreach($color_size_arr as $type_id=>$data_size)
                    {
                        foreach($data_size as $size_id=>$data_val)
                        {
                            $tot_row_td++;
                            ?>
                            <th width="40" align="center"><?=$size_library[$size_id];?></th>
                            <?
                        }
                    }
                    ?>
                </tr>
            </thead>
            <tbody>
                <?
				 $sam_nameArray_imge =sql_select("SELECT b.id,a.image_location FROM common_photo_library a,sample_development_dtls b where  b.id= nvl(a.master_tble_id,0) and a.file_type=1 and a.form_name='sample_details_1' and b.id in(".implode(",",$sampIdArr).") ");
				 
				foreach($sam_nameArray_imge as $row)
                {
					$samp_imgArr[$row[csf("id")]]=$row[csf("image_location")];
				}
				
                $i=1; $k=0; $gr_tot_sum=0; $gr_sub_sum=0;
                foreach($result as $row)
                {
                    $dtls_ids=$row[csf('id')];
                    $prod_sum=$prod_sum+$row[csf('sample_prod_qty')];
                    $sub_sum=$sub_sum+$row[csf('submission_qty')];
                    $k++;
                    ?>
                    <tr>
                        <td align="center"><?=$k;?></td>
                        <td align="left"><?=$sample_library[$row[csf('sample_name')]];?></td>
                        <td align="left"><?=$garments_item[$row[csf('gmts_item_id')]];?></td>
                        <td align="left"><?=change_date_format($row[csf('delv_end_date')]);?></td>
                        <td align="left"><?=$row[csf('article_no')];?></td>
                        <td align="left"><?=$color_library[$row[csf('sample_color')]];?></td>
                        <?
                        $total_sizes_qty=0;  $total_sizes_qty_subm=0;
                        foreach($color_size_arr as $type_id=>$data_size)
                        {
                            foreach($data_size as $size_id=>$data_val)
                            {
                                $size_qty=$color_size_dtls_qty_arr[$type_id][$dtls_ids][$size_id];
                                ?>
                                <td align="right"><?=$size_qty;?></td>
                                <?
                                if($type_id==1)
                                {
                                $total_sizes_qty_subm+=$size_qty;
                                }
                                $total_sizes_qty+=$size_qty;
                            }
                        }
						$path="../../";
                        ?>
                        <td align="right"><?=number_format($total_sizes_qty,2);?></td>
                        <td align="right"><?=number_format($total_sizes_qty_subm,2);?></td>
                        <td align="left"><?=change_date_format($row[csf('sent_to_buyer_date')]);?> </td>
                        <td align="left"> <b> 
                        <?
                        if($samp_imgArr[$row[csf('id')]]!='')
						{
						?>
                        <img src="<? echo $path.$samp_imgArr[$row[csf('id')]]; ?>" width="80" height="auto" border="1" />
                        
                        <?
						}
						?>
                        
                        </b>   </td>
                        
                        <td align="left"><?=$row[csf('comments')];?> </td>
                    </tr>
                    <?
                    $gr_tot_sum+=$total_sizes_qty;
                    $gr_sub_sum+=$total_sizes_qty_subm;
                }
                ?>
                <tr>
                    <td colspan="<?=6 + $tot_row_td;?>" align="right"><b>Total</b></td>
                    <td align="right"><b><?=number_format($gr_tot_sum,2);?> </b></td>
                    <td align="right"><b><?=number_format($gr_sub_sum,2);?> </b></td>
                    <td colspan="3">&nbsp;</td>
                </tr>
            </tbody>
        </table>
       <div> &nbsp; </div> <br/>

        <table align="left" style="margin:5px;" cellspacing="0" border="1" width="1100" class="rpt_table" rules="all">
            <thead>
                <tr>
                    <td colspan="10" align="center"><strong>Required Accessories</strong></td>
                </tr>
                <tr>
                    <th width="30">Sl</th>
                    <th width="100">Sample Name</th>
                    <th width="120">Garment Item</th>
                    <th width="100">Trims Group</th>
                    <th width="100">Description</th>
                    <th width="100">Supplier</th>
                    <th width="100">Brand/Supp.Ref</th>
                    <th width="30">UOM</th>
                    <th width="30">Req/Dzn</th>
                    <th width="30">Req/Qty</th>
                    <th width="80">Acc.Sour.</th>
                    <th width="100">Acc Delivery Date</th>
                    <th>Remarks </th>
                </tr>
            </thead>
            <tbody>
				<?
                $sql_qryA="SELECT id,sample_mst_id,sample_name_ra,gmts_item_id_ra,trims_group_ra,description_ra,brand_ref_ra,uom_id_ra,req_dzn_ra,req_qty_ra,remarks_ra,delivery_date,supplier_id,fabric_source from sample_development_fabric_acc where status_active =1 and is_deleted=0 and form_type=2 and sample_mst_id='$data[1]' order by id asc";

                $resultA=sql_select($sql_qryA);
                $i=1;$k=0; $req_dzn_ra=0; $req_qty_ra=0;
                foreach($resultA as $rowA)
                {
					$req_dzn_ra=$req_dzn_ra+$rowA[csf('req_dzn_ra')];
					$req_qty_ra=$req_qty_ra+$rowA[csf('req_qty_ra')];
					$k++;
					?>
					<tr>
                        <td align="center"><? echo $k;?></td>
                        <td align="left"><? echo $sample_library[$rowA[csf('sample_name_ra')]];?></td>
                        <td align="left"><? echo $garments_item[$rowA[csf('gmts_item_id_ra')]];?></td>
                        <td align="left"><? echo $trims_group_lib[$rowA[csf('trims_group_ra')]];?></td>
                        <td align="left"><? echo $rowA[csf('description_ra')];?></td>
                        <td align="left"><? echo $supplier_library[$rowA[csf('supplier_id')]];?></td>
                        <td align="left"><? echo $rowA[csf('brand_ref_ra')];?></td>
                        <td align="center"><? echo $unit_of_measurement[$rowA[csf('uom_id_ra')]];?></td>
                        <td align="right"><? echo number_format($rowA[csf('req_dzn_ra')],2);?></td>
                        <td align="right"><? echo number_format($rowA[csf('req_qty_ra')],2);?></td>
                        <td align="left"><? echo $fabric_source[$rowA[csf('fabric_source')]];?></td>
                        <td align="left"><? echo change_date_format($rowA[csf('delivery_date')]);?></td>
                        <td align="left"><? echo $rowA[csf('remarks_ra')];?></td>
					</tr>
					<?
                }
                ?>
                <tr>
                    <td colspan="8" align="center"><b>Total </b></td>
                    <td align="right"><b><? echo number_format($req_qty_ra,2);?> </b></td>
                    <td>&nbsp;</td>
                </tr>
            </tbody>
        </table>
        <div> &nbsp; </div> <br/>
        <table align="left" style="margin:5px;" cellspacing="0" border="1" width="1100" class="rpt_table" rules="all">
            <thead>
                <tr>
                	<td colspan="6" align="center"><strong>Required Emebellishment</strong></td>
                </tr>
                <tr>
                    <th width="30">Sl</th>
                    <th width="100">Sample Name</th>
                    <th width="110">Garment Item</th>
                    <th width="110">Body Part</th>
                    <th width="100">Supplier</th>
                    <th width="60">Name</th>
                    <th width="70">Type</th>
                    <th width="100">Emb.Del.Date</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
				<?
                $sql_qry="SELECT id,sample_mst_id,sample_name_re,gmts_item_id_re,name_re,type_re,remarks_re,body_part_id,delivery_date,supplier_id from sample_development_fabric_acc where sample_mst_id='$data[1]' and form_type=3 and is_deleted=0  and status_active=1 order by id asc";

                $result=sql_select($sql_qry); $k=0;
                $type_array=array(1=>$emblishment_print_type,2=>$emblishment_embroy_type,3=>$emblishment_wash_type,4=>$emblishment_spwork_type,5=>$emblishment_gmts_type);
                foreach($result as $row)
                {
					$k++;
					?>
					<tr>
                        <td align="center"><? echo $k;?></td>
                        <td align="left"><? echo $sample_library[$row[csf('sample_name_re')]];?></td>
                        <td align="left"><? echo $garments_item[$row[csf('gmts_item_id_re')]];?></td>
                        <td align="left"><? echo $body_part[$row[csf('body_part_id')]];?></td>
                        <td align="left"><? echo $supplier_library[$row[csf('supplier_id')]];?></td>
                        <td align="left"><? echo $emblishment_name_array[$row[csf('name_re')]];?></td>
                        <td align="left">
                            <?
                            if($row[csf('name_re')]==1) echo $emblishment_print_type[$row[csf('type_re')]];
                            if($row[csf('name_re')]==2) echo $emblishment_embroy_type[$row[csf('type_re')]];
                            if($row[csf('name_re')]==3) echo $emblishment_wash_type[$row[csf('type_re')]];
                            if($row[csf('name_re')]==4) echo $emblishment_spwork_type[$row[csf('type_re')]];
                            if($row[csf('name_re')]==5) echo $emblishment_gmts_type[$row[csf('type_re')]];
                            ?>
                        </td>
                        <td align="left"><? echo change_date_format($row[csf('delivery_date')]);?></td>
                        <td align="left"><? echo $row[csf('remarks_re')];?></td>
                    </tr>
					<?
                }
                ?>
            </tbody>
        </table>
         <div> &nbsp; </div> <br/>
               	<table   style="margin:5px;" class="rpt_table" width="1100" align="left" border="1" cellpadding="0" cellspacing="0" rules="all">
                <caption> <b> Yarn Required Summary </b> </caption>
                	<thead>
                    	<tr align="center">
                        	<th align="center" width="40">Sl</th>
                        	<th align="center">Yarn Desc.</th>
                             <th align="center">Req. Qty</th> 
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count"  );
					$lib_supllier_arr=return_library_array( "select supplier_id,booking_no from wo_non_ord_samp_booking_mst where booking_no='$data[2]' and entry_form_id=140", "booking_no", "supplier_id"  );
				//	echo  "select supplier_id,booking_no from wo_non_ord_samp_booking_mst where booking_no='$data[2]' and entry_form_id=140";
					$tot_req_qty=0;//sample_development_mst
					$data_array=sql_select("select b.booking_no,b.determin_id,b.count_id,b.copm_one_id,b.percent_one,b.type_id,b.cons_qnty from  sample_development_yarn_dtls b,sample_development_fabric_acc a where a.sample_mst_id=b.mst_id and a.determination_id=b.determin_id and b.status_active=1 and a.status_active=1 and b.mst_id='$data[1]' and a.form_type=1 group by b.booking_no, b.determin_id,b.count_id,b.copm_one_id,b.percent_one,b.type_id,b.cons_qnty");
					//echo "select b.booking_no,b.determin_id,b.count_id,b.copm_one_id,b.percent_one,b.type_id,b.cons_qnty from  sample_development_yarn_dtls b,sample_development_fabric_acc a where a.sample_mst_id=b.mst_id and a.determination_id=b.determin_id and b.status_active=1 and a.status_active=1 and b.booking_no='$data[2]' and b.mst_id='$data[1]' and a.form_type=1";
				
					if ( count($data_array)>0)
					{
						$l=1;
						foreach( $data_array as $key=>$row )
						{
							$yarn_des=$lib_yarn_count[$row[csf("count_id")]].','.$composition[$row[csf("copm_one_id")]].','.$row[csf("percent_one")].'%,'.$yarn_type[$row[csf("type_id")]];
							?>
                            	<tr>
                                    <td> <? echo $l;?> </td>
                                    <td> <? echo $yarn_des; ?> </td>
                                    <td align="right"> <? echo number_format($row[csf("cons_qnty")],2); ?> </td>
                                </tr>
                            <?
                            $l++;
							$tot_req_qty+=$row[csf("cons_qnty")];
						}
					}

					?>
                    <tr>
						<th  colspan="2" align="right"><b>Total</b></th>
						<th  align="right"><? echo number_format($tot_req_qty,2);?></th>
					</tr>
                </tbody>
            </table>
            <div> &nbsp; </div>
       	 
         <?
				$yarn_sql_array=sql_select("SELECT min(a.id) as id, a.item_id, sum(a.qnty) as qnty ,min(b.supplier_id) as supplier_id,min(b.lot) as lot from inv_material_allocation_dtls a,product_details_master b where a.item_id=b.id and a.booking_no='$booking_no' and  a.status_active=1 and a.is_deleted=0 group by a.item_id order by id");
			 
				if(count($yarn_sql_array)>0)
				{
				?>
                   <table  width="1100"  style="margin:5px;" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                    <tr align="center">
                    <td colspan="7"><b>Allocated Yarn</b></td>

                    </tr>
                    <tr align="center">
                    <td>Sl</td>
                    <td>Yarn Description</td>
                    <td>Brand</td>
                    <td>Lot</td>


                    <td>Allocated Qty (Kg)</td>
                    </tr>
                    <?
					$total_allo=0;
					$item=return_library_array( "select id, product_name_details from   product_details_master",'id','product_name_details');
					$supplier=return_library_array( "select id, short_name from   lib_supplier",'id','short_name');
					$i=0;
					$total_yarn=0;
					foreach($yarn_sql_array  as $row)
                    {

						$i++;
					?>
                    <tr align="center">
                    <td><? echo $i; ?></td>
                    <td>
					<?

					echo $item[$row[csf('item_id')]];
					?>
                    </td>
                    <td>
                    <?

					echo $supplier[$row[csf('supplier_id')]];
					?>
                    </td>
                    <td>
					<?

					echo $row[csf('lot')];
					?>
                    </td>
                    <td align="right"><? echo number_format($row[csf('qnty')],4); $total_allo+= $row[csf('qnty')];?></td>
                    </tr>
                    <?
					}
					?>
                    <tr align="center">
                    <td>Total</td>
                    <td></td>


                    <td></td>
                    <td></td>
                    <td align="right"><? echo number_format($total_allo,4); ?></td>
                    </tr>
                    </table>
                    <?
				}
				?>
                <br>
                
        <table style="margin:5px;" class="rpt_table" width="600" align="left" border="1" cellpadding="0" cellspacing="0" rules="all">
            <thead>
                <tr>
                    <th width="40">Sl</th>
                    <th>Special Instruction</th>
                </tr>
            </thead>
            <tbody>
				<?
                $data_array=sql_select("select id, terms from  wo_booking_terms_condition where entry_form=140 and booking_no='$data[2]'");
                if(count($data_array)>0)
                {
					$l=1;
					foreach( $data_array as $key=>$row )
					{
						?>
						<tr>
                            <td><? echo $l;?> </td>
                            <td style="word-break:break-all"><? echo $row[csf("terms")]; ?> </td>
						</tr>
						<?
						$l++;
					}
                }
                ?>
            </tbody>
        </table>
		<br>
			<table width="780" align="center">
					<tr>
						<div style="text-align:center;font-size:xx-large; font-style:italic; margin-top:20px; color:#FF0000;">
								<?
								if(count($approval_arr)>0)
								{				
									if($is_approved == 0){echo "Draft";}else{}
								}
								?>
						</div>
					</tr>
			</table>
		<br>
        <?

		$user_id=$_SESSION['logic_erp']['user_id'];
		$user_arr=return_library_array( "select id, USER_NAME from user_passwd where id=$user_id", "id", "USER_NAME");
		$prepared_by = $user_arr[$user_id];
          //echo signature_table(134, $data[0], "810px");
		  echo signature_table(134, $data[0], "1080px",$cbo_template_id, 10,$prepared_by);
        ?>
    </div>
   <script type="text/javascript" src="../../js/jquery.js"></script>
   <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
   <script>
		function fnc_generate_Barcodes( valuess, img_id )
		{
			var value = valuess;//$("#barcodeValue").val();
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer ='bmp';// $("input[name=renderer]:checked").val();
			var settings = {
			  output:renderer,
			  bgColor: '#FFFFFF',
			  color: '#000000',
			  barWidth: 1,
			  barHeight: 60,
			  moduleSize:5,
			  posX: 10,
			  posY: 20,
			  addQuietZone: 1
			};
			$("#"+img_id).html('11');
			 value = {code:value, rect: false};
			$("#"+img_id).show().barcode(value, btype, settings);
		}
   </script>
   <script type="text/javascript">
   	fnc_generate_Barcodes('<? echo $barcode_no; ?>','barcode_img_id');
   </script>
	<?
    exit();
}

if($action=="sample_requisition_print6")
{
	extract($_REQUEST);
	// $data=explode('*',$data);
	// $cbo_template_id=$data[3];

	$cbo_template_id=str_replace("'","",$cbo_template_id);
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$update_id=str_replace("'","",$update_id);
	
	$path="../../";
	if(count($data)>3)
	{
		if($data[4]=='../')
		{
			$path=$data[4];
		}
	}
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$supplier_library=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
	$company_img=sql_select("select image_location  from common_photo_library  where master_tble_id='$cbo_company_name' and form_name='company_details' and is_deleted=0 and file_type=1");
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$dealing_merchant_library=return_library_array( "select id,team_member_name from lib_mkt_team_member_info", "id", "team_member_name"  );
	$team_leader_arr=return_library_array( "select id,team_leader_name from lib_marketing_team", "id", "team_leader_name"  );

	$sample_library=return_library_array( "select id, sample_name from lib_sample", "id", "sample_name");
	$brand_arr=return_library_array( "select id, brand_name from lib_brand", "id", "brand_name");
	$product_sub_dept_arr=return_library_array( "select id, sub_department_name from lib_pro_sub_deparatment", "id", "sub_department_name");
	$size_library=return_library_array( "select id, size_name from lib_size", "id", "size_name"  );
	$color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
	$season_arr=return_library_array( "select id,season_name from lib_buyer_season  where status_active =1 and is_deleted=0 ", "id", "season_name"  );
	$trims_group_lib=return_library_array( "select id,item_name from lib_item_group  where status_active =1 and is_deleted=0", "id", "item_name");
	//concate(buyer_name,'_',contact_person)
	$appDate=return_field_value("approved_date","approval_history","entry_form=25 and mst_id='$update_id' order by id desc");
	$appBy=return_field_value("approved_by","approval_history","entry_form=25 and mst_id='$update_id'");
	$user_arr=return_library_array( "select id, user_name from user_passwd", "id", "user_name"  );
	$user_library=return_library_array( "select id, user_full_name from user_passwd", "id", "user_full_name"  );
	//$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='sample_requisition_2' and file_type=1 and master_tble_id=$data[1]",'master_tble_id','image_location');
	//echo __LINE__.print_r($imge_arr); die;
	$image_location='';
	$image_location_arr = sql_select("select master_tble_id,image_location from common_photo_library where form_name='sample_requisition_2' and file_type=1 and master_tble_id='$update_id'");
	
	foreach ($image_location_arr as $row) {
		$image_locationArr[$row[csf('image_location')]]=$row[csf('image_location')];
	}
	//echo $image_location; //die;
	$sample_dtls_addi_value=sql_select("SELECT print, printseq, embro, embroseq, wash, washseq, spworks, spworksseq, gmtsdying, gmtsdyingseq, aop, aopseq, bush, bushseq, peach, peachseq, yd, ydseq from sample_details_additional_value where mst_id=$update_id");
	$print_status=2; $aop_status=2; $embro_status=2; $wash_status=2; $peach_status=2; $bush_status=2; $yd_status=2;
	foreach ($sample_dtls_addi_value as $row) {
		if($row[csf('print')]==1){
			$print_status=1;
		}
		if($row[csf('embro')]==1){
			$embro_status=1;
		}
		if($row[csf('aop')]==1){
			$aop_status=1;
		}
		if($row[csf('wash')]==1){
			$wash_status=1;
		}
		if($row[csf('peach')]==1){
			$peach_status=1;
		}
		if($row[csf('bush')]==1){
			$bush_status=1;
		}
		if($row[csf('yd')]==1){
			$yd_status=1;
		}
	}

	$sql_embellishment =sql_select("SELECT id, sample_mst_id, sample_name_re, gmts_item_id_re, name_re, type_re, remarks_re, body_part_id, delivery_date, supplier_id from sample_development_fabric_acc where sample_mst_id='$update_id' and form_type=3 and is_deleted=0  and status_active=1 and name_re in (1,2,3) order by id asc");
	$emb_print_type=''; $emb_embroy_type=''; $emb_wash_type='';
	foreach ($sql_embellishment as $row) {
		if($row[csf('name_re')]==1){
			$print_status=1;
			$emb_print_type=$emblishment_print_type[$row[csf('type_re')]];
		}
		if($row[csf('name_re')]==2){
			$embro_status=1;
			$emb_embroy_type=$emblishment_embroy_type[$row[csf('type_re')]];
		}
		if($row[csf('name_re')]==3){
			$wash_status=1;
			$emb_wash_type=$emblishment_wash_type[$row[csf('type_re')]];
		}
	}
 ?>
	<style>
		#mstDiv {
			margin:0px auto;
			width:1130px;
		}
		#mstDiv @media print {
			thead {display: table-header-group;}
		}
		@media print{
			html>body table.rpt_table {
				margin-left:12px;
			}
		}
		
		@media print {
			footer {
				position: fixed;
				bottom: 0;
				margin-top:100px;
			}

			body{
				position: absulate;
				/* height:500px; */
				top:0px;
				bottom: 100px;
			}
		}
		
    </style>
	<body>
	<div id="mstDiv" style="font-family: Arial Narrow, Arial, sans-serif;">
    	<?	ob_start();
		
		?>
        <table width="1100" cellspacing="0" border="0"   >
            <tr>
                <td rowspan="4" valign="top" width="150"><img width="150" height="80" src="<?=$path?><? echo $company_img[0][csf("image_location")]; ?>"></td>
                <td colspan="5" style="font-size:20px;text-align: center;"><strong><b><? echo $company_library[$cbo_company_name]; ?></b></strong></td>
            </tr>
            <tr>
                <td colspan="5" style="text-align: center;">
					<?
                    $val=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
                    echo ($val[0][csf('plot_no')])?   $val[0][csf('plot_no')].',': "";
                    echo ($val[0][csf('level_no')])?  $val[0][csf('level_no')].',': "";
                    echo ($val[0][csf('road_no')])?   $val[0][csf('road_no')].',': "";
                    echo ($val[0][csf('block_no')])?  $val[0][csf('block_no')].',': "";
                    echo ($val[0][csf('city')])?      $val[0][csf('city')].',': "";
                    echo ($val[0][csf('zip_code')])?  $val[0][csf('zip_code')].',': "";
                    echo ($val[0][csf('province')])?  $val[0][csf('province')].',': "";
                    echo($val[0][csf('country_id')])? $country_arr[$val[0][csf('country_id')]]: "";
                    echo ($val[0][csf('email')])?    "</br>". $val[0][csf('email')].',': "</br>";
                    echo($val[0][csf('website')])?    $val[0][csf('website')]: "";

                    $sql="SELECT id, requisition_number, requisition_number_prefix_num, style_ref_no, buyer_name, season, product_dept, dealing_marchant, agent_name, buyer_ref, bh_merchant, estimated_shipdate, team_leader, season_buyer_wise, remarks, quotation_id, sample_stage_id, requisition_date, material_delivery_date, season_year, brand_id, is_acknowledge,refusing_cause,sub_dept_id,inserted_by from sample_development_mst where  id='$update_id' and entry_form_id=203 and  is_deleted=0  and status_active=1";
                    $dataArray=sql_select($sql);
                    $refusing_cause=$dataArray[0][csf('refusing_cause')];
                    $barcode_no=$dataArray[0][csf('requisition_number')];
					$prepared_by=$user_library[$dataArray[0][csf('inserted_by')]];
                    if($dataArray[0][csf("sample_stage_id")]==1)
                    {
                        $job_lib=return_library_array( "SELECT a.id,min(b.shipment_date) as shipment_date  from wo_po_details_master  a,wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and a.company_name='$cbo_company_name' GROUP BY a.id", "id", "shipment_date"  );
                    }
					 $sqls="SELECT style_desc, supplier_id, revised_no, buyer_req_no, source, team_leader, dealing_marchant, booking_date, attention, remarks from wo_non_ord_samp_booking_mst where  booking_no='$txt_booking_no' and is_deleted=0  and status_active=1";
 					 $dataArray_book=sql_select($sqls);

 					 $sample_acc_arr=sql_select("SELECT confirm_del_end_date, refusing_cause, unacknowledge_date, insert_date from sample_requisition_acknowledge where sample_mst_id= '$update_id'");
 					 
                    ?>
                </td>
            </tr>
            <tr>
                <td colspan="5" style="font-size:medium; text-align: center;"><strong style="font-size:18px">Sample Program Without Order</strong></td>               
            </tr>
        </table>

        <table width="1100" cellspacing="0" border="1" class="rpt_table" style="font-size:14px" >
        	<tr>
        		<th align="left" width="150">S. Requisition NO</th>
        		<td colspan="2" align="left" width="150"><?=$dataArray[0][csf("requisition_number")];?></td>
        		<th align="left" width="100">Revised</th>
        		<td colspan="3" align="left" width="80"><?=$dataArray_book[0][csf('revised_no')];?></td>
        		<td rowspan="9" width="250">
        		<? if($image_location!=''){ ?>
        		<!--<img width="240" height="210" src="<? //echo $path.$image_location; ?>" >-->
        		<? } else{ ?>
        		<!--<img width="240" height="210" src="../../images/no-image.jpg" >-->
        		<? }
				 ?>
                 
                 
                        <table width="100%">
                            <tr>
                            <?
							
                            $img_counter = 0;
							$width=240;$height=210;
							$width2=100;$height2=100;
							$tot_row=count($image_locationArr);
							//echo $tot_row.'D';
                            foreach($image_locationArr as $result_imge)
                            {

                                ?>
                                <td>
                                <p> <img src="<? echo '../../../'.$result_imge; ?>" width="<? if($tot_row==1) echo $width;else echo $width2;?>" height="<? if($tot_row==1) echo $height;else echo  $height2;?>" border="2" /></p>
                                </td>
                                <?

                                $img_counter++;
                            }
                            ?>
                            </tr>
                       </table>
                        
                       
        		</td>
        		<th align="left" width="150">Sample Req. Date</th>
        		<td width="120"><?= change_date_format($dataArray[0][csf("requisition_date")]);?></td>
        	</tr>
            <tr>
        		<th align="left">S.Fab. Booking No.</th>
        		<td colspan="2"><?=$txt_booking_no;?></td>
                <th align="left">Revised Date</th>
        		<td align="left" colspan="3"><?= change_date_format($sample_acc_arr[0][csf('unacknowledge_date')]); ?></td>
        		<th align="left">Fab. Booking Date</th>
        		<td align="left"><?= change_date_format($dataArray_book[0][csf('booking_date')]);?></td>
        	</tr>
        	<tr>
        		<th align="left">Style Ref.</th>
        		<td colspan="6" align="left"><?=$dataArray[0][csf('style_ref_no')];?></td>
                <th align="left">Style Desc.</th>
        		<td><?=$dataArray_book[0][csf('style_desc')];?></td>
        	</tr>
        	<tr>
        		<th align="left">Buyer</th>
        		<td colspan="2" align="left"><?=$buyer_library[$dataArray[0][csf('buyer_name')]];?></td>
        		<th align="left">Season - S.Year</th>
        		<td colspan="3" align="left"><?=$season_arr[$dataArray[0][csf('season')]].'-'.$dataArray[0][csf('season_year')];?></td>
        		<th align="left">Fab. Delivery Date</th>
        		<td><?=change_date_format($sample_acc_arr[0][csf('confirm_del_end_date')]); ?></td>
        	</tr>
        	<tr>
        		<th align="left">Product Dept</th>
        		<td colspan="2" align="left"><?=$product_dept[$dataArray[0][csf('product_dept')]];?></td>
        		<th align="left">Brand</th>
        		<td colspan="3" align="left"><?=$brand_arr[$dataArray[0][csf('brand_id')]];?></td>
        		<th align="left">Acknowledgement St.</th>
        		<td><?=$yes_no[$dataArray[0][csf('is_acknowledge')]]; ?></td>
        	</tr>
        	<tr>
        		<th align="left">Prod. Sub Dept.</th>
        		<td colspan="2" align="left"><?=$product_sub_dept_arr[$dataArray[0][csf('sub_dept_id')]];?></td>
        		<th align="left">AOP</th>
        		<td colspan="3" align="left"><?= $yes_no[$aop_status]  ?></td>
        		<th align="left">Acknowledgement Date</th>
        		<td><?= change_date_format($sample_acc_arr[0][csf('insert_date')]); ?></td>
        	</tr>
        	<tr>
        		<th align="left">Print</th>
        		<td colspan="2" align="left"><?= $yes_no[$print_status]  ?></td>
        		<td colspan="4" align="left"><?= $emb_print_type  ?></td>
        		<th align="left">Team Leader</th>
        		<td align="left"><?=$team_leader_arr[$dataArray_book[0][csf('team_leader')]];?></td>
        	</tr>
        	<tr>
        		<th align="left">Embroidery</th>
        		<td colspan="2" align="left"><?= $yes_no[$embro_status]  ?></td>
        		<td colspan="4" align="left"><?= $emb_embroy_type  ?></td>
                <th align="left">Dealing Merchandiser</th>
        		<td align="left"><?=$dealing_merchant_library[$dataArray_book[0][csf('dealing_marchant')]];?></td>
        		
        	</tr>
        	<tr>
        		<th align="left">Wash</th>
        		<td colspan="2" align="left"><?= $yes_no[$wash_status]  ?></td>
        		<td colspan="4" align="left"><?= $emb_wash_type  ?></td>
        		<td>&nbsp;</td>
                <td>&nbsp;</td>
        	</tr>
        	<tr>
        		<th align="left">Peach Finish</th>
        		<td align="left"><?= $yes_no[$peach_status]  ?></td>
        		<th align="left" width="100">Brushing</th>
        		<td align="left"><?= $yes_no[$bush_status]  ?></td>
        		<th align="left" width="35">YDS</th>
        		<td align="left" colspan="2" ><?= $yes_no[$yd_status]  ?></td>
        		<th align="left">Attention</th>
        		<td align="left" colspan="2"><?=$dataArray_book[0][csf('attention')];?></td>
        	</tr>
        	<tr>
        		<th align="left">Cause of Revised</th>
        		<td colspan="9" align="left"><?=$refusing_cause;// change_date_format($sample_acc_arr[0][csf('refusing_cause')]); ?></td>
        	</tr>
        	<tr>
        		<th align="left">S.Order Remarks</th>
        		<td colspan="9" align="left"><?=$dataArray[0][csf('remarks')];?></td>
        	</tr>
        	<tr>
        		<th align="left">S.Fab Booking Remarks</th>
        		<td colspan="9" align="left"><?=$dataArray_book[0][csf('remarks')];?></td>
        	</tr>
        </table>
        <br>
		<?
         $color_sql="SELECT b.color_id ,b.process_loss_percent ,b.fabric_color,b.contrast,b.mst_id,b.dtls_id, b.qnty from  sample_development_rf_color b where b.status_active=1 and b.is_deleted=0 and b.qnty>0 and b.mst_id='$update_id' ";
		 $color_res=sql_select($color_sql);
		 $color_rf_data=array();
		 foreach ($color_res as $val) {
		 	$color_rf_data[$val[csf('dtls_id')]][$val[csf('color_id')]][$val[csf('fabric_color')]]['process_loss_percent']=$val[csf('process_loss_percent')];
		 	$color_rf_data[$val[csf('dtls_id')]][$val[csf('color_id')]][$val[csf('fabric_color')]]['contrast']=$val[csf('contrast')];
		 	$color_rf_data[$val[csf('dtls_id')]][$val[csf('color_id')]][$val[csf('fabric_color')]]['qnty']=$val[csf('qnty')];
		 }

		 $sql_fab="SELECT a.sample_name, a.gmts_item_id, c.gmts_color as color_id,   a.delivery_date, a.fabric_description, a.body_part_id, a.fabric_source, a.remarks_ra, a.gsm, a.dia, a.color_type_id, a.width_dia_id, a.uom_id, c.grey_fabric as grey_fab_qnty, c.fabric_color,c.dtls_id,c.finish_fabric as qnty,a.id,a.determination_id from sample_development_fabric_acc a, wo_non_ord_samp_booking_dtls c where  a.id=c.dtls_id  and a.form_type=1  and c.grey_fabric>0 and a.status_active=1 and a.is_deleted=0  and c.status_active=1 and c.is_deleted=0  and a.sample_mst_id='$update_id' ";
		//echo  $sql_fab;
        $sql_fab_arr=array();
        $dtls_id_arr=array();
        $determination_id_arr=array();

        foreach(sql_select($sql_fab) as $vals)
        {
        	$contrast=$color_rf_data[$vals[csf('dtls_id')]][$vals[csf('color_id')]][$vals[csf('fabric_color')]]['contrast'];
			 $process_loss_percent=$color_rf_data[$vals[csf('dtls_id')]][$vals[csf('color_id')]][$vals[csf('fabric_color')]]['process_loss_percent'];

            $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$contrast]["qnty"]+=$vals[csf("qnty")];
            $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$contrast]["process_loss_percent"]+=$process_loss_percent;

            $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$contrast]["grey_fab_qnty"]+=$vals[csf("grey_fab_qnty")];

            $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$contrast]["delivery_date"] =change_date_format($vals[csf("delivery_date")]);

            $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$contrast]["fabric_source"] =$vals[csf("fabric_source")];

            $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$contrast]["uom_id"] =$vals[csf("uom_id")];
            $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$contrast]["dia"] =$vals[csf("dia")];

            $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$contrast]["width_dia_id"] =$vals[csf("width_dia_id")];

            $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$contrast]["remarks"] =$vals[csf("remarks_ra")];
            $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$contrast]["color_type_id"] =$vals[csf("color_type_id")];

            $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$contrast]["determination_id"] =$vals[csf("determination_id")];
            array_push($dtls_id_arr,$vals[csf('id')]);
            array_push($determination_id_arr,$vals[csf('determination_id')]);
        }
        $sample_item_wise_span=array();
        /*echo '<pre>';
        print_r($sql_fab_arr); die;*/
        $determination_id_cond= where_con_using_array($determination_id_arr,0,"a.id");

        $update_dtls_id_cond= where_con_using_array($dtls_id_arr,0,"a.dtls_id");
        $sql = "SELECT a.sample_color, a.size_id, a.item_size, a.qnty_pcs, b.body_part_id from sample_requisition_coller_cuff a join sample_development_fabric_acc b on a.DTLS_ID=b.id where  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $update_dtls_id_cond";
        //echo $sql; die;
		$collar_cuff_data_arr = sql_select($sql);
		foreach ($collar_cuff_data_arr as $row)
		{
			$sample_color = $row[csf('sample_color')];
			
			$itemsize = $row[csf('item_size')];
			//$collarCuffarr[$sample_color].=$itemsize."***";
			$collarCuffarr[$sample_color][$row[csf('body_part_id')]][$itemsize]=$itemsize;
			
		}
		 $sql_d = "SELECT b.fabric_composition_name, a.id, a.construction FROM lib_yarn_count_determina_mst a left join lib_fabric_composition b on a.fabric_composition_id = b.id AND b.status_active = 1 AND b.is_deleted = 0 WHERE a.status_active = 1 AND a.is_deleted = 0  $determination_id_cond";
		// echo $sql_d; //die;
		$determina_arr = sql_select($sql_d);
		$determina_data_arr=array();
		foreach ($determina_arr as $row)
		{
			
			$determina_data_arr[$row[csf('id')]].=$row[csf('fabric_composition_name')]."***";
			$construction_data_arr[$row[csf('id')]].=$row[csf('construction')]."***";
			
		}

        foreach($sql_fab_arr as $sample_type=>$colorType_data)
        {
            foreach($colorType_data as $colorType=>$gmts_color_data)
            {

                foreach($gmts_color_data as $gmts_color_id=>$body_part_data)
                {
                	$sample_span=0;
                    foreach($body_part_data as $body_part_id=>$fab_desc_data)
                    {
                        //$kk=0;
                        foreach($fab_desc_data as $fab_id=>$gsm_data)
                        {
                            foreach($gsm_data as $gsm_id=>$dia_data)
                            {
                                foreach($dia_data as $dia_id=>$color_data)
                                {
                                    foreach($color_data as $contrast_id=>$row)
                                    {
                                        $sample_span++;
                                        //$kk++;
                                    }
                                }
                            }
                        }
                        //$bodypart_item_wise_span[$sample_type][$gmts_item_id][$body_part_id]=$kk;
                    }
                    $sample_item_wise_span[$sample_type][$gmts_color_id]=$sample_span;
                }
            }
        }
	  /*        echo "<pre>";
        print_r($sample_item_wise_span);die;*/

        $sql_sample_dtls= "SELECT a.sample_name, a.article_no, a.sample_color from sample_development_dtls a, lib_color b where a.status_active=1 and a.is_deleted=0 and a.entry_form_id=203  and sample_mst_id='$update_id' and b.status_active=1 and b.id=a.sample_color  group by a.sample_name, a.article_no, a.sample_color";
        foreach(sql_select($sql_sample_dtls) as $key=>$value)
        {
            if($sample_wise_article_no[$value[csf("sample_name")]][$value[csf("sample_color")]]=="")
            {
                $sample_wise_article_no[$value[csf("sample_name")]][$value[csf("sample_color")]]=$value[csf("article_no")];
            }
            else
            {
                if(!in_array($value[csf("article_no")], $sample_wise_article_no))
                {
                    $sample_wise_article_no[$value[csf("sample_name")]][$value[csf("sample_color")]].= ', '.$value[csf("article_no")];
                }
            }
        }
        // echo "<pre>"; print_r($sample_wise_article_no);die;

        ?>
        <table class="rpt_table" width="1120"  border="1" cellpadding="0" cellspacing="0" rules="all" style="margin-top:5px; font-size:14px">
            <thead>
                <tr>
                    <th colspan="20">Required Fabric</th>
                </tr>
                <tr>
                    <th width="20">Sl</th>
                    <th width="60">ALT / [C/W]</th>
                    <th width="80">Sample Type</th>
                    <th width="65">Gmt Color</th>
                    <th width="60">Fab. Deli<br>Date</th>
                    <th width="80">Body Part</th>
                    <th width="90">Fabric<br>Construction</th>
                    <th width="130">Fabric Desc & <br> Composition</th>
                    <th width="65">Color Type</th>
                    <th width="65">Fab. Color/ Contrast.</th>
                    <th width="70">Item Size</th>
                    <th width="45">GSM</th>
                    <th width="45">Dia</th>
                    <th width="45">Width</br>/Dia</th>
                    <th width="40">UOM</th>
                    <th width="50">Grey Qty</th>
                    <th width="40">P. Loss</th>
                    <th width="50">Fin Fab Qty</th>
                    <th width="60">Fabric<br>Source</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                <?
                function str_replace_first($search, $replace, $subject)
				{
				    $search = '/'.preg_quote($search, '/').'/';
				    return preg_replace($search, $replace, $subject, 1);
				}
                $p=1; $total_finish=0; $total_grey=0; $total_process=0;
                foreach($sql_fab_arr as $sample_type=>$colorType_data)
                {
                    foreach($colorType_data as $colorType=>$gmts_color_data)
                    {
                        foreach($gmts_color_data as $gmts_color_id=>$body_part_data)
                        {
                            $nn=0;
                            foreach($body_part_data as $body_part_id=>$fab_desc_data)
                            {
                                foreach($fab_desc_data as $fab_id=>$gsm_data)
                                {
                                    foreach($gsm_data as $gsm_id=>$dia_data)
                                    {
                                        foreach($dia_data as $dia_id=>$color_data)
                                        {
                                            //$i=0;
                                            foreach($color_data as $contrast_id=>$value)
                                            {
                                            	$constr=implode(",", array_unique(explode("***", chop($construction_data_arr[$value['determination_id']],"***"))));
                                                ?>
                                                <tr>
                                                    <td  align="center" style="word-wrap: break-word;word-break: break-all;"><? echo $p;$p++;?></td>
                                                    <?
                                                   /* if($nn==0)
                                                    {*/
                                                        $rowspan=0;
                                                        //$rowspan=$sample_item_wise_span[$sample_type][$gmts_color_id];
                                                        ?>
                                                        <td rowspan="<?=$rowspan;?>" align="center"><?=ltrim($sample_wise_article_no[$sample_type][$gmts_color_id], ',');?></td>
                                                        <td rowspan="<?=$rowspan;?>" align="center"><?=$sample_library[$sample_type];?></td>
                                                        <td align="center" rowspan="<?=$rowspan;?>"><?=$color_library[$gmts_color_id];?> </td>
                                                        <?
                                                        $nn++;
                                                    /*}*/
                                                    
                                                    ?>
                                                    <td align="center"><?=$value["delivery_date"];?> </td>
                                                    <td align="center" style="word-break:break-all"><?=$body_part[$body_part_id];?></td>
                                                    <td align="center" style="word-break:break-all"><?=$constr;?></td>
                                                    <td align="center" style="word-break:break-all"><?=str_replace_first(trim($constr), "", $fab_id);//implode(" , ", array_unique(explode("***", chop($determina_data_arr[$value['determination_id']],"***"))));// echo $fab_id;?></td>
                                                    <td align="center" style="word-break:break-all"><?=$color_type[$colorType];?></td>
                                                    <td align="center" style="word-break:break-all"><?=$contrast_id;?></td>
                                                    <td align="center" style="word-break:break-all"><? echo implode(", ", $collarCuffarr[$gmts_color_id][$body_part_id]);?></td>
                                                    <td align="center" style="word-break:break-all"><?=$gsm_id;?></td>
                                                    <td align="center" style="word-break:break-all"><?=$value["dia"];?></td>
                                                    <td align="center" style="word-break:break-all"><?=$fabric_typee[$value["width_dia_id"]];?></td>
                                                    <td align="center" style="word-break:break-all"><?=$unit_of_measurement[$value["uom_id"]];?></td>
                                                    <td align="right"><?=number_format($value["grey_fab_qnty"], 2);?></td>
                                                    <td align="right"><?=$value["process_loss_percent"];?></td>
                                                    <td align="right"><?=number_format($value["qnty"],2);?></td>
                                                    <td style="word-break:break-all"><?=$fabric_source[$value["fabric_source"]];?></td>
                                                    <td style="word-break:break-all"><?=$value["remarks"];?></td>
                                                </tr>
                                                <?
                                                //$i++;
                                                $total_finish +=$value["qnty"];
                                                $total_grey +=$value["grey_fab_qnty"];
                                                $total_process +=$value["process_loss_percent"];
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                ?>
                <tr>
                    <th colspan="14" align="right"><b>Total</b></th>
                    <th align="right"></th>
                    <th align="right"><?=number_format($total_grey, 2);?></th>
                    <th align="right"></th>
                    <th align="right"><?=number_format($total_finish, 2);?></th>
                    <th colspan="2">&nbsp;</th>
                </tr>
            </tbody>
        </table>
        <br/>
        <?
        $sample_color_arr=return_library_array( "select id, sample_color from sample_development_dtls", "id", "sample_color");
        $sql_qry="SELECT id, sample_mst_id, sample_name, gmts_item_id, smv, article_no, sample_color, sample_prod_qty, submission_qty, delv_start_date, delv_end_date, sample_charge, sample_curency, sent_to_buyer_date, comments from sample_development_dtls where status_active=1 and is_deleted=0 and entry_form_id=203 and sample_mst_id='$update_id' order by id asc";

        $sql_qry_color="SELECT a.id, a.sample_mst_id, a.sample_name, a.gmts_item_id, a.smv, a.article_no, a.sample_color, a.sample_prod_qty, a.submission_qty, a.delv_start_date, a.delv_end_date, a.sample_charge, a.sample_curency, a.sent_to_buyer_date, a.comments, c.dtls_id, c.size_id, c.bh_qty, c.self_qty, c.test_qty, c.plan_qty, c.dyeing_qty from sample_development_dtls a, sample_development_size c where a.id=c.dtls_id and a.status_active =1 and a.is_deleted=0 and a.entry_form_id=203 and a.sample_mst_id='$update_id' order by a.id asc";
        $size_type_arr=array(1=>"BH Qty",2=>"Self Qty",3=>"Test qty",4=>"Plan Qty",5=>"Dyeing Qty");
        $color_size_arr=array();
        foreach(sql_select($sql_qry_color) as $vals)
        {
            if($vals[csf("bh_qty")]>0)
            {
                $color_size_arr[1][$vals[csf("size_id")]]='Bh Qty';
                $bh_qty=$vals[csf("bh_qty")];
                $color_size_dtls_qty_arr[1][$vals[csf("id")]][$vals[csf("size_id")]]=$bh_qty;
            }
            if($vals[csf("self_qty")]>0)
            {
                $color_size_arr[2][$vals[csf("size_id")]]='self qty';
                $color_size_dtls_qty_arr[2][$vals[csf("id")]][$vals[csf("size_id")]]=$vals[csf("self_qty")];
            }
            if($vals[csf("test_qty")]>0)
            {
                $color_size_arr[3][$vals[csf("size_id")]]='test qty';
                $color_size_dtls_qty_arr[3][$vals[csf("id")]][$vals[csf("size_id")]]=$vals[csf("test_qty")];
            }
            if($vals[csf("plan_qty")]>0)
            {
                $color_size_arr[4][$vals[csf("size_id")]]='plan qty';
                $color_size_dtls_qty_arr[4][$vals[csf("id")]][$vals[csf("size_id")]]=$vals[csf("plan_qty")];
            }
            if($vals[csf("dyeing_qty")]>0)
            {
                $color_size_arr[5][$vals[csf("size_id")]]='Dyeing qty';
                $color_size_dtls_qty_arr[5][$vals[csf("id")]][$vals[csf("size_id")]]=$vals[csf("dyeing_qty")];
            }
        }
        $tot_row=count($color_size_arr);
        $result=sql_select($sql_qry);
		$head_tot_row_td=0;
		foreach($color_size_arr as $type_id=>$data_size)
		{
			foreach($data_size as $size_id=>$data_val)
			{
				$head_tot_row_td++;
			}
		}
        ?>
        <table align="left" cellspacing="0" border="1" width="1100" class="rpt_table" rules="all" style="margin-top: 5px; font-size:14px">
            <thead>
                <tr>
                    <td width="150" colspan="<? echo 10+$head_tot_row_td;?>" align="center"><strong>Sample Details</strong></td>
                </tr>
                <tr>
                    <th width="30" rowspan="2">Sl</th>
                    <th width="100" rowspan="2">Sample Name</th>
                    <th width="120" rowspan="2">Garment Item</th>
                    <th width="70" rowspan="2">Sample Delv.  Date</th>
                    <th width="55" rowspan="2">ALT / [C/W]</th>
                    <th width="70" rowspan="2">Color</th>
                        <?
                        $tot_row_td=0;
                        foreach($color_size_arr as $type_id=>$val)
                        {
                            ?>
                            <th width="45" align="center" colspan="<?=count($val);?>"><?=$size_type_arr[$type_id];?></th>
                            <?
                        }
                        ?>
                    <th rowspan="2" width="55">Total</th>
                    <th rowspan="2" width="55">Submn Qty</th>
                    <th rowspan="2"  width="70">Buyer Submisstion Date</th>
                    <th rowspan="2">Remarks</th>
                </tr>
                <tr>
                    <?
					$tot_row_td=0;
                    foreach($color_size_arr as $type_id=>$data_size)
                    {
                        foreach($data_size as $size_id=>$data_val)
                        {
                            $tot_row_td++;
                            ?>
                            <th width="40" align="center"><?=$size_library[$size_id];?></th>
                            <?
                        }
                    }
                    ?>
                </tr>
            </thead>
            <tbody>
                <?
                $i=1; $k=0; $gr_tot_sum=0; $gr_sub_sum=0;
                foreach($result as $row)
                {
                    $dtls_ids=$row[csf('id')];
                    $prod_sum=$prod_sum+$row[csf('sample_prod_qty')];
                    $sub_sum=$sub_sum+$row[csf('submission_qty')];
                    $k++;
                    ?>
                    <tr>
                        <td align="center"><?=$k;?></td>
                        <td align="left"><?=$sample_library[$row[csf('sample_name')]];?></td>
                        <td align="left"><?=$garments_item[$row[csf('gmts_item_id')]];?></td>
                        <td align="left"><?=change_date_format($row[csf('delv_end_date')]);?></td>
                        <td align="left"><?=$row[csf('article_no')];?></td>
                        <td align="left"><?=$color_library[$row[csf('sample_color')]];?></td>
                        <?
                        $total_sizes_qty=0;  $total_sizes_qty_subm=0;
                        foreach($color_size_arr as $type_id=>$data_size)
                        {
                            foreach($data_size as $size_id=>$data_val)
                            {
                                $size_qty=$color_size_dtls_qty_arr[$type_id][$dtls_ids][$size_id];
                                ?>
                                <td align="right"><?=$size_qty;?></td>
                                <?
                                if($type_id==1)
                                {
                                $total_sizes_qty_subm+=$size_qty;
                                }
                                $total_sizes_qty+=$size_qty;
                            }
                        }
                        ?>
                        <td align="right"><?=number_format($total_sizes_qty,2);?></td>
                        <td align="right"><?=number_format($total_sizes_qty_subm,2);?></td>
                        <td align="left"><?=change_date_format($row[csf('sent_to_buyer_date')]);?> </td>
                        <td align="left"><?=$row[csf('comments')];?> </td>
                    </tr>
                    <?
                    $gr_tot_sum+=$total_sizes_qty;
                    $gr_sub_sum+=$total_sizes_qty_subm;
                }
                ?>
                <tr>
                    <td colspan="<?=6+$tot_row_td;?>" align="right"><b>Total</b></td>
                    <td align="right"><b><?=number_format($gr_tot_sum,2);?> </b></td>
                    <td align="right"><b><?=number_format($gr_sub_sum,2);?> </b></td>
                    <td colspan="2">&nbsp;</td>
                </tr>
            </tbody>
        </table>
        <br>&nbsp;

        <table align="left" cellspacing="0" border="1" width="1100" class="rpt_table" rules="all" style="margin-top: 5px; font-size:14px">
            <thead>
                <tr>
                    <td colspan="13" align="center"><strong>Required Accessories</td>
                </tr>
                <tr>
                    <th width="30">Sl</th>
                    <th width="100">Sample Name</th>
                    <th width="120">Garment Item</th>
                    <th width="100">Trims Group</th>
                    <th width="100">Description</th>
                    <th width="100">Supplier</th>
                    <th width="100">Brand/Supp.Ref</th>
                    <th width="30">UOM</th>
                    <th width="30">Req/Dzn</th>
                    <th width="30">Req/Qty</th>
                    <th width="80">Acc.Sour.</th>
                    <th width="100">Acc Delivery Date</th>
                    <th>Remarks </th>
                </tr>
            </thead>
            <tbody>
				<?
                $sql_qryA="SELECT id,sample_mst_id,sample_name_ra,gmts_item_id_ra,trims_group_ra,description_ra,brand_ref_ra,uom_id_ra,req_dzn_ra,req_qty_ra,remarks_ra,delivery_date,supplier_id,fabric_source from sample_development_fabric_acc where status_active =1 and is_deleted=0 and form_type=2 and sample_mst_id='$update_id' order by id asc";

                $resultA=sql_select($sql_qryA);
                $i=1;$k=0; $req_dzn_ra=0; $req_qty_ra=0;
                foreach($resultA as $rowA)
                {
					$req_dzn_ra=$req_dzn_ra+$rowA[csf('req_dzn_ra')];
					$req_qty_ra=$req_qty_ra+$rowA[csf('req_qty_ra')];
					$k++;
					?>
					<tr>
                        <td align="center"><? echo $k;?></td>
                        <td align="left"><? echo $sample_library[$rowA[csf('sample_name_ra')]];?></td>
                        <td align="left"><? echo $garments_item[$rowA[csf('gmts_item_id_ra')]];?></td>
                        <td align="left"><? echo $trims_group_lib[$rowA[csf('trims_group_ra')]];?></td>
                        <td align="left"><? echo $rowA[csf('description_ra')];?></td>
                        <td align="left"><? echo $supplier_library[$rowA[csf('supplier_id')]];?></td>
                        <td align="left"><? echo $rowA[csf('brand_ref_ra')];?></td>
                        <td align="center"><? echo $unit_of_measurement[$rowA[csf('uom_id_ra')]];?></td>
                        <td align="right"><? echo number_format($rowA[csf('req_dzn_ra')],2);?></td>
                        <td align="right"><? echo number_format($rowA[csf('req_qty_ra')],2);?></td>
                        <td align="left"><? echo $fabric_source[$rowA[csf('fabric_source')]];?></td>
                        <td align="left"><? echo change_date_format($rowA[csf('delivery_date')]);?></td>
                        <td align="left"><? echo $rowA[csf('remarks_ra')];?></td>
					</tr>
					<?
                }
                ?>
                <tr>
                    <td colspan="8" align="center"><b>Total </b></td>
                    <td align="right"><b><? echo number_format($req_qty_ra,2);?> </b></td>
                    <td>&nbsp;</td>
                </tr>
            </tbody>
        </table>
        <br>
        <table align="left" cellspacing="0" border="1" width="1000" class="rpt_table" rules="all" style="margin-top: 5px;font-size:14px">
            <thead>
                <tr>
                	<td colspan="9" align="center"><strong>Required Emebellishment</td>
                </tr>
                <tr>
                    <th width="30">Sl</th>
                    <th width="100">Sample Name</th>
                    <th width="110">Garment Item</th>
                    <th width="110">Body Part</th>
                    <th width="100">Supplier</th>
                    <th width="60">Name</th>
                    <th width="70">Type</th>
                    <th width="100">Emb.Del.Date</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
				<?
                $sql_qry="SELECT id,sample_mst_id,sample_name_re,gmts_item_id_re,name_re,type_re,remarks_re,body_part_id,delivery_date,supplier_id from sample_development_fabric_acc where sample_mst_id='$update_id' and form_type=3 and is_deleted=0  and status_active=1 order by id asc";

                $result=sql_select($sql_qry); $k=0;
                $type_array=array(1=>$emblishment_print_type,2=>$emblishment_embroy_type,3=>$emblishment_wash_type,4=>$emblishment_spwork_type,5=>$emblishment_gmts_type);
                foreach($result as $row)
                {
					$k++;
					?>
					<tr>
                        <td align="center"><? echo $k;?></td>
                        <td align="left"><? echo $sample_library[$row[csf('sample_name_re')]];?></td>
                        <td align="left"><? echo $garments_item[$row[csf('gmts_item_id_re')]];?></td>
                        <td align="left"><? echo $body_part[$row[csf('body_part_id')]];?></td>
                        <td align="left"><? echo $supplier_library[$row[csf('supplier_id')]];?></td>
                        <td align="left"><? echo $emblishment_name_array[$row[csf('name_re')]];?></td>
                        <td align="left">
                            <?
                            if($row[csf('name_re')]==1) echo $emblishment_print_type[$row[csf('type_re')]];
                            if($row[csf('name_re')]==2) echo $emblishment_embroy_type[$row[csf('type_re')]];
                            if($row[csf('name_re')]==3) echo $emblishment_wash_type[$row[csf('type_re')]];
                            if($row[csf('name_re')]==4) echo $emblishment_spwork_type[$row[csf('type_re')]];
                            if($row[csf('name_re')]==5) echo $emblishment_gmts_type[$row[csf('type_re')]];
                            ?>
                        </td>
                        <td align="left"><? echo change_date_format($row[csf('delivery_date')]);?></td>
                        <td align="left"><? echo $row[csf('remarks_re')];?></td>
                    </tr>
					<?
                }
                ?>
            </tbody>
        </table>
          <br>
        	<table  style="margin-top: 10px;font-size:13px;float:left;margin-right:1%" class="rpt_table" width="30%" align="left" border="1" cellpadding="0" cellspacing="0" rules="all">
                <caption> <b> Yarn Required Summary- </b> </caption>
                	<thead>
                    	<tr align="center">
                        	<th align="center" width="40">Sl</th>
                        	<th align="center">Yarn Desc.</th>
                             <th align="center">Req. Qty</th> 
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count"  );
					$lib_supllier_arr=return_library_array( "select supplier_id,booking_no from wo_non_ord_samp_booking_mst where booking_no='$txt_booking_no' and entry_form_id=140", "booking_no", "supplier_id"  );
					$tot_req_qty=0;

					
					$sql_yarn="select b.count_id,b.copm_one_id,b.percent_one,b.type_id,sum(b.cons_qnty) as  cons_qnty from  sample_development_yarn_dtls b where  b.status_active=1  and b.mst_id='$update_id' and b.determin_id in (select determination_id from sample_development_fabric_acc  where status_active=1 and sample_mst_id='$update_id' and form_type=1) group by b.count_id,b.copm_one_id,b.percent_one,b.type_id";
					//echo $sql_yarn;
					$data_array=sql_select($sql_yarn);

					
					if ( count($data_array)>0)
					{
						$l=1;
						foreach( $data_array as $key=>$row )
						{
							$yarn_des=$lib_yarn_count[$row[csf("count_id")]].','.$composition[$row[csf("copm_one_id")]].$yarn_type[$row[csf("type_id")]];
							?>
                            	<tr>
                                    <td> <? echo $l;?> </td>
                                    <td> <? echo $yarn_des; ?> </td>
                                    <td align="right"> <? echo number_format($row[csf("cons_qnty")],2); ?> </td>
                                </tr>
                            <?
                            $l++;
							$tot_req_qty+=$row[csf("cons_qnty")];
						}
					}

					?>
                    <tr>
						<th  colspan="2" align="right"><b>Total</b></th>
						<th  align="right"><? echo number_format($tot_req_qty,2);?></th>
					</tr>
                </tbody>
            </table>
			<table  style="margin-top: 10px;font-size:14px ;float:left" class="rpt_table" width="69%" align="left" border="1" cellpadding="0" cellspacing="0" rules="all">
                <caption> <b> Dyes To Match</b> </caption>
                	<thead>
                    	<tr align="center">
                        	<th align="center" width="40">Sl</th>
                        	<th align="center">Item</th>
							<th align="center">Item Desc.</th>
							 <th align="center">Body Color</th>
                        	<th align="center">Item Color</th>
                             <th align="center">Finish  Qty</th> 
							 <th align="center">UOM</th> 
                        </tr>
                    </thead>
                    <tbody>
                    <?
				
					$lib_item_group_arr=return_library_array( "select item_name, id from lib_item_group where item_category=4 and is_deleted=0  and  status_active=1 order by item_name", "id", "item_name");
					$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count"  );

					$lib_supllier_arr=return_library_array( "select supplier_id,booking_no from wo_non_ord_samp_booking_mst where booking_no='$txt_booking_no' and entry_form_id=140", "booking_no", "supplier_id"  );
					$tot_req_qty=0;

					$dtm_arr_item_color=array();
					$sql=sql_select("select sample_req_fabric_cost_id,fabric_color,sample_req_trim_cost_id,item_color,sum(qty) as qty from sample_dev_dye_to_match where booking_no='$txt_booking_no'  and status_active=1 and is_deleted=0 group by sample_req_fabric_cost_id,fabric_color,item_color,sample_req_trim_cost_id");
					
					foreach($sql as $row){
						$dtm_arr[$row[csf('fabric_color')]][$row[csf('sample_req_trim_cost_id')]]+=$row[csf('qty')];
						$dtm_arr_item_color[$row[csf('fabric_color')]][$row[csf('sample_req_trim_cost_id')]]=$row[csf('item_color')];
					}




					
					$dye_to_match_sql="select a.id,a.sample_mst_id, a.trims_group_ra as trim_group, a.fabric_description as description,a.uom_id_ra as cons_uom,sum(a.req_qty_ra) as req_qty_ra,  c.fabric_color as fabric_color_id,a.description_ra    FROM sample_development_fabric_acc a,  wo_non_ord_samp_booking_dtls c
					WHERE c.style_id=a.sample_mst_id  and a.form_type=2 and c.booking_no ='$txt_booking_no' and c.status_active=1 and  c.status_active=1  and a.status_active=1 and c.is_deleted=0
					group by a.id,a.sample_mst_id, a.trims_group_ra,a.fabric_description,a.uom_id_ra,  c.fabric_color ,a.description_ra    order by a.id  ";
					//echo $sql_yarn;
					$data_array=sql_select($dye_to_match_sql);

					
					if ( count($data_array)>0)
					{
						$l=1;
						foreach( $data_array as $key=>$row )
						{
							$yarn_des=$lib_yarn_count[$row[csf("count_id")]].','.$composition[$row[csf("copm_one_id")]].','.$row[csf("percent_one")].'%,'.$yarn_type[$row[csf("type_id")]];
							?>
                            	<tr>
                                    <td> <? echo $l;?> </td>
                                    <td> <? echo $lib_item_group_arr[$row[csf("trim_group")]]; ?> </td>
                                    <td align="left"> <? echo $row[csf("description_ra")]; ?> </td>							
                              								
									<td> <? echo  $color_library[$row[csf('fabric_color_id')]]; ?> </td>
                                    <td > <? echo $color_library[$dtm_arr_item_color[$row[csf('fabric_color_id')]][$row[csf('id')]]]; ?> </td>									
									<td align="right"> <? echo $dtm_arr[$row[csf('fabric_color_id')]][$row[csf('id')]] ;//echo $row[csf("fin_fab_qnty")]; //echo $dtm_arr[$fabric_cost_id][$color][$row[csf('id')]]?> </td>
                                    <td align="right"> <? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?> </td>
                                </tr>
                            <?
                            $l++;
							$tot_req_qty+=$dtm_arr[$row[csf('fabric_color_id')]][$row[csf('id')]] ;;
						}
					}

					?>
                    <tr>
						<th  colspan="5" align="right"><b>Total</b></th>
						<th  align="right"><? echo number_format($tot_req_qty,2);?></th>
					</tr>
                </tbody>
            </table>
        <br>
        <br>
        <?
        	$sample_stripe_data=sql_select("SELECT a.body_part_id, b.contrast, b.color_id, b.grey_fab_qnty,c.id as strip_mst_id, c.stripe_color, c.measurement, c.uom, c.fabreq, c.yarn_dyed FROM sample_development_fabric_acc a join sample_development_rf_color b on a.id=b.dtls_id join wo_sample_stripe_color c on a.id=c.sample_fab_dtls_id and b.color_id=c.color_number_id where a.status_active=1 and a.is_deleted=0 and a.color_type_id in (2,3,4,6,31,32,33,34) and a.form_type=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.sample_mst_id=$update_id");
        	
        	foreach ($sample_stripe_data as $row) {
        		$key=$row[csf('body_part_id')].'*'.$row[csf('color_id')];
        		$sample_stripe_arr[$key]['body_part_id'] = $row[csf('body_part_id')];
        		$sample_stripe_arr[$key]['fabric_color'] = $row[csf('color_id')];
        		$sample_stripe_arr[$key]['fabric_qty'] = $row[csf('grey_fab_qnty')];
				
        		$sample_stripe_arr[$key]['stripe_color'][$row[csf('strip_mst_id')]][$row[csf('stripe_color')]]['color'] = $row[csf('stripe_color')];
        		$sample_stripe_arr[$key]['stripe_color'][$row[csf('strip_mst_id')]][$row[csf('stripe_color')]]['measurement'] = $row[csf('measurement')];
        		$sample_stripe_arr[$key]['stripe_color'][$row[csf('strip_mst_id')]][$row[csf('stripe_color')]]['uom'] = $row[csf('uom')];
        		$sample_stripe_arr[$key]['stripe_color'][$row[csf('strip_mst_id')]][$row[csf('stripe_color')]]['qty'] = $row[csf('fabreq')];
        		$sample_stripe_arr[$key]['stripe_color'][$row[csf('strip_mst_id')]][$row[csf('stripe_color')]]['yarn_dyed'] = $row[csf('yarn_dyed')];
        		
				$stripe_color_summ[$row[csf('stripe_color')]] += $row[csf('fabreq')];
        	}
        ?>
        <div style="width:1000px; ">
	        <table align="left" cellspacing="0" border="1" style="width:800px;float: left; right; margin-top: 5px;font-size:14px" class="rpt_table" rules="all">
	        	<thead>
	        		<tr>
	        			<th colspan="9">Stripe Details</th>
	        		</tr>
	        		<tr>
	        			<th width="30">SL</th>
	        			<th width="100">Body Part</th>
	        			<th width="60">Fabric Color</th>
	        			<th width="60">Fabric Qty(KG)</th>
	        			<th width="60">Stripe Color</th>
	        			<th width="60">Stripe Measurement</th>
	        			<th width="60">Stripe Uom</th>
	        			<th width="60">Qty.(KG)</th>
	        			<th width="60">Y/D Req.</th>
	        		</tr>
	        	</thead>
	        	<tbody>
	        		<? $sl=1;
	        		foreach ($sample_stripe_arr as $sdata) {
	        			$rowspan = count($sdata['stripe_color']);
	        			$i=1;
	        			foreach ($sdata['stripe_color'] as $stripe_mst) {
							foreach ($stripe_mst as $stripe_data) {
	        				if($i==1){
	        					$total_fabric += $sdata['fabric_qty'];
	        					$total_stripe_fabric += $stripe_data['qty'];
	        				?>
	        				<tr>
			        			<td rowspan="<?=$rowspan?>"><?= $sl; ?></td>
			        			<td rowspan="<?=$rowspan?>"><?= $body_part[$sdata['body_part_id']]; ?></td>
			        			<td rowspan="<?=$rowspan?>"><?= $color_library[$sdata['fabric_color']]; ?></td>
			        			<td align="right" rowspan="<?=$rowspan?>"><?= $sdata['fabric_qty']; ?></td>
			        			<td><?= $color_library[$stripe_data['color']]; ?></td>
			        			<td align="right"><?= $stripe_data['measurement']; ?></td>
			        			<td><?= $unit_of_measurement[$stripe_data['uom']]; ?></td>
			        			<td align="right"><?= $stripe_data['qty']; ?></td>
			        			<td><?= $yes_no[$stripe_data['yarn_dyed']]; ?></td>
			        		</tr>
	        				<?
	        					$i++;
	        				}
	        				else{
	        					$total_stripe_fabric += $stripe_data['qty'];
	        					?>
	        						<tr>
	        							<td><?= $color_library[$stripe_data['color']]; ?></td>
					        			<td align="right"><?= $stripe_data['measurement']; ?></td>
					        			<td><?= $unit_of_measurement[$stripe_data['uom']]; ?></td>
					        			<td align="right"><?= $stripe_data['qty']; ?></td>
					        			<td><?= $yes_no[$stripe_data['yarn_dyed']]; ?></td>
	        						</tr>
	        					<?
	        				}
	        			}
	        			$sl++;
						}
	        		} ?>
	        	</tbody>
	        	<tfoot>
	        		<tr>
	        			<th colspan="3">Total</th>
	        			<th align="right"><?= $total_fabric ?></th>
	        			<th></th>
	        			<th></th>
	        			<th></th>
	        			<th align="right"><?= $total_stripe_fabric ?></th>
	        			<th></th>
	        		</tr>
	        	</tfoot>
	        </table>
	        <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:180px; margin-left: 2px; float: right; right; margin-top: 5px;font-size:14px" rules="all">
		        <thead>
		        	<tr>
		        		<th colspan="3">Stripe Color wise Summary</th>
		        	</tr>
		        	<tr>
		        		<th>SL</th>
		        		<th>Stripe Color</th>
		        		<th>Qty.(KG)</th>
		        	</tr>
		        </thead>
		        <tbody>
		        	<?
		        	$sl=1;
		        	foreach ($stripe_color_summ as $color_id => $value) {
		        	 	$total_fabric_qty+= $value;
		        	?>
		        	<tr>
		        		<td><?= $sl ?></td>
		        		<td><?= $color_library[$color_id]; ?></td>
		        		<td><?= $value ?></td>
		        	</tr>
		        	<? $sl++;
		        	} ?>
		        </tbody>
		        <tfoot>
		        	<tr>
		        		<th colspan="2">Total</th>
		        		<th><?= $total_fabric_qty; ?></th>
		        	</tr>
		        </tfoot>
	        </table>
        </div>
        <?
			$coller_cuff_data=sql_select("SELECT a.sample_color, a.size_id, a.item_size, a.qnty_pcs, c.body_part_type from sample_requisition_coller_cuff a join sample_development_fabric_acc b on b.id=a.dtls_id join lib_body_part c on b.body_part_id=c.id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.sample_mst_id=$update_id");
			$coller_data_arr=array(); $cuff_data_arr=array();
			foreach ($coller_cuff_data as $row) {
				if($row[csf('body_part_type')]==40)
				{
					$coller_size_arr[$row[csf('size_id')]]=$row[csf('size_id')];
					$color_size_data[$row[csf('size_id')]]['item_size']=$row[csf('item_size')];
					$color_color_data[$row[csf('sample_color')]][$row[csf('size_id')]]['qnty_pcs']=$row[csf('qnty_pcs')];
				}
				if($row[csf('body_part_type')]==50)
				{
					$cuff_size_arr[$row[csf('size_id')]]=$row[csf('size_id')];
					$cuff_size_data[$row[csf('size_id')]]['item_size']=$row[csf('item_size')];
					$cuff_color_data[$row[csf('sample_color')]][$row[csf('size_id')]]['qnty_pcs']=$row[csf('qnty_pcs')];
				}
			} 
			/*echo '<pre>';
			print_r($color_color_data); die;*/
        ?>
        <div style="width:1000px; margin-top: 10px; ">
        	<table align="left" cellspacing="0" border="1" style="width:495px;float: left; margin-top: 5px;font-size:14px" class="rpt_table" rules="all">
        		<thead>
        			<tr>
        				<th colspan="<? echo count($coller_size_arr)+2;  ?>">Cuff - Color Size Brakedown in Pcs.</th>
        			</tr>
        			<tr>
        				<th>Size</th>
        				<? foreach ($coller_size_arr as $size_id) { ?>
        				<th><?= $size_library[$size_id]; ?></th>
        				<? } ?>
        				<th rowspan="2">Total</th>
        			</tr>
        		</thead>
        		<tbody>
        			<tr>
        			<th>Cuff Size</th>
        				<?
        					foreach ($coller_size_arr as $size_id) {
        				 ?>
        				 <td><?= $color_size_data[$size_id]['item_size'] ?></td>        				
        				<? }         				
        			?></tr>
        			<? foreach ($color_color_data as $fabric_color =>$size_data) { ?>
        				<tr>
        					<td><?= $color_library[$fabric_color]?></td><?
        					$total_size_qty=0;
        					foreach ($coller_size_arr as $size_id) {
        						$total_size_qty+=$size_data[$size_id]['qnty_pcs'];
        						$total_size_arr[$size_id]+=$size_data[$size_id]['qnty_pcs'];
        				 ?>
        				 <td><?= ($size_data[$size_id]['qnty_pcs']) ? $size_data[$size_id]['qnty_pcs'] : 0; ?></td>        				
        				<? } ?>
        					<td><? echo $total_size_qty; ?></td>
        				</tr><?
        			}
        			 ?>        			
        		</tbody>
        		<tfoot>
        			<tr>
        			<th align="right">Total</th>
        			<?
    				foreach ($coller_size_arr as $size_id) {
    					$grand_size_qty_total+= $total_size_arr[$size_id]
    				 ?>
    					<th align="left"><?  echo $total_size_arr[$size_id]  ?></th>
    				<? }
        			?>
        			<th align="left"><?= $grand_size_qty_total ?></th>
        			</tr>
        		</tfoot>
        	</table>
        	<table align="left" cellspacing="0" border="1" style="width:495px;float: right; margin-top: 5px;font-size:14px" class="rpt_table" rules="all">
        		<thead>
        			<tr>
        				<th colspan="<? echo count($cuff_size_arr)+2;  ?>">Collar - Color Size Brakedown in Pcs.</th>
        			</tr>
        			<tr>
        				<th>Size</th>
        				<? foreach ($cuff_size_arr as $size_id) { ?>
        				<th><?= $size_library[$size_id]; ?></th>
        				<? } ?>
        				<th rowspan="2">Total</th>
        			</tr>
        		</thead>
        		<tbody>
        			<tr>
        			<th>Collar Size</th>
        				<?
        					foreach ($cuff_size_arr as $size_id) {
        				 ?>
        				 <td><?= $cuff_size_data[$size_id]['item_size'] ?></td>        				
        				<? }         				
        			?></tr>
        			<? foreach ($cuff_color_data as $fabric_color =>$size_data) { ?>
        				<tr>
        					<td><?= $color_library[$fabric_color]?></td><?
        					$total_size_qty=0;
        					foreach ($cuff_size_arr as $size_id) {
        						$total_size_qty+=$size_data[$size_id]['qnty_pcs'];
        						$total_cuff_size_arr[$size_id]+=$size_data[$size_id]['qnty_pcs'];
        				 ?>
        				 <td><?= ($size_data[$size_id]['qnty_pcs']) ? $size_data[$size_id]['qnty_pcs'] : 0; ?></td>        				
        				<? } ?>
        					<td><? echo $total_size_qty; ?></td>
        				</tr><?
        			}
        			 ?>        			
        		</tbody>
        		<tfoot>
        			<tr>
        			<th align="right">Total</th>
        			<?
    				foreach ($cuff_size_arr as $size_id) {
    					$grand_qty_total+= $total_cuff_size_arr[$size_id]
    				 ?>
    					<th align="left"><?  echo $total_cuff_size_arr[$size_id];  ?></th>
    				<? }
        			?>
        			<th align="left"><?= $grand_qty_total ?></th>
        			</tr>
        		</tfoot>
        	</table>
        </div>

		<br><br><br>
		<?
		//echo get_spacial_instruction($txt_booking_no,"97%",118);
		$mst_id=$txt_booking_no; $width="100%"; $entry_form=140;
		
			if ($entry_form != '') {$entry_form_con = " and entry_form=$entry_form";}
			//echo "select id, terms from  wo_booking_terms_condition where   booking_no='" . str_replace("'", "", $mst_id) . "' $entry_form_con   order by id";
			$data_array = sql_select("select id, terms from  wo_booking_terms_condition where   booking_no='" . str_replace("'", "", $mst_id) . "' $entry_form_con   order by id asc");
			$tot_row=count($data_array)/2;
			//echo $tot_row;
			$k=1;
			foreach($data_array as $row)
			{
				if($k<=$tot_row)
				{
				$term_bookingArr[$row[csf('id')]]['terms']=$row[csf('terms')];
				}
				else
				{
				$other_term_bookingArr[$row[csf('id')]]['terms']=$row[csf('terms')];	
				}
				$k++;
			}
			
if (count($data_array) > 0) {
		?>
        <br>
        <table align="left"  width="<?=$width;?>" align="center"   border="0" cellpadding="0" cellspacing="0" >
        <tr>
        <td valign="top">
        
        <table   width="650" class="rpt_table"   align="center"  border="1" cellpadding="0" cellspacing="0" rules="all">
        <thead>
            <tr style="border:1px solid black;">
            <th width="4%" style="border:1px solid black;">Sl</th>
            <th width="45%" style="border:1px solid black;">Special Instruction</th>
            </tr>
        </thead>
        <tbody>
		<?
		
			//print_r($term_bookingArr);
		$sl=1;
				foreach ($term_bookingArr as $term=>$row) {
					?>
					<tr id="settr_1" align="" style="border:1px solid black;">
					<td align="center" style="border:1px solid black;text-align:center"><?=$sl;?></td>
				   <td style="border:1px solid black; font-weight:bold"><?=$row['terms'];?></td>
					<?
					$sl++;
					}
				
		?>
	</tbody>
	</table>
    </td>
    <!--1st part end-->
    <?
	$sl2=$sl;
    if (count($other_term_bookingArr) > 0) {
	?>
		<td valign="top">
        	<table  width="650" class="rpt_table"   align="center"  border="1" cellpadding="0" cellspacing="0" rules="all">
        <thead>
            <tr style="border:1px solid black;">
            <th width="4%" style="border:1px solid black;" >Sl</th>
            <th width="45%" style="border:1px solid black;">Special Instruction</th>
            </tr>
        </thead>
        <tbody>
				<?
				foreach ($other_term_bookingArr as $term2=>$row2) {
					?>
					<tr id="settr_2" align="" style="border:1px solid black;">
					<td align="center" style="border:1px solid black; text-align:center"><?=$sl2;?></td>
				   <td style="border:1px solid black; font-weight:bold"><?=$row2['terms'];?></td>
					<?
					$sl2++;
					}
				
			?>
		</tbody>
		</table>
    
        	</td> 
        <?
		}
		?>   
    </tr>
    </table>
    <?
}
	?>	
	<br>
        

    
	</div>

 </body>
			<? 

			$reportBody=ob_get_contents();
			$user_id=$_SESSION['logic_erp']['user_id'];
			$user_arr=return_library_array( "select id, USER_NAME from user_passwd where id=$user_id", "id", "USER_NAME");
			$prepared_by = $user_arr[$user_id];
			$report_signature=signature_table1(134, $cbo_company_name, "1080px", $cbo_template_id, $padding_top = 70,$prepared_by,'');
			ob_end_clean();
			
			foreach (glob("../../../auto_mail/tmp/sample_req_with_booking_".$user_id.".pdf") as $filename) {			
				@unlink($filename);
			}
			$att_file_arr=array();
			require('../../../ext_resource/mpdf60/mpdf.php');
			$mpdf = new mPDF('', 'A4', '', '', 10, 10, 10, 35, 3, 3);	
			$mpdf->SetHTMLFooter($report_signature);
			$mpdf->WriteHTML($reportBody,2);
			$user_id=$_SESSION['logic_erp']['user_id'];
			$REAL_FILE_NAME = 'sample_req_with_booking_'.$user_id.'.pdf';
			$file_path='../../../auto_mail/tmp/' . $REAL_FILE_NAME;
			$mpdf->Output($file_path, 'F');
			$att_file_arr[]='../../../auto_mail/tmp/'.$REAL_FILE_NAME.'**'.$REAL_FILE_NAME;
			
?>
		
   <script type="text/javascript" src="../../js/jquery.js"></script>
   <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
   <script>
		function fnc_generate_Barcodes( valuess, img_id )
		{
			var value = valuess;//$("#barcodeValue").val();
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer ='bmp';// $("input[name=renderer]:checked").val();
			var settings = {
			  output:renderer,
			  bgColor: '#FFFFFF',
			  color: '#000000',
			  barWidth: 1,
			  barHeight: 60,
			  moduleSize:5,
			  posX: 10,
			  posY: 20,
			  addQuietZone: 1
			};
			$("#"+img_id).html('11');
			 value = {code:value, rect: false};
			$("#"+img_id).show().barcode(value, btype, settings);
		}
   </script>
   <script type="text/javascript">
   	fnc_generate_Barcodes('<? echo $barcode_no; ?>','barcode_img_id');
   </script>
	<?
    exit();
}


if($action=="sample_requisition_print7")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$cbo_template_id=$data[3];
	$path="../../";
	if(count($data)>3)
	{
		if($data[4]=='../')
		{
			$path=$data[4];
		}
	}
	//echo $path;
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$supplier_library=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );

	$company_img=sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");


	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$dealing_merchant_library=return_library_array( "select id,team_member_name from lib_mkt_team_member_info", "id", "team_member_name"  );
	$team_leader_arr=return_library_array( "select id,team_leader_name from lib_marketing_team", "id", "team_leader_name"  );

	$sample_library=return_library_array( "select id, sample_name from lib_sample", "id", "sample_name");
	$size_library=return_library_array( "select id, size_name from lib_size", "id", "size_name"  );
	$color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
	$season_arr=return_library_array( "select id,season_name from lib_buyer_season  where status_active =1 and is_deleted=0 ", "id", "season_name"  );
	$trims_group_lib=return_library_array( "select id,item_name from lib_item_group  where status_active =1 and is_deleted=0", "id", "item_name");
	//concate(buyer_name,'_',contact_person)
	$appDate=return_field_value("approved_date","approval_history","entry_form=25 and mst_id='$data[1]' order by id desc");
	$appBy=return_field_value("approved_by","approval_history","entry_form=25 and mst_id='$data[1]'");
	$user_arr=return_library_array( "select id, user_name from user_passwd", "id", "user_name"  );
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='sample_details_1' and file_type=1",'master_tble_id','image_location');




 ?>
 <style>
	#mstDiv {
	    margin:0px auto;
	    width:1130px;

	}
	#mstDiv @media print {

	   thead {display: table-header-group;}

	}

	 @media print{
			html>body table.rpt_table {
			margin-left:12px;
	  		}

		}
	</style>

		<div id="mstDiv">

	     <table width="1100" cellspacing="0" border="0"  style="font-family: Arial Narrow;margin-left: 20px;" >
	     <tr>
	     	<td rowspan="4" valign="top" width="300"><img width="150" height="80" src="<?=$path;?><? echo $company_img[0][csf("image_location")]; ?>" ></td>
	     	<td colspan="4" style="font-size: 24px;"><strong><b><? echo $company_library[$data[0]]; ?></b></strong></td>
	            <td width="200">
	            <?

	             $nameArray_approved=sql_select( "SELECT approved_by,approved_date from wo_non_ord_samp_booking_mst a, approval_history b where a.id=b.mst_id and a.booking_no='$data[2]' and b.entry_form=9 and a.status_active =1 and a.is_deleted=0 order by b.id desc ");
	             $approved_by= $user_arr[$nameArray_approved[0][csf("approved_by")]];
	             $approved_date= change_date_format($nameArray_approved[0][csf("approved_date")]);
	              ?>
	             </td>
	     </tr>




	        <tr>
	            <td colspan="5">
					<?

	                    $val=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
						//echo ($val[0][csf('plot_no')])?   $val[0][csf('plot_no')].',': "";
						echo ($val[0][csf('level_no')])?  $val[0][csf('level_no')].',': "";
						echo ($val[0][csf('road_no')])?   $val[0][csf('road_no')].',': "";
						echo ($val[0][csf('block_no')])?  $val[0][csf('block_no')].',': "";
						echo ($val[0][csf('city')])?      $val[0][csf('city')].',': "";
						echo ($val[0][csf('zip_code')])?  $val[0][csf('zip_code')].',': "";
						echo ($val[0][csf('province')])?  $val[0][csf('province')].',': "";
						echo($val[0][csf('country_id')])? $country_arr[$val[0][csf('country_id')]]: "";
						echo ($val[0][csf('email')])?    "</br>". $val[0][csf('email')].',': "</br>";
						echo($val[0][csf('website')])?    $val[0][csf('website')]: "";
						$sql="SELECT id, requisition_number, requisition_number_prefix_num, style_ref_no, buyer_name, season, product_dept, dealing_marchant, agent_name, buyer_ref, bh_merchant, estimated_shipdate, team_leader, season_buyer_wise, remarks, quotation_id, sample_stage_id, requisition_date, material_delivery_date, inserted_by from sample_development_mst where  id='$data[1]' and entry_form_id=203 and  is_deleted=0  and status_active=1";
						$dataArray=sql_select($sql);
						$barcode_no=$dataArray[0][csf('requisition_number')];
						$inserted_by=$dataArray[0][csf('inserted_by')];
	 					if($dataArray[0][csf("sample_stage_id")]==1)
	 					{
	 					  	$job_lib=return_library_array( "SELECT a.id,min(b.shipment_date) as shipment_date  from wo_po_details_master  a,wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and a.company_name='$data[0]' GROUP BY a.id", "id", "shipment_date"  );
	 					}

	 					$sqls="SELECT style_desc,supplier_id,revised_no,buyer_req_no,source,team_leader,dealing_marchant,pay_mode  from wo_non_ord_samp_booking_mst where  booking_no='$data[2]'  and  is_deleted=0  and status_active=1";
	 					$dataArray_book=sql_select($sqls);
						// $style_desc= $dataArray_book[0][csf('style_desc')];


	                ?>
	            </td>

	        </tr>
	        <tr>
	            <td colspan="3" style="font-size:medium"><strong> <b>Sample Program Without Order</b></strong></td>
	             <td colspan="2" id="" width="250"><b>Approved By :<? echo $approved_by ?></b> </br><b>Approved Date :<? echo $approved_date ?></b> </td>

	        </tr>


	        </table>

	        <table width="1100" cellspacing="0" border="0" class="rpt_table" style="font-family: Arial Narrow;" >
	        	<tr>
	        		<td colspan="4" align="left"><strong>Requisition No. &nbsp;<? echo $dataArray[0][csf("requisition_number")]; ?> </strong></td>
	        		<td ><strong>Revise:</strong></td>
	        		<td ><? echo $dataArray_book[0][csf('revised_no')];?></td>
	        		<td colspan="2"></td>
	        	</tr>

	        	<tr>
	        	<td width="100"><strong>Booking No: </strong></td>
	        		<td width="130" align="left"><? echo $data[2];?></td>
	        		<td width="120"  align="left">&nbsp;&nbsp;<strong>Style Ref:</strong></td>
	        		<td width="110">&nbsp;<? echo $dataArray[0][csf('style_ref_no')];?></td>
	        		<td width="110"   align="left"><strong>Sample Sub Date:</strong></td>
	        		<td width="100" ><? echo change_date_format($dataArray[0][csf('material_delivery_date')]);?></td>
	        		<td width="110"   align="left"><strong>Style Desc:</strong></td>
	        		<td   ><? echo $dataArray_book[0][csf('style_desc')];?></td>


	        	</tr>
	        	<tr>
	        		<td width="100"><strong>Buyer Name: </strong></td>
	        		<td width="130" align="left"><? echo $buyer_library[$dataArray[0][csf('buyer_name')]];?></td>
	        		<td width="120" style="word-break:break-all;" align="left">&nbsp;&nbsp;<strong>Season:</strong></td>
	        		<td width="110">&nbsp;<? echo $season_arr[$dataArray[0][csf('season')]];?></td>
	        		<td width="110"><strong>BH Merchandiser:</strong></td>
	        		<td width="100"><? echo $dataArray[0][csf('bh_merchant')];?></td>
	        		<td width="110"><strong>Remarks/Desc:</strong></td>
	        		<td   style="word-wrap: break-word;word-break: break-all;" ><? echo $dataArray[0][csf('remarks')];?></td>

	        	</tr>
	        	<tr>
	        		<td width="100"   align="left"><strong>Buyer Ref:</strong></td>
	        		<td width="130" ><? echo $dataArray[0][csf('buyer_ref')];?></td>
	        		<td width="120"  >&nbsp;&nbsp;<strong>Product Dept:</strong></td>
	        		<td width="110" ><? echo $product_dept[$dataArray[0][csf('product_dept')]];?></td>
	        		<td width="110"  ><strong>Supplier</strong></td>
	        		<td width="100" ><? 
					
						   if($dataArray_book[0][csf('pay_mode')]==1 || $dataArray_book[0][csf('pay_mode')]==2){
							echo $supplier_library[$dataArray_book[0][csf('supplier_id')]];
						   }elseif($dataArray_book[0][csf('pay_mode')]==3 || $dataArray_book[0][csf('pay_mode')]==4 || $dataArray_book[0][csf('pay_mode')]==4){
							echo $company_library[$dataArray_book[0][csf('supplier_id')]];
						   }

					?></td>
	        		<td width="110"><strong>Est. Ship Date</strong></td>
	        		<td ><? echo change_date_format($dataArray[0][csf('estimated_shipdate')]); ?></td>

	        	</tr>
	            <tr>
	        		<td width="100"><strong>Team Leader</strong></td>
	        		<td width="130" ><? echo $team_leader_arr[$dataArray_book[0][csf('team_leader')]];?></td>
	        		<td width="120"  >&nbsp;&nbsp;<strong>Dealing Merchandiser:</strong></td>
	        		<td width="110" ><? echo $dealing_merchant_library[$dataArray_book[0][csf('dealing_marchant')]];?></td>
	        		<td width="110"  ><strong>Sample Stage</strong></td>
	        		<td width="100" ><? echo $sample_stage[$dataArray[0][csf('sample_stage_id')]];?></td>
	        		<td width="110">&nbsp;</td>
	        		<td >&nbsp;</td>

	        	</tr>
	        </table>

	        <table width="1100" cellspacing="0" border="0"   style="font-family: Arial Narrow;" >
	         <tr>
	        	<td width="250" align="left" valign="top" colspan="2">
	            <table align="left" cellspacing="0" border="0" width="90%" >

	        	</table>
				</td>
				</tr>



	         <tr> <td colspan="6">&nbsp;</td></tr>
	        	<tr>
	        	<td width="250" align="left" valign="top" colspan="2">
	        	<?
				 $sql_sample_dtls= "SELECT a.sample_name, a.article_no, a.sample_color from sample_development_dtls a , lib_color b  where a.status_active=1 and a.is_deleted=0 and a.entry_form_id=203  and sample_mst_id='$data[1]' and b.status_active=1 and a.status_active=1 and b.id=a.sample_color  group by a.sample_name,a.article_no,a.sample_color";

				foreach(sql_select($sql_sample_dtls) as $key=>$value)
				{
					if($sample_wise_article_no[$value[csf("sample_name")]][$value[csf("sample_color")]]=="")
					{
						$sample_wise_article_no[$value[csf("sample_name")]][$value[csf("sample_color")]]=$value[csf("article_no")];
					}
					else
					{
						if(!in_array($value[csf("article_no")], $sample_wise_article_no))
						{
							$sample_wise_article_no[$value[csf("sample_name")]][$value[csf("sample_color")]].= ', '.$value[csf("article_no")];
						}

					}
				}
				 $color_sql="SELECT b.color_id ,b.process_loss_percent ,b.fabric_color,b.contrast,b.mst_id,b.dtls_id from  sample_development_rf_color b where b.status_active=1 and b.is_deleted=0 and b.qnty>0 and b.mst_id='$data[1]' ";
				 $color_res=sql_select($color_sql);
				 $color_rf_data=array();
				 foreach ($color_res as $val) {
				 	$color_rf_data[$val[csf('dtls_id')]][$val[csf('color_id')]][$val[csf('fabric_color')]]['process_loss_percent']=$val[csf('process_loss_percent')];
				 	$color_rf_data[$val[csf('dtls_id')]][$val[csf('color_id')]][$val[csf('fabric_color')]]['contrast']=$val[csf('contrast')];
				 }

			 $sql_fab="SELECT a.sample_name, a.gmts_item_id, c.gmts_color as color_id,   a.delivery_date, a.fabric_description, a.body_part_id, a.fabric_source, a.remarks_ra, a.gsm, a.dia, a.color_type_id, a.width_dia_id, a.uom_id, c.grey_fabric as grey_fab_qnty, c.fabric_color,c.dtls_id,c.finish_fabric as qnty,a.id,a.determination_id from sample_development_fabric_acc a, wo_non_ord_samp_booking_dtls c where  a.id=c.dtls_id  and a.form_type=1  and c.grey_fabric>0 and a.status_active=1 and a.is_deleted=0  and c.status_active=1 and c.is_deleted=0  and a.sample_mst_id='$data[1]' ";
			
				 $sql_fab_arr=array();
				 foreach(sql_select($sql_fab) as $vals)
				 {
				 	$contrast=$color_rf_data[$vals[csf('dtls_id')]][$vals[csf('color_id')]][$vals[csf('fabric_color')]]['contrast'];
			 		$process_loss_percent=$color_rf_data[$vals[csf('dtls_id')]][$vals[csf('color_id')]][$vals[csf('fabric_color')]]['process_loss_percent'];

					$article_no=rtrim($sample_wise_article_no[$vals[csf("sample_name")]][$vals[csf("color_id")]],',');
					$article_no=implode(",",array_unique(explode(",",$article_no)));
					$sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$contrast]["qnty"]+=$vals[csf("qnty")];
				 	$sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$contrast]["process_loss_percent"]=$process_loss_percent;

				 	$sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$contrast]["grey_fab_qnty"]+=$vals[csf("grey_fab_qnty")];

				 	$sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$contrast]["delivery_date"] =change_date_format($vals[csf("delivery_date")]);

				 	$sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$contrast]["fabric_source"] =$vals[csf("fabric_source")];

				 	$sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$contrast]["uom_id"] =$vals[csf("uom_id")];
					$sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$contrast]["dia"] =$vals[csf("dia")];

				 	$sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$contrast]["width_dia_id"] =$vals[csf("width_dia_id")];

				 	$sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$contrast]["remarks"] =$vals[csf("remarks_ra")];
				 	$sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$contrast]["color_type_id"] =$vals[csf("color_type_id")];
				 }
				 $sample_item_wise_span=array(); $sample_item_wise_color_span=array();

			  foreach($sql_fab_arr as $article_no=>$article_data) 
	          {
				$article_no_span=0;
				foreach($article_data as $sample_type_id=>$sampleType_data) 
	        	{
				$sample_type_span=0;
				foreach($sampleType_data as $gmts_color_id=>$gmts_color_data)
	        	{
					$sample_span=0;
	        		foreach($gmts_color_data as $body_part_id=>$body_part_data)
	        		{
	        			
						//echo $gmts_color_id.'d';

	        			foreach($body_part_data as $fab_id=>$fab_desc_data)
	        			{
	        				//$kk=0;
	        				foreach($fab_desc_data as $colorType=>$colorType_data)
	        				{

	        					foreach($colorType_data as $gsm_id=>$gsm_data)
	        					{
	        						foreach($gsm_data as $dia_id=>$dia_data)
	        						{
									   foreach($dia_data as $dia_type_id=>$diatype_data)
	        						   {

	        							foreach($diatype_data as $contrast_id=>$value)
	        							{
	        								$sample_span++;$sample_type_span++;$article_no_span++;
	        								//$kk++;

	        							}
											$article_wise_span[$article_no]=$article_no_span;
											$sample_item_wise_span[$article_no][$sample_type_id]=$sample_type_span;
											$sample_item_wise_color_span[$article_no][$sample_type_id][$gmts_color_id]=$sample_span;
									  }
	        						}

	        					}


	        				}

	        			}

	        		  }
					 }

	        		}
				}
	        	//echo "<pre>";
	        	//print_r($sample_item_wise_color_span);die;
				// echo "<pre>"; print_r($sample_wise_article_no);die;

				?>
				<table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all">
					<thead>
					<tr>
						<th colspan="19">Required Fabric</th>
					</tr>
						<tr>
							<th width="30">Sl</th>
							<th width="90">ALT / [C/W]</th>
							<th width="110">Sample Type</th>
							<th width="80">Gmt Color</th>
							<th width="80">Fab. Deli Date</th>
							<th width="120">Body Part</th>
							<th width="200">Fabric Desc & Composition</th>
							<th width="80">Color Type</th>
							<th width="80">Fab.Color</th>
							<th width="40">Item Size</th>
							<th width="55">GSM</th>
							<th width="55">Dia</th>
							<th width="60">Width/Dia</th>
							<th width="40">UOM</th>
							<th width="60">Grey Qnty</th>
							<th width="40">P. Loss</th>
							<th width="80">Fin Fab Qnty</th>
							<th width="80">Fabric Source</th>
							<th width="80">Remarks</th>

						</tr>
					</thead>
					<tbody>
						<?
						$p=1;
						$total_finish=0;
						$total_grey=0;
						$total_process=0;
			 foreach($sql_fab_arr as $article_no=>$article_data) 
	         {
				$aa=0;
				foreach($article_data as $sample_type_id=>$sampleType_data) 
	        	{
				$nn=0;
				foreach($sampleType_data as $gmts_color_id=>$gmts_color_data)
	        	{
					$cc=0;
	        		foreach($gmts_color_data as $body_part_id=>$body_part_data)
	        		{
	        			
						//echo $gmts_color_id.'d';

	        			foreach($body_part_data as $fab_id=>$fab_desc_data)
	        			{
	        				//$kk=0;
	        				foreach($fab_desc_data as $colorType=>$colorType_data)
	        				{

	        					foreach($colorType_data as $gsm_id=>$gsm_data)
	        					{
	        						foreach($gsm_data as $dia_id=>$dia_data)
	        						{

	        							foreach($dia_data as $dia_type=>$diatype_data)
	        							{
											foreach($diatype_data as $contrast_id=>$value)
	        							    {

															 
														?>
														<tr>


																
																<?
															if($aa==0)
															{
																?>
	                                                            <td  rowspan="<? echo $article_wise_span[$article_no];?>"  align="left" style="word-wrap: break-word;word-break: break-all;"><? echo $p;$p++;?></td>
	                                                            <td   rowspan="<? echo $article_wise_span[$article_no];?>" align="center"><? echo $article_no;?></td>
	                                                            <?
															}
															if($nn==0)
															{
																?>
																
																<td   rowspan="<? echo $sample_item_wise_span[$article_no][$sample_type_id];?>"  align="center"><? echo $sample_library[$sample_type_id]; ?></td>
																
																<?
																
															}
															if($cc==0)
															{
															 ?>
	                                                         <td   align="center" rowspan="<? echo $sample_item_wise_color_span[$article_no][$sample_type_id][$gmts_color_id];?>"><? echo $color_library[$gmts_color_id];?> </td>
	                                                          <td   rowspan="<? echo $sample_item_wise_color_span[$article_no][$sample_type_id][$gmts_color_id];?>" align="center" ><? echo $value["delivery_date"];?> </td>
	                                                         <?
	                                                        } ?>

															
															 <td width="120"  align="center"><? echo $body_part[$body_part_id];?></td>
															 <td  align="center"><? echo $fab_id;?></td>
															 <td  align="center"> <? echo $color_type[$colorType]; ?></td>
															 <td  align="center"><? echo $contrast_id; ?></td>
															 <td  align="center"><? echo $value["item_size"]; ?></td>
															 <td  align="center"><? echo $gsm_id; ?></td>
															 <td  align="center"><? echo $value["dia"]; ?></td>
															 <td  align="center"><? echo $fabric_typee[$dia_type]; ?></td>
															 <td   align="center"><? echo $unit_of_measurement[$value["uom_id"]];?></td>

															 <td align="right"><? echo number_format($value["grey_fab_qnty"],2);?></td>
															 <td align="right"><? echo $value["process_loss_percent"];?></td>
															 <td align="right"><? echo number_format($value["qnty"],2);?></td>

															 <td align="center"><? echo $fabric_source[$value["fabric_source"]];?></td>
															 <td  align="center"><? echo $value["remarks"];?></td>

														</tr>


														<?
														$nn++;$cc++;$aa++;
			        									//$i++;
														$total_finish +=$value["qnty"];
														$total_grey +=$value["grey_fab_qnty"];
														$total_process +=$value["process_loss_percent"];
													}
												}
											}
										}
									}
								}
							  }
							}
						}
			 		}

						?>

						<tr>
							<th colspan="14" align="right"><b>Total</b></th>
							<th width="80" align="right"><? echo number_format($total_grey,2);?></th>
							<th width="40" align="right">&nbsp;</th>
							<th width="60" align="right"><? echo number_format($total_finish,2);?></th>
							<th width="80" colspan="2"> </th>

						</tr>

					</tbody>



				</table><br/><?

				$sample_color_arr=return_library_array( "select id, sample_color from sample_development_dtls", "id", "sample_color"  );
	                      $sql_qry="SELECT id,sample_mst_id,sample_name,gmts_item_id,smv,article_no,sample_color,sample_prod_qty,submission_qty,delv_start_date,delv_end_date,sample_charge,sample_curency,sent_to_buyer_date,comments from sample_development_dtls where status_active =1 and is_deleted=0 and entry_form_id=203 and sample_mst_id='$data[1]' order by id asc";
						    $sql_qry_color="SELECT a.id,a.sample_mst_id,a.sample_name,a.gmts_item_id,a.smv,a.article_no,a.sample_color,a.sample_prod_qty,a.submission_qty,a.delv_start_date,a.delv_end_date,a.sample_charge,a.sample_curency,a.sent_to_buyer_date,a.comments,c.dtls_id,c.size_id,c.bh_qty,c.self_qty,c.test_qty,c.plan_qty,c.dyeing_qty from sample_development_dtls a,sample_development_size c where a.id=c.dtls_id and  a.status_active =1 and a.is_deleted=0 and a.entry_form_id=203 and a.sample_mst_id='$data[1]' order by a.id asc";
						 $size_type_arr=array(1=>"bh_qty",2=>"Self Qty",3=>"Test qty",4=>"Plan Qty",5=>"Dyeing Qty");
						 $color_size_arr=array();
						  foreach(sql_select($sql_qry_color) as $vals)
						 {
								if($vals[csf("bh_qty")]>0)
								{
								$color_size_arr[1][$vals[csf("size_id")]]='Bh Qty';
								$bh_qty=$vals[csf("bh_qty")];
								$color_size_dtls_qty_arr[1][$vals[csf("id")]][$vals[csf("size_id")]]=$bh_qty;
								}
								if($vals[csf("self_qty")]>0)
								{
								$color_size_arr[2][$vals[csf("size_id")]]='self qty';
								$color_size_dtls_qty_arr[2][$vals[csf("id")]][$vals[csf("size_id")]]=$vals[csf("self_qty")];
								}
								if($vals[csf("test_qty")]>0)
								{
								$color_size_arr[3][$vals[csf("size_id")]]='test qty';
								$color_size_dtls_qty_arr[3][$vals[csf("id")]][$vals[csf("size_id")]]=$vals[csf("test_qty")];
								}
								if($vals[csf("plan_qty")]>0)
								{
								$color_size_arr[4][$vals[csf("size_id")]]='plan qty';
								//$size_plan_arr[$vals[csf("size_id")]]=$vals[csf("size_id")];
								$color_size_dtls_qty_arr[4][$vals[csf("id")]][$vals[csf("size_id")]]=$vals[csf("plan_qty")];

								}
								if($vals[csf("dyeing_qty")]>0)
								{
								$color_size_arr[5][$vals[csf("size_id")]]='Dyeing qty';
								$color_size_dtls_qty_arr[5][$vals[csf("id")]][$vals[csf("size_id")]]=$vals[csf("dyeing_qty")];

								}

							}
							$tot_row=count($color_size_arr);
							$result=sql_select($sql_qry);

				?>


	            	<table align="left" cellspacing="0" border="1" width="100%" class="rpt_table" rules="all">
	            	<thead>
	            			<tr>
	                            <td width="150" colspan="<? echo 12+$tot_row;?>" align="center"><strong>Sample Details</td>
	                        </tr>
	                        <tr>
									<th width="30" rowspan="2" align="left">Sl</th>
									<th width="100" rowspan="2" align="center">Sample Name</th>
									<th width="120" rowspan="2" align="center">Garment Item</th>

									<th width="55" rowspan="2" align="center">ALT / [C/W]</th>
									<th width="70" rowspan="2" align="center">Color</th>
	                                <?
									$tot_row_td=0;
	                                foreach($color_size_arr as $type_id=>$val)
									{ ?>
										<th width="45" align="center" colspan="<? echo count($val);?>"> <?
	                                 		  echo  $size_type_arr[$type_id];
										?></th>
	                                    <?

									}
									?>
									<th rowspan="2" width="55" align="center">Total</th>
									<th rowspan="2" width="55" align="center">Submn Qty</th>
									<th rowspan="2"  width="70" align="center">Buyer Submisstion Date</th>
									<th rowspan="2"  width="70" align="center">Remarks</th>
	                         </tr>
	                         <tr>
	                         	<?
	                            foreach($color_size_arr as $type_id=>$data_size)
								{
									foreach($data_size as $size_id=>$data_val)
									{
									$tot_row_td++;
									?>
										<th width="40" align="center"><? echo $size_library[$size_id]; ?></th>
										<?
									}
	                         	}

	                         	?>
	                         </tr>

	            	</thead>
	                    <tbody>

	                        <?

	 						$i=1;$k=0;
	 						$gr_tot_sum=0;
	 						$gr_sub_sum=0;
							foreach($result as $row)
							{
								$dtls_ids=$row[csf('id')];
								 //$size_select=sql_select("SELECT  size_id,total_qty  from sample_development_size where  mst_id='$data[1]' and status_active=1 and is_deleted=0 and dtls_id='$dtls_ids' ");
	 							$prod_sum=$prod_sum+$row[csf('sample_prod_qty')];
								$sub_sum=$sub_sum+$row[csf('submission_qty')];

							?>
	                        <tr>
	                            <?
	 							$k++;
								?>
	                            <td  align="left"><? echo $k;?></td>
	                            <td  align="left"><? echo $sample_library[$row[csf('sample_name')]];?></td>
	                            <td  align="left"><? echo $garments_item[$row[csf('gmts_item_id')]];?></td>

	                            <td   align="left"><? echo $row[csf('article_no')];?></td>
	                            <td width="70" align="left"><? echo $color_library[$row[csf('sample_color')]];?></td>


	                            <?
	                            $total_sizes_qty=0;
	                            $total_sizes_qty_subm=0;
	                          	foreach($color_size_arr as $type_id=>$data_size)
								{
									foreach($data_size as $size_id=>$data_val)
									{
									$size_qty=$color_size_dtls_qty_arr[$type_id][$dtls_ids][$size_id];
	                            	?>
	                            	<td align="right"><? echo $size_qty; ?></td>
	                            	<?
										if($type_id==1)
										{
										$total_sizes_qty_subm+=$size_qty;
										}
										$total_sizes_qty+=$size_qty;
									}
	                            }
	                            ?>
	                            <td align="right"><? echo $total_sizes_qty;?></td>
	                            <td align="right"><? echo $total_sizes_qty_subm;?></td>
	                            <td   align="left"><? echo change_date_format($row[csf('sent_to_buyer_date')]);?> </td>
	                            <td   align="left"><? echo $row[csf('comments')];?> </td>
	                            <?
	                            $gr_tot_sum+=$total_sizes_qty;
	 							$gr_sub_sum+=$total_sizes_qty_subm;
	                        }
							?>
	                        </tr>
								<tr>
										<td colspan="<? echo 5+$tot_row_td; ?>" align="right"><b>Total</b></td>
	 									<td   align="right"><b><? echo number_format($gr_tot_sum,2);?> </b></td>
	 									<td  align="right"><b><? echo number_format($gr_sub_sum,2);?> </b></td>
										<td colspan="2"></td>
								</tr>
	                    </tbody>
	                    <tfoot>
	                     </tfoot>
	               </table>
	             </td>
       		 </tr>
      	  </table>
		<br>
        <table  style="font-size:14px" class="rpt_table" width="600" align="left" border="1" cellpadding="0" cellspacing="0" rules="all">
            	<thead>
            		<tr>
                      <td colspan="3" align="center">Yarn Required Summary</td>
                    </tr>
                	<tr align="center">
                    	<th align="left" width="30">Sl</th>
                    	<th align="center">Yarn Desc.</th>
                         <th align="center">Req. Qty</th> 
                    </tr>
                </thead>
                <tbody>
                <?
				$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count"  );
				$lib_supllier_arr=return_library_array( "select supplier_id,booking_no from wo_non_ord_samp_booking_mst where booking_no='$data[2]' and entry_form_id=140", "booking_no", "supplier_id"  );
				$tot_req_qty=0;

				
				$sql_yarn="select b.count_id,b.copm_one_id,b.percent_one,b.type_id,sum(b.cons_qnty) as  cons_qnty from  sample_development_yarn_dtls b where  b.status_active=1  and b.mst_id='$data[1]' and b.determin_id in (select determination_id from sample_development_fabric_acc  where status_active=1 and sample_mst_id='$data[1]' and form_type=1) group by b.count_id,b.copm_one_id,b.percent_one,b.type_id";
				//echo $sql_yarn;
				$data_array=sql_select($sql_yarn);

				
				if ( count($data_array)>0)
				{
					$l=1;
					foreach( $data_array as $key=>$row )
					{
						$yarn_des=$lib_yarn_count[$row[csf("count_id")]].','.$composition[$row[csf("copm_one_id")]].','.$row[csf("percent_one")].'%,'.$yarn_type[$row[csf("type_id")]];
						?>
                        	<tr>
                                <td> <? echo $l;?> </td>
                                <td> <? echo $yarn_des; ?> </td>
                                <td align="right"> <? echo number_format($row[csf("cons_qnty")],2); ?> </td>
                            </tr>
                        <?
                        $l++;
						$tot_req_qty+=$row[csf("cons_qnty")];
					}
				}

				?>
                <tr>
					<th  colspan="2" align="right"><b>Total</b></th>
					<th  align="right"><? echo number_format($tot_req_qty,2);?></th>
				</tr>
            </tbody>
        </table>
        <br>
        <br>
        <?
        	$sample_stripe_data=sql_select("SELECT a.body_part_id, b.contrast, b.color_id, b.grey_fab_qnty,c.id as strip_mst_id, c.stripe_color, c.measurement, c.uom, c.fabreq, c.yarn_dyed FROM sample_development_fabric_acc a join sample_development_rf_color b on a.id=b.dtls_id join wo_sample_stripe_color c on a.id=c.sample_fab_dtls_id and b.color_id=c.color_number_id where a.status_active=1 and a.is_deleted=0 and a.color_type_id in (2,3,4,6,31,32,33,34) and a.form_type=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.sample_mst_id=$data[1]");
        	$stripe_color_summ=array();
        	foreach ($sample_stripe_data as $row) {
        		$key=$row[csf('body_part_id')].'*'.$row[csf('color_id')];
        		$sample_stripe_arr[$key]['body_part_id'] = $row[csf('body_part_id')];
        		$sample_stripe_arr[$key]['fabric_color'] = $row[csf('color_id')];
        		$sample_stripe_arr[$key]['fabric_qty'] = $row[csf('grey_fab_qnty')];
				
        		$sample_stripe_arr[$key]['stripe_color'][$row[csf('strip_mst_id')]][$row[csf('stripe_color')]]['color'] = $row[csf('stripe_color')];
        		$sample_stripe_arr[$key]['stripe_color'][$row[csf('strip_mst_id')]][$row[csf('stripe_color')]]['measurement'] = $row[csf('measurement')];
        		$sample_stripe_arr[$key]['stripe_color'][$row[csf('strip_mst_id')]][$row[csf('stripe_color')]]['uom'] = $row[csf('uom')];
        		$sample_stripe_arr[$key]['stripe_color'][$row[csf('strip_mst_id')]][$row[csf('stripe_color')]]['qty'] = $row[csf('fabreq')];
        		$sample_stripe_arr[$key]['stripe_color'][$row[csf('strip_mst_id')]][$row[csf('stripe_color')]]['yarn_dyed'] = $row[csf('yarn_dyed')];
        		
				$stripe_color_summ[$row[csf('stripe_color')]] += $row[csf('fabreq')];
        	}
        ?>
        <div style="width:1000px;">
	        <table align="left" cellspacing="0" border="1" style="width:800px;float: left; margin-top: 5px;font-size:14px" class="rpt_table" rules="all">
	        	<thead>
	        		<tr>
	        			<th colspan="9">Stripe Details</th>
	        		</tr>
	        		<tr>
	        			<th width="30">SL</th>
	        			<th width="100">Body Part</th>
	        			<th width="60">Fabric Color</th>
	        			<th width="60">Fabric Qty(KG)</th>
	        			<th width="60">Stripe Color</th>
	        			<th width="60">Stripe Measurement</th>
	        			<th width="60">Stripe Uom</th>
	        			<th width="60">Qty.(KG)</th>
	        			<th width="60">Y/D Req.</th>
	        		</tr>
	        	</thead>
	        	<tbody>
	        		<? 
	        		if(count($sample_stripe_arr)>0){
		        		$sl=1;
		        		foreach ($sample_stripe_arr as $sdata) {
		        			$rowspan = count($sdata['stripe_color']);
		        			$i=1;
		        			foreach ($sdata['stripe_color'] as $stripe_mst) {
								foreach ($stripe_mst as $stripe_data) {
		        				if($i==1){
		        					$total_fabric += $sdata['fabric_qty'];
		        					$total_stripe_fabric += $stripe_data['qty'];
		        				?>
		        				<tr>
				        			<td rowspan="<?=$rowspan?>" align="left"><?= $sl; ?></td>
				        			<td rowspan="<?=$rowspan?>"><?= $body_part[$sdata['body_part_id']]; ?></td>
				        			<td rowspan="<?=$rowspan?>"><?= $color_library[$sdata['fabric_color']]; ?></td>
				        			<td align="right" rowspan="<?=$rowspan?>"><?= $sdata['fabric_qty']; ?></td>
				        			<td><?= $color_library[$stripe_data['color']]; ?></td>
				        			<td align="right"><?= $stripe_data['measurement']; ?></td>
				        			<td><?= $unit_of_measurement[$stripe_data['uom']]; ?></td>
				        			<td align="right"><?= $stripe_data['qty']; ?></td>
				        			<td><?= $yes_no[$stripe_data['yarn_dyed']]; ?></td>
				        		</tr>
		        				<?
		        					$i++;
		        				}
		        				else{
		        					$total_stripe_fabric += $stripe_data['qty'];
		        					?>
		        						<tr>
		        							<td><?= $color_library[$stripe_data['color']]; ?></td>
						        			<td align="right"><?= $stripe_data['measurement']; ?></td>
						        			<td><?= $unit_of_measurement[$stripe_data['uom']]; ?></td>
						        			<td align="right"><?= number_format($stripe_data['qty'],2); ?></td>
						        			<td><?= $yes_no[$stripe_data['yarn_dyed']]; ?></td>
		        						</tr>
		        					<?
		        				}
		        			}
		        			$sl++;
							}
		        		}
	        		} else { ?>
	        		<tr>
	        			<tr>
		        			<td>1</td>
		        			<td></td>
		        			<td></td>
		        			<td></td>
		        			<td></td>
		        			<td></td>
		        			<td></td>
		        			<td></td>
		        			<td></td>
		        		</tr>
	        		</tr>
	        		<? } ?>
	        	</tbody>
	        	<tfoot>
	        		<tr>
	        			<th colspan="3">Total</th>
	        			<th align="right"><?= number_format($total_fabric,2) ?></th>
	        			<th></th>
	        			<th></th>
	        			<th></th>
	        			<th align="right"><?= number_format($total_stripe_fabric,2) ?></th>
	        			<th></th>
	        		</tr>
	        	</tfoot>
	        </table>
	        <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:180px; margin-left: 2px; float: right;  margin-top: 5px;font-size:14px" rules="all">
		        <thead>
		        	<tr>
		        		<th colspan="3">Stripe Color wise Summary</th>
		        	</tr>
		        	<tr>
		        		<th>SL</th>
		        		<th>Stripe Color</th>
		        		<th>Qty.(KG)</th>
		        	</tr>
		        </thead>
		        <tbody>
		        	<?
		        	if(count($stripe_color_summ)>0){
			        	$sl=1;
			        	foreach ($stripe_color_summ as $color_id => $value) {
			        	 	$total_fabric_qty+= $value;
			        	?>
			        	<tr>
			        		<td><?= $sl ?></td>
			        		<td><?= $color_library[$color_id]; ?></td>
			        		<td><?= number_format($value,2) ?></td>
			        	</tr>
			        	<? $sl++;
			        	}
		        	} else { ?>
		        		<tr>
			        		<td>1</td>
			        		<td></td>
			        		<td></td>
			        	</tr>
		        	<? } ?>
		        </tbody>
		        <tfoot>
		        	<tr>
		        		<th colspan="2">Total</th>
		        		<th><?= number_format($total_fabric_qty,2); ?></th>
		        	</tr>
		        </tfoot>
	        </table>
        </div>
        <?
			$coller_cuff_data=sql_select("SELECT a.sample_color, a.size_id, a.item_size, a.qnty_pcs, c.body_part_type from sample_requisition_coller_cuff a join sample_development_fabric_acc b on b.id=a.dtls_id join lib_body_part c on b.body_part_id=c.id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.sample_mst_id=$data[1]");
			$coller_data_arr=array(); $cuff_data_arr=array();
			foreach ($coller_cuff_data as $row) {
				if($row[csf('body_part_type')]==40)
				{
					$coller_size_arr[$row[csf('size_id')]]=$row[csf('size_id')];
					$color_size_data[$row[csf('size_id')]]['item_size']=$row[csf('item_size')];
					$color_color_data[$row[csf('sample_color')]][$row[csf('size_id')]]['qnty_pcs']=$row[csf('qnty_pcs')];
				}
				if($row[csf('body_part_type')]==50)
				{
					$cuff_size_arr[$row[csf('size_id')]]=$row[csf('size_id')];
					$cuff_size_data[$row[csf('size_id')]]['item_size']=$row[csf('item_size')];
					$cuff_color_data[$row[csf('sample_color')]][$row[csf('size_id')]]['qnty_pcs']=$row[csf('qnty_pcs')];
				}
			} 
			/*echo '<pre>';
			print_r($color_color_data); die;*/
        ?>
        <div style="width:1000px; margin-top: 10px;">
        	<table align="left" cellspacing="0" border="1" style="width:495px;float: left; margin-top: 5px;font-size:14px" class="rpt_table" rules="all">
        		<thead>
        			<tr>
        				<th colspan="<? echo count($coller_size_arr)+2;  ?>">Collar - Color Size Brakedown in Pcs.</th>
        			</tr>
        			<tr>
        				<th>Size</th>
        				<? foreach ($coller_size_arr as $size_id) { ?>
        				<th><?= $size_library[$size_id]; ?></th>
        				<? } ?>
        				<th rowspan="2">Total</th>
        			</tr>
        		</thead>
        		<tbody>
        			<tr>
        			<th>Collar Size</th>
        				<?
        					foreach ($coller_size_arr as $size_id) {
        				 ?>
        				 <td><?= $color_size_data[$size_id]['item_size'] ?></td>        				
        				<? }         				
        			?></tr>
        			<? foreach ($color_color_data as $fabric_color =>$size_data) { ?>
        				<tr>
        					<td><?= $color_library[$fabric_color]?></td><?
        					$total_size_qty=0;
        					foreach ($coller_size_arr as $size_id) {
        						$total_size_qty+=$size_data[$size_id]['qnty_pcs'];
        						$total_size_arr[$size_id]+=$size_data[$size_id]['qnty_pcs'];
        				 ?>
        				 <td><?= ($size_data[$size_id]['qnty_pcs']) ? $size_data[$size_id]['qnty_pcs'] : 0; ?></td>        				
        				<? } ?>
        					<td><? echo $total_size_qty; ?></td>
        				</tr><?
        			}
        			 ?>        			
        		</tbody>
        		<tfoot>
        			<tr>
        			<th align="right">Total</th>
        			<?
    				foreach ($coller_size_arr as $size_id) {
    					$grand_size_qty_total+= $total_size_arr[$size_id]
    				 ?>
    					<th align="left"><?  echo $total_size_arr[$size_id]  ?></th>
    				<? }
        			?>
        			<th align="left"><?= $grand_size_qty_total ?></th>
        			</tr>
        		</tfoot>
        	</table>
        	<table align="left" cellspacing="0" border="1" style="width:495px;float: right; margin-top: 5px;font-size:14px" class="rpt_table" rules="all">
        		<thead>
        			<tr>
        				<th colspan="<? echo count($cuff_size_arr)+2;  ?>">Cuff - Color Size Brakedown in Pcs.</th>
        			</tr>
        			<tr>
        				<th>Size</th>
        				<? foreach ($cuff_size_arr as $size_id) { ?>
        				<th><?= $size_library[$size_id]; ?></th>
        				<? } ?>
        				<th rowspan="2">Total</th>
        			</tr>
        		</thead>
        		<tbody>
        			<tr>
        			<th>Cuff Size</th>
        				<?
        					foreach ($cuff_size_arr as $size_id) {
        				 ?>
        				 <td><?= $cuff_size_data[$size_id]['item_size'] ?></td>        				
        				<? }         				
        			?></tr>
        			<? foreach ($cuff_color_data as $fabric_color =>$size_data) { ?>
        				<tr>
        					<td><?= $color_library[$fabric_color]?></td><?
        					$total_size_qty=0;
        					foreach ($cuff_size_arr as $size_id) {
        						$total_size_qty+=$size_data[$size_id]['qnty_pcs'];
        						$total_cuff_size_arr[$size_id]+=$size_data[$size_id]['qnty_pcs'];
        				 ?>
        				 <td><?= ($size_data[$size_id]['qnty_pcs']) ? $size_data[$size_id]['qnty_pcs'] : 0; ?></td>        				
        				<? } ?>
        					<td><? echo $total_size_qty; ?></td>
        				</tr><?
        			}
        			 ?>        			
        		</tbody>
        		<tfoot>
        			<tr>
        			<th align="right">Total</th>
        			<?
    				foreach ($cuff_size_arr as $size_id) {
    					$grand_qty_total+= $total_cuff_size_arr[$size_id]
    				 ?>
    					<th align="left"><?  echo $total_cuff_size_arr[$size_id];  ?></th>
    				<? }
        			?>
        			<th align="left"><?= $grand_qty_total ?></th>
        			</tr>
        		</tfoot>
        	</table>
        </div>


        <table style="margin-top:10px; font-size:14px" class="rpt_table" width="600" align="left" border="1" cellpadding="0" cellspacing="0" rules="all">
            <thead>
                <tr>
                    <th width="40">Sl</th>
                    <th>Special Instruction</th>
                </tr>
            </thead>
            <tbody>
				<?
                $data_array=sql_select("select id, terms from  wo_booking_terms_condition where entry_form=140 and booking_no='$data[2]'");
                if(count($data_array)>0)
                {
					$l=1;
					foreach( $data_array as $key=>$row )
					{
						?>
						<tr>
                            <td><? echo $l;?> </td>
                            <td style="word-break:break-all"><? echo $row[csf("terms")]; ?> </td>
						</tr>
						<?
						$l++;
					}
                }
                ?>
            </tbody>
        </table>     


    	<table align="left" cellspacing="0" width="810" class="rpt_table" >
        	<tr>
            	<td colspan="6" align="left">
					<?

						// $user_id=$_SESSION['logic_erp']['user_id'];
						$user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
						// $prepared_by = $user_arr[$user_id];
	                    //echo signature_table(134, $data[0], "810px");
					  	echo signature_table(134, $data[0], "1080px",$cbo_template_id,$padding_top = 70,$user_lib_name[$inserted_by]);
                    ?>
                </td>
            </tr>

        </table>

    </div>
   <script type="text/javascript" src="../../js/jquery.js"></script>
   <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
   <script>

	function fnc_generate_Barcodes( valuess, img_id )
	{
		var value = valuess;//$("#barcodeValue").val();
		var btype = 'code39';//$("input[name=btype]:checked").val();
		var renderer ='bmp';// $("input[name=renderer]:checked").val();
		var settings = {
		  output:renderer,
		  bgColor: '#FFFFFF',
		  color: '#000000',
		  barWidth: 1,
		  barHeight: 60,
		  moduleSize:5,
		  posX: 10,
		  posY: 20,
		  addQuietZone: 1
		};
		$("#"+img_id).html('11');
		 value = {code:value, rect: false};
		$("#"+img_id).show().barcode(value, btype, settings);
	}
   </script>
   <script type="text/javascript">
   	fnc_generate_Barcodes('<? echo $barcode_no; ?>','barcode_img_id');
   </script>

 <?
 exit();
}

if($action=="sample_requisition_print8")
{
	extract($_REQUEST);
	 $data=explode('*',$data);
	 $cbo_template_id=$data[3];

	// $cbo_template_id=str_replace("'","",$cbo_template_id);
	// $txt_booking_no=str_replace("'","",$txt_booking_no);
	// $cbo_company_name=str_replace("'","",$cbo_company_name);
	// $update_id=str_replace("'","",$update_id);
	
	$path="../../";
	if(count($data)>3)
	{
		if($data[4]=='../')
		{
			$path=$data[4];
		}
	}
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$supplier_library=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
	$company_img=sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$dealing_merchant_library=return_library_array( "select id,team_member_name from lib_mkt_team_member_info", "id", "team_member_name"  );
	$team_leader_arr=return_library_array( "select id,team_leader_name from lib_marketing_team", "id", "team_leader_name"  );

	$sample_library=return_library_array( "select id, sample_name from lib_sample", "id", "sample_name");
	$brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand", "id", "brand_name");
	$product_sub_dept_arr=return_library_array( "select id, sub_department_name from lib_pro_sub_deparatment", "id", "sub_department_name");
	$size_library=return_library_array( "select id, size_name from lib_size", "id", "size_name"  );
	$color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
	$season_arr=return_library_array( "select id,season_name from lib_buyer_season  where status_active =1 and is_deleted=0 ", "id", "season_name"  );
	$trims_group_lib=return_library_array( "select id,item_name from lib_item_group  where status_active =1 and is_deleted=0", "id", "item_name");
	//concate(buyer_name,'_',contact_person)
	$appDate=return_field_value("approved_date","approval_history","entry_form=25 and mst_id='$data[1]' order by id desc");
	$appBy=return_field_value("approved_by","approval_history","entry_form=25 and mst_id='$data[1]'");
	$user_arr=return_library_array( "select id, user_name from user_passwd", "id", "user_name"  );
	$user_library=return_library_array( "select id, user_full_name from user_passwd", "id", "user_full_name"  );
	$image_location='';
	$image_location_arr = sql_select("select master_tble_id,image_location from common_photo_library where form_name='sample_requisition_2' and file_type=1 and master_tble_id='$data[1]'");
	foreach ($image_location_arr as $row) {
		$image_locationArr[$row[csf('image_location')]]=$row[csf('image_location')];
	}
	$data_img=sql_select("select image_location,master_tble_id  from common_photo_library  where   form_name='required_fabric_1' and is_deleted=0 and file_type=1");
	$system_img_arr=array();
	foreach($data_img as $row)
	{
	  $system_img_arr[$row[csf('master_tble_id')]]['img']=$row[csf('image_location')];
	}

	$sam_img=sql_select("select image_location,master_tble_id  from common_photo_library  where   form_name='sample_details_1' and is_deleted=0 and file_type=1");
	$sam_img_arr=array();
	foreach($sam_img as $row)
	{
	  $sam_img_arr[$row[csf('master_tble_id')]]['img']=$row[csf('image_location')];
	}
	//  echo '<pre>';
    //     print_r($sam_img_arr); die;

	
	

	$sample_dtls_addi_value=sql_select("SELECT print, printseq, embro, embroseq, wash, washseq, spworks, spworksseq, gmtsdying, gmtsdyingseq, aop, aopseq, bush, bushseq, peach, peachseq, yd, ydseq from sample_details_additional_value where mst_id=$data[1]");
	$print_status=2; $aop_status=2; $embro_status=2; $wash_status=2; $peach_status=2; $bush_status=2; $yd_status=2;
	foreach ($sample_dtls_addi_value as $row) {
		if($row[csf('print')]==1){
			$print_status=1;
		}
		if($row[csf('embro')]==1){
			$embro_status=1;
		}
		if($row[csf('aop')]==1){
			$aop_status=1;
		}
		if($row[csf('wash')]==1){
			$wash_status=1;
		}
		if($row[csf('peach')]==1){
			$peach_status=1;
		}
		if($row[csf('bush')]==1){
			$bush_status=1;
		}
		if($row[csf('yd')]==1){
			$yd_status=1;
		}
	}

	$sql_embellishment =sql_select("SELECT id, sample_mst_id, sample_name_re, gmts_item_id_re, name_re, type_re, remarks_re, body_part_id, delivery_date, supplier_id from sample_development_fabric_acc where sample_mst_id='$data[1]' and form_type=3 and is_deleted=0  and status_active=1 and name_re in (1,2,3) order by id asc");
	$emb_print_type=''; $emb_embroy_type=''; $emb_wash_type='';
	foreach ($sql_embellishment as $row) {
		if($row[csf('name_re')]==1){
			$print_status=1;
			$emb_print_type=$emblishment_print_type[$row[csf('type_re')]];
		}
		if($row[csf('name_re')]==2){
			$embro_status=1;
			$emb_embroy_type=$emblishment_embroy_type[$row[csf('type_re')]];
		}
		if($row[csf('name_re')]==3){
			$wash_status=1;
			$emb_wash_type=$emblishment_wash_type[$row[csf('type_re')]];
		}
	}
 ?>
 <style>
	#mstDiv {
	    margin:0px auto;
	    width:1130px;

	}
	#mstDiv @media print {

	   thead {display: table-header-group;}

	}

	 @media print{
			html>body table.rpt_table {
			margin-left:12px;
	  		}

		}
	</style>

		

	<div id="mstDiv">

        <table width="1100" cellspacing="0" border="0"   >
            <tr>
                <td rowspan="4" valign="top" width="150"><img width="150" height="80" src="<?=$path?><? echo $company_img[0][csf("image_location")]; ?>"></td>
                <td colspan="5" style="font-size:20px;text-align: center;"><strong><b><? echo $company_library[$data[0]]; ?></b></strong></td>
            </tr>
            <tr>
                <td colspan="5" style="text-align: center;">
					<?
                    $val=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
                    echo ($val[0][csf('plot_no')])?   $val[0][csf('plot_no')].',': "";
                    echo ($val[0][csf('level_no')])?  $val[0][csf('level_no')].',': "";
                    echo ($val[0][csf('road_no')])?   $val[0][csf('road_no')].',': "";
                    echo ($val[0][csf('block_no')])?  $val[0][csf('block_no')].',': "";
                    echo ($val[0][csf('city')])?      $val[0][csf('city')].',': "";
                    echo ($val[0][csf('zip_code')])?  $val[0][csf('zip_code')].',': "";
                    echo ($val[0][csf('province')])?  $val[0][csf('province')].',': "";
                    echo($val[0][csf('country_id')])? $country_arr[$val[0][csf('country_id')]]: "";
                    echo ($val[0][csf('email')])?    "</br>". $val[0][csf('email')].',': "</br>";
                    echo($val[0][csf('website')])?    $val[0][csf('website')]: "";

                      $sql="SELECT id, requisition_number, requisition_number_prefix_num, style_ref_no, buyer_name, season, product_dept, dealing_marchant, agent_name, buyer_ref, bh_merchant, estimated_shipdate, team_leader, season_buyer_wise, remarks, quotation_id, sample_stage_id, requisition_date, material_delivery_date, season_year, brand_id, is_acknowledge,refusing_cause,sub_dept_id,inserted_by from sample_development_mst where  id='$data[1]' and entry_form_id=203 and  is_deleted=0  and status_active=1";
                    $dataArray=sql_select($sql);
                    $refusing_cause=$dataArray[0][csf('refusing_cause')];
                    $barcode_no=$dataArray[0][csf('requisition_number')];
					$prepared_by=$user_library[$dataArray[0][csf('inserted_by')]];
                    if($dataArray[0][csf("sample_stage_id")]==1)
                    {
                        $job_lib=return_library_array( "SELECT a.id,min(b.shipment_date) as shipment_date  from wo_po_details_master  a,wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and a.company_name='$cbo_company_name' GROUP BY a.id", "id", "shipment_date"  );
                    }
					 $sqls="SELECT style_desc, supplier_id, revised_no, buyer_req_no, source, team_leader, dealing_marchant, booking_date, attention, remarks from wo_non_ord_samp_booking_mst where  booking_no='$data[2]' and is_deleted=0  and status_active=1";
 					 $dataArray_book=sql_select($sqls);

 					 $sample_acc_arr=sql_select("SELECT confirm_del_end_date, refusing_cause, unacknowledge_date, insert_date from sample_requisition_acknowledge where sample_mst_id= '$data[1]'");
					 $sample_delivery="SELECT delivery_date from sample_development_fabric_acc where sample_mst_id= '$data[1]'";
					 $dataSample_delivery=sql_select($sample_delivery);
					 $delivery_date=$dataSample_delivery[0][csf('delivery_date')];
					 //echo "SELECT delivery_date from sample_development_fabric_acc where sample_mst_id= '$data[1]'";
				
 					 
                    ?>
                </td>
            </tr>
			<tr>
                <td colspan="5" style="font-size:medium; text-align: center;"><strong style="font-size:18px">Sample Program Without Order</strong></td>               
            </tr>
			<tr>
				<td align="right"><strong style="background-color:yellow;font-size: 25px;margin-right:10%;"><?=str_replace("'","",$data[2]);;?></strong></td>
            </tr>
             
        </table>

        <table width="1100" cellspacing="0" border="1" class="rpt_table" style="font-size:14px" >
        	<tr>
        		<th align="left" width="150" >S. Requisition NO</th>
        		<td colspan="2" align="left" width="150" ><?=$dataArray[0][csf("requisition_number")];?></td>
        		<th align="left" width="100">Revised</th>
        		<td colspan="3" align="left" width="80"><?=$dataArray_book[0][csf('revised_no')];?></td>
        		<td rowspan="9" width="250">
        		<? if($image_location!=''){ ?>
        		<!--<img width="240" height="210" src="<? //echo $path.$image_location; ?>" >-->
        		<? } else{ ?>
        		<!--<img width="240" height="210" src="../../images/no-image.jpg" >-->
        		<? }
				 ?>
                 
                 
                        <table width="100%">
                            <tr>
                            <?
							
                            $img_counter = 0;
							$width=240;$height=210;
							$width2=100;$height2=100;
							$tot_row=count($image_locationArr);
							//echo $tot_row.'D';
                            foreach($image_locationArr as $result_imge)
                            {

                                ?>
                                <td>
                                <p> <img src="<? echo '../../'.$result_imge; ?>" width="<? if($tot_row==1) echo $width;else echo $width2;?>" height="<? if($tot_row==1) echo $height;else echo  $height2;?>" border="2" /></p>
                                </td>
                                <?

                                $img_counter++;
                            }
                            ?>
                            </tr>
                       </table>
                        
                       
        		</td>
        		<th align="left" width="150">Sample Req. Date</th>
        		<td width="120"><?= change_date_format($dataArray[0][csf("requisition_date")]);?></td>
        	</tr>
            <tr>
        		<th align="left">S.Fab. Booking No.</th>
        		<td colspan="2"><?=$data[2];?></td>
                <th align="left">Revised Date</th>
        		<td align="left" colspan="3"><?= change_date_format($sample_acc_arr[0][csf('unacknowledge_date')]); ?></td>
        		<th align="left">Fab. Booking Date</th>
        		<td align="left"><?= change_date_format($dataArray_book[0][csf('booking_date')]);?></td>
        	</tr>
        	<tr>
        		<th align="left">Style Ref.</th>
        		<td colspan="6" align="left"><?=$dataArray[0][csf('style_ref_no')];?></td>
                <th align="left">Style Desc.</th>
        		<td><?=$dataArray_book[0][csf('style_desc')];?></td>
        	</tr>
        	<tr>
        		<th align="left">Buyer</th>
        		<td colspan="2" align="left"><?=$buyer_library[$dataArray[0][csf('buyer_name')]];?></td>
        		<th align="left">Season - S.Year</th>
        		<td colspan="3" align="left"><?=$season_arr[$dataArray[0][csf('season')]].'-'.$dataArray[0][csf('season_year')];?></td>
        		<th align="left">Fab. Delivery Date</th>
        		<td><?=change_date_format($delivery_date); ?></td>
        	</tr>
        	<tr>
        		<th align="left">Product Dept</th>
        		<td colspan="2" align="left"><?=$product_dept[$dataArray[0][csf('product_dept')]];?></td>
        		<th align="left">Brand</th>
        		<td colspan="3" align="left"><?=$brand_arr[$dataArray[0][csf('brand_id')]];?></td>
        		<th align="left">Acknowledgement St.</th>
        		<td><?=$yes_no[$dataArray[0][csf('is_acknowledge')]]; ?></td>
        	</tr>
        	<tr>
        		<th align="left">Prod. Sub Dept.</th>
        		<td colspan="2" align="left"><?=$product_sub_dept_arr[$dataArray[0][csf('sub_dept_id')]];?></td>
        		<th align="left">AOP</th>
        		<td colspan="3" align="left"><?= $yes_no[$aop_status]  ?></td>
        		<th align="left">Acknowledgement Date</th>
        		<td><?= change_date_format($sample_acc_arr[0][csf('insert_date')]); ?></td>
        	</tr>
        	<tr>
        		<th align="left">Print</th>
        		<td colspan="2" align="left"><?= $yes_no[$print_status]  ?></td>
        		<td colspan="4" align="left"><?= $emb_print_type  ?></td>
        		<th align="left">Team Leader</th>
        		<td align="left"><?=$team_leader_arr[$dataArray_book[0][csf('team_leader')]];?></td>
        	</tr>
        	<tr>
        		<th align="left">Embroidery</th>
        		<td colspan="2" align="left"><?= $yes_no[$embro_status]  ?></td>
        		<td colspan="4" align="left"><?= $emb_embroy_type  ?></td>
                <th align="left">Dealing Merchandiser</th>
        		<td align="left"><?=$dealing_merchant_library[$dataArray_book[0][csf('dealing_marchant')]];?></td>
        		
        	</tr>
        	<tr>
        		<th align="left">Wash</th>
        		<td colspan="2" align="left"><?= $yes_no[$wash_status]  ?></td>
        		<td colspan="4" align="left"><?= $emb_wash_type  ?></td>
        		<td>&nbsp;</td>
                <td>&nbsp;</td>
        	</tr>
        	<tr>
        		<th align="left">Peach Finish</th>
        		<td align="left"><?= $yes_no[$peach_status]  ?></td>
        		<th align="left" width="100">Brushing</th>
        		<td align="left"><?= $yes_no[$bush_status]  ?></td>
        		<th align="left" width="35">YDS</th>
        		<td align="left" colspan="2" ><?= $yes_no[$yd_status]  ?></td>
        		<th align="left">Attention</th>
        		<td align="left" colspan="2"><?=$dataArray_book[0][csf('attention')];?></td>
        	</tr>
        	<tr>
        		<th align="left">Cause of Revised</th>
        		<td colspan="9" align="left"><?=$refusing_cause;// change_date_format($sample_acc_arr[0][csf('refusing_cause')]); ?></td>
        	</tr>
        	<tr>
        		<th align="left">S.Order Remarks</th>
        		<td colspan="9" align="left"><?=$dataArray[0][csf('remarks')];?></td>
        	</tr>
        	<tr>
        		<th align="left">S.Fab Booking Remarks</th>
        		<td colspan="9" align="left"><?=$dataArray_book[0][csf('remarks')];?></td>
        	</tr>
        </table>
        <br>
		<?
         $color_sql="SELECT b.color_id ,b.process_loss_percent ,b.fabric_color,b.contrast,b.mst_id,b.dtls_id, b.qnty from  sample_development_rf_color b where b.status_active=1 and b.is_deleted=0 and b.qnty>0 and b.mst_id='$data[1]' ";
		 $color_res=sql_select($color_sql);
		 $color_rf_data=array();
		 foreach ($color_res as $val) {
		 	$color_rf_data[$val[csf('dtls_id')]][$val[csf('color_id')]][$val[csf('fabric_color')]]['process_loss_percent']=$val[csf('process_loss_percent')];
		 	$color_rf_data[$val[csf('dtls_id')]][$val[csf('color_id')]][$val[csf('fabric_color')]]['contrast']=$val[csf('contrast')];
		 	$color_rf_data[$val[csf('dtls_id')]][$val[csf('color_id')]][$val[csf('fabric_color')]]['qnty']=$val[csf('qnty')];
		 }

		 $sql_fab="SELECT a.id,a.sample_name, a.gmts_item_id, c.gmts_color as color_id,   a.delivery_date, a.fabric_description, a.body_part_id, a.fabric_source, a.remarks_ra, a.gsm, a.dia, a.color_type_id, a.width_dia_id, a.uom_id, c.grey_fabric as grey_fab_qnty, c.fabric_color,c.dtls_id,c.finish_fabric as qnty,a.id,a.determination_id from sample_development_fabric_acc a, wo_non_ord_samp_booking_dtls c where  a.id=c.dtls_id  and a.form_type=1  and c.grey_fabric>0 and a.status_active=1 and a.is_deleted=0  and c.status_active=1 and c.is_deleted=0  and a.sample_mst_id='$data[1]' ";
		//echo  $sql_fab;
        $sql_fab_arr=array();
        $dtls_id_arr=array();
        $determination_id_arr=array();

        foreach(sql_select($sql_fab) as $vals)
        {
        	$contrast=$color_rf_data[$vals[csf('dtls_id')]][$vals[csf('color_id')]][$vals[csf('fabric_color')]]['contrast'];

			$process_loss_percent=$color_rf_data[$vals[csf('dtls_id')]][$vals[csf('color_id')]][$vals[csf('fabric_color')]]['process_loss_percent'];

            $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$contrast]["qnty"]+=$vals[csf("qnty")];
            $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$contrast]["process_loss_percent"]+=$process_loss_percent;

            $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$contrast]["grey_fab_qnty"]+=$vals[csf("grey_fab_qnty")];

            $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$contrast]["delivery_date"] =change_date_format($vals[csf("delivery_date")]);

			$sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$contrast]["id"] =$vals[csf("id")];

            $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$contrast]["fabric_source"] =$vals[csf("fabric_source")];

            $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$contrast]["uom_id"] =$vals[csf("uom_id")];
            $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$contrast]["dia"] =$vals[csf("dia")];

            $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$contrast]["width_dia_id"] =$vals[csf("width_dia_id")];

            $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$contrast]["remarks"] =$vals[csf("remarks_ra")];
            $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$contrast]["color_type_id"] =$vals[csf("color_type_id")];

            $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$contrast]["determination_id"] =$vals[csf("determination_id")];
            array_push($dtls_id_arr,$vals[csf('id')]);
            array_push($determination_id_arr,$vals[csf('determination_id')]);
        }
        $sample_item_wise_span=array();
        /*echo '<pre>';
        print_r($sql_fab_arr); die;*/
        $determination_id_cond= where_con_using_array($determination_id_arr,0,"a.id");

        $update_dtls_id_cond= where_con_using_array($dtls_id_arr,0,"a.dtls_id");
        $sql = "SELECT a.sample_color, a.size_id, a.item_size, a.qnty_pcs, b.body_part_id from sample_requisition_coller_cuff a join sample_development_fabric_acc b on a.DTLS_ID=b.id where  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $update_dtls_id_cond";
        //echo $sql; die;
		$collar_cuff_data_arr = sql_select($sql);
		foreach ($collar_cuff_data_arr as $row)
		{
			$sample_color = $row[csf('sample_color')];
			
			$itemsize = $row[csf('item_size')];
			//$collarCuffarr[$sample_color].=$itemsize."***";
			$collarCuffarr[$sample_color][$row[csf('body_part_id')]][$itemsize]=$itemsize;
			
		}
		 $sql_d = "SELECT b.fabric_composition_name, a.id, a.construction FROM lib_yarn_count_determina_mst a left join lib_fabric_composition b on a.fabric_composition_id = b.id AND b.status_active = 1 AND b.is_deleted = 0 WHERE a.status_active = 1 AND a.is_deleted = 0  $determination_id_cond";
		// echo $sql_d; //die;
		 $determina_arr = sql_select($sql_d);
		$determina_data_arr=array();
		foreach ($determina_arr as $row)
		{
			
			$determina_data_arr[$row[csf('id')]].=$row[csf('fabric_composition_name')]."***";
			$construction_data_arr[$row[csf('id')]].=$row[csf('construction')]."***";
			
		}

        foreach($sql_fab_arr as $sample_type=>$colorType_data)
        {
            foreach($colorType_data as $colorType=>$gmts_color_data)
            {

                foreach($gmts_color_data as $gmts_color_id=>$body_part_data)
                {
                	$sample_span=0;
                    foreach($body_part_data as $body_part_id=>$fab_desc_data)
                    {
                        //$kk=0;
                        foreach($fab_desc_data as $fab_id=>$gsm_data)
                        {
                            foreach($gsm_data as $gsm_id=>$dia_data)
                            {
                                foreach($dia_data as $dia_id=>$color_data)
                                {
                                    foreach($color_data as $contrast_id=>$row)
                                    {
                                        $sample_span++;
                                        //$kk++;
                                    }
                                }
                            }
                        }
                        //$bodypart_item_wise_span[$sample_type][$gmts_item_id][$body_part_id]=$kk;
                    }
                    $sample_item_wise_span[$sample_type][$gmts_color_id]=$sample_span;
                }
            }
        }
	  /*        echo "<pre>";
        print_r($sample_item_wise_span);die;*/

        $sql_sample_dtls= "SELECT a.sample_name, a.article_no, a.sample_color from sample_development_dtls a, lib_color b where a.status_active=1 and a.is_deleted=0 and a.entry_form_id=203  and sample_mst_id='$data[1]' and b.status_active=1 and b.id=a.sample_color  group by a.sample_name, a.article_no, a.sample_color";
        foreach(sql_select($sql_sample_dtls) as $key=>$value)
        {
            if($sample_wise_article_no[$value[csf("sample_name")]][$value[csf("sample_color")]]=="")
            {
                $sample_wise_article_no[$value[csf("sample_name")]][$value[csf("sample_color")]]=$value[csf("article_no")];
            }
            else
            {
                if(!in_array($value[csf("article_no")], $sample_wise_article_no))
                {
                    $sample_wise_article_no[$value[csf("sample_name")]][$value[csf("sample_color")]].= ', '.$value[csf("article_no")];
                }
            }
        }

		$sql_labdip=sql_select("select c.lapdip_no,c.job_no_mst,c.color_name_id,c.po_break_down_id as po_id,c.booking_no from wo_po_lapdip_approval_info c where c.booking_no='$data[2]' and  c.status_active=1 ");
		// echo "select c.lapdip_no,c.job_no_mst,c.color_name_id,c.po_break_down_id as po_id,c.booking_no from wo_po_lapdip_approval_info c where c.booking_no='$data[1]' and  c.status_active=1 ";
			foreach($sql_labdip as $row)
			{
				if($row[csf("lapdip_no")])
				{
				$labdip_arr[$row[csf("color_name_id")]].=$row[csf("lapdip_no")].',';
				$gmt_labdip_arr[$color_library[$row[csf("color_name_id")]]]=$row[csf("lapdip_no")];
				}
			}
        // echo "<pre>"; print_r($sample_wise_article_no);die;

        ?>
        <table class="rpt_table" width="1100"  border="1" cellpadding="0" cellspacing="0" rules="all" style="margin-top:5px; font-size:14px">
            <thead>
                <tr>
                    <th colspan="21">Required Fabric</th>
                </tr>
                <tr>
                    <th width="20">Sl</th>
                    <th width="80">Sample Type</th>
                    <th width="60">Gmt Color</th>
                    <th width="60">Fab. Deli<br>Date</th>
                    <th width="80">Body Part</th>
                    <th width="75">Fabric<br>Construction</th>
                    <th width="115">Fabric Desc & <br> Composition</th>
                    <th width="60">Color Type</th>
                    <th width="60">Fab. Color/ Contrast.</th>
                    <th width="70">Item Size</th>
                    <th width="40">GSM</th>
                    <th width="40">Dia</th>
                    <th width="40">Width</br>/Dia</th>
                    <th width="40">UOM</th>
                    <th width="50">Grey Qty</th>
                    <th width="40">P. Loss</th>
                    <th width="50">Fin Fab Qty</th>
                    <th width="60">Fabric<br>Source</th>
					<th width="50">LD No</th>
					<th width="50">Image</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                <?
                function str_replace_first($search, $replace, $subject)
				{
				    $search = '/'.preg_quote($search, '/').'/';
				    return preg_replace($search, $replace, $subject, 1);
				}
                $p=1; $total_finish=0; $total_grey=0; $total_process=0;
                foreach($sql_fab_arr as $sample_type=>$colorType_data)
                {
                    foreach($colorType_data as $colorType=>$gmts_color_data)
                    {
                        foreach($gmts_color_data as $gmts_color_id=>$body_part_data)
                        {
                            $nn=0;
                            foreach($body_part_data as $body_part_id=>$fab_desc_data)
                            {
                                foreach($fab_desc_data as $fab_id=>$gsm_data)
                                {
                                    foreach($gsm_data as $gsm_id=>$dia_data)
                                    {
                                        foreach($dia_data as $dia_id=>$color_data)
                                        {
                                            //$i=0;
                                            foreach($color_data as $contrast_id=>$value)
                                            {
                                            	$constr=implode(",", array_unique(explode("***", chop($construction_data_arr[$value['determination_id']],"***"))));
                                                ?>
                                                <tr>
                                                    <td  align="center" style="word-wrap: break-word;word-break: break-all;"><? echo $p;$p++;?></td>
                                                    <?
                                                   /* if($nn==0)
                                                    {*/
                                                        $rowspan=0;
                                                        //$rowspan=$sample_item_wise_span[$sample_type][$gmts_color_id];
                                                        ?>
                                                        <!-- <td rowspan="<?=$rowspan;?>" align="center"><?=ltrim($sample_wise_article_no[$sample_type][$gmts_color_id], ',');?></td> -->
                                                        <td rowspan="<?=$rowspan;?>" align="center"><?=$sample_library[$sample_type];?></td>
                                                        <td align="center" rowspan="<?=$rowspan;?>"><?=$color_library[$gmts_color_id];?> </td>
                                                        <?
                                                        $nn++;
                                                    /*}*/
                                                    
                                                    ?>
                                                    <td align="center"><?=$value["delivery_date"];?> </td>
                                                    <td align="center" style="word-break:break-all"><?=$body_part[$body_part_id];?></td>
                                                    <td align="center" style="word-break:break-all"><?=$constr;?></td>
                                                    <td align="center" style="word-break:break-all"><?=str_replace_first(trim($constr), "", $fab_id);//implode(" , ", array_unique(explode("***", chop($determina_data_arr[$value['determination_id']],"***"))));// echo $fab_id;?></td>
                                                    <td align="center" style="word-break:break-all"><?=$color_type[$colorType];?></td>
                                                    <td align="center" style="word-break:break-all"><?=$contrast_id;?></td>
                                                    <td align="center" style="word-break:break-all"><? echo implode(", ", $collarCuffarr[$gmts_color_id][$body_part_id]);?></td>
                                                    <td align="center" style="word-break:break-all"><?=$gsm_id;?></td>
                                                    <td align="center" style="word-break:break-all"><?=$value["dia"];?></td>
                                                    <td align="center" style="word-break:break-all"><?=$fabric_typee[$value["width_dia_id"]];?></td>
                                                    <td align="center" style="word-break:break-all"><?=$unit_of_measurement[$value["uom_id"]];?></td>
                                                    <td align="right"><?=number_format($value["grey_fab_qnty"], 2);?></td>
                                                    <td align="right"><?=$value["process_loss_percent"];?></td>
                                                    <td align="right"><?=number_format($value["qnty"],2);?></td>
                                                    <td style="word-break:break-all"><?=$fabric_source[$value["fabric_source"]];?></td>
													<td style="word-break:break-all">
														<?
														 $lapdip_noAll="";
														 $labdip=rtrim($labdip_arr[$gmts_color_id],',');
														 $lapdip_noAll=implode(",",array_unique(explode(",",$labdip)));
														 echo $lapdip_noAll;
														?>
													</td>
													<td style="word-break:break-all" align="center">
													<? 
													$img_ref_id=$value['id'];
													$sam_req_img=$system_img_arr[$img_ref_id]['img'];
													
													?>
													<img src='../../<? echo $sam_req_img; ?>' height='30' width='30'  />
											
                                                    <td style="word-break:break-all"><?=$value["remarks"];?></td>
                                                </tr>
                                                <?
                                                //$i++;
                                                $total_finish +=$value["qnty"];
                                                $total_grey +=$value["grey_fab_qnty"];
                                                $total_process +=$value["process_loss_percent"];
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                ?>
                <tr>
                    <th colspan="13" align="right"><b>Total</b></th>
                    <th align="right"></th>
                    <th align="right"><?=number_format($total_grey, 2);?></th>
                    <th align="right"></th>
                    <th align="right"><?=number_format($total_finish, 2);?></th>
                    <th colspan="4">&nbsp;</th>
                </tr>
            </tbody>
        </table>
        <br/>
        <?
        $sample_color_arr=return_library_array( "select id, sample_color from sample_development_dtls", "id", "sample_color");
        $sql_qry="SELECT id, sample_mst_id, sample_name, gmts_item_id, smv, article_no, sample_color, sample_prod_qty, submission_qty, delv_start_date, delv_end_date, sample_charge, sample_curency, sent_to_buyer_date, comments from sample_development_dtls where status_active=1 and is_deleted=0 and entry_form_id=203 and sample_mst_id='$data[1]' order by id asc";

        $sql_qry_color="SELECT a.id, a.sample_mst_id, a.sample_name, a.gmts_item_id, a.smv, a.article_no, a.sample_color, a.sample_prod_qty, a.submission_qty, a.delv_start_date, a.delv_end_date, a.sample_charge, a.sample_curency, a.sent_to_buyer_date, a.comments, c.dtls_id, c.size_id, c.bh_qty, c.self_qty, c.test_qty, c.plan_qty, c.dyeing_qty from sample_development_dtls a, sample_development_size c left join lib_size s on c.size_id=s.id where a.id=c.dtls_id and a.status_active =1 and a.is_deleted=0 and a.entry_form_id=203 and a.sample_mst_id='$data[1]' order by a.id,a.sample_color,s.sequence asc";
        $size_type_arr=array(1=>"BH Qty",2=>"Self Qty",3=>"Test qty",4=>"Plan Qty",5=>"Dyeing Qty");
        $color_size_arr=array();
        foreach(sql_select($sql_qry_color) as $vals)
        {
            if($vals[csf("bh_qty")]>0)
            {
                $color_size_arr[1][$vals[csf("size_id")]]='Bh Qty';
                $bh_qty=$vals[csf("bh_qty")];
                $color_size_dtls_qty_arr[1][$vals[csf("id")]][$vals[csf("size_id")]]=$bh_qty;
            }
            if($vals[csf("self_qty")]>0)
            {
                $color_size_arr[2][$vals[csf("size_id")]]='self qty';
                $color_size_dtls_qty_arr[2][$vals[csf("id")]][$vals[csf("size_id")]]=$vals[csf("self_qty")];
            }
            if($vals[csf("test_qty")]>0)
            {
                $color_size_arr[3][$vals[csf("size_id")]]='test qty';
                $color_size_dtls_qty_arr[3][$vals[csf("id")]][$vals[csf("size_id")]]=$vals[csf("test_qty")];
            }
            if($vals[csf("plan_qty")]>0)
            {
                $color_size_arr[4][$vals[csf("size_id")]]='plan qty';
                $color_size_dtls_qty_arr[4][$vals[csf("id")]][$vals[csf("size_id")]]=$vals[csf("plan_qty")];
            }
            if($vals[csf("dyeing_qty")]>0)
            {
                $color_size_arr[5][$vals[csf("size_id")]]='Dyeing qty';
                $color_size_dtls_qty_arr[5][$vals[csf("id")]][$vals[csf("size_id")]]=$vals[csf("dyeing_qty")];
            }
        }
        $tot_row=count($color_size_arr);
        $result=sql_select($sql_qry);
		$head_tot_row_td=0;
		foreach($color_size_arr as $type_id=>$data_size)
		{
			foreach($data_size as $size_id=>$data_val)
			{
				$head_tot_row_td++;
			}
		}
        ?>
        <table align="left" cellspacing="0" border="1" width="1100" class="rpt_table" rules="all" style="margin-top: 5px; font-size:14px">
            <thead>
                <tr>
                    <td width="150" colspan="<? echo 11+$head_tot_row_td;?>" align="center"><strong>Sample Details</strong></td>
                </tr>
                <tr>
                    <th width="30" rowspan="2">Sl</th>
                    <th width="100" rowspan="2">Sample Name</th>
                    <th width="120" rowspan="2">Garment Item</th>
                    <th width="70" rowspan="2">Sample Delv.  Date</th>
                    <th width="55" rowspan="2">ALT / [C/W]</th>
                    <th width="70" rowspan="2">Color</th>
                        <?
                        $tot_row_td=0;
                        foreach($color_size_arr as $type_id=>$val)
                        {
                            ?>
                            <th width="45" align="center" colspan="<?=count($val);?>"><?=$size_type_arr[$type_id];?></th>
                            <?
                        }
                        ?>
                    <th rowspan="2" width="55">Total</th>
                    <th rowspan="2" width="55">Submn Qty</th>
                    <th rowspan="2"  width="70">Buyer Submisstion Date</th>
					<th rowspan="2"  width="70">Image</th>
                    <th rowspan="2">Remarks</th>
                </tr>
                <tr>
                    <?
					$tot_row_td=0;
                    foreach($color_size_arr as $type_id=>$data_size)
                    {
                        foreach($data_size as $size_id=>$data_val)
                        {
                            $tot_row_td++;
                            ?>
                            <th width="40" align="center"><?=$size_library[$size_id];?></th>
                            <?
                        }
                    }
                    ?>
                </tr>
            </thead>
            <tbody>
                <?
                $i=1; $k=0; $gr_tot_sum=0; $gr_sub_sum=0;
                foreach($result as $row)
                {
                    $dtls_ids=$row[csf('id')];
                    $prod_sum=$prod_sum+$row[csf('sample_prod_qty')];
                    $sub_sum=$sub_sum+$row[csf('submission_qty')];
                    $k++;
                    ?>
                    <tr>
                        <td align="center"><?=$k;?></td>
                        <td align="left"><?=$sample_library[$row[csf('sample_name')]];?></td>
                        <td align="left"><?=$garments_item[$row[csf('gmts_item_id')]];?></td>
                        <td align="left"><?=change_date_format($row[csf('delv_end_date')]);?></td>
                        <td align="left"><?=$row[csf('article_no')];?></td>
                        <td align="left"><?=$color_library[$row[csf('sample_color')]];?></td>
                        <?
                        $total_sizes_qty=0;  $total_sizes_qty_subm=0;
                        foreach($color_size_arr as $type_id=>$data_size)
                        {
                            foreach($data_size as $size_id=>$data_val)
                            {
                                $size_qty=$color_size_dtls_qty_arr[$type_id][$dtls_ids][$size_id];
                                ?>
                                <td align="right"><?=$size_qty;?></td>
                                <?
                                if($type_id==1)
                                {
                                $total_sizes_qty_subm+=$size_qty;
                                }
                                $total_sizes_qty+=$size_qty;
                            }
                        }
                        ?>
                        <td align="right"><?=number_format($total_sizes_qty,2);?></td>
                        <td align="right"><?=number_format($total_sizes_qty_subm,2);?></td>
                        <td align="left"><?=change_date_format($row[csf('sent_to_buyer_date')]);?> </td>
						<td align="middle"><? 
							$img_ref_id= $dtls_ids;
							$sam_req_img=$sam_img_arr[$img_ref_id]['img'];
							?>
							<img src='../../<? echo $sam_req_img; ?>' height='50' width='70'  /></td>
                        <td align="left"><?=$row[csf('comments')];?> </td>
                    </tr>
                    <?
                    $gr_tot_sum+=$total_sizes_qty;
                    $gr_sub_sum+=$total_sizes_qty_subm;
                }
                ?>
                <tr>
                    <td colspan="<?=6+$tot_row_td;?>" align="right"><b>Total</b></td>
                    <td align="right"><b><?=number_format($gr_tot_sum,2);?> </b></td>
                    <td align="right"><b><?=number_format($gr_sub_sum,2);?> </b></td>
                    <td colspan="3">&nbsp;</td>
                </tr>
            </tbody>
        </table>
        <br>&nbsp;

        <table align="left" cellspacing="0" border="1" width="1100" class="rpt_table" rules="all" style="margin-top: 5px; font-size:14px">
            <thead>
                <tr>
                    <td colspan="13" align="center"><strong>Required Accessories</td>
                </tr>
                <tr>
                    <th width="30">Sl</th>
                    <th width="100">Sample Name</th>
                    <th width="120">Garment Item</th>
                    <th width="100">Trims Group</th>
                    <th width="100">Description</th>
                    <th width="100">Supplier</th>
                    <th width="100">Brand/Supp.Ref</th>
                    <th width="30">UOM</th>
                    <th width="30">Req/Dzn</th>
                    <th width="30">Req/Qty</th>
                    <th width="80">Acc.Sour.</th>
                    <th width="100">Acc Delivery Date</th>
                    <th>Remarks </th>
                </tr>
            </thead>
            <tbody>
				<?
                $sql_qryA="SELECT id,sample_mst_id,sample_name_ra,gmts_item_id_ra,trims_group_ra,description_ra,brand_ref_ra,uom_id_ra,req_dzn_ra,req_qty_ra,remarks_ra,delivery_date,supplier_id,fabric_source from sample_development_fabric_acc where status_active =1 and is_deleted=0 and form_type=2 and sample_mst_id='$data[1]' order by id asc";

                $resultA=sql_select($sql_qryA);
                $i=1;$k=0; $req_dzn_ra=0; $req_qty_ra=0;
                foreach($resultA as $rowA)
                {
					$req_dzn_ra=$req_dzn_ra+$rowA[csf('req_dzn_ra')];
					$req_qty_ra=$req_qty_ra+$rowA[csf('req_qty_ra')];
					$k++;
					?>
					<tr>
                        <td align="center"><? echo $k;?></td>
                        <td align="left"><? echo $sample_library[$rowA[csf('sample_name_ra')]];?></td>
                        <td align="left"><? echo $garments_item[$rowA[csf('gmts_item_id_ra')]];?></td>
                        <td align="left"><? echo $trims_group_lib[$rowA[csf('trims_group_ra')]];?></td>
                        <td align="left"><? echo $rowA[csf('description_ra')];?></td>
                        <td align="left"><? echo $supplier_library[$rowA[csf('supplier_id')]];?></td>
                        <td align="left"><? echo $rowA[csf('brand_ref_ra')];?></td>
                        <td align="center"><? echo $unit_of_measurement[$rowA[csf('uom_id_ra')]];?></td>
                        <td align="right"><? echo number_format($rowA[csf('req_dzn_ra')],2);?></td>
                        <td align="right"><? echo number_format($rowA[csf('req_qty_ra')],2);?></td>
                        <td align="left"><? echo $fabric_source[$rowA[csf('fabric_source')]];?></td>
                        <td align="left"><? echo change_date_format($rowA[csf('delivery_date')]);?></td>
                        <td align="left"><? echo $rowA[csf('remarks_ra')];?></td>
					</tr>
					<?
                }
                ?>
                <tr>
                    <td colspan="8" align="center"><b>Total </b></td>
                    <td align="right"><b><? echo number_format($req_qty_ra,2);?> </b></td>
                    <td>&nbsp;</td>
                </tr>
            </tbody>
        </table>
        <br>
        <table align="left" cellspacing="0" border="1" width="1000" class="rpt_table" rules="all" style="margin-top: 5px;font-size:14px">
            <thead>
                <tr>
                	<td colspan="9" align="center"><strong>Required Emebellishment</td>
                </tr>
                <tr>
                    <th width="30">Sl</th>
                    <th width="100">Sample Name</th>
                    <th width="110">Garment Item</th>
                    <th width="110">Body Part</th>
                    <th width="100">Supplier</th>
                    <th width="60">Name</th>
                    <th width="70">Type</th>
                    <th width="100">Emb.Del.Date</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
				<?
                $sql_qry="SELECT id,sample_mst_id,sample_name_re,gmts_item_id_re,name_re,type_re,remarks_re,body_part_id,delivery_date,supplier_id from sample_development_fabric_acc where sample_mst_id='$data[1]' and form_type=3 and is_deleted=0  and status_active=1 order by id asc";

                $result=sql_select($sql_qry); $k=0;
                $type_array=array(1=>$emblishment_print_type,2=>$emblishment_embroy_type,3=>$emblishment_wash_type,4=>$emblishment_spwork_type,5=>$emblishment_gmts_type);
                foreach($result as $row)
                {
					$k++;
					?>
					<tr>
                        <td align="center"><? echo $k;?></td>
                        <td align="left"><? echo $sample_library[$row[csf('sample_name_re')]];?></td>
                        <td align="left"><? echo $garments_item[$row[csf('gmts_item_id_re')]];?></td>
                        <td align="left"><? echo $body_part[$row[csf('body_part_id')]];?></td>
                        <td align="left"><? echo $supplier_library[$row[csf('supplier_id')]];?></td>
                        <td align="left"><? echo $emblishment_name_array[$row[csf('name_re')]];?></td>
                        <td align="left">
                            <?
                            if($row[csf('name_re')]==1) echo $emblishment_print_type[$row[csf('type_re')]];
                            if($row[csf('name_re')]==2) echo $emblishment_embroy_type[$row[csf('type_re')]];
                            if($row[csf('name_re')]==3) echo $emblishment_wash_type[$row[csf('type_re')]];
                            if($row[csf('name_re')]==4) echo $emblishment_spwork_type[$row[csf('type_re')]];
                            if($row[csf('name_re')]==5) echo $emblishment_gmts_type[$row[csf('type_re')]];
                            ?>
                        </td>
                        <td align="left"><? echo change_date_format($row[csf('delivery_date')]);?></td>
                        <td align="left"><? echo $row[csf('remarks_re')];?></td>
                    </tr>
					<?
                }
                ?>
            </tbody>
        </table>
          <br>
        	<table  style="margin-top: 10px;font-size:13px;float:left;margin-right:1%" class="rpt_table" width="30%" align="left" border="1" cellpadding="0" cellspacing="0" rules="all">
                <caption> <b> Yarn Required Summary- </b> </caption>
                	<thead>
                    	<tr align="center">
                        	<th align="center" width="40">Sl</th>
                        	<th align="center">Yarn Desc.</th>
                             <th align="center">Req. Qty</th> 
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count"  );
					$lib_supllier_arr=return_library_array( "select supplier_id,booking_no from wo_non_ord_samp_booking_mst where booking_no='$txt_booking_no' and entry_form_id=140", "booking_no", "supplier_id"  );
					$tot_req_qty=0;

					
					$sql_yarn="select b.count_id,b.copm_one_id,b.percent_one,b.type_id,sum(b.cons_qnty) as  cons_qnty from  sample_development_yarn_dtls b where  b.status_active=1  and b.mst_id='$data[1]' and b.determin_id in (select determination_id from sample_development_fabric_acc  where status_active=1 and sample_mst_id='$data[1]' and form_type=1) group by b.count_id,b.copm_one_id,b.percent_one,b.type_id";
					//echo $sql_yarn;
					$data_array=sql_select($sql_yarn);

					
					if ( count($data_array)>0)
					{
						$l=1;
						foreach( $data_array as $key=>$row )
						{
							$yarn_des=$lib_yarn_count[$row[csf("count_id")]].','.$composition[$row[csf("copm_one_id")]].$yarn_type[$row[csf("type_id")]];
							?>
                            	<tr>
                                    <td> <? echo $l;?> </td>
                                    <td> <? echo $yarn_des; ?> </td>
                                    <td align="right"> <? echo number_format($row[csf("cons_qnty")],2); ?> </td>
                                </tr>
                            <?
                            $l++;
							$tot_req_qty+=$row[csf("cons_qnty")];
						}
					}

					?>
                    <tr>
						<th  colspan="2" align="right"><b>Total</b></th>
						<th  align="right"><? echo number_format($tot_req_qty,2);?></th>
					</tr>
                </tbody>
            </table>
			<table  style="margin-top: 10px;font-size:14px ;float:left" class="rpt_table" width="69%" align="left" border="1" cellpadding="0" cellspacing="0" rules="all">
                <caption> <b> Dyes To Match</b> </caption>
                	<thead>
                    	<tr align="center">
                        	<th align="center" width="40">Sl</th>
                        	<th align="center">Item</th>
							<th align="center">Item Desc.</th>
							 <th align="center">Body Color</th>
                        	<th align="center">Item Color</th>
                             <th align="center">Finish  Qty</th> 
							 <th align="center">UOM</th> 
                        </tr>
                    </thead>
                    <tbody>
                    <?
				
					$lib_item_group_arr=return_library_array( "select item_name, id from lib_item_group where item_category=4 and is_deleted=0  and  status_active=1 order by item_name", "id", "item_name");
					$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count"  );

					$lib_supllier_arr=return_library_array( "select supplier_id,booking_no from wo_non_ord_samp_booking_mst where booking_no='$txt_booking_no' and entry_form_id=140", "booking_no", "supplier_id"  );
					$tot_req_qty=0;

					$dtm_arr_item_color=array();
					$sql=sql_select("select sample_req_fabric_cost_id,fabric_color,sample_req_trim_cost_id,item_color,sum(qty) as qty from sample_dev_dye_to_match where booking_no='$txt_booking_no'  and status_active=1 and is_deleted=0 group by sample_req_fabric_cost_id,fabric_color,item_color,sample_req_trim_cost_id");
					
					foreach($sql as $row){
						$dtm_arr[$row[csf('fabric_color')]][$row[csf('sample_req_trim_cost_id')]]+=$row[csf('qty')];
						$dtm_arr_item_color[$row[csf('fabric_color')]][$row[csf('sample_req_trim_cost_id')]]=$row[csf('item_color')];
					}




					
					$dye_to_match_sql="select a.id,a.sample_mst_id, a.trims_group_ra as trim_group, a.fabric_description as description,a.uom_id_ra as cons_uom,sum(a.req_qty_ra) as req_qty_ra,  c.fabric_color as fabric_color_id,a.description_ra    FROM sample_development_fabric_acc a,  wo_non_ord_samp_booking_dtls c
					WHERE c.style_id=a.sample_mst_id  and a.form_type=2 and c.booking_no ='$txt_booking_no' and c.status_active=1 and  c.status_active=1  and a.status_active=1 and c.is_deleted=0
					group by a.id,a.sample_mst_id, a.trims_group_ra,a.fabric_description,a.uom_id_ra,  c.fabric_color ,a.description_ra    order by a.id  ";
					//echo $sql_yarn;
					$data_array=sql_select($dye_to_match_sql);

					
					if ( count($data_array)>0)
					{
						$l=1;
						foreach( $data_array as $key=>$row )
						{
							$yarn_des=$lib_yarn_count[$row[csf("count_id")]].','.$composition[$row[csf("copm_one_id")]].','.$row[csf("percent_one")].'%,'.$yarn_type[$row[csf("type_id")]];
							?>
                            	<tr>
                                    <td> <? echo $l;?> </td>
                                    <td> <? echo $lib_item_group_arr[$row[csf("trim_group")]]; ?> </td>
                                    <td align="left"> <? echo $row[csf("description_ra")]; ?> </td>							
                              								
									<td> <? echo  $color_library[$row[csf('fabric_color_id')]]; ?> </td>
                                    <td > <? echo $color_library[$dtm_arr_item_color[$row[csf('fabric_color_id')]][$row[csf('id')]]]; ?> </td>									
									<td align="right"> <? echo $dtm_arr[$row[csf('fabric_color_id')]][$row[csf('id')]] ;//echo $row[csf("fin_fab_qnty")]; //echo $dtm_arr[$fabric_cost_id][$color][$row[csf('id')]]?> </td>
                                    <td align="right"> <? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?> </td>
                                </tr>
                            <?
                            $l++;
							$tot_req_qty+=$dtm_arr[$row[csf('fabric_color_id')]][$row[csf('id')]] ;;
						}
					}

					?>
                    <tr>
						<th  colspan="5" align="right"><b>Total</b></th>
						<th  align="right"><? echo number_format($tot_req_qty,2);?></th>
					</tr>
                </tbody>
            </table>
        <br>
        <br>
        <?
        	$sample_stripe_data=sql_select("SELECT a.body_part_id, b.contrast, b.color_id, b.grey_fab_qnty,c.id as strip_mst_id, c.stripe_color, c.measurement, c.uom, c.fabreq, c.yarn_dyed,c.totfidder FROM sample_development_fabric_acc a join sample_development_rf_color b on a.id=b.dtls_id join wo_sample_stripe_color c on a.id=c.sample_fab_dtls_id and b.color_id=c.color_number_id where a.status_active=1 and a.is_deleted=0 and a.color_type_id in (2,3,4,6,31,32,33,34) and a.form_type=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.sample_mst_id=$data[1]");
        	
        	foreach ($sample_stripe_data as $row) {
        		$key=$row[csf('body_part_id')].'*'.$row[csf('color_id')];
        		$sample_stripe_arr[$key]['body_part_id'] = $row[csf('body_part_id')];
        		$sample_stripe_arr[$key]['fabric_color'] = $row[csf('color_id')];
        		$sample_stripe_arr[$key]['fabric_qty'] = $row[csf('grey_fab_qnty')];
				
        		$sample_stripe_arr[$key]['stripe_color'][$row[csf('strip_mst_id')]][$row[csf('stripe_color')]]['color'] = $row[csf('stripe_color')];
        		$sample_stripe_arr[$key]['stripe_color'][$row[csf('strip_mst_id')]][$row[csf('stripe_color')]]['measurement'] = $row[csf('measurement')];
        		$sample_stripe_arr[$key]['stripe_color'][$row[csf('strip_mst_id')]][$row[csf('stripe_color')]]['uom'] = $row[csf('uom')];
        		$sample_stripe_arr[$key]['stripe_color'][$row[csf('strip_mst_id')]][$row[csf('stripe_color')]]['qty'] = $row[csf('fabreq')];
        		$sample_stripe_arr[$key]['stripe_color'][$row[csf('strip_mst_id')]][$row[csf('stripe_color')]]['yarn_dyed'] = $row[csf('yarn_dyed')];
				$sample_stripe_arr[$key]['stripe_color'][$row[csf('strip_mst_id')]][$row[csf('stripe_color')]]['totfidder'] = $row[csf('totfidder')];
        		
				$stripe_color_summ[$row[csf('stripe_color')]] += $row[csf('fabreq')];
        	}
        ?>
        <div style="width:1000px; ">
	        <table align="left" cellspacing="0" border="1" style="width:860px;float: left; right; margin-top: 5px;font-size:14px" class="rpt_table" rules="all">
	        	<thead>
	        		<tr>
	        			<th colspan="10">Stripe Details</th>
	        		</tr>
	        		<tr>
	        			<th width="30">SL</th>
	        			<th width="100">Body Part</th>
	        			<th width="60">Fabric Color</th>
	        			<th width="60">Fabric Qty(KG)</th>
	        			<th width="60">Stripe Color</th>
	        			<th width="60">Stripe Measurement</th>
	        			<th width="60">Stripe Uom</th>
						<th width="60">Total Feeder</th>
	        			<th width="60">Qty.(KG)</th>
	        			<th width="60">Y/D Req.</th>
	        		</tr>
	        	</thead>
	        	<tbody>
	        		<? $sl=1;
	        		foreach ($sample_stripe_arr as $sdata) {
	        			$rowspan = count($sdata['stripe_color']);
	        			$i=1;
	        			foreach ($sdata['stripe_color'] as $stripe_mst) {
							foreach ($stripe_mst as $stripe_data) {
	        				if($i==1){
	        					$total_fabric += $sdata['fabric_qty'];
	        					$total_stripe_fabric += $stripe_data['qty'];
	        				?>
	        				<tr>
			        			<td rowspan="<?=$rowspan?>"><?= $sl; ?></td>
			        			<td rowspan="<?=$rowspan?>"><?= $body_part[$sdata['body_part_id']]; ?></td>
			        			<td rowspan="<?=$rowspan?>"><?= $color_library[$sdata['fabric_color']]; ?></td>
			        			<td align="right" rowspan="<?=$rowspan?>"><?= $sdata['fabric_qty']; ?></td>
			        			<td><?= $color_library[$stripe_data['color']]; ?></td>
			        			<td align="right"><?= $stripe_data['measurement']; ?></td>
			        			<td><?= $unit_of_measurement[$stripe_data['uom']]; ?></td>
								<td align="right"><?= $stripe_data['totfidder']; ?></td>
			        			<td align="right"><?= $stripe_data['qty']; ?></td>
			        			<td><?= $yes_no[$stripe_data['yarn_dyed']]; ?></td>
			        		</tr>
	        				<?
	        					$i++;
	        				}
	        				else{
	        					$total_stripe_fabric += $stripe_data['qty'];
	        					?>
	        						<tr>
	        							<td><?= $color_library[$stripe_data['color']]; ?></td>
					        			<td align="right"><?= $stripe_data['measurement']; ?></td>
					        			<td><?= $unit_of_measurement[$stripe_data['uom']]; ?></td>
										<td align="right"><?= $stripe_data['totfidder']; ?></td>
					        			<td align="right"><?= $stripe_data['qty']; ?></td>
					        			<td><?= $yes_no[$stripe_data['yarn_dyed']]; ?></td>
	        						</tr>
	        					<?
	        				}
	        			}
	        			$sl++;
						}
	        		} ?>
	        	</tbody>
	        	<tfoot>
	        		<tr>
	        			<th colspan="3">Total</th>
	        			<th align="right"><?= $total_fabric ?></th>
	        			<th></th>
	        			<th></th>
	        			<th></th>
						<th></th>
	        			<th align="right"><?= $total_stripe_fabric ?></th>
	        			<th></th>
	        		</tr>
	        	</tfoot>
	        </table>
	        <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:180px; margin-left: 2px; float: right; margin-top: 5px;font-size:14px" rules="all">
		        <thead>
		        	<tr>
		        		<th colspan="3">Stripe Color wise Summary</th>
		        	</tr>
		        	<tr>
		        		<th>SL</th>
		        		<th>Stripe Color</th>
		        		<th>Qty.(KG)</th>
		        	</tr>
		        </thead>
		        <tbody>
		        	<?
		        	$sl=1;
		        	foreach ($stripe_color_summ as $color_id => $value) {
		        	 	$total_fabric_qty+= $value;
		        	?>
		        	<tr>
		        		<td><?= $sl ?></td>
		        		<td><?= $color_library[$color_id]; ?></td>
		        		<td><?= $value ?></td>
		        	</tr>
		        	<? $sl++;
		        	} ?>
		        </tbody>
		        <tfoot>
		        	<tr>
		        		<th colspan="2">Total</th>
		        		<th><?= $total_fabric_qty; ?></th>
		        	</tr>
		        </tfoot>
	        </table>
        </div>
        <?
			$coller_cuff_data=sql_select("SELECT a.sample_color, a.size_id, a.item_size, a.qnty_pcs, c.body_part_type from sample_requisition_coller_cuff a join sample_development_fabric_acc b on b.id=a.dtls_id join lib_body_part c on b.body_part_id=c.id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.sample_mst_id=$data[1]");
			 
			$coller_data_arr=array(); $cuff_data_arr=array();
			foreach ($coller_cuff_data as $row) {
				if($row[csf('body_part_type')]==40)
				{
					$coller_size_arr[$row[csf('size_id')]]=$row[csf('size_id')];
					$color_size_data[$row[csf('size_id')]]['item_size']=$row[csf('item_size')];
					$color_color_data[$row[csf('sample_color')]][$row[csf('size_id')]]['qnty_pcs']=$row[csf('qnty_pcs')];
				}
				if($row[csf('body_part_type')]==50)
				{
					$cuff_size_arr[$row[csf('size_id')]]=$row[csf('size_id')];
					$cuff_size_data[$row[csf('size_id')]]['item_size']=$row[csf('item_size')];
					$cuff_color_data[$row[csf('sample_color')]][$row[csf('size_id')]]['qnty_pcs']=$row[csf('qnty_pcs')];
				}
			} 
			/*echo '<pre>';
			print_r($color_color_data); die;*/
        ?>
        <div style="width:1000px; margin-top: 10px;">
            <?
            $collar_cuff_percent_arr=array(); $collar_cuff_body_arr=array(); $collar_cuff_color_arr=array(); $collar_cuff_size_arr=array(); $collar_cuff_item_size_arr=array(); $color_size_sensitive_arr=array();

			$collar_cuff_sql="select b.id, b.gmts_item_id as item_number_id, a.qnty_pcs,a.sample_color as color_number_id, a.size_id as gmts_sizes, a.item_size, a.size_id as size_number_id,  e.body_part_full_name, e.body_part_type
			FROM sample_requisition_coller_cuff a left join lib_size s on a.size_id=s.id, sample_development_fabric_acc b, lib_body_part  e

			WHERE b.id=a.dtls_id   and b.body_part_id=e.id and e.body_part_type in (40,50)  and b.sample_mst_id=$data[1] and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and e.status_active=1 and e.is_deleted=0 order by  b.id,a.sample_color,s.sequence";
			//echo $collar_cuff_sql;
			$collar_cuff_sql_res=sql_select($collar_cuff_sql);
			$itemIdArr=array();

			foreach($collar_cuff_sql_res as $collar_cuff_row)
			{
				$collar_cuff_percent_arr[$collar_cuff_row[csf('body_part_type')]][$collar_cuff_row[csf('body_part_full_name')]][$collar_cuff_row[csf('color_number_id')]][$collar_cuff_row[csf('gmts_sizes')]]=$collar_cuff_row[csf('colar_cuff_per')];
				$collar_cuff_body_arr[$collar_cuff_row[csf('body_part_type')]][$collar_cuff_row[csf('body_part_full_name')]]=$collar_cuff_row[csf('body_part_full_name')];
				$collar_cuff_size_arr[$collar_cuff_row[csf('body_part_type')]][$collar_cuff_row[csf('body_part_full_name')]][$collar_cuff_row[csf('size_number_id')]]=$collar_cuff_row[csf('size_number_id')];
				if(!empty($collar_cuff_row[csf('item_size')]))
				{
					$collar_cuff_item_size_arr[$collar_cuff_row[csf('body_part_full_name')]][$collar_cuff_row[csf('size_number_id')]][$collar_cuff_row[csf('item_size')]]=$collar_cuff_row[csf('item_size')];
				}
				
				$color_size_sensitive_arr[$collar_cuff_row[csf('body_part_full_name')]][$collar_cuff_row[csf('id')]][$collar_cuff_row[csf('color_number_id')]]=$collar_cuff_row[csf('qnty_pcs')];
				// $collar_cuff_size_Qty_arr[$collar_cuff_row[csf('body_part_type')]][$collar_cuff_row[csf('body_part_full_name')]][$collar_cuff_row[csf('id')]][$collar_cuff_row[csf('size_number_id')]]=$collar_cuff_row[csf('qnty_pcs')];

				$collar_cuff_size_Qty_arr[$collar_cuff_row[csf('body_part_type')]][$collar_cuff_row[csf('body_part_full_name')]][$collar_cuff_row[csf('id')]][$collar_cuff_row[csf('color_number_id')]][$collar_cuff_row[csf('size_number_id')]]=$collar_cuff_row[csf('qnty_pcs')];
				
				$itemIdArr[$collar_cuff_row[csf('body_part_type')]].=$collar_cuff_row[csf('item_number_id')].',';
			}
			//print_r($collar_cuff_percent_arr[40]) ;
			unset($collar_cuff_sql_res);
			//$count_collar_cuff=count($collar_cuff_size_arr);
			
			/*$order_plan_qty_arr=array();
			$color_wise_wo_sql_qnty=sql_select( "select item_number_id, color_number_id, size_number_id, sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as order_quantity  from wo_po_color_size_breakdown where  po_break_down_id in ($booking_po_id) and status_active=1 and is_deleted =0  group by item_number_id, color_number_id, size_number_id");//and item_number_id in (".implode(",",$itemIdArr).")
			foreach($color_wise_wo_sql_qnty as $row)
			{
				$order_plan_qty_arr[$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['plan']=$row[csf('plan_cut_qnty')];
				$order_plan_qty_arr[$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['order']=$row[csf('order_quantity')];
			}
			unset($color_wise_wo_sql_qnty);*/

			
			foreach($collar_cuff_body_arr as $body_type=>$body_name)
			{
				$gmtsItemId=array_filter(array_unique(explode(",",$itemIdArr[$body_type])));
				foreach($body_name as $body_val)
				{
					$count_collar_cuff=count($collar_cuff_size_arr[$body_type][$body_val]);
					$pre_grand_tot_collar=0; $pre_grand_tot_collar_order_qty=0;

					?>
                    <div style="max-height:1330px; overflow:auto; float:left; padding-top:5px; margin-left:5px; margin-bottom:5px; position:relative;font-size:18px;">
					<table width="625" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                        <tr>
                        	<td colspan="<? echo $count_collar_cuff+3; ?>" align="center"><b><? echo $body_val; ?> - Color Size Brakedown in Pcs.</b></td>
                        </tr>
                        <tr>
                            <td width="100">Size</td>
								<?
                                foreach($collar_cuff_size_arr[$body_type][$body_val]  as $size_number_id)
                                {
									?>
									<td align="center" style="border:1px solid black"><strong><? echo $size_library[$size_number_id];?></strong></td>
									<?
                                }
                                ?>
                            <td width="60" rowspan="2" align="center"><strong>Total</strong></td>
                            <td rowspan="2" align="center"><strong>Extra %</strong></td>
                        </tr>
                        <tr>
                            <td style="font-size:12px"><? echo $body_val; ?> Size</td>
                            <?
                            foreach($collar_cuff_item_size_arr[$body_val]  as $size_number_id=>$size_number)
                            {
								if(count($size_number)>0)
								{
									 foreach($size_number  as $item_size=>$val)
									 {
										?>
										<td align="center" style="border:1px solid black"><strong><? echo $item_size;?></strong></td>
										<?
									 }
								}
								else
								{
									?>
									<td align="center" style="border:1px solid black"><strong>&nbsp;</strong></td>
									<?
								}
                            }
                            ?>
                        </tr>
                            <?

                            $pre_size_total_arr=array();
                            foreach($color_size_sensitive_arr[$body_val] as $fab_req_id=>$pre_cost_data)
                            {
								foreach($pre_cost_data as $color_number_id=>$color_number_data)
								{
									//foreach($color_number_data as $color_size_sensitive=>$color_break_down)
									//{
										$pre_color_total_collar=0;
										$pre_color_total_collar_order_qnty=0;
										 
										?>
										<tr>
											<td>
												<?
                                               
												 echo $color_library[$color_number_id];
                                                ?>
											</td>
											<?
											foreach($collar_cuff_size_arr[$body_type][$body_val] as $size_number_id)
											{
												?>
												<td align="center" style="border:1px solid black">
													<?   $collerqty=0;  
													$color_cuff_cut=0;
													// $color_cuff_cut=$collar_cuff_size_Qty_arr[$body_type][$body_val][$fab_req_id][$size_number_id];
													$color_cuff_cut=$collar_cuff_size_Qty_arr[$body_type][$body_val][$fab_req_id][$color_number_id][$size_number_id];
                                                	if($body_type==50){
														// $collerqty=$color_cuff_cut*2;
														$collerqty=$color_cuff_cut;
													}else{
														$collerqty=$color_cuff_cut;
													}
                                                    echo number_format($collerqty);
                                                    $pre_size_total_arr[$size_number_id]+=$collerqty;
                                                    $pre_color_total_collar+=$collerqty;
                                                    $pre_color_total_collar_order_qnty+=$color_cuff_cut;
                                                    ?>
												</td>
												<?
											}
											?>

											<td align="center"><? echo number_format($pre_color_total_collar); ?></td>
											<!-- <td align="center"><? echo number_format((($pre_color_total_collar-$pre_color_total_collar_order_qnty)/$pre_color_total_collar_order_qnty)*100,2); ?></td> -->
												<td align="center"><? echo $collar_ex_per; ?></td>
										</tr>
										<?
										$pre_grand_collar_ex_per+=$collar_ex_per;
										$pre_grand_tot_collar+=$pre_color_total_collar;
										$pre_grand_tot_collar_order_qty+=$pre_color_total_collar_order_qnty;
									
								}
							}
							?>
                        
                        <tr>
                            <td>Size Total</td>
								<?
                               // foreach($pre_size_total_arr  as $size_qty)
                               // {
                                	foreach($collar_cuff_size_arr[$body_type][$body_val] as $size_number_id)
									{
										$size_qty=$pre_size_total_arr[$size_number_id];
										?>
										<td style="border:1px solid black;  text-align:center"><? echo number_format($size_qty); ?></td>
										<?
									}

                               // }
                                ?>
                            <td style="border:1px solid black; text-align:center"><? echo number_format($pre_grand_tot_collar); ?></td>
                            <!-- <td align="center" style="border:1px solid black"><? echo number_format((($pre_grand_tot_collar-$pre_grand_tot_collar_order_qty)/$pre_grand_tot_collar_order_qty)*100,2); ?></td> -->
							<td align="center" style="border:1px solid black"></td>
                        </tr>
					</table>
                </div>
                <?
            }
        }
			?>
        </div>

		<br><br><br>
		<table style="margin-top:10px; font-size:14px" class="rpt_table" width="600" align="left" border="1" cellpadding="0" cellspacing="0" rules="all">
            <thead>
                <tr>
                    <th width="40">Sl</th>
                    <th>Special Instruction</th>
                </tr>
            </thead>
            <tbody>
				<?
                $data_array=sql_select("select id, terms from  wo_booking_terms_condition where entry_form=140 and booking_no='$data[2]'");
                if(count($data_array)>0)
                {
					$l=1;
					foreach( $data_array as $key=>$row )
					{
						?>
						<tr>
                            <td><? echo $l;?> </td>
                            <td style="word-break:break-all"><? echo $row[csf("terms")]; ?> </td>
						</tr>
						<?
						$l++;
					}
                }
                ?>
            </tbody>
        </table>     


    	<table align="left" cellspacing="0" width="810" class="rpt_table" >
        	<tr>
            	<td colspan="6" align="left">
					<?

						$user_id=$_SESSION['logic_erp']['user_id'];
						$user_arr=return_library_array( "select id, USER_NAME from user_passwd where id=$user_id", "id", "USER_NAME");
						$prepared_by = $user_arr[$user_id];
	                      //echo signature_table(134, $data[0], "810px");
					  	echo signature_table(134, $data[0], "1080px",$cbo_template_id,$padding_top = 70,$prepared_by);
                    ?>
                </td>
            </tr>

        </table>
			</div>
   <script type="text/javascript" src="../../js/jquery.js"></script>
   <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
   <script>
		function fnc_generate_Barcodes( valuess, img_id )
		{
			var value = valuess;//$("#barcodeValue").val();
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer ='bmp';// $("input[name=renderer]:checked").val();
			var settings = {
			  output:renderer,
			  bgColor: '#FFFFFF',
			  color: '#000000',
			  barWidth: 1,
			  barHeight: 60,
			  moduleSize:5,
			  posX: 10,
			  posY: 20,
			  addQuietZone: 1
			};
			$("#"+img_id).html('11');
			 value = {code:value, rect: false};
			$("#"+img_id).show().barcode(value, btype, settings);
		}
   </script>
   <script type="text/javascript">
   	fnc_generate_Barcodes('<? echo $barcode_no; ?>','barcode_img_id');
   </script>
	<?
    exit();
}

if($action=="sample_requisition_print9")// md mamun ahmed sagor//copy button-4//crm-21058
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$cbo_template_id=$data[3];
	$path="../../";
	if(count($data)>3)
	{
		if($data[4]=='../')
		{
			$path=$data[4];
		}
	}
	//echo $path;
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$supplier_library=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );

	$company_img=sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");


	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$dealing_merchant_library=return_library_array( "select id,team_member_name from lib_mkt_team_member_info", "id", "team_member_name"  );
	$team_leader_arr=return_library_array( "select id,team_leader_name from lib_marketing_team", "id", "team_leader_name"  );
	$user_arr=return_library_array( "select id, user_name from user_passwd", "id", "user_name"  );
	$sample_library=return_library_array( "select id, sample_name from lib_sample", "id", "sample_name");
	$size_library=return_library_array( "select id, size_name from lib_size", "id", "size_name"  );
	$color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
	$season_arr=return_library_array( "select id,season_name from lib_buyer_season  where status_active =1 and is_deleted=0 ", "id", "season_name"  );
	$trims_group_lib=return_library_array( "select id,item_name from lib_item_group  where status_active =1 and is_deleted=0", "id", "item_name");
	//concate(buyer_name,'_',contact_person)
	$appDate=return_field_value("approved_date","approval_history","entry_form=25 and mst_id='$data[1]' order by id desc");
	$appBy=return_field_value("approved_by","approval_history","entry_form=25 and mst_id='$data[1]'");
	$user_arr=return_library_array( "select id, user_name from user_passwd", "id", "user_name"  );
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='sample_details_1' and file_type=1",'master_tble_id','image_location');

	


	$booking_sql= "SELECT booking_no,booking_date,company_id,buyer_id,item_category,fabric_source,currency_id,exchange_rate,pay_mode,booking_month,delivery_date,source,booking_year,is_approved,ready_to_approved,team_leader,	dealing_marchant,style_desc,source,revised_no,rmg_process_breakdown,insert_date from wo_non_ord_samp_booking_mst  where booking_no='$data[2]' and entry_form_id='140' and status_active=1 and is_deleted=0 order by booking_no desc ";
	// echo $booking_sql;
	$booking_data=sql_select($booking_sql);

	$sql="SELECT id, requisition_number, requisition_number_prefix_num, style_ref_no, buyer_name, season, product_dept, dealing_marchant, agent_name, buyer_ref, bh_merchant, estimated_shipdate, team_leader, season_buyer_wise, remarks, quotation_id, sample_stage_id, requisition_date, material_delivery_date,insert_date,internal_ref,fit_id,style_desc from sample_development_mst where  id='$data[1]' and entry_form_id=203 and  is_deleted=0  and status_active=1";
	$dataArray=sql_select($sql);
	
	$po_id_all=$dataArray[0][csf('po_break_down_id')];
	$booking_uom=$dataArray[0][csf('uom')];
	$bookingup_date=$dataArray[0][csf('update_date')];
	$req_date=$dataArray[0][csf('insert_date')];
	$booking_date=$booking_data[0][csf('insert_date')];
	$revised_no=$booking_data[0][csf('revised_no')];
	
	$bookingcompany_id=$dataArray[0][csf('company_id')];
	$bookingbuyer_id=$dataArray[0][csf('buyer_id')];
	$nameArray_size=sql_select("SELECT a.id,a.sample_mst_id,a.sample_name,a.gmts_item_id,a.smv,a.article_no,a.sample_color,a.sample_prod_qty,a.submission_qty,a.delv_start_date,a.delv_end_date,a.sample_charge,a.sample_curency,a.sent_to_buyer_date,a.comments,c.dtls_id,c.size_id,c.bh_qty,c.self_qty,c.test_qty,c.plan_qty,c.dyeing_qty,c.others_qty,c.test_fit_qty,c.samp_dept_qty,c.total_qty from sample_development_dtls a,sample_development_size c where a.id=c.dtls_id and  a.status_active =1 and a.is_deleted=0 and a.entry_form_id=203 and a.sample_mst_id='$data[1]' order by a.id asc");$sizeidArr=array();
			foreach($nameArray_size  as $result_size)
			{
				$sizeidArr[$result_size[csf('size_id')]]['size']=$result_size[csf('size_id')];
				$sizeidArr[$result_size[csf('size_id')]]['plan']+=$result_size[csf('plan_cut_qnty')];
				$sizeidArr[$result_size[csf('size_id')]]['po']+=$result_size[csf('total_qty')];
				$po_qnty_tot1+=$result_size[csf('total_qty')];
				$garmentItemArr[$sample_library[$result_size[csf('sample_name')]]]=$sample_library[$result_size[csf('sample_name')]];
			}
			unset($nameArray_size);
 ?>
 <style>
	#mstDiv {
	    margin:0px auto;
	    width:1130px;

	}
	#mstDiv @media print {

	   thead {display: table-header-group;}

	}

	 @media print{
			html>body table.rpt_table {
			margin-left:12px;
	  		}

		}
	</style>

	<div id="mstDiv">
			<?
				
				foreach ($dataArray as $result)
				{
					$total_set_qnty=$result[csf('total_set_qnty')];
					$colar_excess_percent=$result[csf('colar_excess_percent')];
					$cuff_excess_percent=$result[csf('cuff_excess_percent')];
					$rmg_process_breakdown=$booking_data[0][csf('rmg_process_breakdown')];
					$brand_id=$result[csf('brand_id')];
					$extra=explode("_",$rmg_process_breakdown);
					
					$booking_percent=$result[csf('booking_percent')];
					$booking_po_id=$result[csf('po_break_down_id')];      
		
					$a_process_loss=$extra[14]+$extra[8]+$extra[6]+$extra[12]+$extra[0]+$extra[10]+$extra[2]+$extra[1];//+$extra[13]
					$b_process_loss=$extra[4]+$extra[3]+$extra[15];
					$tot_pro_loss=$a_process_loss+$b_process_loss;
		
					/*if($db_type==2) $group_concat_all=" listagg(cast(b.grouping as varchar2(4000)),',') within group (order by b.grouping) as grouping,
					listagg(cast(b.file_no as varchar2(4000)),',') within group (order by b.file_no) as file_no  ";
					else { $group_concat_all="group_concat(b.grouping) as grouping, group_concat(b.file_no) as file_no";}
					$data_array3=sql_select("select a.job_no,a.company_name,a.buyer_name,$group_concat_all from wo_po_details_master a, wo_po_break_down b where b.id in (".$result[csf('po_break_down_id')].") and a.job_no=b.job_no_mst group by a.job_no,a.company_name,a.buyer_name");*/
		
						//  print_r($total_fin_fabqnty);
		
					ob_start();
					if($brand_id>0) $brand_id_name="/".$brand_name_arr[$brand_id];else  $brand_id_name='';
					?>
					<table  class="rpt_table"  border="1" style="border:1px solid black;"   cellpadding="0"  width="1100" cellspacing="0" rules="all" >
						<tr>
							<td align="center" colspan="2"><img  src="<?=$path;?><? echo $company_img[0][csf("image_location")]; ?>" height='5%' width='7%' align="left"/><p style=" margin-top:2%;"><?=$company_library[$data[0]]; 
							if(str_replace("'","",$id_approved_id) ==1){ ?>
								<span style="font-size:25px; float:right;"><strong> <font style="color:green"> <? echo "Approved"; ?> </font></strong></span> 
							 <? } ?>
					
							</p></td>
							<?
							$nameArray_approved=sql_select( "SELECT approved_by,approved_date from wo_non_ord_samp_booking_mst a, approval_history b where a.id=b.mst_id and a.booking_no='$data[2]' and b.entry_form=9 and a.status_active =1 and a.is_deleted=0 order by b.id desc ");
							$approved_by= $user_arr[$nameArray_approved[0][csf("approved_by")]];
	             			$approved_date= change_date_format($nameArray_approved[0][csf("approved_date")]);
							?>
							<td align="center" colspan="2" width="170">Approved By :<? echo $approved_by ?> </br>Approved Date :<? echo $approved_date ?></td>	
							<td align="center" width="100" colspan="2"> TEAM : &nbsp; <?=$team_leader_arr[$result[csf('team_leader')]];?></td>	
							<td align="center" width="100" colspan="2"> FIT: &nbsp; <?=$fit_list_arr[$result[csf('fit_id')]];?></td>
							<td align="center" width="100" colspan="2">Revised No:<? echo $revised_no;?></td>
							<td align="center" colspan="2" width="170">Sample Fabric Booking Sheet <br><?= $data[2];?>&nbsp;</td>
						</tr>
					 </table>
					 <table  class="rpt_table"  border="1" style="border:1px solid black;"   cellpadding="0"  width="1100" cellspacing="0" rules="all" >
						<tr>
							<td width="80" style="font-size:16px;"><b>Portal Date</b></td>
							<td width="40" style="font-size:16px;" align="center"><b>:</b></td>
							<td width="100" style="font-size:16px;" colspan="2">&nbsp;<b><?=change_date_format($req_date);?></b></td>
							<td width="110" style="font-size:16px;"><b>Requisition No.</b></td>
							<td width="40" style="font-size:16px;" align="center"><b>:</b></td>
							<td width="100" style="font-size:16px;" colspan="2">&nbsp;<b><?= $dataArray[0][csf("requisition_number")]; ?></b></td>
							<td width="110" style="font-size:16px;"><b>Internal Ref</b></td>
							<td width="40" style="font-size:16px;" align="center"><b>:</b></td>
							<td width="100" style="font-size:16px;" colspan="2">&nbsp;<b><? echo $result[csf('internal_ref')];?></b></td>
						</tr>
						<tr>
							<td width="150" style="font-size:16px;"><b>Prepared Date</b></td>
							<td width="40" style="font-size:16px;" align="center"><b>:</b></td>
							<td width="100" style="font-size:16px;" colspan="2">&nbsp;<b><?=change_date_format($booking_date);?></b></td>
							<td width="150" style="font-size:16px;"><b>Style Description</b></td>
							<td width="40" style="font-size:16px;" align="center"><b>:</b></td>
							<td width="100" style="font-size:16px;" colspan="2">&nbsp;<b><? echo $result[csf('style_desc')]; $job_no= $result[csf('job_no')];?></b></td>
							<td width="150" style="font-size:16px;"><b>Sample Sub Date:</b></td>
							<td width="40" style="font-size:16px;" align="center"><b>:</b></td>
							<td width="100" style="font-size:16px;" colspan="2">&nbsp;<b><?= change_date_format($dataArray[0][csf('material_delivery_date')]); ?></b></td>
						</tr>
						<tr>
							<td width="150" style="font-size:16px;"><b>Buyer / Brand </b></td>
							<td width="40" style="font-size:16px;" align="center"><b>:</b></td>
							<td width="100" style="font-size:16px;" colspan="2">&nbsp;<b><? echo $buyer_library[$dataArray[0][csf('buyer_name')]].$brand_id_name; ?></b></td>
							<td width="150" style="font-size:16px;"><b>Order Quantity</b></td>
							<td width="40" style="font-size:16px;" align="center"><b>:</b></td>
							<td width="100" style="font-size:16px;" colspan="2">&nbsp;<b><?=$po_qnty_tot1;?></b></td>
							<td width="150" style="font-size:16px;"><b>Sample Stage</b></td>
							<td width="40" style="font-size:16px;" align="center"><b>:</b></td>
							<td width="100" style="font-size:16px;" colspan="2">&nbsp;<b><? echo $sample_stage[$dataArray[0][csf('sample_stage_id')]];?></b></td>
						</tr>
						<tr>
							<td width="150" style="font-size:16px;"><b>Style Ref. No.</b></td>
							<td width="40" style="font-size:16px;" align="center"><b>:</b></td>
							<td width="100" style="font-size:16px;" colspan="2"><b>&nbsp;<?=$dataArray[0][csf('style_ref_no')];?> </b></td>
							<td width="150" style="font-size:16px;"><b>Target Input Qty.</b></td>
							<td width="40" style="font-size:16px;" align="center"><b>:</b></td>
							<td width="100" style="font-size:16px;" colspan="2">&nbsp;<b><?=$po_qnty_tot1*((100+$tot_pro_loss-$a_process_loss)/100);?></b></td>
							<td width="150" style="font-size:16px;" rowspan="3"><b>Remarks/Desc:</b></td>
							<td width="40" style="font-size:16px;" align="center"rowspan="3"><b>:</b></td>
							<td width="100" style="font-size:16px;" colspan="2"rowspan="3"><b>&nbsp;<?=$dataArray[0][csf('remarks')];?> </b></td>
						</tr>
						<tr>
							<td width="150" style="font-size:16px;"><b>Garments Item</b></td>
							<td width="40" style="font-size:16px;" align="center"><b>:</b></td>
							<td width="100" style="font-size:16px;" colspan="2">&nbsp;<b><?
								echo implode(",",$garmentItemArr);
								?></b></td>
							<td width="150" style="font-size:16px;"><b>Gmts  Cons.(Dzn)</b></td>
							<td width="40" style="font-size:16px;" align="center"><b>:</b></td>
							<td width="100" style="font-size:16px;" colspan="2">&nbsp;<b><?=number_format(($sum_fin_fabqnty[0][csf('qty')]/$po_qnty_tot1)*12,2); ?></b></td>
						</tr>
						<tr>
							<td width="150" style="font-size:16px;"><b>Product Dept:</b></td>
							<td width="40" style="font-size:16px;" align="center"><b>:</b></td>
							<td width="100" style="font-size:16px;" colspan="2"><b>&nbsp;<b><? echo $product_dept[$dataArray[0][csf('product_dept')]]; ?></b></td>
							<td width="150" style="font-size:16px;"><b>Cutting Cons.(Dzn)</b></td>
							<td width="40" style="font-size:16px;" align="center"><b>:</b></td>
							<td width="100" style="font-size:16px;" colspan="2">&nbsp;<b><?
							$target_qty=$po_qnty_tot1*((100+$tot_pro_loss-$a_process_loss)/100);
							echo number_format(($sum_fin_fabqnty[0][csf('qty')]/$target_qty)*12,2);?></b></td>
						</tr>
					 </table>
					 <table  class="rpt_table"  border="1" style="border:1px solid black;"   cellpadding="0"  width="1100" cellspacing="0" rules="all" >
						<tr>
							<td width="150" style="font-size:16px;"><b>A) B/Input  Rej%</b></td>
							<td width="100" style="font-size:16px;"><b><b>(1)&nbsp; Fabric :&nbsp<?=$extra[14]+$extra[8]+$extra[6]+$extra[12]+$extra[0];?>%</b></td>
							<td width="100" style="font-size:16px;"><b>(2)&nbsp; Print:<?=$extra[10]+$extra[2];?>%</b></td>
							<td width="100" style="font-size:16px;"><b> (3)&nbsp; Embo :<?=$extra[1];?>%</b></td>
							<td width="100" style="font-size:16px;"></td><!--<b> (4)&nbsp; AOP :<?//=$extra[13];?>%</b>-->
							<td width="100" align="right" style="font-size:16px;"><b>Total:</b></td>
							<td width="50" align="center" style="font-size:16px;"><b><?=$extra[14]+$extra[8]+$extra[6]+$extra[12]+$extra[0]+$extra[10]+$extra[2]+$extra[1];//+$extra[13]?>%</b></td>
						</tr>
						<tr>
							<td width="150" style="font-size:16px;"><b>B)&nbsp; A/Input Extra/Rej %</b></td>					
							<td width="100" style="font-size:16px;"><b>(1)&nbsp; Sewing:<?=$extra[4];?>%</b></td>
							<td width="100" style="font-size:16px;"><b>(2)&nbsp; Wash:<?=$extra[3];?>%</b></td>
							<td width="100" style="font-size:16px;"><b>(3)&nbsp; Packing: <? echo $extra[15];  ?>%</b></td>
							<td width="100" style="font-size:16px;"><b></b></td>
							<td width="100" align="right" style="font-size:16px;"><b>Total: </b></td>
							<td width="50" align="center" style="font-size:16px;"><b><?=$extra[4]+$extra[3]+$extra[15];?>%</b></td>
						</tr>
					 </table>
					<?
				}


				// $nameArray_size=sql_select( "select min(id) as id, size_number_id, min(size_order) as size_order, sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as  order_quantity from wo_po_color_size_breakdown where po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and is_deleted=0 and status_active=1 group by size_number_id order by size_order"); 
				
			// print_r($sizeidArr);
			?>
            <table class="rpt_table" border="1" align="left" cellpadding="0"  width="1100" cellspacing="0" rules="all" >
                <tr>
                    <td style="border:1px solid black" colspan="2" width="80"><strong> Size</strong></td>										
                    <?
                    foreach($sizeidArr  as $sizeidstr=>$sizeval)
                    {
                        ?><td align="center" style="word-break:break-all" style="border:1px solid black"><strong><?=$size_library[$sizeidstr];?></strong></td>
                    <? } ?>
                    <td style="border:1px solid black; width:130px" align="center"><strong> Total Order Qty(Pcs)</strong></td>
                </tr>
                <?
                $color_size_order_qnty_array=array(); $color_size_qnty_array=array(); $size_tatal=array(); $size_tatal_order=array();
                $item_size_tatal=array(); $item_size_tatal_order=array(); $item_grand_total=0; $item_grand_total_order=0;
				?>
				<tr>
					<td align="left" style="border:1px solid black" width="220">C) Order Qty (Pcs)  @</td>
					<td align="left" style="border:1px solid black" width="60">100%</td>
					<?
					$color_total=0; $color_total_order=0;
					foreach($sizeidArr  as $sizeidstr=>$sizeval)
					{
						?>
						<td style="border:1px solid black; text-align:center; font-size:18px;">
						<?
						// if($sizeval['plan']!= "")
						// {
							echo number_format($sizeval['po'],0);
							$color_total += $sizeval['plan'];
							$color_total_order += $sizeval['po'];
							$item_grand_total+=$sizeval['plan'];
							$item_grand_total_order+=$sizeval['po'];
							$grand_total +=$sizeval['plan'];
							$grand_total_order +=$sizeval['po'];
							
							$color_size_qnty_array[$sizeidstr][$result_color[csf('color_number_id')]]=$sizeval['plan'];
							$color_size_order_qnty_array[$sizeidstr][$result_color[csf('color_number_id')]]=$sizeval['po'];
							if (array_key_exists($sizeidstr, $size_tatal))
							{
								$size_tatal[$sizeidstr]+=$sizeval['plan'];
								$size_tatal_order[$sizeidstr]+=$sizeval['po'];
							}
							else
							{
								$size_tatal[$sizeidstr]=$sizeval['plan'];
								$size_tatal_order[$sizeidstr]=$sizeval['po'];
							}
							if (array_key_exists($sizeidstr, $item_size_tatal))
							{
								$item_size_tatal[$sizeidstr]+=$sizeval['plan'];
								$item_size_tatal_order[$sizeidstr]+=$sizeval['po'];
							}
							else
							{
								$item_size_tatal[$sizeidstr]=$sizeval['plan'];
								$item_size_tatal_order[$sizeidstr]=$sizeval['po'];
							}
					//	}
						//else echo "0";
						?>
						</td>
						<?
					}
					?>
					<td style="border:1px solid black; text-align:center; font-size:18px;"><?=number_format(round($color_total_order),0); ?></td>
				</tr>
                <tr>
                    <td align="left" style="border:1px solid black" width="220">D) Cutting Qty (A+B+C) @ </td>
                    <td align="left" style="border:1px solid black" width="60"><?=100+$tot_pro_loss;?>%</td>
                    <?
                    $color_total=0; $color_total_order=0;
                   foreach($sizeidArr  as $sizeidstr=>$sizeval)
                    {
						?>
						<td style="border:1px solid black; text-align:center; font-size:18px;">
						<?
						// if($sizeval['plan']!= "")
						// {
							echo number_format($sizeval['po']*((100+$tot_pro_loss)/100),0);
							$color_total += $sizeval['plan']*((100+$tot_pro_loss)/100) ;
							$color_total_order += $sizeval['po']*((100+$tot_pro_loss)/100) ;
							$item_grand_total+=$sizeval['plan']*((100+$tot_pro_loss)/100);
							$item_grand_total_order+=$sizeval['po']*((100+$tot_pro_loss)/100);
						
							
							$color_size_qnty_array[$sizeidstr][$result_color[csf('color_number_id')]]=$sizeval['plan']*((100+$tot_pro_loss)/100);
							$color_size_order_qnty_array[$sizeidstr][$result_color[csf('color_number_id')]]=$sizeval['po']*((100+$tot_pro_loss)/100);
							if (array_key_exists($sizeidstr, $size_tatal))
							{
								$size_tatal[$sizeidstr]+=$sizeval['plan']*((100+$tot_pro_loss)/100);
								$size_tatal_order[$sizeidstr]+=$sizeval['po']*((100+$tot_pro_loss)/100);
							}
							else
							{
								$size_tatal[$sizeidstr]=$sizeval['plan']*((100+$tot_pro_loss)/100);
								$size_tatal_order[$sizeidstr]=$sizeval['po']*((100+$tot_pro_loss)/100);
							}
							if (array_key_exists($sizeidstr, $item_size_tatal))
							{
								$item_size_tatal[$sizeidstr]+=$sizeval['plan']*((100+$tot_pro_loss)/100);
								$item_size_tatal_order[$sizeidstr]+=$sizeval['po']*((100+$tot_pro_loss)/100);
							}
							else
							{
								$item_size_tatal[$sizeidstr]=$sizeval['plan']*((100+$tot_pro_loss)/100);
								$item_size_tatal_order[$sizeidstr]=$sizeval['po']*((100+$tot_pro_loss)/100);
							}
					//	}
					//	else echo "0";
						?>
						</td>
						<?
                    }
                    ?>
                    <td style="border:1px solid black; text-align:center; font-size:18px;"><?=number_format(round($color_total_order),0); ?></td>
                </tr>
                <tr>
                    <td align="left" style="border:1px solid black" width="220">Target Input Qty. (D-A) @ &nbsp;&nbsp; </td>
                    <td align="left" style="border:1px solid black" width="60"><?=(100+$tot_pro_loss)-$a_process_loss;?>%</td>
                    <?
                    $target_input_loss=(100+$tot_pro_loss)-$a_process_loss;
                    $color_total=0; $color_total_order=0;
                    foreach($sizeidArr  as $sizeidstr=>$sizeval)
                    {
						?>
						<td style="border:1px solid black; text-align:center; font-size:18px;">
						<?
						// if($sizeval['plan']!= "")
						// {
							echo number_format($sizeval['po']*($target_input_loss/100),0);
							$color_total += $sizeval['plan']*($target_input_loss/100) ;
							$color_total_order += $sizeval['po']*($target_input_loss/100) ;
							$item_grand_total+=$sizeval['plan']*($target_input_loss/100);
							$item_grand_total_order+=$sizeval['po']*($target_input_loss/100);
						
							
							$color_size_qnty_array[$sizeidstr][$result_color[csf('color_number_id')]]=$sizeval['plan']*($target_input_loss/100);
							$color_size_order_qnty_array[$sizeidstr][$result_color[csf('color_number_id')]]=$sizeval['po']*($target_input_loss/100);
							if (array_key_exists($sizeidstr, $size_tatal))
							{
								$size_tatal[$sizeidstr]+=$sizeval['plan']*($target_input_loss/100);
								$size_tatal_order[$sizeidstr]+=$sizeval['po']*($target_input_loss/100);
							}
							else
							{
								$size_tatal[$sizeidstr]=$sizeval['plan']*($target_input_loss/100);
								$size_tatal_order[$sizeidstr]=$sizeval['po']*($target_input_loss/100);
							}
							if (array_key_exists($sizeidstr, $item_size_tatal))
							{
								$item_size_tatal[$sizeidstr]+=$sizeval['plan']*($target_input_loss/100);
								$item_size_tatal_order[$sizeidstr]+=$sizeval['po']*($target_input_loss/100);
							}
							else
							{
								$item_size_tatal[$sizeidstr]=$sizeval['plan']*($target_input_loss/100);
								$item_size_tatal_order[$sizeidstr]=$sizeval['po']*($target_input_loss/100);
							}
						// }
						// else echo "0";
						?>
						</td>
						<?
                    }
                    ?>
                    <td style="border:1px solid black; text-align:center; font-size:18px;"><?=number_format(round($color_total_order),0); ?></td>
                </tr>
            </table>
	
	        <table width="1100" cellspacing="0" border="0"   style="font-family: Arial Narrow;" >
	         <tr>
	        	<td width="250" align="left" valign="top" colspan="2">
	            <table align="left" cellspacing="0" border="0" width="90%" >

	        	</table>
				</td>
				</tr>



	         <tr> <td colspan="6">&nbsp;</td></tr>
	        	<tr>
	        	<td width="250" align="left" valign="top" colspan="2">
	        	<?
				 $sql_sample_dtls= "SELECT a.sample_name, a.article_no, a.sample_color from sample_development_dtls a , lib_color b  where a.status_active=1 and a.is_deleted=0 and a.entry_form_id=203  and sample_mst_id='$data[1]' and b.status_active=1 and a.status_active=1 and b.id=a.sample_color  group by a.sample_name,a.article_no,a.sample_color";

				foreach(sql_select($sql_sample_dtls) as $key=>$value)
				{
					if($sample_wise_article_no[$value[csf("sample_name")]][$value[csf("sample_color")]]=="")
					{
						$sample_wise_article_no[$value[csf("sample_name")]][$value[csf("sample_color")]]=$value[csf("article_no")];
					}
					else
					{
						if(!in_array($value[csf("article_no")], $sample_wise_article_no))
						{
							$sample_wise_article_no[$value[csf("sample_name")]][$value[csf("sample_color")]].= ', '.$value[csf("article_no")];
						}

					}
				}
				 $color_sql="SELECT b.color_id ,b.process_loss_percent ,b.fabric_color,b.contrast,b.mst_id,b.dtls_id from  sample_development_rf_color b where b.status_active=1 and b.is_deleted=0 and b.qnty>0 and b.mst_id='$data[1]' ";
				 $color_res=sql_select($color_sql);
				 $color_rf_data=array();
				 foreach ($color_res as $val) {
				 	$color_rf_data[$val[csf('dtls_id')]][$val[csf('color_id')]][$val[csf('fabric_color')]]['process_loss_percent']=$val[csf('process_loss_percent')];
				 	$color_rf_data[$val[csf('dtls_id')]][$val[csf('color_id')]][$val[csf('fabric_color')]]['contrast']=$val[csf('contrast')];
				 }

			  $sql_fab="SELECT a.sample_name, a.gmts_item_id, c.gmts_color as color_id,   a.delivery_date, a.fabric_description, a.body_part_id, a.fabric_source, a.remarks_ra, a.gsm, a.dia, a.color_type_id, a.width_dia_id, a.uom_id, c.grey_fabric as grey_fab_qnty, c.fabric_color,c.dtls_id,c.finish_fabric as qnty,a.id,a.determination_id from sample_development_fabric_acc a, wo_non_ord_samp_booking_dtls c where  a.id=c.dtls_id  and a.form_type=1  and c.grey_fabric>0 and a.status_active=1 and a.is_deleted=0  and c.status_active=1 and c.is_deleted=0  and a.sample_mst_id='$data[1]' ";
			
				 $sql_fab_arr=array();
				 $determination_id_arr=array();
				 foreach(sql_select($sql_fab) as $vals)
				 {
				 	$contrast=$color_rf_data[$vals[csf('dtls_id')]][$vals[csf('color_id')]][$vals[csf('fabric_color')]]['contrast'];
			 		$process_loss_percent=$color_rf_data[$vals[csf('dtls_id')]][$vals[csf('color_id')]][$vals[csf('fabric_color')]]['process_loss_percent'];

					$article_no=rtrim($sample_wise_article_no[$vals[csf("sample_name")]][$vals[csf("color_id")]],',');
					$article_no=implode(",",array_unique(explode(",",$article_no)));
					$sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$contrast]["qnty"]+=$vals[csf("qnty")];
				 	$sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$contrast]["process_loss_percent"]=$process_loss_percent;

				 	$sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$contrast]["grey_fab_qnty"]+=$vals[csf("grey_fab_qnty")];

				 	$sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$contrast]["delivery_date"] =change_date_format($vals[csf("delivery_date")]);

				 	$sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$contrast]["fabric_source"] =$vals[csf("fabric_source")];

				 	$sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$contrast]["uom_id"] =$vals[csf("uom_id")];
					$sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$contrast]["dia"] =$vals[csf("dia")];

				 	$sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$contrast]["width_dia_id"] =$vals[csf("width_dia_id")];

				 	$sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$contrast]["remarks"] =$vals[csf("remarks_ra")];
				 	$sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$contrast]["color_type_id"] =$vals[csf("color_type_id")];
					 $sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$contrast]["determination_id"] =$vals[csf("determination_id")];
					 array_push($determination_id_arr,$vals[csf('determination_id')]);
				 }
				 $determination_id_cond= where_con_using_array($determination_id_arr,0,"a.id");
				 $sql_d = "SELECT b.fabric_composition_name, a.id, a.construction FROM lib_yarn_count_determina_mst a left join lib_fabric_composition b on a.fabric_composition_id = b.id AND b.status_active = 1 AND b.is_deleted = 0 WHERE a.status_active = 1 AND a.is_deleted = 0  $determination_id_cond";
				// echo $sql_d; //die;
				$determina_arr = sql_select($sql_d);
				$determina_data_arr=array();
				foreach ($determina_arr as $row)
				{
					
					$determina_data_arr[$row[csf('id')]].=$row[csf('fabric_composition_name')]."***";
					$construction_data_arr[$row[csf('id')]].=$row[csf('construction')]."***";
					
				}


				 $sample_item_wise_span=array(); $sample_item_wise_color_span=array();

			  foreach($sql_fab_arr as $article_no=>$article_data) 
	          {
				$article_no_span=0;
				foreach($article_data as $sample_type_id=>$sampleType_data) 
	        	{
				$sample_type_span=0;
				foreach($sampleType_data as $gmts_color_id=>$gmts_color_data)
	        	{
					$sample_span=0;
	        		foreach($gmts_color_data as $body_part_id=>$body_part_data)
	        		{
	        			
						//echo $gmts_color_id.'d';

	        			foreach($body_part_data as $fab_id=>$fab_desc_data)
	        			{
	        				//$kk=0;
	        				foreach($fab_desc_data as $colorType=>$colorType_data)
	        				{

	        					foreach($colorType_data as $gsm_id=>$gsm_data)
	        					{
	        						foreach($gsm_data as $dia_id=>$dia_data)
	        						{
									   foreach($dia_data as $dia_type_id=>$diatype_data)
	        						   {

	        							foreach($diatype_data as $contrast_id=>$value)
	        							{
	        								$sample_span++;$sample_type_span++;$article_no_span++;
	        								//$kk++;

	        							}
											$article_wise_span[$article_no]=$article_no_span;
											$sample_item_wise_span[$article_no][$sample_type_id]=$sample_type_span;
											$sample_item_wise_color_span[$article_no][$sample_type_id][$gmts_color_id]=$sample_span;
									  }
	        						}

	        					}


	        				}

	        			}

	        		  }
					 }

	        		}
				}
	        	//echo "<pre>";
	        	//print_r($sample_item_wise_color_span);die;
				// echo "<pre>"; print_r($sample_wise_article_no);die;

				?>
				<table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all">
					<thead>
					<tr>
						<th colspan="19">Required Fabric</th>
					</tr>
						<tr>
							<th width="30">Sl</th>
							<th width="90">ALT / [C/W]</th>
							<th width="110">Sample Type</th>
							<th width="80">Gmt Color</th>
							<th width="80">Fab. Deli Date</th>
							<th width="120">Body Part</th>
							<th width="150">Fabric Desc</th>
							<th width="150">yarn Composition</th>
							<th width="80">Color Type</th>
							<th width="80">Fab.Color</th>
							<th width="40">Item Size</th>
							<th width="55">GSM</th>
							<th width="55">Dia</th>
							<th width="60">Width/Dia</th>
							<th width="40">UOM</th>
							<th width="60">Grey Qnty</th>
							<th width="40">P. Loss</th>
							<th width="80">Fin Fab Qnty</th>
							<th width="80">Fabric Source</th>
							<th width="80">Remarks</th>

						</tr>
					</thead>
					<tbody>
						<?
						 function str_replace_first($search, $replace, $subject)
						 {
							 $search = '/'.preg_quote($search, '/').'/';
							 return preg_replace($search, $replace, $subject, 1);
						 }
						$p=1;
						$total_finish=0;
						$total_grey=0;
						$total_process=0;
			  foreach($sql_fab_arr as $article_no=>$article_data) 
	          {
				$aa=0;
				foreach($article_data as $sample_type_id=>$sampleType_data) 
	        	{
				$nn=0;
				foreach($sampleType_data as $gmts_color_id=>$gmts_color_data)
	        	{
					$cc=0;
	        		foreach($gmts_color_data as $body_part_id=>$body_part_data)
	        		{
	        			
						//echo $gmts_color_id.'d';

	        			foreach($body_part_data as $fab_id=>$fab_desc_data)
	        			{
	        				//$kk=0;
	        				foreach($fab_desc_data as $colorType=>$colorType_data)
	        				{

	        					foreach($colorType_data as $gsm_id=>$gsm_data)
	        					{
	        						foreach($gsm_data as $dia_id=>$dia_data)
	        						{

	        							foreach($dia_data as $dia_type=>$diatype_data)
	        							{
											foreach($diatype_data as $contrast_id=>$value)
	        							    {
												$constr=implode(",", array_unique(explode("***", chop($construction_data_arr[$value['determination_id']],"***"))));
															 
														?>
														<tr>


																
																<?
															if($aa==0)
															{
																?>
	                                                            <td  rowspan="<? echo $article_wise_span[$article_no];?>"  align="left" style="word-wrap: break-word;word-break: break-all;"><? echo $p;$p++;?></td>
	                                                            <td   rowspan="<? echo $article_wise_span[$article_no];?>" align="center"><? echo $article_no;?></td>
	                                                            <?
															}
															if($nn==0)
															{
																?>
																
																<td   rowspan="<? echo $sample_item_wise_span[$article_no][$sample_type_id];?>"  align="center"><? echo $sample_library[$sample_type_id]; ?></td>
																
																<?
																
															}
															if($cc==0)
															{
															 ?>
	                                                         <td   align="center" rowspan="<? echo $sample_item_wise_color_span[$article_no][$sample_type_id][$gmts_color_id];?>"><? echo $color_library[$gmts_color_id];?> </td>
	                                                          <td   rowspan="<? echo $sample_item_wise_color_span[$article_no][$sample_type_id][$gmts_color_id];?>" align="center" ><? echo $value["delivery_date"];?> </td>
	                                                         <?
	                                                        } ?>

															
															 <td  align="center"><? echo $body_part[$body_part_id];?></td>
															 <td  align="center"><? echo $constr;?></td>
															 <td  align="center" ><? echo str_replace_first(trim($constr), " ", $fab_id);?></td>
															 <td  align="center"><? echo $color_type[$colorType]; ?></td>
															 <td  align="center"><? echo $contrast_id; ?></td>
															 <td  align="center"><? echo $value["item_size"]; ?></td>
															 <td  align="center"><? echo $gsm_id; ?></td>
															 <td  align="center"><? echo $value["dia"]; ?></td>
															 <td  align="center"><? echo $fabric_typee[$dia_type]; ?></td>
															 <td   align="center"><? echo $unit_of_measurement[$value["uom_id"]];?></td>

															 <td align="right"><? echo number_format($value["grey_fab_qnty"],2);?></td>
															 <td align="right"><? echo $value["process_loss_percent"];?></td>
															 <td align="right"><? echo number_format($value["qnty"],2);?></td>

															 <td align="center"><? echo $fabric_source[$value["fabric_source"]];?></td>
															 <td  align="center"><? echo $value["remarks"];?></td>

														</tr>


														<?
														$nn++;$cc++;$aa++;
			        									//$i++;
														$total_finish +=$value["qnty"];
														$total_grey +=$value["grey_fab_qnty"];
														$total_process +=$value["process_loss_percent"];
													}
												}
											}
										}
									}
								}
							  }
							}
						}
			 		}

						?>

						<tr>
							<th colspan="15" align="right"><b>Total</b></th>
							<th width="80" align="right"><? echo number_format($total_grey,2);?></th>
							<th width="40" align="right">&nbsp;</th>
							<th width="60" align="right"><? echo number_format($total_finish,2);?></th>
							<th width="80" colspan="2"> </th>

						</tr>

					</tbody>



				</table><br/><?

				$sample_color_arr=return_library_array( "select id, sample_color from sample_development_dtls", "id", "sample_color"  );
	                      $sql_qry="SELECT id,sample_mst_id,sample_name,gmts_item_id,smv,article_no,sample_color,sample_prod_qty,submission_qty,delv_start_date,delv_end_date,sample_charge,sample_curency,sent_to_buyer_date,comments from sample_development_dtls where status_active =1 and is_deleted=0 and entry_form_id=203 and sample_mst_id='$data[1]' order by id asc";
						    $sql_qry_color="SELECT a.id,a.sample_mst_id,a.sample_name,a.gmts_item_id,a.smv,a.article_no,a.sample_color,a.sample_prod_qty,a.submission_qty,a.delv_start_date,a.delv_end_date,a.sample_charge,a.sample_curency,a.sent_to_buyer_date,a.comments,c.dtls_id,c.size_id,c.bh_qty,c.self_qty,c.test_qty,c.plan_qty,c.dyeing_qty,c.others_qty,c.test_fit_qty,c.samp_dept_qty from sample_development_dtls a,sample_development_size c where a.id=c.dtls_id and  a.status_active =1 and a.is_deleted=0 and a.entry_form_id=203 and a.sample_mst_id='$data[1]' order by a.id asc";
						 $size_type_arr=array(1=>"BH Qty",2=>"Self Qty",3=>"Test qty",4=>"Plan Qty",5=>"Dyeing Qty",6=>"Test Fit",7=>"Samp. Dept",8=>"Others");
						 $color_size_arr=array();
						  foreach(sql_select($sql_qry_color) as $vals)
						 {
							$color_size_qnty_arr[1][$vals[csf("size_id")]]='Bh Qty';
								if($vals[csf("bh_qty")]>0)
								{
								$color_size_arr[1][$vals[csf("size_id")]]='Bh Qty';
								$bh_qty=$vals[csf("bh_qty")];
								$color_size_dtls_qty_arr[1][$vals[csf("id")]][$vals[csf("size_id")]]=$bh_qty;
								}
								if($vals[csf("self_qty")]>0)
								{
								$color_size_arr[2][$vals[csf("size_id")]]='self qty';
								$color_size_dtls_qty_arr[2][$vals[csf("id")]][$vals[csf("size_id")]]=$vals[csf("self_qty")];
								}
								if($vals[csf("test_qty")]>0)
								{
								$color_size_arr[3][$vals[csf("size_id")]]='test qty';
								$color_size_dtls_qty_arr[3][$vals[csf("id")]][$vals[csf("size_id")]]=$vals[csf("test_qty")];
								}
								if($vals[csf("plan_qty")]>0)
								{
								$color_size_arr[4][$vals[csf("size_id")]]='plan qty';
								//$size_plan_arr[$vals[csf("size_id")]]=$vals[csf("size_id")];
								$color_size_dtls_qty_arr[4][$vals[csf("id")]][$vals[csf("size_id")]]=$vals[csf("plan_qty")];

								}
								if($vals[csf("dyeing_qty")]>0)
								{
								$color_size_arr[5][$vals[csf("size_id")]]='Dyeing qty';
								$color_size_dtls_qty_arr[5][$vals[csf("id")]][$vals[csf("size_id")]]=$vals[csf("dyeing_qty")];

								}
								if($vals[csf("test_fit_qty")]>0)
								{
								$color_size_arr[6][$vals[csf("size_id")]]='Test Fit';
								$color_size_dtls_qty_arr[6][$vals[csf("id")]][$vals[csf("size_id")]]=$vals[csf("test_fit_qty")];

								}
								if($vals[csf("samp_dept_qty")]>0)
								{
								$color_size_arr[7][$vals[csf("size_id")]]='Samp. Dept';
								$color_size_dtls_qty_arr[7][$vals[csf("id")]][$vals[csf("size_id")]]=$vals[csf("samp_dept_qty")];

								}
								if($vals[csf("others_qty")]>0)
								{
								$color_size_arr[8][$vals[csf("size_id")]]='Others';
								$color_size_dtls_qty_arr[8][$vals[csf("id")]][$vals[csf("size_id")]]=$vals[csf("others_qty")];

								}

							}
							$tot_row=count($color_size_arr);
							$result=sql_select($sql_qry);

				?>


	            	<table align="left" cellspacing="0" border="1" width="100%" class="rpt_table" rules="all">
	            	<thead>
	            			<tr>
	                            <td width="150" colspan="<? echo 12+$tot_row;?>" align="center"><strong>Sample Details</td>
	                        </tr>
	                        <tr>
									<th width="30" rowspan="2" align="left">Sl</th>
									<th width="100" rowspan="2" align="center">Sample Name</th>
									<th width="120" rowspan="2" align="center">Garment Item</th>

									<th width="55" rowspan="2" align="center">ALT / [C/W]</th>
									<th width="70" rowspan="2" align="center">Color</th>
	                                <?
									$tot_row_td=0;
	                                foreach($color_size_arr as $type_id=>$val)
									{ ?>
										<th width="45" align="center" colspan="<? echo count($val);?>"> <?
	                                 		  echo  $size_type_arr[$type_id];
										?></th>
	                                    <?

									}
									?>
									<th rowspan="2" width="55" align="center">Total</th>
									<th rowspan="2" width="55" align="center">Submn Qty</th>
									<th rowspan="2"  width="70" align="center">Buyer Submisstion Date</th>
									<th rowspan="2"  width="70" align="center">Remarks</th>
	                         </tr>
	                         <tr>
	                         	<?
	                            foreach($color_size_arr as $type_id=>$data_size)
								{
									foreach($data_size as $size_id=>$data_val)
									{
									$tot_row_td++;
									?>
										<th width="40" align="center"><? echo $size_library[$size_id]; ?></th>
										<?
									}
	                         	}

	                         	?>
	                         </tr>

	            	</thead>
	                    <tbody>

	                        <?

	 						$i=1;$k=0;
	 						$gr_tot_sum=0;
	 						$gr_sub_sum=0;
							foreach($result as $row)
							{
								$dtls_ids=$row[csf('id')];
								 //$size_select=sql_select("SELECT  size_id,total_qty  from sample_development_size where  mst_id='$data[1]' and status_active=1 and is_deleted=0 and dtls_id='$dtls_ids' ");
	 							$prod_sum=$prod_sum+$row[csf('sample_prod_qty')];
								$sub_sum=$sub_sum+$row[csf('submission_qty')];

							?>
	                        <tr>
	                            <?
	 							$k++;
								?>
	                            <td  align="left"><? echo $k;?></td>
	                            <td  align="left"><? echo $sample_library[$row[csf('sample_name')]];?></td>
	                            <td  align="left"><? echo $garments_item[$row[csf('gmts_item_id')]];?></td>

	                            <td   align="left"><? echo $row[csf('article_no')];?></td>
	                            <td width="70" align="left"><? echo $color_library[$row[csf('sample_color')]];?></td>


	                            <?
	                            $total_sizes_qty=0;
	                            $total_sizes_qty_subm=0;
	                          	foreach($color_size_arr as $type_id=>$data_size)
								{
									foreach($data_size as $size_id=>$data_val)
									{
									$size_qty=$color_size_dtls_qty_arr[$type_id][$dtls_ids][$size_id];
	                            	?>
	                            	<td align="right"><? echo $size_qty; ?></td>
	                            	<?
										if($type_id==1)
										{
										$total_sizes_qty_subm+=$size_qty;
										}
										$total_sizes_qty+=$size_qty;
									}
	                            }
	                            ?>
	                            <td align="right"><? echo $total_sizes_qty;?></td>
	                            <td align="right"><? echo $row[csf('submission_qty')];?></td>
	                            <td   align="left"><? echo change_date_format($row[csf('sent_to_buyer_date')]);?> </td>
	                            <td   align="left"><? echo $row[csf('comments')];?> </td>
	                            <?
	                            $gr_tot_sum+=$total_sizes_qty;
	 							$gr_sub_sum+=$row[csf('submission_qty')];
	                        }
							?>
	                        </tr>
								<tr>
										<td colspan="<? echo 5+$tot_row_td; ?>" align="right"><b>Total</b></td>
	 									<td   align="right"><b><? echo number_format($gr_tot_sum,2);?> </b></td>
	 									<td  align="right"><b><? echo number_format($gr_sub_sum,2);?> </b></td>
										<td colspan="2"></td>
								</tr>
	                    </tbody>
	                    <tfoot>
	                     </tfoot>
	               </table>
	             </td>
       		 </tr>
      	  </table>
		<br>

		<div width="100%">
        <table  style="font-size:14px" class="rpt_table" width="40%" align="left" border="1" cellpadding="0" cellspacing="0" rules="all">
            	<thead>
            		<tr>
                      <td colspan="3" align="center">Yarn Required Summary</td>
                    </tr>
                	<tr align="center">
                    	<th align="left" width="30">Sl</th>
                    	<th align="center">Yarn Desc.</th>
                         <th align="center">Req. Qty</th> 
                    </tr>
                </thead>
                <tbody>
                <?
				$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count"  );
				$lib_supllier_arr=return_library_array( "select supplier_id,booking_no from wo_non_ord_samp_booking_mst where booking_no='$data[2]' and entry_form_id=140", "booking_no", "supplier_id"  );
				$tot_req_qty=0;

				
				$sql_yarn="select b.count_id,b.copm_one_id,b.percent_one,b.type_id,round(sum(b.cons_qnty),2) as  cons_qnty from  sample_development_yarn_dtls b where  b.status_active=1  and b.mst_id='$data[1]' and b.determin_id in (select determination_id from sample_development_fabric_acc  where status_active=1 and sample_mst_id='$data[1]' and form_type=1) group by b.count_id,b.copm_one_id,b.percent_one,b.type_id";
				//echo $sql_yarn;
				$data_array=sql_select($sql_yarn);
				foreach( $data_array as $key=>$row )
				{
					$yarn_des=$lib_yarn_count[$row[csf("count_id")]].','.$composition[$row[csf("copm_one_id")]].','.$yarn_type[$row[csf("type_id")]];
					$yarn_data_arr[$yarn_des]['cons_qnty']+=$row[csf("cons_qnty")];
				}
				
				if (count($yarn_data_arr)>0)
				{
					$l=1;
					foreach( $yarn_data_arr as $key=>$row )
					{
						
						?>
                        	<tr>
                                <td> <? echo $l;?> </td>
                                <td> <? echo $key; ?> </td>
                                <td align="right"><? echo number_format($row['cons_qnty'],2);?> </td>
                            </tr>
                        <?
                        $l++;
						$tot_req_qty+=$row['cons_qnty'];
					}
				}

				?>
                <tr>
					<th  colspan="2" align="right"><b>Total</b></th>
					<th  align="right"><? echo number_format($tot_req_qty,2);?></th>
				</tr>
            </tbody>
        </table>
		<table  style="font-size:14px ;float:left; margin-left:2%" class="rpt_table" width="50%" align="left" border="1" cellpadding="0" cellspacing="0" rules="all">
             
                	<?
				
					$lib_item_group_arr=return_library_array( "select item_name, id from lib_item_group where item_category=4 and is_deleted=0  and  status_active=1 order by item_name", "id", "item_name");
					$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count"  );

					$lib_supllier_arr=return_library_array( "select supplier_id,booking_no from wo_non_ord_samp_booking_mst where booking_no='$data[2]' and entry_form_id=140", "booking_no", "supplier_id"  );
					$tot_req_qty=0;

					$dtm_arr_item_color=array();
					$sql=sql_select("select sample_req_fabric_cost_id,fabric_color,sample_req_trim_cost_id,item_color,sum(qty) as qty from sample_dev_dye_to_match where booking_no='$data[2]'  and status_active=1 and is_deleted=0 group by sample_req_fabric_cost_id,fabric_color,item_color,sample_req_trim_cost_id");
					
					foreach($sql as $row){
						$dtm_arr[$row[csf('fabric_color')]][$row[csf('sample_req_trim_cost_id')]]+=$row[csf('qty')];
						$dtm_arr_item_color[$row[csf('fabric_color')]][$row[csf('sample_req_trim_cost_id')]]=$row[csf('item_color')];
					}




					
					$dye_to_match_sql="select a.id,a.sample_mst_id, a.trims_group_ra as trim_group, a.fabric_description as description,a.uom_id_ra as cons_uom,sum(a.req_qty_ra) as req_qty_ra,  c.fabric_color as fabric_color_id,a.description_ra    FROM sample_development_fabric_acc a,  wo_non_ord_samp_booking_dtls c
					WHERE c.style_id=a.sample_mst_id  and a.form_type=2 and c.booking_no ='$data[2]' and c.status_active=1 and  c.status_active=1  and a.status_active=1 and c.is_deleted=0
					group by a.id,a.sample_mst_id, a.trims_group_ra,a.fabric_description,a.uom_id_ra,  c.fabric_color ,a.description_ra    order by a.id  ";
					//echo $sql_yarn;
					$data_array=sql_select($dye_to_match_sql);

					
					if ( count($data_array)>0)
					{
						?>

						<thead>
					<tr>
                      <td colspan="7" align="center"><b> Dyes To Match</b></td>
                    </tr>
                    	<tr align="center">
                        	<th align="center" width="40">Sl</th>
                        	<th align="center">Item</th>
							<th align="center">Item Desc.</th>
							<th align="center">Body Color</th>
                        	<th align="center">Item Color</th>
                            <th align="center">Finish  Qty</th> 
							<th align="center">UOM</th> 
                        </tr>
                    </thead>
                    <tbody>
                    <?
						$l=1;
						foreach( $data_array as $key=>$row )
						{
							$yarn_des=$lib_yarn_count[$row[csf("count_id")]].','.$composition[$row[csf("copm_one_id")]].','.$row[csf("percent_one")].'%,'.$yarn_type[$row[csf("type_id")]];
							?>
                            	<tr>
                                    <td> <? echo $l;?> </td>
                                    <td> <? echo $lib_item_group_arr[$row[csf("trim_group")]]; ?> </td>
                                    <td align="left"> <? echo $row[csf("description_ra")]; ?> </td>							
                              								
									<td> <? echo  $color_library[$row[csf('fabric_color_id')]]; ?> </td>
                                    <td > <? echo $color_library[$dtm_arr_item_color[$row[csf('fabric_color_id')]][$row[csf('id')]]]; ?> </td>									
									<td align="right"> <? echo $dtm_arr[$row[csf('fabric_color_id')]][$row[csf('id')]] ;//echo $row[csf("fin_fab_qnty")]; //echo $dtm_arr[$fabric_cost_id][$color][$row[csf('id')]]?> </td>
                                    <td align="right"> <? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?> </td>
                                </tr>
                            <?
                            $l++;
							$tot_req_qty+=$dtm_arr[$row[csf('fabric_color_id')]][$row[csf('id')]] ;
							
						}
						?>
					
                    <tr>
						<th  colspan="5" align="right"><b>Total</b></th>
						<th  align="right"><? echo number_format($tot_req_qty,2);?></th>
					</tr>
                </tbody>
				<?
						}
				?>
            </table>
			</div>
        <br>
        <br>
        <?
        	$sample_stripe_data=sql_select("SELECT a.body_part_id, b.contrast, b.color_id, b.grey_fab_qnty,c.id as strip_mst_id, c.stripe_color, c.measurement, c.uom, c.fabreq, c.yarn_dyed FROM sample_development_fabric_acc a join sample_development_rf_color b on a.id=b.dtls_id join wo_sample_stripe_color c on a.id=c.sample_fab_dtls_id and b.color_id=c.color_number_id where a.status_active=1 and a.is_deleted=0 and a.color_type_id in (2,3,4,6,31,32,33,34) and a.form_type=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.sample_mst_id=$data[1]");
        	$stripe_color_summ=array();
        	foreach ($sample_stripe_data as $row) {
        		$key=$row[csf('body_part_id')].'*'.$row[csf('color_id')];
        		$sample_stripe_arr[$key]['body_part_id'] = $row[csf('body_part_id')];
        		$sample_stripe_arr[$key]['fabric_color'] = $row[csf('color_id')];
        		$sample_stripe_arr[$key]['fabric_qty'] = $row[csf('grey_fab_qnty')];
				
        		$sample_stripe_arr[$key]['stripe_color'][$row[csf('strip_mst_id')]][$row[csf('stripe_color')]]['color'] = $row[csf('stripe_color')];
        		$sample_stripe_arr[$key]['stripe_color'][$row[csf('strip_mst_id')]][$row[csf('stripe_color')]]['measurement'] = $row[csf('measurement')];
        		$sample_stripe_arr[$key]['stripe_color'][$row[csf('strip_mst_id')]][$row[csf('stripe_color')]]['uom'] = $row[csf('uom')];
        		$sample_stripe_arr[$key]['stripe_color'][$row[csf('strip_mst_id')]][$row[csf('stripe_color')]]['qty'] = $row[csf('fabreq')];
        		$sample_stripe_arr[$key]['stripe_color'][$row[csf('strip_mst_id')]][$row[csf('stripe_color')]]['yarn_dyed'] = $row[csf('yarn_dyed')];
        		
				$stripe_color_summ[$row[csf('stripe_color')]] += $row[csf('fabreq')];
        	}
        ?>
        <div style="width:1000px;">
	        <table align="left" cellspacing="0" border="1" style="width:800px;float: left; margin-top: 5px;font-size:14px" class="rpt_table" rules="all">
	        	
	        		<? 
	        		if(count($sample_stripe_arr)>0){
						?>
						<thead>
	        		<tr>
	        			<th colspan="9">Stripe Details</th>
	        		</tr>
	        		<tr>
	        			<th width="30">SL</th>
	        			<th width="100">Body Part</th>
	        			<th width="60">Fabric Color</th>
	        			<th width="60">Fabric Qty(KG)</th>
	        			<th width="60">Stripe Color</th>
	        			<th width="60">Stripe Measurement</th>
	        			<th width="60">Stripe Uom</th>
	        			<th width="60">Qty.(KG)</th>
	        			<th width="60">Y/D Req.</th>
	        		</tr>
	        	</thead>
	        	<tbody>
					<?
		        		$sl=1;
		        		foreach ($sample_stripe_arr as $sdata) {
		        			$rowspan = count($sdata['stripe_color']);
		        			$i=1;
		        			foreach ($sdata['stripe_color'] as $stripe_mst) {
								foreach ($stripe_mst as $stripe_data) {
		        				if($i==1){
		        					$total_fabric += $sdata['fabric_qty'];
		        					$total_stripe_fabric += $stripe_data['qty'];
		        				?>
		        				<tr>
				        			<td rowspan="<?=$rowspan?>" align="left"><?= $sl; ?></td>
				        			<td rowspan="<?=$rowspan?>"><?= $body_part[$sdata['body_part_id']]; ?></td>
				        			<td rowspan="<?=$rowspan?>"><?= $color_library[$sdata['fabric_color']]; ?></td>
				        			<td align="right" rowspan="<?=$rowspan?>"><?= $sdata['fabric_qty']; ?></td>
				        			<td><?= $color_library[$stripe_data['color']]; ?></td>
				        			<td align="right"><?= $stripe_data['measurement']; ?></td>
				        			<td><?= $unit_of_measurement[$stripe_data['uom']]; ?></td>
				        			<td align="right"><?= $stripe_data['qty']; ?></td>
				        			<td><?= $yes_no[$stripe_data['yarn_dyed']]; ?></td>
				        		</tr>
		        				<?
		        					$i++;
		        				}
		        				else{
		        					$total_stripe_fabric += $stripe_data['qty'];
		        					?>
		        						<tr>
		        							<td><?= $color_library[$stripe_data['color']]; ?></td>
						        			<td align="right"><?= $stripe_data['measurement']; ?></td>
						        			<td><?= $unit_of_measurement[$stripe_data['uom']]; ?></td>
						        			<td align="right"><?= number_format($stripe_data['qty'],2); ?></td>
						        			<td><?= $yes_no[$stripe_data['yarn_dyed']]; ?></td>
		        						</tr>
		        					<?
		        				}
		        			}
		        			$sl++;
							}
		        		}
	        		
					?>
	        	</tbody>
	        	<tfoot>
	        		<tr>
	        			<th colspan="3">Total</th>
	        			<th align="right"><?= number_format($total_fabric,2) ?></th>
	        			<th></th>
	        			<th></th>
	        			<th></th>
	        			<th align="right"><?= number_format($total_stripe_fabric,2) ?></th>
	        			<th></th>
	        		</tr>
	        	</tfoot>
				<? } ?>
	        </table>
	        <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:180px; margin-left: 2px; float: right;  margin-top: 5px;font-size:14px" rules="all">
		       
		        	<?
		        	if(count($stripe_color_summ)>0){
						?>
						 <thead>
		        	<tr>
		        		<th colspan="3">Stripe Color wise Summary</th>
		        	</tr>
		        	<tr>
		        		<th>SL</th>
		        		<th>Stripe Color</th>
		        		<th>Qty.(KG)</th>
		        	</tr>
		        </thead>
		        <tbody>
					<?
			        	$sl=1;
			        	foreach ($stripe_color_summ as $color_id => $value) {
			        	 	$total_fabric_qty+= $value;
			        	?>
			        	<tr>
			        		<td><?= $sl ?></td>
			        		<td><?= $color_library[$color_id]; ?></td>
			        		<td><?= number_format($value,2) ?></td>
			        	</tr>
			        	<? $sl++;
			        	}
		        	 ?>
		        </tbody>
		        <tfoot>
		        	<tr>
		        		<th colspan="2">Total</th>
		        		<th><?= number_format($total_fabric_qty,2); ?></th>
		        	</tr>
		        </tfoot>
				<? } ?>
	        </table>
        </div>
        <?
			$coller_cuff_data=sql_select("SELECT a.sample_color, a.size_id, a.item_size, a.qnty_pcs, c.body_part_type from sample_requisition_coller_cuff a join sample_development_fabric_acc b on b.id=a.dtls_id join lib_body_part c on b.body_part_id=c.id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.sample_mst_id=$data[1]");
			$coller_data_arr=array(); $cuff_data_arr=array();
			foreach ($coller_cuff_data as $row) {
				if($row[csf('body_part_type')]==40)
				{
					$coller_size_arr[$row[csf('size_id')]]=$row[csf('size_id')];
					$color_size_data[$row[csf('size_id')]]['item_size']=$row[csf('item_size')];
					$color_color_data[$row[csf('sample_color')]][$row[csf('size_id')]]['qnty_pcs']=$row[csf('qnty_pcs')];
				}
				if($row[csf('body_part_type')]==50)
				{
					$cuff_size_arr[$row[csf('size_id')]]=$row[csf('size_id')];
					$cuff_size_data[$row[csf('size_id')]]['item_size']=$row[csf('item_size')];
					$cuff_color_data[$row[csf('sample_color')]][$row[csf('size_id')]]['qnty_pcs']=$row[csf('qnty_pcs')];
				}
			} 
			/*echo '<pre>';
			print_r($color_color_data); die;*/
			if (count($coller_cuff_data) > 0) {
				?>
        
		<br>
     
        	<table align="left" cellspacing="0" border="1" style="width:495px;float: left; margin-top: 5px;font-size:14px" class="rpt_table" rules="all">
        		<thead>
        			<tr>
        				<th colspan="<? echo count($coller_size_arr)+2;  ?>">Collar - Color Size Brakedown in Pcs.</th>
        			</tr>
        			<tr>
        				<th>Size</th>
        				<? foreach ($coller_size_arr as $size_id) { ?>
        				<th><?= $size_library[$size_id]; ?></th>
        				<? } ?>
        				<th rowspan="2">Total</th>
        			</tr>
        		</thead>
        		<tbody>
        			<tr>
        			<th>Collar Size</th>
        				<?
        					foreach ($coller_size_arr as $size_id) {
        				 ?>
        				 <td><?= $color_size_data[$size_id]['item_size'] ?></td>        				
        				<? }         				
        			?></tr>
        			<? foreach ($color_color_data as $fabric_color =>$size_data) { ?>
        				<tr>
        					<td><?= $color_library[$fabric_color]?></td><?
        					$total_size_qty=0;
        					foreach ($coller_size_arr as $size_id) {
        						$total_size_qty+=$size_data[$size_id]['qnty_pcs'];
        						$total_size_arr[$size_id]+=$size_data[$size_id]['qnty_pcs'];
        				 ?>
        				 <td><?= ($size_data[$size_id]['qnty_pcs']) ? $size_data[$size_id]['qnty_pcs'] : 0; ?></td>        				
        				<? } ?>
        					<td><? echo $total_size_qty; ?></td>
        				</tr><?
        			}
        			 ?>        			
        		</tbody>
        		<tfoot>
        			<tr>
        			<th align="right">Total</th>
        			<?
    				foreach ($coller_size_arr as $size_id) {
    					$grand_size_qty_total+= $total_size_arr[$size_id]
    				 ?>
    					<th align="left"><?  echo $total_size_arr[$size_id]  ?></th>
    				<? }
        			?>
        			<th align="left"><?= $grand_size_qty_total ?></th>
        			</tr>
        		</tfoot>
        	</table>
			<br>
			<br>
        	<table align="left" cellspacing="0" border="1" style="width:495px;float: right; margin-top: 5px;font-size:14px" class="rpt_table" rules="all">
        		<thead>
        			<tr>
        				<th colspan="<? echo count($cuff_size_arr)+2;  ?>">Cuff - Color Size Brakedown in Pcs.</th>
        			</tr>
        			<tr>
        				<th>Size</th>
        				<? foreach ($cuff_size_arr as $size_id) { ?>
        				<th><?= $size_library[$size_id]; ?></th>
        				<? } ?>
        				<th rowspan="2">Total</th>
        			</tr>
        		</thead>
        		<tbody>
        			<tr>
        			<th>Cuff Size</th>
        				<?
        					foreach ($cuff_size_arr as $size_id) {
        				 ?>
        				 <td><?= $cuff_size_data[$size_id]['item_size'] ?></td>        				
        				<? }         				
        			?></tr>
        			<? foreach ($cuff_color_data as $fabric_color =>$size_data) { ?>
        				<tr>
        					<td><?= $color_library[$fabric_color]?></td><?
        					$total_size_qty=0;
        					foreach ($cuff_size_arr as $size_id) {
        						$total_size_qty+=$size_data[$size_id]['qnty_pcs'];
        						$total_cuff_size_arr[$size_id]+=$size_data[$size_id]['qnty_pcs'];
        				 ?>
        				 <td><?= ($size_data[$size_id]['qnty_pcs']) ? $size_data[$size_id]['qnty_pcs'] : 0; ?></td>        				
        				<? } ?>
        					<td><? echo $total_size_qty; ?></td>
        				</tr><?
        			}
        			 ?>        			
        		</tbody>
        		<tfoot>
        			<tr>
        			<th align="right">Total</th>
        			<?
    				foreach ($cuff_size_arr as $size_id) {
    					$grand_qty_total+= $total_cuff_size_arr[$size_id]
    				 ?>
    					<th align="left"><?  echo $total_cuff_size_arr[$size_id];  ?></th>
    				<? }
        			?>
        			<th align="left"><?= $grand_qty_total ?></th>
        			</tr>
        		</tfoot>
        	</table>
			<? } ?>
      
			<?
		

		$width="1100";
		
			$data_array = sql_select("select id, terms from  wo_booking_terms_condition where   booking_no='$data[2]' and entry_form=140   order by id asc");
			$tot_row=count($data_array)/2;
			//echo $tot_row;
			$k=1;
			foreach($data_array as $row)
			{
				if($k<=$tot_row)
				{
				$term_bookingArr[$row[csf('id')]]['terms']=$row[csf('terms')];
				}
				else
				{
				$other_term_bookingArr[$row[csf('id')]]['terms']=$row[csf('terms')];	
				}
				$k++;
			}
			
			if (count($data_array) > 0) {
					?>
					<br>
					<table align="left"  width="<?=$width;?>" style="margin-top: 10px;" align="center"   border="0" cellpadding="0" cellspacing="0" >
					<tr>
					<td valign="top">
					
					<table   width="550" class="rpt_table" style="margin-top: 10px;"  align="center"  border="1" cellpadding="0" cellspacing="0" rules="all">
					<thead>
						<tr style="border:1px solid black;">
						<th width="3%" style="border:1px solid black;">Sl</th>
						<th width="40%" style="border:1px solid black;">Special Instruction</th>
						</tr>
					</thead>
					<tbody>
					<?
					
						//print_r($term_bookingArr);
					$sl=1;
							foreach ($term_bookingArr as $term=>$row) {
								?>
								<tr id="settr_1" align="" style="border:1px solid black;">
								<td align="center" style="border:1px solid black;text-align:center"><?=$sl;?></td>
							<td style="border:1px solid black; font-weight:bold"><div style="word-wrap:break-word;"><?=$row['terms'];?></div></td>
								<?
								$sl++;
								}
							
					?>
				</tbody>
				</table>
				</td>
				<!--1st part end-->
				<?
				$sl2=$sl;
				if (count($other_term_bookingArr) > 0) {
				?>
					<td valign="top">
						<table  width="550" class="rpt_table"  style="margin-top: 10px;"  align="center"  border="1" cellpadding="0" cellspacing="0" rules="all">
					<thead>
						<tr style="border:1px solid black;">
						<th width="3%" style="border:1px solid black;" >Sl</th>
						<th width="40%" style="border:1px solid black;">Special Instruction</th>
						</tr>
					</thead>
					<tbody>
					<?
							foreach ($other_term_bookingArr as $term2=>$row2) {
								?>
								<tr id="settr_2" align="" style="border:1px solid black;">
								<td align="center" style="border:1px solid black; text-align:center"><?=$sl2;?></td>
							<td style="border:1px solid black; font-weight:bold"><div style="word-wrap:break-word;"><?=$row2['terms'];?></td>
								<?
								$sl2++;
								}
							
					?>
				</tbody>
				</table>
				
					</td> 
					<?
				}
					?>   
				</tr>
				</table>
				<?
			}
				?>	
			

    	<table align="left" cellspacing="0" width="810" class="rpt_table" >
        	<tr>
            	<td colspan="6" align="left">
					<?

						$user_id=$_SESSION['logic_erp']['user_id'];
						$user_arr=return_library_array( "select id, USER_NAME from user_passwd where id=$user_id", "id", "USER_NAME");
						$prepared_by = $user_arr[$user_id];
	                      //echo signature_table(134, $data[0], "810px");
					  	echo signature_table(134, $data[0], "1000px",$cbo_template_id,$padding_top = 70,$prepared_by);
                    ?>
                </td>
            </tr>

        </table>

    </div>
   <script type="text/javascript" src="../../js/jquery.js"></script>
   <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
   <script>

	function fnc_generate_Barcodes( valuess, img_id )
	{
		var value = valuess;//$("#barcodeValue").val();
		var btype = 'code39';//$("input[name=btype]:checked").val();
		var renderer ='bmp';// $("input[name=renderer]:checked").val();
		var settings = {
		  output:renderer,
		  bgColor: '#FFFFFF',
		  color: '#000000',
		  barWidth: 1,
		  barHeight: 60,
		  moduleSize:5,
		  posX: 10,
		  posY: 20,
		  addQuietZone: 1
		};
		$("#"+img_id).html('11');
		 value = {code:value, rect: false};
		$("#"+img_id).show().barcode(value, btype, settings);
	}
   </script>
   <script type="text/javascript">
   	fnc_generate_Barcodes('<? echo $barcode_no; ?>','barcode_img_id');
   </script>

 <?
 exit();
}
if($action=="sample_requisition_print10")
{
	extract($_REQUEST);
	 $data=explode('*',$data);
	 $cbo_template_id=$data[3];

	// $cbo_template_id=str_replace("'","",$cbo_template_id);
	// $txt_booking_no=str_replace("'","",$txt_booking_no);
	// $cbo_company_name=str_replace("'","",$cbo_company_name);
	// $update_id=str_replace("'","",$update_id);
	
	$path="../../";
	if(count($data)>3)
	{
		if($data[4]=='../')
		{
			$path=$data[4];
		}
	}
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	//	$supplier_library=return_library_array( "select id,address_1, supplier_name from lib_supplier", "id", "supplier_name"  );
	$sql_supp=sql_select("select id,address_1,supplier_name from lib_supplier where status_active =1 and is_deleted=0");
	foreach ($sql_supp as $row)  
	{
		$supplier_address_library[$row[csf('id')]]=$row[csf('address_1')];
		$supplier_library[$row[csf('id')]]=$row[csf('supplier_name')];
	}
	$company_img=sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$dealing_merchant_library=return_library_array( "select id,team_member_name from lib_mkt_team_member_info", "id", "team_member_name"  );
	//$team_leader_arr=return_library_array( "select id,team_leader_name from lib_marketing_team", "id", "team_leader_name"  );

	$sample_library=return_library_array( "select id, sample_name from lib_sample", "id", "sample_name");
	//$brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand", "id", "brand_name");
	//$product_sub_dept_arr=return_library_array( "select id, sub_department_name from lib_pro_sub_deparatment", "id", "sub_department_name");
	$size_library=return_library_array( "select id, size_name from lib_size", "id", "size_name"  );
	$color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
	//$season_arr=return_library_array( "select id,season_name from lib_buyer_season  where status_active =1 and is_deleted=0 ", "id", "season_name"  );
	//$trims_group_lib=return_library_array( "select id,item_name from lib_item_group  where status_active =1 and is_deleted=0", "id", "item_name");
	//concate(buyer_name,'_',contact_person)
	//$appDate=return_field_value("approved_date","approval_history","entry_form=25 and mst_id='$data[1]' order by id desc");
	//$appBy=return_field_value("approved_by","approval_history","entry_form=25 and mst_id='$data[1]'");
	$user_arr=return_library_array( "select id, user_name from user_passwd", "id", "user_name"  );
	$user_library=return_library_array( "select id, user_full_name from user_passwd", "id", "user_full_name"  );
	$image_location='';
	$image_location_arr = sql_select("select master_tble_id,image_location from common_photo_library where form_name='sample_requisition_2' and file_type=1 and master_tble_id='$data[1]'");
	foreach ($image_location_arr as $row) {
		$image_locationArr[$row[csf('image_location')]]=$row[csf('image_location')];
	}
	$data_img=sql_select("select image_location,master_tble_id  from common_photo_library  where   form_name='required_fabric_1' and is_deleted=0 and file_type=1");
	$system_img_arr=array();
	foreach($data_img as $row)
	{
	  $system_img_arr[$row[csf('master_tble_id')]]['img']=$row[csf('image_location')];
	}

	$sam_img=sql_select("select image_location,master_tble_id  from common_photo_library  where   form_name='sample_details_1' and is_deleted=0 and file_type=1");
	$sam_img_arr=array();
	foreach($sam_img as $row)
	{
	  $sam_img_arr[$row[csf('master_tble_id')]]['img']=$row[csf('image_location')];
	}
	//  echo '<pre>';
    //     print_r($sam_img_arr); die;

	
	

	/*$sample_dtls_addi_value=sql_select("SELECT print, printseq, embro, embroseq, wash, washseq, spworks, spworksseq, gmtsdying, gmtsdyingseq, aop, aopseq, bush, bushseq, peach, peachseq, yd, ydseq from sample_details_additional_value where mst_id=$data[1]");
	$print_status=2; $aop_status=2; $embro_status=2; $wash_status=2; $peach_status=2; $bush_status=2; $yd_status=2;
	foreach ($sample_dtls_addi_value as $row) {
		if($row[csf('print')]==1){
			$print_status=1;
		}
		if($row[csf('embro')]==1){
			$embro_status=1;
		}
		if($row[csf('aop')]==1){
			$aop_status=1;
		}
		if($row[csf('wash')]==1){
			$wash_status=1;
		}
		if($row[csf('peach')]==1){
			$peach_status=1;
		}
		if($row[csf('bush')]==1){
			$bush_status=1;
		}
		if($row[csf('yd')]==1){
			$yd_status=1;
		}
	}

	$sql_embellishment =sql_select("SELECT id, sample_mst_id, sample_name_re, gmts_item_id_re, name_re, type_re, remarks_re, body_part_id, delivery_date, supplier_id from sample_development_fabric_acc where sample_mst_id='$data[1]' and form_type=3 and is_deleted=0  and status_active=1 and name_re in (1,2,3) order by id asc");
	$emb_print_type=''; $emb_embroy_type=''; $emb_wash_type='';
	foreach ($sql_embellishment as $row) {
		if($row[csf('name_re')]==1){
			$print_status=1;
			$emb_print_type=$emblishment_print_type[$row[csf('type_re')]];
		}
		if($row[csf('name_re')]==2){
			$embro_status=1;
			$emb_embroy_type=$emblishment_embroy_type[$row[csf('type_re')]];
		}
		if($row[csf('name_re')]==3){
			$wash_status=1;
			$emb_wash_type=$emblishment_wash_type[$row[csf('type_re')]];
		}
	}*/
 ?>
 <style>
	#mstDiv {
	    margin:0px auto;
	    width:1130px;

	}
	#mstDiv @media print {

	   thead {display: table-header-group;}

	}

	 @media print{
			html>body table.rpt_table {
			margin-left:12px;
	  		}

		}
	</style>

		

	<div id="mstDiv">

        <table width="1100" cellspacing="0" border="0"   >
            <tr>
                <td rowspan="4" valign="top" width="150"><img width="150" height="80" src="<?=$path?><? echo $company_img[0][csf("image_location")]; ?>"></td>
                <td colspan="5" style="font-size:20px;text-align: center;"><strong><b><? echo $company_library[$data[0]]; ?></b></strong></td>
            </tr>
            <tr>
                <td colspan="5" style="text-align: center;">
					<?
                    $val=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
                    echo ($val[0][csf('plot_no')])?   $val[0][csf('plot_no')].',': "";
                    echo ($val[0][csf('level_no')])?  $val[0][csf('level_no')].',': "";
                    echo ($val[0][csf('road_no')])?   $val[0][csf('road_no')].',': "";
                    echo ($val[0][csf('block_no')])?  $val[0][csf('block_no')].',': "";
                    echo ($val[0][csf('city')])?      $val[0][csf('city')].',': "";
                    echo ($val[0][csf('zip_code')])?  $val[0][csf('zip_code')].',': "";
                    echo ($val[0][csf('province')])?  $val[0][csf('province')].',': "";
                    echo($val[0][csf('country_id')])? $country_arr[$val[0][csf('country_id')]]: "";
                    echo ($val[0][csf('email')])?    "</br>". $val[0][csf('email')].',': "</br>";
                    echo($val[0][csf('website')])?    $val[0][csf('website')]: "";

                      $sql="SELECT id, requisition_number, style_desc,requisition_number_prefix_num, style_ref_no, buyer_name, season, product_dept, dealing_marchant, agent_name, buyer_ref, bh_merchant, estimated_shipdate, team_leader, season_buyer_wise, remarks, quotation_id, sample_stage_id, requisition_date, material_delivery_date, season_year, brand_id, is_acknowledge,refusing_cause,sub_dept_id,inserted_by from sample_development_mst where  id='$data[1]' and entry_form_id=203 and  is_deleted=0  and status_active=1";
                    $dataArray=sql_select($sql);
                    $refusing_cause=$dataArray[0][csf('refusing_cause')]; 
					$style_ref_no=$dataArray[0][csf('style_ref_no')];
					$style_desc=$dataArray[0][csf('style_desc')];
                    $barcode_no=$dataArray[0][csf('requisition_number')];
					$prepared_by=$user_library[$dataArray[0][csf('inserted_by')]];
                    if($dataArray[0][csf("sample_stage_id")]==1)
                    {
                        $job_lib=return_library_array( "SELECT a.id,min(b.shipment_date) as shipment_date  from wo_po_details_master  a,wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and a.company_name='$cbo_company_name' GROUP BY a.id", "id", "shipment_date"  );
                    }
					 $sqls="SELECT id, style_desc, is_approved,supplier_id,attention, revised_no,pay_mode, buyer_req_no,exchange_rate,currency_id, source, team_leader, dealing_marchant, booking_date, attention, remarks from wo_non_ord_samp_booking_mst where  booking_no='$data[2]' and is_deleted=0  and status_active=1";
 					 $dataArray_book=sql_select($sqls);
					  $is_approved=$dataArray_book[0][csf('is_approved')];
					  $booking_mst_id=$dataArray_book[0][csf('id')];

 					 $sample_acc_arr=sql_select("SELECT confirm_del_end_date, refusing_cause, unacknowledge_date, insert_date from sample_requisition_acknowledge where sample_mst_id= '$data[1]'");
					 $sample_delivery="SELECT delivery_date from sample_development_fabric_acc where sample_mst_id= '$data[1]'";
					 $dataSample_delivery=sql_select($sample_delivery);
					 $delivery_date=$dataSample_delivery[0][csf('delivery_date')];
					 //echo "SELECT delivery_date from sample_development_fabric_acc where sample_mst_id= '$data[1]'";
				
 				//	 select id,team_member_name from lib_mkt_team_member_info where team_id='$data' and status_active =1 and is_deleted=0 order by team_member_name
					$msg="";
					if($is_approved==1 || $is_approved==3)
					{
						$msg="Approved";
					}
                    ?>
                </td>
            </tr>
			<tr>
                <td colspan="5" style="font-size:medium; text-align: center;"><strong style="font-size:18px">Sample Fabric Booking -Without order</strong></td>               
            </tr>
			<tr>
				<td align="right"><strong style="color:red;font-size: 25px;margin-right:10%;"><?=$msg;?></strong></td>
            </tr>
        </table>
        <table width="1100" cellspacing="0" border="0" class="rpt_table" style="font-size:14px" >
        	<tr>
        		<td align="left" width="150" >Booking No</td>
        		<td align="left" width="150" ><?=$data[2];//$dataArray[0][csf("requisition_number")];?></td>
        		<td align="left" width="100">Booking Date</td>
        		<td align="left" width="80"><?=$dataArray_book[0][csf('booking_date')];?></td>
        		<td align="left" width="100">Fab. Delivery Date</td>
        		<td width="80"><?=change_date_format($delivery_date); ?></td>
        		 
        	</tr>
             
        	<tr>
        		<td width="150" align="left">Buyer</td>
                <td width="150" align="left"><?=$buyer_library[$dataArray[0][csf('buyer_name')]];?></td>
                <td align="left" width="100">Requisition No</td>
                <td align="left" width="80"> <?=$dataArray[0][csf("requisition_number")];?></td>
                
                <td align="left" width="100">Supplier Name</td>
                <td  align="left" > <? 
				if($dataArray_book[0][csf('pay_mode')]==3 || $dataArray_book[0][csf('pay_mode')]==5)
				{
					echo $company_library[$dataArray_book[0][csf("supplier_id")]];
				}
				else
				{
					echo $supplier_library[$dataArray_book[0][csf("supplier_id")]];
					$supplier_address=$supplier_address_library[$row[csf('supplier_id')]];
				}?></td>
        	</tr>
        	<tr>
        		<td width="150" align="left">Currency</td>
                <td width="150" align="left"><?=$currency[$dataArray_book[0][csf('currency_id')]];?></td>
                <td align="left" width="100">Attention</td>
                <td align="left" width="80"> <?=$dataArray_book[0][csf("attention")];?></td>
                <td align="left" width="100">Supplier Address</td>
                <td  align="left"> <?=$supplier_address;?></td>
        	</tr>
        	<tr>
        		<td width="150" align="left">Pay mode</td>
                <td width="150" align="left"><?=$pay_mode[$dataArray_book[0][csf('pay_mode')]];?></td>
                <td align="left" width="100">Conversion Rate</td>
                <td align="left" width="80"> <?=$dataArray_book[0][csf("exchange_rate")];?></td>
                <td align="left" width="100">Dealing Merchant</td>
                <td  align="left"> <?=$dealing_merchant_library[$dataArray_book[0][csf("dealing_marchant")]];?></td>
        	</tr>
        	 
        	 
        </table>
        <br>
		<?
         $color_sql="SELECT b.color_id ,b.process_loss_percent ,b.fabric_color,b.contrast,b.mst_id,b.dtls_id, b.qnty from  sample_development_rf_color b where b.status_active=1 and b.is_deleted=0 and b.qnty>0 and b.mst_id='$data[1]' ";
		 $color_res=sql_select($color_sql);
		 $color_rf_data=array();
		 foreach ($color_res as $val) {
		 	$color_rf_data[$val[csf('dtls_id')]][$val[csf('color_id')]][$val[csf('fabric_color')]]['process_loss_percent']=$val[csf('process_loss_percent')];
		 	$color_rf_data[$val[csf('dtls_id')]][$val[csf('color_id')]][$val[csf('fabric_color')]]['contrast']=$val[csf('contrast')];
		 	$color_rf_data[$val[csf('dtls_id')]][$val[csf('color_id')]][$val[csf('fabric_color')]]['qnty']=$val[csf('qnty')];
		 }

		 $sql_fab="SELECT a.id,a.sample_name, a.gmts_item_id, c.gmts_color as color_id,  a.determination_id, a.delivery_date, a.fabric_description, a.body_part_id, a.fabric_source, a.remarks_ra, a.gsm, a.dia, a.color_type_id, a.width_dia_id, a.uom_id,c.rate,c.amount, c.grey_fabric as grey_fab_qnty, c.fabric_color,c.dtls_id,c.finish_fabric as qnty,a.id,a.determination_id from sample_development_fabric_acc a, wo_non_ord_samp_booking_dtls c where  a.id=c.dtls_id  and a.form_type=1  and c.grey_fabric>0 and a.status_active=1 and a.is_deleted=0  and c.status_active=1 and c.is_deleted=0  and a.sample_mst_id='$data[1]' ";
		//echo  $sql_fab;
        $sql_fab_arr=array();
        $dtls_id_arr=array();
        $determination_id_arr=array();

        foreach(sql_select($sql_fab) as $vals)
        {
        	$contrast=$color_rf_data[$vals[csf('dtls_id')]][$vals[csf('color_id')]][$vals[csf('fabric_color')]]['contrast'];

			$process_loss_percent=$color_rf_data[$vals[csf('dtls_id')]][$vals[csf('color_id')]][$vals[csf('fabric_color')]]['process_loss_percent'];

            $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$contrast]["qnty"]+=$vals[csf("qnty")];
            $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$contrast]["process_loss_percent"]+=$process_loss_percent;

            $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$contrast]["grey_fab_qnty"]+=$vals[csf("grey_fab_qnty")];
			 $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$contrast]["amount"]+=$vals[csf("amount")];
			  $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$contrast]["rate"]=$vals[csf("rate")];

            $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$contrast]["delivery_date"] =change_date_format($vals[csf("delivery_date")]);
			 $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$contrast]["remarks_ra"] =$vals[csf("remarks_ra")];

			$sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$contrast]["id"] =$vals[csf("id")];

            $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$contrast]["fabric_source"] =$vals[csf("fabric_source")];

            $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$contrast]["uom_id"] =$vals[csf("uom_id")];
            $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$contrast]["dia"] =$vals[csf("dia")];

            $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$contrast]["width_dia_id"] =$vals[csf("width_dia_id")];

            $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$contrast]["remarks"] =$vals[csf("remarks_ra")];
            $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$contrast]["color_type_id"] =$vals[csf("color_type_id")];

            $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$contrast]["determination_id"] =$vals[csf("determination_id")];
            array_push($dtls_id_arr,$vals[csf('id')]);
            array_push($determination_id_arr,$vals[csf('determination_id')]);
        }
        $sample_item_wise_span=array();
        /*echo '<pre>';
        print_r($sql_fab_arr); die;*/
        $determination_id_cond= where_con_using_array($determination_id_arr,0,"a.id");

        $update_dtls_id_cond= where_con_using_array($dtls_id_arr,0,"a.dtls_id");
        $sql = "SELECT a.sample_color, a.size_id, a.item_size, a.qnty_pcs, b.body_part_id from sample_requisition_coller_cuff a join sample_development_fabric_acc b on a.DTLS_ID=b.id where  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $update_dtls_id_cond";
        //echo $sql; die;
		$collar_cuff_data_arr = sql_select($sql);
		foreach ($collar_cuff_data_arr as $row)
		{
			$sample_color = $row[csf('sample_color')];
			$itemsize = $row[csf('item_size')];
			//$collarCuffarr[$sample_color].=$itemsize."***";
			$collarCuffarr[$sample_color][$row[csf('body_part_id')]][$itemsize]=$itemsize;
		}
		//
		 $sql_d_yr = "SELECT  a.id, b.type_id FROM lib_yarn_count_determina_mst a left join lib_yarn_count_determina_dtls b on a.id = b.mst_id AND b.status_active = 1 AND b.is_deleted = 0 WHERE a.status_active = 1 AND a.is_deleted = 0  $determination_id_cond";
		  $determina_y_arr = sql_select($sql_d_yr);
		 foreach ($determina_y_arr as $row)
		{
			$yarn_construction_data_arr[$row[csf('id')]].=$yarn_type[$row[csf('type_id')]]."***";
		}
		 $sql_d = "SELECT b.fabric_composition_name, a.id, a.construction FROM lib_yarn_count_determina_mst a left join lib_fabric_composition b on a.fabric_composition_id = b.id AND b.status_active = 1 AND b.is_deleted = 0 WHERE a.status_active = 1 AND a.is_deleted = 0  $determination_id_cond";
		// echo $sql_d; //die;
		 $determina_arr = sql_select($sql_d);
		$determina_data_arr=array();
		foreach ($determina_arr as $row)
		{
			$determina_data_arr[$row[csf('id')]].=$row[csf('fabric_composition_name')]."***";
			$construction_data_arr[$row[csf('id')]].=$row[csf('construction')]."***";
		}

        foreach($sql_fab_arr as $sample_type=>$colorType_data)
        {
            foreach($colorType_data as $colorType=>$gmts_color_data)
            {

                foreach($gmts_color_data as $gmts_color_id=>$body_part_data)
                {
                	$sample_span=0;
                    foreach($body_part_data as $body_part_id=>$fab_desc_data)
                    {
                        //$kk=0;
                        foreach($fab_desc_data as $fab_id=>$gsm_data)
                        {
                            foreach($gsm_data as $gsm_id=>$dia_data)
                            {
                                foreach($dia_data as $dia_id=>$color_data)
                                {
                                    foreach($color_data as $contrast_id=>$row)
                                    {
                                        $sample_span++;
                                        //$kk++;
                                    }
									 $sample_item_wise_span[$sample_type][$gmts_color_id]=$sample_span;
                                }
                            }
                        }
                        //$bodypart_item_wise_span[$sample_type][$gmts_item_id][$body_part_id]=$kk;
                    }
                   
                }
            }
        }
	  /*        echo "<pre>";
        print_r($sample_item_wise_span);die;*/

        $sql_sample_dtls= "SELECT a.sample_name,a.sample_charge, a.article_no, a.sample_color from sample_development_dtls a, lib_color b where a.status_active=1 and a.is_deleted=0 and a.entry_form_id=203  and sample_mst_id='$data[1]' and b.status_active=1 and b.id=a.sample_color ";
        foreach(sql_select($sql_sample_dtls) as $key=>$value)
        {
            if($sample_wise_article_no[$value[csf("sample_name")]][$value[csf("sample_color")]]=="")
            {
                $sample_wise_article_no[$value[csf("sample_name")]][$value[csf("sample_color")]]=$value[csf("article_no")];
            }
            else
            {
                if(!in_array($value[csf("article_no")], $sample_wise_article_no))
                {
                    $sample_wise_article_no[$value[csf("sample_name")]][$value[csf("sample_color")]].= ', '.$value[csf("article_no")];
                }
            }
			$sample_wise_rate_arr[$value[csf("sample_name")]][$value[csf("sample_color")]]=$value[csf("sample_charge")];
        }

        // echo "<pre>"; print_r($sample_wise_article_no);die;

        ?>
        <table class="rpt_table" width="1100"  border="1" cellpadding="0" cellspacing="0" rules="all" style="margin-top:5px; font-size:14px">
            <thead>
                <tr>
                    <th colspan="20">Required Fabric</th>
                </tr>
                <tr>
                    <th width="20">Sl</th>
                    <th width="60">Style Ref.</th>
                    <th width="60">Style Desc.</th>
                    <th width="60">Sample</th>
                    <th width="60">Body Part</th>
                    <th width="50">Color Type</th>
                    <th width="60">Construction</th>
                    <th width="70">Composition & Yarn</th>
                    <th width="60">Y/Type</th>
                    <th width="40">GSM</th>
                    <th width="70">Gmts Color</th>
                    <th width="70">Fabric Color</th>
                    <th width="40">Dia/ Width</th>
                    <th width="70">Fin Fab Qnty</th>
                    <th width="50">Process Loss</th>
                    <th width="70">Gray Qnty</th>
                    <th width="40">UOM</th>
                    <th width="60">Fab. Source</th>
                    <th width="">Remarks</th>
					
					
                    
                </tr>
            </thead>
            <tbody>
                <?
                function str_replace_first($search, $replace, $subject)
				{
				    $search = '/'.preg_quote($search, '/').'/';
				    return preg_replace($search, $replace, $subject, 1);
				}
                $p=1; $total_finish=0; $total_grey=0; $total_process=$total_amount=0;
                foreach($sql_fab_arr as $sample_type=>$colorType_data)
                {
                    foreach($colorType_data as $colorType=>$gmts_color_data)
                    {
                        foreach($gmts_color_data as $gmts_color_id=>$body_part_data)
                        {
                            $nn=0;
                            foreach($body_part_data as $body_part_id=>$fab_desc_data)
                            {
                                foreach($fab_desc_data as $fab_id=>$gsm_data)
                                {
                                    foreach($gsm_data as $gsm_id=>$dia_data)
                                    {
                                        foreach($dia_data as $dia_id=>$color_data)
                                        {
                                            //$i=0;
                                            foreach($color_data as $contrast_id=>$value)
                                            {
                                            	$constr=implode(",", array_unique(explode("***", chop($construction_data_arr[$value['determination_id']],"***"))));
												$yarn_type_id=implode(",", array_unique(explode("***", chop($yarn_construction_data_arr[$value['determination_id']],"***"))));
                                                ?>
                                                <tr>
                                                    <td  align="center" style="word-wrap: break-word;word-break: break-all;"><? echo $p;$p++;?></td>
                                                    <?
													 
                                                   /* if($nn==0)
                                                    {*/
                                                        $rowspan=0;
                                                        //$rowspan=$sample_item_wise_span[$sample_type][$gmts_color_id];
                                                        ?>
                                                       
                                                        <td rowspan="<?=$rowspan;?>" align="center"><?=$style_ref_no;?></td>
                                                        <td align="center"><?=$style_desc;?> </td>
                                                        <td rowspan="<?=$rowspan;?>" align="center"><?=$sample_library[$sample_type];?></td>
                                                       
                                                        <?
                                                        $nn++;
                                                    /*}*/
                                                 //  echo $contrast_id.'DDDDDDDS';
												 $sample_wise_rate= $sample_wise_rate_arr[$sample_type][$gmts_color_id];
                                                    ?>
                                                   
                                                    <td align="center" style="word-break:break-all"><?=$body_part[$body_part_id];?></td>
                                                     <td align="center" style="word-break:break-all"><?=$color_type[$colorType];?></td>
                                                    <td align="center" style="word-break:break-all"><?=$constr;?></td>
                                                    <td align="center" style="word-break:break-all"><?=str_replace_first(trim($constr), "", $fab_id);//implode(" , ", array_unique(explode("***", chop($determina_data_arr[$value['determination_id']],"***"))));// echo $fab_id;?></td>
                                                   
                                                    <td align="center" style="word-break:break-all"><?=$yarn_type_id;//$contrast_id;?></td>
                                                     <td align="center" style="word-break:break-all"><?=$gsm_id;?></td>
                                                    <td align="center" style="word-break:break-all"><? echo $color_library[$gmts_color_id];//implode(", ", $collarCuffarr[$gmts_color_id][$body_part_id]);?></td>
                                                   
                                                    <td align="center" style="word-break:break-all"><?=$contrast_id;//$value["dia"];?></td>
                                                    <td align="center" style="word-break:break-all"><?=$fabric_typee[$value["width_dia_id"]];?></td>
                                                     <td align="right"><?=number_format($value["grey_fab_qnty"], 2);?></td>
                                                     <td align="right"><?=$value["process_loss_percent"];?></td>
                                                    <td align="right"><?=number_format($value["qnty"],2);?></td>
                                                    
                                                    <td align="center" style="word-break:break-all"><?=$unit_of_measurement[$value["uom_id"]];?></td>
                                                     <td align="center"><? echo $fabric_source[$value["fabric_source"]];?></td>
                                                    
                                                    <td style="word-break:break-all"><?=$value["remarks"];?></td>
													 
                                                </tr>
                                                <?
                                                //$i++;
                                                $total_finish +=$value["qnty"];
                                                $total_grey +=$value["grey_fab_qnty"];
											//	$total_amount +=$sample_wise_rate*$value["amount"];
                                               // $total_process +=$value["process_loss_percent"];
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                ?>
                <tr>
                    <th colspan="12" align="right"><b>Total</b></th>
                    <th align="right"></th>
                    <th align="right"><?=number_format($total_grey, 2);?></th>
                    <th align="right"></th>
                    <th align="right"><?=number_format($total_finish, 2);?></th>
                    <th colspan="2">&nbsp;</th>
                     
                </tr>
            </tbody>
        </table>
        <br/>
      
        <br>&nbsp;
        <?
        	$sample_stripe_data=sql_select("SELECT a.body_part_id, b.contrast, b.color_id, b.grey_fab_qnty,c.id as strip_mst_id, c.stripe_color, c.measurement, c.uom, c.fabreq, c.yarn_dyed,c.totfidder FROM sample_development_fabric_acc a join sample_development_rf_color b on a.id=b.dtls_id join wo_sample_stripe_color c on a.id=c.sample_fab_dtls_id and b.color_id=c.color_number_id where a.status_active=1 and a.is_deleted=0 and a.color_type_id in (2,3,4,6,31,32,33,34) and a.form_type=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.sample_mst_id=$data[1]");
        	
        	foreach ($sample_stripe_data as $row) {
        		$key=$row[csf('body_part_id')].'*'.$row[csf('color_id')];
        		$sample_stripe_arr[$key]['body_part_id'] = $row[csf('body_part_id')];
        		$sample_stripe_arr[$key]['fabric_color'] = $row[csf('color_id')];
        		$sample_stripe_arr[$key]['fabric_qty'] = $row[csf('grey_fab_qnty')];
				
        		$sample_stripe_arr[$key]['stripe_color'][$row[csf('strip_mst_id')]][$row[csf('stripe_color')]]['color'] = $row[csf('stripe_color')];
        		$sample_stripe_arr[$key]['stripe_color'][$row[csf('strip_mst_id')]][$row[csf('stripe_color')]]['measurement'] = $row[csf('measurement')];
        		$sample_stripe_arr[$key]['stripe_color'][$row[csf('strip_mst_id')]][$row[csf('stripe_color')]]['uom'] = $row[csf('uom')];
        		$sample_stripe_arr[$key]['stripe_color'][$row[csf('strip_mst_id')]][$row[csf('stripe_color')]]['qty'] = $row[csf('fabreq')];
        		$sample_stripe_arr[$key]['stripe_color'][$row[csf('strip_mst_id')]][$row[csf('stripe_color')]]['yarn_dyed'] = $row[csf('yarn_dyed')];
				$sample_stripe_arr[$key]['stripe_color'][$row[csf('strip_mst_id')]][$row[csf('stripe_color')]]['totfidder'] = $row[csf('totfidder')];
        		
				$stripe_color_summ[$row[csf('stripe_color')]] += $row[csf('fabreq')];
        	}
        ?>
     
        <?
			$coller_cuff_data=sql_select("SELECT a.sample_color, a.size_id, a.item_size, a.qnty_pcs, c.body_part_type from sample_requisition_coller_cuff a join sample_development_fabric_acc b on b.id=a.dtls_id join lib_body_part c on b.body_part_id=c.id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.sample_mst_id=$data[1]");
			//echo "SELECT a.sample_color, a.size_id, a.item_size, a.qnty_pcs, c.body_part_type from sample_requisition_coller_cuff a join sample_development_fabric_acc b on b.id=a.dtls_id join lib_body_part c on b.body_part_id=c.id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.sample_mst_id=$data[1]";
			
			 
			$coller_data_arr=array(); $cuff_data_arr=array();
			foreach ($coller_cuff_data as $row) {
				if($row[csf('body_part_type')]==40)
				{
					$coller_size_arr[$row[csf('size_id')]]=$row[csf('size_id')];
					$color_size_data[$row[csf('size_id')]]['item_size']=$row[csf('item_size')];
					$color_color_data[$row[csf('sample_color')]][$row[csf('size_id')]]['qnty_pcs']=$row[csf('qnty_pcs')];
				}
				if($row[csf('body_part_type')]==50)
				{
					$cuff_size_arr[$row[csf('size_id')]]=$row[csf('size_id')];
					$cuff_size_data[$row[csf('size_id')]]['item_size']=$row[csf('item_size')];
					$cuff_color_data[$row[csf('sample_color')]][$row[csf('size_id')]]['qnty_pcs']=$row[csf('qnty_pcs')];
				}
			} 
			/*echo '<pre>';
			print_r($color_color_data); die;*/
        ?>
        <div style="width:1000px; margin-top: 10px;">
            <?
            $collar_cuff_percent_arr=array(); $collar_cuff_body_arr=array(); $collar_cuff_color_arr=array(); $collar_cuff_size_arr=array(); $collar_cuff_item_size_arr=array(); $color_size_sensitive_arr=array();

			$collar_cuff_sql="select b.id, b.gmts_item_id as item_number_id, a.qnty_pcs,a.sample_color as color_number_id, a.size_id as gmts_sizes, a.item_size, a.size_id as size_number_id,  e.body_part_full_name, e.body_part_type
			FROM sample_requisition_coller_cuff a left join lib_size s on a.size_id=s.id, sample_development_fabric_acc b, lib_body_part  e

			WHERE b.id=a.dtls_id   and b.body_part_id=e.id and e.body_part_type in (40,50)  and b.sample_mst_id=$data[1] and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and e.status_active=1 and e.is_deleted=0 order by  b.id,a.sample_color,s.sequence";
			//echo $collar_cuff_sql;
			$collar_cuff_sql_res=sql_select($collar_cuff_sql);
			$itemIdArr=array();

			foreach($collar_cuff_sql_res as $collar_cuff_row) 
			{
				$collar_cuff_percent_arr[$collar_cuff_row[csf('body_part_type')]][$collar_cuff_row[csf('body_part_full_name')]][$collar_cuff_row[csf('color_number_id')]][$collar_cuff_row[csf('gmts_sizes')]]=$collar_cuff_row[csf('colar_cuff_per')];
				$collar_cuff_body_arr[$collar_cuff_row[csf('body_part_type')]][$collar_cuff_row[csf('body_part_full_name')]]=$collar_cuff_row[csf('body_part_full_name')];
				$collar_cuff_size_arr[$collar_cuff_row[csf('body_part_type')]][$collar_cuff_row[csf('body_part_full_name')]][$collar_cuff_row[csf('size_number_id')]]=$collar_cuff_row[csf('size_number_id')];
				if(!empty($collar_cuff_row[csf('item_size')]))
				{
					$collar_cuff_item_size_arr[$collar_cuff_row[csf('body_part_full_name')]][$collar_cuff_row[csf('size_number_id')]][$collar_cuff_row[csf('item_size')]]=$collar_cuff_row[csf('item_size')];
				}
				
				$color_size_sensitive_arr[$collar_cuff_row[csf('body_part_full_name')]][$collar_cuff_row[csf('id')]][$collar_cuff_row[csf('color_number_id')]]=$collar_cuff_row[csf('qnty_pcs')];
				// $collar_cuff_size_Qty_arr[$collar_cuff_row[csf('body_part_type')]][$collar_cuff_row[csf('body_part_full_name')]][$collar_cuff_row[csf('id')]][$collar_cuff_row[csf('size_number_id')]]=$collar_cuff_row[csf('qnty_pcs')];

				$collar_cuff_size_Qty_arr[$collar_cuff_row[csf('body_part_type')]][$collar_cuff_row[csf('body_part_full_name')]][$collar_cuff_row[csf('id')]][$collar_cuff_row[csf('color_number_id')]][$collar_cuff_row[csf('size_number_id')]]=$collar_cuff_row[csf('qnty_pcs')];
				
				$itemIdArr[$collar_cuff_row[csf('body_part_type')]].=$collar_cuff_row[csf('item_number_id')].',';
			}
			//print_r($collar_cuff_percent_arr[40]) ;
			unset($collar_cuff_sql_res);
			//$count_collar_cuff=count($collar_cuff_size_arr);
			
			/*$order_plan_qty_arr=array();
			$color_wise_wo_sql_qnty=sql_select( "select item_number_id, color_number_id, size_number_id, sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as order_quantity  from wo_po_color_size_breakdown where  po_break_down_id in ($booking_po_id) and status_active=1 and is_deleted =0  group by item_number_id, color_number_id, size_number_id");//and item_number_id in (".implode(",",$itemIdArr).")
			foreach($color_wise_wo_sql_qnty as $row)
			{
				$order_plan_qty_arr[$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['plan']=$row[csf('plan_cut_qnty')];
				$order_plan_qty_arr[$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['order']=$row[csf('order_quantity')];
			}
			unset($color_wise_wo_sql_qnty);*/

			
			foreach($collar_cuff_body_arr as $body_type=>$body_name)
			{
				$gmtsItemId=array_filter(array_unique(explode(",",$itemIdArr[$body_type])));
				foreach($body_name as $body_val)
				{
					$count_collar_cuff=count($collar_cuff_size_arr[$body_type][$body_val]);
					$pre_grand_tot_collar=0; $pre_grand_tot_collar_order_qty=0;

					?>
                    <div style="max-height:1330px; overflow:auto; float:left; padding-top:5px; margin-left:5px; margin-bottom:5px; position:relative;font-size:18px;">
					<table width="625" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                        <tr>
                        	<td colspan="<? echo $count_collar_cuff+3; ?>" align="center"><b><? echo $body_val; ?> - Color Size Brakedown in Pcs.</b></td>
                        </tr>
                        <tr>
                            <td width="100">Size</td>
								<?
                                foreach($collar_cuff_size_arr[$body_type][$body_val]  as $size_number_id)
                                {
									?>
									<td align="center" style="border:1px solid black"><strong><? echo $size_library[$size_number_id];?></strong></td>
									<?
                                }
                                ?>
                            <td width="60" rowspan="2" align="center"><strong>Total</strong></td>
                            
                        </tr>
                        <tr>
                            <td style="font-size:12px"><? echo $body_val; ?> Size</td>
                            <?
                            foreach($collar_cuff_item_size_arr[$body_val]  as $size_number_id=>$size_number)
                            {
								if(count($size_number)>0)
								{
									 foreach($size_number  as $item_size=>$val)
									 {
										?>
										<td align="center" style="border:1px solid black"><strong><? echo $item_size;?></strong></td>
										<?
									 }
								}
								else
								{
									?>
									<td align="center" style="border:1px solid black"><strong> &nbsp;</strong></td>
									<?
								}
                            }
                            ?>
                        </tr>
                            <?

                            $pre_size_total_arr=array();
                            foreach($color_size_sensitive_arr[$body_val] as $fab_req_id=>$pre_cost_data)
                            {
								foreach($pre_cost_data as $color_number_id=>$color_number_data)
								{
									//foreach($color_number_data as $color_size_sensitive=>$color_break_down)
									//{
										$pre_color_total_collar=0;
										$pre_color_total_collar_order_qnty=0;
										 
										?>
										<tr>
											<td>
												<?
                                               
												 echo $color_library[$color_number_id];
                                                ?>
											</td>
											<?
											foreach($collar_cuff_size_arr[$body_type][$body_val] as $size_number_id)
											{
												?>
												<td align="center" style="border:1px solid black">
													<?   $collerqty=0;  
													$color_cuff_cut=0;
													// $color_cuff_cut=$collar_cuff_size_Qty_arr[$body_type][$body_val][$fab_req_id][$size_number_id];
													$color_cuff_cut=$collar_cuff_size_Qty_arr[$body_type][$body_val][$fab_req_id][$color_number_id][$size_number_id];
                                                	if($body_type==50){
														// $collerqty=$color_cuff_cut*2;
														$collerqty=$color_cuff_cut;
													}else{
														$collerqty=$color_cuff_cut;
													}
                                                    echo number_format($collerqty);
                                                    $pre_size_total_arr[$size_number_id]+=$collerqty;
                                                    $pre_color_total_collar+=$collerqty;
                                                    $pre_color_total_collar_order_qnty+=$color_cuff_cut;
                                                    ?>
												</td>
												<?
											}
											?>

											<td align="center"><? echo number_format($pre_color_total_collar); ?></td>
											 
												 
										</tr>
										<?
										$pre_grand_collar_ex_per+=$collar_ex_per;
										$pre_grand_tot_collar+=$pre_color_total_collar;
										$pre_grand_tot_collar_order_qty+=$pre_color_total_collar_order_qnty;
									
								}
							}
							?>
                        
                        <tr>
                            <td>Size Total</td>
								<?
                               // foreach($pre_size_total_arr  as $size_qty)
                               // {
                                	foreach($collar_cuff_size_arr[$body_type][$body_val] as $size_number_id)
									{
										$size_qty=$pre_size_total_arr[$size_number_id];
										?>
										<td style="border:1px solid black;  text-align:center"><? echo number_format($size_qty); ?></td>
										<?
									}

                               // }
                                ?>
                            <td style="border:1px solid black; text-align:center"><? echo number_format($pre_grand_tot_collar); ?></td>
                            <!-- <td align="center" style="border:1px solid black"><? echo number_format((($pre_grand_tot_collar-$pre_grand_tot_collar_order_qty)/$pre_grand_tot_collar_order_qty)*100,2); ?></td> -->
							 
                        </tr>
					</table>
                </div>
                <?
            }
        }
			?>
            <br>
              <?
        $sample_color_arr=return_library_array( "select id, sample_color from sample_development_dtls", "id", "sample_color");
      
 
         $sql_qry="SELECT id, sample_mst_id, sample_name, gmts_item_id, smv, article_no, sample_color, sample_prod_qty, submission_qty, delv_start_date, delv_end_date, sample_charge, sample_curency, sent_to_buyer_date, comments from sample_development_dtls where status_active=1 and is_deleted=0 and entry_form_id=203 and sample_mst_id='$data[1]' order by id asc";
        $result=sql_select($sql_qry);
        ?>
        <table align="left" cellspacing="0" border="1" width="1100" class="rpt_table" rules="all" style="margin-top: 5px; font-size:14px">
            <thead>
                <tr>
                    <td width="150" colspan="3" align="center"><strong>Sample Details</strong></td>
                </tr>
                <tr>
                    <th width="30" >Sl</th>
                    <th width="70">Color</th>
                    <th width="55">Qnty</th>
                   
                </tr>
                 
            </thead>
            <tbody>
                <?
                $i=1; $k=0; $gr_tot_sum=0; $gr_sub_sum=0;
                foreach($result as $row)
                {
                    $dtls_ids=$row[csf('id')];
                    $prod_sum=$prod_sum+$row[csf('sample_prod_qty')];
                    $total_color_qty=$sub_sum+$row[csf('submission_qty')];
                    $k++;
					// $size_qty=$color_size_dtls_qty_arr[$type_id][$dtls_ids][$size_id];
                    ?>
                    <tr>
                        <td align="center"><?=$k;?></td>
                        <td align="left"><?=$color_library[$row[csf('sample_color')]];?></td>
                        <td align="right"><?=number_format($total_color_qty,2);?></td>
                        
                    </tr>
                    <?
                    $gr_tot_sum+=$total_color_qty;
                   // $gr_sub_sum+=$total_sizes_qty_subm;
                }
                ?>
                <tr>
                    <td colspan="2" align="right"><b>Total</b></td>
                    <td align="right"><b><?=number_format($gr_tot_sum,2);?> </b></td>
                    
                     
                </tr>
            </tbody>
        </table>
        <br>  <br>
            <? 
                    $lib_designation_arr=return_library_array(" select id,custom_designation from lib_designation","id","custom_designation");
                    $user_lib_designation_arr=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
                    $user_lib_name_arr=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
                    
                    $mst_id=$booking_mst_id;//return_field_value("id as mst_id","sample_development_mst","id='$data[1]'","mst_id");
                   // $approve_data_array=sql_select("select b.approved_by, min(b.approved_date) as approved_date from   approval_history b where b.mst_id=$mst_id and b.entry_form=9  group by  b.approved_by order by b.approved_by asc");
                    
                    //$unapprove_data_array=sql_select("select b.id,b.approved_by,b.approved_date,b.un_approved_reason,b.un_approved_date from   approval_history b where b.mst_id=$mst_id and b.entry_form=9  order by b.approved_date,b.approved_by");
					//echo "select b.id,b.approved_by,b.approved_date,b.un_approved_reason,b.un_approved_date from   approval_history b where b.mst_id=$mst_id and b.entry_form=9  order by b.approved_date,b.approved_by";
                    ?>
                      <?
    
	 $desg_name=return_library_array( "select id, custom_designation from lib_designation", "id", "custom_designation"  );
	 $data_array=sql_select("select b.approved_by,b.approved_no, b.approved_date, c.user_full_name,c.designation from  wo_non_ord_samp_booking_mst a , approval_history b, user_passwd c where a.id=b.mst_id and b.approved_by=c.id and b.mst_id=$mst_id and b.entry_form=9 order by b.id asc");
	?>  <br>
       <table  width="100%" class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all" style="margin:5px;">
            <thead>
            <tr style="border:1px solid black;">
                <th colspan="3" style="border:1px solid black;">Approval Status</th>
                </tr>
                <tr style="border:1px solid black;">
                <th width="3%" style="border:1px solid black;">Sl</th>
                <th width="50%" style="border:1px solid black;">Name/Designation</th>
                <th width="27%" style="border:1px solid black;">Approval Date</th>
                <th width="20%" style="border:1px solid black;">Approval No</th>
                </tr>
            </thead>
            <tbody>
            <?
			$i=1;
			foreach($data_array as $row){
			?>
            <tr style="border:1px solid black;">
                <td width="3%" style="border:1px solid black;"><? echo $i;?></td><td width="50%" style="border:1px solid black;"><? echo $row[csf('user_full_name')].'/'.$desg_name[$row[csf('designation')]];?></td><td width="27%" style="border:1px solid black;"><? echo date ("d-m-Y h:i:s",strtotime($row[csf('approved_date')])); //echo change_date_format($row[csf('approved_date')],"dd-mm-yyyy","-");?></td><td width="20%" style="border:1px solid black;"><? echo $row[csf('approved_no')];?></td>
                </tr>
                <?
				$i++;
			}
				?>
            </tbody>
        </table>
        <br>
        </div>

		<br><br> 
        
		<table style="margin-top:10px; font-size:14px" class="rpt_table" width="600" align="left" border="1" cellpadding="0" cellspacing="0" rules="all">
            <thead>
                <tr>
                    <th width="40">Sl</th>
                    <th>Special Instruction</th>
                </tr>
            </thead>
            <tbody>
				<?
                $data_array=sql_select("select id, terms from  wo_booking_terms_condition where entry_form=140 and booking_no='$data[2]'");
                if(count($data_array)>0)
                {
					$l=1;
					foreach( $data_array as $key=>$row )
					{
						?>
						<tr>
                            <td><? echo $l;?> </td>
                            <td style="word-break:break-all"><? echo $row[csf("terms")]; ?> </td>
						</tr>
						<?
						$l++;
					}
                }
                ?>
            </tbody>
        </table>     


    	<table align="left" cellspacing="0" width="810" class="rpt_table" >
        	<tr>
            	<td colspan="6" align="left">
					<?

						$user_id=$_SESSION['logic_erp']['user_id'];
						$user_arr=return_library_array( "select id, USER_NAME from user_passwd where id=$user_id", "id", "USER_NAME");
						$prepared_by = $user_arr[$user_id];
	                      //echo signature_table(134, $data[0], "810px");
					  	echo signature_table(134, $data[0], "1080px",$cbo_template_id,$padding_top = 70,$prepared_by);
                    ?>
                </td>
            </tr>

        </table>
			</div>
   <script type="text/javascript" src="../../js/jquery.js"></script>
   <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
   <script>
		function fnc_generate_Barcodes( valuess, img_id )
		{
			var value = valuess;//$("#barcodeValue").val();
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer ='bmp';// $("input[name=renderer]:checked").val();
			var settings = {
			  output:renderer,
			  bgColor: '#FFFFFF',
			  color: '#000000',
			  barWidth: 1,
			  barHeight: 60,
			  moduleSize:5,
			  posX: 10,
			  posY: 20,
			  addQuietZone: 1
			};
			$("#"+img_id).html('11');
			 value = {code:value, rect: false};
			$("#"+img_id).show().barcode(value, btype, settings);
		}
   </script>
   <script type="text/javascript">
   	fnc_generate_Barcodes('<? echo $barcode_no; ?>','barcode_img_id');
   </script>
	<?
    exit();
}

if($action=="sample_requisition_print11")
{
	extract($_REQUEST);
	 $data=explode('*',$data);
	 $cbo_template_id=$data[3];

	// $cbo_template_id=str_replace("'","",$cbo_template_id);
	// $txt_booking_no=str_replace("'","",$txt_booking_no);
	// $cbo_company_name=str_replace("'","",$cbo_company_name);
	// $update_id=str_replace("'","",$update_id);
	
	$path="../../";
	if(count($data)>3)
	{
		if($data[4]=='../')
		{
			$path=$data[4];
		}
	}
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	//	$supplier_library=return_library_array( "select id,address_1, supplier_name from lib_supplier", "id", "supplier_name"  );
	$sql_supp=sql_select("select id,address_1,supplier_name from lib_supplier where status_active =1 and is_deleted=0");
	foreach ($sql_supp as $row)  
	{
		$supplier_address_library[$row[csf('id')]]=$row[csf('address_1')];
		$supplier_library[$row[csf('id')]]=$row[csf('supplier_name')];
	}
	$company_img=sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$dealing_merchant_library=return_library_array( "select id,team_member_name from lib_mkt_team_member_info", "id", "team_member_name"  );
	$sample_library=return_library_array( "select id, sample_name from lib_sample", "id", "sample_name");
	$size_library=return_library_array( "select id, size_name from lib_size", "id", "size_name"  );
	$color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
	$user_arr=return_library_array( "select id, user_name from user_passwd", "id", "user_name"  );
	$user_library=return_library_array( "select id, user_full_name from user_passwd", "id", "user_full_name"  );
	$season_arr=return_library_array( "select id,season_name from lib_buyer_season  where status_active =1 and is_deleted=0 ", "id", "season_name"  );
	$image_location='';
	$image_location_arr = sql_select("select master_tble_id,image_location from common_photo_library where form_name='sample_requisition_2' and file_type=1 and master_tble_id='$data[1]'");
	foreach ($image_location_arr as $row) {
		$image_locationArr[$row[csf('image_location')]]=$row[csf('image_location')];
	}
	$data_img=sql_select("select image_location,master_tble_id  from common_photo_library  where   form_name='required_fabric_1' and is_deleted=0 and file_type=1");
	$system_img_arr=array();
	foreach($data_img as $row)
	{
	  $system_img_arr[$row[csf('master_tble_id')]]['img']=$row[csf('image_location')];
	}

	$sam_img=sql_select("select image_location,master_tble_id  from common_photo_library  where   form_name='sample_details_1' and is_deleted=0 and file_type=1");
	$sam_img_arr=array();
	foreach($sam_img as $row)
	{
	  $sam_img_arr[$row[csf('master_tble_id')]]['img']=$row[csf('image_location')];
	}
 ?>
 <style>
	#mstDiv {
	    margin:0px auto;
	    width:1200px;

	}
	#mstDiv @media print {

	   thead {display: table-header-group;}

	}

	 @media print{
			html>body table.rpt_table {
			margin-left:12px;
	  		}

		}
	</style>

		

	<div id="mstDiv">

        <table width="1100" cellspacing="0" border="0"   >
            <tr>
                <td rowspan="4" valign="top" width="150"><img width="150" height="80" src="<?=$path?><? echo $company_img[0][csf("image_location")]; ?>"></td>
                <td colspan="5" style="font-size:20px;text-align: center;"><strong><b><? echo $company_library[$data[0]]; ?></b></strong></td>
            </tr>
            <tr>
                <td colspan="5" style="text-align: center;">
					<?
                    $val=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
                    echo ($val[0][csf('block_no')])?  $val[0][csf('block_no')].',': "";
                    echo ($val[0][csf('city')])?      $val[0][csf('city')].',': "";
                    echo($val[0][csf('country_id')])? $country_arr[$val[0][csf('country_id')]]: "";
                    echo ($val[0][csf('email')])?    "</br>". $val[0][csf('email')].',': "</br>";
                    echo($val[0][csf('website')])?    $val[0][csf('website')]: "";

                    $sql="SELECT id, requisition_number, style_desc,requisition_number_prefix_num, style_ref_no, buyer_name, season, product_dept, dealing_marchant, agent_name, buyer_ref, bh_merchant, estimated_shipdate, team_leader, season_buyer_wise, remarks, quotation_id, sample_stage_id, requisition_date, material_delivery_date,req_ready_to_approved, season_year, brand_id, is_acknowledge,refusing_cause,sub_dept_id,inserted_by from sample_development_mst where  id='$data[1]' and entry_form_id=203 and  is_deleted=0  and status_active=1";
                    $dataArray=sql_select($sql);
                    $refusing_cause=$dataArray[0][csf('refusing_cause')]; 
					$style_ref_no=$dataArray[0][csf('style_ref_no')];
					$style_desc=$dataArray[0][csf('style_desc')];
                    $barcode_no=$dataArray[0][csf('requisition_number')];
					$season=$dataArray[0][csf('season')];
					$req_ready_to_approved=$dataArray[0][csf('req_ready_to_approved')];
					$prepared_by=$user_library[$dataArray[0][csf('inserted_by')]];
                    if($dataArray[0][csf("sample_stage_id")]==1)
                    {
                        $job_lib=return_library_array( "SELECT a.id,min(b.shipment_date) as shipment_date  from wo_po_details_master  a,wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and a.company_name='$cbo_company_name' GROUP BY a.id", "id", "shipment_date"  );
                    }
					 $sqls="SELECT id, style_desc, is_approved,supplier_id,attention, revised_no,pay_mode, buyer_req_no,exchange_rate,currency_id, source, team_leader, dealing_marchant, booking_date, attention, remarks from wo_non_ord_samp_booking_mst where  booking_no='$data[2]' and is_deleted=0  and status_active=1";
 					 $dataArray_book=sql_select($sqls);
					  $is_approved=$dataArray_book[0][csf('is_approved')];
					  $booking_mst_id=$dataArray_book[0][csf('id')];

 					 $sample_acc_arr=sql_select("SELECT confirm_del_end_date, refusing_cause, unacknowledge_date, insert_date from sample_requisition_acknowledge where sample_mst_id= '$data[1]'");
					 $sample_delivery="SELECT delivery_date from sample_development_fabric_acc where sample_mst_id= '$data[1]'";
					 $dataSample_delivery=sql_select($sample_delivery);
					 $delivery_date=$dataSample_delivery[0][csf('delivery_date')];
					 //echo "SELECT delivery_date from sample_development_fabric_acc where sample_mst_id= '$data[1]'";
				
 				//	 select id,team_member_name from lib_mkt_team_member_info where team_id='$data' and status_active =1 and is_deleted=0 order by team_member_name
					$msg="";
					if($is_approved==1 || $is_approved==3)
					{
						$msg="Approved";
					}
                    ?>
                </td>
            </tr>
			<tr>
                <td colspan="5" style="font-size:medium; text-align: center;"><strong style="font-size:18px">Sample Fabric Booking -Without order</strong></td>               
            </tr>
			<tr>
				<td align="right"><strong style="color:red;font-size: 25px;margin-right:10%;"><?=$msg;?></strong></td>
            </tr>
        </table>
        <table width="1100" cellspacing="0" border="0" class="rpt_table" style="font-size:14px" >
        	<tr>
        		<td align="left" width="150" >Booking No</td>
        		<td align="left" width="150" ><?=$data[2];//$dataArray[0][csf("requisition_number")];?></td>
        		<td align="left" width="100">Booking Date</td>
        		<td align="left" width="80"><?=$dataArray_book[0][csf('booking_date')];?></td>
        		<td align="left" width="100">Fab. Delivery Date</td>
        		<td width="80"><?=change_date_format($delivery_date); ?></td>
        		 
        	</tr>
             
        	<tr>
        		<td width="150" align="left">Buyer</td>
                <td width="150" align="left"><?=$buyer_library[$dataArray[0][csf('buyer_name')]];?></td>
                <td align="left" width="100">Requisition No</td>
                <td align="left" width="80"> <?=$dataArray[0][csf("requisition_number")];?></td>
                
                <td align="left" width="100">Supplier Name</td>
                <td  align="left" > <? 
				if($dataArray_book[0][csf('pay_mode')]==3 || $dataArray_book[0][csf('pay_mode')]==5)
				{
					echo $company_library[$dataArray_book[0][csf("supplier_id")]];
				}
				else
				{
					echo $supplier_library[$dataArray_book[0][csf("supplier_id")]];
					$supplier_address=$supplier_address_library[$row[csf('supplier_id')]];
				}?></td>
        	</tr>
        	<tr>
        		<td width="150" align="left">Currency</td>
                <td width="150" align="left"><?=$currency[$dataArray_book[0][csf('currency_id')]];?></td>
                <td align="left" width="100">Attention</td>
                <td align="left" width="80"> <?=$dataArray_book[0][csf("attention")];?></td>
                <td align="left" width="100">Supplier Address</td>
                <td  align="left"> <?=$supplier_address;?></td>
        	</tr>
        	<tr>
        		<td width="150" align="left">Pay mode</td>
                <td width="150" align="left"><?=$pay_mode[$dataArray_book[0][csf('pay_mode')]];?></td>
                <td align="left" width="100">Conversion Rate</td>
                <td align="left" width="80"> <?=$dataArray_book[0][csf("exchange_rate")];?></td>
                <td align="left" width="100">Dealing Merchant</td>
                <td  align="left"> <?=$dealing_merchant_library[$dataArray_book[0][csf("dealing_marchant")]];?></td>
        	</tr>
			<tr>
        		<td width="150" align="left"></td>
                <td width="150" align="left"></td>
                <td align="left" width="100">Ready To App </td>
                <td align="left" width="80"><b> <?=$yes_no[$req_ready_to_approved];?></b></td>
                <td align="left" width="100">Season</td>
                <td  align="left"> <?=$season_arr[$season];?></td>
        	</tr>
        	 
        	 
        </table>
        <br>
		<?
         $color_sql="SELECT b.color_id ,b.process_loss_percent ,b.fabric_color,b.contrast,b.mst_id,b.dtls_id, b.qnty from  sample_development_rf_color b where b.status_active=1 and b.is_deleted=0 and b.qnty>0 and b.mst_id='$data[1]' ";
		 $color_res=sql_select($color_sql);
		 $color_rf_data=array();
		 foreach ($color_res as $val) {
		 	$color_rf_data[$val[csf('dtls_id')]][$val[csf('color_id')]][$val[csf('fabric_color')]]['process_loss_percent']=$val[csf('process_loss_percent')];
		 	$color_rf_data[$val[csf('dtls_id')]][$val[csf('color_id')]][$val[csf('fabric_color')]]['contrast']=$val[csf('contrast')];
		 	$color_rf_data[$val[csf('dtls_id')]][$val[csf('color_id')]][$val[csf('fabric_color')]]['qnty']=$val[csf('qnty')];
		 }

		 $sql_fab="SELECT a.id,a.sample_name, a.gmts_item_id, c.gmts_color as color_id,  a.determination_id, a.delivery_date, a.fabric_description, a.body_part_id, a.fabric_source, a.remarks_ra, a.gsm, a.dia, a.color_type_id, a.width_dia_id, a.uom_id,c.rate,c.amount, c.grey_fabric as grey_fab_qnty, c.fabric_color,c.dtls_id,c.finish_fabric as qnty,a.id,a.determination_id from sample_development_fabric_acc a, wo_non_ord_samp_booking_dtls c where  a.id=c.dtls_id  and a.form_type=1  and c.grey_fabric>0 and a.status_active=1 and a.is_deleted=0  and c.status_active=1 and c.is_deleted=0  and a.sample_mst_id='$data[1]' ";
		//echo  $sql_fab;
        $sql_fab_arr=array();
        $dtls_id_arr=array();
        $determination_id_arr=array();

        foreach(sql_select($sql_fab) as $vals)
        {
        	$contrast=$color_rf_data[$vals[csf('dtls_id')]][$vals[csf('color_id')]][$vals[csf('fabric_color')]]['contrast'];
			$garmentItemArr[$sample_library[$vals[csf('sample_name')]]]=$sample_library[$vals[csf('sample_name')]];
			$process_loss_percent=$color_rf_data[$vals[csf('dtls_id')]][$vals[csf('color_id')]][$vals[csf('fabric_color')]]['process_loss_percent'];

            $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$contrast]["qnty"]+=$vals[csf("qnty")];
            $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$contrast]["process_loss_percent"]+=$process_loss_percent;

            $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$contrast]["grey_fab_qnty"]+=$vals[csf("grey_fab_qnty")];
			 $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$contrast]["amount"]+=$vals[csf("amount")];
			  $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$contrast]["rate"]=$vals[csf("rate")];

            $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$contrast]["delivery_date"] =change_date_format($vals[csf("delivery_date")]);
			 $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$contrast]["remarks_ra"] =$vals[csf("remarks_ra")];

			$sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$contrast]["id"] =$vals[csf("id")];

            $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$contrast]["fabric_source"] =$vals[csf("fabric_source")];

            $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$contrast]["uom_id"] =$vals[csf("uom_id")];
            $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$contrast]["dia"] =$vals[csf("dia")];

            $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$contrast]["width_dia_id"] =$vals[csf("width_dia_id")];
			$sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$contrast]["gmts_item_id"] =$vals[csf("gmts_item_id")];

            $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$contrast]["remarks"] =$vals[csf("remarks_ra")];
            $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$contrast]["color_type_id"] =$vals[csf("color_type_id")];

            $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$contrast]["determination_id"] =$vals[csf("determination_id")];
            array_push($dtls_id_arr,$vals[csf('id')]);
            array_push($determination_id_arr,$vals[csf('determination_id')]);
        }
        $sample_item_wise_span=array();
        /*echo '<pre>';
        print_r($sql_fab_arr); die;*/
        $determination_id_cond= where_con_using_array($determination_id_arr,0,"a.id");

        $update_dtls_id_cond= where_con_using_array($dtls_id_arr,0,"a.dtls_id");
        $sql = "SELECT a.sample_color, a.size_id, a.item_size, a.qnty_pcs, b.body_part_id from sample_requisition_coller_cuff a join sample_development_fabric_acc b on a.DTLS_ID=b.id where  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $update_dtls_id_cond";
        //echo $sql; die;
		$collar_cuff_data_arr = sql_select($sql);
		foreach ($collar_cuff_data_arr as $row)
		{
			$sample_color = $row[csf('sample_color')];
			$itemsize = $row[csf('item_size')];
			//$collarCuffarr[$sample_color].=$itemsize."***";
			$collarCuffarr[$sample_color][$row[csf('body_part_id')]][$itemsize]=$itemsize;
		}
		//
		 $sql_d_yr = "SELECT  a.id, b.type_id FROM lib_yarn_count_determina_mst a left join lib_yarn_count_determina_dtls b on a.id = b.mst_id AND b.status_active = 1 AND b.is_deleted = 0 WHERE a.status_active = 1 AND a.is_deleted = 0  $determination_id_cond";
		  $determina_y_arr = sql_select($sql_d_yr);
		 foreach ($determina_y_arr as $row)
		{
			$yarn_construction_data_arr[$row[csf('id')]].=$yarn_type[$row[csf('type_id')]]."***";
		}
		 $sql_d = "SELECT b.fabric_composition_name, a.id, a.construction FROM lib_yarn_count_determina_mst a left join lib_fabric_composition b on a.fabric_composition_id = b.id AND b.status_active = 1 AND b.is_deleted = 0 WHERE a.status_active = 1 AND a.is_deleted = 0  $determination_id_cond";
		// echo $sql_d; //die;
		 $determina_arr = sql_select($sql_d);
		$determina_data_arr=array();
		foreach ($determina_arr as $row)
		{
			$determina_data_arr[$row[csf('id')]].=$row[csf('fabric_composition_name')]."***";
			$construction_data_arr[$row[csf('id')]].=$row[csf('construction')]."***";
		}
		
		$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count"  );
		$sql_yarn="select b.determin_id,b.count_id,b.copm_one_id,b.percent_one,b.type_id,sum(b.cons_qnty) as  cons_qnty from  sample_development_yarn_dtls b where  b.status_active=1  and b.mst_id='$data[1]' and b.determin_id in (select determination_id from sample_development_fabric_acc  where status_active=1 and sample_mst_id='$data[1]' and form_type=1) group by b.determin_id,b.count_id,b.copm_one_id,b.percent_one,b.type_id";
		$sql_yarn_data = sql_select($sql_yarn);
		$yarn_data_arr=array();
		foreach ($sql_yarn_data as $row)
		{
			$yarn_data_arr[$row[csf('determin_id')]].=$lib_yarn_count[$row[csf('count_id')]]."***";
		}
		/* echo "<pre>";
        print_r($yarn_data_arr);die; */
        foreach($sql_fab_arr as $sample_type=>$colorType_data)
        {
            foreach($colorType_data as $colorType=>$gmts_color_data)
            {

                foreach($gmts_color_data as $gmts_color_id=>$body_part_data)
                {
                	$sample_span=0;
                    foreach($body_part_data as $body_part_id=>$fab_desc_data)
                    {
                        //$kk=0;
                        foreach($fab_desc_data as $fab_id=>$gsm_data)
                        {
                            foreach($gsm_data as $gsm_id=>$dia_data)
                            {
                                foreach($dia_data as $dia_id=>$color_data)
                                {
                                    foreach($color_data as $contrast_id=>$row)
                                    {
                                        $sample_span++;
                                        //$kk++;
                                    }
									 $sample_item_wise_span[$sample_type][$gmts_color_id]=$sample_span;
                                }
                            }
                        }
                        //$bodypart_item_wise_span[$sample_type][$gmts_item_id][$body_part_id]=$kk;
                    }
                   
                }
            }
        }
	  /*        echo "<pre>";
        print_r($sample_item_wise_span);die;*/

        $sql_sample_dtls= "SELECT a.sample_name,a.sample_charge, a.article_no, a.sample_color,a.comments from sample_development_dtls a, lib_color b where a.status_active=1 and a.is_deleted=0 and a.entry_form_id=203  and sample_mst_id='$data[1]' and b.status_active=1 and b.id=a.sample_color ";
        foreach(sql_select($sql_sample_dtls) as $key=>$value)
        {
            if($sample_wise_article_no[$value[csf("sample_name")]][$value[csf("sample_color")]]=="")
            {
                $sample_wise_article_no[$value[csf("sample_name")]][$value[csf("sample_color")]]=$value[csf("article_no")];
            }
            else
            {
                if(!in_array($value[csf("article_no")], $sample_wise_article_no))
                {
                    $sample_wise_article_no[$value[csf("sample_name")]][$value[csf("sample_color")]].= ', '.$value[csf("article_no")];
                }
            }
			$sample_wise_rate_arr[$value[csf("sample_name")]][$value[csf("sample_color")]]=$value[csf("sample_charge")];
			$sample_wise_cmnt_arr[$value[csf("sample_name")]][$value[csf("sample_color")]]=$value[csf("comments")];
        }

        // echo "<pre>"; print_r($sample_wise_cmnt_arr);die;

        ?>
        <table class="rpt_table" width="1300"  border="1" cellpadding="0" cellspacing="0" rules="all" style="margin-top:5px; font-size:14px">
            <thead>
                <tr>
                    <th colspan="20">Required Fabric</th>
                </tr>
                <tr>
                    <th width="20">Sl</th>
                    <th width="60">Style Ref.</th>
                    <th width="60">Style No.</th>
                    <th width="60">Sample</th>
					<th width="60">Garments Item</th>
                    <th width="60">Body Part</th>
                    <th width="50">Color Type</th>
                    <th width="60">Construction</th>
                    <th width="60">Composition</th>
					<th width="60">Y. Count</th>
                    <th width="40">GSM</th>
                    <th width="70">Gmts Color</th>
                    <th width="70">Fabric Color</th>
                    <th width="40">Dia/ Width</th>
                    <th width="70">G. Qty</th>
                    <th width="50">Process Loss</th>
                    <th width="70">Fin Fab Qnty</th>
					<th width="70">Fabric Del. Date</th>
                    <th width="40">UOM</th>
                    <th width="60">Fab. Source</th>
                    <th width="">Remarks</th>
					
					
                    
                </tr>
            </thead>
            <tbody>
                <?
                function str_replace_first($search, $replace, $subject)
				{
				    $search = '/'.preg_quote($search, '/').'/';
				    return preg_replace($search, $replace, $subject, 1);
				}
                $p=1; $total_finish=0; $total_grey=0; $total_process=$total_amount=0;
                foreach($sql_fab_arr as $sample_type=>$colorType_data)
                {
                    foreach($colorType_data as $colorType=>$gmts_color_data)
                    {
                        foreach($gmts_color_data as $gmts_color_id=>$body_part_data)
                        {
                            $nn=0;
                            foreach($body_part_data as $body_part_id=>$fab_desc_data)
                            {
                                foreach($fab_desc_data as $fab_id=>$gsm_data)
                                {
                                    foreach($gsm_data as $gsm_id=>$dia_data)
                                    {
                                        foreach($dia_data as $dia_id=>$color_data)
                                        {
                                            //$i=0;
                                            foreach($color_data as $contrast_id=>$value)
                                            {
                                            	$constr=implode(",", array_unique(explode("***", chop($construction_data_arr[$value['determination_id']],"***"))));
												$yarn_type_id=implode(",", array_unique(explode("***", chop($yarn_data_arr[$value['determination_id']],"***"))));
                                                ?>
                                                <tr>
                                                    <td  align="center" style="word-wrap: break-word;word-break: break-all;"><? echo $p;$p++;?></td>
                                                    <?
													 
                                                   /* if($nn==0)
                                                    {*/
                                                        $rowspan=0;
                                                        //$rowspan=$sample_item_wise_span[$sample_type][$gmts_color_id];
														$comment_style_no= $sample_wise_cmnt_arr[$sample_type][$gmts_color_id];
                                                        ?>
                                                       
                                                        <td rowspan="<?=$rowspan;?>" align="center"><?=$style_ref_no;?></td>
                                                        <td align="center"><?=$comment_style_no;?> </td>
                                                        <td rowspan="<?=$rowspan;?>" align="center"><?=$sample_library[$sample_type];?></td>
														<td rowspan="<?=$rowspan;?>" align="center"><?=$garments_item[$value["gmts_item_id"]];?></td>
                                                       
                                                        <?
                                                        $nn++;
                                                    /*}*/
                                                 //  echo $contrast_id.'DDDDDDDS';
												 $sample_wise_rate= $sample_wise_rate_arr[$sample_type][$gmts_color_id];
                                                    ?>
                                                   
                                                    <td align="center" style="word-break:break-all"><?=$body_part[$body_part_id];?></td>
                                                     <td align="center" style="word-break:break-all"><?=$color_type[$colorType];?></td>
                                                    <td align="center" style="word-break:break-all"><?=$constr;?></td>
                                                    <td align="center" style="word-break:break-all"><?=str_replace_first(trim($constr), "", $fab_id);//implode(" , ", array_unique(explode("***", chop($determina_data_arr[$value['determination_id']],"***"))));// echo $fab_id;?></td>
                                                   
                                                    <td align="center" style="word-break:break-all"><?=$yarn_type_id;//$contrast_id;?></td>
                                                     <td align="center" style="word-break:break-all"><?=$gsm_id;?></td>
                                                    <td align="center" style="word-break:break-all"><? echo $color_library[$gmts_color_id];//implode(", ", $collarCuffarr[$gmts_color_id][$body_part_id]);?></td>
                                                   
                                                    <td align="center" style="word-break:break-all"><?=$contrast_id;//$value["dia"];?></td>
                                                    <td align="center" style="word-break:break-all"><?=$fabric_typee[$value["width_dia_id"]];?></td>
                                                     <td align="right"><?=number_format($value["grey_fab_qnty"], 2);?></td>
                                                     <td align="right"><?=$value["process_loss_percent"];?></td>
                                                    <td align="right"><?=number_format($value["qnty"],2);?></td>
                                                    <td align="right"><?=change_date_format($value["delivery_date"]);?></td>
                                                    <td align="center" style="word-break:break-all"><?=$unit_of_measurement[$value["uom_id"]];?></td>
                                                     <td align="center"><? echo $fabric_source[$value["fabric_source"]];?></td>
                                                    
                                                    <td style="word-break:break-all"><?=$value["remarks"];?></td>
													 
                                                </tr>
                                                <?
                                                //$i++;
                                                $total_finish +=$value["qnty"];
                                                $total_grey +=$value["grey_fab_qnty"];
											//	$total_amount +=$sample_wise_rate*$value["amount"];
                                               // $total_process +=$value["process_loss_percent"];
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                ?>
                <tr>
                    <th colspan="13" align="right"><b>Total</b></th>
                    <th align="right"></th>
                    <th align="right"><?=number_format($total_grey, 2);?></th>
                    <th align="right"></th>
                    <th align="right"><?=number_format($total_finish, 2);?></th>
                    <th colspan="4">&nbsp;</th>
                     
                </tr>
            </tbody>
        </table>
        <br/>
      
        <br>&nbsp;
		<?
        $sample_color_arr=return_library_array( "select id, sample_color from sample_development_dtls", "id", "sample_color");
        $sql_qry="SELECT id, sample_mst_id, sample_name, gmts_item_id, smv, article_no, sample_color, sample_prod_qty, submission_qty, delv_start_date, delv_end_date, sample_charge, sample_curency, sent_to_buyer_date, comments from sample_development_dtls where status_active=1 and is_deleted=0 and entry_form_id=203 and sample_mst_id='$data[1]' order by id asc";

        $sql_qry_color="SELECT a.id,a.sample_mst_id,a.sample_name,a.gmts_item_id,a.smv,a.article_no,a.sample_color,a.sample_prod_qty,a.submission_qty,a.delv_start_date,a.delv_end_date,a.sample_charge,a.sample_curency,a.sent_to_buyer_date,a.comments,c.dtls_id,c.size_id,c.bh_qty,c.self_qty,c.test_qty,c.plan_qty,c.dyeing_qty,c.others_qty,c.test_fit_qty,c.samp_dept_qty from sample_development_dtls a,sample_development_size c where a.id=c.dtls_id and  a.status_active =1 and a.is_deleted=0 and a.entry_form_id=203 and a.sample_mst_id='$data[1]' order by a.id asc";
		$size_type_arr=array(1=>"BH Qty",2=>"Self Qty",3=>"Test qty",4=>"Plan Qty",5=>"Dyeing Qty",6=>"Test Fit",7=>"Samp. Dept",8=>"Others");
        $color_size_arr=array();
        foreach(sql_select($sql_qry_color) as $vals)
        {
            if($vals[csf("bh_qty")]>0)
            {
                $color_size_arr[1][$vals[csf("size_id")]]='Bh Qty';
                $bh_qty=$vals[csf("bh_qty")];
                $color_size_dtls_qty_arr[1][$vals[csf("id")]][$vals[csf("size_id")]]=$bh_qty;
            }
            if($vals[csf("self_qty")]>0)
            {
                $color_size_arr[2][$vals[csf("size_id")]]='self qty';
                $color_size_dtls_qty_arr[2][$vals[csf("id")]][$vals[csf("size_id")]]=$vals[csf("self_qty")];
            }
            if($vals[csf("test_qty")]>0)
            {
                $color_size_arr[3][$vals[csf("size_id")]]='test qty';
                $color_size_dtls_qty_arr[3][$vals[csf("id")]][$vals[csf("size_id")]]=$vals[csf("test_qty")];
            }
            if($vals[csf("plan_qty")]>0)
            {
                $color_size_arr[4][$vals[csf("size_id")]]='plan qty';
                $color_size_dtls_qty_arr[4][$vals[csf("id")]][$vals[csf("size_id")]]=$vals[csf("plan_qty")];
            }
            if($vals[csf("dyeing_qty")]>0)
            {
                $color_size_arr[5][$vals[csf("size_id")]]='Dyeing qty';
                $color_size_dtls_qty_arr[5][$vals[csf("id")]][$vals[csf("size_id")]]=$vals[csf("dyeing_qty")];
            }
			if($vals[csf("test_fit_qty")]>0)
			{
			$color_size_arr[6][$vals[csf("size_id")]]='Test Fit';
			$color_size_dtls_qty_arr[6][$vals[csf("id")]][$vals[csf("size_id")]]=$vals[csf("test_fit_qty")];

			}
			if($vals[csf("samp_dept_qty")]>0)
			{
			$color_size_arr[7][$vals[csf("size_id")]]='Samp. Dept';
			$color_size_dtls_qty_arr[7][$vals[csf("id")]][$vals[csf("size_id")]]=$vals[csf("samp_dept_qty")];

			}
			if($vals[csf("others_qty")]>0)
			{
			$color_size_arr[8][$vals[csf("size_id")]]='Others';
			$color_size_dtls_qty_arr[8][$vals[csf("id")]][$vals[csf("size_id")]]=$vals[csf("others_qty")];

			}
        }
        $tot_row=count($color_size_arr);
        $result=sql_select($sql_qry);
		$head_tot_row_td=0;
		foreach($color_size_arr as $type_id=>$data_size)
		{
			foreach($data_size as $size_id=>$data_val)
			{
				$head_tot_row_td++;
			}
		}
        ?>
        <table align="left" cellspacing="0" border="1" width="900" class="rpt_table" rules="all" style="margin-top: 5px; font-size:14px">
            <thead>
                <tr>
                    <td width="150" colspan="<? echo 6+$head_tot_row_td;?>" align="center"><strong>Sample Details</strong></td>
                </tr>
                <tr>
                    <th width="30" rowspan="2">Sl</th>
                    <th width="100" rowspan="2">Sample Type</th>
					<!-- <th width="100" rowspan="2">Style no</th> -->
					<th width="70" rowspan="2">Color</th>
                    <th width="70" rowspan="2">Dev. Start Date</th>
                    <th width="70" rowspan="2">Dev. End Date</th>
                        <?
                        $tot_row_td=0;
                        foreach($color_size_arr as $type_id=>$val)
                        {
                            ?>
                            <th width="45" align="center" colspan="<?=count($val);?>"><?=$size_type_arr[$type_id];?></th>
                            <?
                        }
                        ?>
                    <th rowspan="4" width="155">Total</th>
                </tr>
                <tr>
                    <?
					$tot_row_td=0;
                    foreach($color_size_arr as $type_id=>$data_size)
                    {
                        foreach($data_size as $size_id=>$data_val)
                        {
                            $tot_row_td++;
                            ?>
                            <th width="40" align="center"><?=$size_library[$size_id];?></th>
                            <?
                        }
                    }
                    ?>
                </tr>
            </thead>
            <tbody>
                <?
                $i=1; $k=0; $gr_tot_sum=0; $gr_sub_sum=0;
                foreach($result as $row)
                {
                    $dtls_ids=$row[csf('id')];
                    $prod_sum=$prod_sum+$row[csf('sample_prod_qty')];
                    $sub_sum=$sub_sum+$row[csf('submission_qty')];
					$style_no_cmnts=$row[csf('comments')];
                    $k++;
                    ?>
                    <tr>
                        <td align="center"><?=$k;?></td>
                        <td align="left"><?=$sample_library[$row[csf('sample_name')]];?></td>
						<!-- <td align="left"><?//=$style_no_cmnts;?></td> -->
						<td align="left"><?=$color_library[$row[csf('sample_color')]];?></td>
                        <td align="left"><?=change_date_format($row[csf('delv_start_date')]);?></td>
						<td align="left"><?=change_date_format($row[csf('delv_end_date')]);?></td>
                        <?
                        $total_sizes_qty=0;  $total_sizes_qty_subm=0;
                        foreach($color_size_arr as $type_id=>$data_size)
                        {
                            foreach($data_size as $size_id=>$data_val)
                            {
                                $size_qty=$color_size_dtls_qty_arr[$type_id][$dtls_ids][$size_id];
                                ?>
                                <td align="right"><?=$size_qty;?></td>
                                <?
                                if($type_id==1)
                                {
                                $total_sizes_qty_subm+=$size_qty;
                                }
                                $total_sizes_qty+=$size_qty;
                            }
                        }
                        ?>
                        <td align="right"><?=number_format($total_sizes_qty,2);?></td>
                    </tr>
                    <?
                    $gr_tot_sum+=$total_sizes_qty;
                    $gr_sub_sum+=$total_sizes_qty_subm;
                }
                ?>
                <tr>
                    <td colspan="<?=5+$tot_row_td;?>" align="right"><b>Total</b></td>
                    <td align="right"><b><?=number_format($gr_tot_sum,2);?> </b></td>
                </tr>
            </tbody>
        </table>
        <br> <br> <br>&nbsp;

        <?
        	$sample_stripe_data=sql_select("SELECT a.body_part_id, b.contrast, b.color_id, b.grey_fab_qnty,c.id as strip_mst_id, c.stripe_color, c.measurement, c.uom, c.fabreq, c.yarn_dyed,c.totfidder FROM sample_development_fabric_acc a join sample_development_rf_color b on a.id=b.dtls_id join wo_sample_stripe_color c on a.id=c.sample_fab_dtls_id and b.color_id=c.color_number_id where a.status_active=1 and a.is_deleted=0 and a.color_type_id in (2,3,4,6,31,32,33,34) and a.form_type=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.sample_mst_id=$data[1]");
        	
        	foreach ($sample_stripe_data as $row) {
        		$key=$row[csf('body_part_id')].'*'.$row[csf('color_id')];
        		$sample_stripe_arr[$key]['body_part_id'] = $row[csf('body_part_id')];
        		$sample_stripe_arr[$key]['fabric_color'] = $row[csf('color_id')];
        		$sample_stripe_arr[$key]['fabric_qty'] = $row[csf('grey_fab_qnty')];
				
        		$sample_stripe_arr[$key]['stripe_color'][$row[csf('strip_mst_id')]][$row[csf('stripe_color')]]['color'] = $row[csf('stripe_color')];
        		$sample_stripe_arr[$key]['stripe_color'][$row[csf('strip_mst_id')]][$row[csf('stripe_color')]]['measurement'] = $row[csf('measurement')];
        		$sample_stripe_arr[$key]['stripe_color'][$row[csf('strip_mst_id')]][$row[csf('stripe_color')]]['uom'] = $row[csf('uom')];
        		$sample_stripe_arr[$key]['stripe_color'][$row[csf('strip_mst_id')]][$row[csf('stripe_color')]]['qty'] = $row[csf('fabreq')];
        		$sample_stripe_arr[$key]['stripe_color'][$row[csf('strip_mst_id')]][$row[csf('stripe_color')]]['yarn_dyed'] = $row[csf('yarn_dyed')];
				$sample_stripe_arr[$key]['stripe_color'][$row[csf('strip_mst_id')]][$row[csf('stripe_color')]]['totfidder'] = $row[csf('totfidder')];
        		
				$stripe_color_summ[$row[csf('stripe_color')]] += $row[csf('fabreq')];
        	}
        ?>
     
        <?
			$coller_cuff_data=sql_select("SELECT a.sample_color, a.size_id, a.item_size, a.qnty_pcs, c.body_part_type from sample_requisition_coller_cuff a join sample_development_fabric_acc b on b.id=a.dtls_id join lib_body_part c on b.body_part_id=c.id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.sample_mst_id=$data[1]");
			//echo "SELECT a.sample_color, a.size_id, a.item_size, a.qnty_pcs, c.body_part_type from sample_requisition_coller_cuff a join sample_development_fabric_acc b on b.id=a.dtls_id join lib_body_part c on b.body_part_id=c.id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.sample_mst_id=$data[1]";
			
			 
			$coller_data_arr=array(); $cuff_data_arr=array();
			foreach ($coller_cuff_data as $row) {
				if($row[csf('body_part_type')]==40)
				{
					$coller_size_arr[$row[csf('size_id')]]=$row[csf('size_id')];
					$color_size_data[$row[csf('size_id')]]['item_size']=$row[csf('item_size')];
					$color_color_data[$row[csf('sample_color')]][$row[csf('size_id')]]['qnty_pcs']=$row[csf('qnty_pcs')];
				}
				if($row[csf('body_part_type')]==50)
				{
					$cuff_size_arr[$row[csf('size_id')]]=$row[csf('size_id')];
					$cuff_size_data[$row[csf('size_id')]]['item_size']=$row[csf('item_size')];
					$cuff_color_data[$row[csf('sample_color')]][$row[csf('size_id')]]['qnty_pcs']=$row[csf('qnty_pcs')];
				}
			} 
			/*echo '<pre>';
			print_r($color_color_data); die;*/
        ?>
        <div style="width:1000px; margin-top: 10px;">
            <?
            $collar_cuff_percent_arr=array(); $collar_cuff_body_arr=array(); $collar_cuff_color_arr=array(); $collar_cuff_size_arr=array(); $collar_cuff_item_size_arr=array(); $color_size_sensitive_arr=array();

			$collar_cuff_sql="select b.id, b.gmts_item_id as item_number_id, a.qnty_pcs,a.sample_color as color_number_id, a.size_id as gmts_sizes, a.item_size, a.size_id as size_number_id,  e.body_part_full_name, e.body_part_type
			FROM sample_requisition_coller_cuff a left join lib_size s on a.size_id=s.id, sample_development_fabric_acc b, lib_body_part  e

			WHERE b.id=a.dtls_id   and b.body_part_id=e.id and e.body_part_type in (40,50)  and b.sample_mst_id=$data[1] and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and e.status_active=1 and e.is_deleted=0 order by  b.id,a.sample_color,s.sequence";
			//echo $collar_cuff_sql;
			$collar_cuff_sql_res=sql_select($collar_cuff_sql);
			$itemIdArr=array();

			foreach($collar_cuff_sql_res as $collar_cuff_row) 
			{
				$collar_cuff_percent_arr[$collar_cuff_row[csf('body_part_type')]][$collar_cuff_row[csf('body_part_full_name')]][$collar_cuff_row[csf('color_number_id')]][$collar_cuff_row[csf('gmts_sizes')]]=$collar_cuff_row[csf('colar_cuff_per')];
				$collar_cuff_body_arr[$collar_cuff_row[csf('body_part_type')]][$collar_cuff_row[csf('body_part_full_name')]]=$collar_cuff_row[csf('body_part_full_name')];
				$collar_cuff_size_arr[$collar_cuff_row[csf('body_part_type')]][$collar_cuff_row[csf('body_part_full_name')]][$collar_cuff_row[csf('size_number_id')]]=$collar_cuff_row[csf('size_number_id')];
				if(!empty($collar_cuff_row[csf('item_size')]))
				{
					$collar_cuff_item_size_arr[$collar_cuff_row[csf('body_part_full_name')]][$collar_cuff_row[csf('size_number_id')]][$collar_cuff_row[csf('item_size')]]=$collar_cuff_row[csf('item_size')];
				}
				
				$color_size_sensitive_arr[$collar_cuff_row[csf('body_part_full_name')]][$collar_cuff_row[csf('id')]][$collar_cuff_row[csf('color_number_id')]]=$collar_cuff_row[csf('qnty_pcs')];
				// $collar_cuff_size_Qty_arr[$collar_cuff_row[csf('body_part_type')]][$collar_cuff_row[csf('body_part_full_name')]][$collar_cuff_row[csf('id')]][$collar_cuff_row[csf('size_number_id')]]=$collar_cuff_row[csf('qnty_pcs')];

				$collar_cuff_size_Qty_arr[$collar_cuff_row[csf('body_part_type')]][$collar_cuff_row[csf('body_part_full_name')]][$collar_cuff_row[csf('id')]][$collar_cuff_row[csf('color_number_id')]][$collar_cuff_row[csf('size_number_id')]]=$collar_cuff_row[csf('qnty_pcs')];
				
				$itemIdArr[$collar_cuff_row[csf('body_part_type')]].=$collar_cuff_row[csf('item_number_id')].',';
			}
			unset($collar_cuff_sql_res);
			foreach($collar_cuff_body_arr as $body_type=>$body_name)
			{
				$gmtsItemId=array_filter(array_unique(explode(",",$itemIdArr[$body_type])));
				foreach($body_name as $body_val)
				{
					$count_collar_cuff=count($collar_cuff_size_arr[$body_type][$body_val]);
					$pre_grand_tot_collar=0; $pre_grand_tot_collar_order_qty=0;

					?>
                    <div style="max-height:1330px; overflow:auto; float:left; padding-top:5px; margin-left:5px; margin-bottom:5px; position:relative;font-size:18px;">
					<table width="625" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                        <tr>
                        	<td colspan="<? echo $count_collar_cuff+3; ?>" align="center"><b><? echo $body_val; ?> - Color Size Brakedown in Pcs.</b></td>
                        </tr>
                        <tr>
                            <td width="100">Size</td>
								<?
                                foreach($collar_cuff_size_arr[$body_type][$body_val]  as $size_number_id)
                                {
									?>
									<td align="center" style="border:1px solid black"><strong><? echo $size_library[$size_number_id];?></strong></td>
									<?
                                }
                                ?>
                            <td width="60" rowspan="2" align="center"><strong>Total</strong></td>
                            
                        </tr>
                        <tr>
                            <td style="font-size:12px"><? echo $body_val; ?> Size</td>
                            <?
                            foreach($collar_cuff_item_size_arr[$body_val]  as $size_number_id=>$size_number)
                            {
								if(count($size_number)>0)
								{
									 foreach($size_number  as $item_size=>$val)
									 {
										?>
										<td align="center" style="border:1px solid black"><strong><? echo $item_size;?></strong></td>
										<?
									 }
								}
								else
								{
									?>
									<td align="center" style="border:1px solid black"><strong> &nbsp;</strong></td>
									<?
								}
                            }
                            ?>
                        </tr>
                            <?

                            $pre_size_total_arr=array();
                            foreach($color_size_sensitive_arr[$body_val] as $fab_req_id=>$pre_cost_data)
                            {
								foreach($pre_cost_data as $color_number_id=>$color_number_data)
								{
									//foreach($color_number_data as $color_size_sensitive=>$color_break_down)
									//{
										$pre_color_total_collar=0;
										$pre_color_total_collar_order_qnty=0;
										 
										?>
										<tr>
											<td>
												<?
                                               
												 echo $color_library[$color_number_id];
                                                ?>
											</td>
											<?
											foreach($collar_cuff_size_arr[$body_type][$body_val] as $size_number_id)
											{
												?>
												<td align="center" style="border:1px solid black">
													<?   $collerqty=0;  
													$color_cuff_cut=0;
													// $color_cuff_cut=$collar_cuff_size_Qty_arr[$body_type][$body_val][$fab_req_id][$size_number_id];
													$color_cuff_cut=$collar_cuff_size_Qty_arr[$body_type][$body_val][$fab_req_id][$color_number_id][$size_number_id];
                                                	if($body_type==50){
														// $collerqty=$color_cuff_cut*2;
														$collerqty=$color_cuff_cut;
													}else{
														$collerqty=$color_cuff_cut;
													}
                                                    echo number_format($collerqty);
                                                    $pre_size_total_arr[$size_number_id]+=$collerqty;
                                                    $pre_color_total_collar+=$collerqty;
                                                    $pre_color_total_collar_order_qnty+=$color_cuff_cut;
                                                    ?>
												</td>
												<?
											}
											?>

											<td align="center"><? echo number_format($pre_color_total_collar); ?></td>
											 
												 
										</tr>
										<?
										$pre_grand_collar_ex_per+=$collar_ex_per;
										$pre_grand_tot_collar+=$pre_color_total_collar;
										$pre_grand_tot_collar_order_qty+=$pre_color_total_collar_order_qnty;
									
								}
							}
							?>
                        
                        <tr>
                            <td>Size Total</td>
								<?
                               // foreach($pre_size_total_arr  as $size_qty)
                               // {
                                	foreach($collar_cuff_size_arr[$body_type][$body_val] as $size_number_id)
									{
										$size_qty=$pre_size_total_arr[$size_number_id];
										?>
										<td style="border:1px solid black;  text-align:center"><? echo number_format($size_qty); ?></td>
										<?
									}

                               // }
                                ?>
                            <td style="border:1px solid black; text-align:center"><? echo number_format($pre_grand_tot_collar); ?></td>
                            <!-- <td align="center" style="border:1px solid black"><? echo number_format((($pre_grand_tot_collar-$pre_grand_tot_collar_order_qty)/$pre_grand_tot_collar_order_qty)*100,2); ?></td> -->
							 
                        </tr>
					</table>
                </div>
                <?
            }
        }
			?>
            <br>
             
        <br>  <br>
            <? 
                    $lib_designation_arr=return_library_array(" select id,custom_designation from lib_designation","id","custom_designation");
                    $user_lib_designation_arr=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
                    $user_lib_name_arr=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
                    
                    $mst_id=$booking_mst_id;//return_field_value("id as mst_id","sample_development_mst","id='$data[1]'","mst_id");
                   // $approve_data_array=sql_select("select b.approved_by, min(b.approved_date) as approved_date from   approval_history b where b.mst_id=$mst_id and b.entry_form=9  group by  b.approved_by order by b.approved_by asc");
                    
                    //$unapprove_data_array=sql_select("select b.id,b.approved_by,b.approved_date,b.un_approved_reason,b.un_approved_date from   approval_history b where b.mst_id=$mst_id and b.entry_form=9  order by b.approved_date,b.approved_by");
					//echo "select b.id,b.approved_by,b.approved_date,b.un_approved_reason,b.un_approved_date from   approval_history b where b.mst_id=$mst_id and b.entry_form=9  order by b.approved_date,b.approved_by";
                    ?>
                      <?
    
	 $desg_name=return_library_array( "select id, custom_designation from lib_designation", "id", "custom_designation"  );
	 $data_array=sql_select("select b.approved_by,b.approved_no, b.approved_date, c.user_full_name,c.designation from  wo_non_ord_samp_booking_mst a , approval_history b, user_passwd c where a.id=b.mst_id and b.approved_by=c.id and b.mst_id=$mst_id and b.entry_form=9 order by b.id asc");
	?>  <br>
       <table  width="100%" class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all" style="margin:5px;">
            <thead>
            <tr style="border:1px solid black;">
                <th colspan="3" style="border:1px solid black;">Approval Status</th>
                </tr>
                <tr style="border:1px solid black;">
                <th width="3%" style="border:1px solid black;">Sl</th>
                <th width="50%" style="border:1px solid black;">Name/Designation</th>
                <th width="27%" style="border:1px solid black;">Approval Date</th>
                <th width="20%" style="border:1px solid black;">Approval No</th>
                </tr>
            </thead>
            <tbody>
            <?
			$i=1;
			foreach($data_array as $row){
			?>
            <tr style="border:1px solid black;">
                <td width="3%" style="border:1px solid black;"><? echo $i;?></td><td width="50%" style="border:1px solid black;"><? echo $row[csf('user_full_name')].'/'.$desg_name[$row[csf('designation')]];?></td><td width="27%" style="border:1px solid black;"><? echo date ("d-m-Y h:i:s",strtotime($row[csf('approved_date')])); //echo change_date_format($row[csf('approved_date')],"dd-mm-yyyy","-");?></td><td width="20%" style="border:1px solid black;"><? echo $row[csf('approved_no')];?></td>
                </tr>
                <?
				$i++;
			}
				?>
            </tbody>
        </table>
        <br>
        </div>

		<br><br> 
        
		<table style="margin-top:10px; font-size:14px" class="rpt_table" width="600" align="left" border="1" cellpadding="0" cellspacing="0" rules="all">
            <thead>
                <tr>
                    <th width="40">Sl</th>
                    <th>Special Instruction</th>
                </tr>
            </thead>
            <tbody>
				<?
                $data_array=sql_select("select id, terms from  wo_booking_terms_condition where entry_form=140 and booking_no='$data[2]'");
                if(count($data_array)>0)
                {
					$l=1;
					foreach( $data_array as $key=>$row )
					{
						?>
						<tr>
                            <td><? echo $l;?> </td>
                            <td style="word-break:break-all"><? echo $row[csf("terms")]; ?> </td>
						</tr>
						<?
						$l++;
					}
                }
                ?>
            </tbody>
        </table> 
		<table class="rpt_table" width="1300"  border="1" cellpadding="0" cellspacing="0" rules="all" style="margin-top:5px; font-size:14px">
		<br><br>
		<tr>
			<td>
                <?
				$sql_image=sql_select("select image_location from common_photo_library where master_tble_id='$data[1]' and form_name='sample_requisition_2' and file_type=1");
				$img_counter = 0;
                foreach($sql_image as $result_imge)
				{					
					?>
                        <img src="<? echo base_url($result_imge[csf('image_location')])." "; ?>" width="200" height="150" border="2" />	
					<?
					$img_counter++;
				}
				?>
                </td>
		</tr> 
		</table>  


    	<table align="left" cellspacing="0" width="810" class="rpt_table" >
        	<tr>
            	<td colspan="6" align="left">
					<?

						$user_id=$_SESSION['logic_erp']['user_id'];
						$user_arr=return_library_array( "select id, USER_NAME from user_passwd where id=$user_id", "id", "USER_NAME");
						$prepared_by = $user_arr[$user_id];
	                      //echo signature_table(134, $data[0], "810px");
					  	echo signature_table(134, $data[0], "1080px",$cbo_template_id,$padding_top = 70,$prepared_by);
                    ?>
                </td>
            </tr>

        </table>
			</div>
   <script type="text/javascript" src="../../js/jquery.js"></script>
   <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
   <script>
		function fnc_generate_Barcodes( valuess, img_id )
		{
			var value = valuess;//$("#barcodeValue").val();
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer ='bmp';// $("input[name=renderer]:checked").val();
			var settings = {
			  output:renderer,
			  bgColor: '#FFFFFF',
			  color: '#000000',
			  barWidth: 1,
			  barHeight: 60,
			  moduleSize:5,
			  posX: 10,
			  posY: 20,
			  addQuietZone: 1
			};
			$("#"+img_id).html('11');
			 value = {code:value, rect: false};
			$("#"+img_id).show().barcode(value, btype, settings);
		}
   </script>
   <script type="text/javascript">
   	fnc_generate_Barcodes('<? echo $barcode_no; ?>','barcode_img_id');
   </script>
	<?
    exit();
}
if ($action=="sizeinfo_popup")
{
	echo load_html_head_contents("Sample Requisition Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	//echo $update_id_dtl.'SAD';;
		?>

    <script>
		var permission='<? echo $permission; ?>';
		var str_size = [<? echo substr(return_library_autocomplete( "select size_name from lib_size group by size_name order by size_name", "size_name" ), 0, -1); ?> ];

		function total_submission_qty(inc)
		{
			// this function will calculate the sum of the BH Qty,Plan Qty,Dyeing,Test,Self Qty in Sizeinfo popup related with Sample Details Module for every row
			var tot_row=$('#size_tbl tbody tr').length;
			 var total="";
			for(var i=1; i<=tot_row; i++)
			{
				var bhQty=$("#txtbhqty_"+i).val()*1;
				var plQty=$("#txtplqty_"+i).val()*1;
				var dyQty=$("#txtdyqty_"+i).val()*1;
				var testQty=$("#txttestqty_"+i).val()*1;
				var selfQty=$("#txtselfqty_"+i).val()*1;
				var samp_deptQty=$("#txtsampdeptqty_"+i).val()*1;
				var testfitQty=$("#txttestfitqty_"+i).val()*1;
				var otherQty=$("#txtothersqty_"+i).val()*1;
				var total=bhQty + plQty + dyQty + testQty + selfQty + samp_deptQty + testfitQty + otherQty;
				$("#txttotalqty_"+i).val(total);
			}
		}

		function calculate_total_qnty_by_type()
		{
			var tot_row=$('#size_tbl tbody tr').length;
			var total_bhqnty=""; var total_plqnty=""; var total_dyqnty=""; var total_testqnty=""; var total_selfqnty="";var total_samp_deptqnty="";var total_testfitqnty="";var total_otherqnty=""; var total_all_qnty=""; var total='';
			for(var i=1; i<=tot_row; i++)
			{
				var bhQty=$("#txtbhqty_"+i).val()*1;
				
				var plQty=$("#txtplqty_"+i).val()*1;
				var dyQty=$("#txtdyqty_"+i).val()*1;
				var testQty=$("#txttestqty_"+i).val()*1;
				var selfQty=$("#txtselfqty_"+i).val()*1;
				var samp_deptQty=$("#txtsampdeptqty_"+i).val()*1;
				var testfitQty=$("#txttestfitqty_"+i).val()*1;
				var otherQty=$("#txtothersqty_"+i).val()*1;
				var total=bhQty+plQty + dyQty + testQty + selfQty + samp_deptQty + testfitQty + otherQty;
				$("#txttotalqty_"+i).val(total);

				var bh_qty=$("#txtbhqty_"+i).val()*1;
				total_bhqnty=total_bhqnty*1+$("#txtbhqty_"+i).val()*1;

				var pl_qty=$("#txtplqty_"+i).val()*1;
				total_plqnty=total_plqnty*1+$("#txtplqty_"+i).val()*1;

				var dy_qty=$("#txtdyqty_"+i).val()*1;
				total_dyqnty=total_dyqnty*1+$("#txtdyqty_"+i).val()*1;

				var test_qty=$("#txttestqty_"+i).val()*1;
				total_testqnty=total_testqnty*1+$("#txttestqty_"+i).val()*1;

				var self_qty=$("#txtselfqty_"+i).val()*1;
				total_selfqnty=total_selfqnty*1+$("#txtselfqty_"+i).val()*1;

				var samp_dept_qty=$("#txtsampdeptqty_"+i).val()*1;
				total_samp_deptqnty=total_samp_deptqnty*1+$("#txtsampdeptqty_"+i).val()*1;

				var test_fit_qty=$("#txttestfitqty_"+i).val()*1;
				total_testfitqnty=total_testfitqnty*1+$("#txttestfitqty_"+i).val()*1;

				var others_qty=$("#txtothersqty_"+i).val()*1;
				total_otherqnty=total_otherqnty*1+$("#txtothersqty_"+i).val()*1;

				var total_qty=$("#txttotalqty_"+i).val()*1;
				total_all_qnty=total_all_qnty*1+$("#txttotalqty_"+i).val()*1;
				total_all_qnty_chk=$("#txttotalqty_"+i).val()*1;
				
				var hiddentotalcutqty=$("#hiddentotalcutqty"+i).val()*1;
				
				if(total_all_qnty_chk<hiddentotalcutqty)
				{
					alert('Sample Qty is not allowed less than cutting qty.');
					$("#txttotalqty_"+i).val('');
					//return;
				}
			}
			document.getElementById('txt_total_bh_qty').value=total_bhqnty;
			document.getElementById('txt_total_pl_qty').value=total_plqnty;
			document.getElementById('txt_total_dy_qty').value=total_dyqnty;
			document.getElementById('txt_total_test_qty').value=total_testqnty;
			document.getElementById('txt_total_self_qty').value=total_selfqnty;
			document.getElementById('txt_total_samp_dept_qty').value=total_samp_deptqnty;
			document.getElementById('txt_total_test_fit_qty').value=total_testfitqnty;
			document.getElementById('txt_total_others_qty').value=total_otherqnty;
			document.getElementById('txt_total_all_qty').value=total_all_qnty;
		}


		function add_break_down_tr( i )
		{
			var row_num=$('#size_tbl tbody tr').length;

			if (row_num!=i)
			{
				return false;
			}
			else
			{
				i++;
				$("#size_tbl tbody tr:last").clone().find("input,select").each(function(){

				$(this).attr({
				  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
				  'name': function(_, name) { return name },
				  'value': function(_, value) { return '' }
				});

				}).end().appendTo("#size_tbl");

				$("#size_tbl tbody tr:last").removeAttr('id').attr('id','row_'+i);

				$('#increase_'+i).removeAttr("value").attr("value","+");
				$('#decrease_'+i).removeAttr("value").attr("value","-");
				$('#increase_'+i).removeAttr("onclick").attr("onclick","add_break_down_tr("+i+");");
				$('#decrease_'+i).removeAttr("onclick").attr("onclick","fn_deleteRow("+i+");");
				add_auto_complete(i);
				set_all_onclick();
			}
		}

		function fn_deleteRow(rowNo)
		{
			var numRow=$('#size_tbl tbody tr').length;
			if(numRow==rowNo && rowNo!=1)
			{
				$('#size_tbl tbody tr:last').remove();
			}
			else
			{
				$("#txtsizename_"+rowNo).val('');
				$("#txtgmtpcs_"+rowNo).val('');
				$("#txtgmtbhqty_"+rowNo).val('');
				$("#sizeupid_"+rowNo).val('');
			}
		}

		function add_auto_complete(i)
		{
			$(document).ready(function(e)
			 {
					$("#txtsizename_"+i).autocomplete({
					 source: str_size
				  });
			 });
		}

		function fnc_close( )
		{
			var rowCount = $('#size_tbl tr').length-1;
			//alert( rowCount );return;
			var breck_down_data="";
			for(var i=1; i<=rowCount; i++)
			{
				var tot_qty=$("#txttotalqty_"+i).val();
				//alert(tot_qty);
				if(tot_qty=='' || tot_qty==0)
				{
					 
					if (form_validation('txttotalqty_'+i,'Total Qty')==false)
					{
						//release_freezing();
						return;   
					}
				}
				
				if(breck_down_data=="")
				{
					breck_down_data+=$('#txtsizename_'+i).val()+'_'+($('#txtbhqty_'+i).val()*1)+'_'+($('#txtplqty_'+i).val()*1)+'_'+($('#txtdyqty_'+i).val()*1)+'_'+($('#txttestqty_'+i).val()*1)+'_'+($('#txtselfqty_'+i).val()*1)+'_'+($('#txtsampdeptqty_'+i).val()*1)+'_'+($('#txttestfitqty_'+i).val()*1)+'_'+($('#txtothersqty_'+i).val()*1)+'_'+($('#txttotalqty_'+i).val()*1);
				}
				else
				{
					breck_down_data+="__"+$('#txtsizename_'+i).val()+'_'+($('#txtbhqty_'+i).val()*1)+'_'+($('#txtplqty_'+i).val()*1)+'_'+($('#txtdyqty_'+i).val()*1)+'_'+($('#txttestqty_'+i).val()*1)+'_'+($('#txtselfqty_'+i).val()*1)+'_'+($('#txtsampdeptqty_'+i).val()*1)+'_'+($('#txttestfitqty_'+i).val()*1)+'_'+($('#txtothersqty_'+i).val()*1)+'_'+($('#txttotalqty_'+i).val()*1);
				}
			}
			//alert (breck_down_data);
			document.getElementById('hidden_size_data').value=breck_down_data;
			document.getElementById('hidden_total_self_and_all_data').value=document.getElementById('txt_total_self_qty').value+'___'+document.getElementById('txt_total_all_qty').value;
			parent.emailwindow.hide();
		}
    </script>

    <body onLoad="add_auto_complete(1);" >
		<div align="center" style="width:100%;" >
        <? echo load_freeze_divs ("../../../",$permission,1); ?>
        <form name="size_1" id="size_1">
			<fieldset style="width:930px;">
            <table align="center" cellspacing="0" width="930" class="rpt_table" border="1" rules="all" id="size_tbl" >
                <thead>
                    <th width="110" >Size</th>
                    <th width="70" >BH Qty</th>
                    <th width="70" >Plan</th>
                    <th width="70" >Dyeing</th>
                    <th width="70" >Test</th>
                    <th width="70" >Self</th>
					<th width="70" >Samp. Dept</th>
					<th width="70" >Test Fit</th>
					<th width="70" >Others</th>
                    <th width="70" >Total</th>


                    <th><Input type="hidden" name="mainupid" class="text_boxes" ID="mainupid" value="<? echo $txt_style_id; ?>" style="width:30px" /><Input type="hidden" name="dtlsupid" class="text_boxes" ID="dtlsupid" value="<? echo $update_id_dtl; ?>" style="width:30px" />
                    <!--<Input type="hidden" name="samp_color_id" class="text_boxes" ID="samp_color_id" value="<? //echo $txt_sample_color; ?>" style="width:30px" />-->
                    </th>
                </thead>
                <tbody>

                <?
				$update_dtls_id=str_replace("'","",$update_id_dtl);
				  // $sql_smap="select b.sample_color,c.size_id,c.total_qty from sample_development_dtls b,sample_development_size c where b.id=c.dtls_id and b.status_active=1 and c.status_active=1 and b.id=$update_dtls_id and b.sample_color=$hiddenColorid";//hiddenColorid
				  //sample_sewing_output_mst_id, sample_sewing_output_dtls_id
				    $sql_smap="select  d.color_id, d.size_id, d.size_pass_qty, d.size_rej_qty from sample_sewing_output_mst b,sample_sewing_output_dtls c,sample_sewing_output_colorsize d where  b.id=c.sample_sewing_output_mst_id and d.sample_sewing_output_mst_id=b.id 
 and d.sample_sewing_output_dtls_id=c.id and b.status_active=1 and c.status_active=1 and c.sample_dtls_row_id=$update_dtls_id and d.color_id=$hiddenColorid and c.entry_form_id=127 ";//hiddenColorid
				   
				  $sql_smap_res=sql_select($sql_smap);
				  foreach ($sql_smap_res as $row)
					{
						$qtyArr[$size_arr[$row[csf('size_id')]]]+=$row[csf('size_pass_qty')];
					}
					$data_all=explode('__',$data);
					$count_tr=count($data_all);
					if($count_tr>0)
					{
						$i=1;
						foreach ($data_all as $size_data)
						{
							$size_name=''; $bh_qty=0; $pl_qty=0; $dy_qty=0; $test_qty=0; $self_qty=0; $samp_deptqty=0; $test_fit_qty=0;$others_qty=0; $totalqty=0;
							$ex_size_data=explode('_',$size_data);
							$size_name=$ex_size_data[0];
							$bh_qty=$ex_size_data[1];
							$pl_qty=$ex_size_data[2];
							$dy_qty=$ex_size_data[3];
							$test_qty=$ex_size_data[4];
							$self_qty=$ex_size_data[5];
							$samp_deptqty=$ex_size_data[6];
							$test_fit_qty=$ex_size_data[7];
							$others_qty=$ex_size_data[8];
							$totalqty=$ex_size_data[9];
							$size_namechk=strtoupper($size_name);
							
							$cutting_qty=$qtyArr[$size_namechk];
							// echo $cutting_qty.'DD'.$size_namechk.'<br>';
						?>
							<tr id="row_<? echo $i; ?>" >
								<td><input name="txtsizename[]" class="text_boxes" ID="txtsizename_<? echo $i; ?>" value="<? echo $size_name; ?>" style="width:100px" autofocus/><input type="hidden" name="sizeupid[]" class="text_boxes" ID="sizeupid_<? echo $i; ?>" value="" style="width:30px" ></td>

								 <td><input name="txtbhqty[]" class="text_boxes_numeric" ID="txtbhqty_<? echo $i; ?>" style="width:70px" onBlur="calculate_total_qnty_by_type();" value="<? echo $bh_qty; ?>" /></td>

								<td><input name="txtplqty[]" class="text_boxes_numeric" ID="txtplqty_<? echo $i; ?>" style="width:70px" onBlur="calculate_total_qnty_by_type();" value="<? echo $pl_qty; ?>" /></td>

								<td><input name="txtdyqty[]" class="text_boxes_numeric" ID="txtdyqty_<? echo $i; ?>" style="width:70px" onBlur="calculate_total_qnty_by_type();" value="<? echo $dy_qty; ?>" /></td>

							   <td><input name="txttestqty[]" class="text_boxes_numeric" ID="txttestqty_<? echo $i; ?>" style="width:70px" onBlur="calculate_total_qnty_by_type();" value="<? echo $test_qty; ?>" /></td>

							   <td><input name="txtselfqty[]" class="text_boxes_numeric" ID="txtselfqty_<? echo $i; ?>" style="width:70px"  onBlur="calculate_total_qnty_by_type();" value="<? echo $self_qty; ?>"/></td>

							   <td><input name="txtsamp_deptqty[]" class="text_boxes_numeric" ID="txtsampdeptqty_<? echo $i; ?>" style="width:70px"  onBlur="calculate_total_qnty_by_type();" value="<? echo $samp_deptqty; ?>"/></td>

							   <td><input name="txttestfitqty[]" class="text_boxes_numeric" ID="txttestfitqty_<? echo $i; ?>" style="width:70px" onBlur="calculate_total_qnty_by_type();" value="<? echo $test_fit_qty; ?>" /></td>

							   <td><input name="txtothersqty[]" class="text_boxes_numeric" ID="txtothersqty_<? echo $i; ?>" style="width:70px"  onBlur="calculate_total_qnty_by_type();" value="<? echo $others_qty; ?>"/></td>

							   <td><input name="txttotalqty[]" class="text_boxes_numeric" ID="txttotalqty_<? echo $i; ?>" style="width:70px"  readonly value="<? echo $totalqty; ?>" />
                               <input type="hidden" name="hiddentotalcutqty[]" class="text_boxes_numeric" ID="hiddentotalcutqty<? echo $i; ?>" style="width:70px"  readonly value="<? echo $cutting_qty; ?>" /></td>
								<td align="center">
									<input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr( <? echo $i; ?> )" />
									<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbutton" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
								</td>
							</tr>
						<?
						$i++;
						}
					}
					else
					{
						?>
						<tr id="row_1">
							<td width="110" align="center" ><Input name="txtsizename[]" class="text_boxes" ID="txtsizename_1" value="" style="width:100px" /><Input type="hidden" name="sizeupid[]" class="text_boxes" ID="sizeupid_1" value="" style="width:30px"></td>

							 <td width="70" align="center" ><Input name="txtbhqty[]" class="text_boxes_numeric" ID="txtbhqty_1" style="width:70px" onBlur="calculate_total_qnty_by_type();" /></td>

							<td width="70" align="center" ><Input name="txtplqty[]" class="text_boxes_numeric" ID="txtplqty_1" style="width:70px" onBlur="calculate_total_qnty_by_type();" /></td>

							<td width="70" align="center" ><Input name="txtdyqty[]" class="text_boxes_numeric" ID="txtdyqty_1" style="width:70px" onBlur="calculate_total_qnty_by_type();" /></td>

                           <td width="70" align="center" ><Input name="txttestqty[]" class="text_boxes_numeric" ID="txttestqty_1" style="width:70px" onBlur="calculate_total_qnty_by_type();" /></td>

                           <td width="70" align="center" ><Input name="txtselfqty[]" class="text_boxes_numeric" ID="txtselfqty_1" style="width:70px"  onBlur="calculate_total_qnty_by_type();"/></td>

						   <td width="70" align="center" ><Input name="txtsamp_deptqty[]" class="text_boxes_numeric" ID="txtsampdeptqty_1" style="width:70px"  onBlur="calculate_total_qnty_by_type();"/></td>

						   <td width="70" align="center" ><Input name="txttestfitqty[]" class="text_boxes_numeric" ID="txttestfitqty_1" style="width:70px" onBlur="calculate_total_qnty_by_type();" /></td>


						   <td width="70" align="center" ><Input name="txtothersqty[]" class="text_boxes_numeric" ID="txtothersqty_1" style="width:70px"  onBlur="calculate_total_qnty_by_type();"/></td>

                           <td width="70" align="center" ><Input name="txttotalqty[]" class="text_boxes_numeric" ID="txttotalqty_1" style="width:70px"  readonly /></td>
							<td align="center">
								<input type="button" id="increase_1" name="increase[]" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr( 1 )" />
								<input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbutton" value="-" onClick="fn_deleteRow(1);" />
							</td>
						</tr>
					<?
					}
                ?>
                </tbody>
            </table>
            <table align="center" cellspacing="0" width="930" class="rpt_table" border="1" rules="all" id="" >
				<tr>
					<td width="110">&nbsp;</td>
					<td width="70" align="center"><Input name="txt_total_bh_qty" class="text_boxes_numeric" ID="txt_total_bh_qty" style="width:70px" value="<? echo $total_bhqty; ?>" readonly /></td>
					<td width="70" align="center"><Input name="txt_total_pl_qty" class="text_boxes_numeric" ID="txt_total_pl_qty" style="width:70px" value="<? echo $total_plqty; ?>" readonly /></td>
					<td width="70" align="center"><Input name="txt_total_dy_qty" class="text_boxes_numeric" ID="txt_total_dy_qty" style="width:70px" value="<? echo $total_dyqty; ?>" readonly /></td>
					<td width="70" align="center"><Input name="txt_total_test_qty" class="text_boxes_numeric" ID="txt_total_test_qty" style="width:70px" value="<? echo $total_testqty; ?>" readonly /></td>
					<td width="70" align="center"><Input name="txt_total_self_qty" class="text_boxes_numeric" ID="txt_total_self_qty" style="width:70px" value="<? echo $total_selfqty; ?>" readonly /></td>
					<td width="70" align="center"><Input name="txt_total_samp_dept_qty" class="text_boxes_numeric" ID="txt_total_samp_dept_qty" style="width:70px" value="<? echo $total_samp_dept_qty; ?>" readonly /></td>
					<td width="70" align="center"><Input name="txt_total_test_fit_qty" class="text_boxes_numeric" ID="txt_total_test_fit_qty" style="width:70px" value="<? echo $total_testfitqty; ?>" readonly /></td>
					<td width="70" align="center"><Input name="txt_total_others_qty" class="text_boxes_numeric" ID="txt_total_others_qty" style="width:70px" value="<? echo $total_others_qty; ?>" readonly /></td>
					<td width="70" align="center"><Input name="txt_total_all_qty" class="text_boxes_numeric" ID="txt_total_all_qty" style="width:70px" value="<? echo $total_all_qty; ?>" readonly /></td>
					 <td>&nbsp;</td>
				</tr>
                <tr>
                    <td colspan="10" align="center" class="">
                        <input type="hidden" name="hidden_size_data" id="hidden_size_data" class="text_boxes /">
                        <input type="hidden" name="hidden_total_self_and_all_data" id="hidden_total_self_and_all_data" class="text_boxes /">
                    </td>
                </tr>
                <tr>
                    <td align="center" colspan="10">
                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
                    </td>
                </tr>
            </table>
            </fieldset>
            </form>
        </div>
    </body>
    <script>calculate_total_qnty_by_type(); </script>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}


if ($action=="sizeinfo_popup_mouseover")
{
	echo load_html_head_contents("Sample Requisition Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
		?>

    <script>
		var permission='<? echo $permission; ?>';
		var str_size = [<? echo substr(return_library_autocomplete( "select size_name from lib_size group by size_name order by size_name", "size_name" ), 0, -1); ?> ];

		function total_submission_qty(inc)
		{
			// this function will calculate the sum of the BH Qty,Plan Qty,Dyeing,Test,Self Qty in Sizeinfo popup related with Sample Details Module for every row
			var tot_row=$('#size_tbl tbody tr').length;
			 var total="";
			for(var i=1; i<=tot_row; i++)
			{
				var bhQty=$("#txtbhqty_"+i).val()*1;
				var plQty=$("#txtplqty_"+i).val()*1;
				var dyQty=$("#txtdyqty_"+i).val()*1;
				var testQty=$("#txttestqty_"+i).val()*1;
				var selfQty=$("#txtselfqty_"+i).val()*1;
				var total=bhQty+plQty + dyQty + testQty + selfQty;
				$("#txttotalqty_"+i).val(total);
			}
		}

		function calculate_total_qnty_by_type()
		{
			var tot_row=$('#size_tbl tbody tr').length;
			var total_bhqnty=""; var total_plqnty=""; var total_dyqnty=""; var total_testqnty=""; var total_selfqnty=""; var total_all_qnty=""; var total='';
			for(var i=1; i<=tot_row; i++)
			{
				var bhQty=$("#txtbhqty_"+i).val()*1;
				var bhQty=$("#txtbhqty_"+i).val()*1;
				var plQty=$("#txtplqty_"+i).val()*1;
				var dyQty=$("#txtdyqty_"+i).val()*1;
				var testQty=$("#txttestqty_"+i).val()*1;
				var selfQty=$("#txtselfqty_"+i).val()*1;
				var total=bhQty+plQty + dyQty + testQty + selfQty;
				$("#txttotalqty_"+i).val(total);

				var bh_qty=$("#txtbhqty_"+i).val()*1;
				total_bhqnty=total_bhqnty*1+$("#txtbhqty_"+i).val()*1;

				var pl_qty=$("#txtplqty_"+i).val()*1;
				total_plqnty=total_plqnty*1+$("#txtplqty_"+i).val()*1;

				var dy_qty=$("#txtdyqty_"+i).val()*1;
				total_dyqnty=total_dyqnty*1+$("#txtdyqty_"+i).val()*1;

				var test_qty=$("#txttestqty_"+i).val()*1;
				total_testqnty=total_testqnty*1+$("#txttestqty_"+i).val()*1;

				var self_qty=$("#txtselfqty_"+i).val()*1;
				total_selfqnty=total_selfqnty*1+$("#txtselfqty_"+i).val()*1;

				var total_qty=$("#txttotalqty_"+i).val()*1;
				total_all_qnty=total_all_qnty*1+$("#txttotalqty_"+i).val()*1;
			}
			document.getElementById('txt_total_bh_qty').value=total_bhqnty;
			document.getElementById('txt_total_pl_qty').value=total_plqnty;
			document.getElementById('txt_total_dy_qty').value=total_dyqnty;
			document.getElementById('txt_total_test_qty').value=total_testqnty;
			document.getElementById('txt_total_self_qty').value=total_selfqnty;
			document.getElementById('txt_total_all_qty').value=total_all_qnty;
		}


		function add_break_down_tr( i )
		{
			var row_num=$('#size_tbl tbody tr').length;

			if (row_num!=i)
			{
				return false;
			}
			else
			{
				i++;
				$("#size_tbl tbody tr:last").clone().find("input,select").each(function(){

				$(this).attr({
				  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
				  'name': function(_, name) { return name },
				  'value': function(_, value) { return '' }
				});

				}).end().appendTo("#size_tbl");

				$("#size_tbl tbody tr:last").removeAttr('id').attr('id','row_'+i);

				$('#increase_'+i).removeAttr("value").attr("value","+");
				$('#decrease_'+i).removeAttr("value").attr("value","-");
				$('#increase_'+i).removeAttr("onclick").attr("onclick","add_break_down_tr("+i+");");
				$('#decrease_'+i).removeAttr("onclick").attr("onclick","fn_deleteRow("+i+");");
				add_auto_complete(i);
				set_all_onclick();
			}
		}

		function fn_deleteRow(rowNo)
		{
			var numRow=$('#size_tbl tbody tr').length;
			if(numRow==rowNo && rowNo!=1)
			{
				$('#size_tbl tbody tr:last').remove();
			}
			else
			{
				$("#txtsizename_"+rowNo).val('');
				$("#txtgmtpcs_"+rowNo).val('');
				$("#txtgmtbhqty_"+rowNo).val('');
				$("#sizeupid_"+rowNo).val('');
			}
		}

		function add_auto_complete(i)
		{
			$(document).ready(function(e)
			 {
					$("#txtsizename_"+i).autocomplete({
					 source: str_size
				  });
			 });
		}

		function fnc_close( )
		{
			var rowCount = $('#size_tbl tr').length-1;
			//alert( rowCount );return;
			var breck_down_data="";
			for(var i=1; i<=rowCount; i++)
			{
				if(breck_down_data=="")
				{
					breck_down_data+=$('#txtsizename_'+i).val()+'_'+($('#txtbhqty_'+i).val()*1)+'_'+($('#txtplqty_'+i).val()*1)+'_'+($('#txtdyqty_'+i).val()*1)+'_'+($('#txttestqty_'+i).val()*1)+'_'+($('#txtselfqty_'+i).val()*1)+'_'+($('#txttotalqty_'+i).val()*1);
				}
				else
				{
					breck_down_data+="__"+$('#txtsizename_'+i).val()+'_'+($('#txtbhqty_'+i).val()*1)+'_'+($('#txtplqty_'+i).val()*1)+'_'+($('#txtdyqty_'+i).val()*1)+'_'+($('#txttestqty_'+i).val()*1)+'_'+($('#txtselfqty_'+i).val()*1)+'_'+($('#txttotalqty_'+i).val()*1);
				}
			}
			//alert (breck_down_data);
			document.getElementById('hidden_size_data').value=breck_down_data;
			document.getElementById('hidden_total_self_and_all_data').value=document.getElementById('txt_total_self_qty').value+'___'+document.getElementById('txt_total_all_qty').value;
			parent.emailwindow.hide();
		}
    </script>

    <body onLoad="add_auto_complete(1);" >
		<div align="center" style="width:100%;" >
        <? echo load_freeze_divs ("../../../",$permission,1); ?>
        <form name="size_1" id="size_1">
			<fieldset style="width:700px;">
            <table align="center" cellspacing="0" width="700" class="rpt_table" border="1" rules="all" id="size_tbl" >
                <thead>
                    <th width="110" >Size</th>
                    <th width="70" >BH Qty</th>
                    <th width="70" >Plan</th>
                    <th width="70" >Dyeing</th>
                    <th width="70" >Test</th>
                    <th width="70" >Self</th>
                    <th width="70" >Total</th>


                    <th><Input type="hidden" name="mainupid" class="text_boxes" ID="mainupid" value="<? echo $txt_style_id; ?>" style="width:30px" /><Input type="hidden" name="dtlsupid" class="text_boxes" ID="dtlsupid" value="<? echo $update_id_dtl; ?>" style="width:30px" />
                    <!--<Input type="hidden" name="samp_color_id" class="text_boxes" ID="samp_color_id" value="<? //echo $txt_sample_color; ?>" style="width:30px" />-->
                    </th>
                </thead>
                <tbody>

                <?
					$data_all=explode('__',$data);
					$count_tr=count($data_all);
					if($count_tr>0)
					{
						$i=1;
						foreach ($data_all as $size_data)
						{
							$size_name=''; $bh_qty=0; $pl_qty=0; $dy_qty=0; $test_qty=0; $self_qty=0; $totalqty=0;
							$ex_size_data=explode('_',$size_data);
							$size_name=$ex_size_data[0];
							$bh_qty=$ex_size_data[1];
							$pl_qty=$ex_size_data[2];
							$dy_qty=$ex_size_data[3];
							$test_qty=$ex_size_data[4];
							$self_qty=$ex_size_data[5];
							$totalqty=$ex_size_data[6];
						?>
							<tr id="row_<? echo $i; ?>" >
								<td><input name="txtsizename[]" class="text_boxes" ID="txtsizename_<? echo $i; ?>" disabled="" value="<? echo $size_name; ?>" style="width:100px" autofocus/><input type="hidden" name="sizeupid[]" class="text_boxes" ID="sizeupid_<? echo $i; ?>" value="" style="width:30px" ></td>

								 <td><input name="txtbhqty[]" class="text_boxes_numeric" ID="txtbhqty_<? echo $i; ?>" disabled="" style="width:70px" onBlur="calculate_total_qnty_by_type();" value="<? echo $bh_qty; ?>" /></td>

								<td><input name="txtplqty[]" class="text_boxes_numeric" ID="txtplqty_<? echo $i; ?>" disabled="" style="width:70px" onBlur="calculate_total_qnty_by_type();" value="<? echo $pl_qty; ?>" /></td>

								<td><input name="txtdyqty[]" class="text_boxes_numeric" ID="txtdyqty_<? echo $i; ?>" disabled="" style="width:70px" onBlur="calculate_total_qnty_by_type();" value="<? echo $dy_qty; ?>" /></td>

							   <td><input name="txttestqty[]" class="text_boxes_numeric" ID="txttestqty_<? echo $i; ?>" style="width:70px" onBlur="calculate_total_qnty_by_type();" value="<? echo $test_qty; ?>" disabled="" /></td>

							   <td><input name="txtselfqty[]" class="text_boxes_numeric" ID="txtselfqty_<? echo $i; ?>" style="width:70px"  onBlur="calculate_total_qnty_by_type();" value="<? echo $self_qty; ?>" disabled="" /></td>

							   <td><input name="txttotalqty[]" class="text_boxes_numeric" ID="txttotalqty_<? echo $i; ?>" style="width:70px"  readonly value="<? echo $totalqty; ?>" disabled="" /></td>
								<td align="center">
									<input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:30px" class="formbutton" disabled="" value="+" onClick="add_break_down_tr( <? echo $i; ?> )" />
									<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbutton" disabled="" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
								</td>
							</tr>
						<?
						$i++;
						}
					}
					else
					{
						?>
						<tr id="row_1">
							<td width="110" align="center" ><Input name="txtsizename[]" class="text_boxes" ID="txtsizename_1" value="" style="width:100px" /><Input type="hidden" name="sizeupid[]" class="text_boxes" ID="sizeupid_1" value="" style="width:30px"></td>

							 <td width="70" align="center" ><Input name="txtbhqty[]" class="text_boxes_numeric" ID="txtbhqty_1" style="width:70px" onBlur="calculate_total_qnty_by_type();" /></td>

							<td width="70" align="center" ><Input name="txtplqty[]" class="text_boxes_numeric" ID="txtplqty_1" style="width:70px" onBlur="calculate_total_qnty_by_type();" /></td>

							<td width="70" align="center" ><Input name="txtdyqty[]" class="text_boxes_numeric" ID="txtdyqty_1" style="width:70px" onBlur="calculate_total_qnty_by_type();" /></td>

                           <td width="70" align="center" ><Input name="txttestqty[]" class="text_boxes_numeric" ID="txttestqty_1" style="width:70px" onBlur="calculate_total_qnty_by_type();" /></td>
                           <td width="70" align="center" ><Input name="txtselfqty[]" class="text_boxes_numeric" ID="txtselfqty_1" style="width:70px"  onBlur="calculate_total_qnty_by_type();"/></td>
                           <td width="70" align="center" ><Input name="txttotalqty[]" class="text_boxes_numeric" ID="txttotalqty_1" style="width:70px"  readonly /></td>
							<td align="center">
								<input type="button" id="increase_1" name="increase[]" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr( 1 )" />
								<input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbutton" value="-" onClick="fn_deleteRow(1);" />
							</td>
						</tr>
					<?
					}
                ?>
                </tbody>
            </table>
            <table align="center" cellspacing="0" width="700" class="rpt_table" border="1" rules="all" id="" >
				<tr>
					<td width="110">&nbsp;</td>
					<td width="70" align="center"><Input name="txt_total_bh_qty" class="text_boxes_numeric" ID="txt_total_bh_qty" style="width:70px" value="<? echo $total_bhqty; ?>" readonly /></td>
					<td width="70" align="center"><Input name="txt_total_pl_qty" class="text_boxes_numeric" ID="txt_total_pl_qty" style="width:70px" value="<? echo $total_plqty; ?>" readonly /></td>
					<td width="70" align="center"><Input name="txt_total_dy_qty" class="text_boxes_numeric" ID="txt_total_dy_qty" style="width:70px" value="<? echo $total_dyqty; ?>" readonly /></td>
					<td width="70" align="center"><Input name="txt_total_test_qty" class="text_boxes_numeric" ID="txt_total_test_qty" style="width:70px" value="<? echo $total_testqty; ?>" readonly /></td>
					<td width="70" align="center"><Input name="txt_total_self_qty" class="text_boxes_numeric" ID="txt_total_self_qty" style="width:70px" value="<? echo $total_selfqty; ?>" readonly /></td>
					<td width="70" align="center"><Input name="txt_total_all_qty" class="text_boxes_numeric" ID="txt_total_all_qty" style="width:70px" value="<? echo $total_all_qty; ?>" readonly /></td>
					 <td>&nbsp;</td>
				</tr>
                <tr>
                    <td colspan="8" align="center" class="">
                        <input type="hidden" name="hidden_size_data" id="hidden_size_data" class="text_boxes /">
                        <input type="hidden" name="hidden_total_self_and_all_data" id="hidden_total_self_and_all_data" class="text_boxes /">
                    </td>
                </tr>
                <tr>
                    <td align="center" colspan="8">
                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
                    </td>
                </tr>
            </table>
            </fieldset>
            </form>
        </div>
    </body>
    <script>calculate_total_qnty_by_type();add_auto_complete(1); </script>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}


if($action=="load_data_to_sizeinfo")
{
	$qry_size="select id, mst_id, dtls_id, size_id, size_qty,bh_qty,plan_qty,dyeing_qty,test_qty,self_qty,samp_dept_qty,test_fit_qty,others_qty,total_qty from sample_development_size where dtls_id='$data'";
	$qry_result=sql_select($qry_size);
	foreach ($qry_result as $row)
	{
		if($id=="") $id=$row[csf("id")]; else $id.="*".$row[csf("id")];
		if($size_id=="") $size_id=$size_arr[$row[csf("size_id")]]; else $size_id.="*".$size_arr[$row[csf("size_id")]];
		if($size_qty=="") $size_qty=$row[csf("size_qty")]; else $size_qty.="*".$row[csf("size_qty")];
		if($bh_qty=="") $bh_qty=$row[csf("bh_qty")]; else $bh_qty.="*".$row[csf("bh_qty")];
		if($pl_qty=="") $pl_qty=$row[csf("plan_qty")]; else $pl_qty.="*".$row[csf("plan_qty")];
		if($dy_qty=="") $dy_qty=$row[csf("dyeing_qty")]; else $dy_qty.="*".$row[csf("dyeing_qty")];
		if($test_qty=="") $test_qty=$row[csf("test_qty")]; else $test_qty.="*".$row[csf("test_qty")];
		if($self_qty=="") $self_qty=$row[csf("self_qty")]; else $self_qty.="*".$row[csf("self_qty")];		
		if($samp_dept_qty=="") $samp_dept_qty=$row[csf("samp_dept_qty")]; else $samp_dept_qty.="*".$row[csf("samp_dept_qty")];
		if($test_fit_qty=="") $test_fit_qty=$row[csf("test_fit_qty")]; else $test_fit_qty.="*".$row[csf("test_fit_qty")];
		if($others_qty=="") $others_qty=$row[csf("others_qty")]; else $others_qty.="*".$row[csf("others_qty")];
		if($total_qty=="") $total_qty=$row[csf("total_qty")]; else $total_qty.="*".$row[csf("total_qty")];
	}
	echo "document.getElementById('hidden_size_id').value 	 				= '".$size_id."';\n";
	echo "document.getElementById('hidden_bhqty').value 	 					= '".$bh_qty."';\n";
	echo "document.getElementById('hidden_plnqnty').value 	 					= '".$pl_qty."';\n";
	echo "document.getElementById('hidden_dyqnty').value 	 					= '".$dy_qty."';\n";
	echo "document.getElementById('hidden_testqnty').value 	 					= '".$test_qty."';\n";
	echo "document.getElementById('hidden_selfqnty').value 	 					= '".$self_qty."';\n";
	echo "document.getElementById('hidden_samp_deptqty').value 	 					= '".$samp_dept_qty."';\n";
	echo "document.getElementById('hidden_testfitqnty').value 	 					= '".$test_fit_qty."';\n";
	echo "document.getElementById('hidden_othersqty').value 	 					= '".$others_qty."';\n";
	echo "document.getElementById('hidden_totalqnty').value 	 					= '".$total_qty."';\n";
	echo "document.getElementById('hidden_tbl_size_id').value 	 			= '".$id."';\n";
	exit();
}


if ($action=="load_drop_down_location")
{
	$sql="select location_name,id from lib_location where company_id='$data' and is_deleted=0  and status_active=1 $location_credential_cond  order by location_name";
	if(count(sql_select($sql))==1)
	{
		echo create_drop_down( "cbo_location_name", 130, $sql,'id,location_name', 0, '--- Select Location ---', 0, ""  );
	}
	else
	{
		echo create_drop_down( "cbo_location_name", 130, $sql,'id,location_name', 1, '--- Select Location ---', 0, ""  );
	}
	exit();
}

if ($action=="load_drop_down_garment_item_for_after_order")
{
 	 $dt=explode(",",$data);
 	 if(count($dt)>1)
 	 {
		echo create_drop_down( "cboGarmentItem_1", 100, $garments_item,"", 1, "-- Select Item --", $selected, "",0,$data );
	 }
	else
	{

		 echo create_drop_down( "cboGarmentItem_1", 100, $garments_item,"", 0, "-- Select Item --", $selected, "",0,$data );
	}
}

if ($action=="load_drop_down_garment_item_for_not_after_order")
{

	echo create_drop_down( "cboGarmentItem_1", 100, $garments_item,"", 1, "Select Item", 0, "");
}

if ($action=="load_drop_down_trims_group_from_budget_for_after_order")
{
 $sql="select a.item_name,a.id from lib_item_group a,wo_pre_cost_trim_cost_dtls b where a.item_category=4 and  a.is_deleted=0  and a.status_active=1 and b.trim_group=a.id group by a.item_name,a.id";
echo create_drop_down( "cboRaTrimsGroup_1", 100, $sql,"id,item_name", 1, "Select Item", 0, "");
}

if ($action=="load_drop_down_fabric_nature_for_after_order")
{
 	 $dt=explode(",",$data);
 	 if(count($dt)>1)
		echo create_drop_down( "cboRfFabricNature_1", 95, $item_category,"", 1, "-- Select Fabric Nature --", $selected, "",0,$data );
	else
		 echo create_drop_down( "cboRfFabricNature_1", 95, $item_category,"", 0, "-- Select Fabric Nature --", $selected, "",0,$data );
}

if ($action=="load_drop_down_fabric_nature_for_not_after_order")
{

	echo create_drop_down( "cboRfFabricNature_1", 95, $item_category,"", 1, "Select Item", 0, "");
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down( 'requires/sample_requisition_with_booking_controller', this.value, 'load_drop_down_season_buyer', 'season_td');load_drop_down( 'requires/sample_requisition_with_booking_controller', this.value, 'load_drop_down_sample_for_buyer', 'sample_td');get_buyer_config(this.value);" );
}

if ($action=="load_drop_down_sample_for_buyer")
{
	echo create_drop_down( "cboSampleName_1", 100, "select a.id,a.sample_name,b.sequ from lib_sample a,lib_buyer_tag_sample b where a.id=b.tag_sample and  b.buyer_id=$data and b.sequ  is not null and
 a.status_active=1 and a.is_deleted=0  group by  a.id,a.sample_name,b.sequ order by b.sequ ","id,sample_name", 1, "-- Select Buyer --", $selected, "" );
}

if ($action=="load_drop_down_buyer_req")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
}

if ($action=="load_drop_down_buyer_style")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
}

if ($action=="load_drop_down_buyer_inq")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
}

if ($action=="load_drop_down_season_buyer")
{
	//$datas=explode('_',$data);
	//echo create_drop_down( "cbo_season_name", 158, "select a.id,a.season_name from LIB_BUYER_SEASON a,variable_order_tracking b where a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and a.buyer_id='$datas[0]' and b.company_name='$datas[1]' and b.season_mandatory=1 and b.variable_list=44","id,season_name", 1, "-- Select Season --", $selected, "" );
	//echo "select a.id,a.season_name from lib_buyer_season a where a.status_active =1 and a.is_deleted=0 and a.buyer_id='$data'";
	$sql="select a.id,a.season_name from lib_buyer_season a where a.status_active =1 and a.is_deleted=0 and a.buyer_id='$data'";
	if(count(sql_select($sql))==1)
	{
		echo create_drop_down( "cbo_season_name", 130, $sql,'id,season_name', 0, '--- Select Season ---', 1, ""  );
	}
	else
	{
		echo create_drop_down( "cbo_season_name", 130, $sql,'id,season_name', 1, '--- Select Season ---', 0, ""  );
	}
}

if ($action=="load_drop_down_agent")
{
	echo create_drop_down( "cbo_agent", 130, "SELECT a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b,lib_buyer_party_type c where b.buyer_id=c.buyer_id and  a.status_active =1 and a.is_deleted=0 and c.party_type in(20,21) and b.buyer_id=a.id and b.tag_company='$data' group by  a.id,a.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Agent --", $selected, "" );
	exit();
}

if ($action=="cbo_dealing_merchant")
{
	echo create_drop_down( "cbo_dealing_merchant", 130, "select id,team_member_name from lib_mkt_team_member_info where team_id='$data' and status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select Team Member --", $selected, "" );
	exit();
}

if ($action=="save_update_delete_mst")
{
   $process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$Remarks=str_replace("'","",$txt_remarks);
	$str_rep=array("+", "&", "*", "(", ")", "=","'","\r", "\n",'"','#');
	$mst_remarks=str_replace($str_rep,' ',$Remarks);


 	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$id_mst=return_next_id( "id", "sample_development_mst", 1 ) ;

		if($db_type==0) $yearCond="YEAR(insert_date)"; else if($db_type==2) $yearCond="to_char(insert_date,'YYYY')";

		$new_system_id=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', '', date("Y",time()), 5, "select requisition_number_prefix, requisition_number_prefix_num from sample_development_mst where entry_form_id=203 and company_id=$cbo_company_name and $yearCond=".date('Y',time())." order by id desc ", "requisition_number_prefix", "requisition_number_prefix_num" ));

		$field_array="id, requisition_number_prefix, requisition_number_prefix_num, requisition_number, sample_stage_id, requisition_date, quotation_id, style_ref_no, company_id, location_id, buyer_name, season, product_dept, dealing_marchant, agent_name, buyer_ref, bh_merchant, estimated_shipdate, remarks, inserted_by, insert_date, status_active, is_deleted, entry_form_id, is_copy, req_ready_to_approved, material_delivery_date, fabric_material_id, sustainability_std_id, order_nature_id, quality_level_id, design_source_id, factory_merchant, team_leader, client_id, item_catgory, season_year, brand_id, sub_dept_id, style_desc,fit_id,internal_ref,control_no,qrr_date";
		$data_array="(".$id_mst.",'".$new_system_id[1]."',".$new_system_id[2].",'".$new_system_id[0]."',".$cbo_sample_stage.",".$txt_requisition_date.",".$txt_quotation_id.",".$txt_style_name.",".$cbo_company_name.",".$cbo_location_name.",".$cbo_buyer_name.",".$cbo_season_name.",".$cbo_product_department.",".$cbo_dealing_merchant.",".$cbo_agent.",".$txt_buyer_ref.",".$txt_bhmerchant.",".$txt_est_ship_date.",'".$mst_remarks."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0,203,0,".$cbo_ready_to_approved.",".$txt_material_dlvry_date.", ".$cbo_fab_material.", ".$sustainability_standard.", ".$cbo_quality_level.", ".$cbo_qltyLabel.", ".$cbo_design_source_id.", ".$cbo_factory_merchant.", ".$cbo_team_leader.", ".$cbo_client.", ".$txt_item_catgory.", ".$cbo_season_year.", ".$cbo_brand_id.", ".$cbo_sub_dept.", ".$txt_style_desc.", ".$cbo_fit_id.", ".$txt_internal_ref.", ".$txt_control_no.", ".$txt_qrr_date.")";//
		
		
			$image_mandatory=image_mandatory($cbo_company_name);
			
			
			$image_mandatory=1;
			if($image_mandatory ==2){

				if(str_replace("'","",$sample_fabric_booking_file) !==""){

					$rID=sql_insert("sample_development_mst",$field_array,$data_array,1);
			   }else{
				  echo "10**11";die;
			   }

			}else{
				//echo "10**Insert into sample_development_mst ($field_array) values $data_array"; die;
				$rID=sql_insert("sample_development_mst",$field_array,$data_array,1);
			}
		
		//echo $rID; die;

		if($db_type==0)
		{
			if($rID )
			{
				mysql_query("COMMIT");
				echo "0**".$new_system_id[0]."**".$id_mst;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".$id_mst;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID )
			{
				oci_commit($con);
				echo "0**".$new_system_id[0]."**".$id_mst;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$id_mst;
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)  // Update Here
	{
		$con = connect();
		if($db_type==0) mysql_query("BEGIN");		

		$field_array="sample_stage_id*requisition_date*style_ref_no*buyer_name*season*product_dept*dealing_marchant*agent_name*buyer_ref*bh_merchant*estimated_shipdate*remarks*updated_by*update_date*req_ready_to_approved*material_delivery_date*quotation_id*fabric_material_id*sustainability_std_id*order_nature_id*quality_level_id*design_source_id*factory_merchant*team_leader*client_id*item_catgory*season_year*brand_id*sub_dept_id*style_desc*fit_id*internal_ref*control_no*qrr_date";
		
		//txt_bhmerchant*txt_product_code
		$data_array="".$cbo_sample_stage."*".$txt_requisition_date."*".$txt_style_name."*".$cbo_buyer_name."*".$cbo_season_name."*".$cbo_product_department."*".$cbo_dealing_merchant."*".$cbo_agent."*".$txt_buyer_ref."*".$txt_bhmerchant."*".$txt_est_ship_date."*'".$mst_remarks."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_ready_to_approved."*".$txt_material_dlvry_date."*".$txt_quotation_id."*".$cbo_fab_material."*".$sustainability_standard."*".$cbo_quality_level."*".$cbo_qltyLabel."*".$cbo_design_source_id."*".$cbo_factory_merchant."*".$cbo_team_leader."*".$cbo_client."*".$txt_item_catgory."*".$cbo_season_year."*".$cbo_brand_id."*".$cbo_sub_dept."*".$txt_style_desc."*".$cbo_fit_id."*".$txt_internal_ref."*".$txt_control_no."*".$txt_qrr_date."";

		$rID=sql_update("sample_development_mst",$field_array,$data_array,"id","".$update_id."",1);

		if($db_type==0)
		{
			if($rID )
			{
				mysql_query("COMMIT");
				echo "1**".str_replace("'","",$update_id);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$update_id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID )
			{
				oci_commit($con);
				//echo "1**".str_replace("'","",$update_id);
				echo "1**".str_replace("'","",$txt_requisition_id)."**".str_replace("'","",$update_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$update_id);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)  // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}		

		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
		$rID1=sql_delete("sample_development_mst",$field_array,$data_array,"id","".$update_id."",0);
		$rID2=sql_delete("sample_development_dtls",$field_array,$data_array,"sample_mst_id","".$update_id."",0);
		$rID3=sql_delete("sample_development_size",$field_array,$data_array,"mst_id","".$update_id."",0);
		$rID4=sql_delete("sample_development_fabric_acc",$field_array,$data_array,"sample_mst_id","".$update_id."",0);
		$rID5=sql_delete("sample_development_rf_color",$field_array,$data_array,"mst_id","".$update_id."",0);
		if($db_type==0)
		{
			if($rID1 && $rID2 && $rID3 )
			{
				mysql_query("COMMIT");
				echo "2**".str_replace("'","",$update_id);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$update_id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID1 && $rID2 && $rID3)
			{
				oci_commit($con);
				echo "2**".str_replace("'","",$update_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$update_id);
			}
		}
		disconnect($con);
	}
}

if($action=="style_id_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Sample Requisition Info","../../../", 1, 1, $unicode);
	if($cbo_company_name>0) $isDis=1; else $isDis=0;
	?>
	<script>
		$(document).ready(function(e) {
			$("#txt_search_common").focus();
		});
		function search_populate(str)
		{
			//alert(str);
			if(str==0)
			{
				document.getElementById('search_by_th_up').innerHTML="Enter Style ID";
				document.getElementById('search_by_td').innerHTML='<input	type="text"	name="txt_search_common" style="width:130px " class="text_boxes" id="txt_search_common"	value=""  />';
			}
			else if(str==1)
			{
				document.getElementById('search_by_th_up').innerHTML="Enter Style Name";
				document.getElementById('search_by_td').innerHTML='<input	type="text"	name="txt_search_common" style="width:130px " class="text_boxes" id="txt_search_common"	value=""  />';
			}
		}

		function js_set_value( mst_id )
		{
			document.getElementById('selected_job').value=mst_id;
			//alert(document.getElementById('selected_job').value);
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
		<div align="center" style="width:100%;" >
			<form name="searchsampledevelopmentfrm_1"  id="searchsampledevelopmentfrm_1" autocomplete="off">
                <table  width="990" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
                    <thead>
                        <th colspan="8"><? echo create_drop_down( "cbo_search_category", 140, $string_search_type,'', 1, "-- Search Catagory --" ); ?></th>
                    </thead>
                    <thead>
                        <th width="140">Company Name</th>
                        <th width="100">Job NO</th>
                        <th width="100">Order NO</th>
                        <th width="130">Buyer Name</th>
                        <th width="70">Style ID</th>
                        <th width="100" >Style Name</th>
                        <th width="200">Est. Ship Date Range</th>
                        <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
                    </thead>
                    <tr class="general">
                        <td>
                            <input type="hidden" id="selected_job">
                            <? echo create_drop_down( "cbo_company_mst", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0  $company_credential_cond  order by company_name","id,company_name",1, "-- Select Company --", $cbo_company_name,"load_drop_down( 'sample_requisition_with_booking_controller', this.value, 'load_drop_down_buyer_style', 'buyer_td_st' );",$isDis ); ?>
                        </td>
                        <td><input type="text" style="width:100px" class="text_boxes"  name="txt_job_no" id="txt_job_no" /></td>
                        <td><input type="text" style="width:100px" class="text_boxes"  name="txt_order_no" id="txt_order_no" /></td>
                        <td id="buyer_td_st"><? echo create_drop_down( "cbo_buyer_name", 130, $blank_array,'', 1, "-- Select Buyer --" ); ?></td>
                        <td><input type="text" style="width:70px" class="text_boxes"  name="txt_style_id" id="txt_style_id" /></td>
                        <td> <input type="text" style="width:100px" class="text_boxes"  name="txt_style_name1" id="txt_style_name1"  /></td>
                        <td>
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px"> To
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
                        </td>
                        <td>
                            <input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_style_id').value+'_'+document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_style_name1').value+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_order_no').value, 'create_style_id_search_list_view', 'search_div', 'sample_requisition_with_booking_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:80px;" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="8" align="center" valign="middle"><? echo load_month_buttons(1); ?></td>
                    </tr>
                </table>
			</form>
            <div id="search_div"></div>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="inquiry_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	if($company>0) $isDis=1; else $isDis=0;
?>

<script>
	function js_set_value(mrr)
	{
 		$("#txt_inquiry_id").val(mrr); // mrr number
		parent.emailwindow.hide();
	}
</script>

</head>

<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="500" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
            <thead>
                <th colspan="8"><? echo create_drop_down( "cbo_string_search_type", 140, $string_search_type,'', 1, "--Searching Type--" ); ?></th>
           </thead>
            <thead>
                <tr>
                   <th width="150">Company Name</th>
                    <th width="150">Buyer Name</th>
                    <th width="100">Inquery ID</th>
                    <th width="80">Year</th>
                    <th width="150" >Style Reff.</th>
                    <th width="100" >Buyer Inquery No</th>
                    <th width="100">Inquery Date </th>
                    <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:80px" class="formbutton"  /></th>
                </tr>
            </thead>
            <tbody>
                <tr class="general">
                    <td><? echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_credential_cond  order by company_name","id,company_name", 1, "-- Select Company --",$company, "load_drop_down( 'sample_requisition_with_booking_controller', this.value, 'load_drop_down_buyer_inq', 'buyer_td_inq' );",$isDis); ?></td>
                    <td id="buyer_td_inq"><? echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-- Select Buyer --", $selected, "" ); ?></td>
                    <td><input type="text" style="width:80px" class="text_boxes"  name="txt_inquery_id" id="txt_inquery_id" /></td>
                    <td><? echo create_drop_down( "cbo_year", 70, $year,"", 1, "- Select- ", date('Y'), "" ); ?></td>
                    <td><input type="text" style="width:120px" class="text_boxes"  name="txt_style" id="txt_style" /></td>
                    <td><input type="text" style="width:80px" class="text_boxes"  name="txt_requst_no" id="txt_requst_no" /></td>
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="Date" /></td>
                    <td>
                    <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_inquery_id').value+'_'+document.getElementById('cbo_year').value+'_'+document.getElementById('txt_requst_no').value+'_'+document.getElementById('cbo_string_search_type').value, 'create_inquiry_search_list_view', 'search_div', 'sample_requisition_with_booking_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:80px;" />
                    	<input type="hidden" id="txt_inquiry_id" value="" />
                    </td>
                </tr>
            </tbody>
        </table>
        <div align="center" valign="top" id="search_div"> </div>
        </form>
   </div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="create_inquiry_search_list_view")
{

	$ex_data = explode("_",$data);
	$txt_buyer = $ex_data[0];
	$txt_style = $ex_data[1];
	$inq_date = $ex_data[2];
	$company = $ex_data[3];
    if($company==0) $company_name=""; else $company_name=" and company_id=$company";
	if($txt_buyer==0) $buyer_name=""; else $buyer_name="and buyer_id=$txt_buyer";
	if($db_type==0) $year_cond=" and SUBSTRING_INDEX(`insert_date`, '-', 1)=$ex_data[5]";
	if($db_type==2) $year_cond=" and to_char(insert_date,'YYYY')=$ex_data[5]";
	if( $inq_date!="" )  $inquery_date.= " and inquery_date='".change_date_format($inq_date,'yyyy-mm-dd',"-",1)."'";

	$sql_cond='';
	$inquery_id_cond='';
	$request_no='';
	if($ex_data[7]==1)
		{

		   if(str_replace("'","",$txt_style)!="")  $sql_cond="and  style_refernce='".str_replace("'","",$txt_style)."'";
		   if (trim($ex_data[4])!="")  $inquery_id_cond=" and system_number_prefix_num='$ex_data[4]'  $year_cond";
		   if (trim($ex_data[6])!="") $request_no=" and buyer_request='$ex_data[6]'";
		}

	if($ex_data[7]==4 || $ex_data[7]==0)
		{
		  if(str_replace("'","",$txt_style)!="")  $sql_cond="and  style_refernce like '%".str_replace("'","",$txt_style)."%' ";
		  if (trim($ex_data[4])!="") $inquery_id_cond=" and system_number_prefix_num like '%$ex_data[4]%' $year_cond";
		  if (trim($ex_data[6])!="") $request_no=" and buyer_request like '%$ex_data[6]%' ";
		}

	if($ex_data[7]==2)
		{
		  if(str_replace("'","",$txt_style)!="")  $sql_cond="and  style_refernce like '".str_replace("'","",$txt_style)."%' ";
		  if (trim($ex_data[4])!="") $inquery_id_cond=" and system_number_prefix_num like '$ex_data[4]%' $year_cond";
		  if (trim($ex_data[6])!="") $request_no=" and buyer_request like '$ex_data[6]%' ";
		}

	if($ex_data[7]==3)
		{
		  if(str_replace("'","",$txt_style)!="")  $sql_cond="and  style_refernce like '%".str_replace("'","",$txt_style)."' ";
		  if (trim($ex_data[4])!="") $inquery_id_cond=" and system_number_prefix_num like '%$ex_data[4]' $year_cond";
		  if (trim($ex_data[6])!="") $request_no=" and buyer_request like '%$ex_data[6]' ";
		}
	$season_buyer_wise_arr = return_library_array("select id,season_name from  lib_buyer_season ","id","season_name");
	$buyer_arr = return_library_array("select id,buyer_name from  lib_buyer ","id","buyer_name");
	$company_arr = return_library_array("select id,company_name from lib_company","id","company_name");
	$arr=array(0=>$company_arr,1=>$buyer_arr,7=>$season_buyer_wise_arr);
	 $sql = "select system_number_prefix_num,system_number,buyer_request, company_id,buyer_id,season_buyer_wise,inquery_date,style_refernce,status_active,extract(year from insert_date) as year ,id from wo_quotation_inquery where is_deleted=0 $company_name $buyer_name $sql_cond $inquery_id_cond $request_no $inquery_date  order by system_number_prefix_num ";
	//echo $sql;
	echo create_list_view("list_view", "Company Name,Buyer Name,Inquery ID,Year,Buyer Inquery No,Style Reff., Inquery Date,Season","120,120,70,50,70,120,90,120","875","260",0, $sql , "js_set_value", "id", "", 1, "company_id,buyer_id,0,0,0,0,0,season_buyer_wise", $arr, "company_id,buyer_id,system_number_prefix_num,year,buyer_request,style_refernce,inquery_date,season_buyer_wise", "",'','0') ;
	?>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if($action=="populate_data_from_inquiry_search")
{
	$sql = sql_select("select  id,company_id,buyer_id,season_buyer_wise,inquery_date,style_refernce,department_name,remarks,dealing_marchant,gmts_item,est_ship_date,color,season from wo_quotation_inquery where id='$data' order by id");
	foreach($sql as $row)
	{
		echo "load_drop_down( 'requires/sample_requisition_with_booking_controller', '".$row[csf("company_id")]."', 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/sample_requisition_with_booking_controller', '".$row[csf("company_id")]."', 'load_drop_down_location', 'location_td' );load_drop_down( 'requires/sample_requisition_with_booking_controller', '".$row[csf("company_id")]."', 'load_drop_down_agent', 'agent_td' );load_drop_down( 'requires/sample_requisition_with_booking_controller','".$row[csf("buyer_id")]."', 'load_drop_down_season_buyer', 'season_td');load_drop_down( 'requires/sample_requisition_with_booking_controller','".$row[csf("buyer_id")]."', 'load_drop_down_sample_for_buyer', 'sample_td');load_drop_down( 'requires/sample_requisition_with_booking_controller', '".$row[csf("gmts_item")]."', 'load_drop_down_garment_item_for_after_order', 'item_id_1')\n";
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";
		//echo "$('#cbo_location_name').val('".$result[csf('location_name')]."');\n";
		echo "document.getElementById('txt_quotation_id').value = '".$row[csf("id")]."';\n";
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_id")]."';\n";
		echo "document.getElementById('txt_style_name').value = '".$row[csf("style_refernce")]."';\n";
		echo "document.getElementById('txt_remarks').value = '".$row[csf("remarks")]."';\n";
		echo "document.getElementById('cbo_product_department').value = '".$row[csf("department_name")]."';\n";
		echo "document.getElementById('cbo_season_name').value = '".$row[csf("season_buyer_wise")]."';\n";
		echo "document.getElementById('cbo_dealing_merchant').value = ".$row[csf("dealing_marchant")].";\n";
		echo "document.getElementById('txt_est_ship_date').value = '".change_date_format($row[csf("est_ship_date")],"dd-mm-yyyy","-")."';\n";

	}
}

if($action=="create_style_id_search_list_view")
{
	$data=explode('_',$data);
	if ($data[2]!=0) $company=" and a.company_name='$data[2]'"; else { echo "Please Select Company First."; die; }
	if ($data[3]!=0) $buyer=" and a.buyer_name='$data[3]'"; else $buyer="";//{ echo "Please Select Buyer First."; die; }
	if($data[0]==1)
		{
		   if (trim($data[1])!="") $style_id_cond=" and a.id='$data[1]'"; else $style_id_cond="";
		   if ($data[6]!="") $style_cond=" and a.style_ref_no='$data[6]'"; else $style_cond="";
		}

	if($data[0]==4 || $data[0]==0)
		{
		  if (trim($data[1])!="") $style_id_cond=" and a.id like '%$data[1]%' "; else $style_id_cond="";
		  if ($data[6]!="") $style_cond=" and a.style_ref_no like '%$data[6]%' "; else $style_cond="";
		}

	if($data[0]==2)
		{
		  if (trim($data[1])!="") $style_id_cond=" and a.id like '$data[1]%' "; else $style_id_cond="";
		  if ($data[6]!="") $style_cond=" and a.style_ref_no like '$data[6]%' "; else $style_cond="";
		}

	if($data[0]==3)
		{
		  if (trim($data[1])!="") $style_id_cond=" and a.id like '%$data[1]' "; else $style_id_cond="";
		  if ($data[6]!="") $style_cond=" and a.style_ref_no like '%$data[6]' "; else $style_cond="";
		}


	if($db_type==0)
	{
		if ($data[4]!="" &&  $data[5]!="") $estimated_shipdate  = "and a.estimated_shipdate  between '".change_date_format($data[4], "yyyy-mm-dd", "-")."' and a.'".change_date_format($data[5], "yyyy-mm-dd", "-")."'"; else $estimated_shipdate ="";
	}
	else if($db_type==2)
	{
		if ($data[4]!="" &&  $data[5]!="") $estimated_shipdate  = "and a.estimated_shipdate  between '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."' and a.'".change_date_format($data[5], "yyyy-mm-dd", "-",1)."'"; else $estimated_shipdate ="";
	}

	if ($data[7]!="") $job=" and a.job_no_prefix_num like '%$data[7]'"; else $job="";
	if ($data[8]!="") $order=" and b.po_number like '%$data[8]'"; else $order="";

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');

	$dealing_marchant=return_library_array( "select id, team_member_name from lib_mkt_team_member_info",'id','team_member_name');
	$team_leader=return_library_array( "select id, team_leader_name from lib_marketing_team",'id','team_leader_name');

	$arr=array (3=>$buyer_arr,5=>$product_dept,6=>$team_leader,7=>$dealing_marchant);
	$sql="";

	if($db_type==0)
	{
		$sql= "SELECT a.id,a.job_no_prefix_num,SUBSTRING_INDEX(a.`insert_date`, '-', 1) as year,a.company_name,a.buyer_name,a.style_ref_no,a.product_dept,a.team_leader,a.dealing_marchant,b.po_number from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 $company $buyer $style_id_cond $style_cond $job $order  order by a.id";
	}
	else if($db_type==2)
	{
		$sql= "SELECT a.id,a.job_no_prefix_num,to_char(a.insert_date,'YYYY') as year,a.company_name,a.buyer_name,a.style_ref_no,a.product_dept,a.team_leader,a.dealing_marchant,b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 $company $buyer $style_id_cond $style_cond $job $order order by a.id";
	}
	// echo $sql;die();
	echo create_list_view("list_view", "Year,Job No,PO Number,Buyer Name,Style Name,Product Department,Team Leader,Dealing Merchandiser", "60,80,120,140,100,90,90,90","960","240",0, $sql , "js_set_value", "id", "", 1, "0,0,0,buyer_name,0,product_dept,team_leader,dealing_marchant,0", $arr , "year,job_no_prefix_num,po_number,buyer_name,style_ref_no,product_dept,team_leader,dealing_marchant", "",'','0,0,0,0,0,0,0,0') ;

	exit();
}

if($action=="populate_data_from_search_popup")
{
	$res = sql_select("select * from wo_po_details_master where id=$data");

 	foreach($res as $result)
	{
		//echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_sample_requisition_mst_info',1);\n";
		echo "load_drop_down( 'requires/sample_requisition_with_booking_controller', '".$result[csf("company_name")]."', 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/sample_requisition_with_booking_controller', '".$result[csf("company_name")]."', 'load_drop_down_location', 'location_td' );load_drop_down( 'requires/sample_requisition_with_booking_controller', '".$result[csf("company_name")]."', 'load_drop_down_agent', 'agent_td' );load_drop_down( 'requires/sample_requisition_with_booking_controller', '".$result[csf("company_name")]."', 'load_drop_down_buyer', 'buyer_td');load_drop_down( 'requires/sample_requisition_with_booking_controller','".$result[csf("buyer_name")]."', 'load_drop_down_season_buyer', 'season_td');load_drop_down( 'requires/sample_requisition_with_booking_controller','".$result[csf("buyer_name")]."', 'load_drop_down_sample_for_buyer', 'sample_td');load_drop_down( 'requires/sample_requisition_with_booking_controller', '".$result[csf("gmts_item_id")]."', 'load_drop_down_garment_item_for_after_order', 'item_id_1');load_drop_down( 'requires/sample_requisition_with_booking_controller', '".$result[csf("garments_nature")]."', 'load_drop_down_fabric_nature_for_after_order', 'rf_fabric_nature_1');load_drop_down( 'requires/sample_requisition_with_booking_controller','".$result[csf("buyer_name")]."_".$result[csf('product_dept')]."', 'load_drop_down_sub_dep', 'sub_td' );load_drop_down( 'requires/sample_requisition_with_booking_controller', '".$result[csf("buyer_name")]."*1', 'load_drop_down_brand', 'brand_td');load_drop_down( 'requires/sample_requisition_with_booking_controller', '".$result[csf('team_leader')]."', 'cbo_dealing_merchant', 'div_marchant' );load_drop_down( 'requires/sample_requisition_with_booking_controller', '".$result[csf("company_name")]."', 'load_drop_down_party_type', 'party_type_td' );load_drop_down( 'requires/sample_requisition_with_booking_controller','".$result[csf('team_leader')]."' , 'cbo_factory_merchant', 'div_marchant_factory' );\n";
		//load_drop_down( 'requires/sample_requisition_with_booking_controller', '".$result[csf("item_number_id")]."', 'load_drop_down_trims_group_for_after_order', 'ra_trims_group_1');

		echo "$('#txt_quotation_id').val('".$result[csf('id')]."');\n";
		echo "$('#txt_quotation_job_no').val('".$result[csf('job_no')]."');\n";
		echo "$('#cbo_company_name').val('".$result[csf('company_name')]."');\n";
		echo "$('#cbo_location_name').val('".$result[csf('location_name')]."');\n";
		echo "$('#cbo_buyer_name').val('".$result[csf('buyer_name')]."');\n";
		//echo "document.getElementById('txt_quotation_id').value = '".$result[csf("quotation_id")]."';\n";
		echo "$('#txt_style_name').val('".$result[csf('style_ref_no')]."');\n";
		echo "$('#cbo_product_department').val('".$result[csf('product_dept')]."');\n";
		echo "$('#cbo_sub_dept').val('".$result[csf('pro_sub_dep')]."');\n";
		echo "$('#cbo_brand_id').val('".$result[csf('brand_id')]."');\n";
		echo "$('#cbo_season_year').val('".$result[csf('season_year')]."');\n";
		echo "$('#txt_item_catgory').val('".$result[csf('product_category')]."');\n";
		echo "$('#cbo_team_leader').val('".$result[csf('team_leader')]."');\n";
		echo "$('#cbo_factory_merchant').val('".$result[csf('factory_marchant')]."');\n";
		echo "$('#cbo_design_source_id').val('".$result[csf('design_source_id')]."');\n";
		echo "$('#cbo_qltyLabel').val('".$result[csf('qlty_label')]."');\n";
		echo "$('#cbo_quality_level').val('".$result[csf('quality_level')]."');\n";
		echo "$('#sustainability_standard').val('".$result[csf('sustainability_standard')]."');\n";
		echo "$('#cbo_fab_material').val('".$result[csf('fab_material')]."');\n";
		echo "$('#cbo_agent').val('".$result[csf('agent_name')]."');\n";
		echo "$('#cbo_client').val('".$result[csf('client_id')]."');\n";
		//echo "$('#cbo_season_name').val('".$result[csf('season')]."');\n";
		$season_id=0;
		if($result[csf("season_matrix")]!=0) $season_id=$result[csf("season_matrix")];
		else $season_id=$result[csf("season_buyer_wise")];

		echo "$('#cbo_season_name').val('".$result[csf('season_id')]."');\n";
		echo "$('#cbo_dealing_merchant').val('".$result[csf('dealing_marchant')]."');\n";
		echo "$('#txt_product_code').val('".$result[csf('product_code')]."');\n";
		echo "$('#txt_bhmerchant').val('".$result[csf('bh_merchant')]."');\n";

		//echo "$('#txt_est_ship_date').val('".change_date_format($result[csf('estimated_shipdate')],'dd-mm-yyyy','-')."');\n";
		echo "$('#txt_remarks').val('".$result[csf('remarks')]."');\n";

		//echo "$('#update_id').val('".$result[csf('id')]."');\n";


  	}
 	exit();
}

// if ($action=="cbo_factory_merchant")
// {
// 	echo create_drop_down( "cbo_factory_merchant", 150, "select a.id, a.team_member_name from lib_mkt_team_member_info a, lib_marketing_team b where a.team_id=b.id and a.team_id='$data' and b.team_type in (2) and a.status_active =1 and a.is_deleted=0 order by a.team_member_name","id,team_member_name", 1, "-- Select Team Member --", $selected, "" );
// 	exit();	
// }


if($action=="requisition_id_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Sample Requisition Info","../../../", 1, 1, $unicode);
	if($cbo_company_name>0) $isDis=1; else $isDis=0;
?>
	<script>
		$(document).ready(function(e) {
            $("#txt_search_common").focus();
        });
		function search_populate(str)
		{
			//alert(str);
			if(str==0)
			{
				document.getElementById('search_by_th_up').innerHTML="Enter Style ID";
				document.getElementById('search_by_td').innerHTML='<input	type="text"	name="txt_search_common" style="width:130px " class="text_boxes" id="txt_search_common"	value=""  />';
			}
			else if(str==1)
			{
				document.getElementById('search_by_th_up').innerHTML="Enter Style Name";
				document.getElementById('search_by_td').innerHTML='<input	type="text"	name="txt_search_common" style="width:130px " class="text_boxes" id="txt_search_common"	value=""  />';
			}
		}

		function js_set_value( mst_id )
		{
			document.getElementById('selected_job').value=mst_id;
			parent.emailwindow.hide();
		}
    </script>
</head>
<body>
	<div align="center" style="width:100%;" >
	<form name="searchsampledevelopmentfrm_1"  id="searchsampledevelopmentfrm_1" autocomplete="off">
        <table width="950" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead>
                <th colspan="10"><? echo create_drop_down( "cbo_search_category", 140, $string_search_type,'', 1, "-- Search Catagory --" ); ?></th>
            </thead>
            <thead>
                <th width="140" class="must_entry_caption">Company Name</th>
                <th width="157">Buyer Name</th>
                <th width="70">Requisition No</th>
                <th width="70">Booking No</th>
                <th width="70">Style ID</th>
                <th width="80">Style Name</th>
                <th width="90">Sample Stage</th>
                <th width="130" colspan="2">Requisition date</th>
                <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:80px;" /></th>
            </thead>
            <tr class="general">
                <td>
                    <input type="hidden" id="selected_job">
                    <? echo create_drop_down( "cbo_company_mst", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_credential_cond  order by company_name","id,company_name",1, "-- Select Company --", $cbo_company_name,"load_drop_down( 'sample_requisition_with_booking_controller', this.value, 'load_drop_down_buyer_req', 'buyer_td_req' );",$isDis ); ?> </td>
                <td id="buyer_td_req"><? echo create_drop_down( "cbo_buyer_name", 157, $blank_array,'', 1, "-- Select Buyer --" ); ?></td>
                <td><input type="text" style="width:60px" class="text_boxes" name="txt_requisition_num" id="txt_requisition_num"  /></td>
                <td><input type="text" style="width:60px" class="text_boxes" name="txt_booking_num" id="txt_booking_num"  /></td>
                <td><input type="text" style="width:60px" class="text_boxes"  name="txt_style_id" id="txt_style_id"  /></td>
                <td><input type="text" style="width:70px" class="text_boxes"  name="txt_style_name1" id="txt_style_name1"  /></td>
                <td><? echo create_drop_down( "cbo_sample_stage", 90, $sample_stage, "", 1, "-Select Stage-", $selected, "", "", "1,2,3","" ); ?></td>

                <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px" placeholder="From Date"></td>
                <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px" placeholder="To Date"></td>
                <td>
                    <input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_style_id').value+'_'+document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_style_name1').value+'_'+document.getElementById('txt_requisition_num').value+'_'+document.getElementById('cbo_sample_stage').value+'_'+document.getElementById('txt_booking_num').value+'_'+document.getElementById('cbo_year_selection').value, 'create_requisition_id_search_list_view', 'search_div', 'sample_requisition_with_booking_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:80px;" />
                </td>
            </tr>
            <tr>
                <td align="center" colspan="10" valign="middle"><? echo load_month_buttons(1);  ?></td>
            </tr>
        </table>
        <div id="search_div"></div>
    </form>
	</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action == "yarn_dtls_popup") 
{
	extract($_REQUEST);
	echo load_html_head_contents("Sample yarn dtls info","../../../", 1, 1, $unicode);
	/*$data = explode('**', $data);
	$yarnbreackdown = $data[0];
	$dtls_id = $data[1];
	$yarncountid = $data[2];
	$oldyarncountid = $data[3];
	$yarn_dtls = array();*/
	if($req_id != '')
	{
		$yarn_dtls = sql_select("SELECT id,samp_fab_dtls_id,determin_id, mst_id, count_id,copm_one_id, cons_ratio, type_id, cons_qnty from sample_development_yarn_dtls where is_deleted=0 and status_active=1 and mst_id =".$req_id." order by id");
	}
	$yarn_deter_min_id='';
	foreach ($yarn_dtls as $row) {
		$yarn_deter_min_id.=$row[csf('determin_id')].",";
	}
	$yarn_deter_min_id=rtrim($yarn_deter_min_id,",");
	$construction_arr=return_library_array( "select id, construction from lib_yarn_count_determina_mst where id in($yarn_deter_min_id)",'id','construction');


	?>
    <script>
	var permission='<? echo $permission;?>';
	function fnc_yarn_dtls( operation ){

		//alert(operation);
		var delete_cause="";
		if(operation==2){
			//release_freezing();
			alert('Not allowed');
				return;
		}

		var row_num=$('#tbl_yarn_cost tr').length;
		//release_freezing();

		var data_all="";
		for (var i=1; i<=row_num; i++){ //determinid_
			data_all=data_all+get_submitted_data_string('hiddenreqid*cbocount_'+i+'*yarndtlsid_'+i+'*cbocompone_'+i+'*percentone_'+i+'*cbotype_'+i+'*consqnty_'+i+'*determinid_'+i+'*sampfabdtldid_'+i,"../../../",i);
		}
		var data="action=save_update_delete_yarn_dtls&operation="+operation+'&total_row='+row_num+data_all+"&delete_cause="+delete_cause;
		http.open("POST","sample_requisition_with_booking_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_yarn_dtls_reponse;
	}

	function fnc_yarn_dtls_reponse(){
		if(http.readyState == 4){
			 var reponse=trim(http.responseText).split('**');
			 if(parseInt(trim(reponse[0]))==1 || parseInt(trim(reponse[0]))==2){
				alert("Update is successfully");
				//reset_form('','form_data_con','','');
				//release_freezing();
				parent.emailwindow.hide();
				//show_msg(trim(reponse[0]));
			 }


			// release_freezing();
		}
	}

		function fnc_close()
		{
			parent.emailwindow.hide();
		}
	</script>
 <body>
<div align="center" style="width:100%;" >
 <? echo load_freeze_divs ("../../../",$permission);  ?>
	<fieldset style="width:780px;" id="yarn_dtls_1">
    <label><b>Yarn Details</b> </label>
     <input style="width:125px;" type="hidden" class="text_boxes"  name="hiddenreqid" id="hiddenreqid" value="<? echo trim($req_id);  ?>" />
		<table width="780" cellspacing="0" class="rpt_table" border="0" rules="all">
			<thead>
		    	<tr>
		            <th width="100" class="must_entry_caption">Construction</th>
		        	<th width="60">Count</th>
		            <th width="100" class="must_entry_caption">Comp.</th>
		            <th width="50" class="must_entry_caption">%</th>
		            <th width="110">Type</th>
		            <th width="75" class="must_entry_caption">Cons Qnty</th>
		            </th>
		        </tr>
		    </thead>
		    <tbody id="tbl_yarn_cost" >
	<?
	$i=1;

		foreach ($yarn_dtls as $yarnData) {
		?>
		<tr id="yarncost_<? echo $i; ?>" align="center">
			  <td><? echo create_drop_down( "cboconstruction_".$i, 100, $construction_arr,"", 1, "-- Select --", $yarnData[csf('determin_id')], "",1,"" ); ?></td>
                <td>
               <? echo create_drop_down( "cbocount_".$i, 100, "select id,yarn_count from  lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count", "id,yarn_count",1,"-- Select Item --", $yarnData[csf('count_id')],""); ?>
                <input type="hidden" name="yarndtlsid_<? echo $i?>" id="yarndtlsid_<? echo $i?>" value="<? echo $yarnData[csf('id')]; ?>">
                 <input type="hidden" name="sampfabdtldid_<? echo $i?>" id="sampfabdtldid_<? echo $i?>" value="<? echo $yarnData[csf('samp_fab_dtls_id')]; ?>">
                 <input type="hidden" name="consratio_<? echo $i?>" id="consratio_<? echo $i?>" value="<? echo $yarnData[csf('cons_ratio')]; ?>">
                  <input type="hidden" name="determinid_<? echo $i?>" id="determinid_<? echo $i?>" value="<? echo $yarnData[csf('determin_id')]; ?>">
                </td>
                <td><? echo create_drop_down( "cbocompone_".$i, 100, $composition,"", 1, "-- Select --", $yarnData[csf('copm_one_id')], "",1,"" ); ?></td>
              
              
               <td><input type="text" id="percentone_<? echo $i; ?>"  name="percentone_<? echo $i; ?>" class="text_boxes" style="width:40px" onChange="control_composition(<? echo $i; ?>,this.id,'percent_one')" value="<? echo $yarnData[csf('cons_ratio')];  ?>" readonly/>
                </td>

                <td><? echo create_drop_down( "cbotype_".$i, 110, $yarn_type,"", 1, "-- Select --", $yarnData[csf('type_id')], "",$disabled,"" ); ?></td>
                <td>
                    <input type="text" id="consqnty_<? echo $i; ?>" name="consqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:65px" value="<? echo $yarnData[csf('cons_qnty')] ?>"  readonly />
                </td>
            </tr>
		<?
		$i++;
		}

	?>
			</tbody>
             <tr>
              	<td align="center" colspan="5">&nbsp;</td>
            </tr>
            <tr>
              	<td align="center" colspan="5">
					<?
                    echo load_submit_buttons( $permission, "fnc_yarn_dtls",1,0,"reset_form('yarn_dtls_1','','')",1);
                    ?>
            	</td>
            </tr>
            <tr>
              	<td align="center" colspan="5">
					 <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
            	</td>
            </tr>
	</table>
	</fieldset>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </div>
    </body>

	<?
	exit();
}
if ($action=="save_update_delete_yarn_dtls")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if ($operation==1)  // Update Here
	{
			$con = connect();
 			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			//hidden_req_id*cbocount_'+i+'*yarn_dtls_id_'+i+'*cbocompone_'+i+'*percentone_'+i+'*cbotype_'+i+'*consqnty_'


	$field_yarn_dtls_up="samp_fab_dtls_id*determin_id*count_id*copm_one_id*percent_one*type_id*cons_qnty*updated_by*update_date";

			$m=0;$yarn_data_array_dtls="";
			for ($i=1;$i<=$total_row;$i++) //Yarn Start here
		    {
				$hidden_req_id="hiddenreqid";
				$samp_fab_dtls_id="sampfabdtldid_".$i;
				$determin_id="determinid_".$i;
				$yarn_dtls_id="yarndtlsid_".$i;
				$percent_one="percentone_".$i;
				$consqnty="consqnty_".$i;
				$count_id="cbocount_".$i;
				$copm_one_id="cbocompone_".$i;
				$determinid="determinid_".$i;
				$cbotype="cbotype_".$i;
				//if ($i!=1) $libyarncountdeterminationid .=",";
					//if ($m!=0) $yarn_data_array_dtls .=",";

				if (str_replace("'",'',$$yarn_dtls_id)!="")
				{
					$id_arr[]=str_replace("'",'',$$yarn_dtls_id);

					$yarn_data_dtls_up[str_replace("'",'',$$yarn_dtls_id)] =explode("*",("".$$samp_fab_dtls_id."*".$$determinid."*".$$count_id."*".$$copm_one_id."*".$$percent_one."*".$$cbotype."*".$$consqnty."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
					$m++;
				}
				 //foreach end
			}//For End



 			$flag=1;
 			if(count($yarn_data_dtls_up))
			{
				$rIDup=execute_query(bulk_update_sql_statement("sample_development_yarn_dtls", "id",$field_yarn_dtls_up,$yarn_data_dtls_up,$id_arr ));
				//echo "10**".bulk_update_sql_statement("sample_development_yarn_dtls", "id",$field_yarn_dtls_up,$yarn_data_dtls_up,$id_arr );die;
				if($rIDup) $flag=1; else $flag=0;
			}

			//echo "10**".$rIDs.'='.$rID1.'='.$rID_size_dlt.'='.$flag;die;



			if($db_type==0)
			{
				if($flag==1){
					mysql_query("COMMIT");
					echo "1**".str_replace("'",'',$hidden_req_id)."**2";
				}
				else{
					mysql_query("ROLLBACK");
					echo "10**";
				}
			}
			elseif($db_type==2 || $db_type==1 )
			{
				if($flag==1)
				{
					oci_commit($con);
					echo "1**".str_replace("'",'',$hidden_req_id)."**2";

				}
				else
				{
					oci_rollback($con);
					echo "10**";
				}
			}
			disconnect($con);
			die;
	}

	if ($operation==2)  // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		echo "10**";disconnect($con);die;
		if($db_type==0)
		{
			if($rID  && $update_dtls )
			{
				mysql_query("COMMIT");
				echo "2**".str_replace("'","",$update_id);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$update_id);
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID  && $update_dtls )
			{
				oci_commit($con);
				echo "2**".str_replace("'","",$update_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$update_id);
			}
		}
		disconnect($con);
	}
}

if($action=="create_requisition_id_search_list_view")
{
	$data=explode('_',$data);
	if ($data[2]!=0) $company=" and company_id='$data[2]'"; else { echo "Please Select Company First."; die; }
	if ($data[3]!=0) $buyer=" and buyer_name='$data[3]'"; else $buyer="";//{ echo "Please Select Buyer First."; die; }
	if($data[0]==1)
	{
		if (trim($data[1])!="") $style_id_cond=" and id='$data[1]'"; else $style_id_cond="";
		if ($data[6]!="") $style_cond=" and style_ref_no='$data[6]'"; else $style_cond="";
	}
	else if($data[0]==4 || $data[0]==0)
	{
		if (trim($data[1])!="") $style_id_cond=" and id like '%$data[1]%' "; else $style_id_cond="";
		if ($data[6]!="") $style_cond=" and style_ref_no like '%$data[6]%' "; else $style_cond="";
	}
	else if($data[0]==2)
	{
		if (trim($data[1])!="") $style_id_cond=" and id like '$data[1]%' "; else $style_id_cond="";
		if ($data[6]!="") $style_cond=" and style_ref_no like '$data[6]%' "; else $style_cond="";
	}
	else if($data[0]==3)
	{
		if (trim($data[1])!="") $style_id_cond=" and id like '%$data[1]' "; else $style_id_cond="";
		if ($data[6]!="") $style_cond=" and style_ref_no like '%$data[6]' "; else $style_cond="";
	}

	if($db_type==0)
	{
		if ($data[4]!="" &&  $data[5]!="") $estimated_shipdate  = "and requisition_date  between '".change_date_format($data[4], "yyyy-mm-dd", "-")."' and '".change_date_format($data[5], "yyyy-mm-dd", "-")."'"; else $estimated_shipdate ="";
	}
	else if($db_type==2)
	{
		if ($data[4]!="" &&  $data[5]!="") $estimated_shipdate  = "and requisition_date  between '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[5], "yyyy-mm-dd", "-",1)."'"; else $estimated_shipdate ="";
	}
	
	if ($data[7]!="") $requisition_num=" and requisition_number_prefix_num like '%$data[7]' "; else $requisition_num="";

	if ($data[8]!=0) $stage_id=" and sample_stage_id= '$data[8]' "; else  $stage_id="";
	if ($data[9]!=0) $book_cond=" and id in(SELECT b.style_id from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.status_active=1 and b.status_active=1 and a.booking_no_prefix_num='$data[9]' and a.entry_form_id=140) "; else  $book_cond="";
	
	$year_cond=" and to_char(insert_date,'YYYY')=$data[10]";

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');

	$dealing_marchant=return_library_array( "select id, team_member_name from lib_mkt_team_member_info",'id','team_member_name');
	$req_wise_booking=return_library_array( "select style_id, booking_no from wo_non_ord_samp_booking_dtls where status_active=1 and entry_form_id=140",'style_id','booking_no');
	$team_leader=return_library_array( "select id, team_leader_name from lib_marketing_team",'id','team_leader_name');

	$arr=array (2=>$buyer_arr,4=>$product_dept,5=>$dealing_marchant,6=>$sample_stage,7=>$req_wise_booking);
	$sql="";
	if($db_type==0)
	{
		$sql= "SELECT id, requisition_number_prefix_num, SUBSTRING_INDEX(insert_date, '-', 1) as year, company_id, buyer_name, style_ref_no, product_dept, dealing_marchant, sample_stage_id from sample_development_mst where entry_form_id=203 and status_active=1 and is_deleted=0 $company $buyer $style_id_cond $style_cond  $estimated_shipdate $requisition_num $stage_id $book_cond order by id DESC";
	}
	else if($db_type==2)
	{
		$sql= "SELECT id, requisition_number_prefix_num, to_char(insert_date,'YYYY') as year, company_id, buyer_name, style_ref_no, product_dept, dealing_marchant, sample_stage_id from sample_development_mst where entry_form_id=203 and  status_active=1 and is_deleted=0 $company $buyer $style_id_cond $style_cond $estimated_shipdate $requisition_num  $stage_id $book_cond $year_cond order by id DESC";
	}

	echo  create_list_view("list_view", "Year,Requisition No,Buyer Name,Style Name,Product Department,Dealing Merchandiser,Sample Stage,Booking No", "60,140,140,100,90,90,100,100","950","240",0, $sql , "js_set_value", "id", "", 1, "0,0,buyer_name,0,product_dept,dealing_marchant,sample_stage_id,id", $arr , "year,requisition_number_prefix_num,buyer_name,style_ref_no,product_dept,dealing_marchant,sample_stage_id,id", "",'','0,0,0,0,0,0') ;

	exit();
}


if($action=="populate_data_from_requisition_search_popup") 
{
	$res = sql_select("select id,company_id,location_id,buyer_name,style_ref_no,product_dept,agent_name,dealing_marchant,bh_merchant,season,buyer_ref,estimated_shipdate,remarks,requisition_number,sample_stage_id,requisition_date,material_delivery_date,quotation_id,is_acknowledge as is_approved,req_ready_to_approved, copy_from, fabric_material_id, sustainability_std_id, order_nature_id, quality_level_id, design_source_id, factory_merchant, team_leader, client_id, item_catgory, season_year, brand_id, sub_dept_id, style_desc,qrr_date,fit_id,internal_ref,control_no from sample_development_mst where id=$data and entry_form_id=203 and is_deleted=0 and status_active=1");
	$sample_st=$res[0][csf("sample_stage_id")];
	$quotation_info=$res[0][csf("quotation_id")];
	$company_id=$res[0][csf("company_id")];
	
	echo "get_php_form_data( '".$company_id."', 'company_wise_report_button_setting','requires/sample_requisition_with_booking_controller' );\n";
	
	if($sample_st==1)
	{
		$job_arr=array();
		$job_sql="select job_no, id, company_name, buyer_name, style_ref_no, product_dept, location_name, agent_name, dealing_marchant, bh_merchant, season_matrix, season_buyer_wise,gmts_item_id,garments_nature from wo_po_details_master where is_deleted=0 and status_active=1 and id=$quotation_info";
		$job_sql_res=sql_select($job_sql);
		foreach($job_sql_res as $jrow)
		{
			$season_id=0;
			if($jrow[csf("season_matrix")]!=0) $season_id=$jrow[csf("season_matrix")];
			else $season_id=$jrow[csf("season_buyer_wise")];

			$job_arr[$jrow[csf("id")]]['company']=$jrow[csf("company_name")];

			$job_arr[$jrow[csf("id")]]['buyer']=$jrow[csf("buyer_name")];
			$job_arr[$jrow[csf("id")]]['style']=$jrow[csf("style_ref_no")];
			$job_arr[$jrow[csf("id")]]['dept']=$jrow[csf("product_dept")];
			$job_arr[$jrow[csf("id")]]['loaction']=$jrow[csf("location_name")];
			$job_arr[$jrow[csf("id")]]['agent']=$jrow[csf("agent_name")];
			$job_arr[$jrow[csf("id")]]['dmarchant']=$jrow[csf("dealing_marchant")];
			$job_arr[$jrow[csf("id")]]['bh']=$jrow[csf("bh_merchant")];
			$job_arr[$jrow[csf("id")]]['gmts']=$jrow[csf("gmts_item_id")];
			$job_arr[$jrow[csf("id")]]['gmtsnature']=$jrow[csf("garments_nature")];
			$job_arr[$jrow[csf("id")]]['season']=$season_id;
			$job_no=$jrow[csf("job_no")];
		}
	 	unset($job_sql_res);
		 $is_booking = sql_select("SELECT a.booking_no,a.is_approved, a.fabric_source, a.currency_id, a.pay_mode, a.booking_date, a.team_leader, a.dealing_marchant, a.remarks, a.ready_to_approved from wo_booking_mst a join wo_booking_dtls b on a.id=b.booking_mst_id where b.job_no='$job_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form_id=140 and b.booking_type=4 and b.is_short=2 group by a.is_approved,a.booking_no, a.fabric_source, a.currency_id, a.pay_mode, a.booking_date, a.team_leader, a.dealing_marchant, a.remarks, a.ready_to_approved");

	}
	if($sample_st==2 || $sample_st==3) //&& $quotation_info
	{
		$inq_arr=array();
		$inq_sql="select id,company_id,buyer_id,season_buyer_wise,inquery_date,style_refernce,department_name,remarks,dealing_marchant,gmts_item,est_ship_date,color,season from wo_quotation_inquery where is_deleted=0 and status_active=1";
		$inq_sql_res=sql_select($inq_sql);
		foreach($inq_sql_res as $Inqrow)
		{
			$inq_arr[$Inqrow[csf("id")]]['company']=$Inqrow[csf("company_id")];
			$inq_arr[$Inqrow[csf("id")]]['buyer']=$Inqrow[csf("buyer_id")];
			$inq_arr[$Inqrow[csf("id")]]['style']=$Inqrow[csf("style_refernce")];
			//$inq_arr[$Inqrow[csf("id")]]['dept']=$Inqrow[csf("department_name")];
			$inq_arr[$Inqrow[csf("id")]]['dmarchant']=$Inqrow[csf("dealing_marchant")];
			$inq_arr[$Inqrow[csf("id")]]['gmts']=$Inqrow[csf("gmts_item")];
			$inq_arr[$Inqrow[csf("id")]]['season']=$Inqrow[csf("season")];
			$inq_arr[$Inqrow[csf("id")]]['est']=$Inqrow[csf("est_ship_date")];
			$inq_arr[$Inqrow[csf("id")]]['remarks']=$Inqrow[csf("remarks")];
		}
		unset($inq_sql_res);
//
		$is_booking = sql_select("SELECT a.booking_no,a.is_approved,a.currency_id,a.fabric_source,a.pay_mode,a.team_leader,a.dealing_marchant,a.ready_to_approved from wo_non_ord_samp_booking_dtls b,wo_non_ord_samp_booking_mst a where a.booking_no=b.booking_no and  b.style_id=$data and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.entry_form_id=140 group by a.booking_no,a.is_approved,a.currency_id,a.fabric_source,a.pay_mode,a.team_leader,a.dealing_marchant,a.ready_to_approved  ");
		 
	}


	  
	 //clearstatcache();


 	foreach($res as $result) 
	{

		echo "load_drop_down( 'requires/sample_requisition_with_booking_controller', '".$result[csf("company_id")]."', 'load_drop_down_buyer', 'buyer_td' );\n";
		$location_td='';
		$sql="select location_name,id from lib_location where company_id='".$result[csf("company_id")]."' and is_deleted=0  and status_active=1 $location_credential_cond  order by location_name";
		if(count(sql_select($sql))==1)
		{
			$location_td=create_drop_down( "cbo_location_name", 130, $sql,'id,location_name', 0, '--- Select Location ---', 0, ""  );
		}
		else
		{
			$location_td=create_drop_down( "cbo_location_name", 130, $sql,'id,location_name', 1, '--- Select Location ---', 0, ""  );
		}
		echo "document.getElementById('location_td').innerHTML = '".$location_td."';\n";

		$agent_td=create_drop_down( "cbo_agent", 130, "SELECT a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b,lib_buyer_party_type c where b.buyer_id=c.buyer_id and  a.status_active =1 and a.is_deleted=0 and c.party_type in(20,21) and b.buyer_id=a.id and b.tag_company='".$result[csf("company_id")]."' group by  a.id,a.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Agent --", $selected, "" );
		echo "document.getElementById('agent_td').innerHTML = '".$agent_td."';\n";

		$sql="select a.id,a.season_name from lib_buyer_season a where a.status_active =1 and a.is_deleted=0 and a.buyer_id='".$result[csf("buyer_name")]."'";
		if(count(sql_select($sql))==1)
		{
			$season_td=create_drop_down( "cbo_season_name", 130, $sql,'id,season_name', 0, '--- Select Season ---', 1, ""  );
		}
		else
		{
			$season_td=create_drop_down( "cbo_season_name", 130, $sql,'id,season_name', 1, '--- Select Season ---', 0, ""  );
		}
		echo "document.getElementById('season_td').innerHTML = '".$season_td."';\n";

		$sql="select a.id,a.sample_name,b.id as dtls_id from  lib_sample a ,sample_development_dtls b where  a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.id=b.sample_name and b.entry_form_id=203 and b.sample_mst_id='".$result[csf("id")]."' group by a.id,a.sample_name,b.id order by b.id";
		$samp_array=array();
		$samp_result=sql_select($sql);
		if(count($samp_result)>0)
		{
			foreach($samp_result as $keys=>$vals)
			{
				$samp_array[$vals[csf("id")]]=$vals[csf("sample_name")];
			}

		}
		$div_marchant= create_drop_down( "cbo_dealing_merchant", 130, "select id,team_member_name from lib_mkt_team_member_info where team_id='".$result[csf("team_leader")]."' and status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select Team Member --", $selected, "" );
		echo "document.getElementById('div_marchant').innerHTML = '".$div_marchant."';\n";

		$div_marchant_factory= create_drop_down( "cbo_factory_merchant", 130, "select id,team_member_name from lib_mkt_team_member_info where team_id='".$result[csf("team_leader")]."' and status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select Team Member --", $selected, "" );
		echo "document.getElementById('div_marchant_factory').innerHTML = '".$div_marchant_factory."';\n";


		echo "load_drop_down( 'requires/sample_requisition_with_booking_controller', '".$result[csf("buyer_name")]."*1*".$result[csf('brand_id')]."', 'load_drop_down_brand', 'brand_td');\n";	
		echo "sub_dept_load('".$row[csf("buyer_id")]."','".$row[csf("prod_dept")]."');\n";
 		echo "$('#txt_requisition_id').val('".$result[csf('requisition_number')]."');\n";
		echo "$('#cbo_sample_stage').val('".$result[csf('sample_stage_id')]."');\n";
		echo "$('#txt_requisition_date').val('".change_date_format($result[csf('requisition_date')],'dd-mm-yyyy','-')."');\n";
		echo "$('#txt_qrr_date').val('".change_date_format($result[csf('qrr_date')],'dd-mm-yyyy','-')."');\n";
		echo "$('#txt_material_dlvry_date').val('".change_date_format($result[csf('material_delivery_date')],'dd-mm-yyyy','-')."');\n";
		echo "$('#update_id').val('".$result[csf('id')]."');\n";
		echo "$('#cbo_ready_to_approved').val('".$result[csf('req_ready_to_approved')]."');\n";
		echo "$('#txt_copy_form').val('".$result[csf('copy_from')]."');\n";
		echo "$('#cbo_fab_material').val('".$result[csf('fabric_material_id')]."');\n";
		echo "$('#sustainability_standard').val('".$result[csf('sustainability_std_id')]."');\n";
		echo "$('#cbo_quality_level').val('".$result[csf('order_nature_id')]."');\n";
		echo "$('#cbo_qltyLabel').val('".$result[csf('quality_level_id')]."');\n";
		echo "$('#cbo_design_source_id').val('".$result[csf('design_source_id')]."');\n";
		echo "$('#cbo_dealing_merchant').val('".$result[csf('dealing_marchant')]."');\n";
		echo "$('#cbo_factory_merchant').val('".$result[csf('factory_merchant')]."');\n";
		echo "$('#cbo_team_leader').val('".$result[csf('team_leader')]."');\n";
		echo "$('#cbo_client').val('".$result[csf('client_id')]."');\n";
		echo "$('#cbo_fit_id').val('".$result[csf('fit_id')]."');\n";
		echo "$('#txt_internal_ref').val('".$result[csf('internal_ref')]."');\n";
		echo "$('#txt_control_no').val('".$result[csf('control_no')]."');\n";
		echo "$('#txt_item_catgory').val('".$result[csf('item_catgory')]."');\n";
		echo "$('#cbo_season_year').val('".$result[csf('season_year')]."');\n";
		echo "$('#cbo_brand_id').val('".$result[csf('brand_id')]."');\n";
		echo "$('#cbo_sub_dept').val('".$result[csf('sub_dept_id')]."');\n";
		echo "$('#txt_style_desc').val('".$result[csf('style_desc')]."');\n";

		$buyer_id='';
		if($result[csf('sample_stage_id')]==1)
		{
			echo "$('#txt_quotation_id').val('".$result[csf('quotation_id')]."');\n";
			echo "$('#cbo_company_name').val('".$job_arr[$result[csf("quotation_id")]]['company']."');\n";
			echo "$('#cbo_location_name').val('".$job_arr[$result[csf("quotation_id")]]['loaction']."');\n";
			echo "$('#cbo_buyer_name').val('".$job_arr[$result[csf("quotation_id")]]['buyer']."');\n";
			$buyer_id=$job_arr[$result[csf("quotation_id")]]['buyer'];
			echo "$('#txt_style_name').val('".$job_arr[$result[csf("quotation_id")]]['style']."');\n";
			echo "$('#cbo_product_department').val('".$job_arr[$result[csf("quotation_id")]]['dept']."');\n";
			echo "$('#cbo_agent').val('".$job_arr[$result[csf("quotation_id")]]['agent']."');\n";
			echo "$('#txt_product_code').val('".$result[csf('product_code')]."');\n";
			echo "$('#txt_bhmerchant').val('".$job_arr[$result[csf("quotation_id")]]['bh']."');\n";
			echo "$('#cbo_season_name').val('".$job_arr[$result[csf("quotation_id")]]['season']."');\n";
			echo "fnc_browse_style('".$result[csf('sample_stage_id')]."');\n";

		}

		else if($result[csf('sample_stage_id')]==2  && ($result[csf('quotation_id')]))
		{
			echo "$('#txt_quotation_id').val('".$result[csf('quotation_id')]."');\n";
			echo "$('#cbo_company_name').val('".$inq_arr[$result[csf("quotation_id")]]['company']."');\n";
			echo "$('#cbo_location_name').val('".$result[csf('location_id')]."');\n";
			echo "$('#cbo_buyer_name').val('".$inq_arr[$result[csf("quotation_id")]]['buyer']."');\n";
			$buyer_id=$inq_arr[$result[csf("quotation_id")]]['buyer'];
			echo "$('#txt_style_name').val('".$inq_arr[$result[csf("quotation_id")]]['style']."');\n";
			echo "$('#cbo_product_department').val('".$result[csf('product_dept')]."');\n";
			echo "$('#cbo_season_name').val('".$inq_arr[$result[csf("quotation_id")]]['season']."');\n";
			echo "$('#txt_est_ship_date').val('".$inq_arr[$result[csf("quotation_id")]]['est']."');\n";
			echo "$('#cbo_season_name').val('".$result[csf('season')]."');\n";
			echo "$('#txt_remarks').val('".$inq_arr[$result[csf("quotation_id")]]['remarks']."');\n";
			echo "fnc_browse_style('".$result[csf('sample_stage_id')]."');\n";
			echo "$('#txt_style_name').removeAttr('readonly','');\n";

		}
		else if($result[csf('sample_stage_id')]==3 && ($result[csf('quotation_id')]))
		{
			echo "$('#txt_quotation_id').val('".$result[csf('quotation_id')]."');\n";
			echo "$('#cbo_company_name').val('".$inq_arr[$result[csf("quotation_id")]]['company']."');\n";
			echo "$('#cbo_location_name').val('".$result[csf('location_id')]."');\n";
			echo "$('#cbo_buyer_name').val('".$inq_arr[$result[csf("quotation_id")]]['buyer']."');\n";
			$buyer_id=$inq_arr[$result[csf("quotation_id")]]['buyer'];
			echo "$('#txt_style_name').val('".$inq_arr[$result[csf("quotation_id")]]['style']."');\n";
			echo "$('#cbo_product_department').val('".$result[csf('product_dept')]."');\n";
			echo "$('#cbo_season_name').val('".$inq_arr[$result[csf("quotation_id")]]['season']."');\n";
			echo "$('#txt_est_ship_date').val('".$inq_arr[$result[csf("quotation_id")]]['est']."');\n";
			echo "$('#cbo_season_name').val('".$result[csf('season')]."');\n";
			echo "$('#txt_remarks').val('".$inq_arr[$result[csf("quotation_id")]]['remarks']."');\n";
			echo "fnc_browse_style('".$result[csf('sample_stage_id')]."');\n";
			echo "$('#txt_style_name').removeAttr('readonly','');\n";

		}
 		else
		{
			echo "$('#cbo_company_name').val('".$result[csf('company_id')]."');\n";
			echo "$('#cbo_location_name').val('".$result[csf('location_id')]."');\n";
			echo "$('#cbo_buyer_name').val('".$result[csf('buyer_name')]."');\n";
			$buyer_id=$result[csf('buyer_name')];
			echo "$('#txt_style_name').val('".$result[csf('style_ref_no')]."');\n";
			echo "$('#cbo_product_department').val('".$result[csf('product_dept')]."');\n";
			echo "$('#txt_style_name').removeAttr('readonly','');\n";

		}
		if(!empty($buyer_id))
		{
			$sql_sam=sql_select("SELECT id, sample_name, gmts_item_id, smv,article_no, sample_color, sample_prod_qty, submission_qty, delv_start_date, delv_end_date, sample_charge, sample_curency, size_data,fabric_status,acc_status,embellishment_status,sent_to_buyer_date,comments,fab_status_id from sample_development_dtls where entry_form_id=203 and sample_mst_id='".$result[csf('id')]."' and  is_deleted=0  and status_active=1 order by id ASC");
			if(count($sql_sam)==0)
			{
				echo "load_drop_down( 'requires/sample_requisition_with_booking_controller', ".$buyer_id.", 'load_drop_down_sample_for_buyer', 'sample_td');\n";
			}
		}
		
		echo "$('#txt_est_ship_date').val('".change_date_format($result[csf('estimated_shipdate')],'dd-mm-yyyy','-')."');\n";
		echo "$('#txt_remarks').val('".$result[csf('remarks')]."');\n";
		echo "$('#txt_buyer_ref').val('".$result[csf('buyer_ref')]."');\n";
		echo "$('#txt_bhmerchant').val('".$result[csf('bh_merchant')]."');\n";
		echo "$('#cbo_agent').val('".$result[csf('agent_name')]."');\n";
		echo "$('#cbo_season_name').val('".$result[csf('season')]."');\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_sample_requisition_mst_info',1);\n";
		echo "$('#approvedMsg').html('');\n";
		echo "$('#booking_approvedMsg').html('');\n";
		
 		//echo "$('#sample_dtls').removeProp('disabled')".";\n";
		echo "$('#required_fab_dtls').removeProp('disabled')".";\n";
		echo "$('#required_accessories_dtls').removeProp('disabled')".";\n";
		echo "$('#required_embellishment_dtls').removeProp('disabled')".";\n";
		echo "$('#sample_dtls').removeProp('disabled')".";\n";
		if(count($is_booking)>0)
		{
			$is_approved=$is_booking[0][csf('is_approved')];
			echo "$('#approvedMsg').html('Booking found aganist this Requisition!!');\n";			
			if($sample_st==2 || $sample_st==3){
				echo "$('#txt_booking_no').val('".$is_booking[0][csf('booking_no')]."');\n";
				$remarks=return_field_value("remarks", "wo_non_ord_samp_booking_mst", "booking_no='".$is_booking[0][csf('booking_no')]."' and is_deleted=0  and status_active=1");
				echo "$('#txt_booking_remarks').val('".$remarks."');\n";
				//echo "$('#txt_booking_no').val('".$is_booking[0][csf('booking_no')]."');\n";
				echo "$('#cbo_currency').val('".$is_booking[0][csf('currency_id')]."');\n";
				echo "$('#cbo_fabric_source').val('".$is_booking[0][csf('fabric_source')]."');\n";
				echo "$('#cbo_pay_mode').val('".$is_booking[0][csf('pay_mode')]."');\n";
				echo "$('#cbo_team_leader_book').val('".$is_booking[0][csf('team_leader')]."');\n";
				echo "$('#cbo_dealing_merchant_book').val('".$is_booking[0][csf('dealing_marchant')]."');\n";
				echo "$('#cbo_ready_to_approved_book').val('".$is_booking[0][csf('ready_to_approved')]."');\n";
			//	echo "$('#txt_booking_remarks').val('".$is_booking[0][csf('remarks')]."');\n";
				
				if($is_approved==1 || $is_approved==3)
				{
				echo "$('#booking_approvedMsg').html('This Booking is Approved');\n";		
				echo "$('#txt_style_desc_book').attr('disabled','true')".";\n";
				echo "$('#cbo_currency').attr('disabled','true')".";\n";
				echo "$('#txt_exchange_rate').attr('disabled','true')".";\n";
				echo "$('#cbo_sources').attr('disabled','true')".";\n";
				echo "$('#cbo_pay_mode').attr('disabled','true')".";\n";
				echo "$('#cbo_supplier_name').attr('disabled','true')".";\n";
				echo "$('#cbo_dealing_merchant_book').attr('disabled','true')".";\n";
				echo "$('#txt_buyer_req_no').attr('disabled','true')".";\n";
				echo "$('#cbo_ready_to_approved_book').attr('disabled','true')".";\n";
				echo "$('#txt_revise_no').attr('disabled','true')".";\n";
				echo "$('#txt_attention').attr('disabled','true')".";\n";
				echo "$('#txt_booking_remarks').attr('disabled','true')".";\n";
				}
			}
			if($sample_st==1){ //booking_approvedMsg
				foreach($is_booking as $row){
					
					echo "$('#txt_booking_no').val('".$row[csf('booking_no')]."');\n";
					echo "$('#cbo_currency').val('".$row[csf('currency_id')]."');\n";
					echo "$('#cbo_fabric_source').val('".$row[csf('fabric_source')]."');\n";
					echo "$('#cbo_pay_mode').val('".$row[csf('pay_mode')]."');\n";
					echo "$('#cbo_team_leader_book').val('".$row[csf('team_leader')]."');\n";
					echo "$('#cbo_dealing_merchant_book').val('".$row[csf('dealing_marchant')]."');\n";
					echo "$('#cbo_ready_to_approved_book').val('".$row[csf('ready_to_approved')]."');\n";
					echo "$('#txt_booking_remarks').val('".$row[csf('remarks')]."');\n";
					$is_approved=$row[csf('is_approved')];
					if($is_approved==1 || $is_approved==3)
					{
					echo "$('#booking_approvedMsg').html('Booking Approved found aganist this Requisition!!');\n";		
					echo "$('#txt_style_desc_book').attr('disabled','true')".";\n";
					echo "$('#cbo_currency').attr('disabled','true')".";\n";
					echo "$('#txt_exchange_rate').attr('disabled','true')".";\n";
					echo "$('#cbo_sources').attr('disabled','true')".";\n";
					echo "$('#cbo_pay_mode').attr('disabled','true')".";\n";
					echo "$('#cbo_supplier_name').attr('disabled','true')".";\n";
					echo "$('#cbo_dealing_merchant_book').attr('disabled','true')".";\n";
					echo "$('#txt_buyer_req_no').attr('disabled','true')".";\n";
					echo "$('#cbo_ready_to_approved_book').attr('disabled','true')".";\n";
					echo "$('#txt_revise_no').attr('disabled','true')".";\n";
					echo "$('#txt_attention').attr('disabled','true')".";\n";
					echo "$('#txt_booking_remarks').attr('disabled','true')".";\n";
					}
				}
			}
			
		}
 		if($result[csf('is_approved')]==1  || $result[csf('is_approved')]==3)
		{

			echo "$('#approvedMsg').html('This Requisition is Approved by Authority..!!');\n";
			
			
  			echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_sample_requisition_mst_info',1,1);\n";
 			echo "$('#save1').removeClass('formbutton').addClass('formbutton_disabled');\n";
 			echo "$('#save1').removeAttr('onclick','fnc_sample_requisition_mst_info(0)');\n";
			echo "$('#cbo_sample_stage').attr('disabled','true')".";\n";
			echo "$('#txt_requisition_date').attr('disabled','true')".";\n";
			echo "$('#txt_style_name').attr('disabled','true')".";\n";
			echo "$('#cbo_company_name').attr('disabled','true')".";\n";
			echo "$('#cbo_location_name').attr('disabled','true')".";\n";
			echo "$('#cbo_buyer_name').attr('disabled','true')".";\n";
			echo "$('#cbo_season_name').attr('disabled','true')".";\n";
			echo "$('#cbo_product_department').attr('disabled','true')".";\n";
			echo "$('#cbo_dealing_merchant').attr('disabled','true')".";\n";
			echo "$('#cbo_agent').attr('disabled','true')".";\n";
			echo "$('#txt_buyer_ref').attr('disabled','true')".";\n";
			echo "$('#txt_bhmerchant').attr('disabled','true')".";\n";
			echo "$('#txt_est_ship_date').attr('disabled','true')".";\n";
			echo "$('#txt_remarks').attr('disabled','true')".";\n";
			echo "$('#cbo_ready_to_approved').attr('disabled','true')".";\n";
 			echo "$('#required_fab_dtls').prop('disabled','true')".";\n";
			echo "$('#sample_dtls').prop('disabled','true')".";\n";
			echo "$('#required_accessories_dtls').prop('disabled','true')".";\n";
			echo "$('#required_embellishment_dtls').prop('disabled','true')".";\n";

  		}
		else
		{
			echo "$('#cbo_sample_stage').removeAttr('disabled','')".";\n";
			echo "$('#txt_requisition_date').removeAttr('disabled','')".";\n";
			echo "$('#txt_style_name').removeAttr('disabled','')".";\n";
 			echo "$('#cbo_season_name').removeAttr('disabled','')".";\n";
 			//echo "$('#cbo_dealing_merchant').attr('disabled','true')".";\n";
 			echo "$('#txt_buyer_ref').removeAttr('disabled','')".";\n";
			echo "$('#txt_bhmerchant').removeAttr('disabled','')".";\n";
			echo "$('#txt_est_ship_date').removeAttr('disabled','')".";\n";
			echo "$('#txt_remarks').removeAttr('disabled','')".";\n";
			echo "$('#cbo_ready_to_approved').removeAttr('disabled','')".";\n";
		}
		
  	}
    unlink($res);
 	exit();
}

if($action=="all_remarks_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Remarks Info","../../../", 1, 1, $unicode);
	?>
    <script>
		function fnc_close( )
		{
			 var remarks_text_area=document.getElementById('remarks_text_area').value;
 			document.getElementById('txt_remarks').value=remarks_text_area;
			parent.emailwindow.hide();
		}
    </script>
    <div>
	    <form>
	    <table>
	    	<tr>
	    		<td><strong>Remarks</strong></td>
	    		<td><textarea id="remarks_text_area" style="border:1px solid grey;border-radius: 3px;"  rows="8" cols="50"><? echo $remarks;?></textarea></td>
	    	</tr>
	    	<tr>
	    		<td></td>
	    		<td align="center">
		    		<input type="hidden" id="txt_remarks" value="">
		    	 	<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
	    	 	</td>
	    	</tr>
	    </table>

	    </form>
	</div>

    <?

	exit();
}

if($action=="color_popup_bk")
{
	extract($_REQUEST);
	echo load_html_head_contents("Sample Color Info","../../../", 1, 1, $unicode);
	?>
    <script>
		function js_set_value( mst_id )
		{
			document.getElementById('txt_color_name').value=mst_id;
			//document.getElementById('txt_color_id').value=color_id;
			parent.emailwindow.hide();
		}
    </script>
    <input type="hidden" id="txt_color_name">
    <input type="hidden" id="txt_color_id">
    <?
	$lib_color_arr=return_library_array( "select id,color_name from lib_color", "id","color_name" );
	$job_arr=return_library_array( "select id,job_no from wo_po_details_master", "id","job_no" );
	$arr=array(1=>$lib_color_arr);
	if($style_db_id!='')
	{
		 $sql= "select b.color_name from wo_po_color_size_breakdown a, lib_color b where a.color_number_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.job_no_mst='".$job_arr[$style_db_id]."' group by b.color_name";

		echo  create_list_view("list_view", "Color Name", "150","220","240",0, $sql , "js_set_value", "color_name", "", 1, "0,0", $arr , "color_name","requires/sample_requisition_with_booking_controller", 'setFilterGrid("list_view",-1);' );
	}
	else
	{
		$sql= "select  color_name from lib_color where status_active=1 and is_deleted=0";

		echo  create_list_view("list_view", "color_name", "150","220","240",0, $sql , "js_set_value", "color_name", "", 1, "0", $arr , "color_name","requires/sample_requisition_with_booking_controller", 'setFilterGrid("list_view",-1);' );
	}
	exit();
}
if($action=="color_popup")
{
	echo load_html_head_contents("Sample Color Info","../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	?>
    <script>
		var selected_name = new Array();
		function check_all_data() {
			var tbl_row_count = document.getElementById( 'color_table' ).rows.length;
			tbl_row_count = tbl_row_count-1;
			if(document.getElementById('check_all').checked){
				for( var i = 1; i <= tbl_row_count; i++ ) {
					document.getElementById( 'search' + i ).style.backgroundColor = 'yellow';
					if( jQuery.inArray( $('#txtcolordata_' + i).val(), selected_name ) == -1 ) {
						selected_name.push($('#txtcolordata_' + i).val());
					}
				}
				var colordata='';
                for( var i = 0; i < selected_name.length; i++ ) {
                    colordata += selected_name[i] + '__';
                }
                colordata = colordata.substr( 0, colordata.length - 2 );
                $('#color_data').val( colordata );
			}else{
				for( var i = 1; i <= tbl_row_count; i++ ) {
					if(i%2==0  ){
						document.getElementById('search'+i).style.backgroundColor = '#FFFFFF';
					}
					if(i%2!=0 ){
						document.getElementById('search'+i).style.backgroundColor = '#E9F3FF';
					}
					for( var j = 0; j < selected_name.length; j++ ) {
							if( selected_name[j] == $('#txtcolordata_' + i).val() ) break;
					}
					selected_name.splice( j,1 );
				}
				var colordata='';
                for( var i = 0; i < selected_name.length; i++ ) {
                    colordata += selected_name[i] + '__';
                }
                colordata = colordata.substr( 0, colordata.length - 2 );
                $('#color_data').val( colordata );

			}

		}

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function onlyUnique(value, index, self) {
			return self.indexOf(value) === index;
		}

		function js_set_value( str ) {
			var tbl_row_count = document.getElementById( 'color_table' ).rows.length;
			tbl_row_count = tbl_row_count-1;
			if($("#search"+str).css("display") !='none'){
				if(str%2==0  ){
					toggle( document.getElementById( 'search' + str ), '#FFFFFF');
				}
				if(str%2!=0 ){
					toggle( document.getElementById( 'search' + str ), '#E9F3FF');
				}
				if( jQuery.inArray( $('#txtcolordata_' + str).val(), selected_name ) == -1 ) {
					selected_name.push($('#txtcolordata_' + str).val());
				}
				else{
					for( var i = 0; i < selected_name.length; i++ ) {
						if( selected_name[i] == $('#txtcolordata_' + str).val() ) break;
					}
					selected_name.splice( i,1 );
				}
			}
			
			var colordata='';
			for( var i = 0; i < selected_name.length; i++ ) {
				colordata += trim(selected_name[i]) + '__';
			}
			if(selected_name.length == tbl_row_count){
                document.getElementById("check_all").checked = true;
            }
            else{
                document.getElementById("check_all").checked = false;
            }
			
			colordata = colordata.substr( 0, colordata.length - 2 );
			 //alert(colordata);

			$('#color_data').val( colordata );
		}
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;">
        <input type="hidden" id="color_data" name="color_data" style="width:80px;"/>
        <? 
		$sql_tgroup=sql_select( "select id, item_name, order_uom,trim_uom,trim_type from lib_item_group where item_category=4 and is_deleted=0 and status_active=1 order by item_name"); 
		$lib_color_arr=return_library_array( "select id,color_name from lib_color", "id","color_name" );
		//echo $cbo_buyer_name;
		$sql_tag_buyer=sql_select("select a.color_name, a.id FROM lib_color a, lib_color_tag_buyer c where a.id=c.color_id and c.buyer_id=$cbo_buyer_name and a.status_active=1 and a.is_deleted=0");
		 foreach($sql_tag_buyer as $row)
		{
			$ColorIdArr[$row[csf('id')]]=$row[csf('id')];
		}
		//echo "select a.color_name, a.id FROM lib_color a, lib_color_tag_buyer c where a.id=c.color_id and c.buyer_id=$cbo_buyer_name and a.status_active=1 and a.is_deleted=0";
		//lib_color_tag_buyer
		$colorIdCond=implode(",",$ColorIdArr);
		if($style_db_id!='')
		{
			$color_sql= sql_select("SELECT b.id as color_id, b.color_name from wo_po_color_size_breakdown a, lib_color b where a.color_number_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.job_id=$style_db_id and b.id in($colorIdCond) group by b.color_name, b.id");
			//echo "SELECT b.id as color_id, b.color_name from wo_po_color_size_breakdown a, lib_color b where a.color_number_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.job_id=$style_db_id and b.id in($colorIdCond) group by b.color_name, b.id";
		}
		else
		{
			$color_sql= sql_select("SELECT  id as color_id, color_name from lib_color where status_active=1 and is_deleted=0 and id in($colorIdCond)");
		}
		?>
        <table width="250" cellspacing="0" class="rpt_table" border="0" rules="all">
            <thead>
            	<th width="40">SL</th>
            	<th  width="180">Color Name</th>
            </thead>
        </table>
        <table width="250" cellspacing="0" class="rpt_table" border="0" rules="all" id="color_table">
            <tbody>
				<?
                $i=1;
                foreach($color_sql as $row)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$str="";
					$str=$row[csf('color_id')].'***'.$row[csf('color_name')];
					?>
					<tr style="text-decoration:none;cursor: pointer;" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)" bgcolor="<? echo $bgcolor; ?>">
						<td width="40"><? echo $i; ?></td>
						<td  width="160"><? echo $row[csf('color_name')]; ?>
                        	<input type="hidden" name="txtcolordata_<? echo $i; ?>" id="txtcolordata_<? echo $i; ?>" value="<? echo $str; ?>"/>
                        </td>
					</tr>
					<?
					$i++;
                }
                ?>
            </tbody>
        </table>
        <table width="250" cellspacing="0" cellpadding="0" style="border:none" align="center">
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
    </div>
    </body>
	<script>setFilterGrid('color_table',-1);</script>
	</html>
	<?
	exit();
}
if ($action=="save_update_delete_sample_details")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$color_arr=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name" );

	if ($operation==0 || $operation==1)
	{
		$sql_sample_capacity="SELECT b.month_id,b.date_calc,b.capacity_pcs from sample_capacity_calc_mst a,sample_capacity_calc_dtls b where  a.id=b.mst_id and b.day_status=1 and b.capacity_pcs>0 and a.status_active=1  and company_id=$cbo_company_name and a.location_id=$cbo_location_name";
		$sql_sample_capacity_res=sql_select($sql_sample_capacity);
		foreach($sql_sample_capacity_res as $row)
		{
			//$date_calc=date('d-M-Y',strtotime($row[csf('date_calc')]));
			$date_calc=date("j-M-Y",strtotime($row[csf('date_calc')]));
			$sample_CapacityArr[$date_calc]+=$row[csf('capacity_pcs')];
		}
		
		$tot_sample_capacity_pcs=$tot_sample_prod_qty=0;$date_chkArr=array();
		for ($i=1;$i<=$total_row;$i++)
		    {
				$txtSampleProdQty="txtSampleProdQty_".$i;
				$txtBuyerSubDate="txtBuyerSubDate_".$i;
				$is_updated=str_replace("'","",$$txtisupdated);
				$Buyer_SubDate=str_replace("'","",$$txtBuyerSubDate);
				$SampleProdQty=str_replace("'","",$$txtSampleProdQty);
				$BuyerSubDate=date("j-M-Y",strtotime($Buyer_SubDate));
				if($date_chkArr[$BuyerSubDate]=='')
				{
				$tot_sample_capacity_pcs+=$sample_CapacityArr[$BuyerSubDate];
				$date_chkArr[$BuyerSubDate]=$BuyerSubDate;
				}
				//
				$tot_sample_prod_qty+=$SampleProdQty;
				 
			} //========For Loop End========
			if($tot_sample_capacity_pcs>0 && $tot_sample_prod_qty>$tot_sample_capacity_pcs)
			{
				echo "14**".str_replace("'",'',$update_id).'**'."Production capacity($tot_sample_capacity_pcs) not allow over sample required qty.=".$tot_sample_prod_qty;
				disconnect($con);die;
			}
			// echo "10**=A".$tot_sample_prod_qty.'='.$tot_sample_capacity_pcs.'='.$BuyerSubDate;die;
	}
				
	if ($operation==0)  // Insert Here
	{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
 			$id_dtls=return_next_id( "id", "sample_development_dtls", 1 ) ;
 			$field_array= "id,sample_mst_id,sample_name,gmts_item_id,smv,article_no,sample_color,sample_prod_qty,submission_qty,delv_start_date,delv_end_date,sent_to_buyer_date,comments,sample_charge,sample_curency,inserted_by,insert_date,status_active,is_deleted,entry_form_id,size_data,fabric_status,acc_status,embellishment_status,fab_status_id,acc_status_id,embellishment_status_id";

			$ids=return_next_id( "id","sample_development_size", 1 ) ;
			$field_array_size="id, mst_id, dtls_id,size_id,bh_qty,plan_qty,dyeing_qty,test_qty,self_qty,samp_dept_qty,test_fit_qty,others_qty,total_qty,inserted_by, insert_date, status_active, is_deleted";

			$add_ids=return_next_id( "id","sample_details_additional_value", 1 ) ;
			$field_array_additional="id, mst_id, dtls_id, print, printseq, embro, embroseq, wash, washseq, spworks, spworksseq, gmtsdying, gmtsdyingseq, aop, aopseq, bush, bushseq, peach, peachseq, yd, ydseq";

			for ($i=1;$i<=$total_row;$i++)
		    {
				$cboSampleName="cboSampleName_".$i;
				$cboGarmentItem="cboGarmentItem_".$i;
				$txtSmv="txtSmv_".$i;
				$txtArticle="txtArticle_".$i;
				$txtColor="txtColor_".$i;
				
				$txtSampleProdQty="txtSampleProdQty_".$i;
				$txtSubmissionQty="txtSubmissionQty_".$i;
				$txtDelvStartDate="txtDelvStartDate_".$i;
				$txtDelvEndDate="txtDelvEndDate_".$i;
				$txtBuyerSubDate="txtBuyerSubDate_".$i;
				$txtRemarks="txtRemarks_".$i;
				$txtChargeUnit="txtChargeUnit_".$i;
				$cboCurrency="cboCurrency_".$i;
				$txtAllData="txtAllData_".$i;
				$hiddenadditionalvaluedata="hiddenadditionalvaluedata_".$i;
				//$updateIdDtls="updateidsampledtl_".$i;
				$Remarks=str_replace("'","",$$txtRemarks);

				$str_rep=array("+", "&", "*", "(", ")", "=","'","\r", "\n",'"','#');
				$sam_remarks=str_replace($str_rep,' ',$Remarks);
				

				if(str_replace("'","",$$txtColor)!="")
				{
					if (!in_array(str_replace("'","",$$txtColor),$new_array_color))
					{
						$color_id = return_id( str_replace("'","",$$txtColor), $color_arr, "lib_color", "id,color_name","140");
						//echo $$txtColorName.'='.$color_id.'<br>';
						$new_array_color[$color_id]=str_replace("'","",$$txtColor);
					}
					else $color_id =  array_search(str_replace("'","",$$txtColor), $new_array_color);
				}
				else $color_id=0;


				if ($i!=1) $data_array .=",";
				$data_array .="(".$id_dtls.",".$update_id.",".$$cboSampleName.",".$$cboGarmentItem.",".$$txtSmv.",".$$txtArticle.",'".$color_id."',".$$txtSampleProdQty.",".$$txtSubmissionQty.",".$$txtDelvStartDate.",".$$txtDelvEndDate.",".$$txtBuyerSubDate.",'".$sam_remarks."',".$$txtChargeUnit.",".$$cboCurrency.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0,203,".$$txtAllData.",0,0,0,0,0,0)";


				$countsize=0; $ex_data="";

				$ex_data=explode("__",str_replace("'","",$$txtAllData));
				$countsize=count($ex_data);

				$data_array_size.='';
				foreach($ex_data as $size_data)
				{
					$size_name=""; $bhqty=0;  $dyqty=0; $testqty=0; $selfqty=0; $samp_deptqty=0; $testfitqty=0; $othersqty=0; $totalqty=0;
					$ex_size_data=explode("_",$size_data);
					$size_name=$ex_size_data[0];
					$bhqty=$ex_size_data[1];
					$plqty=$ex_size_data[2];
					$dyqty=$ex_size_data[3];
					$testqty=$ex_size_data[4];
					$selfqty=$ex_size_data[5];
					$samp_deptqty=$ex_size_data[6];
					$testfitqty=$ex_size_data[7];
					$othersqty=$ex_size_data[8];
					$totalqty=$ex_size_data[9];
					
					$othersqty=str_replace("'","",$othersqty);
					$totalqty=str_replace("'","",$totalqty);
					if($othersqty=='') $othersqty=0;	if($totalqty=='') $totalqty=0;


					if($size_name!="")
					{
						if (!in_array($size_name,$new_array_size))
						{
							$size_id = return_id( $size_name, $size_arr, "lib_size", "id,size_name","140");
							$new_array_size[$size_id]=str_replace("'","",$size_name);
						}
						else $size_id =  array_search($size_name, $new_array_size);
					}
					else $size_id=0;
					if($i==1) $add_comma=""; else $add_comma=",";

					$data_array_size.="$add_comma(".$ids.",".$update_id.",".$id_dtls.",".$size_id.",".$bhqty.",".$plqty.",".$dyqty.",".$testqty.",".$selfqty.",".$samp_deptqty.",".$testfitqty.",".$othersqty.",".$totalqty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
					$ids=$ids+1;
				}

				//$additional_data_arr=explode("__",str_replace("'","",$$hiddenadditionalvaluedata));
				$data_array_additional.='';
				if(str_replace("'","",$$hiddenadditionalvaluedata) !="")
				{
					/*foreach($additional_data_arr as $additional_data)
					{*/
						$additionalvalue_data=explode("_",str_replace("'","",$$hiddenadditionalvaluedata));
						$print = $additionalvalue_data[0];
						$printseq = $additionalvalue_data[1];
						$embro = $additionalvalue_data[2];
						$embroseq = $additionalvalue_data[3];
						$wash = $additionalvalue_data[4];
						$washseq = $additionalvalue_data[5];
						$spworks = $additionalvalue_data[6];
						$spworksseq = $additionalvalue_data[7];
						$gmtsdying = $additionalvalue_data[8];
						$gmtsdyingseq = $additionalvalue_data[9];
						$aop = $additionalvalue_data[10];
						$aopseq = $additionalvalue_data[11];
						$brush = $additionalvalue_data[12];
						$brushseq = $additionalvalue_data[13];
						$peach = $additionalvalue_data[14];
						$peachseq = $additionalvalue_data[15];
						$yd = $additionalvalue_data[16];
						$ydseq = $additionalvalue_data[17];

						if($i==1) $add_comma=""; else $add_comma=",";

						$data_array_additional.="$add_comma(".$add_ids.",".$update_id.",".$id_dtls.",".$print.",".$printseq.",".$embro.",".$embroseq.",".$wash.",".$washseq.",".$spworks.",".$spworksseq.",".$gmtsdying.",".$gmtsdyingseq.",".$aop.",".$aopseq.",".$brush.",".$brushseq.",".$peach.",".$peachseq.",".$yd.",".$ydseq.")";
						$add_ids=$add_ids+1;
					//}
				}
				$id_dtls=$id_dtls+1;
				//echo "insert into sample_development_size (".$field_array_size.") Values ".$data_array_size."";die;

		    }

 			//echo "5**"."INSERT INTO sample_development_size(".$field_array_size.")VALUES ".$data_array_size; die;
			$rID_1=sql_insert("sample_development_dtls",$field_array,$data_array,1);
			$rIDs=sql_insert("sample_development_size",$field_array_size,$data_array_size,1);
			$rID2=sql_insert("sample_details_additional_value",$field_array_additional,$data_array_additional,1);

			if($db_type==0)
			{
				if($rID_1){
					mysql_query("COMMIT");
					echo "0**".str_replace("'",'',$update_id)."**1";
				}
				else{
					mysql_query("ROLLBACK");
					echo "10**";
				}
			}
			else if($db_type==2 || $db_type==1 )
			{
				if($rID_1)
				{
					oci_commit($con);
					echo "0**".str_replace("'",'',$update_id)."**1";

				}
			else{
					oci_rollback($con);
					echo "10**";
				}
			}
			disconnect($con);
			die;

	}
	else if ($operation==1)  // Update Here
	{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}



 			$id_dtls=return_next_id( "id", "sample_development_dtls", 1);

			$field_array_up="sample_name*gmts_item_id*smv*article_no*sample_color*sample_prod_qty*submission_qty*delv_start_date*delv_end_date*sent_to_buyer_date*comments*sample_charge*sample_curency*updated_by*update_date*size_data";

			$field_array= "id, sample_mst_id, sample_name, gmts_item_id, smv,article_no, sample_color, sample_prod_qty, submission_qty, delv_start_date, delv_end_date,sent_to_buyer_date,comments,sample_charge, sample_curency, inserted_by, insert_date, status_active, is_deleted, entry_form_id, size_data,fabric_status,acc_status,embellishment_status,fab_status_id,acc_status_id,embellishment_status_id";
			$ids=return_next_id( "id","sample_development_size", 1 ) ;
			$field_array_size="id, mst_id, dtls_id,size_id,bh_qty,plan_qty,dyeing_qty,test_qty,self_qty,samp_dept_qty,test_fit_qty,others_qty,total_qty,inserted_by, insert_date, status_active, is_deleted";
			$add_ids=return_next_id( "id","sample_details_additional_value", 1 ) ;
			$field_array_additional="id, mst_id, dtls_id, print, printseq, embro, embroseq, wash, washseq, spworks, spworksseq, gmtsdying, gmtsdyingseq, aop, aopseq, bush, bushseq, peach, peachseq, yd, ydseq";

			$add_comma=0; $data_array=""; //echo "10**";
			$is_updated_flag=0;
			for ($i=1;$i<=$total_row;$i++)
		    {
				$cboSampleName="cboSampleName_".$i;
				$cboGarmentItem="cboGarmentItem_".$i;
				$txtSmv="txtSmv_".$i;
				$txtArticle="txtArticle_".$i;
				$txtColor="txtColor_".$i;
				$txtisupdated="txtisupdated_".$i;
				
				$txtSampleProdQty="txtSampleProdQty_".$i;
				$txtSubmissionQty="txtSubmissionQty_".$i;
				$txtDelvStartDate="txtDelvStartDate_".$i;
				$txtDelvEndDate="txtDelvEndDate_".$i;
				$txtChargeUnit="txtChargeUnit_".$i;
				$cboCurrency="cboCurrency_".$i;
				$updateIdDtls="updateidsampledtl_".$i;
				$txtAllData="txtAllData_".$i;
				$txtBuyerSubDate="txtBuyerSubDate_".$i;
				$txtRemarks="txtRemarks_".$i;
				$hiddenColorid="hiddenColorid_".$i;
				$hiddenadditionalvaluedata="hiddenadditionalvaluedata_".$i;
				
				$is_updated=str_replace("'","",$$txtisupdated);
				$GarmentItemId="";$SampleNameId="";$SamColorId="";
				$GarmentItemId=str_replace("'","",$$cboGarmentItem);
				$SampleNameId=str_replace("'","",$$cboSampleName);

				$Remarks=str_replace("'","",$$txtRemarks);

				$str_rep=array("+", "&", "*", "(", ")", "=","'","\r", "\n",'"','#');
				$sam_remarks=str_replace($str_rep,' ',$Remarks);

				//$SamColorId=str_replace("'","",$$hiddenColorid);

				if(str_replace("'","",$$txtColor)!="")
				{
					if (!in_array(str_replace("'","",$$txtColor),$new_array_color))
					{
						$color_id = return_id( str_replace("'","",$$txtColor), $color_arr, "lib_color", "id,color_name","140");
						//echo $$txtColorName.'='.$color_id.'<br>';
						$new_array_color[$color_id]=str_replace("'","",$$txtColor);
					}
					else $color_id =  array_search(str_replace("'","",$$txtColor), $new_array_color);
				}
				else $color_id=0;
				
				//echo str_replace("'",'',$$updateIdDtls);
				$prev_size_ids="SELECT dtls_id,size_id from sample_development_size where status_active=1 and is_deleted=0 and mst_id=$update_id";
				foreach(sql_select($prev_size_ids) as $key_id=>$key_val)
				{
					$previ_sample_sizeArr[$key_val[csf('dtls_id')]][$key_val[csf('size_id')]]=$key_val[csf('size_id')];
				}
				
				$prev_ids="SELECT id,fab_status_id,gmts_item_id,sample_name,sample_color from sample_development_dtls where status_active=1 and is_deleted=0 and sample_mst_id=$update_id order by id";
				$dtlsUpdate_id_array=array();$color_delete_arr=array();$previ_sample_colorArr=array();
				foreach(sql_select($prev_ids) as $key_id=>$key_val)
				{
					$dtlsUpdate_id_array[]=$key_val[csf('id')];
					$color_delete_arr[$key_val[csf('id')]]['fab_id']=$key_val[csf('fab_status_id')];
					$color_delete_arr[$key_val[csf('id')]]['sample_color']=$key_val[csf('sample_color')];
					
					$previ_sample_colorArr[$key_val[csf('id')]]['color']=$key_val[csf('sample_color')];
					$previ_sample_colorArr[$key_val[csf('id')]]['sample']=$key_val[csf('sample_name')];
					$previ_sample_colorArr[$key_val[csf('id')]]['item']=$key_val[csf('gmts_item_id')];
					
				
				}

				if (str_replace("'",'',$$updateIdDtls)!="")
				{
					$previ_sample_color=$previ_sample_name=$previ_gmts_item_id='';
					$previ_sample_color=$previ_sample_colorArr[str_replace("'",'',$$updateIdDtls)]['color'];
					$previ_sample_name=$previ_sample_colorArr[str_replace("'",'',$$updateIdDtls)]['sample'];
					$previ_gmts_item_id=$previ_sample_colorArr[str_replace("'",'',$$updateIdDtls)]['item'];
					$is_updated_flag=0;
						if($color_id!=$previ_sample_color)
						{
							$is_updated_flag=1;
						}
						elseif($SampleNameId!=$previ_sample_name)
						{
							$is_updated_flag=1;
						}
						elseif($GarmentItemId!=$previ_gmts_item_id)
						{
							$is_updated_flag=1;
						}
					//echo "10**=".$SampleNameId.'='.$previ_sample_name.'='.$is_updated_flag.'<br>'; 
					
					 
					
					$id_arr[]=str_replace("'",'',$$updateIdDtls);

					$data_array_up[str_replace("'",'',$$updateIdDtls)] =explode("*",("".$$cboSampleName."*".$$cboGarmentItem."*".$$txtSmv."*".$$txtArticle."*'".$color_id."'*".$$txtSampleProdQty."*".$$txtSubmissionQty."*".$$txtDelvStartDate."*".$$txtDelvEndDate."*".$$txtBuyerSubDate."*'".$sam_remarks."'*".$$txtChargeUnit."*".$$cboCurrency."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'*".$$txtAllData.""));

					$countsize=0; $ex_data="";
					$ex_data=explode("__",str_replace("'","",$$txtAllData));
					$countsize=count($ex_data);

					$data_array_size.='';
					foreach($ex_data as $size_data)
					{
						$size_name=""; $bhqty=0; $dyqty=0; $testqty=0; $selfqty=0; $totalqty=0;$samp_deptqty=0;$testfitqty=0;$othersqty=0; 
						$ex_size_data=explode("_",$size_data);
						$size_name=$ex_size_data[0];
						$bhqty=$ex_size_data[1];
						$plqty=$ex_size_data[2];
						$dyqty=$ex_size_data[3];
						$testqty=$ex_size_data[4];
						$selfqty=$ex_size_data[5];
						$samp_deptqty=$ex_size_data[6];
						$testfitqty=$ex_size_data[7];
						$othersqty=$ex_size_data[8];
						$totalqty=$ex_size_data[9];
						
						$othersqty=str_replace("'","",$othersqty);
						$totalqty=str_replace("'","",$totalqty);
						if($othersqty=='') $othersqty=0;	if($totalqty=='') $totalqty=0;

						if($size_name!="")
						{
							if (!in_array($size_name,$new_array_size))
							{
								$size_id = return_id( $size_name, $size_arr, "lib_size", "id,size_name","140");
								//echo $$txtColorName.'='.$color_id.'<br>';
								$new_array_size[$size_id]=str_replace("'","",$size_name);

							}
							else $size_id =  array_search($size_name, $new_array_size);
						}
						else $size_id=0;
						
						$previ_sample_size=$previ_sample_sizeArr[str_replace("'",'',$$updateIdDtls)][$size_id];
						if($size_id!=$previ_sample_size) //Size Update check here
						{
							$is_updated_flag=1;
						}
						 // echo "10**=".$size_id.'='.$previ_sample_size.'='.$is_updated_flag.'<br>'; 
						if($is_updated_flag==1)
						{
								$update_sample_dtls=execute_query("UPDATE sample_development_dtls set is_updated=$is_updated_flag where sample_mst_id=$update_id ",1);
								if($update_sample_dtls) $flag=1; else $flag=0;
								$update_all_dtls=execute_query("UPDATE sample_development_fabric_acc set is_updated=$is_updated_flag where sample_mst_id=$update_id ",1);
								if($update_all_dtls) $flag=1; else $flag=0;
						}

						if($i==1) $add_comma=""; else $add_comma=",";

						$data_array_size.="$add_comma(".$ids.",".$update_id.",".$$updateIdDtls.",".$size_id.",".$bhqty.",".$plqty.",".$dyqty.",".$testqty.",".$selfqty.",".$samp_deptqty.",".$testfitqty.",".$othersqty.",".$totalqty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
						$ids=$ids+1;
					}

					$fab_id_color=$color_delete_arr[str_replace("'",'',$$updateIdDtls)]['fab_id'];
					$hiddenColorid=str_replace("'","",$$hiddenColorid);
					$sample_color_id=$color_delete_arr[str_replace("'",'',$$updateIdDtls)]['sample_color'];
					if($color_id!=$sample_color_id)
					{
						$update_color_delete=execute_query("UPDATE sample_development_rf_color set status_active=0,is_deleted=1 where mst_id=$update_id and dtls_id=".$fab_id_color." and color_id=".$hiddenColorid."",1);
						if($update_color_delete) $flag=1; else $flag=0;
					}

					//$additional_data_arr=explode("__",str_replace("'","",$$hiddenadditionalvaluedata));
					$data_array_additional.='';
					if(str_replace("'","",$$hiddenadditionalvaluedata) !="")
					{
						/*foreach($additional_data_arr as $additional_data)
						{*/
							$additionalvalue_data=explode("_",str_replace("'","",$$hiddenadditionalvaluedata));
							$print = $additionalvalue_data[0];
							$printseq = $additionalvalue_data[1];
							$embro = $additionalvalue_data[2];
							$embroseq = $additionalvalue_data[3];
							$wash = $additionalvalue_data[4];
							$washseq = $additionalvalue_data[5];
							$spworks = $additionalvalue_data[6];
							$spworksseq = $additionalvalue_data[7];
							$gmtsdying = $additionalvalue_data[8];
							$gmtsdyingseq = $additionalvalue_data[9];
							$aop = $additionalvalue_data[10];
							$aopseq = $additionalvalue_data[11];
							$brush = $additionalvalue_data[12];
							$brushseq = $additionalvalue_data[13];
							$peach = $additionalvalue_data[14];
							$peachseq = $additionalvalue_data[15];
							$yd = $additionalvalue_data[16];
							$ydseq = $additionalvalue_data[17];

							if($i==1) $add_comma=""; else $add_comma=",";

							$data_array_additional.="$add_comma(".$add_ids.",".$update_id.",".$$updateIdDtls.",".$print.",".$printseq.",".$embro.",".$embroseq.",".$wash.",".$washseq.",".$spworks.",".$spworksseq.",".$gmtsdying.",".$gmtsdyingseq.",".$aop.",".$aopseq.",".$brush.",".$brushseq.",".$peach.",".$peachseq.",".$yd.",".$ydseq.")";
							$add_ids=$add_ids+1;
						//}
					}
					
				}
			 	else
				{
					if ($add_comma!=0) $data_array .=",";
					$data_array .="(".$id_dtls.",".$update_id.",".$$cboSampleName.",".$$cboGarmentItem.",".$$txtSmv.",".$$txtArticle.",'".$color_id."',".$$txtSampleProdQty.",".$$txtSubmissionQty.",".$$txtDelvStartDate.",".$$txtDelvEndDate.",".$$txtBuyerSubDate.",'".$sam_remarks."',".$$txtChargeUnit.",".$$cboCurrency.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0,203,".$$txtAllData.",0,0,0,0,0,0)";

					$countsize=0; $ex_data="";
					$ex_data=explode("__",str_replace("'","",$$txtAllData));
					$countsize=count($ex_data);

					$data_array_size.='';
					foreach($ex_data as $size_data)
					{
						$size_name=""; $bhqty=0; $dyqty=0; $testqty=0; $selfqty=0;$samp_deptqty=0;$testfitqty=0;$othersqty=0; $totalqty=0;
						$ex_size_data=explode("_",$size_data);
						$size_name=$ex_size_data[0];
						$bhqty=$ex_size_data[1];
						$plqty=$ex_size_data[2];
						$dyqty=$ex_size_data[3];
						$testqty=$ex_size_data[4];
						$selfqty=$ex_size_data[5];
						$samp_deptqty=$ex_size_data[6];
						$testfitqty=$ex_size_data[7];
						$othersqty=$ex_size_data[8];
						$totalqty=$ex_size_data[9];
						
						$othersqty=str_replace("'","",$othersqty);
						$totalqty=str_replace("'","",$totalqty);
						if($othersqty=='') $othersqty=0;	if($totalqty=='') $totalqty=0;

						if($size_name!="")
						{
							if (!in_array($size_name,$new_array_size))
							{
								$size_id = return_id( $size_name, $size_arr, "lib_size", "id,size_name","140");
								//echo $$txtColorName.'='.$color_id.'<br>';
								$new_array_size[$size_id]=str_replace("'","",$size_name);
							}
							else $size_id =  array_search($size_name, $new_array_size);
						}
						else $size_id=0;


						if($i==1) $add_comma=""; else $add_comma=",";

						$data_array_size.="$add_comma(".$ids.",".$update_id.",".$id_dtls.",".$size_id.",".$bhqty.",".$plqty.",".$dyqty.",".$testqty.",".$selfqty.",".$samp_deptqty.",".$testfitqty.",".$othersqty.",".$totalqty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
						$ids=$ids+1;
					}
					//$additional_data_arr=explode("__",str_replace("'","",$$hiddenadditionalvaluedata));
					$data_array_additional.='';
					if(str_replace("'","",$$hiddenadditionalvaluedata) !="")
					{
						/*foreach($additional_data_arr as $additional_data)
						{*/
							$additionalvalue_data=explode("_",str_replace("'","",$$hiddenadditionalvaluedata));
							$print = $additionalvalue_data[0];
							$printseq = $additionalvalue_data[1];
							$embro = $additionalvalue_data[2];
							$embroseq = $additionalvalue_data[3];
							$wash = $additionalvalue_data[4];
							$washseq = $additionalvalue_data[5];
							$spworks = $additionalvalue_data[6];
							$spworksseq = $additionalvalue_data[7];
							$gmtsdying = $additionalvalue_data[8];
							$gmtsdyingseq = $additionalvalue_data[9];
							$aop = $additionalvalue_data[10];
							$aopseq = $additionalvalue_data[11];
							$brush = $additionalvalue_data[12];
							$brushseq = $additionalvalue_data[13];
							$peach = $additionalvalue_data[14];
							$peachseq = $additionalvalue_data[15];
							$yd = $additionalvalue_data[16];
							$ydseq = $additionalvalue_data[17];

							if($i==1) $add_comma1=""; else $add_comma1=",";

							$data_array_additional.="$add_comma1(".$add_ids.",".$update_id.",".$id_dtls.",".$print.",".$printseq.",".$embro.",".$embroseq.",".$wash.",".$washseq.",".$spworks.",".$spworksseq.",".$gmtsdying.",".$gmtsdyingseq.",".$aop.",".$aopseq.",".$brush.",".$brushseq.",".$peach.",".$peachseq.",".$yd.",".$ydseq.")";
							$add_ids=$add_ids+1;
						//}
					}
					
					$id_dtls=$id_dtls+1;
					$add_comma++;
				}

		    } //For Loop End

			if(implode(',',$id_arr)!="")
			{
				$distance_delete_id=array_diff($dtlsUpdate_id_array,$id_arr);
			}
			else
			{
				$distance_delete_id=$dtlsUpdate_id_array;
			}



			$field_array_del="status_active*is_deleted*updated_by*update_date";
			$data_array_del="'0'*'1'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			if(implode(',',$distance_delete_id)!="")
			{

				foreach($distance_delete_id as $id_val)
				{
					$delDtls=sql_update("sample_development_dtls",$field_array_del,$data_array_del,"id","".$id_val."",1);
					if($delDtls) $flag=1; else $flag=0;

					$fab_id=$color_delete_arr[$id_val]['fab_id'];
					$sample_color=$color_delete_arr[$id_val]['sample_color'];

					if($flag==1)
					{
					$update_color_delete=execute_query("UPDATE sample_development_rf_color set status_active=0,is_deleted=1 where mst_id=$update_id and dtls_id=".$fab_id." and color_id=".$hiddenColorid."",1);
					if($update_color_delete) $flag=1; else $flag=0;
					}
				}
			}

		//	echo "10**XX";die;


			$flag=1;
			if($data_array!="")
			{
				$rID_dtls=sql_insert("sample_development_dtls",$field_array,$data_array,0);
				$rID_size=sql_insert("sample_development_size",$field_array_size,$data_array_size,1);
				if($rID_dtls && $rID_size) $flag=1; else $flag=0;
			}
			/*echo '=='.$data_array.'==';
			die;*/
			//echo "10**"."INSERT INTO sample_details_additional_value(".$field_array_additional.")VALUES ".$data_array_additional; die;
			if($data_array_up!="")
			{
				$rID_size_dlt=execute_query( "delete from sample_development_size where mst_id=$update_id",0);
				$rID_additional_dlt=execute_query( "delete from sample_details_additional_value where mst_id=$update_id",0);
				$rIDs=sql_insert("sample_development_size",$field_array_size,$data_array_size,1);
				$rID2=sql_insert("sample_details_additional_value",$field_array_additional,$data_array_additional,1);
				$rID1=execute_query(bulk_update_sql_statement("sample_development_dtls", "id",$field_array_up,$data_array_up,$id_arr ));
				if($rID1) $flag=1; else $flag=0;
			}


			if($txtDeltedIdSd!="" || $txtDeltedIdSd!=0)
			{

				//$fields="is_deleted";
				//$delDtls=sql_multirow_update("sample_development_dtls",$fields,"1","id",$txtDeltedIdSd,0);
 			 }


			if($db_type==0)
			{
				if($flag==1)
				{
					mysql_query("COMMIT");
					echo "1**".str_replace("'",'',$update_id)."**1";
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "10**";
				}
			}
			elseif($db_type==2 || $db_type==1 )
			{
				if($flag==1)
				{
					oci_commit($con);
					echo "1**".str_replace("'",'',$update_id)."**1";

				}
				else
				{
					oci_rollback($con);
					echo "10**";
				}
			}
			disconnect($con);
			die;
	}

	if ($operation==2)  // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$is_approved=return_field_value("is_approved","sample_development_mst","id=$update_id and entry_form_id=203 and status_active=1 and is_deleted=0");
		$next_process=sql_select("SELECT id from sample_development_fabric_acc where sample_mst_id=$update_id and status_active=1 and is_deleted=0");
		if(count($next_process)>0)
		{
			echo "321**";
			disconnect($con);die;
		}

		if( $is_approved==1)
		{
			echo "323**";
			disconnect($con);die;
		}


		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";

		$rID=sql_delete("sample_development_dtls",$field_array,$data_array,"sample_mst_id*entry_form_id","".$update_id."*203",0);
		if($rID)
		{
			$update_dtls=execute_query("UPDATE sample_development_size set status_active=0,is_deleted=1 where mst_id=$update_id",1);
		}
		if($db_type==0)
		{
			if($rID  && $update_dtls )
			{
				mysql_query("COMMIT");
				echo "2**".str_replace("'","",$update_id);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$update_id);
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID  && $update_dtls )
			{
				oci_commit($con);
				echo "2**".str_replace("'","",$update_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$update_id);
			}
		}
		disconnect($con);
	}


}

if ($action=="save_update_delete_required_fabric")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$color_arr=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name" );
	$string_text='';
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$id_dtls=return_next_id( "id", "sample_development_fabric_acc", 1 ) ;
		$field_array= "id,sample_mst_id,sample_name,gmts_item_id,process_loss_percent,grey_fab_qnty,delivery_date,fabric_source,remarks_ra,fin_fab_qnty,body_part_id,fabric_nature_id,fabric_description,gsm,dia,color_data,color_type_id,width_dia_id,uom_id,required_dzn,required_qty,inserted_by,insert_date,status_active,is_deleted,form_type,determination_id,collar_cuff_breakdown,yarn_dtls";
		$field_array_col="id, mst_id, dtls_id, color_id, contrast, fabric_color, qnty, process_loss_percent, grey_fab_qnty, inserted_by, insert_date, status_active, is_deleted"; 
		$field_array_coller="id, mst_id, dtls_id, sample_color, size_id, item_size, qnty_pcs, inserted_by, insert_date, status_active, is_deleted";
		$idColorTbl=return_next_id( "id","sample_development_rf_color", 1 ) ;
		$idCollerCuff=return_next_id( "id","sample_requisition_coller_cuff", 1 ) ;
		$yarn_deter_id="";
		$yar_details_data_arr="";
		$count_determina_arr=array();
		for ($i=1;$i<=$total_row;$i++)
		{
			$cboRfSampleName="cboRfSampleName_".$i;
			$cboRfGarmentItem="cboRfGarmentItem_".$i;
			$cboRfBodyPart="cboRfBodyPart_".$i;
			$cboRfFabricNature="cboRfFabricNature_".$i;
			$txtRfFabricDescription="txtRfFabricDescription_".$i;
			$txtRfGsm="txtRfGsm_".$i;
			$txtRfDia="txtRfDia_".$i;
			$txtRfColor="txtRfColor_".$i;
			$cboRfColorType="cboRfColorType_".$i;
			$cboRfWidthDia="cboRfWidthDia_".$i;
			$cboRfUom="cboRfUom_".$i;
			$txtRfReqDzn="txtRfReqDzn_".$i;
			$txtRfyarndtls="txtRfyarndtls_".$i;
			$txtRfReqQty="txtRfReqQty_".$i;
			$txtRfColorAllData="txtRfColorAllData_".$i;
			$libyarncountdeterminationid="libyarncountdeterminationid_".$i;
			$collercuffdata="hiddencollarCuffdata_".$i;

			$txtProcessLoss="txtProcessLoss_".$i;
			$txtGrayFabric="txtGrayFabric_".$i;
			$fabricDelvDate="fabricDelvDate_".$i;
			$cboRfFabricSource="cboRfFabricSource_".$i;
			$txtRfRemarks="txtRfRemarks_".$i;

			$yarn_deter_id.=str_replace("'","",$$libyarncountdeterminationid).',';
			$count_determina_arr[str_replace("'",'',$$libyarncountdeterminationid)]++;
			$yar_details_data_arr.=str_replace("'","",$$libyarncountdeterminationid).'##'.$id_dtls.'***';

			$libDeterId=str_replace("'",'',$$libyarncountdeterminationid);
			$fab_nature_arr[$libDeterId]=str_replace("'",'',$$cboRfFabricNature);
			$fab_greyQty_arr[$id_dtls]+=str_replace("'",'',$$txtGrayFabric);
			$fab_gsm_arr[$id_dtls]=str_replace("'",'',$$txtRfGsm);

			$str_rep=array("+", "&", "*", "(", ")", "=","'","\r", "\n",'"','#');
			$Remarks=str_replace("'","",$$txtRfRemarks);
			$str_rep=array("+", "&", "*", "(", ")", "=","'","\r", "\n",'"','#');
			$fab_remarks=str_replace($str_rep,' ',$Remarks);

			

			$ex_data="";
			$ex_data=explode("-----",str_replace("'","",$$txtRfColorAllData));
			$new_rf_color_all_data="";
			foreach($ex_data as $color_data)
			{
				$ex_size_data=explode("_",$color_data);
				$contrast=$ex_size_data[3];
				if(str_replace("'","",$contrast)!="")
				{
					if (!in_array(str_replace("'","",$contrast),$new_array_color))
					{
						$fab_color_id = return_id( str_replace("'","",$contrast), $color_arr, "lib_color", "id,color_name","140");
						$new_array_color[$fab_color_id]=str_replace("'","",$contrast);
					}
					else $fab_color_id =  array_search(str_replace("'","",$contrast), $new_array_color);
				}
				else $fab_color_id=0;
				
				if($new_rf_color_all_data=="")
				{
					$new_rf_color_all_data.=$color_data."_".$fab_color_id;
				}
				else
				{
					$new_rf_color_all_data.="-----".$color_data."_".$fab_color_id;
				}
			}

			if ($i!=1) $data_array .=",";
			$data_array .="(".$id_dtls.",".$update_id.",".$$cboRfSampleName.",".$$cboRfGarmentItem.",".$$txtProcessLoss.",".$$txtGrayFabric.",".$$fabricDelvDate.",".$$cboRfFabricSource.",'".$fab_remarks."',".$$txtRfReqQty.",".$$cboRfBodyPart.",".$$cboRfFabricNature.",".$$txtRfFabricDescription.",".$$txtRfGsm.",".$$txtRfDia.",'".$new_rf_color_all_data."',".$$cboRfColorType.",".$$cboRfWidthDia.",".$$cboRfUom.",".$$txtRfReqDzn.",".$$txtRfReqQty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0,1, ".$$libyarncountdeterminationid.",".$$collercuffdata.",".$$txtRfyarndtls.")";


			$data_array_col.='';

			$add_comm="";
			$ex_data=explode("-----",str_replace("'","",$new_rf_color_all_data));
			foreach($ex_data as $color_data)
			{
				$ex_size_data=explode("_",$color_data);
				$colorName=$ex_size_data[1];
				$colorId=$ex_size_data[2];
				$contrast=$ex_size_data[3];
				$qnty=$ex_size_data[4];
				$txtProcessLoss=$ex_size_data[5];
				$txtGrayFabric=$ex_size_data[6];
				$fab_color_id=$ex_size_data[7];


				 if($data_array_col!="") $data_array_col.=",";
				$data_array_col.="(".$idColorTbl.",".$update_id.",".$id_dtls.",".$colorId.",'".$contrast."','".$fab_color_id."','".$qnty."','".$txtProcessLoss."','".$txtGrayFabric."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				$idColorTbl = $idColorTbl + 1;

				if($qnty>0)
				{

					$rId_rf_status=execute_query( "update sample_development_dtls set fabric_status=1,fab_status_id=".$id_dtls." where sample_mst_id=$update_id and sample_name=".$$cboRfSampleName." and gmts_item_id=".$$cboRfGarmentItem." and sample_color=".$colorId."",0);
				}
			}

			$coller_cuff_arr=explode(",",str_replace("'","",$$collercuffdata));
			foreach($coller_cuff_arr as $coller_cuff)
			{
				$coller_cuff_data=explode("_",$coller_cuff);
				$colorId=$coller_cuff_data[0];
				$sizeid=$coller_cuff_data[1];
				$itemsize=$coller_cuff_data[2];
				$qnty=$coller_cuff_data[3];
				if($colorId!=0 && $sizeid!=0)
				{					
					if($data_array_coller!="") $data_array_coller.=",";
					$data_array_coller.="(".$idCollerCuff.",".$update_id.",".$id_dtls.",".$colorId.",".$sizeid.",'".$itemsize."','".$qnty."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
					$idCollerCuff = $idCollerCuff + 1;
				}
			}

			$id_dtls=$id_dtls+1;
		}
		$yarn_deter_ids=rtrim($yarn_deter_id,',');//id_dtls
		$select_deter=sql_select("SELECT a.id,b.id as dtls_id, b.copmposition_id,b.percent,b.count_id,b.type_id from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and a.id in($yarn_deter_ids)");
		$determin_arr="";
		$determin_arr_bind=array();
		foreach($select_deter as $row)
		{
			//$determin_arr.=$row[csf('id')].'**'.$row[csf('percent')].'**'.$row[csf('copmposition_id')].'**'.$row[csf('count_id')].'**'.$row[csf('type_id')].'##';
			$determin_arr_bind[$row[csf('id')]].=$row[csf('id')].'**'.$row[csf('percent')].'**'.$row[csf('copmposition_id')].'**'.$row[csf('count_id')].'**'.$row[csf('type_id')]."#*#";
		}
		// $determin_id_arr=explode(",", $yarn_deter_ids);
		// foreach ($determin_id_arr as $key => $det_id) {
		// 	$determin_arr.=$determin_arr_bind[$det_id].'##';
			
		// }

		$yar_details_data_arr=rtrim($yar_details_data_arr,'***');
		//$determin_id_arr=explode(",", $yarn_deter_ids);
		$determin_id_arr=explode("***", $yar_details_data_arr);
		foreach ($determin_id_arr as $key => $det_id_data) {
			$ex_det=explode("##",$det_id_data);
			$det_id=$ex_det[0];
			//$determin_arr.=$determin_arr_bind[$det_id].'**'.$ex_det[1].'##';
			$deter_data_explode=explode("#*#", rtrim($determin_arr_bind[$det_id],"#*#"));
			foreach ($deter_data_explode as $key => $value) {
				$determin_arr.=$value.'**'.$ex_det[1].'##';
			}
			
		}

		$determin_data=rtrim($determin_arr,'##');
		$yarn_id_dtls=return_next_id( "id", "sample_development_yarn_dtls", 1 ) ;
		$m=0;$yarn_data_array_dtls="";
		
			$ex_data=explode("##",$determin_data);
			
			foreach($ex_data as $deter_data)
			{
				if ($m!=0) $yarn_data_array_dtls .=",";
				$ex_dtl_data=explode("**",$deter_data);
				$deter_mst_id=$ex_dtl_data[0];
				$percent=$ex_dtl_data[1];
				$copmposition_id=$ex_dtl_data[2];
				$count_id=$ex_dtl_data[3];
				$type_id=$ex_dtl_data[4];
				$req_fab_dtls_id=$ex_dtl_data[5];
				
				$fab_nature=$fab_nature_arr[$deter_mst_id];
				$fab_greyQty=$fab_greyQty_arr[$req_fab_dtls_id];
				$fab_gsm=$fab_gsm_arr[$req_fab_dtls_id];
				
				if(str_replace("'",'',$fab_nature)==2)
				{
					$yanr_cons=(str_replace("'",'',$fab_greyQty)*$percent)/100;
				}
				if(str_replace("'",'',$fab_nature)==3)
				{
					$yanr_cons=(str_replace("'",'',$fab_gsm)*$percent)/100;
				}
				$yanr_cons=$yanr_cons;//$count_determina_arr[$deter_mst_id];
				
				$yarn_data_array_dtls .="(".$yarn_id_dtls.",".$req_fab_dtls_id.",".$update_id.",".$deter_mst_id.",".$count_id.",'".$copmposition_id."','".$percent."','".$type_id."','".$percent."','".$yanr_cons."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$yarn_id_dtls=$yarn_id_dtls+1;
				$m++;
			} //foreach end
			

		$yarn_field_array="id,req_fab_dtls_id, mst_id,determin_id,count_id, copm_one_id, percent_one, type_id, cons_ratio, cons_qnty,inserted_by, insert_date";
		$rID_1=sql_insert("sample_development_fabric_acc",$field_array,$data_array,1);
		//echo "10**insert into sample_development_rf_color (".$field_array_col.") Values ".$data_array_col;die;
		$rIDs=sql_insert("sample_development_rf_color",$field_array_col,$data_array_col,1);
		if($data_array_coller !="")
		{
			$rIDColler=sql_insert("sample_requisition_coller_cuff",$field_array_coller,$data_array_coller,1);
		}			
		
		if($yarn_data_array_dtls!="")
		 {
			//echo "10**insert into wo_non_ord_samp_yarn_dtls (".$yarn_field_array.") Values ".$yarn_data_array_dtls;die;
			$rID_2=sql_insert("sample_development_yarn_dtls",$yarn_field_array,$yarn_data_array_dtls,0);

		 }
		 //echo "10**".$rID_1.'--'.$rIDs.'--'.$rID_2; die;
		if($db_type==0)
		{
			if($rID_1 && $rIDs && $rID_2){
				mysql_query("COMMIT");
				echo "0**".str_replace("'",'',$update_id)."**2";
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$rID_1 ."**".$rIDs ."**". $rID_2;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID_1 && $rIDs && $rID_2)
			{
				oci_commit($con);
				echo "0**".str_replace("'",'',$update_id)."**2";

			}
			else
			{
				oci_rollback($con);
				echo "10**".$rID_1 ."**".$rIDs ."**". $rID_2;
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)  // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$booking_idss= return_field_value("id","wo_non_ord_samp_booking_mst","booking_no='".str_replace("'",'',$txt_booking_no)."' and status_active=1");		
		$is_approved = return_field_value("is_approved","wo_non_ord_samp_booking_mst","id=$booking_idss and status_active=1 and is_approved in(1,3)");

        if($is_approved==1 || $is_approved==3)
        {
            echo "14**Approved. Update or Delete not allowed.";         
            disconnect($con);
            die;
        }

		$prev_ids="SELECT id ,is_updated from sample_development_fabric_acc where status_active=1 and is_deleted=0 and sample_mst_id=$update_id and form_type=1";
		$prev_ids_array=array();
		foreach(sql_select($prev_ids) as $key_id=>$key_val)
		{
			$prev_ids_array[$key_val[csf("id")]]=$key_val[csf("id")];
			if($key_val[csf("is_updated")]==1)
			{
			$is_updated_found=$key_val[csf("is_updated")];
			}
		}

		$id_dtls=return_next_id( "id", "sample_development_fabric_acc", 1);
		//echo "10**".$id_dtls; die;
		$idCollerCuff=return_next_id( "id","sample_requisition_coller_cuff", 1 ) ;

		$field_array_up="sample_name*gmts_item_id*process_loss_percent*grey_fab_qnty*delivery_date*fabric_source*remarks_ra*fin_fab_qnty*body_part_id*fabric_nature_id*fabric_description*gsm*dia*color_data*color_type_id*width_dia_id*uom_id*required_dzn*required_qty*updated_by*update_date*determination_id*collar_cuff_breakdown*yarn_dtls";

		$field_array= "id,sample_mst_id,sample_name,gmts_item_id,process_loss_percent,grey_fab_qnty,delivery_date,fabric_source,remarks_ra,fin_fab_qnty,body_part_id,fabric_nature_id,fabric_description,gsm,dia,color_data,color_type_id,width_dia_id,uom_id,required_dzn,required_qty,inserted_by,insert_date,status_active,is_deleted,form_type,determination_id,collar_cuff_breakdown,yarn_dtls";
		$idColorTbl=return_next_id( "id","sample_development_rf_color", 1 ) ;
		$field_array_col="id, mst_id, dtls_id,color_id,contrast,fabric_color,qnty,process_loss_percent,grey_fab_qnty,inserted_by, insert_date, status_active, is_deleted";
		$field_array_coller="id, mst_id, dtls_id, sample_color, size_id, item_size, qnty_pcs, inserted_by, insert_date, status_active, is_deleted";

		$add_comma=0; $data_array=""; //echo "10**";
		$yarn_deter_id="";
		$yar_details_data_arr="";
		$count_determina_arr=array();
		for ($i=1;$i<=$total_row;$i++)
		{
			$cboRfSampleName="cboRfSampleName_".$i;
			$cboRfGarmentItem="cboRfGarmentItem_".$i;
			$cboRfBodyPart="cboRfBodyPart_".$i;
			$cboRfFabricNature="cboRfFabricNature_".$i;
			$txtRfFabricDescription="txtRfFabricDescription_".$i;
			$txtRfGsm="txtRfGsm_".$i;
			$txtRfDia="txtRfDia_".$i;
			$txtRfyarndtls="txtRfyarndtls_".$i;
			$txtRfColor="txtRfColor_".$i;
			$cboRfColorType="cboRfColorType_".$i;
			$cboRfWidthDia="cboRfWidthDia_".$i;
			$cboRfUom="cboRfUom_".$i;
			$txtRfReqDzn="txtRfReqDzn_".$i;
			$txtRfReqQty="txtRfReqQty_".$i;
			$updateidRequiredDtlf="updateidRequiredDtl_".$i;
			$txtRfColorAllData="txtRfColorAllData_".$i;
			$libyarncountdeterminationid="libyarncountdeterminationid_".$i;
			$txtProcessLoss="txtProcessLoss_".$i;
			$txtGrayFabric="txtGrayFabric_".$i;
			$fabricDelvDate="fabricDelvDate_".$i;
			$cboRfFabricSource="cboRfFabricSource_".$i;
			$txtRfRemarks="txtRfRemarks_".$i;
			$collercuffdata="hiddencollarCuffdata_".$i;
			$yarn_deter_id.=str_replace("'","",$$libyarncountdeterminationid).',';

			 
			$Remarks=str_replace("'","",$$txtRfRemarks);
			$str_rep=array("+", "&", "*", "(", ")", "=","'","\r", "\n",'"','#');
			$fab_remarks=str_replace($str_rep,' ',$Remarks);

			unset($prev_ids_array[str_replace("'",'',$$updateidRequiredDtlf)]);
			$count_determina_arr[str_replace("'",'',$$libyarncountdeterminationid)]++;

			if (str_replace("'",'',$$updateidRequiredDtlf)!="")
			{
				$ex_data="";
				$ex_data=explode("-----",str_replace("'","",$$txtRfColorAllData));
				$new_rf_color_all_data="";
				foreach($ex_data as $color_data)
				{
					$ex_size_data=explode("_",$color_data);
					$contrast=$ex_size_data[3];
					if(str_replace("'","",$contrast)!="")
					{
						if (!in_array(str_replace("'","",$contrast),$new_array_color))
						{
							$fab_color_id = return_id( str_replace("'","",$contrast), $color_arr, "lib_color", "id,color_name","140");
							$new_array_color[$fab_color_id]=str_replace("'","",$contrast);

						}
						else $fab_color_id =  array_search(str_replace("'","",$contrast), $new_array_color);
					}
					else $fab_color_id=0;
					
					if($new_rf_color_all_data=="")
					{
						$new_rf_color_all_data.=$color_data."_".$fab_color_id;
					}
					else
					{
						$new_rf_color_all_data.="-----".$color_data."_".$fab_color_id;
					}
				}


				$id_arr[]=str_replace("'",'',$$updateidRequiredDtlf);

				$data_array_up[str_replace("'",'',$$updateidRequiredDtlf)] =explode("*",("".$$cboRfSampleName."*".$$cboRfGarmentItem."*".$$txtProcessLoss."*
				".$$txtGrayFabric."*".$$fabricDelvDate."*".$$cboRfFabricSource."*'".$fab_remarks."'*".$$txtRfReqQty."*".$$cboRfBodyPart."*".$$cboRfFabricNature."*".$$txtRfFabricDescription."*".$$txtRfGsm."*".$$txtRfDia."*'".$new_rf_color_all_data."'*".$$cboRfColorType."*".$$cboRfWidthDia."*".$$cboRfUom."*".$$txtRfReqDzn."*".$$txtRfReqQty."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'*".$$libyarncountdeterminationid."*".$$collercuffdata."*".$$txtRfyarndtls.""));
				$yar_details_data_arr.=str_replace("'","",$$libyarncountdeterminationid)."##".str_replace("'",'',$$updateidRequiredDtlf)."***";

				$ex_data="";
				$ex_data=explode("-----",str_replace("'","",$new_rf_color_all_data));
				$cc=0;
				foreach($ex_data as $color_data)
				{
					$ex_size_data=explode("_",$color_data);
					$colorName=$ex_size_data[1];
					$colorId=$ex_size_data[2];
					if($colorId=="") $colorId=0;else $colorId=$colorId;
					$contrast=$ex_size_data[3];
					$qnty=0;
					$qnty=$ex_size_data[4];
					$txtProcessLoss=$ex_size_data[5];
					$txtGrayFabric=$ex_size_data[6];
					$fab_col_id=$ex_size_data[7] ;

					$updateidRequiredDtlfID=str_replace("'",'',$$updateidRequiredDtlf);

					if($cc!=0) { $data_array_col .=",";}

					$data_array_col.="(".$idColorTbl.",".$update_id.",".$updateidRequiredDtlfID.",".$colorId.",'".$contrast."','".$fab_col_id."','".$qnty."','".$txtProcessLoss."','".$txtGrayFabric."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
					$idColorTbl=$idColorTbl+1;
					$cc++;
					$rId_rf_status=execute_query( "update sample_development_dtls set fabric_status=0 where sample_mst_id=$update_id and fab_status_id=".$updateidRequiredDtlfID."  and sample_name=".$$cboRfSampleName." and gmts_item_id=".$$cboRfGarmentItem." and sample_color=".$colorId."",0);

					if($qnty>0)
					{
						$rId_rf_status_ac=execute_query( "update sample_development_dtls set fabric_status=1,fab_status_id=".$updateidRequiredDtlfID." where sample_mst_id=$update_id and sample_name=".$$cboRfSampleName." and gmts_item_id=".$$cboRfGarmentItem." and sample_color=".$colorId."",0);
					}
					if($is_updated_found==1)
					{
							$update_sample_dtls=execute_query("UPDATE sample_development_dtls set is_updated=0 where sample_mst_id=$update_id and sample_name=".$$cboRfSampleName." and gmts_item_id=".$$cboRfGarmentItem." and sample_color=".$colorId."",1);
							if($update_sample_dtls) $flag=1; else $flag=0;
							$update_all_dtls=execute_query("UPDATE sample_development_fabric_acc set is_updated=0 where sample_mst_id=$update_id and form_type=1 ",1);
							if($update_all_dtls) $flag=1; else $flag=0;
					}


				}
				
				$coller_cuff_arr=explode(",",str_replace("'","",$$collercuffdata));
				foreach($coller_cuff_arr as $coller_cuff)
				{
					$coller_id_dtls=str_replace("'",'',$$updateidRequiredDtlf);
					$coller_cuff_data=explode("_",$coller_cuff);
					$colorId=$coller_cuff_data[0];
					$sizeid=$coller_cuff_data[1];
					$itemsize=$coller_cuff_data[2];
					$qnty=$coller_cuff_data[3];	
					if($colorId!=0 && $sizeid!=0)
					{				
						if($data_array_coller!="") $data_array_coller.=",";
						$data_array_coller.="(".$idCollerCuff.",".$update_id.",".$coller_id_dtls.",".$colorId.",".$sizeid.",'".$itemsize."','".$qnty."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
						$idCollerCuff = $idCollerCuff + 1;
					}
				}					
			}
			else
			{
				$ex_data="";
				$ex_data=explode("-----",str_replace("'","",$$txtRfColorAllData));
				$new_rf_color_all_data="";
				foreach($ex_data as $color_data)
				{
					$ex_size_data=explode("_",$color_data);
					$contrast=$ex_size_data[3];
					if(str_replace("'","",$contrast)!="")
					{
						if (!in_array(str_replace("'","",$contrast),$new_array_color))
						{
							$fab_color_id = return_id( str_replace("'","",$contrast), $color_arr, "lib_color", "id,color_name","140");
							$new_array_color[$fab_color_id]=str_replace("'","",$contrast);

						}
						else $fab_color_id =  array_search(str_replace("'","",$contrast), $new_array_color);
					}
					else $fab_color_id=0;
					
					if($new_rf_color_all_data=="")
					{
						$new_rf_color_all_data.=$color_data."_".$fab_color_id;
					}
					else
					{
						$new_rf_color_all_data.="-----".$color_data."_".$fab_color_id;
					}
				}

				if ($add_comma!=0) $data_array .=",";
				$data_array .="(".$id_dtls.",".$update_id.",".$$cboRfSampleName.",".$$cboRfGarmentItem.",".$$txtProcessLoss.",".$$txtGrayFabric.",".$$fabricDelvDate.",".$$cboRfFabricSource.",'".$fab_remarks."',".$$txtRfReqQty.",".$$cboRfBodyPart.",".$$cboRfFabricNature.",".$$txtRfFabricDescription.",".$$txtRfGsm.",".$$txtRfDia.",'".$new_rf_color_all_data."',".$$cboRfColorType.",".$$cboRfWidthDia.",".$$cboRfUom.",".$$txtRfReqDzn.",".$$txtRfReqQty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0,1,".$$libyarncountdeterminationid.",".$$collercuffdata.",".$$txtRfyarndtls.")";
				$yar_details_data_arr.=str_replace("'","",$$libyarncountdeterminationid)."##".$id_dtls."***";

				$ex_datas=explode("-----",str_replace("'","",$new_rf_color_all_data));
				$data_array_cols.='';
				foreach($ex_datas as $color_datas)
				{
					$ex_size_data=explode("_",$color_datas);
					$colorName=$ex_size_data[1];
					$colorId=$ex_size_data[2];
					if($colorId=="") $colorId=0;else $colorId=$colorId;
					$contrast=$ex_size_data[3];
					$qnty=$ex_size_data[4];
					$txtProcessLoss=$ex_size_data[5];
					$txtGrayFabric=$ex_size_data[6];
					$fab_color_id=$ex_size_data[7];

					if($data_array_cols)   $data_array_cols.=",";
					$data_array_cols.="(".$idColorTbl.",".$update_id.",".$id_dtls.",".$colorId.",'".$contrast."','".$fab_color_id."','".$qnty."','".$txtProcessLoss."','".$txtGrayFabric."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
					$idColorTbl=$idColorTbl+1;

					if($qnty>0)
					{
						$rId_rf_status=execute_query( "update sample_development_dtls set fabric_status=1, fab_status_id=$id_dtls where sample_mst_id=$update_id and sample_name=".$$cboRfSampleName."  and gmts_item_id=".$$cboRfGarmentItem." and sample_color=".$colorId."",0);
					}
				}
				$coller_cuff_arr=explode(",",str_replace("'","",$$collercuffdata));
				foreach($coller_cuff_arr as $coller_cuff)
				{
					$coller_cuff_data=explode("_",$coller_cuff);
					$colorId=$coller_cuff_data[0];
					$sizeid=$coller_cuff_data[1];
					$itemsize=$coller_cuff_data[2];
					$qnty=$coller_cuff_data[3];
					if($colorId!=0 && $sizeid!=0)
					{
						if($data_array_coller!="") $data_array_coller.=",";
						$data_array_coller.="(".$idCollerCuff.",".$update_id.",".$id_dtls.",".$colorId.",".$sizeid.",'".$itemsize."','".$qnty."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
						$idCollerCuff = $idCollerCuff + 1;
					}					
				}
				$id_dtls=$id_dtls+1;
				$add_comma++;
			}
		}
			$yarn_deter_ids=rtrim($yarn_deter_id,',');
			$select_deter=sql_select("SELECT a.id,b.id as dtls_id, b.copmposition_id,b.percent,b.count_id,b.type_id from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and a.id in($yarn_deter_ids)");
			

			$yarn_prev_sql=sql_select("SELECT id,req_fab_dtls_id, mst_id,determin_id, count_id, copm_one_id, percent_one, type_id, cons_ratio, cons_qnty from sample_development_yarn_dtls where mst_id in($update_id) and status_active=1 and is_deleted=0");
			$yarn_prev_data=array();
			$determin_arr_bind=array();
			$determin_id_arr_prev=array();
			
			foreach ($yarn_prev_sql as $row) 
			{
				$yarn_prev_data[$row[csf('req_fab_dtls_id')]].=$row[csf('determin_id')].'**'.$row[csf('percent_one')].'**'.$row[csf('copm_one_id')].'**'.$row[csf('count_id')].'**'.$row[csf('type_id')]."#*#";
				array_push($determin_id_arr_prev, $row[csf('determin_id')]);
			}
			foreach($select_deter as $row)
			{
				//$determin_arr.=$row[csf('id')].'**'.$row[csf('percent')].'**'.$row[csf('copmposition_id')].'**'.$row[csf('count_id')].'**'.$row[csf('type_id')].'##';
				$determin_arr_bind[$row[csf('id')]].=$row[csf('id')].'**'.$row[csf('percent')].'**'.$row[csf('copmposition_id')].'**'.$row[csf('count_id')].'**'.$row[csf('type_id')]."#*#";

			}

			$determin_arr='';

			$yar_details_data_arr=rtrim($yar_details_data_arr,"***");
			//$determin_id_arr=explode(",", $yarn_deter_ids);

			$determin_id_arr=explode("***", $yar_details_data_arr);
			foreach ($determin_id_arr as $key => $det_id_data) {
				$ex_det=explode("##",$det_id_data);
				$det_id=$ex_det[0];
				// if(!empty($yarn_prev_data[$ex_det[1]]) && in_array($det_id, $determin_id_arr_prev))
				// {
				// 	$determin_arr.=$yarn_prev_data[$ex_det[1]].'**'.$ex_det[1].'##';
				// }
				// else
				// {
				// 	$determin_arr.=$determin_arr_bind[$det_id].'**'.$ex_det[1].'##';
				// }
				if(!empty($yarn_prev_data[$ex_det[1]]) && in_array($det_id, $determin_id_arr_prev))
				{
					$deter_data_explode=explode("#*#", rtrim($yarn_prev_data[$ex_det[1]],"#*#"));
					$wh=1;
					//$determin_arr.=$yarn_prev_data[$ex_det[1]].'**'.$ex_det[1].'##';
				}
				else
				{
					$deter_data_explode=explode("#*#", rtrim($determin_arr_bind[$det_id],"#*#"));
					$wh=2;
					//$determin_arr.=$determin_arr_bind[$det_id].'**'.$ex_det[1].'##';
				}
				foreach ($deter_data_explode as $key => $value) 
				{
					$determin_arr.=$value.'**'.$ex_det[1].'##';
				}
			}
		
			$yarn_id_dtls=return_next_id( "id", "sample_development_yarn_dtls", 1 ) ;
			$m=0;$yarn_data_array_dtls="";
			
			for ($i=1; $i<=$total_row; $i++) //Yarn Start here
			{
				$cboRfFabricSource="cboRfFabricSource_".$i;
				$txtRfGsm="txtRfGsm_".$i;
				$txtRfReqDzn="txtRfReqDzn_".$i;
				$txtRfReqQty="txtRfReqQty_".$i;
				$required_fab_id="updateidRequiredDtl_".$i;
				$txtGrayFabric="txtGrayFabric_".$i;
				$libyarncountdeterminationid="libyarncountdeterminationid_".$i;
				$cboRfFabricNature="cboRfFabricNature_".$i;
				//if ($i!=1) $libyarncountdeterminationid .=",";
				$libDeterId=str_replace("'",'',$$libyarncountdeterminationid);
				
				$fab_nature_arr[$libDeterId]=str_replace("'",'',$$cboRfFabricNature);
				$fab_greyQty_arr[str_replace("'",'',$$required_fab_id)]+=str_replace("'",'',$$txtGrayFabric);
				$fab_gsm_arr[str_replace("'",'',$$required_fab_id)]=str_replace("'",'',$$txtRfGsm);
				//$fab_nature_arr[$libDeterId]=str_replace("'",'',$$txtGrayFabric);
			}//For End
			$determin_datas=rtrim($determin_arr,'##');
			$ex_data=explode("##",$determin_datas);
			//echo "10**";
			foreach($ex_data as $deter_data)
			{
				if ($m!=0) $yarn_data_array_dtls .=",";
				$ex_dtl_data=explode("**",$deter_data);
				$deter_mst_id=$ex_dtl_data[0];
				$percent=$ex_dtl_data[1];
				$copmposition_id=$ex_dtl_data[2];
				$count_id=$ex_dtl_data[3];
				$type_id=$ex_dtl_data[4];
				$req_fab_dtls_id=$ex_dtl_data[5];
				$fab_nature=$fab_nature_arr[$deter_mst_id];
				$fab_greyQty=$fab_greyQty_arr[$req_fab_dtls_id];
				$fab_gsm=$fab_gsm_arr[$req_fab_dtls_id];
				
				if(str_replace("'",'',$fab_nature)==2)
				{
					$yanr_cons=(str_replace("'",'',$fab_greyQty)*$percent)/100;
					//echo $yanr_cons.'=='.str_replace("'",'',$fab_greyQty).'=='.$percent.'<br>';
				}
				else if(str_replace("'",'',$fab_nature)==3)
				{
					$yanr_cons=(str_replace("'",'',$fab_gsm)*$percent)/100;
				}
				$yanr_cons=$yanr_cons;//$count_determina_arr[$deter_mst_id];
				$booking_no=str_replace("'",'',$txt_booking_no);
				//echo $yanr_cons.'<br>';
				if($booking_no!="")
				{
					$yarn_data_array_dtls .="(".$yarn_id_dtls.",".$req_fab_dtls_id.",".$update_id.",".$deter_mst_id.",".$count_id.",'".$copmposition_id."','".$percent."','".$type_id."','".$percent."','".$yanr_cons."','".$txt_booking_no."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				}
				else{
					$yarn_data_array_dtls .="(".$yarn_id_dtls.",".$req_fab_dtls_id.",".$update_id.",".$deter_mst_id.",".$count_id.",'".$copmposition_id."','".$percent."','".$type_id."','".$percent."','".$yanr_cons."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				}
				$yarn_id_dtls=$yarn_id_dtls+1;
				$m++;
				//}
			} //foreach end

			//die;
		$flag=1;
		if(count($data_array_up))
		{
			$rID_size_dlt=execute_query( "delete from sample_development_rf_color where mst_id=$update_id",0);
			$rIDs=sql_insert("sample_development_rf_color",$field_array_col,$data_array_col,1);
			$rID1=execute_query(bulk_update_sql_statement("sample_development_fabric_acc", "id",$field_array_up,$data_array_up,$id_arr ));
			if($rID1)
			{
				$del_ids=implode(",",$prev_ids_array );
				if($del_ids)
				{
					execute_query( "delete from sample_development_fabric_acc where id  in($del_ids)",0);
					execute_query( "update sample_development_dtls set fabric_status=0,fab_status_id=0 where fab_status_id  in($del_ids)",0);
				}
			}
			if($rIDs && $rID1) $flag=1; else $flag=0;
		}
		if($booking_no!="")
		{
			$yarn_field_array="id,req_fab_dtls_id, mst_id,determin_id, count_id, copm_one_id, percent_one, type_id, cons_ratio, cons_qnty,booking_no, inserted_by, insert_date";
		}
		else
		{
			$yarn_field_array="id,req_fab_dtls_id, mst_id,determin_id, count_id, copm_one_id, percent_one, type_id, cons_ratio, cons_qnty, inserted_by, insert_date";
		}
		if($flag==1)
		{
			$yarn_delete=execute_query( "delete from sample_development_yarn_dtls where mst_id  in($update_id)",0);
			if($yarn_delete) $flag=1; else $flag=0;
		}

		if($yarn_data_array_dtls!="")
		 {
			//print_r($ex_data);
			//echo "10**insert into sample_development_yarn_dtls (".$yarn_field_array.") Values ".$yarn_data_array_dtls;die;
			if($flag==1)
			{
				$rID2=sql_insert("sample_development_yarn_dtls",$yarn_field_array,$yarn_data_array_dtls,0);
			}
			if($rID2) $flag=1; else $flag=0;

		 }

		//echo "10**".$rIDs.'='.$rID1.'='.$rID_size_dlt.'='.$rID2.'='.$yarn_delete.'='.$flag;die;
		if($data_array!="")
		{
			//echo "10**insert into sample_development_fabric_acc (".$field_array.") values ".$data_array;
			$rID=sql_insert("sample_development_fabric_acc",$field_array,$data_array,0);
			$rIDs=sql_insert("sample_development_rf_color",$field_array_col,$data_array_cols,1);
			if($rID && $rIDs) $flag=1; else $flag=0;
		}
		if($txtDeltedIdRf!="" || $txtDeltedIdRf!=0)
		{
			$fields="is_deleted";
			$fields_sd="fabric_status";
			$delrfDtls=sql_multirow_update("sample_development_dtls",$fields_sd,"0","fab_status_id",$txtDeltedIdRf,0);
			$del=sql_multirow_update("sample_development_fabric_acc",$fields,"1","id",$txtDeltedIdRf,0);
			if($delrfDtls && $del) $flag=1; else $flag=0;
		 }
		 //echo "10**".$delrfDtls.'--'.$del; die;
		if($data_array_coller !="")
		{
			if(count($id_arr)>0)
			{
				$updateids=implode(",", $id_arr);
				$coller_cuff_status=execute_query( "delete sample_requisition_coller_cuff where dtls_id in ($updateids) ",0);
			}
			//echo "10**INSERT INTO sample_requisition_coller_cuff ($field_array_coller) values $data_array_coller"; die;
			$rIDColler=sql_insert("sample_requisition_coller_cuff",$field_array_coller,$data_array_coller,1);
			if($rIDColler) $flag=1; else $flag=0;
		}

		if($db_type==0)
		{
			if($flag==1){
				mysql_query("COMMIT");
				echo "1**".str_replace("'",'',$update_id)."**2";
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "1**".str_replace("'",'',$update_id)."**2";

			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)  // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$booking_idss= return_field_value("id","wo_non_ord_samp_booking_mst","booking_no='".str_replace("'",'',$txt_booking_no)."' and status_active=1");
		$is_approved = return_field_value("is_approved","wo_non_ord_samp_booking_mst","id=$booking_idss and status_active=1 and is_approved in(1,3)");
        if($is_approved==1 || $is_approved==3)
        {
            echo "14**Approved. Update or Delete not allowed.";
            disconnect($con);
            die;
        }

		$non_ord_booking=return_field_value("id","wo_non_ord_samp_booking_dtls","style_id=$update_id and entry_form_id=140 and status_active=1 and is_deleted=0");
		$ord_booking=return_field_value("id","wo_booking_dtls","style_id=$update_id and entry_form_id=139 and status_active=1 and is_deleted=0");
		$is_approved=return_field_value("is_approved","sample_development_mst","id=$update_id and entry_form_id=203 and status_active=1 and is_deleted=0");
		if($is_approved==1)
		{
			echo "323**";
			disconnect($con);die;
		}
		if($non_ord_booking*1 >0 || $ord_booking*1 >0)
		{
			echo "321**";
			disconnect($con);die;
		}

		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";

		$rID=sql_delete("sample_development_fabric_acc",$field_array,$data_array,"sample_mst_id*form_type","".$update_id."*1",0);
		$rID1=sql_delete("sample_development_rf_color",$field_array,$data_array,"mst_id","".$update_id."",0);
		if($rID)
		{
			$update_dtls=execute_query("UPDATE sample_development_dtls set fabric_status=0 where sample_mst_id=$update_id",1);
		}
		if($db_type==0)
		{
			if($rID  && $update_dtls )
			{
				mysql_query("COMMIT");
				echo "2**".str_replace("'","",$update_id);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$update_id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID  && $update_dtls )
			{
				oci_commit($con);
				echo "2**".str_replace("'","",$update_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$update_id);
			}
		}
		disconnect($con);
	}
}

if ($action=="save_update_delete_required_accessories")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if ($operation==0)  // Insert Here
	{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}

			$id_dtls=return_next_id( "id", "sample_development_fabric_acc", 1 ) ;
			$field_array= "id,sample_mst_id,sample_name_ra,gmts_item_id_ra,supplier_id,delivery_date,fabric_source,trims_group_ra,description_ra,brand_ref_ra,uom_id_ra,req_dzn_ra,req_qty_ra,remarks_ra,inserted_by,insert_date,status_active,is_deleted,form_type";
			for ($i=1;$i<=$total_row;$i++)
		    {
				$cboRaSampleName="cboRaSampleName_".$i;
				$cboRaGarmentItem="cboRaGarmentItem_".$i;
				$cboRaTrimsGroup="cboRaTrimsGroup_".$i;
				$txtRaDescription="txtRaDescription_".$i;
				$txtRaBrandSupp="txtRaBrandSupp_".$i;
				$cboRaUom="cboRaUom_".$i;
				$txtRaReqDzn="txtRaReqDzn_".$i;
				$txtRaReqQty="txtRaReqQty_".$i;
				$txtRaRemarks="txtRaRemarks_".$i;
				$cboRaSupplierName="cboRaSupplierName_".$i;
				$accDate="accDate_".$i;
				$cboRaFabricSource="cboRaFabricSource_".$i;

				$Remarks=str_replace("'","",$$txtRaRemarks);
				$str_rep=array("+", "&", "*", "(", ")", "=","'","\r", "\n",'"','#');
				$acc_remarks=str_replace($str_rep,' ',$Remarks);




				if ($i!=1) $data_array .=",";
				$data_array .="(".$id_dtls.",".$update_id.",".$$cboRaSampleName.",".$$cboRaGarmentItem.",".$$cboRaSupplierName.",".$$accDate.",".$$cboRaFabricSource.",".$$cboRaTrimsGroup.",".$$txtRaDescription.",".$$txtRaBrandSupp.",".$$cboRaUom.",".$$txtRaReqDzn.",".$$txtRaReqQty.",'".$acc_remarks."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0,2)";
				$rId_acc_status=execute_query( "update sample_development_dtls set acc_status=1,acc_status_id=".$id_dtls." where sample_mst_id=$update_id and sample_name=".$$cboRaSampleName."",0);
				$id_dtls=$id_dtls+1;

		    }
 			//echo "5**"."INSERT INTO sample_development_dtls(".$field_array."VALUES ".$data_array; die;
			$rID_1=sql_insert("sample_development_fabric_acc",$field_array,$data_array,1);

			if($db_type==0)
			{
				if($rID_1){
					mysql_query("COMMIT");
					echo "0**".str_replace("'",'',$update_id)."**3";
				}
				else{
					mysql_query("ROLLBACK");
					echo "10**";
				}
			}
			else if($db_type==2 || $db_type==1 )
			{
				if($rID_1)
				{
					oci_commit($con);
					echo "0**".str_replace("'",'',$update_id)."**3";

				}
			else{
					oci_rollback($con);
					echo "10**";
				}
			}
			disconnect($con);
			die;
	}
	else if ($operation==1)  // Update Here
	{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}

			$id_dtls=return_next_id( "id", "sample_development_fabric_acc", 1);
			$field_array_up="sample_name_ra*gmts_item_id_ra*supplier_id*delivery_date*fabric_source*trims_group_ra*description_ra*brand_ref_ra*uom_id_ra*req_dzn_ra*req_qty_ra*remarks_ra*updated_by*update_date";
			$field_array= "id,sample_mst_id,sample_name_ra,gmts_item_id_ra,supplier_id,delivery_date,fabric_source,trims_group_ra,description_ra,brand_ref_ra,uom_id_ra,req_dzn_ra,req_qty_ra,remarks_ra,inserted_by,insert_date,status_active,is_deleted,form_type";
			$add_comma=0; $data_array=""; //echo "10**";
			for ($i=1;$i<=$total_row;$i++)
		    {
				$cboRaSampleName="cboRaSampleName_".$i;
				$cboRaGarmentItem="cboRaGarmentItem_".$i;
				$cboRaTrimsGroup="cboRaTrimsGroup_".$i;
				$txtRaDescription="txtRaDescription_".$i;
				$txtRaBrandSupp="txtRaBrandSupp_".$i;
				$cboRaUom="cboRaUom_".$i;
				$txtRaReqDzn="txtRaReqDzn_".$i;
				$txtRaReqQty="txtRaReqQty_".$i;
				$txtRaRemarks="txtRaRemarks_".$i;
				$updateIdAccDtls="updateidAccessoriesDtl_".$i;
				$cboRaSupplierName="cboRaSupplierName_".$i;
				$accDate="accDate_".$i;
				$cboRaFabricSource="cboRaFabricSource_".$i;
				$Remarks=str_replace("'","",$$txtRaRemarks);
				$str_rep=array("+", "&", "*", "(", ")", "=","'","\r", "\n",'"','#');
				$acc_remarks=str_replace($str_rep,' ',$Remarks);

				if (str_replace("'",'',$$updateIdAccDtls)!="")
				{
					$id_arr[]=str_replace("'",'',$$updateIdAccDtls);

					$data_array_up[str_replace("'",'',$$updateIdAccDtls)] =explode("*",("".$$cboRaSampleName."*".$$cboRaGarmentItem."*".$$cboRaSupplierName."*".$$accDate."*".$$cboRaFabricSource."*".$$cboRaTrimsGroup."*".$$txtRaDescription."*".$$txtRaBrandSupp."*".$$cboRaUom."*".$$txtRaReqDzn."*".$$txtRaReqQty."*'".$acc_remarks."'*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
					$rId_acc_status=execute_query( "update sample_development_dtls set acc_status=0 where sample_mst_id=$update_id and acc_status_id=".$$updateIdAccDtls."",0);
					$rId_acc_status_ac=execute_query( "update sample_development_dtls set acc_status=1,acc_status_id=".$$updateIdAccDtls." where sample_mst_id=$update_id and sample_name=".$$cboRaSampleName."",0);
					 
				$update_sample_dtls=execute_query("UPDATE sample_development_dtls set is_updated=0 where sample_mst_id=$update_id and acc_status_id=".$$updateIdAccDtls."",1);
				if($update_sample_dtls) $flag=1; else $flag=0;
				$update_all_dtls=execute_query("UPDATE sample_development_fabric_acc set is_updated=0 where sample_mst_id=$update_id and form_type=2 ",1);
				if($update_all_dtls) $flag=1; else $flag=0;
					 
					
				}
			 	else
				{
					if ($add_comma!=0) $data_array .=",";
					$data_array .="(".$id_dtls.",".$update_id.",".$$cboRaSampleName.",".$$cboRaGarmentItem.",".$$cboRaSupplierName.",".$$accDate.",".$$cboRaFabricSource.",".$$cboRaTrimsGroup.",".$$txtRaDescription.",".$$txtRaBrandSupp.",".$$cboRaUom.",".$$txtRaReqDzn.",".$$txtRaReqQty.",'".$acc_remarks."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0,2)";
					$rId_acc_status=execute_query( "update sample_development_dtls set acc_status=1,acc_status_id=".$id_dtls." where sample_mst_id=$update_id and sample_name=".$$cboRaSampleName."",0);
					$id_dtls=$id_dtls+1;
					$add_comma++;

				}

		    }

			$flag=1;
			if($data_array!="")
			{
				$rID=sql_insert("sample_development_fabric_acc",$field_array,$data_array,0);
				if($rID) $flag=1; else $flag=0;
			}
			

			if($data_array_up!="")
			{
				$rID1=execute_query(bulk_update_sql_statement("sample_development_fabric_acc", "id",$field_array_up,$data_array_up,$id_arr ));
				if($rID1) $flag=1; else $flag=0;
			}

			if($txtDeltedIdRa!="" || $txtDeltedIdRa!=0)
			{
 				$fields="is_deleted";
				$fields_sd="acc_status";
				$delSampleDtls=sql_multirow_update("sample_development_dtls",$fields_sd,"0","acc_status_id",$txtDeltedIdRa,0);
 				$del=sql_multirow_update("sample_development_fabric_acc",$fields,"1","id",$txtDeltedIdRa,0);

  			}


			if($db_type==0)
			{
				if($flag==1){
					mysql_query("COMMIT");
					echo "1**".str_replace("'",'',$update_id)."**3";
				}
				else{
					mysql_query("ROLLBACK");
					echo "10**";
				}
			}
			elseif($db_type==2 || $db_type==1 )
			{
				if($flag==1)
				{
					oci_commit($con);
					echo "1**".str_replace("'",'',$update_id)."**3";

				}
				else
				{
					oci_rollback($con);
					echo "10**";
				}
			}
			disconnect($con);
			die;
	}


	if ($operation==2)  // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$is_approved=return_field_value("is_approved","sample_development_mst","id=$update_id and entry_form_id=203 and status_active=1 and is_deleted=0");
		if($is_approved==1)
		{
			echo "323**";
			disconnect($con);die;
		}


		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";

		$rID=sql_delete("sample_development_fabric_acc",$field_array,$data_array,"sample_mst_id*form_type","".$update_id."*2",0);
		if($rID)
		{
			$update_dtls=execute_query("UPDATE sample_development_dtls set acc_status=0 where sample_mst_id=$update_id",1);
		}
		if($db_type==0)
		{
			if($rID  && $update_dtls )
			{
				mysql_query("COMMIT");
				echo "2**".str_replace("'","",$update_id);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$update_id);
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID  && $update_dtls )
			{
				oci_commit($con);
				echo "2**".str_replace("'","",$update_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$update_id);
			}
		}
		disconnect($con);
	}
}

if ($action=="save_update_delete_required_embellishment")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if ($operation==0)  // Insert Here
	{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}

			$id_dtls=return_next_id( "id", "sample_development_fabric_acc", 1 ) ;
			$id1=return_next_id( "id", "sample_develop_embl_color_size", 1 ) ;
 			$field_array= "id,sample_mst_id,sample_name_re,gmts_item_id_re,name_re,type_re,color_size_breakdown,fin_fab_qnty,rate,amount,remarks_re,inserted_by,insert_date,status_active,is_deleted,form_type,body_part_id,supplier_id,delivery_date";
			$field_array_size= "id,mst_id,dtls_id,sample_size_dtls_id,item_id,color_id,size_id,qnty,rate,amount,inserted_by,insert_date,status_active,is_deleted";
			$add_comma=0;	$data_array_size="";
			for ($i=1;$i<=$total_row;$i++)
		    {
				$cboReSampleName="cboReSampleName_".$i;
				$cboReGarmentItem="cboReGarmentItem_".$i;
				$cboReName="cboReName_".$i;
				$cboReType="cboReType_".$i;
				$cboReRemarks="txtReRemarks_".$i;

				$cboReSupplierName="cboReSupplierName_".$i;
				$cboReBodyPart="cboReBodyPart_".$i;
				$deliveryDate="deliveryDate_".$i;
				
				$txtReQty="txtReQty_".$i;
				$txtReRate="txtReRate_".$i;
				$txtReAmount="txtReAmount_".$i;
				$txtcolorBreakdown="txtcolorBreakdown_".$i;
				//$updateIdDtls="updateidRequiredEmbellishdtl_".$i;
				// fab_status_id,acc_status_id,embellishment_status_id
				$Remarks=str_replace("'","",$$cboReRemarks);
				$str_rep=array("+", "&", "*", "(", ")", "=","'","\r", "\n",'"','#');
				$emb_remarks=str_replace($str_rep,' ',$Remarks);

				if ($i!=1) $data_array .=",";
				$data_array .="(".$id_dtls.",".$update_id.",".$$cboReSampleName.",".$$cboReGarmentItem.",".$$cboReName.",".$$cboReType.",".$$txtcolorBreakdown.",".$$txtReQty.",".$$txtReRate.",".$$txtReAmount.",'".$emb_remarks."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0,3,".$$cboReBodyPart.",".$$cboReSupplierName.",".$$deliveryDate.")";

				//	CONS break down===============================================================================================
			if(str_replace("'",'',$$txtcolorBreakdown) !=''){
			
				//$rID_de1=execute_query( "delete from sample_develop_embl_color_size where  wo_booking_dtls_id =".$$txtbookingid."",0);
				$consbreckdown_array=explode('__',str_replace("'",'',$$txtcolorBreakdown));
				for($c=0;$c < count($consbreckdown_array);$c++){
					$consbreckdownarr=explode('_',$consbreckdown_array[$c]);
					//cons_break_down+=gmtItemID+'_'+gmtcolorid+'_'+gmtssizesid+'_'+reqqty+'_'+rate+'_'+amount+'_'+dtlsid+'_'+sizedtlsid+'_'+updateid;
					if ($c!=0) $data_array_size .=",";
					$data_array_size .="(".$id1.",".$update_id.",".$id_dtls.",".$consbreckdownarr[7].",".$consbreckdownarr[0].",".$consbreckdownarr[1].",".$consbreckdownarr[2].",".$consbreckdownarr[3].",".$consbreckdownarr[4].",".$consbreckdownarr[5].",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
					$id1=$id1+1;
					$add_comma++;
					//echo "10** insert into wo_emb_book_con_dtls (".$field_array2.") values ".$data_array2;die;
				}
			}
			//CONS break down end===============================================================================================
			
				
				$rId_emb_status=execute_query( "update sample_development_dtls set embellishment_status=1,embellishment_status_id=".$id_dtls." where sample_mst_id=$update_id and sample_name=".$$cboReSampleName."",0);
				$id_dtls=$id_dtls+1;

		    }
 			//echo "5**"."INSERT INTO sample_development_dtls(".$field_array."VALUES ".$data_array; die;
			$flag=1;
			$rID_1=sql_insert("sample_development_fabric_acc",$field_array,$data_array,1);
			if($rID_1) $flag=1;else $flag=0;
			
			if($data_array_size !=""){
				if($flag==1)
				{
				 $rID2=sql_insert("sample_develop_embl_color_size",$field_array_size,$data_array_size,1);
				 if($rID2) $flag=1;else $flag=0;
				}
			}
			//echo "10**".$rID_1.'='.$rID2.'='.$flag;die;
			

			if($db_type==0)
			{
				if($flag==1){
					mysql_query("COMMIT");
					echo "0**".str_replace("'",'',$update_id)."**4";
				}
				else{
					mysql_query("ROLLBACK");
					echo "10**";
				}
			}
			else if($db_type==2 || $db_type==1 )
			{
				if($flag==1)
				{
					oci_commit($con);
					echo "0**".str_replace("'",'',$update_id)."**4";

				}
			else{
					oci_rollback($con);
					echo "10**";
				}
			}
			disconnect($con);
			die;
	}
	else if ($operation==1)  // Update Here
	{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
  			$id_dtls=return_next_id( "id", "sample_development_fabric_acc", 1);

			$field_array_up="sample_name_re*gmts_item_id_re*name_re*type_re*color_size_breakdown*fin_fab_qnty*rate*amount*remarks_re*updated_by*update_date*body_part_id*supplier_id*delivery_date";
			$field_array= "id, sample_mst_id, sample_name_re,gmts_item_id_re,name_re,type_re,color_size_breakdown,fin_fab_qnty,rate,amount,remarks_re,inserted_by,insert_date,status_active,is_deleted,form_type,body_part_id,supplier_id,delivery_date";
			$field_array_size= "id,mst_id,dtls_id,sample_size_dtls_id,item_id,color_id,size_id,qnty,rate,amount,inserted_by,insert_date,status_active,is_deleted";
			
			$field_array_size_up= "mst_id*dtls_id*sample_size_dtls_id*item_id*color_id*size_id*qnty*rate*amount*updated_by*update_date*status_active*is_deleted";
			
			$id1=return_next_id( "id", "sample_develop_embl_color_size", 1 ) ;
			$add_comma=0;$add_comma2=0;$add_comma3=0; $data_array=""; //echo "10**";
			for ($i=1;$i<=$total_row;$i++)
		    {
				$cboReSampleName="cboReSampleName_".$i;
				$cboReGarmentItem="cboReGarmentItem_".$i;
				$cboReName="cboReName_".$i;
				$cboReType="cboReType_".$i;
				$cboReRemarks="txtReRemarks_".$i;
				$updateIdDtls="updateidRequiredEmbellishdtl_".$i;
				$cboReSupplierName="cboReSupplierName_".$i;
				$cboReBodyPart="cboReBodyPart_".$i;
				$deliveryDate="deliveryDate_".$i;
				$txtReQty="txtReQty_".$i;
				$txtReRate="txtReRate_".$i;
				$txtReAmount="txtReAmount_".$i;
				$txtcolorBreakdown="txtcolorBreakdown_".$i;

				$Remarks=str_replace("'","",$$cboReRemarks);
				$str_rep=array("+", "&", "*", "(", ")", "=","'","\r", "\n",'"','#');
				$emb_remarks=str_replace($str_rep,' ',$Remarks);
				
				if (str_replace("'",'',$$updateIdDtls)!="")
				{
					//	CONS break down===============================================================================================
				if(str_replace("'",'',$$txtcolorBreakdown) !=''){
				
					//$rID_de1=execute_query( "delete from sample_develop_embl_color_size where  dtls_id =".$$updateIdDtls."",0);
					$consbreckdown_array=explode('__',str_replace("'",'',$$txtcolorBreakdown));
					for($c=0;$c < count($consbreckdown_array);$c++){
						$consbreckdownarr=explode('_',$consbreckdown_array[$c]);
						if(str_replace("'",'',$consbreckdownarr[9])==0) $consbreckdownarr[9]="";
						if(str_replace("'",'',$consbreckdownarr[9])!="")
						{
						$size_mst_update_arr[]=str_replace("'",'',$consbreckdownarr[9]);
						$data_array_size_up[str_replace("'",'',$consbreckdownarr[9])] =explode("*",("".$update_id."*".$$updateIdDtls."*".$consbreckdownarr[7]."*".$consbreckdownarr[0]."*".$consbreckdownarr[1]."*".$consbreckdownarr[2]."*".$consbreckdownarr[3]."*".$consbreckdownarr[4]."*".$consbreckdownarr[5]."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'*1*0"));
						}
						else
						{
							$consbreckdown_array=explode('__',str_replace("'",'',$$txtcolorBreakdown));
							for($c=0;$c < count($consbreckdown_array);$c++){
								$consbreckdownarr=explode('_',$consbreckdown_array[$c]);
								//cons_break_down+=gmtItemID+'_'+gmtcolorid+'_'+gmtssizesid+'_'+reqqty+'_'+rate+'_'+amount+'_'+dtlsid+'_'+sizedtlsid+'_'+updateid;
								if ($c!=0) $data_array_size .=",";
								$data_array_size .="(".$id1.",".$update_id.",".$$updateIdDtls.",".$consbreckdownarr[7].",".$consbreckdownarr[0].",".$consbreckdownarr[1].",".$consbreckdownarr[2].",".$consbreckdownarr[3].",".$consbreckdownarr[4].",".$consbreckdownarr[5].",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
								$id1=$id1+1;
								$add_comma3++;
								//echo "10** insert into wo_emb_book_con_dtls (".$field_array2.") values ".$data_array2;die;
							}
						}
						//cons_break_down+=gmtItemID+'_'+gmtcolorid+'_'+gmtssizesid+'_'+reqqty+'_'+rate+'_'+amount+'_'+dtlsid+'_'+sizedtlsid+'_'+updateid;
					}
				}
				//CONS break down end===============================================================================================
				
					$id_arr[]=str_replace("'",'',$$updateIdDtls);

					$data_array_up[str_replace("'",'',$$updateIdDtls)] =explode("*",("".$$cboReSampleName."*".$$cboReGarmentItem."*".$$cboReName."*".$$cboReType."*".$$txtcolorBreakdown."*".$$txtReQty."*".$$txtReRate."*".$$txtReAmount."*'".$emb_remarks."'*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'*".$$cboReBodyPart."*".$$cboReSupplierName."*".$$deliveryDate.""));
					$rId_emb_status=execute_query( "update sample_development_dtls set embellishment_status=0 where sample_mst_id=$update_id and embellishment_status_id=".$$updateIdDtls."",0);
					$rId_emb_status_ac=execute_query( "update sample_development_dtls set is_updated=0,embellishment_status=1,embellishment_status_id=".$$updateIdDtls." where sample_mst_id=$update_id and sample_name=".$$cboReSampleName."",0);
					
					//$update_sample_dtls=execute_query("UPDATE sample_development_dtls set is_updated=0 where sample_mst_id=$update_id and sample_name=".$$cboReSampleName."",1);
					//if($update_sample_dtls) $flag=1; else $flag=0;
					$update_all_dtls=execute_query("UPDATE sample_development_fabric_acc set is_updated=0 where sample_mst_id=$update_id and form_type=3 ",1);
					if($update_all_dtls) $flag=1; else $flag=0;
				
				}
			 	else
				{
					//	CONS break down===============================================================================================
				if(str_replace("'",'',$$txtcolorBreakdown)!=''){
					//$data_array_size="";
					//$rID_de1=execute_query( "delete from sample_develop_embl_color_size where  dtls_id =".$$updateIdDtls."",0);
					$consbreckdown_array=explode('__',str_replace("'",'',$$txtcolorBreakdown));
					for($c=0;$c < count($consbreckdown_array);$c++){
						$consbreckdownarr=explode('_',$consbreckdown_array[$c]);
						//cons_break_down+=gmtItemID+'_'+gmtcolorid+'_'+gmtssizesid+'_'+reqqty+'_'+rate+'_'+amount+'_'+dtlsid+'_'+sizedtlsid+'_'+updateid;
						if ($c!=0) $data_array_size .=",";
						$data_array_size .="(".$id1.",".$update_id.",".$id_dtls.",".$consbreckdownarr[7].",".$consbreckdownarr[0].",".$consbreckdownarr[1].",".$consbreckdownarr[2].",".$consbreckdownarr[3].",".$consbreckdownarr[4].",".$consbreckdownarr[5].",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
						$id1=$id1+1;
						$add_comma3++;
						//echo "10** insert into wo_emb_book_con_dtls (".$field_array2.") values ".$data_array2;die;
					}
				}
				//CONS break down end===============================================================================================
				
					if ($add_comma!=0) $data_array .=",";
					$data_array .="(".$id_dtls.",".$update_id.",".$$cboReSampleName.",".$$cboReGarmentItem.",".$$cboReName.",".$$cboReType.",".$$txtcolorBreakdown.",".$$txtReQty.",".$$txtReRate.",".$$txtReAmount.",".$$cboReRemarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0,3,".$$cboReBodyPart.",".$$cboReSupplierName.",".$$deliveryDate.")";
					$rId_emb_status=execute_query( "update sample_development_dtls set embellishment_status=1,embellishment_status_id=".$id_dtls."  where sample_mst_id=$update_id and sample_name=".$$cboReSampleName."",0);
					$id_dtls=$id_dtls+1;
					$add_comma++;

				}

		    }
			//echo $data_array.'=='; die;
			//$rID_1=sql_insert("sample_development_dtls",$field_array2,$data_array2,1);

			$flag=1;
			if($data_array!="")
			{
				//echo "insert into sample_development_dtls (".$field_array.") values ".$data_array;
				$rID=sql_insert("sample_development_fabric_acc",$field_array,$data_array,0);
				if($rID) $flag=1; else $flag=0;
			}
			/*echo '=='.$data_array.'==';
			die;*/
			if($data_array_up!="")
			{
				$rID1=execute_query(bulk_update_sql_statement("sample_development_fabric_acc", "id",$field_array_up,$data_array_up,$id_arr ));

				if($rID1) $flag=1; else $flag=0;
			}
			if($data_array_size_up!="")
			{
				if($flag==1)
				{
				$rID2=execute_query(bulk_update_sql_statement("sample_develop_embl_color_size", "id",$field_array_size_up,$data_array_size_up,$size_mst_update_arr ));
				//echo "10**".bulk_update_sql_statement("sample_develop_embl_color_size", "id",$field_array_size_up,$data_array_size_up,$size_mst_update_arr );die;
				
				if($rID2) $flag=1; else $flag=0;
				}
			}
			
			if($data_array_size !=""){
				if($flag==1)
				{
				 $rID2=sql_insert("sample_develop_embl_color_size",$field_array_size,$data_array_size,1);
				if($rID2) $flag=1;else $flag=0;
				}
			}

			if($txtDeltedIdRe!="" || $txtDeltedIdRe!=0)
			{

				$fields="is_deleted";
				$fields2="status_active*is_deleted";
				$fields_sd="embellishment_status";
				$delSampleDtls=sql_multirow_update("sample_development_dtls",$fields_sd,"0","embellishment_status_id",$txtDeltedIdRe,0);
				// echo $delSampleDtls;die;
				$del=sql_multirow_update("sample_development_fabric_acc",$fields,"1","id",$txtDeltedIdRe,0);
				$size_del=sql_multirow_update("sample_develop_embl_color_size",$fields2,"0*1","dtls_id",$txtDeltedIdRe,0);
				//echo $delSampleDtls." second ".$del;


				//$rId_emb_status=execute_query( "update sample_development_dtls set embellishment_status=0 where sample_mst_id=$update_id and sample_name=".$$cboReSampleName."",0);
 			}

			if($db_type==0)
			{
				if($flag==1){
					mysql_query("COMMIT");
					echo "1**".str_replace("'",'',$update_id)."**4";
				}
				else{
					mysql_query("ROLLBACK");
					echo "10**";
				}
			}
			elseif($db_type==2 || $db_type==1 )
			{
				if($flag==1)
				{
					oci_commit($con);
					echo "1**".str_replace("'",'',$update_id)."**4";

				}
				else
				{
					oci_rollback($con);
					echo "10**";
				}
			}
			disconnect($con);
			die;
	}
	else if ($operation==2)  // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$is_approved=return_field_value("is_approved","sample_development_mst","id=$update_id and entry_form_id=203 and status_active=1 and is_deleted=0");
		if($is_approved==1)
		{
			echo "323**";
			disconnect($con);die;
		}


		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";

		$rID=sql_delete("sample_development_fabric_acc",$field_array,$data_array,"sample_mst_id*form_type","".$update_id."*3",0);
		if($rID)
		{
			$update_dtls=execute_query("UPDATE sample_development_dtls set embellishment_status=0 where sample_mst_id=$update_id",1);
		}
		if($db_type==0)
		{
			if($rID  && $update_dtls )
			{
				mysql_query("COMMIT");
				echo "2**".str_replace("'","",$update_id);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$update_id);
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID  && $update_dtls )
			{
				oci_commit($con);
				echo "2**".str_replace("'","",$update_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$update_id);
			}
		}
		disconnect($con);
	}
}
if($action=="get_smv_value"){
	$ex_data = explode("**",$data);
	$job_id=$ex_data[0];
	$gmts_item_id=$ex_data[1];
	$order_gmts_item=sql_select("SELECT gmts_item_id, smv_pcs from wo_po_details_mas_set_details where job_id=$job_id and gmts_item_id=$gmts_item_id");
	foreach($order_gmts_item as $row){
		$smv_value=$row[csf('smv_pcs')];
	}
	echo $smv_value;
}

if($action=="check_save_update"){
	$ex_data = explode("**",$data);
	$up_id=$ex_data[0];
	$type=$ex_data[1];
	if($type==1){
		$sql_data=sql_select("SELECT id from sample_development_dtls where entry_form_id=203 and sample_mst_id='$up_id'  and  is_deleted=0  and status_active=1 order by id ASC");	
	}
	else if($type==2)//Fabric
	{
		$sql_data=sql_select("SELECT id from sample_development_fabric_acc where sample_mst_id='$up_id' and form_type=1    and  is_deleted=0  and status_active=1 order by id ASC");
	}
	else if($type==3)//Accessories
	{
		$sql_data=sql_select("SELECT id from sample_development_fabric_acc where sample_mst_id='$up_id' and form_type=2    and  is_deleted=0  and status_active=1 order by id ASC");
	}
	else if($type==4){
		$sql_data=sql_select("SELECT id from sample_development_fabric_acc where sample_mst_id='$up_id' and form_type=3    and  is_deleted=0  and status_active=1  order by id ASC");
	}
	if(count($sql_data)>0){
		echo 1;
	}
	else{
		echo 0;
	}
	

}
//If Sample name,Gmt item Color
if($action=="get_update_found"){
	$ex_data = explode("**",$data);
	$up_id=$ex_data[0];
	$type=$ex_data[1];
	if($type==1){
		$sql_data=sql_select("SELECT is_updated from sample_development_dtls where entry_form_id=203 and sample_mst_id='$up_id' and  is_deleted=0  and status_active=1 and is_updated=1 order by id ASC");	
	}
	else if($type==2)//Fabric
	{
		$sql_data=sql_select("SELECT is_updated from sample_development_fabric_acc where sample_mst_id='$up_id' and form_type=1 and  is_deleted=0  and status_active=1 and is_updated=1 order by id ASC");
	}
	else if($type==3){
		$sql_data=sql_select("SELECT is_updated from sample_development_fabric_acc where sample_mst_id='$up_id' and form_type=2 and  is_deleted=0  and status_active=1 and is_updated=1  order by id ASC");
	}
	else if($type==4){
		$sql_data=sql_select("SELECT is_updated from sample_development_fabric_acc where sample_mst_id='$up_id' and form_type=3 and  is_deleted=0  and status_active=1 and is_updated=1  order by id ASC");
	}
	if(count($sql_data)>0){
		echo 1;
	}
	else{
		echo 0;
	}
	

}

if($action=="load_php_dtls_form")
{
	$ex_data = explode("**",$data);
	$up_id=$ex_data[0];
	$type=$ex_data[1];
	//echo $ex_data[1];
	$color_arr=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name" );
	$readonly="";
	$sample_mst_data=sql_select("SELECT sample_stage_id, quotation_id, buyer_name from sample_development_mst where status_active=1 and is_deleted=0 and entry_form_id=203 and id='$up_id'");
	$sample_stage=0;
	if(count($sample_mst_data)>0){
		foreach($sample_mst_data as $row){
			$sample_stage=$row[csf('sample_stage_id')];
			$job_id=$row[csf('quotation_id')];
			$buyer_name=$row[csf('buyer_name')];
		}               
	}
	if($sample_stage==1){
		$order_gmts_item=sql_select("SELECT gmts_item_id, smv_pcs from wo_po_details_mas_set_details where job_id=$job_id");
		if(count($order_gmts_item)>0){
			foreach($order_gmts_item as $row){
				$order_gmts_arr[$row[csf('gmts_item_id')]]['gmt_item']=$row[csf('gmts_item_id')];
				$order_gmts_arr[$row[csf('gmts_item_id')]]['smv_pcs']=$row[csf('smv_pcs')];
				$order_gmts_id_arr[$row[csf('gmts_item_id')]]=$row[csf('gmts_item_id')];
			}
		}
		$readonly="readonly";
	}
	$order_gmts_str=implode(",",$order_gmts_id_arr);
	if($type==1)//Sample Details
	{
		$buyer_aganist_req=return_library_array( "select id,buyer_name from sample_development_mst where is_deleted=0 and status_active=1 order by buyer_name", "id", "buyer_name"  );
		$sql_sam="SELECT id, sample_name, gmts_item_id,is_updated, smv,article_no, sample_color, sample_prod_qty, submission_qty, delv_start_date, delv_end_date, sample_charge, sample_curency, size_data,fabric_status,acc_status,embellishment_status,sent_to_buyer_date,comments,fab_status_id from sample_development_dtls where entry_form_id=203 and sample_mst_id='$up_id' and  is_deleted=0  and status_active=1 order by id ASC";
		//echo $sql_sam; die;
		$sql_result =sql_select($sql_sam);
		
		$value=return_field_value("quotation_id","sample_development_mst","entry_form_id=203 and id='$up_id' and status_active=1 and is_deleted=0");
		$i=1;
		$get_smv_fnc="";
		$fixed_gmt_item="";
		if(count($sql_result)>0)
		{
			foreach($sql_result as $row)
			{
				if($sample_stage==1){
					$get_smv_fnc="get_smv_value($i,this.value)";
					$fixed_gmt_item=$order_gmts_str;
				}
				else{
					if($value!="" || $value!=0)
					{
						$fixed_gmt_item=$row[csf("gmts_item_id")];
					}
				}
				
				 ?>
				<tr id="tr_<?=$i; ?>" style="height:10px;" class="general">
					<td>
						<?
						if($row[csf("fabric_status")]==1 || $row[csf("acc_status")]==1 || $row[csf("embellishment_status")]==1)
						{
							$sql="select a.id,a.sample_name,b.sequ from lib_sample a,lib_buyer_tag_sample b where a.id=b.tag_sample and b.buyer_id=$buyer_aganist_req[$up_id] and b.sequ is not null and a.status_active=1 and a.is_deleted=0  group by  a.id,a.sample_name,b.sequ order by b.sequ";
							echo create_drop_down( "cboSampleName_$i", 100, $sql,"id,sample_name", 1, "select Sample", $row[csf("sample_name")], "",1);
						}
						else
						{
							$sql="select a.id,a.sample_name,b.sequ from lib_sample a,lib_buyer_tag_sample b where a.id=b.tag_sample and b.buyer_id=$buyer_aganist_req[$up_id] and b.sequ is not null and a.status_active=1 and a.is_deleted=0  group by  a.id,a.sample_name,b.sequ order by b.sequ";
							echo create_drop_down( "cboSampleName_$i", 100, $sql,"id,sample_name", 1, "select Sample", $row[csf("sample_name")], "",0);
						}
						?>
					</td>
					<td>
						<?
						//echo create_drop_down( "cboGarmentItem_$i", 100, $garments_item,"", 1, "Select Item", $value['gmt_item'], "get_smv_value( $i, this.value)","",$order_gmts_str);
						if($row[csf("fabric_status")]==1 || $row[csf("acc_status")]==1 || $row[csf("embellishment_status")]==1)
						{
							if($value=="" || $value==0)
							{
								echo create_drop_down( "cboGarmentItem_$i", 100, $garments_item,"", 1, "Select Item",$row[csf("gmts_item_id")], $get_smv_fnc,1,$fixed_gmt_item);
							}
							else
							{
								echo create_drop_down( "cboGarmentItem_$i", 100, $garments_item,"", 1, "Select Item",$row[csf("gmts_item_id")], $get_smv_fnc,1,$fixed_gmt_item);
							}
						}
						else
						{
							if($value=="" || $value==0)
							{
								echo create_drop_down( "cboGarmentItem_$i", 100, $garments_item,"", 1, "Select Item",$row[csf("gmts_item_id")], $get_smv_fnc,0,$fixed_gmt_item);
							}
							else
							{
								echo create_drop_down( "cboGarmentItem_$i", 100, $garments_item,"", 1, "Select Item",$row[csf("gmts_item_id")], $get_smv_fnc,0,$fixed_gmt_item);
							}
						}
						?>
					</td>
					<td>
						<input style="width:40px;" type="text" class="text_boxes_numeric" name="txtSmv_<?=$i; ?>" id="txtSmv_<?=$i; ?>" value="<?=$row[csf("smv")]; ?>" <?= $readonly ?>/>
						<input type="hidden" id="updateidsampledtl_<?=$i; ?>" name="updateidsampledtl_<?=$i; ?>" style="width:20px" value="<?=$row[csf("id")]; ?>" />
                        <input type="hidden" id="txtisupdated_<?=$i; ?>" name="txtisupdated_<?=$i; ?>" style="width:20px" value="<?=$row[csf("is_updated")]; ?>" />
                        <input type="hidden" id="txtDeltedIdSd" name="txtDeltedIdSd"  class="text_boxes" style="width:20px" value="" />
					</td>
					<td><input style="width:60px;" type="text" class="text_boxes"  name="txtArticle_<?=$i; ?>" id="txtArticle_<?=$i; ?>" placeholder="Write" value="<?=$row[csf("article_no")]; ?>" /></td>
					<td><input style="width:80px;" type="text" class="text_boxes"  name="txtColor_<? echo $i; ?>" id="txtColor_<? echo $i; ?>" placeholder="write/browse" onDblClick="openmypage_color_size('requires/sample_requisition_with_booking_controller.php?action=color_popup','Color Search','1','<? echo $i; ?>');" value="<? echo $color_arr[$row[csf("sample_color")]]; ?>" <?= $readonly ?>/>
                    <input type="hidden" id="hiddenColorid_<? echo $i; ?>" name="hiddenColorid_<? echo $i; ?>"  class="text_boxes" style="width:20px" value="<? echo $row[csf("sample_color")];?>" />
                    </td>
					<td>
						<?
						if($row[csf("fabric_status")]==1 || $row[csf("acc_status")]==1 || $row[csf("embellishment_status")]==1)
						{
							?>
							<input style="width:100px;" type="text" class="text_boxes_numeric"  name="txtSampleProdQty_<? echo $i; ?>" readonly id="txtSampleProdQty_<? echo $i; ?>" placeholder="browse"  ondblclick="openmypage_sizeinfo('requires/sample_requisition_with_booking_controller.php?action=sizeinfo_popup','Size Search','<? echo $i;?>')" value="<? echo $row[csf("sample_prod_qty")]; ?>"    />

							<?
							//onFocus="openmypage_sizeinfo('requires/sample_requisition_with_booking_controller.php?action=sizeinfo_popup_mouseover','Size Search','<? echo $i;
						}
						else {
							?>
							<input style="width:100px;" type="text" class="text_boxes_numeric"  name="txtSampleProdQty_<? echo $i; ?>" readonly id="txtSampleProdQty_<? echo $i; ?>" placeholder="browse"   ondblclick="openmypage_sizeinfo('requires/sample_requisition_with_booking_controller.php?action=sizeinfo_popup','Size Search','<? echo $i;?>')"  value="<? echo $row[csf("sample_prod_qty")]; ?>"/>
							<?
						}
						?>
					</td>
					<input type="hidden" class="text_boxes"  name="txtAllData_<? echo $i;?>" id="txtAllData_<? echo $i;?>" value="<? echo $row[csf("size_data")]; ?>"/>

					<td><input style="width:100px;" type="text" class="text_boxes_numeric"  name="txtSubmissionQty_<? echo $i; ?>" readonly id="txtSubmissionQty_<? echo $i; ?>" placeholder=""  value="<? echo $row[csf("submission_qty")]; ?>" /></td>
					<td><input style="width:85px;" class="datepicker" name="txtDelvStartDate_<? echo $i; ?>" id="txtDelvStartDate_<? echo $i; ?>" value="<? echo change_date_format($row[csf("delv_start_date")]); ?>"/></td>
					<td><input style="width:85px;" class="datepicker" name="txtDelvEndDate_<? echo $i; ?>" id="txtDelvEndDate_<? echo $i; ?>" value="<? echo change_date_format($row[csf("delv_end_date")]); ?>" /></td>
					<td><input style="width:85px;" class="datepicker" name="txtBuyerSubDate_<? echo $i; ?>" id="txtBuyerSubDate_<? echo $i; ?>" value="<? echo change_date_format($row[csf("sent_to_buyer_date")]); ?>" /></td>

					<td><input style="width:70px;" type="text" class="text_boxes_numeric"  name="txtChargeUnit_<? echo $i; ?>" id="txtChargeUnit_<? echo $i; ?>" placeholder="write" value="<? echo $row[csf("sample_charge")]; ?>"/></td>
					<td><? echo create_drop_down( "cboCurrency_$i", 70, $currency, "","","",$row[csf("sample_curency")], "", "", "" ); ?></td>
					<td><input type="button" class="image_uploader" name="txtFile_<? echo $i; ?>" id="txtFile_<? echo $i; ?>" size="10" value="ADD IMAGE" onClick="file_uploader ( '../../', document.getElementById('updateidsampledtl_<? echo $i;?>').value,'', 'sample_details_1', 0 ,1)"></td>
					<td><input style="width:70px;" type="text" class="text_boxes"  name="txtRemarks_<? echo $i; ?>" id="txtRemarks_<? echo $i; ?>" placeholder="click"  readonly="" onClick="openmypage_remarks('requires/sample_requisition_with_booking_controller.php?action=all_remarks_popup','Remarks','1',<? echo $i; ?>);"  value="<? echo $row[csf("comments")]; ?>"/></td>
					<td>
						<input type="button" name="additionalvalue_<? echo $i; ?>" id="additionalvalue_<? echo $i; ?>" class="formbuttonplasminus" value="Additional Value" onClick="openpage_additionalvalue(<? echo $i; ?>);" style="width:100px"/>
						<input type="hidden" name="hiddenadditionalvaluedata_<? echo $i; ?>" id="hiddenadditionalvaluedata_<? echo $i; ?>" value="">
					</td>

					<td>
						<?
						if($row[csf("fabric_status")] ==1 || $row[csf("acc_status")]==1 || $row[csf("embellishment_status")]==1)
						{
							?>
							<input type="button" id="increase_<? echo $i; ?>" name="increase_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(<? echo $i; ?>)" />
							<input type="button" id="decrease_<? echo $i; ?>" name="decrease_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="" />
							<?
						}
						else
						{
							?>
							<input type="button" id="increase_<? echo $i; ?>" name="increase_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(<? echo $i; ?>)" />
							<input type="button" id="decrease_<? echo $i; ?>" name="decrease_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
							<?
						}
						?>
					</td>
				</tr>
				<?
				$i++;
			}
		}
		else{
			if($sample_stage==1){		
				$i=1;			
				foreach($order_gmts_arr as $gmtsid=>$value){ 				
					?>
					<tr id="tr_<?=$i?>" style="height:10px;" class="general">
						<td id="sample_td">
						<?
							echo create_drop_down( "cboSampleName_$i", 100, "select a.id,a.sample_name,b.sequ from lib_sample a,lib_buyer_tag_sample b where a.id=b.tag_sample and  b.buyer_id=$buyer_name and b.sequ  is not null and a.status_active=1 and a.is_deleted=0  group by  a.id,a.sample_name,b.sequ order by b.sequ ","id,sample_name", 1, "Select Sample", $selected, "" );
						?>
						</td>
						<td align="center" id="item_id_<?=$i?>">
							<?
							echo create_drop_down( "cboGarmentItem_$i", 100, $garments_item,"", 1, "Select Item", $value['gmt_item'], "get_smv_value( $i, this.value)","",$order_gmts_str);

							?>
						</td>
						<td align="center" id="smv_<?=$i?>">
							<input style="width:40px;" type="text" class="text_boxes_numeric"  name="txtSmv_<?=$i?>" id="txtSmv_<?=$i?>" value="<?= $value['smv_pcs'] ?>" readonly/>
							<input type="hidden" id="updateidsampledtl_<?=$i?>" name="updateidsampledtl_<?=$i?>"  class="text_boxes" style="width:20px" value="" />
                             <input type="hidden" id="txtisupdated_1" name="txtisupdated_1" style="width:20px"  />
						</td>
						<input type="hidden" id="txtDeltedIdSd" name="txtDeltedIdSd"  class="text_boxes" style="width:20px" value="" />
						<td align="center" id="article_<?=$i?>">
							<input style="width:60px;" type="text" class="text_boxes"  name="txtArticle_1" id="txtArticle_<?=$i?>" placeholder="Write" />
						</td>
						<td align="center" id="color_<?=$i?>">
							<input style="width:80px;" type="text" class="text_boxes"  name="txtColor_<?=$i?>" id="txtColor_<?=$i?>" placeholder="Browse" onDblClick="openmypage_color_size('requires/sample_requisition_with_booking_controller.php?action=color_popup','Color Search','1','<?=$i?>');" readonly/>
								<input type="hidden" id="hiddenColorid_<?=$i?>" name="hiddenColorid_<?=$i?>"  class="text_boxes" style="width:20px"  />
						</td>

						<td align="center" id="sample_prod_qty_<?=$i?>">
							<input style="width:100px;" type="text" class="text_boxes_numeric"  name="txtSampleProdQty_<?=$i?>" id="txtSampleProdQty_<?=$i?>"  readonly placeholder="Browse" onDblClick="openmypage_sizeinfo('requires/sample_requisition_with_booking_controller.php?action=sizeinfo_popup','Size Search','<?=$i?>')" /><input type="hidden" class="text_boxes"  name="txtAllData_<?=$i?>" id="txtAllData_<?=$i?>"/>

						</td>

						<td align="center" id="submission_qty_<?=$i?>">
							<input style="width:100px;" type="text" class="text_boxes_numeric"  name="txtSubmissionQty_<?=$i?>" id="txtSubmissionQty_<?=$i?>" placeholder="Display" readonly />
						</td>

						<td align="center" id="delv_start_date_<?=$i?>">
							<input style="width:85px;" type="text" class="datepicker" placeholder="Select Date" autocomplete="off" name="txtDelvStartDate_<?=$i?>" id="txtDelvStartDate_<?=$i?>" />
						</td>


						<td align="center" id="delv_end_date_<?=$i?>">
							<input style="width:85px;" type="text" class="datepicker" placeholder="Select Date" autocomplete="off" name="txtDelvEndDate_<?=$i?>" id="txtDelvEndDate_<?=$i?>" />
						</td>

						<td align="center" id="buyer_sub_date_<?=$i?>">
							<input style="width:85px;" type="text" class="datepicker" placeholder="Select Date" autocomplete="off" name="txtBuyerSubDate_<?=$i?>" id="txtBuyerSubDate_<?=$i?>" />
						</td>


						<td align="center" id="charge_unit_<?=$i?>">
							<input style="width:70px;" type="text" class="text_boxes_numeric"  name="txtChargeUnit_<?=$i?>" id="txtChargeUnit_<?=$i?>" placeholder="Write"/>
						</td>

						<td align="center" id="currency_<?=$i?>">

							<?
							echo create_drop_down( "cboCurrency_$i", 70, $currency, "","","",2, "", "", "" );
							?>
						</td>

						<td id="image_<?=$i?>"><input type="button" class="image_uploader" name="txtFile_1" id="txtFile_1" onClick="file_uploader ( '../../', document.getElementById('updateidsampledtl_<?=$i?>').value,'', 'sample_details_1', 0 ,1)" style="" value="ADD IMAGE"></td>
						<td align="center" id="remarks_<?=$i?>">
							<input style="width:70px;" type="text" class="text_boxes"  name="txtRemarks_<?=$i?>" id="txtRemarks_<?=$i?>" placeholder="click"  readonly="" onClick="openmypage_remarks('requires/sample_requisition_with_booking_controller.php?action=all_remarks_popup','Remarks','1','<?= $i ?>');"/>
						</td>
						<td>
							<input type="button" name="additionalvalue_<?=$i?>" id="additionalvalue_<?=$i?>" class="formbuttonplasminus" value="Additional Value" onClick="openpage_additionalvalue(<?=$i?>);" style="width:100px"/>
							<input type="hidden" name="hiddenadditionalvaluedata_<?=$i?>" id="hiddenadditionalvaluedata_<?=$i?>" value="">
						</td>

						<td width="70">
							<input type="button" id="increase_<?=$i?>" name="increase_<?=$i?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(<?=$i?>,this)" />
							<input type="button" id="decrease_<?=$i?>" name="decrease_<?=$i?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<?=$i?>);" />
						</td>
					</tr>
				<? 
				$i++;
				}
			}
		}
	}
	else if($type==2)//Fabric
	{
		$sample_color_sql=sql_select("select sample_mst_id,sample_name,gmts_item_id,sample_color from sample_development_dtls where entry_form_id=203 and sample_mst_id=$up_id  and is_deleted=0  and status_active=1 order by id ASC");

		foreach($sample_color_sql as $row)
		{
			$sample_color_arr[$row[csf("sample_name")]][$row[csf("gmts_item_id")]].=$color_arr[$row[csf("sample_color")]].'***';
		}
		//print_r($sample_color_arr);

		$sql_fabric="SELECT id,sample_mst_id,sample_name,gmts_item_id,body_part_id,fabric_nature_id,fabric_description,gsm,dia,sample_color,color_type_id,width_dia_id,uom_id,required_dzn,required_qty,color_data, determination_id,fabric_source,delivery_date,process_loss_percent,grey_fab_qnty,remarks_ra, collar_cuff_breakdown,yarn_dtls from sample_development_fabric_acc where sample_mst_id='$up_id' and form_type=1 and  is_deleted=0  and status_active=1 order by id ASC";
		$sql_resultf =sql_select($sql_fabric);  $i=1;
		if(count($sql_resultf)>0)
		{
			foreach($sql_resultf as $row)
			{
				$fab_color=$row[csf("sample_color")];
				$gmts_item_id=$row[csf("gmts_item_id")];

				//$sample_name=$row[csf("sample_name")];
				$sample_colors=rtrim($sample_color_arr[$row[csf("sample_name")]][$row[csf("gmts_item_id")]],'***');
				$sample_color_id=$sample_color_id_arr[$row[csf("sample_name")]];

				$a=$row[csf("color_data")];
				$colors="";
				$c=explode("-----",$a);
				foreach($c as $v)
				{
					$cc=explode("_",$v);
					if($colors=="") $colors.=$cc[1]; else $colors.='***'.$cc[1];
				}

				if($sample_colors!=$colors)
				{
					$td_title='Sample Details Color is changed,You should update';
					$color_data='';
				}
				else
				{
					$td_color=''; $td_title='';
				}
				//echo $sample_colors.'='.$colors.'D';
				$color_data=$row[csf("color_data")];
				$sample_colors=$colors;
				?>
				<tr id="tr_<?=$i; ?>" style="height:10px;" class="general">
					<td id="rfSampleId_<?=$i; ?>">
						<?
						$sql="select a.id,a.sample_name,b.id as dtls_id from  lib_sample a ,sample_development_dtls b where  a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.id=b.sample_name and b.entry_form_id=203 and b.sample_mst_id='$up_id' group by a.id,a.sample_name,b.id order by b.id";
						$samp_array=array();
						$samp_result=sql_select($sql);
						if(count($samp_result)>0)
						{
							foreach($samp_result as $keys=>$vals)
							{
								$samp_array[$vals[csf("id")]]=$vals[csf("sample_name")];
							}
						}
						$smple=$row[csf("sample_name")];
						echo create_drop_down( "cboRfSampleName_$i", 90, $samp_array,"", '', "", $row[csf("sample_name")],"sample_wise_item($up_id,this.value,$i,1);");
						?>
					</td>
					<td id="rfItemId_<?=$i; ?>">
						<?
						$sql_f=sql_select("select id,gmts_item_id from sample_development_dtls where is_deleted=0  and status_active=1 and entry_form_id=203 and sample_mst_id='$up_id'");
						$gmtsf="";
						foreach ($sql_f as $rowf)
						{
							$gmtsf.=$rowf[csf("gmts_item_id")].",";
						}
						echo create_drop_down( "cboRfGarmentItem_$i", 100, $garments_item,"", 1, "Select Item",$row[csf("gmts_item_id")],"","",$gmtsf);
						?>
					</td>
					<td id="rf_body_part_<?=$i; ?>"><?=create_drop_down( "cboRfBodyPart_$i", 90, $body_part,"", 1, "Select Body Part", $row[csf("body_part_id")], "load_data_to_rfcolor('$i');"); ?></td>
					<td id="rf_fabric_nature_<?=$i; ?>"><?=create_drop_down( "cboRfFabricNature_$i", 90, $item_category,"", 0, "Select Fabric Nature",$row[csf("fabric_nature_id")] , "","","2,3"); ?></td>
					<td id="rf_fabric_description_<?=$i; ?>">
						<input style="width:62px;" type="text" class="text_boxes" name="txtRfFabricDescription_<?=$i; ?>" id="txtRfFabricDescription_<?=$i; ?>" placeholder="Write/Browse" onDblClick="open_fabric_description_popup(<?=$i; ?>);" readonly value="<?=$row[csf("fabric_description")]; ?>"/>
						<input type="hidden" name="libyarncountdeterminationid_<?=$i; ?>" id="libyarncountdeterminationid_<?=$i; ?>" class="text_boxes" style="width:10px" value="<?=$row[csf("determination_id")]; ?>">
					</td>
					<td id="rf_gsm_<?=$i; ?>">
						<input style="width:38px;" type="text" class="text_boxes_numeric"  name="txtRfGsm_<?=$i; ?>" id="txtRfGsm_<?=$i; ?>" value="<?=$row[csf("gsm")]; ?>"/>
                        <input type="hidden" id="updateidRequiredDtl_<?=$i; ?>" name="updateidRequiredDtl_<?=$i; ?>" value="<?=$row[csf("id")]; ?>"  />
						<input type="hidden" id="txtDeltedIdRf" name="txtDeltedIdRf" value="" />
					</td>
					<td id="rf_dia_<?=$i; ?>"><input style="width:38px;" type="text" class="text_boxes" name="txtRfDia_<?=$i; ?>" id="txtRfDia_<?=$i; ?>" value="<?=$row[csf("dia")]; ?>" /></td>
					<td id="rf_color_<?=$i; ?>" title="<?=$td_title;?>" >
                        <input style="width:58px; background-color:<?=$td_color;?>" type="text" class="text_boxes"  name="txtRfColor_<? echo $i; ?>" id="txtRfColor_<? echo $i; ?>" placeholder="browse" onDblClick="openmypage_rf_color('requires/sample_requisition_with_booking_controller.php?action=color_popup_rf','Color Search','<? echo $i;?>');" readonly  value="<?=$sample_colors;?>"/>
                        <input type="hidden" name="txtRfColorAllData_<? echo $i; ?>" id="txtRfColorAllData_<? echo $i; ?>" value="<? echo $color_data; ?>"  class="text_boxes">
					</td>
					<td id="rf_color_type_<?=$i; ?>"><?=create_drop_down( "cboRfColorType_$i", 80, $color_type,"", 1, "Select Color Type", $row[csf("color_type_id")], ""); ?></td>
					<td id="rf_width_dia_<?=$i; ?>"><?=create_drop_down( "cboRfWidthDia_$i", 80, $fabric_typee,"", 1, "Select Width/Dia", $row[csf("width_dia_id")], ""); ?></td>
					<td id="rf_uom_<?=$i; ?>"><?=create_drop_down( "cboRfUom_$i", 50, $unit_of_measurement,'', '',"",$row[csf("uom_id")],"","","12,27,1,23" ); ?></td>
					<td id="rf_req_dzn_<?=$i; ?>" style="display: none;"><input style="width:48px;" type="text" class="text_boxes_numeric"  name="txtRfReqDzn_<?=$i; ?>" id="txtRfReqDzn_<?=$i; ?>" placeholder="write" value="<? echo $row[csf("required_dzn")]; ?>" onBlur="calculate_required_qty('1','<?=$i ;?>');" /></td>

					<td id="rf_req_qty_<?=$i; ?>">
                        <input style="width:48px;" type="text" class="text_boxes_numeric" name="txtRfReqQty_<?=$i; ?>" id="txtRfReqQty_<?=$i; ?>" value="<?=$row[csf("required_qty")]; ?>" readonly/>
                        <input type="hidden" class="text_boxes" name="txtMemoryDataRf_<?=$i; ?>" id="txtMemoryDataRf_<?=$i; ?>" />
                    </td>
					<td id="rf_reqs_qty_<?=$i; ?>"><input style="width:48px;" type="text" class="text_boxes_numeric"  name="txtProcessLoss_<? echo $i; ?>" id="txtProcessLoss_<? echo $i; ?>" placeholder=""  onChange="calculate_requirement('<? echo $i; ?>');" value="<? echo $row[csf("process_loss_percent")]; ?>" readonly /></td>
					<td id="rf_grey_qnty_<?=$i; ?>"><input style="width:48px;" type="text" class="text_boxes_numeric"  name="txtGrayFabric_<? echo $i; ?>" id="txtGrayFabric_<? echo $i; ?>" value="<? echo $row[csf("grey_fab_qnty")]; ?>" placeholder="" readonly /></td>
					<td id="deliveryrfDateid_<?=$i; ?>"><input style="width:48px;" type="text" class="datepicker" placeholder="Select Date" autocomplete="off" name="fabricDelvDate_<?=$i; ?>" id="fabricDelvDate_<?=$i; ?>" value="<?=change_date_format($row[csf("delivery_date")]); ?>" /></td>
					<td id="rf_fab_<?=$i; ?>"><?=create_drop_down( "cboRfFabricSource_$i", 70, $fabric_source,'', '', "",$row[csf("fabric_source")],"","","1,2,4" ); ?></td>
					<td id="rf_image_<?=$i; ?>"><input type="button" class="image_uploader" name="txtRfFile_<? echo $i; ?>" id="txtRfFile_<? echo $i; ?>" onClick="file_uploader ( '../../', document.getElementById('updateidRequiredDtl_<? echo $i;?>').value,'', 'required_fabric_1', 0 ,1)" value="ADD IMAGE"></td>
					<td id="rf_yarn_dtls_<?=$i; ?>"><input style="width:48px;" type="text" class="text_boxes" name="txtRfyarndtls_<?=$i; ?>" id="txtRfyarndtls_<?=$i; ?>" value="<?=$row[csf("yarn_dtls")]; ?>"  placeholder="Browse/Write" onClick="openmypage_remarks2('requires/sample_requisition_with_booking_controller.php?action=all_remarks_popup','Yarn','2',<?=$i; ?>);" /></td>
                    <td id="rf_remarks_<?=$i; ?>"><input style="width:48px;" type="text" class="text_boxes"  name="txtRfRemarks_<?=$i; ?>" id="txtRfRemarks_<?=$i; ?>" value="<?=$row[csf("remarks_ra")]; ?>" placeholder="Click" readonly onClick="openmypage_remarks('requires/sample_requisition_with_booking_controller.php?action=all_remarks_popup','Remarks','2',<?=$i; ?>);" /></td>
					<td>
						<input type="button" name="collarCuff_<?=$i; ?>" id="collarCuff_<?=$i; ?>" class="formbuttonplasminus" value="Collar & Cuff" onClick="openpage_collarCuff(<?=$i; ?>);" style="width:80px"/>
						<input type="hidden" name="hiddencollarCuffdata_<?=$i; ?>" id="hiddencollarCuffdata_<?=$i; ?>" value="<?= $row[csf('collar_cuff_breakdown')] ?>">
					</td>
					<td>
						<input type="button" id="increaserf_<?=$i; ?>" name="increaserf_<?=$i; ?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_rf_tr(<?=$i; ?>);" />
						<input type="button" id="decreaserf_<?=$i; ?>" name="decreaserf_<?=$i; ?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_rf_deleteRow(<?=$i; ?>);" />
					</td>
				</tr>
				<?
				$i++;
			}
		}
		else{
			if($sample_stage==1){
				$sql_fabric="SELECT item_number_id as gmts_item_id,body_part_id,fab_nature_id as fabric_nature_id,fabric_description,lib_yarn_count_deter_id as determination_id,gsm_weight as gsm,color_type_id,width_dia_type as width_dia_id,uom as uom_id from wo_pre_cost_fabric_cost_dtls where job_id='$job_id' and is_deleted=0  and status_active=1 order by id asc";
				//echo $sql_fabric; die;
				$sql_resultf =sql_select($sql_fabric);  $i=1;
				if(count($sql_resultf)>0)
				{
					foreach($sql_resultf as $row)
					{
						?>
						<tr id="tr_<?=$i; ?>" style="height:10px;" class="general">
							<td id="rfSampleId_<?=$i; ?>">
								<?
								$sql="select a.id,a.sample_name,b.id as dtls_id from  lib_sample a ,sample_development_dtls b where  a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.id=b.sample_name and b.entry_form_id=203 and b.sample_mst_id='$up_id' group by a.id,a.sample_name,b.id order by b.id";
								$samp_array=array();
								$samp_result=sql_select($sql);
								if(count($samp_result)>0)
								{
									foreach($samp_result as $keys=>$vals)
									{
										$samp_array[$vals[csf("id")]]=$vals[csf("sample_name")];
									}
								}
								$smple=$row[csf("sample_name")];
								echo create_drop_down( "cboRfSampleName_$i", 90, $samp_array,"", '', "", $row[csf("sample_name")],"sample_wise_item($up_id,this.value,$i,1);");
								?>
							</td>
							<td id="rfItemId_<?=$i; ?>">
								<?
								$sql_f=sql_select("select id,gmts_item_id from sample_development_dtls where is_deleted=0  and status_active=1 and entry_form_id=203 and sample_mst_id='$up_id'");
								$gmtsf="";
								foreach ($sql_f as $rowf)
								{
									$gmtsf.=$rowf[csf("gmts_item_id")].",";
								}
								echo create_drop_down( "cboRfGarmentItem_$i", 100, $garments_item,"", 1, "Select Item",$row[csf("gmts_item_id")],"","",$gmtsf);
								?>
							</td>
							<td id="rf_body_part_<?=$i; ?>"><?=create_drop_down( "cboRfBodyPart_$i", 90, $body_part,"", 1, "Select Body Part", $row[csf("body_part_id")], "load_data_to_rfcolor('$i');"); ?></td>
							<td id="rf_fabric_nature_<?=$i; ?>"><?=create_drop_down( "cboRfFabricNature_$i", 90, $item_category,"", 0, "Select Fabric Nature",$row[csf("fabric_nature_id")] , "","","2,3"); ?></td>
							<td id="rf_fabric_description_<?=$i; ?>">
								<input style="width:62px;" type="text" class="text_boxes" name="txtRfFabricDescription_<?=$i; ?>" id="txtRfFabricDescription_<?=$i; ?>" placeholder="Write/Browse" onDblClick="open_fabric_description_popup(<?=$i; ?>);" readonly value="<?=$row[csf("fabric_description")]; ?>"/>
								<input type="hidden" name="libyarncountdeterminationid_<?=$i; ?>" id="libyarncountdeterminationid_<?=$i; ?>" class="text_boxes" style="width:10px" value="<?=$row[csf("determination_id")]; ?>">
							</td>
							<td id="rf_gsm_<?=$i; ?>">
								<input style="width:38px;" type="text" class="text_boxes_numeric"  name="txtRfGsm_<?=$i; ?>" id="txtRfGsm_<?=$i; ?>" value="<?=$row[csf("gsm")]; ?>"/>
								<input type="hidden" id="updateidRequiredDtl_<?=$i; ?>" name="updateidRequiredDtl_<?=$i; ?>" value="<?=$row[csf("id")]; ?>"  />
								<input type="hidden" id="txtDeltedIdRf" name="txtDeltedIdRf" value="" />
							</td>
							<td id="rf_dia_<?=$i; ?>"><input style="width:38px;" type="text" class="text_boxes" name="txtRfDia_<?=$i; ?>" id="txtRfDia_<?=$i; ?>" value="<?=$row[csf("dia")]; ?>" /></td>
							<td id="rf_color_<?=$i; ?>" title="<?=$td_title;?>" >
								<input style="width:58px; background-color:<?=$td_color;?>" type="text" class="text_boxes"  name="txtRfColor_<? echo $i; ?>" id="txtRfColor_<? echo $i; ?>" placeholder="browse" onDblClick="openmypage_rf_color('requires/sample_requisition_with_booking_controller.php?action=color_popup_rf','Color Search','<? echo $i;?>');" readonly  value="<?=$sample_colors;?>"/>
								<input type="hidden" name="txtRfColorAllData_<? echo $i; ?>" id="txtRfColorAllData_<? echo $i; ?>" value="<? echo $color_data; ?>"  class="text_boxes">
							</td>
							<td id="rf_color_type_<?=$i; ?>"><?=create_drop_down( "cboRfColorType_$i", 80, $color_type,"", 1, "Select Color Type", $row[csf("color_type_id")], ""); ?></td>
							<td id="rf_width_dia_<?=$i; ?>"><?=create_drop_down( "cboRfWidthDia_$i", 80, $fabric_typee,"", 1, "Select Width/Dia", $row[csf("width_dia_id")], ""); ?></td>
							<td id="rf_uom_<?=$i; ?>"><?=create_drop_down( "cboRfUom_$i", 50, $unit_of_measurement,'', '',"",$row[csf("uom_id")],"","","12,27,1,23" ); ?></td>
							<td id="rf_req_dzn_<?=$i; ?>" style="display: none;"><input style="width:48px;" type="text" class="text_boxes_numeric"  name="txtRfReqDzn_<?=$i; ?>" id="txtRfReqDzn_<?=$i; ?>" placeholder="write" value="<? echo $row[csf("required_dzn")]; ?>" onBlur="calculate_required_qty('1','<?=$i ;?>');" /></td>

							<td id="rf_req_qty_<?=$i; ?>">
								<input style="width:48px;" type="text" class="text_boxes_numeric" name="txtRfReqQty_<?=$i; ?>" id="txtRfReqQty_<?=$i; ?>" value="<?=$row[csf("required_qty")]; ?>" readonly/>
								<input type="hidden" class="text_boxes" name="txtMemoryDataRf_<?=$i; ?>" id="txtMemoryDataRf_<?=$i; ?>" />
							</td>
							<td id="rf_reqs_qty_<?=$i; ?>"><input style="width:48px;" type="text" class="text_boxes_numeric"  name="txtProcessLoss_<? echo $i; ?>" id="txtProcessLoss_<? echo $i; ?>" placeholder=""  onChange="calculate_requirement('<? echo $i; ?>');" value="<? echo $row[csf("process_loss_percent")]; ?>" readonly /></td>
							<td id="rf_grey_qnty_<?=$i; ?>"><input style="width:48px;" type="text" class="text_boxes_numeric"  name="txtGrayFabric_<? echo $i; ?>" id="txtGrayFabric_<? echo $i; ?>" value="<? echo $row[csf("grey_fab_qnty")]; ?>" placeholder="" readonly /></td>
							<td id="deliveryrfDateid_<?=$i; ?>"><input style="width:48px;" type="text" class="datepicker" placeholder="Select Date" autocomplete="off" name="fabricDelvDate_<?=$i; ?>" id="fabricDelvDate_<?=$i; ?>" value="<?=change_date_format($row[csf("delivery_date")]); ?>" /></td>
							<td id="rf_fab_<?=$i; ?>"><?=create_drop_down( "cboRfFabricSource_$i", 70, $fabric_source,'', '', "",$row[csf("fabric_source")],"","","1,2,4" ); ?></td>
							<td id="rf_image_<?=$i; ?>"><input type="button" class="image_uploader" name="txtRfFile_<? echo $i; ?>" id="txtRfFile_<? echo $i; ?>" onClick="file_uploader ( '../../', document.getElementById('updateidRequiredDtl_<? echo $i;?>').value,'', 'required_fabric_1', 0 ,1)" value="ADD IMAGE"></td>
							<td id="rf_yarn_dtls_<?=$i; ?>"><input style="width:48px;" type="text" class="text_boxes" name="txtRfyarndtls_<?=$i; ?>" id="txtRfyarndtls_<?=$i; ?>" value="<?=$row[csf("yarn_dtls")]; ?>"  placeholder="Browse/Write" onClick="openmypage_remarks2('requires/sample_requisition_with_booking_controller.php?action=all_remarks_popup','Yarn','2',<?=$i; ?>);" /></td>
							<td id="rf_remarks_<?=$i; ?>"><input style="width:48px;" type="text" class="text_boxes"  name="txtRfRemarks_<?=$i; ?>" id="txtRfRemarks_<?=$i; ?>" value="<?=$row[csf("remarks_ra")]; ?>" placeholder="Click" readonly onClick="openmypage_remarks('requires/sample_requisition_with_booking_controller.php?action=all_remarks_popup','Remarks','2',<?=$i; ?>);" /></td>
							<td>
								<input type="button" name="collarCuff_<?=$i; ?>" id="collarCuff_<?=$i; ?>" class="formbuttonplasminus" value="Collar & Cuff" onClick="openpage_collarCuff(<?=$i; ?>);" style="width:80px"/>
								<input type="hidden" name="hiddencollarCuffdata_<?=$i; ?>" id="hiddencollarCuffdata_<?=$i; ?>" value="<?= $row[csf('collar_cuff_breakdown')] ?>">
							</td>
							<td>
								<input type="button" id="increaserf_<?=$i; ?>" name="increaserf_<?=$i; ?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_rf_tr(<?=$i; ?>);" />
								<input type="button" id="decreaserf_<?=$i; ?>" name="decreaserf_<?=$i; ?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_rf_deleteRow(<?=$i; ?>);" />
							</td>
						</tr>
						<?
						$i++;
					}
				}
			}
		}
	}
	else if($type==3) //Accessories
	{
		$sql_sam="SELECT id,sample_mst_id,sample_name_ra,gmts_item_id_ra,trims_group_ra,description_ra,brand_ref_ra,uom_id_ra,req_dzn_ra,req_qty_ra,remarks_ra,supplier_id,delivery_date,fabric_source from sample_development_fabric_acc where sample_mst_id='$up_id' and form_type=2 and  is_deleted=0  and status_active=1 order by id ASC";
		$sql_result =sql_select($sql_sam);  $i=1;
		if(count($sql_result)>0)
		{
			foreach($sql_result as $row)
			{
				?>
				<tr  id="tr_<? echo $i;?>"  class="general">
					<td align="center" id="raSampleId_1" width="100">
						<?
						$sql="select a.id,a.sample_name,b.id as dtls_id from  lib_sample a ,sample_development_dtls b where  a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.id=b.sample_name and b.entry_form_id=203 and b.sample_mst_id='$up_id' group by a.id,a.sample_name,b.id order by b.id";
						$samp_array=array();
						$samp_result=sql_select($sql);
						if(count($samp_result)>0)
						{
							foreach($samp_result as $keys=>$vals)
							{
								$samp_array[$vals[csf("id")]]=$vals[csf("sample_name")];
							}
						}
						echo create_drop_down( "cboRaSampleName_$i", 100, $samp_array,"", '', "",$row[csf("sample_name_ra")], "sample_wise_item($up_id,this.value,$i,2);","");
						?>
					</td>
					<td align="center" id="raItemId_1" width="100">
						<?
						$sql_gmts=sql_select("select id,gmts_item_id from sample_development_dtls where is_deleted=0  and status_active=1 and entry_form_id=203 and sample_mst_id='$up_id'");
						$gmts="";
						foreach ($sql_gmts as $rows)
						{
							$gmts.=$rows[csf("gmts_item_id")].",";
						}
						echo create_drop_down( "cboRaGarmentItem_$i", 100, $garments_item,"", 1, "Select Item",$row[csf("gmts_item_id_ra")] , "",0,$gmts);
						?>
					</td>
					<td align="center" id="ra_trims_group_1" width="100">
						<?
						$sql="select item_name,id from lib_item_group where item_category=4 and is_deleted=0  and
						status_active=1 order by item_name";
						echo create_drop_down( "cboRaTrimsGroup_$i", 100, $sql,"id,item_name", 1, "Select Item", $row[csf("trims_group_ra")] , "load_uom_for_trims('$i',this.value);");

						?>
					</td>
					<td align="center" id="ra_description_1" width="130">
						<input style="width:130px;" type="text" class="text_boxes"  name="txtRaDescription_<? echo $i;?>" id="txtRaDescription_<? echo $i;?>" placeholder="Write" value="<? echo $row[csf("description_ra")]; ?>"/>

						<input type="hidden" id="updateidAccessoriesDtl_<? echo $i;?>" name="updateidAccessoriesDtl_<? echo $i;?>"  class="text_boxes" style="width:20px" value="<? echo $row[csf("id")]; ?>" />
					</td>
					<td>
						<?
						echo create_drop_down( "cboRaSupplierName_$i", 100, "select a.id,a.supplier_name from lib_supplier a,lib_supplier_party_type b  where a.status_active =1 and a.is_deleted=0  and b.party_type in (select party_type from lib_supplier_party_type where a.id=b.supplier_id and b.party_type in(4,5)) order by a.supplier_name","id,supplier_name", 1, "-- Select Supplier --", $row[csf("supplier_id")],
							"",0 );
							?>
						</td>
					<td align="center" id="ra_brand_supp_1" width="130">
						<input style="width:130px;" type="text" class="text_boxes"  name="txtRaBrandSupp_<? echo $i;?>" id="txtRaBrandSupp_<? echo $i;?>" placeholder="write" value="<? echo $row[csf("brand_ref_ra")]; ?>"/>
                        <input type="hidden" id="txtDeltedIdRa" name="txtDeltedIdRa"  class="text_boxes" style="width:20px" value="" />
					</td>
					<td align="center" id="ra_uom_1" width="100">
						<?
						echo create_drop_down( "cboRaUom_$i", 100, $unit_of_measurement,'', '', "",$row[csf("uom_id_ra")],"",1,"" );
						?>
					</td>
					<td align="center" id="ra_req_dzn_1" width="100">
						<input style="width:100px;" type="text" class="text_boxes_numeric"  name="txtRaReqDzn_<? echo $i;?>" id="txtRaReqDzn_<? echo $i;?>" placeholder="Write" value="<? echo $row[csf("req_dzn_ra")]; ?>"  />
						<? //onBlur="calculate_required_qty('2','<? echo $i ;');" ?>
					</td>
					<td align="center" id="ra_req_qty_1" width="100">
						<input style="width:100px;" type="text" class="text_boxes_numeric"  name="txtRaReqQty_<? echo $i;?>" id="txtRaReqQty_<? echo $i;?>" placeholder="Write" value="<? echo $row[csf("req_qty_ra")]; ?>" />
                        <input type="hidden" class="text_boxes"  name="txtMemoryDataRa_<? echo $i;?>" id="txtMemoryDataRa_<? echo $i;?>" />
					</td>
					<td align="center" id="deliveryraDateid_<? echo $i;?>">
						<input style="width:85px;" type="text" class="datepicker" placeholder="Select Date" autocomplete="off" name="accDate_<? echo $i;?>" id="accDate_<? echo $i;?>" value="<? echo change_date_format($row[csf("delivery_date")]); ?>" />
					</td>

					<td id="ra_fab_<?=$i; ?>"><?=create_drop_down( "cboRaFabricSource_$i", 80, $fabric_source,'', '', "",$row[csf("fabric_source")],"","","2,4" ); ?></td>
					<td align="center" id="ra_remarks_1" width="70">
						<input style="width:70px;" type="text" class="text_boxes"  name="txtRaRemarks_<? echo $i;?>" id="txtRaRemarks_<? echo $i;?>" placeholder="Click"  readonly="" onClick="openmypage_remarks('requires/sample_requisition_with_booking_controller.php?action=all_remarks_popup','Remarks','3',<? echo $i; ?>);"  value="<? echo $row[csf("remarks_ra")]; ?>" />
					</td>
					<td id="ra_image_1"><input type="button" class="image_uploader" name="txtRaFile_<? echo $i;?>" id="txtRaFile_<? echo $i;?>" onClick="file_uploader ( '../../', document.getElementById('updateidAccessoriesDtl_<? echo $i;?>').value,'', 'required_accessories_1', 0 ,1)"style="width:80px;" value="ADD IMAGE"></td>
					<td width="70">
						<input type="button" id="increasera_<? echo $i;?>" name="increasera_<? echo $i;?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_ra_tr(<? echo $i;?>)" />
						<input type="button" id="decreasera_<? echo $i;?>" name="decreasera_<? echo $i;?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_ra_deleteRow(<? echo $i;?>);" />
					</td>
				</tr>
				<?
				$i++;
			}
		}
	}
	else if($type==4) //Embellishment
	{
		 $sql_sam="SELECT id,sample_mst_id,sample_name_re,gmts_item_id_re,name_re,type_re,remarks_re,body_part_id,delivery_date,supplier_id,color_size_breakdown,fin_fab_qnty,rate,amount from sample_development_fabric_acc where sample_mst_id='$up_id' and form_type=3 and  is_deleted=0  and status_active=1  order by id ASC";
		$sql_result =sql_select($sql_sam);  $i=1;
		if(count($sql_result)>0)
		{
			foreach($sql_result as $row)
			{
				?>
				<tr id="tr_<? echo $i;?>" style="height:10px;" class="general">
					<td align="center" id="reSampleId_1">
						<?
						$sql="SELECT a.id,a.sample_name,b.id as dtls_id from  lib_sample a ,sample_development_dtls b where  a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.id=b.sample_name and b.entry_form_id=203 and b.sample_mst_id='$up_id' group by a.id,a.sample_name,b.id order by b.id";
						$samp_array=array();
						$samp_result=sql_select($sql);
						if(count($samp_result)>0)
						{
							foreach($samp_result as $keys=>$vals)
							{
								$samp_array[$vals[csf("id")]]=$vals[csf("sample_name")];
							}

						}

						echo create_drop_down( "cboReSampleName_$i", 140, $samp_array,"", '', "",$row[csf("sample_name_re")],"sample_wise_item($up_id,this.value,$i,3);","");
						?>

					</td>

					<td align="center" id="reItemIid_1">
						<?
						$sql_gmts_re=sql_select("select id,gmts_item_id from sample_development_dtls where is_deleted=0  and status_active=1 and entry_form_id=203 and sample_mst_id='$up_id'");
						$gmts="";
						foreach ($sql_gmts_re as $rowss)
						{
							$gmts.=$rowss[csf("gmts_item_id")].",";
						}
						echo create_drop_down( "cboReGarmentItem_$i", 140, $garments_item,"", 1, "Select Item",$row[csf("gmts_item_id_re")], "","",$gmts);
						?>

						<input type="hidden" id="updateidRequiredEmbellishdtl_<? echo $i;?>" name="updateidRequiredEmbellishdtl_<? echo $i;?>"   style="width:20px;" value="<? echo $row[csf("id")]; ?>" class="text_boxes"/>
						<input type="hidden" id="txtDeltedIdRe" name="txtDeltedIdRe"   style="width:20px;" value="" class="text_boxes"/>
					</td>
					<td align="center" id="re_name_1">
						<?
						       // $sql="select id,item_name from  lib_garment_item where status_active=1 and is_deleted=0";
						echo create_drop_down( "cboReName_$i", 140, $emblishment_name_array,"", 1, "Select Name", $row[csf("name_re")], "cbotype_loder($i);");

						?>
					</td>
					<td align="center" id="reType_<? echo $i ?>">
						<?
						$type_array=array(1=>$emblishment_print_type,2=>$emblishment_embroy_type,3=>$emblishment_wash_type,4=>$emblishment_spwork_type,5=>$emblishment_gmts_type);
						echo create_drop_down( "cboReType_$i", 140, $type_array[$row[csf("name_re")]],"", 1, "Select Type",$row[csf("type_re")] , "");

						?>
					</td>

					<td align="center" id="re_body_part_1">
						<?


						echo create_drop_down( "cboReBodyPart_$i", 95, $body_part,"", 1, "Select Body Part",$row[csf("body_part_id")], "");

						?>
					</td>
					<td>
					<?
                    echo create_drop_down( "cboReSupplierName_$i", 100, "select a.id,a.supplier_name from lib_supplier a,lib_supplier_party_type b  where a.status_active =1 and a.is_deleted=0  and b.party_type in (select party_type from lib_supplier_party_type where a.id=b.supplier_id and b.party_type=23) order by a.supplier_name","id,supplier_name", 1, "-- Select Supplier --", $row[csf("supplier_id")],
                        "",0 );
                        ?>
                    </td>
                    
                    <td align="center" id="re_qty_1">
                    <input style="width:50px;" type="text" class="text_boxes"  name="txtReQty_<? echo $i;?>" id="txtReQty_<? echo $i;?>" placeholder="Click"  readonly="" onClick="open_consumption_popup('requires/sample_requisition_with_booking_controller.php?action=consumption_popup', 'Consumtion Entry Form',<? echo $i;?>)" value="<? echo $row[csf("fin_fab_qnty")]; ?>"/>
                      <input style="width:40px;" type="hidden" class="text_boxes"  name="txtcolorBreakdown_<? echo $i;?>" id="txtcolorBreakdown_<? echo $i;?>" value="<? echo $row[csf("color_size_breakdown")]; ?>"   />

                    </td>
                    <td align="center" id="re_rate_1">
                        <input style="width:40px;" type="text" class="text_boxes"  name="txtReRate_<? echo $i;?>" id="txtReRate_<? echo $i;?>" value="<? echo $row[csf("rate")]; ?>" placeholder="Rate"  readonly="" />

                    </td>
                      <td align="center" id="re_amount_1">
                        <input style="width:50px;" type="text" class="text_boxes"  name="txtReAmount_<? echo $i;?>" id="txtReAmount_<? echo $i;?>" value="<? echo $row[csf("amount")]; ?>" placeholder="Amount"  readonly="" />
                    </td>

					<td align="center" id="re_remarks_1">
						<input style="width:90px;" type="text" class="text_boxes"  name="txtReRemarks_<? echo $i;?>" id="txtReRemarks_<? echo $i;?>" placeholder="Click"  readonly="" onClick="openmypage_remarks('requires/sample_requisition_with_booking_controller.php?action=all_remarks_popup','Remarks','4',<? echo $i; ?>);" value="<? echo $row[csf("remarks_re")]; ?>"/>
					</td>
					<td align="center" id="deliveryDateid_<? echo $i;?>">
					<input style="width:70px;" type="text" class="datepicker" placeholder="Select Date" autocomplete="off" name="deliveryDate_<? echo $i;?>" value="<? echo change_date_format($row[csf("delivery_date")]); ?>" id="deliveryDate_<? echo $i;?>" />
					</td>
					<td id="re_image_1"><input type="button" class="image_uploader" name="reTxtFile_<? echo $i;?>" id="reTxtFile_<? echo $i;?>" size="20" style="width:120px;" value="ADD/VIEW IMAGE" onClick="file_uploader ( '../../', document.getElementById('updateidRequiredEmbellishdtl_<? echo $i;?>').value,'', 'required_embellishment_1', 0 ,1);"></td>
					<td width="70">
						<input type="button" id="increasere_<? echo $i; ?>" name="increasere_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_re_tr(<? echo $i; ?>)" />
						<input type="button" id="decreasere_<? echo $i; ?>" name="decreasere_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_re_deleteRow(<? echo $i; ?>);" />
					</td>
				</tr>
				<?
				$i++;
			}
		}
		else{
			if($sample_stage==1){
				$sql_sam="SELECT id,emb_name as name_re, emb_type as type_re,body_part_id, nominated_supp_multi as supplier_id from wo_pre_cost_embe_cost_dtls where job_id='$job_id' and  is_deleted=0  and status_active=1 and  emb_type>0 order by id asc";
				$sql_result =sql_select($sql_sam);  $i=1;
				if(count($sql_result)>0)
				{
					foreach($sql_result as $row)
					{
						?>
						<tr id="tr_<? echo $i;?>" style="height:10px;" class="general">
							<td align="center" id="reSampleId_1">
								<?
								$sql="SELECT a.id,a.sample_name,b.id as dtls_id from  lib_sample a ,sample_development_dtls b where  a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.id=b.sample_name and b.entry_form_id=203 and b.sample_mst_id='$up_id' group by a.id,a.sample_name,b.id order by b.id";
								$samp_array=array();
								$samp_result=sql_select($sql);
								if(count($samp_result)>0)
								{
									foreach($samp_result as $keys=>$vals)
									{
										$samp_array[$vals[csf("id")]]=$vals[csf("sample_name")];
									}
		
								}
		
								echo create_drop_down( "cboReSampleName_$i", 140, $samp_array,"", '', "",$row[csf("sample_name_re")],"sample_wise_item($up_id,this.value,$i,3);","");
								?>
		
							</td>
		
							<td align="center" id="reItemIid_1">
								<?
								$sql_gmts_re=sql_select("select id,gmts_item_id from sample_development_dtls where is_deleted=0  and status_active=1 and entry_form_id=203 and sample_mst_id='$up_id'");
								$gmts="";
								foreach ($sql_gmts_re as $rowss)
								{
									$gmts.=$rowss[csf("gmts_item_id")].",";
								}
								echo create_drop_down( "cboReGarmentItem_$i", 140, $garments_item,"", 1, "Select Item",$row[csf("gmts_item_id_re")], "","",$order_gmts_str);
								?>
		
								<input type="hidden" id="updateidRequiredEmbellishdtl_<? echo $i;?>" name="updateidRequiredEmbellishdtl_<? echo $i;?>"   style="width:20px;" value="<? echo $row[csf("id")]; ?>" class="text_boxes"/>
								<input type="hidden" id="txtDeltedIdRe" name="txtDeltedIdRe"   style="width:20px;" value="" class="text_boxes"/>
							</td>
							<td align="center" id="re_name_1">
								<?
									   // $sql="select id,item_name from  lib_garment_item where status_active=1 and is_deleted=0";
								echo create_drop_down( "cboReName_$i", 140, $emblishment_name_array,"", 1, "Select Name", $row[csf("name_re")], "cbotype_loder($i);");
		
								?>
							</td>
							<td align="center" id="reType_<? echo $i ?>">
								<?
								$type_array=array(1=>$emblishment_print_type,2=>$emblishment_embroy_type,3=>$emblishment_wash_type,4=>$emblishment_spwork_type,5=>$emblishment_gmts_type);
								echo create_drop_down( "cboReType_$i", 140, $type_array[$row[csf("name_re")]],"", 1, "Select Type",$row[csf("type_re")] , "");
		
								?>
							</td>
		
							<td align="center" id="re_body_part_1">
								<?
		
		
								echo create_drop_down( "cboReBodyPart_$i", 95, $body_part,"", 1, "Select Body Part",$row[csf("body_part_id")], "");
		
								?>
							</td>
							<td>
							<?
							echo create_drop_down( "cboReSupplierName_$i", 100, "select a.id,a.supplier_name from lib_supplier a,lib_supplier_party_type b  where a.status_active =1 and a.is_deleted=0  and b.party_type in (select party_type from lib_supplier_party_type where a.id=b.supplier_id and b.party_type=23) order by a.supplier_name","id,supplier_name", 1, "-- Select Supplier --", $row[csf("supplier_id")],
								"",0 );
								?>
							</td>
							
							<td align="center" id="re_qty_1">
							<input style="width:50px;" type="text" class="text_boxes"  name="txtReQty_<? echo $i;?>" id="txtReQty_<? echo $i;?>" placeholder="Click"  readonly="" onClick="open_consumption_popup('requires/sample_requisition_with_booking_controller.php?action=consumption_popup', 'Consumtion Entry Form',<? echo $i;?>)" value="<? echo $row[csf("fin_fab_qnty")]; ?>"/>
							  <input style="width:40px;" type="hidden" class="text_boxes"  name="txtcolorBreakdown_<? echo $i;?>" id="txtcolorBreakdown_<? echo $i;?>" value="<? echo $row[csf("color_size_breakdown")]; ?>"   />
		
							</td>
							<td align="center" id="re_rate_1">
								<input style="width:40px;" type="text" class="text_boxes"  name="txtReRate_<? echo $i;?>" id="txtReRate_<? echo $i;?>" value="<? echo $row[csf("rate")]; ?>" placeholder="Rate"  readonly="" />
		
							</td>
							  <td align="center" id="re_amount_1">
								<input style="width:50px;" type="text" class="text_boxes"  name="txtReAmount_<? echo $i;?>" id="txtReAmount_<? echo $i;?>" value="<? echo $row[csf("amount")]; ?>" placeholder="Amount"  readonly="" />
							</td>
		
							<td align="center" id="re_remarks_1">
								<input style="width:90px;" type="text" class="text_boxes"  name="txtReRemarks_<? echo $i;?>" id="txtReRemarks_<? echo $i;?>" placeholder="Click"  readonly="" onClick="openmypage_remarks('requires/sample_requisition_with_booking_controller.php?action=all_remarks_popup','Remarks','4',<? echo $i; ?>);" value="<? echo $row[csf("remarks_re")]; ?>"/>
							</td>
							<td align="center" id="deliveryDateid_<? echo $i;?>">
							<input style="width:70px;" type="text" class="datepicker" placeholder="Select Date" autocomplete="off" name="deliveryDate_<? echo $i;?>" value="<? echo change_date_format($row[csf("delivery_date")]); ?>" id="deliveryDate_<? echo $i;?>" />
							</td>
							<td id="re_image_1"><input type="button" class="image_uploader" name="reTxtFile_<? echo $i;?>" id="reTxtFile_<? echo $i;?>" size="20" style="width:120px;" value="ADD/VIEW IMAGE" onClick="file_uploader ( '../../', document.getElementById('updateidRequiredEmbellishdtl_<? echo $i;?>').value,'', 'required_embellishment_1', 0 ,1);"></td>
							<td width="70">
								<input type="button" id="increasere_<? echo $i; ?>" name="increasere_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_re_tr(<? echo $i; ?>)" />
								<input type="button" id="decreasere_<? echo $i; ?>" name="decreasere_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_re_deleteRow(<? echo $i; ?>);" />
							</td>
						</tr>
						<?
						$i++;
					}
				}
			}
		}
	}
	exit();
}

if ($action == "consumption_popup")
{
	echo load_html_head_contents("Consumption Entry","../../../", 1, 1, $unicode,'','');
	$color_library=return_library_array("select id, color_name from lib_color", "id", "color_name");
	$size_library=return_library_array("select id, size_name from lib_size", "id", "size_name");
	?>
	<script>
		var str_gmtssizes = [<? echo substr(return_library_autocomplete( "select size_name from  lib_size", "size_name"  ), 0, -1); ?>];
		var str_diawidth = [<? echo substr(return_library_autocomplete( "select color_name from lib_color", "color_name"  ), 0, -1); ?>];
		

		function poportionate_qty(qty)
		{
			var txtwoq=document.getElementById('txtwoq').value;
			var txtwoq_qty=document.getElementById('txtwoq_qty').value*1;
			var rowCount = $('#tbl_consmption_cost tbody tr').length;
			for(var i=1; i<=rowCount; i++){
				var poreqqty=$('#poreqqty_'+i).val();
				var txtwoq_cal =number_format_common((txtwoq_qty/txtwoq) * (poreqqty),6,0);
				//alert(txtwoq_cal); 
				//hiddenreqqty_
				$('#reqqty_'+i).val(txtwoq_cal);
				//calculate_requirement(i)
			}
			set_sum_value( 'qty_sum', 'reqqty_')
			var j=i-1;
			var qty_sum=document.getElementById('qty_sum').value*1;
			if(qty_sum >txtwoq_qty ){
				$('#reqqty_'+j).val(number_format_common(txtwoq_cal*1-(qty_sum-txtwoq_qty),6,0))
			}
			else if(qty_sum < txtwoq_qty ){
				$('#reqqty_'+j).val(number_format_common((txtwoq_cal*1) +(txtwoq_qty - qty_sum),6,0))
			}
			else{
				$('#reqqty_'+j).val(number_format_common(txtwoq_cal,6,0));
			}
			set_sum_value( 'qty_sum', 'reqqty_');
			calculate_requirement(j)
		}

		function calculate_requirement(i){
			var cons=(document.getElementById('reqqty_'+i).value)*1;
			//var WastageQty='';
			WastageQty=cons;
			WastageQty= number_format_common( WastageQty, 6, 0) ;
			document.getElementById('reqqty_'+i).value= WastageQty;
			calculate_amount(i);
		}

		function set_sum_value(des_fil_id,field_id)
		{
			if(des_fil_id=='qty_sum') var ddd={dec_type:6,comma:0,currency:0};
			if(des_fil_id=='amount_sum') var ddd={dec_type:6,comma:0,currency:0};
			var rowCount = $('#tbl_consmption_cost tbody tr').length;
			math_operation( des_fil_id, field_id, '+', rowCount,ddd );
		}

		function copy_value(value,field_id,i)
		{
				//alert(value);
			var gmtssizesid=document.getElementById('gmtssizesid_'+i).value;
			var pocolorid=document.getElementById('gmtsColorID_'+i).value;
			
			var rowCount = $('#tbl_consmption_cost tbody tr').length;
			var copy_basis=$('input[name="copy_basis"]:checked').val();
		

			for(var j=i; j<=rowCount; j++)
			{
				
				if(field_id=='reqqty_'){
					if(copy_basis==0){
						document.getElementById(field_id+j).value=value;
						calculate_requirement(j)
						set_sum_value( 'qty_sum', 'reqqty_'  );
					}
					if(copy_basis==1){
						if( gmtssizesid==document.getElementById('gmtssizesid_'+j).value){
							document.getElementById(field_id+j).value=value;
							calculate_requirement(j)
							set_sum_value( 'qty_sum', 'reqqty_'  );
						}
					}
					if(copy_basis==2){
						if( pocolorid==document.getElementById('gmtsColorID_'+j).value){
							document.getElementById(field_id+j).value=value;
							calculate_requirement(j)
							set_sum_value( 'qty_sum', 'reqqty_'  );
						}
					}
				}
			
				if(field_id=='rate_'){
					if(copy_basis==0){
						document.getElementById(field_id+j).value=value;
						calculate_amount(j)
					}
					if(copy_basis==1){
						if( gmtssizesid==document.getElementById('gmtssizesid_'+j).value){
							document.getElementById(field_id+j).value=value;
							calculate_amount(j)
						}
					}
					if(copy_basis==2){
						if( pocolorid==document.getElementById('gmtsColorID_'+j).value){
							document.getElementById(field_id+j).value=value;
							calculate_amount(j)
						}
					}
				}
			}
		}

		function calculate_amount(i){
			var rate=(document.getElementById('rate_'+i).value)*1;
			var woqny=(document.getElementById('reqqty_'+i).value)*1;
			var amount=number_format_common((rate*woqny),5,0);
			document.getElementById('amount_'+i).value=amount;
			set_sum_value( 'amount_sum', 'amount_' );
			calculate_avg_rate()
		}

		function calculate_avg_rate(){
			var woqty_sum=document.getElementById('qty_sum').value;
			var amount_sum=document.getElementById('amount_sum').value;
			var avg_rate=number_format_common((amount_sum/woqty_sum),5,0);
			document.getElementById('rate_sum').value=avg_rate;
		}

		function js_set_value(){
			//var reg=/[^a-zA-Z0-9!@#$%^,;.:<>{}?\+|\[\]\- \/]/g;
			var row_num=$('#tbl_consmption_cost tbody tr').length;
			var cons_break_down="";
			for(var i=1; i<=row_num; i++){
				
				//alert(txtdescription.match(reg))
				
				var gmtItemID=$('#cboGarmentItem_'+i).val();
				if(gmtItemID=='') gmtItemID=0;
				
				var gmtcolorid=$('#gmtsColorID_'+i).val();
				if(gmtcolorid=='') gmtcolorid=0;

				var gmtssizesid=$('#gmtssizesid_'+i).val();
				if(gmtssizesid=='') gmtssizesid=0;
				var reqqty=$('#reqqty_'+i).val();
				if(reqqty=='') reqqty=0;

				var rate=$('#rate_'+i).val();
				if(rate=='') rate=0;

				var amount=$('#amount_'+i).val();
				if(amount=='') amount=0;

			
				var dtlsid=$('#dtlsid_'+i).val();
				if(dtlsid=='') dtlsid=0;
				var sizedtlsid=$('#sizedtlsid_'+i).val()
				if(sizedtlsid=='') sizedtlsid=0;

				var updateid=$('#updateid_'+i).val();
				if(updateid=='') updateid=0;
				var mstupdateid=$('#mstupdateid_'+i).val();
				if(mstupdateid=='') mstupdateid=0;
			
				if(cons_break_down==""){
					cons_break_down+=gmtItemID+'_'+gmtcolorid+'_'+gmtssizesid+'_'+reqqty+'_'+rate+'_'+amount+'_'+dtlsid+'_'+sizedtlsid+'_'+updateid+'_'+mstupdateid;
				}
				else{
					cons_break_down+="__"+gmtItemID+'_'+gmtcolorid+'_'+gmtssizesid+'_'+reqqty+'_'+rate+'_'+amount+'_'+dtlsid+'_'+sizedtlsid+'_'+updateid+'_'+mstupdateid;
				}
			}
			//alert(cons_break_down);
			document.getElementById('cons_break_down').value=cons_break_down;
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
		<?
        extract($_REQUEST);
     
        ?>
        <div align="center" style="width:610px;" >
            <fieldset>
                <form id="consumptionform_1" autocomplete="off">
                    <table width="610" cellspacing="0" class="rpt_table" align="center" border="0" id="tbl_consmption_cost" rules="all">
                        <thead>
                        	<tr>
                                <th colspan="10" id="td_sync_msg" style="color:#FF0000"></th>
                            </tr>
                            <tr>
                                <th colspan="10">
                                    <input type="hidden" id="cons_break_down" name="cons_break_down" value="" />
                                    <input type="hidden" id="txtwoq" value="<? //echo $txtReQty;?>"/>
                                    Cons Qty:<input type="hidden" id="txtwoq_qty" class="text_boxes_numeric" onBlur="poportionate_qty(this.value)" value="<? echo $txtReQty; ?>"/>
                                    <input type="radio" name="copy_basis" value="0" <? if(!$updateidRequiredEmbellishdtl) { echo "checked";} ?>>Copy to All
                                    <input type="radio" name="copy_basis" value="1">Gmts Size Wise
                                    <input type="radio" name="copy_basis" value="2">Gmts Color Wise
                                    <input type="radio" name="copy_basis" value="10" <? if($updateidRequiredEmbellishdtl) { echo "checked";} ?>>No Copy
                                </th>
                            </tr>
                            <tr>
                                <th width="30">SL</th>
                                <th  width="150">Gmts. Item</th>
                                <th  width="150">Gmts. Color</th>
                                <th  width="70">Gmts. sizes</th>
                                <th width="70">Qty</th>
                                <th width="70">Rate</th>
                                <th width="">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?
                       $booking_data=sql_select("select id,mst_id,dtls_id,sample_size_dtls_id,item_id,color_id,size_id,qnty,rate,amount  from sample_develop_embl_color_size where dtls_id in($updateidRequiredEmbellishdtl) and status_active=1 and is_deleted=0");
					// echo "select id,mst_id,dtls_id,sample_size_dtls_id,item_id,color_id,size_id,qnty,rate,amount  from sample_develop_embl_color_size where dtls_id in($updateidRequiredEmbellishdtl) and status_active=1 and is_deleted=0";
                        foreach($booking_data as $row){
							$req_data_arr[$row[csf('sample_size_dtls_id')]]['id']=$row[csf('id')];
							$req_data_arr[$row[csf('sample_size_dtls_id')]]['qnty']=$row[csf('qnty')];
							$req_data_arr[$row[csf('sample_size_dtls_id')]]['rate']=$row[csf('rate')];
							$req_data_arr[$row[csf('sample_size_dtls_id')]]['amount']=$row[csf('amount')];
                        }
						//echo $updateidRequiredEmbellishdtl.'DD';
						  $sql="select b.id as sam_dtls_id,b.gmts_item_id,b.sample_color,c.size_id,c.id as size_dtls_id, c.total_qty  from sample_development_mst a, sample_development_dtls b,sample_development_size c  where a.id=b.sample_mst_id and a.id=c.mst_id and b.id=c.dtls_id and a.entry_form_id=203 and a.company_id=$cbo_company_name  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.status_active=1 and  a.id in($update_id) and b.sample_name in($cboReSampleName) and b.gmts_item_id in($cboReGarmentItem)   order by b.id"; 
						 
                        $data_array=sql_select($sql);
                        if ( count($data_array)>0){
							$i=0;
							foreach( $data_array as $row ){
									if($req_data_arr[$row[csf('size_dtls_id')]]['qnty'] !=''){
										$req_qty = $req_data_arr[$row[csf('size_dtls_id')]]['qnty'];
									}
									else{
										$req_qty = $row[csf('total_qty')];
									}
									$i++;
								?>
									<tr id="break_1" align="center">
                                        <td><? echo $i;?></td>
                                        <td>
                                        <?
                                        	echo create_drop_down( "cboGarmentItem_".$i, 150, $garments_item,"", 1, "Select Item", $row[csf('gmts_item_id')], "",1);
										?>
                                        </td>
                                        <td>
                                            <input type="text" id="gmtsColor_<? echo $i;?>"  name="gmtsColor_<? echo $i;?>" class="text_boxes" style="width:150px" value="<? echo $color_library[$row[csf('sample_color')]]; ?>"  disabled readonly/>
                                            <input type="hidden" id="gmtsColorID_<? echo $i;?>"  name="gmtsColorID_<? echo $i;?>" class="text_boxes" style="width:70px" value="<? echo $row[csf('sample_color')]; ?>"  disabled readonly/>
                                         
                                        </td>
                                        <td>
                                            <input type="text" id="gmtssizes_<? echo $i;?>"  name="gmtssizes_<? echo $i;?>" class="text_boxes" style="width:70px" value="<? echo $size_library[$row[csf('size_id')]]; ?>" disabled readonly/>
                                            <input type="hidden" id="gmtssizesid_<? echo $i;?>"  name="gmtssizesid_<? echo $i;?>" class="text_boxes" style="width:40px" value="<? echo $row[csf('size_id')]; ?>" readonly />
                                        </td>
                                       
                                        <td><input type="hidden" id="hiddenreqqty_<? echo $i;?>"  name="reqqty_<? echo $i;?>" class="text_boxes_numeric" style="width:70px"    value="<? //echo $txtwoq_cal ?>" readonly/>
                                        	<input type="text" id="reqqty_<? echo $i;?>"  onChange="set_sum_value( 'qty_sum', 'reqqty_' );calculate_requirement(<? echo $i;?>);copy_value(this.value,'reqqty_',<? echo $i;?>)"  name="reqqty_<? echo $i;?>" class="text_boxes_numeric" style="width:70px"   placeholder="<? //echo $txtwoq_cal; ?>" value="<? echo number_format($req_qty,0,'.','');?>"/>
                                        </td>
                                       
                                        <td>
                                        	<input type="text" id="rate_<? echo $i;?>"  name="rate_<? echo $i;?>" class="text_boxes_numeric" style="width:70px" onChange="calculate_amount(<? echo $i;?>);set_sum_value( 'amount_sum', 'amount_' );copy_value(this.value,'rate_',<? echo $i;?>) " value="<? echo $req_data_arr[$row[csf('size_dtls_id')]]['rate']; ?>"/>
                                        </td>
                                        <td>
                                        	<input type="text" id="amount_<? echo $i;?>"  name="amount_<? echo $i;?>"  onBlur="set_sum_value( 'amount_sum', 'amount_' ) " class="text_boxes_numeric" style="width:70px" value="<? echo $req_data_arr[$row[csf('size_dtls_id')]]['amount']; ?>" readonly>
                                             <input type="hidden" id="dtlsid_<? echo $i;?>"  name="dtlsid_<? echo $i;?>" class="text_boxes" style="width:50px" value="<? echo $row[csf('sam_dtls_id')]; ?>" readonly />
                                             <input type="hidden" id="sizedtlsid_<? echo $i;?>"  name="sizedtlsid_<? echo $i;?>" class="text_boxes" style="width:50px" value="<? echo $row[csf('size_dtls_id')]; ?>" readonly />
                                             <input type="hidden" id="updateid_<? echo $i;?>"  name="updateid_<? echo $i;?>" class="text_boxes" style="width:50px" value="<? echo $update_id; ?>" readonly />
                                              <input type="hidden" id="mstupdateid_<? echo $i;?>"  name="mstupdateid_<? echo $i;?>" class="text_boxes" style="width:50px" value="<? echo $req_data_arr[$row[csf('size_dtls_id')]]['id']; ?>" readonly />
                                        </td>
                                       
									</tr>
								<?
								//}
							}
                        }
                        ?>
                        </tbody>
                        <tfoot>
                            <tr>
                               <th width="30">&nbsp;</th>
                               <th width="150">&nbsp;</th>
                                <th width="150">&nbsp;</th>
                                <th width="70">&nbsp;</th>
                                <th width="70"><input type="text" id="qty_sum" name="qty_sum" class="text_boxes_numeric" style="width:70px"  readonly></th>
                                <th width="70"><input type="text" id="rate_sum"  name="rate_sum" class="text_boxes_numeric" style="width:70px" readonly></th>
                                <th width=""><input type="text" id="amount_sum" name="amount_sum" class="text_boxes_numeric" style="width:70px" readonly></th>
                               
                            </tr>
                        </tfoot>
                    </table>
                    <table width="610" cellspacing="0" class="" border="0" rules="all">
                        <tr>
                            <td align="center" width="100%"> <input type="button" class="formbutton" value="Close" onClick="js_set_value()"/> </td>
                        </tr>
                    </table>
                </form>
            </fieldset>
        </div>
	</body>
	<script>
		$("input[type=text]").focus(function() {
		   $(this).select();
		});
		
		set_sum_value( 'qty_sum', 'reqqty_' );
		//set_sum_value( 'woqty_sum', 'woqny_' );
		set_sum_value( 'amount_sum', 'amount_' );
		//set_sum_value( 'pcs_sum', 'pcs_' );
		calculate_avg_rate();
		//var wo_qty=$('#txtwoq_qty').val()*1;

	//	var wo_qty_sum=$('#qty_sum').val()*1;

		//if(wo_qty!=wo_qty_sum)
		//{
			//$('#td_sync_msg').html("Booking Info not synchronized with order entry and pre-costing. order entry or pre-costing has updated after booking entry.");
		//}


	</script>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
    exit();
}
if ($action=="copy_requisition")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

  	if ($operation==5)  // Insert Here
  	{
  		$con = connect();
  		if($db_type==0)
  		{
  			mysql_query("BEGIN");
  		}
  		$id_mst=return_next_id( "id", "sample_development_mst", 1 ) ;
  		if($db_type==0)
  		{
  			$new_system_id=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', '', date("Y",time()), 5, "select requisition_number_prefix,requisition_number_prefix_num from sample_development_mst where  entry_form_id=203 and company_id=$cbo_company_name and YEAR(insert_date)=".date('Y',time())." order by id desc ", "requisition_number_prefix", "requisition_number_prefix_num" ));
  		}
  		if($db_type==2)
  		{
  			$new_system_id=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', '', date("Y",time()), 5, "select requisition_number_prefix,requisition_number_prefix_num from sample_development_mst where entry_form_id=203 and company_id=$cbo_company_name and to_char(insert_date,'YYYY')=".date('Y',time())." order by id desc ", "requisition_number_prefix", "requisition_number_prefix_num" ));
  		}

  		$field_array="id, requisition_number_prefix, requisition_number_prefix_num, requisition_number, sample_stage_id, requisition_date, quotation_id, style_ref_no, company_id, location_id, buyer_name, season, product_dept, dealing_marchant, agent_name, buyer_ref, bh_merchant, estimated_shipdate, remarks, item_category, inserted_by, insert_date, status_active, is_deleted, entry_form_id, is_copy, req_ready_to_approved, copy_from, material_delivery_date";
  		$data_array="(".$id_mst.",'".$new_system_id[1]."',".$new_system_id[2].",'".$new_system_id[0]."',".$cbo_sample_stage.",".$txt_requisition_date.",".$txt_quotation_id.",".$txt_style_name.",".$cbo_company_name.",".$cbo_location_name.",".$cbo_buyer_name.",".$cbo_season_name.",".$cbo_product_department.",".$cbo_dealing_merchant.",".$cbo_agent.",".$txt_buyer_ref.",".$txt_bhmerchant.",".$txt_est_ship_date.",".$txt_remarks.",".$txt_item_catgory.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0,203,1,'2',".$txt_requisition_id.",".$txt_material_dlvry_date.")";//team_leader
  		$rID=sql_insert("sample_development_mst",$field_array,$data_array,1);

		$id_size=return_next_id( "id","sample_development_size", 1 ) ;
		$id_fabric=return_next_id( "id", "sample_development_fabric_acc", 1 ) ;
		$mst_id=return_field_value("max(id) as id","sample_development_mst","status_active=1 and is_deleted=0","id");
  		$id_dtls=return_next_id( "id", "sample_development_dtls", 1 ) ;


		// fabric details entry
		$field_array_fabric= "id,sample_mst_id,sample_name,gmts_item_id,process_loss_percent,grey_fab_qnty,delivery_date,fabric_source,remarks_ra,fin_fab_qnty,determination_id,body_part_id,fabric_nature_id,fabric_description,gsm,dia,color_data,color_type_id,width_dia_id,uom_id,required_dzn,required_qty,inserted_by,insert_date,status_active,is_deleted,form_type";

		$query_fabric=sql_select("SELECT id,sample_mst_id,sample_name,gmts_item_id,process_loss_percent,grey_fab_qnty,delivery_date,fabric_source,remarks_ra,fin_fab_qnty,determination_id,body_part_id,fabric_nature_id,fabric_description,gsm,dia,color_data,color_type_id,width_dia_id,uom_id,required_dzn,required_qty,form_type from sample_development_fabric_acc where form_type=1 and status_active=1 and is_deleted=0 and sample_mst_id=$update_id");


		$field_array_col="id, mst_id, dtls_id,color_id,contrast,fabric_color,qnty,process_loss_percent,grey_fab_qnty,inserted_by, insert_date, status_active, is_deleted";
	  	$idColorTbl=return_next_id( "id","sample_development_rf_color", 1 ) ;
		for($i=0;$i<count($query_fabric);$i++)
		{

			if ($i!=0) $data_array_fabric .=",";

			$data_array_fabric .="(".$id_fabric.",".$mst_id.",".$query_fabric[$i][csf("sample_name")].",".$query_fabric[$i][csf("gmts_item_id")].",'".$query_fabric[$i][csf("process_loss_percent")]."','".$query_fabric[$i][csf("grey_fab_qnty")]."','".$query_fabric[$i][csf("delivery_date")]."','".$query_fabric[$i][csf("fabric_source")]."','".$query_fabric[$i][csf("remarks_ra")]."','".$query_fabric[$i][csf("fin_fab_qnty")]."','".$query_fabric[$i][csf("determination_id")]."',".$query_fabric[$i][csf("body_part_id")].",".$query_fabric[$i][csf("fabric_nature_id")].",'".$query_fabric[$i][csf("fabric_description")]."','".$query_fabric[$i][csf("gsm")]."','".$query_fabric[$i][csf("dia")]."','".$query_fabric[$i][csf("color_data")]."',".$query_fabric[$i][csf("color_type_id")].",".$query_fabric[$i][csf("width_dia_id")].",".$query_fabric[$i][csf("uom_id")].",'".$query_fabric[$i][csf("required_dzn")]."','".$query_fabric[$i][csf("required_qty")]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0,1)";
			$fabric_acc_match_arr[$query_fabric[$i][csf("id")]]=$id_fabric;
			$ex_data=explode("-----",$query_fabric[$i][csf("color_data")]);
			foreach($ex_data as $color_data)
			{
				$ex_size_data=explode("_",$color_data);
				$colorName=$ex_size_data[1];
				$colorId=$ex_size_data[2];
				$contrast=$ex_size_data[3];
				$qnty=$ex_size_data[4];
				$txtProcessLoss=$ex_size_data[5];
				$txtGrayFabric=$ex_size_data[6];
				$fab_color_id=$ex_size_data[7];
				if($data_array_col !="")  $data_array_col.=",";
				   if ($i!=1) $add_comma .=",";
				$data_array_col.="(".$idColorTbl.",".$mst_id.",".$id_fabric.",".$colorId.",'".$contrast."','".$fab_color_id."','".$qnty."','".$txtProcessLoss."','".$txtGrayFabric."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				$idColorTbl = $idColorTbl + 1;
			}
			$id_fabric=$id_fabric+1;

		}  		
  		$field_array_dtls= "id,sample_mst_id,sample_name,gmts_item_id,smv,article_no,sample_color,sample_prod_qty,submission_qty,delv_start_date,delv_end_date,sent_to_buyer_date,comments,sample_charge,sample_curency,inserted_by,insert_date,status_active,is_deleted,entry_form_id,size_data,fabric_status,acc_status,embellishment_status,fab_status_id,acc_status_id,embellishment_status_id";
  		$query_dtls=sql_select("SELECT id,sample_mst_id,sample_name,gmts_item_id,smv,article_no,sample_color,sample_prod_qty,submission_qty,delv_start_date,delv_end_date,sent_to_buyer_date,comments,sample_charge,sample_curency,status_active,is_deleted,entry_form_id,size_data,fabric_status,acc_status,embellishment_status,fab_status_id,acc_status_id,embellishment_status_id from sample_development_dtls where entry_form_id=203 and status_active=1 and is_deleted=0 and sample_mst_id=$update_id");
  		
  		$field_array_size="id, mst_id, dtls_id,size_id,bh_qty,plan_qty,dyeing_qty,test_qty,self_qty,total_qty,inserted_by, insert_date, status_active, is_deleted";
  		$data_array_dtls="";
  		$data_array_size="";
  		for ($i=0;$i<count($query_dtls);$i++)
  		{
  			if ($data_array_dtls) $data_array_dtls .=",";
			$fabric_status_id=$fabric_acc_match_arr[$query_dtls[$i][csf("fab_status_id")]];
  			$data_array_dtls .="(".$id_dtls.",".$mst_id.",".$query_dtls[$i][csf("sample_name")].",".$query_dtls[$i][csf("gmts_item_id")].",'".$query_dtls[$i][csf("smv")]."','".$query_dtls[$i][csf("article_no")]."','".$query_dtls[$i][csf("sample_color")]."','".$query_dtls[$i][csf("sample_prod_qty")]."','".$query_dtls[$i][csf("submission_qty")]."','".$query_dtls[$i][csf("delv_start_date")]."','".$query_dtls[$i][csf("delv_end_date")]."','".$query_dtls[$i][csf("sent_to_buyer_date")]."','".$query_dtls[$i][csf("comments")]."','".$query_dtls[$i][csf("sample_charge")]."','".$query_dtls[$i][csf("sample_curency")]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0,203,'".$query_dtls[$i][csf("size_data")]."','".$query_dtls[$i][csf("fabric_status")]."','".$query_dtls[$i][csf("acc_status")]."','".$query_dtls[$i][csf("embellishment_status")]."','".$fabric_status_id."','".$query_dtls[$i][csf("acc_status_id")]."','".$query_dtls[$i][csf("embellishment_status_id")]."')";

  			$ex_data=explode("__",$query_dtls[$i][csf("size_data")]);
  			$countsize=count($ex_data);

  			foreach($ex_data as $size_data)
  			{
  				$size_name=""; $bhqty=0; $dyqty=0; $testqty=0; $selfqty=0; $totalqty=0;
  				$ex_size_data=explode("_",$size_data);
  				$size_name=$ex_size_data[0];
  				$bhqty=$ex_size_data[1];
  				$plqty=$ex_size_data[2];
  				$dyqty=$ex_size_data[3];
  				$testqty=$ex_size_data[4];
  				$selfqty=$ex_size_data[5];
  				$totalqty=$ex_size_data[6];

  				if($size_name!="")
  				{
  					if (!in_array($size_name,$new_array_size))
  					{
  						$size_id = return_id( $size_name, $size_arr, "lib_size", "id,size_name","140");
  						$new_array_size[$size_id]=str_replace("'","",$size_name);

  					}
  					else $size_id =  array_search($size_name, $new_array_size);
  				}
  				else $size_id=0;
  				

  				if($data_array_size) $data_array_size .=',';
  				$data_array_size.="(".$id_size.",".$mst_id.",".$id_dtls.",'".$size_id."','".$bhqty."','".$plqty."','".$dyqty."','".$testqty."','".$selfqty."','".$totalqty."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
  				$id_size=$id_size+1;
  			}
  			$id_dtls=$id_dtls+1;
  		}

  		$rid_dtls=sql_insert("sample_development_dtls",$field_array_dtls,$data_array_dtls,1);
  		$rid_size=sql_insert("sample_development_size",$field_array_size,$data_array_size,1);	    

  		$rid_fabric=sql_insert("sample_development_fabric_acc",$field_array_fabric,$data_array_fabric,1);
  		$rid_color_rf=sql_insert("sample_development_rf_color",$field_array_col,$data_array_col,1);

		//accessories entry
  		$id_acc=return_next_id( "id", "sample_development_fabric_acc", 1 ) ;


  		$field_array_acc= "id,sample_mst_id,sample_name_ra,gmts_item_id_ra,supplier_id,delivery_date,fabric_source,trims_group_ra,description_ra,brand_ref_ra,uom_id_ra,req_dzn_ra,req_qty_ra,remarks_ra,inserted_by,insert_date,status_active,is_deleted,form_type";
  		$query_acc=sql_select("select id,sample_mst_id,sample_name_ra,gmts_item_id_ra,supplier_id,delivery_date,fabric_source,trims_group_ra,description_ra,brand_ref_ra,uom_id_ra,req_dzn_ra,req_qty_ra,remarks_ra,form_type  from sample_development_fabric_acc where form_type=2 and status_active=1 and is_deleted=0 and sample_mst_id=$update_id");
  		for($i=0;$i<count($query_acc);$i++)
  		{
  			if ($i!=0) $data_array_acc .=",";
  			$data_array_acc .="(".$id_acc.",".$mst_id.",".$query_acc[$i][csf("sample_name_ra")].",".$query_acc[$i][csf("gmts_item_id_ra")].",'".$query_acc[$i][csf("supplier_id")]."','".$query_acc[$i][csf("delivery_date")]."','".$query_acc[$i][csf("fabric_source")]."','".$query_acc[$i][csf("trims_group_ra")]."','".$query_acc[$i][csf("description_ra")]."','".$query_acc[$i][csf("brand_ref_ra")]."',".$query_acc[$i][csf("uom_id_ra")].",'".$query_acc[$i][csf("req_dzn_ra")]."','".$query_acc[$i][csf("req_qty_ra")]."','".$query_acc[$i][csf("remarks_ra")]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0,2)";

  			$id_acc=$id_acc+1;

  		}
  		$acc_id=sql_insert("sample_development_fabric_acc",$field_array_acc,$data_array_acc,1);


	  //print_r($query_emb);
  		//$a=count($query_emb);

		// embellishment entry
  		$id_emb=return_next_id( "id", "sample_development_fabric_acc", 1 ) ;

  		$query_emb=sql_select("SELECT id,sample_mst_id,sample_name_re,gmts_item_id_re,body_part_id,supplier_id,delivery_date,name_re,type_re,remarks_re,inserted_by,insert_date,status_active,is_deleted from sample_development_fabric_acc where form_type=3 and status_active=1 and is_deleted=0 and sample_mst_id=$update_id");
	  //print_r($query_emb);
  		$a=count($query_emb);
  		$field_array_emb= "id,sample_mst_id,sample_name_re,gmts_item_id_re,body_part_id,supplier_id,delivery_date,name_re,type_re,remarks_re,inserted_by,insert_date,status_active,is_deleted,form_type";
  		for ($i=0;$i<$a;$i++)
  		{

  			if ($i!=0) $data_array_emb .=",";
  			$data_array_emb .="(".$id_emb.",".$mst_id.",'".$query_emb[$i][csf("sample_name_re")]."','".$query_emb[$i][csf("gmts_item_id_re")]."','".$query_emb[$i][csf("body_part_id")]."','".$query_emb[$i][csf("supplier_id")]."','".$query_emb[$i][csf("delivery_date")]."','".$query_emb[$i][csf("name_re")]."','".$query_emb[$i][csf("type_re")]."','".$query_emb[$i][csf("remarks_re")]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0,3)";

  			$id_emb=$id_emb+1;

  		}

  		$emb_id=sql_insert("sample_development_fabric_acc",$field_array_emb,$data_array_emb,1);
  		//echo "10**$rID && $rid_dtls && $rid_size ";
  		if($db_type==0)
  		{
			//&&  $emb_id && $acc_id &&  $rid_fabric && $rid_color_rf
  			if($rID && $rid_dtls && $rid_size )
  			{
  				mysql_query("COMMIT");
  				echo "0**".$new_system_id[0]."**".$id_mst;
  			}
  			else
  			{
  				mysql_query("ROLLBACK");
  				echo "10**".$id_mst;
  			}
  		}
  		//echo "10**$rID  $rid_dtls $rid_size";
  		else if($db_type==2 || $db_type==1 )
  		{
			//&&  $emb_id && $acc_id && $rid_fabric && $rid_color_rf
  			if($rID && $rid_dtls && $rid_size  )
  			{
  				oci_commit($con);
  				echo "0**".$new_system_id[0]."**".$id_mst;
  			}
  			else
  			{
  				oci_rollback($con);
  				echo "10**".$id_mst;
  			}
  		}
  		disconnect($con);
  		die;
  	}
}

if ($action == 'btn_load_acknowledge') {
	$sql = "";
	$data_array = sql_select($sql);
	echo count($data_array);
	exit();
}

if ($action == 'show_acknowledge')
{
	$sql = "select requisition_number_prefix_num, requisition_number, refusing_cause from sample_development_mst where status_active=1 and is_deleted=0 and refusing_cause is not null order by id desc";
	$data_array = sql_select($sql);

	?>
	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="290">
		<thead>
			<th width="20" align="center">SL</th>
			<th width="80">Req No</th>
			<th>Refusing Cause</th>
		</thead>
	</table><!--onClick='set_form_data("<? //echo $row[csf('id')] . "**" . $row[csf('company_id')] . "**" . $row[csf('sales_booking_no')]; ?>")' -->
	<div style="width:290px; max-height:130px; overflow-y:scroll" id="list_container_cause" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="272" class="rpt_table" id="tbl_list_search">
			<?
			$i = 1;
			foreach ($data_array as $row)
			{
				if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer">
					<td width="20" align="center"><? echo $i; ?></td>
					<td width="80" style="word-break:break-all"><? echo $row[csf('requisition_number')]; ?></td>
					<td style="word-break:break-all"><? echo $row[csf('refusing_cause')]; ?></td>
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
if ($action=="cbo_dealing_merchant_book")
{
	echo create_drop_down( "cbo_dealing_merchant_book", 150, "select id,team_member_name from lib_mkt_team_member_info where team_id='$data' and status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select Team Member --", $selected, "" );
	exit();
}

if($action=="save_update_delete_booking")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$txt_booking_remarks=str_replace("'","",$txt_booking_remarks);
	$str_rep=array("+", "&", "*", "(", ")", "=","'","\r", "\n",'"','#');
	$txt_booking_remark=str_replace($str_rep,' ',$txt_booking_remarks);
	
	if($sample_stage==2 || $sample_stage==3){
		if ($operation==0)  // Insert Here  update here
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			$booking_no=str_replace("'", "", $txt_booking_no);
			$booking_idss= return_field_value("id","wo_non_ord_samp_booking_mst","booking_no='$booking_no' and status_active=1");
			$is_approved = return_field_value("is_approved","wo_non_ord_samp_booking_mst","id=$booking_idss and status_active=1 and is_approved in(1,3)");
			if($is_approved==1 || $is_approved==3)
			{
				echo "14**Approved. Update or Delete not allowed.";
				disconnect($con);
				die;
			}

			$flag=1;
			if($booking_no)
			{
				$field_array_up="fabric_source*currency_id*source*buyer_req_no*revised_no*style_desc*exchange_rate*pay_mode*supplier_id*attention*ready_to_approved*team_leader*dealing_marchant*remarks*rmg_process_breakdown*updated_by*update_date";
				$data_array_up ="".$cbo_fabric_source."*".$cbo_currency."*".$cbo_sources."*".$txt_buyer_req_no."*".$txt_revise_no."*'".$txt_style_desc."'*".$txt_exchange_rate."*".$cbo_pay_mode."*".$cbo_supplier_name."*".$txt_attention."*".$cbo_ready_to_approved_book."*".$cbo_team_leader_book."*".$cbo_dealing_merchant_book."*'".$txt_booking_remark."'*".$txt_processloss_breck_down."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				$rID=sql_update("wo_non_ord_samp_booking_mst",$field_array_up,$data_array_up,"booking_no","".$txt_booking_no."",0);
				
				if($rID) $flag=1;else $flag=0;
				$new_booking_no=$booking_no;
				
			}
			else
			{
				if($db_type==0)
				{
					$new_booking_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'SMN', date("Y",time()), 5, "select booking_no_prefix, booking_no_prefix_num from wo_non_ord_samp_booking_mst where company_id=$cbo_company_name and booking_type=4 and YEAR(insert_date)=".date('Y',time())." order by booking_no_prefix_num desc ", "booking_no_prefix", "booking_no_prefix_num" ));
				}
				if($db_type==2)
				{
					$new_booking_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'SMN', date("Y",time()), 5, "select booking_no_prefix, booking_no_prefix_num from wo_non_ord_samp_booking_mst where company_id=$cbo_company_name and booking_type=4 and to_char(insert_date,'YYYY')=".date('Y',time())." order by booking_no_prefix_num desc ", "booking_no_prefix", "booking_no_prefix_num" ));
				}
				$id=return_next_id( "id", "wo_non_ord_samp_booking_mst", 1 ) ;
				$field_array="id,booking_type,booking_no_prefix,booking_no_prefix_num,booking_no,company_id,buyer_id,item_category,fabric_source,currency_id,source,buyer_req_no,revised_no,exchange_rate,pay_mode,booking_date,supplier_id,attention,ready_to_approved,team_leader,dealing_marchant,inserted_by,insert_date,entry_form_id,style_desc,rmg_process_breakdown,remarks";
				$data_array ="(".$id.",4,'".$new_booking_no[1]."',".$new_booking_no[2].",'".$new_booking_no[0]."',".$cbo_company_name.",".$cbo_buyer_name.",'2',".$cbo_fabric_source.",".$cbo_currency.",".$cbo_sources.",".$txt_buyer_req_no.",".$txt_revise_no.",".$txt_exchange_rate.",".$cbo_pay_mode.",".$txt_booking_date.",".$cbo_supplier_name.",".$txt_attention.",".$cbo_ready_to_approved_book.",".$cbo_team_leader_book.",".$cbo_dealing_merchant_book.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','140','".$txt_style_desc."',".$txt_processloss_breck_down.",'".$txt_booking_remark."')";
				//echo "10** insert into wo_non_ord_samp_booking_mst ($field_array) values $data_array";die;
				$rID=sql_insert("wo_non_ord_samp_booking_mst",$field_array,$data_array,0);
				if($rID) $flag=1;else $flag=0;
				$new_booking_no=$new_booking_no[0];
				$update_prev=1;
				$lap=1;

				//========lapdip====================
				$id_lap=return_next_id( "id", "wo_po_lapdip_approval_info", 1 ) ;
				// $cbosampletype=return_field_value( 'id', 'lib_sample', 'sample_type=2 and status_active=1 and is_deleted=0' );
				$field_array_lap="id,booking_no,booking_id,color_name_id,status_active,is_deleted";
				$data_array_lapdip=sql_select("SELECT sample_color,sample_mst_id FROM sample_development_dtls  WHERE entry_form_id = 203
				AND sample_mst_id = $update_id AND is_deleted = 0	AND status_active = 1 GROUP BY  sample_color,sample_mst_id");
				foreach ( $data_array_lapdip as $row_lap1 )
				{
					$dup_lap=sql_select("select id from wo_po_lapdip_approval_info where booking_id=$id and color_name_id=".$row_lap1[csf('sample_color')]."  and status_active=1 and is_deleted=0");
					list($idlap)=$dup_lap;
					if( $idlap[csf('id')] =='')
					{
						if ($lap!=1) $data_array_lap .=",";
						$data_array_lap .="(".$id_lap.",'".$new_booking_no."',".$id.",".$row_lap1[csf('sample_color')].",1,0)";
						$id_lap=$id_lap+1;
						$lap=$lap+1;
					}
				}
				if($data_array_lap !='')
				{
					$rID4=sql_insert("wo_po_lapdip_approval_info",$field_array_lap,$data_array_lap,1);
				//	echo "insert into wo_po_lapdip_approval_info (".$field_array_lap.") values ".$data_array_lap;die;
					if($rID4==1 && $flag==1) $flag=1; else $flag=0;
				}
			
			}
			if($flag==1)
			{
				if($booking_no)
				{
					$select_prev=sql_select("SELECT booking_no from wo_non_ord_samp_booking_dtls where status_active=1 and is_deleted=0 and entry_form_id=140  and style_id=$update_id group by booking_no");
					//and booking_no='$new_booking_no'
					if(count($select_prev)>0)
					{
						$update_prev=execute_query("UPDATE wo_non_ord_samp_booking_dtls set status_active=0,is_deleted=1 where entry_form_id=140   and style_id=$update_id  ");//and booking_no='$new_booking_no'
						if($flag==1)
						{
							if($update_prev) $flag=1;else $flag=0;
						}
					}
				}
				$id_dtls=return_next_id( "id", "wo_non_ord_samp_booking_dtls", 1 ) ;
				$field_array_dtls= "id,booking_no,booking_mst_id,style_id,sample_type,gmts_item_id,body_part,fabric_source, fabric_description,gsm_weight,dia,color_all_data,color_type_id,dia_width,uom,req_dzn,finish_fabric,dtls_id,inserted_by,insert_date,status_active,is_deleted,entry_form_id,process_loss,grey_fabric,lib_yarn_count_deter_id,remarks,gmts_color,fabric_color,delivery_date";//wo_non_ord_samp_book_dtls_id

				$yarn_deter_id="";
				for ($i=1;$i<=$total_row;$i++)
				{

					$cboRfSampleName="cboRfSampleName_".$i;
					$cboRfGarmentItem="cboRfGarmentItem_".$i;
					$cboRfBodyPart="cboRfBodyPart_".$i;
					$cboRfFabricSource="cboRfFabricSource_".$i;
					$txtRfFabricDescription="txtRfFabricDescription_".$i;
					$txtRfGsm="txtRfGsm_".$i;
					$txtRfDia="txtRfDia_".$i;
					$txtRfColor="txtRfColor_".$i;
					$cboRfColorType="cboRfColorType_".$i;
					$cboRfWidthDia="cboRfWidthDia_".$i;
					$cboRfUom="cboRfUom_".$i;
					$txtRfReqDzn="txtRfReqDzn_".$i;
					$txtRfReqQty="txtRfReqQty_".$i;
					$txtRfColorAllData="txtRfColorAllData_".$i;
					$required_fab_id="updateidRequiredDtl_".$i;
					$txtProcessLoss="txtProcessLoss_".$i;
					$txtGrayFabric="txtGrayFabric_".$i;
					$libyarncountdeterminationid="libyarncountdeterminationid_".$i;
					$txtRfRemarks="txtRfRemarks_".$i;
					$fabricDelvDate="fabricDelvDate_".$i;
					$cboRfFabricNatureId="cboRfFabricNature_".$i;
					$yarn_deter_id.=$$libyarncountdeterminationid.',';
					
					$libDeterId=str_replace("'",'',$$libyarncountdeterminationid);
					$fab_nature_arr[$libDeterId]=str_replace("'",'',$$cboRfFabricNatureId);
					$fab_greyQty_arr[$libDeterId]=str_replace("'",'',$$txtGrayFabric);
					$fab_gsm_arr[$libDeterId]=str_replace("'",'',$$txtRfGsm);

					$RfRemarks=str_replace("'","",$txtRfRemarks);
					$Rf_Remarks=str_replace($str_rep,' ',$RfRemarks);
					

					if ($i!=1) $data_array_dtls .=",";
					$ex_data=explode("-----",str_replace("'","",$$txtRfColorAllData));
					foreach($ex_data as $color_data)
					{
						$ex_size_data=explode("_",$color_data);
						$contrast=$ex_size_data[3];
						if(str_replace("'","",$contrast)!="")
						{
							if (!in_array(str_replace("'","",$contrast),$new_array_color))
							{
								$fab_color_id = return_id( str_replace("'","",$contrast), $color_arr, "lib_color", "id,color_name","140");
								$new_array_color[$fab_color_id]=str_replace("'","",$contrast);
							}
							else $fab_color_id =  array_search(str_replace("'","",$contrast), $new_array_color);
						}
						else $fab_color_id=0;					
						$colorName=$ex_size_data[1];
						$colorId=$ex_size_data[2];
						$contrast=$ex_size_data[3];
						$qnty2=$ex_size_data[4];
						$txtProcessLoss2=$ex_size_data[5];
						$txtGrayFabric2=$ex_size_data[6];
						$fab_col_id=$fab_color_id;
						
						if($txtGrayFabric2>0)
						{
						$data_array_dtls .="(".$id_dtls.",'".$new_booking_no."','".$id."',".$update_id.",".$$cboRfSampleName.",".$$cboRfGarmentItem.",".$$cboRfBodyPart.",".$$cboRfFabricSource.",".$$txtRfFabricDescription.",".$$txtRfGsm.",".$$txtRfDia.",".$$txtRfColorAllData.",".$$cboRfColorType.",".$$cboRfWidthDia.",".$$cboRfUom.",".$$txtRfReqDzn.",'".$qnty2."',".$$required_fab_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0','140','".$txtProcessLoss2."','".$txtGrayFabric2."',".$$libyarncountdeterminationid.",'".$Rf_Remarks."','".$colorId."','".$fab_col_id."',".$$fabricDelvDate.")";

						$id_dtls=$id_dtls+1;
						}

					}
				}
				$rID_1=sql_insert("wo_non_ord_samp_booking_dtls",$field_array_dtls,$data_array_dtls,0);
				if($flag==1)
				{
					if($rID_1) $flag=1;else $flag=0;
				}
				$updateId=str_replace("'",'',$update_id);

				$rID_up_yarn=execute_query( "update sample_development_yarn_dtls set booking_no='".$new_booking_no."',update_date='".$pc_date_time."',updated_by=".$_SESSION['logic_erp']['user_id']." where mst_id in($updateId)",0);
				if($flag==1)
				{
					if($rID_up_yarn) $flag=1;else $flag=0;
				}
			}

			$rID3= execute_query( "update fabric_sales_order_mst set is_apply_last_update=2 where sales_booking_no =".$txt_booking_no." and within_group=1 and status_active=1 and is_deleted=0",0);
			if($flag==1)
			{
				if($rID3) $flag=1;else $flag=1;
			}
			
			//echo '10**='.$rID.'&&'.$rID_1 .'&&'. $rID_in2;die;
			if($db_type==0)
			{
				//if($rID && $rID_1 && $rID_in2 && $update_prev)
				if($flag==1)
				{
					mysql_query("COMMIT");
					echo "0**".$new_booking_no;
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "10**".$new_booking_no;
				}
			}
			else if($db_type==2 || $db_type==1 )
			{
				//if($rID && $rID_1 && $rID_in2 && $update_prev)
				if($flag==1)
				{
					oci_commit($con);
					echo "0**".$new_booking_no;
				}
				else
				{
					oci_rollback($con);
					echo "10**".$new_booking_no;
				}
			}
			disconnect($con);
			die;
		}
	}
	if($sample_stage==1){
		if ($operation==0)  // Insert Here  update here
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			$po_data_arr=explode(",",$po_data);
			foreach($po_data_arr as $value){
				$v_arr=explode("***",$value);
				$po_arr[$v_arr[0]]=$v_arr[1];
				$po_qty+=$v_arr[1];
			}
			$job_id=str_replace("'","",$txt_quotation_id);
			$job_no=return_field_value("job_no","wo_po_details_master","id=$job_id and status_active=1");
			$booking_no=str_replace("'", "", $txt_booking_no);
			$booking_idss= return_field_value("id","wo_booking_mst","booking_no='$booking_no' and status_active=1");
			$is_approved = return_field_value("is_approved","wo_booking_mst","id=$booking_idss and status_active=1 and is_approved in(1,3)");
			if($is_approved==1 || $is_approved==3)
			{
				echo "14**Approved. Update or Delete not allowed.";
				disconnect($con);
				die;
			}

			$flag=1;
			if($booking_no)
			{
				$field_array_up="fabric_source*currency_id*source*revised_no*exchange_rate*pay_mode*supplier_id*attention*ready_to_approved*team_leader*dealing_marchant*remarks*rmg_process_breakdown*updated_by*update_date";
				$data_array_up ="".$cbo_fabric_source."*".$cbo_currency."*".$cbo_sources."*".$txt_revise_no."*".$txt_exchange_rate."*".$cbo_pay_mode."*".$cbo_supplier_name."*".$txt_attention."*".$cbo_ready_to_approved_book."*".$cbo_team_leader_book."*".$cbo_dealing_merchant_book."*'".$txt_booking_remark."'*".$txt_processloss_breck_down."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				$rID=sql_update("wo_booking_mst",$field_array_up,$data_array_up,"booking_no","".$txt_booking_no."",0);
				
				if($rID) $flag=1;else $flag=0;
				$new_booking_no=$booking_no;
				
			}
			else
			{
				if($db_type==0)
				{
					$new_booking_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'SM', date("Y",time()), 5, "select booking_no_prefix, booking_no_prefix_num from wo_booking_mst where company_id=$cbo_company_name and booking_type=4 and YEAR(insert_date)=".date('Y',time())." order by booking_no_prefix_num desc ", "booking_no_prefix", "booking_no_prefix_num" ));
				}
				if($db_type==2)
				{
					$new_booking_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'SM', date("Y",time()), 5, "select booking_no_prefix, booking_no_prefix_num from wo_booking_mst where company_id=$cbo_company_name and booking_type=4 and to_char(insert_date,'YYYY')=".date('Y',time())." order by booking_no_prefix_num desc ", "booking_no_prefix", "booking_no_prefix_num" ));
				}
				$id=return_next_id( "id", "wo_booking_mst", 1 ) ;
				$field_array="id,booking_type,booking_no_prefix,booking_no_prefix_num,booking_no,company_id,buyer_id,item_category,fabric_source,currency_id,source,revised_no,exchange_rate,pay_mode,booking_date,supplier_id,attention,ready_to_approved,team_leader,dealing_marchant,inserted_by,insert_date,entry_form,rmg_process_breakdown,remarks";
				$data_array ="(".$id.",4,'".$new_booking_no[1]."',".$new_booking_no[2].",'".$new_booking_no[0]."',".$cbo_company_name.",".$cbo_buyer_name.",'2',".$cbo_fabric_source.",".$cbo_currency.",".$cbo_sources.",".$txt_revise_no.",".$txt_exchange_rate.",".$cbo_pay_mode.",".$txt_booking_date.",".$cbo_supplier_name.",".$txt_attention.",".$cbo_ready_to_approved_book.",".$cbo_team_leader_book.",".$cbo_dealing_merchant_book.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','140',".$txt_processloss_breck_down.",'".$txt_booking_remark."')";
				//echo "10** insert into wo_booking_mst ($field_array) values $data_array";die;
				$rID=sql_insert("wo_booking_mst",$field_array,$data_array,0);
				if($rID) $flag=1;else $flag=0;
				$new_booking_no=$new_booking_no[0];
				$update_prev=1;
				$lap=1;

				//========lapdip====================
				/* $id_lap=return_next_id( "id", "wo_po_lapdip_approval_info", 1 ) ;
				$field_array_lap="id,booking_no,booking_id,color_name_id,status_active,is_deleted";
				$data_array_lapdip=sql_select("SELECT sample_color,sample_mst_id FROM sample_development_dtls  WHERE entry_form_id = 203
				AND sample_mst_id = $update_id AND is_deleted = 0	AND status_active = 1 GROUP BY  sample_color,sample_mst_id");
				foreach ( $data_array_lapdip as $row_lap1 )
				{
					$dup_lap=sql_select("select id from wo_po_lapdip_approval_info where booking_id=$id and color_name_id=".$row_lap1[csf('sample_color')]."  and status_active=1 and is_deleted=0");
					list($idlap)=$dup_lap;
					if( $idlap[csf('id')] =='')
					{
						if ($lap!=1) $data_array_lap .=",";
						$data_array_lap .="(".$id_lap.",'".$new_booking_no."',".$id.",".$row_lap1[csf('sample_color')].",1,0)";
						$id_lap=$id_lap+1;
						$lap=$lap+1;
					}
				}
				if($data_array_lap !='')
				{
					$rID4=sql_insert("wo_po_lapdip_approval_info",$field_array_lap,$data_array_lap,1);
					if($rID4==1 && $flag==1) $flag=1; else $flag=0;
				} */
			
			}
			if($flag==1)
			{
				if($booking_no)
				{
					$select_prev=sql_select("SELECT booking_no from wo_booking_dtls where status_active=1 and is_deleted=0 and entry_form_id=140  and style_id=$update_id group by booking_no");
					if(count($select_prev)>0)
					{
						$update_prev=execute_query("UPDATE wo_booking_dtls set status_active=0,is_deleted=1 where entry_form_id=140 and style_id=$update_id  ");
						if($flag==1)
						{
							if($update_prev) $flag=1;else $flag=0;
						}
					}
				}
				$id_dtls=return_next_id( "id", "wo_booking_dtls", 1 ) ;
				$field_array_dtls= "id,booking_no,booking_mst_id,booking_type, is_short,job_no, po_break_down_id,style_id,sample_type,gmt_item,body_part,fabric_source, fabric_description,gsm_weight,dia,color_all_data,color_type,dia_width,uom,req_dzn,fin_fab_qnty,pre_cost_fabric_cost_dtls_id,inserted_by,insert_date,status_active,is_deleted,entry_form_id,process_loss_percent,grey_fab_qnty,remark,gmts_color_id,fabric_color_id,delivery_date";

				$yarn_deter_id="";
				$k=1;
				foreach($po_arr as $poid=>$poqty){
					for ($i=1;$i<=$total_row;$i++)
					{
						$cboRfSampleName="cboRfSampleName_".$i;
						$cboRfGarmentItem="cboRfGarmentItem_".$i;
						$cboRfBodyPart="cboRfBodyPart_".$i;
						$cboRfFabricSource="cboRfFabricSource_".$i;
						$txtRfFabricDescription="txtRfFabricDescription_".$i;
						$txtRfGsm="txtRfGsm_".$i;
						$txtRfDia="txtRfDia_".$i;
						$txtRfColor="txtRfColor_".$i;
						$cboRfColorType="cboRfColorType_".$i;
						$cboRfWidthDia="cboRfWidthDia_".$i;
						$cboRfUom="cboRfUom_".$i;
						$txtRfReqDzn="txtRfReqDzn_".$i;
						$txtRfReqQty="txtRfReqQty_".$i;
						$txtRfColorAllData="txtRfColorAllData_".$i;
						$required_fab_id="updateidRequiredDtl_".$i;
						$txtProcessLoss="txtProcessLoss_".$i;
						$txtGrayFabric="txtGrayFabric_".$i;
						$libyarncountdeterminationid="libyarncountdeterminationid_".$i;
						$txtRfRemarks="txtRfRemarks_".$i;
						$fabricDelvDate="fabricDelvDate_".$i;
						$cboRfFabricNatureId="cboRfFabricNature_".$i;
						$yarn_deter_id.=$$libyarncountdeterminationid.',';
						
						$libDeterId=str_replace("'",'',$$libyarncountdeterminationid);
						$fab_nature_arr[$libDeterId]=str_replace("'",'',$$cboRfFabricNatureId);
						$fab_greyQty_arr[$libDeterId]=str_replace("'",'',$$txtGrayFabric);
						$fab_gsm_arr[$libDeterId]=str_replace("'",'',$$txtRfGsm);

						$RfRemarks=str_replace("'","",$txtRfRemarks);
					$Rf_Remarks=str_replace($str_rep,' ',$RfRemarks);
						

						if ($k!=1) $data_array_dtls .=",";
						$ex_data=explode("-----",str_replace("'","",$$txtRfColorAllData));
						foreach($ex_data as $color_data)
						{
							$ex_size_data=explode("_",$color_data);
							$contrast=$ex_size_data[3];
							if(str_replace("'","",$contrast)!="")
							{
								if (!in_array(str_replace("'","",$contrast),$new_array_color))
								{
									$fab_color_id = return_id( str_replace("'","",$contrast), $color_arr, "lib_color", "id,color_name","140");
									$new_array_color[$fab_color_id]=str_replace("'","",$contrast);
								}
								else $fab_color_id =  array_search(str_replace("'","",$contrast), $new_array_color);
							}
							else $fab_color_id=0;					
							$colorName=$ex_size_data[1];
							$colorId=$ex_size_data[2];
							$contrast=$ex_size_data[3];

							$fabric_cons=$ex_size_data[4]/$po_qty;
							$qnty2=$fabric_cons*$poqty;


							$txtProcessLoss2=$ex_size_data[5];

							$grayfabric_cons=$ex_size_data[6]/$po_qty;
							$txtGrayFabric2=$grayfabric_cons*$poqty;
							//$txtGrayFabric2=$ex_size_data[6];
							$fab_col_id=$fab_color_id;
							
							if($txtGrayFabric2>0)
							{
							$data_array_dtls .="(".$id_dtls.",'".$new_booking_no."','".$id."',4,2,'".$job_no."',".$poid.",".$update_id.",".$$cboRfSampleName.",".$$cboRfGarmentItem.",".$$cboRfBodyPart.",".$$cboRfFabricSource.",".$$txtRfFabricDescription.",".$$txtRfGsm.",".$$txtRfDia.",".$$txtRfColorAllData.",".$$cboRfColorType.",".$$cboRfWidthDia.",".$$cboRfUom.",".$$txtRfReqDzn.",'".$qnty2."',".$$required_fab_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0','140','".$txtProcessLoss2."','".$txtGrayFabric2."','".$Rf_Remarks."','".$colorId."','".$fab_col_id."',".$$fabricDelvDate.")";

							$id_dtls=$id_dtls+1;
							$k++;
							}

						}
					}
				}
				//echo "10** insert into wo_booking_dtls ($field_array_dtls) values $data_array_dtls";die;
				$rID_1=sql_insert("wo_booking_dtls",$field_array_dtls,$data_array_dtls,0);
				if($flag==1)
				{
					if($rID_1) $flag=1;else $flag=0;
				}
				/* $updateId=str_replace("'",'',$update_id);

				$rID_up_yarn=execute_query( "update sample_development_yarn_dtls set booking_no='".$new_booking_no."',update_date='".$pc_date_time."',updated_by=".$_SESSION['logic_erp']['user_id']." where mst_id in($updateId)",0);
				if($flag==1)
				{
					if($rID_up_yarn) $flag=1;else $flag=0;
				} */
			}

			$rID3= execute_query( "update fabric_sales_order_mst set is_apply_last_update=2 where sales_booking_no =".$txt_booking_no." and within_group=1 and status_active=1 and is_deleted=0",0);
			if($flag==1)
			{
				if($rID3) $flag=1;else $flag=1;
			}
			
			//echo '10**='.$rID.'&&'.$rID_1;die;
			if($db_type==0)
			{
				//if($rID && $rID_1 && $rID_in2 && $update_prev)
				if($flag==1)
				{
					mysql_query("COMMIT");
					echo "0**".$new_booking_no;
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "10**".$new_booking_no;
				}
			}
			else if($db_type==2 || $db_type==1 )
			{
				//if($rID && $rID_1 && $rID_in2 && $update_prev)
				if($flag==1)
				{
					oci_commit($con);
					echo "0**".$new_booking_no;
				}
				else
				{
					oci_rollback($con);
					echo "10**".$new_booking_no;
				}
			}
			disconnect($con);
			die;
		}
	}
	
	exit();
}


if ($action=="populate_booking_data_from_search_popup")
{
	  $sql= "SELECT booking_no,booking_date,company_id,buyer_id,item_category,fabric_source,currency_id,exchange_rate,pay_mode,booking_month,supplier_id,attention,delivery_date,source,booking_year,is_approved,ready_to_approved,team_leader,	dealing_marchant,style_desc,source,revised_no,buyer_req_no,rmg_process_breakdown from wo_non_ord_samp_booking_mst  where booking_no='$data' and entry_form_id='140' and status_active=1 and is_deleted=0 order by booking_no desc ";
	 $requisition_id=return_field_value( "style_id", "wo_non_ord_samp_booking_dtls","booking_no='$data' and status_active=1 and is_deleted=0 and entry_form_id=140");
	  $requisition_no=return_field_value( "requisition_number", "sample_development_mst","id='$requisition_id' and entry_form_id=203 and status_active=1 and is_deleted=0");

	 $data_array=sql_select($sql);
	 foreach ($data_array as $row)
	 {
 		echo "load_drop_down( 'requires/sample_requisition_with_booking_controller', '".$row[csf("pay_mode")]."', 'load_drop_down_suplier', 'sup_td' );\n";
 		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";
		if($row[csf("buyer_id")]>0)
		{
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_id")]."';\n";
		}
		echo "document.getElementById('txt_booking_no').value = '".$row[csf("booking_no")]."';\n";
		echo "document.getElementById('cbo_fabric_natu').value = '2';\n";
		echo "document.getElementById('cbo_fabric_source').value = '".$row[csf("fabric_source")]."';\n";
		echo "document.getElementById('cbo_currency').value = '".$row[csf("currency_id")]."';\n";
		echo "document.getElementById('txt_exchange_rate').value = '".$row[csf("exchange_rate")]."';\n";
		echo "document.getElementById('cbo_pay_mode').value = '".$row[csf("pay_mode")]."';\n";
		echo "document.getElementById('txt_processloss_breck_down').value = '".$row[csf("rmg_process_breakdown")]."';\n";
		echo "document.getElementById('cbo_ready_to_approved_book').value = '".$row[csf("ready_to_approved")]."';\n";
		echo "document.getElementById('cbo_team_leader_book').value = '".$row[csf("team_leader")]."';\n";
		//echo "load_drop_down( 'requires/sample_requisition_with_booking_controller', '".$row[csf("team_leader")]."', 'cbo_dealing_merchant_book', 'div_marchant' );\n";
		echo "document.getElementById('cbo_dealing_merchant_book').value = '".$row[csf("dealing_marchant")]."';\n";

		echo "document.getElementById('txt_booking_date').value = '".change_date_format($row[csf("booking_date")],'dd-mm-yyyy','-')."';\n";
		echo "document.getElementById('cbo_supplier_name').value = '".$row[csf("supplier_id")]."';\n";
		echo "document.getElementById('txt_attention').value = '".$row[csf("attention")]."';\n";
		echo "document.getElementById('txt_style_desc_book').value = '".$row[csf("style_desc")]."';\n";
		echo "document.getElementById('txt_buyer_req_no').value = '".$row[csf("buyer_req_no")]."';\n";
		echo "document.getElementById('cbo_sources').value = '".$row[csf("source")]."';\n";
		echo "document.getElementById('txt_revise_no').value = '".$row[csf("revised_no")]."';\n";
	}




 }


 if($action=="terms_condition_popup")
{
	echo load_html_head_contents("Order Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
	function add_break_down_tr(i)
	{
		var row_num=$('#tbl_termcondi_details tr').length-1;
		if (row_num!=i)
		{
			return false;
		}
		else
		{
			i++;

			 $("#tbl_termcondi_details tr:last").clone().find("input,select").each(function() {
				$(this).attr({
				  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
				  'name': function(_, name) { return name + i },
				  'value': function(_, value) { return value }
				});
			  }).end().appendTo("#tbl_termcondi_details");
			 $('#increase_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
			  $('#decrease_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+")");
			  $('#termscondition_'+i).val("");
		}

	}

	function fn_deletebreak_down_tr(rowNo)
	{


			var numRow = $('table#tbl_termcondi_details tbody tr').length;
			if(numRow==rowNo && rowNo!=1)
			{
				$('#tbl_termcondi_details tbody tr:last').remove();
			}

	}

	function fnc_fabric_booking_terms_condition( operation )
	{
		    var row_num=$('#tbl_termcondi_details tr').length-1;
			var data_all="";
			for (var i=1; i<=row_num; i++)
			{

				if (form_validation('termscondition_'+i,'Term Condition')==false)
				{
					return;
				}

				data_all=data_all+get_submitted_data_string('txt_booking_no*termscondition_'+i,"");
			}
			var data="action=save_update_delete_fabric_booking_terms_condition&operation="+operation+'&total_row='+row_num+data_all;
			//freeze_window(operation);
			http.open("POST","sample_requisition_with_booking_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_fabric_booking_terms_condition_reponse;
	}

	function fnc_fabric_booking_terms_condition_reponse()
	{

		if(http.readyState == 4)
		{
		    var reponse=trim(http.responseText).split('**');
				if (reponse[0].length>2) reponse[0]=10;
				if(reponse[0]==0 || reponse[0]==1)
				{
					//alert(reponse[0]);
					parent.emailwindow.hide();
				}
		}
	}
    </script>

</head>

<body>
<div align="center" style="width:100%;" >
<? echo load_freeze_divs ("../../../",$permission);  ?>
<fieldset>
        	<form id="termscondi_1" autocomplete="off">
           <input type="text" id="txt_booking_no" name="txt_booking_no" value="<? echo str_replace("'","",$txt_booking_no) ?>"/>
            <table width="650" cellspacing="0" class="rpt_table" border="0" id="tbl_termcondi_details" rules="all">
                	<thead>
                    	<tr>
                        	<th width="50">Sl</th><th width="530">Terms</th><th ></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$data_array=sql_select("select id, terms from  wo_booking_terms_condition where entry_form=140 and booking_no=$txt_booking_no");// quotation_id='$data'
					if ( count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
							$i++;
							?>
                            	<tr id="settr_1" align="center">
                                    <td>
                                    <? echo $i;?>
                                    </td>
                                    <td>
                                     <input type="text" id="termscondition_<? echo $i;?>"   name="termscondition_<? echo $i;?>" style="width:95%"  class="text_boxes"  value="<? echo $row[csf('terms')]; ?>"  />
                                    </td>
                                    <td>
                                    <input type="button" id="increase_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<? echo $i; ?> )" />
                                    <input type="button" id="decrease_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?>);" />
                                    </td>
                                </tr>
                            <?
						}
					}
					else
					{
						//echo "select id, terms from  lib_terms_condition  where is_default=1 and page_id in(203,140) order by id asc ";
					$data_array2=sql_select("select id, terms from  lib_terms_condition  where is_default=1 and page_id in(203) order by id asc ");// quotation_id='$data'
					foreach( $data_array2 as $row )
						{
							$i++;
					?>
                    <tr id="settr_1" align="center">
                                    <td>
                                    <? echo $i;?>
                                    </td>
                                    <td>
                                    <input type="text" id="termscondition_<? echo $i;?>"   name="termscondition_<? echo $i;?>" style="width:95%"  class="text_boxes"  value="<? echo $row[csf('terms')]; ?>"  />
                                    </td>
                                    <td>
                                    <input type="button" id="increase_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<? echo $i; ?> )" />
                                    <input type="button" id="decrease_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?> );" />
                                    </td>
                                </tr>
                    <?
						}
					}
					?>
                </tbody>
                </table>

                <table width="650" cellspacing="0" class="" border="0">
                	<tr>
                        <td align="center" height="15" width="100%"> </td>
                    </tr>
                	<tr>
                        <td align="center" width="100%" class="button_container">
						        <?
									echo load_submit_buttons( $permission, "fnc_fabric_booking_terms_condition", 0,0 ,"reset_form('termscondi_1','','','','')",1) ;
									?>
                        </td>
                    </tr>
                </table>

            </form>
        </fieldset>
</div>
<script type="text/javascript">
	var data_array='<? echo count($data_array) ;?>';
	var permissions='<? echo $permission ;?>';
	if(data_array*1>0)
	{
		set_button_status(1, permissions, 'fnc_fabric_booking_terms_condition',1);
 	}

</script>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="save_update_delete_fabric_booking_terms_condition")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if ($operation==0 || $operation==1 )  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		 $id=return_next_id( "id", "wo_booking_terms_condition", 1 ) ;
		 $field_array="id,booking_no,terms,entry_form";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $termscondition="termscondition_".$i;
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$txt_booking_no.",".$$termscondition.",140)";
			$id=$id+1;
		 }
		$rID_de3=execute_query( "delete from wo_booking_terms_condition where entry_form=140 and  booking_no =".$txt_booking_no."",0);
		if($operation==0)
		{
			$rID_de3=1;
		}

		 $rID=sql_insert("wo_booking_terms_condition",$field_array,$data_array,1);
		 //check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID && $rID_de3 ){
				mysql_query("COMMIT");
				echo "0**".$new_booking_no[0];
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$new_booking_no[0];
			}
		}

		if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID_de3 ){
				oci_commit($con);
				echo "0**".$new_booking_no[0];
			}
			else{
				oci_rollback($con);
				echo "10**".$new_booking_no[0];
			}
		}
		disconnect($con);
		die;
	}
	exit();

}

if($action=="sample_name_change_popup")
{
	echo load_html_head_contents("Sample Change","../../../", 1, 1, $unicode);
	extract($_REQUEST);

	?>
	<script>

	var sample_mst_id='<? echo $sample_mst_id;?>';

	function fnc_sample_name_change( operation )
	{
			var sample_from=$("#sample_from").val();
			var sample_to=$("#sample_to").val();
 			var data="action=save_update_delete_sample_name_change&operation="+operation+'&sample_from='+sample_from+'&sample_to='+sample_to+'&sample_mst_id='+sample_mst_id;
			//freeze_window(operation);
			http.open("POST","sample_requisition_with_booking_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_sample_name_change_reponse;
	}

	function fnc_sample_name_change_reponse()
	{

		if(http.readyState == 4)
		{
		    var reponse=trim(http.responseText).split('**');
				if (reponse[0].length>2) reponse[0]=10;
				if(reponse[0]==0 || reponse[0]==1)
				{
					//alert(reponse[0]);
					parent.emailwindow.hide();
				}
		}
	}
    </script>

	</head>

	<body>
		<div align="center" style="width:100%;margin-top:20px;" >
			<? echo load_freeze_divs ("../../../",$permission);  ?>
			<fieldset width="400"  style=" margin-top:20px;">
				<form id="sample_change" autocomplete="off">

					<table width="400" cellspacing="0" class="" border="0" id="" rules="">

						<tbody>
						<tr>
							<td class="must_entry_caption"><strong>Sample From</strong></td>
							<td>
								<?
								$sql="select a.id,a.sample_name  from lib_sample a,lib_buyer_tag_sample b,sample_development_dtls c  where a.id=b.tag_sample and a.id=c.sample_name and c.sample_mst_id ='$sample_mst_id' and  b.buyer_id='$cbo_buyer_name' and b.sequ  is not null group by a.id,a.sample_name  ";

									$sql_to="select a.id,a.sample_name  from lib_sample a,lib_buyer_tag_sample b where a.id=b.tag_sample and  b.buyer_id='$cbo_buyer_name' and b.sequ  is not null group by a.id,a.sample_name  ";
									echo create_drop_down( "sample_from", 100, $sql,"id,sample_name", 1, "Select Sample", $selected, "");
								?>
							</td>
							<td class="must_entry_caption"><strong>Sample To</strong></td>
							<td>
								<?

									echo create_drop_down( "sample_to", 100, $sql_to,"id,sample_name", 1, "Select Sample", $selected, "");
								?>
							</td>
						</tr>

						</tbody>
					</table>

					<table width="400" cellspacing="0" class="" border="0">
						<tr>
							<td align="center" height="15" width="100%"> </td>
						</tr>
						<tr>
							<td align="center" width="100%" class="button_container">
								<?
								echo load_submit_buttons( $permission, "fnc_sample_name_change", 1,0 ,"reset_form('sample_change','','','','')",1) ;
								?>
							</td>
						</tr>
					</table>

				</form>
			</fieldset>
		</div>

	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}
if($action=="save_update_delete_sample_name_change")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if ($operation==1 )  // update only
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}


		$rID1=execute_query( "update sample_development_dtls set sample_name='$sample_to',prev_sample_id='$sample_from' where sample_mst_id='$sample_mst_id'  and sample_name='$sample_from' and entry_form_id=203 ",0);
		$rID2=execute_query( "update sample_development_fabric_acc set sample_name='$sample_to' where sample_mst_id='$sample_mst_id'  and sample_name='$sample_from' and form_type=1   ",0);
		$rID3=execute_query( "update sample_development_fabric_acc set sample_name_ra='$sample_to' where sample_mst_id='$sample_mst_id'  and sample_name_ra='$sample_from'  and form_type=2 ",0);
		$rID4=execute_query( "update sample_development_fabric_acc set sample_name_re='$sample_to' where sample_mst_id='$sample_mst_id'  and sample_name_re='$sample_from' and form_type=3   ",0);

		$rID5=execute_query( "update wo_non_ord_samp_booking_dtls set sample_type='$sample_to' where style_id='$sample_mst_id'  and sample_type='$sample_from'    ",0);

		$rID6=execute_query( "update sample_ex_factory_dtls set sample_name='$sample_to' where sample_development_id='$sample_mst_id'  and sample_name='$sample_from'    ",0);
		$all_production_mst_id_arr=array();
		$prod_sql="SELECT   id,   sample_development_id from sample_sewing_output_mst Where sample_development_id = '$sample_mst_id' ";
		foreach(sql_select($prod_sql) as $v)
		{
			$all_production_mst_id_arr[$v[csf("id")]]=$v[csf("id")];
		}
		$all_production_mst_ids=implode(",", $all_production_mst_id_arr);
		if(!$all_production_mst_ids)$all_production_mst_ids=0;

		$rID7=execute_query( "update sample_sewing_output_dtls set sample_name='$sample_to' where sample_sewing_output_mst_id in($all_production_mst_ids)  and sample_name='$sample_from'    ",0);
		//echo $rID1.  $rID2. $rID3. $rID4. $rID5. $rID6. $rID7;die;

		if($db_type==0)
		{
			if($rID1 && $rID2 && $rID3 && $rID4 && $rID5 && $rID6 && $rID7 ){
				mysql_query("COMMIT");
				echo "1**";
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**";
			}
		}

		if($db_type==2 || $db_type==1 )
		{
			if($rID1 && $rID2 && $rID3 && $rID4 && $rID5 && $rID6 && $rID7 ){
				oci_commit($con);
				echo "1**";
			}
			else{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
	exit();

}
if ($action=="load_drop_down_sub_dep")
{
	$data=explode("_",$data);
	echo create_drop_down( "cbo_sub_dept", 130, "select id,sub_department_name from lib_pro_sub_deparatment where buyer_id=$data[0] and	department_id='$data[1]' and status_active =1 and is_deleted=0 order by sub_department_name","id,sub_department_name", 1, "-- Select Sub Dep --", $selected, "" );
	exit();
}
if ($action=="load_drop_down_brand")
{
	$data_arr = explode("*", $data);
	if($data_arr[1] == 1) $width=130; else $width=130;
	echo create_drop_down( "cbo_brand_id", $width, "select id, brand_name from lib_buyer_brand brand where buyer_id='$data_arr[0]' and status_active =1 and is_deleted=0 $brand_cond order by brand_name ASC","id,brand_name", 1, "--Brand--", $selected, "" );
	exit();
}
if ($action=="load_drop_down_party_type")
{
	echo create_drop_down( "cbo_client", 130, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$data' and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (7))  order by buyer_name","id,buyer_name", 1, "-- Select Client --", $selected, "" ); 
	exit();	 
}

if ($action == "collarCuff_info_popup")
{
	echo load_html_head_contents("Collar & Cuff Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$lib_color_arr=return_library_array( "select id,color_name from lib_color", "id","color_name" );
	$lib_size_arr=return_library_array( "select id,size_name from lib_size", "id","size_name" );
	?>
	<script>

		function fnc_close() {
			var save_string = "";
			var breakOut = true;
			$("#tbl_list_search").find('tbody tr').each(function () {
				if (breakOut == false) {
					return;
				}

				var txtgmtcolorid = $(this).find('input[name="txtgmtcolorid[]"]').val();
				var txtgmtsizeid = $(this).find('input[name="txtgmtsizeid[]"]').val();
				var txtitemsize = $(this).find('input[name="txtitemsize[]"]').val();
				//var txtFinish = $(this).find('input[name="txtFinish[]"]').val();
				var txtQtyPcs = $(this).find('input[name="txtQtyPcs[]"]').val() * 1;
				if (txtQtyPcs >0) {
					var newitemsize = txtitemsize.replace(/[_,]/g, " ");
					if (save_string == "") {
						save_string = txtgmtcolorid + "_" + txtgmtsizeid + "_" + newitemsize + "_" + txtQtyPcs;
					}
					else {
						save_string += "," + txtgmtcolorid + "_" + txtgmtsizeid + "_" + newitemsize + "_" + txtQtyPcs;
					}
				}

				
			});
			$('#hidden_collarCuff_data').val(save_string);
			parent.emailwindow.hide();
		}

		function calculate_tot_qnty() {
			var txtTotQtyPcs = '';
			$("#tbl_list_search").find('tbody tr').each(function () {
				var txtQtyPcs = $(this).find('input[name="txtQtyPcs[]"]').val() * 1;
				txtTotQtyPcs = txtTotQtyPcs * 1 + txtQtyPcs * 1;
			});

			$('#txtTotQtyPcs').val(Math.round(txtTotQtyPcs));
		}
		
	</script>
	</head>
	<body>
		<div style="width:570px;" align="center">
			<form name="searchwofrm" id="searchwofrm">
				<fieldset style="width:570px; margin-top:5px">
					<input type="hidden" name="hidden_collarCuff_data" id="hidden_collarCuff_data" class="text_boxes"
					value="">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="560" class="rpt_table">
						<thead>
							<th width="30">SL</th>
							<th width="100">Gmt Color</th>
							<th width="100">Gmt Size</th>
							<th width="100">Item Size</th>
							<th width="100">Gmt Qnty</th>
							<th width="100">Qty. Pcs</th>
						</thead>
					</table>
					<div style="width:570px; overflow-y:scroll; max-height:230px;" id="buyer_list_view">
						<table cellspacing="0" cellpadding="0" border="1" rules="all" width="550" class="rpt_table"
						id="tbl_list_search">
						<tbody>
							<?
							$size_color_data = sql_select("SELECT a.sample_name, a.sample_color,  b.size_id, b.total_qty from sample_development_dtls a join sample_development_size b on a.id=b.dtls_id left join lib_size s on b.size_id=s.id where a.sample_name=$sampleid and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.sample_mst_id=$updateId order by a.sample_color,s.sequence"); 
							$i=1;
							$collarCuff_data = ($collarCuff_data != "") ? explode(",", $collarCuff_data) : array();
							if (count($collarCuff_data)>0)
							{
								$sl = 1;
								for ($i = 0; $i < count($collarCuff_data); $i++)
								{
									$body_part_wise_data = explode("_", $collarCuff_data[$i]);
									$sample_color = $body_part_wise_data[0];
									$size_id = $body_part_wise_data[1];
									$itemsize = $body_part_wise_data[2];
									$qty = $body_part_wise_data[3];
									$collarCuffarr[$sample_color][$size_id]['sample_color']= $sample_color;
									$collarCuffarr[$sample_color][$size_id]['size_id']= $size_id;
									$collarCuffarr[$sample_color][$size_id]['itemsize']= $itemsize;
									$collarCuffarr[$sample_color][$size_id]['qty']= $qty;
								}
								foreach ($size_color_data as $row) {
									if($bodyparttype==40)
									{
										$txtQtyPcs = $row[csf('total_qty')];
									}
									if($bodyparttype==50)
									{
										$txtQtyPcs = $row[csf('total_qty')]*2;
									}
									
									?>
									<tr align="center" bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $sl++; ?>">
										<td width="30" align="center"><? echo $sl; ?></td>
										<td width="100">
											<input type="hidden" name="txtgmtcolorid[]" id="txtgmtcolorid_<?php echo $i ?>" value="<? echo $row[csf('sample_color')] ?>">
											<input type="hidden" id="bodyParttypeId_<?php echo $i ?>"
											value="<? echo $bodyparttype; ?>"/>
											<? echo $lib_color_arr[$row[csf('sample_color')]]; ?>
										</td>
										<td width="100">
											<input type="hidden" name="txtgmtsizeid[]" id="txtgmtsizeid_<?php echo $i ?>" value="<? echo $row[csf('size_id')] ?>">
											<?php echo $lib_size_arr[$row[csf('size_id')]]; ?>
										</td>
										<td width="100"><input type="text" name="txtitemsize[]" id="txtitemsize_<? echo $i; ?>"
											class="text_boxes" style="width:80px"
											value="<? echo $collarCuffarr[$row[csf('sample_color')]][$row[csf('size_id')]]['itemsize'];  ?>"/>
										</td>
										<td width="100"><input type="text" name="txtFinish[]" id="txtFinish_<? echo $i; ?>"
											class="text_boxes" style="width:80px"
											value="<?php echo $row[csf('total_qty')]; ?>" readonly />
										</td>
										<td width="100">
											<input type="text" name="txtQtyPcs[]" id="txtQtyPcs_<? echo $i; ?>"
											class="text_boxes_numeric" style="width:80px"
											value="<? echo $collarCuffarr[$row[csf('sample_color')]][$row[csf('size_id')]]['qty'] ?>" onChange="calculate_tot_qnty()" placeholder="<? echo $txtQtyPcs ?>"/>
										</td>
									</tr>
									<?	
									$total_finish_qty+=$row[csf('total_qty')];									
									$total_qty_pcs+=$txtQtyPcs;	
									$totalpcsqty+=$collarCuffarr[$row[csf('sample_color')]][$row[csf('size_id')]]['qty'];
									$i++;								
								}
							}
							else
							{
								if($update_dtls_id !='')
								{
									$sql = "select sample_color, size_id, item_size, qnty_pcs from sample_requisition_coller_cuff where dtls_id=$update_dtls_id and status_active=1 and is_deleted=0";
									$collar_cuff_data_arr = sql_select($sql);
									if(count($collar_cuff_data_arr)>0)
									{
										$sl = 1;
										foreach ($collar_cuff_data_arr as $row)
										{
											$sample_color = $row[csf('sample_color')];
											$size_id = $row[csf('size_id')];
											$itemsize = $row[csf('item_size')];
											$qty = $row[csf('qnty_pcs')];
											$collarCuffarr[$sample_color][$size_id]['sample_color']= $sample_color;
											$collarCuffarr[$sample_color][$size_id]['size_id']= $size_id;
											$collarCuffarr[$sample_color][$size_id]['itemsize']= $itemsize;
											$collarCuffarr[$sample_color][$size_id]['qty']= $qty;
										}
										foreach ($size_color_data as $row) {
											if($bodyparttype==40)
											{
												$txtQtyPcs = $row[csf('total_qty')];
											}
											if($bodyparttype==50)
											{
												$txtQtyPcs = $row[csf('total_qty')]*2;
											}
											
											?>
											<tr align="center" bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $sl++; ?>">
												<td width="30" align="center"><? echo $sl; ?></td>
												<td width="100">
													<input type="hidden" name="txtgmtcolorid[]" id="txtgmtcolorid_<?php echo $i ?>" value="<? echo $row[csf('sample_color')] ?>">
													<input type="hidden" id="bodyParttypeId_<?php echo $i ?>"
													value="<? echo $bodyparttype; ?>"/>
													<? echo $lib_color_arr[$row[csf('sample_color')]]; ?>
												</td>
												<td width="100">
													<input type="hidden" name="txtgmtsizeid[]" id="txtgmtsizeid_<?php echo $i ?>" value="<? echo $row[csf('size_id')] ?>">
													<?php echo $lib_size_arr[$row[csf('size_id')]]; ?>
												</td>
												<td width="100"><input type="text" name="txtitemsize[]" id="txtitemsize_<? echo $i; ?>"
													class="text_boxes" style="width:80px"
													value="<? echo $collarCuffarr[$row[csf('sample_color')]][$row[csf('size_id')]]['itemsize'];  ?>"/>
												</td>
												<td width="100"><input type="text" name="txtFinish[]" id="txtFinish_<? echo $i; ?>"
													class="text_boxes" style="width:80px"
													value="<?php echo $row[csf('total_qty')]; ?>" readonly />
												</td>
												<td width="100">
													<input type="text" name="txtQtyPcs[]" id="txtQtyPcs_<? echo $i; ?>"
													class="text_boxes_numeric" style="width:80px"
													value="<? echo $collarCuffarr[$row[csf('sample_color')]][$row[csf('size_id')]]['qty'] ?>" onChange="calculate_tot_qnty()" placeholder="<? echo $txtQtyPcs ?>"/>
												</td>
											</tr>
											<?	
											$total_finish_qty+=$row[csf('total_qty')];									
											$total_qty_pcs+=$txtQtyPcs;	
											$totalpcsqty+=$collarCuffarr[$row[csf('sample_color')]][$row[csf('size_id')]]['qty'];
											$i++;								
										}
									}
									else
									{
										foreach ($size_color_data as $row) {
											if($bodyparttype==40)
											{
												$txtQtyPcs = $row[csf('total_qty')];
											}
											if($bodyparttype==50)
											{
												$txtQtyPcs = $row[csf('total_qty')]*2;
											}
											
											?>
											<tr align="center" bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $sl++; ?>">
												<td width="30" align="center"><? echo $sl; ?></td>
												<td width="100">
													<input type="hidden" name="txtgmtcolorid[]" id="txtgmtcolorid_<?php echo $i ?>" value="<? echo $row[csf('sample_color')] ?>">
													<input type="hidden" id="bodyParttypeId_<?php echo $i ?>"
													value="<? echo $bodyparttype; ?>"/>
													<? echo $lib_color_arr[$row[csf('sample_color')]]; ?>
												</td>
												<td width="100">
													<input type="hidden" name="txtgmtsizeid[]" id="txtgmtsizeid_<?php echo $i ?>" value="<? echo $row[csf('size_id')] ?>">
													<?php echo $lib_size_arr[$row[csf('size_id')]]; ?>
												</td>
												<td width="100"><input type="text" name="txtitemsize[]" id="txtitemsize_<? echo $i; ?>"
													class="text_boxes" style="width:80px"
													value=""/>
												</td>
												<td width="100"><input type="text" name="txtFinish[]" id="txtFinish_<? echo $i; ?>"
													class="text_boxes" style="width:80px"
													value="<?php echo $row[csf('total_qty')]; ?>" readonly />
												</td>
												<td width="100">
													<input type="text" name="txtQtyPcs[]" id="txtQtyPcs_<? echo $i; ?>"
													class="text_boxes_numeric" style="width:80px"
													value="" onChange="calculate_tot_qnty()" placeholder="<? echo $txtQtyPcs ?>"/>
												</td>
											</tr>
											<?	
											$total_finish_qty+=$row[csf('total_qty')];									
											$total_qty_pcs+=$txtQtyPcs;	
											$i++;								
										}
									}									
								}
								else
								{									
									foreach ($size_color_data as $row) {
										?>
										<tr align="center" bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $sl++; ?>">
											<td width="30" align="center"><? echo $sl; ?></td>
											<td width="100">
												<input type="text" name="txtBodyPartId[]" id="txtgmtcolor_<?php echo $i ?>"
												value="<? echo $lib_color_arr[$row[csf('sample_color')]]; ?>" class="text_boxes"
												style="width:80px" disabled/>
												<input type="hidden" id="txtgmtcolorid_<?php echo $i ?>" value="<? echo $row[csf('sample_color')] ?>">
												<input type="hidden" id="bodyParttypeId_<?php echo $i ?>"
												value="<? echo $bodyparttype; ?>"/>
											</td>
											<td width="100"><input type="text" id="txtgmtsize_<? echo $i; ?>"
												class="text_boxes" style="width:80px"
												value="<?php echo $lib_size_arr[$row[csf('size_id')]]; ?>" disabled/>
												<input type="hidden" id="txtgmtsizeid_<?php echo $i ?>" value="<? echo $row[csf('size_id')] ?>">
											</td>
											<td width="100"><input type="text" name="txtFinish[]" id="txtFinish_<? echo $i; ?>"
												class="text_boxes" style="width:80px"
												value="<?php echo $row[csf('total_qty')]; ?>" readonly />
											</td>
											<td width="100">
												<input type="text" name="txtQtyPcs[]" id="txtQtyPcs_<? echo $i; ?>"
												class="text_boxes_numeric" style="width:80px" placeholder="<?  ?>"
												value=""/>
											</td>
										</tr>
										<?										
									}
								}
							}
							?>
						</tbody>
						<tfoot>
							<th colspan="4">Total</th>
							<th style="text-align:left;">
								<input type="text" name="txtTotgmtPcs" id="txtTotgmtPcs" class="text_boxes_numeric" style="width:80px"
								value="<? echo $total_finish_qty; ?>" readonly/>
								<input type="hidden" name="txt_tot_row" id="txt_tot_row" value="<? echo $i - 1; ?>"/></th>
								<th style="text-align:left;">
									<input type="text" name="txtTotQtyPcs" id="txtTotQtyPcs" class="text_boxes_numeric" style="width:80px"	value="<? echo $totalpcsqty; ?>" placeholder="<? echo $total_qty_pcs ?>" readonly/>
								</th>
							</tfoot>
						</table>
					</div>
					<table width="560" id="tbl_close">
						<tr>
							<td align="center">
								<input type="button" name="close" class="formbutton" value="Close" id="main_close"
								onClick="fnc_close();" style="width:100px"/>
							</td>
						</tr>
					</table>
				</fieldset>
			</form>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action == "additional_value_popup")
{
	echo load_html_head_contents("Additional Values Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	//$lib_color_arr=return_library_array( "select id,color_name from lib_color", "id","color_name" );
	//$lib_size_arr=return_library_array( "select id,size_name from lib_size", "id","size_name" );
	?>
	<script>

		function fnc_close() {
			var save_string = "";
			var print = $('#emblish_1').val();
			var printseq = $('#printseq_1').val();
			var embro = $('#embro_1').val();
			var embroseq = $('#embroseq_1').val();
			var wash = $('#wash_1').val();
			var washseq = $('#washseq_1').val();
			var spworks = $('#spworks_1').val();
			var spworksseq = $('#spworksseq_1').val();
			var gmtsdying = $('#gmtsdying_1').val();
			var gmtsdyingseq = $('#gmtsdyingseq_1').val();
			var aop = $('#aop_1').val();
			var aopseq = $('#aopseq_1').val();
			var brush = $('#brush_1').val();
			var brushseq = $('#brushseq_1').val();
			var peach = $('#peach_1').val();
			var peachseq = $('#peachseq_1').val();
			var yd = $('#yd_1').val();
			var ydseq = $('#ydseq_1').val();

			if(printseq==''){
				printseq=0;
			}
			if(embroseq==''){
				embroseq=0;
			}
			if(washseq==''){
				washseq=0;
			}
			if(spworksseq==''){
				spworksseq=0;
			}
			if(gmtsdyingseq==''){
				gmtsdyingseq=0;
			}
			if(aopseq==''){
				aopseq=0;
			}
			if(brushseq==''){
				brushseq=0;
			}
			if(peachseq==''){
				peachseq=0;
			}
			if(ydseq==''){
				ydseq=0;
			}

			if (save_string == "") {
				save_string = print + "_" + printseq + "_" + embro + "_" + embroseq+"_"+wash + "_" + washseq + "_" + spworks + "_" + spworksseq+"_"+gmtsdying + "_" + gmtsdyingseq + "_" + aop + "_" + aopseq+"_"+brush + "_" + brushseq + "_" + peach + "_" + peachseq+ "_" + yd + "_" + ydseq;
			}
			else {
				save_string += "," + print + "_" + printseq + "_" + embro + "_" + embroseq+"_"+wash + "_" + washseq + "_" + spworks + "_" + spworksseq+"_"+gmtsdying + "_" + gmtsdyingseq + "_" + aop + "_" + aopseq+"_"+brush + "_" + brushseq + "_" + peach + "_" + peachseq+ "_" + yd + "_" + ydseq;
			}
			$('#hidden_additional_value_data').val(save_string);
			parent.emailwindow.hide();
		}
	
	</script>
	</head>
	<body>
		<div style="width:910px;" align="center">
			<form name="searchwofrm" id="searchwofrm">
				<fieldset style="width:910px; margin-top:5px">
					<input type="hidden" name="hidden_additional_value_data" id="hidden_additional_value_data" class="text_boxes"
					value="">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="900" class="rpt_table">
						<thead>
							<th width="100">Print</th>
							<th width="100">Embro</th>
							<th width="100">Wash</th>
							<th width="100">SP. Works</th>
							<th width="100">Gmts Dyeing</th>
							<th width="100">AOP</th>
							<th width="100">Brushing</th>
							<th width="100">Peached Finish</th>
							<th width="100">Yarn Dyeing</th>
						</thead>
					</table>
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="900" class="rpt_table"
					id="tbl_list_search">
					<tbody>
						<?
						$i=1;
						$additionalvalue_data = ($additionalvalue_data != "") ? explode("_", $additionalvalue_data) : array();
						if (count($additionalvalue_data)>0)
						{
							$sl = 1;
							$print = $additionalvalue_data[0];
							$printseq = $additionalvalue_data[1];
							$embro = $additionalvalue_data[2];
							$embroseq = $additionalvalue_data[3];
							$wash = $additionalvalue_data[4];
							$washseq = $additionalvalue_data[5];
							$spworks = $additionalvalue_data[6];
							$spworksseq = $additionalvalue_data[7];
							$gmtsdying = $additionalvalue_data[8];
							$gmtsdyingseq = $additionalvalue_data[9];
							$aop = $additionalvalue_data[10];
							$aopseq = $additionalvalue_data[11];
							$brush = $additionalvalue_data[12];
							$brushseq = $additionalvalue_data[13];
							$peach = $additionalvalue_data[14];
							$peachseq = $additionalvalue_data[15];
							$yd = $additionalvalue_data[16];
							$ydseq = $additionalvalue_data[17];
							?>
								<tr align="center" bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $sl++; ?>">
									<td><? echo create_drop_down( "emblish_1", 60, $yes_no, "",1," -- Select --", $print , "",'','' ); ?>
		                                <input type="text" id="printseq_1"   name="printseq_1" style="width:20px" class="text_boxes_numeric" value="<? echo $printseq;  ?>" /> 
		                            </td>
		                            <td><? echo create_drop_down( "embro_1", 60, $yes_no, "",1," -- Select--", $embro, "",'','' ); ?>
		                                <input type="text" id="embroseq_1"   name="embroseq_1" style="width:20px" class="text_boxes_numeric" value="<? echo $embroseq;  ?>" /> 
		                            </td>
		                            <td><? echo create_drop_down( "wash_1", 60, $yes_no, "",1," -- Select--", $wash, "",'','' ); ?>
		                                <input type="text" id="washseq_1"   name="washseq_1" style="width:20px" class="text_boxes_numeric" value="<? echo $washseq;  ?>" /> 
		                            </td>
		                            <td><? echo create_drop_down( "spworks_1", 60, $yes_no, "",1," -- Select--", $spworks, "",'','' ); ?>
		                                <input type="text" id="spworksseq_1"   name="spworksseq_1" style="width:20px" class="text_boxes_numeric" value="<? echo $spworksseq;  ?>" /> 
		                            </td>
		                            <td><? echo create_drop_down( "gmtsdying_1", 60, $yes_no, "",1," -- Select--", $gmtsdying, "",$disabled,'' ); ?>
		                                <input type="text" id="gmtsdyingseq_1"   name="gmtsdyingseq_1" style="width:20px" class="text_boxes_numeric" value="<? echo $gmtsdyingseq;  ?>" /> 
		                            </td>
		                            <td><? echo create_drop_down( "aop_1", 60, $yes_no, "",1," -- Select--", $aop, "",'','' ); ?>
		                                <input type="text" id="aopseq_1"   name="aopseq_1" style="width:20px" class="text_boxes_numeric" value="<? echo $aopseq;  ?>" /> 
		                            </td>
		                            <td><? echo create_drop_down( "brush_1", 60, $yes_no, "",1," -- Select--", $brush , "",'','' ); ?>
		                                <input type="text" id="brushseq_1"   name="brushseq_1" style="width:20px" class="text_boxes_numeric" value="<? echo $brushseq;  ?>" /> 
		                            </td>
		                            <td><? echo create_drop_down( "peach_1", 60, $yes_no, "",1," -- Select--", $peach , "",'','' ); ?>
		                                <input type="text" id="peachseq_1"   name="peachseq_1" style="width:20px" class="text_boxes_numeric" value="<? echo $peachseq;  ?>" /> 
		                            </td>
		                            <td><? echo create_drop_down( "yd_1", 60, $yes_no, "",1," -- Select--", $yd, "",'','' ); ?>
		                                <input type="text" id="ydseq_1"   name="ydseq_1" style="width:20px" class="text_boxes_numeric" value="<? echo $ydseq;  ?>" /> 
		                            </td>
								</tr>
						<?
						}
						else
						{
							if($update_dtls_id !='')
							{
								$sql = "SELECT print, printseq, embro, embroseq, wash, washseq, spworks, spworksseq, gmtsdying, gmtsdyingseq, aop, aopseq, bush, bushseq, peach, peachseq, yd, ydseq from sample_details_additional_value where dtls_id=$update_dtls_id";
								//echo $sql; die;
								$additionalvalue_data_arr = sql_select($sql);
								if(count($additionalvalue_data_arr)>0)
								{
									$sl = 1;
									foreach ($additionalvalue_data_arr as $row)
									{																				
									?>
									<tr align="center" bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $sl++; ?>">
										<td><? echo create_drop_down( "emblish_1", 60, $yes_no, "",1," -- Select --", $row[csf('print')], "",'','' ); ?>
			                                <input type="text" id="printseq_1"   name="printseq_1" style="width:20px" class="text_boxes_numeric" value="<? echo $row[csf('printseq')] ?>" /> 
			                            </td>
			                            <td><? echo create_drop_down( "embro_1", 60, $yes_no, "",1," -- Select--", $row[csf('embro')], "",$disabled,'' ); ?>
			                                <input type="text" id="embroseq_1"   name="embroseq_1" style="width:20px" class="text_boxes_numeric" value="<? echo $row[csf('embroseq')] ?>" /> 
			                            </td>
			                            <td><? echo create_drop_down( "wash_1", 60, $yes_no, "",1," -- Select--", $row[csf('wash')], "",$disabled,'' ); ?>
			                                <input type="text" id="washseq_1"   name="washseq_1" style="width:20px" class="text_boxes_numeric" value="<? echo $row[csf('washseq')] ?>" /> 
			                            </td>
			                            <td><? echo create_drop_down( "spworks_1", 60, $yes_no, "",1," -- Select--", $row[csf('spworks')], "",$disabled,'' ); ?>
			                                <input type="text" id="spworksseq_1"   name="spworksseq_1" style="width:20px" class="text_boxes_numeric" value="<? echo $row[csf('spworksseq')] ?>" /> 
			                            </td>
			                            <td><? echo create_drop_down( "gmtsdying_1", 60, $yes_no, "",1," -- Select--", $row[csf('gmtsdying')], "",$disabled,'' ); ?>
			                                <input type="text" id="gmtsdyingseq_1"   name="gmtsdyingseq_1" style="width:20px" class="text_boxes_numeric" value="<? echo $row[csf('gmtsdyingseq')] ?>" /> 
			                            </td>
			                            <td><? echo create_drop_down( "aop_1", 60, $yes_no, "",1," -- Select--", $row[csf('aop')], "",$disabled,'' ); ?>
			                                <input type="text" id="aopseq_1"   name="aopseq_1" style="width:20px" class="text_boxes_numeric" value="<? echo $row[csf('aopseq')] ?>" /> 
			                            </td>
			                            <td><? echo create_drop_down( "brush_1", 60, $yes_no, "",1," -- Select--", $row[csf('bush')], "",$disabled,'' ); ?>
			                                <input type="text" id="brushseq_1"   name="brushseq_1" style="width:20px" class="text_boxes_numeric" value="<? echo $row[csf('bushseq')] ?>" /> 
			                            </td>
			                            <td><? echo create_drop_down( "peach_1", 60, $yes_no, "",1," -- Select--", $row[csf('peach')], "",$disabled,'' ); ?>
			                                <input type="text" id="peachseq_1"   name="peachseq_1" style="width:20px" class="text_boxes_numeric" value="<? echo $row[csf('peachseq')] ?>" /> 
			                            </td>
			                            <td><? echo create_drop_down( "yd_1", 60, $yes_no, "",1," -- Select--", $row[csf('yd')], "",$disabled,'' ); ?>
			                                <input type="text" id="ydseq_1"   name="ydseq_1" style="width:20px" class="text_boxes_numeric" value="<? echo $row[csf('ydseq')] ?>" /> 
			                            </td>
									</tr>
									<?
									$i++;
									}
								}
								else
								{										
									?>
									<tr align="center" bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $sl++; ?>">
										<td><? echo create_drop_down( "emblish_1", 60, $yes_no, "",1," -- Select --", 0, "",'','' ); ?>
			                                <input type="text" id="printseq_1"   name="printseq_1" style="width:20px" class="text_boxes_numeric" value="" /> 
			                            </td>
			                            <td><? echo create_drop_down( "embro_1", 60, $yes_no, "",1," -- Select--", '', "",$disabled,'' ); ?>
			                                <input type="text" id="embroseq_1"   name="embroseq_1" style="width:20px" class="text_boxes_numeric" value="" /> 
			                            </td>
			                            <td><? echo create_drop_down( "wash_1", 60, $yes_no, "",1," -- Select--", '', "",$disabled,'' ); ?>
			                                <input type="text" id="washseq_1"   name="washseq_1" style="width:20px" class="text_boxes_numeric" value="" /> 
			                            </td>
			                            <td><? echo create_drop_down( "spworks_1", 60, $yes_no, "",1," -- Select--", '', "",$disabled,'' ); ?>
			                                <input type="text" id="spworksseq_1"   name="spworksseq_1" style="width:20px" class="text_boxes_numeric" value="" /> 
			                            </td>
			                            <td><? echo create_drop_down( "gmtsdying_1", 60, $yes_no, "",1," -- Select--", '', "",$disabled,'' ); ?>
			                                <input type="text" id="gmtsdyingseq_1"   name="gmtsdyingseq_1" style="width:20px" class="text_boxes_numeric" value="" /> 
			                            </td>
			                            <td><? echo create_drop_down( "aop_1", 60, $yes_no, "",1," -- Select--", '', "",$disabled,'' ); ?>
			                                <input type="text" id="aopseq_1"   name="aopseq_1" style="width:20px" class="text_boxes_numeric" value="" /> 
			                            </td>


			                            <td><? echo create_drop_down( "brush_1", 60, $yes_no, "",1," -- Select--", '', "",$disabled,'' ); ?>
			                                <input type="text" id="brushseq_1"   name="brushseq_1" style="width:20px" class="text_boxes_numeric" value="" /> 
			                            </td>
			                            <td><? echo create_drop_down( "peach_1", 60, $yes_no, "",1," -- Select--", '', "",$disabled,'' ); ?>
			                                <input type="text" id="peachseq_1"   name="peachseq_1" style="width:20px" class="text_boxes_numeric" value="" /> 
			                            </td>
			                            <td><? echo create_drop_down( "yd_1", 60, $yes_no, "",1," -- Select--", '', "",$disabled,'' ); ?>
			                                <input type="text" id="ydseq_1"   name="ydseq_1" style="width:20px" class="text_boxes_numeric" value="" /> 
			                            </td>
									</tr>
									<?
								}									
							}
							else
							{									
								?>
								<tr align="center" bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $sl++; ?>">
									<td><? echo create_drop_down( "emblish_1", 60, $yes_no, "",1," -- Select --", 0, "",'','' ); ?>
		                                <input type="text" id="printseq_1"   name="printseq_1" style="width:20px" class="text_boxes_numeric" value="" /> 
		                            </td>
		                            <td><? echo create_drop_down( "embro_1", 60, $yes_no, "",1," -- Select--", '', "",$disabled,'' ); ?>
		                                <input type="text" id="embroseq_1"   name="embroseq_1" style="width:20px" class="text_boxes_numeric" value="" /> 
		                            </td>
		                            <td><? echo create_drop_down( "wash_1", 60, $yes_no, "",1," -- Select--", '', "",$disabled,'' ); ?>
		                                <input type="text" id="washseq_1"   name="washseq_1" style="width:20px" class="text_boxes_numeric" value="" /> 
		                            </td>
		                            <td><? echo create_drop_down( "spworks_1", 60, $yes_no, "",1," -- Select--", '', "",$disabled,'' ); ?>
		                                <input type="text" id="spworksseq_1"   name="spworksseq_1" style="width:20px" class="text_boxes_numeric" value="" /> 
		                            </td>
		                            <td><? echo create_drop_down( "gmtsdying_1", 60, $yes_no, "",1," -- Select--", '', "",$disabled,'' ); ?>
		                                <input type="text" id="gmtsdyingseq_1"   name="gmtsdyingseq_1" style="width:20px" class="text_boxes_numeric" value="" /> 
		                            </td>
		                            <td><? echo create_drop_down( "aop_1", 60, $yes_no, "",1," -- Select--", '', "",$disabled,'' ); ?>
		                                <input type="text" id="aopseq_1"   name="aopseq_1" style="width:20px" class="text_boxes_numeric" value="" /> 
		                            </td>
		                            <td><? echo create_drop_down( "brush_1", 60, $yes_no, "",1," -- Select--", '', "",$disabled,'' ); ?>
		                                <input type="text" id="brushseq_1"   name="brushseq_1" style="width:20px" class="text_boxes_numeric" value="" /> 
		                            </td>
		                            <td><? echo create_drop_down( "peach_1", 60, $yes_no, "",1," -- Select--", '', "",$disabled,'' ); ?>
		                                <input type="text" id="peachseq_1"   name="peachseq_1" style="width:20px" class="text_boxes_numeric" value="" /> 
		                            </td>
		                            <td><? echo create_drop_down( "yd_1", 60, $yes_no, "",1," -- Select--", '', "",$disabled,'' ); ?>
		                                <input type="text" id="ydseq_1"   name="ydseq_1" style="width:20px" class="text_boxes_numeric" value="" /> 
		                            </td>
								</tr>
								<?
							}
						}
						?>
					</tbody>
					</table>
					<table width="900" id="tbl_close">
						<tr>
							<td align="center">
								<input type="button" name="close" class="formbutton" value="Close" id="main_close"
								onClick="fnc_close();" style="width:100px"/>
							</td>
						</tr>
					</table>
				</fieldset>
			</form>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="sample_requisition_print3")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$cbo_template_id=$data[3];
	$path="../../";
	if(count($data)>3)
	{
		if($data[4]=='../')
		{
			$path=$data[4];
		}
	}
	//echo $path;
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$supplier_library=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );

	$company_img=sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");


	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$dealing_merchant_library=return_library_array( "select id,team_member_name from lib_mkt_team_member_info", "id", "team_member_name"  );
	$team_leader_arr=return_library_array( "select id,team_leader_name from lib_marketing_team", "id", "team_leader_name"  );

	$sample_library=return_library_array( "select id, sample_name from lib_sample", "id", "sample_name");
	$size_library=return_library_array( "select id, size_name from lib_size", "id", "size_name"  );
	$color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
	$season_arr=return_library_array( "select id,season_name from lib_buyer_season  where status_active =1 and is_deleted=0 ", "id", "season_name"  );
	$trims_group_lib=return_library_array( "select id,item_name from lib_item_group  where status_active =1 and is_deleted=0", "id", "item_name");
	//concate(buyer_name,'_',contact_person)
	$appDate=return_field_value("approved_date","approval_history","entry_form=25 and mst_id='$data[1]' order by id desc");
	$appBy=return_field_value("approved_by","approval_history","entry_form=25 and mst_id='$data[1]'");
	$user_arr=return_library_array( "select id, user_name from user_passwd", "id", "user_name"  );
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='sample_details_1' and file_type=1",'master_tble_id','image_location');




 ?>
 <style>
	#mstDiv {
	    margin:0px auto;
	    width:1130px;

	}
	#mstDiv @media print {

	   thead {display: table-header-group;}

	}

	 @media print{
			html>body table.rpt_table {
			margin-left:12px;
	  		}

		}
	</style>

		<div id="mstDiv">

	     <table width="1100" cellspacing="0" border="0"  style="font-family: Arial Narrow;margin-left: 20px;" >
	     <tr>
	     	<td rowspan="4" valign="top" width="300"><img width="150" height="80" src="<?=$path;?><? echo $company_img[0][csf("image_location")]; ?>" ></td>
	     	<td colspan="4" style="font-size: 24px;"><strong><b><? echo $company_library[$data[0]]; ?></b></strong></td>
	            <td width="200">
	            <?

	             $nameArray_approved=sql_select( "SELECT approved_by,approved_date from wo_non_ord_samp_booking_mst a, approval_history b where a.id=b.mst_id and a.booking_no='$data[2]' and b.entry_form=9 and a.status_active =1 and a.is_deleted=0 order by b.id desc ");
	             $approved_by= $user_arr[$nameArray_approved[0][csf("approved_by")]];
	             $approved_date= change_date_format($nameArray_approved[0][csf("approved_date")]);
	              ?>
	             </td>
	     </tr>




	        <tr>
	            <td colspan="5">
					<?

	                    $val=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
						//echo ($val[0][csf('plot_no')])?   $val[0][csf('plot_no')].',': "";
						echo ($val[0][csf('level_no')])?  $val[0][csf('level_no')].',': "";
						echo ($val[0][csf('road_no')])?   $val[0][csf('road_no')].',': "";
						echo ($val[0][csf('block_no')])?  $val[0][csf('block_no')].',': "";
						echo ($val[0][csf('city')])?      $val[0][csf('city')].',': "";
						echo ($val[0][csf('zip_code')])?  $val[0][csf('zip_code')].',': "";
						echo ($val[0][csf('province')])?  $val[0][csf('province')].',': "";
						echo($val[0][csf('country_id')])? $country_arr[$val[0][csf('country_id')]]: "";
						echo ($val[0][csf('email')])?    "</br>". $val[0][csf('email')].',': "</br>";
						echo($val[0][csf('website')])?    $val[0][csf('website')]: "";
						  $sql="SELECT id, requisition_number, requisition_number_prefix_num, style_ref_no, buyer_name, season, product_dept, dealing_marchant, agent_name, buyer_ref, bh_merchant, estimated_shipdate, team_leader, season_buyer_wise, remarks, quotation_id, sample_stage_id, requisition_date, material_delivery_date from sample_development_mst where  id='$data[1]' and entry_form_id=203 and  is_deleted=0  and status_active=1";
	 					  $dataArray=sql_select($sql);
	 					  $barcode_no=$dataArray[0][csf('requisition_number')];
	 					  if($dataArray[0][csf("sample_stage_id")]==1)
	 					  {
	 					  	 $job_lib=return_library_array( "SELECT a.id,min(b.shipment_date) as shipment_date  from wo_po_details_master  a,wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and a.company_name='$data[0]' GROUP BY a.id", "id", "shipment_date"  );
	 					  }

	 					   $sqls="SELECT style_desc,supplier_id,revised_no,buyer_req_no,source,team_leader,dealing_marchant from wo_non_ord_samp_booking_mst where  booking_no='$data[2]'  and  is_deleted=0  and status_active=1";
	 					  $dataArray_book=sql_select($sqls);
						// $style_desc= $dataArray_book[0][csf('style_desc')];


	                ?>
	            </td>

	        </tr>
	        <tr>
	            <td colspan="3" style="font-size:medium"><strong> <b>Sample Program Without Order</b></strong></td>
	             <td colspan="2" id="" width="250"><b>Approved By :<? echo $approved_by ?></b> </br><b>Approved Date :<? echo $approved_date ?></b> </td>

	        </tr>


	        </table>

	        <table width="1100" cellspacing="0" border="0" class="rpt_table" style="font-family: Arial Narrow;margin-left: 20px;" >
	        	<tr>
	        		<td colspan="4" align="left"><strong>System No. &nbsp;<? echo $dataArray[0][csf("requisition_number")]; ?> </strong></td>
	        		<td ><strong>Revise:</strong></td>
	        		<td ><? echo $dataArray_book[0][csf('revised_no')];?></td>
	        		<td colspan="2"></td>
	        	</tr>
	        	<tr>
	        	<td width="100"><strong>Booking No: </strong></td>
	        		<td width="130" align="left"><? echo $data[2];?></td>
	        		<td width="120"  align="left">&nbsp;&nbsp;<strong>Style Ref:</strong></td>
	        		<td width="110">&nbsp;<? echo $dataArray[0][csf('style_ref_no')];?></td>
	        		<td width="110"   align="left"><strong>Sample Sub Date:</strong></td>
	        		<td width="100" ><? echo change_date_format($dataArray[0][csf('material_delivery_date')]);?></td>
	        		<td width="110"   align="left"><strong>Style Desc:</strong></td>
	        		<td   ><? echo $dataArray_book[0][csf('style_desc')];?></td>
	        	</tr>
	        	<tr>
	        		<td width="100"><strong>Buyer Name: </strong></td>
	        		<td width="130" align="left"><? echo $buyer_library[$dataArray[0][csf('buyer_name')]];?></td>
	        		<td width="120" style="word-break:break-all;" align="left">&nbsp;&nbsp;<strong>Season:</strong></td>
	        		<td width="110">&nbsp;<? echo $season_arr[$dataArray[0][csf('season')]];?></td>
	        		<td width="110"><strong>BH Merchandiser:</strong></td>
	        		<td width="100"><? echo $dataArray[0][csf('bh_merchant')];?></td>
	        		<td width="110"><strong>Remarks/Desc:</strong></td>
	        		<td   style="word-wrap: break-word;word-break: break-all;" ><? echo $dataArray[0][csf('remarks')];?></td>

	        	</tr>
	        	<tr>
	        		<td width="100"   align="left"><strong>Buyer Ref:</strong></td>
	        		<td width="130" ><? echo $dataArray[0][csf('buyer_ref')];?></td>
	        		<td width="120"  >&nbsp;&nbsp;<strong>Product Dept:</strong></td>
	        		<td width="110" ><? echo $product_dept[$dataArray[0][csf('product_dept')]];?></td>
	        		<td width="110"  ><strong>Supplier</strong></td>
	        		<td width="100" ><? echo $supplier_library[$dataArray_book[0][csf('supplier_id')]];?></td>
	        		<td width="110"><strong>Est. Ship Date</strong></td>
	        		<td ><? echo change_date_format($dataArray[0][csf('estimated_shipdate')]); ?></td>

	        	</tr>
	            <tr>
	        		<td width="100"><strong>Team Leader</strong></td>
	        		<td width="130" ><? echo $team_leader_arr[$dataArray_book[0][csf('team_leader')]];?></td>
	        		<td width="120"  >&nbsp;&nbsp;<strong>Dealing Merchandiser:</strong></td>
	        		<td width="110" ><? echo $dealing_merchant_library[$dataArray_book[0][csf('dealing_marchant')]];?></td>
	        		<td width="110"  ><strong>Sample Stage</strong></td>
	        		<td width="100" ><? echo $sample_stage[$dataArray[0][csf('sample_stage_id')]];?></td>
	        		<td width="110">&nbsp;</td>
	        		<td >&nbsp;</td>

	        	</tr>
	        </table>

	        <table width="1100" cellspacing="0" border="0"   style="font-family: Arial Narrow;margin-left: 20px;" >
	         <tr>
	        	<td width="250" align="left" valign="top" colspan="2">
	            <table align="left" cellspacing="0" border="0" width="90%" >
	        	</table>
	        </td>
	        </tr>
	        <tr> <td colspan="6">&nbsp;</td></tr>
	        <tr>
	        	<td width="250" align="left" valign="top" colspan="2">
	        	<?
				 $sql_sample_dtls= "SELECT a.sample_name, a.article_no, a.sample_color from sample_development_dtls a , lib_color b  where a.status_active=1 and a.is_deleted=0 and a.entry_form_id=203  and sample_mst_id='$data[1]' and b.status_active=1 and a.status_active=1 and b.id=a.sample_color  group by a.sample_name,a.article_no,a.sample_color";

				foreach(sql_select($sql_sample_dtls) as $key=>$value)
				{
					if($sample_wise_article_no[$value[csf("sample_name")]][$value[csf("sample_color")]]=="")
					{
						$sample_wise_article_no[$value[csf("sample_name")]][$value[csf("sample_color")]]=$value[csf("article_no")];
					}
					else
					{
						if(!in_array($value[csf("article_no")], $sample_wise_article_no))
						{
							$sample_wise_article_no[$value[csf("sample_name")]][$value[csf("sample_color")]].= ', '.$value[csf("article_no")];
						}

					}
				}

			  $sql_fab="SELECT a.sample_name,a.gmts_item_id,b.color_id,b.contrast,c.finish_fabric as qnty,a.delivery_date,a.fabric_description,a.body_part_id, a.fabric_source,a.remarks_ra  ,a.gsm,a.dia, a.color_type_id,a.width_dia_id,a.uom_id,b.process_loss_percent,c.grey_fabric as grey_fab_qnty  from sample_development_fabric_acc a,sample_development_rf_color b, wo_non_ord_samp_booking_dtls c where a.id=b.dtls_id and  a.sample_mst_id=b.mst_id and a.id=c.dtls_id and c.fabric_color=b.fabric_color and c.gmts_color=b.color_id and c.dtls_id=b.dtls_id and c.style_id=a.sample_mst_id and c.style_id=b.mst_id and a.determination_id=c.lib_yarn_count_deter_id  and a.form_type=1 and b.qnty>0 and c.grey_fabric>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and a.sample_mst_id='$data[1]' and b.mst_id='$data[1]'  ";
				 $sql_fab_arr=array();
				 foreach(sql_select($sql_fab) as $vals)
				 {
					$article_no=rtrim($sample_wise_article_no[$vals[csf("sample_name")]][$vals[csf("color_id")]],',');
					$article_no=implode(",",array_unique(explode(",",$article_no)));
					$sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$vals[csf("contrast")]]["qnty"]+=$vals[csf("qnty")];
				 	$sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$vals[csf("contrast")]]["process_loss_percent"]=$vals[csf("process_loss_percent")];

				 	$sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$vals[csf("contrast")]]["grey_fab_qnty"]+=$vals[csf("grey_fab_qnty")];

				 	$sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$vals[csf("contrast")]]["delivery_date"] =change_date_format($vals[csf("delivery_date")]);

				 	$sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$vals[csf("contrast")]]["fabric_source"] =$vals[csf("fabric_source")];

				 	$sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$vals[csf("contrast")]]["uom_id"] =$vals[csf("uom_id")];
					$sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$vals[csf("contrast")]]["dia"] =$vals[csf("dia")];

				 	$sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$vals[csf("contrast")]]["width_dia_id"] =$vals[csf("width_dia_id")];

				 	$sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$vals[csf("contrast")]]["remarks"] =$vals[csf("remarks_ra")];
				 	$sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$vals[csf("contrast")]]["color_type_id"] =$vals[csf("color_type_id")];
				 }
				 $sample_item_wise_span=array(); $sample_item_wise_color_span=array();

			  foreach($sql_fab_arr as $article_no=>$article_data) 
	          {
				$article_no_span=0;
				foreach($article_data as $sample_type_id=>$sampleType_data) 
	        	{
				$sample_type_span=0;
				foreach($sampleType_data as $gmts_color_id=>$gmts_color_data)
	        	{
					$sample_span=0;
	        		foreach($gmts_color_data as $body_part_id=>$body_part_data)
	        		{
	        			
						//echo $gmts_color_id.'d';

	        			foreach($body_part_data as $fab_id=>$fab_desc_data)
	        			{
	        				//$kk=0;
	        				foreach($fab_desc_data as $colorType=>$colorType_data)
	        				{

	        					foreach($colorType_data as $gsm_id=>$gsm_data)
	        					{
	        						foreach($gsm_data as $dia_id=>$dia_data)
	        						{
									   foreach($dia_data as $dia_type_id=>$diatype_data)
	        						   {

	        							foreach($diatype_data as $contrast_id=>$value)
	        							{
	        								$sample_span++;$sample_type_span++;$article_no_span++;
	        								//$kk++;

	        							}
											$article_wise_span[$article_no]=$article_no_span;
											$sample_item_wise_span[$article_no][$sample_type_id]=$sample_type_span;
											$sample_item_wise_color_span[$article_no][$sample_type_id][$gmts_color_id]=$sample_span;
									  }
	        						}
	        					}
	        				}
	        			}

	        		  }
					 }

	        		}
				}

				?>
				<table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all">
					<thead>
					<tr>
						<th colspan="19">Required Fabric</th>
					</tr>
						<tr>
							<th width="30">Sl</th>
							<th width="90">ALT / [C/W]</th>
							<th width="110">Sample Type</th>
							<th width="80">Gmt Color</th>
							<th width="80">Fab. Deli Date</th>
							<th width="120">Body Part</th>
							<th width="200">Fabric Desc & Composition</th>
							<th width="80">Color Type</th>
							<th width="80">Fab.Color</th>
							<th width="40">Item Size</th>
							<th width="55">GSM</th>
							<th width="55">Dia</th>
							<th width="60">Width/Dia</th>
							<th width="40">UOM</th>
							<th width="60">Grey Qnty</th>
							<th width="40">P. Loss</th>
							<th width="80">Fin Fab Qnty</th>
							<th width="80">Fabric Source</th>
							<th width="80">Remarks</th>

						</tr>
					</thead>
					<tbody>
						<?
						$p=1;
						$total_finish=0;
						$total_grey=0;
						$total_process=0;
			 foreach($sql_fab_arr as $article_no=>$article_data) 
	         {
				$aa=0;
				foreach($article_data as $sample_type_id=>$sampleType_data) 
	        	{
				$nn=0;
				foreach($sampleType_data as $gmts_color_id=>$gmts_color_data)
	        	{
					$cc=0;
	        		foreach($gmts_color_data as $body_part_id=>$body_part_data)
	        		{
	        			foreach($body_part_data as $fab_id=>$fab_desc_data)
	        			{
	        				//$kk=0;
	        				foreach($fab_desc_data as $colorType=>$colorType_data)
	        				{

	        					foreach($colorType_data as $gsm_id=>$gsm_data)
	        					{
	        						foreach($gsm_data as $dia_id=>$dia_data)
	        						{

	        							foreach($dia_data as $dia_type=>$diatype_data)
	        							{
											foreach($diatype_data as $contrast_id=>$value)
	        							    {

															 
														?>
														<tr>																
															<?
															if($aa==0)
															{
																?>
	                                                            <td  rowspan="<? echo $article_wise_span[$article_no];?>"  align="center" style="word-wrap: break-word;word-break: break-all;"><? echo $p;$p++;?></td>
	                                                            <td   rowspan="<? echo $article_wise_span[$article_no];?>" align="center"><? echo $article_no;?></td>
	                                                            <?
															}
															if($nn==0)
															{
																?>
																
																<td   rowspan="<? echo $sample_item_wise_span[$article_no][$sample_type_id];?>"  align="center"><? echo $sample_library[$sample_type_id]; ?></td>
																
																<?
																
															}
															if($cc==0)
															{
															 ?>
	                                                         <td   align="center" rowspan="<? echo $sample_item_wise_color_span[$article_no][$sample_type_id][$gmts_color_id];?>"><? echo $color_library[$gmts_color_id];?> </td>
	                                                          <td   rowspan="<? echo $sample_item_wise_color_span[$article_no][$sample_type_id][$gmts_color_id];?>" align="center" ><? echo $value["delivery_date"];?> </td>
	                                                         <?
	                                                        } ?>

															
															 <td width="120"  align="center"><? echo $body_part[$body_part_id];?></td>
															 <td  align="center"><? echo $fab_id;?></td>
															 <td  align="center"> <? echo $color_type[$colorType]; ?></td>
															 <td  align="center"><? echo $contrast_id; ?></td>
															 <td  align="center"><? echo $value["item_size"]; ?></td>
															 <td  align="center"><? echo $gsm_id; ?></td>
															 <td  align="center"><? echo $value["dia"]; ?></td>
															 <td  align="center"><? echo $fabric_typee[$dia_type]; ?></td>
															 <td   align="center"><? echo $unit_of_measurement[$value["uom_id"]];?></td>
															 <td align="right"><? echo number_format($value["grey_fab_qnty"],2);?></td>
															 <td align="right"><? echo $value["process_loss_percent"];?></td>
															 <td align="right"><? echo number_format($value["qnty"],2);?></td>
															 <td align="center"><? echo $fabric_source[$value["fabric_source"]];?></td>
															 <td  align="center"><? echo $value["remarks"];?></td>
														</tr>


														<?
														$nn++;$cc++;$aa++;
			        									//$i++;
														$total_finish +=$value["qnty"];
														$total_grey +=$value["grey_fab_qnty"];
														$total_process +=$value["process_loss_percent"];
													}
												}
											}
										}
									}
								}
							  }
							}
						}
			 		}

						?>

						<tr>
							<th colspan="14" align="right"><b>Total</b></th>
							<th width="80" align="right"><? echo number_format($total_grey,2);?></th>
							<th width="40" align="right">&nbsp;</th>
							<th width="60" align="right"><? echo number_format($total_finish,2);?></th>
							<th width="80" colspan="2"> </th>
						</tr>
					</tbody>
				</table>
				<br/>



	<?

				$sample_color_arr=return_library_array( "select id, sample_color from sample_development_dtls", "id", "sample_color"  );
	                      $sql_qry="SELECT id,sample_mst_id,sample_name,gmts_item_id,smv,article_no,sample_color,sample_prod_qty,submission_qty,delv_start_date,delv_end_date,sample_charge,sample_curency,sent_to_buyer_date,comments from sample_development_dtls where status_active =1 and is_deleted=0 and entry_form_id=203 and sample_mst_id='$data[1]' order by id asc";
						    $sql_qry_color="SELECT a.id,a.sample_mst_id,a.sample_name,a.gmts_item_id,a.smv,a.article_no,a.sample_color,a.sample_prod_qty,a.submission_qty,a.delv_start_date,a.delv_end_date,a.sample_charge,a.sample_curency,a.sent_to_buyer_date,a.comments,c.dtls_id,c.size_id,c.bh_qty,c.self_qty,c.test_qty,c.plan_qty,c.dyeing_qty from sample_development_dtls a,sample_development_size c where a.id=c.dtls_id and  a.status_active =1 and a.is_deleted=0 and a.entry_form_id=203 and a.sample_mst_id='$data[1]' order by a.id asc";
						 $size_type_arr=array(1=>"bh_qty",2=>"Self Qty",3=>"Test qty",4=>"Plan Qty",5=>"Dyeing Qty");
						 $color_size_arr=array();
						  foreach(sql_select($sql_qry_color) as $vals)
						 {
								if($vals[csf("bh_qty")]>0)
								{
								$color_size_arr[1][$vals[csf("size_id")]]='Bh Qty';
								$bh_qty=$vals[csf("bh_qty")];
								$color_size_dtls_qty_arr[1][$vals[csf("id")]][$vals[csf("size_id")]]=$bh_qty;
								}
								if($vals[csf("self_qty")]>0)
								{
								$color_size_arr[2][$vals[csf("size_id")]]='self qty';
								$color_size_dtls_qty_arr[2][$vals[csf("id")]][$vals[csf("size_id")]]=$vals[csf("self_qty")];
								}
								if($vals[csf("test_qty")]>0)
								{
								$color_size_arr[3][$vals[csf("size_id")]]='test qty';
								$color_size_dtls_qty_arr[3][$vals[csf("id")]][$vals[csf("size_id")]]=$vals[csf("test_qty")];
								}
								if($vals[csf("plan_qty")]>0)
								{
								$color_size_arr[4][$vals[csf("size_id")]]='plan qty';
								//$size_plan_arr[$vals[csf("size_id")]]=$vals[csf("size_id")];
								$color_size_dtls_qty_arr[4][$vals[csf("id")]][$vals[csf("size_id")]]=$vals[csf("plan_qty")];

								}
								if($vals[csf("dyeing_qty")]>0)
								{
								$color_size_arr[5][$vals[csf("size_id")]]='Dyeing qty';
								$color_size_dtls_qty_arr[5][$vals[csf("id")]][$vals[csf("size_id")]]=$vals[csf("dyeing_qty")];

								}

							}
							$tot_row=count($color_size_arr);
							$result=sql_select($sql_qry);

	?>


	            	<table align="left" cellspacing="0" border="1" width="100%" class="rpt_table" rules="all">
	            	<thead>
	            		<tr>
	                            <td width="150" colspan="<? echo 12+$tot_row;?>" align="center"><strong>Sample Details</td>
	                        </tr>
	                        <tr>
									<th width="30" rowspan="2" align="center">Sl</th>
									<th width="100" rowspan="2" align="center">Sample Name</th>
									<th width="120" rowspan="2" align="center">Garment Item</th>

									<th width="55" rowspan="2" align="center">ALT / [C/W]</th>
									<th width="70" rowspan="2" align="center">Color</th>
	                                <?
									$tot_row_td=0;
	                                foreach($color_size_arr as $type_id=>$val)
									{ ?>
										<th width="45" align="center" colspan="<? echo count($val);?>"> <?
	                                 		  echo  $size_type_arr[$type_id];
										?></th>
	                                    <?

									}
									?>
									<th rowspan="2" width="55" align="center">Total</th>
									<th rowspan="2" width="55" align="center">Submn Qty</th>
									<th rowspan="2"  width="70" align="center">Buyer Submisstion Date</th>
									<th rowspan="2"  width="70" align="center">Remarks</th>
	                         </tr>
	                         <tr>
	                         	<?
	                            foreach($color_size_arr as $type_id=>$data_size)
								{
									foreach($data_size as $size_id=>$data_val)
									{
									$tot_row_td++;
									?>
										<th width="40" align="center"><? echo $size_library[$size_id]; ?></th>
										<?
									}
	                         	}

	                         	?>
	                         </tr>

	            	</thead>
	                    <tbody>

	                        <?

	 						$i=1;$k=0;
	 						$gr_tot_sum=0;
	 						$gr_sub_sum=0;
							foreach($result as $row)
							{
								$dtls_ids=$row[csf('id')];
								 //$size_select=sql_select("SELECT  size_id,total_qty  from sample_development_size where  mst_id='$data[1]' and status_active=1 and is_deleted=0 and dtls_id='$dtls_ids' ");
	 							$prod_sum=$prod_sum+$row[csf('sample_prod_qty')];
								$sub_sum=$sub_sum+$row[csf('submission_qty')];

							?>
	                        <tr>
	                            <?
	 							$k++;
								?>
	                            <td  align="center"><? echo $k;?></td>
	                            <td  align="left"><? echo $sample_library[$row[csf('sample_name')]];?></td>
	                            <td  align="left"><? echo $garments_item[$row[csf('gmts_item_id')]];?></td>

	                            <td   align="left"><? echo $row[csf('article_no')];?></td>
	                            <td width="70" align="left"><? echo $color_library[$row[csf('sample_color')]];?></td>


	                            <?
	                            $total_sizes_qty=0;
	                            $total_sizes_qty_subm=0;
	                          	foreach($color_size_arr as $type_id=>$data_size)
								{
									foreach($data_size as $size_id=>$data_val)
									{
									$size_qty=$color_size_dtls_qty_arr[$type_id][$dtls_ids][$size_id];
	                            	?>
	                            	<td align="right"><? echo $size_qty; ?></td>
	                            	<?
										if($type_id==1)
										{
										$total_sizes_qty_subm+=$size_qty;
										}
										$total_sizes_qty+=$size_qty;
									}
	                            }
	                            ?>
	                            <td align="right"><? echo $total_sizes_qty;?></td>
	                            <td align="right"><? echo $total_sizes_qty_subm;?></td>
	                            <td   align="left"><? echo change_date_format($row[csf('sent_to_buyer_date')]);?> </td>
	                            <td   align="left"><? echo $row[csf('comments')];?> </td>
	                            <?
	                            $gr_tot_sum+=$total_sizes_qty;
	 							$gr_sub_sum+=$total_sizes_qty_subm;
	                        }
							?>
	                        </tr>
								<tr>
										<td colspan="<? echo 5+$tot_row_td; ?>" align="right"><b>Total</b></td>
	 									<td   align="right"><b><? echo number_format($gr_tot_sum,2);?> </b></td>
	 									<td  align="right"><b><? echo number_format($gr_sub_sum,2);?> </b></td>
										<td colspan="2"></td>
								</tr>
	                    </tbody>
	                    <tfoot>
	                     </tfoot>
	               </table>
	             </td>
        </tr>
         <tr> <td colspan="6">&nbsp;</td></tr>
        <tr>
        	<td width="250" align="left" valign="top" colspan="2">

             </td>
        </tr>

        <tr>
        	<td width="250" align="left" valign="top" colspan="2">
            	<table align="left" cellspacing="0" border="1" width="100%" class="rpt_table" rules="all">
            	<thead>
            		<tr>
                            <td width="150" colspan="10" align="center"><strong>Required Accessories</td>
                        </tr>
                        <tr>
								<th width="30" align="center">Sl</th>
								<th width="100" align="center">Sample Name</th>
								<th width="120" align="center">Garment Item</th>
								<th width="100" align="center">Trims Group</th>
								<th width="100" align="center">Description</th>
								<th width="100" align="center">Supplier</th>
								<th width="100" align="center">Brand/Supp.Ref</th>
 								<th width="30" align="center">UOM</th>
								<th width="30" align="center">Req/Dzn </th>
								<th width="30" align="center">Req/Qty </th>
								<th width="80" align="center">Acc.Sour. </th>
								<th width="100" align="center">Acc Delivery Date </th>
								<th width="80" align="center">Remarks </th>
                         </tr>
            	</thead>
                    <tbody>


                        <?
					   $sql_qryA="SELECT id,sample_mst_id,sample_name_ra,gmts_item_id_ra,trims_group_ra,description_ra,brand_ref_ra,uom_id_ra,req_dzn_ra,req_qty_ra,remarks_ra,delivery_date,supplier_id,fabric_source from sample_development_fabric_acc where status_active =1 and is_deleted=0 and form_type=2 and sample_mst_id='$data[1]' order by id asc";

						$resultA=sql_select($sql_qryA);
 						$i=1;$k=0;
 						$req_dzn_ra=0;
 						$req_qty_ra=0;
						foreach($resultA as $rowA)
						{
							$req_dzn_ra=$req_dzn_ra+$rowA[csf('req_dzn_ra')];
							$req_qty_ra=$req_qty_ra+$rowA[csf('req_qty_ra')];

						?>
                        <tr>
                            <?
 							$k++;
							?>
                            <td  align="center"><? echo $k;?></td>
                            <td  align="left"><? echo $sample_library[$rowA[csf('sample_name_ra')]];?></td>
                            <td  align="left"><? echo $garments_item[$rowA[csf('gmts_item_id_ra')]];?></td>
                            <td  align="left"><? echo $trims_group_lib[$rowA[csf('trims_group_ra')]];?></td>
                            <td  align="left"><? echo $rowA[csf('description_ra')];?></td>
                            <td  align="left"><? echo $supplier_library[$rowA[csf('supplier_id')]];?></td>
                            <td  align="left"><? echo $rowA[csf('brand_ref_ra')];?></td>
                             <td  align="center"><? echo $unit_of_measurement[$rowA[csf('uom_id_ra')]];?></td>
                            <td  align="right"><? echo $rowA[csf('req_dzn_ra')];?></td>
                            <td  align="right"><? echo $rowA[csf('req_qty_ra')];?></td>
                            <td  align="left"><? echo $fabric_source[$rowA[csf('fabric_source')]];?></td>
                            <td  align="left"><? echo change_date_format($rowA[csf('delivery_date')]);?></td>
                            <td  align="left"><? echo $rowA[csf('remarks_ra')];?></td>

                            <?
                        }

						?>




                        </tr>

                          <tr>
									<td colspan="8" align="center"><b>Total </b></td>
									<!-- <td align="right"><b><? echo number_format($req_dzn_ra,2);?> </b></td> -->
  									<td align="right"  ><b><? echo number_format($req_qty_ra,2);?> </b></td>
  									<td>&nbsp;</td>

 							</tr>


                    </tbody>
                    <tfoot>

                    </tfoot>
               </table>
             </td>
        </tr>
         <tr> <td colspan="6">&nbsp;</td></tr>
         <tr>
        	<td width="250" align="left" valign="top" colspan="2">
            	<table align="left" cellspacing="0" border="1" width="100%" class="rpt_table" rules="all">
            	<thead>
            		<tr>
                            <td width="150" colspan="6" align="center"><strong>Required Emebellishment</td>
                        </tr>
                        <tr>
                        	<th width="30" align="center">Sl</th>
                        	<th width="100" align="center">Sample Name</th>
                        	<th width="110" align="center">Garment Item</th>
                        	<th width="110" align="center">Body Part</th>
                        	<th width="100" align="center">Supplier</th>
                        	<th width="60" align="center">Name</th>
                        	<th width="70" align="center">Type</th>
                        	<th width="100" align="center">Emb.Del.Date</th>
                        	<th width="70" align="center">Remarks</th>

                         </tr>
            	</thead>
                    <tbody>
                        <?
                        $sql_qry="SELECT id,sample_mst_id,sample_name_re,gmts_item_id_re,name_re,type_re,remarks_re,body_part_id,delivery_date,supplier_id from sample_development_fabric_acc where sample_mst_id='$data[1]' and form_type=3 and is_deleted=0  and status_active=1 order by id asc";

						$result=sql_select($sql_qry);
 						$k=0;
 						$type_array=array(1=>$emblishment_print_type,2=>$emblishment_embroy_type,3=>$emblishment_wash_type,4=>$emblishment_spwork_type,5=>$emblishment_gmts_type);
						foreach($result as $row)
						{

						?>
                        <tr>
                            <?
 							$k++;
							?>
                            <td  align="center"><? echo $k;?></td>
                            <td  align="left"><? echo $sample_library[$row[csf('sample_name_re')]];?></td>
                            <td  align="left"><? echo $garments_item[$row[csf('gmts_item_id_re')]];?></td>
                            <td  align="left"><? echo $body_part[$row[csf('body_part_id')]];?></td>
                            <td  align="left"><? echo $supplier_library[$row[csf('supplier_id')]];?></td>
                            <td  align="left"><? echo $emblishment_name_array[$row[csf('name_re')]];?></td>
                            <td  align="left">
                            <?
                            if($row[csf('name_re')]==1)
                            {
                          	  echo $emblishment_print_type[$row[csf('type_re')]];
                          	}
                          	if($row[csf('name_re')]==2)
                            {
                          	  echo $emblishment_embroy_type[$row[csf('type_re')]];
                          	}
                          	if($row[csf('name_re')]==3)
                            {
                          	  echo $emblishment_wash_type[$row[csf('type_re')]];
                          	}
                          	if($row[csf('name_re')]==4)
                            {
                          	  echo $emblishment_spwork_type[$row[csf('type_re')]];
                          	}
                          	if($row[csf('name_re')]==5)
                            {
                          	  echo $emblishment_gmts_type[$row[csf('type_re')]];
                          	}
                            ?>

                            </td>
                            <td  align="left"><? echo change_date_format($row[csf('delivery_date')]);?></td>
                            <td  align="left"><? echo $row[csf('remarks_re')];?></td>
                            <?
                        }
						?>
                        </tr>
                    </tbody>
                    <tfoot>

                    </tfoot>
               </table>

               <br>
               <table>
               		<tr>
               			<td>
   				            <table  style="margin-top: 10px;" class="rpt_table" width="600" align="left" border="1" cellpadding="0" cellspacing="0" rules="all">
   				                <caption> <b> Yarn Required Summary </b> </caption>
   				                	<thead>
   				                    	<tr align="center">
   				                        	<th width="40">Sl</th>
   				                        	<th>Yarn Desc.</th>
   				                             <th>Req. Qty</th> 
   				                        </tr>
   				                    </thead>
   				                    <tbody>
   				                    <?
   									$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count"  );
   									$lib_supllier_arr=return_library_array( "select supplier_id,booking_no from wo_non_ord_samp_booking_mst where booking_no='$data[2]' and entry_form_id=140", "booking_no", "supplier_id"  );
   								//	echo  "select supplier_id,booking_no from wo_non_ord_samp_booking_mst where booking_no='$data[2]' and entry_form_id=140";
   									$tot_req_qty=0;//sample_development_mst
   									$data_array=sql_select("select b.booking_no,b.determin_id,b.count_id,b.copm_one_id,b.percent_one,b.type_id,b.cons_qnty from  sample_development_yarn_dtls b,sample_development_fabric_acc a where a.sample_mst_id=b.mst_id and a.determination_id=b.determin_id and b.status_active=1 and a.status_active=1 and b.mst_id='$data[1]' and a.form_type=1 group by b.booking_no, b.determin_id, b.count_id, b.copm_one_id, b.percent_one, b.type_id, b.cons_qnty");
   									
   									//echo "select b.booking_no,b.determin_id,b.count_id,b.copm_one_id,b.percent_one,b.type_id,b.cons_qnty from  sample_development_yarn_dtls b,sample_development_fabric_acc a where a.sample_mst_id=b.mst_id and a.determination_id=b.determin_id and b.status_active=1 and a.status_active=1  and b.mst_id='$data[1]' and a.form_type=1";
   								
   									if ( count($data_array)>0)
   									{
   										$l=1;
   										foreach( $data_array as $key=>$row )
   										{
   											$yarn_des=$lib_yarn_count[$row[csf("count_id")]].','.$composition[$row[csf("copm_one_id")]].','.$row[csf("percent_one")].'%,'.$yarn_type[$row[csf("type_id")]];
   											?>
   				                            	<tr>
   				                                    <td> <? echo $l;?> </td>
   				                                    <td> <? echo $yarn_des; ?> </td>
   				                                    <td align="right"> <? echo number_format($row[csf("cons_qnty")],2); ?> </td>
   				                                </tr>
   				                            <?
   				                            $l++;
   											$tot_req_qty+=$row[csf("cons_qnty")];
   										}
   									}

   									?>
   				                    <tr>
   										<th  colspan="2" align="right"><b>Total</b></th>
   										<th  align="right"><? echo number_format($tot_req_qty,2);?></th>
   									</tr>
   				                </tbody>
   				            </table>
               			</td>
               			<td width="300">
               				<?php 

               					$sql_image=sql_select("select image_location from common_photo_library where master_tble_id='$data[2]' ");

               				 ?>
               				 <img src="<?=$path;?><?php echo $sql_image[0][csf('image_location')];?>" width="200" height="150" style="justify-content: center;text-align: center;float: right;">
               			</td>
               		</tr>
               </table>
               	
            
                <br>
                 <br>

               	<table  style="margin-top: 10px;" class="rpt_table" width="600" align="left" border="1" cellpadding="0" cellspacing="0" rules="all">
                	<thead>
                    	<tr>
                        	<th align="left" width="40">Sl</th>
                        	<th align="left" >Special Instruction</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$data_array=sql_select("select id, terms from  wo_booking_terms_condition where entry_form=140 and booking_no='$data[2]'");
					if ( count($data_array)>0)
					{
						$l=1;
						foreach( $data_array as $key=>$row )
						{

							?>
                            	<tr  align="">
                                    <td> <? echo $l;?> </td>
                                    <td> <? echo $row[csf("terms")]; ?> </td>
                                </tr>
                            <?
                            $l++;
						}
					}

					?>
                </tbody>
            </table>
             </br>


             </td>
        </tr>
         <tr> <td colspan="6">&nbsp;</td></tr>

        <tr>
        	<td width="810" align="left" valign="top" colspan="2" >
            	<table align="left" cellspacing="0" width="810" class="rpt_table" >
                	<tr>
                    	<td colspan="6">
							<?

								$user_id=$_SESSION['logic_erp']['user_id'];
								$user_arr=return_library_array( "select id, USER_NAME from user_passwd where id=$user_id", "id", "USER_NAME");
								$prepared_by = $user_arr[$user_id];
	                              //echo signature_table(134, $data[0], "810px");
							  	echo signature_table(134, $data[0], "1080px",$cbo_template_id,$padding_top = 70,$prepared_by);
                            ?>
                        </td>

                    </tr>

                </table>
            </td>
        </tr>
        </table>

    </div>
   <script type="text/javascript" src="../../js/jquery.js"></script>
   <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
   <script>

	function fnc_generate_Barcodes( valuess, img_id )
	{
		var value = valuess;//$("#barcodeValue").val();
		var btype = 'code39';//$("input[name=btype]:checked").val();
		var renderer ='bmp';// $("input[name=renderer]:checked").val();
		var settings = {
		  output:renderer,
		  bgColor: '#FFFFFF',
		  color: '#000000',
		  barWidth: 1,
		  barHeight: 60,
		  moduleSize:5,
		  posX: 10,
		  posY: 20,
		  addQuietZone: 1
		};
		$("#"+img_id).html('11');
		 value = {code:value, rect: false};
		$("#"+img_id).show().barcode(value, btype, settings);
	}
   </script>
   <script type="text/javascript">
   	fnc_generate_Barcodes('<? echo $barcode_no; ?>','barcode_img_id');
   </script>

 <?
 exit();
}
$color_library=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name" );
if($action=="dtm_popup")
{
	echo load_html_head_contents("DTM Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	//echo $req_id.'SD';
	?>
<script>

function fnc_trims_popup(page_link,title,i)
{
	var txt_req_id=$('#txt_req_id').val();
	var booking_no=$('#txt_booking_no').val();
	var txt_req_no=$('#txt_req_no').val();
	var fabric=$('#fabric_'+i).val();
	var color=$('#color_'+i).val();
	var fabric_cost_id=$('#fabric_cost_id_'+i).val();
//alert(txt_req_id);

	if(booking_no=='')
	{
		alert('Booking  Not Found.');
		$('#txt_booking_no').focus();
		return;
	}

	page_link=page_link+'&txt_req_no='+txt_req_no+'&booking_no='+booking_no+'&txt_req_id='+txt_req_id+'&fabric='+fabric+'&color='+color+'&fabric_cost_id='+fabric_cost_id+'&index='+i;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=300px,center=1,resize=1,scrolling=0','../../')

}
</script>
<?
  
//echo $job_no."_".$booking_no."_".$selected_no;
//$sql=sql_select("select id,booking_id,booking_no,pre_cost_fabric_cost_id,fabric_color,precost_trim_cost_id,item_group,qty from sample_dev_dye_to_match where booking_no='$booking_no'");
$dtm_arr=array();
$sql=sql_select("select sample_req_fabric_cost_id,fabric_color,sum(qty) as qty  from sample_dev_dye_to_match where booking_no='$booking_no' and status_active=1 and is_deleted=0 group by sample_req_fabric_cost_id,fabric_color");
foreach($sql as $row){
	$dtm_arr[$row[csf('sample_req_fabric_cost_id')]][$row[csf('fabric_color')]]=$row[csf('qty')];
}
//fabric_description

 $trims_matches_sql=sql_select("select a.id, a.body_part_id, a.fabric_description,a.gsm as gsm_weight, c.fabric_color as fabric_color_id, min(c.id) as cid, sum(c.finish_fabric) as fin_fab_qnty, sum(c.grey_fabric) as grey_fab_qnty FROM sample_development_fabric_acc a, sample_development_rf_color b, wo_non_ord_samp_booking_dtls c
			WHERE a.id=b.dtls_id and  a.id=c.dtls_id and  c.style_id=a.sample_mst_id  and a.determination_id=c.lib_yarn_count_deter_id  and a.form_type=1 and c.booking_no ='$booking_no' and c.status_active=1 and  c.status_active=1  and a.status_active=1 and c.is_deleted=0
			group by a.id, a.body_part_id,a.fabric_description,a.gsm, c.fabric_color order by a.id, cid ");
?>



</head>
<body>
<div align="center" style="width:100%;" >
<input type="hidden" id="txt_req_id" name="txt_req_id" value="<? echo $update_id;  ?>"/>
<input type="hidden" id="txt_req_no" name="txt_req_no" value="<? echo $req_no;  ?>"/>
<input type="hidden" id="txt_booking_no" name="txt_booking_no" value="<? echo $booking_no;  ?>"/>
 
	<table width="800" cellspacing="0" class="rpt_table" border="0" id="tbl_trims_dyes_match1" rules="all">
	<thead>
	  <tr>
		<th width="40">S/L</th>
		<th width="300">Fabric Driscription</th>
		<th width="150">Color</th>
		<th width="100">Fabric Qnty.</th>
		<th width="100">Trims</th>
	  </tr>
	</thead>
	<tbody>
	<?

	$i=1;
	foreach($trims_matches_sql as $row)
	 {
	 ?>
	  <tr>
	  	<td width="40"><? echo $i; ?></td>
	  	<td width="300">
        <input class="text_boxes" type="text" style="width:300px;"  name="fabric_<? echo $i; ?>" id="fabric_<? echo $i; ?>" value="<? echo $body_part[$row[csf('body_part_id')]].",".$row[csf('fabric_description')].",".$row[csf('gsm_weight')];?>" readonly/>
         <input class="text_boxes" type="hidden" style="width:300px;"  name="fabric_cost_id_<? echo $i; ?>" id="fabric_cost_id_<? echo $i; ?>" value="<? echo $row[csf('id')];?>" readonly/>
        </td>
	  	<td width="150">
        <? echo $color_library[$row[csf('fabric_color_id')]];?>
        <input class="text_boxes" type="hidden" style="width:150px;"  name="color_<? echo $i; ?>" id="color_<? echo $i; ?>" value="<? echo $row[csf('fabric_color_id')];?>" readonly/>
        </td>
	  	<td width="100" align="right">
		<? echo $row[csf('fin_fab_qnty')];?>
        </td>
	  	<td width="100"><input class="text_boxes" type="text" style="width:100px;"  name="trims_<? echo $i; ?>" id="trims_<? echo $i; ?>" value="<? echo $dtm_arr[$row[csf('id')]][$row[csf('fabric_color_id')]] ?>" onDblClick="fnc_trims_popup('sample_requisition_with_booking_controller.php?action=trims_popup','Trims Item',<? echo $i ?>)" readonly/></td>
	  </tr>
	  <? $i++;

	  } ?>
	</tbody>
	</table>
</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}
if($action=="trims_popup")
{
	echo load_html_head_contents("DTM Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
<script>
function fnc_fabric_dye_to_match( operation )
{

	var txt_req_no=$('#txt_req_no').val();
	var booking_no=$('#txt_booking_no').val();
	var txt_req_id=$('#txt_req_id').val();
	var fabric=$('#fabric').val();
	var color=$('#color').val();
	var fabric_cost_id=$('#fabric_cost_id').val();
	var index=$('#index').val();

	    var row_num=$('#tbl_trims_dyes_match tbody tr').length;
		var data_all="";
		for (var i=1; i<=row_num; i++)
		{

			data_all=data_all+get_submitted_data_string('trim_group_'+i+'*pre_cost_trim_cost_id_'+i+'*dyeqty_'+i+'*color_'+i,"../../../",i);

		}
		//alert(data_all);
		var data="action=save_update_delete_dye_to_match&operation="+operation+'&total_row='+row_num+data_all+'&booking_no='+booking_no+'&fabric='+fabric+'&color='+color+'&fabric_cost_id='+fabric_cost_id+'&txt_req_id='+txt_req_id;
		//alert(data);return;
		freeze_window(operation);
		http.open("POST","sample_requisition_with_booking_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_fabric_booking_terms_condition_reponse;
}

function fnc_fabric_booking_terms_condition_reponse()
{

	if(http.readyState == 4)
	{
	        var reponse=trim(http.responseText).split('**');
			 if(trim(reponse[0])=='approved')
			 {
				 alert("This booking is approved");
				 release_freezing();
				 return;
			 }

			if(trim(reponse[0])=='papproved'){
				alert("This booking is Partial approved");
				release_freezing();
				return;
			}

			if (reponse[0].length>2) reponse[0]=10;
			release_freezing();
			if(reponse[0]==0 || reponse[0]==1)
			{
				var index=$('#index').val();
				parent.document.getElementById('trims_'+index).value=reponse[1];
				parent.emailwindow.hide();
			}
	}
}
</script>
<?
$lib_item_group_arr=return_library_array( "select item_name, id from lib_item_group where item_category=4 and is_deleted=0  and  status_active=1 order by item_name", "id", "item_name");

$dtm_arr=array();
$dtm_arr_item_color=array();
$sql=sql_select("select sample_req_fabric_cost_id,fabric_color,sample_req_trim_cost_id,item_color,sum(qty) as qty from sample_dev_dye_to_match where booking_no='$booking_no' and sample_req_fabric_cost_id='$fabric_cost_id' and status_active=1 and is_deleted=0 group by sample_req_fabric_cost_id,fabric_color,item_color,sample_req_trim_cost_id");

foreach($sql as $row){
	$dtm_arr[$row[csf('sample_req_fabric_cost_id')]][$row[csf('fabric_color')]][$row[csf('sample_req_trim_cost_id')]]=$row[csf('qty')];
	$dtm_arr_item_color[$row[csf('sample_req_fabric_cost_id')]][$row[csf('fabric_color')]][$row[csf('sample_req_trim_cost_id')]]=$row[csf('item_color')];
}
/*$trims_matches_sql=sql_select("select a.id,a.job_no,a.trim_group,a.description,a.cons_uom FROM wo_pre_cost_trim_cost_dtls a, wo_pre_cost_trim_co_cons_dtls b,  wo_booking_dtls c
			WHERE
			a.job_no=b.job_no and
			a.job_no=c.job_no and
			a.id=b.wo_pre_cost_trim_cost_dtls_id and
			b.po_break_down_id=c.po_break_down_id and
			c.booking_no ='$booking_no' and
			b.po_break_down_id in($selected_no)
			and a.status_active=1 and a.is_deleted=0
			and c.status_active=1 and c.is_deleted=0
			group by a.id,a.job_no,a.trim_group,a.description,a.cons_uom");*/
			  $trims_matches_sql=sql_select("select a.id,a.sample_mst_id, a.trims_group_ra as trim_group, a.fabric_description as description,a.uom_id_ra as cons_uom,sum(a.req_qty_ra) as req_qty_ra,a.description_ra   FROM sample_development_fabric_acc a,  wo_non_ord_samp_booking_dtls c
			WHERE c.style_id=a.sample_mst_id  and a.form_type=2 and c.booking_no ='$booking_no' and c.status_active=1 and  c.status_active=1  and a.status_active=1 and c.is_deleted=0
			group by a.id,a.sample_mst_id, a.trims_group_ra,a.fabric_description,a.uom_id_ra,a.description_ra  order by a.id ");

	/*$condition= new condition();
	if(str_replace("'","",$job_no) !=''){
	$condition->job_no("='$job_no'");
	}
	if(str_replace("'","",$selected_no) !=''){
		$condition->po_id("in($selected_no)");
	}
	$condition->init();
	$trim= new trims($condition);
	//echo $trim->getQuery();
	$totalqtyarray_arr=$trim->getQtyArray_by_jobAndPrecostdtlsid();*/
?>
</head>
<body>
<div align="center" style="width:100%;" >
 <? echo load_freeze_divs ("../../../",$permission);  ?>

<fieldset>
<form id="dtm_1">
<input type="hidden" id="txt_req_no" name="txt_req_no" value="<? echo $txt_req_no;  ?>"/>
<input type="hidden" id="txt_booking_no" name="txt_booking_no" value="<? echo $booking_no;  ?>"/>
<input type="hidden" id="txt_req_id" name="txt_req_id" value="<? echo $txt_req_id;  ?>"/>
<input type="hidden" id="fabric" name="fabric" value="<? echo $fabric;  ?>"/>
<input type="hidden" id="color" name="color" value="<? echo $color;  ?>"/>
<input type="hidden" id="fabric_cost_id" name="fabric_cost_id" value="<? echo $fabric_cost_id;  ?>"/>
<input type="hidden" id="index" name="index" value="<? echo $index;  ?>"/>
	<table width="700" cellspacing="0" class="rpt_table" border="0" id="tbl_trims_dyes_match" rules="all">
	<thead>
	  <tr>
		<th width="40">S/L</th>
		<th width="150">Item Group</th>
		<th width="150">Item Color</th>
        <th width="150">Item Driscription</th>
		<th width="100">Req. Qty.</th>
		<th width="60">Uom</th>
        <th width="60">Dye Qnty</th>
	  </tr>
	</thead>
	<tbody>
	<?

	$i=1;
	foreach($trims_matches_sql as $row)
	 {
	 $item_color=$dtm_arr_item_color[$fabric_cost_id][$color][$row[csf('id')]];
	 if($item_color==""){
		 $item_color=$color;
	 }
	// echo $color.'DD';
	 ?>
	  <tr>
	  	<td width="40"><? echo $i; ?></td>
	  	<td width="150">
        <? echo $lib_item_group_arr[$row[csf('trim_group')]];?>
        <input class="text_boxes" type="hidden" style="width:150px;"  name="trim_group_<? echo $i; ?>" id="trim_group_<? echo $i; ?>" value="<? echo $row[csf('trim_group')];?>" readonly/>
         <input class="text_boxes" type="hidden" style="width:150px;"  name="pre_cost_trim_cost_id_<? echo $i; ?>" id="pre_cost_trim_cost_id_<? echo $i; ?>" value="<? echo $row[csf('id')];?>" readonly/>
        </td>
	  	<td width="150">
        <? //echo $color_library[$color];?>
        <input class="text_boxes" type="text" style="width:150px;"  name="color_<? echo $i; ?>" id="color_<? echo $i; ?>" value="<? echo $color_library[$item_color];?>"/>
        </td>
	  	<td width="120">
		<? echo $row[csf('description_ra')];?> 
        </td>
	  	<td width="100">
        <input class="text_boxes_numeric" type="text" style="width:100px;"  name="reqqty_<? echo $i; ?>" id="reqqty_<? echo $i; ?>" value="<? echo $row[csf('req_qty_ra')]; ?>" readonly/>
        </td>
        <td width="60">
        <input class="text_boxes" type="text" style="width:60px;"  name="uom_<? echo $i; ?>" id="uom_<? echo $i; ?>" value="<? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?>" readonly/>
        </td>
        <td width="60">
        <input class="text_boxes_numeric" type="text" style="width:60px;"  name="dyeqty_<? echo $i; ?>" id="dyeqty_<? echo $i; ?>" value="<? echo $dtm_arr[$fabric_cost_id][$color][$row[csf('id')]] ?>"/>
        </td>
	  </tr>
	  <? $i++;

	  } ?>
	</tbody>
	</table>
    </form>
    <table width="650" cellspacing="0" class="" border="0">
        <tr>
            <td align="center" width="100%" class="button_container">
            <?
            echo load_submit_buttons( $permission, "fnc_fabric_dye_to_match", 0,0 ,"reset_form('dtm_1','','','','')",1) ;
            ?>
            </td>
        </tr>
    </table>
    </fieldset>
</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}
if($action=="save_update_delete_dye_to_match")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	if ($operation==0)
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$booking_id=return_field_value( "id", "wo_non_ord_samp_booking_mst","booking_no ='$booking_no'");

		$is_approved=0;
		$sql=sql_select("select is_approved from wo_non_ord_samp_booking_mst where booking_no='$booking_no'");
		 foreach($sql as $row){
           // if($row[csf('is_approved')]==3) $is_approved=1; else $is_approved=$row[csf('is_approved')];
		    $is_approved=$row[csf('is_approved')];
        }
        if($is_approved==1) { echo "approved**".str_replace("'","",$txt_booking_no); disconnect($con);die; }
		else if($is_approved==3) { echo "papproved**".str_replace("'","",$txt_booking_no); disconnect($con);die; }
				//var data="action=save_update_delete_dye_to_match&operation="+operation+'&total_row='+row_num+data_all+'&booking_no='+booking_no+'&fabric='+fabric+'&color='+color;
				//data_all=data_all+get_submitted_data_string('trim_group_'+i+'*pre_cost_trim_cost_id_'+i+'*dyeqty_'+i,"../../../",i);

		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; disconnect($con);die;}
		$id=return_next_id( "id", "sample_dev_dye_to_match", 1 ) ;
		$field_array="id,booking_id,booking_no,sample_mst_id,sample_req_fabric_cost_id,fabric_color,item_color,sample_req_trim_cost_id,item_group,qty";//,status_active,is_deleted
		$total_dye_qty=0;
		$new_array_color=array();
		for ($i=1;$i<=$total_row;$i++)
		{
			$trim_group="trim_group_".$i; 
			$pre_cost_trim_cost_id="pre_cost_trim_cost_id_".$i;
			$dyeqty="dyeqty_".$i;
			$item_color="color_".$i;
			//$req_id="txt_req_id".$i;
			
			if(str_replace("'","",$$item_color)!="")
			{
				if (!in_array(str_replace("'","",$$item_color),$new_array_color)){
					$color_id = return_id( str_replace("'","",$$item_color), $color_library, "lib_color", "id,color_name","203");
					$new_array_color[$color_id]=str_replace("'","",$$item_color);
				}
				else $color_id =  array_search(str_replace("'","",$$item_color), $new_array_color);
			}
			else $color_id=0;
			
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$booking_id.",'".$booking_no."','".$txt_req_id."','".$fabric_cost_id."','".$color."','".$color_id."',".$$pre_cost_trim_cost_id.",".$$trim_group.",".$$dyeqty.")";
			$total_dye_qty+=str_replace("'"," ",$$dyeqty);
			$id=$id+1;
		}
		//echo "10**insert into sample_dev_dye_to_match (".$field_array.") values ".$data_array;die;
		$rID_de3=execute_query( "delete from sample_dev_dye_to_match where  sample_req_fabric_cost_id ='".$fabric_cost_id."' and fabric_color= '".$color."'",0);
		$rID=sql_insert("sample_dev_dye_to_match",$field_array,$data_array,1);
		check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID && $rID_de3){
			mysql_query("COMMIT");
			echo "0**".$total_dye_qty;
			}
			else{
			mysql_query("ROLLBACK");
			echo "10**".$total_dye_qty;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID_de3){
			oci_commit($con);
			echo "0**".$total_dye_qty;
			}
			else{
			oci_rollback($con);
			echo "10**".$total_dye_qty;
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="rmg_process_loss_popup")
{
	echo load_html_head_contents("Order Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
 ?>
	<script>
 function js_set_value_set()
 {
	  var cutting_per=$('#cutting_per').val();
	  if(cutting_per=="") cutting_per=0;

	  var embbroidery_per=$('#embbroidery_per').val();
	  if(embbroidery_per=="") embbroidery_per=0;

	  var printing_per=$('#printing_per').val();
	  if(printing_per=="") printing_per=0;

	  var wash_per=$('#wash_per').val();
	  if(wash_per=="") wash_per=0;

	  var sew_per=$('#sew_per').val();
	  if(sew_per=="") sew_per=0;

	  var fin_per=$('#fin_per').val();
	  if(fin_per=="") fin_per=0;

	var knitt_per=$('#knitt_per').val();
	  if(knitt_per=="") knitt_per=0;

	  var dying_per=$('#dying_per').val();
	  if(dying_per=="") dying_per=0;

	  var extracutt_per=$('#extracutt_per').val();
	  if(extracutt_per=="") extracutt_per=0;

	  var other_per=$('#other_per').val();
	  if(other_per=="")other_per=0;

	  var neck_sleev_printing_per=$('#neck_sleev_printing_per').val();
	  if(neck_sleev_printing_per=="") neck_sleev_printing_per=0;

	 // var gmt_other_per=$('#gmt_other_per').val();
	 // if(gmt_other_per=="") gmt_other_per=0;
	  gmt_other_per=0;

	  var yarn_dyeing_per=$('#yarn_dyeing_per').val();
	  if(yarn_dyeing_per=="") yarn_dyeing_per=0;

	  var all_over_print_per=$('#all_over_print_per').val();
	  if(all_over_print_per=="")all_over_print_per=0;

	  var lay_wash_per=$('#lay_wash_per').val();
	  if(lay_wash_per=="") lay_wash_per=0;

	  var gmtfinish_per=$('#gmtfinish_per').val();
	  if(gmtfinish_per=="") gmtfinish_per=0;

	 var txt_processloss_breck_down=cutting_per+'_'+embbroidery_per+'_'+printing_per+'_'+wash_per+'_'+sew_per+'_'+fin_per+'_'+knitt_per+'_'+dying_per+'_'+extracutt_per+'_'+other_per+'_'+neck_sleev_printing_per+'_'+gmt_other_per+'_'+yarn_dyeing_per+'_'+all_over_print_per+'_'+lay_wash_per+'_'+gmtfinish_per;
	 document.getElementById('txt_processloss_breck_down').value=txt_processloss_breck_down;
	 parent.emailwindow.hide();
 }
    </script>

 </head>

 <body>
 <div align="center" style="width:100%;" >
 <? echo load_freeze_divs ("../../../",$permission);  ?>
	<?
	$data=explode("_",$txt_processloss_breck_down);
	?>
 <fieldset>
    <form autocomplete="off">
    <input style="width:60px;" type="hidden" class="text_boxes"  name="txt_processloss_breck_down" id="txt_processloss_breck_down" />
    <table width="180" class="rpt_table" border="1" rules="all">
               <tr>
                <td width="130">
               Cut Panel rejection <!--  Extra Cutting %  breack Down 8-->
                </td>
                <td>
                <input style="width:60px;" type="text" class="text_boxes_numeric"  name="extracutt_per" id="extracutt_per" value="<? echo $data[8];  ?>"  />
                </td>
                </tr>
                <tr>
                <td width="130">
                 Chest Printing <!-- Printing % breack Down 2-->
                </td>
                <td>
                <input style="width:60px;" type="text" class="text_boxes_numeric"  name="printing_per" id="printing_per" value="<? echo $data[2];  ?>" />
                </td>
                </tr>


                <tr>
                <td width="130">
                 Neck/Sleeve Printing <!-- new breack Down 10-->
                </td>
                <td>
                <input style="width:60px;" type="text" class="text_boxes_numeric"  name="neck_sleev_printing_per" id="neck_sleev_printing_per" value="<? echo $data[10];  ?>" />
                </td>
                </tr>


                <tr>
                <td width="130">
                Embroidery  <!-- Embroidery  % breack Down 1-->
                </td>
                <td>
                <input style="width:60px;" type="text" class="text_boxes_numeric"  name="embbroidery_per" id="embbroidery_per" value="<? echo $data[1];  ?>"  />
                </td>
                </tr>


                <tr>
                <td width="130">
                Sewing/Input <!-- Sewing % breack Down 4-->
                </td>
                <td>
                <input style="width:60px;" type="text" class="text_boxes_numeric"  name="sew_per" id="sew_per" value="<? echo $data[4];  ?>" />
                </td>
                </tr>

                <tr>
                <td width="130">
                Garments Wash  <!-- Washing % breack Down 3-->
                </td>
                <td>
                <input style="width:60px;" type="text" class="text_boxes_numeric"  name="wash_per" id="wash_per"  value="<? echo $data[3];  ?>" />
                </td>
                </tr>

                <tr>
                <td width="130">
                Gmts Finishing  <!-- Washing % breack Down 3-->
                </td>
                <td>
                <input style="width:60px;" type="text" class="text_boxes_numeric"  name="gmtfinish_per" id="gmtfinish_per"  value="<? echo $data[15];  ?>" />
                </td>
                </tr>


                <!--<tr>
                <td width="130">-->
                  <!--  Others New breack Down 11-->
               <!-- </td>
                <td>
                <input style="width:60px;" type="text" class="text_boxes_numeric"  name="gmt_other_per" id="gmt_other_per" value="<? //echo $data[11];  ?>"  />
                </td>
                </tr>-->

                <tr>
                <td width="130">
                 Knitting   <!-- Knitting % breack Down 6-->
                </td>
                <td>
                <input style="width:60px;" type="text" class="text_boxes_numeric"  name="knitt_per" id="knitt_per" value="<? echo $data[6];  ?>"  />
                </td>
                </tr>

                <tr>
                <td width="130">
                 Yarn Dyeing   <!-- New breack Down 12-->
                </td>
                <td>
                <input style="width:60px;" type="text" class="text_boxes_numeric"  name="yarn_dyeing_per" id="yarn_dyeing_per" value="<? echo $data[12];  ?>"  />
                </td>
                </tr>

                <tr>
                <td width="130">
                Dyeing & Finishing   <!-- Finishing % breack Down 5-->
                </td>
                <td>
                <input style="width:60px;" type="text" class="text_boxes_numeric"  name="fin_per" id="fin_per" value="<? echo $data[5];  ?>"  />
                </td>
                </tr>


                <tr>
                <td width="130">
                All Over Print  <!-- New breack Down 13-->
                </td>
                <td>
                <input style="width:60px;" type="text" class="text_boxes_numeric"  name="all_over_print_per" id="all_over_print_per" value="<? echo $data[13];  ?>"  />
                </td>
                </tr>

                <tr>
                <td width="130">
                Lay Wash (Fabric)  <!-- New breack Down 14-->
                </td>
                <td>
                <input style="width:60px;" type="text" class="text_boxes_numeric"  name="lay_wash_per" id="lay_wash_per" value="<? echo $data[14];  ?>"  />
                </td>
                </tr>


                <tr>
                <td width="130">
                 Dyeing  <!--breack Down 7-->
                </td>
                <td>
                <input style="width:60px;" type="text" class="text_boxes_numeric"  name="dying_per" id="dying_per" value="<? echo $data[7];  ?>"  />
                </td>
                </tr>
                <tr>
                <td width="130">
                 Cutting (Febric) <!-- Cutting % breack Down 0-->
                </td>
                <td>
                <input style="width:60px;" type="text" class="text_boxes_numeric"  name="cutting_per" id="cutting_per" value="<? echo $data[0];  ?>" />
                </td>
                </tr>
                <tr>
                <td width="130">
                 Others <!--breack Down 9-->
                </td>
                <td>
                <input style="width:60px;" type="text" class="text_boxes_numeric"  name="other_per" id="other_per" value="<? echo $data[9];  ?>"  />
                </td>
                </tr>

                <tr>
               <td align="center"  class="button_container" colspan="2">
			    <input type="button" class="formbutton" value="Close" onClick="js_set_value_set()"/>
                 </td>
                </tr>
           </table>
    </form>
 </fieldset>
 </div>
 </body>
 <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
 </html>
 <?
 exit();
}

if($action=="file_upload")
{
	header("Content-Type: application/json");
	$filename = time().$_FILES['file']['name']; 
	$location = "../../../file_upload/".$filename; 
	$uploadOk = 1;
	if(empty($mst_id))
	{
		$mst_id=$_GET['mst_id'];
	} 
	if(move_uploaded_file($_FILES['file']['tmp_name'], $location))
	{ 
		 $uploadOk = 1;
	}
	else
	{ 
		$uploadOk=0; 
	} 
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}

	$id=return_next_id( "id","COMMON_PHOTO_LIBRARY", 1 ) ;
	$data_array .="(".$id.",".$mst_id.",'sample_requisition_2','file_upload/".$filename."','1','".$filename."')";
	$field_array="id,master_tble_id,form_name,image_location,file_type,real_file_name";
	$rID=sql_insert("COMMON_PHOTO_LIBRARY",$field_array,$data_array,1);

	if($db_type==0)
	{
		if($rID==1 && $uploadOk==1)
		{
			mysql_query("COMMIT");
			echo "0**".$new_system_id[0]."**".$mst_id;
		}
		else
		{
			mysql_query("ROLLBACK");
			echo "10**".$mst_id;
		}
	}
	else if($db_type==2 || $db_type==1 )
	{
		if($rID==1 && $uploadOk==1)
		{
			oci_commit($con);
			echo "0**".$new_system_id[0]."**".$mst_id;
		}
		else
		{
			oci_rollback($con);
			echo "10**".$rID."**".$uploadOk."**INSERT INTO COMMON_PHOTO_LIBRARY(".$field_array.") VALUES ".$data_array;
		}
	}
	disconnect($con);
	die;
}

if($action == "generate_booking_popup")
{
	extract($_REQUEST);
    echo load_html_head_contents("Popup Info", "../../../", 1, 1, $unicode);?>
    <script>
        var selected_name = new Array();
        function check_all_data() {
            var tbl_row_count = document.getElementById( 'template_data_tbl' ).rows.length;
            tbl_row_count = tbl_row_count;
			//alert(tbl_row_count);

            if(document.getElementById('check_all').checked){
                for( var i = 1; i <= tbl_row_count; i++ ) {
	                document.getElementById( 'search' + i ).style.backgroundColor = 'yellow';
	                if( jQuery.inArray( $('#txttemplatedata_' + i).val(), selected_name ) == -1 ) {
	                    selected_name.push($('#txttemplatedata_' + i).val());
	                }
                }
                var templatedata='';
                for( var i = 0; i < selected_name.length; i++ ) {
                    templatedata += selected_name[i] + ',';
                }
                templatedata = templatedata.substr( 0, templatedata.length - 1 );
                $('#select_template_data').val( templatedata );
            }else{
                for( var i = 1; i <= tbl_row_count; i++ ) {
                    if(i%2==0  ){
                        document.getElementById('search'+i).style.backgroundColor = '#FFFFFF';
                    }
                    if(i%2!=0 ){
                        document.getElementById('search'+i).style.backgroundColor = '#E9F3FF';
                    }
                    for( var j = 0; j < selected_name.length; j++ ) {
                        if( selected_name[j] == $('#txttemplatedata_' + i).val() ) break;
                    }
                    selected_name.splice( j,1 );

                }
                var templatedata='';
                for( var i = 0; i < selected_name.length; i++ ) {
                    templatedata += selected_name[i] + ',';
                }
                templatedata = templatedata.substr( 0, templatedata.length - 1 );
                $('#select_template_data').val( templatedata );

            }

        }

        function toggle( x, origColor) {
            var newColor = 'yellow';
            if ( x.style ) {
                x.style.backgroundColor = (newColor == x.style.backgroundColor)? origColor : newColor;

            }
        }

        function onlyUnique(value, index, self) {
            return self.indexOf(value) === index;
        }

        function js_set_value( str) {
        	var tbl_row_count = document.getElementById( 'template_data_tbl' ).rows.length;
            tbl_row_count = tbl_row_count;
            if($("#search"+str).css("display") !='none'){
                if(str%2==0  ){
                    toggle( document.getElementById( 'search' + str ), '#FFFFFF');
                }
                if(str%2!=0 ){
                    toggle( document.getElementById( 'search' + str ), '#E9F3FF');
                }
                if( jQuery.inArray( $('#txttemplatedata_' + str).val(), selected_name ) == -1 ) {
                    selected_name.push($('#txttemplatedata_' + str).val());
                }
                else{
                    for( var i = 0; i < selected_name.length; i++ ) {
                        if( selected_name[i] == $('#txttemplatedata_' + str).val() ) break;
                    }
                    selected_name.splice( i,1 );
                }
            }
            var templatedata='';
            for( var i = 0; i < selected_name.length; i++ ) {
                templatedata += selected_name[i] + ',';
            }
            if(selected_name.length == tbl_row_count){
				document.getElementById("check_all").checked = true;
			}
			else{
				document.getElementById("check_all").checked = false;
			}
            templatedata = templatedata.substr( 0, templatedata.length - 1 );
            $('#select_template_data').val( templatedata );
        }
    </script>
    <?
    $po_data=sql_select("SELECT a.id as po_id, a.po_number, sum(b.order_quantity) as po_qty from wo_po_break_down a join wo_po_color_size_breakdown b on a.id=b.po_break_down_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.job_id=$job_id group by a.id, a.po_number");
      ?>
    </head>
    <body>
    <div align="center" style="width:100%;">
        <table id="trmplate_data_tbl" cellspacing="0" cellpadding="0" border="1" rules="all" width="320" class="rpt_table">
            <input type="hidden" id="select_template_data" name="select_template_data"/>
            <thead>
                <tr>
                	<th width="50">SL</th>
                    <th width="100">PO Number</th>
                    <th width="100">PO Qty</th>
                </tr>
            </thead>
        </table>
        <table id="template_data_tbl" cellspacing="0" cellpadding="0" border="1" rules="all" width="320" class="rpt_table">
            <tbody id="template_date">
				<?
				$i=1;
				foreach ($po_data as $row){
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$str="";
					$str=$row[csf('po_id')].'***'.$row[csf('po_qty')];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" class="itemdata" onClick="js_set_value(<? echo $i; ?>)" >
					<td width="50"><?=$i; ?></td>
					<td width="100">
						<? echo $row[csf("po_number")]; ?>
						<input type="hidden" name="txttemplatedata_<? echo $i; ?>" id="txttemplatedata_<? echo $i; ?>" value="<? echo $str; ?>"/>
						</td>
					<td width="100"><? echo $row[csf("po_qty")]; ?></td>
				</tr>
				<? $i++; } ?>
            </tbody>
        </table>
        <table width="320" id="check_all_tbl" cellspacing="0" cellpadding="0" style="border:none; margin-top: 10px" align="center">
        <tr>
            <td align="center" height="30" width="200" valign="bottom">
                <div style="width:300px">
                    <div style="width:150px; float:left" align="left">
                        <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
                    </div>
                    <div style="width:150px; float:left" align="left">
                        <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                    </div>
                </div>
            </td>
        </tr>
    </table>
    </div>
    </body>
    <script type="text/javascript">
        setFilterGrid("template_name_tbl",-1);
    </script>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

?>
