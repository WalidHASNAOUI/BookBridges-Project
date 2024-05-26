package com.example.projetalgo;

import javafx.collections.FXCollections;
import javafx.collections.ObservableList;
import javafx.event.ActionEvent;
import javafx.fxml.FXML;
import javafx.scene.control.*;

/**
 * Contrôleur pour gérer l'interface utilisateur de la gestion des livres.
 */
public class BookController {

    @FXML
    private TableView<Book> tableView;
    @FXML
    private TableColumn<Book, Integer> idColumn;
    @FXML
    private TableColumn<Book, Integer> idAuteurColumn;
    @FXML
    private TableColumn<Book, String> titreColumn;
    @FXML
    private TableColumn<Book, String> resumeColumn;
    @FXML
    private TableColumn<Book, String> imageColumn;
    @FXML
    private TableColumn<Book, String> genreColumn;
    @FXML
    private TableColumn<Book, String> anneeColumn;
    @FXML
    private TableColumn<Book, String> prixColumn;
    @FXML
    private ComboBox<String> auteurComboBox;
    @FXML
    private TextField titreField;
    @FXML
    private TextField resumeField;
    @FXML
    private TextField imageField;
    @FXML
    private TextField anneeField;
    @FXML
    private TextField prixField;
    @FXML
    private ComboBox<String> genreComboBox;
    @FXML
    private TextField searchField;
    @FXML
    private Button addButton;
    @FXML
    private Button updateButton;
    @FXML
    private Button deleteButton;

    private ObservableList<Book> bookList = FXCollections.observableArrayList();
    private ObservableList<String> genreList = FXCollections.observableArrayList();
    private ObservableList<String> auteurList = FXCollections.observableArrayList();

    /**
     * Méthode d'initialisation appelée après le chargement du FXML.
     * Initialise les colonnes de la table et charge les données.
     */
    @FXML
    public void initialize() {
        getGenres();
        getAuteurs();
        getBooks();

        idColumn.setCellValueFactory(cellData -> cellData.getValue().idProperty().asObject());
        idAuteurColumn.setCellValueFactory(cellData -> cellData.getValue().idAuteurProperty().asObject());
        titreColumn.setCellValueFactory(cellData -> cellData.getValue().titreProperty());
        resumeColumn.setCellValueFactory(cellData -> cellData.getValue().resumeProperty());
        imageColumn.setCellValueFactory(cellData -> cellData.getValue().imageProperty());
        genreColumn.setCellValueFactory(cellData -> cellData.getValue().genreProperty());
        anneeColumn.setCellValueFactory(cellData -> cellData.getValue().anneeProperty());
        prixColumn.setCellValueFactory(cellData -> cellData.getValue().prixProperty());

        tableView.setItems(bookList);

        tableView.getSelectionModel().selectedItemProperty().addListener(
                (observable, oldValue, newValue) -> showBookDetails(newValue));
    }

    /**
     * Affiche les détails d'un livre sélectionné dans les champs appropriés.
     *
     * @param book le livre sélectionné
     */
    private void showBookDetails(Book book) {
        if (book != null) {
            titreField.setText(book.getTitre());
            resumeField.setText(book.getResume());
            imageField.setText(book.getImage());
            anneeField.setText(book.getAnnee());
            prixField.setText(book.getPrix());
            auteurComboBox.setValue(getAuteurById(book.getIdAuteur()));
            genreComboBox.setValue(book.getGenre());
        } else {
            clearFields();
        }
    }

    /**
     * Récupère le nom de l'auteur à partir de son ID.
     *
     * @param idAuteur l'ID de l'auteur
     * @return le nom de l'auteur
     */
    private String getAuteurById(int idAuteur) {
        for (String auteur : auteurComboBox.getItems()) {
            if (DatabaseConnexion.getAuteurId(auteur) == idAuteur) {
                return auteur;
            }
        }
        return null;
    }

    /**
     * Récupère et charge la liste des livres depuis la base de données.
     */
    private void getBooks() {
        bookList.clear();
        bookList.addAll(DatabaseConnexion.getBooks());
        tableView.setItems(bookList);
    }

    /**
     * Récupère et charge la liste des genres depuis la base de données.
     */
    private void getGenres() {
        genreList.clear();
        genreList.addAll(DatabaseConnexion.getGenres());
        genreComboBox.setItems(genreList);
    }

    /**
     * Récupère et charge la liste des auteurs depuis la base de données.
     */
    private void getAuteurs() {
        auteurList.clear();
        auteurList.addAll(DatabaseConnexion.getAuteurs());
        auteurComboBox.setItems(auteurList);
    }

    /**
     * Ajoute un nouveau livre à la base de données.
     *
     * @param event l'événement déclenché par le bouton Ajouter
     */
    @FXML
    private void addBook(ActionEvent event) {
        String auteur = auteurComboBox.getValue();
        String titre = titreField.getText();
        String resume = resumeField.getText();
        String image = imageField.getText();
        String annee = anneeField.getText();
        String prix = prixField.getText();
        String genre = genreComboBox.getValue();

        if (auteur != null && !titre.isEmpty() && !resume.isEmpty() && !image.isEmpty() && !annee.isEmpty() && !prix.isEmpty() && genre != null) {
            int idAuteur = DatabaseConnexion.getAuteurId(auteur);

            if (DatabaseConnexion.bookExists(idAuteur, titre)) {
                Alert alert = new Alert(Alert.AlertType.WARNING);
                alert.setTitle("Livre existant");
                alert.setHeaderText(null);
                alert.setContentText("Ce livre avec le même auteur existe déjà !");
                alert.showAndWait();
                return;
            }

            DatabaseConnexion.insertBook(idAuteur, titre, resume, image, annee, prix, genre);
            getBooks();
            clearFields();
        }
    }

    /**
     * Met à jour les informations d'un livre sélectionné dans la base de données.
     *
     * @param event l'événement déclenché par le bouton Mettre à jour
     */
    @FXML
    private void updateBook(ActionEvent event) {
        Book selectedBook = tableView.getSelectionModel().getSelectedItem();
        if (selectedBook != null) {
            String auteur = auteurComboBox.getValue();
            String titre = titreField.getText();
            String resume = resumeField.getText();
            String image = imageField.getText();
            String annee = anneeField.getText();
            String prix = prixField.getText();
            String genre = genreComboBox.getValue();
            if (auteur != null && !titre.isEmpty() && !resume.isEmpty() && !image.isEmpty() && !annee.isEmpty() && !prix.isEmpty() && genre != null) {
                int idAuteur = DatabaseConnexion.getAuteurId(auteur);
                DatabaseConnexion.updateBook(selectedBook.getId(), idAuteur, titre, resume, image, annee, prix, genre);
                getBooks();
                clearFields();
                getBooks();
            }
        }
    }

    /**
     * Supprime un livre sélectionné de la base de données.
     *
     * @param event l'événement déclenché par le bouton Supprimer
     */
    @FXML
    private void deleteBook(ActionEvent event) {
        Book selectedBook = tableView.getSelectionModel().getSelectedItem();
        if (selectedBook != null) {
            DatabaseConnexion.deleteBook(selectedBook.getId());
            getBooks();
        }
    }

    /**
     * Recherche des livres dans la base de données en fonction du texte de recherche.
     *
     * @param event l'événement déclenché par le bouton Rechercher
     */
    @FXML
    private void search(ActionEvent event) {
        String searchText = searchField.getText().toLowerCase();
        if (searchText.isEmpty()) {
            tableView.setItems(bookList);
        } else {
            ObservableList<Book> filteredList = FXCollections.observableArrayList();
            for (Book book : bookList) {
                if (book.getTitre().toLowerCase().contains(searchText) ||
                        book.getResume().toLowerCase().contains(searchText) ||
                        book.getGenre().toLowerCase().contains(searchText)) {
                    filteredList.add(book);
                }
            }
            tableView.setItems(filteredList);
        }
    }

    /**
     * Efface les champs de saisie.
     */
    private void clearFields() {
        auteurComboBox.getSelectionModel().clearSelection();
        titreField.clear();
        resumeField.clear();
        imageField.clear();
        anneeField.clear();
        prixField.clear();
        genreComboBox.getSelectionModel().clearSelection();
    }
}
