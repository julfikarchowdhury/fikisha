$(document).ready(function () {
    $("#hub_id").select2();
    $(".selectSet").select2();

    var i = 1;
    var response = false;
    $("#batch_track_id").on("keyup", function () {
        const ids = $('input[name="batch_ids[]"]')
            .map(function () {
                return this.value;
            })
            .get();

        if (response == false) {
            response = true;
            $.ajax({
                type: "POST",
                url: $("#batch_track_id").data("url"),
                data: {
                    track_id: $("#batch_track_id").val(),
                },
                dataType: "json",
                success: function (data) {
                    response = false;
                    console.log(data);
                    if (data == 0) {
                        toastr.error("Batch not found!", "Error");
                    } else if (ids.includes(data["id"].toString())) {
                        toastr.error("Already added!", "Error");
                    } else if (
                        data.is_packaged &&
                        data.is_packaged == $("#is_packed").val()
                    ) {
                        toastr.error("This batch already loaded", "Error");
                    } else {
                        toastr.success("Batch added successfully.", "Success");
                        var row = "";
                        row += "<tr>";
                        row += "<td>" + i++ + "</td>";
                        row += "<td>" + data.package_no + "</td>";
                        row += "<td>" + data.total_delivery_amount + "</td>";
                        row += "<td>" + data.total_cash_collection + "</td>";
                        row +="<td><label class='rowRemoveBtn' style='cursor:pointer'><i  class='fa fa-trash '></i></label></td>";
                        row +='<input type="hidden" value="' +data["id"] +'" name="batch_ids[]">';
                        row += "</tr>";
                        $("#packaging_list").append(row);
                        document.getElementById("batch_track_id").value = "";
                        $(".rowRemoveBtn").click(function () {
                            $(this).parent().parent().remove();
                            i--;
                        });
                    }
                },
            });
        }
    });

    $(document).on("change", "#hub_id", function () {
        $("#packaging_list").html("");
        i = 1;
    });

    $(".load-id").click(function () {
        var loadId = $(this).data("load");
        $(".modal_load_id").attr("value", loadId);
    });

    onScan.attachTo(document);
    document.addEventListener("scan", function (sScancode, data) {
        if (
            sScancode.detail.scanCode != "" &&
            sScancode.detail.scanCode != null
        ) {
            $("#loadStatus").submit();
        }
    });

    $(document).on("change", "#packageStatus", function () {
        if ($(this).val() != "") {
            $(this).closest("form").submit();
        }
    });
});
