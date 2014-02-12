/* 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
var Aes = Aes || {};

Aes.TableItemView = Aes.ItemView.extend({
    getTplStr: function() {
        return Aes.TableItemView.getTpl();
    }
}, {
    getTpl: function() {
        return '<table class="table table-bordered table-striped">'
            + '<colgroup>'
              + '<col class="span1">'
              + '<col class="span7">'
            + '</colgroup>'
            + '<thead>'
              + '<tr><th>Attribute</th><th>Value</th></tr>'
            + '</thead>'
            + '<tbody>'
              + '<% for(attr in modelAttrs) { %>' 
                + '<tr><td><%= attr %></td><td><%= modelAttrs[attr] %></td></tr>'
              + '<% } %>'
            + '</tbody>'
          + '</table>';
    }
});