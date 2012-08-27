YUI(YUI3_config).use('event', function (Y) {
    Y.on('domready', function () {
        Y.all('*[data-action=toggleclass]').each(function(elem) {
            var cl = elem.getAttribute('data-class'),
                targets = Y.all(elem.getAttribute('data-target'));
            elem.on('click', function (e) {
                e.preventDefault();
                targets.toggleClass(cl);
            });
        });
    });
});
