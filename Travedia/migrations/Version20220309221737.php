<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220309221737 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE Paiement (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, client_id INT NOT NULL, planning_id INT DEFAULT NULL, prix DOUBLE PRECISION NOT NULL, statut VARCHAR(255) NOT NULL, date_creation DATE NOT NULL, date_paiement DATE DEFAULT NULL, type_paiement VARCHAR(255) NOT NULL, session_id LONGTEXT DEFAULT NULL, INDEX IDX_48AA18487E3C61F9 (owner_id), INDEX IDX_48AA184819EB6921 (client_id), INDEX IDX_48AA18483D865311 (planning_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE actualite (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE categorie (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, image VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commentaire (id INT AUTO_INCREMENT NOT NULL, poste_id INT DEFAULT NULL, contenu VARCHAR(255) NOT NULL, date DATE NOT NULL, INDEX IDX_67F068BCA0905086 (poste_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE destination (id INT AUTO_INCREMENT NOT NULL, utilisateur_id INT DEFAULT NULL, evenement_id INT DEFAULT NULL, region_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, image VARCHAR(255) DEFAULT NULL, evaluation INT DEFAULT NULL, longitude VARCHAR(255) DEFAULT NULL, latitude VARCHAR(255) DEFAULT NULL, INDEX IDX_3EC63EAAFB88E14F (utilisateur_id), INDEX IDX_3EC63EAAFD02F13 (evenement_id), INDEX IDX_3EC63EAA98260155 (region_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE destination_planning (destination_id INT NOT NULL, planning_id INT NOT NULL, INDEX IDX_8A297FC7816C6140 (destination_id), INDEX IDX_8A297FC73D865311 (planning_id), PRIMARY KEY(destination_id, planning_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE evenement (id INT AUTO_INCREMENT NOT NULL, utilisateur_id INT DEFAULT NULL, categorie_id INT DEFAULT NULL, actualite_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, datedeb DATE NOT NULL, datefin DATE NOT NULL, image VARCHAR(255) NOT NULL, INDEX IDX_B26681EFB88E14F (utilisateur_id), INDEX IDX_B26681EBCF5E72D (categorie_id), INDEX IDX_B26681EA2843073 (actualite_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE evenement_planning (evenement_id INT NOT NULL, planning_id INT NOT NULL, INDEX IDX_5BFF241EFD02F13 (evenement_id), INDEX IDX_5BFF241E3D865311 (planning_id), PRIMARY KEY(evenement_id, planning_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE hotel (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, adresse VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, num_tel INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, sender_id INT DEFAULT NULL, reciever_id INT DEFAULT NULL, contenu VARCHAR(255) NOT NULL, date DATE NOT NULL, INDEX IDX_B6BD307FF624B39D (sender_id), INDEX IDX_B6BD307F5D5C928D (reciever_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE newsletter (id INT AUTO_INCREMENT NOT NULL, utilisateur_id INT DEFAULT NULL, contenu VARCHAR(255) NOT NULL, image VARCHAR(255) DEFAULT NULL, date DATE NOT NULL, INDEX IDX_7E8585C8FB88E14F (utilisateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE planning (id INT AUTO_INCREMENT NOT NULL, utilisateur_id INT DEFAULT NULL, actualite_id INT DEFAULT NULL, date_depart DATE NOT NULL, date_fin DATE NOT NULL, prix INT DEFAULT NULL, type_plan VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, INDEX IDX_D499BFF6FB88E14F (utilisateur_id), INDEX IDX_D499BFF6A2843073 (actualite_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE planning_evenement (planning_id INT NOT NULL, evenement_id INT NOT NULL, INDEX IDX_A376B21D3D865311 (planning_id), INDEX IDX_A376B21DFD02F13 (evenement_id), PRIMARY KEY(planning_id, evenement_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE planning_destination (planning_id INT NOT NULL, destination_id INT NOT NULL, INDEX IDX_73EDFEF33D865311 (planning_id), INDEX IDX_73EDFEF3816C6140 (destination_id), PRIMARY KEY(planning_id, destination_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE planning_hotel (planning_id INT NOT NULL, hotel_id INT NOT NULL, INDEX IDX_92FFE4463D865311 (planning_id), INDEX IDX_92FFE4463243BB18 (hotel_id), PRIMARY KEY(planning_id, hotel_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE poste (id INT AUTO_INCREMENT NOT NULL, actualite_id INT DEFAULT NULL, profile_id INT NOT NULL, image VARCHAR(255) DEFAULT NULL, contenu VARCHAR(255) DEFAULT NULL, likes INT NOT NULL, date DATE NOT NULL, INDEX IDX_7C890FABA2843073 (actualite_id), INDEX IDX_7C890FABCCFA12B8 (profile_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE profile (id INT AUTO_INCREMENT NOT NULL, utilisateur_id INT DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, evaluation INT DEFAULT NULL, UNIQUE INDEX UNIQ_8157AA0FFB88E14F (utilisateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reclamation (id INT AUTO_INCREMENT NOT NULL, utilisateur_id INT DEFAULT NULL, contenu VARCHAR(255) NOT NULL, etat_reclamation VARCHAR(255) NOT NULL, sujet VARCHAR(255) NOT NULL, reclamation_Rep_id INT DEFAULT NULL, INDEX IDX_CE606404FB88E14F (utilisateur_id), UNIQUE INDEX UNIQ_CE606404C2D02C78 (reclamation_Rep_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reclamation_reponse (id INT AUTO_INCREMENT NOT NULL, reclamation_id INT NOT NULL, contenu VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8E9C00562D6BA2D9 (reclamation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE region (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, image VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE utilisateur (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, cin INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, adresse VARCHAR(255) DEFAULT NULL, num_tel INT DEFAULT NULL, langue VARCHAR(255) DEFAULT NULL, is_verified TINYINT(1) NOT NULL, is_blocked TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_1D1C63B3E7927C74 (email), UNIQUE INDEX UNIQ_1D1C63B3ABE530DA (cin), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE Paiement ADD CONSTRAINT FK_48AA18487E3C61F9 FOREIGN KEY (owner_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE Paiement ADD CONSTRAINT FK_48AA184819EB6921 FOREIGN KEY (client_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE Paiement ADD CONSTRAINT FK_48AA18483D865311 FOREIGN KEY (planning_id) REFERENCES planning (id)');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BCA0905086 FOREIGN KEY (poste_id) REFERENCES poste (id)');
        $this->addSql('ALTER TABLE destination ADD CONSTRAINT FK_3EC63EAAFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE destination ADD CONSTRAINT FK_3EC63EAAFD02F13 FOREIGN KEY (evenement_id) REFERENCES evenement (id)');
        $this->addSql('ALTER TABLE destination ADD CONSTRAINT FK_3EC63EAA98260155 FOREIGN KEY (region_id) REFERENCES region (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE destination_planning ADD CONSTRAINT FK_8A297FC7816C6140 FOREIGN KEY (destination_id) REFERENCES destination (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE destination_planning ADD CONSTRAINT FK_8A297FC73D865311 FOREIGN KEY (planning_id) REFERENCES planning (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE evenement ADD CONSTRAINT FK_B26681EFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE evenement ADD CONSTRAINT FK_B26681EBCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE evenement ADD CONSTRAINT FK_B26681EA2843073 FOREIGN KEY (actualite_id) REFERENCES actualite (id)');
        $this->addSql('ALTER TABLE evenement_planning ADD CONSTRAINT FK_5BFF241EFD02F13 FOREIGN KEY (evenement_id) REFERENCES evenement (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE evenement_planning ADD CONSTRAINT FK_5BFF241E3D865311 FOREIGN KEY (planning_id) REFERENCES planning (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FF624B39D FOREIGN KEY (sender_id) REFERENCES profile (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F5D5C928D FOREIGN KEY (reciever_id) REFERENCES profile (id)');
        $this->addSql('ALTER TABLE newsletter ADD CONSTRAINT FK_7E8585C8FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE planning ADD CONSTRAINT FK_D499BFF6FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE planning ADD CONSTRAINT FK_D499BFF6A2843073 FOREIGN KEY (actualite_id) REFERENCES actualite (id)');
        $this->addSql('ALTER TABLE planning_evenement ADD CONSTRAINT FK_A376B21D3D865311 FOREIGN KEY (planning_id) REFERENCES planning (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE planning_evenement ADD CONSTRAINT FK_A376B21DFD02F13 FOREIGN KEY (evenement_id) REFERENCES evenement (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE planning_destination ADD CONSTRAINT FK_73EDFEF33D865311 FOREIGN KEY (planning_id) REFERENCES planning (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE planning_destination ADD CONSTRAINT FK_73EDFEF3816C6140 FOREIGN KEY (destination_id) REFERENCES destination (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE planning_hotel ADD CONSTRAINT FK_92FFE4463D865311 FOREIGN KEY (planning_id) REFERENCES planning (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE planning_hotel ADD CONSTRAINT FK_92FFE4463243BB18 FOREIGN KEY (hotel_id) REFERENCES hotel (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE poste ADD CONSTRAINT FK_7C890FABA2843073 FOREIGN KEY (actualite_id) REFERENCES actualite (id)');
        $this->addSql('ALTER TABLE poste ADD CONSTRAINT FK_7C890FABCCFA12B8 FOREIGN KEY (profile_id) REFERENCES profile (id)');
        $this->addSql('ALTER TABLE profile ADD CONSTRAINT FK_8157AA0FFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE reclamation ADD CONSTRAINT FK_CE606404FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE reclamation ADD CONSTRAINT FK_CE606404C2D02C78 FOREIGN KEY (reclamation_Rep_id) REFERENCES reclamation_reponse (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reclamation_reponse ADD CONSTRAINT FK_8E9C00562D6BA2D9 FOREIGN KEY (reclamation_id) REFERENCES reclamation (id)');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES utilisateur (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE evenement DROP FOREIGN KEY FK_B26681EA2843073');
        $this->addSql('ALTER TABLE planning DROP FOREIGN KEY FK_D499BFF6A2843073');
        $this->addSql('ALTER TABLE poste DROP FOREIGN KEY FK_7C890FABA2843073');
        $this->addSql('ALTER TABLE evenement DROP FOREIGN KEY FK_B26681EBCF5E72D');
        $this->addSql('ALTER TABLE destination_planning DROP FOREIGN KEY FK_8A297FC7816C6140');
        $this->addSql('ALTER TABLE planning_destination DROP FOREIGN KEY FK_73EDFEF3816C6140');
        $this->addSql('ALTER TABLE destination DROP FOREIGN KEY FK_3EC63EAAFD02F13');
        $this->addSql('ALTER TABLE evenement_planning DROP FOREIGN KEY FK_5BFF241EFD02F13');
        $this->addSql('ALTER TABLE planning_evenement DROP FOREIGN KEY FK_A376B21DFD02F13');
        $this->addSql('ALTER TABLE planning_hotel DROP FOREIGN KEY FK_92FFE4463243BB18');
        $this->addSql('ALTER TABLE Paiement DROP FOREIGN KEY FK_48AA18483D865311');
        $this->addSql('ALTER TABLE destination_planning DROP FOREIGN KEY FK_8A297FC73D865311');
        $this->addSql('ALTER TABLE evenement_planning DROP FOREIGN KEY FK_5BFF241E3D865311');
        $this->addSql('ALTER TABLE planning_evenement DROP FOREIGN KEY FK_A376B21D3D865311');
        $this->addSql('ALTER TABLE planning_destination DROP FOREIGN KEY FK_73EDFEF33D865311');
        $this->addSql('ALTER TABLE planning_hotel DROP FOREIGN KEY FK_92FFE4463D865311');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BCA0905086');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FF624B39D');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F5D5C928D');
        $this->addSql('ALTER TABLE poste DROP FOREIGN KEY FK_7C890FABCCFA12B8');
        $this->addSql('ALTER TABLE reclamation_reponse DROP FOREIGN KEY FK_8E9C00562D6BA2D9');
        $this->addSql('ALTER TABLE reclamation DROP FOREIGN KEY FK_CE606404C2D02C78');
        $this->addSql('ALTER TABLE destination DROP FOREIGN KEY FK_3EC63EAA98260155');
        $this->addSql('ALTER TABLE Paiement DROP FOREIGN KEY FK_48AA18487E3C61F9');
        $this->addSql('ALTER TABLE Paiement DROP FOREIGN KEY FK_48AA184819EB6921');
        $this->addSql('ALTER TABLE destination DROP FOREIGN KEY FK_3EC63EAAFB88E14F');
        $this->addSql('ALTER TABLE evenement DROP FOREIGN KEY FK_B26681EFB88E14F');
        $this->addSql('ALTER TABLE newsletter DROP FOREIGN KEY FK_7E8585C8FB88E14F');
        $this->addSql('ALTER TABLE planning DROP FOREIGN KEY FK_D499BFF6FB88E14F');
        $this->addSql('ALTER TABLE profile DROP FOREIGN KEY FK_8157AA0FFB88E14F');
        $this->addSql('ALTER TABLE reclamation DROP FOREIGN KEY FK_CE606404FB88E14F');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('DROP TABLE Paiement');
        $this->addSql('DROP TABLE actualite');
        $this->addSql('DROP TABLE categorie');
        $this->addSql('DROP TABLE commentaire');
        $this->addSql('DROP TABLE destination');
        $this->addSql('DROP TABLE destination_planning');
        $this->addSql('DROP TABLE evenement');
        $this->addSql('DROP TABLE evenement_planning');
        $this->addSql('DROP TABLE hotel');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE newsletter');
        $this->addSql('DROP TABLE planning');
        $this->addSql('DROP TABLE planning_evenement');
        $this->addSql('DROP TABLE planning_destination');
        $this->addSql('DROP TABLE planning_hotel');
        $this->addSql('DROP TABLE poste');
        $this->addSql('DROP TABLE profile');
        $this->addSql('DROP TABLE reclamation');
        $this->addSql('DROP TABLE reclamation_reponse');
        $this->addSql('DROP TABLE region');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE utilisateur');
    }
}
