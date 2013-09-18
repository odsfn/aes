<?php
/**
 * Form to search people
 * 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class PeopleSearch extends CFormModel{
    
    public $name;
    
    public $ageFrom;
    
    public $ageTo;
    
    public $gender;
    
    public $birth_place;
    
    public $birth_day;
    
    public function rules() {
	return array(
//	    array('name', 'match', 'pattern'=>'/^[:alpha:]{2,}$/u'),
            
	    array('gender', 'numerical', 'integerOnly' => true),
            array('gender', 'in', 'range' => Profile::getAvailableGenders(), 'allowEmpty' => true),
            
            array('ageFrom', 'numerical', 'min' => 0, 'max' => 128,'integerOnly' => true),
            
            array('ageTo', 'numerical', 'min' => 1, 'max' => 128,'integerOnly' => true),
            array('ageTo', 'compare', 'compareAttribute' => 'ageFrom', 'operator' => '>', 'allowEmpty' => true),
            
	    array('name, birth_place', 'length', 'max' => 128),
	    
	    array('birth_day', 'date', 'format'=>'MM/dd/yyyy'),
            
	    // The following rule is used by search().
	    // Please remove those attributes that should not be searched.
	    array('name, birth_place, ageFrom, ageTo, gender, birth_day', 'safe', 'on' => 'search'),
	);
    }
    
    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
	return array(
	    'name' => 'Name',
	    'birth_place' => 'Birth Place',
	    'ageFrom' => 'Age from',
	    'ageTo' => 'Age to',
	    'gender' => 'Gender',
            'birth_day' => 'Birth Day'
	);
    }
    
    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search() {
	// Warning: Please modify the following code to remove attributes that
	// should not be searched.

	$criteria = new CDbCriteria;
        
        if(($pos = strpos($this->name, ' ')) !== FALSE && $pos > 0) {
            
            $nameParts = explode(' ', $this->name);
            
            $criteria->addCondition('(first_name LIKE "%' . $nameParts[0] . '%" AND last_name LIKE "%' . $nameParts[1] . '%")'
                    . ' OR (first_name LIKE "%' . $nameParts[1] . '%" AND last_name LIKE "%' . $nameParts[0] . '%")'
            );
            
        } else {
            $criteria->compare('first_name', $this->name, true);
            $criteria->compare('last_name', $this->name, true, 'OR');
        }

        
	$criteria->compare('birth_place', $this->birth_place, true);
        
        if($this->birth_day) {
            
            $date = new DateTime($this->birth_day);
            $criteria->compare('birth_day',$date->format('Y-m-d'));
            
            $this->ageFrom = null;
            $this->ageTo = null;
            
        }else{

            if($this->ageFrom) {
                $criteria->compare('birth_day', '<=' . $this->calculateStartDate($this->ageFrom)->format('Y-m-d'));
            }

            if($this->ageTo) {
                $criteria->compare('birth_day', '>=' . $this->calculateStartDate($this->ageTo)->format('Y-m-d'));
            }
            
        }
        
	$criteria->compare('gender', $this->gender);

	return new CActiveDataProvider(new Profile, array(
	    'criteria' => $criteria,
	));
    }
    
    /**
     * Calculates date from which specified amount of years have passed at 
     * the specified moment
     * @param int $years
     * @param DateTime $at From this moment. Default now.
     * @return DateTime
     */
    public function calculateStartDate($years, $at = null) {
        if(empty($at))
            $at = new DateTime;
        
        $at->sub(new DateInterval('P' . $years . 'Y'));
        
        return $at;
    }
}
