<?php
include('../common.php');
class Notifications
{
    
	
	function __construct()
    {
		
	}

    public function notificationEngine($id,$company,$entry_form,$array_param = array(),$user_id ='')
    {
        $user_ids = "";
        $user_ids = $this->get_electronic_approval_user($company,$entry_form,$array_param,$user_id);
        //return  $user_ids;
        if(!empty($user_ids))
        {
            $users = explode(",",$user_ids);
            $data = array();
            if(!empty($array_param['approval_data']))
            {
                $data = $array_param['approval_data'];//json_encode($array_param['approval_data']);
            }
            $desc = "";
            if(!empty($array_param['approval_desc']))
            {
                $desc = $array_param['approval_desc'];
            }
           
           

            $title = "";
            if(!empty($array_param['title']))
            {
                $title = $array_param['title'];
            }

            $res =  $this->insert_notification_data($id,$entry_form,$user_ids,$desc);
            if($res)
            {
                foreach($users as $user_id)
                {
                    
                    $result = sql_select("select FCM_TOKEN,DEVICE_TYPE from APPROVAL_NOTI_USER_DEVICES where user_id = $user_id order by ID DESC");

                    foreach($result as $row)
                    {
                        $key = $row['FCM_TOKEN'];
                        $device_type = $row['DEVICE_TYPE'];
                        $this->sendMessageToFCM($key,$title,$data,1,$device_type);
                    }
                }
            }
            return $res;
        }
        else return 1;
    } 

    public function get_electronic_approval_user($company,$entry_form,$array_param = array(),$user_id ='') 
    {
        if(empty($company) || empty($entry_form)) return "";

        $extra_cond = "";
        $BRAND_ID       = $array_param['BRAND_ID'];
        $BUYER_ID       = $array_param['BUYER_ID'];
        $DEPARTMENT     = $array_param['DEPARTMENT'];
        $SUPPLIER_ID    = $array_param['SUPPLIER_ID'];
        $PARTY_ID       = $array_param['PARTY_ID'];
        $LOCATION       = $array_param['LOCATION'];
        $ITEM_CATEGORY  = $array_param['ITEM_CATEGORY'];

        if(!empty($BRAND_ID))
        {
            $extra_cond .= " AND  ( BRAND_ID like '%,$BRAND_ID,%' OR BRAND_ID like '%,$BRAND_ID' OR BRAND_ID like '$BRAND_ID,%' OR BRAND_ID = '$BRAND_ID' OR BRAND_ID IS NULL OR BRAND_ID = '0') ";
        }
        if(!empty($BUYER_ID))
        {
            $extra_cond .= " AND  ( BUYER_ID like '%,$BUYER_ID,%' OR BUYER_ID like '%,$BUYER_ID' OR BUYER_ID like '$BUYER_ID,%' OR BUYER_ID = '$BUYER_ID' OR BUYER_ID IS NULL OR BUYER_ID = '0') ";
        }
        if(!empty($DEPARTMENT))
        {
            $extra_cond .= " AND  ( DEPARTMENT like '%,$DEPARTMENT,%' OR DEPARTMENT like '%,$DEPARTMENT' OR DEPARTMENT like '$DEPARTMENT,%' OR DEPARTMENT = '$DEPARTMENT' OR DEPARTMENT IS NULL OR DEPARTMENT ='0') ";
        }
        if(!empty($SUPPLIER_ID))
        {
            $extra_cond .= " AND  ( SUPPLIER_ID like '%,$SUPPLIER_ID,%' OR SUPPLIER_ID like '%,$SUPPLIER_ID' OR SUPPLIER_ID like '$SUPPLIER_ID,%' OR SUPPLIER_ID = '$SUPPLIER_ID' OR SUPPLIER_ID IS NULL OR SUPPLIER_ID ='0') ";
        }
        if(!empty($PARTY_ID))
        {
            $extra_cond .= " AND  ( PARTY_ID like '%,$PARTY_ID,%' OR PARTY_ID like '%,$PARTY_ID' OR PARTY_ID like '$PARTY_ID,%' OR PARTY_ID = '$PARTY_ID' OR PARTY_ID IS NULL OR PARTY_ID='0') ";
        }
        if(!empty($LOCATION))
        {
           $extra_cond .= " AND  ( LOCATION like '%,$LOCATION,%' OR LOCATION like '%,$LOCATION' OR LOCATION like '$LOCATION,%' OR LOCATION = '$LOCATION' OR LOCATION IS NULL OR LOCATION='0') ";
    
        }
        if(!empty($ITEM_CATEGORY))
        {
            $extra_cond .= " AND  (";
            $categories  = explode(",",$ITEM_CATEGORY);
            $sl = 0;
            foreach($categories as $category_id)
            {
                if($sl > 0) $extra_cond .= " OR ";
                $extra_cond .= "  ( ITEM_CATEGORY like '%,$category_id,%' OR ITEM_CATEGORY like '%,$category_id' OR ITEM_CATEGORY like '$category_id,%' OR ITEM_CATEGORY = '$category_id' ) ";
                $sl++;
            }
            $extra_cond .= "  OR ITEM_CATEGORY IS NULL OR ITEM_CATEGORY ='' )";
        }

        if(!empty($user_id))
        {
            $SEQUENCE_NO = return_field_value("SEQUENCE_NO","ELECTRONIC_APPROVAL_SETUP","COMPANY_ID=$company AND ENTRY_FORM=$entry_form $extra_cond AND IS_DELETED = 0 AND USER_ID IN ( $user_id )","SEQUENCE_NO");
            if(!empty($SEQUENCE_NO ))
            {
                $extra_cond .= " AND SEQUENCE_NO > $SEQUENCE_NO ";
            }

            $extra_cond .= " AND USER_ID NOT IN ( $user_id ) ";
        }

        

        $sql_elec = "select SEQUENCE_NO,BYPASS,USER_ID,GROUP_NO FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID=$company AND ENTRY_FORM=$entry_form $extra_cond AND IS_DELETED = 0 ORDER BY SEQUENCE_NO ASC";
        // echo $sql_elec;die;
    
        $data_array=sql_select($sql_elec);
        $userIdInSetup = array();
        foreach($data_array as $row)
        {
            $userIdInSetup[$row['USER_ID']] = $row['USER_ID'];
        }
        $userIds="";
        $user_cred_sql = array();
        if(count($userIdInSetup))
        {
            $sql_cred = "SELECT UNIT_ID AS COMPANY_ID, STORE_LOCATION_ID, ITEM_CATE_ID, SUPPLIER_ID,BUYER_ID,COMPANY_LOCATION_ID,DEPARTMENT_ID,SUPPLIER_ID,BRAND_ID,ID FROM USER_PASSWD WHERE ID in (".implode(",",$userIdInSetup).")";
           // echo $sql_cred;die;
            $user_cred_sql = sql_select($sql_cred);
        }
       
    
        $user_cred_store = array();
        
    
        foreach($user_cred_sql as $row)
        {
            if(!empty($row['STORE_LOCATION_ID']))
            {
                $stores = explode(",",$row['STORE_LOCATION_ID']);
                foreach($stores as $store_id)
                {
                    $user_cred_store[$row['ID']][$store_id] = $store_id;
                }
            }
            else
            {
                $user_cred_store[$row['ID']][$array_param['STORE_ID']] = $array_param['STORE_ID'];
            }
            
        }

        $prev_group = "";
        $count_group_by = 0;
        foreach($data_array as $row)
        {   
            $flag = true; 
            if(!empty($array_param['STORE_ID']))
            {
                if(count($user_cred_store[$row['USER_ID']]) > 0 && empty($user_cred_store[$row['USER_ID']][$array_param['STORE_ID']]))
                {
                    $flag = false;
                }
                else if(count($user_cred_store[$row['USER_ID']]) ==0)
                {
                    $flag = false;
                }
            }

           // echo $flag;die;
           
            if($prev_group == "" || $prev_group == $row['GROUP_NO'] || (!empty($user_id) && $count_group_by<1))
            {
                if($row['BYPASS']==1 && $flag == true)
                {
                    $userIds .=$row['USER_ID'].","; 
                    $prev_group = $row['GROUP_NO'];
                }
                elseif($row['BYPASS']==2 && $flag == true)
                {
                    $userIds .=$row['USER_ID'].",";
                    $prev_group = $row['GROUP_NO'];
                    break;
                }
                if( !empty($prev_group) && ($prev_group != $row['GROUP_NO']) && $flag == true)
                {
                    
                    if($row['BYPASS']==2)
                    {
                        $count_group_by++;
                    }
                }
            }
            /*
            if($prev_group == "" || $prev_group == $row['GROUP_NO'] )
            {
                if($row['BYPASS']==1 && $flag == true)
                {
                    $userIds .=$row['USER_ID'].","; 
                    $prev_group = $row['GROUP_NO'];
                }
                elseif($row['BYPASS']==2 && $flag == true)
                {
                    $userIds .=$row['USER_ID'].",";
                    $prev_group = $row['GROUP_NO'];
                    break;
                }
            }*/
        }
        return  rtrim($userIds,",");  
    }

    public function sendMessageToFCM_backup($key,$title,$data,$type ='')
    {
        
        $path_to_fcm = 'https://fcm.googleapis.com/fcm/send';
        $server_key ='AAAA71zSyIc:APA91bFxBmJaK59r4T-DIMvSbyTWPalRf5Q9BcV2JaTmefpzRH90NIaOudwmFi6HpUbpjvgB9pLyaborMPF9L6iO7r5NOI5CEh9ZxtLOzLKFL0EI53EmG7ynym4H_r9SDwq1Da0wWJmj';
        
        $headers = array(
                        'Authorization:key=' .$server_key,
                        'Content-Type:application/json'
                        );

        $arr = [
            'data'=> [
                'title'=> $title,
                'body'=> $data,
                'type' => $type
                ],
            "to" => $key
        ];
    
        $payload = json_encode($arr);
    
        $curl_session = curl_init();
        curl_setopt($curl_session, CURLOPT_URL, $path_to_fcm);
        curl_setopt($curl_session, CURLOPT_POST, true);
        curl_setopt($curl_session, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl_session, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl_session, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
        curl_setopt($curl_session, CURLOPT_POSTFIELDS, $payload);
    
    
        $result = curl_exec($curl_session);
        curl_close($curl_session);
        return $result;
    }
    public function sendMessageToFCM($key, $title, $data, $type = '', $device_type = '')
    {
        $url = 'https://fcm.googleapis.com/fcm/send';
        $server_key = 'AAAA71zSyIc:APA91bFxBmJaK59r4T-DIMvSbyTWPalRf5Q9BcV2JaTmefpzRH90NIaOudwmFi6HpUbpjvgB9pLyaborMPF9L6iO7r5NOI5CEh9ZxtLOzLKFL0EI53EmG7ynym4H_r9SDwq1Da0wWJmj';

        $headers = array(
            'Authorization:key=' . $server_key,
            'Content-Type:application/json'
        );

        // Use anonymous function to create notification payload
        $create_payload = function ($device_type, $key, $title, $data, $type){
            if ($device_type == "ios")
            {
                // Use notification parameter for iOS devices
                $notification = array(
                    'title' => $title,
                    'body' => $data['DESC'] ?? '',
                    'sound' => 'default',
                    'badge' => '1',
                    'data' => $data,
                    'type' => $type
    
                );
    
                $arrayToSend = array(
                    'to' => $key,
                    'notification' => $notification, // For iOS devices
                    'priority' => 'high'
                );
            }
            else if ($device_type == "android")
            {
                // Use data parameter for Android devices
                $data = array(
                    'title' => $title,
                    'body' => $data,
                    'type' => $type
                );

                $arrayToSend = array(
                    'to' => $key,
                    'data' => $data, // For Android devices
                    'priority' => 'high'
                );
            }
            return json_encode($arrayToSend);
        };

        // Call anonymous function to get notification payload
        $payload = $create_payload($device_type, $key, $title, $data, $type);

        $curl_session = curl_init();
        curl_setopt($curl_session, CURLOPT_URL, $url);
        curl_setopt($curl_session, CURLOPT_POST, true);
        curl_setopt($curl_session, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl_session, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl_session, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($curl_session, CURLOPT_POSTFIELDS, $payload);

        $result = curl_exec($curl_session);
        curl_close($curl_session);
        return $result;
    }

    public function insert_notification_data($ref_id,$entry_form,$user_ids,$desc)
    {

        $menu_id=return_field_value("page_id as menu_id","ELECTRONIC_APPROVAL_SETUP","entry_form=$entry_form","menu_id");

        $id=return_next_id( "ID", "APPROVAL_NOTIFICATION_ENGINE",1);
 
        $con = connect();

        $column = "ID,REF_ID,ENTRY_FORM,M_MENU_ID,NOTIFI_DESC,USER_ID,IS_APPROVED,IS_SEEN,STATUS_ACTIVE,INSERTED_BY,INSERT_DATE";

       

        $pc_date_time = date("d-M-Y h:i:s A",time());

        $user_arr = explode(",",$user_ids);
        $i = 1;
        $data = "";
        foreach($user_arr as $user_id)
        {
            if ($i!=1) $data .=",";

            $inserted_by = $_SESSION['logic_erp']['user_id'];

            if($_SESSION['logic_erp']['user_id'] == "")
            {
                $inserted_by = 0;
            }
				
			$data .="(".$id.",".$ref_id.",".$entry_form.",".$menu_id.",'".$desc."',".$user_id.",0,0,1,".$inserted_by.",'".$pc_date_time."')";
			$id=$id+1;
        }
        $rDelete=execute_query("DELETE FROM APPROVAL_NOTIFICATION_ENGINE WHERE USER_ID in (".$user_ids.") and REF_ID = $ref_id AND ENTRY_FORM = $entry_form ");
        $rID=sql_insert("APPROVAL_NOTIFICATION_ENGINE",$column,$data,0);
       
        if($rID && $rDelete)
        {

            oci_commit($con);
            disconnect($con);
            return 1;
        }
        else
        {
            oci_rollback($con);
            disconnect($con);
            return 0;
        }
    }

    public function pushAll($ref_id,$entry_form,$array_param = array())
    {
        $cond = "";
        $user_id = $array_param['USER_ID'];
        $notification_type = $array_param['NOTIFICATION_TYPE'];
        $user_arr = array();
        if(!empty($user_id) )
        {
            $user_arr[$user_id] = $user_id;
            $SEQUENCE_NO = $array_param['SEQUENCE_NO'];
            $COMPANY_ID = $array_param['COMPANY_ID'];
            $cond = " AND USER_ID IN ( SELECT USER_ID FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID=$COMPANY_ID AND ENTRY_FORM=$entry_form  AND IS_DELETED = 0 AND SEQUENCE_NO <$SEQUENCE_NO)";
        }
        $users=sql_select("SELECT USER_ID FROM APPROVAL_NOTIFICATION_ENGINE  WHERE ENTRY_FORM=$entry_form AND REF_ID IN ($ref_id) $cond");

        
        foreach($users as $row)
        {
            $user_arr[$row['USER_ID']] = $row['USER_ID'];
        }

        if(count($user_arr) > 0)
        {
            
            $data = $array_param['approval_data'];//$this->getData($ref_id,$entry_form);
            $json = $data;// json_encode($data);
            
            $result  = sql_select("select FCM_TOKEN,DEVICE_TYPE from APPROVAL_NOTI_USER_DEVICES where user_id IN (".implode(",",$user_arr).") order by ID DESC");
            $flag = 1;

            foreach($result as $row)
            {
                $key = $row['FCM_TOKEN'];
                $device_type = $row['DEVICE_TYPE'];
                $ret =  $this->sendMessageToFCM($key,'',$json,0,$device_type);
                if($flag == 0 || !isset($ret))
                {
                    $flag = 0;
                }
            }

            if(!empty($cond))
            {
                $con = connect();
                try
                {
                    $res = execute_query("DELETE FROM APPROVAL_NOTIFICATION_ENGINE WHERE ENTRY_FORM=$entry_form AND REF_ID IN ($ref_id) $cond");
                    if($res !=1)
                    {
                        throw new Exception("Something error in Push to All");
                    }
                    
                    oci_commit($con);
                    disconnect($con);
                    return 1;
                }
                catch(Exception $e)
                {
                    oci_rollback($con);
                    disconnect($con);
                    return 0;
                }
                
            }
            return $flag ;
        }
    }

}
?>