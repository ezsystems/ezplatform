YUI(YUI3_config).use('event', function (Y) {
    Y.on('domready', function () {
        var h = Y.one('html'),
            ua = Y.UA;

        if ( h.hasClass('ie') ) {
            // conditional comments did the job
            return;
        }
        // Y.UA is based on the user agent string,
        // this is bad... but should be used only to fix "small" CSS issues
        if ( ua.ie ) {
            h.addClass('ie').addClass('ie-gt9');
        } else if ( ua.webkit ) {
            h.addClass('webkit').addClass('vers_' + (ua.webkit + '').replace('.', '_'));
        } else if ( ua.gecko ) {
            h.addClass('gecko').addClass('vers_' + (ua.gecko + '').replace('.', '_'));
        }
    });
});
