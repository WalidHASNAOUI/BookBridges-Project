package com.example.projetalgo;

import javafx.event.ActionEvent;
import javafx.fxml.FXML;
import javafx.geometry.Rectangle2D;
import javafx.scene.control.Alert;
import javafx.scene.control.TextField;
import javafx.stage.Screen;
import javafx.stage.Stage;
import javafx.fxml.FXMLLoader;
import javafx.scene.Parent;
import javafx.scene.Scene;

import java.io.IOException;
import java.nio.file.Files;
import java.nio.file.Paths;

/**
 * Contrôleur pour la gestion de l'authentification administrateur.
 */
public class AdminController {

    @FXML
    private TextField usernameField;
    @FXML
    private TextField passwordField;

    /**
     * Gère l'événement de connexion.
     * Vérifie les informations d'identification de l'utilisateur et, si elles sont valides, charge la scène de gestion des livres.
     *
     * @param event l'événement de clic sur le bouton de connexion
     */
    @FXML
    private void handleLogin(ActionEvent event) {
        String username = usernameField.getText();
        String password = passwordField.getText();

        if (authenticate(username, password)) {
            try {
                // Chargement de la nouvelle scène
                FXMLLoader loader = new FXMLLoader(getClass().getResource("/com/example/projetalgo/book_manager.fxml"));
                Parent root = loader.load();

                // Récupération de la fenêtre actuelle et mise à jour de la scène
                Stage stage = (Stage) usernameField.getScene().getWindow();
                stage.setScene(new Scene(root));
                stage.setTitle("BookMeet Database Manager");

                // Centrage de la fenêtre sur l'écran
                Rectangle2D screenBounds = Screen.getPrimary().getVisualBounds();
                stage.setX((screenBounds.getWidth() - stage.getWidth()) / 2);
                stage.setY((screenBounds.getHeight() - stage.getHeight()) / 2);

                stage.show();
            } catch (IOException e) {
                e.printStackTrace();
            }
        } else {
            // Affichage d'une alerte en cas d'échec de la connexion
            Alert alert = new Alert(Alert.AlertType.ERROR);
            alert.setTitle("Login Failed");
            alert.setHeaderText(null);
            alert.setContentText("Invalid username or password.");
            alert.showAndWait();
        }
    }

    /**
     * Authentifie l'utilisateur en vérifiant les informations d'identification.
     *
     * @param username le nom d'utilisateur
     * @param password le mot de passe
     * @return true si les informations d'identification sont valides, false sinon
     */
    private boolean authenticate(String username, String password) {
        String user = null;
        String pass = null;
        try {
            // Lecture des informations de connexion à partir d'un fichier
            String[] credentials = Files.readAllLines(Paths.get("src\\main\\resources\\loginInfo\\admin.txt"))
                    .toArray(new String[0]);
            for (String line : credentials) {
                String[] parts = line.split(":");
                if (parts.length == 2) {
                    String key = parts[0].trim();
                    String value = parts[1].trim();
                    if (key.equals("username")) {
                        user = value;
                    } else if (key.equals("password")) {
                        pass = value;
                    }
                }
            }
            return user != null && pass != null && user.equals(username) && pass.equals(password);
        } catch (Exception e) {
            System.out.println("Erreur lors de la lecture des informations de connexion: " + e.getMessage());
            return false;
        }
    }
}
