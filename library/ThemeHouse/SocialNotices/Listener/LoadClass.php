<?php

class ThemeHouse_SocialNotices_Listener_LoadClass extends ThemeHouse_Listener_LoadClass
{
    protected function _getExtendedClasses()
    {
        return array(
            'ThemeHouse_SocialNotices' => array(
                'controller' => array(
                    'ThemeHouse_SocialGroups_ControllerPublic_SocialForum',
                    'XenForo_ControllerAdmin_Notice'
                ), /* END 'controller' */
                'datawriter' => array(
                    'XenForo_DataWriter_Notice'
                ), /* END 'datawriter' */
                'model' => array(
                    'ThemeHouse_SocialGroups_Model_SocialForum',
                    'XenForo_Model_Notice',
                ), /* END 'model' */
            ), /* END 'ThemeHouse_SocialNotices' */
        );
    } /* END _getExtendedClasses */

    public static function loadClassController($class, array &$extend)
    {
        $loadClassController = new ThemeHouse_SocialNotices_Listener_LoadClass($class, $extend, 'controller');
        $extend = $loadClassController->run();
    } /* END loadClassController */

    public static function loadClassDataWriter($class, array &$extend)
    {
        $loadClassDataWriter = new ThemeHouse_SocialNotices_Listener_LoadClass($class, $extend, 'datawriter');
        $extend = $loadClassDataWriter->run();
    } /* END loadClassDataWriter */

    public static function loadClassModel($class, array &$extend)
    {
        $loadClassModel = new ThemeHouse_SocialNotices_Listener_LoadClass($class, $extend, 'model');
        $extend = $loadClassModel->run();
    } /* END loadClassModel */
}