<?php
namespace SilverStripe\Omnipay\UI\Tasks;
/**
 * Creates JavaScript files ready for consumption by framework/javascript/i18n.js,
 * based on source files in JSON format. This is necessary in order to support
 * translations in a format which our collaborative translation service (Transifex)
 * supports, while retaining the ability to combine JavaScript files in SilverStripe
 * without resorting to JSONP or other means of processing raw JSON files.
 */
class GenerateJavascriptI18nTask extends \BuildTask {

	private $modulePath = BASE_PATH . '/omnipay-ui';
	private $sourceDir = 'javascript/lang/src';
	private $targetDir = 'javascript/lang';


	public function setModulePath($modulePath) {
		$this->modulePath = $modulePath;
	}

	public function getTemplate() {
		$tmpl = <<<TMPL
// This file was generated by GenerateJavaScriptI18nTask from %FILE%.
// See https://github.com/silverstripe/silverstripe-buildtools for details
if(typeof(ss) == 'undefined' || typeof(ss.i18n) == 'undefined') {
	if(typeof(console) != 'undefined') console.error('Class ss.i18n not defined');
} else {
	ss.i18n.addDictionary('%LOCALE%', %TRANSLATIONS%);
}
TMPL;
		return $tmpl;
	}

	public function run($request) {
		if($request->getVar('module')){
			$this->setModulePath(BASE_PATH . '/' . $request->getVar('module'));
		}

		if (!is_dir($this->modulePath)) {
			throw new \Exception("Invalid module path: $this->modulePath");
		}
		$ds = DIRECTORY_SEPARATOR;
		$iterator = new \GlobIterator(
			$this->modulePath . $ds . $this->sourceDir . $ds . '*.json'
		);
		foreach($iterator as $item) {
			$translations = file_get_contents($item->getPathName());
			$locale = preg_replace('/\.json/','', $item->getFilename());
			$targetPath = $this->modulePath . $ds . $this->targetDir . $ds . $locale . '.js';
			echo "Generating $targetPath\n";
			file_put_contents(
				$targetPath,
				str_replace(
					array(
						'%TRANSLATIONS%',
						'%FILE%',
						'%LOCALE%'
					),
					array(
						$translations,
						$this->sourceDir . $ds . $item->getFilename(),
						$locale
					),
					$this->getTemplate()
				)
			);
		}
	}
}
