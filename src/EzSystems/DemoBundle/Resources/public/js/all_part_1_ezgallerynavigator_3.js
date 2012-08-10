YUI(YUI3_config).add('ezgallerynavigator', function (Y) {

    Y.namespace('eZ');

    var L = Y.Lang;

    var defaultConfig = {
        gallery: '',
        next: '.next',
        prev: '.prev',
        cursor: '.cursor',
        container: '.images',
        images: 'figure',
        transitionDuration: 0.5,
        easing: 'cubic-bezier'
    };

    function width(el) {
        return parseInt(el.getStyle('width'));
    }

    function left(el) {
        return parseInt(el.getStyle('left'));
    }

    /**
     * Constructor of the Y.eZ.GalleryNavigator component
     *
     * @param conf
     */
    function eZGN(conf) {
        var that = this;

        this.conf = Y.merge(defaultConfig, conf);

        if ( L.isString(this.conf.gallery) ) {
            this.gallery = Y.one(this.conf.gallery);
        } else if ( L.isObject(this.conf.gallery) ) {
            this.gallery = this.conf.gallery;
        }

        this.container = this.gallery.one(this.conf.container);
        this.nextLink = this.gallery.one(this.conf.next);
        this.prevLink = this.gallery.one(this.conf.prev);

        this.images = this.container.all(this.conf.images);
        this.index = 0;
        this.total = this.images.size();

        this.cursor = this.gallery.one(this.conf.cursor);
        this.cursor.setStyles({
            left: this._computeCursorX(this.getSelectedImage()) + 'px',
            display: 'inline-block'
         });

        this._init();
    }

    eZGN.DEFAULT_CONFIG = defaultConfig;

   
    eZGN.prototype._init = function () {
        var that = this;

        this.nextLink.on('click', function (e) {
            e.preventDefault();
            that.next();
        });

        this.prevLink.on('click', function (e) {
            e.preventDefault();
            that.previous();
        });

        this.images.each(function(img, k) {
            img.on('click', function (e) {
                e.preventDefault();
                that.select(k);
            });
        });
    }

    eZGN.NAME = 'gallerynavigator';

    /**
     * Returns the selected figure
     *
     * @return Y.Node
     */
    eZGN.prototype.getSelectedImage = function () {
        return this.images.item(this.index);
    }

    /**
     * Returns a list of figures in the navigator
     *
     * @return Y.NodeList
     */
    eZGN.prototype.getImages = function () {
        return this.images;
    }

    /**
     * Returns the main container of the navigator
     *
     * @return Y.Node
     */
    eZGN.prototype.getContainer = function () {
        return this.container;
    }

    /**
     * Selects an image based on its position. When this method is called,
     * it fires the 'select' event.
     *
     * @param i integer
     */
    eZGN.prototype.select = function (i) {
        var p = this.index;

        if ( !L.isUndefined(i) ) {
            this.index = i;
        }

        var s = this.getSelectedImage();
        this.fire('select', {
            index: this.index,
            previous: p,
            total: this.total,
            imageNode: s,
        });
        this._handleNavigationLink();
        this._animate();
    }

    /**
     * Moves to the next image if possible
     */
    eZGN.prototype.next = function () {
        if ( this.index == (this.total -1) ) {
            return;
        }
        this.select(this.index + 1);
    }

    /**
     * Moves to the previous image if possible
     */
    eZGN.prototype.previous = function () {
        if ( this.index == 0 ) {
            return;
        }
        this.select(this.index - 1);
    }

    /**
     * Checks whether the selected image is outside of the navigator
     * on the right
     *
     * @private
     */
    eZGN.prototype._isSelectedImageOutsideRight = function () {
        var s = this.getSelectedImage(),
            lRight = this.gallery.getX() + parseInt(this.gallery.getStyle('width'));
       if ( ((s.getX() + parseInt(s.getStyle('width'))) > lRight) ) {
           return true;
       }
       return false;
    }

    /**
     * Checks whether the selected image is outside of the navigator
     * on the left
     *
     * @private
     */
    eZGN.prototype._isSelectedImageOutsideLeft = function () {
        var s = this.getSelectedImage(),
            lLeft = this.gallery.getX();
       if ( s.getX() < lLeft ) {
          return true;
       }
       return false;
    }

    /**
     * Computes the left position of the cusor so that it is centered
     * on the s figure.
     *
     * @param s Y.Node corresponding to the selected figure
     * @private
     */
    eZGN.prototype._computeCursorX = function (s) {
        var offset = this.gallery.getX(),
            selectedWidth = width(s),
            cursorWidth = width(this.cursor);
        return s.getX() - offset + selectedWidth/2 - cursorWidth/2;
    }
 
    /**
     * Animates the cursor and/or the container of images
     *
     * @private
     */
    eZGN.prototype._animate = function () {
        var trConf = {
                duration: this.conf.transitionDuration,
                easing: this.conf.easing
            }, sel = this.getSelectedImage(),
            cursorX = this._computeCursorX(sel), containerX, containerXOrig;

        if ( this._isSelectedImageOutsideRight() ) {
            // Image is outside in the right
            // Moving images so that selected images becomes the first visible one
            containerXOrig = left(this.container);
            containerX = this.container.getX() - sel.getX();
            cursorX += containerX - containerXOrig;
            trConf['left'] = containerX + 'px';
            this._doTransition(this.container, trConf);
        } else if ( this._isSelectedImageOutsideLeft() ) {
            // Image is outside in the left
            // Looking for the image in the left so that the selected image is the last visible one
            containerXOrig = left(this.container);
            var selectedBorderLeft = sel.getX() + width(sel), sizeBetween,
                widthGallery = width(this.gallery);
            for(var i = this.index; i >= 0; i--) {
                sizeBetween = selectedBorderLeft - this.images.item(i).getX();
                if ( sizeBetween > widthGallery ) {
                    i++;
                    // this.images.item(i) should be the first visible
                    break;
                }
            }
            if ( i < 0 )
                i = 0;
            containerX = this.container.getX() - this.images.item(i).getX();
            cursorX += containerX - containerXOrig;
            trConf['left'] = containerX + 'px';
            this._doTransition(this.container, trConf);
        }
        trConf['left'] = cursorX + 'px';
        this._doTransition(this.cursor, trConf);
    }

    /**
     * Workaround an IE9 bug where transtion does not work as expected. So,
     * in IE9, we fallback to Y.Anim instead of a native transition.
     */
    eZGN.prototype._doTransition = function(node, conf) {
        if ( Y.UA.ie ) {
            var anim = new Y.Anim({
                node: node,
                duration: conf.duration,
                to: {
                    left: conf.left
                 }
            });
            anim.run();

        } else {
            node.transition(conf);
        }

    }

    /**
     * Shows and Hides previous/next links when needed
     */
    eZGN.prototype._handleNavigationLink = function () {
        var d = this.conf.transitionDuration,
            showC = {
                opacity:1,
                duration: d
            },
            hideC = {
                opacity:0,
                duration: d
            };

        if ( this.index == 0 ) {
            this.prevLink.transition(hideC);
        } else if ( this.index >= 1 ) {
            this.prevLink.transition(showC);
        }

        if ( this.index == (this.total -1) ) {
            this.nextLink.transition(hideC);
        } else if ( this.index <= (this.total - 2) ) {
            this.nextLink.transition(showC);
        }
    }


    Y.augment(eZGN, Y.EventTarget, true, null, {emitFacade: true});
    Y.eZ.GalleryNavigator = eZGN;

}, '1.0.0', {
    requires: [
        'event-custom', 'transition', 'node-base', 'node-screen', 'anim'
    ]
});
