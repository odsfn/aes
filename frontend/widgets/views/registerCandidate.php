<div class="row-fluid">
    <button id="register-candidate" class="btn btn-large span8 offset2">Register as Candidate</button>
</div>
<script type="text/javascript">
    $(function() {
        var autoElectorRegistration = false;
        <?php 
        if ($this->election->autoElectorRegistrationAvailable())
            echo "autoElectorRegistration = true;";
        ?>
        var parent = $('#register-candidate').parent();
        var onSuccess = function(model, response, options) {
            $('#register-candidate').remove();
            var message = '';
            
            if(response.status && response.status == 'exists') {
                Aes.Notifications.add(response.message, 'warning');
            } else if(model.checkStatus('Registered')) {
                message = 'You have been registered as candidate.';
                
                if(autoElectorRegistration) {
                    message = 'You have been registered as candidate and elector.';
                }
                
                Aes.Notifications.add(message, 'success');
            } else {
                Aes.Notifications.add('Your registration request was sent. Election manager will consider it as soon as possible.', 'success');
            }               

            if(autoElectorRegistration && $('#register-elector').length > 0) {
                $('#register-elector').remove();
            }

            $('body').trigger('candidate_registered', [model]);
            parent.unmask();
        };

        $('#register-candidate').click(function(){

            parent.mask();

            var candidate = new Candidate({
                user_id: <?= Yii::app()->user->id ?>,
                election_id: <?= $this->election->id ?>
            });

            candidate.save({}, {
                success: function(model, response, options) {
                    onSuccess(model, response, options);
                }
            });
        });
    });
</script>