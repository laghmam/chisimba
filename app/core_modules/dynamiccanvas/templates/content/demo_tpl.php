<?php
ob_start();
$objFix = $this->getObject('cssfixlength', 'htmlelements');
$objFix->fixThree();
?>

<div id="threecolumn">
    <div id="Canvas_Content_Body_Region1">
        {
            "display" : "block",
            "module" : "dynamiccanvas",
            "block" : "test1"
        }
        {
            "display" : "block",
            "module" : "canvas",
            "block" : "selecttype"
        }
    </div>
    <div id="Canvas_Content_Body_Region3">
        {
            "display" : "block",
            "module" : "dynamiccanvas",
            "block" : "test2"
        }
        {
            "display" : "block",
            "module" : "security",
            "block" : "login"
        }
        {
            "display" : "block",
            "module" : "blocks",
            "block" : "wrapper"
        }
        {
            "display" : "block",
            "module" : "blocks",
            "block" : "table"
        }
    </div>
    <div id="Canvas_Content_Body_Region2">
        {
            "display" : "block",
            "module" : "canvas",
            "block" : "canvasviewer"
        }
        {
            "display" : "block",
            "module" : "userdetails",
            "block" : "userdetails"
        }
        {
            "display" : "block",
            "module" : "dynamiccanvas",
            "block" : "nonexistentblock"
        }
        {
            "display" : "block",
            "module" : "dynamiccanvas",
            "block" : "thirdtest",
            "showTitle":0
        }
        {
            "display" : "block",
            "module" : "canvas",
            "block" : "canvasviewer"
        }
        {
            "display" : "block",
            "module" : "dynamiccanvas",
            "block" : "thirdtest",
            "showTitle":0
        }
        {
            "display" : "block",
            "module" : "stories",
            "block" : "stories",
            "properties" : [
                {
                    "category" : "postlogin"
                }
            ]
        }
        <div id="fb-root"></div><script src="http://connect.facebook.net/en_US/all.js#appId=108543009171174&amp;xfbml=1"></script><fb:comments numposts="10" width="425" publish_feed="true"></fb:comments>
    </div>
</div>
<?php
// Get the contents for the layout template
$pageContent = ob_get_contents();
ob_end_clean();
$this->setVar('pageContent', $pageContent);
?>