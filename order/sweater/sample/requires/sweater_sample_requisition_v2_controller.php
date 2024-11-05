<?
header('Content-type:text/html; charset=utf-8');
session_start();
if($_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
extract($_REQUEST);
include('../../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];

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
				$col_sql="select id,color_name from lib_color where status_active=1 and is_deleted=0 and  color_name='$vals' and id in(select sample_color from sample_development_dtls where status_active=1 and is_deleted=0 and entry_form_id=459 and sample_mst_id='$data[2]')";
				$col_arr=sql_select($col_sql);
				$col_id=$col_arr[0][csf("id")];
			}
			else
			{
				$col_arr=sql_select("select id,color_name from lib_color where status_active=1 and is_deleted=0 and  color_name='$vals' and id in(select sample_color from sample_development_dtls where status_active=1 and is_deleted=0 and entry_form_id=459 and sample_mst_id='$data[2]')");
				$col_id.=','.$col_arr[0][csf("id")];
			}
		}
		$value=return_field_value("sum(sample_prod_qty)","sample_development_dtls","entry_form_id=459 and sample_name=$data[0] and gmts_item_id=$data[1] and sample_mst_id=$data[2] and status_active=1 and is_deleted=0 and sample_color in($col_id) ");
	}
	else
	{
		$value=return_field_value("sum(sample_prod_qty)","sample_development_dtls","entry_form_id=459 and sample_name=$data[0] and gmts_item_id=$data[1] and sample_mst_id=$data[2] and status_active=1 and is_deleted=0 ");
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

    $sql=sql_select("select distinct(sample_color) from sample_development_dtls where entry_form_id=459 and sample_mst_id=$data[2] and sample_name=$data[0] and gmts_item_id=$data[1] and is_deleted=0  and status_active=1");
	if(count($sql)==1)
	{
		echo "1__".$color_library[$sql[0][csf("sample_color")]]."__".$sql[0][csf("sample_color")]."__";
	}
	exit();
}

if($action=="auto_sd_color_generation")
{
	$data=explode("***",$data);
	$sql=sql_select("select sample_color from sample_development_dtls where entry_form_id=459 and sample_name=$data[0] and gmts_item_id=$data[1] and sample_mst_id=$data[2] and status_active=1 and is_deleted=0 ");
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
	}
	else if($data[0]==2)
	{
		echo create_drop_down( "cboReType_".$data[1], 140,$emblishment_embroy_type,"", 1, "-- Select --", "", "","","" );
	}
	else if($data[0]==3)
	{
		echo create_drop_down( "cboReType_".$data[1], 140,$emblishment_wash_type,"", 1, "-- Select --", "", "","","" );
	}
	else if($data[0]==4)
	{
		echo create_drop_down( "cboReType_".$data[1], 140,$emblishment_spwork_type,"", 1, "-- Select --", "", "","","" );
	}
	else if($data[0]==5)
	{
		echo create_drop_down( "cboReType_".$data[1], 140,$emblishment_gmts_type,"", 1, "-- Select --", "", "","","" );
	}
	else
	{
		echo create_drop_down( "cboReType_".$data[1], 140,$emblishment_gmts_type,"", 1, "-- Select --", "", "","","" );
	}
	exit();
}

if ($action=="load_drop_down_required_fabric_gmts_item")
{
	$data=explode("_", trim($data));
 	$sql=sql_select("select id,gmts_item_id from sample_development_dtls where is_deleted=0  and status_active=1 and entry_form_id=459 and sample_mst_id='$data[0]'");

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
	$sql="select a.id,a.sample_name,b.id as dtls_id from  lib_sample a ,sample_development_dtls b where  a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.id=b.sample_name and b.entry_form_id=459 and b.sample_mst_id='$data[0]' group by a.id,a.sample_name,b.id order by b.id";
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
			echo create_drop_down( "cboRfSampleName_1", 95, $samp_array,"", 0, "select Sample", $selected,"");
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
			echo create_drop_down( "cboRaSampleName_1", 100, $samp_array,"", 0, "select Sample", $selected,"");
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
			echo create_drop_down( "cboReSampleName_1", 140, $samp_array,"", 0, "select Sample", $selected,"");
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
	echo load_html_head_contents("Fabric Description Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(data)
		{
			var data=data.split('_');
			var fabric_yarn_description=return_global_ajax_value(data[0], 'fabric_yarn_description', '', 'sweater_sample_requisition_v2_controller');
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
            	<tr>
					<th colspan="4" align="center"><? echo create_drop_down( "cbo_string_search_type", 100, $string_search_type,'', 1, "-- Searching Type --",4 ); ?></th>
                </tr>
            	<tr>
            		<th>RD No</th>
                    <th>Construction</th>
                    <th>GSM/Weight</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th>
            	</tr>            		
                </thead>
                <tbody>
                	<tr>
                		<td><input type="text" style="width:80px" class="text_boxes" name="txt_rdno" id="txt_rdno" /></td>
                        <td align="center">
                            <input type="text" style="width:130px" class="text_boxes" name="txt_construction" id="txt_construction" />
                        </td>
                        <td align="center">
                            <input type="text" style="width:130px" class="text_boxes" name="txt_gsm_weight" id="txt_gsm_weight" />
                        </td>
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $fabric_nature; ?>'+'**'+'<? echo $libyarncountdeterminationid; ?>'+'**'+document.getElementById('txt_construction').value+'**'+document.getElementById('txt_gsm_weight').value+'**'+document.getElementById('txt_rdno').value+'**'+document.getElementById('cbo_string_search_type').value, 'fabric_description_popup_search_list_view', 'search_div', 'sweater_sample_requisition_v2_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:100px;" />
                    </td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:10px" id="search_div"></div>
		</fieldset>
	</form>
    </div>
    </body>
    <script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if($action=="fabric_remarks_popup")
{
	echo load_html_head_contents("Fabric Remarks Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>


    </head>
    <body onLoad="close();">
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset>
            <table width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                     <tr>
                     	<th  colspan="3">Remarks</th>
                     </tr>
                </thead>
                <tbody align="center">
                	<tr>
                		<td colspan="3">
                			<input type="hidden" id="remarks_hidden" name="">
                			<textarea  id="remarks" style="height: 130px;width: 335px;"><? echo $existing_rem;?></textarea>
                		</td>

                	</tr>
                	<tr>
                		<td colspan="3" align="center" ><input class="button" style="border:1px solid black;border-radius: 2px;height: 20px;width: 55px;font-weight: bold;color:crimson;" type="submit" id="close" value="Close" name=""></td>
                	</tr>

                </tbody>

           	</table>

		</fieldset>

	</form>
    </div>
    </body>
    <script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
    <script>
		 $("#close").click(function(){
   				document.getElementById('remarks_hidden').value=$("#remarks").val();
				parent.emailwindow.hide();
		});
	</script>
    </html>
    <?
	exit();
}


if($action=="fabric_description_popup_search_list_view")
{
	extract($_REQUEST);
	list($fabric_nature,$libyarncountdeterminationid,$construction,$gsm_weight,$rdno)=explode('**',$data);
	//$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count"  );
	$search_con ="";
	$search_con='';
	if($string_search_type==1)
	{
		if($construction!='') {$search_con .= " and a.construction='".trim($construction)."'";}
		if($gsm_weight!='') {$search_con .= " and a.gsm_weight='".trim($gsm_weight)."'";}
		if($rdno!='') {$search_con .= " and a.rd_no ='".trim($rdno)."'";}
	}
	else if($string_search_type==2)
	{
		if($construction!='') {$search_con .= " and a.construction like ('".trim($construction)."%')";}
		if($gsm_weight!='') {$search_con .= " and a.gsm_weight like ('".trim($gsm_weight)."%')";}
		if($rdno!='') {$search_con .= " and a.rd_no like ('".trim($rdno)."%')";}
	}
	else if($string_search_type==3)
	{
		if($construction!='') {$search_con .= " and a.construction like ('%".trim($construction)."')";}
		if($gsm_weight!='') {$search_con .= " and a.gsm_weight like ('%".trim($gsm_weight)."')";}
		if($rdno!='') {$search_con .= " and a.rd_no like ('%".trim($rdno)."')";}
	}
	else if($string_search_type==4 || $string_search_type==0)
	{
		if($construction!='') {$search_con .= " and a.construction like ('%".trim($construction)."%')";}
		if($gsm_weight!='') {$search_con .= " and a.gsm_weight like ('%".trim($gsm_weight)."%')";}
		if($rdno!='') {$search_con .= " and a.rd_no like ('%".trim($rdno)."%')";}
	}
	//if($construction!=''){$search_con = " and a.construction like('%".trim($construction)."%')";}
	//if($gsm_weight!=''){$search_con  .= " and a.gsm_weight like('%".trim($gsm_weight)."%')";}
	//if($rdno!='') {$search_con .= " and a.rd_no like ('%".trim($rdno)."%')";}
	?>


		<div align="center">
			<form action="#">
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
			$composition_arr=array(); $other_data_arr=array();
			$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
			//$arr=array (0=>$item_category, 3=>$color_range,6=>$composition,8=>$lib_yarn_count,9=>$yarn_type);
			//$sql="select  a.id, a.fab_nature_id, a.construction, a.gsm_weight, a.color_range_id, a.stich_length, a.process_loss, b.copmposition_id, b.percent, b.count_id, b.type_id, a.rd_no , a.mill_ref , a.gauge,a.count from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and  a.is_deleted=0 and a.fab_nature_id=$fabric_nature $search_con order by b.id";
			$sql="select id, fab_nature_id, rd_no, mill_ref, construction, color_range_id, gauge, process_loss, sequence_no, fab_composition, fabric_composition_id, count, yarn_type, inserted_by, status_active from lib_yarn_count_determina_mst where is_deleted=0 and entry_form=461 order by id DESC";
			//echo $sql; die;
			$data_array=sql_select($sql);

			$fab_description=""; $yarn_description="";

			?>
			<table class="rpt_table" width="950" cellspacing="0" cellpadding="0" border="0" rules="all">
				<thead>
					<tr>
						<th width="50">SL No</th>
						<th width="100">Fab Nature</th>
						<th width="60">RD No</th>
						<th width="60">Mill Ref</th>
						<th width="60">Count</th>
						<th width="250">Composition</th>
						<th width="60">Type</th>
						<th width="60">Gauge</th>
					</tr>
				</thead>
			</table>
			<div id="" style="max-height:350px; width:948px; overflow-y:scroll">
				<table id="list_view" class="rpt_table" width="930" height="" cellspacing="0" cellpadding="0" border="1" rules="all">
					<tbody>
						<?
						foreach($data_array as $row)
						{
							$fab_nature_id=$composition=$gsm_weight=$process_loss=$color_range_id=$stich_length='';
							$fab_nature_id=$row[csf('fab_nature_id')];
							$rd_no=$row[csf('rd_no')];
							$composition=$row[csf('fab_composition')];
							$gsm_weight=$row[csf('gsm_weight')];
							$process_loss=$row[csf('process_loss')];
							$id = $row[csf('id')];
							
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							?>
							<tr id="tr_<? echo $id; ?>" bgcolor="<? echo $bgcolor; ?>" height="20" style="cursor:pointer; word-break:break-all;" onClick="js_set_value('<? echo $id."_".$fab_nature_id."_".$composition."_".$gsm_weight."_".$process_loss; ?>')">
								<td width="50"><? echo $i; ?></td>
								<td width="100" align="left"><? echo $item_category[$fab_nature_id]; ?></td>
								<td width="60" align="left"><? echo $rd_no; ?></td>
								<td width="60" align="left"><? echo $row[csf('mill_ref')]; ?></td>
								<td width="60" align="left"><? echo $lib_yarn_count[$row[csf('count')]]; ?></td>
								<td width="250" align="left"><? echo $row[csf('fab_composition')]; ?></td>
								<td width="60" align="left"><? echo $yarn_type[$row[csf('yarn_type')]]; ?></td>
								<td width="60" align="left"><? echo $gauge_arr[$row[csf('gauge')]]; ?></td>
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
	<?
	exit();
}

if($action =="fabric_yarn_description")
{
	$fab_description=""; $yarn_description="";
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
	exit();
}

if($action=="sweater_sample_requisition_print")
{
    extract($_REQUEST);
	list($company_name,$update_id)=explode('*',$data);
	$company_library=return_library_array( "select id, company_name from lib_company where id=$company_name", "id", "company_name"  );
	
	$company_img=sql_select("select image_location  from common_photo_library  where master_tble_id='$company_name' and form_name='company_details' and is_deleted=0 and file_type=1");
	
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='sample_details_1' and file_type=1",'master_tble_id','image_location');
	
	$emb_imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='required_embellishment_1' and file_type=1",'master_tble_id','image_location');
	
	
	
	
	$size_arr=return_library_array( "select id,size_name from lib_size", "id", "size_name" );
	$season_arr=return_library_array( "select id,season_name from lib_buyer_season  where status_active =1 and is_deleted=0 ", "id", "season_name"  );
	$dealing_merchant_library=return_library_array( "select id,team_member_name from lib_mkt_team_member_info", "id", "team_member_name"  );
	$sample_library=return_library_array( "select id, sample_name from lib_sample", "id", "sample_name");
	$color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$trims_group_lib=return_library_array( "select id, item_name from lib_item_group", "id", "item_name"  );
	$brandArr=return_library_array( "select id, brand_name from lib_buyer_brand", "id", "brand_name");
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$bom_arr=return_library_array( "select id, bom_no from wo_quotation_inquery", "id", "bom_no");
	
	$store_name_lib=return_library_array( "select id, store_name from lib_store_location", "id", "store_name"  );
	
	$team_name_lib=return_library_array( "select id, team_name from lib_sample_production_team where  product_category=6 and status_active=1 and is_deleted=0", "id", "team_name"  );
	
 ?>


<div id="mstDiv">

    <table cellspacing="0" cellpadding="5" border="0">
     <tr>
     	<td rowspan="2" colspan="3">
        	<img width="150" height="80" src="../../../<? echo $company_img[0][csf('image_location')]; ?>">
        </td>
     	<td align="center" colspan="6" style="font-size: 24px;"><strong><? echo $company_library[$company_name]; ?></strong></td>
     </tr>
     <tr>
        <td colspan="9" align="center">
            <?
                $val=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$company_name");
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
                  $sql="SELECT id, requisition_number, requisition_number_prefix_num, style_ref_no, buyer_name, season, product_dept, dealing_marchant, agent_name, buyer_ref, estimated_shipdate, season_buyer_wise, remarks, quotation_id, sample_stage_id, requisition_date, team_leader, season_year, brand_id from sample_development_mst where id=$update_id and entry_form_id=459 and  is_deleted=0  and status_active=1";
                  $dataArray=sql_select($sql);
                  
				$sample_name=return_field_value("sample_name","sample_development_dtls","sample_mst_id=$update_id"); 
				  
            ?>
        </td>
        </tr>
        <tr>
            <td colspan="9" align="center"><strong>Sample Requisition</strong></td>
				<?
                    $is_app=return_field_value("is_approved","sample_development_mst","entry_form_id=459 and id=$update_id and status_active=1 and is_deleted=0");
                    if($is_app==3){$is_app=1;}
                    $appDate=explode(" ", $appDate);
                    if($is_app==1)
                    {
                        echo "<span style='color:red;border:2px solid black;'>
						- Approved By ".$user_arr[$appBy]."
                        , Approved Date: ". change_date_format($appDate[0],'yyyy-mm-dd')." </span>";
                    }
                 ?>
        	</td>
        </tr>
        <tr>
            <td><strong>Requisition No</strong></td><td>:</td>
            <td><? echo $dataArray[0][csf("requisition_number")]; ?></td> 
            <td><strong>Req. Date</strong></td><td>:</td>
            <td><? echo change_date_format($dataArray[0][csf("requisition_date")],"dd-mm-yyyy"); ?></td>
            <td><strong>Buyer Name</strong></td><td>:</td>
            <td><? echo $buyer_library[$dataArray[0][csf('buyer_name')]];?></td>
            
        </tr>
        <tr>
        	<td><strong>Master/Style Ref</strong></td><td>:</td>
            <td><? echo $dataArray[0][csf('style_ref_no')];?></td>
            <td><strong>Season</strong></td><td>:</td>
            <td><? echo $season_arr[$dataArray[0][csf('season')]];?></td>
            <td><strong>Saeason Year</strong></td><td>:</td>
            <td><?=$dataArray[0][csf('season_year')]; ?></td>
       	</tr>
       	 <tr>
         	<td><strong>Dealing Merchant</strong></td><td>:</td>
            <td><? echo $dealing_merchant_library[$dataArray[0][csf('dealing_marchant')]];?></td>
            <td><strong>Product Dept</strong></td><td>:</td>
            <td><? echo $product_dept[$dataArray[0][csf('product_dept')]];?></td>
            <td><strong>Sample Name</strong></td><td>:</td>
            <td><? echo $sample_library[$sample_name];?></td>
		 </tr>
		 <tr>
         	<td><strong>Buyer Ref</strong></td><td>:</td>
            <td><? echo $dataArray[0][csf('buyer_ref')];?></td>
            <td><strong>Est.Ship Date</strong></td><td>:</td>
            <td><? echo change_date_format($dataArray[0][csf('estimated_shipdate')]);?></td>
            <td><strong>Brand</strong></td><td>:</td>
            <td><P><?=$brandArr[$dataArray[0][csf('brand_id')]]; ?></P></td>
        </tr>
        <tr>
        	<td><strong>Team Name</strong></td><td>:</td>
            <td><P><? echo $team_name_lib[$dataArray[0][csf('team_leader')]];?></P></td>
            <td><strong>BOM</strong></td><td>:</td>
            <td><P><? echo $bom_arr[$dataArray[0][csf('quotation_id')]];?></P></td>
       		 
        </tr>
        <tr>
        	<td><strong>Remarks/Desc</strong></td><td>:</td>
       		<td colspan="3" style="word-wrap: break-word;word-break: break-all;" ><? echo $dataArray[0][csf('remarks')];?></td>
        </tr>
    </table>
        
    <table cellspacing="0" border="1" class="rpt_table" rules="all" width="800">
        <thead>
            <tr><td colspan="11" align="center"><strong>Sample Details</strong></td></tr>
            <tr>
                <th width="30">SL</th>
                <th width="120">Garment Item</th>
                <th width="55">Article No</th>
                <th width="70">Gmts Color</th>
                <th width="70">Color Combo NO</th>
                <th width="60"> Size</th>
                <th width="60">Prod Qty</th>
                <th width="45">Submission Qty</th>
                <th width="70">Start Date</th>
                <th width="70">Delivery Date</th>
                <th>Images</th>
             </tr>
        </thead>
        <tbody>

            <?
		$size_select=sql_select("SELECT  BH_QTY,DTLS_ID,SIZE_ID,TOTAL_QTY  from sample_development_size where mst_id=$update_id and status_active=1 and is_deleted=0");
		foreach($size_select as $row)
		{
			$sizeDataArr[$row[DTLS_ID]][$row[SIZE_ID]]+=$row[TOTAL_QTY];
			$bhQtyArr[$row[DTLS_ID]]+=$row[BH_QTY];
		}
		  
		 $sql_qry="select id,sample_mst_id,sample_name,gmts_item_id,smv,article_no,sample_color,sample_prod_qty,submission_qty,delv_start_date,delv_end_date,sample_charge,sample_curency,color_combo_no from sample_development_dtls where status_active =1 and is_deleted=0 and entry_form_id=459 and sample_mst_id=$update_id and  status_active =1 and is_deleted=0 order by id asc";
			
		$result=sql_select($sql_qry);
		$i=1;$totalSizeQty=0;$totalSubmissionQty=0;
		foreach($result as $row)
		{
            $rowspan=count($sizeDataArr[$row[csf('id')]]);
			$totalSubmissionQty+=$bhQtyArr[$row[csf('id')]];
			?>
            <tr>
                <td rowspan="<? echo $rowspan;?>" align="center"><? echo $i;?></td>
                <td rowspan="<? echo $rowspan;?>"><? echo $garments_item[$row[csf('gmts_item_id')]];?></td>
                <td rowspan="<? echo $rowspan;?>"><? echo $row[csf('article_no')];?></td>
                <td rowspan="<? echo $rowspan;?>"><? echo $color_library[$row[csf('sample_color')]];?></td>
                <td rowspan="<? echo $rowspan;?>"><? echo $row[csf('color_combo_no')];?></td>
                <? 
				$ii=1;
				
				foreach($sizeDataArr[$row[csf('id')]] as $size_id=>$size_qty){
				   if($ii !=1){echo "<tr>";}
				   $totalSizeQty+=$size_qty;
				?>
                <td width="80" align="center"><p><? echo $size_arr[$size_id];?></p></td>
                <td align="right" width="50"><? echo $size_qty;?></td>
                <? 
					if($ii==1){?>
					<td rowspan="<? echo $rowspan;?>" align="right"><? echo $bhQtyArr[$row[csf('id')]];?></td>
					<td rowspan="<? echo $rowspan;?>" align="center"><? echo change_date_format($row[csf('delv_start_date')]);?> </td>
					<td rowspan="<? echo $rowspan;?>" align="center"><? echo change_date_format($row[csf('delv_end_date')]);?> </td>
					<td rowspan="<? echo $rowspan;?>"><img src="../../../<? echo $imge_arr[$row[csf('id')]];?>" width="120" height='60'></td>
					<?	
					}
					echo "</tr>";
				$ii++;
				}
			$i++;
           }

            ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6" align="center"><b>Total</b></td>
                <td align="right"><b><? echo number_format($totalSizeQty,2);?> </b></td>
                <td  align="right"><b><? echo number_format($totalSubmissionQty,2);?> </b></td>
                <td colspan="3"></td>
            </tr>
        </tfoot>
   </table>
    <br>  
  	<table cellspacing="0" border="1" class="rpt_table" rules="all" width="800">
        <thead>
            <tr> 
                <th colspan="10" align="center"><strong>Required Yarn</strong></th>
            </tr>
            <tr>
                <th width="30" align="center">SL</th>
                <th width="120" align="center">Garment Item</th>
                <th width="70">Buyer Prov </th>
                <th width="120" align="center">Gmts Color</th>
                <th width="170" align="center">Yarn Composition</th>
                <th width="60" align="center">Count</th>
                <th width="60" align="center">Guage</th>
                <th width="70">No Of Ends </th>
                <th width="60" align="center">Yarn Color</th>
                <th width="60" align="center">Yarn Req. Qty.</th>
                <th width="60" align="center">UOM</th>
                <th width="60" align="center">Lot No</th>
                <th width="60" align="center">Yarn Store</th>
                <th align="center">Remarks</th>
            </tr>
        </thead>
        <tbody>
            <?
           $sql_qryf="SELECT a.uom_id,a.gauge, a.id, a.gmts_item_id, a.sample_mst_id, a.sample_name, a.body_part_id, a.fabric_nature_id, a.fabric_description, a.gsm, a.dia, a.SAMPLE_COLOR, a.color_data, a.color_type_id, a.width_dia_id,a.remarks_ra,a.buyer_prov,a.no_of_ends
    FROM sample_development_fabric_acc a WHERE a.sample_mst_id = $update_id AND a.form_type = 1 and  a.status_active =1 and a.is_deleted=0
ORDER BY a.id ASC";
		    $resultf=sql_select($sql_qryf);
            $k=1;
			$sl=0;
			foreach($resultf as $row)
            {
			$color_data_arr=explode('-----',$row[csf('color_data')]);
			foreach($color_data_arr as $dataArr){
				list($txtSL,$txtColor,$hiddenColorId,$txtYarnColor,$txtCount,$txtComposition,$cboType,$cboYarnSource,$txtGreyQnty,$cboUom,$txtLot,$cboStore,$text_comments,$yarn_color_id)=explode('__',$dataArr);
				$totalFinishReqQty+=$txtGreyQnty;
				?>
				<tr>
					<td align="center"><? echo $k;?></td>
					<td align="left"><? echo $garments_item[$row[csf('gmts_item_id')]];?></td>
					<td align="left"><? echo $row[csf('buyer_prov')];?></td>
					<td align="left"><? echo $color_library[$row[SAMPLE_COLOR]];?></td>
					<td align="left"><? echo $txtComposition;?></td>
					<td align="left"><? echo $count_arr[$txtCount];?></td>
					<td align="left"><? echo $gauge_arr[$row[csf('gauge')]];?></td>
					<td align="left"><? echo $row[csf('no_of_ends')];?></td>
					<td align="left"><? echo $txtYarnColor;?></td>
					<td align="right"><? echo $txtGreyQnty;?></td>
					<td align="center"><? echo $unit_of_measurement[$cboUom];?></td>
					<td align="center"><? echo $txtLot;?></td>
					<td align="center"><? echo $store_name_lib[$cboStore];?></td>
					<td align="right"><p><? echo $text_comments;?></p></td>
				 </tr>
			   <?
			   $k++;
			}
           }
           ?>
        </tbody>
        <tfoot>
            <td colspan="9" align="right"><b>Total </b></td>
            <td  align="right"><b><? echo number_format($totalFinishReqQty,2);?> </b></td>
            <td  align="right"></td>
            <td  align="right"></td>
            <td  align="right"></td>
         </tfoot>
    </table>    
    
    <br>  
 	<table cellspacing="0" border="1"  class="rpt_table" rules="all" width="800">
        <thead>
            <tr>
                 <td colspan="10" align="center"><strong>Required Accessories</strong></td>
             </tr>
             <tr>
                <th width="30" align="center">SL</th>
                <th width="120" align="center">Garment Item</th>
                <th width="100" align="center">Trims Group</th>
                <th width="100" align="center">Description</th>
                <th width="100" align="center">Brand/Supp.Ref</th>
                <th width="30" align="center">UOM</th>
                <th width="30" align="center">Req/Dzn </th>
                <th width="30" align="center">Req/Qty </th>
                <th align="center">Remarks </th>
            </tr>
        </thead>
        <tbody>
		<?
           $sql_qryA="select id,sample_mst_id,sample_name_ra,gmts_item_id_ra,trims_group_ra,description_ra,brand_ref_ra,uom_id_ra,req_dzn_ra,req_qty_ra,remarks_ra from sample_development_fabric_acc where status_active =1 and is_deleted=0 and form_type=2 and sample_mst_id='$update_id' order by id asc";

            $resultA=sql_select($sql_qryA);
            $k=1;
            $req_dzn_ra=0;
            $req_qty_ra=0;
            foreach($resultA as $rowA)
            {
                $req_dzn_ra=$req_dzn_ra+$rowA[csf('req_dzn_ra')];
                $req_qty_ra=$req_qty_ra+$rowA[csf('req_qty_ra')];
            ?>
            <tr>
                <td  align="center"><? echo $k;?></td>
                <td  align="left"><? echo $garments_item[$rowA[csf('gmts_item_id_ra')]];?></td>
                <td  align="left"><? echo $trims_group_lib[$rowA[csf('trims_group_ra')]];?></td>
                <td  align="left"><? echo $rowA[csf('description_ra')];?></td>
                <td  align="left"><? echo $rowA[csf('brand_ref_ra')];?></td>
                <td  align="center"><? echo $unit_of_measurement[$rowA[csf('uom_id_ra')]];?></td>
                <td  align="right"><? echo $rowA[csf('req_dzn_ra')];?></td>
                <td  align="right"><? echo $rowA[csf('req_qty_ra')];?></td>
                <td  align="left"><? echo $rowA[csf('remarks_ra')];?></td>
             <?
             $k++;
            }
            ?>
            </tr>
        </tbody>
        <tfoot>
            <td colspan="7" align="center"><b>Total </b></td>
            <td align="right"><b><? echo number_format($req_qty_ra,2);?> </b></td>
            <td>&nbsp;</td>
        </tfoot>
   </table> 
    <br>  
 	<table cellspacing="0" border="1" class="rpt_table" rules="all">
        <thead>
            <tr>
                <td width="150" colspan="6" align="center"><strong>Required Emebellishment</td>
            </tr>
        <tr>
            <th width="30" align="center">SL</th>
            <th width="120" align="center">Garment Item</th>
            <th width="100" align="center">Name</th>
            <th width="70" align="center">Type</th>
            <th align="center">Remarks</th>
            <th width="100">Images</th>
        </tr>
        </thead>
        <tbody>
            <?
            $sql_qry="select id,sample_mst_id,sample_name_re,gmts_item_id_re,name_re,type_re,remarks_re from sample_development_fabric_acc where sample_mst_id='$update_id' and form_type=3 and is_deleted=0  and status_active=1 order by id asc";
            $result=sql_select($sql_qry);
            $k=1;
            $type_array=array(1=>$emblishment_print_type,2=>$emblishment_embroy_type,3=>$emblishment_wash_type,4=>$emblishment_spwork_type,5=>$emblishment_gmts_type);
            foreach($result as $row)
            {
            ?>
            <tr>
                <td  align="center"><? echo $k;?></td>
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
                <td><p><? echo $row[csf('remarks_re')];?></p></td>
				<td><img src="../../../<? echo $emb_imge_arr[$row[csf('id')]];?>" width="120" height='60'></td>
             <?
			 $k++;
            }

            ?>
            </tr>
        </tbody>
   </table>     

 	<? echo signature_table(459, $data[0], "810px");?>    
     
    </div>
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
	echo load_html_head_contents("Sample Requisition Info","../../../../", 1, 1, $unicode);
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
        <? echo load_freeze_divs ("../../../../",$permission,1); ?>
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
    <script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if ($action=="sizeinfo_popup_mouseover")
{
	echo load_html_head_contents("Sample Requisition Info","../../../../", 1, 1, $unicode);
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
        <? echo load_freeze_divs ("../../../../",$permission,1); ?>
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
    <script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if($action=="load_data_to_sizeinfo")
{
	$size_arr=return_library_array( "select id,size_name from lib_size", "id", "size_name" );
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
		echo create_drop_down( "cbo_location_name", 150, $sql,'id,location_name', 0, '--- Select Location ---', 0, ""  );
	}
	else
	{
		echo create_drop_down( "cbo_location_name", 150, $sql,'id,location_name', 1, '--- Select Location ---', 0, ""  );
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
	exit();
}

if ($action=="load_drop_down_garment_item_for_not_after_order")
{
	echo create_drop_down( "cboGarmentItem_1", 100, $garments_item,"", 1, "Select Item", 0, "");
	exit();
}

if ($action=="load_drop_down_trims_group_from_budget_for_after_order")
{
 $sql="select a.item_name,a.id from lib_item_group a,wo_pre_cost_trim_cost_dtls b where  a.is_deleted=0  and a.status_active=1 and b.trim_group=a.id group by a.item_name,a.id";
echo create_drop_down( "cboRaTrimsGroup_1", 100, $sql,"id,item_name", 1, "Select Item", 0, "");
exit();
}

if ($action=="load_drop_down_fabric_nature_for_after_order")
{
 	 $dt=explode(",",$data);
 	 if(count($dt)>1)
		echo create_drop_down( "cboRfFabricNature_1", 95, $item_category,"", 1, "-- Select Fabric Nature --", $selected, "",0,$data );
	else
		 echo create_drop_down( "cboRfFabricNature_1", 95, $item_category,"", 0, "-- Select Fabric Nature --", $selected, "",0,$data );
	exit();
}

if ($action=="load_drop_down_fabric_nature_for_not_after_order")
{
	echo create_drop_down( "cboRfFabricNature_1", 95, $item_category,"", 1, "Select Item", 0, "");
	exit();
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", 0, "load_drop_down( 'requires/sweater_sample_requisition_v2_controller', this.value, 'load_drop_down_season_buyer', 'season_td');load_drop_down( 'requires/sweater_sample_requisition_v2_controller', this.value, 'load_drop_down_sample_for_buyer', 'sample_td');load_drop_down( 'requires/sweater_sample_requisition_v2_controller', this.value+'*'+1, 'load_drop_down_brand', 'brand_td');" );
	exit();
}

if ($action=="load_drop_down_sample_for_buyer")
{
	echo create_drop_down( "cboSampleName_1", 100, "select a.id,a.sample_name,b.sequ from lib_sample a,lib_buyer_tag_sample b where a.id=b.tag_sample and  b.buyer_id=$data and b.sequ  is not null and
 a.status_active=1 and a.is_deleted=0 and a.business_nature=100  group by  a.id,a.sample_name,b.sequ order by b.sequ ","id,sample_name", 1, "-- Select Sample --", $selected, "" );
 exit();
}

if ($action=="load_drop_down_buyer_req")
{
	echo create_drop_down( "cbo_buyer_name", 157, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down( 'sweater_sample_requisition_v2_controller', this.value, 'load_drop_down_season', 'season_td'); load_drop_down( 'sweater_sample_requisition_v2_controller', this.value+'*'+1, 'load_drop_down_brand', 'brand_td');" );
	exit();
}
if ($action=="load_drop_down_season")
{
	echo create_drop_down( "cbo_season_id", 90, "select id, season_name from lib_buyer_season where buyer_id='$data' and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "-- Select Season--", "", "" );
	exit();
}

if ($action=="load_drop_down_buyer_style")
{
	echo create_drop_down( "cbo_buyer_name", 157, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
	exit();
}

if ($action=="load_drop_down_buyer_inq")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
	exit();
}


if ($action=="load_drop_down_season_buyer")
{

	echo create_drop_down( "cbo_season_name", 150, "select a.id,a.season_name from lib_buyer_season a where a.status_active =1 and a.is_deleted=0 and a.buyer_id='$data'","id,season_name", 1, "-- Select Season --", $selected, "" );
	exit();
}

if ($action=="load_drop_down_agent")
{
	echo create_drop_down( "cbo_agent", 150, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$data' order by buyer_name","id,buyer_name", 1, "-- Select Agent --", $selected, "" );
	exit();
}

if ($action=="cbo_dealing_merchant")
{
	echo create_drop_down( "cbo_dealing_merchant", 172, "select id,team_member_name from lib_mkt_team_member_info where team_id='$data' and status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select Team Member --", $selected, "" );
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

		$id_mst=return_next_id( "id", "sample_development_mst", 1 ) ;
		if($db_type==0)
		{
			$new_system_id=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', '', date("Y",time()), 5, "select requisition_number_prefix,requisition_number_prefix_num from sample_development_mst where  entry_form_id=459 and company_id=$cbo_company_name and YEAR(insert_date)=".date('Y',time())." order by id desc ", "requisition_number_prefix", "requisition_number_prefix_num" ));
		}
		if($db_type==2)
		{
			$new_system_id=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', '', date("Y",time()), 5, "select requisition_number_prefix,requisition_number_prefix_num from sample_development_mst where entry_form_id=459 and company_id=$cbo_company_name and to_char(insert_date,'YYYY')=".date('Y',time())." order by id desc ", "requisition_number_prefix", "requisition_number_prefix_num" ));
		}

		$field_array="id, requisition_number_prefix, requisition_number_prefix_num, requisition_number, sample_stage_id, requisition_date, quotation_id, style_ref_no, company_id, location_id, buyer_name, season, product_dept, dealing_marchant, agent_name, buyer_ref, estimated_shipdate, remarks, inserted_by, insert_date, status_active, is_deleted, entry_form_id, is_copy, req_ready_to_approved, material_delivery_date, team_leader, season_year, brand_id";
		$data_array="(".$id_mst.",'".$new_system_id[1]."',".$new_system_id[2].",'".$new_system_id[0]."',".$cbo_sample_stage.",".$txt_requisition_date.",".$txt_quotation_id.",".$txt_style_name.",".$cbo_company_name.",".$cbo_location_name.",".$cbo_buyer_name.",".$cbo_season_name.",".$cbo_product_department.",".$cbo_dealing_merchant.",".$cbo_agent.",".$txt_buyer_ref.",".$txt_est_ship_date.",".$txt_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0,459,0,".$cbo_ready_to_approved.",".$txt_material_dlvry_date.",".$cbo_sample_team.",".$cbo_season_year.",".$cbo_brand_id.")";
		$rID=sql_insert("sample_development_mst",$field_array,$data_array,1);
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

	if ($operation==1)  // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$field_array="sample_stage_id*requisition_date*style_ref_no*buyer_name*season*product_dept*dealing_marchant*agent_name*buyer_ref*estimated_shipdate*remarks*updated_by*update_date*status_active*is_deleted*req_ready_to_approved*material_delivery_date*team_leader*season_year*brand_id";
		//txt_bhmerchant*txt_product_code
		$data_array="".$cbo_sample_stage."*".$txt_requisition_date."*".$txt_style_name."*".$cbo_buyer_name."*".$cbo_season_name."*".$cbo_product_department."*".$cbo_dealing_merchant."*".$cbo_agent."*".$txt_buyer_ref."*".$txt_est_ship_date."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0*".$cbo_ready_to_approved."*".$txt_material_dlvry_date."*".$cbo_sample_team."*".$cbo_season_year."*".$cbo_brand_id."";

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




if ($action=="save_update_delete_sample_details")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$size_arr=return_library_array( "select id,size_name from lib_size", "id", "size_name" );
	if ($operation==0)  // Insert Here
	{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
 			$id_dtls=return_next_id( "id", "sample_development_dtls", 1 ) ;
 			$field_array= "id,sample_mst_id,sample_name,gmts_item_id,smv,article_no,sample_color,sample_prod_qty,submission_qty,delv_start_date,delv_end_date,sample_charge,sample_curency,inserted_by,insert_date,status_active,is_deleted,entry_form_id,size_data,fabric_status,acc_status,embellishment_status,fab_status_id,acc_status_id,embellishment_status_id,color_combo_no";

			$ids=return_next_id( "id","sample_development_size", 1 ) ;
			$field_array_size="id, mst_id, dtls_id,size_id,bh_qty,plan_qty,dyeing_qty,test_qty,self_qty,total_qty,inserted_by, insert_date, status_active, is_deleted";

			for ($i=1;$i<=$total_row;$i++)
		    {
				$cboSampleName="cboSampleName_".$i;
				$cboGarmentItem="cboGarmentItem_".$i;
				$txtSmv="txtSmv_".$i;
				$txtArticle="txtArticle_".$i;
				$txtColor="txtColor_".$i;
				$txtcolorcombono="txtcolorcombono_".$i;
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
						$color_id = return_id( str_replace("'","",$$txtColor), $color_arr, "lib_color", "id,color_name","459");
						//echo $$txtColorName.'='.$color_id.'<br>';
						$new_array_color[$color_id]=str_replace("'","",$$txtColor);
					}
					else $color_id =  array_search(str_replace("'","",$$txtColor), $new_array_color);
				}
				else $color_id=0;


				if ($i!=1) $data_array .=",";
				$data_array .="(".$id_dtls.",".$update_id.",".$$cboSampleName.",".$$cboGarmentItem.",".$$txtSmv.",".$$txtArticle.",'".$color_id."',".$$txtSampleProdQty.",".$$txtSubmissionQty.",".$$txtDelvStartDate.",".$$txtDelvEndDate.",".$$txtChargeUnit.",".$$cboCurrency.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0,459,".$$txtAllData.",0,0,0,0,0,0,".$$txtcolorcombono.")";


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
							$size_id = return_id( $size_name, $size_arr, "lib_size", "id,size_name","459");
							//echo $$txtColorName.'='.$color_id.'<br>';
							$new_array_size[$size_id]=str_replace("'","",$size_name);
						}
						else $size_id =  array_search($size_name, $new_array_size);
					}
					else $size_id=0;


					if($i==1) $add_comma=""; else $add_comma=",";

					$data_array_size.="$add_comma(".$ids.",".$update_id.",".$id_dtls.",".$size_id.",".$bhqty.",".$plqty.",".$dyqty.",".$testqty.",".$selfqty.",".$totalqty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
					$ids=$ids+1;
				}
				$id_dtls=$id_dtls+1;

		    }

			$rID_1=sql_insert("sample_development_dtls",$field_array,$data_array,1);
			$rIDs=sql_insert("sample_development_size",$field_array_size,$data_array_size,1);

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
			$prev_ids_array=array();
			$prev_ids="SELECT id from sample_development_dtls where status_active=1 and is_deleted=0 and sample_mst_id=$update_id and entry_form_id=459";
			$prev_ids_array=array();
			foreach(sql_select($prev_ids) as $key_id=>$key_val)
			{
				$prev_ids_array[$key_val[csf("id")]]=$key_val[csf("id")];
			}

 			$id_dtls=return_next_id( "id", "sample_development_dtls", 1);

			$field_array_up="sample_name*gmts_item_id*smv*article_no*sample_color*sample_prod_qty*submission_qty*delv_start_date*delv_end_date*sample_charge*sample_curency*updated_by*update_date*size_data*color_combo_no";

			$field_array= "id, sample_mst_id, sample_name, gmts_item_id, smv,article_no, sample_color, sample_prod_qty, submission_qty, delv_start_date, delv_end_date, sample_charge, sample_curency, inserted_by, insert_date, status_active, is_deleted, entry_form_id, size_data,fabric_status,acc_status,embellishment_status,fab_status_id,acc_status_id,embellishment_status_id,color_combo_no";
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
				$txtcolorcombono="txtcolorcombono_".$i;
				$txtSampleProdQty="txtSampleProdQty_".$i;
				$txtSubmissionQty="txtSubmissionQty_".$i;
				$txtDelvStartDate="txtDelvStartDate_".$i;
				$txtDelvEndDate="txtDelvEndDate_".$i;
				$txtChargeUnit="txtChargeUnit_".$i;
				$cboCurrency="cboCurrency_".$i;
				$updateIdDtls="updateidsampledtl_".$i;
				$txtAllData="txtAllData_".$i;
				unset($prev_ids_array[str_replace("'",'',$$updateIdDtls)]);

				if(str_replace("'","",$$txtColor)!="")
				{
					if (!in_array(str_replace("'","",$$txtColor),$new_array_color))
					{
						$color_id = return_id( str_replace("'","",$$txtColor), $color_arr, "lib_color", "id,color_name","459");
						//echo $$txtColorName.'='.$color_id.'<br>';
						$new_array_color[$color_id]=str_replace("'","",$$txtColor);
					}
					else $color_id =  array_search(str_replace("'","",$$txtColor), $new_array_color);
				}
				else $color_id=0;
				
				
				if (str_replace("'",'',$$updateIdDtls)!="")
				{
					$id_arr[]=str_replace("'",'',$$updateIdDtls);

					$data_array_up[str_replace("'",'',$$updateIdDtls)] =explode("*",("".$$cboSampleName."*".$$cboGarmentItem."*".$$txtSmv."*".$$txtArticle."*'".$color_id."'*".$$txtSampleProdQty."*".$$txtSubmissionQty."*".$$txtDelvStartDate."*".$$txtDelvEndDate."*".$$txtChargeUnit."*".$$cboCurrency."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'*".$$txtAllData."*".$$txtcolorcombono.""));

				$countsize=0; $ex_data="";
				$ex_data=explode("__",str_replace("'","",$$txtAllData));
				$countsize=count($ex_data);

				$data_array_size.='';
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
								$size_id = return_id( $size_name, $size_arr, "lib_size", "id,size_name","459");
								$new_array_size[$size_id]=str_replace("'","",$size_name);
							}
							else $size_id =  array_search($size_name, $new_array_size);
						}
						else $size_id=0;


						if($i==1) $add_comma=""; else $add_comma=",";

						$data_array_size.="$add_comma(".$ids.",".$update_id.",".$$updateIdDtls.",".$size_id.",".$bhqty.",".$plqty.",".$dyqty.",".$testqty.",".$selfqty.",".$totalqty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
						$ids=$ids+1;
					}
				}
			 	else
				{
					if ($add_comma!=0) $data_array .=",";
					$data_array .="(".$id_dtls.",".$update_id.",".$$cboSampleName.",".$$cboGarmentItem.",".$$txtSmv.",".$$txtArticle.",'".$color_id."',".$$txtSampleProdQty.",".$$txtSubmissionQty.",".$$txtDelvStartDate.",".$$txtDelvEndDate.",".$$txtChargeUnit.",".$$cboCurrency.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0,459,".$$txtAllData.",0,0,0,0,0,0,".$$txtcolorcombono.")";

				$countsize=0; $ex_data="";
				$ex_data=explode("__",str_replace("'","",$$txtAllData));
				$countsize=count($ex_data);

				$data_array_size.='';
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
							$size_id = return_id( $size_name, $size_arr, "lib_size", "id,size_name","459");
							$new_array_size[$size_id]=str_replace("'","",$size_name);
						}
						else $size_id =  array_search($size_name, $new_array_size);
					}
					else $size_id=0;


					if($i==1) $add_comma=""; else $add_comma=",";

					$data_array_size.="$add_comma(".$ids.",".$update_id.",".$id_dtls.",".$size_id.",".$bhqty.",".$plqty.",".$dyqty.",".$testqty.",".$selfqty.",".$totalqty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
					$ids=$ids+1;
				}


					$id_dtls=$id_dtls+1;
					$add_comma++;
				}




		    }

			$flag=1;
			if($data_array!="")
			{
				$rID_dtls=sql_insert("sample_development_dtls",$field_array,$data_array,0);
				$rID_size=sql_insert("sample_development_size",$field_array_size,$data_array_size,1);
				if($rID_dtls && $rID_size) $flag=1; else $flag=0;
			}

			if($data_array_up!="")
			{
				$rID_size_dlt=execute_query( "delete from sample_development_size where mst_id=$update_id",0);
				$rIDs=sql_insert("sample_development_size",$field_array_size,$data_array_size,1);
				$rID1=execute_query(bulk_update_sql_statement("sample_development_dtls", "id",$field_array_up,$data_array_up,$id_arr ));
				if($rID1) $flag=1; else $flag=0;
			}
			$del_ids=implode(",",$prev_ids_array );
			if($del_ids!="" || $del_ids!=0)
			{

				$fields="status_active*is_deleted";
				$delDtls=sql_multirow_update("sample_development_dtls",$fields,"0*1","id",$del_ids,0);
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

		$is_approved=return_field_value("is_approved","sample_development_mst","id=$update_id and entry_form_id=459 and status_active=1 and is_deleted=0");
		if($is_approved==3){
			$is_approved=1;
		}
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

		$rID=sql_delete("sample_development_dtls",$field_array,$data_array,"sample_mst_id*entry_form_id","".$update_id."*459",0);
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
	$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$sample_details = sql_select("SELECT sample_name, gmts_item_id, sample_prod_qty FROM sample_development_dtls WHERE status_active =1 AND is_deleted = 0 AND sample_mst_id = $update_id");
	$sample_arr = array();
	foreach ($sample_details as $value) {
		$sample_arr[$value[csf('sample_name')]][$value[csf('gmts_item_id')]] = $value[csf('sample_prod_qty')];
	}
	if ($operation==0)  // Insert Here
	{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}

			$id_dtls=return_next_id( "id", "sample_development_fabric_acc", 1 ) ;
			$field_array= "id,sample_mst_id,sample_name,gmts_item_id,body_part_id,fabric_nature_id,fabric_description,sample_color,color_data,color_type_id,uom_id,remarks_ra,required_qty,form_type,development_no,gauge,determination_id,buyer_prov,no_of_ends,inserted_by,insert_date,status_active,is_deleted";


			$field_array_col="id, mst_id, dtls_id,color_id,fabric_color,qnty,count,composition,type,yarn_source,lot,store_id,comments,inserted_by, insert_date, status_active, is_deleted";
			$idColorTbl=return_next_id( "id","sample_development_rf_color", 1 ) ;
			for ($i=1;$i<=$total_row;$i++)
		    {
				
				$cboRfSampleName='cboRfSampleName_'.$i;
				$txtRfDevelopmentNo='txtRfDevelopmentNo_'.$i;
				$cboRfGarmentItem='cboRfGarmentItem_'.$i;
				$cboRfBodyPart='cboRfBodyPart_'.$i;
				$cboRfFabricNature='cboRfFabricNature_'.$i;
				$txtRfFabricDescription='txtRfFabricDescription_'.$i;
				$txtRfGauge='txtRfGauge_'.$i;
				$txtRfColor='txtRfColor_'.$i;
				$cboRfColorType='cboRfColorType_'.$i;
				$cboRfUom='cboRfUom_'.$i;
				$txtRfRemarks='txtRfRemarks_'.$i;
				$txtRfReqQty='txtRfReqQty_'.$i;
				$updateidRequiredDtl='updateidRequiredDtl_'.$i;
				$txtRfColorAllData='txtRfColorAllData_'.$i;
				$libyarncountdeterminationid="libyarncountdeterminationid_".$i;
				$cboRfGmtsColorId="cboRfGmtsColorId_".$i;
				
				$txtBuyerProv="txtBuyerProv_".$i;
				$txtNoOfEnds="txtNoOfEnds_".$i;
				
				$ex_data="";
 				$ex_data=explode("-----",str_replace("'","",$$txtRfColorAllData));
 				$new_rf_color_all_data="";
 				foreach($ex_data as $color_data)
 				{
 					$ex_size_data=explode("__",$color_data);
 					$yarnColor=$ex_size_data[3];
					if(str_replace("'","",$yarnColor)!="")
					{
						if (!in_array(str_replace("'","",$yarnColor),$new_array_color))
						{
							$yarn_color_id = return_id( str_replace("'","",$yarnColor), $color_arr, "lib_color", "id,color_name","459");
							$new_array_color[$yarn_color_id]=str_replace("'","",$yarnColor);
						}
						else $yarn_color_id =  array_search(str_replace("'","",$contrast), $new_array_color);
					}
					else $yarn_color_id=0;
					
					if($new_rf_color_all_data=="")
					{
						$new_rf_color_all_data.=$color_data."__".$yarn_color_id;
					}
					else
					{
						$new_rf_color_all_data.="-----".$color_data."__".$yarn_color_id;
					}

 				}



				if ($i!=1) $data_array .=",";
			
				$data_array .="(".$id_dtls.",".$update_id.",".$$cboRfSampleName.",".$$cboRfGarmentItem.",".$$cboRfBodyPart.",".$$cboRfFabricNature.",".$$txtRfFabricDescription.",".$$cboRfGmtsColorId.",'".$new_rf_color_all_data."',".$$cboRfColorType.",".$$cboRfUom.",".$$txtRfRemarks.",".$$txtRfReqQty.",1,".$$txtRfDevelopmentNo.",".$$txtRfGauge.",".$$libyarncountdeterminationid.",'".str_replace("'",'',$$txtBuyerProv)."',".$$txtNoOfEnds.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				
				//$rId_rf_status=execute_query( "update sample_development_dtls set fabric_status=1,fab_status_id=".$id_dtls." where sample_mst_id=$update_id and sample_name=".$$cboRfSampleName."",0);


  				$data_array_col.='';
				$ex_data="";
 				$ex_data=explode("-----",str_replace("'","",$new_rf_color_all_data));
 				$add_comm="";
  				foreach($ex_data as $color_data)
				{
					list($txtSL,$txtColor,$hiddenColorId,$txtYarnColor,$txtCount,$txtComposition,$cboType,$cboYarnSource,$txtGreyQnty,$cboUom,$txtLot,$cboStore,$txtComments,$yarn_color_id)=explode('__',$color_data);

 					 if($add_comm) $add_comm.=","; else $add_comm.="";
  					$data_array_col.="$add_comm(".$idColorTbl.",".$update_id.",".$id_dtls.",'".$hiddenColorId."','".$yarn_color_id."','".$txtGreyQnty."','".$txtCount."','".$txtComposition."','".$cboType."','".$cboYarnSource."','".$txtLot."',".$cboStore.",'".$txtComments."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
					
					$idColorTbl = $idColorTbl + 1;
				}
				$id_dtls=$id_dtls+1;
		    }
			
			//echo "10**". $data_array;die;
			
			// echo "10**".$data_array_col;oci_rollback($con);die;
			
			
			$rID_1=sql_insert("sample_development_fabric_acc",$field_array,$data_array,1);
			$rIDs=sql_insert("sample_development_rf_color",$field_array_col,$data_array_col,1);
 			
			 //echo "10**".$rID_1.'**'.$data_array_col; oci_rollback($con);die;
			
			if($db_type==0)
			{
				if($rID_1 && $rIDs){
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
				if($rID_1  && $rIDs)
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
			$prev_ids="SELECT id from sample_development_fabric_acc where status_active=1 and is_deleted=0 and sample_mst_id=$update_id and form_type=1";
			$prev_ids_array=array();
			foreach(sql_select($prev_ids) as $key_id=>$key_val)
			{
				$prev_ids_array[$key_val[csf("id")]]=$key_val[csf("id")];
			}

			$id_dtls=return_next_id( "id", "sample_development_fabric_acc", 1);
			

			$field_array_up="sample_name*gmts_item_id*body_part_id*fabric_nature_id*fabric_description*sample_color*color_data*color_type_id*uom_id*remarks_ra*required_qty*development_no*gauge*determination_id*buyer_prov*no_of_ends*updated_by*update_date";
			
			$field_array= "id,sample_mst_id,sample_name,gmts_item_id,body_part_id,fabric_nature_id,fabric_description,sample_color,color_data,color_type_id,uom_id,remarks_ra,required_qty,form_type,development_no,gauge,determination_id,inserted_by,insert_date,status_active,is_deleted";			
			$idColorTbl=return_next_id( "id","sample_development_rf_color", 1 ) ;
			$field_array_col="id, mst_id, dtls_id,color_id,fabric_color,qnty,count,composition,type,yarn_source,lot,store_id,comments,inserted_by, insert_date, status_active, is_deleted";

			$add_comma=0; $data_array=""; //echo "10**";
			$pp=0;
			for ($i=1;$i<=$total_row;$i++)
		    {
				
				$cboRfSampleName='cboRfSampleName_'.$i;
				$txtRfDevelopmentNo='txtRfDevelopmentNo_'.$i;
				$cboRfGarmentItem='cboRfGarmentItem_'.$i;
				$cboRfBodyPart='cboRfBodyPart_'.$i;
				$cboRfFabricNature='cboRfFabricNature_'.$i;
				$txtRfFabricDescription='txtRfFabricDescription_'.$i;
				$txtRfGauge='txtRfGauge_'.$i;
				$txtRfColor='txtRfColor_'.$i;
				$cboRfColorType='cboRfColorType_'.$i;
				$cboRfUom='cboRfUom_'.$i;
				$txtRfRemarks='txtRfRemarks_'.$i;
				$txtRfReqQty='txtRfReqQty_'.$i;
				$updateidRequiredDtl='updateidRequiredDtl_'.$i;
				$txtRfColorAllData='txtRfColorAllData_'.$i;
				$libyarncountdeterminationid="libyarncountdeterminationid_".$i;
				$cboRfGmtsColorId="cboRfGmtsColorId_".$i;
				
				$txtBuyerProv="txtBuyerProv_".$i;
				$txtNoOfEnds="txtNoOfEnds_".$i;
				
				unset($prev_ids_array[str_replace("'",'',$$updateidRequiredDtl)]);

				if (str_replace("'",'',$$updateidRequiredDtl)!="")
				{
					$ex_data="";
					$ex_data=explode("-----",str_replace("'","",$$txtRfColorAllData));
					$new_rf_color_all_data="";
					foreach($ex_data as $color_data)
					{
						$ex_size_data=explode("__",$color_data);

						$yarnColor=$ex_size_data[3];
						if(str_replace("'","",$yarnColor)!="")
						{
							if (!in_array(str_replace("'","",$yarnColor),$new_array_color))
							{
								$yarn_color_id = return_id( str_replace("'","",$yarnColor), $color_arr, "lib_color", "id,color_name","459");
								$new_array_color[$yarn_color_id]=str_replace("'","",$yarnColor);

							}
							else $yarn_color_id =  array_search(str_replace("'","",$yarnColor), $new_array_color);
						}
						else $yarn_color_id=0;
						
						if($new_rf_color_all_data=="")
						{
							$new_rf_color_all_data.=$color_data."__".$yarn_color_id;
						}
						else
						{
							$new_rf_color_all_data.="-----".$color_data."__".$yarn_color_id;
						}
					}

					$id_arr[]=str_replace("'",'',$$updateidRequiredDtl);
					$data_array_up[str_replace("'",'',$$updateidRequiredDtl)] =explode("*",("".$$cboRfSampleName."*".$$cboRfGarmentItem."*".$$cboRfBodyPart."*".$$cboRfFabricNature."*".$$txtRfFabricDescription."*".$$cboRfGmtsColorId."*'".str_replace("'",'',$new_rf_color_all_data)."'*".$$cboRfColorType."*".$$cboRfUom."*'".str_replace("'",'',$$txtRfRemarks)."'*".$$txtRfReqQty."*".$$txtRfDevelopmentNo."*".$$txtRfGauge."*".$$libyarncountdeterminationid."*'".str_replace("'",'',$$txtBuyerProv)."'*".$$txtNoOfEnds."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
					
					//$rId_rf_status=execute_query( "update sample_development_dtls set fabric_status=0 where sample_mst_id=$update_id and fab_status_id=".$$updateidRequiredDtlf."",0);
					//$rId_rf_status_ac=execute_query( "update sample_development_dtls set fabric_status=1,fab_status_id=".$$updateidRequiredDtlf." where sample_mst_id=$update_id and sample_name=".$$cboRfSampleName."",0);

					$ex_data="";
					$ex_data=explode("-----",str_replace("'","",$new_rf_color_all_data));
					$cc=0;$add_comm="";
					foreach($ex_data as $color_data)
					{
						list($txtSL,$txtColor,$hiddenColorId,$txtYarnColor,$txtCount,$txtComposition,$cboType,$cboYarnSource,$txtGreyQnty,$cboUom,$txtLot,$cboStore,$txtComments,$yarn_color_id)=explode('__',$color_data);
						
						if($add_comm) $add_comm.=","; else $add_comm.="";
  						$data_array_col.="$add_comm(".$idColorTbl.",".$update_id.",".$id_dtls.",'".$hiddenColorId."','".$yarn_color_id."','".$txtGreyQnty."','".$txtCount."','".$txtComposition."','".$cboType."','".$cboYarnSource."','".$txtLot."',".$cboStore.",'".$txtComments."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
						$idColorTbl=$idColorTbl+1;
						$cc++;$add_comma++;
					}
				}
			 	else
				{
					$ex_data="";
					$ex_data=explode("-----",str_replace("'","",$$txtRfColorAllData));
					$new_rf_color_all_data="";
					foreach($ex_data as $color_data)
					{
						$ex_size_data=explode("__",$color_data);
						$yarnColor=$ex_size_data[3];
						if(str_replace("'","",$yarnColor)!="")
						{
							if (!in_array(str_replace("'","",$yarnColor),$new_array_color))
							{
								$yarn_color_id = return_id( str_replace("'","",$yarnColor), $color_arr, "lib_color", "id,color_name","459");
								$new_array_color[$yarn_color_id]=str_replace("'","",$yarnColor);

							}
							else $yarn_color_id =  array_search(str_replace("'","",$yarnColor), $new_array_color);
						}
						else $yarn_color_id=0;
						
						if($new_rf_color_all_data=="")
						{
							$new_rf_color_all_data.=$color_data."_".$yarn_color_id;
						}
						else
						{
							$new_rf_color_all_data.="-----".$color_data."_".$yarn_color_id;
						}

					}
					
					
 					if ($pp) $data_array .=",";
					$data_array .="(".$id_dtls.",".$update_id.",".$$cboRfSampleName.",".$$cboRfGarmentItem.",".$$cboRfBodyPart.",".$$cboRfFabricNature.",".$$txtRfFabricDescription.",".$$cboRfGmtsColorId.",'".$new_rf_color_all_data."',".$$cboRfColorType.",".$$cboRfUom.",".$$txtRfRemarks.",".$$txtRfReqQty.",1,".$$txtRfDevelopmentNo.",".$$txtRfGauge.",".$$libyarncountdeterminationid.",'".str_replace("'",'',$$txtBuyerProv)."',".$$txtNoOfEnds.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
					
					
					//$rId_rf_status=execute_query( "update sample_development_dtls set fabric_status=1,fab_status_id=".$id_dtls." where sample_mst_id=$update_id and sample_name=".$$cboRfSampleName."",0);

  					$ex_datas="";
					$ex_datas=explode("-----",str_replace("'","",$new_rf_color_all_data));
					$data_array_cols.='';
					$kk=1;
  					foreach($ex_datas as $color_datas)
					{
						list($txtSL,$txtColor,$hiddenColorId,$txtYarnColor,$txtCount,$txtComposition,$cboType,$cboYarnSource,$txtGreyQnty,$cboUom,$txtLot,$cboStore,$txtComments,$yarn_color_id)=explode('__',$color_datas);
						if($kk==1) $add_comma=""; else $add_comma=",";
						$data_array_cols.="$add_comma(".$idColorTbl.",".$update_id.",".$id_dtls.",'".$hiddenColorId."','".$yarn_color_id."','".$txtGreyQnty."','".$txtCount."','".$txtComposition."','".$cboType."','".$cboYarnSource."','".$txtLot."',".$cboStore.",'".$txtComments."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
						
						$idColorTbl=$idColorTbl+1;
						$kk++;

					}
					$id_dtls=$id_dtls+1;
					//$rId_rf_status=execute_query( "update sample_development_dtls set fabric_status=1 where sample_mst_id=$update_id and sample_name=".$$cboRfSampleName."",0);
					$add_comma++;
					$pp++;
				}
		    }
			
 			 //echo "10**".$data_array_cols;die;
			
			
			$flag=1;
 			if($data_array_up!="")
			{
				$rID_size_dlt=execute_query( "delete from sample_development_rf_color where mst_id=$update_id",0);
				$rIDs=sql_insert("sample_development_rf_color",$field_array_col,$data_array_col,1);
				$rID1=execute_query(bulk_update_sql_statement("sample_development_fabric_acc", "id",$field_array_up,$data_array_up,$id_arr ));
				
				
				//echo "10**".bulk_update_sql_statement("sample_development_fabric_acc", "id",$field_array_up,$data_array_up,$id_arr );oci_rollback($con);die;
				
				if($rID1)
				{
					$del_ids=implode(",",$prev_ids_array );
					if($del_ids)
					{
						execute_query( "delete from sample_development_fabric_acc where id  in($del_ids)",0);
					}


				}
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
		$is_approved=return_field_value("is_approved","sample_development_mst","id=$update_id and entry_form_id=459 and status_active=1 and is_deleted=0");
		if($is_approved==3){
			$is_approved=1;
		}
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

		$is_approved=return_field_value("is_approved","sample_development_mst","id=$update_id and entry_form_id=459 and status_active=1 and is_deleted=0");
		if($is_approved==3){
			$is_approved=1;
		}
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

		$is_approved=return_field_value("is_approved","sample_development_mst","id=$update_id and entry_form_id=459 and status_active=1 and is_deleted=0");
		if($is_approved==3){
			$is_approved=1;
		}
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


if($action=="style_id_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Sample Requisition Info","../../../../", 1, 1, $unicode);
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
				<table width="900" cellspacing="0" cellpadding="0" class="rpt_table" align="center">
					<tr>
						<td align="center" width="100%">
							<table  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
								<thead>
									<th  colspan="6">
										<?
										echo create_drop_down( "cbo_search_category", 140, $string_search_type,'', 1, "-- Search Catagory --" );
										?>
									</th>

								</thead>
								<thead>
									<th width="140">Company Name</th>
									<th width="160">Buyer Name</th>
									<th width="130">Style ID</th>
									<th  width="130" >Style Name</th>
									<th width="200">Est. Ship Date Range</th>
									<th width="80"><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
								</thead>
								<tr>
									<td width="140">
										<input type="hidden" id="selected_job">
										<?
										echo create_drop_down( "cbo_company_mst", 140, "select id,company_name from lib_company where status_active =1 and is_deleted=0  order by company_name","id,company_name",1, "-- Select Company --", '',"load_drop_down( 'sweater_sample_requisition_v2_controller', this.value, 'load_drop_down_buyer_style', 'buyer_td_st' );" );
										?>
									</td>
									<td id="buyer_td_st" width="160">
										<?
										echo create_drop_down( "cbo_buyer_name", 157, $blank_array,'', 1, "-- Select Buyer --" );
										?>
									</td>
									<td width="130">
										<input type="text" style="width:130px" class="text_boxes"  name="txt_style_id" id="txt_style_id"  />
									</td>
									<td width="130" align="center">
										<input type="text" style="width:130px" class="text_boxes"  name="txt_style_name1" id="txt_style_name1"  />
									</td>
									<td align="center">
										<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px"> To
										<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
									</td>
									<td align="center">
										<input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_style_id').value+'_'+document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_style_name1').value+'_'+document.getElementById('cbo_year_selection').value, 'create_style_id_search_list_view', 'search_div', 'sweater_sample_requisition_v2_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:80px;" />
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td  align="center" height="40" valign="middle">
							<?
							echo create_drop_down( "cbo_year_selection", 70, $year,"", 1, "-- Select --", date('Y'), "",0 );
							?>
							<? echo load_month_buttons();  ?>
						</td>
					</tr>
					<tr>
						<td align="center" valign="top" id="search_div"></td>
					</tr>
				</table>
			</form>
		</div>
	</body>
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="inquiry_popup")
{
  	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
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
	<table width="500" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
            <thead>
                    <th colspan="4"> </th>
                    <th  >
                      <?
                       echo create_drop_down( "cbo_string_search_type", 140, $string_search_type,'', 1, "--Searching Type--" );
                      ?>
                    </th>
                   <th colspan="4" > </th>
           </thead>
            <thead>
                <tr>
                   <th width="150">Company Name</th>
                    <th width="150">Buyer Name</th>
                    <th width="100">Inquery ID</th>
                    <th width="80">Year</th>
                    <th width="150" >Master/Style Reff.</th>
                    <th width="100" >BOM</th>
                    <th width="100" >Buyer Inquery No</th>
                    <th width="100">Inquery Date </th>
                    <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:80px" class="formbutton"  /></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                <td>
                    <?
                    	$company_cond=set_user_lavel_filtering(' and comp.id','company_id');
                        echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0  $company_cond order by company_name","id,company_name", 1, "-- Select Company --",'', "load_drop_down( 'sweater_sample_requisition_v2_controller', this.value, 'load_drop_down_buyer_inq', 'buyer_td_inq' );");
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
                    <td>
                    	<input type="text" style="width:100px" class="text_boxes"  name="txt_bom" id="txt_bom" />
                    </td>
                    <td width="" align="center" >
                        <input type="text" style="width:80px" class="text_boxes"  name="txt_requst_no" id="txt_requst_no" />
                    </td>
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="  Date" />

                    </td>
                    <td align="center">
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_inquery_id').value+'_'+document.getElementById('cbo_year').value+'_'+document.getElementById('txt_requst_no').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_bom').value, 'create_inquiry_search_list_view', 'search_div', 'sweater_sample_requisition_v2_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:80px;" />
                    </td>
            </tr>
        	<tr>
            	<td align="center" height="40" valign="middle" colspan="9">


                     <input type="hidden" id="txt_inquiry_id" value="" />

                </td>
            </tr>
            </tbody>
         </tr>
        </table>
        <div align="center" valign="top" id="search_div"> </div>
        </form>
   </div>
</body>
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
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
	$txt_bom = $ex_data[8];
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
	$bom_cond="";
	if(!empty($txt_bom))
	{
		$bom_cond=" and bom_no='".$txt_bom."'";
	}
	$season_buyer_wise_arr = return_library_array("select id,season_name from  lib_buyer_season ","id","season_name");
	$buyer_arr = return_library_array("select id,buyer_name from  lib_buyer ","id","buyer_name");
	$company_arr = return_library_array("select id,company_name from lib_company","id","company_name");
	$arr=array(0=>$company_arr,1=>$buyer_arr,8=>$season_buyer_wise_arr);
	 $sql = "select system_number_prefix_num,system_number,buyer_request, company_id,bom_no,buyer_id,season_buyer_wise,inquery_date,style_refernce,status_active,extract(year from insert_date) as year ,id from wo_quotation_inquery where is_deleted=0 and entry_form=457 $company_name $buyer_name $sql_cond $inquery_id_cond $request_no $inquery_date $bom_cond order by system_number_prefix_num ";
	//echo $sql;
	echo create_list_view("list_view", "Company Name,Buyer Name,Inquery ID,Year,Buyer Inquery No,Style Reff.,BOM, Inquery Date,Season","120,120,70,50,70,120,100,90,120","975","260",0, $sql , "js_set_value", "id", "", 1, "company_id,buyer_id,0,0,0,0,0,0,season_buyer_wise", $arr, "company_id,buyer_id,system_number_prefix_num,year,buyer_request,style_refernce,bom_no,inquery_date,season_buyer_wise", "",'','0') ;
	?>
	</body>
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="populate_data_from_inquiry_search")
{
	$sql = sql_select("select  id,company_id,buyer_id,season_buyer_wise,inquery_date,style_refernce,department_name,remarks,dealing_marchant,gmts_item,est_ship_date,color,season,bom_no,season_year,brand_id from wo_quotation_inquery where id='$data' order by id");
	foreach($sql as $row)
	{
		echo "load_drop_down( 'requires/sweater_sample_requisition_v2_controller', '".$row[csf("company_id")]."', 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/sweater_sample_requisition_v2_controller', '".$row[csf("company_id")]."', 'load_drop_down_location', 'location_td' );load_drop_down( 'requires/sweater_sample_requisition_v2_controller', '".$row[csf("company_id")]."', 'load_drop_down_agent', 'agent_td' );load_drop_down( 'requires/sweater_sample_requisition_v2_controller','".$row[csf("buyer_id")]."', 'load_drop_down_season_buyer', 'season_td');load_drop_down( 'requires/sweater_sample_requisition_v2_controller','".$row[csf("buyer_id")]."', 'load_drop_down_brand', 'brand_td');load_drop_down( 'requires/sweater_sample_requisition_v2_controller','".$row[csf("buyer_id")]."', 'load_drop_down_sample_for_buyer', 'sample_td');load_drop_down( 'requires/sweater_sample_requisition_v2_controller', '".$row[csf("gmts_item")]."', 'load_drop_down_garment_item_for_after_order', 'item_id_1')\n";
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";
		//echo "$('#cbo_location_name').val('".$result[csf('location_name')]."');\n";
		echo "document.getElementById('txt_quotation_id').value = '".$row[csf("id")]."';\n";
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_id")]."';\n";
		echo "document.getElementById('txt_bom').value = '".$row[csf("bom_no")]."';\n";
		echo "document.getElementById('cbo_season_year').value = '".$row[csf("season_year")]."';\n";
		echo "document.getElementById('cbo_brand_id').value = '".$row[csf("brand_id")]."';\n";
		echo "document.getElementById('txt_style_name').value = '".$row[csf("style_refernce")]."';\n";
		echo "document.getElementById('txt_remarks').value = '".$row[csf("remarks")]."';\n";
		echo "document.getElementById('cbo_product_department').value = '".$row[csf("department_name")]."';\n";
		echo "document.getElementById('cbo_season_name').value = '".$row[csf("season_buyer_wise")]."';\n";
		echo "document.getElementById('cbo_dealing_merchant').value = ".$row[csf("dealing_marchant")].";\n";
		echo "document.getElementById('txt_est_ship_date').value = '".change_date_format($row[csf("est_ship_date")],"dd-mm-yyyy","-")."';\n";

	}
	exit();
}

if($action=="create_style_id_search_list_view")
{
	$data=explode('_',$data);
	if($data[7])$year_cond=" and to_char(insert_date,'YYYY')=$data[7]";
	if ($data[2]!=0) $company=" and company_name='$data[2]'"; else { echo "Please Select Company First."; die; }
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
		$sql= "SELECT id,job_no_prefix_num,SUBSTRING_INDEX(`insert_date`, '-', 1) as year,company_name,buyer_name,style_ref_no,product_dept,team_leader,dealing_marchant from wo_po_details_master where  status_active=1 and is_deleted=0 $company $buyer $style_id_cond $style_cond  order by id";
	}

	if($db_type==2)
	{
		$sql= "SELECT id,job_no_prefix_num,to_char(insert_date,'YYYY') as year,company_name,buyer_name,style_ref_no,product_dept,team_leader,dealing_marchant from wo_po_details_master where  status_active=1 and is_deleted=0 $company $buyer $style_id_cond $style_cond $year_cond order by id";
	}
		 	echo  create_list_view("list_view", "Year,Job No,Buyer Name,Style Name,Product Department,Team Leader,Dealing Merchant", "60,140,140,100,90,90,90","900","240",0, $sql , "js_set_value", "id", "", 1, "0,0,buyer_name,0,product_dept,team_leader,dealing_marchant,0", $arr , "year,job_no_prefix_num,buyer_name,style_ref_no,product_dept,team_leader,dealing_marchant", "",'','0,0,0,0,0,0,0') ;

	exit();
}

if($action=="populate_data_from_search_popup")
{

	$res = sql_select("select * from wo_po_details_master where id=$data");

 	foreach($res as $result)
	{
		//echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_sweater_sample_requisition_mst_info',1);\n";
		echo "load_drop_down( 'requires/sweater_sample_requisition_v2_controller', '".$result[csf("company_name")]."', 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/sweater_sample_requisition_v2_controller', '".$result[csf("company_name")]."', 'load_drop_down_location', 'location_td' );load_drop_down( 'requires/sweater_sample_requisition_v2_controller', '".$result[csf("company_name")]."', 'load_drop_down_agent', 'agent_td' );load_drop_down( 'requires/sweater_sample_requisition_v2_controller', '".$result[csf("company_name")]."', 'load_drop_down_buyer', 'buyer_td');load_drop_down( 'requires/sweater_sample_requisition_v2_controller','".$result[csf("buyer_name")]."', 'load_drop_down_season_buyer', 'season_td');load_drop_down( 'requires/sweater_sample_requisition_v2_controller','".$result[csf("buyer_name")]."', 'load_drop_down_sample_for_buyer', 'sample_td');load_drop_down( 'requires/sweater_sample_requisition_v2_controller', '".$result[csf("gmts_item_id")]."', 'load_drop_down_garment_item_for_after_order', 'item_id_1');load_drop_down( 'requires/sweater_sample_requisition_v2_controller', '".$result[csf("garments_nature")]."', 'load_drop_down_fabric_nature_for_after_order', 'rf_fabric_nature_1');\n";
		//load_drop_down( 'requires/sweater_sample_requisition_v2_controller', '".$result[csf("item_number_id")]."', 'load_drop_down_trims_group_for_after_order', 'ra_trims_group_1');

		echo "$('#txt_quotation_id').val('".$result[csf('id')]."');\n";
		echo "$('#txt_quotation_job_no').val('".$result[csf('job_no')]."');\n";
		echo "$('#cbo_company_name').val('".$result[csf('company_name')]."');\n";
		echo "$('#cbo_location_name').val('".$result[csf('location_name')]."');\n";
		echo "$('#cbo_buyer_name').val('".$result[csf('buyer_name')]."');\n";
		//echo "document.getElementById('txt_quotation_id').value = '".$result[csf("quotation_id")]."';\n";
		echo "$('#txt_style_name').val('".$result[csf('style_ref_no')]."');\n";
		echo "$('#cbo_product_department').val('".$result[csf('product_dept')]."');\n";
		echo "$('#cbo_agent').val('".$result[csf('agent_name')]."');\n";
		//echo "$('#cbo_season_name').val('".$result[csf('season')]."');\n";
		$season_id=0;
		if($result[csf("season_matrix")]!=0) $season_id=$result[csf("season_matrix")];
		else $season_id=$result[csf("season_buyer_wise")];

		echo "$('#cbo_season_name').val('".$result[csf('season_id')]."');\n";
		echo "$('#cbo_dealing_merchant').val('".$result[csf('dealing_marchant')]."');\n";
		echo "$('#txt_product_code').val('".$result[csf('product_code')]."');\n";
		//echo "$('#txt_bhmerchant').val('".$result[csf('bh_merchant')]."');\n";

		//echo "$('#txt_est_ship_date').val('".change_date_format($result[csf('estimated_shipdate')],'dd-mm-yyyy','-')."');\n";
		echo "$('#txt_remarks').val('".$result[csf('remarks')]."');\n";

		//echo "$('#update_id').val('".$result[csf('id')]."');\n";


  	}
 	exit();
}

if($action=="requisition_id_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Sweater Sample Requisition Info","../../../../", 1, 1, $unicode);
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
		
	function show_system_id(){
		
		if(document.getElementById('txt_requisition_num').value=='' && document.getElementById('txt_style_id').value==''  &&  document.getElementById('txt_style_name1').value==''  &&  document.getElementById('cbo_sample_stage').value==0 && ( document.getElementById('txt_date_from').value=='' || document.getElementById('txt_date_to').value=='')){
			var fillData="cbo_company_mst*txt_date_from*txt_date_to";
			var fillMessage=" Company Name*Est. Ship From Date*Est. Ship To Date";
		}
		else
		{
			var fillData="cbo_company_mst";
			var fillMessage="Company Name Stage";
		}
		
		if (form_validation(fillData,fillMessage)==false)
		{
			return;
		}
		else{
			show_list_view ( document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_style_id').value+'_'+document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_style_name1').value+'_'+document.getElementById('txt_requisition_num').value+'_'+document.getElementById('cbo_sample_stage').value+'_'+document.getElementById('cbo_brand_id').value+'_'+document.getElementById('cbo_season_year').value+'_'+document.getElementById('cbo_season_id').value, 'create_requisition_id_search_list_view', 'search_div', 'sweater_sample_requisition_v2_controller', 'setFilterGrid(\'list_view\',-1)')
		}
	}
		
		
    </script> 
</head>
<body>
	<div align="center" style="width:1300px;" >
	<form name="searchsampledevelopmentfrm_1"  id="searchsampledevelopmentfrm_1" autocomplete="off">
		<table width="1300" cellspacing="0" cellpadding="0" align="center">
    		<tr>
        		<td align="center" width="100%">
            		<table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                        <thead>
                        	<th  colspan="11">
                              <? echo create_drop_down( "cbo_search_category", 140, $string_search_type,'', 1, "-- Search Catagory --" ); ?>
                            </th>
                        </thead>
                        <thead>
                        	<th class="must_entry_caption" width="140">Company Name</th>
                            <th width="157">Buyer Name</th>
                            <th width="90">Brand</th>
                            <th width="70">Requisition No</th>
                            <th width="100">Style ID</th>
                            <th width="90">Season Year</th>
                            <th width="90">Season</th>
                            <th  width="120" >Style Name</th>
                            <th class="must_entry_caption" width="90">Sample Stage</th>
                            <th width="160">Requisition Date</th>
                            <th width="80"><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
                        </thead>
        				<tr>
                        	<td width="140">
                            	<input type="hidden" id="selected_job">
								<?
                                    echo create_drop_down( "cbo_company_mst", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "-- Select Company --", $company_id,"load_drop_down( 'sweater_sample_requisition_v2_controller', this.value, 'load_drop_down_buyer_req', 'buyer_td_req' );" );
                                ?>
                    		</td>
                   			<td id="buyer_td_req" width="157">
								 <?
                                    echo create_drop_down( "cbo_buyer_name", 157, $blank_array,'', 1, "-- Select Buyer --" );
                                ?>
                            </td>
                            <td id="brand_td"><? echo create_drop_down( "cbo_brand_id", 90, $blank_array,'', 1, "Brand",$selected, "" ); ?>
                            <td width="70">
								<input type="text" style="width:70px" class="text_boxes" name="txt_requisition_num" id="txt_requisition_num"  />
                            </td>

                            <td width="100">
								<input type="text" style="width:100px" class="text_boxes"  name="txt_style_id" id="txt_style_id"  />
                            </td>
        					<td><? echo create_drop_down( "cbo_season_year", 90, create_year_array(),"", 1,"-Year-", 1, "",0,"" ); ?></td>
                            <td id="season_td"><? echo create_drop_down( "cbo_season_id", 90, $blank_array,'', 1, "Season",$selected, "" ); ?></td>
                            <td width="90" align="center">
                                <input type="text" style="width:90px" class="text_boxes"  name="txt_style_name1" id="txt_style_name1"  />
                            </td>

                            <td width="90" align="center">
                                <?
                    				echo create_drop_down( "cbo_sample_stage", 90, $sample_stage, "", 1, "-Select Stage-", $selected, "", "", "" );
                    			?>
                            </td>

                            <td  width="160">
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px"> To
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px">
                            </td>
                            <td align="center" width="80">
                                <input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_system_id()" style="width:80px;" />
                            </td>
        				</tr>
                        <tr>
                            <td colspan="11" align="center"><?=load_month_buttons(1); ?></td>
                        </tr>
             		</table>
          		</td>
        	</tr>
        	<tr>
            	<td align="center" valign="top" id="search_div"></td>
        	</tr>
    	</table>
    </form>
	</div>
</body>
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if ($action=="load_drop_down_brand")
{
	$data_arr = explode("*", $data);
	if($data_arr[1] == 1) $width=90; else $width=150;
	echo create_drop_down( "cbo_brand_id", $width, "select id, brand_name from lib_buyer_brand brand where buyer_id='$data_arr[0]' and status_active =1 and is_deleted=0 $brand_cond order by brand_name ASC","id,brand_name", 1, "--Brand--", $selected, "" );
	exit();
}

if($action=="create_requisition_id_search_list_view")
{
	$data=explode('_',$data);
	
	
	if ($data[2]!=0) $company=" and company_id='$data[2]'"; else 
	{ echo "<b style='color:crimson;'> Please Select Company First.</b>"; die; }
	
	
	
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
	if ($data[4]!="" &&  $data[5]!="") $estimated_shipdate  = "and REQUISITION_DATE  between '".change_date_format($data[4], "yyyy-mm-dd", "-")."' and '".change_date_format($data[5], "yyyy-mm-dd", "-")."'"; else $estimated_shipdate ="";
	}
	if($db_type==2)
	{
	if ($data[4]!="" &&  $data[5]!="") $estimated_shipdate  = "and REQUISITION_DATE  between '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[5], "yyyy-mm-dd", "-",1)."'"; else $estimated_shipdate ="";
	}
	if ($data[7]!="") $requisition_num=" and requisition_number_prefix_num like '%$data[7]' "; else $requisition_num="";

	if ($data[8]!=0) $stage_id=" and sample_stage_id= '$data[8]' "; else  $stage_id="";
	if ($data[9]!=0) $brand_id=" and brand_id= '$data[9]' "; else  $brand_id="";
	if ($data[10]!=0) $season_year=" and season_year= '$data[10]' "; else  $season_year="";
	if ($data[11]!=0) $season=" and season= '$data[11]' "; else  $season="";
	//if (!$data[8] && trim($data[7])=="") {echo "<b style='color:crimson;'> Please Select Sample Stage</b>";die;}


	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');

	$dealing_marchant=return_library_array( "select id, team_member_name from lib_mkt_team_member_info",'id','team_member_name');
	$team_leader=return_library_array( "select id, team_leader_name from lib_marketing_team",'id','team_leader_name');
	$brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand",'id','brand_name');
	$season_arr=return_library_array( "select id,season_name from lib_buyer_season  where status_active =1 and is_deleted=0 ", "id", "season_name"  );

	$arr=array (2=>$buyer_arr,3=>$brand_arr,5=>$season_arr,7=>$product_dept,8=>$dealing_marchant,9=>$sample_stage);
	$sql="";
if($db_type==0)
	{
		$sql= "select id,requisition_number_prefix_num,SUBSTRING_INDEX(insert_date, '-', 1) as year,company_id,buyer_name,style_ref_no,product_dept,dealing_marchant ,sample_stage_id, season, season_year, brand_id from sample_development_mst where entry_form_id=459 and status_active=1 and is_deleted=0 $company $buyer $style_id_cond $style_cond  $estimated_shipdate $requisition_num   $stage_id $brand_id $season_year $season order by id DESC";

	}
	else if($db_type==2)
	{
	$sql= "select id,requisition_number_prefix_num,to_char(insert_date,'YYYY') as year,company_id,buyer_name,style_ref_no,product_dept,dealing_marchant,sample_stage_id, season, season_year, brand_id from sample_development_mst where entry_form_id=459 and  status_active=1 and is_deleted=0 $company $buyer $style_id_cond $style_cond $estimated_shipdate $requisition_num  $stage_id $brand_id $season_year $season order by id DESC";
	}

	echo  create_list_view("list_view", "Year,Requisition No,Buyer Name,Brand,Season Year,Season,Style Name,Product Department,Dealing Merchant,Sample Stage", "60,100,120,90,90,90,100,90,90,100","950","240",0, $sql , "js_set_value", "id", "", 1, "0,0,buyer_name,brand_id,0,season,0,product_dept,dealing_marchant,sample_stage_id", $arr , "year,requisition_number_prefix_num,buyer_name,brand_id,season_year,season,style_ref_no,product_dept,dealing_marchant,sample_stage_id", "",'','0,0,0,0,0,0,0,0,0,0') ;

	exit();
}


if($action=="populate_data_from_requisition_search_popup")
{
	$res = sql_select("SELECT id, company_id, team_leader, location_id, buyer_name, style_ref_no, product_dept, agent_name, dealing_marchant, season, buyer_ref, estimated_shipdate, remarks, requisition_number, sample_stage_id, requisition_date, material_delivery_date, quotation_id, is_approved, is_acknowledge, req_ready_to_approved, season_year, brand_id from sample_development_mst where id=$data and entry_form_id=459 and is_deleted=0 and status_active=1");
	$sample_st=$res[0][csf("sample_stage_id")];
	$quotation_info=$res[0][csf("quotation_id")];
	if($sample_st==1)
	{
		$job_arr=array();
		$job_sql="select id,company_name, buyer_name, style_ref_no, product_dept, location_name, agent_name, dealing_marchant, season_matrix, season_buyer_wise,gmts_item_id,garments_nature from wo_po_details_master where is_deleted=0 and status_active=1";
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
			//$job_arr[$jrow[csf("id")]]['bh']=$jrow[csf("bh_merchant")];
			$job_arr[$jrow[csf("id")]]['gmts']=$jrow[csf("gmts_item_id")];
			$job_arr[$jrow[csf("id")]]['gmtsnature']=$jrow[csf("garments_nature")];
			$job_arr[$jrow[csf("id")]]['season']=$season_id;
		}
	 	unset($job_sql_res);

	}

	if($sample_st==2 && $quotation_info)
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
			//$job_arr[$jrow[csf("id")]]['cbo_sample_team']=$team_leader_id;
		}
		unset($inq_sql_res);
	}


	  $is_booking = sql_select("SELECT style_id from wo_non_ord_samp_booking_dtls where style_id=$data and status_active=1 and is_deleted=0 and entry_form_id=140 group by style_id union SELECT style_id from wo_booking_dtls where style_id=$data and status_active=1 and is_deleted=0 and entry_form_id=139 group by style_id");
	 //clearstatcache();
 	foreach($res as $result)
	{
		echo "load_drop_down( 'requires/sweater_sample_requisition_v2_controller', '".$result[csf("company_id")]."', 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/sweater_sample_requisition_v2_controller', '".$result[csf("company_id")]."', 'load_drop_down_location', 'location_td' );load_drop_down( 'requires/sweater_sample_requisition_v2_controller', '".$result[csf("company_id")]."', 'load_drop_down_agent', 'agent_td' );load_drop_down( 'requires/sweater_sample_requisition_v2_controller','".$result[csf("buyer_name")]."', 'load_drop_down_season_buyer', 'season_td');load_drop_down( 'requires/sweater_sample_requisition_v2_controller','".$result[csf("buyer_name")]."', 'load_drop_down_brand', 'brand_td');load_drop_down( 'requires/sweater_sample_requisition_v2_controller','".$result[csf("buyer_name")]."', 'load_drop_down_sample_for_buyer', 'sample_td');load_drop_down( 'requires/sweater_sample_requisition_v2_controller', '".$result[csf("id")]._1."', 'load_drop_down_required_fabric_sample_name', 'rfSampleId_1');load_drop_down( 'requires/sweater_sample_requisition_v2_controller','".$result[csf("id")]._1."', 'load_drop_down_required_fabric_gmts_item', 'rfItemId_1');load_drop_down( 'requires/sweater_sample_requisition_v2_controller', '".$result[csf("id")]._2."', 'load_drop_down_required_fabric_sample_name','raSampleId_1');load_drop_down( 'requires/sweater_sample_requisition_v2_controller', '".$result[csf("id")]._2."', 'load_drop_down_required_fabric_gmts_item', 'raItemId_1');load_drop_down( 'requires/sweater_sample_requisition_v2_controller', '".$result[csf("id")]._3."', 'load_drop_down_required_fabric_sample_name','reSampleId_1'); load_drop_down( 'requires/sweater_sample_requisition_v2_controller','".$result[csf("id")]._3."', 'load_drop_down_required_fabric_gmts_item', 'reItemIid_1');\n";

 		echo "$('#txt_requisition_id').val('".$result[csf('requisition_number')]."');\n";
		echo "$('#cbo_sample_stage').val('".$result[csf('sample_stage_id')]."');\n";
		echo "$('#txt_requisition_date').val('".change_date_format($result[csf('requisition_date')],'dd-mm-yyyy','-')."');\n";
		echo "$('#txt_requisition_date').attr('disabled','true')".";\n";
		echo "$('#txt_material_dlvry_date').val('".change_date_format($result[csf('material_delivery_date')],'dd-mm-yyyy','-')."');\n";
		echo "$('#update_id').val('".$result[csf('id')]."');\n";
		echo "$('#cbo_ready_to_approved').val('".$result[csf('req_ready_to_approved')]."');\n";

		echo "$('#cbo_sample_team').val('".$result[csf('team_leader')]."');\n";
		
		
		if($result[csf('sample_stage_id')]==1)
		{
			echo "$('#txt_quotation_id').val('".$result[csf('quotation_id')]."');\n";
			echo "$('#cbo_company_name').val('".$job_arr[$result[csf("quotation_id")]]['company']."');\n";
			echo "$('#cbo_location_name').val('".$job_arr[$result[csf("quotation_id")]]['loaction']."');\n";
			echo "$('#cbo_buyer_name').val('".$job_arr[$result[csf("quotation_id")]]['buyer']."');\n";
			echo "document.getElementById('cbo_season_year').value = '".$result[csf("season_year")]."';\n";
			echo "$('#cbo_product_department').val('".$job_arr[$result[csf("quotation_id")]]['dept']."');\n";
			echo "$('#cbo_agent').val('".$job_arr[$result[csf("quotation_id")]]['agent']."');\n";
			echo "$('#cbo_dealing_merchant').val('".$job_arr[$result[csf("quotation_id")]]['dmarchant']."');\n";
			//echo "$('#txt_product_code').val('".$result[csf('product_code')]."');\n";
			echo "$('#cbo_brand_id').val('".$result[csf("brand_id")]."');\n";
			echo "$('#cbo_season_name').val('".$job_arr[$result[csf("quotation_id")]]['season']."');\n";
			echo "fnc_browse_style('".$result[csf('sample_stage_id')]."');\n";
			echo "$('#txt_style_name').val('".$job_arr[$result[csf("quotation_id")]]['style']."');\n";
			echo "load_drop_down( 'requires/sweater_sample_requisition_v2_controller', '".$job_arr[$result[csf("quotation_id")]]['gmts']."', 'load_drop_down_garment_item_for_after_order', 'item_id_1');\n";
		}

		else if($result[csf('sample_stage_id')]==2 && ($result[csf('quotation_id')]))
		{
			$quotation= $result[csf('quotation_id')];
			$bom_no=return_field_value("bom_no","wo_quotation_inquery","id=$quotation");
			echo "$('#txt_quotation_id').val('".$result[csf('quotation_id')]."');\n";
			echo "$('#txt_bom').val('".$bom_no."');\n";
			echo "$('#cbo_company_name').val('".$inq_arr[$result[csf("quotation_id")]]['company']."');\n";
			echo "$('#cbo_location_name').val('".$result[csf('location_id')]."');\n";
			echo "$('#cbo_buyer_name').val('".$inq_arr[$result[csf("quotation_id")]]['buyer']."');\n";
			echo "$('#cbo_product_department').val('".$result[csf('product_dept')]."');\n";
			echo "$('#cbo_dealing_merchant').val('".$inq_arr[$result[csf("quotation_id")]]['dmarchant']."');\n";
			echo "$('#cbo_brand_id').val('".$result[csf("brand_id")]."');\n";
			echo "$('#cbo_season_name').val('".$inq_arr[$result[csf("quotation_id")]]['season']."');\n";
			echo "document.getElementById('cbo_season_year').value = '".$result[csf("season_year")]."';\n";
			echo "$('#txt_est_ship_date').val('".$inq_arr[$result[csf("quotation_id")]]['est']."');\n";
			echo "$('#cbo_season_name').val('".$result[csf('season')]."');\n";
			echo "$('#txt_remarks').val('".$inq_arr[$result[csf("quotation_id")]]['remarks']."');\n";
			echo "fnc_browse_style('".$result[csf('sample_stage_id')]."');\n";
			echo "$('#txt_style_name').val('".$inq_arr[$result[csf("quotation_id")]]['style']."');\n";
			echo "load_drop_down( 'requires/sweater_sample_requisition_v2_controller', '".$inq_arr[$result[csf("quotation_id")]]['gmts']."', 'load_drop_down_garment_item_for_after_order', 'item_id_1');\n";

		}
 		else
		{
			echo "$('#cbo_company_name').val('".$result[csf('company_id')]."');\n";
			echo "$('#cbo_location_name').val('".$result[csf('location_id')]."');\n";
			echo "$('#cbo_buyer_name').val('".$result[csf('buyer_name')]."');\n";
			echo "$('#txt_style_name').val('".$result[csf('style_ref_no')]."');\n";
			echo "$('#cbo_product_department').val('".$result[csf('product_dept')]."');\n";
			echo "$('#cbo_dealing_merchant').val('".$result[csf('dealing_marchant')]."');\n";
		}
		echo "$('#txt_est_ship_date').val('".change_date_format($result[csf('estimated_shipdate')],'dd-mm-yyyy','-')."');\n";
		echo "$('#txt_remarks').val('".$result[csf('remarks')]."');\n";
		echo "$('#txt_buyer_ref').val('".$result[csf('buyer_ref')]."');\n";
		//echo "$('#txt_bhmerchant').val('".$result[csf('bh_merchant')]."');\n";
		echo "$('#cbo_agent').val('".$result[csf('agent_name')]."');\n";
		echo "$('#cbo_season_name').val('".$result[csf('season')]."');\n";
		echo "document.getElementById('cbo_season_year').value = '".$result[csf("season_year")]."';\n";
		echo "$('#cbo_brand_id').val('".$result[csf("brand_id")]."');\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_sweater_sample_requisition_mst_info',1);\n";
		echo "$('#approvedMsg').html('');\n";
 		//echo "$('#sample_dtls').removeProp('disabled')".";\n";
		echo "$('#required_fab_dtls').removeProp('disabled')".";\n";
		echo "$('#required_accessories_dtls').removeProp('disabled')".";\n";
		echo "$('#required_embellishment_dtls').removeProp('disabled')".";\n";
		echo "$('#sample_dtls').removeProp('disabled')".";\n";
		if($result[csf('is_approved')]==3){
			$is_approved=1;
		}else{
			$is_approved=$result[csf('is_approved')];
		}
		$is_acknowledge=$result[csf('is_acknowledge')];
 		if($is_approved==1 || count($is_booking)>0 || $is_acknowledge==1 )
		{	if($is_approved==1)
			{

				 echo "$('#approvedMsg').html('This Requisition is Approved by Authority..!!');\n";
			}
			if($is_acknowledge==1)
			{
				echo "$('#approvedMsg').html('This Requisition is Acknowledge by Authority..!!');\n";
			}
			if(count($is_booking)>0)
			{
				echo "$('#approvedMsg').html('Booking found aganist this Requisition!!');\n";
			}
  			echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_sweater_sample_requisition_mst_info',1,1);\n";
 			echo "$('#save1').removeClass('formbutton').addClass('formbutton_disabled');\n";
 			echo "$('#save1').removeAttr('onclick','fnc_sweater_sample_requisition_mst_info(0)');\n";
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
			//echo "$('#txt_bhmerchant').attr('disabled','true')".";\n";
			echo "$('#txt_est_ship_date').attr('disabled','true')".";\n";
			echo "$('#txt_remarks').attr('disabled','true')".";\n";
			echo "$('#cbo_ready_to_approved').attr('disabled','true')".";\n";
 			echo "$('#required_fab_dtls').prop('disabled','true')".";\n";
			echo "$('#sample_dtls').prop('disabled','true')".";\n";
			echo "$('#required_accessories_dtls').prop('disabled','true')".";\n";
			echo "$('#required_embellishment_dtls').prop('disabled','true')".";\n";
  		}

		if($is_approved!=1 || $is_acknowledge!=1)
		{
 			echo "$('#cbo_sample_stage').removeAttr('disabled','')".";\n";
			//echo "$('#txt_requisition_date').removeAttr('disabled','')".";\n";
			echo "$('#txt_style_name').removeAttr('disabled','')".";\n";
 			echo "$('#cbo_season_name').removeAttr('disabled','')".";\n";
 			echo "$('#cbo_dealing_merchant').attr('disabled','true')".";\n";
 			echo "$('#txt_buyer_ref').removeAttr('disabled','')".";\n";
			//echo "$('#txt_bhmerchant').removeAttr('disabled','')".";\n";
			echo "$('#txt_est_ship_date').removeAttr('disabled','')".";\n";
			echo "$('#txt_remarks').removeAttr('disabled','')".";\n";
			echo "$('#cbo_ready_to_approved').removeAttr('disabled','')".";\n";
		}
  	}
   unset($res);
 	exit();
}

if($action=="color_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Sample Color Info","../../../../", 1, 1, $unicode);
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

		echo  create_list_view("list_view", "Color Name", "150","220","240",0, $sql , "js_set_value", "color_name", "", 1, "0,0", $arr , "color_name","requires/sweater_sample_requisition_v2_controller", 'setFilterGrid("list_view",-1);' );
	}
	else
	{
		$sql= "select id, color_name from lib_color where color_name <> '' or color_name is not null";

		echo  create_list_view("list_view", "Color Name", "150","220","240",0, $sql , "js_set_value", "color_name", "", 1, "0", $arr , "color_name","requires/sweater_sample_requisition_v2_controller", 'setFilterGrid("list_view",-1);' );
	}
	exit();
}




if($action=="load_php_dtls_form")
{
	$ex_data = explode("**",$data);
	$up_id=$ex_data[0];
	$type=$ex_data[1];
	$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$buyer_aganist_req=return_library_array( "select id,buyer_name from sample_development_mst where is_deleted=0 and status_active=1 order by buyer_name", "id", "buyer_name"  );
	
	$is_copy=return_field_value("IS_COPY","sample_development_mst","entry_form_id=459 and id='$up_id' and status_active=1 and is_deleted=0");
	$is_disable=($is_copy)?0:1;
	if($type==1)
	{
		$sql_sam="SELECT id, sample_name, gmts_item_id, smv,article_no, sample_color, sample_prod_qty, submission_qty, delv_start_date, delv_end_date, sample_charge, sample_curency, size_data,fabric_status,acc_status,embellishment_status,color_combo_no from sample_development_dtls where entry_form_id=459 and sample_mst_id='$up_id' and  is_deleted=0  and status_active=1 order by id ASC"; //echo $sql_sam;
		$value=return_field_value("quotation_id","sample_development_mst","entry_form_id=459 and id='$up_id' and status_active=1 and is_deleted=0");
		
		
		
		$sql_result =sql_select($sql_sam);  $i=1;
		if(count($sql_result)>0)
		{
			foreach($sql_result as $row)
			{
				?>
				<tr id="tr_<? echo $i; ?>" style="height:10px;" class="general">
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
							echo create_drop_down( "cboSampleName_$i", 100, $sql,"id,sample_name", 1, "select Sample", $row[csf("sample_name")], "",$is_disable);
						}
						?>
					</td>
					<td>

						<?
						if($row[csf("fabric_status")]==1 || $row[csf("acc_status")]==1 || $row[csf("embellishment_status")]==1)
						{
							if($value=="" || $value==0)
							{
								echo create_drop_down( "cboGarmentItem_$i", 100, get_garments_item_array(100),"", 1, "Select Item",$row[csf("gmts_item_id")], "",1,"");
							}
							else
							{
								echo create_drop_down( "cboGarmentItem_$i", 100, get_garments_item_array(100),"", 1, "Select Item",$row[csf("gmts_item_id")], "",1,$row[csf("gmts_item_id")]);
							}
						}
						else
						{
							if($value=="" || $value==0)
							{
								echo create_drop_down( "cboGarmentItem_$i", 100, get_garments_item_array(100),"", 1, "Select Item",$row[csf("gmts_item_id")], "",$is_disable,"");
							}
							else
							{
								echo create_drop_down( "cboGarmentItem_$i", 100, get_garments_item_array(100),"", 1, "Select Item",$row[csf("gmts_item_id")], "",1,$row[csf("gmts_item_id")]);
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
					<td><input style="width:80px;" type="text" class="text_boxes"  name="txtColor_<? echo $i; ?>" id="txtColor_<? echo $i; ?>" placeholder="write/browse" onDblClick="openmypage_color_size('requires/sweater_sample_requisition_v2_controller.php?action=color_popup','Color Search','1','<? echo $i; ?>');" value="<? echo $color_arr[$row[csf("sample_color")]]; ?>"/></td>

					</td>
					<td>
						<input style="width:60px;" type="text" class="text_boxes"  name="txtcolorcombono_<? echo $i; ?>" id="txtcolorcombono_<? echo $i; ?>" placeholder="write" value="<? echo $row[csf("color_combo_no")]; ?>" />
					</td>
					<td>
						<?
						if($row[csf("fabric_status")]==1 || $row[csf("acc_status")]==1 || $row[csf("embellishment_status")]==1)
						{
							?>
							<input style="width:100px;" type="text" class="text_boxes_numeric"  name="txtSampleProdQty_<? echo $i; ?>" readonly id="txtSampleProdQty_<? echo $i; ?>" placeholder="browse"  ondblclick="openmypage_sizeinfo('requires/sweater_sample_requisition_v2_controller.php?action=sizeinfo_popup','Size Search','<? echo $i;?>')" value="<? echo $row[csf("sample_prod_qty")]; ?>"   />

							 <!--onFocus="openmypage_sizeinfo('requires/sweater_sample_requisition_v2_controller.php?action=sizeinfo_popup_mouseover','Size Search','< ? echo $i;?>')"-->
							
							<?
						}
						else {
							?>
							<input style="width:100px;" type="text" class="text_boxes_numeric"  name="txtSampleProdQty_<? echo $i; ?>" readonly id="txtSampleProdQty_<? echo $i; ?>" placeholder="browse"   ondblclick="openmypage_sizeinfo('requires/sweater_sample_requisition_v2_controller.php?action=sizeinfo_popup','Size Search','<? echo $i;?>')"  value="<? echo $row[csf("sample_prod_qty")]; ?>"/>
							<?
						}
						?>

					</td>

					<input type="hidden" class="text_boxes"  name="txtAllData_<? echo $i;?>" id="txtAllData_<? echo $i;?>" value="<? echo $row[csf("size_data")]; ?>"/>

					<td><input style="width:100px;" type="text" class="text_boxes_numeric"  name="txtSubmissionQty_<? echo $i; ?>" readonly id="txtSubmissionQty_<? echo $i; ?>" placeholder=""  value="<? echo $row[csf("submission_qty")]; ?>" /></td>
					<td><input style="width:85px;" class="datepicker" name="txtDelvStartDate_<? echo $i; ?>" id="txtDelvStartDate_<? echo $i; ?>" value="<? echo change_date_format($row[csf("delv_start_date")]); ?>" onChange="fn_calculate_delivery_date(<? echo $i; ?>)"  <? echo $disabled; $disabled='disabled';?> /></td>
					<td><input style="width:85px;" class="datepicker" name="txtDelvEndDate_<? echo $i; ?>" id="txtDelvEndDate_<? echo $i; ?>" value="<? echo change_date_format($row[csf("delv_end_date")]); ?>" readonly disabled /></td>
					<td><input style="width:70px;" type="text" class="text_boxes_numeric"  name="txtChargeUnit_<? echo $i; ?>" id="txtChargeUnit_<? echo $i; ?>" placeholder="write" value="<? echo $row[csf("sample_charge")]; ?>"/></td>
					<td><? echo create_drop_down( "cboCurrency_$i", 70, $currency, "","","",$row[csf("sample_curency")], "", "", "" ); ?></td>
					<td><input type="button" class="image_uploader" name="txtFile_<? echo $i; ?>" id="txtFile_<? echo $i; ?>" size="10" value="ADD IMAGE" onClick="file_uploader ( '../../../', document.getElementById('updateidsampledtl_<? echo $i;?>').value,'', 'sample_details_1', 0 ,1)"></td>
					<td>
						<?
						if($row[csf("fabric_status")] ==1 || $row[csf("acc_status")]==1 || $row[csf("embellishment_status")]==1)
						{
							?>
							<input type="button" id="increase_<? echo $i; ?>" name="increase_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="+"  onClick="add_break_down_tr(<? echo $i; ?>)" />
							<input type="button" id="decrease_<? echo $i; ?>" name="decrease_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="-" disabled onClick="" />
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
		
		$sql_fabric="SELECT id,sample_mst_id,sample_name,gmts_item_id,body_part_id,fabric_nature_id,fabric_description,gsm,dia,sample_color,color_type_id,width_dia_id,uom_id,remarks_ra,required_dzn,required_qty,color_data, determination_id,process_loss_percent,grey_fab_qnty,gauge,development_no,buyer_prov,no_of_ends from sample_development_fabric_acc where sample_mst_id='$up_id' and form_type=1 and  is_deleted=0  and status_active=1 order by id ASC";
		 //echo $sql_fabric;die;
		$fabric_composition_arr=sql_select("SELECT a.id, a.fab_composition from lib_yarn_count_determina_mst a join wo_quotation_inquery b on to_char(a.id)=b.fabrication join sample_development_mst c on b.id=c.quotation_id  where a.is_deleted=0 and a.fab_nature_id=100 and c.id=$up_id order by a.id ASC");
		foreach ($fabric_composition_arr as $row) {
			$fabric_compo=$row[csf("fab_composition")];
			$fabric_compo_id=$row[csf("id")];
		}
		$sql_resultf =sql_select($sql_fabric);
		$i=1;
		if(count($sql_resultf)>0)
		{
			
			$sql="SELECT a.id,a.sample_name,b.id as dtls_id from  lib_sample a ,sample_development_dtls b where  a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.id=b.sample_name and b.entry_form_id=459 and b.sample_mst_id='$up_id' group by a.id,a.sample_name,b.id order by b.id";
			$samp_array=array();
			$samp_result=sql_select($sql);
			if(count($samp_result)>0)
			{
				foreach($samp_result as $keys=>$vals)
				{
					$samp_array[$vals[csf("id")]]=$vals[csf("sample_name")];
				}

			}
			
			
			foreach($sql_resultf as $row)
			{
				?>
				<tr id="tr_<? echo $i; ?>" style="height:10px;" class="general">
					<td align="center" id="rfSampleId_1">
						<?
						echo create_drop_down( "cboRfSampleName_$i", 95, $samp_array,"", '', "", $row[csf("sample_name")],"");
						?>

					</td>
                    <td align="center" id="rfDevelopmentNo_1">
						<input style="width:70px;" type="text" placeholder="Write" class="text_boxes"  name="txtRfDevelopmentNo_<? echo $i; ?>" id="txtRfDevelopmentNo_<? echo $i; ?>" value="<? echo $row[csf("development_no")];?>"/>
					</td>
					<td align="center" id="rfItemId_1">
						<?
						$sql_f=sql_select("select id,gmts_item_id from sample_development_dtls where is_deleted=0  and status_active=1 and entry_form_id=459 and sample_mst_id='$up_id'");
						$gmtsf="";
						foreach ($sql_f as $rowf)
						{
							$gmtsf.=$rowf[csf("gmts_item_id")].",";
						}
						echo create_drop_down( "cboRfGarmentItem_$i", 95, $garments_item,"", 1, "Select Item",$row[csf("gmts_item_id")],"","",$gmtsf);

						?>

					</td>
                    
                    <td align="center">
						<input style="width:70px;" type="text" placeholder="Write" class="text_boxes"  name="txtBuyerProv_<?= $i;?>" id="txtBuyerProv_<?= $i;?>" value="<? echo $row[csf("buyer_prov")]; ?>"/>
					</td>
                    
                    
                    <td align="center" id="rfGmtsColorId_1">
                        <?
                        echo create_drop_down( "cboRfGmtsColorId_$i", 70, $color_arr,"", 0, "Select Color", $row[csf("sample_color")], "","",$row[csf("sample_color")]);
                        ?>
                    </td>
					<td align="center" id="rf_body_part_1">
						<input type="hidden" id="cboRfBodyPart_<? echo $i; ?>" name="cboRfBodyPart_<? echo $i; ?>" class="text_boxes" style="width:70px"  value="<? echo $row[csf("body_part_id")];?>"  readonly/>
						<input type="text" id="cboRfBodyPartname_<? echo $i; ?>" name="cboRfBodyPartname_<? echo $i; ?>" class="text_boxes" style="width:70px" onDblClick="open_body_part_popup(<? echo $i; ?>)" value="<? echo $body_part[$row[csf("body_part_id")]];?>" onBlur="load_data_to_rfcolor(<? echo $i; ?>)"   placeholder="DblClick" readonly/>
					</td>
					<td align="center" id="rf_fabric_nature_1">
						<?
						echo create_drop_down( "cboRfFabricNature_$i", 95, $item_category,"", 0, "Select Fabric Nature",$row[csf("fabric_nature_id")] , "","1","100");

						?>

					</td>
					<td align="center" id="rf_fabric_description_1">
						<input style="width:60px;" type="text" class="text_boxes"  name="txtRfFabricDescription_<? echo $i; ?>" id="txtRfFabricDescription_<? echo $i; ?>" placeholder="browse" onDblClick="open_fabric_description_popup(<? echo $i; ?>)" value="<? echo $row[csf("fabric_description")]; ?>" readonly/>
						<input type="hidden" name="libyarncountdeterminationid_<? echo $i; ?>" id="libyarncountdeterminationid_<? echo $i; ?>" class="text_boxes" style="width:10px" value="<? echo $row[csf("determination_id")]; ?>">
					</td>

                    <td align="center" id="rf_gauge_1">
                        <?php echo create_drop_down( "txtRfGauge_".$i, 50, $gauge_arr,"", 1, "Select", $row[csf("gauge")],""); ?>
                    </td>
                    <td align="center">
                        <input style="width:70px;" type="text" placeholder="Write" class="text_boxes_numeric"  name="txtNoOfEnds_<?= $i; ?>" id="txtNoOfEnds_<?= $i; ?>" value="<? echo $row[csf("no_of_ends")]; ?>"/>
                    </td>

					<td align="center" id="rf_color_1">
                        <input style="width:60px;" type="text" class="text_boxes"  name="txtRfColor_<? echo $i; ?>" id="txtRfColor_<? echo $i; ?>" placeholder="browse" onDblClick="openmypage_rf_color('requires/sweater_sample_requisition_v2_controller.php?action=color_popup_rf','Color Search','<? echo $i;?>');"

						readonly=""  value="<?
						$a=$row[csf("color_data")];
						$colors="";
						$c=explode("-----",$a);
						foreach($c as $v)
						{
							$cc=explode("__",$v);
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
                    

					<td align="center" id="rf_color_type_1">
						<?
						echo create_drop_down( "cboRfColorType_$i", 95, $color_type,"", 1, "Select Color Type", $row[csf("color_type_id")], "");
						?>
                        
                        
                        <input type="hidden" id="updateidRequiredDtl_<? echo $i; ?>" name="updateidRequiredDtl_<? echo $i; ?>"  class="text_boxes" style="width:20px" value="<? echo $row[csf("id")]; ?>"  />
                        <input type="hidden" id="txtDeltedIdRf" name="txtDeltedIdRf"  class="text_boxes" style="width:20px" value="" />
                        <input type="hidden" name="txtRfColorAllData_<? echo $i; ?>" id="txtRfColorAllData_<? echo $i; ?>" value="<? echo $row[csf("color_data")]; ?>"  class="text_boxes">
                                            
					</td>
					<td align="center" id="rf_uom_1">
						<?
						echo create_drop_down( "cboRfUom_$i", 56, $unit_of_measurement,'', '',"",$row[csf("uom_id")],"",1,"12,15,27,1,23" );
						?>
					</td>

					<td align="center" id="rf_req_qty_1">
						<input style="width:50px;" type="text" class="text_boxes_numeric"  name="txtRfReqQty_<? echo $i; ?>" id="txtRfReqQty_<? echo $i; ?>" placeholder="" value="<? echo $row[csf("required_qty")]; ?>" readonly/>
					</td>

					 <td align="center" id="rf_req_dzn_1" style="display:none;">
                         <input style="width:50px;" type="text" class="text_boxes" value="<? echo $row[csf("remarks_ra")]; ?>"  name="txtRfRemarks_<? echo $i;?>" id="txtRfRemarks_<? echo $i;?>" onClick="required_fab_remarks(<? echo $i; ?>);"  />
                     </td>



					<td id="rf_image_1"><input type="button" class="image_uploader" name="txtRfFile_<? echo $i; ?>" id="txtRfFile_<? echo $i; ?>" onClick="file_uploader ( '../../../', document.getElementById('updateidRequiredDtl_<? echo $i;?>').value,'', 'required_fabric_1', 0 ,1)" value="ADD IMAGE"></td>
					<td width="70">
						<input type="button" id="increaserf_<? echo $i; ?>" name="increaserf_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_rf_tr(<? echo $i; ?>)" />
						<input type="button" id="decreaserf_<? echo $i; ?>" name="decreaserf_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_rf_deleteRow(<? echo $i; ?>);" />
					</td>
				</tr>

				<?
				$i++;
			}
		}
		else
		{ 
			$sql_sam="SELECT a.id,a.sample_name,b.id as dtls_id from  lib_sample a ,sample_development_dtls b where  a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.id=b.sample_name and b.entry_form_id=459 and b.sample_mst_id='$up_id' group by a.id,a.sample_name,b.id order by b.id";
			$samp_result =sql_select($sql_sam);
			
			$samp_array=array();
			if(count($samp_result)>0)
			{
				foreach($samp_result as $vals)
				{
					$samp_array[$vals[csf("id")]]=$vals[csf("sample_name")];
				}

			}
			
			
			$samp_sql="SELECT id, sample_name, gmts_item_id, smv,article_no, sample_color, sample_prod_qty, submission_qty, delv_start_date, delv_end_date, sample_charge, sample_curency, size_data,fabric_status,acc_status,embellishment_status from sample_development_dtls where entry_form_id=459 and sample_mst_id='$up_id' and  is_deleted=0  and status_active=1 order by id ASC";
			$samp_sql_result =sql_select($samp_sql);
			$i=1;
			foreach($samp_sql_result as $row)
			{
				?>

				<tr id="tr_<? echo $i; ?>" style="height:10px;" class="general">
					<td align="center" id="rfSampleId_<? echo $i; ?>">
						
						<?
						echo create_drop_down( "cboRfSampleName_$i", 95, $samp_array,"", '', "", $row[csf("sample_name")],"");
						?>

					</td>
                    <td align="center" id="rfDevelopmentNo_<? echo $i; ?>">
						<input style="width:70px;" type="text" placeholder="Write" class="text_boxes"  name="txtRfDevelopmentNo_<? echo $i; ?>" id="txtRfDevelopmentNo_<? echo $i; ?>" value="<? echo $row[csf("development_no")];?>"/>
					</td>
					<td align="center" id="rfItemId_<? echo $i; ?>">
						<?
						$sql_f=sql_select("select id,gmts_item_id from sample_development_dtls where is_deleted=0  and status_active=1 and entry_form_id=459 and sample_mst_id='$up_id'");
						$gmtsf="";
						foreach ($sql_f as $rowf)
						{
							$gmtsf.=$rowf[csf("gmts_item_id")].",";
						}
						echo create_drop_down( "cboRfGarmentItem_$i", 95, $garments_item,"", 1, "Select Item",$row[csf("gmts_item_id")],"","",$gmtsf);

						?>

					</td>
                    <td align="center">
						<input style="width:70px;" type="text" placeholder="Write" class="text_boxes"  name="txtBuyerProv_<?= $i;?>" id="txtBuyerProv_<?= $i;?>" value="<? echo $row[csf("buyer_prov")]; ?>"/>
					</td>
                    
                    
                    <td align="center" id="rfGmtsColorId_<? echo $i; ?>">
                        <?
                        echo create_drop_down( "cboRfGmtsColorId_$i", 70, $color_arr,"", 0, "Select Color", $row[csf("sample_color")], "","",$row[csf("sample_color")]);
                        ?>
                    </td>
					<td align="center" id="rf_body_part_<? echo $i; ?>">
						<input type="hidden" id="cboRfBodyPart_<? echo $i; ?>" name="cboRfBodyPart_<? echo $i; ?>" class="text_boxes" style="width:70px"  value="<? echo $row[csf("body_part_id")];?>"  readonly/>
						<input type="text" id="cboRfBodyPartname_<? echo $i; ?>" name="cboRfBodyPartname_<? echo $i; ?>" class="text_boxes" style="width:70px" onDblClick="open_body_part_popup(<? echo $i; ?>)" value="<? echo $body_part[$row[csf("body_part_id")]];?>" onBlur="load_data_to_rfcolor(<? echo $i; ?>)"   placeholder="DblClick" readonly/>
					</td>
					<td align="center" id="rf_fabric_nature_<? echo $i; ?>">
						<?
						echo create_drop_down( "cboRfFabricNature_$i", 95, $item_category,"", 0, "Select Fabric Nature",$row[csf("fabric_nature_id")] , "","","100");

						?>

					</td>
					<td align="center" id="rf_fabric_description_<? echo $i; ?>">
						<input style="width:60px;" type="text" class="text_boxes"  name="txtRfFabricDescription_<? echo $i; ?>" id="txtRfFabricDescription_<? echo $i; ?>" placeholder="browse" onDblClick="open_fabric_description_popup(<? echo $i; ?>)" value="<? echo $fabric_compo; //$row[csf("fabric_description")]; ?>" readonly/>
						<input type="hidden" name="libyarncountdeterminationid_<? echo $i; ?>" id="libyarncountdeterminationid_<? echo $i; ?>" class="text_boxes" style="width:10px" value="<? echo $fabric_compo_id; ?>">
					</td>

                    <td align="center" id="rf_gauge_<? echo $i; ?>">
                        <?php echo create_drop_down( "txtRfGauge_$i", 50, $gauge_arr,"", 1, "Select", "",""); ?>
                    </td> 
                    
                    <td align="center">
                        <input style="width:70px;" type="text" placeholder="Write" class="text_boxes_numeric"  name="txtNoOfEnds_<?= $i; ?>" id="txtNoOfEnds_<?= $i; ?>" value="<? echo $row[csf("no_of_ends")]; ?>"/>
                    </td>
                                       

					<td align="center" id="rf_color_<? echo $i; ?>">
                        <input style="width:60px;" type="text" class="text_boxes"  name="txtRfColor_<? echo $i; ?>" id="txtRfColor_<? echo $i; ?>" placeholder="browse" onDblClick="openmypage_rf_color('requires/sweater_sample_requisition_v2_controller.php?action=color_popup_rf','Color Search','<? echo $i;?>');"

						readonly=""  value="<?
						$a=$row[csf("color_data")];
						$colors="";
						$c=explode("-----",$a);
						foreach($c as $v)
						{
							$cc=explode("__",$v);
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
                    

					<td align="center" id="rf_color_type_<? echo $i; ?>">
						<?
						echo create_drop_down( "cboRfColorType_$i", 95, $color_type,"", 1, "Select Color Type", 2, "");
						?>
                        
                        
                        <input type="hidden" id="updateidRequiredDtl_<? echo $i; ?>" name="updateidRequiredDtl_<? echo $i; ?>"  class="text_boxes" style="width:20px" value="<? echo $row[csf("id")]; ?>"  />
                        <input type="hidden" id="txtDeltedIdRf" name="txtDeltedIdRf"  class="text_boxes" style="width:20px" value="" />
                        <input type="hidden" name="txtRfColorAllData_<? echo $i; ?>" id="txtRfColorAllData_<? echo $i; ?>" value="<? echo $row[csf("color_data")]; ?>"  class="text_boxes">
                                            
					</td>
					<td align="center" id="rf_uom_<? echo $i; ?>">
						<?
						echo create_drop_down( "cboRfUom_$i", 56, $unit_of_measurement,'', '',"",$row[csf("uom_id")],"",1,"12,15,27,1,23" );
						?>
					</td>

					<td align="center" id="rf_req_qty_<? echo $i; ?>">
						<input style="width:50px;" type="text" class="text_boxes_numeric"  name="txtRfReqQty_<? echo $i; ?>" id="txtRfReqQty_<? echo $i; ?>" placeholder="" value="<? echo $row[csf("required_qty")]; ?>" readonly/>
					</td>

					 <td align="center" id="rf_req_dzn_<? echo $i; ?>" style="display:none;">
                        <input style="width:50px;" type="text" class="text_boxes" value="<? echo $row[csf("remarks_ra")]; ?>"  name="txtRfRemarks_<? echo $i;?>" id="txtRfRemarks_<? echo $i;?>" onClick="required_fab_remarks(<? echo $i; ?>);"  />
                     </td>



					<td id="rf_image_<? echo $i; ?>"><input type="button" class="image_uploader" name="txtRfFile_<? echo $i; ?>" id="txtRfFile_<? echo $i; ?>" onClick="file_uploader ( '../../../', document.getElementById('updateidRequiredDtl_<? echo $i;?>').value,'', 'required_fabric_1', 0 ,1)" value="ADD IMAGE"></td>
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
						$sql="select a.id,a.sample_name,b.id as dtls_id from  lib_sample a ,sample_development_dtls b where  a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.id=b.sample_name and b.entry_form_id=459 and b.sample_mst_id='$up_id' group by a.id,a.sample_name,b.id order by b.id";
						$samp_array=array();
						$samp_result=sql_select($sql);
						if(count($samp_result)>0)
						{
							foreach($samp_result as $keys=>$vals)
							{
								$samp_array[$vals[csf("id")]]=$vals[csf("sample_name")];
							}

						}
						echo create_drop_down( "cboRaSampleName_$i", 100, $samp_array,"", '', "",$row[csf("sample_name_ra")], "","");

						?>

					</td>

					<td align="center" id="raItemId_1" width="100">
						<?
						$sql_gmts=sql_select("select id,gmts_item_id from sample_development_dtls where is_deleted=0  and status_active=1 and entry_form_id=459 and sample_mst_id='$up_id'");
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
						$sql="select item_name,id from lib_item_group where  is_deleted=0  and
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
					<td id="ra_image_1"><input type="button" class="image_uploader" name="txtRaFile_<? echo $i;?>" id="txtRaFile_<? echo $i;?>" onClick="file_uploader ( '../../../', document.getElementById('updateidAccessoriesDtl_<? echo $i;?>').value,'', 'required_accessories_1', 0 ,1)"style="width:80px;" value="ADD IMAGE"></td>
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
						$sql="select a.id,a.sample_name,b.id as dtls_id from  lib_sample a ,sample_development_dtls b where  a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.id=b.sample_name and b.entry_form_id=459 and b.sample_mst_id='$up_id' group by a.id,a.sample_name,b.id order by b.id";
						$samp_array=array();
						$samp_result=sql_select($sql);
						if(count($samp_result)>0)
						{
							foreach($samp_result as $keys=>$vals)
							{
								$samp_array[$vals[csf("id")]]=$vals[csf("sample_name")];
							}

						}

						echo create_drop_down( "cboReSampleName_$i", 140, $samp_array,"", '', "",$row[csf("sample_name_re")],"","");
						?>

					</td>

					<td align="center" id="reItemIid_1">
						<?
						$sql_gmts_re=sql_select("select id,gmts_item_id from sample_development_dtls where is_deleted=0  and status_active=1 and entry_form_id=459 and sample_mst_id='$up_id'");
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



					<td id="re_image_1"><input type="button" class="image_uploader" name="reTxtFile_<? echo $i;?>" id="reTxtFile_<? echo $i;?>" size="20" style="width:170px;" value="CLICK TO ADD/VIEW IMAGE" onClick="file_uploader ( '../../../', document.getElementById('updateidRequiredEmbellishdtl_<? echo $i;?>').value,'', 'required_embellishment_1', 0 ,1);"></td>
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
	$size_arr=return_library_array( "select id,size_name from lib_size", "id", "size_name" );

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
  			$new_system_id=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', '', date("Y",time()), 5, "select requisition_number_prefix,requisition_number_prefix_num from sample_development_mst where  entry_form_id=459 and company_id=$cbo_company_name and YEAR(insert_date)=".date('Y',time())." order by id desc ", "requisition_number_prefix", "requisition_number_prefix_num" ));
  		}
  		if($db_type==2)
  		{
  			$new_system_id=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', '', date("Y",time()), 5, "select requisition_number_prefix,requisition_number_prefix_num from sample_development_mst where entry_form_id=459 and company_id=$cbo_company_name and to_char(insert_date,'YYYY')=".date('Y',time())." order by id desc ", "requisition_number_prefix", "requisition_number_prefix_num" ));
  		}


  		$field_array="id,requisition_number_prefix,requisition_number_prefix_num,requisition_number,sample_stage_id,requisition_date,quotation_id,style_ref_no,company_id,location_id,buyer_name,season,product_dept,dealing_marchant,agent_name,buyer_ref,estimated_shipdate,remarks,inserted_by,insert_date,status_active,is_deleted,entry_form_id,is_copy,req_ready_to_approved,material_delivery_date,team_leader";
  		$data_array="(".$id_mst.",'".$new_system_id[1]."',".$new_system_id[2].",'".$new_system_id[0]."',".$cbo_sample_stage.",".$txt_requisition_date.",".$txt_quotation_id.",".$txt_style_name.",".$cbo_company_name.",".$cbo_location_name.",".$cbo_buyer_name.",".$cbo_season_name.",".$cbo_product_department.",".$cbo_dealing_merchant.",".$cbo_agent.",".$txt_buyer_ref.",".$txt_est_ship_date.",".$txt_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0,459,1,'2',".$txt_material_dlvry_date.",".$cbo_sample_team.")";
		
		
  		$rID=sql_insert("sample_development_mst",$field_array,$data_array,1);
  		$mst_id=return_field_value("max(id)","sample_development_mst","status_active=1 and is_deleted=0");


	    // sample details entry
  		$id_dtls=return_next_id( "id", "sample_development_dtls", 1 ) ;
  		$field_array_dtls= "id,sample_mst_id,sample_name,gmts_item_id,smv,article_no,sample_color,sample_prod_qty,submission_qty,sample_charge,sample_curency,inserted_by,insert_date,status_active,is_deleted,entry_form_id,size_data,fabric_status,acc_status,embellishment_status,fab_status_id,acc_status_id,embellishment_status_id";
		
  		$query_dtls=sql_select("select id,sample_mst_id,sample_name,gmts_item_id,smv,article_no,sample_color,sample_prod_qty,submission_qty,delv_start_date,delv_end_date,sample_charge,sample_curency,status_active,is_deleted,entry_form_id,size_data,fabric_status,acc_status,embellishment_status,fab_status_id,acc_status_id,embellishment_status_id from sample_development_dtls where entry_form_id=459 and status_active=1 and is_deleted=0 and sample_mst_id=$update_id");

  		$id_size=return_next_id( "id","sample_development_size", 1 ) ;
  		$field_array_size="id, mst_id, dtls_id,size_id,bh_qty,plan_qty,dyeing_qty,test_qty,self_qty,total_qty,inserted_by, insert_date, status_active, is_deleted";

  		for ($i=0;$i<count($query_dtls);$i++)
  		{
  			if ($i!=0) $data_array_dtls .=",";
  			$data_array_dtls .="(".$id_dtls.",".$mst_id.",".$query_dtls[$i][csf("sample_name")].",".$query_dtls[$i][csf("gmts_item_id")].",'".$query_dtls[$i][csf("smv")]."','".$query_dtls[$i][csf("article_no")]."','".$query_dtls[$i][csf("sample_color")]."','".$query_dtls[$i][csf("sample_prod_qty")]."','".$query_dtls[$i][csf("submission_qty")]."','".$query_dtls[$i][csf("sample_charge")]."','".$query_dtls[$i][csf("sample_curency")]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0,459,'".$query_dtls[$i][csf("size_data")]."',0,0,0,0,0,0)";


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
  						$size_id = return_id( $size_name, $size_arr, "lib_size", "id,size_name","459");
  						$new_array_size[$size_id]=str_replace("'","",$size_name);
  					}
  					else $size_id =  array_search($size_name, $new_array_size);
  				}
  				else $size_id=0;

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
  		$field_array_fabric= "id,sample_mst_id,sample_name,gmts_item_id,body_part_id,fabric_nature_id,fabric_description,determination_id,gsm,dia,color_data,color_type_id,width_dia_id,uom_id,required_dzn,required_qty,inserted_by,insert_date,status_active,is_deleted,form_type";
  		$query_fabric=sql_select("SELECT id,sample_mst_id,sample_name,gmts_item_id,body_part_id,fabric_nature_id,fabric_description,gsm,dia,color_data,color_type_id,width_dia_id,uom_id,required_dzn,required_qty,determination_id,form_type from sample_development_fabric_acc where form_type=1 and status_active=1 and is_deleted=0 and sample_mst_id=$update_id");
  		//$field_array_col="id, mst_id, dtls_id,color_id,contrast,inserted_by, insert_date, status_active, is_deleted";
  		
  		$field_array_col="id, mst_id, dtls_id,color_id,contrast,inserted_by, insert_date, status_active, is_deleted";
		//LOT,STORE_ID,COMMENTS,
		
		
		$idColorTbl=return_next_id( "id","sample_development_rf_color", 1 ) ;
  		for($i=0;$i<count($query_fabric);$i++)
  		{

  			if ($i!=0) $data_array_fabric .=",";
				//
  			$data_array_fabric .="(".$id_fabric.",".$mst_id.",".$query_fabric[$i][csf("sample_name")].",".$query_fabric[$i][csf("gmts_item_id")].",".$query_fabric[$i][csf("body_part_id")].",".$query_fabric[$i][csf("fabric_nature_id")].",'".$query_fabric[$i][csf("fabric_description")]."','".$query_fabric[$i][csf("determination_id")]."','".$query_fabric[$i][csf("gsm")]."','".$query_fabric[$i][csf("dia")]."','".$query_fabric[$i][csf("color_data")]."',".$query_fabric[$i][csf("color_type_id")].",".$query_fabric[$i][csf("width_dia_id")].",".$query_fabric[$i][csf("uom_id")].",'".$query_fabric[$i][csf("required_dzn")]."','".$query_fabric[$i][csf("required_qty")]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0,1)";
  			$ex_data=explode("-----",$query_fabric[$i][csf("color_data")]);
  			foreach($ex_data as $color_data)
  			{
  				//$ex_size_data=explode("_",$color_data);
				$ex_size_data=explode("__",$color_data);

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
	$sql = "select requisition_number_prefix_num, requisition_number, refusing_cause from sample_development_mst where is_acknowledge!=1 and status_active=1 and is_deleted=0 and refusing_cause is not null order by id desc";
	$data_array = sql_select($sql);

	?>
	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="290">
		<thead>
			<th width="20" align="center">SL</th>
			<th width="90">Req No</th>
			<th>Refusing Cause</th>
		</thead>
	</table><!--onClick='set_form_data("<? //echo $row[csf('id')] . "**" . $row[csf('company_id')] . "**" . $row[csf('sales_booking_no')]; ?>")' -->
	<div style="width:290px; max-height:130px; overflow-y:scroll" id="list_container_cause" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="272" class="rpt_table" id="tbl_list_search">
			<?
			$i = 1;
			foreach ($data_array as $row) {
					if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer">
					<td width="20" align="center"><? echo $i; ?></td>
					<td width="90"><? echo $row[csf('requisition_number')]; ?></td>
					<td><? echo $row[csf('refusing_cause')]; ?></td>
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

if($action=="body_part_popup")
{
	echo load_html_head_contents("Body Part Select","../../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	?>
	<script>
	function js_set_value(id, name,type)
	{
		document.getElementById('gid').value=id;
		document.getElementById('gname').value=name;
		document.getElementById('gtype').value=type;
		parent.emailwindow.hide();
	}
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;">
        <input type="hidden" id="gid" name="gid"/>
        <input type="hidden" id="gname" name="gname"/>
        <input type="hidden" id="gtype" name="gtype"/>
        <?
		
        $sql_tgroup=sql_select( "select l.id,l.body_part_full_name,l.body_part_short_name,l.body_part_type from lib_body_part_tag_entry_page lt join lib_body_part l on lt.entry_page_id=459 and lt.mst_id=l.id and l.status_active=1 and l.is_deleted=0"); 
		
        ?>
        <table width="420" cellspacing="0" class="rpt_table" border="0" rules="all">
            <thead>
            	<th width="40">SL</th><th width="300">Item Group</th><th>Type</th>
            </thead>
        </table>
        <table width="420" cellspacing="0" class="rpt_table" border="0" rules="all" id="item_table">
            <tbody>
            <?
            $i=1;
            foreach($sql_tgroup as $row_tgroup)
			{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr onClick="js_set_value(<? echo $row_tgroup[csf('id')]; ?>, '<? echo $row_tgroup[csf('body_part_full_name')]; ?>', '<? echo $row_tgroup[csf('body_part_type')]; ?>')" bgcolor="<? echo $bgcolor; ?>">
					<td width="40"><? echo $i; ?></td><td width="300"><? echo $row_tgroup[csf('body_part_full_name')]; ?></td><td width=""><? echo $body_part_type[$row_tgroup[csf('body_part_type')]]; ?></td>
				</tr>
				<?
				$i++;
            }
            ?>
            </tbody>
        </table>
        </div>
	</body>
	<script>
	setFilterGrid('item_table',-1)
	</script>
	<!--<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>-->
	</html>
	<?
	exit();
}

if ($action=="color_popup_rf")
{
	echo load_html_head_contents("Sample Requisition Info","../../../../", 1, 1, $unicode);
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
				$('#increase_'+i).removeAttr("value").attr("value","+");
				$('#decrease_'+i).removeAttr("value").attr("value","-");
				$('#txtComposition_'+i).removeAttr("ondblclick").attr("ondblclick","open_fabric_description("+i+");");
				$('#increase_'+i).removeAttr("onclick").attr("onclick","add_break_down_tr("+i+");");
				$('#decrease_'+i).removeAttr("onclick").attr("onclick","fn_deleteRow("+i+");");
 			}
		}
		function fn_deleteRow(rowNo) 
		{
			if(rowNo!=0)
			{
				var index=rowNo-1
				$("#col_tbl tbody tr:eq("+index+")").remove();
				var numRow=$('#col_tbl tbody tr').length;
				for(i = rowNo;i <= numRow;i++){
					$("#col_tbl tr:eq("+i+")").find("input,select").each(function() {
						$(this).attr({
						  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
						  //'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i },
						  'value': function(_, value) { return value }              
						}); 
						
					$('#increase_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
					$('#decrease_'+i).removeAttr("onClick").attr("onClick","fn_deleteRow("+i+")");
					//$("#col_tbl tr:eq("+i+") td:eq(0)").text(i);
					$('#txtSL_'+(i)).val(i);
					})

				}
			}		
		}		
		function fnc_close( )
		{
			var uomArr=$('#cboUom_1').val()*1;
			var rowCount = $('#col_tbl tbody tr').length;
			var breck_down_data="";
			var display_col="";
			var total_grey_qty=0;
			for(var i=1; i<=rowCount; i++)
			{
				
				if($('#cboUom_'+i).val()*1 != uomArr ){
					alert("UOM Mixed Not Allowed");return false;	
				}
				
				
				if(breck_down_data=="")
				{
					breck_down_data+=($('#txtSL_'+i).val()*1)+'__'+$('#txtColor_'+i).val()+'__'+$('#hiddenColorId_'+i).val()*1+'__'+$('#txtYarnColor_'+i).val()+'__'+$('#txtCount_'+i).val()+'__'+$('#txtComposition_'+i).val()+'__'+$('#cboType_'+i).val()*1+'__'+$('#cboYarnSource_'+i).val()*1+'__'+$('#txtGreyQnty_'+i).val()*1+'__'+$('#cboUom_'+i).val()*1+'__'+$('#txtLot_'+i).val()+'__'+$('#cboYarnStore_'+i).val()+'__'+$('#txtComments_'+i).val();
					  display_col +=$('#txtColor_'+i).val() ;
				}
				else
				{
					breck_down_data+="-----"+($('#txtSL_'+i).val()*1)+'__'+$('#txtColor_'+i).val()+'__'+$('#hiddenColorId_'+i).val()*1+'__'+$('#txtYarnColor_'+i).val()+'__'+$('#txtCount_'+i).val()+'__'+$('#txtComposition_'+i).val()+'__'+$('#cboType_'+i).val()*1+'__'+$('#cboYarnSource_'+i).val()*1+'__'+$('#txtGreyQnty_'+i).val()*1+'__'+$('#cboUom_'+i).val()*1+'__'+$('#txtLot_'+i).val()+'__'+$('#cboYarnStore_'+i).val()+'__'+$('#txtComments_'+i).val();
					  
					  display_col +='***'+$('#txtColor_'+i).val() ;
				}
				total_grey_qty+=$('#txtGreyQnty_'+i).val()*1;
			}
			
 			document.getElementById('txtRfColorAllData').value=breck_down_data;
 			document.getElementById('displayAllcol').value=display_col;
 			document.getElementById('total_grey').value=total_grey_qty;
			parent.emailwindow.hide();
		}
		function open_fabric_description(i)
		{
			var cbofabricnature=100;
			var libyarncountdeterminationid =1;
			var page_link='sweater_sample_requisition_v2_controller.php?action=fabric_description_popup&fabric_nature='+cbofabricnature+'&libyarncountdeterminationid='+libyarncountdeterminationid;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Fabric Description', 'width=900px,height=200px,center=1,resize=0,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var fab_des_id=this.contentDoc.getElementById("fab_des_id");
				var fab_desctiption=this.contentDoc.getElementById("fab_desctiption");
				var fab_gsm=this.contentDoc.getElementById("fab_gsm");
				//document.getElementById('libyarncountdeterminationid_'+i).value=fab_des_id.value;
				document.getElementById('txtComposition_'+i).value=fab_desctiption.value;
				//document.getElementById('txtRfGauge_1'+i).value=fab_gsm.value;
			}
		}
    </script>

    <body>
		<div align="center" style="width:100%;" >
        <? echo load_freeze_divs ("../../../../",$permission,1); ?>
        <form name="size_1" id="size_1">
			<fieldset style="width:990px;">
            <table align="center" cellspacing="0" width="980" class="rpt_table" border="1" rules="all" id="col_tbl" >
            	<thead>
            	
            		<tr>
            			<th width="30">SL</th>
            			<th width="70">Gmts Color</th>
                        <th width="70">Yarn Color</th>
            			<th width="50">Count</th>
            			<th >Composition</th>
            			<th width="70">Type</th>
            			<th width="70">Yarn Source</th>
            			<th width="50">Req. Qty</th>
            			<th width="65">UOM</th>
            			<th width="50">Lot</th>
            			<th width="100">Store</th>
                        <th width="100">Remarks</th>
            			<th width="70">
            				<input type="hidden" name="mainupid" class="text_boxes" ID="mainupid" value="<? echo $mainId; ?>" style="width:30px" />
                            <Input type="hidden" name="dtlsupid" class="text_boxes" ID="dtlsupid" value="<? echo $dtlId; ?>" style="width:30px" />

            			</th>
            		</tr>


            	</thead>
                <tbody>

                	<?
                	if($data)
                	{
                		$data_all=explode('-----',$data);
                		$count_tr=count($data_all);
                		if($count_tr>0)
                		{
                			$i=1;
                			foreach ($data_all as $size_data)
                			{
							$ex_size_data=explode('__',$size_data);
							
							list($txtSL,$txtColor,$hiddenColorId,$txtYarnColor,$txtCount,$txtComposition,$cboType,$cboYarnSource,$txtGreyQnty,$cboUom,$txtLot,$cboStore,$txtComments)=explode('__',$size_data);

							?>
							<tr id="row_<? echo $i; ?>">

								<td>
                                	<input name="txtSL_<? echo $i; ?>" class="text_boxes" id="txtSL_<? echo $i; ?>" value="<? echo $i; ?>" style="width:30px" readonly /><input type="hidden" name="colorupid_<? echo $i; ?>" class="text_boxes" ID="colorupid_<? echo $i; ?>" value="" style="width:30px">
                                </td>
								<td>
									<input name="txtColor_<? echo $i; ?>" class="text_boxes" id="txtColor_<? echo $i; ?>" style="width:70px" value="<? echo $txtColor; ?>" / disabled ><input type="hidden" name="hiddenColorId_<? echo $i; ?>" id="hiddenColorId_<? echo $i; ?>" value="<? echo $hiddenColorId ?>">

								</td>
                                <td>
									<input name="txtYarnColor_<? echo $i; ?>" class="text_boxes" id="txtYarnColor_<? echo $i; ?>" style="width:70px" value="<? echo $txtYarnColor; ?>"  />

								</td>
                                
                                <td>
									<!-- <input name="txtCount_<? echo $i; ?>" class="text_boxes" id="txtCount_<? echo $i; ?>" style="width:45px" value="<? echo $txtCount; ?>"  /> -->
									<? echo create_drop_down( "txtCount_".$i, 50, "select id,yarn_count from  lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count", "id,yarn_count",1," -- Select Count --", $txtCount, '','','' ); ?>
								</td>
                                <td>
									<input name="txtComposition_<? echo $i; ?>" class="text_boxes" id="txtComposition_<? echo $i; ?>" style="width:90px" value="<? echo $txtComposition; ?>" onDblClick="open_fabric_description(<? echo $i; ?>)" readonly placeholder="Browse" />
								</td>

								<td>
								<?
									echo create_drop_down( "cboType_".$i, 65, $yarn_type,"", 1, "--Select--", 1, "" );
								?>
                            	</td>
                                <td>
								<?
									echo create_drop_down( "cboYarnSource_".$i, 65, $fabric_source,"", 1, "-- Select --", 2, "","" );
								?>
                            	</td>

								<td><input name="txtGreyQnty_<? echo $i; ?>" class="text_boxes_numeric" ID="txtGreyQnty_<? echo $i; ?>" style="width:45px"  value="<? echo $txtGreyQnty;?>"   /></td>
                                <td>
								<?
                                	//$cboUom='12,15,27,1,23';
									echo create_drop_down( "cboUom_".$i, 65, $unit_of_measurement,"", 0, "-- Select --", $cboUom, "",0,'15' );
								?>
                            	</td>
                                <td>
									<input name="txtLot_<? echo $i; ?>" class="text_boxes" id="txtLot_<? echo $i; ?>" style="width:45px" value="<? echo $txtLot; ?>"  />
								</td>
                                <td>
								<?
									if ($_SESSION['logic_erp']['store_location_id'] != '') {$store_location_credential_cond = "and a.id in(".$_SESSION['logic_erp']['store_location_id'].")";} else { $store_location_credential_cond = "";}
								
									echo create_drop_down( "cboYarnStore_".$i, 100, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$company' $location_cond and a.status_active=1 and a.is_deleted=0 and b.category_type=1 $store_location_credential_cond  group by a.id, a.store_name order by a.store_name", "id,store_name", 1, "--Select--", $cboStore, "",0,'','','','','','');
								?>
                            	</td>
                                <td><input type="text" id="txtComments_<? echo $i; ?>" name="txtComments_<? echo $i; ?>" style="width:90px" class="text_boxes" value="<? echo $txtComments; ?>" maxlength="50"  /></td>
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
						$sql_col="select id,sample_color from sample_development_dtls where entry_form_id=459 and sample_mst_id=$mainId and sample_name=$sampleName and gmts_item_id=$garmentItem and is_deleted=0 and sample_color=$gmtsColorId  and status_active=1 order by id ASC";
						$sql_result =sql_select($sql_col);
						$i=1;
						foreach($sql_result as $row)
						{
							$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );

							?>

							<tr id="row_<? echo $i; ?>">
								<td align="center" ><input name="txtSL_<? echo $i; ?>" class="text_boxes" ID="txtSL_<? echo $i; ?>" value="<? echo $i; ?>" style="width:30px" readonly /><input type="hidden" name="colorupid_<? echo $i; ?>" class="text_boxes" ID="colorupid_<? echo $i; ?>" value="" style="width:30px"></td>

								<td align="center" >
                                <input name="txtColor_<? echo $i; ?>" class="text_boxes" ID="txtColor_<? echo $i; ?>" value="<? echo $color_library[$row[csf('sample_color')]];  ?>" style="width:70px"  disabled />
								<input type="hidden" name="hiddenColorId_<? echo $i; ?>" id="hiddenColorId_<? echo $i; ?>" value="<? echo $row[csf('sample_color')];  ?>">
								</td>
                                
                                <td align="center" >
                                <input name="txtYarnColor_<? echo $i; ?>" class="text_boxes" ID="txtYarnColor_<? echo $i; ?>" value="" style="width:70px"  />
								</td>

								<td>
									<!-- <input name="txtCount_<? echo $i; ?>" class="text_boxes" ID="txtCount_<? echo $i; ?>" style="width:45px" value="<? echo $txtColor; ?>"  /> -->
									<? echo create_drop_down( "txtCount_".$i, 50, "select id,yarn_count from  lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count", "id,yarn_count",1," -- Select Count --", $txtColor, '','','' ); ?>
								</td><td>
									<input name="txtComposition_<? echo $i; ?>" class="text_boxes" ID="txtComposition_<? echo $i; ?>" style="width:90px" value="<? echo $FabricDescription; ?>" onDblClick="open_fabric_description(<? echo $i; ?>)" readonly placeholder="Browse" />
								</td>

								<td>
								<? 
									echo create_drop_down( "cboType_".$i, 65, $yarn_type,"", 1, "--Select--", 1, "" );
								?>
                            	</td>
                                <td>
								<?
									echo create_drop_down( "cboYarnSource_".$i, 65,$fabric_source ,"", 1, "-- Select --", 2, "" );
								?>
                            	</td>

								<td><input name="txtGreyQnty_<? echo $i; ?>" class="text_boxes" ID="txtGreyQnty_<? echo $i; ?>" style="width:65px"  value="<? echo $txtGreyQnty;?>"   /></td>
                                <td>
								<?
									//$sampleUom='12,15,27,1,23';
									echo create_drop_down( "cboUom_".$i, 55, $unit_of_measurement,"", 0, "-- Select --", $sampleUom, "" ,0,"15");
								?>
                            	</td>
                                <td>
									<input name="txtLot_<? echo $i; ?>" class="text_boxes" id="txtLot_<? echo $i; ?>" style="width:45px" value="<? echo $txtLot; ?>"  />
								</td>
                                <td>
								<?
									if ($_SESSION['logic_erp']['store_location_id'] != '') {$store_location_credential_cond = "and a.id in(".$_SESSION['logic_erp']['store_location_id'].")";} else { $store_location_credential_cond = "";}
								
									echo create_drop_down( "cboYarnStore_".$i, 100, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$company' $location_cond and a.status_active=1 and a.is_deleted=0 and b.category_type=1 $store_location_credential_cond  group by a.id, a.store_name order by a.store_name", "id,store_name", 1, "--Select--", $cboStore, "",0,'','','','','','');
								?>
                            	</td>
                                <td><input type="text" id="txtComments_<? echo $i; ?>" name="txtComments_<? echo $i; ?>" style="width:90px" class="text_boxes" value="<? echo $txtComments; ?>" maxlength="50"  /></td>
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
            <table align="center" cellspacing="0" width="880" class="rpt_table" border="1" rules="all" id="" >

                <tr>
                    <td colspan="12" align="center">
                        <input type="hidden" name="txtRfColorAllData" id="txtRfColorAllData" class="text_boxes"  value="" >
                        <input type="hidden" name="displayAllcol" id="displayAllcol">
                        <input type="hidden" name="total_grey" id="total_grey">
                     </td>
                </tr>
                <tr>
                    <td align="center" colspan="12" class="button_container">
                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
                    </td>
                </tr>
            </table>
            </fieldset>
            </form>
        </div>
    </body>
     <script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}


if ($action == 'show_calender')
{
	
list($company_name,$requisition_date,$sample_team)=explode("__",$data);	
	
	$date_from=$requisition_date;
	$date_to=add_date($requisition_date,17);
	
    if($date_from && $date_to)
    {
    	if($db_type==0)
        {
            $date_cond = " and confirm_del_end_date between '".date("Y-m-d", strtotime(str_replace("'", "",  $date_from)))."' and '".date("Y-m-d", strtotime(str_replace("'", "",  $date_to)))."'";
        }
        else
        {
        	$date_cond = " and confirm_del_end_date between '".date("d-M-Y", strtotime(str_replace("'", "",  $date_from)))."' and '".date("d-M-Y", strtotime(str_replace("'", "",  $date_to)))."'";
        }
    }

    $day_diff = datediff( 'd', $date_from, $date_to);

   	for($i=0; $i<$day_diff; $i++)
	{
		$new_date=add_date($date_from,$i);
		$day_arr[date("d-m-Y",strtotime($new_date))] = date("d-m-Y",strtotime($new_date));
		$day_month_arr[date("d-m-Y",strtotime($new_date))]['full_day'] = date("l",strtotime($new_date));
	}
	
	
	
	$sql = "select id, team_name, style_capacity from lib_sample_production_team where product_category=6 and status_active=1 and is_deleted=0 and id=$sample_team";
	$sql_res = sql_select($sql);
	foreach ($sql_res as $rows)
	{
		$req_capacity_arr[$rows[csf("id")]]=$rows[csf("style_capacity")];
	}	
	
	
	$sql_dtls = "select sample_mst_id, team_leader, confirm_del_end_date from sample_requisition_acknowledge where entry_form=345 and team_leader>0 and status_active=1 and is_deleted=0 and company_id=$company_name and team_leader=$sample_team $date_cond";
	$sql_rslt = sql_select($sql_dtls);
	foreach ($sql_rslt as $rows)
	{
		$key= date("d-m-Y",strtotime($rows[csf("confirm_del_end_date")]));
		$dataArr[$rows[csf("team_leader")]][$key][$rows[csf("sample_mst_id")]]=$rows[csf("sample_mst_id")];
	}
	
	
	foreach ($day_arr as $date) {
		$balance = $req_capacity_arr[$sample_team]-count($dataArr[$sample_team][$date]);
		if($balance > 0 && $day_month_arr[$date]['full_day'] != 'Friday'){
			$availableDataArr[$date]=array(
				_date=>$date,
				_day=>$day_month_arr[$date]['full_day'],
				_capa=>$req_capacity_arr[$sample_team],
				_book=>count($dataArr[$sample_team][$date]),
				_status=>'Available',
			);
		}
	}

	
	?>
	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="290">
		<thead>
			<th width="20" align="center">SL</th>
			<th width="60">Date</th>
			<th width="55">Day</th>
			<th width="35">Capa</th>
			<th width="35">Book</th>
			<th>Status</th>
		</thead>
	</table>
	<div style="width:290px; max-height:130px; overflow-y:scroll" id="list_container_cause" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="272" class="rpt_table" id="tbl_list_search">
			<?
			$i = 1;
			foreach ($availableDataArr as $rows) {
				$bgcolor = ($i % 2 == 0)?"#E9F3FF":"#FFFFFF";
				?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trc_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trc_<? echo $i; ?>"  style="cursor:pointer">
					<td width="20" align="center"><? echo $i; ?></td>
					<td width="60"><? echo $rows[_date]; ?></td>
					<td width="55"><? echo $rows[_day]; ?></td>
					<td width="35" align="right"><? echo $rows[_capa]; ?></td>
					<td width="35" align="right" bgcolor="<? echo ($rows[_book]>0)?'#FFA500':$bgcolor;?>"><? echo $rows[_book]; ?></td>
					<td align="center"><? echo $rows[_status];?></td>
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


if ($action=="update_delivery_date")
{
	
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	
	list($update_id,$delivery_date_update,$delvStartDate)=explode('__',$data);
	if($db_type==0){$date_format = "Y-m-d";}else{$date_format = "d-M-Y";}
	
	$is_embellishment=return_field_value("count(id)","sample_development_fabric_acc","sample_mst_id=$update_id and status_active=1 and is_deleted=0 and form_type=3");
	
	$ddDate=($is_embellishment>0)?10:6;
	
	
	//Teamp Capacity ........start;
	$sql = "select a.ID, a.TEAM_NAME, a.STYLE_CAPACITY,b.COMPANY_ID from lib_sample_production_team a,SAMPLE_DEVELOPMENT_MST b where product_category=6 and a.status_active=1 and a.is_deleted=0 and a.id=b.TEAM_LEADER and b.id=$update_id";
	
	//echo $sql;die;
	$sql_res = sql_select($sql);
	foreach ($sql_res as $rows)
	{
		$req_capacity=$rows[STYLE_CAPACITY];
		$company_id=$rows[COMPANY_ID];
		$team_id=$rows[ID];
	}	
	// echo $req_capacity;die;
	
	
	//Teamp Capacity ........end;
	 
	if($delivery_date_update==0)
	{
		
		$dateCon=date($date_format, strtotime($delvStartDate. " + $ddDate day"));
		$sql="select a.SAMPLE_MST_ID, a.TEAM_LEADER, a.CONFIRM_DEL_END_DATE from sample_requisition_acknowledge a where a.entry_form=345 and a.team_leader>0 and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id and a.team_leader=$team_id and a.CONFIRM_DEL_END_DATE >= '$dateCon'"; 
		 // echo $sql;die;
		$sql_requisition_conf = sql_select($sql);
		$requisition_conf_arr=array();
		foreach ($sql_requisition_conf as $rows)
		{
			$key=date($date_format,strtotime($rows[CONFIRM_DEL_END_DATE]));
			$requisition_conf_arr[$key][$rows[SAMPLE_MST_ID]]=$rows[SAMPLE_MST_ID];
		}	
		
		
		$addDate=($is_embellishment>0)?10:6;
		$extraDay=0;
		for($addDay=1;$addDay<=$addDate;$addDay++){
			if(date('l', strtotime($delvStartDate. " + $addDay day"))=='Friday'){
				$extraDay+=1;
			}
		}
		$addDate+=$extraDay;
		if(date('l', strtotime($delvStartDate. " + $addDay day"))=='Friday'){
			$addDate+=1;
		}
		
		$newDay=$addDate;
		for($i=0; $i<= count($requisition_conf_arr)+10; $i++){
			$newDay2 = $newDay+$i; 
			$key=date($date_format, strtotime($delvStartDate. " + $newDay2 day"));
			$blance=$req_capacity-count($requisition_conf_arr[$key])*1;
			
			 //echo $newDay.'*'. $i.'='.$key.',';
			if($blance>0){
				
				if(date('l', strtotime($delvStartDate. " + $addDate day"))=='Friday'){
					$addDate+=1;
				}
				if(date('l', strtotime($key))!='Friday'){
					echo $addDate;die;
					break;
				}
			}
			else
			{
				$addDate+=1;
			}
		}
		
		
	}
	else
	{
		$sql_dtls = "select ID,DELV_START_DATE from sample_development_dtls where SAMPLE_MST_ID=$update_id and status_active=1 and is_deleted=0 and entry_form_id=459";
		$sql_rslt = sql_select($sql_dtls);
		foreach ($sql_rslt as $rows)
		{
			
			
			$dateCon=date($date_format, strtotime($rows[DELV_START_DATE]. " + $ddDate day"));
			$sql="select a.SAMPLE_MST_ID, a.TEAM_LEADER, a.CONFIRM_DEL_END_DATE from sample_requisition_acknowledge a where a.entry_form=345 and a.team_leader>0 and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id and a.team_leader=$team_id and a.CONFIRM_DEL_END_DATE >= '$dateCon'"; 
			 // echo $sql;die;
			$sql_requisition_conf = sql_select($sql);
			$requisition_conf_arr=array();
			foreach ($sql_requisition_conf as $con_rows)
			{
				$key=date($date_format,strtotime($con_rows[CONFIRM_DEL_END_DATE]));
				$requisition_conf_arr[$key][$con_rows[SAMPLE_MST_ID]]=$con_rows[SAMPLE_MST_ID];
			}	
			
			
			
			$addDate=($is_embellishment>0)?10:6;
			$extraDay=0;
			for($addDay=1;$addDay<=$addDate;$addDay++){
				if(date('l', strtotime($rows[DELV_START_DATE]. " + $addDay day"))=='Friday'){
					$extraDay+=1;
				}
			}
			$addDate+=$extraDay;			
			
			if(date('l', strtotime($rows[DELV_START_DATE]. " + $addDay day"))=='Friday'){
				$addDate+=1;
			}
			
			
			
			$newDay=$addDate;
			for($i=0; $i<= count($requisition_conf_arr)+10; $i++){
				$newDay2 = $newDay+$i; 
				$key=date($date_format, strtotime($rows[DELV_START_DATE]. " + $newDay2 day"));
				$blance=$req_capacity-count($requisition_conf_arr[$key])*1;
				if($blance>0){
					if(date('l', strtotime($rows[DELV_START_DATE]. " + $addDate day"))=='Friday'){
						$addDate+=1;
					}
					//echo $addDate;
					if(date('l', strtotime($key))!='Friday'){
						break;
					}
				}
				else
				{
					$addDate+=1;
				}
			}
		

			
			
			
			$delivery_date=date($date_format,strtotime(add_date(date('Y-m-d',strtotime($rows[DELV_START_DATE])),$addDate)));
			$id_arr[]=$rows[ID];
			$data_array_up[$rows[ID]] = explode("*",("'".$delivery_date."'"));
		}
		$field_array_up="delv_end_date";
		
		$rID=execute_query(bulk_update_sql_statement("sample_development_dtls", "id",$field_array_up,$data_array_up,$id_arr ));
	}
	
	
	
	
	if($db_type==0)
	{
		if($rID )
		{
			mysql_query("COMMIT");
			echo $addDate;
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
			echo $addDate;
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



if($action=='get_sample_requisition'){

		$sql="SELECT a.ID AS SAMPLE_MST_ID,a.COMPANY_ID,a.REQUISITION_NUMBER,a.REQUISITION_DATE,a.DEALING_MARCHANT,a.BUYER_NAME,a.SEASON,a.STYLE_REF_NO,a.TEAM_LEADER,a.REFUSING_CAUSE,
		sum(b.SAMPLE_PROD_QTY) as SAMPLE_QTY,max(b.DELV_START_DATE) as DELV_START_DATE, min(b.DELV_END_DATE) as DELV_END_DATE,
		LISTAGG(CAST(b.EMBELLISHMENT_STATUS_ID AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.id) as EMBELLISHMENT_STATUS_ID,
		sum(c.REQUIRED_QTY) AS REQUIRED_QTY
		 FROM SAMPLE_DEVELOPMENT_MST a,SAMPLE_DEVELOPMENT_DTLS b,SAMPLE_DEVELOPMENT_FABRIC_ACC c WHERE a.id=b.SAMPLE_MST_ID and b.SAMPLE_MST_ID=c.SAMPLE_MST_ID  and a.entry_form_id=459 and c.FORM_TYPE=1 and a.ID='$data'
		 and a.IS_DELETED=0 and a.STATUS_ACTIVE=1 and b.IS_DELETED=0 and b.STATUS_ACTIVE=1 and c.IS_DELETED=0 and c.STATUS_ACTIVE=1
		group by a.ID,a.COMPANY_ID,a.REQUISITION_NUMBER,a.REQUISITION_DATE,a.DEALING_MARCHANT,a.BUYER_NAME,a.SEASON,a.STYLE_REF_NO,a.TEAM_LEADER,a.REFUSING_CAUSE
		";// and a.is_acknowledge <> 1
		
		//echo $sql;die;
		
		$nameArray=sql_select( $sql );
		foreach ($nameArray as $row)
		{
			$confirm_del_end_date=$row[DELV_END_DATE];
			echo "'".$row[UPDATE_ID]."'***".$row[SAMPLE_MST_ID]."***'".$row[REQUISITION_NUMBER]."'***'".$row[REQUISITION_DATE]."'***'".$row[COMPANY_ID]."'***'".$row[BUYER_NAME]."'***'".$row[SEASON]."'***'".$row[STYLE_REF_NO]."'***'".$row[SAMPLE_QTY]."'***'".$row[REQUIRED_QTY]."'***'".$row[EMBELLISHMENT_STATUS_ID]."'***'".$row[DELV_START_DATE]."'***'".$row[DELV_END_DATE]."'***'".$row[TEAM_LEADER]."'***'".$confirm_del_end_date."'***''***'".$row[DEALING_MARCHANT]."'";	
		}
	
}



if($action=='check_attached_file'){
	$is_file_att = sql_select("select count(id) as TOTAL_FILE from common_photo_library where (MASTER_TBLE_ID = '".$data."' AND FORM_NAME = 'sweater_sample_requisition_1' AND FILE_TYPE = 2)");
	echo $is_file_att[0][TOTAL_FILE];
	exit();
}
if($action =='get_sample_style_source')
{
	$nameArray=sql_select( "select style_from_library,id from  variable_order_tracking where company_name='$data' and variable_list=77 order by id" );
	$style_source=0;
	foreach ($nameArray as $value) {
		$style_source=$value[csf('style_from_library')];
	}
	echo $style_source;
	exit();
}

if($action=='file_upload'){
	
	$filename = time().$_FILES['file']['name']; 
	$location = "../../../../file_upload/".$filename; 
	$uploadOk = 1; 

		if(move_uploaded_file($_FILES['file']['tmp_name'], $location))
		{ 
			echo $location; 
		}
		else
		{ 
			echo 0; 
		} 


  		$con = connect();
  		if($db_type==0)
  		{
  			mysql_query("BEGIN");
  		}

			$id=return_next_id( "id","COMMON_PHOTO_LIBRARY", 1 ) ;
			$data_array .="(".$id.",".$mst_id.",'sweater_sample_requisition_1','file_upload/".$filename."','2','".$filename."')";
			$field_array="id,master_tble_id,form_name,image_location,file_type,real_file_name";
			$rID=sql_insert("COMMON_PHOTO_LIBRARY",$field_array,$data_array,1);

  		if($db_type==0)
  		{
  			if($rID)
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
  			if($rID)
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
?>
