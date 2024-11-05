<script src="js/jquery.js" type="text/javascript"></script>



<script type="text/javascript">

	$(function() {

    // hide all the sub-menus
    $("span.toggle").next().hide();
	// add a link nudging animation effect to each link
	$("#jQ-menu a, #jQ-menu span.toggle").hover(function() {
		$(this).stop().animate( {
			fontSize:"12px",
			paddingLeft:"5px"
			//color:"black"
		}, 100);
	}, function() {
		$(this).stop().animate( {
			fontSize:"12px",
			paddingLeft:"0px"
			//color:"black"
		}, 100);
	});

	// set the cursor of the toggling span elements
	$("span.toggle").css("cursor", "pointer");

	// prepend a plus sign to signify that the sub-menus aren't expanded
	$("span.toggle").prepend("+");

	$("#jQ-menu ul > li").css('border-left','2px solid #FFFFFF'); //F33
	$("#jQ-menu ul > li").css('border-top','0.2px solid #FFFFFF'); //FF0033
	$("#jQ-menu ul > li").css('border-right','2px solid #FFFFFF');
	$("span.toggle > ul li").css('border-bottom-left-radius','50px');
	$("span.toggle > ul li").css('border-top-left-radius','50px');

	// add a click function that toggles the sub-menu when the corresponding
	// span element is clicked
	$("span.toggle").click(function() {
		$(this).next().toggle(500);
		// switch the plus to a minus sign or vice-versa
		var v = $(this).html().substring( 0, 1 );
		if ( v == "+" )
			$(this).html( "-" + $(this).html().substring( 1 ) );
		else if ( v == "-" )
			$(this).html( "+" + $(this).html().substring( 1 ) );
	});
});

	$(document).ready(function() {
		$('#jQ-menu ul li a').bind("mouseover", function(){
			/* var color  = $(this).css("background-color");*/
			$(this).css("background", "#C2DCFF");
			$(this).bind("mouseout", function(){
				$(this).css("background", 'none');
			})
		})
	})
</script>

<style>

#jQ-menu{
	width:230px;
	overflow:hidden;
	font-size:12px;
	background-color:#88AAD6;
}
#jQ-menu ul {
	list-style-type: none;
	background-color:#88AAD6
}

#jQ-menu a, #jQ-menu li {
	color:#333;
	text-decoration: none;
	padding-bottom: 5px;
	padding-top: 5px;
	padding-left: 3px;
	border-radius:2px;

	background-image: -webkit-gradient(linear,left bottom,left top,	color-stop(0.07, rgb(100,188,191)),	color-stop(0.5, rgb(226,235,233)),	color-stop(0.96, rgb(89,171,171)));
	background-image: -moz-linear-gradient(bottom, rgb(136,170,214) 7%, rgb(194,220,255) 10%, rgb(136,170,214) 96%);

	/*background-image: -moz-linear-gradient(bottom, rgb(136,170,214) 7%, rgb(194,220,255) 10%, rgb(136,170,214) 96%);*/
	/*background-image:linear-gradient(rgb(194, 220, 255) 10%, rgb(136, 170, 214) 96%)*/

}


</style>
<?
session_start();
include('includes/common.php');
$uid=$_SESSION['logic_erp']["user_id"];

$_SESSION['module_id']="";
$_SESSION['module_id']=$_GET["module_id"];
$m_id = $_GET["module_id"];

$menu_details=array();

?>
</head>
<body>
	<!-- Menu Start -->
	<div id="jQ-menu">
		<ul>
			<?php
			
			$iso_sql=sql_select( "SELECT COMPANY_ID, MENU_ID, ISO_NO FROM LIB_ISO where MODULE_ID='$m_id' and STATUS_ACTIVE=1" );
			$iso_data_set=array();
			if(count($iso_sql)>0)
			{
				foreach($iso_sql as $val)
				{
					$iso_data_set[$val["MENU_ID"]].=$val["COMPANY_ID"]."_".$val["ISO_NO"]."$";
				}
				unset($iso_sql);
			}
			//print_r($iso_data_set);
	
			$level_one=sql_select( "SELECT a.m_menu_id,a.menu_name,a.f_location,a.fabric_nature, b.save_priv,b.edit_priv,b.delete_priv,b.approve_priv FROM main_menu a,user_priv_mst b where a.m_module_id='$m_id' and a.position='1' and a.status='1' and a.m_menu_id=b.main_menu_id and b.valid=1 and b.user_id='".$uid."' and b.show_priv=1 order by a.slno" );
			$i = 0;
			$leve1counter = count( $level_one );

			$module_menu_arr=array();
			if($m_id!="")
			{
				$level_one=sql_select( "SELECT a.m_menu_id,a.menu_name,a.f_location,a.fabric_nature, b.save_priv,b.edit_priv,b.delete_priv,b.approve_priv FROM main_menu a,user_priv_mst b where a.m_module_id='$m_id' and a.position='1' and a.status='1' and a.m_menu_id=b.main_menu_id and b.valid=1 and b.user_id=".$uid." and b.show_priv=1 order by a.slno" );
				$i = 0;
				$leve1counter = count( $level_one );

				
				foreach ($level_one as $r_sql)
				{
					$module_menu_arr[$r_sql[csf('M_MENU_ID')]] = $r_sql[csf('M_MENU_ID')];
				}
			}
			

			if(!empty($module_menu_arr)){
				$child_level2=sql_select("SELECT a.root_menu,a.m_menu_id,a.menu_name,a.f_location,a.fabric_nature,a.position,b.save_priv,b.edit_priv,b.delete_priv,b.approve_priv FROM main_menu a,user_priv_mst b where a.m_module_id=$m_id and a.root_menu in(".implode(",",$module_menu_arr).")  and a.position=2 and a.status=1 and a.m_menu_id=b.main_menu_id and b.valid=1 and b.user_id=$uid and b.show_priv=1 order by a.slno");
				foreach ($child_level2 as $r_sql)
				{
					$child_menu1_arr[$m_id][$uid][$r_sql[csf('ROOT_MENU')]][] = $r_sql[csf('M_MENU_ID')]."**".$r_sql[csf('MENU_NAME')]."**".$r_sql[csf('F_LOCATION')]."**".$r_sql[csf('SAVE_PRIV')]."**".$r_sql[csf('EDIT_PRIV')]."**".$r_sql[csf('DELETE_PRIV')]."**".$r_sql[csf('APPROVE_PRIV')]."**".$r_sql[csf('fabric_nature')];
					$module_sub_menu_arr[$r_sql[csf('M_MENU_ID')]] = $r_sql[csf('M_MENU_ID')];
				}
			}

			if(!empty($module_sub_menu_arr)){
				$child_level3=sql_select("SELECT a.root_menu,a.sub_root_menu,a.m_menu_id,a.menu_name,a.f_location,a.fabric_nature, b.save_priv,b.edit_priv,b.delete_priv,b.approve_priv  FROM main_menu a,user_priv_mst b where a.m_module_id=$m_id and a.sub_root_menu  in(".implode(",",$module_sub_menu_arr).") and a.position=3  and a.status=1 and a.m_menu_id=b.main_menu_id and b.valid=1 and b.user_id=$uid and b.show_priv=1 order by a.slno");
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
					if($iso_data_set[$r_sql[csf('M_MENU_ID')]]) $men.="__".chop($iso_data_set[$r_sql[csf('M_MENU_ID')]],"$");
					
					?>
					<li><a  id="lid<?php echo $r_sql[csf('M_MENU_ID')]; ?>" href="#one<?php echo $menu[$j]['menu_id']; ?>" onClick="<?php if( trim( $r_sql[csf('F_LOCATION')] ) == "" ) echo "javascript:return false;"; else { ?>javascript:callurl.load( 'main_body', '<?php echo $r_sql[csf('F_LOCATION')]. "?permission=" . $r_sql[csf('SAVE_PRIV')] . "_" .  $r_sql[csf('EDIT_PRIV')] . "_" . $r_sql[csf('DELETE_PRIV')] . "_" .  $r_sql[csf('APPROVE_PRIV')]."&mid=".$r_sql[csf('M_MENU_ID')]."&fnat=".$men; ?>', false, '', '' )<?php } ?>"><?php echo $r_sql[csf('MENU_NAME')]; ?></a></li>
					<?
				}
				else
				{
					echo '<li><span class="toggle">'.$r_sql[csf('MENU_NAME')].'</span> <ul>';
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
							if($iso_data_set[$menu_id]) $men.="__".chop($iso_data_set[$menu_id],"$");
							?>
							<li><a  id="lid<?php echo $menu_id; ?>" href="#one<?php echo $menu[$j]['menu_id']; ?>" onClick="<?php if( trim( $f_location ) == "" ) echo "javascript:return false;"; else { ?>javascript:callurl.load( 'main_body', '<?php echo $f_location . "?permission=" . $save_priv . "_" .  $edit_priv . "_" . $delete_priv . "_" .  $approve_priv."&mid=".$menu_id."&fnat=".$men; ?>', false, '', '' )<?php } ?>"><?php echo  $menu_name; ?></a></li>
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
								if($iso_data_set[$menu_id]) $men.="__".chop($iso_data_set[$menu_id],"$");
								?>
								<li><a  id="lid<?php echo $menu_id; ?>" href="#one<?php echo $menu[$j]['menu_id']; ?>" onClick="<?php if( trim( $f_location ) == "" ) echo "javascript:return false;"; else { ?>javascript:callurl.load( 'main_body', '<?php echo $f_location . "?permission=" . $save_priv . "_" .  $edit_priv . "_" . $delete_priv . "_" .  $approve_priv."&mid=".$menu_id."&fnat=".$men; ?>', false, '', '' )<?php } ?>"><?php echo  $menu_name; ?></a></li>
								<?
							}
							echo '</ul></li>';
						}

					}
					echo '</ul></li>';
				}
			}
			?>
		</ul>
	</div>
	<!-- End Menu -->
</body>
</html>