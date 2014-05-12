<?php
$this->layout = '//layouts/main';

$clientScript = Yii::app()->clientScript;

$clientScript->registerPackage('qunit');

$clientScript->registerScriptFile('/js/libs/sinon-1.9.0.js');

$clientScript->registerPackage('aes-common');
$clientScript->registerPackage('marionette');
$clientScript->registerScriptFile('/js/libs/aes/i18n.js');
$clientScript->registerScriptFile('/js/libs/bootstrap.button.js', CClientScript::POS_END);
$clientScript->registerScript('resolveBtnConflict', 
   '$(function(){ var btn = $.fn.button.noConflict();
    $.fn.bButton = btn; });', CClientScript::POS_END);


$clientScript->registerScriptFile('/js/libs/bootstrap.tooltip.js', CClientScript::POS_END);
$clientScript->registerScript('resolveTooltipConflict', 
   '$(function(){
       var bTooltip = $.fn.tooltip;
       
       $.fn.tooltip.noConflict();
       $.fn.jqTooltip = $.fn.tooltip;
       
       $.fn.tooltip = bTooltip;
    });', CClientScript::POS_END); 


$this->createWidget('RatesMarionetteWidget')->register();
?>
<style>
    .qout {
        padding: 20px;
        border: 1px solid #929292;
        background-color: #fafafa;
        border-radius: 5px;
    }
</style>

<div id="qunit"></div>
<div id="qunit-fixture"></div>
<div id="qunit-output"></div>
<div id="qunit-fixtures" style="display: none;">

</div>
<script type="text/javascript">
    
    var outAfterQunitBlock = true;
    
    var prepareFixture = function() {
        
        var fixtureForTest = $('.qfix-' + QUnit.config.current.testNumber);
            
        if(fixtureForTest.length)
            $('#qunit-fixture').append(fixtureForTest.html());
    };

    var passOutput = function() {
        if(outAfterQunitBlock)
            $('#qunit-output').append('<div class="row-fluid qout-' + QUnit.config.current.testNumber + '"><b>Test #' + QUnit.config.current.testNumber + ' output</b>:<br></div>');
        else
            $('#qunit-test-output' + (QUnit.config.current.testNumber - 1)).append('<div>Output:</div><div class="qout qout-' + QUnit.config.current.testNumber + '"></div>');
        
        $('#qunit-fixture').children().each(function(index, el) {
            $(el).appendTo($('.qout-' + QUnit.config.current.testNumber));
        });    
    };
    
    module('RatesWidget tests', {
        setup: function() {
            prepareFixture();
        },
        
        teardown: function() {
            passOutput();
        }
    });
    
    test('Rates collection is equal for all RateViews with same target', function(){
        var widgetConf = {
            targetId: 1,
            targetType: 'Item'
        };

        var rates1 = RatesWidget.create(widgetConf);
        var rates2 = RatesWidget.create(widgetConf);
        
        ok(_.isEqual(rates1.getRatesCollection(), rates2.getRatesCollection()));
    });
    
    test('Rates collection of RateView is not equal to other', function() {
        var rates1 = RatesWidget.create({
            targetId: 1,
            targetType: 'Item'
        });
        
        var rates2 = RatesWidget.create({
            targetId: 2,
            targetType: 'Item'
        });
        
        ok(!_.isEqual(rates1.getRatesCollection(), rates2.getRatesCollection())); 
    });
</script>