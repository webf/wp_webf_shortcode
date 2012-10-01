<?php
/*

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
lib     comma separated library list requred by webf
jquery	include jquery library if not available
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

*/

$webf_loaded_script = array();

function is_script_loaded($script){
    global $webf_loaded_script;
    foreach($webf_loaded_script as $s) {
        if ($s==$script) return true;
    }
    $webf_loaded_script[]=$script;
    return false;
}
function webf_func( $atts, $content=null ) {

    global $webf_matches;

    if (isset($webf_matches[$content]))
        $content=$webf_matches[$content];

    if (!isset($atts["tpl"])){
        if (!isset($atts["class"]))
        return "tpl attribute not set";
        $tpl="template-".$atts["class"];
    } else $tpl=$atts["tpl"];
    if (!isset($atts["class"])){ $cls="null"; } else $cls='"'.$atts["class"].'"';

    if (isset($atts["rep"])) $rep=$atts["rep"]; else $rep='http://archive.portablehtml.com/dust-repo';
    if (isset($atts["ref"])) $ref=$atts["ref"]; else $ref=null;
    if (isset($atts["id"])) $id=$atts["id"]; else $id='webf';
    if (isset($atts["data"])) $data=$atts["data"]; else $data=null;
    if (isset($atts["data_ref"])) $data_ref=$atts["data_ref"]; else $data_ref=null;

    if (is_null($ref)){
        $idRef = $id;
        $idCode = <<<EOQ
<div id="$id"></div>
EOQ;
    } else {
        $idRef = $ref;
        $idCode = "";
    }

    $scriptList = array();
    if (!is_script_loaded("http://akdubya.github.com/dustjs/lib/dust.js"))
        $scriptList[]="http://akdubya.github.com/dustjs/lib/dust.js";
    if (!is_script_loaded("http://akdubya.github.com/dustjs/lib/parser.js"))
        $scriptList[]="http://akdubya.github.com/dustjs/lib/parser.js";
    if (!is_script_loaded("http://akdubya.github.com/dustjs/lib/compiler.js"))
        $scriptList[]="http://akdubya.github.com/dustjs/lib/compiler.js";
    if (isset($atts["jquery"]) && !is_script_loaded("http://akdubya.github.com/dustjs/vendor/jquery.min.js"))
        $scriptList[]="http://akdubya.github.com/dustjs/vendor/jquery.min.js";
    if (!is_script_loaded("$rep/dust-manager.js"))
        $scriptList[]="$rep/dust-manager.js";
    if (isset($atts["lib"])){
        $parts = preg_split("/[,]/", $atts["lib"]);
        foreach ($parts as $part) {
            if (!is_script_loaded($part)){
                $scriptList[]=$part;
            }

        }
    }
    $parts = preg_split("/[,]/", $tpl);
    $repList = '';
    foreach ($parts as $part) {
        if (!is_script_loaded("$rep/$part.js")){
            $scriptList[]="$rep/$part.js";
        }
    }



    if (!is_null($data_ref)){
        $dataRef = <<<EOQ
<script src="$data_ref.js" type="text/javascript"></script>
EOQ;
    } else if (is_null($data)) $data=<<<EOQ
    hasMoreData: 1,

    getData: function(name){

        return { $content };
    },
    loadData: function(){ this.hasMoreData--; }
EOQ;


    if (!is_null($data)){
        $dataCode="{ $data }";
    } else if (is_null($dataRef)){
        return <<<EOQ
Data loader not found! usage:
        [webf data="your data"] or
        [webf dataRef="your script"] or
            var dataOf"id"={
                hasMoreData: 1,

                getData: function(name){
                    switch (name){
                        "d1": return {...}
                    }
                },
                loadData: function(){}
            };
        [webf]your data[/webf]
                hasMoreData: 1,

                getData: function(name){
                    switch (name){
                        "d1": return {...}
                    }
                },
                loadData: function(webf){}
EOQ;
    } else {
        $dataCode="dataOf$id";
    }

    foreach ($scriptList as $part) {
        $repList .= <<<EOQ
<script src="$part" type="text/javascript"></script>
EOQ;
    }

    return <<<EOQ
$repList
$dataRef

<script type="text/javascript">// <![CDATA[
    (function () {
        var webf=webFMan.webF("#$idRef", $cls);
        webf.init( $dataCode );
    })();
// ]]></script>
$idCode
EOQ;
};

add_shortcode( 'webf', 'webf_func' );

add_filter('the_content', 'webf_before_format', 0);

$webf_matches = array();
$webf_id = 0;

function holdWebf($match){
    global $webf_matches, $webf_id;
    $key = "webf_".($webf_id++);
    $webf_matches[$key] = $match[2];

    return $match[1].$key.$match[3];
}

function webf_before_format($content){
    return preg_replace_callback( "/(\[webf[^]]+?\])(.*?)(\[\/webf\])/siu", "holdWebf", $content ); }

?>
