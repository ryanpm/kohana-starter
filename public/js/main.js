var modAlertFunctions = {};

function modLogin(callback) {

    var _id = parseInt(Math.random()*99999999);
    modAlertFunctions['submit_'+_id] = function(){

        var dlg_username = $('#dlg_username_'+ _id ).val();
        var dlg_password = $('#dlg_password_'+ _id ).val();

        serverProcess({
            action:'user/auth',
            data:'login=1&user_username='+ dlg_username +'&user_password='+ dlg_password,
            callback:function(json){

                if(json.success){

                    is_guest = false;
                    $('._log_hide').hide();
                    $('._log_show').show();
                    $('._log_enable').attr('disabled',false);
                    $('._log_fullname').html( json.welcome_name );

                    closeColorBox();
                    try{
                        callback();
                    }catch(e){}

                }else{
                    if(json.html_error!==null){
                       $('#dlg_login_error_'+_id).html('<div class="error">'+json.html_error+'</div>');
                    }
                }

            }
        });

    };

    modAlertFunctions['cancel_'+_id] = function(){
        closeColorBox();
    };

    openColorBox({html:"<div><div style='background:#ee1f37;padding:5px'><img src='"+ urlbase +"images/warning.png' style='float:left;padding-right:3px' ><h4 style='padding:0px;margin:0px'>Please Login</h4></div>  <div style='padding:10px;height:60px' ><div id='dlg_login_error_"+_id+"'></div><div style='padding:3px'><div class='left'  style='width:80px'>Username:</div> <div class='input left'><img src='"+ urlbase +"images/bg_input01.png' class='left'><input  id='dlg_username_"+ _id +"' type='text' style='width:180px;' class='left' /><img src='"+ urlbase +"images/bg_input02.png' class='left'></div> </div> <div style='clear:both'></div> <div style='padding:3px'><div class='left' style='width:80px'>Password:</div> <div class='input left'><img src='"+ urlbase +"images/bg_input01.png' class='left'><input  id='dlg_password_"+ _id +"' type='password' style='width:180px;' class='left' /><img src='"+ urlbase +"images/bg_input02.png' class='left'></div></div> </div><div style='text-align:right;padding:5px;'><input type='button' onclick='modAlertFunctions.submit_"+ _id +"(this); ' value='   Submit   '  class='btn_red02' style='cursor:pointer'><input type='button' onclick='modAlertFunctions.cancel_"+ _id +"(); ' value='   Cancel   ' class='btn_red02' style='cursor:pointer;margin-left:10px'></div></div>",transition:'none',height:'220px',width:'450px',fixed:true,title:false});

    // $.ajax({
    //     url: urlsite + 'server/User/login',
    //     success:function(html){
    //         openColorBox({html:"<div><div style='background:#ee1f37;padding:5px'><img src='"+ urlbase +"images/warning.png' style='float:left;padding-right:3px' ><h4 style='padding:0px;margin:0px'>Please Login</h4></div>  <div style='padding:10px;height:60px' ><div id='dlg_login_error_"+_id+"'></div><div style='padding:3px'>Username: "+ username +"</div><div style='padding:3px'>Password: <input  id='dlg_password_"+ _id +"' type='password' /></div> </div><div style='text-align:right;padding:5px;'><input type='button' onclick='modAlertFunctions.submit_"+ _id +"(this); ' value='   Submit   ' style='cursor:pointer'><input type='button' onclick='modAlertFunctions.cancel_"+ _id +"(); ' value='   Cancel   ' style='cursor:pointer'></div></div>",transition:'none',height:'170px',width:'450px',fixed:true,title:false});
    //     }
    // });

}


function modLogout () {
    is_guest = true;
    $('._log_hide').show();
    $('._log_show').hide();
    $('._log_enable').attr('disabled',true);
    modLogin(connectToServer);
}


function modProgressBar(message){
    var _id = parseInt(Math.random()*99999999);
    openColorBox({html:"<div><div style='background:#ee1f37;padding:5px'><h4 style='padding:0px;margin:0px'>Progress</h4></div><div style='padding:10px;' >"+ message +"</div><div style='text-align:right;padding:5px;'><br/><span id='modProgressBar_percent_"+ _id +"'>0%</span><div style='width:100%;border:1px solid white;height:10px;'><div style='background:white;width:0%;height:10px' id='modProgressBar_bar_"+ _id +"' ></div></div></div></div>",transition:'none',height:'220px',width:'450px',fixed:true,overlayClose: false,title:false,onLoad:function(){
         $('#cboxClose').remove();
    }});
    function updateProgress(percent){
        $('#modProgressBar_percent_'+_id).html(parseInt(percent*100) +'%');
        $('#modProgressBar_bar_'+_id).css('width', parseInt(percent*100) +'%');
    }
    function closeProgressBar() {
        $.colorbox.close();
    }
    return {progress:updateProgress,close:closeProgressBar};
}

var modTimer_closer;
function closeColorBox() {
    if( $.colorbox === undefined )return;

    // modTasks.push(function(){
    //     $.colorbox.close();
    // });
    clearTimeout(modTimer_closer);
    modTimer_closer = setTimeout(function(){
        $.colorbox.close();
    },300);

}

var modTasks = [];
function openColorBox(options) {
    if( $.colorbox === undefined )return;
    clearTimeout(modTimer_closer);
    // modTasks.push(function(){
    //     $.colorbox(options);
    // });

    setTimeout(function(){
       $.colorbox(options);
    },200);
}

// function modTaskRunner(){

//     var operation = modTasks.shift();
//     console.log(operation);
//     console.log(typeof operation);
//     if( typeof operation == 'function' ){
//         operation();
//     }
// }

// setInterval( modTaskRunner, 1000);

function modAlert(message, callback) {
    // alert(message);

    var _id = parseInt(Math.random()*99999999);
    modAlertFunctions['ok_'+_id] = function(){
        closeColorBox();
        try{
            callback();
        }catch(e){}
    };
    if(message){
        message = message.replace(new RegExp('\n', 'g'),'<br/>');
    }

    openColorBox({html:"<div><div style='background:#ee1f37;padding:5px'><img src='"+ urlbase +"images/warning.png' style='float:left;padding-right:3px' ><h4 style='padding:0px;margin:0px'>Alert!</h4></div><div style='padding:10px;height:60px' >"+ message +"</div><div style='text-align:right;padding:5px;'><input type='button'   onclick='modAlertFunctions.ok_"+ _id +"(); ' value='   OK   ' class='btn_red02' style='cursor:pointer'></div></div>",transition:'none',height:'220px',width:'450px',fixed:true,title:false,onLoad:function(){
         $('#cboxClose').remove();
    }});

}

var serverProcessing_flag = false;
function serverProcessing(flag){
    if( flag ){

        openColorBox({html:"<div><div style='background:#ee1f37;padding:5px'><img src='"+ urlbase +"images/web_server.png' style='float:left;padding-right:3px' ><h4 style='padding:0px;margin:0px'>Processing!</h4></div><div style='padding-top:35px;height:20px;width:20px;padding-left:55px;float:left' >Loading...<br/><img id='ajax_loader' src='"+ urlbase +"images/ajax-loader.gif'></div><div style='float: left; font-size:12px;color:222222;padding-top:35px;'></div></div>",transition:'none',height:'210px',width:'270px',fixed:true,title:false,overlayClose: false,escKey:false
        });

        $('#_ajax_loader').insertAfter('#ajax_loader')

    }else{
        closeColorBox();
    }
    serverProcessing_flag = flag;

}

var modConfirmFunctions = {};
function modConfirm(message,callback_yes,callback_no) {

    var _id = parseInt(Math.random()*99999999);
    modConfirmFunctions['yes_'+_id] = function(){
        closeColorBox();
        if( callback_yes !== undefined ){
            callback_yes();
        }
    };
    modConfirmFunctions['no_'+_id] = function(){
        closeColorBox();
        if( callback_no !== undefined ){
            callback_no();
        }
    };

    message = message.replace(new RegExp('\n', 'g'),'<br/>');
    openColorBox({html:"<div><div style='background:#ee1f37;padding:5px'><img src='"+ urlbase +"images/warning.png' style='float:left;padding-right:3px' ><h4 style='padding:0px;margin:0px'>Confirm!</h4></div><div style='padding:10px;height:60px' >"+ message +"</div><div style='text-align:right;padding:5px;'><input name='yes' type='button' onclick='modConfirmFunctions.yes_"+ _id +"(); ' value='   Yes   ' class='btn_red02' style='cursor:pointer'><input name='no'  type='button' onclick='modConfirmFunctions.no_"+ _id +"();' value='   No   ' class='btn_red02' style='cursor:pointer;margin-left:10px'></div></div>",transition:'none',height:'220px',width:'450px',fixed:true});

}
var show_server_response_ids = {};
function showSuccess(container,message,delay,callback) {
    if(delay===undefined){
        delay = 3000;
    }
    var _id = $(container).attr('id');
    if( show_server_response_ids[_id] !== undefined ){
        clearTimeout(show_server_response_ids[_id]);
    }
    if( $.trim(message) !== '' ){
        $(container).html('<div class="success">'+ message +'</div>').show();
        show_server_response_ids[_id] = setTimeout(function(){
             $(container).hide();
             if( callback !== undefined ){
                callback();
             }
        },delay);
    }
}

function showError(container,message,delay,callback) {
    if(delay===undefined){
        delay = 3000;
    }
    var _id = $(container).attr('id');
    if( show_server_response_ids[_id] !== undefined ){
        clearTimeout(show_server_response_ids[_id]);
    }
    if( $.trim(message) !== '' ){
        $(container).html('<div class="error">'+ message +'</div>').show();
        show_server_response_ids[_id] = setTimeout(function(){
             $(container).hide('slide');
             if( callback !== undefined ){
                callback();
             }
        },delay);
    }
}


function getServerData(action,data,callback){

    $.ajax({
        url: urlsite + 'server/' + action,
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
        url: urlsite + 'server/' + action,
        success: function(response) {

            try{

                if( typeof response === 'string' ){
                    if( response.search('session_expired') !== -1 ){
                        is_guest = true;
                        $('._log_hide').show();
                        $('._log_show').hide();
                        $('._log_enable').attr('disabled',true);
                        modLogin(function(){
                            loadServerContent(container, action, callback, autoredirect)
                        });
                        return;
                    }
                }
            }catch(e){}

            $(container).html(response);
            if (callback !== undefined) {
                callback(response);
            }
        }

    });

}

var server_loading_process = {};
function serverProcess(options) {


    var defaults = {
        action:'',
        data:'',
        type:'post',
        dataType:'json',
        show_process:false,
        autoredirect:false,
        callback:null,
        onInputSuccess:null,
        onFileSuccess:null,
        fileOnly:false
    };
    options = $.extend({},defaults, options);


    var _parent = {
        setAction:function(action){
            console.log('new '+action);
            options.action = action;
        }
    };

    action_id = options.action.replace('/','_');
    if( server_loading_process[action_id] === undefined ){
        server_loading_process[action_id] = false;
    }

    server_loading_process[action_id] = false;
    if( server_loading_process[action_id] === false  ){
        server_loading_process[action_id] = true;
        connectToServer();
    }

    function connectToServer() {

        function sendInputs(data, callback) {
            if( $.trim(data) !== '' || options.type === 'get' ){
                $.ajax({
                    url: urlsite + 'server/' + options.action,
                    dataType: options.dataType,
                    type: options.type,
                    data: data,
                    success: callback
                });
            }
        }

        function sendFile(callback){

            var pb;
            if( options.show_process ){
                pb = modProgressBar('Uploading...');
            }
            // base on http://abandon.ie/notebook/simple-file-uploads-using-jquery-ajax
            var data = new FormData();
            $('input[type=file]', options.data ).each(function(){
                var files = $(this).get(0).files;
                var name = $(this).attr('name');
                $.each(files, function(key, value)
                {
                    data.append(name, value);
                });
            });

            // send some system indicator include in url var
            // to tell server this is file upload
            var _ind = '?';
            if( options.action.indexOf('?') !== -1 ){
                _ind  = '&';
            }
            _ind  += '_ajax_file_upd=1';

            $.ajax({
                url: urlsite + 'server/' + options.action + _ind,
                dataType: 'json',
                type: 'post',
                data: data,
                cache: false,
                processData: false, // Don't process the files
                contentType: false, // Set content type to false as jQuery will tell the server its a query string request
                xhr: function()
                {
                    var xhr = new window.XMLHttpRequest();
                    //Upload progress
                    xhr.upload.addEventListener("progress", function(evt){
                      if (evt.lengthComputable) {
                        var percentComplete = evt.loaded / evt.total;
                        //Do something with upload progress
                        // console.log('percent -> '+percentComplete);
                        if(pb) pb.progress(percentComplete);

                      }
                    }, false);
                    //Download progress
                    xhr.addEventListener("progress", function(evt){
                      if (evt.lengthComputable) {
                        var percentComplete = evt.loaded / evt.total;
                        //Do something with download progress
                        // console.log('percent -> '+percentComplete);
                      }
                    }, false);
                    return xhr;
                },
                success: function(response){

                    if(pb) pb.progress(1);
                    console.log('done pload');
                    setTimeout(function(){
                        if(pb) pb.close();
                        setTimeout(function(){
                            callback(response);
                        },200);
                    },1000);
                    if(options.onFileSuccess){
                        options.onFileSuccess(_parent, response);
                    }

                }
            });

        }

        if(  typeof options.data === 'object' ){

            var total_inputs =  $('input[type!=file],select,textarea',options.data).length;

            var total_files_to_upload = 0;
            $('input[type=file]', options.data ).each(function(){
                var files = $(this).get(0).files;
                var name = $(this).attr('name');
                $.each(files, function(key, value)
                {
                    total_files_to_upload++;
                });
            });

            if( options.fileOnly ){

                if( total_files_to_upload > 0 ){
                    sendFile(function(response){
                        onServerResponse(response);
                        if(pb) pb.close();
                    });
                }

            }else{

                var data = $('input[type!=file],select,textarea',options.data).serialize();
                if(data!==''){
                    sendInputs(
                        data,
                        function(response){
                            if(options.onInputSuccess){
                                options.onInputSuccess(_parent, response);
                            }
                            if(total_files_to_upload===0 || !response.success){
                                onServerResponse(response);
                            }else{
                                sendFile(onServerResponse);
                            }
                        }
                    );
                }else if( total_files_to_upload > 0 ){
                    sendFile(function(){
                        onServerResponse(response);
                        if(pb) pb.close();
                    });
                }

            }

        }else{

            if( options.show_process ){
                serverProcessing(true);
            }
            sendInputs(
                options.data,
                function(response){
                    if( options.show_process ){
                        serverProcessing(false);
                    }
                    onServerResponse(response);
                }
            );

        }

        // if( use_ajax ){

        // }else{

            // __form.attr('method','post');
            // __form.attr('action', urlsite + 'server/' + action);
            // var iframe = $('<iframe id="'+ iframe_id +'" name="'+ iframe_id +'"  style="display: none"></iframe>').insertAfter(__form);
            // __form.submit();
            // iframe.load(function(){

            //     var body = $(this).contents().find('body');
            //     try{
            //         var html = $.trim(body.html());
            //         html = html.replace(/\\/g,'\\\\');
            //         console.log( html );
            //         var match = html.match(/\{.*\}/);
            //         console.log( match );
            //         console.log( match[0] );
            //         var json = $.parseJSON( match[0] );
            //         onServerResponse(json);

            //     }catch(e){
            //         if( options.dataType === 'json' ){
            //             onServerResponse({content:body.html()});
            //         }else{
            //             onServerResponse(body.html());
            //         }
            //     }
            //     //$(iframe).remove();
            // });

        // }

    }

    function onServerResponse(result){

        try{
            if( typeof result === 'object' ){
                if( result ){
                    if( result.session_expired ){
                        modLogout ();
                        return;
                    }
                }

            }

        }catch(e){}

        try{
            if( typeof result === 'string' ){
                if( result.search('session_expired') !== -1 ){
                    if( result.session_expired ){
                        modLogout();
                        return;
                    }
                }
            }

        }catch(e){}

        server_loading_process[action_id] = false;
        if( options.dataType == 'json' ){
            if (result === null || result === '' || result === undefined) {
                modAlert('Error accessing server please try again.');
                return;
            }
            if (options.autoredirect) {
                if (result.logged_in === false) {
                    window.location.href = urlsite+'login';
                    return;
                }
            }
        }
        try{
            options.callback(result);
        }catch(e){}


    }


}

var ap_current_page = {};
function ajaxPagination(page, options) {

        var _options = {
            loader_img: null,
            id: null,
            html: null,
            container: null,
            action : null,
            page: null,
            data: null,
            load: true,
            callback: null
        };

        _options = $.extend({},_options,options);
        if(_options.load===null){
            _options.load = true;
        }

        if( ap_current_page[_options.id] === undefined ){
            ap_current_page[_options.id] = 1;
        }
        var _id = _options.id;


        var isloaded = false;
        if( _options.load  ){
            console.log(_options.container);
            console.log(_options.html);
           if( _options.html !== null ){
               $(_options.container).html(_options.html);
               setLinks();
           }else{
                loadPage();
            }
        }else{
            setLinks(null);
        }

        function _url(){
             var __url;
            if( post_data() === null || post_data() === '' ){
                __url = '?';
            }else{
                __url = '?'+ post_data() +'&';
            }
            __url = __url +'page='+ap_current_page[ _options.id];
            return __url;
        }

        function setLinks(response){

            options = $.extend({},options,{load:true});
            $(_options.container).find('.pagination a.gotopage').click(function() {
                ap_current_page[ _options.id] =  parseInt($(this).attr('data-page'));
                page(ap_current_page[ _options.id]);
                return false;
            });
            $(_options.container).find('.pagination a.page_back, .arrow_prev').click(function() {
                prev();
                return false;
            });
            $(_options.container).find('.pagination a.page_next, .arrow_next').click(function() {
                next();
                return false;
            });
            if(_options.callback!==null){
                _options.callback({pagenum:ap_current_page[ _options.id]});
            }
            serverProcessing(false);
        }

        function loadPage(){
            if( _options.loader_img !== null ){

                var w = $(_options.container).width();
                var h = $(_options.container).height();
                var cw = (w/2)-30;
                var ch = (h/2)-30;
                serverProcessing(true);
                //$('<div class="ajax-loader"><img style="left:'+ cw +'px;top:'+ ch +'px" src="'+ _options.loader_img +'"  /></div>').prependTo(_options.container);
            }
            loadServerContent( _options.container,  _options.action +_url(), setLinks);
            isloaded = true;
        }

        function page(_newpage) {
            options = $.extend({},options,{load:true,page:_newpage});
            loadPage();
        }

        function prev() {
             ap_current_page[ _options.id]--;
            page(ap_current_page[ _options.id]);
        }
        function next() {
             ap_current_page[ _options.id]++;
             page(ap_current_page[ _options.id]);
        }
        function load(callback) {
             page(1);
             if( typeof callback != 'undefined' ){
                callback();
             }
        }
        function fn_isloaded(){
            return isloaded;
        }
        function post_data(){
            if( typeof _options.data == "function" ){
                return _options.data();
            }else{
                return  _options.data;
            }
        }
        function currentPage () {
            return ap_current_page[_options.id];
        }
        return {
            refresh:loadPage,page:page,next:next,prev:prev,options:_options,isloaded:fn_isloaded,load:load,post_data:post_data,currentPage:currentPage
        };

}

 

function jsValidate(form, error_container){

    var valid = true;
    $('.validate',form).each(function(){

        if( !valid ) return;

        var label = $(this).attr('data-label') || $(this).attr('name');
        var value = $(this).val();
        if( $.trim($(this).val()) === '' ){
            showError( error_container, label+' must not be empty',60000);
            valid  = false;
        }else{

            if( $(this).attr('type') === 'file' ){

                var video_limit  = $(this).attr('data-validate-maxsize');
                if( $(this).attr('data-validate-maxsize') ){
                    if (window.FileReader) {

                        var input = $(this).get(0);
                        if (!input || !input.file || !input.files[0]) {
                        }else{
                            file = input.files[0];
                            if( (file.size / 1024) >  video_limit ){
                                showError( $('.error_container'), label + ' is more than '+video_limit+'KB.',60000);
                                valid = false;
                            }
                        }
                    }
                }

                if( $(this).attr('data-validate-ext') ){

                    var did_matched = false;
                    var allowed_ext = $(this).attr('data-validate-ext').split(',');
                    var val_parts =  value.split('.');
                    var i_ext = val_parts[ val_parts.length - 1  ];
                    allowed_ext.map(function(i,e){
                        if( i.toLowerCase() == i_ext.toLowerCase() && !did_matched  ){
                            did_matched = true;
                        }
                    });

                    if(!did_matched){
                        showError( $('.error_container'), label + ' accepts file type with '+ allowed_ext.join(', ') +'.',60000);
                        valid = false;
                    }
                }

            }
        }

    });

    return valid;

}
