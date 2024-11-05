<?
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id = $_SESSION['logic_erp']["user_id"];
$permission=$_SESSION['page_permission'];


if($action=="terms_condition_popup")
{
	echo load_html_head_contents("Terms Condition","../../", 1, 1, $unicode);
	extract($_REQUEST);
	//echo $sys_id.'='.$page_id.'='.$fso_id;
	if($sys_id!='')
	{
		$is_approved=return_field_value( "is_approved", "wo_booking_mst","booking_no='".$sys_id."'","is_approved");
	}
	?>
	<script>
	var sys_id='<? echo $sys_id;?>';
	var page_id='<? echo $page_id;?>';
	var fso_id='<? echo $fso_id;?>';
	//alert(fso_id);
	var is_approved='<? echo $is_approved;?>';
	function add_break_down_tr(i) 
	{
		if(is_approved==1)
			{
				alert('This booking is already approved');
				return;
			}
		var row_num=$('#tbl_termcondi_details tr').length-1;
		if (row_num!=i)
		{
			return false;
		}
		else
		{
			i++;
		 
			 $("#tbl_termcondi_details tr:last").clone().find("input,select").each(function() {
				$(this).attr({
				  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
				  //'name': function(_, name) {  var name=name.split("_"); return name[0] +"_"+ i },
				  'value': function(_, value) { return value }              
				});  
			  }).end().appendTo("#tbl_termcondi_details");
			$('#increase_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
			$('#decrease_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+")");
			$('#termscondition_'+i).val("");
			$("#tbl_termcondi_details tr:last td:eq(0)").text(i);
		}		  
	}

	function fn_deletebreak_down_tr(rowNo) 
	{
			if(is_approved==1)
			{
				alert('This booking is already approved');
				return;
			}
			if(rowNo!=0)
			{
				var index=rowNo-1
				$("#tbl_termcondi_details tbody tr:eq("+index+")").remove();
				var numRow=$('#tbl_termcondi_details tbody tr').length;
				for(i = rowNo;i <= numRow;i++){
					$("#tbl_termcondi_details tr:eq("+i+")").find("input,select").each(function() {
						$(this).attr({
						  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
						  //'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i },
						  'value': function(_, value) { return value }              
						}); 
						
					$('#increase_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
					$('#decrease_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+")");
					$("#tbl_termcondi_details tr:eq("+i+") td:eq(0)").text(i);
					})

				}
			}		
	}

	function fnc_fabric_booking_terms_condition( operation )
	{
		    var row_num=$('#tbl_termcondi_details tr').length-1;
		    if (row_num==0) 
		    {
		    	alert('Please Select At Least One Term & Condition');
		    	return;
		    }
			var data_all="";
			for (var i=1; i<=row_num; i++)
			{
				
				if (form_validation('termscondition_'+i,'Term Condition')==false)
				{
					return;
				}
				
				data_all=data_all+get_submitted_data_string('txt_booking_no*txt_entry_form*termscondition_'+i,"../../",i);
			}
			var data="action=save_update_delete&operation="+operation+'&total_row='+row_num+data_all;
			
			freeze_window(operation);
			http.open("POST","terms_condition_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_fabric_booking_terms_condition_reponse;
	}

	function fnc_fabric_booking_terms_condition_reponse()
	{
		
		if(http.readyState == 4) 
		{
		    var reponse=trim(http.responseText).split('**');
				if (reponse[0].length>2) reponse[0]=10;
				release_freezing();
				if(reponse[0]==0 || reponse[0]==1)
				{
					parent.emailwindow.hide();
				}
		}
	}

	function open_extra_terms_popup(page_link,title)
	{
	    page_link=page_link+'&txt_booking_no='+sys_id+'&page_id='+page_id;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=400px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("terms_breck_down");
			if (theemail.value!="")
			{
				var counter=$('#tbl_termcondi_details tr').length-1;
				var data=JSON.parse(theemail.value);
				for(var i=0;i<data.length;i++)
				{
					//alert(data[i])
					counter++;
					$('#tbl_termcondi_details tbody').append(
					'<tr id="settr_1" align="center">'
					+ '<td>'+counter+'</td><td><input type="text" name="termscondition_'+counter+'" class="text_boxes" id="termscondition_'+counter+'"  style="width:95%;" value="'+data[i]+'"/></td><td><input type="button" class="formbutton" id="increase_'+counter+'"  style="width:30px;" value="+" onClick="add_break_down_tr('+counter+')"/><input type="button" class="formbutton" id="decrease_'+counter+'"  style="width:30px;" value="-" onClick="javascript:fn_deletebreak_down_tr('+counter+')"/></td>'+ '</tr>');
				}
			}
		}
	}
    
    function remarks_from_fso(isChecked,fso_id) 
    {
    	//alert(fso_id);
		if(isChecked) 
		{
			var counter=$('#tbl_termcondi_details tr').length-1;
			//alert(counter);
			var data="action=add_fso_remarks&operation="+isChecked+"&fso_id="+fso_id+"&counter="+counter;
		
			freeze_window(operation);
			http.open("POST","terms_condition_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = remarks_from_fso_reponse;
		}
	}
	function remarks_from_fso_reponse()
	{		
		if(http.readyState == 4) 
		{
		    var reponse=trim(http.responseText);
		    // alert(reponse);
		    
			$('#tbl_termcondi_details tbody').append(reponse);
			release_freezing();
		}
	}
   
    </script>

	</head>

	<body>
	<div align="center" style="width:100%;" >
	 	<? echo load_freeze_divs ("../../",$permission);  ?>
		<fieldset>
	        <form id="termscondi_1" autocomplete="off">
	           <input type="hidden" id="txt_booking_no" name="txt_booking_no" value="<? echo str_replace("'","",$sys_id) ?>"/>
	           <input type="hidden" id="txt_entry_form" name="txt_entry_form" value="<? echo str_replace("'","",$page_id) ?>"/>
	        
	        	<table width="650" cellspacing="0" class="rpt_table" border="0" id="tbl_termcondi_details" rules="all">
	            	<thead>
	                	<tr>
	                    	<th width="50">Sl</th>
	                    	<th width="530">Terms</th>
	                    	<th></th>
	                    </tr>
	                </thead>
	                <tbody>
	                    <?
						//echo  $is_approved.'ff';
						if($is_approved==1) $readonly_check="readonly=readonly"; else $readonly_check="";
						//echo $readonly_check;
						//$data_array=sql_select("select id, terms from  wo_booking_terms_condition where booking_no='".str_replace("'","",$sys_id)."' and entry_form > 0 order by id");
						$data_array=sql_select("select id, terms from  wo_booking_terms_condition where booking_no='".str_replace("'","",$sys_id)."' and entry_form = $page_id order by id");
						if ( count($data_array)>0)
						{
							$is_update=1;
							$i=0;
							foreach( $data_array as $row )
							{
								$i++;
								?>
	                            	<tr id="settr_1" align="center">
	                                    <td>
	                                    <? echo $i;?>
	                                    </td>
	                                    <td>
	                                    <input type="text" id="termscondition_<? echo $i;?>"   name="termscondition_<? echo $i;?>" style="width:95%"  class="text_boxes"  value="<? echo $row[csf('terms')]; ?>" <? echo $readonly_check;?>   /> 
	                                    </td>
	                                    <td> 
	                                    <input type="button" id="increase_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<? echo $i; ?> );" <? echo $readonly_check;?> />
	                                    <input type="button" id="decrease_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?>);"  />
	                                    </td>
	                                </tr>
	                            <?
							}
						}
						else
						{
							$is_update=0;
							$data_array=sql_select("select id, terms from  lib_terms_condition where is_default=1 and page_id=$page_id order by id asc");// quotation_id='$data'
							foreach( $data_array as $row )
							{
								$i++;
								?>
	                    		<tr id="settr_1" align="center">
	                                <td> <? echo $i;?> </td>
	                                <td><input type="text" id="termscondition_<? echo $i;?>"   name="termscondition_<? echo $i;?>" style="width:95%"  class="text_boxes"  value="<? echo $row[csf('terms')]; ?>" <? echo $readonly_check;?>/></td>
	                                <td><input type="button" id="increase_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<? echo $i; ?> );" <? echo $readonly_check;?> />
	                                <input type="button" id="decrease_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?> );"/></td>
	                            </tr>
	                    		<? 
							}
						} 
						?>
	            	</tbody>
	            </table>
	            
	            <table width="650" cellspacing="0" class="" border="0">
	            	<tr>
	                    <td align="center" height="15" width="50%"> 
	                    <input type="button" id="set_button" class="image_uploader" style="width:100px;" value="Add More.." onClick="open_extra_terms_popup('terms_condition_controller.php?action=extra_terms_popup','Terms Condition')" /> 
	                    </td> 
						<td align="center" height="15" width="50%">  
				           <?
				           if ($page_id==64) 
				           	{ 
				           	?>  	
				           <input type="hidden" id="txt_fso_id" name="txt_fso_id" value="<? echo str_replace("'","",$fso_id) ?>"/>
				           	Remarks From FSO <input type="checkbox" name="checkbox" onClick="remarks_from_fso(this.checked,fso_id);"> Checkbox
				            <?
				       		}
				           ?>
				        </td>
	                </tr>
	            </table>
	            <table width="650" cellspacing="0" class="" border="0">

	            	<tr>
	                    <td align="center" width="100%" class="button_container">
						    <?
								echo load_submit_buttons( $permission, "fnc_fabric_booking_terms_condition", $is_update,0 ,"reset_form('termscondi_1','','','','')",1) ; 
							?>
	                    </td> 
	                </tr>
	            </table>
	        </form>
	    </fieldset>
	</div>
	</body>           
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}


if($action=="add_fso_remarks")
{	
	extract($_REQUEST);
	
	$sql="SELECT b.pre_cost_remarks
	FROM fabric_sales_order_mst a, fabric_sales_order_dtls b 
	WHERE a.id=b.mst_id and a.id=$fso_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
	GROUP BY b.pre_cost_remarks";
	$sql_result=sql_select($sql);
	foreach ($sql_result as $key => $row) 
	{
		$counter++;
		$fsoRemarks=$row[csf('pre_cost_remarks')];
		?>
			<tr id="settr_1" align="center">
				<td><? echo $counter;?></td>
				<td><input type="text" name="termscondition_<? echo $counter;?>" class="text_boxes" id="termscondition_<? echo $counter;?>"  style="width:95%;" value="<? echo $fsoRemarks;?>"/>
				</td>
				<td><input type="button" class="formbutton" id="increase_<? echo $counter;?>"  style="width:30px;" value="+" onClick="add_break_down_tr('<? echo $counter;?>')"/><input type="button" class="formbutton" id="decrease_<? echo $counter;?>"  style="width:30px;" value="-" onClick="javascript:fn_deletebreak_down_tr('<? echo $counter;?>')"/>
				</td>
			</tr>
		
		<?
		
	}
}

if($action=="extra_terms_popup")
{
	echo load_html_head_contents("Terms Condition","../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
	function toggle( x, origColor ) 
	{
		var newColor = 'yellow';
		if ( x.style ) {
			x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
		}
	}
	var selected_id = new Array(); 
	var selected_item=new Array();
	function js_set_value(counter,id,terms)
	{
		toggle( document.getElementById( 'search' + counter ), '#FFFFCC' );
		
		if( jQuery.inArray( id, selected_id ) == -1 ) 
		{
			
			selected_id.push( id );
			selected_item.push(terms);
		}
		else
		{
			for( var i = 0; i < selected_id.length; i++ ) {
				if( selected_id[i] == id ) break;
			}
			selected_id.splice( i, 1 );
			selected_item.splice( i,1 );
		}
		
		var ids = '';
		var termCon='';
		for( var i = 0; i < selected_id.length; i++ ) {
			ids += selected_id[i] + ',';
			termCon+=selected_item[i]+ ',';
		}
		ids = ids.substr( 0, ids.length - 1 );
		termCon = termCon.substr( 0, termCon.length - 1 );
		$('#terms_breck_down').val( JSON.stringify(selected_item) );
		$('#txt_pre_cost_dtls_id').val( ids );
	}
    </script>

	</head>

	<body>
	<div align="center" style="width:100%;" >
	 <? echo load_freeze_divs ("../../",$permission);  ?>
	<fieldset>
	    <form autocomplete="off">
	    <input style="width:60px;" type="hidden" class="text_boxes"  name="terms_breck_down" id="terms_breck_down" /> 
	    <input style="width:60px;" type="hidden" class="text_boxes"  name="txt_pre_cost_dtls_id" id="txt_pre_cost_dtls_id" /> 
	    <table width="400" class="rpt_table" border="1" rules="all">
	    <thead>
	    <th width="40">
	    SL
	    </th>
	    <th>
	    Terms
	    </th>
	    </thead>
	    <tbody>
		<?  $i=1;
			$data_array=sql_select("select id, terms from  lib_terms_condition where is_default=0 and page_id=$page_id");
			foreach( $data_array as $row )
			{
				 if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			
				?>
		       <tr style="text-decoration:none; cursor:pointer" bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i ;?>,'<? echo $row[csf('id')];?>','<? echo $row[csf('terms')];?>')">
		        <td width="40">
		       <? echo $i;?>
		        </td>
		        <td><? echo $row[csf('terms')]; ?></td>
		        </tr>
		        <?
				$i++;
			}
			?>
	        </tbody>
	        </table> 
	        <table width="400" class="rpt_table" border="1" rules="all">
	        <tr>
	       <td align="center"  class="button_container" colspan="2">
		    <input type="button" class="formbutton" value="Close" onClick="parent.emailwindow.hide()"/> 
	         </td> 
	        </tr>
	   </table>   
	    </form>
	</fieldset>
	</div>
	</body>           
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}


if($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0 || $operation==1)  // Insert/Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}		
		 $id=return_next_id( "id", "wo_booking_terms_condition", 1 ) ;
		 $field_array="id,booking_no,entry_form,terms";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $termscondition="termscondition_".$i;
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$txt_booking_no.",".$txt_entry_form.",".$$termscondition.")";
			$id=$id+1;
		 }
		 //echo  $data_array;
		$rID_de3=execute_query( "delete from wo_booking_terms_condition where entry_form=".$txt_entry_form." and  booking_no =".$txt_booking_no."",0);

		$rID=sql_insert("wo_booking_terms_condition",$field_array,$data_array,1);
		//$rID=true;
		check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "0**".$new_booking_no[0];
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$new_booking_no[0];
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "0**".$new_booking_no[0];
			}
			else{
				oci_rollback($con);
				echo "10**".$new_booking_no[0];
			}
		}
		disconnect($con);
		die;
	}	
}

?>