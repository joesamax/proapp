<?php $menu_id=0;
//var_dump($this->teaser_settings);
_e('Here you can set up the teaser which will be displayed on the start view of your app.','nh-ynaa');
 ?>
 <input type="hidden" name="<?php echo $this->teaser_settings_key; ?>[ts]" value="<?php echo time(); ?>" />
<div>
	<table class="form-table">
		<tbody>
			<tr>
				<th><?php _e('Teaser source','nh-ynaa'); ?></th>
				<td>
					<select id="teaser_source"  name="<?php echo $this->teaser_settings_key; ?>[source]" class="nh-floatleft" >
						<option value="indi" <?php if($this->teaser_settings['source']=='indi') echo 'selected="selected"'; ?>><?php _e('Individual','nh-ynaa'); ?></option>
						<option value="cat" <?php if($this->teaser_settings['source']=='cat') echo 'selected="selected"'; ?>><?php _e('Category','nh-ynaa'); ?></option>
						<option value="recent" <?php if($this->teaser_settings['source']=='recent') echo 'selected="selected"'; ?>><?php _e('Recent posts','nh-ynaa'); ?></option>
					</select>
					<div class="helptext padding5"><?php _e('Select your source for the teasers in the app.'); ?></div>

				</td>
			</tr>
			<tr class="teaser_categories" style="<?php if($this->teaser_settings['source']!='cat') echo 'display:none;';?>" >
				<th><?php _e('Teaser Category', 'nh-ynaa');?></th>
				<td><?php
					$args=array(
								  'orderby' => 'name',
								  'order' => 'ASC',
								  'taxonomy' => $this->nh_find_taxonomies()
								);
								$categories = get_categories( $args );

								if($categories){
									echo '<select id="teaser_cat" name="'.$this->teaser_settings_key.'[cat]" class="nh-floatleft">';
										foreach ( $categories as $category ) {
										$selected = "";
											if($this->teaser_settings['cat']==$category->term_id) $selected = ' selected="selected" ' ;
										echo '<option value="'.$category->term_id.'" '.$selected.'>'.$category->name.'</option>';

									}
									echo '</select>';
								}
								else {
									echo '<select><option>'.__('No categories existing', 'nh-ynaa').'</option></select>';
								}
				?></td>
			</tr>
			<tr style="<?php if(!isset($this->teaser_settings['source']) || $this->teaser_settings['source']=='indi') echo 'display:none;'; ?>" class="teaser_limit">
				<th><?php _e('Teaser limit', 'nh-ynaa'); ?></th>
				<td>
					<input type="number" name="<?php echo $this->teaser_settings_key; ?>[limit]" max="10" value="<?php echo $this->teaser_settings['limit']; ?>" class="my-input-field nh-floatleft" step="1" min="1" >
					<div class="helptext"><?php _e('Number of teaser to be shown.','nh-ynaa'); ?></div>
				</td>
			</tr>
			<tr style="<?php if(!isset($this->teaser_settings['source']) || $this->teaser_settings['source']=='indi') echo 'display:none;'; ?>" class="teaser_limit">
				<th><?php _e('Ignore this post on homescreen', 'nh-ynaa'); ?></th>
				<td>
					<input type="checkbox" name="<?php echo $this->teaser_settings_key; ?>[hidehome]" value="1" class="my-input-field nh-floatleft" <?php if(isset($this->teaser_settings['hidehome']) ) echo ' checked '; ?> >
					<div class="helptext"><?php _e('Exclude teaserposts from the posts listing on the startview.','nh-ynaa'); ?></div>
				</td>
			</tr>
		</tbody>
	</table>
</div>
<div id="nav-menus-frame" style="<?php if(isset($this->teaser_settings['source']) && $this->teaser_settings['source']!='indi') echo 'display:none;'; ?>">
	<div id="menu-settings-column" class="metabox-holder">
		<div class="clear"></div>
		<div class="accordion-container" id="side-sortables">
			<ul class="outer-border">
				<?php

					$post_types = get_post_types();

					foreach( $post_types as $post_type ){
						if( !in_array( $post_type, array( 'attachment', 'revision', 'nav_menu_item' ) ) ){
						?>
							<li id="add-<?php echo $post_type; ?>" class="control-section accordion-section add-<?php echo $post_type; ?>">
								<h3 title="<?php echo ucfirst(__($post_type,'nh-ynaa')); ?>" tabindex="0" class="accordion-section-title hndle"><?php echo ucfirst(__($post_type,'nh-ynaa'));?></h3>
								<div class="accordion-section-content ">
									<div class="inside">
									<?php
										$args = array(
											'post_type'=> $post_type,
											'order'    => 'post_modified',
											'post_status'=>'publish'
											//,
											//'nopaging' => true
											);
										$the_query = new WP_Query( $args );
										if($the_query->have_posts() ) :
											?>
											<div class="posttypediv tabclass" id="posttype-<?php echo $post_type; ?>">
												<ul class="add-menu-item-tabs">
													<li><a href="#tabs-panel-posttype-<?php echo $post_type; ?>-most-recent" data-type="tabs-panel-posttype-<?php echo $post_type; ?>-most-recent" class="nav-tab-link"><?php _e('Most Recent','nh-ynaa'); ?></a></li>
													<!--<li><a class="nav-tab-link" data-type="<?php echo $post_type; ?>-all" href="#<?php echo $post_type; ?>-all"><?php _e('View All','nh-ynaa'); ?></a></li>-->
													<li>
														<a href="#tabs-panel-posttype-<?php echo $post_type; ?>-search" data-type="tabs-panel-posttype-<?php echo $post_type; ?>-search" class="nav-tab-link">
															<?php _e('Search');?></a>
													</li>
												</ul><!-- .posttype-tabs -->
												<div class="tabs-panel tabs-panel-active" id="tabs-panel-posttype-<?php echo $post_type; ?>-most-recent">
													<ul class="categorychecklist form-no-clear" id="<?php echo $post_type; ?>checklist-most-recent">
													<?php
													$i=0;
													$li = "";
													while ( $the_query->have_posts() ) : $the_query->the_post();
														//if($i>7) break;
														$temp = "";
														$temp .= '<li>';
														$temp .=  '<label class="menu-item-title">';
														$temp .=  '<input type="checkbox" value="'.$the_query->post->ID.'" name="menu-item-'.$post_type.$menu_id.'" class="menu-item-checkbox" /> ';
														$temp .=  $this->shortenText($the_query->post->post_title).'</label>';
														$temp .=  '</li>';
														echo $temp;
														$li .= $temp;
														$i++;
														$menu_id++;
													endwhile;
													?>
													</ul>
												</div><!-- /.tabs-panel -->


                                                <div id="tabs-panel-posttype-<?php echo $post_type; ?>-search" class="tabs-panel">
                                                        <p class="quick-search-wrap">
                                                			<input type="search" name="quick-search-posttype-<?php echo $post_type; ?>" value="" title="Suchen" class="quick-search" autocomplete="off">
                                                            <input type="hidden" value="<?php echo $post_type; ?>" class="search-post-type" />

                                                            <span class="spinner" style="display: none;"></span>
                                                			<input type="submit" value="Suchen" class="button button-small quick-search-submit hide-if-js" id="submit-quick-search-posttype-page" name="submit">		</p>

                                                    <ul class="categorychecklist form-no-clear" data-wp-lists="list:page" id="page-search-checklist"></ul>
                                                </div>

												<p class="button-controls">
													<span class="list-controls"><a class="select-all" href="<?php echo $_SERVER['PHP_SELF']; ?>?page=ynaa_plugin_options&page-tab=all&amp;selectall=1#posttype-<?php echo $post_type; ?>"><?php _e('Select All'); ?></a></span>
													<span class="add-to-menu">
														<input type="submit" id="submit-posttype-<?php echo $post_type; ?>" name="add-post-type-menu-item" value="<?php _e('Add to Teaser','nh-ynaa'); ?>" class="button-secondary submit-add-to-teaser right">
														<span class="spinner"></span>
													</span>
												</p>
											</div><!-- /.posttypediv -->

											<?php
										else:
											_e('No items.');
										endif; //End Post Query
									?>
									</div><!-- .inside -->
								</div><!-- .accordion-section-content -->
							</li><!-- .accordion-section -->
						<?php


						}
					}
				?>
                <li id="add-custom-categories" class="control-section accordion-section   add-custom-categories">
				<h3 title="<?php _e('Categories'); ?>" tabindex="0" class="accordion-section-title hndle"><?php _e('Categories'); ?></h3>
				<div class="accordion-section-content ">
					<div class="inside">
						<div id="customcategoriediv" class="customlinkdiv">
							<ul class="categorychecklist form-no-clear" id="cat-checklist-all">
								<?php
								$args=array(
								  'orderby' => 'name',
								  'order' => 'ASC',
								  'taxonomy' => $this->nh_find_taxonomies()
								);
								$categories = get_categories( $args );
								$post_type = 'cat';
								foreach ( $categories as $category ) {
									//var_dump($this->categories_settings[$category->term_id]);
									if(isset($this->categories_settings[$category->term_id]) && $this->categories_settings[$category->term_id]['hidecat']==1) continue;

									/* check if category is used in teaser */
									$active_class = '';
									foreach($this->teaser_settings['teaser'] as $k=>$v) {
										if($k && $k == 'type') continue;
										if($this->teaser_settings['teaser']['type'][$k] == 'cat') {
											if($v == $category->term_id) {
												$active_class = ' activated';
												break;
											}
										}
									}

									echo '<li>';
									echo '<input type="hidden" value="'.$post_type.'" name="type-menu-item-'.$post_type.$menu_id.'" id="type-menu-item-'.$post_type.$menu_id.'">';
									echo '<input type="hidden" value="'.$post_type.'" name="link-type-menu-item-'.$post_type.$menu_id.'" id="link-type-menu-item-'.$post_type.$menu_id.'">';
									echo '<input type="hidden" value="'.$category->name.'" name="title-menu-item-'.$post_type.$menu_id.'" id="title-menu-item-'.$post_type.$menu_id.'">';
									echo '<label class="menu-item-title'.$active_class.'">';
									echo '<input type="checkbox" value="'.$category->term_id.'" name="menu-item-'.$post_type.$menu_id.'" class="menu-item-checkbox" /> ';
									echo $category->name.'</label>';
									echo '</li>';
									$menu_id++;
								}
									//wp_category_checklist();

								?>
							</ul>
							<p class="button-controls">
								<span class="list-controls"><a class="select-all" href="<?php echo $_SERVER['PHP_SELF']; ?>?page=ynaa_plugin_options&page-tab=all&amp;selectall=1#category ?>"><?php //_e('Select All'); ?></a></span>
								<span class="add-to-menu">
									<input type="submit" id="submit-customcategorydiv" name="add-custom-menu-item" value="<?php _e('Add to Teaser'); ?>" class="button-secondary submit-add-to-teaser right">
									<span class="spinner"></span>
								</span>
							</p>

						</div><!-- /.customlinkdiv -->
					</div><!-- .inside -->
				</div><!-- .accordion-section-content -->
			</li>
				<?php if(true) { ?>
					<li id="add-app-menus" class="control-section accordion-section   add-app-menus">
						<h3 title="<?php _e('App Extras').'nh-ynaa'; ?>" tabindex="0" class="accordion-section-title hndle"><?php _e('App Extras'); ?></h3>
						<div class="accordion-section-content ">
							<div class="inside">
								<div id="app-menusdiv" class="customlinkdiv">
									<ul class="categorychecklist form-no-clear" id="app-checklist-all">
										<?php
										/*$args=array(
                                          'orderby' => 'name',
                                          'order' => 'ASC'
                                        );
                                        $categories = get_categories( $args );
                                        */

										foreach ( $this->appmenus_pre as $k=>$appmenu_pre ) {
											if(!in_array($appmenu_pre['type'], $this->teaser_support_type)) continue;
											//if($appmenu_pre['id']!=-1 &&  $appmenu_pre['id'] !=-2 &&  $appmenu_pre['id'] !=-98 && $appmenu_pre['id'] !=-3 ) continue;
											//if($appmenu_pre['id']==-1 && !$this->general_settings['eventplugin']) continue;

											echo '<li>';
											echo '<input type="hidden" value="'.$appmenu_pre['type'].'" name="type-menu-item-'.$appmenu_pre['type'].$menu_id.'" id="type-menu-item-'.$appmenu_pre['type'].$menu_id.'">';
											echo '<input type="hidden" value="cat" name="link-type-menu-item-'.$appmenu_pre['type'].$menu_id.'" id="link-type-menu-item-'.$appmenu_pre['type'].$menu_id.'">';
											echo '<input type="hidden" value="'.$appmenu_pre['title'].'" name="title-menu-item-'.$appmenu_pre['type'].$menu_id.'" id="title-menu-item-'.$appmenu_pre['type'].$menu_id.'">';
											echo '<input type="hidden" value="'.$k.'" name="app-menu-item-'.$appmenu_pre['type'].$menu_id.'" id="app-menu-item-'.$appmenu_pre['type'].$menu_id.'">';
											echo '<label class="menu-item-title">';
											echo '<input type="checkbox" value="'.$appmenu_pre['id'].'" name="menu-item-'.$appmenu_pre['type'].$menu_id.'" class="menu-item-checkbox" /> ';
											echo $appmenu_pre['title'].'</label>';
											echo '</li>';
											$menu_id++;
										}

										do_action('nh_add_app_extras_teaser');

										//wp_category_checklist();

										?>
									</ul>
									<p class="button-controls">
										<!--<span class="list-controls"><a class="select-all" href="<?php echo $_SERVER['PHP_SELF']; ?>?page=ynaa_plugin_options&page-tab=all&amp;selectall=1#category ?>"><?php //_e('Select All'); ?></a></span>-->
								<span class="add-to-menu">
									<input type="submit" id="submit-customcategorydiv" name="add-custom-teaser-item" value="<?php _e('Add to teaser','nh-ynaa'); ?>" class="button-secondary submit-add-to-teaser right">
									<span class="spinner"></span>
								</span>
									</p>

								</div><!-- /.customlinkdiv -->
							</div><!-- .inside -->
						</div><!-- .accordion-section-content -->
					</li>
				<?php }
				 do_action('nh_add_accordion_teaser');
				?>
		</ul><!-- .outer-border -->
        <input type="hidden" value="<?php echo $menu_id; ?>" id="menu_id_counter">
		</div>
	</div><!-- /#menu-settings-column -->

	<div id="menu-management-liquid" >
		<div id="menu-management">
			<div class="menu-edit ">
				<div id="nav-menu-header">
					<div class="major-publishing-actions">
						<div class="publishing-action"><?php _e('Teaser','nh-ynaa'); ?></div>
					</div><!-- END .major-publishing-actions -->
				</div><!-- END .nav-menu-header -->
					<div id="post-body">

						<div id="post-body-content" class="">
							<h3><?php _e('Teaser Structure','nh-ynaa'); ?></h3>
							<div class="drag-instructions post-body-plain">
								<p><?php _e('Here you change the settings for teasers on the home screen.','nh-ynaa'); ?></p>
							</div>
							<div id="menu-accordion">
								<ul id="menu-to-edit" class="menu nav-menus-php nh-teaser-ul drag-teaser">
									<?php
									if($this->teaser_settings['teaser'] && !empty($this->teaser_settings['teaser'])){
										$menuitems= $this->teaser_settings['teaser'];
										//var_dump($menuitems);
										foreach($menuitems as $k=>$v){
											//if($k && $k=='type') continue;

											$deactivated_class = '';
											$deactivated_cat_title = '';

											if($v['type']=='cat'){

												$category = $this->nh_get_category($v['id']);

												if($category) {
													if(!empty($this->categories_settings[$v['id']])) {
														$cat = $this->categories_settings[$v['id']];
														//var_dump($v, $cat, $this->categories_settings);
														if ($cat['hidecat']) {
															$deactivated_class = ' cat_deactivated';
															$deactivated_cat_title = __('This category is deactivated in the app', 'nh-ynaa');
														}
														if ($cat) {
															//if($cat['hidecat']==1) $result['error'] = 1;;
															$title = $cat['cat_name'];
															if ($cat['usecatimg'] == 1 && !empty($cat['img'])) {
																$thumb = $cat['img'];
															} else {
																$post = $this->nh_wp_get_recent_posts(1, $v['id']);
																$result['uma']['$post_id'] = $post;
																$result['uma']['$post[0]'] = $post[0];
																$result['uma']['$post[0]->ID'] = $post[0]->ID;
																$result['uma']['$post[0]->[ID]'] = $post[0]['ID'];
																$thumb = $this->nh_getthumblepic($post[0]['ID'], 'full');;
															}
														}
													}
													else {
													//	var_dump($category);
														$title = $category->name  ;

														$post = $this->nh_wp_get_recent_posts(1,$v['id'] );
														$result['uma']['$post_id']=$post;
														$result['uma']['$post[0]']=$post[0];
														$result['uma']['$post[0]->ID']=$post[0]->ID;
														$result['uma']['$post[0]->[ID]']=$post[0]['ID'];
														$thumb = $this->nh_getthumblepic($post[0]['ID'],'full');  ;
													}
													//echo $title;
												}
											}
											else {
												if(isset($v['img'])) $thumb = $v['img'];
												else $thumb = $this->nh_getthumblepic($v['id']);
												if(isset($v['title']))$title = $v['title'];
												else $title = get_the_title($v['id']);
											}
											//echo $title;

										?>
											<li id="teaserli<?php echo $k; ?>"  class="floatli teaserli"><div id="image-div<?php echo $k; ?>" class="teaserdiv<?php echo $deactivated_class; ?>" style="background-image:url('<?php echo $thumb; ?>');" title="<?php echo $deactivated_cat_title; ?>">
                                                <div class="ttitle"><?php echo $title; ?></div>
                                                </div>
                                                <div>
                                                <a href="<?php echo $k; ?>" class="dellteaser"><?php _e('Delete'); ?></a>
                                                <input type="hidden" value="<?php echo $v['id']; ?>"  name="<?php echo $this->teaser_settings_key; ?>[teaser][<?php echo $k; ?>][id]" />
                                                <input type="hidden" value="<?php echo $v['type']; ?>"  name="<?php echo $this->teaser_settings_key; ?>[teaser][<?php echo $k; ?>][type]" />
													<?php
													if(in_array($v['type'], $this->teaser_support_type) || isset($v['customfilter']) ){
														echo '<span style="float: right;"><span id="reset-cat-img-link-cont_'.$k.'">';
														$hidea = '';
														if($thumb) {
															echo '<a href="'.$k.'" class="reset-cat-img-link">Reset image</a>';

															$hidea = 'display:none;';
														}
														echo '</span><a style="'.$hidea.'" href="#" class="upload_image_button" id="upload_image_teaser'.$k.'" name="nh_ynaa_categories_settings_items_' .$k. '_img">Set image</a></span>';
														echo '<input type="hidden" value="'.$v['img'].'" id="nh_ynaa_categories_settings_items_' .$k. '_img" name="' . $this->teaser_settings_key.  '[teaser]['.$k.'][img]" data-id="image-div'.$k.'" data-link="'.$k.'">';
														echo '<div>'.
															'<input data-titleid="#image-div'.$k.'" type="text" value="'.$v['title'].'" name="' . $this->teaser_settings_key. '[teaser]['.$k.'][title]" placeholder="Enter link title here"  class="teaser_title_input">';

														if($v['type']=='webview')
															echo '<input type="url" value="'.$v['url'].'" name="' .$this->teaser_settings_key. '[teaser]['.$k.'][url]" placeholder="Enter link URL here">';
														echo '</div>';
														if(isset($v['customfilter'])){
															echo '<input type="hidden" value="'.$v['customfilter'].'" name="'.$this->teaser_settings_key.'[teaser]['.$k.'][customfilter]" id="'.$this->teaser_settings_key.'_teaser_'.$k.'_customfilter">';
														}
													}

													?>
                                                </div>
                                            </li><!--End menu-item -->
										<?php
										}
										?>


										<?php
									}
									?>



								</ul>
                                <div style="clear:both;"></div>
							</div><!-- /#menu-accordion -->
						</div><!-- /#post-body-content -->
					</div><!-- /#post-body -->
					<div id="nav-menu-footer">
						<div class="major-publishing-actions">
							<div class="publishing-action">&nbsp;<?php //submit_button(); ?>
								<!--<input type="submit" name="save_menu" id="save_menu_header" class="button button-primary menu-save" value="Men� erstellen">-->
							</div><!-- END .publishing-action -->
						</div><!-- END .major-publishing-actions -->
					</div><!-- /#nav-menu-footer -->
				</div><!-- /.menu-edit -->
			<!--</form>--><!-- /#update-nav-menu -->
		</div><!-- /#menu-management -->
	</div><!-- /#menu-management-liquid -->
</div><!-- /#nav-menus-frame -->
<div style="clear:both;"></div>