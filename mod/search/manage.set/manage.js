ph_mod_search_manage = {

    //
    // init
    //
    'init' : function() {

        this.search_result(); // 사이트맵 관리

    },

    //
    // 사이트맵 관리
    //
    'search_result' : function(functions) {

        var $wait_box = $('body.mod-search-manage #searchModifyForm .search-wait').clone();
        var list_arr = new Array;
        list_arr[0] = {
            'axis' : 'y',
            'stop' : function() {
                $('body.mod-search-manage #searchListForm input[name=type]').val('modify');
                list_refrs();
            }
        }
        var $list_ele = new Array;

        var get_sortable = function() {
            $list_ele[0] = $('body.mod-search-manage #searchListForm .sortable');
            $list_ele[0].sortable(list_arr[0]).disableSelection();
        }

        var list_charlen = function(str) {
            if (escape(str).length < 3) {
                var min = 4 - escape(str).length;
                var output = '';
                for (var i = 0; i < min; i++) {
                    output += '0';
                }
            }
            output = output + str;
            return output;
        }

        var request_sbm = function() {
            $('body.mod-search-manage #searchListForm').submit();

        }

        var list_reload = function() {
            $('body.mod-search-manage #searchListForm').load(MOD_SEARCH_DIR.replace(PH_DIR, PH_DIR + '/manage') + '/result/searchList', function(){
                get_sortable();
                $('body.mod-search-manage .searchbox').removeClass('with-ajax-cover');
            });
        }
        list_reload();

        var list_refrs = function() {
            var eqidx = new Array();
            var eqval = new Array();

            $('body.mod-search-manage .searchbox').addClass('with-ajax-cover');
            $('body.mod-search-manage #searchListForm').append('<div class="ajax-cover"></div>');

            get_sortable();

            $list_ele[0].find('input[name="caidx[]"]').each(function() {
                var $this = $(this);
                var depth = $this.data('depth');

                if ($this.data('depth') === 1) {
                    eqidx[0] = parseInt($this.index('body.mod-search-manage input[name="caidx[]"][data-depth=1]')) + 1;
                    eqval[0] = list_charlen(eqidx[0]);
                    if (eqidx[0]!=0) {
                        $this.val(eqval[0]);
                    }
                }
            });

            $('body.mod-search-manage input[name="idx[]"]').each(function() {
                if (!$(this).val()) {
                    $(this).addClass('search_new_added_ele');

                } else {
                    $(this).removeClass('search_new_added_ele');
                }
            });

            $('body.mod-search-manage #searchListForm input[name=new_caidx]').val($('.search_new_added_ele').eq(0).next('input[name="caidx[]"]').val());
            request_sbm();
            $('body.mod-search-manage #searchListForm input[name=type]').val('add');
        }

        var secc_modify = function() {
            alert('성공적으로 수정 되었습니다.');
            list_reload();
        }

        if (functions) {
            eval(functions+'()');
            return false;
        }

        $(document).on('click', 'body.mod-search-manage #searchListForm .add-1d', function(e) {
            e.preventDefault();
            var html = '<div class="st-1d"><h4><a href="#" class="modify-btn"><input type="hidden" name="idx[]" value="" /><input type="hidden" name="caidx[]" value="" data-depth="1" /><input type="hidden" name="org_caidx[]" value="" />새로운 통합검색 콘텐츠</a><i class="fa fa-trash-alt st-del del-1d"></i></h4></div>';
            $(html).hide().appendTo($('.sortable'));
            list_refrs();
        });

        $(document).on('click', 'body.mod-search-manage #searchListForm .del-1d', function(e) {
            e.preventDefault();
            if (!confirm('삭제하는 경우 복구할 수 없습니다.\n\n그래도 진행 하시겠습니까?')) {
                return false;
            }
            var $this = $(this);
            $this.parents('.st-1d').remove();
            $('body.mod-search-manage #searchListForm input[name=type]').val('modify');
            $('body.mod-search-manage #searchModifyForm').empty().append($wait_box);
            list_refrs();
        });

        $(document).on('click', 'body.mod-search-manage #searchListForm a.modify-btn', function(e) {
            e.preventDefault();
            var idx = $(this).find('input[name="idx[]"]').val();
            $('body.mod-search-manage #searchModifyForm').hide().load(MOD_SEARCH_DIR.replace(PH_DIR, PH_DIR + '/manage') + '/result/searchModify?idx=' + idx).fadeIn(100);
        });

    }

}

$(function() {
    ph_mod_search_manage.init();
});
