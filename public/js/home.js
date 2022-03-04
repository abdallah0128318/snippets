$(function(){
    // initialize tooltip
    $('[data-toggle="tooltip"]').tooltip();


    //************************************************/
    //        options list dynamic styles           //
    // **********************************************/


    // display options list when hovering the button
    $('.options-button').on('mouseenter', function(){
        var optionsList = $(this).siblings('.options');
        optionsList.show();
    });

    // hide options list when the mouse leave hovering the list
    $('.options').on('mouseleave', function(){
        $(this).hide();
    });

    // Hide options list if the use clicked any area rather tahn  the list and its children elements

    $(document).on('mouseup', (e)=>{
        var optionsList = $('.options');
        if(!optionsList.is(e.target) && optionsList.has(e.target).length === 0)
        {
            optionsList.hide();
        }
    });
    





});