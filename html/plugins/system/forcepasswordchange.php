<?php
/**
 * @package        Force Password Change
 * @copyright (C) 2010-2012 by Source Coast - All rights reserved
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.event.plugin');

class plgSystemForcePasswordChange extends JPlugin
{
    function __construct(& $subject, $config)
    {
        parent::__construct($subject, $config);
    }

    function onAfterRoute()
    {
        $app = JFactory::getApplication();
        if ($app->isAdmin())
            return;

        $user = &JFactory::getUser();

        $option = JRequest::getVar('option');
        $view = JRequest::getVar('view');
        $task = JRequest::getVar('task');
        $layout = JRequest::getVar('layout');
        // no_html is sent by Mighty Registration for ajax checks, so we need to ignore them
        $noHtml = JRequest::getVar('no_html');

        
        $editProfileOption = "com_user";
        $editProfileLayout = "form";
        $editProfileSaveTask = "save";
        $editProfileView = "user";
         //SC15

        

        // Use these for Mighty Registration
        /*
          $editProfileOption = "com_juser";
          $editProfileLayout = "mydetails";
          $editProfileSaveTask = "user_update";
          */

        if (!$user->guest && $user->lastvisitDate == "0000-00-00 00:00:00" && $noHtml != "1")
        {
            // The user is not a guest and their lastvisitDate is zeros
            if ($option == $editProfileOption && $task == $editProfileSaveTask)
            {
                // The user is saving their profile

                // Set the last visit date to a real value so we won't continue forcing them to update their profile
                $user->setLastVisit();
                $date = JFactory::getDate();
                $user->lastvisitDate = $date->toMySQL();
            }
            else if (!($option == $editProfileOption && $view == $editProfileView && $layout == $editProfileLayout))
            {
                // The user is not on the edit profile form

                // Update lastvisitDate back to zero
                $dbo = &JFactory::getDBO();
                $query = "UPDATE #__users " .
                         "SET lastvisitDate = " . $dbo->quote("0000-00-00 00:00:00") . " " .
                         "WHERE id = " . $dbo->quote($user->id);
                $dbo->setQuery($query);
                $dbo->query();

                // Redirect to edit profile
                $lang =& JFactory::getLanguage();
                $lang->load('plg_system_forcepasswordchange', JPATH_ADMINISTRATOR);

                $app = &JFactory::getApplication();

                $app->redirect(
                    "index.php?option=" . $editProfileOption . "&view=" . $editProfileView . "&layout=" . $editProfileLayout,
                    JText::_("PLG_FORCEPASSWORDCHANGE_UPDATE_YOUR_PASSWORD")
                );
            }
        }
    }
}
