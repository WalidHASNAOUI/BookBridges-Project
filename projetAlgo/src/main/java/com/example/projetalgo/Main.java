package com.example.projetalgo;

import javafx.application.Application;
import javafx.fxml.FXMLLoader;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.stage.Stage;

/**
 * Classe principale de l'application JavaFX.
 * Elle charge et affiche la fenêtre de connexion à l'application.
 */
public class Main extends Application {

    /**
     * Point d'entrée principal de l'application JavaFX.
     * Charge le fichier FXML pour l'interface de connexion et l'affiche.
     *
     * @param primaryStage la scène principale de l'application
     * @throws Exception si une erreur survient lors du chargement du fichier FXML
     */
    @Override
    public void start(Stage primaryStage) throws Exception {
        // Chargement du fichier FXML pour l'interface de connexion
        Parent root = FXMLLoader.load(getClass().getResource("/com/example/projetalgo/admin.fxml"));

        // Configuration de la scène principale
        primaryStage.setTitle("Login");
        primaryStage.setScene(new Scene(root));
        primaryStage.show();
    }

    /**
     * Méthode principale pour lancer l'application JavaFX.
     *
     * @param args les arguments de la ligne de commande
     */
    public static void main(String[] args) {
        launch(args);
    }
}
