<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_name=$_SESSION['logic_erp']['user_id'];
$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0",'id','buyer_name');
$company_arr=return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0",'id','company_name');
$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
$season_arr=return_library_array( "select id, season_name from lib_buyer_season where status_active=1 and is_deleted=0","id","season_name");

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 110, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in ($data) and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select --", $selected, "" );
	exit();
}

if ($action=="load_drop_down_store")
{
	echo create_drop_down( "cbo_store_name", 120, "select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id  and a.status_active=1 and a.is_deleted=0 and a.company_id in ($data) and  b.category_type=2 group by a.id,a.store_name order by a.store_name","id,store_name", 1, "--Select Store--", 1, "",0 );
	exit();
}

if($action=="load_drop_down_floors")
{
    extract($_REQUEST);

    $datas=explode("_", $data);

    $company_ids = str_replace("'","",$datas[0]); 
    if($datas[1] != ""){$store_id_cond="and b.store_id in ($datas[1])";}

	echo create_drop_down( "cbo_floor_id", 120, "SELECT a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id FROM lib_floor_room_rack_mst a INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.floor_id
    	WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN($company_ids) $store_id_cond
    	GROUP BY a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id
    	ORDER BY a.floor_room_rack_name","floor_room_rack_id,floor_room_rack_name", 0, "", 0, "",$disable );
	exit();
}

if($action=="load_drop_down_rooms")
{
    extract($_REQUEST);

    $datas=explode("_", $data);

    $company_ids = str_replace("'","",$datas[0]); 
    if($datas[1] != ""){$store_id_cond="and b.store_id in ($datas[1])";}
    if($datas[2] != ""){$floor_id_cond="and b.floor_id in ($datas[2])";}

	echo create_drop_down( "cbo_room_id", 120, "SELECT a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id FROM lib_floor_room_rack_mst a INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.room_id
    	WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN($company_ids) $store_id_cond $floor_id_cond
    	GROUP BY a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id
    	ORDER BY a.floor_room_rack_name","floor_room_rack_id,floor_room_rack_name", 0, "", 0, "",$disable );
	exit();
}

if($action=="load_drop_down_racks")
{
    extract($_REQUEST);

    $datas=explode("_", $data);

    $company_ids = str_replace("'","",$datas[0]); 
    if($datas[1] != ""){$store_id_cond="and b.store_id in ($datas[1])";}
    if($datas[2] != ""){$floor_id_cond="and b.floor_id in ($datas[2])";}
    if($datas[3] != ""){$room_id_cond="and b.room_id in ($datas[3])";}

	echo create_drop_down( "cbo_rack_id", 120, "SELECT a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id FROM lib_floor_room_rack_mst a INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.rack_id
    	WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN($company_ids) $store_id_cond $floor_id_cond $room_id_cond
    	GROUP BY a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id
    	ORDER BY a.floor_room_rack_name","floor_room_rack_id,floor_room_rack_name", 0, "", 0, "",$disable );
	exit();
}

if($action=="load_drop_down_shelfs")
{
    extract($_REQUEST);

    $datas=explode("_", $data);

    $company_ids = str_replace("'","",$datas[0]); 
    if($datas[1] != ""){$store_id_cond="and b.store_id in ($datas[1])";}
    if($datas[2] != ""){$floor_id_cond="and b.floor_id in ($datas[2])";}
    if($datas[3] != ""){$room_id_cond="and b.room_id in ($datas[3])";}
    if($datas[4] != ""){$rack_id_cond="and b.rack_id in ($datas[4])";}

	echo create_drop_down( "cbo_shelf_id", 120, "SELECT a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id FROM lib_floor_room_rack_mst a INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.shelf_id
    	WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN($company_ids) $store_id_cond $floor_id_cond $room_id_cond $rack_id_cond
    	GROUP BY a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id
    	ORDER BY a.floor_room_rack_name","floor_room_rack_id,floor_room_rack_name", 0, "", 0, "",$disable );
	exit();
}

if($action=="requisition_id_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Sample Requisition Info","../../../", 1, 1, $unicode);
	if($cbo_company_name>0) $isDis=1; else $isDis=0;
?>
	<script>
		function fn_basis(type)
		{
			if(type==1)
			{
				document.getElementById('search_by_th').innerHTML="Requisition No";
			}
			else if(type==2)
			{
				document.getElementById('search_by_th').innerHTML="Style ref No";
			}
		}
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
			
			$('#hide_requ_id').val( id );
			$('#hide_requ_no').val( name );
		}
    </script>
</head>
<body>
	<div align="center" style="width:100%;" >
	<form name="searchsampledevelopmentfrm_1"  id="searchsampledevelopmentfrm_1" autocomplete="off">
        <table width="580" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead>
                <th width="120">Buyer Name</th>
                <th width="100">Search by</th>
                <th width="130" id="search_by_th">Requisition No</th>
                <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:80px;" /></th>
				<input type="hidden" name="hide_requ_no" id="hide_requ_no" value="" />
                <input type="hidden" name="hide_requ_id" id="hide_requ_id" value="" />
            </thead>
            <tr class="general">
                <td><? 
				if($cbo_company_name)
				{
					$company_cond= " and b.tag_company in ($cbo_company_name)";
				}
				
				echo create_drop_down( "cbo_buyer_name", 110, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $company_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select --", $selected, "" ); ?></td>
				<td>
					<?
					$report_type=array(1=>"Requisition no",2=>"Style ref no",3=>"Booking no");
						echo create_drop_down("cbo_search_by",130,$report_type,"", 0, "-- Select Type --", 1,"fn_basis(this.value);",0,'')
					?>
				</td>
                <td><input type="text" style="width:100px" class="text_boxes" name="txt_common_search" id="txt_common_search"  /></td>
                <td>
                    <input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( '<? echo $cbo_company_name;?>' +'_'+document.getElementById('cbo_buyer_name').value +'_'+document.getElementById('cbo_search_by').value+'_'+ document.getElementById('txt_common_search').value, 'create_requisition_id_search_list_view', 'search_div', 'sample_requisition_wise_finish_fabric_status_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:80px;" />
                </td>
            </td>
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
	if ($data[0]!=0)
	{ $company=" and company_id in ( $data[0] )";
	} 

	if ($data[1]!=0) $buyer=" and buyer_name='$data[1]'"; else $buyer="";//{ echo "Please Select Buyer First."; die; }

	$requisition_num="";
	if (trim($data[3]) != "") 
	{
		if (trim($data[2]) == 1) 
		{
			$search_cond=" and a.requisition_number like '%$data[3]' ";
		}
		else if(trim($data[2]) == 2) 
		{
			$search_cond=" and a.style_ref_no like '%$data[3]' ";
		}
		else if(trim($data[2]) == 3) 
		{
			$search_cond=" and b.booking_no like '%$data[3]' ";
		}
	}
	
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$arr=array (0=>$comp,1=>$buyer_arr);
	$sql="";

	$sql= "SELECT a.id, a.requisition_number, a.company_id, a.buyer_name, a.style_ref_no, b.booking_no FROM sample_development_mst a, wo_non_ord_samp_booking_dtls b where a.id=b.style_id and a.entry_form_id=203 and  a.status_active=1 and a.is_deleted=0 and a.sample_stage_id=2 and b.status_active=1 and b.is_deleted=0 $company $buyer $search_cond group by a.id, a.requisition_number, a.company_id, a.buyer_name, a.style_ref_no, b.booking_no order by id DESC";

	echo  create_list_view("list_view", "Company,Buyer Name,Requisition No,Style Name,Booking No.", "100,100,100,140,120","650","240",0, $sql , "js_set_value", "id,requisition_number", "", 1, "company_id,buyer_name,0,0,0", $arr , "company_id,buyer_name,requisition_number,style_ref_no,booking_no", "",'','0,0,0,0,0','',1) ;

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
			var fabric_description=trim(data[2])+' '+trim(data[5]);
			document.getElementById('fab_des_id').value=data[0];
			document.getElementById('fab_gsm').value=trim(data[3]);
			document.getElementById('fab_desctiption').value=trim(fabric_description);
			parent.emailwindow.hide();
		}
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $fabric_nature; ?>'+'**'+'<? echo $libyarncountdeterminationid; ?>'+'**'+document.getElementById('txt_construction').value+'**'+document.getElementById('txt_gsm_weight').value, 'fabric_description_popup_search_list_view', 'search_div', 'sample_requisition_wise_finish_fabric_status_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:100px;" />
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
				<input type="hidden" id="fab_gsm" name="fab_gsm" />
				<input type="hidden" id="fab_desctiption" name="fab_desctiption" />
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
			<table class="rpt_table" width="770" cellspacing="0" cellpadding="0" border="0" rules="all">
				<thead>
					<tr>
						<th width="50">SL No</th>
						<th width="100">Fab Nature</th>
						<th width="100">Construction</th>
						<th width="100">GSM/Weight</th>
						<th width="100">Color Range</th>
						<th width="">Composition</th>
					</tr>
				</thead>
			</table>
			<div id="" style="max-height:350px; width:770px; overflow-y:scroll">
				<table id="list_view" class="rpt_table" width="770" height="" cellspacing="0" cellpadding="0" border="1" rules="all">
					<tbody>
						<?

						$sql_data=sql_select("SELECT a.fab_nature_id,a.construction,a.gsm_weight,a.color_range_id,a.stich_length,a.process_loss,a.id, c.fabric_composition_name from  lib_yarn_count_determina_mst a join lib_yarn_count_determina_dtls b on a.id=b.mst_id left join lib_fabric_composition c  on c.id = a.fabric_composition_id and c.status_active=1 and c.is_deleted=0 where a.fab_nature_id= '$fabric_nature' and a.status_active=1 and a.is_deleted=0  and  b.status_active=1 and b.is_deleted=0 $search_con group by a.id, a.fab_nature_id, a.construction, a.gsm_weight, a.color_range_id, a.stich_length, a.process_loss, c.fabric_composition_name order by a.id");
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
								<td width=""><? echo $composition_arr[$row[csf('id')]]; ?></td>
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

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$req_no=str_replace("'", "", $txt_req_no);
	$cbo_sample_type=str_replace("'", "", $cbo_sample_type);
	$cbo_company_id=str_replace("'", "", $cbo_company_id);
	$cbo_buyer_name=str_replace("'", "", $cbo_buyer_name);
	$cbo_fab_nature=str_replace("'", "", $cbo_fab_nature);
	$cbo_store_name=str_replace("'", "", $cbo_store_name);
	$cbo_floor_id=str_replace("'", "", $cbo_floor_id);
	$cbo_room_id=str_replace("'", "", $cbo_room_id);
	$cbo_rack_id=str_replace("'", "", $cbo_rack_id);
	$cbo_shelf_id=str_replace("'", "", $cbo_shelf_id);
	$sample_year=str_replace("'", "", $cbo_year);
	$search_type=str_replace("'", "", $search_type);
	$txt_search_value=trim(str_replace("'", "", $txt_search_value));
	$cbo_value_with=str_replace("'", "", $cbo_value_with);

	$cbo_store_name 	= str_replace("'","",$cbo_store_name);
	$cbo_floor_id 		= str_replace("'","",$cbo_floor_id);
	$cbo_room_id 		= str_replace("'","",$cbo_room_id);
	$cbo_rack_id 		= str_replace("'","",$cbo_rack_id);
	$cbo_shelf_id 		= str_replace("'","",$cbo_shelf_id);

	if($cbo_store_name > 0){
		$g_store_cond = " and g.store_id in ($cbo_store_name)";
		$h_store_cond = " and h.store_id in ($cbo_store_name)";

		$from_store_cond = " and g.from_store in ($cbo_store_name)";
		$to_store_cond = " and g.to_store in ($cbo_store_name)";
	}

	if($cbo_floor_id > 0){
		$g_floor_cond = " and g.floor in ($cbo_floor_id)";
		$from_floor_cond = " and g.floor_id in ($cbo_floor_id)";
		$to_floor_cond = " and g.to_floor_id in ($cbo_floor_id)";
	}
	if($cbo_room_id > 0){
		$g_room_cond = " and g.room in ($cbo_room_id)";
		$from_room_cond = " and g.room in ($cbo_room_id)";
		$to_room_cond = " and g.to_room in ($cbo_room_id)";
	}
	if($cbo_rack_id > 0){
		$g_rack_cond = " and g.rack_no in ($cbo_rack_id)";
		$from_rack_cond = " and g.rack in ($cbo_rack_id)";
		$to_rack_cond = " and g.to_rack in ($cbo_rack_id)";
	}
	if($cbo_shelf_id > 0){
		$g_shelf_cond = " and g.shelf_no in ($cbo_shelf_id)";
		$from_shelf_cond = " and g.shelf in ($cbo_shelf_id)";
		$to_shelf_cond = " and g.to_shelf in ($cbo_shelf_id)";
	}

	     

	
	$year_cond="";

	if($db_type==2)
	{
		$year_cond=($sample_year)? " and  to_char(a.insert_date,'YYYY')=$sample_year" : " ";
	}
	else
	{
		$year_cond=($sample_year)? " and year(a.insert_date)=$sample_year" : " ";
	}

	if($txt_search_value !="")
	{
		if($search_type==1)
		{
			$req_no_cond =" and a.style_ref_no like '%$txt_search_value%'";
		}
		else{
			$req_no_cond =" and a.requisition_number_prefix_num =$txt_search_value";
		}
	}

	if(str_replace("'","",$cbo_company_id)==0) $company_name=""; else $company_name=" and a.company_id in ($cbo_company_id)";
	if(str_replace("'","",$cbo_buyer_name)==0) $buyer_name="";else $buyer_name=" and a.buyer_name=$cbo_buyer_name";

	$txt_date="";
	if(str_replace("'","",trim($txt_date_from))!="" && str_replace("'","",trim($txt_date_to))!="")
	{
		$txt_date=" and a.requisition_date between $txt_date_from and $txt_date_to";
	}

	$fromDate = change_date_format( str_replace("'","",trim($txt_date_from)) );
	$toDate = change_date_format( str_replace("'","",trim($txt_date_to)) );


	$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");

	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent, b.count_id, b.type_id from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0  and  b.status_active=1 and b.is_deleted=0";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		if($composition_arr[$row[csf('id')]]=="")
		{
			$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
		}
		else{
			$composition_arr[$row[csf('id')]].= " ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
		}
		

		$yarn_count_arr[$row[csf('id')]].=$lib_yarn_count[$row[csf('count_id')]].",";
		$yarn_type_arr[$row[csf('id')]].=$yarn_type[$row[csf('type_id')]].",";
	}

	$con = connect();
	$r_id=execute_query("delete from tmp_booking_id where userid=$user_name");
	if($r_id)
	{
		oci_commit($con);
	}

	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );

	$query="SELECT a.id as requ_id, a.company_id, a.requisition_number as requ_no,to_char(a.insert_date,'YYYY') as requ_year,e.booking_date, a.buyer_name, a.style_ref_no,d.id, d.finish_fabric, d.lib_yarn_count_deter_id as deter_id, c.color_id as garments_color, b.delivery_date, 
b.fabric_description, d.id as booking_dtls_id, e.id as booking_id, e.booking_no, c.fabric_color, d.gsm_weight, g.id as rcv_dtls_id, g.receive_qnty
from sample_development_mst a, sample_development_fabric_acc b,sample_development_rf_color c, wo_non_ord_samp_booking_dtls d, 
wo_non_ord_samp_booking_mst e, pro_batch_create_mst f, pro_finish_fabric_rcv_dtls g, inv_receive_master h
where a.entry_form_id in(203) and a.id=b.sample_mst_id and b.id=c.dtls_id and b.id=d.dtls_id and a.id=d.style_id 
and d.booking_no=e.booking_no and a.is_deleted=0  and b.form_type=1 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1 and e.booking_no=f.booking_no and f.id=g.batch_id and g.mst_id=h.id and h.entry_form=37 and d.gsm_weight=g.gsm and d.lib_yarn_count_deter_id=g.fabric_description_id and (f.color_id=c.fabric_color or f.color_id=c.color_id) and g.status_active=1 and g.is_deleted=0 $txt_date $company_name $buyer_name $req_no_cond  $year_cond $h_store_cond $g_floor_cond $g_room_cond $g_rack_cond $g_shelf_cond";
			
	//echo $query;//die;

	$rcv_sql=sql_select($query);
	
	$data_array=array();
	$buyer_summary_arr=array();
	foreach($rcv_sql as $row)
	{
		if($row[csf('fabric_color')])
		{
			$fabric_color =$row[csf('fabric_color')];
		}
		else
		{
			$fabric_color =$row[csf('color_id')];
		}
		
		//$string = $constructtion_arr[$row[csf('deter_id')]] .'**'. $composition_arr[$row[csf('deter_id')]] .'**'. $fabric_color .'**'. $row[csf('gsm_weight')];
		$string = $row[csf('deter_id')] .'**'. $fabric_color .'**'. $row[csf('gsm_weight')];
		$data_array[$row[csf('buyer_name')]][$row[csf('requ_year')]][$row[csf('requ_no')]][$string]['buyer_name']= $row[csf('buyer_name')];
		$data_array[$row[csf('buyer_name')]][$row[csf('requ_year')]][$row[csf('requ_no')]][$string]['requ_no']= $row[csf('requ_no')];
		$data_array[$row[csf('buyer_name')]][$row[csf('requ_year')]][$row[csf('requ_no')]][$string]['requ_year']= $row[csf('requ_year')];
		$data_array[$row[csf('buyer_name')]][$row[csf('requ_year')]][$row[csf('requ_no')]][$string]['booking_id']= $row[csf('booking_id')];
		$data_array[$row[csf('buyer_name')]][$row[csf('requ_year')]][$row[csf('requ_no')]][$string]['requ_id']= $row[csf('requ_id')];
		$data_array[$row[csf('buyer_name')]][$row[csf('requ_year')]][$row[csf('requ_no')]][$string]['style_ref_no']= $row[csf('style_ref_no')];
		$data_array[$row[csf('buyer_name')]][$row[csf('requ_year')]][$row[csf('requ_no')]][$string]['delivery_date'] .= $row[csf('delivery_date')].",";
		$data_array[$row[csf('buyer_name')]][$row[csf('requ_year')]][$row[csf('requ_no')]][$string]['fabrication'] = $constructtion_arr[$row[csf('deter_id')]] .', '. $composition_arr[$row[csf('deter_id')]];
		
		if($booking_dtls_check[$row[csf('booking_dtls_id')]]=="")
		{
			$booking_dtls_check[$row[csf('booking_dtls_id')]]=$row[csf('booking_dtls_id')];
			$data_array[$row[csf('buyer_name')]][$row[csf('requ_year')]][$row[csf('requ_no')]][$string]['fin_requ_qnty'] += $row[csf('finish_fabric')];
		}

		if($rcv_dtls_check[$row[csf('rcv_dtls_id')]]=="")
		{
			$rcv_dtls_check[$row[csf('rcv_dtls_id')]]=$row[csf('rcv_dtls_id')];
			$data_array[$row[csf('buyer_name')]][$row[csf('requ_year')]][$row[csf('requ_no')]][$string]['rcv_qnty'] += $row[csf('receive_qnty')];
		}
	}
	

	/* echo "<pre>";
	print_r($data_array);
	die; */
	
	
	$sql_finish_trans_in=sql_select("SELECT a.company_id, a.requisition_number as requ_no,to_char(e.booking_date,'YYYY') as requ_year,e.booking_date, a.buyer_name, a.style_ref_no,d.id, 
	d.finish_fabric, d.lib_yarn_count_deter_id as deter_id, c.color_id as garments_color, b.delivery_date, 
	b.fabric_description, d.id as booking_dtls_id, e.id as booking_id, e.booking_no, c.fabric_color, d.gsm_weight, f.id as trans_dtls_id, f.transfer_qnty
	from sample_development_mst a, sample_development_fabric_acc b,sample_development_rf_color c, wo_non_ord_samp_booking_dtls d, 
	wo_non_ord_samp_booking_mst e, inv_item_transfer_dtls f, inv_item_transfer_mst g, product_details_master h
	where a.entry_form_id in(203) and a.id=b.sample_mst_id and b.id=c.dtls_id and b.id=d.dtls_id and a.id=d.style_id 
	and d.booking_no=e.booking_no and a.is_deleted=0  and b.form_type=1
	and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1
	and e.id=f.to_order_id and f.mst_id=g.id and g.entry_form=306 and g.transfer_criteria in (6,8) and f.to_prod_id=h.id
	and d.gsm_weight=h.gsm and d.lib_yarn_count_deter_id=h.DETARMINATION_ID and (h.color = c.fabric_color or h.color = c.color_id) $txt_date $company_name $buyer_name $req_no_cond  $year_cond  $to_floor_cond $to_room_cond $to_rack_cond $to_shelf_cond");

	foreach($sql_finish_trans_in as $row)
	{
		if($row[csf('fabric_color')])
		{
			$fabric_color =$row[csf('fabric_color')];
		}
		else
		{
			$fabric_color =$row[csf('garments_color')];
		}
		
		//$string = $constructtion_arr[$row[csf('deter_id')]] .'**'. $composition_arr[$row[csf('deter_id')]] .'**'. $fabric_color .'**'. $row[csf('gsm_weight')];
		$string = $row[csf('deter_id')] .'**'. $fabric_color .'**'. $row[csf('gsm_weight')];
		$data_array[$row[csf('buyer_name')]][$row[csf('requ_year')]][$row[csf('requ_no')]][$string]['buyer_name']= $row[csf('buyer_name')];
		$data_array[$row[csf('buyer_name')]][$row[csf('requ_year')]][$row[csf('requ_no')]][$string]['requ_no']= $row[csf('requ_no')];
		$data_array[$row[csf('buyer_name')]][$row[csf('requ_year')]][$row[csf('requ_no')]][$string]['requ_year']= $row[csf('requ_year')];
		$data_array[$row[csf('buyer_name')]][$row[csf('requ_year')]][$row[csf('requ_no')]][$string]['booking_id']= $row[csf('booking_id')];
		$data_array[$row[csf('buyer_name')]][$row[csf('requ_year')]][$row[csf('requ_no')]][$string]['requ_id']= $row[csf('requ_id')];
		$data_array[$row[csf('buyer_name')]][$row[csf('requ_year')]][$row[csf('requ_no')]][$string]['style_ref_no']= $row[csf('style_ref_no')];
		$data_array[$row[csf('buyer_name')]][$row[csf('requ_year')]][$row[csf('requ_no')]][$string]['delivery_date'].= $row[csf('delivery_date')].",";
		$data_array[$row[csf('buyer_name')]][$row[csf('requ_year')]][$row[csf('requ_no')]][$string]['fabrication'] = $constructtion_arr[$row[csf('deter_id')]] .', '. $composition_arr[$row[csf('deter_id')]];
		
		if($booking_dtls_check[$row[csf('booking_dtls_id')]]=="")
		{
			$booking_dtls_check[$row[csf('booking_dtls_id')]]=$row[csf('booking_dtls_id')];
			$data_array[$row[csf('buyer_name')]][$row[csf('requ_year')]][$row[csf('requ_no')]][$string]['fin_requ_qnty'] += $row[csf('finish_fabric')];
		}

		if(!$booking_id_check[$row[csf('booking_id')]])
		{
			$booking_id_check[$row[csf('booking_id')]]=$row[csf('booking_id')];
			$BOOKINGID = $row[csf('booking_id')];
			$BOOKINGNO = $row[csf('booking_no')];
			$rID=execute_query("insert into tmp_booking_id (userid, booking_id,booking_no,type) values ($user_name,$BOOKINGID,'$BOOKINGNO',99)");
		}

		if($trans_dtls_check[$row[csf('trans_dtls_id')]]=="")
		{
			$trans_dtls_check[$row[csf('trans_dtls_id')]]=$row[csf('trans_dtls_id')];
			$data_array[$row[csf('buyer_name')]][$row[csf('requ_year')]][$row[csf('requ_no')]][$string]['trans_in_qnty'] += $row[csf('transfer_qnty')];
		}

	}
	unset($sql_finish_trans_in);

	if(empty($data_array))
	{
		echo "Data not found";
		die;
	}

	$sql_finish_trans_out=sql_select("SELECT a.company_id, a.requisition_number as requ_no,to_char(e.booking_date,'YYYY') as requ_year,e.booking_date, a.buyer_name, a.style_ref_no,d.id, 
	d.finish_fabric, d.lib_yarn_count_deter_id as deter_id, c.color_id as garments_color, b.delivery_date, 
	b.fabric_description, d.id as booking_dtls_id, e.id as booking_id, e.booking_no, c.fabric_color, d.gsm_weight, f.id as trans_dtls_id, f.transfer_qnty
	from sample_development_mst a, sample_development_fabric_acc b,sample_development_rf_color c, wo_non_ord_samp_booking_dtls d, 
	wo_non_ord_samp_booking_mst e, inv_item_transfer_dtls f, inv_item_transfer_mst g, product_details_master h
	where a.entry_form_id in(203) and a.id=b.sample_mst_id and b.id=c.dtls_id and b.id=d.dtls_id and a.id=d.style_id 
	and d.booking_no=e.booking_no and a.is_deleted=0  and b.form_type=1
	and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1 
	and e.id=f.from_order_id and f.mst_id=g.id and g.entry_form=306 and g.transfer_criteria in (7,8) and f.from_prod_id=h.id
	and d.gsm_weight=h.gsm and d.lib_yarn_count_deter_id=h.DETARMINATION_ID and (h.color = c.fabric_color or h.color = c.color_id) $txt_date $company_name $buyer_name $req_no_cond  $year_cond $from_store_cond $from_floor_cond $from_room_cond $from_rack_cond $from_shelf_cond");

	foreach($sql_finish_trans_out as $row)
	{
		if($row[csf('fabric_color')])
		{
			$fabric_color =$row[csf('fabric_color')];
		}
		else
		{
			$fabric_color =$row[csf('garments_color')];
		}
		$string = $row[csf('deter_id')] .'**'. $fabric_color .'**'. $row[csf('gsm_weight')];
		if($trans_out_dtls_check[$row[csf('trans_dtls_id')]]=="")
		{
			$trans_out_dtls_check[$row[csf('trans_dtls_id')]]=$row[csf('trans_dtls_id')];
			$data_tr_out_array[$row[csf('buyer_name')]][$row[csf('requ_no')]][$string]['trans_out_qnty'] += $row[csf('transfer_qnty')];
		}
	}
	unset($sql_finish_trans_in);

	$sql_cutting_issue=sql_select("SELECT a.company_id, a.requisition_number as requ_no,to_char(e.booking_date,'YYYY') as requ_year,e.booking_date, a.buyer_name, a.style_ref_no,d.id, 
	d.finish_fabric, d.lib_yarn_count_deter_id as deter_id, c.color_id as garments_color, b.delivery_date, 
	b.fabric_description, d.id as booking_dtls_id, e.id as booking_id, e.booking_no, c.fabric_color, d.gsm_weight, g.id as iss_dtls_id, g.issue_qnty
	from sample_development_mst a, sample_development_fabric_acc b,sample_development_rf_color c, wo_non_ord_samp_booking_dtls d, 
	wo_non_ord_samp_booking_mst e, pro_batch_create_mst f, inv_finish_fabric_issue_dtls g, inv_issue_master h, product_details_master i
	where a.entry_form_id in(203) and a.id=b.sample_mst_id and b.id=c.dtls_id and b.id=d.dtls_id and a.id=d.style_id and d.booking_no=e.booking_no and a.is_deleted=0 and b.form_type=1 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1 and e.booking_no=f.booking_no and f.id=g.batch_id and g.mst_id=h.id and h.entry_form=18 and g.prod_id=i.id and d.gsm_weight=i.gsm and d.lib_yarn_count_deter_id=i.detarmination_id and (f.color_id = c.fabric_color or f.color_id = c.color_id) $txt_date $company_name $buyer_name $req_no_cond  $year_cond $g_store_cond $g_floor_cond $g_room_cond $g_rack_cond $g_shelf_cond");

	foreach($sql_cutting_issue as $row)
	{
		if($row[csf('fabric_color')])
		{
			$fabric_color =$row[csf('fabric_color')];
		}
		else
		{
			$fabric_color =$row[csf('garments_color')];
		}
		$string = $row[csf('deter_id')] .'**'. $fabric_color .'**'. $row[csf('gsm_weight')];
		if($issue_dtls_check[$row[csf('iss_dtls_id')]]=="")
		{
			$issue_dtls_check[$row[csf('iss_dtls_id')]]=$row[csf('iss_dtls_id')];
			$data_issue_array[$row[csf('buyer_name')]][$row[csf('requ_no')]][$string]['issue_qnty'] += $row[csf('issue_qnty')];
		}
	}
	unset($sql_cutting_issue);

	/* echo "<pre>";
	print_r($data_array);
	die; */

	ob_start();
	?>
	
	<div>
        <table cellpadding="0" cellspacing="0" width="1850">
            <tr  class="form_caption" style="border:none;">
           		 <td align="center" width="100%" colspan="18" style="font-size:20px"><strong><? echo 'Fabric Receive Status Without Order Report'; ?></strong></td>
            </tr>
            <tr  class="form_caption" style="border:none;">
                <td colspan="18" align="center" style="border:none; font-size:14px;">
                <b><? echo $company_library[$cbo_company_name]; ?></b>
                </td>
            </tr>
            <tr  class="form_caption" style="border:none;">
                <td align="center" width="100%" colspan="18" style="font-size:12px">
                <? if(str_replace("'","",$fromDate)!="" && str_replace("'","",$toDate)!="") echo "From ".change_date_format(str_replace("'","",$fromDate),'dd-mm-yyyy')." To ".change_date_format(str_replace("'","",$toDate),'dd-mm-yyyy')."" ;?>
                </td>
            </tr>
        </table>
		<table cellspacing="0" cellpadding="0" border="1" class="rpt_table" width="1630" rules="all" id="table_header" align="left">
			<thead>
				<tr>
					<th width="30" rowspan="2">Sl No</th>
					<th width="100" rowspan="2">Buyer Name</th>
					<th width="100" rowspan="2">Requisition No</th>
					<th width="100" rowspan="2">Year</th>
					<th width="100" rowspan="2">Style Ref.</th>
					<th width="100" rowspan="2">Fabric Delivery Date</th>
					<th width="100" rowspan="2">Fin. Fab Color</th>
					<th width="200" rowspan="2">Fabrication</th>
					<th width="100" rowspan="2">GSM</th>
					<th width="100" rowspan="2">Req. Qty</th>
					<th width="200" colspan="2">Total Received</th>
					<th width="100" rowspan="2">Receive Balance</th>
					<th width="200" colspan="2">Total Received</th>
					<th width="100" rowspan="2">Stock</th>
				</tr>
				<tr>
					<th width="100">Receive</th>
					<th width="100">Trans. In</th>
					<th width="100">Issue</th>
					<th width="100">Trans. Out</th>
				</tr>
			</thead>
		</table>
		<div style="max-height:320px; overflow-y:scroll; width:1650px; float:left" id="scroll_body" align="left">
			<table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="1630" rules="all" id="table_body" align="left">
				<tbody>
					<?
					$i=1; 
					$books_ar=array();
					$j=1;
					$book_rowspan="";
					$receive_qty=0;

					foreach ($data_array as $buyer_name=>$buyer_name_data)
					{
						asort($buyer_name_data);
						foreach ($buyer_name_data as $requ_year=>$requ_year_data)
						{
							foreach ($requ_year_data as $requ_no => $requ_no_data) 
							{
								foreach ($requ_no_data as $fabstring => $row) 
								{
									$fabstring_array = explode("**",$fabstring);
									$deter_id = $fabstring_array[0];
									$color_no = $fabstring_array[1];
									$gsm_weight = $fabstring_array[2];

									$trans_out_qnty = $data_tr_out_array[$buyer_name][$requ_no][$fabstring]['trans_out_qnty'];
									$issue_qnty = $data_issue_array[$buyer_name][$requ_no][$fabstring]['issue_qnty'];

									$finish_receive_balance = $row['fin_requ_qnty']-$row['rcv_qnty']-$row['trans_in_qnty'];
									$stock_qnty = $row['rcv_qnty']+$row['trans_in_qnty'] - ($issue_qnty + $trans_out_qnty);

									if($cbo_value_with ==1  || ($cbo_value_with ==2 && $stock_qnty !=0))
									{
										if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
										?>
										<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
											<td width="30" align="center"><? echo $i; ?></td>
											<td width="100"><? echo $buyer_arr[$row['buyer_name']]; ?></td>
											<td width="100"><? echo $row['requ_no']; ?></td> 
											<td width="100"><? echo $row['requ_year']; ?></td>
											<td width="100"><? echo $row['style_ref_no'] ; ?></td>
											
											<td width="100">
												<? 
												$delivery_date_arr = array_filter(array_unique(explode(",",chop($row['delivery_date'],","))));
												$delivery_dates="";
												foreach ($delivery_date_arr as $d_date) {
													$delivery_dates .= change_date_format($d_date).","; 
												}
												echo chop($delivery_dates,','); 
												?>
											</td>
											<td width="100"><? echo $color_arr[$color_no];?></td>
											<td width="200"><? echo $row['fabrication']; ?></td>
											<td width="100"><? echo $gsm_weight; ?></td>
											<td width="100" align="right">
												<a href="##" onclick="openmypage('required_popup','<? echo  $requ_no;?>',<? echo $deter_id;?>,<? echo $color_no;?>,<? echo $gsm_weight;?>)">
												<? echo number_format($row['fin_requ_qnty'],2); ?>
												</a>
											</td> 
											<td width="100" align="right">
												<a href="##" onclick="openmypage('receive_popup','<? echo  $requ_no;?>',<? echo $deter_id;?>,<? echo $color_no;?>,<? echo $gsm_weight;?>)">
												<? echo number_format($row['rcv_qnty'],2); ?>
												</a>
											</td>
											<td width="100" align="right">
												<a href="##" onclick="openmypage('trans_in_popup','<? echo  $requ_no;?>',<? echo $deter_id;?>,<? echo $color_no;?>,<? echo $gsm_weight;?>)">
												<? echo number_format($row['trans_in_qnty'],2); ?>
												</a>
											</td>
											<td width="100" align="right"><? echo number_format($finish_receive_balance,2); ?></td>
											<td width="100" align="right">
												<a href="##" onclick="openmypage('issue_popup','<? echo  $requ_no;?>',<? echo $deter_id;?>,<? echo $color_no;?>,<? echo $gsm_weight;?>)">
												<? echo number_format($issue_qnty,2); ?>
												</a>
											</td>
											<td width="100" align="right">
												<a href="##" onclick="openmypage('trans_out_popup','<? echo  $requ_no;?>',<? echo $deter_id;?>,<? echo $color_no;?>,<? echo $gsm_weight;?>)">
												<? echo number_format($trans_out_qnty,2); ?>
												</a>
											</td> 
											<td width="100" align="right">
												<a href="##" onclick="openmypage('stock_popup','<? echo  $requ_no;?>',<? echo $deter_id;?>,<? echo $color_no;?>,<? echo $gsm_weight;?>)">
												<? echo number_format($stock_qnty,2); ?>
												</a>
											</td>
										</tr>
										<?
										$i++;
										$total_fin_requ_qnty += $row['fin_requ_qnty'];
										$total_rcv_qnty += $row['rcv_qnty'];
										$total_trans_in_qnty += $row['trans_in_qnty'];
										$total_finish_receive_balance += $finish_receive_balance;
										$total_issue_qnty += $issue_qnty;
										$total_trans_out_qnty += $trans_out_qnty;
										$total_stock_qnty += $stock_qnty;
									}
								}
							}
						}
					}
					?>
					</tbody>
				</table>
			</div>
			<table width="1630" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table" align="left"> 
				<tfoot>
					<tr>
						<th width="30">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="200">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100" align="right"><? echo number_format($total_fin_requ_qnty,2);?></th>
						<th width="100" align="right"><? echo number_format($total_rcv_qnty,2);?></th>
						<th width="100" align="right"><? echo number_format($total_trans_in_qnty,2);?></th>
						<th width="100" align="right"><? echo number_format($total_finish_receive_balance,2);?></th>
						<th width="100" align="right"><? echo number_format($total_issue_qnty,2);?></th>
						<th width="100" align="right"><? echo number_format($total_trans_out_qnty,2);?></th>
						<th width="100" align="right"><? echo number_format($total_stock_qnty,2);?></th>
					</tr>
				</tfoot>
			</table>
		</div>
		<?
	foreach (glob("$user_name*.xls") as $filename) {
	if (@filemtime($filename) < (time() - $seconds_old))
		@unlink($filename);
	}
	//---------end------------//
	$name = time();
	$filename = $user_name . "_" . $name . ".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, ob_get_contents());
	//$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	}

	

if($action=="receive_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);

	$sql="SELECT a.requisition_number_prefix_num,to_char(a.insert_date,'YYYY') as requ_year, a.buyer_name, a.style_ref_no, a.internal_ref,
	c.color_id as garments_color, b.fabric_description, e.booking_no, c.fabric_color, g.id as rcv_dtls_id, g.receive_qnty
	,g.prod_id, h.recv_number, h.receive_date, f.batch_no, h.knitting_source, h.knitting_company, h.knitting_location_id, f.color_id, g.gsm, g.width, h.store_id, g.fabric_description_id
	from sample_development_mst a, sample_development_fabric_acc b,sample_development_rf_color c, wo_non_ord_samp_booking_dtls d, 
	wo_non_ord_samp_booking_mst e, pro_batch_create_mst f, pro_finish_fabric_rcv_dtls g, inv_receive_master h
	where a.entry_form_id in(203) and a.id=b.sample_mst_id and b.id=c.dtls_id and b.id=d.dtls_id and a.id=d.style_id 
	and d.booking_no=e.booking_no and a.is_deleted=0  and b.form_type=1 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 
	and e.is_deleted=0 and e.status_active=1 and e.booking_no=f.booking_no and f.id=g.batch_id and g.mst_id=h.id and h.entry_form=37 and d.gsm_weight=g.gsm and d.lib_yarn_count_deter_id=g.fabric_description_id 
	and (f.color_id=c.fabric_color or f.color_id=c.color_id) and g.status_active=1 and g.is_deleted=0 and a.requisition_number='$requ_no' and f.color_id=$color and d.lib_yarn_count_deter_id=$deter_id and d.gsm_weight=$gsm ";
	
	
	
	$result=sql_select($sql);
	foreach($result as $row)
	{
		if($rcvDtlsCheck[$row[csf('rcv_dtls_id')]]=="")
		{
			$rcvDtlsCheck[$row[csf('rcv_dtls_id')]]=$row[csf('rcv_dtls_id')];
			$buyer =$row[csf('buyer_name')];
			$requisition_number_prefix_num =$row[csf('requisition_number_prefix_num')];
			$style_ref_no =$row[csf('style_ref_no')];
			$requ_year =$row[csf('requ_year')];
			$internal_ref =$row[csf('internal_ref')];
			$fabric_description =$row[csf('fabric_description')];

			
		}

		if($row[csf('fabric_color')])
		{
			$book_color = $row[csf('fabric_color')];
		} 
		else if($row[csf('garments_color')])
		{
			$book_color = $row[csf('fabric_color')];
		}

		$all_deter_arr[$row[csf('fabric_description_id')]] = $row[csf('fabric_description_id')];
		
	}

	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier where status_active=1 and is_deleted=0",'id','supplier_name');
	$location_arr=return_library_array( "select id, location_name from lib_location where status_active=1 and is_deleted=0",'id','location_name');
	$store_arr=return_library_array( "select id, store_name from lib_store_location where status_active=1 and is_deleted=0",'id','store_name');
	?>
	<script>

		function print_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		
			d.close();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="230px";
		}	
		
	</script>
		<fieldset style="width:1130px;">
			<div id="report_container">
				<table border="1" class="rpt_table" rules="all" width="770" cellpadding="0" cellspacing="0">

					<thead>
						<tr>
							<th colspan="7">Style Details</th>
						</tr>
						<tr>
							<th width="100">Buyer</th>
							<th width="80">Req. No</th>
							<th width="80">Year</th>
							<th width="100">Style</th>
							<th width="100">Int. Ref.</th>
							<th width="100">Finish Fab.Color</th>
							<th width="100">Fabric Type</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><? echo $buyer_arr[$buyer]; ?></td>
							<td><? echo $requisition_number_prefix_num; ?></td>
							<td><? echo $requ_year; ?></td>
							<td><? echo $style_ref_no; ?></td>
							<td><? echo $internal_ref; ?></td>
							<td><? echo $color_arr[$book_color]; ?></td>
							<td><? echo $fabric_description; ?></td>
						</tr>
					</tbody>
					</table>
					<br>

				<table border="1" class="rpt_table" rules="all" width="1100" cellpadding="0" cellspacing="0">
					<thead>
						<th colspan="13"><b>Receive Details</b></th>
					</thead>
					<thead>
						<th width="30">SL</th>
						<th width="60">Product Id</th>
						<th width="100">Transaction ID</th>
						<th width="90">Transaction Date</th>
						<th width="100">Batch No</th>
						<th width="100">Service Company</th>
						<th width="100">Service Location</th>
						<th width="100">Batch Color</th>
						<th width="100">Fabric Des.</th>
						<th width="60">GSM</th>
						<th width="60">F.Dia</th>
						<th width="100">Quantity</th>
						<th width="100">Store</th>
					</thead>
					</table>
					<div style="width:1120px; max-height:330px; overflow-y:scroll" id="scroll_body">
						<table border="1" class="rpt_table" rules="all" width="1100" cellpadding="0" cellspacing="0">
						<?
						$i=1; $total_receive_qnty=0;
						//$product_arr=return_library_array( "select id,product_name_details from  product_details_master",'id','product_name_details'); 

						$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent, b.count_id, b.type_id from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0  and  b.status_active=1 and b.is_deleted=0 and a.id in (". implode(',',$all_deter_arr).")";
						$data_array=sql_select($sql_deter);
						foreach( $data_array as $row )
						{
							$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
							if($composition_arr[$row[csf('id')]]=="")
							{
								$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
							}
							else{
								$composition_arr[$row[csf('id')]].= " ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
							}
						}

						foreach($result as $row)
						{
							if($rcvDtlsCheck2[$row[csf('rcv_dtls_id')]]=="")
							{
								$rcvDtlsCheck2[$row[csf('rcv_dtls_id')]]=$row[csf('rcv_dtls_id')];
								if ($i%2==0)  
									$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";	
								
								if($row[csf('knitting_source')]==3)
								{
									$knitting_company = $supplier_arr[$row[csf('knitting_company')]];
								}else if($row[csf('knitting_source')]==1){
									$knitting_company = $company_arr[$row[csf('knitting_company')]];
								}
							?>
								<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
									<td width="30"><? echo $i; ?></td>
									<td width="60"><p><? echo $row[csf('prod_id')]; ?></p></td>
									<td width="100"><p><? echo $row[csf('recv_number')]; ?></p></td>
									<td width="90"><p><? echo change_date_format($row[csf('receive_date')]);; ?></p></td>
									<td width="100"><p><? echo $row[csf('batch_no')]; ?>&nbsp;</p></td>
									<td width="100"><p><? echo $knitting_company; ?></p></td>
									<td width="100"><? echo $location_arr[$row[csf('knitting_location_id')]]; ?></td>
									<td width="100"><? echo $color_arr[$row[csf('color_id')]]; ?></td>
									<td width="100"><? echo $constructtion_arr[$row[csf('fabric_description_id')]]." ".$composition_arr[$row[csf('fabric_description_id')]]; ?></td>
									<td width="60"><p><? echo $row[csf('gsm')]; ?>&nbsp;</p></td>
									<td width="60"><p><? echo $row[csf('width')]; ?>&nbsp;</p></td>
									
									<td align="right" width="100"><? echo number_format($row[csf('receive_qnty')],2,'.',''); ?></td>
									<td width="100"><p><? echo $store_arr[$row[csf('store_id')]]; ?>&nbsp;</p></td>
									</tr>
							<?
							$total_receive_qnty+=$row[csf('receive_qnty')];
							$i++;
							}
							
						}
					
						?>
						<tfoot>
							<th colspan="11" align="right">Total</th>
							<th align="right"><? echo number_format($total_receive_qnty,2,'.',''); ?></th>
							<th>&nbsp;</th>
						</tfoot>
					</table>
				</div>	
			</div>
		</fieldset>   
	<?
	exit();
}

if($action=="trans_in_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);

	$sql="SELECT a.requisition_number_prefix_num ,to_char(e.booking_date,'YYYY') as requ_year, a.buyer_name, a.style_ref_no, a.internal_ref,d.lib_yarn_count_deter_id as deter_id, c.color_id as garments_color, 
	b.fabric_description, c.fabric_color, d.gsm_weight, f.id as trans_dtls_id, f.to_prod_id, g.transfer_system_id, g.transfer_date, f.transfer_qnty, f.from_order_id, g.transfer_criteria, i.color_id, i.batch_no, h.dia_width, h.product_name_details, f.to_store
	from sample_development_mst a, sample_development_fabric_acc b,sample_development_rf_color c, wo_non_ord_samp_booking_dtls d, 
	wo_non_ord_samp_booking_mst e, inv_item_transfer_dtls f, inv_item_transfer_mst g, product_details_master h, pro_batch_create_mst i
	where a.entry_form_id in(203) and a.id=b.sample_mst_id and b.id=c.dtls_id and b.id=d.dtls_id and a.id=d.style_id 
	and d.booking_no=e.booking_no and a.is_deleted=0  and b.form_type=1 and f.to_batch_id=i.id
	and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1
	and e.id=f.to_order_id and f.mst_id=g.id and g.entry_form=306 and g.transfer_criteria in (6,8) and f.to_prod_id=h.id
	and d.gsm_weight=h.gsm and d.lib_yarn_count_deter_id=h.DETARMINATION_ID and (h.color = c.fabric_color or h.color = c.color_id) 
	and a.requisition_number='$requ_no' and f.color_id=$color and d.lib_yarn_count_deter_id=$deter_id and d.gsm_weight=$gsm";
	
	$result=sql_select($sql);
	foreach($result as $row)
	{
		if($rcvDtlsCheck[$row[csf('trans_dtls_id')]]=="")
		{
			$rcvDtlsCheck[$row[csf('trans_dtls_id')]]=$row[csf('trans_dtls_id')];
			$buyer =$row[csf('buyer_name')];
			$requisition_number_prefix_num =$row[csf('requisition_number_prefix_num')];
			$style_ref_no =$row[csf('style_ref_no')];
			$requ_year =$row[csf('requ_year')];
			$internal_ref =$row[csf('internal_ref')];
			$fabric_description =$row[csf('fabric_description')];

			
		}

		if($row[csf('fabric_color')])
		{
			$book_color = $row[csf('fabric_color')];
		} 
		else if($row[csf('garments_color')])
		{
			$book_color = $row[csf('fabric_color')];
		}

		if($row[csf('transfer_criteria')] ==6)
		{
			$from_order_arr[$row[csf('from_order_id')]] = $row[csf('from_order_id')];
		}else{
			$from_sample_arr[$row[csf('from_order_id')]] = $row[csf('from_order_id')];
		}

		$all_deter_array[$row[csf('deter_id')]] = $row[csf('deter_id')];
	}

	

	if(!empty($from_sample_arr))
	{
		$from_sample_inf_ref = return_library_array("SELECT e.booking_no, a.internal_ref from sample_development_mst a, wo_non_ord_samp_booking_dtls d, wo_non_ord_samp_booking_mst e where a.entry_form_id in(203)and a.id=d.style_id and d.booking_no=e.booking_no and a.is_deleted=0  
	and a.status_active=1 and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1 and e.id in (".implode(',',$from_sample_arr).") group by e.booking_no, a.internal_ref",'booking_no','internal_ref');
	}

	if(!empty($from_order_arr))
	{
		$from_order_inf_ref = return_library_array("SELECT id, grouping from wo_po_break_down where status_active in (1,3) and id in (".implode(',',$from_order_arr).")",'id','grouping');
	}

	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier where status_active=1 and is_deleted=0",'id','supplier_name');
	$location_arr=return_library_array( "select id, location_name from lib_location where status_active=1 and is_deleted=0",'id','location_name');
	$store_arr=return_library_array( "select id, store_name from lib_store_location where status_active=1 and is_deleted=0",'id','store_name');
	?>
	<script>

		function print_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		
			d.close();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="230px";
		}	
		
	</script>
		<fieldset style="width:1130px;">
			<div id="report_container">
				<table border="1" class="rpt_table" rules="all" width="770" cellpadding="0" cellspacing="0">

					<thead>
						<tr>
							<th colspan="7">Style Details</th>
						</tr>
						<tr>
							<th width="100">Buyer</th>
							<th width="80">Req. No</th>
							<th width="80">Year</th>
							<th width="100">Style</th>
							<th width="100">Int. Ref.</th>
							<th width="100">Finish Fab.Color</th>
							<th width="100">Fabric Type</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><? echo $buyer_arr[$buyer]; ?></td>
							<td><? echo $requisition_number_prefix_num; ?></td>
							<td><? echo $requ_year; ?></td>
							<td><? echo $style_ref_no; ?></td>
							<td><? echo $internal_ref; ?></td>
							<td><? echo $color_arr[$book_color]; ?></td>
							<td><? echo $fabric_description; ?></td>
						</tr>
					</tbody>
					</table>
					<br>

				<table border="1" class="rpt_table" rules="all" width="1100" cellpadding="0" cellspacing="0">
					<thead>
						<th colspan="13"><b>Transfer In Details</b></th>
					</thead>
					<thead>
						<th width="30">SL</th>
						<th width="60">Product Id</th>
						<th width="100">Transaction ID</th>
						<th width="90">Transaction Date</th>
						<th width="100">From Int. Ref</th>
						<th width="100">Batch No</th>
						<th width="100">Batch Color</th>
						<th width="100">Fabric Des.</th>
						<th width="60">GSM</th>
						<th width="60">F.Dia</th>
						<th width="100">Quantity</th>
						<th width="100">Store</th>
					</thead>
					</table>
					<div style="width:1120px; max-height:330px; overflow-y:scroll" id="scroll_body">
						<table border="1" class="rpt_table" rules="all" width="1100" cellpadding="0" cellspacing="0">
						<?
						$i=1; $total_receive_qnty=0;

						$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent, b.count_id, b.type_id from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0  and  b.status_active=1 and b.is_deleted=0 and a.id in (". implode(',',$all_deter_array).")";
						$deter_array=sql_select($sql_deter);
						foreach( $deter_array as $row )
						{
							$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
							if($composition_arr[$row[csf('id')]]=="")
							{
								$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
							}
							else{
								$composition_arr[$row[csf('id')]].= " ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
							}
						}

						foreach($result as $row)
						{
							if($rcvDtlsCheck2[$row[csf('trans_dtls_id')]]=="")
							{
								$rcvDtlsCheck2[$row[csf('trans_dtls_id')]]=$row[csf('trans_dtls_id')];
								if ($i%2==0)  
									$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";	
								
								if($row[csf('transfer_criteria')]==6)
								{
									$from_reference = $from_order_inf_ref[$row[csf('from_order_id')]];
								}
								else if($row[csf('transfer_criteria')]==8)
								{
									$from_reference = $from_sample_inf_ref[$row[csf('from_order_id')]];
								}
							?>
								<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
									<td width="30"><? echo $i; ?></td>
									<td width="60"><p><? echo $row[csf('to_prod_id')]; ?></p></td>
									<td width="100"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
									<td width="90"><p><? echo change_date_format($row[csf('transfer_date')]); ?></p></td>
									<td width="100"><p><? echo $from_reference; ?></p></td>
									<td width="100"><p><? echo $row[csf('batch_no')]; ?>&nbsp;</p></td>
									<td width="100"><? echo $color_arr[$row[csf('color_id')]]; ?></td>
									<td width="100"><? echo $constructtion_arr[$row[csf('deter_id')]]." ".$composition_arr[$row[csf('deter_id')]]; ?></td>
									<td width="60"><p><? echo $row[csf('gsm_weight')]; ?>&nbsp;</p></td>
									<td width="60"><p><? echo $row[csf('dia_width')]; ?>&nbsp;</p></td>
									
									<td align="right" width="100"><? echo number_format($row[csf('transfer_qnty')],2,'.',''); ?></td>
									<td width="100"><p><? echo $store_arr[$row[csf('to_store')]]; ?>&nbsp;</p></td>
									</tr>
							<?
							$total_trans_in_qnty+=$row[csf('transfer_qnty')];
							$i++;
							}
							
						}
					
						?>
						<tfoot>
							<th colspan="10" align="right">Total</th>
							<th align="right"><? echo number_format($total_trans_in_qnty,2,'.',''); ?></th>
							<th>&nbsp;</th>
						</tfoot>
					</table>
				</div>	
			</div>
		</fieldset>   
	<?
	exit();
}


if($action=="trans_out_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);


	$sql = "SELECT a.requisition_number_prefix_num,to_char(e.booking_date,'YYYY') as requ_year, a.buyer_name, a.style_ref_no, a.internal_ref, d.lib_yarn_count_deter_id as deter_id, c.color_id as garments_color, b.fabric_description, c.fabric_color, d.gsm_weight, f.id as trans_dtls_id, f.transfer_qnty, f.from_prod_id, f.to_order_id, g.transfer_criteria, g.transfer_system_id, g.transfer_date,i.color_id, i.batch_no, h.dia_width, f.from_store	
	from sample_development_mst a, sample_development_fabric_acc b,sample_development_rf_color c, wo_non_ord_samp_booking_dtls d, wo_non_ord_samp_booking_mst e, inv_item_transfer_dtls f, inv_item_transfer_mst g, product_details_master h, pro_batch_create_mst i
	where a.entry_form_id in(203) and a.id=b.sample_mst_id and b.id=c.dtls_id and b.id=d.dtls_id and a.id=d.style_id and d.booking_no=e.booking_no and a.is_deleted=0  and b.form_type=1 and f.batch_id=i.id and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1 and e.id=f.from_order_id and f.mst_id=g.id and g.entry_form=306 and g.transfer_criteria in (7,8) and f.from_prod_id=h.id and d.gsm_weight=h.gsm and d.lib_yarn_count_deter_id=h.detarmination_id and (h.color = c.fabric_color or h.color = c.color_id) and a.requisition_number='$requ_no' and f.color_id=$color and d.lib_yarn_count_deter_id=$deter_id and d.gsm_weight=$gsm";
	
	$result=sql_select($sql);
	foreach($result as $row)
	{
		if($rcvDtlsCheck[$row[csf('trans_dtls_id')]]=="")
		{
			$rcvDtlsCheck[$row[csf('trans_dtls_id')]]=$row[csf('trans_dtls_id')];
			$buyer =$row[csf('buyer_name')];
			$requisition_number_prefix_num =$row[csf('requisition_number_prefix_num')];
			$style_ref_no =$row[csf('style_ref_no')];
			$requ_year =$row[csf('requ_year')];
			$internal_ref =$row[csf('internal_ref')];
			$fabric_description =$row[csf('fabric_description')];

			
		}

		if($row[csf('fabric_color')])
		{
			$book_color = $row[csf('fabric_color')];
		} 
		else if($row[csf('garments_color')])
		{
			$book_color = $row[csf('fabric_color')];
		}

		if($row[csf('transfer_criteria')] ==7)
		{
			$to_order_arr[$row[csf('to_order_id')]] = $row[csf('to_order_id')];
		}else{
			$to_sample_arr[$row[csf('to_order_id')]] = $row[csf('to_order_id')];
		}

		$all_deter_array[$row[csf('deter_id')]] = $row[csf('deter_id')];
	}

	

	if(!empty($to_sample_arr))
	{
		$to_sample_inf_ref = return_library_array("SELECT e.booking_no, a.internal_ref from sample_development_mst a, wo_non_ord_samp_booking_dtls d, wo_non_ord_samp_booking_mst e where a.entry_form_id in(203)and a.id=d.style_id and d.booking_no=e.booking_no and a.is_deleted=0  
	and a.status_active=1 and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1 and e.id in (".implode(',',$to_sample_arr).") group by e.booking_no, a.internal_ref",'booking_no','internal_ref');
	}

	if(!empty($to_order_arr))
	{
		$to_order_inf_ref = return_library_array("SELECT id, grouping from wo_po_break_down where status_active in (1,3) and id in (".implode(',',$to_order_arr).")",'id','grouping');
	}

	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier where status_active=1 and is_deleted=0",'id','supplier_name');
	$location_arr=return_library_array( "select id, location_name from lib_location where status_active=1 and is_deleted=0",'id','location_name');
	$store_arr=return_library_array( "select id, store_name from lib_store_location where status_active=1 and is_deleted=0",'id','store_name');
	?>
	<script>

		function print_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		
			d.close();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="230px";
		}	
		
	</script>
		<fieldset style="width:1130px;">
			<div id="report_container">
				<table border="1" class="rpt_table" rules="all" width="770" cellpadding="0" cellspacing="0">

					<thead>
						<tr>
							<th colspan="7">Style Details</th>
						</tr>
						<tr>
							<th width="100">Buyer</th>
							<th width="80">Req. No</th>
							<th width="80">Year</th>
							<th width="100">Style</th>
							<th width="100">Int. Ref.</th>
							<th width="100">Finish Fab.Color</th>
							<th width="100">Fabric Type</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><? echo $buyer_arr[$buyer]; ?></td>
							<td><? echo $requisition_number_prefix_num; ?></td>
							<td><? echo $requ_year; ?></td>
							<td><? echo $style_ref_no; ?></td>
							<td><? echo $internal_ref; ?></td>
							<td><? echo $color_arr[$book_color]; ?></td>
							<td><? echo $fabric_description; ?></td>
						</tr>
					</tbody>
					</table>
					<br>

				<table border="1" class="rpt_table" rules="all" width="1100" cellpadding="0" cellspacing="0">
					<thead>
						<th colspan="13"><b>Transfer In Details</b></th>
					</thead>
					<thead>
						<th width="30">SL</th>
						<th width="60">Product Id</th>
						<th width="100">Transaction ID</th>
						<th width="90">Transaction Date</th>
						<th width="100">To Int. Ref</th>
						<th width="100">Batch No</th>
						<th width="100">Batch Color</th>
						<th width="100">Fabric Des.</th>
						<th width="60">GSM</th>
						<th width="60">F.Dia</th>
						<th width="100">Quantity</th>
						<th width="100">Store</th>
					</thead>
					</table>
					<div style="width:1120px; max-height:330px; overflow-y:scroll" id="scroll_body">
						<table border="1" class="rpt_table" rules="all" width="1100" cellpadding="0" cellspacing="0">
						<?
						$i=1; $total_receive_qnty=0;

						$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent, b.count_id, b.type_id from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0  and  b.status_active=1 and b.is_deleted=0 and a.id in (". implode(',',$all_deter_array).")";
						$deter_array=sql_select($sql_deter);
						foreach( $deter_array as $row )
						{
							$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
							if($composition_arr[$row[csf('id')]]=="")
							{
								$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
							}
							else{
								$composition_arr[$row[csf('id')]].= " ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
							}
						}

						foreach($result as $row)
						{
							if($rcvDtlsCheck2[$row[csf('trans_dtls_id')]]=="")
							{
								$rcvDtlsCheck2[$row[csf('trans_dtls_id')]]=$row[csf('trans_dtls_id')];
								if ($i%2==0)  
									$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";	
								
								if($row[csf('transfer_criteria')]==7)
								{
									$to_reference = $to_order_inf_ref[$row[csf('to_order_id')]];
								}
								else if($row[csf('transfer_criteria')]==8)
								{
									$to_reference = $to_sample_inf_ref[$row[csf('to_order_id')]];
								}
							?>
								<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
									<td width="30"><? echo $i; ?></td>
									<td width="60"><p><? echo $row[csf('from_prod_id')]; ?></p></td>
									<td width="100"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
									<td width="90"><p><? echo change_date_format($row[csf('transfer_date')]); ?></p></td>
									<td width="100"><p><? echo $to_reference; ?></p></td>
									<td width="100"><p><? echo $row[csf('batch_no')]; ?>&nbsp;</p></td>
									<td width="100"><? echo $color_arr[$row[csf('color_id')]]; ?></td>
									<td width="100"><? echo $constructtion_arr[$row[csf('deter_id')]]." ".$composition_arr[$row[csf('deter_id')]]; ?></td>
									<td width="60"><p><? echo $row[csf('gsm_weight')]; ?>&nbsp;</p></td>
									<td width="60"><p><? echo $row[csf('dia_width')]; ?>&nbsp;</p></td>
									
									<td align="right" width="100"><? echo number_format($row[csf('transfer_qnty')],2,'.',''); ?></td>
									<td width="100"><p><? echo $store_arr[$row[csf('from_store')]]; ?>&nbsp;</p></td>
									</tr>
							<?
							$total_trans_in_qnty+=$row[csf('transfer_qnty')];
							$i++;
							}
							
						}
					
						?>
						<tfoot>
							<th colspan="10" align="right">Total</th>
							<th align="right"><? echo number_format($total_trans_in_qnty,2,'.',''); ?></th>
							<th>&nbsp;</th>
						</tfoot>
					</table>
				</div>	
			</div>
		</fieldset>   
	<?
	exit();
}


if($action=="issue_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);

	$sql= "SELECT a.requisition_number_prefix_num,to_char(e.booking_date,'YYYY') as requ_year, a.buyer_name, a.style_ref_no, d.lib_yarn_count_deter_id as deter_id, c.color_id as garments_color, b.delivery_date, b.fabric_description,  c.fabric_color, d.gsm_weight, g.id as iss_dtls_id, g.issue_qnty ,g.store_id, h.issue_number, h.issue_date, h.knit_dye_source, h.knit_dye_company, h.location_id, f.color_id, f.batch_no, i.dia_width, g.prod_id
	from sample_development_mst a, sample_development_fabric_acc b,sample_development_rf_color c, wo_non_ord_samp_booking_dtls d, 
	wo_non_ord_samp_booking_mst e, pro_batch_create_mst f, inv_finish_fabric_issue_dtls g, inv_issue_master h, product_details_master i
	where a.entry_form_id in(203) and a.id=b.sample_mst_id and b.id=c.dtls_id and b.id=d.dtls_id and a.id=d.style_id and d.booking_no=e.booking_no and a.is_deleted=0 and b.form_type=1 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1 and e.booking_no=f.booking_no and f.id=g.batch_id and g.mst_id=h.id and h.entry_form=18 and g.prod_id=i.id and d.gsm_weight=i.gsm and d.lib_yarn_count_deter_id=i.detarmination_id and (f.color_id = c.fabric_color or f.color_id = c.color_id) 
	and a.requisition_number='$requ_no' and f.color_id=$color and d.lib_yarn_count_deter_id=$deter_id and d.gsm_weight=$gsm";
	
	
	
	$result=sql_select($sql);
	foreach($result as $row)
	{
		if($rcvDtlsCheck[$row[csf('rcv_dtls_id')]]=="")
		{
			$rcvDtlsCheck[$row[csf('rcv_dtls_id')]]=$row[csf('rcv_dtls_id')];
			$buyer =$row[csf('buyer_name')];
			$requisition_number_prefix_num =$row[csf('requisition_number_prefix_num')];
			$style_ref_no =$row[csf('style_ref_no')];
			$requ_year =$row[csf('requ_year')];
			$internal_ref =$row[csf('internal_ref')];
			$fabric_description =$row[csf('fabric_description')];

			
		}

		if($row[csf('fabric_color')])
		{
			$book_color = $row[csf('fabric_color')];
		} 
		else if($row[csf('garments_color')])
		{
			$book_color = $row[csf('fabric_color')];
		}

		$all_deter_arr[$row[csf('deter_id')]] = $row[csf('deter_id')];
		
	}

	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier where status_active=1 and is_deleted=0",'id','supplier_name');
	$location_arr=return_library_array( "select id, location_name from lib_location where status_active=1 and is_deleted=0",'id','location_name');
	$store_arr=return_library_array( "select id, store_name from lib_store_location where status_active=1 and is_deleted=0",'id','store_name');
	?>
	<script>

		function print_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		
			d.close();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="230px";
		}	
		
	</script>
		<fieldset style="width:1130px;">
			<div id="report_container">
				<table border="1" class="rpt_table" rules="all" width="770" cellpadding="0" cellspacing="0">

					<thead>
						<tr>
							<th colspan="7">Style Details</th>
						</tr>
						<tr>
							<th width="100">Buyer</th>
							<th width="80">Req. No</th>
							<th width="80">Year</th>
							<th width="100">Style</th>
							<th width="100">Int. Ref.</th>
							<th width="100">Finish Fab.Color</th>
							<th width="100">Fabric Type</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><? echo $buyer_arr[$buyer]; ?></td>
							<td><? echo $requisition_number_prefix_num; ?></td>
							<td><? echo $requ_year; ?></td>
							<td><? echo $style_ref_no; ?></td>
							<td><? echo $internal_ref; ?></td>
							<td><? echo $color_arr[$book_color]; ?></td>
							<td><? echo $fabric_description; ?></td>
						</tr>
					</tbody>
					</table>
					<br>

				<table border="1" class="rpt_table" rules="all" width="1100" cellpadding="0" cellspacing="0">
					<thead>
						<th colspan="13"><b>Receive Details</b></th>
					</thead>
					<thead>
						<th width="30">SL</th>
						<th width="60">Product Id</th>
						<th width="100">Transaction ID</th>
						<th width="90">Transaction Date</th>
						<th width="100">Batch No</th>
						<th width="100">Service Company</th>
						<th width="100">Service Location</th>
						<th width="100">Batch Color</th>
						<th width="100">Fabric Des.</th>
						<th width="60">GSM</th>
						<th width="60">F.Dia</th>
						<th width="100">Quantity</th>
						<th width="100">Store</th>
					</thead>
					</table>
					<div style="width:1120px; max-height:330px; overflow-y:scroll" id="scroll_body">
						<table border="1" class="rpt_table" rules="all" width="1100" cellpadding="0" cellspacing="0">
						<?
						$i=1; $total_receive_qnty=0;
						//$product_arr=return_library_array( "select id,product_name_details from  product_details_master",'id','product_name_details'); 

						$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent, b.count_id, b.type_id from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0  and  b.status_active=1 and b.is_deleted=0 and a.id in (". implode(',',$all_deter_arr).")";
						$data_array=sql_select($sql_deter);
						foreach( $data_array as $row )
						{
							$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
							if($composition_arr[$row[csf('id')]]=="")
							{
								$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
							}
							else{
								$composition_arr[$row[csf('id')]].= " ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
							}
						}

						foreach($result as $row)
						{
							if($rcvDtlsCheck2[$row[csf('rcv_dtls_id')]]=="")
							{
								$rcvDtlsCheck2[$row[csf('rcv_dtls_id')]]=$row[csf('rcv_dtls_id')];
								if ($i%2==0)  
									$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";	
								
								if($row[csf('knit_dye_source')]==3)
								{
									$knitting_company = $supplier_arr[$row[csf('knit_dye_company')]];
								}else if($row[csf('knit_dye_source')]==1){
									$knitting_company = $company_arr[$row[csf('knit_dye_company')]];
								}
							?>
								<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
									<td width="30"><? echo $i; ?></td>
									<td width="60"><p><? echo $row[csf('prod_id')]; ?></p></td>
									<td width="100"><p><? echo $row[csf('issue_number')]; ?></p></td>
									<td width="90"><p><? echo change_date_format($row[csf('issue_date')]);; ?></p></td>
									<td width="100"><p><? echo $row[csf('batch_no')]; ?>&nbsp;</p></td>
									<td width="100"><p><? echo $knitting_company; ?></p></td>
									<td width="100"><? echo $location_arr[$row[csf('location_id')]]; ?></td>
									<td width="100"><? echo $color_arr[$row[csf('color_id')]]; ?></td>
									<td width="100"><? echo $constructtion_arr[$row[csf('deter_id')]]." ".$composition_arr[$row[csf('deter_id')]]; ?></td>
									<td width="60"><p><? echo $row[csf('gsm_weight')]; ?>&nbsp;</p></td>
									<td width="60"><p><? echo $row[csf('dia_width')]; ?>&nbsp;</p></td>
									
									<td align="right" width="100"><? echo number_format($row[csf('issue_qnty')],2,'.',''); ?></td>
									<td width="100"><p><? echo $store_arr[$row[csf('store_id')]]; ?>&nbsp;</p></td>
									</tr>
							<?
							$total_issue_qnty+=$row[csf('issue_qnty')];
							$i++;
							}
							
						}
					
						?>
						<tfoot>
							<th colspan="11" align="right">Total</th>
							<th align="right"><? echo number_format($total_issue_qnty,2,'.',''); ?></th>
							<th>&nbsp;</th>
						</tfoot>
					</table>
				</div>	
			</div>
		</fieldset>   
	<?
	exit();
}


if($action=="stock_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$rcv_sql = "SELECT a.requisition_number_prefix_num,to_char(a.insert_date,'YYYY') as requ_year, a.buyer_name, a.style_ref_no, a.internal_ref, c.color_id as garments_color, b.fabric_description, c.fabric_color, g.id as rcv_dtls_id, h.store_id, g.room, g.rack_no, g.shelf_no, g.prod_id, f.batch_no, g.receive_qnty
	from sample_development_mst a, sample_development_fabric_acc b,sample_development_rf_color c, wo_non_ord_samp_booking_dtls d, wo_non_ord_samp_booking_mst e, pro_batch_create_mst f, pro_finish_fabric_rcv_dtls g, inv_receive_master h
	where a.entry_form_id in(203) and a.id=b.sample_mst_id and b.id=c.dtls_id and b.id=d.dtls_id and a.id=d.style_id and d.booking_no=e.booking_no and a.is_deleted=0  and b.form_type=1 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1 and e.booking_no=f.booking_no and f.id=g.batch_id and g.mst_id=h.id and h.entry_form=37 and d.gsm_weight=g.gsm and d.lib_yarn_count_deter_id=g.fabric_description_id and (f.color_id=c.fabric_color or f.color_id=c.color_id) and g.status_active=1 and g.is_deleted=0 and a.requisition_number='$requ_no' and f.color_id=$color and d.lib_yarn_count_deter_id=$deter_id and d.gsm_weight=$gsm ";
	
	$result=sql_select($rcv_sql);
	foreach($result as $row)
	{
		if($rcvDtlsCheck[$row[csf('rcv_dtls_id')]]=="")
		{
			$rcvDtlsCheck[$row[csf('rcv_dtls_id')]]=$row[csf('rcv_dtls_id')];
			$buyer =$row[csf('buyer_name')];
			$requisition_number_prefix_num =$row[csf('requisition_number_prefix_num')];
			$style_ref_no =$row[csf('style_ref_no')];
			$requ_year =$row[csf('requ_year')];
			$internal_ref =$row[csf('internal_ref')];
			$fabric_description =$row[csf('fabric_description')];

			$store_room_rack_shelf_string = $row[csf('store_id')].'*'.$row[csf('room')].'*'.$row[csf('rack_no')].'*'.$row[csf('shelf_no')];
			$all_rcv_trans_data[$row[csf('prod_id')]][$row[csf('batch_no')]][$store_room_rack_shelf_string]['rcv'] +=$row[csf('receive_qnty')];
		}

		if($row[csf('fabric_color')])
		{
			$book_color = $row[csf('fabric_color')];
		} 
		else if($row[csf('garments_color')])
		{
			$book_color = $row[csf('fabric_color')];
		}
	}

	$trans_in_sql="SELECT a.requisition_number_prefix_num ,to_char(e.booking_date,'YYYY') as requ_year, a.buyer_name, a.style_ref_no, a.internal_ref, c.color_id as garments_color, 
	b.fabric_description, c.fabric_color, f.id as trans_dtls_id, f.to_prod_id, i.batch_no, f.to_store, f.to_room, f.to_rack, f.to_shelf, f.transfer_qnty
	from sample_development_mst a, sample_development_fabric_acc b,sample_development_rf_color c, wo_non_ord_samp_booking_dtls d, 
	wo_non_ord_samp_booking_mst e, inv_item_transfer_dtls f, inv_item_transfer_mst g, product_details_master h, pro_batch_create_mst i
	where a.entry_form_id in(203) and a.id=b.sample_mst_id and b.id=c.dtls_id and b.id=d.dtls_id and a.id=d.style_id 
	and d.booking_no=e.booking_no and a.is_deleted=0  and b.form_type=1 and f.to_batch_id=i.id
	and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1
	and e.id=f.to_order_id and f.mst_id=g.id and g.entry_form=306 and g.transfer_criteria in (6,8) and f.to_prod_id=h.id
	and d.gsm_weight=h.gsm and d.lib_yarn_count_deter_id=h.DETARMINATION_ID and (h.color = c.fabric_color or h.color = c.color_id) 
	and a.requisition_number='$requ_no' and f.color_id=$color and d.lib_yarn_count_deter_id=$deter_id and d.gsm_weight=$gsm";
	
	$trans_in_result=sql_select($trans_in_sql);
	foreach($trans_in_result as $row)
	{
		if($trasnInDtlsCheck[$row[csf('trans_dtls_id')]]=="")
		{
			$trasnInDtlsCheck[$row[csf('trans_dtls_id')]]=$row[csf('trans_dtls_id')];
			$buyer =$row[csf('buyer_name')];
			$requisition_number_prefix_num =$row[csf('requisition_number_prefix_num')];
			$style_ref_no =$row[csf('style_ref_no')];
			$requ_year =$row[csf('requ_year')];
			$internal_ref =$row[csf('internal_ref')];
			$fabric_description =$row[csf('fabric_description')];

			$store_room_rack_shelf_string = $row[csf('to_store')].'*'.$row[csf('to_room')].'*'.$row[csf('to_rack')].'*'.$row[csf('to_shelf')];
			$all_rcv_trans_data[$row[csf('to_prod_id')]][$row[csf('batch_no')]][$store_room_rack_shelf_string]['trans_in'] +=$row[csf('transfer_qnty')];
		}

		if($row[csf('fabric_color')])
		{
			$book_color = $row[csf('fabric_color')];
		} 
		else if($row[csf('garments_color')])
		{
			$book_color = $row[csf('fabric_color')];
		}
	}

	$issue_sql= "SELECT g.id as iss_dtls_id, g.issue_qnty ,g.store_id, f.batch_no, g.prod_id, g.store_id, g.room, g.rack_no, g.shelf_no
	from sample_development_mst a, sample_development_fabric_acc b,sample_development_rf_color c, wo_non_ord_samp_booking_dtls d, 
	wo_non_ord_samp_booking_mst e, pro_batch_create_mst f, inv_finish_fabric_issue_dtls g, inv_issue_master h, product_details_master i
	where a.entry_form_id in(203) and a.id=b.sample_mst_id and b.id=c.dtls_id and b.id=d.dtls_id and a.id=d.style_id and d.booking_no=e.booking_no and a.is_deleted=0 and b.form_type=1 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1 and e.booking_no=f.booking_no and f.id=g.batch_id and g.mst_id=h.id and h.entry_form=18 and g.prod_id=i.id and d.gsm_weight=i.gsm and d.lib_yarn_count_deter_id=i.detarmination_id and (f.color_id = c.fabric_color or f.color_id = c.color_id) 
	and a.requisition_number='$requ_no' and f.color_id=$color and d.lib_yarn_count_deter_id=$deter_id and d.gsm_weight=$gsm";

	$issue_result=sql_select($issue_sql);
	foreach($issue_result as $row)
	{
		if($issDtlsCheck[$row[csf('iss_dtls_id')]]=="")
		{
			$issDtlsCheck[$row[csf('iss_dtls_id')]]=$row[csf('iss_dtls_id')];
			$store_room_rack_shelf_string = $row[csf('store_id')].'*'.$row[csf('room')].'*'.$row[csf('rack_no')].'*'.$row[csf('shelf_no')];
			$all_rcv_trans_data[$row[csf('prod_id')]][$row[csf('batch_no')]][$store_room_rack_shelf_string]['issue'] +=$row[csf('issue_qnty')];
			
		}
	}


	$trans_out_sql = "SELECT f.id as trans_dtls_id, f.from_prod_id, i.batch_no, f.from_store, f.room,f.rack, f.shelf, f.transfer_qnty	
	from sample_development_mst a, sample_development_fabric_acc b,sample_development_rf_color c, wo_non_ord_samp_booking_dtls d, wo_non_ord_samp_booking_mst e, inv_item_transfer_dtls f, inv_item_transfer_mst g, product_details_master h, pro_batch_create_mst i
	where a.entry_form_id in(203) and a.id=b.sample_mst_id and b.id=c.dtls_id and b.id=d.dtls_id and a.id=d.style_id and d.booking_no=e.booking_no and a.is_deleted=0  and b.form_type=1 and f.batch_id=i.id and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1 and e.id=f.from_order_id and f.mst_id=g.id and g.entry_form=306 and g.transfer_criteria in (7,8) and f.from_prod_id=h.id and d.gsm_weight=h.gsm and d.lib_yarn_count_deter_id=h.detarmination_id and (h.color = c.fabric_color or h.color = c.color_id) and a.requisition_number='$requ_no' and f.color_id=$color and d.lib_yarn_count_deter_id=$deter_id and d.gsm_weight=$gsm";
	
	$trans_out_result=sql_select($trans_out_sql);
	foreach($trans_out_result as $row)
	{
		if($transOutDtlsCheck[$row[csf('trans_dtls_id')]]=="")
		{
			$transOutDtlsCheck[$row[csf('trans_dtls_id')]]=$row[csf('trans_dtls_id')];
			$store_room_rack_shelf_string = $row[csf('from_store')].'*'.$row[csf('room')].'*'.$row[csf('rack')].'*'.$row[csf('shelf')];
			$all_rcv_trans_data[$row[csf('from_prod_id')]][$row[csf('batch_no')]][$store_room_rack_shelf_string]['trans_out'] +=$row[csf('transfer_qnty')];
		}
	}

	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier where status_active=1 and is_deleted=0",'id','supplier_name');
	$room_rack_name_arr=return_library_array( "select floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst where status_active=1 and is_deleted=0",'floor_room_rack_id','floor_room_rack_name');
	$store_arr=return_library_array( "select id, store_name from lib_store_location where status_active=1 and is_deleted=0",'id','store_name');
	?>
	<script>

		function print_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		
			d.close();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="230px";
		}	
		
	</script>
		<fieldset style="width:1130px;">
			<div id="report_container">
				<table border="1" class="rpt_table" rules="all" width="770" cellpadding="0" cellspacing="0">

					<thead>
						<tr>
							<th colspan="7">Style Details</th>
						</tr>
						<tr>
							<th width="100">Buyer</th>
							<th width="80">Req. No</th>
							<th width="80">Year</th>
							<th width="100">Style</th>
							<th width="100">Int. Ref.</th>
							<th width="100">Finish Fab.Color</th>
							<th width="100">Fabric Type</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><? echo $buyer_arr[$buyer]; ?></td>
							<td><? echo $requisition_number_prefix_num; ?></td>
							<td><? echo $requ_year; ?></td>
							<td><? echo $style_ref_no; ?></td>
							<td><? echo $internal_ref; ?></td>
							<td><? echo $color_arr[$book_color]; ?></td>
							<td><? echo $fabric_description; ?></td>
						</tr>
					</tbody>
					</table>
					<br>

				<table border="1" class="rpt_table" rules="all" width="690" cellpadding="0" cellspacing="0">
					<thead>
						<th colspan="8"><b>Receive Details</b></th>
					</thead>
					<thead>
						<th width="30">SL</th>
						<th width="60">Product Id</th>
						<th width="100">Batch No</th>
						<th width="100">Room</th>
						<th width="100">Rack</th>
						<th width="100">Shelf</th>
						<th width="100">Quantity</th>
						<th width="100">Store</th>
					</thead>
					</table>
					<div style="width:710px; max-height:330px; overflow-y:scroll" id="scroll_body">
						<table border="1" class="rpt_table" rules="all" width="690" cellpadding="0" cellspacing="0">
						<?
						$i=1; $total_receive_qnty=0;

						foreach($all_rcv_trans_data as $prod_id=>$prod_data)
						{
							foreach ($prod_data as $batch_no => $batch_data) 
							{
								foreach ($batch_data as $room_rack_str => $row) 
								{
									$room_rack_arr = explode("*",$room_rack_str);
									$store_id= $room_rack_arr[0];
									$room_id= $room_rack_arr[1];
									$rack_id= $room_rack_arr[2];
									$shelf_id= $room_rack_arr[3];

									if ($i%2==0)  
										$bgcolor="#E9F3FF";
									else
										$bgcolor="#FFFFFF";	


									$stock = $row['rcv'] +$row['trans_in'] - ( $row['issue'] +$row['trans_out']);
									//echo $row['rcv'] ."+".$row['trans_in'] ."- (". $row['issue'] ."+". $row['trans_out'].")<br>";
									
								?>
									<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
										<td width="30"><? echo $i; ?></td>
										<td width="60"><p><? echo $prod_id; ?></p></td>
										<td width="100"><p><? echo $batch_no; ?></p></td>
										<td width="90"><p><? echo $room_rack_name_arr[$room_id]; ?></p></td>
										<td width="100"><p><? echo $room_rack_name_arr[$rack_id]; ?>&nbsp;</p></td>
										<td width="100"><? echo $room_rack_name_arr[$shelf_id]; ?></td>
										
										<td align="right" width="100"><? echo number_format($stock,2,'.',''); ?></td>
										<td width="100"><p><? echo $store_arr[$store_id]; ?>&nbsp;</p></td>
									</tr>
								<?
								$total_stock_qnty+=$stock;
								$i++;
								}
							}
						}
					
						?>
						<tfoot>
							<th colspan="6" align="right">Total</th>
							<th align="right"><? echo number_format($total_stock_qnty,2,'.',''); ?></th>
							<th>&nbsp;</th>
						</tfoot>
					</table>
				</div>	
			</div>
		</fieldset>   
	<?
	exit();
}

if($action=="required_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);

	$sql="SELECT a.requisition_number as requ_no,to_char(e.booking_date,'YYYY') as requ_year,e.booking_date, a.buyer_name, a.style_ref_no, a.internal_ref,
	d.finish_fabric, d.grey_fabric, d.lib_yarn_count_deter_id as deter_id, c.color_id as garments_color, b.delivery_date, b.fabric_description, d.id as booking_dtls_id, e.booking_no, c.fabric_color, d.gsm_weight
	from sample_development_mst a, sample_development_fabric_acc b,sample_development_rf_color c, wo_non_ord_samp_booking_dtls d, wo_non_ord_samp_booking_mst e
	where a.entry_form_id in(203) and a.id=b.sample_mst_id and b.id=c.dtls_id and b.id=d.dtls_id and a.id=d.style_id and d.booking_no=e.booking_no and a.is_deleted=0 and b.form_type=1 and a.status_active=1 
	and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1 
	and a.requisition_number='$requ_no' and d.gsm_weight=$gsm and d.lib_yarn_count_deter_id=$deter_id and (c.fabric_color=$color or c.color_id=$color)";
	
	$result=sql_select($sql);

	?>
	<script>

		function print_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		
			d.close();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="230px";
		}	
		
	</script>
		<fieldset style="width:1130px;">
			<div id="report_container">
				<table border="1" class="rpt_table" rules="all" width="730" cellpadding="0" cellspacing="0">
					<thead>
						<th colspan="7"><b>Receive Details</b></th>
					</thead>
					<thead>
						<th width="30">SL</th>
						<th width="150">Int. Ref.</th>
						<th width="100">Booking Year</th>
						<th width="150">Booking No</th>
						<th width="100">Booking Type</th>
						<th width="100">Grey Qty. Kg</th>
						<th width="100">Fin Qty. Kg</th>
					</thead>
					</table>
					<div style="width:750px; max-height:330px; overflow-y:scroll" id="scroll_body">
						<table border="1" class="rpt_table" rules="all" width="730" cellpadding="0" cellspacing="0">
						<?
						$i=1; $total_receive_qnty=0;

						foreach($result as $row)
						{
							if($rcvDtlsCheck2[$row[csf('booking_dtls_id')]]=="")
							{
								$rcvDtlsCheck2[$row[csf('booking_dtls_id')]]=$row[csf('booking_dtls_id')];
								if ($i%2==0)  
									$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";	
								
							?>
								<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
									<td width="30"><? echo $i; ?></td>
									<td width="150"><p><? echo $row[csf('internal_ref')]; ?></p></td>
									<td width="100"><p><? echo $row[csf('requ_year')]; ?></p></td>
									<td width="150"><p><? echo $row[csf('booking_no')]; ?></p></td>
									<td width="100"><p><? echo "Sample"; ?>&nbsp;</p></td>
									<td width="100" align="right"><p><? echo $row[csf('grey_fabric')]; ?>&nbsp;</p></td>
									<td width="100" align="right"><p><? echo $row[csf('finish_fabric')]; ?>&nbsp;</p></td>
									
									</tr>
							<?
							$total_grey_fabric+=$row[csf('grey_fabric')];
							$total_finish_fabric+=$row[csf('finish_fabric')];
							$i++;
							}
							
						}
					
						?>
						<tfoot>
							<th colspan="5" align="right">Total</th>
							<th align="right"><? echo number_format($total_grey_fabric,2,'.',''); ?></th>
							<th align="right"><? echo number_format($total_finish_fabric,2,'.',''); ?></th>
						</tfoot>
					</table>
				</div>	
			</div>
		</fieldset>   
	<?
	exit();
}



?>