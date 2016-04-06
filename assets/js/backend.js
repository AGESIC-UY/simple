$(document).ready(function() {

    // -- Para que el tooltip funcione en los contenidos cargados con ajax
    $(document).on("hover",".tt",function(){
        $(this).tooltip({
            html: true,
            trigger: "manual"
        }).tooltip('toggle');
    });
});