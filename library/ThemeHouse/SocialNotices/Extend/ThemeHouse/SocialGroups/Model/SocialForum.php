<?php

class ThemeHouse_SocialNotices_Extend_ThemeHouse_SocialGroups_Model_SocialForum extends XFCP_ThemeHouse_SocialNotices_Extend_ThemeHouse_SocialGroups_Model_SocialForum
{

    /**
     * Determines if the specified user can manage social notices.
     *
     * @param array $socialForum
     * @param string $errorPhraseKey By ref. More specific error, if available.
     * @param array|null $viewingUser Viewing user reference
     *
     * @return boolean
     */
    public function canManageSocialNotices(array $socialForum, &$errorPhraseKey = '', array $nodePermissions = null,
        array $viewingUser = null)
    {
        $this->standardizeViewingUserReferenceForNode($socialForum['node_id'], $viewingUser, $nodePermissions);

        return XenForo_Permission::hasContentPermission($nodePermissions, 'manageSocialNotices');
    } /* END canManageSocialNotices */
}