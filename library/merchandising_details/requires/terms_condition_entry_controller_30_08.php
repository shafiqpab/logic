<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$buyer_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');

if ($action=="color_list_view")
{
		$arr=array (2=>$entry_form,3=>$is_deleted);
		echo  create_list_view ( "list_view", "Terms Prefix, Terms,Page,Status", "100,250,150,50","600","220",0, "select    terms,is_default, page_id,id,terms_prefix from lib_terms_condition where page_id>0 order by id asc", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,0,page_id,is_default", $arr , "terms_prefix,terms,page_id,is_default", "../merchandising_details/requires/terms_condition_entry_controller", 'setFilterGrid("list_view",-1,tableFilters);' ) ;
		
		
}
if ($action=="load_php_data_to_form")
{
	
	$nameArray=sql_select( "select terms,is_default, page_id,id,terms_prefix from  lib_terms_condition where id='$data'" );
	foreach ($nameArray as $inf)
	{

		
		echo "document.getElementById('txt_terms_prefix').value  = '".$inf[csf("terms_prefix")]."';\n";
		
		if($inf[csf("is_default")]==1){
			echo "$('#txt_terms_condition').attr('disabled', false);\n";
			echo "$('#txt_terms_condition_more').attr('disabled', true);\n";
			echo "$('#txt_terms_condition_more').val('');\n";
			//echo "document.getElementById('txt_terms_condition').value = '".($inf[csf("terms")])."';\n"; 
			//echo "document.getElementById('txt_terms_condition_hdn').value = '".($inf[csf("terms")])."';\n"; 
			
			echo "document.getElementById('txt_terms_condition').value = '".($inf[csf("terms")])."';\n"; 
			echo "document.getElementById('txt_terms_condition_hdn').value = '".($inf[csf("terms")])."';\n"; 
			
			
			
		}
		else
		{
			echo "$('#txt_terms_condition_more').attr('disabled', false);\n";
			echo "$('#txt_terms_condition').attr('disabled', true);\n";
			echo "$('#txt_terms_condition').val('');\n";
			echo "document.getElementById('txt_terms_condition_more').value = '".($inf[csf("terms")])."';\n"; 
			echo "document.getElementById('txt_terms_condition_hdn').value = '".($inf[csf("terms")])."';\n"; 
		}
		echo "document.getElementById('cbo_status').value  = '".($inf[csf("is_default")]*1)."';\n";
		echo "document.getElementById('update_id').value  = '".($inf[csf("id")])."';\n"; 
		$page_name='';
		$tag_page_arr=explode(",",$inf[csf("page_id")]);
		foreach($tag_page_arr as $val)
		{
			if($page_name=="") $page_name=$entry_form[$val]; else $page_name.=",".$entry_form[$val];
		}
		
		echo "document.getElementById('txt_tag_page').value  = '".$page_name."';\n"; 
		echo "document.getElementById('txt_tag_page_id').value  = '".$inf[csf("page_id")]."';\n"; 
		//echo "set_multiselect('cbo_tag_buyer','0','1','".($inf[csf("tag_buyer")])."','0');\n"; 
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_terms_condition',1);\n";  
	}
}

if ($action=="save_update_delete")
{  

	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$txt_terms_condition=(str_replace("'","",$txt_terms_condition)!='')?$txt_terms_condition:$txt_terms_condition_more;
	
	
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$id=return_next_id( "id", "lib_terms_condition", 1 ) ;
		$field_array="id,terms,page_id,is_default,terms_prefix";
		
		$data_array="";
		$tag_page=explode(',',str_replace("'","",$txt_tag_page_id));
		for($i=0; $i<count($tag_page); $i++)
		{
			if($i==0) $add_comma=""; else $add_comma=",";
			$data_array.="$add_comma(".$id.",".trim($txt_terms_condition).",".$tag_page[$i].",".$cbo_status.",".$txt_terms_prefix.")";
			$id++;
		}
		$rID=sql_insert("lib_terms_condition",$field_array,$data_array,1);

		//----------------------------------------------------------------------------------
	   
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "0**".$rID;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);   
				echo "0**".$rID;
			}
			else{
				oci_rollback($con);
				echo "10**".$rID;
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}		

			$id=return_next_id( "id", "lib_terms_condition", 1 ) ;
			$field_array="id,terms,page_id,is_default,terms_prefix";
			
			$data_array="";
			$tag_page=explode(',',str_replace("'","",$txt_tag_page_id)); 

			$page_id_sql=sql_select("select page_id from lib_terms_condition where page_id>0 and id=$update_id"); 
			
			$page_id_arr=array();
			foreach ($page_id_sql as $key => $page_id) 
			{
				$page_id_arr[]=$page_id[csf('page_id')];
			}

			for($i=0; $i<count($tag_page); $i++)
			{
				if(in_array($tag_page[$i], $page_id_arr))
				{
					$rID1=sql_multirow_update("lib_terms_condition","terms*terms_prefix",$txt_terms_condition."*".$txt_terms_prefix,"id",$update_id,0);
				}
				else
				{
					if($i==0) $add_comma=""; else $add_comma=",";
					$data_array.="$add_comma(".$id.",".trim($txt_terms_condition).",".$tag_page[$i].",".$cbo_status.",".$txt_terms_prefix.")";
					$id++;

				} 
			} 
			if($rID1) $flag=1; else $flag=0;

			$rID=sql_insert("lib_terms_condition",$field_array,$data_array,1);
			if($rID) $flag=1; else $flag=0;
			 
		
		//----------------------------------------------------------------------------------
		
		if($db_type==0)
		{
			if($rID || $rID1)
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
			if($rID || $rID1)
			{
				oci_commit($con);   
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

	else if ($operation==2)   // Delete Here
	{
		
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		// $booking_no_result=return_field_value("booking_no","wo_booking_terms_condition","entry_form=$txt_tag_page_id and terms='$txt_terms_condition_hdn' order by id desc","booking_no");
		$booking_no_result=sql_select("SELECT booking_no as BOOKING_NO from wo_booking_terms_condition where entry_form=$txt_tag_page_id and terms=$txt_terms_condition_hdn");
		if(count($booking_no_result)>0)
		{
			foreach($booking_no_result as $row)
			{
				$booking_no_all.=$row['BOOKING_NO'].', ';
			}
			$booking_no_all=chop($booking_no_all,', ');
			echo "15**Data Found ".$booking_no_all;die;
		}
		else
		{
			$rID=execute_query( "delete from lib_terms_condition where id = $update_id",0);
		}
		
		
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
				else{
					oci_rollback($con);
					echo "10**".$rID;
				}
		   }
		disconnect($con);
		die;
	}		
	
}
if($action=="page_name_popup")
{
  	echo load_html_head_contents("Page Name Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
?>
	<script>
	
		$(document).ready(function(e) {
			setFilterGrid('tbl_list_search',-1);
		});
		
		var selected_id = new Array(); var selected_name = new Array();
		
		function check_all_data() 
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length; 

			tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function set_all()
		{
			var old=document.getElementById('txt_buyer_row_id').value; 
			if(old!="")
			{   
				old=old.split(",");
				for(var k=0; k<old.length; k++)
				{   
					js_set_value( old[k] ) 
				} 
			}
		}
		
		function js_set_value( str ) 
		{
			
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
				selected_name.push( $('#txt_individual' + str).val() );
				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
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
			
			$('#hidden_page_id').val(id);
			$('#hidden_buyer_name').val(name);
		}
    </script>

</head>
<body>
<div align="center">
	<fieldset style="width:370px;margin-left:10px">
    	<input type="hidden" name="hidden_page_id" id="hidden_page_id" class="text_boxes" value="">
        <input type="hidden" name="hidden_buyer_name" id="hidden_buyer_name" class="text_boxes" value="">
        <form name="searchbuyerfrm_1"  id="searchbuyerfrm_1" autocomplete="off">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="350" class="rpt_table" >
                <thead>
                    <th width="50">SL</th>
                    <th width="50">Page ID</th>
                    <th>Page Name</th>
                </thead>
            </table>
            <div style="width:350px; overflow-y:scroll; max-height:280px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="332" class="rpt_table" id="tbl_list_search" >
                <?
					
                    $i=1; $buyer_row_id=""; 
					$hidden_page_id=explode(",",$txt_tag_page_id);
                    foreach($entry_form as $id=>$name)
                    {
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						 
						if(in_array($id,$hidden_page_id)) 
						{ 
							if($buyer_row_id=="") $buyer_row_id=$i; else $buyer_row_id.=",".$i;
						}
						
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)"> 
							<td width="50" align="center"><?php echo $i; ?>
								<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $id; ?>"/>	
								<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $name; ?>"/>
								<input type="hidden" name="txt_mandatory" id="txt_mandatory<?php echo $i ?>" value="<? echo $mandatory; ?>"/>
							</td>	
							<td width="50"><p><? echo $id; ?></p></td>
							<td><p><? echo $name; ?></p></td>
						</tr>
						<?
						$i++;
                    }
                ?>
                    <input type="hidden" name="txt_buyer_row_id" id="txt_buyer_row_id" value="<?php echo $buyer_row_id; ?>"/>
                </table>
            </div>
             <table width="350" cellspacing="0" cellpadding="0" style="border:none" align="center">
                <tr>
                    <td align="center" height="30" valign="bottom">
                        <div style="width:100%"> 
                            <div style="width:50%; float:left" align="left">
                                <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
                            </div>
                            <div style="width:50%; float:left" align="left">
                                <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </form>
    </fieldset>
</div>    
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	set_all();
</script>
</html>
<?
exit();
}

?>