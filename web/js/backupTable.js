$(document).ready(function(){
   var $table = $('#dm_page_meta_table');
   
        $table.dataTable({
            "oLanguage": {
                "sUrl": $table.metadata().translation_url
            },
            "bJQueryUI": true,
            "sPaginationType": "full_numbers",
            "aaSorting": [[1,'desc']],
            "aoColumns": [{"bSortable": false}, null, null, null, {"bSortable": false}]
        }); 
        $('.dm_download_link').click(function(){
            var meta = $(this).metadata();
            window.open(meta.link + '?file=' + meta.file);
        });        
        
        $('input.check_all').click(function(evt){
            evt.stopImmediatePropagation();
            if (!$(this).prop('checked')) {
                $(this).closest('table').find('input[type=checkbox]').removeAttr('checked');
            } else {
                $(this).closest('table').find('input[type=checkbox]').attr('checked', 'checked');
            };
        });
        
        $('.dm_delete_link').click(function(){
            var $button = $(this);
            var meta = $button.metadata();
            if (confirm(meta.message)) {
                $(this).closest('tr').find('input[type=checkbox]').attr('checked', 'checked');
                $('.batch_delete_button').click();
            };
        });
        
        $('.batch_delete_button').click(function(){
            if ($(this).closest('form').find('input[type=checkbox]').filter(function(){
                if ($(this).hasClass('check_all')) return false;
                if ($(this).prop('checked')) return true;
                return false;
            }).length == 0) {
                alert($(this).metadata().message)
                return false;
            };
        });
        $('.automatic_download').click(function(){
            var meta = $(this).metadata();
            window.open(meta.link + '?file=' + meta.file);
        });
        
        var automatic = function() {
            if ($('.automatic_download').length) $('.automatic_download').click().remove();
        };
        setTimeout(automatic, 2000);
});