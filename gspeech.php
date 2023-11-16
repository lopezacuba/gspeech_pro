<?php
/**
* @version   $Id: gspeech.php 22 2012-03-25 15:42:53Z info@creative-solutions.net $
* @package   GSpeech
* @copyright Copyright (C) 2008 - 2015 creative-solutions.net. All rights reserved.
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');
jimport( 'joomla.html.parameter' );

/**
 * GSpeech plugin
 *
 */
class  plgSystemGspeech extends JPlugin {

    function __construct( &$subject, $config) {
        parent::__construct( $subject, $config );

        // load plugin parameters and language file
        $this->_plugin = JPluginHelper::getPlugin( 'system', 'gspeech' );
        $this->_params = json_decode( $this->_plugin->params );
        $this -> conf = $config;
        JPlugin::loadLanguage('plg_system_gspeech', JPATH_ADMINISTRATOR);
    }

    function render_styles_scripts() {
        $mainframe = JFactory::getApplication();

        $document = JFactory::getDocument();
        $content = $mainframe->getBody();
        $db = JFactory::getDBO();

        $plugin_version = '2.7.0-pro';
        $scripts = '';

        //check if component or module loaded CCF scripts already, if no, load them
        if (strpos($content,'plugins/system/gspeech/includes/css/gspeech.css') === false) {

            $cssFile = JURI::base(true).'/plugins/system/gspeech/includes/css/gspeech.css?version='.$plugin_version;
            $scripts .= '<link rel="stylesheet" href="'.$cssFile.'" type="text/css" />'."\n";

            $cssFile = JURI::base(true).'/plugins/system/gspeech/includes/css/the-tooltip.css';
            $scripts .= '<link rel="stylesheet" href="'.$cssFile.'" type="text/css" />'."\n";

            $jsFile = JURI::base(true).'/plugins/system/gspeech/includes/js/jquery-1.8.1.min.js';
            $scripts .= '<script src="'.$jsFile.'" type="text/javascript"></script>'."\n";

            $jsFile = JURI::base(true).'/plugins/system/gspeech/includes/js/color.js';
            $scripts .= '<script src="'.$jsFile.'" type="text/javascript"></script>'."\n";

            $jsFile = JURI::base(true).'/plugins/system/gspeech/includes/js/jQueryRotate.2.1.js';
            $scripts .= '<script src="'.$jsFile.'" type="text/javascript"></script>'."\n";

            $jsFile = JURI::base(true).'/plugins/system/gspeech/includes/js/easing.js';
            $scripts .= '<script src="'.$jsFile.'" type="text/javascript"></script>'."\n";

            $jsFile = JURI::base(true).'/plugins/system/gspeech/includes/js/mediaelement-and-player.min.js';
            $scripts .= '<script src="'.$jsFile.'" type="text/javascript"></script>'."\n";

        }

        $pluginParams = new JRegistry( $this -> conf['params'] );
        $speak_any_text = $pluginParams->get( 'speak_any_text');
        if($speak_any_text == 1) {
            $jsFile = JURI::base(true).'/plugins/system/gspeech/includes/js/gspeech.js?version='.$plugin_version;
            $scripts .= '<script src="'.$jsFile.'" type="text/javascript"></script>'."\n";
        }
       
        $jsFile = JURI::base(true).'/plugins/system/gspeech/includes/js/gspeech_pro.js?version='.$plugin_version;
        $scripts .= '<script src="'.$jsFile.'" type="text/javascript"></script>'."\n";

        $content = str_replace('</head>', $scripts . '</head>', $content);
        return $content;
    }
    
    function onAfterInitialise() {
        $mainframe = JFactory::getApplication();
         
        if($mainframe->isClient('administrator')) {
            //check if curl is loaded
            if (!extension_loaded('curl')) {
                JError::raiseWarning(null,'You must have <a href="http://www.php.net/curl" target="_blank">curl</a> library enabled to use GSpeech');
            }
             
            return;
        }
    }
    /**
     * Add JavaScript reloader
     *
     * @access public
     */
    function onAfterRender() {
       $mainframe = JFactory::getApplication();
       
        // Enable only on frontend
        if($mainframe->isClient('administrator'))
            return;

        $content = $mainframe->getBody();
        
        $plugin_version = '2.6.0-pro';

        $pluginParams = new JRegistry( $this -> conf['params'] );

        $gspeech_lang = $pluginParams->get( 'language');
        $speak_any_text = $pluginParams->get( 'speak_any_text');
        $greeting_text = $pluginParams->get( 'greeting_text');
        $greeting_text = $greeting_text == 'blank' ? '' : $greeting_text;

        $render_gspeech_enabled = false;
        if(preg_match('/{gspeech[^}]*?}/si',$content) || $speak_any_text == 1 || $greeting_text != '')
            $render_gspeech_enabled = true;

        if($render_gspeech_enabled)
            $content = $this->render_styles_scripts();
        else
            return;

        // Load user_profile plugin language
        $lang = JFactory::getLanguage();
        $lang->load('plg_system_gspeech', JPATH_ADMINISTRATOR);

        $userRegistered = (JFactory::getUser()->id == 0) ? 0 : 1;
        $base_url = JURI::base();
        
        //$speech_engine = $pluginParams->get( 'speech_engine');
        $tr_tool = 'g';
        $speech_lenght = '100';
        
        $bcp1 = $pluginParams->get( 'bcp1');
        $cp1 = $pluginParams->get( 'cp1');
        $bca1 = $pluginParams->get( 'bca1');
        $ca1 = $pluginParams->get( 'ca1');
        $spop1 = $pluginParams->get( 'spop1');
        $spop1_ = $spop1 / 100;
        $spoa1 = $pluginParams->get( 'spoa1');
        $animation_time_1 = $pluginParams->get( 'animation_time_1');
        $speaker_type_1 =  $pluginParams->get('speaker_type_1');
        $speaker_size_1 =  $pluginParams->get('speaker_size_1');
        $tooltip_1 =  $pluginParams->get('tooltip_1');
        
        $bcp2 = $pluginParams->get( 'bcp2');
        $cp2 = $pluginParams->get( 'cp2');
        $bca2 = $pluginParams->get( 'bca2');
        $ca2 = $pluginParams->get( 'ca2');
        $spop2 = $pluginParams->get( 'spop2');
        $spop2_ = $spop2 / 100;
        $spoa2 = $pluginParams->get( 'spoa2');
        $animation_time_2 = $pluginParams->get( 'animation_time_2');
        $speaker_type_2 =  $pluginParams->get('speaker_type_2');
        $speaker_size_2 =  $pluginParams->get('speaker_size_2');
        $tooltip_2 =  $pluginParams->get('tooltip_2');
        
        $bcp3 = $pluginParams->get( 'bcp3');
        $cp3 = $pluginParams->get( 'cp3');
        $bca3 = $pluginParams->get( 'bca3');
        $ca3 = $pluginParams->get( 'ca3');
        $spop3 = $pluginParams->get( 'spop3');
        $spop3_ = $spop3 / 100;
        $spoa3 = $pluginParams->get( 'spoa3');
        $animation_time_3 = $pluginParams->get( 'animation_time_3');
        $speaker_type_3 =  $pluginParams->get('speaker_type_3');
        $speaker_size_3 =  $pluginParams->get('speaker_size_3');
        $tooltip_3 =  $pluginParams->get('tooltip_3');
        
        $bcp4 = $pluginParams->get( 'bcp4');
        $cp4 = $pluginParams->get( 'cp4');
        $bca4 = $pluginParams->get( 'bca4');
        $ca4 = $pluginParams->get( 'ca4');
        $spop4 = $pluginParams->get( 'spop4');
        $spop4_ = $spop4 / 100;
        $spoa4 = $pluginParams->get( 'spoa4');
        $animation_time_4 = $pluginParams->get( 'animation_time_4');
        $speaker_type_4 =  $pluginParams->get('speaker_type_4');
        $speaker_size_4 =  $pluginParams->get('speaker_size_4');
        $tooltip_4 =  $pluginParams->get('tooltip_4');
        
        $bcp5 = $pluginParams->get( 'bcp5');
        $cp5 = $pluginParams->get( 'cp5');
        $bca5 = $pluginParams->get( 'bca5');
        $ca5 = $pluginParams->get( 'ca5');
        $spop5 = $pluginParams->get( 'spop5');
        $spop5_ = $spop5 / 100;
        $spoa5 = $pluginParams->get( 'spoa5');
        $animation_time_5 = $pluginParams->get( 'animation_time_5');
        $speaker_type_5 =  $pluginParams->get('speaker_type_5');
        $speaker_size_5 =  $pluginParams->get('speaker_size_5');
        $tooltip_5 =  $pluginParams->get('tooltip_5');
        
        $speaker_types_array = array("1" => $speaker_type_1, "2" => $speaker_type_2, "3" => $speaker_type_3, "4" => $speaker_type_4, "5" => $speaker_type_5);
        $speaker_sizes_array = array("1" => $speaker_size_1, "2" => $speaker_size_2, "3" => $speaker_size_3, "4" => $speaker_size_4, "5" => $speaker_size_5);
        $tooltips_array = array("1" => $tooltip_1, "2" => $tooltip_2, "3" => $tooltip_3, "4" => $tooltip_4, "5" => $tooltip_5);
        
        $code_path = $base_url.'plugins/system/gspeech/includes/';

        $speech_title = JText::_('PLG_GSPEECH_SPEECH_BLOCK_TITLE');
        $speech_powered_by = JText::_('PLG_GSPEECH_SPEECH_POWERED_BY');
        $gspeech_content = <<<EOM
        <span id="sexy_tooltip_title"><span class="the-tooltip top left {$tooltip_2}"><span class="tooltip_inner">{$speech_title}</span></span></span>
        <div id="sound_container" class="sound_div sound_div_basic size_$speaker_size_2 $speaker_type_2" title="" style=""><div id="sound_text"></div>
        </div><div id="sound_audio"></div>
        <script type="text/javascript">
            var players = new Array(),
                blink_timer = new Array(),
                rotate_timer = new Array(),
                lang_identifier = '{$gspeech_lang}',
                selected_txt = '',
                sound_container_clicked = false,
                sound_container_visible = true,
                blinking_enable = true,
                basic_plg_enable = true,
                pro_container_clicked = false,
                streamerphp_folder = '{$code_path}',
                translation_tool = '{$tr_tool}',
                //translation_audio_type = 'audio/x-wav',
                translation_audio_type = 'audio/mpeg',
                speech_text_length = {$speech_lenght},
                blink_start_enable_pro = true,
                createtriggerspeechcount = 0,
                speechtimeoutfinal = 0,
                speechtxt = '',
                userRegistered = "{$userRegistered}",
                gspeech_bcp = ["{$bcp1}","{$bcp2}","{$bcp3}","{$bcp4}","{$bcp5}"],
                gspeech_cp = ["{$cp1}","{$cp2}","{$cp3}","{$cp4}","{$cp5}"],
                gspeech_bca = ["{$bca1}","{$bca2}","{$bca3}","{$bca4}","{$bca5}"],
                gspeech_ca = ["{$ca1}","{$ca2}","{$ca3}","{$ca4}","{$ca5}"],
                gspeech_spop = ["{$spop1}","{$spop2}","{$spop3}","{$spop4}","{$spop5}"],
                gspeech_spoa = ["{$spoa1}","{$spoa2}","{$spoa3}","{$spoa4}","{$spoa5}"],
                gspeech_animation_time = ["{$animation_time_1}","{$animation_time_2}","{$animation_time_3}","{$animation_time_4}","{$animation_time_5}"];
        </script>
        <!--[if (gte IE 6)&(lte IE 8)]>
        <script defer src="{$code_path}js/nwmatcher-1.2.4-min.js"></script>
        <script defer src="{$code_path}js/selectivizr-min.js"></script>
        <![endif]-->
        <style type="text/css">.gspeech_style_,.gspeech_style_1{background-color:{$bcp1};color:{$cp1};}.gspeech_style_2{background-color:{$bcp2};color:{$cp2};}.gspeech_style_3{background-color:{$bcp3};color:{$cp3};}.gspeech_style_4{background-color:{$bcp4};color:{$cp4};}.gspeech_style_5{background-color:{$bcp5};color:{$cp5};}</style>
        <style type="text/css">.gspeech_style_.active,.gspeech_style_1.active{background-color:{$bca1} !important;color:{$ca1} !important;}.gspeech_style_2.active{background-color:{$bca2} !important;color:{$ca2} !important;}.gspeech_style_3.active{background-color:{$bca3} !important;color:{$ca3} !important;}.gspeech_style_4.active{background-color:{$bca4} !important;color:{$ca4} !important;}.gspeech_style_5.active{background-color:{$bca5} !important;color:{$ca5} !important;}</style>
        <style type="text/css">.sound_div_,.sound_div_1{opacity:{$spop1_};filter: alpha(opacity = {$spop1})}.sound_div_2{opacity:{$spop2_};filter: alpha(opacity = {$spop2})}.sound_div_3{opacity:{$spop3_};filter: alpha(opacity = {$spop3})}.sound_div_4{opacity:{$spop4_};filter: alpha(opacity = {$spop4})}.sound_div_5{opacity:{$spop5_};filter: alpha(opacity = {$spop5})}</style>
        <style type="text/css">
           ::selection {
                background: {$bca2};
                color: {$ca2};
            }
            ::-moz-selection {
                background: {$bca2};
                color: {$ca2};
            }
        </style>
EOM;

        // Makes appropriate changes to the HTML
        function strip_htm($matches) {
            
            $speech_title = JText::_('PLG_GSPEECH_SPEECH_BLOCK_TITLE');
            $speech_powered_by = JText::_('PLG_GSPEECH_SPEECH_POWERED_BY');
         
            $mainframe = JFactory::getApplication();
            $sitename = $mainframe->getCfg('sitename');
            $userRegistered = (JFactory::getUser()->id == 0) ? 0 : 1;
            $username = JFactory::getUser()->username;
            $realname = JFactory::getUser()->name;
            
            if($userRegistered == 0 && $matches[10] == 1) {
                if($matches[16] != 1) {
                    return $matches[17];
                }
                else return;
            }
            if($userRegistered == 1 && $matches[10] == 2) {
                if($matches[16] != 1) {
                    return $matches[17];
                }
                else return;
            }
            
            $htm = strip_tags($matches[17]);
            $htm = preg_replace('/<script\b[^>]*>(.*?)<\/script>/si', "", $htm);
            $htm = preg_replace('/<style\b[^>]*>(.*?)<\/style>/si', "", $htm);
            $htm = str_replace(array("\"","'"),"",$htm);
            
            $htm = str_replace("SITENAME",$sitename,$htm);
            if($userRegistered == 1) {
                $htm = str_replace("USERNAME",$username,$htm);
                $htm = str_replace("REALNAME",$realname,$htm);
            }
            
            $htm_original = str_replace("SITENAME",$sitename,$matches[17]);
            if($userRegistered == 1) {
                $htm_original = str_replace("USERNAME",$username,$htm_original);
                $htm_original = str_replace("REALNAME",$realname,$htm_original);
            }
            
            $hidespeaker_pre = $matches[16] == 1 ? '<div style="display:none">' : '';
            $hidespeaker_af = $matches[16] == 1 ? '</div>' : '';
            $hidespeaker_index = $matches[16] == 1 ? 1 : 0;
             
            $style_index = $matches[2] == '' ? 1 : $matches[2];
            
            return $hidespeaker_pre.'
            <span class="gspeech_selection gspeech_style_'.$style_index.'" roll="'.$style_index.'">'.$htm_original.'</span>
            <span class="gspeech_pro_main_wrapper">&nbsp;
            <span class="sexy_tooltip"><span class="the-tooltip top left sexy_tooltip_'.$style_index.'"><span class="tooltip_inner">'.$speech_title.'</span></span></span>
            <span class="sound_container_pro sound_div_'.$style_index.'" language="'.$matches[4].'" roll="'.$style_index.'" autoplaypro="'.$matches[6].'" speechtimeout="'.$matches[8].'" selector="'.$matches[12].'" hidespeaker="'.$hidespeaker_index.'" eventpro="'.$matches[14].'" title="" style=""><span class="sound_text_pro">'.$htm.'</span></span>
            </span>'.$hidespeaker_af;
        }
        
        $greeting_text = preg_replace_callback('/{gspeech( style=([\d]*?))?( language=([\S]*?))?( autoplay=([\d]*?))?( speechtimeout=([\d]*?))?( registered=([\d]*?))?( selector=(.*?))?( event=(.*?))?( hidespeaker=([\d]*?))?[\s]?}(.*?){\/gspeech}/si', 'strip_htm', $greeting_text);
        $greeting_text = preg_replace('/{gspeech[^}]*?}/si', '', $greeting_text);
        $greeting_text = preg_replace('/{\/gspeech}/si', '', $greeting_text);
        $greeting_text = str_replace('sound_container_pro','sound_container_pro greeting_block',$greeting_text);
        
        $content = preg_replace_callback('/{gspeech( style=([\d]*?))?( language=([\S]*?))?( autoplay=([\d]*?))?( speechtimeout=([\d]*?))?( registered=([\d]*?))?( selector=(.*?))?( event=(.*?))?( hidespeaker=([\d]*?))?[\s]?}(.*?){\/gspeech}/si', 'strip_htm', $content);
        $content = preg_replace('/{gspeech[^}]*?}/si', '', $content);
        $content = preg_replace('/{\/gspeech}/si', '', $content);
        
        $style_index = 1;
        $speaker_type = $speaker_types_array[$style_index];
        $speaker_size = $speaker_sizes_array[$style_index];
        $replace_val1 = 'sound_div_1';
        $replace_val2 = 'sound_div_1 size_'.$speaker_size.' '.$speaker_type;
        $content = str_replace($replace_val1, $replace_val2, $content);
        $tooltip = $tooltips_array[$style_index];
        $content = str_replace('sexy_tooltip_1', $tooltip, $content);
        
        $style_index = 2;
        $speaker_type = $speaker_types_array[$style_index];
        $speaker_size = $speaker_sizes_array[$style_index];
        $replace_val1 = 'sound_div_2';
        $replace_val2 = 'sound_div_2 size_'.$speaker_size.' '.$speaker_type;
        $content = str_replace($replace_val1, $replace_val2, $content);
        $tooltip = $tooltips_array[$style_index];
        $content = str_replace('sexy_tooltip_2', $tooltip, $content);
        
        $style_index = 3;
        $speaker_type = $speaker_types_array[$style_index];
        $speaker_size = $speaker_sizes_array[$style_index];
        $replace_val1 = 'sound_div_3';
        $replace_val2 = 'sound_div_3 size_'.$speaker_size.' '.$speaker_type;
        $content = str_replace($replace_val1, $replace_val2, $content);
        $tooltip = $tooltips_array[$style_index];
        $content = str_replace('sexy_tooltip_3', $tooltip, $content);
        
        $style_index = 4;
        $speaker_type = $speaker_types_array[$style_index];
        $speaker_size = $speaker_sizes_array[$style_index];
        $replace_val1 = 'sound_div_4';
        $replace_val2 = 'sound_div_4 size_'.$speaker_size.' '.$speaker_type;
        $content = str_replace($replace_val1, $replace_val2, $content);
        $tooltip = $tooltips_array[$style_index];
        $content = str_replace('sexy_tooltip_4', $tooltip, $content);
        
        $style_index = 5;
        $speaker_type = $speaker_types_array[$style_index];
        $speaker_size = $speaker_sizes_array[$style_index];
        $replace_val1 = 'sound_div_5';
        $replace_val2 = 'sound_div_5 size_'.$speaker_size.' '.$speaker_type;
        $content = str_replace($replace_val1, $replace_val2, $content);
        $tooltip = $tooltips_array[$style_index];
        $content = str_replace('sexy_tooltip_5', $tooltip, $content);

        // 2.7.0
        $enable_autoplay_once = true;
        // unset($_SESSION['gs_page_loaded_count']);

        // start session, if needed
        if (session_status() !== PHP_SESSION_ACTIVE || session_id() === ""){
            session_start(); 
        }

        // set session
        if(!isset($_SESSION['gs_page_loaded_count'])) {
            $_SESSION['gs_page_loaded_count'] = 0;
        }

        $gs_page_loaded_count = (int) $_SESSION['gs_page_loaded_count'];
        $gs_page_loaded_count_new = $enable_autoplay_once ? $gs_page_loaded_count + 1 : 0;

        $_SESSION['gs_page_loaded_count'] = $gs_page_loaded_count_new;

        $autoplay_enabled = ($enable_autoplay_once && $gs_page_loaded_count != 0) ? 0 : 1;

        $greeting_text = str_replace('<span class="sound_container_pro', '<span data-autoplay_="'.$autoplay_enabled.'" class="sound_container_pro', $greeting_text);

        $content = str_replace('<span class="sound_container_pro', '<span data-gs_loaded_count="'.$gs_page_loaded_count_new.'" data-autoplay_="'.$autoplay_enabled.'" class="sound_container_pro', $content);
        // END 2.7.0
        
        $is_htm = strpos($content, '<body');
        if($is_htm)
            $content = preg_replace('/<body([^>]*)?>/si', "<body$1>" . $greeting_text, $content);
        $content = str_replace('</body>', $gspeech_content . '</body>', $content);
        
        $mainframe->setBody($content);
    }
}