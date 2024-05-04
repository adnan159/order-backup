jQuery(document).ready(function($) {
    $('#syncButton').on('click', function(event) {
        event.preventDefault();

        var data = {
            action: 'backup_order_remote_db',
            startDate: $('#startDate').val(),
            endDate : $('#endDate').val()
        };

        $.ajax({
            url: ajaxurl, // WordPress AJAX URL
            type: 'POST',
            data: data,
            success: function(response) {
                // Handle success response
                console.log(response.message);
                console.log(response.data_received);
            },
            error: function(xhr, status, error) {
                // Handle error response
                console.log(xhr.responseText);
            }
        });
    });
});