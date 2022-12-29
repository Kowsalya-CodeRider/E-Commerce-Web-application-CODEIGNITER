
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
    


});    
    

        //orders
        function order_action(url, id, message) {
            swal({
                text: message,
                icon: "warning",
                buttons: true,
                buttons: ["Cancel!", "OK!"],
                dangerMode: true,
            }).then(function (willDelete) {
                if (willDelete) {
                    var data = {
                        'id': id,
                    };
                    data[csfr_token_name] = csfr_hash;
                    $.ajax({
                        type: "POST",
                        url: base_url + url,
                        data: data,
                        success: function (response) {
                            location.reload();
                        }
                    });
                }
            });
        };  
    

        //delete item
        function delete_item(url, id, message) {
            swal({
                text: message,
                icon: "warning",
                buttons: true,
                buttons: ["Cancel!", "OK!"],
                dangerMode: true,
            }).then(function (willDelete) {
                if (willDelete) {
                    var data = {
                        'id': id,
                    };
                    data[csfr_token_name] = csfr_hash;
                    $.ajax({
                        type: "POST",
                        url: base_url + url,
                        data: data,
                        success: function (response) {
                            location.reload();
                        }
                    });
                }
            });
        };    
    

      $(function () {
        // bootstrap WYSIHTML5 - text editor
        $('.textarea').wysihtml5({
          toolbar: { fa: true }
        });
       });    
    
	/*============================================
	Summernote
	==============================================*/

    $('#summernote').summernote({
        callbacks: {
			onImageUpload: function(image) {
				uploadImage(image[0]);
			},
        },
        height: 500,
    });

    function uploadImage(image) {
        var data = new FormData();
		data.append("image", image);
        var url = 'admin/summernote';
        //data[csfr_token_name] = csfr_hash;
        $.ajax({
            method: 'POST',
            url: base_url + url,
            contentType: false,
            cache: false,
            processData: false,
            data: data,
            success: function (img) {
                $('#summernote').summernote('insertImage', img);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error(textStatus + " " + errorThrown);
            }
        });
    }    
    
	/*============================================
	Summernote 2
	==============================================*/

	
        $('#summernote-1').summernote({
            height: ($(window).height() - 200),
            callbacks: {
                onImageUpload: function(image) {
                    uploadImage(image[0]);
                },

                onMediaDelete : function(target) {
                     //alert(target[0].src) 
                     var src = target[0].id;
                    deleteFile(src);
                },		
            }
        });

        function uploadImage(image) {
            var data = new FormData();
            data.append("image", image);
            var url = 'admin/summernote';
            $.ajax({
                url: base_url + url,
                cache: false,
                contentType: false,
                processData: false,
                data: data,
                type: "post",
                success: function(url) {
                    var new_url = url.replace(/\s+/g, '');
                    console.log(new_url);
                    var image = $('<img>').attr('src', '<?= base_url()?>public/uploads/summernote/'+new_url).attr('id',new_url).attr('class','img-fluid');
                    $('#summernote-1').summernote("insertNode", image[0]);
                },
                error: function(data) {
                    console.log(data);
                }
            });
        }	
        function deleteFile(src) {

            var url = 'admin/summernote/delete';

            $.ajax({
                url: base_url + url,
                type: "POST",
                data: "src="+src,
                cache: false,
                success: function(resp) {
                    console.log(resp);
                }
            });
        }        