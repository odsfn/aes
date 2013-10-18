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

	$criteria = new CDbCriteria(array(
            //Select only active
            'join' => 'INNER JOIN user ON user.id = t.user_id AND user.status = ' . UserAccount::STATUS_ACTIVE
        ));

        $criteria->mergeWith(self::getCriteriaFindByName($this->name));
        
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
    
    /**
     * Creates or modifies criteria to search by user name
     * 
     * @param string $name  Of the user to find
     * @param string $columnPrefix
     * @param CDbCriteria $criteria
     * @return \CDbCriteria
     */
    public static function getCriteriaFindByName($name, $columnPrefix = null, $criteria = null) {
        
        if(empty($criteria))
            $criteria = new CDbCriteria;
        
        if(!empty($columnPrefix))
            $columnPrefix .= '.';
        
        if(($pos = strpos($name, ' ')) !== FALSE && $pos > 0) {
            
            $nameParts = explode(' ', $name);
            
            $criteria->addCondition('(' . $columnPrefix . 'first_name LIKE "%' . $nameParts[0] . '%" AND ' . $columnPrefix . 'last_name LIKE "%' . $nameParts[1] . '%")'
                    . ' OR (' . $columnPrefix . 'first_name LIKE "%' . $nameParts[1] . '%" AND ' . $columnPrefix . 'last_name LIKE "%' . $nameParts[0] . '%")'
            );
            
        } else {
            $criteria->compare($columnPrefix . 'first_name', $name, true);
            $criteria->compare($columnPrefix . 'last_name', $name, true, 'OR');
        }
        
        return $criteria;
    }
}
