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
$clientScript->registerScriptFile('/js/libs/aes/views/FormView.js');
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
    
    <div class="qfix-1">
        <div>
            <input type="text" value="" class="span4">
        </div>
    </div>
    <div class="qfix-5">
        <input type="text" value="" class="span4"/>
    </div>
    
    <div class="qfix-6">
        <div>
            <select name="sel1" class="span4">
                <option value="1" selected="selected">A</option>
                <option value="2">B</option>
            </select>
        </div>
    </div>
    
    <div class="qfix-7">
        <div class="row-fluid">
            <div class="search-form span4">
                <form class="well form-vertical">
                    <div>
                        <label for="PeopleSearch_name">Name</label>
                        <input type="text" id="PeopleSearch_name" name="name" maxlength="128" class="filter span12">
                    </div>
                    
                    <div>
                        <label for="birth_place">Birth Place</label>
                        <input type="text" id="PeopleSearch_birth_place" name="birth_place" maxlength="128" class="filter span12">
                    </div>
                    
                    <div>
                        <label for="ageFrom">Age from</label>
                        <input type="text" id="PeopleSearch_ageFrom" name="ageFrom" class="filter span12">
                    </div>
                    
                    <div>
                        <label for="ageTo">Age to</label>
                        <input type="text" id="PeopleSearch_ageTo" name="ageTo" class="filter span12">
                    </div>
                    
                    <div>
                        <label for="gender">Gender</label>
                        <select id="PeopleSearch_gender" name="gender" class="filter span12">
                            <option selected="selected" value="">Any</option>
                            <option value="1">Male</option>
                            <option value="2">Female</option>
                        </select>
                    </div>
                    
                    <div class="form-actions">
                        <input type="button" value="Search" class="btn btn-primary form-submit">
                        <input type="button" value="Reset" class="btn form-reset">
                    </div>
                </form>           
            </div>
        </div>
    </div>
    
    <div class="qfix-19">
        <div class="navbar head">
            <div class="navbar-inner">
                <ul class="nav pull-right">
                    <li><a id="items-count"><img style="display: none;" src="/img/loader-circle-16.gif" class="loader">Found <span class="items-count">2</span> </a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    
    var outAfterQunitBlock = false;
    
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
    
    module('FormFields input-text', {
        setup: function() {
            prepareFixture();
        },
        
        teardown: function() {
            passOutput();
        }
    });
    
    test('#1 TextFormField gotchas from DOM', function(){
        var $input = $('#qunit-fixture div');
    
        var textField = new Aes.TextFormField({
            el: $input
        });
        
        textField.setValue('Foo');
        
        equal($('#qunit-fixture input').val(),'Foo');
        equal(textField.getValue(), 'Foo');
    });
    
    test('#2 TextFormField renders', function() {
        var textField = new Aes.TextFormField();
        textField.render();
        
        $('#qunit-fixture').append(textField.$el);
        
        equal($('#qunit-fixture input[type="text"]').length, 1);
    });
    
    test('#3 TextFormField accepts value', function() {
        var textField = new Aes.TextFormField();
        $('#qunit-fixture').append(textField.render().$el);
        
        $('#qunit-fixture input[type="text"]').val('New Value').blur();
        
        equal(textField.getValue(), 'New Value');
    });
    
    test('TextFormField validate', function(){
        var textField = new Aes.TextFormField({
            validator: {
                required: true,
                pattern: 'email'
            }
        });
        $('#qunit-fixture').append(textField.render().$el);
        
        $('#qunit-fixture input[type="text"]').val('New Value').blur();
        
        equal(textField.getValue(), 'New Value');
        
        ok($('#qunit-fixture .error').length > 0);
        
        $('#qunit-fixture input[type="text"]').val('validemail@gmail.com').blur();
        
        ok($('#qunit-fixture .error').length === 0);
    });
    
    test('validate picked up from DOM', function() {
        var textField = new Aes.TextFormField({
            el: $('#qunit-fixture input'),
            validator: {
                required: true,
                pattern: 'email'
            }
        });
        $('#qunit-fixture').append(textField.render().$el);
        
        $('#qunit-fixture input[type="text"]').val('New Value').blur();
        
        equal(textField.getValue(), 'New Value');
        
        ok($('#qunit-fixture .error').length > 0);
        
        $('#qunit-fixture input[type="text"]').val('validemail@gmail.com').blur();
        
        ok($('#qunit-fixture .error').length === 0);        
    });
    
    test('Select pickes up from DOM', function() {
        var select = new Aes.SelectFormField({
            el: $('#qunit-fixture div')
        });
        
        equal(select.getValue(), '1');
    });
    
    test('Form pickes up from DOM', function() {
        var form = new Aes.FormView({
            el: $('#qunit-fixture form'),

            fields: {
                name: {
                    type: 'text',
                    el: $('#qunit-fixture input[name="name"]').parent()
                },
                birth_place: {
                    el: $('#qunit-fixture input[name="birth_place"]').parent()
                },
                ageFrom: {
                    el: $('#qunit-fixture input[name="ageFrom"]').parent(),
                    validator: {
                        required: false,
                        min: 1,
                        max: 100
                    }
                },
                ageTo: {
                    el: function() { return $('#qunit-fixture input[name="ageTo"]').parent(); },
                    validator: {
                        required: false,
                        min: 1,
                        max: 100,
                        greaterThan: {
                            attr: 'ageFrom',
                            validOnEqual: true
                        }
                    }
                },
                gender: {
                    type: 'select',
                    el: $('#qunit-fixture select[name="gender"]').parent()
                }
            },

            onSubmit: function(event) {
                console.log('Form submitted with values: ' + JSON.stringify(this.getValues()));

                if(this.hasErrors())
                    console.log('Form has errors: ' + JSON.stringify(this.getErrors()));
            }            
        });
        
        ok(form.getFields()['name']);
        
        $('#qunit-fixture input[name="ageFrom"]').val(0).blur();
        ok($('#qunit-fixture .error').length > 0);
        
        $('#qunit-fixture input[name="ageFrom"]').val('').blur();
        ok($('#qunit-fixture .error').length === 0);
        
//        $('#qunit-fixture input[name="ageTo"]').val(50).blur();
//        ok($('#qunit-fixture .error').length === 0);
//        
//        $('#qunit-fixture input[name="ageFrom"]').val(51);
//        $('#qunit-fixture input[name="ageTo"]').blur();
//        ok($('#qunit-fixture .error').length > 0);
//        
//        $('#qunit-fixture input[name="ageFrom"]').val(50).blur();
//        ok($('#qunit-fixture .error').length === 0);
//        
//        $('#qunit-fixture input[name="ageFrom"]').val(5).blur();
//        ok($('#qunit-fixture .error').length === 0);       
    });
    
    test('Form renders', function() {
        var form = new Aes.FormView({
            attributes: {
                class: 'span4'
            },
            fields: {
                name: {
                    label: 'Name',
                    type: 'text'
                },
                birth_place: {
                    label: 'Birth place',
                    type: 'text'
                },
                ageFrom: {
                    label: 'Age From',
                    validator: {
                        required: false,
                        min: 1,
                        max: 100
                    }
                }
            }
        });
        
        $('#qunit-fixture').append('<div class="row-fluid"></div>');
        $('#qunit-fixture .row-fluid').append(form.render().$el);
        
        ok($('#qunit-fixture').find('form').length > 0);
        ok($('#qunit-fixture form').find('input[type="text"]').length === 3);
        ok($('#qunit-fixture form').find('input[type="button"]').length === 2);
    });
    
    test('Field renders with label', function() {
        var input = new Aes.TextFormField({
            name: 'foo',
            label: 'Foo'
        });
        
        $('#qunit-fixture').append(input.render().$el);
        ok($('#qunit-fixture').has('input[type="text"][name="foo"]').length === 1);
        ok($('#qunit-fixture').find('label').length > 0);
    });
    
    test('Input accepts attributes', function(){
        var input = new Aes.TextFormField({
            id: 'i-1',
            
            attributes: {
                title: 'foo'
            },
            
            uiAttributes: {
                input: {
                    id: 'input-bar',
                    class: 'span6',
                    "data-bar": 'uiui'
                }
            },
            
            name: 'foo',
            label: 'Foo'
        });
        
        $('#qunit-fixture').append(input.render().$el);
        equal($('#qunit-fixture div').attr('id'), 'i-1');
        equal($('#qunit-fixture div').attr('title'), 'foo');
        ok($('#qunit-fixture input').hasClass('span6'));
        equal($('#qunit-fixture input').attr('id'), 'input-bar');
        equal($('#qunit-fixture input').attr('data-bar'), 'uiui');
    });

    test('Get form fields values', function() {
        var form = new Aes.FormView({
            fields: {
                a: {},
                b: {},
                c: {}
            }
        });
        
        $('#qunit-fixture').append(form.render().$el);
        
        $('#qunit-fixture input[name="a"]').val(1).blur();
        $('#qunit-fixture input[name="b"]').val('foo').blur();
        $('#qunit-fixture input[name="c"]').val('bar').blur();
        
        ok(_.isEqual(form.getValues(), {a:'1', b: 'foo', c: 'bar'}));
    });
    
    test('Form renders fields in correct ordrer', function() {
        var form = new Aes.FormView({
            fields: {
                a: {},
                b: {},
                c: {}
            }
        });
        
        $('#qunit-fixture').append('<div class="row-fluid"></div>');
        $('#qunit-fixture .row-fluid').append(form.render().$el);
        
        equal($('#qunit-fixture input:eq(0)').attr('name'), 'a');
        equal($('#qunit-fixture input:eq(2)').attr('name'), 'c');
    });
    
    test('Form customize attributes', function() {
        var form = new Aes.FormView({
            
            attributes: {
                class: 'span6'
            },
            
            uiAttributes: {
                form: {
                    class: 'form-horizontal'
                },
                
                inputs: {
                    class: 'span8'
                }
            },
            
            fields: {
                a: {},
                b: {},
                c: {
                    uiAttributes: {
                        input: {
                            class: 'span4'
                        }
                    }
                }
            }
        });
        
        
        $('#qunit-fixture').append('<div class="row-fluid"></div>');
        $('#qunit-fixture .row-fluid').append(form.render().$el);
        
        equal($('#qunit-fixture .row-fluid > div').attr('class'), 'span6');
        equal($('#qunit-fixture form').attr('class'),'form-horizontal');
        equal($('#qunit-fixture input[name="a"]').attr('class'),'span8');
        equal($('#qunit-fixture input[name="b"]').attr('class'),'span8');
        equal($('#qunit-fixture input[name="c"]').attr('class'),'span4');
    });    
    
    test('Form validate on submit', function() {
        
        expect(3);
        
        var form = new Aes.FormView({
            fields: {
                a: {
                    validator: {
                        required: true
                    }
                },
                b: {},
            },
            
            onSubmit: function() {
                ok(true, 'on submit called');
            }
        });
        
        $('#qunit-fixture').append(form.render().$el);
        
        $('#qunit-fixture .form-submit').click();
        ok($('#qunit-fixture .error').length === 1);
        
        $('#qunit-fixture input[name="a"]').val('foo');
        $('#qunit-fixture .form-submit').click();
        ok($('#qunit-fixture .error').length === 0);
        
    });
    
    test('Reset fields', function() {
        var form = new Aes.FormView({
            fields: {
                a: {},
                b: {}
            }
        });
        
        $('#qunit-fixture').append(form.render().$el);
        
        $('#qunit-fixture input[name="a"]').val('foo').blur();
        $('#qunit-fixture input[name="b"]').val('bar');
        
        form.reset();
        
        equal($('#qunit-fixture input[name="a"]').val(), '');
        equal($('#qunit-fixture input[name="b"]').val(), '');
    });
    
    test('Form buttons text', function() {
        var form = new Aes.FormView({
            
            submitBtnText: 'Go',
            
            resetBtnText: 'No!No!No!',
            
            fields: {
                a: {},
                b: {}
            }
        });
        
        $('#qunit-fixture').append(form.render().$el);
        equal($('#qunit-fixture .form-submit').val(), 'Go');
        equal($('#qunit-fixture .form-reset').val(), 'No!No!No!');
    });
    
    test('Textarea field', function() {
        
        var textarea = new Aes.TextareaFormField({
            showLabel: true,
            validator: {
                required: true
            }
        });
        
        $('#qunit-fixture').append(textarea.render().$el);
        
        ok($('#qunit-fixture').find('textarea').length === 1);
        
        textarea.setValue('Some interesting text');
        
        equal($('#qunit-fixture textarea').val(), 'Some interesting text');
        
        $('#qunit-fixture textarea').val('');
        $('#qunit-fixture textarea').blur();
        ok($('#qunit-fixture .error').length > 0);
        
        $('#qunit-fixture textarea').val('Hello world!').blur();
        
        equal(textarea.getValue(), 'Hello world!');
        ok($('#qunit-fixture .error').length === 0);
    });
    
    test('Select field', function() {
       var select = new Aes.SelectFormField({
            name: 'foo_sel',
            options: [
                {label: 'One', value: 1},
                {value: 'Two'},
                {label: 'Three', value: 3, selected: true}
            ]
       });
       
       $('#qunit-fixture').append(select.render().$el);
       
       ok($('#qunit-fixture select').length === 1);
       ok($('#qunit-fixture option').length === 3);
       
       equal($('#qunit-fixture option[value="1"]').text(), 'One');
       equal($('#qunit-fixture option[value="Two"]').text(), 'Two');
       equal($('#qunit-fixture option[value="3"][selected="selected"]').text(), 'Three');
       
       equal($('#qunit-fixture select').val(), '3');
    });    
    
    test('Navbar Form', function() {
        var form = new Aes.NavbarFormView({
            fields: {
                a: {
                    validator: {
                        required: true
                    },
                    uiAttributes: {
                        input: {
                            class: 'span2'
                        }
                    }
                },
                b: {
                    validator: {
                        required: true
                    },
                    uiAttributes: {
                        input: {
                            class: 'span4'
                        }
                    }                
                }
            }
        });
        
        $('#qunit-fixture .navbar-inner').prepend(form.render().$el);
        equal($('#qunit-fixture form.form-inline.navbar-search').length, 1);
        
    });
    
    test('Radio Field', function() {
        var radio = new Aes.RadioFormField({
            name: 'foo_radio',
            value: 'foo_radio_value',
            label: 'Foo radio'
        });
        
        $('#qunit-fixture').append(radio.render().$el);
        
        ok($('#qunit-fixture input[type="radio"][name="foo_radio"][value="foo_radio_value"]').length === 1);
        ok($('#qunit-fixture input[name="foo_radio"]:checked').length === 0);
        
        equal(radio.getValue(), false);
        
        radio.check();
        equal(radio.getValue(), 'foo_radio_value');
        ok($('#qunit-fixture input[type="radio"][name="foo_radio"][value="foo_radio_value"]:checked').length === 1);
        
        radio.uncheck();
        equal(radio.getValue(), false);
        ok($('#qunit-fixture input[type="radio"][name="foo_radio"][value="foo_radio_value"]:checked').length === 0);
        
        $('#qunit-fixture label.radio').click();
        equal(radio.getValue(), 'foo_radio_value');
        ok($('#qunit-fixture input[type="radio"][name="foo_radio"][value="foo_radio_value"]:checked').length === 1);
        
        radio.uncheck();
        equal(radio.getValue(), false);
        ok($('#qunit-fixture input[type="radio"][name="foo_radio"][value="foo_radio_value"]:checked').length === 0);
        
        $('#qunit-fixture label.radio > input').click();
        equal(radio.getValue(), 'foo_radio_value');
        ok($('#qunit-fixture input[type="radio"][name="foo_radio"][value="foo_radio_value"]:checked').length === 1);        
    });
    
    test('Radio Field Cheked By default', function() {
        var radio = new Aes.RadioFormField({
            name: 'foo_radio1',
            value: 'foo_radio1_value',
            label: 'Foo radio 1',
            checked: true
        });
        
        $('#qunit-fixture').append(radio.render().$el);
        
        ok($('#qunit-fixture input[type="radio"][name="foo_radio1"][value="foo_radio1_value"]:checked').length === 1);
    });
    
    test('Radio Fields As Part Of Form', function() {
        var form = new Aes.FormView({
            fields: {
                bar_radio: {
                    type: 'radio-group',
                    options: [
                        {label: 'Option A', value: 'Value A'},
                        {label: 'Option B', value: 'Value B'}
                    ]
                }
            }
        });
        
        $('#qunit-fixture').append(form.render().$el);
        
        ok($('#qunit-fixture label.radio > input[type="radio"][name="bar_radio"][value="Value A"]').not(':checked').length === 1);
        ok($('#qunit-fixture label.radio > input[type="radio"][name="bar_radio"][value="Value B"]').not(':checked').length === 1);
        
        var values = form.getValues();
        equal(values.bar_radio, false);
        
        $('#qunit-fixture input[name="bar_radio"][value="Value B"]').click();
        ok($('#qunit-fixture input[name="bar_radio"][value="Value B"]:checked').length === 1);
        
        var values = form.getValues();
        equal(values.bar_radio, 'Value B');
        
        form.reset();
        equal(form.getValues().bar_radio, false);
        ok($('#qunit-fixture input[name="bar_radio"]:checked').length === 0);
        
        form.getField('bar_radio').setValue('Value A');
        ok($('#qunit-fixture input[name="bar_radio"]:checked').length === 1);
        ok($('#qunit-fixture input[name="bar_radio"][value="Value A"]:checked').length === 1);
        equal(form.getValues().bar_radio, 'Value A');
    });
    
    test('RadioFields has top-level lable', function() {
        var form = new Aes.FormView({
            fields: {
                bar_radio: {
                    type: 'radio-group',
                    label: 'Bar Radio',
                    options: [
                        {label: 'Option A', value: 'Value A'},
                        {label: 'Option B', value: 'Value B'}
                    ]
                }
            }
        });
        
        $('#qunit-fixture').append(form.render().$el);
        
        ok($('#qunit-fixture form > div > div > label.radio > input[type="radio"][name="bar_radio"][value="Value A"]').not(':checked').length === 1);
        ok($('#qunit-fixture form > div > div > label.radio > input[type="radio"][name="bar_radio"][value="Value B"]').not(':checked').length === 1);
        equal($('#qunit-fixture form > div > span.radios-heading').text(), 'Bar Radio');
    });
    
    test('RadioFields validation pass without check', function() { // TODO: Realize validation! 
    
        var form = new Aes.FormView({
            fields: {
                bar_radio: {
                    type: 'radio-group',
                    label: 'Bar Radio',
                    options: [
                        {label: 'Option A', value: 'Value A'},
                        {label: 'Option B', value: 'Value B'}
                    ],
                    
                    validator: {
                        required: true
                    }                    
                }
            }
        });
        
        $('#qunit-fixture').append(form.render().$el);
        
        ok(form.validate());
        
        ok($('#qunit-fixture .error').length === 0);
    });
//    test('MultySelect field');

//    test('Checkbox field', function(){
//        var check = new Aes.CheckboxFormField({
//            options: [
//                {label: 'One', value: 1},
//                {value: 'Two'},
//                {label: 'Three', value: 3, selected: true}
//            ],
//            name: 'somecheck'
//        });
//    });

//    test('Radio field');
//    test('Field defaults');
//    test('Field initial value');
//    test('Field shows caption'); ?through attributes?
//    test('Field mask');
//    test('Date field');
//    test('Get form errors');
//    test('Disable/enable field/form');
//    test('BootstrapForm type inline');
//    test('BootstrapForm type horizontal');
//    test('Pick up fields from DOM without el specifying');
//    test('Dependent form validators');
//    test('Form initialized then assigned to the element and picked from it')
</script>