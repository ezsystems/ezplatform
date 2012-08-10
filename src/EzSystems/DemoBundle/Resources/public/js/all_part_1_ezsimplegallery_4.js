YUI(YUI3_config).add('ezsimplegallery', function (Y) {

    Y.namespace('eZ');

    var L = Y.Lang;

    var defaultConfig = {
        gallery: '',
        next: '.next',
        prev: '.prev',
        indicators: '.indicator li',
        selectedIndactorClass: 'selected',
        container: '.images',
        images: 'figure',
        transitionDuration: 0.8,
        easing: 'cubic-bezier'
    };

    /**
     * Constructor of Y.eZ.SimpleGallery component
     *
     * @param conf configuration object containing:
     *      - gallery (required): the node or a selector to the node containing the gallery
     *      - next (default .next): selector to the element that allows to see the next image
     *      - prev (default .prev): selector to the element that allows to see the previous image
     *      - indicators (default .indicators li): selector to elements that will be used as an indicator of the position in the gallery
     *      - selectedIndactorClass (default selected): class to set on the indicator corresponding to the selected image
     *      - container (default .images): selector to the element containing the images, its left CSS value will be changed
     *      - images (default figure): selector to element representing an image
     *      - transitionDuration (default 0.8): number of second the transition should last between two images
     *      - easing: the easing to use for the transition
     */
    function eZSG(conf) {
        this.conf = Y.merge(defaultConfig, conf);

        if ( L.isString(this.conf.gallery) ) {
            this.gallery = Y.one(this.conf.gallery);
        } else if ( L.isObject(this.conf.gallery) ) {
            this.gallery = this.conf.gallery;
        }


        this.container = this.gallery.one(this.conf.container);
        this.next = this.gallery.one(this.conf.next);
        this.prev = this.gallery.one(this.conf.prev);
        this.indicators = this.gallery.all(this.conf.indicators);

        this.index = 0;
        this.total = this.gallery.all(this.conf.images).size();

        this._init();
    }

    /**
     * Initialises the component:
     *  - init click events on prev/next links and on the indicators
     *  - init windowresize event to adapt the position of the currently seen images
     */
    eZSG.prototype._init = function () {
        var that = this;

        this.next.on('click', function (e) {
            e.preventDefault();
            that.showNext();
        });
        this.prev.on('click', function (e) {
            e.preventDefault();
            that.showPrev();
        });
        Y.on('windowresize', function () {
            // realign the gallery when the window is resized
            that.scrollTo(that.index);
        });

        this.indicators.each(function(ind, k) {
            ind.on('click', function (e) {
                if ( !this.hasClass(that.conf.selectedIndactorClass) ) {
                    that.scrollTo(k);
                }
            });
        });
    }

    /**
     * Scrolls to the next images if there's one
     */
    eZSG.prototype.showNext = function () {
        if ( this.index == (this.total - 1) ) {
            return;
        }
        this.scrollTo(this.index + 1);
    }

    /**
     * Scrolls to the previous images if there's one
     */
    eZSG.prototype.showPrev = function () {
        if ( this.index == 0 ) {
            return;
        }
        this.scrollTo(this.index - 1);
    }

    /**
     * Scrolls to a given image by its index
     */
    eZSG.prototype.scrollTo = function (newIndex) {
        var f = 1, s = this.conf.selectedIndactorClass,
            c = this.container, o = this.index * this._getOffset() * -1,
            hasIndicator = (this.indicators.size() > 0);

        f =  this.index - newIndex;

        if ( f != 0 ) {
            if ( hasIndicator )
                this.indicators.item(this.index).removeClass(s);
            this.index = newIndex;
            if ( hasIndicator )
                this.indicators.item(this.index).addClass(s);
        }

        var target = o + (f * this._getOffset());
        if ( Y.UA.ie ) {
            // IE seems to have some issue with this transition ?!?
            // so we fall back on Anim instead...
            var anim = new Y.Anim({
                node: c,
                duration: this.conf.transitionDuration,
                to: {
                    left: target + 'px'
                }
           });
           anim.run();
        } else {
            c.transition({
                left: {
                    value: target + 'px',
                    duration: this.conf.transitionDuration,
                    easing: this.conf.easing
                }
            });
        }
        this._handleNavigationLink();
    }

    /**
     * Calculates the offset between two images
     */
    eZSG.prototype._getOffset = function () {
        return this.gallery.get('clientWidth');
    }

    /**
     * Shows and Hides previous/next links when needed
     */
    eZSG.prototype._handleNavigationLink = function () {
        var d = this.conf.transitionDuration,
            showC = {
                opacity:1,
                duration: d
            },
            hideC = {
                opacity:0,
                duration: d
            }

        if ( this.index == 0 ) {
            this.prev.transition(hideC);
        } else if ( this.index >= 1 ) {
            this.prev.transition(showC);
        }

        if ( this.index == (this.total -1) ) {
            this.next.transition(hideC);
        } else if ( this.index <= (this.total - 2) ) {
            this.next.transition(showC);
        }
    }


    Y.eZ.SimpleGallery = eZSG;


}, '1.0.0', {
    requires: [
        'node-base', 'node-screen', 'transition', 'anim', 'event-resize'
    ]
});
