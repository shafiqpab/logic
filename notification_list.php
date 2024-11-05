<?
    session_start();
    include('includes/common.php');
    extract($_REQUEST);
	$data=$_REQUEST['data'];
    $action=$_REQUEST['action'];

    function timeAgo($time_ago){
        $cur_time 	= time();
        $time_elapsed 	= $cur_time - $time_ago;
        $seconds 	= $time_elapsed ;
        $minutes 	= round($time_elapsed / 60 );
        $hours 		= round($time_elapsed / 3600);
        $days 		= round($time_elapsed / 86400 );
        $weeks 		= round($time_elapsed / 604800);
        $months 	= round($time_elapsed / 2600640 );
        $years 		= round($time_elapsed / 31207680 );
        // Seconds
        if($seconds <= 60){
            echo "$seconds seconds ago";
        }
        //Minutes
        else if($minutes <=60){
            if($minutes==1){
                echo "one minute ago";
            }
            else{
                echo "$minutes minutes ago";
            }
        }
        //Hours
        else if($hours <=24){
            if($hours==1){
                echo "an hour ago";
            }else{
                echo "$hours hours ago";
            }
        }
        //Days
        else if($days <= 7){
            if($days==1){
                echo "yesterday";
            }else{
                echo "$days days ago";
            }
        }
        //Weeks
        else if($weeks <= 4.3){
            if($weeks==1){
                echo "a week ago";
            }else{
                echo "$weeks weeks ago";
            }
        }
        //Months
        else if($months <=12){
            if($months==1){
                echo "a month ago";
            }else{
                echo "$months months ago";
            }
        }
        //Years
        else{
            if($years==1){
                echo "one year ago";
            }else{
                echo "$years years ago";
            }
        }
    }

    if($action == "get_break_down_list_view")
	{
        $data = explode("_",$data);
        $dtls_id = $data[0];

        $dtls_cond = "";
        if(!empty($dtls_id)){
            $dtls_cond = " and A.M_MENU_ID=$dtls_id";
        }
        $user_id = $_SESSION['logic_erp']['user_id'];

        $menu_priviledge_sql = "SELECT a.root_menu,a.m_menu_id,a.menu_name,a.f_location,a.fabric_nature,a.position,b.save_priv,b.edit_priv,b.delete_priv,b.approve_priv 
        FROM main_menu a,user_priv_mst b 
        where a.m_menu_id=$dtls_id
        and a.position=2 and a.status=1 
        and a.m_menu_id=b.main_menu_id and b.valid=1 and b.user_id=$user_id 
        and b.show_priv=1";
        $menu_priviledge_data = sql_select($menu_priviledge_sql);
        $menu_priviledge_arr=array();
        foreach($menu_priviledge_data as $row)
        {
            $menu_priviledge_arr[$row[csf('m_menu_id')]]['f_location'] = $row[csf('f_location')];
            $menu_priviledge_arr[$row[csf('m_menu_id')]]['fnat'] = $row[csf('menu_name')]."__".$row[csf('fabric_nature')];;
            $menu_priviledge_arr[$row[csf('m_menu_id')]]['permission'] = $row[csf('save_priv')]."_".$row[csf('edit_priv')]."_".$row[csf('delete_priv')]."_".$row[csf('approve_priv')];
        }

		$sql_noti_dtls = "SELECT A.ID,A.M_MENU_ID, B.MENU_NAME,A.REF_ID,A.ENTRY_FORM,A.USER_ID,A.IS_APPROVED,A.INSERTED_BY,A.NOTIFI_DESC,A.INSERT_DATE
        FROM APPROVAL_NOTIFICATION_ENGINE A, MAIN_MENU B
        WHERE A.M_MENU_ID = B.M_MENU_ID AND A.IS_SEEN = 0 and A.USER_ID =  $user_id $dtls_cond order by A.IS_APPROVED";
		$res_noti_dtls = sql_select($sql_noti_dtls);
        $user_arr = return_library_array("select id,user_full_name from user_passwd", "id", "user_full_name");
		?>
		<table  width="100%" style="vertical-align:top;" align="top">
            <div  class="head">▣ All Notifications</div>
            <div style="max-height: 600px;overflow-y: scroll">
                <?
                $i = 1;
                if(!empty($res_noti_dtls))
                {
                    foreach($res_noti_dtls as $row)
                    {
                        $permission = $menu_priviledge_arr[$row[('M_MENU_ID')]]['permission'];
                        $f_location = $menu_priviledge_arr[$row[('M_MENU_ID')]]['f_location'];
                        $fnat = $menu_priviledge_arr[$row[('M_MENU_ID')]]['fnat'];
                        $menu_id = $row[('M_MENU_ID')];
                        $menu_name = $row[('MENU_NAME')];

                        $noti_insert_datetime=$row[('INSERT_DATE')];
                        $time_ago=strtotime($row[('INSERT_DATE')]);                    
                        ?>
                        <div class="content">
                            <a class="notification-list" id="lid<?php echo $menu_id; ?>" href="#one<?php echo $menu_id; ?>" onClick="<?php if( trim( $f_location ) == "" ) echo "javascript:return false;"; else { ?>javascript:callurl.load( 'main_body', '<?php echo $f_location . "?permission=" . $permission."&mid=".$menu_id."&fnat=".$fnat; ?>', false, '', '' )<?php } ?>">
                                <div><?$i++;?></div>
                                <div class="margin-bottom-5"><?=$row[('NOTIFI_DESC')];?></div>
                                <div class="margin-bottom-5"><? if($row[csf('IS_APPROVED')] == 1) {echo "Approved By - "; }else {echo "Forwarded by - ";}
                                echo $user_arr[$row[csf('INSERTED_BY')]];?></div> ⌚<span class="time-ago"><? echo timeAgo($time_ago);?></span>
                                <?
                                if($row[csf('IS_APPROVED')]==1)
                                {
                                    echo "<span class='not-badge-success'>✔".$approval_type_arr[$row[csf('IS_APPROVED')]]."</span>";
                                }
                                else
                                {
                                    echo "<span class='not-badge-warning'>⌛".$approval_type_arr[$row[csf('IS_APPROVED')]]."</span>";
                                }
                                ?>
                            </a>
                        </div>
                        <?
                    }
                }
                else
                {
                    echo "<h3 style='text-align:center; margin-top:20px;'>No notification available.</h3>";
                }
                
                ?>
            </div>
		</table>
		<?
        die;
	}

    if($action == "main_list")
    {
        $sql_noti_dtls = "SELECT A.M_MENU_ID, B.MENU_NAME
                          FROM APPROVAL_NOTIFICATION_ENGINE A, MAIN_MENU B
                          WHERE A.M_MENU_ID = B.M_MENU_ID AND A.IS_SEEN = 0
                          GROUP BY A.M_MENU_ID, B.MENU_NAME";
		$res_noti_dtls = sql_select($sql_noti_dtls);
		?>
		<div style="max-height: 470px;overflow-y: scroll">
            <?
            foreach($res_noti_dtls as $row)
            {
                ?>
                <div onclick="load_break_down_manu('<?=$row[('M_MENU_ID')];?>','<?=$row[csf('app_noti_dtls_id')];?>',)" class="content">▣ <?=$row[('MENU_NAME')];?></div>
                <?
            }
            ?>
        </div>
		<?
        die;
    }
?>