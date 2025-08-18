<?php

class GroupNameValidator extends AppModel {
  // Required by COmanage Plugins

  public $name = "GroupNameValidator";

  public $belongsTo = array(
    "Co",
  );

  public $cmPluginHasMany = array(
    "Co" => array("GroupNameValidator")
  );

  public $cmPluginType = "other";

  public $displayField = "description";

  // Validation rules for table elements
  public $validate = array(
    'co_id' => array(
      'rule' => 'numeric',
      'required' => true,
      'message' => 'A CO ID must be provided'
    ),
    'description' => array(
      'rule' => array('validateInput'),
      'required' => false,
      'allowEmpty' => true
    ),
    'status' => array(
      'rule' => array('inList', array(SuspendableStatusEnum::Active,
                                        SuspendableStatusEnum::Suspended)),
      'required' => true,
      'allowEmpty' => false
    ),
    'name_format' => array(
      'rule' => '/.*/',
      'required' => true,
      'allowEmpty' => false
    ),
    'error_message' => array(
      'rule' => array('validateInput'),
      'required' => true,
      'allowEmpty' => false
    )
  );
        

  public function cmPluginMenus() {
    return array(
      "coconfig" => array(_txt('ct.group_name_validators.1') =>
        array('icon'       => 'playlist_add_check',
              'controller' => 'group_name_validators',
              'action'     => 'index')
      )
    );
  }

  /**
   * Actions to take before a save operation is executed.
   *
   */

  public function beforeSave($options = array()) { 

    //keep parent logic
    if (!parent::beforeSave($options)) {
      return false;
    }


    if (!empty($this->data['GroupNameValidator']['status']) && 
         $this->data['GroupNameValidator']['status'] === SuspendableStatusEnum::Active) {

      $coId = $this->data['GroupNameValidator']['co_id'];

      // exclude the current id if we are editing
      $excludeId = !empty($this->data['GroupNameValidator']['id'])
        ? $this->data['GroupNameValidator']['id']
        : null;

      $args = array();
      $conditions['co_id'] = $coId;
      $conditions['GroupNameValidator.status'] = SuspendableStatusEnum::Active;
      if ($excludeId) {
        $conditions['GroupNameValidator.id !='] = $excludeId;
      }

      //Suspend all other Active rows
      $this->updateAll(
        array('GroupNameValidator.status' => "'" . SuspendableStatusEnum::Suspended . "'"),
        $conditions
      );
    }
    return true;
  } 

} 
