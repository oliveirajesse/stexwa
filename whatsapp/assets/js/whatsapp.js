"use strict";
function WhatsappJs(){
    var self= this;
    var tmp_chat = [];
    var INSTANCE_ID = undefined;
    var ACCESS_TOKEN = undefined;
    var realtime = [];
    var change_battery = [];
    var change_state = [];
    if (typeof WHATSAPP_SERVER_API !== "undefined") {
        var API_PATH = WHATSAPP_SERVER_API;
        var socket = io (WHATSAPP_SERVER_API, { transport : ['websocket'] });
    }else{
        var API_PATH = undefined;
    }
    
    this.init= function(){
        self.fix();
        self.sidebar();
        self.scroll();
        self.inbox();
        self.auto_responder();
        self.bulk_schedule();
    }

    this.socket_message = function(key){
        if(realtime.indexOf( key ) === -1){
            realtime.push( key );
            socket.on(key, function(data) {
                var chat_id = $(".user-chat").attr("data-chat-id");
                if( INSTANCE_ID == data.instance_id && 
                    (data.message.from == chat_id || data.message.to == chat_id) && 
                    tmp_chat.indexOf( data.message.id.id ) === -1 &&
                    $(".wa-item[data-id='"+data.message.id.id+"']").length == 0
                ){
                    tmp_chat.push(  data.message.id.id );
                    var endpoint = $(".user-chat").attr("data-get-message");
                    var data = $.param({data : data.message, token: token});

                    if(endpoint.search("\\?") == -1){
                        endpoint = endpoint+"?instance_id="+INSTANCE_ID+"&access_token="+ACCESS_TOKEN;
                    }else{
                        endpoint = endpoint+"&instance_id="+INSTANCE_ID+"&access_token="+ACCESS_TOKEN;
                    }

                    $.post( endpoint, data, function(result) {
                        if(result.status == "success"){
                            $(".wa-body").append(result.content);
                        }else{

                        }

                        setTimeout(function(){
                            $('.wa-body.wa-scroll').scrollTop( $('.wa-body.wa-scroll').get(0).scrollHeight, -1 );
                            self.scroll();
                            Core.overplay("hide");
                        },100);
                    }, 'json')
                    .done(function() {})
                    .fail(function() {})
                    .always(function() {});
                }else{
                    tmp_chat = [];
                }
            });
        }
    }

    this.socket_change_battery = function(key){
        if(change_battery.indexOf( key ) === -1){
            change_battery.push( key );
            socket.on(key, function(data) {
                if(data.data.battery >= 20){
                    $(".wa-info .battery").removeClass("text-success text-danger").addClass("text-success").html( data.data.battery );
                }else{
                    $(".wa-info .battery").removeClass("text-success text-danger").addClass("text-danger").html( data.data.battery );
                }
            });
        }
    }

    this.socket_change_state = function(key){
        if(change_state.indexOf( key ) === -1){
            change_state.push( key );
            socket.on(key, function(data) {
                switch(data.data){
                    case 'CONNECTED':
                        $(".wa-info .state").removeClass("text-success text-danger text-warning text-info text-primary").addClass("text-success").html( Core.l("CONNECTED") );
                        break;
                    case 'CONFLICT':
                        $(".wa-info .state").removeClass("text-success text-danger text-warning text-info text-primary").addClass("text-warning").html( Core.l("CONFLICT") );
                        break;
                    case 'DEPRECATED_VERSION':
                        $(".wa-info .state").removeClass("text-success text-danger text-warning text-info text-primary").addClass("text-danger").html( Core.l("DEPRECATED_VERSION") );
                        break;
                    case 'OPENING':
                        $(".wa-info .state").removeClass("text-success text-danger text-warning text-info text-primary").addClass("text-info").html( Core.l("OPENING") );
                        break;
                    case 'PAIRING':
                        $(".wa-info .state").removeClass("text-success text-danger text-warning text-info text-primary").addClass("text-danger").html( Core.l("PAIRING") );
                        break;
                    case 'PROXYBLOCK':
                        $(".wa-info .state").removeClass("text-success text-danger text-warning text-info text-primary").addClass("text-danger").html( Core.l("PROXYBLOCK") );
                        break;
                    case 'SMB_TOS_BLOCK':
                        $(".wa-info .state").removeClass("text-success text-danger text-warning text-info text-primary").addClass("text-danger").html( Core.l("SMB_TOS_BLOCK") );
                        break;
                    case 'TIMEOUT':
                        $(".wa-info .state").removeClass("text-success text-danger text-warning text-info text-primary").addClass("text-warning").html( Core.l("TIMEOUT") );
                        break;
                    case 'TOS_BLOCK':
                        $(".wa-info .state").removeClass("text-success text-danger text-warning text-info text-primary").addClass("text-danger").html( Core.l("TOS_BLOCK") );
                        break;
                    case 'UNLAUNCHED':
                        $(".wa-info .state").removeClass("text-success text-danger text-warning text-info text-primary").addClass("text-danger").html( Core.l("UNLAUNCHED") );
                        break;
                    case 'UNPAIRED':
                        $(".wa-info .state").removeClass("text-success text-danger text-warning text-info text-primary").addClass("text-danger").html( Core.l("UNPAIRED") );
                        break;
                    case 'UNPAIRED_IDLE':
                        $(".wa-info .state").removeClass("text-success text-danger text-warning text-info text-primary").addClass("text-danger").html( Core.l("UNPAIRED_IDLE") );
                        break;
                }
            });
        }
    }

    this.update_scroll = function(){
        setTimeout(function(){
            $(".wa-scroll").getNiceScroll().resize();
        }, 300);

        setInterval(function(){
            $(".wa-scroll").getNiceScroll().resize();
        }, 3000);
    }

    this.scroll = function(){
        $(".wa-scroll").niceScroll({cursorcolor:"#dddbd1"});

        $(document).on("click", ".wa-reset-scrolll", function(){
            $(".wa-scroll").getNiceScroll().resize();
        });  
    }

    this.editor = function(){
        if($('.input-message').length > 0){
            if( $(".wa-editor .emojionearea").length == 0 ){
                var el = $(".input-message").emojioneArea({
                    hideSource: true,
                    useSprite: false,
                    pickerPosition    : "top",
                    filtersPosition   : "top",
                });

                setTimeout(function(){
                    $(".emojionearea-editor").niceScroll({cursorcolor:"#ddd"});
                }, 1000);

                $(".input-message").data("emojioneArea").on("keyup", function(editor, event) {
                    if (event.keyCode == 13) {
                        if(event.shiftKey){
                            event.stopPropagation();
                        } else {
                            self.send_message();
                        }
                    }
                });
            }
        }
    }

    this.download = function(base64, mimetype, filename){
        var a = document.createElement("a"); //Create <a>
        a.href = "data:"+mimetype+";base64," + base64; //Image Base64 Goes here
        a.download = filename; //File name Here
        a.click();
    }

    this.sidebar = function(){

        var window_width = $(window).width();
        if(window_width < 992){
            var wa_w_window = $(window).width();
            var wa_w_actions = $(".wa-actions").width();
            var wa_w_settings = $(".wa-settings").width();
            $(".wa-account-wrapper").width(wa_w_window - wa_w_settings - wa_w_actions);
        }else{
            $(".wa-account-wrapper").attr("style", "");
        }

        $(window).resize(function(){
            if(window_width < 992){
                var wa_w_window = $(window).width();
                var wa_w_actions = $(".wa-actions").width();
                var wa_w_settings = $(".wa-settings").width();

                $(".wa-account-wrapper").width(wa_w_window - wa_w_settings - wa_w_actions);
            }else{
                $(".wa-account-wrapper").attr("style", "");
            }
        });

    }

    this.tagsinput = function(el){
        $("."+el).tagsinput("items", {
          trimValue: true
        });
    }

    this.fix = function(){
        $("#wa-accounts").getNiceScroll().resize();
        $("#wa-pages").getNiceScroll().resize();

        $(window).resize(function(){
            var wa_w_window = $(window).width();
            var wa_w_actions = $(".wa-actions").width();
            var wa_w_settings = $(".wa-settings").width();

            $(".wa-account-wrapper").width(wa_w_window - wa_w_settings - wa_w_actions);
        });
    }

    this.inbox = function(){

        /*
        * RUN INSTANCE
        */
        $(document).on("click", ".wa-accounts .nav-item a", function(){
            var that = $(this);
            var instance_id = that.data("instance-id");
            var access_token = that.data("access-token");

            INSTANCE_ID = instance_id;
            ACCESS_TOKEN = access_token;

            Core.overplay();
            $.get( self.path() + "get/menu?instance_id="+INSTANCE_ID+"&access_token="+ACCESS_TOKEN, function( data ) {
                var data = $.parseJSON(data);
                if(data.status == "success"){
                    $(".wa-info .avatar").attr( "src", (data.data.avatar != undefined && data.data.avatar)?data.data.avatar:"https://ui-avatars.com/api/?name="+encodeURIComponent(data.data.pushname)+"&background=5578eb&color=fff&font-size=0.5&rounded=true" );
                    $(".wa-info .name").html( data.data.pushname +" | "+ data.data.me.user );
                    $(".wa-info").removeClass("d-none");
                    $(".whatsapp .subheader-main").hide();

                    $(".wa-pages").html(data.content);
                    $(".wa-account-wrapper").addClass("active");
                    $(".wa-back-account").addClass("active");
                    $('[data-toggle="tooltip"]').tooltip();
                    self.update_scroll();
                    self.socket_change_battery("change_battery/"+INSTANCE_ID);
                    self.socket_change_state("change_state/"+INSTANCE_ID);
                }else{
                    if(data.relogin != undefined && data.relogin){
                        $(".wa-accounts a[data-instance-id='"+INSTANCE_ID+"']").parents(".nav-item").remove();
                    }
                    Core.notify(data.message, "error");
                }
                Core.overplay("hide");
            }).done(function() {})
            .fail(function() {})
            .always(function() {});
        });
        /*
        * END RUN INSTANCE
        */

        $(document).on("click", ".wa-action-item", function(){
            var that = $(this);
            var page = $(this).attr("href");
            var redirect = $(this).data("redirect");
            var el_submenu = $(this).data("result-submenu");
            var el_content = $(this).data("result-content");
            var call_after = $(this).data("call-after");
            var remove = $(this).data("remove");
            var confirm = $(this).data("confirm");

            if(confirm != undefined){
                if(!window.confirm(confirm)) return false;
            }

            if(page != "#" && page !="javascript:void(0);"){
                Core.overplay();
                if(page.search("\\?") == -1){
                    var enpoint = page+"?instance_id="+INSTANCE_ID+"&access_token="+ACCESS_TOKEN;
                }else{
                    var enpoint = page+"&instance_id="+INSTANCE_ID+"&access_token="+ACCESS_TOKEN;
                }

                if(redirect != undefined){
                    Core.overplay("hide");
                    window.location.assign(enpoint);
                    return false;
                }

                $.get( enpoint, function( data ) {

                    $(".wa-action-item").parents("li.wa-submenu-item").removeClass("active");
                    that.parents("li.wa-submenu-item").addClass("active");
                    
                    if(data != "" && data){
                        var data = $.parseJSON(data);
                        if(data.status == "success"){
                            $("."+el_submenu).html(data.submenu);
                            $("."+el_content).html(data.content);
    
                            if( that.hasClass("wa-open-content") ){
                                $(".wa-content").addClass("active");
                            }else{
                                $(".wa-content").removeClass("active");
                            }
    
                            if( $(".wa-body").length > 0 ){
                                $('.wa-body.wa-scroll').scrollTop( $('.wa-body.wa-scroll').get(0).scrollHeight, -1 );
                            }
    
                            if( data.logout != undefined ){
                                $(".wa-accounts .nav-link[data-instance-id='"+INSTANCE_ID+"']").parents(".nav-item").remove();
                            }
    
                            if(that.hasClass("open-chat-item")){
                                var chat_id = that.data("chat-id");
                                self.socket_message(INSTANCE_ID+"/"+chat_id);
                            }

                            //Call After
                            if(call_after != undefined){
                                eval(call_after);
                            }

                            //Remove Element
                            if(remove != undefined){
                                that.parents('.'+remove).remove();
                            }
    
                            self.scroll();
                            self.editor();
                            Core.emojioneArea();
                            Caption.append();
                            Core.date();
                            File_Manager.uploadFile("#upload_media");
                            self.tagsinput("tagsinput");
                            $('[data-toggle="tooltip"]').tooltip();
                            self.caption();
                        }else{
                            if(data.relogin != undefined && data.relogin){
                                window.location.reload();
                            }
                            Core.notify(data.message, "error");
                        }
                    }
                    Core.overplay("hide");
                });
            }
            return false;
        });

        $(document).on("click", ".wa-btn-send-message", function(){
            self.send_message();
        });

        $(document).on("change", "#wa_send_media", function(){
            self.send_media();
        });

        $(document).on("click", ".wa-back-submenu", function(){
            $(".wa-content").removeClass("active");
        });

        $(document).on("click", ".wa-btn-open-content", function(){
            $(".wa-content").addClass("active");
        });

        $(document).on("click", ".wa-btn-open-content", function(){
            $(".wa-content").addClass("active");
        });

        $(document).on("click", ".wa-back-account", function(){
            $(".wa-account-wrapper").removeClass("active");
            $(".wa-back-account").removeClass("active");
            self.update_scroll();
            $(".wa-info").addClass("d-none");
            $(".whatsapp .subheader-main").show();
        }); 

        $(document).on("change", "input[name='chatbot_status']", function(){
            var data = $.param({token:token, instance_id: INSTANCE_ID, access_token: ACCESS_TOKEN });
            Core.overplay();
            $.post(self.path()+"whatsapp/chatbot_status", data, function(result){
                Core.overplay("hide");
            });
        }); 
    }

    this.send_message = function(){
        var chat_id = $(".user-chat").attr("data-chat-id");
        var endpoint = $(".user-chat").attr("data-endpoint");
        var type = 1;

        if(endpoint.search("\\?") == -1){
            endpoint = endpoint+"?instance_id="+INSTANCE_ID+"&access_token="+ACCESS_TOKEN;
        }else{
            endpoint = endpoint+"&instance_id="+INSTANCE_ID+"&access_token="+ACCESS_TOKEN;
        }

        var message = $(".input-message").data("emojioneArea").getText();
        var data = $.param({body : message, chat_id: chat_id, token: token, type: type});
        $(".input-message").data("emojioneArea").setText('');
        Core.overplay();
        $.post( endpoint, data, function(result) {
            if(result.status == "success"){
                $(".wa-body").append(result.content);
            }else{
                Core.notify(result.message, "error");
            }
            setTimeout(function(){
                $('.wa-body.wa-scroll').scrollTop( $('.wa-body.wa-scroll').get(0).scrollHeight, -1 );
                self.scroll();
            },100);

            Core.overplay("hide");
        }, 'json')
        .done(function() {})
        .fail(function() {})
        .always(function() {});
    }

    this.send_media = function(){

        var file = $("#wa_send_media")[0];
        if (!window.File || !window.FileReader || !window.FileList || !window.Blob) { 
            alert('The File APIs are not fully supported in this browser.'); 
            return; 
        } 

        if (!file) { 
            return; 
        } 
        else if (!file.files) { 
            return; 
        } 
        else if (!file.files[0]) { 
            return; 
        } 
        else { 
            var file = file.files[0]; 
            var file_size = file.size/1024/1024;
            if(file_size > 2){
                Core.notify("Only files smaller than 2MB are supported", "error");
                return false;
            }

            var fr = new FileReader(); 
            fr.readAsDataURL(file);
            fr.onload = function () {
                var chat_id = $(".user-chat").attr("data-chat-id");
                var endpoint = $(".user-chat").attr("data-endpoint");
                var message = $(".wa-text-message").val();
                var type = 2;
                $(".wa-editor .emojionearea-editor").html('');
                $('#wa_send_media').val('');
                var body = fr.result;
                var data = $.param({body : body, caption : message, chat_id: chat_id, filename: file.name, type: type, token: token});

                if(endpoint.search("\\?") == -1){
                    endpoint = endpoint+"?instance_id="+INSTANCE_ID+"&access_token="+ACCESS_TOKEN;
                }else{
                    endpoint = endpoint+"&instance_id="+INSTANCE_ID+"&access_token="+ACCESS_TOKEN;
                }

                $.post( endpoint, data, function(result) {
                    if(result.status == "success"){
                        $(".wa-body").append(result.content);
                    }else{
                        Core.notify(result.message, "error");
                    }

                    setTimeout(function(){
                        $('.wa-body.wa-scroll').scrollTop( $('.wa-body.wa-scroll').get(0).scrollHeight, -1 );
                        self.scroll();
                        Core.overplay("hide");
                    },100);
                    
                }, 'json')
                .done(function() {})
                .fail(function() {})
                .always(function() {});
            };
            fr.onerror = function (error) {
                return; 
            };
        } 
    }

    this.search_contact = function(except){
        $.get(self.path() + "get/search_contact?instance_id="+INSTANCE_ID+"&access_token="+ACCESS_TOKEN, function(data){
            var ms = $('#ms1').magicSuggest({
                placeholder: Core.l("Select contact"),
                allowFreeEntries: false,
                data: data,
                selectionPosition: 'bottom',
                selectionStacked: true,
                name: 'except',
            });
            ms.setValue(except);
        }, 'json');
    }

    this.auto_responder = function(){
        var old_text = "";
        var old_media = "";
        setInterval(function(){ 
            if( $(".autoresponder").length > 0 ){
                var d_none = true;
                var media = $(".autoresponder .file-manager input").val();
                if( (media != undefined && media != old_media) || (media != undefined && $(".autoresponder_preview .item-autoresponder-preview .img").html() == "")){
                    old_media = media;
                    var media = $(".autoresponder .file-manager input").val();
                    $(".autoresponder_preview .item-autoresponder-preview .img").html("<img src='"+media+"'>");
                    var d_none = false;
                }

                var el = $("textarea[name=caption]").emojioneArea();
                var text = el[0].emojioneArea.getText();
                text = self.nl2br(text); 
                if( (text != old_text) || $(".autoresponder_preview .item-autoresponder-preview .text").html() == ""){
                    old_text = text; 
                    $(".autoresponder_preview .item-autoresponder-preview .text").html(text);
                    var d_none = false;
                }

                if(d_none){
                    $(".item-autoresponder-preview").removeClass("d-none");
                }else{
                    $(".item-autoresponder-preview").addClass("d-none");
                }
            }
            $(".conversation-wrap.wa-scroll").getNiceScroll().resize();
        }, 3000);
    }

    this.bulk_schedule = function(){
        $(document).on("click", ".action-contact-group-import", function(){
            var that = $(this);
            var action = that.attr("href");

            $(".wa-contact-group-import-modal").remove();

            Core.ajax_post(that, action, { token: token }, function(result){
                $("body").append(result.content);
                $('#wa-contact-group-import-modal').modal('show');
                self.phone_numbers();
                setTimeout(function(){
                    self.scroll();
                }, 500);
            });

            return false;
        });

        $(document).on("click", ".wa-bulk-schedules .item .options a", function(){
            event.preventDefault();    
            var that           = $(this);
            var action         = that.attr("href");
            var id             = undefined;
            var data           = $.param({token:token, id: id});

            Core.ajax_post(that, action, data, function(result){
                if(result.status == "success"){
                    that.parents(".options").html(result.content);
                }
            });
            return false;
        });
    }

    this.reload_chat = function(page){
        if(page != "#" && page !="javascript:void(0);"){
            if(page.search("\\?") == -1){
                var enpoint = page+"?instance_id="+INSTANCE_ID+"&access_token="+ACCESS_TOKEN;
            }else{
                var enpoint = page+"&instance_id="+INSTANCE_ID+"&access_token="+ACCESS_TOKEN;
            }

            $.get( enpoint, function( data ) {
                if(data != "" && data){
                    var data = $.parseJSON(data);
                    if(data.status == "success"){
                        setTimeout(function(){
                            $(".wa-submenu-data").html(data.submenu);
                            self.scroll();
                            $('[data-toggle="tooltip"]').tooltip();
                        }, 1000);
                    }else{
                        Core.notify(data.message, "error");
                    }
                }
            });
        }
        return false;
    }

    this.reload_contact_group = function(){
        $(".wa-contact-group-menu").trigger("click");
    }

    this.reload_bulk_create = function(){
        $(".wa-contact-group-create a").trigger("click");
    }

    this.reload_bulk_schedules = function(result){
        if(result.status == "success"){
            $(".wa-contact-group-shedule a").trigger("click");
        }
    }

    this.reload_chatbot = function(result){
        if(result.status == "success"){
            $(".menu-item-chatbot").trigger("click");
        }
    }
    
    this.phone_numbers = function(){
        self.call_load_more_phone_numbers();
        self.ajax_load_phone_numbers(0);
    };

    this.call_load_more_phone_numbers = function(){
        var that = $('.ajax-load-log[data-load-type="scroll"]');
        var scrollDiv = that.data('scroll');
        if ( that.length > 0 )
        {
            $(scrollDiv).bind('scroll',function(){
                var _scrollPadding = 80;
                var _scrollTop = $(scrollDiv).scrollTop();
                var _divHeight = $(scrollDiv).height();
                var _scrollHeight = $(scrollDiv).get(0).scrollHeight;

                $(window).trigger('resize'); 
                if( _scrollTop + _divHeight + _scrollPadding >= _scrollHeight) {
                    self.ajax_load_phone_numbers();
                }

            });
        }
    };

    this.ajax_load_phone_numbers = function(page){
        var that = $('.ajax-load-log');
        var type = that.attr('data-type');
        var ids = that.data('id');

        if(type == undefined){
            var type = "";
        }else{
            var type = '/' + type
        }
        
        if(page != undefined){
            that.attr('data-page', 0);
            that.attr('data-loading', 0);
        }

        if ( that.length > 0 )
        {
            var action = PATH + 'whatsapp/ajax_load_contact_group/' + ids + type;
            var type = that.data('type');
            var page = parseInt(that.attr('data-page'));
            var loading = that.attr('data-loading');
            var data = { token: token, page: page };
            var scrollDiv = that.data('scroll');

            if ( loading == undefined || loading == 0 )
            {
                that.attr('data-loading', 1);

                $.ajax({
                    url: action,
                    type: 'POST',
                    dataType: 'html',
                    data: data
                }).done(function(result) {
                    if ( page == 0 )
                    {
                        that.html( result );
                    }
                    else
                    {
                        that.append( result );
                    }

                    if(result != ''){
                        that.attr('data-loading', 0);
                    } 

                    that.attr( 'data-page', page + 1);
                    
                    $(".nicescroll").getNiceScroll().resize();
                });
            }
        }
    };

    this.load = function(page){
        $.get( self.path() + "get/"+page+"?instance_id="+INSTANCE_ID+"&access_token="+ACCESS_TOKEN, function( data ) {
            $(".wa-pages").html(data);
            $(".wa-account-wrapper").addClass("active");
            $(".wa-back-account").addClass("active");
            Core.overplay("hide");
        });
    }

    this.ajax = function(url, callback){
        $.ajax({
            type: "GET",
            url: url,
            dataType:"json",
            success: function(res){
                if(callback != null){
                    callback.apply(this, [res]);
                }
            },
            error: function(res) {
                if(callback != null){
                    callback.apply(this, [{ "status": "error", "message": "" }]);
                }
            }
        });
    }

    this.caption = function(){
        //Review content
        if($(".post-message").length > 0){
            $(".post-message").data("emojioneArea").on("keyup", function(editor) {
                var data = editor.html();
                editor.parents(".caption").find('.count-word span').html( data.length );
                if(data != ""){
                    $(".post-preview .caption").html(data);
                }else{
                    $(".post-preview .caption").html('<div class="line-no-text"></div><div class="line-no-text"></div><div class="line-no-text w50"></div>');
                }
            });

            $(".post-message").data("emojioneArea").on("change", function(editor) {
                var data = editor.html();
                editor.parents(".caption").find('.count-word span').html( data.length );
                if(data != ""){
                    $(".post-preview .caption").html(data);
                }else{
                    $(".post-preview .caption").html('<div class="line-no-text"></div><div class="line-no-text"></div><div class="line-no-text w50"></div>');
                }
            });

            $(".post-message").data("emojioneArea").on("emojibtn.click", function(editor) {
                var data = $(".emojionearea-editor").html();
                editor.parents(".caption").find('.count-word span').html( data.length );
                if(data != ""){
                    $(".post-preview .caption").html(data);
                }else{
                    $(".post-preview .caption").html('<div class="line-no-text"></div><div class="line-no-text"></div><div class="line-no-text w50"></div>');
                }
            });
        }
    }

    this.nl2br = function(str, is_xhtml) {
        if (typeof str === 'undefined' || str === null) {
            return '';
        }
        var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
        return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
    }

    this.path = function(){
        return PATH+'whatsapp/';
    };
}

WhatsappJs= new WhatsappJs();
$(function(){
    WhatsappJs.init();
});

jQuery.loadScript = function (url, callback) {
    jQuery.ajax({
        url: url,
        dataType: 'script',
        success: callback,
        async: true
    });
}