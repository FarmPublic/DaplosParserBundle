<?php

declare(strict_types=1);

namespace FarmPublic\DaplosParserBundle;

class DaplosParser implements DaplosParserInterface
{
    public function parse(string $filePath): array
    {
        $handle = fopen($filePath, 'r');
        $data = [];

        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $flag = substr($line, 0, 2);

                $data[] = match ($flag) {
                    'EI' => $this->parseEiFlag($line),
                    'DE' => $this->parseDeFlag($line),
                    'DA' => $this->parseDaFlag($line),
                    'DT' => $this->parseDtFlag($line),
                    'DP' => $this->parseDpFlag($line),
                    'PC' => $this->parsePcFlag($line),
                    'CC' => $this->parseCcFlag($line),
                    'PS' => $this->parsePsFlag($line),
                    'SC' => $this->parseScFlag($line),
                    'PE' => $this->parsePeFlag($line),
                    'PH' => $this->parsePhFlag($line),
                    'HA' => $this->parseHaFlag($line),
                    'PA' => $this->parsePaFlag($line),
                    'PV' => $this->parsePvFlag($line),
                    'VC' => $this->parseVcFlag($line),
                    'VB' => $this->parseVbFlag($line),
                    'VI' => $this->parseViFlag($line),
                    'IC' => $this->parseIcFlag($line),
                    'IL' => $this->parseIlFlag($line),
                    'IA' => $this->parseIaFlag($line),
                    'VR' => $this->parseVrFlag($line),
                    'RL' => $this->parseRlFlag($line),
                    'LC' => $this->parseLcFlag($line),
                    'VH' => $this->parseVhFlag($line),
                    default => null, // Gérer les cas de flags inconnus ou non traités
                };
            }
            fclose($handle);
        }
        return $data;
    }

        /**
     * Extract and trim a substring from a string
     */
    public function extractAndTrim(string $line, int $start, int $length): string {
        // Ensure the line is trimmed from the start before extracting substrings
        $line = ltrim($line);  // Remove leading spaces from the line
        return trim(substr($line, $start-1, $length));
    }
    
    /**
     * Extract and parse the EI flag
     */
    private function parseEiFlag($line) {
        return [
        'emitter_id' => $this->extractAndTrim($line, 3, 14),  // Emitter identification (SIRET or EAN code)
        'emitter_code_type' => $this->extractAndTrim($line, 17, 3), // Type of emitter code (5: SIRET, 14: EAN)
        'recipient_id' => $this->extractAndTrim($line, 20, 14),  // Recipient identification (SIRET or EAN code)
        'recipient_code_type' => $this->extractAndTrim($line, 34, 3), // Type of recipient code (5: SIRET, 14: EAN)
        'document_count' => $this->extractAndTrim($line, 37, 4) // Number of documents in the exchange, trimmed to remove any \r or \n
        ];
    }

    /**
     * Extract and parse the DE flag (Entête du document)
     */
    private function parseDeFlag($line) {
        return [
            'document_ref' => $this->extractAndTrim($line, 3, 35),  // Référence du document (toujours vide ici)
            'fonction_code' => $this->extractAndTrim($line, 37, 1),  // Fonction (en code), commence à la position 37
            'document_date' => $this->extractAndTrim($line, 38, 8),  // Date du document (format SSAAMMJJ), commence à 38
            'num_fiches_parcellaires' => $this->extractAndTrim($line, 46, 4),  // Nombre de fiches parcellaires, commence à 46
            'version_message' => $this->extractAndTrim($line, 50, 4),  // N° de version du message, commence à 50
        ];
    }

    /**
     * Extract and parse the DA flag (Adresses intervenants)
     */
    private function parseDaFlag($line) {
        return [
            'qualifiant_intervenant' => $this->extractAndTrim($line, 3, 3),  // Qualifiant de l'intervenant (TF, FR, MR)
            'id_intervenant' => $this->extractAndTrim($line, 6, 17),  // Identification de l'intervenant (SIRET ou autre code)
            'type_identification' => $this->extractAndTrim($line, 23, 3),  // Type d’identification (9: EAN, 107: SIRET)
            'raison_sociale_1' => $this->extractAndTrim($line, 26, 35),  // Raison sociale (1)
            'raison_sociale_2' => $this->extractAndTrim($line, 61, 35),  // Raison sociale (2), facultative
            'adresse_1' => $this->extractAndTrim($line, 96, 35),  // Adresse Rue (1)
            'adresse_2' => $this->extractAndTrim($line, 131, 35),  // Adresse Rue (2), facultative
            'ville' => $this->extractAndTrim($line, 166, 35),  // Ville de l'exploitation
            'code_postal' => $this->extractAndTrim($line, 201, 9),  // Code postal
            'pays' => $this->extractAndTrim($line, 210, 2),  // Pays (code ISO, ex: FR pour France)
            'ref_complementaire_1' => $this->extractAndTrim($line, 212, 20),  // Référence complémentaire 1, facultative
            'ref_complementaire_2' => $this->extractAndTrim($line, 232, 20),  // Référence complémentaire 2, facultative
            'ref_complementaire_3' => $this->extractAndTrim($line, 252, 20),  // Référence complémentaire 3, facultative
        ];
    }

    /**
     * Extract and parse the DT flag (Type d'agriculture pratiquée)
     */
    private function parseDtFlag($line) {
        return [
            'type_agriculture_code' => $this->extractAndTrim($line, 3, 3),  // Type d’agriculture pratiquée (code)
            'num_certificat' => $this->extractAndTrim($line, 6, 20),  // N° de certificat
            'autre_type_agriculture' => $this->extractAndTrim($line, 26, 20),  // Autre type d’agriculture
        ];
    }

    /**
     * Extract and parse the DP flag (Parcelle culturale)
     */
    private function parseDpFlag($line) {
        return [
            'num_parcelle' => $this->extractAndTrim($line, 3, 4),  // N° d'ordre de la parcelle
            'id_parcelle_culturale' => $this->extractAndTrim($line, 7, 4),  // Identification parcelle culturale
            'annee_recolte' => $this->extractAndTrim($line, 11, 4),  // Année prévue de récolte (SSAA)
            'date_debut_parcelle' => $this->extractAndTrim($line, 15, 8),  // Date de début de la parcelle (SSAAMMJJ)
            'date_creation_fiche' => $this->extractAndTrim($line, 23, 8),  // Date de création de la fiche (SSAAMMJJ)
            'date_dern_saisie_parcelle' => $this->extractAndTrim($line, 31, 8),  // Date de dernière saisie sur la parcelle (SSAAMMJJ)
            'date_fin_parcelle' => $this->extractAndTrim($line, 39, 8),  // Date de fin de la parcelle (SSAAMMJJ)
            'espece_botanique' => $this->extractAndTrim($line, 47, 3),  // Espèce botanique attendue (en code)
            'variete_semee_1' => $this->extractAndTrim($line, 50, 7),  // Variété semée 1 (code GNIS)
            'variete_semee_2' => $this->extractAndTrim($line, 57, 7),  // Variété semée 2 (code GNIS)
            'variete_semee_3' => $this->extractAndTrim($line, 64, 7),  // Variété semée 3 (code GNIS)
            'variete_semee_4' => $this->extractAndTrim($line, 71, 7),  // Variété semée 4 (code GNIS)
            'variete_semee_5' => $this->extractAndTrim($line, 78, 7),  // Variété semée 5 (code GNIS)
            'qualifiant_espece' => $this->extractAndTrim($line, 85, 3),  // Qualifiant de l'espèce
            'periode_semis' => $this->extractAndTrim($line, 88, 3),  // Période de semis (en code)
            'destination' => $this->extractAndTrim($line, 91, 3),  // Destination (en code)
            'rendement_objectif' => $this->extractAndTrim($line, 94, 9),  // Rendement objectif (en chiffres)
            'unite_rendement' => $this->extractAndTrim($line, 103, 3),  // Unité de mesure du rendement (en code)
            'intitule_parcelle' => $this->extractAndTrim($line, 106, 35),  // Intitulé de la parcelle culturale
            'num_pac' => $this->extractAndTrim($line, 141, 10),  // N° PAC
            'num_parcelle_perenne' => $this->extractAndTrim($line, 151, 10),  // N° parcelle pérenne
            'num_commune' => $this->extractAndTrim($line, 161, 5),  // N° de commune
            'profondeur_sol' => $this->extractAndTrim($line, 167, 3),  // Profondeur du sol (en cm)
            'pierrosite_surface' => $this->extractAndTrim($line, 170, 3),  // Pierrosité de surface (en pourcentage)
            'type_sol_code' => $this->extractAndTrim($line, 173, 3),  // Type de sol (en code)
            'autre_type_sol' => $this->extractAndTrim($line, 176, 35),  // Autre type de sol (en texte libre)
            'acidite_sol_code' => $this->extractAndTrim($line, 211, 3),  // Acidité du sol (en code)
            'profondeur_sous_sol' => $this->extractAndTrim($line, 214, 3),  // Profondeur qualitative d’apparition du sous-sol (en code)
            'type_sous_sol_code' => $this->extractAndTrim($line, 217, 3),  // Type de sous-sol (en code)
        ];
    }

    /**
     * Extract and parse the PC flag (Parcelle cadastrale)
     */
    private function parsePcFlag($line) {
        return [
            'num_ordre_parcelle' => $this->extractAndTrim($line, 3, 4),  // N° d'ordre de la parcelle
            'ref_parcelle_culturale' => $this->extractAndTrim($line, 7, 4),  // Référence parcelle culturale
            'annee_recolte' => $this->extractAndTrim($line, 11, 4),  // Année prévue de récolte
            'num_parcelle_cadastrale' => $this->extractAndTrim($line, 15, 16),  // N° parcelle cadastrale
            'surface_parcelle' => $this->extractAndTrim($line, 31, 9),  // Surface de la parcelle cadastrale (en hectares)
        ];
    }

    /**
     * Extract and parse the CC flag (Coordonnée des points géographiques)
     */
    private function parseCcFlag($line) {
        return [
            'num_ordre_parcelle' => $this->extractAndTrim($line, 3, 4),  // N° d'ordre de la parcelle
            'ref_parcelle_culturale' => $this->extractAndTrim($line, 7, 4),  // Référence parcelle culturale
            'annee_recolte' => $this->extractAndTrim($line, 11, 4),  // Année prévue de récolte
            'num_parcelle_cadastrale' => $this->extractAndTrim($line, 15, 16),  // N° parcelle cadastrale
            'qualifiant_position_geo' => $this->extractAndTrim($line, 31, 3),  // Qualifiant de la position géographique
            'longitude' => $this->extractAndTrim($line, 34, 11),  // Longitude (7 entiers et 3 décimales)
            'latitude' => $this->extractAndTrim($line, 45, 10),  // Latitude (7 entiers et 3 décimales)
            'altitude' => $this->extractAndTrim($line, 55, 18),  // Altitude (7 entiers et 3 décimales)
        ];
    }

    /**
     * Extract and parse the PS flag (Surface parcelle culturale)
     */
    private function parsePsFlag($line) {
        return [
            'num_ordre_parcelle' => $this->extractAndTrim($line, 3, 4),  // N° d'ordre de la parcelle
            'ref_parcelle_culturale' => $this->extractAndTrim($line, 7, 4),  // Référence parcelle culturale
            'annee_recolte' => $this->extractAndTrim($line, 11, 4),  // Année prévue de récolte
            'type_surface' => $this->extractAndTrim($line, 15, 3),  // Type de surface (en code)
            'surface' => $this->extractAndTrim($line, 18, 9),  // Surface en hectares (avec 4 décimales maximum)
        ];
    }

    /**
     * Extract and parse the SC flag (Coordonnées des points géographiques d'une surface)
     */
    private function parseScFlag($line) {
        return [
            'num_ordre_parcelle' => $this->extractAndTrim($line, 3, 4),  // N° d'ordre de la parcelle
            'ref_parcelle_culturale' => $this->extractAndTrim($line, 7, 4),  // Référence parcelle culturale
            'annee_recolte' => $this->extractAndTrim($line, 11, 4),  // Année prévue de récolte
            'qualifiant_position_geo' => $this->extractAndTrim($line, 15, 3),  // Qualifiant de la position géographique
            'longitude' => $this->extractAndTrim($line, 18, 11),  // Longitude (7 entiers et 3 décimales)
            'latitude' => $this->extractAndTrim($line, 29, 10),  // Latitude (7 entiers et 3 décimales)
            'altitude' => $this->extractAndTrim($line, 39, 18),  // Altitude (7 entiers et 3 décimales)
        ];
    }

    /**
     * Extract and parse the PE flag (Engagement)
     */
    private function parsePeFlag($line) {
        return [
            'num_ordre_parcelle' => $this->extractAndTrim($line, 3, 4),  // N° d'ordre de la parcelle
            'ref_parcelle_culturale' => $this->extractAndTrim($line, 7, 4),  // Référence parcelle culturale
            'annee_recolte' => $this->extractAndTrim($line, 11, 4),  // Année prévue de récolte
            'code_engagement' => $this->extractAndTrim($line, 15, 3),  // Code engagement (en code)
            'libelle_autre_contrat' => $this->extractAndTrim($line, 18, 35),  // Libellé autre contrat
            'num_contrat' => $this->extractAndTrim($line, 53, 35),  // N° de contrat
            'date_contrat' => $this->extractAndTrim($line, 88, 8),  // Date du contrat (SSAAMMJJ)
            'id_contractant' => $this->extractAndTrim($line, 96, 14),  // Identification du contractant (EAN ou SIRET)
            'type_identification' => $this->extractAndTrim($line, 110, 3),  // Type d’identification (9: EAN, 107: SIRET)
            'raison_sociale_1' => $this->extractAndTrim($line, 113, 35),  // Raison sociale (1)
            'raison_sociale_2' => $this->extractAndTrim($line, 148, 35),  // Raison sociale (2)
            'adresse_rue_1' => $this->extractAndTrim($line, 183, 35),  // Adresse Rue (1)
            'adresse_rue_2' => $this->extractAndTrim($line, 218, 35),  // Adresse Rue (2)
            'ville' => $this->extractAndTrim($line, 253, 35),  // Ville
            'code_postal' => $this->extractAndTrim($line, 288, 9),  // Code postal
            'pays' => $this->extractAndTrim($line, 297, 2),  // Pays (Code ISO)
        ];
    }

    /**
     * Extract and parse the PH flag (Historique / Précédent de la parcelle culturale)
     */
    private function parsePhFlag($line) {
        return [
            'num_ordre_parcelle' => $this->extractAndTrim($line, 3, 4),  // N° d'ordre de la parcelle
            'ref_parcelle_culturale' => $this->extractAndTrim($line, 7, 4),  // Référence parcelle culturale
            'annee_recolte' => $this->extractAndTrim($line, 11, 4),  // Année prévue de récolte
            'num_ordre_precedent' => $this->extractAndTrim($line, 15, 2),  // N° d'ordre du précédent
            'cle_parcelle_precedent' => $this->extractAndTrim($line, 17, 4),  // Clé de la parcelle du précédent
            'espece_botanique' => $this->extractAndTrim($line, 21, 3),  // Espèce botanique
            'variete_semee_1' => $this->extractAndTrim($line, 24, 7),  // Variété semée 1
            'variete_semee_2' => $this->extractAndTrim($line, 31, 7),  // Variété semée 2
            'variete_semee_3' => $this->extractAndTrim($line, 38, 7),  // Variété semée 3
            'variete_semee_4' => $this->extractAndTrim($line, 45, 7),  // Variété semée 4
            'variete_semee_5' => $this->extractAndTrim($line, 52, 7),  // Variété semée 5
            'qualifiant_espece' => $this->extractAndTrim($line, 59, 3),  // Qualifiant d'espèce
            'periode_semis' => $this->extractAndTrim($line, 62, 3),  // Période de semis
            'destination' => $this->extractAndTrim($line, 65, 3),  // Destination
            'gestion_residus' => $this->extractAndTrim($line, 68, 3),  // Gestion des résidus
            'quantite_epandue' => $this->extractAndTrim($line, 71, 9),  // Quantité épandue (en tonne/ha)
        ];
    }

    /**
     * Extract and parse the HA flag (Amendement et résidus)
     */
    private function parseHaFlag($line) {
        return [
            'num_ordre_parcelle' => $this->extractAndTrim($line, 3, 4),  // N° d'ordre de la parcelle
            'ref_parcelle_culturale' => $this->extractAndTrim($line, 7, 4),  // Référence parcelle culturale
            'annee_recolte' => $this->extractAndTrim($line, 11, 4),  // Année prévue de récolte
            'type_amendement' => $this->extractAndTrim($line, 15, 3),  // Type d’amendement
            'complement_info_amendement' => $this->extractAndTrim($line, 18, 35),  // Complément information sur type amendement
            'date_amendement' => $this->extractAndTrim($line, 53, 8),  // Date de l’amendement (SSAAMMJJ)
            'quantite_epandue' => $this->extractAndTrim($line, 61, 9),  // Quantité épandue
            'unite_mesure_quantite' => $this->extractAndTrim($line, 70, 3),  // Unité de mesure de la quantité épandue
            'raison_sociale_1' => $this->extractAndTrim($line, 73, 35),  // Raison sociale (1)
            'raison_sociale_2' => $this->extractAndTrim($line, 108, 35),  // Raison sociale (2)
            'adresse_rue_1' => $this->extractAndTrim($line, 143, 35),  // Adresse Rue (1)
            'adresse_rue_2' => $this->extractAndTrim($line, 178, 35),  // Adresse Rue (2)
            'ville' => $this->extractAndTrim($line, 213, 35),  // Ville
            'code_postal' => $this->extractAndTrim($line, 248, 9),  // Code postal
            'pays' => $this->extractAndTrim($line, 257, 2),  // Pays (Code ISO)
        ];
    }

    /**
     * Extract and parse the PA flag (Analyse)
     */
    private function parsePaFlag($line) {
        return [
            'num_ordre_parcelle' => $this->extractAndTrim($line, 3, 4),  // N° d'ordre de la parcelle
            'ref_parcelle_culturale' => $this->extractAndTrim($line, 7, 4),  // Référence parcelle culturale
            'annee_recolte' => $this->extractAndTrim($line, 11, 4),  // Année prévue de récolte
            'num_bordereau_analyse' => $this->extractAndTrim($line, 15, 35),  // N° de bordereau d'analyse de sol
            'id_laboratoire' => $this->extractAndTrim($line, 50, 9),  // Identification du laboratoire d'analyse (SIREN)
            'raison_sociale_1' => $this->extractAndTrim($line, 59, 35),  // Raison sociale (1)
            'raison_sociale_2' => $this->extractAndTrim($line, 94, 35),  // Raison sociale (2)
            'adresse_rue_1' => $this->extractAndTrim($line, 129, 35),  // Adresse Rue (1)
            'adresse_rue_2' => $this->extractAndTrim($line, 164, 35),  // Adresse Rue (2)
            'ville' => $this->extractAndTrim($line, 199, 35),  // Ville
            'code_postal' => $this->extractAndTrim($line, 234, 9),  // Code postal
            'pays' => $this->extractAndTrim($line, 243, 2),  // Pays (Code ISO)
            'date_analyse' => $this->extractAndTrim($line, 245, 8),  // Date d'analyse (SSAAMMJJ)
            'date_prelevement' => $this->extractAndTrim($line, 253, 8),  // Date de prélèvement (SSAAMMJJ)
        ];
    }

    /**
     * Extract and parse the PV flag (Événement)
     */
    private function parsePvFlag($line) {
        return [
            'num_ordre_parcelle' => $this->extractAndTrim($line, 3, 4),  // N° d'ordre de la parcelle
            'ref_parcelle_culturale' => $this->extractAndTrim($line, 7, 4),  // Référence parcelle culturale
            'annee_recolte' => $this->extractAndTrim($line, 11, 4),  // Année prévue de récolte
            'ref_evenement' => $this->extractAndTrim($line, 15, 32),  // Référence de l’événement
            'action_evenement' => $this->extractAndTrim($line, 47, 1),  // Action sur l’événement
            'type_evenement' => $this->extractAndTrim($line, 48, 3),  // Type d’événement (en code)
            'prevu_ou_realise' => $this->extractAndTrim($line, 51, 1),  // Prévu ou Réalisé
            'intitule_evenement' => $this->extractAndTrim($line, 52, 35),  // Intitulé de l’événement
            'date_heure_debut' => $this->extractAndTrim($line, 87, 12),  // Date et Heure à laquelle le traitement commence (SSAAMMJJHHmm)
            'date_heure_fin' => $this->extractAndTrim($line, 101, 12),  // Date et Heure à laquelle le traitement prend fin (SSAAMMJJHHmm)
            'duree_traitement' => $this->extractAndTrim($line, 113, 6),  // Durée du traitement (jour/heure/minute)
            'date_preconisation' => $this->extractAndTrim($line, 119, 8),  // Date de préconisation (SSAAMMJJ)
            'stade_culture_code' => $this->extractAndTrim($line, 127, 3),  // Stade de la culture (en code)
            'precision_stade_culture' => $this->extractAndTrim($line, 130, 35),  // Précision sur le stade de culture
            'type_travail_code' => $this->extractAndTrim($line, 165, 3),  // Type de travail (en code)
            'complement_type_travail' => $this->extractAndTrim($line, 168, 35),  // Complément infos sur le type de travail
            'motivation' => $this->extractAndTrim($line, 203, 3),  // Motivation
            'complement_motivation' => $this->extractAndTrim($line, 206, 35),  // Complément sur motivation
            'type_operateur' => $this->extractAndTrim($line, 241, 3),  // Type d'opérateur (en code)
            'licence_operateur' => $this->extractAndTrim($line, 244, 20),  // N° licence opérateur
            'nom_operateur' => $this->extractAndTrim($line, 262, 35),  // Nom de l'opérateur
            'conditions_meteo' => $this->extractAndTrim($line, 299, 3),  // Conditions météo
            'traitements_speciaux' => $this->extractAndTrim($line, 302, 3),  // Traitements spéciaux
            'signe_temperature' => $this->extractAndTrim($line, 305, 1),  // Signe de la température
            'temperature_exterieure' => $this->extractAndTrim($line, 306, 3),  // Température extérieure
            'hygrometrie' => $this->extractAndTrim($line, 309, 3),  // Pourcentage d'hygrométrie
            'quantite_bouillie_visee' => $this->extractAndTrim($line, 312, 9),  // Quantité de bouillie visée par hectare
            'unite_bouillie_visee' => $this->extractAndTrim($line, 321, 3),  // Unité de mesure de la quantité de bouillie visée
            'quantite_bouillie_effective' => $this->extractAndTrim($line, 324, 9),  // Quantité de bouillie effective par hectare
            'unite_bouillie_effective' => $this->extractAndTrim($line, 333, 3),  // Unité de mesure de la quantité de bouillie effective
            'surface_couverte' => $this->extractAndTrim($line, 336, 9),  // Surface couverte par l’événement (en hectares)
            'commentaires_1' => $this->extractAndTrim($line, 345, 70),  // Commentaires (1)
            'commentaires_2' => $this->extractAndTrim($line, 415, 70),  // Commentaires (2)
        ];
    }

    /**
     * Extract and parse the VC flag (Coordonnées géographiques)
     */
    private function parseVcFlag($line) {
        return [
            'num_ordre_parcelle' => $this->extractAndTrim($line, 3, 4),  // N° d'ordre de la parcelle
            'ref_parcelle_culturale' => $this->extractAndTrim($line, 7, 4),  // Référence parcelle culturale
            'annee_recolte' => $this->extractAndTrim($line, 11, 4),  // Année prévue de récolte
            'ref_evenement' => $this->extractAndTrim($line, 15, 32),  // Référence de l’événement
            'qualifiant_position_geo' => $this->extractAndTrim($line, 47, 3),  // Qualifiant de la position géographique
            'longitude' => $this->extractAndTrim($line, 50, 11),  // Longitude (7 entiers et 3 décimales)
            'latitude' => $this->extractAndTrim($line, 61, 10),  // Latitude (7 entiers et 3 décimales)
            'altitude' => $this->extractAndTrim($line, 71, 18),  // Altitude (7 entiers et 3 décimales)
        ];
    }

    /**
     * Extract and parse the VB flag (Cible événement)
     */
    private function parseVbFlag($line) {
        return [
            'num_ordre_parcelle' => $this->extractAndTrim($line, 3, 4),  // N° d'ordre de la parcelle
            'ref_parcelle_culturale' => $this->extractAndTrim($line, 7, 4),  // Référence parcelle culturale
            'annee_recolte' => $this->extractAndTrim($line, 11, 4),  // Année prévue de récolte
            'ref_evenement' => $this->extractAndTrim($line, 15, 32),  // Référence de l’événement
            'cible_intervention_v94' => $this->extractAndTrim($line, 47, 3),  // Cible de l’intervention v0.94
            'cible_intervention_v95' => $this->extractAndTrim($line, 50, 12),  // Cible de l’intervention v0.95
        ];
    }

    /**
     * Extract and parse the VI flag (Intrant)
     */
    private function parseViFlag($line) {
        return [
            'num_ordre_parcelle' => $this->extractAndTrim($line, 3, 4),  // N° d'ordre de la parcelle
            'ref_parcelle_culturale' => $this->extractAndTrim($line, 7, 4),  // Référence parcelle culturale
            'annee_recolte' => $this->extractAndTrim($line, 11, 4),  // Année prévue de récolte
            'ref_evenement' => $this->extractAndTrim($line, 15, 32),  // Référence de l’événement
            'type_intrant' => $this->extractAndTrim($line, 47, 3),  // Type d’intrant
            'libelle_intrant' => $this->extractAndTrim($line, 50, 70),  // Libellé
            'code_ean' => $this->extractAndTrim($line, 120, 13),  // Code EAN du produit
            'code_amm' => $this->extractAndTrim($line, 133, 35),  // Code AMM du produit
            'code_gnis' => $this->extractAndTrim($line, 168, 7),  // Code GNIS
            'code_apport_organique' => $this->extractAndTrim($line, 175, 3),  // Code Apport Organique
            'code_eau' => $this->extractAndTrim($line, 178, 3),  // Code eau
            'code_adjuvant' => $this->extractAndTrim($line, 181, 3),  // Code adjuvant
            'code_calco_magnesien' => $this->extractAndTrim($line, 184, 3),  // Code calco-magnésien
            'qualifiant_effluent_1' => $this->extractAndTrim($line, 187, 3),  // Qualifiant de l'effluent (1)
            'qualifiant_effluent_2' => $this->extractAndTrim($line, 190, 3),  // Qualifiant de l'effluent (2)
            'qualifiant_effluent_3' => $this->extractAndTrim($line, 193, 3),  // Qualifiant de l'effluent (3)
            'qualifiant_effluent_4' => $this->extractAndTrim($line, 196, 3),  // Qualifiant de l'effluent (4)
            'qualifiant_effluent_5' => $this->extractAndTrim($line, 199, 3),  // Qualifiant de l'effluent (5)
            'qualifiant_effluent_6' => $this->extractAndTrim($line, 202, 3),  // Qualifiant de l'effluent (6)
            'qualifiant_semence_1' => $this->extractAndTrim($line, 205, 3),  // Qualifiant semence (1)
            'qualifiant_semence_2' => $this->extractAndTrim($line, 208, 3),  // Qualifiant semence (2)
            'qualifiant_semence_3' => $this->extractAndTrim($line, 211, 3),  // Qualifiant semence (3)
            'quantite_totale_effective' => $this->extractAndTrim($line, 214, 6),  // Quantité totale effective intrant
            'unite_mesure' => $this->extractAndTrim($line, 220, 3),  // Unité de mesure (en code)
            'quantite_effective_par_hectare' => $this->extractAndTrim($line, 223, 9),  // Quantité effective par hectare
            'unite_mesure_par_hectare' => $this->extractAndTrim($line, 232, 3),  // Unité de mesure de la quantité par hectare
            'dose_hectare_visee' => $this->extractAndTrim($line, 235, 9),  // Dose hectare visée
            'unite_mesure_dose_hectare' => $this->extractAndTrim($line, 244, 3),  // Unité de mesure (en code)
            'nombre_passages_preconises' => $this->extractAndTrim($line, 247, 6),  // Nombre de passages préconisés
            'raison_sociale_1' => $this->extractAndTrim($line, 253, 35),  // Raison sociale (1)
            'raison_sociale_2' => $this->extractAndTrim($line, 288, 35),  // Raison sociale (2)
            'adresse_rue_1' => $this->extractAndTrim($line, 323, 35),  // Adresse Rue (1)
            'adresse_rue_2' => $this->extractAndTrim($line, 358, 35),  // Adresse Rue (2)
            'ville' => $this->extractAndTrim($line, 393, 35),  // Ville
            'code_postal' => $this->extractAndTrim($line, 428, 9),  // Code postal
            'pays' => $this->extractAndTrim($line, 437, 2),  // Pays
            'densite_volumique_intrant' => $this->extractAndTrim($line, 439, 9),  // Densité volumique de l’intrant (en kg/L)
        ];
    }

    /**
     * Extract and parse the IC flag (Composition du produit)
     */
    private function parseIcFlag($line) {
        return [
            'num_ordre_parcelle' => $this->extractAndTrim($line, 3, 4),  // N° d'ordre de la parcelle
            'ref_parcelle_culturale' => $this->extractAndTrim($line, 7, 4),  // Référence parcelle culturale
            'annee_recolte' => $this->extractAndTrim($line, 11, 4),  // Année prévue de récolte
            'ref_evenement' => $this->extractAndTrim($line, 15, 32),  // Référence de l’événement
            'code_composant' => $this->extractAndTrim($line, 47, 3),  // Code du composant
            'teneur' => $this->extractAndTrim($line, 50, 9),  // Teneur en unité d'éléments fertilisants
        ];
    }

    /**
     * Extract and parse the IL flag (Lot fabricant)
     */
    private function parseIlFlag($line) {
        return [
            'num_ordre_parcelle' => $this->extractAndTrim($line, 3, 4),  // N° d'ordre de la parcelle
            'ref_parcelle_culturale' => $this->extractAndTrim($line, 7, 4),  // Référence parcelle culturale
            'annee_recolte' => $this->extractAndTrim($line, 11, 4),  // Année prévue de récolte
            'ref_evenement' => $this->extractAndTrim($line, 15, 32),  // Référence de l’événement
            'code_produit' => $this->extractAndTrim($line, 47, 35),  // Code produit
            'lot_fabricant_intrant' => $this->extractAndTrim($line, 82, 35),  // N° de lot du fabricant de l’intrant
            'quantite_par_lot' => $this->extractAndTrim($line, 117, 9),  // Quantité par lot
            'unite_mesure_quantite' => $this->extractAndTrim($line, 126, 3),  // Unité de mesure
            'pmg' => $this->extractAndTrim($line, 129, 9),  // PMG (Poids de Mille Grains)
            'unite_mesure_pmg' => $this->extractAndTrim($line, 138, 3),  // Unité de mesure pour PMG
        ];
    }

    /**
     * Extract and parse the IA flag (Analyse d'effluent)
     */
    private function parseIaFlag($line) {
        return [
            'num_ordre_parcelle' => $this->extractAndTrim($line, 3, 4),  // N° d'ordre de la parcelle
            'ref_parcelle_culturale' => $this->extractAndTrim($line, 7, 4),  // Référence parcelle culturale
            'annee_recolte' => $this->extractAndTrim($line, 11, 4),  // Année prévue de récolte
            'ref_evenement' => $this->extractAndTrim($line, 15, 32),  // Référence de l’événement
            'bordereau_analyse' => $this->extractAndTrim($line, 47, 35),  // N° de bordereau d'analyse
            'id_laboratoire' => $this->extractAndTrim($line, 82, 9),  // Identification du laboratoire d'analyse (Code SIREN)
            'raison_sociale_1' => $this->extractAndTrim($line, 91, 35),  // Raison sociale (1) du laboratoire
            'raison_sociale_2' => $this->extractAndTrim($line, 126, 35),  // Raison sociale (2) du laboratoire
            'adresse_rue_1' => $this->extractAndTrim($line, 161, 35),  // Adresse Rue (1) du laboratoire
            'adresse_rue_2' => $this->extractAndTrim($line, 196, 35),  // Adresse Rue (2) du laboratoire
            'ville' => $this->extractAndTrim($line, 231, 35),  // Ville du laboratoire
            'code_postal' => $this->extractAndTrim($line, 266, 9),  // Code postal du laboratoire
            'pays' => $this->extractAndTrim($line, 275, 2),  // Code ISO du pays du laboratoire
            'date_analyse' => $this->extractAndTrim($line, 277, 8),  // Date d'analyse (SSAAMMJ)
            'date_prelevement' => $this->extractAndTrim($line, 285, 8),  // Date de prélèvement (SSAAMMJ)
        ];
    }

    /**
     * Extract and parse the VR flag (Récolte)
     */
    private function parseVrFlag($line) {
        return [
            'num_ordre_parcelle' => $this->extractAndTrim($line, 3, 4),  // N° d'ordre de la parcelle
            'ref_parcelle_culturale' => $this->extractAndTrim($line, 7, 4),  // Référence parcelle culturale
            'annee_recolte' => $this->extractAndTrim($line, 11, 4),  // Année prévue de récolte
            'ref_evenement' => $this->extractAndTrim($line, 15, 32),  // Référence de l’événement
            'produit_principal_co_produit' => $this->extractAndTrim($line, 47, 3),  // Produit principal ou co-produit
            'code_produit_recolte' => $this->extractAndTrim($line, 50, 3),  // Code produit récolté
            'libelle_produit' => $this->extractAndTrim($line, 53, 35),  // Libellé du produit
            'destination_produit' => $this->extractAndTrim($line, 88, 3),  // Destination produit ou co-produit
            'quantite_recoltee' => $this->extractAndTrim($line, 91, 9),  // Quantité récoltée
            'unite_mesure_quantite' => $this->extractAndTrim($line, 100, 3),  // Unité de mesure de la quantité récoltée
            'rendement_calcule' => $this->extractAndTrim($line, 103, 9),  // Rendement calculé
            'unite_mesure_rendement_calcule' => $this->extractAndTrim($line, 112, 3),  // Unité de mesure pour le rendement calculé
            'rendement_estime' => $this->extractAndTrim($line, 115, 9),  // Rendement estimé
            'unite_mesure_rendement_estime' => $this->extractAndTrim($line, 124, 3),  // Unité de mesure pour le rendement estimé
        ];
    }

    /**
     * Extract and parse the RL flag (Lot Récolte)
     */
    private function parseRlFlag($line) {
        return [
            'num_ordre_parcelle' => $this->extractAndTrim($line, 3, 4),  // N° d'ordre de la parcelle
            'ref_parcelle_culturale' => $this->extractAndTrim($line, 7, 4),  // Référence parcelle culturale
            'annee_recolte' => $this->extractAndTrim($line, 11, 4),  // Année prévue de récolte
            'ref_evenement' => $this->extractAndTrim($line, 15, 32),  // Référence de l’événement
            'lot_os' => $this->extractAndTrim($line, 47, 35),  // N° de lot OS
            'lot_agriculteur' => $this->extractAndTrim($line, 82, 35),  // N° de lot Agriculteur
            'quantite_lot' => $this->extractAndTrim($line, 117, 9),  // Quantité du lot (en tonnes)
        ];
    }

    /**
     * Extract and parse the LC flag (Caractérisation du produit récolté pour le lot)
     */
    private function parseLcFlag($line) {
        return [
            'num_ordre_parcelle' => $this->extractAndTrim($line, 3, 4),  // N° d'ordre de la parcelle
            'ref_parcelle_culturale' => $this->extractAndTrim($line, 7, 4),  // Référence parcelle culturale
            'annee_recolte' => $this->extractAndTrim($line, 11, 4),  // Année prévue de récolte
            'ref_evenement' => $this->extractAndTrim($line, 15, 32),  // Référence de l’événement
            'type_caracteristique' => $this->extractAndTrim($line, 47, 3),  // Type de caractéristique (en code)
            'valeur_caracteristique' => $this->extractAndTrim($line, 50, 9),  // Valeur de la caractéristique
            'unite_mesure' => $this->extractAndTrim($line, 59, 3),  // Unité de mesure
        ];
    }

    /**
     * Extract and parse the VH flag (Historique Indicateur de décision)
     */
    private function parseVhFlag($line) {
        return [
            'num_ordre_parcelle' => $this->extractAndTrim($line, 3, 4),  // N° d'ordre de la parcelle
            'ref_parcelle_culturale' => $this->extractAndTrim($line, 7, 4),  // Référence parcelle culturale
            'annee_recolte' => $this->extractAndTrim($line, 11, 4),  // Année prévue de récolte
            'ref_evenement' => $this->extractAndTrim($line, 15, 32),  // Référence de l’événement
            'type_lien' => $this->extractAndTrim($line, 47, 3),  // Type de lien (en code)
            'ref_evenement_considere' => $this->extractAndTrim($line, 50, 32),  // Référence de l’événement considéré
            'num_parcelle_anterior' => $this->extractAndTrim($line, 82, 4),  // N° de la parcelle antérieur
            'annee_recolte_anterior' => $this->extractAndTrim($line, 86, 4),  // Année de récolte
            'identification_exploitation' => $this->extractAndTrim($line, 90, 17),  // Identification exploitation
            'type_identification' => $this->extractAndTrim($line, 107, 3),  // Type d’identification (SIRET, etc.)
            'raison_sociale_1' => $this->extractAndTrim($line, 110, 35),  // Raison sociale (1)
            'raison_sociale_2' => $this->extractAndTrim($line, 145, 35),  // Raison sociale (2)
            'adresse_rue_1' => $this->extractAndTrim($line, 180, 35),  // Adresse Rue (1)
            'adresse_rue_2' => $this->extractAndTrim($line, 215, 35),  // Adresse Rue (2)
            'ville' => $this->extractAndTrim($line, 250, 35),  // Ville
            'code_postal' => $this->extractAndTrim($line, 285, 9),  // Code postal
            'pays' => $this->extractAndTrim($line, 294, 2),  // Pays (Code ISO)
            'info_parcelle_non_edi_1' => $this->extractAndTrim($line, 296, 70),  // Informations parcelle non EDI (1)
            'info_parcelle_non_edi_2' => $this->extractAndTrim($line, 366, 70),  // Informations parcelle non EDI (2)
        ];
    }
}
