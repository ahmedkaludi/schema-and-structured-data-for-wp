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

                                          shortcode += `headline-`+key+`="`+headlineTag+`" question-`+key+`="`+question+`" answer-`+key+`="`+answer+`" image-`+key+`="`+imageID+`" `;
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
                }
                //HowTo ends here

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
    
});