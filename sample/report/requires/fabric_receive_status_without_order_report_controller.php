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
$dealing_arr=return_library_array( "select id, team_member_name from lib_mkt_team_member_info where status_active=1 and is_deleted=0",'id','team_member_name');
$sample_name_arr=return_library_array( "select id,sample_name  from lib_sample where is_deleted=0 and status_active=1 order by sample_name", "id", "sample_name"  );
$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
$size_arr=return_library_array( "select id, size_name from lib_size where status_active=1 and is_deleted=0","id","size_name");
$season_arr=return_library_array( "select id, season_name from lib_buyer_season where status_active=1 and is_deleted=0","id","season_name");

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 110, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in ($data) and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select --", $selected, "" );
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
			else if(type==3)
			{
				document.getElementById('search_by_th').innerHTML="Booking No";
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
                    <input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( '<? echo $cbo_company_name;?>' +'_'+document.getElementById('cbo_buyer_name').value +'_'+document.getElementById('cbo_search_by').value+'_'+ document.getElementById('txt_common_search').value, 'create_requisition_id_search_list_view', 'search_div', 'fabric_receive_status_without_order_report_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:80px;" />
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $fabric_nature; ?>'+'**'+'<? echo $libyarncountdeterminationid; ?>'+'**'+document.getElementById('txt_construction').value+'**'+document.getElementById('txt_gsm_weight').value, 'fabric_description_popup_search_list_view', 'search_div', 'fabric_receive_status_without_order_report_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:100px;" />
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
	$cbo_company_name=str_replace("'", "", $cbo_company_name);
	$sample_year=str_replace("'", "", $cbo_year);
	$search_type=str_replace("'", "", $search_type);
	$txt_gsm=str_replace("'", "", $txt_gsm);
	$txt_fabric_id=str_replace("'", "", $txt_fabric_id);
	$year_cond="";
	if($db_type==2)
	{
		$year_cond=($sample_year)? " and  to_char(e.booking_date,'YYYY')=$sample_year" : " ";
	}
	else
	{
		$year_cond=($sample_year)? " and year(e.booking_date)=$sample_year" : " ";
	}

	if($req_no !="")
	{
		$req_no =  "'".implode("','",explode(",",$req_no))."'";
		$req_no_cond =" and a.requisition_number in ($req_no) ";
	}

	if(str_replace("'","",$cbo_company_name)==0) $company_name=""; else $company_name=" and a.company_id in ($cbo_company_name)";
	if(str_replace("'","",$cbo_buyer_name)==0) $buyer_name="";else $buyer_name=" and a.buyer_name=$cbo_buyer_name";

	$txt_date="";
	if(str_replace("'","",trim($txt_date_from))!="" && str_replace("'","",trim($txt_date_to))!="")
	{
		if($search_type==1)
		{
			$txt_date=" and e.booking_date between $txt_date_from and $txt_date_to";
		}
		else{
			$txt_date=" and b.delivery_date between $txt_date_from and $txt_date_to";
		}

	}

	if($txt_fabric_id)
	{
		$fabrication_cond = " and d.lib_yarn_count_deter_id=".$txt_fabric_id;
	}

	if($txt_gsm)
	{
		$gsm_cond = " and d.gsm_weight=".$txt_gsm;
	}

	if(str_replace("'","",$txt_req_no)=="") $req_no=""; else $req_no=" and a.requisition_number like '%$req_no%' ";

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
	

	$query= "SELECT a.company_id, a.id as requisition_id, a.requisition_number as requ_no,to_char(e.booking_date,'YYYY') as booking_year,e.booking_date, a.buyer_name, a.style_ref_no,d.id, d.grey_fabric, d.finish_fabric, b.gmts_item_id, d.lib_yarn_count_deter_id as deter_id, c.color_id as garments_color, b.delivery_date, b.fabric_description, d.id as booking_dtls_id, e.id as booking_id, e.booking_no, c.fabric_color
	from sample_development_mst a, sample_development_dtls f, sample_development_fabric_acc b,sample_development_rf_color c, wo_non_ord_samp_booking_dtls d, wo_non_ord_samp_booking_mst e 
	where a.entry_form_id in(203) and a.id=b.sample_mst_id and a.id=f.sample_mst_id and f.gmts_item_id=b.gmts_item_id and f.sample_name = b.sample_name and b.id=c.dtls_id and b.id=d.dtls_id and a.id=d.style_id and d.booking_no=e.booking_no and a.is_deleted=0  and b.form_type=1 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1 $txt_date $company_name $buyer_name $req_no_cond  $year_cond $fabrication_cond $gsm_cond
	group by a.company_id, a.id, a.requisition_number, a.buyer_name, a.style_ref_no, d.finish_fabric, b.gmts_item_id,c.color_id, d.id, d.grey_fabric, d.lib_yarn_count_deter_id, b.delivery_date, b.fabric_description, e.booking_date, e.id, e.booking_no, c.fabric_color
	order by a.company_id, e.id,b.delivery_date asc, c.color_id,b.fabric_description ";
	//echo $query;//die;

	$sql=sql_select($query);
	
	$data_array=array();
	$buyer_summary_arr=array();
	foreach($sql as $row)
	{
		$fabric_color="";
		if($row[csf('fabric_color')])
		{
			$fabric_color =$row[csf('fabric_color')];
		}
		else
		{
			$fabric_color =$row[csf('garments_color')];
		}
		
		//$string = $constructtion_arr[$row[csf('deter_id')]].'**'.$composition_arr[$row[csf('deter_id')]].'**'.$fabric_color;
		$string = $row[csf('deter_id')].'**'.$fabric_color;
		$data_array[$row[csf('booking_year')]][$row[csf('requ_no')]][$string]['requ_no']= $row[csf('requ_no')];
		$data_array[$row[csf('booking_year')]][$row[csf('requ_no')]][$string]['requisition_id']= $row[csf('requisition_id')];
		$data_array[$row[csf('booking_year')]][$row[csf('requ_no')]][$string]['company_id']= $row[csf('company_id')];
		$data_array[$row[csf('booking_year')]][$row[csf('requ_no')]][$string]['booking_year']= $row[csf('booking_year')];
		$data_array[$row[csf('booking_year')]][$row[csf('requ_no')]][$string]['booking_no']= $row[csf('booking_no')];
		$data_array[$row[csf('booking_year')]][$row[csf('requ_no')]][$string]['booking_id']= $row[csf('booking_id')];
		$data_array[$row[csf('booking_year')]][$row[csf('requ_no')]][$string]['booking_date']= $row[csf('booking_date')];
		$data_array[$row[csf('booking_year')]][$row[csf('requ_no')]][$string]['buyer_name']= $row[csf('buyer_name')];
		$data_array[$row[csf('booking_year')]][$row[csf('requ_no')]][$string]['style_ref_no']= $row[csf('style_ref_no')];
		$data_array[$row[csf('booking_year')]][$row[csf('requ_no')]][$string]['delivery_date']= $row[csf('delivery_date')];
		$data_array[$row[csf('booking_year')]][$row[csf('requ_no')]][$string]['gmts_item_id'] .= $garments_item[$row[csf('gmts_item_id')]].",";
		$data_array[$row[csf('booking_year')]][$row[csf('requ_no')]][$string]['count'] = $yarn_count_arr[$row[csf('deter_id')]];
		$data_array[$row[csf('booking_year')]][$row[csf('requ_no')]][$string]['type'] = $yarn_type_arr[$row[csf('deter_id')]];
		$data_array[$row[csf('booking_year')]][$row[csf('requ_no')]][$string]['construction'] = $constructtion_arr[$row[csf('deter_id')]];
		$data_array[$row[csf('booking_year')]][$row[csf('requ_no')]][$string]['composition'] = $composition_arr[$row[csf('deter_id')]];
		$data_array[$row[csf('booking_year')]][$row[csf('requ_no')]][$string]['fabric_description'] .= $row[csf('fabric_description')].",";

		if($booking_dtls_check[$row[csf('booking_dtls_id')]]=="")
		{
			$booking_dtls_check[$row[csf('booking_dtls_id')]]=$row[csf('booking_dtls_id')];
			$data_array[$row[csf('booking_year')]][$row[csf('requ_no')]][$string]['requ_qnty'] += $row[csf('grey_fabric')];
			$data_array[$row[csf('booking_year')]][$row[csf('requ_no')]][$string]['fin_requ_qnty'] += $row[csf('finish_fabric')];

			$buyer_summary_arr[$row[csf('buyer_name')]]['grey_requ_qnty'] +=$row[csf('grey_fabric')];
			$buyer_summary_arr[$row[csf('buyer_name')]]['fin_requ_qnty'] +=$row[csf('finish_fabric')];
			$buyer_summary_arr[$row[csf('buyer_name')]]['buyer_name'] =$row[csf('buyer_name')];
		}

		if(!$booking_id_check[$row[csf('booking_id')]])
		{
			$booking_id_check[$row[csf('booking_id')]]=$row[csf('booking_id')];
			$BOOKINGID = $row[csf('booking_id')];
			$BOOKINGNO = $row[csf('booking_no')];
			$rID=execute_query("insert into tmp_booking_id (userid, booking_id,booking_no,type) values ($user_name,$BOOKINGID,'$BOOKINGNO',99)");
		}
		


	}
	if($rID)
	{
		oci_commit($con);
	}

	/* echo "<pre>";
	print_r($data_array);
	die; */
	/* $sql_yarn_issue=sql_select("SELECT a.booking_id, c.buyer_id, sum(b.cons_quantity) as issue_qty,min(a.issue_date) as issue_date from tmp_booking_id x, inv_issue_master a, inv_transaction b, wo_non_ord_samp_booking_mst c  where x.booking_id=a.booking_id and x.userid=$user_name and x.type=99 and a.id=b.mst_id and a.issue_basis=1 and a.issue_purpose=8 and a.item_category=1 and b.transaction_type=2 and a.entry_form=3 and x.booking_id=c.id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.booking_id>0 and a.company_id in ($cbo_company_name) and x.booking_no=a.booking_no group by a.booking_id, c.buyer_id
	union all
	SELECT e.id as booking_id, e.buyer_id, sum(b.cons_quantity) as issue_qty,min(a.issue_date) as issue_date 
	from inv_issue_master a, inv_transaction b, ppl_yarn_requisition_entry c,ppl_planning_entry_plan_dtls d,wo_non_ord_samp_booking_mst e, tmp_booking_id x 
	where a.id=b.mst_id and b.requisition_no=c.requisition_no and c.knit_id=d.dtls_id and d.booking_no=e.booking_no and e.id=x.booking_id  and x.userid=$user_name and x.type=99 and c.prod_id=b.prod_id and a.issue_basis=3 and a.issue_purpose=1 and a.item_category=1 and b.transaction_type=2 and a.entry_form=3 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.company_id in ($cbo_company_name) and x.booking_no=d.booking_no group by e.id, e.buyer_id"); */

	$sql_yarn_issue=sql_select("SELECT a.booking_id, c.buyer_id, d.yarn_comp_type1st, d.yarn_count_id, d.yarn_type, sum(b.cons_quantity) as issue_qty,min(a.issue_date) as issue_date 
	from tmp_booking_id x, inv_issue_master a, inv_transaction b, wo_non_ord_samp_booking_mst c, product_details_master d 
	where x.booking_id=a.booking_id and x.userid=$user_name and x.type=99 and a.id=b.mst_id and a.issue_basis=1 and a.issue_purpose=8 and a.item_category=1 and b.transaction_type=2 and a.entry_form=3 and x.booking_id=c.id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.booking_id>0 and a.company_id in (6) and x.booking_no=a.booking_no and b.prod_id=d.id
	group by a.booking_id, c.buyer_id, d.yarn_comp_type1st, d.yarn_count_id, d.yarn_type
	union all 
	SELECT e.id as booking_id, e.buyer_id, f.yarn_comp_type1st, f.yarn_count_id, f.yarn_type, sum(b.cons_quantity) as issue_qty,min(a.issue_date) as issue_date 
	from inv_issue_master a, inv_transaction b, ppl_yarn_requisition_entry c,ppl_planning_entry_plan_dtls d,wo_non_ord_samp_booking_mst e, tmp_booking_id x, product_details_master f 
	where a.id=b.mst_id and b.requisition_no=c.requisition_no and c.knit_id=d.dtls_id and d.booking_no=e.booking_no and e.id=x.booking_id and x.userid=$user_name and x.type=99 and c.prod_id=b.prod_id and a.issue_basis=3 and a.issue_purpose=1 and a.item_category=1 and b.transaction_type=2 and a.entry_form=3 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.company_id in (6) and x.booking_no=d.booking_no and b.prod_id=f.id
	group by e.id, e.buyer_id, f.yarn_comp_type1st, f.yarn_count_id, f.yarn_type");


	foreach($sql_yarn_issue as $row)
	{
		//$yarn_issue_arr[$row[csf("booking_id")]] +=$row[csf("issue_qty")];

		$yarn_issue_arr[$row[csf("booking_id")]] +=$row[csf("issue_qty")];

		$buyer_summary_arr[$row[csf('buyer_id')]]['yarn_issue'] +=$row[csf('issue_qty')];
	}

	$sql_yarn_issue_rtn=sql_select("SELECT a.booking_id, c.buyer_id, sum(b.cons_quantity) as issue_rtn_qty from tmp_booking_id x,inv_receive_master a, inv_transaction b, wo_non_ord_samp_booking_mst c where x.booking_id=a.booking_id and x.userid=$user_name and x.type=99 and a.id=b.mst_id and a.receive_basis=1 and a.item_category=1 and b.transaction_type=4 and a.entry_form=9 and a.booking_id>0 and a.booking_without_order=1 and x.booking_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.booking_id, c.buyer_id
	union all 
	SELECT c.order_id as booking_id, d.buyer_id, sum(b.cons_quantity) as issue_rtn_qty 
	from inv_receive_master a, inv_transaction b, ppl_yarn_requisition_breakdown c, wo_non_ord_samp_booking_mst d, tmp_booking_id x  
	where a.id=b.mst_id 
	and a.booking_id=c.requisition_id and c.item_id=b.prod_id and c.order_id=d.id and c.order_id=x.booking_id and x.userid=$user_name and x.type=99 and a.receive_basis=3 and a.item_category=1 and b.transaction_type=4 and a.entry_form=9 and a.booking_id>0 and a.booking_without_order=1  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
	group by c.order_id, d.buyer_id");

	foreach($sql_yarn_issue_rtn as $row)
	{
		$yarn_issue_rtn_arr[$row[csf("booking_id")]]=$row[csf("issue_rtn_qty")];
		$buyer_summary_arr[$row[csf('buyer_id')]]['yarn_issue_ret'] +=$row[csf('issue_rtn_qty')];
	}

	$sql_grey_knit_production=sql_select("SELECT a.booking_id, c.buyer_id, b.febric_description_id, b.color_id, sum(b.grey_receive_qnty) as receive_qty, max(a.receive_date) as receive_date 
	from tmp_booking_id x, inv_receive_master a, pro_grey_prod_entry_dtls b, wo_non_ord_samp_booking_mst c 
	where x.booking_id=a.booking_id and x.userid=$user_name and x.type=99 and a.id=b.mst_id and a.receive_basis=1 and a.booking_without_order=1 and a.entry_form=2 and a.booking_id>0 and x.booking_id=c.id 
	group by a.booking_id, c.buyer_id, b.febric_description_id, b.color_id
	union all 
	SELECT e.id as booking_id, e.buyer_id, b.febric_description_id, b.color_id, sum(b.grey_receive_qnty) as receive_qty, max(a.receive_date) as receive_date 
	from inv_receive_master a, pro_grey_prod_entry_dtls b, ppl_planning_entry_plan_dtls c, wo_non_ord_samp_booking_mst e,tmp_booking_id x 
	where a.id=b.mst_id and a.booking_id=c.dtls_id and c.booking_no=e.booking_no and e.id=x.booking_id and x.userid=$user_name and x.type=99 
	and a.receive_basis=2 and a.booking_without_order=1 and a.entry_form=2 and a.booking_id>0 group by e.id, e.buyer_id, b.febric_description_id, b.color_id ");

	$prod_booking_id_arr=array();
	foreach($sql_grey_knit_production as $row)
	{
		//$grey_knit_production_arr[$row[csf("booking_id")]][$constructtion_arr[$row[csf('febric_description_id')]].'**'.$composition_arr[$row[csf('febric_description_id')]].'**'.$row[csf('color_id')]]+=$row[csf("receive_qty")];

		$grey_knit_production_arr[$row[csf("booking_id")]][$row[csf('febric_description_id')].'**'.$row[csf('color_id')]]+=$row[csf("receive_qty")];
		$grey_knit_production_date[$row[csf("booking_id")]]=$row[csf("receive_date")];
		$buyer_summary_arr[$row[csf('buyer_id')]]['grey_production'] +=$row[csf('receive_qty')];
	}

	$sql_gray_delivery=sql_select("SELECT a.booking_id, e.buyer_id, d.febric_description_id, d.color_id, sum(b.current_delivery) as current_stock 
	from tmp_booking_id x, inv_receive_master a, pro_grey_prod_delivery_dtls b, pro_grey_prod_delivery_mst c, pro_grey_prod_entry_dtls d, wo_non_ord_samp_booking_mst e
	where x.booking_id=a.booking_id and x.userid=$user_name and x.type=99 and c.id=b.mst_id and a.entry_form=2 and c.entry_form in(53,56) and b.grey_sys_id=a.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_without_order=1 and b.sys_dtls_id=d.id and x.booking_id=e.id
	group by a.booking_id, e.buyer_id, d.febric_description_id, d.color_id 
	union all 
	select e.id as booking_id, e.buyer_id, f.febric_description_id, f.color_id, sum(b.current_delivery) as current_stock 
	from inv_receive_master a, pro_grey_prod_delivery_dtls b, pro_grey_prod_delivery_mst c, ppl_planning_entry_plan_dtls d, wo_non_ord_samp_booking_mst e,tmp_booking_id x, pro_grey_prod_entry_dtls f
	where c.id=b.mst_id and a.entry_form=2 and c.entry_form in(56) and b.grey_sys_id=a.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_id=d.dtls_id and d.booking_no=e.booking_no and e.id=x.booking_id and x.userid=$user_name and x.type=99 and a.booking_without_order=1 and b.sys_dtls_id=f.id
	group by e.id, e.buyer_id, f.febric_description_id, f.color_id");

	$grey_prod_booking_array=array();$all_delivery_id="";$delivery_book_id=array();
	foreach($sql_gray_delivery as $row)
	{
		//$grey_delivery_stock[$row[csf("booking_id")]][$constructtion_arr[$row[csf('febric_description_id')]] .'**'. $composition_arr[$row[csf('febric_description_id')]].'**'.$row[csf('color_id')]]+=$row[csf("current_stock")];
		
		$grey_delivery_stock[$row[csf("booking_id")]][$row[csf('febric_description_id')].'**'.$row[csf('color_id')]]+=$row[csf("current_stock")];

		$buyer_summary_arr[$row[csf('buyer_id')]]['grey_production'] +=$row[csf('current_stock')];
	}
	unset($sql_gray_delivery);


	$sql_grey_purchase=sql_select("SELECT a.booking_id, d.buyer_id, c.febric_description_id, c.color_id, sum( b.cons_quantity) as receive_qty
	from inv_receive_master a, inv_transaction b, pro_grey_prod_entry_dtls c, tmp_booking_id x, wo_non_ord_samp_booking_mst d 
	where a.id=b.mst_id and b.id=c.trans_id and a.id=c.mst_id and a.booking_without_order=1 and a.entry_form in(22) 
	and a.receive_basis in(2) and b.transaction_type in(1) and a.booking_id>0 and a.booking_id=x.booking_id and x.userid=$user_name and x.type=99 and x.booking_id=d.id group by a.booking_id, d.buyer_id, c.febric_description_id, c.color_id");

	foreach($sql_grey_purchase as $row)
	{
		//$grey_knit_purchase_arr[$row[csf("booking_id")]][$constructtion_arr[$row[csf('febric_description_id')]] .'**'. $composition_arr[$row[csf('febric_description_id')]].'**'.$row[csf('color_id')]]+=$row[csf("receive_qty")];
		$grey_knit_purchase_arr[$row[csf("booking_id")]][$row[csf('febric_description_id')].'**'.$row[csf('color_id')]]+=$row[csf("receive_qty")];
		$buyer_summary_arr[$row[csf('buyer_id')]]['grey_receive'] +=$row[csf('receive_qty')];
	}
	unset($sql_grey_purchase);

	$sql_grey_rcv=sql_select("SELECT c.po_breakdown_id as booking_id, d.buyer_id, b.febric_description_id, b.color_id, sum( c.qnty) as receive_qty
	from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c,  tmp_booking_id x, wo_non_ord_samp_booking_mst d 
	where a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and c.booking_without_order=1 and a.entry_form in(58) and c.entry_form in(58) 
	and c.po_breakdown_id=x.booking_id and x.userid=$user_name and x.type=99 and x.booking_id=d.id
	group by c.po_breakdown_id, d.buyer_id, b.febric_description_id, b.color_id");

	foreach($sql_grey_rcv as $row)
	{
		//$grey_knit_recv_arr[$row[csf("booking_id")]][$constructtion_arr[$row[csf('febric_description_id')]] .'**'. $composition_arr[$row[csf('febric_description_id')]].'**'.$row[csf('color_id')]]+=$row[csf("receive_qty")];

		$grey_knit_recv_arr[$row[csf("booking_id")]][$row[csf('febric_description_id')].'**'.$row[csf('color_id')]]+=$row[csf("receive_qty")];

		$buyer_summary_arr[$row[csf('buyer_id')]]['grey_receive'] +=$row[csf('receive_qty')];
	}
	unset($sql_grey_rcv);

	$sql_trans_in = sql_select("SELECT b.po_breakdown_id, d.buyer_id, e.febric_description_id, e.color_id, sum(b.qnty) as tranfer_in
	from  pro_roll_details b, inv_item_transfer_dtls c, tmp_booking_id x, wo_non_ord_samp_booking_mst d, pro_roll_details a, pro_grey_prod_entry_dtls e where b.dtls_id=c.id and c.item_category=13 and b.entry_form in (180,110) and b.booking_without_order=1 
	and b.po_breakdown_id=x.booking_id and x.userid=$user_name and x.type=99 and x.booking_id= d.id and b.barcode_no=a.barcode_no and a.entry_form=2 and a.dtls_id=e.id group by b.po_breakdown_id, d.buyer_id, e.febric_description_id, e.color_id");

	foreach($sql_trans_in as $row)
	{
		//$sample_transectionArr[$row[csf("po_breakdown_id")]][$constructtion_arr[$row[csf('febric_description_id')]] .'**'. $composition_arr[$row[csf('febric_description_id')]].'**'.$row[csf('color_id')]]['tranfer_in'] += $row[csf("tranfer_in")];
		$sample_transectionArr[$row[csf("po_breakdown_id")]][$row[csf('febric_description_id')].'**'.$row[csf('color_id')]]['tranfer_in'] += $row[csf("tranfer_in")];

		$buyer_summary_arr[$row[csf('buyer_id')]]['grey_tranfer_in'] +=$row[csf('tranfer_in')];
	}
	unset($sql_trans_in);
	
	$sql_trans_out = sql_select("SELECT b.order_id, d.buyer_id, g.febric_description_id, g.color_id,
	sum(e.qnty) as tranfer_out from inv_transaction b, inv_item_transfer_dtls c, tmp_booking_id x, wo_non_ord_samp_booking_mst d, 
	pro_roll_details e, pro_roll_details f,pro_grey_prod_entry_dtls g 
	where b.id=c.trans_id and b.order_id=x.booking_id and x.userid=$user_name and x.type=99 
	and b.item_category=13 and b.transaction_type in(6) and x.booking_id=d.id and c.id=e.dtls_id and e.entry_form in (180,183) and e.barcode_no=f.barcode_no and f.entry_form=2 and f.dtls_id=g.id
	group by b.order_id, d.buyer_id, g.febric_description_id, g.color_id");

	foreach($sql_trans_out as $row)
	{
		//$sample_transectionArr[$row[csf("order_id")]][$constructtion_arr[$row[csf('febric_description_id')]] .'**'. $composition_arr[$row[csf('febric_description_id')]].'**'.$row[csf('color_id')]]['tranfer_out'] += $row[csf("tranfer_out")];
		$sample_transectionArr[$row[csf("order_id")]][$row[csf('febric_description_id')].'**'.$row[csf('color_id')]]['tranfer_out'] += $row[csf("tranfer_out")];
		$buyer_summary_arr[$row[csf('buyer_id')]]['grey_tranfer_out'] +=$row[csf('tranfer_out')];
	}
	unset($sql_trans_out);

	$sql_grey_issue=sql_select("SELECT a.detarmination_id, d.buyer_id, b.color_id, c.po_breakdown_id as booking_id, sum(c.qnty) as issue_qty
	from product_details_master a, inv_grey_fabric_issue_dtls b, pro_roll_details c, tmp_booking_id x, wo_non_ord_samp_booking_mst d
	where a.id=b.prod_id and b.id=c.dtls_id and x.booking_id=c.po_breakdown_id and x.userid=$user_name and x.type=99  
	and c.entry_form in(61) and c.booking_without_order=1 and c.status_active=1 and x.booking_id=d.id group by c.po_breakdown_id, d.buyer_id, a.detarmination_id, b.color_id");

	foreach($sql_grey_issue as $row)
	{
		//$grey_issue_arr[$row[csf("booking_id")]][$constructtion_arr[$row[csf('detarmination_id')]] .'**'. $composition_arr[$row[csf('detarmination_id')]].'**'.$row[csf('color_id')]]+=$row[csf("issue_qty")];
		$grey_issue_arr[$row[csf("booking_id")]][$row[csf('detarmination_id')].'**'.$row[csf('color_id')]]+=$row[csf("issue_qty")];
		$buyer_summary_arr[$row[csf('buyer_id')]]['grey_issue'] +=$row[csf('issue_qty')];
	}
	unset($sql_grey_issue);

	
	$sql_batch_qty=sql_select("SELECT a.id, a.batch_no, a.booking_no_id, d.buyer_id, c.detarmination_id, a.color_id, sum(b.batch_qnty) as batch_qnty
	from tmp_booking_id x, pro_batch_create_mst a, pro_batch_create_dtls b, product_details_master c, wo_non_ord_samp_booking_mst d 
	where  x.booking_id=a.booking_no_id and x.userid=$user_name and x.type=99 and  a.id=b.mst_id and b.prod_id=c.id and a.booking_without_order=1 and x.booking_id=d.id
	group by a.id,a.batch_no,a.booking_no_id ,c.detarmination_id,a.color_id, d.buyer_id ");

	foreach($sql_batch_qty as $row)
	{
		//$batch_qty_arr[$row[csf("booking_no_id")]][$constructtion_arr[$row[csf('detarmination_id')]] .'**'. $composition_arr[$row[csf('detarmination_id')]].'**'.$row[csf('color_id')]]['batch_qnty']+=$row[csf("batch_qnty")];
		$batch_qty_arr[$row[csf("booking_no_id")]][$row[csf('detarmination_id')].'**'.$row[csf('color_id')]]['batch_qnty']+=$row[csf("batch_qnty")];

		$buyer_summary_arr[$row[csf('buyer_id')]]['batch_qnty'] +=$row[csf('batch_qnty')];
	}
	unset($sql_batch_qty);

	$sql_dyeing_qty=sql_select("SELECT a.id, a.booking_no_id, b.buyer_id, e.detarmination_id, a.color_id, sum(d.batch_qty) as dyeing_qnty
	from tmp_booking_id x,pro_batch_create_mst a, pro_fab_subprocess c, pro_fab_subprocess_dtls d, product_details_master e, wo_non_ord_samp_booking_mst b
	where x.booking_id=a.booking_no_id and x.userid=$user_name and x.type=99 and c.batch_id=a.id and c.id=d.mst_id and a.booking_without_order=1 and c.load_unload_id=2  and d.status_active=1 and c.status_active=1 and d.prod_id=e.id and x.booking_id=b.id
	group by a.id, a.booking_no_id, b.buyer_id, e.detarmination_id, a.color_id ");
	foreach($sql_dyeing_qty as $row)
	{
		//$dyeing_qty_arr[$row[csf("booking_no_id")]][$constructtion_arr[$row[csf('detarmination_id')]] .'**'. $composition_arr[$row[csf('detarmination_id')]].'**'.$row[csf('color_id')]]['dyeing_qnty']+=$row[csf("dyeing_qnty")];

		$dyeing_qty_arr[$row[csf("booking_no_id")]][$row[csf('detarmination_id')].'**'.$row[csf('color_id')]]['dyeing_qnty']+=$row[csf("dyeing_qnty")];

		$buyer_summary_arr[$row[csf('buyer_id')]]['dyeing_qnty'] +=$row[csf('dyeing_qnty')];
	}
	unset($sql_dyeing_qty);

	$sql_fin_prod_qty=sql_select("SELECT a.receive_basis, d.id as booking_id, d.buyer_id, b.fabric_description_id, b.color_id, a.entry_form, sum(b.receive_qnty) as production_qty
	from inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_batch_create_mst c, wo_non_ord_samp_booking_mst d, tmp_booking_id x
	where a.id=b.mst_id and b.batch_id=c.id and c.booking_no=d.booking_no and a.entry_form in (7,37)
	and x.booking_id=d.id and x.userid=$user_name and x.type=99
	group by a.receive_basis, d.id, d.buyer_id, b.fabric_description_id, b.color_id, a.entry_form");

	foreach($sql_fin_prod_qty as $row)
	{
		if($row[csf("entry_form")]==7)
		{
			//$fin_prod_qty_arr[$row[csf("booking_id")]][$constructtion_arr[$row[csf('fabric_description_id')]] .'**'. $composition_arr[$row[csf('fabric_description_id')]].'**'.$row[csf('color_id')]]['fin_prod_qnty']+=$row[csf("production_qty")];
			$fin_prod_qty_arr[$row[csf("booking_id")]][$row[csf('fabric_description_id')].'**'.$row[csf('color_id')]]['fin_prod_qnty']+=$row[csf("production_qty")];
		}
		else if($row[csf("entry_form")]==37 && $row[csf("receive_basis")]==9)
		{
			//$fin_prod_qty_arr[$row[csf("booking_id")]][$constructtion_arr[$row[csf('fabric_description_id')]] .'**'. $composition_arr[$row[csf('fabric_description_id')]].'**'.$row[csf('color_id')]]['fin_rcv_production_qnty']+=$row[csf("production_qty")];
			$fin_prod_qty_arr[$row[csf("booking_id")]][$row[csf('fabric_description_id')].'**'.$row[csf('color_id')]]['fin_rcv_production_qnty']+=$row[csf("production_qty")];
			$buyer_summary_arr[$row[csf('buyer_id')]]['fin_rcv_production_qnty'] +=$row[csf('production_qty')];
		}
		else if($row[csf("entry_form")]==37 && $row[csf("receive_basis")]!=9)
		{
			//$fin_prod_qty_arr[$row[csf("booking_id")]][$constructtion_arr[$row[csf('fabric_description_id')]] .'**'. $composition_arr[$row[csf('fabric_description_id')]].'**'.$row[csf('color_id')]]['fin_rcv_purchase_qnty']+=$row[csf("production_qty")];
			$fin_prod_qty_arr[$row[csf("booking_id")]][$row[csf('fabric_description_id')].'**'.$row[csf('color_id')]]['fin_rcv_purchase_qnty']+=$row[csf("production_qty")];
			$buyer_summary_arr[$row[csf('buyer_id')]]['fin_rcv_purchase_qnty'] +=$row[csf('production_qty')];
		}
	}
	unset($sql_fin_prod_qty);

	$sql_finish_prodction_delivery=sql_select("SELECT c.booking_no_id, b.determination_id, b.color_id, sum(b.current_delivery) as delivery_qty 
	from pro_grey_prod_delivery_mst a,pro_grey_prod_delivery_dtls b, pro_batch_create_mst c,tmp_booking_id x  
	where a.id=b.mst_id and b.batch_id=c.id and c.booking_no_id=x.booking_id and x.userid=$user_name and x.type=99 and  a.entry_form=54  and b.entry_form=54 and c.batch_against=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by c.booking_no_id, b.determination_id, b.color_id ");
	foreach($sql_finish_prodction_delivery as $row)
	{
		//$finish_deli_qty_arr[$row[csf("booking_no_id")]][$constructtion_arr[$row[csf('determination_id')]] .'**'. $composition_arr[$row[csf('determination_id')]].'**'.$row[csf('color_id')]]=$row[csf("delivery_qty")];
		$finish_deli_qty_arr[$row[csf("booking_no_id")]][$row[csf('determination_id')].'**'.$row[csf('color_id')]]=$row[csf("delivery_qty")];
	}
	unset($sql_finish_prodction_delivery);
	
	$sql_finish_trans_in=sql_select("SELECT b.to_order_id as booking_id, d.buyer_id, c.detarmination_id, c.color, b.transfer_qnty as to_trans_qnty
from inv_item_transfer_mst a, inv_item_transfer_dtls b, product_details_master c, tmp_booking_id x, wo_non_ord_samp_booking_mst d
where a.id=b.mst_id and a.entry_form=306 and a.transfer_criteria in (6,8) and b.to_prod_id=c.id and b.to_order_id=x.booking_id and x.userid=$user_name and x.type=99 and b.status_active=1 and b.is_deleted=0 and x.booking_id=d.id");

	foreach($sql_finish_trans_in as $row)
	{
		//$finish_trans_qty_arr[$row[csf("booking_id")]][$constructtion_arr[$row[csf('detarmination_id')]] .'**'. $composition_arr[$row[csf('detarmination_id')]].'**'.$row[csf('color')]]['trans_in']=$row[csf("to_trans_qnty")];
		$finish_trans_qty_arr[$row[csf("booking_id")]][$row[csf('detarmination_id')].'**'.$row[csf('color')]]['trans_in']=$row[csf("to_trans_qnty")];

		$buyer_summary_arr[$row[csf('buyer_id')]]['fin_trans_in'] +=$row[csf('to_trans_qnty')];
	}
	unset($sql_finish_trans_in);

	$sql_finish_trans_out=sql_select("SELECT b.from_order_id as booking_id, d.buyer_id, c.detarmination_id, c.color, b.transfer_qnty as from_trans_qnty
from inv_item_transfer_mst a, inv_item_transfer_dtls b, product_details_master c, tmp_booking_id x, wo_non_ord_samp_booking_mst d
where a.id=b.mst_id and a.entry_form=306 and a.transfer_criteria in (7,8) and b.from_prod_id=c.id and b.from_order_id=x.booking_id and x.userid=$user_name and x.type=99 and x.booking_id=d.id and b.status_active=1 and b.is_deleted=0");

	foreach($sql_finish_trans_out as $row)
	{
		//$finish_trans_qty_arr[$row[csf("booking_id")]][$constructtion_arr[$row[csf('detarmination_id')]] .'**'. $composition_arr[$row[csf('detarmination_id')]].'**'.$row[csf('color')]]['trans_out']=$row[csf("from_trans_qnty")];
		$finish_trans_qty_arr[$row[csf("booking_id")]][$row[csf('detarmination_id')].'**'.$row[csf('color')]]['trans_out']=$row[csf("from_trans_qnty")];

		$buyer_summary_arr[$row[csf('buyer_id')]]['fin_trans_out'] +=$row[csf('from_trans_qnty')];
	}
	unset($sql_finish_trans_in);


	$sql_cutting_issue=sql_select("SELECT c.booking_no_id, d.buyer_id, e.detarmination_id, c.color_id, sum(b.issue_qnty) as issue_qty 
	from inv_issue_master a, inv_finish_fabric_issue_dtls b, pro_batch_create_mst c, wo_non_ord_samp_booking_mst d, product_details_master e, tmp_booking_id x 
	where a.id=b.mst_id and b.batch_id=c.id and a.entry_form=18 and c.booking_no_id>0 and c.booking_without_order=1 and c.booking_no = d.booking_no and b.prod_id=e.id and d.booking_no=x.booking_no and c.booking_no_id=x.booking_id and x.userid=$user_name and x.type=99 and b.status_active=1 and b.is_deleted=0 group by c.booking_no_id, d.buyer_id, e.detarmination_id, c.color_id");

	foreach($sql_cutting_issue as $row)
	{
		//$issue_to_cut_arr[$row[csf("booking_no_id")]][$constructtion_arr[$row[csf('detarmination_id')]] .'**'. $composition_arr[$row[csf('detarmination_id')]].'**'.$row[csf('color_id')]]+=$row[csf("issue_qty")];
		$issue_to_cut_arr[$row[csf("booking_no_id")]][$row[csf('detarmination_id')].'**'.$row[csf('color_id')]]+=$row[csf("issue_qty")];
		$buyer_summary_arr[$row[csf('buyer_id')]]['issue_to_cut'] +=$row[csf('issue_qty')];
	}
	unset($sql_cutting_issue);

	/* echo "<pre>";
	print_r($buyer_summary_arr);
	die; */

	$r_id=execute_query("delete from tmp_booking_id where userid=$user_name and type=99");
	if($r_id)
	{
		oci_commit($con);
	}

	ob_start();
	?>
	

	<!-- All Summary Start-->
	<div style="width:2350px; margin-bottom:10px;">
		<!-- Buyer Level Summary Start-->
	 	<div style="float:left;  margin-bottom:10px;">
		<table class="rpt_table" border="1" rules="all" width="1700" cellpadding="0" cellspacing="0">
			<thead>
	        	<tr>
					<th  colspan="16" align="center">Buyer Level Summary</th>
				</tr>
	        	<tr>
					<th width="40">SL</th>
	                <th width="80">Buyer Name</th>
					<th width="100">Grey Req.</th>
	                <th width="100">Yarn Issue</th>
	                <th width="100">Yarn Balance</th>
					<th width="100">Knitting Total</th>
					<th width="100">Knit Balance</th>
					<th width="100">Grey Issue</th>
					<th width="100">Batch Qnty</th>
					<th width="100">Batch Balance</th>
					<th width="100">Total Dyeing</th>
					<th width="100">Dyeing Balance</th>
					<th width="100">Fin. Fab Req.</th>
	                <th width="100">Fin. Fab total</th>
	                <th width="100">Fin. Fab Balance</th>
					<th>Issue to Cutting </th>
				</tr>
	        </thead>
	        <tbody>
	        <?
			$p=1;
			$gt_issue_cutting=0;
			$gt_finish_available=0;
			$gt_batch_qty=0; 
			$gt_yarn_issue=0; 
			$gt_dying_qty=0; 
			$dtls_tot_dying_qty=0; 
			foreach($buyer_summary_arr as $buyer_id=>$row)
			{
				if ($p%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$net_yarn_issue=  $row["yarn_issue"]-$row["yarn_issue_ret"];
				$finish_fabric_total=  ($row["fin_rcv_production_qnty"] + $row["fin_rcv_purchase_qnty"] + $row["fin_trans_in"]) - $row["fin_trans_out"];

				$grey_available = ($row["grey_receive"] + $row["grey_trans_in"]) - $row["grey_trans_out"];
				$summary_tot_grey_available += $grey_available;
				  
				?>
	        	<tr bgcolor="<? echo $bgcolor; ?>">
					<td><? echo $p; ?></td>
	                <td><? echo $buyer_arr[$buyer_id]; ?></td>
					<td align="right"><? echo number_format($row["grey_requ_qnty"],2); $buyer_tot_feb_req+=$row["grey_requ_qnty"];?></td>
	                <td align="right"><? echo number_format($net_yarn_issue,2);  $buyer_tot_yarn_issue+=$net_yarn_issue; ?></td>
	                <td align="right"><? echo number_format(($row["grey_requ_qnty"]-$net_yarn_issue),2); $buyer_tot_yarn_balance+=($row["grey_requ_qnty"]-$net_yarn_issue); ?></td>
					<td align="right"><? echo number_format($row["grey_production"],2); $buyer_tot_grey_knitting+=$row["grey_production"]; ?></td>
					<td align="right"><? echo number_format(($row["grey_requ_qnty"]-$row["grey_production"]),2); $buyer_tot_grey_knitting_bal+=($row["grey_requ_qnty"]-$row["grey_production"]); ?></td>
					<td align="right"><? echo number_format($row["grey_issue"],2); $buyer_tot_grey_issue+=$row["grey_issue"];  ?></td>
					<td align="right"><? echo number_format($row["batch_qnty"],2); $buyer_tot_batch_qty+=$row["batch_qnty"]; ?></td>
					<td align="right"><? echo number_format(($row["grey_requ_qnty"]-$row["batch_qnty"]),2); $buyer_tot_batch_balance+=($row["grey_requ_qnty"]-$row["batch_qnty"]); ?></td>
					<td align="right"><? echo number_format($row["dyeing_qnty"],2);  $buyer_tot_dyeing_qty+=$row["dyeing_qnty"];  ?></td>
					<td align="right"><? echo number_format(($row["grey_requ_qnty"]-$row["dyeing_qnty"]),2); $buyer_tot_dyeing_balance+=($row["grey_requ_qnty"]-$row["dyeing_qnty"]); ?></td>
					<td align="right"><? echo number_format($row["fin_requ_qnty"],2);  $buyer_tot_finish_req_qty+=$row["fin_requ_qnty"];  ?></td>
	                <td align="right"><? echo number_format($finish_fabric_total,2); $buyer_tot_finish_abable_qty+=$finish_fabric_total;  ?></td>
	                <td align="right"><? echo number_format(($row["fin_requ_qnty"]-$finish_fabric_total),2); $buyer_tot_finish_balance+=($row["fin_requ_qnty"]-$finish_fabric_total);  ?></td>

					<td align="right"><? echo number_format($row["issue_to_cut"],2); $buyer_tot_cutting_qty+=$row["issue_to_cut"]; ?></td>
				</tr>
	            <?
				$p++;
			}
			?>
	        </tbody>
	        <tfoot>
	        	<tr>
	                <th>Total:</th>
					<th align="right"></th>
					<th align="right"><? echo number_format($buyer_tot_feb_req,2); ?></th>
	                <th align="right"><? echo number_format($buyer_tot_yarn_issue,2); ?></th>
	                <th align="right"><? echo number_format($buyer_tot_yarn_balance,2); ?></th>
					<th align="right"><? echo number_format($buyer_tot_grey_knitting,2); ?></th>
					<th align="right"><? echo number_format($buyer_tot_grey_knitting_bal,2); ?></th>
					<th align="right"><? echo number_format($buyer_tot_grey_issue,2); ?></th>
					<th align="right"><? echo number_format($buyer_tot_batch_qty,2); ?></th>
					<th align="right"><? echo number_format($buyer_tot_batch_balance,2); ?></th>
					<th align="right"><? echo number_format($buyer_tot_dyeing_qty,2); ?></th>
					<th align="right"><? echo number_format($buyer_tot_dyeing_balance,2); ?></th>
					<th align="right"><? echo number_format($buyer_tot_finish_req_qty,2); ?></th>
	                <th align="right"><? echo number_format($buyer_tot_finish_abable_qty,2); ?></th>
	                <th align="right"><? echo number_format($buyer_tot_finish_balance,2); ?></th>
					<th align="right"><? echo number_format($buyer_tot_cutting_qty,2); ?> </th>
				</tr>
	        </tfoot>
	    </table>
	    </div>
	    <!-- Buyer Level Summary End-->
	    <!-- Summary Start -->
		<div style="float:left; width:320px;  margin-left:20px;">
		    <table class="rpt_table" border="1" rules="all" width="400" cellpadding="0" cellspacing="0">
		        <thead>
		        	<tr>
		            	<th colspan="4">Summary</th>
		            </tr>
		            <tr>
		            	<th width="30">Sl</th>
		            	<th width="200">Particulars</th>
		                <th width="80">Quantity</th>
		                <th width="70">%</th>
		            </tr>
		        </thead>
		        <tbody>
		        	<tr>
		            	<td>1</td>
		            	<td>Total Yarn Required</td>
		                <td align="right"><? echo number_format($buyer_tot_feb_req,2); ?></td>
		                <td></td>
		            </tr>
		            <tr>
		            	<td>2</td>
		            	<td>Total Yarn Issued</td>
		                <td align="right"><? echo number_format($buyer_tot_yarn_issue,2); ?></td>
		                <td align="right"><? $yarn_issue_parcent=(($buyer_tot_yarn_issue/$buyer_tot_feb_req)*100); echo number_format($yarn_issue_parcent,2)."%"; ?></td>
		            </tr>
		            <tr>
		            	<td>3</td>
		            	<td ><strong> Total Issue Balance</strong></td>
		                <td align="right"><? $gt_issue_balance=$buyer_tot_feb_req-$buyer_tot_yarn_issue; echo number_format($gt_issue_balance,2); ?></td>
		                <td align="right"><? $issue_balance_parcentage=(($gt_issue_balance/$buyer_tot_feb_req)*100); echo number_format($issue_balance_parcentage,2)."%";  ?></td>
		            </tr>
		            <tr>
		            	<td>4</td>
		            	<td>Total Grey Fabric Required</td>
		                <td align="right"><? echo number_format($buyer_tot_feb_req,2); ?></td>
		                <td></td>
		            </tr>
		            <tr>
		            	<td>5</td>
		            	<td>Total Grey Fabric Available</td>
		                <td align="right"><? echo number_format($summary_tot_grey_available,2); ?></td>
		                <td align="right"><? $grey_available_parcentage=(($summary_tot_grey_available/$buyer_tot_feb_req)*100); echo number_format($grey_available_parcentage,2)."%";  ?></td>
		            </tr>
		            <tr>
		            	<td>6</td>
		            	<td>Total Grey Fabric Issued To Dye</td>
		                <td align="right"><? echo number_format($buyer_tot_grey_issue,2); ?></td>
		                <td align="right"><? $grey_dying_parcentage=(($buyer_tot_grey_issue/$buyer_tot_feb_req)*100); echo number_format($grey_dying_parcentage,2)."%";  ?></td>
		            </tr>
		         
		            
		            <tr>
		            	<td>7</td>
		            	<td><strong>Total Grey Fabric Issued Balance</strong></td>
		                <td align="right"><? $gt_grey_balance=$buyer_tot_feb_req-$gt_grey_available;  echo number_format($gt_grey_balance,2); ?></td>
		                <td align="right"><? $grey_balance_parcentage=(($gt_grey_balance/$buyer_tot_feb_req)*100); echo number_format($grey_balance_parcentage,2)."%";  ?></td>
		            </tr>
		           
		            <tr>
		            	<td>8</td>
		            	<td>Total Batch Qty.</td>
		                <td align="right"><? echo number_format($buyer_tot_batch_qty,2); ?></td>
		                <td align="right"></td>
		            </tr>
		             <tr>
		            	<td>9</td>
		            	<td><strong>Total Batch Balance To Grey</strong></td>
		                <td align="right"><? $total_batch_balance=$buyer_tot_feb_req-$buyer_tot_batch_qty; echo number_format($total_batch_balance,2); ?></td>
		                <td align="right"><? //$grey_batch_balance_parcentage=(($total_batch_balance/$gt_yarn_grey_required)*100); echo number_format($total_batch_balance,2)."%";  ?></td>
		            </tr>
		            <tr>
		            	<td>10</td>
		            	<td>Total Dyeing Qty</td>
		                <td align="right"><? echo number_format($buyer_tot_dyeing_qty,2); ?></td>
		                <td align="right"><? $grey_dying_parcentage=(($buyer_tot_dyeing_qty/$buyer_tot_feb_req)*100); echo number_format($grey_dying_parcentage,2)."%";  ?></td>
		            </tr>
		            <tr>
		            	<td>11</td>
		            	<td><strong>Total Dye Balance To Grey</strong></td>
		                <td align="right"><? $total_dying_balance=$buyer_tot_feb_req-$buyer_tot_dyeing_qty; echo number_format($total_batch_balance,2); ?></td>
		                <td align="right"><? $grey_dying_balance_parcentage=(($total_dying_balance/$buyer_tot_feb_req)*100); echo number_format($total_batch_balance,2)."%";  ?></td>
		            </tr>
		            <tr>
		            	<td>12</td>
		            	<td>Total Finish Fabric Required</td>
		                <td align="right"><? echo number_format($buyer_tot_finish_req_qty,2); ?></td>
		                <td></td>
		            </tr>
		            <tr>
		            	<td>13</td>
		            	<td>Total Finish Fabric Available</td>
		                <td align="right"><? echo number_format($buyer_tot_finish_abable_qty,2); ?></td>
		                <td align="right"><? $finish_available_parcentage=(($buyer_tot_finish_abable_qty/$buyer_tot_finish_req_qty)*100); echo number_format($finish_available_parcentage,2)."%";  ?></td>
		            </tr>
		            <tr>
		            	<td>14</td>
		            	<td><strong>Total Finish Fabric Balance</strong></td>
		                <td align="right"><? $gt_finish_balance=$buyer_tot_finish_req_qty-$buyer_tot_finish_abable_qty;  echo number_format($gt_finish_balance,2); ?></td>
		                <td align="right"><? $finish_balance_parcentage=(($gt_finish_balance/$buyer_tot_finish_req_qty)*100); echo number_format($finish_balance_parcentage,2)."%";  ?></td>
		            </tr>
		            <tr>
		            	<td>15</td>
		            	<td>Total Issue to Cutting</td>
		                <td align="right"><? echo number_format($buyer_tot_cutting_qty,2); ?></td>
		                <td align="right"><? $finish_issue_cutting_parcentage=(($buyer_tot_cutting_qty/$buyer_tot_finish_req_qty)*100); echo number_format($finish_issue_cutting_parcentage,2)."%";  ?></td>
		            </tr>
		            <tr>
		            	<td>16</td>
		            	<td><strong>Total Issue Balance</strong></td>
		                <td align="right"><? $gt_finish_issue_cut_balance=$buyer_tot_finish_req_qty-$buyer_tot_cutting_qty;  echo number_format($gt_finish_issue_cut_balance,2); ?></td>
		                <td align="right"><? $finish_issue_cut_bal_parcentage=(($gt_finish_issue_cut_balance/$buyer_tot_finish_req_qty)*100); echo number_format($finish_issue_cut_bal_parcentage,2)."%";  ?></td>
		            </tr>
		        </tbody>
		    </table>
	    </div>
	    <!-- Summary End -->
	</div>
	<!-- All Summary End-->

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
		<table cellspacing="0" cellpadding="0" border="1" class="rpt_table" width="3980" rules="all" id="table_header" >
			<thead>
				<tr>
					<th colspan="12">Booking details</th>
					<th colspan="5">Yarn details</th>
					<th colspan="4">Knitting production</th>
					<th colspan="6">Grey fabric store</th>
					<th colspan="4">Dyeing production</th>
					<th colspan="5">Finish fabric production</th>
					<th colspan="7">Finish fabric store</th>
					<th rowspan="2" width="180">Fabric description</th>
				</tr>
				<tr>
					<th width="30">Sl No</th>
					<th width="80">Booking Year</th>
					<th width="90">Requisition No</th>
					<th width="90">Booking No</th>
					<th width="90">Buyer Name</th>
					<th width="90">Style Ref.</th>
					<th width="90">Item Name</th>
					<th width="90">W/O Booking Date</th>
					<th width="90">Knitting Production Date</th>
					<th width="90">Finished Fabric Delivery Date</th>
					<th width="90">Construction</th>
					<th width="135">Composition</th>
					<th width="45">Count</th>
					<th width="90">Type</th>

					<th width="90">Required<br/><font style="font-size:9px; font-weight:100">(As Per Booking)</font></th>
					<th width="90">Issued</th>
					<th width="90">Issue Balance<br/><font style="font-size:9px; font-weight:100">(Grey Req-Yarn Issue)</font></th>
					<th width="90">Knitted Production</th>
					
					<th width="90">Knit Balance</th>
					<th width="90">Grey Fab Delv. To Store</th>
					<th width="90">Grey in Knitting Floor</th>
					<th width="90">Grey Rcvd Prod.</th>

					<th width="90">Grey Rcvd - Purchase</th>
					<th width="90">Net Transfer</th>
					<th width="90">Fabric Available</th>
					<th width="90">Receive Balance</th>
					<th width="90">Grey Issue</th>


					<th width="90">Fabric Color</th>
					<th width="90">Batch Qnty</th>
					<th width="90">Dye Qnty</th>
					<th width="90">Balance	Qty<font style="font-size:9px; font-weight:100">Batch Qnty - Dye Qnty</font></th>
					<th width="90">Required Qty<br/><font style="font-size:9px; font-weight:100">(As Per Booking)</font></th>
					<th width="90">Production Qty.</th>
					<th width="90">Balance Qty</th>
											
									
					<th width="90">Finish Fab. Delv. To Store</th>
					<th width="90">Fabric in Prod. Floor</th>
					<th width="90">Received - Prod.</th>
					<th width="90">Received - Purchase</th>
					<th width="90">Fabric Available <br/><font style="font-size:9px; font-weight:100">Finish receive + transfer in - transfer out</font></th>
										
						
					<th width="90">Receive Balance <br/><font style="font-size:9px; font-weight:100">Finish Required - Fabric Available</font></th>
					<th width="90">Issue to Cutting</th>
					<th width="90">Yet to Issue <br/><font style="font-size:9px; font-weight:100">Finish Required - Issue to Cutting</font></th>
					<th width="90">Fabric Stock/ Left Over<br/><font style="font-size:9px; font-weight:100">Finish receive balance - Yet to issue</font></th>
				</tr>
			</thead>
		</table>
		<div style="max-height:320px; overflow-y:scroll; width:4000px;" id="scroll_body">
			<table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="3980" rules="all" id="table_body" align="left">
				<tbody>
					<?
					$i=1; 
					$books_ar=array();
					$j=1;
					$book_rowspan="";
					$receive_qty=0;
					foreach ($data_array as $book_year=>$book_year_data)
					{
						foreach ($book_year_data as $requ_no => $requ_no_data) 
						{
							foreach ($requ_no_data as $fabstring => $row) 
							{
								$fabstring_array = explode("**",$fabstring);
								/* $construction_no = $fabstring_array[0];
								$composition_no = $fabstring_array[1];
								$color_no = $fabstring_array[2]; */

								$construction_no = $row["construction"];
								$composition_no = $row["composition"];
								$determination_id = $fabstring_array[0];
								$color_no = $fabstring_array[1];    //$row["construction"] $row["composition"]

								$net_yarn_issue=$yarn_issue_arr[$row["booking_id"]]-$yarn_issue_rtn_arr[$row["booking_id"]];
								$yarn_issue_balance = $row['requ_qnty'] - $net_yarn_issue;
								
								$knitting_production = $grey_knit_production_arr[$row["booking_id"]][$determination_id.'**'.$color_no];
								$knitting_production_balance = $row['requ_qnty'] - $knitting_production;

								$grey_delivery = $grey_delivery_stock[$row["booking_id"]][$determination_id.'**'.$color_no];

								$grey_in_knit_floor = $knitting_production-$grey_delivery;

								$grey_knit_recv = $grey_knit_recv_arr[$row["booking_id"]][$determination_id.'**'.$color_no];
								$grey_knit_purchase = $grey_knit_purchase_arr[$row["booking_id"]][$determination_id.'**'.$color_no];

								$tranfer_in = $sample_transectionArr[$row["booking_id"]][$determination_id.'**'.$color_no]['tranfer_in'];
								$tranfer_out = $sample_transectionArr[$row["booking_id"]][$determination_id.'**'.$color_no]['tranfer_out'];
								$net_transfer = $tranfer_in- $tranfer_out;
								$grey_available = ($grey_knit_recv + $grey_knit_purchase + $tranfer_in) - $tranfer_out;

								$grey_balance = $row['requ_qnty'] - ($grey_knit_recv + $grey_knit_purchase + $net_transfer);


								$grey_issue = $grey_issue_arr[$row["booking_id"]][$determination_id.'**'.$color_no];
								$batch_qty = $batch_qty_arr[$row["booking_id"]][$determination_id.'**'.$color_no]['batch_qnty'];
								$dyeing_qty = $dyeing_qty_arr[$row["booking_id"]][$determination_id.'**'.$color_no]['dyeing_qnty'];
								$dyeing_balance =$batch_qty- $dyeing_qty;

								$fin_prod_qty = $fin_prod_qty_arr[$row["booking_id"]][$determination_id.'**'.$color_no]['fin_prod_qnty'];
								$finish_production_balance = $row['fin_requ_qnty']-$fin_prod_qty;

								$fin_rcv_production_qnty = $fin_prod_qty_arr[$row["booking_id"]][$determination_id.'**'.$color_no]['fin_rcv_production_qnty'];

								$fin_rcv_purchase_qnty = $fin_prod_qty_arr[$row["booking_id"]][$determination_id.'**'.$color_no]['fin_rcv_purchase_qnty'];

								$finish_deli_qty = $finish_deli_qty_arr[$row["booking_id"]][$determination_id.'**'.$color_no];

								$finish_deli_balance = $fin_prod_qty-$finish_deli_qty;


								$finish_trans_in = $finish_trans_qty_arr[$row["booking_id"]][$determination_id.'**'.$color_no]['trans_in'];
								$finish_trans_out = $finish_trans_qty_arr[$row["booking_id"]][$determination_id.'**'.$color_no]['trans_out'];

								$finish_available = ($fin_rcv_production_qnty+$fin_rcv_purchase_qnty +$finish_trans_in) - $finish_trans_out;

								$finish_receive_balance = $row['fin_requ_qnty']-$finish_available;

								$issue_to_cut = $issue_to_cut_arr[$row["booking_id"]][$determination_id.'**'.$color_no];
								$yet_to_issue = $row['fin_requ_qnty']-$issue_to_cut;
								$fabric_left_over = $finish_receive_balance-$issue_to_cut;

								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
									<td width="30" align="center"><? echo $i; ?></td>
									<td width="80"><? echo $row['booking_year']; ?></td>
									<td width="90"><a href="##"  onclick='open_report("<? echo $row['company_id']; ?>","<? echo $row['requisition_id']; ?>","<? echo $row['booking_no']; ?>","sample_requisition_print7")' >
										<? echo $row['requ_no']; ?>
									</a></td> 
									<td width="90">
										<a href="##"  onclick='open_report("<? echo $row['company_id']; ?>","<? echo $row['requisition_id']; ?>","<? echo $row['booking_no']; ?>","sample_requisition_print10")' >
										<? echo $row['booking_no']; ?>
									</a></td>
									<td width="90"><? echo $buyer_arr[$row['buyer_name']]; ?></td>
									<td width="90"><? echo $row['style_ref_no'] ; ?></td>
									<td width="90"><? echo implode(",",array_unique(explode(",",chop($row['gmts_item_id'],","))));?></td>
									<td width="90"><? echo change_date_format($row['booking_date']); ?></td>
									<td width="90"><? //echo change_date_format($row['booking_date']); ?></td>
									<td width="90"><? echo change_date_format($row['delivery_date']); ?></td>

									<td width="90"><? echo $construction_no; ?></td> 
									<td width="135"><? echo $composition_no; ?></td> 
									<td width="45"><? echo implode(",",array_filter(array_unique(explode(",",chop($row['count'],",")))));?></td>
									<td width="90"><? echo implode(",",array_filter(array_unique(explode(",",chop($row['type'],",")))));?></td>
									<td width="90"><? echo number_format($row['requ_qnty'],2,".","");?></td>
									
									<td width="90">
									<a  href="##" onclick='openmypage("<? echo $row['booking_id']; ?>","yarn_issue","<? echo $row[('booking_no')]; ?>")'>
									<? echo number_format($net_yarn_issue,2,".","");?></td>
									</a>
									<td width="90"><? echo number_format($yarn_issue_balance,2,".","");?></td>
									<td width="90">
									<a  href="##" onclick="openmypage('<? echo $row["booking_id"]; ?>','knitting_production','','<? echo $construction_no;?>','<? echo $composition_no;?>','<? echo $color_no;?>','<? echo $determination_id;?>')">
										<? echo number_format($knitting_production,2,".","");?>
									</a>
									</td>
									<td width="90"><? echo number_format($knitting_production_balance,2,".","");?></td>
									<td width="90"><? echo number_format($grey_delivery,2,".","");?></td>
									<td width="90"><? echo number_format($grey_in_knit_floor,2,".","");?></td>

									<td width="90">
									<a  href="##" onclick="openmypage('<? echo $row["booking_id"]; ?>','grey_receive_prod','','<? echo $construction_no;?>','<? echo $composition_no;?>','<? echo $color_no;?>','<? echo $determination_id;?>')">
										<? echo number_format($grey_knit_recv,2,".","");?>
									</a>
									</td>
									<td width="90"><? echo number_format($grey_knit_purchase,2,".","");?></td>
									<td width="90">
									<a href="##" onclick="openmypage('<? echo $row["booking_id"]; ?>','grey_fabric_transfer','','<? echo $construction_no;?>','<? echo $composition_no;?>','<? echo $color_no;?>','<? echo $determination_id;?>')">
										<? echo number_format($net_transfer,2,".","");?>
									</a>
									</td>

									<td width="90"><? echo number_format($grey_available,2,'.','');?></td>
									<td width="90"><? echo number_format($grey_balance,2,".","");?></td>
									<td width="90">
										<a href="##" onclick="openmypage('<? echo $row["booking_id"]; ?>','grey_issue','','<? echo $construction_no;?>','<? echo $composition_no;?>','<? echo $color_no;?>','<? echo $determination_id;?>')">
										<? echo number_format($grey_issue,2,".","");?>
										</a>
									</td>
									<td width="90"><? echo $color_arr[$color_no];?></td>
									<td width="90">
									<a  href="##" onclick="openmypage('<? echo $row["booking_id"]; ?>','batch_qty_popup','','<? echo $construction_no;?>','<? echo $composition_no;?>','<? echo $color_no;?>','<? echo $determination_id;?>')">
										<? echo number_format($batch_qty,2,".","");?>
									</a>
									</td>
									<td width="90" title="dyeing_qty">
									<a  href="##" onclick="openmypage('<? echo $row["booking_id"]; ?>','dying_qty_popup','','<? echo $construction_no;?>','<? echo $composition_no;?>','<? echo $color_no;?>','<? echo $determination_id;?>')">
										<? echo number_format($dyeing_qty,2,".","");?>
									</a>
									</td>
									<td width="90"><? echo number_format($dyeing_balance,2,".","");?></td>
									<td width="90" title="fin_requ_qnty"><? echo number_format($row['fin_requ_qnty'],2,".","");?></td>
									<td width="90">
									<a  href="##" onclick="openmypage('<? echo $row["booking_id"]; ?>','finish_feb_prod','','<? echo $construction_no;?>','<? echo $composition_no;?>','<? echo $color_no;?>','<? echo $determination_id;?>')">
										<? echo number_format($fin_prod_qty,2,".","");?>
									</a>
									</td>
									<td width="90" title="finish_production_balance"><? echo number_format($finish_production_balance,2,".","");?></td>
									<td width="90">
									<a  href="##" onclick="openmypage('<? echo $row["booking_id"]; ?>','finish_fabric_delivery_to_store','','<? echo $construction_no;?>','<? echo $composition_no;?>','<? echo $color_no;?>','<? echo $determination_id;?>')">
										<? echo number_format($finish_deli_qty,2,".","");?>
									</a>
									</td>
									<td width="90"><? echo number_format($finish_deli_balance,2,".","");?></td>
									<td width="90">
									<a  href="##" onclick="openmypage('<? echo $row["booking_id"]; ?>','finish_fabric_receive_by_store','','<? echo $construction_no;?>','<? echo $composition_no;?>','<? echo $color_no;?>','<? echo $determination_id;?>')">
										<? echo number_format($fin_rcv_production_qnty,2,".","");?>
									</a>
									</td>
									<td width="90"><? echo number_format($fin_rcv_purchase_qnty,2,".","");?></td>
									<td width="90" title='<? echo  "(rcv prod: $fin_rcv_production_qnty + rcv purch: $fin_rcv_purchase_qnty + trans in : $finish_trans_in) - trans out: $finish_trans_out";?>'><? echo number_format($finish_available,2,".","");?></td> 
									<td width="90"><? echo number_format($finish_receive_balance,2,".","");?></td> 
									<td width="90">
									<a  href="##" onclick="openmypage('<? echo $row["booking_id"]; ?>','issue_to_cut','','<? echo $construction_no;?>','<? echo $composition_no;?>','<? echo $color_no;?>','<? echo $determination_id;?>')">
										<? echo number_format($issue_to_cut,2,'.','');?>
									</a>
									</td>
									<td width="90"><? echo number_format($yet_to_issue,2,'.','');?></td>
									<td width="90"><? echo number_format($fabric_left_over ,2,'.','');?></td>
									<td width="180"><? echo implode(",", array_filter(array_unique(explode(",",chop( $row['fabric_description'],","))))) ; ?> </td>
								</tr>
								<?
								$i++;

								$dtls_tot_grey_required +=$row['requ_qnty'];
								$dtls_tot_net_yarn_issue +=$net_yarn_issue;
								$dtls_tot_yarn_issue_balance +=$yarn_issue_balance;

								$dtls_tot_knitting_production +=$knitting_production;
								$dtls_tot_knitting_production_balance +=$knitting_production_balance;
								$dtls_tot_grey_delivery +=$grey_delivery;
								$dtls_tot_grey_in_knit_floor +=$grey_in_knit_floor;

								$dtls_tot_grey_knit_recv +=$grey_knit_recv;
								$dtls_tot_grey_knit_purchase +=$grey_knit_purchase;
								$dtls_tot_net_transfer +=$net_transfer;
								$dtls_tot_grey_available +=$grey_available;
								$dtls_tot_grey_balance +=$grey_balance;
								$dtls_tot_grey_issue +=$grey_issue;

								$dtls_tot_batch_qty +=$batch_qty;
								$dtls_tot_dyeing_qty +=$dyeing_qty;
								$dtls_tot_dyeing_balance +=$dyeing_balance;

								$dtls_tot_fin_requ_qnty +=$row['fin_requ_qnty'];
								$dtls_tot_fin_prod_qty +=$fin_prod_qty;
								$dtls_tot_finish_production_balance +=$finish_production_balance;
								$dtls_tot_finish_deli_qty +=$finish_deli_qty;
								$dtls_tot_finish_deli_balance +=$finish_deli_balance;

								$dtls_tot_fin_rcv_production_qnty +=$fin_rcv_production_qnty;
								$dtls_tot_fin_rcv_purchase_qnty +=$fin_rcv_purchase_qnty;
								$dtls_tot_finish_available +=$finish_available;
								$dtls_tot_finish_receive_balance +=$finish_receive_balance;
								$dtls_tot_issue_to_cut +=$issue_to_cut;
								$dtls_tot_yet_to_issue +=$yet_to_issue;
								$dtls_tot_fabric_left_over +=$fabric_left_over;
							}
							
						}
					}
					?>
					</tbody>
				</table>
			</div>
			<table width="3980" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table"> 
				<tfoot>
                 	<tr>
						<th width="30">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="90">&nbsp;</th>
						<th width="90">&nbsp;</th>
						<th width="90">&nbsp;</th>
						<th width="90">&nbsp;</th>
						<th width="90">&nbsp;</th>
						<th width="90">&nbsp;</th>
						<th width="90">&nbsp;</th>
						<th width="90">&nbsp;</th>

						<th width="90">&nbsp;</th>
						<th width="135">&nbsp;</th>
						<th width="45">&nbsp;</th>
						<th width="90">&nbsp;</th>

						<th width="90" id="value_dtls_tot_gery_req"><? echo number_format($dtls_tot_grey_required,2);?></th>
						<th width="90" id="value_dtls_tot_yarn_issue"><? echo number_format($dtls_tot_net_yarn_issue,2);?></th>
						<th width="90" id="value_dtls_tot_yarn_balance"><? echo number_format($dtls_tot_yarn_issue_balance,2);?></th>

						<th width="90" id="value_dtls_tot_gery_knit_product"><? echo number_format($dtls_tot_knitting_production,2);?></th>
						<th width="90" id="value_dtls_tot_gray_bal"><? echo number_format($dtls_tot_knitting_production_balance,2);?></th>
						<th width="90" id="value_dtls_tot_gery_delivery"><? echo number_format($dtls_tot_grey_delivery,2);?></th>
						<th width="90" id="value_dtls_tot_gery_in_knit_product"><? echo number_format($dtls_tot_grey_in_knit_floor,2);?></th>

						<th width="90" id="value_dtls_tot_grey_knit_receive_prod"><? echo number_format($dtls_tot_grey_knit_recv,2);?></th>
						<th width="90" id="value_dtls_tot_grey_knit_receive_purchase"><? echo number_format($dtls_tot_grey_knit_purchase,2);?></th>
						<th width="90" id="value_dtls_tot_net_transfer"><? echo number_format($dtls_tot_net_transfer,2);?></th>
						<th width="90" id="value_dtls_tot_gray_available_all"><? echo number_format($dtls_tot_grey_available,2);?></th>
						<th width="90" id="value_dtls_tot_gray_balance"><? echo number_format($dtls_tot_grey_balance,2);?></th>
						<th width="90" id="value_dtls_tot_gray_issue"><? echo number_format($dtls_tot_grey_issue,2);?></th>


						<th width="90">&nbsp;</th>
						<th width="90" id="value_dtls_tot_batch_qty"><? echo number_format($dtls_tot_batch_qty,2);?></th>
						<th width="90" id="value_dtls_tot_dying_qty"><? echo number_format($dtls_tot_dyeing_qty,2);?></th>
						<th width="90" id="value_dtls_tot_dying_balance"><? echo number_format($dtls_tot_dyeing_balance,2);?></th>

						<th width="90" id="value_dtls_tot_fin_req_qty"><? echo number_format($dtls_tot_fin_requ_qnty,2);?></th>
						<th width="90" id="value_dtls_tot_fin_prod_qnty"><? echo number_format($dtls_tot_fin_prod_qty,2);?></th>
						<th width="90" id="value_tot_fin_balance"><? echo number_format($dtls_tot_finish_production_balance,2);?></th>
						<th width="90" id="value_dtls_tot_fin_delivery_qty"><? echo number_format($dtls_tot_finish_deli_qty,2);?></th>
						<th width="90" id="value_dtls_tot_fabric_in_prod_floor"><? echo number_format($dtls_tot_finish_deli_balance,2);?></th>

						<th width="90" id="value_dtls_tot_finish_prod_rece_store"><? echo number_format($dtls_tot_fin_rcv_production_qnty,2);?></th>
						<th width="90" id="value_finish_parchase_rece_store"><? echo number_format($dtls_tot_fin_rcv_purchase_qnty,2);?></th>
						<th width="90" id="value_dtls_tot_fabric_store_available"><? echo number_format($dtls_tot_finish_available,2);?></th>
						<th width="90" id="value_dtls_tot_fin_balance"><? echo number_format($dtls_tot_finish_receive_balance,2);?></th>
						<th width="90" id="value_dtls_tot_cutting_qty"><? echo number_format($dtls_tot_issue_to_cut,2);?></th>
						<th width="90" id="value_dtls_tot_yet_to_issue"><? echo number_format($dtls_tot_yet_to_issue,2);?></th>
						<th width="90" id="value_dtls_tot_left_over"><? echo number_format($dtls_tot_fabric_left_over,2);?></th>

						<th width="180">&nbsp;</th>		
					
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

if($action=="yarn_issue")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$brand_arr=return_library_array( "select id, brand_name from lib_brand", "id", "brand_name");
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
	<fieldset style="width:965px; margin-left:3px">
		<div id="report_container">
            <table border="1" class="rpt_table" rules="all" width="960" cellpadding="0" cellspacing="0">
                <?
				$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
				$supplier_details=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
                $i=1; $total_yarn_issue_qnty=0; $total_yarn_issue_qnty_out=0;

				$sql="SELECT a.booking_id, a.booking_no,a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company, sum(d.cons_quantity) as issue_qnty, c.lot, c.yarn_type, c.id as prod_id, c.product_name_details, d.brand_id 
				from inv_issue_master a, product_details_master c, inv_transaction d 
				where a.id=d.mst_id and d.prod_id=c.id  and d.transaction_type=2 and d.item_category=1 and a.issue_basis=1 and a.issue_purpose=8 and d.transaction_type=2 and a.entry_form=3 and a.booking_id=$boking_id and a.booking_no='$booking_no' and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 
				group by a.booking_id, a.booking_no,a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company,c.lot, c.yarn_type, c.id, c.product_name_details,d.brand_id
				union all 
				SELECT a.booking_id, a.booking_no,a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company, sum(d.cons_quantity) as issue_qnty,
				c.lot, c.yarn_type, c.id as prod_id, c.product_name_details, d.brand_id 
				from inv_issue_master a, product_details_master c, inv_transaction d,  ppl_yarn_requisition_entry e,ppl_planning_entry_plan_dtls f
				where a.id=d.mst_id and d.prod_id=c.id and d.requisition_no=e.requisition_no and e.knit_id=f.dtls_id and f.booking_no='$booking_no' and e.prod_id=d.prod_id and a.issue_basis=3 and a.issue_purpose=1 and a.item_category=1 and d.transaction_type=2 and a.entry_form=3 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 group by a.booking_id, a.booking_no,a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company,c.lot, c.yarn_type, c.id, 
				c.product_name_details,d.brand_id ";

                $result=sql_select($sql);
				if(!empty($result))
				{
					?>
	            	<thead>
						<th colspan="10"><b>Yarn Issue</b></th>
					</thead>
					<thead>
	                    <th width="105">Issue Id</th>
	                    <th width="90">Issue To</th>
	                    <th width="105">Booking No</th>
	                    <th width="80">Challan No</th>
	                    <th width="60">Issue Date</th>
	                    <th width="70">Brand</th>
	                    <th width="60">Lot No</th>
	                    <th width="180">Yarn Description</th>
	                    <th width="70">Issue Qnty (In)</th>
	                    <th>Issue Qnty (Out)</th>
					</thead>
	                <?
				}
				foreach($result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				
					if($row[csf('knit_dye_source')]==1) $issue_to=$company_library[$row[csf('knit_dye_company')]]; 
					else if($row[csf('knit_dye_source')]==3) $issue_to=$supplier_details[$row[csf('knit_dye_company')]];
					else $issue_to="&nbsp;";
						
                    $yarn_issued=$row[csf('issue_qnty')];
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="105"><p><? echo $row[csf('issue_number')]; ?></p></td>
                        <td width="90"><p><? echo $issue_to; ?></p></td>
                        <td width="105"><p><? echo $row[csf('booking_no')];?></p></td>
                        <td width="80"><p><? echo $row[csf('challan_no')]; ?></p></td>
                        <td width="60" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
                        <td width="70"><p><? echo $brand_arr[$row[csf("brand_id")]]; ?></p></td>
                        <td width="60"><p><? echo $row[csf('lot')]; ?></p></td>
                        <td width="180"><p><? echo$row[csf('product_name_details')]; ?></p></td>
                        <td align="right" width="70">
							<? 
								if($row[csf('knit_dye_source')]!=3)
								{
									echo number_format($yarn_issued,2);
									$total_yarn_issue_qnty+=$yarn_issued;
								}
								else echo "&nbsp;";
                            ?>
                        </td>
                        <td align="right">
							<? 
								if($row[csf('knit_dye_source')]==3)
								{ 
									echo number_format($yarn_issued,2); 
									$total_yarn_issue_qnty_out+=$yarn_issued;
								}
								else echo "&nbsp;";
                            ?>
                        </td>
                    </tr>
                	<?
                	$i++;
                }
                ?>
                <tr style="font-weight:bold">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td align="right">Total</td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty,2);?></td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty_out,2);?></td>
                </tr>
                <tr style="font-weight:bold">
                    <td align="right" colspan="9">Issue Total</td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty+$total_yarn_issue_qnty_out,2);?></td>
                </tr>
                <?
                $total_yarn_return_qnty=0; $total_yarn_return_qnty_out=0;
				$sql_out="SELECT a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no, sum(d.cons_quantity) as returned_qnty, c.lot, c.yarn_type, c.id as prod_id, c.product_name_details, d.brand_id 
				from inv_receive_master a, product_details_master c, inv_transaction d 
				where a.id=d.mst_id  and d.prod_id=c.id  and d.transaction_type=4 and d.item_category=1 and a.entry_form=9 and a.booking_id=$boking_id and a.booking_without_order=1 and a.receive_basis=1 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0  group by a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no,c.lot, c.yarn_type, c.id, c.product_name_details, d.brand_id 
				union all
				SELECT a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no, sum(d.cons_quantity) as returned_qnty, c.lot, c.yarn_type, c.id as prod_id, c.product_name_details, d.brand_id 
				from inv_receive_master a, product_details_master c, inv_transaction d, ppl_yarn_requisition_breakdown e
				where a.id=d.mst_id  and d.prod_id=c.id and a.booking_id=e.requisition_id and e.item_id=d.prod_id and c.id=e.item_id and d.transaction_type=4 and d.item_category=1 and a.entry_form=9 and a.booking_without_order=1 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and e.order_id=$boking_id
				group by a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no,c.lot, c.yarn_type, c.id, c.product_name_details, d.brand_id";
				//echo $sql_out;
				
                $result_out=sql_select($sql_out);
				if(!empty($result_out))
				{
					?>
	                <thead>
	                    <th colspan="10"><b>Yarn Return</b></th>
	                </thead>
	                <thead>
	                	<th width="105">Return Id</th>
	                    <th width="90">Return From</th>
	                    <th width="105">Booking No</th>
	                    <th width="80">Challan No</th>
	                    <th width="60">Return Date</th>
	                    <th width="70">Brand</th>
	                    <th width="60">Lot No</th>
	                    <th width="180">Yarn Description</th>
	                    <th width="70">Return Qnty (In)</th>
	                    <th>Return Qnty (Out)</th>
	               	</thead>
	                <?
				}
				foreach($result_out as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				
					if($row[csf('knitting_source')]==1) $return_from=$company_library[$row[csf('knitting_company')]]; 
					else if($row[csf('knitting_source')]==3) $return_from=$supplier_details[$row[csf('knitting_company')]];
					else $return_from="&nbsp;";
						
                    $yarn_returned=$row[csf('returned_qnty')];
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="105"><p><? echo $row[csf('recv_number')]; ?></p></td>
                        <td width="90"><p><? echo $return_from; ?></p></td>
                        <td width="105"><p><? echo $booking_no_details[$boking_id];//$row[csf('booking_no')];?></p></td>
                        <td width="80"><p><? echo $row[csf('challan_no')]; ?></p></td>
                        <td width="70"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
                        <td width="60" align="center"><? $brand_arr[$row[csf("brand_id")]]; ?></td>
                        <td width="60"><p><? echo $row[csf('lot')]; ?></p></td>
                        <td width="180"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                        <td align="right" width="70">
							<? 
								if($row[csf('knitting_source')]!=3)
								{
									echo number_format($yarn_returned,2);
									$total_yarn_return_qnty+=$yarn_returned;
								}
								else echo "&nbsp;";
                            ?>
                        </td>
                        <td align="right">
							<? 
								if($row[csf('knitting_source')]==3)
								{ 
									echo number_format($yarn_returned,2); 
									$total_yarn_return_qnty_out+=$yarn_returned;
								}
								else echo "&nbsp;";
                            ?>
                        </td>
                    </tr>
                	<?
                	$i++;
                }
                ?>
                <tr style="font-weight:bold">
                    <td align="right" colspan="9">Total Issue Rtn</td>
                    <td align="right"><? echo number_format(($total_yarn_return_qnty+$total_yarn_return_qnty_out),2);?></td>
                </tr>
                <tfoot>    
                    <tr>
                        <th align="right" colspan="9">Net Issue</th>
                        <th align="right"><? echo number_format(($total_yarn_issue_qnty+$total_yarn_issue_qnty_out)-($total_yarn_return_qnty+$total_yarn_return_qnty_out),2);?></th>
                    </tr>
                </tfoot>
            </table>	
		</div>
	</fieldset> 
	<?
	exit();
}

if($action=="knitting_production")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	$machine_arr = return_library_array("select id, machine_no from lib_machine_name","id","machine_no");
	$supplier_details=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	
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
	<fieldset style="width:990px;">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="970" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="12"><b>Grey Receive Info</b></th>
				</thead>
				<thead>
                	<th width="30">SL</th>
                    <th width="110">Receive Id</th>
                    <th width="95">Prod. Basis</th>
                    <th width="110">Product Details</th>
                    <th width="100">Booking / Program No</th>
                    <th width="60">Machine No</th>
                    <th width="70">Production Date</th>
                    <th width="75">Inhouse Production</th>
                    <th width="75">Outside Production</th>
                    <th width="75">Total Prod. Qnty</th>
                    <th width="70">Challan No</th>
                    <th>Kniting Com.</th>
				</thead>
            </table>
            <div style="width:990px; max-height:330px; overflow-y:scroll" id="scroll_body">
                <table border="1" class="rpt_table" rules="all" width="970" cellpadding="0" cellspacing="0">
                    <?
					$receive_basis=array(0=>"Independent",1=>"Fabric Booking",2=>"Knitting Plan");
                    $i=1; $total_receive_qnty=0;
					$product_arr=return_library_array( "select id,product_name_details from  product_details_master", 'id', 'product_name_details'); 

					$sql="SELECT a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.febric_description_id, b.machine_no_id, b.prod_id, sum(b.grey_receive_qnty) as quantity 
					from inv_receive_master a, pro_grey_prod_entry_dtls b, lib_yarn_count_determina_mst c
					where a.id=b.mst_id and a.receive_basis=1 and a.booking_without_order=1 and a.entry_form=2 and a.booking_id=$boking_id and b.color_id='$color' and b.febric_description_id=c.id and c.construction='$construction' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.febric_description_id=$deter_id
					group by a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.febric_description_id, b.machine_no_id, b.prod_id
					union all 
					select a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.febric_description_id, b.machine_no_id, b.prod_id, 
					sum(b.grey_receive_qnty) as quantity 
					from inv_receive_master a, pro_grey_prod_entry_dtls b, ppl_planning_entry_plan_dtls c, wo_non_ord_samp_booking_mst e, lib_yarn_count_determina_mst d 
					where a.id=b.mst_id and a.booking_id=c.dtls_id and c.booking_no=e.booking_no and a.receive_basis=2 and a.booking_without_order=1 and a.entry_form=2 and e.id=$boking_id and b.color_id='$color' and b.febric_description_id=d.id and d.construction='$construction' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and b.febric_description_id=$deter_id
					group by a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.febric_description_id, b.machine_no_id, b.prod_id";
					
                    $result=sql_select($sql);

					foreach($result as $row)
                    {
						$all_deter_arr[$row[csf('febric_description_id')]]=$row[csf('febric_description_id')];
					}


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
						//echo $composition_arr[$row[csf('febric_description_id')]]."====".$compositions."<br>";
						if($composition_arr[$row[csf('febric_description_id')]] == $compositions)
						{
							if ($i%2==0)  
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";	
						
							$total_receive_qnty+=$row[csf('quantity')];
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="30"><? echo $i; ?></td>
								<td width="110"><p><? echo $row[csf('recv_number')]; ?></p></td>
								<td width="95"><p><? echo $receive_basis[$row[csf('receive_basis')]]; ?></p></td>
								<td width="110"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
								<td width="100"><p><? echo $row[csf('booking_no')]; ?>&nbsp;</p></td>
								<td width="60"><p>&nbsp;<? echo $machine_arr[$row[csf('machine_no_id')]]; ?></p></td>
								<td width="70" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
								<td align="right" width="75">
									<? 
										if($row[csf('knitting_source')]!=3)
										{
											echo number_format($row[csf('quantity')],2,'.','');
											$total_receive_qnty_in+=$row[csf('quantity')];
										}
										else echo "&nbsp;";
									?>
								</td>
								<td align="right" width="75">
									<? 
										if($row[csf('knitting_source')]==3)
										{
											echo number_format($row[csf('quantity')],2,'.','');
											$total_receive_qnty_out+=$row[csf('quantity')];
										}
										else echo "&nbsp;";
									?>
								</td>
								<td align="right" width="75"><? echo number_format($row[csf('quantity')],2,'.',''); ?></td>
								<td width="70"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
								<td><p><? if ($row[csf('knitting_source')]==1) echo $company_library[$row[csf('knitting_company')]]; else if ($row[csf('knitting_source')]==3) echo $supplier_details[$row[csf('knitting_company')]]; ?></p></td>
							</tr>
							<?
							$i++;
						}
                    }
                    ?>
                    <tfoot>
                        <th colspan="7" align="right">Total</th>
                        <th align="right"><? echo number_format($total_receive_qnty_in,2,'.',''); ?></th>
                        <th align="right"><? echo number_format($total_receive_qnty_out,2,'.',''); ?></th>
                        <th align="right"><? echo number_format($total_receive_qnty,2,'.',''); ?></th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                    </tfoot>
                </table>
            </div>	
        </div>
	</fieldset>   
	<?
	exit();
}


if($action=="grey_receive_prod")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);

	$machine_arr = return_library_array("select id, machine_no from lib_machine_name","id","machine_no");	
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
		<fieldset style="width:990px;">
			<div id="report_container">
				<table border="1" class="rpt_table" rules="all" width="970" cellpadding="0" cellspacing="0">
					<thead>
						<th colspan="12"><b>Grey Receive Info</b></th>
					</thead>
					<thead>
	                	<th width="30">SL</th>
	                    <th width="110">Receive Id</th>
	                    <th width="95">Prod. Basis</th>
	                    <th width="110">Product Details</th>
	                    <th width="100">Booking / Program No</th>
	                    <th width="60">Machine No</th>
	                    <th width="70">Production Date</th>
	                    <th width="75">Inhouse Production</th>
	                    <th width="75">Outside Production</th>
	                    <th width="75">Total Prod. Qnty</th>
	                    <th width="70">Challan No</th>
	                    <th>Kniting Com.</th>
					</thead>
	             </table>
	             <div style="width:990px; max-height:330px; overflow-y:scroll" id="scroll_body">
	                 <table border="1" class="rpt_table" rules="all" width="970" cellpadding="0" cellspacing="0">
	                    <?
						$receive_basis=array(0=>"Independent",1=>"Fabric Booking",2=>"Knitting Plan",9=>"Production");
						$i=1; $total_receive_qnty=0;
						$product_arr=return_library_array( "select id,product_name_details from  product_details_master",'id','product_name_details'); 

						$sql="SELECT a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.challan_no, a.knitting_company, b.machine_no_id, b.febric_description_id, b.prod_id, c.booking_no, sum(c.qnty) as quantity 
						from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, lib_yarn_count_determina_mst d 
						where c.entry_form=58 and a.id=b.mst_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=c.mst_id and b.id=c.dtls_id and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id=$boking_id and c.booking_without_order=1 and b.febric_description_id=d.id and b.color_id='$color' and d.construction='$construction' 
						group by c.booking_no, a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.challan_no, a.knitting_company, b.machine_no_id, b.febric_description_id, b.prod_id";
						$result=sql_select($sql);
						foreach($result as $row)
						{
							$all_deter_arr[$row[csf('febric_description_id')]]=$row[csf('febric_description_id')];
						}

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
							if ($i%2==0)  
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";	

							if($composition_arr[$row[csf('febric_description_id')]] == $compositions)
							{
								$total_receive_qnty+=$row[csf('quantity')];
							?>
								<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
									<td width="30"><? echo $i; ?></td>
									<td width="110"><p><? echo $row[csf('recv_number')]; ?></p></td>
									<td width="95"><p><? echo $receive_basis[$row[csf('receive_basis')]]; ?></p></td>
									<td width="110"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
									<td width="100"><p><? echo $booking_no_details[$boking_id];//echo $row[csf('booking_no')]; ?>&nbsp;</p></td>
									<td width="60"><p>&nbsp;<? echo $machine_arr[$row[csf('machine_no_id')]]; ?></p></td>
									<td width="70" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
									<td align="right" width="75">
										<? 
											if($row[csf('knitting_source')]!=3)
											{
												echo number_format($row[csf('quantity')],2,'.','');
												$total_receive_qnty_in+=$row[csf('quantity')];
											}
											else echo "&nbsp;";
										?>
									</td>
									<td align="right" width="75">
										<? 
											if($row[csf('knitting_source')]==3)
											{
												echo number_format($row[csf('quantity')],2,'.','');
												$total_receive_qnty_out+=$row[csf('quantity')];
											}
											else echo "&nbsp;";
										?>
									</td>
									<td align="right" width="75"><? echo number_format($row[csf('quantity')],2,'.',''); ?></td>
									<td width="70"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
									<td><p><? if ($row[csf('knitting_source')]==1) echo $company_library[$row[csf('knitting_company')]]; else if ($row[csf('knitting_source')]==3) echo $supplier_details[$row[csf('knitting_company')]]; ?></p></td>
								</tr>
							<?
							$i++;
							}
						}
					
	                    ?>
	                    <tfoot>
	                        <th colspan="7" align="right">Total</th>
	                        <th align="right"><? echo number_format($total_receive_qnty_in,2,'.',''); ?></th>
	                        <th align="right"><? echo number_format($total_receive_qnty_out,2,'.',''); ?></th>
	                        <th align="right"><? echo number_format($total_receive_qnty,2,'.',''); ?></th>
	                        <th>&nbsp;</th>
	                        <th>&nbsp;</th>
	                    </tfoot>
	                </table>
	            </div>	
	        </div>
		</fieldset>   
	<?
	exit();
}

if($action=="batch_qty_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$machine_arr = return_library_array("select id, machine_no from lib_machine_name","id","machine_no");
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
	<fieldset style="width:990px;">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="760" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<th colspan="9"><b>Batch Info</b></th>
				</thead>
				<thead>
                	<th width="30">SL</th>
                    <th width="100">Batch ID </th>
                    <th width="120">Batch Name</th>
                    <th width="120">Batch Color</th>
                    <th width="120">Booking No</th>
                    <th width="90">Batch   Date</th>
                    <th width="90">Batch Weight </th>
                   <th width="90">Batch Qnty </th>
				</thead>
             </table>
             <div style="width:782px; max-height:330px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="760" cellpadding="0" cellspacing="0">
                    <?
                    $i=1;
					$color_array=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
					$sql="SELECT a.id, a.batch_no, a.booking_no_id, e.booking_no, a.color_id, sum(b.batch_qnty) as batch_qnty, a.batch_date as  batch_date, a.batch_weight, c.detarmination_id as febric_description_id from  pro_batch_create_mst a,pro_batch_create_dtls b, product_details_master c, lib_yarn_count_determina_mst d, wo_non_ord_samp_booking_mst e where a.id=b.mst_id and a.booking_without_order=1 and b.prod_id=c.id and c.detarmination_id=d.id and a.booking_no_id=e.id and d.construction='$construction' and a.booking_no_id=$boking_id and a.color_id='$color' and b.status_active=1 and b.is_deleted=0 group by a.id, a.batch_no, a.booking_no_id, e.booking_no, a.color_id, a.batch_weight, a.batch_date, c.detarmination_id";

                   $result=sql_select($sql);
				   foreach($result as $row)
				   {
					   $all_deter_arr[$row[csf('febric_description_id')]]=$row[csf('febric_description_id')];
				   }

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
						if($composition_arr[$row[csf('febric_description_id')]] == $compositions)
						{
							if ($i%2==0)  
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";	
						
							$total_receive_qnty+=$row[csf('batch_qnty')];
							?>
								<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
									<td width="30"><? echo $i; ?></td>
									<td width="100"><p><? echo $row[csf('id')]; ?></p></td>
									<td width="120"><p><? echo $row[csf('batch_no')]; ?></p></td>
									<td width="120"><p><? echo $color_array[$row[csf('color_id')]]; ?></p></td>
									<td width="120"><p><? echo $row[csf('booking_no')]; ?>&nbsp;</p></td>
									<td width="90" align="center"><? echo change_date_format($row[csf('batch_date')]); ?></td>
									<td  width="90" align="center"><? echo $row[csf('batch_weight')]; ?></td>
									<td  width="90" align="right"><? echo number_format($row[csf('batch_qnty')],2,'.',''); ?></td>
								</tr>
							<?
							$i++;
						}
                    }
                    ?>
                    <tfoot>
                        <th colspan="7" align="right">Total</th>
                       
                        <th align="right"><? echo number_format($total_receive_qnty,2,'.',''); ?></th>
                        
                    </tfoot>
                </table>
            </div>	
        </div>
	</fieldset>   
	<?
	exit();
}

if($action=="grey_fabric_transfer")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$sql_tr_in = sql_select("SELECT a.id, a.transfer_system_id, a.transfer_date, a.challan_no, a.transfer_criteria, b.po_breakdown_id, sum(b.qnty) as tranfer_in
	from  inv_item_transfer_mst a, pro_roll_details b, inv_item_transfer_dtls c, pro_roll_details a, pro_grey_prod_entry_dtls e 
	where a.id=b.mst_id and b.dtls_id=c.id and c.item_category=13 and b.entry_form in (180,110) and b.booking_without_order=1 
	and b.barcode_no=a.barcode_no and a.entry_form=2 and a.dtls_id=e.id and b.po_breakdown_id='$boking_id' and e.febric_description_id=$deter_id and e.color_id='$color'
	group by a.id, a.transfer_system_id, a.transfer_date, a.challan_no, a.transfer_criteria");

	foreach ($sql_tr_in as $row ) 
	{
		$data_array[$row[csf("transfer_system_id")]]["transfer_system_id"]=$row[csf("transfer_system_id")];
		$data_array[$row[csf("transfer_system_id")]]["transfer_date"]=$row[csf("transfer_date")];
		$data_array[$row[csf("transfer_system_id")]]["challan_no"]=$row[csf("challan_no")];
		$data_array[$row[csf("transfer_system_id")]]["challan_no"]=$row[csf("challan_no")];
		$data_array[$row[csf("transfer_system_id")]]["transfer_criteria"]=$row[csf("transfer_criteria")];
		$data_array[$row[csf("transfer_system_id")]]["tranfer_in"] +=$row[csf("tranfer_in")];
	}

	$sql_tr_out = sql_select("SELECT a.id, a.transfer_system_id, a.transfer_date, a.challan_no, a.transfer_criteria, b.order_id, sum(e.qnty) as tranfer_out 
	from inv_item_transfer_mst a, inv_transaction b, inv_item_transfer_dtls c, pro_roll_details e, pro_roll_details f,pro_grey_prod_entry_dtls g 
	where a.id=b.mst_id and b.id=c.trans_id 
	and b.item_category=13 and b.transaction_type in(6) and c.id=e.dtls_id and e.entry_form in (180,183) and e.barcode_no=f.barcode_no 
	and f.entry_form=2 and f.dtls_id=g.id and b.order_id='$boking_id' and g.febric_description_id=$deter_id and g.color_id='$color'
	group by a.id, a.transfer_system_id, a.transfer_date, a.challan_no, a.transfer_criteria, b.order_id");

	foreach ($sql_tr_out as $row ) 
	{
		$data_array[$row[csf("transfer_system_id")]]["transfer_system_id"]=$row[csf("transfer_system_id")];
		$data_array[$row[csf("transfer_system_id")]]["transfer_date"]=$row[csf("transfer_date")];
		$data_array[$row[csf("transfer_system_id")]]["challan_no"]=$row[csf("challan_no")];
		$data_array[$row[csf("transfer_system_id")]]["challan_no"]=$row[csf("challan_no")];
		$data_array[$row[csf("transfer_system_id")]]["transfer_criteria"]=$row[csf("transfer_criteria")];
		$data_array[$row[csf("transfer_system_id")]]["tranfer_out"] +=$row[csf("tranfer_out")];
	}

	?>
	<fieldset style="width:770px; margin-left:7px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="750" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="8"><b>Grey Fabric Transfer Information</b></th>
				</thead>
				<thead>
                	<th width="50">SL</th>
                    <th width="120">System Id</th>
                    <th width="80">Transfered Date</th>
                    <th width="80">Challan No</th>
                    <th width="80">Transfered Type</th>
                    <th width="80">Transfered In Qnty</th>
                    <th width="80">Transfered Out Qnty</th>
                    <th width="80">Net Transfered Qnty</th>
				</thead>
             </table>
             <div style="width:767px; max-height:320px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="750" cellpadding="0" cellspacing="0">
				<?
				$i=1;
				if(empty($result_transfer_mst))
				{
					foreach($data_array as $key=>$row)
					{
						 if ($i%2==0)  
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";	
						?>    
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td width="50" align="center"><?php echo $i; ?></td>
							<td width="120"><p><?php echo $row['transfer_system_id'];?></p></td>
							<td width="80" align="center"><?php echo change_date_format($row['transfer_date']) ;?></td>
							<td width="80" align="center"><?php echo $row['challan_no'];?></td>
							<td width="80" align="center"><?php echo $item_transfer_criteria[$row['transfer_criteria']];?></td>
                            <td width="80" align="center">
                            	<?php 
								echo $transferIn = $row['tranfer_in'];
								$totalTransferIn +=$transferIn;
								?>
                            </td>
                            <td width="80" align="center">
                            	<?php 
								echo $transferOut = $row['tranfer_out'];
								$totalTransferOut +=$transferOut; 
								?>
                            </td>
                            <td width="80" align="center">
                            <?php 
								echo $netTranferQty = ($row['tranfer_in']-$row['tranfer_out']);
								$totalNettranferQty += $netTranferQty;
							?>
							</td>
						</tr>
				   <? 
						$i++;
				   } 
				} else{
					echo "<h3 style='color:red;'> Transfer data not found!!</h3>";	
				}
			   ?>
                       
                    <tfoot>
                        <th colspan="5">Total</th>
                        <th style="text-align:center;"><?php echo $totalTransferIn; ?></th>
                        <th style="text-align:center;"><?php echo $totalTransferOut; ?></th>
                        <th style="text-align:center;"><?php echo $totalNettranferQty; ?></th>
                    </tfoot>
                </table>
            </div>	
        </div>
	</fieldset>   
	<?
	exit();
}

if($action=="grey_issue")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);

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
		<fieldset style="width:880px; margin-left:3px">
			<div id="report_container">
				<table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
	                <thead>
	                	<tr>
	                        <th colspan="9"><b>Grey Issue Info</b></th>
	                    </tr>
	                    <tr>
	                        <th width="40">SL</th>
	                        <th width="120">Issue Id</th>
	                        <th width="100">Issue Purpose</th>
	                        <th width="100">Issue To</th>
	                        <th width="115">Booking No</th>
	                        <th width="90">Batch No</th>
	                        <th width="80">Issue Date</th>
	                        <th width="100">Issue Qnty (In)</th>
	                        <th>Issue Qnty (Out)</th>
	                    </tr>
					</thead>
	             </table>
	             <div style="width:877px; max-height:320px; overflow-y:scroll" id="scroll_body">
	                 <table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
	                    <?
	                    $i=1; $issue_to='';

	                    /* if($booking_type=="withoutRoll")
	                    {
		                    $sql="select a.issue_number, a.issue_date, a.issue_purpose, a.knit_dye_source, a.knit_dye_company, a.booking_no, a.batch_no, sum(b.issue_qnty) as quantity 
							from inv_issue_master a, inv_grey_fabric_issue_dtls b
							where a.id=b.mst_id and a.entry_form=16 and a.booking_id=$id and a.issue_basis=1 and a.issue_purpose=8 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
							group by a.issue_number, a.issue_date, a.issue_purpose, a.knit_dye_source, a.knit_dye_company, a.booking_no, a.batch_no";
						}
 */
$supplier_details=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );

$sql="SELECT d.issue_number, d.issue_date, d.issue_purpose, d.knit_dye_source, d.knit_dye_company, d.batch_no, c.booking_no, sum(c.qnty) as quantity from product_details_master a, inv_grey_fabric_issue_dtls b, pro_roll_details c, inv_issue_master d where a.id=b.prod_id and b.id=c.dtls_id  and c.mst_id=d.id and d.entry_form=61 and c.entry_form in(61) and c.booking_without_order=1 and c.status_active=1 and a.detarmination_id=$deter_id and b.color_id='$color' and c.po_breakdown_id=$boking_id group by d.issue_number, d.issue_date, d.issue_purpose, d.knit_dye_source, d.knit_dye_company, d.batch_no, c.booking_no";
						// echo $sql;
	                    $result=sql_select($sql);
	        			foreach($result as $row)
	                    {
	                        if ($i%2==0)  
	                            $bgcolor="#E9F3FF";
	                        else
	                            $bgcolor="#FFFFFF";	
	                    
	                        if($row[csf('knit_dye_source')]==1) 
	                        {
	                            $issue_to=$company_library[$row[csf('knit_dye_company')]]; 
	                        }
	                        else if($row['knit_dye_source']==3) 
	                        {
	                            $issue_to=$supplier_details[$row[csf('knit_dye_company')]];
	                        }
	                        else
	                            $issue_to="&nbsp;";
	                    
	                    ?>
	                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
	                            <td width="40"><? echo $i; ?></td>
	                            <td width="120"><p><? echo $row[csf('issue_number')]; ?></p></td>
	                            <td width="100"><? echo $yarn_issue_purpose[$row[csf('issue_purpose')]]; ?></td>
	                            <td width="100"><p><? echo $issue_to; ?></p></td>
	                            <td width="115"><? echo $row[csf('booking_no')]; ?>&nbsp;</td>
	                            <td width="90"><p><? echo $batch_details[$row[csf('batch_no')]]; ?>&nbsp;</p></td>
	                            <td width="80" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
	                            <td width="100" align="right">
									<?
	                                    if($row[csf('knit_dye_source')]!=3)
	                                    {
	                                        echo number_format($row[csf('quantity')],2);
	                                        $total_issue_qnty+=$row[csf('quantity')];
	                                    }
	                                    else echo "&nbsp;";
	                                ?>
	                            </td>
	                            <td align="right">
	                                <?
	                                    if($row[csf('knit_dye_source')]==3)
	                                    {
	                                        echo number_format($row[csf('quantity')],2);
	                                        $total_issue_qnty_out+=$row[csf('quantity')];
	                                    }
	                                    else echo "&nbsp;";
	                                ?>
	                            </td>
	                        </tr>
	                    <?
	                    $i++;
	                    }
	                    ?>
	                    <tfoot>
	                    	<tr>
	                            <th colspan="7" align="right">Total</th>
	                            <th align="right"><? echo number_format($total_issue_qnty,2); ?></th>
	                            <th align="right"><? echo number_format($total_issue_qnty_out,2); ?></th>
	                        </tr>
	                        <tr>
	                            <th colspan="7" align="right">Grand Total</th>
	                            <th align="right" colspan="2"><? echo number_format($total_issue_qnty+$total_issue_qnty_out,2); ?></th>
	                        </tr>
	                    </tfoot>
	                </table>
	            </div>	
	        </div>
		</fieldset>   
	<?
	exit();
}

if($action=="batch_qty_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$machine_arr = return_library_array("select id, machine_no from lib_machine_name","id","machine_no");
	$color_array=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
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
	<fieldset style="width:990px;">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="760" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<th colspan="9"><b>Batch Info</b></th>
				</thead>
				<thead>
                	<th width="30">SL</th>
                    <th width="100">Batch ID </th>
                    <th width="120">Batch Name</th>
                    <th width="120">Batch Color</th>
                    <th width="120">Booking No</th>
                    <th width="90">Batch   Date</th>
                    <th width="90">Batch Weight </th>
                   <th width="90">Batch Qnty </th>
				</thead>
             </table>
             <div style="width:782px; max-height:330px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="760" cellpadding="0" cellspacing="0">
                    <?
                    $i=1; 
                    $sql="select a.id,a.batch_no,a.booking_no_id,a.booking_no,a.color_id,sum(b.batch_qnty) as batch_qnty,a.batch_date as  batch_date,a.batch_weight 
					from  pro_batch_create_mst a, pro_batch_create_dtls b, product_details_master c
					where a.id=b.mst_id and a.booking_without_order=1 and b.prod_id=c.id
					and a.booking_no_id=$boking_id and a.color_id='$color_id' and c.detarmination_id=$deter_id
					group by a.id,a.batch_no,a.booking_no_id,a.booking_no,a.color_id,a.batch_weight,a.batch_date";

                   $result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	
                    
                        $total_receive_qnty+=$row[csf('batch_qnty')];
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="100"><p><? echo $row[csf('id')]; ?></p></td>
                            <td width="120"><p><? echo $row[csf('batch_no')]; ?></p></td>
                            <td width="120"><p><? echo $color_array[$row[csf('color_id')]]; ?></p></td>
                            <td width="120"><p><? echo $row[csf('booking_no')]; ?>&nbsp;</p></td>
                            <td width="90" align="center"><? echo change_date_format($row[csf('batch_date')]); ?></td>
                        	<td  width="90" align="center"><? echo $row[csf('batch_weight')]; ?></td>
                            <td  width="90" align="right"><? echo number_format($row[csf('batch_qnty')],2,'.',''); ?></td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tfoot>
                        <th colspan="7" align="right">Total</th>
                       
                        <th align="right"><? echo number_format($total_receive_qnty,2,'.',''); ?></th>
                        
                    </tfoot>
                </table>
            </div>	
        </div>
	</fieldset>   
	<?
	exit();
}

if($action=="dying_qty_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$machine_arr = return_library_array("select id, machine_no from lib_machine_name","id","machine_no");
	$color_array=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
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
		<fieldset style="width:990px;">
			<div id="report_container">
				<table border="1" class="rpt_table" rules="all" width="960" cellpadding="0" cellspacing="0" align="center">
					<thead>
						<th colspan="10"><b>Batch Info</b></th>
					</thead>
					<thead>
	                	<th width="30">SL</th>
	                    <th width="80">Batch ID </th>
	                    <th width="120">Batch Name</th>
	                    <th width="120">Batch Color</th>
	                    <th width="120">Booking No</th>
	                    <th width="90">Batch   Date</th>
	                    <th width="90">Batch Weight </th>
	                    <th width="90">Batch Qnty </th>
	                    <th width="90">Process </th>
	                    <th width="">Productuon Date</th>
					</thead>
	             </table>
	           	  <div style="width:990px; max-height:330px; overflow-y:scroll" id="scroll_body">
	                 <table border="1" class="rpt_table" rules="all" width="960" cellpadding="0" cellspacing="0">
	                    <?
	                    $i=1; 
	                    $sql="SELECT a.id, a.batch_no, a.booking_no_id,a.booking_no,a.color_id,sum(b.batch_qnty) as batch_qnty,a.batch_date as  batch_date,a.batch_weight,c.process_id, c.process_end_date 
						from  pro_batch_create_mst a,pro_batch_create_dtls b,pro_fab_subprocess c, product_details_master d 
						where c.batch_id=a.id and c.load_unload_id=2 and a.id=b.mst_id and a.booking_without_order=1 and a.booking_no_id=$boking_id and a.color_id='$color'	and b.prod_id=d.id and d.detarmination_id=$deter_id
						group by a.id,a.batch_no,a.booking_no_id, a.booking_no, a.color_id,a.batch_weight, a.batch_date,c.process_id, c.process_end_date ";
		
						//echo $sql;
	                   $result=sql_select($sql);
	        			foreach($result as $row)
	                    {
	                        if ($i%2==0)  
	                            $bgcolor="#E9F3FF";
	                        else
	                            $bgcolor="#FFFFFF";	
	                    
	                        $total_receive_qnty+=$row[csf('batch_qnty')];
	                    ?>
	                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
	                            <td width="30"><? echo $i; ?></td>
	                            <td width="80"><p><? echo $row[csf('id')]; ?></p></td>
	                            <td width="120"><p><? echo $row[csf('batch_no')]; ?></p></td>
	                            <td width="120"><p><? echo $color_array[$row[csf('color_id')]]; ?></p></td>
	                            <td width="120"><p><? echo $row[csf('booking_no')]; ?>&nbsp;</p></td>
	                            <td width="90" align="center"><? echo change_date_format($row[csf('batch_date')]); ?></td>
	                        	<td  width="90" align="center"><? echo $row[csf('batch_weight')]; ?></td>
	                            <td  width="90" align="right"><? echo number_format($row[csf('batch_qnty')],2,'.',''); ?></td>
	                            <td width="90"><p><? echo $conversion_cost_head_array[$row[csf('process_id')]]; ?>&nbsp;</p></td>
	                            <td width="" align="center"><? echo change_date_format($row[csf('process_end_date')]); ?></td>
	                        </tr>
	                    <?
	                    $i++;
	                    }
	                    ?>
	                    <tfoot>
	                        <th colspan="7" align="right">Total</th>
	                       
	                        <th align="right"><? echo number_format($total_receive_qnty,2,'.',''); ?></th>
	                        <th align="right"></th>
	                        <th  align="right"></th>
	                    </tfoot>
	                </table>
	            </div>
	        </div>
		</fieldset>   
	<?
	exit();
}

if($action=="finish_feb_prod")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);

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
		<!--<div style="width:885px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>-->
		<fieldset style="width:880px; margin-left:3px">
			<div id="report_container">
				<table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
					<thead>
						<th colspan="9"><b>Fabric Receive Info</b></th>
					</thead>
					<thead>
	                	<th width="30">SL</th>
	                    <th width="120">System Id</th>
	                    <th width="75">Rec. Date</th>
	                    <th width="80">Rec. Basis</th>
	                    <th width="90">Batch No</th>
	                    <th width="90">Dyeing Source</th>
	                    <th width="100">Dyeing Company</th>
	                    <th width="90">Receive Qnty</th>
	                    <th>Fabric Description</th>
					</thead>
	             </table>
	             <div style="width:877px; max-height:320px; overflow-y:scroll" id="scroll_body">
	                 <table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
	                    <?
	                    $i=1;
	                    $total_fabric_recv_qnty=0; $dye_company='';
	                   /*  if ($mydata[1]=="withoutRoll")
	                    {
		                    $sql="select a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, b.batch_id, b.prod_id, sum(b.receive_qnty) as quantity from inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_batch_create_mst c where a.id=b.mst_id and b.batch_id=c.id  and a.receive_basis=5 and a.entry_form=7 and c.booking_without_order=1 and c.booking_no_id=$boking_id and b.color_id='$color_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, b.batch_id, b.prod_id";
						}
						else
						{
							$sql="select a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, b.batch_id, b.prod_id, sum(d.qnty) as quantity from inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_batch_create_mst c, pro_roll_details d where a.id=b.mst_id and b.batch_id=c.id and a.id=d.mst_id and b.id=d.dtls_id and d.barcode_no in ($barcode_ids) and d.entry_form=66 and d.status_active=1 and d.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, b.batch_id, b.prod_id";
						} */

						$supplier_details=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
						$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );

						$sql="SELECT a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, a.entry_form, c.batch_no, d.product_name_details, sum(b.receive_qnty) as production_qty
from inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_batch_create_mst c, product_details_master d
where a.id=b.mst_id and b.batch_id=c.id and a.entry_form in (7) and b.fabric_description_id=$deter_id and b.color_id='$color' 
and c.booking_no_id=$boking_id and b.prod_id=d.id
group by a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, c.batch_no, d.product_name_details, a.entry_form";

						//echo $sql;
	                    $result=sql_select($sql);
	        			foreach($result as $row)
	                    {
	                        if ($i%2==0)  
	                            $bgcolor="#E9F3FF";
	                        else
	                            $bgcolor="#FFFFFF";	
	                    
	                        if($row[csf('knitting_source')]==1) 
	                        {
	                            $dye_company=$company_library[$row[csf('knitting_company')]]; 
	                        }
	                        else if($row['knitting_source']==3) 
	                        {
	                            $dye_company=$supplier_details[$row[csf('knitting_company')]];
	                        }
	                        else
	                            $dye_company="&nbsp;";
	                    
	                        $total_fabric_recv_qnty+=$row[csf('production_qty')];
	                    ?>
	                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
	                            <td width="30"><? echo $i; ?></td>
	                            <td width="120"><p><? echo $row[csf('recv_number')]; ?></p></td>
	                            <td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
	                            <td width="80"><? echo $receive_basis_arr[$row[csf('receive_basis')]]; ?></td>
	                            <td width="90"><p><? echo $row[csf('batch_no')]; ?></p></td>
	                            <td width="90"><? echo $knitting_source[$row[csf('knitting_source')]]; ?></td>
	                            <td width="100"><p><? echo $dye_company; ?></p></td>
	                            <td width="90" align="right"><? echo number_format($row[csf('production_qty')],2); ?></td>
	                            <td><p><? echo $row[csf('product_name_details')]; ?></p></td>
	                        </tr>
	                    <?
	                    $i++;
	                    }
	                    ?>
	                    <tfoot>
	                        <th colspan="7" align="right">Total</th>
	                        <th align="right"><? echo number_format($total_fabric_recv_qnty,2); ?></th>
	                        <th>&nbsp;</th>
	                    </tfoot>
	                </table>
	            </div>	
	        </div>
		</fieldset>   
	<?
	exit();
}

if($action=="finish_fabric_delivery_to_store")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);

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
		<!--<div style="width:885px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>-->
		<fieldset style="width:980px; margin-left:3px">
			<div id="report_container">
				<table border="1" class="rpt_table" rules="all" width="950" cellpadding="0" cellspacing="0">
					<thead>
						<th colspan="9"><b>Fabric Delivery Info</b></th>
					</thead>
					<thead>
	                	<th width="30">SL</th>
	                    <th width="120">System Id</th>
	                    <th width="75">Prd. date</th>
	                    <th width="120">Booking No</th>
	                    <th width="120">Knitting Source </th>
	                    <th width="120">Knitting Company</th>
	                    <th width="90">Color</th>
	                    <th width="100">Batch No</th>
	                    
	                    <th width="">Delivery Qty</th>
					</thead>
	             </table>
	             <div style="width:977px; max-height:320px; overflow-y:scroll" id="scroll_body">
	                 <table border="1" class="rpt_table" rules="all" width="950" cellpadding="0" cellspacing="0">
	                    <?
	                    $i=1;
	                    $total_fabric_recv_qnty=0; $dye_company='';

						$supplier_details=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
						$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
						$color_array=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );

	                    /* if($mydata[1]=="withoutRoll")
	                    {
							$sql="SELECT c.id as batch_id,e.color_id,d.sys_number as grey_sys_number,e.determination_id as determination_id,sum(e.current_delivery) as delivery_qty,c.booking_no_id,d.delevery_date as receive_date,d.knitting_source,d.knitting_company from pro_batch_create_mst c,pro_grey_prod_delivery_mst d,pro_grey_prod_delivery_dtls e where c.booking_no_id=$boking_id and e.color_id=$color_id and d.id=e.mst_id and e.mst_id=d.id and d.entry_form=54  and e.entry_form=54   and e.batch_id=c.id  and c.batch_against=3 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0   group by c.id,e.color_id,d.sys_number,e.determination_id,c.booking_no_id,d.delevery_date,d.knitting_source,d.knitting_company";
	                    }
	                    else
	                    {
	                    	$sql="SELECT e.grey_sys_number, e.determination_id, sum(f.qnty) as delivery_qty, a.receive_date,a.knitting_source,a.knitting_company from inv_receive_master a, pro_grey_prod_delivery_mst d, pro_grey_prod_delivery_dtls e, pro_roll_details f where d.id=e.mst_id and e.grey_sys_id=a.id and d.id=f.mst_id and e.id=f.dtls_id and f.barcode_no in ($barcode_ids) and e.color_id=$color_id and f.entry_form=67 and f.status_active=1 and f.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 group by  e.grey_sys_number, e.determination_id, a.receive_date, a.knitting_source, a.knitting_company";
	                    } */

						$sql="SELECT c.id as batch_id, c.batch_no, b.color_id, a.sys_number as grey_sys_number, b.determination_id, a.delevery_date as receive_date,a.knitting_source,a.knitting_company, c.booking_no, sum(b.current_delivery) as delivery_qty 
						from pro_grey_prod_delivery_mst a,pro_grey_prod_delivery_dtls b, pro_batch_create_mst c
						where a.id=b.mst_id and b.batch_id=c.id and a.entry_form=54  and b.entry_form=54 and c.batch_against=3 
						and c.booking_no_id=$boking_id and b.color_id=$color and b.determination_id=$deter_id
						and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  
						group by c.id, b.color_id,  c.batch_no, a.sys_number, b.determination_id, a.delevery_date,a.knitting_source,a.knitting_company, c.booking_no";

						//echo $sql;
	                    $result=sql_select($sql);
	        			foreach($result as $row)
	                    {
	                        if ($i%2==0)  
	                            $bgcolor="#E9F3FF";
	                        else
	                            $bgcolor="#FFFFFF";	
	                    
	                        if($row[csf('knitting_source')]==1) 
	                        {
	                            $dye_company=$company_library[$row[csf('knitting_company')]]; 
	                        }
	                        else if($row['knitting_source']==3) 
	                        {
	                            $dye_company=$supplier_details[$row[csf('knitting_company')]];
	                        }
	                        else
	                            $dye_company="&nbsp;";
	                    
	                        $total_fabric_recv_qnty+=$row[csf('delivery_qty')];
	                    ?>
	                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
	                            <td width="30"><? echo $i; ?></td>
	                            <td width="120"><p><? echo $row[csf('grey_sys_number')]; ?></p></td>
	                            <td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
	                            <td width="120"><? echo $row[csf('booking_no')]; ?></td>
	                            <td width="120" align="left"><? echo $knitting_source[$row[csf('knitting_source')]]; ?></td>
	                            <td width="120"><p><? echo $dye_company; ?></p></td>
	                            <td width="90"><? echo $color_array[$row[csf('color_id')]]; ?></td>
	                            <td width="100"><p><? echo $row[csf('batch_no')]; ?></p></td>
	                            
	                            <td  align="right"><? echo number_format($row[csf('delivery_qty')],2); ?></td>
	                        </tr>
	                    <?
	                    $i++;
	                    }
	                    ?>
	                    <tfoot>
	                        <th colspan="8" align="right">Total</th>
	                        <th align="right"><? echo number_format($total_fabric_recv_qnty,2); ?></th>
	                        
	                    </tfoot>
	                </table>
	            </div>	
	        </div>
		</fieldset>   
	<?
	exit();
}

if($action=="finish_fabric_receive_by_store")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
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
		<fieldset style="width:880px; margin-left:3px">
			<div id="report_container">
				<table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
					<thead>
						<th colspan="9"><b>Fabric Receive Info</b></th>
					</thead>
					<thead>
	                	<th width="30">SL</th>
	                    <th width="120">System Id</th>
	                    <th width="75">Rec. Date</th>
	                    <th width="80">Rec. Basis</th>
	                    <th width="90">Batch No</th>
	                    <th width="90">Dyeing Source</th>
	                    <th width="100">Dyeing Company</th>
	                    <th width="90">Receive Qnty</th>
	                    <th>Fabric Description</th>
					</thead>
	             </table>
	             <div style="width:877px; max-height:320px; overflow-y:scroll" id="scroll_body">
	                 <table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
	                    <?
						$receive_basis=array(0=>"Independent",1=>"Fabric Booking",2=>"Knitting Plan");
						$supplier_details=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
						$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
						$color_array=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
	                    $i=1;
	                    $total_fabric_recv_qnty=0; $dye_company='';

					$sql="SELECT c.booking_no_id,b.color_id,sum(b.receive_qnty) as production_qty,a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, b.batch_id, c.batch_no, b.prod_id, d.product_name_details
					from inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_batch_create_mst c, product_details_master d
					where a.id=b.mst_id and a.entry_form=37 and a.receive_basis =9 and b.batch_id=c.id  and c.batch_against=3 and b.prod_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and  c.booking_no_id=$boking_id and b.color_id='$color' and b.fabric_description_id=$deter_id group by c.booking_no_id,b.color_id,a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, b.batch_id, c.batch_no, b.prod_id, d.product_name_details";

	                    $result=sql_select($sql);
	        			foreach($result as $row)
	                    {
	                        if ($i%2==0)  
	                            $bgcolor="#E9F3FF";
	                        else
	                            $bgcolor="#FFFFFF";	
	                    
	                        if($row[csf('knitting_source')]==1) 
	                        {
	                            $dye_company=$company_library[$row[csf('knitting_company')]]; 
	                        }
	                        else if($row['knitting_source']==3) 
	                        {
	                            $dye_company=$supplier_details[$row[csf('knitting_company')]];
	                        }
	                        else
	                            $dye_company="&nbsp;";
	                    
	                        $total_fabric_recv_qnty+=$row[csf('production_qty')];
	                    ?>
	                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
	                            <td width="30"><? echo $i; ?></td>
	                            <td width="120"><p><? echo $row[csf('recv_number')]; ?></p></td>
	                            <td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
	                            <td width="80"><? echo $receive_basis_arr[$row[csf('receive_basis')]]; ?></td>
	                            <td width="90"><p><? echo $row[csf('batch_no')]; ?></p></td>
	                            <td width="90"><? echo $knitting_source[$row[csf('knitting_source')]]; ?></td>
	                            <td width="100"><p><? echo $dye_company; ?></p></td>
	                            <td width="90" align="right"><? echo number_format($row[csf('production_qty')],2); ?></td>
	                            <td><p><? echo $row[csf('product_name_details')]; ?></p></td>
	                        </tr>
	                    <?
	                    $i++;
	                    }
	                    ?>
	                    <tfoot>
	                        <th colspan="7" align="right">Total</th>
	                        <th align="right"><? echo number_format($total_fabric_recv_qnty,2); ?></th>
	                        <th>&nbsp;</th>
	                    </tfoot>
	                </table>
	            </div>	
	        </div>
		</fieldset>   
	<?
	exit();
}

if($action=="issue_to_cut")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
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
		<fieldset style="width:770px; margin-left:7px">
			<div id="report_container">
				<table border="1" class="rpt_table" rules="all" width="750" cellpadding="0" cellspacing="0">
					<thead>
						<th colspan="6"><b>Issue To Cutting Info</b></th>
					</thead>
					<thead>
	                	<th width="50">SL</th>
	                    <th width="120">System Id</th>
	                    <th width="80">Issue Date</th>
	                    <th width="120">Batch No</th>
	                    <th width="110">Issue Qnty</th>
	                    <th>Fabric Description</th>
					</thead>
	             </table>
	             <div style="width:767px; max-height:320px; overflow-y:scroll" id="scroll_body">
	                 <table border="1" class="rpt_table" rules="all" width="750" cellpadding="0" cellspacing="0">
	                    <?
	                    $i=1; $total_issue_to_cut_qnty=0;

	                    $sql="SELECT a.issue_number, a.issue_date, b.batch_id, c.batch_no, b.prod_id, e.product_name_details, sum(b.issue_qnty) as quantity from inv_issue_master a, inv_finish_fabric_issue_dtls b, pro_batch_create_mst c, wo_non_ord_samp_booking_mst d, product_details_master e where a.id=b.mst_id and b.batch_id=c.id and a.entry_form=18 and c.booking_no = d.booking_no and c.booking_no_id>0 and b.prod_id=e.id and c.booking_without_order=1 and c.booking_no_id=$boking_id and c.color_id='$color' and e.detarmination_id=$deter_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.issue_number, a.issue_date, b.batch_id, c.batch_no, b.prod_id, e.product_name_details ";
						//echo $sql;
	                    $result=sql_select($sql);
	        			foreach($result as $row)
	                    {
	                        if ($i%2==0)  
	                            $bgcolor="#E9F3FF";
	                        else
	                            $bgcolor="#FFFFFF";	
	                    
	                        $total_issue_to_cut_qnty+=$row[csf('quantity')];
	                    ?>
	                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
	                            <td width="50"><? echo $i; ?></td>
	                            <td width="120"><p><? echo $row[csf('issue_number')]; ?></p></td>
	                            <td width="80" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
	                            <td width="120"><p><? echo $row[csf('batch_no')]; ?></p></td>
	                            <td width="110" align="right"><? echo number_format($row[csf('quantity')],2); ?></td>
	                            <td><p><? echo $row[csf('product_name_details')]; ?></p></td>
	                        </tr>
	                    <?
	                    $i++;
	                    }
	                    ?>
	                    <tfoot>
	                        <th colspan="4" align="right">Total</th>
	                        <th align="right"><? echo number_format($total_issue_to_cut_qnty,2); ?></th>
	                        <th>&nbsp;</th>
	                    </tfoot>
	                </table>
	            </div>	
	        </div>
		</fieldset>   
	<?
	exit();
}
?>