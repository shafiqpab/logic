<?php
  
    // approval user define function start
    function get_next_id($column,$table)
    {
        $column = strtoupper($column);
        $sql = "SELECT max(".$column.") as ".$column." from ".$table; 
        $result = sql_select_arr($sql);
        if(!empty($result))
        {
            $nextId = $result[0][$column] + 1;
        }
        else {
            $nextId = 1;
        }
        return $nextId;
    }
  
	function get_electronic_approval_user($company,$entry_form,$array_param = array(),$user_id ='') 
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
            $extra_cond .= " AND  ( BUYER_ID like '%,$BUYER_ID,%' OR BUYER_ID like '%,$BUYER_ID' OR BUYER_ID like '$BUYER_ID,%' OR BUYER_ID = '$BUYER_ID' OR BUYER_ID IS NULL OR BUYER_ID ='0') ";
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
            $extra_cond .= " AND  ( PARTY_ID like '%,$PARTY_ID,%' OR PARTY_ID like '%,$PARTY_ID' OR PARTY_ID like '$PARTY_ID,%' OR PARTY_ID = '$PARTY_ID' OR PARTY_ID IS NULL OR PARTY_ID ='0') ";
        }
        if(!empty($LOCATION))
        {
           $extra_cond .= " AND  ( LOCATION like '%,$LOCATION,%' OR LOCATION like '%,$LOCATION' OR LOCATION like '$LOCATION,%' OR LOCATION = '$LOCATION' OR LOCATION IS NULL OR LOCATION ='0') ";
    
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
            $extra_cond .= "  OR ITEM_CATEGORY IS NULL OR ITEM_CATEGORY ='0')";
        }

        if(!empty($user_id))
        {
            $SEQUENCE_NO = return_field_value("SEQUENCE_NO","ELECTRONIC_APPROVAL_SETUP","COMPANY_ID=$company AND ENTRY_FORM=$entry_form $extra_cond AND IS_DELETED = 0 and  USER_ID in ($user_id)","SEQUENCE_NO");
       

            if(!empty($SEQUENCE_NO ))
            {
                $extra_cond .= " AND SEQUENCE_NO > $SEQUENCE_NO ";
            }
            $extra_cond .= " AND USER_ID NOT IN ( $user_id ) ";
        }

        $sql_elec = "SELECT SEQUENCE_NO,BYPASS,USER_ID,GROUP_NO FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID=$company AND ENTRY_FORM=$entry_form $extra_cond AND IS_DELETED = 0 ORDER BY SEQUENCE_NO ASC";
        //return $sql_elec; 
        $data_array  = sql_select_arr($sql_elec);
    
        //$data_array=$this->sqlSelect($sql_elec);
        $userIdInSetup = array();
        foreach($data_array as $row)
        {
            $userIdInSetup[$row['USER_ID']] = $row['USER_ID'];
        }
        $userIds="";
        $user_cred_sql = array();
        if(count($userIdInSetup) > 0)
        {
            $sql_cred = "SELECT UNIT_ID AS COMPANY_ID, STORE_LOCATION_ID, ITEM_CATE_ID, SUPPLIER_ID,BUYER_ID,COMPANY_LOCATION_ID,DEPARTMENT_ID,SUPPLIER_ID,BRAND_ID,ID FROM USER_PASSWD WHERE ID in (".implode(",",$userIdInSetup).")";
            //return $sql_cred;
            $user_cred_sql  = sql_select_arr($sql_cred);
            // $user_cred_sql = $this->sqlSelect($sql_cred);
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
			else{
				$user_cred_store[$row['ID']][$array_param['STORE_ID']] = $array_param['STORE_ID'];
			}
        }

        $prev_group = "";
		$count_group_by = 0;
		$text = "";
        foreach($data_array as $row)
        {
            $flag = true; 
            if(!empty($array_param['STORE_ID']))
            {
                if(count($user_cred_store[$row['USER_ID']]) > 0 && empty($user_cred_store[$row['USER_ID']][$array_param['STORE_ID']]) )
                {
                    $flag = false;
                }
				else if(count($user_cred_store[$row['USER_ID']]) == 0)
				{
					$flag = false;
				}
            }
            if($prev_group == "" || $prev_group == $row['GROUP_NO']  || (!empty($user_id) && $count_group_by<1))
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
					if($row['BYPASS']==2 && $flag == true)
                	{
                   	 	$count_group_by++;
					}
                }
				
            }
			
        }
        return  rtrim($userIds,",");  
    }
 
	function return_field_value( $field_name, $table_name, $query_cond, $return_fld_name )
	{
		$ci=& get_instance();
        $ci->load->database();
		if ($return_fld_name=="") $return_fld_name=$field_name;
	
		$queryText="select ".$field_name." from ".$table_name." where ".$query_cond." "  ;
			
        $query = $ci->db->query($queryText);
        $rows = $query->result_array();
		foreach($rows as $row)
			if($ci->db->dbdriver!='mysqli')
			{
				if($row[strtoupper($return_fld_name)]!="") return $row[strtoupper($return_fld_name)]; else return false;
			}
			else
				if($row[strtolower($return_fld_name)]!="") return $row[strtolower($return_fld_name)]; else return false;
			
		//die;
	}
  
	function  get_final_user($parameterArr=array())
    {
		$ci=& get_instance();
        $ci->load->database();

        $lib_buyer_arr=implode(',',(array_keys($parameterArr['lib_buyer_arr'])));
        $lib_brand_arr=implode(',',(array_keys($parameterArr['lib_brand_arr'])));
        $lib_store_arr=implode(',',(array_keys($parameterArr['lib_store_arr'])));
        $lib_department_id_string=implode(',',(array_keys($parameterArr['lib_department_id_arr']))); 
        $lib_location_id_string=implode(',',(array_keys($parameterArr['lib_location_id_arr'])));
        $lib_item_cat_id_string=implode(',',(array_keys($parameterArr['lib_item_cat_arr']))); 
     
    
        //User data.....................
        
        $sql_user="SELECT ID,STORE_LOCATION_ID as STORE_ID FROM USER_PASSWD WHERE VALID=1";
        //$sql_user_result=sql_select($sql_user);
        $sql_user_result = $ci->db->query($sql_user)->result();
        $userDataArr=array();
        foreach($sql_user_result as $rows){
            $userDataArr[$rows->ID]['STORE_ID']=$rows->STORE_ID;
        }
        
        
        //Electronic app setup data.....................
        $sql="SELECT COMPANY_ID,PAGE_ID,USER_ID,BYPASS,SEQUENCE_NO,BUYER_ID,BRAND_ID,DEPARTMENT as DEPARTMENT_ID,LOCATION as LOCATION_ID ,ITEM_CATEGORY as ITEM_ID FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr['company_id']} AND PAGE_ID = {$parameterArr['page_id']} AND IS_DELETED = 0  order by SEQUENCE_NO";

        $sql_result = $ci->db->query($sql)->result();
           //echo $sql;die;
        //$sql_result=sql_select($sql);
        foreach($sql_result as $rows)
        {
            if(empty($userDataArr[$rows->USER_ID]['STORE_ID'])){
                $userDataArr[$rows->USER_ID]['STORE_ID']=$lib_store_arr;
            }
            if(empty($rows->DEPARTMENT_ID)){
                $rows->DEPARTMENT_ID=$lib_department_id_string;
            }
            if(empty($rows->LOCATION_ID)){
                $rows->LOCATION_ID=$lib_location_id_string;
            }
            if(empty($rows->ITEM_ID)){$rows->ITEM_ID=$lib_item_cat_id_string;}
    
            
            $usersDataArr[$rows->USER_ID]['STORE_ID'] = explode(',', $userDataArr[$rows->USER_ID]['STORE_ID']);
            $usersDataArr[$rows->USER_ID]['DEPARTMENT_ID'] = explode(',', $rows->DEPARTMENT_ID);
            $usersDataArr[$rows->USER_ID]['ITEM_ID'] = explode(',', $rows->ITEM_ID);
            $usersDataArr[$rows->USER_ID]['LOCATION_ID'] = explode(',', $rows->LOCATION_ID);
    
            $userSeqDataArr[$rows->USER_ID] = $rows->SEQUENCE_NO;
        
        }
        //echo '<pre>';print_r($usersDataArr);
        //echo '<pre>';print_r($parameterArr[match_data]);
        $finalSeq=array();
        foreach($parameterArr['match_data'] as $sys_id=>$bbtsRows){
            
            foreach($userSeqDataArr as $user_id=>$seq){
    
                $validation_check = true;
                if($parameterArr['category_mixing'] == 2){
                    if(in_array($bbtsRows['item'], $usersDataArr[$user_id]['ITEM_ID']) || $bbtsRows['item']==0){$validation_check = true;}
                    else{$validation_check = false;}
                }
                if(
                    in_array($bbtsRows['store'],$usersDataArr[$user_id]['STORE_ID'])
                    && (in_array($bbtsRows['department'],$usersDataArr[$user_id]['DEPARTMENT_ID']) || $bbtsRows['department']==0)
                    && (in_array($bbtsRows['location_id'],$usersDataArr[$user_id]['LOCATION_ID']) || $bbtsRows['location_id']==0)
                    && ($validation_check == true)
                    &&  $bbtsRows['store']>0
                ){
                    $finalSeq[$sys_id][$user_id]=$seq;
                }
            }
        }
    
        //var_dump($finalSeq);
        //die;
        return array('final_seq'=>$finalSeq,'user_seq'=>$userSeqDataArr);
    }

	function sql_multi_row_update($strTable,$arrUpdateFields,$arrUpdateValues,$arrRefFields,$arrRefValues)
    {
		$ci=& get_instance();
        $ci->load->database();

        $strQuery = "UPDATE ".$strTable." SET ";
        $arrUpdateFields=explode("*",$arrUpdateFields);
        $arrUpdateValues=explode("*",$arrUpdateValues);
    
    
        if(is_array($arrUpdateFields))
        {
            $arrayUpdate = array_combine($arrUpdateFields,$arrUpdateValues);
            $Arraysize = count($arrayUpdate);
            $i = 1;
            foreach($arrayUpdate as $key=>$value):
                $strQuery .= ($i != $Arraysize)? $key."=".$value.", ":$key."=".$value." WHERE ";
                $i++;
            endforeach;
        }
        else
        {
            $strQuery .= $arrUpdateFields."=".$arrUpdateValues." WHERE ";
        }
        $strQuery .= $arrRefFields." in (".$arrRefValues.")";
        $result = $ci->db->query($strQuery);

       if ($result)
       {
         return 1;
       }
       else
       {
         return 0;
       }
    }

	function sql_update($strTable,$arrUpdateFields,$arrUpdateValues,$arrRefFields,$arrRefValues,$return_query=0)
    {

		$ci=& get_instance();
        $ci->load->database();

        $strQuery = "UPDATE ".$strTable." SET ";
        $arrUpdateFields=explode("*",$arrUpdateFields);
        $arrUpdateValues=explode("*",$arrUpdateValues);

        if(count($arrUpdateFields)!=count($arrUpdateValues)){
            return "0";
        }

        if(is_array($arrUpdateFields))
        {
            $arrayUpdate = array_combine($arrUpdateFields,$arrUpdateValues);
            $Arraysize = count($arrayUpdate);
            $i = 1;
            foreach($arrayUpdate as $key=>$value):
                $strQuery .= ($i != $Arraysize)? $key."=".$value.", ":$key."=".$value;
                $i++;
            endforeach;
        }
        else
        {
            $strQuery .= $arrUpdateFields."=".$arrUpdateValues;
        }
        $strQuery .=" WHERE ";

        $arrRefFields=explode("*",$arrRefFields);
        $arrRefValues=explode("*",$arrRefValues);
        if(is_array($arrRefFields))
        {
            $arrayRef = array_combine($arrRefFields,$arrRefValues);
            $Arraysize = count($arrayRef);
            $i = 1;
            foreach($arrayRef as $key=>$value):
                $strQuery .= ($i != $Arraysize)? $key."=".$value." AND ":$key."=".$value."";
                $i++;
            endforeach;
        }
        else
        {
            $strQuery .= $arrRefFields."=".$arrRefValues."";
        }
        if($return_query==1){return $strQuery ;}

            //return $strQuery;die;
      
        $result = $ci->db->query($strQuery);

       
        if ($result)
        {
            return 1;
        }
        else
        {
            $error = $ci->db->error();
            return "Error Code: " . $error['code'] . "\n"."Error Message: " . $error['message'];
        }
    }

	function execute_query($sql)
    {
		$ci=& get_instance();
        $ci->load->database();

        $result = $ci->db->query($sql);

        if ($result)
        {
            return 1;
        }
        else
        {
            $error = $ci->db->error();
            return "Error Code: " . $error['code'] . "\n"."Error Message: " . $error['message'];
        }
    }

	function return_library_arr($sql,$key,$value)
    {
        $result = sql_select_arr($sql);
        $data = array();
		foreach($result as $row)
        {
            if(isset($row[$key]))
            {
                $data[$row[$key]]=$row[$value];
            }
		}
		return $data;
    }

	function  bulk_update_sql_statement($table, $id_column, $update_column, $data_values, $id_count)
    {
        $field_array = explode("*", $update_column);
        //$id_count=explode("*",$id_count);
        //$data_values=explode("*",$data_values);
        //print_r($data_values);die;
        $sql_up = "";
        $sql_up .= "UPDATE $table SET ";
    
        for ($len = 0; $len < count($field_array); $len++) {
            $sql_up .= " " . $field_array[$len] . " = CASE $id_column ";
            for ($id = 0; $id < count($id_count); $id++) {
                if (trim($data_values[$id_count[$id]][$len]) == "") {
                    $sql_up .= " when " . $id_count[$id] . " then  '" . $data_values[$id_count[$id]][$len] . "'";
                } else {
                    $sql_up .= " when " . $id_count[$id] . " then  " . $data_values[$id_count[$id]][$len] . "";
                }
    
            }
            if ($len != (count($field_array) - 1)) {
                $sql_up .= " END, ";
            } else {
                $sql_up .= " END ";
            }
    
        }
        $sql_up .= " where $id_column in (" . implode(",", $id_count) . ")";
        return $sql_up;
    }

	function push_to_fcm_backup($key,$title,$data,$type = '')
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

	function push_to_fcm($key, $title, $data, $type = '', $device_type = '')
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

	function push_all($ref_id,$entry_form,$array_param = array())
    {
		$ci=& get_instance();
        $ci->load->database();

        $cond = "";
        $user_id = $array_param['USER_ID'];
        $notification_type = $array_param['NOTIFICATION_TYPE'];
        $user_arr = array();
        if(!empty($user_id) )
        {
            $user_arr[ $user_id] =  $user_id;
            $SEQUENCE_NO = $array_param['SEQUENCE_NO'];
            $COMPANY_ID = $array_param['COMPANY_ID'];
            $cond = " AND USER_ID IN ( SELECT USER_ID FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID=$COMPANY_ID AND ENTRY_FORM=$entry_form  AND IS_DELETED = 0 AND SEQUENCE_NO <$SEQUENCE_NO)";
        }

        $sql ="SELECT USER_ID FROM APPROVAL_NOTIFICATION_ENGINE  WHERE ENTRY_FORM=$entry_form AND REF_ID IN ($ref_id) $cond";
        $users  = sql_select_arr($sql);
        // $users=$this->sql_select_arr("SELECT USER_ID FROM APPROVAL_NOTIFICATION_ENGINE  WHERE ENTRY_FORM=$entry_form AND REF_ID IN ($ref_id) $cond");

       

        foreach($users as $row)
        {
            $user_arr[$row['USER_ID']] = $row['USER_ID'];
        }
       

        if(count($user_arr) > 0)
        {
            
            $json = "";
            
			if(!empty($array_param['approval_data']))
			{
				$json = $array_param['approval_data'];//json_encode($array_param['approval_data']);
			}
            
            $title = "";
            
            $sql ="select FCM_TOKEN,DEVICE_TYPE from APPROVAL_NOTI_USER_DEVICES where user_id IN (".implode(",",$user_arr).") order by ID DESC";
            $result  = sql_select_arr($sql);

           // $result  = $this->sql_select_arr("select FCM_TOKEN from APPROVAL_NOTI_USER_DEVICES where user_id IN (".implode(",",$user_arr).") order by ID DESC");
            $flag = 1;
            foreach($result as $row)
            {
                $key = $row['FCM_TOKEN'];
                $device_type = $row['DEVICE_TYPE'];
                $ret =  push_to_fcm($key, $title, $json,0,$device_type);
                if($flag == 0 || !isset($ret))
                {
                    $flag = 0;
                }
            }

            if(!empty($cond))
            {
                $ci->db->trans_begin();
                $res = execute_query("DELETE APPROVAL_NOTIFICATION_ENGINE  WHERE ENTRY_FORM=$entry_form AND REF_ID IN ($ref_id) $cond");
                if($res !=1)
                {
                    $ci->db->trans_rollback();
                    return 0;
                }
                else
                {
                    $ci->db->trans_commit();
                }
                return 1;
            }
            return $flag;
        }
    }

	function sql_select_arr($sql)
	{
		$ci=& get_instance();
        $ci->load->database();
		$result = $ci->db->query($sql)->result();
		
		$res = array();
        foreach($result as $row)
        {
            $res[] = (array) $row;
        }
        return $res;
	}
    // approval user define function end
	
	function sql_select($sql)
	{
		$ci=& get_instance();
        $ci->load->database();
		return $ci->db->query($sql)->result();
	}

    function return_library_array($sql,$key,$value){
        $ci=& get_instance();
        $ci->load->database(); 
  
        //$sql = "select * from table"; 
		$data=array();
        $query = $ci->db->query($sql);
        $rows = $query->result_array();
		foreach($rows as $row){
			if($ci->db->dbdriver!='mysqli')
			$data[$row[strtoupper($key)]]=$row[strtoupper($value)];
			else
				$data[$row[strtolower($key)]]=$row[strtolower($value)];
		}
		return $data;
    }
   
    function csf($data,$db_types=1)							 
    {
   		 
   		if ($db_types==0 || $db_types==1 )  return strtolower($data); else return strtoupper($data);
    }

	function return_next_id($field_name, $table_name, $max_row = 1, $new_conn='',$db_type='') // Checked   3
	{
	   	$ci=& get_instance();
	   	$ci->load->database(); 

		$increment = 1;
		$queryText = "select max(" . $field_name . ") as " . $field_name . "  from " . $table_name . " ";
		$nameArray = sql_select($queryText, '', $new_conn);
		if($ci->db->dbdriver=='mysqli'){$field_name=strtolower($field_name); }
		else {$field_name=strtoupper($field_name);}
		foreach ($nameArray as $result)
		{
			//return ($result[csf($field_name,$db_type)] + $increment);
			return ($result->$field_name + $increment);
		}

		
	}

   function return_db_type()
   {
	   	$ci=& get_instance();
	   	$ci->load->database(); 
	   	if($ci->db->dbdriver=='mysqli') 
	   	{
	   		return  0;
	   	}
	   	else
	   	{
	   		return 2;

	   	}

   }

	function return_next_id_by_sequence( $seq_name,$table_name,$new_conn="",$is_mrr="",$company_id=0,$mrr_prefix="",$entry_form=0,$year_id=0,$item_category_id=0,$booking_type=0,$production_type=0,$embelishment_type=0,$transfer_criteria=0 )
	{
		$ci=& get_instance();
        $ci->load->database(); 
		//global $db_type;
		$item_category_id=0; // see function defination
		if($ci->db->dbdriver!='mysqli')
		{
			if($is_mrr == 1)
			{
				$mrr_cond="";
				$mrr_cond = ($mrr_cond != "") ? " and $mrr_cond" : "";
				//echo "10**";
				$seq_sql="select f_NextSeq('".strtoupper($table_name)."',$company_id,$entry_form,$year_id,$item_category_id,$booking_type,$production_type,$embelishment_type,$transfer_criteria) next_id from dual";
				$seqArray=sql_select( $seq_sql);
				//print_r($seqArray); die;
				// Prepare System ID
				$comp_prefix = return_field_value("company_short_name","lib_company", "id=$company_id","company_short_name");
				$recv_number_prefix = $comp_prefix . "-" . $mrr_prefix . "-" . substr(date("Y", time()),2,2) . "-";

				$recv_number = $recv_number_prefix . "" . str_pad($seqArray[0]->NEXT_ID, 5, '0', STR_PAD_LEFT) . "*" . $recv_number_prefix . "*" . str_pad($seqArray[0]->NEXT_ID, 5, '0', STR_PAD_LEFT);
				// die;

				return $recv_number;
			}
			else
			{
				$seq_sql="select ".$seq_name.".nextval as ID from dual";
				$seqArray=sql_select( $seq_sql);
				return $seqArray[0]->ID;
			}
		}
		else
		{
			if($is_mrr == 1)
			{
				$seq_sql = "select NextVal($is_mrr,'$table_name',$company_id,$entry_form,$year_id,$item_category_id,$booking_type,$production_type,$embelishment_type,$transfer_criteria) as next_val";
				$seqArray = sql_select( $seq_sql,'');
				// Prepare System ID
				$comp_prefix = return_field_value("company_short_name","lib_company", "id=$company_id","company_short_name");
				$recv_number_prefix = $comp_prefix . "-" . $mrr_prefix . "-" . substr(date("Y", time()),2,2) . "-";

				$recv_number = $recv_number_prefix . "" . str_pad($seqArray[0]->next_val, 5, '0', STR_PAD_LEFT) . "*" . $recv_number_prefix . "*" . str_pad($seqArray[0]->next_val, 5, '0', STR_PAD_LEFT);

				return $recv_number;
			}
			else
			{
				$seq_sql = "select NextVal(0,'$table_name',$company_id,$entry_form,$year_id,$item_category_id,$booking_type,$production_type,$embelishment_type,$transfer_criteria) as next_val";
				$seqArray = sql_select( $seq_sql,'' );
				foreach ($seqArray as $result)
					return $result->next_val;
			}
		}
	}


   
   
   
	
	
	 

	function date_diff_days($date1,$date2)
	{  
		$ci=& get_instance();
 		$str = strtotime($date1) - (strtotime($date2));
		return abs(floor($str/3600/24));
	}
	
	

	function datediff( $interval, $datefrom, $dateto, $using_timestamps = false ) 
	{
				if( $datefrom != "" and $dateto != "" ) {
					if( !$using_timestamps ) {
						$datefrom = strtotime( $datefrom, 0 );
						$dateto = strtotime( $dateto, 0 );
					}
				$difference = $dateto - $datefrom; // Difference in seconds
				switch( $interval ) {
					case 'yyyy': // Number of full years
					$years_difference = floor( $difference / 31536000 ); 
					if( mktime( date( "H", $datefrom ), date( "i", $datefrom ), date( "s", $datefrom ), date( "n", $datefrom ), date( "j", $datefrom ), date( "Y", $datefrom ) + $years_difference ) > $dateto ) {
						$years_difference--;
					}
					if( mktime( date( "H", $dateto ), date( "i", $dateto ), date( "s", $dateto ), date( "n", $dateto ), date( "j", $dateto ), date( "Y", $dateto ) - ( $years_difference + 1 ) ) > $datefrom ) {
						$years_difference++;
					}
					$datediff = $years_difference;
					break;
					case "q": // Number of full quarters
					$quarters_difference = floor( $difference / 8035200 );
					while( mktime( date( "H", $datefrom ), date( "i", $datefrom ), date( "s", $datefrom ), date( "n", $datefrom ) + ( $quarters_difference * 3 ), date( "j", $dateto ), date( "Y", $datefrom ) ) < $dateto ) {
						$months_difference++;
					}
					$quarters_difference--;
					$datediff = $quarters_difference;
					break;
					case "m": // Number of full months
					$months_difference = floor( $difference / 2678400 );
					while( mktime( date( "H", $datefrom ), date( "i", $datefrom ), date( "s", $datefrom ), date( "n", $datefrom ) + ( $months_difference ), date( "j", $dateto ), date( "Y", $datefrom ) ) < $dateto ) {
						$months_difference++;
					}
						//$months_difference--;
					$datediff = $months_difference;
					break;
					case 'y': // Difference between day numbers
					$datediff = date( "z", $dateto ) - date( "z", $datefrom );
					break;
					case "d": // Number of full days
					$datediff = ( floor( $difference / 86400 ) + 1 );
					break;
					case "w": // Number of full weekdays
					$days_difference = floor( $difference / 86400 );
						$weeks_difference = floor( $days_difference / 7 ); // Complete weeks
						$first_day = date( "w", $datefrom );
						$days_remainder = floor( $days_difference % 7 );
						$odd_days = $first_day + $days_remainder; // Do we have a Saturday or Sunday in the remainder?
						if( $odd_days > 7 ) $days_remainder--;	// Sunday
						if( $odd_days > 6 ) $days_remainder--;	// Saturday
						$datediff = ( $weeks_difference * 5 ) + $days_remainder;
						break;
					case "ww": // Number of full weeks
					$datediff = floor( $difference / 604800 );
					break;
					case "h": // Number of full hours
					$datediff = floor( $difference / 3600 );
					break;
					case "n": // Number of full minutes
					$datediff = floor( $difference / 60 );
					break;
					default: // Number of full seconds (default)
					$datediff = $difference;
					break;
				}
				return $datediff;
			}
	}


	function get_resource_allocation_variable($company_id)
    {
    	$ci=& get_instance();
        $ci->load->database();
        $prod_reso_allo_sql="select AUTO_UPDATE from variable_settings_production where company_name ='$company_id' and variable_list=23 and is_deleted=0 and status_active=1";
        $prod_reso_allo= $ci->db->query($prod_reso_allo_sql)->row();    
        $value=0;
        if(!empty($prod_reso_allo))
        {       
            $value=$prod_reso_allo->AUTO_UPDATE;         
        }  
        return $value;

    }


	function get_max_value($table_name, $field_name) {
		$ci=& get_instance();
		$ci->load->database();
        //return $ci->db->select_max($fieldName)->get($tableName)->row()->{$fieldName};

		$increment = 1;
		echo $queryText = "select max(" . $field_name . ") as " . $field_name . "  from " . $table_name . " ";
		$nameArray = sql_select($queryText, '', $new_conn);
		if($ci->db->dbdriver=='mysqli'){$field_name=strtolower($field_name); }
		else {$field_name=strtoupper($field_name);}

		//print_r($nameArray);
		foreach ($nameArray as $result)
		{
			return ($result->$field_name + 1);
		}

    }

	function return_id( $field_text, $library_array, $table_name, $table_field, $entry_form )
	{
		$ci=& get_instance();
		$ci->load->database();
		if($ci->db->dbdriver!='mysqli')
		{
			$field_text=str_replace("'","",trim(strtoupper($field_text)));
			$field_text=str_replace('"','',trim(strtoupper($field_text)));
			if($field_text=="") { return 0; die; }

			$field_text=str_replace("(","[",trim(strtoupper($field_text)));
			$field_text=str_replace(")","]",trim(strtoupper($field_text)));
		}
		else
		{
			$field_text=str_replace("'","",trim(strtolower($field_text)));
			$field_text=str_replace('"','',trim(strtolower($field_text)));
			if($field_text=="") { return 0; die; }

			$field_text=str_replace("(","[",trim(strtolower($field_text)));
			$field_text=str_replace(")","]",trim(strtolower($field_text)));
		}
		$library_array_new=array_combine($library_array,$library_array);

		if(in_array($field_text, $library_array_new))
		{
			 $data_id =  array_search($field_text, $library_array, true);
		}
		else
		{
	
			$data_id = get_max_value("lib_color", "ID")+1;
			if($entry_form!='') 
			{
				$data_fld["ID"]=$data_id;
				if($ci->db->dbdriver!='mysqli')
				$data_fld[$table_field]=trim(strtoupper($field_text));
				else
					$data_fld[$table_field]=trim(strtolower($field_text));
				$data_fld["ENTRY_FORM"]=$entry_form;
			}
			else
			{

				$data_fld["ID"]=$data_id;
				if($ci->db->dbdriver!='mysqli')
				$data_fld[$table_field]=trim(strtoupper($field_text));
				else
					$data_fld[$table_field]=trim(strtolower($field_text));

			}
			
			$ci->db->trans_start();
			$ci->db->insert($table_name, $data_fld);
			$ci->db->trans_complete();
			
			
			//$rID=sql_insert( $table_name, $table_field, $data_fld,1);
		}
			return $data_id;
			die;
	}


	function add_date($orgDate,$days,$type)
	{
		$ci=& get_instance();
		$cd = strtotime($orgDate);
		if($type == 1){
			$retDAY = date('Y-m-d', mktime(0,0,0,date('m',$cd),date('d',$cd)+$days,date('Y',$cd)));
		}else{
			$retDAY = date('Y-m-d', mktime(0,0,0,date('m',$cd),date('d',$cd)-$days,date('Y',$cd)));
		}
		return $retDAY;
	}
	 
	function month_add($orgDate, $mon) {
		$cd = strtotime($orgDate);
		//$retDAY = date('Y-m-d', mktime(0,0,0,date('m',$cd)+$mon,date('d',$cd),date('Y',$cd)));
		$retDAY = date('Y-m-d', mktime(0, 0, 0, date('m', $cd) + $mon, 1, date('Y', $cd)));
		return $retDAY;
	}
	
	
     function writeFiles($fileName,$txt){
		$file="objectData/".$fileName.".md";
		$current = file_get_contents($file);
		$current .= $txt."\n\n..........".date('d-m-Y h:i:s a',time()).".........\n\n";
		file_put_contents($file, $current);
	 }
	
	function where_con_using_array($arrayData,$dataType=0,$table_coloum){
		$chunk_list_arr=array_chunk($arrayData,999);
		if(count($chunk_list_arr)<1){return " and ".$table_coloum." in(0)";}
		$p=1;
		foreach($chunk_list_arr as $process_arr)
		{
			if($dataType==0){
				if($p==1){$sql .=" and (".$table_coloum." in(".implode(',',$process_arr).")"; }
				else {$sql .=" or ".$table_coloum." in(".implode(',',$process_arr).")";}
			}
			else{
				if($p==1){$sql .=" and (".$table_coloum." in('".implode("','",$process_arr)."')"; }
				else {$sql .=" or ".$table_coloum." in('".implode("','",$process_arr)."')";}
			}
			$p++;
		}
		
		$sql.=") ";
		return $sql;
	}

	function change_date_format($date, $new_format, $new_sep, $on_save,$db_type) 
	{
		$ci=& get_instance();	

		if ($new_sep == "") 
		{
			$new_sep = "-";
		}

		if ($new_format == "") 
		{
			$new_format = "dd-mm-yyyy";
		}

	 
		if ($date == "0000-00-00" || $date == "" || $date == 0) 
		{
			return "";
		}

		if ($db_type == 2) 
		{
			if ($date == "0000-00-00" || $date == "" || $date == 0) 
			{
				return "";
			}

			if ($on_save == 0) 
			{
				return date("d-m-Y", strtotime($date));
			} 
			else
			{
				return date("d-M-Y", strtotime($date));
			}

		}
		$year = date("Y", strtotime($date));
		$mon = date("m", strtotime($date));
		$day = date("d", strtotime($date));

		if ($new_format == "yyyy-mm-dd") // yyyy-mm-dd
		{
			$dd = $year . $new_sep . $mon . $new_sep . $day;
		} else if ($new_format == "dd-mm-yyyy") // dd-mm-yyyy
		{
			$dd = $day . $new_sep . $mon . $new_sep . $year;
		}

		if ($db_type == 0 || $db_type == 1) {
			if ($dd == "1970-01-01" || $dd == "01-01-1970" || $dd == "30-11--0001") {
				return "";
			} else {
				return $dd;
			}
		} else
		if ($dd == "1970-01-01" || $dd == "01-01-1970" || $dd == "30-11--0001") {
			return "";
		} else {
			return date("Y-M-d", strtotime($dd));
		}

 
	}

function check_operation_status($col_id, $order_id, $job_no, $cutting_no, $hidden_barcode, $sequence_array) {
		$precost_job = return_field_value("a.job_no", "wo_pre_cost_mst a", "a.job_no='" . $job_no . "' and a.status_active=1 and a.is_deleted=0 ", "job_no");
		//echo $precost_job;die;
		if (!empty($precost_job)) {
			$sql_extrawork = sql_select("select b.id,b.emb_name,a.item_number_id from wo_pre_cos_emb_co_avg_con_dtls a,wo_pre_cost_embe_cost_dtls b  where a.job_no=b.job_no and b.emb_name in (1,2,4) and b.job_no='" . $job_no . "' and a.pre_cost_emb_cost_dtls_id=b.id   and color_size_table_id in (" . $col_id . ") and a.requirment>0  and b.status_active=1 and b.is_deleted=0 group by b.emb_name,b.id,a.item_number_id order by b.id ASC");
			
			$first_op_arr = array();
			if (count($sql_extrawork) > 0) {
				$i = 1;

				foreach ($sql_extrawork as $row) {
					$current_op = $row->EMB_NAME;
					if ($row->EMB_NAME == 1) //print
					{
						$preceding = "3_1";
						$succeding = "2_1";
					}
					if ($row->EMB_NAME == 2) {
						$preceding = "3_2";
					$succeding = "2_2"; // $first_op_arr[$row[csf("item_number_id")]]=$row[csf("emb_name")];
				}
				if ($row->EMB_NAME == 4) {
					$preceding = "3_4";
					$succeding = "2_4"; // $first_op_arr[$row[csf("item_number_id")]]=$row[csf("emb_name")];
				}

				if ($i == 1) {
					$sequence_array[$col_id]["9" . $current_op]['preceding'] = 1;
				} else {
					$sequence_array[$col_id]["9" . $current_op]['preceding'] = $last_preceding;
				}

				if ($last_op != '') {
					$sequence_array[$col_id]["9" . $last_op]['succeding'] = $succeding;
				}

				if ($i == count($sql_extrawork)) {
					$sequence_array[$col_id]["9" . $current_op]['succeding'] = 4;
				} else {
					$sequence_array[$col_id]["9" . $current_op]['succeding'] = $succeding;
				}

				$last_succeding = $succeding;
				$last_preceding = $preceding;
				$last_op = $current_op;
				$i++;

				$sequence_array[$col_id]["9" . $current_op]['job_no'] = $job_no;
				$sequence_array[$col_id]["9" . $current_op]['po_no'] = $order_id;
				$sequence_array[$col_id]["9" . $current_op]['cut_no'] = $cutting_no;
			}
			$sequence_array[$col_id][4]['succeding'] = 5;
			$sequence_array[$col_id][4]['preceding'] = $preceding;
			$sequence_array[$col_id][4]['job_no'] = $job_no;
			$sequence_array[$col_id][4]['po_no'] = $order_id;
			$sequence_array[$col_id][4]['cut_no'] = $cutting_no;

			$sequence_array[$col_id][5]['succeding'] = 6;
			$sequence_array[$col_id][5]['preceding'] = 4;
			$sequence_array[$col_id][5]['job_no'] = $job_no;
			$sequence_array[$col_id][5]['po_no'] = $order_id;
			$sequence_array[$col_id][5]['cut_no'] = $cutting_no;

		} else {
			$sequence_array[$col_id][4]['succeding'] = 5;
			$sequence_array[$col_id][4]['preceding'] = 1;
			$sequence_array[$col_id][4]['job_no'] = $job_no;
			$sequence_array[$col_id][4]['po_no'] = $order_id;
			$sequence_array[$col_id][4]['cut_no'] = $cutting_no;

			$sequence_array[$col_id][5]['succeding'] = 6;
			$sequence_array[$col_id][5]['preceding'] = 4;
			$sequence_array[$col_id][5]['job_no'] = $job_no;
			$sequence_array[$col_id][5]['po_no'] = $order_id;
			$sequence_array[$col_id][5]['cut_no'] = $cutting_no;
		}

	} else // Job Table
	{
		$sql_extrawork = sql_select(" select gmts_item_id ,job_no,embelishment,printseq, embro,embroseq, spworks, spworksseq from wo_po_details_mas_set_details where  job_no='" . $job_no . "'");
		if (count($sql_extrawork) > 0) {
			$last_operation = array();
			foreach ($sql_extrawork as $val) {
				$print_sequence = $val[csf("printseq")];
				$emblishment_sequence = $val[csf("embroseq")] * 1;
				$spwork_sequence = $val[csf("spworksseq")] * 1;
				$tmparr[$print_sequence] = 1;
				$tmparr[$emblishment_sequence] = 2;
				$tmparr[$spwork_sequence] = 4;
				ksort($tmparr);

				if ($spwork_sequence == 0 && $emblishment_sequence == 0 && $print_sequence == 0) {
					$tmparr = array();
				}

				if (count($tmparr) > 0) {
					foreach ($tmparr as $embel_name) {
						$current_op = $embel_name;
						if ($embel_name == 1) //print
						{
							$preceding = "3_1";
							$succeding = "2_1";
						}
						if ($embel_name == 2) {
							$preceding = "3_2";
							$succeding = "2_2"; // $first_op_arr[$row[csf("item_number_id")]]=$row[csf("emb_name")];
						}
						if ($embel_name == 4) {
							$preceding = "3_4";
							$succeding = "2_4"; // $first_op_arr[$row[csf("item_number_id")]]=$row[csf("emb_name")];
						}

						if ($i == 1) {
							$sequence_array[$col_id]["9" . $current_op]['preceding'] = 1;
						} else {
							$sequence_array[$col_id]["9" . $current_op]['preceding'] = $last_preceding;
						}

						if ($last_op != '') {
							$sequence_array[$col_id]["9" . $last_op]['succeding'] = $succeding;
						}

						if ($i == count($sql_extrawork)) {
							$sequence_array[$col_id]["9" . $current_op]['succeding'] = 4;
						} else {
							$sequence_array[$col_id]["9" . $current_op]['succeding'] = $succeding;
						}

						$last_succeding = $succeding;
						$last_preceding = $preceding;
						$last_op = $current_op;
						$i++;

						$sequence_array[$col_id]["9" . $current_op]['job_no'] = $job_no;
						$sequence_array[$col_id]["9" . $current_op]['po_no'] = $order_id;
						$sequence_array[$col_id]["9" . $current_op]['cut_no'] = $cutting_no;
					}
					$sequence_array[$col_id][4]['succeding'] = 5;
					$sequence_array[$col_id][4]['preceding'] = $preceding;
					$sequence_array[$col_id][4]['job_no'] = $job_no;
					$sequence_array[$col_id][4]['po_no'] = $order_id;
					$sequence_array[$col_id][4]['cut_no'] = $cutting_no;

					$sequence_array[$col_id][5]['succeding'] = 6;
					$sequence_array[$col_id][5]['preceding'] = 4;
					$sequence_array[$col_id][5]['job_no'] = $job_no;
					$sequence_array[$col_id][5]['po_no'] = $order_id;
					$sequence_array[$col_id][5]['cut_no'] = $cutting_no;
				} else {
					$sequence_array[$col_id][4]['succeding'] = 5;
					$sequence_array[$col_id][4]['preceding'] = 1;
					$sequence_array[$col_id][4]['job_no'] = $job_no;
					$sequence_array[$col_id][4]['po_no'] = $order_id;
					$sequence_array[$col_id][4]['cut_no'] = $cutting_no;

					$sequence_array[$col_id][5]['succeding'] = 6;
					$sequence_array[$col_id][5]['preceding'] = 4;
					$sequence_array[$col_id][5]['job_no'] = $job_no;
					$sequence_array[$col_id][5]['po_no'] = $order_id;
					$sequence_array[$col_id][5]['cut_no'] = $cutting_no;
				}
			}
		} else {
			$sequence_array[$col_id][4]['succeding'] = 5;
			$sequence_array[$col_id][4]['preceding'] = 1;
			$sequence_array[$col_id][4]['job_no'] = $job_no;
			$sequence_array[$col_id][4]['po_no'] = $order_id;
			$sequence_array[$col_id][4]['cut_no'] = $cutting_no;

			$sequence_array[$col_id][5]['succeding'] = 6;
			$sequence_array[$col_id][5]['preceding'] = 4;
			$sequence_array[$col_id][5]['job_no'] = $job_no;
			$sequence_array[$col_id][5]['po_no'] = $order_id;
			$sequence_array[$col_id][5]['cut_no'] = $cutting_no;
		}
	}
	return $sequence_array;
}

	$production_squence = 2;
	function gmt_production_validation_script($opcode, $is_preceding, $colorSizeid, $cutting_no, $production_squence) {
		$last_operation = array();
		global $production_squence;
		if ($colorSizeid != '') {
			$colorS = " and col_size_id='" . $colorSizeid . "'";
		}
	
		if ($cutting_no != '') {
			$cutting = " and cutting_no='" . $cutting_no . "'";
		} else {
			return $last_operation;
		}
	
		if ($production_squence == 1) // precoting sequence
		{
			if ($cutting_no != '') {
				$cutting = " and cutting_no='" . $cutting_no . "'";
			} else {
				return $last_operation;
			}
	
			$sql = "select preceding_op,succeding_op,embel_name,col_size_id from pro_production_sequence where current_operation=$opcode $colorS $cutting";
			$sql_check= $ci->db->query($sql)->row();    
		} else {
			if ($is_preceding == 1) {
				$str = " and c.production_type=1 ";
			} else {
				$str = " and c.production_type=4 ";
			}
	
			$last_operation[$str] = 0;
			return $last_operation;
		}
	
		foreach ($sql_check as $chkrow) {
			$str = '';
			if ($is_preceding == 1) {
				if (($chkrow->embel_name * 1) == 0) {
					$str = " and c.production_type=" . $chkrow->preceding_op;
				} else {
					$str = " and c.production_type=" . $chkrow->preceding_op . " and a.embel_name=" . $chkrow->embel_name;
				}
	
				$last_operation[$str] .= "," . $chkrow->col_size_id;
			} else {
				$embl = explode("_", $chkrow->succeding_op);
	
				if (($embl[1] * 1) == 0) {
					$str = " and c.production_type=" . $embl[0];
				} else {
					$str = " and c.production_type=" . $embl[0] . " and a.embel_name=" . $embl[1];
				}
	
				$last_operation[$str] .= "," . $chkrow->col_size_id;
			}
		}
		return $last_operation;
	}
	





function fnc_tempengine($table_name="", $user_id="", $entry_form="", $ref_from="", $ref_id_arr=[],  $ref_str_arr="")
{
	//global $con ;

	//$con = oci_pconnect('PLATFORMERPV3', 'PLATFORMERPV3', '//59.152.60.146:5496/logicdb');
	//print_r($GLOBALS['api_db']['username']);die;
	$con = oci_pconnect($GLOBALS['api_db']['username'],$GLOBALS['api_db']['password'],"//".$GLOBALS['api_db']['hostname'].":".$GLOBALS['api_db']['port']."/".$GLOBALS['api_db']['service']);
	
	$numeless=count($ref_id_arr);
	//echo $con.'='.$user_id.'='.$entry_form.'='.$ref_from.'='.$ref_id_arr;
	//print_r($ref_id_arr);
	$psql = "BEGIN PRC_TEMPENGINE(:in_user_id,:in_ref_from,:in_entry_form,:in_ref_id_arr, :in_ref_table); END;";//:in_ref_str_arr, 
	$stmt = oci_parse($con,$psql);
	oci_bind_by_name($stmt,":in_user_id",$user_id);
	oci_bind_by_name($stmt,":in_entry_form",$entry_form);
	oci_bind_by_name($stmt,":in_ref_from",$ref_from);
	
	oci_bind_array_by_name($stmt, ":in_ref_id_arr", $ref_id_arr, $numeless, -1, SQLT_INT);
	//oci_bind_array_by_name($stmt, ":in_ref_str_arr", $ref_str_arr, $numeless, -1, SQLT_CHR);
	
	oci_bind_by_name($stmt,":in_ref_table",$table_name);
	oci_execute($stmt); 
	//echo "jahid";
	oci_commit($con);
	oci_free_statement($stmt);
	oci_close($con);
	//disconnect($con);
}

//Notification.....................................

function insertNotificationData($ref_id,$entry_form,$user_ids,$desc,$is_approved = 0,$is_commit = 0)
{
	$ci=& get_instance();
	$ci->load->database();
	//print_r($desc);die;
	$menu_id=return_field_value("PAGE_ID AS MENU_ID","ELECTRONIC_APPROVAL_SETUP","ENTRY_FORM=$entry_form","MENU_ID");

	$id=return_next_id( "ID", "APPROVAL_NOTIFICATION_ENGINE");

   

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
			
		$data .="(".$id.",".$ref_id.",".$entry_form.",".$menu_id.",'".$desc."',".$user_id.",".$is_approved.",0,1,".$inserted_by.",'".$pc_date_time."')";
		$id=$id+1;
	}
   
    if($is_commit ==1)
	{
		$ci->db->trans_strict(TRUE);
		$ci->db->trans_begin();
	}
	
	try
	{
		$rDelete = execute_query("DELETE FROM APPROVAL_NOTIFICATION_ENGINE WHERE USER_ID in (".$user_ids.") and REF_ID = $ref_id AND ENTRY_FORM = $entry_form ");
		if($rDelete!=1)
		{
			throw new Exception($rDelete);
		}
		
		$rID=sql_insert("APPROVAL_NOTIFICATION_ENGINE",$column,$data);
		if($rID!=1) 
		{
			throw new Exception($rID);
		}
		//print_r(5);die;
		if($is_commit ==1)
		{
			if ($ci->db->trans_status() === FALSE)
			{
				$ci->db->trans_rollback();
				return 0;
			}
			else
			{
				$ci->db->trans_commit();
				return 1;
			}
		}
		return 1;
		
	}
	catch(Exception $e)
	{
		
		if($is_commit ==1)
		{
			$ci->db->trans_rollback();
		}
		return $e->getMessage();
	}
}



function sql_insert( $strTable, $arrNames, $arrValues )
{
	
	$ci=& get_instance();
	$ci->load->database();
	
	$tmpv=explode(")",$arrValues);
	if(count($tmpv)>2)
		$strQuery= "INSERT ALL \n";
	else
		$strQuery= "INSERT  \n";

	for($i=0; $i<count($tmpv)-1; $i++)
	{
		if( strpos(trim($tmpv[$i]), ",")==0)
			$tmpv[$i]=substr_replace($tmpv[$i], " ", 0, 1);
		$strQuery .=" INTO ".$strTable." (".$arrNames.") values ".$tmpv[$i].") \n";
	}

   if(count($tmpv)>2) $strQuery .= "SELECT * FROM dual";

   $result = $ci->db->query($strQuery);

   
   if ($result)
   {
	 return 1;
   }
   else
   {
		$error = $ci->db->error();
		return "Error Code: " . $error['code'] . "\n"."Error Message: " . $error['message'];
   }

   
}


function notificationEngineForApps($id, $company, $entry_form, $array_param = array(), $user_id =0)
{
	$user_ids = "";
	$user_ids = get_electronic_approval_user($company, $entry_form, $array_param, $user_id);
	//return  $user_ids;
	if(!empty($user_ids))
	{
		$users = explode(",",$user_ids);
		$data = array();
		if(!empty($array_param['approval_data']))
		{
			$data = $array_param['approval_data'];
		}

		$desc = "";
		if(!empty($array_param['approval_desc']))
		{
			$desc = $array_param['approval_desc'];
		}
		$is_commit = 0;
		if(!empty($array_param['is_commit']))
		{
			$is_commit = $array_param['is_commit'];
		}
		
		$res =  insertNotificationData($id,$entry_form,$user_ids,$desc,0,$is_commit);
		if($res == 1)
		{
			$title = "";
			if(!empty($array_param['title']))
			{
				$title = $array_param['title'];
			}
			
			$user_email = return_library_arr("SELECT ID,USER_EMAIL FROM USER_PASSWD WHERE ID IN(".$user_ids.")  AND USER_EMAIL IS NOT NULL","ID","USER_EMAIL");
			
			foreach($users as $user_id)
			{
				if(!empty($user_email[$user_id]) && filter_var($user_email[$user_id], FILTER_VALIDATE_EMAIL))
				{
					//sendMail($user_email[$user_id],$title,$data);
				}
				
				$json = $data;// json_encode($data);

				$sql = "select FCM_TOKEN,DEVICE_TYPE from APPROVAL_NOTI_USER_DEVICES where user_id = $user_id order by ID DESC";
				$result  = sql_select_arr($sql);
				
				foreach($result as $row)
				{
					$key = $row['FCM_TOKEN'];
					$device_type = $row['DEVICE_TYPE'];
					push_to_fcm($key,$title,$json,1,$device_type);
				}
			}
		}
		return $res == 1;
	}
	else return 1;
}

function sendMail($to,$subject,$body,$attach='',$from='')
{
	$active_cunnection='logicsoftware97@gmail.com_split_fshvgkbrluxlymmn_split_Logic Platform_split_smtp.gmail.com_split_587_split_TLS_split_reza@logicsoftbd.com,muktobani@gmail.com_split_1_split_0';
	list($user,$pass,$sender,$host,$port,$secure_port,$send_to,$is_smtp,$smtp_debug)=explode('_split_',$active_cunnection);
	$config = Array(
		'protocol' => 'smtp',
		'smtp_host' => $host,
		'smtp_port' => $port,
		'smtp_user' => $user, // change it to yours
		'smtp_pass' => $pass, // change it to yours
		'mailtype' => 'html',
		'charset' => 'iso-8859-1',
		'wordwrap' => TRUE
	);

	if(empty($from))
	{
		$from = $sender;
	}

	$ci=& get_instance();
	$ci->load->library('email', $config);
	$ci->email->set_newline("\r\n");
	$ci->email->from($from); 
	$ci->email->to($to);
	$ci->email->subject($subject);
	$ci->email->message(str_replace("\n","<br>",$body));
	if($ci->email->send())
	{
		return 1;
	}
	else
	{
		return $ci->email->print_debugger();
	}

}

function fnc_variable_engine($company_id, $variable_id){

	$ci=& get_instance();
    $ci->load->database();

	$query_pro_variable = "SELECT * FROM VARIABLE_SETTINGS_PRODUCTION WHERE VARIABLE_LIST =$variable_id and company_name=$company_id and status_active=1 and is_deleted=0";
	
	$table_pro_variable = $ci->db->query($query_pro_variable)->row();

	return $table_pro_variable;
}

function is_duplicate_field($field_name, $table_name, $query_cond) // checkd 3
{
	// This function will Return Last number of Row of table
	// To generate next Id
	// Return value:  true false
	// Uses  single field:: is_duplicate_field("buyer", "lib_buyer", "buyer_name like 'eta'");
	$queryText = "select " . $field_name . " from " . $table_name . " where " . $query_cond . "";
//echo $queryText;
	$nameArray = sql_select($queryText);
	if (count($nameArray) > 0) {
		return 1;
	} else {
		return 0;
	}

	///die;
}

function manage_allocation_transaction_log($log_data)
{
	$id = return_next_id_by_sequence("INV_ALLOCAT_TRANS_LOG_SEQ", "INV_MATERIAL_ALLOCAT_TRANS_LOG", $con);
	$field_array = "ID, ENTRY_FORM, REF_ID, REF_NUMBER, PRODUCT_ID, CURRENT_STOCK, ALLOCATED_QTY,AVAILABLE_QTY, DYED_TYPE,INSERT_DATE";
	$data_array = "(" . $id . "," . $log_data['entry_form'] . "," . $log_data['ref_id'] . ",'" . $log_data['ref_number'] . "'," . $log_data['product_id'] . "," . $log_data['current_stock'] . "," . $log_data['allocated_qty'] . "," .$log_data['available_qty'] . "," . $log_data['dyed_type'] . ",'" . $log_data['insert_date'] . "')";
	//return "INSERT INTO INV_MATERIAL_ALLOCAT_TRANS_LOG(".$field_array.") VALUES".$data_array;
	return sql_insert("INV_MATERIAL_ALLOCAT_TRANS_LOG", $field_array, $data_array);
}
?>