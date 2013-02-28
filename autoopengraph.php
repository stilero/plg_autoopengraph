<?php
/**
 * Description of AutoOpenGraph
 *
 * @version  2.2
 * @author Daniel Eliasson (joomla@stilero.com)
 * @copyright  (C) 2012-dec-27 Stilero Webdesign (www.stilero.com)
 * @category Plugins
 * @license	GPLv2
 */

// no direct access
defined('_JEXEC') or die('Restricted access'); 
define('AOG_CLASS_FOLDER', dirname(__FILE__).DS.'autoopengraph'.DS.'includes'.DS);
// import library dependencies
jimport('joomla.plugin.plugin');
JLoader::register('Opengraph', AOG_CLASS_FOLDER.'opengraph.php');

class plgContentAutoopengraph extends JPlugin {
    var $Article;
    var $ArticleClass;
    protected $OpenGraph;
    var $classNames;
    var $ogTagsAdded = FALSE;
    protected $articleClassFolder;
    protected static $articleClassFile = 'aogjarticle.php';
    protected static $opt_off = 0;
    protected static $opt_title_article = 1;
    protected static $opt_title_custom = 2;
    protected static $opt_type_article = 1;
    protected static $opt_type_website = 2;
    protected static $opt_type_book = 3;
    protected static $opt_type_profile = 4;
    protected static $opt_type_custom = 5;
    protected static $opt_img_prio_first = 1;
    protected static $opt_img_prio_intro = 2;
    protected static $opt_img_prio_full = 3;
    protected static $opt_img_prio_class = 4;
    protected static $opt_img_prio_custom = 5;
    protected static $opt_locale_site = 1;
    protected static $opt_locale_article = 2;
    protected static $opt_locale_custom = 3;
    protected static $opt_sitename_site = 1;
    protected static $opt_sitename_custom = 2;
    protected static $opt_desc_meta = 1;
    protected static $opt_desc_intro = 2;
    protected static $opt_desc_site = 3;
    protected static $opt_desc_custom = 4;

    function plgContentAutoopengraph ( &$subject, $config ) {
        parent::__construct( $subject, $config );
        $language = JFactory::getLanguage();
        $language->load('plg_content_autoopengraph', JPATH_ADMINISTRATOR, 'en-GB', true);
        $language->load('plg_content_autoopengraph', JPATH_ADMINISTRATOR, null, true);
        $this->classNames = array(
            'com_article'       =>  'AOGJArticle',
            'com_content'       =>  'AOGJArticle',
            'com_k2'            =>  'AOGK2Article',
            'com_zoo'           =>  'AOGZooArticle',
            'com_virtuemart'    =>  'AOGVMArticle'
        );
    }
    
    /**
     * Method is called by the view
     * 
     * @var string  $context    The context of the content passed to the plugin
     * @var object  $article    content object. Note $article->text is also available
     * @var object  $params     content params
     * @var integer $limitstart The 'page' number
     * @return void
     * @since 1.6
     */        
    public function onContentPrepare($context, &$article, &$params, $limitstart=0){
        if( $context != 'com_content.article' && $context !='com_virtuemart.productdetails' && $context !='com_k2.item'){
            return;
        }
        if($this->ogTagsAdded){
            return;
        }
        if(!$this->loadClasses($article)){
            return;
        }
        $this->OpenGraph->insertTags(); 
        $this->ogTagsAdded = TRUE;
    }
    
  
    /**
     * The first stage in preparing content for output and is the most common point for content orientated plugins to do their work.
     * 
     * @var object  $article    A reference to the article that is being rendered by the view.
     * @var array   $params     A reference to an associative array of relevant parameters.
     * @var integer $limitstart An integer that determines the "page" of the content that is to be generated.
     * @return void
     * @since 1.5
     */
    public function onPrepareContent( &$article, &$params, $limitstart=0 ){
        if($this->ogTagsAdded){
            return;
        }
        if(!$this->loadClasses($article)){
            return;
        }
        if(!$this->ArticleClass->isArticle()){
            return;
        }
        $this->OpenGraph->insertTags(); 
        $this->ogTagsAdded = TRUE;
    }
     
    protected function prepareOpenGraph(){
        $this->OpenGraph = new Opengraph();
        $this->OpenGraph->title = $this->title();
        $this->OpenGraph->type = $this->type();
        $this->OpenGraph->description = $this->desc();
        $this->OpenGraph->locale = $this->locale();
        $this->OpenGraph->siteName = $this->siteName();
        $this->OpenGraph->imgSrc = $this->image();
        $this->OpenGraph->url = $this->url();
        $this->OpenGraph->timePublish = $this->timePublish();
        $this->OpenGraph->timeModified = $this->timeModified();
        $this->OpenGraph->section = $this->section();
        $this->OpenGraph->fbAppId = $this->fbAppID();
    }
    
    private function loadClasses($article){
       $component = JRequest::getVar('option');
        if(array_key_exists($component, $this->classNames)){
            $className = $this->classNames[$component];
            JLoader::register( $className, AOG_CLASS_FOLDER.self::$articleClassFile);
            $this->ArticleClass = new $className($article);
            $this->Article = $this->ArticleClass->getArticleObj();
            $this->prepareOpenGraph();
            return TRUE;
        }
        return false;
   }
   
   protected function title(){
       $title = '';
        switch ($this->params->def('og-title')) {
            case self::$opt_title_article :
                $title = $this->Article->title;
                break;
            case self::$opt_title_custom :
                $title = $this->params->def('og-title-custom');
                break;
            default:
                break;
        }
        return $title;
   }
   
   protected function type(){
        $type = '';
        switch ($this->params->def('og-type')) {
            case self::$opt_type_article:
                $type = 'article';
                break;
            case self::$opt_type_website:
                $type = 'website';
                break;
            case self::$opt_type_book:
                $type = 'book';
                break;
            case self::$opt_type_profile:
                $type = 'profile';
                break;
            case self::$opt_type_custom:
                $type = $this->params->def('og-type-custom');
                break;
            default:
                break;
        }
        return $type;
    }

    protected function desc(){
        $desc = '';
        switch ($this->params->def('og-desc')) {
            case self::$opt_desc_meta:
                $desc = $this->Article->metadesc;
                break;
            case self::$opt_desc_intro:
                $desc = $this->Article->introtext;
                break;
            case self::$opt_desc_site:
                $joomlaConfig = JFactory::getConfig();
                $joomlaSiteName = $joomlaConfig->getValue( 'config.MetaDesc' );
                $desc = $joomlaSiteName;
                break;
            case self::$opt_desc_custom:
                $desc = $this->params->def('og-desc-custom');
                break;
            default:
                break;
        }
        return $desc;
    }

    private function locale(){
        $language = JFactory::getLanguage();
        $locale = str_replace( "-", "_", $language->getTag() );
        switch ($this->params->def('og-locale')) {
            case self::$opt_locale_custom:
                $locale = str_replace( "-", "_", $this->params->def('og-locale-custom') );
                break;
        }
        return $locale;
    }

    protected function siteName(){
        $joomlaConfig = JFactory::getConfig();
        $siteName = $joomlaConfig->getValue( 'config.sitename' );
        switch ($this->params->def('og-sitename')) {
            case self::$opt_sitename_custom:
                $siteName = $this->params->def('og-sitename-custom');
                break;
            default:
                break;
        }
        return $siteName;
    }

    protected function getImageWithClass($images, $cssClass){
        if( (!isset($images)) || (empty ($images))  ){
            return;
        }
        foreach ($images as $image) {
            if($image['class'] == $cssClass){
                return $image['src'];
            }
        }
    }
    
    protected function imageWithOption($option){
        switch ($option) {
            case self::$opt_img_prio_first:
                $image = (isset($this->Article->firstContentImage)) ? $this->Article->firstContentImage : '';
                break;
            case self::$opt_img_prio_intro:
                $image = (isset($this->Article->introImage)) ? $this->Article->introImage : '';
                break;
            case self::$opt_img_prio_full:
                $image = (isset($this->Article->fullTextImage)) ? $this->Article->fullTextImage : '';
                break;
            case self::$opt_img_prio_class:
                $images = (!empty($this->Article->imageArray)) ? $this->Article->imageArray : null;
                $cssClass = $this->params->def('og-img-class');
                $classImage = $this->getImageWithClass($images, $cssClass);
                $image = (isset($classImage)) ? $classImage : '';
                break;
            case self::$opt_img_prio_custom:
                if($this->params->def('og-img-custom') != ''){
                    $image = 'images/'.$this->params->def('og-img-custom');
                }
                break;
            default:
                return;
                break;
        }
        if($image != ""){
            $image = preg_match('/http/', $image)? $image : JURI::root().$image;
            return $image;
        }
    }

    
    protected function image(){
        $image = $this->imageWithOption($this->params->def('og-img-prio1'));
        if($image == "" ){
            $image = $this->imageWithOption($this->params->def('og-img-prio2'));
        }
        if($image == "" ){
            $image = $this->imageWithOption($this->params->def('og-img-prio3'));
        }
        return $image;
    }

    protected function url(){
        $url = $this->Article->url;
        return $url;
    }
    
    protected function timePublish(){
        return date('c', strtotime( $this->Article->publish_up) );
    }
    
    protected function timeModified(){
        return date('c', strtotime( $this->Article->modified) );
    }
    
    protected function section(){
        return $this->Article->category_title;
    }
    
    protected function fbAppID(){
        return $this->params->def('og-fbappid');
    }
} //End Class