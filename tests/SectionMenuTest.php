<?php
class SectionMenuTest extends WP_UnitTestCase{

	public function setUp(){
        parent::setUp();
        $this->submenu = $GLOBALS['jcsubmenu'];
    }

    /**
     * Basic Menu Section 1/2
     */
    function testSectionMenu01(){

        $walker = new JC_Submenu_Nav_Walker(array('menu_item' => 1, 'menu_depth' => 1));

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
            (object)array(
                'menu_item_parent' => 1,
                'db_id' => 3,
            ),
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 4,
            ),
        );
        $result = array(
            (object)array(
                'menu_item_parent' => 1,
                'db_id' => 3,
                'menu_depth' => 1
            ),
        );
    
        $elements = $walker->set_elements_depth($elements, 0, true);
        $output = $walker->_process_menu_section($elements);
        $this->assertEquals($result, $output);
    }

    /**
     * Basic Menu Section 2/2
     */
    function testSectionMenu02(){

        $walker = new JC_Submenu_Nav_Walker(array('menu_item' => 2, 'menu_depth' => 1));

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
            (object)array(
                'menu_item_parent' => 1,
                'db_id' => 3,
            ),
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 4,
            ),
        );
        $result = array(
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 4,
                'menu_depth' => 1
            ),
        );
    
        $elements = $walker->set_elements_depth($elements, 0, true);
        $output = $walker->_process_menu_section($elements);
        $this->assertEquals($result, $output);
    }

    /**
     * Test Show Parent 1/2
     */
    function testSectionMenu03(){

        $walker = new JC_Submenu_Nav_Walker(array('menu_item' => 1, 'menu_depth' => 1, 'show_parent' => 1));

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
            (object)array(
                'menu_item_parent' => 1,
                'db_id' => 3,
            ),
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 4,
            ),
        );
        $result = array(
            (object)array(
                'menu_item_parent' => 0,
                'db_id' => 1,
                'current' => 1,
                'menu_depth' => 0
            ),
            (object)array(
                'menu_item_parent' => 1,
                'db_id' => 3,
                'menu_depth' => 1
            ),
        );
    
        $elements = $walker->set_elements_depth($elements, 0, true);
        $output = $walker->_process_menu_section($elements);
        $this->assertEquals($result, $output);
    }

    /**
     * Test Show Parent 2/2
     */
    function testSectionMenu04(){

        $walker = new JC_Submenu_Nav_Walker(array('menu_item' => 2, 'menu_depth' => 1, 'show_parent' => 1));

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
            (object)array(
                'menu_item_parent' => 1,
                'db_id' => 3,
            ),
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 4,
            ),
        );
        $result = array(
            (object)array(
                'menu_item_parent' => 0,
                'db_id' => 2,
                'menu_depth' => 0
            ),
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 4,
                'menu_depth' => 1
            ),
        );
    
        $elements = $walker->set_elements_depth($elements, 0, true);
        $output = $walker->_process_menu_section($elements);
        $this->assertEquals($result, $output);
    }
}

?>