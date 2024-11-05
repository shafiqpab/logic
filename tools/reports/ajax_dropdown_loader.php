 
<?php 
date_default_timezone_set( 'UTC' );
$distId = intval( $_GET['distId'] );
$type = intval( $_GET['type'] );

include('../../includes/common.php');

if( $type == 1 ) {
	//echo "select m_menu_id,menu_name from main_menu where status=1 and m_module_id='$distId'  order by menu_name"; die;
?>
 
	<select name="cbo_menu_name" id="cbo_menu_name" style="width:150px"  class="combo_boxes">
                                	 
        <option value="0">--- All menu ---</option>
        <?
             
        $mod_sql= mysql_db_query($DB, "select m_menu_id,menu_name from main_menu where status=1 and m_module_id='$distId'  order by menu_name"); //where is_deleted=0 and status=0
        
        while ($r_mod=mysql_fetch_array($mod_sql))
        {
           
        ?>
        <option value=<? echo $r_mod["m_menu_id"];
        if ($company_combo==$r_mod["m_menu_id"]){?> selected <?php }?>><? echo "$r_mod[menu_name]" ?> </option>
        <?
        }
        ?>
    </select>
 
<?php
}
 ?>