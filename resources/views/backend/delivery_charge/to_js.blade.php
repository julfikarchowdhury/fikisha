<script type="text/javascript"> 
    $(document).ready(function(){
       
        $("#to_country_id").on('change', function() {
            var id = $(this).val();
            var op = " ";
            
            $.ajax({
                type: "GET",
                url: "{{ url('country/by/city') }}/"+id,
                dataType:'json',
                success: function(data){
                    op +='<option  value="">--Select City--</option>';
                    for (var i=0; i<data.length; i++) {
                        op +='<option  value="'+data[i].id+'">'+data[i].name+'</option>';
                    } 
                    $('#to_city_id').html(op);
                    city();
                    district();
                    town();
                }
            });
            
        });

        $("#to_city_id").on('change', function() {
            city();
            district();
            town();
        });

        $("#to_district_id").on('change', function() {
            district();
            town();
        });

        function city(){
            var id = $('#to_city_id').val();
            var op = " ";
            if(id){
                $.ajax({
                    type: "GET",
                    url: "{{ url('city/by/district') }}/"+id,
                    success: function(data){ 
                        op +='<option  value="">--Select District--</option>';
                        for (var i=0; i<data.length; i++) {
                            op +='<option  value="'+data[i].id+'">'+data[i].name+'</option>';
                        }
                        $('#to_district_id').html(op);
                    }
                });
            }else{
                op +='<option  value="">--Select District--</option>';
                $('#to_district_id').html(op);
            }
        }

        function district(){
            var id = $('#to_district_id').val();
            var op = "";
            if(id){
                $.ajax({
                    type: "GET",
                    url: "{{ url('district/by/town') }}/"+id,
                    success: function(data){ 
                        op +='<option  value="">--Select Town--</option>';
                        for (var i=0; i<data.length; i++){
                            op +='<option  value="'+data[i].id+'">'+data[i].name+'</option>';
                        }
                        $('#to_town_id').html(op);
                    }
                });
            }else{
                op +='<option  value="">--Select Town--</option>';
                $('#to_town_id').html(op);
            }
        }


        $("#to_town_id").on('change', function() {
            town();
        });

        function town(){ 
            var id = $('#to_town_id').val();
                if(id){
                    var op = " ";
                    $.ajax({
                        type: "GET",
                        url: "{{ url('town/by/portal_code') }}/"+id,
                        success: function(data){
                            $("#to_portal_code").val(data.portal_code);
                        }
                    });
                
            }else{
                op +='<option  value="">--Select Town--</option>';
                $('#to_town_id').html(op);
            }
        }

    });
</script>