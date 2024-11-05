<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create for Date Wise Yarn Issue Report For Buyer

Functionality	:
JS Functions	:
Created by		:	Md. Wasy Ul Amin
Creation date 	: 	07-11-2023
Updated by 		:
Update date		:
QC Performed BY	:
QC Date			:
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Date Wise Yarn Issue","../../../", 1, 1, $unicode,1,1); 
?>
<script> 
    var permission='<? echo $permission; ?>';
    if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

    
	function openmypage_style()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();
		var buyer = $("#cbo_buyer_name").val();
		var txt_style_ref_no = $("#txt_style_ref_no").val();
		var txt_style_ref_id = $("#txt_style_ref_id").val();
		var txt_style_ref = $("#txt_style_ref").val();
		var cbo_year = $("#cbo_year").val();
		//var txt_style_ref_no = $("#txt_style_ref_no").val();
		var page_link='requires/date_wise_yarn_issue_report_for_buyer_controller.php?action=style_refarence_surch&company='+company+'&buyer='+buyer+'&txt_style_ref_no='+txt_style_ref_no+'&txt_style_ref_id='+txt_style_ref_id+'&txt_style_ref='+txt_style_ref+'&cbo_year='+cbo_year;
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=420px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var style_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var style_des=this.contentDoc.getElementById("txt_selected").value; // product Description
			var style_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			//alert(style_no);
			$("#txt_style_ref").val(style_des);
			$("#txt_style_ref_id").val(style_id);
			$("#txt_style_ref_no").val(style_no);
		}
	}


    function  generate_report(rptType)
    {
            //var cbo_item_cat = $("#cbo_item_cat").val();            
            //var cbo_style_owner = $("#cbo_style_owner").val();            
            //var txt_order = $("#txt_order").val();
            //var txt_order_id = $("#txt_order_id").val();            
            //var cbo_source = $("#cbo_source").val();
            //var cbo_yarn_count = $("#cbo_yarn_count").val();
            //var cbo_based_on = $("#cbo_based_on").val();
            //var cbo_order_type = $("#cbo_order_type").val();
            //var cbo_knitting_source = $("#cbo_knitting_source").val();
            //var txt_search_val = $("#txt_search_val").val();
            //var cbo_search_id = $("#cbo_search_id").val();
            //var cbo_use_for = $("#cbo_use_for").val();
            var cbo_company_name = $("#cbo_company_name").val();
            var cbo_buyer_name = $("#cbo_buyer_name").val();
            var txt_style_ref = $("#txt_style_ref").val();
            var txt_style_ref_id = $("#txt_style_ref_id").val();
            var txt_date_from = $("#txt_date_from").val();
            var txt_date_to = $("#txt_date_to").val();
            var cbo_dyed_type = $("#cbo_dyed_type").val();
            var cbo_store_name = $("#cbo_store_name").val();
            var internal_ref = $("#internal_ref").val();
            //var cbo_year = $("#cbo_year").val();
            //var fso_id = $("#fso_id").val();

            if(txt_style_ref!="")
            {
                if( form_validation('cbo_company_name','Company Name')==false)
                {
                    return;
                }
            }
            else
            {
                if( form_validation('cbo_company_name','Company Name')==false )
                {
                    return;
                } else {

                    if(cbo_company_name == 0) {
                        alert("Please Select a company");
                        return;
                    }

                    if(txt_style_ref=="" && internal_ref==""){
                        if( form_validation('txt_date_from*txt_date_to','Form Date*To Date')==false)
                        {
                            return;
                        }
                    }                    
                }
            }                          

            var dataString = "&cbo_company_name="+cbo_company_name+"&cbo_buyer_name="+cbo_buyer_name+"&txt_style_ref="+txt_style_ref+"&txt_style_ref_id="+txt_style_ref_id+"&txt_date_from="+txt_date_from+"&txt_date_to="+txt_date_to+"&cbo_dyed_type="+cbo_dyed_type+"&rptType="+rptType+"&cbo_store_name="+cbo_store_name+"&cbo_year="+cbo_year+"&internal_ref="+internal_ref;             
            var data="action=generate_report"+dataString;
            //freeze_window(5);
            http.open("POST","requires/date_wise_yarn_issue_report_for_buyer_controller.php",true);
            http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
            http.send(data);
            http.onreadystatechange = generate_report_reponse;
        }

        function generate_report_reponse()
        {
            if(http.readyState == 4)
	        {
                var reponse=trim(http.responseText).split("**");
                $("#report_container2").html(reponse[0]);  
                document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview"   name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
                setFilterGrid("table_body", -1);
                show_msg('3');
                release_freezing();
            }

        }

         

        function new_window()
        {
            
                document.getElementById('scroll_body').style.overflow="auto";
                document.getElementById('scroll_body').style.maxHeight="none"; 
                var w = window.open("Surprise", "#");
                var d = w.document.open();
                d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
            '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
                d.close(); 
                document.getElementById('scroll_body').style.overflow="auto"; 
                document.getElementById('scroll_body').style.maxHeight="250px";
        }

         

        

</script>
<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../../../",$permission);  ?><br />
    <form name="item_receive_issue_1" id="item_receive_issue_1" autocomplete="off" >
    <h3 align="left" id="accordion_h1" style="width:1820px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
    <div style="width:980px;" align="center" id="content_search_panel">
        <fieldset style="width:980px;">
                <table class="rpt_table" width="980" cellpadding="0" cellspacing="0" rules="all">
                <thead>
                    <tr>
                        <th width="100" class="must_entry_caption" style="color:blue;">Companyn</th>
                       <!-- <th width="100" class="must_entry_caption">Item Category</th>
                        <th width="100" class="">Style Owner</th> -->
                        <th width="100">Buyer Name</th>
                        <th width="100">Store Name</th>
                        <th width="80">Dyed Type</th>
                        <!--<th width="80">Count</th> -->
                        <th width="60">Job Year.</th>
                        <th width="60">Job No.</th>                         
                        <th width="60">Internal Ref</th>                         
                        <!--<th width="60">Order No.</th>
                        <th width="70">Search By</th>
                        <th width="60" id="file_ref_td" >File No</th>
                        <th width="80">Based On</th>-->
                        <th width="170" id="up_tr_date" class="must_entry_caption" style="color:blue;">Transaction Date Range</th>
                        <!--<th width="80">Order Type</th>
                        <th width="80">Grey Source</th>
                        <th width="80">Use For</th> -->
                        <th width="170"><input type="reset" name="res" id="res" value="Reset" style="width:60px" class="formbutton" onClick="reset_field()" />
                         
                        <span style="float: left;display: none;margin-left: 10px;" id="show_booking_span">Show Booking <input type="checkbox"  name="show_booking" id="show_booking" value="0" onClick="booking_check_item_fnc()" ></span>
                        </th>
                    </tr>
                </thead>
                <tr class="general">
                    <td>
                        <?
                        	echo create_drop_down( "cbo_company_name", 100, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0  $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/date_wise_yarn_issue_report_for_buyer_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );load_drop_down('requires/date_wise_yarn_issue_report_for_buyer_controller',this.value, 'load_drop_down_store', 'store_td' );" );
                             
                        ?>
                    </td>
                    <!--<td>
						<?
                        	//echo create_drop_down( "cbo_item_cat", 100, $item_category,"", 1, "-- Select Item --", $selected, "job_order_per();showHide(this.value);showBooking();",0,"1,2,3,4,13,8,9,10,11,15,16,17,18,19,20,21,22,32,34,35,36,37,38,39,23,33,40,41,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,81,89,90,91,92,93,94,99,101,106,107" );
                        ?>
                    </td>

                    <td>
                            <?
                        	//echo create_drop_down( "cbo_style_owner", 100, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Owner --", $selected, "" );
                        ?>
                    </td> -->
                    <td id="buyer_td"><?
                        	echo create_drop_down( "cbo_buyer_name", 100, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" );
                        ?>
                    </td>
                    <td id="store_td"><?
                        	echo create_drop_down( "cbo_store_name", 100, $blank_array,"", 1, "-- All Store --", $selected, "",0,"" );
                        ?>
                    </td>
                     <td align="center">
						<?
                        $dyedType=array(0=>'All',1=>'Dyed Yarn',2=>'Non Dyed Yarn');
                        echo create_drop_down( "cbo_dyed_type", 80, $dyedType,"", 0, "--Select--", $selected, "", "","");
                        ?>
                     </td>
                    <!--<td>
						<?
                        //echo create_drop_down( "cbo_yarn_count", 80, "select id,yarn_count from lib_yarn_count where is_deleted = 0 AND status_active = 1 ORDER BY yarn_count ASC","id,yarn_count", 1, "--Select--", 0, "",0 );
                        ?>
                    </td> -->
                     <td>
						<?
                            echo create_drop_down( "cbo_year", 65, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" );
                        ?>
                    </td>

                    <td align="center">
                        <input style="width:60px;"  name="txt_style_ref" id="txt_style_ref"  ondblclick="openmypage_style()"  class="text_boxes" placeholder="Browse/Write"   />
                        <input type="hidden" name="txt_style_ref_id" id="txt_style_ref_id"/>    <input type="hidden" name="txt_style_ref_no" id="txt_style_ref_no"/>
                    </td>

                    <td>
                        <input style="width:60px;"  name="internal_ref" id="internal_ref"  ondblclick=""  class="text_boxes"/>
                    </td>

                     <!--<td align="center">
                        <input type="text" style="width:60px;"  name="txt_order" id="txt_order"  ondblclick="openmypage_order()"  class="text_boxes" placeholder="Browse/Write"   />
                        <input type="hidden" name="txt_order_id" id="txt_order_id"/> <input type="hidden" name="txt_order_id_no" id="txt_order_id_no"/>
                    </td>
                     <td>
                    	<?
						//$search_by_arr=array(1=>"File No",2=>"Ref. No");
                        //echo create_drop_down( "cbo_search_id", 70, $search_by_arr,"", 0, "", 1, "fn_change_base2(this.value);",0 );
                        ?>
                    </td>
                    <td>
                    	<input type="text" name="txt_search_val" id="txt_search_val" class="text_boxes" style="width:60px;" placeholder="" />

                    </td>
                    <td>
                    	<?
						//$base_on_arr=array(1=>"Transaction Date",2=>"Insert Date");
                        //echo create_drop_down( "cbo_based_on", 80, $base_on_arr,"", 0, "", 1, "fn_change_base(this.value);",0 );
                        ?>
                    </td>-->
                    
                    <td>
                    	<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px;" placeholder="From Date" readonly/>&nbsp; TO &nbsp;
                    	<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" placeholder="To Date" style="width:55px;" readonly/>
                    </td>

                    <!--<td>
                    	<?
						//$order_type=array(1=>"With Order",2=>"Without Order");
                        //echo create_drop_down( "cbo_order_type", 80, $order_type,"", 1, "ALL", 0, "",0 );
                        ?>
                    </td>
                    <td>
                    	<?
                        //echo create_drop_down( "cbo_knitting_source", 80, $knitting_source,"", 1, "ALL", 0, "","","1,3" );
                        ?>
                    </td>
                    <td>
                    	<?
						//echo create_drop_down( "cbo_use_for", 90, $use_for,"", 1, "-- Select --", "", "" );
						?>
                    </td>--> 
                    <td>                         
                        <input type="button" name="search4" id="search4" value="Show" onClick="generate_report(3)" style="width:55px" class="formbutton"/>
                         
                    </td>
                </tr>
                <tr>
                	<td colspan="8" align="center" valign="bottom"><? echo load_month_buttons(1);  ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    
                    <!--<input type="button" name="search7" id="search7" value="General" onClick="generate_report(9)" style="width:100px" class="formbutton" />
                    <input type="button" name="search_excel" id="search_excel" value="Excel All" onClick="generate_report_for_excel(1)" style="width:100px" class="formbutton" /> -->
                    </td>
                    
                    
                    <td align="center" id="summary_button">
                   <!-- <input type="button" name="search8" id="search8" value="Receive Issue Summary" onClick="generate_report(4)" style="width:130px" class="formbutton" />&nbsp;
                    <input type="button" name="search9" id="search9" value="Issue Return" onClick="generate_report(6)" style="width:80px" class="formbutton" />&nbsp; -->
                    </td>
                </tr>

            </table>
        </fieldset>

    </div>
        <!-- Result Contain Start -->
        	<div style="margin-top:10px" id=""><span id="report_container"></span><span id="report_container3"></span></div>
            <div id="report_container2"></div>
        <!-- Result Contain END -->


    </form>
</div>
</body>


<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	// set_multiselect('cbo_source','0','0','','0');
	$('#cbo_style_owner').val(0);
</script>
</html>
