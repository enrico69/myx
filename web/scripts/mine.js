$('#searchIcon').mouseover(function(){
    $('#searchFormContainer').show();
});

$('#searchFormContainer').mouseleave(function(){
    $('#searchFormContainer').hide();
});


function resizeCheckBoxes() {
    $('select').each(function(index){
        if($(this).attr('multiple')) {
            var size = $(this).find('option').length;
            if(size > 1) {
                $(this).attr('size', 2);
            } else {
                $(this).attr('size', 1);
            }
        }
    });
} 