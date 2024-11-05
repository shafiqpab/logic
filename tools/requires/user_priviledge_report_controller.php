<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

if ($action=="load_page_dropdown")
{
	$sql= "select m_menu_id, menu_name from  main_menu WHERE m_module_id = $data and status=1 ORDER BY menu_name ASC";
	echo create_drop_down( "cbo_page_name", 155, $sql,"m_menu_id,menu_name", 1, "-- Select Page --", $selected );
	exit();
}

//Company Details
$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
$main_module=return_library_array( "select m_mod_id, main_module from  main_module",'m_mod_id','main_module');
$main_menu=return_library_array( "select m_menu_id, menu_name from  main_menu",'m_menu_id','menu_name');

if($action=="user_name_search")
{
	echo load_html_head_contents("Popup Info","../../", 1, '', $unicode);
	?>
	<script>

		var tableFilters ={col_2: "select",display_all_text: " --All--"}

		var selected_id = new Array;
		var selected_name = new Array;

		function check_all_data() {
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			
			//This part is for range check -------start;
			var check_from=$('#txt_check_from').val();
			var check_to=$('#txt_check_to').val();
			if($('#check_all'). prop("checked") == true){
				$('#txt_check_from').attr("disabled", true);
				$('#txt_check_to').attr("disabled", true);
			}
			else
			{
				$('#txt_check_from').attr("disabled",false);
				$('#txt_check_to').attr("disabled", false);
			}
			check_from=(check_from)?check_from:1;
			check_to=(check_to)?check_to:tbl_row_count;
			//-------end;
			
			
			
			for( var i = check_from; i <= check_to; i++ ) {
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				
				
				if($('#tr_' + i).is(':visible'))
				{
					js_set_value( functionParam );
				}
				

			}
		}

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value( strCon )
		{
			var splitSTR = strCon.split("_");
			var str = splitSTR[0];
			var selectID = splitSTR[1];
			var selectDESC = splitSTR[2];
			toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );

			if( jQuery.inArray( selectID, selected_id ) == -1 ) {
				selected_id.push( selectID );
				selected_name.push( selectDESC );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == selectID ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id = ''; var name = ''; var job = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			id 		= id.substr( 0, id.length - 1 );
			name 	= name.substr( 0, name.length - 1 );

			$('#txt_selected_id').val( id );
			$('#txt_selected').val( name );
		}
	</script>
	<?
	$arr=array (1=>$row_status);
	$sql = "select id,user_name,valid from user_passwd where valid=1 order by user_name asc";
	echo create_list_view("list_view", "User ID,Status","200,100","340","350",0, $sql , "js_set_value", "id,user_name", "", 1, "0,valid", $arr, "user_name,valid", "","setFilterGrid('list_view',-1,tableFilters)","0","",1) ;
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	
	
	echo "Check From: <input type='text' id='txt_check_from' class='text_boxes' style='width:30px;' />";
	echo " To <input type='text' id='txt_check_to' class='text_boxes' style='width:30px;' />";
	
	
	exit();
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$txt_user_id=str_replace("'","",$txt_user_id);
	$cbo_page_name=str_replace("'","",$cbo_page_name);
	$cbo_main_module=str_replace("'","",$cbo_main_module);
	ob_start();
	?>

	<div style="width:1180px; margin:0 auto;">
		<fieldset style="width:100%;">
			<table width="1160" cellpadding="0" cellspacing="0" id="caption">
				<tr>
					<td align="center" width="100%" colspan="11" class="form_caption" >
						<strong style="font-size:18px">User Privilege Report</strong>
					</td>
				</tr>
			</table>
			<br />
			<table width="1160" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header">
				<thead>
					<th width="35">SL</th>
					<th width="100">User ID</th>
					<th width="125">Company Name</th>
					<th width="125">Buyer Name</th>
					<th width="150">Module Name</th>
					<th width="180">Page Name</th>
					<th width="70">Visibility</th>
					<th width="70">Insert Privilege</th>
					<th width="70">Edit Privilege</th>
					<th width="70">Delete Privilege</th>
					<th width="70">Approve Privilege</th>
					<th>Expire Date</th>
				</thead>
			</table>
			<div style="max-height:300px; width:1180px; overflow-y:scroll;" id="scroll_body">
				<table width="1160" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
					<?
					$sql_user=sql_select("SELECT id, user_name, unit_id, buyer_id, expire_on FROM user_passwd where id in($txt_user_id)");
					foreach($sql_user as $row)
					{
						$user_result[$row[csf("id")]]["id"]=$row[csf("id")];
						$user_result[$row[csf("id")]]["user_name"]=$row[csf("user_name")];
						$user_result[$row[csf("id")]]["unit_id"]=$row[csf("unit_id")];
						$user_result[$row[csf("id")]]["buyer_id"]=$row[csf("buyer_id")];
						$user_result[$row[csf("id")]]["expire_on"]=change_date_format($row[csf("expire_on")]);
					}

					if($txt_user_id!="")$user_con="and b.user_id in($txt_user_id)"; else $user_con="";
					if($cbo_page_name!=0)$page_con="and a.m_menu_id =$cbo_page_name"; else $page_con="";
					if($cbo_main_module!=0)$module_con="and a.m_module_id = $cbo_main_module"; else $module_con="";


					$sql="SELECT
					b.module_id,b.user_id, c.main_menu_id,a.m_menu_id, c.show_priv, c.delete_priv, c.save_priv, c.edit_priv, c.approve_priv
					FROM main_menu a, user_priv_module b, user_priv_mst c
					WHERE a.m_menu_id=c.main_menu_id and a.m_module_id=b.module_id and
					b.user_id=c.user_id $user_con $page_con $module_con group by b.module_id,b.user_id, c.main_menu_id,a.m_menu_id, c.show_priv, c.delete_priv, c.save_priv, c.edit_priv, c.approve_priv order by b.user_id, a.m_menu_id";

					$sql_result=sql_select($sql);
					foreach($sql_result as $row)
					{
						$data_arr[$row[csf("user_id")]][$row[csf("module_id")]][]=$row[csf("main_menu_id")];
						$show_priv_arr[$row[csf("user_id")]][$row[csf("main_menu_id")]]=$yes_no[$row[csf("show_priv")]];
						$insert_priv_arr[$row[csf("user_id")]][$row[csf("main_menu_id")]]=$yes_no[$row[csf("save_priv")]];
						$edit_priv_arr[$row[csf("user_id")]][$row[csf("main_menu_id")]]=$yes_no[$row[csf("edit_priv")]];
						$del_priv_arr[$row[csf("user_id")]][$row[csf("main_menu_id")]]=$yes_no[$row[csf("delete_priv")]];
						$appro_priv_arr[$row[csf("user_id")]][$row[csf("main_menu_id")]]=$yes_no[$row[csf("approve_priv")]];
						$user_page[$row[csf("user_id")]]++;
						$user_module[$row[csf("user_id")]][$row[csf("module_id")]]++;
					}

					$i=1;
					foreach($data_arr as $user_id_key=>$module_data_arr)
					{
						$flag=1;
						$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>"  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="35" align="center" rowspan="<? echo $user_page[$user_id_key]; ?>"><? echo $i;?></td>
							<td width="100" rowspan="<? echo $user_page[$user_id_key]; ?>">
							<div style="width:98px; word-wrap:break-word; word-break:break-all;">
							<? echo $user_result[$user_id_key]["user_name"];?>
                            </div>
                            </td>
							<td width="125" rowspan="<? echo $user_page[$user_id_key]; ?>">
								<div style="width:123px; word-wrap:break-word; word-break:break-all;">
									<?
									$com_arr=explode(",",$user_result[$user_id_key]["unit_id"]);
									$company_name_arr=array();
									foreach($com_arr as $val)
									{
										$company_name_arr[]=$company_arr[$val];
									}
									echo implode(", ",$company_name_arr);
									?>
								</div>
							</td>
							<td width="125" rowspan="<? echo $user_page[$user_id_key]; ?>">
								<p>
									<?
									$buy_arr=explode(",",$user_result[$user_id_key]["buyer_id"]);
									$buyer_name_arr=array();
									foreach($buy_arr as $val)
									{
										$buyer_name_arr[]=$buyer_arr[$val];
									}
									echo implode(", ",$buyer_name_arr);
									?>
								</p>
							</td>
							<?
							foreach($module_data_arr as $module_id_key=>$page_data_arr){
								if($flag==0){?> <tr> <? $flag=1;}
								?>
								<td width="150" rowspan="<? echo $user_module[$user_id_key][$module_id_key]; ?>"><p><? echo $main_module[$module_id_key]; ?></p></td>
								<?
								foreach($page_data_arr as $page)
								{
									if($flag==0){?> <tr> <? }
									?>
									<td width="180"><p><? echo $main_menu[$page]; ?></p></td>
									<td width="70" align="center"><? echo $show_priv_arr[$user_id_key][$page]; ?></td>
									<td width="70" align="center"><? echo $insert_priv_arr[$user_id_key][$page]; ?></td>
									<td width="70" align="center"><? echo $edit_priv_arr[$user_id_key][$page]; ?></td>
									<td width="70" align="center"><? echo $del_priv_arr[$user_id_key][$page]; ?></td>
									<td width="70" align="center"><? echo $appro_priv_arr[$user_id_key][$page]; ?></td>
									<td align="center"><? echo $user_result[$user_id_key]["expire_on"]; ?></td>
								</tr>
								<?
								$flag=0;
							}
						}
					
						$i++;
					}
					?>
				</table>
			</div>
		</fieldset>
	</div>
	<?
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
}//end action;
disconnect($con);
?>


