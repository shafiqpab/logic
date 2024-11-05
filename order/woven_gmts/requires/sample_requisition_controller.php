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
Entry From 		 		 : 117
*/
header('Content-type:text/html; charset=utf-8');
session_start();
if($_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
extract($_REQUEST);
include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];
$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
$size_arr=return_library_array( "select id,size_name from lib_size", "id", "size_name" );
$buyer_aganist_req=return_library_array( "select id,buyer_name from sample_development_mst where is_deleted=0 and status_active=1 order by buyer_name", "id", "buyer_name"  );

if ($action=="load_variable_settings")
{
	$sql_result = sql_select("select variable_list, color_from_library from variable_order_tracking where company_name=$data and variable_list in (23) and status_active=1 and is_deleted=0");
	$color_from_lib=0;
 	foreach($sql_result as $result)
	{
		if($result[csf('variable_list')]==23) $color_from_lib=$result[csf('color_from_library')];
	}
	echo $color_from_lib;
 	exit();
}

function get_company_config($data){
	global $buyer_cond;
	$cbo_buyer_name= create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "season_semple_load(this.value)" );

	echo "document.getElementById('buyer_td').innerHTML = '".$cbo_buyer_name."';\n";

	$agent= create_drop_down( "cbo_agent", 130, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$data' order by buyer_name","id,buyer_name", 1, "-- Select Agent --", $selected, "" );

	echo "document.getElementById('agent_td').innerHTML = '".$agent."';\n";

	$sql="select location_name,id from lib_location where company_id='$data' and is_deleted=0  and status_active=1  order by location_name";
	if(count(sql_select($sql))==1)
	{
		$location= create_drop_down( "cbo_location_name", 130, $sql,'id,location_name', 0, '--- Select Location ---', 0, ""  );
	}
	else
	{
		$location= create_drop_down( "cbo_location_name", 130, $sql,'id,location_name', 1, '--- Select Location ---', 0, ""  );
	}
	echo "document.getElementById('location_td').innerHTML = '".$location."';\n";

	$sql_variable=sql_select("select style_from_library from variable_order_tracking where company_name='$data' and variable_list in (47) and status_active=1 and is_deleted=0");

	foreach($sql_variable as $result)
	{
		if($result[csf('style_from_library')]!=''){
            $style_from_library=$result[csf('style_from_library')];
        }
	}
    echo "style_from_library($style_from_library);\n";
}

if($action=="get_company_config"){
	$action($data);
}

if($action=="style_ref_popup")
{
	echo load_html_head_contents("Style Description Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>

	<script>
		function js_set_value(data)
		{
 			var data=data.split('_');
			document.getElementById('txt_style_name').value=data[1];
			document.getElementById('txt_style_ref_id').value=data[0];
			parent.emailwindow.hide();
		}
		function toggle( x, origColor )
		{
			var newColor = 'yellow';
			document.getElementById(x).style.backgroundColor = ( newColor == document.getElementById(x).style.backgroundColor )? origColor : newColor;
		}
		function check_quatation(data){
			var r=confirm("If you want to continue press Ok, otherwise press Cancel");
			if(r==false)
			{
				$('#txt_style_name').val('');
				$('#txt_style_ref_id').val('');
				parent.emailwindow.hide();
				return;
			}
			else
			{
				js_set_value(data);
			}
		}
		function quotation_insert(data)
		{
			var data_org=data;
			var data=data.split('_');
			var style_id=data[0];
			var txt_style_ref=data[1];
			var quotation_data=return_global_ajax_value( txt_style_ref+"**"+style_id, 'check_style_ref', '', 'sample_requisition_controller');
			var quotation_data=trim(quotation_data) ;
			if(quotation_data)
			{
				$("tbody#quotation_date").html('');
				$("tbody#quotation_date").append(quotation_data);
			}
			else
			{
				alert("No Requisition Found Aganist This Style");
				js_set_value(data_org);
			}

		}


</script>
    </head>
   <body>
		<div align="center">
			    <form>
			        <input type="hidden" id="txt_style_name" name="txt_style_name" />
			        <input type="hidden" id="txt_style_ref_id" name="txt_style_ref_id" />

			    </form>

				<?
		 		$sql="SELECT id, style_ref_name from  lib_style_ref  where status_active=1 and is_deleted=0 and buyer_id='$buyer_name' order by insert_date DESC";
				$data_array=sql_select($sql);

				?>
				<table class="rpt_table" width="240" cellspacing="0" cellpadding="0" border="0" rules="all" align="left">
					<thead>
						<tr>
							<th width="40">SI</th>
							<th width="200">Style Ref.</th>
						</tr>
					</thead>
				<table class="rpt_table" width="240" cellspacing="0" cellpadding="0" border="0" rules="all" align="left" id="style_list">
					<tbody>
						<?
						$i=1;
						foreach($data_array as $row)
						{
							if ($i%2==0)
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";
								?>
								<tr id="tr_<? echo $row[csf('id')] ?>" bgcolor="<? echo $bgcolor; ?>" height="20" style="cursor:pointer; word-break:break-all;" onClick="quotation_insert('<? echo $row[csf('id')]."_".$row[csf('style_ref_name')]; ?>')">
									<td width="40"><? echo $i; ?></td>
									<td width="200" align="left"><? echo $row[csf('style_ref_name')]; ?></td>

								</tr>

							<?
							$i++;
						}
							?>
					</tbody>
					</table>
					<table class="rpt_table" id="quotation_table" width="190" cellspacing="0" cellpadding="0" border="0" rules="all">
						<thead>
							<tr>
								<th width="100">Requisition Id</th>
								<th width="130">Date</th>
							</tr>
						</thead>
						<tbody id="quotation_date">

						</tbody>
					</table>
						<script>
						setFilterGrid("list_view",-1);
						setFilterGrid("style_list",-1);
						</script>

		</div>

	</body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}
if($action=="check_style_ref")
{
	list($name,$id)=explode("**",$data);

	$cond="";
	if($id!="") $cond= " and id !=$id"; else $cond="";
	$idArr=array();
	$sql=sql_select("select id,requisition_number,insert_date,style_ref_no,lib_style_id from sample_development_mst where  style_ref_no='$name'  and lib_style_id='$id' and status_active=1 and is_deleted=0 order by id");
	foreach($sql as $row)
	{
		?>
		<tr onClick="check_quatation('<? echo $row[csf('lib_style_id')]."_".$row[csf('style_ref_no')]; ?>')" bgcolor="#FFFFFF" height="20" style="cursor:pointer;">
			<td width="100"><? echo $row[csf("requisition_number")];?></td>
			<td width="140"><? echo change_date_format($row[csf("insert_date")]);?></td>

		</tr>


		<?

	}

	exit();
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
				$col_sql="select id,color_name from lib_color where status_active=1 and is_deleted=0 and  color_name='$vals' and id in(select sample_color from sample_development_dtls where status_active=1 and is_deleted=0 and entry_form_id=117 and sample_mst_id='$data[2]')";
				$col_arr=sql_select($col_sql);
				$col_id=$col_arr[0][csf("id")];
			}
			else
			{
				$col_arr=sql_select("select id,color_name from lib_color where status_active=1 and is_deleted=0 and  color_name='$vals' and id in(select sample_color from sample_development_dtls where status_active=1 and is_deleted=0 and entry_form_id=117 and sample_mst_id='$data[2]')");
				$col_id.=','.$col_arr[0][csf("id")];

			}
		}
		$value=return_field_value("sum(sample_prod_qty)","sample_development_dtls","entry_form_id=117 and sample_name=$data[0] and gmts_item_id=$data[1] and sample_mst_id=$data[2] and status_active=1 and is_deleted=0 and sample_color in($col_id) ");
	}
	else
	{

		$value=return_field_value("sum(sample_prod_qty)","sample_development_dtls","entry_form_id=117 and sample_name=$data[0] and gmts_item_id=$data[1] and sample_mst_id=$data[2] and status_active=1 and is_deleted=0 ");

	}
	echo trim($value);
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
    $color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );

    $sql=sql_select("select distinct(sample_color) from sample_development_dtls where entry_form_id=117 and sample_mst_id=$data[2] and sample_name=$data[0] and gmts_item_id=$data[1] and is_deleted=0  and status_active=1");
	if(count($sql)==1)
	{
		echo "1_".$color_library[$sql[0][csf("sample_color")]]."_".$sql[0][csf("sample_color")]."_";
	}
	exit();
}

if($action=="auto_sd_color_generation")
{
	$data=explode("***",$data);
	$sql=sql_select("select sample_color from sample_development_dtls where entry_form_id=117 and sample_name=$data[0] and gmts_item_id=$data[1] and sample_mst_id=$data[2] and status_active=1 and is_deleted=0 ");
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
		echo create_drop_down( "cboReType_".$data[1], 140,$emblishment_print_type,"", 1, "-- Select --", "", "","","" );
		die;
	}
	if($data[0]==2)
	{
		echo create_drop_down( "cboReType_".$data[1], 140,$emblishment_embroy_type,"", 1, "-- Select --", "", "","","" );
		die;
	}
	if($data[0]==3)
	{
		echo create_drop_down( "cboReType_".$data[1], 140,$emblishment_wash_type,"", 1, "-- Select --", "", "","","" );
		die;
	}
	if($data[0]==4)
	{
		echo create_drop_down( "cboReType_".$data[1], 140,$emblishment_spwork_type,"", 1, "-- Select --", "", "","","" );
		die;
	}
	if($data[0]==5)
	{
		echo create_drop_down( "cboReType_".$data[1], 140,$emblishment_gmts_type,"", 1, "-- Select --", "", "","","" );
		die;
	}
	exit();
}


if ($action=="load_drop_down_required_fabric_gmts_item")
{
	$data=explode("_", trim($data));
 	$sql=sql_select("select id,gmts_item_id from sample_development_dtls where is_deleted=0  and status_active=1 and entry_form_id=117 and sample_mst_id='$data[0]'");

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
	$sql="select a.id,a.sample_name,b.id as dtls_id from  lib_sample a ,sample_development_dtls b where  a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.id=b.sample_name and b.entry_form_id=117 and b.sample_mst_id='$data[0]' group by a.id,a.sample_name,b.id order by b.id";

		if($data[1]==1)
		{
		    if(count(sql_select($sql))>1)
		    {
		    	echo create_drop_down( "cboRfSampleName_1", 95, $sql,"id,sample_name", 0, "select Sample", $selected,"");
		    }
		    	else
		    	{
		    		echo create_drop_down( "cboRfSampleName_1", 95, $sql,"id,sample_name", 0, "select Sample", $selected,"");
		    	}
		}

		  else if($data[1]==2)
			{
		   // echo create_drop_down( "cboRaSampleName_1", 100, $sql,"id,sample_name", 1, "select Sample", $selected,"");
				if(count(sql_select($sql))>1)
		   		 {
		    	echo create_drop_down( "cboRaSampleName_1", 100, $sql,"id,sample_name", 0, "select Sample", $selected,"");
		    	 }
			    	else
			    	{
			    		echo create_drop_down( "cboRaSampleName_1", 100, $sql,"id,sample_name", 0, "select Sample", $selected,"");
			    	}
			}
				else if($data[1]==3)
				{
			  //  echo create_drop_down( "cboReSampleName_1", 140, $sql,"id,sample_name", 1, "select Sample", $selected,"");
					if(count(sql_select($sql))>1)
		   		 	{
		    	echo create_drop_down( "cboReSampleName_1", 140, $sql,"id,sample_name", 0, "select Sample", $selected,"");
		    		 }
			    	else
			    	{
			    		echo create_drop_down( "cboReSampleName_1", 140, $sql,"id,sample_name", 0, "select Sample", $selected,"");
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
			var fabric_yarn_description=return_global_ajax_value(data[0], 'fabric_yarn_description', '', 'sample_requisition_controller');
			var fabric_yarn_description_arr=fabric_yarn_description.split("**");
			var fabric_description=trim(data[2])+' '+trim(fabric_yarn_description_arr[0]);
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $fabric_nature; ?>'+'**'+'<? echo $libyarncountdeterminationid; ?>'+'**'+document.getElementById('txt_construction').value+'**'+document.getElementById('txt_gsm_weight').value, 'fabric_description_popup_search_list_view', 'search_div', 'sample_requisition_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:100px;" />
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
			$sql="select a.fab_nature_id,a.construction,a.gsm_weight,a.color_range_id,a.stich_length,a.process_loss,b.copmposition_id,b.percent,b.count_id,b.type_id,a.id from  lib_yarn_count_determina_mst a,  lib_yarn_count_determina_dtls b where a.id=b.mst_id and  a.is_deleted=0 order by a.id";
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
			<table class="rpt_table" width="950" cellspacing="0" cellpadding="0" border="0" rules="all">
				<thead>
					<tr>
						<th width="50">SL No</th>
						<th width="100">Fab Nature</th>
						<th width="100">Construction</th>
						<th width="100">GSM/Weight</th>
						<th width="100">Color Range</th>
						<th width="90">Stich Length</th>
						<th width="50">Process Loss</th>
						<th>Composition</th>
					</tr>
				</thead>
			</table>
			<div id="" style="max-height:350px; width:948px; overflow-y:scroll">
				<table id="list_view" class="rpt_table" width="930" height="" cellspacing="0" cellpadding="0" border="1" rules="all">
					<tbody>
						<?

						$sql_data=sql_select("select a.fab_nature_id,a.construction,a.gsm_weight,a.color_range_id,a.stich_length,a.process_loss,a.id from  lib_yarn_count_determina_mst a,  lib_yarn_count_determina_dtls b where a.id=b.mst_id and  a.fab_nature_id= '$fabric_nature' and  a.is_deleted=0 $search_con group by a.id,a.fab_nature_id,a.construction,a.gsm_weight,a.color_range_id,a.stich_length,a.process_loss order by a.id");
						$i=1;
						foreach($sql_data as $row)
						{
							if ($i%2==0)
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";
							?>
							<tr id="tr_<? echo $row[csf('id')] ?>" bgcolor="<? echo $bgcolor; ?>" height="20" style="cursor:pointer; word-break:break-all;" onClick="js_set_value('<? echo $row[csf('id')]."_".$row[csf('fab_nature_id')]."_".$row[csf('construction')]."_".$row[csf('gsm_weight')]."_".$row[csf('process_loss')] ?>')">
								<td width="50"><? echo $i; ?></td>
								<td width="100" align="left"><? echo $item_category[$row[csf('fab_nature_id')]]; ?></td>
								<td width="100" align="left"><? echo $row[csf('construction')]; ?></td>
								<td width="100" align="right"><? echo $row[csf('gsm_weight')]; ?></td>
								<td width="100" align="left"><? echo $color_range[$row[csf('color_range_id')]]; ?></td>
								<td width="90" align="right"><? echo $row[csf('stich_length')]; ?></td>
								<td width="50" align="right"><? echo $row[csf('process_loss')]; ?></td>
								<td><? echo $composition_arr[$row[csf('id')]]; ?></td>
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
	$sql="select a.fab_nature_id,a.construction,a.gsm_weight,a.color_range_id,a.stich_length,a.process_loss,b.copmposition_id,b.percent,b.count_id,b.type_id,a.id from  lib_yarn_count_determina_mst a,  lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id=$data and  a.is_deleted=0 order by a.id";
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

if ($action=="color_popup_rf")
{
	echo load_html_head_contents("Sample Requisition Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
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
			//var rowCount = $('#col_tbl tr').length-1;
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
					breck_down_data+="-----"+($('#txtSL_'+i).val()*1)+'_'+$('#txtColor_'+i).val()+'_'+($('#hiddenColorId_'+i).val())*1+'_'+$('#txtContrast_'+i).val()+'_'+$('#txtQnty_'+i).val()*1+'_'+$('#txtProcessLoss_'+i).val()*1+'_'+$('#txtGreyQnty_'+i).val()*1;
					  display_col +='***'+$('#txtColor_'+i).val() ;
				}
				//alert(breck_down_data);
				total_qnty+=$('#txtQnty_'+i).val()*1;
				total_loss+=$('#txtProcessLoss_'+i).val()*1;
				total_grey+=$('#txtGreyQnty_'+i).val()*1;
			}
			var loss_per= ((total_grey-total_qnty)/total_qnty)*100;
 			document.getElementById('txtRfColorAllData').value=breck_down_data;
 			document.getElementById('displayAllcol').value=display_col;
			//alert(total_qnty);
			document.getElementById('total_qnty_kg').value=total_qnty;
 			document.getElementById('total_loss').value=loss_per;
 			document.getElementById('total_grey').value=total_grey;
			
			parent.emailwindow.hide();
		}
	function calculate_requirement(i)
     {
      	var cbo_company_name= '<? echo $company;?>';

     	var cbo_fabric_natu= 2;
      	var process_loss_method_id=return_global_ajax_value(cbo_company_name+'_'+cbo_fabric_natu, 'process_loss_method_id', '', 'sample_requisition_booking_with_order_controller');
      	//alert(process_loss_method_id);
     	var txt_finish_qnty=(document.getElementById('txtQnty_'+i).value)*1;
     	var processloss=(document.getElementById('txtProcessLoss_'+i).value)*1;
     	var WastageQty='';

     	if(process_loss_method_id==1)
     	{
     		WastageQty=txt_finish_qnty+txt_finish_qnty*(processloss/100);
     	}
     	else if(process_loss_method_id==2)
     	{
     		var devided_val = 1-(processloss/100);
			//alert(WastageQty);
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
	 function copy_gmts_color_to_fab(id)
		{
			var gmts_color=$("#txtColor_"+id).val();
			$("#txtContrast_"+id).val(gmts_color);

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
     function copyValue(data)
     {
     	var check_val=$('#checkboxIdAll').is(':checked');
     	if(check_val==true)
     	{
     		var rows = $('#col_tbl tbody tr').length*1;
     		for(var x=1;x<=rows;x++)
     		{
     			var txtColor=$("#txtColor_"+x).val();
     			$("#txtContrast_"+x).val(txtColor);
     		}
     	}
     }
    </script>

    <body>
		<div align="center" style="width:100%;" >
        <? echo load_freeze_divs ("../../../",$permission,1); ?>
        <form name="size_1" id="size_1">
			<fieldset style="width:600px;">
            <table align="center" cellspacing="0" width="600" class="rpt_table" border="1" rules="all" id="col_tbl" >
            
                <thead>
                	<tr>
            		<td colspan="7" align="center">Copy<input type="checkbox" name="checkboxId" id="checkboxId" value="1">&nbsp;&nbsp;Copy Gmts to Fab. Color<input type="checkbox" name="checkboxIdAll" id="checkboxIdAll" value="0" onClick="copyValue(this.value);"></td>
            	</tr>
                <tr>
                    <th width="30" >SL</th>
                    <th width="70">Gmts Color</th>
                    <th width="70">Contrast</th>
                    <th width="40">Fin Qnty</th>
            		<th width="50">Process Loss%</th>
            		<th width="50">Grey Qnty</th>
                        
                    <th><Input type="hidden" name="mainupid" class="text_boxes" ID="mainupid" value="<? echo $mainId; ?>" style="width:30px" /><Input type="hidden" name="dtlsupid" class="text_boxes" ID="dtlsupid" value="<? echo $dtlId; ?>" style="width:30px" />
                     </th>
                   </tr>
                </thead>
                <tbody>

                <?
					if($data)
					{


					$data_all=explode('-----',$data);
					$count_tr=count($data_all);
					//print_r($data);die;
					if($count_tr>0)
					{
 						$i=1;
						foreach ($data_all as $size_data)
						{
							/*$txtSL=0;
							$txtColor='';
							$hiddenColorId=0;
							$txtContrast=''; */
							$ex_size_data=explode('_',$size_data);
							$txtSL=$ex_size_data[0];
							$txtColor=$ex_size_data[1];
							$hiddenColorId=$ex_size_data[2];
							$txtContrast=$ex_size_data[3];
							$txtQnty=$ex_size_data[4];
							$txtProcessLoss=$ex_size_data[5];
							$txtGreyQnty=$ex_size_data[6];
 						?>
							<tr id="row_<? echo $i; ?>">

								<td><input name="txtSL_<? echo $i; ?>" class="text_boxes" ID="txtSL_<? echo $i; ?>" value="<? echo $i; ?>" style="width:30px" /><input type="hidden" name="colorupid_<? echo $i; ?>" class="text_boxes" ID="colorupid_<? echo $i; ?>" value="" style="width:30px"></td>

								 <td><input name="txtColor_<? echo $i; ?>" class="text_boxes" ID="txtColor_<? echo $i; ?>" style="width:70px" value="<? echo $txtColor; ?>" disabled  />
								  <input type="hidden" name="hiddenColorId_<? echo $i; ?>" id="hiddenColorId_<? echo $i; ?>" value="<? echo $hiddenColorId ?>">

								 </td>

								 <td>
                                 
                                 <input name="txtContrast_<? echo $i; ?>" class="text_boxes" ID="txtContrast_<? echo $i; ?>" style="width:70px" onChange="copy_all_field(this.id,this.value,'3');"
									value="<? echo $txtContrast;?>"  ondblclick="copy_gmts_color_to_fab(<? echo $i; ?>);" />
                                 </td>
                                 
                                 <td><input name="txtQnty_<? echo $i; ?>" class="text_boxes" ID="txtQnty_<? echo $i; ?>" style="width:40px" onBlur="calculate_requirement(<? echo $i; ?>);"  onchange="copy_all_field(this.id,this.value,'1');"  value="<? echo $txtQnty;?>"   /></td>

									<td><input name="txtProcessLoss_<? echo $i; ?>" class="text_boxes" ID="txtProcessLoss_<? echo $i; ?>" style="width:50px" onChange="copy_all_field(this.id,this.value,'2');" onBlur="calculate_requirement(<? echo $i; ?>);"  value="<? echo $txtProcessLoss;?>"   /></td>

									<td><input readonly name="txtGreyQnty_<? echo $i; ?>" class="text_boxes" ID="txtGreyQnty_<? echo $i; ?>" style="width:50px"  value="<? echo $txtGreyQnty;?>"   /></td>

								<td align="center">
									<input type="button" id="increase_<? echo $i; ?>" name="increase_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr( <? echo $i; ?> )" />
									<input type="button" id="decrease_<? echo $i; ?>" name="decrease_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
								</td>
							</tr>
						<?
						$i++;
						}
					}
					}
					else
					{
						$sql_col="select id,sample_color from sample_development_dtls where entry_form_id=117 and sample_mst_id=$mainId and sample_name=$sampleName and gmts_item_id=$garmentItem and is_deleted=0  and status_active=1 order by id ASC";
						$sql_result =sql_select($sql_col);
						$i=1;
						foreach($sql_result as $row)
								{
 					$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );

						?>

						<tr id="row_<? echo $i; ?>">
							<td width="30" align="center" ><input name="txtSL_<? echo $i; ?>" class="text_boxes" ID="txtSL_<? echo $i; ?>" value="<? echo $i; ?>" style="width:30px" /><input type="hidden" name="colorupid_<? echo $i; ?>" class="text_boxes" ID="colorupid_<? echo $i; ?>" value="" style="width:30px"></td>

							 <td width="70" align="center" ><input name="txtColor_<? echo $i; ?>" class="text_boxes" ID="txtColor_<? echo $i; ?>" value="<? echo $color_library[$row[csf('sample_color')]];  ?>" style="width:70px"  disabled />
							 <input type="hidden" name="hiddenColorId_<? echo $i; ?>" id="hiddenColorId_<? echo $i; ?>" value="<? echo $row[csf('sample_color')];  ?>">

							 </td>

							<td width="70" align="center" ><Input name="txtContrast_<? echo $i; ?>" class="text_boxes" ID="txtContrast_<? echo $i; ?>" style="width:70px" onChange="copy_all_field(this.id,this.value,'3');" value="" onDblClick="copy_gmts_color_to_fab(<? echo $i; ?>);"/>					
                            </td>					
								<td width="40" align="center" ><Input name="txtQnty_<? echo $i; ?>" class="text_boxes" ID="txtQnty_<? echo $i; ?>" onBlur="calculate_requirement(<? echo $i; ?>);" onChange="copy_all_field(this.id,this.value,'1');" style="width:70px" value="" /></td>
								<td width="50" align="center" ><Input name="txtProcessLoss_<? echo $i; ?>" class="text_boxes" ID="txtProcessLoss_<? echo $i; ?>" style="width:70px" onChange="copy_all_field(this.id,this.value,'2');" value="" onBlur="calculate_requirement(<? echo $i; ?>);" /></td>
								<td width="50" align="center" ><Input name="txtGreyQnty_<? echo $i; ?>" readonly class="text_boxes" ID="txtGreyQnty_<? echo $i; ?>" style="width:70px" value="" /></td>
                                    
 							<td align="center">
								<input type="button" id="increase_<? echo $i; ?>" name="increase_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr( <? echo $i; ?> )" />
								<input type="button" id="decrease_<? echo $i; ?>" name="decrease_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
							</td>
						</tr>
					<?
						$i++;
								}
					}
                ?>
                </tbody>
            </table>
            <table align="center" cellspacing="0" width="700" class="rpt_table" border="1" rules="all" id="" >

                <tr>
                    <td colspan="7" align="center">
                        <input type="hidden" name="txtRfColorAllData" id="txtRfColorAllData" class="text_boxes"  value="" />
                        <input type="hidden" name="displayAllcol" id="displayAllcol">
                        <input type="hidden" name="total_qnty_kg" id="total_qnty_kg">
                        <input type="hidden" name="total_loss" id="total_loss">
                        <input type="hidden" name="total_grey" id="total_grey">
                     </td>
                </tr>
                <tr>
                    <td align="center" colspan="4" align="center" class="button_container">
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
 	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );

 	 $company_img=sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");


	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$dealing_merchant_library=return_library_array( "select id,team_member_name from lib_mkt_team_member_info", "id", "team_member_name"  );
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
	$required_fabric_imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='required_fabric_1' and file_type=1",'master_tble_id','image_location');
	$required_accessories_imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='required_accessories_1' and file_type=1",'master_tble_id','image_location');
	$required_embellishment_imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='required_embellishment_1' and file_type=1",'master_tble_id','image_location');


//print_r($appDate);die;

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
     	<td rowspan="4" valign="top" width="300"><? if($company_img[0][csf("image_location")] !=''){ ?><img width="150" height="80" src="../../<? echo $company_img[0][csf("image_location")]; ?>"> <? } ?></td>
     	<td colspan="4" style="font-size: 24px;"><strong><b><? echo $company_library[$data[0]]; ?></b></strong></td>
            <td width="200">
            <?
            $is_app=return_field_value("is_approved","sample_development_mst","entry_form_id=117 and id=$data[1] and status_active=1 and is_deleted=0");
            $appDate=explode(" ", $appDate);
            if($is_app==1)
            {
            	echo "<div style='color:red;border:2px solid black;'>
            	Approved By $user_arr[$appBy]
            	<br>
            	Approved Date: ". change_date_format($appDate[0],'yyyy-mm-dd')." </div>
            	";

            }
              ?>
             </td>
             <td colspan="2" id="barcode_img_id" width="250"> </td>
     </tr>




        <tr>
            <td colspan="5">
				<?

                    $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
					foreach ($nameArray as $result)
					{
					?>
						 <? echo $result[csf('plot_no')]; ?>
						 <? if($result[csf('level_no')]!="") echo ",".$result[csf('level_no')]?>
						 <? if($result[csf('road_no')]!="") echo ",".$result[csf('road_no')]; ?>
						 <? if($result[csf('block_no')]!="") echo ",".$result[csf('block_no')];?>
						 <? if($result[csf('city')]!="") echo ",".$result[csf('city')];?>
						 <? if($result[csf('zip_code')]!="") echo ",".$result[csf('zip_code')]; ?>
						 <? if($result[csf('province')]!="") echo ",".$result[csf('province')];?>
						 <? if($result[csf('country_id')]!="") echo ",".$country_arr[$result[csf('country_id')]]; ?><br>
						 Email:<? if($result[csf('email')]!="") echo $result[csf('email')].",";?>
						 Website:<? if($result[csf('website')]!="") echo $result[csf('website')];


					}

					  $sql="SELECT id,requisition_number,requisition_number_prefix_num,style_ref_no,buyer_name,season,product_dept,dealing_marchant,agent_name,buyer_ref,bh_merchant,estimated_shipdate,season_buyer_wise,remarks,quotation_id,sample_stage_id,requisition_date from sample_development_mst where  id='$data[1]' and entry_form_id=117 and  is_deleted=0  and status_active=1";
 					  $dataArray=sql_select($sql);
 					  $barcode_no=$dataArray[0][csf('requisition_number')];
 					  if($dataArray[0][csf("sample_stage_id")]==1)
 					  {
 					  	 $job_lib=return_library_array( "SELECT a.id,min(b.shipment_date) as shipment_date  from wo_po_details_master  a,wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and a.company_name='$data[0]' GROUP BY a.id", "id", "shipment_date"  );
 					  }


                ?>
            </td>

        </tr>
        <tr>
            <td colspan="3" style="font-size:x-large"><strong> <b>Sample Requisition</b></strong></td>
             

        </tr>
        <tr> <td colspan="5"> </td> <td colspan="2"><strong>&nbsp;<? echo $dataArray[0][csf("requisition_number")]; ?> <br>&nbsp;Requisition No. &nbsp;<? echo $dataArray[0][csf("requisition_number_prefix_num")]; ?> <br>&nbsp;Req. Date <?  echo change_date_format($dataArray[0][csf("requisition_date")],"dd-mm-yyyy"); ?></strong></td></tr>

        </table>

        <table width="1000" cellspacing="0" border="1" align="center" rules="all" class="rpt_table" style="font-family: Arial Narrow;margin-left: 20px;" >

        <tr>
					<td width="130"><strong>Buyer Name: </strong></td>
					<td width="270" align="left"><? echo $buyer_library[$dataArray[0][csf('buyer_name')]];?></td>
					<td width="130"  align="left"><strong>Style Ref:</strong></td>
					<td width="240">&nbsp;<? echo $dataArray[0][csf('style_ref_no')];?></td>
					<td width="160" style="word-break:break-all;" align="left"><strong>Season:</strong></td>
					<td>&nbsp;<? echo $season_arr[$dataArray[0][csf('season')]];?></td>

       	</tr>


       	 <tr>
						<td width="130"  ><strong>Product Dept:</strong></td>
						<td width="270" ><? echo $product_dept[$dataArray[0][csf('product_dept')]];?></td>
						<td width="130"   align="left"><strong>Buyer Ref:</strong></td>
						<td width="240" ><? echo $dataArray[0][csf('buyer_ref')];?></td>
						<td width="160"  align="left"><strong>Dealing Merchant:</strong></td>
						<td ><? echo $dealing_merchant_library[$dataArray[0][csf('dealing_marchant')]];?></td>
		 </tr>



		 <tr>
				<td width="130"   align="left"><strong>Est.Ship Date:</strong></td>
				<td width="270" ><? echo change_date_format($dataArray[0][csf('estimated_shipdate')]);?></td>
				<td width="130"><strong>BH Merchant:</strong></td>
				<td width="240"><? echo $dataArray[0][csf('bh_merchant')];?></td>
				<td width="160"><strong><? echo ($dataArray[0][csf("sample_stage_id")]==1)? "Shipment Date:": "&nbsp;";

				?></strong></td>
				<td><? echo ($dataArray[0][csf("sample_stage_id")]==1)?  change_date_format($job_lib[$dataArray[0][csf('quotation_id')]],"yyyy-mm-dd") : "";?></td>


        </tr>
        <tr>
       		 <td><strong>Remarks/Desc:</strong></td>
       		 <td colspan="5" style="word-wrap: break-word;word-break: break-all;" ><? echo $dataArray[0][csf('remarks')];?></td>
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
            	<table align="left" cellspacing="0" border="1" width="100%" class="rpt_table" rules="all">
            	<thead>
            		<tr>
                            <td width="150" colspan="11" align="center"><strong>Sample Details</td>
                        </tr>
                        <tr>
								<th width="30" align="center">SL</th>
								<th width="100" align="center">Sample Name</th>
								<th width="120" align="center">Garment Item</th>
								<th width="30" align="center">SMV</th>
								<th width="55" align="center">Article No</th>
								<th width="70" align="center">Color</th>
								<th width="100" align="center">Prod Qty</th>
								<th width="55" align="center">Submn Qty</th>
								<th width="70" align="center">Delv Start Date</th>
								<th width="70" align="center">Delv End Date</th>
								<th width="120" align="center">Images</th>

                         </tr>

            	</thead>
                    <tbody>

                        <?

					   $sample_color_arr=return_library_array( "select id, sample_color from sample_development_dtls", "id", "sample_color"  );
                       $sql_qry="select id,sample_mst_id,sample_name,gmts_item_id,smv,article_no,sample_color,sample_prod_qty,submission_qty,delv_start_date,delv_end_date,sample_charge,sample_curency from sample_development_dtls where status_active =1 and is_deleted=0 and entry_form_id=117 and sample_mst_id='$data[1]' order by id asc";

						$result=sql_select($sql_qry);
 						$i=1;$k=0;
 						$prod_sum=0;
 						$sub_sum=0;
						foreach($result as $row)
						{
							$dtls_ids=$row[csf('id')];
							 $size_select=sql_select("SELECT  size_id,total_qty  from sample_development_size where  mst_id='$data[1]' and status_active=1 and is_deleted=0 and dtls_id='$dtls_ids' ");
 							$prod_sum=$prod_sum+$row[csf('sample_prod_qty')];
							$sub_sum=$sub_sum+$row[csf('submission_qty')];

						?>
                        <tr>
                            <?
 							$k++;
							?>
                            <td width="30" align="center"><? echo $k;?></td>
                            <td width="100" align="left"><? echo $sample_library[$row[csf('sample_name')]];?></td>
                            <td width="120" align="left"><? echo $garments_item[$row[csf('gmts_item_id')]];?></td>
                            <td  width="30" align="right"><? echo $row[csf('smv')];?></td>
                            <td  width="55"align="right"><? echo $row[csf('article_no')];?></td>
                            <td width="70" align="left"><? echo $color_library[$row[csf('sample_color')]];?></td>
                            <td width="45" align="left">
                            	<table border="1" class="rpt_table" rules="all" cellspacing="0">
                            		<tr >
                            			<?
                            			$total_sizes_qty=0;
                            			foreach($size_select as $val_size)
                            			{
                            				?>
                            				<td align="center"><? echo $size_arr[$val_size[csf("size_id")]]; ?></td>

                            				<?
                            				$total_sizes_qty+=$val_size[csf("total_qty")];

                            			}
                            			?>
                            			<td align="center">Total</td>


                            		</tr>

                            		<tr>
                            			<? foreach($size_select as $val_size)
                            			{

                            				?>
                            				<td align="center"><? echo $val_size[csf("total_qty")]; ?></td>

                            				<?

                            			}
                            			?>
                            			<td align="center"><? echo $total_sizes_qty; ?></td>

                            		</tr>
                            	</table>
                            </td>
                            <td width="55" align="right"><? echo $row[csf('submission_qty')];?></td>
                            <td width="70" align="center"><? echo change_date_format($row[csf('delv_start_date')]);?> </td>
                            <td width="70" align="center"><? echo change_date_format($row[csf('delv_end_date')]);?> </td>
                            <td><? if($imge_arr[$row[csf('id')]] !=''){ ?><img src="../../<? echo $imge_arr[$row[csf('id')]];?>" width="120" height='60'><? } ?></td>



                            <?
                        }

						?>




                        </tr>
							<tr>
									<td colspan="6" align="center"><b>Total</b></td>
 									<td   align="right"><b><? echo number_format($prod_sum,2);?> </b></td>
 									<td  align="right"><b><? echo number_format($sub_sum,2);?> </b></td>
									<td colspan="2"></td>
							</tr>


                    </tbody>
                    <tfoot>
                     </tfoot>
               </table>
             </td>
        </tr>
         <tr> <td colspan="6">&nbsp;</td></tr>



         <tr> <td colspan="6">&nbsp;</td></tr>
        <tr>
        	<td width="250" align="left" valign="top" colspan="2">
            	<table align="left" cellspacing="0" border="1" width="100%" class="rpt_table" rules="all">
            	<thead>
            		<tr>
                            <td width="150" colspan="14" align="center"><strong>Required Fabric</td>
                        </tr>
                        <tr>
								<th width="30" align="center">SL</th>
								<th width="120" align="center">Sample Name</th>
								<th width="120" align="center">Garment Item</th>
								<th width="70" align="center">Body</th>
								<th width="105" align="center">Fabric Nature</th>
								<th width="110" align="center">Fabric Desc</th>
								<th width="35" align="center">GSM</th>
								<th width="35" align="center">Dia</th>
								<th width="70" align="center">Fabric Color</th>
								<th width="60" align="center">Color Type</th>
								<th width="70" align="center">Width/Dia</th>
								<th width="25" align="center">UOM</th>
								<th width="30" align="center">Req/Dzn </th>
								<th width="30" align="center">Req/Qty </th>
								<th width="120" align="center">Image</th>
                         </tr>
            	</thead>
                    <tbody>


                        <?
					   $sql_qryf="select id,sample_mst_id,sample_name,gmts_item_id,body_part_id,fabric_nature_id,fabric_description,gsm,dia,color_data,color_type_id,width_dia_id,uom_id,required_dzn,required_qty from sample_development_fabric_acc where status_active =1 and is_deleted=0 and form_type=1 and sample_mst_id='$data[1]' order by id asc";
						$resultf=sql_select($sql_qryf);
 						$i=1;$k=0;
 						$req_dzn=0;
 						$req_qty=0;
						foreach($resultf as $rowf)
						{
							$req_dzn=$req_qty+$rowf[csf('required_dzn')];
							$req_qty=$req_qty+$rowf[csf('required_qty')];

						?>
                        <tr>
                            <?
 							$k++;
							?>
                            <td  align="center"><? echo $k;?></td>
                            <td  align="left"><? echo $sample_library[$rowf[csf('sample_name')]];?></td>
                            <td  align="left"><? echo $garments_item[$rowf[csf('gmts_item_id')]];?></td>
                            <td  align="left"><? echo $body_part[$rowf[csf('body_part_id')]];?></td>
                            <td  align="left"><? echo $item_category[$rowf[csf('fabric_nature_id')]];?></td>
                            <td  align="left"><? echo $rowf[csf('fabric_description')];?></td>
                            <td  align="right"><? echo $rowf[csf('gsm')];?></td>
                            <td  align="right"><? echo $rowf[csf('dia')];?></td>
                            <td  align="left">
                             	<?
									$color='';
									$contrast='';
  									$dd=explode("-----",$rowf[csf('color_data')]);
									for($key=0;$key<=count($dd);$key++)
									{
										$ddd=explode("_",$dd[$key]);
										if($ddd[3]=="") {$color.=$ddd[1].",";}
										else
										{
											$color.=$ddd[3].",";
										}


										//$contrast.=$ddd[3].",";
									}
									//$contrast_col=rtrim($contrast,",");

  										echo rtrim($color,",");

                             	 ?>
                            </td>
                            <td  align="center"><? echo $color_type[$rowf[csf('color_type_id')]];?></td>
                            <td  align="left"><? echo $fabric_typee[$rowf[csf('width_dia_id')]];?></td>
                            <td  align="center"><? echo $unit_of_measurement[$rowf[csf('uom_id')]];?></td>
                            <td  align="right"><? echo $rowf[csf('required_dzn')];?></td>
                            <td  align="right"><? echo $rowf[csf('required_qty')];?></td>
                            <td><? if($required_fabric_imge_arr[$rowf[csf('id')]] !='' ){  ?><img src="../../<?echo $required_fabric_imge_arr[$rowf[csf('id')]];?>" width="120" height='60'><? } ?></td>


                            <?
                        }

						?>




                        </tr>

                        <tr>
									<td colspan="13" align="center"><b>Total </b></td>
									<!-- <td  align="right"><b><? echo number_format($req_dzn,2);?></b></td> -->
  									<td  align="right"><b><? echo number_format($req_qty,2);?> </b></td>
 							</tr>


                    </tbody>
                    <tfoot>

                    </tfoot>
               </table>
             </td>
        </tr>
         <tr> <td colspan="6">&nbsp;</td></tr>

          <tr> <td colspan="6">&nbsp;</td></tr>
        <tr>
        	<td width="250" align="left" valign="top" colspan="2">
            	<table align="left" cellspacing="0" border="1" width="100%" class="rpt_table" rules="all">
            	<thead>
            		<tr>
                            <td width="150" colspan="10" align="center"><strong>Required Accessories</td>
                        </tr>
                        <tr>
								<th width="30" align="center">SL</th>
								<th width="100" align="center">Sample Name</th>
								<th width="120" align="center">Garment Item</th>
								<th width="100" align="center">Trims Group</th>
								<th width="100" align="center">Description</th>
								<th width="100" align="center">Brand/Supp.Ref</th>
 								<th width="30" align="center">UOM</th>
								<th width="30" align="center">Req/Dzn </th>
								<th width="30" align="center">Req/Qty </th>
								<th width="80" align="center">Remarks </th>
								<th width="120" align="center">Image </th>
                         </tr>
            	</thead>
                    <tbody>


                        <?
					   $sql_qryA="select id,sample_mst_id,sample_name_ra,gmts_item_id_ra,trims_group_ra,description_ra,brand_ref_ra,uom_id_ra,req_dzn_ra,req_qty_ra,remarks_ra from sample_development_fabric_acc where status_active =1 and is_deleted=0 and form_type=2 and sample_mst_id='$data[1]' order by id asc";

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
                            <td  align="left"><? echo $rowA[csf('brand_ref_ra')];?></td>
                             <td  align="center"><? echo $unit_of_measurement[$rowA[csf('uom_id_ra')]];?></td>
                            <td  align="right"><? echo $rowA[csf('req_dzn_ra')];?></td>
                            <td  align="right"><? echo $rowA[csf('req_qty_ra')];?></td>
                            <td  align="left"><? echo $rowA[csf('remarks_ra')];?></td>
                            <td><? if($required_accessories_imge_arr[$rowA[csf('id')]] !=''){ ?><img src="../../<?echo $required_accessories_imge_arr[$rowA[csf('id')]];?>" width="120" height='60'><? } ?></td>

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
								<th width="30" align="center">SL</th>
								<th width="100" align="center">Sample Name</th>
								<th width="110" align="center">Garment Item</th>
								<th width="60" align="center">Name</th>
 								<th width="70" align="center">Type</th>
								<th width="70" align="center">Remarks</th>
								<th width="120" align="center">Image</th>

                         </tr>
            	</thead>
                    <tbody>


                        <?
                        $sql_qry="select id,sample_mst_id,sample_name_re,gmts_item_id_re,name_re,type_re,remarks_re from sample_development_fabric_acc where sample_mst_id='$data[1]' and form_type=3 and is_deleted=0  and status_active=1 order by id asc";

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
                            <td  align="left"><? echo $row[csf('remarks_re')];?></td>
                            <td><? if($required_embellishment_imge_arr[$row[csf('id')]]){ ?> <img src="../../<?echo $required_embellishment_imge_arr[$row[csf('id')]];?>" width="120" height='60'><? } ?></td>
                              <?
                        }

						?>




                        </tr>


                    </tbody>
                    <tfoot>

                    </tfoot>
               </table>
             </td>
        </tr>


        <tr>
        	<td width="810" align="left" valign="top" colspan="2" >
            	<table align="left" cellspacing="0" width="810" class="rpt_table" >
                	<tr>
                    	<td colspan="6">
							<?

                             echo signature_table(86, $data[0], "810px");

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


if ($action=="sizeinfo_popup")
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
								<td><input name="txtsizename[]" class="text_boxes" ID="txtsizename_<? echo $i; ?>" value="<? echo $size_name; ?>" style="width:100px" autofocus/><input type="hidden" name="sizeupid[]" class="text_boxes" ID="sizeupid_<? echo $i; ?>" value="" style="width:30px" ></td>

								 <td><input name="txtbhqty[]" class="text_boxes_numeric" ID="txtbhqty_<? echo $i; ?>" style="width:70px" onBlur="calculate_total_qnty_by_type();" value="<? echo $bh_qty; ?>" /></td>

								<td><input name="txtplqty[]" class="text_boxes_numeric" ID="txtplqty_<? echo $i; ?>" style="width:70px" onBlur="calculate_total_qnty_by_type();" value="<? echo $pl_qty; ?>" /></td>

								<td><input name="txtdyqty[]" class="text_boxes_numeric" ID="txtdyqty_<? echo $i; ?>" style="width:70px" onBlur="calculate_total_qnty_by_type();" value="<? echo $dy_qty; ?>" /></td>

							   <td><input name="txttestqty[]" class="text_boxes_numeric" ID="txttestqty_<? echo $i; ?>" style="width:70px" onBlur="calculate_total_qnty_by_type();" value="<? echo $test_qty; ?>" /></td>

							   <td><input name="txtselfqty[]" class="text_boxes_numeric" ID="txtselfqty_<? echo $i; ?>" style="width:70px"  onBlur="calculate_total_qnty_by_type();" value="<? echo $self_qty; ?>"/></td>

							   <td><input name="txttotalqty[]" class="text_boxes_numeric" ID="txttotalqty_<? echo $i; ?>" style="width:70px"  readonly value="<? echo $totalqty; ?>" /></td>
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
    <script>calculate_total_qnty_by_type(); </script>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}



if($action=="load_data_to_sizeinfo")
{
	$qry_size="select id, mst_id, dtls_id, size_id, size_qty,bh_qty,plan_qty,dyeing_qty,test_qty,self_qty,total_qty from sample_development_size where dtls_id='$data'";
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
		if($total_qty=="") $total_qty=$row[csf("total_qty")]; else $total_qty.="*".$row[csf("total_qty")];
	}
	echo "document.getElementById('hidden_size_id').value 	 				= '".$size_id."';\n";
	echo "document.getElementById('hidden_bhqty').value 	 					= '".$bh_qty."';\n";
	echo "document.getElementById('hidden_plnqnty').value 	 					= '".$pl_qty."';\n";
	echo "document.getElementById('hidden_dyqnty').value 	 					= '".$dy_qty."';\n";
	echo "document.getElementById('hidden_testqnty').value 	 					= '".$test_qty."';\n";
	echo "document.getElementById('hidden_selfqnty').value 	 					= '".$self_qty."';\n";
	echo "document.getElementById('hidden_totalqnty').value 	 					= '".$total_qty."';\n";
	echo "document.getElementById('hidden_tbl_size_id').value 	 			= '".$id."';\n";
	exit();
}


if ($action=="load_drop_down_location")
{
	$sql="select location_name,id from lib_location where company_id='$data' and is_deleted=0  and status_active=1  order by location_name";
	if(count(sql_select($sql))==1)
	{
		echo create_drop_down( "cbo_location_name", 160, $sql,'id,location_name', 0, '--- Select Location ---', 0, ""  );
	}
	else
	{
		echo create_drop_down( "cbo_location_name", 160, $sql,'id,location_name', 1, '--- Select Location ---', 0, ""  );
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
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buyer_name","id,buyer_name", 1, "-- Select buyer --", $selected, "load_drop_down( 'requires/sample_requisition_controller', this.value, 'load_drop_down_season_buyer', 'season_td');load_drop_down( 'requires/sample_requisition_controller', this.value, 'load_drop_down_sample_for_buyer', 'sample_td');" );
}

if ($action=="load_drop_down_sample_for_buyer")
{
	echo create_drop_down( "cboSampleName_1", 100, "select a.id,a.sample_name,b.sequ from lib_sample a,lib_buyer_tag_sample b where a.id=b.tag_sample and  b.buyer_id=$data and b.sequ  is not null and
 a.status_active=1 and a.is_deleted=0  group by  a.id,a.sample_name,b.sequ order by b.sequ ","id,sample_name", 1, "-- Select Sample --", $selected, "" );
}

if ($action=="load_drop_down_buyer_req")
{
	/*echo create_drop_down( "cbo_buyer_name", 157, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" ); */

	if($data != 0)
    {
	echo create_drop_down( "cbo_buyer_name", 157, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
	exit();
    }
    else{
        echo create_drop_down( "cbo_buyer_name", 157, "SELECT buy.id as id ,buy.buyer_name as buyer_name  from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id    and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,30,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
        exit();
    }
}

if ($action=="load_drop_down_buyer_style")
{
	if($data != 0)
    {
	echo create_drop_down( "cbo_buyer_name", 157, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
	exit();
    }
    else{
        echo create_drop_down( "cbo_buyer_name", 157, "SELECT buy.id as id ,buy.buyer_name as buyer_name  from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id    and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,30,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
        exit();
    }
}

if ($action=="load_drop_down_buyer_inq")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
}


if ($action=="load_drop_down_season_buyer")
{
	//$datas=explode('_',$data);
	//echo create_drop_down( "cbo_season_name", 158, "select a.id,a.season_name from LIB_BUYER_SEASON a,variable_order_tracking b where a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and a.buyer_id='$datas[0]' and b.company_name='$datas[1]' and b.season_mandatory=1 and b.variable_list=44","id,season_name", 1, "-- Select Season --", $selected, "" );
	echo create_drop_down( "cbo_season_name", 130, "select a.id,a.season_name from lib_buyer_season a where a.status_active =1 and a.is_deleted=0 and a.buyer_id='$data'","id,season_name", 1, "-- Select Season --", $selected, "" );
}

if ($action=="load_drop_down_agent")
{
	echo create_drop_down( "cbo_agent", 130, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$data' order by buyer_name","id,buyer_name", 1, "-- Select Agent --", $selected, "" );
	exit();
}

if ($action=="load_drop_down_dealing_merchant")
{
	echo create_drop_down( "cbo_dealing_merchant", 130, "select id,team_member_name from lib_mkt_team_member_info where team_id='$data' and status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select Team Member --", $selected, "" );
	exit();
}
if($action=="check_acknowledge")
{
	$data=explode("**", $data);
	$id=$data[1];
	$requisition_number=$data[0];
	$sql="select is_acknowledge from sample_development_mst where entry_form_id=117 and requisition_number='$requisition_number' and id=$id and status_active=1 and is_deleted=0 and is_acknowledge=1";
	$res=sql_select($sql);
	//echo $sql;
	if(count($res)>0)
	{
		echo "1";
	}
	else echo "0";
	exit();
}
if ($action=="save_update_delete_mst")
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
		$id_mst=return_next_id( "id", "sample_development_mst", 0) ;
		$new_system_id=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', '', date("Y",time()), 5, "select requisition_number_prefix,requisition_number_prefix_num from sample_development_mst where entry_form_id=117 and company_id=$cbo_company_name and to_char(insert_date,'YYYY')=".date('Y',time())." order by id desc ", "requisition_number_prefix", "requisition_number_prefix_num" ));

		$style_ref_id = str_replace("'","",$txt_style_ref_id);
		if($style_ref_id == ''){
			$buyer = str_replace("'","",$cbo_buyer_name);
			$style=sql_select("select id from lib_style_ref where style_ref_name=$txt_style_name and buyer_id =$buyer ");
			if( count($style) > 0){
				foreach ($style as $result){
					$txt_style_ref_id=$result[csf('id')];
				}
			}
			else{
				$id=return_next_id( "id", "lib_style_ref", 0);
				$field_array1="id,style_ref_name,buyer_id,inserted_by,insert_date, status_active,is_deleted";
				$data_array1="(".$id.",".$txt_style_name.",".$cbo_buyer_name.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				//echo $data_array; die;
				$style_ref=sql_insert("lib_style_ref",$field_array1,$data_array1,1);
				if($db_type==0)
				{
					if($style_ref )
					{
						mysql_query("COMMIT");
						$txt_style_ref_id= $id;
					}
					else
					{
						mysql_query("ROLLBACK");
						echo "10**".$id;
						disconnect($con);
						die;
					}
				}
				if($db_type==2 || $db_type==1 )
				{
					if($style_ref )
					{
						oci_commit($con);
						$txt_style_ref_id= $id;
					}
					else
					{
						oci_rollback($con);
						echo "10**".$id;
						disconnect($con);
						die;
					}
				}
				//echo $txt_style_ref_id; die;
			}
		}
		else{
			$txt_style_ref_id = str_replace("'","",$txt_style_ref_id);
		}


		if($txt_style_ref_id != '')
		{
			$field_array="id, requisition_number_prefix, requisition_number_prefix_num, requisition_number, sample_stage_id, requisition_date, quotation_id, style_ref_no, company_id, location_id, buyer_name, season_year, season, product_dept, team_leader, dealing_marchant, agent_name, buyer_ref, bh_merchant, estimated_shipdate, remarks, inserted_by, insert_date, status_active, is_deleted, entry_form_id, req_ready_to_approved, material_delivery_date, lib_style_id";
			$data_array="(".$id_mst.",'".$new_system_id[1]."',".$new_system_id[2].",'".$new_system_id[0]."',".$cbo_sample_stage.",".$txt_requisition_date.",".$txt_quotation_id.",".$txt_style_name.",".$cbo_company_name.",".$cbo_location_name.",".$cbo_buyer_name.",".$cbo_season_year.",".$cbo_season_name.",".$cbo_product_department.",".$cbo_team_leader.",".$cbo_dealing_merchant.",".$cbo_agent.",".$txt_buyer_ref.",".$txt_bhmerchant.",".$txt_est_ship_date.",".$txt_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0,117,".$cbo_ready_to_approved.",".$txt_material_dlvry_date.",".$txt_style_ref_id.")";
			//echo "insert into sample_development_mst ($field_array) values $data_array"; die;
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
		if($db_type==2 || $db_type==1 )
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
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$style_ref_id = str_replace("'","",$txt_style_ref_id);
		if($style_ref_id == ''){
			$buyer = str_replace("'","",$cbo_buyer_name);
			//echo "select id from lib_style_ref where style_ref_name=$txt_style_name and buyer_id =$buyer"; die;
			$style=sql_select("select id from lib_style_ref where style_ref_name=$txt_style_name and buyer_id =$buyer ");
			if( count($style) > 0){
				foreach ($style as $result){
					$txt_style_ref_id=$result[csf('id')];
				}
			}
			else{
				$id=return_next_id( "id", "lib_style_ref", 0);
				$field_array1="id,style_ref_name,buyer_id,inserted_by,insert_date, status_active,is_deleted";
				$data_array1="(".$id.",".$txt_style_name.",".$cbo_buyer_name.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				//echo $data_array; die;
				$style_ref=sql_insert("lib_style_ref",$field_array1,$data_array1,1);
				if($db_type==0)
				{
					if($style_ref )
					{
						mysql_query("COMMIT");
						$txt_style_ref_id= $id;
					}
					else
					{
						mysql_query("ROLLBACK");
						echo "10**".$id;
						disconnect($con);
						die;
					}
				}
				if($db_type==2 || $db_type==1 )
				{
					if($style_ref )
					{
						oci_commit($con);
						$txt_style_ref_id= $id;
					}
					else
					{
						oci_rollback($con);
						echo "10**".$id;
						disconnect($con);
						die;
					}
				}
				//echo $txt_style_ref_id; die;
			}
		}
		else{
			$txt_style_ref_id = str_replace("'","",$txt_style_ref_id);
		}
		if($txt_style_ref_id != ''){
			$field_array="sample_stage_id*requisition_date*style_ref_no*buyer_name*season_year*season*product_dept*team_leader*dealing_marchant*agent_name*buyer_ref*bh_merchant*estimated_shipdate*remarks*updated_by*update_date*status_active*is_deleted*req_ready_to_approved*material_delivery_date*lib_style_id";
			$data_array="".$cbo_sample_stage."*".$txt_requisition_date."*".$txt_style_name."*".$cbo_buyer_name."*".$cbo_season_year."*".$cbo_season_name."*".$cbo_product_department."*".$cbo_team_leader."*".$cbo_dealing_merchant."*".$cbo_agent."*".$txt_buyer_ref."*".$txt_bhmerchant."*".$txt_est_ship_date."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0*".$cbo_ready_to_approved."*".$txt_material_dlvry_date."*".$txt_style_ref_id."";

		 $rID=sql_update("sample_development_mst",$field_array,$data_array,"id","".$update_id."",1);
		}



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
		if($db_type==2 || $db_type==1 )
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
		if($db_type==2 || $db_type==1 )
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
                <table width="900" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
                    <thead>
                        <th  colspan="7">
                            <?
                            echo create_drop_down( "cbo_search_category", 140, $string_search_type,'', 1, "-- Search Catagory --" );
                            ?>
                        </th>
                    </thead>
                    <thead>
                        <th width="140" class="must_entry_caption">Company Name</th>
                        <th width="160" class="must_entry_caption">Buyer Name</th>
                        <th width="130">Style ID</th>
                        <th width="130" >Style Name</th>
                        <th width="130" colspan="2">Est. Ship Date Range</th>
                        <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
                    </thead>
                    <tr class="general">
                        <td width="140">
                            <input type="hidden" id="selected_job">
                            <?
                            echo create_drop_down( "cbo_company_mst", 140, "select id,company_name from lib_company where status_active =1 and is_deleted=0  order by company_name","id,company_name",1, "-- Select Company --", '',"load_drop_down( 'sample_requisition_controller', this.value, 'load_drop_down_buyer_style', 'buyer_td_st' );" );
                            ?>
                        </td>
                        <td id="buyer_td_st" width="160">
                            <?
                            echo create_drop_down( "cbo_buyer_name", 157, "SELECT buy.id as id ,buy.buyer_name as buyer_name  from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id    and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,30,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
                            ?>
                        </td>
                        <td width="130">
                            <input type="text" style="width:130px" class="text_boxes"  name="txt_style_id" id="txt_style_id"  />
                        </td>
                        <td width="130" align="center">
                            <input type="text" style="width:130px" class="text_boxes"  name="txt_style_name1" id="txt_style_name1"  />
                        </td>
                        <td align="center">
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px" placeholder="From"></td>
                        <td align="center">
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px" placeholder="To">
                        </td>
                        <td align="center">
                            <input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_style_id').value+'_'+document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_style_name1').value, 'create_style_id_search_list_view', 'search_div', 'sample_requisition_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:80px;" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="7" align="center" valign="middle">
                            <? echo load_month_buttons(1);  ?>
                        </td>
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


if($action=="inquiry_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
?>

<script>
	function js_set_value(mrr)
	{
		var exmrr=mrr.split("_");
 		$("#txt_style_ref_id").val(exmrr[0]); // mrr number
		$("#txt_style_name").val(exmrr[1]); // mrr number
		
		parent.emailwindow.hide();
	}
</script>

</head>

<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="500" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
            <thead>
                    <th colspan="4"> </th>
                    <th  >
                      <?
                       echo create_drop_down( "cbo_string_search_type", 140, $string_search_type,'', 1, "--Searching Type--" );
                      ?>
                    </th>
                   <th colspan="3" > </th>
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
                <tr>
                <td>
                    <?
                        echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0  order by company_name","id,company_name", 1, "-- Select Company --",'', "load_drop_down( 'sample_requisition_controller', this.value, 'load_drop_down_buyer_inq', 'buyer_td_inq' );");
                     ?>


                </td>
                    <td id="buyer_td_inq">
                        <?
							echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-- Select Buyer --", $selected, "" );
                        ?>
                    </td>


                    <td width="" align="center" >
                        <input type="text" style="width:80px" class="text_boxes"  name="txt_inquery_id" id="txt_inquery_id" />
                    </td>
                       <td>
                         <?
                            echo create_drop_down( "cbo_year", 70, $year,"", 1, "- Select- ", date('Y'), "" );
                         ?>

                    </td>
                    <td width="" align="center" >
                        <input type="text" style="width:120px" class="text_boxes"  name="txt_style" id="txt_style" />
                    </td>
                    <td width="" align="center" >
                        <input type="text" style="width:80px" class="text_boxes"  name="txt_requst_no" id="txt_requst_no" />
                    </td>
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="  Date" />

                    </td>
                    <td align="center">
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_inquery_id').value+'_'+document.getElementById('cbo_year').value+'_'+document.getElementById('txt_requst_no').value+'_'+document.getElementById('cbo_string_search_type').value, 'create_inquiry_search_list_view', 'search_div', 'sample_requisition_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:80px;" />
                    </td>
            </tr>
        	<tr>
            	<td align="center" valign="middle" colspan="7">
                     <input type="hidden" id="txt_style_ref_id" value="" />
                     <input type="hidden" id="txt_style_name" value="" />
                </td>
            </tr>
            </tbody>
         </tr>
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
	echo create_list_view("list_view", "Company Name,Buyer Name,Inquery ID,Year,Buyer Inquery No,Style Ref., Inquery Date,Season","120,120,70,50,70,120,90,120","875","260",0, $sql , "js_set_value", "id,style_refernce", "", 1, "company_id,buyer_id,0,0,0,0,0,season_buyer_wise", $arr, "company_id,buyer_id,system_number_prefix_num,year,buyer_request,style_refernce,inquery_date,season_buyer_wise", "",'','0,0,0,0,0,0,3') ;
	?>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if($action=="populate_data_from_inquiry_search")
{
$sql = sql_select("select  id,company_id,buyer_id,season_buyer_wise,inquery_date,style_refernce,department_name,remarks,team_leader,dealing_marchant,gmts_item,est_ship_date,color,season from wo_quotation_inquery where id='$data' order by id");
	foreach($sql as $row)
	{
		echo "load_drop_down( 'requires/sample_requisition_controller', '".$row[csf("company_id")]."', 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/sample_requisition_controller', '".$row[csf("company_id")]."', 'load_drop_down_location', 'location_td' );load_drop_down( 'requires/sample_requisition_controller', '".$row[csf("company_id")]."', 'load_drop_down_agent', 'agent_td' );load_drop_down( 'requires/sample_requisition_controller','".$row[csf("buyer_id")]."', 'load_drop_down_season_buyer', 'season_td');load_drop_down( 'requires/sample_requisition_controller','".$row[csf("buyer_id")]."', 'load_drop_down_sample_for_buyer', 'sample_td');load_drop_down( 'requires/sample_requisition_controller', '".$row[csf("gmts_item")]."', 'load_drop_down_garment_item_for_after_order', 'item_id_1')\n";
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";
		//echo "$('#cbo_location_name').val('".$result[csf('location_name')]."');\n";
		echo "document.getElementById('txt_quotation_id').value = '".$row[csf("id")]."';\n";
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_id")]."';\n";
		echo "document.getElementById('txt_style_name').value = '".$row[csf("style_refernce")]."';\n";
		echo "document.getElementById('txt_remarks').value = '".$row[csf("remarks")]."';\n";
		echo "document.getElementById('cbo_product_department').value = '".$row[csf("department_name")]."';\n";
		echo "document.getElementById('cbo_season_name').value = '".$row[csf("season_buyer_wise")]."';\n";
		echo "document.getElementById('cbo_team_leader').value = '".$row[csf("team_leader")]."';\n";
		echo "document.getElementById('cbo_dealing_merchant').value = ".$row[csf("dealing_marchant")].";\n";
		echo "document.getElementById('txt_est_ship_date').value = '".change_date_format($row[csf("est_ship_date")],"dd-mm-yyyy","-")."';\n";

	}
}



if($action=="create_style_id_search_list_view")
{
	$data=explode('_',$data);
	if($data[2]==0 && $data[3]==0)
	{
		echo "<span style='color:red; font-weight:bold; font-size:20px; text-align:center'>Please select company or buyer first.";
		die;
	}
	if ($data[2]!=0) $company=" and company_name='$data[2]'"; else $company="";
	if ($data[3]!=0) $buyer=" and buyer_name='$data[3]'"; else $buyer="";//{ echo "Please Select Buyer First."; die; }
	if($data[0]==1)
		{
		   if (trim($data[1])!="") $style_id_cond=" and id='$data[1]'"; else $style_id_cond="";
		   if ($data[6]!="") $style_cond=" and style_ref_no='$data[6]'"; else $style_cond="";
		}

	if($data[0]==4 || $data[0]==0)
		{
		  if (trim($data[1])!="") $style_id_cond=" and id like '%$data[1]%' "; else $style_id_cond="";
		  if ($data[6]!="") $style_cond=" and style_ref_no like '%$data[6]%' "; else $style_cond="";
		}

	if($data[0]==2)
		{
		  if (trim($data[1])!="") $style_id_cond=" and id like '$data[1]%' "; else $style_id_cond="";
		  if ($data[6]!="") $style_cond=" and style_ref_no like '$data[6]%' "; else $style_cond="";
		}

	if($data[0]==3)
		{
		  if (trim($data[1])!="") $style_id_cond=" and id like '%$data[1]' "; else $style_id_cond="";
		  if ($data[6]!="") $style_cond=" and style_ref_no like '%$data[6]' "; else $style_cond="";
		}


	if($db_type==0)
	{
	if ($data[4]!="" &&  $data[5]!="") $estimated_shipdate  = "and estimated_shipdate  between '".change_date_format($data[4], "yyyy-mm-dd", "-")."' and '".change_date_format($data[5], "yyyy-mm-dd", "-")."'"; else $estimated_shipdate ="";
	}
	if($db_type==2)
	{
	if ($data[4]!="" &&  $data[5]!="") $estimated_shipdate  = "and estimated_shipdate  between '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[5], "yyyy-mm-dd", "-",1)."'"; else $estimated_shipdate ="";
	}

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');

	$dealing_marchant=return_library_array( "select id, team_member_name from lib_mkt_team_member_info",'id','team_member_name');
	$team_leader=return_library_array( "select id, team_leader_name from lib_marketing_team",'id','team_leader_name');

	$arr=array (2=>$buyer_arr,4=>$product_dept,5=>$team_leader,6=>$dealing_marchant);
	$sql="";

	if($db_type==0)
	{
	$sql= "select id,job_no_prefix_num,SUBSTRING_INDEX(a.`insert_date`, '-', 1) as year,company_name,buyer_name,style_ref_no,product_dept,team_leader,dealing_marchant from wo_po_details_master where  status_active=1 and is_deleted=0 $company $buyer $style_id_cond $style_cond  order by id";
	}

	if($db_type==2)
	{
	$sql= "select id,job_no_prefix_num,to_char(insert_date,'YYYY') as year,company_name,buyer_name,style_ref_no,product_dept,team_leader,dealing_marchant from wo_po_details_master where  status_active=1 and is_deleted=0 $company $buyer $style_id_cond $style_cond  order by id";
	}
		 	echo  create_list_view("list_view", "Year,Job No,Buyer Name,Style Name,Product Department,Team Leader,Dealing Merchant", "60,140,140,100,90,90,90","900","240",0, $sql , "js_set_value", "id", "", 1, "0,0,buyer_name,0,product_dept,team_leader,dealing_marchant,0", $arr , "year,job_no_prefix_num,buyer_name,style_ref_no,product_dept,team_leader,dealing_marchant", "",'','0,0,0,0,0,0,0') ;

	exit();
}

if($action=="populate_data_from_search_popup")
{
	$res = sql_select("select * from wo_po_details_master where id=$data");

 	foreach($res as $result)
	{
		//echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_sample_requisition_mst_info',1);\n";
		echo "load_drop_down( 'requires/sample_requisition_controller', '".$result[csf("company_name")]."', 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/sample_requisition_controller', '".$result[csf("company_name")]."', 'load_drop_down_location', 'location_td' );load_drop_down( 'requires/sample_requisition_controller', '".$result[csf("company_name")]."', 'load_drop_down_agent', 'agent_td' );load_drop_down( 'requires/sample_requisition_controller', '".$result[csf("company_name")]."', 'load_drop_down_buyer', 'buyer_td');load_drop_down( 'requires/sample_requisition_controller','".$result[csf("buyer_name")]."', 'load_drop_down_season_buyer', 'season_td');load_drop_down( 'requires/sample_requisition_controller','".$result[csf("team_leader")]."', 'load_drop_down_dealing_merchant', 'div_marchant');load_drop_down( 'requires/sample_requisition_controller','".$result[csf("buyer_name")]."', 'load_drop_down_sample_for_buyer', 'sample_td');load_drop_down( 'requires/sample_requisition_controller', '".$result[csf("gmts_item_id")]."', 'load_drop_down_garment_item_for_after_order', 'item_id_1');load_drop_down( 'requires/sample_requisition_controller', '".$result[csf("garments_nature")]."', 'load_drop_down_fabric_nature_for_after_order', 'rf_fabric_nature_1');\n";
		//load_drop_down( 'requires/sample_requisition_controller', '".$result[csf("item_number_id")]."', 'load_drop_down_trims_group_for_after_order', 'ra_trims_group_1');

		echo "$('#txt_quotation_id').val('".$result[csf('id')]."');\n";
		echo "$('#txt_quotation_job_no').val('".$result[csf('job_no')]."');\n";
		echo "$('#cbo_company_name').val('".$result[csf('company_name')]."');\n";
		echo "$('#cbo_location_name').val('".$result[csf('location_name')]."');\n";
		echo "$('#cbo_buyer_name').val('".$result[csf('buyer_name')]."');\n";
		//echo "document.getElementById('txt_quotation_id').value = '".$result[csf("quotation_id")]."';\n";
		echo "$('#txt_style_name').val('".$result[csf('style_ref_no')]."');\n";
		echo "$('#cbo_product_department').val('".$result[csf('product_dept')]."');\n";
		echo "$('#cbo_agent').val('".$result[csf('agent_name')]."');\n";
		echo "$('#cbo_season_year').val('".$result[csf('season_year')]."');\n";
		$season_id=0;
		if($result[csf("season_matrix")]!=0) $season_id=$result[csf("season_matrix")];
		else $season_id=$result[csf("season_buyer_wise")];

		echo "$('#cbo_season_name').val('".$season_id."');\n";
		echo "$('#cbo_team_leader').val('".$result[csf('team_leader')]."');\n";
		echo "$('#cbo_dealing_merchant').val('".$result[csf('dealing_marchant')]."');\n";
		echo "$('#txt_product_code').val('".$result[csf('product_code')]."');\n";
		echo "$('#txt_bhmerchant').val('".$result[csf('bh_merchant')]."');\n";

		//echo "$('#txt_est_ship_date').val('".change_date_format($result[csf('estimated_shipdate')],'dd-mm-yyyy','-')."');\n";
		echo "$('#txt_remarks').val('".$result[csf('remarks')]."');\n";

		//echo "$('#update_id').val('".$result[csf('id')]."');\n";
  	}
 	exit();
}




if($action=="requisition_id_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Sample Requisition Info","../../../", 1, 1, $unicode);
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
        <table width="950" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
            <thead>
                <th colspan="9">
                  <?
                   echo create_drop_down( "cbo_search_category", 140, $string_search_type,'', 1, "-- Search Catagory --" );
                  ?>
                </th>

            </thead>
            <thead>
                <th width="140" class="must_entry_caption">Company Name</th>
                <th width="157" class="must_entry_caption">Buyer Name</th>
                <th width="70">Requisition No</th>
                <th width="100">Style ID</th>
                <th width="120" >Style Name</th>
                <th width="90">Sample Stage</th>
                <th width="130" colspan="2">Est. Ship Date</th>
                <th width="80"><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
            </thead>
            <tr class="general">
                <td width="140">
                    <input type="hidden" id="selected_job">
                    <?
                        echo create_drop_down( "cbo_company_mst", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", '',"load_drop_down( 'sample_requisition_controller', this.value, 'load_drop_down_buyer_req', 'buyer_td_req' );" );
                    ?>
                </td>
                <td id="buyer_td_req" width="157">
                     <?
                        echo create_drop_down( "cbo_buyer_name", 157, "SELECT buy.id as id ,buy.buyer_name as buyer_name  from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id    and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,30,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
                    ?>
                </td>
                <td width="70">
                    <input type="text" style="width:70px" class="text_boxes" name="txt_requisition_num" id="txt_requisition_num"  />
                </td>

                <td width="100">
                    <input type="text" style="width:100px" class="text_boxes"  name="txt_style_id" id="txt_style_id"  />
                </td>
                <td width="90" align="center">
                    <input type="text" style="width:90px" class="text_boxes"  name="txt_style_name1" id="txt_style_name1"  />
                </td>

                <td width="90" align="center">
                    <?
                        echo create_drop_down( "cbo_sample_stage", 90, $sample_stage, "", 1, "-Select Stage-", $selected, "", "", "" );
                    ?>
                </td>

                <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px" placeholder="From"></td>
                <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px" placeholder="To"></td>
                <td align="center">
                    <input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_style_id').value+'_'+document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_style_name1').value+'_'+document.getElementById('txt_requisition_num').value+'_'+document.getElementById('cbo_sample_stage').value, 'create_requisition_id_search_list_view', 'search_div', 'sample_requisition_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:80px;" />
                </td>
            </tr>
            <tr>
                <td colspan="9" align="center" valign="middle">
                    <? echo load_month_buttons(1);  ?>
                </td>
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
if($action=="create_requisition_id_search_list_view")
{
	$data=explode('_',$data);
	if($data[2]==0 && $data[3]==0)
	{
		echo "<span style='color:red; font-weight:bold; font-size:20px; text-align:center'>Please select company or buyer first.";
		die;
	}
	if ($data[2]!=0) $company=" and company_id='$data[2]'"; else $company = "";
	if ($data[3]!=0) $buyer=" and buyer_name='$data[3]'"; else $buyer="";//{ echo "Please Select Buyer First."; die; }
	if($data[0]==1)
		{
		   if (trim($data[1])!="") $style_id_cond=" and id='$data[1]'"; else $style_id_cond="";
		   if ($data[6]!="") $style_cond=" and style_ref_no='$data[6]'"; else $style_cond="";
		}

	if($data[0]==4 || $data[0]==0)
		{
		  if (trim($data[1])!="") $style_id_cond=" and id like '%$data[1]%' "; else $style_id_cond="";
		  if ($data[6]!="") $style_cond=" and style_ref_no like '%$data[6]%' "; else $style_cond="";
		}

	if($data[0]==2)
		{
		  if (trim($data[1])!="") $style_id_cond=" and id like '$data[1]%' "; else $style_id_cond="";
		  if ($data[6]!="") $style_cond=" and style_ref_no like '$data[6]%' "; else $style_cond="";
		}

	if($data[0]==3)
		{
		  if (trim($data[1])!="") $style_id_cond=" and id like '%$data[1]' "; else $style_id_cond="";
		  if ($data[6]!="") $style_cond=" and style_ref_no like '%$data[6]' "; else $style_cond="";
		}


	if($db_type==0)
	{
	if ($data[4]!="" &&  $data[5]!="") $estimated_shipdate  = "and estimated_shipdate  between '".change_date_format($data[4], "yyyy-mm-dd", "-")."' and '".change_date_format($data[5], "yyyy-mm-dd", "-")."'"; else $estimated_shipdate ="";
	}
	if($db_type==2)
	{
	if ($data[4]!="" &&  $data[5]!="") $estimated_shipdate  = "and estimated_shipdate  between '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[5], "yyyy-mm-dd", "-",1)."'"; else $estimated_shipdate ="";
	}
	if ($data[7]!="") $requisition_num=" and requisition_number_prefix_num like '%$data[7]' "; else $requisition_num="";

	if ($data[8]!=0) $stage_id=" and sample_stage_id= '$data[8]' "; else  $stage_id="";


	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');

	$dealing_marchant=return_library_array( "select id, team_member_name from lib_mkt_team_member_info",'id','team_member_name');
	$team_leader=return_library_array( "select id, team_leader_name from lib_marketing_team",'id','team_leader_name');

	$arr=array (2=>$buyer_arr,4=>$product_dept,5=>$dealing_marchant,6=>$sample_stage);
	$sql="";
if($db_type==0)
	{
		$sql= "select id,requisition_number_prefix_num,SUBSTRING_INDEX(insert_date, '-', 1) as year,company_id,buyer_name,style_ref_no,product_dept,dealing_marchant ,sample_stage_id from sample_development_mst where entry_form_id=117 and status_active=1 and is_deleted=0 $company $buyer $style_id_cond $style_cond  $estimated_shipdate $requisition_num   $stage_id order by id DESC";
	}
	else if($db_type==2)
	{
	$sql= "select id,requisition_number_prefix_num,to_char(insert_date,'YYYY') as year,company_id,buyer_name,style_ref_no,product_dept,dealing_marchant,sample_stage_id from sample_development_mst where entry_form_id=117 and  status_active=1 and is_deleted=0 $company $buyer $style_id_cond $style_cond $estimated_shipdate $requisition_num  $stage_id order by id DESC";
	}

	echo  create_list_view("list_view", "Year,Requisition No,Buyer Name,Style Name,Product Department,Dealing Merchant,Sample Stage", "60,140,140,100,90,90,100","950","240",0, $sql , "js_set_value", "id", "", 1, "0,0,buyer_name,0,product_dept,dealing_marchant,sample_stage_id", $arr , "year,requisition_number_prefix_num,buyer_name,style_ref_no,product_dept,dealing_marchant,sample_stage_id", "",'','0,0,0,0,0,0') ;

	exit();
}
if($action == "get_style_library"){
	$sql_variable=sql_select("select style_from_library from variable_order_tracking where company_name='$data' and variable_list in (47) and status_active=1 and is_deleted=0");

	foreach($sql_variable as $result)
	{
		if($result[csf('style_from_library')]!=''){
			$style_from_library=$result[csf('style_from_library')];
		}
	}
	echo $style_from_library;
}

if($action=="populate_data_from_requisition_search_popup")
{
	$res = sql_select("SELECT id, company_id, lib_style_id, location_id, buyer_name, style_ref_no, product_dept, agent_name, team_leader, dealing_marchant, bh_merchant, season_year,season, buyer_ref, estimated_shipdate, remarks, requisition_number, sample_stage_id, requisition_date, material_delivery_date, quotation_id, is_approved, req_ready_to_approved, is_acknowledge from sample_development_mst where id=$data and entry_form_id=117 and is_deleted=0 and status_active=1");
	$sample_st=$res[0][csf("sample_stage_id")];
	$quotation_info=$res[0][csf("quotation_id")];
	if($sample_st==1)
	{
		$job_arr=array();
		$job_sql="select id, company_name, buyer_name, style_ref_no, product_dept, location_name, agent_name, team_leader, dealing_marchant, bh_merchant, season_matrix, season_buyer_wise,gmts_item_id,garments_nature from wo_po_details_master where is_deleted=0 and status_active=1";
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
			$job_arr[$jrow[csf("id")]]['tleader']=$jrow[csf("team_leader")];
			$job_arr[$jrow[csf("id")]]['dmarchant']=$jrow[csf("dealing_marchant")];
			$job_arr[$jrow[csf("id")]]['bh']=$jrow[csf("bh_merchant")];
			$job_arr[$jrow[csf("id")]]['gmts']=$jrow[csf("gmts_item_id")];
			$job_arr[$jrow[csf("id")]]['gmtsnature']=$jrow[csf("garments_nature")];
			$job_arr[$jrow[csf("id")]]['season']=$season_id;
		}
	 	unset($job_sql_res);

	}
	if($sample_st==2 && $quotation_info)
	{
		$inq_arr=array();
		$inq_sql="select id,company_id,buyer_id,season_buyer_wise,inquery_date,style_refernce,department_name,remarks,team_leader,dealing_marchant,gmts_item,est_ship_date,color,season from wo_quotation_inquery where is_deleted=0 and status_active=1";
		$inq_sql_res=sql_select($inq_sql);
		foreach($inq_sql_res as $Inqrow)
		{
			$inq_arr[$Inqrow[csf("id")]]['company']=$Inqrow[csf("company_id")];
			$inq_arr[$Inqrow[csf("id")]]['buyer']=$Inqrow[csf("buyer_id")];
			$inq_arr[$Inqrow[csf("id")]]['style']=$Inqrow[csf("style_refernce")];
			//$inq_arr[$Inqrow[csf("id")]]['dept']=$Inqrow[csf("department_name")];
			$inq_arr[$Inqrow[csf("id")]]['tleader']=$Inqrow[csf("team_leader")];
			$inq_arr[$Inqrow[csf("id")]]['dmarchant']=$Inqrow[csf("dealing_marchant")];
			$inq_arr[$Inqrow[csf("id")]]['gmts']=$Inqrow[csf("gmts_item")];
			$inq_arr[$Inqrow[csf("id")]]['season']=$Inqrow[csf("season")];
			$inq_arr[$Inqrow[csf("id")]]['est']=$Inqrow[csf("est_ship_date")];
			$inq_arr[$Inqrow[csf("id")]]['remarks']=$Inqrow[csf("remarks")];
		}
		unset($inq_sql_res);
	}
	$is_booking = sql_select("SELECT style_id from wo_non_ord_samp_booking_dtls where style_id=$data and status_active=1 and is_deleted=0 and entry_form_id=140 group by style_id union SELECT style_id from wo_booking_dtls where style_id=$data and status_active=1 and is_deleted=0 and entry_form_id=139 group by style_id");
	 //clearstatcache();


 	foreach($res as $result)
	{
	    get_company_config($result[csf("company_id")]);
		echo "load_drop_down( 'requires/sample_requisition_controller','".$result[csf("buyer_name")]."', 'load_drop_down_season_buyer', 'season_td');load_drop_down( 'requires/sample_requisition_controller','".$result[csf("buyer_name")]."', 'load_drop_down_sample_for_buyer', 'sample_td');load_drop_down( 'requires/sample_requisition_controller', '".$result[csf("id")]._1."', 'load_drop_down_required_fabric_sample_name', 'rfSampleId_1');load_drop_down( 'requires/sample_requisition_controller','".$result[csf("id")]._1."', 'load_drop_down_required_fabric_gmts_item', 'rfItemId_1');load_drop_down( 'requires/sample_requisition_controller', '".$result[csf("id")]._2."', 'load_drop_down_required_fabric_sample_name','raSampleId_1');load_drop_down( 'requires/sample_requisition_controller', '".$result[csf("id")]._2."', 'load_drop_down_required_fabric_gmts_item', 'raItemId_1');load_drop_down( 'requires/sample_requisition_controller', '".$result[csf("id")]._3."', 'load_drop_down_required_fabric_sample_name','reSampleId_1');
		load_drop_down( 'requires/sample_requisition_controller','".$result[csf("id")]._3."', 'load_drop_down_required_fabric_gmts_item', 'reItemIid_1');\n";
		
		
		$div_marchant= create_drop_down( "cbo_dealing_merchant", 130, "select id,team_member_name from lib_mkt_team_member_info where team_id='".$result[csf("team_leader")]."' and status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select Team Member --", $selected, "" );
		echo "document.getElementById('div_marchant').innerHTML = '".$div_marchant."';\n";

 		echo "$('#txt_requisition_id').val('".$result[csf('requisition_number')]."');\n";
		echo "$('#cbo_sample_stage').val('".$result[csf('sample_stage_id')]."');\n";
		echo "$('#txt_requisition_date').val('".change_date_format($result[csf('requisition_date')],'dd-mm-yyyy','-')."');\n";
		echo "$('#txt_material_dlvry_date').val('".change_date_format($result[csf('material_delivery_date')],'dd-mm-yyyy','-')."');\n";
		echo "$('#update_id').val('".$result[csf('id')]."');\n";
		echo "$('#cbo_ready_to_approved').val('".$result[csf('req_ready_to_approved')]."');\n";


		if($result[csf('sample_stage_id')]==1)
		{
			echo "$('#txt_quotation_id').val('".$result[csf('quotation_id')]."');\n";
			echo "$('#cbo_company_name').val('".$job_arr[$result[csf("quotation_id")]]['company']."');\n";
			echo "$('#cbo_location_name').val('".$job_arr[$result[csf("quotation_id")]]['loaction']."');\n";
			echo "$('#cbo_buyer_name').val('".$job_arr[$result[csf("quotation_id")]]['buyer']."');\n";
			//echo "document.getElementById('txt_quotation_id').value = '".$result[csf("quotation_id")]."';\n";
			echo "$('#txt_style_name').val('".$job_arr[$result[csf("quotation_id")]]['style']."');\n";
			echo "$('#cbo_product_department').val('".$job_arr[$result[csf("quotation_id")]]['dept']."');\n";
			echo "$('#cbo_agent').val('".$job_arr[$result[csf("quotation_id")]]['agent']."');\n";
			echo "$('#cbo_team_leader').val('".$job_arr[$result[csf("quotation_id")]]['tleader']."');\n";
			echo "$('#cbo_dealing_merchant').val('".$job_arr[$result[csf("quotation_id")]]['dmarchant']."');\n";
			//echo "$('#txt_product_code').val('".$result[csf('product_code')]."');\n";
			echo "$('#txt_bhmerchant').val('".$job_arr[$result[csf("quotation_id")]]['bh']."');\n";
			echo "$('#cbo_season_name').val('".$job_arr[$result[csf("quotation_id")]]['season']."');\n";
			echo "fnc_browse_style('".$result[csf('sample_stage_id')]."');\n";
			echo "load_drop_down( 'requires/sample_requisition_controller', '".$job_arr[$result[csf("quotation_id")]]['gmts']."', 'load_drop_down_garment_item_for_after_order', 'item_id_1');\n";
		}
		else if($result[csf('sample_stage_id')]==2 && ($result[csf('quotation_id')]))
		{
			echo "$('#txt_quotation_id').val('".$result[csf('quotation_id')]."');\n";
			echo "$('#cbo_company_name').val('".$inq_arr[$result[csf("quotation_id")]]['company']."');\n";
			echo "$('#cbo_location_name').val('".$result[csf('location_id')]."');\n";
			echo "$('#cbo_buyer_name').val('".$inq_arr[$result[csf("quotation_id")]]['buyer']."');\n";
			echo "$('#txt_style_name').val('".$inq_arr[$result[csf("quotation_id")]]['style']."');\n";
			echo "$('#cbo_product_department').val('".$result[csf('product_dept')]."');\n";
			echo "$('#cbo_team_leader').val('".$inq_arr[$result[csf("quotation_id")]]['tleader']."');\n";
			echo "$('#cbo_dealing_merchant').val('".$inq_arr[$result[csf("quotation_id")]]['dmarchant']."');\n";
			echo "$('#cbo_season_name').val('".$inq_arr[$result[csf("quotation_id")]]['season']."');\n";
			echo "$('#txt_est_ship_date').val('".$inq_arr[$result[csf("quotation_id")]]['est']."');\n";
			echo "$('#cbo_season_name').val('".$result[csf('season')]."');\n";
			echo "$('#txt_remarks').val('".$inq_arr[$result[csf("quotation_id")]]['remarks']."');\n";
			echo "fnc_browse_style('".$result[csf('sample_stage_id')]."');\n";
			echo "load_drop_down( 'requires/sample_requisition_controller', '".$inq_arr[$result[csf("quotation_id")]]['gmts']."', 'load_drop_down_garment_item_for_after_order', 'item_id_1');\n";
		}
 		else
		{
			echo "$('#cbo_company_name').val('".$result[csf('company_id')]."');\n";
			echo "$('#cbo_location_name').val('".$result[csf('location_id')]."');\n";
			echo "$('#cbo_buyer_name').val('".$result[csf('buyer_name')]."');\n";
			echo "$('#txt_style_name').val('".$result[csf('style_ref_no')]."');\n";
			echo "$('#txt_style_ref_id').val('".$result[csf('lib_style_id')]."');\n";
			echo "$('#cbo_product_department').val('".$result[csf('product_dept')]."');\n";
			echo "$('#cbo_team_leader').val('".$result[csf('team_leader')]."');\n";
			echo "$('#cbo_dealing_merchant').val('".$result[csf('dealing_marchant')]."');\n";
			echo "$('#txt_style_name').attr('onChange','remove_style_ref_id()');";

		}
		echo "$('#cbo_team_leader').val('".$result[csf('team_leader')]."');\n";
		echo "$('#txt_est_ship_date').val('".change_date_format($result[csf('estimated_shipdate')],'dd-mm-yyyy','-')."');\n";
		echo "$('#txt_remarks').val('".$result[csf('remarks')]."');\n";
		echo "$('#txt_buyer_ref').val('".$result[csf('buyer_ref')]."');\n";
		echo "$('#txt_bhmerchant').val('".$result[csf('bh_merchant')]."');\n";
		echo "$('#cbo_agent').val('".$result[csf('agent_name')]."');\n";
		echo "$('#cbo_season_year').val('".$result[csf('season_year')]."');\n";
		echo "$('#cbo_season_name').val('".$result[csf('season')]."');\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_sample_requisition_mst_info',1);\n";
		echo "$('#approvedMsg').html('');\n";
 		//echo "$('#sample_dtls').removeProp('disabled')".";\n";
		echo "$('#required_fab_dtls').removeProp('disabled')".";\n";
		echo "$('#required_accessories_dtls').removeProp('disabled')".";\n";
		echo "$('#required_embellishment_dtls').removeProp('disabled')".";\n";
		echo "$('#sample_dtls').removeProp('disabled')".";\n";
 		if($result[csf('is_approved')]==1 || $result[csf('is_acknowledge')]==1 || count($is_booking)>0)
		{	if($result[csf('is_approved')]==1)
			{
				 echo "$('#approvedMsg').html('This Requisition is Approved by Authority..!!');\n";
			}
			if($result[csf('is_acknowledge')]==1)
			{
				 echo "$('#approvedMsg').html('This Requisition is Acknowledge by Authority..!!');\n";
			}

			if(count($is_booking)>0)
			{
				echo "$('#approvedMsg').html('Booking found aganist this Requisition!!');\n";
			}
  			echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_sample_requisition_mst_info',1,1);\n";
 			echo "$('#save1').removeClass('formbutton').addClass('formbutton_disabled');\n";
 			echo "$('#save1').removeAttr('onclick','fnc_sample_requisition_mst_info(0)');\n";
			echo "$('#cbo_sample_stage').attr('disabled','true')".";\n";
			echo "$('#txt_requisition_date').attr('disabled','true')".";\n";
			echo "$('#txt_style_name').attr('disabled','true')".";\n";
			echo "$('#cbo_company_name').attr('disabled','true')".";\n";
			echo "$('#cbo_location_name').attr('disabled','true')".";\n";
			echo "$('#cbo_buyer_name').attr('disabled','true')".";\n";
			echo "$('#cbo_season_year').attr('disabled','true')".";\n";
			echo "$('#cbo_season_name').attr('disabled','true')".";\n";
			echo "$('#cbo_product_department').attr('disabled','true')".";\n";
			echo "$('#cbo_team_leader').attr('disabled','true')".";\n";
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

		if($result[csf('is_approved')]!=1 && $result[csf('is_acknowledge')]!=1)
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

if($action=="color_popup_bk")
{
	extract($_REQUEST);
	echo load_html_head_contents("Sample Color Info","../../../", 1, 1, $unicode);
	?>
    <script>
		function js_set_value( mst_id )
		{
			document.getElementById('txt_color_name').value=mst_id;
			parent.emailwindow.hide();
		}
    </script>
    <input type="hidden" id="txt_color_name">
    <?
	$lib_color_arr=return_library_array( "select id,color_name from lib_color", "id","color_name" );
	$job_arr=return_library_array( "select id,job_no from wo_po_details_master", "id","job_no" );
	$arr=array(1=>$lib_color_arr);
	if($style_db_id!='')
	{
		$sql= "select b.color_name from wo_po_color_size_breakdown a, lib_color b where a.color_number_id=b.id and a.status_active=1 and a.is_deleted=0 and a.job_no_mst='".$job_arr[$style_db_id]."' group by b.color_name";

		echo  create_list_view("list_view", "Color Name", "150","220","240",0, $sql , "js_set_value", "color_name", "", 1, "0,0", $arr , "color_name","requires/sample_requisition_controller", 'setFilterGrid("list_view",-1);' );
	}
	else
	{
		$sql= "select id, color_name from lib_color";

		echo  create_list_view("list_view", "color_name", "150","220","240",0, $sql , "js_set_value", "color_name", "", 1, "0", $arr , "color_name","requires/sample_requisition_controller", 'setFilterGrid("list_view",-1);' );
	}
	exit();
}
if($action=="color_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Sample Color Info","../../../", 1, 1, $unicode);
	?>
    <script>
    	var selected_name = new Array();
    	function toggle( x, origColor) {
            var newColor = 'yellow';
            if ( x.style ) {
                x.style.backgroundColor = (newColor == x.style.backgroundColor)? origColor : newColor;

            }
        }
		function js_set_value( str )

		{
			/*document.getElementById('txt_color_name').value=mst_id;
			parent.emailwindow.hide();*/
			if(str%2==0  ){
                toggle( document.getElementById( 'color_' + str ), '#FFFFFF');
            }
            if(str%2!=0 ){
                toggle( document.getElementById( 'color_' + str ), '#E9F3FF');
            }
            if( jQuery.inArray( $('#colorName_' + str).val(), selected_name ) == -1 ) {
                selected_name.push($('#colorName_' + str).val());
            }
            else{
                for( var i = 0; i < selected_name.length; i++ ) {
                    if( selected_name[i] == $('#colorName_' + str).val() ) break;
                }
                selected_name.splice( i,1 );
            }
            var colordata='';
            for( var i = 0; i < selected_name.length; i++ ) {
                colordata += selected_name[i] + ',';
            }

            colordata = colordata.substr( 0, colordata.length - 1 );
            $('#hidden_color_name').val( colordata );
		}
    </script>
    <input type="hidden" id="txt_color_name">
    <?
	$lib_color_arr=return_library_array( "select id,color_name from lib_color", "id","color_name" );
	$job_arr=return_library_array( "select id,job_no from wo_po_details_master", "id","job_no" );
	$arr=array(1=>$lib_color_arr);
	if($style_db_id!='')
	{
		$color_arr= sql_select("select b.color_name from wo_po_color_size_breakdown a, lib_color b where a.color_number_id=b.id and a.status_active=1 and a.is_deleted=0 and a.job_no_mst='".$job_arr[$style_db_id]."' group by b.color_name");
		?>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" width="400" align="left">
        		<tr>
        			<input id="hidden_color_name" type="hidden" name="hidden_color_name">
        			<th width="50">SL</th>
        			<th width="350">Color Name</th>
        		</tr>
            </table>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" width="400" align="left" id="list_view">
            	<tbody style="max-height:400px;display:block;overflow-y:scroll">
            	<? $i=1;
            	 foreach ($color_arr as $color) {  ?>
            		<tr id="color_<? echo $i; ?>" onClick="js_set_value(<? echo $i; ?>)">
            			<td width="50"><? echo $i; ?></td>
        				<td width="380">
        					<? echo $color[csf('color_name')];  ?>
        					<input type="hidden" name="color_name" value="<? echo $color[csf('color_name')]; ?>" id="colorName_<? echo $i; ?>">
        					</td>
        			</tr>
            	<? $i++; } ?>
            	</tbody>
            </table>
            <div style="text-align: center;padding-top: 10px">
            	<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
            </div>
		<?
	}
	else
	{
		$color_arr= sql_select("select id, color_name from lib_color");
		?>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" width="400" align="left">
        		<tr>
        			<input id="hidden_color_name" type="hidden" name="hidden_color_name">
        			<th width="50">SL</th>
        			<th width="350">Color Name</th>
        		</tr>
            </table>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" width="400" align="left" id="list_view">
            	<tbody style="max-height:400px;display:block;overflow-y:scroll">
            	<? $i=1;
            	 foreach ($color_arr as $color) {  ?>
            		<tr id="color_<? echo $i; ?>" onClick="js_set_value(<? echo $i; ?>)">
            			<td width="50"><? echo $i; ?></td>
        				<td width="380">
        					<? echo $color[csf('color_name')];  ?>
        					<input type="hidden" name="color_name" value="<? echo $color[csf('color_name')]; ?>" id="colorName_<? echo $i; ?>">
        					</td>
        			</tr>
            	<? $i++; } ?>
            	</tbody>
            </table>
            <div style="text-align: center;padding-top: 10px">
            	<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
            </div>
		<?
	}
	?>
	<script type="text/javascript">
		setFilterGrid("list_view",-1);
	</script>
	<?
	exit();
}


if ($action=="save_update_delete_sample_details")
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
 			$id_dtls=return_next_id( "id", "sample_development_dtls", 1 ) ;
 			$field_array= "id,sample_mst_id,sample_name,gmts_item_id,smv,article_no,sample_color,sample_prod_qty,submission_qty,delv_start_date,delv_end_date,sample_charge,sample_curency,inserted_by,insert_date,status_active,is_deleted,entry_form_id,size_data,fabric_status,acc_status,embellishment_status,fab_status_id,acc_status_id,embellishment_status_id";

			$ids=return_next_id( "id","sample_development_size", 1 ) ;
			$field_array_size="id, mst_id, dtls_id,size_id,bh_qty,plan_qty,dyeing_qty,test_qty,self_qty,total_qty,inserted_by, insert_date, status_active, is_deleted";

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
				$txtChargeUnit="txtChargeUnit_".$i;
				$cboCurrency="cboCurrency_".$i;
				$txtAllData="txtAllData_".$i;
				//$updateIdDtls="updateidsampledtl_".$i;

				if(str_replace("'","",$$txtColor)!="")
				{
					if (!in_array(str_replace("'","",$$txtColor),$new_array_color))
					{
						$color_id = return_id( str_replace("'","",$$txtColor), $color_arr, "lib_color", "id,color_name","117");
						//echo $$txtColorName.'='.$color_id.'<br>';
						$new_array_color[$color_id]=str_replace("'","",$$txtColor);

					}
					else $color_id =  array_search(str_replace("'","",$$txtColor), $new_array_color);
				}
				else
				{
					$color_id=0;
				}


				if ($i!=1) $data_array .=",";
				$data_array .="(".$id_dtls.",".$update_id.",".$$cboSampleName.",".$$cboGarmentItem.",".$$txtSmv.",".$$txtArticle.",'".$color_id."',".$$txtSampleProdQty.",".$$txtSubmissionQty.",".$$txtDelvStartDate.",".$$txtDelvEndDate.",".$$txtChargeUnit.",".$$cboCurrency.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0,117,".$$txtAllData.",0,0,0,0,0,0)";


				$countsize=0; $ex_data="";

				$ex_data=explode("__",str_replace("'","",$$txtAllData));
				$countsize=count($ex_data);

				$data_array_size.='';
				/*for($i=1;$i<=$countsize; $i++)
				{*/
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
							$size_id = return_id( $size_name, $size_arr, "lib_size", "id,size_name","117");
							//echo $$txtColorName.'='.$color_id.'<br>';
							$new_array_size[$size_id]=str_replace("'","",$size_name);

						}
						else $size_id =  array_search($size_name, $new_array_size);
					}
					else
					{
						$size_id=0;
					}


					if($i==1) $add_comma=""; else $add_comma=",";
				//	$field_array_size="id, mst_id, dtls_id,size_id,bh_qty,plan_qty,dyeing_qty,test_qty,self_qty,total_qty,inserted_by, insert_date, status_active, is_deleted";

					$data_array_size.="$add_comma(".$ids.",".$update_id.",".$id_dtls.",".$size_id.",".$bhqty.",".$plqty.",".$dyqty.",".$testqty.",".$selfqty.",".$totalqty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
					$ids=$ids+1;
				}
				$id_dtls=$id_dtls+1;
				//echo "insert into sample_development_size (".$field_array_size.") Values ".$data_array_size."";die;

		    }

 			//echo "5**"."INSERT INTO sample_development_size(".$field_array_size.")VALUES ".$data_array_size; die;
			$rID_1=sql_insert("sample_development_dtls",$field_array,$data_array,0);
			$rIDs=sql_insert("sample_development_size",$field_array_size,$data_array_size,0);

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

			$field_array_up="sample_name*gmts_item_id*smv*article_no*sample_color*sample_prod_qty*submission_qty*delv_start_date*delv_end_date*sample_charge*sample_curency*updated_by*update_date*size_data";

			$field_array= "id, sample_mst_id, sample_name, gmts_item_id, smv,article_no, sample_color, sample_prod_qty, submission_qty, delv_start_date, delv_end_date, sample_charge, sample_curency, inserted_by, insert_date, status_active, is_deleted, entry_form_id, size_data,fabric_status,acc_status,embellishment_status,fab_status_id,acc_status_id,embellishment_status_id";
			$ids=return_next_id( "id","sample_development_size", 1 ) ;
			$field_array_size="id, mst_id, dtls_id,size_id,bh_qty,plan_qty,dyeing_qty,test_qty,self_qty,total_qty,inserted_by, insert_date, status_active, is_deleted";

			$add_comma=0; $data_array=""; //echo "10**";
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
				$txtChargeUnit="txtChargeUnit_".$i;
				$cboCurrency="cboCurrency_".$i;
				$updateIdDtls="updateidsampledtl_".$i;
				$txtAllData="txtAllData_".$i;

				if(str_replace("'","",$$txtColor)!="")
				{
					if (!in_array(str_replace("'","",$$txtColor),$new_array_color))
					{
						$color_id = return_id( str_replace("'","",$$txtColor), $color_arr, "lib_color", "id,color_name","117");
						//echo $$txtColorName.'='.$color_id.'<br>';
						$new_array_color[$color_id]=str_replace("'","",$$txtColor);

					}
					else $color_id =  array_search(str_replace("'","",$$txtColor), $new_array_color);
				}
				else
				{
					$color_id=0;
				}
				//echo str_replace("'",'',$$updateIdDtls);

				if (str_replace("'",'',$$updateIdDtls)!="")
				{
					$id_arr[]=str_replace("'",'',$$updateIdDtls);

					$data_array_up[str_replace("'",'',$$updateIdDtls)] =explode("*",("".$$cboSampleName."*".$$cboGarmentItem."*".$$txtSmv."*".$$txtArticle."*'".$color_id."'*".$$txtSampleProdQty."*".$$txtSubmissionQty."*".$$txtDelvStartDate."*".$$txtDelvEndDate."*".$$txtChargeUnit."*".$$cboCurrency."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'*".$$txtAllData.""));

				$countsize=0; $ex_data="";
				$ex_data=explode("__",str_replace("'","",$$txtAllData));
				$countsize=count($ex_data);

				$data_array_size.='';
				/*for($i=1;$i<=$countsize; $i++)
				{*/
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
								$size_id = return_id( $size_name, $size_arr, "lib_size", "id,size_name");
								//echo $$txtColorName.'='.$color_id.'<br>';
								$new_array_size[$size_id]=str_replace("'","",$size_name);

							}
							else $size_id =  array_search($size_name, $new_array_size);
						}
						else
						{
							$size_id=0;
						}


						if($i==1) $add_comma=""; else $add_comma=",";

						$data_array_size.="$add_comma(".$ids.",".$update_id.",".$$updateIdDtls.",".$size_id.",".$bhqty.",".$plqty.",".$dyqty.",".$testqty.",".$selfqty.",".$totalqty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
						$ids=$ids+1;
					}
				}
			 	else
				{
					if ($add_comma!=0) $data_array .=",";
					$data_array .="(".$id_dtls.",".$update_id.",".$$cboSampleName.",".$$cboGarmentItem.",".$$txtSmv.",".$$txtArticle.",'".$color_id."',".$$txtSampleProdQty.",".$$txtSubmissionQty.",".$$txtDelvStartDate.",".$$txtDelvEndDate.",".$$txtChargeUnit.",".$$cboCurrency.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0,117,".$$txtAllData.",0,0,0,0,0,0)";

				$countsize=0; $ex_data="";
				$ex_data=explode("__",str_replace("'","",$$txtAllData));
				$countsize=count($ex_data);

				$data_array_size.='';
				/*for($i=1;$i<=$countsize; $i++)
				{*/
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
							$size_id = return_id( $size_name, $size_arr, "lib_size", "id,size_name");
							//echo $$txtColorName.'='.$color_id.'<br>';
							$new_array_size[$size_id]=str_replace("'","",$size_name);

						}
						else $size_id =  array_search($size_name, $new_array_size);
					}
					else
					{
						$size_id=0;
					}


					if($i==1) $add_comma=""; else $add_comma=",";

					$data_array_size.="$add_comma(".$ids.",".$update_id.",".$id_dtls.",".$size_id.",".$bhqty.",".$plqty.",".$dyqty.",".$testqty.",".$selfqty.",".$totalqty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
					$ids=$ids+1;
				}


					$id_dtls=$id_dtls+1;
					$add_comma++;
				}




		    }
			//echo $data_array.'=='; die;
			//$rID_1=sql_insert("sample_development_dtls",$field_array2,$data_array2,1);

			$flag=1;
			if($data_array!="")
			{
				$rID_dtls=sql_insert("sample_development_dtls",$field_array,$data_array,0);
				$rID_size=sql_insert("sample_development_size",$field_array_size,$data_array_size,0);
				if($rID_dtls && $rID_size) $flag=1; else $flag=0;
			}
			/*echo '=='.$data_array.'==';
			die;*/
			if($data_array_up!="")
			{
				$rID_size_dlt=execute_query( "update sample_development_size set status_active=0,is_deleted=1 ,updated_by=".$_SESSION['logic_erp']['user_id'].",update_date='".$pc_date_time."' where mst_id=$update_id",0);
				$rIDs=sql_insert("sample_development_size",$field_array_size,$data_array_size,0);
				$rID1=execute_query(bulk_update_sql_statement("sample_development_dtls", "id",$field_array_up,$data_array_up,$id_arr ));
				if($rID1) $flag=1; else $flag=0;
			}

			if($txtDeltedIdSd!="" || $txtDeltedIdSd!=0)
			{

				$fields="status_active*is_deleted";
				$delDtls=sql_multirow_update("sample_development_dtls",$fields,"0*1","id",$txtDeltedIdSd,0);
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

		$is_approved=return_field_value("is_approved","sample_development_mst","id=$update_id and entry_form_id=117 and status_active=1 and is_deleted=0");
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

		$rID=sql_delete("sample_development_dtls",$field_array,$data_array,"sample_mst_id*entry_form_id","".$update_id."*117",0);
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

	if ($operation==0)  // Insert Here
	{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}

			$id_dtls=return_next_id( "id", "sample_development_fabric_acc", 1 ) ;
			$field_array= "id,sample_mst_id,sample_name,gmts_item_id,body_part_id,fabric_nature_id,fabric_description,gsm,dia,color_data,color_type_id,width_dia_id,uom_id,fin_fab_qnty,grey_fab_qnty,process_loss_percent,required_dzn,required_qty,inserted_by,insert_date,status_active,is_deleted,form_type,determination_id";


			//$field_array_col="id, mst_id, dtls_id,color_id,contrast,inserted_by, insert_date, status_active, is_deleted";
			$field_array_col="id, mst_id, dtls_id,color_id,contrast,fabric_color,qnty,process_loss_percent,grey_fab_qnty,inserted_by, insert_date, status_active, is_deleted";
			$idColorTbl=return_next_id( "id","sample_development_rf_color", 1 ) ;
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
				$txtRfReqQty="txtRfReqQty_".$i;
				$txtGrayFabric="txtGrayFabric_".$i;
				$txtProcessLoss="txtProcessLoss_".$i;
				$txtRfColorAllData="txtRfColorAllData_".$i;
				$libyarncountdeterminationid="libyarncountdeterminationid_".$i;


				if ($i!=1) $data_array .=",";
				//
				$data_array .="(".$id_dtls.",".$update_id.",".$$cboRfSampleName.",".$$cboRfGarmentItem.",".$$cboRfBodyPart.",".$$cboRfFabricNature.",".$$txtRfFabricDescription.",".$$txtRfGsm.",".$$txtRfDia.",".$$txtRfColorAllData.",".$$cboRfColorType.",".$$cboRfWidthDia.",".$$cboRfUom.",".$$txtRfReqQty.",".$$txtGrayFabric.",".$$txtProcessLoss.",".$$txtRfReqDzn.",".$$txtRfReqQty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0,1, ".$$libyarncountdeterminationid.")";
				$rId_rf_status=execute_query( "update sample_development_dtls set fabric_status=1,fab_status_id=".$id_dtls." where sample_mst_id=$update_id and sample_name=".$$cboRfSampleName."",0);

				$id_dtls=$id_dtls+1;
  				$data_array_col.='';
				$ex_data="";
 				$ex_data=explode("-----",str_replace("'","",$$txtRfColorAllData));
				$kk=1;
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
					
 					// if($i==1) $add_comma=""; else $add_comma=",";
						if($kk==1) $add_comma=""; else $add_comma=",";
 					//if ($i!=1) $add_comma .=",";
  					$data_array_col.="$add_comma(".$idColorTbl.",".$update_id.",".$id_dtls.",".$colorId.",'".$contrast."','".$fab_color_id."','".$qnty."','".$txtProcessLoss."','".$txtGrayFabric."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
					
					$idColorTbl = $idColorTbl + 1;
					$kk++;
				}
		    }
			$rID_1=sql_insert("sample_development_fabric_acc",$field_array,$data_array,1);
			$rIDs=sql_insert("sample_development_rf_color",$field_array_col,$data_array_col,1);
			// echo "10**insert into sample_development_rf_color (".$field_array_col.") values ".$data_array_col;die;
			//echo "10**".$rID_1.'='.$rIDs."";die;
 			if($db_type==0)
			{
				if($rID_1){
					mysql_query("COMMIT");
					echo "0**".str_replace("'",'',$update_id)."**2";
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
					echo "0**".str_replace("'",'',$update_id)."**2";

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

			$field_array_up="sample_name*gmts_item_id*body_part_id*fabric_nature_id*fabric_description*gsm*dia*color_data*color_type_id*width_dia_id*uom_id*fin_fab_qnty*grey_fab_qnty*process_loss_percent*required_dzn*required_qty*updated_by*update_date*determination_id";

			$field_array= "id,sample_mst_id,sample_name,gmts_item_id,body_part_id,fabric_nature_id,fabric_description,gsm,dia,color_data,color_type_id,width_dia_id,uom_id,required_dzn,required_qty,inserted_by,insert_date,status_active,is_deleted,form_type,determination_id";
			$idColorTbl=return_next_id( "id","sample_development_rf_color", 1 ) ;
			//$field_array_col="id, mst_id, dtls_id,color_id,contrast,inserted_by, insert_date, status_active, is_deleted";
			$field_array_col="id, mst_id, dtls_id,color_id,contrast,fabric_color,qnty,process_loss_percent,grey_fab_qnty,inserted_by, insert_date, status_active, is_deleted";

			$add_comma=0; $data_array=""; //echo "10**";
			$pp=0;
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
				$txtRfReqQty="txtRfReqQty_".$i;
				$updateidRequiredDtlf="updateidRequiredDtl_".$i;
				$txtRfColorAllData="txtRfColorAllData_".$i;
				$libyarncountdeterminationid="libyarncountdeterminationid_".$i;
				$txtGrayFabric="txtGrayFabric_".$i;
				$txtProcessLoss="txtProcessLoss_".$i;

  				if (str_replace("'",'',$$updateidRequiredDtlf)!="")
				{
					$id_arr[]=str_replace("'",'',$$updateidRequiredDtlf);

					$data_array_up[str_replace("'",'',$$updateidRequiredDtlf)] =explode("*",("".$$cboRfSampleName."*".$$cboRfGarmentItem."*".$$cboRfBodyPart."*".$$cboRfFabricNature."*".$$txtRfFabricDescription."*".$$txtRfGsm."*".$$txtRfDia."*".$$txtRfColorAllData."*".$$cboRfColorType."*".$$cboRfWidthDia."*".$$cboRfUom."*".$$txtRfReqQty."*".$$txtGrayFabric."*".$$txtProcessLoss."*".$$txtRfReqDzn."*".$$txtRfReqQty."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'*".$$libyarncountdeterminationid.""));

					$rId_rf_status=execute_query( "update sample_development_dtls set fabric_status=0 where sample_mst_id=$update_id and fab_status_id=".$$updateidRequiredDtlf."",0);
					$rId_rf_status_ac=execute_query( "update sample_development_dtls set fabric_status=1,fab_status_id=".$$updateidRequiredDtlf." where sample_mst_id=$update_id and sample_name=".$$cboRfSampleName."",0);

					$ex_data="";
					$ex_data=explode("-----",str_replace("'","",$$txtRfColorAllData));
					//print_r($ex_data);
					//echo "10**G";die;
					//$data_array_col.='';
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
 					//if($i==1) $add_comma=""; else $add_comma=",";
					if ($add_comma!=0) $data_array_col .=",";
 					$data_array_col.="(".$idColorTbl.",".$update_id.",".$$updateidRequiredDtlf.",".$colorId.",'".$contrast."','".$fab_color_id."','".$qnty."','".$txtProcessLoss."','".$txtGrayFabric."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
					$idColorTbl=$idColorTbl+1;
					$add_comma++;
					}
				}
			 	else
				{
 					if ($add_comma!=0) $data_array .=",";
					$data_array .="(".$id_dtls.",".$update_id.",".$$cboRfSampleName.",".$$cboRfGarmentItem.",".$$cboRfBodyPart.",".$$cboRfFabricNature.",".$$txtRfFabricDescription.",".$$txtRfGsm.",".$$txtRfDia.",".$$txtRfColorAllData.",".$$cboRfColorType.",".$$cboRfWidthDia.",".$$cboRfUom.",".$$txtRfReqDzn.",".$$txtRfReqQty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0,1,".$$libyarncountdeterminationid.")";
					$rId_rf_status=execute_query( "update sample_development_dtls set fabric_status=1,fab_status_id=".$id_dtls." where sample_mst_id=$update_id and sample_name=".$$cboRfSampleName."",0);

  					$ex_datas="";
					$ex_datas=explode("-----",str_replace("'","",$$txtRfColorAllData));
					$data_array_cols.='';
					$kk=1;
  					foreach($ex_datas as $color_datas)
					{
 					$ex_size_data=explode("_",$color_datas);
 					$colorName=$ex_size_data[1];
					$colorId=$ex_size_data[2];
					$contrast=$ex_size_data[3];
					$qnty=$ex_size_data[4];
					$txtProcessLoss=$ex_size_data[5];
					$txtGrayFabric=$ex_size_data[6];
					$fab_color_id=$ex_size_data[7];
 					//if($i==1) $add_comma=""; else $add_comma=",";
					if($kk==1) $add_comma=""; else $add_comma=",";
 					$data_array_cols.="$add_comma(".$idColorTbl.",".$update_id.",".$id_dtls.",".$colorId.",'".$contrast."','".$fab_color_id."','".$qnty."','".$txtProcessLoss."','".$txtGrayFabric."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
					$idColorTbl=$idColorTbl+1;
						$kk++;

					}
					$id_dtls=$id_dtls+1;
					$rId_rf_status=execute_query( "update sample_development_dtls set fabric_status=1 where sample_mst_id=$update_id and sample_name=".$$cboRfSampleName."",0);
					$add_comma++;
				}


		    }
			
 			$flag=1;
 			if($data_array_up!="")
			{
				$rID_size_dlt=execute_query( "delete from sample_development_rf_color where mst_id=$update_id",0);
				$rIDs=sql_insert("sample_development_rf_color",$field_array_col,$data_array_col,1);
				// echo "10**insert into sample_development_rf_color (".$field_array_col.") values ".$data_array_col;die;
				$rID1=execute_query(bulk_update_sql_statement("sample_development_fabric_acc", "id",$field_array_up,$data_array_up,$id_arr ));
				if($rIDs && $rID1) $flag=1; else $flag=0;
			}

			
			if($data_array!="")
			{
				
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
			elseif($db_type==2 || $db_type==1 )
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

	if ($operation==2)  // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$non_ord_booking=return_field_value("id","wo_non_ord_samp_booking_dtls","style_id=$update_id and entry_form_id=140 and status_active=1 and is_deleted=0");
		$ord_booking=return_field_value("id","wo_booking_dtls","style_id=$update_id and entry_form_id=139 and status_active=1 and is_deleted=0");
		$is_approved=return_field_value("is_approved","sample_development_mst","id=$update_id and entry_form_id=117 and status_active=1 and is_deleted=0");
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
			$field_array= "id,sample_mst_id,sample_name_ra,gmts_item_id_ra,trims_group_ra,description_ra,brand_ref_ra,uom_id_ra,req_dzn_ra,req_qty_ra,remarks_ra,inserted_by,insert_date,status_active,is_deleted,form_type";
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


				if ($i!=1) $data_array .=",";
				$data_array .="(".$id_dtls.",".$update_id.",".$$cboRaSampleName.",".$$cboRaGarmentItem.",".$$cboRaTrimsGroup.",".$$txtRaDescription.",".$$txtRaBrandSupp.",".$$cboRaUom.",".$$txtRaReqDzn.",".$$txtRaReqQty.",".$$txtRaRemarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0,2)";
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
			$field_array_up="sample_name_ra*gmts_item_id_ra*trims_group_ra*description_ra*brand_ref_ra*uom_id_ra*req_dzn_ra*req_qty_ra*remarks_ra*updated_by*update_date";
			$field_array= "id,sample_mst_id,sample_name_ra,gmts_item_id_ra,trims_group_ra,description_ra,brand_ref_ra,uom_id_ra,req_dzn_ra,req_qty_ra,remarks_ra,inserted_by,insert_date,status_active,is_deleted,form_type";
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


				if (str_replace("'",'',$$updateIdAccDtls)!="")
				{
					$id_arr[]=str_replace("'",'',$$updateIdAccDtls);

					$data_array_up[str_replace("'",'',$$updateIdAccDtls)] =explode("*",("".$$cboRaSampleName."*".$$cboRaGarmentItem."*".$$cboRaTrimsGroup."*".$$txtRaDescription."*".$$txtRaBrandSupp."*".$$cboRaUom."*".$$txtRaReqDzn."*".$$txtRaReqQty."*".$$txtRaRemarks."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
					$rId_acc_status=execute_query( "update sample_development_dtls set acc_status=0 where sample_mst_id=$update_id and acc_status_id=".$$updateIdAccDtls."",0);
					$rId_acc_status_ac=execute_query( "update sample_development_dtls set acc_status=1,acc_status_id=".$$updateIdAccDtls." where sample_mst_id=$update_id and sample_name=".$$cboRaSampleName."",0);
				}
			 	else
				{
					if ($add_comma!=0) $data_array .=",";
					$data_array .="(".$id_dtls.",".$update_id.",".$$cboRaSampleName.",".$$cboRaGarmentItem.",".$$cboRaTrimsGroup.",".$$txtRaDescription.",".$$txtRaBrandSupp.",".$$cboRaUom.",".$$txtRaReqDzn.",".$$txtRaReqQty.",".$$txtRaRemarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0,2)";
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

		$is_approved=return_field_value("is_approved","sample_development_mst","id=$update_id and entry_form_id=117 and status_active=1 and is_deleted=0");
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
 			$field_array= "id,sample_mst_id,sample_name_re,gmts_item_id_re,name_re,type_re,remarks_re,inserted_by,insert_date,status_active,is_deleted,form_type";
			for ($i=1;$i<=$total_row;$i++)
		    {
				$cboReSampleName="cboReSampleName_".$i;
				$cboReGarmentItem="cboReGarmentItem_".$i;
				$cboReName="cboReName_".$i;
				$cboReType="cboReType_".$i;
				$cboReRemarks="txtReRemarks_".$i;
				//$updateIdDtls="updateidRequiredEmbellishdtl_".$i;
			// fab_status_id,acc_status_id,embellishment_status_id
				if ($i!=1) $data_array .=",";
				$data_array .="(".$id_dtls.",".$update_id.",".$$cboReSampleName.",".$$cboReGarmentItem.",".$$cboReName.",".$$cboReType.",".$$cboReRemarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0,3)";

				$rId_emb_status=execute_query( "update sample_development_dtls set embellishment_status=1,embellishment_status_id=".$id_dtls." where sample_mst_id=$update_id and sample_name=".$$cboReSampleName."",0);
				$id_dtls=$id_dtls+1;

		    }
 			//echo "5**"."INSERT INTO sample_development_dtls(".$field_array."VALUES ".$data_array; die;
			$rID_1=sql_insert("sample_development_fabric_acc",$field_array,$data_array,1);

			if($db_type==0)
			{
				if($rID_1){
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
				if($rID_1)
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

			$field_array_up="sample_name_re*gmts_item_id_re*name_re*type_re*remarks_re*updated_by*update_date";
			$field_array= "id, sample_mst_id, sample_name_re,gmts_item_id_re,name_re,type_re,remarks_re,inserted_by,insert_date,status_active,is_deleted,form_type";
			$add_comma=0; $data_array=""; //echo "10**";
			for ($i=1;$i<=$total_row;$i++)
		    {
				$cboReSampleName="cboReSampleName_".$i;
				$cboReGarmentItem="cboReGarmentItem_".$i;
				$cboReName="cboReName_".$i;
				$cboReType="cboReType_".$i;
				$cboReRemarks="txtReRemarks_".$i;
				$updateIdDtls="updateidRequiredEmbellishdtl_".$i;


				if (str_replace("'",'',$$updateIdDtls)!="")
				{
					$id_arr[]=str_replace("'",'',$$updateIdDtls);

					$data_array_up[str_replace("'",'',$$updateIdDtls)] =explode("*",("".$$cboReSampleName."*".$$cboReGarmentItem."*".$$cboReName."*".$$cboReType."*".$$cboReRemarks."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
					$rId_emb_status=execute_query( "update sample_development_dtls set embellishment_status=0 where sample_mst_id=$update_id and embellishment_status_id=".$$updateIdDtls."",0);
					$rId_emb_status_ac=execute_query( "update sample_development_dtls set embellishment_status=1,embellishment_status_id=".$$updateIdDtls." where sample_mst_id=$update_id and sample_name=".$$cboReSampleName."",0);
				}
			 	else
				{
					if ($add_comma!=0) $data_array .=",";
					$data_array .="(".$id_dtls.",".$update_id.",".$$cboReSampleName.",".$$cboReGarmentItem.",".$$cboReName.",".$$cboReType.",".$$cboReRemarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0,3)";
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

			if($txtDeltedIdRe!="" || $txtDeltedIdRe!=0)
			{

				$fields="is_deleted";
				$fields_sd="embellishment_status";
				$delSampleDtls=sql_multirow_update("sample_development_dtls",$fields_sd,"0","embellishment_status_id",$txtDeltedIdRe,0);
				// echo $delSampleDtls;die;
				$del=sql_multirow_update("sample_development_fabric_acc",$fields,"1","id",$txtDeltedIdRe,0);
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

	if ($operation==2)  // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$is_approved=return_field_value("is_approved","sample_development_mst","id=$update_id and entry_form_id=117 and status_active=1 and is_deleted=0");
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

if($action=="load_php_style_ref")
{
	$ex_data = explode("**",$data);
	$buyer_id=$ex_data[0];
	$style_name=$ex_data[1];
	$style=sql_select("select id from lib_style_ref where style_ref_name='".$style_name."' and buyer_id =$buyer_id");
	if( count($style) > 0){
		foreach ($style as $result){
			$txt_style_ref_id=$result[csf('id')];
		}
		echo $txt_style_ref_id;
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
	
	$sql_devlop=sql_select("select id,buyer_name,company_id from sample_development_mst where is_deleted=0 and status_active=1 and id=$up_id order by buyer_name");
	foreach($sql_devlop as $result)
	{
		//$buyer_aganist_req[$result[csf('id')]]=$result[csf('buyer_name')];
		$company_id=$result[csf('company_id')];
	}
	
	$sql_result = sql_select("select variable_list, color_from_library from variable_order_tracking where company_name=$company_id and variable_list in (23) and status_active=1 and is_deleted=0");
	$color_from_lib=0;
 	foreach($sql_result as $result)
	{
		if($result[csf('variable_list')]==23) $color_from_lib=$result[csf('color_from_library')];
	}

	if($type==1)
	{
		$sql_sam="SELECT id, sample_name, gmts_item_id, smv,article_no, sample_color, sample_prod_qty, submission_qty, delv_start_date, delv_end_date, sample_charge, sample_curency, size_data,fabric_status,acc_status,embellishment_status from sample_development_dtls where entry_form_id=117 and sample_mst_id='$up_id' and  is_deleted=0  and status_active=1 order by id ASC";
		$value=return_field_value("quotation_id","sample_development_mst","entry_form_id=117 and id='$up_id' and status_active=1 and is_deleted=0");
		$sql_result =sql_select($sql_sam);  $i=1;
		if(count($sql_result)>0)
		{
			foreach($sql_result as $row)
			{
				?>
				<tr id="tr_<? echo $i; ?>" style="height:10px;" class="general" colorData=<? echo $i;?>>
					<td>
						<?
						if($row[csf("fabric_status")]==1 || $row[csf("acc_status")]==1 || $row[csf("embellishment_status")]==1)
						{
 	               //$sql="select id,sample_name from  lib_sample where  status_active=1 and is_deleted=0";
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
						if($row[csf("fabric_status")]==1 || $row[csf("acc_status")]==1 || $row[csf("embellishment_status")]==1)
						{

	               // echo create_drop_down( "cboGarmentItem_$i", 100, $garments_item,"", 1, "Select Item",$row[csf("gmts_item_id")], "",1,"");
							if($value=="" || $value==0)
							{
								echo create_drop_down( "cboGarmentItem_$i", 100, get_garments_item_array(3),"", 1, "Select Item",$row[csf("gmts_item_id")], "",1,"");
							}
							else
							{
								echo create_drop_down( "cboGarmentItem_$i", 100, get_garments_item_array(3),"", 1, "Select Item",$row[csf("gmts_item_id")], "",1,$row[csf("gmts_item_id")]);
							}
						}
						else
						{
							if($value=="" || $value==0)
							{
								echo create_drop_down( "cboGarmentItem_$i", 100, get_garments_item_array(3),"", 1, "Select Item",$row[csf("gmts_item_id")], "",0,"");
							}
							else
							{
								echo create_drop_down( "cboGarmentItem_$i", 100, get_garments_item_array(3),"", 1, "Select Item",$row[csf("gmts_item_id")], "",0,$row[csf("gmts_item_id")]);
							}
						}
						?>

					</td>
					<td>
						<input style="width:40px;" type="text" class="text_boxes_numeric" name="txtSmv_<? echo $i; ?>" id="txtSmv_<? echo $i; ?>" value="<? echo $row[csf("smv")]; ?>"/>
						<input type="hidden" id="updateidsampledtl_<? echo $i; ?>" name="updateidsampledtl_<? echo $i; ?>" style="width:20px" value="<? echo $row[csf("id")]; ?>" />
					</td>
					<input type="hidden" id="txtDeltedIdSd" name="txtDeltedIdSd"  class="text_boxes" style="width:20px" value="" />
					<td><input style="width:60px;" type="text" class="text_boxes"  name="txtArticle_<? echo $i; ?>" id="txtArticle_<? echo $i; ?>" placeholder="write" value="<? echo $row[csf("article_no")]; ?>" /></td>
					<td>
                    <?
                    if($color_from_lib==1)
					{ ?>
						<input style="width:70px;" type="text" class="text_boxes"  name="txtColor_<? echo $i; ?>" id="txtColor_<? echo $i; ?>" onDblClick="openmypage_color_size('requires/sample_requisition_controller.php?action=color_popup','Color Search','1','<? echo $i; ?>');" value="<? echo $color_arr[$row[csf("sample_color")]]; ?>"/>
					<? }
					else{ ?>
						<input style="width:70px;" type="text" class="text_boxes"  name="txtColor_<? echo $i; ?>" id="txtColor_<? echo $i; ?>" value="<? echo $color_arr[$row[csf("sample_color")]]; ?>"/>
					<? }
					?>	
					<td>
						<?
						if($row[csf("fabric_status")]==1 || $row[csf("acc_status")]==1 || $row[csf("embellishment_status")]==1)
						{
							?>
							<input style="width:100px;" type="text" class="text_boxes_numeric"  name="txtSampleProdQty_<? echo $i; ?>" readonly id="txtSampleProdQty_<? echo $i; ?>" placeholder="browse"  ondblclick="openmypage_sizeinfo('requires/sample_requisition_controller.php?action=sizeinfo_popup','Size Search','<? echo $i;?>')" value="<? echo $row[csf("sample_prod_qty")]; ?>" onFocus="openmypage_sizeinfo('requires/sample_requisition_controller.php?action=sizeinfo_popup_mouseover','Size Search','<? echo $i;?>')"   />

							<?
						}
						else {
							?>
							<input style="width:100px;" type="text" class="text_boxes_numeric"  name="txtSampleProdQty_<? echo $i; ?>" readonly id="txtSampleProdQty_<? echo $i; ?>" placeholder="browse"   ondblclick="openmypage_sizeinfo('requires/sample_requisition_controller.php?action=sizeinfo_popup','Size Search','<? echo $i;?>')"  value="<? echo $row[csf("sample_prod_qty")]; ?>"/>
							<?
						}
						?>

					</td>

					<input type="hidden" class="text_boxes"  name="txtAllData_<? echo $i;?>" id="txtAllData_<? echo $i;?>" value="<? echo $row[csf("size_data")]; ?>"/>

					<td><input style="width:100px;" type="text" class="text_boxes_numeric"  name="txtSubmissionQty_<? echo $i; ?>" readonly id="txtSubmissionQty_<? echo $i; ?>" placeholder=""  value="<? echo $row[csf("submission_qty")]; ?>" /></td>
					<td><input style="width:85px;" class="datepicker" name="txtDelvStartDate_<? echo $i; ?>" id="txtDelvStartDate_<? echo $i; ?>" value="<? echo change_date_format($row[csf("delv_start_date")]); ?>"/></td>
					<td><input style="width:85px;" class="datepicker" name="txtDelvEndDate_<? echo $i; ?>" id="txtDelvEndDate_<? echo $i; ?>" value="<? echo change_date_format($row[csf("delv_end_date")]); ?>" /></td>
					<td><input style="width:70px;" type="text" class="text_boxes_numeric"  name="txtChargeUnit_<? echo $i; ?>" id="txtChargeUnit_<? echo $i; ?>" placeholder="write" value="<? echo $row[csf("sample_charge")]; ?>"/></td>
					<td><? echo create_drop_down( "cboCurrency_$i", 70, $currency, "","","",$row[csf("sample_curency")], "", "", "" ); ?></td>
					<td><input type="button" class="image_uploader" name="txtFile_<? echo $i; ?>" id="txtFile_<? echo $i; ?>" size="10" value="ADD IMAGE" onClick="file_uploader ( '../../', document.getElementById('updateidsampledtl_<? echo $i;?>').value,'', 'sample_details_1', 0 ,1)"></td>
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

	}

	else if($type==2)
	{
		$sql_fabric="SELECT id,sample_mst_id,sample_name,gmts_item_id,body_part_id,fabric_nature_id,fabric_description,gsm,dia,sample_color,color_type_id,width_dia_id,uom_id,required_dzn,grey_fab_qnty,process_loss_percent,required_qty,color_data, determination_id from sample_development_fabric_acc where sample_mst_id='$up_id' and form_type=1 and  is_deleted=0  and status_active=1 order by id ASC";
		$sql_resultf =sql_select($sql_fabric);  $i=1;
		if(count($sql_resultf)>0)
		{
			foreach($sql_resultf as $row)
			{
				?>



				<tr id="tr_<? echo $i; ?>" style="height:10px;" class="general">
					<td align="center" id="rfSampleId_1">
						<?
						$sql="select a.id,a.sample_name,b.id as dtls_id from  lib_sample a ,sample_development_dtls b where  a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.id=b.sample_name and b.entry_form_id=117 and b.sample_mst_id='$up_id' group by a.id,a.sample_name,b.id order by b.id";
						echo create_drop_down( "cboRfSampleName_$i", 95, $sql,"id,sample_name", '', "", $row[csf("sample_name")],"");
						?>

					</td>
					<td align="center" id="rfItemId_1">
						<?
						$sql_f=sql_select("select id,gmts_item_id from sample_development_dtls where is_deleted=0  and status_active=1 and entry_form_id=117 and sample_mst_id='$up_id'");
						$gmtsf="";
						foreach ($sql_f as $rowf)
						{
							$gmtsf.=$rowf[csf("gmts_item_id")].",";
						}
						echo create_drop_down( "cboRfGarmentItem_$i", 95, $garments_item,"", 1, "Select Item",$row[csf("gmts_item_id")],"","",$gmtsf);

						?>

					</td>
					<td align="center" id="rf_body_part_1">
						<?
						echo create_drop_down( "cboRfBodyPart_$i", 95, $body_part,"", 1, "Select Body Part", $row[csf("body_part_id")], "load_data_to_rfcolor('$i');");

						?>
					</td>
					<td align="center" id="rf_fabric_nature_1">
						<?
						echo create_drop_down( "cboRfFabricNature_$i", 95, $item_category,"", 0, "Select Fabric Nature",$row[csf("fabric_nature_id")] , "","","2,3");

						?>

					</td>
					<td align="center" id="rf_fabric_description_1">
						<input style="width:60px;" type="text" class="text_boxes"  name="txtRfFabricDescription_<? echo $i; ?>" id="txtRfFabricDescription_<? echo $i; ?>" placeholder="write/browse" onDblClick="open_fabric_description_popup(<? echo $i; ?>)" readonly value="<? echo $row[csf("fabric_description")]; ?>"/>
						<input type="hidden" name="libyarncountdeterminationid_<? echo $i; ?>" id="libyarncountdeterminationid_<? echo $i; ?>" class="text_boxes" style="width:10px" value="<? echo $row[csf("determination_id")]; ?>">
					</td>

					<td align="center" id="rf_gsm_1">
						<input style="width:40px;" type="text" class="text_boxes_numeric"  name="txtRfGsm_<? echo $i; ?>" id="txtRfGsm_<? echo $i; ?>" placeholder="" value="<? echo $row[csf("gsm")]; ?>"/>
					</td>
					<input type="hidden" id="updateidRequiredDtl_<? echo $i; ?>" name="updateidRequiredDtl_<? echo $i; ?>"  class="text_boxes" style="width:20px" value="<? echo $row[csf("id")]; ?>"  />
					<input type="hidden" id="txtDeltedIdRf" name="txtDeltedIdRf"  class="text_boxes" style="width:20px" value="" />

					<td align="center" id="rf_dia_1">
						<input style="width:40px;" type="text" class="text_boxes"  name="txtRfDia_<? echo $i; ?>" id="txtRfDia_<? echo $i; ?>"   value="<? echo $row[csf("dia")]; ?>" />
					</td>

					<td align="center" id="rf_color_1">
						<input style="width:60px;" type="text" class="text_boxes"  name="txtRfColor_<? echo $i; ?>" id="txtRfColor_<? echo $i; ?>" placeholder="browse" onDblClick="openmypage_rf_color('requires/sample_requisition_controller.php?action=color_popup_rf','Color Search','<? echo $i;?>');"

						readonly=""  value="<?
						$a=$row[csf("color_data")];
						$colors="";
						$c=explode("-----",$a);
						foreach($c as $v)
						{
							$cc=explode("_",$v);
							if($colors=="")
							{
								$colors.=$cc[1];
							}
							else
							{
								$colors.='***'.$cc[1];
							}
						}
						echo $colors;

						?>"/>
					</td>
					<input type="hidden" name="txtRfColorAllData_<? echo $i; ?>" id="txtRfColorAllData_<? echo $i; ?>" value="<? echo $row[csf("color_data")]; ?>"  class="text_boxes">

					<td align="center" id="rf_color_type_1">
						<?
						echo create_drop_down( "cboRfColorType_$i", 95, $color_type,"", 1, "Select Color Type", $row[csf("color_type_id")], "");

						?>
					</td>
					<td align="center" id="rf_width_dia_1">
						<?
						echo create_drop_down( "cboRfWidthDia_$i", 80, $fabric_typee,"", 1, "Select Width/Dia", $row[csf("width_dia_id")], "");

						?>
					</td>

					<td align="center" id="rf_uom_1">
						<?
						echo create_drop_down( "cboRfUom_$i", 56, $unit_of_measurement,'', '',"",$row[csf("uom_id")],"","","12,27,1,23" );
						?>
					</td>

					<td align="center" id="rf_req_dzn_1">
						<input style="width:50px;" type="text" class="text_boxes_numeric"  name="txtRfReqDzn_<? echo $i; ?>" id="txtRfReqDzn_<? echo $i; ?>" placeholder="write" value="<? echo $row[csf("required_dzn")]; ?>" onBlur="calculate_required_qty2('1','<? echo $i ;?>');"  />
                         <input type="hidden" class="text_boxes"  name="txtMemoryDataRf_<? echo $i;?>" id="txtMemoryDataRf_<? echo $i;?>" />

					</td>
					
                    <td align="center" id="rf_req_qty_1">
						<input style="width:50px;" type="text" class="text_boxes_numeric"  name="txtRfReqQty_<? echo $i; ?>" id="txtRfReqQty_<? echo $i; ?>" placeholder="" value="<? echo $row[csf("required_qty")]; ?>" readonly/>
					</td>
					

					<td align="center" id="rf_reqs_qty_1" style="display: none;">
						<input style="width:50px;" type="text" class="text_boxes_numeric"  name="txtProcessLoss_<? echo $i; ?>" id="txtProcessLoss_<? echo $i; ?>" placeholder=""  onChange="calculate_requirement('<? echo $i; ?>');" value="<? echo $row[csf("process_loss_percent")]; ?>" />
					</td>

					<td align="center" id="rf_grey_qnty_1" style="display: none;">
						<input style="width:50px;" type="text" class="text_boxes_numeric"  name="txtGrayFabric_<? echo $i; ?>" id="txtGrayFabric_<? echo $i; ?>" value="<? echo $row[csf("grey_fab_qnty")]; ?>" placeholder="" readonly />
					</td>
					


					<td id="rf_image_1"><input type="button" class="image_uploader" name="txtRfFile_<? echo $i; ?>" id="txtRfFile_<? echo $i; ?>" onClick="file_uploader ( '../../', document.getElementById('updateidRequiredDtl_<? echo $i;?>').value,'', 'required_fabric_1', 0 ,1)" value="ADD IMAGE"></td>
					<td width="70">
						<input type="button" id="increaserf_<? echo $i; ?>" name="increaserf_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_rf_tr(<? echo $i; ?>)" />
						<input type="button" id="decreaserf_<? echo $i; ?>" name="decreaserf_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_rf_deleteRow(<? echo $i; ?>);" />
					</td>
				</tr>

				<?
				$i++;
			}
		}

	}


	else if($type==3)
	{
		$sql_sam="SELECT id,sample_mst_id,sample_name_ra,gmts_item_id_ra,trims_group_ra,description_ra,brand_ref_ra,uom_id_ra,req_dzn_ra,req_qty_ra,remarks_ra from sample_development_fabric_acc where sample_mst_id='$up_id' and form_type=2 and  is_deleted=0  and status_active=1 order by id ASC";
		$sql_result =sql_select($sql_sam);  $i=1;
		if(count($sql_result)>0)
		{
			foreach($sql_result as $row)
			{
				?>
				<tr  id="tr_<? echo $i;?>"  class="general">
					<td align="center" id="raSampleId_1" width="100">
						<?
						$sql="select a.id,a.sample_name,b.id as dtls_id from  lib_sample a ,sample_development_dtls b where  a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.id=b.sample_name and b.entry_form_id=117 and b.sample_mst_id='$up_id' group by a.id,a.sample_name,b.id order by b.id";
						echo create_drop_down( "cboRaSampleName_$i", 100, $sql,"id,sample_name", '', "",$row[csf("sample_name_ra")], "","");

                        		 //echo create_drop_down( "cboRfUom_$i", 56, $unit_of_measurement,'', '1',"s",$row[csf("uom_id")],"","","12,27,1,23" );
						?>

					</td>

					<td align="center" id="raItemId_1" width="100">
						<?
						$sql_gmts=sql_select("select id,gmts_item_id from sample_development_dtls where is_deleted=0  and status_active=1 and entry_form_id=117 and sample_mst_id='$up_id'");
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
						<input style="width:130px;" type="text" class="text_boxes"  name="txtRaDescription_<? echo $i;?>" id="txtRaDescription_<? echo $i;?>" placeholder="write" value="<? echo $row[csf("description_ra")]; ?>"/>

						<input type="hidden" id="updateidAccessoriesDtl_<? echo $i;?>" name="updateidAccessoriesDtl_<? echo $i;?>"  class="text_boxes" style="width:20px" value="<? echo $row[csf("id")]; ?>" />
					</td>
					<input type="hidden" id="txtDeltedIdRa" name="txtDeltedIdRa"  class="text_boxes" style="width:20px" value="" />
					<td align="center" id="ra_brand_supp_1" width="130">
						<input style="width:130px;" type="text" class="text_boxes"  name="txtRaBrandSupp_<? echo $i;?>" id="txtRaBrandSupp_<? echo $i;?>" placeholder="write" value="<? echo $row[csf("brand_ref_ra")]; ?>"/>
					</td>

					<td align="center" id="ra_uom_1" width="100">
						<?
						echo create_drop_down( "cboRaUom_$i", 100, $unit_of_measurement,'', '', "",$row[csf("uom_id_ra")],"","","" );
						?>
					</td>

					<td align="center" id="ra_req_dzn_1" width="100">
						<input style="width:100px;" type="text" class="text_boxes_numeric"  name="txtRaReqDzn_<? echo $i;?>" id="txtRaReqDzn_<? echo $i;?>" placeholder="write" value="<? echo $row[csf("req_dzn_ra")]; ?>" onBlur="calculate_required_qty('2','<? echo $i ;?>');" />
					</td>

					<td align="center" id="ra_req_qty_1" width="100">
						<input style="width:100px;" type="text" class="text_boxes_numeric"  name="txtRaReqQty_<? echo $i;?>" id="txtRaReqQty_<? echo $i;?>" placeholder="write" value="<? echo $row[csf("req_qty_ra")]; ?>" readonly/>
					</td>
					<input type="hidden" class="text_boxes"  name="txtMemoryDataRa_<? echo $i;?>" id="txtMemoryDataRa_<? echo $i;?>" />

					<td align="center" id="ra_remarks_1" width="70">
						<input style="width:70px;" type="text" class="text_boxes"  name="txtRaRemarks_<? echo $i;?>" id="txtRaRemarks_<? echo $i;?>" placeholder="write" value="<? echo $row[csf("remarks_ra")]; ?>" />
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


	else if($type==4)
	{
		$sql_sam="SELECT id,sample_mst_id,sample_name_re,gmts_item_id_re,name_re,type_re,remarks_re from sample_development_fabric_acc where sample_mst_id='$up_id' and form_type=3 and  is_deleted=0  and status_active=1  order by id ASC";
		$sql_result =sql_select($sql_sam);  $i=1;
		if(count($sql_result)>0)
		{

			foreach($sql_result as $row)
			{
				?>
				<tr id="tr_<? echo $i;?>" style="height:10px;" class="general">
					<td align="center" id="reSampleId_1">
						<?
						$sql="select a.id,a.sample_name,b.id as dtls_id from  lib_sample a ,sample_development_dtls b where  a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.id=b.sample_name and b.entry_form_id=117 and b.sample_mst_id='$up_id' group by a.id,a.sample_name,b.id order by b.id";
						echo create_drop_down( "cboReSampleName_$i", 140, $sql,"id,sample_name", '', "",$row[csf("sample_name_re")],"","");
						?>

					</td>

					<td align="center" id="reItemIid_1">
						<?
						$sql_gmts_re=sql_select("select id,gmts_item_id from sample_development_dtls where is_deleted=0  and status_active=1 and entry_form_id=117 and sample_mst_id='$up_id'");
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
					<td align="center" id="re_remarks_1">
						<input style="width:90px;" type="text" class="text_boxes"  name="txtReRemarks_<? echo $i;?>" id="txtReRemarks_<? echo $i;?>" placeholder="write" value="<? echo $row[csf("remarks_re")]; ?>"/>
					</td>



					<td id="re_image_1"><input type="button" class="image_uploader" name="reTxtFile_<? echo $i;?>" id="reTxtFile_<? echo $i;?>" size="20" style="width:170px;" value="CLICK TO ADD/VIEW IMAGE" onClick="file_uploader ( '../../', document.getElementById('updateidRequiredEmbellishdtl_<? echo $i;?>').value,'', 'required_embellishment_1', 0 ,1);"></td>
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
		//$new_system_id=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), 'SR', date("Y",time()), 5, "select requisition_number_prefix, requisition_number_prefix_num from sample_development_mst where entry_form_id=117 and company_id=$cbo_company_name  and $year_cond=".date('Y',time())." order by requisition_number_prefix_num desc ", "requisition_number_prefix", "requisition_number_prefix_num" ));
  		if($db_type==0)
  		{
  			$new_system_id=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', '', date("Y",time()), 5, "select requisition_number_prefix,requisition_number_prefix_num from sample_development_mst where  entry_form_id=117 and company_id=$cbo_company_name and YEAR(insert_date)=".date('Y',time())." order by id desc ", "requisition_number_prefix", "requisition_number_prefix_num" ));
  		}
  		if($db_type==2)
  		{
  			$new_system_id=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', '', date("Y",time()), 5, "select requisition_number_prefix,requisition_number_prefix_num from sample_development_mst where entry_form_id=117 and company_id=$cbo_company_name and to_char(insert_date,'YYYY')=".date('Y',time())." order by id desc ", "requisition_number_prefix", "requisition_number_prefix_num" ));
  		}


  		$field_array="id,requisition_number_prefix,requisition_number_prefix_num,requisition_number,sample_stage_id,requisition_date,quotation_id,style_ref_no,company_id,location_id,buyer_name,season,product_dept,dealing_marchant,agent_name,buyer_ref,bh_merchant,estimated_shipdate,remarks,inserted_by,insert_date,status_active,is_deleted,entry_form_id,req_ready_to_approved";
  		$data_array="(".$id_mst.",'".$new_system_id[1]."',".$new_system_id[2].",'".$new_system_id[0]."',".$cbo_sample_stage.",".$txt_requisition_date.",".$txt_quotation_id.",".$txt_style_name.",".$cbo_company_name.",".$cbo_location_name.",".$cbo_buyer_name.",".$cbo_season_name.",".$cbo_product_department.",".$cbo_dealing_merchant.",".$cbo_agent.",".$txt_buyer_ref.",".$txt_bhmerchant.",".$txt_est_ship_date.",".$txt_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0,117,'2')";
  		$rID=sql_insert("sample_development_mst",$field_array,$data_array,1);
  		$mst_id=return_field_value("max(id)","sample_development_mst","status_active=1 and is_deleted=0");


	    // sample details entry
  		$id_dtls=return_next_id( "id", "sample_development_dtls", 1 ) ;
  		$field_array_dtls= "id,sample_mst_id,sample_name,gmts_item_id,smv,article_no,sample_color,sample_prod_qty,submission_qty,delv_start_date,delv_end_date,sample_charge,sample_curency,inserted_by,insert_date,status_active,is_deleted,entry_form_id,size_data,fabric_status,acc_status,embellishment_status,fab_status_id,acc_status_id,embellishment_status_id";
  		$query_dtls=sql_select("select id,sample_mst_id,sample_name,gmts_item_id,smv,article_no,sample_color,sample_prod_qty,submission_qty,delv_start_date,delv_end_date,sample_charge,sample_curency,status_active,is_deleted,entry_form_id,size_data,fabric_status,acc_status,embellishment_status,fab_status_id,acc_status_id,embellishment_status_id from sample_development_dtls where entry_form_id=117 and status_active=1 and is_deleted=0 and sample_mst_id=$update_id");

  		$id_size=return_next_id( "id","sample_development_size", 1 ) ;
  		$field_array_size="id, mst_id, dtls_id,size_id,bh_qty,plan_qty,dyeing_qty,test_qty,self_qty,total_qty,inserted_by, insert_date, status_active, is_deleted";

  		for ($i=0;$i<count($query_dtls);$i++)
  		{
  			if ($i!=0) $data_array_dtls .=",";
  			$data_array_dtls .="(".$id_dtls.",".$mst_id.",".$query_dtls[$i][csf("sample_name")].",".$query_dtls[$i][csf("gmts_item_id")].",'".$query_dtls[$i][csf("smv")]."','".$query_dtls[$i][csf("article_no")]."','".$query_dtls[$i][csf("sample_color")]."','".$query_dtls[$i][csf("sample_prod_qty")]."','".$query_dtls[$i][csf("submission_qty")]."','".$query_dtls[$i][csf("delv_start_date")]."','".$query_dtls[$i][csf("delv_end_date")]."','".$query_dtls[$i][csf("sample_charge")]."','".$query_dtls[$i][csf("sample_curency")]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0,117,'".$query_dtls[$i][csf("size_data")]."',0,0,0,0,0,0)";




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
  						$size_id = return_id( $size_name, $size_arr, "lib_size", "id,size_name");
  						$new_array_size[$size_id]=str_replace("'","",$size_name);

  					}
  					else $size_id =  array_search($size_name, $new_array_size);
  				}
  				else
  				{
  					$size_id=0;
  				}

  				if($data_array_size !="") $data_array_size .=',';
  				$data_array_size.="(".$id_size.",".$mst_id.",".$id_dtls.",'".$size_id."','".$bhqty."','".$plqty."','".$dyqty."','".$testqty."','".$selfqty."','".$totalqty."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
  				$id_size=$id_size+1;
  			}
  			$id_dtls=$id_dtls+1;
  		}
 			//echo "555**"."INSERT INTO sample_development_size(".$field_array_size.")VALUES ".$data_array_size;
  		$rid_dtls=sql_insert("sample_development_dtls",$field_array_dtls,$data_array_dtls,1);
  		$rid_size=sql_insert("sample_development_size",$field_array_size,$data_array_size,1);


	    // fabric details entry
  		$id_fabric=return_next_id( "id", "sample_development_fabric_acc", 1 ) ;
  		$field_array_fabric= "id,sample_mst_id,sample_name,gmts_item_id,body_part_id,fabric_nature_id,fabric_description,gsm,dia,color_data,color_type_id,width_dia_id,uom_id,required_dzn,required_qty,inserted_by,insert_date,status_active,is_deleted,form_type";
  		$query_fabric=sql_select("select id,sample_mst_id,sample_name,gmts_item_id,body_part_id,fabric_nature_id,fabric_description,gsm,dia,color_data,color_type_id,width_dia_id,uom_id,required_dzn,required_qty,form_type from sample_development_fabric_acc where form_type=1 and status_active=1 and is_deleted=0 and sample_mst_id=$update_id");
  		$field_array_col="id, mst_id, dtls_id,color_id,contrast,inserted_by, insert_date, status_active, is_deleted";
  		$idColorTbl=return_next_id( "id","sample_development_rf_color", 1 ) ;
  		for($i=0;$i<count($query_fabric);$i++)
  		{

  			if ($i!=0) $data_array_fabric .=",";
				//
  			$data_array_fabric .="(".$id_fabric.",".$mst_id.",".$query_fabric[$i][csf("sample_name")].",".$query_fabric[$i][csf("gmts_item_id")].",".$query_fabric[$i][csf("body_part_id")].",".$query_fabric[$i][csf("fabric_nature_id")].",'".$query_fabric[$i][csf("fabric_description")]."','".$query_fabric[$i][csf("gsm")]."','".$query_fabric[$i][csf("dia")]."','".$query_fabric[$i][csf("color_data")]."',".$query_fabric[$i][csf("color_type_id")].",".$query_fabric[$i][csf("width_dia_id")].",".$query_fabric[$i][csf("uom_id")].",'".$query_fabric[$i][csf("required_dzn")]."','".$query_fabric[$i][csf("required_qty")]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0,1)";
  			$ex_data=explode("-----",$query_fabric[$i][csf("color_data")]);
  			foreach($ex_data as $color_data)
  			{
  				$ex_size_data=explode("_",$color_data);
  				$colorName=$ex_size_data[1];
  				$colorId=$ex_size_data[2];
  				$contrast=$ex_size_data[3];
  				if($data_array_col !="")  $data_array_col.=",";
 					//if ($i!=1) $add_comma .=",";
  				$data_array_col.="(".$idColorTbl.",".$mst_id.",".$id_fabric.",".$colorId.",'".$contrast."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
  				$idColorTbl = $idColorTbl + 1;
  			}
  			$id_fabric=$id_fabric+1;

  		}
  		$rid_fabric=sql_insert("sample_development_fabric_acc",$field_array_fabric,$data_array_fabric,1);
  		$rid_color_rf=sql_insert("sample_development_rf_color",$field_array_col,$data_array_col,1);

		//accessories entry
  		$id_acc=return_next_id( "id", "sample_development_fabric_acc", 1 ) ;
  		$field_array_acc= "id,sample_mst_id,sample_name_ra,gmts_item_id_ra,trims_group_ra,description_ra,brand_ref_ra,uom_id_ra,req_dzn_ra,req_qty_ra,remarks_ra,inserted_by,insert_date,status_active,is_deleted,form_type";
  		$query_acc=sql_select("select id,sample_mst_id,sample_name_ra,gmts_item_id_ra,trims_group_ra,description_ra,brand_ref_ra,uom_id_ra,req_dzn_ra,req_qty_ra,remarks_ra,form_type  from sample_development_fabric_acc where form_type=2 and status_active=1 and is_deleted=0 and sample_mst_id=$update_id");
  		for($i=0;$i<count($query_acc);$i++)
  		{
  			if ($i!=0) $data_array_acc .=",";
  			$data_array_acc .="(".$id_acc.",".$mst_id.",".$query_acc[$i][csf("sample_name_ra")].",".$query_acc[$i][csf("gmts_item_id_ra")].",'".$query_acc[$i][csf("trims_group_ra")]."','".$query_acc[$i][csf("description_ra")]."','".$query_acc[$i][csf("brand_ref_ra")]."',".$query_acc[$i][csf("uom_id_ra")].",'".$query_acc[$i][csf("req_dzn_ra")]."','".$query_acc[$i][csf("req_qty_ra")]."','".$query_acc[$i][csf("remarks_ra")]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0,2)";

  			$id_acc=$id_acc+1;

  		}
  		$acc_id=sql_insert("sample_development_fabric_acc",$field_array_acc,$data_array_acc,1);


	  //print_r($query_emb);
  		$a=count($query_emb);

		// embellishment entry
  		$id_emb=return_next_id( "id", "sample_development_fabric_acc", 1 ) ;
  		$query_emb=sql_select("select id,sample_mst_id,sample_name_re,gmts_item_id_re,name_re,type_re,remarks_re,inserted_by,insert_date,status_active,is_deleted from sample_development_fabric_acc where form_type=3 and status_active=1 and is_deleted=0 and sample_mst_id=$update_id");
	  //print_r($query_emb);
  		$a=count($query_emb);
  		$field_array_emb= "id,sample_mst_id,sample_name_re,gmts_item_id_re,name_re,type_re,remarks_re,inserted_by,insert_date,status_active,is_deleted,form_type";
  		for ($i=0;$i<$a;$i++)
  		{

  			if ($i!=0) $data_array_emb .=",";
  			$data_array_emb .="(".$id_emb.",".$mst_id.",'".$query_emb[$i][csf("sample_name_re")]."','".$query_emb[$i][csf("gmts_item_id_re")]."','".$query_emb[$i][csf("name_re")]."','".$query_emb[$i][csf("type_re")]."','".$query_emb[$i][csf("remarks_re")]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0,3)";

  			$id_emb=$id_emb+1;

  		}

  		$emb_id=sql_insert("sample_development_fabric_acc",$field_array_emb,$data_array_emb,1);
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
  		if($db_type==2 || $db_type==1 )
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



?>
