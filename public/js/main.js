
var modTimer_closer;
function closeColorBox() {
  clearTimeout(modTimer_closer);
	modTimer_closer = setTimeout(function(){
		$.colorbox.close();
	},300);
}

function openColorBox(options) {
	clearTimeout(modTimer_closer);
	setTimeout(function(){
	 	$.colorbox(options);
 	},200);
}

function modAlert(message) {
	// alert(message);
	message = message.replace('\n','<br/>');
	openColorBox({html:"<div><div style='background:#e1de21;padding:5px'><img src='"+ urlbase +"images/warning.png' style='float:left;padding-right:3px' ><h4 style='padding:0px;margin:0px'>Alert!</h4></div><div style='padding:10px;height:60px' >"+ message +"</div><div style='text-align:right;padding:5px;'><input type='button' onclick='closeColorBox()' value='   OK   ' style='cursor:pointer'></div></div>",transition:'none',height:'150px',width:'450px',fixed:true,title:true});
}

var serverProcessing_flag = false;
function serverProcessing(flag){
	// alert(message);
	if( flag ){
		openColorBox({html:"<div><div style='background:#e1de21;padding:5px'><img src='"+ urlbase +"images/web_server.png' style='float:left;padding-right:3px' ><h4 style='padding:0px;margin:0px'>Processing!</h4></div><div style='padding-top:25px;height:60px;width:54px;margin:auto' ><img src='"+ urlbase +"images/ajax-loader.gif'></div></div>",transition:'none',height:'150px',width:'250px',fixed:true,title:true,overlayClose: false,escKey:false})
	}else{
		closeColorBox();
	}
	serverProcessing_flag = flag;

}

var modConfirmFunctions = {};
function modConfirm(message,callback_yes,callback_no) {

	var _id = parseInt(Math.random()*99999999);
	modConfirmFunctions['yes_'+_id] = function(){
		if( callback_yes != undefined ){
			callback_yes();
		}
		closeColorBox();
	}
	modConfirmFunctions['no_'+_id] = function(){
		if( callback_no != undefined ){
			callback_no();
		}
		closeColorBox();
	}

	message = message.replace('\n','<br/>');
	openColorBox({html:"<div><div style='background:#e1de21;padding:5px'><img src='"+ urlbase +"images/warning.png' style='float:left;padding-right:3px' ><h4 style='padding:0px;margin:0px'>Confirm!</h4></div><div style='padding:10px;height:60px' >"+ message +"</div><div style='text-align:right;padding:5px;'><input name='yes' type='button' onclick='modConfirmFunctions.yes_"+ _id +"(); ' value='   Yes   ' style='cursor:pointer'><input name='no'  type='button' onclick='modConfirmFunctions.no_"+ _id +"();' value='   No   ' style='cursor:pointer'></div></div>",transition:'none',height:'150px',width:'450px',fixed:true});

	//var c = confirm(message);
	// if( c ){
	// 	if( callback_yes != undefined ){
	// 		callback_yes();
	// 	}
	// }else{
	// 	if( callback_no != undefined ){
	// 		callback_no();
	// 	}
	// }
	// return c;

}
var show_server_response_ids = {};
function showSuccess(container,message,delay,callback) {
	if(delay==undefined){
		delay = 3000;
	}
	var _id = $(container).attr('id');
	if( show_server_response_ids[_id] != undefined ){
		clearTimeout(show_server_response_ids[_id])
	}
	if( $.trim(message) != '' ){
		$(container).html('<div class="success">'+ message +'</div>').show();
		show_server_response_ids[_id] = setTimeout(function(){
			 $(container).hide('slide');
			 if( callback != undefined ){
			 	callback();
			 }
		},delay)
	}
}

function showError(container,message,delay,callback) {
	if(delay==undefined){
		delay = 3000;
	}
	var _id = $(container).attr('id');
	if( show_server_response_ids[_id] != undefined ){
		clearTimeout(show_server_response_ids[_id])
	}
	if( $.trim(message) != '' ){
		$(container).html('<div class="error">'+ message +'</div>').show();
		show_server_response_ids[_id] = setTimeout(function(){
			 $(container).hide('slide');
			 if( callback != undefined ){
			 	callback();
			 }
		},delay)
	}
}


function getServerData(action,data,callback){

	$.ajax({
		url: urlsite + 'dataservice/' + action,
        dataType: "json",
		type: 'post',
		data: data,
		success: function(json) {
			if (json === null || json === '' || json === undefined) {
				modAlert('Error accessing server please try again.');
				return;
			}
			if (callback !== undefined) {
				callback(json);
			}
		}

	});

}

function loadServerContent(container, action, callback, autoredirect) {

	autoredirect = (autoredirect !== undefined) ? false : autoredirect;
	$.ajax({
		url: urlsite + 'dataservice/' + action,
		success: function(response) {

			if (autoredirect && $('<span>' + response + '</span>').find('#session_expired').length > 0) {
				window.location.href = urlsite + 'login';
				return false;
			} else {
				$(container).html(response);
			}
			if (callback !== undefined) {
				callback(response);
			}
		}

	});

}

var server_loading_process = {};
function serverProcess(action, data, callback, autoredirect, show_process) {

	action_id = action.replace('/','_');
	if( server_loading_process[action_id] == undefined ){
		server_loading_process[action_id] = false;
	}
	autoredirect = (autoredirect === undefined) ? false : autoredirect;
	show_process = (show_process === undefined) ? false : show_process;

	if( show_process ){
		serverProcessing(true);
	}

	if( server_loading_process[action_id] === false  ){
		server_loading_process[action_id] = true;

		var user_ajax = true;
		var iframe_id='iframe_'+parseInt(Math.random()*99999);
		var __form;
		if(  typeof data == 'object' ){
			__form = data;
			data = $(data).serialize();
			if( __form.attr('enctype')  == "multipart/form-data" ){
				__form.attr('target',iframe_id);
				var user_ajax = false;
			}
		}

		if( user_ajax ){

			$.ajax({
				url: urlsite + 'server/' + action,
		        dataType: "json",
				type: 'post',
				data: data,
				success: onServerResponse
			});

		}else{
			__form.attr('method','post');
			__form.attr('action',urlsite + 'server/' + action);
			var iframe = $('<iframe id="'+ iframe_id +'" name="'+ iframe_id +'"  style="display: none"></iframe>').insertAfter(__form);

			__form.submit();
			iframe.load(function(){
				var body = $(this).contents().find('body').text();
				console.log(body);
				var json = $.parseJSON(body);
				onServerResponse(json);
				//$(iframe).remove();
			});
		}

	}

	function onServerResponse(json){
		if( show_process ){
			serverProcessing(false);
		}

		server_loading_process[action_id] = false;
		if (json === null || json === '' || json === undefined) {
			modAlert('Error accessing server please try again.');
			return;
		}
		if (autoredirect) {
			if (json.logged_in === false) {
				window.location.href = urlsite+'login';
				return;
			}
		}
		if (callback !== undefined) {
			callback(json);
		}


	}


}


function addToWishList(product_id, product_name) {
	if (is_logged_in) {

		serverProcess('profile/addWishList', 'product_id='+product_id, function(json) {
			if (json.success) {
				modConfirm('Successfully added to wish list, would you like to see your wishslist?',function(){
					window.location.href = urlsite+'profile/wishlist';
				});
			} else {
				modConfirm('\nWould you like to see your wishslist?',function(){
					window.location.href = urlsite+'profile/wishlist';
				});
			}
		});
	} else {
		setCookie('add_wish_list', product_id, 1);
		window.location.href = urlsite + 'login?a=item/'+product_id+'/'+product_name;
	}
	return false;
}

function addToCart(product_id, qty, attributes,callback) {

	//set default
	qty = (qty === undefined) ? 1 : qty;
	var valid = true;
	// validate attributes selected
	$(attributes).each(function() {
		console.log(this)
		console.log($(this).val())
		if( $(this).val() === '' && valid === true ){
			modAlert('Please select '+ $(this).attr('data-name') );
			valid = false;
			return;
		}
	});

	if (valid) {

		var data = 'product_id=' + product_id +'&quantity='+qty+'&'+$(attributes).serialize();
		serverProcess('cart/addItem', data, function(json) {
			if (json.success) {
				var redir = true;
				if( callback != undefined ){
					redir = callback(json);
				}
				if(redir){
					window.location.href = urlsite + 'cart';
				}
			} else {
				if (json.error !== undefined) {
					modAlert(json.error);
				}
			}
		});

	}

	return false;

}

function subscription(action){
    serverProcess('profile/emailSubscription','action='+action,function(json){
        if( json.success ){
            alert('Successfully updated');
        }
    },true);
}

function setCookie(c_name, value, exdays)
{
	var exdate = new Date();
	exdate.setDate(exdate.getDate() + exdays);
	var c_value = escape(value) + ((exdays == null) ? "" : "; expires=" + exdate.toUTCString() + "; path=/");
	document.cookie = c_name + "=" + c_value;
}


function getCookie(c_name)
{
	var c_value = document.cookie;
	var c_start = c_value.indexOf(" " + c_name + "=");
	if (c_start == -1)
	{
		c_start = c_value.indexOf(c_name + "=");
	}
	if (c_start == -1)
	{
		c_value = null;
	}
	else
	{
		c_start = c_value.indexOf("=", c_start) + 1;
		var c_end = c_value.indexOf(";", c_start);
		if (c_end == -1)
		{
			c_end = c_value.length;
		}
		c_value = unescape(c_value.substring(c_start, c_end));
	}
	return c_value;
}

function checkCookie()
{
	var username = getCookie("username");
	if (username != null && username != "")
	{
		alert("Welcome again " + username);
	}
	else
	{
		username = prompt("Please enter your name:", "");
		if (username != null && username != "")
		{
			setCookie("username", username, 365);
		}
	}
}

var registeredFilterFunction = new Array();
var sortby_filter_variables = '';
var yipyy_current_page = {};

function yipyyPagination(page, options) {

		var _options = {
			id: null,
			container: null,
			action : null,
			page: null,
			data: null,
			load: null,
			callback: null
		}

		_options = $.extend({},_options,options);
		if(_options.load==null){
			_options.load = true;
		}

		if( yipyy_current_page[_options.id] == undefined ){
			yipyy_current_page[_options.id] = 1;
		}
		var _id = _options.id;

		var _data = null;
		if( typeof _options.data == "function" ){
			_data = _options.data();
		}else{
			_data = _options.data;
		}

		if( _options.load  ){
	        loadPage();
		}else{
			setLinks(null);
		}

		function _url(){

			var _url = '?';
			if( _data != null ){
				_url +=  _data +'&';
			}
			_url = _url +'page='+yipyy_current_page[ _options.id];
			return _url;
		}

    	function setLinks(response){
			options = $.extend({},options,{load:true});
    		$(_options.container).find('.pagination a.gotopage').click(function() {
            	yipyy_current_page[ _options.id] =  parseInt($(this).attr('data-page'));
                page(yipyy_current_page[ _options.id]);
                return false;
            });
            $( _options.container).find('.pagination a.page_back, .arrow_prev').click(function() {
            	prev();
                return false;
            });
            $( _options.container).find('.pagination a.page_next, .arrow_next').click(function() {
                next()
                return false;
            });
            if(_options.callback!=null){
            	_options.callback();
            }
    	}

    	function loadPage(){
    		loadServerContent( _options.container,  _options.action +_url(),setLinks);
    	}

    	function page(_newpage) {
    		options = $.extend({},options,{load:true,page:_newpage});
    		loadPage();
    	}

    	function prev() {
    		 yipyy_current_page[ _options.id]--;
            page(yipyy_current_page[ _options.id]);
    	}
    	function next() {
    		 yipyy_current_page[ _options.id]++;
             page(yipyy_current_page[ _options.id]);
    	}

    	return {
    		refresh:loadPage,page:page,next:next,prev:prev
    	};

}

function registerFilterFunction(func){
		registeredFilterFunction.push(func);
}

function updatePageResult(){
	sortby_filter_variables = $( "#sortResultsBy" ).serialize();
	$.each(registeredFilterFunction,function(i,func){ func(); })
}

// extend jquery
(function($){

	$.fn.disableInputs = function(){
		var p = this;
		$('input,textarea,select',p).attr('disabled',true);
	}
	$.fn.enableInputs = function(){
		var p = this;
		$('input,textarea,select',p).attr('disabled',false);
	}

})(jQuery);

var popupWindow = null;
function centeredPopup(url,winName,w,h,scroll){
	LeftPosition = (screen.width) ? (screen.width-w)/2 : 0;
	TopPosition = (screen.height) ? (screen.height-h)/2 : 0;
	settings =
	'height='+h+',width='+w+',top='+TopPosition+',left='+LeftPosition+',scrollbars='+scroll+',resizable'
	popupWindow = window.open(url,winName,settings)
}


// extend jquery
// pop up box (NOT window) + grey background
(function($){
	var bg_grey_el;
	function bg_grey_on (div){
		bg_grey_el = $("<div></div>");
		bg_grey_el
			 .css({
				position:"fixed",
				background:"#000",
				opacity:.5,
				top:0,
				bottom: 0,
				left:0,
				right: 0,
				zIndex: 1000
			  })
			 .appendTo("body");
	}
	function bg_grey_off(div){
		bg_grey_el.remove();
	}
	$.fn.mycenter = function () {
		this.css("position","fixed");
		this.css("z-index",1500);
		this.css("top", Math.max(0, (($(window).height() - $(this).outerHeight()) / 2) ) + "px");
		this.css("left", Math.max(0, (($(window).width() - $(this).outerWidth()) / 2) ) + "px");
		return this;
	}
	$.fn.mypopup = function () {
		if(this.is(':visible')){return;}
		bg_grey_on();
		this.fadeIn(500);
		this.mycenter();
	}
	$.fn.mypopup_close = function () {
		this.fadeOut(500, function(){ bg_grey_off(); });
	}
})(jQuery);
