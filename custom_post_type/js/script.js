;(function($) {

    'usestrict';
    console.log(admin_localizer);
    var countLike = function() {
        $(document.body).on('click','#js-add-like', function(e) {
            e.preventDefault();
            var postID = $(this).data('id');
            var _self = $(this);
            $.ajax({
                url: admin_localizer.ajax_url,
                type: 'post',
                data: {
                    post_id: postID,
                    action: 'count_like' // PHP Function
                },
                success: function(res) {
                    var data = JSON.parse(res);
                    _self.find('.like-count').html(data)
                }
            })
        })
    }
    countLike();

})(jQuery);


