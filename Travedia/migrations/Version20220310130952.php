<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220310130952 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE evenement_planning (evenement_id INT NOT NULL, planning_id INT NOT NULL, INDEX IDX_5BFF241EFD02F13 (evenement_id), INDEX IDX_5BFF241E3D865311 (planning_id), PRIMARY KEY(evenement_id, planning_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE facture (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, client_id INT NOT NULL, planning_id INT DEFAULT NULL, prix DOUBLE PRECISION NOT NULL, statut VARCHAR(255) NOT NULL, date_creation DATE NOT NULL, date_paiement DATE DEFAULT NULL, type_paiement VARCHAR(255) NOT NULL, session_id LONGTEXT DEFAULT NULL, INDEX IDX_FE8664107E3C61F9 (owner_id), INDEX IDX_FE86641019EB6921 (client_id), INDEX IDX_FE8664103D865311 (planning_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE evenement_planning ADD CONSTRAINT FK_5BFF241EFD02F13 FOREIGN KEY (evenement_id) REFERENCES evenement (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE evenement_planning ADD CONSTRAINT FK_5BFF241E3D865311 FOREIGN KEY (planning_id) REFERENCES planning (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE facture ADD CONSTRAINT FK_FE8664107E3C61F9 FOREIGN KEY (owner_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE facture ADD CONSTRAINT FK_FE86641019EB6921 FOREIGN KEY (client_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE facture ADD CONSTRAINT FK_FE8664103D865311 FOREIGN KEY (planning_id) REFERENCES planning (id)');
        $this->addSql('DROP TABLE message');
        $this->addSql('ALTER TABLE paiement DROP FOREIGN KEY FK_48AA18483D865311');
        $this->addSql('ALTER TABLE paiement ADD CONSTRAINT FK_48AA18487E3C61F9 FOREIGN KEY (owner_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE paiement ADD CONSTRAINT FK_48AA184819EB6921 FOREIGN KEY (client_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE paiement ADD CONSTRAINT FK_48AA18483D865311 FOREIGN KEY (planning_id) REFERENCES planning (id)');
        $this->addSql('ALTER TABLE poste CHANGE profile_id profile_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE profile DROP FOREIGN KEY FK_8157AA0FFB88E14F');
        $this->addSql('ALTER TABLE profile ADD CONSTRAINT FK_8157AA0FFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE reclamation DROP FOREIGN KEY FK_CE606404FB88E14F');
        $this->addSql('ALTER TABLE reclamation ADD CONSTRAINT FK_CE606404FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES utilisateur (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, sender_id INT DEFAULT NULL, reciever_id INT DEFAULT NULL, contenu VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, date DATE NOT NULL, INDEX IDX_B6BD307FF624B39D (sender_id), INDEX IDX_B6BD307F5D5C928D (reciever_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F5D5C928D FOREIGN KEY (reciever_id) REFERENCES profile (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FF624B39D FOREIGN KEY (sender_id) REFERENCES profile (id)');
        $this->addSql('DROP TABLE evenement_planning');
        $this->addSql('DROP TABLE facture');
        $this->addSql('ALTER TABLE Paiement DROP FOREIGN KEY FK_48AA18487E3C61F9');
        $this->addSql('ALTER TABLE Paiement DROP FOREIGN KEY FK_48AA184819EB6921');
        $this->addSql('ALTER TABLE Paiement DROP FOREIGN KEY FK_48AA18483D865311');
        $this->addSql('ALTER TABLE Paiement ADD CONSTRAINT FK_48AA18483D865311 FOREIGN KEY (planning_id) REFERENCES planning (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE poste CHANGE profile_id profile_id INT NOT NULL');
        $this->addSql('ALTER TABLE profile DROP FOREIGN KEY FK_8157AA0FFB88E14F');
        $this->addSql('ALTER TABLE profile ADD CONSTRAINT FK_8157AA0FFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reclamation DROP FOREIGN KEY FK_CE606404FB88E14F');
        $this->addSql('ALTER TABLE reclamation ADD CONSTRAINT FK_CE606404FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES utilisateur (id) ON DELETE CASCADE');
    }
}
