<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210118184056 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_8157AA0FA76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__profile AS SELECT id, user_id, last_name, first_name, phone, sex, patronymic FROM profile');
        $this->addSql('DROP TABLE profile');
        $this->addSql('CREATE TABLE profile (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER DEFAULT NULL, last_name VARCHAR(180) NOT NULL COLLATE BINARY, first_name VARCHAR(180) NOT NULL COLLATE BINARY, phone VARCHAR(15) NOT NULL COLLATE BINARY, sex INTEGER NOT NULL, patronymic VARCHAR(180) NOT NULL, CONSTRAINT FK_8157AA0FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO profile (id, user_id, last_name, first_name, phone, sex, patronymic) SELECT id, user_id, last_name, first_name, phone, sex, patronymic FROM __temp__profile');
        $this->addSql('DROP TABLE __temp__profile');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8157AA0FA76ED395 ON profile (user_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_8157AA0FA76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__profile AS SELECT id, user_id, last_name, first_name, patronymic, phone, sex FROM profile');
        $this->addSql('DROP TABLE profile');
        $this->addSql('CREATE TABLE profile (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER DEFAULT NULL, last_name VARCHAR(180) NOT NULL, first_name VARCHAR(180) NOT NULL, phone VARCHAR(15) NOT NULL, sex INTEGER NOT NULL, patronymic VARCHAR(180) DEFAULT NULL COLLATE BINARY)');
        $this->addSql('INSERT INTO profile (id, user_id, last_name, first_name, patronymic, phone, sex) SELECT id, user_id, last_name, first_name, patronymic, phone, sex FROM __temp__profile');
        $this->addSql('DROP TABLE __temp__profile');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8157AA0FA76ED395 ON profile (user_id)');
    }
}
