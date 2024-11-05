<? 
session_start();
include('../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$permission=$_SESSION['page_permission'];
$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

//--------------------------------------------------------------------------------------------------------------------
if($db_type==0) $concat_m="concat(first_name,middle_name,last_name) as emp_name ";
	if($db_type==2) $concat_m="first_name || middle_name || last_name as emp_name ";

	if($db_type==0) $grpby_m=" group by emp_code order by emp_code";
	if($db_type==2) $grpby_m=" group by emp_code,id,designation_name, line_name, company_name, department_name,first_name, middle_name ,last_name order by emp_code";
	
if($action=="employee_search_popup")
{
	echo load_html_head_contents("PO Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
?>
	<script>
		
		var selected_id = new Array; var selected_name = new Array;
		
		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ )
			{
				$('#tr_'+i).trigger('click'); 
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str ) {
			
			if (str!="") str=str.split("_");
			 
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );
			 
			if( jQuery.inArray( str[1], selected_id ) == -1 ) {
				selected_id.push( str[1] );
				selected_name.push( str[2] );
				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str[1] ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			$('#hide_po_id').val( id );
			$('#hide_emp_code').val( name );
		}
		
		function hidden_field_reset()
		{
			$('#hide_po_id').val('');
			$('#hide_emp_code').val( '' );
			selected_id = new Array();
			selected_name = new Array();
		}
	
    </script>

</head>

<body>
<div align="center">
	<form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:780px;">
            <table width="600" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Company Name</th>
                    <th>Department Name</th>
                    <th>Line Name</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
                    <input type="hidden" name="hide_po_id" id="hide_po_id" value="" />
                    <input type="hidden" name="hide_emp_code" id="hide_emp_code" value="" />
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <?
							 if($db_type==0) $m_null="company_name!=''";
							  if($db_type==2) $m_null="company_name!='0' ";
							
		//echo "select distinct  company_name from lib_employee comp where status_active =1 and is_deleted=0 and $m_null order by company_name";
								 echo create_drop_down( "cbo_company_name", 150, "select distinct  company_name from lib_employee comp where status_active =1 and is_deleted=0 and $m_null order by company_name","company_name,company_name", 1, "-- Select Company --", $companyID, "" );
								
							?>
                        </td>                 
                        <td align="center">	
                    	<?
                       		echo create_drop_down( "cbo_department", 110,"Select distinct  department_name from lib_employee where department_name!='' order by department_name","department_name,department_name", 1, "-- Select --", 0, "",0,"" );
						?>
                        </td>     
                        <td align="center">	
                        <?			
                            echo create_drop_down( "cbo_line", 110, "Select distinct line_name from  lib_employee order by line_name","line_name,line_name", 1, "-- Select --", 0, "",'',"" );	
						?>
                        </td> 	
                       
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_name').value+'**'+document.getElementById('cbo_department').value+'**'+document.getElementById('cbo_line').value, 'create_employee_search_list_view', 'search_div', 'manual_attendance_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1);');" style="width:100px;" />
                    	</td>
                    </tr>
                   
            	</tbody>
           	</table>
            <div style="margin-top:10px" id="search_div"></div>
		</fieldset>
	</form>
</div>
</body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="create_employee_search_list_view")
{
	$data=explode('**',$data);
	if( $data[0]!="" ) $comp_cond=" and company_name='$data[0]' "; else $comp_cond="";
	if( $data[1]!='0' && $data[1]!='') $dep_cond=" and department_name='$data[1]' "; else $dep_cond="";
	if( $data[2]!='0' && $data[2]!='') $line_cond=" and line_name='$data[2]' "; else $line_cond="";
	
	//$arr=array(0=>$buyer_arr,5=>$unit_of_measurement);
	
							
	$sql = "Select id,emp_code,$concat_m, designation_name, line_name, company_name, department_name from lib_employee where status_active=1 and is_deleted=0 $comp_cond $dep_cond $line_cond  $grpby_m";
		
	echo create_list_view("tbl_list_search", "Emp Code,Emp Name,Designation,Depertment,Line No", "110,110,110,110,110","700","210",0, $sql , "js_set_value", "id,emp_code", "", 1, "0,0,0,0,0", $arr , "emp_code,emp_name,designation_name,department_name,line_name", "",'','0,0,0,0,0','',1) ;
	
   exit(); 
} 




if($action=="report_container")
{

	//echo "sohel";die;
	$data=explode("*",$data);
	if( $data[0]!="" ) $comp_cond=" and company_name='$data[0]' "; else $comp_cond="";
	if( $data[1]!='0' && $data[1]!='') $dep_cond=" and department_name='$data[1]' "; else $dep_cond="";
	if( $data[2]!='0' && $data[2]!='') $line_cond=" and line_name='$data[2]' "; else $line_cond="";
	if( $data[3]!="" ) $emp_code_cond=" and emp_code in ($data[3])"; else $emp_code_cond="";
	if($data[4]!="" && $data[5]!="")
	{
		//echo $data[4];
		if($db_type==0)
		{
		$date_cond="where attnd_date between '".change_date_format(trim($data[4]), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($data[5]), "yyyy-mm-dd", "-")."'";
		}
		if($db_type==2)
		{
		$date_cond="where attnd_date between '".change_date_format(trim($data[4]), "mm-dd-yyyy", "-",1)."' and '".change_date_format(trim($data[5]),  "mm-dd-yyyy", "-",1)."'";
		}
	}
	
	
	 $sql_date = "Select id,emp_code,attnd_date,sign_in_time,sign_out_time from prod_attendance $date_cond";
	
	$sql=sql_select($sql_date);
	
		$in_time_arr=array();
		$out_time_arr=array();
		$table_id_arr=array();
		foreach ( $sql as $row )
		{
			$d_new=change_date_format($row[csf('attnd_date')],'','',1);
			$intime_arr[$row[csf('emp_code')]][$d_new]=$row[csf('sign_in_time')];
			$out_time_arr[$row[csf('emp_code')]][$d_new]=$row[csf('sign_out_time')];
			$table_id_arr[$row[csf('emp_code')]][$d_new]=$row[csf('id')];
		} 
		//var_dump($intime_arr);//change_date_format

	?>
    
	<table cellspacing="0" border="1" rules="all" cellpadding="0" width="930" class="rpt_table">
			<thead>				
                <th width="20">SL</th>
                <th width="100">Code</th>
                <th width="150">Employee Name</th>
                <th width="150">Designation</th>
                 <th width="120">Department</th>
                <th width="80">Date</th>
                <th width="130">In Time</th>
                <th width="130">Out Time</th>
                <th width="">Next Day</th>
           </thead>
   </table>
            <div style="width:950px; overflow-y:scroll; max-height:250px" id="scroll_body">
            <table cellspacing="0" border="1" rules="all" cellpadding="0" width="930" class="rpt_table" id="emp_tab">
           <?
		   /*	if($db_type==0) $concat_m="concat(first_name,middle_name,last_name) as emp_name";
			if($db_type==2) $concat_m="first_name || middle_name || last_name as emp_name ";
			else $concat_m="";*/
	
		   $emp_data = "Select emp_code,$concat_m, designation_name, line_name, company_name, department_name from lib_employee where status_active=1 and is_deleted=0 $comp_cond $dep_cond $line_cond $emp_code_cond $grpby_m";
			$sql_dtls=sql_select($emp_data);
			$datediff=datediff("d",$data[4],$data[5]);
			//echo $emp_data; die;
			$sl=0;
			foreach ( $sql_dtls as $row )
			{
				for($j=0;$j<$datediff;$j++)
           	 	{
					$newdate =add_date(str_replace("'","",$data[4]),$j);
					$date_format=change_date_format($newdate,'','',1);
					$sl++;
					if ($sl%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
					<tr  bgcolor="<? echo $bgcolor; ?>" >         	
                        <td width="20"><? echo $sl; ?></td>	
                        <td width="100"><? echo $row[csf('emp_code')]; ?> <input type="hidden" name="txtempcode_<? echo $sl;?>" id="txtempcode_<? echo $sl;?>"  value="<? echo $row[csf('emp_code')]; ?>" size="8" readonly /></td> 				  
                        <td width="150"><? echo $row[csf('emp_name')]; ?> </td>	
                        <td width="150"><? echo $row[csf('designation_name')];?></td>					
                        <td width="120"><? echo $row[csf('department_name')]; ?></td>	
                        <td width="80"> <input type="text" name="txtattdate_<? echo $sl;?>" id="txtattdate_<? echo $sl;?>" value="<? echo change_date_format($newdate); ?>" size="10" readonly /></td>	
                        <td width="130" align="center">
                        <? 
						
						if($intime_arr[$row[csf('emp_code')]][$date_format]=="")
						{
							$show_date='';
						}
						else
						{ 
						 	$show_date=explode(":",DATE("h:i", STRTOTIME( $intime_arr[$row[csf('emp_code')]][$date_format] )));
						}
						?>
                         <input type="text" name="txtintimehours_<? echo $sl;?>" id="txtintimehours_<? echo $sl;?>" class="text_boxes_numeric" placeholder="HH"  style="width:20px;"  onKeyUp="fnc_move_cursor(this.value,'txtintimehours_<? echo $sl;?>','txtintimeminuties_<? echo $sl;?>',2,23);" value="<? echo $show_date[0]; ?>" /> :
                                    <input type="text" name="txtintimeminuties_<? echo $sl;?>" id="txtintimeminuties_<? echo $sl;?>" class="text_boxes_numeric" placeholder="MM"  style="width:20px;" onKeyUp="fnc_move_cursor(this.value,'txtintimeminuties_<? echo $sl;?>','txtintimeminuties_<? echo $sl;?>',2,59)" value="<? echo $show_date[1]; ?>" />
						<? //echo $intime_arr[$row[csf('emp_code')]][$newdate]; ?>
                        
                        </td>	
                        <td align="center" width="130">
                         <? 
						 if($out_time_arr[$row[csf('emp_code')]][$date_format]=="")
						{
							$show_date_out='';
						}
						else
						{
						  //$show_date_out=explode(":",DATE("h:i", STRTOTIME( $out_time_arr[$row[csf('emp_code')]][$newdate] )));
						  $show_date_out=explode(":",date("H:i", strtotime($out_time_arr[$row[csf('emp_code')]][$date_format])));
						  //echo  DATE("h:i", STRTOTIME( $out_time_arr[$row[csf('emp_code')]][$newdate] ));
						 // echo date("H:i", strtotime($out_time_arr[$row[csf('emp_code')]][$newdate]));
						}
						
						?>
                        <input type="text" name="txtouttimehours_<? echo $sl;?>" id="txtouttimehours_<? echo $sl;?>" class="text_boxes_numeric" placeholder="HH"  style="width:20px;"  onKeyUp="fnc_move_cursor(this.value,'txtouttimehours_<? echo $sl;?>','txtouttimeminuties_<? echo $sl;?>',2,23);" value="<? echo $show_date_out[0]; ?>" /> :
                                    <input type="text" name="txtouttimeminuties_<? echo $sl;?>" id="txtouttimeminuties_<? echo $sl;?>" class="text_boxes_numeric" placeholder="MM"  style="width:20px;"  onKeyUp="fnc_move_cursor(this.value,'txtouttimeminuties_<? echo $sl;?>','txtouttimeminuties_<? echo $sl;?>',2,59)" value="<? echo $show_date_out[1]; ?>" />
						
						<? //echo $out_time_arr[$row[csf('emp_code')]][$newdate]; 
						//02-02-2014 07:20:00 AM
					
						$dayt=explode(" ",$out_time_arr[$row[csf('emp_code')]][$date_format]);
						$daysss=explode("-",$dayt[0]);
						
						$dd=$daysss[2]."-".$daysss[1]."-".$daysss[0];
						
						$sel="";
						
						if( strtotime($date_format) < strtotime($dd) )
						{
							$sel="checked='checked'";
							$check_val=1;
						}
						else
						{
							$check_val=0;
						}
							  
						?>
                        </td>
                        <td>
                        <?
          					
						?>
                       <input type="hidden" name="updateid_<? echo $sl;?>" id="updateid_<? echo $sl;?>"  value="<? /*$date_format=change_date_format($newdate,'','',1);*/ echo $table_id_arr[$row[csf('emp_code')]][$date_format]; ?>" size="8" />
                       <input type="checkbox" name="chk_all" id="chk_all_<?php echo $sl; ?>"  <? echo $sel; ?> onClick="js_set_value(<? echo $sl; ?>)" value="<? echo $check_val; ?>" />
                        </td>	
					</tr> 
					<?
				}
			}
		   ?>
           </table>
           </div>           
		
     <?
	
	exit();
}

if ($action=="save_update_delete")
{
	
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)  // Insert Here=================================
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$id=return_next_id( "id", "prod_attendance", 1 ) ; 
		$field_array="id,emp_code,attnd_date,sign_in_time,sign_out_time,dstatus";
		$field_array_up ="emp_code*attnd_date*sign_in_time*sign_out_time";
		
		$statas="P";
		$add_comma=0; 
		for($i=1;$i<=$tot_row;$i++)
		{
			$txtempcode="txtempcode_".$i;
			$txtattdate="txtattdate_".$i;
			
			
			$txtintimehours="txtintimehours_".$i;
			$txtintimeminuties="txtintimeminuties_".$i;
			
			$txtouttimehours="txtouttimehours_".$i;
			$txtouttimeminuties="txtouttimeminuties_".$i;
			
			$updateid="updateid_".$i;
			$is_next="chk_all_".$i;
			
			//echo $$is_next;die;
			
			if(str_replace("'","",$$is_next)==1){  $crnt_date = add_date( str_replace("'","",$$txtattdate) ,1);  } else { $crnt_date = $$txtattdate; }
			
			//die;
			
			if(str_replace("'","",$$txtintimehours)=="" || str_replace("'","",$$txtouttimehours)=="")
			{
				$sign_in_time="";
				$sign_out_time="";	
			}
			else
			{ //change_date_format
				$sign_in_time= date('d-m-Y h:i:s A', STRTOTIME(str_replace("'","",$$txtattdate). str_replace("'","",$$txtintimehours).":".str_replace("'","",$$txtintimeminuties) ));
				//$in_date=change_date_format($sign_in_time,'mm-dd-yyyy','-',1);
				$sign_out_time= date('d-m-Y h:i:s A', STRTOTIME(str_replace("'","",$crnt_date). str_replace("'","",$$txtouttimehours).":".str_replace("'","",$$txtouttimeminuties) ));
				//$out_date=change_date_format($sign_out_time,'mm-dd-yyyy','-',1);
			}
			
			//$att_date=$$txtattdate;
			//print($att_date);die;
			 $crnt_date;
			 //print_r( $$updateid);die;
			//echo $dd=change_date_format(str_replace("'","",$crnt_date),'','',1);die;
			if(str_replace("'","",$$updateid)=="")
			{
				$datetxt=change_date_format(str_replace("'","",$$txtattdate),'','',1);
				if ($add_comma!=0) $data_array .=",";
				$data_array .="(".$id.",".$$txtempcode.",'".$datetxt."','".$sign_in_time."','".$sign_out_time."','".$statas."')";
				$id=$id+1;
				$add_comma++;
				//print_r($data_array);
				
			}
			else
			{	
				$datetxt=change_date_format(str_replace("'","",$$txtattdate),'','',1);
				$id_arr[]=str_replace("'",'',$$updateid);
				$data_array_up[str_replace("'",'',$$updateid)] =explode("*",("".$$txtempcode."*'".$datetxt."'*'".$sign_in_time."'*'".$sign_out_time."'"));
				//$return_no=$$updateid;
				//print_r($data_array_up);
			}
		}
		$rID=1;
		if($data_array!="")
		{
			//echo "INSERT INTO prod_attendance (".$field_array.") VALUES ".$data_array;
			$rID=sql_insert("prod_attendance",$field_array,$data_array,0);
		}
		$rID2=1;
		if($data_array_up!="")
		{
			//print_r($data_array_up);
			//echo bulk_update_sql_statement( "prod_attendance", "id",$field_array_up,$data_array_up,$id_arr );
			$rID2=execute_query(bulk_update_sql_statement("prod_attendance","id",$field_array_up,$data_array_up,$id_arr ),0);
		}
		
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "0";
				//echo "0**".str_replace("'",'',$return_no);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10";
			}
		}
		if($db_type==2)
		{
			if($rID && $rID2)
			{
				oci_commit($con);
				echo "0";
				//echo "0**".str_replace("'",'',$return_no);
			}
			else
			{
				oci_rollback($con);
				echo "10";
			}
		}
		disconnect($con);
		die;
	}
	exit();
}
?>