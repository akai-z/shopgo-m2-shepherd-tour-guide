require([
    'jquery',
    'tether',
    'Shepherd',
    'Magento_Ui/js/modal/confirm'
], function($, tether, Shepherd, confirm) {
    var tour = null;
    var isMissingElement = false;
    var overlay = '<div class="shepherd-overlay"></div>';

    function setTour() {
        tour = new Shepherd.Tour({
            defaults: {
                classes: 'shepherd-theme-arrows',
                scrollTo: true,
                showCancelLink: true
            }
        });
    }

    function setEvents() {
        tour.on('start', function () {
            addOverlay();
        });
        tour.on('hide', function () {
            removeOverlay();
            disableCurrentPageTour();
        });
        tour.on('cancel', function () {
            removeOverlay();
            disableCurrentPageTour();
        });
    }

    function addOverlay() {
        $('body').append(overlay);
    }

    function removeOverlay() {
        $('.shepherd-overlay').remove();
    }

    function disableCurrentPageTour() {
        confirm({
            content: shopgo.shepherd.disableTourMessage.current,
            actions: {
                confirm: function() { disableTour(shopgo.shepherd.fullActionName); },
                cancel: function() { disableAllPagesTours(); }
            }
        });
    }

    function disableAllPagesTours() {
        confirm({
            content: shopgo.shepherd.disableTourMessage.all,
            actions: {
                confirm: function() { disableTour('*'); }
            }
        });
    }

    function disableTour(fullActionName) {
        var ajaxConfig = {
            type: 'POST',
            url: shopgo.shepherd.disableTourUrl,
            data: {
                full_action_name: fullActionName
            }
        };

        $.ajax(ajaxConfig);
    }

    function addTourSteps() {
        $.each(shopgo.shepherd.tourSteps, function (name, config) {
            if (typeof config['attachTo'] != 'undefined') {
                var target = config['attachTo'].split(' ')[0];

                if ($(target).length <= 0) {
                    isMissingElement = true;
                    return false;
                }
            }

            $.each(config['buttons'], function(i, button) {
                config['buttons'][i].action = eval('tour.' + button.action);
            });

            tour.addStep(name, config);
        });
    }

    $(function() {
        setTour();
        setEvents();
        addTourSteps();

        if (!isMissingElement) {
            tour.start();
        }
    });
});
