<?php
function mobapper_admin() {
    ?>

    <?php
    global $mobapper_options;
    $category_values = $mobapper_options['exclude_categories'];
    $cat_values_array = explode(",", $category_values);

    $page_values = $mobapper_options['exclude_pages'];
    $page_values_array = explode(",", $page_values);
    ?>

    <style>
        #col-left{
            width:40% !important;
        }
        .div-scroll{
          overflow-y: auto;
          max-height:500px;  
        }
        #create-app{
            
            
            padding-right: 30px;
        }
        .mailto-us{
/*            float: right;*/
            
        }
        #col-right{
            padding-top: 44px;
            width: 30% !important;
        }
        .mail-to-us-div{
            margin-top: 20px;
            margin-left: 72px; 
        }
    </style>

    <div class="wrap nosubsub">
        <h2>Mobapper Dashboard</h2>

        


        <br class="clear">

        <div id="col-container">
            <div id="col-right">
                <div class="create-app">
                <a href="http://mobapper.com" class="button button-hero button-primary signup-button">
                    Create App &amp; Sign Up
                </a>
                </div>  
                <div class="mail-to-us-div">
                    <a href="mailto:info@mobapper.com" class="mailto-us" >Contact us</a>
                </div>
                
            </div>    
            <div id="col-left">
                <div class="col-wrap">
                    <?php
                    if (isset($_POST['submit_page'])) {
                        echo '<br class="clear">';
                    }
                    if (isset($_POST['submit_category'])) {
                        //echo 'cat';
                        global $mobapper_options;
                        $page_values = $mobapper_options['exclude_pages'];
                        $values = "";
                        $cats = $_POST['post_category'];
                        //print_r($cats);
                        $str = implode(", ", $cats);
                        $mobapper_options = array('exclude_categories' => $str,
                            'exclude_pages' => $page_values);
                        $category_values = $mobapper_options['exclude_categories'];
                        $cat_values_array = explode(",", $category_values);
                        update_option("mobapper_settings", $mobapper_options);
                        echo '<div class="alert alert-warning">
                                <strong>Mobapper settings saved</strong> 
                              </div>';
                    }
                    ?>

                    <h3>Select categories to exclude from mobile apps</h3>
                    <form id="category-exclude" action="" method="post" name="categoryexcludeform">
                         <table class="wp-list-table widefat fixed tags">
                            <thead>

                                <tr>

                                    <th scope="col" id="name" class="manage-column column-name sortable desc" style="width:50%">
                            <h4>&nbspCategory</h4>
                            </th>
                            <th scope="col" id="name" class="manage-column column-name sortable desc" style="width:50%">
                            <h4>Exclude</h4> 
                            </th>

                            </tr>
                            </thead>
                          </table>
                        <div class="div-scroll">
                           
                        <table class="wp-list-table widefat fixed tags">

                            <tbody id="the-list" data-wp-lists="list:tag">
                                <?php
                                $categories = get_categories();
                                $c = 0;
                                foreach ($categories as $category) {
                                    $c++;
                                    if ($category->term_id == 1 && $category->slug == 'uncategorized') {
                                        $c--;
                                        continue;
                                    }
                                    if ($c % 2 == 0) {
                                        $num = "alternate";
                                    } else {
                                        $num = "";
                                    }

                                    if (in_array($category->term_id, $cat_values_array)) {
                                        $checked = "checked";
                                    } else {
                                        $checked = "";
                                    }
                                    echo'<tr id="tag-2" class="' . $num . '">
                                      <td class="name column-name">
                                      <strong>
                                       ' . $category->name . '
                                      </strong>  
                                    </td>
                                    <td class="check-column">
                                       <input value="' . $category->term_id . '" type="checkbox" name="post_category[]" id="in-category-' . $category->term_id . '" ' . $checked . '/>
                                    </td>
                                  </tr>';
                                }
                                ?>

                            </tbody>
                        </table>
                        </div>
                        <table class="wp-list-table widefat fixed tags">
                            <thead></thead>
                            <tbody>
                                <tr class="alternate">
                                    <td></td>
                                    <td>
                                        <input type="submit" name="submit_category" value="Save" style="float: right" class="button button-primary button-large" />
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <br class="clear">
                    </form>

                </div>
            </div>

            

            <div id="col-left">
                <div class="col-wrap">
                    <?php
                    if (isset($_POST['submit_category'])) {
                        echo '<br class="clear">';
                    }
                    if (isset($_POST['submit_page'])) {
                        //echo 'page';
                        global $mobapper_options;
                        $values = "";
                        $cat_values = $mobapper_options['exclude_categories'];
                        $pages = $_POST['post_page'];
//                        print_r($pages);
                        $str = implode(", ", $pages);
                        $mobapper_options = array('exclude_categories' => $cat_values,
                            'exclude_pages' => $str);
                        $page_values = $mobapper_options['exclude_pages'];
                        $page_values_array = explode(",", $page_values);
                        update_option("mobapper_settings", $mobapper_options);
                        echo '<div class="alert alert-warning">
                                <strong>Mobapper settings saved</strong> 
                              </div>';
                    }
                    ?>

                    <h3>Select pages to exclude from mobile apps</h3>
                    <form id="category-exclude" action="" method="post" name="categoryexcludeform">
                         <table class="wp-list-table widefat fixed tags">
                            <thead>

                                <tr>
 
                                    <th scope="col" id="name" class="manage-column column-name sortable desc" style="width:50%">
                            <h4>&nbspPage</h4>
                            </th>
                            <th scope="col" id="name" class="manage-column column-name sortable desc" style="width:50%">
                            <h4>Exclude</h4> 
                            </th>

                            </tr>
                            </thead>
                         </table>   
                       <div class="div-scroll"> 
                        <table class="wp-list-table widefat fixed tags">

                            <tbody id="the-list" data-wp-lists="list:tag">
                                <?php
                                $pages = get_pages();
                                $c = 0;
                                foreach ($pages as $page) {
                                    $c++;
                                    if ($c % 2 == 0) {
                                        $num = "alternate";
                                    } else {
                                        $num = "";
                                    }

                                    if (in_array($page->ID, $page_values_array)) {
                                        $checked = "checked";
                                    } else {
                                        $checked = "";
                                    }
                                    echo'<tr id="tag-2" class="' . $num . '">
                                      <td class="name column-name">
                                      <strong>
                                       ' . $page->post_name . '
                                      </strong>  
                                    </td>
                                    <td class="check-column">
                                       <input value="' . $page->ID . '" type="checkbox" name="post_page[]" id="in-category-' . $page->ID . '" ' . $checked . '/>
                                    </td>
                                  </tr>';
                                }
                                ?>

                            </tbody>
                        </table>
                       </div>    
                        <table class="wp-list-table widefat fixed tags">
                            <thead></thead>
                            <tbody>
                                <tr class="alternate">
                                    <td></td>
                                    <td>
                                        <input type="submit" name="submit_page" value="Save" style="float: right" class="button button-primary button-large" />
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <br class="clear">
                    </form>

                </div>
            </div>




        </div>
    </div>
    <?php
}
