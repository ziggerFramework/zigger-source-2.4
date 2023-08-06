ph_theme_layout = {

    //
    // init
    //
    'init' : function() {

        this.gnb_auto_active(); // gnb auto active
        this.mo_slide(); // slideMenu for mobile
        this.mo_gnb(); // gnb for mobile
        
    },

    //
    // gnb auto active
    //
    'gnb_auto_active' : function() {

        if (typeof PH_CATEGORY_KEY !== 'undefined') {
            $('#gnb a[data-category-key=' + PH_CATEGORY_KEY + '], #mo-gnb a[data-category-key=' + PH_CATEGORY_KEY + ']').parents('li').addClass('on');
            $('#lnb a[data-category-key=' + PH_CATEGORY_KEY + '], #mo-gnb a[data-category-key=' + PH_CATEGORY_KEY + ']').parents('li').addClass('on');
        }
        
    },

    //
    // slideMenu for mobile
    //
    'mo_slide' : function() {

        var $ele = {
            'win' : $(window),
            'doc' : $(document),
            'slide' : $('#slide-menu'),
            'bg' : $('#slide-bg'),
            'btn' : $('#slide-btn'),
            'close' : $('#slide-close, #slide-menu-close')
        }

        //open & close
        $ele.btn.on({
            'click' : function(e) {
                e.preventDefault();
                var on = $(this).hasClass('on');

                if (!on) {
                    $ele.btn.addClass('on');
                    $ele.slide.addClass('on');
                    $ele.bg.addClass('on');

                } else {
                    $ele.btn.removeClass('on');
                    $ele.slide.removeClass('on');
                    $ele.bg.removeClass('on');
                }
            }
        });
        
        $ele.close.on({
            'click' : function(e) {
                e.preventDefault();

                $ele.btn.removeClass('on');
                $ele.slide.removeClass('on');
                $ele.bg.removeClass('on');
            }
        });

    },

    //
    // gnb for mobile
    //
    'mo_gnb' : function() {

        var $ele = {
            'win' : $(window),
            'doc' : $(document),
            'menu' : $('#mo-gnb a')
        }

        $ele.menu.each(function() {
            var $ul = $(this).parent('li').children('ul');
            var len = $ul.length;
            if (len > 0) {
                $(this).addClass('have-children');
            }
        });

        //open & close
        $ele.menu.on({
            'click' : function(e) {
                var $this = $(this);
                var $li = $this.parent('li');
                var $ul = $this.parent('li').children('ul');
                var $li_sib = $this.parent('li').siblings();
                var len = $ul.length;

                if (len > 0 && $this.attr('href') == $ul.find('a').eq(0).attr('href')) {
                    e.preventDefault();
                    $li.toggleClass('on');
                    $li_sib.removeClass('on');
                }
            }
        });

    }

}

$(function() {
    ph_theme_layout.init();
});
