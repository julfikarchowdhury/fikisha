$(document).ready(function () {
    $("#customer_name")
        .autocomplete({
            minLength: 1,
            source: function (request, add) {
                //source
                var receivers = [];
                $.ajax({
                    url: receiverSuggestion,
                    method: "post",
                    dataType: "json",
                    async: false,
                    data: {
                        search: $("#customer_name").val(),
                    },
                    success: (response) => {
                        receivers = $.map(response, function (item) {
                            return {
                                value: item.name,
                                phone: item.phone,
                                address: item.address,
                            };
                        });
                    },
                    error: (error) => {
                        console.log(error);
                    },
                });
                add(receivers);
            },
            focus: function (event, ui) {},
            select: function (e, ui) {
                $("#customer_phone").val(ui.item.phone);
                $("#customer_address").val(ui.item.address);
            },
            autoFocus: true,
            delay: 500,
        })
        .autocomplete("instance")._renderItem = function (ul, item) {
        var string = "<div>" + item.value + "-" + item.phone + "</div>";
        return $("<li>").append(string).appendTo(ul);
    };
});
