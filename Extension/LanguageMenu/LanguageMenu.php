<?php
/**
 * @link      http://fraym.org
 * @author    Dominik Weber <info@fraym.org>
 * @copyright Dominik Weber <info@fraym.org>
 * @license   http://www.opensource.org/licenses/gpl-license.php GNU General Public License, version 2 or later (see the LICENSE file)
 */
namespace Fraym\Extension\LanguageMenu;

use Fraym\Annotation\Registry;

/**
 * @package Fraym\Extension\LanguageMenu
 * @Registry(
 * name="Language Menu",
 * repositoryKey="fraym/language-menu-extension",
 * entity={
 *      "\Fraym\Block\Entity\Extension"={
 *          {
 *           "name"="Language Menu",
 *           "description"="Create a language menu.",
 *           "class"="\Fraym\Extension\LanguageMenu\LanguageMenu",
 *           "execMethod"="execBlock"
 *           },
 *      }
 * }
 * )
 * @Injectable(lazy=true)
 */
class LanguageMenu
{
    /**
     * @Inject
     * @var \Fraym\Extension\LanguageMenu\LanguageMenuController
     */
    protected $languageMenuController;

    /**
     * @Inject
     * @var \Fraym\Locale\Locale
     */
    protected $locale;

    /**
     * @Inject
     * @var \Fraym\Route\Route
     */
    protected $route;

    /**
     * @Inject
     * @var \Fraym\Template\Template
     */
    protected $template;

    /**
     * @Inject
     * @var \Fraym\Database\Database
     */
    protected $db;

    /**
     * @param $xml
     * @return mixed
     */
    public function execBlock($xml)
    {
        $languageMenu = array();
        $currentLocale = $this->locale->getLocale()->id;
        $currentMenuItem = $this->route->getCurrentMenuItem();

        foreach ($currentMenuItem->translations as $translation) {
            $url = $this->route->buildFullUrl($translation, true);
            $languageMenu[$translation->locale->id] = array(
                'enable' => $translation->active,
                'url' => $url,
                'active' => $translation->locale->id === $currentLocale,
                'name' => $translation->locale->name
            );
        }

        foreach ($languageMenu as $locale => $language) {
            if ($language['enable'] === false) {
                $menuItem = $currentMenuItem;
                do {
                    $menuItem = $menuItem->parent;
                } while ($menuItem->parent);

                $translation = $menuItem->getTranslation($locale);
                $languageMenu[$locale]['url'] = $translation ? $translation->url : '/';
            } else {
                $localeData = $this->db->getRepository('\Fraym\Locale\Entity\Locale')->findOneById($locale);
                $localeData = explode('_', strtolower($localeData->locale));
                $this->template->addHeadData('<link rel="alternate" hreflang="' . $localeData[0] . '" href="' . $language['url'] . '" />', 'hreflang_' . $localeData[0]);
            }
        }
        $this->languageMenuController->renderHtml($languageMenu);
    }
}
