<script type="text/javascript">
    var fromcity              = "{{ url('country/by/city') }}/";
    var fromdistric_url       = "{{ url('city/by/district') }}/";
    var fromtown_url          = "{{ url('district/by/town') }}/";
    var fromportalcode_url    = "{{ url('town/by/portal_code') }}/";
    var fromGetCityUrl        = "{{ url('town/by/city') }}/";

    var tocountries_url       = "{{ url('deliverycharge/tocountries') }}";
    var tocities_url          = "{{ url('deliverycharge/tocities') }}";
    var todistrict_url        = "{{ url('deliverycharge/todistrict') }}";
    var totownurl             = "{{ url('deliverycharge/totown') }}";
    var toportalcodeurl       = "{{ url('deliverycharge/toportalcode') }}";

    var get_shipping_type_url = "{{ route('get.shipping.type') }}";
    var add_item              = "{{ route('parcel.add.item') }}";
    // New Work
</script>
