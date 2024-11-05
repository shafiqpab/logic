<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

//Company Details
$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
$main_module=return_library_array( "select m_mod_id, main_module from  main_module",'m_mod_id','main_module');
$main_menu=return_library_array( "select m_menu_id, menu_name from  main_menu",'m_menu_id','menu_name');


if($action=="user_name_search")
{
	//echo "Yes";die;
			  
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	?>
    <script>
		
		var selected_id = new Array;
		var selected_name = new Array;
		
    	function check_all_data() {
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value( functionParam );
				
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
			//alert(strCon);
				var splitSTR = strCon.split("_");
				var str = splitSTR[0];
				var selectID = splitSTR[1];
				var selectDESC = splitSTR[2];
				//$('#txt_individual_id' + str).val(splitSTR[1]);
				//$('#txt_individual' + str).val('"'+splitSTR[2]+'"');
				
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
	//extract($_REQUEST);
	$sql = "select id,user_name from user_passwd order by id "; 
	//echo $sql;
	echo create_list_view("list_view", "User Name","250","300","400",0, $sql , "js_set_value", "id,user_name", "", 1, "0", $arr, "user_name", "","setFilterGrid('list_view',-1)","0","",1) ;	
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	exit();

}


if($action=="report_generate")
{
	//echo "yes";die;
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$txt_user_id=str_replace("'","",$txt_user_id);
	//echo $txt_user_id;die;
	?>
<div style="width:830px">
    <fieldset style="width:100%;">	
        <table width="1300" cellpadding="0" cellspacing="0" id="caption">
            <tr>  
                <td align="center" width="100%" colspan="11" class="form_caption" >
                <strong style="font-size:18px">User Priviledge Report</strong>
                </td>
            </tr>
        </table>
    	<br />
        <table width="1300" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="">
            <thead>
                <tr>
                    <th width="40">Sl</th>
                    <th width="150">User Name</th>
                    <th width="225">Company Name</th>
                    <th width="225">Buyer Name</th>
                    <th width="150">Module Name</th>
                    <th width="180">Page Name</th>
                    <th width="50">Save Privilede</th>
                    <th width="50">Edit Privilede</th>
                    <th width="50">Delete Privilede</th>
                    <th width="50">Aprove Privilede</th>
                    <th >Expire Date</th>
                </tr>
            </thead>
            <tbody>
            <?
			if($txt_user_id!="")$sql_cond="and b.user_id in($txt_user_id)"; else $sql_cond="";
			$sql="SELECT 
			 b.module_id, c.main_menu_id, c.show_priv, c.delete_priv, c.save_priv, c.edit_priv, c.approve_priv 
			FROM 
					main_menu a, user_priv_module b, user_priv_mst c 
			WHERE
					a.m_menu_id=c.main_menu_id and a.m_module_id=b.module_id and 
b.user_id=c.user_id   $sql_cond  group by a.m_menu_id order by a.m_menu_id";
			 
			 
			$sql_result=sql_select($sql);
            $i=1;$j=1;$k=1;$user_name=array();$module_name=array();$manu_id_name=array();$com_arr=array(); $buy_arr=array();
			foreach($sql_result as $row)
			{
				$user_name_arr[$row[csf("id")]][$row[csf("module_id")]][$row[csf("main_menu_id")]]=$row[csf("main_menu_id")];
				$com_arr[$row[csf("id")]][$row[csf("unit_id")]]=$row[csf("unit_id")];
				//$module_name_arr[$row[csf("module_id")]]=$row[csf("module_id")];
			}
			//var_dump($user_name_arr);
			
			foreach($sql_result as $row)
			{            
            	?>
                <tr>
                    <td><? echo $i;?>&nbsp;</td>
                    <?
					if(!in_array($row[csf("id")],$user_name))
					{
						?>
						<td><? echo $row[csf("user_name")];?>&nbsp;</td>
						 <td >
						<?
						$company_id=explode(",",$row[csf("unit_id")]);
						//var_dump($company_id);
						foreach($company_id as $id)
						{
							if($j!=1) $company_name.=", ";
							$company_name.=$company_arr[$id];
							$j++;
						}
						echo $company_name;
						 $company_name="";
						 ?>&nbsp;
						</td>
						<td >
						<?
						$buyer_id=explode(",",$row[csf("buyer_id")]);
						foreach($buyer_id as $id)
						{
							if($k!=1) $buyer_name.=", ";
							$buyer_name.=$buyer_arr[$id];
							$k++;
						}
						 echo $buyer_name;
						 $buyer_name="";
						 
						 ?>&nbsp;
						</td>
						<?
                        $user_name[]=$row[csf("id")];
						$module_name="";
					}
					else
					{
						?>
						<td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <?
					}
					$j=1;$k=1;
					if(!in_array($row[csf("module_id")],$module_name))
					{
						?>
						<td><? echo $main_module[$row[csf("module_id")]];?>&nbsp;</td>
						<?
						$module_name[]=$row[csf("module_id")];
					}
					else
					{
						?>
						<td>&nbsp;</td>
						<?
					}
					?>
                    <td><? echo $main_menu[$row[csf("main_menu_id")]];?>&nbsp;</td>
                    <?
					if($row[csf("save_priv")]==1)
					{
						?>
						<td align="center"><? echo "Yes";?>&nbsp;</td>
						<?
					}
					else
					{
						?>
						<td align="center">&nbsp;</td>
						<?
					}
					if($row[csf("edit_priv")]==1)
					{
						?>
						<td align="center"><? echo "Yes";?>&nbsp;</td>
						<?
					}
					else
					{
						?>
						<td align="center">&nbsp;</td>
						<?
					}
					if($row[csf("delete_priv")]==1)
					{
						?>
						<td align="center"><? echo "Yes";?>&nbsp;</td>
						<?
					}
					else
					{
						?>
						<td align="center">&nbsp;</td>
						<?
					}
					if($row[csf("approve_priv")]==1)
					{
						?>
						<td align="center"><? echo "Yes";?>&nbsp;</td>
						<?
					}
					else
					{
						?>
						<td align="center">&nbsp;</td>
						<?
					}
					?>
                    <td><?  if($row[csf('expire_on')]!="0000-00-00") echo change_date_format($row[csf("expire_on")]); else echo "00-00-0000"?>&nbsp;</td>
                </tr>
                <?
				$i++;
			}
			?>
            </tbody>
        </table>
    </fieldset>          
</div>
<?
	exit();
	
}
disconnect($con);
?>


