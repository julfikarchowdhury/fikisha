$(document).ready(function () {
    //animation
    AOS.init();

    // video view modal
    new VenoBox({
        selector: ".playButton",
    });

    //navigation sticky
    $(window).scroll(function () {
        if ($(window).scrollTop() > 70) {
            $("#sticky-naviation").addClass("shrink");
        } else {
            $("#sticky-naviation").removeClass("shrink");
        }
    });

    //Page loading work
    if ($(window).scrollTop() > 70) {
        $("#sticky-naviation").addClass("shrink");
    } else {
        $("#sticky-naviation").removeClass("shrink");
    }

    jarallax(document.querySelectorAll(".jarallax"), {
        speed: 0.5,
    });
    // end page loading work

    //end navigation sticky

    // odometer
    var el = $(".odometer");
    el.each(function () {
        var countNumber = parseInt($(this).attr("data-count")).toFixed(2);

        var k = countNumber / 1000;
        var lakh = k / 100;
        var million = lakh / 10;
        if (million >= 1) {
            var countPosition = "M+";
            var number = million;
        } else if (lakh >= 1) {
            var countPosition = "L+";
            var number = lakh;
        } else if (k >= 1) {
            var countPosition = "K+";
            var number = k;
        } else {
            var countPosition = "+";
            var number = countNumber;
        }
        $(this).html(parseInt(number));
        $(this).closest("h1").find(".odometer-position").html(countPosition);
    });

    // end odometer

    /* ==================================================
    # Select Style
   ===============================================*/

    // Iterate over each select element
    $("select").each(function () {
        // Cache the number of options
        var $this = $(this),
            numberOfOptions = $(this).children("option").length;
        $this.addClass("s-hidden"); // Hides the select element
        $this.wrap('<div class="select"></div>'); // Wrap the select element in a div
        $this.after('<div class="styledSelect"></div>'); // Insert a styled div to sit over the top of the hidden select element
        var $styledSelect = $this.next("div.styledSelect"); // Cache the styled div
        $styledSelect.text($this.children("option").eq(0).text()); // Show the first select option in the styled div

        // Insert an unordered list after the styled div and also cache the list
        var $list = $("<ul />", {
            class: "options",
        }).insertAfter($styledSelect);

        // Insert a list item into the unordered list for each select option
        for (var i = 0; i < numberOfOptions; i++) {
            $("<li />", {
                text: $this.children("option").eq(i).text(),
                rel: $this.children("option").eq(i).val(),
            }).appendTo($list);
        }

        var $listItems = $list.children("li"); // Cache the list items
        // Show the unordered list when the styled div is clicked (also hides it if the div is clicked again)
        $styledSelect.click(function (e) {
            e.stopPropagation();
            // Check if the styled select already has the "active" class
            if ($(this).hasClass("active")) {
                $(this).removeClass("active").next("ul.options").hide();
            } else {
                // If it doesn't have the class, remove it from other elements and show the options
                $("div.styledSelect.active").each(function () {
                    $(this).removeClass("active").next("ul.options").hide();
                });
                $(this).addClass("active").next("ul.options").show();
            }
        });

        // Hides the unordered list when a list item is clicked and updates the styled div to show the selected list item
        // Updates the select element to have the value of the equivalent option
        $listItems.click(function (e) {
            e.stopPropagation();
            $styledSelect.text($(this).text()).removeClass("active");
            $this.val($(this).attr("rel"));
            $list.hide();
        });

        // Hides the unordered list when clicking outside of it
        $(document).click(function () {
            $styledSelect.removeClass("active");
            $list.hide();
        });
    });

    /* ==================================================
    # end Select Style
   ===============================================*/
});
