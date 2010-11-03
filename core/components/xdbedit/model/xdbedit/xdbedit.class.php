<?php
/**
 * stellenboerse
 *
 * @author Bruno Perner
 *
 *
 * @package stellenboerse
 */
/**
 * @package stellenboerse
 * @subpackage controllers
 */
class Xdbedit {
    /**
     * @access protected
     * @var array A collection of preprocessed chunk values.
     */
    protected $chunks = array();
    /**
     * @access public
     * @var modX A reference to the modX object.
     */
    public $modx = null;
    /**
     * @access public
     * @var array A collection of properties to adjust Quip behaviour.
     */
    public $config = array();

    /**
     * @access public
     * @var array A collection of custom-properties to set processor-pathes and form-configs.
     */
    public $customconfigs = array();
    /**
     * The Quip Constructor.
     *
     * This method is used to create a new Quip object.
     *
     * @param modX &$modx A reference to the modX object.
     * @param array $config A collection of properties that modify Quip
     * behaviour.
     * @return Quip A unique Quip instance.
     */
    function __construct(modX &$modx,array $config = array()) {
        $this->modx =& $modx;

        /* allows you to set paths in different environments
         * this allows for easier SVN management of files
         */
        $corePath = $this->modx->getOption('xdbedit.core_path',null,$modx->getOption('core_path').'components/xdbedit/');
        $assetsPath = $this->modx->getOption('xdbedit.assets_path',null,$modx->getOption('assets_path').'components/xdbedit/');
        $assetsUrl = $this->modx->getOption('xdbedit.assets_url',null,$modx->getOption('assets_url').'components/xdbedit/');

        $this->config = array_merge(array(
            //'debug' => true,
			'corePath' => $corePath,
            'modelPath' => $corePath.'model/',
            'processorsPath' => $corePath.'processors/',
            'controllersPath' => $corePath.'controllers/',
            'chunksPath' => $corePath.'elements/chunks/',
            'snippetsPath' => $corePath.'elements/snippets/',

            'baseUrl' => $assetsUrl,
            'cssUrl' => $assetsUrl.'css/',
            'jsUrl' => $assetsUrl.'js/',
            'connectorUrl' => $assetsUrl.'connector.php',
            'thread' => '',

        ),$config);



        /* load debugging settings */
        if ($this->modx->getOption('debug',$this->config,false)) {
            error_reporting(E_ALL); ini_set('display_errors',true);
            $this->modx->setLogTarget('HTML');
            $this->modx->setLogLevel(modX::LOG_LEVEL_ERROR);

            $debugUser = $this->config['debugUser'] == '' ? $this->modx->user->get('username') : 'anonymous';
            $user = $this->modx->getObject('modUser',array('username' => $debugUser));
            if ($user == null) {
                $this->modx->user->set('id',$this->modx->getOption('debugUserId',$this->config,1));
                $this->modx->user->set('username',$debugUser);
            } else {
                $this->modx->user = $user;
            }
        }
    }

    /**
     * Initializes Quip based on a specific context.
     *
     * @access public
     * @param string $ctx The context to initialize in.
     * @return string The processed content.
     */
    public function initialize($ctx = 'mgr') {
        $output = '';
	
		switch ($ctx) {
            case 'mgr':
                if (!$this->modx->loadClass('xdbedit.request.xdbeditControllerRequest',$this->config['modelPath'],true,true)) {
                    return 'Could not load controller request handler.';
                }
                $this->request = new xdbeditControllerRequest($this);
                $output = $this->request->handleRequest();
                break;
        }
        
		
		return $output;
    }

    public function loadConfigs(){
        
		$configs = ( isset ($this->config['configs']))?explode(',', $this->config['configs']): array ();
            //$configs = array_merge( array ('master'), $configs);
            foreach ($configs as $config)
            {
                $configFile = $this->config['corePath'].'configs/'.$config.'.config.inc.php'; // [ file ]
        
                if (file_exists($configFile))
                {
                    include ($configFile);
                }
            }
    }

    public function getClassName($tablename){
        $manager= $this->modx->getManager();
        $generator= $manager->getGenerator();
        return $generator->getClassName($tablename);
    }
    /**
     * Gets a Chunk and caches it; also falls back to file-based templates
     * for easier debugging.
     *
     * Will always use the file-based chunk if $debug is set to true.
     *
     * @access public
     * @param string $name The name of the Chunk
     * @param array $properties The properties for the Chunk
     * @return string The processed content of the Chunk
     */
    public function getChunk($name,$properties = array()) {
        $chunk = null;
        if (!isset($this->chunks[$name])) {
            if (!$this->modx->getOption('boerse.debug',null,false)) {
                $chunk = $this->modx->getObject('modChunk',array('name' => $name));
            }
            if (empty($chunk)) {
                $chunk = $this->_getTplChunk($name);
                if ($chunk == $name) return $name;
            }
            $this->chunks[$name] = $chunk->getContent();
        } else {
            $o = $this->chunks[$name];
            $chunk = $this->modx->newObject('modChunk');
            $chunk->setContent($o);
        }
        $chunk->setCacheable(false);
        return $chunk->process($properties);
    }
    /**
     * Returns a modChunk object from a template file.
     *
     * @access private
     * @param string $name The name of the Chunk. Will parse to name.chunk.tpl
     * @param string $suffix The suffix to postfix the chunk with
     * @return modChunk/boolean Returns the modChunk object if found, otherwise
     * false.
     */
    private function _getTplChunk($name,$suffix = '.chunk.html') {
        $chunk = $name;
        $suffix = $this->modx->getOption('suffix',$this->config,$suffix);
        $f = $this->config['chunksPath'].strtolower($name).$suffix;
		if (file_exists($f)) {
            $o = file_get_contents($f);
            $chunk = $this->modx->newObject('modChunk');
            $chunk->set('name',$name);
            $chunk->setContent($o);
        }
        return $chunk;
    }

    public function getTask(){
    	return $this->customconfigs['task'];
    }

    public function getTabs(){
    	

		
		return $this->customconfigs['tabs'];
		
		
		
    }

    /**
     * Builds simple pagination markup. Not yet used.
     *
     * @access public
     * @param array $options An array of options:
     * - count The total number of records
     * - limit The number to limit to
     * - start The record to start on
     * - url The URL to prefix pagination urls with
     * @return string The rendered template.
     */
    public function buildPagination(array $options = array()) {
        $pageCount = $options['count'] / $options['limit'];
        $curPage = $options['start'] / $options['limit'];
        $pages = '';

        $params = $this->modx->request->getParameters();
        unset($params[$this->modx->getOption('request_param_alias',null,'q')]);

        $tplItem = $this->modx->getOption('tplPaginationItem',$options,'quipPaginationItem');
        $tplCurrentItem = $this->modx->getOption('tplPaginationCurrentItem',$options,'quipPaginationCurrentItem');
        $pageCls = $this->modx->getOption('pageCls',$options,'quip-page-number');
        $currentPageCls = $this->modx->getOption('currentPageCls',$options,'quip-page-current');

        for ($i=0;$i<$pageCount;$i++) {
            $newStart = $i*$options['limit'];
            $u = $options['url'].(strpos($options['url'],'?') !== false ? '&' : '?').http_build_query(array_merge($params,array(
                'quip_start' => $newStart,
                'quip_limit' => $options['limit'],
            )));
            if ($i != $curPage) {
                $pages .= $this->getChunk($tplItem,array(
                    'url' => $u,
                    'idx' => $i+1,
                    'cls' => $pageCls,
                ));
            } else {
                $pages .= $this->getChunk($tplCurrentItem,array(
                    'idx' => $i+1,
                    'cls' => $pageCls.' '.$currentPageCls,
                ));
            }
        }
        return $this->getChunk($this->modx->getOption('tplPagination',$options,'quipPagination'),array(
            'pages' => $pages,
            'cls' => $this->modx->getOption('paginationCls',$options,'quip-pagination'),
        ));
    }

   

    /**
     * Gets a properly formatted "time ago" from a specified timestamp. Copied
     * from MODx core output filters.
     */
    public function getTimeAgo($time = '') {
        if (empty($time)) return false;
        $this->modx->lexicon->load('filters');
        $agoTS = array();

        $uts = array();
        $uts['start'] = strtotime($time);
        $uts['end'] = time();
        if( $uts['start']!==-1 && $uts['end']!==-1 ) {
          if( $uts['end'] >= $uts['start'] ) {
            $diff = $uts['end'] - $uts['start'];

            $years = intval((floor($diff/31536000)));
            if ($years) $diff = $diff % 31536000;

            $months = intval((floor($diff/2628000)));
            if ($months) $diff = $diff % 2628000;

            $weeks = intval((floor($diff/604800)));
            if ($weeks) $diff = $diff % 604800;

            $days = intval((floor($diff/86400)));
            if ($days) $diff = $diff % 86400;

            $hours = intval((floor($diff/3600)));
            if ($hours) $diff = $diff % 3600;

            $minutes = intval((floor($diff/60)));
            if ($minutes) $diff = $diff % 60;

            $diff = intval($diff);
            $agoTS = array(
              'years' => $years,
              'months' => $months,
              'weeks' => $weeks,
              'days' => $days,
              'hours' => $hours,
              'minutes' => $minutes,
              'seconds' => $diff,
            );
          }
        }

        $ago = array();
        if (!empty($agoTS['years'])) {
          $ago[] = $this->modx->lexicon(($agoTS['years'] > 1 ? 'ago_years' : 'ago_year'),array('time' => $agoTS['years']));
        }
        if (!empty($agoTS['months'])) {
          $ago[] = $this->modx->lexicon(($agoTS['months'] > 1 ? 'ago_months' : 'ago_month'),array('time' => $agoTS['months']));
        }
        if (!empty($agoTS['weeks']) && empty($agoTS['years'])) {
          $ago[] = $this->modx->lexicon(($agoTS['weeks'] > 1 ? 'ago_weeks' : 'ago_week'),array('time' => $agoTS['weeks']));
        }
        if (!empty($agoTS['days']) && empty($agoTS['months']) && empty($agoTS['years'])) {
          $ago[] = $this->modx->lexicon(($agoTS['days'] > 1 ? 'ago_days' : 'ago_day'),array('time' => $agoTS['days']));
        }
        if (!empty($agoTS['hours']) && empty($agoTS['weeks']) && empty($agoTS['months']) && empty($agoTS['years'])) {
          $ago[] = $this->modx->lexicon(($agoTS['hours'] > 1 ? 'ago_hours' : 'ago_hour'),array('time' => $agoTS['hours']));
        }
        if (!empty($agoTS['minutes']) && empty($agoTS['days']) && empty($agoTS['weeks']) && empty($agoTS['months']) && empty($agoTS['years'])) {
          $ago[] = $this->modx->lexicon('ago_minutes',array('time' => $agoTS['minutes']));
        }
        if (empty($ago)) { /* handle <1 min */
          $ago[] = $this->modx->lexicon('ago_seconds',array('time' => $agoTS['seconds']));
        }
        $output = implode(', ',$ago);
        $output = $this->modx->lexicon('ago',array('time' => $output));
        return $output;
    }

    /**
     * Gets a proper array of time since a timestamp
     */
    public function timesince($input) {
        $output = '';
        $uts['start'] = strtotime($input);
        $uts['end'] = time();
        if( $uts['start']!==-1 && $uts['end']!==-1 ) {
            if( $uts['end'] >= $uts['start'] ) {
                $diff = $uts['end'] - $uts['start'];
                $days = intval((floor($diff/86400)));
                if ($days) $diff = $diff % 86400;
                $hours = intval((floor($diff/3600)));
                if ($hours) $diff = $diff % 3600;
                $minutes = intval((floor($diff/60)));
                if ($minutes) $diff = $diff % 60;

                $diff = intval($diff);
                $output = array(
                    'days' => $days
                    ,'hours' => $hours
                    ,'minutes' => $minutes
                    ,'seconds' => $diff
                );
            }
        }
        return $output;
    }
}