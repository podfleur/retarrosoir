<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240314145906 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE abonnement (id INT AUTO_INCREMENT NOT NULL, suiveur_id_id INT NOT NULL, suivi_personne_id_id INT DEFAULT NULL, suivi_etablissement_id_id INT DEFAULT NULL, suivi_hashtag_id_id INT DEFAULT NULL, INDEX IDX_351268BBA960B43B (suiveur_id_id), INDEX IDX_351268BB71875A3A (suivi_personne_id_id), INDEX IDX_351268BB93BE1598 (suivi_etablissement_id_id), INDEX IDX_351268BBE23F54B (suivi_hashtag_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commentaire (id INT AUTO_INCREMENT NOT NULL, post_id_id INT NOT NULL, compte_id_id INT NOT NULL, texte VARCHAR(2200) NOT NULL, date DATETIME NOT NULL, INDEX IDX_67F068BCE85F12B8 (post_id_id), INDEX IDX_67F068BC86A5793C (compte_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE compte (id INT AUTO_INCREMENT NOT NULL, photo_id_id INT DEFAULT NULL, etablissement_id_id INT DEFAULT NULL, pseudo VARCHAR(20) NOT NULL, nom_affichage VARCHAR(20) NOT NULL, email VARCHAR(60) NOT NULL, mdp VARCHAR(40) NOT NULL, biographie VARCHAR(150) DEFAULT NULL, dernier_golden_like DATE DEFAULT NULL, INDEX IDX_CFF65260C51599E0 (photo_id_id), INDEX IDX_CFF65260FC5092A6 (etablissement_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE etablissement (id INT AUTO_INCREMENT NOT NULL, photo_id_id INT DEFAULT NULL, nom VARCHAR(25) NOT NULL, code_postal INT DEFAULT NULL, pays VARCHAR(25) DEFAULT NULL, INDEX IDX_20FD592CC51599E0 (photo_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE format (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(3) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE hashtag (id INT AUTO_INCREMENT NOT NULL, texte VARCHAR(25) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `like` (id INT AUTO_INCREMENT NOT NULL, post_id_id INT NOT NULL, compte_id_id INT NOT NULL, golden TINYINT(1) NOT NULL, INDEX IDX_AC6340B3E85F12B8 (post_id_id), INDEX IDX_AC6340B386A5793C (compte_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE photo (id INT AUTO_INCREMENT NOT NULL, format_id_id INT NOT NULL, donnees_photo VARBINARY(255) NOT NULL, INDEX IDX_14B7841860DED95A (format_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post (id INT AUTO_INCREMENT NOT NULL, compte_id_id INT NOT NULL, titre VARCHAR(75) DEFAULT NULL, description VARCHAR(2200) DEFAULT NULL, date DATETIME NOT NULL, temps_retard DATETIME NOT NULL, INDEX IDX_5A8A6C8D86A5793C (compte_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post_hashtag (id INT AUTO_INCREMENT NOT NULL, post_id_id INT NOT NULL, hashtag_id_id INT NOT NULL, INDEX IDX_675D9D52E85F12B8 (post_id_id), INDEX IDX_675D9D52F6228C5F (hashtag_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post_photo (id INT AUTO_INCREMENT NOT NULL, post_id_id INT NOT NULL, photo_id_id INT NOT NULL, INDEX IDX_83AC08F7E85F12B8 (post_id_id), INDEX IDX_83AC08F7C51599E0 (photo_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE signalement (id INT AUTO_INCREMENT NOT NULL, signaleur_id_id INT NOT NULL, signale_id_id INT DEFAULT NULL, post_id_id INT DEFAULT NULL, motif VARCHAR(150) NOT NULL, INDEX IDX_F4B55114C7164438 (signaleur_id_id), INDEX IDX_F4B551145A1C2710 (signale_id_id), INDEX IDX_F4B55114E85F12B8 (post_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE abonnement ADD CONSTRAINT FK_351268BBA960B43B FOREIGN KEY (suiveur_id_id) REFERENCES compte (id)');
        $this->addSql('ALTER TABLE abonnement ADD CONSTRAINT FK_351268BB71875A3A FOREIGN KEY (suivi_personne_id_id) REFERENCES compte (id)');
        $this->addSql('ALTER TABLE abonnement ADD CONSTRAINT FK_351268BB93BE1598 FOREIGN KEY (suivi_etablissement_id_id) REFERENCES etablissement (id)');
        $this->addSql('ALTER TABLE abonnement ADD CONSTRAINT FK_351268BBE23F54B FOREIGN KEY (suivi_hashtag_id_id) REFERENCES hashtag (id)');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BCE85F12B8 FOREIGN KEY (post_id_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC86A5793C FOREIGN KEY (compte_id_id) REFERENCES compte (id)');
        $this->addSql('ALTER TABLE compte ADD CONSTRAINT FK_CFF65260C51599E0 FOREIGN KEY (photo_id_id) REFERENCES photo (id)');
        $this->addSql('ALTER TABLE compte ADD CONSTRAINT FK_CFF65260FC5092A6 FOREIGN KEY (etablissement_id_id) REFERENCES etablissement (id)');
        $this->addSql('ALTER TABLE etablissement ADD CONSTRAINT FK_20FD592CC51599E0 FOREIGN KEY (photo_id_id) REFERENCES photo (id)');
        $this->addSql('ALTER TABLE `like` ADD CONSTRAINT FK_AC6340B3E85F12B8 FOREIGN KEY (post_id_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE `like` ADD CONSTRAINT FK_AC6340B386A5793C FOREIGN KEY (compte_id_id) REFERENCES compte (id)');
        $this->addSql('ALTER TABLE photo ADD CONSTRAINT FK_14B7841860DED95A FOREIGN KEY (format_id_id) REFERENCES format (id)');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D86A5793C FOREIGN KEY (compte_id_id) REFERENCES compte (id)');
        $this->addSql('ALTER TABLE post_hashtag ADD CONSTRAINT FK_675D9D52E85F12B8 FOREIGN KEY (post_id_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE post_hashtag ADD CONSTRAINT FK_675D9D52F6228C5F FOREIGN KEY (hashtag_id_id) REFERENCES hashtag (id)');
        $this->addSql('ALTER TABLE post_photo ADD CONSTRAINT FK_83AC08F7E85F12B8 FOREIGN KEY (post_id_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE post_photo ADD CONSTRAINT FK_83AC08F7C51599E0 FOREIGN KEY (photo_id_id) REFERENCES photo (id)');
        $this->addSql('ALTER TABLE signalement ADD CONSTRAINT FK_F4B55114C7164438 FOREIGN KEY (signaleur_id_id) REFERENCES compte (id)');
        $this->addSql('ALTER TABLE signalement ADD CONSTRAINT FK_F4B551145A1C2710 FOREIGN KEY (signale_id_id) REFERENCES compte (id)');
        $this->addSql('ALTER TABLE signalement ADD CONSTRAINT FK_F4B55114E85F12B8 FOREIGN KEY (post_id_id) REFERENCES post (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE abonnement DROP FOREIGN KEY FK_351268BBA960B43B');
        $this->addSql('ALTER TABLE abonnement DROP FOREIGN KEY FK_351268BB71875A3A');
        $this->addSql('ALTER TABLE abonnement DROP FOREIGN KEY FK_351268BB93BE1598');
        $this->addSql('ALTER TABLE abonnement DROP FOREIGN KEY FK_351268BBE23F54B');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BCE85F12B8');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC86A5793C');
        $this->addSql('ALTER TABLE compte DROP FOREIGN KEY FK_CFF65260C51599E0');
        $this->addSql('ALTER TABLE compte DROP FOREIGN KEY FK_CFF65260FC5092A6');
        $this->addSql('ALTER TABLE etablissement DROP FOREIGN KEY FK_20FD592CC51599E0');
        $this->addSql('ALTER TABLE `like` DROP FOREIGN KEY FK_AC6340B3E85F12B8');
        $this->addSql('ALTER TABLE `like` DROP FOREIGN KEY FK_AC6340B386A5793C');
        $this->addSql('ALTER TABLE photo DROP FOREIGN KEY FK_14B7841860DED95A');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D86A5793C');
        $this->addSql('ALTER TABLE post_hashtag DROP FOREIGN KEY FK_675D9D52E85F12B8');
        $this->addSql('ALTER TABLE post_hashtag DROP FOREIGN KEY FK_675D9D52F6228C5F');
        $this->addSql('ALTER TABLE post_photo DROP FOREIGN KEY FK_83AC08F7E85F12B8');
        $this->addSql('ALTER TABLE post_photo DROP FOREIGN KEY FK_83AC08F7C51599E0');
        $this->addSql('ALTER TABLE signalement DROP FOREIGN KEY FK_F4B55114C7164438');
        $this->addSql('ALTER TABLE signalement DROP FOREIGN KEY FK_F4B551145A1C2710');
        $this->addSql('ALTER TABLE signalement DROP FOREIGN KEY FK_F4B55114E85F12B8');
        $this->addSql('DROP TABLE abonnement');
        $this->addSql('DROP TABLE commentaire');
        $this->addSql('DROP TABLE compte');
        $this->addSql('DROP TABLE etablissement');
        $this->addSql('DROP TABLE format');
        $this->addSql('DROP TABLE hashtag');
        $this->addSql('DROP TABLE `like`');
        $this->addSql('DROP TABLE photo');
        $this->addSql('DROP TABLE post');
        $this->addSql('DROP TABLE post_hashtag');
        $this->addSql('DROP TABLE post_photo');
        $this->addSql('DROP TABLE signalement');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
