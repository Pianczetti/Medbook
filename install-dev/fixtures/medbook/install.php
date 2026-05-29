<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;
use PrestaShopBundle\Install\XmlLoader;

/**
 * MedBook fixture installer.
 * Extends the default XmlLoader to install medbook_booking module fixtures
 * during fresh PrestaShop installations with the MedBook fixture set.
 */
class InstallFixturesMedbook extends XmlLoader
{
    /**
     * {@inheritdoc}
     */
    public function populateFromXmlFiles()
    {
        parent::populateFromXmlFiles();

        // Ensure medbook_booking module is installed and its fixtures are loaded
        $moduleManagerBuilder = ModuleManagerBuilder::getInstance();
        $moduleManager = $moduleManagerBuilder->build();

        if (!$moduleManager->isInstalled('medbook_booking')) {
            $moduleManager->install('medbook_booking');
        }
    }
}
