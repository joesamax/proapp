jQuery(document).ready(function ($) {
    //wp_option variable
    var $general_settings_key = 'nh_ynaa_general_settings';
    if (php_data.general_settings_key) {
        $general_settings_key = php_data.general_settings_key;
        var $menu_settings_key = php_data.menu_settings_key;
        var $teaser_settings_key = php_data.teaser_settings_key;
        var $homepreset_settings_key = php_data.homepreset_settings_key;
        var $delete = php_data.delete2;
        var $catText = php_data.catText;
        var $allowremoveText = php_data.allowremoveText;
        var $color01 = php_data.color01;
        var $hideTitleStartscreen = php_data.hideTitleStartscreen;
        var $hideTitleStartscreenTT = php_data.hideTitleStartscreenTT;

    }


    if ($('.my-color-field').length > 0) {
        $('.my-color-field').wpColorPicker({
            //clear: function() {alert('Dieser Wert ist ung�ltig.');}
        });
    }



    //Secon Submit Button
    $('.submitbutton').click(function () {
        $('#submit').trigger('click');
    });

    //Menu select tabs
    $('.tabclass').tabs({
        activate: function (event, ui) {
            $(ui.oldPanel).removeClass('tabs-panel-active');
            $(ui.newPanel).addClass('tabs-panel-active');
        }
    });

    //Menu Select
    $('a.select-all').on("click", function (event) {
        $(event.target).parentsUntil('div.inside').find('input[type="checkbox"]').attr("checked", false);
        $(event.target).parentsUntil('div.inside').children('div.tabs-panel-active').find('input[type="checkbox"]').attr("checked", true);
        return false;
    });

    //Menu accoding
    $('#menu-to-edit.nh-menu-ul')
      /*.accordion({
          header: "> li > dl",
          collapsible: true,
          heightStyle: "content"
      })*/
      .sortable({
          axis: "y",
          handle: "dl",
          stop: function (event, ui) {
              // IE doesn't register the blur when sorting
              // so trigger focusout handlers to remove .ui-state-focus
              ui.item.children("dl").triggerHandler("focusout");
          },
          update: function (event, ui) {
              var $i = 0;
              $.each($('.menu-pos-ynaa'), function (key, ob) {
                  $i++;
                  $(ob).val($i);
              });
          }
      });
    /*$('#menu-to-edit.nh-menu-ul > li').accordion({
        header: "> dl",
        collapsible: true,
        heightStyle: "content",
        active: false
    });*/
    $('#menu-to-edit.nh-menu-ul .item-edit').live('click',function(){
        $(this).parents('li').toggleClass('menu-item-edit-inactive','menu-item-edit-active');
        //alert($(this).parents('li').attr('id'));
        return false;

    });


   // $('#menu-to-edit.nh-menu-ul > li > dl :first').trigger('click');

    //teaser draggable
    $('#menu-to-edit.nh-teaser-ul, #menu-to-edit.nh-homepreset-ul').sortable();

    //Add Items to menu
    $('.submit-add-to-menu').click(function (e) {
        //alert(2);
        if ($(e.target).parentsUntil('div.inside').children('div.tabs-panel-active') && $(e.target).parentsUntil('div.inside').children('div.tabs-panel-active').length > 0) {

            var obj = ($(e.target).parentsUntil('div.inside').children('div.tabs-panel-active').find('input[type="checkbox"]:checked'));
        }
        else {
            var obj = ($(e.target).parentsUntil('div.inside').find('input[type="checkbox"]:checked'));
        }

        $.each(obj, function (key, ob) {

            var $id = getMaxMenuID();
            var $pos = (getItemscount('ul#menu-to-edit.nh-menu-ul li'));
            while ($('#menu-item-' + $pos).length > 0) {
                $pos++;
            }
            var $o = $(ob);
            if (typeof ($o.attr('checked')) != 'undefined') {
                var nameattr = $o.attr('name');
                var $type = $('#type-' + nameattr).val();
                var $menu_id = $o.val();
                //alert($menu_id);
                var $title = $('#title-' +nameattr).val();
                /*var $post_date = $('#post_date-'+$o.attr('name')).val(); //alert($post_date);
                if (typeof $post_date == 'undefined') $post_date=0;*/
                var $inputurl = '';
                if($('#extrafield-' + nameattr).length>0) {
                    var extrafields = JSON.parse($('#extrafield-' + nameattr).val());
                    $.each(extrafields, function(i, item) {
                        $inputurl += '<div style="clear:left; margin-top:10px;"><p><label for="edit-menu-item-'+i+'-' + $pos + '">'+item.label+'<br><input type="'+item.type+'" value="'+item.value+'" name="' + $menu_settings_key + '[menu][' + $pos + ']['+i+']" class="widefat edit-menu-item-url" id="edit-menu-item-'+i+'-' + $pos + '"></p></div>';
                    });

                }
                if($('#filter-' + nameattr).length>0) {
                    var filter = $('#filter-' + nameattr).val();
                    $inputurl += '<input type="hidden" value="'+filter+'" name="' + $menu_settings_key + '[menu][' + $pos + '][customfilter]" id="edit-menu-item-customfilter-' + $pos + '">';
                }

                var $objhtml = '<li class="menu-item menu-item-depth-0 menu-item-' + $type + ' menu-item-edit-active pending" id="menu-item-' + $pos + '" style="display: list-item;">' +
									'<dl class="menu-item-bar">' +
										'<dt class="menu-item-handle">' +
											'<span class="item-title"><span class="menu-item-title">' + $title + '</span></span>' +
											'<span class="item-controls">' +
												//'<span class="item-type">' + $type_text + '</span>' +
												'<span class="item-order hide-if-js"></span>' +
												'<a href="#" title="' + $title + '" id="edit-' + $pos + '" class="item-edit">' + $title + '</a>' +
											'</span>' +
										'</dt>' +
									'</dl>' +
									'<div id="menu-item-settings-' + $pos + '" class="menu-item-settings">' +
										'<p class="description description-thin">' +
											'<label for="edit-menu-item-title-' + $pos + '">Label<br>' +
											'<input type="text" value="' + $title + '" name="' + $menu_settings_key + '[menu][' + $pos + '][title]" class="widefat edit-menu-item-title" id="edit-menu-item-title-' + $pos + '">' +
											'<input type="hidden" value="' + $pos + '" name="' + $menu_settings_key + '[menu][' + $pos + '][pos]" id="menu-pos' + $pos + '" class="menu-pos-ynaa" />' +
									 		'<input type="hidden" value="' + $type + '" name="' + $menu_settings_key + '[menu][' + $pos + '][type]" id="menu-type' + $pos + '" />' +
										//	'<input type="hidden" value="' + $type_text + '" name="' + $menu_settings_key + '[menu][' + $pos + '][type_text]" id="menu-type_text' + $pos + '" />' +
											'<input type="hidden" value="' + $id + '" name="' + $menu_settings_key + '[menu][' + $pos + '][id]" id="menu-id' + $pos + '" class="menu-id-ynaa" />' +
											'<input type="hidden" value="' + $menu_id + '" name="' + $menu_settings_key + '[menu][' + $pos + '][item_id]" id="menu-item-id' + $pos + '" />' +
											'<input type="hidden" value="1" name="' + $menu_settings_key + '[menu][' + $pos + '][status]" id="menu-status' + $pos + '" />' +
                //'<input type="hidden" value="'+$post_date+'" name="'+$menu_settings_key+'[menu]['+$pos+'][post_date]" id="menu-post_date'+$pos+'" />' +
											'</label>' +
										'</p>' +
										$inputurl +
										'<div class="menu-item-actions description-wide submitbox">' +
											'<a href="' + $pos + '" id="delete-' + $pos + '" class="item-delete submitdelete deletion">' + $delete + '</a>' +
										'</div><div style="clear: both"></div>' +
									'</div><!-- .menu-item-settings-->' +
								'</li>';

                // Add a new header and panel
                $($objhtml).appendTo("#menu-to-edit");
                $o.attr('checked', false);

                // Refresh the accordion
               // $("#menu-to-edit").accordion("refresh");
            }
        });
        return false;
    });



    //Teaser Add Elemt
    $('.submit-add-to-teaser').click(function (e) {

        if ($(e.target).parentsUntil('div.inside').children('div.tabs-panel-active') && $(e.target).parentsUntil('div.inside').children('div.tabs-panel-active').length > 0) {
            var obj = ($(e.target).parentsUntil('div.inside').children('div.tabs-panel-active').find('input[type="checkbox"]:checked'));

        }
        else {
            var obj = ($(e.target).parentsUntil('div.inside').find('input[type="checkbox"]:checked'));

        }
        var $type2 = '';
        var $inputurl = '';
        var $type_text = '';



        $.each(obj, function (key, ob) {

            var $o = $(ob);
            var appextra = 0;
            var filter = 0;
            //alert($o.val());
            if (typeof ($o.attr('checked')) != 'undefined') {

                var nameattr = $o.attr('name');
                var $type = $('#type-' + nameattr).val();
                var $linktype = $('#link-type-' + nameattr).val();
                var val = $o.val();
                if($('#app-' + nameattr).length>0) appextra= $('#app-' + nameattr).val();


                $('<li id="replace' + val + '" class="floatli empty_teaser_li"></li>').appendTo("ul.nh-teaser-ul");
                if(false && ($type =='webview' || $type =='favorites' ||  $type =='search' )){
                }
                else if($linktype=='custom'){
                    //console.log($linktype);
                    var data = new Object();
                    data.title = $('#title-' + $o.attr('name')).val();
                    data.img = null;
                    data.type = $type;
                    data.linktype = $linktype;
                    if($('#filter-' + nameattr).length>0) filter= $('#filter-' + nameattr).val();
                    data.filter = filter;
                    //console.log(data);
                    var $objhtml = nh_teaser_elemet(data, $o);
                    $($objhtml).replaceAll("#replace" + $o.val());
                    $o.attr('checked', false);

                }
                else
                {
                    jQuery.ajax({
                        type: "post",
                        url: ajaxurl,
                        dataType: "json",
                        data: {action: "ny_ynaa_teaser_action", tpid: $o.val(), type: $type, appextra:appextra},
                        success: function (data, textStatus, jqXHR) {
                           // console.log(data);
                            if (data.error == 0) {

                                var $objhtml = nh_teaser_elemet(data, $o);
                                $($objhtml).replaceAll("#replace" + $o.val());
                                $o.attr('checked', false);
                            }
                            else {
                                alert(data.error);
                                $('.empty_teaser_li').remove();
                            }

                            /*if(response.type == "success") {
                             //jQuery("#vote_counter").html(response.vote_count)

                             }
                             else {
                             alert("Your vote could not be added")
                             }*/
                        }
                    });
                }

            }
        });

        return false;
    });

    function nh_teaser_elemet(data, $o){
        var $pos = (getItemscount('ul#menu-to-edit.nh-teaser-ul li.teaserli'));
       // console.log($('#teaserli' + $pos).length);
        while ($('#teaserli' + $pos).length > 0) {

            $pos++;

        }
        var $objhtml = '<li id="teaserli' + $pos + '" class="floatli teaserli">' +
            '<div  class="teaserdiv" id="image-div'+$pos+'" style="background-image:url(' + data.img + ');">' +
            '<div class="ttitle">'+data.title + '</div>' +
            '</div>' +
            '<div style="width: 250px;">' +
            '<a href="' + $pos + '" class="dellteaser">' + $delete + '</a> ' +
            '<input type="hidden" value="' + $o.val() + '"  name="' + $teaser_settings_key + '[teaser]['+$pos+'][id]" /> ' +
            '<input type="hidden" value="' + data.type + '"  name="' + $teaser_settings_key + '[teaser]['+$pos+'][type]" /> ';


        if(data.type=='webview' || data.type=='favorites' || data.type=='search' || data.linktype=='custom'){
            $objhtml += '<span style="float: right;"><span id="reset-cat-img-link-cont_'+$pos+'"></span><a href="#" class="upload_image_button" id="upload_image_teaser'+$pos+'" name="nh_ynaa_categories_settings_items_' + $pos + '_img">Set image</a><input type="hidden" value="" id="nh_ynaa_categories_settings_items_' + $pos + '_img" name="' + $teaser_settings_key + '[teaser]['+$pos+'][img]" data-id="image-div'+$pos+'" data-link="'+$pos+'"></span>';
            $objhtml += '<div>'+
                    '<input data-titleid="#image-div'+$pos+'" type="text" class="teaser_title_input" value="'+ $('#title-' + $o.attr('name')).val()+'" name="' + $teaser_settings_key + '[teaser]['+$pos+'][title]" placeholder="Enter title here" >';
            if(data.type=='webview')
                $objhtml +=   '<input type="url" value="http://" name="' + $teaser_settings_key + '[teaser]['+$pos+'][url]" placeholder="Enter link URL here" style="width:100%">';
            if(data.filter)
                $objhtml +=   '<input type="hidden" value="'+data.filter+'" name="' + $teaser_settings_key + '[teaser]['+$pos+'][customfilter]">';

            $objhtml +=   '</div>';
        }
        $objhtml +=  '</div></li>';
        return $objhtml;
    }

    //Element aus Teaser entfernen
    $('.dellteaser').live('click', function (e) {
        //  alert($(this).attr('href'));
        //  return false;
        if (confirm("Wollen Sie diesen Teaser entfernen?")) {
            $('li#teaserli' + ($(this).attr('href'))).remove();

        }
        return false;
    });

    /*
    * Teaser source
    */
    $('#teaser_source').change(function () {
        if ($(this).val() == 'indi') {
            $('.teaser_categories').hide();
            $('.teaser_limit').hide();
            $('#nav-menus-frame').show();

        }
        else {
            if ($(this).val() == 'cat') {
                $('.teaser_categories').show();
            }
            else {
                $('.teaser_categories').hide();
            }
            $('.teaser_limit').show();
            $('#nav-menus-frame').hide();
        }
    });

    $('.teaser_title_input').live('keyup', function(){
        $($(this).data('titleid')+' > .ttitle').text($(this).val());
    });


    //Element aus Homepreset entfernen
    $('.delhp').live('click', function (e) {
        //  alert($(this).attr('href'));
        //  return false;
        if (confirm("Wollen Sie diesen Element entfernen?")) {
            //$('li#homepresetli'+($(this).attr('href'))).next('.empty_li_clear').remove();
            $('li#homepresetli' + ($(this).attr('href'))).remove();
            //if(parseInt($('.floatli').length)% 2 != 0 ) $('.empty_li_clear:last' ).remove();
        }
        return false;
    });

    //Element aus Menu entfernen
    $('.item-delete').live('click', function (e) {
        //  alert($(this).attr('href'));
        //  return false;
        if (confirm("Wollen Sie diesen Eintrag entfernen?")) {
            $('li#menu-item-' + ($(this).attr('href'))).remove();
            //$("#menu-to-edit").accordion("refresh");
        }
        return false;
    });

    //Alle Elemente aus Menü enfernen
    $('.item-delete-all').live('click', function (e) {
      if (confirm("ACHTUNG wirklich alle Einträge entfernen?")) {
          $("#menu-to-edit li").remove();
       /* $("#menu-to-edit li").each(function(i)
        {
          $('li#menu-item-' + (i+1) ).remove();
        });
        */
      }
    });

    //Element im Menu deaktivieren
    $('.item-deactiv').live('click', function (e) {
        //  alert($(this).attr('href'));
        //  return false;
        if (confirm("Wollen Sie diesen Men&uuml;punkt deaktivieren?")) {
            $('li#menu-item-' + ($(this).attr('href'))).remove();
            $("#menu-to-edit").accordion("refresh");
        }
        return false;
    });

    //Change Homepreset Title
    $('.hptitle').live('keyup', function (e) {
        var tempid = '#' + $(this).attr('id') + 'div';
        $(tempid).text($(this).val());

    });


    //Add Item to homepreset
    $('.submit-add-to-homepreset').click(function (e) {

        if ($(e.target).parentsUntil('div.inside').children('div.tabs-panel-active') && $(e.target).parentsUntil('div.inside').children('div.tabs-panel-active').length > 0) {
            var obj = ($(e.target).parentsUntil('div.inside').children('div.tabs-panel-active').find('input[type="checkbox"]:checked'));
        }
        else {
            var obj = ($(e.target).parentsUntil('div.inside').find('input[type="checkbox"]:checked'));
        }

        $.each(obj, function (key, ob) {
            var $o = $(ob);
            if (typeof ($o.attr('checked')) != 'undefined') {
                console.log('checked');
                $('<li class="floatli empty_homepreset_li"></li>').appendTo("ul.nh-homepreset-ul");
                var nameattr = $o.attr('name');
                var $type = $('#type-' + nameattr).val();
                var $link_type = $('#link-type-' + nameattr).val();
                console.log($link_type);
                var $pos = getMaxHomepresetID();
                console.log($pos);
                if ($link_type == 'cat') {
                    var $title = $('#title-' + nameattr).val();
                    var $oval = $o.val();
                    var $urldiv = '';
                    if($('#extrafield-' + nameattr).length>0) {
                        var extrafields = JSON.parse($('#extrafield-' + nameattr).val());
                        $.each(extrafields, function(i, item) {
                           // $urldiv += '<div style="clear:left; margin-top:10px;"><p><label for="edit-menu-item-'+i+'-' + $pos + '">'+item.label+'<br><input type="'+item.type+'" value="'+item.value+'" name="' + $menu_settings_key + '[menu][' + $pos + ']['+i+']" class="widefat edit-menu-item-url" id="edit-menu-item-'+i+'-' + $pos + '"></p></div>';
                            $urldiv += '<div class="h30"><input type="'+item.type+'" value="'+item.value+'" id="' + $homepreset_settings_key + '_items_' + $pos + '_'+i+'" name="' + $homepreset_settings_key + '[items][' + $pos + ']['+i+']" data-id="hpdiv' + $pos + '" /></div>';
                        });

                    }

                    /*if ($type == 'webview') {
                        $oval = ($o.val()) - (100 + $pos);
                        $urldiv += '<div class="h30"><input type="text" value="http://" id="' + $homepreset_settings_key + '_items_' + $pos + '_url" name="' + $homepreset_settings_key + '[items][' + $pos + '][url]" data-id="hpdiv' + $pos + '" /></div>';
                    }*/

                    $objhtml = '<li id="homepresetli' + $pos + '" class="floatli">' +
										'<div class="hpdiv" id="hpdiv' + $pos + '" style="background-color:' + $color01 + ';">' +
											'<div class="ttitle" id="hptitle' + $pos + 'div">' + $title + '</div>' +
											'<div class="setdefaultcatpic" style="display:none;"><a id="upload_image_button" class="upload_image_button" href="#" name="' + $homepreset_settings_key + '_items_' + $pos + '_img">' + $catText + '</a></div>' +
										'</div>' +
									   '<div><input type="text" value="' + $title + '" id="hptitle' + $pos + '" name="' + $homepreset_settings_key + '[items][' + $pos + '][title]" class="hptitle" /></div>' +
                     '<div><label  class="tooltip " title="'+$hideTitleStartscreenTT+'"  for="hphide' + $pos + '">'+$hideTitleStartscreen+' </label><input id="hphide' + $pos + '" name="' + $homepreset_settings_key + '[items][' + $pos + '][hidetitle]" type="checkbox" style="width: 16px;"></div>' +
									   $urldiv;
                    //$objhtml +=   '<div><input type="checkbox" checked="checked" name="'+$homepreset_settings_key+'[items]['+$pos+'][allowRemove]" id="allowRemove'+$pos+'" value="1" /><label for="allowRemove'+$pos+'"> '+$allowremoveText+'</label></div>';
                    $objhtml += '<div>' +
											'<a href="' + $pos + '" class="delhp">' + $delete + '</a>' +
											'<input type="hidden" value="' + $o.val() + '"  name="' + $homepreset_settings_key + '[items][' + $pos + '][id]" />' +
											'<input type="hidden" value="' + $type + '"  name="' + $homepreset_settings_key + '[items][' + $pos + '][type]" />' +
											'<input type="hidden" value="" id="' + $homepreset_settings_key + '_items_' + $pos + '_img" name="' + $homepreset_settings_key + '[items][' + $pos + '][img]" data-id="hpdiv' + $pos + '" />' +
											'<input type="hidden" value="' + $pos + '" name="' + $homepreset_settings_key + '[items][' + $pos + '][id2]" id="menu-id' + $pos + '" class="homepreset-id-ynaa" />' +
										'</div>' +
									'</li><!--End Hompreset-item -->';
                    //alert(data.title);
                    $($objhtml).replaceAll(".empty_homepreset_li");
                    //if(parseInt($('.floatli').length)% 2 == 0 ) $('<li class="empty_li_clear"></li>' ).appendTo( "ul.nh-homepreset-ul" );
                    $o.attr('checked', false);

                }
                else if($link_type == 'custom') {
                    var $title = $('#title-' + nameattr).val();
                    var $oval = $o.val();
                    var filter = $('#filter-' + nameattr).val();


                    $objhtml = '<li id="homepresetli' + $pos + '" class="floatli">' +
                        '<div class="hpdiv" id="hpdiv' + $pos + '" style="background-color:' + $color01 + ';">' +
                        '<div class="ttitle" id="hptitle' + $pos + 'div">' + $title + '</div>' +
                        '<div class="setdefaultcatpic" style=""><a id="upload_image_button" class="upload_image_button" href="#" name="' + $homepreset_settings_key + '_items_' + $pos + '_img">' + 'Set default image' + '</a></div>' +
                        '</div>' +
                        '<div><input type="text" value="' + $title + '" id="hptitle' + $pos + '" name="' + $homepreset_settings_key + '[items][' + $pos + '][title]" class="hptitle" /></div>' +
                        '<div><label  class="tooltip " title="'+$hideTitleStartscreenTT+'"  for="hphide' + $pos + '">'+$hideTitleStartscreen+' </label><input id="hphide' + $pos + '" name="' + $homepreset_settings_key + '[items][' + $pos + '][hidetitle]" type="checkbox" style="width: 16px;"></div>' ;
                    $objhtml += '<div>' +
                        '<a href="' + $pos + '" class="delhp">' + $delete + '</a>' +
                        '<input type="hidden" value="' + $o.val() + '"  name="' + $homepreset_settings_key + '[items][' + $pos + '][id]" />' +
                        '<input type="hidden" value="' + $type + '"  name="' + $homepreset_settings_key + '[items][' + $pos + '][type]" />' +
                        '<input type="hidden" value="" id="' + $homepreset_settings_key + '_items_' + $pos + '_img" name="' + $homepreset_settings_key + '[items][' + $pos + '][img]" data-id="hpdiv' + $pos + '" />' +
                        '<input type="hidden" value="' + $pos + '" name="' + $homepreset_settings_key + '[items][' + $pos + '][id2]" id="menu-id' + $pos + '" class="homepreset-id-ynaa" />' +
                        '<input type="hidden" value="'+filter+'" name="' + $homepreset_settings_key + '[items][' + $pos + '][customfilter]" id="custom-id' + $pos + '" class="homepreset-id-ynaa" />' +
                        '</div>' +
                        '</li><!--End Hompreset-item -->';
                    //alert(data.title);


                    $($objhtml).replaceAll(".empty_homepreset_li");
                    $o.attr('checked', false);

                }
                else {
                    //alert($o.val());
                    jQuery.ajax({
                        type: "post",
                        url: ajaxurl,
                        dataType: "json",
                        async: false,
                        data: { action: "ny_ynaa_teaser_action", tpid: $o.val() },
                        success: function (data, textStatus, jqXHR) {
                            //console.log(data);
                            if (data.error == 0) {
                                $objhtml = '<li id="homepresetli' + $pos + '" class="floatli">' +
											'<div class="hpdiv" id="hpdiv' + $pos + '" style="background-image:url(' + data.img + ');">' +
												'<div class="ttitle" id="hptitle' + $pos + 'div">' + data.title + '</div>' +

											'</div>' +
										   '<div><input type="text" value="' + data.title + '" id="hptitle' + $pos + '" name="' + $homepreset_settings_key + '[items][' + $pos + '][title]" class="hptitle" /></div>' +
                       '<div><label  class="tooltip " title="'+$hideTitleStartscreenTT+'"  for="hphide' + $pos + '">'+$hideTitleStartscreen+' </label><input id="hphide' + $pos + '" name="' + $homepreset_settings_key + '[items][' + $pos + '][hidetitle]" type="checkbox" style="width: 16px;"></div>';
                                //$objhtml +=	   '<div><input type="checkbox" checked="checked" name="'+$homepreset_settings_key+'[items]['+$pos+'][allowRemove]" id="allowRemove'+$pos+'" value="1" /><label for="allowRemove'+$pos+'"> '+$allowremoveText+'</label></div>';
                                $objhtml += '<div>' +
												'<a href="' + $pos + '" class="delhp">' + $delete + '</a>' +
												'<input type="hidden" value="' + $o.val() + '"  name="' + $homepreset_settings_key + '[items][' + $pos + '][id]" />' +
												'<input type="hidden" value="' + $type + '"  name="' + $homepreset_settings_key + '[items][' + $pos + '][type]" />' +

												'<input type="hidden" value="' + $pos + '" name="' + $homepreset_settings_key + '[items][' + $pos + '][id2]" id="menu-id' + $pos + '" class="homepreset-id-ynaa" />' +
											'</div>' +
										'</li><!--End Hompreset-item -->';
                                //alert(data.title);
                                $($objhtml).replaceAll(".empty_homepreset_li");
                                $o.attr('checked', false);
                                if (parseInt($('.floatli').length) % 2 == 0) $('<li class="empty_li_clear"></li>').appendTo("ul.nh-homepreset-ul");
                                //$( $objhtml ).appendTo( "ul.nh-homepreset-ul" );
                            }
                            else {
                                //alert (data.error);
                                $('.empty_homepreset_li').remove();
                            }
                            /*if(response.type == "success") {
                            //jQuery("#vote_counter").html(response.vote_count)

                            }
                            else {
                            alert("Your vote could not be added")
                            }*/
                        }
                    });
                }
            }
        });
        activeTooltip();
        return false;
    });




    //Upload image
    var $send_url_to = '';
    $('a.upload_image_button').live('click', function () {

        formfield = $(this).attr('name');
        $send_url_to = formfield;

        //$(this).closest( "div.image-div" ).css( "background-color", "red" );

        tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');


        return false;
    });
    window.send_to_editor = function (html) {
        imgurl = $('img', html).attr('src');
        if (typeof imgurl === "undefined") {
            imgurl = $(html).attr('src');
        }
        // alert(imgurl);
        $('#' + $send_url_to).val(imgurl);

        var tempid = '#' + $('#' + $send_url_to).attr('data-id');
        $(tempid).css('background-image', 'url("' + imgurl + '")');
        $(tempid).css('background-color', '');

        if ($('#' + $send_url_to).attr('data-link')) {

            var $id = $('#' + $send_url_to).attr('data-link');
            //alert($id);
            //alert($('#'+$send_url_to).attr('id'));
            $('#reset-cat-img-link-cont_' + $id).html('<a href="' + $id + '" class="reset-cat-img-link">Reset image</a>');
            $('#upload_image_teaser'+$id).hide();
        }
        tb_remove();
    }

    /* RESET IMAGE */
    $('.reset-cat-img-link').live('click', function () {
        if (confirm('Do you want reset the image?')) {
            var $id = $(this).attr('href');
            $('#image-div' + $id).css('background-image', "url('')");
            $('#nh_ynaa_categories_settings_items_' + $id + '_img').val('');
            $('#reset-cat-img-link-cont_' + $id + ' a').remove();
            $('#upload_image_teaser'+$id).show();
            return false;
        }
        return false;
    });

    $('.hidecat').change(function(){

        if($(this).val() == 1) {
            $('.' + $(this).data('class')).prop('disabled', true);
        }
        else {
            $('.' + $(this).data('class')).prop('disabled', false);
        }

    });






    $('div.tabs-panel p.quick-search-wrap input.quick-search').bind('keypress', function (e) {
        $this = $(this);
        //$this.next('span.spinner').show();
        if (e.keyCode == 13 && $this.val() != '') {

            var $type = $this.next('input.search-post-type').val();
            var $menu_id = $('#menu_id_counter').val();

            $.ajax({
                //url: "/wp-content/plugins/nh_ynaa/include/load.php",

                url: ajaxurl,
                data: { action: 'nh_search_action', s: $this.val(), pt: $type, mid: $menu_id },
                type: 'POST',
                beforeSend: function (xhr) {

                    $this.next('span.spinner').show();


                }
            })
			.done(function (response) {
			    $this.parent().next('ul').html(response);
			    $this.next('span.spinner').hide();
			    //$('#'+$this.attr('data-type')+' ul').html(data);
			    /*if ( console && console.log ) {
			    console.log( "Sample of data:", data.slice( 0, 100 ) );
			    }*/
			});
            return false;
        }

    });

    /* active autopush */
    if ($('#id_appkey').length > 0) {

        $('#id_appkey, #id_pushsecret').change(function () {
            if ($('#id_appkey').val() != '' && $('#id_pushsecret').val() != '') {

                $("input[name='nh_ynaa_push_settings[autopush]']").prop('checked', true);
            }
        });
    }


    if ($('.showoverviewposts').length > 0) {
        $('.showoverviewposts').change(function () {
            //alert($(this).val());
            var catid = $(this).attr('data-catid');
            //alert(catid);
            if ($(this).val() == '1') {

                $('#showoverviewposts' + catid).show();
            }
            else {
                $('#showoverviewposts' + catid).hide();
            }

        });
    }

    /* deactive post order if startvie = category */

    $('#nh_homescreentype').change(function () {

        if ($(this).val() == 3) {

            $('.nh_sorttype-tr').hide();
            $('#nav-menus-frame').hide();
            $('#nh_posttype-tr').hide();
        }
        else if ($(this).val() != 1 && $(this).val() != 2) {
            //alert('disable=false setzen');
            //$('#nh_sorttype').prop('disabled', false);
            $('.nh_sorttype-tr').hide();
            $('#nav-menus-frame').show();
            $('#nh_posttype-tr').hide();
        }
        else {
            //alert('disable=true setzen');
            //$('#nh_sorttype').removeAttr('disabled');
            //$('#nh_sorttype').prop('disabled', true);
            $('.nh_sorttype-tr').show();
            $('#nav-menus-frame').hide();

            if ($(this).val() == 1) $('#nh_posttype-tr').show();
            else $('#nh_posttype-tr').hide();
        }
    });

    //Show Post on category overvie page
    //if($('.showoverviewposts').length>0){

    //}

    if ($('#ynaa_nav_tab').length > 0) {


        var $ynaa_nav_tab = $('#ynaa_nav_tab');

        var eTop = $ynaa_nav_tab.offset().top; //get the offset top of the element
        nh_tab_nav_fix(eTop);
        // log(eTop - $(window).scrollTop()); //position of the ele w.r.t window
        $(window).scroll(function (event) {
            //var position = $('h2.nav-tab-wrapper').position();
            //$('#placeholder').html(eTop - $(window).scrollTop());
            nh_tab_nav_fix(eTop - $(window).scrollTop());

        });
    }


    /* Preview App container */
    /*if ($('#previewapp_help').length > 0) {
        $('#previewapp_help h3').click(function () {
            $('.previewapp_content').slideToggle();
            $('#previewapp_help h3 span').toggleClass('dashicons-arrow-down-alt2').toggleClass('dashicons-arrow-up-alt2');
        });
    }*/

    $("#accordion").accordion({
        collapsible: true,
        heightStyle: "content"
    });

    $('.contextual-help-link-a').click(function () {

        $('#contextual-help-link').trigger('click');
        return false;
    });




});


function nh_tab_nav_fix(eTop){
	var $ynaa_nav_tab = jQuery('#ynaa_nav_tab');

	if(eTop<30){
		  if(!$ynaa_nav_tab.hasClass('fixed')){
			$ynaa_nav_tab.addClass('fixed');
		  }
	  }
	  else if($ynaa_nav_tab.hasClass('fixed')){
		  $ynaa_nav_tab.removeClass('fixed');
	  }

}

function getMaxMenuID(){
	var maxid=-1;
	if(jQuery('.menu-id-ynaa')){
		jQuery.each( jQuery('.menu-id-ynaa'), function( key, ob ){
			//console.log(jQuery(ob).val());
			if(maxid < parseInt(jQuery(ob).val()))
				maxid=jQuery(ob).val();

		});
	}
	maxid++;

	if(maxid<11)maxid=11;
	//alert(maxid);
	return maxid;
}
function getMaxHomepresetID(){
	var maxid=0;
	if(jQuery('.homepreset-id-ynaa')){
		jQuery.each( jQuery('.homepreset-id-ynaa'), function( key, ob ){

			if(maxid < parseInt(jQuery(ob).val()))
				maxid=jQuery(ob).val();

		});
	}
	maxid++;

	return maxid;

}

function getItemscount(e){
    var pos = 0;

	if(jQuery(e).length>0) {
      //  console.log(pos);
        jQuery.each( jQuery(e), function( key, ob ){
            var tempid = parseInt((jQuery(ob).attr('id')).match(/\d+/)[0]);
            if(pos < tempid)
                pos=tempid

        });

        //pos = jQuery(e).length;
    }
    //console.log('getI'+pos);
    pos++;

	return (pos);
}

//Uplud imGE function
function uploadcatimg(){

}
