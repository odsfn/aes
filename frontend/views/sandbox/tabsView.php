<?php
$this->layout = '//layouts/main';

$clientScript = Yii::app()->clientScript;

$clientScript->registerPackage('qunit');

$clientScript->registerPackage('aes-common');
$clientScript->registerPackage('backbone.validation');
$clientScript->registerPackage('marionette');
$clientScript->registerScriptFile('/js/libs/aes/i18n.js');
$clientScript->registerScriptFile('/js/libs/jquery.dateFormat-1.0.js');
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

$clientScript->registerScriptFile('/js/libs/aes/views/ItemView.js');
$clientScript->registerScriptFile('/js/libs/aes/views/TabsView.js');
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
    
    module('PopUpView', {
        setup: function() {
            prepareFixture();
        },
        
        teardown: function() {
            passOutput();
        }
    });
    
   test('TabsRenders', function(){
       var tabs = new Aes.TabsView({
           tabs: {
               first: {
                   title: '<b>First</b> tab title',
                   content: 'First tab <b>content</b>'
               },
               second: {
                   title: '<b>Second</b> tab title',
                   content: 'Second tab <b>content</b>'
               }
           }
       });
       
       $('#qunit-fixture').append(tabs.render().el);
       tabs.triggerMethod('show');
       
       ok($('#qunit-fixture .tabs-container').length === 1);
       ok($('#qunit-fixture .tabs-container > ul.nav.nav-tabs').length === 1);
       ok($('#qunit-fixture .tabs-container > ul.nav.nav-tabs > li').length === 2);
       ok($('#qunit-fixture .tabs-container li:first').hasClass('active'));
       ok($('#qunit-fixture .tabs-container li:eq(0) > a[href="#first-tab"]').length === 1);
       ok($('#qunit-fixture .tabs-container li:eq(1) > a[href="#second-tab"]').length === 1);
       ok($('#qunit-fixture .tabs-container > div.tab-content').length === 1);
       ok($('#qunit-fixture div.tab-content > div:eq(0)[id="first-tab"]').length === 1);
       ok($('#qunit-fixture div.tab-content > div:eq(1)[id="second-tab"]').length === 1);
       ok($('#qunit-fixture #first-tab:visible').hasClass('active'));
       
   });
    
    test('Tabs switches', function() {
        var tabs = new Aes.TabsView({
            tabs: {
                first: {
                    title: '<b>First</b> tab title',
                    content: 'First tab <b>content</b>'
                },
                second: {
                    title: '<b>Second</b> tab title',
                    content: 'Second tab <b>content</b>'
                }
            }
        });
        
        $('#qunit-fixture').append(tabs.render().el);
        tabs.triggerMethod('show');
        
        $('#qunit-fixture .tabs-container li:eq(1) > a').click();
        
        ok(!$('#qunit-fixture .tabs-container li:first').hasClass('active'));
        ok($('#qunit-fixture .tabs-container li:eq(1)').hasClass('active'));
        
        ok($('#qunit-fixture #first-tab:visible').length === 0);
        ok($('#qunit-fixture #second-tab:visible').length === 1);
        ok($('#qunit-fixture #second-tab').hasClass('active'));
        
        tabs.select('first');
        ok(!$('#qunit-fixture #second-tab').hasClass('active'));
        ok($('#qunit-fixture #first-tab').hasClass('active'));
        
        tabs.select(tabs.tabViews.findByCustom('second'));
        ok($('#qunit-fixture #second-tab').hasClass('active'));
        ok(!$('#qunit-fixture #first-tab').hasClass('active'));
    });
  
  test('Tabs with nested views', function() {
      var tabs = new Aes.TabsView({
          tabs: {
              first: {
                  title: '<b>First</b> tab title',
                  content: 'First tab <b>content</b>'
              },
              second: {
                  title: '<b>Second</b> tab title',
                  content: new Aes.ItemView({tpl: 'Second tab <b>content</b>'})
              }
          }
      });
      
      $('#qunit-fixture').append(tabs.render().el);
      tabs.triggerMethod('show');
      
      ok($('#qunit-fixture .tabs-container > ul.nav.nav-tabs > li').length === 2);
      ok($('#qunit-fixture .tabs-container li:first').hasClass('active'));
      ok($('#qunit-fixture .tabs-container li:eq(0) > a[href="#first-tab"]').length === 1);
      ok($('#qunit-fixture .tabs-container li:eq(1) > a[href="#second-tab"]').length === 1);
      ok($('#qunit-fixture div.tab-content > div:eq(0)[id="first-tab"]').length === 1);
      ok($('#qunit-fixture div.tab-content > div:eq(1)[id="second-tab"]').length === 1);
      
      ok($('#qunit-fixture #second-tab > div').html() === 'Second tab <b>content</b>');
  });
  
  test('Tabs can be added and removed', function() {
      var tabs = new Aes.TabsView({
          tabs: {
              first: {
                  title: '<b>First</b> tab title',
                  content: 'First tab <b>content</b>'
              },
              second: {
                  title: '<b>Second</b> tab title',
                  content: 'Second tab <b>content</b>'
              }
          }
      });
      
      $('#qunit-fixture').append(tabs.render().el);
      tabs.triggerMethod('show');
      
      tabs.add({
          tabId: 'third',
          title: 'Third',
          content: '<b>Third</b> tab content'
      });
      
      ok($('#qunit-fixture .tabs-container > ul.nav.nav-tabs > li').length === 3);
      
      ok($('#qunit-fixture .tabs-container li:eq(0) > a[href="#first-tab"]').length === 1);
      ok($('#qunit-fixture .tabs-container li:eq(1) > a[href="#second-tab"]').length === 1);
      ok($('#qunit-fixture div.tab-content > div:eq(0)[id="first-tab"]').length === 1);
      ok($('#qunit-fixture div.tab-content > div:eq(1)[id="second-tab"]').length === 1);        
      
      ok($('#qunit-fixture .tabs-container li:eq(2) > a[href="#third-tab"]').html() === 'Third');
      ok($('#qunit-fixture #third-tab').html() === '<b>Third</b> tab content');
  });
  
  test('Tabs can be removed', function() {
        var tabs = new Aes.TabsView({
          tabs: {
              first: {
                  title: '<b>First</b> tab title',
                  content: 'First tab <b>content</b>'
              },
              second: {
                  title: '<b>Second</b> tab title',
                  content: 'Second tab <b>content</b>'
              },
              third: {
                  title: '<b>Third</b> tab title',
                  content: 'Third tab <b>content</b>'
              }
          }
        });

        $('#qunit-fixture').append(tabs.render().el);
        tabs.triggerMethod('show');

        tabs.select('third');

        tabs.removeTab('third');

        ok($('#qunit-fixture .tabs-container .tab-content > div').length === 2);
        ok($('#qunit-fixture .tabs-container li').length === 2);
        ok($('#qunit-fixture .tabs-container li:eq(0) > a[href="#first-tab"]').length === 1);
        ok($('#qunit-fixture div.tab-content > div:eq(0)[id="first-tab"]').length === 1);
        ok($('#qunit-fixture .tabs-container li:eq(1) > a[href="#second-tab"]').length === 1);
        ok($('#qunit-fixture div.tab-content > div:eq(1)[id="second-tab"]').length === 1);

        //checks that first reselected
        ok($('#qunit-fixture .tabs-container li:eq(0)').hasClass('active'));
        ok($('#qunit-fixture .tab-content > div.active').html() === 'First tab <b>content</b>');
      
        //check that we can select other tab
        $('#qunit-fixture .tabs-container li:eq(1) > a').click();
        
        ok(!$('#qunit-fixture .tabs-container li:first').hasClass('active'));
        ok($('#qunit-fixture .tabs-container li:eq(1)').hasClass('active'));
        
        ok($('#qunit-fixture #first-tab:visible').length === 0);
        ok($('#qunit-fixture #second-tab:visible').length === 1);
        ok($('#qunit-fixture #second-tab').hasClass('active'));
        
        tabs.select('first');
        ok(!$('#qunit-fixture #second-tab').hasClass('active'));
        ok($('#qunit-fixture #first-tab').hasClass('active'));
        
        tabs.select(tabs.tabViews.findByCustom('second'));
        ok($('#qunit-fixture #second-tab').hasClass('active'));
        ok(!$('#qunit-fixture #first-tab').hasClass('active'));
  });
  
  test('Closable tab is removed', function() {
      var tabs = new Aes.TabsView({
          tabs: {
              first: {
                  title: '<b>First</b> tab title',
                  content: 'First tab <b>content</b>'
              },
              second: {
                  title: '<b>Second</b> tab title',
                  content: 'Second tab <b>content</b>',
                  closable: true
              }
          }
      });
      
      $('#qunit-fixture').append(tabs.render().el);
      tabs.triggerMethod('show');
      
      ok($('#qunit-fixture .tabs-container li:eq(0) span.icon-remove').length === 0);
      ok($('#qunit-fixture .tabs-container li:eq(1) span.icon-remove').length === 1);
      
      $('#qunit-fixture span.icon-remove').click();
      
      ok($('#qunit-fixture .tabs-container .tab-content > div').length === 1);
      ok($('#qunit-fixture .tabs-container li').length === 1);
      ok($('#qunit-fixture .tabs-container li:eq(0) > a[href="#first-tab"]').length === 1);
      ok($('#qunit-fixture div.tab-content > div:eq(0)[id="first-tab"]').length === 1);
    });
    
    test('Removing first active tab', function() {

        var tabs = new Aes.TabsView({
          tabs: {
              first: {
                  title: '<b>First</b> tab title',
                  content: 'First tab <b>content</b>',
                  closable: true
              },
              second: {
                  title: '<b>Second</b> tab title',
                  content: 'Second tab <b>content</b>',
                  closable: true
              }
          }
        });

        $('#qunit-fixture').append(tabs.render().el);
        tabs.triggerMethod('show');  

        $('#qunit-fixture .tabs-container li:eq(0) span.icon-remove').click();

        ok($('#qunit-fixture .tabs-container .tab-content > div').length === 1);
        ok($('#qunit-fixture .tabs-container li').length === 1);
        ok($('#qunit-fixture .tabs-container li:eq(0).active').length === 1);
        ok($('#qunit-fixture div.tab-content > div:eq(0)[id="second-tab"].active').length === 1);

    });
  
    test('Close icon shows on selected tab and when mouse is enetered', function() {
        var tabs = new Aes.TabsView({
          tabs: {
              first: {
                  title: '<b>First</b> tab title',
                  content: 'First tab <b>content</b>',
                  closable: true
              },
              second: {
                  title: '<b>Second</b> tab title',
                  content: 'Second tab <b>content</b>',
                  closable: true
              }
          }
        });

        $('#qunit-fixture').append(tabs.render().el);
        tabs.triggerMethod('show');

        ok($('#qunit-fixture .tabs-container li:eq(0) span.icon-remove:visible').length === 1);
        ok($('#qunit-fixture .tabs-container li:eq(1) span.icon-remove:visible').length === 0);

        $('#qunit-fixture .tabs-container li:eq(0) span.icon-remove').mouseleave();

        ok($('#qunit-fixture .tabs-container li:eq(0) span.icon-remove:visible').length === 1);
        ok($('#qunit-fixture .tabs-container li:eq(1) span.icon-remove:visible').length === 0);

        $('#qunit-fixture .tabs-container li:eq(1) span.icon-remove').mouseenter();

        ok($('#qunit-fixture .tabs-container li:eq(0) span.icon-remove:visible').length === 1);
        ok($('#qunit-fixture .tabs-container li:eq(1) span.icon-remove:visible').length === 1);

        $('#qunit-fixture .tabs-container li:eq(1) span.icon-remove').mouseleave();

        ok($('#qunit-fixture .tabs-container li:eq(0) span.icon-remove:visible').length === 1);
        ok($('#qunit-fixture .tabs-container li:eq(1) span.icon-remove:visible').length === 0);        
    });
  
    test('Close icon does not show after rendered selected closable tab deselects', function(){
        var tabs = new Aes.TabsView({
          tabs: {
              first: {
                  title: '<b>First</b> tab title',
                  content: 'First tab <b>content</b>',
                  closable: true
              },
              second: {
                  title: '<b>Second</b> tab title',
                  content: 'Second tab <b>content</b>',
                  closable: true
              }
          }
        });

        $('#qunit-fixture').append(tabs.render().el);
        tabs.triggerMethod('show');
        
        ok($('#qunit-fixture .tabs-container li:eq(0) span.icon-remove:visible').length === 1);
        ok($('#qunit-fixture .tabs-container li:eq(1) span.icon-remove:visible').length === 0);
        
        $('#qunit-fixture .tabs-container li:eq(1) > a').mouseenter().click();
        
        ok($('#qunit-fixture .tabs-container li:eq(0) span.icon-remove:visible').length === 0);
        ok($('#qunit-fixture .tabs-container li:eq(1) span.icon-remove:visible').length === 1);
        
    });
    
    test('Renders subviews', function() {
        var layoutView = new SomeLayoutView();
        layoutView.firstRegion.show(new Aes.ItemView({tpl: '<b>First region</b> internal view'}));
        layoutView.secondRegion.show(new Aes.ItemView({tpl: '<b>Second region</b> internal view'}));
        
        $('#qunit-fixture').append('<h6>Layout view exanple</h6><div id="layoutCntr"></div>');
        $('#qunit-fixture #layoutCntr').append(layoutView.render().el);
        
        var tabs = new Aes.TabsView({
          tabs: {
              first: {
                  title: '<b>First</b> tab title',
                  content: 'First tab <b>content</b>',
                  closable: true
              },
              second: {
                  title: '<b>Second</b> tab title',
                  content: new Aes.ItemView({tpl: '<b>Subview content</b>'}),
                  closable: true
              },
              third: {
                  title: 'Third',
                  content: layoutView,
                  closable: true
              }
          }
        });

        $('#qunit-fixture').append(tabs.render().el);
        tabs.triggerMethod('show');
        
        ok(false);
    });
    
    var SomeLayoutView = Marionette.Layout.extend({
        template: '#some-layout-tpl',
        regions: {
            firstRegion: '#first',
            secondRegion: '#second'
        }
    });
</script>

<script type="text/template" id="some-layout-tpl">
<div>
    <div id="first">View should be rendered here</div>
    <div id="second">View should be rendered here</div>
</div>
</script>