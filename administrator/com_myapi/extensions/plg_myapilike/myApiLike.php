<?php
/*****************************************************************************
 **                                                                         ** 
 **                                         .o.                   o8o  	    **
 **                                        .888.                  `"'  	    **
 **     ooo. .oo.  .oo.   oooo    ooo     .8"888.     oo.ooooo.  oooo  	    **
 **     `888P"Y88bP"Y88b   `88.  .8'     .8' `888.     888' `88b `888  	    **
 **      888   888   888    `88..8'     .88ooo8888.    888   888  888  	    **
 **      888   888   888     `888'     .8'     `888.   888   888  888  	    **
 **     o888o o888o o888o     .8'     o88o     o8888o  888bod8P' o888o      **
 **                       .o..P'                       888             	    **
 **                       `Y8P'                       o888o            	    **
 **                                                                         **
 **                                                                         **
 **   Joomla! 1.5 Plugin myApiContent                                       **
 **   @Copyright Copyright (C) 2011 - Thomas Welton                         **
 **   @license GNU/GPL http://www.gnu.org/copyleft/gpl.html                 **	
 **                                                                         **	
 **   myApiContent is free software: you can redistribute it and/or modify  **
 **   it under the terms of the GNU General Public License as published by  **
 **   the Free Software Foundation, either version 3 of the License, or	    **	
 **   (at your option) any later version.                                   **
 **                                                                         **
 **   myApiContent is distributed in the hope that it will be useful,	    **
 **   but WITHOUT ANY WARRANTY; without even the implied warranty of	    **
 **   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         **
 **   GNU General Public License for more details.                          **
 **                                                                         **
 **   You should have received a copy of the GNU General Public License	    **
 **   along with myApiContent.  If not, see <http://www.gnu.org/licenses/>  **
 **                                                                         **			
 *****************************************************************************/
 
// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

class plgContentmyApiLike extends JPlugin
{

	function onPrepareContent( &$article, &$params, $limitstart )
	{
		if(!file_exists(JPATH_SITE.DS.'plugins'.DS.'system'.DS.'myApiConnectFacebook.php')){ return; }
		
		//this may fire fron a component other than com_content
		if(($article->id != '') && (@$_POST['fb_sig_api_key'] == ''))
		{
			$doc = & JFactory::getDocument();
			
			$plugin = & JPluginHelper::getPlugin('content', 'myApiLike');

			// Load plugin params info
			$myapiparama = new JParameter($plugin->params);
			
			$like_sections = $myapiparama->get('like_sections');
			$like_categories = $myapiparama->get('like_categories');
			$like_show_on = $myapiparama->get('like_show_on');
			$layout_style = $myapiparama->get('layout_style');
			$show_faces = $myapiparama->get('show_faces');
			$color_scheme = $myapiparama->get('color_scheme');
			$verb = $myapiparama->get('verb');
			$width = $myapiparama->get('width');
			$like_style = $myapiparama->get('like_style');
			$like_show = false;
		
			global $facebook;
			
			if($article->sectionid != '')
			{
				if( is_array($like_sections) )
				{	foreach($like_sections as $id)
					{ if($id == $article->sectionid) { $like_show = true; } }
				}
				else{ if($like_sections == $article->sectionid) { $like_show = true; } }
			}
			
			if($article->category != '')
			{
				if( is_array($like_categories) )
				{	foreach($like_categories as $id)
					{ if($id == $article->category) { $like_show = true; } }
				}
				else
				{ if($like_categories == $article->category) { $like_show = true; }	}
			}
			
			if(($like_show) || ($like_show_on == 'all'))
			{
				require_once(JPATH_SITE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php');
				
				$link = JRoute::_(ContentHelperRoute::getArticleRoute($article->slug, $article->catslug, $article->sectionid));
				$u =& JURI::getInstance( JURI::base() );
				$link = 'http://'.$u->getHost().$link;
				$newtext = '<fb:like href="'.$link.'" style="'.$like_style.'" layout="'.$layout_style.'" show_faces="'.$show_faces.'" width="'.$width.'" action="'.$verb.'" colorscheme="'.$color_scheme.'"></fb:like>';
		
				$newtext = $newtext.$article->text;
				$article->text = $newtext;
			}
		}

	}

    function bind( $array, $ignore = '' )
    {
        if (key_exists( 'like_sections', $array ) && is_array( $array['like_sections'] )) {
                $array['like_sections'] = implode( ',', $array['like_sections'] );
        }
		 if (key_exists( 'like_categories', $array ) && is_array( $array['like_categories'] )) {
                $array['like_categories'] = implode( ',', $array['like_categories'] );
        }
 
        return parent::bind( $array, $ignore );
    }

}
