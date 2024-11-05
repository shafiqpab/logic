<?
/*-------------------------------------------- Comments
Purpose			: 	This form created for Item Transfer Report All Category
				
Functionality	:	
JS Functions	:
Created by		:	Nayem
Creation date 	: 	29/06/2021
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Item Transfer Report All Category","../../", 1, 1, $unicode,1,1); 

?>	
<script>
    var permission='<? echo $permission; ?>';
    if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

        
    function reset_field()
    {
        reset_form('item_transfer_report_all_category_1','report_container2','','','','');
    }


    function  generate_report(rptType)
    {
        var cbo_transfer_type = $("#cbo_transfer_type").val();
        var cbo_company_from = $("#cbo_company_from").val();
        var cbo_company_to = $("#cbo_company_to").val();
        var cbo_store_from = $("#cbo_store_from").val();
        var cbo_store_to = $("#cbo_store_to").val();
        var txt_from_order_id = $("#txt_from_order_id").val();
        var txt_to_order_id = $("#txt_to_order_id").val();
        var cbo_item_cat = $("#cbo_item_cat").val();
        var txt_date_from = $("#txt_date_from").val();
        var txt_date_to = $("#txt_date_to").val();

        if( form_validation('cbo_transfer_type*cbo_item_cat*txt_date_from*txt_date_to','Transfer Criteria*Item Cetagory*Transfer Date*Transfer Date')==false )
        {
            return;
        }
        if(cbo_transfer_type==1)
        {    
            if( cbo_company_from==0 && cbo_company_to==0)
            {
                alert("Select From Company Or To Company")
                return;
            }
            else
            {
                var dataString = "&cbo_transfer_type="+cbo_transfer_type+"&cbo_company_from="+cbo_company_from+"&cbo_company_to="+cbo_company_to+"&cbo_item_cat="+cbo_item_cat+"&txt_date_from="+txt_date_from+"&txt_date_to="+txt_date_to;
            }
        }
        if(cbo_transfer_type==2)
        {    
            if( cbo_store_from==0 && cbo_store_to==0)
            {
                alert("Select From Store Or To Store")
                return;
            }
            else
            {
                var dataString = "&cbo_transfer_type="+cbo_transfer_type+"&cbo_store_from="+cbo_store_from+"&cbo_store_to="+cbo_store_to+"&cbo_item_cat="+cbo_item_cat+"&txt_date_from="+txt_date_from+"&txt_date_to="+txt_date_to;
            }
        }
        if(cbo_transfer_type == 4)
        {    
            // if( txt_from_order_id=='' && txt_to_order_id=='')
            // {
            //     alert("Select From Order Or To Order")
            //     return;
            // }
            // else
            // {
                var dataString = "&cbo_transfer_type="+cbo_transfer_type+"&txt_from_order_id="+txt_from_order_id+"&txt_to_order_id="+txt_to_order_id+"&cbo_item_cat="+cbo_item_cat+"&txt_date_from="+txt_date_from+"&txt_date_to="+txt_date_to;
            // }
        }

        var data="action=generate_report"+dataString;
        // alert(data);return;
        freeze_window(5);
        http.open("POST","requires/item_transfer_report_all_category_controller.php",true);
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = generate_report_reponse; 
    }

    function generate_report_reponse()
    {	
        if(http.readyState == 4) 
        {
            //alert(http.responseText);	 
            var reponse=trim(http.responseText).split("**");
            //alert(reponse[2]);
            $("#report_container2").html(reponse[0]);
            document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
            
            
            var tableFilters = 
                {
                col_30: "none",
                col_operation: {
                id: ["grand_total_qnty","grand_total_value"],
                col: [16,18],
                operation: ["sum","sum"],
                write_method: ["innerHTML","innerHTML"]
                }
                }
            var tableFilters1 = 
                {
                col_30: "none",
                col_operation: {
                id: ["grand_total_qnty","grand_total_value"],
                col: [13,15],
                operation: ["sum","sum"],
                write_method: ["innerHTML","innerHTML"]
                }
                }
            var tableFilters2 = 
                {
                col_30: "none",
                col_operation: {
                id: ["grand_total_qnty","grand_total_value"],
                col: [14,16],
                operation: ["sum","sum"],
                write_method: ["innerHTML","innerHTML"]
                }
                }
            var tableFilters3 = 
                {
                col_30: "none",
                col_operation: {
                id: ["grand_total_qnty","grand_total_value"],
                col: [14,16],
                operation: ["sum","sum"],
                write_method: ["innerHTML","innerHTML"]
                }
                }
            var tableFilters4 = 
                {
                col_30: "none",
                col_operation: {
                id: ["grand_total_qnty","grand_total_value"],
                col: [12,14],
                operation: ["sum","sum"],
                write_method: ["innerHTML","innerHTML"]
                }
                }
            
            if(reponse[2]==1){setFilterGrid("table_body",-1,tableFilters);}
            if(reponse[2]==2){setFilterGrid("table_body",-1,tableFilters1);}
            if(reponse[2]==3){setFilterGrid("table_body",-1,tableFilters2);}
            if(reponse[2]==4){setFilterGrid("table_body",-1,tableFilters3);}
            if(reponse[2]==5 || reponse[2]==5){setFilterGrid("table_body",-1,tableFilters4);}
            
            
            release_freezing();
            show_msg('3');
        }
    } 

    function new_window()
    {
            
        document.getElementById('scroll_body').style.overflow="auto";
        document.getElementById('scroll_body').style.maxHeight="none";
        $('#table_body tr:first').hide(); 
        $('.hide_td').hide();  
            $('#tbl_headers tr:first').show();
            $('.hide_td_header').show();
        var w = window.open("Surprise", "#");
        var d = w.document.open();
        d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
        '<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
        d.close(); 
        document.getElementById('scroll_body').style.overflow="auto"; 
        document.getElementById('scroll_body').style.maxHeight="360px";
        $('#table_body tr:first').show();
        $('.hide_td').show();
            $('#tbl_headers tr:first').hide();
            $('.hide_td_header').hide(); 
        
    }

    function fnc_change_caption(type)
    {
        if(type==1)
        {
            $("#from_caption_td").text("From Company");
            $("#to_caption_td").text("To Company");
        }
        if(type==2)
        {
            $("#from_caption_td").text("From Store");
            $("#to_caption_td").text("To Store");
        }
        if(type==4)
        {
            $("#from_caption_td").text("From Order");
            $("#to_caption_td").text("To Order");
        }
        
    }

    function openmypage_orderNo(type)
    {
        var cbo_company_id = $('#cbo_company_name').val();

        if (form_validation('cbo_company_name','Company')==false)
        {
            return;
        }
        
        var title = 'Order Info';	
        var page_link = 'requires/item_transfer_report_all_category_controller.php?cbo_company_id='+cbo_company_id+'&type='+type+'&action=order_popup';

        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=370px,center=1,resize=1,scrolling=0','../');
        
        emailwindow.onclose=function()
        {
            var theform=this.contentDoc.forms[0];
            var order_id=this.contentDoc.getElementById("order_id").value;
            var order_no=this.contentDoc.getElementById("order_no").value;
            //alert(order_id);
            //var po_order=order_id.split("_");

            if(type=='from')
            {
                $("#txt_from_order_id").val(order_id);	
                $("#txt_from_order_no").val(order_no);	
            }
            if(type=='to')
            {
                $("#txt_to_order_id").val(order_id);	
                $("#txt_to_order_no").val(order_no);	
            }
        }
    }

    function set_item_transfer_report(company_id,update_id){

        var show_val_column = "0";
    	var r = confirm("Press \"OK\" to Hide Rate and Amount.\nPress \"Cancel\" to Show Rate and Amount.");
    	if (r == true) show_val_column = "1";

		var report_title=$( "div.form_caption" ).html();
		print_report( company_id+'*'+update_id+'*'+report_title+'*'+show_val_column, "yarn_transfer_print", "../general_store/requires/general_item_transfer_controller" ) 
		return;
    }

    function set_transfer_req_report(company_id,update_id){

        var report_title=$( "div.form_caption" ).html();
        print_report(company_id+'*'+update_id+'*'+report_title, "general_transfer_requisition_print", "../general_store/requires/general_transfer_requisition_controller" ) ;
        return;

    }

</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",$permission);  ?><br />    		 
    <form name="item_transfer_report_all_category_1" id="item_transfer_report_all_category_1" autocomplete="off" >
   <h3 align="left" id="accordion_h1" style="width:1050px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
    
    <div style="width:1050px;" align="center" id="content_search_panel">
        <fieldset style="width:100%;">
                <table class="rpt_table" cellpadding="0" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th class="must_entry_caption">Company</th>
                        <th class="must_entry_caption">Transfer Criteria</th>
                        <th id="from_caption_td">From Company</th>   
                        <th id="to_caption_td">To Company</th>   
                        <th class="must_entry_caption">Item Category</th>
                        <th class="must_entry_caption">Transfer Date</th>
                        <th><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_field()" /></th>
                    </tr>
                </thead>
                <tr class="general">
                     <td align="center">
						<?   
                        echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
                        ?>              
                     </td>
                     <td align="center">
						<?   
                        echo create_drop_down( "cbo_transfer_type", 130, $item_transfer_criteria ,"", 0, "--Select--", $selected, "load_drop_down( 'requires/item_transfer_report_all_category_controller',this.value+'**'+$('#cbo_company_name').val(), 'load_drop_down_from_company', 'from_td' );load_drop_down( 'requires/item_transfer_report_all_category_controller',this.value+'**'+$('#cbo_company_name').val(), 'load_drop_down_to_company', 'to_td' );fnc_change_caption(this.value)", "","1,2,4");
                        ?>              
                     </td>
                    <td id="from_td">
                         <?
                        	echo create_drop_down( "cbo_company_from", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
                        ?>                          
                    </td>
                    <td id="to_td">
                         <?
                        	echo create_drop_down( "cbo_company_to", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
                        ?>                          
                    </td>
                    <td>
						<?
                            $transfer_item_category=array(1=>"Yarn",2=>"Grey Fabric(Knit)",3=>"Knit Finish Fabric",4=>"Accessories",5=>"General",6=>"Chemical");

                            echo create_drop_down( "cbo_item_cat", 120, $transfer_item_category,"", 1, "-- Select Item --", $selected, "",0,"" ); 
                        ?>
                    </td>
                    <td>
                    	<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:100px;" placeholder="From Date" readonly/>&nbsp; TO &nbsp;
                    	<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" placeholder="To Date" style="width:100px;" readonly/>
                    </td>
                    <td>
                        <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:70px" class="formbutton" />
                    </td>
                </tr>
                <tr>
                	<td colspan="12" align="center" valign="bottom"><? echo load_month_buttons(1);  ?></td>
                </tr>
                
            </table> 
        </fieldset> 
           
    </div>
        <div id="report_container" align="center"></div>
        <div id="report_container2"></div>  
    
    </form>    
</div>    
</body>

<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
