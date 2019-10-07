(function ($) {

    $(".phoneManagerNoReaded").live("click", function(){
        $(this).removeClass("phoneManagerNoReaded");
        var id = $(this).attr("data-id");
        $.ajax({
            url: 'modules/phoneManager/ajax.php',
            method: 'GET',

            data: {
                'id': id,
            },
            success: function (data) {

            },
        });
    });

    $(".text-decoded").live("click", function() {

        var childs = $(this).children("td");
        var childs = $(childs[2]).children("span");
        childs[0].hide();
        childs[1].show();

    });

})(jQuery);