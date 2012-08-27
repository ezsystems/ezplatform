YUI(YUI3_config).use('event-base', 'node-base', 'event-outside', function (Y) {
    Y.on('domready', function () {
        Y.all('.transition-showed').each(function () {
            this.on('clickoutside', function (e) {
                if ( this.get('id') === location.hash.replace('#', '') ) {
                    location.hash = '';
                }
            });
        });
    });
});
