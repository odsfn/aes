var PetitionsFeedView = Aes.FeedView.extend({
    template: '#petitions-feed-tpl',
    itemView: PetitionView,

    getFiltersConfig: function() {
        return {

            enabled: true,

            submitBtnText: 'Filter',

            uiAttributes: {
                form: {
                    class: 'span3 well'
                },
                inputs: {
                    class: 'span12'
                }
            },

            fields: {
                title: {
                    label: 'Petition title',
                    type: 'text',

                    filterOptions: {
                        extendedFormat: true
                    }
                },
                creator_name: {
                    label: 'Authored by'
                },
                support: {
                    label: 'Support type',
                    type: 'radio-group',
                    options: [
                        {label: 'Any', value: 'any', checked: true},
                        {label: 'Created by me', value: 'created_by_user'},
                        {label: 'Supported by me', value: 'supported_by_user'}
                    ]
                },
                creation_date: {
                    label: 'Creation date',
                    type: 'radio-group',
                    options: [
                        {label: 'Any', value: 'any', checked: true},
                        {label: 'Today', value: 'today'},
                        {label: 'This week', value: 'week'},
                        {label: 'This month', value: 'month'}
                    ]
                }
            }  
        };
    }
});