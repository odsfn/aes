<?php
/**
 * personIdentifier module config file
 */
return array(
    'defaultIdentifierType' => 'passport',
    'personIdentifiers' => array(
        'passport' => array(
            'rules' => array(
                array('serialNumber, code', 'required'),
                array('code', 'numerical', 'integerOnly' => true, 'min' => 0, 'max' => 9999)
            )
        ),
        'anotherId' => array(
            'rules' => array(
                array('someField, anotherField', 'required')
            ),
            'form' => array(
                'popover' => array(
                    'someField' => array(
                        'img' => 'Passport_RF.jpg'
                    )
                )
            )            
        ),
        'passport_rf' => array(
            'caption' => 'Пасспорт гражданина Российской Федерации', 
            'rules' => array(
                array('serial', 'match', 'pattern' => '/\d{4}/', 'allowEmpty' => false),
                array('number', 'match', 'pattern' => '/\d{6}/', 'allowEmpty' => false),
                array('issued', 'date', 'format' => 'MM/dd/yyyy', 'allowEmpty' => false),
                array('serial, number, issued, issuer', 'required')
            ),
            'form' => array(
                'popover' => array(
                    'serial, number' => array(
                        'img' => 'Passport_RF.jpg',
                        'title' => 'Document fields example',
                    )
                )
            ),
            'labels' => array(
                'serial' => 'Серия',
                'number' => 'Номер',
                'issued' => 'Дата выдачи',
                'issuer' => 'Орган, осуществивший выдачу'
            )
        )
    )
);

