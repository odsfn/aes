<div class="row-fluid">
    <button id="register-candidate" class="btn btn-large span8 offset2">Register as Candidate</button>
</div>
<script type="text/javascript">
    $(function() {
        var parent = $('#register-candidate').parent();
        var onSuccess = function(model) {
            $('#register-candidate').remove();

            if(model.checkStatus('Registered')) {
                Aes.Notifications.add('You have been registered as candidate.', 'success');
            } else {
                Aes.Notifications.add('Your registration request was sent. Election manager will consider it as soon as possible.', 'success');
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
                success: function(model) {
                    onSuccess(model);
                }
            });
        });
    });
</script>