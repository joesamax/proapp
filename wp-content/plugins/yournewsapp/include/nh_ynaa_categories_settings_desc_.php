<?php

			$categories = @get_categories(array('orderby'=>'name', 'order'=>'ASC', 'hide_empty'=>0, 'taxonomy' => $this->nh_find_taxonomies()));
			if($categories){
				//var_dump($this->categories_settings);
				echo '<p>'.__('Here you can specify the names of the categories in the app individually. Assign categories to the default images that are displayed in the app, should a post or page have no post image. You can also set or define whether the category in the app should be hidden or not the sort order here.', 'nh-ynaa').'</p>';
        echo '<input type="hidden" name="'.$this->categories_settings_key.'[ts]" id="'.$this->categories_settings_key.'_ts" value="'.time().'" />';
				echo '<div id="categorie-div-con" class="categorie-div-con"><ul>';
				foreach($categories as $category){
					 //var_dump($this->categories_settings);
					 //var_dump($this->categories_settings[$category->term_id]);
					 if(!$this->categories_settings[$category->term_id]['cat_name']) $this->categories_settings[$category->term_id]['cat_name']= $category->cat_name;


            				$deactivated_cat_class = '';
            				$deactivated_cat_title = '';
            				if($this->categories_settings[$category->term_id]['hidecat']) {
            					$deactivated_cat_class = '-hidden';
								$deactivated_cat_title = __('This category is deactivated in the app', 'nh-ynaa');
            				}
           			?>
					<?php
					if($this->categories_settings[$category->term_id]['hidecat']){
						$yesradio = 'checked';
						$noradio = '';
						$disabled = 'disabled';
					}
					else{
						$yesradio = '';
						$noradio = 'checked';
						$disabled = '';
					}
					?>

            		<li class="cat<?php echo $deactivated_cat_class; ?>">

                    	<div class="image-div" id="<?php echo 'image-div'.$category->term_id;  ?>" style="background-image:url('<?php echo $this->categories_settings[$category->term_id]['img'] ?>')" data-link="<?php echo $category->term_id;  ?>" >
                        	<div class="ttitle ttitle<?php echo $deactivated_cat_class; ?>" title="<?php echo $deactivated_cat_title; ?>"><?php echo ($this->categories_settings[$category->term_id]['cat_name']); if($deactivated_cat_class != '') echo '<span>'.__('Category is not visible in the app!','nh-ynaa').'</span>'; ?></div>
                        </div>
                        <div>
                        	<div><a id="upload_image_button<?php echo $category->term_id; ?>" class="upload_image_button" href="#" name="<?php echo $this->categories_settings_key; ?>_items_<?php echo $category->term_id; ?>_img" data-image="<?php echo '#image-div'.$category->term_id;  ?>"   ><?php _e('Set default image for category','nh-ynaa'); ?></a>
           											<input <?php echo $disabled; ?> class="disabled<?php echo $category->term_id; ?>" type="hidden" value="<?php echo $this->categories_settings[$category->term_id]['img'] ?>" id="<?php echo $this->categories_settings_key; ?>_items_<?php echo $category->term_id; ?>_img" name="<?php echo $this->categories_settings_key; ?>[<?php echo $category->term_id; ?>][img]" data-id="image-div<?php echo $category->term_id; ?>" data-link="<?php echo $category->term_id;  ?>" /></div>


                            <?php  echo '<div class="reset-cat-img-link-cont" id="reset-cat-img-link-cont_'.$category->term_id.'">';
								if($this->categories_settings[$category->term_id]['img']) echo '<a href="'.$category->term_id.'" class="reset-cat-img-link">'.(__('Reset image', 'nh-ynaa')).'</a>'; else echo '<br>';
								echo '</div>'; ?>
                            <div>

                            	<div class="margin-botton"><input <?php echo $disabled; ?> class="disabled<?php echo $category->term_id; ?>" type="text" class="cat-name-input" value="<?php echo $this->categories_settings[$category->term_id]['cat_name']; ?>" name="<?php echo $this->categories_settings_key; ?>[<?php echo $category->term_id; ?>][cat_name]" placeholder="<?php echo $category->name; ?>"></div>
                                <div class="margin-botton"><label><?php _e('Order posts in category:', 'nh-ynaa'); ?></label>
                                <select <?php echo $disabled; ?> class="disabled<?php echo $category->term_id; ?>" name="<?php echo $this->categories_settings_key; ?>[<?php echo $category->term_id; ?>][cat_order]" id="cat_order">
                                	<option  value="date-desc" <?php if($this->categories_settings[$category->term_id]['cat_order'] == 'date-desc') echo ' selected '; ?>><?php _e('Recent posts','nh-ynaa') ?></option>
                                    <option value="date-asc" <?php if($this->categories_settings[$category->term_id]['cat_order'] == 'date-asc') echo ' selected '; ?>><?php _e('Oldest posts','nh-ynaa') ?></option>
                                    <option value="alpha-asc" <?php if($this->categories_settings[$category->term_id]['cat_order'] == 'alpha-asc') echo ' selected '; ?>><?php _e('Alphabetically','nh-ynaa') ?></option>
                                </select>
                                </div>
                            	<div class="margin-botton hide-cat-div"><?php _e('Hide this category and all posts in this category in the app:','nh-ynaa'); ?><br>

									<input data-class="disabled<?php echo $category->term_id; ?>" type="radio" class="hidecat" id="hidecat1<?php echo $category->term_id; ?>" name="<?php echo $this->categories_settings_key.'['.$category->term_id.'][hidecat]';?>" <?php echo $yesradio; ?> value="1"> <label for="hidecat1<?php echo $category->term_id; ?>"><?php _e('Yes', 'nh-ynaa');  ?></label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <input data-class="disabled<?php echo $category->term_id; ?>" type="radio" class="hidecat"   id="hidecat0<?php echo $category->term_id; ?>" name="<?php echo $this->categories_settings_key.'['.$category->term_id.'][hidecat]';?>" value="0" <?php echo $noradio; ?>><label for="hidecat0<?php echo $category->term_id; ?>"><?php _e('No', 'nh-ynaa'); ?></label>
								</div>
                                <div class="margin-botton hide-cat-home"><?php _e('Hide this category and all posts in this category on the homescreen:','nh-ynaa'); ?><br>
									<?php
									$active = 0;
									if(isset($this->categories_settings[$category->term_id]['hidecathome']) && $this->categories_settings[$category->term_id]['hidecathome']) $active=1;
									?>
									<input <?php echo $disabled; ?>  type="radio" class="disabled<?php echo $category->term_id; ?>" id="hidecathome1<?php echo $category->term_id; ?>" name="<?php echo $this->categories_settings_key.'['.$category->term_id.'][hidecathome]';?>" <?php if($active)echo 'checked'; ?> value="1"> <label for="hidecathome1<?php echo $category->term_id; ?>"><?php _e('Yes', 'nh-ynaa');  ?></label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									<input <?php echo $disabled; ?>  type="radio" class="disabled<?php echo $category->term_id; ?>"   id="hidecathome0<?php echo $category->term_id; ?>" name="<?php echo $this->categories_settings_key.'['.$category->term_id.'][hidecathome]';?>" value="0" <?php if(!$active)echo 'checked'; ?>><label for="hidecathome0<?php echo $category->term_id; ?>"><?php _e('No', 'nh-ynaa'); ?></label>

								</div>
								<div class="use-cat-image-div">
									<?php
									$active = 0;
									if(isset($this->categories_settings[$category->term_id]['usecatimg']) && $this->categories_settings[$category->term_id]['usecatimg'] ) $active=1;
									?>
                                	<div class="margin-botton">
										<label for="use_cat_image0"><input <?php echo $disabled; ?> class="disabled<?php echo $category->term_id; ?>" type="radio" value="0"  name="<?php echo $this->categories_settings_key.'['.$category->term_id.'][usecatimg]';?>" id="use_cat_image1" <?php if(!$active) echo 'checked'; ?>> <?php _e('Use post image on homescreen', 'nh-ynaa'); ?></label>
                                    </div>
                                    <div class="margin-botton">
										<label for="use_cat_image1"><input <?php echo $disabled; ?> class="disabled<?php echo $category->term_id; ?>" type="radio" value="1"  name="<?php echo $this->categories_settings_key.'['.$category->term_id.'][usecatimg]';?>" id="use_cat_image1" <?php if($active) echo 'checked'; ?>> <?php _e('Use category image on homescreen', 'nh-ynaa'); ?></label>
                                     </div>

                                </div>
								<div class="margin-botton push-default"><?php _e('Activate push on default for this category:','nh-ynaa'); ?><br>
									<?php
									$active = 1;
									if(isset($this->categories_settings[$category->term_id]['pDA']) && !$this->categories_settings[$category->term_id]['pDA']) $active=0;
									?>
									<input <?php echo $disabled; ?>  type="radio" class="disabled<?php echo $category->term_id; ?>" id="pushdefaultactive1<?php echo $category->term_id; ?>" name="<?php echo $this->categories_settings_key.'['.$category->term_id.'][pDA]';?>" <?php if($active)echo 'checked'; ?> value="1"> <label for="pushdefaultactive1<?php echo $category->term_id; ?>"><?php _e('Yes', 'nh-ynaa');  ?></label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									<input <?php echo $disabled; ?>  type="radio" class="disabled<?php echo $category->term_id; ?>"   id="pushdefaultactive0<?php echo $category->term_id; ?>" name="<?php echo $this->categories_settings_key.'['.$category->term_id.'][pDA]';?>" value="0" <?php if(!$active)echo 'checked'; ?>><label for="pushdefaultactive0<?php echo $category->term_id; ?>"><?php _e('No', 'nh-ynaa'); ?></label>

								</div>

                            	<div class="margin-botton  show-subcat-div">
							<?php
								if(get_categories(array('hide_empty'=>0, 'child_of'=>$category->term_id, 'taxonomy'=>$category->taxonomy))){
									if($this->categories_settings[$category->term_id]['showsub']){
										$yesradio = 'checked';
										$noradio = '';
										$hidethisdiv = "";
									}
									else{
										$yesradio = '';
										$noradio = 'checked';
										$hidethisdiv = "hidethisdiv";
									}
									echo '<div class="margin-botton">';
									 _e('Show subcategories overview:', 'nh-ynaa');
									 echo '<br><input '.$disabled.'  type="radio" name="'.$this->categories_settings_key.'['.$category->term_id.'][showsub]" value="1" id="yesradio_'.$category->term_id.'" '.$yesradio.' class="showoverviewposts disabled'.$category->term_id.'" data-catid="'.$category->term_id.'" /><label for="yesradio_'.$category->term_id.'">'; _e('Yes', 'nh-ynaa');
									 echo '</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input '.$disabled.' type="radio" name="'.$this->categories_settings_key.'['.$category->term_id.'][showsub]" value="0" id="noradio_'.$category->term_id.'" '.$noradio.' class="showoverviewposts disabled'.$category->term_id.'" data-catid="'.$category->term_id.'" /><label for="noradio_'.$category->term_id.'">'; _e('No', 'nh-ynaa');
									 echo '</label>';
									echo '</div>';
								//SUb categories overview show post


									if($this->categories_settings[$category->term_id]['showoverviewposts']){
										$yesradioshowoverviewposts = 'checked';
										$noradioshowoverviewposts = '';


									}
									else{
										$yesradioshowoverviewposts = '';
										$noradioshowoverviewposts = 'checked';

									}
									echo '<div id="showoverviewposts'.$category->term_id.'" class="categorieovervie_sub '.$hidethisdiv.'">';
									_e('Show posts under subcategories overview', 'nh-ynaa');
									 echo '<br><input '.$disabled.' class="disabled'.$category->term_id.'" type="radio" name="'.$this->categories_settings_key.'['.$category->term_id.'][showoverviewposts]" value="1" id="yesshowoverviewpostsradio_'.$category->term_id.'" '.$yesradioshowoverviewposts.' /><label for="yesshowoverviewpostsradio_'.$category->term_id.'">'; _e('Yes', 'nh-ynaa');
									 echo '</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input '.$disabled.' class="disabled'.$category->term_id.'" type="radio" name="'.$this->categories_settings_key.'['.$category->term_id.'][showoverviewposts]" value="0" id="noshowoverviewpostsradio_'.$category->term_id.'" '.$noradioshowoverviewposts.' /><label for="noshowoverviewpostsradio_'.$category->term_id.'">'; _e('No', 'nh-ynaa');
									 echo '</label>';
									 echo '</div>';


								}

							 ?>
                             	</div>
								<?php
								do_action('nh_category_list_action',$category->term_id, $this->categories_settings[$category->term_id], $this->categories_settings_key, $disabled);
								?>
                        	</div>
                        </div>

                    </li>
            <?php
				}
				echo '</ul></div><div class="clear"></div>';

            }
			else {
				_e('No categories.', 'nh-ynaa');
			}
			if($this->general_settings['eventplugin'] || $this->general_settings['location']){
			echo '<h3>App Extras</h3>';
			echo '<div id="extras-div-con"  class="categorie-div-con"><ul>';
			//Events
			if($this->general_settings['eventplugin']){
					$category->term_id = -1;

					if(!$this->categories_settings[$category->term_id]['cat_name']) $this->categories_settings[$category->term_id]['cat_name']= __('Events','nh-ynaa');
				?>
					<li>

						<div class="image-div" id="<?php echo 'image-div'.$category->term_id;;  ?>" style="background-image:url('<?php echo $this->categories_settings[$category->term_id]['img'] ?>')" data-link="-1" >
                        	<div class="ttitle"><?php echo ($this->categories_settings[$category->term_id]['cat_name']); ?></div>
                        </div>
                        <div><a id="upload_image_button<?php echo $category->term_id; ?>" class="upload_image_button" href="#" name="<?php echo $this->categories_settings_key; ?>_items_<?php echo $category->term_id; ?>_img" data-image="<?php echo '#image-div'.$category->term_id;  ?>"   ><?php _e('Set default image for events','nh-ynaa'); ?></a>
           											<input type="hidden" value="<?php echo $this->categories_settings[$category->term_id]['img'] ?>" id="<?php echo $this->categories_settings_key; ?>_items_<?php echo $category->term_id; ?>_img" name="<?php echo $this->categories_settings_key; ?>[<?php echo $category->term_id; ?>][img]" data-id="image-div<?php echo $category->term_id; ?>" data-link="<?php echo $category->term_id;  ?>" /></div>
                        <?php
						echo '<div id="reset-cat-img-link-cont_'.$category->term_id.'">';
							if($this->categories_settings[$category->term_id]['img']) echo '<a href="'.$category->term_id.'" class="reset-cat-img-link">'.(__('Reset image', 'nh-ynaa')).'</a>'; else echo '<br>';
						echo '</div>'; ?>
                        <div><input type="text" class="cat-name-input" value="<?php echo $this->categories_settings[$category->term_id]['cat_name']; ?>" name="<?php echo $this->categories_settings_key; ?>[<?php echo $category->term_id; ?>][cat_name]"></div>

					</li>
               <?php
			}
			//Map
			if($this->general_settings['location']){
					$category->term_id = -98;

					if(!$this->categories_settings[$category->term_id]['cat_name']) $this->categories_settings[$category->term_id]['cat_name']= __('Locations','nh-ynaa');
				?>
					<li>

						<div class="image-div" id="<?php echo 'image-div'.$category->term_id;;  ?>" style="background-image:url('<?php echo $this->categories_settings[$category->term_id]['img'] ?>')" data-link="-1" >
                        	<div class="ttitle"><?php echo ($this->categories_settings[$category->term_id]['cat_name']); ?></div>
                        </div>
                        <div><a id="upload_image_button<?php echo $category->term_id; ?>" class="upload_image_button" href="#" name="<?php echo $this->categories_settings_key; ?>_items_<?php echo $category->term_id; ?>_img" data-image="<?php echo '#image-div'.$category->term_id;  ?>"   ><?php _e('Set default image for location','nh-ynaa'); ?></a>
           											<input type="hidden" value="<?php echo $this->categories_settings[$category->term_id]['img'] ?>" id="<?php echo $this->categories_settings_key; ?>_items_<?php echo $category->term_id; ?>_img" name="<?php echo $this->categories_settings_key; ?>[<?php echo $category->term_id; ?>][img]" data-id="image-div<?php echo $category->term_id; ?>" data-link="<?php echo $category->term_id;  ?>" /></div>
                        <?php
						echo '<div id="reset-cat-img-link-cont_'.$category->term_id.'">';
							if($this->categories_settings[$category->term_id]['img']) echo '<a href="'.$category->term_id.'" class="reset-cat-img-link">'.(__('Reset image', 'nh-ynaa')).'</a>'; else echo '<br>';
						echo '</div>'; ?>
                        <div><input type="text" class="cat-name-input" value="<?php echo $this->categories_settings[$category->term_id]['cat_name']; ?>" name="<?php echo $this->categories_settings_key; ?>[<?php echo $category->term_id; ?>][cat_name]"></div>

					</li>
               <?php
			}
			echo '</ul></div>';

			echo '<div class="clear"></div>';
			}
			//var_dump($this->general_settings);

?>