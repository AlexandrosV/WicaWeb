<?php

/**
 * Description of ontology
 *
 */
class Ontology_Form_Ontology extends Zend_Form
{
    /**
     * Loads all the objects of the form when the form is initialized
     */
    
    public function init()
    {
        
        $this->setMethod('post');
        $this->setAttrib('id', 'frmOntology');
        $this->setAttrib('class', 'well');
        $this->setAction('/ontology/ontology/save');
        
        $lang = Zend_Registry::get('Zend_Translate');
        
        $original = New Zend_Form_Element_Text('original');
        $original->setLabel($lang->translate('Original').':');
        $original->setAttrib('style', 'width:100%');
        $original->setAttrib('readonly','true');
        
        $semiotic = New Zend_Form_Element_Text('semiotic');
        $semiotic->setLabel($lang->translate('Alternative Web Semiotic Unit').':');
        $semiotic->setFilters(array( new Zend_Filter_StringTrim()));
        $semiotic->setAttrib('style', 'width:100%');
        
        $origin = New Zend_Form_Element_Select('origin');
        $origin->setLabel($lang->translate('Original').':');
        $origin->setAttrib('style', 'width:100%');
        
        //Submit Button
        $submit = New Zend_Form_Element_Button('submit');
        $submit->setLabel($lang->translate('Save'));
        $submit->setAttrib('class', 'btn btn-success');
        $submit->setIgnore(true);
        
        //Cancel Button
        $cancel = New Zend_Form_Element_Button('cancel');
        $cancel->setLabel($lang->translate('Cancel'));
        $cancel->setAttrib('class', 'btn');
        $cancel->setIgnore(true);
		
        //Hidden Id
        $id = New Zend_Form_Element_Hidden('id');
        $id->removeDecorator('Label');
        $id->removeDecorator('HtmlTag');
        
        //Hidden Section_id
        $section_id = New Zend_Form_Element_Hidden('section_id');
        $section_id->removeDecorator('Label');
        $section_id->removeDecorator('HtmlTag');
        
        //Hidden Parent_id
        $parent_id = New Zend_Form_Element_Hidden('parent_id');
        $parent_id->removeDecorator('Label');
        $parent_id->removeDecorator('HtmlTag');
        
        //Hidden Level
        $level = New Zend_Form_Element_Hidden('level');
        $level->removeDecorator('Label');
        $level->removeDecorator('HtmlTag');
        
        //add elements to the form
        $this->addElements(array(
            $original,
            $origin,
            $semiotic,
            $submit,
            $cancel,
            $id,
            $section_id,
            $parent_id,
            $level,
            ));
    }
}

?>
