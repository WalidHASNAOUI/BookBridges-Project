package com.example.projetalgo;

import java.nio.file.Files;
import java.nio.file.Paths;
import java.sql.*;
import java.util.ArrayList;
import java.util.List;

/**
 * Classe utilitaire pour gérer la connexion à la base de données et les opérations CRUD pour les livres.
 */
public class DatabaseConnexion {

    private static String dbhost;
    private static String dbname;
    private static String dbuser;
    private static String dbpass;

    // Chargement des informations de connexion à partir d'un fichier lors de l'initialisation de la classe
    static {
        try {
            String[] credentials = Files.readAllLines(Paths.get("src\\main\\resources\\loginInfo\\login.txt"))
                    .toArray(new String[0]);
            for (String line : credentials) {
                String[] parts = line.split(":");
                if (parts.length == 2) {
                    String key = parts[0].trim();
                    String value = parts[1].trim();
                    switch (key) {
                        case "username":
                            dbuser = value;
                            break;
                        case "password":
                            dbpass = value;
                            break;
                        case "dbhost":
                            dbhost = value;
                            break;
                        case "dbname":
                            dbname = value;
                            break;
                    }
                }
            }
        } catch (Exception e) {
            System.out.println("Erreur lors de la lecture des informations de connexion: " + e.getMessage());
        }
    }

    private static final String URL = "jdbc:mysql://" + dbhost + "/" + dbname;

    /**
     * Établit une connexion à la base de données MySQL.
     *
     * @return la connexion à la base de données ou null en cas d'échec
     */
    public static Connection connect() {
        Connection conn = null;
        try {
            conn = DriverManager.getConnection(URL, dbuser, dbpass);
        } catch (SQLException e) {
            System.out.println("Erreur de connexion à la base de données: " + e.getMessage());
        }
        return conn;
    }

    /**
     * Vérifie si un livre existe déjà dans la base de données.
     *
     * @param idAuteur l'ID de l'auteur
     * @param titre le titre du livre
     * @return true si le livre existe, false sinon
     */
    public static boolean bookExists(int idAuteur, String titre) {
        String sql = "SELECT COUNT(*) FROM bookbridges_livre WHERE id_auteur = ? AND titre = ?";
        try (Connection conn = connect();
             PreparedStatement pstmt = conn.prepareStatement(sql)) {
            pstmt.setInt(1, idAuteur);
            pstmt.setString(2, titre);
            ResultSet rs = pstmt.executeQuery();
            if (rs.next()) {
                return rs.getInt(1) > 0;
            }
        } catch (SQLException e) {
            System.out.println(e.getMessage());
        }
        return false;
    }

    /**
     * Insère un nouveau livre dans la base de données.
     *
     * @param idAuteur l'ID de l'auteur du livre
     * @param titre le titre du livre
     * @param resume le résumé du livre
     * @param image le lien vers l'image du livre
     * @param annee l'année de publication du livre
     * @param prix le prix du livre
     * @param genre le genre du livre
     */
    public static void insertBook(int idAuteur, String titre, String resume, String image, String annee, String prix, String genre) {
        String livre = "INSERT INTO bookbridges_livre(id_auteur, titre, resume, image, annee, prix) VALUES(?, ?, ?, ?, ?, ?)";
        String sqlGenre = "INSERT INTO bookbridges_livre_categorie(id_livre, id_categorie) VALUES(?, ?)";

        try (Connection conn = connect();
             PreparedStatement pstmtLivre = conn.prepareStatement(livre, Statement.RETURN_GENERATED_KEYS);
             PreparedStatement pstmtGenre = conn.prepareStatement(sqlGenre)) {
            pstmtLivre.setInt(1, idAuteur);
            pstmtLivre.setString(2, titre);
            pstmtLivre.setString(3, resume);
            pstmtLivre.setString(4, image);
            pstmtLivre.setString(5, annee);
            pstmtLivre.setString(6, prix);
            pstmtLivre.executeUpdate();

            ResultSet generatedKeys = pstmtLivre.getGeneratedKeys();
            if (generatedKeys.next()) {
                int idLivre = generatedKeys.getInt(1);
                int idGenre = getGenreId(genre, conn);
                pstmtGenre.setInt(1, idLivre);
                pstmtGenre.setInt(2, idGenre);
                pstmtGenre.executeUpdate();
            }
        } catch (SQLException e) {
            System.out.println(e.getMessage());
        }
    }

    /**
     * Récupère tous les livres de la base de données.
     *
     * @return une liste de livres
     */
    public static List<Book> getBooks() {
        String sql = "SELECT l.id, l.id_auteur, l.titre, l.resume, l.image, l.annee, l.prix, c.nom AS genre " +
                "FROM bookbridges_livre l " +
                "JOIN bookbridges_livre_categorie lc ON l.id = lc.id_livre " +
                "JOIN bookbridges_categorie c ON lc.id_categorie = c.id";
        List<Book> books = new ArrayList<>();

        try (Connection conn = connect();
             PreparedStatement pstmt = conn.prepareStatement(sql)) {
            ResultSet rs = pstmt.executeQuery();

            while (rs.next()) {
                books.add(new Book(
                        rs.getInt("id"),
                        rs.getInt("id_auteur"),
                        rs.getString("titre"),
                        rs.getString("resume"),
                        rs.getString("image"),
                        rs.getString("genre"),
                        rs.getString("annee"),
                        rs.getString("prix")
                ));
            }
        } catch (SQLException e) {
            System.out.println(e.getMessage());
        }
        return books;
    }

    /**
     * Met à jour les informations d'un livre dans la base de données.
     *
     * @param id l'ID du livre à mettre à jour
     * @param idAuteur l'ID de l'auteur du livre
     * @param titre le titre du livre
     * @param resume le résumé du livre
     * @param image le lien vers l'image du livre
     * @param annee l'année de publication du livre
     * @param prix le prix du livre
     * @param genre le genre du livre
     */
    public static void updateBook(int id, int idAuteur, String titre, String resume, String image, String annee, String prix, String genre) {
        String sqlLivre = "UPDATE bookbridges_livre SET id_auteur = ?, titre = ?, resume = ?, image = ?, annee = ?, prix = ? WHERE id = ?";
        String sqlGenre = "UPDATE bookbridges_livre_categorie SET id_categorie = ? WHERE id_livre = ?";

        try (Connection conn = connect();
             PreparedStatement pstmtLivre = conn.prepareStatement(sqlLivre);
             PreparedStatement pstmtGenre = conn.prepareStatement(sqlGenre)) {
            pstmtLivre.setInt(1, idAuteur);
            pstmtLivre.setString(2, titre);
            pstmtLivre.setString(3, resume);
            pstmtLivre.setString(4, image);
            pstmtLivre.setString(5, annee);
            pstmtLivre.setString(6, prix);
            pstmtLivre.setInt(7, id);
            pstmtLivre.executeUpdate();

            int idGenre = getGenreId(genre, conn);
            pstmtGenre.setInt(1, idGenre);
            pstmtGenre.setInt(2, id);
            pstmtGenre.executeUpdate();
        } catch (SQLException e) {
            System.out.println(e.getMessage());
        }
    }

    /**
     * Supprime un livre de la base de données.
     *
     * @param id l'ID du livre à supprimer
     */
    public static void deleteBook(int id) {
        String sqlLivre = "DELETE FROM bookbridges_livre WHERE id = ?";
        String sqlGenre = "DELETE FROM bookbridges_livre_categorie WHERE id_livre = ?";

        try (Connection conn = connect();
             PreparedStatement pstmtLivre = conn.prepareStatement(sqlLivre);
             PreparedStatement pstmtGenre = conn.prepareStatement(sqlGenre)) {
            pstmtGenre.setInt(1, id);
            pstmtGenre.executeUpdate();

            pstmtLivre.setInt(1, id);
            pstmtLivre.executeUpdate();
        } catch (SQLException e) {
            System.out.println(e.getMessage());
        }
    }

    /**
     * Récupère tous les genres de la base de données.
     *
     * @return une liste de genres
     */
    public static List<String> getGenres() {
        String sql = "SELECT nom FROM bookbridges_categorie";
        List<String> genres = new ArrayList<>();

        try (Connection conn = connect();
             PreparedStatement pstmt = conn.prepareStatement(sql)) {
            ResultSet rs = pstmt.executeQuery();

            while (rs.next()) {
                genres.add(rs.getString("nom"));
            }
        } catch (SQLException e) {
            System.out.println(e.getMessage());
        }
        return genres;
    }

    /**
     * Récupère l'ID d'un genre à partir de son nom.
     *
     * @param genre le nom du genre
     * @param conn la connexion à la base de données
     * @return l'ID du genre
     * @throws SQLException si le genre n'est pas trouvé
     */
    private static int getGenreId(String genre, Connection conn) throws SQLException {
        String sql = "SELECT id FROM bookbridges_categorie WHERE nom = ?";
        try (PreparedStatement pstmt = conn.prepareStatement(sql)) {
            pstmt.setString(1, genre);
            ResultSet rs = pstmt.executeQuery();
            if (rs.next()) {
                return rs.getInt("id");
            } else {
                throw new SQLException("Genre not found: " + genre);
            }
        }
    }

    /**
     * Récupère tous les auteurs de la base de données.
     *
     * @return une liste d'auteurs
     */
    public static List<String> getAuteurs() {
        String sql = "SELECT nom FROM bookbridges_auteur";
        List<String> auteurs = new ArrayList<>();

        try (Connection conn = connect();
             PreparedStatement pstmt = conn.prepareStatement(sql)) {
            ResultSet rs = pstmt.executeQuery();

            while (rs.next()) {
                auteurs.add(rs.getString("nom"));
            }
        } catch (SQLException e) {
            System.out.println(e.getMessage());
        }
        return auteurs;
    }

    /**
     * Récupère l'ID d'un auteur à partir de son nom.
     *
     * @param nom le nom de l'auteur
     * @return l'ID de l'auteur ou -1 si l'auteur n'est pas trouvé
     */
    public static int getAuteurId(String nom) {
        String sql = "SELECT id FROM bookbridges_auteur WHERE nom = ?";
        try (Connection conn = connect();
             PreparedStatement pstmt = conn.prepareStatement(sql)) {
            pstmt.setString(1, nom);
            ResultSet rs = pstmt.executeQuery();
            if (rs.next()) {
                return rs.getInt("id");
            } else {
                throw new SQLException("Auteur not found: " + nom);
            }
        } catch (SQLException e) {
            System.out.println(e.getMessage());
            return -1;
        }
    }
}
