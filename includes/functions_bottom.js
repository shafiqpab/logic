
	// To Change Background Color of Validated Field\
	var field_level_menual = (field_level_menual == 1)?field_level_menual:0;

	jQuery(".text_boxes").click(function() {
		var contentPanelId = jQuery(this).attr("id");
		if(document.getElementById(contentPanelId).style.backgroundColor!="")
			document.getElementById(contentPanelId).style.backgroundColor="";

	});

	jQuery(".text_area").click(function() {
		var contentPanelId = jQuery(this).attr("id");
		if(document.getElementById(contentPanelId).style.backgroundColor!="")
			document.getElementById(contentPanelId).style.backgroundColor="";

	});

	jQuery(".combo_boxes").click(function() {
		var contentPanelId = jQuery(this).attr("id");
		if(document.getElementById(contentPanelId).style.backgroundColor!="")
			document.getElementById(contentPanelId).style.backgroundColor="";
	});

	jQuery(".text_boxes_numeric").click(function() {
		var contentPanelId = jQuery(this).attr("id");
		if(document.getElementById(contentPanelId).style.backgroundColor!="")
			document.getElementById(contentPanelId).style.backgroundColor="";

	});
	jQuery(".datepicker").click(function() {
		var contentPanelId = jQuery(this).attr("id");
		if(document.getElementById(contentPanelId).style.backgroundColor!="")
			document.getElementById(contentPanelId).style.backgroundColor="";

	});
 // To Change Background Color of Validated Field ends

 		/*																				//Numeric Text Box Validation
 jQuery(".text_boxes_numeric").keydown(function(e) {
	var c = String.fromCharCode(e.which);
 	var evt = (e) ? e : window.event;
      var key = (evt.keyCode) ? evt.keyCode : evt.which;


      if(key != null)
        key = parseInt(key, 10);
		if (isUserFriendlyChar(key)) return false;


 alert(c+"="+ e.newValue+"="+key);

      return true;

});
*/

jQuery(".text_boxes_numeric").keypress(function(e) {

	var c = String.fromCharCode(e.which);
	var evt = (e) ? e : window.event;
	var key = (evt.keyCode) ? evt.keyCode : evt.which;
	if(key != null) key = parseInt(key, 10);
	var allowed = '1234567890.'; // ~ replace of Hash(#)
	if (isUserFriendlyChar(key)) return true
		else if (key != 8 && key !=0 && allowed.indexOf(c) < 0)
			return false;
		else if (!numeric_valid( $(this).attr('id'), 0))
			return false;


});

jQuery(".text_boxes_numeric").blur(function(e) {
	numeric_valid( $(this).attr('id'), 1)
});

function numeric_valid( id, from)
{
	var txt=$('#'+id).val();//.split('.');
	var dotposl=txt.lastIndexOf(".");
	var dotposf=txt.indexOf(".");
	if (dotposl!=dotposf)
	{
		var txt_d=$('#'+id).val().substr(0,dotposl);
		$('#'+id).val(txt_d);//alert(txt_d);
		numeric_valid( id, from )
	}
	else return true;
}

function isUserFriendlyChar(val) {
      	//Backspace, Tab, Enter, Insert, and Delete
      if(val == 8 || val == 9 || val == 13 || val == 46 )// || val == 45 Insert
      	return true;
	// Ctrl, Alt, CapsLock, Home, End, and Arrows
     // if((val > 16 && val < 21) || (val > 34 && val < 41))
     //   return true;
	// The rest
	return false;
}
 //Numeric Text Box Validation Ends


	// Special Character Validation
	jQuery(".text_boxes").keypress(function(e) {
		var c = String.fromCharCode(e.which);
	//alert(c);
	var allowed = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890.-,%@!/\<>?+[]{};: '; // ~ replace of Hash(#)()
	if (e.which != 8 && e.which !=0 && allowed.indexOf(c) < 0)
		return false;

});

	$('.text_boxes').blur(function(e) {
		var target = e.target || e.srcElement;
		if (document.getElementById(target.id).value!="") document.getElementById(target.id).value=document.getElementById(target.id).value.replace("#","~");
		if (document.getElementById(target.id).value!="") document.getElementById(target.id).value=document.getElementById(target.id).value.replace("&","n");
		if (document.getElementById(target.id).value!="") document.getElementById(target.id).value=document.getElementById(target.id).value.replace("(","[");
		if (document.getElementById(target.id).value!="") document.getElementById(target.id).value=document.getElementById(target.id).value.replace(")","]");
	});

	jQuery(".text_area").keypress(function(e) {
		var c = String.fromCharCode(e.which);
	var allowed = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890.-,%@!/\<>?+[]{};: '; // ~ replace of Hash(#)()
	if (e.which != 8 && e.which != 13 && e.which !=0 && allowed.indexOf(c) < 0)
		return false;

});

	$('.text_area').blur(function(e) {
		var target = e.target || e.srcElement;
		document.getElementById(target.id).value=document.getElementById(target.id).value.replace("#","~");
		document.getElementById(target.id).value=document.getElementById(target.id).value.replace("(","[");
		document.getElementById(target.id).value=document.getElementById(target.id).value.replace(")","]");
	});


	// Special Character Validation Ends

	// Global Date Picker Initialisaton
	$( ".datepicker" ).datepicker({
		dateFormat: 'dd-mm-yy',
		changeMonth: true,
		changeYear: true
	});

	$('.timepicker').timepicker({
		H: true
	});
	 // Datapickker ENds

	 function set_bangla()
	 {
	 	$(".bangla").bnKb({
	 		'switchkey': {"webkit":"k","mozilla":"y","safari":"k","chrome":"k","msie":"y"},
	 		'driver': phonetic
	 	});
	 }


	 $(".must_entry_caption").each(function( index ) {
	 	var ht=" <font color='blue'>"+$(this).html()+"</font>";
	 	$(this).html(ht);
	 	$(this).attr('title','Must Entry Field.');
	 });

	 var allowedchars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890.-,%@!/\<>?+[]{};: ';
	 $(".text_boxes").each(function( index ) {
	 	var ttl=$(this).attr('title');
	 	if(!ttl) var ttl=""; else ttl=ttl+";"
	 	$(this).attr('title',ttl+'  Allowed Characters: '+allowedchars);
	 });
	 $(".text_area").each(function( index ) {
	 	var ttl=$(this).attr('title');
	 	if(!ttl) var ttl=""; else ttl=ttl+";"
	 	$(this).attr('title',ttl+'  Allowed Characters: '+allowedchars);
	 });

	//if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
		// alert("Android");
	//}

	/*
	if( select_job_year_all==1 )
	{
		$('#cbo_year').val(0);
	}
	*/




if(isMobile.any()) {  // 09-03-2015
   //alert("This is a Mobile Device");
   $(".text_boxes").each(function( index ) {
   	var ttl=$(this).attr('onDblClick');
   	if(!ttl) var ttl=""; else ttl=ttl+";"
   	$(this).attr('onClick',ttl);
   });

   $(".text_boxes_numeric").each(function( index ) {
   	var ttl=$(this).attr('onDblClick');
   	if(!ttl) var ttl=""; else ttl=ttl+";"
   	$(this).attr('onClick',ttl);
   });
}


if( $('.form_caption').html()!=null)
{
	//alert ($('.form_caption').html());
	window.parent.document.title = $('.form_caption').html();
}
else
{
	if( $("a.active").html()!=null)
	{
		window.parent.document.title = $("a.active").html();
	}
	else
	{
		window.parent.document.title = 'Logic ERP Solution';
	}
}

var first_values=new Array;
var first_sts = new Array;
var check_disable=new Array;
var check_value = new Array;
function set_field_level_access( company )
{ 
	
	try{
		if(field_level_data['action_company_id']!=undefined){
			/*
				Note: Follow blew link. here push 'action_company_id' properti to json field_level_data
				production/finish_feb_delivery_entry.php
			*/
			var company=$('#'+field_level_data['action_company_id']).val();
		}
	}
	catch(error){
		return true;
	}

	
	$.each( first_sts, function( keys, values ) {
		if(jQuery.inArray(values.f_titles, check_disable) == -1)
		{
			check_disable.push(values.f_titles);
			if(values.f_vals==undefined)
				$('#'+values.f_titles).attr('disabled',false);
			else
				$('#'+values.f_titles).attr('disabled',true);
		}
	});
	$.each( first_values, function( keys, values ) {
		if(jQuery.inArray(values.f_title, check_value) == -1)
		{
			check_value.push(values.f_title);
			$('#'+values.f_title).val(values.f_val);
			//$('#'+values.f_title).val(5);
		}
	});
	check_value.length=0;
	check_disable.length=0;

	if( field_level_data[company]==undefined){
		return;
	}

	if(company!=0)
	{
		$.each( field_level_data[company], function( key, value ) {
			
			var keryArr = key.split('_');
			let lastElement = keryArr.pop();
			if(lastElement ==1 || lastElement ==0){
				var idpreFix = key.replace(lastElement,'');
				var totalRows = $('input[id^='+idpreFix+']').length;
			   	for(var i = lastElement;i<= totalRows;i++){
					if( value['is_disable']==1){$('#'+idpreFix+i).attr('disabled',true);}
					else {$('#'+idpreFix+i).attr('disabled',false);}
					if(value['defalt_value']==null || value['defalt_value']=="undefined"){value['defalt_value']='';}
					if(value['defalt_value']!='')
					{ 
						if(value['defalt_value']=="") return;
						first_values.push({
							f_title: idpreFix+i,
							f_val:  $('#'+idpreFix+i).val()
						});
						if(value['defalt_value']!='')
						{ 
							$('#'+idpreFix+i).val(value['defalt_value']);
							$('#'+idpreFix+i).change();
						}
					}
				}
			}
			else{

				if( value['is_disable']==1) {
					first_sts.push({
						f_titles: key,
						f_vals:  $('#'+key).attr('disabled')
					});
					$('#'+key).attr('disabled',true);
				}
				else
				{
					first_sts.push({
						f_titles: key,
						f_vals:  $('#'+key).attr('disabled')
					});
					$('#'+key).attr('disabled',false);
				}
				
				if(value['defalt_value']==null || value['defalt_value']=="undefined"){value['defalt_value']='';}
				
				if(value['defalt_value']!='')
				{ 
					if(value['defalt_value']=="") return;
					first_values.push({
						f_title: key,
						f_val:  $('#'+key).val()
					});
					
					if(value['defalt_value']!='')
					{ 
						$('#'+key).val(value['defalt_value']);
						$('#'+key).change();
					}
				}
			}


		});
	}

}


var avoidCompany=['cbo_transport_company'];
   
if(field_level_menual == 0){
	// "LIVE" EVENT IS DEPRICATED IN V3.1.1.
	if(jQuery.fn.jquery == '3.1.1'){
		jQuery(document).on('change','.combo_boxes', function(){
			var str= $(this).attr('id');
			if( str.indexOf("company") >-1){
				if(str!=avoidCompany[0]){
					set_field_level_access( $(this).val() );
				}
			}
		});
	}else{
	
			jQuery('.combo_boxes').live('change', function(){
				var str= $(this).attr('id');
				if( str.indexOf("company") >-1){
					if(str!=avoidCompany[0]){
						set_field_level_access( $(this).val() );
					}
				}
			});
	}
}





jQuery('.text_boxes_numeric').bind('keypress', function(event){
	var dataLength = $(this).attr("data-length");
	if ($(this).attr("data-length")) 
	{
		var $this = $(this);
		if ((event.which != 46 || $this.val().indexOf('.') != -1) &&
		((event.which < 48 || event.which > 57) &&
		(event.which != 0 && event.which != 8))) {
			event.preventDefault();
		}

		var thisValue = $(this).val();
		if ((event.which == 46) && (thisValue.indexOf('.') == -1)) {
			setTimeout(function() {
				if ($this.val().substring($this.val().indexOf('.')).length > 3) {
					$this.val($this.val().substring(0, $this.val().indexOf('.') + 3));
				}
			}, 1);
		}

		if ((thisValue.indexOf('.') != -1) &&
			(thisValue.substring(thisValue.indexOf('.')).length > dataLength) &&
			(event.which != 0 && event.which != 8) &&
			($(this)[0].selectionStart >= thisValue.length - dataLength)) {
				event.preventDefault();
		}
	}
	      
});

jQuery('.text_boxes_numeric').bind('paste', function(e){
	var dataLength = $(this).attr("data-length")*1;
	if ($(this).attr("data-length")) 
	{
		var thisValue = e.originalEvent.clipboardData.getData('Text');
		if ((thisValue.split(".")[1].length > dataLength)) {
			e.preventDefault();
			$(this).val(thisValue.substring(0, thisValue.indexOf('.') + (dataLength+1)));
		}
	}
});

/**
 * Given an input field, this function will only allow numbers with up to two decimal places to be input.
 * @param {object} element
 * @return {number}
 */
 function forceNumber(element) {
	element
	  .data("oldValue", '')
	  .bind("paste", function(e) {
		var validNumber = /^[-]?\d+(\.\d{1,2})?$/;
		element.data('oldValue', element.val())
		setTimeout(function() {
		  if (!validNumber.test(element.val()))
			element.val(element.data('oldValue'));
		}, 0);
	  });
	element
	  .keypress(function(event) {
		var text = $(this).val();
		if ((event.which != 46 || text.indexOf('.') != -1) && //if the keypress is not a . or there is already a decimal point
		  ((event.which < 48 || event.which > 57) && //and you try to enter something that isn't a number
			(event.which != 45 || (element[0].selectionStart != 0 || text.indexOf('-') != -1)) && //and the keypress is not a -, or the cursor is not at the beginning, or there is already a -
			(event.which != 0 && event.which != 8))) { //and the keypress is not a backspace or arrow key (in FF)
		  event.preventDefault(); //cancel the keypress
		}
  
		if ((text.indexOf('.') != -1) && (text.substring(text.indexOf('.')).length > 2) && //if there is a decimal point, and there are more than two digits after the decimal point
		  ((element[0].selectionStart - element[0].selectionEnd) == 0) && //and no part of the input is selected
		  (element[0].selectionStart >= element.val().length - 2) && //and the cursor is to the right of the decimal point
		  (event.which != 45 || (element[0].selectionStart != 0 || text.indexOf('-') != -1)) && //and the keypress is not a -, or the cursor is not at the beginning, or there is already a -
		  (event.which != 0 && event.which != 8)) { //and the keypress is not a backspace or arrow key (in FF)
		  event.preventDefault(); //cancel the keypress
		}
	  });
  }

// set_field_level_access( 1 )

$( ".combo_boxes" ).each(function( index ) 
{
	
	if($('#'+this.id+' option').length==2)
	{
		if($('#'+this.id+' option:first').val()==0)
		{
			$('#'+this.id).val($('#'+this.id+' option:last').val());
			//alert($('#'+this.id+' option:last').val());
			if (!$(this).hasClass("onchange_void")) {
				eval( $('#'+this.id).attr('onchange') );
			}
			
			var str= $(this).attr('id');
			if( str.indexOf("company") >-1){set_field_level_access( $(this).val() );}
		}
	}
	else if($('#'+this.id+' option').length==1 && $('#'+this.id+' option:last').val()!=0)
	{
		$('#'+this.id).val($('#'+this.id+' option:last').val());
		if (!$(this).hasClass("onchange_void")) {
			eval( $('#'+this.id).attr('onchange') );
		 }
	}

	/*if($('#'+this.id+'').val!=0)
	{
		//$('#'+this.id).val($('#'+this.id+' option:last').val());
		eval($('#'+this.id).attr('onchange'));
	}*/
});


/*function load_room_rack_self_bin1(controllerValue,action_type,selector,company_id,location_id='',sotre_id='',floor_id='',room_id='',rack_id='',shelf_id='',bin_id='',custom_function='')
{
	var controller=controllerValue.split('*');
	var action = "load_room_rack_self_bin";
	var item_category = controller[1];
	var element_id = controller[2];
	alert(selector);
	load_drop_down( controller[0], action_type + '*' + company_id + '*' + location_id + '*' + sotre_id + '*' + floor_id + '*' + room_id + '*' + rack_id + '*' + shelf_id + '*' + bin_id + '*' + element_id + '*' + item_category+ '*' + custom_function, action, selector );
}*/

function load_room_rack_self_bin(controllerValue,action_type,selector,company_id,location_id='',sotre_id='',floor_id='',room_id='',rack_id='',shelf_id='',bin_id='',custom_function='',element_width='',display_orientation='V')
{
	var controller 		= controllerValue.split('*');
	var action 			= "load_room_rack_self_bin";
	var item_category 	= controller[1];
	var element_id 		= controller[2];
	var element_seq 	= controller[3];

	if(display_orientation=="H"){ 
		var parents 		= $("#"+element_id).parents("#tr__"+element_seq).find("td#room_td_to").attr("id");
		if(action_type=="floor")
			var selector 		= "#tr__"+element_seq + " #floor_td_to";
		if(action_type=="room")
			var selector 		= "#tr__"+element_seq + " #room_td_to";

		if(action_type=="rack")
			var selector 		= "#tr__"+element_seq + " #rack_td_to";

		if(action_type=="shelf")
			var selector 		= "#tr__"+element_seq + " #shelf_td_to";

		if(action_type=="bin")
			var selector 		= "#tr__"+element_seq + " #bin_td_to"; 

	}else{
		var selector = "#"+selector;

	}

	load_drop_down_by_element( controller[0], action_type + '*' + company_id + '*' + location_id + '*' + sotre_id + '*' + floor_id + '*' + room_id + '*' + rack_id + '*' + shelf_id + '*' + bin_id + '*' + element_id + '*' + item_category + '*' + custom_function + '*' + element_seq + '*' + element_width + '*' + display_orientation, action, selector );
}

function load_drop_down_by_element( plink, data, action, container) {
	var strURL = plink+".php?data=" + data+"&action=" + action+"&action_from=" + plink;
	var http = createObject();
	if( http ) {
		http.onreadystatechange = function() {
			if( http.readyState == 4 ) {
				if( http.status == 200 ){

					$(container).html(http.responseText);set_all_onclick();
				}
			}
		}
		http.open( "GET", strURL, false );
		http.send( null );
	}
}

/*function reset_room_rack_self_bin1(selectorValue){
	var selector=selectorValue.split('*');
	var selector_name = (selector[1]!=undefined)? "_"+selector[1]:"";
	if(selector[0] == "store"){
		$('#cbo_floor'+selector_name+',#cbo_room'+selector_name+',#txt_rack'+selector_name+',#txt_shelf'+selector_name+',#cbo_bin'+selector_name).html("<option value='0'>--Select--</option>");
	}
	if(selector[0] == "floor"){
		$('#cbo_room'+selector_name+',#txt_rack'+selector_name+',#txt_shelf'+selector_name+',#cbo_bin'+selector_name).html("<option value='0'>--Select--</option>");
	}
	if(selector[0] == "room"){
		$('#txt_rack'+selector_name+',#txt_shelf'+selector_name+',#cbo_bin'+selector_name).html("<option value='0'>--Select--</option>");
	}
	if(selector[0] == "rack"){
		$('#txt_shelf'+selector_name+',#cbo_bin'+selector_name).html("<option value='0'>--Select--</option>");
	}
	if(selector[0] == "bin"){
		$('#cbo_bin'+selector_name).html("<option value='0'>--Select--</option>");
	}
}*/

function reset_room_rack_self_bin(selectorValue){
	var selector=selectorValue.split('*');
	var selector_name = (selector[1]!=undefined)? "_"+selector[1]:"";
	if(selector[0] == "store"){
		$('#cbo_floor'+selector_name+',#cbo_room'+selector_name+',#txt_rack'+selector_name+',#txt_shelf'+selector_name+',#cbo_bin'+selector_name).html("<option value='0'>--Select--</option>");
	}
	if(selector[0] == "floor"){
		$('#cbo_room'+selector_name+',#txt_rack'+selector_name+',#txt_shelf'+selector_name+',#cbo_bin'+selector_name).html("<option value='0'>--Select--</option>");
	}
	if(selector[0] == "room"){
		$('#txt_rack'+selector_name+',#txt_shelf'+selector_name+',#cbo_bin'+selector_name).html("<option value='0'>--Select--</option>");
	}
	if(selector[0] == "rack"){
		$('#txt_shelf'+selector_name+',#cbo_bin'+selector_name).html("<option value='0'>--Select--</option>");
	}
	if(selector[0] == "shelf"){
		$('#cbo_bin'+selector_name).html("<option value='0'>--Select--</option>");
	}
}

/**
 * Appened a button to go to the top of the page
 * //Added by Mohammad Shafiqur Rahman
 * */
 function onScroll() {
 	var top_button = "<a class='go_top_button' onClick='go_to_top()'> ^ </a>";
 	if($(document).scrollTop()>150){
 		$("body").append(top_button);
 	}else{
 		$(".go_top_button").css({
 			"display": "none"
 		});
 		$(".go_top_button").remove();
 	}
	//update();
}

function go_to_top() {

	$("html, body").animate({scrollTop: 0}, 300);
}

window.addEventListener('scroll', onScroll, false);



function setFieldLevelAccess(company){
	// alert(company);
	console.log(field_level_data);
	if(company!=0)
	{    //console.log(field_level_data[company]);
		if(field_level_data[company]!=undefined){
		
			$.each( field_level_data[company], function( key, value ) {

				var keryArr = key.split('_');
				let lastElement = keryArr.pop();


				if(lastElement ==1 || lastElement ==0){
					var idpreFix = key.replace(lastElement,'');
					var totalRows = $('input[id^='+idpreFix+']').length;
					
					for(var i = lastElement;i<= totalRows;i++){
						if( value['is_disable']==1){$('#'+idpreFix+i).attr('disabled',true);}
						else {$('#'+idpreFix+i).attr('disabled',false);}
						 
						
						
						if(value['defalt_value']==null || value['defalt_value']=="undefined"){value['defalt_value']='';}
						if(value['defalt_value']!='')
						{ 
							if(value['defalt_value']=="") return;
							first_values.push({
								f_title: idpreFix+i,
								f_val:  $('#'+idpreFix+i).val()
							});
							if(value['defalt_value']!='')
							{ 
								$('#'+idpreFix+i).val(value['defalt_value']);
								$('#'+idpreFix+i).change();
							}
						}



					}
				}
				else{
					if( value['is_disable']==1) {
						first_sts.push({
							f_titles: key,
							f_vals:  $('#'+key).attr('disabled')
						});

						 console.log('#'+key);

						$('#'+key).attr('disabled',true);
					}
					else
					{
						first_sts.push({
							f_titles: key,
							f_vals:  $('#'+key).attr('disabled')
						});
						$('#'+key).attr('disabled',false);
					}
					
					if(value['defalt_value']==null || value['defalt_value']=="undefined"){value['defalt_value']='';}
					
					if(value['defalt_value']!='')
					{  
						if(value['defalt_value']=='') return;
						first_values.push({ 
							f_title: key,
							f_val:  $('#'+key).val()
						});
						
						if(value['defalt_value']!="")
						{
							
							if(key=='cbo_order_uom' && $('#'+key).val()!=0){
								//$('#'+key).val(value['defalt_value']);
								//$('#'+key).change();
							}
							else{
								$('#'+key).val(value['defalt_value']);
								$('#'+key).change();
							}						
						}
		
					}
				}

			});
		}//undefined  alert(value['defalt_value']);
	}
	
}


$(document).ready(function() {
	$('.combo_boxes_search').select2();
});


jQuery('.combo_boxes').live('change', function(){
	var str= $(this).attr('id');
	if( str.indexOf("company") >-1){
		$(".form_caption span").remove();
		var path_count=window.location.pathname.slice(1).split("/").length;
		path_count=path_count-2;
		var path_length="";
		for(var i=0; i<path_count; i++)
		{
			path_length+="../";
		}
		//alert(set_iso_no(path_length));
		var iso_string_arr=set_iso_no(path_length).split("$");
		for(var i in iso_string_arr)
		{
			var iso_string_data_arr=iso_string_arr[i].split("_");
			if(trim($(this).val())==trim(iso_string_data_arr[0])){
				//alert(iso_string_data_arr[0]+"="+iso_string_data_arr[1]);
				$(".form_caption").append("&nbsp;&nbsp;<span> ISO No : "+iso_string_data_arr[1]+"</span>");
			}
		}
	}
});



// ======= end of Scroll button ======//
