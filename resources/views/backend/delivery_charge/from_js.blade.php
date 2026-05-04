<script type="text/javascript"> 
    $(document).ready(function(){
       
        $("#from_country_id").on('change', function() {
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
                    $('#from_city_id').html(op);
                    city();
                    district();
                    town();
                }
            });
            
        });

        $("#from_city_id").on('change', function() {
            city();
            district();
            town();
        });

        $("#from_district_id").on('change', function() {
            district();
            town();
        });

        function city(){
            var id = $('#from_city_id').val();
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
                        $('#from_district_id').html(op);
                    }
                });
            }else{
                op +='<option  value="">--Select District--</option>';
                $('#from_district_id').html(op);
            }
        }

        function district(){
            var id = $('#from_district_id').val();
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
                        $('#from_town_id').html(op);
                    }
                });
            }else{
                op +='<option  value="">--Select Town--</option>';
                $('#from_town_id').html(op);
            }
        }


        $("#from_town_id").on('change', function() {
            town();
        });

        function town(){ 
            var id = $('#from_town_id').val();
                if(id){
                    var op = " ";
                    $.ajax({
                        type: "GET",
                        url: "{{ url('town/by/portal_code') }}/"+id,
                        success: function(data){
                            $("#from_portal_code").val(data.portal_code);
                        }
                    });
                
            }else{
                op +='<option  value="">--Select Town--</option>';
                $('#from_town_id').html(op);
            }
        }

    });
</script>