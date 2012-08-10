YUI(YUI3_config).add('ezflyout', function (Y) {

    Y.namespace('eZ');

    var L = Y.Lang;

    var defaultConfig = {
        element: '',
        close: '.close',
        scrollTrigger: 0,
        trackInitialScroll: true,
        hideTransition: {

        },
        showTransition: {

        }
    };

    /**
     * Constructor of Y.eZ.FlyOut object
     *
     * @param conf configuration object containing the following elements:
     *      - element String (required), a selector to the element that will be shown/hidden
     *      - close String (default ".close"), a selector relative the element pointing to nodes on which a click will hide the element
     *      - scrollTrigger (default 0) int|string|Node if it's an integer, the number of pixel to scroll to show the element;
     *          if it's a string, a selector to a Node, its y position will be used as the limit;
     *          if it's a Node, its y position will be used as the limit
     *      - trackInitialScroll bool (default true), if true, the Y.eZ.FlyOut will check the initial scroll to show the element
     *      - hideTransition configuration object for the transition to hide the element
     *      - showTransition (required) configuration object for the transition to show the element
     * 
     * see http://yuilibrary.com/yui/docs/transition/ for hideTransition and showTransition configuration object.
     * In addition, Y.eZ.FlyOut also allows to put function instead of plain values in the transition properties.
     * The transtion's start and end callbacks are also usable.
     */
    function eZFlyOut(conf) {
        this.conf = Y.merge(defaultConfig, conf);
        this.element = Y.one(this.conf.element);
        this.hidden = true;
        this.scrollSubscription = false;

        this._initEvents();
    }

    /**
     * Checks wether the Y.eZ.FlyOut is hidden or not
     */
    eZFlyOut.prototype.isHidden = function () {
        return this.hidden;
    }

    /**
     * Shows the Y.eZ.FlyOut using the show transition configuration object.
     * It triggers the "show" event.
     */
    eZFlyOut.prototype.show = function () {
        if ( this.isHidden() ) {
            this.element.transition(
                this._transitionConf(this.conf.showTransition)
            );
            this.hidden = false;
            this.fire('show');
        }
    }

    /**
     * Hides the Y.eZ.FlyOut using the hide transition configuration object.
     * It triggers the "hide" event.
     */
    eZFlyOut.prototype.hide = function () {
        if ( !this.isHidden() ) {
            this.element.transition(
                this._transitionConf(this.conf.hideTransition)
            );
            this.hidden = true;
            this.fire('hide');
        }
    }

    /**
     * Closes the Y.eZ.FlyOut. This method is supposed to be called when
     * the user clicks on a "close" element. It hides the element and
     * completely disables the Y.eZ.FlyOut instance;
     * It triggers the "close" event.
     */
    eZFlyOut.prototype.close = function () {
        this.scrollSubscription.detach();
        this.hide();
        this.fire('close');
    }

    /**
     * Initializes the events needed by Y.eZ.FlyOut:
     *   - scroll event to detect the scroll beyond the configured limit
     *   - click event on a "close" element
     * @private
     */
    eZFlyOut.prototype._initEvents = function () {
        var that = this,
            handleScroll = function () {
            var limit = false;
            if ( L.isNumber(that.conf.scrollTrigger) ) {
                limit = that.conf.scrollTrigger;
            } else {
                if ( L.isString(that.conf.scrollTrigger) ) {
                    limit = Y.one(that.conf.scrollTrigger);
                }
                if ( !L.isObject(limit) ) {
                    return;
                }
                limit = limit.getY();
            }
            if ( that.element.get('docScrollY') >= limit ) {
                that.show();
            } else {
                that.hide();
            }
        };

        this.scrollSubscription = Y.on('scroll', handleScroll);

        this.element.delegate('click', function () {
            that.close();
        }, this.conf.close);

        this.fire('ready');
        if ( this.conf.trackInitialScroll ) {
            handleScroll();
        }
    }

    /**
     * Creates a transition config object by cloning the conf parameter and
     * executing the methods it contains.
     *
     * @param conf configuration object
     * @private
     * @return object
     */
    eZFlyOut.prototype._transitionConf = function(conf) {
        var res = Y.clone(conf, false);
        Y.Object.each(res, function(v, k) {
            if ( L.isFunction(v) ) {
                res[k] = v.call(this);
            } else if ( k !== 'on' && L.isObject(v) ) {
                res[k] = this._transitionConf(v);
            }
        }, this);
        return res;
    }

    Y.augment(eZFlyOut, Y.EventTarget, true, null, {emitFacade: true});

    Y.eZ.FlyOut = eZFlyOut;

}, '1.0.0', {
    requires: [
        'event', 'node-screen', 'transition', 'node-event-delegate'
    ]
});
