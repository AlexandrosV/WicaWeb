$(document).ready(function() {    

    $('#cms_container').load("/ontology/ontology/index", {
        id : 'all'
    }, function() {
        $('#parent_section').addClass('selected');
        $.getScript('/js/modules/ontology/ontology/ontology.js');
    });		

}); //END DOCUMENT ROOT


