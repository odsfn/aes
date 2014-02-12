/* 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
var Aes = Aes || {};

Aes.NoItemView = Aes.ItemView.extend({
    getTplStr: function() {
        return Aes.NoItemView.getTpl();
    }
}, {
    getTpl: function() {
        return 'There is no items.';
    }
});
