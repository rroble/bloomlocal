jQuery(function($) {

    var handlers = [];

    var handleNext = function () {
        for (var i = 0; i < handlers.length; i++) {
            try {
                handlers[i]();
            } catch (err) {
                console.error(err);
            }
        }

        return false;
    };
    
    // loop, stop if found
    var loop_t;
    loop_t = setInterval(function () {
        var events = $("#wpmc-next").data("events");
        if (events != undefined) {
            clearInterval(loop_t);

            if (events.click != undefined) {
                for (var i = 0; i < events.click.length; i++) {
                    if (events.click[i].handler != undefined) {
                        handlers.push(events.click[i].handler);
                        events.click[i].handler = function () {
                            // nop
                        };
                    }
                }
            }

            $("form.checkout")
                .attr("id", "bloomlocal_checkout_form")
                .addClass("processing")
                .on("submit", handleNext);

            $("#wpmc-next")
                .attr("type", "submit")
                .attr("form", "bloomlocal_checkout_form");
        }
    }, 100);

    // always run
    setInterval(function () {
        $(".validate-required input, .validate-required select").each(function () {
            $(this).prop("required", $(this).is(":visible"));
        });

        $("form.checkout").removeAttr("novalidate");
    }, 100);

});
