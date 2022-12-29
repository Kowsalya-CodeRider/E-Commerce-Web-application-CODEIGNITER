
$(document).ready(function() {
	
	"use strict";
    
	/*============================================
	Datatable
	==============================================*/
        $('.data_table').DataTable({
            "order": [[0, "desc"]],
            "aLengthMenu": [[15, 30, 60, 100], [15, 30, 60, 100, "All"]],
            "language": {
                "lengthMenu": "Show _MENU_",
                "search": "Search:",
                "zeroRecords": "No records found!"
            },
            "infoCallback": function (settings, start, end, max, total, pre) {
                return total > 0 ? "Number of Entries: " + total : '';
            }
        });
        
        $('#cs_datatable_currency').DataTable({
            "ordering": false,
            "aLengthMenu": [[15, 30, 60, 100], [15, 30, 60, 100, "All"]],
            "language": {
                "lengthMenu": "Show _MENU_",
                "search": "Search:",
                "zeroRecords": "No records found!"
            },
            "infoCallback": function (settings, start, end, max, total, pre) {
                return total > 0 ? "Number of Entries: " + total : '';
            }
        });
    
	/*============================================
	Filter Data
	==============================================*/
        filter_data(1);

        function filter_data(page)
        {
            $('.filter_data').html('<div id="loading" style="" ></div>');
            var action = 'fetch_data';
            //var page = 1;
            var category = get_filter('category');
            console.log(category);
            var data = {
                "action": action,
                "category": category
            };
            data[csfr_token_name] = csfr_hash;
            $.ajax({
                url: base_url+"home/fetch_data/"+page,
                method:"POST",
                dataType:"JSON",
                data:data,
                success:function(data)
                {

                    $('.filter_data').html(data.product_list);
                    $('#pagination_link').html(data.pagination_link);
                }
            })
        }

        function get_filter(class_name)
        {
            var filter = [];
            $('.'+class_name+':checked').each(function(){
                filter.push($(this).val());
            });
            return filter;
        }

        $(document).on("click", ".pagination li a", function(event){
            event.preventDefault();
            var page = $(this).data("ci-pagination-page");
            filter_data(page);
        });

        $('.common_selector').on('click', function(){
            filter_data(1);
        });   
    
	/*============================================
	Search
	==============================================*/
           $("#search").on('keyup', function(){
          if($("#search").val().length>3){
          $.ajax({
           type: "post",
           url: base_url+"home/search",
           cache: false,  
           dataType:"JSON",  
           data:'search='+$("#search").val(),
           success: function(data){
               console.log(data.product_list);

                $('#finalResult').html(data.product_list);
                $(".filter_data").addClass('d-none');
                $("#pagination_link").addClass('d-none');

           },
           error: function(){      
            alert('Error while request..');
           }
          });
          }
            $('#finalResult').html("");
            $(".filter_data").removeClass('d-none');
            $("#pagination_link").removeClass('d-none');
           });    
        

});
