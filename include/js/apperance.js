$(document).on("click", "#dark-mode", function () {
    $("html").removeClass("dark-mode");
    localStorage.setItem('theme', 'light');
    $(this).css("display", "none");
    $("#light-mode").css("display", "block");
});


$(document).on("click", "#light-mode", function () {
    $("html").addClass("dark-mode");
    localStorage.setItem('theme', 'dark');
    //dark mode butonu göster
    $(this).css("display", "none");
    $("#dark-mode").css("display", "block");
});


// $(document).ready(function () {
//     const theme = localStorage.getItem('theme');
//     console.log(theme);
//     if (theme === 'dark') {
//         $('body').addClass('dark-mode');
//         $('#dark-mode').show();
//         $('#light-mode').hide();
//     } else {
        
//         $('body').removeClass('dark-mode');
       
//         $('#dark-mode').hide();
//         $('#light-mode').show();
//     }
// });