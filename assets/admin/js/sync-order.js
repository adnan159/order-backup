jQuery(document).ready(function($) {
    $('#syncButton').on('click', function(event) {
        event.preventDefault();

        var countData = {
            action: 'total_order_count_action',
            startDate: $('#startDate').val(),
            endDate : $('#endDate').val()
        };

        var batchData = {
            action: 'backup_order_remote_db',
            startDate: $('#startDate').val(),
            endDate : $('#endDate').val()
        };

        $.ajax({
            url: ajaxurl, // WordPress AJAX URL
            type: 'POST',
            data: countData,
            success: function(response) {
                // Handle success response
                const batch = response.data;

                for( i = 1; i<=batch; i++ ) {
                    $.ajax({
                        url: ajaxurl, // WordPress AJAX URL
                        type: 'POST',
                        data: batchData,
                        success: function(response) {
                            // Handle success response
                            console.log(response.message);
                            console.log(response.data_received);
                        },
                        error: function(xhr, status, error) {
                            // Handle error response
                            console.log(xhr);
                        }
                    });
                }
            },
            error: function(xhr, status, error) {
                // Handle error response
                console.log(xhr);
            }
        });
    });
});