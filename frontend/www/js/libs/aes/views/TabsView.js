/* 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
Aes.TabsView = Aes.ItemView.extend({
    
    ui: {
        tabBodies: 'div.tab-content',
        tabTitles: 'ul.nav-tabs'
    },
    
    className: 'tabs-container',
    
    tabViews: null,
    
    getTplStr: function() {
        return Aes.TabsView.getTpl();
    },
    
    getSelected: function() {
        return this.tabViews.find(function(tabView) {
            return tabView.selected;
        });
    },
    
    getTab: function(tabId) {
        return this.tabViews.findByCustom(tabId);
    },
    
    select: function(tab) {
        if (typeof tab === 'string') {
           tab = this.getTab(tab); 
        }
        
        tab.select();
    },
    
    add: function(tabOptions, append) {
        if(append === undefined)
            append = true;
        
        _.extend(tabOptions, {
            tabsContainer: this
        });
        
        this.tabViews.add(new TabView(tabOptions), tabOptions.tabId);
        
        if(append) {
            var newTab = this.tabViews.findByCustom(tabOptions.tabId);
            this.appendTabView(newTab);
        }
    },
            
    remove: function(tabId) {
        var tab = this.tabViews.findByCustom(tabId);
        
        if(tab) {
            if(tab.selected)
                this.tabViews.first().select();
                
            this.tabViews.remove(tab);
            this.render();
        }
    },
    
    onRender: function() {
        
        this.ui.tabBodies.html('');
        this.ui.tabTitles.html('');
        
        this.tabViews.each(_.bind(function(tab) {
           this.appendTabView(tab);
        },this));
        
    },
    
    renderTab: function(tab) {
        tab.titleView.render();
        tab.render();
    },
    
    appendTabView: function(tabView) {
        this.renderTab(tabView);
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
        var selectedTab = this.options.tabsContainer.getSelected();
        if(selectedTab)
            selectedTab.selected = false;

        this.titleView.select();
        this.selected = true;
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

    clicked: function() {
       this.options.tabContentView.select();
    },

    closeClicked: function() {
       this.options.tabsContainer.remove(this.model.get('id'));
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
        if(!this.options.tabContentView.selected) {
            this.$('.icon-remove').hide();
        }
    },
            
    select: function() {
       this.ui.anchor.tab('show');
    },

    tagName: 'li',

    tpl: '<a href="#<%= id %>-tab"><%= title %><% if(closable === true) { %>&nbsp;<span class="icon-remove"></span><% } %></a>'
});