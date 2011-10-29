(function($) {
    var $table = $('#dm_page_meta_table');
   
    $table.dataTable({
        "oLanguage": {
            "sUrl": $table.metadata().translation_url
        },
        "bJQueryUI": true,
        "sPaginationType": "full_numbers",
        "aaSorting": [[1,'desc']],
        "aoColumns": [{
            "bSortable": false
        }, null, null, null, null, null, null, null, null, {
            "bSortable": false
        }]
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
            var $form = $('<form method="post"></form>').attr('action', meta.link).attr('method','post').css('display','none')
            .append($('<input/>').attr('type', 'hidden').attr('name', '_file_name').val(meta.file))
            .append($('<input/>').attr('type', 'hidden').attr('name', '_delete'));            
            $('body').append($form);
            $form.submit();
            return false;
        };
        return false;
    });
    
    $('.dm_download_link').click(function(){
        var meta = $(this).metadata();
        window.open(meta.link + '?_file_name=' + meta.file);
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
    
})(jQuery);