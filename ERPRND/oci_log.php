<?php
date_default_timezone_set("Asia/Dhaka");
require_once('../includes/common.php');



	//$new_conn = $result[csf("server_name")] . "*" . $result[csf("login_name")] . "*" . $result[csf("login_password")] . "*" . $result[csf("database_name")];

//	$dataArray=sql_select( "select EMP_CODE,ID_CARD_NO,FIRST_NAME,MIDDLE_NAME,LAST_NAME,FULL_NAME_BANGLA,DEPARTMENT_ID,DESIGNATION_ID,DESIGNATION_LEVEL,CATEGORY from hrm_employee where EMP_CODE=$data",'',$new_conn );


//$company_library=return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name"  );
	$sql = "select s.username,s.sid,s.serial#,s.last_call_et/60 mins_running,q.sql_text from v$session s join v$sqltext_with_newlines q
	on s.sql_address = q.address where status='ACTIVE' and type <>'BACKGROUND'
	and last_call_et> 60 order by last_call_et desc";
	$mail_sql=sql_select($sql);
	foreach($mail_sql as $row)
	{
		echo $row[csf('username')];
	}
		
		
		
	 


?> 