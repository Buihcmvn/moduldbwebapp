<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260217102307 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE area (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, beschreibung VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE file (id INT AUTO_INCREMENT NOT NULL, hardware_id INT DEFAULT NULL, encrypted_path VARCHAR(255) NOT NULL, INDEX IDX_8C9F3610C9CC762B (hardware_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE hardware (id INT AUTO_INCREMENT NOT NULL, image_id INT DEFAULT NULL, name VARCHAR(50) NOT NULL, bezeichnen VARCHAR(50) NOT NULL, beschreibung VARCHAR(1000) NOT NULL, kommentar VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_FE99E9E03DA5256D (image_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE images (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, erweiterung VARCHAR(255) NOT NULL, dateipfad VARCHAR(255) NOT NULL, kategorie VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE meilenstein (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, projekt_id INT NOT NULL, software_id INT NOT NULL, hardware_id INT NOT NULL, entwickler VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, kommentar VARCHAR(255) NOT NULL, start DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', end DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE projekte (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, beschreibung VARCHAR(500) DEFAULT NULL, kommentar VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE projekte_area (projekte_id INT NOT NULL, area_id INT NOT NULL, INDEX IDX_EFE07B9387FF857B (projekte_id), INDEX IDX_EFE07B93BD0F409C (area_id), PRIMARY KEY(projekte_id, area_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE projekte_hardware (projekte_id INT NOT NULL, hardware_id INT NOT NULL, INDEX IDX_B8B88D5887FF857B (projekte_id), INDEX IDX_B8B88D58C9CC762B (hardware_id), PRIMARY KEY(projekte_id, hardware_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE projekte_software (projekte_id INT NOT NULL, software_id INT NOT NULL, INDEX IDX_31F10C7787FF857B (projekte_id), INDEX IDX_31F10C77D7452741 (software_id), PRIMARY KEY(projekte_id, software_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE recht (id INT AUTO_INCREMENT NOT NULL, recht_name VARCHAR(255) NOT NULL, recht_beschreibung VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE rolle (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, beschreibung VARCHAR(255) DEFAULT NULL, kommentar VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE rolle_recht (id INT AUTO_INCREMENT NOT NULL, rolle_id INT NOT NULL, recht_id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE software (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, bezeichnen VARCHAR(50) NOT NULL, beschreibung VARCHAR(500) NOT NULL, kommentar VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user_projekt (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, projekt_id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user_rolle (user_id INT NOT NULL, rolle_id INT NOT NULL, INDEX IDX_71EADB87A76ED395 (user_id), INDEX IDX_71EADB8740A53BF6 (rolle_id), PRIMARY KEY(user_id, rolle_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE file ADD CONSTRAINT FK_8C9F3610C9CC762B FOREIGN KEY (hardware_id) REFERENCES hardware (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE hardware ADD CONSTRAINT FK_FE99E9E03DA5256D FOREIGN KEY (image_id) REFERENCES images (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE projekte_area ADD CONSTRAINT FK_EFE07B9387FF857B FOREIGN KEY (projekte_id) REFERENCES projekte (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE projekte_area ADD CONSTRAINT FK_EFE07B93BD0F409C FOREIGN KEY (area_id) REFERENCES area (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE projekte_hardware ADD CONSTRAINT FK_B8B88D5887FF857B FOREIGN KEY (projekte_id) REFERENCES projekte (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE projekte_hardware ADD CONSTRAINT FK_B8B88D58C9CC762B FOREIGN KEY (hardware_id) REFERENCES hardware (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE projekte_software ADD CONSTRAINT FK_31F10C7787FF857B FOREIGN KEY (projekte_id) REFERENCES projekte (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE projekte_software ADD CONSTRAINT FK_31F10C77D7452741 FOREIGN KEY (software_id) REFERENCES software (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_rolle ADD CONSTRAINT FK_71EADB87A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_rolle ADD CONSTRAINT FK_71EADB8740A53BF6 FOREIGN KEY (rolle_id) REFERENCES rolle (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user CHANGE name name VARCHAR(255) NOT NULL, CHANGE email email VARCHAR(255) NOT NULL, CHANGE abteilung abteilung VARCHAR(255) NOT NULL, CHANGE zusaetzlich zusaetzlich VARCHAR(255) NOT NULL, CHANGE username username VARCHAR(255) NOT NULL, CHANGE password password VARCHAR(255) NOT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE file DROP FOREIGN KEY FK_8C9F3610C9CC762B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE hardware DROP FOREIGN KEY FK_FE99E9E03DA5256D
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE projekte_area DROP FOREIGN KEY FK_EFE07B9387FF857B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE projekte_area DROP FOREIGN KEY FK_EFE07B93BD0F409C
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE projekte_hardware DROP FOREIGN KEY FK_B8B88D5887FF857B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE projekte_hardware DROP FOREIGN KEY FK_B8B88D58C9CC762B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE projekte_software DROP FOREIGN KEY FK_31F10C7787FF857B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE projekte_software DROP FOREIGN KEY FK_31F10C77D7452741
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_rolle DROP FOREIGN KEY FK_71EADB87A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_rolle DROP FOREIGN KEY FK_71EADB8740A53BF6
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE area
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE file
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE hardware
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE images
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE meilenstein
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE projekte
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE projekte_area
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE projekte_hardware
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE projekte_software
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE recht
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE rolle
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE rolle_recht
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE software
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user_projekt
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user_rolle
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user CHANGE name name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE username username VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE password password VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE email email VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE abteilung abteilung VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE zusaetzlich zusaetzlich VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`
        SQL);
    }
}
