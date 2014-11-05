<?php
class SplitMenuTest extends WP_UnitTestCase{

	public function setUp(){
        parent::setUp();
        $this->submenu = $GLOBALS['jcsubmenu'];
    }

    public function stripResults($elements){
    	foreach($elements as $elm){
    		if(isset($elm->classes)){
    			unset($elm->classes);
    		}
			if(isset($elm->current_item_ancestor)){
	    		unset($elm->current_item_ancestor);
	    	}
    		if(isset($elm->current_item_parent)){
	        	unset($elm->current_item_parent);
	        }
    	}
    	return $elements;
    }

    public function orderItems($elements){
    	usort($elements, array($this, 'usortElement'));
    	return $elements;
    }

    public function usortElement($a, $b){
    	return $a->db_id - $b->db_id;
    }

    /**
     * Top Level Split Test 1/2
     * Test a basic split menu from menu item 1
     */
    public function testSplitMenu01(){

    	$elements = array(
            (object)array(
                'menu_item_parent' => 0,
                'db_id' => 1,
                'current' => 1
            ),
            (object)array(
                'menu_item_parent' => 0,
                'db_id' => 2,
            ),
            // lvl 1
            (object)array(
                'menu_item_parent' => 1,
                'db_id' => 3,
            ),
            (object)array(
                'menu_item_parent' => 1,
                'db_id' => 4,
            ),
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 5,
            ),
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 6,
            ),
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 7,
            ),
        );
        $result = array(
            (object)array(
                'menu_item_parent' => 0,
                'db_id' => 1,
                'current' => 1,
                'split_section' => 1,
                'menu_depth' => 0
            ),
            // lvl 1
            (object)array(
                'menu_item_parent' => 1,
                'db_id' => 3,
                'menu_depth' => 1
            ),
            (object)array(
                'menu_item_parent' => 1,
                'db_id' => 4,
                'menu_depth' => 1
            ),
        );

        $walker = new JC_Submenu_Nav_Walker(array('split_menu' => true, 'menu_start' => 1 ));
        
        $elements = $walker->_set_elements_state($elements);
        $elements = $walker->set_elements_depth($elements, 0, true);
        $output = $walker->_process_split_menu($elements);

        $this->assertEquals($this->orderItems($result), $this->orderItems($output));
    }

    /**
     * Top Leve Split Test 2/2
     * Test a basic split menu from menu item 2
     */
    public function testSplitMenu02(){

    	$elements = array(
            (object)array(
                'menu_item_parent' => 0,
                'db_id' => 1,
            ),
            (object)array(
                'menu_item_parent' => 0,
                'db_id' => 2,
                'current' => 1
            ),
            // lvl 1
            (object)array(
                'menu_item_parent' => 1,
                'db_id' => 3,
            ),
            (object)array(
                'menu_item_parent' => 1,
                'db_id' => 4,
            ),
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 5,
            ),
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 6,
            ),
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 7,
            ),
        );

        $result = array(
            (object)array(
                'menu_item_parent' => 0,
                'db_id' => 2,
                'current' => 1,
                'menu_depth' => 0,
                'split_section' => 1
            ),
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 5,
                'menu_depth' => 1,
            ),
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 6,
                'menu_depth' => 1,
            ),
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 7,
                'menu_depth' => 1,
            ),
        );

        $walker = new JC_Submenu_Nav_Walker(array('split_menu' => true, 'menu_start' => 1 ));
        
        $elements = $walker->_set_elements_state($elements);
        $elements = $walker->set_elements_depth($elements, 0, true);
        $output = $walker->_process_split_menu($elements);
        
        $this->assertEquals($this->orderItems($result), $this->orderItems($output));
    }

    /**
     * Top Level Split Test with parent 1/2
     * Test a basic split menu from menu item 1
     */
    public function testSplitMenu03(){

    	$elements = array(
            (object)array(
                'menu_item_parent' => 0,
                'db_id' => 1,
            ),
            (object)array(
                'menu_item_parent' => 0,
                'db_id' => 2,
            ),
            // lvl 1
            (object)array(
                'menu_item_parent' => 1,
                'db_id' => 3,
                'current' => 1
            ),
            (object)array(
                'menu_item_parent' => 1,
                'db_id' => 4,
            ),
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 5,
            ),
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 6,
            ),
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 7,
            ),
        );

        $result = array(
            (object)array(
                'menu_item_parent' => 0,
                'db_id' => 1,
                'split_section' => 1,
                'menu_depth' => 0,
            ),
            // lvl 1
            (object)array(
                'menu_item_parent' => 1,
                'db_id' => 3,
                'menu_depth' => 1,
                'current' => 1,
                'split_section' => 1,
            ),
            (object)array(
                'menu_item_parent' => 1,
                'db_id' => 4,
                'menu_depth' => 1
            ),
        );

        $walker = new JC_Submenu_Nav_Walker(array('split_menu' => true, 'menu_start' => 1 ));
        
        $elements = $walker->_set_elements_state($elements);
        $elements = $walker->set_elements_depth($elements, 0, true);
        $output = $walker->_process_split_menu($elements);
        
        $output = $this->stripResults($output);
        $this->assertEquals($this->orderItems($result), $this->orderItems($output));

    }

    /**
     * Top Leve Split Test with parent 2/2
     * Test a basic split menu from menu item 2
     */
    public function testSplitMenu04(){

    	$elements = array(
            (object)array(
                'menu_item_parent' => 0,
                'db_id' => 1,
            ),
            (object)array(
                'menu_item_parent' => 0,
                'db_id' => 2,
            ),
            // lvl 1
            (object)array(
                'menu_item_parent' => 1,
                'db_id' => 3,
            ),
            (object)array(
                'menu_item_parent' => 1,
                'db_id' => 4,
            ),
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 5,
                'current' => 1
            ),
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 6,
            ),
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 7,
            ),
        );

        $result = array(
            (object)array(
                'menu_item_parent' => 0,
                'db_id' => 2,
                'menu_depth' => 0,
                'split_section' => 1
            ),
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 5,
                'menu_depth' => 1,
                'current' => 1,
                'split_section' => 1
            ),
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 6,
                'menu_depth' => 1,
            ),
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 7,
                'menu_depth' => 1,
            ),
        );

        $walker = new JC_Submenu_Nav_Walker(array('split_menu' => true, 'menu_start' => 1 ));
        
        $elements = $walker->_set_elements_state($elements);
        $elements = $walker->set_elements_depth($elements, 0, true);
        $output = $walker->_process_split_menu($elements);

        $output = $this->stripResults($output);
        $this->assertEquals($this->orderItems($result), $this->orderItems($output));

    }

    /**
     * First child split menu test 1/2
     */
    public function testSplitMenu05(){

    	$elements = array(
            (object)array(
                'menu_item_parent' => 0,
                'db_id' => 1,
            ),
            (object)array(
                'menu_item_parent' => 0,
                'db_id' => 2,
                
            ),
            // lvl 1
            (object)array(
                'menu_item_parent' => 1,
                'db_id' => 3,
            ),
            (object)array(
                'menu_item_parent' => 1,
                'db_id' => 4,
                'current' => 1
            ),
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 5,
            ),
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 6,
            ),
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 7,
            ),
        );

        $result = array(
            (object)array(
                'menu_item_parent' => 0,
                'db_id' => 1,
                'split_section' => 1,
                'menu_depth' => 0,
            ),
            // lvl 1
            (object)array(
                'menu_item_parent' => 1,
                'db_id' => 3,
                'menu_depth' => 1,
            ),
            (object)array(
                'menu_item_parent' => 1,
                'db_id' => 4,
                'current' => 1,
                'split_section' => 1,
                'menu_depth' => 1,
            ),
        );

        $walker = new JC_Submenu_Nav_Walker(array('split_menu' => true, 'trigger_depth' => 0, 'menu_start' => 1, 'menu_depth' => 1, 'show_parent' => false));
        
        $elements = $walker->_set_elements_state($elements);
        $elements = $walker->set_elements_depth($elements, 0, true);
        $output = $walker->_process_split_menu($elements);

        $output = $this->stripResults($output);
        $this->assertEquals($this->orderItems($result), $this->orderItems($output));

    }

    /**
     * First child split menu test 2/2
     */
    public function testSplitMenu06(){

    	$elements = array(
            (object)array(
                'menu_item_parent' => 0,
                'db_id' => 1,
            ),
            (object)array(
                'menu_item_parent' => 0,
                'db_id' => 2,
                
            ),
            // lvl 1
            (object)array(
                'menu_item_parent' => 1,
                'db_id' => 3,
            ),
            (object)array(
                'menu_item_parent' => 1,
                'db_id' => 4,
            ),
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 5,
            ),
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 6,
                'current' => 1
            ),
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 7,
            ),
        );

        $result = array(
            (object)array(
                'menu_item_parent' => 0,
                'db_id' => 2,
                'split_section' => 1,
                'menu_depth' => 0,
            ),
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 5,
                'menu_depth' => 1,
            ),
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 6,
                'current' => 1,
                'split_section' => 1,
                'menu_depth' => 1,
            ),
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 7,
                'menu_depth' => 1,
            ),
        );

        $walker = new JC_Submenu_Nav_Walker(array('split_menu' => true, 'trigger_depth' => 0, 'menu_start' => 1, 'menu_depth' => 1, 'show_parent' => false));
        
        $elements = $walker->_set_elements_state($elements);
        $elements = $walker->set_elements_depth($elements, 0, true);
        $output = $walker->_process_split_menu($elements);
        
        $output = $this->stripResults($output);
        $this->assertEquals($this->orderItems($result), $this->orderItems($output));

    }

    /**
     * Empty Split Menu Test
     */
    public function testSplitMenu07(){

    	$elements = array(
            (object)array(
                'menu_item_parent' => 0,
                'db_id' => 1,
            ),
            (object)array(
                'menu_item_parent' => 0,
                'db_id' => 2,
            ),
            // lvl 1
            (object)array(
                'menu_item_parent' => 1,
                'db_id' => 3,
            ),
            (object)array(
                'menu_item_parent' => 1,
                'db_id' => 4,
            ),
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 5,
            ),
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 6,
            ),
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 7,
            ),
        );

        $walker = new JC_Submenu_Nav_Walker(array('split_menu' => true, 'trigger_depth' => 0, 'menu_start' => 1, 'menu_depth' => 1, 'show_parent' => false));
        
        $elements = $walker->_set_elements_state($elements);
        $elements = $walker->set_elements_depth($elements, 0, true);
        $output = $walker->_process_split_menu($elements);
        $this->assertEmpty($output);
    }

    /**
     * Test current item with missing parent item
     */
    public function testSplitMenu08(){

    	$elements = array(
            (object)array(
                'menu_item_parent' => 0,
                'db_id' => 1,
            ),
            (object)array(
                'menu_item_parent' => 0,
                'db_id' => 2,
            ),
            // lvl 1
            (object)array(
                'menu_item_parent' => 1,
                'db_id' => 3,
            ),
            (object)array(
                'menu_item_parent' => 1,
                'db_id' => 4,
            ),
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 5,
            ),
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 6,
            ),
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 7,
            ),
            (object)array(
                'menu_item_parent' => 8,
                'db_id' => 8,
                'current' => 1
            ),
        );

        $result = array(
            (object)array(
                'menu_item_parent' => 8,
                'db_id' => 8,
                'current' => 1,
                'split_section' => 1
            ),
        );

        $walker = new JC_Submenu_Nav_Walker(array('split_menu' => true, 'trigger_depth' => 0, 'menu_start' => 1, 'menu_depth' => 1, 'show_parent' => false));
        
        $elements = $walker->_set_elements_state($elements);
        $elements = $walker->set_elements_depth($elements, 0, true);
        $output = $walker->_process_split_menu($elements);
        
        $output = $this->stripResults($output);
        $this->assertEquals($this->orderItems($result), $this->orderItems($output));
    }
}

?>