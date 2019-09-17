$( document ).ready(function() {
    var check_href_in_nav = $('.ajax_link');
    if(check_href_in_nav) {
        var last_page = check_href_in_nav.attr('data-lastpage');
        var last_page_num = (parseInt(last_page) + 1);


        $('html, body').animate({
            scrollTop: ($('.news-list-custom').last().offset().top)
        },50);
    }

//AJAX запрос новых новостей при прокрутке
});//Прокрутка к концу страницы и обновление УРЛ

function asyncLoad(){
    url_raw =  $('.ajax_link').attr('data-url');
    if (url_raw !== undefined && url_raw.length > 1) {
        url = url_raw +'&NOT_NEW_PAGE=TRUE';
        var to_delete = $('.ajax_link');

        BX.ajax(
            {
                url: url,
                method: 'GET',
                dataType: 'html',
                timeout: 0,
                async: true,
                processData: true,
                scriptsRunFirst: false,
                emulateOnload: false,
                start: true,
                cache: false,
                onsuccess: function (result) {
                    if (result) {
                        var pars = $($.parseHTML(result));
                        var elements = $(pars).find('.news-list-custom'),
                            pagination = $(pars).find('.ajax_link');
                        if(elements !== undefined) {
                            var link_to_get_for_browser = $(elements).attr('data-page');
                        }
                        var check_href_in_nav = $('.ajax_link');
                        var last_page = check_href_in_nav.attr('data-lastpage');
                        var last_page_num = (parseInt(last_page) + 1);

                        if($(elements).attr('data-page') > last_page_num) {
                            return false;
                        }
                        if(link_to_get_for_browser !== undefined) {
                            window.history.pushState("object or string", "Title", location.pathname+"?PAGEN_1="+$(elements).attr('data-page'));
                            $(to_delete).remove();
                            $('.place_to_insert').before(elements);
                            $('.place_to_insert').after(pagination);
                        }
                    }
                }
            });
    }
}
