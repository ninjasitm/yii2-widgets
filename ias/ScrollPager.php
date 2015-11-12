<?php

namespace nitm\widgets\ias;

use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\i18n\PhpMessageSource;
use yii\web\JsExpression;
use yii\web\View;
use yii\widgets\LinkPager;
use Yii;

/**
 * Modified version of this package using roles to init scrolling assets and fiing bug with container
 *
 * ScrollPager turns your regular paginated page into an infinite scrolling page using AJAX.
 *
 * ScrollPager works with a [[Pagination]] object which specifies the totally number of pages and the current page number.
 *
 * <br>
 * <i>Example usage:</i>
 * <code>
 * echo ListView::widget([
 *      'dataProvider' => $dataProvider,
 *      'itemOptions' => ['class' => 'item'],
 *      'itemView' => '_item_view',
 *      'pager' => ['class' => \kop\y2sp\ScrollPager::className()]
 * ]);
 * </code>
 *
 * This widget is using {@link http://infiniteajaxscroll.com/ JQuery Infinite Ajax Scroll plugin}.
 *
 * @link      http://kop.github.io/yii2-scroll-pager Y2SP project page.
 * @license   https://github.com/kop/yii2-scroll-pager/blob/master/LICENSE.md MIT
 *
 * @author    Ivan Koptiev <ikoptev@gmail.com>
 * @version   2.1.2
 */
class ScrollPager extends \kop\y2sp\ScrollPager
{
	public $force = false;
	/**
	 * This option was missing from kop's implementation
	 */
	public $overflowContainer;
	public $triggerOffset = 250;


	public $spinnerTemplate = "<div class='loading-wrapper'><div class='loading'></div></div>";
	public $noneLeftTemplate = "<h4 style='position: absolute; min-height: 20px; width: 90%; left: 5%; right: 5%; text-align: center; padding: 6px;'>{text}</h4>";
	public $noneLeftText = 'To not be continued!';

	public function init()
	{
		parent::init();
		Asset::register($this->getView());
	}

    /**
     * Executes the widget.
     *
     * This overrides the parent implementation by initializing jQuery IAS and displaying the generated page buttons.
     *
     * @throws \yii\base\InvalidConfigException
     * @return mixed
     */
    public function run()
    {
        // Initialize jQuery IAS plugin
        $pluginSettings = [
			'container' => $this->overflowContainer,
			'ias' => [
				'container' => $this->container,
				'item' => $this->item,
				'pagination' => "{$this->container} .pagination",
				'next' => '.next a',
				'delay' => $this->delay,
				'negativeMargin' => $this->negativeMargin
			]
        ];

        // Register IAS extensions
       $pluginSettings['extensions'] = $this->registerExtensions([
            [
                'name' => self::EXTENSION_PAGING
            ],
            [
                'name' => self::EXTENSION_SPINNER,
                'options' =>
                    !empty($this->spinnerSrc)
                        ? ['html' => $this->spinnerTemplate, 'src' => $this->spinnerSrc]
                        : ['html' => $this->spinnerTemplate]
            ],
            [
                'name' => self::EXTENSION_TRIGGER,
                'options' => [
                    'text' => $this->triggerText,
                    'html' => $this->triggerTemplate,
                    'offset' => $this->triggerOffset
                ]
            ],
            [
                'name' => self::EXTENSION_NONE_LEFT,
                'options' => [
                    'text' => $this->noneLeftText,
                    'html' => $this->noneLeftTemplate
                ]
            ],
            [
                'name' => self::EXTENSION_HISTORY,
                'options' => [
                    'prev' => $this->historyPrev
                ],
                'depends' => [
                    self::EXTENSION_TRIGGER,
                    self::EXTENSION_PAGING
                ]
            ]
        ]);

        // Register event handlers
        $pluginSettings['events'] = $this->registerEventHandlers([
            'scroll' => [],
            'load' => [],
            'loaded' => [],
            'render' => [],
            'rendered' => [],
            'noneLeft' => [],
            'next' => [],
            'ready' => [],
            'pageChange' => [
                self::EXTENSION_PAGING
            ]
        ]);

        // Render pagination links
        return LinkPager::widget([
            'pagination' => $this->pagination,
            'options' => [
                'class' => 'pagination hidden',
				'role' => 'iasContainer',
				'data-ias' => json_encode($pluginSettings)
            ]
        ]);
    }

    /**
     * Register jQuery IAS extensions.
     *
     * This method takes jQuery IAS extensions definition as a parameter and registers this extensions.
     *
     * @param array $config jQuery IAS extensions definition.
     * @throws \yii\base\InvalidConfigException If extension dependencies are not met.
     */
    protected function registerExtensions(array $config)
    {
		$ret_val = [];
        foreach ($config as $entry) {

            // Parse config entry values
            $name = ArrayHelper::getValue($entry, 'name', false);
            $options = ArrayHelper::getValue($entry, 'options', '');
            $depends = ArrayHelper::getValue($entry, 'depends', []);

            // If extension is enabled
            if (in_array($name, $this->enabledExtensions)) {

                // Make sure dependencies are met
                if (!$this->checkEnabledExtensions($depends)) {
                    throw new InvalidConfigException(
                        "Extension {$name} requires " . implode(', ', $depends) . " extensions to be enabled."
                    );
                }

                // Register extension
				$ret_val[$name] = $options;
            }
        }
		return $ret_val;
    }

    /**
     * Register jQuery IAS event handlers.
     *
     * This method takes jQuery IAS event handlers definition as a parameter and registers this event handlers.
     *
     * @param array $config jQuery IAS event handlers definition.
     * @throws \yii\base\InvalidConfigException If vent handlers dependencies are not met.
     */
    protected function registerEventHandlers(array $config)
    {
		$ret_val = [];
        foreach ($config as $name => $depends) {

            // If event is enabled
            $eventName = 'eventOn' . ucfirst($name);
            if (!empty($this->$eventName)) {

                // Make sure dependencies are met
                if (!$this->checkEnabledExtensions($depends)) {
                    throw new InvalidConfigException(
                        "The \"{$name}\" event requires " . implode(', ', $depends) . " extensions to be enabled."
                    );
                }
				$ret_val[$name] = $this->$eventName;
            }
        }
		return $ret_val;
    }
}
