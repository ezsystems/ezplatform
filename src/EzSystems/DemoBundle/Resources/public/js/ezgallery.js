YUI(YUI3_config).add('ezgallery', function (Y) {

    Y.namespace('eZ');

    var L = Y.Lang;

    var defaultConfig = {
        container: '.gallery-viewer',
        title: 'h2 a',
        counter: '.counter span',
        image: 'figure img',
        caption: 'figcaption',
        figure: 'figure',
        transitionDuration: 0.8,
        autoScrollOnSelect: true,
        autoFixSizes: true,
        initFunc: function () { },
        updateFunc: function (elem) { },
        navigator: Y.eZ.GalleryNavigator.DEFAULT_CONFIG
    }

    /**
     * Constructor of the Y.eZ.Gallery components
     *
     * @param conf
     */
    function eZG(conf) {
        this.conf = Y.merge(defaultConfig, conf);
        this.navigator = new Y.eZ.GalleryNavigator(conf.navigator);
        this._init();

        this.hasStarted = false;
    }

    /**
     * Initialises the Y.eZ.Gallery
     *  - call the init function from the configuration
     *  - set the event handler from Y.eZ.GalleryNavigator
     *  - set the event handler on window resize
     */
    eZG.prototype._init = function () {
        var that = this;

        this.container = Y.one(this.conf.container);
        this._fixSizes();
        this.conf.initFunc.call(this);

        this.navigator.on('select', function (item) {
            that.hasStarted = true;
            if ( that.conf.autoScrollOnSelect ) {
                that.container.scrollIntoView(true);
            }
            // if index == previous we are after a resize
            // so we don't need a transition
            that.update(item, (item.index != item.previous));
        });

        Y.on('windowresize', function () {
            if ( that.hasStarted && that.conf.autoScrollOnSelect ) {
                that.container.scrollIntoView(true);
            }
            that.navigator.select();
        });
    }
 
    /**
     * Updates the visible image
     *
     * @param item object send by Y.eZ.GalleryNavigator when a selection is done
     * @param animate bool, whether an animation is required or not
     */
    eZG.prototype.update = function (item, animate) {
        var that = this;

        if ( animate ) {
            this.container.setStyle('opacity', 0);
            this.conf.updateFunc.call(that, item);
            this._fixSizes();
            this.container.transition({
                duration: this.conf.transitionDuration,
                opacity: 1
            });
        } else {
            this.conf.updateFunc.call(this, item);
            this._fixSizes();
        }
    }

    /**
     * fix the size of the figure and img element so that the gallery fits
     * on the browser window and the figcaption is visible if there's any
     *
     * @private
     */
    eZG.prototype._fixSizes = function () {
        if ( !this.conf.autoFixSizes ) {
            return;
        }
        var c = this.container,
            fig = c.one(this.conf.figure),
            nav = this.navigator.getContainer();
            caption = c.one(this.conf.caption);
            img = c.one(this.conf.image),
            offsetFig = 0, figH = 0, offsetImg = 0,
            imgRatio = parseInt(img.getAttribute('width')) / parseInt(img.getAttribute('height'));

        // compute the figure height so that the bottom of the navigator is aligned
        // with the bottom of the viewport.
        fig.setStyle('height', 'auto');
        offsetFig = nav.getY() + nav.get('offsetHeight') - this.container.getY() - nav.get('winHeight');
        figH = fig.get('offsetHeight') - offsetFig
        fig.setStyle('height', figH + 'px');

        img.setStyles({height: 'auto', width: 'auto'});
        offsetImg = img.get('offsetHeight') + caption.get('offsetHeight') - figH;
        if ( offsetImg > 0 ) {
            var imgH = img.get('offsetHeight') - offsetImg,
                imgW = imgH * imgRatio;
            if ( imgH > parseInt(img.getAttribute('height')) ) {
                // don't upscale
                imgH = parseInt(img.getAttribute('height'));
                imgW = imgH * imgRatio;
            }
            img.setStyles({height: imgH + 'px', width: imgW + 'px'});
        }
    }

    Y.eZ.Gallery = eZG;

}, '1.0.0', {
    requires: [
        'ezgallerynavigator', 'transition', 'event-resize', 'anim', 'node-base'
    ]
});
