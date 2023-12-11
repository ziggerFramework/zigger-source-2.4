//
// Ajax Validator
//
ajax_valid = {

	'init' : function() {
		this.action();
	},

	'action' : function() {
		var $ele = $('*[data-validt-event]');

		if ($ele.length > 0) {
			$ele.each(function() {
				var $this = $(this);
				var action = $this.data('validt-action');
				var evt = $this.data('validt-event');
				var group = $this.data('validt-group');
				var $validt = $('.validt[data-validt-group='+group+']');

				$validt.hide();
				$(document).on(evt, 'input[name="'+group+'"]', function(e){
					var chk_var = true;
					if ($(this).val() === '') {
						chk_var = false;
						$validt.hide();
					}

					if (chk_var) {
						$.ajax({
							'type' : 'POST',
							'url' : PH_DIR + action,
							'cache' : false,
							'data' : $('input[name="' + group+'"]').serialize(),
							'dataType' : 'html',
							'success' : function(data){
								if(data.indexOf('"success" :') === -1){
									alert('일시적인 오류 : '+data);
									return false;
								}
								var json = eval(data);
								var success = json[0].success;
								var opt = json[0].opt[0];

								switch (success) {
									case 'error' :
										$validt.show().text(opt.msg).removeClass('checked');
										break;

									case 'ajax-validt' :
										$validt.show().text(opt.msg).addClass('checked');
										break;

									default :
										alert('일시적인 오류 : '+data);
								}
							}
						});
					}
				});
			});
		}
	}

}
$(function(){
	ajax_valid.init();
});

//
// Ajax Form Validator
//
valid = {

	'error' : function($form, opt) {

		if (opt.input) {
			var $inp = $('*[name="' + opt.input + '"]', $form);
			var inp_tit = $inp.attr('title');
		}

		if ($.trim(opt.err_code) === 'ERR_NULL') {
			zigalert(inp_tit + ' : 입력해 주세요.');

		} else if ($.trim(opt.err_code) === 'NOTMATCH_CAPTCHA') {
			zigalert('Captcha(스팸방지)가 올바르지 않습니다.');

		} else if ($.trim(opt.msg) !== '') {
            if (typeof inp_tit != 'undefined' && typeof inp_tit != 'null') {
                zigalert(inp_tit + ' : ' + opt.msg);

            } else {
                zigalert(opt.msg);
            }

		} else {
			zigalert(inp_tit + ' : 올바르게 입력해 주세요.');
		}
		if (opt.input) {
			$inp.focus();
		}
	},

	'success' : function($form,success,opt) {

		switch (success) {
			case 'alert->location' :
				if ($.trim(opt.msg) !== '') {
					alert(opt.msg);
				}
				window.document.location.href = opt.location;
				break;

			case 'alert->reload' :
				if ($.trim(opt.msg) !== '') {
					alert(opt.msg);
				}
				window.document.location.reload();
				break;

			case 'callback':
				if ($.trim(opt.function) !== '') {
					eval(opt.function);
				}
				break;

			case 'callback-txt':
				if ($.trim(opt.element) !== '') {
					var tagName = $(opt.element).prop('tagName').toLowerCase();
					if (tagName === "input" || tagName === "textarea") {
						$(opt.element).val(opt.msg);
					} else {
						$(opt.element).html(opt.msg);
					}
				}
				break;

			case 'alert->close->opener-reload':
				opener.document.location.reload();
				window.close();
				break;

			case 'ajax-load':
				if ($.trim(opt.element) !== '') {
					$(opt.element).load(opt.document);
				}
				break;

			case 'none':
				return false;
				break;
		}
	}

}

//
// Return Ajax Submit
//
returnAjaxSubmit = function($form, data) {

    var trim_data = data.replace(/(<([^>]+)>)/ig, '');

	if (data.indexOf('"success" :') === -1) {
		alert('일시적인 오류 : ' + trim_data);
		return false;
	}

    var first_char = data.replace(/^\s+|\s+$/g, '');
    first_char = first_char.charAt(0);

    if (first_char !== '[') {
        alert('일시적인 오류 : ' + trim_data);
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
		case 'callback' :
		case 'callback-txt' :
		case 'alert->close->opener-reload' :
		case 'ajax-load' :
		case 'none' :
			valid.success($form, success, opt);
			break;
		default :
			alert('일시적인 오류 : ' + trim_data);
	}
}

//
// Plugin : CKEditor
//
function ckeEditor_action() {

	$('textarea[ckeditor]').each(function() {
		var t_id = $(this).attr('id');
		var t_cont = CKEDITOR.instances[t_id].getData();
		$(this).val(t_cont);
	});

}

//
// Ajax Submit
//
ajaxSubmit = {

	'init' : function($form) {
		this.action($form);
	},

	'action' : function($form) {
        ckeEditor_action();

		var ajaxAction = $form.attr('ajax-action');

        $.ajax({
            'type' : 'POST',
            'url' : ajaxAction,
            'cache' : false,
            'async' : true,
            'data' : $form.find('input, select, textarea').serialize(),
            'dataType' : 'html',
            'beforeSend' : function() {
                $form.find('button,:button').attr('disabled', true);
            },
            'success' : function(data) {
                returnAjaxSubmit($form,data);
                $form.find('button, :button').attr('disabled', false);
            }
        });
	}

}

//
// Ajax Submit With File
//
ajaxFileSubmit = {

	'init' : function($form) {
		this.action($form);
	},

    'action' : function($form) {
        ckeEditor_action();

        var ajaxAction = $form.attr('ajax-action');
        var formData = new FormData();

        $form.find('input, select, textarea').each(function() {
            var ele_name = $(this).attr('name');
            var ele_type = $(this).attr('type');

            switch (ele_type) {
                case 'file' :
                    formData.append(ele_name, $(this)[0].files[0]);
                    break;

                case 'checkbox' :
                case 'radio' :
                    if ($(this).prop('checked') == false) {
                        return;
                    }

                default :
                    formData.append(ele_name, $(this)[0].value);
            }
        });

		var post_max_size = 0;
		
		$form.find('input:file').each(function() {
			var fileInput = $(this);
			if (fileInput[0].files.length > 0) {
				post_max_size += (fileInput[0].files[0].size / 1024 / 1024);
			}
        });
		
		if (PH_POST_MAX_SIZE > 0 && post_max_size > PH_POST_MAX_SIZE) {
			alert('서버의 첨부 가능한 허용 용량을 초과하였습니다.');
			return false;
		}

		$.ajax({
            'type' : 'POST',
            'url' : ajaxAction,
            'cache' : false,
            'async' : true,
            'data' : formData,
            'contentType' : false,
            'processData' : false,
            'dataType' : 'html',
            'beforeSend' : function() {
                $form.find('button,:button').attr('disabled', true);
				$form.append('<div class="form-progress"><div class="track"><div class="bar"></div></div></div>');
            },
            'success' : function(data) {
                returnAjaxSubmit($form, data);
                $form.find('button, :button').attr('disabled', false);
				$form.find('.form-progress').remove();
            },
			'xhr' : function() {
				// make progress
				var xhr = new window.XMLHttpRequest();

				xhr.upload.addEventListener('progress', function(e) {
					if (e.lengthComputable) {
						var percentComplete = (e.loaded / e.total) * 100;

						$form.find('.form-progress .bar').css({
							'width' : percentComplete + '%'
						});
					}
				}, false);

				return xhr;
			}
        });
    }

}

//
// Ajax Form을 본문에서 찾아 Form setting
//
setAjaxForm = {

	'init' : function() {
		this.action();
	},

	'action' : function() {
		var $ele = {
			'doc' : $(document)
		}

		$ele.doc.on('submit', 'form[ajax-action]', function(e) {
			var ajaxType = $(this).attr('ajax-type');

			switch (ajaxType) {
				case 'multipart' :
					e.preventDefault();
					ajaxFileSubmit.init($(this));
					break;

				case 'html' :
					e.preventDefault();
					ajaxSubmit.init($(this));
					break;
			}
		});
	}

}

$(function() {
	setAjaxForm.init();
});

//
// Cookie
//
function setCookie(name, value, expiredays) {

	var todayDate = new Date();
	if (expiredays === null) {
		expiredays = 30;
	}
	// Cookie 저장 시간 (1Day = 1)
	todayDate.setDate(todayDate.getDate() + expiredays);
	document.cookie = name + '=' + escape( value ) + '; path=/; expires=' + todayDate.toGMTString() + ';'

}

function getCookie(name) {

    var nameOfCookie = name + '=';
    var x = 0;

    while (x <= document.cookie.length) {
        var y = (x + nameOfCookie.length);
        if (document.cookie.substring(x,y) === nameOfCookie) {
            if ((endOfCookie = document.cookie.indexOf(';', y)) === -1) {
                endOfCookie = document.cookie.length;
            }
            return unescape(document.cookie.substring(y, endOfCookie));
        }
        x = document.cookie.indexOf(' ', x) + 1;
        if (x === 0) {
            break;
        }
    }
    return '';

}

//
// Before confirm
//
formBeforeConfirm = {

    'init' : function() {
        this.action();
    },

    'action' : function() {
        $(document).on('click', '*[data-form-before-confirm]', function(e) {
            e.preventDefault();

            var $this = $(this);
            var val = $(this).data('form-before-confirm');
            var val_exp = val.split('=>');

            for (var i=0; i < val_exp.length; i++) {
                val_exp[i] = val_exp[i].replace(/^\s+|\s+$/g, '');
            }
			
			var confirmed = (val_exp[0]) ? confirm(val_exp[0]) : true;

            if (confirmed) {
                var $form = $this.closest('form')
                var $inp = new Array;
                var org_val = new Array;

                for (var i = 1; i < val_exp.length; i++) {
                    $inp[i] = $('input[name="' + val_exp[i].split(':')[0] + '"]' ,$form);
                    org_val[i] = $('input[name="' + val_exp[i].split(':')[0] + '"]').val();
                    $inp[i].val(val_exp[i].split(':')[1])
                }

                $form.closest('form').submit();

                for (var i = 1; i < $inp.length; i++) {
                    $inp[i].val(org_val[i]);
                }
            }
        });
    }

}

$(function(){
    formBeforeConfirm.init();
});

//
// Setting Tabindex & Handling Enter Key
//
set_elements_tabindex = function() {

	// Tabindex data attributes
    $('button, input[type!="hidden"], a, *[tabindex]').each(function(i) {
        if ($(this).closest('*[data-no-tab-index]').length > 0) {
            return;
        }

        $(this).attr('data-tab-index', i);
    });

	// Enter key click on lebel
	$(document).on('keydown', 'label', function(e) {
		var $this = $(this);
		if (e.keyCode == '13') {
			e.preventDefault();
			if ($this.prev('input').length > 0) $this.prev('input').click();
			if ($this.next('input').length > 0) $this.next('input').click();
			if ($this.find('input').length > 0) $this.find('input').click();
		}
	});

}
make_elements_tabindex = {
    'init' : function() {
        this.action();
    },
    'action' : function() {

        set_elements_tabindex();
        $(document).on('click', 'button, input, a, *[tabindex]', function() {
            var tab_index = $(this).data('tab-index');

            if (tab_index) {
                PH_NOW_TABINDEX = tab_index;
            }
        });
    }
}

$(function() {
    make_elements_tabindex.init();
    setInterval(set_elements_tabindex, 100);
});

//
// Zigger alert
//
zigalert = function(msg) {

    var $ele = {
        'wrap' : $('#zig-alert-wrap'),
        'alert' : $('<div id="zig-alert"><p><i class="fa fa-exclamation-circle"></i>' + msg + '</p></div>')
    }

    if ($ele.wrap.length < 1) {
        $wrap = $('<div id="zig-alert-wrap"></div>');
        $wrap.appendTo('body');
        $ele.wrap = $wrap;
    }

    $ele.alert.prependTo($ele.wrap).delay(3000).queue(function() {
        $(this).remove();
    });

    $ele.alert.on({
        'click' : function(e) {
            e.preventDefault();
            $(this).remove();
        }
    });
	
}
