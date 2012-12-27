<?php
/**
 * OpenGraph Class
 *
 * @version  1.0
 * @package Stilero
 * @subpackage AutoOpenGraph
 * @author Daniel Eliasson (joomla@stilero.com)
 * @copyright  (C) 2012-dec-27 Stilero Webdesign (www.stilero.com)
 * @license	GNU General Public License version 2 or later.
 * @link http://www.stilero.com
 */

// no direct access
defined('_JEXEC') or die('Restricted access'); 

class Opengraph{
    
    protected $title;
    protected $type;
    protected $description;
    protected $locale;
    protected $siteName;
    protected $imgSrc;
    protected $url;
    protected $timePublish;
    protected $timeModified;
    protected $section;
    protected $fbAppId;
    protected static $OG_TITLE = 'og:title';
    protected static $OG_TYPE = 'og:type';
    protected static $OG_DESC = 'og:description';
    protected static $OG_LOCALE = 'og:locale';
    protected static $OG_SITENAME = 'og:site_name';
    protected static $OG_IMAGE = 'og:image';
    protected static $OG_URL = 'og:url';
    protected static $OG_TIMEPUB = 'article:published_time';
    protected static $OG_TIMEMOD = 'article:modified_time';
    protected static $OG_SECTION = 'article:section';
    protected static $OG_FBAPPID = 'fb:app_id';
    
    /**
     * Class for inserting OG-tags in the HTML header
     * @param string $title
     * @param string $type
     * @param string $desc
     * @param string $locale
     * @param string $siteName
     * @param string $img
     * @param string $url
     * @param string $timePublish
     * @param string $timeModified
     * @param string $section
     * @param string $fbAppId
     */
    public function __construct($title='', $type='', $desc='', $locale='', $siteName='', $img='', $url='', $timePublish='', $timeModified='', $section='', $fbAppId='') {
        $this->title = $title;
        $this->type = $type;
        $this->description = $desc;
        $this->locale = $locale;
        $this->siteName = $siteName;
        $this->imgSrc = $img;
        $this->url = $url;
        $this->timePublish = $timePublish;
        $this->timeModified = $timeModified;
        $this->section = $section;
        $this->fbAppId = $fbAppId;
    }
    
    /**
     * Method for creating and inserting meta tags in the html header
     * @param String $property
     * @param String $content
     */
    protected function insertTag($property, $content){
        $document = JFactory::getDocument();
        $doctype    = $document->getType();
        if ( $doctype !== 'html' || $content == '') {
            return;
        }
        $sanitizedContent = htmlentities(strip_tags($content), ENT_QUOTES, "UTF-8");
        $meta = '<meta property="'.$property.'" content="'.$sanitizedContent.'" />';
        $document->addCustomTag($meta);
    }
    
    /**
     * Method for inserting tags in the HTML header
     * @return none
     */
    public function insertTags(){
        $this->insertTag(self::$OG_TITLE, $this->title);
        $this->insertTag(self::$OG_TYPE, $this->type);
        $this->insertTag(self::$OG_DESC, $this->description);
        $this->insertTag(self::$OG_LOCALE, $this->locale);
        $this->insertTag(self::$OG_SITENAME, $this->siteName);
        $this->insertTag(self::$OG_IMAGE, $this->imgSrc);
        $this->insertTag(self::$OG_URL, $this->url);
        $this->insertTag(self::$OG_TIMEPUB, $this->timePublish);
        $this->insertTag(self::$OG_TIMEMOD, $this->timeModified);
        $this->insertTag(self::$OG_SECTION, $this->section);
        $this->insertTag(self::$OG_FBAPPID, $this->fbAppId);
    }

    public function __set($name, $value) {
        $this->$name = $value;
    }
    
    public function __get($name) {
        return $this->$name;
    }
}
