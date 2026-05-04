
"use strict";
 $('#exportTable').click(function(){
     var title = $(this).data('title');
     var filename= $(this).data('filename');
 
     let options = {
    	"separator": ",",
    	"newline": "\n",
    	"quoteFields": true,
    	"excludeColumns": "",
    	"excludeRows": "",
    	"trimContent": true,
    	"filename": filename+".csv",
    	"appendTo": "#output"
    }
    
    $(".table").table2csv('download', options)
 
});

$(document).ready(function(){
    // search user
    $( "#user_id").select2({
        ajax: {
            url: $("#user_id").data('url'),
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    search: params.term,
                    searchQuery: true
                };
            },
            processResults: function (response) {

                return {
                    results: response
                };
            },
            cache: true
        }
    });


    $("#user_type").on('change',function(){
        var type = $(this).val();
        if(type == '1' ){
            $('#merchantType').show();
            $('#hubType').hide();
            $('#deliverymanType').hide();
        }else if( type == '2'){
            $('#hubType').show();
            $('#merchantType').hide();
            $('#deliverymanType').hide();
        }else if(type == '3'){
            $('#deliverymanType').show();
            $('#merchantType').hide();
            $('#hubType').hide();
        }


    });


    $( "#parcelMerchantid_" ).select2({
        ajax: {
            url: merchantUrl,
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    search: params.term,
                    searchQuery: true
                };
            },
            processResults: function (response) {
                console.log(response);
                return {

                    results: response
                };
            },
            cache: true
        }

    });


    //hub search

    $( "#income_hub_id" ).select2({
        ajax: {
            url: hubUrl,
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    search: params.term,
                    searchQuery: true
                };
            },
            processResults: function (response) {
                return {
                    results: response
                };
            },
            cache: true
        }
    });


    //delivery man search
    $( "#parcelDeliveryManID_").select2({
        ajax: {
            url: $("#parcelDeliveryManID_").data('url'),
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: function (params) {

                return {
                    search: params.term,
                    searchQuery: true
                };
            },
            processResults: function (response) {
                console.log(response);
                return {

                    results: response
                };
            },
            cache: true
        }
    });





});
