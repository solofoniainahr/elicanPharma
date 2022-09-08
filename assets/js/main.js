$('#client_socialReason').keyup(function(){
    const value = $(this).val()
    const url = $(this).data('target')

    $.ajax({
        url: url,
        type: 'POST',
        data: { value: value },
        success: function (data) {}
    })
})