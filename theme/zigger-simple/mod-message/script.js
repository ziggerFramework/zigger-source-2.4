ph_mod_message = {

    //
    // init
    //
    'init' : function() {

        this.send_msg(); // 새로운 메시지 발송
        this.cnum_allchk(); // 관리 checkbox 전체 선택

    },

    //
    // 새로운 메시지 발송
    //
    'send_msg' : function() {

        var $ele = {
            'form' : '',
            'sendpop' : '',
            'sendpopBG' : ''
        }

        // open
        $(document).on('click', '*[data-message-send]', function(e) {
            e.preventDefault();

            var to_mb_id = $(this).data('message-send');
            var reply_parent_hash = $(this).data('message-send-reply');

            $('<div id="message-send-bg" data-no-tab-index></div>').appendTo('body');
            $('<div id="message-send" data-no-tab-index></div>').appendTo('body');
            $ele.sendpop = $('#message-send');
            $ele.sendpopBG = $('#message-send-bg');

            $.ajax({
                'type' : 'GET',
                'url' : MOD_MESSAGE_DIR + '/controller/pop/message-send',
                'cache' : false,
                'data' : {
                    'to_mb_id' : to_mb_id,
                    'reply_parent_hash' : reply_parent_hash
                },
                'dataType' : 'html',
                'success' : function(data) {
                    $ele.sendpop.html(data).fadeIn(100);
                    $ele.sendpopBG.fadeIn(100);

                    //접근성 위해 layer로 focus 이동.
                    $ele.sendpop.find('.close').focus();
                }
            });
        });

        // close
        $(document).on('click', '#message-send .close', function(e) {
            e.preventDefault();

            //접근성 위해 layer띄운 요소로 focus 이동.
            $('*[data-tab-index='+PH_NOW_TABINDEX+']').focus();

            $ele.sendpop.remove();
            $ele.sendpopBG.remove();
        });

        // 쪽지 발송 후 처리
        get_message_after_send = function() {
            $ele.sendpop = $('#message-send');
            $ele.sendpopBG = $('#message-send-bg');

            zigalert('성공적으로 발송 되었습니다.', function() {
                $ele.sendpop.remove();
                $ele.sendpopBG.remove();
            });
        }
    },

    //
    // 관리 checkbox 전체 선택
    //
    'cnum_allchk' : function() {

        $(document).on('click', 'body.mod-message .cnum_allchk', function() {
            var chked = $(this).is(':checked');
    
            if (chked) {
                $('body.mod-message input[name="cnum[]"]').prop('checked', true);
    
            } else {
                $('body.mod-message input[name="cnum[]"]').prop('checked', false);
            }
        });

    },

}

$(function() {
    ph_mod_message.init();
})