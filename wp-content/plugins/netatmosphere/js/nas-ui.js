
/*jQuery( document ).tooltip({
    tooltipClass: "custom-tooltip-styling",
    position: {
        my: "left center", at: "right center" 
    }
});*/

// format table with www.datatables.net
jQuery(document).ready(function($) {
    /*$( '#test-tooltip' ).tooltip();*/

    // change defaults
    /*$.extend( $.fn.dataTable.defaults, {
        paging: 'false',
        scrollCollapse: 'true',
        scrollY: '100vh',
    } );*/

    $("#nas-devices").DataTable({
        "paging": false,
        "scrollCollapse": true,
        "scrollX": true,
        "scrollY": '100vh'
    });
    
     
    /*$('#hsl-results tbody')
        .on('mouseenter', 'td', function () {
            var colIdx = table.cell(this).index().column;
 
            $(table.cells().nodes()).removeClass('highlight');
            $(table.column(colIdx).nodes()).addClass('highlight');
        } );
    $('#hsl-results tbody')
        .on('mouseleave', 'td', function() {
            $(table.cells().nodes()).removeClass('highlight');
        });*/
});