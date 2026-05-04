"use strict";
$(document).ready(function () {
    $("#shopID").select2();
    $("#category_id").select2();
    $("#weightID").select2();
    $("#delivery_type_id").select2();
});

function toMerchant() {
    var url = $("#getProvinceAccountTypeWiseMerchant").data("url");
    var province_id = $("#to_state_id").val();
    var to_account_type = $("#to_account_type").val();
    var merchant_id = $("#merchant_id").val();
    if (province_id && to_account_type) {
        $.ajax({
            type: "POST",
            url: url,
            data: {
                type: "to",
                merchant_id: merchant_id,
                province_id: province_id,
                account_type: to_account_type,
            },
            success: function (data) {
                $("#to_merchant_id").html(data.merchant_option);
            },
        });
    }
}

function distanceMap(lat1, lon1, lat2, lon2) {
    var origin1 = new google.maps.LatLng(lat1, lon1);
    var origin2 = origin1;
    var destinationA = new google.maps.LatLng(lat2, lon2);
    var destinationB = new google.maps.LatLng(lat2, lon2);
    var service = new google.maps.DistanceMatrixService();
    service.getDistanceMatrix(
        {
            origins: [origin1, origin2],
            destinations: [destinationA, destinationB],
            travelMode: google.maps.TravelMode.DRIVING,
            unitSystem: google.maps.UnitSystem.METRIC,
            avoidHighways: false,
            avoidTolls: false,
        },
        callback
    );
    function callback(response, status) {
        if (status == "OK") {
            if (response.rows[0].elements[1].status == "OK") {
                var total_km = response.rows[0].elements[1].distance.value;
                total_km = total_km / 1000;
                $(".distance_km").val(total_km);
                $("#distance_km").html(total_km);
                return true;
            }
        } else {
            $(".distance_km").val(0);
            $("#distance_km").html(0);
            return false;
        }
    }
    return true;
}

function distanceMapKm() {
    var latitude1 = $("#pickup_lat").val();
    var longitude1 = $("#pickup_long").val();
    var latitude2 = $("#drop_latitude").val();
    var longitude2 = $("#drop_longitude").val();
    if (latitude1 && longitude1 && latitude2 && longitude2) {
        distanceMap(latitude1, longitude1, latitude2, longitude2);
        deliveryCharge();
    }
}

$(document).on("change", "#merchant_id", function () {
    var url = $(this).data("url");
    $.ajax({
        type: "POST",
        url: url,
        data: { id: $(this).val(), shop: true },
        dataType: "html",
        success: function (data) {
            $("#shopID").html(data);
            shop(url);
            deliveryCharge();
            toMerchant();
        },
    });
    cod();
});

$(document).on(
    "change",
    "#merchant_id, #to_state_id, #to_account_type",
    function () {
        var merchant_id = $("#merchant_id").val();
        var account_type = $("#to_account_type").val();
        var province_id = $("#to_state_id").val();
        if (merchant_id && province_id) {
            var urlCustomer = $("#urlCustomer").data("url");
            var payload = {
                sender_id: merchant_id,
                province_id: province_id,
            };
            if (account_type) {
                payload.account_type = account_type;
            }
            $.ajax({
                type: "POST",
                url: urlCustomer,
                data: payload,
                success: function (data) {
                    $("#customer_id").html(data);
                },
            });
        }
    }
);

$(document).on("change", "#customer_id", function () {
    var id = $("#customer_id").val();
    var url = $("#customer_id").data("url");
    if (id) {
        $.ajax({
            type: "POST",
            url: url,
            data: { id },
            success: function (data) {
                $("#to_company_name_field").hide();
                $("#customer_first_name").val(data.first_name);
                $("#customer_last_name").val(data.last_name);
                $("#customer_phone").val(data.phone_number);
                $("#receiver_email").val(data.email);
                $("#customer_address").val(data.address);
            },
        });
    }
});

$(document).on("change", "#to_merchant_id", function () {
    var url = $(this).data("url");
    $.ajax({
        type: "POST",
        url: url,
        data: { id: $(this).val(), shop: true },
        dataType: "html",
        success: function (data) {
            $("#to_shopID").html(data);
        },
    });
});

//cod charge dynamic
function cod() {
    $.ajax({
        type: "POST",
        url: $("#merchanturl").data("url"),
        data: { merchant_id: $("#merchant_id").val() },
        dataType: "json",
        success: function (data) {
            $("#inside_city").val(data.inside_city);
            $("#sub_city").val(data.sub_city);
            $("#outside_city").val(data.outside_city);
        },
    });
}
//end cod charge dynamic

$(document).on("change", "#shopID", function () {
    var url = $(this).data("url");
    shop(url);
});

function shop(url) {
    var shop_id = $("select#shopID option").filter(":selected").val();
    if ($("#merchant_id").val()) {
        $.ajax({
            type: "POST",
            url: url,
            data: { id: shop_id, shop: false },
            dataType: "html",
            success: function (data) {
                var shop = JSON.parse(data);
                console.log(shop);
                if (shop.merchant.account_type == 1) {
                    $("#company_name_field").hide();
                    $("#pickup_address").val(shop.merchant.user.address);
                } else {
                    $("#company_name_field").show();
                    $("#pickup_address").val(shop.merchant.address);
                }
                $("#pickup_phone").val(shop.contact_no);
                $("#residential_address").val(shop.address);
                $("#company_name").val(shop.merchant.business_name);
                $("#first_name").val(shop.merchant.user.first_name);
                $("#last_name").val(shop.merchant.user.last_name);
                $("#sender_email").val(shop.merchant.user.email);
                if (shop.merchant.user.location) {
                    $("#autocomplete-input").val(shop.merchant.user.location);
                    $("#pickup_lat").val(shop.merchant.user.latitude);
                    $("#pickup_long").val(shop.merchant.user.longitude);
                    var pointA = new google.maps.LatLng(shop.merchant.user.latitude, shop.merchant.user.longitude);
                    var myOptions = {
                        zoom: 15,
                        center: pointA,
                    };
                    var map = new google.maps.Map(
                        document.getElementById("mapDirection"),
                        myOptions
                    );
                    googleMapDirections();
                }
            },
        });
    }
}

$(document).on("change", "#to_shopID", function () {
    var url = $(this).data("url");
    toShop(url);
});

function toShop(url) {
    var shop_id = $("select#to_shopID option").filter(":selected").val();
    if ($("#to_merchant_id").val()) {
        $.ajax({
            type: "POST",
            url: url,
            data: { id: shop_id, shop: false },
            dataType: "html",
            success: function (data) {
                var shop = JSON.parse(data);
                if (shop.merchant.account_type == 1) {
                    $("#to_company_name_field").hide();
                    $("#pickup_address").val(shop.merchant.user.address);
                } else {
                    $("#to_company_name_field").show();
                    $("#pickup_address").val(shop.merchant.address);
                }
                $("#customer_phone").val(shop.contact_no);
                $("#customer_residential_address").val(shop.address);
                $("#customer_company_name").val(shop.merchant.business_name);
                $("#customer_first_name").val(shop.merchant.user.first_name);
                $("#customer_last_name").val(shop.merchant.user.last_name);
                $("#receiver_email").val(shop.merchant.user.email);
                $("#pickup_address").val(shop.merchant.user.address);
                if (shop.merchant.user.location) {
                    $("#autocomplete").val(shop.merchant.user.location);
                    $("#drop_latitude").val(shop.merchant.user.latitude);
                    $("#drop_longitude").val(shop.merchant.user.longitude);
                    var pointA = new google.maps.LatLng(shop.merchant.user.latitude, shop.merchant.user.longitude);
                    var myOptions = {
                        zoom: 15,
                        center: pointA,
                    };
                    var map = new google.maps.Map(
                        document.getElementById("mapDirection"),
                        myOptions
                    );
                    googleMapDirections();
                }
            },
        });
    }
}

if (category_id === "1") {
    $("#categoryWeight").show();
    $("#weightID").show();
} else {
    $("#categoryWeight").hide();
    $("#weightID").hide();
}

$(document).on("change", "#category_id", function () {
    var category_id = $(this).val();
    if (category_id !== "") {
        $.ajax({
            type: "POST",
            url: $(this).data("url"),
            data: { category_id: $(this).val() },
            dataType: "html",
            success: function (data) {
                if (category_id == "1") {
                    $("#categoryWeight").show();
                    $("#weightID").show();
                    $("#weightID").html(data);
                    $("#weightID").select2();
                } else {
                    $("#categoryWeight").hide();
                    $("#weightID").hide();
                }
                deliveryCharge();
            },
        });
    }
});

$(document).on("change", "#delivery_type_id", function () {
    deliveryCharge();
    //cod charge calculation
    var delivery_type = $(this).val();
    var cash_collection = parseFloat($("#cash_collection").val());
    if (cash_collection == "" || isNaN(cash_collection)) {
        cash_collection = 0;
    }
    var type = 0;
    if (delivery_type == 1 || delivery_type == 2) {
        type = $("#inside_city").val();
    } else if (delivery_type == 3) {
        type = $("#sub_city").val();
    } else if (delivery_type == 4) {
        type = $("#outside_city").val();
    } else {
        type = 0;
    }
    var codAmount = percentage(cash_collection, type);
    $("#codChargeAmount").text(codAmount.toFixed(2));
    //end cod charge calculation
});


if (packaging_id !== "") {
    $("#packagingShow").show();
} else {
    $("#packagingShow").hide();
}

$(document).on("change", "#packaging_id", function () {
    var amount = parseFloat(
        $("select#packaging_id option")
            .filter(":selected")
            .data("packagingamount")
    );
    if (isNaN(amount) || amount === "") {
        $("#packagingShow").hide();
        amount = 0;
    } else {
        $("#packagingShow").show();
    }

    $("#packagingAmount").text(amount);
    totalSum();
});

if (liquid_fragile_amount !== "") {
    $(".hideShowLiquidFragile").show();
} else {
    $(".hideShowLiquidFragile").hide();
}

// Fragile Liquid
function processCheck(event) {
    multipleFragileLiquid();
}

$(document).on("change", ".fragile-liquid", function () {
    multipleFragileLiquid();
});

function multipleFragileLiquid() {
    var liquidFragileAmount = 0;
    var fragiles = $(".fragile-liquid:checked");
    fragiles.each(function () {
        //outside charge
        var total_distance_km = $(".distance_km").val();
        var inside_distance_km = parseFloat($(this).data("inside-distance"));
        if (total_distance_km > inside_distance_km) {
            liquidFragileAmount += parseFloat($(this).data("outside-amount"));
            $(this).val(parseFloat($(this).data("outside-amount")));
        } else {
            liquidFragileAmount += parseFloat($(this).data("amount"));
            $(this).val(parseFloat($(this).data("amount")));
        }
        //end outside charge
    });

    if (fragiles.length > 0) {
        $(".hideShowLiquidFragile").show();
        $("#liquidFragileAmount").text(liquidFragileAmount.toFixed(2));
        $("#fragileItems").html(liquidFragileAmount.toFixed(2));
    } else {
        $(".hideShowLiquidFragile").hide();
        $("#liquidFragileAmount").text(liquidFragileAmount.toFixed(2));
        $("#fragileItems").html(liquidFragileAmount.toFixed(2));
    }
    totalSum();
}

function percentage(totalAmount, percentageAmount) {
    return totalAmount * (percentageAmount / 100);
}

$(document).on("keyup change", "#cash_collection", function () {
    var cash_collection = parseFloat($(this).val());
    if (cash_collection === "" || isNaN(cash_collection)) {
        $("#totalCashCollection").text("0.00");
        $("#codChargeAmount").text("0.00");
    } else {
        var codAmount = percentage(cash_collection, 1);
        $("#codChargeAmount").text(codAmount.toFixed(2));
        $("#totalCashCollection").text(cash_collection);
    }
    totalSum();
});

deliveryCharge();
function deliveryCharge() {
    var merchant_id = $("select#merchant_id option").filter(":selected").val();
    var shipping_type = "";
    var total_cbm = parseFloat($("#total_cbm").val());

    let distance_km = $(".distance_km").val();
    var length = $("#length").val();
    var width = $("#width").val();
    var height = $("#height").val();
    var weight = parseFloat($("#total_weight").val());

    if (isNaN(weight)) {
        weight = 0;
    }

    if (length !== "" && width !== "" && height !== "" && weight !== "") {
        $.ajax({
            type: "POST",
            url: deliverChargeUrl,
            data: {
                merchant_id: merchant_id,
                weight: weight,
                total_cbm: total_cbm,
                total_distance_km: distance_km,
            },
            dataType: "json",
            success: function (data) {
                $("#deliveryChargeAmount").text(data);
                setTimeout(() => {
                    specialDiscount();
                    totalSum();
                }, 500);
            },
        });
    }
}

function specialDiscount() {
    var special_discount = parseInt($("#special_discount").val());
    var total_delivery_fee = parseInt($("#deliveryChargeAmount").text());
    if (isNaN(special_discount)) {
        special_discount = 0;
    }
    $("#special_discount_amount").text(special_discount);
    var sub_total = (total_delivery_fee - special_discount).toFixed(2);
    $("#sub_total").text(sub_total);
}

totalSum();
function totalSum() {
    merchant();
    var totalCashCollection = 0;
    var deliveryChargeAmount = parseFloat($("#deliveryChargeAmount").text());
    deliveryChargeAmount = deliveryChargeAmount - parseInt($("#special_discount_amount").text());
    var codChargeAmount = 0;
    var vatTex = parseFloat($("#merchantVat").val());
    var merchantCodCharge = 0;
    var liquidFragileAmount = parseFloat($("#liquidFragileAmount").text());
    var rushHourServiceAmount = parseFloat($("#rushHourServiceAmount").text());
    var scheduledServiceAmount = parseFloat($("#scheduledServiceAmount").text());
    var totalExtraCost = parseFloat($("#extraCostAmount").text());
    var packagingAmount = parseFloat($("#packagingAmount").text());
    if (isNaN(packagingAmount)) {
        packagingAmount = 0;
        parseFloat($("#packagingAmount").text(packagingAmount.toFixed(2)));
    }
    var totalAmount =
        codChargeAmount +
        deliveryChargeAmount +
        liquidFragileAmount +
        rushHourServiceAmount +
        scheduledServiceAmount +
        totalExtraCost +
        packagingAmount;
    var vat = percentage(totalAmount, vatTex);
    $("#VatAmount").text(vat.toFixed(2));
    $("#totalDeliveryChargeAmount").text(totalAmount.toFixed(2));

    if ($("#discount_eligible").val() == "true") {
        //discount
        var merchant_discount = parseFloat($(".merchant_discount").val());
        var discountamount = (totalAmount / 100) * merchant_discount;
        if (isNaN(discountamount)) {
            discountamount = 0;
        }
        $("#merchant_discount").text(discountamount.toFixed(2));
        $(".merchant_discount_amount").val(discountamount);
        totalAmount -= discountamount;
        //end discount
    }

    totalAmount += vat;
    var totalCurrentAmount = (totalAmount - totalCashCollection);
    var totalShippingFee = (totalAmount - totalCashCollection);

    var who_pays_either = $("#who_pays_either").val();
    var totalAfterDelivery = 0;
    totalCurrentAmount = totalShippingFee;


    $("#netPayable").text(totalAmount.toFixed(2));
    $("#product_price").val(totalAmount.toFixed(2));
    $("#currentPayable").text('-' + totalCurrentAmount.toFixed(2));
    var totalDeliveryChargeAmount = parseFloat($("#totalDeliveryChargeAmount").text());
    var currentPayable = parseFloat($("#currentPayable").text());
    var VatAmount = parseFloat($("#VatAmount").text());
    var obj = {
        vatTex: vatTex,
        deliveryChargeAmount: deliveryChargeAmount,
        VatAmount: VatAmount,
        liquidFragileAmount: liquidFragileAmount,
        rushHourServiceAmount: rushHourServiceAmount,
        scheduledServiceAmount: scheduledServiceAmount,
        totalExtraCost: totalExtraCost,
        packagingAmount: packagingAmount,
        totalDeliveryChargeAmount: totalDeliveryChargeAmount,
        currentPayable: currentPayable,
    };
    $("#chargeDetails").val(JSON.stringify(obj));
}

function merchant() {
    var merchant_id = $("select#merchant_id option").filter(":selected").val();
    var delivery_type_id = $("select#delivery_type_id option")
        .filter(":selected")
        .val();
    if (merchant_id !== "") {
        $.ajax({
            type: "POST",
            url: merchantUrl,
            data: { search: merchant_id, searchQuery: false },
            dataType: "json",
            success: function (data) {
                var cash_collection = parseFloat($("#cash_collection").val());
                if (isNaN(cash_collection)) {
                    cash_collection = 0;
                }
                var merchantCodCharge = 0;
                var codAmount = 0;
                if (
                    (delivery_type_id !== "" && delivery_type_id === "1") ||
                    delivery_type_id === "2"
                ) {
                    merchantCodCharge = data[0].cod_charges.inside_city;
                    codAmount = parseFloat(
                        percentage(
                            cash_collection,
                            data[0].cod_charges.inside_city
                        )
                    );
                } else if (
                    delivery_type_id !== "" &&
                    delivery_type_id === "3"
                ) {
                    merchantCodCharge = data[0].cod_charges.sub_city;
                    codAmount = parseFloat(
                        percentage(
                            cash_collection,
                            data[0].cod_charges.sub_city
                        )
                    );
                } else if (
                    delivery_type_id !== "" &&
                    delivery_type_id === "4"
                ) {
                    merchantCodCharge = data[0].cod_charges.outside_city;
                    codAmount = parseFloat(
                        percentage(
                            cash_collection,
                            data[0].cod_charges.outside_city
                        )
                    );
                } else {
                    merchantCodCharge = 0;
                    codAmount = parseFloat(percentage(cash_collection, 0));
                }
                // $('#codChargeAmount').text(codAmount.toFixed(2));
                $("#merchantVat").val(data[0].vat);
                $(".merchant_discount").val(data[0].discount);
                $("#merchantCodCharge").val(merchantCodCharge);
            },
        });
    }
}

//cbm formula
$(document).on(
    "keyup",
    ".cbm-main input,#shipping_type,#cash_collection,#special_discount",
    function () {
        senderInfo();
        totalCBM();
        deliveryCharge();
        totalSum();
    }
);

$(document).on("change", "#package_type_id,#payment_mode,#who_pays_either, #sender_payment_mode", function () {
    totalCBM();
    deliveryCharge();
    totalSum();
});

totalCBM();
function totalCBM() {
    var main_item = cbmFormulaCalculation();
    var multiple_items = totalItemCalculation();
    var total_cubic_meter = main_item["total_cbm"] + multiple_items["total_item_cbm"];
    var total_weight = main_item["total_weight"] + multiple_items["total_item_weight"];
    $("#total_cbm").val(total_cubic_meter);
    $("#total_weight").val(total_weight);

    var total_cbm_weight = main_item["total_cbm_weight"] + multiple_items["total_cbm_weight"];
    $("#total_cbm_weight").val(total_cbm_weight);
    $("#total_valumetric_weight").text(total_cbm_weight.toFixed(3)+' kg'); 
    console.log(total_cbm_weight);
}

function cbmFormulaCalculation() {
    var cbm_length = parseFloat($(".cbm-main #length").val());
    var total_cbm_weight = 0;
    if (isNaN(cbm_length)) {
        cbm_length = 0;
    }
    var cbm_width = parseFloat($(".cbm-main #width").val());
    if (isNaN(cbm_width)) {
        cbm_width = 0;
    }
    var cbm_height = parseFloat($(".cbm-main #height").val());
    if (isNaN(cbm_height)) {
        cbm_height = 0;
    }
    var total_cbm = cbm_length * cbm_width * cbm_height;
    total_cbm = total_cbm / 1000000; //cbm kg

    var dimentions = cbm_length * cbm_width * cbm_height;
    total_cbm_weight = dimentions /5000;

    var quantity = parseFloat($(".cbm-main #quantity").val());
    if (isNaN(quantity)) {
        quantity = 0;
    }

    var weight = parseFloat($(".cbm-main #local_weight").val());
    if (isNaN(weight)) {
        weight = 0;
    }
    var total_weight = weight * quantity;
    total_weight = parseFloat(total_weight.toFixed(2));

    total_cbm = total_cbm * quantity;
    total_cbm = parseFloat(total_cbm.toFixed(3));

    $("#main_total_cbm").val(total_cbm);
    $("#main_total_weight").val(total_weight);
    $("#main_unit_parcel_service_cost").val(0);

    var total = [];
    total["total_cbm"] = total_cbm;
    total["total_weight"] = total_weight;
    total["total_cbm_weight"] = total_cbm_weight;
    var parcel_value = parseFloat($(".cbm-main #parcel_value").val());
    if (isNaN(parcel_value)) {
        var parcel_value = 0;
    }
    var package_type_id = $(".cbm-main #package_type_id").val();
    if (package_type_id == 1) {
        var main_parcel_value = 0;
        $(".cbm-main #parcel_value").val(0);
    } else if (package_type_id == 2) {
        var main_parcel_value = (parcel_value * quantity);
    }
    total["main_parcel_value"] = main_parcel_value;
    return total;
}

// parcel items
var item = 0;
$("#add_item").click(function () {
    item++;
    $.ajax({
        type: "get",
        url: add_item,
        data: {
            item_number: item,
        },
        dataType: "json",
        success: function (data) {
            $(".cbm-row").append(data.view);
            totalCBM();
        },
    });
});

$(document).on("click", ".cloneMainParcelItem", function () {
    const package_type_id = $(this).closest(".cbm-box").find("#package_type_id").val();
    const length = $(this).closest(".cbm-box").find("#length").val();
    const width = $(this).closest(".cbm-box").find("#width").val();
    const height = $(this).closest(".cbm-box").find("#height").val();
    const local_weight = $(this).closest(".cbm-box").find("#local_weight").val();
    const quantity = $(this).closest(".cbm-box").find("#quantity").val();
    const category_id = $(this).closest(".cbm-box").find("#main_category_id").val();
    const fragilliquid = $(this).closest(".cbm-box").find(".fragile-liquid:checked").val();
    const parcel_with_insurance = $(this).closest(".cbm-box").find(".parcel_with_insurance:checked").val();
    const rush_hour_service = $(this).closest(".cbm-box").find("#rush_hour_service:checked").val();
    const extra_cost = $(this).closest(".cbm-box").find("#extra_cost:checked").val();
    const extra_cost_amount = $(this).closest(".cbm-box").find("#extra_cost_amount").val();
    const extra_cost_description = $(this).closest(".cbm-box").find("#extra_cost_description").val();
    const packaging_id = $(this).closest(".cbm-box").find("#packaging_id").val();
    const description = $(this).closest(".cbm-box").find("#main_description").val();
    const item_total_weight = $(this).closest(".cbm-box").find("#main_total_weight").val();
    const item_total_cbm = $(this).closest(".cbm-box").find("#main_total_cbm").val();
    const content_parcel = $(this).closest(".cbm-box").find("#content_parcel").val();
    const parcel_value = $(this).closest(".cbm-box").find("#parcel_value").val();

    item++;
    $.ajax({
        type: "get",
        url: add_item,
        data: {
            type: "clone",
            item_number: item,
            package_type_id: package_type_id,
            length: length,
            width: width,
            height: height,
            local_weight: local_weight,
            quantity: quantity,
            category_id: category_id,
            fragilliquid: fragilliquid,
            parcel_with_insurance: parcel_with_insurance,
            rush_hour_service: rush_hour_service,
            extra_cost: extra_cost,
            extra_cost_amount: extra_cost_amount,
            extra_cost_description: extra_cost_description,
            packaging_id: packaging_id,
            description: description,
            item_total_weight: item_total_weight,
            item_total_cbm: item_total_cbm,
            content_parcel: content_parcel,
            parcel_value: parcel_value,
        },
        dataType: "json",
        success: function (data) {
            $(".cbm-row").append(data.view);
            totalCBM();
            multipleFragileLiquid();
            multipleRushHourServiceCheck();
            multipleExtraCostMainCheck();
            multiplePackagingServiceCheck();
        },
    });
});

$(document).on("click", ".clone-parcel-item", function () {
    const package_type_id = $(this).closest(".cbm-box").find("#items_package_type_id").val();
    const length = $(this).closest(".cbm-box").find("#length").val();
    const width = $(this).closest(".cbm-box").find("#width").val();
    const height = $(this).closest(".cbm-box").find("#height").val();
    const local_weight = $(this).closest(".cbm-box").find("#local_weight").val();
    const quantity = $(this).closest(".cbm-box").find("#quantity").val();
    const category_id = $(this).closest(".cbm-box").find("#category_id").val();
    const description = $(this).closest(".cbm-box").find("#description").val();
    const fragilliquid = $(this).closest(".cbm-box").find(".fragile-liquid:checked").val();
    const parcel_with_insurance = $(this).closest(".cbm-box").find(".parcel_with_insurance:checked").val();
    const rush_hour_service = $(this).closest(".cbm-box").find(".rush_hour_service:checked").val();
    const extra_cost = $(this).closest(".cbm-box").find(".extra_cost:checked").val();
    const extra_cost_amount = $(this).closest(".cbm-box").find(".extra_cost_amount").val();
    const extra_cost_description = $(this).closest(".cbm-box").find(".extra_cost_description").val();
    const packaging_id = $(this).closest(".cbm-box").find(".packaging_id").val();
    const item_total_weight = $(this).closest(".cbm-box").find("#item_total_weight").val();
    const item_total_cbm = $(this).closest(".cbm-box").find("#item_total_cbm").val();
    const content_parcel = $(this).closest(".cbm-box").find("#content_parcel").val();
    const parcel_value = $(this).closest(".cbm-box").find("#parcel_value").val();

    item++;
    $.ajax({
        type: "get",
        url: add_item,
        data: {
            type: "clone",
            item_number: item,
            package_type_id: package_type_id,
            length: length,
            width: width,
            height: height,
            local_weight: local_weight,
            quantity: quantity,
            category_id: category_id,
            description: description,
            fragilliquid: fragilliquid,
            parcel_with_insurance: parcel_with_insurance,
            rush_hour_service: rush_hour_service,
            extra_cost: extra_cost,
            extra_cost_amount: extra_cost_amount,
            extra_cost_description: extra_cost_description,
            packaging_id: packaging_id,
            item_total_weight: item_total_weight,
            item_total_cbm: item_total_cbm,
            content_parcel: content_parcel,
            parcel_value: parcel_value
        },
        dataType: "json",
        success: function (data) {
            $(".cbm-row").append(data.view);
            totalCBM();
            multipleFragileLiquid();
            multipleRushHourServiceCheck();
            multipleExtraCostMainCheck();
            multiplePackagingServiceCheck();
        },
    });
});

$(document).on("click", ".remove-parcel-item", function () {
    $(this).closest(".cbm-box").remove();
    totalCBM();
    multipleFragileLiquid();
    multipleRushHourServiceCheck();
    multipleExtraCostMainCheck();
    multiplePackagingServiceCheck();
});

$(document).on("keyup", ".cbm-item input,#shipping_type,#cash_collection,#special_discount", function () {
    totalCBM();
    deliveryCharge();
    totalSum();
});

$(document).on("change", "#items_package_type_id,#payment_mode,#who_pays_either, #sender_payment_mode", function () {
    totalCBM();
    deliveryCharge();
    totalSum();
});

function totalItemCalculation() {
    var total_row = $(".cbm-item");
    var total_cubic_meter = 0;
    var total_weight = 0;
    var total_cbm_weight = 0;
    var total_item_parcel_value = 0;
    total_row.each(function () {
        var length = parseFloat($(this).find("#length").val());
        if (isNaN(length)) {
            length = 0;
        }
        var width = parseFloat($(this).find("#width").val());
        if (isNaN(width)) {
            width = 0;
        }
        var height = parseFloat($(this).find("#height").val());
        if (isNaN(height)) {
            height = 0;
        }

        var cubic_centimeter = length * width * height;
        var cubic_meter = cubic_centimeter / 1000000; //cubic meter
        total_cbm_weight  += cubic_centimeter / 5000;// valumetric weight 
        var quantity = parseFloat($(this).find("#quantity").val());
        if (isNaN(quantity)) {
            quantity = 0;
        }

        var weight = parseFloat($(this).find("#local_weight").val());
        if (isNaN(weight)) {
            weight = 0;
        }
        var total_single_weight = weight * quantity;
        total_weight += parseFloat(total_single_weight.toFixed(2));
        $(this).find("#item_total_weight").val(total_single_weight);

        cubic_meter = cubic_meter * quantity;
        cubic_meter = parseFloat(cubic_meter.toFixed(3));

        $(this).find("#item_total_cbm").val(cubic_meter);
        $(this).find("#unit_parcel_service_cost").val(0);
        total_cubic_meter += cubic_meter;
        var parcel_value = parseFloat($(this).find("#parcel_value").val());
        if (isNaN(parcel_value)) {
            var parcel_value = 0;
        }
        var package_type_id = $(this).find("#items_package_type_id").val();
        if (package_type_id == 1) {
            var item_parcel_value = 0;
            $(this).find("#parcel_value").val(0);
        } else if (package_type_id == 2) {
            var item_parcel_value = (parcel_value * quantity);
        }
        total_item_parcel_value += item_parcel_value;
    });
    var total = [];
    total["total_item_cbm"] = total_cubic_meter;
    total["total_item_weight"] = total_weight;
    total["total_cbm_weight"]  = total_cbm_weight;
    total["item_parcel_value"] = total_item_parcel_value;

    multipleFragileLiquid();
    multipleRushHourServiceCheck();
    multipleExtraCostMainCheck();
    multiplePackagingServiceCheck();
    return total;
}
//end parcel items

$(document).on("change", "#shipping_type,#location,#merchant_id", function () {
    $("#codChargeAmount").text(0);
    $("#merchantCodCharge").val(0);
    totalCBM();
    deliveryCharge();
    totalSum();
});

// delivery charge location
$(document).on("change", "#delivery_type_id", function () {
    toLocation();
    deliveryCharge();
});

//maps with distance
google.maps.event.addDomListener(window, "load", initialize);
var geocoder;
function initialize() {
    var input = document.getElementById("autocomplete-input");
    var autocomplete = new google.maps.places.Autocomplete(input);
    autocomplete.addListener("place_changed", function () {
        var place = autocomplete.getPlace();
        $("#pickup_lat").val(place.geometry["location"].lat());
        $("#pickup_long").val(place.geometry["location"].lng());
        const LatlngData = {
            lat: parseFloat(place.geometry["location"].lat()),
            lng: parseFloat(place.geometry["location"].lng()),
        };
        new google.maps.Map(document.getElementById("googleMap"), {
            zoom: 15,
            center: LatlngData,
        });
        googleMapDirections();
    });
}

function getLocation() {
    geocoder = new google.maps.Geocoder();
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function (position) {
            getLatLongPosition(
                position.coords.latitude,
                position.coords.longitude
            );
            var Latitude = position.coords.latitude;
            var Longitude = position.coords.longitude;
            var latlng = new google.maps.LatLng(Latitude, Longitude);
            distanceMapKm();
            geocoder.geocode(
                {
                    latLng: latlng,
                },
                function (results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                        if (results[1]) {
                            $("#autocomplete-input").val(
                                results[0].formatted_address
                            );
                        }
                    }
                }
            );
        });
    } else {
        alert("Geolocation is not supported by this browser.");
        return false;
    }
}

function getLatLongPosition(latitude, longitude) {
    const myLatlng = {
        lat: parseFloat(latitude),
        lng: parseFloat(longitude),
    };
    const map = new google.maps.Map(document.getElementById("googleMap"), {
        zoom: 15,
        center: myLatlng,
    });
}

google.maps.event.addDomListener(window, "load", initializeTwo);
function initializeTwo() {
    var inputOne = document.getElementById("autocomplete");
    var autocompleteOne = new google.maps.places.Autocomplete(inputOne);
    autocompleteOne.addListener("place_changed", function () {
        var placeOne = autocompleteOne.getPlace();
        $("#drop_latitude").val(placeOne.geometry["location"].lat());
        $("#drop_longitude").val(placeOne.geometry["location"].lng());
        const myLatlng = {
            lat: parseFloat(placeOne.geometry["location"].lat()),
            lng: parseFloat(placeOne.geometry["location"].lng()),
        };
        new google.maps.Map(document.getElementById("googleMapTwo"), {
            zoom: 15,
            center: myLatlng,
        });

        googleMapDirections();
        setTimeout(() => {
            shippingTypes();
            processCheck();
        }, 500);
        setTimeout(() => {
            deliveryCharge();
            totalSum();
        }, 1000);
    });
}

// setInterval(function () {
// pickAndDeliveryInfo();
// }, 1000);

var latitudec = $("#pickup_lat").val();
var longitudec = $("#pickup_long").val();
if (latitudec && longitudec) {
    getPositionData();
}

function getPositionData() {
    var latitude1 = $("#pickup_lat").val();
    var longitude1 = $("#pickup_long").val();
    var latitude2 = $("#drop_latitude").val();
    var longitude2 = $("#drop_longitude").val();

    const myLatlngOne = {
        lat: parseFloat(latitude1),
        lng: parseFloat(longitude1),
    };
    const mapOne = new google.maps.Map(document.getElementById("googleMap"), {
        zoom: 15,
        center: myLatlngOne,
    });

    const myLatlngTwo = {
        lat: parseFloat(latitude2),
        lng: parseFloat(longitude2),
    };
    const mapTwo = new google.maps.Map(
        document.getElementById("googleMapTwo"),
        {
            zoom: 15,
            center: myLatlngTwo,
        }
    );
}

shippingTypes();
function shippingTypes() {
    var from_point = parseInt($("#from_point").val());
    var to_point = parseInt($("#to_point").val());
    var type = "door_to_door";
    if (from_point == 1 && to_point == 1) {
        type = "door_to_door";
    } else if (from_point == 1 && to_point == 2) {
        type = "door_to_hub";
    } else if (from_point == 2 && to_point == 1) {
        type = "hub_to_door";
    } else if (from_point == 2 && to_point == 2) {
        type = "hub_to_hub";
    }

    let total_km = $(".distance_km").val();
    var from_state_id = $("#from_state_id").val();
    var to_state_id = $("#to_state_id").val();
    if (from_state_id && to_state_id) {
        $.ajax({
            type: "GET",
            url: get_shipping_type_url,
            dataType: "html",
            data: {
                from_state_id: from_state_id,
                to_state_id: to_state_id,
                total_distance_km: total_km,
                type: type,
            },
            success: function (data) {
                if (data) {
                    $("#submitDisabled").attr("disabled", false);
                    $("#shipping_type").html(data);
                    $("#shippingTypeText").text(
                        $("#shipping_type :selected").text()
                    );
                    if ($("#from_state_id").val() == $("#to_state_id").val()) {
                        $("#sideTypeText").text('Inside');
                    } else {
                        $("#sideTypeText").text('Outside');
                    }
                } else {
                    $("#shippingTypeText").text("");
                    $("#submitDisabled").attr("disabled", true);
                    Swal.fire({
                        title: "Shipping Type",
                        text: "The selected shipping service is currently not available. Please try to select a different collection and delivery point and check the service availability again.",
                        position: "top",
                        showCancelButton: true,
                        confirmButtonText: yes,
                        cancelButtonText: cancel,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // console.log("ok");
                        }
                    });
                }
                senderInfo();
            },
        });
    }
}

$("#from_account_type").change(function () {
    var from_account_type = $("#from_account_type").val();
    if (from_account_type == 1) {
        $("#customer_id").val("");
        $(".toAccountType").removeClass("d-none");
        $(".toAccountType").addClass("d-block");

        $(".toCustomerType").removeClass("d-block");
        $(".toCustomerType").addClass("d-none");
    } else if (from_account_type == 2) {
        $("#to_merchant_id").val("");
        $(".toAccountType").removeClass("d-block");
        $(".toAccountType").addClass("d-none");

        $(".toCustomerType").removeClass("d-none");
        $(".toCustomerType").addClass("d-block");
    }
});

$("#from_state_id, #from_account_type").change(function () {
    var url = $("#getProvinceAccountTypeWiseMerchant").data("url");
    var province_id = $("#from_state_id").val();
    var from_account_type = $("#from_account_type").val();
    if (province_id && from_account_type) {
        $.ajax({
            type: "POST",
            url: url,
            data: {
                type: "from",
                province_id: province_id,
                account_type: from_account_type,
            },
            success: function (data) {
                $("#merchant_id").html(data.merchant_option);
            },
        });
    }
});

$("#to_state_id, #to_account_type").change(function () {
    var url = $("#getProvinceAccountTypeWiseMerchant").data("url");
    var province_id = $("#to_state_id").val();
    var to_account_type = $("#to_account_type").val();
    var merchant_id = $("#merchant_id").val();
    $("#to_merchant_id").html('');
    if (province_id && to_account_type) {
        $.ajax({
            type: "POST",
            url: url,
            data: {
                type: "to",
                merchant_id: merchant_id,
                province_id: province_id,
                account_type: to_account_type,
            },
            success: function (data) {
                $("#to_merchant_id").html(data.merchant_option);
            },
        });
    }
});

$(".from_point_button button").click(function () {
    var type = $(this).data("type");
    $("#from_point").val(type);
    shippingTypes();

    if (type == 1) {
        $(".from_location_heading").html("Pickup point");
        $("#autocomplete-input").attr("placeholder", "Enter Pickup point");
        getFromHubInfo();
        $(".fromOnlyShowDoor").removeClass("d-none");
        $(".fromOnlyShowDoor").addClass("d-block");

        $(".fromPointButton").removeClass("d-block");
        $(".fromPointButton").addClass("d-none");
    } else if (type == 2) {
        $(".from_location_heading").html("Drop off point");
        $("#autocomplete-input").attr(
            "placeholder",
            "Enter Drop off point"
        );
        getFromHubInfo();
        $(".fromOnlyShowDoor").removeClass("d-block");
        $(".fromOnlyShowDoor").addClass("d-none");

        $(".fromPointButton").removeClass("d-none");
        $(".fromPointButton").addClass("d-block");
    }
});

$(".to_point_button button").click(function () {
    var type = $(this).data("type");
    $("#to_point").val(type);
    shippingTypes();

    if (type == 1) {
        $(".to_location_heading").html("Delivery point");
        $("#autocomplete").attr("placeholder", "Enter Delivery point");
        getToHubInfo();
        $(".toOnlyShowDoor").removeClass("d-none");
        $(".toOnlyShowDoor").addClass("d-block");

        $(".toPointButton").removeClass("d-block");
        $(".toPointButton").addClass("d-none");
    } else if (type == 2) {
        $(".to_location_heading").html("Drop off point");
        $("#autocomplete").attr("placeholder", "Enter Drop off point");
        getToHubInfo();
        $(".toOnlyShowDoor").removeClass("d-block");
        $(".toOnlyShowDoor").addClass("d-none");

        $(".toPointButton").removeClass("d-none");
        $(".toPointButton").addClass("d-block");
    }
});

$("#from_state_id, #to_state_id").change(function () {
    shippingTypes();
});

$("#shipping_type").change(function () {
    senderInfo();
});

senderInfo();
function senderInfo() {
    var shipping_type = $("select#shipping_type option")
        .filter(":selected")
        .val();
    if (
        shipping_type == 1 ||
        shipping_type == 2 ||
        shipping_type == 5 ||
        shipping_type == 5
    ) {
        if (shipping_type == 1 || shipping_type == 5) {
            // door to door
            if (!$(".customer_info").hasClass("d-none")) {
                $(".customer_info").addClass("d-none");
            }
            if ($(".merchant_info").hasClass("d-none")) {
                $(".merchant_info").removeClass("d-none");
            }
            if ($(".toHub2").hasClass("d-none")) {
                $(".toHub2").removeClass("d-none");
            }
        } else if (shipping_type == 2 || shipping_type == 6) {
            //door to hub
            if ($(".customer_info").hasClass("d-none")) {
                $(".customer_info").removeClass("d-none");
            }
            if (!$(".toHub2").hasClass("d-none")) {
                $(".toHub2").addClass("d-none");
            }

            if ($(".merchant_info").hasClass("d-none")) {
                $(".merchant_info").removeClass("d-none");
            }
        }
    }
    // hub to door and hub to hub
    else if (
        shipping_type == 3 ||
        shipping_type == 4 ||
        shipping_type == 7 ||
        shipping_type == 8
    ) {
        $(".merchant_info").addClass("d-none");

        if (shipping_type == 3 || shipping_type == 7) {
            // hub to door
            if ($(".customer_info").hasClass("d-none")) {
                $(".customer_info").removeClass("d-none");
            }
            if ($(".fromHub").hasClass("d-none")) {
                $(".fromHub").removeClass("d-none");
            }
            if (!$(".toHub2").hasClass("d-none")) {
                $(".toHub2").addClass("d-none");
            }
        } else if (shipping_type == 4 || shipping_type == 8) {
            //hub to hub
            if ($(".customer_info").hasClass("d-none")) {
                $(".customer_info").removeClass("d-none");
            }
            if ($(".fromHub").hasClass("d-none")) {
                $(".fromHub").removeClass("d-none");
            }
            $(".customer_info").addClass("d-none");
            if ($(".toHub2").hasClass("d-none")) {
                $(".toHub2").removeClass("d-none");
            }
        }
    } else {
        if ($(".merchant_info").hasClass("d-none")) {
            $(".merchant_info").removeClass("d-none");
        }
    }
}

$("#schedule,#pickup_type,#tomorrow").on("click", function () {
    if ($("#schedule").is(":checked")) {
        $(".scheduledField").removeClass("d-none");
    } else {
        $(".scheduledField").removeClass("d-none");
        $(".scheduledField").addClass("d-none");
    }
    scheduledMainServiceCheck();
});

$("#from_state_id").on("change", function () {
    var url = $(this).data("url");
    var id = $(this).val();
    $("#from_hub_id").html("");
    $("#from_portal_code").val("");
    $.ajax({
        type: "POST",
        url: url,
        data: { id },
        success: function (data) {
            var cityOptions = "";
            if (data && data.city_option) {
                cityOptions = data.city_option;
            } else if (Array.isArray(data)) {
                cityOptions = '<option value="">---Select City---</option>';
                data.forEach(function (city) {
                    cityOptions += '<option value="' + city.id + '">' + city.name + "</option>";
                });
            }
            if (data && data.hubs_option) {
                $("#from_hub_id").html(data.hubs_option);
            }
            $("#from_city_id").html(cityOptions);
        },
    });
});

$("#from_city_id").on("change", function () {
    var url = $(this).data("url");
    var id = $(this).val();
    $("#from_portal_code").val("");
    if (id) {
        $.ajax({
            type: "POST",
            url: url,
            data: { id },
            success: function (data) {
                $("#from_portal_code").val(data);
            },
        });
    }
});

$("#to_state_id").on("change", function () {
    var url = $(this).data("url");
    var id = $(this).val();
    $("#to_hub_id").html("");
    $("#to_portal_code").val("");
    $.ajax({
        type: "POST",
        url: url,
        data: { id },
        success: function (data) {
            var cityOptions = "";
            if (data && data.city_option) {
                cityOptions = data.city_option;
            } else if (Array.isArray(data)) {
                cityOptions = '<option value="">---Select City---</option>';
                data.forEach(function (city) {
                    cityOptions += '<option value="' + city.id + '">' + city.name + "</option>";
                });
            }
            if (data && data.hubs_option) {
                $("#to_hub_id").html(data.hubs_option);
            }
            $("#to_city_id").html(cityOptions);
        },
    });
});

$("#to_city_id").on("change", function () {
    var url = $(this).data("url");
    var id = $(this).val();
    $("#to_portal_code").val("");
    if (id) {
        $.ajax({
            type: "POST",
            url: url,
            data: { id },
            success: function (data) {
                $("#to_portal_code").val(data);
            },
        });
    }
});

$("#payment_mode").on("change", function () {
    var payment_mode = $("#payment_mode").val();
    if (payment_mode == 1) {
        $(".paymentMethod").removeClass("d-none");
        $(".paymentMethod").addClass("d-block");
    } else {
        $(".paymentMethod").removeClass("d-block");
        $(".paymentMethod").addClass("d-none");
    }
});

$(".cbm-main #package_type_id").on("change", function () {
    var package_type_id = $("#package_type_id").val();
    parcelValueIdCheck();
    if (package_type_id == 1) {
        $(".parcelTypeId").removeClass("d-block");
        $(".parcelTypeId").addClass("d-none");
    } else {
        $(".parcelTypeId").removeClass("d-none");
        $(".parcelTypeId").addClass("d-block");
    }
});

$("#who_pays_either").on("change", function () {
    var who_pays_either = $(this).val();
    var data_val = "";
    if (who_pays_either == 1) {
        data_val += '<option value="">--Select Payment Mode--</option>';
        data_val += '<option value="3">Term</option>';
        data_val += '<option value="1">Prepaid</option>';
        $("#sender_payment_mode").html(data_val);
    } else {
        data_val += '<option value="">--Select Payment Mode--</option>';
        data_val += '<option value="2">COD</option>';
        data_val += '<option value="1">Prepaid</option>';
        $("#sender_payment_mode").html(data_val);
    }
});

$(document).on("change", ".cbm-item #items_package_type_id", function () {
    var items_package_type_id = $(this).closest(".cbm-item").find("#items_package_type_id").val();
    parcelValueIdCheck();
    if (items_package_type_id == 1) {
        // Document
        $(this).closest(".cbm-item").find(".parcelDocument").removeClass("d-none");
        $(this).closest(".cbm-item").find(".parcelDocument").addClass("d-block");

        // Parcel
        $(this).closest(".cbm-item").find(".parcelTypeId").removeClass("d-block");
        $(this).closest(".cbm-item").find(".parcelTypeId").addClass("d-none");
    } else {
        // Document
        $(this).closest(".cbm-item").find(".parcelTypeId").removeClass("d-none");
        $(this).closest(".cbm-item").find(".parcelTypeId").addClass("d-block");

        // Parcel
        $(this).closest(".cbm-item").find(".parcelDocument").removeClass("d-block");
        $(this).closest(".cbm-item").find(".parcelDocument").addClass("d-none");
    }
});


parcelValueIdCheck();
function parcelValueIdCheck() {
    var totalRow = 0;
    var total_row = $(".cbm-all");
    total_row.each(function () {
        var package_type_id = parseFloat($(this).find("#package_type_id").val());
        if (isNaN(package_type_id)) {
            package_type_id = 0;
        }
        if (package_type_id == 2) {
            totalRow += 1;
        }
        var items_package_type_id = parseFloat($(this).find("#items_package_type_id").val());
        if (isNaN(items_package_type_id)) {
            items_package_type_id = 0;
        }
        if (items_package_type_id == 2) {
            totalRow += 1;
        }
    });
    $("#package_total_row").val(totalRow);
    if (totalRow > 0) {
        $(".parcelValueId").removeClass("d-none");
        $(".parcelValueId").addClass("d-block");
    } else {
        $(".parcelValueId").removeClass("d-block");
        $(".parcelValueId").addClass("d-none");
    }
}

//Start rush Hour Service
$(".rushHourService").hide();
multipleRushHourServiceCheck();
function rushHourServiceCheck(event) {
    multipleRushHourServiceCheck();
}

$(document).on("change", ".rush_hour_service", function () {
    multipleRushHourServiceCheck();
});

function multipleRushHourServiceCheck() {
    var rushHourServiceAmount = 0;
    var rush_1_hour_service = $(".rush_hour_service:checked");
    rush_1_hour_service.each(function () {
        //outside charge
        var total_distance_km = $(".distance_km").val();
        var inside_distance_km = parseFloat($(this).data("inside-distance"));
        if (total_distance_km > inside_distance_km) {
            rushHourServiceAmount += parseFloat($(this).data("outside-amount"));
            $(this).val(parseFloat($(this).data("outside-amount")));
        } else {
            rushHourServiceAmount += parseFloat($(this).data("amount"));
            $(this).val(parseFloat($(this).data("amount")));
        }
        //end outside charge
    });

    if (rush_1_hour_service.length > 0) {
        $(".rushHourService").show();
        $("#rushHourServiceAmount").text(rushHourServiceAmount.toFixed(2));
        $("#rush_hour_service").val(rushHourServiceAmount.toFixed(2));
    } else {
        $(".rushHourService").hide();
        $("#rushHourServiceAmount").text(rushHourServiceAmount.toFixed(2));
        $("#rush_hour_service").val(rushHourServiceAmount.toFixed(2));
    }
    totalSum();
}
//End rush Hour Service

//Start Scheduled Service
$(".scheduledService").hide();
scheduledMainServiceCheck();
function scheduledMainServiceCheck() {
    var scheduledServiceAmount = 0;
    var pickup_type = $('input[name="pick_type"]:checked').val();
    if (pickup_type == 3) {
        var total_distance_km = $(".distance_km").val() - 0;
        var inside_distance_km = parseFloat($("#scheduled").data("inside-distance"));
        if (total_distance_km > inside_distance_km) {
            //Inside charge
            scheduledServiceAmount += parseFloat($("#scheduled").data("outside-amount"));
            $("#scheduled").val(parseFloat($("#scheduled").data("outside-amount")));
        } else {
            //Outside charge
            scheduledServiceAmount += parseFloat($("#scheduled").data("amount"));
            $("#scheduled").val(parseFloat($("#scheduled").data("amount")));
        }
        $(".scheduledService").show();
        $("#scheduledServiceAmount").text(scheduledServiceAmount.toFixed(2));
        $("#scheduled").val(scheduledServiceAmount.toFixed(2));
    } else {
        $(".scheduledService").hide();
        $("#scheduledServiceAmount").text(scheduledServiceAmount.toFixed(2));
        $("#scheduled").val(scheduledServiceAmount.toFixed(2));
    }
    totalSum();
}
//End Scheduled Service

//Start Extra Cost Service
function extraCostMainCheck(event) {
    if (event.checked) {
        $(".extraCostCheck1").removeClass("d-none");
        $(".extraCostCheck1").addClass("d-block");
        $("#extra_cost_amount").val(0);
        $("#extra_cost_description").val('');
    } else {
        $(".extraCostCheck1").removeClass("d-block");
        $(".extraCostCheck1").addClass("d-none");
        $("#extra_cost_amount").val(0);
        $("#extra_cost_description").val('');
    }
    multipleExtraCostMainCheck();
}

$(document).on("click", ".cbm-item .extra_cost", function () {
    const scheduledData = $(this)
        .closest(".cbm-box")
        .find(".extra_cost:checked")
        .val();
    if (scheduledData || scheduledData == "") {
        $(this)
            .closest(".cbm-box")
            .find(".extraCostCheck")
            .removeClass("d-none");
        $(this).closest(".cbm-box").find(".extraCostCheck").addClass("d-block");
        $(this).closest(".cbm-box").find(".extra_cost_amount").val(0);
        $(this).closest(".cbm-box").find(".extra_cost_description").val('');
    } else {
        $(this)
            .closest(".cbm-box")
            .find(".extraCostCheck")
            .removeClass("d-block");
        $(this).closest(".cbm-box").find(".extraCostCheck").addClass("d-none");
        $(this).closest(".cbm-box").find(".extra_cost_amount").val(0);
        $(this).closest(".cbm-box").find(".extra_cost_description").val('');
    }
    multipleExtraCostMainCheck();
});

$(document).on("change, keyup", ".extra_cost,.extra_cost_amount", function () {
    multipleExtraCostMainCheck();
});

$(".extraCostCheckService").show();
function multipleExtraCostMainCheck() {
    var extraCostAmount = 0;
    var extra_cost = $(".extra_cost:checked");
    $(".extra_cost_amount").each(function (i, e) {
        extraCostAmount += parseFloat($(this).val() - 0);
    });

    if (extra_cost.length > 0) {
        $(".extraCostCheckService").show();
        $("#extraCostAmount").text(extraCostAmount.toFixed(2));
        $("#total_extra_cost").val(extraCostAmount.toFixed(2));
    } else {
        $(".extraCostCheckService").hide();
        $("#extraCostAmount").text(extraCostAmount.toFixed(2));
        $("#total_extra_cost").val(extraCostAmount.toFixed(2));
    }
    totalSum();
}
//End Extra Cost Service

//Start Packaging Service
$(".packagingShow").hide();
multiplePackagingServiceCheck();
function packagingServiceCheck(event) {
    multiplePackagingServiceCheck();
}

$(document).on("change", ".packaging_id", function () {
    multiplePackagingServiceCheck();
});

function multiplePackagingServiceCheck() {
    var packagingServiceAmount = 0;
    var packaging = $("select.packaging_id option").filter(":selected");
    packaging.each(function () {
        if ($(this).filter(":selected").data("packagingamount")) {
            packagingServiceAmount += parseFloat(
                $(this).filter(":selected").data("packagingamount")
            );
        }
    });

    if (packaging.length > 0) {
        $(".packagingShow").show();
        $("#packagingAmount").html(packagingServiceAmount.toFixed(2));
    } else {
        $(".packagingShow").hide();
        $("#packagingAmount").html(packagingServiceAmount.toFixed(2));
    }
    totalSum();
}
//End Packaging Service

function pickAndDeliveryInfo() {
    var shipping_type = $("#shipping_type").val();
    if (shipping_type == 1) {
        $(".fromHub").hide();
        $(".toHub").hide();
        $(".customerInfo").show();
    } else if (shipping_type == 2) {
        $(".fromHub").hide();
        $(".toHub").show();
        $(".customerInfo").hide();
    } else if (shipping_type == 3) {
        $(".fromHub").show();
        $(".toHub").hide();
        $(".customerInfo").show();
    } else if (shipping_type == 4) {
        $(".fromHub").show();
        $(".toHub").show();
        $(".customerInfo").hide();
    }

    var total_row = $(".cbm-item");
    var total_length = total_row.length + 1;
    $("#numberOfParcels").html(total_length);
    var main_total_weight = $("#total_weight").val();
    $("#totalWeight").html(main_total_weight);
    shippingTypeCharge();
}

shippingTypeCharge();
function shippingTypeCharge() {
    var wamount = parseFloat(
        $("select#shipping_type option").filter(":selected").data("wamount")
    );
    var vamount = parseFloat(
        $("select#shipping_type option").filter(":selected").data("vamount")
    );
    var wweight = parseFloat(
        $("select#shipping_type option").filter(":selected").data("weight")
    );
    var vvolume = parseFloat(
        $("select#shipping_type option").filter(":selected").data("volume")
    );
    const totalCBM = parseFloat($("#total_cbm").val());
    if (isNaN(wamount)) {
        wamount = 0;
    }

    if (isNaN(vamount)) {
        vamount = 0;
    }

    if (isNaN(wweight)) {
        wweight = "";
    }

    if (isNaN(vvolume)) {
        vvolume = "";
    }

    $("#first_weight").text(wweight);
    $("#first_weight_amount").text(wamount);
    $("#first_volume").text(vvolume);
    if (totalCBM > 0) {
        $("#first_volume_amount").text(vamount);
    } else {
        $("#first_volume_amount").text(0);
    }
    $("#totalVolume").html(totalCBM.toFixed(3));
}

getFromHubInfo();
function getFromHubInfo() {
    var id = $("#from_hub_id").val();
    if (id == "") {
        $("#from_hub_name").html("");
        $("#from_hub_phone_number").html("");
        $("#from_hub_email").html("");
        $("#from_hub_residential_address").html("");
    }
    $.ajax({
        type: "GET",
        url: getFromHubInfoUrl + id,
        dataType: "json",
        success: function (data) {
            $("#from_hub_name").html(data.name);
            $("#from_hub_phone_number").html(data.phone);
            $("#hub_from_whatsapp_number").html(data.whatsapp_number);
            $("#from_hub_email").html(data.email);
            $("#from_hub_residential_address").html(data.address);
            $("#pickup_lat").val(data.location_lat);
            $("#pickup_long").val(data.location_long);
            $("#autocomplete-inputs").val(data.location);
            // Call Map
            googleMapDirections();
        },
    });
}

getToHubInfo();
function getToHubInfo() {
    var id = $("#to_hub_id").val();

    if (id == "") {
        $("#to_hub_name").html("");
        $("#to_hub_residential_address").html("");
        $("#to_hub_phone_number").html("");
        $("#to_hub_email").html("");
    }
    $.ajax({
        type: "GET",
        url: getFromHubInfoUrl + id,
        dataType: "json",
        success: function (data) {
            $("#to_hub_phone_number").html(data.phone);
            $("#to_whatsapp_number").html(data.whatsapp_number);
            $("#to_hub_email").html(data.email);
            $("#to_hub_name").html(data.name);
            $("#to_hub_residential_address").html(data.address);
            $("#autocomplete").val(data.location);
            $("#drop_latitude").val(data.location_lat);
            $("#drop_longitude").val(data.location_long);
            // Call Map
            googleMapDirections();
        },
        error: function (error) {
            // console.log(error);
        },
    });
}

// Start get Location
var geocoder;
function getLocation() {
    geocoder = new google.maps.Geocoder();
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function (position) {
            getLatLongPosition(
                position.coords.latitude,
                position.coords.longitude
            );
            var Latitude = position.coords.latitude;
            var Longitude = position.coords.longitude;
            var latlng = new google.maps.LatLng(Latitude, Longitude);
            distanceMapKm();
            geocoder.geocode(
                {
                    latLng: latlng,
                },
                function (results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                        if (results[1]) {
                            $("#autocomplete-input").val(
                                results[0].formatted_address
                            );
                        }
                    }
                }
            );
        });
    } else {
        alert("Geolocation is not supported by this browser.");
        return false;
    }
}

function getLatLongPosition(latitude, longitude) {
    const myLatlng = {
        lat: parseFloat(latitude),
        lng: parseFloat(longitude),
    };
    const map = new google.maps.Map(document.getElementById("googleMap"), {
        zoom: 15,
        center: myLatlng,
    });
}
// Ens get Location

//Start Google Map Directions
googleMapDirections();
function googleMapDirections() {
    var pointA = new google.maps.LatLng(23.7956037, 90.3536548);
    var myOptions = {
        zoom: 15,
        center: pointA,
    };
    var map = new google.maps.Map(
        document.getElementById("mapDirection"),
        myOptions
    );
    // Instantiate a directions service.
    var directionsService = new google.maps.DirectionsService();
    var directionsDisplay = new google.maps.DirectionsRenderer({
        map: map,
    });
    // get route from A to B
    var originAddress = $("#autocomplete-input").val();
    var destinationAddress = $("#autocomplete").val();
    if (originAddress && destinationAddress) {
        directionsService.route(
            {
                origin: originAddress,
                destination: destinationAddress,
                avoidTolls: true,
                avoidHighways: false,
                provideRouteAlternatives: false,
                travelMode: google.maps.TravelMode.DRIVING,
                unitSystem: google.maps.UnitSystem.METRIC,
            },
            function (response, status) {
                if (status == google.maps.DirectionsStatus.OK) {
                    directionsDisplay.setDirections(response);
                } else {
                    window.alert("Directions request failed due to " + status);
                }
            }
        );
    }
    distanceMapKm();
    scheduledMainServiceCheck();
}
//End Google Map Directions
