<?php
/**
 * Template for the client application.
 * Represents Petition rates view.
 * 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
?>
<script type="text/template" id="petition-rates-tpl">
    <div class="support-count-info"><?= Yii::t('aes','Petition supporters') ?>: <%= likes %></div>
    <div>
        <span 
            class="icon-thumbs-up pull-right" 
            <% if(currentUserRate !== false) { %>
            title="<% if(currentUserRate <= 0) { %>Click to support<% } else { %>You've supported this. Click to express a neutral opinion<% } %>"
            <% } %>
        >  
            <% if(currentUserRate <= 0) { %>Support<% } else { %>Supported<% } %>
        </span>
        
        <span style="display: none;" class="icon-thumbs-down"></span>
    </div>
</script>