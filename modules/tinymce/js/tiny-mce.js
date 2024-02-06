(function() {    

    tinymce.PluginManager.add('saswp_tinymce_dropdown', function( editor, url ) {
        editor.addButton( 'saswp_tinymce_dropdown', { 
            title: 'SASWP Schema Block',           
            icon: 'dashicon dashicons-media-code',
            type: 'menubutton',
            menu: [
                
                //Multi FAQ starts here
                {
                    text: 'FAQ Schema',                    
                    onclick: function() {
                        editor.windowManager.open({
                            title: 'SASWP Featured Snippet FAQ',
                            minWidth: 500,
                            height: 500,
                            autoScroll: true,
                            body:[
                                {
                                    type: 'checkbox',
                                    name: 'saswp_multi_faq_render_html',
                                    label: 'Render HTML',
                                    checked: true,
                                },
                               {
                                type: 'container',
                                name: 'container',
                                label: '',
                                html: ` 
                                        <form id="saswp-faq-start-point" class="saswp-tiny-form">
                                        <div id="saswp-fields">
                                            <fieldset id="saswp-fieldset-0" data-key="0">
                                                <hr class="saswp-hr">
                                                <div>
                                                    <label>Title Tag</label>
                                                    <select name="saswp_faq_headline" id="saswp_faq_headline-0">
                                                        <option value="h2">h2</option>
                                                        <option value="h3">h3</option>
                                                        <option value="h4">h4</option>
                                                        <option value="h5">h5</option>
                                                        <option value="h6">h6</option>
                                                        <option value="p">p</option>
                                                    </select>
                                                </div>
                                                <div class="saswp-faq-font-wrapper">
                                                    <label>Title Font Size</label>
                                                    <input type="text" id="saswp_faq_headline_font_size-0" name="saswp_faq_headline_font_size" class="saswp_faq_font_elements">
                                                    <select name="saswp_faq_headline_font_unit" id="saswp_faq_headline_font_unit-0" class="saswp_faq_font_elements">
                                                        <option value="px">px</option>
                                                        <option value="pt">pt</option>
                                                        <option value="%">%</option>
                                                        <option value="em">em</option>
                                                    </select>
                                                </div>
                                                <div>
                                                    <label>Question</label>
                                                    <input type="text" id="saswp_faq_question-0" name="saswp_faq_question" placeholder="Enter Your Question here...">
                                                </div>
                                                <div>
                                                    <label>Answer</label>
                                                    <textarea id="saswp_faq_answer-0" rows="5" name="saswp_faq_answer" placeholder="Enter your answer here..."></textarea>
                                                </div>
                                                <div>
                                                    <div type="text" id="saswp_faq_img_id-0" name="saswp_faq_img_id" class="saswp_tiny_img_container"></div>
                                                    <div class="mce-btn">
                                                        <button type="button" class="saswp-tiny-select_image" data-target="saswp_faq_img_id-0">Add Image</button>
                                                    </div>
                                                </div> 
                                            </fieldset>
                                        </div>
                                        <div class="mce-btn long">
                                            <button id="saswp-add-more-tiny-faq" type="button">Add More FAQ</button>
                                        </div>
                                    </form>`,
                              }
                                                                 
                            ],
                            onsubmit: function(e){
                                                                                                                                        
                                        let shortcode = `[saswp_tiny_multiple_faq `,
                                            fieldsets = jQuery('#saswp-faq-start-point fieldset');
                                      
                                        for (let i = 0; i < fieldsets.length; i++) {
                                          var key = fieldsets[i].dataset.key,
                                              headlineTag = jQuery("#saswp_faq_headline-"+key).val(),
                                              question = jQuery(`#saswp_faq_question-`+key).val(),
                                              answer = jQuery(`#saswp_faq_answer-`+key).val(),
                                              imageID = jQuery(`#saswp_faq_img_id-`+key).html();                                            
                                              fontSize = jQuery(`#saswp_faq_headline_font_size-`+key).val();                                            
                                              fontUnit = jQuery(`#saswp_faq_headline_font_unit-`+key).val();                                            

                                              shortcode += `headline-`+key+`="`+headlineTag+`" question-`+key+`="`+question+`" answer-`+key+`="`+answer+`" image-`+key+`="`+imageID+`" `;
                                              if(jQuery.trim(fontSize).length > 0){
                                                shortcode += ` fontsize-${key}="${fontSize}" fontunit-${key}="${fontUnit}" `;
                                              }
                                        }
                                                                              
                                        shortcode += ` count="`+fieldsets.length+`" html="`+e.data.saswp_multi_faq_render_html+`"]`;
                                                                                                                    
                                        editor.insertContent(shortcode)     
                                        
                            }

                        })
                    }
                },
                //Multi FAQ ends here
                //HowTo starts here
                {
                    text: 'HowTo Schema',                    
                    onclick: function() {
                        editor.windowManager.open({
                            title: 'SASWP Featured Snippet HowTo',
                            minWidth: 500,
                            height: 500,
                            autoScroll: true,
                            body:[
                                {
                                    type: 'checkbox',
                                    name: 'saswp_howto_render_html',
                                    label: 'Render HTML',
                                    checked: true,
                                },
                                {
                                    type: 'textbox',
                                    name: 'saswp_howto_time_days',
                                    label: 'Days',
                                    value: '',
                                    placeholder: 'DD',                                    
                                    
                                  },
                                  {
                                    type: 'textbox',
                                    name: 'saswp_howto_time_hours',
                                    label: 'Hours',
                                    value: '',
                                    placeholder: 'HH',                                    
                                    
                                  },
                                  {
                                    type: 'textbox',
                                    name: 'saswp_howto_time_minutes',
                                    label: 'Minutes',
                                    value: '',
                                    placeholder: 'MM',                                    
                                    
                                  },
                                  {
                                    type: 'textbox',
                                    name: 'saswp_howto_estimate_cost_currency',
                                    label: 'Estimate Cost Currency',
                                    value: '',
                                    placeholder: 'USD',                                                                        
                                  },
                                  {
                                    type: 'textbox',
                                    name: 'saswp_howto_estimate_cost',
                                    label: 'Estimate Cost',
                                    value: '',
                                    placeholder: '20',                                                                        
                                  },
                                  {
                                    type: 'textbox',
                                    name: 'saswp_howto_description',
                                    label: 'Description',
                                    value: '',
                                    placeholder: 'Enter your description here...',
                                    multiline: true,
                                    minHeight: 100,
                                  },
                               {
                                type: 'container',
                                name: 'container',
                                label: '',
                                html: ` 
                                        <form id="saswp-howto-start-point" class="saswp-tiny-form">
                                        <div id="saswp-fields">
                                            <fieldset id="saswp-fieldset-0" data-key="0">
                                                <hr class="saswp-hr">
                                                <div>
                                                    <label>Title Tag</label>
                                                    <select name="saswp_howto_headline" id="saswp_howto_headline-0">
                                                        <option value="h2">h2</option>
                                                        <option value="h3">h3</option>
                                                        <option value="h4">h4</option>
                                                        <option value="h5">h5</option>
                                                        <option value="h6">h6</option>
                                                        <option value="p">p</option>
                                                    </select>
                                                </div>
                                                <div>
                                                    <label>Step Title</label>
                                                    <input type="text" id="saswp_howto_step_title-0" name="saswp_howto_step_title" placeholder="Enter a step title...">
                                                </div>
                                                <div>
                                                    <label>Step Description</label>
                                                    <textarea id="saswp_howto_step_description-0" rows="5" name="saswp_howto_step_description" placeholder="Enter a step description..."></textarea>
                                                </div>
                                                <div>
                                                    <div type="text" id="saswp_howto_img_id-0" name="saswp_howto_img_id" class="saswp_tiny_img_container"></div>
                                                    <div class="mce-btn">
                                                        <button type="button" class="saswp-tiny-select_image" data-target="saswp_howto_img_id-0">Add Image</button>
                                                    </div>
                                                </div> 
                                            </fieldset>
                                        </div>
                                        <div class="mce-btn long">
                                            <button id="saswp-add-more-tiny-howto" type="button">Add More Steps</button>
                                        </div>
                                    </form>`,
                              }
                                                                 
                            ],
                            onsubmit: function(e){
                                                                                                                                        
                                        let shortcode = `[saswp_tiny_howto `,
                                            fieldsets = jQuery('#saswp-howto-start-point fieldset');
                                      
                                        for (let i = 0; i < fieldsets.length; i++) {
                                          var key = fieldsets[i].dataset.key,
                                              headlineTag = jQuery("#saswp_howto_headline-"+key).val(),
                                              question = jQuery(`#saswp_howto_step_title-`+key).val(),
                                              answer = jQuery(`#saswp_howto_step_description-`+key).val(),
                                              imageID = jQuery(`#saswp_howto_img_id-`+key).html();                                            

                                          shortcode += `headline-`+key+`="`+headlineTag+`" step_title-`+key+`="`+question+`" step_description-`+key+`="`+answer+`" image-`+key+`="`+imageID+`" `;
                                        }
                                                                              
                                        shortcode += ` count="`+fieldsets.length+`" html="`+e.data.saswp_howto_render_html+`" days="`+e.data.saswp_howto_time_days+`" hours="`+e.data.saswp_howto_time_hours+`" minutes="`+e.data.saswp_howto_time_minutes+`" cost_currency="`+e.data.saswp_howto_estimate_cost_currency+`" cost="`+e.data.saswp_howto_estimate_cost+`" description="`+e.data.saswp_howto_description+`"]`;
                                                                                                                    
                                        editor.insertContent(shortcode)     
                                        
                            }

                        })
                    }
                },
                //HowTo ends here
                // Recipe starts here
                {
                     text: 'Recipe Schema', 
                     onclick: function() {
                        editor.windowManager.open({
                            title: 'SASWP Featured Snippet Recipe',
                            minWidth: 500,
                            height: 500,
                            autoScroll: true,
                            body:[
                                {
                                    type: 'checkbox',
                                    name: 'saswp_recipe_render_html',
                                    label: 'Render HTML',
                                    checked: true,
                                },
                                {
                                    type: 'textbox',
                                    name: 'saswp_recipe_by',
                                    label: 'Recipe By',
                                    value: '',
                                    placeholder: 'Recipe By',                                    
                                    
                                },
                                {
                                    type: 'textbox',
                                    name: 'saswp_recipe_course',
                                    label: 'Course',
                                    value: '',
                                    placeholder: 'Starter',                                    
                                    
                                },
                                {
                                    type: 'textbox',
                                    name: 'saswp_recipe_cusine',
                                    label: 'Cusine',
                                    value: '',
                                    placeholder: 'American',                                    
                                    
                                },
                                {
                                    type: 'textbox',
                                    name: 'saswp_recipe_difficulty',
                                    label: 'Difficulty',
                                    value: '',
                                    placeholder: 'Easy',                                    
                                    
                                },
                                {
                                    type: 'textbox',
                                    name: 'saswp_recipe_servings',
                                    label: 'Servings',
                                    value: '',
                                    placeholder: '30',                                    
                                    
                                },
                                {
                                    type: 'textbox',
                                    name: 'saswp_recipe_ptime',
                                    label: 'Prepration Time (in minutes)',
                                    value: '',
                                    placeholder: '20',                                    
                                    
                                },
                                {
                                    type: 'textbox',
                                    name: 'saswp_recipe_ctime',
                                    label: 'Cooking Time  (in minutes)',
                                    value: '',
                                    placeholder: '20',                                    
                                    
                                },
                                {
                                    type: 'textbox',
                                    name: 'saswp_recipe_calories',
                                    label: 'Calories (kcal)',
                                    value: '',
                                    placeholder: '300',                                    
                                    
                                },
                               {
                                type: 'container',
                                name: 'container',
                                label: '',
                                html: ` <div>
                                            <div type="text" id="saswp_recipe_img_id-0" name="saswp_recipe_img_id" class="saswp_tiny_img_container"></div>
                                            <div class="mce-btn">
                                                <button type="button"  class="saswp-tiny-select_image" data-target="saswp_recipe_img_id-0">Add Image</button>
                                            </div>
                                        </div>
                                        <form id="saswp-recipe-start-point" class="saswp-tiny-form">
                                        <div id="saswp-ingredients-fields">
                                            <hr class="saswp-hr">
                                            <div>
                                                <h1 style="font-size=40px;">Ingredients</h1>
                                                <input type="hidden" value="1" id="saswp-ingredients-count"/>
                                                <ol class="saswp-ingredients-wrapper">
                                                    <li id="saswp-li-wrapper-1" style="padding: 10px; margin: 10px;">
                                                        <input type="text" name="saswp_recipe_ingredients[]" class="saswp_recipe_ingredients saswp-width-70"/>
                                                    </li>
                                                </ol>
                                                <div class="mce-btn">
                                                    <button type="button" id="saswp-recipe-add-ingredient">Add More Ingredients</button>
                                                </div>
                                            </div>
                                        </div>

                                        <div id="saswp-direction-fields">
                                            <hr class="saswp-hr">
                                            <div>
                                                <h1 style="font-size=40px;">Directions</h1>
                                                <input type="hidden" value="1" id="saswp-direction-count"/>
                                                <ol class="saswp-direction-wrapper">
                                                    <li id="saswp-li-direction-wrapper-1" style="padding: 10px; margin: 10px;">
                                                        <input type="text" name="saswp_recipe_directions[]" class="saswp_recipe_directions saswp-width-70"/>
                                                    </li>
                                                </ol>
                                                <div class="mce-btn">
                                                    <button type="button" id="saswp-recipe-add-directions">Add More Direction</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="saswp-notes-fields">
                                            <hr class="saswp-hr">
                                            <div>
                                                <h1 style="font-size=40px;">Notes</h1>
                                                <input type="hidden" value="1" id="saswp-notes-count"/>
                                                <ol class="saswp-notes-wrapper">
                                                    <li id="saswp-li-notes-wrapper-1" style="padding: 10px; margin: 10px;">
                                                        <input type="text" name="saswp_recipe_notes[]" class="saswp_recipe_notes saswp-width-70"/>
                                                    </li>
                                                </ol>
                                                <div class="mce-btn">
                                                    <button type="button" id="saswp-recipe-add-notes">Add More Notes</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>`,
                              }
                                                                 
                            ],
                            onsubmit: function(e){
                                let recipeBy = jQuery.trim(e.data.saswp_recipe_by); 
                                let recipeCourse = jQuery.trim(e.data.saswp_recipe_course);                                                                                                          
                                let recipeCusine = jQuery.trim(e.data.saswp_recipe_cusine);                                                                                                          
                                let recipeDifficulty = jQuery.trim(e.data.saswp_recipe_difficulty);                                                                                                          
                                let recipeServings = jQuery.trim(e.data.saswp_recipe_servings);                                                                                                          
                                let recipePTime = jQuery.trim(e.data.saswp_recipe_ptime);                                                                                                          
                                let recipeCTime = jQuery.trim(e.data.saswp_recipe_ctime);                                                                                                          
                                let recipeCalories = jQuery.trim(e.data.saswp_recipe_calories);   
                                let recipeImageId = jQuery('#saswp_recipe_img_id-0').html();                                                                                                        
                                let shortcode = '[saswp_tiny_recipe ';
                                shortcode += 'recipe_by="'+recipeBy+'" course="'+recipeCourse+'" cusine="'+recipeCusine+'" difficulty="'+recipeDifficulty+'" servings="'+recipeServings+'" prepration_time="'+recipePTime+'" cooking_time="'+recipeCTime+'" calories="'+e.data.saswp_recipe_calories+'" image="'+recipeImageId+'"';
                                let iCount = 0;
                                jQuery('.saswp_recipe_ingredients').each(function(e){
                                    let ingradientName = jQuery.trim(jQuery(this).val());
                                    shortcode += ' ingradient_name-'+iCount+'="'+ingradientName+'"';
                                    iCount++;
                                });

                                let dCount = 0;
                                jQuery('.saswp_recipe_directions').each(function(e){
                                    let direction = jQuery.trim(jQuery(this).val());
                                    shortcode += ' direction_name-'+dCount+'="'+direction+'"';
                                    dCount++;
                                });

                                let nCount = 0;
                                jQuery('.saswp_recipe_notes').each(function(e){
                                    let notes = jQuery.trim(jQuery(this).val());
                                    shortcode += ' notes_name-'+nCount+'="'+notes+'"';
                                    nCount++;
                                }); 
                                shortcode += ' html="'+e.data.saswp_recipe_render_html+'"]';                    
                                editor.insertContent(shortcode)     
                                        
                            }

                        })
                    } 
                }
                // Recipe schema end

            ]
        });
    });
})();

jQuery(document).ready(function($){
    
    //HowTo starts here

    $(document).on('click', '#saswp-add-more-tiny-howto', function(e){
        e.preventDefault();
        
        let id = $('#saswp-fields fieldset').length,
        baseHeight = $(`#saswp-fields #fieldset-${id - 1}`).offsetHeight,
        height = id === 1 ? baseHeight + 30 : baseHeight - 30,
        layoutWrapper = $(
            '.mce-container > .mce-container-body.mce-abs-layout'),
        nextField = $(`#saswp-fields #fieldset-${id - 1}`);

    const template = `
                    <fieldset id="saswp-fieldset-`+id+`" data-key="`+id+`">
                        <hr class="saswp-hr">
                        <div>
                            <label>Title Tag</label>
                            <select name="saswp_howto_headline" id="saswp_howto_headline-`+id+`">
                                <option value="h2">h2</option>
                                <option value="h3">h3</option>
                                <option value="h4">h4</option>
                                <option value="h5">h5</option>
                                <option value="h6">h6</option>
                                <option value="p">p</option>
                            </select>
                        </div>
                        <div>
                            <label>Step Title</label>
                            <input type="text" id="saswp_howto_step_title-`+id+`" name="saswp_howto_step_title" placeholder="Enter Step Title...">
                        </div>
                        <div>
                            <label>Step Description</label>
                            <textarea id="saswp_howto_step_description-`+id+`" rows="5" name="saswp_howto_step_description" placeholder="Enter Step Description..."></textarea>
                        </div>
                        <div>
                            <div type="text" id="saswp_howto_img_id-`+id+`" name="saswp_howto_img_id" class="saswp_tiny_img_container"></div>
                            <div class="mce-btn">
                                <button type="button"  class="saswp-tiny-select_image" data-target="saswp_howto_img_id-`+id+`">Add Image</button>
                            </div>
                        </div>
                        <div class="mce-btn saswp_removelast_btn">
                            <button type="button" class="saswp_removelast" data-target="bild-`+id+`">- Remove</button>
                        </div>
                    </fieldset>
                `;
                
                $("#saswp-fields").append(template);                
    });

    //HowTo ends here

    //Multi FAQ starts here

    $(document).on('click', '#saswp-add-more-tiny-faq', function(e){
        e.preventDefault();
        

        let id = $('#saswp-fields fieldset').length,
        baseHeight = $(`#saswp-fields #fieldset-${id - 1}`).offsetHeight,
        height = id === 1 ? baseHeight + 30 : baseHeight - 30,
        layoutWrapper = $(
            '.mce-container > .mce-container-body.mce-abs-layout'),
        nextField = $(`#saswp-fields #fieldset-${id - 1}`);

    const template = `
                    <fieldset id="saswp-fieldset-`+id+`" data-key="`+id+`">
                        <hr class="saswp-hr">
                        <div>
                            <label>Title Tag</label>
                            <select name="saswp_faq_headline" id="saswp_faq_headline-`+id+`">
                                <option value="h2">h2</option>
                                <option value="h3">h3</option>
                                <option value="h4">h4</option>
                                <option value="h5">h5</option>
                                <option value="h6">h6</option>
                                <option value="p">p</option>
                            </select>
                        </div>
                        <div class="saswp-faq-font-wrapper">
                                <label>Title Font Size</label>
                                <input type="text" id="saswp_faq_headline_font_size-`+id+`" name="saswp_faq_headline_font_size" class="saswp_faq_font_elements">
                                <select name="saswp_faq_headline_font_unit" id="saswp_faq_headline_font_unit-`+id+`" class="saswp_faq_font_elements">
                                    <option value="px">px</option>
                                    <option value="pt">pt</option>
                                    <option value="%">%</option>
                                    <option value="em">em</option>
                                </select>
                            </div>
                        <div>
                            <label>Question</label>
                            <input type="text" id="saswp_faq_question-`+id+`" name="saswp_faq_question" placeholder="Enter Your Question here...">
                        </div>
                        <div>
                            <label>Answer</label>
                            <textarea id="saswp_faq_answer-`+id+`" rows="5" name="saswp_faq_answer" placeholder="Enter your answer here..."></textarea>
                        </div>
                        <div>
                            <div type="text" id="saswp_faq_img_id-`+id+`" name="saswp_faq_img_id" class="saswp_tiny_img_container"></div>
                            <div class="mce-btn">
                                <button type="button"  class="saswp-tiny-select_image" data-target="saswp_faq_img_id-`+id+`">Add Image</button>
                            </div>
                        </div>
                        <div class="mce-btn saswp_removelast_btn">
                            <button type="button" class="saswp_removelast" data-target="bild-`+id+`">- Remove</button>
                        </div>
                    </fieldset>
                `;
                
                $("#saswp-fields").append(template);                
    });

    $(document).on('click','.saswp_removelast', function(e){
        $('.saswp-tiny-form fieldset:last-of-type').remove();
    })

    $(document).on('click', '.saswp-tiny-select_image', function(e){
        e.preventDefault();
        var current = $(this);
        var custom_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Add Image',
            button: {
                text: 'Add Image'
            },
            multiple: false
        });
        custom_uploader.on('select', function() {
            var attachment = custom_uploader.state().get('selection').first().toJSON();
            current.parent().parent().find('.saswp_tiny_img_container').html(attachment.id);           
        });
        custom_uploader.open();

    });

    //Multi FAQ ends here   

    // Add More Recipe Ingredients
    $(document).on('click', '#saswp-recipe-add-ingredient', function(e){
        e.preventDefault();
        let ingredientCount = $('#saswp-ingredients-count').val();
        ingredientCount = parseInt(ingredientCount) + 1;
        let wrapperClass = "saswp-li-wrapper-"+ingredientCount;
        let ingredientInput = '';
        ingredientInput  = '<li id="'+wrapperClass+'" style="padding: 10px; margin: 10px;">';
        ingredientInput  += '<input type="text" name="saswp_recipe_ingredients[]" class="saswp_recipe_ingredients saswp-width-70"/>';
        ingredientInput  += '<span><div class="mce-btn" style="float: right;"><button type="button" class="saswp-remove-ingredient" data-id="'+ingredientCount+'">- Remove</button></div></span>';
        ingredientInput  += '</li>';
        $('.saswp-ingredients-wrapper').append(ingredientInput);
        $('.saswp-ingredients-wrapper').sortable();
        $('#saswp-ingredients-count').val(ingredientCount);
    }); 

    // Remove Added Recipe Ingredients
    $(document).on('click', '.saswp-remove-ingredient', function(e){
        e.preventDefault();
        let dataId = $(this).attr('data-id');
        let wrapperClass = "#saswp-li-wrapper-"+dataId;
        $(wrapperClass).remove();
        $('.saswp-ingredients-wrapper').sortable();
    });

    // Add More Recipe Ingredients
    $(document).on('click', '#saswp-recipe-add-directions', function(e){
        e.preventDefault();
        let directionCount = $('#saswp-direction-count').val();
        directionCount = parseInt(directionCount) + 1;
        let wrapperClass = "saswp-li-direction-wrapper-"+directionCount;
        let directionInput = '';
        directionInput  = '<li id="'+wrapperClass+'" style="padding: 10px; margin: 10px;">';
        directionInput  += '<input type="text" name="saswp_recipe_directions[]" class="saswp_recipe_directions saswp-width-70"/>';
        directionInput  += '<span><div class="mce-btn" style="float: right;"><button type="button" class="saswp-remove-direction" data-id="'+directionCount+'">- Remove</button></div></span>';
        directionInput  += '</li>';
        $('.saswp-direction-wrapper').append(directionInput);
        $('.saswp-direction-wrapper').sortable();
        $('#saswp-direction-count').val(directionCount);
    });

    // Remove Added Recipe Directions
    $(document).on('click', '.saswp-remove-direction', function(e){
        e.preventDefault();
        let dataId = $(this).attr('data-id');
        let wrapperClass = "#saswp-li-direction-wrapper-"+dataId;
        $(wrapperClass).remove();
        $('.saswp-direction-wrapper').sortable();
    });

    // Add More Recipe Notes
    $(document).on('click', '#saswp-recipe-add-notes', function(e){
        e.preventDefault();
        let notesCount = $('#saswp-direction-count').val();
        notesCount = parseInt(notesCount) + 1;
        let wrapperClass = "saswp-li-notes-wrapper-"+notesCount;
        let notesInput = '';
        notesInput  = '<li id="'+wrapperClass+'" style="padding: 10px; margin: 10px;">';
        notesInput  += '<input type="text" name="saswp_recipe_notes[]" class="saswp_recipe_notes saswp-width-70"/>';
        notesInput  += '<span><div class="mce-btn" style="float: right;"><button type="button" class="saswp-remove-notes" data-id="'+notesCount+'">- Remove</button></div></span>';
        notesInput  += '</li>';
        $('.saswp-notes-wrapper').append(notesInput);
        $('.saswp-notes-wrapper').sortable();
        $('#saswp-notes-count').val(notesCount);
    });

    // Remove Added Recipe Directions
    $(document).on('click', '.saswp-remove-notes', function(e){
        e.preventDefault();
        let dataId = $(this).attr('data-id');
        let wrapperClass = "#saswp-li-notes-wrapper-"+dataId;
        $(wrapperClass).remove();
        $('.saswp-notes-wrapper').sortable();
    });
    
});