/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var PersonIdentifier = (function(){
    var initPopup = function(conf) {
        $('#PersonIdentifier_' + conf.attr).popover({
            title: conf.title,
            html: true,
            trigger: 'focus',
            placement: 'left',
            animation: true,
            content: '<div style="width: ' + conf.width + 'px; height: ' + conf.height + 'px;"><img src="' + conf.img + '"></div>'
        });
    };
    
    var initPopups = function(conf) {
        for (var i = 0; i < conf.length; i++) {
            initPopup(conf[i]);
        }
    };
    
    return {
        'initPopups': initPopups
    }
})();

