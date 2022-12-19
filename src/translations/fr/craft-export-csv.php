<?php
/**
 * craft-export-csv plugin for Craft CMS 3.x
 *
 * Description
 *
 * @link      http://kffein.com
 * @copyright Copyright (c) 2018 KFFEIN
 */

/**
 * craft-export-csv en Translation
 *
 * Returns an array with the string to be translated (as passed to `Craft::t('craft-export-csv', '...')`) as
 * the key, and the translation as the value.
 *
 * http://www.yiiframework.com/doc-2.0/guide-tutorial-i18n.html
 *
 * @author    KFFEIN
 * @package   CraftExportCsv
 * @since     1.0.1
 */
return [
    // Nav
    'reports-label' => 'Rapport',
    'settings' => 'Paramètres',

    // Form
    'numberOfRows-label' => 'Nombre d\'entrées',
    'numberOfRows-instructions' => 'Entrer le nombre d\'entrées max pour chaque requête.',
    'section-handle-label' => 'Entrée à exporter',
    'sites-handle-label' => 'Sites',
    'entryStatus-handle-label' => 'Status des entrées',
    'expireEntries-label' => 'Faire expirer les entrées exportées',
    'expireEntries-instructions' => 'Choisissez si vous souhaitez définir le statut des entrées sur expiré une fois qu\'elles ont été exportées.',
    'section-handle-instructions' => 'Sélectionnez le type d’entrée que vous souhaitez exporter.',
    'name-label' => 'Nom de l\'export',
    'name-instructions' => 'Choisir un nom descriptif pour l\'export',
    'filename-label' => 'Nom du fichier',
    'filename-instructions' => 'Clés disponibiles: {batch}, {timestamp}, {Y}, {d}, {m}, {H}, {i}, {section-handle}',
    'fields-label' => 'Colonnes',
    'fields-instructions' => 'Décrivez les colonnes à exporter.',
    'field-name' => 'Titre de la colonne',
    'field-type' => 'Type de correspondance',
    'field-type-handle' => 'Handle',
    'field-type-concat-handle' => 'Concaténation d’handle',
    'field-type-custom-query' => 'Requête custon ! Attention, ça peut faire crasher le rapport !',
    'field-value' => 'Valeur',

    // Reports
    'generate-heading' => 'Générer un export pour la section: {sectionName}',
    'generate' => 'Générer le fichier csv {filename}',
    'download' => 'Télécharger le fichier csv {filename}',
    'no-reports' => 'Aucun rapport configuré.',
    'configure-report' => 'Configurer un rapport',
    'no-result' => 'Aucune entrée à télécharger',
    'reports-list' => 'Liste de rapport',
];
