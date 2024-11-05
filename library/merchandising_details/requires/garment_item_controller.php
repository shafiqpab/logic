<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
$user_id=$_SESSION['logic_erp']['user_id'];
include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_product_type")
{
	if($data==1)
	{
		echo create_drop_down( "cbo_product_type", 150, $product_types,'', 1,"--- Select Product Type ---",0,"",0,"3,4");
	}
	else if($data==2)
	{
		echo create_drop_down( "cbo_product_type", 150, $product_types,'', 1,"--- Select Product Type ---",0,"",0,"1,2,5");
	}
	else if($data==6 || $data==9)
	{
		echo create_drop_down( "cbo_product_type", 150, $product_types,'', 1,"--- Select Product Type ---",0,"",0,"9");
	}
	else if($data==7)
	{
		echo create_drop_down( "cbo_product_type", 150, $product_types,'', 1,"--- Select Product Type ---",0,"",0,"7");
	}
	else if($data==8)
	{
		echo create_drop_down( "cbo_product_type", 150, $product_types,'', 1,"--- Select Product Type ---",0,"",0,"8");
	}
	else
	{
		echo create_drop_down( "cbo_product_type", 150, $blank_array,'', 1,"--- Select Product Type ---",0);
	}
	exit();
}
 

 if($action=="notes_popup")
 {
	 echo load_html_head_contents("Operation Templete", "../../../", 1, 1,'','','');
	 extract($_REQUEST);
	 $hdn_notes=trim(str_replace("'","",$hdn_notes));
	 $hdn_notes_arr=explode("__",$hdn_notes);
	 ?>
	 <script>
	 function add_break_down_tr(i)
	 {
		 var row_num=$('#tbl_list_search tbody tr').length;
		 if (row_num!=i)
		 {
			 return false;
		 }
		 else
		 {
			 i++;
			 $("#tbl_list_search tr:last").clone().find("input,select").each(function() {
				 $(this).attr({
					 'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
					 'name': function(_, name) { return name + i },
					 'value': function(_, value) { return value }
					 });
				 }).end().appendTo("#tbl_list_search");
			 //
			 $("#tbl_list_search tr:eq("+i+")").removeAttr('id').attr('id','tr_'+i);
			 $("#tbl_list_search tr:eq("+i+")").find('td:first').html(i);		 
			 $('#increase_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
			 $('#decrease_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+")");
			 $("#tbl_list_search tr:last td:eq(0)").text(i);
			 set_all_onclick();
		 }
	 }
 
	 function fn_deletebreak_down_tr(rowNo)
	 {
		 var numRow = $('table#tbl_list_search tbody tr').length;
		 if(rowNo!=0)
			{
				var index=rowNo-1
				$("#tbl_list_search tbody tr:eq("+index+")").remove();
				var numRow=$('#tbl_list_search tbody tr').length;
				for(i = rowNo;i <= numRow;i++){
					$("#tbl_list_search tr:eq("+i+")").find("input,select").each(function() {
						$(this).attr({
						  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
						  //'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i },
						  'value': function(_, value) { return value }              
						}); 
						
					$('#increase_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
					$('#decrease_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+")");
					$("#tbl_list_search tr:eq("+i+") td:eq(0)").text(i);
					})

				}
			}
		 //if(rowNo!=1)
		 //{
			 //var permission_array=permission.split("_");
			 //var rowid=$('#tr_'+rowNo).val();
			 //var index=rowNo-1
			 //$('#tbl_list_search tbody tr:eq('+index+')').remove();
		 //}
	 }
	 
	 function fn_close()
	 {
		 var numRow = $('table#tbl_list_search tbody tr').length;
		 var data_all=""; var tot_amt=0;
		 for(i = 1; i <= numRow; i++)
		 {
			 var txtNotes=trim($('#txtNotes_'+i).val());
			 if(txtNotes!="")
			 data_all+=txtNotes+"__";
		 }
		 data_all = data_all.substr( 0, data_all.length - 2 );
		 //alert(data_all);return;
		 $('#notes_dtls_data').val(data_all);
		 parent.emailwindow.hide();
	 }
	 </script>
	 </head>
	 <body>
	 <div align="center" style="width:100%;" >
	 <form name="invoiceFreightInfo_1"  id="invoiceFreightInfo_1" autocomplete="off">
		 <table width="590" cellspacing="0" cellpadding="0" class="rpt_table" align="center" border="1" rules="all" id="tbl_list_search">
			 <thead>
				 <tr>
					 <th width="50">SL</th>
					 <th width="450">Operation Templete</th>
					 <th><input type="hidden" id="notes_dtls_data" name="notes_dtls_data" /></th>
				 </tr>
			 </thead>
			 <tbody>
			 <?
			 $i=1;
			 if($hdn_notes!="" && count($hdn_notes_arr)>0)
			 {
				 foreach($hdn_notes_arr as $val)
				 {
					 if ($i%2==0)
						 $bgcolor="#FFFFFF";
					 else
						 $bgcolor="#E9F3FF";
					 ?>
					 <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
						 <td align="center"><? echo $i; ?></td>
						 <td><input type="text" id="txtNotes_<? echo $i;?>" name="txtNotes[]" class="text_boxes" style="width:430px;" value="<? echo $val; ?>"/></td>
						 <td>
						 <input type="button" id="increase_<? echo $i;?>" name="increase_<? echo $i;?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<? echo $i;?>)" />
						 <input type="button" id="decrease_<? echo $i;?>" name="decrease_<? echo $i;?>" style="width:30px" class="formbutton" value="-" onClick="fn_deletebreak_down_tr(<? echo $i;?>);" />
						 </td>
					 </tr>
					 <?
					 $i++;
				 }
			 }
			 else
			 {
				 ?>
				 <tr bgcolor="<? echo $bgcolor; ?>" id="tr_1">
					 <td align="center">1</td>
					 <td><input type="text" id="txtNotes_1" name="txtNotes[]" class="text_boxes" style="width:430px;" value=""/></td>
					 <td>
					 <input type="button" id="increase_1" name="increase_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(1)" />
					 <input type="button" id="decrease_1" name="decrease_1" style="width:30px" class="formbutton" value="-" onClick="fn_deletebreak_down_tr(1);" />
					 </td>
				 </tr>
				 <?
			 }
			 ?>	
			 </tbody>    
		 </table>
		 <p style="text-align:center"><input type="button" value="Close" style="width:100px" id="btb_close" name="btn_close" onClick="fn_close()" class="formbuttonplasminus" /></p>
	 </form>
	 </div>
	 </body>
	 <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	 </html>
	 <?
	 exit();
 }

if($action=="show_list_view_item")
{
	$arr=array (3=>$product_category,4=>$product_types,7=>$row_status);
	echo  create_list_view ( "list_view", "Item Name,Gmts Prod Code,Commercial Name,Product Category,Product Type,SMV,Efficiency,Status,HS Code","200,100,130,90,80,70,70,90,100","970","220",0, "select id,item_name,product_code,commercial_name,product_category_id,product_type_id,status_active,standard_smv,efficiency,hs_code from lib_garment_item where is_deleted=0 ", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,0,0,product_category_id,product_type_id,0,0,status_active,0", $arr , "item_name,product_code,commercial_name,product_category_id,product_type_id,standard_smv,efficiency,status_active,hs_code", "../merchandising_details/requires/garment_item_controller", 'setFilterGrid("list_view",-1);') ; 
	exit();             
}

if($action=="load_php_data_to_form")
{
	$dbData=sql_select("select id, item_name, commercial_name, product_category_id, product_type_id, status_active, standard_smv, efficiency, operation_bulletin, product_nature, is_default, hs_code,product_code from lib_garment_item where id='$data' ");
	// print_r($dbData);die;
	foreach ($dbData as $inf) 
	{
		$productnature="";
		$exprodNature=explode(",",$inf[csf("product_nature")]);
		foreach($exprodNature as $pnid)
		{
			if($productnature=="") $productnature=$item_category[$pnid]; else $productnature.=','.$item_category[$pnid];
		}
		
		echo "document.getElementById('hidd_product_nature_id').value = '".($inf[csf("product_nature")])."';\n"; 
		echo "document.getElementById('txt_product_nature').value = '".$productnature."';\n";  
		echo "document.getElementById('txt_item_name').value = '".($inf[csf("item_name")])."';\n";    	  
		echo "document.getElementById('txt_commercial_name').value = '".($inf[csf("commercial_name")])."';\n";
		echo "document.getElementById('txt_product_code').value = '".($inf[csf("product_code")])."';\n";
		echo "document.getElementById('cbo_product_category').value = '0';\n";     	
		echo "document.getElementById('cbo_product_category').value = '".($inf[csf("product_category_id")])."';\n"; 
		echo "load_drop_down('requires/garment_item_controller', '".($inf[csf("product_category_id")])."', 'load_drop_down_product_type', 'product_type' );\n";   
		echo "document.getElementById('cbo_product_type').value = '".($inf[csf("product_type_id")])."';\n";    
		echo "document.getElementById('txt_standard_smv').value = '".($inf[csf("standard_smv")])."';\n";    
		echo "document.getElementById('txt_efficiency').value = '".($inf[csf("efficiency")])."';\n";    
		echo "document.getElementById('cbo_status').value  = '".($inf[csf("status_active")])."';\n";
		echo "document.getElementById('update_id').value  = '".($inf[csf("id")])."';\n"; 
		echo "document.getElementById('txt_hs_code').value  = '".($inf[csf("hs_code")])."';\n"; 
		echo "document.getElementById('txt_notes').value = '".$inf[csf("operation_bulletin")]."';\n";
		echo "document.getElementById('hdn_notes').value = '".$inf[csf("operation_bulletin")]."';\n";
		echo "document.getElementById('default_value_id').value  = '".($inf[csf("is_default")])."';\n"; 
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_garment_item',1);\n"; 
	}
	exit();
}

if($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 

	$txt_hs_code=str_replace("'", "", $txt_hs_code);
	if($operation==0)
	{
		$con=connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$id=return_next_id("id","lib_garment_item",1);
		$id_lib_garment_item_tag_nature=return_next_id("id","lib_garment_item_tag_nature",1);
		$field_array="id, item_name, hs_code, commercial_name, product_category_id, product_type_id, standard_smv, efficiency, operation_bulletin, product_nature, product_code, inserted_by, insert_date, status_active, is_deleted";
		$data_array="(".$id.",".$txt_item_name.",'".$txt_hs_code."',".$txt_commercial_name.",".$cbo_product_category.",".$cbo_product_type.",".$txt_standard_smv.",".$txt_efficiency.",".$txt_notes.",".$hidd_product_nature_id.",".$txt_product_code.",'".$user_id."','".$pc_date_time."',".$cbo_status.",0)"; 
		
		$field_array1="id, garment_item_id, product_nature";
		$data_array1=""; $add_comma="";
		$product_nature_id=explode(',',str_replace("'","",$hidd_product_nature_id));
		for($i=0; $i<count($product_nature_id); $i++)
		{
			if($i==0) $add_comma=""; else $add_comma=",";
			$data_array1.="$add_comma(".$id_lib_garment_item_tag_nature.",".$id.",".$product_nature_id[$i].")";
			$id_lib_garment_item_tag_nature=$id_lib_garment_item_tag_nature+1;
		}
		
		$flag=1;
		
		$rID=sql_insert("lib_garment_item",$field_array,$data_array,1);
		if($rID==1) $flag=1; else $flag=0;
		
		$rID1=sql_insert("lib_garment_item_tag_nature",$field_array1,$data_array1,1);
		if($rID1==1 && $flag==1) $flag=1; else $flag=0;
		
		//echo "10**".$rID.'='.$rID1.'='.$flag; oci_rollback($con); disconnect($con); die;

		if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);  
				echo "0**".$rID;
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
	else if($operation==1) // update here ------------------------
	{
		$con=connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$field_array="item_name*hs_code*commercial_name*product_category_id*product_type_id*standard_smv*efficiency*operation_bulletin*product_nature*product_code*updated_by*update_date*status_active";
		$data_array="".$txt_item_name."*'".$txt_hs_code."'*".$txt_commercial_name."*".$cbo_product_category."*".$cbo_product_type."*".$txt_standard_smv."*".$txt_efficiency."*".$txt_notes."*".$hidd_product_nature_id."*".$txt_product_code."*'".$user_id."'*'".$pc_date_time."'*".$cbo_status."";
		$field_array_default="hs_code*commercial_name*product_category_id*product_type_id*standard_smv*efficiency*operation_bulletin*product_nature*updated_by*update_date*status_active";
		$data_array_default="'".$txt_hs_code."'*".$txt_commercial_name."*".$cbo_product_category."*".$cbo_product_type."*".$txt_standard_smv."*".$txt_efficiency."*".$txt_notes."*".$hidd_product_nature_id."*".$txt_product_code."*'".$user_id."'*'".$pc_date_time."'*".$cbo_status."";
		//echo $data_array;
		$id_lib_garment_item_tag_nature=return_next_id("id","lib_garment_item_tag_nature",1);
		$field_array1="id, garment_item_id, product_nature";
		$data_array1=""; $add_comma="";
		$product_nature_id=explode(',',str_replace("'","",$hidd_product_nature_id));
		for($i=0; $i<count($product_nature_id); $i++)
		{
			if($i==0) $add_comma=""; else $add_comma=",";
			$data_array1.="$add_comma(".$id_lib_garment_item_tag_nature.",".$update_id.",".$product_nature_id[$i].")";
			$id_lib_garment_item_tag_nature=$id_lib_garment_item_tag_nature+1;
		}
		 
		$rID=0; $flag=1;
		$value=str_replace("'","",$default_value_id);
		if($value=='0')
		{
			$rID=sql_update("lib_garment_item",$field_array,$data_array,"id","".$update_id."",0);
			if($rID==1) $flag=1; else $flag=0;
		}
		else if($value=='1')
		{
			$rID=sql_update("lib_garment_item",$field_array_default,$data_array_default,"id","".$update_id."",0);
			if($rID==1) $flag=1; else $flag=0;
		}
		
		$rID1=execute_query("delete from lib_garment_item_tag_nature where  garment_item_id = $update_id",0);
		if($rID1==1 && $flag==1) $flag=1; else $flag=0;
		
		$rID2=sql_insert("lib_garment_item_tag_nature",$field_array1,$data_array1,1);
		if($rID2==1 && $flag==1) $flag=1; else $flag=0;
		
		//echo "10**".$rID.'='.$rID1.'='.$rID2.'='.$flag; oci_rollback($con); disconnect($con); die;
 
	  	if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);   
				echo "1**".$rID;
			}
			else{
				oci_rollback($con);
				echo "10**".$rID;
			}
		}
		disconnect($con);
		die;
    }
	else if($operation==2)	//Delete here----------------------------------------------Delete here--------------
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$gmts_item_id=return_field_value("gmts_item_id as gmts_item_id", "wo_po_details_mas_set_details", "gmts_item_id=$update_id","gmts_item_id");
		$gmts_item=return_field_value("gmts_item", "wo_quotation_inquery", "gmts_item=$update_id","gmts_item");
		if($gmts_item_id!=0 || $gmts_item_id!='' || $gmts_item!=0 || $gmts_item!='')
		{
			echo "50**Some Entries Found For This Item, Deleting Not Allowed.";	
			disconnect($con);
			die;
		}
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="'".$user_id."'*'".$pc_date_time."'*0*1";
		$rID=sql_delete("lib_garment_item",$field_array,$data_array,"id","".$update_id."",1);
		
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

if($action=="product_nature_popup")
{
	echo load_html_head_contents("Product Nature Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $product_nature_id;
	?>
	<script>
		var selected_id = new Array();
		var selected_name = new Array();
		
		$(document).ready(function(e) {
			setFilterGrid('tbl_list_search',-1);
		});

		function toggle( x, origColor) {
            var newColor = 'yellow';
            if ( x.style ) {
                x.style.backgroundColor = (newColor == x.style.backgroundColor)? origColor : newColor;
            }
        }

		function check_all_data()
		{
            var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length-1;
            tbl_row_count = tbl_row_count;
			//alert(tbl_row_count);

            if(document.getElementById('check_all').checked)
			{
                for( var i = 1; i <= tbl_row_count; i++ ) {
	                document.getElementById( 'search' + i ).style.backgroundColor = 'yellow';
	                if( jQuery.inArray( $('#txt_individual_id' + i).val(), selected_id ) == -1 ) {
						selected_id.push( $('#txt_individual_id' + i).val() );
						selected_name.push( $('#txt_individual' + i).val() );
					}
                }

                var id = ''; var name = '';
				for( var i = 0; i < selected_id.length; i++ ) {
					id += selected_id[i] + ',';
					name += selected_name[i] + ',';
				}
				id = id.substr( 0, id.length - 1 );
				name = name.substr( 0, name.length - 1 );
				$('#hidd_product_nature_id').val(id);
				$('#txt_product_nature').val(name);
            }
			else
			{				
                for( var i = 1; i <= tbl_row_count; i++ ) {
                    if(i%2==0 ) document.getElementById('search'+i).style.backgroundColor = '#FFFFFF';
                    else document.getElementById('search'+i).style.backgroundColor = '#E9F3FF';

					for( var j = 0; j < selected_id.length; j++ ) {
                        if( selected_id[j] == $('#txt_individual_id' + i).val() ) break;
                    }
                    selected_id.splice( j,1 );
                }

				var id = ''; var name = '';
				for( var i = 0; i < selected_id.length; i++ ) {
					id += selected_id[i] + ',';
					name += selected_name[i] + ',';
				}
				id = id.substr( 0, id.length - 1 );
				name = name.substr( 0, name.length - 1 );
				$('#hidd_product_nature_id').val(id);
				$('#txt_product_nature').val(name);
            }
        }

		function js_set_value( str) 
		{
        	var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
            tbl_row_count = tbl_row_count-1;
            if ($("#search"+str).css("display") !='none')
			{
                if ( str%2==0 ) toggle( document.getElementById( 'search' + str ), '#FFFFFF');
				else toggle( document.getElementById( 'search' + str ), '#E9F3FF');

				if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
					selected_id.push( $('#txt_individual_id' + str).val() );
					selected_name.push( $('#txt_individual' + str).val() );
				}
				else
				{
					for( var i = 0; i < selected_id.length; i++ ) {
						if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
					}
					selected_id.splice( i, 1 );
					selected_name.splice( i, 1 );
				}
            }

			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			$('#hidd_product_nature_id').val(id);
			$('#txt_product_nature').val(name);

            if (selected_id.length == tbl_row_count) document.getElementById("check_all").checked = true;
			else document.getElementById("check_all").checked = false;
        }

		function set_all()
        {
            var old=document.getElementById('txt_product_nature_row_id').value; 
            if(old!="")
            {   
                old=old.split(",");
                for(var k=0; k<old.length; k++)
                {   
                    js_set_value( old[k] )
                } 
            }
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			if (old.length == tbl_row_count) document.getElementById("check_all").checked = true;
			else document.getElementById("check_all").checked = false;
        }
    </script>
	</head>
	<body>
		<div align="center">
			<fieldset style="width:300px;margin-left:10px">
		    	<input type="hidden" name="hidd_product_nature_id" id="hidd_product_nature_id" class="text_boxes" value="">
		        <input type="hidden" name="txt_product_nature" id="txt_product_nature" class="text_boxes" value="">
		        <form name="searchprocessfrm_1"  id="searchprocessfrm_1" autocomplete="off">
		            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="290" class="rpt_table" >
		                <thead>
		                    <th width="50">SL</th>
		                    <th>Product Nature</th>
		                </thead>
		            </table>
		            <div style="width:290px; overflow-y:scroll; max-height:180px;" id="buyer_list_view" align="center">
		                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="270" class="rpt_table" id="tbl_list_search" >
		                <?
		                    $i=1; $product_nature_row_id=''; 
							$nature_id_print_arr=array(2,3,100);
							$product_nature_id=explode(",",$product_nature_id);
		                    foreach($item_category as $id=>$name)
		                    {
								if(in_array($id,$nature_id_print_arr))
								{
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									if(in_array($id,$product_nature_id)) 
									{ 
										if($product_nature_row_id=="") $product_nature_row_id=$i; else $product_nature_row_id.=",".$i;
									}
									?>
									<tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?=$i;?>" onClick="js_set_value(<?=$i; ?>);"> 
										<td width="50" align="center"><?=$i; ?>
											<input type="hidden" name="txt_individual_id" id="txt_individual_id<?=$i; ?>" value="<?=$id; ?>"/>	
											<input type="hidden" name="txt_individual" id="txt_individual<?=$i; ?>" value="<?=$name; ?>"/>
										</td>	
										<td style="word-break:break-all"><?=$name; ?></td>
									</tr>
									<?
									$i++;
								}
		                    }
		                ?>
		                <input type="hidden" name="txt_product_nature_row_id" id="txt_product_nature_row_id" value="<? echo $product_nature_row_id; ?>"/>
		                </table>
		            </div>
		             <table width="290" cellspacing="0" cellpadding="0" style="border:none" align="center">
		                <tr>
		                    <td align="center" height="30" valign="bottom">
		                        <div style="width:100%"> 
		                            <div style="width:50%; float:left" align="left">
		                                <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data();" /> Check / Uncheck All
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
	<script>set_all();</script>
	</html>
	<?
	exit();
}
?>