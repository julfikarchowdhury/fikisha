$(document).ready(function () {
    $("#packageStatus").select2();
    $("#shipping_type").select2();
    $("#hub_id").select2();
    $("#receiver_branch").select2();
    $(".selectSet").select2();

    var i = 1;
    var response = false;
    $("#parcel_track_id").on("keyup", function () {
        const ids = $('input[name="parcel_ids[]"]')
            .map(function () {
                return this.value; 
            })
            .get();

        if (response == false) {
            response = true;
            $.ajax({
                type: "POST",
                url: $("#parcel_track_id").data("url"),
                data: {
                    track_id: $("#parcel_track_id").val(),
                    hub_id: $("#hub_id").val()
                },
                dataType: "json",
                success: function (data) {
                    response = false;
                    if (data == 0) {
                        toastr.error("Order not found!", "Error");
                    } else if (ids.includes(data["id"].toString())) {
                        toastr.error("Already added!", "Error");
                    } else if (
                        data.is_packaged &&
                        data.is_packaged == $("#is_packed").val()
                    ) {
                        toastr.error("This order already batched", "Error");
                    } else {
                        toastr.success("Order added successfully.", "Success");
                        var row = "";
                        row += "<tr>";
                        row += "<td>" + i++ + "</td>";
                        row += "<td>" + data.tracking_id + "</td>";
                        row += "<td>" + data.merchant.business_name + "</td>";
                        row += "<td>" + data.customer_phone + "</td>";
                        row += "<td>" + data.total_delivery_amount + "</td>";
                        row += "<td>" + data.cash_collection + "</td>";
                        row +="<td><label class='rowremovebtn' style='cursor:pointer'><i  class='fa fa-trash '></i></label></td>";
                        row +='<input type="hidden" value="' +data["id"] +'" name="parcel_ids[]">';
                        row += "</tr>";
                        $("#packaging_parcel_list").append(row);
                        document.getElementById("parcel_track_id").value = "";
                        $(".rowremovebtn").click(function () {
                            $(this).parent().parent().remove();
                            i--;
                        });
                    }
                },
            });
        }
    });

    $(document).on("change", "#hub_id,#shipping_type", function () {
        $("#packaging_parcel_list").html("");
        i = 1;
    });

    $(".package-id").click(function () {
        var packageId = $(this).data("package");
        $(".modal_package_id").attr("value", packageId);
    });

    onScan.attachTo(document);
    // Register event listener
    document.addEventListener("scan", function (sScancode, data) {
        if (
            sScancode.detail.scanCode != "" &&
            sScancode.detail.scanCode != null
        ) {
            $("#packageUpdate").submit();
        }
    });

    $(document).on("change", "#packageUpdate1", function () {
        if ($(this).val() != "") {
            $(this).closest("form").submit();
        }
    });

    $("#tick-all").on("click", function () {
        if (!$(this).is(":checked")) {
            $("td").closest("tr").find(".common-key").prop("checked", false);
        } else {
            if ($(this).is(":checked")) {
                $("td").closest("tr").find(".common-key").prop("checked", true);
            }
        }
        showPrintBtn();
    });

    $(".common-key").on("click", function () {
        showPrintBtn();
    });

    function showPrintBtn() {
        if ($(".common-key:checked").length > 0) {
            $(".multipleLabelPrint").show();
            var inputs = "";
            $(".common-key:checked").each(function () {
                inputs +='<input type="hidden" name="batches[]" value="' +$(this).val() +'"/>';
            });
            $("#print_label_content").html(inputs);
        } else {
            $(".multipleLabelPrint").hide();
            $("#tick-all").prop("checked", false);
            $("#print_label_content").html("");
        }
    }
});
