<?php
/**
 * DooSiteMagic class file.
 *
 * @author Leng Sheng Hong <darkredz@gmail.com>
 * @link http://www.doophp.com/
 * @copyright Copyright &copy; 2009 Leng Sheng Hong
 * @license http://www.doophp.com/license
 */

/**
 * DooSiteMagic provides useful tools in development.
 *
 * <p>If you have your routes defined, call DooSiteMagic::buildSite() and
 * it will generate the Controller files for all the controllers defined along with the methods</p>
 * 
 * <p>DooSiteMagic also generates sitemap(routes.conf.php) and some features for development use.</p>
 *
 * @author Leng Sheng Hong <darkredz@gmail.com>
 * @version $Id: DooSiteMagic.php 1000 2009-08-06 17:10:12
 * @package doo.app
 * @since 1.1
 */
class DooSiteMagic{

    /**
     * Display some info as a welcome page for DooPHP
     */
	public static function displayHome(){
		echo '<div style="font-family:\'Courier New\', Courier, monospace;"><h1>It Works!</h1>';
		echo '<h3>What now?</h3><p><a href="./tools/sitemap.html">Generate Sitemap</a> | <a href="./index.php/gen_site">Generate Controllers</a> | <a href="./index.php/gen_models">Generate Models</a> | <a href="./tools/logviewer.html">View Logs</a> | <a href="./index.php/allurl">View All URLs</a></p>';
		echo '<br/><strong>Suggested workflow:</strong><ol>
			  <li>Plan your website and draft a sitemap</li>
			  <li>Convert the sitemap into routes.conf.php</li>
			  <li>Generate the Controllers</li>
			  <li>Define your database relationship & settings</li>
			  <li>Generate the Models</li>
			  <li>Start coding & have fun !</li></ol></div>';	
	}
	
    /**
     * Display logs/profiles message from the XML log files.
     */
	public static function showDebug(){
		if(isset(Doo::conf()->LOG_PATH)){
			header('Content-Type: text/xml');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			echo '<xml>';
			if(isset($this->params['filename']))
				include Doo::conf()->LOG_PATH . $this->params['filename'] .'.xml';
			else
				include Doo::conf()->LOG_PATH . 'log.xml';
			echo '</xml>';
			exit;	
		}
	}
	
    /**
     * Show all URLs available in application based on the route definition
     */
	public static function showAllUrl(){
        $data = array();
        $n = 1;
		$route = Doo::app()->route;
        foreach($route as $req=>$r){
            foreach($r as $rname=>$value){
                $rname_strip = 'index.php'.$rname;
                $data[$n++ ." $req"] = '<a href="'.Doo::conf()->APP_URL.$rname_strip.'">'.$rname.'</a>';
            }
        }
		echo '<pre>';
		print_r($data);
	}
	
	/**
	 * Write the generated routes into routes.conf.php
	 */
	public static function buildSitemap(){
		if(!isset($_POST['routes']) || empty($_POST['routes'])){
			echo 'result=false';
			exit;
		}
		$handle = fopen(Doo::conf()->SITE_PATH.'protected/config/routes2.conf.php', 'w+');
		$rs = fwrite($handle, $_POST['routes']);
		fclose($handle);
		if($rs===False){
			echo 'result=false';
		}else{
			echo 'result=true';
		}
	}
	
    /**
     * Generates Controller class files from routes definition
     */
	public static function buildSite(){
		$route = Doo::app()->route;
		$controllers = array();
		foreach($route as $req=>$r){
            foreach($r as $rname=>$value){
                $controllers[$value[0]][] = $value[1];
            }
        }
		
		echo "<html><head><title>DooPHP Site Generator </title></head><body bgcolor=\"#2e3436\">";
		$total = 0;
		
		foreach($controllers as $cname=>$methods){
			$filestr = '';
			$filestr .= "<?php\n\nclass $cname extends DooController {" ;
			$methods = array_unique($methods);
			foreach($methods as $mname){
				$filestr .= "\n\n\tfunction $mname() {\n\t\techo 'You are visiting '.\$_SERVER['REQUEST_URI'];\n\t}";
			}
			$filestr .= "\n\n}\n?>";
			if(file_exists(Doo::conf()->SITE_PATH.'protected/controller/'.$cname.'.php')){
				echo "<span style=\"font-size:190%;font-family: 'Courier New', Courier, monospace;\"><strong><span style=\"color:#729fbe;\">$cname.php</span></strong><span style=\"color:#fff;\"> <span style=\"color:#fff;\">file exists! Skipped ...</span></span></span><br/><br/>";
			}else{
				echo "<span style=\"font-size:190%;font-family: 'Courier New', Courier, monospace;\"><span style=\"color:#fff;\">Controller file </span><strong><span style=\"color:#e7c118;\">$cname</span></strong><span style=\"color:#fff;\"> generated.</span></span><br/><br/>";
				$total++;
				$handle = fopen(Doo::conf()->SITE_PATH.'protected/controller/'.$cname.'.php', 'w+');
				fwrite($handle, $filestr);
				fclose($handle);
			}
		}
        echo "<span style=\"font-size:190%;font-family: 'Courier New', Courier, monospace;color:#fff;\">Total $total file(s) generated.</span></body></html>";
	}
}

?>