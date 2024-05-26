package com.example.projetalgo;

import javafx.beans.property.*;

/**
 * Classe représentant un livre avec ses propriétés.
 */
public class Book {
    private final IntegerProperty id;
    private final IntegerProperty idAuteur;
    private final StringProperty titre;
    private final StringProperty resume;
    private final StringProperty image;
    private final StringProperty genre;
    private final StringProperty annee;
    private final StringProperty prix;

    /**
     * Constructeur de la classe Book.
     *
     * @param id       l'identifiant du livre
     * @param idAuteur l'identifiant de l'auteur du livre
     * @param titre    le titre du livre
     * @param resume   le résumé du livre
     * @param image    le lien vers l'image du livre
     * @param genre    le genre du livre
     * @param annee    l'année de publication du livre
     * @param prix     le prix du livre
     */
    public Book(int id, int idAuteur, String titre, String resume, String image, String genre, String annee, String prix) {
        this.id = new SimpleIntegerProperty(id);
        this.idAuteur = new SimpleIntegerProperty(idAuteur);
        this.titre = new SimpleStringProperty(titre);
        this.resume = new SimpleStringProperty(resume);
        this.image = new SimpleStringProperty(image);
        this.genre = new SimpleStringProperty(genre);
        this.annee = new SimpleStringProperty(annee);
        this.prix = new SimpleStringProperty(prix);
    }

    /**
     * Retourne l'identifiant du livre.
     *
     * @return l'identifiant du livre
     */
    public int getId() {
        return id.get();
    }

    /**
     * Retourne la propriété de l'identifiant du livre.
     *
     * @return la propriété de l'identifiant du livre
     */
    public IntegerProperty idProperty() {
        return id;
    }

    /**
     * Retourne l'identifiant de l'auteur du livre.
     *
     * @return l'identifiant de l'auteur du livre
     */
    public int getIdAuteur() {
        return idAuteur.get();
    }

    /**
     * Retourne la propriété de l'identifiant de l'auteur du livre.
     *
     * @return la propriété de l'identifiant de l'auteur du livre
     */
    public IntegerProperty idAuteurProperty() {
        return idAuteur;
    }

    /**
     * Retourne le titre du livre.
     *
     * @return le titre du livre
     */
    public String getTitre() {
        return titre.get();
    }

    /**
     * Retourne la propriété du titre du livre.
     *
     * @return la propriété du titre du livre
     */
    public StringProperty titreProperty() {
        return titre;
    }

    /**
     * Retourne le résumé du livre.
     *
     * @return le résumé du livre
     */
    public String getResume() {
        return resume.get();
    }

    /**
     * Retourne la propriété du résumé du livre.
     *
     * @return la propriété du résumé du livre
     */
    public StringProperty resumeProperty() {
        return resume;
    }

    /**
     * Retourne le genre du livre.
     *
     * @return le genre du livre
     */
    public String getGenre() {
        return genre.get();
    }

    /**
     * Retourne la propriété du genre du livre.
     *
     * @return la propriété du genre du livre
     */
    public StringProperty genreProperty() {
        return genre;
    }

    /**
     * Retourne l'année de publication du livre.
     *
     * @return l'année de publication du livre
     */
    public String getAnnee() {
        return annee.get();
    }

    /**
     * Retourne la propriété de l'année de publication du livre.
     *
     * @return la propriété de l'année de publication du livre
     */
    public StringProperty anneeProperty() {
        return annee;
    }

    /**
     * Retourne le prix du livre.
     *
     * @return le prix du livre
     */
    public String getPrix() {
        return prix.get();
    }

    /**
     * Retourne la propriété du prix du livre.
     *
     * @return la propriété du prix du livre
     */
    public StringProperty prixProperty() {
        return prix;
    }

    /**
     * Retourne le lien vers l'image du livre.
     *
     * @return le lien vers l'image du livre
     */
    public String getImage() {
        return image.get();
    }

    /**
     * Retourne la propriété du lien vers l'image du livre.
     *
     * @return la propriété du lien vers l'image du livre
     */
    public StringProperty imageProperty() {
        return image;
    }
}
