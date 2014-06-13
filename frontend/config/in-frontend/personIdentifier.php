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
            )
        ),
        'passport_rf' => array(
            'rules' => array(
                array('serial', 'match', 'pattern' => '/\d{4}/', 'allowEmpty' => false),
                array('number', 'match', 'pattern' => '/\d{6}/', 'allowEmpty' => false),
                array('issued', 'date', 'format' => 'MM/dd/yyyy', 'allowEmpty' => false),
                array('serial, number, issued, issuer', 'required')
            ),
//                        'labels' => array(
//                            'serial' => 'Серия',
//                            'code' => 'Код',
//                            'issued' => 'Дата выдачи',
//                            'issuer' => 'Орган, осуществивший выдачу'
//                        ),
//                        'fieldTypes' => array(
//                            'date' => 'datePicker',
//                            'issuer' => 'textarea'
//                        )
        )
    )
);

