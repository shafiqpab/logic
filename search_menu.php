<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('includes/common.php');

$user_name=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


if($action=="search")
{
	echo load_html_head_contents("Popup Info","", 1, 1, $unicode,'','');
	?>
	<script>
		function get_menu_list()
		{
			if(document.getElementById('txt_search').value==''){alert("Please Write Menu Name");return;}
			var data="action=menu_list_view&txt_search="+document.getElementById('txt_search').value;
			http.open("POST","search_menu.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = get_menu_list_reponse;
			
		}
		
	function get_menu_list_reponse()
	{
		if(http.readyState == 4) 
		{
			$('#report_container').html(http.responseText);
		}
		
	}
		
	function js_set_value(str)
	{
		$('#txt_menu_info').val(str);
		parent.emailwindow.hide();
	}
	</script>
    </head>
	<body>
        <div align="center" style="width:100%;" >
            <form name="keydate_1"  id="keydate_1" autocomplete="off">
                <input type="hidden" id="txt_menu_info" value="" />
                <table cellspacing="0" cellpadding="6" border="1" rules="all" class="rpt_table" align="center">
                    <thead>
                         <tr>
                            <th>Type Menu Name : 
                             <input name="txt_search" id="txt_search" class="text_boxes" style="width:200px" placeholder="Type here........" autocomplete="on"> 
                             <input type="button" class="formbutton" value="Search" onClick="get_menu_list( )" style="width:100px;" /></th>
                         </tr>
                  	</thead>
               
                 </table>
             </form>
             </div>
             <div id="report_container"></div>
	</body>           
	</html>
    <?
	exit();
}

if($action=="menu_list_view")
{
	extract($_REQUEST); 

	$uid=$user_name;$m_id=1;
	if($txt_search){$whereCon=" and a.menu_name like('%".$txt_search."%')";}

 	
		$module_arr=return_library_array("select M_MOD_ID,MAIN_MODULE from MAIN_MODULE","M_MOD_ID","MAIN_MODULE");
		
			
			
			$level_one=sql_select( "SELECT a.m_module_id,a.m_menu_id,a.menu_name,a.f_location,a.fabric_nature, b.save_priv,b.edit_priv,b.delete_priv,b.approve_priv FROM main_menu a,user_priv_mst b where a.position='1' $whereCon and a.status='1' and a.m_menu_id=b.main_menu_id and b.valid=1 and a.is_mobile_menu not in (1)   and b.user_id=".$uid." and b.show_priv=1 order by a.slno" );
			$i = 0;
			$leve1counter = count( $level_one );
			
			if($leve1counter==0){echo "<h1>Not Match</h1>";exit();}

			$module_menu_multy_arr=array();
			foreach ($level_one as $r_sql)
			{
				$module_menu_multy_arr[$r_sql[csf('m_module_id')]][$r_sql[csf('M_MENU_ID')]] = $r_sql[csf('M_MENU_ID')];
			}

			$module_menu_arr=array();
			foreach($module_menu_multy_arr as $module_id=>$module_menu_arr){
				echo '<div id="jQ-menu">
					<h2>'.$module_arr[$module_id].'</h2>
				<ul>';
				if(!empty($module_menu_arr)){
				$child_level2=sql_select("SELECT a.root_menu,a.m_menu_id,a.menu_name,a.f_location,a.fabric_nature,a.position,b.save_priv,b.edit_priv,b.delete_priv,b.approve_priv FROM main_menu a,user_priv_mst b where a.m_module_id=$m_id and a.root_menu in(".implode(",",$module_menu_arr).")  and a.position=2 and a.status=1 and a.is_mobile_menu not in (1)   and a.m_menu_id=b.main_menu_id and b.valid=1 and b.user_id=$uid and b.show_priv=1 order by a.slno");
				$module_sub_menu_arr=array();$child_menu1_arr=array();
				foreach ($child_level2 as $r_sql)
				{
					$child_menu1_arr[$m_id][$uid][$r_sql[csf('ROOT_MENU')]][] = $r_sql[csf('M_MENU_ID')]."**".$r_sql[csf('MENU_NAME')]."**".$r_sql[csf('F_LOCATION')]."**".$r_sql[csf('SAVE_PRIV')]."**".$r_sql[csf('EDIT_PRIV')]."**".$r_sql[csf('DELETE_PRIV')]."**".$r_sql[csf('APPROVE_PRIV')]."**".$r_sql[csf('fabric_nature')];
					$module_sub_menu_arr[$r_sql[csf('M_MENU_ID')]] = $r_sql[csf('M_MENU_ID')];
				}
			}

				if(!empty($module_sub_menu_arr)){
				$child_level3=sql_select("SELECT a.root_menu,a.sub_root_menu,a.m_menu_id,a.menu_name,a.f_location,a.fabric_nature, b.save_priv,b.edit_priv,b.delete_priv,b.approve_priv  FROM main_menu a,user_priv_mst b where a.m_module_id=$m_id and a.sub_root_menu  in(".implode(",",$module_sub_menu_arr).") and a.position=3 and a.is_mobile_menu not in (1)   and a.status=1 and a.m_menu_id=b.main_menu_id and b.valid=1 and b.user_id=$uid and b.show_priv=1 order by a.slno");
				$child_menu2_arr=array();
				foreach ($child_level3 as $r_sql)
				{
					$child_menu2_arr[$m_id][$uid][$r_sql[csf('ROOT_MENU')]][$r_sql[csf('SUB_ROOT_MENU')]][] = $r_sql[csf('M_MENU_ID')]."**".$r_sql[csf('MENU_NAME')]."**".$r_sql[csf('F_LOCATION')]."**".$r_sql[csf('SAVE_PRIV')]."**".$r_sql[csf('EDIT_PRIV')]."**".$r_sql[csf('DELETE_PRIV')]."**".$r_sql[csf('APPROVE_PRIV')]."**".$r_sql[csf('fabric_nature')];
				}
			}

				foreach ($level_one as $r_sql)
				{
				$i++;
				$level2 = $child_menu1_arr[$m_id][$uid][$r_sql[csf('M_MENU_ID')]];
				if( count( $level2 ) < 1)
				{
					$men=$r_sql[csf('MENU_NAME')]."__".$r_sql[csf('fabric_nature')];
					
					$actionVal= 'main_body**'.$r_sql[csf('F_LOCATION')]. "?permission=" . $r_sql[csf('SAVE_PRIV')] . "_" .  $r_sql[csf('EDIT_PRIV')] . "_" . $r_sql[csf('DELETE_PRIV')] . "_" .  $r_sql[csf('APPROVE_PRIV')]."&mid=".$r_sql[csf('M_MENU_ID')]."&fnat=".$men."**false******".$module_id;
					
					if( trim( $r_sql[csf('F_LOCATION')] ) != "" ){
					?>
					<li>&raquo;<a  id="lid<?php echo $r_sql[csf('M_MENU_ID')]; ?>" href="#one<?php echo $menu[$j]['menu_id']; ?>" onClick="<?php if( trim( $r_sql[csf('F_LOCATION')] ) == "" ) echo "javascript:return false;"; else { ?>javascript:js_set_value('<?= $actionVal;?>')<?php } ?>"><?php echo $r_sql[csf('MENU_NAME')]; ?></a></li>
					<?
					}
				}
				else
				{
					echo '<li><h3 class="toggle">'.$r_sql[csf('MENU_NAME')].'</h3> <ul>';
					foreach ($level2 as $level2_menu)
					{
						$i++;
						$r_sql2 		= explode("**",$level2_menu);
						$menu_id 		= $r_sql2[0];
						$menu_name 		= $r_sql2[1];
						$f_location 	= $r_sql2[2];
						$save_priv 		= $r_sql2[3];
						$edit_priv 		= $r_sql2[4];
						$delete_priv 	= $r_sql2[5];
						$approve_priv 	= $r_sql2[6];
						$fabric_nature 	= $r_sql2[7];

						$level3 = $child_menu2_arr[$m_id][$uid][$r_sql[csf('M_MENU_ID')]][$menu_id];
						if( count( $level3 ) < 1)
						{
							$men=$menu_name."__".$fabric_nature;
							 
							 $actionVal='main_body**'.$f_location . "?permission=" . $save_priv . "_" .  $edit_priv . "_" . $delete_priv . "_" .  $approve_priv."&mid=".$menu_id."&fnat=".$men."**false******".$module_id;
							
							
							
							?>
							<li>&raquo; <a  id="lid<?php echo $menu_id; ?>" href="#one<?php echo $menu[$j]['menu_id']; ?>" onClick="<?php if( trim( $f_location ) == "" ) echo "javascript:return false;"; else { ?>javascript:js_set_value('<?= $actionVal;?>' )<?php } ?>"><?php echo  $menu_name; ?></a></li>
							<?
						}
						else
						{
							echo '<li><span class="toggle">'.$menu_name.'</span> <ul>';
							foreach ($level3 as $level3_menu)
							{
								$r_sql3 		= explode("**",$level3_menu);
								$menu_id 		= $r_sql3[0];
								$menu_name 		= $r_sql3[1];
								$f_location 	= $r_sql3[2];
								$save_priv 		= $r_sql3[3];
								$edit_priv 		= $r_sql3[4];
								$delete_priv 	= $r_sql3[5];
								$approve_priv 	= $r_sql3[6];
								$fabric_nature 	= $r_sql3[7];
								$men=$menu_name."__".$fabric_nature;
								
								
								
								$actionVal="main_body**".$f_location . "?permission=" . $save_priv . "_" .  $edit_priv . "_" . $delete_priv . "_" .  $approve_priv."&mid=".$menu_id."&fnat=".$men."**false******".$module_id;
								
								?>
							
                                <li>&raquo; <a  id="lid<?php echo $menu_id; ?>" href="#one<?php echo $menu[$j]['menu_id']; ?>" onClick="<?php if( trim( $f_location ) == "" ) echo "javascript:return false;"; else { ?>javascript:js_set_value( '<?= $actionVal;?>' )<?php } ?>"><?php echo  $menu_name; ?></a></li>
								<?
							}
							echo '</ul></li>';
						}

					}
					echo '</ul></li>';
				}
			}
			echo '</ul></div>';
			}
			
			
			?>
		
	<style>
	#jQ-menu{border:1px solid #666; width:28%; float:left; padding:5px; margin:5px;}
	#jQ-menu ul li{list-style:none;}
	
	</style>

<?
exit();
}





?>