ph_mod_message = {

    //
    // init
    //
    'init' : function() {

        this.send_msg(); // 새로운 메시지 발송

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
            var reply_parent_idx = $(this).data('message-send-reply');

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
                    'reply_parent_idx' : reply_parent_idx
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
    }

}

$(function() {
    ph_mod_message.init();
})