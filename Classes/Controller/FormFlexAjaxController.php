<?php

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with TYPO3 source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

declare(strict_types=1);

namespace Causal\ImageAutoresize\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

class FormFlexAjaxController extends \TYPO3\CMS\Backend\Controller\FormFlexAjaxController
{

    /**
     * Render a single flex form section container to add it to the DOM
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function containerAdd(ServerRequestInterface $request): ResponseInterface
    {
        $GLOBALS['TCA']['tx_imageautoresize'] = include(ExtensionManagementUtility::extPath('image_autoresize') . 'Configuration/TCA/Module/Options.php');
        $GLOBALS['TCA']['tx_imageautoresize']['ajax'] = true;

        // Trick to use a virtual record
        $dataProviders =& $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['tcaDatabaseRecord'];
        $dataProviders[\Causal\ImageAutoresize\Backend\Form\FormDataProvider\VirtualDatabaseEditRow::class] = [
            'before' => [
                \TYPO3\CMS\Backend\Form\FormDataProvider\DatabaseEditRow::class,
            ]
        ];

        $record = [
            'uid' => \Causal\ImageAutoresize\Controller\ConfigurationController::virtualRecordId,
            'pid' => 0,
        ];

        // Initialize record in our virtual provider
        \Causal\ImageAutoresize\Backend\Form\FormDataProvider\VirtualDatabaseEditRow::initialize($record);

        $response = parent::containerAdd($request);
        return $response->withHeader('Content-Type', 'application/json; charset=utf-8');
    }

}
