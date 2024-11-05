<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="bank_info_list_view")
{
	$sql= "select id,bank_name,branch_name,status_active,total_account  from lib_bank where is_deleted=0 order by bank_name  ";
	$arr=array (2=>$row_status);
	echo  create_list_view ( "list_view", "Bank Name,Branch Name,Status,Total Account", "200,200,100","620","220",1, $sql, "get_php_form_data", "id","'load_php_data_to_form'", 1, "0,0,status_active,0", $arr , "bank_name,branch_name,status_active,total_account", "../general_info/requires/bank_info_controller", 'setFilterGrid("list_view",-1);','0,0,0,1') ;	  
}



else if ($action=="load_php_data_to_form")
{
	//echo  "select id,bank_name,branch_name,bank_code,contact_person,contact_no,swift_code,web_site,email,address,country_id,lien_bank,issusing_bank,advising_bank,	salary_bank,remark, status_active from lib_bank  where id='$data'";die;
$nameArray=sql_select( "select id,bank_name,bank_short_name,branch_name,bank_code,contact_person,contact_no,swift_code,web_site,email,address,country_id,lien_bank,issusing_bank,advising_bank,salary_bank,remark, status_active,designation,ac_type_id,cheque_template from lib_bank  where id='$data'" );
		foreach ($nameArray as $inf)
		{
			echo "document.getElementById('txt_bank_name').value = '".($inf[csf("bank_name")])."';\n";
			echo "document.getElementById('txt_bank_short_name').value = '".($inf[csf("bank_short_name")])."';\n";
			echo "document.getElementById('cbo_designation').value = '".($inf[csf("designation")])."';\n";
			echo "document.getElementById('cbo_ac_type').value = '".($inf[csf("ac_type_id")])."';\n";     
			echo "document.getElementById('txt_branch_name').value  = '".($inf[csf("branch_name")])."';\n"; 
			echo "document.getElementById('txt_bank_code').value  = '".($inf[csf("bank_code")])."';\n";
			echo "document.getElementById('txt_bank_address').value  = '".($inf[csf("address")])."';\n"; 
			echo "document.getElementById('txt_bank_email').value  = '".($inf[csf("email")])."';\n";  
			echo "document.getElementById('txt_bank_website').value  = '".($inf[csf("web_site")])."';\n"; 
			echo "document.getElementById('txt_bank_contact_person').value  = '".($inf[csf("contact_person")])."';\n"; 
			echo "document.getElementById('txt_remarks').value  = '".($inf[csf("remark")])."';\n"; 
			echo "document.getElementById('txt_bank_phone_no').value  = '".($inf[csf("contact_no")])."';\n"; 
			echo "document.getElementById('txt_swift_code').value  = '".($inf[csf("swift_code")])."';\n";
			echo "document.getElementById('cbo_cheque_template').value  = '".($inf[csf("cheque_template")])."';\n"; 
			echo "document.getElementById('update_id').value  = '".($inf[csf("id")])."';\n"; 
			if( $inf[csf("issusing_bank")]==1)
			{
			//echo "formObj.cb_issuing_bank.checked = '".checked."';\n";
			echo "document.getElementById('cb_issuing_bank').checked  ='".checked."';\n";
			}
			else
			{
				//echo "formObj.cb_issuing_bank.checked = '".False."';\n";
				echo "document.getElementById('cb_issuing_bank').checked  ='".False."';\n";

			}
			if( $inf[csf("lien_bank")]==1)
			{
			//echo "formObj.cb_lien_bank.checked = '".checked."';\n";
			echo "document.getElementById('cb_lien_bank').checked  ='".checked."';\n";
			}
			else
			{
				//echo "formObj.cb_lien_bank.checked = '".False."';\n";
			echo "document.getElementById('cb_lien_bank').checked  ='".False."';\n";
			}
			if( $inf[csf("advising_bank")]==1)
			{
			//echo "formObj.cb_advs_bank.checked = '".checked."';\n";
			echo "document.getElementById('cb_advs_bank').checked  ='".checked."';\n";

			}
			else
			{
				//echo "formObj.cb_advs_bank.checked = '".False."';\n";
			echo "document.getElementById('cb_advs_bank').checked  ='".False."';\n";
			}
			if( $inf[csf("salary_bank")]==1)
			{
			//echo "formObj.cb_salary_bank.checked = '".checked."';\n";
			echo "document.getElementById('cb_salary_bank').checked  ='".checked."';\n";
			}
			else
			{
				//echo "formObj.cb_salary_bank.checked = '".False."';\n";
			echo "document.getElementById('cb_salary_bank').checked  ='".False."';\n";
			}
			echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_bank_entry',1);\n"; 
			echo "show_list_view('".$inf[csf("id")]."', 'account_list_view', 'account_list_view', '../general_info/requires/bank_info_controller', 'setFilterGrid(\'list_view1\',-1)');\n";  
		}
}

else if ($action=="account_list_view")
{
	//id,account_id,account_type,account_no,currency,loan_limit,loan_type,company_name,po_status,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted
		if ($data!="")
		{
			$sql= "select id, account_id, account_type, account_no, currency, loan_limit, loan_type, company_id, po_status,status_active from lib_bank_account where account_id ='$data'  and is_deleted=0 order by id";
		}
		else
		{
			$sql= "select id, account_id, account_type, account_no, currency, loan_limit, loan_type, company_id ,status_active from lib_bank_account where is_deleted=0 order by id";
		}
		
		$company_name_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
		$arr=array (0=>$commercial_head,2=>$currency,4=>$loan_type,5=>$company_name_arr,6=>$row_status);
		echo  create_list_view ( "list_view1", "Account Type,Account No,Currency,Loan Limit,Limit Type,Company Name,Status", "120,120,70,120,80,120","780","120",1, $sql, "get_php_form_data", "id","'load_php_data_to_form_bank_acc_info'", 1, "account_type,0,currency,0,loan_type,company_id,status_active", $arr , "account_type,account_no,currency,loan_limit,loan_type,company_id,status_active", "../general_info/requires/bank_info_controller", 'setFilterGrid("list_view1",-1);' ) ;	 
}


else if ($action=="load_php_data_to_form_bank_acc_info")
{
		$nameArray=sql_select( "select id, account_id, account_type, account_no, currency, loan_limit, loan_type, company_id ,status_active from lib_bank_account where id='$data'" );
		foreach ($nameArray as $inf)
		{
			//cbo_account_type*txt_account_no*cbo_currency*txt_loan_limit*cbo_loan_type*cbo_company_name*cbo_status*update_id_dtl*update_id  
			echo "document.getElementById('cbo_account_type').value = '".($inf[csf("account_type")])."';\n";    
		    echo "document.getElementById('txt_account_no').value  = '".($inf[csf("account_no")])."';\n"; 
			echo "document.getElementById('cbo_currency').value  = '".($inf[csf("currency")])."';\n"; 
			echo "document.getElementById('txt_loan_limit').value  = '".($inf[csf("loan_limit")])."';\n";  
			echo "document.getElementById('cbo_loan_type').value  = '".($inf[csf("loan_type")])."';\n";
            echo "document.getElementById('cbo_company_name').value  = '".($inf[csf("company_id")])."';\n";  
			echo "document.getElementById('cbo_status').value  = '".($inf[csf("status_active")])."';\n";	
			echo "document.getElementById('update_id_dtl').value  = '".($inf[csf("id")])."';\n";			
			echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_bank_acc_entry',2);\n"; 
		}
}

else if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 

	//	die;
	if ( $operation==0)  // Insert Here
	{
		$duplicate = is_duplicate_field("id","lib_bank","bank_name=$txt_bank_name and branch_name=$txt_branch_name and status_active=1 and is_deleted=0");
		//if (is_duplicate_field( "bank_name", " lib_bank", "bank_name=$txt_bank_name" ) == 1)
		if ($duplicate == 1)
		{
			echo "11**0"; die;
		}
		else
		{
			
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			$id=return_next_id( "id", "lib_bank", 1 ) ;
			$field_array="id,bank_name,bank_short_name,branch_name, bank_code,contact_person,contact_no, 	swift_code,web_site,email,address,country_id,lien_bank,issusing_bank,advising_bank,salary_bank,remark,designation,ac_type_id,cheque_template,inserted_by,insert_date,status_active, is_deleted";
			$data_array="(".$id.",".$txt_bank_name.",".$txt_bank_short_name.",".$txt_branch_name.",".$txt_bank_code.",".$txt_bank_contact_person.",".$txt_bank_phone_no.",".$txt_swift_code.",".$txt_bank_website.",".$txt_bank_email.",".$txt_bank_address.",'',".$cb_lien_bank.",".$cb_issuing_bank.",".$cb_advs_bank.",".$cb_salary_bank.",".$txt_remarks.",".$cbo_designation.",".$cbo_ac_type.",".$cbo_cheque_template.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			
			$rID=sql_insert("lib_bank",$field_array,$data_array,1);
			//echo $rID; die;
			if($db_type==0)
			{
				if($rID )
				{
					mysql_query("COMMIT");  
					echo "0**".$rID."**".$id;
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "10**".$rID;
				}
			}
			
			if($db_type==2 || $db_type==1 )
			{
				if($rID )
				{
					oci_commit($con);  
					echo "0**".$rID."**".$id;
				}
				else
				{
					oci_rollback($con); 
					echo "10**".$rID;
				}
			}
			disconnect($con);
			die;
		}
	}
	
	if ($operation==1)  // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$field_array="bank_name*bank_short_name*branch_name* bank_code*contact_person*contact_no*swift_code*web_site*email*address*country_id*lien_bank*issusing_bank*advising_bank *salary_bank*remark*designation*ac_type_id*cheque_template*updated_by*update_date*status_active*is_deleted";
		$data_array="".$txt_bank_name."*".$txt_bank_short_name."*".$txt_branch_name."*".$txt_bank_code."*".$txt_bank_contact_person."*".$txt_bank_phone_no."*".$txt_swift_code."*".$txt_bank_website."*".$txt_bank_email."*".$txt_bank_address."*''*".$cb_lien_bank."*".$cb_issuing_bank."*".$cb_advs_bank."*".$cb_salary_bank."*".$txt_remarks."*".$cbo_designation."*".$cbo_ac_type."*".$cbo_cheque_template."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";
		//echo "10**".$data_array;die;
		
		$rID=sql_update("lib_bank",$field_array,$data_array,"id","".$update_id."",1);
		if($db_type==0)
		{
			if($rID )
			{
				mysql_query("COMMIT");  
				echo "1**".$rID."**".str_replace("'","",$update_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID )
			{
				oci_commit($con);  
				echo "1**".$rID."**".str_replace("'","",$update_id);
			}
			else
			{
				oci_rollback($con); 
				echo "10**".$rID;
			}
		}
		disconnect($con);
		die;
	}
	
	if ($operation==2)  // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
		$rID=sql_update("lib_bank",$field_array,$data_array,"id","".$update_id."",1);
		if($db_type==0)
		{
			if($rID )
			{
				mysql_query("COMMIT");  
				echo "2**".$rID;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID )
			{
				oci_commit($con); 
				echo "2**".$rID;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$rID;
			}
		}
		disconnect($con);
		die;
	}
}



else if ($action=="save_update_delete_dtl")
{       
       $process = array( &$_POST );
		extract(check_magic_quote_gpc( $process ));
		
		if ($operation==0)  // Insert Here
		{
				$con = connect();
				if($db_type==0)
				{
					mysql_query("BEGIN");
				}
				//cbo_account_type*txt_account_no*cbo_currency*txt_loan_limit*cbo_loan_type*cbo_company_name*cbo_status*update_id_dtl*update_id
				//id,account_id,account_type,account_no,currency,loan_limit,loan_type,company_id,po_status,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted
				$id=return_next_id( "id", "lib_bank_account", 1 ) ;
				$field_array="id,account_id,account_type,account_no,currency,loan_limit,loan_type,company_id,po_status,inserted_by,insert_date,status_active,is_deleted";
				$data_array="(".$id.",".$update_id.",".$cbo_account_type.", ".$txt_account_no.",".$cbo_currency." ,".$txt_loan_limit.",".$cbo_loan_type.",".$cbo_company_name.",'',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_status.",0)";
				$rID=sql_insert("lib_bank_account",$field_array,$data_array,0);
				$total_account=return_field_value("count(account_id)","lib_bank_account","account_id=$update_id and is_deleted=0");
				$field_array="total_account";
				$data_array="".$total_account."";
				$rID1=sql_update("lib_bank",$field_array,$data_array,"id","".$update_id."",1);
				//echo $rID; die;
				if($db_type==0)
				{
					if($rID && $rID1 )
					{
						mysql_query("COMMIT");  
						echo "0**".$rID1."**".$id;
					}
					else
					{
						mysql_query("ROLLBACK"); 
						echo "10**".$rID1;
					}
				}
				
				if($db_type==2 || $db_type==1 )
				{  
				   	if($rID && $rID1 )
					{
						oci_commit($con) ; 
						echo "0**".$rID1."**".$id;
					}
					else
					{
						oci_rollback($con); 
						echo "10**".$rID1;
					}
				}
				disconnect($con);
				die;
		}
		
		if ($operation==1)  //Update Here
		{
				$con = connect();
				if($db_type==0)
				{
					mysql_query("BEGIN");
				}
				$field_array="account_id*account_type*account_no*currency*loan_limit*loan_type*company_id*po_status*updated_by*update_date*status_active*is_deleted";
				$data_array="".$update_id."*".$cbo_account_type."* ".$txt_account_no."*".$cbo_currency." *".$txt_loan_limit."*".$cbo_loan_type."*".$cbo_company_name."*''*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_status."*0";
				$rID=sql_update("lib_bank_account",$field_array,$data_array,"id","".$update_id_dtl."",1);
				//echo $rID; die;
				if($db_type==0)
				{
				if($rID )
				{
					mysql_query("COMMIT");  
					echo "1**".$rID;
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "10**".$rID;
				}
				}
				
				if($db_type==2 || $db_type==1 )
				{
				if($rID )
					{
						oci_commit($con) ;  
						echo "1**".$rID;
					}
				else
					{
						oci_rollback($con);
						echo "10**".$rID;
					}
				}
				disconnect($con);
				die;
		}
		
		if ($operation==2)  // Delete Here
		{
				$con = connect();
				if($db_type==0)
				{
					mysql_query("BEGIN");
				}
				$field_array="updated_by*update_date*status_active*is_deleted";
				$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
				$rID=sql_update("lib_bank_account",$field_array,$data_array,"id","".$update_id_dtl."",0);
				$total_account=return_field_value("count(account_id)","lib_bank_account","account_id=$update_id and is_deleted=0");
				$field_array="total_account";
				$data_array="".$total_account."";
				$rID1=sql_update("lib_bank",$field_array,$data_array,"id","".$update_id."",1);
				if($db_type==0)
				{
				if($rID )
				{
					mysql_query("COMMIT");  
					echo "2**".$rID;
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "10**".$rID;
				}
				}
				
				if($db_type==2 || $db_type==1 )
				{
					if($rID )
						{
						oci_commit($con) ;  
						echo "2**".$rID;
						 }
					else
						{
						oci_rollback($con);
						echo "10**".$rID;
						}
				}
				disconnect($con);
				die;
		}
}

if($action == "comm_distribution")
{
	echo load_html_head_contents("Com. Proceed Distribution %", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $update_id;die;
	//$_SESSION['page_permission']=$permission;
	$permission=$_SESSION['page_permission'];
	//echo $permission."456";
	//$buyer_name=return_field_value("buyer_name", "lib_buyer", "id=$hidden_buyer_id");
	?>
	<script>
	function add_factor_row( i) 
	{	
		var achead=0; var adpecent=0;
		var row_num=$('#tbl_pay_head tbody tr').length;
		
		if (row_num!=i)
		{
			return false;
		}
		i++;
		
		/*if(i!=2)
		{
			achead=$('#cbodistributionHead_'+(i-1)).val();
			adpecent=$('#txtpercent_'+(i-1)).val();
		}
		else
		{
			achead=$('cbodistributionHead_1').val();
			adpecent=$('#txtpercent_1').val();
		}*/
		
		//alert(adpecent);
	 
		$("#tbl_pay_head tbody tr:last").clone().find("input,select").each(function() {
			$(this).attr({
			'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i; },
			//'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i; },
			'value': function(_, value) { if(value=='+' || value=="-"){return value}else{return ''} }              
			});
			
		}).end().appendTo("#tbl_pay_head");
		$('#cbodistributionHead_'+i).val(0);
		$("#tbl_pay_head tbody tr:last ").removeAttr('id').attr('id','tr_'+i);
		
		
		var k=i-1;
		$('#incrementfactor_'+k).hide();
		$('#decrementfactor_'+k).hide();
		//$('#updateiddtls_'+i).val('');
		
		$('#incrementfactor_'+i).removeAttr("onClick").attr("onClick","add_factor_row("+i+");");
		$('#decrementfactor_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+");");
		//$('#cbodistributionHead_'+i).val(achead);
		//$('#txtpercent_'+i).val(adpecent);
		
	}
	
	function fn_deletebreak_down_tr(rowNo ) 
	{
		var numRow = $('#tbl_pay_head tbody tr').length;
		if(numRow==rowNo && rowNo!=1)
		{
			var k=rowNo-1;
			$('#incrementfactor_'+k).show();
			$('#decrementfactor_'+k).show();
			
			$('#tbl_pay_head tbody tr:last').remove();
		}
		else
			return false;
	}
	
	var permission='<? echo $permission; ?>';
	function fnc_distribute_entry(operation)
	{
		if(operation==2)
		{
			alert("Delete Restricted");return;
		}
		
		if( form_validation('cbo_company_name*txt_dis_date','Company * Distribute Date')==false )
		{
			return;
		}
		var row_num=$('#tbl_pay_head tbody tr').length;
		var pay_head_arr=new Array();
		var data_all="";var tot_percent=0; var cbodistributionHead=txtpercent="";
		for (var i=1; i<=row_num; i++)
		{
			if( form_validation('cbodistributionHead_'+i+'*txtpercent_'+i,'Pay Head*Percent')==false )
			{
				return;
			}
			
			if( jQuery.inArray( $('#cbodistributionHead_' + i).val(), pay_head_arr ) == -1 )
			{
				pay_head_arr.push( $('#cbodistributionHead_' + i).val() );
			}
			else
			{
				alert("Duplicate Pay Head");return;
			}
			tot_percent+=$('#txtpercent_'+i).val()*1;
			data_all=data_all+'&cbodistributionHead_'+i+"="+$('#cbodistributionHead_' + i).val()+'&txtpercent_'+i+"="+$('#txtpercent_' + i).val();
			//data_all=data_all+get_submitted_data_string('cbodistributionHead_'+i+'*txtpercent_'+i,"../../../");
			
		}
		
		if(tot_percent != 100)
		{
			alert("Total Distribution Percent Must Be 100");return;
		}
		
		var data="action=save_update_delete_distribute&operation="+operation+"&row_num="+row_num+get_submitted_data_string('cbo_company_name*txt_dis_date*update_dts_id*txt_bank_id',"../../../")+data_all;
		//alert(data);return;
		freeze_window(operation);
		http.open("POST","bank_info_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_comm_info_reponse;
	}
	
	function fnc_comm_info_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split('**');
			//release_freezing();
			//alert(trim(http.responseText));return;
			//if (reponse[0].length>2) reponse[0]=10;
			if(reponse[0]==0 || reponse[0]==1)
			{
				//$('#update_id').val(reponse[1]);
				show_list_view( $('#txt_bank_id').val(),'show_comm_list_view','date_wise_list','bank_info_controller','');
				//reset_form('commcostinfo_1','','');
				set_button_status(0, permission, 'fnc_comm_info');
				//parent.emailwindow.hide();
			}
			release_freezing();
		}
	}
	
	function fn_dtls_data(dtls_id)
	{
		get_php_form_data(dtls_id,'load_php_comm_data_to_form','bank_info_controller');
		var dtls_html_data=return_global_ajax_value( dtls_id, 'populate_dtls_data', '', 'bank_info_controller');
		$("#incrising_table_body").html("");
		$("#incrising_table_body").html(dtls_html_data);
		//set_all_onclick();
	}
</script>
</head>
<body>
	<div align="center">
		<? echo load_freeze_divs ("../../../",$permission);  ?>
		<fieldset style="width:600px;">
			<form name="commcostinfo_1" id="commcostinfo_1" autocomplete="off" method="post">
                <table cellspacing="1" cellpadding="1" width="600" id="tbl_pay_head" border="1" class="rpt_table" align="center" rules="all">
                	<thead>
                        <tr>
                            <td width="100" align="left">Company Name</td>
                            <td width="180">
							<? 
                                echo create_drop_down( "cbo_company_name", 172, "select company_name,id from lib_company where is_deleted=0 and status_active=1 $company_name order by company_name",'id,company_name', 1, '--- Select Company ---', 0, "" );
                            ?>
                            </td>
                            <td width="100">Date</td>
                            <td width="140"><input type="text" name="txt_dis_date" id="txt_dis_date" class="datepicker" style="width:120px;" placeholder="Select Date"  value="<? echo date('d-m-Y');?>" readonly/></td>
                            <td>&nbsp;</td>
                        </tr>
                    </thead>
                    <tbody id="incrising_table_body">
                    	<tr id="tr_1">
                        	<td>Account Head</td>
                            <td>
                            <?
							echo create_drop_down( "cbodistributionHead_1", 172, $commercial_head,"", 1, "-- Select Account Head --", 0, "","","","","","","","","cbodistributionHead[]" );
							?>
                            </td>
                            <td>Distribution  %</td>
                            <td><input type="text" id="txtpercent_1" name="txtpercent[]" style="width:120px;" class="text_boxes_numeric" ></td>
                            <td>
                            <input style="width:30px;" type="button" id="incrementfactor_1" name="incrementfactor[]"  class="formbutton" value="+" onClick="add_factor_row(1)"/>
                     		<input style="width:30px;" type="button" id="decrementfactor_1" name="decrementfactor[]"  class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(1)"/>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                    	<tr>
                        	<td align="center" colspan="5" valign="middle" class="button_container">
							<?
                            echo load_submit_buttons( $permission, "fnc_distribute_entry", 0,0,"",0);
                            ?>
                            <input type="hidden" id="update_dts_id" name="update_dts_id" value="">
                            <input type="hidden" id="txt_bank_id" name="txt_bank_id" value="<? echo $update_id; ?>">
                            </td>
                        </tr>
                    </tfoot>
                </table>
			</form>
		</fieldset>
        <div id="date_wise_list"></div>
	</div>
    
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    <script>
        show_list_view( $('#txt_bank_id').val(),'show_comm_list_view','date_wise_list','bank_info_controller','');
    </script>
    </html>
	<?
    exit();
}

if($action=="populate_dtls_data")
{
	$sql_dis="select id, bank_id, company_id, dtis_date, head_percent_string from bank_head_distribute_dtls where id=$data";
	$nameArray=sql_select($sql_dis);
	$i=1;
	
	foreach($nameArray as $row)
	{
		$ac_head_arr=explode("__",$row[csf("head_percent_string")]);
		$tot_row=count($ac_head_arr);
		foreach($ac_head_arr as $val)
		{
			$ref_val=explode("_",$val);
			//$ac_head_data.=$commercial_head[$ref_val[0]].",";
			?>
            <tr id="tr_<?=$i;?>">
                <td>Account Head</td>
                <td>
                <?
                echo create_drop_down( "cbodistributionHead_".$i, 172, $commercial_head,"", 1, "-- Select Account Head --", $ref_val[0], "","","","","","","","","cbodistributionHead[]" );
                ?>
                </td>
                <td>Distribution  %</td>
                <td><input type="text" id="txtpercent_<?=$i;?>" name="txtpercent[]" style="width:120px;" class="text_boxes_numeric" value="<?= $ref_val[1]; ?>" ></td>
                <td>
                <?
                if($i==$tot_row)
                {
                    ?>
                    <input style="width:30px;" type="button" id="incrementfactor_<?=$i;?>" name="incrementfactor[]"  class="formbutton" value="+" onClick="add_factor_row(<?=$i;?>)"/>
                    <input style="width:30px;" type="button" id="decrementfactor_<?=$i;?>" name="decrementfactor[]"  class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<?=$i;?>)"/>
                    <?
                }
                else
                {
                    ?>
                    <input style="width:30px; display:none" type="button" id="incrementfactor_<?=$i;?>" name="incrementfactor[]"  class="formbutton" value="+" onClick="add_factor_row(<?=$i;?>)"/>
                    <input style="width:30px; display:none" type="button" id="decrementfactor_<?=$i;?>" name="decrementfactor[]"  class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<?=$i;?>)"/>
                    <?
                }
                ?>
                </td>
            </tr>
            <?
            $i++;
		}
	}
	die;
}

if($action=="save_update_delete_distribute")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$head_percent_string="";
	for($i=1; $i<=$row_num; $i++)
	{
		$cbodistributionHead='cbodistributionHead_'.$i;
		$txtpercent='txtpercent_'.$i;
		$head_percent_string.=$$cbodistributionHead."_".$$txtpercent."__";
	}
	$head_percent_string=chop($head_percent_string,"__");
	
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0) mysql_query("BEGIN");
		
		//echo "10**select id from lib_comm_import_fabric where mst_id=$hidden_buyer_id and effective_date=$txt_effective_date and is_deleted=0";die;
		/*if (is_duplicate_field( "id", "lib_comm_import_fabric", "mst_id=$hidden_buyer_id and effective_date=$txt_effective_date and is_deleted=0" ) == 1)
		{
			echo "11**0";
			die;
		}
		data_all=data_all+get_submitted_data_string('cbodistributionHead_'+i+'*txtpercent_'+i,"../../../");
		var data="action=save_update_delete_distribute&operation="+operation+"&row_num="+row_num+get_submitted_data_string('cbo_company_name*txt_dis_date*update_dts_id*txt_bank_id',"../../../")+data_all;
		*/
		//echo "10** $row_num =".$head_percent_string;die;
		
		$id=return_next_id( "id", "bank_head_distribute_dtls", 1 ) ;
		$field_array="id, bank_id, company_id, dtis_date, head_percent_string, inserted_by, insert_date, status_active, is_deleted";
		$data_array ="(".$id.",".$txt_bank_id.",".$cbo_company_name.",".$txt_dis_date.",'".$head_percent_string."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";

		$rID=sql_insert("bank_head_distribute_dtls",$field_array,$data_array,1);
		
		
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");
				echo "0**".$id;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$id;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "0**".$id;
			}
			else{
				oci_rollback($con);
				echo "10**".$id;
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)  // Update Here
	{
		$con = connect();
		if($db_type==0) mysql_query("BEGIN");
		
		/*if (is_duplicate_field( "id", "lib_comm_import_fabric", "id!=$update_id and mst_id=$hidden_buyer_id and effective_date=$txt_effective_date and is_deleted=0") == 1)
		{
			echo "11**0";
			die;
		}*/
		
		$id=str_replace("'","",$update_dts_id);
		$field_array="company_id*dtis_date*head_percent_string*update_by*update_date";
		 
		$data_array="".$cbo_company_name."*".$txt_dis_date."*'".$head_percent_string."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$rID=sql_update("bank_head_distribute_dtls",$field_array,$data_array,"id","".$update_dts_id."",1);
		
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");
				echo "1**".$id;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$id;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "1**".$id;
			}
			else{
				oci_rollback($con);
				echo "10**".$id;
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)  // Delete Here
	{
		/*$con = connect();
		if($db_type==0) mysql_query("BEGIN");
		
		
		$id=str_replace("'","",$update_id);
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";

		$rID=sql_delete("lib_comm_import_fabric",$field_array,$data_array,"id","".$update_id."",1);
		
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");
				echo "2**".$id;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$id;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "2**".$id;
			}
			else{
				oci_rollback($con);
				echo "10**".$id;
			}
		}
		disconnect($con);
		die;*/
	}
}

if ($action=="show_comm_list_view")
{
	$sql_dis="select id, bank_id, company_id, dtis_date, head_percent_string from bank_head_distribute_dtls where bank_id=$data";
	$sql_dis_result=sql_select($sql_dis);
	if(count($sql_dis_result)>0)
	{
		$company_arr = return_library_array("select id,company_name from lib_company","id","company_name");
		?>
		<div>
		<table cellspacing="1" cellpadding="1" width="600" id="dtls_table" border="1" class="rpt_table" align="center" rules="all">
			<thead>
				<tr>
					<th width="150" align="left">Company</th>
					<th width="100">Date</th>
					<th>Account Head</th>
				</tr>
			</thead>
			<tbody>
			<?
			$i=1;
			foreach($sql_dis_result as $row)
			{
				if ($i%2==0)$bgcolor="#E9F3FF";						
				else $bgcolor="#FFFFFF";
				$ac_head_arr=explode("__",$row[csf("head_percent_string")]);
				$ac_head_data="";
				foreach($ac_head_arr as $val)
				{
					$ref_val=explode("_",$val);
					$ac_head_data.=$commercial_head[$ref_val[0]].",";
				}
				$ac_head_data=chop($ac_head_data,",");
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick="fn_dtls_data(<? echo $row[csf('id')]?>);" style="cursor:pointer">
					<td><? echo $company_arr[$row[csf("company_id")]]; ?></td>
					<td align="center"><? echo change_date_format($row[csf("dtis_date")]); ?></td>
					<td><? echo $ac_head_data; ?></td>
				</tr>
				<? 
				$i++; 
			}
			?>
			</tbody>
		</table>
		</div>
		<?
	}
	
	//echo create_list_view ( "list_view", "Company,Com. Cost for import fabric %,Short Realization %", "130,100,100","400","200",0, "select id, effective_date, com_cost_imp_fabric, short_realization_per from lib_comm_import_fabric where mst_id=$data", "get_php_form_data", "id", "'load_php_comm_data_to_form'", 1, "0,0,0", "" , "effective_date,com_cost_imp_fabric,short_realization_per", "buyer_info_controller",'setFilterGrid("list_view",-1);','3,0,0');
	exit();
}

if ($action=="load_php_comm_data_to_form")
{
	$sql_dis="select id, bank_id, company_id, dtis_date, head_percent_string from bank_head_distribute_dtls where id=$data";
	$nameArray=sql_select($sql_dis);
	foreach ($nameArray as $inf)
	{
		echo "document.getElementById('update_dts_id').value = '".($inf[csf("id")])."';\n";
		echo "document.getElementById('txt_bank_id').value = '".($inf[csf("bank_id")])."';\n";
		echo "document.getElementById('cbo_company_name').value = '".($inf[csf("company_id")])."';\n";
		echo "document.getElementById('txt_dis_date').value  = '".change_date_format($inf[csf("dtis_date")])."';\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_distribute_entry',1);\n";
	}
	exit();
}


?>