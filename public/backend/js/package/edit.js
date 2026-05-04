$(document).ready(function () {
    $('#shipping_type').select2();
    $('#hub_id').select2();
    $('#receiver_branch').select2();
    $('.selectSet').select2();

    var i = parseInt($('#parcel_track_id').data('count'));
    var response = false;
    $('#parcel_track_id').on('keyup', function () {
        const ids = $('input[name="parcel_ids[]"]').map(function () {
            return this.value;  
        }).get();

        if (response == false) {
            response = true;
            $.ajax({
                type: 'POST',
                url: $("#parcel_track_id").data('url'),
                data: { 'track_id': $('#parcel_track_id').val(), 'hub_id': $('#hub_id').val(), 'shipping_type': $('#shipping_type').val() },
                dataType: "json",
                success: function (data) {
                    console.log(data);
                    response = false;
                    if (data == 0) {
                        toastr.error('Order not found!', 'Error');
                    }
                    else if (ids.includes(data['id'].toString())) {
                        toastr.error('Already added!', 'Error');
                    }
                    else if (data.is_packaged && data.is_packaged == $('#is_packed').val()) {
                        toastr.error('This order already batched', 'Error');
                    } else {
                        toastr.success('Order added successfully.', 'Success');
                        var row = "";
                        row += "<tr>";
                        row += "<td>" + ++i + "</td>";
                        row += "<td>" + data.tracking_id + "</td>";
                        row += "<td>" + data.merchant.business_name + "</td>";
                        row += "<td>" + data.customer_phone + "</td>";
                        row += "<td id='weight'>" + data.total_weight + "</td>";
                        row += "<td id='cubic_meters'>" + data.total_cubic_meters + "</td>";
                        row += "<td id='shipping_fee'>" + data.total_delivery_amount + "</td>";
                        row += "<td id='parcel_value'>" + data.total_parcel_value + "</td>";
                        row += "<td id='current_payable'>" + data.current_payable + "</td>";
                        row += "<td><label class='rowremovebtn' style='cursor:pointer'><i  class='fa fa-trash '></i></label></td>";
                        row += '<input type="hidden" value="' + data['id'] + '" name="parcel_ids[]">';
                        row += "</tr>";
                        $('#packaging_parcel_list').append(row);
                        document.getElementById("parcel_track_id").value = '';
                        totalParcelCalculation();
                    }
                }
            });
        }
    });

    $(document).on('click', ".rowremovebtn", function () {
        $(this).parent().parent().remove();
        i--;
    });

    $(document).on('change', "#hub_id,#shipping_type", function () {
        $('#packaging_parcel_list').html('');
        i = 1;
    });
});

totalParcelCalculation();
function totalParcelCalculation() {
    var total_row = $("#packaging_parcel_list tr");
    var total_weight = 0;
    var total_cubic_meters = 0;
    var total_shipping_fee = 0;
    var total_parcel_value = 0;
    var total_current_payable = 0;
    total_row.each(function () {
        var weight = parseFloat($(this).find("#weight").text());
        if (isNaN(weight)) {
            weight = 0;
        }
        // console.log(weight);
        var cubic_meters = parseFloat($(this).find("#cubic_meters").text());
        if (isNaN(cubic_meters)) {
            cubic_meters = 0;
        }
        var shipping_fee = parseFloat($(this).find("#shipping_fee").text());
        if (isNaN(shipping_fee)) {
            shipping_fee = 0;
        }
        var parcel_value = parseFloat($(this).find("#parcel_value").text());
        if (isNaN(parcel_value)) {
            parcel_value = 0;
        }
        var current_payable = parseFloat($(this).find("#current_payable").text());
        if (isNaN(current_payable)) {
            current_payable = 0;
        }
        total_weight += weight;
        total_cubic_meters += cubic_meters;
        total_shipping_fee += shipping_fee;
        total_parcel_value += parcel_value;
        total_current_payable += current_payable;
    });
    $("#total_weight").text(total_weight.toFixed(2));
    $("#total_cubic_meters").text(total_cubic_meters.toFixed(3));
    $("#total_shipping_fee").text(total_shipping_fee.toFixed(2));
    $("#total_parcel_value").text(total_parcel_value.toFixed(2));
    $("#total_current_payable").text(total_current_payable.toFixed(2));
}

setInterval(() => {
   totalParcelCalculation();
}, 1000);