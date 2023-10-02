var br_saved_timeout;
var br_savin_ajax = false;
(function ($){
    $(document).ready( function () {
        $(document).on('click', '.br_notices_page_add', function(event) {
            event.preventDefault();
            if( $('.id_'+$('.br_notices_page_select').val()).length == 0 ) {
                var html = '<li class="br_notices_page_id id_'+$('.br_notices_page_select').val()+'"><input type="hidden" name="br-cart_notices-options[pages][]" value="'+$('.br_notices_page_select').val()+'"><button type="button" class="button br_notices_page_remove">'+$('.br_notices_page_select').find('option:selected').text()+'</button></li>';
                $('.br_notices_pages').append($(html));
            }
        });
        $(document).on('click', '.br_notices_page_remove', function(event) {
            $(this).parents('.br_notices_page_id').remove();
        });
        
        function check_br_notice_fix_duplicate() {
            if( $('.br_notice_fix_duplicate').prop('checked') ) {
                $('.br_notice_fix_duplicate_show').show();
            } else {
                $('.br_notice_fix_duplicate_show').hide();
            }
        }
        check_br_notice_fix_duplicate();
        $(document).on('change', '.br_notice_fix_duplicate', check_br_notice_fix_duplicate);
        
        function check_br_notice_not_fix_duplicate() {
            if( $('.br_notice_not_fix_duplicate').prop('checked') ) {
                $('.br_notice_fix_duplicate_all').show();
                $('.br_notice_not_fix_duplicate_all').hide();
            } else {
                $('.br_notice_fix_duplicate_all').hide();
                $('.br_notice_not_fix_duplicate_all').show();
            }
        }
        check_br_notice_not_fix_duplicate();
        $(document).on('change', '.br_notice_not_fix_duplicate', check_br_notice_not_fix_duplicate);
    });
})(jQuery);
