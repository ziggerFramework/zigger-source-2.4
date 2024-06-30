ph_mod_board_manage = {

    //
    // init
    //
    'init' : function() {

        this.get_ctr_popup() // 관리 팝업
        this.use_notice_opt() // 공지사항 옵션 체크시 답변알림 옵션 & 카테고리 숨김
        this.get_view_comment(); // Comment
        this.get_delete(); // 글 삭제
        this.get_board_data_temporary(); // 글 삭제

    },

    //
    // 관리 팝업
    //
    'get_ctr_popup' : function() {

        var $ele = {
            'form' : '',
            'ctrpop' : '',
            'ctrpopBG' : ''
        }

        // open
        $(document).on('click', 'body.mod-board-manage #list-ctr-btn', function(e) {
            e.preventDefault();

            var $form = $('body.mod-board-manage #board-listForm');
            var cnum = $form.find(':checkbox[name="cnum[]"]:checked');

            if (cnum.length < 1) {
                zigalert('게시글을 한개 이상 선택해 주세요.');
                return false;
            }

            $('<div id="ctrpop-bg" data-no-tab-index></div>').appendTo('body.mod-board-manage ');
            $('<div id="ctrpop" data-no-tab-index></div>').appendTo('body.mod-board-manage ');
            $ele.ctrpop = $('body.mod-board-manage #ctrpop');
            $ele.ctrpopBG = $('body.mod-board-manage #ctrpop-bg');

            $.ajax({
                'type' : 'POST',
                'url' : MOD_BOARD_DIR + '/controller/pop/ctrl',
                'cache' : false,
                'data' : $form.serialize(),
                'dataType' : 'html',
                'success' : function(data) {
                    $ele.ctrpop.html(data).fadeIn(100);
                    $ele.ctrpopBG.fadeIn(100);

                    //접근성 위해 layer로 focus 이동.
                    $ele.ctrpop.find('.close').focus();
                }
            });
        });

        // close
        $(document).on('click', 'body.mod-board-manage #ctrpop .close', function(e) {
            e.preventDefault();

            // 접근성 위해 layer띄운 요소로 focus 이동.
            $('*[data-tab-index='+PH_NOW_TABINDEX+']').focus();

            $ele.ctrpop.fadeOut(100);
            $ele.ctrpopBG.fadeOut(100);
        });

        // 삭제 버튼을 클릭하는 경우
        $(document).on('click', 'body.mod-board-manage #board_ctrpopForm #delete-btn', function(e) {
            e.preventDefault();
            zigconfirm('정말로 삭제 하시겠습니까?\r\n선택된 게시물이 많은 경우 시간이 다소 소요될 수 있습니다.', function(result) {
                if (result) {
                    $('body.mod-board-manage #board_ctrpopForm input[name=type]').val('del');
                    $('body.mod-board-manage #board_ctrpopForm').submit();
                }
            });
        });

        // 복사 버튼을 클릭하는 경우
        $(document).on('click', 'body.mod-board-manage #board_ctrpopForm #copy-btn', function(e) {
            e.preventDefault();
            zigconfirm('답글은 복사 되지 않습니다.\n계속 진행 하시겠습니까?\r\n선택된 게시물이 많은 경우 시간이 다소 소요될 수 있습니다.', function(result) {
                if (result) {
                    $('body.mod-board-manage #board_ctrpopForm input[name=type]').val('copy');
                    $('body.mod-board-manage #board_ctrpopForm').submit();
                }
            });
        });

        // 이동 버튼을 클릭하는 경우
        $(document).on('click', 'body.mod-board-manage #board_ctrpopForm #move-btn', function(e) {
            e.preventDefault();
            zigconfirm('답글은 부모글 없이 단독으로 이동되지 않습니다.\n계속 진행 하시겠습니까?\r\n선택된 게시물이 많은 경우 시간이 다소 소요될 수 있습니다.', function(result) {
                if (result) {
                    $('body.mod-board-manage #board_ctrpopForm input[name=type]').val('move');
                $('body.mod-board-manage #board_ctrpopForm').submit();
                }
            });
        });

    },

    //
    // 공지사항 옵션 체크시 답변알림 옵션 & 카테고리 숨김
    //
    'use_notice_opt' : function() {

        var use_notice_opt = function($this) {
            var chked = $this.is(':checked');
            if (chked) {
                $('body.mod-board-manage input[name=use_email]').next('label').hide();
                $('body.mod-board-manage select[name=category]').prop('disabled', true);
        
            } else {
                $('body.mod-board-manage input[name=use_email]').next('label').show();
                $('body.mod-board-manage select[name=category]').prop('disabled', false);
            }
        }
        
        var $opt = $('body.mod-board-manage input[name=use_notice]');
        $opt.on({
            'click' : function() {
                use_notice_opt($opt);
            }
        });

        use_notice_opt($opt);
        
    },

    
    //
    // Comment
    //
    'get_view_comment' : function() {

        cmt_stat_mdf = false;

        // load
        view_cmt_load = function() {
            var comment_board_id = $('body.mod-board-manage #board-readForm input[name=board_id]').val();
            var comment_read = $('body.mod-board-manage #board-readForm input[name=read]').val();
            var comment_thisuri = $('body.mod-board-manage #board-readForm input[name=thisuri]').val();
            
            $('body.mod-board-manage #board-comment').load(MOD_BOARD_DIR + '/controller/comment/load?request=manage&board_id=' + comment_board_id + '&read=' + comment_read + '&thisuri=' + comment_thisuri,function() {
                if ($('.g-recaptcha').length < 1) {
                    return false;
                }
                var comment_timer;
                var comment_load = function(){
                    if (g_recaptcha_captcha_act > 0) {
                        g_recaptcha_captcha(1);
                    } else {
                        if (comment_timer) {
                            clearTimeout(comment_timer);
                        }
                        comment_timer = setTimeout(comment_load, 200);
                    }
                }
                comment_load();
            });
        }
        view_cmt_load();

        // Comment 작성
        $(document).on('click', 'body.mod-board-manage #commentForm .sbm', function(e) {
            e.preventDefault();

            var $form =  $('body.mod-board-manage #commentForm');
            $form.find('input[name=mode]').val('write');
            $form.find('input[name=cidx]').val('');
            $form.submit();
        });

        // Comment 삭제
        $(document).on('click', 'body.mod-board-manage #cmt-delete',function(e) {
            e.preventDefault();

            var $this = $(this);

            zigconfirm('댓글을 삭제 하시겠습니까?', function(result) {
                if (result) {
                    var $form =  $('body.mod-board-manage #commentForm');
                    var cidx = $this.data('cmt-delete');
                    $form.find('input[name=mode]').val('delete');
                    $form.find('input[name=cidx]').val(cidx);
                    $form.submit();
                }
            });
        });

        // Comment 답글 작성
        var comm_re_form_idx = 0;
        var $comm_re_form;

        $(document).on('click','body.mod-board-manage #cmt-reply', function(e) {
            e.preventDefault();

            if (cmt_stat_mdf) {
                $('body.mod-board-manage li.comm-list-li .comment > p').show();
                $('body.mod-board-manage #comm-re-form textarea[name=re_comment]').val('');
                cmt_stat_mdf = false;
            }

            var vis = $('> #comm-re-form', $(this).parents('li.comm-list-li')).is(':visible');

            if (comm_re_form_idx === 0) {
                $comm_re_form = $('body.mod-board-manage #comm-re-form').html();
                comm_re_form_idx++;
            }

            if (!vis) {
                $('body.mod-board-manage #comm-re-form').remove();
                $('body.mod-board-manage #commentForm input[name=cidx]').val($(this).data("cmt-reply"));
                $(this).parents('li.comm-list-li').append('<div id="comm-re-form">' + $comm_re_form + '</div>');
                $(this).parents('li.comm-list-li').find('#comm-re-form').show();

                if ($('.g-recaptcha').length > 0) {
                    g_recaptcha_re_captcha(1);
                }
                cmt_stat_val = 'reply';

            } else {
                $('body.mod-board-manage #comm-re-form').hide();
            }
        });

        // Comment 수정
        $(document).on('click', 'body.mod-board-manage #cmt-modify', function(e) {
            e.preventDefault();
    
            if (cmt_stat_mdf) {
                $('body.mod-board-manage li.comm-list-li').find('.comment').find('p').show();
                $('body.mod-board-manage #comm-re-form textarea[name=re_comment]').val('');
                cmt_stat_mdf = false;
            }
    
            var vis = $('.comment #comm-re-form', $(this).parents('li.comm-list-li')).is(':visible');
            var comment = $('.comment > p', $(this).parents('li.comm-list-li')).text();
    
            if (!vis) {
                $comm_re_form = $('body.mod-board-manage #comm-re-form').clone();
                $('body.mod-board-manage #comm-re-form').remove();
                $('body.mod-board-manage #commentForm input[name=cidx]').val($(this).data("cmt-modify"));
                $('.comment > p',$(this).parents('li.comm-list-li')).hide();
                $('.comment',$(this).parents('li.comm-list-li')).append($comm_re_form);
                $('#comm-re-form',$(this).parents('li.comm-list-li')).show();
                $('body.mod-board-manage #comm-re-form textarea[name=re_comment]').val(comment);
                cmt_stat_mdf = true;
                cmt_stat_val = 'modify';
    
            } else {
                $('body.mod-board-manage #comm-re-form').hide();
                $('.comment > p',$(this).parents('li.comm-list-li')).show();
                cmt_stat_mdf = false;
            }
        });
                
        //
        // Comment 답글 & 수정 Submit
        //
        $(document).on('click', 'body.mod-board-manage #comm-re-form .re_sbm, body.mod-board-manage #commentForm .re_sbm', function(e) {
            e.preventDefault();
            $('body.mod-board-manage #commentForm input[name=mode]').val(cmt_stat_val);
            $('body.mod-board-manage #commentForm').submit();
        });

    },

    //
    // 글 삭제
    //
    'get_delete' : function() {

        $(document).on('click', 'body.mod-board-manage #del-btn', function(e) {
            e.preventDefault();
            var thisuri = $('#board-readForm input[name=thisuri]').val();
            
            zigconfirm('이 글을 삭제 하시겠습니까?', function(result) {
                if (result) {
                    $('body.mod-board-manage #board-readForm').attr({
                        'method' : 'POST',
                        'action' : MOD_BOARD_DIR + '/controller/delete/delete'
                    }).submit();
                }
            });
        });

    },

    //
    // 게시글 임시저장
    //
    'get_board_data_temporary' : function() {

        var $ele = {
            'btn_wrap' : $('body.mod-board-manage #board-temporary-btnbox'),
            'add_btn' : $('body.mod-board-manage #board-temporary-btnbox .save-btn'),
            'load_btn' : $('body.mod-board-manage #board-temporary-btnbox .load-btn'),
            'form' : '',
            'temppop' : '',
            'temppopBG' : ''
        }
        
        // save
        $ele.add_btn.on('click', function(e) {
            e.preventDefault();

            $ele.form = $('body.mod-board-manage #board-writeForm');

            zigconfirm('현재 작성된 글을 임시저장 하시겠습니까?', function(result) {
                if (result) {

                    // temporary form으로 변조
                    $ele.form.attr({
                        'ajax-action' : '/mod/board/controller/write/write-temporary-submit?rewritetype=submit',
                        'id' : 'board-writeTemporaryForm', 
                        'name' : 'board-writeTemporaryForm', 
                    }).off().submit();

                    // 원본 form 으로 다시 돌려 놓음
                    $ele.form.attr({
                        'ajax-action' : '/mod/board/controller/write/write-submit?rewritetype=submit',
                        'id' : 'board-writeForm', 
                        'name' : 'board-writeForm', 
                    });
                }
            });
        });

        // load
        $ele.load_btn.on('click', function(e) {
            e.preventDefault();

            $ele.form = $('body.mod-board-manage #board-writeForm');

            var temp_hash = $ele.form.find('input:hidden[name="temp_hash"]').val();
            var board_id = $('body.mod-board-manage input[name=board_id]').val();

            $('<div id="temppop-bg" data-no-tab-index></div>').appendTo('body.mod-board-manage ');
            $('<div id="temppop" data-no-tab-index></div>').appendTo('body.mod-board-manage ');
            $ele.temppop = $('body.mod-board-manage #temppop');
            $ele.temppopBG = $('body.mod-board-manage #temppop-bg');

            $.ajax({
                'type' : 'GET',
                'url' : MOD_BOARD_DIR + '/controller/pop/temporary?request=manage',
                'cache' : false,
                'data' : {
                    'board_id' : board_id,
                    'temp_hash' : temp_hash
                },
                'dataType' : 'html',
                'success' : function(data) {
                    $ele.temppop.html(data).fadeIn(100);
                    $ele.temppopBG.fadeIn(100);

                    //접근성 위해 layer로 focus 이동.
                    $ele.temppop.find('.close').focus();
                }
            });
        });

        // close
        $(document).on('click', 'body.mod-board-manage #temppop .close', function(e) {
            e.preventDefault();

            // 접근성 위해 layer 띄운 요소로 focus 이동.
            $('*[data-tab-index='+PH_NOW_TABINDEX+']').focus();

            $ele.temppop.remove();
            $ele.temppopBG.remove();
        });

        // 임시저장글 선택시 처리
        $(document).on('click', 'body.mod-board-manage #temppop a.sbj', function(e) {
            e.preventDefault();

            var href = window.location.search;
            var temp_hash = $(this).data('temphash');
            href = href + '&temp_hash=' + temp_hash;

            zigconfirm('기존 작성중인 글은 초기화됩니다.\r\n임시저장 글을 적용 하시겠습니까?', function(result) {
                if (result) {
                    window.document.location.href = href;
                }
            });
        });

        // 임시저장글 삭제시 처리
        $(document).on('click', 'body.mod-board-manage #temppop a.remove-btn', function(e) {
            e.preventDefault();

            $ele.form = $('body.mod-board-manage #board_temporaryForm');
            var temp_hash = $(this).data('temphash');
            
            zigconfirm('임시글을 삭제 하시겠습니까?', function(result) {
                if (result) {
                    $ele.form.find('input[name="temp_hash"]').val(temp_hash).submit();
                }
            });
        });

        // 임시저장글 삭제 후 처리
        get_board_after_tempdata_delete = function(hash) {
            
            // 팝업에서 해당 데이터 제거
            $ele.temppop = $('body.mod-board-manage #temppop');
            $ele.temppop.find('a.remove-btn[data-temphash="' + hash + '"]').closest('tr').remove();

            if ($ele.temppop.find('a.remove-btn').length < 1) $ele.temppop.find('#list-nodata').show();

            // 글작성 화면에서 임시글 개수 갱신
            var nowCount = parseInt($ele.btn_wrap.find('.load-btn strong').text());
            $ele.btn_wrap.find('.load-btn strong').text(nowCount - 1);
            
        }
    },

}

$(function() {
    ph_mod_board_manage.init();
});