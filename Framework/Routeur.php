<?php

require_once 'Controleur.php';
require_once 'Requete.php';
require_once 'Vue.php';

/*
 * Classe de routage des requêtes entrantes.
 * 
 * Inspirée du framework PHP de Nathan Davison
 * (https://github.com/ndavison/Nathan-MVC)
 * 
 * @author Baptiste Pesquet
 */
class Routeur {

    /**
     * Méthode principale appelée par le contrôleur frontal
     * Examine la requête et exécute l'action appropriée
     */
    public function routerRequete() {
        try {
            // Grâce à la redirection, toutes les URL entrantes sont du type :
            // index.php?controleur=XXX&action=YYY&id=ZZZ
            // $_GET contient (même en cas de requête POST) les paramètres de l'URL
            $requete = new Requete($_REQUEST);
            
            $controleur = $this->creerControleur($requete);
            $action = $this->creerAction($requete);
            
            $controleur->executerAction($action);
        }
        catch (Exception $e) {
            $this->gererErreur($e);
        }
    }

    /**
     * Crée le contrôleur approprié en fonction de la requête reçue
     * 
     * @return 
     * @throws Exception
     */
    private function creerControleur(Requete $requete) {
        $controleur = "Accueil";  // Contrôleur par défaut
        if ($requete->existeParametre('controleur')) {
            $controleur = $requete->getParametre('controleur');
        }
        // Création du nom du fichier du contrôleur
        $classeControleur = "Controleur" . $controleur;
        $fichierControleur = "Controleur/" . $classeControleur . ".php";
        if (file_exists($fichierControleur)) {
            // Instanciation du contrôleur adapté à la requête
            require($fichierControleur);
            $controleur = new $classeControleur();
            $controleur->setRequete($requete);
            return $controleur;
        }
        else {
            throw new Exception("Erreur interne : fichier '$fichierControleur' introuvable");
        }
    }

    private function creerAction(Requete $requete) {
        $action = "index";  // Action par défaut
        if ($requete->existeParametre('action')) {
            $action = $requete->getParametre('action');
        }
        return $action;
    }

    /**
     * Gère une erreur d'exécution (exception)
     * 
     * @param type $exception L'exception qui s'est produite
     */
    private function gererErreur(Exception $exception) {
        $vue = new Vue('erreur');
        $vue->generer(array('msgErreur' => $exception->getMessage()));
    }

}
