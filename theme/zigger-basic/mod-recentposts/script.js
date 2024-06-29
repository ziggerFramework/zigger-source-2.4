ph_mod_recentposts = {

    //
    // init
    //
    'init' : function() {

        this.set_recent_write_submit(); // 게시글 작성시 레코드 기록
        this.set_recent_ctrl_submit(); // 게시글 관리

    },

    //
    // 레코드 기록
    //
    'set_recent_write_submit' : function() {
        
        // function overriding (write & modify)
        $('body.mod-board form[name=board-writeForm]').on({
            'submit' : function() {

                var returnAjaxSubmitOrg = returnAjaxSubmit;

                // function overriding
                returnAjaxSubmit = function($form, data) {
                    var trim_data = data.replace(/(<([^>]+)>)/ig, '');
                
                    if (data.indexOf('"success" :') === -1) {
                        zigalert('일시적인 오류 : ' + trim_data);
                        return false;
                    }
                
                    var first_char = data.replace(/^\s+|\s+$/g, '');
                    first_char = first_char.charAt(0);
                
                    if (first_char !== '[') {
                        zigalert('일시적인 오류 : ' + trim_data);
                        return false;
                    }
                
                    var json = eval(data);
                
                    var success = json[0].success;
                    var opt = json[0].opt[0];
                
                    switch (success) {
                        case 'error' :
                            valid.error($form,opt);
                            break;
                
                        case 'alert->location' :

                            ckeEditor_action();
                            var action = MOD_RECENTPOSTS_DIR + '/controller/set_recent/set_recent_write_submit';
                            $.ajax({
                                'type' : 'POST',
                                'url' : action,
                                'async' : false,
                                'cache' : false,
                                'data' : $('body.mod-board form[name=board-writeForm').serialize(),
                                'dataType' : 'html',
                                'success' : function(data){
                                    valid.success($form, success, opt);
                                }
                            });
                            break;

                        default :
                            zigalert('일시적인 오류 : ' + trim_data);
                    }
                    returnAjaxSubmit = returnAjaxSubmitOrg;
                }
                
            }
        });

        // function overriding (delete)
        $(document).off('click', 'body.mod-board #del-btn');
        $(document).on('click', 'body.mod-board #del-btn', function(e) {
            e.preventDefault();

            var $form = $('body.mod-board #board-readForm');
            var thisuri = $('#board-readForm input[name=thisuri]').val();

            zigconfirm("이 글을 삭제 하시겠습니까?", function(result) {
                if (result) {
                    var action = MOD_RECENTPOSTS_DIR + '/controller/set_recent/set_recent_write_submit';
                    $.ajax({
                        'type' : 'POST',
                        'url' : action,
                        'async' : false,
                        'cache' : false,
                        'data' : {
                            'board_id' : $form.find('input[name=board_id]').val(),
                            'read' : $form.find('input[name=read]').val(),
                            'wrmode' : 'delete'
                        },
                        'dataType' : 'html',
                        'success' : function(data){
    
                            $('body.mod-board #board-readForm').attr({
                                'method' : 'POST',
                                'action' : PH_DOMAIN + thisuri + '?mode=delete'
                            }).submit();
                        }
                    });
                }
            });
            
        });

    },

    //
    // 게시글 관리
    //
    'set_recent_ctrl_submit' : function() {
        
        $(document).off('click', 'body.mod-board #board_ctrpopForm #delete-btn');
        $(document).off('click', 'body.mod-board #board_ctrpopForm #copy-btn');
        $(document).off('click', 'body.mod-board #board_ctrpopForm #move-btn');

        // function overriding (contoll)
        get_mod_recentposts_ctrl_form = function() {

            var returnAjaxSubmitOrg = returnAjaxSubmit;

            // function overriding
            returnAjaxSubmit = function($form, data) {

                var trim_data = data.replace(/(<([^>]+)>)/ig, '');
            
                if (data.indexOf('"success" :') === -1) {
                    zigalert('일시적인 오류 : ' + trim_data);
                    return false;
                }
            
                var first_char = data.replace(/^\s+|\s+$/g, '');
                first_char = first_char.charAt(0);
            
                if (first_char !== '[') {
                    zigalert('일시적인 오류 : ' + trim_data);
                    return false;
                }
            
                var json = eval(data);
            
                var success = json[0].success;
                var opt = json[0].opt[0];
            
                switch (success) {
                    case 'error' :
                        valid.error($form,opt);
                        break;
            
                    case 'alert->location' :
                    case 'alert->reload' :

                        ckeEditor_action();
                        var action = MOD_RECENTPOSTS_DIR + '/controller/set_recent/set_recent_ctrl_submit';
                        $.ajax({
                            'type' : 'POST',
                            'url' : action,
                            'async' : false,
                            'cache' : false,
                            'data' : $('body.mod-board form#board_ctrpopForm').serialize(),
                            'dataType' : 'html',
                            'success' : function(data){
                                valid.success($form, success, opt);
                            }
                        });
                        break;

                    default :
                        zigalert('일시적인 오류 : ' + trim_data);
                }
                returnAjaxSubmit = returnAjaxSubmitOrg;
            }

            $('body.mod-board #board_ctrpopForm').submit();
        }

        // 삭제 버튼을 클릭하는 경우
        $(document).on('click', 'body.mod-board #board_ctrpopForm #delete-btn', function(e) {
            e.preventDefault();
            zigconfirm('정말로 삭제 하시겠습니까?\r\n선택된 게시물이 많은 경우 시간이 다소 소요될 수 있습니다.', function(result) {
                if (result) {
                    $('body.mod-board #board_ctrpopForm input[name=type]').val('del');
                    get_mod_recentposts_ctrl_form();
                }
            });
        });

        // 복사 버튼을 클릭하는 경우
        $(document).on('click', 'body.mod-board #board_ctrpopForm #copy-btn', function(e) {
            e.preventDefault();
            zigconfirm('답글은 복사 되지 않습니다.\n계속 진행 하시겠습니까?\n\n선택된 게시물이 많은 경우 시간이 다소 소요될 수 있습니다.', function(result) {
                if (result) {
                    $('body.mod-board #board_ctrpopForm input[name=type]').val('copy');
                    get_mod_recentposts_ctrl_form();
                }
            });
        });

        // 이동 버튼을 클릭하는 경우
        $(document).on('click', 'body.mod-board #board_ctrpopForm #move-btn', function(e) {
            e.preventDefault();
            zigconfirm('답글은 부모글 없이 단독으로 이동되지 않습니다.\n계속 진행 하시겠습니까?\n\n선택된 게시물이 많은 경우 시간이 다소 소요될 수 있습니다.', function(result) {
                if (result) {
                    $('body.mod-board #board_ctrpopForm input[name=type]').val('move');
                    get_mod_recentposts_ctrl_form();
                }
            });
        });
        
    }

}

$(function() {
    ph_mod_recentposts.init();
});