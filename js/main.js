jQuery(document).ready(function ($) {


    $('body').on('click', '.dialog-close-button', function (e) {
        e.preventDefault();
        $(this).closest('.dialog-widget').fadeOut();
    });
    //remove websitepolicies css after load
    var mutationObserver = new MutationObserver(function (mutations, observer) {
        mutations.forEach(function (mutation) {
            if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                mutation.addedNodes.forEach((element) => {
                    if (element.nodeName === 'LINK' && element.getAttribute('href').indexOf('websitepolicies') !== -1) {
                        observer.disconnect();
                        element.remove();
                    }
                })
            }
        });
    });

    mutationObserver.observe(document.head, {
        childList: true,
    });

    $('.multiple-choice').each((i, item) => {
        $(item).find('.grade').text($(item).val());
    });


    //hide notices
    setTimeout(() => {
        if ($('.woocommerce-notices-wrapper').length > 0) $('.woocommerce-notices-wrapper').fadeOut();
    }, 15000)

    let get_name = $('.elementor-item-anchor span')[0],
        create_clone = $(get_name).clone(),
        hide_mess = () => {
            let get_mess = $('.woocommerce-notices-wrapper');

            if (get_mess.length > 0) setTimeout(() => {
                $(get_mess).hide();
            }, 15000);
        };


    //clone geader for mobyle menu
    setTimeout(() => {
        $(create_clone).addClass('span-clone-name');
        $('.header-menu-1').prepend(create_clone);

        $('.header-menu-1').on('click', () => {
            $('.dropdown-arrow-custom').toggleClass('dropdown-arrow-custom_rout');
            hide_mess();
        });

        $('.header-menu-0').on('click', () => {
            $('.dropdown-arrow-custom').removeClass('dropdown-arrow-custom_rout');
            hide_mess();
        });
    }, 10);

    //custom events for checkout
    $(document.body).on('updated_checkout', function (data) {
        if ($('.woocommerce-terms-and-conditions-bg-custom').length > 0) {
            $('.woocommerce-terms-and-conditions-link').on('click', function (e) {
                e.preventDefault();
                $('.woocommerce-terms-and-conditions-bg-custom').fadeIn().css('display', 'flex');
                $('.woocommerce-terms-and-conditions-custom').fadeIn('slow');
                $('body').css('overflow', 'hidden');
            });
        }
        $('.close-term-cond').on('click', function () {
            $('.woocommerce-terms-and-conditions-bg-custom').fadeOut('slow', function () {
                $(this).css('display', 'none')
            });
            $('.woocommerce-terms-and-conditions-custom').fadeOut('slow');
            $('body').css('overflow', 'auto');
        });

        if ($('.woocommerce-terms-and-conditions-custom span').length == 0) {
            var span = document.createElement("span");
            span.innerHTML = "<svg width='12' height='12' viewBox='0 0 12 12' fill='none' xmlns='http://www.w3.org/2000/svg'> <path d='M11.6466 9.95266C12.1153 10.4214 12.1153 11.1807 11.6466 11.6494C11.4141 11.8838 11.1066 12 10.7992 12C10.4917 12 10.185 11.8828 9.95097 11.6485L5.99953 7.69909L2.04847 11.6475C1.81411 11.8838 1.50701 12 1.19991 12C0.892805 12 0.586079 11.8838 0.351535 11.6475C-0.117178 11.1788 -0.117178 10.4195 0.351535 9.95078L4.30373 5.99859L0.351535 2.04828C-0.117178 1.57956 -0.117178 0.820248 0.351535 0.351535C0.820248 -0.117178 1.57956 -0.117178 2.04828 0.351535L5.99953 4.3056L9.95172 0.35341C10.4204 -0.115303 11.1798 -0.115303 11.6485 0.35341C12.1172 0.822123 12.1172 1.58144 11.6485 2.05015L7.69627 6.00234L11.6466 9.95266Z' fill='#333333'/></svg>";
            $('.woocommerce-terms-and-conditions-custom').prepend(span);
        }
    });

    //add style tittl
    $('.wp-block-sensei-lms-collapsible, .wp-block-sensei-lms-course-outline__arrow').addClass('collapsed');
    $('.wp-block-sensei-lms-course-outline').find('.wp-block-sensei-lms-course-outline-module__progress-indicator:not(.completed)').next().removeClass('collapsed');
    $('.wp-block-sensei-lms-course-outline').find('.wp-block-sensei-lms-course-outline-module__progress-indicator:not(.completed)').parent().next().removeClass('collapsed');

    // Menu hide
    $('.elementor-menu-toggle').each((i, el) => {
        $(el).addClass(`header-menu-${i}`);
    });

    $(document).on('click', '.header-menu-0', function () {
        clickResTrue('header-menu-0', 'header-menu-1');
    });

    $(document).on('click', '.header-menu-1', function () {
        clickResTrue('header-menu-1', 'header-menu-0');
    });

    let clickResTrue = (classFirst, classSecond) => {
        if ($(`.${classFirst}`).hasClass(`${classFirst}_active`)) {
            $(`.${classFirst}`).removeClass(`${classFirst}_active`);
        } else {
            $(`.${classFirst}`).addClass(`${classFirst}_active`);
            $(`.${classSecond}_active`).removeClass('elementor-active');
            $(`.${classSecond}_active`).removeClass(`${classSecond}_active`);
        }
    }

    // Woocommerce account details disabled btn
    $('.woocommerce-EditAccountForm input').keyup(function () {
        if ($(this).val() !== '') {
            $('.my-acc-btn .woocommerce-Button').removeAttr('disabled');
        } else {
            $('.my-acc-btn .woocommerce-Button')[0].setAttribute("disabled", "disabled");
        }
    });

    // Accordion
    if ($('.course-progress-module').length > 0) {
        $('.course-progress-module').append($('<i class="sidebar-arrow"></i>'));
    }

    $('.course-progress-module').click(function () {
        findnext(this);

        let arrow = $(this).find('.sidebar-arrow');
        arrow.toggleClass('sidebar-arrow-close');
    });

    //next lesson
    function findnext(elem) {
        var next = $(elem).next();

        if (next.hasClass('course-progress-lesson')) {
            next.slideToggle();
            findnext(next);
        } else {
            return false;
        }
    }

    //btn for video
    $('.single-lesson .widget-area, .single-course section.wp-block-sensei-lms-course-outline').append('<div class="button-bg"><span class="btn-width"><svg width="15" height="24" viewBox="0 0 15 24" fill="none" xmlns="http://www.w3.org/2000/svg">\n' +
        '<path d="M0 2.544L9.24679 12L0 21.456L2.48772 24L14.2222 12L2.48772 0L0 2.544Z" fill="#B7B7B7"/>\n' +
        '</svg>\n</span></div>');

    $(document).find('.video').append('<span id="actin-width-video" class="full-width-video"><i class="icon-full-width-video"></i></span>');

    //full width video
    $(document).on('click', '.button-bg', function () {
        $('body').toggleClass('full-width-contener');
        $('.widget-area').toggleClass('sidebar-full-width');
        $('.wp-block-sensei-lms-course-outline').toggleClass('sidebar-full-width');
        $('#main').toggleClass('content-full-width');
        $('.entry-content').toggleClass('content-full-width');
        $('.btn-width svg').toggleClass('rotated');
    });

    var secondary = $('#secondary');

    $(secondary).clone().addClass('clone').appendTo("#main");

    var secondary_clone = $('#secondary.clone');

    $(document).on('click', '#actin-width-video', function () {
        if ($('body').hasClass('full-width-contener-video')) {
            $('.icon-full-width-video').toggleClass('icon-full-width-video_close');
            $('body').removeClass('full-width-contener-video');
        } else {
            $('body').addClass('full-width-contener-video');
            $('.icon-full-width-video').toggleClass('icon-full-width-video_close');
        }
    });

    $(".wp-block-sensei-lms-course-outline__arrow").click(function () {
        $(".wp-block-sensei-lms-collapsible, .wp-block-sensei-lms-course-outline__arrow").addClass('collapsed');
    });

    if ($('.course .sensei-message').length > 0) {
        setTimeout(function () {
            $('.sensei-message').animate({
                'opacity': '0'
            }, 2000);
        }, 2000);
    }

    if ($('.sensei #content #main .sensei-message.info').length > 0) {
        $('.sensei #content #main .sensei-message.info').append('<span class="close_mess_btn"></span>');
    }

    //close sensei mess lesson
    $(document).on('click', '.close_mess_btn', function () {
        $(this).parent().fadeOut();
    });

    if ($('.lesson #private_message')) {
        $('#private_message').appendTo('.sensei-buttons-container');
        let cur_div = $('.sensei-buttons-container').closest('.wp-block-group');
        if (cur_div.length > 0) {
            $(cur_div).after($('.sensei-message'));
        } else {
            let cur_div = $('.sensei-buttons-container').closest('.sensei-block-wrapper');
            $(cur_div.prevObject[0]).after($('.sensei-message'));
        }
    }

    //show only if have pass
    let pass_detect = function (formClass, field1_class, field2_class, btn_class) {
        $(formClass).on('change keyup', function () {
            let field1 = $(field1_class).val();
            let field2 = $(field2_class).val();
            if (field1.length > 0 && field2.length > 0) {
                $(btn_class).removeAttr('disabled', 'disabled');
                $(btn_class).removeClass('button_hide').removeClass('disabled');
            } else {
                $(btn_class).attr('disabled', 'disabled');
                $(btn_class).addClass('button_hide').addClass('disabled');
            }
        });
    }

    pass_detect('.woocommerce-form-login', '#username', '#password', '.button');
    pass_detect('#loginform', '#sensei_user_login', '#sensei_user_pass', '.sensei-login-submit .button');
    pass_detect('.woocommerce-ResetPassword.lost_reset_password', 'input[name="password_1"]', 'input[name="password_2"]', '.woocommerce-Button.button');


    if (window.matchMedia("(max-width: 992px)").matches) {
        let get_comment = $('#comments');
        $('.sensei.lesson #content').append(get_comment);
    }

    //player-outroVisible
    var video = '.video iframe';
    setTimeout(() => {
        if ($(".video").length > 0) {
            // Auto play
            if (-1 === $(video).attr('src').indexOf('&playsinline=1')) {
                $(video).attr('src', `${$(video).attr('src')}&playsinline=1`);
            }
            var player = new Vimeo.Player($(video));

            //auto play after end
            player.on('ended', function () {
                let get_complate = document.querySelectorAll('.current');
                let get_current = get_complate[get_complate.length - 1];
                let classes_string_array = $('body').attr('class').split(' ');
                var id_less = '';
                $(classes_string_array).each(function (i, el) {
                    if (el.indexOf('postid-') === 0) {
                        id_less = el.substr(7);
                    }
                });
                $.ajax({
                    type: "post",
                    url: "/wp-admin/admin-ajax.php",
                    data: {
                        action: "lesson_item_complate",
                        id: id_less,
                    },
                    success: function () {
                        let next_item = $(get_current).next();
                        if ($(next_item).hasClass('course-progress-module')) {
                            next_item = $(next_item).next();
                        } else if ($(next_item).hasClass('completed')) {
                            let last_item = $('.completed');
                            let last_complated = last_item[last_item.length - 1];
                            next_item = $(last_complated).next();
                        }
                        let href = $(next_item).find('a').attr('href');
                        if (href !== undefined) {
                            window.location.href = href;
                        } else {
                            let progress_bar = $('#sensei_course_progress-2')[0];
                            let not_complate_lessons = $(progress_bar).find('.not-completed a');
                            let item = $(not_complate_lessons)[0];
                            let href = $(item).attr('href');

                            if (href !== undefined) {
                                window.location.href = href;
                            } else {
                                location.reload();
                            }
                        }
                    },
                    error: function (error) {

                    }
                });
            });
        }
    }, 3000);

    $('#sensei-quiz-form .send-message-button').appendTo('.sensei-quiz-actions');
    $('#sensei-quiz-list').after($('.menuSet'));
    $('.single-quiz #sensei-quiz-form input[type=radio]').attr('required', 'required');

    //coments upload image
    $(document).on('click', '.upload-comment-image', function (e) {
        $(this).closest('.comment-form').find('[name=comment_image]').trigger('click');
    });

    //send comment
    $(document).on('submit', '.comment-form', function (e) {
        e.preventDefault();

        var self = $(this),
            image = self.find('[name=comment_image]'),
            types = 'image/jpeg, image/png',
            data = new FormData($(this)[0]);
        //data.append('action', 'save_comment');
        if (0 < formFiles.length) {
            formFiles.forEach(function (file, index) {
                data.append('comment_images' + index, file);
            });
        }

        $('.comment-respond .preloader').show().css('display', 'flex');
        $.ajax({
            url: theme_data.ajaxurl,
            dataType: 'json',
            processData: false,
            contentType: false,
            method: 'POST',
            data: data,
            //data: new URLSearchParams(data),
            success: response => {
                $('.comment-respond .preloader').hide();

                if (response) {
                    alert(theme_data.labels.comment_added);
                    location.reload();
                }
            },
            error: function (jqXHR, exception) {
                if (jqXHR.status === 0) {
                    alert('Not connect. Verify Network.');
                } else if (jqXHR.status == 404) {
                    alert('Requested page not found (404).');
                } else if (jqXHR.status == 500) {
                    alert('Internal Server Error (500).');
                } else if (exception === 'parsererror') {
                    alert('Requested JSON parse failed.');
                } else if (exception === 'timeout') {
                    alert('Time out error.');
                } else if (exception === 'abort') {
                    alert('Ajax request aborted.');
                } else {
                    alert('Uncaught Error. ' + jqXHR.responseText);
                }
            }
        });
    });

    //replay comments
    $(document).on('click', '.comment-reply-link', function (e) {
        e.preventDefault();
        $('.comment-form textarea').css({
            'border-color': '#05A7DA',
            'outline-color': '#05A7DA'
        });
        var self = $(this),
            id = self.attr('data-commentid'),
            text = self.closest('.comment-body').find('.comment-content p').text(),
            author = self.closest('.comment-body').find('.comment-author .fn').text();

        if (50 < text.length) {
            text = text.slice(0, 50) + ' ...';
        }

        $('.comment-form [name=parent_id]').val(id);
        $('.comment-form .reply-to .text').text('“' + text + '“');
        $('.comment-form .reply-to span b').text(author + ':');
        $('.comment-form .reply-to').show().css('display', 'flex');

        $('html, body').stop().animate({
            'scrollTop': $('.comment-form').offset().top - 300
        }, 900, 'swing');
    });

    //remove replay comment
    $(document).on('click', '.remove-reply', function (e) {
        e.preventDefault();
        $('.comment-form textarea').css({
            'border-color': '#B7B7B7',
            'outline-color': '#B7B7B7'
        });
        $('.comment-form [name=parent_id]').val('');
        $('.comment-form .reply-to').hide();
        $('.comment-form .reply-to span b').text('');
    });

    $('.button.quiz-submit').text(theme_data.labels.submit);
    $('.quiz .quiz-submit.reset').text(theme_data.labels.reset_quiz);
    $('.wp-block-sensei-lms-button-view-quiz').closest('a').hide();
    $('.lesson_button_form [type=submit]').val(theme_data.labels.mark_as_complete);
    $('.wp-block-sensei-lms-button-complete-lesson button').text(theme_data.labels.mark_as_complete);
    $('.lesson .quiz-submit.reset').val(theme_data.labels.reset_lesson);


    $(document).on('change', 'form.comment-form [type=file]', function (e) {
        e.preventDefault();
        if (3 < this.files.length) {
            alert(theme_data.labels.images_limit);
            $(this).val('');
        }

        show_selected_image(this);
    });

    $(document).on('click', '.uploaded-image', function (e) {
        e.preventDefault();

        formFiles.splice($(this).attr('data-index'), 1);

        $(this).remove();
    });

    window.formFiles = [];


    function show_selected_image(object) {
        $('form.comment-form .comment-form-upload-image .uploaded-image').remove();

        if (0 < object.files.length) {
            var types = 'image/jpeg, image/png',
                count = 0;

            for (var i = 0; i < object.files.length; i++) {
                if (-1 != types.search(object.files[i].type)) {
                    var reader = new FileReader();

                    formFiles.push(object.files[i]);

                    reader.onload = function (e) {
                        $('form.comment-form .comment-form-upload-image').prepend('<div class="uploaded-image" data-index="' + count + '"><img src="' + e.target.result + '" alt="Uploaded Image"></div>');
                        count++;
                    };

                    reader.readAsDataURL(object.files[i]);
                }
            }
        }
    }


    if ($('body').hasClass('quiz') && 0 < $('.sensei-message .next-lesson').length) {
        var next_lesson = $('.sensei-message .next-lesson')[0];
        $(next_lesson).appendTo($('.sensei-quiz-action'));
        $('.sensei-message .next-lesson').remove();
    }

    $(document).on('click', '.quiz form ol#sensei-quiz-list li .question_media_display img', function (e) {
        e.preventDefault();
        e.stopPropagation();
    });

    $(document).on('click', '.reply-to', function (e) {
        e.preventDefault();

        var id = $('form.comment-form [name=parent_id]').val();

        $('html, body').stop().animate({
            'scrollTop': $('#comment-' + id).offset().top - 300
        }, 900, 'swing');
    });



    //finish lesson modal
    $('.popp-finish-form-wrapper form').on('submit', function (e) {
        e.preventDefault();
        $('.js-submit-rating-btn').trigger('click');
        let title = $(this).find('input').val();
        let feedback = $(this).find('textarea').val();
        let stars = $('.rmp-icon--processing-rating');
        $.ajax({
            type: "post",
            url: "/wp-admin/admin-ajax.php",
            data: {
                action: "feedback_mess",
                title: title,
                feedback: feedback,
                stars: (stars) ? stars.length : 0
            },
            success: function (data) {

                alert('Thank you for your opinion');
                $('.popp-finish').remove();

                setTimeout(() => {
                    document.location.reload();
                }, 3000);
            },
            error: function (error) {
            }
        });
    });

    // close finish popp 
    $('.close-form-popp').on('click', function () {
        $('.popp-finish').hide();
    });
    //detect that all lesson complate and show finish popp
    let arr = $('#sensei_course_progress-2')[0];
    let lesson_all = $(arr).find('.course-progress-lesson');
    let lesson_complate = $(arr).find('.completed');
    if (lesson_all.length === lesson_complate.length) {
        $('.popp-finish').css('display', 'flex');
    }

    if ($('#sensei_course_progress-2').length > 0) {
        if ($($('.widget_sensei_course_progress')[1]).find('.completed').length == $($('.widget_sensei_course_progress')[1]).find('.course-progress-lesson').length) {
            $.ajax({
                type: "post",
                url: "/wp-admin/admin-ajax.php",
                data: {
                    action: "get_user_certificate",
                },
                success: function (res) {
                    // window.open(res, "_blank", "toolbar = yes, top = 500, left = 500, width = 600, height = 600");
                    $($('.widget_sensei_course_progress')[1]).append($('<a id="get_user_certificate" target="_blank" href="' + res + '" class="certificate_complate"><i class="download-icon"></i>Your Certificate of Completion</a>'));
                }
            });
        }
    }

    //ANCHOR - Remove input autozoom on Iphone 

    const addMaximumScaleToMetaViewport = () => {
        const el = document.querySelector('meta[name=viewport]');
        if (el !== null) {
            let content = el.getAttribute('content');
            let re = /maximum\-scale=[0-9\.]+/g;
        
            if (re.test(content)) {
                content = content.replace(re, 'maximum-scale=1.0');
            } else {
                content = [content, 'maximum-scale=1.0'].join(', ')
            }
            el.setAttribute('content', content);
        }
    };
    
    const disableIosTextFieldZoom = addMaximumScaleToMetaViewport;
    const checkIsIOS = () =>
        /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
    if (checkIsIOS()) {
        disableIosTextFieldZoom();
    };

    //ANCHOR - Hide popup by clicking outside
    $('.background').click(function(e) {
        if (!$(e.target).closest('.popup_content').length){
            $('.background').fadeOut('slow', function () {
                $(this).css('display', 'none')
            });
            $('.popup_content').slideUp('slow');
            $('body').css('overflow', 'auto');
        }
    });

    // ====================================
    
    var $window = $(window),
    lazyBgArr = [];

    $window.on('load resize scroll', function(){    
        for (i = 0; i < lazyBgArr.length; i++) {
            var func = lazyBgArr[i];
            if (func !== undefined){ func(); }
            
        }
    });

    function lazyBg( strEl, intPos ){

        return function(){

            var intCheckVal = (($window.scrollTop() + $window.height()) + 100);
            if ( intCheckVal > strEl.offset().top) {  

                //   ===============================================
                

                if (window.matchMedia("(min-width: 1025px)").matches) { 
                //         var data_bg = "data-bg-min-1701";
                    var data_bg = "data-bg-desktop";
                    var bg_img_1025 = strEl.attr('data-bg-desktop');
                    strEl.css({'background-image':"url("+bg_img_1025+")"});
                    strEl.css({'background-repeat':"no-repeat"});
                    strEl.css({'background-size':"cover"});
                } 

                else if (window.matchMedia("(min-width: 1024px) and (max-width: 768px)").matches) { 
                    var data_bg = "data-bg-tablet";
                    var bg_img_768 = strEl.attr('data-bg-tablet');
                    strEl.css({'background-image':"url("+bg_img_768+")"});
                    strEl.css({'background-position':"center center"});
                    strEl.css({'background-size':"cover"});
                }

                else if (window.matchMedia("(max-width: 767px)").matches) { 
                    var data_bg = "data-bg-mobile";
                    var bg_img_767 = strEl.attr('data-bg-mobile');
                    strEl.css({'background-image':"url("+bg_img_767+")"});
                    strEl.css({'background-position':"center bottom"});
                    strEl.css({'background-size':"cover"});
                } 

                //   ===============================================     
                var tmpImg = new Image(),strSrc = strEl.attr(data_bg);
                tmpImg.src = strSrc;
                delete lazyBgArr[intPos];

                $(tmpImg).on('load', function(){
                    strEl.append('').css('opacity');
                    strEl.addClass('loaded');
                });
            }
        };
    }

    $('.lazy-background').each(function(i){
        lazyBgArr.push( lazyBg($(this), i) );                         
    });
   

    // ====================================
});
