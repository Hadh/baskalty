$(document).ready(function(){
    $("#recruiter").hide();
    $("#pills-profile-tab, #pills-home-tab").on('click', function(){
        $("#candidate, #recruiter").toggle()
    });
    $("li").click(function() {
        $("li").removeClass("active");
        $(this).addClass("active");
    });
});
