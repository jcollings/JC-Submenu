<?php
class DynamicItemsTest extends WP_UnitTestCase{

	var $importer;

	public function setUp(){
        parent::setUp();
        $this->submenu = $GLOBALS['jcsubmenu'];
    }

    function testElementDepths(){
        $walker = new JC_Submenu_Nav_Walker();

        $elements = array(
            (object)array(
                'menu_item_parent' => 0,
                'db_id' => 1,
            ),
            // lvl 1
            (object)array(
                'menu_item_parent' => 1,
                'db_id' => 2,
            ),
            (object)array(
                'menu_item_parent' => 1,
                'db_id' => 3,
            ),
            (object)array(
                'menu_item_parent' => 1,
                'db_id' => 4,
            ),
            // lvl 2
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 5,
            ),
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 6,
            ),
            // lvl 3
            (object)array(
                'menu_item_parent' => 5,
                'db_id' => 7,
            ),
            (object)array(
                'menu_item_parent' => 6,
                'db_id' => 8,
            ),
        );

        $output = $walker->set_elements_depth($elements);
        $this->assertEquals(8, count($elements));
        $this->assertEquals($elements[0]->depth, 0);
        $this->assertEquals($elements[1]->depth, 1);
        $this->assertEquals($elements[2]->depth, 1);
        $this->assertEquals($elements[3]->depth, 1);
        $this->assertEquals($elements[4]->depth, 2);
        $this->assertEquals($elements[5]->depth, 2);
        $this->assertEquals($elements[6]->depth, 3);
        $this->assertEquals($elements[7]->depth, 3);
    }

    function testElementState01(){

        $walker = new JC_Submenu_Nav_Walker();

        /**
         * Top level parent
         */

        $elements = array(
            (object)array(
                'menu_item_parent' => 0,
                'db_id' => 1,
                'current' => 1
            ),
            // lvl 1
            (object)array(
                'menu_item_parent' => 1,
                'db_id' => 2,
            ),
            (object)array(
                'menu_item_parent' => 1,
                'db_id' => 3,
            ),
            (object)array(
                'menu_item_parent' => 1,
                'db_id' => 4,
            ),
            // lvl 2
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 5,
            ),
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 6,
            ),
            // lvl 3
            (object)array(
                'menu_item_parent' => 5,
                'db_id' => 7,
            ),
            (object)array(
                'menu_item_parent' => 6,
                'db_id' => 8,
            ),
        );

        $result = array(
            (object)array(
                'menu_item_parent' => 0,
                'db_id' => 1,
                'current' => 1,
                'split_section' => 1
            ),
            // lvl 1
            (object)array(
                'menu_item_parent' => 1,
                'db_id' => 2,
            ),
            (object)array(
                'menu_item_parent' => 1,
                'db_id' => 3,
            ),
            (object)array(
                'menu_item_parent' => 1,
                'db_id' => 4,
            ),
            // lvl 2
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 5,
            ),
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 6,
            ),
            // lvl 3
            (object)array(
                'menu_item_parent' => 5,
                'db_id' => 7,
            ),
            (object)array(
                'menu_item_parent' => 6,
                'db_id' => 8,
            ),
        );

        $output = $walker->_set_elements_state($elements);
        $this->assertEquals($result, $output);
    }

    function testElementState02(){

        $walker = new JC_Submenu_Nav_Walker();

        /**
         * Second level parent
         */
        $elements = array(
            (object)array(
                'menu_item_parent' => 0,
                'db_id' => 1,
            ),
            // lvl 1
            (object)array(
                'menu_item_parent' => 1,
                'db_id' => 2,
                'current' => 1
            ),
            (object)array(
                'menu_item_parent' => 1,
                'db_id' => 3,
            ),
            (object)array(
                'menu_item_parent' => 1,
                'db_id' => 4,
            ),
            // lvl 2
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 5,
            ),
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 6,
            ),
            // lvl 3
            (object)array(
                'menu_item_parent' => 5,
                'db_id' => 7,
            ),
            (object)array(
                'menu_item_parent' => 6,
                'db_id' => 8,
            ),
        );
        $result = array(
            (object)array(
                'menu_item_parent' => 0,
                'db_id' => 1,
                'classes' => array('current-menu-parent', 'current-menu-ancestor'),
                'current_item_ancestor' => 1,
                'current_item_parent' => 1,
                'split_section' => 1
            ),
            // lvl 1
            (object)array(
                'menu_item_parent' => 1,
                'db_id' => 2,
                'current' => 1,
                'split_section' => 1
            ),
            (object)array(
                'menu_item_parent' => 1,
                'db_id' => 3,
            ),
            (object)array(
                'menu_item_parent' => 1,
                'db_id' => 4,
            ),
            // lvl 2
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 5,
            ),
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 6,
            ),
            // lvl 3
            (object)array(
                'menu_item_parent' => 5,
                'db_id' => 7,
            ),
            (object)array(
                'menu_item_parent' => 6,
                'db_id' => 8,
            ),
        );

        $output = $walker->_set_elements_state($elements);
        $this->assertEquals($result, $output);
    }

    function testElementState03(){

        $walker = new JC_Submenu_Nav_Walker();

        /**
         * Third level parent
         */
        $elements = array(
            (object)array(
                'menu_item_parent' => 0,
                'db_id' => 1,
            ),
            // lvl 1
            (object)array(
                'menu_item_parent' => 1,
                'db_id' => 2,
                
            ),
            (object)array(
                'menu_item_parent' => 1,
                'db_id' => 3,
            ),
            (object)array(
                'menu_item_parent' => 1,
                'db_id' => 4,
            ),
            // lvl 2
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 5,
                'current' => 1
            ),
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 6,
            ),
            // lvl 3
            (object)array(
                'menu_item_parent' => 5,
                'db_id' => 7,
            ),
            (object)array(
                'menu_item_parent' => 6,
                'db_id' => 8,
            ),
        );
        $result = array(
            (object)array(
                'menu_item_parent' => 0,
                'db_id' => 1,
                'classes' => array('current-menu-ancestor'),
                'split_section' => 1,
                'current_item_ancestor' => 1,
            ),
            // lvl 1
            (object)array(
                'menu_item_parent' => 1,
                'db_id' => 2,
                'classes' => array('current-menu-parent', 'current-menu-ancestor'),
                'current_item_parent' => 1,
                'current_item_ancestor' => 1,
                'split_section' => 1
            ),
            (object)array(
                'menu_item_parent' => 1,
                'db_id' => 3,
            ),
            (object)array(
                'menu_item_parent' => 1,
                'db_id' => 4,
            ),
            // lvl 2
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 5,
                'current' => 1,
                'split_section' => 1
            ),
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 6,
            ),
            // lvl 3
            (object)array(
                'menu_item_parent' => 5,
                'db_id' => 7,
            ),
            (object)array(
                'menu_item_parent' => 6,
                'db_id' => 8,
            ),
        );

        $output = $walker->_set_elements_state($elements);
        $this->assertEquals($result, $output);

    }

    function testElementState04(){

        $walker = new JC_Submenu_Nav_Walker();
        /**
         * Fourth Level Parent
         */
        $elements = array(
            (object)array(
                'menu_item_parent' => 0,
                'db_id' => 1,
            ),
            // lvl 1
            (object)array(
                'menu_item_parent' => 1,
                'db_id' => 2,
                
            ),
            (object)array(
                'menu_item_parent' => 1,
                'db_id' => 3,
            ),
            (object)array(
                'menu_item_parent' => 1,
                'db_id' => 4,
            ),
            // lvl 2
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 5,
                
            ),
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 6,
            ),
            // lvl 3
            (object)array(
                'menu_item_parent' => 5,
                'db_id' => 7,
                'current' => 1
            ),
            (object)array(
                'menu_item_parent' => 6,
                'db_id' => 8,
            ),
            // no parent
            (object)array(
                'menu_item_parent' => 9,
                'db_id' => 9,
            ),
        );
        $result = array(
            (object)array(
                'menu_item_parent' => 0,
                'db_id' => 1,
                'classes' => array('current-menu-ancestor'),
                'current_item_ancestor' => 1,
                'split_section' => 1
            ),
            // lvl 1
            (object)array(
                'menu_item_parent' => 1,
                'db_id' => 2,
                'classes' => array('current-menu-ancestor'),
                'current_item_ancestor' => 1,
                'split_section' => 1
            ),
            (object)array(
                'menu_item_parent' => 1,
                'db_id' => 3,
            ),
            (object)array(
                'menu_item_parent' => 1,
                'db_id' => 4,
            ),
            // lvl 2
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 5,
                'classes' => array('current-menu-parent', 'current-menu-ancestor'),
                'current_item_ancestor' => 1,
                'current_item_parent' => 1,
                'split_section' => 1
            ),
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 6,
            ),
            // lvl 3
            (object)array(
                'menu_item_parent' => 5,
                'db_id' => 7,
                'current' => 1,
                'split_section' => 1
            ),
            (object)array(
                'menu_item_parent' => 6,
                'db_id' => 8,
            ),
            // no parent
            (object)array(
                'menu_item_parent' => 9,
                'db_id' => 9,
            ),
        );

        $output = $walker->_set_elements_state($elements);
        $this->assertEquals($result, $output);
    }

    /**
     * Test Single Menu Item who is current
     */
    function testElementState05(){

        $walker = new JC_Submenu_Nav_Walker();
        /**
         * Fourth Level Parent
         */
        $elements = array(
            (object)array(
                'menu_item_parent' => 0,
                'db_id' => 1,
                'current' => 1
            )
        );
        $result = array(
            (object)array(
                'menu_item_parent' => 0,
                'db_id' => 1,
                'current' => 1,
                'split_section' => 1
            )
        );

        $output = $walker->_set_elements_state($elements);
        $this->assertEquals($result, $output);
    }

    /**
     * Test Single Menu Item not current
     */
    function testElementState06(){

        $walker = new JC_Submenu_Nav_Walker();

        $elements = array(
            (object)array(
                'menu_item_parent' => 0,
                'db_id' => 1,
            )
        );
        $result = array(
            (object)array(
                'menu_item_parent' => 0,
                'db_id' => 1,
            )
        );

        $output = $walker->_set_elements_state($elements);
        $this->assertEquals($result, $output);
    }
}

?>