<?php

/**
 * Controller class for the context groups module.
 * 
 * Purpose of this module is to allow for context member management.
 * It should hide the group information from the user.
 * Target user: Lecturers.
 * Precondition : User must be in a context.
 * Tasks: Add/remove Lecturers, students, or guests.
 * 
 * PHP versions 4 and 5
 * 
 * This program is free software; you can redistribute it and/or modify 
 * it under the terms of the GNU General Public License as published by 
 * the Free Software Foundation; either version 2 of the License, or 
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful, 
 * but WITHOUT ANY WARRANTY; without even the implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the 
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License 
 * along with this program; if not, write to the 
 * Free Software Foundation, Inc., 
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 * 
 * @category  Chisimba
 * @package   contextgroups
 * @author    Jonathan Abrahams <jabrahams@uwc.ac.za>
 * @copyright 2007 Jonathan Abrahams
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global string $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/**
 * Controller class for the context groups module.
 * 
 * Purpose of this module is to allow for context member management.
 * It should hide the group information from the user.
 * Target user: Lecturers.
 * Precondition : User must be in a context.
 * Tasks: Add/remove Lecturers, students, or guests.
 * 
 * @category  Chisimba
 * @package   contextgroups
 * @author    Jonathan Abrahams <jabrahams@uwc.ac.za>
 * @copyright 2007 Jonathan Abrahams
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
class contextgroups extends controller
{
    /**
    * @var groupadminmodel Object reference.
    */
    var $_objGroupAdmin;
    /**
    * @var dbcontect Object reference.
    */
    var $_objDBContext;
    /**
    * @var language Object reference.
    */
    var $_objLanguage;
    /**
    * @var contextcondition Object reference.
    */
    var $_objContextCondition;

    /**
    * Method to initialise the module.
    */
    function init()
    {
        $this->_objGroupAdmin = &$this->newObject('groupadminmodel','groupadmin');
        $this->_objDBContext = &$this->newObject('dbcontext','context');
        $this->_objLanguage = &$this->newObject('language','language');
        $this->_objContextCondition = &$this->newObject('contextcondition','contextpermissions');

        $this->lectGroupId = $this->_objGroupAdmin->getLeafId( array( $this->_objDBContext->getContextCode(), 'Lecturers' ) );
        $this->studGroupId = $this->_objGroupAdmin->getLeafId( array( $this->_objDBContext->getContextCode(), 'Students' ) );
        $this->guestGroupId = $this->_objGroupAdmin->getLeafId( array( $this->_objDBContext->getContextCode(), 'Guest' ) );

        $this->setVar( 'lectGroupId' , $this->lectGroupId );
        $this->setVar( 'studGroupId' , $this->studGroupId );
        $this->setVar( 'guestGroupId' , $this->guestGroupId );

        $this->setVar( 'linkToContextHome', $this->linkToContextHome() );

        $this->errCodes= array();
        // Action to take for errors.
        $this->errCodes['notInContext']= array(
            'action' => $this->linkToModule('word_home','postlogin'),
            'title'  => 'mod_contextgroups_ttlNotInContext',
            'error'  => 'mod_contextgroups_notInContext');
        $this->errCodes['notLect']= array(
            'action' => $this->linkBack('word_back'),
            'title'  => 'mod_contextgroups_ttlNotLect',
            'error'  => 'mod_contextgroups_notLect');

    }

    /**
    * Method used by the framework to handle messages.
    */
    function dispatch( $action )
    {
        // Precondition: Must be in context.
        if( !$this->_objDBContext->isInContext() ) {
            // If not in context redirect to error page.
            return $this->showError('notInContext');
        }
/*
        else // Only Admin and Lecturers users can access this module.
        if( !( $this->objEngine->_objUser->isAdmin() || $this->_objContextCondition->isContextMember('Lecturers') ) ) {
            return $this->showError('notLect');
        }
*/

        // Normal course of events.
        switch( $action ) {
            case 'lecturers_form' :
                return $this->processManage( 'Lecturers' );
            case 'students_form' :
                return $this->processManage( 'Students' );
            case 'guest_form' :
                return $this->processManage( 'Guest' );

            case 'manage_lect' :
                return $this->showManage('Lecturers');
            case 'manage_stud' :
                return $this->showManage('Students');
            case 'manage_guest' :
                return $this->showManage('Guest');

            case 'main':
            default : return $this->showMain();
            
           
        }
    }

    /**
    * Method to show the main template.
    * @return string template name.
    */
    function showMain( )
    {
    	//$this->setVar('pagesuppressxml', TRUE);
        $objMembers = &$this->newObject('groupadmin_members','groupadmin');
        $objMembers->setHeaders( array( 'Firstname', 'Surname') );
        $this->setVarByRef('objMembers2',$objMembers);
		 //$objMembers->setGroupId( $this->lectGroupId );
        //echo $objMembers->show('lects');
        $lnkLect = $this->newObject('link', 'htmlelements');
        $lnkLect->href = $this->uri( array( 'action'=>'manage_lect' ) );
        $lnkLect->link = $this->_objLanguage->code2Txt('mod_contextgroups_managelects','contextgroups',array('authors'=>''));
        $this->setVarByRef('lnkLect', $lnkLect );

        $lnkStud = $this->newObject('link', 'htmlelements');
        $lnkStud->href = $this->uri( array( 'action'=>'manage_stud' ) );
        $lnkStud->link = $this->_objLanguage->code2Txt('mod_contextgroups_managestuds','contextgroups',array('readonlys'=>''));
        $this->setVarByRef('lnkStud', $lnkStud );

        $lnkGuest = $this->newObject('link', 'htmlelements');
        $lnkGuest->href = $this->uri( array( 'action'=>'manage_guest' ) );
        $lnkGuest->link = $this->_objLanguage->languageText('mod_contextgroups_manageguests','contextgroups');
        $this->setVarByRef('lnkGuest', $lnkGuest );

        $title = $this->_objLanguage->code2Txt(
            'mod_contextgroups_ttlManage','contextgroups',
            array( 'TITLE'=> $this->_objDBContext->getTitle() ) );

        $this->setVar('ttlLecturers', $this->_objLanguage->code2Txt('mod_contextgroups_ttlLecturers','contextgroups',array('authors'=>'')) );
        $this->setVar('title',$title);
        $this->setVar('ttlStudents', $this->_objLanguage->code2Txt('mod_contextgroups_ttlStudents','contextgroups') );
        $this->setVar('ttlGuests', $this->_objLanguage->languageText('mod_contextgroups_ttlGuest','contextgroups') );

        return 'main_tpl.php';
    }

    /**
    * Method to process the request to manage a member group.
    * @param string the group to be managed.
    */
    function processManage( $groupName )
    {//var_dump($_POST);
        $groupId = $this->_objGroupAdmin->getLeafId( array( $this->_objDBContext->getContextCode(), $groupName ) );
        if ( $this->getParam( 'button' ) == 'save' && $groupId <> '' ) {
            // Get the revised member ids
            if(is_array($this->getParam( 'list2' )))
            {
            	$list = $this->getParam( 'list2' );
            } else {
            	$list = array();
            }
           
            // Get the original member ids
            $fields = array ( 'tbl_users.id' );
            $memberList = &$this->_objGroupAdmin->getGroupUsers( $groupId, $fields );
            $oldList = $this->_objGroupAdmin->getField( $memberList, 'id' );
            // Get the added member ids
            $addList = array_diff( $list, $oldList );
            // Get the deleted member ids
            $delList = array_diff( $oldList, $list );
            // Add these members
            foreach( $addList as $userId ) {
                $this->_objGroupAdmin->addGroupUser( $groupId, $userId );
            }
            // Delete these members
            foreach( $delList as $userId ) {
                $this->_objGroupAdmin->deleteGroupUser( $groupId, $userId );
            }
        }
        if ( $this->getParam( 'button' ) == 'cancel' && $groupId <> '' ) {

        }
        // After processing return to main
        return $this->nextAction( 'main', array() );

    }
    /**
    * Method to show the manage member group template.
    * @param string the group to be managed.
    */
    function showManage( $groupName )
    {
        $groupId = $this->_objGroupAdmin->getLeafId( array( $this->_objDBContext->getContextCode(), $groupName ) );
        // The member list of this group
        $fields = array ( 'firstName', 'surname', 'tbl_users.id' );
        $memberList = $this->_objGroupAdmin->getGroupUsers( $groupId, $fields );
        $memberIds  = $this->_objGroupAdmin->getField( $memberList, 'id' );
        $filter = "'" . implode( "', '", $memberIds ) . "'";

        // Users list need the firstname, surname, and userId fields.
        $fields = array ( 'firstName', 'surname', 'id' );
        $usersList = $this->_objGroupAdmin->getUsers( $fields, " WHERE id NOT IN($filter)" );
        sort( $usersList );

        // Members list dropdown
        $this->loadClass('dropdown', 'htmlelements');
        $lstMembers = new dropdown(); //$this->newObject( 'dropdown', 'htmlelements' );
        $lstMembers->name = 'list2[]';
        $lstMembers->extra = ' multiple="multiple" style="width:100pt" size="10" ondblclick="moveSelectedOptions(this.form[\'list2[]\'],this.form[\'list1[]\'],true); "';
        foreach ( $memberList as $user ) {
        	
            $fullName = $user['firstname'] . " " . $user['surname'];
            $userPKId = $user['id'];
            $lstMembers->addOption( $userPKId, $fullName );
        }

        $this->loadClass('htmltable', 'htmlelements' );
		$tblLayoutM= new htmlTable(); //&$this->newObject( 'htmltable', 'htmlelements' );
		$tblLayoutM->row_attributes = 'align="center" ';
		$tblLayoutM->width = '100px';
		$tblLayoutM->startRow();
			//$tblLayoutM->addCell( $this->_objLanguage->code2Txt('mod_contextgroups_ttl'.$groupName),null,null,null,'heading' );
		$tblLayoutM->endRow();
		$tblLayoutM->startRow();
			$tblLayoutM->addCell( $lstMembers->show() );
		$tblLayoutM->endRow();
        $this->setVarByRef('lstMembers', $tblLayoutM);

        // Users list dropdown
        $this->loadClass('dropdown', 'htmlelements');
        $lstUsers = new dropdown(); //( 'dropdown', 'htmlelements' );
        $lstUsers->name = 'list1[]';
        $lstUsers->extra = ' multiple="multiple" style="width:100pt"  size="10" ondblclick="moveSelectedOptions(this.form[\'list1[]\'],this.form[\'list2[]\'],true)"';
        foreach ( $usersList as $user ) {
            $fullName = $user['firstname'] . " " . $user['surname'];
            $userPKId = $user['id'];
            $lstUsers->addOption( $userPKId, $fullName );

        }
		$tblLayoutU= &$this->newObject( 'htmltable', 'htmlelements' );
		$tblLayoutU->row_attributes = 'align="center"';
		$tblLayoutU->width = '100px';
		$tblLayoutU->startRow();
			$tblLayoutU->addCell( $this->_objLanguage->code2Txt('mod_contextgroups_ttlUsers','contextgroups'),'10%',null,null,'heading' );
		$tblLayoutU->endRow();
		$tblLayoutU->startRow();
			$tblLayoutU->addCell( $lstUsers->show() );
		$tblLayoutU->endRow();
        $this->setVarByRef('lstUsers', $tblLayoutU );

        // Link method
        $this->loadClass('link', 'htmlelements');
        $lnkSave = new link(); //$this->newObject('link','htmlelements');
        $lnkSave->href  = '#';
        $lnkSave->extra = 'onclick="javascript:';
        $lnkSave->extra.= 'selectAllOptions( document.forms[\'frmManage\'][\'list2[]\'] ); ';
        $lnkSave->extra.= 'document.forms[\'frmManage\'][\'button\'].value=\'save\'; ';
        $lnkSave->extra.= 'document.forms[\'frmManage\'].submit(); "';
        $lnkSave->link  = $this->_objLanguage->languageText( 'word_save' );

        $lnkCancel = new link(); //$this->newObject('link','htmlelements');
        $lnkCancel->href  = '#';
        $lnkCancel->extra = 'onclick="javascript:';
        $lnkCancel->extra.= 'document.forms[\'frmManage\'][\'button\'].value=\'cancel\'; ';
        $lnkCancel->extra.= 'document.forms[\'frmManage\'].submit(); "';
        $lnkCancel->link  = $this->_objLanguage->languageText( 'word_cancel' );

        $ctrlButtons = array();
        $ctrlButtons['lnkSave'] = $lnkSave->show();
        $ctrlButtons['lnkCancel'] = $lnkCancel->show();
        $this->setVar('ctrlButtons',$ctrlButtons);

        $navButtons = array();
        $navButtons['lnkRight']    = $this->navLink('>>','Selected',"forms['frmManage']['list1[]']", "forms['frmManage']['list2[]']");
        $navButtons['lnkRightAll'] = $this->navLink('All >>','All',"forms['frmManage']['list1[]']", "forms['frmManage']['list2[]']");
        $navButtons['lnkLeft']     = $this->navLink('<<','Selected',"forms['frmManage']['list2[]']", "forms['frmManage']['list1[]']");
        $navButtons['lnkLeftAll']  = $this->navLink('All <<','All',"forms['frmManage']['list2[]']", "forms['frmManage']['list1[]']");
        $this->setVar('navButtons',$navButtons);

        $this->loadClass('form', 'htmlelements');
        $frmManage = new form(); //&$this->getObject( 'form', 'htmlelements' );
        $frmManage->name = 'frmManage';
        $frmManage->displayType = '3';
        $frmManage->action = $this->uri ( array( 'action' => $groupName.'_form' ) );
        $frmManage->addToForm("<input type='hidden' name='button' value='' />");
        
        
        
        $this->setVarByRef('frmManage', $frmManage );

        $title = $this->_objLanguage->code2Txt(
            'mod_contextgroups_ttlManageMembers','contextgroups', array(
                'GROUPNAME'=>$groupName,
                'TITLE'=>$this->_objDBContext->getTitle() )
            );
        $this->setVar('title', $title );

        return 'manage_tpl.php';
    }

    /**
    * Method to create a navigation button link
    */
    function navLink( $linkText, $moveType, $from, $to )
    {
        $link = $this->newObject('link','htmlelements');
        $link->href  = '#';
        $link->extra = 'onclick="javascript:';
        $link->extra.= 'move'.$moveType.'Options';
        $link->extra.= '( document.'.$from;
        $link->extra.= ', document.'.$to;
        $link->extra.= ', true );"';
        $link->link  = htmlspecialchars( $linkText );
        return $link->show();
    }
    /**
    * Method to show the error message.
    * @param  string The error code.
    * @return string template name.
    */
    function showError( $errCode )
    {
        // Extracts error, action, title for this error
        extract($this->errCodes[$errCode]);

        // Translate text.
        $errMessage = $this->_objLanguage->code2Txt($error,array('ACTION'=>$action));
        $errTitle = $this->_objLanguage->code2Txt($title);

        // Set template variables.
        $this->setVar('title', $errTitle );
        $this->setVar('errMessage',$errMessage);
        return 'error_tpl.php';
    }

    /**
    * Method to redirect to another module
    * @param  string The link text.
    * @param  string The module to be redirected to.
    * @param  array  The parameters that are needed.
    * @return string HTML link element.
    */
    function linkToModule( $linkName, $moduleName, $params = array() )
    {
        $link = $this->newObject('link','htmlelements');
        $link->href=$this->uri( $params,$moduleName);
        $link->link = $this->_objLanguage->languageText($linkName);
        return $link->show();
    }

    /**
    * Method to redirect to previous page
    * @param  string The link text.
    * @return string HTML link element.
    */
    function linkBack( $linkName )
    {
        $link = $this->newObject('link','htmlelements');
        $link->href= "javascript:history.back();";
        $link->link = $this->_objLanguage->languageText($linkName);
        return $link->show();
    }

    /**
    * Method to redirect to context home.
    * @return string HTML link element.
    */
    function linkToContextHome()
    {
        $lblContextHome = $this->_objLanguage->languageText( "word_course" ) . ' ' . $this->_objLanguage->languageText( "word_home" );

        $icnContextHome = &$this->newObject( 'geticon', 'htmlelements' );
        $icnContextHome->setIcon( 'home' );
        $icnContextHome->alt = $lblContextHome;

        $lnkContextHome = &$this->newObject( 'link', 'htmlelements' );
        $lnkContextHome->href = $this->uri( array(), 'context' );
        $lnkContextHome->link = $icnContextHome->show() . $lblContextHome;
        return $lnkContextHome->show();
    }
}
?>