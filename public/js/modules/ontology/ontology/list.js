$(document).ready(function() {

    //New ontology action
    $("#new_ontology").bind("click",function(){
        $('#original').removeAttr('readonly');
        $('#cms_container').load("/ontology/ontology/new", {
            section_id: $('#section_id').val(),
            parent_id : $('#parent_id').val(),
            level : $('#level').val()
        },function(){
            $.getScript('/js/modules/ontology/ontology/list.js');
            //submit button
            $('#submit').bind('click',function(){
                if($("#frmOntology").valid()){
                    //ajax save
                    $.ajax({
                        type: 'POST',
                        async: false,
			url: '/ontology/ontology/save',
			dataType: 'json',
			data: 	$( "#frmOntology" ).serialize(),
			success: function(data) {
                            if(data['serial']){
                                $('#cms_container').load("/ontology/ontology/index", {},function(){
                                    $.getScript('/js/modules/ontology/ontology/ontology.js');
                                });
                            }
                        }
                    });
                }
            });
        });
    });	
	
    //Edit ontology 
    $('[id^="edit_ontology_"]').each(function() {
        $(this).bind('click', function() {
            //mark_edit_section_selected($(this));
            $('#cms_container').load("/ontology/ontology/edit", {
                id: this.id.replace('edit_ontology_',''),
                section_id: $('#section_id').val(),
                parent_id : $('#parent_id').val(),
                level : $('#level').val()
            },function(){
                $.getScript('/js/modules/ontology/ontology/list.js');
                //submit button
                $('#submit').bind('click',function(){
                    if($("#frmOntology").valid()){
                        //ajax save
                        $.ajax({
                            type: 'POST',
                            async: false,
                            url: '/ontology/ontology/save',
                            dataType: 'json',
                            data: 	$( "#frmOntology" ).serialize(),
                            success: function(data) {
                                if(data['serial']){
                                    $('#cms_container').load("/ontology/ontology/index", {},function(){
                                        $.getScript('/js/modules/ontology/ontology/ontology.js');
                                    });
                                }
                            }
                        });
                    }
                });
            });
        });
    });

    //Edit subsection
    $('[id^="edit_subsec_"]').each(function() {
        $(this).bind('click', function() {
            //mark_edit_section_selected($(this));
            $('#cms_container').load("/ontology/ontology/list", {
                id: this.id.replace('edit_subsec_',''),
                level: $('#level').val(),
                parent_id : $('#parent_id').val()
            },function(){
                $.getScript('/js/modules/ontology/ontology/list.js');
            });
        });
    });
        
    //Delete ontology
    $('[id^="delete_ontology_"]').each(function() {
        $(this).bind('click', function() {
            $.ajax({
                type: 'POST',
                async: false,
                url: '/ontology/ontology/delete',
                dataType: 'json',
                data: 	{
                    id: this.id.replace("delete_ontology_","")
                },
                success: function(data) {
                    if(data['serial']){
                        $('#cms_container').load("/ontology/ontology/index", {
                            id: data['serial']
                        }, function() {
                            $.getScript('/js/modules/ontology/ontology/ontology.js');
                        });
                    }
                }
            });
        });
    });
        
    //cancel button
    $("#cancel").bind('click',function(){
        window.location = "/ontology";
    });
        
    $('[id^="ws"]').each(function(){
        $(this).bind('click', function(){
            window.location = "/ontology";
        });
    });
        
}); //END DOCUMENT ROOT
