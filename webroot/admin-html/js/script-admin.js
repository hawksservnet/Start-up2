$(document).ready(function () {
    //active tootip (page17)
    $('[data-toggle="tooltip"]').tooltip();
    //get value start select (page17)

    //show popup when change select (page17)
    var value_curent;
    $('.select-1').on('click',function () {
        value_curent = $(this).val();
        //console.log(value_curent);
    }).change(function () {
        $(this).find(":selected").each(function () {
            var value_change = $(this).val();
            //console.log($(this).val());
            if(value_curent === '1' && value_change ==='2' )
            $('#myModal').modal('show');
        });
    });

    //show or hide when choose select (page20)
    $('#select-2').on('change', function() {
        if ( this.value == '1')
        {
            //console.log('1');
            $("#ad-name").fadeIn();
            $("#ad-textarea").fadeOut();
            $("#ad-other").fadeOut();
            $("#ad-number-max").fadeOut();
            $("#ad-checkbox").fadeIn();
            return
        }
        if ( this.value == '2')
        {
           // console.log('2')
            $("#ad-name").fadeIn();
            $("#ad-textarea").fadeIn();
            $("#ad-other").fadeIn();
            $("#ad-number-max").fadeOut();
            $("#ad-checkbox").fadeIn();
            return
        }
        else
        {
            //console.log('3')
            $("#ad-name").fadeIn();
            $("#ad-textarea").fadeIn();
            $("#ad-other").fadeIn();
            $("#ad-number-max").fadeIn();
            $("#ad-checkbox").fadeIn();
            return
        }
    });

});
