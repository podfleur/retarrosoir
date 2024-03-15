<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240315090545 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE compte ADD username VARCHAR(180) NOT NULL, ADD roles JSON NOT NULL COMMENT \'(DC2Type:json)\', ADD password VARCHAR(255) NOT NULL, ADD dernier_goldden_like DATETIME DEFAULT NULL, DROP pseudo, DROP mdp, DROP dernier_golden_like, CHANGE nom_affichage nom_affichage VARCHAR(30) DEFAULT NULL, CHANGE email email VARCHAR(255) NOT NULL, CHANGE biographie biographie VARCHAR(255) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CFF65260F85E0677 ON compte (username)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_CFF65260F85E0677 ON compte');
        $this->addSql('ALTER TABLE compte ADD pseudo VARCHAR(20) NOT NULL, ADD mdp VARCHAR(40) NOT NULL, ADD dernier_golden_like DATE DEFAULT NULL, DROP username, DROP roles, DROP password, DROP dernier_goldden_like, CHANGE biographie biographie VARCHAR(150) DEFAULT NULL, CHANGE email email VARCHAR(60) NOT NULL, CHANGE nom_affichage nom_affichage VARCHAR(20) NOT NULL');
    }
}
