<?php
/**
 * Description of OntologyController
 *
 */
class Ontology_OntologyController extends Zend_Controller_Action
{
    /**
     * Loads header, footer and menu
     */
    public function init(){

		//Create Zend layout
		$layout = new Zend_Layout();
		// Set a layout scripts path
		$layout->setLayoutPath(APPLICATION_PATH.'/modules/core/layouts/scripts/');
		// choose a different layout script:
		$layout->setLayout('core');
		
		//session
		$id = New Zend_Session_Namespace('id');

                if(!isset($id->user_id)){
                    $this->_helper->redirector ( 'index','index','core' );
                }
		/** sections tree**/
		
		//Create section and section temp model
		$section = new Core_Model_Section();
		$section_temp = new Core_Model_SectionTemp();
		$sections_arr = array();
		 
		//find existent sections on db according website
		$sections_list = $section->personalized_find('wc_section', array(array('website_id','=',$id->website_id)),array('article','order_number'));
		if(count($sections_list)>0)
		{
			foreach ($sections_list as $k => &$slt)
			{
				$sections_published_arr[] = $slt->id;
				$slt->temp = 0;
			}
		}
		$sections_list_temp = $section_temp->personalized_find('wc_section_temp', array(array('website_id','=',$id->website_id)),array('article','order_number'));
		if(count($sections_list_temp)>0)
		{
			foreach ($sections_list_temp as $k => &$stp)
			{
				$stp->temp = 1;
			}
		}
		 
		if(count($sections_list)>0 && count($sections_list_temp)>0)
		{
			$sections_copied_arr = array();
			//replacing sections that area eddited on temp
			foreach ($sections_list as $k => &$sbc)
			{
				foreach ($sections_list_temp as $p => &$sct)
				{
					if($sbc->id == $sct->section_id)
					{
						$sct->id = $sct->section_id;
						$sections_list_res[] = $sct;
						$sections_copied_arr[] = $sct->section_id;
					}
				}
			}
			 
			//adding sections created on temp
			if(count($sections_copied_arr)>0)
			{
				$section_pub_missing = array_diff($sections_published_arr, $sections_copied_arr);
				if(count($section_pub_missing)>0)
				{
					foreach ($section_pub_missing as $serial)
					{
						$section_obj = $section->find('wc_section', array('id'=>$serial));
						$section_obj[0]->temp = 0;
						$sections_list_res[] = $section_obj[0];
					}
				}
			}
			$sections_list = $sections_list_res;
		}
			//sections list array
			if(count($sections_list)>0)
			{
				foreach ($sections_list as $sec)
				{
					$sections_arr[] = array('id'=>$sec->id,
							'temp'=>$sec->temp,
							'section_parent_id'=>$sec->section_parent_id,
							'title'=>$sec->title,
							'article'=>$sec->article,
							'order_number'=>$sec->order_number
					);
				}
			}
		
			/** Begin filter sections with variable area only**/
			//Find all sections with variable content
			$sections_list_aux = $section->getSectionWithVariableContent();
				
			//sections list id array
			if(isset($sections_list_aux))
			{
				foreach ($sections_list_aux as $sec)
				{
					$sections_arr_id_aux[] = array('id'=>$sec['id']);
				}
			}
		
			//Get sections with content variable and non temp
			if(isset($sections_arr_id_aux)){
				foreach ($sections_arr as $sav){
					foreach ($sections_arr_id_aux as $sa)
					{
						if($sav['id']==$sa['id']){
							$sections_arr_list[] = $sav;
						}
					}
				}
			}
			
			/** End filter sections with fixed area only**/
		
			$sections_arr = array();
			
			if(isset($sections_arr_list)){
				$sections_arr = $sections_arr_list;
			}	
		
		// Ordering sections by article and number
		$sort_col_number = array();
		$sort_col_article = array();
		foreach ($sections_arr as $key=> $row) {
			if($row['article']=='yes')
				$sort_col_article[$key] = 1;
			else
				$sort_col_article[$key] = 2;
			$sort_col_number[$key] = $row['order_number'];
		}
		array_multisort($sort_col_article, SORT_ASC, $sort_col_number, SORT_ASC, $sections_arr);
		 
		//string with sections tree html
		$html_list = '';
		 
		if(count($sections_arr)>0)
		{
			//sections tree - parents and children as array
			$sections_tree = GlobalFunctions::buildSectionTree($sections_arr);
			//sections tree as list
			$html_list = GlobalFunctions::buildHtmlSectionTree($sections_tree);
			
		}

		//Register html_list in view
//		$this->view->data = $html_list;
		//Disabled display section bar in index
		$this->view->displaysectionbar = false;
		 
		$cms_arr = array();
		 
		//Get module_id by module_name
		 
		$module_obj = new Core_Model_Module();
		$module = $module_obj->find('wc_module',array('name'=>'Ontology'));
		$module_id = $module[0]->id;
		 
		//Get user module action
		if($id->user_modules_actions){
			foreach ($id->user_modules_actions as $k => $mod)
			{
				 
				if($mod->module_id == $module_id)
				{
					$cms_arr[$mod->action_id] = array('action'=> $mod->action_name, 'title'=>$mod->action_title);
				}
			}
		}

		//Put sections array in session var
//		$id->sections_ontology_array = $sections_arr;
		
		//Put user module actions in view
		$this->view->cms_links = $cms_arr;	 		
    }
    
    /**
     * Gets sections of the website
     */
    public function indexAction()
    {	
        //Disable layout for action	
        $this->_helper->layout->disableLayout ();
        
        $id = New Zend_Session_Namespace('id');
        
        if(!isset($id->user_id)){
            $this->_helper->redirector ( 'index','index','core' );
        }
        
        //All sections from CMS content structure are listed
        $ont_obj = new Core_Model_Section();
        $ont_list_obj = $ont_obj->find("wc_section", array('section_parent_id'=>''));
				
        //Convert objClass to normal array
        if($ont_list_obj){
            foreach ($ont_list_obj as $bl){
                $ont_list[] = get_object_vars($bl);
            }
        }
		
        //$ont_list = GlobalFunctions::buildSectionTree($ont_list);
        //ontology_list to index view
        if(isset($ont_list)){
            $this->view->ontology_list = $ont_list;
        }

    }
    
    /**
     * Lists subsections of sections. 
     * Lists alternative semiotic units that belong to the subsection.
     */
    public function listAction()
    {	
        //Disable layout for action	
        $this->_helper->layout->disableLayout ();
        $section_id = $this->_getParam('id');
        $parent_id = $this->_getParam('parent_id');
        $level = $this->_getParam('level');
        
        $this->view->section_id = $section_id;
        $this->view->parent_id = $parent_id;
        $this->view->level = $level+1;
        
        //Checks if the request is the website or a section 
        if ($section_id == 'ws') // It's website
            $section ='';
        else
            $section = $section_id;
        
        //Gets alternative semiotic units that belong to section 
        $ont_obj = new Ontology_Model_Ontology();
        $ont_list_obj = $ont_obj->find('wc_ontology',array('section_id'=>$section));
				
        //Convert objClass to normal array
        if($ont_list_obj){
            foreach ($ont_list_obj as $bl){
                $ont_list[] = get_object_vars($bl);
            }
        }
				    
        if(isset($ont_list)){
            $this->view->ontology_list = $ont_list;
        }
        
        //Gets subsctions of the section
        $sec_obj = new Core_Model_Section();
        $sec_list_obj = $sec_obj->find('wc_section',array('section_parent_id'=>$section_id));
				
        if($sec_list_obj){
            foreach ($sec_list_obj as $bl){
                $sec_list[] = get_object_vars($bl);
            }
        }
	
        //Checks if subsebtion has subsections.
        if(isset($sec_list)){
            if($section_id == 'ws' || $section_id == '')
                $this->view->section_list = array();
            else
                $this->view->section_list = $sec_list;
        }

    }
    
    /**
     * Populates form for new alternative web semiotic unit.
     */
    public function newAction(){
        
        $this->_helper->layout->disableLayout ();
    	$section_id = $this->_getParam('section_id');
        $parent_id = $this->_getParam('parent_id');
        $level = $this->_getParam('level');
        
        //List of signs that can be translated
        if($section_id == 'ws'){
            $option = array('Section'=>'Section');
        } else {
            $option = array('Section'=>'Section','Subsection'=>'Subsection','Article'=>'Article','Content'=>'Content','Link Content' => 'Link Content');
        }
        
        //Verifies if the signs already has an alternative web semiotic unit.
        $ont = new Ontology_Model_Ontology();
        $ont_obj = $ont->find('wc_ontology', array('section_id' => $section_id));
        foreach ($ont_obj as $sub){
            unset($option[$sub->original]);
        }
        
        //Polulates form for a new alternative web semiotic unit. 
    	$form = new Ontology_Form_Ontology();
        $form->origin->setMultiOptions($option);
        $form->populate(array('section_id'=>$section_id,'parent_id'=>$parent_id,'level'=>$level));
        $this->view->form = $form;
    }

    /**
     * Edits alternative web semiotic unit
     */
    public function editAction(){
        
        $this->_helper->layout->disableLayout ();
        
        $lang = Zend_Registry::get('Zend_Translate');
    	
    	$ont = new Ontology_Model_Ontology();
    	$form = new Ontology_Form_Ontology();
    	
        $id = $this->_getParam('id');
        
        //Verifies if the id of the alternative web seiotic unit has been sent. 
        if(!$id){
            $this->_helper->flashMessenger->addMessage(array('error'=>$lang->translate('Access denied')));
            $this->_helper->redirector('index','ontology','ontology');
    	}
        
        //Gets the alternative web semiotic unit.
        $data = $ont->find('wc_ontology', array('id'=>$id));
        
        //Verifies if the alternative web semiotic unit exists.
        if(!$data)
    	{
    		$this->_helper->flashMessenger->addMessage(array('error'=>$lang->translate('Alternative web semiotic unit does not exist')));
    		$this->_helper->redirector('index','ontology','ontology');
    	}
        
        $arr_data = get_object_vars($data[0]);
        $form->populate($arr_data);
        $this->view->form = $form;
    }
    
    /**
     * Saves new alternative web semiotic unit
     */
    public function saveAction(){
        //translate library
    	$lang = Zend_Registry::get('Zend_Translate');
        
        //disable autorendering for this action
        $this->_helper->layout->disableLayout ();
        $this->_helper->viewRenderer->setNoRender();
        
        $form = New Ontology_Form_Ontology();
        
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $ont = new Ontology_Model_Ontology();
            $ont_obj = $ont->getNewRow('wc_ontology');
            
            //Values are assigned to the model's object 
            if(isset($formData['id'])){
                $ont_obj->id =  $formData['id'];
                $ont_obj->original =  GlobalFunctions::value_cleaner($formData['original']);
            } else {
                $ont_obj->original =  GlobalFunctions::value_cleaner($formData['origin']);
            }

            $ont_obj->section_id = $formData['section_id'];
            $ont_obj->parent_id = $formData['parent_id'];
            $ont_obj->level = $formData['level'];
            $ont_obj->semiotic =  GlobalFunctions::value_cleaner($formData['semiotic']);

            $save_ont = $ont->save('wc_ontology', $ont_obj);

            if($save_ont){
                $arr_success = array('serial'=>$save_ont);
                echo json_encode($arr_success);
                $this->_helper->flashMessenger->addMessage(array('success'=>$lang->translate('Success saved')));
            } else {
                $this->_helper->flashMessenger->addMessage(array('error'=>$lang->translate('Errors in saving data')));
            }  
        }
    }
    
    /*
     * Delete alternative web semiotic unit
     */
    public function deleteAction()
    {
        $this->_helper->layout->disableLayout ();
    	// disable autorendering for this action
    	$this->_helper->viewRenderer->setNoRender();
        
        $id = $this->_getParam('id');
        
        $ont = new Ontology_Model_Ontology();
        $ont_obj = $ont->delete('wc_ontology', array('id' => $id));
        
        if($ont_obj){
            $this->_helper->flashMessenger->addMessage(array('success' => 'Success deleted'));
            $arr_success = array('serial'=>$id);
            echo json_encode($arr_success);
        } else {
            $this->_helper->flashMessenger->addMessage(array('error' => 'Errors in deleting data'));
        }
    }
}

?>
