<?php
class page {

    private $ViewModel;
    private $application_config = null;

    public function _getFilesToInclude($files_to_include){
        foreach ($files_to_include as $include){
            include($include);
        }
    }

    public function _getCss($csss){
        $css_string = '';
        foreach ($csss as $css){
            $css_string .= '<link href="'.$css.'" rel="stylesheet">';
        }
        return $css_string;
    }

    public function _getJs($jss){
        $js_string = '';
        foreach ($jss as $js){
            $js_string .= '<script src="'.$js.'"></script>';
        }
        return $js_string;
    }

    public function _getTitle($module){
        return $module;
    }

    public function getToken(){
        $token = new token();
        return $token->getToken();
    }

    public function getLocalization($application_config, $module, $controller, $action){
        $localization = new localization($application_config);
        return $localization->getLocalization($application_config['language'], $module, $controller, $action);
    }
    
    public function getResponse($application_configs, $module, $action, $post, $optional_parameters){
        $method = '_action_'.$action;
        return $this->$method($application_configs, $module, $action, $post, $optional_parameters);
    }

    public function _getInitScript($page_related_scripts = ''){
        $localization = $this->getLocalization($this->application_config, '', '', 'default');
        ?>
        <script>
            var token = "<?php if(isset($this->application_config['token']) && $this->application_config['token'] !== ''){echo $this->application_config['token'];}else{echo '';}?>";
            var APPLICATION_URL = "<?php echo $this->application_config['APPLICATION_URL']; ?>";
            var error_log_done = '<?php if(isset($localization)){echo $localization['error-log-done'];}?>';
            var error_log_fail = '<?php if(isset($localization)){echo $localization['error-log-fail'];}?>';

            window.onerror = function (message, source, lineno, columnno, error) {
                console.log("Error: " + message + " at line: " + lineno + " source: " + source + " columnNo: " + columnno + " error: " + error);
                sendError('onerror', message, source, lineno, columnno, error);
            }

            function sendError(position, message, source, lineno, columnno, error){
                $.post( APPLICATION_URL + "free-logger-aiwhoaiwje.php", { position: position, token: token, message: message, source: source, lineno: lineno, columnno: columnno, error: error})
                .done(function(data) {
                    console.log(data);
                    alert(error_log_done);
                })
                .fail(function(data) {
                    console.log(data.responseText);
                    alert(error_log_fail);
                });
                return true;
            }

            <?php echo $page_related_scripts;?>
        </script>
    <?php
    }
    public function currentUser(){
        return new currentuser($this->application_config);
    }
    public function headScript(){
        return new headScript();
    }
    public function headLink(){
        return new headLink();
    }
    public function escapeHtml($module){
        return $module;
    }
    public function basePath($path = NULL){
        global $application_configs;
        if(isset($path) && $path != ''){
            return $application_configs['APPLICATION_URL'].$path;
        }else{
            return $application_configs['APPLICATION_URL'];
        }
    }
    public function inlineScript(){
        return '';
    }
    public function params(){
        return new params();
    }
    public function _include($modulename, $_view_folder, $_action, $__action){
        global $application_configs;
        $this->ViewModel = $this->$__action();

//#30042021 todo: fix this section
//        $_page_path = $application_configs['PRIVATE_FOLDER_MODULE'].$modulename.'/view/'.$_view_folder.'/'.$_action.'/'.$_action.'.phtml';
$_container = 'index'; //# todo: fix this section

        $_page_path = $application_configs['PRIVATE_FOLDER_MODULE'].$modulename.'/view/'.$_view_folder.'/'.$_container.'/'.$_action.'.phtml';
        include($application_configs['PRIVATE_FOLDER_MODULE'].'Application/view/layout/layout.phtml');
    }
    public function __construct($application_config) {
        if(isset($application_config)){
            $this->application_config = $application_config;
        }else{
            die('no $application_config');
        }
    }
    public function _get_application_configs(){
        if(isset($this->application_config)){
            return $this->application_config;
        }
    }
    
}
