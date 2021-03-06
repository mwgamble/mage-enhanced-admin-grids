<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   BL
 * @package    BL_CustomGrid
 * @copyright  Copyright (c) 2014 Benoît Leulliette <benoit.leulliette@gmail.com>
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class BL_CustomGrid_Model_Grid extends Mage_Core_Model_Abstract
{
    /**
     * Session keys
     */
    const SESSION_BASE_KEY_CURRENT_PROFILE = '_blcg_current_profile_';
    
    /**
     * Attribute columns base keys
     */
    const ATTRIBUTE_COLUMN_ID_PREFIX  = '_blcg_attribute_column_';
    const ATTRIBUTE_COLUMN_GRID_ALIAS = 'blcg_attribute_field_';
    
    /**
     * Custom columns base keys
     */
    const CUSTOM_COLUMN_ID_PREFIX  = '_blcg_custom_column_';
    const CUSTOM_COLUMN_GRID_ALIAS = 'blcg_custom_field_';
    
    /**
     * Pitch to put between each column order at initialization
     * 
     * @var int
     */
    const COLUMNS_ORDER_PITCH = 10;
    
    /**
     * Default pagination values (usually hard-coded in grid template)
     * 
     * @var int[]
     */
    static protected $_defaultPaginationValues = array(20, 30, 50, 100, 200);
    
    /**
     * Grid parameters base keys
     */
    const GRID_PARAM_NONE   = 'none';
    const GRID_PARAM_PAGE   = 'page';
    const GRID_PARAM_LIMIT  = 'limit';
    const GRID_PARAM_SORT   = 'sort';
    const GRID_PARAM_DIR    = 'dir';
    const GRID_PARAM_FILTER = 'filter';
    
    /**
     * Grid parameters base keys
     * 
     * @var string[]
     */
    static protected $_gridParamsKeys = array(
        self::GRID_PARAM_PAGE,
        self::GRID_PARAM_LIMIT,
        self::GRID_PARAM_SORT,
        self::GRID_PARAM_DIR,
        self::GRID_PARAM_FILTER,
    );
    
    /**
     * Grid actions (used to set and check permissions)
     */
    const ACTION_CUSTOMIZE_COLUMNS                  = 'customize';
    const ACTION_USE_CUSTOMIZED_COLUMNS             = 'use_customized';
    const ACTION_VIEW_GRID_INFOS                    = 'view_infos';
    const ACTION_CHOOSE_EDITABLE_COLUMNS            = 'choose_editable';
    const ACTION_EDIT_COLUMNS_VALUES                = 'edit_values';
    const ACTION_EDIT_DEFAULT_PARAMS                = 'edit_default';
    const ACTION_USE_DEFAULT_PARAMS                 = 'use_default';
    const ACTION_EXPORT_RESULTS                     = 'export';
    const ACTION_ENABLE_DISABLE                     = 'enable_disable';
    const ACTION_EDIT_FORCED_TYPE                   = 'edit_forced_type';
    const ACTION_EDIT_CUSTOMIZATION_PARAMS          = 'edit_customization_params';
    const ACTION_EDIT_DEFAULT_PARAMS_BEHAVIOURS     = 'edit_default_params_behaviours';
    const ACTION_EDIT_ROLES_PERMISSIONS             = 'edit_roles_permissions';
    const ACTION_DELETE                             = 'delete';
    const ACTION_ACCESS_ALL_PROFILES                = 'access_all_profiles';
    const ACTION_CHOOSE_OWN_USER_DEFAULT_PROFILE    = 'choose_own_user_default_profile';
    const ACTION_CHOOSE_OTHER_USERS_DEFAULT_PROFILE = 'choose_other_users_default_profile';
    const ACTION_CHOOSE_OWN_ROLE_DEFAULT_PROFILE    = 'choose_own_role_default_profile';
    const ACTION_CHOOSE_OTHER_ROLES_DEFAULT_PROFILE = 'choose_other_roles_default_profile';
    const ACTION_CHOOSE_GLOBAL_DEFAULT_PROFILE      = 'choose_global_default_profile';
    const ACTION_COPY_PROFILES_TO_NEW               = 'copy_profiles_to_new';
    const ACTION_COPY_PROFILES_TO_EXISTING          = 'copy_profiles_to_existing';
    const ACTION_EDIT_PROFILES                      = 'edit_profiles';
    const ACTION_ASSIGN_PROFILES                    = 'assign_profiles';
    const ACTION_DELETE_PROFILES                    = 'delete_profiles';
    
    /**
     * Grid actions options hash
     * 
     * @var string[]
     */
    static protected $_gridActions = null;
    
    /**
     * Grouped grid actions options hashes
     * 
     * @var string[]
     */
    static protected $_groupedGridActions = null;
    
    /**
     * Grids actions corresponding paths in the ACL configuration
     * 
     * @var string[]
     */
    static protected $_gridActionsAclPaths = array(
        self::ACTION_CUSTOMIZE_COLUMNS                  => 'customgrid/customization/edit_columns',
        self::ACTION_USE_CUSTOMIZED_COLUMNS             => 'customgrid/customization/use_columns',
        self::ACTION_VIEW_GRID_INFOS                    => 'customgrid/customization/view_grid_infos',
        self::ACTION_EDIT_DEFAULT_PARAMS                => 'customgrid/customization/edit_default_params',
        self::ACTION_USE_DEFAULT_PARAMS                 => 'customgrid/customization/use_default_params',
        self::ACTION_EXPORT_RESULTS                     => 'customgrid/customization/export_results',
        self::ACTION_CHOOSE_EDITABLE_COLUMNS            => 'customgrid/editor/choose_columns',
        self::ACTION_EDIT_COLUMNS_VALUES                => 'customgrid/editor/edit_columns',
        self::ACTION_ENABLE_DISABLE                     => 'customgrid/administration/enable_disable',
        self::ACTION_EDIT_FORCED_TYPE                   => 'customgrid/administration/edit_forced_type',
        self::ACTION_EDIT_CUSTOMIZATION_PARAMS          => 'customgrid/administration/edit_customization_params',
        self::ACTION_EDIT_DEFAULT_PARAMS_BEHAVIOURS     => 'customgrid/administration/edit_default_params_behaviours',
        self::ACTION_EDIT_ROLES_PERMISSIONS             => 'customgrid/administration/edit_roles_permissions',
        self::ACTION_DELETE                             => 'customgrid/administration/delete',
        self::ACTION_ACCESS_ALL_PROFILES                => 'customgrid/profiles/access_all',
        self::ACTION_CHOOSE_OWN_USER_DEFAULT_PROFILE    => 'customgrid/profiles/choose_own_user_default',
        self::ACTION_CHOOSE_OTHER_USERS_DEFAULT_PROFILE => 'customgrid/profiles/choose_other_users_default',
        self::ACTION_CHOOSE_OWN_ROLE_DEFAULT_PROFILE    => 'customgrid/profiles/choose_own_role_default',
        self::ACTION_CHOOSE_OTHER_ROLES_DEFAULT_PROFILE => 'customgrid/profiles/choose_other_roles_default',
        self::ACTION_CHOOSE_GLOBAL_DEFAULT_PROFILE      => 'customgrid/profiles/choose_global_default',
        self::ACTION_COPY_PROFILES_TO_NEW               => 'customgrid/profiles/copy_to_new',
        self::ACTION_COPY_PROFILES_TO_EXISTING          => 'customgrid/profiles/copy_to_existing',
        self::ACTION_EDIT_PROFILES                      => 'customgrid/profiles/edit',
        self::ACTION_ASSIGN_PROFILES                    => 'customgrid/profiles/assign',
        self::ACTION_DELETE_PROFILES                    => 'customgrid/profiles/delete',
    );
    
    /**
     * Grid actions permissions flags
     */
    const ACTION_PERMISSION_USE_CONFIG = '0';
    const ACTION_PERMISSION_YES        = '1';
    const ACTION_PERMISSION_NO         = '2';
    
    /**
     * Default parameters behaviours
     */
    const DEFAULT_PARAM_DEFAULT             = 'default';
    const DEFAULT_PARAM_FORCE_ORIGINAL      = 'force_original';
    const DEFAULT_PARAM_FORCE_CUSTOM        = 'force_custom';
    const DEFAULT_PARAM_MERGE_DEFAULT       = 'merge_default'; 
    const DEFAULT_PARAM_MERGE_BASE_ORIGINAL = 'merge_on_original';
    const DEFAULT_PARAM_MERGE_BASE_CUSTOM   = 'merge_on_custom';
    
    protected function _construct()
    {
        parent::_construct();
        $this->_init('customgrid/grid');
        $this->setIdFieldName('grid_id');
    }
    
    /**
     * Return the absorber model usable to initialize/update the grid model values from a grid block
     * 
     * @return BL_CustomGrid_Model_Grid_Absorber
     */
    public function getAbsorber()
    {
        return $this->getDataSetDefault(
            'absorber',
            Mage::getModel('customgrid/grid_absorber')->setGridModel($this)
        );
    }
    
    /**
     * Return the applier model usable to apply the grid model values to a grid block
     * 
     * @return BL_CustomGrid_Model_Grid_Applier
     */
    public function getApplier()
    {
        return $this->getDataSetDefault(
            'applier',
            Mage::getModel('customgrid/grid_applier')->setGridModel($this)
        );
    }
    
    /**
     * Return the exporter model usable to export the grid results
     * 
     * @return BL_CustomGrid_Model_Grid_Exporter
     */
    public function getExporter()
    {
        return $this->getDataSetDefault(
            'exporter',
            Mage::getModel('customgrid/grid_exporter')->setGridModel($this)
        );
    }
    
    /**
     * Throw a permission-related exception
     * 
     * @param string|null $message Custom exception message
     */
    public function throwPermissionException($message = null)
    {
        $message = (is_null($message) ? $this->getHelper()->__('You are not allowed to use this action') : $message);
        throw new BL_CustomGrid_Grid_Permission_Exception($message); 
    }
    
    /**
     * Return base helper
     * 
     * @return BL_CustomGrid_Helper_Data
     */
    public function getHelper()
    {
        return Mage::helper('customgrid');
    }
    
    /**
     * Return config helper
     * 
     * @return BL_CustomGrid_Helper_Config
     */
    public function getConfigHelper()
    {
        return Mage::helper('customgrid/config');
    }
    
    /**
     * Return admin session model
     * 
     * @return Mage_Admin_Model_Session
     */
    public function getAdminSession()
    {
        return Mage::getSingleton('admin/session');
    }
    
    /**
     * Return adminhtml session model
     * 
     * @return Mage_Adminhtml_Model_Session
     */
    public function getAdminhtmlSession()
    {
        return Mage::getSingleton('adminhtml/session');
    }
    
    /**
     * Return our own session model
     * 
     * @return BL_CustomGrid_Model_Session
     */
    public function getBlcgSession()
    {
        return Mage::getSingleton('customgrid/session');
    }
    
    /**
     * Return currently logged-in user
     * 
     * @return Mage_Admin_Model_User|null
     */
    public function getSessionUser()
    {
        /** @var $user Mage_Admin_Model_User */
        $user = $this->getAdminSession()->getUser();
        return ($user && $user->getId() ? $user : null);
    }
    
    /**
     * Return the role of the currently logged-in user
     * 
     * @return Mage_Admin_Model_Role|null
     */
    public function getSessionRole()
    {
        return (($user = $this->getSessionUser()) ? $user->getRole() : null);
    }
    
    /**
     * Reset given data keys
     * 
     * @param array $keys Data keys to reset
     * @return this
     */
    protected function _resetKeys(array $keys)
    {
        foreach ($keys as $key) {
            $this->unsetData($key);
        }
        return $this;
    }
    
    /**
     * Reset data keys associated to grid type values
     *
     * @return this
     */
    public function resetTypeValues()
    {
        return $this->_resetKeys(array('type_code', 'type_model', 'base_type_model'));
    }
    
    /**
     * Reset data keys associated to columns values
     *
     * @return this
     */
    public function resetColumnsValues()
    {
        return $this->_resetKeys(array('columns', 'max_order', 'origin_ids', 'appliable_default_filter'));
    }
    
    /**
     * Reset data keys associated to users config values
     *
     * @return this
     */
    public function resetUsersConfigValues()
    {
        return $this->_resetKeys(array('users_config'));
    }
    
    /**
     * Reset data keys associated to roles config values
     *
     * @return this
     */
    public function resetRolesConfigValues()
    {
        return $this->_resetKeys(array('roles_config'));
    }
    
    /**
     * Reset data keys associated to profiles values
     *
     * @return this
     */
    public function resetProfilesValues()
    {
        $this->resetAvailableProfilesValues();
        return $this->_resetKeys(array('profiles', 'profile_id'));
    }
    
    /**
     * Reset data keys associated to available profiles
     * 
     * @return this
     */
    public function resetAvailableProfilesValues()
    {
        return $this->_resetKeys(array('available_profiles_ids'));
    }
    
    /**
     * Reset all data keys associated to sub values
     *
     * @return this
     */
    public function resetSubValues()
    {
        $this->resetTypeValues();
        $this->resetColumnsValues();
        $this->resetRolesConfigValues();
        $this->resetUsersConfigValues();
        $this->resetProfilesValues();
        return $this;
    }
    
    /**
     * Reset data before load
     * 
     * @param mixed $id
     * @param mixed $field
     * @return this
     */
    protected function _beforeLoad($id, $field = null)
    {
        $this->setData(array());
        return parent::_beforeLoad($id, $field);
    }
    
    /**
     * Set default values to uninitialized data keys before save
     *
     * @return this
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        $this->getDataSetDefault('max_attribute_column_base_block_id', 0);
        $this->getDataSetDefault('max_custom_column_base_block_id', 0);
        return $this;
    }
    
    /**
     * Reset all sub values after save
     *
     * @return this
     */
    protected function _afterSave()
    {
        parent::_afterSave();
        $this->resetSubValues();
        return $this;
    }
    
    /**
     * Enforce corresponding user permission before delete
     *
     * @return this
     */
    protected function _beforeDelete()
    {
        return !$this->checkUserPermissions(self::ACTION_DELETE)
            ? $this->throwPermissionException()
            : parent::_beforeDelete();
    }
    
    /**
     * Set grid block type
     *
     * @param string $blockType Grid block type (eg: "adminhtml/catalog_product_grid")
     * @return this
     */
    public function setBlockType($blockType)
    {
        if ($blockType != $this->getBlockType()) {
            // Reset type model if the block type has changed
            $this->resetTypeValues();
            $this->setData('block_type', $blockType);
        }
        return $this;
    }
    
    /**
     * Disable / enable the grid
     * 
     * @param bool $disabled Whether the grid is disabled or not
     * @return this
     */
    public function setDisabled($disabled)
    {
        return !$this->checkUserPermissions(self::ACTION_ENABLE_DISABLE)
            ? $this->throwPermissionException()
            : $this->setData('disabled', (bool) $disabled);
    }
    
    /**
     * Return grid parameters base keys
     * 
     * @param bool $withNone Whether "None" option should be included
     * @return string[]
     */
    public function getGridParamsKeys($withNone = false)
    {
        $keys = self::$_gridParamsKeys;
        
        if ($withNone) {
            array_unshift($keys, self::GRID_PARAM_NONE);
        }
        
        return $keys;
    }
    
    /**
     * Return the keys corresponding to the variable names used by grid blocks
     * 
     * @return string[]
     */
    public function getBlockVarNameKeys()
    {
        return $this->getGridParamsKeys();
    }
    
    /**
     * Return the default variable names used by grid blocks
     *
     * @return string[]
     */
    public function getBlockVarNameDefaults()
    {
        return array(
            self::GRID_PARAM_PAGE   => 'page',
            self::GRID_PARAM_LIMIT  => 'limit',
            self::GRID_PARAM_SORT   => 'sort',
            self::GRID_PARAM_DIR    => 'dir',
            self::GRID_PARAM_FILTER => 'filter',
        );
    }
    
    /**
     * Return block variable name
     *
     * @param string $key Variable key
     * @return string
     */
    public function getBlockVarName($key)
    {
        $defaults = $this->getBlockVarNameDefaults();
        return (!isset($defaults[$key]) ? null : $this->getDataSetDefault('var_name_' . $key, $defaults[$key]));
    }
    
    /**
     * Return block variable names
     *
     * @return string[]
     */
    public function getBlockVarNames()
    {
        $varNames = array();
        
        foreach ($this->getBlockVarNameKeys() as $key) {
            $varNames[$key] = $this->getBlockVarName($key);
        }
        
        return $varNames;
    }
    
    /**
     * Return grid block session key for given parameter
     *
     * @param string $param Grid block parameter (should correspond to variable names)
     * @return string|null
     */
    public function getBlockParamSessionKey($param)
    {
        /**
         * Note: some grids may have a dynamic ID, but as it should be based in those cases on uniqHash(),
         * returning an old ID should not imply any potential conflict with any other ID
         */
        return (($blockId = $this->_getData('block_id')) ? $blockId . $param : null); 
    }
    
    /**
     *  Return grid type model
     *
     * @return BL_CustomGrid_Model_Grid_Type_Abstract|null
     */
    public function getTypeModel()
    {
        if (!$this->hasData('type_model')) {
            if ($blockType = $this->_getData('block_type')) {
                $rewritingClassName = $this->_getData('rewriting_class_name');
                $typeModels = Mage::getSingleton('customgrid/grid_type_config')->getTypesInstances();
                
                foreach ($typeModels as $code => $typeModel) {
                    if ($typeModel->isAppliableToGridBlock($blockType, $rewritingClassName)) {
                        $this->addData(
                            array(
                                'type_code'  => $code,
                                'type_model' => $typeModel,
                                'base_type_model' => $typeModel,
                            )
                        );
                        break;
                    }
                }
                
                if (($forcedTypeCode = $this->_getData('forced_type_code'))
                    && isset($typeModels[$forcedTypeCode])) {
                    $this->setData('type_model', $typeModels[$forcedTypeCode]);
                } 
            } else {
                $this->unsetData('type_code');
            }
        }
        return $this->_getData('type_model');
    }
    
    /**
     * Return active type model name, or given default value if the grid has no base type nor forced type
     *
     * @param string $default Default value
     * @return string
     */
    public function getTypeModelName($default = '')
    {
        return (($typeModel = $this->getTypeModel()) ? $typeModel->getName() : $default);
    }
    
    /**
     * Return base type model name, or given default value if the grid has no base type
     *
     * @param string $default Default value
     * @return string
     */
    public function getBaseTypeModelName($default = '')
    {
        return ($this->getTypeModel() && ($typeModel = $this->getBaseTypeModel()) ? $typeModel->getName() : $default);
    }
    
    /**
     * Update the forced grid type
     * 
     * @param string $forcedTypeCode Code of the grid type to force
     * @return this
     */
    public function updateForcedType($forcedTypeCode)
    {
        if (!$this->checkUserPermissions(self::ACTION_EDIT_FORCED_TYPE)) {
            $this->throwPermissionException();
        }
        
        $helper = $this->getHelper();
        
        if (!empty($forcedTypeCode)) {
            $typeModels = Mage::getSingleton('customgrid/grid_type_config')->getTypesInstances();
            
            if (!isset($typeModels[$forcedTypeCode])) {
                Mage::throwException($helper->__('The forced grid type does not exist'));
            }
        } else {
            $forcedTypeCode = null;
        }
        
        $this->resetTypeValues();
        $this->setForcedTypeCode($forcedTypeCode);
        
        return $this;
    }
    
    /**
     * Set profiles
     * 
     * @param array $profiles Grid profiles
     * @return this
     */
    public function setProfiles(array $profiles)
    {
        $this->resetColumnsValues();
        $this->resetProfilesValues();
        
        foreach ($profiles as $key => $profile) {
            if (is_array($profile)) {
                $profiles[$key] = Mage::getModel('customgrid/grid_profile', $profile);
            }
            if (!is_object($profiles[$key])) {
                unset($profiles[$key]);
                continue;
            }
            $profiles[$key]->setData('grid_model', $this);
        }
        
        return $this->setData('profiles', $profiles);
    }
    
    /**
     * Return all profiles
     *
     * @return BL_CustomGrid_Model_Grid_Profile[]
     */
    protected function _getProfiles()
    {
        if (!$this->hasData('profiles')) {
            $profiles = (($id = $this->getId()) ? $this->_getResource()->getGridProfiles($id) : array());
            $this->setProfiles($profiles);
        }
        return $this->_getData('profiles');
    }
    
    /**
     * Profiles sort callback
     *
     * @param BL_CustomGrid_Model_Grid_Profile $a One profile
     * @param BL_CustomGrid_Model_Grid_Profile $b Another profile
     * @return int
     */
    protected function _sortProfiles($profileA, $profileB)
    {
        return $profileA->isBase()
            ? -1
            : ($profileB->isBase() ? 1 : strcasecmp($profileA->getName(), $profileB->getName()));
    }
    
    /**
     * Return profiles list
     *
     * @param bool $onlyAvailable Whether only the profiles available to the current user should be returned
     * @param bool $sorted Whether profiles should be sorted
     * @return BL_CustomGrid_Model_Grid_Profile[]
     */
    public function getProfiles($onlyAvailable = false, $sorted = false)
    {
        $profiles = $this->_getProfiles();
        
        if ($onlyAvailable && !empty($profiles)) {
            $baseProfile = (($baseProfileId = $this->getBaseProfileId()) ? $profiles[$baseProfileId] : null);
            $profiles = array_intersect_key($profiles, array_flip($this->getAvailableProfilesIds()));
            
            if (empty($profiles) && $baseProfileId) {
                $profiles[$baseProfileId] = $baseProfile;
            }
        }
        if ($sorted) {
            uasort($profiles, array($this, '_sortProfiles'));
        }
        
        return $profiles;
    }
    
    /**
     * Return the ID of the base profile
     *
     * @return int|null
     */
    public function getBaseProfileId()
    {
        return (!is_null($profileId = $this->_getData('base_profile_id')) ? (int) $profileId : null);
    }
    
    /**
     * Return IDs of the profiles assigned to the given role
     *
     * @param int|null $roleId Role ID (if null, the role of the current user will be used)
     * @return int[]
     */
    public function getRoleAssignedProfilesIds($roleId = null)
    {
        $assignedProfilesIds = array();
        
        if (is_null($roleId)) {
            $roleId = (($role = $this->getSessionRole()) ? $role->getId() : null);
        }
        if (!is_null($roleId) && ($roleConfig = $this->getRoleConfig($roleId))) {
            $assignedProfilesIds = $roleConfig->getDataSetDefault('assigned_profiles_ids', array());
        }
        
        return $assignedProfilesIds;
    }
    
    /**
     * Return available profiles IDs
     * 
     * @return int[]
     */
    public function getAvailableProfilesIds()
    {
        if (!$this->hasData('available_profiles_ids')) {
            $profiles = $this->_getProfiles();
            
            if (!$this->checkUserPermissions(self::ACTION_ACCESS_ALL_PROFILES)) {
                foreach ($profiles as $key => $profile) {
                    if (!$profile->isAvailable()) {
                        unset($profiles[$key]);
                    }
                }
            }
            
            $this->setData('available_profiles_ids', array_keys($profiles));
        }
        return $this->_getData('available_profiles_ids');
    }
    
    /**
     * Return whether given profile ID is available for the current user
     * 
     * @param int $profileId Profile Id
     * @return bool
     */
    public function isAvailableProfile($profileId)
    {
        return in_array($profileId, $this->getAvailableProfilesIds(), true);
    }
    
    /**
     * Handle a permanent profile change, by restoring and/or remembering the necessary session values
     * 
     * @param BL_CustomGrid_Model_Grid_Profile $newProfile New permanent profile
     * @param BL_CustomGrid_Model_Grid_Profile $previousProfile Previous permanent profile (if it still exists)
     * @return this
     */
    protected function _handlePermanentProfileChange(
        BL_CustomGrid_Model_Grid_Profile $newProfile,
        BL_CustomGrid_Model_Grid_Profile $previousProfile = null
    ) {
        $session = $this->getAdminhtmlSession();
        $rememberedValues   = $newProfile->getRememberedSessionValues();
        $rememberableValues = array();
        $rememberableParams = ($previousProfile ? $previousProfile->getRememberedSessionParams() : array());
        $isFilterRememberable = false;
        
        foreach ($this->getBlockVarNames() as $gridParam => $varName) {
            $isRememberedValue = isset($rememberedValues[$gridParam]);
            
            if ($sessionKey = $this->getBlockParamSessionKey($varName)) {
                if (in_array($gridParam, $rememberableParams) && $session->hasData($sessionKey)) {
                    $rememberableValues[$gridParam] = $session->getData($sessionKey);
                    $isFilterRememberable = ($varName == self::GRID_PARAM_FILTER);
                }
                if ($isRememberedValue) {
                    $session->setData($sessionKey, $rememberedValues[$gridParam]);
                } else {
                    $session->unsetData($sessionKey);
                }
            }
        }
        
        if ($previousProfile) {
            $previousProfile->setRememberableSessionValues($rememberableValues);
            
            if ($isFilterRememberable) {
                // Ensure that the next filters verification won't mess with the default filters
                // when switching back to the previous profile
                $previousProfile->setSessionAppliedFilters(array());
                $previousProfile->setSessionRemovedFilters(array());
            }
        }
        
        return $this;
    }
    
    /**
     * Set the new current/permanent profile ID
     * 
     * @param int $profileId New current/permanent profile ID
     * @return this
     */
    protected function _setPermanentProfileId($profileId)
    {
        $profiles = $this->_getProfiles();
        $session  = $this->getAdminhtmlSession();
        $sessionKey = $this->_getSessionProfileIdKey();
        
        if ($session->hasData($sessionKey)) {
            $previousProfileId = $this->getSessionProfileId();
            $session->setData($sessionKey, $profileId);
            
            if ($profileId !== $previousProfileId) {
                $this->_handlePermanentProfileChange(
                    $profiles[$profileId],
                    (isset($profiles[$previousProfileId]) ? $profiles[$previousProfileId] : null)
                );
            }
        } else {
            $session->setData($sessionKey, $profileId);
        }
        
        return $this;
    }
    
    /**
     * Set the new current profile ID, either for temporary or "permanent" use
     *
     * @param int $profileId New current profile Id
     * @param bool $temporary Whether the profile ID should only be set temporary (= not in session / no session check)
     * @param bool $forced Whether the given profile ID is "forced" (ie, was not determined automatically)
     * @return this
     */
    public function setProfileId($profileId, $temporary = false, $forced = true)
    {
        $profileId = (int) $profileId;
        $profiles  = $this->getProfiles(true);
        
        if (!isset($profiles[$profileId])) {
            Mage::throwException($this->getHelper()->__('This profile is not available'));
        }
        
        $this->resetColumnsValues();
        $this->setData('profile_id', $profileId);
        
        if (!$temporary) {
            $this->_setPermanentProfileId($profileId);
        }
        
        return $this;
    }
    
    /**
     * Return session key corresponding to the current profile ID
     *
     * @return string
     */
    protected function _getSessionProfileIdKey()
    {
        return self::SESSION_BASE_KEY_CURRENT_PROFILE . '_' . $this->getId();
    }
    
    /**
     * Return the current profile ID in session
     *
     * @return int|null
     */
    public function getSessionProfileId()
    {
        $profileId = $this->getAdminhtmlSession()->getData($this->_getSessionProfileIdKey());
        
        if (!is_null($profileId)) {
            $profileId = (int) $profileId;
        }
        
        return $profileId;
    }
    
    /**
     * Return default profile ID for given user
     *
     * @param int|null $userId User ID (if null, the current user will be used)
     * @return int|null
     */
    public function getUserDefaultProfileId($userId = null)
    {
        $defaultProfileId = null;
        
        if (is_null($userId)) {
            $userId = (($user = $this->getSessionUser()) ? $user->getId() : null);
        }
        if (!is_null($userId) && ($userConfig = $this->getUserConfig($userId))) {
            $defaultProfileId = $userConfig->getData('default_profile_id');
        }
        
        return $defaultProfileId;
    }
    
    /**
     * Return default profile ID for given role
     *
     * @param int|null $roleId Role ID (if null, the role of the current user will be used)
     * @return int|null
     */
    public function getRoleDefaultProfileId($roleId = null)
    {
        $defaultProfileId = null;
        
        if (is_null($roleId)) {
            $roleId = (($role = $this->getSessionRole()) ? $role->getId() : null);
        }
        if (!is_null($roleId) && ($roleConfig = $this->getRoleConfig($roleId))) {
            $defaultProfileId = $roleConfig->getData('default_profile_id');
        }
        
        return $defaultProfileId;
    }
    
    /**
     * Return the global default profile ID (which can not be the base profile)
     *
     * @return int|null
     */
    public function getGlobalDefaultProfileId()
    {
        return (!is_null($profileId = $this->_getData('global_default_profile_id')) ? (int) $profileId : null);
    }
    
    /**
     * Return the current profile ID
     *
     * @return int
     */
    public function getProfileId()
    {
        if (!$this->hasData('profile_id')) {
            if (!$this->getId()) {
                return null;
            } else {
                $profiles  = $this->getProfiles(true);
                $profileId = key($profiles);
                $sessionProfileId = $this->getSessionProfileId();
                
                $defaultProfilesIds = array(
                    $sessionProfileId,
                    $this->getUserDefaultProfileId(),
                    $this->getRoleDefaultProfileId(),
                    $this->getGlobalDefaultProfileId(),
                    $this->getBaseProfileId(),
                );
                
                foreach ($defaultProfilesIds as $defaultProfileId) {
                    if (!is_null($defaultProfileId) && isset($profiles[$defaultProfileId])) {
                        $profileId = (int) $defaultProfileId;
                        break;
                    }
                }
                
                if (is_null($profileId)) {
                    Mage::throwException($this->getHelper()->__('There is not any available profile'));
                }
                if (is_int($sessionProfileId) && ($sessionProfileId !== $profileId)) {
                    $this->getBlcgSession()
                        ->addNotice($this->getHelper()->__('The previous profile is not available anymore'));
                }
                
                $this->setProfileId($profileId, false, false);
            }
        }
        return $this->_getData('profile_id');
    }
    
    /**
     * Return a profile by its ID
     *
     * @param int|null $profileId Profile ID (if not set, current profile ID will be used)
     * @return BL_CustomGrid_Model_Grid_Profile
     */
    public function getProfile($profileId = null)
    {
        $profiles = $this->getProfiles(true);
        
        if (is_null($profileId)) {
            $profileId = $this->getProfileId();
        }
        if (!isset($profiles[$profileId])) {
            Mage::throwException($this->getHelper()->__('This profile is not available'));
        }
        
        return $profiles[$profileId];
    }
    
    /**
     * Return whether profiles created by users who do not have the permission to assign profiles
     * should be restricted by default
     * 
     * @return bool
     */
    public function getProfilesDefaultRestricted()
    {
        return is_null($value = $this->_getData('profiles_default_restricted'))
            ? $this->getConfigHelper()->getProfilesDefaultRestricted()
            : (bool) $value;
    }
    
    /**
     * Return the roles IDs to which should be assigned the profiles created by users
     * who do not have the permission to do so
     * 
     * @return int[]
     */
    public function getProfilesDefaultAssignedTo()
    {
        return is_null($value = $this->_getData('profiles_default_assigned_to'))
            ? $this->getConfigHelper()->getProfilesDefaultAssignedTo()
            : $this->getHelper()->parseCsvIntArray($value, true, false, 1);
    }
    
    /**
     * Return the session parameters that should be restored upon returning to a profile previously used during
     * the same session
     * 
     * @return string[]
     */
    public function getProfilesRememberedSessionParams()
    {
        return is_null($value = $this->_getData('profiles_remembered_session_params'))
            ? $this->getConfigHelper()->getProfilesRememberedSessionParams()
            : explode(',', $value);
    }
    
    /**
     * Update profiles default values corresponding to assignation values
     * 
     * @param array $defaults New profiles default values
     * @return this
     */
    protected function _updateProfilesAssignationDefaults(array $defaults)
    {
        if ($this->checkUserPermissions(self::ACTION_ASSIGN_PROFILES)) {
            if (isset($defaults['restricted']) && ($defaults['restricted'] !== '')) {
                $this->setData('profiles_default_restricted', (bool) $defaults['restricted']);
            } else {
                $this->setData('profiles_default_restricted', null);
            }
            if (isset($defaults['assigned_to']) && is_array($defaults['assigned_to'])) {
                $this->setData('profiles_default_assigned_to', implode(',', $defaults['assigned_to']));
            } else {
                $this->setData('profiles_default_assigned_to', null);
            }
        } elseif (isset($defaults['restricted']) || isset($defaults['assigned_to'])) {
            $this->throwPermissionException();
        }
        return $this;
    }
    
    /**
     * Update profiles default values corresponding to base values
     * 
     * @param array $defaults New profiles default values
     * @return this
     */
    protected function _updateProfilesBaseDefaults(array $defaults)
    {
        if ($this->checkUserPermissions(self::ACTION_EDIT_PROFILES)) {
            $sessionParams = null;
            
            if (isset($defaults['remembered_session_params']) && is_array($defaults['remembered_session_params'])) {
                $sessionParams = array_intersect(
                    $defaults['remembered_session_params'],
                    $this->getGridParamsKeys(true)
                );
                
                if (in_array(self::GRID_PARAM_NONE, $sessionParams)) {
                    $sessionParams = array(self::GRID_PARAM_NONE);
                }
            }
            
            $this->setData(
                'profiles_remembered_session_params',
                (empty($sessionParams) ? null : implode(',', $sessionParams))
            );
        } elseif (isset($defaults['remembered_session_params'])) {
            $this->throwPermissionException();
        }
        return $this;
    }
    
    /**
     * Update profiles default values
     * 
     * @param array $defaults New profiles default values
     * @return this
     */
    public function updateProfilesDefaults(array $defaults)
    {
        $this->_updateProfilesAssignationDefaults($defaults);
        $this->_updateProfilesBaseDefaults($defaults);
        return $this->setDataChanges(true);
    }
    
    /**
     * Return column block IDs by column origin
     *
     * @return string[]
     */
    protected function _getColumnBlockIdsByOrigin()
    {
        return $this->getDataSetDefault(
            'column_block_ids_by_origin',
            array(
                BL_CustomGrid_Model_Grid_Column::ORIGIN_GRID       => array(),
                BL_CustomGrid_Model_Grid_Column::ORIGIN_COLLECTION => array(),
                BL_CustomGrid_Model_Grid_Column::ORIGIN_ATTRIBUTE  => array(),
                BL_CustomGrid_Model_Grid_Column::ORIGIN_CUSTOM     => array(),
            )
        );
    }
    
    /**
     * Return column block IDs by column origin
     *
     * @param string $origin If specified, only the column block IDs from this origin will be returned
     * @return string[]
     */
    public function getColumnBlockIdsByOrigin($origin = null)
    {
        $originIds = $this->_getColumnBlockIdsByOrigin();
        return (is_null($origin) ? $originIds : (isset($originIds[$origin]) ? $originIds[$origin] : array()));
    }
    
    /**
     * Return default interval between two columns order values
     *
     * @return int
     */
    public function getColumnsOrderPitch()
    {
        return self::COLUMNS_ORDER_PITCH;
    }
    
    /**
     * Return the maximum order value amongst all columns
     *
     * @return int
     */
    public function getColumnsMaxOrder()
    {
        return $this->getDataSetDefault('columns_max_order', 0);
    }
    
    /**
     * Recompute columns maximum order
     *
     * @var int $newOrder If set, the new maximum order will only be computed from the current value and the given one
     * @return this
     */
    protected function _recomputeColumnsMaxOrder($newOrder = null)
    {
        if (is_null($newOrder)) {
            $maxOrder = ~PHP_INT_MAX;
            
            foreach ($this->getColumns() as $column) {
                $maxOrder = max($maxOrder, $column->getOrder());
            }
            
            $this->setData('columns_max_order', $maxOrder);
        } else {
            $this->setData('columns_max_order', max($this->getColumnsMaxOrder(), $newOrder));
        }
        return $this;
    }
    
    /**
     * Increase maximum order by the order pitch and return the new value
     *
     * @return int
     */
    public function getNextColumnOrder()
    {
        $this->setData('columns_max_order', $this->getColumnsMaxOrder() + $this->getColumnsOrderPitch());
        return $this->getColumnsMaxOrder();
    }
    
    /**
     * Add a column to the columns list
     *
     * @param array $data Column values
     * @return this
     */
    public function addColumn(array $data)
    {
        $this->getColumns();
        $this->getColumnBlockIdsByOrigin();
        $data['grid_model'] = $this;
        $blockId = $data['block_id'];
        $this->_data['columns'][$blockId] = Mage::getModel('customgrid/grid_column', $data);
        $this->_data['column_block_ids_by_origin'][$data['origin']][] = $blockId;
        $this->_recomputeColumnsMaxOrder($data['order']);
        $this->setDataChanges(true);
        return $this;
    }
    
    /**
     * Update a column from the columns list
     * 
     * @param string $columnBlockId Column block ID
     * @param array $data New column values
     * @return this
     */
    public function updateColumn($columnBlockId, array $data)
    {
        if ($column = $this->getColumnByBlockId($columnBlockId)) {
            $previousOrigin = $column->getOrigin();
            $column->addData($data);
            
            if (isset($data['origin']) && ($data['origin'] != $previousOrigin)) {
                $this->getColumnBlockIdsByOrigin();
                
                $previousKey = array_search(
                    $columnBlockId,
                    $this->_data['column_block_ids_by_origin'][$previousOrigin]
                );
                
                if ($previousKey !== false) {
                    unset($this->_data['column_block_ids_by_origin'][$previousOrigin][$previousKey]);
                }
                
                $this->_data['column_block_ids_by_origin'][$data['origin']][] = $columnBlockId;
            }
            if (isset($data['order'])) {
                $this->_recomputeColumnsMaxOrder($data['order']);
            }
            
            $this->setDataChanges(true);
        }
        return $this;
    }
    
    /**
     * Remove a column from the columns list
     * 
     * @param string $columnBlockId Column block ID
     * @return this
     */
    public function removeColumn($columnBlockId)
    {
        if ($column = $this->getColumnByBlockId($columnBlockId)) {
            $this->getColumnBlockIdsByOrigin();
            unset($this->_data['columns'][$columnBlockId]);
            
            $origin = $column->getOrigin();
            $originKey = array_search($columnBlockId, $this->_data['column_block_ids_by_origin'][$origin]);
            
            if ($originKey !== false) {
                unset($this->_data['column_block_ids_by_origin'][$origin][$originKey]);
            }
            
            $this->_recomputeColumnsMaxOrder();
            $this->setDataChanges(true);
        }
        return $this;
    }
    
    /**
     * Set columns
     *
     * @param array $columns Grid columns
     * @return this
     */
    public function setColumns(array $columns)
    {
        $this->resetColumnsValues();
        $this->setData('columns', array());
        
        foreach ($columns as $column) {
            if (isset($column['block_id'])) {
                $this->addColumn($column);
            }
        }
        
        return $this;
    }
    
    /**
     * Return all columns
     *
     * @return BL_CustomGrid_Model_Grid_Column[]
     */
    protected function _getColumns()
    {
        if (!$this->hasData('columns')) {
            $columns = array();
            
            if ($id = $this->getId()) {
                $columns = $this->_getResource()->getGridColumns($id, $this->getProfileId());
            }
            
            $this->setColumns($columns);
        }
        return $this->_getData('columns');
    }
    
    /**
     * Return all columns, possibly with some additional informations
     * 
     * @param bool $withEditConfigs Whether edit configs should be added to the corresponding columns
     * @param bool $withCustomColumns Whether custom columns models should be added to the corresponding columns
     * @return BL_CustomGrid_Model_Grid_Column[]
     */
    public function getColumns($withEditConfigs = false, $withCustomColumns = false)
    {
        $columns = $this->_getColumns();
        
        if (($withEditConfigs || $withCustomColumns)
            && ($typeModel = $this->getTypeModel())) {
            if ($withEditConfigs) {
                $columns = $typeModel->applyEditConfigsToColumns($this->getBlockType(), $columns);
            }
            if ($withCustomColumns) {
                $columnBlockIds = $this->getColumnBlockIdsByOrigin(BL_CustomGrid_Model_Grid_Column::ORIGIN_CUSTOM);
                $customColumns  = $this->getAvailableCustomColumns(false, true);
                
                foreach ($columnBlockIds as $blockId) {
                    if (isset($customColumns[$columns[$blockId]->getIndex()])) {
                        $columns[$blockId]->setCustomColumnModel($customColumns[$columns[$blockId]->getIndex()]);
                    }
                }
            }
        }
        
        return $columns;
    }
    
    /**
     * Columns sort callback
     *
     * @param BL_CustomGrid_Model_Grid_Column $columnA One column
     * @param BL_CustomGrid_Model_Grid_Column $columnB Another column
     * @return int
     */
    public function sortColumns(
        BL_CustomGrid_Model_Grid_Column $columnA,
        BL_CustomGrid_Model_Grid_Column $columnB
    ) {
        return $columnA->compareOrderTo($columnB);
    }
    
    /**
     * Return sorted columns, possibly filtered and with some additional informations
     *
     * @param bool $includeValid Whether valid columns should be returned (ie not missing ones)
     * @param bool $includeMissing Whether missing columns should be returned
     * @param bool $includeAttribute Whether attribute columns should be returned
     * @param bool $includeCustom Whether custom columns should be returned
     * @param bool $onlyVisible Whether only visible columns should be returned
     * @param bool $withEditConfigs Whether edit configs should be added to the corresponding columns
     * @param bool $withCustomColumn Whether custom columns models should be added to the corresponding columns
     * @return BL_CustomGrid_Model_Grid_Column[]
     */
    public function getSortedColumns(
        $includeValid = true,
        $includeMissing = true,
        $includeAttribute = true,
        $includeCustom = true,
        $onlyVisible = false,
        $withEditConfigs = false,
        $withCustomColumn = false
    ) {
        $columns = array();
        
        foreach ($this->getColumns($withEditConfigs, $withCustomColumn) as $columnBlockId => $column) {
            if (($onlyVisible && !$column->isVisible())
                || (!$includeMissing && $column->isMissing())
                || (!$includeValid && !$column->isMissing())
                || (!$includeAttribute && $column->isAttribute())
                || (!$includeCustom && $column->isCustom())) {
                continue;
            }
            $columns[$columnBlockId] = $column;
        }
        
        uasort($columns, array($this, 'sortColumns'));
        return $columns;
    }
    
    /**
     * Return the column corresponding to the given internal ID
     *
     * @param int $columnId Column internal ID
     * @return BL_CustomGrid_Model_Grid_Column|null
     */
    public function getColumnById($columnId)
    {
        $foundColumn = null;
        
        foreach ($this->getColumns() as $column) {
            if ($column->getId() == $columnId) {
                $foundColumn = $column;
                break;
            }
        }
        
        return $foundColumn;
    }
    
    /**
     * Return the column corresponding to the given block ID
     *
     * @param string $blockId Column block ID
     * @return BL_CustomGrid_Model_Grid_Column|null
     */
    public function getColumnByBlockId($blockId)
    {
        $columns = $this->getColumns();
        return (isset($columns[$blockId]) ? $columns[$blockId] : null);
    }
    
    /**
     * Return a column index from given code, origin and position (if applying)
     *
     * @param string $code Column code
     * @param string $origin Column origin
     * @param int $position Column position (used for attribute and custom origins)
     * @return string|null
     */
    public function getColumnIndexFromCode($code, $origin, $position = null)
    {
        $columns = $this->getColumns();
        $originIds = $this->getColumnBlockIdsByOrigin();
        
        if (($origin == BL_CustomGrid_Model_Grid_Column::ORIGIN_ATTRIBUTE)
            || ($origin == BL_CustomGrid_Model_Grid_Column::ORIGIN_CUSTOM)) {
            // Assume given code corresponds to attribute/custom column code
            $foundColumn = null;
            $correspondingColumns = array();
            
            foreach ($originIds[$origin] as $columnId) {
                if ($columns[$columnId]->getIndex() == $code) {
                    $correspondingColumns[] = $columns[$columnId];
                }
            }
            
            usort($correspondingColumns, 'sortColumns');
            $columnsCount = count($correspondingColumns);
            
            // If column is found, return the actual index that will be used for the grid block
            if (($position >= 1) && ($position <= $columnsCount)) {
                $foundColumn = $correspondingColumns[$position-1];
            } elseif ($columnsCount > 0) {
                $foundColumn = $correspondingColumns[0];
            }
            
            if (!is_null($foundColumn)) {
                if ($origin == BL_CustomGrid_Model_Grid_Column::ORIGIN_ATTRIBUTE) {
                    return self::ATTRIBUTE_COLUMN_GRID_ALIAS
                        . str_replace(self::ATTRIBUTE_COLUMN_ID_PREFIX, '', $foundColumn->getBlockId());
                } else {
                    return self::CUSTOM_COLUMN_GRID_ALIAS
                        . str_replace(self::CUSTOM_COLUMN_ID_PREFIX, '', $foundColumn->getBlockId());
                }
            }
        } elseif (array_key_exists($origin, Mage::getSingleton('customgrid/grid_column')->getOrigins())) {
            // Assume given code corresponds to column block ID
            if (isset($columns[$code]) && in_array($code, $originIds[$origin], true)) {
                // Return column index only if column exists and comes from wanted origin
                return $columns[$code]->getIndex();
            }
        }
        
        return null;
    }
    
    /**
     * Return whether attribute columns are available
     *
     * @return bool
     */
    public function canHaveAttributeColumns()
    {
        return (($typeModel = $this->getTypeModel()) && $typeModel->canHaveAttributeColumns($this->getBlockType()));
    }
    
    /**
     * Return available attributes
     *
     * @param bool $withRendererCodes Whether renderers codes should be added to the attributes
     * @param bool $withEditableFlags Whether editable flag should be added to the attributes
     * @return Mage_Eav_Model_Entity_Attribute[]
     */
    public function getAvailableAttributes($withRendererCodes = false, $withEditableFlags = false)
    {
        $attributes = array();
        
        if ($typeModel = $this->getTypeModel()) {
            $attributes = $typeModel->getAvailableAttributes($this->getBlockType(), $withEditableFlags);
            
            if ($withRendererCodes) {
                $renderers = Mage::getSingleton('customgrid/column_renderer_config_attribute')->getRenderersInstances();
                
                foreach ($attributes as $attribute) {
                    $attribute->setRendererCode(null);
                    
                    foreach ($renderers as $code => $renderer) {
                        if ($renderer->isAppliableToAttribute($attribute, $this)) {
                            $attribute->setRendererCode($code);
                            break;
                        }
                    }
                }
            }
        }
        
        return $attributes;
    }
    
    /**
     * Return available attributes codes
     *
     * @return string[]
     */
    public function getAvailableAttributesCodes()
    {
        return array_keys($this->getAvailableAttributes());
    }
    
    /**
     * Return renderer types codes from available attributes
     *
     * @return string[]
     */
    public function getAvailableAttributesRendererTypes()
    {
        $rendererTypes = array();
        $attributes = $this->getAvailableAttributes(true);
        
        foreach ($attributes as $code => $attribute) {
            $rendererTypes[$code] = $attribute->getRendererCode();
        }
        
        return $rendererTypes;
    }
    
    /**
     * Return next attribute column block ID (auto-generated ones)
     *
     * @return string
     */
    public function getNextAttributeColumnBlockId()
    {
        if (($maxId = $this->getMaxAttributeColumnBaseBlockId()) > 0) {
            $baseBlockId = $maxId + 1;
        } else {
            $baseBlockId = 1;
        }
        $this->setMaxAttributeColumnBaseBlockId($baseBlockId);
        return self::ATTRIBUTE_COLUMN_ID_PREFIX . $baseBlockId;
    }
    
    /**
     * Return whether some custom columns are available
     *
     * @return bool
     */
    public function canHaveCustomColumns()
    {
        return ($typeModel = $this->getTypeModel())
            && $typeModel->canHaveCustomColumns($this->getBlockType(), $this->getRewritingClassName());
    }
    
    /**
     * Add grid type code to given custom column code
     *
     * @param string $code Column code
     * @param string $typeCode Grid type code
     * @return this
     */
    protected function _addTypeToCustomColumnCode(&$code, $typeCode = null)
    {
        if (strpos($code, '/') === false) {
            if (is_null($typeCode)) {
                if ($typeModel = $this->getTypeModel()) {
                    $typeCode = $typeModel->getCode();
                } else {
                    return $this;
                }
            }
            $code = $typeCode . '/' . $code;
        }
        return $this;
    }
    
    /**
     * Return currently used custom columns codes
     *
     * @param bool $includeTypeCode Whether column codes should include the grid type code
     * @return string[]
     */
    public function getUsedCustomColumnsCodes($includeTypeCode = false)
    {
        if ($typeModel = $this->getTypeModel()) {
            $typeCode = $typeModel->getCode();
        } else {
            return array();
        }
        
        $codes = array();
        $columns = $this->getColumns();
        $columnBlockIds = $this->getColumnBlockIdsByOrigin(BL_CustomGrid_Model_Grid_Column::ORIGIN_CUSTOM);
        
        foreach ($columnBlockIds as $blockId) {
            $parts = explode('/', $columns[$blockId]->getIndex());
            
            if ($parts[0] == $typeCode) {
                $codes[] = $parts[1];
            }
        }
        if ($includeTypeCode) {
            array_walk($codes, array($this, '_addTypeToCustomColumnCode'), $typeCode);
        }
        
        return $codes;
    }
    
    /**
     * Return available custom columns
     *
     * @param bool $grouped Whether columns should be arranged by group
     * @param bool $includeTypeCode Whether column codes should include the grid type code
     * @return Bl_CustomGrid_Model_Custom_Column_Abstract[]
     */
    public function getAvailableCustomColumns($grouped = false, $includeTypeCode = false)
    {
        $availableColumns = array();
        
        if ($typeModel = $this->getTypeModel()) {
            $customColumns = $typeModel->getCustomColumns($this->getBlockType(), $this->getRewritingClassName());
            $usedCodes = $this->getUsedCustomColumnsCodes();
            $typeCode  = $typeModel->getCode();
            
            if (is_array($customColumns)) {
                if ($grouped) {
                    foreach ($customColumns as $code => $customColumn) {
                        if (!isset($availableColumns[$customColumn->getGroupId()])) {
                            $availableColumns[$customColumn->getGroupId()] = array();
                        }
                        if (in_array($code, $usedCodes)) {
                            $customColumn->setSelected(true);
                        }
                        if ($includeTypeCode) {
                            $this->_addTypeToCustomColumnCode($code, $typeCode);
                        }
                        
                        $availableColumns[$customColumn->getGroupId()][$code] = $customColumn;
                    }
                } elseif ($includeTypeCode) {
                    foreach ($customColumns as $code => $customColumn) {
                        $this->_addTypeToCustomColumnCode($code, $typeCode);
                        $availableColumns[$code] = $customColumn;
                    }
                } else {
                    $availableColumns = $customColumns;
                }
            }
        }
        
        return $availableColumns;
    }
    
    /**
     * Return available custom columns codes
     * 
     * @param bool $includeTypeCode Whether column codes should include the grid type code
     * @return string[]
     */
    public function getAvailableCustomColumnsCodes($includeTypeCode = false)
    {
        return array_keys($this->getAvailableCustomColumns(false, $includeTypeCode));
    }
    
    /**
     * Return custom column groups
     *
     * @param bool $onlyUsed Whether only groups which contain available custom columns should be returned
     * @return string[]
     */
    public function getCustomColumnsGroups($onlyUsed = true)
    {
        $groups = array();
        
        if ($typeModel = $this->getTypeModel()) {
            $groups = $typeModel->getCustomColumnsGroups();
            
            if ($onlyUsed) {
                $groupsIds = array();
                
                foreach ($this->getAvailableCustomColumns() as $column) {
                    $groupsIds[] = $column->getGroupId();
                }
                
                $groupsIds = array_unique($groupsIds);
                $groups = array_intersect_key($groups, array_flip($groupsIds));
            }
        }
        
        return $groups;
    }
    
    /**
     * Return next custom column block ID (auto-generated ones)
     *
     * @return string
     */
    public function getNextCustomColumnBlockId()
    {
        if (($maxId = $this->getMaxCustomColumnBaseBlockId()) > 0) {
            $baseBlockId = $maxId + 1;
        } else {
            $baseBlockId = 1;
        }
        $this->setMaxCustomColumnBaseBlockId($baseBlockId);
        return self::CUSTOM_COLUMN_ID_PREFIX . $baseBlockId;
    }
    
    /**
     * Return column header
     *
     * @param string $columnBlockId Column block ID
     * @return string|null
     */
    public function getColumnHeader($columnBlockId)
    {
        return ($column = $this->getColumnByBlockId($columnBlockId))
            ? $column->getHeader()
            : null;
    }
    
    /**
     * Return column locked values (ie that should not be user-defined)
     *
     * @param string $columnBlockId Column block ID
     * @return array
     */
    public function getColumnLockedValues($columnBlockId)
    {
        $values = array();
        
        if (($typeModel = $this->getTypeModel())
            && (($column = $this->getColumnByBlockId($columnBlockId)) && $column->isCollection())) {
            $values = $typeModel->getColumnLockedValues($this->getBlockType(), $columnBlockId);
        }
        
        return (is_array($values) ? $values : array());
    }
    
    /**
     * Return whether given block type and block ID correspond to this grid
     *
     * @param string $blockType Block type
     * @param string $blockId Block ID in layout
     * @return bool
     */
    public function matchGridBlock($blockType, $blockId)
    {
        return (($typeModel = $this->getTypeModel()) && $typeModel->matchGridBlock($blockType, $blockId, $this));
    }
    
    /**
     * Return whether the grid has editable columns
     *
     * @return bool
     */
    public function hasEditableColumns()
    {
        $hasEditableColumns = false;
        
        if ($typeModel = $this->getTypeModel()) {
            $editableValues = $typeModel->getEditableAttributes($this->getBlockType());
            
            if (!empty($editableValues)) {
                $hasEditableColumns = true;
            } else {
                $editableValues = array_merge(
                    $typeModel->getEditableFields($this->getBlockType()),
                    $typeModel->getEditableAttributeFields($this->getBlockType())
                );
                
                if (!empty($editableValues)) {
                    $columns = $this->getSortedColumns(true, true, false, false);
                    $editableValues = array_intersect_key($columns, $editableValues);
                    $hasEditableColumns = !empty($editableValues);
                }
            }
        }
        
        return $hasEditableColumns;
    }
    
    /**
     * Return whether the current user has edit permissions over the grid columns
     *
     * @param Mage_Adminhtml_Block_Widget_Grid $gridBlock Grid block
     * @return bool
     */
    public function hasUserEditPermissions(Mage_Adminhtml_Block_Widget_Grid $gridBlock)
    {
        return ($typeModel = $this->getTypeModel())
            ? $typeModel->checkUserEditPermissions($this->getBlockType(), $this, $gridBlock)
            : $this->checkUserPermissions(self::ACTION_EDIT_COLUMNS_VALUES);
    }
    
    /**
     * Return additional parameters needed for edit, corresponding to given grid block
     *
     * @param Mage_Adminhtml_Block_Widget_Grid $gridBlock Grid block
     * @return array
     */
    public function getAdditionalEditParams(Mage_Adminhtml_Block_Widget_Grid $gridBlock)
    {
        return ($typeModel = $this->getTypeModel())
            ? $typeModel->getAdditionalEditParams($this->getBlockType(), $gridBlock)
            : array();
    }
    
    /**
     * Return grid collection row identifiers
     *
     * @param Varien_Object $row Grid row
     * @return array
     */
    public function getCollectionRowIdentifiers(Varien_Object $row)
    {
        return ($typeModel = $this->getTypeModel())
            ? $typeModel->getEntityRowIdentifiers($this->getBlockType(), $row)
            : array();
    }
    
    /**
     * Update default parameters behaviours
     *
     * @param array $behaviours New behaviours
     * @return this
     */
    public function updateDefaultParametersBehaviours(array $behaviours)
    {
        if (!$this->checkUserPermissions(self::ACTION_EDIT_DEFAULT_PARAMS_BEHAVIOURS)) {
            $this->throwPermissionException();
        }
        
        $keys = array_fill_keys($this->getGridParamsKeys(), false);
        $keys[self::GRID_PARAM_FILTER] = true;
        
        $scalarValues = array(
            self::DEFAULT_PARAM_DEFAULT,
            self::DEFAULT_PARAM_FORCE_CUSTOM,
            self::DEFAULT_PARAM_FORCE_ORIGINAL,
        );
        
        $arrayValues = array(
            self::DEFAULT_PARAM_MERGE_DEFAULT,
            self::DEFAULT_PARAM_MERGE_BASE_CUSTOM,
            self::DEFAULT_PARAM_MERGE_BASE_ORIGINAL,
        );
        
        foreach ($keys as $key => $isArray) {
            if (isset($behaviours[$key])) {
                $value = null;
                
                if (in_array($behaviours[$key], $scalarValues)
                    || ($isArray && in_array($behaviours[$key], $arrayValues))) {
                    $value = $behaviours[$key];
                }
                
                $this->setData('default_' . $key . '_behaviour', $value);
            }
        }
        
        $this->setDataChanges(true);
        return $this;
    }
    
    /**
     * Set users config
     *
     * @param array $usersConfig Users config
     * @return this
     */
    public function setUsersConfig(array $usersConfig)
    {
        $this->resetUsersConfigValues();
        
        foreach ($usersConfig as $key => $userConfig) {
            if (is_array($userConfig)) {
                $userConfig = new BL_CustomGrid_Object($userConfig);
                $usersConfig[$key] = $userConfig;
            }
            if (!is_object($userConfig)) {
                unset($usersConfig[$key]);
                continue;
            }
            if (!is_null($defaultProfileId = $userConfig->getData('default_profile_id'))) {
                $userConfig->setData('default_profile_id', (int) $defaultProfileId);
            }
        }
        
        return $this->setData('users_config', $usersConfig);
    }
    
    /**
     * Return users config
     *
     * @return BL_CustomGrid_Object[]
     */
    public function getUsersConfig()
    {
        if (!$this->hasData('users_config')) {
            $usersConfig = (($id = $this->getId()) ? $this->_getResource()->getGridUsers($id) : array());
            $this->setUsersConfig($usersConfig);
        }
        return $this->_getData('users_config');
    }
    
    /**
     * Return the config corresponding to the given user ID, or null if none exists
     *
     * @return BL_CustomGrid_Object|null
     */
    public function getUserConfig($userId)
    {
        $usersConfig = $this->getUsersConfig();
        return (isset($usersConfig[$userId]) ? $usersConfig[$userId] : null);
    }
    
    /**
     * Set roles config
     *
     * @param array $rolesConfig Roles config
     * @return this
     */
    public function setRolesConfig(array $rolesConfig)
    {
        $this->resetRolesConfigValues();
        
        foreach ($rolesConfig as $key => $roleConfig) {
            if (is_array($roleConfig)) {
                $roleConfig = new BL_CustomGrid_Object($roleConfig);
                $rolesConfig[$key] = $roleConfig;
            }
            if (!is_object($roleConfig)) {
                unset($rolesConfig[$key]);
                continue;
            }
            if (!is_array($permissions = $roleConfig->getData('permissions'))) {
                $permissions = array();
            }
            if (!is_null($defaultProfileId = $roleConfig->getData('default_profile_id'))) {
                $defaultProfileId = (int) $defaultProfileId;
            }
            if (!is_array($assignedProfilesIds = $roleConfig->getData('assigned_profiles_ids'))) {
                $assignedProfilesIds = array();
            }
            
            $roleConfig->addData(
                array(
                    'permissions' => $permissions,
                    'default_profile_id' => $defaultProfileId,
                    'assigned_profiles_ids' => array_map('intval', $assignedProfilesIds),
                )
            );
        }
        
        return $this->setData('roles_config', $rolesConfig);
    }
    
    /**
     * Return roles config
     *
     * @return BL_CustomGrid_Object[]
     */
    public function getRolesConfig()
    {
        if (!$this->hasData('roles_config')) {
            $rolesConfig = (($id = $this->getId()) ? $this->_getResource()->getGridRoles($id) : array());
            $this->setRolesConfig($rolesConfig);
        }
        return $this->_getData('roles_config');
    }
    
    /**
     * Return the config corresponding to the given role ID, or null if none exists
     *
     * @return BL_CustomGrid_Object|null
     */
    public function getRoleConfig($roleId)
    {
        $rolesConfig = $this->getRolesConfig();
        return (isset($rolesConfig[$roleId]) ? $rolesConfig[$roleId] : null);
    }
    
    /**
     * Return the permissions for the given role
     *
     * @param int $roleId Role ID
     * @param mixed $default Default value to return if there is no permissions for the given role ID
     * @return mixed
     */
    public function getRolePermissions($roleId, $default = array())
    {
        return ($roleConfig = $this->getRoleConfig($roleId))
            ? $roleConfig->getDataSetDefault('permissions', array())
            : $default;
    }
    
    /**
     * Update grid roles permissions
     *
     * @param array $permissions Roles permissions
     * @return this
     */
    public function updateRolesPermissions(array $permissions)
    {
        if (!$this->checkUserPermissions(self::ACTION_EDIT_ROLES_PERMISSIONS)) {
            $this->throwPermissionException();
        }
        
        $flags = array(
            self::ACTION_PERMISSION_USE_CONFIG,
            self::ACTION_PERMISSION_YES,
            self::ACTION_PERMISSION_NO,
        );
        
        $actions = self::getGridActions();
        $rolesConfig = $this->getRolesConfig();
        
        foreach ($permissions as $roleId => $rolePermissions) {
            if (!isset($rolesConfig[$roleId])) {
                $rolesConfig[$roleId] = new BL_CustomGrid_Object(
                    array(
                        'permissions' => array(),
                        'default_profile_id' => null,
                        'assigned_profiles_ids' => array(),
                    )
                );
            }
            if (!is_array($rolePermissions)) {
                $rolePermissions = array();
            }
            
            foreach ($rolePermissions as $action => $flag) {
                $flag = strval($flag);
                
                if (!isset($actions[$action]) || !in_array($flag, $flags, true)) {
                    unset($rolePermissions[$action]);
                }
            }
            
            $rolesConfig[$roleId]->setData('permissions', $rolePermissions);
        }
        
        $this->setData('roles_config', $rolesConfig);
        return $this;
    }
    
    /**
     * Prepare and return the given base values from an user permissions check, ensuring that they are fully suitable
     * for further tests
     * 
     * @param string|array $actions Actions codes
     * @param bool|array|null $aclPermissions Corresponding ACL permissions values
     * return array Array containing the two prepared values
     */
    protected function _prepareUserPermissionsCheckValues($actions, $aclPermissions)
    {
        if (!is_array($actions)) {
            if (!is_null($aclPermissions) && !is_array($aclPermissions)) {
                $aclPermissions = array($actions => $aclPermissions);
            }
            $actions = array($actions);
        }
        if (!is_array($aclPermissions)) {
            $aclPermissions = array();
        }
        return array($actions, $aclPermissions);
    }
    
    /**
     * Check if the current user has the required permission for the given action
     * 
     * @param string $action Action code
     * @param array $permissions Role permissions values
     * @param array $aclPermissions ACL permissions values
     * @return bool
     */
    protected function _checkUserActionPermission($action, array $permissions, array $aclPermissions)
    {
        $result = true;
        $actionPermission = (isset($permissions[$action]) ? $permissions[$action] : self::ACTION_PERMISSION_USE_CONFIG);
        
        if ($actionPermission === self::ACTION_PERMISSION_NO) {
            $result = false;
        } elseif ($actionPermission === self::ACTION_PERMISSION_USE_CONFIG) {
            $result = isset($aclPermissions[$action])
                ? (bool) $aclPermissions[$action]
                : (bool) $this->getAdminSession()->isAllowed(self::$_gridActionsAclPaths[$action]);
        }
        
        return $result;
    }
    
    /**
     * Check if the current user has the required permissions for any or all of the given actions
     *
     * @param string|array $actions Actions codes
     * @param bool|array|null $aclPermissions Corresponding ACL permissions values
     * @param bool $any Whether the user should have any of the given permissions, otherwise all
     * @return bool
     */
    public function checkUserPermissions($actions, $aclPermissions = null, $any = true)
    {
        $any = (bool) $any;
        $session = $this->getAdminSession();
        
        if (($user = $session->getUser()) && ($role = $user->getRole())) {
            $roleId = $role->getId();
        } else {
            return false;
        }
        
        list($actions, $aclPermissions) = $this->_prepareUserPermissionsCheckValues($actions, $aclPermissions);
        $permissions = $this->getRolePermissions($roleId);
        $result = true;
        
        foreach ($actions as $action) {
            $result = $this->_checkUserActionPermission($action, $permissions, $aclPermissions);
            
            if ($any === $result) {
                break;
            }
        }
        
        return $result;
    }
    
    /**
     * Return whether the "System" part of the columns list should be displayed
     *
     * @return bool
     */
    public function getDisplaySystemPart()
    {
        return is_null($value = $this->_getData('display_system_part'))
            ? $this->getConfigHelper()->getDisplaySystemPart()
            : (bool) $value;
    }
    
    /**
     * Return whether custom headers should be ignored for columns coming from grid block
     *
     * @return bool
     */
    public function getIgnoreCustomHeaders()
    {
        return is_null($value = $this->_getData('ignore_custom_headers'))
            ? $this->getConfigHelper()->getIgnoreCustomHeaders()
            : (bool) $value;
    }
    
    /**
     * Return whether custom widths should be ignored for columns coming from grid block
     *
     * @return bool
     */
    public function getIgnoreCustomWidths()
    {
        return is_null($value = $this->_getData('ignore_custom_widths'))
            ? $this->getConfigHelper()->getIgnoreCustomWidths()
            : (bool) $value;
    }
    
    /**
     * Return whether custom alignments should be ignored for columns coming from grid block
     *
     * @return bool
     */
    public function getIgnoreCustomAlignments()
    {
        return is_null($value = $this->_getData('ignore_custom_alignments'))
            ? $this->getConfigHelper()->getIgnoreCustomAlignments()
            : (bool) $value;
    }
    
    /**
     * Return whether custom pagination values should be merged with the base ones
     *
     * @return bool
     */
    public function getMergeBasePagination()
    {
        return is_null($value = $this->_getData('merge_base_pagination'))
            ? $this->getConfigHelper()->getMergeBasePagination()
            : (bool) $value;
    }
    
    /**
     * Return custom pagination values
     *
     * @return int[]
     */
    public function getPaginationValues()
    {
        return is_null($value = $this->_getData('pagination_values'))
            ? $this->getConfigHelper()->getPaginationValues()
            : $this->getHelper()->parseCsvIntArray($value, true, true, 1);
    }
    
    /**
     * Return default pagination value
     *
     * @return int
     */
    public function getDefaultPaginationValue()
    {
        return is_null($value = $this->_getData('default_pagination_value'))
            ? $this->getConfigHelper()->getDefaultPaginationValue()
            : (int) $value;
    }
    
    /**
     * Return appliable pagination values
     *
     * @return int[]
     */
    public function getAppliablePaginationValues()
    {
        if (!$this->hasData('appliable_pagination_values')) {
            $values = $this->getPaginationValues();
            
            if (!is_array($values) || empty($values)) {
                $values = self::$_defaultPaginationValues;
            } elseif ($this->getMergeBasePagination()) {
                $values = array_unique(array_merge($values, self::$_defaultPaginationValues));
                sort($values, SORT_NUMERIC);
            }
            
            $this->setData('appliable_pagination_values', $values);
        }
        return $this->_getData('appliable_pagination_values');
    }
    
    /**
     * Return whether the grid header should be pinned (pager / export / mass-actions block)
     *
     * @return bool
     */
    public function getPinHeader()
    {
        return is_null($value = $this->_getData('pin_header'))
            ? $this->getConfigHelper()->getPinHeader()
            : (bool) $value;
    }
    
    /**
     * Return whether the RSS links should be displayed in a dedicated window
     *
     * @return bool
     */
    public function getUseRssLinksWindow()
    {
        return is_null($value = $this->_getData('use_rss_links_window'))
            ? $this->getConfigHelper()->getUseRssLinksWindow()
            : (bool) $value;
    }
    
    /**
     * Return whether the original export block should be hidden
     *
     * @return bool
     */
    public function getHideOriginalExportBlock()
    {
        return is_null($value = $this->_getData('hide_original_export_block'))
            ? $this->getConfigHelper()->getHideOriginalExportBlock()
            : (bool) $value;
    }
    
    /**
     * Return whether the filter reset button should be hidden
     *
     * @return bool
     */
    public function getHideFilterResetButton()
    {
        return is_null($value = $this->_getData('hide_filter_reset_button'))
            ? $this->getConfigHelper()->getHideFilterResetButton()
            : (bool) $value;
    }
    
    /**
     * Update customization parameters
     * 
     * @param array $params Customization parameters
     * @return this
     */
    public function updateCustomizationParameters(array $params)
    {
        if (!$this->checkUserPermissions(self::ACTION_EDIT_CUSTOMIZATION_PARAMS)) {
            $this->throwPermissionException();
        }
        
        $booleanParams = array_intersect_key(
            $params,
            array_flip(
                array(
                    'display_system_part',
                    'ignore_custom_headers',
                    'ignore_custom_widths',
                    'ignore_custom_aligments',
                    'merge_base_pagination',
                    'pin_header',
                    'rss_links_window',
                    'hide_original_export_block',
                    'hide_filter_reset_button',
                )
            )
        );
        
        foreach ($booleanParams as $key => $value) {
            $this->setData($key, ($value !== '' ? (bool) $value : null));
        }
        
        if (isset($params['pagination_values'])) {
            $value = ($params['pagination_values'] !== '' ? $params['pagination_values'] : null);
            $this->setData('pagination_values', $value);
        }
        if (isset($params['default_pagination_value'])) {
            $value = ($params['default_pagination_value'] !== '' ? (int) $params['default_pagination_value'] : null);
            $this->setData('default_pagination_value', $value);
        }
        
        return $this;
    }
    
    /**
     * Return grid actions options hash
     *
     * @param bool $grouped Whether actions should be grouped by general category
     * @return array
     */
    public function getGridActions($grouped = false)
    {
        if (is_null(self::$_groupedGridActions)) {
            $helper = Mage::helper('customgrid');
            
            self::$_groupedGridActions = array(
                'customization' => array(
                    'label'   => $helper->__('Customization'),
                    'actions' => array(
                        self::ACTION_CUSTOMIZE_COLUMNS       => 'Customize Columns',
                        self::ACTION_USE_CUSTOMIZED_COLUMNS  => 'Use Customized Columns',
                        self::ACTION_VIEW_GRID_INFOS         => 'View Grids Informations',
                        self::ACTION_EDIT_DEFAULT_PARAMS     => 'Edit Default Parameters',
                        self::ACTION_USE_DEFAULT_PARAMS      => 'Use Default Parameters',
                        self::ACTION_EXPORT_RESULTS          => 'Export Results',
                        self::ACTION_CHOOSE_EDITABLE_COLUMNS => 'Choose Editable Columns',
                        self::ACTION_EDIT_COLUMNS_VALUES     => 'Edit Columns Values',
                    ),
                ),
                'administration' => array(
                    'label'   => $helper->__('Administration'),
                    'actions' => array(
                        self::ACTION_ENABLE_DISABLE                 => 'Enable / Disable',
                        self::ACTION_EDIT_FORCED_TYPE               => 'Edit Forced Type',
                        self::ACTION_EDIT_CUSTOMIZATION_PARAMS      => 'Edit Customization Parameters', 
                        self::ACTION_EDIT_DEFAULT_PARAMS_BEHAVIOURS => 'Edit Default Parameters Behaviours', 
                        self::ACTION_EDIT_ROLES_PERMISSIONS         => 'Edit Roles Permissions', 
                        self::ACTION_DELETE                         => 'Delete', 
                    ),
                ),
                'profiles' => array(
                    'label'   => $helper->__('Profiles'),
                    'actions' => array(
                        self::ACTION_ACCESS_ALL_PROFILES                => 'Access All Profiles',
                        self::ACTION_CHOOSE_OWN_USER_DEFAULT_PROFILE    => 'Choose Default Profile (Own User)',
                        self::ACTION_CHOOSE_OTHER_USERS_DEFAULT_PROFILE => 'Choose Default Profile (Other Users)',
                        self::ACTION_CHOOSE_OWN_ROLE_DEFAULT_PROFILE    => 'Choose Default Profile (Own Role)',
                        self::ACTION_CHOOSE_OTHER_ROLES_DEFAULT_PROFILE => 'Choose Default Profile (Other Roles)',
                        self::ACTION_CHOOSE_GLOBAL_DEFAULT_PROFILE      => 'Choose Default Profile (Global)',
                        self::ACTION_COPY_PROFILES_TO_NEW               => 'Copy Profiles (To New)',
                        self::ACTION_COPY_PROFILES_TO_EXISTING          => 'Copy Profiles (To Existing)',
                        self::ACTION_EDIT_PROFILES                      => 'Edit Profiles',
                        self::ACTION_ASSIGN_PROFILES                    => 'Assign Profiles To Roles',
                        self::ACTION_DELETE_PROFILES                    => 'Delete Profiles',
                    ),
                ),
            );
            
            foreach (self::$_groupedGridActions as $groupKey => $values) {
                foreach ($values['actions'] as $actionKey => $actionLabel) {
                    self::$_groupedGridActions[$groupKey]['actions'][$actionKey] = $helper->__($actionLabel);
                }
            }
        }
        
        if ($grouped) {
            return self::$_groupedGridActions;
        }
        
        if (is_null(self::$_gridActions)) {
            self::$_gridActions = array();
            
            foreach (self::$_groupedGridActions as $actionsGroup) {
                foreach ($actionsGroup['actions'] as $key => $value) {
                    self::$_gridActions[$key] = $value;
                }
            }
        }
        
        return self::$_gridActions;
    }
}
