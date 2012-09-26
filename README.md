wp_webf_shortcode
=================

webf shortcode for wordpress

place webf_shortcode.php in

WP_INSTALL_DIR/shortcodes

add the following code

require_once( ABSPATH . "/shortcodes/webf_shortcode.php" );

at the end of

WP_INSTALL_DIR/wp-includes/shortcodes.php

usage:

[webf
   rep="http://archive.portablehtml.com/dust-rep"
   id="create a new div to where put the webf" || ref="put inside this element"
   class="fb-timeline" <-- which webf to use
   tpl="fb-dust-templates" <-- comma separated list of templates
   data="" || data_ref="" <-- where data is getted
]content[/webf]

---------------------------------------
rep     is the webf repository
id      say to create a div where to put the webf
ref     is an element where to put webf
class   is means which template to use (if more than one is present)
tpl     is the repository files for the webf
data, data_ref and content
        control how data is generated
-----------------------------------------

if data_ref is used data_ref is an url to a script like:
            var dataOf"id"={
                hasMoreData: 1, // is data available

                getData: function(name){ // depends on webf
                    switch (name){
                        "d1": return {...}
                    }
                },
                loadData: function(webf){
                    // here load the data
                    // if asynchronous use
                    ajaxCall({
                        onDataAvailable(){ webf.onLoadData(); }
                    });
                    return 1;
                    // end asynchronous
                }
            };

if content is used put
                hasMoreData: 1, // is data available

                getData: function(name){ // depends on webf
                    switch (name){
                        "d1": return {...}
                    }
                },
                loadData: function(webf){
                    // here load the data
                    // if asynchronous use
                    ajaxCall({
                        onDataAvailable(){ webf.onLoadData(); }
                    });
                    return 1;
                    // end asynchronous
                }

if data is used put
                hasMoreData: 1,

                getData: function(name){
                    switch (name){    // vvvvvvvvvvvvvvvvv this
                        "d1": return    {...              }
                                     //  ^^^^^^^^^^^^^^^^^ this
                    }
                },
                loadData: function(webf){...}


Examples

[webf id="timeline" class="fb-timeline" tpl="fb-dust-templates"]
var events =
            [
                {
                   type: "headerFilters",
                    data: { id: "septFilters", title: "September 2012",
  				    items: [{ 	name: "Early", checked:1	}, { name: "Mid"}, { name:"Late" }]  }
                }
           ];

var toc={
    data: [
            { name: "Now",  selected:1 },
            { name: "2012",  items: [
                { name: "September" },
                { name: "August" },
                { name: "May" },
                { name: "April" },
                { name: "March" },
                { name: "February" },
                { name: "January" },
            ] }
        ],
        actions: [ {
            title: "Create a page",
            handler: function(){ alert("new Page"); }
    }]
};

var dataOftimeline = {
    hasMoreData: 1,

    getData: function(name){
        switch (name){
            case "events": return events;
            case "toc": return toc;
        }
    },

    loadData: function(){ this.hasMoreData--; }
};
[/webf]

[webf id="timeline"
      class="fb-timeline"
      tpl="fb-dust-templates"
      data_ref="http://my-data-script.js"]


[webf id="timeline"
      tpl="fb-dust-templates"
      data_ref="http://my-data-script.js"]
only one class is present

[webf id="timeline"
      class="twitter"
      tpl="fb-dust-templates,twitter-dust"
      data_ref="http://my-data-script.js"]
two class are registered

[webf tpl="fb-dust-templates"
      data_ref="http://my-data-script.js"]
id is optional but only one webf

