$(document).ready(function(){
    $('[id^="edit_"]').each(function() {
        $(this).bind('click', function() {
            //mark_edit_section_selected($(this));
            $('#cms_container').load("/ontology/ontology/list", {
                id: this.id.replace('edit_',''),
                parent_id : this.id.replace('edit_',''),
                level : '0'
            },function(){                    
                $.getScript('/js/modules/ontology/ontology/list.js');
            });
        });
    });
});