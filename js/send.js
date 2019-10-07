(function ($) {

        $("#createMsg").live("click", function(){
            $("#smsCard").show();

        });

        $("#closeSmsBox").live("click", function(){
            $("#smsCard").hide();

        });

        $(".text-decoded-sended").live("click", function() {

            var childs = $(this).children("td");
            var childs = $(childs[3]).children("span");
            childs[0].hide();
            childs[1].show();
    
        });

})(jQuery);