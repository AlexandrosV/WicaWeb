<?php

/**
 *
 */
class Zend_View_Helper_OntologyChange extends Zend_View_Helper_Abstract
{
	/**
         * Takes original web semiotic unit and returns alternative web semiotic unit.
         * @param string $phrase Original web semiotic unit.
         * @param string $section_id Id of the section
         * @param integer $has Flag for sign "Has Subsection". 0 -> It is not sign, 1 -> it is sign 
         * @return string Alternative web semiotic unit. 
         */
	
    public function ontologyChange($phrase, $section_id, $has=0)
    {
        $semiotic = new Ontology_Model_Ontology();
        $sec = new Core_Model_Section();
        $level = 0;//
        $count = 0;//
        
        //Checks if $section_id is defined
        if (isset($section_id)){
            //Finds sections of the id 
            $sec_obj = $sec->find('wc_section', array('id' => $section_id));
            //Checks if the section/subsection has parent. 
            if(isset($sec_obj[0]->section_parent_id) && $has == 0){
                //Get the level of the section/sybsection
                $obj = $this->searchLevel($section_id);
                //Gets all alternative semiotc units contained in the section/subsection (Same parent & level)
                $ont_obj = $semiotic->find('wc_ontology', array('parent_id' => $obj['parent_id'], 'level' => $obj['level']));
                $level++;
            }
            //Sign is "Has Subsection"
            if($has == 1){
                $obj = $this->searchLevel($section_id);
                //Alternative web semiotic unit belongs one subsection ahead
                $lvl = $obj['level']+1;
                //Checks if the section/subsection has parent.
                if($sec_obj[0]->section_parent_id){
                    $parent = $obj['parent_id'];
                } else {
                    $parent = $section_id; //It is the same section/subsection
                }
                //Gets all alternative semiotc units contained in the section/subsection (Same parent & level)
                $ont_obj = $semiotic->find('wc_ontology', array('parent_id' => $parent, 'level' => $lvl));
                $level++;
            }
            //Gets all alternative semiotc units contained in the specific section/subsection 
            $sem_obj = $semiotic->find('wc_ontology', array('section_id'=>$section_id));
        } else {
            //Section_id is null therefore the user is in website level
            $sem_obj = $semiotic->find('wc_ontology', array('section_id'=>''));
        }

        if($has == 0){
            //Checks if an original web semiotic unit has an alternative web semiotic unit.
            foreach ($sem_obj as $sub)
            {
                $output = "";
                if(isset($sub->semiotic) && $sub->semiotic!=NULL && $sub->semiotic!=''){
                    if($phrase == $sub->original)
                        $output = str_replace($sub->original, $sub->semiotic, $phrase, $count);
                }
                if($count>0)
                    break;
            }
        }
        
        /*If alternative web semitiotic unit hasn't been found for the specific section
         * alternative web semiotic unit will be seek in subsection with the same parent 
         * and level.
         */
        
        if($count == 0 && $level > 0) {
            foreach ($ont_obj as $sub)
            {
                $output = "";
                if(isset($sub->semiotic) && $sub->semiotic!=NULL && $sub->semiotic!=''){
                    if($phrase == $sub->original)
                        $output = str_replace($sub->original, $sub->semiotic, $phrase, $count);
                }
                if($count>0)
                    break;
            }
        }
        
        //Alternative web semiotic unit not defined.
        if($count == 0) {
            $output = $phrase;
        }
        
        return $output;
    }
    
    /**
     * Gets the parent and the level of a section
     * @param string $section_id Id of the section
     * @return array parent & level
     */
    public function searchLevel($section_id){
        //It is required to stablish a max for the loop, wc_section doesn't store level of subsections. 
        $count = 20;
        $level = 0;
        $id = $section_id;
        $sec = new Core_Model_Section();
        
        while($count>0){
            $sec_ob = $sec->find('wc_section',array('id' => $id));
            if ($sec_ob[0]->section_parent_id){
                $id = $sec_ob[0]->section_parent_id;
                $level++;
                $count--;
            } else {
                $level++;
                break;
            }
        }
        return array('parent_id' => $id,'level' => $level);
    }
    
}