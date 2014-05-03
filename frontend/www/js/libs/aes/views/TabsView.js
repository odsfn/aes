/** 
 * TabsView
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
Aes.TabsView = (function() { 
    
    var TabsView = Aes.ItemView.extend({
    
        ui: {
            tabBodies: 'div.tab-content',
            tabTitles: 'ul.nav-tabs'
        },

        className: 'tabs-container',

        tabViews: null,

        getTplStr: function() {
            return Aes.TabsView.getTpl();
        },

        /** 
         * Searches and return currently selected tab 
         * @return {TabView} Selected tab 
         */
        getSelected: function() {
            return this.tabViews.find(function(tabView) {
                return tabView.selected;
            });
        },

        /**
         * Searches tab by it's id
         * @param {String} tabId
         * @return {TabView} Found tab *undefinded* otherwise
         */
        getTab: function(tabId) {
            return this.tabViews.findByCustom(tabId);
        },

        /**
         * Switches TabsView to specified tab ( makes it visible )
         * @param {String|TabView} tab Tab id or TabView to select
         */
        select: function(tab) {
            if (typeof tab === 'string') {
               tab = this.getTab(tab); 
            }

            var shouldSelect = this.triggerMethod('before:select', tab);
            if(!shouldSelect)
                return;

            tab.select();
            this.trigger('selected', tab);
        },

        /**
         * Adds new tab to TabsView
         * @param {Object} tabOptions Options that will be passed to the TabView.initialize
         * @param {Boolean} append Whether to render and append new tab html after success adding
         * @example <caption>tabOptions properties</caption>
         * {
         *      tabId: 'someTab',               //unique tab id
         *      title: '<b>Some title</b>',     //text or html content to show as tab title
         *      content: 'Some <i>content</i>', //text or html to show as tab content
         *      closable: true                  //set to true if you want allow for the user ability to close tab
         * }
         * @example <caption>Adding tab view with sub-view</caption>
         * {
         *      tabId: 'someOtherTab',
         *      title: '<b>Some other tab title</b>',
         *      content: new SomeView(),                //this view will be rendered and shown with TabsView
         *      closable: true
         * }
         */
        add: function(tabOptions, append) {
            if(append === undefined)
                append = true;

            _.extend(tabOptions, {
                tabsContainer: this
            });

            var newTab = new TabView(tabOptions);
            
            var shouldAdd = this.triggerMethod("before:add", newTab);
            if(!shouldAdd)
                return;

            this.tabViews.add(newTab, tabOptions.tabId);

            if(append) {
                var newTab = this.tabViews.findByCustom(tabOptions.tabId);
                this._appendTabView(newTab);
            }

            this.trigger('added', newTab);
            
            return newTab;
        },

        /**
         * Remove TabView from TabsView and destroy it's html
         * @param {String} tabId
         */
        removeTab: function(tabId) {
            var tab = this.tabViews.findByCustom(tabId);

            var shouldRemove = this.triggerMethod('before:remove', tab);
            if(!shouldRemove)
                return;

            if(tab) {                
                this.tabViews.remove(tab);

                if(tab.selected)
                    this.tabViews.first().select();

                this.render();
            }

            this.trigger('removed', tab);
        },

        onRender: function() {

            this.ui.tabBodies.html('');
            this.ui.tabTitles.html('');

            this.tabViews.each(_.bind(function(tab) {
               this._appendTabView(tab);
            },this));

        },

        onBeforeSelect: function(tab) {
            return true;
        },

        onBeforeAdd: function(tab) {
            return true;
        },

        onBeforeRemove: function(tab) {
            return true;
        },

        _renderTab: function(tab) {
            tab.titleView.render();
            tab.titleView.delegateEvents();
            tab.render();
            tab.delegateEvents();
        },

        _appendTabView: function(tabView) {
            this._renderTab(tabView);
            this.ui.tabTitles.append(tabView.titleView.$el);
            this.ui.tabBodies.append(tabView.$el);
        },

        onShow: function() {
            this.tabViews.each(function(tab) {
               tab.titleView.triggerMethod('show');
               tab.triggerMethod('show');
            });

            var selected = this.getSelected();
            if(selected)
                this.select(selected);
        },

        initialize: function(options) {
            this.tabViews = new Backbone.ChildViewContainer;

            for(var tabName in options.tabs) {
                var tabOptions = options.tabs[tabName];
                _.extend(tabOptions, {
                    tabId: tabName
                });

                this.add(tabOptions, false);
            }

            if(this.tabViews.length > 0 && !this.getSelected())
                this.tabViews.first().selected = true;
        }

    }, {
        getTpl: function() {
            return '<ul class="nav nav-tabs"></ul>'
                 + '<div class="tab-content"></div>';
        }
    });

    var TabView = Aes.ItemView.extend({
        selected: false, 

        tpl: '<%= content %>',

        className: 'tab-pane tab-view',

        titleView: null,

        contentView: null,

        select: function() {
            var shouldSelect = this.triggerMethod('before:select');
            if(!shouldSelect)
                return;
            
            var selectedTab = this.options.tabsContainer.getSelected();
            if(selectedTab)
                selectedTab.unselect();

            this.titleView.select();
            this.selected = true;

            this.options.tabsContainer.trigger('selected', this);
        },

        unselect: function() {
            this.selected = false;
            this.titleView.unselect();
        },

        remove: function() {
            this.options.tabsContainer.removeTab(this.model.get('id'));

            Aes.ItemView.prototype.remove.apply(this, arguments);
        },

        onRender: function() {
            if (this.contentView) {
               this.contentView.render();
               this.$el.html(this.contentView.$el);
            }
        },

        onShow: function() {
            if (this.contentView) {
                this.contentView.triggerMethod('show');
            }
        },

        onBeforeSelect: function() {
            var handler;
            
            if(handler = this.options.onBeforeSelect)
                return handler.apply(this);
                
            return true;
        },

        initialize: function(options) {
            this.$el.attr('id', options.tabId + '-tab');

            var modelAttrs = {
                id: options.tabId,
                title: options.title,
                closable: options.closable || false,
                content: ''
            };

            if (typeof options.content === 'string') {
                modelAttrs.content = options.content;
            } else {
                this.contentView = options.content;
            }

            this.model = new Backbone.Model(modelAttrs);

            this.titleView = new TabTitleView({
               tabsContainer: options.tabsContainer,
               tabContentView: this,
               model: this.model,
               tabId: options.tabId
            });
        }

    });

    var TabTitleView = Aes.ItemView.extend({
        ui: {
           anchor: 'a'
        },

        events: {
           'click a': 'clicked',
           'click a > span.icon-remove': 'closeClicked',
           'mouseenter': 'mouseEntered',
           'mouseleave': 'mouseLeaved'
        },

        clicked: function(event) {
            event.preventDefault();
            this.options.tabContentView.select();
        },

        closeClicked: function() {
           this.options.tabsContainer.removeTab(this.model.get('id'));
        },

        mouseEntered: function() {
            if(this.model.get('closable') === true)
                this.$('.icon-remove').show();
        },

        mouseLeaved: function() {
            if(!this.options.tabContentView.selected && this.model.get('closable') === true)
                this.$('.icon-remove').hide();
        },

        onRender: function() {
            if(!this.options.tabContentView.selected && !this.options.tabContentView.selected) {
                this.$('.icon-remove').hide();
            }
        },

        select: function() {
           this.ui.anchor.tab('show');
           this.$('.icon-remove').show();
        },

        unselect: function() {
           this.$('.icon-remove').hide();    
        },

        tagName: 'li',

        tpl: '<a href="#<%= id %>-tab"><%= title %><% if(closable === true) { %>&nbsp;<span class="icon-remove"></span><% } %></a>'
    });

    return TabsView;
})();